import React, {useEffect, useState} from 'react';
import './Collection.scss';
import StarRatingHtml from './../../common/star-rating-html/StarRatingHtml';
import Pagination from './../../common/pagination/pagination'


const Grid = (props) => {
  
  const [pagination, setPagination]           = useState(false);
  const [postsCount, setPostsCount]           = useState(0);
  const [pageCount, setPageCount]             = useState(1);
  const [paginateClicked, setPaginateClicked] = useState(1);  
  const [gridCol, setGridCol]                 = useState([]);


  const handleGridPagination = (e) => {

    e.preventDefault();      
        
    setPaginateClicked(e.currentTarget.dataset.id);    
  
  }

  const handlePaginateAction = () => {

      
    let grid_col   = props.collectionArray;    
    let per_page   = props.postMeta.saswp_collection_per_page;
    let page_count = Math.ceil(props.collectionArray.length / per_page);  
    let next_page  = per_page;
    let offset     = 0;
    let pagination = props.postMeta.saswp_collection_pagination;

    
    if(paginateClicked > 0){                        
        next_page            = paginateClicked * per_page;                
    } 

    offset              = next_page - per_page;

    if(pagination && per_page > 0){
                               
      grid_col = grid_col.slice(offset, next_page);
      
    }      
        
    setPagination(pagination);
    setPostsCount(props.collectionArray.length);
    setPageCount(page_count);
    setGridCol(grid_col);
    
  }
  
  useEffect(() => {                               
    
    handlePaginateAction();
    
  },[]);
  
  useEffect(() => {                               
    
    handlePaginateAction();
    
  },[paginateClicked]);
  
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
        gridCol ? 
            <div className="saswp-r1">    
            <ul style={templateColumns()}>    
                {
                    gridCol.map((value, i) => (
                        
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

            {pagination ? 
            <div className="saswp-list-pagination">
              <Pagination pageCount={pageCount} postsCount={postsCount} paginateClicked={paginateClicked} onPaginate = {handleGridPagination} />
            </div> : ''}

            </div> 
            : ''
            }         
    </div>
  )
}
export default Grid;