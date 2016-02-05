<?php
/*
 *   Sirve como implementacion de un template simple. 
 *   Uso . 
 *    en los templates : $nombre_variable_1. 
 * 
 *    $Mytemplate = new $(directorio);
 *    $Mytemplatee->nombre_variable_1 = val 1;
 *    $Mytemplate->nombre_variable_2 = val2;
 * 
 *     Tambien:
 *      $Mytemplatee->templates_variables = array(
 *          'nombre_variable_1' => val 1,
 *          'nombre_variable_2' => val 2
 *      );
 * 
 * 
 *    $Mytemplate->display($file);  or $content = $Mytemplate->getContent($file);
 *    
 *     ourrea
 */

namespace PluginCore\libraries;

class MicroTemplate {
    protected $template_dir;
    public    $templates_variables = array();
    
    public function __construct($templatedir = '') {
        if(!empty($templatedir)) $this->setDir ($templatedir);
        
    }
    
    public function setDir($templatedir){
        $this->template_dir = rtrim($templatedir, '\\/');
    }
    
    public function display($file){
        extract($this->templates_variables);
        include ($this->template_dir.DIRECTORY_SEPARATOR.$file); 
    }
    
    public function getContent($file){
        extract($this->templates_variables);
        ob_start();
        include ($this->template_dir.DIRECTORY_SEPARATOR.$file); 
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    public function __set($name, $value) {
       $this->templates_variables[$name]=$value;
    }
    /**
     * Si una vatiable no está definida devuelve un string vacío
     * @param type $name
     * @return string 
     */
    public function __get($name){
        if(!empty($this->templates_variables[$name])) return $this->templates_variables[$name];
        return '';
    }
    
}