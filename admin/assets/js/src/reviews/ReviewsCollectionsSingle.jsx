import React, {useState, useEffect, useReducer} from 'react';
import './Reviews.scss';
import DottedSpinner from './../common/dotted-spinner/DottedSpinner';
import MainSpinner from './../common/main-spinner/MainSpinner';
import Icon from '@duik/icon'
import {Link} from 'react-router-dom';
import queryString from 'query-string'

import Grid from './collection-desing/Grid';
import Badge from './collection-desing/Badge';
import Fomo from './collection-desing/Fomo';
import Gallery from './collection-desing/Gallery';
import Popup from './collection-desing/Popup';
import $ from 'jquery';

$(document).on("click", ".saswp-accordion", function(){
  $(this).toggleClass("active");  
  $(this).next(".saswp-accordion-panel").slideToggle(200);
});


const ReviewsCollectionsSingle = () => {

  const [mainSpinner, setMainSpinner]                             = useState(false);  
  const [dottedSpinner, setDottedSpinner]                         = useState(false); 
  const [platformList, setPlatformList]                           = useState({}); 
  const [collectionId, setCollectionId]                           = useState(null); 
  
  const [reviewCount, setReviewCount]                             = useState(5); 
  const [addedReviews, setAddedReviews]                           = useState([]); 

  const [collectionObject, setCollectionObject]                   = useState({});
  const [collectionArray, setCollectionArray]                     = useState([]);    
  const {__} = wp.i18n; 
  const page = queryString.parse(window.location.search);  

  const [postMeta, setPostMeta] = useReducer(
    (state, newState) => ({...state, ...newState}),
    {    
      saswp_collection_title:           '',        
      saswp_collection_design:          '',
      saswp_collection_sorting:         '',
      saswp_collection_display_type:    '',
      saswp_collection_gallery_type:    '',
      saswp_collection_cols:            1,    
      saswp_gallery_arrow:              '',
      saswp_gallery_dots:               '',
      saswp_collection_pagination:      '', 
      saswp_collection_per_page:        10,
      saswp_fomo_interval:               3,
      saswp_fomo_visibility:             0,
      saswp_platform_ids:               {},
      saswp_total_reviews:              [],       
    }
  );

  const handleReviewCount = (e) => {

    let {value} = evt.target;
    setReviewCount(value);

  }
  const handlePostMetaChange = evt => {
    let { name, value, type } = evt.target;

    if(type === "checkbox"){
            value = evt.target.checked;
    }
    setPostMeta({[name]: value});    
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

 const getReviewsByPlatformId =  (rvcount, platform_id) => {
    
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

 const handlePlatformOption =  () => {
   
  let active   = [];
  let inactive = [];
  
  if(platformList.active){
    Object.keys(platformList.active).map(function(key) {      
      active.push( <option key={key} value={key}>{platformList.active[key]}</option>);
    });
  }

  if(platformList.inactive){
    Object.keys(platformList.inactive).map(function(key) {      
      inactive.push( <option key={key} value={key}>{platformList.inactive[key]}</option>);
    });
  }
  
  if(active || inactive){
    return (<select name="saswp_review_platform">
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

  
  useEffect(() => {                               

    getPlatformList(); 

    if(typeof(page.id)  != 'undefined' ) {                   
      setCollectionId(page.id);            
      getCollectionData(page.id);      
    }

  }, []);

  useEffect(() => {                               
    
    if(postMeta.saswp_platform_ids){
        Object.keys(postMeta.saswp_platform_ids).map(function(key) {  
          getReviewsByPlatformId(postMeta.saswp_platform_ids[key], key);                    
        });
    }        

  }, [postMeta]);

  useEffect(() => {                               
    console.log(addedReviews);      
  }, [addedReviews]);

   
  return (
    <div>
      {mainSpinner ? <MainSpinner /> : ''}
      
      <form encType="multipart/form-data" method="post" id="saswp_review_form">
      <div className="saswp-single-header">
          <div className="saswp-single-header-left"><h3>Collection Setup</h3></div>
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
                    {(collectionArray.length > 0) ? 
                          <Grid
                           collectionObject={collectionObject}
                           collectionArray ={collectionArray}                                                      
                           collectionCols={postMeta.saswp_collection_cols}                           
                           postMeta={postMeta}                           
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
                                    <a className="saswp-accordion">Reviews Source</a>
                                    <div className="saswp-accordion-panel">
                                                                              <div className="saswp-plf-lst-rv-cnt">
                                                                              {handlePlatformOption()}  
                                        <input type="number" id="saswp-review-count" name="saswp-review-count" min="0" onChange={handleReviewCount} value={reviewCount} />
                                        <a className="button button-default saswp-add-to-collection">Add</a>
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
                                    <a className="saswp-accordion">Presentation</a>
                                    <div className="saswp-accordion-panel">
                                        <div className="saswp-dp-dsg">
                                        <label>Design</label>  
                                        <select onChange={handlePostMetaChange} value={postMeta.saswp_collection_design} name="saswp_collection_design" className="saswp-collection-desing saswp-coll-settings-options">
                                            <option value="grid">Grid</option>
                                            <option value="gallery">Gallery</option>
                                            <option value="badge">Badge</option>
                                            <option value="popup">PopUp</option>
                                            <option value="fomo">Fomo</option>                                    
                                         </select>
                                        </div>
                                        <div className="saswp-dp-dsg saswp-coll-options saswp-grid-options saswp-dp-dtm">
                                        <label>Columns</label>
                                        <input onChange={handlePostMetaChange} value={postMeta.saswp_collection_cols} type="number" id="saswp-collection-cols" name="saswp_collection_cols" min="1" className="saswp-number-change saswp-coll-settings-options saswp-coll-options saswp-grid-options" />    
                                        </div>
                                        
                                        <div className="saswp-dp-dsg saswp-coll-options saswp-grid-options saswp-dp-dtm">
                                            <span>Pagination</span>
                                            <span><input onChange={handlePostMetaChange} checked={postMeta.saswp_collection_pagination} name="saswp_collection_pagination" type="checkbox" id="saswp-coll-pagination" className="saswp-coll-settings-options" /></span>
                                        </div>
                                        
                                        <div className="saswp-dp-dsg saswp-coll-options saswp-grid-options saswp-dp-dtm saswp_hide_imp">
                                            <label>Per Page</label>
                                            <input onChange={handlePostMetaChange} value={postMeta.saswp_collection_per_page} name="saswp_collection_per_page" type="number" min="1" id="saswp-coll-per-page" className="saswp-coll-settings-options" />
                                        </div>
                                        
                                        <div className="saswp-dp-dsg saswp-dp-dtm saswp-slider-options saswp-coll-options saswp_hide">
                                         <label>Slider Type</label>
                                        <select onChange={handlePostMetaChange} value={postMeta.saswp_collection_gallery_type} name="saswp_collection_gallery_type" id="saswp_collection_gallery_type" className="saswp-slider-type saswp-slider-options saswp_hide saswp-coll-settings-options saswp-coll-options">
                                            <option value="slider">Slider</option>
                                            <option value="carousel">Carousel</option>
                                        </select>
                                        </div>
                                        <div className="saswp-slider-display saswp-slider-options saswp_hide saswp-coll-settings-options saswp-coll-options">
                                            <span><input type="checkbox" id="saswp_gallery_arrow" name="saswp_gallery_arrow" onChange={handlePostMetaChange} checked={postMeta.saswp_gallery_dots} /> Arrows</span>
                                            <span><input type="checkbox" id="saswp_gallery_dots" name="saswp_gallery_dots" onChange={handlePostMetaChange} checked={postMeta.saswp_gallery_dots}  /> Dots</span>
                                        </div>
                                        
                                        <div className="saswp-fomo-options saswp_hide saswp-coll-options"> 
                                            <div className="saswp-dp-dsg saswp-dp-dtm">
                                            <span>Delay Time In Sec                                            </span>
                                            <input type="number" id="saswp_fomo_interval" name="saswp_fomo_interval" className="saswp-number-change" min="1" onChange={handlePostMetaChange} value={postMeta.saswp_fomo_interval} /> 
                                            </div>                                                                           
                                        </div>                                                                        
                                    </div>
                                </li>
                              <li>

                                <a className="saswp-accordion">Filter</a>
                                <div className="saswp-accordion-panel">
                                    <div className="saswp-dp-dsg">
                                        <label>Sorting</label>  
                                        <select onChange={handlePostMetaChange} value={postMeta.saswp_collection_sorting} name="saswp_collection_sorting" className="saswp-collection-sorting saswp-coll-settings-options">                                      
                                          <option value="recent">Recent</option>
                                          <option value="oldest">Oldest</option>
                                          <option value="newest">Newest</option>
                                          <option value="highest">Highest Rating</option>
                                          <option value="lowest">Lowest Rating</option>
                                          <option value="random">Random</option>                                        
                                          </select>
                                    </div>
                                </div>
                              </li>
                              <li>
                                <a className="saswp-accordion">Display</a>
                                <div className="saswp-accordion-panel">
                                    <div className="saswp-dp-dsg">
                                        <label>Display Type</label>
                                        <select className="saswp-collection-display-method" name="saswp_collection_display_type">
                                            <option value="shortcode">Shortcode</option> 
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
                                <button type="submit" className="button button-primary"> 
                                    Save</button>
                            </div>   
      </div>


      </div>
      </div>

      </form>  
      

    </div>
  )
}
export default ReviewsCollectionsSingle;