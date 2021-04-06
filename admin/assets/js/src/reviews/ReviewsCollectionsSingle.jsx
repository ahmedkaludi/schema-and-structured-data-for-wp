import React, {useState, useEffect, useReducer} from 'react';
import './Reviews.scss';
import { useHistory } from 'react-router-dom';
import DottedSpinner from './../common/dotted-spinner/DottedSpinner';
import MainSpinner from './../common/main-spinner/MainSpinner';
import Icon from '@duik/icon'
import {Link} from 'react-router-dom';
import queryString from 'query-string'

import Grid from './collection-desing/Grid';
import Badge from './collection-desing/Badge';
import Fomo from './collection-desing/Fomo';
import Gallery from './collection-desing/Gallery';
import { Button } from '@duik/it'
import Popup from './collection-desing/Popup';
import $ from 'jquery';

$(document).on("click", ".saswp-accordion", function(){
  $(this).toggleClass("active");  
  $(this).next(".saswp-accordion-panel").slideToggle(200);
});


const ReviewsCollectionsSingle = () => {

  const history = useHistory();

  const [mainSpinner, setMainSpinner]                             = useState(false);  
  const [dottedSpinner, setDottedSpinner]                         = useState(false); 
  const [isLoaded, setIsLoaded]                                   = useState(true);  

  const [platformList, setPlatformList]                           = useState({}); 
  const [collectionId, setCollectionId]                           = useState(null); 
      
  const [addedReviews, setAddedReviews]                           = useState([]); 

  const [collectionObject, setCollectionObject]                   = useState({});
  const [collectionArray, setCollectionArray]                     = useState([]);    
  const {__} = wp.i18n; 
  const page = queryString.parse(window.location.search);  
  
  const [postsCount, setPostsCount]           = useState(0);
  const [pageCount, setPageCount]             = useState(1);
  const [paginateClicked, setPaginateClicked] = useState(1);  
  const [gridCol, setGridCol]                 = useState([]);

  const [postMeta, setPostMeta] = useReducer(
    (state, newState) => ({...state, ...newState}),
    {    
      saswp_collection_title:           'Untitle Collection',        
      saswp_collection_design:          'grid',
      saswp_collection_sorting:         'recent',
      saswp_collection_display_type:    'shortcode',
      saswp_collection_gallery_type:    'slider',
      saswp_collection_cols:            1,    
      saswp_gallery_arrow:              true,
      saswp_gallery_dots:               true,
      saswp_collection_pagination:      false, 
      saswp_collection_per_page:        10,
      saswp_fomo_interval:               3,
      saswp_fomo_visibility:             0,
      saswp_platform_ids:               {},
      saswp_total_reviews:              [],
      saswp_review_platform:             '',
      saswp_review_count:               5,
      saswp_collection_date_format:     '',
      saswp_collection_where:            [],
      saswp_collection_where_data:       [],

    }
  );

  
  const prepareGridData = () => {
  
    let grid_col   = collectionArray;     
    let per_page   = postMeta.saswp_collection_per_page;
    let page_count = Math.ceil(collectionArray.length / per_page);  
    let next_page  = per_page;
    let pagination = postMeta.saswp_collection_pagination;
    let offset     = 0;  
  
    
    if(paginateClicked > 0){                        
        next_page            = paginateClicked * per_page;                
    } 
  
    offset              = next_page - per_page;
  
    if(pagination && per_page > 0){
                               
      grid_col = grid_col.slice(offset, next_page);
         
    }      
          
    setPostsCount(collectionArray.length);
    setPageCount(page_count);
    setGridCol(grid_col);
  
  }

  const handlePostMetaChange = evt => {
    let { name, value, type } = evt.target;

    if(type === "checkbox"){
            value = evt.target.checked;
    }
    setPostMeta({[name]: value});      
  }

  const getReviewsByPlatformId =  (rvcount, platform_id, from) => {
    
    let url = saswp_localize_data.rest_url+'saswp-route/get-reviews-by-platform-id?rvcount='+rvcount+'&platform_id='+platform_id;
  
    fetch(url,{
      headers: {                    
        'X-WP-Nonce': saswp_localize_data.nonce,
      }
    }
    )
    .then(res => res.json())
    .then(
      (result) => {                                              
        
          if(result.status){            
                          
            let data = result['message'];          
            setCollectionArray( (prevState) => ([ ...prevState, ...data ]));  
            setCollectionObject(prevState => ({ ...prevState, [platform_id]: data}));          
            
            let rvcount = [{platform_id : platform_id, platform_name: data[0].saswp_review_platform_name, review_count: data.length}];
            
            setAddedReviews( (prevState) => ([ ...prevState, ...rvcount ]));  
          }
  
      },        
      (error) => {
        
      }
    );  
  
    }
  
  const getCollectionData =  (collection_id) => {
    
    let url = saswp_localize_data.rest_url+'saswp-route/get-collection-data-by-id?collection_id='+collection_id;

    fetch(url,{
      headers: {                    
        'X-WP-Nonce': saswp_localize_data.nonce,
      }
    }
    )
    .then(res => res.json())
    .then(
      (result) => {                                              
        
        if(result.post_meta){
          setPostMeta(result.post_meta);          
            
          if(result.post_meta.saswp_platform_ids){
            Object.keys(result.post_meta.saswp_platform_ids).map(function(key) {  
              getReviewsByPlatformId(result.post_meta.saswp_platform_ids[key], key, '');                    
            });
          }

        }

      },        
      (error) => {
        
      }
    );  

 }

  const getPlatformList =  () => {
    
    let url = saswp_localize_data.rest_url+'saswp-route/get-platforms?bystatus=yes';

    fetch(url,{
      headers: {                    
        'X-WP-Nonce': saswp_localize_data.nonce,
      }
    }
    )
    .then(res => res.json())
    .then(
      (result) => {                                              
        setPlatformList(result);                              
      },        
      (error) => {
        
      }
    );  

 }

 

 const handlePlatformOption =  () => {
   
  let active   = [];
  let inactive = [];
  
  if(typeof(platformList.active) !== 'undefined'){        
    Object.keys(platformList.active).map(function(key) {      
      active.push( <option key={key} value={key}>{platformList.active[key]}</option>);
    });
  }

  if(typeof(platformList.inactive) !== 'undefined'){        
    Object.keys(platformList.inactive).map(function(key) {      
      inactive.push( <option disabled={true} key={key} value={key}>{platformList.inactive[key]}</option>);
    });
  }
  
  if(active || inactive){
    return (<select onChange={handlePostMetaChange} value={postMeta.saswp_review_platform} name="saswp_review_platform">
      {active ? <optgroup label="Active">
        {active}
      </optgroup>: ''}      
      {inactive ? <optgroup label="InActive">
        {inactive}
      </optgroup> : ''}      
    </select>);
  }else{
    return null;
  }
  
}

const handleCollectionSorting = (sorting_type) => {
             
  if(collectionArray.length > 0){
      
      switch(sorting_type){
           
       case 'lowest':
               
               collectionArray.sort(function(a, b) {
                 return a.saswp_review_rating - b.saswp_review_rating;
               });   
                                       
           break;
           
       case 'highest':
                   
               collectionArray.sort(function(a, b) {
                 return a.saswp_review_rating - b.saswp_review_rating;
               });   
               collectionArray.reverse();
               break;
               
      case 'newest':
      case 'recent':
                   
               collectionArray.sort(function(a, b) {
                 var dateA = new Date(a.saswp_review_date), dateB = new Date(b.saswp_review_date);  
                 return dateA - dateB;
               });   
               collectionArray.reverse();                                   
                                                                                 
           break;
           
      case 'oldest':
          
               collectionArray.sort(function(a, b) {
                 var dateA = new Date(a.saswp_review_date), dateB = new Date(b.saswp_review_date);  
                 return dateA - dateB;
               });   
                                                                                                                       
           break; 
       
       case 'random':
                   
               collectionArray.sort(function(a, b) {
                 return 0.5 - Math.random();
               });   
                                                                                 
           break;
           
       }
      
  }
             
}


const handleGridPagination = (e) => {

  e.preventDefault();      
      
  setPaginateClicked(e.currentTarget.dataset.id);    

}

const handleFetchReviews = (e) => {

    e.preventDefault();
    let platform_id = null;
    let rv_count  = postMeta.saswp_review_count;

    if(postMeta.saswp_review_platform){
        platform_id = postMeta.saswp_review_platform;
    }else{
      if(typeof(platformList.active) !== 'undefined'){
        platform_id = Object.keys(platformList.active)[0];
      }
      
    }

    if(rv_count && platform_id){
      getReviewsByPlatformId(rv_count, platform_id, 'clicked');
    }

}

const handleSaveCollection = (e) => {
  
  e.preventDefault();
  
  setIsLoaded(false);

  let   body_json       = {};    
      
  body_json.post_meta       = postMeta;                 
  body_json.collection_id   = collectionId;
                                     
  let url = saswp_localize_data.rest_url + 'saswp-route/update-collection';
  fetch(url,{
    method: "post",
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-WP-Nonce': saswp_localize_data.nonce,
    },    
    body: JSON.stringify(body_json)
  })
  .then(res => res.json())
  .then(
    (result) => {

      setIsLoaded(true);
          
          if(result.collection_id){

              setCollectionId(result.collection_id);                                               
              let query = '?page='+page.page+'&path='+page.path+'&id='+result.collection_id;
              let search = location.pathname + query;
              history.push(search);

          }                
                             
    },        
    (error) => {    
    }
  ); 
  
}

useEffect(() => {                               

  prepareGridData();
  
}, [paginateClicked]);

  useEffect(() => {                               

    getPlatformList(); 

    if(typeof(page.id)  != 'undefined' ) {                   
      setCollectionId(page.id);            
      getCollectionData(page.id);      
    }    

  }, []);

  useEffect(() => {                               

    let review_id = [];

    handleCollectionSorting(postMeta.saswp_collection_sorting);
    prepareGridData();
    
    if(collectionArray){
      collectionArray.map( (item) => {
        review_id.push(item.saswp_review_id);
      })
    }  
    setPostMeta({saswp_total_reviews: review_id});          
  }, [collectionArray]);

  useEffect(() => {                               
    
    handleCollectionSorting(postMeta.saswp_collection_sorting);
    prepareGridData();
      
  }, [postMeta]);
  
   
  return (
    <div>
      {mainSpinner ? <MainSpinner /> : ''}
      
      <form encType="multipart/form-data" method="post" id="saswp_review_form">
      <div className="saswp-single-header">
          <div className="saswp-single-header-left"><h3>{__('Collection Setup', 'schema-and-structured-data-for-wp')}</h3></div>
          <div className="saswp-single-header-right"><Link to={`admin.php?page=saswp&path=reviews_collections`}><Icon>close</Icon></Link></div>
      </div>

      <div className="saswp-collection-container">

      <div className="saswp-collection-body">
      <div className="saswp-collection-lp">
        <div className="saswp-collection-title">
        <input type="text" onChange={handlePostMetaChange} value={postMeta.saswp_collection_title} id="saswp_collection_title" name="saswp_collection_title" />
        </div>
        <div className="saswp-collection-preview">
                
        {(() => {
            
            switch (postMeta.saswp_collection_design) {

              case 'grid':
                
                  return(
                    <>
                    {(gridCol.length > 0) ? 
                          <Grid
                           collectionObject={collectionObject}
                           collectionArray ={collectionArray}                                                      
                           collectionCols={postMeta.saswp_collection_cols}                           
                           postMeta={postMeta}  
                           
                           gridCol={gridCol}  
                           pageCount={pageCount}  
                           postsCount={postsCount}  
                           paginateClicked={paginateClicked}  
                           handleGridPagination={handleGridPagination}  
                        />
                         : ''}
                    </>
                  );

              case 'gallery':
                return(                
                  <>
                    {(collectionArray.length > 0) ? 
                          <Gallery
                          collectionObject={collectionObject}
                          collectionArray ={collectionArray}
                          postMeta={postMeta}                           
                        />
                         : ''}
                    </>
                );
              case 'badge':
                  return(
                    <>
                    {(collectionArray.length > 0) ? 
                          <Badge
                          collectionObject={collectionObject}
                          collectionArray ={collectionArray}
                     />
                         : ''}
                    </>
                  );

              case 'popup':
                return(
                  <>
                    {(collectionArray.length > 0) ? 
                       <Popup
                       collectionObject={collectionObject}
                       collectionArray ={collectionArray}
                      />
                         : ''}
                    </>
                    );

              case 'fomo':
                return(
                  <>
                    {(collectionArray.length > 0) ? 
                       <Fomo                  
                       collectionObject={collectionObject}
                       collectionArray ={collectionArray}
                       interval   = {postMeta.saswp_fomo_interval}
                       visibility = {postMeta.saswp_fomo_visibility}
                      />
                         : ''}
                    </>
                );
            }
          })()}

        </div>
      </div>
      
      <div className="saswp-collection-settings">
                            <ul>
                                <li>
                                    <a className="saswp-accordion">{__('Reviews Source', 'schema-and-structured-data-for-wp')}</a>
                                    <div className="saswp-accordion-panel">
                                        <div className="saswp-plf-lst-rv-cnt">
                                          {handlePlatformOption()}  
                                        <input type="number" id="saswp_review_count" name="saswp_review_count" min="0" onChange={handlePostMetaChange} value={postMeta.saswp_review_count} />
                                        <a className="button button-default saswp-add-to-collection" onClick={handleFetchReviews}>{__('Add', 'schema-and-structured-data-for-wp')}</a>
                                      </div>
                                      <div className="saswp-platform-added-list">

                                        {
                                          addedReviews ? 

                                          addedReviews.map((item, key) => {                                              
                                                return(<div key={key} className="cancel-btn">                                                                                                    
                                                    <span>{item.platform_name}</span>
                                                    <span>({item.review_count})</span>                                                                                                        
                                                    <a data-id={item.platform_id} className="button button-default saswp-remove-platform"></a>
                                                   </div>)
                                          })

                                          : ''
                                        }
                                        
                                          </div>                                                                            
                                    </div>
                                </li>
                                <li>                                     
                                    <a className="saswp-accordion">{__('Presentation', 'schema-and-structured-data-for-wp')}</a>
                                    <div className="saswp-accordion-panel">
                                        <div className="saswp-dp-dsg">
                                        <label>{__('Design', 'schema-and-structured-data-for-wp')}</label>  
                                        <select onChange={handlePostMetaChange} value={postMeta.saswp_collection_design} name="saswp_collection_design" className="saswp-collection-desing saswp-coll-settings-options">
                                            <option value="grid">{__('Grid', 'schema-and-structured-data-for-wp')}</option>
                                            <option value="gallery">{__('Gallery', 'schema-and-structured-data-for-wp')}</option>
                                            <option value="badge">{__('Badge', 'schema-and-structured-data-for-wp')}</option>
                                            <option value="popup">{__('PopUp', 'schema-and-structured-data-for-wp')}</option>
                                            <option value="fomo">{__('Fomo', 'schema-and-structured-data-for-wp')}</option>
                                         </select>
                                        </div>
                                        {postMeta.saswp_collection_design == 'grid' ? 
                                          <>
                                        <div className="saswp-dp-dsg saswp-coll-options saswp-grid-options saswp-dp-dtm">
                                        <label>{__('Columns', 'schema-and-structured-data-for-wp')}</label>
                                        <input onChange={handlePostMetaChange} value={postMeta.saswp_collection_cols} type="number" id="saswp-collection-cols" name="saswp_collection_cols" min="1" className="saswp-number-change saswp-coll-settings-options saswp-coll-options saswp-grid-options" />
                                        </div>                                        
                                        <div className="saswp-dp-dsg saswp-coll-options saswp-grid-options saswp-dp-dtm">
                                            <span>{__('Pagination', 'schema-and-structured-data-for-wp')}</span>
                                            <span><input onChange={handlePostMetaChange} checked={postMeta.saswp_collection_pagination} name="saswp_collection_pagination" type="checkbox" id="saswp-coll-pagination" className="saswp-coll-settings-options" /></span>
                                        </div> 
                                        {postMeta.saswp_collection_pagination ?

                                        <div className="saswp-dp-dsg saswp-coll-options saswp-grid-options saswp-dp-dtm _imp">
                                          <label>{__('Per Page', 'schema-and-structured-data-for-wp')}</label>
                                          <input onChange={handlePostMetaChange} value={postMeta.saswp_collection_per_page} name="saswp_collection_per_page" type="number" min="1" id="saswp-coll-per-page" className="saswp-coll-settings-options" />
                                        </div>  
                                         : ''}                                                                               
                                          </>
                                        : ''
                                        }                                                                                
                                        {postMeta.saswp_collection_design == 'gallery' ? 
                                        <>
                                        <div className="saswp-dp-dsg saswp-dp-dtm saswp-slider-options saswp-coll-options ">
                                         <label>{__('Slider Type', 'schema-and-structured-data-for-wp')}</label>
                                        <select onChange={handlePostMetaChange} value={postMeta.saswp_collection_gallery_type} name="saswp_collection_gallery_type" id="saswp_collection_gallery_type" className="saswp-slider-type saswp-slider-options  saswp-coll-settings-options saswp-coll-options">
                                            <option value="slider">{__('Slider', 'schema-and-structured-data-for-wp')}</option>
                                            <option value="carousel">{__('Carousel', 'schema-and-structured-data-for-wp')}</option>
                                        </select>
                                        </div>
                                        <div className="saswp-slider-display saswp-slider-options  saswp-coll-settings-options saswp-coll-options">
                                            <span><input type="checkbox" id="saswp_gallery_arrow" name="saswp_gallery_arrow" onChange={handlePostMetaChange} checked={postMeta.saswp_gallery_arrow} /> {__('Arrows', 'schema-and-structured-data-for-wp')}</span>
                                            <span><input type="checkbox" id="saswp_gallery_dots" name="saswp_gallery_dots" onChange={handlePostMetaChange} checked={postMeta.saswp_gallery_dots}  /> {__('Dots', 'schema-and-structured-data-for-wp')}</span>
                                        </div>
                                        </>
                                        : ''}                                        
                                        {postMeta.saswp_collection_design == 'fomo' ? 
                                          <div className="saswp-fomo-options  saswp-coll-options"> 
                                            <div className="saswp-dp-dsg saswp-dp-dtm">
                                            <span>{__('Delay Time In Sec', 'schema-and-structured-data-for-wp')}</span>
                                            <input type="number" id="saswp_fomo_interval" name="saswp_fomo_interval" className="saswp-number-change" min="1" onChange={handlePostMetaChange} value={postMeta.saswp_fomo_interval} /> 
                                          </div>                                                                           
                                          </div>
                                        : ''
                                        }                                                                                                               
                                    </div>
                                </li>
                              <li>
                                <a className="saswp-accordion">{__('Filter', 'schema-and-structured-data-for-wp')}</a>
                                <div className="saswp-accordion-panel">
                                    <div className="saswp-dp-dsg">
                                        <label>{__('Sorting', 'schema-and-structured-data-for-wp')}</label>  
                                        <select onChange={handlePostMetaChange} value={postMeta.saswp_collection_sorting} name="saswp_collection_sorting" className="saswp-collection-sorting saswp-coll-settings-options">
                                          <option value="recent">{__('Recent', 'schema-and-structured-data-for-wp')}</option>
                                          <option value="oldest">{__('Oldest', 'schema-and-structured-data-for-wp')}</option>
                                          <option value="newest">{__('Newest', 'schema-and-structured-data-for-wp')}</option>
                                          <option value="highest">{__('Highest Rating', 'schema-and-structured-data-for-wp')}</option>
                                          <option value="lowest">{__('Lowest Rating', 'schema-and-structured-data-for-wp')}</option>
                                          <option value="random">{__('Random', 'schema-and-structured-data-for-wp')}</option>
                                          </select>
                                    </div>
                                </div>
                              </li>
                              <li>
                                <a className="saswp-accordion">{__('Display', 'schema-and-structured-data-for-wp')}</a>
                                <div className="saswp-accordion-panel">
                                    <div className="saswp-dp-dsg">
                                        <label>{__('Display Type', 'schema-and-structured-data-for-wp')}</label>
                                        <select className="saswp-collection-display-method" name="saswp_collection_display_type">
                                            <option value="shortcode">{__('Shortcode', 'schema-and-structured-data-for-wp')}</option> 
                                        </select>
                                    </div>
                                    
                                        <div id="motivatebox" className="saswp-collection-shortcode">
                                            <span className="motivate">
                                            [saswp-reviews-collection id="125"]
                                            </span>
                                        </div>
                                   
                                </div>
                              </li>
                            </ul>
                            <div className="saswp-sv-btn">
                            <div>{isLoaded ? <a className="btn btn-success" onClick={handleSaveCollection}>{__('Save', 'schema-and-structured-data-for-wp')}</a> : <Button success loading>Loading success</Button>}</div>
                            </div>   
                          </div>
                        </div>
                      </div>
                  </form>        
                </div>
  )
}
export default ReviewsCollectionsSingle;