
( function( blocks, element, editor, components, i18n) {
            
    const el               = element.createElement;    
    const { __ }           = i18n;    
    const { RichText, MediaUpload } = editor;
    const {Button} = components;
                
    blocks.registerBlockType( 'saswp/book-block', {
        title: __('Book (SASWP)', 'schema-and-structured-data-for-wp'),
        icon:     'welcome-learn-more',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'Book', 'book'],
                     
        supports: {
                multiple: true
        },
        
        attributes: {                                                                                                                     
                banner_id: {
                  type: 'integer'                  
                },
                banner_url: {
                  type: 'string'                  
                },
                title: {
                  type: 'string'                  
                },
                series: {
                  type: 'string'                                    
                },
                author: {
                  type: 'string'                
                },
                genre: {
                  type: 'string'                 
                },
                publisher: {
                  type: 'string'                 
                },
                release_date: {
                  type: 'string'                 
                },
                format: {
                  type: 'string'                 
                },
                pages: {
                  type: 'string'                 
                },
                source: {
                  type: 'string'                 
                },
                rating: {
                  type: 'string',                  
                },
                description: {
                  type: 'string'                 
                }                                                
        },                                                      
        edit: function( props ){
           
          const attributes = props.attributes;
          console.log(attributes);
          function createRating(rating){
            let element = [];
            for(let i = 1; i <= 5; i++){

              if(i <= rating){

                element.push(el('span',{
                  'data-id': i,
                  className: 'saswp-book-block-stars dashicons dashicons-star-filled',
                  onClick: function(e){
                    let current_id = e.target.getAttribute("data-id");  
                    props.setAttributes( { rating: current_id} );                  
                  }
                }));

              }else{

                element.push(el('span',{
                  'data-id': i,
                  className: 'saswp-book-block-stars dashicons dashicons-star-empty',
                  onClick: function(e){
                    let current_id = e.target.getAttribute("data-id");  
                    props.setAttributes( { rating: current_id} );                  
                  }
                }));

              }
              
            }

             return(
              element
             );
          }

          return (

            el('div',{
              className: 'saswp-book-block-container'
            },
            el('div',{
              className: 'saswp-book-field-banner'
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

            ),
            el('div',{
              className: 'saswp-book-field-container'
            },

            el('div', {
              className: 'saswp-book-block-field',
              },
                el(
                  'span', {
                  className: 'saswp-book-field-label'
                },
                __('Title : ', 'schema-and-structured-data-for-wp')
                ),
                el( RichText, {                
                  tagName: 'p',
                  className:'saswp-book-field',
                  placeholder: __('Enter title', 'schema-and-structured-data-for-wp'),                   
                  value: attributes.title,
                  autoFocus: true, 
                  onChange: function( newContent ) {                                
                      props.setAttributes( { title: newContent } );
                  }
                }            
              )
            ),

            el('div', {
              className: 'saswp-book-block-field',
              },
                el(
                  'span', {
                  className: 'saswp-book-field-label'
                },
                __('Series : ', 'schema-and-structured-data-for-wp')
                ),
                el( RichText, {                
                  tagName: 'p',
                  className:'saswp-book-field',
                  placeholder: __('Enter Series', 'schema-and-structured-data-for-wp'),                   
                  value: attributes.series,
                  autoFocus: true, 
                  onChange: function( newContent ) {                                
                      props.setAttributes( { series: newContent } );
                  }
                }            
              )
            ),

            el('div', {
              className: 'saswp-book-block-field',
              },
                el(
                  'span', {
                  className: 'saswp-book-field-label'
                },
                __('Author : ', 'schema-and-structured-data-for-wp')
                ),
                el( RichText, {                
                  tagName: 'p',
                  className:'saswp-book-field',
                  placeholder: __('Enter Author', 'schema-and-structured-data-for-wp'),                   
                  value: attributes.author,
                  autoFocus: true, 
                  onChange: function( newContent ) {                                
                      props.setAttributes( { author: newContent } );
                  }
                }            
              )
            ),


            el('div', {
              className: 'saswp-book-block-field',
              },
                el(
                  'span', {
                  className: 'saswp-book-field-label'
                },
                __('Genre : ', 'schema-and-structured-data-for-wp')
                ),
                el( RichText, {                
                  tagName: 'p',
                  className:'saswp-book-field',
                  placeholder: __('Enter Genre', 'schema-and-structured-data-for-wp'),                   
                  value: attributes.genre,
                  autoFocus: true, 
                  onChange: function( newContent ) {                                
                      props.setAttributes( { genre: newContent } );
                  }
                }            
              )
            ),


            el('div', {
              className: 'saswp-book-block-field',
              },
                el(
                  'span', {
                  className: 'saswp-book-field-label'
                },
                __('Publisher : ', 'schema-and-structured-data-for-wp')
                ),
                el( RichText, {                
                  tagName: 'p',
                  className:'saswp-book-field',
                  placeholder: __('Enter Publisher', 'schema-and-structured-data-for-wp'),                   
                  value: attributes.publisher,
                  autoFocus: true, 
                  onChange: function( newContent ) {                                
                      props.setAttributes( { publisher: newContent } );
                  }
                }            
              )
            ),


            el('div', {
              className: 'saswp-book-block-field',
              },
                el(
                  'span', {
                  className: 'saswp-book-field-label'
                },
                __('Release Date : ', 'schema-and-structured-data-for-wp')
                ),
                el( RichText, {                
                  tagName: 'p',
                  className:'saswp-book-field',
                  placeholder: __('Enter Release Date', 'schema-and-structured-data-for-wp'),                   
                  value: attributes.release_date,
                  autoFocus: true, 
                  onChange: function( newContent ) {                                
                      props.setAttributes( { release_date: newContent } );
                  }
                }            
              )
            ),


            el('div', {
              className: 'saswp-book-block-field',
              },
                el(
                  'span', {
                  className: 'saswp-book-field-label'
                },
                __('Format : ', 'schema-and-structured-data-for-wp')
                ),
                el( RichText, {                
                  tagName: 'p',
                  className:'saswp-book-field',
                  placeholder: __('Enter Format', 'schema-and-structured-data-for-wp'),                   
                  value: attributes.format,
                  autoFocus: true, 
                  onChange: function( newContent ) {                                
                      props.setAttributes( { format: newContent } );
                  }
                }            
              )
            ),


            el('div', {
              className: 'saswp-book-block-field',
              },
                el(
                  'span', {
                  className: 'saswp-book-field-label'
                },
                __('Pages : ', 'schema-and-structured-data-for-wp')
                ),
                el( RichText, {                
                  tagName: 'p',
                  className:'saswp-book-field',
                  placeholder: __('Enter Pages', 'schema-and-structured-data-for-wp'),                   
                  value: attributes.pages,
                  autoFocus: true, 
                  onChange: function( newContent ) {                                
                      props.setAttributes( { pages: newContent } );
                  }
                }            
              )
            ),

            el('div', {
              className: 'saswp-book-block-field',
              },
                el(
                  'span', {
                  className: 'saswp-book-field-label'
                },
                __('Source : ', 'schema-and-structured-data-for-wp')
                ),
                el( RichText, {                
                  tagName: 'p',
                  className:'saswp-book-field',
                  placeholder: __('Enter Source', 'schema-and-structured-data-for-wp'),                   
                  value: attributes.source,
                  autoFocus: true, 
                  onChange: function( newContent ) {                                
                      props.setAttributes( { source: newContent } );
                  }
                }            
              )
            ),

            el('div', {
              className: 'saswp-book-block-field',
              },
                el(
                  'span', {
                  className: 'saswp-book-field-label'
                },
                __('Rating : ', 'schema-and-structured-data-for-wp')
                ),
                createRating(attributes.rating)
            ),

            el('div', {
              className: 'saswp-book-block-field',
              },                    
                el( RichText, {                
                  tagName: 'p',
                  className:'saswp-book-field',
                  placeholder: __('Enter Description', 'schema-and-structured-data-for-wp'),                   
                  value: attributes.description,
                  autoFocus: true, 
                  onChange: function( newContent ) {                                
                      props.setAttributes( { description: newContent } );
                  }
                }            
              )
            )
          ),                            
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

