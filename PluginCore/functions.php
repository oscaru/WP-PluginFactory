<?php

namespace PluginCore;

function is_ajax(){
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')? true : false;
}


function add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null ) {
	global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages;

	$menu_slug = plugin_basename( $menu_slug );

	$admin_page_hooks[$menu_slug] = sanitize_title( $menu_title );

	$hookname = get_plugin_page_hookname( $menu_slug, '' );

	if ( !empty( $function ) && !empty( $hookname ) && current_user_can( $capability ) )
		add_action( $hookname, $function );

	if ( empty($icon_url) ) {
		$icon_url = 'dashicons-admin-generic';
		$icon_class = 'menu-icon-generic ';
	} else {
		$icon_url = set_url_scheme( $icon_url );
		$icon_class = '';
	}

	$new_menu = array( $menu_title, $capability, $menu_slug, $page_title, 'menu-top ' . $icon_class . $hookname, $hookname, $icon_url );

  
	if ( null === $position )
		$menu[] = $new_menu;
	else{
        $menu = insertInArray($menu,$position,$new_menu);
    }
    
    
	$_registered_pages[$hookname] = true;

	// No parent as top level
	$_parent_pages[$menu_slug] = false;

	return $hookname;
}


function insertInArray($old,$position,$new_value){
    ksort($old);
    $actual = '';
    $anterior = $new_value;
    foreach($old as $id => $value){
        if($id < $position) continue;
        $actual = $value;
        $old[$id]=$anterior;
        $anterior = $actual;
    }
    $old[] = $anterior;
    return $old;
}


function isNew(){
    if(empty($_POST['post_status']) || $_POST['original_post_status'] ) return false;
    if ( ( $_POST['post_status'] == 'publish' ) && ( $_POST['original_post_status'] != 'publish' ) ) {
        return true;
    }
    return false;
}


