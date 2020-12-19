import React, { useState, useEffect, useReducer } from 'react';
import queryString from 'query-string'
import SettingsNavLink from './../settings-nav-link/SettingsNavLink'
import CompatibilityNavLink from './../compatibility-nav-link/CompatibilityNavLink'
import { BrowserRouter as Router, Switch, Route, Link, matchPath } from 'react-router-dom';
import PremiumFeatures from './PremiumFeatures';
import SettingsServices from './SettingsServices';
import SettingsTools from './SettingsTools';
import Migration from './Migration';
import './Settings.scss';
import { Alert } from '@duik/it'
import TranslationPanel from './translationPanel';
import { Modal } from '@duik/it';
import Select from "react-select";
import { Button } from '@duik/it'
import MainSpinner from './../common/main-spinner/MainSpinner';
import MediaUpload from './../common/mediaUpload/MediaUpload';
import Icon from '@duik/icon'


const App = () => {
  
  const page = queryString.parse(window.location.search);   
  const {__} = wp.i18n; 
   
  const [isLoaded, setIsLoaded]               = useState(true);  
  const [mainSpinner, setMainSpinner]         = useState(false);  
  const [partSpinner, setPartSpinner]         = useState(false);  

  const [supportEmail, setSupportEmail]       = useState('');
  const [supportMessage, setSupportMessage]   = useState('');
  const [supportUserType, setSupportUserType] = useState('');
  const [supportError, setSupportError]       = useState('');
  const [supportSuccess, setSupportSuccess]   = useState('');
  const [backupFile, setBackupFile]           = useState(null);

  const validateEmail = (email) => {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
  }

  const sendSupportQuery = () =>{
            
    if(supportEmail =='' || supportMessage == '' || supportUserType ==''){

      if(supportEmail == ''){
        setSupportError('Email is required');
      }
      if(supportMessage == ''){
        setSupportError('Message is required');
      }
      if(supportUserType == ''){
        setSupportError('User Type is required');
      }

    }else{

      if(validateEmail(supportEmail)){

        const body_json       = {};                
                
      body_json.message  = supportEmail;                 
      body_json.email    = supportMessage;
      body_json.type     = supportUserType;    
      
      let url = saswp_localize_data.rest_url + 'saswp-route/send-customer-query';
        setIsLoaded(false);
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
            if(result.status == 't'){
              setSupportError('');
              setSupportSuccess('Thank You! Message sent successfully. We will contact you shortly');
            }else{
              setSupportError('Something went wrong. Please check your network connection');
              setSupportSuccess('');
            }                 
                                  
        },        
        (error) => {
          setSupportError('Error' + error);
        }
      );   

      }else{
        setSupportError('Enter a valid email');
      }
               
    }
     
  }

  const closeErrorAlert =(e) =>{
    setSupportError('');
  }

  const closeSuccessAlert =(e) =>{
    setSupportSuccess('');
  }

  const [userInput, setUserInput] = useReducer(
    (state, newState) => ({...state, ...newState}),
    {
        'saswp_kb_type'             : '',    
        'sd_name'                   : '',   
        'sd_alt_name'               : '',
        'sd_url'                    : '',                    
        'sd-person-name'            : '',                                            
        'sd-person-url'             : '',                                                                                                
        'saswp_kb_contact_1'        : 0,                                                                                            
        'saswp-for-wordpress'       : true,                                                                        
        'sd_initial_wizard_status'  : true,
        'saswp-microdata-cleanup'   : true,
        'saswp-other-images'        : true,
        'saswp_default_review'      : true,
        'saswp-multiple-size-image' : true,
        'saswp_social_links'        : [],
        'saswp_kb_type'             : '',
        'sd_about_page'             : '',
        'sd_contact_page'           : '', 
        'sd_default_image'          : {'id':'','url':'','height':'','width':'','thumbnail':''},
        'sd_logo'                   : {'id':'','url':'','height':'','width':'','thumbnail':''},
        'sd-person-image'           : {'id':'','url':'','height':'','width':'','thumbnail':''} 
    }            
  );
  
  const [compatibility, setCompatibility] = useState([]);  

  const getSettings = () => {
    setIsLoaded(false);
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
          if(result.compatibility){
            setCompatibility(result.compatibility);  
          }
          setIsLoaded(true);
          setMainSpinner(false);
        },        
        (error) => {
          // this.setState({
          //   isLoaded: true,
           
          // });
        }
      );            

  }
        
    const handleCompatibilityChange = evt => {

      let { name, value, type } = evt.target;
  
      if(type === "checkbox"){
              value = evt.target.checked;
      }
      
      let new_arr = [];
      
      compatibility.map((item) => {

        if(item.opt_name == name){
          item.status = value;
        }

        new_arr.push(item);

      });
      
       setCompatibility(new_arr);      
      
    }

  const handleInputChange = evt => {

    let { name, value, type } = evt.target;

    if(type === 'file'){
      let file = event.target.files[0];
      setBackupFile(file);
    }else{

      if(type === "checkbox"){
        value = evt.target.checked;
      }

      setUserInput({[name]: value});

    }
            
  }

  const saveSettings = (event) => {                 

    setIsLoaded(false);
    const formData = new FormData();
    
    formData.append("file", backupFile);
    formData.append("settings", JSON.stringify(userInput));
    formData.append("compatibility", JSON.stringify(compatibility));
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
          // if(result.status === 't'){              
          //   if(result.file_status === 't'){               
          //     this.setState({file_uploaded:true,button_spinner_toggle:false});
          //     this.setState({settings_saved:true});
          //   }else{
              
          //     this.setState({settings_saved:true, button_spinner_toggle:false});
          //   }
          // }else{
          //   this.setState({settings_error:result.msg, button_spinner_toggle:false});
          // }                               
      },        
      (error) => {
       
      }
    ); 
    
  }

 const saveSettingsHandler = (e) => {
    e.preventDefault();      
    saveSettings();
  }
 
 const resetSettings = (e) =>  {
  e.preventDefault();
  
  let saswp_confirm = confirm("Are you sure?");

  if(saswp_confirm == true){
    setIsLoaded(false);
    const body_json = {};
    let url = saswp_localize_data.rest_url + 'saswp-route/reset-settings';
    
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
                                  
        },        
        (error) => {
          
        }
      );       

  }

  
 }


 const [openDefaultDataModal, setOpenDefaultDataModal] = useState(false);

 const handleDefaultDataSave = (e) => {
  e.preventDefault();
  setOpenDefaultDataModal(false);
  }

 const handleOpenDefaultDataModal = (e) => {
        setOpenDefaultDataModal(true);
 }
 const handleCloseDefaultDataModal = () => {
      setOpenDefaultDataModal(false);
 }

 const [openKnowledgeModal, setOpenKnowledgeModal] = useState(false);

 const handleKnowledgeSave = (e) => {
  e.preventDefault();
  setOpenKnowledgeModal(false);
  }

 const handleOpenKnowledgeModal = (e) => {
        setOpenKnowledgeModal(true);
 }
 const handleCloseKnowledgeModal = () => {
      setOpenKnowledgeModal(false);
 }

 const [openSocialModal, setOpenSocialModal] = useState(false);

 const handleSocialSave = (e) => {
  e.preventDefault();
  setOpenSocialModal(false);
  }

 const handleOpenSocialModal = () => {
        setOpenSocialModal(true);
 }
 const handleCloseSocialModal = () => {
      setOpenSocialModal(false);
 }
 
const handleSocialChange = (event) => {
  
  let data_id = event.currentTarget.dataset.id;

  let social = [...userInput['saswp_social_links']];
  social[data_id] = event.target.value;
  setUserInput({ 'saswp_social_links': social });
}

const addSocialInput = () => {
  
  setUserInput({ 'saswp_social_links': [...userInput['saswp_social_links'], '']})
}

const removeSocialClick = index => {
  
  let vals = [...userInput['saswp_social_links']];  
  vals.splice(index,1);
  setUserInput({ 'saswp_social_links': vals });
}


const handleDefaultImage =(data) =>{
  
  let image = {...userInput['sd_default_image']};
  image['url']       = data.url;
  image['id']        = data.id;
  image['height']    = data.height;
  image['width']     = data.width;
  image['thumbnail'] = data.thumbnail;

  setUserInput({ 'sd_default_image': image });

}

const handlePersonImage =(data) =>{
  
  let image = {...userInput['sd-person-image']};
  image['url']       = data.url;
  image['id']        = data.id;
  image['height']    = data.height;
  image['width']     = data.width;
  image['thumbnail'] = data.thumbnail;

  setUserInput({ 'sd-person-image': image });

}
const handleLogo =(data) =>{

  let image = {...userInput['sd_logo']};  
  image['url']       = data.url;
  image['id']        = data.id;
  image['height']    = data.height;
  image['width']     = data.width;
  image['thumbnail'] = data.thumbnail;

  setUserInput({ 'sd_logo': image });
  
}

const [aboutPageList, setAboutPageList]     = useState([]);
const [contactPageList, setContactPageList] = useState([]);

const [aboutPageValue, setAboutPageValue] = useState({});
const [contactPageValue, setContactPageValue] = useState({});

const getPageList = (type, search, id) => {
  
  setIsLoaded(false);
  let url = saswp_localize_data.rest_url + "saswp-route/get-page-list?search="+search+"&id="+id;
      
      fetch(url, {
        headers: {                    
          'X-WP-Nonce': saswp_localize_data.nonce,
        }
      })
      .then(res => res.json())
      .then(
        (result) => {         
          setIsLoaded(true);
          if(result.status == 't'){
            console.log(result.data);
            if(type === 'about' || type === 'contact'){

              if(type == 'about'){
                
                setAboutPageList(result.data);

                if(id !== ''){
                  setAboutPageValue(result.data);
                }

              }
              if(type == 'contact'){
                
                setContactPageList(result.data);

                if(id !== ''){
                  setContactPageValue(result.data);
                }
              }

            }else{
              setAboutPageList(result.data);
              setContactPageList(result.data);
            }            
            
          }          
          
        },        
        (error) => {
          
        }
      ); 

}

const handleAboutPageChange = (option) => {

    setAboutPageValue(option);
    setUserInput({ 'sd_about_page': option.value });
     
}

const handleContactPageChange = (option) => {
    setContactPageValue(option);              
    setUserInput({ 'sd_contact_page': option.value });    
}

const handleAboutPageSearch = (s) => {
  if(s !== ''){
    getPageList('about', s, ''); 
  }
  
}

const handleContactPageSearch = (s) => {
  if(s !==''){
    getPageList('contact', s, '');
  }
  
}


useEffect(() => {
  getSettings();         
}, [])// pass in an empty array as a second argument

useEffect(() => {
  
  if(userInput['sd_about_page'] !==''){
    getPageList('about', '', userInput['sd_about_page']);    
  }else{
    getPageList('about', '', '');    
  }
  if(userInput['sd_contact_page'] !==''){
    getPageList('contact', '', userInput['sd_contact_page']);
  }else{
    getPageList('contact', '', '');
  }  
    
}, [userInput])// pass in an empty array as a second argument



  return(
    
    <form encType="multipart/form-data" method="post" id="saswp_settings_form">  
    
    {mainSpinner ? <MainSpinner /> : ''}
    <div className="saswp-top-header"><div className="saswp-top-header-left"> <h3><Icon className="saswp-settings-large-icon">settings</Icon>Settings</h3></div><div> <a className="btn btn-success saswp-go-pro">GO PRO</a> </div></div>
    <div className="saswp-settings-form-content">          
    <SettingsNavLink />                   
    <div className=""> 
    {(() => {
      
      switch (page.path) {
        case "settings":   return (
          <div className="saswp-settings-setup">
            <div className="card">
            <div className="card-body">
              <div>
                <h3>General Settings</h3>
                <p>This is the basic schema</p>                
              </div>
            </div>
            <div className="divider-horizontal"></div> 
            
            {/* {KnowldegeGraph Starts here} */}

            <div className="card-body saswp-knowledge-card">    
                  Knowldege Graph
                  <a className="btn btn-default saswp-setup" onClick={handleOpenKnowledgeModal}>Setup</a>            
                 
                <Modal
                isOpen={openKnowledgeModal}
                handleClose={handleCloseKnowledgeModal}                  
                >
                <Modal.Header>
                  <Modal.Title>Setup Knowledge Graph</Modal.Title>
                </Modal.Header>  
                <Modal.Body>
                
                <table className="saswp-knowledge-table">
                  <tbody>
                    <tr>
                      <td>Data Type</td>
                      <td>
                        <select name="saswp_kb_type" onChange={handleInputChange} value={userInput['saswp_kb_type']}>
                        <option value="">Select an item</option>
                        <option value="Organization">Organization</option>
                        <option value="Person">Person</option>
                        </select>
                        </td>
                    </tr>
                  </tbody>
                </table>  
                {
                  userInput['saswp_kb_type'] == 'Organization' ? 
                  <table className="saswp-knowledge-org saswp-knowledge-table">
                  <tbody>
                    <tr>
                        <td>Organization Type</td>
                        <td>
                          <select className="" name="saswp_organization_type" value={userInput['saswp_organization_type']} onChange={handleInputChange}>
                          <option value="">Select (Optional)</option>
                          <option value="Airline">Airline</option>
                          <option value="Consortium">Consortium</option>
                          <option value="Corporation">Corporation</option>
                          <option value="EducationalOrganization">EducationalOrganization</option>
                          <option value="GovernmentOrganization">GovernmentOrganization</option>
                          <option value="LibrarySystem">LibrarySystem</option>
                          <option value="MedicalOrganization">MedicalOrganization</option>
                          <option value="NewsMediaOrganization">NewsMediaOrganization</option>
                          <option value="NGO">NGO</option>
                          <option value="PerformingGroup">PerformingGroup</option>
                          <option value="SportsOrganization">SportsOrganization</option>
                          <option value="WorkersUnion">WorkersUnion</option>
                          </select>
                          </td>
                    </tr>
                    <tr>
                        <td>Organization Name</td>
                        <td><input name="sd_name" value={userInput['sd_name']} onChange={handleInputChange} placeholder="Organization Name" type="text" /></td>
                    </tr>
                    <tr>
                        <td>Organization URL</td>
                        <td><input name="sd_url" value={userInput['sd_url']} onChange={handleInputChange} placeholder="Organization Name" type="text" /></td>
                    </tr>
                    <tr>
                        <td>Contact Type</td>
                        <td>
                          <select className="" name="saswp_contact_type" value={userInput['saswp_contact_type']} onChange={handleInputChange}>                          
                          <option value="">Select an item</option>
                          <option value="customer support">Customer Support</option>
                          <option value="technical support">Technical Support</option>
                          <option value="billing support">Billing Support</option>
                          <option value="bill payment">Bill payment</option>
                          <option value="sales">Sales</option>
                          <option value="reservations">Reservations</option>
                          <option value="credit card support">Credit Card Support</option>
                          <option value="emergency">Emergency</option>
                          <option value="baggage tracking">Baggage Tracking</option>
                          <option value="roadside assistance">Roadside Assistance</option>
                          <option value="package tracking">Package Tracking</option>
                          </select>
                          </td>
                    </tr>
                    <tr>
                        <td>Contact Number</td>
                        <td><input name="saswp_kb_telephone" value={userInput['saswp_kb_telephone']} onChange={handleInputChange} placeholder="+1-012-012-0124" type="text" /></td>
                    </tr>
                    <tr>
                        <td>Contact URL</td>
                        <td><input name="saswp_kb_contact_url" value={userInput['saswp_kb_contact_url']} onChange={handleInputChange} placeholder="https://www.example.com/contact" type="text" /></td>
                    </tr>
                    
                  </tbody>
                </table>
                  
                  : ''
                }
                {
                  userInput['saswp_kb_type'] == 'Person' ? 
                  <table className="saswp-knowledge-person saswp-knowledge-table">
                  <tbody>
                    <tr>
                      <td>Name</td>
                      <td><input name="sd-person-name" value={userInput['sd-person-name']} onChange={handleInputChange} placeholder="Name" type="text" /></td>
                    </tr>
                    <tr>
                      <td>Job Title</td>
                      <td><input name="sd-person-job-title" value={userInput['sd-person-job-title']} onChange={handleInputChange} placeholder="Job Title" type="text" /></td>
                    </tr>
                    <tr>
                      <td>Image</td>
                      <td>
                        <MediaUpload onSelection={handlePersonImage} src={userInput['sd-person-image']['url']}/>
                      </td>
                    </tr>
                    <tr>
                      <td>Phone Number</td>
                      <td><input name="sd-person-phone-number" value={userInput['sd-person-phone-number']} onChange={handleInputChange} placeholder="+1-012-012-0124" type="text" /></td>
                    </tr>
                    <tr>
                      <td>URL</td>
                      <td><input name="sd-person-url" value={userInput['sd-person-url']} onChange={handleInputChange} placeholder="https://www.example.com/person" type="text" /></td>
                    </tr>
                  </tbody>
                </table>

                  : ''
                }

                {
                  (userInput['saswp_kb_type'] == 'Person'  || userInput['saswp_kb_type'] == 'Organization') ?
                  <table className="saswp-knowledge-table"><tbody>
                    <tr>
                      <td>Logo</td>
                      <td>
                      <MediaUpload onSelection={handleLogo} src={userInput['sd_logo']['url']} />
                      </td>
                      </tr>
                    </tbody></table>
                   : '' 
                }
                
                </Modal.Body>
                <Modal.Footer>
                  <a className="btn btn-success" onClick={handleKnowledgeSave}>OK</a>
                </Modal.Footer>
              </Modal>     
              </div>  

            {/* {KnowledgeGraph Ends here} */}
            

            {/* {Social Profile Starts here} */}

                <div className="card-body saswp-social-card">    
                  Social Profile          
                  <a className="btn btn-default saswp-setup" onClick={handleOpenSocialModal}>Setup</a>            
                 
                <Modal
                isOpen={openSocialModal}
                handleClose={handleCloseSocialModal}                
              >
                <Modal.Header>
                  <Modal.Title>Add Social Profiles</Modal.Title>
                </Modal.Header>  
                <Modal.Body>
                
                {
                  userInput['saswp_social_links'] ? 

                  userInput['saswp_social_links'].map((item, i) => (  
                    <div className="saswp-social-div" key={i}>
                        <input className="form-control" placeholder="https://www.facebook.com/profile" data-id={i} type="text" value={item||''} onChange={handleSocialChange} />
                        <span onClick={() => removeSocialClick(i)}>X</span>
                      </div>                    
                  ))

                  :''
                }

                <a className="btn btn-success" onClick={addSocialInput}>Add More</a>
                  
                </Modal.Body>
                <Modal.Footer>
                  <a className="btn btn-success" onClick={handleSocialSave}>OK</a>
                </Modal.Footer>
              </Modal>     
              </div>  
            {/* {Social Profile Ends here} */}

            {/* {Default Data Starts here} */}

            <div className="card-body saswp-default-card">    
                  Default Data          
                  <a className="btn btn-default saswp-setup" onClick={handleOpenDefaultDataModal}>Setup</a>            
                 
                <Modal
                isOpen={openDefaultDataModal}
                handleClose={handleCloseDefaultDataModal}                
              >
                <Modal.Header>
                  <Modal.Title>Add DefaultData</Modal.Title>
                </Modal.Header>  
                <Modal.Body>
                                
                <table className="saswp-knowledge-table">
                  <tbody>
                    <tr>
                      <td>Default Image</td>
                      <td>
                        <MediaUpload onSelection={handleDefaultImage} src={userInput['sd_default_image']['url']}/>
                        <p>This option will add a default review to a woocommerce product if reviews are not there</p>
                      </td>
                    </tr>
                    <tr>
                      <td>Product Default Review</td>
                      <td>
                      <label className="form-check form-group toggle">
                        <input name="saswp_default_review" checked={userInput['saswp_default_review']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                        <span className="form-check-label"></span>
                        <p>This option will add a default review to a woocommerce product if reviews are not there</p>
                      </label>
                      </td>
                    </tr>
                  </tbody>
                </table>  
                  
                </Modal.Body>
                <Modal.Footer>
                  <a className="btn btn-success" onClick={handleDefaultDataSave}>OK</a>
                </Modal.Footer>
              </Modal>     
              </div>  

            {/* {Default Data Ends here} */}            
                                    
            </div>
          </div>
        );
        break;
        case "settings_general":   return (
          <div className="saswp-settings-general">
          <div className="card">
            <div className="card-body">
              <div>
                <h3>Basic Schema</h3>
                <p>This is the basic schema</p>
              </div>
            </div>
            <div className="divider-horizontal"></div> 
            <div className="card-body">
              <div>About Page    
              <Select       
              Clearable ={true} 
              isSearchable ={true}     
              name="sd_about_page"              
              value={aboutPageValue}
              options={aboutPageList}
              onChange={handleAboutPageChange} 
              onInputChange={handleAboutPageSearch}                                     
            />
              </div>
              <div>Contact Page  
              <Select       
              Clearable ={true}   
              isSearchable ={true}    
              name="sd_contact_page"              
              value={contactPageValue}
              options={contactPageList}
              onChange={handleContactPageChange} 
              onInputChange={handleContactPageSearch}                                     
            />
              </div>
            </div>
            <div className="card-body">
              <div className="form-group-container-horizontal">

                <div className="form-group-container">
                  
                <label className="form-check form-group toggle">
                  <input name="saswp-website-schema" checked={userInput['saswp-website-schema']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">Website Schema (Home)</span>
                  <p className="form-check-description">Website schema description goes here</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp_breadcrumb_schema" checked={userInput['saswp_breadcrumb_schema']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">BreadCrumbs</span>
                  <p className="form-check-description">Website schema description goes here</p>
                </label>

                {/* <label className="form-check form-group toggle">
                <input name="saswp-website-schema" checked={userInput['saswp-website-schema']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">Navigation Schema</span>
                  <p className="form-check-description">Website schema description goes here</p>
                </label> */}
                </div>

                <div className="form-group-container">
                <label className="form-check form-group toggle">
                <input name="saswp_archive_schema" checked={userInput['saswp_archive_schema']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">Archive Schema</span>
                  <p className="form-check-description">Website schema description goes here</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp_comments_schema" checked={userInput['saswp_comments_schema']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">Comments Schema</span>
                  <p className="form-check-description">Website schema description goes here</p>
                </label>
                
                </div>

              </div>
            </div>
          </div>
          
          

          <div className="card">
            <div className="card-body">
              <div>
                <h3>Features</h3>
                <p>This is the basic schema</p>
              </div>
            </div>
            <div className="divider-horizontal"></div> 
            <div className="card-body">
              <div className="form-group-container-horizontal">

                  

                <div className="form-group-container">                

                <label className="form-check form-group toggle">
                <input name="saswp-for-amp" checked={userInput['saswp-for-amp']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">AMP Support</span>
                  <p className="form-check-description">Using this option, one can enable or disable schema markup on AMP Pages</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-stars-rating" checked={userInput['saswp-stars-rating']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">Stars Rating</span>
                  <p className="form-check-description">This option adds rating field in wordpress default comment box <a target="_blank" href="https://structured-data-for-wp.com/docs/article/how-to-use-rating-module-in-schema-and-structured-data/">Learn More</a></p>
                </label>
                
                </div>

                <div className="form-group-container">

                <label className="form-check form-group toggle">
                <input name="saswp-for-wordpress" checked={userInput['saswp-for-wordpress']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">Non AMP Support</span>
                  <p className="form-check-description">Using this option, one can enable or disable schema markup on Non AMP Pages</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-review-module" checked={userInput['saswp-review-module']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">Rating Box</span>
                  <p className="form-check-description">This option enables the review metabox on every post/page. <a target="blank" href="https://structured-data-for-wp.com/docs/article/how-to-use-rating-module-in-schema-and-structured-data/">Learn More</a></p>
                </label>
                
                </div>

              </div>
            </div>
          </div>
        </div>

        );
        break;
        case "settings_compatibility":   return (
          <div className="saswp-settings-compatibility">
            <div className="card">
              <div className="card-body">
                <h3>3rd Party compatibility</h3>
                <p>This is description for 3rd party compatibility</p>
              </div>
              <div className="divider-horizontal"></div> 
              
              <CompatibilityNavLink />          

              <div className=""> 
              {(() => {
                let current = 'active';
                if(typeof(page.tab)  != 'undefined' ) { 
                  current = page.tab;
                }
                              
                switch (current) {
                  case "active":

                    const active_list =  compatibility.map((item, index) => (  
                        item.active ? 
                        <label key={index} className="form-check form-group toggle">
                        <input checked={item.status} onChange={handleCompatibilityChange} name={item.opt_name} type="checkbox" className="form-check-input" />
                        <span className="form-check-label">{item.name}</span>                      
                        </label>:''
                      ));
                      
                      const active_half = Math.ceil(active_list.length / 2); 
                      const active_firstHalf = active_list.splice(0, active_half)
                      const active_secondHalf = active_list.splice(-active_half)

                    return(
                      <div className="card-body">
                        <div className="form-group-container-horizontal">
                        <div className="form-group-container">
                        {active_firstHalf}
                        </div>
                        <div className="form-group-container">
                        {active_secondHalf}
                        </div>
                        </div>
                      </div>
                    );
                    break;
                  case "all":

                    const list =  compatibility.map((item, index) => (  

                      <label key={index} className="form-check form-group toggle">
                      <input checked={item.status} onChange={handleCompatibilityChange} name={item.opt_name} type="checkbox" className="form-check-input" />
                      <span className="form-check-label">{item.name}</span>                      
                      </label>
                      ));
                    
                      const half = Math.ceil(list.length / 2); 
                      const firstHalf = list.splice(0, half)
                      const secondHalf = list.splice(-half)

                    return(
                      <div className="card-body">
                        <div className="form-group-container-horizontal">
                        <div className="form-group-container">
                        {firstHalf}
                        </div>
                        <div className="form-group-container">
                        {secondHalf}
                        </div>
                      </div>
                      </div>
                    );                    
                  
                }
              })()}
              </div>   
              
            </div>
            </div>  
        );
        break;
        case "settings_advanced":   return (
          <div className="saswp-settings-general">
          <div className="card">
            <div className="card-body">
              <div>
                <h3>Advanced Settings</h3>
                <p>This allows you to enable advance option in schema</p>
              </div>
            </div>
            <div className="divider-horizontal"></div> 
            <div className="card-body">
              <div className="form-group-container-horizontal">

                <div className="form-group-container">
                <label className="form-check form-group toggle">
                <input name="saswp-defragment" checked={userInput['saswp-defragment']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">Defragment Schema Markup</span>
                  <p className="form-check-description">It relates all schema markups on page to a main entity and merge all markup to a single markup. <a target="_blank" href="https://structured-data-for-wp.com/docs/article/what-is-defragment-schema-markup-and-how-to-add-it/">Learn More</a></p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-markup-footer" checked={userInput['saswp-markup-footer']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">Add Schema Markup in footer</span>
                  <p className="form-check-description">By default schema markup will be added in header section</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-pretty-print" checked={userInput['saswp-pretty-print']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">Pretty Print Schema Markup</span>
                  <p className="form-check-description">By default schema markup will be minified format</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-microdata-cleanup" checked={userInput['saswp-microdata-cleanup']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">MicroData CleanUp</span>
                  <p className="form-check-description">It removes all the microdata generated by third party plugins which cause validation error on google testing tool</p>
                </label>

                </div>

                <div className="form-group-container">                

                <label className="form-check form-group toggle">
                <input name="saswp-other-images" checked={userInput['saswp-other-images']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">Add All Available Images On Post</span>
                  <p className="form-check-description">It adds all the available images on a post to schema markup</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-multiple-size-image" checked={userInput['saswp-multiple-size-image']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">Allow Multiple Size Image Creation</span>
                  <p className="form-check-description">According to Google, For best results, multiple high-resolution images with the following aspect ratios: 16x9, 4x3, and 1x1 should be there</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-rss-feed-image" checked={userInput['saswp-rss-feed-image']} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">Add Featured Image in RSS feed</span>
                  <p className="form-check-description">Showing images alongside news/blogs if your website or blog appears in Google News</p>
                </label>
                
                </div>

              </div>
            </div>
          </div>
                    
          <Migration />
          <TranslationPanel 
            handleInputChange={handleInputChange}
            userInput = {userInput}
           />
          
        </div>

        );
        case "settings_tools": return(
          <SettingsTools 
          userInput = {userInput}
          handleInputChange={handleInputChange}
          resetSettings = {resetSettings}
          />
        );
        case "settings_premium": return(
          <PremiumFeatures />
        );
        case "settings_service": return(
          <SettingsServices />
        );
        case "settings_support":   return (
          <>
          <div className="card">
            <div className="card-body">
              <p>If you have any query, please write the query in below box or email us at <a href="mailto:team@ampforwp.com">team@ampforwp.com</a>. We will reply to your email address shortly</p>
            </div>
            <div className="divider-horizontal"></div> 
            <div className="card-body saswp-support-section">
              <div><input 
               value={supportEmail} 
               onChange={event => setSupportEmail(event.target.value)}
               placeholder="email" 
               name="saswp_query_email"
               type="text" 
              /></div>
              <div><textarea               
              value={supportMessage} 
              onChange={event => setSupportMessage(event.target.value)}
              name="saswp_query_message" 
              placeholder="Write your query"
              rows="5" 
              cols="60" 
              /></div>
              <div>
              <span>Premium Customer ? </span>
                <select
                value={supportUserType} 
                onChange={event => setSupportUserType(event.target.value)}
                name="saswp_query_premium_cus" 
                >
                <option>Select</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
              </select>
              </div>
              <div>
              {isLoaded ? <a className="btn btn-success saswp-send-query" onClick={sendSupportQuery}>Send Message</a> : <Button success loading>Loading success</Button>}
              </div>
              {supportSuccess ? 
              <div>
              <Alert
                // leftEl={<Icon>rocket</Icon>}
                rightEl={<button onClick={closeSuccessAlert} data_type="success" aria-label="Close" className="close" data-dismiss="alert" type="button">X</button>}
                success
              >
                {supportSuccess}
              </Alert>
              </div>
              : ''}

              {supportError ? 
              <div>
              <Alert
                // leftEl={<Icon>rocket</Icon>}
                rightEl={<button onClick={closeErrorAlert} data_type="danger" aria-label="Close" className="close" data-dismiss="alert" type="button">X</button>}
                danger
              >
                {supportError}
              </Alert>
              </div>
              : ''}                            
            </div>
          </div>  
          <div className="card">
          <div className="card-body">
            <h2>Frequently Asked Questions.</h2>
          </div>
          <div className="divider-horizontal"></div> 
          <div className="card-body">

          <h3>1Q) Is there a Documentation Available?</h3>
          <p>A) The Documentation is always updated and available at <a target="_blank" href="http://structured-data-for-wp.com/docs/">http://structured-data-for-wp.com/docs/</a></p>

          <h3>2Q) How can I setup the Schema and Structured data for individual pages and posts?</h3>
          <p>A) Just with one click on the Structured data option, you will find an add new options window in the structured data option panel. Secondly, you need to write the name of the title where, if you would like to set the individual Page/Post then you can set the Page/Post type equal to the Page/Post(Name).</p>

          <h3>3Q) How can I check the code whether the structured data is working or not?</h3>
          <p>A) To check the code, the first step we need to take is to copy the code of a page or post then visit the <a target="_blank" href="https://search.google.com/test/rich-results">Structured data testing tool</a> by clicking on code snippet. Once we paste the snippet we can run the test.</p>

          <h3>4Q) How can I check whether the pages or posts are valid or not?</h3>
          <p>A) To check the page and post validation, please visit the <a target="_blank" href="https://search.google.com/test/rich-results">Structured data testing tool</a> and paste the link of your website. Once we click on run test we can see the result whether the page or post is a valid one or not.</p>

          <h3>5Q) Where should users contact if they faced any issues?</h3>
          <p>A) We always welcome all our users to share their issues and get them fixed just with one click to the link <a href="mailto:team@ampforwp.com">team@ampforwp.com</a> or <a href="https://structured-data-for-wp.com/contact-us/" target="_blank">Support link</a></p>
          </div>
          </div>
          </>
        );
        break;
      }
    })()}
    <div className="saswp-save-settings-btn">
      {isLoaded ? <Button  success onClick={saveSettingsHandler}>              
        {__('Save Settings', 'schema-and-structured-data-for-wp')}
      </Button> : <Button  success loading>  Loading success</Button>                      
      }
      </div>
    </div>                    
    </div>
    </form>
  )
}
export default App;