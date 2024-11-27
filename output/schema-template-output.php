<?php
/**
 * Output Schema template Page
 *
 * @author   Magazine3
 * @category Frontend
 * @path  output/schema-template-output
 * @version 1.39
 */
if (! defined('ABSPATH') ) exit;


/**
 * Genarate schema template markup
 * @param 	$schema_post_id 	integer
 * @return 	array
 * @since 	1.39
 * */
function saswp_get_schema_template_markup( $schema_post_id, $field_key ){
	
	$response 	=	array();

	$template_field   = get_post_meta($schema_post_id, 'saswp_schema_template_field', true);

    if ( ! empty( $template_field ) && is_array( $template_field ) ) {

        foreach ( $template_field as $tf_key => $tf_value ) {

            if ( ! empty ( $tf_value ) && is_array( $tf_value ) && $field_key == $tf_key ) {

                foreach ( $tf_value as $template_id ) {
                    if ( ! empty( $template_id ) ) {
                    	$response[] 	=	saswp_generate_schema_template_markup( $template_id );
                    }
                }

            }

        }

    }
   
    return $response;

}

/**
 * Get schema template meta data
 * @param 	$template_id 	integer
 * @return 	array
 * @since 	1.39
 * */
function saswp_get_schema_template_meta( $template_id ) {

	$meta 						=	array();
	$meta['template_type'] 		=	'';
	$meta['template_id'] 		=	$template_id;
	$meta['template_options'] 	=	array();

	$meta['template_type'] 		=	get_post_meta( $template_id, 'schema_type', true );
	$meta['template_options'] 	=	get_post_meta( $template_id, 'schema_options', true );
		
	return $meta;	
		
}

/**
 * Prepare schema template markup
 * @param 	$template_id 	integer
 * @return 	array
 * @since 	1.39
 * */
function saswp_generate_schema_template_markup( $template_id ){

	global $sd_data;

	$template_meta 				=	saswp_get_schema_template_meta( $template_id );
	$template_type 				=	$template_meta['template_type'];
	$template_options 			=	$template_meta['template_options'];

	$service_object     		=	new SASWP_Output_Service();
	$aggregateRating    		=	$service_object->saswp_rating_box_rating_markup( saswp_get_the_ID() );
	$extra_theme_review 		=	$service_object->saswp_extra_theme_review_details( saswp_get_the_ID() );

	$input1 					=	array();

	switch ( $template_type ) {

		case 'Service':

            $input1['@type']    = 	$template_type;  
                                                                                                                                                                                    
            $input1 			=	apply_filters( 'saswp_modify_service_schema_output', $input1 );
            $input1 			=	saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

		break;

		case 'Article':

			$input1 			= $service_object->saswp_schema_markup_generator( $template_type );
                                
            $mainentity 		= saswp_get_mainEntity( $template_id );
            
            if($mainentity){
               $input1['mainEntity'] = $mainentity;                                     
            }

            $input1 			= apply_filters( 'saswp_modify_article_schema_output', $input1 );  
            $input1 			= saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

            if ( isset( $input1['@context'] ) ) {
            	unset( $input1['@context'] );
            }
            if ( isset( $input1['@id'] ) ) {
            	unset( $input1['@id'] );
            }

		break;

		case 'WebPage':
                                                                
            $input1 = $service_object->saswp_schema_markup_generator( $template_type, $template_id );
            
            if ( isset( $sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1 ) {
                $input1['comment'] = saswp_get_comments( get_the_ID() );
            }                                
            if ( ! empty( $aggregateRating) ) {
                $input1['mainEntity']['aggregateRating'] = $aggregateRating;
            }                                
            if ( ! empty( $extra_theme_review ) ) {
               $input1 			= array_merge( $input1, $extra_theme_review );
            }
            
            $input1 = apply_filters( 'saswp_modify_webpage_schema_output', $input1 );   
         
            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

            if ( isset( $input1['@context'] ) ) {
            	unset( $input1['@context'] );
            }
            if ( isset( $input1['@id'] ) ) {
            	unset( $input1['@id'] );
            }

        break;

	}
	
	return $input1;

}