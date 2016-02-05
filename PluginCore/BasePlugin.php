<?php

namespace PluginCore;

class BasePlugin {
    
    protected $slug;
    protected $actionHandler;
    protected $pluginDir;
    
    
    
    function __construct($slug, $pluginDir) {
        $this->slug = $slug;
        $this->pluginDir  = $pluginDir;
        $this->actionHandler = new \PluginCore\ActionHandler();
        
    }

    public function run(){
        if(is_ajax()){
            $this->add_action('init', array($this,'runAjax'));
        }
        $this->actionHandler->run();
    }
    
    public function runAjax(){
        $action = $_REQUEST['action'];
        do_action('plugincore_ajax_'.$action);
    }
        
    
    public function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
        if(is_string($callback) && !function_exists($callback)) {
           $callback = array($this,$callback);
        }
		$this->actionHandler->add( 'actions', $hook,  $callback ,$priority, $accepted_args);
	}

	public function add_filter( $hook, $callback,$priority = 10, $accepted_args = 1  ) {
        if(is_string($callback) && !function_exists($callback)) {
           $callback = array($this,$callback);
        }
		$this->actionHandler->add( 'filters', $hook, $callback ,$priority ,$accepted_args );
	}
    
    
    public function __call($name, $arguments) {
        $container = \PluginCore\Container::getInstance();
        list($className,$method) = explode('/',$name);
        $registerName = "{$this->slug}\\{$className}";
        $component = $container->get($registerName);
        if(!$component){
            require $this->pluginDir."/controllers/{$className}.php";
            $class = '\\'.$this->slug.'\\'.$className;
            $component = new $class ();
            $container->register($registerName,$component);
        }
        $component->{$method}($arguments);
    }
    
    
    
}

