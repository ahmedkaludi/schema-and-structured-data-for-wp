
( function( blocks, element, editor, components, i18n) {
            
  const el               = element.createElement;    
  const { __ }           = i18n;    
  const { RichText, MediaUpload } = editor;
  const {Button, TextControl} = components;
              
  blocks.registerBlockType( 'saswp/recipe-block', {
      title: __('Recipe (SASWP)', 'schema-and-structured-data-for-wp'),
      icon:     'dashicons dashicons-food',
      category: 'saswp-blocks',
      keywords: ['schema', 'structured data', 'Recipe', 'recipe'],
                   
      supports: {
              multiple: true
      },
      
      attributes: {                                                                                                                     
              servings: {
                type: 'string'                  
              },
              cook_time: {
                type: 'string'                  
              },
              pre_time: {
                type: 'string'                  
              },
              calories: {
                type: 'string'                  
              },
              banner_id: {
                type: 'integer'                  
              },
              banner_url: {
                type: 'string'                  
              },              
              video_id: {
                type: 'integer'                  
              },
              video_url: {
                type: 'string'                  
              },
              notes_label: {
                type: 'string',
                default: 'NOTES'                  
              },              
              title: {
                type: 'string',
                default: 'Recipe Block'                  
              }, 
              direction_label: {
                type: 'string',
                default: 'DIRECTION'                  
              }, 
              ingredients_label: {
                type: 'string',
                default: 'INGREDIENTS '                  
              },                       
              author: {
                type: 'string',                
              },
              course: {
                type: 'string'                 
              },
              cuisine: {
                type: 'string'                 
              },
              difficulty: {
                type: 'string'                 
              },     
              notes: {                     
                default: [{index: 0, text:''}],              
                query: {

                  index: {       
                    type:      'number',                  
                    attribute: 'data-index'                  
                  },                  
                  text: {
                    type: 'string'                                  
                  }

                }
              },
              ingredients: {                     
                default: [{index: 0,name:''}],              
                query: {
                  index: {       
                    type:      'number',                  
                    attribute: 'data-index'                  
                  },                     
                  name: {
                    type: 'string',                                                     
                  }
                }
              },
              directions: {                     
                default: [{index: 0, name:'', text:''}],              
                query: {
                  index: {       
                    type:      'number',                  
                    attribute: 'data-index'                  
                  },                  
                  name: {
                    type: 'string',
                    selector: '.name'                                  
                  },   
                  text: {
                    type: 'string',
                    selector: '.text'                                  
                  }
                }
              }                                                                                                            
      },                                                        
      edit: function( props ){
         
        const attributes = props.attributes;

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
        
        var banner_section = el('div',{
          className: 'saswp-recipe-field-banner'
        },

        attributes.banner_url ? 
        el('div',{className:'saswp-book-banner-div'},
          el('span',{
            className:'dashicons dashicons-trash',
            onClick: function(){
              props.setAttributes( { banner_id: null, banner_url: '' } );
            }
          }),
          el('img',{
            src: attributes.banner_url,
          })
        )
        
        :                         
          el(
            MediaUpload,{
              onSelect: function(media){  
                props.setAttributes( { banner_id: media.id, banner_url: media.url } );
              },
              allowedTypes:[ "image" ],
              value: attributes.banner_id,
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

        );

        var heading_section = el(
          'div',{
            className: 'saswp-recipe-block-heading'
          },
          el( RichText, {                
            tagName: 'h4',            
            placeholder: __('Recipe Block', 'schema-and-structured-data-for-wp'),                   
            value: attributes.title,
            autoFocus: true, 
            onChange: function( newContent ) {                                
                props.setAttributes( { title: newContent } );
            }
          }),
          el('span',{className: 'saswp-recipe-block-author'},'Recipe By ',
          el(TextControl,{
            className:'',
            placeholder: __('author name', 'schema-and-structured-data-for-wp'),
            value: attributes.author,
            onChange: function(newContent){
              props.setAttributes({author: newContent})
            }
          })
          ),                    
          el('div',{className:'saswp-r-course-section'},
          el('span',{className: 'saswp-recipe-block-course'},'Course: ',el('strong',{},
          el(TextControl,{
            className:'',placeholder: __('Starter', 'schema-and-structured-data-for-wp'),
            value: attributes.course,
            onChange: function( newContent ) {                                
              props.setAttributes( { course: newContent } );
            }
            },
            )
          )),
          el('span',{className: 'saswp-recipe-block-cuisine'},'Cusine: ',el('strong',{},
          el(TextControl,{
            className:'',placeholder: __('American', 'schema-and-structured-data-for-wp'),
            value: attributes.cuisine,
            onChange: function( newContent ) {                                
              props.setAttributes( { cuisine: newContent } );
            }
          })
          )),
          el('span',{className: 'saswp-recipe-block-difficulty'},'Difficulty: ',el('strong',{},
          el(TextControl,
            {
            className:'',            
            placeholder: __('Easy', 'schema-and-structured-data-for-wp'),
            value: attributes.difficulty,
            onChange: function( newContent ) {                                
              props.setAttributes( { difficulty: newContent } );
            }
            }
            )
          ))
          )


        );
       
        var details_section = el(
          'div',{
            className: 'saswp-recipe-block-details'
          },
          el('div',{className:'saswp-recipe-block-details-items'},
          
            el('div', {className:'saswp-recipe-block-details-item'},
              
              el('p', {className:'saswp-r-b-label'}, 'Servings'),
              el(TextControl,{            
                className:'saswp-r-b-input',
                value : attributes.servings,
                placeholder: __('30', 'schema-and-structured-data-for-wp'),
                onChange:function(newContent){
                  props.setAttributes( { servings: newContent } );
                }            
              }),
              el('p', {className:'saswp-r-b-unit'}, 'minutes'),
            ),
             
            el('div', {className:'saswp-recipe-block-details-item'},
            
            el('p', {className:'saswp-r-b-label'}, 'Prep Time'),
            el(TextControl,{            
              className:'saswp-r-b-input',
              value : attributes.pre_time,
              placeholder: __('30', 'schema-and-structured-data-for-wp'),
              onChange:function(newContent){
                props.setAttributes( { pre_time: newContent } );
              }            
            }),
            el('p', {className:'saswp-r-b-unit'}, 'minutes'),
          ),
           
            el('div', {className:'saswp-recipe-block-details-item'},
            
            el('p', {className:'saswp-r-b-label'}, 'Cooking Time'),
            el(TextControl,{            
              className:'saswp-r-b-input',
              value : attributes.cook_time,
              placeholder: __('20', 'schema-and-structured-data-for-wp'),
              onChange:function(newContent){
                props.setAttributes( { cook_time: newContent } );
              }           
            }),
            el('p', {className:'saswp-r-b-unit'}, 'minutes'),
          ),
            
            el('div', {className:'saswp-recipe-block-details-item'},
            
            el('p', {className:'saswp-r-b-label'}, 'Calories'),
            el(TextControl,{            
              className:'saswp-r-b-input',
              value : attributes.calories,
              placeholder: __('300', 'schema-and-structured-data-for-wp'),
              onChange:function(newContent){
                props.setAttributes( { calories: newContent } );
              }           
            }),
            el('p', {className:'saswp-r-b-unit'}, 'kcal'),
          )
                            
          )
        );

        var ingredients_loop =  attributes.ingredients.sort(function(a, b){
          return a.index - b.index;            
          }).map(function(item, i){
          
          return el('li',{className:'saswp-r-b-direction-item'},
              el(Button,{
              icon:'trash', 
              'data-id': i,
              className: 'saswp-remove-repeater',
              onClick: function(e){
                   
                    let data_id    = e.currentTarget.dataset.id;
                    let clonedata  = [...attributes.ingredients];                    
                      clonedata.splice(data_id, 1);
                      props.setAttributes( { ingredients: clonedata } );
                                                                           
              }    
              }),
              el(RichText,{
                tagName: 'p', 
                className: "saswp-recipe-b-directions", 
                placeholder: __('Enter ingredients name', 'schema-and-structured-data-for-wp'),               
                value: item.name,
                autoFocus: true,
                onChange: function( value ) {                                
                            var newObject = Object.assign({}, item, {
                              name: value
                            });
                   
                            return props.setAttributes({
                              ingredients: [].concat(_cloneArray(props.attributes.ingredients.filter(function (itemFilter) {
                                return itemFilter.index != item.index;
                              })), [newObject])
                        });
                  }   
                })                                                    
          );
          
      }); 

        var ingredients_section = el(
          'div',{
            className: 'saswp-recipe-block-ingredients'
          },
          el( RichText, {                
            tagName: 'h5',            
            placeholder: __('INGREDIENTS', 'schema-and-structured-data-for-wp'),                   
            value: attributes.ingredients_label,
            autoFocus: true, 
            onChange: function( newContent ) {                                
                props.setAttributes( { ingredients_label: newContent } );
            }
          }),
          el('ol',{className:'saswp-dirction-ul'},ingredients_loop),
          el(Button,
            {className:'saswp-org-repeater',
            isSecondary: true,
            isLarge : true,
            onClick: function() {              
              return props.setAttributes({
                ingredients: [].concat(_cloneArray(props.attributes.ingredients), [{
                  index: props.attributes.ingredients.length                                                                                         
                }])
              });                            
            }
            },
            __('Add More Ingredients', 'schema-and-structured-data-for-wp')
            )          
        );
        

        var directions_loop =  attributes.directions.sort(function(a, b){
          return a.index - b.index;            
          }).map(function(item, i){
          
          return el('li',{className:'saswp-r-b-direction-item'},  
          el(Button,{
            icon:'trash', 
            'data-id': i,
            className: 'saswp-remove-repeater',
            onClick: function(e){
                 
                  let data_id    = e.currentTarget.dataset.id;
                  let clonedata  = [...attributes.directions];                    
                    clonedata.splice(data_id, 1);
                    props.setAttributes( { directions: clonedata } );
                                                                         
            }    
            }),            
              el(RichText,{
                tagName: 'p', 
                className: "saswp-recipe-b-directions", 
                placeholder: __('Enter direction name', 'schema-and-structured-data-for-wp'),              
                value: item.name,
                onChange: function( value ) {                                
                            var newObject = Object.assign({}, item, {
                              name: value
                            });
                   
                            return props.setAttributes({
                              directions: [].concat(_cloneArray(props.attributes.directions.filter(function (itemFilter) {
                                return itemFilter.index != item.index;
                              })), [newObject])
                        });
                  }   
                }),
              el(RichText,{
              tagName: 'p', 
              className: "saswp-recipe-b-directions",                
              value: item.text,
              placeholder: __('Enter direction text', 'schema-and-structured-data-for-wp'),
              onChange: function( value ) {                                
                          var newObject = Object.assign({}, item, {
                            text: value
                          });
                 
                          return props.setAttributes({
                            directions: [].concat(_cloneArray(props.attributes.directions.filter(function (itemFilter) {
                              return itemFilter.index != item.index;
                            })), [newObject])
                      });
                }   
              }),                                      
          );
          
      }); 

        var direction_section = el(
          'div',{
            className: 'saswp-recipe-block-direction'
          },
          el( RichText, {                
            tagName: 'h5',            
            placeholder: __('DIRECTION', 'schema-and-structured-data-for-wp'),                   
            value: attributes.direction_label,
            autoFocus: true, 
            onChange: function( newContent ) {                                
                props.setAttributes( { direction_label: newContent } );
            }
          }),
          el('ol',{className:'saswp-dirction-ul'},directions_loop),
          el(Button,
            {className:'saswp-org-repeater',
            isSecondary: true,
            isLarge : true,
            onClick: function() {              
              return props.setAttributes({
                directions: [].concat(_cloneArray(props.attributes.directions), [{
                  index: props.attributes.directions.length                                                                                         
                }])
              });                            
            }
            },
            __('Add More Direction', 'schema-and-structured-data-for-wp')
            )
        );
                
        var notes_loop =  attributes.notes.sort(function(a, b){
          return a.index - b.index;            
          }).map(function(item, i){
          
          return el('div',{className:'saswp-event-performers'},
              el(Button,{
              icon:'trash', 
              'data-id': i,
              className: 'saswp-remove-repeater',
              onClick: function(e){
                   
                    let data_id    = e.currentTarget.dataset.id;
                    let clonedata  = [...attributes.notes];                    
                      clonedata.splice(data_id, 1);
                      props.setAttributes( { notes: clonedata } );
                                                                           
              }    
              }),
              el(RichText,{
              tagName: 'p', 
              className: "saswp-recipe-b-notes",                
              value: item.text,
              onChange: function( value ) {                                
                          var newObject = Object.assign({}, item, {
                            text: value
                          });
                 
                          return props.setAttributes({
                            notes: [].concat(_cloneArray(props.attributes.notes.filter(function (itemFilter) {
                              return itemFilter.index != item.index;
                            })), [newObject])
                      });
                }   
              })                                      
          );
          
      });     

        var notes_section = el('div',
          {
            className: 'saswp-recipe-block-notes'
          },
          el( RichText, {                
            tagName: 'h5',            
            placeholder: __('NOTES', 'schema-and-structured-data-for-wp'),                   
            value: attributes.notes_label,
            autoFocus: true, 
            onChange: function( newContent ) {                                
                props.setAttributes( { notes_label: newContent } );
            }
          }),
          notes_loop,
          el(Button,
            {className:'saswp-org-repeater',
            isSecondary: true,
            isLarge : true,
            onClick: function() {              
              return props.setAttributes({
                notes: [].concat(_cloneArray(props.attributes.notes), [{
                  index: props.attributes.notes.length                                                                                         
                }])
              });                            
            }
            },
            __('Add More Notes', 'schema-and-structured-data-for-wp')
            )
          )

        return (
          el('div', {className: 'saswp-recipe-block-container'},          
            banner_section,
            heading_section,
            details_section,
            ingredients_section,
            direction_section,                        
            notes_section
          )
        );

  },
  save: function( props ) {
      return null                        
  }
});
}(
  window.wp.blocks,
  window.wp.element,
  window.wp.blockEditor,
  window.wp.components,
  window.wp.i18n,    
) );

