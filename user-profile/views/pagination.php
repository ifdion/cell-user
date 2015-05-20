<?php
	// global $post;

	$current_page = get_query_var( 'page' );
	if ($current_page == 0) {
		$current_page = 1;
	}

	$args = array(
		'base'               => $user_page_url.'%_%',
		'format'             => '%#%/',
		'total'              => $total_page,
		'current'            => $current_page,
		'show_all'           => false,
		'end_size'           => 2,
		'mid_size'           => 1,
		'prev_next'          => false,
		'prev_text'          => __('« '),
		'next_text'          => __(' »'),
		'type'               => 'plain',
		'add_args'           => false,
		// 'add_fragment'       => '',
		// 'before_page_number' => '',
		// 'after_page_number'  => ''
	);
	echo '<div class="cell-pagination">';
	echo paginate_links( $args );
	echo '</div>';

?>