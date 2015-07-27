<?php
/**
 * @package contributors
 * @version 1.6
 */
/*
Plugin Name: Contributors
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong: Hello, Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> in the upper right of your admin screen on every page.
Author: Matt Mullenweg
Version: 1.6
Author URI: http://ma.tt/
*/

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function contributor_add_meta_box() {

	$screens = array( 'post' );

	foreach ( $screens as $screen ) {

		add_meta_box(
			'contributor_memberid',
			__( 'Contributors', 'contributor_textdomain' ),
			'contributor_meta_box_callback',
			$screen
		);
	}
}
add_action( 'add_meta_boxes', 'contributor_add_meta_box' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function contributor_meta_box_callback( $post ) {

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'contributor_save_meta_box_data', 'contributor_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$values = get_post_meta( $post->ID, '_my_meta_contributor_key', false ); 
        
	echo '<label for="contributor_new_field">';
	_e( 'Contributors', 'contributor_textdomain' );
	echo '</label> ';
	//echo '<input type="text" id="contributor_new_field" name="contributor_new_field" value="' . esc_attr( $value ) . '" size="25" />';
	$users = get_users( 'orderby=display_name' );	
        $contri_sel_names = '';
	echo '
	<div class="multiselect">
            <div class="selectBox" onclick="showCheckboxes()">
                <select>
                    <option>Select an option</option>
                </select>
                <div class="overSelect"></div>
            </div>
            <div id="checkboxes">';            
                foreach ( $users as $user ) {				                
                    if(in_array($user->id,$values)){
                        $selected = 'CHECKED="CHECKED"';
                        $contri_sel_names .= $user->display_name.', ';
                    }else{
                        $selected = '';
                    }

                    echo '<label for="one_'.$user->id.'">' .
                         '<input '.$selected.' type="checkbox" value="'.$user->id.'" id="one_'.contributor_new_field.'" name="contributor_new_field[]"/>'.
                          $user->display_name.'</label>';
                }            
        echo '
            </div>';
            echo '<p><label class="contributor-fotter">';
                _e( 'Selected Contributors :', 'contributor_textdomain' );
            echo '</label> ';
            echo '<label>';
                _e( rtrim($contri_sel_names,', '), 'contributor_textdomain' );
            echo '</label></p>';
    echo '</div>';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function contributor_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['contributor_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['contributor_meta_box_nonce'], 'contributor_save_meta_box_data' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */
	
	// Make sure that it is set.
	if ( ! isset( $_POST['contributor_new_field'] ) ) {
		return;
	}

	// Sanitize user input.
	$my_data = sanitize_text_field( $_POST['contributor_new_field'] );
        
         if ($parent_id = wp_is_post_revision($post_id)) $post_id = $parent_id;

        if (!empty($_POST['contributor_new_field']) && is_array($_POST['contributor_new_field'])) {
            delete_post_meta($post_id, '_my_meta_contributor_key');
            foreach ($_POST['contributor_new_field'] as $contributor_new_field) {
                add_post_meta($post_id, '_my_meta_contributor_key', $contributor_new_field);
            }
        }

	// Update the meta field in the database.
	//update_post_meta( $post_id, '_my_meta_contributor_key', $my_data );
}
add_action( 'save_post', 'contributor_save_meta_box_data' );