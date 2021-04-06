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

const ReviewsList = () => {

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
        
      } else if (data.post_id && data.action) {
  
        let newarr = [ ...postsData ];    
        let change_arr = newarr[data.index];
        change_arr.post.post_status = data.action;      
        newarr[data.index] = change_arr;
        setPostsData(newarr);
  
      }
      
    }

    const getReviewsList = (search_text, page) => { 
      
    let url = saswp_localize_data.rest_url + "saswp-route/get-reviews-list?search_param="+search_text+"&page="+page;
    
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
             <div>{postsCount > 0 ? <h4>{__('Review Items', 'schema-and-structured-data-for-wp')} ({postsCount})</h4> : ''} </div>
             <div>
             <Link className="btn btn-success"  to={'admin.php?page=saswp&path=reviews_single'}><Icon style={{'marginRight': '7px'}}>plus_rounded</Icon>{__('Add Review', 'schema-and-structured-data-for-wp')}</Link>                                                                
             </div>
        </div>   
        
        </div>
       <div className="divider-horizontal"></div>    

       { postsData ?   
          <div className="saswp-reviews-list-body">
                      
        {partSpinner ?         
          <DottedSpinner /> :
        <div>          
        { postsData.map((item, index) => (  
              
          <div key={index} className="card-body saswp-review-card">
            <div><input type="checkbox"/></div>
            <div className="saswp-review-avatar">        
            <Link to={`admin.php?page=saswp&path=reviews_single&id=${item.post.post_id}`} className="saswp-edit-btn">
            <span className="avatar">
                <span className="avatar-image-wrapper">
                    <img alt="" className="avatar-image" src={item.post_meta.saswp_reviewer_image}/>
                    </span>
                      <span className="avatar-content">
                        <span className="avatar-name">
                          <strong>{item.post_meta.saswp_reviewer_name} {(item.post.post_status == 'draft' || item.post.post_status == 'pending') ? (<span>( {item.post.post_status} )</span>) : ''}</strong>
                    </span>
                    <span className="avatar-text-bottom">
                    <span className="">{item.post_meta.saswp_review_date}</span>
                    <span className="">
                      <img className="saswp-review-list-img" src={item.post_meta.saswp_review_platform_image}/>
                        <span className="saswp-platform-name">{item.post_meta.saswp_review_platform_name}</span>
                      </span>
                    <span className="">

                    <StarRatings                    
                      isSelectable ={true}
                      isAggregateRating={true}
                      rating={ (typeof(item.post_meta.saswp_review_rating) !== 'undefined') ? Number(item.post_meta.saswp_review_rating) : 0}
                      starRatedColor="#ffd700"
                      starHoverColor="#ffd700"                  
                      numberOfStars={5}
                      name='rating'
                      starDimension="13px"
                      starSpacing="1px"
                    />
                    </span>
                    </span>                    
                    </span>
              </span>
              </Link>            
        </div>
            <div className="saswp-more-action">
              {moreBoxId === item.post.post_id ? 
                <LeftContextMenu onMoreAction={handleMoreClickAction} Option={
                [{menu_name:(item.post.post_status == 'draft' ? 'Publish' : 'Draft'), menu_post_id:item.post.post_id, menu_action:(item.post.post_status == 'draft' ? 'publish' : 'draft'), index:index},
                {menu_name:'Delete', menu_post_id:item.post.post_id, menu_action:'delete', index:index},
                ]} />
              :''
              }
              <Icon onClick={() =>showMoreIconBox(index, item.post.post_id)} style={{'fontSize': '20px'}}>more</Icon>
              </div>          
          </div>
              
          ))}
          </div>
        } 
            
          </div>          
          
          : <div className="saswp-not-found">{__('Reviews not found.', 'schema-and-structured-data-for-wp')} 
            <Link className="btn btn-success"  to={'admin.php?page=saswp&path=reviews_single'}><Icon style={{'marginRight': '7px'}}>plus_rounded</Icon>{__('Add Review', 'schema-and-structured-data-for-wp')}</Link>
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
export default ReviewsList;