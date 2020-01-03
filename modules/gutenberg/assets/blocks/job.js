
( function( blocks, element, editor, components, i18n) {
            
    var el               = element.createElement;    
    const { __ }         = i18n;    
    const { RichText,  AlignmentToolbar, BlockControls, InspectorControls, MediaUpload } = editor;
    const {RadioControl, Popover, Button, IconButton,  TextareaControl, TextControl, ToggleControl, PanelBody, DateTimePicker } = components;
                
    blocks.registerBlockType( 'saswp/job-block', {
        title: __('Job (SASWP)', 'schema-and-structured-data-for-wp'),
        icon: 'calendar',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'Job', 'job'],
        
        attributes: {
            posted_by:{
                type:'string'                
            },
            job_types : {
              type: 'string'              
            },
            location:{
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
            company_video_url:{
                type:'string'                
            },
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
                                label : 'Posted By',
                                onChange: function(value){
                                    props.setAttributes( { posted_by: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-location',
                                value :  attributes.location,
                                label : 'Location',
                                onChange: function(value){
                                    props.setAttributes( { location: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-app-email-url',
                                value :  attributes.app_email_or_website,
                                label : 'Application email/URL',
                                onChange: function(value){
                                    props.setAttributes( { app_email_or_website: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-company-name',
                                value :  attributes.company_name,
                                label : 'Company Name',
                                onChange: function(value){
                                    props.setAttributes( { company_name: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-company-website',
                                value :  attributes.company_website,
                                label : 'Company Website',
                                onChange: function(value){
                                    props.setAttributes( { company_website: value } );
                                }
                                },                        
                                ),
                                el(TextareaControl,{
                                className:'saswp-job-company-tagline',
                                value :  attributes.company_tagline,
                                label : 'Company Tagline',
                                onChange: function(value){
                                    props.setAttributes( { company_tagline: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-company-twitter',
                                value :  attributes.company_twitter,
                                label : 'Company Twitter',
                                onChange: function(value){
                                    props.setAttributes( { company_twitter: value } );
                                }
                                },                        
                                ),
                                el(TextControl,{
                                className:'saswp-job-company-facebook',
                                value :  attributes.company_facebook,
                                label : 'Company Facebook',
                                onChange: function(value){
                                    props.setAttributes( { company_facebook: value } );
                                }
                                },                        
                                ),
                                
                                el('div',{},
                                el(TextControl,{
                                className:'saswp-job-company-video',
                                value :  attributes.company_video_url,
                                label : 'Company Video',
                                onChange: function(value){
                                    props.setAttributes( { company_video_url: value } );
                                }
                                },                                
                                ),
                                el(MediaUpload,{
                                className:'saswp-job-company-video-upload',
                                value : attributes.company_video_url,
                                allowedTypes:[ "video" ],                                
                                onSelect: function(value){
                                    props.setAttributes( { company_video_url: value.url } );
                                },
                                render: function(obj){
                                   return el(IconButton,{
                                       isSecondary: true,
                                       icon : 'upload',
                                       onClick: obj.open  
                                     },
                                      __('Upload', 'schema-and-structured-data-for-wp')
                                     );
                                   }
                                },                                
                                )
                                ),
                                el(TextControl,{
                                className:'saswp-job-list-expire-date',
                                value :  attributes.listing_expire_date,
                                label : 'Listing Expire Date',
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
                                : ''                                        
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

