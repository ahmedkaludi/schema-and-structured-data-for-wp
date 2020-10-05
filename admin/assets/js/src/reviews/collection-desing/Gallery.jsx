import React from 'react';
import StarRatingHtml from './../../common/star-rating-html/StarRatingHtml';
import $ from 'jquery';




function saswpCollectionSlider(){
	
  jQuery(".saswp-cs").each( function(){

var $slider = jQuery(this),
$itemscontainer = $slider.find(".saswp-sic");

if ($itemscontainer.find(".saswp-si.saswp-active").length == 0){
$itemscontainer.find(".saswp-si").first().addClass("saswp-active");
}

function setWidth(){
var totalWidth = 0

jQuery($itemscontainer).find(".saswp-si").each( function(){
totalWidth += jQuery(this).outerWidth();
});

$itemscontainer.width(totalWidth);

}
function setTransform(){

          if(jQuery(".saswp-si.saswp-active").length > 0){
          
              var $activeItem = $itemscontainer.find(".saswp-si.saswp-active"),
                              activeItemOffset = $activeItem.offset().left,
                              itemsContainerOffset = $itemscontainer.offset().left,
                              totalOffset = activeItemOffset - itemsContainerOffset;

              $itemscontainer.css({"transform": "translate( -"+totalOffset+"px, 0px)"})
              
          }
                      
}
function nextSlide(){
var activeItem = $itemscontainer.find(".saswp-si.saswp-active"),
activeItemIndex = activeItem.index(),
sliderItemTotal = $itemscontainer.find(".saswp-si").length,
nextSlide = 0;

if (activeItemIndex + 1 > sliderItemTotal - 1){
nextSlide = 0;
}else{
nextSlide = activeItemIndex + 1
}

var nextSlideSelect = $itemscontainer.find(".saswp-si").eq(nextSlide),
itemContainerOffset = $itemscontainer.offset().left,
totalOffset = nextSlideSelect.offset().left - itemContainerOffset

$itemscontainer.find(".saswp-si.saswp-active").removeClass("saswp-active");
nextSlideSelect.addClass("saswp-active");
$slider.find(".saswp-slider-dots").find(".saswp-dot").removeClass("saswp-active")
$slider.find(".saswp-slider-dots").find(".saswp-dot").eq(nextSlide).addClass("saswp-active");
$itemscontainer.css({"transform": "translate( -"+totalOffset+"px, 0px)"})

}
function prevSlide(){
var activeItem = $itemscontainer.find(".saswp-si.saswp-active"),
activeItemIndex = activeItem.index(),
sliderItemTotal = $itemscontainer.find(".saswp-si").length,
nextSlide = 0;

if (activeItemIndex - 1 < 0){
nextSlide = sliderItemTotal - 1;
}else{
nextSlide = activeItemIndex - 1;
}

var nextSlideSelect = $itemscontainer.find(".saswp-si").eq(nextSlide),
itemContainerOffset = $itemscontainer.offset().left,
totalOffset = nextSlideSelect.offset().left - itemContainerOffset

$itemscontainer.find(".saswp-si.saswp-active").removeClass("saswp-active");
nextSlideSelect.addClass("saswp-active");
$slider.find(".saswp-slider-dots").find(".saswp-dot").removeClass("saswp-active")
$slider.find(".saswp-slider-dots").find(".saswp-dot").eq(nextSlide).addClass("saswp-active");
$itemscontainer.css({"transform": "translate( -"+totalOffset+"px, 0px)"})

}
function makeDots(){
var activeItem = $itemscontainer.find(".saswp-si.saswp-active"),
activeItemIndex = activeItem.index(),
sliderItemTotal = $itemscontainer.find(".saswp-si").length;

for (let i = 0; i < sliderItemTotal; i++){
$slider.find(".saswp-slider-dots").append("<div class='saswp-dot'></div>")
}

$slider.find(".saswp-slider-dots").find(".saswp-dot").eq(activeItemIndex).addClass("saswp-active")

}

setWidth();
setTransform();
makeDots();
  
  jQuery(document).ready( function(){
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
totalOffset = $itemscontainer.find(".saswp-si").eq(dotIndex).offset().left - $itemscontainer.offset().left;

$itemscontainer.find(".saswp-si.saswp-active").removeClass("saswp-active");
$itemscontainer.find(".saswp-si").eq(dotIndex).addClass("saswp-active");
$slider.find(".saswp-slider-dots").find(".saswp-dot").removeClass("saswp-active");
jQuery(this).addClass("saswp-active");

$itemscontainer.css({"transform": "translate( -"+totalOffset+"px, 0px)"})

});

});

 }     



const Gallery = (props) => {

  const convertDateToString = (date_str) => {
           
    let date_time = {};
    
    if(date_str){
        
      let date_string = new Date(date_str); 
      
        date_time = {
            time : date_string.toLocaleTimeString(),
            date : date_string.toLocaleDateString()
        };
    }else{
       date_time = {
            time : '',
            date : ''
        };
    }
    
    return date_time;
    
}


  const reviewsDesignForGallery = (value, index) => {

    let element = [];


      element.push(<div key={index} className="saswp-r2-sli">
      <div className="saswp-r2-b">                                
      <div className="saswp-r2-q">
      {/* <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="95.333px" height="95.332px" viewBox="0 0 95.333 95.332" style="enable-background:new 0 0 95.333 95.332;" xml:space="preserve"><path d="M30.512,43.939c-2.348-0.676-4.696-1.019-6.98-1.019c-3.527,0-6.47,0.806-8.752,1.793    c2.2-8.054,7.485-21.951,18.013-23.516c0.975-0.145,1.774-0.85,2.04-1.799l2.301-8.23c0.194-0.696,0.079-1.441-0.318-2.045    s-1.035-1.007-1.75-1.105c-0.777-0.106-1.569-0.16-2.354-0.16c-12.637,0-25.152,13.19-30.433,32.076    c-3.1,11.08-4.009,27.738,3.627,38.223c4.273,5.867,10.507,9,18.529,9.313c0.033,0.001,0.065,0.002,0.098,0.002    c9.898,0,18.675-6.666,21.345-16.209c1.595-5.705,0.874-11.688-2.032-16.851C40.971,49.307,36.236,45.586,30.512,43.939z"></path><path d="M92.471,54.413c-2.875-5.106-7.61-8.827-13.334-10.474c-2.348-0.676-4.696-1.019-6.979-1.019    c-3.527,0-6.471,0.806-8.753,1.793c2.2-8.054,7.485-21.951,18.014-23.516c0.975-0.145,1.773-0.85,2.04-1.799l2.301-8.23    c0.194-0.696,0.079-1.441-0.318-2.045c-0.396-0.604-1.034-1.007-1.75-1.105c-0.776-0.106-1.568-0.16-2.354-0.16    c-12.637,0-25.152,13.19-30.434,32.076c-3.099,11.08-4.008,27.738,3.629,38.225c4.272,5.866,10.507,9,18.528,9.312    c0.033,0.001,0.065,0.002,0.099,0.002c9.897,0,18.675-6.666,21.345-16.209C96.098,65.559,95.376,59.575,92.471,54.413z"></path></svg> */}
      </div>
      <div className="saswp-rc-cnt">
      <p>
      {value.saswp_review_text}
      </p>
      </div>
      <div className="saswp-r2-strs">
      <span className="saswp-r2-s">
      <StarRatingHtml ratingVal={value.saswp_review_rating} /> 
      </span>
      </div>
      </div>
      <div className="saswp-rc">
      <div className="saswp-rc-a">
      <img src={value.saswp_reviewer_image}/>
      <div className="saswp-rc-nm">
      <a href="#">{value.saswp_reviewer_name}</a>
      <span className="saswp-rc-dt">{convertDateToString(value.saswp_review_date).date}</span>
      </div>
      <div className="saswp-rc-lg">
      <img src={value.saswp_review_platform_icon}/>
      </div>
      </div>
      </div>
      </div>);

    return element;

  }

  const  saswpChunkArray = (myArray, chunk_size) => {
                
    var contentArray = JSON.parse(JSON.stringify(myArray));
    var results = [];
    while (contentArray.length) {
        results.push(contentArray.splice(0, chunk_size));
    }

    return results;
  }

  saswpCollectionSlider();

  var chunkarr = saswpChunkArray(props.collectionArray, 3);

  return (
    <>
    <div className={props.postMeta.saswp_collection_gallery_type == 'slider' ? 'saswp-cst' : 'saswp-cct'}>

      <div className="saswp-cs">

      <div className="saswp-sic">

      {props.postMeta.saswp_collection_gallery_type === 'slider' ?
        props.collectionArray.map( (value, index) => (
          <div key={index} className="saswp-si">
            {reviewsDesignForGallery(value, index)}
          </div>
        ))
      :''
      }

      {props.postMeta.saswp_collection_gallery_type === 'carousel' ?
        
         chunkarr.map( (value, index) => (
          <div key={index} className="saswp-si">
            {value.map((data, i) => (
              reviewsDesignForGallery(data, i)
            ))}            
          </div>
        ))       

      :''
      }

      </div>

      {props.postMeta.saswp_gallery_arrow ? 
          <div className="saswp-slider-controls">
            <a href="#" className="saswp-slider-prev-btn"></a>
            <a href="#" className="saswp-slider-next-btn"></a>
          </div>
      : ''}

      {props.postMeta.saswp_gallery_dots ? 
        <div className="saswp-slider-dots">
        </div>
      : ''}

      </div>
    </div>
    </>
  )
}
export default Gallery;