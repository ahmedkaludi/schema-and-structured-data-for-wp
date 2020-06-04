jQuery(document).ready(function($){  

    jQuery(".saswp-rating-front-div").rateYo({              
              rating : 5,  
              fullStar: true,                           
              onSet: function (rating, rateYoInstance) {
                $(this).next().val(rating);               
                }                              
            });
    jQuery(".saswp-rv-form-btn a").on("click", function(e){
        e.preventDefault();        
        $(".saswp-review-submission-form").slideToggle("fast");        
    });         
});