<?php
    /* 
    Plugin Name:Insert Update Delete Plugin
    Version: 2.0
    Plugin URI: http://9code.in
    Author: Mr. Hardik Vegad
    Author URI: http://9code.in
    Description: Insert Update Plugin With Description
    */ 
    
    // include necessary files
    include_once 'insert_update_delete_function.php';
    
    //When plugin activated then which functin
    register_activation_hook( __FILE__, 'create_database' );
    add_action('admin_menu','add_menu');
?>
