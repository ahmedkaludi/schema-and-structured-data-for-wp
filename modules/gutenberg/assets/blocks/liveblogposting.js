
( function( blocks, element, editor, components, i18n ) {
    
    const { __ }          = i18n;
    const { RichText, MediaUpload, AlignmentToolbar, BlockControls, InspectorControls} = editor;
    const {TextControl, ToggleControl, PanelBody, IconButton, SelectControl, Popover, DateTimePicker, Button} = components;        

    const el                = element.createElement;

    blocks.registerBlockType( 'saswp/live-blog-posting', {

        title: __('Live Blog Posting (SASWP)', 'schema-and-structured-data-for-wp'),
        icon: 'welcome-write-blog',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'live blog posting', 'live-blog'],

        // Allow only one LiveBlogPosting To block per post.
        supports: {
                multiple: false
        },

        attributes: {
            name: {
                  type: 'string',                  
            },
            locationname: {
                  type: 'string',                  
            },
            address: {
                  type: 'string',                  
            },
            locality: {
                  type: 'string',                  
            },
            postalcode: {
                  type: 'string',                  
            },
            region: {
                  type: 'string',                  
            },
            country: {
                  type: 'string',                  
            },
            start_date: {
                  type: 'string',                  
            },
            start_date_toggle: {
                type: 'boolean',
                default: false
            },
            start_date_iso: {
                type: 'string'                
            },
            coverage_start_date: {
                type: 'string'                
            },
            coverage_start_date_toggle: {
                type: 'boolean',
                default: false
            },
            coverage_start_date_iso: {
                type: 'string'                
            },
            coverage_end_date: {
                type: 'string'                
            },
            coverage_end_date_toggle: {
                type: 'boolean',
                default: false
            },
            coverage_end_date_iso: {
                type: 'string'                
            },
            blog_update: {                     
              default: [{index: 0, headline: '', date: '', date_toggle: false, date_iso: '', body: '', image_id: null, image_url: ''}],              
              query: {
                index: {            
                  type: 'number',                  
                  attribute: 'data-index'                  
                },
                isSelected: {            
                  type: 'boolean',
                  default:false      
                },   
                headline: {
                  type: 'string'                                  
                },
                date: {
                  type: 'string'                                    
                },
                date_toggle: {
                    type: 'boolean',
                    default: false
                },
                date_iso: {
                    type: 'string'                
                },
                body: {
                  type: 'string'                                    
                },
                image_id: {
                    type: 'integer'
                },
                image_url: {
                    type: 'integer'
                },
              }
            },
            event_details: {
                type: 'boolean',
                default: false
            },
            event_start_date: {
              type: 'string'                                    
            },
            event_start_date_toggle: {
                type: 'boolean',
                default: false
            },
            event_start_date_iso: {
                type: 'string'                
            },
            event_end_date: {
              type: 'string'                                    
            },
            event_end_date_toggle: {
                type: 'boolean',
                default: false
            },
            event_end_date_iso: {
                type: 'string'                
            },
            event_status: {
              type: 'string'                
            },
            attendance_mode: {
              type: 'string'                
            },
            price: {
              type: 'integer'               
            },
            low_price: {
              type: 'integer'              
            },
            high_price: {
              type: 'integer'              
            },
            offer_url: {
              type: 'string'                
            },
            offer_currency_code: {
                type: 'string',
                default: 'USD'
            },
            event_offer_date: {
              type: 'string'                                    
            },
            event_offer_date_toggle: {
                type: 'boolean',
                default: false
            },
            event_offer_date_iso: {
                type: 'string'                
            },
            organizers: {                     
              default: [{index: 0, name: '', phone: '', website: '', email: ''}],              
              query: {
                index: {            
                  type: 'number',                  
                  attribute: 'data-index'                  
                },
                isSelected: {            
                  type: 'boolean',
                  default:false      
                },   
                name: {
                  type: 'string'                                  
                },
                phone: {
                  type: 'string'                                    
                },
                website: {
                  type: 'string'                                    
                },
                email: {
                  type: 'string'                                    
                },
              }
            },
            performers: {                     
              default: [{index: 0, name: '', url: '', email: ''}],              
              query: {
                index: {            
                  type: 'number',                  
                  attribute: 'data-index'                  
                },
                isSelected: {            
                  type: 'boolean',
                  default:false      
                },   
                name: {
                  type: 'string'                                  
                },
                url: {
                  type: 'string'                                    
                },
                email: {
                  type: 'string'                                    
                }                
              }
            },
        },

        edit: function(props) {

            const attributes = props.attributes;

            var coverage_start_date_div = el( 'div', {},
                el('span',{className:'saswp-live-blog-posting-date-fields'},
                    el(TextControl,{            
                        className:'saswp-live-blog-posting-cover-start-date',
                        label: __( 'Coverage Start Date', 'schema-and-structured-data-for-wp' ),
                        value : attributes.coverage_start_date,
                        onClick:function(){
                            props.setAttributes( { coverage_start_date_toggle: true } );   
                        }            
                    })            
                ),
                attributes.coverage_start_date_toggle ? 
                el(
                    Popover,{
                        class:'saswp-calender-popover',
                        position: 'bottom',
                        onClose: function(){
                           props.setAttributes( { coverage_start_date_toggle: false } );     
                        }
                    },
                    el(DateTimePicker,{
                        currentDate: attributes.coverage_start_date_iso,                 
                        is12Hour : true,
                        onChange: function(value){
                          attributes.coverage_start_date_iso = value;
                          var newDate = moment(value).format('YYYY-MM-DD'); 
                          var newTime = moment(value).format('h:mm:ss a');
                          var fullDateTime =  newDate + ' ' + newTime;
                           props.setAttributes( { coverage_start_date: fullDateTime } ); 
                         
                        }
                    })
                ) 
                : ''
            );

            var coverage_end_date_div = el( 'div', {},
                el('span',{className:'saswp-live-blog-posting-date-fields'},
                    el(TextControl,{            
                        className:'saswp-live-blog-posting-cover-start-date',
                        label: __( 'Coverage End Date', 'schema-and-structured-data-for-wp' ),
                        value : attributes.coverage_end_date,
                        onClick:function(){
                            props.setAttributes( { coverage_end_date_toggle: true } );   
                        }            
                    })            
                ),
                attributes.coverage_end_date_toggle ? 
                el(
                    Popover,{
                        class:'saswp-calender-popover',
                        position: 'bottom',
                        onClose: function(){
                           props.setAttributes( { coverage_end_date_toggle: false } );     
                        }
                    },
                    el(DateTimePicker,{
                        currentDate: attributes.coverage_end_date_iso,                 
                        is12Hour : true,
                        onChange: function(value){
                          attributes.coverage_end_date_iso = value;
                          var newDate = moment(value).format('YYYY-MM-DD'); 
                          var newTime = moment(value).format('h:mm:ss a');
                          var fullDateTime =  newDate + ' ' + newTime;
                           props.setAttributes( { coverage_end_date: fullDateTime } ); 
                         
                        }
                    })
                ) 
                : ''
            );

            var event_start_date_div = el( 'div', {},
                el('span',{className:'saswp-live-blog-posting-date-fields'},
                    el(TextControl,{            
                        className:'saswp-live-blog-posting-cover-start-date',
                        label: __( 'Event Start Date', 'schema-and-structured-data-for-wp' ),
                        value : attributes.event_start_date,
                        onClick:function(){
                            props.setAttributes( { event_start_date_toggle: true } );   
                        }            
                    })            
                ),
                attributes.event_start_date_toggle ? 
                el(
                    Popover,{
                        class:'saswp-calender-popover',
                        position: 'bottom',
                        onClose: function(){
                           props.setAttributes( { event_start_date_toggle: false } );     
                        }
                    },
                    el(DateTimePicker,{
                        currentDate: attributes.event_start_date_iso,                 
                        is12Hour : true,
                        onChange: function(value){
                          attributes.event_start_date_iso = value;
                          var newDate = moment(value).format('YYYY-MM-DD'); 
                          var newTime = moment(value).format('h:mm:ss a');
                          var fullDateTime =  newDate + ' ' + newTime;
                           props.setAttributes( { event_start_date: fullDateTime } ); 
                         
                        }
                    })
                ) 
                : ''
            );

            var event_end_date_div = el( 'div', {},
                el('span',{className:'saswp-live-blog-posting-date-fields'},
                    el(TextControl,{            
                        className:'saswp-live-blog-posting-cover-start-date',
                        label: __( 'Event Start Date', 'schema-and-structured-data-for-wp' ),
                        value : attributes.event_end_date,
                        onClick:function(){
                            props.setAttributes( { event_end_date_toggle: true } );   
                        }            
                    })            
                ),
                attributes.event_end_date_toggle ? 
                el(
                    Popover,{
                        class:'saswp-calender-popover',
                        position: 'bottom',
                        onClose: function(){
                           props.setAttributes( { event_end_date_toggle: false } );     
                        }
                    },
                    el(DateTimePicker,{
                        currentDate: attributes.event_end_date_iso,                 
                        is12Hour : true,
                        onChange: function(value){
                          attributes.event_end_date_iso = value;
                          var newDate = moment(value).format('YYYY-MM-DD'); 
                          var newTime = moment(value).format('h:mm:ss a');
                          var fullDateTime =  newDate + ' ' + newTime;
                           props.setAttributes( { event_end_date: fullDateTime } ); 
                         
                        }
                    })
                ) 
                : ''
            );

            var event_offer_date_div = el( 'div', {},
                el('span',{className:'saswp-live-blog-posting-date-fields'},
                    el(TextControl,{            
                        className:'saswp-live-blog-posting-cover-start-date',
                        label: __( 'Event Valid Date', 'schema-and-structured-data-for-wp' ),
                        value : attributes.event_offer_date,
                        onClick:function(){
                            props.setAttributes( { event_offer_date_toggle: true } );   
                        }            
                    })            
                ),
                attributes.event_offer_date_toggle ? 
                el(
                    Popover,{
                        class:'saswp-calender-popover',
                        position: 'bottom',
                        onClose: function(){
                           props.setAttributes( { event_offer_date_toggle: false } );     
                        }
                    },
                    el(DateTimePicker,{
                        currentDate: attributes.event_offer_date_iso,                 
                        is12Hour : true,
                        onChange: function(value){
                          attributes.event_offer_date_iso = value;
                          var newDate = moment(value).format('YYYY-MM-DD'); 
                          var newTime = moment(value).format('h:mm:ss a');
                          var fullDateTime =  newDate + ' ' + newTime;
                           props.setAttributes( { event_offer_date: fullDateTime } ); 
                         
                        }
                    })
                ) 
                : ''
            );

            var organizers_loop =  attributes.organizers.sort(function(a, b){
                return a.index - b.index;            
            }).map(function(item){
                
                return el('fieldset',{className:'saswp-event-organisers-fieldset'},el('div',{className:'saswp-event-organisers'},
                    el(IconButton,{
                    icon:'trash',
                    className: 'saswp-remove-repeater',
                    onClick: function(e){
                        
                        const oldItems           =  attributes.organizers;  
                        const fieldname          = 'organizers';
                        saswpRemoveRepeater(oldItems, fieldname, item);                                        
                    }    
                    }),
                    el(TextControl,{
                    label:__('Name', 'schema-and-structured-data-for-wp'),    
                    value: item.name,
                    onChange: function( value ) {                                
                                var newObject = Object.assign({}, item, {
                                  name: value
                                });
                                saswp_lbp_on_item_change(newObject, item, 'organizers');                            
                      }   
                    }),
                    el(TextControl,{
                        label:__('Phone', 'schema-and-structured-data-for-wp'),   
                        value: item.phone,
                        onChange: function(value){
                             var newObject = Object.assign({}, item, {
                                  phone: value
                                });
                                saswp_lbp_on_item_change(newObject, item, 'organizers'); 
                        }    
                    }),
                    el(TextControl,{
                        label:__('Website', 'schema-and-structured-data-for-wp'),
                        value: item.website,
                        onChange: function(value){
                             var newObject = Object.assign({}, item, {
                                  website: value
                                });
                                saswp_lbp_on_item_change(newObject, item, 'organizers'); 

                        }    
                    }),
                    el(TextControl,{
                        label:__('Email', 'schema-and-structured-data-for-wp'), 
                        value: item.email,
                        onChange: function(value){
                             var newObject = Object.assign({}, item, {
                                  email: value
                                });
                                saswp_lbp_on_item_change(newObject, item, 'organizers'); 
                        }    
                    })            
                ));
                
            });
        
            var organizers = el('fieldset',{className:'saswp-event-organisers-fieldset'},el('div',{
                className:'saswp-event-organisers-container'
                },
                 el('h3',{},__('Organizers', 'schema-and-structured-data-for-wp')),
                 organizers_loop,
                 el(Button,{
                     className:'saswp-org-repeater',
                     isSecondary: true,
                     isLarge : true,
                     onClick: function() {              
                        return props.setAttributes({
                          organizers: [].concat(_cloneArray(props.attributes.organizers), [{
                            index: props.attributes.organizers.length                                                                                         
                          }])
                        });                            
                      }
                    },
                    __('Add More Organizer', 'schema-and-structured-data-for-wp')                            
                 ),                         
            ));

            var performers_loop =  attributes.performers.sort(function(a, b){
                return a.index - b.index;            
            }).map(function(item){
                
                return el('fieldset',{className:'saswp-event-performers-fieldset'},el('div',{className:'saswp-event-performers'},
                    el(IconButton,{
                    icon:'trash', 
                    className: 'saswp-remove-repeater',
                    onClick: function(e){
                        
                        const oldItems           =  attributes.performers;  
                        const fieldname          = 'performers';
                        saswpRemoveRepeater(oldItems, fieldname, item);                                        
                    }    
                    }),
                    el(TextControl,{
                    label:__('Name', 'schema-and-structured-data-for-wp'),    
                    value: item.name,
                    onChange: function( value ) {                                
                                var newObject = Object.assign({}, item, {
                                  name: value
                                });
                               saswp_lbp_on_item_change(newObject, item, 'performers'); 
                      }   
                    }),
                    el(TextControl,{
                        label:__('URL', 'schema-and-structured-data-for-wp'),   
                        value: item.url,
                        onChange: function(value){
                             var newObject = Object.assign({}, item, {
                                  url: value
                                });
                                saswp_lbp_on_item_change(newObject, item, 'performers');
                        }    
                    }),
                    
                    el(TextControl,{
                        label:__('Email', 'schema-and-structured-data-for-wp'), 
                        value: item.email,
                        onChange: function(value){
                             var newObject = Object.assign({}, item, {
                                  email: value
                                });
                                saswp_lbp_on_item_change(newObject, item, 'performers');
                        }    
                    })            
                ));
                
            });               
                        
            var performers = el('fieldset',{className:'saswp-event-performers-fieldset'},el('div',{
                 className:'saswp-event-performers-container'
                 },
                 el('h3',{},__('Performers', 'schema-and-structured-data-for-wp')),
                 performers_loop,
                 el(Button,{
                     className:'saswp-org-repeater',
                     isSecondary: true,
                     isLarge : true,
                     onClick: function() {              
                        return props.setAttributes({
                          performers: [].concat(_cloneArray(props.attributes.performers), [{
                            index: props.attributes.performers.length                                                                                         
                          }])
                        });                            
                      }
                    },
                    __('Add More Performer', 'schema-and-structured-data-for-wp')                            
                 ),                         
            ));

            var eventDetails = el('div',{
                  className: 'saswp-lbplu-event-wrapper'
                },
                attributes.event_details ?
                el('div',{className:'saswp-lbplu-banner-div1'},
                    el('h3',{},__('Event Details', 'schema-and-structured-data-for-wp')),
                    event_start_date_div,
                    event_end_date_div,
                    el(SelectControl,{
                        value : attributes.event_status,
                        label: __('Event Status', 'schema-and-structured-data-for-wp'),
                        options:[
                            { label: 'Select Status', value: '' },
                            { label: 'EventScheduled', value: 'EventScheduled' },
                            { label: 'Postponed', value: 'EventPostponed' },
                            { label: 'Rescheduled', value: 'EventRescheduled' },
                            { label: 'MovedOnline', value: 'EventMovedOnline' },
                            { label: 'Cancelled', value: 'EventCancelled' },
                        ] ,
                        onChange: function(value){
                           props.setAttributes( { event_status: value } ); 
                        }
                    }),
                    el(SelectControl,{
                        value : attributes.attendance_mode,
                        label: __('Attendance Mode', 'schema-and-structured-data-for-wp'),
                        options:[
                          { label: 'Select Attendance Mode', value: '' },
                          { label: 'Offline', value: 'OfflineEventAttendanceMode' },
                          { label: 'Online', value: 'OnlineEventAttendanceMode' },
                          { label: 'Mixed', value: 'MixedEventAttendanceMode' }                                    
                        ] ,
                        onChange: function(value){
                             props.setAttributes( { attendance_mode: value } ); 
                        }
                    }),
                    el( TextControl, {
                        label: __( 'Offer Price', 'schema-and-structured-data-for-wp' ),
                        value: attributes.price,
                        onChange: function( newContent ) {                                
                            props.setAttributes( { price: parseInt(newContent) } );
                        }
                    }),
                    el( TextControl, { 
                        label: __( 'Offer Low Price', 'schema-and-structured-data-for-wp' ),
                        value: attributes.low_price,
                        onChange: function( newContent ) {                                
                            props.setAttributes( { low_price: parseInt(newContent) } );
                        }
                    }),
                    el( TextControl, {
                        label: __( 'Offer High Price', 'schema-and-structured-data-for-wp' ),
                        value: attributes.high_price,
                        onChange: function( newContent ) {                                
                            props.setAttributes( { high_price: parseInt(newContent) } );
                        }
                    }),
                    el( TextControl, {
                        placeholder: __('Enter Offer Currency', 'schema-and-structured-data-for-wp'), 
                        label: __( 'Offer Currency', 'schema-and-structured-data-for-wp' ),
                        value: attributes.offer_currency_code,
                        onChange: function( newContent ) {                                
                            props.setAttributes( { offer_currency_code: newContent } );
                        }
                    }),
                    el( TextControl, {
                        placeholder: __('Enter Offer URL', 'schema-and-structured-data-for-wp'), 
                        label: __( 'Offer URL', 'schema-and-structured-data-for-wp' ),
                        value: attributes.offer_url,
                        onChange: function( newContent ) {                                
                            props.setAttributes( { offer_url: newContent } );
                        }
                    }),
                    event_offer_date_div,
                    organizers,
                    performers
                )
                : ''
            );

            var blog_update_loop =  attributes.blog_update.sort(function(a, b){
                return a.index - b.index;            
            }).map(function(item){
                
                var date_div = el( 'div', {},
                    el('span',{className:'saswp-live-blog-posting-date-fields'},
                        el(TextControl,{            
                            className:'saswp-live-blog-posting-cover-start-date',
                            label: __( 'Date', 'schema-and-structured-data-for-wp' ),
                            value : item.date,
                            onClick: function( ) {                            
                                var newObject = Object.assign({}, item, {
                                  date_toggle: true,
                                });
                                saswp_lbp_on_item_change(newObject, item, 'blog_update');                            
                            },
                            onChange: function(value) { // Properly update the date value
                                var newObject = Object.assign({}, item, {
                                    date: value
                                });
                                saswp_lbp_on_item_change(newObject, item, 'blog_update');
                            }           
                        })            
                    ),
                    item.date_toggle ? 
                    el(
                        Popover,{
                            class:'saswp-calender-popover',
                            position: 'bottom',
                            onClose: function( value ) {                                
                                var newObject = Object.assign({}, item, {
                                  date_toggle: false
                                });
                                saswp_lbp_on_item_change(newObject, item, 'blog_update');                            
                            }
                        },
                        el(DateTimePicker,{
                            currentDate: item.date_iso,                 
                            is12Hour : true,
                            onChange: function(value){
                                item.date_iso = value;
                                var newDate = moment(value).format('YYYY-MM-DD'); 
                                var newTime = moment(value).format('h:mm:ss a');
                                var fullDateTime =  newDate + ' ' + newTime;
                                var newObject = Object.assign({}, item, {
                                  date: fullDateTime
                                });
                                saswp_lbp_on_item_change(newObject, item, 'blog_update'); 
                             
                            }
                        })
                    ) 
                    : ''
                );

                return el('fieldset',{className:'saswp-live-blog-posting-update-fieldset'},el('div',{className:'saswp-live-blog-posting-update'},
                    el(IconButton,{
                    icon:'trash',
                    className: 'saswp-remove-repeater',
                        onClick: function(e){
                            
                            const oldItems           =  attributes.blog_update;  
                            const fieldname          = 'blog_update';
                            saswpRemoveRepeater(oldItems, fieldname, item);                                        
                        }    
                    }),
                    date_div,
                    el(TextControl,{
                        label:__('Headline', 'schema-and-structured-data-for-wp'),    
                        placeholder: __( 'Enter headline', 'schema-and-structured-data-for-wp' ),
                        value: item.headline,
                        onChange: function( value ) {                                
                                var newObject = Object.assign({}, item, {
                                  headline: value
                                });
                                saswp_lbp_on_item_change(newObject, item, 'blog_update');                            
                          }   
                    }),
                    el(RichText,{
                        tagName: 'p',
                        label:__('Body', 'schema-and-structured-data-for-wp'),
                        placeholder: __( 'Enter Body', 'schema-and-structured-data-for-wp' ),
                        value: item.body,
                        onChange: function(value){
                             var newObject = Object.assign({}, item, {
                                  body: value
                                });
                                saswp_lbp_on_item_change(newObject, item, 'blog_update'); 

                        }    
                    }),
                    el('div',{
                          className: 'saswp-lbplu-field-banner'
                        },

                        item.image_url ? 
                        el('div',{className:'saswp-lbplu-banner-div'},
                          el('span',{
                            className:'dashicons dashicons-trash',
                            onClick: function(){
                                var newObject = Object.assign({}, item, {
                                  image_id: null,
                                  image_url: '',
                                });
                              saswp_lbp_on_item_change(newObject, item, 'blog_update');
                            }
                          }),
                          el('img',{
                            src: item.image_url,
                          })
                        )
                    
                        :                         
                        el(
                            MediaUpload,{
                              onSelect: function(media){ 
                               var newObject = Object.assign({}, item, {
                                  image_id: media.id,
                                  image_url: media.url,
                                }); 
                                saswp_lbp_on_item_change(newObject, item, 'blog_update');
                              },
                              allowedTypes:[ "image" ],
                              value: item.image_id,
                              render:function(obj){
                                    return el( Button, {                         
                                         className: 'button button-primary',            
                                         onClick: obj.open
                                       },
                                     __('Add Image', 'schema-and-structured-data-for-wp')
                                 )
                                }
                            },
                        )
                    ),           
                ));
                
            });

            var blog_update = el('fieldset',{className:'saswp-live-blog-posting-update-fieldset'},el('div',{
                 className:'saswp-live-blog-posting-update-container'
                 },
                 el('h3',{},__('Live Blog Update', 'schema-and-structured-data-for-wp')),
                 blog_update_loop,
                 el(Button,{
                     className:'saswp-lbpup-repeater',
                     isSecondary: true,
                     isLarge : true,
                     onClick: function() {              
                        return props.setAttributes({
                          blog_update: [].concat(_cloneArray(props.attributes.blog_update), [{
                            index: props.attributes.blog_update.length                                                                                         
                          }])
                        });                            
                      }
                    },
                    __('Add Live Blog Update', 'schema-and-structured-data-for-wp')                            
                 ),                         
            ));

            function saswp_lbp_on_item_change(newObject, item, type){
                
                if(type == 'blog_update'){
                    return props.setAttributes({
                        blog_update: [].concat(_cloneArray(props.attributes.blog_update.filter(function (itemFilter) {
                          return itemFilter.index != item.index;
                        })), [newObject])
                  });
                }
                if(type == 'performers'){
                    return props.setAttributes({
                        performers: [].concat(_cloneArray(props.attributes.performers.filter(function (itemFilter) {
                          return itemFilter.index != item.index;
                        })), [newObject])
                  });
                }
                
                if(type == 'organizers'){
                    return props.setAttributes({
                        organizers: [].concat(_cloneArray(props.attributes.organizers.filter(function (itemFilter) {
                          return itemFilter.index != item.index;
                        })), [newObject])
                  });
                }
                
            }

            function _cloneArray(arr) { 
                if (Array.isArray(arr)) { 
                    for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { 
                      arr2[i] = arr[i]; 
                    } 
                    return arr2; 
                } else { 
                    return Array.from(arr); 
                } 
            }

            function saswpRemoveRepeater(oldItems, fieldname, item){
            
                const oldAttributes      =  attributes;                                  
                const newTestimonials    =  oldItems.filter(function(itemFilter){

                      return itemFilter.index != item.index
                   }).map(function(t){                                          
                        if (t.index > oldItems.index) {
                            t.index -= 1;
                        }
                        return t;
                   });

                   newTestimonials.forEach(function(value, index){                                                                                                                      
                      newTestimonials[index]['index'] = index;                                       
                   });

                   oldAttributes[fieldname] = newTestimonials;
                   props.setAttributes({
                     attributes: oldAttributes
                   });    
            
            }

            return [
                el(InspectorControls,{className:'saswp-liveblog-event-inspector'},
                    el(PanelBody,
                        {className:'saswp-event-panel-body',
                         title: __('Settings', 'schema-and-structured-data-for-wp')   
                        },
                        el(ToggleControl,
                        {
                            label : __('Add Event', 'schema-and-structured-data-for-wp'),    
                            className:'saswp-add-event',  
                            checked:attributes.event_details,
                            onChange: function(newContent){
                                props.setAttributes( { event_details: newContent } );
                            }                       
                        }
                    )                
                )),
                el(
                    'div',
                    { className: 'saswp-live-blog-posting-block' },
                    el( TextControl, {
                          className:'saswp-live-blog-posting-name',
                          placeholder: __( 'Enter blog name', 'schema-and-structured-data-for-wp' ), 
                          label: __( 'Blog Name', 'schema-and-structured-data-for-wp' ),
                          value: attributes.name,
                          onChange: function( newContent ) {                                
                              props.setAttributes( { name: newContent } );
                          }
                    }),
                    el( TextControl, {
                          className:'saswp-live-blog-posting-locationname',
                          placeholder: __('Enter location name', 'schema-and-structured-data-for-wp'), 
                          label: __( 'Location Name', 'schema-and-structured-data-for-wp' ),
                          value: attributes.locationname,
                          onChange: function( newContent ) {                                
                              props.setAttributes( { locationname: newContent } );
                          }
                    }),
                    el( TextControl, {
                          className:'saswp-live-blog-posting-address',
                          placeholder: __('Enter address', 'schema-and-structured-data-for-wp'), 
                          label: __( 'Address', 'schema-and-structured-data-for-wp' ),
                          value: attributes.address,
                          onChange: function( newContent ) {                                
                              props.setAttributes( { address: newContent } );
                          }
                    }),
                    el( TextControl, {
                          className:'saswp-live-blog-posting-locality',
                          placeholder: __('Enter locality', 'schema-and-structured-data-for-wp'), 
                          label: __( 'Locality', 'schema-and-structured-data-for-wp' ),
                          value: attributes.locality, 
                          onChange: function( newContent ) {                                
                              props.setAttributes( { locality: newContent } );
                          }
                    }),
                    el( TextControl, {
                          className:'saswp-live-blog-posting-postalcode',
                          placeholder: __('Enter postal code', 'schema-and-structured-data-for-wp'), 
                          label: __( 'Postal Code', 'schema-and-structured-data-for-wp' ),
                          value: attributes.postalcode,
                          onChange: function( newContent ) {                                
                              props.setAttributes( { postalcode: newContent } );
                          }
                    }),
                    el( TextControl, {
                          className:'saswp-live-blog-posting-region',
                          placeholder: __('Enter region', 'schema-and-structured-data-for-wp'), 
                          label: __( 'Region', 'schema-and-structured-data-for-wp' ),
                          value: attributes.region,
                          onChange: function( newContent ) {                                
                              props.setAttributes( { region: newContent } );
                          }
                    }),
                    el( TextControl, {
                          className:'saswp-live-blog-posting-country',
                          placeholder: __('Enter country', 'schema-and-structured-data-for-wp'), 
                          label: __( 'Country', 'schema-and-structured-data-for-wp' ),
                          value: attributes.country,
                          onChange: function( newContent ) {                                
                              props.setAttributes( { country: newContent } );
                          }
                    }),
                    coverage_start_date_div,
                    coverage_end_date_div,
                    eventDetails,
                    blog_update,
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