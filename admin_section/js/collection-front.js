function saswpCollectionSlider(){
	
                jQuery(".saswp-collection-slider").each( function(){
		
		var $slider = jQuery(this),
				$itemscontainer = $slider.find(".saswp-slider-items-container");
		
		if ($itemscontainer.find(".saswp-slider-item.saswp-active").length == 0){
			$itemscontainer.find(".saswp-slider-item").first().addClass("saswp-active");
		}
		
		function setWidth(){
			var totalWidth = 0;
			
			jQuery($itemscontainer).find(".saswp-slider-item").each( function(){
				totalWidth += jQuery(this).outerWidth();
			});
			
			$itemscontainer.width(totalWidth);
			
		}
		function setTransform(){
			
                        if(jQuery(".saswp-slider-item.saswp-active").length > 0){
                        
                            var $activeItem = $itemscontainer.find(".saswp-slider-item.saswp-active"),
                                            activeItemOffset = $activeItem.offset().left,
                                            itemsContainerOffset = $itemscontainer.offset().left,
                                            totalOffset = activeItemOffset - itemsContainerOffset;

                            $itemscontainer.css({"transform": "translate( -"+totalOffset+"px, 0px)"})
                            
                        }
                        						
		}
		function nextSlide(){
			var activeItem = $itemscontainer.find(".saswp-slider-item.saswp-active"),
					activeItemIndex = activeItem.index(),
					sliderItemTotal = $itemscontainer.find(".saswp-slider-item").length,
					nextSlide = 0;
			
			if (activeItemIndex + 1 > sliderItemTotal - 1){
				nextSlide = 0;
			}else{
				nextSlide = activeItemIndex + 1
			}
			
			var nextSlideSelect = $itemscontainer.find(".saswp-slider-item").eq(nextSlide),
					itemContainerOffset = $itemscontainer.offset().left,
					totalOffset = nextSlideSelect.offset().left - itemContainerOffset
			
			$itemscontainer.find(".saswp-slider-item.saswp-active").removeClass("saswp-active");
			nextSlideSelect.addClass("saswp-active");
			$slider.find(".saswp-slider-dots").find(".saswp-dot").removeClass("saswp-active")
			$slider.find(".saswp-slider-dots").find(".saswp-dot").eq(nextSlide).addClass("saswp-active");
			$itemscontainer.css({"transform": "translate( -"+totalOffset+"px, 0px)"})
			
		}
		function prevSlide(){
			var activeItem = $itemscontainer.find(".saswp-slider-item.saswp-active"),
					activeItemIndex = activeItem.index(),
					sliderItemTotal = $itemscontainer.find(".saswp-slider-item").length,
					nextSlide = 0;
			
			if (activeItemIndex - 1 < 0){
				nextSlide = sliderItemTotal - 1;
			}else{
				nextSlide = activeItemIndex - 1;
			}
			
			var nextSlideSelect = $itemscontainer.find(".saswp-slider-item").eq(nextSlide),
					itemContainerOffset = $itemscontainer.offset().left,
					totalOffset = nextSlideSelect.offset().left - itemContainerOffset
			
			$itemscontainer.find(".saswp-slider-item.saswp-active").removeClass("saswp-active");
			nextSlideSelect.addClass("saswp-active");
			$slider.find(".saswp-slider-dots").find(".saswp-dot").removeClass("saswp-active")
			$slider.find(".saswp-slider-dots").find(".saswp-dot").eq(nextSlide).addClass("saswp-active");
			$itemscontainer.css({"transform": "translate( -"+totalOffset+"px, 0px)"})
			
		}
		function makeDots(){
			var activeItem = $itemscontainer.find(".saswp-slider-item.saswp-active"),
					activeItemIndex = activeItem.index(),
					sliderItemTotal = $itemscontainer.find(".saswp-slider-item").length;
			
			for (i = 0; i < sliderItemTotal; i++){
				$slider.find(".saswp-slider-dots").append("<div class='saswp-dot'></div>")
			}
			
			$slider.find(".saswp-slider-dots").find(".saswp-dot").eq(activeItemIndex).addClass("saswp-active")
			
		}
		
		setWidth();
		setTransform();
		makeDots();
		
                jQuery(window).load( function(){
					setWidth();
					setTransform();
		});
                
		jQuery(window).resize( function(){
					setWidth();
					setTransform();
		});
		
		var nextBtn = $slider.find(".saswp-slider-controls").find(".saswp-slider-next-btn"),
				prevBtn = $slider.find(".saswp-slider-controls").find(".saswp-slider-prev-btn");
		
		nextBtn.on('click', function(e){
			e.preventDefault();
			nextSlide();
		});
		
		prevBtn.on('click', function(e){
			e.preventDefault();
			prevSlide();
		});
		
		$slider.find(".saswp-slider-dots").find(".saswp-dot").on('click', function(e){
			
			var dotIndex = jQuery(this).index(),
			totalOffset = $itemscontainer.find(".saswp-slider-item").eq(dotIndex).offset().left - $itemscontainer.offset().left;
					
			$itemscontainer.find(".saswp-slider-item.saswp-active").removeClass("saswp-active");
			$itemscontainer.find(".saswp-slider-item").eq(dotIndex).addClass("saswp-active");
			$slider.find(".saswp-slider-dots").find(".saswp-dot").removeClass("saswp-active");
			jQuery(this).addClass("saswp-active");
			
			$itemscontainer.css({"transform": "translate( -"+totalOffset+"px, 0px)"})
			
		});
		
	});
	
       }    
           
           

jQuery(document).ready(function($){
       
    saswpCollectionSlider();
        
    var fomo_inverval       = jQuery("#saswp_fomo_interval").val();    
    
    var elems = $(".saswp-fomo-wrap");
    var i = 1;
    //saswp_fomo_fade(elems[0])

    function saswp_fomo_fade(elem) {
        if (i > elems.length - 1) {
        i = 0;
     }
     console.log(fomo_inverval);
     $(elem).fadeIn(300).delay(fomo_inverval*1000).fadeOut(300, function() {
         saswp_fomo_fade(elems[i++])
      });
     }
    
    $(document).on("click", ".saswp-opn-cls-btn", function(){
                
                $("#saswp-reviews-cntn").toggle();
                
                if( $('#saswp-reviews-cntn').is(':visible') ) {
                    $(".saswp-onclick-show").css('display','flex');
                    $(".saswp-onclick-hide").hide();
                    $(".saswp-open-class").css('width', '500px');
                }
                else {
                    $(".saswp-onclick-show").hide();
                    $(".saswp-onclick-hide").css('display','flex');
                    $(".saswp-open-class").css('width', '300px');
                }
                                                                                
    });    
    
});  