( function( blocks, element ) {
    var el = element.createElement;
    
    blocks.registerBlockType( 'gutenberg-examples/example-01-basic', {
        title: saswpGutenberg.title,
        icon: 'shield-alt',
        category: 'common',
        edit: function() {
            return el(
                'input',                
                'Hello World, step 1 (from the editor).'
            );
        },
        save: function() {
            return el(
                'input',                
                'Hello World, step 1 (from the frontend).'
            );
        },
    } );
}(
    window.wp.blocks,
    window.wp.element
) );