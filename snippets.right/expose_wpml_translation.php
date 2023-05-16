add_action( 'rest_api_init', function () {
    register_rest_field( 'post', 'translations', [
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
