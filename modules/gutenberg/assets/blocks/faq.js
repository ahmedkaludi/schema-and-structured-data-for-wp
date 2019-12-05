
( function( blocks, element, editor, components, i18n ) {
    
    const { __ }          = i18n;
    
    var el                = element.createElement;
    var RichText          = editor.RichText;
    var MediaUpload       = editor.MediaUpload;       
    var IconButton        = components.IconButton;
    var AlignmentToolbar  = editor.AlignmentToolbar;
    var BlockControls     = editor.BlockControls;    
    var InspectorControls = editor.InspectorControls;
    var ToggleControl     = components.ToggleControl;
    var PanelBody         = components.PanelBody;
            
    blocks.registerBlockType( 'saswp/faq-block', {
        title: __('FAQ (SASWP)', 'schema-and-structured-data-for-wp'),
        icon: 'text',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'FAQ', 'faq'],
        
        // Allow only one How To block per post.
        supports: {
                multiple: false
        },
                        
        attributes: {            
            alignment: {
                type: 'string',
                default: 'none'
            },
            toggleList: {
                type: 'boolean',
                default: false
            },                        
            items: {                     
              default: [{index: 0, title: "", description: "", imageUrl: "", imageId: null}],
              selector: '.saswp-faq-block-data',
              query: {
                title: {
                  type: 'string',                  
                  selector: '.title'
                },
                description: {
                  type: 'string',                  
                  selector: '.description'
                },
                imageId: {
                  type: 'integer'                
                },
                imageUrl: {
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
        edit: function(props) {
            
            var attributes = props.attributes;
            var alignment  = props.attributes.alignment;
                            
            //List of function for the current blocks starts here
            
            function saswpGetImageSrc( item ) {
                
                var contents = item.description;
                
                if ( ! contents ) {
                    
			return false;
		}
                
		const image = contents.match(/<img/);
                
		if ( image ) {
			return true;
		}else {
                        
                        return false;
                }
                		
            }
            
                        
            function moveElementsByDirection(direction, item){
                
                                var newIndex            = null;
                                var oldIndex            = parseInt(item.index);
                                const oldAttributes     =  attributes; 
                                const oldItems          =  attributes.items;                                  
                                
                                if(direction == 'up'){
                                    newIndex = parseInt(item.index)-1;
                                }
                                
                                if(direction == 'down'){
                                    newIndex = parseInt(item.index)+1;
                                }
                               
                                if(newIndex >= oldItems.length){
                                    newIndex = 0;
                                }
                                                                        
                                const newTestimonials    =  move(oldItems, oldIndex, newIndex);                                                                    
                                
                                newTestimonials.forEach(function(value, index){                                     
                                        newTestimonials[index]['title']       = value['title'];
                                        newTestimonials[index]['description'] = value['description'];
                                        newTestimonials[index]['index']       = index; 
                                        newTestimonials[index]['isSelected']  = false; 
                                    
                                 });

                                 oldAttributes['items'] = newTestimonials;
                                 props.setAttributes({
                                   attributes: oldAttributes
                                 });
                
            }
            
            function move(arr, old_index, new_index) {
                    while (old_index < 0) {
                        old_index += arr.length;
                    }
                    while (new_index < 0) {
                        new_index += arr.length;
                    }
                    if (new_index >= arr.length) {
                        var k = new_index - arr.length;
                        while ((k--) + 1) {
                            arr.push(undefined);
                        }
                    }
                     arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);  
                   return arr;
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
            
            function saswpGetButtons(item){
                
                if(!item.isSelected){
                    return null;
                }
                              					
                return el('div', {className:'saswp-faq-button-container'},                        
                        !saswpGetImageSrc(item) ? 
                        el(MediaUpload, {
                            onSelect: function(media){  
                                    
                                    const image = '<img src="'+media.url+'" alt="'+media.alt+'" key="'+media.id+'"/>'; 
                                    
                                    const oldAttributes      =  attributes; 
                                    const oldItems           =  attributes.items;                                                                                                        
                                    oldItems.forEach(function(value, index){ 
                                       
                                       if(index == item.index){
                                                                                       
                                            oldItems[index]['description'] = value['description']+image;                                            
                                            oldItems[index]['imageUrl']    = media.url;
                                            oldItems[index]['imageId']     = media.id;
                                                                                       
                                       }
                                                                               
                                    });
                                    
                                    oldAttributes['items'] = oldItems;
                                    props.setAttributes({
                                      attributes: oldAttributes
                                    }); 
                                                            
                            },
                           allowedTypes:[ "image" ],
                           value: item.id,
                           render:function(obj){
                               return el( IconButton, {
                                    icon: "insert",
                                    className: 'saswp-faq-step-button saswp-to-step-add-media',            
                                    onClick: obj.open
                                  },
                                __('Add Image', 'schema-and-structured-data-for-wp')
                            )
                           }
                        }): null,
                        el( IconButton, {
                            icon: "trash",
                            className: 'saswp-faq-step-button',            
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
                                       newTestimonials[index]['isSelected']  = false; 
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
                            className: 'saswp-faq-step-button',            
                            onClick: function() {              
                                
                                const oldAttributes      =  attributes;
                                const oldItems           =  attributes.items;                                
                                                                
                                const insertitem = {title:"",description:"",index:item.index, isSelected:false};
                                      oldItems.splice(parseInt(item.index)+1, 0, insertitem);
                                    
                                    const newTestimonials    = oldItems;
                                    
                                    newTestimonials.forEach(function(value, index){                                        
                                       newTestimonials[index]['title']       = value['title'];
                                       newTestimonials[index]['description'] = value['description'];
                                       newTestimonials[index]['index']       = index;
                                       newTestimonials[index]['isSelected']  = false;
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
            
            function saswpGetMover(item){
                
                if(!item.isSelected){
                    return null;
                }
                
                return el('div',
                        {className:'saswp-faq-step-mover'},
                        el( IconButton, {
                            icon: "arrow-up-alt2",
                            className: 'editor-block-mover__control',            
                            onClick: function() {              
                                                                         
                              moveElementsByDirection('up', item);  
                              
                            }
                          },
                          
                        ),
                        el( IconButton, {
                            icon: "arrow-down-alt2",
                            className: 'editor-block-mover__control',            
                            onClick: function() {              
                             
                             moveElementsByDirection('down', item);
                                
                            }
                          },                         
                        )
                        );
            }
            //List of function for the current blocks ends here
              
              var itemli = attributes.items.sort(function(a , b) {
                  
                    return a.index - b.index;
                    }).map(function(item){  
                        
                        if(!saswpGetImageSrc(item)){
                            item.imageUrl = '';
                            item.imageId = null;
                        }
                        
                      return el('li', 
                            { 
                                className: 'item',
                                onClick: function(){
                                                                                                              
                                    const oldAttributes      =  attributes; 
                                    const oldItems           =  attributes.items;                                                                                                        
                                    oldItems.forEach(function(value, index){ 
                                       
                                       if(index == item.index){                                            
                                            oldItems[index]['isSelected']  = true;                                                                                       
                                       }else{                                            
                                            oldItems[index]['isSelected']  = false;                                                                                       
                                       }
                                                                               
                                    });
                                    
                                    oldAttributes['items'] = oldItems;
                                    props.setAttributes({
                                      attributes: oldAttributes
                                    });                                    
                                }
                            }, 
                          el('span',{
                              className:'saswp-faq-step-number'                             
                          },
                          attributes.toggleList ? '•':
                          ( parseInt(item.index) + 1) + "."
                          ),  
                          el( RichText, {                
                          tagName: 'p',
                          className:'saswp-faq-step-title',
                          placeholder: __('Enter a question', 'schema-and-structured-data-for-wp'), 
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
                          placeholder: __('Enter answer to the question', 'schema-and-structured-data-for-wp'), 
                          className:'saswp-faq-step-description',
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
                      el('div', {className:'saswp-faq-step-controls-container'},                        
                        saswpGetMover(item),
                        saswpGetButtons(item)        
                      )
                      )
              });
              
            var itemlist = el('ul',{}, itemli);
            
            return [
                el(InspectorControls,
                {
                 className:'saswp-faq-inspector',
                 key: 'inspector'   
                },
                el(PanelBody,
                {className:'saswp-faq-panel-body',
                 title:'Settings'   
                },
                el(ToggleControl,
                {
                    className:'saswp-faq-toggle-list',  
                    checked:attributes.toggleList,
                    onChange: function(newContent){
                        props.setAttributes( { toggleList: newContent } );
                    },
                    help: function(value){
                      return (value == true ? 'Showing step item as an unordered list': 'Showing step item as an ordered list');
                    }
                },
                )
                )
                ),
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
                el('div', { className: 'saswp-faq-setp-list' },        
                  itemlist,
                ),
                el( IconButton, {
                    icon: "insert",
                    className: 'saswp-faq-step-button',            
                    onClick: function() {              
                      return props.setAttributes({
                        items: [].concat(_cloneArray(props.attributes.items), [{
                          index: props.attributes.items.length,                  
                          title: "",
                          description:""
                        }])
                      });                            
                    }
                  },
                  __('Add A Question', 'schema-and-structured-data-for-wp')
                )        
              )];
            
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

