<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class SASWP_View_Taxonomy
{
    /**
     * The unique instance of the plugin.
     *
     * @var SASWP_View_Taxonomy
     */
    private static $instance;
	private $_taxonomy = array(
		'category',
		'post_tag',
		'product_cat',
		'product_tag'
	);

    /**
     * Gets an instance of our plugin.
     *
     * @return SASWP_View_Taxonomy
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct()
    {
        
		if(!empty($this->_taxonomy)){

			foreach ($this->_taxonomy as $value) {
				add_action( "{$value}_edit_form_fields", array($this, "saswp_edit_term_fields"), 10, 2);        
				add_action( "created_{$value}", array($this, "saswp_save_term_fields" ));
				add_action( "edited_{$value}", array($this, "saswp_save_term_fields" ));		
			}

		}		

    }

	public function saswp_edit_term_fields( $term, $taxonomy ) {
 
		$value = get_term_meta( $term->term_id, 'saswp_custom_schema_field', true );

		?>

		<tr class="form-field">
		<th>
			<label for="saswp_custom_schema_field"><?php echo saswp_t_string('Custom Schema') ?></label>
		</th>
		<td>
			<textarea rows="4" cols="50" name="saswp_custom_schema_field" id="saswp_custom_schema_field" placeholder="JSON-LD" type="text" ><?php echo esc_html( $value ); ?></textarea>
			<p class="description"><?php echo saswp_t_string('Please provide a valid JSON-LD') ?></p>
		</td>
		</tr>

		<?php
		 	 
	}
	
		 
	public function saswp_save_term_fields( $term_id ) {
	 
		if(isset($_POST['saswp_custom_schema_field'])){

			$allowed_html = saswp_expanded_allowed_tags(); 
                                                 
			$custom_schema  = wp_kses(wp_unslash($_POST['saswp_custom_schema_field']), $allowed_html);

			update_term_meta( $term_id, 'saswp_custom_schema_field', $custom_schema );

		}else{

			delete_term_meta( $term_id, 'saswp_custom_schema_field');  

		}
			 
	}
    
}
SASWP_View_Taxonomy::get_instance();