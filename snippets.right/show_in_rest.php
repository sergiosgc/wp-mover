add_filter('register_post_type_args', function($args, $post_type) {
	$args['show_in_rest'] = true;
	return $args;
}, 10, 2);
