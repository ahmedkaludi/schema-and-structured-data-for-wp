import React, {useState, useEffect, useReducer} from 'react';
import queryString from 'query-string'
import SchemaTypeNavLink from '../schema-type-nav-link/SchemaTypeNavLink'
import {Link} from 'react-router-dom';
import './Schema.scss';
import Select from "react-select";
import { useParams, useLocation, useHistory, useRouteMatch } from 'react-router-dom';

const SchemaSingle = () => {

        const {__} = wp.i18n; 
        const page = queryString.parse(window.location.search);  
        const history = useHistory();

        const [isLoaded, setIsLoaded]               = useState(true);  
        const [mainSpinner, setMainSpinner]         = useState(false);  
        const [partSpinner, setPartSpinner]         = useState(false); 

        const [currentIncludedConType, setCurrentIncludedConType]           = useState('');
        const [currentExcludedConType, setCurrentExcludedConType]           = useState('');
        const [schemaType, setSchemaType]                                   = useState('');

        const [postStatus, setPostStatus]                                   = useState('');

        const [schemaID, setSchemaID]                                       = useState(null);
        const [includedToggle, setIncludedToggle]                           = useState(false);
        const [excludedToggle, setExcludedToggle]                           = useState(false);
        const [includedRightPlaceholder, setIncludedRightPlaceholder]       = useState('Select Targeting Data');
        const [excludedRightPlaceholder, setExcludedRightPlaceholder]       = useState('Select Targeting Data');
        const [multiTypeIncludedValue, setMultiTypeIncludedValue]           = useState([]);
        const [multiTypeExcludedValue, setMultiTypeExcludedValue]           = useState([]);
        const [multiTypeLeftIncludedValue, setMultiTypeLeftIncludedValue]   = useState([]);
        const [multiTypeRightIncludedValue, setMultiTypeRightIncludedValue] = useState([]);
        const [multiTypeLeftExcludedValue, setMultiTypeLeftExcludedValue]   = useState([]);
        const [multiTypeRightExcludedValue, setMultiTypeRightExcludedValue] = useState([]);
        const [includedDynamicOptions, setIncludedDynamicOptions]           = useState([]);
        const [excludedDynamicOptions, setExcludedDynamicOptions]           = useState([]);

        const [userInput, setUserInput] = useReducer(
                (state, newState) => ({...state, ...newState}),
                {
                firstName: '',
                lastName: '',
                phoneNumber: '',
                }
              );

        const handleInputChange = evt => {
                let { name, value, type } = evt.target;

                if(type === "checkbox"){
                        value = evt.target.checked;
                }
                setUserInput({[name]: value});
                console.log(userInput);
        }      

        const saveFormData = (status) => {
                      
                setIsLoaded(false);

                let   body_json       = {};
                let   post_meta       = {};

                post_meta.schema_type        = schemaType;                
                post_meta.visibility_include = multiTypeIncludedValue;
                post_meta.visibility_exclude = multiTypeExcludedValue;

                body_json.status      = status;                 
                body_json.schema_id   = schemaID;

                body_json.post_meta   = post_meta;                                        

                let url = saswp_localize_data.rest_url + 'saswp-route/update-schema';
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
                        
                        if(result.schema_id){
                            setSchemaID(result.schema_id);    
                            setPostStatus(status);                               
                            let query = '?page='+page.page+'&path='+page.path+'&type='+page.type+'&id='+result.schema_id;
                            let search = location.pathname + query;
                            history.push(search)
                        }                
                                           
                  },        
                  (error) => {                
                  }
                ); 
                
              }

        const getSchemaDataById =  (schema_id) => {

                let url = saswp_localize_data.rest_url+'saswp-route/get-schema-data-by-id?schema_id='+schema_id;      
                fetch(url,{
                  headers: {                    
                    'X-WP-Nonce': saswp_localize_data.nonce,
                  }
                }
                )
                .then(res => res.json())
                .then(
                  (result) => {  
                    
                    setMultiTypeIncludedValue(result.post_meta.visibility_include);
                    setMultiTypeExcludedValue(result.post_meta.visibility_exclude);
                    setPostStatus(result.post.post_status);
                    console.log(result.post);
                              
                  },        
                  (error) => {
                    
                  }
                );  
          
        }

        const draftPost = (event) => {      
                event.preventDefault();            
                let status = postStatus;
                if(status){
                        saveFormData(status); 
                }else{
                        saveFormData('publish'); 
                }
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

        const getConditionMeta = (condition_type, visibility_type, search_param = '') => {

                let url = saswp_localize_data.rest_url +"saswp-route/get-condition-list?condition="+condition_type+'&search='+search_param;
      
                fetch(url, {
                        headers: {                    
                        'X-WP-Nonce': saswp_localize_data.nonce,
                        }
                })
                .then(res => res.json())
                .then(
                        (result) => {      
                                        
                        if(visibility_type == 'include'){                                                                 
                                setMultiTypeRightIncludedValue([]);
                                setIncludedDynamicOptions(result);
                        }

                        if(visibility_type == 'exclude' || visibility_type){                                                 
                                setMultiTypeRightExcludedValue([]);        
                                setExcludedDynamicOptions(result);
                        }
                        
                        },        
                        (error) => {
                                console.log(error);
                        }
                );  

        }
        const handleMultiIncludedRightChange = (option) => {          
                setMultiTypeRightIncludedValue(option);         
        }
        const handleMultiIncludedSearch = (q) => {
                if(q !== ''){
                        getConditionMeta(currentIncludedConType, 'include', q);  
                }
        }
        const handleIncludedToggle = () => {  
                setIncludedToggle(!includedToggle);
        }        
        const handleMultiIncludedLeftChange = (option) => {   
        
                let placeholder = 'Search for ' + option.label;    
                setCurrentIncludedConType(option.value);
                setIncludedRightPlaceholder(placeholder);
                setIncludedRightPlaceholder(placeholder);
                setMultiTypeLeftIncludedValue(option);
                getConditionMeta(option.value, 'include');          
        }
        const addIncluded = (e) => {

                e.preventDefault();  
            
                let type  = multiTypeLeftIncludedValue;
                let value = multiTypeRightIncludedValue;
              
                if( typeof (value.value) !== 'undefined'){                  
                  let data    = multiTypeIncludedValue;
                  data.push({type: type, value: value});
                  let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);          
                  setMultiTypeIncludedValue(newData);       
                }        
              
            }
        const  removeIncluded = (e) => {
                let index = e.currentTarget.dataset.index;                                  
                const newarr = [ ...multiTypeIncludedValue ];    
                newarr.splice(index,1);
                setMultiTypeIncludedValue(newarr);
        }
        
        

        const handleMultiExcludedRightChange = (option) => {          
                setMultiTypeRightExcludedValue(option);         
        }
        const handleMultiExcludedSearch = (q) => {
                if(q !== ''){
                        getConditionMeta(currentExcludedConType, 'exclude', q);  
                }
        }
        const handleExcludedToggle = () => {  
                setExcludedToggle(!excludedToggle);
        }        
        const handleMultiExcludedLeftChange = (option) => {   
        
                let placeholder = 'Search for ' + option.label;    
                setCurrentExcludedConType(option.value);
                setExcludedRightPlaceholder(placeholder);
                setExcludedRightPlaceholder(placeholder);
                setMultiTypeLeftExcludedValue(option);
                getConditionMeta(option.value, 'exclude');          
        }
        const addExcluded = (e) => {

                e.preventDefault();  
            
                let type  = multiTypeLeftExcludedValue;
                let value = multiTypeRightExcludedValue;
              
                if( typeof (value.value) !== 'undefined'){                  
                  let data    = multiTypeExcludedValue;
                  data.push({type: type, value: value});
                  let newData = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);          
                  setMultiTypeExcludedValue(newData);       
                }        
              
            }
        const  removeExcluded = (e) => {
                let index = e.currentTarget.dataset.index;                                  
                const newarr = [ ...multiTypeExcludedValue ];    
                newarr.splice(index,1);
                setMultiTypeExcludedValue(newarr);
        }
        
        const multiTypeOptions = [
                {label:'Post Type', value:'post_type'},
                {label:'General', value:'general'},
                {label:'Logged in User Type', value:'user_type'},
                {label:'Post', value:'post'},
                {label:'Post Category', value:'post_category'},
                {label:'Post Format', value:'post_format'},
                {label:'Page', value:'page'},
                {label:'Taxonomy Terms', value:'taxonomy'},
                {label:'Tags', value:'tags'}        
              ]

        useEffect(() => {                
                setSchemaType(page.type);                

                if(typeof(page.id)  != 'undefined' ) { 
                        setSchemaID(page.id);   
                        getSchemaDataById(page.id)             
                }
                
        }, [])
        
        return (<>
        <div>
        <form encType="multipart/form-data" method="post" id="saswp_schema_form">  
                <div className="saswp-single-header">
                        <div className="saswp-single-header-left"><h3>{schemaType} Schema Setup</h3></div>
                        <div className="saswp-single-header-right"><Link to={`admin.php?page=saswp`}>X</Link></div>
                        </div>
                <div className="saswp-single-body">

                        <div>
                        <div className="card">
                                <div className="card-body">
                                        <h3>Where do you want to insert?</h3>
                                        <p>Where do you want to insert</p>
                                </div>
                                <div className="divider-horizontal"></div>
                                <div className="card-body">
                                <div className="saswp-user-targeting"> 
       <h2>Included On <a onClick={handleIncludedToggle}>+</a>  </h2>                
             <div className="saswp-place-list">
              {                
              multiTypeIncludedValue ? 
              multiTypeIncludedValue.map( (item, index) => (
                typeof(item.type) !='undefined' ?
                <span key={index} className="saswp-selected-place">{item.type.label} - {item.value.label}<span className="saswp-remove-item" onClick={removeIncluded} data-index={index}>  x</span></span>                                
                :''
               ) )
              :''}
             </div>             
        

        {includedToggle ?
        <div className="saswp-targeting-selection">
        <table className="form-table">
         <tbody>
           <tr>             
           <td>
            <Select              
              name="userTargetingIncludedType"
              placeholder="Select Targeting Type"              
              options= {multiTypeOptions}
              value  = {multiTypeLeftIncludedValue}
              onChange={handleMultiIncludedLeftChange}                                                 
            />             
           </td>
           <td>
            <Select       
              Clearable ={true}      
              name="userTargetingIncludedData"
              placeholder={includedRightPlaceholder}
              value={multiTypeRightIncludedValue}
              options={includedDynamicOptions}
              onChange={handleMultiIncludedRightChange} 
              onInputChange={handleMultiIncludedSearch}                                     
            />             
           </td>
           <td><a onClick={addIncluded} className="btn btn-success">Add</a></td>
           </tr>
         </tbody> 
        </table>
        </div>
        : ''}
       </div>                               

       <div className="saswp-user-targeting"> 
       <h2>Excluded From <a onClick={handleExcludedToggle}>+</a>  </h2>                
             <div className="saswp-place-list">
              {                
              multiTypeExcludedValue ? 
              multiTypeExcludedValue.map( (item, index) => (
                typeof(item.type) !='undefined' ?
                <span key={index} className="saswp-selected-place">{item.type.label} - {item.value.label}<span className="saswp-remove-item" onClick={removeExcluded} data-index={index}>  x</span></span>                                
                :''
               ) )
              :''}
             </div>             
        

        {excludedToggle ?
        <div className="saswp-targeting-selection">
        <table className="form-table">
         <tbody>
           <tr>             
           <td>
            <Select              
              name="userTargetingIncludedType"
              placeholder="Select Targeting Type"              
              options= {multiTypeOptions}
              value  = {multiTypeLeftExcludedValue}
              onChange={handleMultiExcludedLeftChange}                                                 
            />             
           </td>
           <td>
            <Select       
              Clearable ={true}      
              name="userTargetingExcludedData"
              placeholder={excludedRightPlaceholder}
              value={multiTypeRightExcludedValue}
              options={excludedDynamicOptions}
              onChange={handleMultiExcludedRightChange} 
              onInputChange={handleMultiExcludedSearch}                                     
            />             
           </td>
           <td><a onClick={addExcluded} className="btn btn-success">Add</a></td>
           </tr>
         </tbody> 
        </table>
        </div>
        : ''}
       </div>                       
                                </div>
                        </div>        
                        </div>
                        <div>
                                <div className="card">
                                        <div className="card-body">
                                                <h3>Automatically Fetch Data from Plugins</h3>
                                        </div>
                                        <div className="divider-horizontal"></div>
                                        <div className="card-body">
                                                <div className="form-group-container-horizontal">

                                                <div className="form-group-container">
                                                <label className="form-check form-group toggle">
                                                <input name="firstName" value={userInput.firstName} onChange={handleInputChange} type="checkbox" className="form-check-input" />
                                                <span className="form-check-label">The Events Calendar</span>
                                                
                                                </label></div>                
                                                
                                                <div className="form-group-container">
                                                <label className="form-check form-group toggle">
                                                <input type="checkbox" className="form-check-input" />
                                                <span className="form-check-label">Testimonial Pro</span>
                                                
                                                </label></div>                
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <div>
                               <div className="card">
                                       <div className="card-body">
                                               <h3>Advanced Options</h3>
                                       </div>
                                       <div className="divider-horizontal"></div>
                                       <div className="card-body">
                                                <span>ItemList </span>                                                
                                                <span><label className="form-check form-group toggle">
                                                <input type="checkbox" className="form-check-input" />
                                                <span className="form-check-label"></span>                                                
                                                </label>
                                                </span>
                                                <span>?</span>
                                                
                                       </div>
                                       <div className="divider-horizontal"></div>
                                       <div className="card-body">
                                                <span>Speakable  </span>                                               
                                                <span><label className="form-check form-group toggle">
                                                <input type="checkbox" className="form-check-input" />
                                                <span className="form-check-label"></span>                                                
                                                </label>
                                                </span>
                                                <span>?</span>
                                                
                                       </div>
                                       <div className="divider-horizontal"></div>
                                       <div className="card-body">
                                                <span>Paywall </span>
                                                <span><label className="form-check form-group toggle">
                                                <input type="checkbox" className="form-check-input" />
                                                <span className="form-check-label"></span>                                                
                                                </label>
                                                </span>
                                                <span>?</span>
                                                
                                       </div>
                                       <div className="divider-horizontal"></div>
                                       <div className="card-body">
                                                <span>Modify Schema Output</span>                                                 
                                                <span><label className="form-check form-group toggle">
                                                <input type="checkbox" className="form-check-input" />
                                                <span className="form-check-label"></span>                                                
                                                </label>
                                                </span>
                                                <span>?</span>
                                       </div>
                                </div> 
                        </div>
                        <div className="saswp-publish-button">
                                {
                                (postStatus == 'publish' || postStatus == 'draft') ? 
                                         <a className="btn btn-success" onClick={publishPost}>Update</a>
                                :
                                <div>
                                        <a className="btn btn-success" onClick={draftPost}>Draft</a>
                                        <a className="btn btn-success" onClick={publishPost}>Publish</a>
                                </div>
                                }                                 
                        </div>

                </div>
                </form>
        </div>
        </>);
}
export default SchemaSingle;