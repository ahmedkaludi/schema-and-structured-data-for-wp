import React, {useState, useEffect, useReducer} from 'react';
import queryString from 'query-string'
import SchemaTypeNavLink from '../schema-type-nav-link/SchemaTypeNavLink'
import {Link} from 'react-router-dom';
import './Schema.scss';
import Select from "react-select";
import { useParams, useLocation, useHistory, useRouteMatch } from 'react-router-dom';
import MediaUpload from './../common/mediaUpload/MediaUpload';

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

        const [metaFields, setMetaFields]                                   = useState([]);  
        const [modifyEntry, setModifyEntry]                                 = useState([]);        
        const [customFieldSearched, setCustomFieldSearched]                 = useState([]);        
        
                      
        const [postMeta, setPostMeta] = useReducer(
                (state, newState) => ({...state, ...newState}),
                {
                        schema_options: {
                                isAccessibleForFree: false,
                                notAccessibleForFree: '',
                                paywall_class_name: '',
                                enable_custom_field: false,
                                saswp_modify_method: 'automatic',
                        },
                        saswp_fixed_text:{},
                        saswp_taxonomy_term:{},
                        saswp_fixed_image:{},
                        saswp_custom_meta_field:{},
                        saswp_meta_list_val:{},      
                }                
              );

        const handleInputChange = evt => {
                let { name, value, type } = evt.target;

                if(type === "checkbox"){
                        value = evt.target.checked;
                }

                switch (name) {

                        case 'enable_custom_field':
                                let data = {enable_custom_field: value};
                                setPostMeta({schema_options: data});
                                break;
                
                        default:
                                setPostMeta({[name]: value});  
                                break;
                }
                              
        }      

        const saveFormData = (status) => {
                      
                setIsLoaded(false);

                let   body_json       = {};
                let   post_meta       = {};
                
                // post_meta.visibility_include = multiTypeIncludedValue;
                // post_meta.visibility_exclude = multiTypeExcludedValue;
                
                let mentry = Object.fromEntries(modifyEntry);

                postMeta.saswp_meta_list_val = mentry;
                postMeta.schema_type  = schemaType;
                post_meta             = postMeta;

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

        const getSchemaDataByType =  (schema_type) => {
                
                let url = saswp_localize_data.rest_url+'saswp-route/get-schema-data-by-type?schema_type='+schema_type;      

                fetch(url,{
                  headers: {                    
                    'X-WP-Nonce': saswp_localize_data.nonce,
                  }
                }
                )
                .then(res => res.json())
                .then(
                  (result) => {                      
                        setMetaFields(result);                                                  
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
                    setPostMeta(result.post_meta);         

                    let entry        = Object.entries(result.post_meta.saswp_meta_list_val);        
                    setModifyEntry(entry);
                              
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
                getSchemaDataByType(page.type);
                if(typeof(page.id)  != 'undefined' ) { 
                        setSchemaID(page.id);                           
                        getSchemaDataById(page.id);             
                }
                
        }, [])

        useEffect(() => {                
              //console.log(postMeta.saswp_meta_list_val);                
        }, [postMeta])

        const handleCustomFieldChange = (i, key, option) => {
                
                let clonedata     = {...postMeta};
                clonedata.saswp_custom_meta_field[key] = option.value; 
                setPostMeta(clonedata);   

        }
        const handleCustomFieldSearch = (q) => {

          
                let url = saswp_localize_data.rest_url + "saswp-route/search-post-meta?search="+q;
      
                fetch(url, {
                        headers: {                    
                        'X-WP-Nonce': saswp_localize_data.nonce,
                        }
                })
                .then(res => res.json())
                .then(
                        (result) => {         
                        
                                if(result.status == 't'){
                                        setCustomFieldSearched(result.data);
                                }
                        
                        },        
                        (error) => {
                        
                        }
                ); 

        } 

        const handleLeftChange = (e) => {

                let { name, value, type } = e.target;

                let data_id = e.currentTarget.dataset.id;
                let clonedata  = [...modifyEntry];
                clonedata[data_id][0] = value;
                clonedata[data_id][1] = "";
                setModifyEntry(clonedata);                

        }
        const handleMiddleChange = (e) => {
                
                let { name, value, type } = e.target;

                let data_id = e.currentTarget.dataset.id;
                let clonedata  = [...modifyEntry];                
                clonedata[data_id][1] = value;
                setModifyEntry(clonedata);                

        }

        const handleRightChange = (e) => {

                let { name, value, type } = e.target;
                let data_id   = e.currentTarget.dataset.id;
                let data_type = e.currentTarget.dataset.type;
                let data_key  = e.currentTarget.dataset.key;
                
                let clonedata     = {...postMeta};

                switch (data_type) {

                        case 'manual_text':
                                clonedata.saswp_fixed_text[data_key] = value;                
                                break;
                        case 'taxonomy_term':
                                clonedata.saswp_taxonomy_term[data_key] = value;                
                                break;        
                        case 'custom_field':
                                
                                break;    
                        case 'fixed_image':
                                
                                break;                                     
                        default:
                                break;
                }
                
                setPostMeta(clonedata);                
                
        }
        const handleFixedImage =(data) => {
                
                let data_key   = data.data_id;                
                let clonedata  = {...postMeta};
                                
                let image_data = {
                        thumbnail: data.url,
                        height: data.height,
                        width: data.width,
                }
                
                clonedata.saswp_fixed_image[data_key] = image_data;

                setPostMeta(clonedata);                

        }

        const firstTd =(key, i) => {

                let first = [];

                if(metaFields.meta_fields){  

                                first.push( 
                                        <select data-id={i} className="saswp-custom-fields-name" key={key} value={key} onChange={handleLeftChange}>   
                                        {
                                                Object.keys(metaFields.meta_fields).map(function(ikey) {                                           
                                                        return ( <option key={ikey} value={ikey}>{metaFields.meta_fields[ikey]}</option>)
                                                })                                                
                                        }                                           
                                         
                                         </select>                                               
                                );
                        
                }
                
                
                return first;
        }
        const secondTd = (key, val, i) => {

                let second = [];
                
                if(metaFields.meta_list_fields){
        
                        let meta_list_arr = metaFields.meta_list_fields.text;

                        if ( key.indexOf('_image') != -1 || key.indexOf('_logo') != -1 ) {
                                meta_list_arr = metaFields.meta_list_fields.image;
                        }

                        second.push(
                                <select data-id={i} className="saswp-custom-meta-list" name={`saswp_meta_list_val[${key}]`} key={key} value={val} onChange={handleMiddleChange}>
                                        {
                                                meta_list_arr.map( (list, index) => {
                        
                                                        return(
                                                                
                                                                <optgroup key={index} label={list['label']}>
                                                                        {
                                                                                Object.keys(list['meta-list']).map(function(ikey) {                                           
                                                                                return ( <option key={ikey} value={ikey}>{list['meta-list'][ikey]}</option>)
                                                                        })         
                                                                        }
                                                                </optgroup>  
                                                                        
                                                        )                                
                        
                                                })
                                        }
                                </select> 
                        )                        
                        
                }
                
               
                return second;
        }
      
        const thirdTd = (key, val, i) => {

                let third = [];

                if(key && val){

                        switch (val) {

                                case 'manual_text':
                                        third.push(<input data-key={key} data-type={val} data-id={i} key={key} type="text" name={`saswp_fixed_text[${key}]`} value={postMeta.saswp_fixed_text[key]} onChange={handleRightChange} />)
                                        break;
                                case 'taxonomy_term':
        
                                        if(metaFields.taxonomies){
                                                third.push(
                                                        <select data-key={key} data-type={val} data-id={i} key={key} name={`saswp_taxonomy_term[${key}]`} value={postMeta.saswp_taxonomy_term[key]} onChange={handleRightChange} >
                                                                {
                                                                        Object.keys(metaFields.taxonomies).map(function(ikey) {                                           
                                                                                return ( <option key={ikey} value={ikey}>{metaFields.taxonomies[ikey]}</option>)
                                                                        })
                                                                                
                                                                }
                                                        </select>
                                                )
                                        }
                                                                                
                                        break;
                                case 'custom_field':
        
                                        let selectval = {
                                                value: postMeta.saswp_custom_meta_field[key],
                                                label: postMeta.saswp_custom_meta_field[key]
                                        };                                        

                                        third.push(
                                                <div key={key} className="saswp-custom-field-div">
                                                <Select        
                                                Clearable ={true} 
                                                isSearchable ={true}     
                                                name={`${key}${val}`}              
                                                value={selectval}
                                                options={customFieldSearched}                                                
                                                onChange={e => handleCustomFieldChange(i,key,e)}
                                                onInputChange={handleCustomFieldSearch}                                     
                                        />
                                        </div>
                                        )
                                
                                        break;
                                case 'fixed_image':
                                        
                                          let image_url = '';
        
                                          if(typeof(postMeta.saswp_fixed_image[key]) != 'undefined'){                                              
                                                        image_url = postMeta.saswp_fixed_image[key].thumbnail;                                              
                                          }
                                          
                                          
                                          third.push(<div key={key}><MediaUpload data_id={key} onSelection={handleFixedImage} src={image_url} /></div>)      
        
                                        break;
                        
                                default:
                                        break;
                        }
                }
                
                return third;

        }

        const handleRemoveTd = (e) => {
                e.preventDefault();
                let index = e.currentTarget.dataset.id;                
                let vals = [...modifyEntry];  
                vals.splice(index,1);
                setModifyEntry(vals);
        }
        const addTr = (e) => {

                e.preventDefault();
                   
                let new_tr = ["", ""];
                                            
                let cloneModify = [...modifyEntry];
                cloneModify.push(new_tr);
                setModifyEntry(cloneModify);                
                console.log(modifyEntry);

        }
        const modifyTr = () => {

                        let result       = [];                
                                                
                        let i = 0;
                        modifyEntry.forEach(([key, val]) => {                                
                                result.push(
                                        <tr key={i}>                                        
                                                <td>{firstTd(key, i)}</td>                                                                                     
                                                <td>{secondTd(key, val, i)}</td>
                                                <td>{thirdTd(key, val, i)}</td>
                                                <td><a data-id={i} onClick={handleRemoveTd} className="btn btn-default saswp-rmv-modify_row">X</a></td>
                                        </tr>
                                );  

                                i++;                         
                          });
                                       
                return result;
        }
        
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
                        {/* <div>
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
                        </div> */}
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
                                               <div className="saswp-global-modify-top">
                                                        <span>Modify Schema Output</span>
                                                         <span><label className="form-check form-group toggle">
                                                        <input checked={postMeta.schema_options.enable_custom_field} onChange={handleInputChange}  name="enable_custom_field" type="checkbox" className="form-check-input" />
                                                        <span className="form-check-label"></span>                                                
                                                        </label>
                                                        </span>
                                                        <span>?</span>
                                               </div>

                                                {
                                                 postMeta.schema_options.enable_custom_field ?
                                                        <div className="saswp-modify-container">
                                                                
                                                                <div className="saswp-dynamic-container">
                                                                        <div className="saswp-custom-fields-div">

                                                                        {(modifyEntry.length > 0) ? 

                                                                        <table className="saswp-custom-fields-table">
                                                                        <tbody>
                                                                                {                                                                                        
                                                                                        modifyTr()
                                                                                }
                                                                                
                                                                        </tbody>
                                                                        </table>

                                                                        :''
                                                                        }
                                                                             
                                                                        <div> <a onClick={addTr} className="saswp-add-custom-fields btn btn-primary">Add Property</a> </div>     
                                                                        </div>     
                                                                </div>
                                                        </div>
                                                 : ''       
                                                }                                                                                               
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