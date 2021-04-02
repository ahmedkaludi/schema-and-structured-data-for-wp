import React from 'react';
import { useState, useReducer, useEffect } from 'react';
import { Button } from '@duik/it'
import DottedSpinner from './../common/dotted-spinner/DottedSpinner';    
import MainSpinner from './../common/main-spinner/MainSpinner';
import './Reviews.scss';
import Icon from '@duik/icon'

const ReviewsFetch = () => {
  
  const platforms = [
    
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/google-1-img.png',
      name: 'Google Reviews' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/shopper-approved-img.png',
      name: 'Shopper Approved' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/agoda-img.png',
      name: 'Agoda' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/airbnb-img.png',
      name: 'Airbnb' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/alternativeto-img.png',
      name: 'AlternativeTo' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/amazon-img.png',
      name: 'Amazon' },
      {image: saswp_localize_data.plugin_url+'admin_section/images/reviews_platform_icon/angies-list-img.png',
      name: 'Angies List' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/aliexpress-img.png',
      name: 'Ali Express' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/appstore-img.png',
      name: 'App Store' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/avvo-img.png',
      name: 'Avvo' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/bbb-img.png',
      name: 'BBB' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/bestbuy-img.png',
      name: 'Bestbuy' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/booking-com-img.png',
      name: 'Booking.com' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/capterra-img.png',
      name: 'Capterra' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/cars-com-img.png',
      name: 'Cars.com' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/cargurus-img.png',
      name: 'Cargurus' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/clutch-img.png',
      name: 'Clutch.com' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/citysearch-img.png',
      name: 'Citysearch' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/consumer-affairs-img.png',
      name: 'Consumer Affairs' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/creditkarma-img.png',
      name: 'CreditKarma' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/customerlobby-img.png',
      name: 'CustomerLobby' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/dealerrater-img.png',
      name: 'DealerRater' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/ebay-img.png',
      name: 'Ebay' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/edmunds-img.png',
      name: 'Edmunds' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/etsy-img.png',
      name: 'Etsy' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/expedia-img.png',
      name: 'Expedia' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/facebook-img.png',
      name: 'Facebook' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/flipkart-img.png',
      name: 'Flipkart' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/foursquare-img.png',
      name: 'Foursquare' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/g2crowd-img.png',
      name: 'G2Crowd' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/gearbest-img.png',
      name: 'Gearbest' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/glassdoor-img.png',
      name: 'Glassdoor' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/healthgrades-img.png',
      name: 'Healthgrades' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/homeadvisor-img.png',
      name: 'HomeAdvisor' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/homestars-img.png',
      name: 'Homestars' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/houzz-img.png',
      name: 'Houzz' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/hotels-com-img.png',
      name: 'Hotels.com' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/hungerstation-img.png',
      name: 'Hungerstation' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/imdb-img.png',
      name: 'Imdb' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/indeed-img.png',
      name: 'Indeed' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/insiderpages-img.png',
      name: 'Insider Pages' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/jet-img.png',
      name: 'Jet' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/lawyers-com-img.png',
      name: 'Lawyers.com' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/lendingtree-img.png',
      name: 'Lending Tree' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/martindale-img.png',
      name: 'Martindale' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/newegg-img.png',
      name: 'Newegg' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/openrice-img.png',
      name: 'OpenRice' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/opentable-img.png',
      name: 'Opentable' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/playstore-img.png',
      name: 'Playstore' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/producthunt-img.png',
      name: 'ProductHunt' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/ratemds-img.png',
      name: 'RateMDs' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/reserveout-img.png',
      name: 'Reserveout' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/rottentomatoes-img.png',
      name: 'Rottentomatoes' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/siftery-img.png',
      name: 'Siftery' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/sitejabber-img.png',
      name: 'Sitejabber' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/softwareadvice-img.png',
      name: 'SoftwareAdvice' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/steam-img.png',
      name: 'Steam' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/talabat-img.png',
      name: 'Talabat' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/theknot-img.png',
      name: 'The Knot' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/thumbtack-img.png',
      name: 'Thumbtack' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/tripadvisor-img.png',
      name: 'TripAdvisor' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/trulia-img.png',
      name: 'Trulia' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/trustedshops-img.png',
      name: 'TrustedShops' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/trustpilot-img.png',
      name: 'Trustpilot' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/trustradius-img.png',
      name: 'TrustRadius' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/vitals-img.png',
      name: 'Vitals' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/walmart-img.png',
      name: 'Walmart' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/weddingwire-img.png',
      name: 'WeddingWire' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/wish-img.png',
      name: 'Wish' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/yelp-img.png',
      name: 'Yelp' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/yellowpages-img.png',
      name: 'Yellow Pages' },  
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/zillow-img.png',
      name: 'Zillow' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/zocdoc-img.png',
      name: 'ZocDoc' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/zomato-img.png',
      name: 'Zomato' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/judge-me-img.png',
      name: 'Judge.me' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/shopify-app-store-img.png',
      name: 'Shopify App Store' },
      {image: saswp_localize_data.plugin_url+'/admin_section/images/reviews_platform_icon/goodreads-img.png',
      name: 'Goodreads' }    
  ];

  const {__} = wp.i18n; 
  
  const [fetchMessage, setFetchMessage]   = useState([]);
  const [fetchLoaded, setFetchLoaded]     = useState([]);

  const [isLoaded, setIsLoaded]               	= useState(true);  
  const [mainSpinner, setMainSpinner]           = useState(false);  
  const [dottedSpinner, setDottedSpinner]       = useState(false);  

  const [platformsList, setPlatformsList]       = useState({});
  const [reviewCSVFile, setReviewCSVFile]       = useState(null);

  const [userInput, setUserInput] = useReducer(
    (state, newState) => ({...state, ...newState}),
    {
        'saswp-google-review'             : '',
        'saswp_google_place_api_key'      : '',
        'saswp_reviews_location_name'     : [],
        'saswp_reviews_location_blocks'   : [],
        'reviews_addon_license_key'       : '',
        'reviews_addon_license_key_status': ''
    }            
  );

  const getPlatformsList = () => {

    setMainSpinner(true);

    let url = saswp_localize_data.rest_url + "saswp-route/get-platforms-list";
      
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': saswp_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {                   
           setPlatformsList(result);                    
           setMainSpinner(false);
        },        
        (error) => {
          
        }
      );            

  }

  const getSettings = () => {
    
    setMainSpinner(true);
    let url = saswp_localize_data.rest_url + "saswp-route/get-settings";
      
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': saswp_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {         
          
          if(result.settings){
            setUserInput(result.settings);
          }
                    
           setMainSpinner(false);
        },        
        (error) => {
          
        }
      );            

  }

  const handleInputChange = evt => {

    let { name, value, type } = evt.target;
                
    if(type === 'file'){
      let file = evt.target.files[0];
      setReviewCSVFile(file);
    }else{

      if(name == 'saswp_reviews_location_name'){
      
        let index = evt.currentTarget.dataset.id;
        let clonedata = {...userInput};
        clonedata['saswp_reviews_location_name'][index] = value;
        setUserInput(clonedata);
      
      }else if(name.includes('reviewsforschema-')){
        
        let index     = evt.currentTarget.dataset.index;
        let platform  = evt.currentTarget.dataset.platform;
            name  = evt.currentTarget.dataset.name;
            
        let clonedata = {...userInput};
            clonedata[platform][index][name] = value;
            setUserInput(clonedata);      
      }else{
        if(type === "checkbox"){
          value = evt.target.checked;
        }
        setUserInput({[name]: value});
      }

    }
                    
  }
  const handleAddPlatformField = (e) => {

    e.preventDefault();
    let platform    = e.currentTarget.dataset.platform;    
    let platform_id = e.currentTarget.dataset.platform_id;    

    let new_obj = {};
    if(platformsList[platform].fields){
      
      platformsList[platform].fields.map( (item, j) => {
        new_obj[item.name] = '';
      })

      let clonedata = {...userInput};      

          if( typeof clonedata[platform_id] == 'undefined'){
            clonedata[platform_id] = [];
            clonedata[platform_id].push(new_obj);
          }else{
            clonedata[platform_id].push(new_obj);
          }
         
         setUserInput(clonedata);                
      
    }
            
  }
  const handleRemovePlatformField = (e) => {
    e.preventDefault();

    let platform = e.currentTarget.dataset.platform;
    let index    = e.currentTarget.dataset.index;

    let clonedata = {...userInput};    
        clonedata[platform].splice(index,1);
        setUserInput(clonedata);                
  }
  const saveSettings = (event) => {                 

    setIsLoaded(false);
    const formData = new FormData();

    formData.append("reviewcsv", reviewCSVFile);     
    formData.append("settings", JSON.stringify(userInput));    
    let url = saswp_localize_data.rest_url + 'saswp-route/update-settings';
    fetch(url,{
      method: "post",
      headers: {
        'Accept': 'application/json', 
        'X-WP-Nonce': saswp_localize_data.nonce,         
      },        
      body: formData
    })
    .then(res => res.json())
    .then(
      (result) => {  
        setIsLoaded(true);          
        if(result.file_status){
          location.reload();
        }
      },        
      (error) => {
       
      }
    ); 
    
  }
  const saveSettingsHandler = (e) =>  {
    e.preventDefault();      
    saveSettings();
  }

  const handleAddLocation = (e) => {
    e.preventDefault();
    let clonedata = {...userInput};
    clonedata['saswp_reviews_location_name'].push('');
    setUserInput(clonedata);
  }
  const handleRemoveLocation = (e) => {

    e.preventDefault();

    let index = e.currentTarget.dataset.id;
    let clonedata = {...userInput};
    clonedata['saswp_reviews_location_name'].splice(index,1);
    setUserInput(clonedata);

  }
  

  const handleFetchProReviews = (e) => {

    let index    = e.currentTarget.dataset.index;
    let platform_id = e.currentTarget.dataset.platform_id;

    console.log(platform_id);
    
    
    
    
  }

  const handleFetchFreeReviews = (e) => {

      e.preventDefault();

      let index    = e.currentTarget.dataset.id;
      let place_id = userInput['saswp_reviews_location_name'][index];
            
      if(place_id){
          setFetchLoaded([]);
          let cloneLoaded  = [...fetchLoaded];            
          cloneLoaded[index] = true;
          setFetchLoaded(cloneLoaded);
        
        let url = saswp_localize_data.rest_url + "saswp-route/fetch-google-free-reviews";
      
        const body_json       = {};                
                
        body_json.location          = place_id;                 
        body_json.g_api             = userInput['saswp_google_place_api_key'];                 
        body_json.blocks            = 5;                
        body_json.premium_status    = 'free';                

        fetch(url, {
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
            
            let clonemessage  = [...fetchMessage];            
            clonemessage[index] = result.message;
            setFetchMessage(clonemessage);

            let cloneLoaded  = [...fetchLoaded];              
            cloneLoaded[index] = false;
            setFetchLoaded(cloneLoaded);
          },        
          (error) => {
            setFetchLoaded([]);
          }
        );            

      }

  }

  const handleLicenseActivation = () => {

        setIsLoaded(false);

        let url = saswp_localize_data.rest_url + "saswp-route/license_status_check";
      
        const body_json       = {};                
        
        let status = 'active';

        if(userInput['reviews_addon_license_key_status'] == 'active'){
          status = 'inactive';
        }

        body_json.license_key          = userInput['reviews_addon_license_key'];                 
        body_json.license_status       = status;                 
        body_json.add_on               = 'reviews';                
             
        fetch(url, {
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
            let clonedata = {...userInput};
            clonedata['reviews_addon_license_key_message'] = result.message;
            clonedata['reviews_addon_license_key_status']  = result.status;            
            setUserInput(clonedata);            
          },        
          (error) => {
            
          }
        );
    
  }

  const reviewsFieldGenerator = (platform, key) => {

    let response = [];

    if(userInput[platform['id']]){

      for(let i = 0; i < userInput[platform['id']].length ; i++){
            
        if(platform['fields']){
            response.push(<tr key={i}>
              {platform['fields'].map( (item, j) => {
                return(<td key={j}><label>{item['label']}</label>
                <input onChange={handleInputChange} type={item['type']} data-id={item['name']} data-platform={platform['id']} data-index={i} data-name={item['name']} name={`reviewsforschema-[${platform['id']}][${i}][${item['name']}]`} value={userInput[platform['id']][i][item['name']]} /></td>);
              })}
            <td><label></label><a data-index={i} data-platform_id={platform['id']} data-platform={key}  className="btn btn-default saswp-fetch-reviews" onClick={handleFetchProReviews}>Fetch</a></td>
            <td><label></label><a onClick={handleRemovePlatformField} data-index={i} data-platform={platform['id']} className="saswp-remove-platform-f btn btn-default">X</a></td>
            <td><span data-platform={key} className="saswp-rv-fetched-msg"></span></td>
          </tr>);
        }
          
      }

    }
    
    return response;

  }

  useEffect(() => {
    getSettings();      
    getPlatformsList();   
  }, [])
    
  return (
    <>    
    {mainSpinner ? <MainSpinner /> : ''}
    <form encType="multipart/form-data" method="post" id="saswp_settings_form">  
    <div className="saswp-fetch-rv-container">
    <div className="card">
      <div className="card-body">
        <table className="form-table saswp-fetch-rv-table">
        <tr>
              <td>{__('Upload Reviews From CSV', 'schema-and-structured-data-for-wp')}</td>
              <td>
                <input type="file" onChange={handleInputChange} name="saswp_upload_rv_csv" id="saswp_upload_rv_csv" multiple="false" accept=".csv" />
                <p>{__('You must follow the format.', 'schema-and-structured-data-for-wp')} <a href={saswp_localize_data.review_csv_format_url}>{__('Click here', 'schema-and-structured-data-for-wp')}</a>  {__('to download the format', 'schema-and-structured-data-for-wp')}</p>
              </td>
        </tr>
        </table>
      </div>

    </div>
    </div>

    {saswp_localize_data.reviewsforschema == 'free' ?
    <div className="saswp-fetch-rv-container">
     <div className="card">
       <div className="card-body">
        <table className="form-table saswp-fetch-rv-table">
          <tbody>            
        <tr>
          <td>{__('Google Review', 'schema-and-structured-data-for-wp')}</td>
          <td><input onChange={handleInputChange} checked={userInput['saswp-google-review'] == 1 ? true : false} type="checkbox" name="saswp-google-review" />
          <p>{__('This option enables the google review section.', 'schema-and-structured-data-for-wp')} <a target="_blank" href="https://structured-data-for-wp.com/docs/article/how-to-fetch-google-reviewfree-version/">{__('Learn More', 'schema-and-structured-data-for-wp')}</a></p>
          {
          userInput['saswp-google-review'] == 1 ? 
          <div>
            <div className="saswp-g-place-api">
              <label>{__('Google place API Key', 'schema-and-structured-data-for-wp')}</label>
              <input name="saswp_google_place_api_key" onChange={handleInputChange} value={userInput['saswp_google_place_api_key']} type="text" />                       
            </div>            
          {
            userInput.saswp_reviews_location_name ? 
            <>
            <table>
              <tbody>
              {userInput.saswp_reviews_location_name.map( (item, i)=> {
                return(<tr key={i}>
                  <td>{__('Place Id', 'schema-and-structured-data-for-wp')}</td>
                  <td>{<input data-id={i} type="text" onChange={handleInputChange} name="saswp_reviews_location_name" value={item}/>}</td>
                  <td>{__('Reviews', 'schema-and-structured-data-for-wp')}</td>
                  <td><input type="text" name="saswp_reviews_location_blocks" defaultValue="5" disabled="disabled" /></td>
                  <td>
                    {fetchLoaded[i]  ? <Button success loading>Loading success</Button>  : <a data-id={i} className="btn btn-success saswp-fetch-g-reviews" onClick={handleFetchFreeReviews}>{__('Fetch', 'schema-and-structured-data-for-wp')}</a>}                    
                  </td>
                  <td><a data-id={i} onClick={handleRemoveLocation} className="saswp-remove-review-item button">X</a></td>
                  <td>{fetchMessage[i] ? <p className="saswp-rv-fetched-msg">{fetchMessage[i]}</p> : ''}</td>
                </tr>)
              })}
            </tbody>
            </table>
            <div>
              <a className="btn" onClick={handleAddLocation}>{__('Add Location', 'schema-and-structured-data-for-wp')}</a>
              <p>
              <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder">{__('Place ID Finder', 'schema-and-structured-data-for-wp')}</a>
              </p>
            </div>  

            </>
            : null
          }
          </div> 
           : null}
          </td>
        </tr>
        </tbody>
        </table>        
        <div className="saswp-quick-links-div">
          <h4>{__('Quick Links', 'schema-and-structured-data-for-wp')}</h4>          
          <p><a target="_blank" href="https://structured-data-for-wp.com/docs/article/how-to-display-reviews-with-collection-feature/">{__('How to show reviews on the website', 'schema-and-structured-data-for-wp')}</a></p>
        </div>
        <div className="saswp-save-settings-btn">
          {isLoaded ?
          <Button  success onClick={saveSettingsHandler}>
          {__('Save Settings', 'schema-and-structured-data-for-wp')}
          </Button>
           : <Button success loading>Loading success</Button>}
          
      </div>
       </div>
       
     </div>
     <div className="card">
      <div className="card-body">
      <h2>{__('Get Your 5 Stars Reviews on Google SERPs', 'schema-and-structured-data-for-wp')}</h2>      
        <p className="saswp_desc">{__('Automatically Fetch your customer reviews from 80+ Platforms and show them on your website with proper schema support.', 'schema-and-structured-data-for-wp')} <a target="_blank" href="https://structured-data-for-wp.com/reviews-for-schema">{__('Learn More...', 'schema-and-structured-data-for-wp')}</a></p>      
        <div className="saswp_cmpny_lst">
        <span className="saswp_lst saswp_avlbl">{__('Integrations Avaliable', 'schema-and-structured-data-for-wp')}</span>
        <ul>
        {platforms.map( (item, i) => {
          return(
          <li key={i}> <img src={item.image} alt={item.name} height="20" width="20" /> <span className="saswp_cmpny">{item.name}</span></li>
          )
        })}          
        </ul>
        </div>
        <div className="saswp-rev-btn">
          <span>{__('With our API service, you can fetch reviews from anywhere you want! and we are always increasing the number of integrations. You can also request for an integration as well.', 'schema-and-structured-data-for-wp')}</span>
          <a target="_blank" href="https://structured-data-for-wp.com/reviews-for-schema">{__('Get The Reviews Addon Now', 'schema-and-structured-data-for-wp')}</a>
        </div>
      </div>
     </div>
     </div> : 
     <div className="saswp-fetch-rv-container">
        <div className="card">
          <div className="card-body">
            <table className="form-table saswp-fetch-rv-table">
              <tbody>
                <tr><td>{__('Reviews Pro API Key', 'schema-and-structured-data-for-wp')}</td><td>                
                  {userInput['reviews_addon_license_key_status'] == 'active' ? <Icon>check</Icon> : <Icon>close</Icon>}
                  <input onChange={handleInputChange} value={userInput['reviews_addon_license_key']} type="text" placeholder="Enter License Key" name="reviews_addon_license_key" id="reviews_addon_license_key" />
                  {isLoaded ? <Button  success onClick={handleLicenseActivation}>
                  {userInput['reviews_addon_license_key_status'] == 'active' ? __('Deactivate', 'schema-and-structured-data-for-wp') : __('Activate', 'schema-and-structured-data-for-wp')}
                  </Button> : <Button  success loading>  Loading success</Button>                      
                  }                
                  </td>
                    <td> {userInput['reviews_addon_license_key_message'] ? userInput['reviews_addon_license_key_message'] : '' } </td>
                  </tr>
              </tbody>
            </table>
          </div>
        </div>

         <div className="card">
           <div className="card-body">
             <div className="saswp_rv_module">
                  { 
                    platformsList ? 

                    Object.keys(platformsList).map(function(key) {
                      
                      return (
                        <div key={key} className={'${platformsList[key]}-section saswp-reviews-list-section'}>
                          <table className="saswp-reviews-list-table">
                            <tbody>
                            <tr>
                            <td><label><strong>{platformsList[key]['label']}</strong>
                              <input type="checkbox" name={`${platformsList[key]['name']}`} onChange={handleInputChange} checked={userInput[platformsList[key]['name']]} /></label>
                            </td>
                            <td>
                            <div className="saswp-reviews-platform-fields">
                              {userInput[platformsList[key]['name']] ? <div><p><strong>{__('Note:', 'schema-and-structured-data-for-wp')}</strong> {__('If the reviews have not been fetched on first instance. We recommend you to fetch again with same reviews count after some time as it may take time to scrap the reviews on server.', 'schema-and-structured-data-for-wp')}</p></div> : ''}
                            {
                              (platformsList[key]['type'] == 'multi' && userInput[platformsList[key]['name']]) ? 
                              <>
                              <table data-platform={key} className="saswp-platform-table">
                                <tbody>
                                  {                                    
                                    reviewsFieldGenerator(platformsList[key], key)                                    
                                  }                                
                                </tbody>
                              </table>
                                <a onClick={handleAddPlatformField} data-platform_id={platformsList[key]['id']} data-platform={key} className="btn btn-default">{__('Add URL', 'schema-and-structured-data-for-wp')}</a>
                                </>
                              : ''
                            }                            

                            </div>
                            </td>
                            </tr>
                            </tbody>
                          </table>
                        </div>
                         )
                    })
                   : null
                  }
             </div>           

          <div className="saswp-quick-links-div">
          <h4>{__('Quick Links', 'schema-and-structured-data-for-wp')}</h4>
          <p><a target="_blank" href="https://structured-data-for-wp.com/docs/article/how-to-display-reviews-with-collection-feature/">{__('How to show reviews on the website', 'schema-and-structured-data-for-wp')}</a></p>
          </div>
          <div className="saswp-save-settings-btn">
            {isLoaded ?
            <Button  success onClick={saveSettingsHandler}>
            {__('Save Settings', 'schema-and-structured-data-for-wp')}
            </Button>
            : <Button success loading>Loading success</Button>}            
          </div>

           </div>
         </div>         
     </div>
     }               
     </form>
    </>
  )
}
export default ReviewsFetch;