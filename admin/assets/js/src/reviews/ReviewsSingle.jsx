import React, { useState, useEffect, useReducer } from 'react';
import {Link} from 'react-router-dom';
import queryString from 'query-string'
import { useParams, useLocation, useHistory } from 'react-router-dom';
import './Reviews.scss';
import MediaUpload from './../common//mediaUpload/MediaUpload';
import DottedSpinner from './../common/dotted-spinner/DottedSpinner';
import MainSpinner from './../common/main-spinner/MainSpinner';
import { Button } from '@duik/it'
import StarRatings from './../../node_modules/react-star-ratings';
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import Icon from '@duik/icon'



const ReviewsSingle = () => {
  
  const [reviewId, setReviewId]               = useState('');
  const [postStatus, setPostStatus]           = useState('');  
  const [platformList, setPlatformList]       = useState({});  
  const [isLoaded, setIsLoaded]               = useState(true);  
  const [mainSpinner, setMainSpinner]         = useState(false);  
  const [partSpinner, setPartSpinner]         = useState(false); 

  const [userInput, setUserInput] = useReducer(
    (state, newState) => ({...state, ...newState}),
    {    
    saswp_review_id:                '',
    saswp_review_location_id:       '',
    saswp_reviewer_image:           '',
    saswp_reviewer_image_id:        '',
    saswp_reviewer_image_detail:    {height:'', width:'', thumbnail:''},    
    saswp_reviewer_name:            '',
    saswp_review_rating:            5,
    saswp_review_date:              new Date(), 
    saswp_review_text:              '',
    saswp_review_link:              '',
    saswp_review_time:              '',
    saswp_reviewer_lang:            '',
    saswp_review_product_id:        null,
    saswp_review_platform:          null  
    }
  );

  const {__} = wp.i18n; 
  const page = queryString.parse(window.location.search);  
  const history = useHistory();
  
  const saveFormData = (status) => {
                      
    setIsLoaded(false);

    let   body_json       = {};    
        
    body_json.status      = status;                 
    body_json.review_id   = reviewId;
    body_json.post_meta   = userInput;                                        

    let url = saswp_localize_data.rest_url + 'saswp-route/update-review';
    fetch(url,{
      method: "post",
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-WP-Nonce': saswp_localize_data.nonce,
      },
      //make sure to serialize your JSON body
      body: JSON.stringify(body_json)
    })
    .then(res => res.json())
    .then(
      (result) => {

        setIsLoaded(true);
            
            if(result.review_id){
                setReviewId(result.review_id);    
                setPostStatus(status);                               
                let query = '?page='+page.page+'&path='+page.path+'&id='+result.review_id;
                let search = location.pathname + query;
                history.push(search)
            }                
                               
      },        
      (error) => {    
      }
    ); 
    
  }

  
  const getPlatformList =  () => {
    
    let url = saswp_localize_data.rest_url+'saswp-route/get-platforms';

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

  const getReviewDataById =  (review_id) => {
    setMainSpinner(true);
    let url = saswp_localize_data.rest_url+'saswp-route/get-review-data-by-id?review_id='+review_id;      
    fetch(url,{
      headers: {                    
        'X-WP-Nonce': saswp_localize_data.nonce,
      }
    }
    )
    .then(res => res.json())
    .then(
      (result) => {  
        setMainSpinner(false);
        console.log(result);
        console.log(result.post_meta);                
        setPostStatus(result.post.post_status);
        setUserInput(result.post_meta);                                  
      },        
      (error) => {
        
      }
    );  

}


  const handleReviewImage =  (data) => {
   
    let image_data = {
      saswp_reviewer_image_id        : data.id,
      saswp_reviewer_image           : data.url,      
      saswp_reviewer_image_detail    : {height: data.height, width: data.width, thumbnail:data.thumbnail}             
    }

    setUserInput(image_data);   
    
  }

  const handleInputChange = evt => {
    let { name, value, type } = evt.target;

    if(type === "checkbox"){
            value = evt.target.checked;
    }
    setUserInput({[name]: value});    
  }
 
const publishPost = (event) => {      
    event.preventDefault();     
    let status = postStatus;
    if(status){
            saveFormData(status); 
    }else{
            saveFormData('publish'); 
    }       
    
}

const handleChangeRating =(rating) => {
      setUserInput({'saswp_review_rating' : rating});
}
const handleDateChange = (date) => {         
     setUserInput({'saswp_review_date' : date});
}
const handlePlatformOption =  () => {

  let element  = [];
  let selected = '';
  if(userInput.saswp_review_platform){
    selected = userInput.saswp_review_platform;
  }
  Object.keys(platformList).map(function(key) {      
      element.push( <option key={key} value={key}>{platformList[key]}</option>);
  });

  if(element){
    return (<select name="saswp_review_platform" onChange={handleInputChange} value={selected}>{element}</select>);
  }else{
    return null;
  }
  
}
    
  useEffect(() => {                               
            getPlatformList();
    if(typeof(page.id)  != 'undefined' ) {             
            getReviewDataById(page.id);    
            setReviewId(page.id);            
    }
    
 }, []);
  
  
  return (
    <div>
      {mainSpinner ? <MainSpinner /> : ''}
      <form encType="multipart/form-data" method="post" id="saswp_review_form">  
      <div className="saswp-single-header">
          <div className="saswp-single-header-left"><h3>{__('Review Setup', 'schema-and-structured-data-for-wp')}</h3></div>
          <div className="saswp-single-header-right"><Link to={`admin.php?page=saswp&path=reviews`}><Icon>close</Icon></Link></div>
      </div>
      <div className="saswp-single-body">
        <div className="card">
          <div className="card-body">
          {__('Review Content', 'schema-and-structured-data-for-wp')}  
          </div>
          <div className="divider-horizontal"></div>
          <div className="card-body">
            <table className="saswp-table">
              <tbody>
                <tr><td>{__('Reviewer Image', 'schema-and-structured-data-for-wp')}</td><td>
                <MediaUpload onSelection={handleReviewImage} src={userInput.saswp_reviewer_image}/>                  
                  </td></tr>    
                <tr><td>{__('Rating', 'schema-and-structured-data-for-wp')}</td><td>

                <StarRatings
                  isAggregateRating={true}
                  rating={Number(userInput.saswp_review_rating)}
                  starRatedColor="#ffd700"
                  starHoverColor="#ffd700"
                  changeRating={handleChangeRating}
                  numberOfStars={5}
                  name='rating'
                  starDimension="30px"
                  starSpacing="5px"
                />

                </td></tr>
                <tr><td>{__('Review Date', 'schema-and-structured-data-for-wp')}</td><td>
                <DatePicker      
                  dateFormat="yyyy-MM-dd"  
                  name="saswp-review-date"
                  selected={new Date(Date.parse(userInput.saswp_review_date))}
                  onChange={handleDateChange}
                />                 
                  </td></tr>
                  <tr><td>{__('Reviewer Name', 'schema-and-structured-data-for-wp')}</td><td><input value={userInput.saswp_reviewer_name} onChange={handleInputChange} type="text" name="saswp_reviewer_name" /></td></tr>
                  <tr><td>{__('Review Text', 'schema-and-structured-data-for-wp')}</td><td>
                  <textarea value={userInput.saswp_review_text} onChange={handleInputChange} name="saswp_review_text" rows="5" />
                 </td></tr>
                <tr><td>{__('Review Link', 'schema-and-structured-data-for-wp')}</td><td>
                  <input value={userInput.saswp_review_link} onChange={handleInputChange} type="text" name="saswp_review_link" />
                </td></tr>
                <tr><td>{__('Review Platform', 'schema-and-structured-data-for-wp')}</td>
                  <td>                    
                    {handlePlatformOption()}                    
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div className="saswp-publish-button">
                                {
                                (postStatus == 'publish' || postStatus == 'draft') ? 
                                <div>{isLoaded ? <a className="btn btn-success" onClick={publishPost}>{__('Update', 'schema-and-structured-data-for-wp')}</a> : <Button success loading>Loading success</Button>}</div>
                                :
                                <div>                                        
                                        <div>{isLoaded ? <a className="btn btn-success" onClick={publishPost}>{__('Update', 'schema-and-structured-data-for-wp')}</a> : <Button success loading>Loading success</Button>}</div>
                                </div>
                                }                                 
        </div>

      </div>
      </form>
    </div>
  )
}
export default ReviewsSingle;