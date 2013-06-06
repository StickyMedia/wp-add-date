<?php
function date_install(){
	$args = array(
			'post_type' => 'post',
			'meta_query' => array(array(
            	'key' => 'the_date',
            	'compare' => 'NOT EXISTS'
        	),),
    	);

	$posts = new WP_Query($args);	
	foreach($posts->get_posts() as $post){
		add_metadata( 'post', $post->ID, 'the_date', '' );
	}
}