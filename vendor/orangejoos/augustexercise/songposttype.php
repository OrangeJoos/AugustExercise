<?php
/**
* Plugin Name: Song Post Type
* Description: Plugin Created for August Practical Exercise 1
* Version: 2.0
* Author: Joshua Smith
**/
session_start(); //Needed up here
if(empty($_SESSION['formdata'])){ //If theres no form data
	$_SESSION['formdata'] = array();
}

function create_song_post_type(){
	
	register_post_type('songs', array(
		'label' => 'Songs', //Unfamiliar with other labels
		//Public is False by Default, but user should be able to see posts
		'public' => true,
		'show_ui' => true,
		'show_in_nav_menus' => true,
		'has_archive' => true,
		'rewrite' => true,
		'exclude_from_search' => false,
		'show_in_rest' => true,
		//'taxonomies' => array(create_taxonomy()),// Probably not needed anymore, but works...
		)
	);	
}

add_action('init', 'create_song_post_type');

function create_taxonomy(){
	register_taxonomy('genres', array('songs'), array(
		'hierarchical' => true, 
		'label' => 'Genres',
		'public' => true,
		'show_ui' => true,
		'rewrite' => array('slug' => 'genre'),
	));
	//Add Terms into taxonomy
	wp_insert_term(
	'Classical', //Name of term
	'genres', //Taxonomy being linked to
	array(	
		'description' => 'Classical Songs',
		'slug' => 'classical'
	)
	);
	

}

add_action('init', 'create_taxonomy');


function setdefaultterm($post_id){
	if(!has_term('', 'genres')){ //If the post has no terms on it
		//Give it classical term
		$term = get_term_by('slug', 'classical', 'genres'); //Gets classical term
		$id = $term->term_id; //Name works aswell, but id is better in case of child terms
		wp_set_object_terms($post_id, $id, 'genres', true); //Sets the current post
	}

}

add_action('save_post', 'setdefaultterm', 10 , 1); //Pass id of post

function add_form() {//
    if ( isset( $_POST['add'] ) ) {
			$details = "Name: " . $_POST['name'] . " Email: " . $_POST["email"];
			array_push($_SESSION['formdata'], $details); 
			echo "Successfully submitted data!";
    }
    ?> 
	<form method='post' action=''> 
			Name: <input type='text' name='name'><br>
			E-mail: <input type='text' name='email'><br>
			<input type='submit' name='add'>
	</form>
    <?php
	

}

add_shortcode('form', 'add_form');

	
function formRESTRegister(){
	register_rest_route('forms', 'data', array(
		'methods' => array('GET', 'POST'),
		'callback' => 'formData',
		));
}

function formData(){
		session_start();
		return $_SESSION['formdata'];
	//	foreach($_SESSION['formdata'] as $data){
	//		echo "$data <br>"; //Not needed since it actually doesn't store any data in the REST
	//	}
}
	

add_action('rest_api_init', 'formRESTRegister');
//Set action to new REST endpoint
?>