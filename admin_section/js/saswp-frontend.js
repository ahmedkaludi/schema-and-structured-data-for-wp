jQuery(document).ready(function($){  

    jQuery("#saswp-comment-rating-div").rateYo({              
              rating : saswp_localize_front_data.rateyo_default_rating,                 
              spacing: "5px",                          
              onSet: function (rating, rateYoInstance) {
                $(this).next().next().val(rating);                
                }                              
            }).on("rateyo.change", function(e, data){
                var rating = data.rating;              
                $(this).next().text(rating);
            });             
            
});