<?php
/*
	Generate a long JSON feed of Photo Booth posts

	It is used by a Google Chrome extension:
	- https://chrome.google.com/webstore/detail/photo-booth-the-new-yorke/ecnlpbkkcihngfehdimchlekdclbofjb

	Extension source code:
	- https://github.com/newyorker/photobooth-extension
	- Pull Requests welcome!
*/

header('Content-Type: application/json');

$cache_key    = 'photobooth_js';
$cache_expire = MINUTE_IN_SECONDS * 30;

if ( false === ( $photobooth_js = get_transient( $cache_key ) ) ) {

	$the_query = new WP_Query(
		array(
			'no_found_rows'		=> true,
			'posts_per_page'	=> 88, /* 88 MPH */
			'post_type'			=> array( 'post' ),
			'post_status'		=> 'publish',
			'category_name'		=> 'photo-booth',
			'orderby'   		=> 'date', 
			'order'     		=> 'DESC'
		)
	);

	$data            = array();
	$data['title']   = "Photo Booth";
	$data['link']    = "http://www.newyorker.com/culture/photo-booth/";
	$data['updated'] = date('r');
	$data['items']   = array();

	if ( $the_query->have_posts() ) {
	    while ( $the_query->have_posts() ) {

			$the_query->the_post();

			$author = get_contributor( get_the_ID() );
			$image_meta = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "medium" );
			$image = $image_meta[0];

			if (!empty($image)) {

				$orientation = "horizontal";
				if ($image_meta[1] < $image_meta[2]) {
					$orientation = "vertical";
				}

				$item                = array();
				$item['link']        = get_permalink( get_the_ID() );
				$item['headline']    = $post->post_title;
				$item['author']      = $author[1];
				$item['image']       = $image;
				$item['orientation'] = $orientation;

				$data['items'][]     = $item;
			}
		}
	} else {
		$data['status'] = array(503, "Error messages / cannot completely convey. / We now know shared loss.");
	}

    $photobooth_js = json_encode($data);
	set_transient( $cache_key, $photobooth_js, $cache_expire );
}

print $photobooth_js;

