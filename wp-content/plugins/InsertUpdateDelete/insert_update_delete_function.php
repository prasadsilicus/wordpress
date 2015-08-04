<?php
    //Function Create Table
    function create_database()
    {
         require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
         
         $query="CREATE TABLE IF NOT EXISTS wp_slides(
                SlideID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                SlideName VARCHAR(40),
                OrderSlide TINYINT(1),
                PublishSlide TINYINT(1),
                FileName VARCHAR(255)
             )ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
         
         dbDelta($query);   //This function use to create table
    }
    
    
    function add_menu()
    {
         //Add Menu Page To Admin Site
         add_menu_page("Manage Slide", "Manage Slide", "slide_cap", "manage_slide_list_slug","fun_operations");
         /*
          * here Argument 1 ==> Page Title
          *               2 ==> Menu Title
          *               3 ==> Capabilities (Which Use Can View This Page)
          *               4 ==> Page Slug Name
          *               5 ==> Function Name Call When Page Menu Click
          */
         
         $role = get_role( 'administrator' );       //get Role
         $role->add_cap('slide_cap');      //Page Capabilities Here
    }
    
    
    //call when page menu click
    function fun_operations()
    { 
        if(isset($_POST["submit"]))
        {
            $id = (isset($_POST['SlideID'])) ? $_POST['SlideID'] : 0;
            do_action("save_friends_hook",$id);
        }
        $id = (isset($_GET['SlideID'])) ? $_GET['SlideID'] : 0;
        do_action("add_friends_hook",$id);
        if($_GET["action"] == "delete")
            do_action("delete_friends_hook",$id);         
        if($_GET["action"] == "publish")
            do_action("publish_friends_hook",$id); 
        do_action("view_friends_hook");
    }
    
    
    function head_script()
    {
        ob_start();
        ?>
        <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>css/DT_bootstrap.css">
        <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>css/bootstrap.css">
        <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>css/styles.css">
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="<?php echo plugin_dir_url(__FILE__); ?>js/jquery-1.9.1.min.js"></script>
        <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script src="<?php echo plugin_dir_url(__FILE__); ?>js/jquery.dataTables.js"></script>
        <script src="<?php echo plugin_dir_url(__FILE__); ?>js/DT_bootstrap.js"></script>    
        <script>
            var gbl_increment=0;
            
            $(document).ready(function() {
                
                    gbl_increment = $(document).find('#example tbody#sortable tr:eq(0)').attr('attr-order');
                    $('#example').on( 'draw.dt', function () {            
                        gbl_increment = $(document).find('#example tbody#sortable tr:eq(0)').attr('attr-order');
                        if($(document).find('#example_filter input').val()==''){
                           $(document).find('#sortable tr td span').css('display','inline') ;
                        }else{
                           $(document).find('#sortable tr td span').css('display','none') ; 
                        }
                    } );                      
                                    
                   $( "#sortable" ).sortable({
                            update: function( event, ui ) {
                               var data = 'action=order_update&start-order='+gbl_increment+'&'+$(this).sortable('serialize');
                                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                                $.post("<?php echo admin_url('admin-ajax.php'); ?>", data, function(response) {
                                        alert(response);
                                });
                            }
                    });             
             });    
        </script>
        
 
        <?php
    }
    add_action('admin_head','head_script');
    add_action("add_friends_hook", "add_friends_form");
    add_action("save_friends_hook","save_friends");
    add_action("wp_ajax_order_update","order_updates");
    add_action("delete_friends_hook","delete_friends_detail");
    add_action("publish_friends_hook","publish_friends_detail");
    add_action('view_friends_hook','view_friends');        
    add_action('wp_enqueue_scripts','load_scripts');
    add_shortcode( 'myslideshow', 'slide_operations' );
    
    
    function load_scripts() {
      wp_enqueue_script( 'jssor1', plugins_url( 'js/jssor.js', __FILE__ ),array('jquery'));   
      wp_enqueue_script( 'jssor2', plugins_url( 'js/jssor.slider.js', __FILE__ ),array('jssor1'));
    }
    
    function slide_operations(){
        global $wpdb;
        $slides = $wpdb->get_results("SELECT * FROM wp_slides WHERE PublishSlide = 1 ORDER BY OrderSlide ASC");
    ?>        
            <ul class="bxslider">
                <?php
                    foreach($slides as $slide){
                       echo"<li><img u=\"image\" src=\"".$slide->FileName."\" /></li>";
                    }
                ?>
            </div>                    
    <?php        
    }
    
    
    
    function order_updates()
    {
        global $wpdb;
       
        $i=$_POST['start-order'];        
        
        foreach($_POST['slide'] as $val){                      
          $wpdb->update('wp_slides', 
          array('OrderSlide' => $i++), 
          array('SlideID' => $val), 
          array('%d'), 
          array('%d'));
        }
        echo "Successfully Updated";wp_die();
    }
    
    function add_friends_form($SlideID)
    {
        global $wpdb;
        if( (isset($_GET['action']) && $_GET['action']=='update'))
            $sel_friend=$wpdb->get_results("SELECT * FROM wp_slides WHERE SlideID=".$SlideID);    //Select Query Fire
        
        ?>
        <form name="9code_friends_list"  method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">
            <?php if($SlideID!=0){
                echo "<input type=hidden name='SlideID' id='SlideID' value='".$SlideID."'>";
                $req = '';
            }else{
                $req = 'required';
            }
                ?>
            <div id="mytable2">   
                <table width="96%">
                     <caption>Slide List Management</caption>
                    <tr>
                        <td style="width:30%">Slide Name:</td>
                        <td style="width:45%;float:left;"><input type="text" name="txtSlideName" required value="<?php echo $sel_friend[0]->SlideName ;?>"></td>
                    </tr>                    
                    <tr>
                        <td>File Name:</td>
                        <td style="width:45%;float:left;"><input type="file" name="slideFile" <?php echo $req; ?> ></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center">
                            <input type="submit" name="submit" value="<?php if($SlideID!=0) echo "Update"; else echo "ADD"; ?>" />
                            <?php if($SlideID!=0) echo "<a href='admin.php?page=manage_slide_list_slug' class='button2'>Add New</a>"; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
        <?php 
    }
    
    function save_friends($SlideID)
    {
        global $wpdb;
        
        
        if ( ! function_exists( 'wp_handle_upload' ) ) 
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            $uploadedfile = $_FILES['slideFile'];
            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
        if ( $movefile ) {            

            if($SlideID==0)
            {
                $insert=array(
                'SlideName'=>$_POST['txtSlideName'],
                'OrderSlide'=>$wpdb->get_var( "SELECT COUNT(*) FROM wp_slides"),
                'PublishSlide'=>1,
                'FileName'=>$movefile['url']
                );
                $wpdb->insert("wp_slides",$insert);
            }
            else
            {
                 $insert=array(
                    'SlideName'=>$_POST['txtSlideName']                
                 );
                 if($movefile['url']!=''){
                    $insert['FileName'] = $movefile['url'];
                 }
                $wpdb->update("wp_slides",$insert, array('SlideID'=>$SlideID));
            }
            //echo "File is valid, and was successfully uploaded.\n";
        } else {
            //echo "Possible file upload attack!\n";
        }
        //echo "<pre>";var_dump($_FILES);die;
    }
    
    function delete_friends_detail($SlideID)
    {
        global $wpdb;
        $wpdb->query("DELETE FROM wp_slides WHERE SlideID=".$SlideID);
    }
    
    function publish_friends_detail($SlideID)
    {
        global $wpdb;
        //$wpdb->query("DELETE FROM wp_slides WHERE SlideID=".$SlideID);
        $wpdb->update('wp_slides', 
          array('PublishSlide' =>  $_GET["publish"]), 
          array('SlideID' => $SlideID), 
          array('%d'), 
          array('%d'));
    }
    
    function view_friends()
    {
        global $wpdb;
        ?>
        <br>
        <div style="width:97%;margin-left:17px;">
        <table id="example" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" >
            <?php
            $rec = $wpdb->get_results("SELECT * FROM wp_slides ORDER BY OrderSlide ASC");
            if ($rec[0]->SlideID != "") 
            {
            ?>
                <thead>
                <tr>
                    <th align="center" scope="col">Slide Name</th>
                    <th align="center" scope="col">Action</th>
                </tr>
                </thead>
                <tbody id="sortable">
                <?php
                    $i = 1;
                    foreach ($rec as $event) 
                    {
                        ?>
                        <tr id="slide-<?php echo $event->SlideID ?>" attr-order="<?php echo $event->OrderSlide ?>">                            
                            <td align="center" scope="col"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><?php echo $event->SlideName; ?></td>                           
                            <td align="center" scope="col">
                                <a href="admin.php?page=manage_slide_list_slug&SlideID=<?php echo $event->SlideID ?>&action=update" style="text-decoration: none;"><img src="<?php echo plugin_dir_url(__FILE__)."images/icon-edit.png" ?>">&nbsp; &nbsp; </a>
                                <a href="admin.php?page=manage_slide_list_slug&SlideID=<?php echo $event->SlideID ?>&action=delete" style="text-decoration: none;" onclick="return confirm('Are you sure you want to delete?')"><img src="<?php echo plugin_dir_url(__FILE__)."images/icon-delete.png" ?>"> &nbsp; &nbsp;</a>
                                <?php if($event->PublishSlide == 1) { ?>
                                    <a href="admin.php?page=manage_slide_list_slug&SlideID=<?php echo $event->SlideID ?>&action=publish&publish=0" style="text-decoration: none;" title="Publish">P&nbsp; &nbsp; </a>
                                <?php } else { ?>
                                    <a href="admin.php?page=manage_slide_list_slug&SlideID=<?php echo $event->SlideID ?>&action=publish&publish=1" style="text-decoration: none;" title="Unpublish">U&nbsp; &nbsp; </a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
                ?>
                </tbody>
        </table>
        </div>
        <?php
    }
?>
