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
add_filter('register_post_type_args', function($args, $post_type) {
    $args['show_in_rest'] = true;
    return $args;
}, 10, 2);
