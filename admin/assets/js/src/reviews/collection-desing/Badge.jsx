import React from 'react';
import StarRatingHtml from './../../common/star-rating-html/StarRatingHtml';

const Badge = (props) => {
    
    
  return (
    <>
    {props.collectionObject ? 
    <div className="saswp-rd3-warp">
        <ul>
            {
                Object.keys(props.collectionObject).map(function(key) {    
                    
                    let review_count   = props.collectionObject[key].length; 
                    let sum_of_rating  = 0;
                    let average_rating = 1;
                    let platform_name  = '';
                    let platform_icon  = '';

                    if(props.collectionObject[key]){
            
                        props.collectionObject[key].map((item) =>{            
             
                         sum_of_rating += parseFloat(item.saswp_review_rating);
                         platform_icon = item.saswp_review_platform_icon;
                         platform_name = item.saswp_review_platform_name;

                         if(platform_name == 'Self'){
                            platform_name = saswp_localize_data.trans_self;
                         }
             
                        });
             
                        if(sum_of_rating > 0){
                                            
                             average_rating = (sum_of_rating / review_count).toFixed(1);
                         
                         }
             
                     }
                    
                        return(
                    <li key={key}>                       
                      <a href="#">
                        <div className="saswp-r3-lg">
                          <span>
                           <img src={platform_icon}/>                           
                          </span>
                        <span className="saswp-r3-tlt">{platform_name}</span>
                        </div>

                      <div className="saswp-r3-rtng">

                        <div className="saswp-r3-rtxt">
                          <span className="saswp-r3-num">
                            {average_rating}
                          </span>
                          <span className="saswp-stars">
                            <StarRatingHtml ratingVal={average_rating.toString()} />                           
                          </span>
                        </div>

                        <span className="saswp-r3-brv">
                        {saswp_localize_data.trans_based_on +' '+ review_count+' '+saswp_localize_data.trans_reviews}
                        </span>

                      </div>
                      </a>
                      </li>
                        )
                  })
            }
        </ul>
    </div>
    :''
    }
    </>
  )
}
export default Badge;