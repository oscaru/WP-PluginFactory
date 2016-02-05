<?php

namespace PluginCore;

class ActionHandler {
    
    
    protected $actions;

	protected $filters;

	public function __construct() {

		$this->actions = array();
		$this->filters = array();

	}

	

	public function add( $hooks, $hook, $callback, $priority , $accepted_args  ) {
     
        
        $this->{$hooks}[] = array(
			'hook'      => $hook,
			'callback'  => $callback,
            'priority'  => $priority,
            'accepted_args'  => $accepted_args
		);

		return $this->{$hooks};

	}

	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], $hook['callback'] ,$hook['priority'] , $hook['accepted_args']);
		}

		foreach ( $this->actions as $hook ) {
		     add_action( $hook['hook'], $hook['callback'] , $hook['priority'] , $hook['accepted_args'] );
		}

	}

    
    
    
}
