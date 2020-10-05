import React from 'react';
import StarRatingHtml from './../../common/star-rating-html/StarRatingHtml';
import $ from 'jquery';

function saswp_fomo_slide(interval, visibility){
                
  var elem = $('.saswp-collection-preview .saswp-r5');
  var l = elem.length;
  var i = 0;
              
  function saswp_fomo_loop() {
      
      elem.eq(i % l).fadeIn(interval, function() {
          elem.eq(i % l).fadeOut(visibility, saswp_fomo_loop);
          i++;
      });
  }

  saswp_fomo_loop();
  
  } 

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

const Fomo = (props) => {
  saswp_fomo_slide(props.interval, props.visibility)
  return (
    <>
    {props.collectionArray ? 
        props.collectionArray.map((value, index) =>(
            <div key={index} id={index} className="saswp-r5">
            <div className="saswp-r5-r">                            
            <div className="saswp-r5-lg">
            <span>
              <img style={{'height': '70px','width':'70px'}} src={value.saswp_review_platform_icon}/>
            </span>
            </div>                            
            <div className="saswp-r5-rng">
              <StarRatingHtml ratingVal={value.saswp_review_rating} />                
              <div className="saswp-r5-txrng">
              <span>{value.saswp_review_rating} Stars</span>
              <span>by</span>
              <span>{value.saswp_reviewer_name}</span>
              </div>
            <span className="saswp-r5-dt">{convertDateToString(value.saswp_review_date).date}</span>
            </div>                            
            </div>
            </div>                   
        )) : ''
    }    
    </>
  )
}
export default Fomo;