jQuery(document).ready(function($){  

 $(".saswp-rating-front-div").rateYo({              
              rating : 1,  
              fullStar: true,                           
              onSet: function (rating, rateYoInstance) {
                $(this).next().val(rating);               
                }                              
            });
});