
( function( blocks, element, editor, components, i18n) {
            
    const el               = element.createElement;    
    const { __ }         = i18n;    
    const { RichText,  AlignmentToolbar, BlockControls, InspectorControls, MediaUpload } = editor;
    const {SelectControl, Popover, Button, IconButton,  TextControl, ToggleControl, PanelBody, DateTimePicker } = components;
                
    blocks.registerBlockType( 'saswp/event-block', {
        title: __('Event (SASWP)', 'schema-and-structured-data-for-wp'),
        icon: 'calendar',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'Event', 'event'],
        
        attributes:{
            description: {
                type: 'string'             
            },
            start_date: {
                type: 'string'             
            },
            start_time: {
                type: 'string'                
            },
            start_date_iso: {
                type: 'string'                
            },
            start_date_toggle: {
                type: 'boolean',
                default: false
            },
            end_date: {
                type: 'string'              
            },
            end_time: {
                type: 'string'                
            },
            end_date_iso: {
                type: 'string'                
            },
            end_date_toggle: {
                type: 'boolean',
                default: false
            },            
            previous_date: {
                type: 'string'              
            },
            previous_time: {
                type: 'string'                
            },
            previous_date_iso: {
                type: 'string'                
            },
            previous_date_toggle: {
                type: 'boolean',
                default: false
            },
            all_day: {
                type: 'boolean',
                default: false
            },
            website: {
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
            currency_code: {
                type: 'string',
                default: 'USD'
            },
            venue_name: {
                type: 'string'                
            },
            venue_address: {
                type: 'string'                
            },
            venue_city: {
                type: 'string'                
            },
            venue_country: {
                type: 'string'                
            },
            venue_state: {
                type: 'string'                
            },
            venue_postal_code: {
                type: 'string'                
            },
            venue_phone: {
                type: 'string'                
            },
            venue_website: {
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
        
        // Allow only one How To block per post.
        supports: {
                multiple: false
        },
                                             
        edit: function( props ) {
        
        var attributes = props.attributes; 
            
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
        
        function saswp_on_item_change(newObject, item, type){
            
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
        
        var previous_date_div = el('div',{
          className:'components-base-control'
        },
        el('div',{className:'components-base-control__field'},el('span', {
          className: 'components-base-control__label'
        }, 'Previous Date'),
            el('span',{className:'saswp-event-date-fields'},
            el(TextControl,{            
            className:'saswp-event-previous-date',
            value : attributes.previous_date,
            onClick:function(){
              props.setAttributes( { previous_date_toggle: true } );   
            }            
            }),
            !attributes.all_day ? 
            el(TextControl,{            
            className:'saswp-event-start-time',
            value : attributes.previous_time,
            onClick:function(){
              props.setAttributes( { previous_date_toggle: true } );   
            }            
            }) : ''            
            ),
            attributes.previous_date_toggle ? 
                el(
                Popover,{
                    class:'saswp-calender-popover',
                    position: 'bottom',
                    onClose: function(){
                       props.setAttributes( { previous_date_toggle: false } );     
                    }
                },
                el(DateTimePicker,{
                 currentDate: attributes.previous_date_iso,                 
                 is12Hour : true,
                 onChange: function(value){
                      attributes.previous_date_iso = value;
                      var newDate = moment(value).format('YYYY-MM-DD'); 
                      var newTime = moment(value).format('h:mm:ss a'); 
                       props.setAttributes( { previous_date: newDate } ); 
                       props.setAttributes( { previous_time: newTime } );                      
                  }
                })
                ) 
                : ''
            )
        );

        var start_date_div = el('div',{},el('span', {}, 'Start Date'),
            el('span',{className:'saswp-event-date-fields'},
            el(TextControl,{            
            className:'saswp-event-start-date',
            value : attributes.start_date,
            onClick:function(){
              props.setAttributes( { start_date_toggle: true } );   
            }            
            }),
            !attributes.all_day ? 
            el(TextControl,{            
            className:'saswp-event-start-time',
            value : attributes.start_time,
            onClick:function(){
              props.setAttributes( { start_date_toggle: true } );   
            }            
            }) : ''            
            ),
            attributes.start_date_toggle ? 
                el(
                Popover,{
                    class:'saswp-calender-popover',
                    position: 'bottom',
                    onClose: function(){
                       props.setAttributes( { start_date_toggle: false } );     
                    }
                },
                el(DateTimePicker,{
                 currentDate: attributes.start_date_iso,                 
                 is12Hour : true,
                 onChange: function(value){
                      attributes.start_date_iso = value;
                      var newDate = moment(value).format('YYYY-MM-DD'); 
                      var newTime = moment(value).format('h:mm:ss a'); 
                       props.setAttributes( { start_date: newDate } ); 
                       props.setAttributes( { start_time: newTime } ); 
                     
                 }
                })
                ) 
                : ''
                );
                
        var end_date_div = el('div',{},
            el('span', {}, 
            __('End Date', 'schema-and-structured-data-for-wp')),
            el('span',{className:'saswp-event-date-fields'},
            el(TextControl,{            
            className:'saswp-event-end-date',
            value : attributes.end_date,
            onClick:function(){
              props.setAttributes( { end_date_toggle: true } );   
            }            
            }),
            !attributes.all_day ? 
            el(TextControl,{            
            className:'saswp-event-end-time',
            value : attributes.end_time,
            onClick:function(){
              props.setAttributes( { end_date_toggle: true } );   
            }            
            }) : ''            
            ),
            attributes.end_date_toggle ? el(
                Popover,{
                    class:'saswp-calender-popover',
                    position: 'bottom',
                    onClose: function(){
                       props.setAttributes( { end_date_toggle: false } );     
                    }
                },
                el(DateTimePicker,{
                 currentDate: attributes.end_date_iso,                 
                 is12Hour : true,
                 onChange: function(value){
                      attributes.end_date_iso = value;
                      var newDate = moment(value).format('YYYY-MM-DD'); 
                      var newTime = moment(value).format('h:mm:ss a'); 
                       props.setAttributes( { end_date: newDate } ); 
                       props.setAttributes( { end_time: newTime } ); 
                     
                 }
                })) : ''  );        
                
                
        var event_details = el('fieldset',{
            className:'saswp-event-date-fieldset'},
                el( RichText, {                
                  tagName: 'p',
                  className:'saswp-event-fieldset-description',
                  placeholder: __('Enter event description', 'schema-and-structured-data-for-wp'),                   
                  value: attributes.description,
                  autoFocus: true, 
                  onChange: function( newContent ) {                                
                      props.setAttributes( { description: newContent } );
                  }
                }            
              ),
                start_date_div, end_date_div,
                el(TextControl,{
                    value : attributes.website,
                    label: __('Website', 'schema-and-structured-data-for-wp'),
                    onChange: function(value){
                         props.setAttributes( { website: value } ); 
                    }
                }),
                el(TextControl,{
                    value : attributes.price,
                    label: __('Price', 'schema-and-structured-data-for-wp'),
                    onChange: function(value){
                         props.setAttributes( { price: parseInt(value) } ); 
                    }
                }),
                el(TextControl,{
                    value : attributes.currency_code,
                    label: __('Currency Code', 'schema-and-structured-data-for-wp'),
                    onChange: function(value){
                         props.setAttributes( { currency_code: value } ); 
                    }
                }),
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
              attributes.event_status == 'EventRescheduled' ? previous_date_div : '',
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
            })
        );
                                     
        var venue = el('fieldset',{className:'saswp-event-venue-fieldset'},el('div',{className:'saswp-event-venue'},
                        el('h3',{},__('Location', 'schema-and-structured-data-for-wp')),
                        el(TextControl,{
                            label:__('Name', 'schema-and-structured-data-for-wp'),
                            value:attributes.venue_name,
                            onChange: function(value){
                                 props.setAttributes( { venue_name: value } );
                            }
                        }),
                        el(TextControl,{
                            label:__('Address', 'schema-and-structured-data-for-wp'),
                            value:attributes.venue_address,
                            onChange: function(value){
                                 props.setAttributes( { venue_address: value } );
                            }
                        }),
                        el(TextControl,{
                            label:__('City', 'schema-and-structured-data-for-wp'),
                            value:attributes.venue_city,
                            onChange: function(value){
                                 props.setAttributes( { venue_city: value } );
                            }
                        }),
                        el(TextControl,{
                            label:__('Country', 'schema-and-structured-data-for-wp'),
                            value:attributes.venue_country,
                            onChange: function(value){
                                 props.setAttributes( { venue_country: value } );
                            }
                        }),
                        el(TextControl,{
                            label:__('State Or Province', 'schema-and-structured-data-for-wp'),
                            value:attributes.venue_state,
                            onChange: function(value){
                                 props.setAttributes( { venue_state: value } );
                            }
                        }),
                        el(TextControl,{
                            label:__('Postal Code', 'schema-and-structured-data-for-wp'),
                            value:attributes.venue_postal_code,
                            onChange: function(value){
                                 props.setAttributes( { venue_postal_code: value } );
                            }
                        }),
                        el(TextControl,{
                            label:__('Phone', 'schema-and-structured-data-for-wp'),
                            value:attributes.venue_phone,
                            onChange: function(value){
                                 props.setAttributes( { venue_phone: value } );
                            }
                        }),
                        el(TextControl,{
                            label:__('Website', 'schema-and-structured-data-for-wp'),
                            value:attributes.venue_website,
                            onChange: function(value){
                                 props.setAttributes( { venue_website: value } );
                            }
                        })                                
                )); 
            
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
                            saswp_on_item_change(newObject, item, 'organizers');                            
                  }   
                }),
                el(TextControl,{
                    label:__('Phone', 'schema-and-structured-data-for-wp'),   
                    value: item.phone,
                    onChange: function(value){
                         var newObject = Object.assign({}, item, {
                              phone: value
                            });
                            saswp_on_item_change(newObject, item, 'organizers'); 
                    }    
                }),
                el(TextControl,{
                    label:__('Website', 'schema-and-structured-data-for-wp'),
                    value: item.website,
                    onChange: function(value){
                         var newObject = Object.assign({}, item, {
                              website: value
                            });
                            saswp_on_item_change(newObject, item, 'organizers'); 

                    }    
                }),
                el(TextControl,{
                    label:__('Email', 'schema-and-structured-data-for-wp'), 
                    value: item.email,
                    onChange: function(value){
                         var newObject = Object.assign({}, item, {
                              email: value
                            });
                            saswp_on_item_change(newObject, item, 'organizers'); 
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
                           saswp_on_item_change(newObject, item, 'performers'); 
                  }   
                }),
                el(TextControl,{
                    label:__('URL', 'schema-and-structured-data-for-wp'),   
                    value: item.url,
                    onChange: function(value){
                         var newObject = Object.assign({}, item, {
                              url: value
                            });
                            saswp_on_item_change(newObject, item, 'performers');
                    }    
                }),
                
                el(TextControl,{
                    label:__('Email', 'schema-and-structured-data-for-wp'), 
                    value: item.email,
                    onChange: function(value){
                         var newObject = Object.assign({}, item, {
                              email: value
                            });
                            saswp_on_item_change(newObject, item, 'performers');
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
            
                
        return [el(InspectorControls,{className:'saswp-event-inspector'},
                el(PanelBody,
                    {className:'saswp-event-panel-body',
                     title: __('Settings', 'schema-and-structured-data-for-wp')   
                    },
                    el(ToggleControl,
                    {
                        label : __('All Day Event', 'schema-and-structured-data-for-wp'),    
                        className:'saswp-event-all-day',  
                        checked:attributes.all_day,
                        onChange: function(newContent){
                            props.setAttributes( { all_day: newContent } );
                        }                       
                    }
                )                
                )),
                el('div',
                    {className:'saswp-event-block-container'},
                    event_details, venue, organizers, performers
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

