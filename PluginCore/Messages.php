<?php

namespace PluginCore;

class Messages 
{
    protected $messages = array();
    protected static $instance = null;
    
    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new static();
        }
        return self::$instance;
    }
    
	private function __construct(){
		add_action('admin_notices', array($this,'showAdminNotices'));
	}
    
    
    public function admin_notice($message, $type='error'){
        if(!in_array($type, array('error' ,'updated' ,'update-nag' ,'info'))) {
            $type = 'error';
        };
        $userdata = \PluginCore\UserData::getInstance();
        $data = $userdata->data('PluginCore');
        if(!isset($data['messages'])) $data['messages'] = array();
        if(!isset($data['messages'][$type]))$data['messages'][$type] = array();
        $data['messages'][$type][md5($message)] = $message;   //md5 permite un único mensaje en repetidas redirecciones
        
        $data = $userdata->set_data('PluginCore',$data);
    }
    
    public function showAdminNotices(){
        $userdata = \PluginCore\UserData::getInstance();
        $data = $userdata->data('PluginCore');
        if ( !empty($data['messages']) ) foreach( $data['messages'] as $type => $messages ){
            switch ($type){
                case 'info':
                case 'updated':
                    $style = 'color: green ';
                    $class = 'updated';
                    break;
                case 'update-nag':
                    $style = 'color: orange ';
                    $class = 'update-nag';
                    break;
                case 'error':
                default:
                    $class = 'error';
                    $style = 'color: red ';
                
            }
            foreach($messages as $message) {
                ?>
                    <div class="<?= $class ?>">
                        <p style="<?= $style ?>"><?= $message ?></p>
                    </div>
                <?php 
            }
        }
        if(isset($data['messages'])) unset($data['messages']) ;
        $data = $userdata->set_data('PluginCore');
    }
}