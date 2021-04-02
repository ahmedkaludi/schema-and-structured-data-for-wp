import React, { useState, useEffect, useReducer } from 'react';
import queryString from 'query-string'
import SettingsNavLink from './../settings-nav-link/SettingsNavLink'
import CompatibilityNavLink from './../compatibility-nav-link/CompatibilityNavLink'
import PremiumFeatures from './PremiumFeatures';
import SettingsServices from './SettingsServices';
import SettingsTools from './SettingsTools';
import Migration from './Migration';
import './Settings.scss';
import { Alert } from '@duik/it'
import TranslationPanel from './translationPanel';
import License from './License';
import { Modal } from '@duik/it';
import Select from "react-select";
import { Button } from '@duik/it'
import MainSpinner from './../common/main-spinner/MainSpinner';
import MediaUpload from './../common/mediaUpload/MediaUpload';
import Icon from '@duik/icon'


const Settings = () => {
  
  const page = queryString.parse(window.location.search);   
  const {__} = wp.i18n; 
   
  const [isLoaded, setIsLoaded]               = useState(true);
  const [isSaved, setIsSaved]                 = useState(true);  
  const [mainSpinner, setMainSpinner]         = useState(false);  
  const [partSpinner, setPartSpinner]         = useState(false);  

  const [supportEmail, setSupportEmail]       = useState('');
  const [supportMessage, setSupportMessage]   = useState('');
  const [supportUserType, setSupportUserType] = useState('');
  const [supportError, setSupportError]       = useState('');
  const [supportSuccess, setSupportSuccess]   = useState('');
  const [backupFile, setBackupFile]           = useState(null);

  const [licenseActivationMessage, setLicenseActivationMessage]     = useState([]);

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
  
  const [compatibility, setCompatibility]   = useState([]); 
  const [navMenu, setNavMenu]               = useState([]);  
  const [postTypes, setPostTypes]           = useState([]);  
  const [userRoles, setUserRoles]           = useState([]);  
  const [folderError, setFolderError]       = useState('');  

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
          if(result.nav_menu){
            setNavMenu(result.nav_menu);  
          }
          if(result.post_types){
            setPostTypes(result.post_types);  
          }
          if(result.user_roles){
            setUserRoles(result.user_roles);  
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
      let file = evt.target.files[0];
      setBackupFile(file);
    }else if(type === 'select-multiple'){
      const values = [...evt.target.selectedOptions].map(opt => opt.value);
      setUserInput({[name]: values});
    }else{
      
      if(name == 'saswp-stars-post-taype'){
        
        let checkedval = evt.target.checked;
        let clonedata  = {...userInput};
        let index = clonedata['saswp-stars-post-taype'].indexOf(value);
        
        if(checkedval){
          clonedata['saswp-stars-post-taype'].push(value);
        }else{
          if (index !== -1) {
            clonedata['saswp-stars-post-taype'].splice(index, 1);          
          }
        }
        
        setUserInput(clonedata);
        
      } else if (name == 'saswp-resized-image-folder'){
           let checkedval = evt.target.checked;
            if(checkedval){
              let url = saswp_localize_data.rest_url + "saswp-route/create-resized-image-folder";
              fetch(url, {
                method: "post",
                headers: {                    
                  'Accept': 'application/json',
                  'Content-Type': 'application/json',
                  'X-WP-Nonce': saswp_localize_data.nonce,
                }                
              })
              .then(res => res.json())
              .then(
                (result) => {                      

                  if(result.status == 't'){                    
                    setUserInput({[name]: checkedval});
                  }else{
                    setFolderError(result.message);
                  }

                },        
                (error) => {
                  setFolderError(error);
                }
              );
              
            }else{
              setUserInput({[name]: value});
            }

      } else{

        if(type === "checkbox"){
          value = evt.target.checked;
        }
        setUserInput({[name]: value});
      }
      
    }
            
  }

  const handleLicenseActivation = (e) => {

    e.preventDefault();
    
    let add_on = e.currentTarget.dataset.id;
    let url = saswp_localize_data.rest_url + "saswp-route/license_status_check";

    setLicenseActivationMessage([]);

    let cloneLoaded  = [...licenseActivationMessage];            
    cloneLoaded[add_on] = true;
    setLicenseActivationMessage(cloneLoaded);
  
    const body_json       = {};                
    
    let status = 'active';

    if(userInput[add_on+'_addon_license_key_status'] == 'active'){
      status = 'inactive';
    }

    body_json.license_key          = userInput[add_on+'_addon_license_key'];                 
    body_json.license_status       = status;                 
    body_json.add_on               = add_on;                
         
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
        clonedata[add_on+'_addon_license_key_message'] = result.message;
        clonedata[add_on+'_addon_license_key_status']  = result.status;            
        setUserInput(clonedata);            

        let cloneLoaded  = [...licenseActivationMessage];            
            cloneLoaded[add_on] = false;
            setLicenseActivationMessage(cloneLoaded);
      },        
      (error) => {
        setLicenseActivationMessage([]);
      }
    );

}

  const saveSettings = (event) => {                 

    setIsSaved(false);
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
        setIsSaved(true);  
        if(result.file_status){
          location.reload();
        }        
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
                        
              if(type == 'about'){
                setAboutPageList(result.data);                
              }
              if(type == 'contact'){                
                setContactPageList(result.data);                
              }                        
            
          }          
          
        },        
        (error) => {
          
        }
      ); 

}

const handleAboutPageChange = (option) => {    
    setUserInput({ 'sd_about_page': option.value, 'sd_about_page_option': option});     
}

const handleContactPageChange = (option) => {    
    setUserInput({ 'sd_contact_page': option.value, 'sd_contact_page_option': option });    
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
  getPageList('about', '', ''); 
  getPageList('contact', '', '');    
  getSettings();         
}, [])// pass in an empty array as a second argument

  return(
    
    <form encType="multipart/form-data" method="post" id="saswp_settings_form">  
    
    {mainSpinner ? <MainSpinner /> : ''}
    <div className="saswp-top-header">
      <div className="saswp-top-header-left"> 
      <h3><Icon className="saswp-settings-large-icon">settings</Icon>{__('Settings', 'schema-and-structured-data-for-wp')}</h3>
      </div>
      <div>
      {saswp_localize_data.is_pro_active ? '' : <a href="https://structured-data-for-wp.com/pricing/" target="_blank" className="btn btn-success saswp-go-pro">{__('GO PRO', 'schema-and-structured-data-for-wp')}</a>}    
      </div>
      </div>
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
                <h3>{__('General Settings', 'schema-and-structured-data-for-wp')}</h3>
                <p>{__('This is the basic schema', 'schema-and-structured-data-for-wp')}</p>                
              </div>
            </div>
            <div className="divider-horizontal"></div> 
            
            {/* {KnowldegeGraph Starts here} */}

            <div className="card-body saswp-knowledge-card">    
                  {__('Knowldege Graph', 'schema-and-structured-data-for-wp')}

                  <div className="saswp-setup">
                    {userInput['sd_logo']['url'] == '' ? <img className="alert-img" style={{width:'20px'}} src={saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/exclamation-mark.png'}/> : ''}                    
                    <a className="btn btn-default" onClick={handleOpenKnowledgeModal}>{__('Setup', 'schema-and-structured-data-for-wp')}</a>            
                  </div>
                                   
                <Modal
                isOpen={openKnowledgeModal}
                handleClose={handleCloseKnowledgeModal}                  
                >
                <Modal.Header>
                  <Modal.Title>{__('Setup Knowledge Graph', 'schema-and-structured-data-for-wp')}</Modal.Title>
                </Modal.Header>  
                <Modal.Body>
                
                <table className="saswp-knowledge-table">
                  <tbody>
                    <tr>
                      <td>{__('Data Type', 'schema-and-structured-data-for-wp')}</td>
                      <td>
                        <select name="saswp_kb_type" onChange={handleInputChange} value={userInput['saswp_kb_type']}>
                        <option value="">{__('Select an item', 'schema-and-structured-data-for-wp')}</option>
                        <option value="Organization">{__('Organization', 'schema-and-structured-data-for-wp')}</option>
                        <option value="Person">{__('Person', 'schema-and-structured-data-for-wp')}</option>
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
                        <td>{__('Organization Type', 'schema-and-structured-data-for-wp')}</td>
                        <td>
                          <select className="" name="saswp_organization_type" value={userInput['saswp_organization_type']} onChange={handleInputChange}>
                          <option value="">{__('Select (Optional)', 'schema-and-structured-data-for-wp')}</option>
                          <option value="Airline">{__('Airline', 'schema-and-structured-data-for-wp')}</option>
                          <option value="Consortium">{__('Consortium', 'schema-and-structured-data-for-wp')}</option>
                          <option value="Corporation">{__('Corporation', 'schema-and-structured-data-for-wp')}</option>
                          <option value="EducationalOrganization">{__('EducationalOrganization', 'schema-and-structured-data-for-wp')}</option>
                          <option value="GovernmentOrganization">{__('GovernmentOrganization', 'schema-and-structured-data-for-wp')}</option>
                          <option value="LibrarySystem">{__('LibrarySystem', 'schema-and-structured-data-for-wp')}</option>
                          <option value="MedicalOrganization">{__('MedicalOrganization', 'schema-and-structured-data-for-wp')}</option>
                          <option value="NewsMediaOrganization">{__('NewsMediaOrganization', 'schema-and-structured-data-for-wp')}</option>
                          <option value="NGO">{__('NGO', 'schema-and-structured-data-for-wp')}</option>
                          <option value="PerformingGroup">{__('PerformingGroup', 'schema-and-structured-data-for-wp')}</option>
                          <option value="SportsOrganization">{__('SportsOrganization', 'schema-and-structured-data-for-wp')}</option>
                          <option value="WorkersUnion">{__('WorkersUnion', 'schema-and-structured-data-for-wp')}</option>
                          </select>
                          </td>
                    </tr>
                    <tr>
                        <td>{__('Organization Name', 'schema-and-structured-data-for-wp')}</td>
                        <td><input name="sd_name" value={userInput['sd_name']} onChange={handleInputChange} placeholder="Organization Name" type="text" /></td>
                    </tr>
                    <tr>
                        <td>{__('Organization URL', 'schema-and-structured-data-for-wp')}</td>
                        <td><input name="sd_url" value={userInput['sd_url']} onChange={handleInputChange} placeholder="Organization Name" type="text" /></td>
                    </tr>
                    <tr>
                        <td>{__('Contact Type', 'schema-and-structured-data-for-wp')}</td>
                        <td>
                          <select className="" name="saswp_contact_type" value={userInput['saswp_contact_type']} onChange={handleInputChange}>                          
                          <option value="">{__('Select an item', 'schema-and-structured-data-for-wp')}</option>
                          <option value="customer support">{__('Customer Support', 'schema-and-structured-data-for-wp')}</option>
                          <option value="technical support">{__('Technical Support', 'schema-and-structured-data-for-wp')}</option>
                          <option value="billing support">{__('Billing Support', 'schema-and-structured-data-for-wp')}</option>
                          <option value="bill payment">{__('Bill payment', 'schema-and-structured-data-for-wp')}</option>
                          <option value="sales">{__('Sales', 'schema-and-structured-data-for-wp')}</option>
                          <option value="reservations">{__('Reservations', 'schema-and-structured-data-for-wp')}</option>
                          <option value="credit card support">{__('Credit Card Support', 'schema-and-structured-data-for-wp')}</option>
                          <option value="emergency">{__('Emergency', 'schema-and-structured-data-for-wp')}</option>
                          <option value="baggage tracking">{__('Baggage Tracking', 'schema-and-structured-data-for-wp')}</option>
                          <option value="roadside assistance">{__('Roadside Assistance', 'schema-and-structured-data-for-wp')}</option>
                          <option value="package tracking">{__('Package Tracking', 'schema-and-structured-data-for-wp')}</option>
                          </select>
                          </td>
                    </tr>
                    <tr>
                        <td>{__('Contact Number', 'schema-and-structured-data-for-wp')}</td>
                        <td><input name="saswp_kb_telephone" value={userInput['saswp_kb_telephone']} onChange={handleInputChange} placeholder="+1-012-012-0124" type="text" /></td>
                    </tr>
                    <tr>
                        <td>{__('Contact URL', 'schema-and-structured-data-for-wp')}</td>
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
                      <td>{__('Name', 'schema-and-structured-data-for-wp')}</td>
                      <td><input name="sd-person-name" value={userInput['sd-person-name']} onChange={handleInputChange} placeholder="Name" type="text" /></td>
                    </tr>
                    <tr>
                      <td>{__('Job Title', 'schema-and-structured-data-for-wp')}</td>
                      <td><input name="sd-person-job-title" value={userInput['sd-person-job-title']} onChange={handleInputChange} placeholder="Job Title" type="text" /></td>
                    </tr>
                    <tr>
                      <td>{__('Image', 'schema-and-structured-data-for-wp')}</td>
                      <td>
                        <MediaUpload onSelection={handlePersonImage} src={userInput['sd-person-image']['url']}/>
                      </td>
                    </tr>
                    <tr>
                      <td>{__('Phone Number', 'schema-and-structured-data-for-wp')}</td>
                      <td><input name="sd-person-phone-number" value={userInput['sd-person-phone-number']} onChange={handleInputChange} placeholder="+1-012-012-0124" type="text" /></td>
                    </tr>
                    <tr>
                      <td>{__('URL', 'schema-and-structured-data-for-wp')}</td>
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
                      <td>{__('Logo', 'schema-and-structured-data-for-wp')}</td>
                      <td>
                      <MediaUpload onSelection={handleLogo} src={userInput['sd_logo']['url']} />
                      </td>
                      </tr>
                    </tbody></table>
                   : '' 
                }
                
                </Modal.Body>
                <Modal.Footer>
                  <a className="btn btn-success" onClick={handleKnowledgeSave}>{__('OK', 'schema-and-structured-data-for-wp')}</a>
                </Modal.Footer>
              </Modal>     
              </div>  

            {/* {KnowledgeGraph Ends here} */}
            

            {/* {Social Profile Starts here} */}

                <div className="card-body saswp-social-card">    
                   {__('Social Profile', 'schema-and-structured-data-for-wp')}         
                  <a className="btn btn-default saswp-setup" onClick={handleOpenSocialModal}>{__('Setup', 'schema-and-structured-data-for-wp')}</a>            
                 
                <Modal
                isOpen={openSocialModal}
                handleClose={handleCloseSocialModal}                
              >
                <Modal.Header>
                  <Modal.Title>{__('Add Social Profiles', 'schema-and-structured-data-for-wp')}</Modal.Title>
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

                <a className="btn btn-success" onClick={addSocialInput}>{__('Add More', 'schema-and-structured-data-for-wp')}</a>
                  
                </Modal.Body>
                <Modal.Footer>
                  <a className="btn btn-success" onClick={handleSocialSave}>{__('OK', 'schema-and-structured-data-for-wp')}</a>
                </Modal.Footer>
              </Modal>     
              </div>  
            {/* {Social Profile Ends here} */}

            {/* {Default Data Starts here} */}

                <div className="card-body saswp-default-card">    
                    {__('Default Data', 'schema-and-structured-data-for-wp')}  
                  <div className="saswp-setup">
                    {userInput['sd_default_image']['url'] == '' ? <img className="alert-img" style={{width:'20px'}} src={saswp_localize_data.plugin_url+'/admin/assets/js/dist/images/exclamation-mark.png'}/> : ''}                    
                    <a className="btn btn-default" onClick={handleOpenDefaultDataModal}>{__('Setup', 'schema-and-structured-data-for-wp')}</a>
                  </div>  
                  
                <Modal
                isOpen={openDefaultDataModal}
                handleClose={handleCloseDefaultDataModal}                
              >
                <Modal.Header>
                  <Modal.Title>{__('Add Default Data', 'schema-and-structured-data-for-wp')}</Modal.Title>
                </Modal.Header>  
                <Modal.Body>
                                
                <table className="saswp-knowledge-table">
                  <tbody>
                    <tr>
                      <td>{__('Default Image', 'schema-and-structured-data-for-wp')}</td>
                      <td>
                        <MediaUpload onSelection={handleDefaultImage} src={userInput['sd_default_image']['url']}/>                        
                      </td>
                    </tr>
                    {
                      compatibility.map((item, index) => (  
                        (item.active && item.opt_name == 'saswp-woocommerce') ? 
                        <tr>
                          <td>{__('Product Default Review', 'schema-and-structured-data-for-wp')}</td>
                          <td>
                          <label className="form-check form-group toggle">
                            <input name="saswp_default_review" checked={userInput['saswp_default_review'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                            <span className="form-check-label"></span>
                            <p>{__('This option will add a default review to a woocommerce product if reviews are not there', 'schema-and-structured-data-for-wp')}</p>
                          </label>
                          </td>
                        </tr>    
                        :''
                      ))
                    }
                    
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
                <h3>{__('Basic Schema', 'schema-and-structured-data-for-wp')}</h3>
                <p>{__('This is the basic schema', 'schema-and-structured-data-for-wp')}</p>
              </div>
            </div>
            <div className="divider-horizontal"></div> 
            <div className="card-body">
              <div>{__('About Page', 'schema-and-structured-data-for-wp')}
              <Select       
              Clearable ={true} 
              isSearchable ={true}     
              name="sd_about_page"              
              value={userInput['sd_about_page_option']}
              options={aboutPageList}
              onChange={handleAboutPageChange} 
              onInputChange={handleAboutPageSearch}                                     
            />
              </div>
              <div>{__('Contact Page', 'schema-and-structured-data-for-wp')}
              <Select       
              Clearable ={true}   
              isSearchable ={true}    
              name="sd_contact_page"              
              value={userInput['sd_contact_page_option']}
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
                  <input name="saswp_website_schema" checked={userInput['saswp_website_schema'] == 1 ? true:false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('Website Schema (Home)', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('It enables Website schema to homepage', 'schema-and-structured-data-for-wp')}</p>
                </label>
                {
                  userInput['saswp_website_schema'] == 1 ? 
                    <label className="form-check form-group toggle saswp-sub-element">
                      <input name="saswp_search_box_schema" checked={userInput['saswp_search_box_schema'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                      <span className="form-check-label">{__('Sitelinks Search Box', 'schema-and-structured-data-for-wp')}</span>                  
                  </label>
                  : ''
                }
                
                <label className="form-check form-group toggle">
                <input name="saswp_breadcrumb_schema" checked={userInput['saswp_breadcrumb_schema'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('BreadCrumbs', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('It enables Breadcrumb schema globally', 'schema-and-structured-data-for-wp')}</p>
                </label>

                {
                  userInput['saswp_breadcrumb_schema'] == 1 ? 
                    <label className="form-check form-group toggle saswp-sub-element">
                      <input name="saswp_breadcrumb_remove_cat" checked={userInput['saswp_breadcrumb_remove_cat'] == 1 ? true : false } onChange={handleInputChange} type="checkbox" className="form-check-input" />
                      <span className="form-check-label">{__('Exclude Category', 'schema-and-structured-data-for-wp')}</span>                  
                  </label>
                  : ''
                }

                {
                  navMenu ? 
                  <div className="form-group">
                    <label>{__('Navigation Schema', 'schema-and-structured-data-for-wp')}</label>
                    <div>
                    <select name="saswp_site_navigation_menu" onChange={handleInputChange} value={userInput['saswp_site_navigation_menu']}>                        
                        <option value="">{__('Select A Menu', 'schema-and-structured-data-for-wp')}</option>
                        {                          
                        navMenu.map((item, i) => (
                          <option key={i} value={item.id}>{item.name}</option>  
                        ))
                        }                                                
                      </select>                                
                    </div>                                                    
                  </div> 
                  : ''
                }                                 
                </div>

                <div className="form-group-container">
                <label className="form-check form-group toggle">
                <input name="saswp_archive_schema" checked={userInput['saswp_archive_schema'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('Archive Schema', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('It enables collectionPage schema for archive, tag and category', 'schema-and-structured-data-for-wp')}</p>
                </label>   
                 {userInput['saswp_archive_schema'] == 1 ? 
                  <select className="saswp-sub-element" name="saswp_archive_schema_type" onChange={handleInputChange} value={userInput['saswp_archive_schema_type']}>                        
                    <option value="Article">{__('Article', 'schema-and-structured-data-for-wp')}</option>
                    <option value="BlogPosting">{__('BlogPosting', 'schema-and-structured-data-for-wp')}</option>
                    <option value="NewsArticle">{__('NewsArticle', 'schema-and-structured-data-for-wp')}</option>
                    <option value="WebPage">{__('WebPage', 'schema-and-structured-data-for-wp')}</option>
                  </select>                                
                 : ''} 
                        
                <label className="form-check form-group toggle">
                <input name="saswp_comments_schema" checked={userInput['saswp_comments_schema'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('Comments Schema', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('It enables comment schema for the particular post', 'schema-and-structured-data-for-wp')}</p>
                </label>
                
                </div>

              </div>
            </div>
          </div>
                    
          <div className="card">
            <div className="card-body">
              <div>
                <h3>{__('Features', 'schema-and-structured-data-for-wp')}</h3>
                <p>{__('This is the basic schema', 'schema-and-structured-data-for-wp')}</p>
              </div>
            </div>
            <div className="divider-horizontal"></div> 
            <div className="card-body">
              <div className="form-group-container-horizontal">
                  
                <div className="form-group-container">                

                <label className="form-check form-group toggle">
                <input name="saswp-for-amp" checked={userInput['saswp-for-amp'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('AMP Support', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('Using this option, one can enable or disable schema markup on AMP Pages', 'schema-and-structured-data-for-wp')}</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-stars-rating" checked={userInput['saswp-stars-rating'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('Stars Rating', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('This option adds rating field in wordpress default comment box', 'schema-and-structured-data-for-wp')} <a target="_blank" href="https://structured-data-for-wp.com/docs/article/how-to-use-rating-module-in-schema-and-structured-data/">{__('Learn More', 'schema-and-structured-data-for-wp')}</a></p>
                </label>

                 { (userInput['saswp-stars-rating'] == 1 && postTypes) ?
                      postTypes.map((item, i) => (
                        <label key={i} className="form-check form-group toggle saswp-sub-element">
                        <input name="saswp-stars-post-taype" value={item.name} checked={userInput['saswp-stars-post-taype'].includes(item.name) ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                          <span className="form-check-label">{item.label}</span>                        
                        </label>
                      ))
                  : ''}                                 
                </div>

                <div className="form-group-container">

                <label className="form-check form-group toggle">
                <input name="saswp-for-wordpress" checked={userInput['saswp-for-wordpress'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('Non AMP Support', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('Using this option, one can enable or disable schema markup on Non AMP Pages', 'schema-and-structured-data-for-wp')}</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-review-module" checked={userInput['saswp-review-module'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('Rating Box', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('This option enables the review metabox on every post/page.', 'schema-and-structured-data-for-wp')} <a target="blank" href="https://structured-data-for-wp.com/docs/article/how-to-use-rating-module-in-schema-and-structured-data/">{__('Learn More', 'schema-and-structured-data-for-wp')}</a></p>
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
                <h3>{__('3rd Party compatibility', 'schema-and-structured-data-for-wp')}</h3>
                <p>{__('This is description for 3rd party compatibility', 'schema-and-structured-data-for-wp')}</p>
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
                        <input checked={item.status == 1 ? true : false } onChange={handleCompatibilityChange} name={item.opt_name} type="checkbox" className="form-check-input" />
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
                      <input checked={item.status == 1 ? true : false } onChange={handleCompatibilityChange} name={item.opt_name} type="checkbox" className="form-check-input" />
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
                <h3>{__('Advanced Settings', 'schema-and-structured-data-for-wp')}</h3>
                <p>{__('This allows you to enable advance option in schema', 'schema-and-structured-data-for-wp')}</p>
              </div>
            </div>
            <div className="divider-horizontal"></div> 
            <div className="card-body">
              <div className="form-group-container-horizontal">

                <div className="form-group-container">
                <label className="form-check form-group toggle">
                <input name="saswp-defragment" checked={userInput['saswp-defragment'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('Defragment Schema Markup', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('It relates all schema markups on page to a main entity and merge all markup to a single markup.', 'schema-and-structured-data-for-wp')} <a target="_blank" href="https://structured-data-for-wp.com/docs/article/what-is-defragment-schema-markup-and-how-to-add-it/">{__('Learn More', 'schema-and-structured-data-for-wp')}</a></p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-markup-footer" checked={userInput['saswp-markup-footer'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('Add Schema Markup in footer', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('By default schema markup will be added in header section', 'schema-and-structured-data-for-wp')}</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-pretty-print" checked={userInput['saswp-pretty-print'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('Pretty Print Schema Markup', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('By default schema markup will be minified format', 'schema-and-structured-data-for-wp')}</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-microdata-cleanup" checked={userInput['saswp-microdata-cleanup'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('MicroData CleanUp', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('It removes all the microdata generated by third party plugins which cause validation error on google testing tool', 'schema-and-structured-data-for-wp')}</p>
                </label>

                {userRoles.length > 0 ? 
                  <div className="form-group">
                  <label>{__('Role Based Access', 'schema-and-structured-data-for-wp')}</label>
                  <select className="input-group" onChange={handleInputChange} value={userInput['saswp-role-based-access']} name="saswp-role-based-access" multiple>
                    {
                       userRoles.map((item, index) => (                          
                        <option key={index} value={item.value}>{item.label}</option>                        
                      ))
                    }
                  </select>
                  </div>                
                : ''}                
                </div>

                <div className="form-group-container">                

                <label className="form-check form-group toggle">
                <input name="saswp-other-images" checked={userInput['saswp-other-images'] == 1 ? true:false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('Add All Available Images On Post', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('It adds all the available images on a post to schema markup', 'schema-and-structured-data-for-wp')}</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-multiple-size-image" checked={userInput['saswp-multiple-size-image'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('Allow Multiple Size Image Creation', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('According to Google, For best results, multiple high-resolution images with the following aspect ratios: 16x9, 4x3, and 1x1 should be there', 'schema-and-structured-data-for-wp')}</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-rss-feed-image" checked={userInput['saswp-rss-feed-image'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('Add Featured Image in RSS feed', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('Showing images alongside news/blogs if your website or blog appears in Google News', 'schema-and-structured-data-for-wp')}</p>
                </label>

                <label className="form-check form-group toggle">
                <input name="saswp-resized-image-folder" checked={userInput['saswp-resized-image-folder'] == 1 ? true : false} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                  <span className="form-check-label">{__('Resized Images in Separate Folder', 'schema-and-structured-data-for-wp')}</span>
                  <p className="form-check-description">{__('Store all resized images by SASWP in a separate folder "schema-and-structured-data-for-wp" for better management and optimization of images', 'schema-and-structured-data-for-wp')}</p>
                  {folderError ? <p className="form-check-description saswp-error">{folderError}</p> : ''}
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
           <License 
           handleInputChange        = {handleInputChange}
           handleLicenseActivation  = {handleLicenseActivation}
           userInput                = {userInput}
           licenseActivationMessage = {licenseActivationMessage}
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
              <p>{__('If you have any query, please write the query in below box or email us at', 'schema-and-structured-data-for-wp')} <a href="mailto:team@magazine3.com">team@magazine3.com</a>. {__('We will reply to your email address shortly', 'schema-and-structured-data-for-wp')}</p>
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
              <span>{__('Premium Customer ?', 'schema-and-structured-data-for-wp')} </span>
                <select
                value={supportUserType} 
                onChange={event => setSupportUserType(event.target.value)}
                name="saswp_query_premium_cus" 
                >
                <option>{__('Select', 'schema-and-structured-data-for-wp')}</option>
                <option value="yes">{__('Yes', 'schema-and-structured-data-for-wp')}</option>
                <option value="no">{__('No', 'schema-and-structured-data-for-wp')}</option>
              </select>
              </div>
              <div>
              {isLoaded ? <a className="btn btn-success saswp-send-query" onClick={sendSupportQuery}>{__('Send Message', 'schema-and-structured-data-for-wp')}</a> : <Button success loading>Loading success</Button>}
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
            <h2>{__('Frequently Asked Questions.', 'schema-and-structured-data-for-wp')}</h2>
          </div>
          <div className="divider-horizontal"></div> 
          <div className="card-body">

          <h3>{__('1Q) Is there a Documentation Available?', 'schema-and-structured-data-for-wp')}</h3>
          <p>{__('A) The Documentation is always updated and available at', 'schema-and-structured-data-for-wp')}  <a target="_blank" href="http://structured-data-for-wp.com/docs/">http://structured-data-for-wp.com/docs/</a></p>

          <h3>{__('2Q) How can I setup the Schema and Structured data for individual pages and posts?', 'schema-and-structured-data-for-wp')}</h3>
          <p>{__('A) Just with one click on the Structured data option, you will find an add new options window in the structured data option panel. Secondly, you need to write the name of the title where, if you would like to set the individual Page/Post then you can set the Page/Post type equal to the Page/Post(Name).', 'schema-and-structured-data-for-wp')}</p>

          <h3>{__('3Q) How can I check the code whether the structured data is working or not?', 'schema-and-structured-data-for-wp')}</h3>
          <p>{__('A) To check the code, the first step we need to take is to copy the code of a page or post then visit the', 'schema-and-structured-data-for-wp')} <a target="_blank" href="https://search.google.com/test/rich-results">{__('Structured data testing tool', 'schema-and-structured-data-for-wp')}</a>{__('by clicking on code snippet. Once we paste the snippet we can run the test.', 'schema-and-structured-data-for-wp')} </p>

          <h3>{__('4Q) How can I check whether the pages or posts are valid or not?', 'schema-and-structured-data-for-wp')}</h3>
          <p>{__('A) To check the page and post validation, please visit the', 'schema-and-structured-data-for-wp')} <a target="_blank" href="https://search.google.com/test/rich-results">{__('Structured data testing tool', 'schema-and-structured-data-for-wp')}</a> {__('and paste the link of your website. Once we click on run test we can see the result whether the page or post is a valid one or not.', 'schema-and-structured-data-for-wp')}</p>

          <h3>{__('5Q) Where should users contact if they faced any issues?', 'schema-and-structured-data-for-wp')}</h3>
          <p>{__('A) We always welcome all our users to share their issues and get them fixed just with one click to the link', 'schema-and-structured-data-for-wp')} <a href="mailto:team@magazine3.com">team@magazine3.com</a> or <a href="https://structured-data-for-wp.com/contact-us/" target="_blank">{__('Support link', 'schema-and-structured-data-for-wp')}</a></p>
          </div>
          </div>
          </>
        );        
      }
    })()}
    <div className="saswp-save-settings-btn">
      {isSaved ? <Button  success onClick={saveSettingsHandler}>              
        {__('Save Settings', 'schema-and-structured-data-for-wp')}
      </Button> : <Button  success loading>  Loading success</Button>                      
      }
      </div>
    </div>                    
    </div>
    </form>
  )
}
export default Settings;