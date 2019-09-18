
( function( blocks, element, editor, components ) {
    var el                = element.createElement;
    var RichText          = editor.RichText;
    var MediaUpload       = editor.MediaUpload;       
    var IconButton        = components.IconButton;
    var AlignmentToolbar  = editor.AlignmentToolbar;
    var BlockControls     = editor.BlockControls;
    var TextControl       = components.TextControl;
    var InspectorControls = editor.InspectorControls;
    var ToggleControl     = components.ToggleControl;
    var PanelBody         = components.PanelBody;
            
    blocks.registerBlockType( 'saswp/how-to-block', {
        title: 'How To',
        icon: 'editor-ol',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'how to', 'how-to'],
        
        // Allow only one How To block per post.
        supports: {
                multiple: false
        },
        
        attributes: {
            hasDuration:{
              type:'boolean',
              default:false
            },
            alignment: {
                type: 'string',
                default: 'none'
            },
            toggleList: {
                type: 'boolean',
                default: false
            },
            description: {
                  type: 'string',                  
                  selector: '.saswp-how-to-main-description'
            },
            days: {
                  type: 'string',                  
                  selector: '.saswp-how-to-days'
            },
            hours: {
                  type: 'string',                 
                  selector: '.saswp-how-to-hours'
            },
            minutes: {
                  type: 'string',
                  selector: '.saswp-how-to-minutes'
            },
            items: {                     
              default: [],
              selector: '.saswp-how-to-block-data',
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
            
            function saswpGetDuration(){
                
                var duration = el('fieldset',{className:'saswp-how-to-duration'},el('legend',{className:'saswp-how-to-duration-legend'}, 'Time Needed : ',
                            el('span',{className:'saswp-how-to-duration-time-input'},
                            el(TextControl,
                            {
                               className:'saswp-how-to-duration-input',
                               placeholder: 'DD',
                               value: attributes.days,                               
                               autoFocus: true,
                               onChange: function( newContent ) { 
                                   
                                   props.setAttributes( { days: newContent } );
                               }
                            },),
                            el(TextControl,
                            {
                               className:'saswp-how-to-duration-input',
                               placeholder: 'HH',
                               value: attributes.hours,                               
                               autoFocus: true,
                               onChange: function( newContent ) {                                
                                    props.setAttributes( { hours: newContent } );
                               }
                            },),
                            el(TextControl,
                            {
                               className:'saswp-how-to-duration-input',
                               placeholder: 'MM',
                               value: attributes.minutes,                               
                               autoFocus: true,
                               onChange: function( newContent ) {                                
                                  props.setAttributes( { minutes: newContent } );
                               }
                            },),
                            el( IconButton, {
                                icon: "trash",
                                className: 'saswp-how-to-step-button',            
                                onClick: function() {  
                                  props.setAttributes( { hasDuration: false } );   
                                }
                              } 
                            )
                           )        
                    ));
            
               var addDuration = el( IconButton, {
                                    icon: "insert",
                                    className: 'saswp-how-to-step-button saswp-to-step-add-media',            
                                    onClick: function(){
                                      props.setAttributes( { hasDuration: true } );  
                                    }
                                  },
                                'Add Duration'
                            );     
                
                if(attributes.hasDuration){
                    return duration;
                }else{
                    return addDuration;
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
                              					
                return el('div', {className:'saswp-how-to-step-button-container'},                        
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
                                    className: 'saswp-how-to-step-button saswp-to-step-add-media',            
                                    onClick: obj.open
                                  },
                                'Add Image'
                            )
                           }
                        }): null,
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
                            className: 'saswp-how-to-step-button',            
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
                        {className:'saswp-how-to-step-mover'},
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
                              className:'saswp-how-to-step-number'                             
                          },
                          attributes.toggleList ? '•':
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
                el(InspectorControls,
                {
                 className:'saswp-how-to-inspector',
                 key: 'inspector'   
                },
                el(PanelBody,
                {className:'saswp-how-to-panel-body',
                 title:'Settings'   
                },
                el(ToggleControl,
                {
                    className:'saswp-how-to-toggle-list',  
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
                saswpGetDuration(),                
                el( RichText, {                
                          tagName: 'p',
                          className:'saswp-how-to-description',
                          placeholder: 'Enter how to description', 
                          style: { textAlign: alignment },
                          value: attributes.description,
                          autoFocus: true, 
                          onChange: function( newContent ) {                                
                              props.setAttributes( { description: newContent } );
                          }
                        }            
                      ),
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
            return null                        
          }
    } );
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.editor,
    window.wp.components,
) );

