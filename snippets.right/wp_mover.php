add_filter( 'toolset_rest_run_exposure_filters', '__return_true' );
add_action( 'rest_api_init', function () {
    foreach (array_keys(get_taxonomies()) as $taxonomy) register_rest_field( $taxonomy, 'translations', [
        'get_callback' => function( $term ) {
            $trid = apply_filters( 'wpml_element_trid', false, $term['id'], 'tax_category');
            if (!$trid) return [];
            $translations = apply_filters( 'wpml_get_element_translations', false, $trid, 'tax_category');
            if (!$translations) return [];
            $result = [];
            foreach($translations as $lang => $tr) if ($tr->element_id != $term['id']) $result[$lang] = (int) $tr->element_id;
            return $result;
        },
        'schema' => [
            'description' => __( 'Taxonomy Translations.' ),
            'type'        => 'array'
        ]
    ]);
    $post_types = array_merge(['post', 'page'], array_keys(get_post_types( 
        [
            'public'   => true,
            '_builtin' => false,
            
        ]
        , 'names', 'and' )));
    
    foreach ($post_types as $post_type) register_rest_field( $post_type, 'translations', [
        'get_callback' => function( $post ) {
            $type = apply_filters( 'wpml_element_type', get_post_type( $post['id'] ) );
            $translations = apply_filters( 
                'wpml_get_element_translations', 
                array(), 
                apply_filters( 'wpml_element_trid', false, $post['id'], $type ), 
                $type );
            
            $result = [];
            foreach($translations as $lang => $tr) if ($tr->element_id != $post['id']) $result[$lang] = (int) $tr->element_id;
            return $result;
        },
        'schema' => [
            'description' => __( 'Post Translations.' ),
            'type'        => 'array'
        ]
    ] );
} );
add_action( 'init', function() {
    if (!function_exists('toolset_get_fields_for_element')) return;
    $after_insert = function($post, $request) {
        $toolset_fields = toolset_get_fields_for_element($post->ID, \OTGS\Toolset\Common\PublicAPI\ElementDomain\POSTS);
        
        if (isset($request['toolset-meta'])) foreach($request['toolset-meta'] as $field_group => $fields) foreach($fields as $field_name => $field_value) if ($field_value['raw'] && array_key_exists($field_name, $toolset_fields)) {
            $value = $field_value['raw'];
            update_post_meta($post->ID, "wpcf-{$field_name}", $value);
        }
        foreach ($toolset_fields as $field_name => $field ) {
            if (isset($request[$field_name])) update_post_meta($post->ID, "wpcf-{$field_name}", $request[$field_name]);
        }
        return $post;
    };
    foreach (get_post_types() as $post_type) add_action( "rest_after_insert_{$post_type}", $after_insert, 10, 3 );
}, 99);
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
