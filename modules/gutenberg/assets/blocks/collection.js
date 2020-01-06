
( function( blocks, element, editor, components, i18n) {
            
    const el               = element.createElement;    
    const { __ }           = i18n;        
    const {SelectControl } = components;
                
    blocks.registerBlockType( 'saswp/collection-block', {
        title: __('Reviews Collections (SASWP)', 'schema-and-structured-data-for-wp'),
        icon:     'admin-comments',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'Reviews', 'reviews'],
        
        attributes:{
            id: {
                type: 'integer'             
            }
        },                     
        supports: {
                multiple: true
        },                                                            
        edit: function( props ) {
            
            var collection = '';
            
                if(saswpGutenbergCollection.collection){
                    collection    = el(SelectControl,{
                        className:'saswp-rv-collection-list',                
                        value: props.attributes.id,
                        options:saswpGutenbergCollection.collection,
                        onChange: function(value){

                             props.setAttributes( { id: parseInt(value) } );   
                        }
                      }            
                    );
                }                
                if(saswpGutenbergCollection.collection_not_found){
                    
                    collection =   el('div',{className:'saswp-collection-not-found'},
                    __('Collection not found ', 'schema-and-structured-data-for-wp'),
                    el('a',{
                        href:saswpGutenbergCollection.collection_url
                    },
                    __('Create One', 'schema-and-structured-data-for-wp'),
                    )
                    );
            
                }
                                                                          
            return collection;                        
        },
        save: function( props ) {
            return null;                        
        }
    } );
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components,
    window.wp.i18n    
) );

