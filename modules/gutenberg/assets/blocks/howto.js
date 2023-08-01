
( function( blocks, element, editor, components, i18n ) {
    
    const { __ }          = i18n;
    const { RichText, MediaUpload, AlignmentToolbar, BlockControls, InspectorControls} = editor;
    const {TextControl, ToggleControl, PanelBody, IconButton, SelectControl} = components;        

    const el                = element.createElement;
            
    blocks.registerBlockType( 'saswp/how-to-block', {
        title: __('How To (SASWP)', 'schema-and-structured-data-for-wp'),
        icon: 'list-view',
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
            hasCost:{
              type:'boolean',
              default:false
            },
            alignment: {
                type: 'string',
                default: 'none'
            },
            listStyleType: {
              type: 'string',
              default: 'number',
          },
          headingTag : {
            type: 'string',
            default: 'h1'
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
            currency: {
              type: 'string',
              selector: '.saswp-how-to-currency'
            },
            price: {
              type: 'number',
              selector: '.saswp-how-to-price'
            },
            items: {                     
              default: [{index: 0, title: "", description: "", imageUrl: "", imageId: null}],
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
                image_size: {
                  type: 'string',
                  default: 'full'                 
                },
                image_sizes: {
                  type: 'object'                  
                },
                image_align:{
                  type:'string',
                  default:'left'
                },
                image_alignment:{
                  type:'object'
                },
                image_height: {
                  type: 'number'                 
                },
                image_width: {
                  type: 'number'                 
                },
                image_selected: {
                  type: 'boolean',
                  default:false                 
                },
                index: {            
                  type: 'number',                  
                  attribute: 'data-index',                  
                },
                isSelected: {            
                  type: 'boolean',
                  default:false      
                }
              }
            },
            tools: {                     
              default: [],
              selector: '.saswp-how-to-block-tools',
              query: {
                name: {
                  type: 'string',                  
                  selector: '.tool-name'
                },                
                index: {            
                  type: 'number',                  
                  attribute: 'data-index',                  
                },
                isSelected: {            
                  type: 'boolean',
                  default:false      
                }
              }
            },
            materials: {                     
              default: [],
              selector: '.saswp-how-to-block-material',
              query: {
                name: {
                  type: 'string',                  
                  selector: '.material-name'
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
            
            const attributes = props.attributes;          
            
            const alignment  = props.attributes.alignment;
                            
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
            
            function saswpGetCost(){
                
              var cost = el('fieldset',{className:'saswp-how-to-cost'},el('legend',{className:'saswp-how-to-cost-legend'}, 'Estimate Cost : ',
                          el('span',{className:'saswp-how-to-duration-time-input'},                          
                          el(TextControl,
                          {
                             className:'saswp-how-to-cost-input',
                             placeholder: 'USD',
                             value: attributes.currency,                               
                             autoFocus: true,
                             onChange: function( newContent ) {                                
                                  props.setAttributes( { currency: newContent } );
                             }
                          },),
                          el(TextControl,
                          {
                             className:'saswp-how-to-cost-input',
                             placeholder: '20',
                             value: attributes.price,                               
                             autoFocus: true,
                             onChange: function( newContent ) {                                
                                props.setAttributes( { price: newContent } );
                             }
                          },),
                          el( IconButton, {
                              icon: "trash",
                              className: 'saswp-how-to-step-button',            
                              onClick: function() {  
                                props.setAttributes( { hasCost: false } );   
                              }
                            } 
                          )
                         )        
                  ));

                  
          
             var addCost = el( IconButton, {
                                  icon: "insert",
                                  className: 'saswp-how-to-step-button',            
                                  onClick: function(){
                                    props.setAttributes( { hasCost: true } );  
                                  }
                                },
                              __('Add Cost', 'schema-and-structured-data-for-wp')
                          );     
              
              if(attributes.hasCost){
                  return cost;
              }else{
                  return addCost;
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
                                __('Add Total Time', 'schema-and-structured-data-for-wp')
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
                                    
                                    const image = `<img style="height:${media.height}px; width: ${media.width}px;" src="${media.url}" alt="${media.alt}" key="${media.id}"/>`; 
                                    
                                    const oldAttributes      =  attributes; 
                                    const oldItems           =  attributes.items;                                                                                                        
                                    oldItems.forEach(function(value, index){ 
                                       
                                       if(index == item.index){
                                                                                       
                                            oldItems[index]['description'] = image+value
                                            ['description'];                                             
                                            oldItems[index]['imageUrl']    = media.url;
                                            oldItems[index]['imageId']     = media.id;
                                            oldItems[index]['image_sizes'] = media.sizes;
                                            oldItems[index]['image_alignment']=media.alignment;
                                            oldItems[index]['image_height']= media.height;
                                            oldItems[index]['image_width'] = media.width;
                                                                                       
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
                                __('Add Image', 'schema-and-structured-data-for-wp')
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
            
            function saswpReplaceImage(desc, image){
              
              let modified_desc = desc.replace(/<img (.*?)\/>/g, image);
                  modified_desc = desc.replace(/<img (.*?)>/g, image);
              
              return modified_desc;
            }

            function saswpImageUpdate(value, item, height, width, image_type,image_align){
                        
                        let image_url;
                        let image;
                        
                        switch (value) {

                            case 'full':

                              if(image_type != null){
                                height = item.image_sizes.full.height;                                
                                width  = item.image_sizes.full.width;
                              }
                              
                              image_url    = item.image_sizes.full.url;
                              image = `<img style="height:${height}px; width: ${width}px;" src="${item.image_sizes.full.url}"  key="${item.image_sizes.full.url}" />`; 
                            break;

                            case 'large':
                             // console.log(item.image_sizes.full.height);
                              if(image_type != null){
                                height = item.image_sizes.full.height;                                
                                width = item.image_sizes.full.width;
                              }                              
                              
                              image_url    = item.image_sizes.full.url;
                              image = `<img style="height:${height}px; width: ${width}px;" src="${item.image_sizes.full.url}"  key="${item.image_sizes.full.url}" />`;
                            break;

                            case 'medium':
                            
                              if(image_type != null){
                                height = item.image_sizes.medium.height;                                
                                width = item.image_sizes.medium.width;
                              }                              
                              
                              image_url    = item.image_sizes.medium.url;
                              image = `<img style="height:${height}px; width: ${width}'px;" src="${item.image_sizes.medium.url}"  key="${item.image_sizes.medium.url}" />`;
                            break;

                            case 'thumbnail':

                              if(image_type != null){
                                height = item.image_sizes.thumbnail.height;                                
                                width  = item.image_sizes.thumbnail.width;
                              }                              
                              
                              image_url    = item.image_sizes.thumbnail.url;
                              image = `<img style="height:'+height+'px; width: '+width+'px;" src="${item.image_sizes.thumbnail.url}"  key="${item.image_sizes.thumbnail.url}" />`;
                            break;

                            case 'right':
                              image = `<img class="alignright" style="height:300px; width: 300px;" src="${item.image_sizes.full.url}"  key="${item.image_sizes.full.url}" />`;
                              image_align = value;
                              value = item.image_size;
                              height = item.image_height;
                              width = item.image_width;
                            break;
                            case 'left':                              
                                image = `<img class="alignleft" style="height:300px; width: 300px;" src="${item.image_sizes.full.url}"  key="${item.image_sizes.full.url}" />`;
                                image_align = value;
                                value = item.image_size;
                                height = item.image_height;
                                width = item.image_width;
                            break;
                        
                          default:
                            break;
                        }
                        
                        var newObject = Object.assign({}, item, {
                          image_size:  value,
                          image_height: height,
                          image_width:  width,
                          image_align:  image_align,                          
                          imageUrl   : image_url,
                          description : saswpReplaceImage(item.description, image)                          
                        });
                        return newObject;
            }

            function saswpImageSettings(item){

              if(item.isSelected && item.imageUrl != ''){
                return el(InspectorControls,
                  {
                   className:'saswp-how-to-inspector',
                   key: 'inspector'   
                  },
                     el(PanelBody,
                    {className:'saswp-how-to-panel-body',
                     title:__('Image Settings', 'schema-and-structured-data-for-wp')   
                    },
                    el(SelectControl,{
                      value : item.image_size,
                      label: __('Image Size', 'schema-and-structured-data-for-wp'),
                      options:[
                        { label: 'Full Size', value: 'full' },
                        { label: 'Large', value: 'large' },
                        { label: 'Medium', value: 'medium' },
                        { label: 'Thumbnail', value: 'thumbnail' },
                      ] ,
                      onChange: function(value){
                        
                        var newObject = saswpImageUpdate(value, item, '', '', 'image_type',item.image_size);
                        console.log("image_size "+ item.image_size);
                        return props.setAttributes({
                          items: [].concat(_cloneArray(props.attributes.items.filter(function (itemFilter) {
                            return itemFilter.index != item.index;
                          })), [newObject])
                        });
                      }
                  }),
                  el(SelectControl,{
                    value: item.image_align,
                    className:"saswp-image-align",
                    label: __( 'Image Align' , 'schema-and-structured-data-for-wp' ),
                    options: [
                     { label: 'Left' , value: 'left'},
                     { label: 'Right' , value: 'right'},
                     
                    ],
                     onChange: function(value){
                     // alert(value);
                       var newObject = saswpImageUpdate(value, item, '', '', 'image_type',item.image_align);
                       console.log("image_align "+ item.image_align);
                     return props.setAttributes({
                       items: [].concat(_cloneArray(props.attributes.items.filter(function (itemFilter) {
                         return itemFilter.index != item.index;
                       })), [newObject])
                     });
                   }
                   }),
                  el('p',{
                    className: 'saswp-how-to-dimesion-p'
                  }, 'Image Dimensions'),
                  el('div', {
                    className: "saswp-how-to-dimension"
                  },                        
                  el( TextControl, {                                          
                    className:'saswp-how-to-image-dimension',
                    label:'Height',
                    type: 'number',
                    min:0,
                    placeholder: __('20', 'schema-and-structured-data-for-wp'),                           
                    value: item.image_height,                                                    
                    onChange: function( value ) { 

                      var newObject = saswpImageUpdate(item.image_size, item, value, item.image_width, null);
                      
                      return props.setAttributes({
                        items: [].concat(_cloneArray(props.attributes.items.filter(function (itemFilter) {
                          return itemFilter.index != item.index;
                        })), [newObject])
                      });
                    }
                  }            
                ),
                el( TextControl, {                                          
                  className:'saswp-how-to-image-dimension',
                  label:'Width',
                  type: 'number',
                  min:0,
                  placeholder: __('20', 'schema-and-structured-data-for-wp'),                           
                  value: item.image_width,                                                    
                  onChange: function( value ) {                                
                    
                    var newObject = saswpImageUpdate(item.image_size, item, item.image_height, value, null);

                    return props.setAttributes({
                      items: [].concat(_cloneArray(props.attributes.items.filter(function (itemFilter) {
                        return itemFilter.index != item.index;
                      })), [newObject])
                      });
                    }
                  }            
                  ),
                  )                                      
                    )             
                  );
              }else{
                return null;
              }

              
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
            //Step list starts here
              
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
                    attributes.listStyleType == 'none'? '' : attributes.listStyleType =='disc' ?'•': attributes.listStyleType=='number' ?
                    ( parseInt(item.index) + 1) + "." :''
                    ),  
                          el( RichText, {                
                          tagName: 'p',
                          className:'saswp-how-to-step-title',
                          placeholder: __('Enter a step title', 'schema-and-structured-data-for-wp'), 
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
                          placeholder: __('Enter a step description', 'schema-and-structured-data-for-wp'), 
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
                        saswpImageSettings(item),
                        saswpGetMover(item),
                        saswpGetButtons(item)        
                      )
                      )
              });
            
            //Step list ends here
            
            //Tool list starts here
              
              var toolli = attributes.tools.sort(function(a , b) {
                  
                    return a.index - b.index;
                    }).map(function(tool){  
                                                                        
                      return el('li', 
                            { 
                                className: 'tool',
                                onClick: function(){
                                                                                                              
                                    const oldAttributes      =  attributes; 
                                    const oldItems           =  attributes.tools;                                                                                                        
                                    oldItems.forEach(function(value, index){ 
                                       
                                       if(index == tool.index){                                            
                                            oldItems[index]['isSelected']  = true;                                                                                       
                                       }else{                                            
                                            oldItems[index]['isSelected']  = false;                                                                                       
                                       }
                                                                               
                                    });
                                    
                                    oldAttributes['tools'] = oldItems;
                                    props.setAttributes({
                                      attributes: oldAttributes
                                    });                                    
                                }
                            },                            
                          el( RichText, {                
                          tagName: 'p',
                          className:'saswp-how-to-tool-name',
                          placeholder: __('Enter a tool name', 'schema-and-structured-data-for-wp'), 
                          style: { textAlign: alignment },
                          value: tool.name,
                          autoFocus: true,
                          onChange: function( value ) {                                
                            var newObject = Object.assign({}, tool, {
                              name: value
                            });
                            return props.setAttributes({
                              tools: [].concat(_cloneArray(props.attributes.tools.filter(function (itemFilter) {
                                return itemFilter.index != tool.index;
                              })), [newObject])
                            });
                          }
                        }            
                      ),                                            
                      el('div', {className:'saswp-how-to-tool-controls-container'},                                                
                      
                      el( IconButton, {
                            icon: "trash",
                            className: 'saswp-how-to-tool-button',            
                            onClick: function() { 
                                
                                 const oldAttributes      =  attributes; 
                                 const oldItems           =  attributes.tools;  
                                 const newTestimonials    =  oldItems
                                 
                                    .filter(function(itemFilter){
                                       return itemFilter.index != tool.index
                                    }).map(function(t){                                          
                                         if (t.index > oldItems.index) {
                                             t.index -= 1;
                                         }
                                         return t;
                                    });
                                    
                                    newTestimonials.forEach(function(value, index){                                                                                                                      
                                       newTestimonials[index]['index']       = index;                                       
                                    });
                                    
                                    oldAttributes['tools'] = newTestimonials;
                                    props.setAttributes({
                                      attributes: oldAttributes
                                    });                                
                                
                            }
                          }                          
                        )
                    
                      )
                      )
              });
            
            //Tool list ends here
            
            //Material list starts here
              
              var materialli = attributes.materials.sort(function(a , b) {
                  
                    return a.index - b.index;
                    }).map(function(material){  
                                                                        
                      return el('li', 
                            { 
                                className: 'material',
                                onClick: function(){
                                                                                                              
                                    const oldAttributes      =  attributes; 
                                    const oldItems           =  attributes.materials;                                                                                                        
                                    oldItems.forEach(function(value, index){ 
                                       
                                       if(index == material.index){                                            
                                            oldItems[index]['isSelected']  = true;                                                                                       
                                       }else{                                            
                                            oldItems[index]['isSelected']  = false;                                                                                       
                                       }
                                                                               
                                    });
                                    
                                    oldAttributes['materials'] = oldItems;
                                    props.setAttributes({
                                      attributes: oldAttributes
                                    });                                    
                                }
                            },                            
                          el( RichText, {                
                          tagName: 'p',
                          className:'saswp-how-to-material-name',
                          placeholder: __('Enter a material name', 'schema-and-structured-data-for-wp'), 
                          style: { textAlign: alignment },
                          value: material.name,
                          autoFocus: true,
                          onChange: function( value ) {                                
                            var newObject = Object.assign({}, material, {
                              name: value
                            });
                            return props.setAttributes({
                              materials: [].concat(_cloneArray(props.attributes.materials.filter(function (itemFilter) {
                                return itemFilter.index != material.index;
                              })), [newObject])
                            });
                          }
                        }            
                      ),                                            
                      el('div', {className:'saswp-how-to-material-controls-container'},                                                
                        el( IconButton, {
                            icon: "trash",
                            className: 'saswp-how-to-material-button',            
                            onClick: function() { 
                                
                                 const oldAttributes      =  attributes; 
                                 const oldItems           =  attributes.materials;  
                                 const newTestimonials    =  oldItems
                                 
                                    .filter(function(itemFilter){
                                       return itemFilter.index != material.index
                                    }).map(function(t){                                          
                                         if (t.index > oldItems.index) {
                                             t.index -= 1;
                                         }
                                         return t;
                                    });
                                    
                                    newTestimonials.forEach(function(value, index){                                                                                                                      
                                       newTestimonials[index]['index']       = index;                                       
                                    });
                                    
                                    oldAttributes['materials'] = newTestimonials;
                                    props.setAttributes({
                                      attributes: oldAttributes
                                    });                                
                                
                            }
                          }                          
                        )       
                      )
                      )
              });
            
            //material list ends here
            
            var itemlist     = el('ul',{}, itemli);
            var toollist     = el('ul',{}, toolli);
            var materiallist = el('ul',{}, materialli);
            
            return [
                el(InspectorControls,
                {
                 className:'saswp-how-to-inspector',
                 key: 'inspector'   
                },
                el(PanelBody,
                {className:'saswp-how-to-panel-body',
                 title:__('List Order Type', 'schema-and-structured-data-for-wp')   
                },
                el(SelectControl,{
                  className:'saswp-faq-toggle-list',  
                  value: attributes.listStyleType,
                  options:[
                        { label:'None', value: 'none' },
                        { label: 'Number', value:'number' },
                        { label:'disc', value:'disc' },
                      ],
                  onChange: function(newContent){
                      props.setAttributes( { listStyleType: newContent } );
                  },
                  help: function(value){
                    if(value=='none')
                      return '<li "style:list-style-type:none"></li>';
                    if( value == 'number')
                      return '<li "style:list-style-type:decimal"></li>';
                    if(value=='disc' )
                      return '<li "style:list-style-type:disc"></li>';
                  }
              },
              ),
                )                
                ),
                el(InspectorControls,
                      {
                      className:'saswp-how-to-inspector',
                      key: 'inspector'   
                      },
                      el(PanelBody,
                      {className:'saswp-how-to-panel-body',
                      title:__('Heading Tag', 'schema-and-structured-data-for-wp')   
                      },
                      el(SelectControl,{
                        className:'saswp-faq-toggle-list',  
                        value: attributes.headingTag,
                        options:[
                              { label:'H1', value: 'h1' },
                              { label: 'H2', value:'h2' },
                              { label:'H3', value:'h3'  },
                              { label:'H4', value: 'h4' },
                              { label: 'H5', value:'h5' },
                              { label:'H6', value:'h6' },
                            ],
                        onChange: function(newContent){
                          console.log(newContent);
                            props.setAttributes( { headingTag: newContent } );
                        }
                    },
                  ),
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
                saswpGetCost(),                
                el( RichText, {                
                          tagName: 'p',
                          className:'saswp-how-to-description',
                          placeholder: __('Enter how to description', 'schema-and-structured-data-for-wp'), 
                          style: { textAlign: alignment },
                          value: attributes.description,
                          autoFocus: true, 
                          onChange: function( newContent ) {                                
                              props.setAttributes( { description: newContent } );
                          }
                        }            
                      ),
                el('div',{className:'saswp-how-to-setp-block'},                
                el('div', { className: 'saswp-how-to-setp-list'},        
                  itemlist,
                ),
                el( IconButton, {
                    icon: "insert",
                    className: 'saswp-how-to-step-button',            
                    onClick: function() {              
                      return props.setAttributes({
                        items: [].concat(_cloneArray(props.attributes.items), [{
                          index: props.attributes.items.length,                  
                          title: "",
                          description: "",
                        }])
                      });                            
                    }
                  },
                  __('Add A Step', 'schema-and-structured-data-for-wp')
                )                
                ),      
                el('div',{className:'saswp-how-to-material-block'},
                el('div', { className: 'saswp-how-to-material-list'},        
                  materiallist,
                ),
                el( IconButton, {
                    icon: "insert",
                    className: 'saswp-how-to-material-button',            
                    onClick: function() {              
                      return props.setAttributes({
                        materials: [].concat(_cloneArray(props.attributes.materials), [{
                          index: props.attributes.materials.length,                  
                          name: ""                          
                        }])
                      });                            
                    }
                  },
                  __('Add A Material', 'schema-and-structured-data-for-wp')
                )
                ),      
                el('div',{className:'saswp-how-to-tool-block'},
                el('div', { className: 'saswp-how-to-tool-list'},        
                  toollist,
                ),
                el( IconButton, {
                    icon: "insert",
                    className: 'saswp-how-to-tool-button',            
                    onClick: function() {              
                      return props.setAttributes({
                        tools: [].concat(_cloneArray(props.attributes.tools), [{
                          index: props.attributes.tools.length,                  
                          name: ""                          
                        }])
                      });                            
                    }
                  },
                  __('Add A Tool', 'schema-and-structured-data-for-wp')
                )
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

