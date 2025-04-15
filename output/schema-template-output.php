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

	$input1 					=	array();

	if ( saswp_check_if_schema_builder_is_active() ) {

		global $sd_data;

		$image_id 	        		=	get_post_thumbnail_id();
		$date 		        		=	get_the_date("c");
	    $modified_date 	    		=	get_the_modified_date("c");        
		$template_meta 				=	saswp_get_schema_template_meta( $template_id );
		$template_type 				=	$template_meta['template_type'];
		$template_options 			=	$template_meta['template_options'];

		$service_object     		=	new SASWP_Output_Service();
		$aggregateRating    		=	$service_object->saswp_rating_box_rating_markup( saswp_get_the_ID() );
		$extra_theme_review 		=	$service_object->saswp_extra_theme_review_details( saswp_get_the_ID() );
		$publisher          		=	$service_object->saswp_get_publisher();

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

	        case 'NewsArticle':
	                                                                                            
			    $image_details 	 = wp_get_attachment_image_src( $image_id );

			    $category_detail = get_the_category( get_the_ID() );//$post->ID
			    $article_section = '';

			    if( $category_detail ){

			        foreach( $category_detail as $cd){

			            if(is_object($cd) ) {
			                $article_section =  $cd->cat_name;
			            }                                        

			        }

			    }
			    
		        $word_count = saswp_reading_time_and_word_count();

		        $input1 = array(
			        '@context'			=> saswp_context_url(),
			        '@type'				=> $template_type ,
			        '@id'				=> saswp_get_permalink().'#newsarticle',
			        'url'				=> saswp_get_permalink(),
			        'headline'			=> saswp_get_the_title(),
			        'mainEntityOfPage'	=> get_the_permalink(),            
			        'datePublished'     => esc_html( $date),
			        'dateModified'      => esc_html( $modified_date ),
			        'description'       => saswp_get_the_excerpt(),
			        'articleSection'    => $article_section,            
			        'articleBody'       => saswp_get_the_content(), 
			        'keywords'          => saswp_get_the_tags(),
			        'name'				=> saswp_get_the_title(), 					
			        'thumbnailUrl'      => saswp_remove_warnings( $image_details, 0, 'saswp_string' ),
			        'wordCount'         => saswp_remove_warnings( $word_count, 'word_count', 'saswp_string' ),
			        'timeRequired'      => saswp_remove_warnings( $word_count, 'timerequired', 'saswp_string' ),            
			        'mainEntity'        => array(
			                                    '@type' => 'WebPage',
			                                    '@id'   => saswp_get_permalink(),
			        						), 
			        'author'			=> saswp_get_main_authors(),
			        'editor'            => saswp_get_edited_authors()
		        );
		            
	            $mainentity 			= saswp_get_mainEntity( $template_id );
	            
	            if ( $mainentity ) {
	            	$input1['mainEntity'] 	= $mainentity;                                     
	            }
	            
	            if ( ! empty( $publisher) ) {
	        
	                $input1 = array_merge( $input1, $publisher );   
	    
	            }                                
	            if ( isset( $sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
	                $input1['comment'] = saswp_get_comments(get_the_ID());
	            }                
	                                                                                                                            
	            $input1 = apply_filters('saswp_modify_news_article_schema_output', $input1 );

	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
		                                      
	        break;

	        case 'AnalysisNewsArticle':
	                                                                                            
	            $image_details 	 = wp_get_attachment_image_src( $image_id );

	            $category_detail = get_the_category( get_the_ID() );//$post->ID
	            $analysis_article_section = '';

	            if($category_detail){

	                foreach( $category_detail as $cd ){

	                    if ( is_object( $cd ) ) {
	                        $analysis_article_section =  $cd->cat_name;
	                    }                                        

	                }

	            }
	            
	            $word_count = saswp_reading_time_and_word_count();

	            $input1 = array(
	                '@context'			=> saswp_context_url(),
	                '@type'				=> $template_type ,
	                '@id'				=> saswp_get_permalink().'#analysisnewsarticle',
	                'url'				=> saswp_get_permalink(),
	                'headline'			=> saswp_get_the_title(),
	                'mainEntityOfPage'	=> get_the_permalink(),            
	                'datePublished'     => esc_html( $date),
	                'dateModified'      => esc_html( $modified_date),
	                'description'       => saswp_get_the_excerpt(),
	                'articleSection'    => $analysis_article_section,            
	                'articleBody'       => saswp_get_the_content(), 
	                'keywords'          => saswp_get_the_tags(),
	                'name'				=> saswp_get_the_title(), 					
	                'thumbnailUrl'      => saswp_remove_warnings( $image_details, 0, 'saswp_string' ),
	                'wordCount'         => saswp_remove_warnings( $word_count, 'word_count', 'saswp_string' ),
	                'timeRequired'      => saswp_remove_warnings( $word_count, 'timerequired', 'saswp_string' ),            
	                'mainEntity'        => array(
	                                            '@type' => 'WebPage',
	                                            '@id'   => saswp_get_permalink(),
	                						), 
	                'author'			=> saswp_get_author_details(),
	                'editor'            => saswp_get_author_details()
	            );
	                
	            $mainentity = saswp_get_mainEntity( $template_id );
	            
	            if($mainentity){
	             $input1['mainEntity'] = $mainentity;                                     
	            }
	            
	            if ( ! empty( $publisher ) ) {
	        
	                $input1 = array_merge( $input1, $publisher );   
	    
	            }                                
	            if ( isset( $sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] == 1 ){
	                $input1['comment'] = saswp_get_comments( get_the_ID() );
	            }                
	                                                                                                                            
	            $input1 = apply_filters( 'saswp_modify_analysis_newsarticle_schema_output', $input1 );
	            
	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	                    
	        break;

	        case 'AskPublicNewsArticle':
	                                                                                            
	            $image_details 	 = wp_get_attachment_image_src( $image_id );

	            $category_detail = get_the_category( get_the_ID() );//$post->ID
	            $askpublic_article_section = '';

	            if ( $category_detail ) {

	                foreach ( $category_detail as $cd ){

	                    if ( is_object( $cd ) ) {
	                        $askpublic_article_section =  $cd->cat_name;
	                    }                                        

	                }

	            }
	            
	            $word_count = saswp_reading_time_and_word_count();

	            $input1 = array(
	                '@context'			=> saswp_context_url(),
	                '@type'				=> $template_type ,
	                '@id'				=> saswp_get_permalink().'#askpublicnewsarticle',
	                'url'				=> saswp_get_permalink(),
	                'headline'			=> saswp_get_the_title(),
	                'mainEntityOfPage'	=> get_the_permalink(),            
	                'datePublished'     => esc_html( $date),
	                'dateModified'      => esc_html( $modified_date),
	                'description'       => saswp_get_the_excerpt(),
	                'articleSection'    => $askpublic_article_section,            
	                'articleBody'       => saswp_get_the_content(), 
	                'keywords'          => saswp_get_the_tags(),
	                'name'				=> saswp_get_the_title(), 					
	                'thumbnailUrl'      => saswp_remove_warnings( $image_details, 0, 'saswp_string' ),
	                'wordCount'         => saswp_remove_warnings( $word_count, 'word_count', 'saswp_string' ),
	                'timeRequired'      => saswp_remove_warnings( $word_count, 'timerequired', 'saswp_string' ),            
	                'mainEntity'        => array(
	                                            '@type' => 'WebPage',
	                                            '@id'   => saswp_get_permalink(),
	                						), 
	                'author'			=> saswp_get_author_details(),
	                'editor'            => saswp_get_author_details()
	            );
	                
	            $mainentity = saswp_get_mainEntity( $template_id );
	            
	            if($mainentity){
	             $input1['mainEntity'] = $mainentity;                                     
	            }
	            
	            if ( ! empty( $publisher) ) {
	        
	                $input1 = array_merge( $input1, $publisher );   
	    
	            }                                
	            if ( isset( $sd_data['saswp_comments_schema'] ) && $sd_data['saswp_comments_schema'] == 1 ) {
	                $input1['comment'] = saswp_get_comments( get_the_ID() );
	            }                
	                                                                                                                            
	            $input1 = apply_filters( 'saswp_modify_askpublic_newsarticle_schema_output', $input1 );
	            
	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	                    
	        break;

	        case 'BackgroundNewsArticle':
	                                                                                            
	            $image_details 	 = wp_get_attachment_image_src( $image_id );

	            $category_detail = get_the_category(get_the_ID());//$post->ID
	            $background_article_section = '';

	            if($category_detail){

	                foreach ( $category_detail as $cd ) {

	                    if ( is_object($cd ) ) {
	                        $background_article_section =  $cd->cat_name;
	                    }                                        

	                }

	            }
	            
	            $word_count = saswp_reading_time_and_word_count();

	            $input1 = array(
	                '@context'			=> saswp_context_url(),
	                '@type'				=> $template_type ,
	                '@id'				=> saswp_get_permalink().'#backgroundnewsarticle',
	                'url'				=> saswp_get_permalink(),
	                'headline'			=> saswp_get_the_title(),
	                'mainEntityOfPage'	=> get_the_permalink(),            
	                'datePublished'     => esc_html( $date),
	                'dateModified'      => esc_html( $modified_date),
	                'description'       => saswp_get_the_excerpt(),
	                'articleSection'    => $background_article_section,            
	                'articleBody'       => saswp_get_the_content(), 
	                'keywords'          => saswp_get_the_tags(),
	                'name'				=> saswp_get_the_title(), 					
	                'thumbnailUrl'      => saswp_remove_warnings( $image_details, 0, 'saswp_string' ),
	                'wordCount'         => saswp_remove_warnings( $word_count, 'word_count', 'saswp_string' ),
	                'timeRequired'      => saswp_remove_warnings( $word_count, 'timerequired', 'saswp_string' ),            
	                'mainEntity'        => array(
	                                            '@type' => 'WebPage',
	                                            '@id'   => saswp_get_permalink(),
	                						), 
	                'author'			=> saswp_get_author_details(),
	                'editor'            => saswp_get_author_details()
	            );
	                    
	            $mainentity = saswp_get_mainEntity( $template_id );
	            
	            if ( $mainentity ) {
	            	$input1['mainEntity'] = $mainentity;                                     
	            }
	            
	            if ( ! empty( $publisher) ) {
	        
	                $input1 = array_merge($input1, $publisher);   
	    
	            }                                
	            if ( isset( $sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1 ) {
	                $input1['comment'] = saswp_get_comments( get_the_ID() );
	            }                
	                                                                                                                            
	            $input1 = apply_filters('saswp_modify_background_newsarticle_schema_output', $input1 );
	            
	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	                    
	        break;

	        case 'OpinionNewsArticle':
	                                                                                            
	            $image_details 	 = wp_get_attachment_image_src( $image_id );

	            $category_detail = get_the_category( get_the_ID() );//$post->ID
	            $background_article_section = '';

	            if($category_detail){

	                foreach ( $category_detail as $cd){

	                    if( is_object( $cd ) ) {
	                        $background_article_section =  $cd->cat_name;
	                    }                                        

	                }

	            }
	            
	            $word_count = saswp_reading_time_and_word_count();

	            $input1 = array(
		            '@context'			=> saswp_context_url(),
		            '@type'				=> $template_type ,
		            '@id'				=> saswp_get_permalink().'#opinionnewsarticle',
		            'url'				=> saswp_get_permalink(),
		            'headline'			=> saswp_get_the_title(),
		            'mainEntityOfPage'	=> get_the_permalink(),            
		            'datePublished'     => esc_html( $date),
		            'dateModified'      => esc_html( $modified_date ),
		            'description'       => saswp_get_the_excerpt(),
		            'articleSection'    => $background_article_section,            
		            'articleBody'       => saswp_get_the_content(), 
		            'keywords'          => saswp_get_the_tags(),
		            'name'				=> saswp_get_the_title(), 					
		            'thumbnailUrl'      => saswp_remove_warnings( $image_details, 0, 'saswp_string' ),
		            'wordCount'         => saswp_remove_warnings( $word_count, 'word_count', 'saswp_string' ),
		            'timeRequired'      => saswp_remove_warnings( $word_count, 'timerequired', 'saswp_string' ),            
		            'mainEntity'        => array(
		                                        '@type' => 'WebPage',
		                                        '@id'   => saswp_get_permalink(),
		            						), 
		            'author'			=> saswp_get_author_details(),
		            'editor'            => saswp_get_author_details()
	            );
	                    
	            $mainentity = saswp_get_mainEntity( $template_id );
	            
	            if($mainentity){
	             $input1['mainEntity'] = $mainentity;                                     
	            }
	            
	            if ( ! empty( $publisher) ) {
	        
	                $input1 = array_merge( $input1, $publisher );   
	    
	            }                                
	            if ( isset( $sd_data['saswp_comments_schema'] ) && $sd_data['saswp_comments_schema'] ==1 ) {
	                $input1['comment'] = saswp_get_comments( get_the_ID() );
	            }                
	                                                                                                                            
	            $input1 = apply_filters( 'saswp_modify_opinion_newsarticle_schema_output', $input1 );
	            
	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	                    
	        break;

	        case 'ReportageNewsArticle':
	                                                                                            
	            $image_details 	 = wp_get_attachment_image_src( $image_id );

	            $category_detail = get_the_category( get_the_ID() );//$post->ID
	            $reportage_article_section = '';

	            if($category_detail){

	                foreach( $category_detail as $cd ) {

	                    if ( is_object($cd) ) {
	                        $reportage_article_section =  $cd->cat_name;
	                    }                                        

	                }

	            }
	            
	            $word_count = saswp_reading_time_and_word_count();

	            $input1 = array(
		            '@context'			=> saswp_context_url(),
		            '@type'				=> $template_type ,
		            '@id'				=> saswp_get_permalink().'#reportagenewsarticle',
		            'url'				=> saswp_get_permalink(),
		            'headline'			=> saswp_get_the_title(),
		            'mainEntityOfPage'	=> get_the_permalink(),            
		            'datePublished'     => esc_html( $date),
		            'dateModified'      => esc_html( $modified_date),
		            'description'       => saswp_get_the_excerpt(),
		            'articleSection'    => $reportage_article_section,            
		            'articleBody'       => saswp_get_the_content(), 
		            'keywords'          => saswp_get_the_tags(),
		            'name'				=> saswp_get_the_title(), 					
		            'thumbnailUrl'      => saswp_remove_warnings($image_details, 0, 'saswp_string'),
		            'wordCount'         => saswp_remove_warnings($word_count, 'word_count', 'saswp_string'),
		            'timeRequired'      => saswp_remove_warnings($word_count, 'timerequired', 'saswp_string'),            
		            'mainEntity'        => array(
	                                            '@type' => 'WebPage',
	                                            '@id'   => saswp_get_permalink(),
		            						), 
		            'author'			=> saswp_get_author_details(),
		            'editor'            => saswp_get_author_details()
	            );
	                    
	            $mainentity = saswp_get_mainEntity( $template_id );
	            
	            if($mainentity){
	             $input1['mainEntity'] = $mainentity;                                     
	            }
	            
	            if ( ! empty( $publisher) ) {
	        
	                $input1 = array_merge($input1, $publisher);   
	    
	            }                                
	            if ( isset( $sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
	                $input1['comment'] = saswp_get_comments(get_the_ID());
	            }                
	                                                                                                                            
	            $input1 = apply_filters('saswp_modify_reportage_newsarticle_schema_output', $input1 );
	            
	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	                    
	        break;

	        case 'ReviewNewsArticle':
	                                                  
	            $review_markup = $service_object->saswp_replace_with_custom_fields_value( $input1, $template_id );                                
	            $item_reviewed = get_post_meta( $template_id, 'saswp_review_item_reviewed_'.$template_id, true );                                          
	            $image_details 	 = wp_get_attachment_image_src( $image_id );

	            $category_detail = get_the_category( get_the_ID() );//$post->ID
	            $reportage_article_section = '';

	            if($category_detail){

	                foreach( $category_detail as $cd ) {

	                    if ( is_object( $cd ) ) {
	                        $reportage_article_section =  $cd->cat_name;
	                    }                                        

	                }

	            }
	            
	            $word_count = saswp_reading_time_and_word_count();

	            $input1 = array(
	                '@context'			=> saswp_context_url(),
	                '@type'				=> $template_type ,
	                '@id'				=> saswp_get_permalink().'#reviewnewsarticle',
	                'url'				=> saswp_get_permalink(),
	                'headline'			=> saswp_get_the_title(),
	                'mainEntityOfPage'	=> get_the_permalink(),            
	                'datePublished'     => esc_html( $date),
	                'dateModified'      => esc_html( $modified_date ),
	                'description'       => saswp_get_the_excerpt(),
	                'articleSection'    => $reportage_article_section,            
	                'articleBody'       => saswp_get_the_content(), 
	                'keywords'          => saswp_get_the_tags(),
	                'name'				=> saswp_get_the_title(), 					
	                'thumbnailUrl'      => saswp_remove_warnings( $image_details, 0, 'saswp_string' ),
	                'wordCount'         => saswp_remove_warnings( $word_count, 'word_count', 'saswp_string' ),
	                'timeRequired'      => saswp_remove_warnings( $word_count, 'timerequired', 'saswp_string' ),            
	                'mainEntity'        => array(
	                                            '@type' => 'WebPage',
	                                            '@id'   => saswp_get_permalink(),
	                						), 
	                'author'			=> saswp_get_author_details(),
	                'editor'            => saswp_get_author_details()
	            );

	            $input1['itemReviewed']['@type']  =  $item_reviewed;
	            if ( isset( $review_markup['item_reviewed']) ) {                                            
	                $item_reviewed          = array( '@type' => $item_reviewed ) + $review_markup['item_reviewed'];                                        
	                $input1['itemReviewed'] = $item_reviewed;
	                
	            }

	            $added_reviews = saswp_append_fetched_reviews( $input1, $template_id );
	    
	            if ( isset( $added_reviews['review']) ) {
	                
	                $input1['itemReviewed']['review']                    = $added_reviews['review'];
	                $input1['itemReviewed']['aggregateRating']           = $added_reviews['aggregateRating'];
	            
	            }
	            
	            $mainentity = saswp_get_mainEntity( $template_id );
	            
	            if($mainentity){
	             $input1['mainEntity'] = $mainentity;                                     
	            }
	            
	            if ( ! empty( $publisher) ) {
	        
	                $input1 = array_merge( $input1, $publisher );   
	    
	            }                                
	            if ( isset( $sd_data['saswp_comments_schema'] ) && $sd_data['saswp_comments_schema'] == 1 ){
	                $input1['comment'] = saswp_get_comments( get_the_ID() );
	            }                
	                                                                                                                            
	            $input1 = apply_filters('saswp_modify_review_newsarticle_schema_output', $input1 );
	            
	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	                    
	        break;

	        case 'Book':
	                                                                                                                                                                        
	            $input1['@context']              = saswp_context_url();
	            $input1['@type']                 = 'Book';
	            $input1['@id']                   = get_permalink().'#Book'; 
	            
	             $woo_markp = $service_object->saswp_schema_markup_generator( $template_type );

	            if($woo_markp){
	                $input1 = array_merge( $input1, $woo_markp );
	            }

	            unset($input1['brand'], $input1['mpn'], $input1['sku'],$input1['gtin8'], $input1['gtin13'], $input1['gtin12']);

	            $input1 = saswp_append_fetched_reviews( $input1, $template_id );

	            $input1 = apply_filters('saswp_modify_book_schema_output', $input1 );

	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	            
	        break;

	        case 'Course':
	                                
	            $input1 = array(
			            '@context'			=> saswp_context_url(),
			            '@type'				=> $template_type ,
			            '@id'				=> saswp_get_permalink().'#course',    
			            'name'			    => saswp_get_the_title(),
			            'description'       => saswp_get_the_excerpt(),			
			            'url'				=> saswp_get_permalink(),
			            'datePublished'     => esc_html( $date ),
			            'dateModified'      => esc_html( $modified_date ),
			            'author'			=> saswp_get_author_details(),    
			            'provider'			=> array(
		                                            '@type' 	=> 'Organization',
		                                            'name'		=> get_bloginfo(),
		                                            'sameAs'	=> get_home_url() 
			                                    )											
	                );

	            if ( ! empty( $aggregateRating ) ) {
	                $input1['aggregateRating'] = $aggregateRating;
	            }                                
	            if ( ! empty( $extra_theme_review ) ) {
	               $input1 = array_merge( $input1, $extra_theme_review );
	            }   
	            $input1 = saswp_append_fetched_reviews( $input1 );                            
	            if ( isset( $sd_data['saswp_comments_schema'] ) && $sd_data['saswp_comments_schema'] == 1 ) {
	               $input1['comment'] = saswp_get_comments( get_the_ID() );
	            }

	            $input1 = apply_filters('saswp_modify_course_schema_output', $input1, $template_id );

	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	            
	        break;

	        case 'CreativeWorkSeries':                                
	                                    
	            $input1 = array(
		            '@context'			=> saswp_context_url(),
		            '@type'				=> 'CreativeWorkSeries',
		            '@id'				=> saswp_get_current_url().'#CreativeWorkSeries',    
		            'url'				=> saswp_get_current_url(),
		            'inLanguage'        => get_bloginfo('language'),                                                                            
		            'description'       => saswp_get_the_excerpt(),                                    
		            'keywords'          => saswp_get_the_tags(),    
		            'name'				=> saswp_get_the_title(),			
		            'datePublished'     => esc_html( $date),
		            'dateModified'      => esc_html( $modified_date),
		            'author'			=> saswp_get_author_details()											
	            );
	                       
	            if ( ! empty( $publisher) ) {
	                    $input1 = array_merge( $input1, $publisher );   
	            }                              
	            if ( isset( $sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] == 1 ) {
	                $input1['comment'] = saswp_get_comments( get_the_ID() );
	            }
	            
	            $input1 = saswp_append_fetched_reviews( $input1, $template_id );

	            $input1 = apply_filters('saswp_modify_creative_work_series_schema_output', $input1 ); 

	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	                                        
	        break;

	        case 'EducationalOccupationalCredential':                                
	                                    
	            $input1 = array(
		            '@context'			=> saswp_context_url(),
		            '@type'				=> 'EducationalOccupationalCredential',
		            '@id'				=> saswp_get_permalink().'#EducationalOccupationalCredential',    
		            'url'				=> saswp_get_permalink(),                                                                                
		            'description'       => saswp_get_the_excerpt(),                                                                        
		            'name'				=> saswp_get_the_title()			                                                                     
	            );                                                                                                                                                                                        
	                                                                                    
	            $input1 = apply_filters('saswp_modify_educational_occupational_credential_schema_output', $input1 ); 

	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	                                        
	        break;

	        case 'LearningResource':

		        $input1['@context']              = saswp_context_url();
		        $input1['@type']                 = 'LearningResource';
		        $input1['@id']                   = saswp_get_permalink().'#LearningResource';                                
		        $input1['url']                   = saswp_get_permalink();  

		        $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
		        $thumbnail_url = wp_get_attachment_url( $thumbnail_id );
		        if ( ! empty( $thumbnail_url ) && is_string($thumbnail_url ) ) {
		            $image_details                   = saswp_get_image_by_url( $thumbnail_url );
		            if ( ! empty( $image_details ) && is_array( $image_details ) ) {
		                $input1['image']         = $image_details;
		            }
		        }    

		        $thumbnail_details   = wp_get_attachment_image_src( $image_id, 'thumbnail' );
		        if ( is_array( $thumbnail_details ) && isset( $thumbnail_details[0] ) ) {
		            $image_details                   = saswp_get_image_by_url( $thumbnail_details[0] );
		            if ( ! empty( $image_details) && is_array( $image_details ) ) {
		                $input1['thumbnail']     = $image_details;
		            } 
		            $input1['thumbnailUrl']  = saswp_remove_warnings( $thumbnail_details, 0, 'saswp_string' );
		        }                          

		        $input1 = apply_filters('saswp_modify_learning_resource_schema_output', $input1 );

		        $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }

		    break;

		    case 'Movie':
	                                                         
	            $input1['@context']              = saswp_context_url();
	            $input1['@type']                 = 'Movie';
	            $input1['@id']                   = saswp_get_permalink().'#Movie';                                                                                                                                              

	            $input1 = saswp_append_fetched_reviews( $input1, $template_id );

	            $input1 = apply_filters('saswp_modify_movie_schema_output', $input1 );

	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	            
	        break;

	        case 'MusicComposition':
	                                                                                                                                                                                                                                       
	            $input1['@context']              = saswp_context_url();
	            $input1['@type']                 = 'MusicComposition';
	            $input1['@id']                   = get_permalink().'#MusicComposition'; 
	            $input1['inLanguage']            = get_bloginfo('language');
	            $input1['datePublished']         = esc_html( $date);                 

	            $input1 = saswp_append_fetched_reviews( $input1, $template_id );

	            $input1 = apply_filters('saswp_modify_music_composition_schema_output', $input1 );

	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	                                                                                                                          
	        break;

	        case 'Review':
	                                                                                            
	            $review_markup = $service_object->saswp_replace_with_custom_fields_value( $input1, $template_id );                                
	            $item_reviewed = get_post_meta( $template_id, 'saswp_review_item_reviewed_'.$template_id, true );
	            
	            if($item_reviewed == 'local_business'){
	                $item_reviewed = 'LocalBusiness';
	            }
	            
	            $input1['@context']               =  saswp_context_url();
	            $input1['@type']                  =  'Review';
	            $input1['@id']                    =  saswp_get_permalink().'#Review';
	            $input1['itemReviewed']['@type']  =  $item_reviewed;                                                                
	                                        
	            if ( isset( $schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] == 1){
	                                                   
	                if($review_markup){
	                 
	                    if ( isset( $review_markup['review']) ) {
	                        
	                        $input1             =  $input1 + $review_markup['review'];
	                        
	                    }
	                    
	                    if ( isset( $review_markup['item_reviewed']) ) {                                            
	                        $item_reviewed          = array( '@type' => $item_reviewed) + $review_markup['item_reviewed'];                                        
	                        $input1['itemReviewed'] = $item_reviewed;
	                        
	                    }
	                    
	                }                                                                                                                                                                                  
	            } 
	            
	            $added_reviews = saswp_append_fetched_reviews( $input1, $template_id );
	            
	            if ( isset( $added_reviews['review']) ) {
	                
	                $input1['itemReviewed']['review']                    = $added_reviews['review'];
	                $input1['itemReviewed']['aggregateRating']           = $added_reviews['aggregateRating'];
	            
	            }                                                                                                                     
	            
	            $input1 = apply_filters('saswp_modify_review_schema_output', $input1 );
	            
	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	            
	        break;

	        case 'CriticReview':
	                                                                                            
	            $review_markup = $service_object->saswp_replace_with_custom_fields_value( $input1, $template_id );                                
	            $item_reviewed = get_post_meta( $template_id, 'saswp_review_item_reviewed_'.$template_id, true );
	            
	            if($item_reviewed == 'local_business'){
	                $item_reviewed = 'LocalBusiness';
	            }
	            
	            $input1['@context']               =  saswp_context_url();
	            $input1['@type']                  =  'CriticReview';
	            $input1['@id']                    =  saswp_get_permalink().'#CriticReview';
	            $input1['itemReviewed']['@type']  =  $item_reviewed;                                                                
	                                        
	            if ( isset( $schema_options['enable_custom_field']) && $schema_options['enable_custom_field'] == 1){
	                                                   
	                if($review_markup){
	                 
	                    if ( isset( $review_markup['review']) ) {
	                        
	                        $input1             =  $input1 + $review_markup['review'];
	                        
	                    }
	                    
	                    if ( isset( $review_markup['item_reviewed']) ) {                                            
	                        $item_reviewed          = array( '@type' => $item_reviewed) + $review_markup['item_reviewed'];                                        
	                        $input1['itemReviewed'] = $item_reviewed;
	                        
	                    }
	                    
	                }                                                                                                                                                                                  
	            } 
	            
	            $added_reviews = saswp_append_fetched_reviews( $input1, $template_id );
	            
	            if ( isset( $added_reviews['review']) ) {
	                
	                $input1['itemReviewed']['review']                    = $added_reviews['review'];
	                $input1['itemReviewed']['aggregateRating']           = $added_reviews['aggregateRating'];
	            
	            }                                                                                                                     
	            
	            $input1 = apply_filters('saswp_modify_critic_review_schema_output', $input1 );
	            
	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	            
	        break;

	        case 'SoftwareApplication':
	                                                                                                           
	            $input1 = array(
		            '@context'			=> saswp_context_url(),
		            '@type'				=> $schema_type ,
		            '@id'				=> saswp_get_permalink().'#softwareapplication',         						                        
		            'datePublished'     => esc_html( $date),
		            'dateModified'      => esc_html( $modified_date),
		            'author'			=> saswp_get_author_details()			
	            );
	                                    
	            $woo_markp = $service_object->saswp_schema_markup_generator( $template_type );
	            
	            if($woo_markp){
	                $input1 = array_merge( $input1, $woo_markp );
	            }
	                                            
	            unset($input1['brand'], $input1['mpn'], $input1['sku'],$input1['gtin8'], $input1['gtin13'], $input1['gtin12']);
	            
	            if ( ! empty( $publisher ) ) {                            
	                 $input1 = array_merge( $input1, $publisher );                            
	            }                                
	            if ( ! empty( $aggregateRating) ) {
	                $input1['aggregateRating'] = $aggregateRating;
	            }                                
	            if ( ! empty( $extra_theme_review) ) {
	               $input1 = array_merge($input1, $extra_theme_review);
	            }                               
	            if ( isset( $sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
	               $input1['comment'] = saswp_get_comments( get_the_ID() );
	            }    
	            
	            $input1 = saswp_append_fetched_reviews( $input1, $template_id );
	                                                                            
	            $input1 = apply_filters('saswp_modify_software_application_schema_output', $input1 );
	            
	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	                                    
	        break;

	        case 'TVSeries':
	                                                                                                                        
	            $input1['@context']              = saswp_context_url();
	            $input1['@type']                 = 'TVSeries';
	            $input1['@id']                   = saswp_get_permalink().'#TVSeries';                                                                                                                                
	            $input1['author']['@type']       = 'Person';                            

	            $input1 = saswp_append_fetched_reviews( $input1, $template_id );

	            $input1 = apply_filters('saswp_modify_tvseries_schema_output', $input1 );

	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	                                                                        
	        break;

	        case 'VisualArtwork':
	                                                                
	            $input1 = array(
	                '@context'			=> saswp_context_url(),
	                '@type'				=> $template_type ,
	                '@id'				=> saswp_get_permalink().'#VisualArtwork',     
	                'url'				=> saswp_get_current_url(),                                                                                    
	                'description'       => saswp_get_the_excerpt(),                                                                        
	                'name'				=> saswp_get_the_title(),
	                'dateCreated'       => esc_html( $date),                                    
	                'creator'			=> saswp_get_author_details()			
	            );
	                                            				                                                                                                                                
	            $input1 = apply_filters('saswp_modify_visualartwork_schema_output', $input1 );  
	            
	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }
	                        
	        break;

	        case 'VideoObject':
	                                
	            $enable_videoobject = get_post_meta( $template_id, 'saswp_enable_videoobject', true );
	            $video_links      = saswp_get_video_metadata();  
	            $description = saswp_get_the_excerpt();

	            if(!$description){
	                $description = get_bloginfo('description');
	            }  

	            $input1['@context'] = saswp_context_url();                               
	            if ( ! empty( $video_links) && count($video_links) > 1){
	              
	                $input1['@type'] = "ItemList";                                                       
	                $i = 1;
	                foreach( $video_links as $vkey => $v_val){  
	                    $vnewarr = array(
	                        '@type'				            => 'VideoObject',
	                        "position"                      => $vkey+1,
	                        "@id"                           => saswp_get_permalink().'#'.$i++,
	                        'name'				            => isset($v_val['title'])? $v_val['title'] : saswp_get_the_title(),
	                        'datePublished'                 => esc_html( $date),
	                        'dateModified'                  => esc_html( $modified_date),
	                        'url'				            => isset($v_val['video_url'])?saswp_validate_url($v_val['video_url']):saswp_get_permalink(),
	                        'interactionStatistic'          => array(
	                            "@type" => "InteractionCounter",
	                            "interactionType" => array("@type" => "WatchAction" ),
	                            "userInteractionCount" => isset($v_val['viewCount'])? $v_val['viewCount'] : '0', 
	                            ),    
	                        'thumbnailUrl'                  => isset($v_val['thumbnail_url'])? $v_val['thumbnail_url'] : saswp_get_thumbnail(),
	                        'author'			            => saswp_get_author_details(),
	                    );

	                    if ( isset( $v_val['video_url']) ) {                                                                        
	                        $vnewarr['contentUrl']  = saswp_validate_url($v_val['video_url']);                                    
	                    }
	        
	                    if ( isset( $v_val['video_url']) ) {                                                                        
	                        $vnewarr['embedUrl']   = saswp_validate_url($v_val['video_url']);                                 
	                    }

	                    if ( isset( $v_val['uploadDate']) ) {                                                                        
	                        $vnewarr['uploadDate']   = $v_val['uploadDate'];                                    
	                    }else{
	                        $vnewarr['uploadDate']   = $date;    
	                    }

	                    if ( isset( $v_val['duration']) ) {                                                                        
	                        $vnewarr['duration']   = $v_val['duration'];                                    
	                    }

	                    if ( isset( $v_val['description']) ) {                                                                        
	                        $vnewarr['description']   = $v_val['description'];                                    
	                    }else{
	                        $vnewarr['description']   = $description;
	                    }
	                    
	                    $input1['itemListElement'][] = $vnewarr;
	                }
	            }else{
	               
	                $input1 = array(
	                    '@context'			            => saswp_context_url(),
	                    '@type'				            => 'VideoObject',
	                    '@id'                           => saswp_get_permalink().'#videoobject',        
	                    'url'				            => saswp_get_permalink(),
	                    'headline'			            => saswp_get_the_title(),
	                    'datePublished'                 => esc_html( $date),
	                    'dateModified'                  => esc_html( $modified_date),
	                    'description'                   => $description,
	                    'transcript'                    => saswp_get_the_content(),
	                    'name'				            => saswp_get_the_title(),
	                    'uploadDate'                    => esc_html( $date),
	                    'thumbnailUrl'                  => isset($video_links[0]['thumbnail_url'])? $video_links[0]['thumbnail_url'] : saswp_get_thumbnail(),
	                    'author'			            => saswp_get_author_details()						                                                                                                      
	                );
	                
	                if ( isset( $video_links[0]['duration']) ) {                                                                        
	                    $input1['duration']   = $video_links[0]['duration'];                                    
	                }
	                if ( isset( $video_links[0]['video_url']) ) {
	                    
	                    $input1['contentUrl'] = saswp_validate_url($video_links[0]['video_url']);
	                    $input1['embedUrl']   = saswp_validate_url($video_links[0]['video_url']);
	                    
	                }
	                
	                if ( ! empty( $publisher) ) {

	                    $input1 = array_merge($input1, $publisher);   

	                }                                                
	                if ( isset( $sd_data['saswp_comments_schema']) && $sd_data['saswp_comments_schema'] ==1){
	                $input1['comment'] = saswp_get_comments(get_the_ID());
	                }                                                
	                if ( ! empty( $aggregateRating) ) {
	                    $input1['aggregateRating'] = $aggregateRating;
	                }                                               
	                if ( ! empty( $extra_theme_review) ) {
	                $input1 = array_merge($input1, $extra_theme_review);
	                }

	                $input1 = saswp_append_fetched_reviews($input1, $template_id);
	               
	            }

	            $input1 = apply_filters('saswp_modify_video_object_schema_output', $input1 );

	            $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

	            if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }                                

	            if ( isset( $enable_videoobject) && $enable_videoobject == 1){
	                
	                if ( isset( $template_options['enable_custom_field']) && $template_options['enable_custom_field'] == 1){
	                    if(empty($input1['contentUrl']) && empty($input1['embedUrl']) ) {
	                        $input1 = array();
	                    }                                            
	                }else{
	                    if(empty($video_links) && count($video_links) == 0 ){
	                        $input1 = array();
	                    }
	                }

	            }
	             

	        break;

	        case 'Organization':
                $organization_type = get_post_meta(
                    $template_id,
                    'saswp_schema_organization_type',
                    true
                );

                if (empty($organization_type)) {
                    $organization_type = 'Organization';
                }

                $input1 = saswp_kb_schema_output();

                if (!empty($input1['@type']) && $input1['@type'] == 'Person') {
                    $input1 = array(
                        '@context'			=> saswp_context_url(),
                        '@type'				=> $organization_type,
                        '@id'				=> saswp_get_current_url().'#Organization',
                        'url'				=> saswp_get_current_url(),
                        'description'       => saswp_get_the_excerpt(),
                        'name'				=> saswp_get_the_title()
                    );
                } else {
                    $input1['@type'] = $organization_type;
                }

                $input1 = saswp_append_fetched_reviews( $input1, $template_id );
                $input1 = apply_filters( 'saswp_modify_organization_schema_output', $input1 );
                $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );

                if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }

            break;

            case 'BlogPosting':
                                
                $input1 = $service_object->saswp_schema_markup_generator( $template_type );
        
                $mainentity = saswp_get_mainEntity( $template_id );

                if($mainentity){
                    $input1['mainEntity'] = $mainentity;                                     
                }
                                                                                    
                $input1 = apply_filters('saswp_modify_blogposting_schema_output', $input1 ); 
                $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );
                
                if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }                        
                
            break;

            case 'Event':
                                
                $event_type         =	get_post_meta( $template_id, 'saswp_event_type', true );  
                            
                $input1['@context'] = 	saswp_context_url();
                $input1['@type']    = 	$event_type ? $event_type : $template_type;
                $input1['@id']      = 	saswp_get_permalink().'#event';
                $input1['url']		=	saswp_get_permalink();
                                                                       
                if ( ! empty( $aggregateRating) ) {
                    $input1['aggregateRating'] = $aggregateRating;
                }                                
                if ( ! empty( $extra_theme_review) ) {
                   $input1 = array_merge( $input1, $extra_theme_review );
                }                                                                                                
                $input1 = saswp_append_fetched_reviews( $input1, $template_id );
                                                                            
                $input1 = apply_filters( 'saswp_modify_event_schema_output', $input1, $template_id );
                
                $input1 = saswp_get_modified_markup( $input1, $template_type, $template_id, $template_options );
                
                if ( isset( $input1['@context'] ) ) {
	            	unset( $input1['@context'] );
	            }
	            if ( isset( $input1['@id'] ) ) {
	            	unset( $input1['@id'] );
	            }                        
                
            break;

		}
	}

	return $input1;

}

/**
 * Prepare schema template meta list
 * @return 	$schema_template array
 * @since 	1.39
 * */
function saswp_get_schema_template_meta_list(){
	
	$schema_template = array();
	$schema_template['label'] 								=	'Schema Template';
	$schema_template['meta-list']['saswp_schema_template'] 	=	'Schema Template';
	return $schema_template;

}

/**
 * Add schema template to meta list
 * @param 	$meta_list_fields array
 * @return 	$meta_list_fields array
 * @since 	1.39
 * */
function saswp_add_schema_template_to_meta_list( $meta_list_fields ){
	
	if ( is_array( $meta_list_fields ) && ! empty( $meta_list_fields['text'] ) && is_array( $meta_list_fields['text'] ) ) {

		$schema_template 				=	saswp_get_schema_template_meta_list();
		$meta_list_fields['text'][] 	=	$schema_template;

	}

	return $meta_list_fields;

}

add_action( 'wp_ajax_saswp_validate_schema_template_attr', 'saswp_validate_schema_template_attr' );
function saswp_validate_schema_template_attr(){
	
	$html_escaped 	=	'';
	$flag 			=	0;

	if ( ! isset( $_POST['saswp_security_nonce'] ) ){
        return; 
    }
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: Nonce verification done here so unslash is not used.
    if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
       return;  
    } 
    if(!current_user_can( saswp_current_user_can()) ) {
        die( '-1' );    
    }

    if ( isset( $_POST['schema_type'] ) && isset( $_POST['field_name'] ) ) {
    	
    	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason post data is just used here so there is no necessary of unslash
    	$schema_type 	=	sanitize_text_field( $_POST['schema_type'] );
    	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash --Reason post data is just used here so there is no necessary of unslash
    	$field_name 	=	sanitize_text_field( $_POST['field_name'] );

		$meta_fields 	=	$meta_field = saswp_get_fields_by_schema_type( null, null, $schema_type, 'manual' );

		if ( ! empty( $meta_fields ) && is_array( $meta_fields ) ) {
			foreach ($meta_fields as $mf_key => $meta) {
				if ( ! empty( $meta ) && is_array( $meta ) && ! empty( $meta['id'] ) ) {
					$id 	=	trim( $meta['id'], '_' );
					if ( $id == $field_name && isset( $meta['is_template_attr'] ) ) {
						$flag 	=	1;
					}	
				}
			}
		}

	}

	if ( $flag == 1 ) {
		
		$schema_template 	=	saswp_get_schema_template_meta_list();
		$html_escaped 		.=	'<optgroup label="'.esc_attr( $schema_template['label'] ).'">';
		foreach ( $schema_template['meta-list'] as $key => $value ) {
			$html_escaped 	.=	'<option value="'.esc_attr( $key ).'">'.esc_html($value).'</option>';	
		}
		$html_escaped 		.=	'</optgroup>';
					
	}

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --Reason Escaping is done above.
	echo $html_escaped;
	wp_die();

}

/**
 * Check if schema custom field is enabled or not
 * @param 	$schema_id 	integer
 * @return 	bool
 * @since 	1.39
 * */
function saswp_is_schema_custom_field_enabled( $schema_id ) {
	$schema_options 	=	get_post_meta( $schema_id, 'schema_options', true );
	if ( is_array( $schema_options ) && isset( $schema_options['enable_custom_field'] ) && $schema_options['enable_custom_field'] == 1 ) {
		return true;	
	}

	return false;
}

/**
 * Prepare hasPart and isPartOf markup for schema template
 * @param 	$prefix		string
 * @param 	$schema_id	integer
 * @return 	$input1		array
 * @since 	1.39
 * */
function saswp_prepare_haspart_and_is_partof_markup( $prefix, $schema_id ) {

	$input1 				=	array();	
	$input1['hasPart'] 		=	array();	
	$input1['isPartOf'] 	=	array();	

	// Check if schema is modified globally and add schema template markup
    if ( saswp_is_schema_custom_field_enabled( $schema_id ) ){
        $template_field   = get_post_meta( $schema_id, 'saswp_schema_template_field', true );
        if ( ! empty( $template_field ) && is_array( $template_field ) ) {
            foreach ( $template_field as $tf_key => $template) {
                $template_markup   =   saswp_get_schema_template_markup( $schema_id, $tf_key );
                if ( ! empty( $template_markup )  ) {

                    switch ( $tf_key ) {

                        case $prefix.'haspart':

                            if ( is_array( $template_markup ) ) {
                            	$input1['hasPart']       =   $template_markup;
                            }

                        break;

                        case $prefix.'ispartof':

                            if ( is_array( $template_markup ) ) {
                            	$input1['isPartOf']       =   $template_markup;
                            }

                        break;

                    }

                }
            }
            
        }
    }

    return $input1;

}

/**
 * Check if schema builder function is enabled or not
 * @since 	1.39
 * */
function saswp_check_if_schema_builder_is_active() {

	$get_sd_data   = get_option( 'sd_data' );
    if ( is_array( $get_sd_data ) && ! empty( $get_sd_data['saswp-template-builder'] ) ) {
    	return true;
    }
    return false;

}