function custom_rest_link_translation($data) {
	if (!isset($data['left'])) return new WP_Error( 'missing_left', 'Missing left parameter', array('status' => 500) );
	if (!isset($data['right'])) return new WP_Error( 'missing_right', 'Missing right parameter', array('status' => 500) );
	if (!isset($data['lang'])) return new WP_Error( 'missing_lang', 'Missing lang parameter', array('status' => 500) );

	$left = get_post((int) $data['left']);
	if (!$left) return new WP_Error( 'missing_left', 'Invalid left post id', array('status' => 500) );
	$right = get_post((int) $data['right']);
	if (!$right) return new WP_Error( 'missing_right', 'Invalid right post id', array('status' => 500) );
	$lang = $data['lang'];

	$wpml_element_type = apply_filters( 'wpml_element_type', $left->post_type );
    $get_language_args = array('element_id' => $left->ID, 'element_type' => $left->post_type );
    $original_post_language_info = apply_filters( 'wpml_element_language_details', null, $get_language_args );
    $set_language_args = [
        'element_id'    => $right->ID,
        'element_type'  => $wpml_element_type,
        'trid'   => $original_post_language_info->trid,
        'language_code'   => $lang,
        'source_language_code' => $original_post_language_info->language_code,
        'check_duplicates' => true
    ];
    $value = do_action( 'wpml_set_element_language_details', $set_language_args );
    return new WP_REST_Response( array( 'message' => 'Posts linked' ), 200 );
}


add_action('rest_api_init', function () {
    register_rest_route('link_translation', '/post', array(
        'methods' => 'POST',
        'callback' => 'custom_rest_link_translation',
    ));
});
