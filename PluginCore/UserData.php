<?php

//class to manage sessions an userdata in wp. as codeigniter style

namespace PluginCore;

class UserData {
	
	protected $flashData = array(); // datos disponibles sólo en la siguiente petición
	protected $paginaAnterior = '';
    protected $paginaActual  = '';
    protected static $instance = null;
    
    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new static();
        }
        return self::$instance;
    }
    
	private function __construct(){
		//iniciamos la session
        
		if ( !session_id() ){
			session_start();
		}
		
		$this->getFlashData();
		$this->setPaginaActual();
	}
	
	//obtenemos flashdata y/o inicializamos el array;
	protected function getFlashData(){
		if( isset($_SESSION['userdata']['_flashdata']) && $_SESSION['userdata']['_flashdata']!== NULL){
			foreach ( $_SESSION['userdata']['_flashdata'] as $key => $val){
				$this->flashData[$key] = $val;
			}
			$_SESSION['userdata']['_flashdata'] = NULL;
		}
	}
	
    protected function setPaginaActual(){
        $now   = 'http://'. $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        $anterior = $this->data('pagina_anterior');
        $actual   = $this->data('pagina_actual');
          
        if(empty($anterior)) $anterior= get_bloginfo ('url');
       
        if($actual != $now){
            $anterior = $actual;
            $actual   = $now;
            
        }
        
       
        $this->paginaAnterior = $anterior;
        $this->paginaActual   = $actual;
   
        $this->set_data('pagina_anterior', $this->paginaAnterior);
        $this->set_data('pagina_actual',   $this->paginaActual);
        
        
    }
    
    public function getActualPage(){
        return $this->paginaActual;
    }
    
    
    public function getPrevPage(){
        return $this->paginaAnterior;
    }
    
	public function set_flashdata ($newdata = array(), $newval = ''){
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}
		
		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $value)
			{
				$_SESSION['userdata']['_flashdata'][$key]= $value;
				$this->flashData[$key]= $value;
			}
		}
		
	}
	
	
			
	public function data($item, $default = FALSE)	{
		if (isset($this->flashData[$item]))
			return $this->flashData[$item];
		elseif( isset($_SESSION['userdata'][$item]) )
			return $_SESSION['userdata'][$item];
		else return $default;
	}
	
	public function set_data($newdata = array(), $newval = ''){
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$_SESSION['userdata'][$key] = $val;
			}
		}
		
	}
	

	public function all_data (){
	   return array_merge($_SESSION['userdata'], $this->flashData);
	}
	
	//vuelve a poner los datos flash para otra peticion
	public function keep_flashdata(){
          	foreach ($this->flashData as $key => $val){
			 $this->set_flashdata($key , $val);
		}
        }
        
	public function unset_data($item){
		if (isset ($_SESSION['userdata'][$item])){
			unset($_SESSION['userdata'][$item]) ;
		}
	}
	
	public function remove_flashdata(){
		$_SESSION['userdata']['_flashdata']= NULL;
		$this->flashData = array();
		
	}
	public function removeAll(){
		$_SESSION['userdata']= array();
	}
		
	public function destroy(){
		session_destroy();
	}
	public function test(){
		return $this->flashData;
	}
}


