<?php
    //Function Create Table
    function create_database()
    {
         require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
         
         $query="CREATE TABLE IF NOT EXISTS Friend_List(
                FriendsID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                FriendsName VARCHAR(40),
                FriendsMobile VARCHAR(10),
                City VARCHAR(20),
                State VARCHAR(20),
                Country VARCHAR(20)
             )ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
         
         dbDelta($query);   //This function use to create table
    }
    function add_menu()
    {
         //Add Menu Page To Admin Site
         add_menu_page("Friend List", "Friend List", "friend_cap", "9code_friend_list_slug","fun_operations");
         /*
          * here Argument 1 ==> Page Title
          *               2 ==> Menu Title
          *               3 ==> Capabilities (Which Use Can View This Page)
          *               4 ==> Page Slug Name
          *               5 ==> Function Name Call When Page Menu Click
          */
         
         $role = get_role( 'administrator' );       //get Role
         $role->add_cap('friend_cap');      //Page Capabilities Here
    }
    
    
    //call when page menu click
    function fun_operations()
    { 
        if(isset($_POST["submit"]))
        {
            $id = (isset($_POST['FriendsID'])) ? $_POST['FriendsID'] : 0;
            do_action("save_friends_hook",$id);
        }
        $id = (isset($_GET['FriendsID'])) ? $_GET['FriendsID'] : 0;
        do_action("add_friends_hook",$id);
        if($_GET["action"] == "delete")
            do_action("delete_friends_hook",$id);
        do_action("view_friends_hook");
        
        //Here do_action has two argument 1==> Hook name you can define any hook name here
                                       // 2==> Parameter you can pass paramentet as your requirement
    }
    
    
    function head_script()
    {
        ob_start();
        ?>
        <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>css/DT_bootstrap.css">
        <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>css/bootstrap.css">
        <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__); ?>css/styles.css">
        <script src="<?php echo plugin_dir_url(__FILE__); ?>js/jquery-1.9.1.min.js"></script>
        <script src="<?php echo plugin_dir_url(__FILE__); ?>js/jquery.dataTables.js"></script>
        <script src="<?php echo plugin_dir_url(__FILE__); ?>js/DT_bootstrap.js"></script>
        <?php
    }
    add_action('admin_head','head_script');
    
    add_action("add_friends_hook", "add_friends_form");
    add_action("save_friends_hook","save_friends");
    add_action("delete_friends_hook","delete_friends_detail");
    add_action('view_friends_hook','view_friends');
    
    add_shortcode( 'myshortcode', 'fun_operations' );
    
    function add_friends_form($FriendsID)
    {
        global $wpdb;
        if( (isset($_GET['action']) && $_GET['action']=='update'))
            $sel_friend=$wpdb->get_results("SELECT * FROM Friend_List WHERE FriendsID=".$FriendsID);    //Select Query Fire
        
        ?>
        <form name="9code_friends_list"  method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
            <?php if($FriendsID!=0)
                        echo "<input type=hidden name='FriendsID' id='FriendsID' value='".$FriendsID."'>";
                ?>
            <div id="mytable2">   
                <table width="96%">
                     <caption>Friend List Management</caption>
                    <tr>
                        <td style="width:30%">Friends Name:</td>
                        <td style="width:45%;float:left;"><input type="text" name="txtFriendName" required value="<?php echo $sel_friend[0]->FriendsName ;?>"></td>
                    </tr>
                    <tr>
                        <td>Mobile:</td>
                        <td style="width:45%;float:left;"><input type="text" name="txtMobile" required value="<?php echo $sel_friend[0]->FriendsMobile ;?>"></td>
                    </tr>
                    <tr>
                        <td>City:</td>
                        <td style="width:45%;float:left;"><input type="text" name="txtCity" required value="<?php echo $sel_friend[0]->City ;?>"></td>
                    </tr>
                    <tr>
                        <td>State:</td>
                        <td style="width:45%;float:left;"><input type="text" name="txtState" required value="<?php echo $sel_friend[0]->State ;?>"></td>
                    </tr>
                    <tr>
                        <td>Country:</td>
                        <td style="width:45%;float:left;"><input type="text" name="txtCountry" required value="<?php echo $sel_friend[0]->Country ;?>"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center">
                            <input type="submit" name="submit" value="<?php if($FriendsID!=0) echo "Update"; else echo "ADD"; ?>" />
                            <?php if($FriendsID!=0) echo "<a href='admin.php?page=9code_friend_list_slug' class='button2'>Add New</a>"; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
        <?php 
    }
    
    function save_friends($FriendsID)
    {
        global $wpdb;
        $insert=array(
            'FriendsName'=>$_POST['txtFriendName'],
            'FriendsMobile'=>$_POST['txtMobile'],
            'City'=>$_POST['txtCity'],
            'State'=>$_POST['txtState'],
            'Country'=>$_POST['txtCountry']
        );
        if($FriendsID==0)
        {
            $wpdb->insert("Friend_List",$insert);
        }
        else
        {
             $wpdb->update("Friend_List",$insert, array('FriendsID'=>$FriendsID));
        }
    }
    
    function delete_friends_detail($FriendsID)
    {
        global $wpdb;
        $wpdb->query("DELETE FROM Friend_List WHERE FriendsID=".$FriendsID);
    }
    
    function view_friends()
    {
        global $wpdb;
        ?>
        <br>
        <div style="width:97%;margin-left:17px;">
        <table id="example" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" >
            <?php
            $rec = $wpdb->get_results("SELECT * FROM Friend_List");
            if ($rec[0]->FriendsID != "") 
            {
            ?>
                <thead>
                <tr>
                    <th align="center" scope="col">Friends Name</th>
                    <th align="center" scope="col">Mobile</th>
                    <th align="center" scope="col">City</th>
                    <th align="center" scope="col">State</th>
                    <th align="center" scope="col">Country</th>
                    <th align="center" scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    $i = 1;
                    foreach ($rec as $event) 
                    {
                        ?>
                        <tr>
                            <td align="center" scope="col"><?php echo $event->FriendsName; ?></td>
                            <td align="center" scope="col"><?php echo $event->FriendsMobile ?></td>
                            <td align="center" scope="col"><?php echo $event->City; ?></td>
                            <td align="center" scope="col"><?php echo $event->State ?></td>
                            <td align="center" scope="col"><?php echo $event->Country; ?></td>
                            <td align="center" scope="col">
                                <a href="admin.php?page=9code_friend_list_slug&FriendsID=<?php echo $event->FriendsID ?>&action=update" style="text-decoration: none"><img src="<?php echo plugin_dir_url(__FILE__)."images/icon-edit.png" ?>">&nbsp; &nbsp; </a>
                                <a href="admin.php?page=9code_friend_list_slug&FriendsID=<?php echo $event->FriendsID ?>&action=delete" style="text-decoration: none" onclick="return confirm('Are you sure you want to delete?')"><img src="<?php echo plugin_dir_url(__FILE__)."images/icon-delete.png" ?>"> </a>
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
