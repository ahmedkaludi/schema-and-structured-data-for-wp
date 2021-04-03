import React, {useState, useEffect, useReducer} from 'react';
import queryString from 'query-string'
import {Link} from 'react-router-dom';
import Select from "react-select";
import {useHistory} from 'react-router-dom';
import MainSpinner from './../common/main-spinner/MainSpinner';
import DottedSpinner from './../common/dotted-spinner/DottedSpinner';
import MediaUpload from './../common/mediaUpload/MediaUpload';
import FieldGenerator from './../common/field-generator/FieldGenerator'
import { Modal } from '@duik/it';
import { Button } from '@duik/it'
import Icon from '@duik/icon'
import './Schema.scss';

const SchemaSingle = () => {

        const {__} = wp.i18n; 
        const page = queryString.parse(window.location.search);  
        const history = useHistory();

        const [isLoaded, setIsLoaded]                                       = useState(true);  
        const [mainSpinner, setMainSpinner]                                 = useState(false);  
        const [partSpinner, setPartSpinner]                                 = useState(false); 
        const [schemaType, setSchemaType]                                   = useState('');
        const [postStatus, setPostStatus]                                   = useState('');
        const [schemaID, setSchemaID]                                       = useState(null);
        const [defaultPlacement, setDefaultPlacement]                       = useState({});                                        
        const [metaFields, setMetaFields]                                   = useState([]);  
        const [modifyEntry, setModifyEntry]                                 = useState([]);        
        const [customFieldSearched, setCustomFieldSearched]                 = useState([]);       
        const [addReviewModal, setAddReviewModal]                           = useState(false); 
        const [reviewTabStatus, setReviewTabStatus]                         = useState(0); 
        const [reviewToBeAdded, setReviewToBeAdded]                         = useState([]);
        const [reviewToBeAddedFound, setReviewToBeAddedFound]               = useState([]);
        const [collectionToBeAdded, setCollectionToBeAdded]                 = useState([]);
        const [collectionToBeAddedFound, setCollectionToBeAddedFound]       = useState([]); 
        
        const businessTypeVal   = {
                automotivebusiness              : [                                                                
                        {value:'',label: 'Select Sub Business Type ( optional )'},
                        {value:'autobodyshop',label: 'Auto Body Shop'},
                        {value:'autodealer',label: 'Auto Dealer'},
                        {value:'autopartsstore',label: 'Auto Parts Store'},
                        {value:'autorental',label: 'Auto Rental'},
                        {value:'autorepair',label: 'Auto Repair'},
                        {value:'autowash',label: 'Auto Wash'},
                        {value:'gasstation',label: 'Gas Station'},
                        {value:'motorcycledealer',label: 'Motorcycle Dealer'},
                        {value:'motorcyclerepair',label: 'Motorcycle Repair'}
                ],
                emergencyservice                :[
                        {value:''               ,label: 'Select Sub Business Type ( optional )'},     
                        {value:'firestation'    ,label: 'Fire Station'},
                        {value:'hospital'       ,label: 'Hospital'},
                        {value:'policestation'  ,label: 'Police Station'}
                ],
                entertainmentbusiness           :[
                        {value:''                   ,label: 'Select Sub Business Type ( optional )'},  
                        {value:'adultentertainment' ,label: 'Adult Entertainment'},
                        {value:'amusementpark'      ,label: 'Amusement Park'},
                        {value:'artgallery'         ,label: 'Art Gallery'},
                        {value:'casino'             ,label: 'Casino'},
                        {value:'comedyclub'         ,label: 'Comedy Club'},
                        {value:'movietheater'       ,label: 'Movie Theater'},
                        {value:'nightclub'          ,label: 'Night Club'}
                ],
                financialservice                :[
                        {value:''                   ,label: 'Select Sub Business Type ( optional )'},   
                        {value:'accountingservice'  ,label: 'Accounting Service'},
                        {value:'automatedteller'    ,label: 'Automated Teller'},
                        {value:'bankorcredit_union' ,label: 'Bank Or Credit Union'},
                        {value:'insuranceagency'    ,label: 'Insurance Agency'}
                ],
                foodestablishment               :[
                        {value:''                   ,label: 'Select Sub Business Type ( optional )'},    
                        {value:'bakery'             ,label: 'Bakery'},
                        {value:'barorpub'           ,label: 'Bar Or Pub'},
                        {value:'brewery'            ,label: 'Brewery'},
                        {value:'cafeorcoffee_shop'  ,label: 'Cafe Or Coffee Shop'}, 
                        {value:'fastfoodrestaurant' ,label: 'Fast Food Restaurant'},
                        {value:'icecreamshop'       ,label: 'Ice Cream Shop'},
                        {value:'restaurant'         ,label: 'Restaurant'},
                        {value:'winery'             ,label: 'Winery'}
                ],
                healthandbeautybusiness         :[
                        {value:''             ,label: 'Select Sub Business Type ( optional )'},    
                        {value:'beautysalon'  ,label: 'Beauty Salon'},
                        {value:'dayspa'       ,label: 'DaySpa'},
                        {value:'hairsalon'    ,label: 'Hair Salon'},
                        {value:'healthclub'   ,label: 'Health Club'}, 
                        {value:'nailsalon'    ,label: 'Nail Salon'},
                        {value:'tattooparlor' ,label: 'Tattoo Parlor'}
                ],
                homeandconstructionbusiness     :[
                        {value:''                  ,label: 'Select Sub Business Type ( optional )'},  
                        {value:'electrician'       ,label: 'Electrician'},
                        {value:'generalcontractor' ,label: 'General Contractor'},
                        {value:'hvacbusiness'      ,label: 'HVAC Business'},
                        {value:'locksmith'         ,label: 'Locksmith'},
                        {value:'movingcompany'     ,label: 'Moving Company'},
                        {value:'plumber'           ,label: 'Plumber'},
                        {value:'roofingcontractor' ,label: 'Roofing Contractor'},
                        {value:'housepainter'      ,label: 'House Painter'}
                ],
                legalservice                    :[
                        {value:''         ,label: 'Select Sub Business Type ( optional )'},  
                        {value:'attorney' ,label: 'Attorney'},
                        {value:'notary'   ,label: 'Notary'}
                ],
                lodgingbusiness                 :[
                        {value:''                ,label: 'Select Sub Business Type ( optional )'},  
                        {value:'bedandbreakfast' ,label: 'Bed And Breakfast'},
                        {value:'campground'      ,label: 'Campground'},
                        {value:'hostel'          ,label: 'Hostel'},
                        {value:'hotel'           ,label: 'Hotel'},
                        {value:'motel'           ,label: 'Motel'},
                        {value:'resort'          ,label: 'Resort'}
                ],
                sportsactivitylocation          :[
                        {value:''                    ,label: 'Select Sub Business Type ( optional )'},  
                        {value:'bowlingalley'        ,label: 'Bowling Alley'},
                        {value:'exercisegym'         ,label: 'Exercise Gym'},
                        {value:'golfcourse'          ,label: 'Golf Course'},
                        {value:'healthclub'          ,label: 'Health Club'},
                        {value:'publicswimming_pool' ,label: 'Public Swimming Pool'},
                        {value:'skiresort'           ,label: 'Ski Resort'},
                        {value:'sportsclub'          ,label: 'Sports Club'},
                        {value:'stadiumorarena'      ,label: 'Stadium Or Arena'},
                        {value:'tenniscomplex'       ,label: 'Tennis Complex'}
                ],
                store                           :[
                        {value:''                      ,label: 'Select Sub Business Type ( optional )'},  
                        {value:'autopartsstore'        ,label: 'Auto Parts Store'},
                        {value:'bikestore'             ,label: 'Bike Store'},
                        {value:'bookstore'             ,label: 'Book Store'},
                        {value:'clothingstore'         ,label: 'Clothing Store'},
                        {value:'computerstore'         ,label: 'Computer Store'},
                        {value:'conveniencestore'      ,label: 'Convenience Store'},
                        {value:'departmentstore'       ,label: 'Department Store'},
                        {value:'electronicsstore'      ,label: 'Electronics Store'},
                        {value:'florist'               ,label: 'Florist'},
                        {value:'furniturestore'        ,label: 'Furniture Store'},
                        {value:'gardenstore'           ,label: 'Garden Store'},
                        {value:'grocerystore'          ,label: 'Grocery Store'},
                        {value:'hardwarestore'         ,label: 'Hardware Store'},
                        {value:'hobbyshop'             ,label: 'Hobby Shop'},
                        {value:'homegoodsstore'        ,label: 'HomeGoods Store'},
                        {value:'jewelrystore'          ,label: 'Jewelry Store'},
                        {value:'liquorstore'           ,label: 'Liquor Store'},
                        {value:'mensclothingstore'     ,label: 'Mens Clothing Store'},
                        {value:'mobilephonestore'      ,label: 'Mobile Phone Store'},
                        {value:'movierentalstore'      ,label: 'Movie Rental Store'},
                        {value:'musicstore'            ,label: 'Music Store'},
                        {value:'officeequipmentstore'  ,label: 'Office Equipment Store'},
                        {value:'outletstore'           ,label: 'Outlet Store'},
                        {value:'pawnshop'              ,label: 'Pawn Shop'},
                        {value:'petstore'              ,label: 'Pet Store'},
                        {value:'shoestore'             ,label: 'Shoe Store'},
                        {value:'sportinggoodsstore'    ,label: 'Sporting Goods Store'},
                        {value:'tireshop'              ,label: 'Tire Shop'},
                        {value:'toystore'              ,label: 'Toy Store'},
                        {value:'wholesalestore'        ,label: 'Wholesale Store'}
                ],
                medicalbusiness                 :[
                        {value:''                 ,label: 'Select Sub Business Type ( optional )'},  
                        {value:'Communityhealth'  ,label: 'Community Health'},
                        {value:'dentist'          ,label: 'Dentist'},
                        {value:'dermatology'      ,label: 'Dermatology'},
                        {value:'dietnutrition'    ,label: 'Diet Nutrition'},
                        {value:'emergency'        ,label: 'Emergency'},
                        {value:'geriatric'        ,label: 'Geriatric'},
                        {value:'gynecologic'      ,label: 'Gynecologic'},
                        {value:'medicalclinic'    ,label: 'Medical Clinic'},
                        {value:'midwifery'        ,label: 'Midwifery'},                              
                        {value:'nursing'         ,label: 'Nursing'},
                        {value:'obstetric'       ,label: 'Obstetric'},
                        {value:'oncologic'       ,label: 'Oncologic'},
                        {value:'optician'        ,label: 'Optician'},
                        {value:'optometric'      ,label: 'Optometric'},
                        {value:'otolaryngologic' ,label: 'Otolaryngologic'},
                        {value:'pediatric'       ,label: 'Pediatric'},
                        {value:'pharmacy'        ,label: 'Pharmacy'},
                        {value:'physician'       ,label: 'Physician'},
                        {value:'physiotherapy'   ,label: 'Physiotherapy'},
                        {value:'plasticsurgery'  ,label: 'Plastic Surgery'},
                        {value:'podiatric'       ,label: 'Podiatric'},
                        {value:'primarycare'     ,label: 'Primary Care'},
                        {value:'psychiatric'     ,label: 'Psychiatric'},
                        {value:'publichealth'    ,label: 'Public Health'},
                        {value:'veterinarycare'  ,label: 'VeterinaryCare'}
                ],
        };

        const [businessType, setBusinessType] = useState([
                                                {value:"", label:"Select Business Type (Optional)"},
                                                {value:"animalshelter", label:"Animal Shelter"},
                                                {value:"automotivebusiness", label:"Automotive Business"},
                                                {value:"childcare", label:"ChildCare"},
                                                {value:"dentist", label:"Dentist"},
                                                {value:"drycleaningorlaundry", label:"Dry Cleaning Or Laundry"},
                                                {value:"emergencyservice", label:"Emergency Service"},
                                                {value:"employmentagency", label:"Employment Agency"},
                                                {value:"entertainmentbusiness", label:"Entertainment Business"},
                                                {value:"financialservice", label:"Financial Service"},
                                                {value:"foodestablishment", label:"Food Establishment"},
                                                {value:"governmentoffice", label:"Government Office"},
                                                {value:"healthandbeautybusiness", label:"Health And Beauty Business"},
                                                {value:"homeandconstructionbusiness", label:"Home And Construction Business"},
                                                {value:"internetcafe", label:"Internet Cafe"},
                                                {value:"legalservice", label:"Legal Service"},
                                                {value:"library", label:"Library"},
                                                {value:"lodgingbusiness", label:"Lodging Business"},
                                                {value:"medicalbusiness", label:"Medical Business"},
                                                {value:"professionalservice", label:"Professional Service"},
                                                {value:"radiostation", label:"Radio Station"},
                                                {value:"realestateagent", label:"Real Estate Agent"},
                                                {value:"recyclingcenter", label:"Recycling Center"},
                                                {value:"selfstorage", label:"Self Storage"},
                                                {value:"shoppingcenter", label:"Shopping Center"},
                                                {value:"sportsactivitylocation", label:"Sports Activity Location"},
                                                {value:"store", label:"Store"},
                                                {value:"televisionstation", label:"Television Station"},
                                                {value:"touristinformationcenter", label:"Tourist Information Center"},
                                                {value:"travelagency", label:"Travel Agency"},
                                        ]); 
                              
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
                        data_group_array: {}
                              
                }                
              );
        const [manualFields, setManualFields]      =  useState([]);
        
        const getManualFields = (schemaType, schemaID) => {

                setMainSpinner(true);

                let url = saswp_localize_data.rest_url+'saswp-route/get-manual-fields?schema_id='+schemaID+'&schema_type='+schemaType;      
                
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
                        setManualFields(result);
                },        
                (error) => {
                
                }
                );
        }
        
        const handleBusinessTypeChange = (option)  => {
                
                let clonedata = {...postMeta};                
                clonedata.saswp_business_type = option.value;
                if(businessTypeVal[option.value]){
                        clonedata.saswp_business_name = option.value;
                }else{
                        clonedata.saswp_business_name = '';
                }
                
                setPostMeta(clonedata);

        }
        const handleSubBusinessTypeChange = (option)  => {
                
                let clonedata = {...postMeta};                
                clonedata.saswp_business_name = option.value;
                setPostMeta(clonedata);

        }
        const handleSubBusinessTypeValue = () => {

                let response = [{value:"", label: "Select Sub Business Type (Optional)"}];

                if(postMeta.saswp_business_name && businessTypeVal[postMeta.saswp_business_type]){
                                                                        
                        businessTypeVal[postMeta.saswp_business_type].map( (list) => {

                                if(list.value == postMeta.saswp_business_name){                                                                                
                                        response[0] = list;    
                                }                                

                        })
                }

                return response;

        }
        const handleBusinessTypeValue = ()  => {

                let response = [{value:"", label: "Select Business Type (Optional)"}];

                if(postMeta.saswp_business_type){
                        
                        businessType.map( (list) => {

                                if(list.value == postMeta.saswp_business_type){                                                                                
                                        response[0] = list;    
                                }                                

                        })
                }

                return response;

        }    

        const getCollectionsOnLoad = (offset = null, page = null) => {
                                                        
                let url = saswp_localize_data.rest_url+'saswp-route/get-collections-list?offset='+offset+'&page='+page;      
                setPartSpinner(true);
                fetch(url,{
                        headers: {                    
                        'X-WP-Nonce': saswp_localize_data.nonce,
                        }
                }
                )
                .then(res => res.json())
                .then(
                (result) => {      
                        setPartSpinner(false);
                        setCollectionToBeAdded( (prevState) => ([ ...prevState, ...result.posts_data ]));                                
                        setCollectionToBeAddedFound(result.posts_found)
                },        
                (error) => {
                
                }
                );

        }      
        const getReviewsOnLoad = (offset = null, page = null) =>{
                
                setPartSpinner(true);

                let url = saswp_localize_data.rest_url+'saswp-route/get-reviews-list?offset='+offset+'&page='+page;      

                fetch(url,{
                        headers: {                    
                        'X-WP-Nonce': saswp_localize_data.nonce,
                        }
                }
                )
                .then(res => res.json())
                .then(
                (result) => {                      
                        setPartSpinner(false);
                        setReviewToBeAdded( (prevState) => ([ ...prevState, ...result.posts_data ]));                                
                        setReviewToBeAddedFound(result.posts_found)
                },        
                (error) => {
                
                }
                );

        }
        const handleLoadMoreCollection = (e) => {

                e.preventDefault();
                let offset = collectionToBeAdded.length
                
                if(offset <= collectionToBeAddedFound){
                        let page   = (offset/10)+1;
                        getCollectionsOnLoad(offset, page);
                }
                
        }
        const handleLoadMoreReviews = (e) => {

                e.preventDefault();
                let offset = reviewToBeAdded.length;
                
                if(offset <= reviewToBeAddedFound){
                        let page   = (offset/10)+1;
                        getReviewsOnLoad(offset, page);
                }
                
        }
        const handleCollectionClick = (e) => {                
                
                let review_id = parseInt(e.currentTarget.dataset.id);                                
                let value     = e.target.checked;
                                
                let clonedata = {...postMeta};

                if(value){
                        clonedata.saswp_attached_collection.push(review_id);        
                }else{
                        
                        let index = clonedata.saswp_attached_collection.indexOf(review_id);
                        
                        if (index > -1) {
                                clonedata.saswp_attached_collection.splice(index, 1);
                        }
                }                
                setPostMeta(clonedata);                                
        }
        const handleReviewClick = (e) => {                

                
                let review_id = parseInt(e.currentTarget.dataset.id);                                
                let value     = e.target.checked;
                                
                let clonedata = {...postMeta};

                if(value){
                        clonedata.saswp_attahced_reviews.push(review_id);        
                }else{
                        
                        let index = clonedata.saswp_attahced_reviews.indexOf(review_id);
                        
                        if (index > -1) {
                                clonedata.saswp_attahced_reviews.splice(index, 1);
                        }
                }                
                setPostMeta(clonedata);                                
        }
        const handleAddReviewTab = (e) => {
                e.preventDefault();
                let index = e.currentTarget.dataset.id;
                
                if(index == 0 && reviewToBeAdded.length == 0){
                        getReviewsOnLoad();
                }
                if(index == 1 && collectionToBeAdded.length == 0){                        
                        getCollectionsOnLoad();
                }

                setReviewTabStatus(index);      
        }      
        const handleCloseAddReviewModal = () =>{
                setAddReviewModal(false);
        }      
        const handleOpenAddReviewModal = (e) =>{
                e.preventDefault();
                setAddReviewModal(true);
                getReviewsOnLoad();
        }
        const handlePlaceFourthTdChange = (key_1, key_3, i, k, option) => {
        
                let clonedata     = {...postMeta};                        
                clonedata['data_group_array']['group-'+i]['data_array'][k]['key_4']       = option.value;
                clonedata['data_group_array']['group-'+i]['data_array'][k]['key_4_saved'] = option;                        
                setPostMeta(clonedata);   
        }      
        const getDefaultPlacement = () => {
                setMainSpinner(true);
                let url = saswp_localize_data.rest_url +"saswp-route/get-default-placement";
                fetch(url, {
                        headers: {                    
                        'X-WP-Nonce': saswp_localize_data.nonce,
                        }
                })
                .then(res => res.json())
                .then(
                        (result) => {      
                                setMainSpinner(false);           
                                setDefaultPlacement(result);
                        
                        },        
                        (error) => {
                                console.log(error);
                        }
                );
        }
        const getConditionMeta = (value, group, group_index, index, key_option, key_saved, q='') => {
                
                let url = saswp_localize_data.rest_url +"saswp-route/get-condition-list?condition="+value+'&search='+q;
                
                fetch(url, {
                        headers: {                    
                        'X-WP-Nonce': saswp_localize_data.nonce,
                        }
                })
                .then(res => res.json())
                .then(
                        (result) => {      
                                        
                                let clonedata     = {...postMeta};                        
                                clonedata['data_group_array'][group]['data_array'][index][key_option] = result;
                                clonedata['data_group_array'][group]['data_array'][index][key_saved]   = [{label:'select', value:''}];
                                setPostMeta(clonedata);                                   
                        
                        },        
                        (error) => {
                                console.log(error);
                        }
                );  

        }
        const handleRemovePlacementTr = (e) => {
                e.preventDefault();

                let index       = e.currentTarget.dataset.index;
                let group_index = e.currentTarget.dataset.group_index;
                let clonedata     = {...postMeta};                        

                clonedata['data_group_array']['group-'+group_index]['data_array'].splice(index, 1);
                setPostMeta(clonedata);                                                   

        }
        const handlePlaceThirdTdChange = (key_1, i, k, option) => {
        
                let clonedata     = {...postMeta};                        
                clonedata['data_group_array']['group-'+i]['data_array'][k]['key_3'] = option.value;
                clonedata['data_group_array']['group-'+i]['data_array'][k]['key_3_saved'] = option;                        
                setPostMeta(clonedata);   

                if(key_1 == 'ef_taxonomy'){
                        getConditionMeta(option.value, 'group-'+i, i, k, 'key_4_options', 'key_4_saved', '');
                }
        }
        const handlePlaceThirdTdInputChange = (key_1, i, k, q) => {                
                if(q){
                        getConditionMeta(key_1, 'group-'+i, i, k, 'key_3_options', 'key_3_saved', q);
                }

        }
        const handlePlaceFourthTdInputChange = (key_1, key_3, i, k, q) => {
                if(q){
                        getConditionMeta(key_3, 'group-'+i, i, k, 'key_4_options', 'key_4_saved', q);
                }
        }
        const handlePlacementOr = (e) => {

                e.preventDefault();
                let clonedata     = {...postMeta};                
                let clone_array   = Object.entries(clonedata.data_group_array);
                let new_length    = clone_array.length;
                let data          = JSON.parse(JSON.stringify(defaultPlacement));
                clonedata.data_group_array["group-"+new_length] = data;
                
                setPostMeta(clonedata);                                   

        }
        const handlePlacementAnd = (e) => {

                e.preventDefault();
                
                let group_index = e.currentTarget.dataset.group_index;                
                
                let clonedata     = {...postMeta};                                                                
                let data          = JSON.parse(JSON.stringify(defaultPlacement));
                clonedata.data_group_array["group-"+group_index]['data_array'].push(data['data_array'][0]);                                                
                setPostMeta(clonedata);                                   

        }
        const handleInputChange = evt => {

                let { name, value, type } = evt.target;
                
                if(name.includes('data_group_array')){
                                               
                        let group       = evt.currentTarget.dataset.group;
                        let group_index = evt.currentTarget.dataset.group_index;
                        let index = evt.currentTarget.dataset.index;
                        let key   = evt.currentTarget.dataset.key;

                        if(key == 'key_1'){
                                getConditionMeta(value, group, group_index, index, 'key_3_options', 'key_3_saved', '');
                        }                        
                        
                        let clonedata     = {...postMeta};
                                              
                        clonedata['data_group_array'][group]['data_array'][index][key] = value;

                        if(key == 'key_1' && value == 'date'){
                                clonedata['data_group_array'][group]['data_array'][index]['key_3'] = '';        
                        }
                        
                        setPostMeta(clonedata);   
                        console.log(defaultPlacement);
                }else{

                        if(type === "checkbox"){
                                value = evt.target.checked;
                        }
                        if(name == 'saswp_enable_append_reviews' && value){
                                getReviewsOnLoad();
                                setAddReviewModal(true);                        
                        }

                        switch (name) {

                                case 'isAccessibleForFree':
                                case 'notAccessibleForFree':
                                case 'paywall_class_name':
                                case 'enable_custom_field':
                                case 'saswp_modify_method':
                                        
                                        let clonedata     = {...postMeta};
                                        clonedata.schema_options[name] = value; 
                                        setPostMeta(clonedata);   
        
                                        break;                        
        
                                default:
                                        setPostMeta({[name]: value});  
                                        break;
                        }

                }

                
                              
        }      

        const saveFormData = (status) => {
                      
                setIsLoaded(false);

                let   body_json       = {};
                let   post_meta       = {};                                
                
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
                setMainSpinner(true);        
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
                        setMainSpinner(false);                  
                        setMetaFields(result);                                                  
                  },        
                  (error) => {
                    
                  }
                );  
          
        }      
        const getSchemaDataById =  (schema_id = null) => {
                setMainSpinner(true);
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
                        setMainSpinner(false);             
                    if(result.post){
                        setPostStatus(result.post.post_status);
                    }                    
                    setPostMeta(result.post_meta);         

                    if(result.post_meta.saswp_meta_list_val){
                        let entry        = Object.entries(result.post_meta.saswp_meta_list_val);        
                        setModifyEntry(entry);
                    }
                                                  
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
                if(!clonedata.saswp_fixed_image){
                        clonedata.saswp_fixed_image = {};
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
                                        if(!postMeta.saswp_fixed_text){
                                                postMeta.saswp_fixed_text = {};
                                        }
                                        third.push(<input data-key={key} 
                                                data-type={val} data-id={i}
                                                key={key} type="text"
                                                name={`saswp_fixed_text[${key}]`} 
                                                value={postMeta.saswp_fixed_text[key]} 
                                                onChange={handleRightChange} />)
                                        break;
                                case 'taxonomy_term':
        
                                        if(metaFields.taxonomies){
                                                if(!postMeta.saswp_taxonomy_term){
                                                        postMeta.saswp_taxonomy_term = {};
                                                }
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

        const handleManualFieldImage = (image) => {
                
                let clonedata = {...postMeta};
                
                clonedata[image.data_id]  = {
                        height: image.height,
                        width: image.width,
                        thumbnail: image.thumbnail
                }                
                setPostMeta(clonedata);

        }                                

        useEffect(() => {                
                getDefaultPlacement();
                setSchemaType(page.type);                
                getSchemaDataByType(page.type);                
                if(typeof(page.id)  != 'undefined' ) { 
                        setSchemaID(page.id);                           
                        getSchemaDataById(page.id);             
                }else{
                        getSchemaDataById();             
                }

                if((page.type == 'local_business' || page.type == 'HowTo' || page.type == 'FAQ' && page.id)){
                        getManualFields(page.type, page.id);
                }
                
        }, [])

        useEffect(() => {                         
              
        }, [postMeta])
                
        return (<>
        <div>
        {mainSpinner ? <MainSpinner /> : ''}
        <form encType="multipart/form-data" method="post" id="saswp_schema_form">  
                <div className="saswp-single-header">
                        <div className="saswp-single-header-left">
                                <h3> <Icon className="saswp-edit-schema-icon">edit</Icon> {schemaType} {__('Schema Setup', 'schema-and-structured-data-for-wp')}</h3></div>
                        <div className="saswp-single-header-right"><Link to={`admin.php?page=saswp`}>X</Link></div>
                        </div>
                <div className="saswp-single-body">

                        <div>
                        {schemaType == 'local_business' ? 
                        <div className="card">
                        <div className="card-body">
                        <table className="form-table">
                                <tbody>
                                        <tr>
                                                <td>{__('Business Type', 'schema-and-structured-data-for-wp')}</td>
                                                <td>                                                
                                                <Select       
                                                        Clearable     = {true}      
                                                        name          = "saswp_business_type"                                                                                                                                
                                                        value         = {handleBusinessTypeValue()}
                                                        options       = {businessType}
                                                        onChange      = {handleBusinessTypeChange}                                                           
                                                        />
                                                </td>
                                        </tr>
                                                {postMeta.saswp_business_name ?
                                                <tr>
                                                <td>{__('Sub Business Type', 'schema-and-structured-data-for-wp')}</td>
                                                <td>
                                                <Select       
                                                        Clearable     = {true}
                                                        name          = "saswp_business_name"
                                                        value         = {handleSubBusinessTypeValue()}
                                                        options       = {businessTypeVal[postMeta.saswp_business_name]}
                                                        onChange      = {handleSubBusinessTypeChange}                                         
                                                        />  
                                                </td>
                                                </tr>     
                                :null}                                                  
                                </tbody>
                        </table>      
                        </div>
                        </div>               
                        : ''}                                
                        <div className="card">
                                <div className="card-body">
                                        <h3>{__('Placement', 'schema-and-structured-data-for-wp')}</h3>
                                        <p>{__('Where do you want to insert', 'schema-and-structured-data-for-wp')}</p>
                                </div>                        
                                <div className="divider-horizontal"></div>

                                <div className="card-body">
                                        <div className="saswp-placement-groups">
                                        {
                                                                                                
                                                postMeta.data_group_array ? 
                                                
                                                Object.keys(postMeta.data_group_array).map(function(key, i) {
                                                        return (
                                                                ( typeof(postMeta.data_group_array[key].data_array) != 'undefined' && postMeta.data_group_array[key].data_array.length > 0) ?
                                                                <div key={i} className="saswp-placement-group">
                                                                        {i != 0 ? <span>Or</span> : null}                                                                        
                                                                        
                                                                <table className="saswp-placement-row-table">
                                                                        <tbody>
                                                                                {
                                                                                postMeta.data_group_array[key].data_array.map( (item, k) => {
                                                                                        return(
                                                                                        <tr key={k} className="toclone">

                                                                                                <td>
                                                                                                <select onChange={handleInputChange} value={item.key_1} name={`data_group_array[${key}][data_array][${i}][key_1]`} data-group={key} data-group_index={i} data-index={k} data-key={`key_1`} >
                                                                                                        <optgroup label="Basic">                                                                                                                
                                                                                                                <option value="post_type"> {__('Post Type', 'schema-and-structured-data-for-wp')} </option>                                                                                                                
                                                                                                                <option value="show_globally"> {__('Show Globally', 'schema-and-structured-data-for-wp')} </option>                                                                                                                
                                                                                                                <option value="user_type"> {__('Logged in User Type', 'schema-and-structured-data-for-wp')} </option>                                                                                                                
                                                                                                                <option value="homepage"> {__('Homepage', 'schema-and-structured-data-for-wp')} </option>
                                                                                                        </optgroup>          
                                                                                                        <optgroup label="Post">
                                                                                                                <option value="post"> {__('Post', 'schema-and-structured-data-for-wp')} </option>                                                                                                                
                                                                                                                <option value="post_category"> {__('Post Category', 'schema-and-structured-data-for-wp')} </option>                                                                                                                
                                                                                                                <option value="post_format"> {__('Post Format', 'schema-and-structured-data-for-wp')} </option>
                                                                                                        </optgroup>          
                                                                                                        <optgroup label="Page">                                                                                                                
                                                                                                                <option value="page"> {__('Page', 'schema-and-structured-data-for-wp')} </option>                                                                                                                
                                                                                                                <option value="page_template"> {__('Page Template', 'schema-and-structured-data-for-wp')} </option>
                                                                                                        </optgroup>          
                                                                                                        <optgroup label="Other">                                                                                                                
                                                                                                                <option   value="ef_taxonomy"> {__('Taxonomy (Tag)', 'schema-and-structured-data-for-wp')} </option>
                                                                                                                <option   value="date"> {__('Date', 'schema-and-structured-data-for-wp')} </option>
                                                                                                        </optgroup>               
                                                                                                </select>

                                                                                                </td>
                                                                                                <td>
                                                                                                        {
                                                                                                              item.key_1 == 'date' ? 
                                                                                                                <select onChange={handleInputChange} name={`data_group_array[${key}][data_array][${i}][key_2]`} value={item.key_2} data-group={key} data-group_index={i} data-index={k} data-key={`key_2`}>
                                                                                                                        <option value="before_published">{__('Before Published', 'schema-and-structured-data-for-wp')}</option>        
                                                                                                                        <option value="after_published">{__('After Published', 'schema-and-structured-data-for-wp')}</option>        
                                                                                                                </select>        
                                                                                                               :   
                                                                                                               <select onChange={handleInputChange} name={`data_group_array[${key}][data_array][${i}][key_2]`} value={item.key_2} data-group={key} data-group_index={i} data-index={k} data-key={`key_2`}>
                                                                                                                        <option value="equal">{__('Equal to', 'schema-and-structured-data-for-wp')}</option>        
                                                                                                                        <option value="not_equal">{__('Not Equal to (Exclude)', 'schema-and-structured-data-for-wp')}</option>        
                                                                                                                </select>        
                                                                                                        }                                                                                                
                                                                                                </td>
                                                                                                <td>    
                                                                                                        
                                                                                                 {
                                                                                                         item.key_1 == 'date' ?
                                                                                                         <input type="text" placeholder="2020-12-31" name={`data_group_array[${key}][data_array][${i}][key_3]`} value={item.key_3} onChange={handleInputChange} data-group={key} data-group_index={i} data-index={k} data-key={`key_3`} />
                                                                                                          : 
                                                                                                          <Select
                                                                                                          className="saswp-placement-select2"
                                                                                                          isSearchable ={true}
                                                                                                          name={`data_group_array[${key}][data_array][${i}][key_3]`}
                                                                                                          value={item.key_3_saved}
                                                                                                          options={item.key_3_options}
                                                                                                          onChange={e => handlePlaceThirdTdChange(item.key_1, i, k, e)}
                                                                                                          onInputChange={e => handlePlaceThirdTdInputChange(item.key_1, i, k, e)}
                                                                                                        />

                                                                                                 }       

                                                                                                 {
                                                                                                         (item.key_1 == 'ef_taxonomy') ?
                                                                                                                                                                                                                         
                                                                                                                 <Select
                                                                                                                 className="saswp-placement-select2"
                                                                                                                 isSearchable ={true}
                                                                                                                 name={`data_group_array[${key}][data_array][${i}][key_4]`}
                                                                                                                 value={item.key_4_saved}
                                                                                                                 options={item.key_4_options}
                                                                                                                 onChange={e => handlePlaceFourthTdChange(item.key_1, item.key_3, i, k, e)}
                                                                                                                 onInputChange={e => handlePlaceFourthTdInputChange(item.key_1, item.key_3, i, k, e)}
                                                                                                         />
                                                                                                          : null
                                                                                                 }

                                                                                                </td>
                                                                                                <td><a onClick={handlePlacementAnd} data-group_index={i} data-index={k} className="btn btn-default">{__('AND', 'schema-and-structured-data-for-wp')}</a></td>
                                                                                                <td><a data-group_index={i} data-index={k} onClick={handleRemovePlacementTr}><span className="dashicons dashicons-trash"></span></a></td>
                                                                                        </tr>)
                                                                                })     
                                                                                }
                                                                        </tbody>       
                                                                </table>
                                                                </div>
                                                                 : null
                                                                
                                                                )
                                                }) : null

                                        }
                                        <a onClick={handlePlacementOr} className="btn saswp-or-btn btn-default">{__('OR', 'schema-and-structured-data-for-wp')}</a>
                                        </div>
                                </div>                        

                        </div>        

                        </div>
                        
                        <div>
                               <div className="card">
                                       <div className="card-body">
                                               <h3>{__('Advanced Options', 'schema-and-structured-data-for-wp')}</h3>
                                       </div>

                                       {
                                               (  schemaType == 'Book' 
                                               || schemaType == 'Course' 
                                               || schemaType == 'Organization' 
                                               || schemaType == 'CreativeWorkSeries'
                                               || schemaType == 'MobileApplication'
                                               || schemaType == 'ImageObject'
                                               || schemaType == 'HowTo' 
                                               || schemaType == 'MusicPlaylist' 
                                               || schemaType == 'MusicAlbum'               
                                               || schemaType == 'Recipe'
                                               || schemaType == 'TVSeries'
                                               || schemaType == 'SoftwareApplication'
                                               || schemaType == 'Event'
                                               || schemaType == 'VideoGame'
                                               || schemaType == 'Service'
                                               || schemaType == 'AudioObject'
                                               || schemaType == 'VideoObject'
                                               || schemaType == 'local_business'
                                               || schemaType == 'Product'
                                               || schemaType == 'Review'
                                    
                                               ) ? 
                                               <div>
                                                       <div className="divider-horizontal"></div>
                                       <div className="card-body">
                                                <span>{__('Add Reviews', 'schema-and-structured-data-for-wp')}</span> 
                                                <span><label className="form-check form-group toggle">
                                                <input checked={postMeta.saswp_enable_append_reviews == 1 ? true : false } onChange={handleInputChange}  name="saswp_enable_append_reviews" type="checkbox" className="form-check-input" />
                                                <span className="form-check-label"></span>
                                                </label>
                                                </span>
                                                <span>?</span>
                                                <div>

                                                        <Modal
                                                        isOpen={addReviewModal}
                                                        handleClose={handleCloseAddReviewModal}                  
                                                        >
                                                                <Modal.Header>
                                                                <Modal.Title>{__('Attach reviews to this schema type', 'schema-and-structured-data-for-wp')}</Modal.Title>
                                                                </Modal.Header>  
                                                                                                                                
                                                                <div>
                                                                        <nav className="tabs">
                                                                                <a onClick={handleAddReviewTab} data-id="0" className={reviewTabStatus == 0 ? 'tab-item active' : 'tab-item'} >{__('Reviews', 'schema-and-structured-data-for-wp')}</a>
                                                                                <a onClick={handleAddReviewTab} data-id="1" className={reviewTabStatus == 1 ? 'tab-item active' : 'tab-item'}>{__('Collections', 'schema-and-structured-data-for-wp')}</a>
                                                                                <a onClick={handleAddReviewTab} data-id="2" className={reviewTabStatus == 2 ? 'tab-item active' : 'tab-item'}>{__('Shortcode', 'schema-and-structured-data-for-wp')}</a>
                                                                        </nav>        
                                                                        <div className="card-body">
                                                                                
                                                                                    {reviewTabStatus == 0 ? <div className="saswp-rv-tab-content">

                                                                                        {partSpinner ? <DottedSpinner /> : <>
                                                                                                {
                                                                                                reviewToBeAdded ? 

                                                                                                reviewToBeAdded.map( (list, index) => {
                                                                                                        
                                                                                                        return(                                                                                                                
                                                                                                                <div key={index} className="saswp-add-rv-loop">
                                                                                                                <input data-id={list.post.post_id} onChange={handleReviewClick} checked={ (postMeta.saswp_attahced_reviews && (postMeta.saswp_attahced_reviews).includes(list.post.post_id)) ? true : false } className="saswp-attach-rv-checkbox" type="checkbox" />  <strong> {list.post_meta.saswp_reviewer_name} ( {__('Rating', 'schema-and-structured-data-for-wp')} - {list.post_meta.saswp_review_rating} ) <span className="saswp-g-plus"><img width="25" height="25" src= {list.post_meta.saswp_review_platform_image}/></span></strong>
                                                                                                                </div>
                                                                                                                        
                                                                                                        )                                

                                                                                                })

                                                                                                : '' 
                                                                                        }
                                                                                        </>}
                                                                                        {reviewToBeAddedFound > 10 ? <div><a onClick={handleLoadMoreReviews}>{__('Load More...', 'schema-and-structured-data-for-wp')}</a></div> : ''}
                                                                                        </div> : ''}    
                                                                                
                                                                                
                                                                                     {reviewTabStatus == 1 ? <div className="saswp-rv-tab-content">                                                                                                                                                                        

                                                                                        {partSpinner ? <DottedSpinner /> : <>
                                                                                                {
                                                                                                collectionToBeAdded ? 

                                                                                                collectionToBeAdded.map( (list, index) => {
                                                                                                        
                                                                                                        return(                                                                                                                
                                                                                                                <div key={index} className="saswp-add-rv-loop">
                                                                                                                <input data-id={list.post.post_id} onChange={handleCollectionClick} checked={ (postMeta.saswp_attached_collection && (postMeta.saswp_attached_collection).includes(list.post.post_id))  ? true : false } className="saswp-attach-rv-checkbox" type="checkbox" />  <strong> {list.post.post_title}  </strong>
                                                                                                                </div>
                                                                                                                        
                                                                                                        )                                

                                                                                                })

                                                                                                : '' 
                                                                                        }
                                                                                        </>}
                                                                                        {collectionToBeAddedFound > 10 ? <div><a onClick={handleLoadMoreCollection}>{__('Load More...', 'schema-and-structured-data-for-wp')}</a></div> : ''}

                                                                                        </div> : ''}                                                                                   
                                                                                                                                                                
                                                                                {reviewTabStatus == 2 ? 
                                                                                <div className="saswp-rv-tab-content">
                                                                                        <p> {__('Output reviews in front and its schema markup in source by using below shortcode', 'schema-and-structured-data-for-wp')} </p>
                                                                                        <strong>[saswp-reviews]</strong>
                                                                                        <br/>
                                                                                        {__('Or', 'schema-and-structured-data-for-wp')}
                                                                                        <br/>
                                                                                        <strong>[saswp-reviews-collection id="your collection id"]</strong>                                                                                        
                                                                                </div> : ''}
                                                                                
                                                                        </div>
                                                                </div>
                                                                                                                                
                                                        </Modal>

                                                        <a className="saswp-attach-reviews" onClick={handleOpenAddReviewModal}>
                                                                
                                                                {

                                                                        (postMeta.saswp_attahced_reviews || postMeta.saswp_attached_collection) ? 
                                                                        <span className="saswp-attached-rv-count">
                                                                               {
                                                                                       postMeta.saswp_attahced_reviews ? 
                                                                                       <span>{__('Attached', 'schema-and-structured-data-for-wp')} {postMeta.saswp_attahced_reviews.length} {__('Reviews', 'schema-and-structured-data-for-wp')} </span>
                                                                                       : ''                                                                                       
                                                                               } 
                                                                               {
                                                                                       postMeta.saswp_attached_collection ? 
                                                                                       <span>, {postMeta.saswp_attached_collection.length} {__('Collections', 'schema-and-structured-data-for-wp')}</span>
                                                                                       : ''
                                                                               }
                                                                                </span>
                                                                        : ''


                                                                }

                                                        </a>
                                                </div>
                                       </div>
                                               </div>
                                               : ''
                                       }
                                       {
                                               (schemaType == 'TechArticle' || schemaType == 'Article' || schemaType == 'Blogposting' || schemaType == 'NewsArticle' || schemaType == 'WebPage') ? 
                                               <div>
                                                       <div className="divider-horizontal"></div>
                                                <div className="card-body">
                                                                {__('ItemList', 'schema-and-structured-data-for-wp')}  
                                                                <div className="saswp-setup"><label className="form-check form-group toggle">
                                                                <input checked={postMeta.saswp_enable_itemlist_schema == 1 ? true : false } onChange={handleInputChange}  name="saswp_enable_itemlist_schema" type="checkbox" className="form-check-input" />
                                                                <span className="form-check-label"></span>                                                
                                                                </label>
                                                                <a href="https://structured-data-for-wp.com/docs/article/how-to-add-item-list-schema-with-data-automatically/" target="_blank"><span>?</span></a>
                                                                </div>
                                                                

                                                                {
                                                                        postMeta.saswp_enable_itemlist_schema == 1 ?
                                                                        <div>
                                                                                <table className="form-table">
                                                                                 <tbody>
                                                                                         <tr>
                                                                                                 <td>Select The Tag</td>
                                                                                                 <td>
                                                                                                 <select value={postMeta.saswp_item_list_tags} name="saswp_item_list_tags" onChange={handleInputChange}>
                                                                                        <option value="h1">H1</option> 
                                                                                        <option value="h2">H2</option> 
                                                                                        <option value="h3">H3</option> 
                                                                                        <option value="h4">H4</option> 
                                                                                        <option value="h5">H5</option> 
                                                                                        <option value="h6">H6</option> 
                                                                                        <option value="custom">Custom</option> 
                                                                                </select>
                                                                                                 </td>
                                                                                         </tr>
                                                                                 </tbody>       
                                                                                </table>
                                                                                      
                                                                        {
                                                                        postMeta.saswp_item_list_tags == "custom" ? 
                                                                        <input type="text" name="saswp_item_list_custom" onChange={handleInputChange} value={postMeta.saswp_item_list_custom} />
                                                                        : ''       
                                                                        }
                                                                                
                                                                </div>       
                                                                        : ''
                                                                }                                                
                                                </div>
                                               </div>
                                               
                                               : ''
                                       }
                                       
                                       {
                                               (schemaType == 'TechArticle' || schemaType == 'Article' || schemaType == 'Blogposting' || schemaType == 'NewsArticle' || schemaType == 'WebPage') ?
                                                        <div>
                                                        <div className="divider-horizontal"></div>
                                                                <div className="card-body">
                                                                                {__('Speakable', 'schema-and-structured-data-for-wp')}
                                                                                <div className="saswp-setup"><label className="form-check form-group toggle">
                                                                                <input checked={postMeta.saswp_enable_speakable_schema == 1 ? true : false } onChange={handleInputChange}  name="saswp_enable_speakable_schema" type="checkbox" className="form-check-input" />
                                                                                <span className="form-check-label"></span>                                                
                                                                                </label>
                                                                                <a href="https://structured-data-for-wp.com/docs/article/how-to-setup-speakable-in-schema-in-wordpress/" target="_blank"><span>?</span></a>
                                                                                </div>
                                                                                
                                                                                
                                                                </div>
                                                        </div>
                                                : ''
                                       }
                                                                              
                                       <div className="divider-horizontal"></div>
                                       <div className="card-body">
                                                {__('Paywall', 'schema-and-structured-data-for-wp')}
                                                <div className="saswp-setup"><label className="form-check form-group toggle">
                                                <input checked={postMeta.schema_options.notAccessibleForFree == 1 ? true : false } onChange={handleInputChange}  name="notAccessibleForFree" type="checkbox" className="form-check-input" />
                                                <span className="form-check-label"></span>                                                
                                                </label>
                                                <a href="https://structured-data-for-wp.com/docs/article/how-to-setup-paywall-schema-in-wordpress" target="_blank"><span>?</span></a>
                                                </div>
                                                

                                                {
                                                        postMeta.schema_options.notAccessibleForFree == 1 ?
                                                        
                                                        <div>
                                                                <table className="form-table">
                                                                 <tbody>
                                                                  <tr>
                                                                          <td>{__('Is accessible for free', 'schema-and-structured-data-for-wp')}</td>
                                                                          <td>
                                                                          <select name="isAccessibleForFree" value={postMeta.schema_options.isAccessibleForFree} onChange={handleInputChange}>
                                                                                <option value="False">{__('False', 'schema-and-structured-data-for-wp')}</option>
                                                                                <option value="True">{__('True', 'schema-and-structured-data-for-wp')}</option>                                                                
                                                                          </select>
                                                                          </td>
                                                                  </tr>
                                                                  <tr><td>{__('Enter the class name of paywall section', 'schema-and-structured-data-for-wp')}</td>
                                                                          <td>
                                                                          <input type="text" name="paywall_class_name" value={postMeta.schema_options.paywall_class_name} onChange={handleInputChange} />
                                                                          </td>
                                                                  </tr>       
                                                                 </tbody>       
                                                                </table>                                                                
                                                                
                                                        </div>
                                                        
                                                        : ''
                                                }
                                                                                                
                                       </div>
                                
                                       
                                       <div className="divider-horizontal"></div>
                                       <div className="card-body">
                                               <div className="saswp-global-modify-top">
                                                        {__('Modify Schema Output', 'schema-and-structured-data-for-wp')}
                                                        <div className="saswp-setup">
                                                                <label className="form-check form-group toggle">
                                                                <input checked={postMeta.schema_options.enable_custom_field == 1 ? true : false} onChange={handleInputChange}  name="enable_custom_field" type="checkbox" className="form-check-input" />
                                                                <span className="form-check-label"></span>                                                
                                                                </label>
                                                                <a href="https://structured-data-for-wp.com/docs/article/how-to-modify-schema-output" target="_blank"><span>?</span></a>
                                                        </div>
                                                        
                                               </div>

                                                {
                                                 postMeta.schema_options.enable_custom_field == 1 ?
                                                        <div className="saswp-modify-container">                                                                
                                                                {
                                                                        (schemaType == 'local_business' || schemaType == 'HowTo' || schemaType == 'FAQ') ? 
                                                                        <div className="saswp-enable-modify-schema">
                                                                                <strong>{__('Choose Method', 'schema-and-structured-data-for-wp')}</strong>
                                                                                <select onChange={handleInputChange} value={postMeta.schema_options.saswp_modify_method} name="saswp_modify_method" className="saswp-enable-modify-schema-output">
                                                                                <option value="automatic">{__('Automatic', 'schema-and-structured-data-for-wp')}</option>
                                                                                <option value="manual">{__('Manual', 'schema-and-structured-data-for-wp')}</option>
                                                                                </select>                                    
                                                                        </div>
                                                                        : ''
                                                                }
                                                                {
                                                                        (postMeta.schema_options.saswp_modify_method == 'manual') ? 
                                                                        <div>
                                                                                {manualFields ? 
                                                                                        <FieldGenerator 
                                                                                                postMeta={postMeta}
                                                                                                handleInputChange={handleInputChange}
                                                                                                handleManualFieldImage = {handleManualFieldImage}
                                                                                                fielddata={manualFields} 
                                                                                        />
                                                                                : null}                                                                        
                                                                        </div> 
                                                                         : 
                                                                        <div className="saswp-dynamic-container">
                                                                        <div className="saswp-custom-fields-div">

                                                                        {(modifyEntry.length > 0) ? 

                                                                        <table className="saswp-custom-fields-table form-table">
                                                                        <tbody>
                                                                                {                                                                                        
                                                                                        modifyTr()
                                                                                }
                                                                                
                                                                        </tbody>
                                                                        </table>

                                                                        :''
                                                                        }
                                                                             
                                                                        <div> <a onClick={addTr} className="saswp-add-custom-fields btn btn-primary">{__('Add Property', 'schema-and-structured-data-for-wp')}</a> </div>     
                                                                        </div>     
                                                                </div>
                                                                }                                                                
                                                        </div>
                                                 : ''       
                                                }                                                                                               
                                       </div>
                                       
                                
                                </div> 
                        </div>
                        <div className="saswp-publish-button">
                                {
                                (postStatus == 'publish' || postStatus == 'draft') ? 
                                        <>
                                        {isLoaded ? <a className="btn btn-success" onClick={publishPost}>{__('Update', 'schema-and-structured-data-for-wp')}</a> : <Button success loading>Loading success</Button>}
                                        </>       
                                :
                                <div>
                                        {isLoaded ? <>
                                                <a className="btn btn-success" onClick={draftPost}>{__('Draft', 'schema-and-structured-data-for-wp')}</a>
                                                <a className="btn btn-success" onClick={publishPost}>{__('Publish', 'schema-and-structured-data-for-wp')}</a>
                                        </> : <Button success loading>Loading success</Button>
                                        }
                                        
                                </div>
                                }                                 
                        </div>

                </div>
                </form>
        </div>
        </>);
}
export default SchemaSingle;