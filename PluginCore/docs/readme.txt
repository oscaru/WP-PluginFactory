El slug el namespace del Plugin.
El nombre del directorio ha de ser el namespace.

Los mensaje de error entre llamadas see guardan en sesion.

el único directorio obligatorio es controllers

//para compartir variables entre clases utilizamos container

    $container = \PluginCore\Container::getInstance();
    $container->register('wpdb',$wpdb);
    ....
    $wpdb = container->get('wpdb');

//container es común a todos los plugins 
 Podemos usar 
    $container->register('OnTeam/miVariable',$miVariable);


//llamar un  controlador
     $this->add_action('init', 'CustomAdmin/registerPosts'); donde CustomAdmin es la clase y registerPosts el metodo


//mensajes en el admin:

    $messages = \PluginCore\Messages::getInstance();
    $messages->admin_notice('tests','updated');

//cargar un modelo en models/
    $teamModel = new \OneTeam\models\TeamModel($wpdb); //OneTeam es el slug del Plugin
   
// un hook propio para ajax 

plugincore_ajax_<action>

donde action es el campo action de $_GET o $_POST