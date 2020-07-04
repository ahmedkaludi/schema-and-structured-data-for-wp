
( function( blocks, element, editor, components, i18n) {
            
    const el               = element.createElement;    
    const { __ }         = i18n;    
    const { RichText,  AlignmentToolbar, BlockControls, InspectorControls, MediaUpload } = editor;
    const {SelectControl, Popover, Button, IconButton,  TextControl, ToggleControl, PanelBody, DateTimePicker } = components;
                
    blocks.registerBlockType( 'saswp/qanda-block', {
        title: __('Q&A (SASWP)', 'schema-and-structured-data-for-wp'),
        icon: 'calendar',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'qanda', 'Q&A', 'Q and A'],
        
        attributes:{            
            question_date_created: {
                type: 'string'             
            },
            question_time_created: {
                type: 'string'                
            },
            question_date_created_iso: {
                type: 'string'                
            },
            question_date_created_toggle: {
                type: 'boolean',
                default: false
            },                                          
            question_name: {
                type: 'string'                
            },
            question_text: {
              type: 'string'                
            },
            question_up_vote: {
              type: 'string'                
            },
            question_author: {
              type: 'string'                
            },
            accepted_answers: {                     
              default: [{index: 0, text: '', vote: '', url: '', author: ''}],              
              query: {
                index: {            
                  type: 'number',                  
                  attribute: 'data-index'                  
                },
                isSelected: {            
                  type: 'boolean',
                  default:false      
                },   
                text: {
                  type: 'string'                                  
                },
                vote: {
                  type: 'string'                                    
                },
                url: {
                  type: 'string'                                    
                },
                author: {
                  type: 'string'                                    
                },
                date_created: {
                  type: 'string'             
                },
                time_created: {
                    type: 'string'                
                },
                date_created_iso: {
                    type: 'string'                
                },
                date_created_toggle: {
                    type: 'boolean',
                    default: false
                },
              }
            },                                    
            suggested_answers: {                     
              default: [{index: 0, text: '', vote: '', url: '', author: ''}],              
              query: {
                index: {            
                  type: 'number',                  
                  attribute: 'data-index'                  
                },
                isSelected: {            
                  type: 'boolean',
                  default:false      
                },   
                text: {
                  type: 'string'                                  
                },
                vote: {
                  type: 'string'                                    
                },
                url: {
                  type: 'string'                                    
                },
                author: {
                  type: 'string'                                    
                },
                date_created: {
                  type: 'string'             
                },
                time_created: {
                    type: 'string'                
                },
                date_created_iso: {
                    type: 'string'                
                },
                date_created_toggle: {
                    type: 'boolean',
                    default: false
                },
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
            
            if(type == 'suggested_answers'){
              return props.setAttributes({
                suggested_answers: [].concat(_cloneArray(props.attributes.suggested_answers.filter(function (itemFilter) {
                    return itemFilter.index != item.index;
                  })), [newObject])
              });
            }
            
            if(type == 'accepted_answers'){
                return props.setAttributes({
                  accepted_answers: [].concat(_cloneArray(props.attributes.accepted_answers.filter(function (itemFilter) {
                      return itemFilter.index != item.index;
                    })), [newObject])
              });
            }
            
        }

        function saswp_answer_date_picker(item, answer_type){

          var start_date_div = el('div',{},el('span', {}, 'Date Created'),
            el('span',{className:'saswp-qanda-date-fields'},
            el(TextControl,{            
            className:'saswp-qanda-start-date',
            value : item.date_created,
            onClick:function(){
              var newObject = Object.assign({}, item, {
                date_created_toggle: true
              });
              saswp_on_item_change(newObject, item, answer_type);                                            
            }            
            }),            
            el(TextControl,{            
            className:'saswp-qanda-start-time',
            value : item.time_created,
            onClick:function(){
              var newObject = Object.assign({}, item, {
                date_created_toggle: true
              });
              saswp_on_item_change(newObject, item, answer_type);                                            
            }            
            })             
            ),
            item.date_created_toggle ? 
                el(
                Popover,{
                    class:'saswp-calender-popover',
                    position: 'bottom',
                    onClose: function(){
                      var newObject = Object.assign({}, item, {
                        date_created_toggle: false
                      });
                      saswp_on_item_change(newObject, item, answer_type);                                            
                    }
                },
                el(DateTimePicker,{
                 currentDate: item.date_created_iso,                 
                 is12Hour : true,
                 onChange: function(value){
                      item.date_created_iso = value;
                      var newDate = moment(value).format('YYYY-MM-DD'); 
                      var newTime = moment(value).format('h:mm:ss a'); 

                      var newObject = Object.assign({}, item, {
                        date_created: newDate,
                        time_created: newTime
                      });
                      saswp_on_item_change(newObject, item, answer_type);    
                                                                 
                 }
                })
                ) 
                : ''
                );

              return start_date_div;
        }
        
        var start_date_div = el('div',{},el('span', {}, 'Date Created'),
            el('span',{className:'saswp-qanda-date-fields'},
            el(TextControl,{            
            className:'saswp-qanda-start-date',
            value : attributes.question_date_created,
            onClick:function(){
              props.setAttributes( { question_date_created_toggle: true } );   
            }            
            }),            
            el(TextControl,{            
            className:'saswp-qanda-start-time',
            value : attributes.question_time_created,
            onClick:function(){
              props.setAttributes( { question_date_created_toggle: true } );   
            }            
            })             
            ),
            attributes.question_date_created_toggle ? 
                el(
                Popover,{
                    class:'saswp-calender-popover',
                    position: 'bottom',
                    onClose: function(){
                       props.setAttributes( { question_date_created_toggle: false } );     
                    }
                },
                el(DateTimePicker,{
                 currentDate: attributes.question_date_created_iso,                 
                 is12Hour : true,
                 onChange: function(value){
                      attributes.question_date_created_iso = value;
                      var newDate = moment(value).format('YYYY-MM-DD'); 
                      var newTime = moment(value).format('h:mm:ss a'); 
                       props.setAttributes( { question_date_created: newDate } ); 
                       props.setAttributes( { question_time_created: newTime } ); 
                     
                 }
                })
                ) 
                : ''
                );
                                                     
        var qanda_details = el('fieldset',{
            className:'saswp-qanda-date-fieldset'},                               
                el(TextControl,{
                    value : attributes.question_name,
                    label: __('Question Name', 'schema-and-structured-data-for-wp'),
                    onChange: function(value){
                         props.setAttributes( { question_name: value } ); 
                    }
                }),
                el(TextControl,{
                    value : attributes.question_text,
                    type  : "textarea", 
                    label : __('Question Text', 'schema-and-structured-data-for-wp'),
                    onChange: function(value){
                         props.setAttributes( { question_text: value } ); 
                    }
                }),                
                el(TextControl,{
                  value : attributes.question_up_vote,
                  type : 'number',
                  label: __('Up Vote Count', 'schema-and-structured-data-for-wp'),
                  onChange: function(value){
                       props.setAttributes( { question_up_vote: value } ); 
                  }
              }),
              el(TextControl,{
                value : attributes.question_author,                
                label: __('Author', 'schema-and-structured-data-for-wp'),
                onChange: function(value){
                     props.setAttributes( { question_author: value } ); 
                }
              }),
              start_date_div                              
        );
                                            
            
        var organizers_loop =  attributes.accepted_answers.sort(function(a, b){
            return a.index - b.index;            
        }).map(function(item){
            
            return el('fieldset',{className:'saswp-qanda-organisers-fieldset'},el('div',{className:'saswp-qanda-organisers'},
                el(IconButton,{
                icon:'trash',
                className: 'saswp-remove-repeater',
                onClick: function(e){
                    
                    const oldItems           =  attributes.accepted_answers;  
                    const fieldname          = 'accepted_answers';
                    saswpRemoveRepeater(oldItems, fieldname, item);                                        
                }    
                }),
                el(TextControl,{
                label:__('Text', 'schema-and-structured-data-for-wp'),    
                value: item.text,
                type : "textarea",
                onChange: function( value ) {                                
                            var newObject = Object.assign({}, item, {
                              text: value
                            });
                            saswp_on_item_change(newObject, item, 'accepted_answers');                            
                  }   
                }),
                el(TextControl,{
                    label:__('Up Vote Count', 'schema-and-structured-data-for-wp'),   
                    value: item.vote,
                    type : 'number',
                    onChange: function(value){
                         var newObject = Object.assign({}, item, {
                              vote: value
                            });
                            saswp_on_item_change(newObject, item, 'accepted_answers'); 
                    }    
                }),
                el(TextControl,{
                    label:__('URL', 'schema-and-structured-data-for-wp'),
                    value: item.url,
                    onChange: function(value){
                         var newObject = Object.assign({}, item, {
                              url: value
                            });
                            saswp_on_item_change(newObject, item, 'accepted_answers'); 

                    }    
                }),
                el(TextControl,{
                    label:__('Author', 'schema-and-structured-data-for-wp'), 
                    value: item.author,
                    onChange: function(value){
                         var newObject = Object.assign({}, item, {
                              author: value
                            });
                            saswp_on_item_change(newObject, item, 'accepted_answers'); 
                    }    
                }),
                saswp_answer_date_picker(item, 'accepted_answers')            
            ));
            
        });

        var suggested_loop =  attributes.suggested_answers.sort(function(a, b){
          return a.index - b.index;            
      }).map(function(item){
          
          return el('fieldset',{className:'saswp-qanda-organisers-fieldset'},el('div',{className:'saswp-qanda-organisers'},
              el(IconButton,{
              icon:'trash',
              className: 'saswp-remove-repeater',
              onClick: function(e){
                  
                  const oldItems           =  attributes.suggested_answers;  
                  const fieldname          = 'suggested_answers';
                  saswpRemoveRepeater(oldItems, fieldname, item);                                        
              }    
              }),
              el(TextControl,{
              label:__('Text', 'schema-and-structured-data-for-wp'),    
              value: item.text,
              type : "textarea",
              onChange: function( value ) {                                
                          var newObject = Object.assign({}, item, {
                            text: value
                          });
                          saswp_on_item_change(newObject, item, 'suggested_answers');                            
                }   
              }),
              el(TextControl,{
                  label:__('Up Vote Count', 'schema-and-structured-data-for-wp'),   
                  value: item.vote,
                  type : 'number',
                  onChange: function(value){
                       var newObject = Object.assign({}, item, {
                            vote: value
                          });
                          saswp_on_item_change(newObject, item, 'suggested_answers'); 
                  }    
              }),
              el(TextControl,{
                  label:__('URL', 'schema-and-structured-data-for-wp'),
                  value: item.url,
                  onChange: function(value){
                       var newObject = Object.assign({}, item, {
                            url: value
                          });
                          saswp_on_item_change(newObject, item, 'suggested_answers'); 

                  }    
              }),
              el(TextControl,{
                  label:__('Author', 'schema-and-structured-data-for-wp'), 
                  value: item.author,
                  onChange: function(value){
                       var newObject = Object.assign({}, item, {
                            author: value
                          });
                          saswp_on_item_change(newObject, item, 'suggested_answers'); 
                  }    
              }),
              saswp_answer_date_picker(item, 'suggested_answers')            
          ));
          
      });
        
        var organizers = el('fieldset',{className:'saswp-qanda-organisers-fieldset'},el('div',{
                         className:'saswp-qanda-organisers-container'
                         },
                         el('h3',{},__('Accepted Answer', 'schema-and-structured-data-for-wp')),
                         organizers_loop,
                         el(Button,{
                             className:'saswp-org-repeater',
                             isSecondary: true,
                             isLarge : true,
                             onClick: function() {              
                                return props.setAttributes({
                                  accepted_answers: [].concat(_cloneArray(props.attributes.accepted_answers), [{
                                    index: props.attributes.accepted_answers.length                                                                                         
                                  }])
                                });                            
                              }
                            },
                            __('Add More Accepted Answer', 'schema-and-structured-data-for-wp')                            
                         ),                         
                        ));                                             
              var suggested = el('fieldset',{className:'saswp-qanda-organisers-fieldset'},el('div',{
                className:'saswp-qanda-organisers-container'
                },
                el('h3',{},__('Suggested Answer', 'schema-and-structured-data-for-wp')),
                suggested_loop,
                el(Button,{
                    className:'saswp-org-repeater',
                    isSecondary: true,
                    isLarge : true,
                    onClick: function() {              
                        return props.setAttributes({
                          suggested_answers: [].concat(_cloneArray(props.attributes.suggested_answers), [{
                            index: props.attributes.suggested_answers.length                                                                                         
                          }])
                        });                            
                      }
                    },
                    __('Add More Suggested Answer', 'schema-and-structured-data-for-wp')                            
                ),                         
                ));                                                           
                
        return [el(InspectorControls,{className:'saswp-qanda-inspector'},
                el(PanelBody,
                    {className:'saswp-qanda-panel-body',
                     title: __('Settings', 'schema-and-structured-data-for-wp')   
                    },                                 
                )),
                el('div',
                    {className:'saswp-qanda-block-container'},
                    qanda_details, organizers, suggested
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

