import React from 'react';
import './StarRatingHtml.scss';

const StarRatingHtml = (props) => {
    
    const starRating = () =>{

        let starating = [];
        let rating_val = props.ratingVal;

        for(var j=0; j<5; j++){  

            if(rating_val >j){

                  var explod = rating_val.split('.');

                  if(explod[1]){

                      if(j < explod[0]){

                        starating.push(<span key = {j} className="str-ic"></span>);   

                      }else{

                        starating.push(<span key = {j} className="half-str"></span>);   

                      }                                           
                  }else{

                    starating.push(<span key = {j} className="str-ic"></span>)    

                  }

            } else{
                starating.push(<span key = {j} className="df-clr"></span>);   
            }                                                                                                                                
          }

          return starating;
    }

    
  return (    
    <>
    <div className="saswp-rvw-str">
     {starRating()}         
    </div>
    </>
  )

}

export default StarRatingHtml;