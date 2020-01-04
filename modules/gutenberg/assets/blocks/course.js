
( function( blocks, element, editor, components, i18n) {
            
    const el               = element.createElement;    
    const { __ }           = i18n;    
    const { RichText,  AlignmentToolbar, BlockControls, InspectorControls, MediaUpload } = editor;
    const {RadioControl, Popover, Button, IconButton,  TextareaControl, TextControl, ToggleControl, PanelBody, DateTimePicker } = components;
                
    blocks.registerBlockType( 'saswp/course-block', {
        title: __('Course (SASWP)', 'schema-and-structured-data-for-wp'),
        icon:     'welcome-learn-more',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'Course', 'course'],
             
        // Allow only one How To block per post.
        supports: {
                multiple: false
        },
        
        attributes: {                                               
            courses: {                     
              default: [{index: 0}],              
              query: {
                name: {
                  type: 'string'                  
                },
                description: {
                  type: 'string'                                    
                },
                image_id: {
                  type: 'integer'                
                },
                image_url: {
                  type: 'string'                 
                },
                provider_name: {
                  type: 'string'                 
                },
                provider_website: {
                  type: 'string'                 
                },                
                index: {            
                  type: 'number',                  
                  attribute: 'data-index'            
                },
                isSelected: {            
                  type: 'boolean',
                  default:false      
                }
              }
            }       
        },                                                      
        edit: function( props ){
            
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
            
            var course_list = attributes.courses.sort(function(a , b){
                 return a.index - b.index;
            }).map(function(course){ 
                
                return el('li',{
                    className: 'course'
                },
                el('fieldset',{className:'saswp-course-fieldset'},
                el(IconButton,{
                icon:'trash',
                className: 'saswp-remove-repeater',
                onClick: function(){                    
                    const oldItems           =  attributes.courses;  
                    const fieldname          = 'courses';
                    saswpRemoveRepeater(oldItems, fieldname, course);                                        
                }    
                }),
                el(TextControl,{            
                    className:'saswp-course-name',
                    label : __('Name', 'schema-and-structured-data-for-wp'),
                    value : course.name,
                    onChange: function( value ) {                                
                            var newObject = Object.assign({}, course, {
                              name: value
                            });
                            return props.setAttributes({
                              courses: [].concat(_cloneArray(props.attributes.courses.filter(function (itemFilter) {
                                return itemFilter.index != course.index;
                              })), [newObject])
                            });
                          }            
                }),
                el(MediaUpload,{
                    className:'saswp-course-image',
                    allowedTypes:[ "image" ],
                    value: course.image_id,
                    onSelect: function(value){
                            
                            var newObject = Object.assign({}, course, {
                              image_id: value.id,
                              image_url: value.url
                            });
                            return props.setAttributes({
                              courses: [].concat(_cloneArray(props.attributes.courses.filter(function (itemFilter) {
                                return itemFilter.index != course.index;
                              })), [newObject])
                            });
                                                              
                    },
                    render:function(obj){
                            
                                 var render_res;                                                                                  
                                 if(course.image_url){
                                     
                                    render_res =      el('div',{
                                      className:'saswp-course-image-panel'},
                                      el('img',{
                                      src:course.image_url, 
                                      onClick: obj.open,
                                     }),
                                     el(Button,{
                                         className:'saswp-remove is-link',
                                         isDestructive : true,
                                         onClick: function(){
                                              var newObject = Object.assign({}, course, {
                                                image_id: null,
                                                image_url: ''
                                              });
                                              return props.setAttributes({
                                                courses: [].concat(_cloneArray(props.attributes.courses.filter(function (itemFilter) {
                                                  return itemFilter.index != course.index;
                                                })), [newObject])
                                              });
                                         }
                                     },
                                     __('Remove Image', 'schema-and-structured-data-for-wp')
                                     )
                                     );
                                 }else{
                                     
                                  render_res =    el( Button, {                                    
                                    isSecondary: true,
                                    className: 'editor-post-featured-image__toggle',            
                                    onClick: obj.open
                                  },
                                    __('Set Image', 'schema-and-structured-data-for-wp')
                                 );
                                 }
                                 return render_res;
                                                
                           }
                         }
                        ),
                el(TextareaControl,{            
                    className:'saswp-course-description',
                    label : __('Description', 'schema-and-structured-data-for-wp'),    
                    value : course.description,
                    onChange: function( value ) {                                
                            var newObject = Object.assign({}, course, {
                              description: value
                            });
                            return props.setAttributes({
                              courses: [].concat(_cloneArray(props.attributes.courses.filter(function (itemFilter) {
                                return itemFilter.index != course.index;
                              })), [newObject])
                            });
                          }            
                }),
                el(TextControl,{            
                    className:'saswp-course-provider-name',
                    label : __('Provider Name', 'schema-and-structured-data-for-wp'),
                    value : course.provider_name,
                    onChange: function( value ) {                                
                            var newObject = Object.assign({}, course, {
                              provider_name: value
                            });
                            return props.setAttributes({
                              courses: [].concat(_cloneArray(props.attributes.courses.filter(function (itemFilter) {
                                return itemFilter.index != course.index;
                              })), [newObject])
                            });
                          }            
                }),
                el(TextControl,{            
                    className:'saswp-course-provider-website',
                    label : __('Provider Website', 'schema-and-structured-data-for-wp'),
                    value : course.provider_website,
                    onChange: function( value ) {                                
                            var newObject = Object.assign({}, course, {
                              provider_website: value
                            });
                            return props.setAttributes({
                              courses: [].concat(_cloneArray(props.attributes.courses.filter(function (itemFilter) {
                                return itemFilter.index != course.index;
                              })), [newObject])
                            });
                          }            
                }),
                
                )
                )
                
            });
            
            var course_data = el('div',{
                className:'saswp-course-list'
                },
                el('ul',{className:'saswp-course-ul'},
                course_list
                ),
                el(Button,{
                    className:'saswp-course-repeater',
                    isSecondary: true,
                    isLarge : true,
                    onClick: function() {              
                       return props.setAttributes({
                         courses: [].concat(_cloneArray(props.attributes.courses), [{
                           index: props.attributes.courses.length                                                                                         
                         }])
                       });                            
                     }
                   },
                   __('Add More Course', 'schema-and-structured-data-for-wp')                            
                 )
                );
            
            return [
                el(InspectorControls,{
                 className: 'saswp-course-inspector',                 
                },                                
                ),
                el('div',{className:'saswp-course-wrapper'},
                course_data
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

