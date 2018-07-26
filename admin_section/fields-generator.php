<?php
/*
  Metabox to show ads type such as custom and adsense 
 */
class fields_generator {
	
	
        public function __construct() {
		$this->media_fields();
	}                        
			
	public function media_fields() {
		?><script>
			jQuery(document).ready(function($){
				if ( typeof wp.media !== 'undefined' ) {
					var _custom_media = true,
					_orig_send_attachment = wp.media.editor.send.attachment;
					$('.mymetabox-media').click(function(e) {
						var send_attachment_bkp = wp.media.editor.send.attachment;
						var button = $(this);
						var id = button.attr('id').replace('_button', '');
						_custom_media = true;
							wp.media.editor.send.attachment = function(props, attachment){
							if ( _custom_media ) {
								$('input#'+id).val(attachment.url);
							} else {
								return _orig_send_attachment.apply( this, [props, attachment] );
							};
						}
						wp.media.editor.open(button);
						return false;
					});
					$('.add_media').on('click', function(){
						_custom_media = false;
					});
				}
			});
		</script><?php
	}
	public function saswp_field_generator( $meta_fields, $settings ) {            
		$output = '';
		foreach ( $meta_fields as $meta_field ) {
                    
                        $class = "";
                        $note = "";
                        if(array_key_exists('class', $meta_field)){
                        $class = $meta_field['class'];    
                        }
                        if(array_key_exists('note', $meta_field)){
                        $note = $meta_field['note'];     
                        }
			$label = '<label for="' . $meta_field['id'] . '">' . $meta_field['label'] . '</label>';			
			
			switch ( $meta_field['type'] ) {
				case 'media':
					$input = sprintf(
						'<input class="%s" style="width: 80%%" id="%s" name="%s" type="text" value="%s"> <input style="width: 19%%" class="button mymetabox-media" id="%s_button" name="%s_button" type="button" value="Upload" />',
                                                $class,
						$meta_field['id'],
						$meta_field['name'],
						$meta_field['id'],
						$meta_field['id'],
						$meta_field['id']
					);
					break;
				case 'checkbox':                                   
					$input = sprintf(
						'<input class="%s" id="%s" name="%s" type="checkbox" value="%s" %s>',
                                                $class,
                                                $meta_field['id'],    
						$meta_field['name'], 
                                                isset($settings[$meta_field['id']]),
                                                checked(1, isset($settings[$meta_field['id']]),true)
						);
					break;
				case 'select':
					$input = sprintf(
						'<select class="%s" id="%s" name="%s">',
                                                $class,
						$meta_field['id'],                                                
						$meta_field['name']
					);                                    
					foreach ( $meta_field['options'] as $key => $value ) {	                                            
						$input .= sprintf(
							'<option %s value="%s">%s</option>',
							$settings[$meta_field['id']] == $key ? 'selected' : '',                                                        
							$key,
							$value
						);
					}
					$input .= '</select>';
					break;
				default:
					$input = sprintf(
						'<input class="%s" %s id="%s" name="%s" type="%s" value="%s">',
                                                $class,
						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
						$meta_field['id'],
						$meta_field['name'],
						$meta_field['type'],
						$settings[$meta_field['id']]
					);
			}
                        
			$output .= '<tr><th>'.$label.'</th><td>'.$input.'<p>'.$note.'</p></td></tr>';
		}
		echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
	}	        
}
