import React, { useState, useEffect } from 'react';
import './Reviews.scss';
import queryString from 'query-string';
import {Link} from 'react-router-dom';
import Pagination from './../common/pagination/pagination';
import DottedSpinner from './../common/dotted-spinner/DottedSpinner';
import MainSpinner from './../common/main-spinner/MainSpinner';
import Icon from '@duik/icon'
import StarRatings from './../../node_modules/react-star-ratings';
import LeftContextMenu from './../common/Left-context-menu/LeftContextMenu';

const ReviewsCollections = () => {

    const [isLoaded, setIsLoaded]               = useState(true);  
    const [mainSpinner, setMainSpinner]         = useState(false);  
    const [partSpinner, setPartSpinner]         = useState(false);  

    const [postsData, setPostsData]             = useState([]);
    const [postsCount, setPostsCount]           = useState(0);
    const [pageCount, setPageCount]             = useState(0);
    const [paginateClicked, setPaginateClicked] = useState(1);
    const [currentPage, setCurrentPage]         = useState(1);
    const [moreBoxId, setMoreBoxId]             = useState(null);
    const [moreBoxIndex, setMoreBoxIndex]       = useState(null);

    const {__} = wp.i18n; 
    const page = queryString.parse(window.location.search); 

    const showMoreIconBox = (index, post_id) => {
                              
      if(moreBoxIndex != index || moreBoxId == null)  {
          setMoreBoxId(post_id);
          setMoreBoxIndex(index);
      }else{
          setMoreBoxId(null);
      }
     
    }

    const handleMoreClickAction =(data) => {
    
      setMoreBoxId(null);
      setMoreBoxIndex(null);
              
      if(data.post_id && data.action === 'delete'){       
  
        let newarr = [ ...postsData ];    
              newarr.splice(data.index,1);
              setPostsData(newarr);
              setPostsCount(postsCount -1);              
      } 
      
    }

    const getReviewsList = (search_text, page) => { 
      
    let url = saswp_localize_data.rest_url + "saswp-route/get-collections-list?search_param="+search_text+"&page="+page;
    
    fetch(url, {
      headers: {                    
        'X-WP-Nonce': saswp_localize_data.nonce,
      }
    })
    .then(res => res.json())
    .then(
      (result) => {              
          setMainSpinner(false); 
          setPartSpinner(false);        
          setPostsData(result.posts_data);
          setPostsCount(result.posts_found);     
      },        
      (error) => {        
      }
    );            
}

const paginateReviews =(e) => { 
    e.preventDefault();      
    setPartSpinner(true);               
    getReviewsList('', e.currentTarget.dataset.id);
    setPaginateClicked(e.currentTarget.dataset.index);
    setCurrentPage(e.currentTarget.dataset.id);    
  }

useEffect(() => {    
    setMainSpinner(true);            
    getReviewsList('',1);
}, [])

useEffect(() => {                    

    if(postsCount > 10){            
      let page_count = Math.ceil(postsCount / 10);
      setPageCount(page_count)
    }

  }, [postsCount])

  return (
      <div>
    {mainSpinner ? <MainSpinner /> : ''}
    <div className="card saswp-reviews-list-card">
        <div className="card-body">
         <div className="saswp-heading-top">
             <div>{postsCount > 0 ? <h4>Collections ({postsCount})</h4> : ''} </div>
             <div>
             <Link className="btn btn-success"  to={'admin.php?page=saswp&path=reviews_collections_single'}><Icon style={{'marginRight': '7px'}}>plus_rounded</Icon>{__('Create Collection', 'schema-and-structured-data-for-wp')}</Link>                                                                
             </div>
        </div>   
        
        </div>
       <div className="divider-horizontal"></div>    

       { postsData ?   
          <div className="saswp-reviews-list-body saswp-collections-list-body">
                      
        {partSpinner ?         
          <DottedSpinner /> :
        <div>
        { postsData.map((item, index) => (  
              
          <div key={index} className="card-body saswp-review-card">
            <div><input type="checkbox"/></div>
            <div className="saswp-review-avatar">        
        <Link to={`admin.php?page=saswp&path=reviews_collections_single&id=${item.post.post_id}`} className="quads-edit-btn">
            <span className="avatar">
                <span className="avatar-image-wrapper">
                    {item.post_meta.saswp_collection_images.map((item, index) => (
                      <img key={index} alt="" className={`avatar-image  collection-image${(index+1)}`} src={item}/>
                    ))}                                        
                    </span>
                      <span className="avatar-content">
                    <span className="avatar-name">{item.post.post_title}</span>
                    <span className="avatar-text-bottom">
                    <span className="">{item.post.post_modified}</span>
                    <span className="">{item.post_meta.saswp_total_reviews.length} reviews</span>                    
                    </span>                    
                    </span>
              </span>
              </Link>            
        </div>
            <div className="saswp-more-action">
              {moreBoxId === item.post.post_id ? 
                <LeftContextMenu onMoreAction={handleMoreClickAction} Option={
                [{menu_name:'Delete', menu_post_id:item.post.post_id, menu_action:'delete', index:index}]} />
              :''
              }
              <Icon onClick={() =>showMoreIconBox(index, item.post.post_id)} style={{'fontSize': '20px'}}>more</Icon>
              </div>          
          </div>
              
          ))}
          </div>
        } 
            
          </div>          
          
          : <div className="saswp-not-found">Collections not found. 
            <Link className="btn btn-success"  to={'admin.php?page=saswp&path=reviews_collections_single'}><Icon style={{'marginRight': '7px'}}>plus_rounded</Icon>{__('Create Collection', 'schema-and-structured-data-for-wp')}</Link>
          </div>  
          
           } 

    </div>
    
    {postsCount > 10 ? 
        <div className="saswp-list-pagination">
          <Pagination pageCount={pageCount} postsCount={postsCount} paginateClicked={paginateClicked} onPaginate = {paginateReviews} />
        </div> : ''}
    </div>
  )
}
export default ReviewsCollections;