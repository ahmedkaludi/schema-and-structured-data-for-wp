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

	$image_id 	        		=	get_post_thumbnail_id();
	$date 		        		=	get_the_date("c");
    $modified_date 	    		=	get_the_modified_date("c");        
    $modify_option      		=	get_option('modify_schema_post_enable_'.get_the_ID()); 
	$template_meta 				=	saswp_get_schema_template_meta( $template_id );
	$template_type 				=	$template_meta['template_type'];
	$template_options 			=	$template_meta['template_options'];

	$service_object     		=	new SASWP_Output_Service();
	$aggregateRating    		=	$service_object->saswp_rating_box_rating_markup( saswp_get_the_ID() );
	$extra_theme_review 		=	$service_object->saswp_extra_theme_review_details( saswp_get_the_ID() );
	$publisher          		=	$service_object->saswp_get_publisher();

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
    if ( !wp_verify_nonce( $_POST['saswp_security_nonce'], 'saswp_ajax_check_nonce' ) ){
       return;  
    } 
    if(!current_user_can( saswp_current_user_can()) ) {
        die( '-1' );    
    }

    if ( isset( $_POST['schema_type'] ) && isset( $_POST['field_name'] ) ) {
    	
    	$schema_type 	=	sanitize_text_field( $_POST['schema_type'] );
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

	echo $html_escaped;
	wp_die();

}