<?php

namespace PluginCore\libraries;

if(!defined("UTF_8")) define("UTF_8", 1);
if(!defined("ASCII")) define("ASCII", 2);
if(!defined("ISO_8859_1")) define("ISO_8859_1", 3);


class CsvUpload {
    
    protected $delimiter    = ',';
    protected $enclosure  = '"';
    protected $escape = '\\';
    protected $error_msg    = [];
    
    
    
    public $file_temp	= "";
    public $file_name	= "";
    public $orig_name	= "";
    public $file_type	= "";
    public $file_size	= "";
    public $file_ext    = "";
	
    public $allowed_types = array('csv');

    protected $messagesHandler;
    
    /*
	public $upload_path				= "";
	public $overwrite				= FALSE;
	public $encrypt_name			= FALSE;
	public $is_image				= FALSE;
	public $image_width				= '';
	public $image_height			= '';
	public $image_type				= '';
	public $image_size_str			= '';
	public $error_msg				= array();
	public $mimes					= array();
	public $remove_spaces			= TRUE;
	public $xss_clean				= FALSE;
	public $temp_prefix				= "temp_file_";
	public $client_name				= '';
    
    */
    
    
    function __construct($config = array()) {
        foreach(array('delimiter','enclosure') as $field){
            if(isset($config[$field])) $this->{$field} = $config[$field];
        }
        
        
        $this->messagesHandler = \PluginCore\Messages::getInstance();
    }
    
    
    function isUploaded ($field){
        
       
        if(file_exists($field)){
            $this->file_temp = $field;
            $this->file_size = filesize ($field);
           
            $pathinfo = pathinfo($field);
            
            $this->file_type = $pathinfo['extension'];
            $this->file_name = $pathinfo['filename'];
            $this->file_ext	 = $pathinfo['extension'];
            $this->client_name = $this->file_name;
            
            return true;
            
        
        } else if ( isset($_FILES[$field])) {
			
            // Was the file able to be uploaded? If not, determine the reason why.
            if ( ! is_uploaded_file($_FILES[$field]['tmp_name']))
            {
                $error = ( ! isset($_FILES[$field]['error'])) ? 4 : $_FILES[$field]['error'];

                switch($error)
                {
                    case 1:	// UPLOAD_ERR_INI_SIZE
                        $this->set_error('upload_file_exceeds_limit');
                        break;
                    case 2: // UPLOAD_ERR_FORM_SIZE
                        $this->set_error('upload_file_exceeds_form_limit');
                        break;
                    case 3: // UPLOAD_ERR_PARTIAL
                        $this->set_error('upload_file_partial');
                        break;
                    case 4: // UPLOAD_ERR_NO_FILE
                        $this->set_error('upload_no_file_selected');
                        break;
                    case 6: // UPLOAD_ERR_NO_TMP_DIR
                        $this->set_error('upload_no_temp_directory');
                        break;
                    case 7: // UPLOAD_ERR_CANT_WRITE
                        $this->set_error('upload_unable_to_write_file');
                        break;
                    case 8: // UPLOAD_ERR_EXTENSION
                        $this->set_error('upload_stopped_by_extension');
                        break;
                    default :   $this->set_error('upload_no_file_selected');
                        break;
                }

                return FALSE;
            }
            
            
        } else {
          
            $this->set_error('upload_no_file_selected');
			return FALSE;
        }
        


		// Set the uploaded data as class variables
		$this->file_temp = $_FILES[$field]['tmp_name'];
		$this->file_size = $_FILES[$field]['size'];
		$this->_file_mime_type($_FILES[$field]);  //sets $this->file_type;
		$this->file_type = preg_replace("/^(.+?);.*$/", "\\1", $this->file_type); 
		$this->file_type = strtolower(trim(stripslashes($this->file_type), '"'));
		$this->file_name = $_FILES[$field]['name'];
		$this->file_ext	 = $this->get_extension($this->file_name);
		$this->client_name = $this->file_name;
                
                
                
        $ext = strtolower(ltrim($this->file_ext, '.'));

		if ( ! in_array($ext, $this->allowed_types))
		{
			$this->set_error('upload_invalid_filetype');
			return FALSE;
		}
                
                return true;

    }
    
    public function getErrores(){
        return $this->error_msg;
    }
    
    
    public function readCSV ($columnId = false){
        $csv = array();
        ini_set("auto_detect_line_endings", true);
        $lines = file($this->file_temp , FILE_IGNORE_NEW_LINES);

        foreach ($lines as $key => $value){
                if(empty($value)) continue;
                $value = $this->utf8_encode_seguro($value);
                $rowArray = str_getcsv($value, $this->delimiter, $this->enclosure,$this->escape);
                $indice =  ($columnId === false)? $key : $rowArray[$columnId];
                $csv[$indice] = $rowArray ;
        }
        unset($lines);
        return $csv;
    }
    
    protected  function utf8_encode_seguro($texto){
        return ($this->codificacion ($texto) == ISO_8859_1) ? utf8_encode($texto) : $texto;
    }
    
    protected function codificacion($texto){
	$c = 0;
	$ascii = true;
	for ($i = 0;$i<strlen($texto);$i++) {
		$byte = ord($texto[$i]);
		if ($c>0) {
			if (($byte>>6) != 0x2) {
				return ISO_8859_1;
			} else {
				$c--;
			}
		} elseif ($byte&0x80) {
			$ascii = false;
			if (($byte>>5) == 0x6) {
				$c = 1;
			} elseif (($byte>>4) == 0xE) {
				$c = 2;
			} elseif (($byte>>3) == 0x1E) {
				$c = 3;
			} else {
				return ISO_8859_1;
			}
		}
	}
	return ($ascii) ? ASCII : UTF_8;
    }
    
    
    protected function get_extension($filename){
		$x = explode('.', $filename);
		return '.'.end($x);
	}
        
    protected function set_error($msg){
        if (is_array($msg))
        {
                foreach ($msg as $val)
                {
                         $this->messagesHandler->admin_notice($msg, 'error');
                }
        }
        else
        {
               $this->messagesHandler->admin_notice($msg, 'error');

        }
    }
        
    // --------------------------------------------------------------------

    /**
     * File MIME type
     *
     * Detects the (actual) MIME type of the uploaded file, if possible.
     * The input array is expected to be $_FILES[$field]
     *
     * @param	array
     * @return	void
     */
    protected function _file_mime_type($file)
    {
            // Use if the Fileinfo extension, if available (only versions above 5.3 support the FILEINFO_MIME_TYPE flag)
            if ( (float) substr(phpversion(), 0, 3) >= 5.3 && function_exists('finfo_file'))
            {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    if ($finfo !== FALSE) // This is possible, if there is no magic MIME database file found on the system
                    {
                            $file_type = $finfo->file($file['tmp_name']);

                            /* According to the comments section of the PHP manual page,
                             * it is possible that this function returns an empty string
                             * for some files (e.g. if they don't exist in the magic MIME database)
                             */
                            if (strlen($file_type) > 1)
                            {
                                    $this->file_type = $file_type;
                                    return;
                            }
                    }
            }

            // Fall back to the deprecated mime_content_type(), if available
            if (function_exists('mime_content_type'))
            {
                    $this->file_type = @mime_content_type($file['tmp_name']);
                    return;
            }

            /* This is an ugly hack, but UNIX-type systems provide a native way to detect the file type,
             * which is still more secure than depending on the value of $_FILES[$field]['type'].
             *
             * Notes:
             *	- a 'W' in the substr() expression bellow, would mean that we're using Windows
             *	- many system admins would disable the exec() function due to security concerns, hence the function_exists() check
             */
            if (DIRECTORY_SEPARATOR !== '\\' && function_exists('exec'))
            {
                    $output = array();
                    @exec('file --brief --mime-type ' . escapeshellarg($file['tmp_path']), $output, $return_code);
                    if ($return_code === 0 && strlen($output[0]) > 0) // A return status code != 0 would mean failed execution
                    {
                            $this->file_type = rtrim($output[0]);
                            return;
                    }
            }

            $this->file_type = $file['type'];
    }

    
}
