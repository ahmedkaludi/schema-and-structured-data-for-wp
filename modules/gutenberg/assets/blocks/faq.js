
( function( blocks, element, editor, components, i18n ) {
    
  const { __ }          = i18n;
  
  var el                = element.createElement;
  var RichText          = editor.RichText;
  var MediaUpload       = editor.MediaUpload;       
  var IconButton        = components.IconButton;
  var AlignmentToolbar  = editor.AlignmentToolbar;
  var BlockControls     = editor.BlockControls;    
  var InspectorControls = editor.InspectorControls;

  var PanelBody         = components.PanelBody;
  var SelectControl     = components.SelectControl;
  var TextControl       = components.TextControl;
  
          
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
          headingTag: {
            type: 'string' ,
            default:'h5',             
          },
          listStyle:{
                type: 'string',
              default:'None',
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
              questionID: {
                type: 'string',                                    
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
                default:'right'
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
                                  
                                  const image = '<img style="height:'+media.height+'px; width: '+media.width+'px;" src="'+media.url+'" alt="'+media.alt+'" class="alignright" key="'+media.id+'"/>'; 
                                  
                                  const oldAttributes      =  attributes; 
                                  const oldItems           =  attributes.items;                                                                                                        
                                  oldItems.forEach(function(value, index){ 
                                     
                                     if(index == item.index){
                                                                                     
                                          oldItems[index]['description'] = image+value['description'];                                            
                                          oldItems[index]['imageUrl']    = media.url;
                                          oldItems[index]['imageId']     = media.id;
                                          oldItems[index]['image_sizes'] = media.sizes;
                                          oldItems[index]['image_alignment']=media.alignment
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
          
              
          
          function saswpReplaceImage(desc, image){
            
            let modified_desc = desc.replace(/<img (.*?)\/>/g, image);
                modified_desc = desc.replace(/<img (.*?)>/g, image);
            
            return modified_desc;
          }
          function saswpImageUpdate(value, item, height, width, image_type,image_align){
                      if(typeof value == 'undefined'){
                        value = 'full';
                      }
                      if(typeof image_align == 'undefined'){
                        image_align = 'right';  
                      }
                      let image_url;
                      let image;
                      
                      switch (value) {
                          case 'full':
                            if(image_type != null){
                              height = item.image_sizes.full.height;                                
                              width  = item.image_sizes.full.width;
                            }
                            
                            image_url    = item.image_sizes.full.url;
                            image = '<img style="height:'+height+'px; width: '+width+'px;" src="'+item.image_sizes.full.url+'"  key="'+item.image_sizes.full.url+'" />';
                          break;
                          case 'large':
                            if(image_type != null){
                              height = item.image_sizes.large.height;                                
                              width = item.image_sizes.large.width;
                            }                              
                            
                            image_url    = item.image_sizes.large.url;
                            image = '<img style="height:'+height+'px; width: '+width+'px;" src="'+item.image_sizes.large.url+'"  key="'+item.image_sizes.large.url+'" />';
                          break;
                          case 'medium':
                            if(image_type != null){
                              height = item.image_sizes.medium.height;                                
                              width = item.image_sizes.medium.width;
                            }                              
                            
                            image_url    = item.image_sizes.medium.url;
                            image = '<img style="height:'+height+'px; width: '+width+'px;" src="'+item.image_sizes.medium.url+'"  key="'+item.image_sizes.medium.url+'" />';
                          break;
                          case 'thumbnail':
                            if(image_type != null){
                              height = item.image_sizes.thumbnail.height;                                
                              width  = item.image_sizes.thumbnail.width;
                            }                              
                            
                            image_url    = item.image_sizes.thumbnail.url;
                            image = '<img style="height:'+height+'px; width: '+width+'px;" src="'+item.image_sizes.thumbnail.url+'"  key="'+item.image_sizes.thumbnail.url+'" />';
                          break;
                          case 'right':
                              image = '<img class="alignright" style="height:'+height+'px; width: '+width+'px; float:right;" src="'+item.image_sizes.full.url+'"  key="'+item.image_sizes.full.url+'" />';
                            break;
                          case 'left':
                                image = '<img class="alignleft" style="height:'+height+'px; width: '+width+'px; float:left;" src="'+item.image_sizes.full.url+'"  key="'+item.image_sizes.full.url+'" />';
                            break;
                      
                        default:
                          break;
                      }
                      
                      var newObject = Object.assign({}, item, {
                        image_size: value,
                        image_height: height,
                        image_align: image_align,
                        image_width: width,
                        imageUrl   : image_url,
                        description : saswpReplaceImage(item.description, image)                          
                      });
                      return newObject;
          }

          function saswpImageSettings(item){
            if(item.isSelected && item.imageUrl != ''){
              return el(InspectorControls,
                {
                 className:'saswp-faq-inspector',
                 key: 'inspector'  
                },
                   el(PanelBody,
                  {className:'saswp-faq-panel-body',
                   title:__('Image Settings', 'schema-and-structured-data-for-wp')  
                  },
                  el(SelectControl,{
                    value : item.image_size,
                    className:"saswp-image-size",
                    label: __('Image Size', 'schema-and-structured-data-for-wp'),
                    options:[
                      { label: 'Full Size', value: 'full' },
                      { label: 'Large', value: 'large' },
                      { label: 'Medium', value: 'medium' },
                      { label: 'Thumbnail', value: 'thumbnail' },
                    ] ,
                    onChange: function(value){
                      var newObject = saswpImageUpdate(value, item, '', '', 'image_type');
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
                  
                  { label: 'Right' , value: 'right'},
                  { label: 'Left' , value: 'left'},
                  // { label: 'Center' , value: 'center'},
                  
                 ],
                  onChange: function(value){
                    var newObject = saswpImageUpdate(value, item, '', '', 'image_type');
                  return props.setAttributes({
                    items: [].concat(_cloneArray(props.attributes.items.filter(function (itemFilter) {
                      return itemFilter.index != item.index;
                    })), [newObject])
                  });
                }
                }),
                el('p',{
                  className: 'saswp-faq-dimesion-p'
                }, 'Image Dimensions'),
                el('div', {
                  className: "saswp-faq-dimension"
                },                        
                el( TextControl, {                                          
                  className:'saswp-faq-image-dimension',
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
                className:'saswp-faq-image-dimension',
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
                        attributes.listStyle == 'none'? '' : attributes.listStyle =='disc' ?'•': attributes.listStyle=='number' ?
                        ( parseInt(item.index) + 1) + "." :''
                        ),    
                        el( RichText, {                
                        tagName: attributes.headingTag,
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
                    el('div', {
                      
                      className:"saswp",
                    },
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
                    ),                
                    el('div', {className:'saswp-faq-step-controls-container'},                        
                      item.isSelected ?
                      el(TextControl,
                        {                             
                          className:'saswp-faq-question-id',
                          value: item.questionID,
                          placeholder: __('Question ID (Optional)', 'schema-and-structured-data-for-wp'), 
                          onChange: function(value){
                           
                            var newObject = Object.assign({}, item, {
                              questionID: value
                            });
                            return props.setAttributes({
                              items: [].concat(_cloneArray(props.attributes.items.filter(function (itemFilter) {
                                return itemFilter.index != item.index;
                              })), [newObject])
                            });
                         }
                        }                          
                        )
                      : '',
                      saswpImageSettings(item),
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
               title:'List Order Type'   
              },
              el(SelectControl,{
                    className:'saswp-faq-toggle-list',  
                    value: attributes.listStyle,
                    options:[
                          { label:'None', value: 'none' },
                          { label: 'Number', value:'number' },
                          { label:'disc', value:'disc' },
                        ],
                    onChange: function(newContent){
                        props.setAttributes( { listStyle: newContent } );
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
              el(SelectControl,{
                value : attributes.headingTag,
                className: "saswp-heading",
                label: __('Heading Tag', 'schema-and-structured-data-for-wp'),
                options:[                    
                  { label: 'H1', value: 'h1' },
                  { label: 'H2', value: 'h2' },
                  { label: 'H3', value: 'h3' },
                  { label: 'H4', value: 'h4' },
                  { label: 'H5', value: 'h5' },
                  { label: 'H6', value: 'h6' },
                  { label: 'Div', value:'div' },
                  { label:'P', value:'p' },
                  { label:'Strong', value:'strong' }

                ] ,
                onChange: function(value){
                     props.setAttributes( { headingTag: value } ); 
                }
              }),
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

