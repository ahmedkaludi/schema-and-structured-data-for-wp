
( function( blocks, element, editor, components, i18n ) {
    
    const { __ }          = i18n;
    
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
            
    blocks.registerBlockType( 'saswp/event-block', {
        title: __('Event (SASWP)', 'schema-and-structured-data-for-wp'),
        icon: 'list-view',
        category: 'saswp-blocks',
        keywords: ['schema', 'structured data', 'Event', 'event'],
        
        // Allow only one How To block per post.
        supports: {
                multiple: false
        },
                             
        edit: function( props ) {},
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

