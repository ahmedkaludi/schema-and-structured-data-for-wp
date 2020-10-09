import React, {useEffect, useState} from 'react';
import './Collection.scss';
import StarRatingHtml from './../../common/star-rating-html/StarRatingHtml';
import Pagination from './../../common/pagination/pagination'


const Grid = (props) => {
  
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


const templateColumns = () => {
  
  let grid_cols = '';

  for(var i=1; i <= props.collectionCols; i++){
    grid_cols +=' 1fr'; 
  }

  let template_style = {  
    gridTemplateColumns: grid_cols, 
    overflowY: "hidden"   
  };

  return template_style;
}
   
  return (    
    <div>
        {          
        props.gridCol ? 
            <div className="saswp-r1">    
            <ul style={templateColumns()}>    
                {
                    props.gridCol.map((value, i) => (
                        
                        <li key={i}>               
                            <div className="saswp-rc">
                            <div className="saswp-rc-a">
                            <div className="saswp-r1-aimg">
                            <img src={value.saswp_reviewer_image}  style={{'width': '56','height': '56'}} />
                            </div>
                            <div className="saswp-rc-nm">
                            <a href="#">{value.saswp_reviewer_name}</a>
                            <StarRatingHtml ratingVal={value.saswp_review_rating} />                             
                            <span className="saswp-rc-dt">{convertDateToString(value.saswp_review_date).date}</span>                            
                            </div>
                            </div>                            
                            <div className="saswp-rc-lg">
                            <img src={value.saswp_review_platform_icon}/>
                            </div>                            
                            </div>
                            <div className="saswp-rc-cnt">
                            <p>{value.saswp_review_text}</p>
                            </div>
                          </li>

                    ))
                }
            
            </ul> 

            {props.postMeta.saswp_collection_pagination ? 
            <div className="saswp-list-pagination">
              <Pagination pageCount={props.pageCount} postsCount={props.postsCount} paginateClicked={props.paginateClicked} onPaginate = {props.handleGridPagination} />
            </div> : ''}

            </div> 
            : ''
            }         
    </div>
  )
}
export default Grid;