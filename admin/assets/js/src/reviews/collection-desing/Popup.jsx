import React from 'react';
import StarRatingHtml from './../../common/star-rating-html/StarRatingHtml';
import $ from 'jquery';

$(document).on("click", ".saswp-opn-cls-btn", function(){
                
  $("#saswp-reviews-cntn").toggle();
  
  if( $('#saswp-reviews-cntn').is(':visible') ) {
      $(".saswp-onclick-show").css('display','flex');
      $(".saswp-onclick-hide").hide();
       $(".saswp-open-class").css('width', '500px');
  }
  else {
      $(".saswp-onclick-show").css('display','none');
      $(".saswp-onclick-hide").show();
      $(".saswp-open-class").css('width', '300px');
  }
                                                                  
});


const Popup = (props) => {

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

  const averageRating = () =>{
                          
    let sum_of_rating  = 0;
    let average_rating = 1;
    
    props.collectionArray.map( (value, index) => {

      sum_of_rating += parseFloat(value.saswp_review_rating);

    })    
    
    if(sum_of_rating > 0){
                        
      average_rating = sum_of_rating / props.collectionArray.length;
      
    }

    return average_rating;

  }


  return (    
    <>    
      <div id="saswp-sticky-review">
      <div className="saswp-open-class saswp-popup-btn">
      <div className="saswp-opn-cls-btn">

      <div className="saswp-onclick-hide">
      <span>
      <StarRatingHtml ratingVal={averageRating().toString()} />       
      </span>
      <span className="saswp-r4-rnm">{averageRating().toFixed(1)} from {props.collectionArray.length} reviews</span>                    
      </div>

      <div className="saswp-onclick-show">
      <span>Ratings and reviews</span>                    
      <span className="saswp-mines"></span>                    
      </div>

      </div>
      <div id="saswp-reviews-cntn">
      <div className="saswp-r4-info">
      <ul>

      <li className="saswp-r4-r">
      <span>
      <StarRatingHtml ratingVal={averageRating().toString()} />       
      </span>
        <span className="saswp-r4-rnm">{averageRating().toFixed(1)} from {props.collectionArray.length} reviews</span>                    
      </li>                                        
      {
        props.collectionArray.map( (value, index) =>(
          <li key={index}>
          <div className="saswp-r4-b">
          <span className="saswp-r4-str">
          <StarRatingHtml ratingVal={value.saswp_review_rating} />                 
          </span>
          <span className="saswp-r4-tx">{convertDateToString(value.saswp_review_date).date}</span>
          </div>          
          <div className="saswp-r4-cnt">
          <h3>{value.saswp_reviewer_name}</h3>
          <p>{value.saswp_review_text}</p>
          </div>          
          </li>
        ))
      }
      </ul>                    
      </div>
      </div>
      </div>
      </div>
    </>
  )
}

export default Popup;