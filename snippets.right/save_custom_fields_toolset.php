add_action( 'init', function() {
	if (!function_exists('toolset_get_fields_for_element')) return;
	$after_insert = function($post, $request) {
        foreach (toolset_get_fields_for_element($post->ID, \OTGS\Toolset\Common\PublicAPI\ElementDomain\POSTS) as $field_name => $field ) {
			if (isset($request[$field_name])) update_post_meta($post->ID, "wpcf-{$field_name}", $request[$field_name]);
		}
		return $post;
	};
	foreach (get_post_types() as $post_type) add_action( "rest_after_insert_{$post_type}", $after_insert, 10, 3 );
}, 99);
