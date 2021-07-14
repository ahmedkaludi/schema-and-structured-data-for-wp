
( function( blocks, element, editor, components, i18n ) {
            
    const el               = element.createElement;    
    const { __ }           = i18n;        
    const {SelectControl } = components;
                
    blocks.registerBlockType( 'saswp/location-block', {
        title: __('Location (SASWP)', 'schema-and-structured-data-for-wp'),
        icon:     'dashicons dashicons-location',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'Location', 'location'],
        
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
            
                if(saswpGutenbergLocation.location){
                    collection    = el(SelectControl,{
                        className:'saswp-rv-collection-list',                
                        value: props.attributes.id,
                        options:saswpGutenbergLocation.location,
                        onChange: function(value){

                             props.setAttributes( { id: parseInt(value) } );   
                        }
                      }            
                    );
                }                
                if(saswpGutenbergLocation.location_not_found){
                    
                    collection =   el('div',{className:'saswp-collection-not-found'},
                    __('Location not found ', 'schema-and-structured-data-for-wp'),
                    el('a',{
                        href:saswpGutenbergLocation.location_url
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

