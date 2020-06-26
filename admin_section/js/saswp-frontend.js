jQuery(document).ready(function($){  

    jQuery("#saswp-comment-rating-div").rateYo({              
              rating : 5,                 
              spacing: "5px",                          
              onSet: function (rating, rateYoInstance) {
                $(this).next().next().val(rating);                
                }                              
            }).on("rateyo.change", function(e, data){
                var rating = data.rating;              
                $(this).next().text(rating);
            });             
            
});