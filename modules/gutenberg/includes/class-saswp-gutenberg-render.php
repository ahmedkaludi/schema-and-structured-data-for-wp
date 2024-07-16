<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_Gutenberg_Render {
    
    public function qanda_block_data($attributes){
                 
        ?>
        <div class="saswp-qanda-block-html"> 
            <div class="saswp-qanda-block-question">
                <h3><?php echo esc_html( $attributes['question_name']); ?></h3>
                <span class="saswp-qand-date"><?php echo esc_html( $attributes['question_date_created']).' '.esc_html( $attributes['question_time_created']).' '.esc_html__( 'by', 'schema-and-structured-data-for-wp' ).' '.esc_html( $attributes['question_author']); ?></span>                
                <p><?php echo esc_html( $attributes['question_text']); ?></p>
                <?php echo esc_html__( 'Vote', 'schema-and-structured-data-for-wp' ).' <span class="dashicons dashicons-thumbs-up"></span> ('.esc_html( $attributes['question_up_vote']).')'; ?>
            </div>
              
            <div class="saswp-qanda-block-answer">
                <h3><?php echo esc_html__( 'Accepted Answers', 'schema-and-structured-data-for-wp' ); ?></h3>
            <?php
                if ( isset( $attributes['accepted_answers']) && !empty($attributes['accepted_answers']) ) {

                    foreach( $attributes['accepted_answers'] as $answer){
                    ?>    
                        <li>
                            <a href="<?php echo esc_url($answer['url']); ?>">
                                <p><?php echo esc_html( $answer['text']); ?></p>                        
                            </a>
                            <span class="saswp-qand-date"><?php echo esc_html( $answer['date_created']).' '.esc_html( $answer['time_created']).' by <strong>'.esc_html( $answer['author']); ?></strong></span>                        
                            <br> <?php echo esc_html__( 'Vote', 'schema-and-structured-data-for-wp' ).' <span class="dashicons dashicons-thumbs-up"></span> ('.esc_html( $answer['vote']).')'; ?>
                            
                        </li>
                    <?php   
                    }

                }
            ?>
            </div>   

            <div class="saswp-qanda-block-answer">
                <h3><?php echo esc_html__( 'Suggested Answers', 'schema-and-structured-data-for-wp' ); ?></h3> 
            <?php
                if ( isset( $attributes['suggested_answers']) && !empty($attributes['suggested_answers']) ) {

                    foreach( $attributes['suggested_answers'] as $answer){
                    ?>    
                        <li>
                            <a href="<?php echo esc_url($answer['url']); ?>">
                                <p><?php echo esc_html( $answer['text']); ?></p>                        
                            </a>
                            <span class="saswp-qand-date"><?php echo esc_html( $answer['date_created']).' '.esc_html( $answer['time_created']).' '. esc_html__( 'by', 'schema-and-structured-data-for-wp' ); ?> <strong><?php echo esc_html( $answer['author']); ?></strong></span>           
                            <br> <?php echo esc_html__( 'Vote', 'schema-and-structured-data-for-wp' ); ?> <span class="dashicons dashicons-thumbs-up"></span> (<?php echo esc_html( $answer['vote']); ?>)                        
                        </li>
                    <?php                       
                    }

                } ?>
            </div>
        </div>
                
    <?php
    }
    public function event_block_data($attributes){
    ?>   
        <div class="saswp-event-wrapper">
            <?php (isset($attributes['description']) ? '<p>'.$attributes['description'].'</p>' : '') ?>
            <div class="saswp-event-dates">
                <h5><?php echo esc_html__( 'Event Details', 'schema-and-structured-data-for-wp' ); ?></h5>
                <strong><?php echo esc_html__( 'Start Date', 'schema-and-structured-data-for-wp' ); ?> : </strong> <span><?php echo esc_html( $attributes['start_date']); ?></span>
                <?php 
                if ( ! isset( $attributes['all_day']) ) { 
                    ?>
                    <span> ,<?php echo esc_html( $attributes['start_time']); ?></span><br>
                <?php
                }else{
                ?> <br> <?php    
                } ?>
                <strong><?php echo esc_html__( 'End Date', 'schema-and-structured-data-for-wp' ); ?> : </strong> <span><?php echo esc_html( $attributes['end_date']); ?></span>
                <?php 
                if ( ! isset( $attributes['all_day']) ) { 
                    ?>
                    <span> ,<?php echo esc_html( $attributes['end_time']); ?></span><br>
                <?php
                }else{
                ?> <br> <?php    
                }

                if ( isset( $attributes['event_status']) && $attributes['event_status'] == 'EventRescheduled' && isset($attributes['previous_date']) ) {
                ?>    
                    <strong><?php echo esc_html__( 'Previous Date', 'schema-and-structured-data-for-wp' ); ?> : </strong> <span><?php echo esc_html( $attributes['previous_date']); ?></span>
                    <?php
                    if ( ! isset( $attributes['all_day']) ) { 
                        ?>
                        <span> <?php echo esc_html( $attributes['previous_time']); ?></span><br> : <br>
                    <?php }         

                }                
                if ( isset( $attributes['website']) ) {
                ?>
                    <strong><?php echo esc_html__( 'Website', 'schema-and-structured-data-for-wp' ); ?> : </strong> <span><a href="<?php echo esc_url($attributes['website']); ?>"><?php echo esc_url($attributes['website']); ?></a></span><br>
                <?php 
                }
                if ( isset( $attributes['price']) ) {
                ?>
                    <strong><?php echo esc_html__( 'Price', 'schema-and-structured-data-for-wp' ); ?> : </strong> 
                    <span><?php echo esc_html( $attributes['price']).' '. (isset($attributes['currency_code']) ? esc_html( $attributes['currency_code']) : 'USD' ); ?></span><br>
                <?php 
                }
                if ( isset( $attributes['attendance_mode']) ) {
                ?>
                    <strong><?php echo esc_html__( 'Attendance Mode', 'schema-and-structured-data-for-wp' ); ?> : </strong> <span><?php echo esc_html( $attributes['attendance_mode']); ?></span><br>
                <?php 
                }
                if ( isset( $attributes['event_status']) ) {
                ?>
                    <strong><?php echo esc_html__( 'Status', 'schema-and-structured-data-for-wp' ); ?> : </strong> <span><?php echo esc_html( $attributes['event_status']); ?></span>
                <?php 
                }
                if ( isset( $attributes['all_day']) ) {
                ?>
                    <div><?php echo esc_html__( 'This event is all day', 'schema-and-structured-data-for-wp' ); ?></div>
                <?php } ?>
            </div>
                
            <div class="saswp-event-venue-details">
            <?php 
            if($attributes['venue_name'] || $attributes['venue_address']){
            ?> <h5><?php echo esc_html__( 'Venue', 'schema-and-structured-data-for-wp' ); ?></h5> <?php
            }
            if($attributes['venue_name']){
            ?> <span><?php echo esc_html( $attributes['venue_name']); ?></span><br><br> <?php
            }
            if($attributes['venue_address']){
            ?> <span><?php echo esc_html( $attributes['venue_address']); ?></span>, <?php
            }
            if($attributes['venue_city']){
            ?> <span><?php echo esc_html( $attributes['venue_city']); ?></span>, <br> <?php
            }
            if($attributes['venue_state']){
            ?> <span><?php echo esc_html( $attributes['venue_state']); ?></span> <?php
            }
            if($attributes['venue_postal_code']){
            ?> <span><?php echo esc_html( $attributes['venue_postal_code']); ?></span>, <?php
            }
            if($attributes['venue_country']){
            ?> <span><?php echo esc_html( $attributes['venue_country']); ?></span><br> <?php
            }                
            if($attributes['venue_phone']){
            ?> <strong><?php echo esc_html__( 'Phone', 'schema-and-structured-data-for-wp' ); ?> : </strong><span><?php echo esc_html( $attributes['venue_phone']); ?></span>
            <?php } ?>                      
        </div> <!-- saswp-event-venue-details div end -->                                    
        <div class="saswp-event-organizers-details">
            <h5><?php echo esc_html__( 'Organizers', 'schema-and-structured-data-for-wp' ); ?></h5>                  
            <?php
            if ( isset( $attributes['organizers']) && !empty($attributes['organizers']) ) {

                foreach( $attributes['organizers'] as $org){
                ?>    
                    <div class="saswp-event-organiser"><span><?php echo esc_html( $org['name']); ?></span><br>
                        <strong><?php echo esc_html__( 'Phone', 'schema-and-structured-data-for-wp' ); ?> : </strong><span><?php echo esc_html( $org['phone']); ?></span><br>
                        <strong><?php echo esc_html__( 'Email', 'schema-and-structured-data-for-wp' ); ?> : </strong><span><?php echo esc_html( $org['email']); ?></span><br>
                        <strong><?php echo esc_html__( 'Website', 'schema-and-structured-data-for-wp' ); ?> : </strong> <span><?php echo esc_html( $org['website']); ?></span>
                    </div>
                <?php    
                }

            } ?>
        </div>              
        <div class="saswp-event-performers-details">
            <h5><?php echo esc_html__( 'Performers', 'schema-and-structured-data-for-wp' ); ?></h5>                    
            <?php
            if ( isset( $attributes['performers']) && !empty($attributes['performers']) ) {

                foreach( $attributes['performers'] as $org){
                ?>    
                    <div class="saswp-event-organiser"><span><?php echo esc_html( $org['name']); ?></span><br>
                        <strong><?php echo esc_html__( 'URL', 'schema-and-structured-data-for-wp' ); ?> : </strong><span><a href="<?php echo esc_url($org['url']); ?>"><?php echo esc_url($org['url']); ?></a></span><br>
                        <strong><?php echo esc_html__( 'Email', 'schema-and-structured-data-for-wp' ); ?> : </strong><span><?php echo esc_html( $org['email']); ?></span><br> 
                    </div>                  
               <?php         
                }

            } ?>
        </div>        
    </div> <!-- saswp-event-wrapper div end -->
    <?php            
    }
    
    public function job_block_data($attributes){
                        
        $response = $location = '';
       
        if($attributes){
 
            if($attributes['location_address']){
                $location .= $attributes['location_address']; 
            }
            if($attributes['location_city']){
                $location .= $attributes['location_city'];
            }
            if($attributes['location_state']){
                $location .= $attributes['location_state'];
            }
            if($attributes['location_country']){
                $location .= $attributes['location_country'];
            }
            if($attributes['location_postal_code']){
                $location .= $attributes['location_postal_code'];
            }
                                          
         ?>
        <div class="saswp-job-listing-wrapper">                   
            <ul class="saswp-job-listing-meta">
                <li class="saswp-location"><span class="dashicons dashicons-location"></span><a target="_blank" href="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( wp_strip_all_tags( $location ) ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false' ); ?>" class="saswp-google-map-link">
                <?php 
                if($attributes['location_address']){
                    echo esc_html( $attributes['location_address']); ?> ,<br> <?php 
                }
                if($attributes['location_city']){
                    echo esc_html( $attributes['location_city']); ?> , <?php 
                }
                if($attributes['location_state']){
                    echo esc_html( $attributes['location_state']); ?> ,<br> <?php 
                }
                if($attributes['location_country']){
                    echo esc_html( $attributes['location_country']); ?> , <?php 
                }
                if($attributes['location_postal_code']){
                    echo esc_html( $attributes['location_postal_code']); ?> <br> <?php 
                }
                ?>
                </a></li>
                <li class="saswp-date-posted"><span class="dashicons dashicons-calendar-alt"></span> <?php echo get_the_date("Y-m-d"); ?></li>
            </ul>
            <div class="saswp-job-company">
                <?php
                 if ( isset( $attributes['company_logo_url']) ) {
                    ?> <img src=<?php echo esc_url($attributes['company_logo_url']); ?>>; <?php
                 } ?>

                <p class="saswp-job-company-name">
                    <?php
                    if($attributes['company_website']){
                    ?> <a target="_blank" class="saswp-job-company-website" href="<?php echo esc_url($attributes['company_website']); ?>"><span class="dashicons dashicons-admin-links"></span> <?php echo esc_html__( 'Website', 'schema-and-structured-data-for-wp' ); ?></a>
                    <?php
                    }
                    if($attributes['company_twitter']){
                    ?> <a target="_blank" class="saswp-job-company-twitter" href="<?php echo esc_url($attributes['company_twitter']); ?>"><span class="dashicons dashicons-twitter"></span> <?php echo esc_html__( 'Twitter', 'schema-and-structured-data-for-wp' ); ?></a>
                    <?php
                    }
                    if($attributes['company_facebook']){
                    ?> <a target="_blank" class="saswp-job-company-facebook" href="<?php echo esc_url($attributes['company_facebook']); ?>"><span class="dashicons dashicons-facebook-alt"></span> <?php echo esc_html__( 'Facebook', 'schema-and-structured-data-for-wp' ); ?></a>
                    <?php } ?>         
                    <strong><?php echo esc_html( $attributes['company_name']); ?></strong>
                </p>
                <p class="saswp-job-company-tagline"><?php echo esc_html( $attributes['company_tagline']); ?></p>
                <?php    
                    if($attributes['base_salary']){
                    ?> <p><strong><?php echo esc_html__( 'Base Salary: ', 'schema-and-structured-data-for-wp' ); ?> </strong> <span><?php echo esc_html( $attributes['base_salary']).' '.esc_html( $attributes['currency_code']).' '. esc_html__( 'per', 'schema-and-structured-data-for-wp' ) .' '.esc_html( $attributes['unit_text']); ?> </span> <p>
                <?php } ?>
            </div> <!-- saswp-job-company div end -->
            <div class="saswp-job-description"> <?php echo esc_html( $attributes['job_description']); ?></div>
            <div class="saswp-job-application">
                <div class="saswp-job-application-details">
                    <?php
                    if($attributes['app_email_or_website']){
                        echo esc_html__( 'To apply for this job', 'schema-and-structured-data-for-wp' ); ?> 
                        <strong><?php echo esc_html( $attributes['app_email_or_website']); ?></strong>
                        <a href="mailto: <?php echo esc_attr( $attributes['app_email_or_website']); ?>"><?php echo esc_html( $attributes['app_email_or_website']); ?></a>
                    <?php } ?>                                    
                </div>
            </div>
        </div>   
        <?php    
        }
        
    }

    public function recipe_block_data($attributes){
        
        ?>
        <div class="saswp-recipe-block-container">

        <?php if ( isset( $attributes['banner_url']) ) {

            echo '<div class="saswp-recipe-field-banner">
            <div class="saswp-book-banner-div">
                <img src="'. esc_url( $attributes['banner_url']).'">
            </div>
            </div>';

        } ?>
            
            <div class="saswp-recipe-block-heading">
                 <h4></h4>   
                 <span class="saswp-recipe-block-author"><?php echo esc_html__( 'Recipe By', 'schema-and-structured-data-for-wp' ) ?> <?php echo (!empty($attributes['author']) ? esc_html( $attributes['author']) : '') ; ?></span>
                 <div class="saswp-r-course-section">
                  <span class="saswp-recipe-block-course">
                    <?php echo esc_html__( 'Course', 'schema-and-structured-data-for-wp' ) ?>: <strong><?php echo (!empty($attributes['course']) ? esc_html( $attributes['course']) : '') ; ?></strong>
                  </span>   
                  <span class="saswp-recipe-block-cuisine">
                  <?php echo esc_html__( 'Cusine', 'schema-and-structured-data-for-wp' ) ?>:<strong><?php echo (!empty($attributes['cuisine']) ? esc_html( $attributes['cuisine']) : '') ; ?></strong>

                  </span>   
                  <span class="saswp-recipe-block-difficulty">
                  <?php echo esc_html__( 'Difficulty', 'schema-and-structured-data-for-wp' ) ?>:<strong><?php echo (!empty($attributes['difficulty']) ? esc_html( $attributes['difficulty']) : '') ; ?></strong>
                  </span>   
                 </div>
            </div>
            <div class="saswp-recipe-block-details">
                <div class="saswp-recipe-block-details-items">

                    <div class="saswp-recipe-block-details-item">
                    <p class="saswp-r-b-label"><?php echo esc_html__( 'Servings', 'schema-and-structured-data-for-wp' ) ?></p>                    
                    <p class="saswp-r-b-unit"><?php echo (!empty($attributes['servings']) ? esc_html( $attributes['servings']) : '') ; ?> <?php echo esc_html__( 'minutes', 'schema-and-structured-data-for-wp' ) ?></p>
                    </div>

                    <div class="saswp-recipe-block-details-item">
                    <p class="saswp-r-b-label"><?php echo esc_html__( 'Preparing Time', 'schema-and-structured-data-for-wp' ) ?></p>                    
                    <p class="saswp-r-b-unit"><?php echo (!empty($attributes['pre_time']) ? esc_html( $attributes['pre_time']) : '') ; ?> <?php echo esc_html__( 'minutes', 'schema-and-structured-data-for-wp' ) ?></p>
                    </div>

                    <div class="saswp-recipe-block-details-item">
                    <p class="saswp-r-b-label"><?php echo esc_html__( 'Cooking Time', 'schema-and-structured-data-for-wp' ) ?></p>                    
                    <p class="saswp-r-b-unit"><?php echo (!empty($attributes['cook_time']) ? esc_html( $attributes['cook_time']) : '') ; ?> <?php echo esc_html__( 'minutes', 'schema-and-structured-data-for-wp' ) ?></p>
                    </div>

                    <div class="saswp-recipe-block-details-item">
                    <p class="saswp-r-b-label"><?php echo esc_html__( 'Calories', 'schema-and-structured-data-for-wp' ) ?></p>                    
                    <p class="saswp-r-b-unit"><?php echo (!empty($attributes['calories']) ? esc_html( $attributes['calories']) : '') ; ?> <?php echo esc_html__( 'kcal', 'schema-and-structured-data-for-wp' ) ?></p>
                    </div>


                </div>
            </div>
            <div class="saswp-recipe-block-ingredients">
                <h4><?php echo esc_html__( 'INGREDIENTS', 'schema-and-structured-data-for-wp' ) ?></h4>

                <?php if ( isset( $attributes['ingredients']) ) {
                    echo '<ol class="saswp-dirction-ul">';
                    foreach ( $attributes['ingredients'] as $value) {
                        echo '<li class="saswp-r-b-direction-item"><p>'.esc_html( $value['name']).'</p></li>';
                    }
                    echo '</ol>';

                } ?>
               
            </div>
            <div class="saswp-recipe-block-direction">
            <h4><?php echo esc_html__( 'DIRECTION', 'schema-and-structured-data-for-wp' ) ?></h4>
               
            <?php if ( isset( $attributes['ingredients']) ) {
                    echo '<ol class="saswp-dirction-ul">';
                    foreach ( $attributes['directions'] as $value) {
                        echo '<li class="saswp-r-b-direction-item">';
                        echo '<strong>'.esc_html( $value['name']).'</strong>';
                        if ( isset( $value['text']) ) {
                            echo '<p>'.wp_kses($value['text'], wp_kses_allowed_html('post')).'</p>';
                        }                        
                        echo '</li>';
                    }
                    echo '</ol>';

                } ?>

            </div>
            <div class="saswp-recipe-block-notes">
                <h4><?php echo esc_html__( 'NOTES', 'schema-and-structured-data-for-wp' ) ?></h4>
                
                <?php if ( isset( $attributes['notes']) ) {
                    echo '<ol class="saswp-dirction-ul">';
                    foreach ( $attributes['notes'] as $value) {
                        echo '<p>'.wp_kses($value['text'], wp_kses_allowed_html('post')).'</p>';
                    }
                    echo '</ol>';

                } ?>

            </div>
        </div>
        <?php

    }

    public function book_block_data($attributes){
        
        ?>
        <div class="saswp-book-block-container">
            <div class="saswp-book-field-banner">
                <div class="saswp-book-banner-div">
                    <img src="<?php echo esc_url($attributes['banner_url']) ?>" />
                </div>
            </div>
            <div class="saswp-book-field-container">

                <div class="saswp-book-block-field">
                 <span class="saswp-book-field-label"><?php echo esc_html__( 'Title', 'schema-and-structured-data-for-wp' ); ?> : </span>
                 <div class="saswp-book-field"><?php echo (!empty($attributes['title']) ? esc_html( $attributes['title']) : '' ) ?></div>   
                </div>

                <div class="saswp-book-block-field">
                 <span class="saswp-book-field-label"><?php echo esc_html__( 'Series', 'schema-and-structured-data-for-wp' ); ?> : </span>
                 <div class="saswp-book-field"><?php echo (!empty($attributes['series']) ? esc_html( $attributes['series']) : '' ) ?></div>   
                </div>

                <div class="saswp-book-block-field">
                 <span class="saswp-book-field-label"><?php echo esc_html__( 'Author', 'schema-and-structured-data-for-wp' ); ?> : </span>
                 <div class="saswp-book-field"><?php echo (!empty($attributes['author']) ? esc_html( $attributes['author']) : '' ) ?></div>   
                </div>

                <div class="saswp-book-block-field">
                 <span class="saswp-book-field-label"><?php echo esc_html__( 'Genre', 'schema-and-structured-data-for-wp' ); ?> : </span>
                 <div class="saswp-book-field"><?php echo (!empty($attributes['genre']) ? esc_html( $attributes['genre']) : '' ) ?></div>   
                </div>

                <div class="saswp-book-block-field">
                 <span class="saswp-book-field-label"><?php echo esc_html__( 'Publisher', 'schema-and-structured-data-for-wp' ); ?> : </span>
                 <div class="saswp-book-field"><?php echo (!empty($attributes['publisher']) ? esc_html( $attributes['publisher']) : '' ) ?></div>   
                </div>

                <div class="saswp-book-block-field">
                 <span class="saswp-book-field-label"><?php echo esc_html__( 'Release Date', 'schema-and-structured-data-for-wp' ); ?> : </span>
                 <div class="saswp-book-field"><?php echo (!empty($attributes['release_date']) ? esc_html( $attributes['release_date']) : '' ) ?></div>   
                </div>

                <div class="saswp-book-block-field">
                 <span class="saswp-book-field-label"><?php echo esc_html__( 'Format', 'schema-and-structured-data-for-wp' ); ?> : </span>
                 <div class="saswp-book-field"><?php echo (!empty($attributes['format']) ? esc_html( $attributes['format']) : '' ) ?></div>   
                </div>

                <div class="saswp-book-block-field">
                 <span class="saswp-book-field-label"><?php echo esc_html__( 'Pages', 'schema-and-structured-data-for-wp' ); ?> : </span>
                 <div class="saswp-book-field"><?php echo (!empty($attributes['pages']) ? esc_html( $attributes['pages']) : '' ) ?></div>   
                </div>

                <div class="saswp-book-block-field">
                 <span class="saswp-book-field-label"><?php echo esc_html__( 'Source', 'schema-and-structured-data-for-wp' ); ?> : </span>
                 <div class="saswp-book-field"><?php echo (!empty($attributes['source']) ? esc_html( $attributes['source']) : '' ) ?></div>   
                </div>

                <div class="saswp-book-block-field">
                 <span class="saswp-book-field-label"><?php echo esc_html__( 'Rating', 'schema-and-structured-data-for-wp' ); ?> : </span>
                 <div class="saswp-book-field">
                <?php
                        if ( isset( $attributes['rating']) ) {
                            for($i = 1; $i <= 5; $i++){

                                if($i <= $attributes['rating']){
                                    echo '<span class="saswp-book-block-stars dashicons dashicons-star-filled"></span>';   
                                }else{
                                    echo '<span class="saswp-book-block-stars dashicons dashicons-star-empty"></span>';   
                                }
                                 
                            }
                        }
                            
                ?>
                 </div>   
                </div>

                <div class="saswp-book-block-field">
                 <p><?php echo (!empty($attributes['description']) ? wp_kses($attributes['description'], wp_kses_allowed_html('post')) : '' ) ?></p>
                </div>
                
            </div>
        </div>
        <?php    
                              
    }
    public function course_block_data($attributes){
                        
        if ( isset( $attributes['courses']) ) {
                        
          foreach( $attributes['courses'] as $course){
            ?>            
            <div class="saswp-course-loop">
                      <h3 class="saswp-course-detail"><?php echo esc_html__( 'Course Details', 'schema-and-structured-data-for-wp' ) ?></h3>
                      <h5><?php echo esc_html( $course['name']) ?></h5>
                      <p>
                        <?php if($course['image_url']){
                             ?>
                            <img src="<?php echo esc_url($course['image_url']); ?>">
                        <?php } ?>                                                                
                      <?php echo esc_html( $course['description']); ?>
                      </p>
                      <h5><?php echo esc_html__( 'Provider Details', 'schema-and-structured-data-for-wp' ); ?></h5>
                      <div><strong><?php echo esc_html__( 'Provider Name', 'schema-and-structured-data-for-wp' ); ?></strong> : <?php echo esc_html( $course['provider_name']) ?></div>
                      <div><strong><?php echo esc_html__( 'Provider Website', 'schema-and-structured-data-for-wp' ); ?></strong> : <a href="<?php echo esc_url($course['provider_website']); ?>">
                        <?php echo esc_url($course['provider_website']) ?></a></div>                   
                      </div>
            <?php

            }  
                        
        }
                
    }
    
    public function collection_block_data($attributes){
                
        $collection_id = null; 
        
        if ( isset( $attributes['id']) ) {            
            $collection_id = $attributes['id'];                        
        }else{
             $review_service = new SASWP_Reviews_Service();
             $col_opt  = $review_service->saswp_get_collection_list(1);
             if ( isset( $col_opt[0]['value']) ) {
                 $collection_id = $col_opt[0]['value'];
             }
        }         
        $coll_block_escaped = do_shortcode('[saswp-reviews-collection id="'.$collection_id.'"]');
        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- all html coming from this shortcode has fully been escaped.
        echo $coll_block_escaped;
        
    }

    public function location_block_data($attributes){
                
        $location_id = null; 
        
        if ( isset( $attributes['id']) ) {            
            $location_id = $attributes['id'];                        
        }else{             
             $col_opt  = saswp_get_location_list();
             if ( isset( $col_opt[0]['value']) ) {
                 $location_id = $col_opt[0]['value'];
             }
        }
        $loc_block_escaped = do_shortcode('[saswp-location id="'.$location_id.'"]');
        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- all html coming from this shortcode has fully been escaped.
        echo $loc_block_escaped;
        
    }
    
}