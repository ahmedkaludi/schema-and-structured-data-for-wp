
( function( blocks, element, editor, components, i18n) {
            
    var el               = element.createElement;    
    const { __ }         = i18n;    
    const { RichText,  AlignmentToolbar, BlockControls, InspectorControls, MediaUpload } = editor;
    const {RadioControl, Popover, Button, IconButton,  TextareaControl, TextControl, ToggleControl, PanelBody, DateTimePicker } = components;
                
    blocks.registerBlockType( 'saswp/course-block', {
        title: __('Course (SASWP)', 'schema-and-structured-data-for-wp'),
        icon: 'dashicons-book',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'Course', 'course'],
             
        // Allow only one How To block per post.
        supports: {
                multiple: false
        },
                                             
        edit: function( props ){
            
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

