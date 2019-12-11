jQuery(document).ready(function($){  

 $(".saswp-rating-front-div").rateYo({              
              rating : 1,  
              halfStar: true,                           
              onSet: function (rating, rateYoInstance) {
                $(this).next().val(rating);               
                }                              
            });
});