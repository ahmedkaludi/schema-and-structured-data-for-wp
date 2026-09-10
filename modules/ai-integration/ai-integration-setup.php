<?php
/**
 * AI Integration Setup and Initializer
 *
 * @author   Magazine3
 * @category Core
 * @path     modules/ai-integration/ai-integration-setup.php
 * @version  1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Include Service class
require_once plugin_dir_path(__FILE__) . 'class-saswp-ai-service.php';

// Hooks
add_action('admin_enqueue_scripts', 'saswp_ai_enqueue_assets');
add_action('wp_ajax_saswp_generate_ai_schema', 'saswp_ajax_generate_ai_schema');
add_action('wp_ajax_saswp_fetch_ai_models', 'saswp_ajax_fetch_ai_models');

// Settings framework registration
add_filter('saswp_default_settings_vals', 'saswp_ai_default_settings');

// Hook background auto-generation on publish (just schedules, doesn't block save)
add_action('transition_post_status', 'saswp_ai_schedule_auto_generate', 10, 3);

// The actual cron worker that runs AI generation asynchronously
add_action('saswp_ai_run_auto_generate', 'saswp_ai_run_auto_generate_cron', 10, 1);

/**
 * Enqueue CSS/JS on post-edit and settings screens
 */
function saswp_ai_enqueue_assets($hook) {
    // Load CSS on settings pages to apply standard layout alignments

    $min = '';
    if ( SASWP_ENVIRONMENT == 'production' ) {
        $min    =   '.min';
    }

    if ( strpos($hook, 'structured_data_options') !== false ) {
        wp_enqueue_style( 'saswp-ai-style', plugin_dir_url(__FILE__) . "css/saswp-ai-style{$min}.css", array(), SASWP_VERSION );
        wp_enqueue_script( 'saswp-ai-settings', plugin_dir_url(__FILE__) . "js/saswp-ai-settings{$min}.js", array('jquery'), SASWP_VERSION, true );
        wp_localize_script( 'saswp-ai-settings', 'saswp_ai_settings_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('saswp_ajax_check_nonce'),
        ));
    }

    // Load full assets on post edit screens
    if ( $hook === 'post.php' || $hook === 'post-new.php' ) {
        $sd_data = get_option('sd_data', array());
        $enable  = isset($sd_data['saswp_ai_enable']) ? $sd_data['saswp_ai_enable'] : 0;
        if ( ! $enable ) {
            return;
        }
        wp_enqueue_style( 'saswp-ai-style', plugin_dir_url(__FILE__) . "css/saswp-ai-style{$min}.css", array(), SASWP_VERSION );
        wp_enqueue_script( 'saswp-ai-editor', plugin_dir_url(__FILE__) . "js/saswp-ai-editor{$min}.js", array('jquery'), SASWP_VERSION, true );
        wp_localize_script( 'saswp-ai-editor', 'saswp_ai_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('saswp_ajax_check_nonce'),
            'loading'  => esc_html__('Generating Schema with AI...', 'schema-and-structured-data-for-wp'),
            'success'  => esc_html__('Schema generated successfully!', 'schema-and-structured-data-for-wp')
        ));
    }
}

/**
 * Register defaults to plugin setting fields array
 */
function saswp_ai_default_settings($defaults) {
    $defaults['saswp_ai_enable']         = 0;
    $defaults['saswp_ai_provider']       = 'gemini';
    $defaults['saswp_ai_gemini_key']     = '';
    $defaults['saswp_ai_gemini_model']   = 'gemini-1.5-flash';
    $defaults['saswp_ai_openai_key']     = '';
    $defaults['saswp_ai_openai_model']   = 'gpt-4o-mini';
    $defaults['saswp_ai_auto_gen']       = 0;
    $defaults['saswp_ai_post_types']     = array();
    $defaults['saswp_ai_overwrite']      = 0;
    return $defaults;
}

/**
 * Render the AI Settings Tab Page Markup via WordPress Settings API Callback
 */
function saswp_ai_settings_callback() {
    $sd_data = get_option('sd_data', array());
    $field_objs = new SASWP_Fields_Generator();

    $saved_gemini_model = isset($sd_data['saswp_ai_gemini_model']) ? $sd_data['saswp_ai_gemini_model'] : 'gemini-1.5-flash';
    $saved_openai_model = isset($sd_data['saswp_ai_openai_model']) ? $sd_data['saswp_ai_openai_model'] : 'gpt-4o-mini';

    $gemini_options = array(
        'gemini-1.5-flash' => 'gemini-1.5-flash (Recommended, Fast & Low Cost)',
        'gemini-1.5-pro'   => 'gemini-1.5-pro (High Quality, Complex Pages)',
        'gemini-2.0-flash' => 'gemini-2.0-flash (Next-gen Fast & Versatile)',
        'gemini-2.0-flash-lite-preview-02-05' => 'gemini-2.0-flash-lite (Ultra-fast Preview)',
        'gemini-2.5-flash' => 'gemini-2.5-flash (Fast & Low Cost)',
        'gemini-2.5-pro'   => 'gemini-2.5-pro (High Quality)',
        'gemini-3.0-flash' => 'gemini-3.0-flash (Next-gen Fast & Versatile)',
        'gemini-3.0-pro'   => 'gemini-3.0-pro (Next-gen High Quality)',
        'gemini-3.1-flash' => 'gemini-3.1-flash (Next-gen Fast & Versatile)',
        'gemini-3.1-pro'   => 'gemini-3.1-pro (Next-gen High Quality)',
        'gemini-1.0-pro'   => 'gemini-1.0-pro (Legacy Stable)'
    );
    if ( ! in_array( $saved_gemini_model, $gemini_options ) ) {
        $gemini_options[$saved_gemini_model] = $saved_gemini_model;
    }

    $openai_options = array(
        'gpt-4o-mini' => 'gpt-4o-mini (Recommended, Fast & Cost Effective)',
        'gpt-4o'      => 'gpt-4o (High Intelligence Flagship)',
        'o3-mini'     => 'o3-mini (Next-gen Fast Reasoning)',
        'o1'          => 'o1 (Complex Reasoning)',
        'o1-mini'     => 'o1-mini (Reasoning Speed Focus)',
        'gpt-4-turbo' => 'gpt-4-turbo (Stable Legacy High-performance)',
        'gpt-4'       => 'gpt-4 (Stable Legacy)',
        'gpt-3.5-turbo' => 'gpt-3.5-turbo (Legacy Fast)'
    );
    if ( ! in_array( $saved_openai_model, $openai_options ) ) {
        $openai_options[$saved_openai_model] = $saved_openai_model;
    }

    $selected_provider = isset($sd_data['saswp_ai_provider']) ? $sd_data['saswp_ai_provider'] : 'gemini';
    $unselected        = ($selected_provider === 'gemini') ? 'openai' : 'gemini';

    // Output initial CSS to hide unselected provider row before DOM paint
    echo "<style>.saswp-ai_settings li:has(.saswp-ai-row." . esc_attr($unselected) . ") { display: none; }</style>";

    // Output our settings wrapper div, showing/hiding it exactly like native tabs
    echo "<div class='saswp-ai-settings-tab-wrapper' style='width: 100%;'>";
    ?>
    <div class="saswp-tools" id="saswp-tools-ai-container">
        <!-- Sub-Tab 1: AI Providers -->
        <div id="saswp-ai-providers-tab" class="saswp-ai-tab-content">
            <h2 class="saswp-advanced-heading"><?php echo esc_html__( 'AI Settings', 'schema-and-structured-data-for-wp' ); ?></h2> 
            <?php
            $provider_fields = array(
                array(
                    'label'  => esc_html__('AI Schema Generation', 'schema-and-structured-data-for-wp'),
                    'id'     => 'saswp-ai-enable-checkbox',                        
                    'name'   => 'saswp-ai-enable-checkbox',
                    'type'   => 'checkbox',
                    'class'  => 'checkbox saswp-checkbox',
                    'note'   => esc_html__('This option enables AI automatic generation and post metabox controls.', 'schema-and-structured-data-for-wp'),
                    'hidden' => array(
                         'id'   => 'saswp_ai_enable',
                         'name' => 'sd_data[saswp_ai_enable]',                             
                    )
                ),
                array(
                    'label'  => esc_html__('Auto-Generate on Publish', 'schema-and-structured-data-for-wp'),
                    'id'     => 'saswp-ai-auto-gen-checkbox',                        
                    'name'   => 'saswp-ai-auto-gen-checkbox',
                    'type'   => 'checkbox',
                    'class'  => 'checkbox saswp-checkbox',
                    'note'   => esc_html__('Automatically generate and save schema via AI behind-the-scenes on first post publish.', 'schema-and-structured-data-for-wp'),
                    'hidden' => array(
                         'id'   => 'saswp_ai_auto_gen',
                         'name' => 'sd_data[saswp_ai_auto_gen]',                             
                    )
                ),
                array(
                    'label'  => esc_html__('Overwrite Manual Schema', 'schema-and-structured-data-for-wp'),
                    'id'     => 'saswp-ai-overwrite-checkbox',                        
                    'name'   => 'saswp-ai-overwrite-checkbox',
                    'type'   => 'checkbox',
                    'class'  => 'checkbox saswp-checkbox',
                    'note'   => esc_html__('If checked, background automation is allowed to overwrite manual schema entries.', 'schema-and-structured-data-for-wp'),
                    'hidden' => array(
                         'id'   => 'saswp_ai_overwrite',
                         'name' => 'sd_data[saswp_ai_overwrite]',                             
                    )
                ),
                array(
                    'label'  => esc_html__('Target Post Types', 'schema-and-structured-data-for-wp'),
                    'id'     => 'saswp-ai-target-post-types',                        
                    'type'   => 'saswp-ai-target-post-types',
                    'note'   => esc_html__('Check the types to automatically generate the schema on first publish.', 'schema-and-structured-data-for-wp'),
                ),
                array(
                    'label'   => esc_html__('Active AI Provider', 'schema-and-structured-data-for-wp'),
                    'id'      => 'saswp_ai_provider',
                    'name'    => 'sd_data[saswp_ai_provider]',
                    'class'   => 'regular-text',
                    'type'    => 'select',
                    'options' => array(
                        'gemini' => esc_html__('Google Gemini', 'schema-and-structured-data-for-wp'),
                        'openai' => esc_html__('OpenAI (ChatGPT)', 'schema-and-structured-data-for-wp')
                    )
                ),
                array(
                    'label' => esc_html__('Gemini API Key', 'schema-and-structured-data-for-wp'),
                    'id'    => 'saswp_ai_gemini_key',
                    'name'  => 'sd_data[saswp_ai_gemini_key]',
                    'type'  => 'text',
                    'class' => 'regular-text saswp-ai-row gemini'
                ),
                array(
                    'label'   => esc_html__('Gemini Model', 'schema-and-structured-data-for-wp'),
                    'id'      => 'saswp_ai_gemini_model',
                    'name'    => 'sd_data[saswp_ai_gemini_model]',
                    'class'   => 'regular-text saswp-ai-row gemini',
                    'type'    => 'select',
                    'options' => $gemini_options
                ),
                array(
                    'label' => esc_html__('OpenAI API Key', 'schema-and-structured-data-for-wp'),
                    'id'    => 'saswp_ai_openai_key',
                    'name'  => 'sd_data[saswp_ai_openai_key]',
                    'type'  => 'text',
                    'class' => 'regular-text saswp-ai-row openai'
                ),
                array(
                    'label'   => esc_html__('OpenAI Model', 'schema-and-structured-data-for-wp'),
                    'id'      => 'saswp_ai_openai_model',
                    'name'    => 'sd_data[saswp_ai_openai_model]',
                    'class'   => 'regular-text saswp-ai-row openai',
                    'type'    => 'select',
                    'options' => $openai_options
                ),
            );
            $field_objs->saswp_field_generator($provider_fields, $sd_data);
            ?>
        </div>
    </div>

    <!-- JS logic is loaded via saswp-ai-settings.js (enqueued in saswp_ai_enqueue_assets) -->
    <?php
    echo "</div>";
}

/**
 * AJAX Handler to fetch available models from Provider API
 */
function saswp_ajax_fetch_ai_models() {
    check_ajax_referer('saswp_ajax_check_nonce', 'saswp_security_nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('error' => esc_html__('Unauthorized capability.', 'schema-and-structured-data-for-wp')));
    }

    $provider = isset($_POST['provider']) ? sanitize_text_field($_POST['provider']) : '';
    $api_key  = isset($_POST['api_key']) ? sanitize_text_field($_POST['api_key']) : '';

    if (empty($provider) || empty($api_key)) {
        wp_send_json_error(array('error' => esc_html__('Provider and API key are required to fetch models.', 'schema-and-structured-data-for-wp')));
    }

    $result = SASWP_AI_Service::fetch_models($provider, $api_key);

    if (!$result['success']) {
        wp_send_json_error(array('error' => $result['error']));
    }

    wp_send_json_success(array('models' => $result['models']));
}

/**
 * AJAX Handler to generate schema
 */
function saswp_ajax_generate_ai_schema() {
    check_ajax_referer('saswp_ajax_check_nonce', 'saswp_security_nonce');

    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('error' => esc_html__('Unauthorized capability.', 'schema-and-structured-data-for-wp')));
    }

    $post_id     = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    $target_type = isset($_POST['target_type']) ? sanitize_text_field($_POST['target_type']) : 'auto';

    if (!$post_id) {
        wp_send_json_error(array('error' => esc_html__('Invalid post ID.', 'schema-and-structured-data-for-wp')));
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( array( 'error' => esc_html__( 'Unauthorized capability.', 'schema-and-structured-data-for-wp' ) ) );
    }

    $post = get_post($post_id);
    if (!$post) {
        wp_send_json_error(array('error' => esc_html__('Post not found.', 'schema-and-structured-data-for-wp')));
    }

    $result = SASWP_AI_Service::generate_schema($post->post_title, $post->post_content, $target_type, $post_id);

    if (!$result['success']) {
        wp_send_json_error(array('error' => $result['error']));
    }

    wp_send_json_success(array('schema' => $result['schema']));
}

/**
 * Schedule async schema generation when a post is published.
 * Runs fast — only validates conditions and queues a cron job.
 */
function saswp_ai_schedule_auto_generate($new_status, $old_status, $post) {
    // Only on first-time publish transitions
    if ($new_status !== 'publish' || $old_status === 'publish') {
        return;
    }
    if (wp_is_post_autosave($post->ID) || wp_is_post_revision($post->ID)) {
        return;
    }

    $sd_data  = get_option('sd_data', array());
    $enable   = intval(isset($sd_data['saswp_ai_enable'])   ? $sd_data['saswp_ai_enable']   : 0);
    $auto_gen = intval(isset($sd_data['saswp_ai_auto_gen']) ? $sd_data['saswp_ai_auto_gen'] : 0);

    // Resolve target post types
    if (isset($sd_data['saswp_ai_post_types']) && is_array($sd_data['saswp_ai_post_types'])) {
        $post_types = $sd_data['saswp_ai_post_types'];
    } else {
        $post_types = array('post');
    }

    if ($enable !== 1 || $auto_gen !== 1 || !in_array($post->post_type, $post_types)) {
        return;
    }

    $overwrite = intval(isset($sd_data['saswp_ai_overwrite']) ? $sd_data['saswp_ai_overwrite'] : 0);
    $existing  = get_post_meta($post->ID, 'saswp_custom_schema_field', true);
    if (!empty($existing) && $overwrite !== 1) {
        return;
    }

    // Schedule an async cron event to do the actual AI call (runs in next WP-Cron cycle)
    if (!wp_next_scheduled('saswp_ai_run_auto_generate', array($post->ID))) {
        wp_schedule_single_event(time(), 'saswp_ai_run_auto_generate', array($post->ID));

        // PRE-SET the toggle to "enabled" (custom = 0) RIGHT NOW, synchronously.
        // This ensures the custom schema section is visible when the user opens
        // the post editor after publish — even before the cron AI call completes.
        $schema_enable = get_post_meta($post->ID, 'saswp_enable_disable_schema', true);
        if (!is_array($schema_enable)) {
            $schema_enable = array();
        }
        // $schema_enable['custom'] = 0; // 0 = enabled / schema visible
        update_post_meta($post->ID, 'saswp_enable_disable_schema', $schema_enable);

        // Immediately spawn a non-blocking cron request so the event fires right away
        spawn_cron();
    }
}

/**
 * Actual AI schema generation — runs inside a WP-Cron event, safely asynchronous.
 */
function saswp_ai_run_auto_generate_cron($post_id) {
    $post = get_post($post_id);
    if (!$post || $post->post_status !== 'publish') {
        return;
    }

    $sd_data   = get_option('sd_data', array());
    $overwrite = intval(isset($sd_data['saswp_ai_overwrite']) ? $sd_data['saswp_ai_overwrite'] : 0);

    // Re-check overwrite guard in cron context
    $existing = get_post_meta($post_id, 'saswp_custom_schema_field', true);
    if (!empty($existing) && $overwrite !== 1) {
        return;
    }

    // Determine schema type from mapping
    $target_type    = 'auto';

    // Generate schema via AI (this is the slow external HTTP call)
    $result = SASWP_AI_Service::generate_schema($post->post_title, $post->post_content, $target_type, $post_id);

    if ($result['success'] && !empty($result['schema'])) {
        update_post_meta($post_id, 'saswp_custom_schema_field', $result['schema']);

        // Ensure custom schema is ENABLED (custom = 0 means enabled in plugin logic)
        $schema_enable = get_post_meta($post_id, 'saswp_enable_disable_schema', true);
        if (!is_array($schema_enable)) {
            $schema_enable = array();
        }
        $schema_enable['custom'] = 0; // 0 = enabled/visible
        update_post_meta($post_id, 'saswp_enable_disable_schema', $schema_enable);
    }
}
