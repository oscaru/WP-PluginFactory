<?php

namespace PluginCore;

class Container
{

    private static $currentPathCore = '';
    private static $instance = null;
    private $store = array();
    
    protected function __construct() {
        
        spl_autoload_register(array($this,'loader'));
       
    }
    
    public static function getInstance(){
        if(self::$instance === null){ 
            self::$currentPathCore = __DIR__;
            self::$instance = new static();
        }
        return self::$instance;
    }
    
    public static function  getPathCore(){
        return self::$currentPathCore;
    }
    
   
    public function register($name,$object){
        $this->store[$name] = $object;
    }
    
    public function get($name){
        if (isset($this->store[$name])) return $this->store[$name];
        
        //register and return model if no exist
        if(strpos($name, '_model') !== FALSE){
          $object =  "\Models\\{$name}";
          $object = new $object ($this->get('db'));
          $this->register($name, $object);
          return $object;
        }
        return NULL;
    }
    
    public function loader($className){
        $searchInFolders = array(WPMU_PLUGIN_DIR,WP_PLUGIN_DIR,__DIR__);
        $className = str_replace('\\', '/', $className);
        $className = trim($className,'/');
        $className = str_replace('PluginCore/', '', $className);
        
        foreach($searchInFolders as $folder){
            $file = $folder.'/'.$className.'.php';
            if(is_file($file)) {
                require $file;
                return;
            }
        }
    }
    
    
}

