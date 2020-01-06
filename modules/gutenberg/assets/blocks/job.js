
( function( blocks, element, editor, components, i18n) {
            
    const el             = element.createElement;    
    const { __ }         = i18n;    
    const { RichText,  AlignmentToolbar, BlockControls, InspectorControls, MediaUpload } = editor;
    const {RadioControl, Popover, Button, IconButton,  TextareaControl, TextControl, ToggleControl, PanelBody, DateTimePicker } = components;
                
    blocks.registerBlockType( 'saswp/job-block', {
        title: __('Job (SASWP)', 'schema-and-structured-data-for-wp'),
        icon: 'id',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'Job', 'job'],
        
        attributes: {
            posted_by:{
                type:'string'                
            },
            job_description : {
              type: 'string'              
            },
            job_types : {
              type: 'string'              
            },
            location_address:{
                type:'string'                
            },
            location_city:{
                type:'string'                
            },
            location_state:{
                type:'string'                
            },
            location_country:{
                type:'string'                
            },
            location_postal_code:{
                type:'string'                
            },            
            app_email_or_website:{
                type:'string'                
            },
            company_name:{
                type:'string'               
            },
            company_website:{
                type:'string'                
            },
            company_tagline:{
                type:'string'                
            },            
            company_twitter:{
                type:'string'                
            },
            company_facebook:{
                type:'string'                
            },
//            company_video_url:{
//                type:'string'                
//            },
            company_logo_url:{
                type:'string'                
            },
            company_logo_id:{
                type:'integer'               
            },
            listing_expire_date:{
                type:'string'                
            },
            listing_toggle:{
                type:'boolean',
                default:false
            },
            base_salary:{
                type:'string'                
            },
            currency_code:{
                type:'string'                
            },
            unit_text:{
                type:'string'              
            }
            
        },        
        // Allow only one How To block per post.
        supports: {
                multiple: false
        },
                                             
        edit: function( props ) {
            
            var attributes = props.attributes; 
            
            var job_data = el('fieldset',{className:''},
            
                                el(TextControl,{
                                className:'saswp-job-posted-by',
                                value :  attributes.posted_by,
                                label : __('Posted By', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { posted_by: value } );
                                }
                                },                        
                                ),
                                
                                el(TextareaControl,{
                                className:'saswp-job-description',
                                value :  attributes.job_description,
                                label : __('Job Description', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { job_description: value } );
                                }
                                },                        
                                ),
                                                                
                                el(TextControl,{
                                className:'saswp-job-location',
                                value :  attributes.location_address,
                                label : __('Location Address', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { location_address: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-location',
                                value :  attributes.location_city,
                                label : __('Location City', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { location_city: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-location',
                                value :  attributes.location_state,
                                label : __('Location State', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { location_state: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-location',
                                value :  attributes.location_country,
                                label : __('Location Country', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { location_country: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-location',
                                value :  attributes.location_postal_code,
                                label : __('Location Postal Code', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { location_postal_code: value } );
                                }
                                },                        
                                ),
                                
                                el(TextControl,{
                                className:'saswp-job-app-email-url',
                                value :  attributes.app_email_or_website,
                                label : __('Application email/URL', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { app_email_or_website: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-company-name',
                                value :  attributes.company_name,
                                label : __('Company Name', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { company_name: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-company-website',
                                value :  attributes.company_website,
                                label : __('Company Website', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { company_website: value } );
                                }
                                },                        
                                ),
                                el(TextareaControl,{
                                className:'saswp-job-company-tagline',
                                value :  attributes.company_tagline,
                                label : __('Company Tagline', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { company_tagline: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-company-twitter',
                                value :  attributes.company_twitter,
                                label : __('Company Twitter', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { company_twitter: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-company-facebook',
                                value :  attributes.company_facebook,
                                label : __('Company Facebook', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { company_facebook: value } );
                                }
                                },                        
                                ),
                                
//                                el('div',{},
//                                el(TextControl,{
//                                className:'saswp-job-company-video',
//                                value :  attributes.company_video_url,
//                                label : 'Company Video',
//                                onChange: function(value){
//                                    props.setAttributes( { company_video_url: value } );
//                                }
//                                },                                
//                                ),
//                                el(MediaUpload,{
//                                className:'saswp-job-company-video-upload',
//                                value : attributes.company_video_url,
//                                allowedTypes:[ "video" ],                                
//                                onSelect: function(value){
//                                    props.setAttributes( { company_video_url: value.url } );
//                                },
//                                render: function(obj){
//                                   return el(IconButton,{
//                                       isSecondary: true,
//                                       icon : 'upload',
//                                       onClick: obj.open  
//                                     },
//                                      __('Upload', 'schema-and-structured-data-for-wp')
//                                     );
//                                   }
//                                },                                
//                                )
//                                ),
                                                
                                el('div',{className:'saswp-listing-fields'},
                                
                                el(TextControl,{
                                className:'saswp-job-list-expire-date',
                                value :  attributes.listing_expire_date,
                                label : __('Listing Expire Date', 'schema-and-structured-data-for-wp'),
                                onClick: function(value){                                    
                                    props.setAttributes( { listing_toggle: true } );
                                }
                                },                                
                                ),
                                attributes.listing_toggle ? 
                                el(
                                Popover,{
                                    class:'saswp-calender-popover',
                                    position: 'bottom',
                                    onClose: function(){
                                       props.setAttributes( { listing_toggle: false } );     
                                    }
                                },
                                el(DateTimePicker,{
                                 currentDate: attributes.listing_expire_date,                 
                                 is12Hour : true,
                                 onChange: function(value){                                      
                                      var newDate = moment(value).format('YYYY-MM-DD');                                       
                                       props.setAttributes( { listing_expire_date: newDate } ); 
                                       
                                 }
                                })
                                ) 
                                : '',
                                
                                ),
                                
                                el(TextControl,{
                                className:'saswp-job-base-salary',
                                value :  attributes.base_salary,
                                label : __('Base Salary', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { base_salary: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-base-salary',
                                value :  attributes.currency_code,
                                label : __('Currency Code', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { currency_code: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-base-salary',
                                value :  attributes.unit_text,
                                label : __('Unit Text', 'schema-and-structured-data-for-wp'),
                                onChange: function(value){
                                    props.setAttributes( { unit_text: value } );
                                }
                                },                        
                                )                                
                            );
            
            return [
                el(InspectorControls,{class:'saswp-job-inspector'},
                el(PanelBody,{
                    className:'saswp-job-types',
                    title:'Job Types' 
                }, 
                el(RadioControl,{
                    selected: attributes.job_types,
                    options : [
                            { label: 'Freelance', value: 'freelance' },
                            { label: 'Full Time', value: 'full_time' },
                            { label: 'Internship', value: 'internship' },
                            { label: 'Part Time', value: 'part_time' },
                            { label: 'Temporary', value: 'temporary' },
			],
                    onChange: function(value) {
                            props.setAttributes( { job_types: value } );
                    }
                },
                
                )
                ),
                el(PanelBody,{
                    className: 'saswp-job-company-logo-panel',
                    title: 'Company Logo'
                },
                el(MediaUpload,{
                    className:'saswp-job-company-logo',
                    allowedTypes:[ "image" ],
                    value: attributes.company_logo_id,
                    onSelect: function(value){
                            
                                props.setAttributes( { company_logo_url: value.url } );                                                                    
                                props.setAttributes( { company_logo_id: value.id } );
                                                              
                    },
                    render:function(obj){
                            
                                 var render_res;                                                                                  
                                 if(attributes.company_logo_url){
                                     
                                    render_res =      el('div',{
                                      className:'saswp-job-company-logo-panel'},
                                      el('img',{
                                      src:attributes.company_logo_url, 
                                      onClick: obj.open,
                                     }),
                                     el(Button,{
                                         className:'saswp-remove is-link',
                                         isDestructive : true,
                                         onClick: function(){
                                              props.setAttributes( { company_logo_url: '' } );                                                                    
                                              props.setAttributes( { company_logo_id: null } );
                                         }
                                     },
                                     __('Remove Logo', 'schema-and-structured-data-for-wp')
                                     )
                                     );
                                 }else{
                                     
                                  render_res =    el( Button, {                                    
                                    isSecondary: true,
                                    className: 'editor-post-featured-image__toggle',            
                                    onClick: obj.open
                                  },
                                    __('Set Company Logo', 'schema-and-structured-data-for-wp')
                                 );
                                 }
                                 return render_res;
                                                
                           }
                         }
                        )
                )
                ),
                el('div',{className:'saswp-job-wrapper'},
                job_data
                )
            ];
                        
        },
        save: function( props ) {
            return null                        
        }
    } );
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n,    
) );

