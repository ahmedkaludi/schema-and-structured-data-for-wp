jQuery(document).ready(function($){  

    jQuery(".saswp-rating-front-div").rateYo({              
              rating : 5,  
              spacing: "5px",                                       
              onSet: function (rating, rateYoInstance) {
                $(this).next().next().val(rating);               
                }                              
            }).on("rateyo.change", function(e, data){
                var rating = data.rating;              
                $(this).next().text(rating);
            });
    jQuery(".saswp-rv-form-btn a").on("click", function(e){
        e.preventDefault();        
        $(".saswp-review-submission-form").slideToggle("fast");        
    });         
});