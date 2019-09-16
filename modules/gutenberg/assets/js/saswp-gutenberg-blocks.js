( function( blocks, element, editor, components ) {
    var el               = element.createElement;
    var RichText         = editor.RichText;
    var MediaUpload      = editor.MediaUpload;   
    var Button           = components.Button;
    var IconButton       = components.IconButton;
    var AlignmentToolbar = editor.AlignmentToolbar;
    var BlockControls    = editor.BlockControls;
    
    blocks.registerBlockType( 'saswp-gutenberg-blocks-namsp/how-to-block', {
        title: saswpGutenberg.title,
        icon: 'editor-ol',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'how to'],
        
        // Allow only one How-To block per post.
        supports: {
                multiple: false
        },
        
        attributes: {
            alignment: {
                type: 'string',
                default: 'none',
            },
            items: {        
              source: 'query',
              default: [],
              selector: '.item',
              query: {
                title: {
                  type: 'string',
                  source: 'text',
                  selector: '.title'
                },
                description: {
                  type: 'string',
                  source: 'text',
                  selector: '.description'
                },
                index: {            
                  type: 'number',
                  source: 'attribute',
                  attribute: 'data-index'            
                }           
              }
            }       
          },               
        edit: function(props) {
            
            var attributes = props.attributes;
            var alignment  = props.attributes.alignment;
              
              
            //List of function for the current blocks starts here
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
            
            function saswpGetButtons(item){
                              					
                return el('div', {className:'saswp-how-to-step-button-container'},
                
                        el(MediaUpload, {
                            onSelect: function(media){  
                                    
                            // console.log(item); 
                            //will do later 
                                
                            },
                           allowedTypes:[ "image" ],
                           value: item.id,
                           render:function(obj){
                               return el( IconButton, {
                                    icon: "insert",
                                    className: 'saswp-how-to-step-button saswp-to-step-add-media',            
                                    onClick: obj.open
                                  },
                                'Add Image'
                            )
                           }
                        }),
                        el( IconButton, {
                            icon: "trash",
                            className: 'saswp-how-to-step-button',            
                            onClick: function() { 
                                
                                 const oldAttributes      =  attributes; 
                                 const oldItems           =  attributes.items;  
                                 const newTestimonials    =  oldItems
                                 
                                    .filter(function(itemFilter){
                                       return itemFilter.index != item.index
                                    }).map(function(t){                                          
                                         if (t.index > oldItems.index) {
                                             t.index -= 1;
                                         }
                                         return t;
                                    });
                                    
                                    newTestimonials.forEach(function(value, index){                                        
                                       newTestimonials[index]['title']       = value['title'];
                                       newTestimonials[index]['description'] = value['description'];
                                       newTestimonials[index]['index']       = index;
                                    });
                                    
                                    oldAttributes['items'] = newTestimonials;
                                    props.setAttributes({
                                      attributes: oldAttributes
                                    });                                
                                
                            }
                          }                          
                        ),
                        el( IconButton, {
                            icon: "insert",
                            className: 'saswp-how-to-step-button',            
                            onClick: function() {              
                                
                                const oldAttributes      =  attributes;
                                const oldItems           =  attributes.items;                                
                                                                
                                const insertitem = {title:"",description:"",index:item.index};
                                      oldItems.splice(parseInt(item.index)+1, 0, insertitem);
                                    
                                    const newTestimonials    = oldItems;
                                    
                                    newTestimonials.forEach(function(value, index){                                        
                                       newTestimonials[index]['title']       = value['title'];
                                       newTestimonials[index]['description'] = value['description'];
                                       newTestimonials[index]['index']       = index;
                                    });
                                    
                                    oldAttributes['items'] = newTestimonials;
                                    props.setAttributes({
                                      attributes: oldAttributes
                                    });                                                                 
                                }
                            }                      
                        )
                
                );
                
            }
            
            function saswpGetMover(){                
                return el('div',
                        {className:'saswp-how-to-step-mover'},
                        el( IconButton, {
                            icon: "arrow-up-alt2",
                            className: 'editor-block-mover__control',            
                            onClick: function() {              
                                                      
                            }
                          },
                          
                        ),
                        el( IconButton, {
                            icon: "arrow-down-alt2",
                            className: 'editor-block-mover__control',            
                            onClick: function() {              
                                                      
                            }
                          },                         
                        )
                        );
            }
            //List of function for the current blocks ends here
              
              var itemli = attributes.items.sort(function(a , b) {
                  
                    return a.index - b.index;
                    }).map(function(item){    
                        
                      return el('li', { className: 'item' }, 
                          el('span',{
                              className:'saswp-how-to-step-number',                             
                          },
                          ( parseInt(item.index) + 1) + "."
                          ),  
                          el( RichText, {                
                          tagName: 'p',
                          className:'saswp-how-to-step-title',
                          placeholder: 'Enter a step title', 
                          style: { textAlign: alignment },
                          value: item.title,
                          autoFocus: true,
                          onChange: function( value ) {                                
                            var newObject = Object.assign({}, item, {
                              title: value
                            });
                            return props.setAttributes({
                              items: [].concat(_cloneArray(props.attributes.items.filter(function (itemFilter) {
                                return itemFilter.index != item.index;
                              })), [newObject])
                            });
                          }
                        }            
                      ),
                      el( RichText, {                
                          tagName: 'p',
                          placeholder: 'Enter a step description', 
                          className:'saswp-how-to-step-description',
                          style: { textAlign: alignment },
                          value: item.description,
                          autoFocus: true,
                          onChange: function( value ) {                                
                            var newObject = Object.assign({}, item, {
                              description: value
                            });
                            return props.setAttributes({
                              items: [].concat(_cloneArray(props.attributes.items.filter(function (itemFilter) {
                                return itemFilter.index != item.index;
                              })), [newObject])
                            });
                          }
                        }            
                      ), 
              
                      el('div', {className:'saswp-how-to-step-controls-container'}, 
                     
                        saswpGetMover(item),
                        saswpGetButtons(item)        
                      )
                      )
              });
              
              var itemlist = el('ul',{}, itemli);
            
            return [
                el(
                    BlockControls,
                    { key: 'controls' },
                    el(
                        AlignmentToolbar,
                        {
                            value: alignment,
                            onChange: function(newAlignment){
                               props.setAttributes( { alignment: newAlignment === undefined ? 'none' : newAlignment } ); 
                            }
                        }
                    )
                ),
                ,el(
                'div',
                { className: props.className },
                el('div', { className: 'saswp-how-to-setp-list' },        
                  itemlist,
                ),
                el( IconButton, {
                    icon: "insert",
                    className: 'saswp-how-to-step-button',            
                    onClick: function() {              
                      return props.setAttributes({
                        items: [].concat(_cloneArray(props.attributes.items), [{
                          index: props.attributes.items.length,                  
                          title: ""                  
                        }])
                      });                            
                    }
                  },
                  'Add Step'
                )        
              )];
            
        },
        save: function( props ) {

            var attributes = props.attributes;            

            if (attributes.items.length > 0) {

              var itemList = attributes.items.map(function(item) {          

                return el('li', { className: 'item', 'data-index': item.index },        
                  el( 'p', {              
                    className: 'title' ,                                 
                  }, item.title),
                  el( 'p', {              
                    className: 'description' ,                                 
                  }, item.description) 
                );

              });

              return el(
                'div',
                { className: props.className },
                el('ul', { className: 'item-list' },        
                  itemList
                )              
              ); 

            } else {
              return null;
            }
          }
    } );
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.editor,
    window.wp.components,
) );