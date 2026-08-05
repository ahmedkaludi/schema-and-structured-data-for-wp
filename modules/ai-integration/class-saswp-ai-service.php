<?php
/**
 * AI Service Class for Schema Generation
 *
 * @author   Magazine3
 * @category Core
 * @path     modules/ai-integration/class-saswp-ai-service
 * @version  1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Interface defining standard provider methods.
 */
interface SASWP_AI_Provider_Interface {
    public function get_api_url($api_key, $model);
    public function get_headers($api_key);
    public function get_body($prompt, $model);
    public function parse_response($response_body);
    public function fetch_models($api_key);
}

/**
 * Google Gemini API Provider
 */
class SASWP_Gemini_Provider implements SASWP_AI_Provider_Interface {
    public function get_api_url($api_key, $model) {
        $model = !empty($model) ? $model : 'gemini-1.5-flash';
        return "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . rawurlencode($api_key);
    }

    public function get_headers($api_key) {
        return array(
            'Content-Type' => 'application/json'
        );
    }

    public function get_body($prompt, $model) {
        return wp_json_encode(array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            )
        ));
    }

    public function parse_response($response_body) {
        $data = json_decode($response_body, true);
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }
        return '';
    }

    public function fetch_models($api_key) {
        if (empty($api_key)) {
            return array('success' => false, 'error' => esc_html__('Gemini API Key is required.', 'schema-and-structured-data-for-wp'));
        }
        $url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . rawurlencode($api_key);
        $response = wp_remote_get($url, array('timeout' => 15, 'sslverify' => false));

        if (is_wp_error($response)) {
            return array('success' => false, 'error' => $response->get_error_message());
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($code !== 200) {
            $msg = isset($data['error']['message']) ? $data['error']['message'] : esc_html__('Failed to fetch models from Google Gemini API.', 'schema-and-structured-data-for-wp');
            return array('success' => false, 'error' => $msg);
        }

        $models = array();
        if (isset($data['models']) && is_array($data['models'])) {
            foreach ($data['models'] as $m) {
                if (isset($m['supportedGenerationMethods']) && is_array($m['supportedGenerationMethods'])) {
                    if (!in_array('generateContent', $m['supportedGenerationMethods'])) {
                        continue;
                    }
                }
                $model_name = str_replace('models/', '', $m['name']);
                $display = isset($m['displayName']) ? $m['displayName'] . ' (' . $model_name . ')' : $model_name;
                $models[] = array(
                    'id'   => $model_name,
                    'name' => $display
                );
            }
        }

        if (empty($models)) {
            return array('success' => false, 'error' => esc_html__('No generation models found for this API key.', 'schema-and-structured-data-for-wp'));
        }

        return array('success' => true, 'models' => $models);
    }
}

/**
 * OpenAI API Provider
 */
class SASWP_OpenAI_Provider implements SASWP_AI_Provider_Interface {
    public function get_api_url($api_key, $model) {
        return 'https://api.openai.com/v1/chat/completions';
    }

    public function get_headers($api_key) {
        return array(
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key
        );
    }

    public function get_body($prompt, $model) {
        $model = !empty($model) ? $model : 'gpt-4o-mini';
        return wp_json_encode(array(
            'model'    => $model,
            'messages' => array(
                array(
                    'role'    => 'user',
                    'content' => $prompt
                )
            )
        ));
    }

    public function parse_response($response_body) {
        $data = json_decode($response_body, true);
        if (isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }
        return '';
    }

    public function fetch_models($api_key) {
        if (empty($api_key)) {
            return array('success' => false, 'error' => esc_html__('OpenAI API Key is required.', 'schema-and-structured-data-for-wp'));
        }
        $url = 'https://api.openai.com/v1/models';
        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key
            ),
            'timeout' => 15,
            'sslverify' => false
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'error' => $response->get_error_message());
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($code !== 200) {
            $msg = isset($data['error']['message']) ? $data['error']['message'] : esc_html__('Failed to fetch models from OpenAI API.', 'schema-and-structured-data-for-wp');
            return array('success' => false, 'error' => $msg);
        }

        $models = array();
        if (isset($data['data']) && is_array($data['data'])) {
            usort($data['data'], function($a, $b) {
                return strcmp($a['id'], $b['id']);
            });

            foreach ($data['data'] as $m) {
                $id = $m['id'];
                if (preg_match('/^(gpt|o1|o3|chatgpt)/i', $id)) {
                    $models[] = array(
                        'id'   => $id,
                        'name' => $id
                    );
                }
            }
        }

        if (empty($models)) {
            return array('success' => false, 'error' => esc_html__('No compatible models found for this API key.', 'schema-and-structured-data-for-wp'));
        }

        return array('success' => true, 'models' => $models);
    }
}

/**
 * Factory class to manage provider selection and execution
 */
class SASWP_AI_Service {

    public static function get_provider($provider_name) {
        switch ($provider_name) {
            case 'openai':
                return new SASWP_OpenAI_Provider();
            case 'gemini':
            default:
                return new SASWP_Gemini_Provider();
        }
    }

    public static function fetch_models($provider_name, $api_key) {
        $provider = self::get_provider($provider_name);
        return $provider->fetch_models($api_key);
    }

    public static function generate_schema($title, $content, $target_type, $post_id = 0) {
        $settings = get_option('sd_data', array());
        $provider_name = isset($settings['saswp_ai_provider']) ? $settings['saswp_ai_provider'] : 'gemini';
        $api_key = '';
        $model = '';

        if ($provider_name === 'gemini') {
            $api_key = isset($settings['saswp_ai_gemini_key']) ? $settings['saswp_ai_gemini_key'] : '';
            $model   = isset($settings['saswp_ai_gemini_model']) ? $settings['saswp_ai_gemini_model'] : 'gemini-1.5-flash';
        } elseif ($provider_name === 'openai') {
            $api_key = isset($settings['saswp_ai_openai_key']) ? $settings['saswp_ai_openai_key'] : '';
            $model   = isset($settings['saswp_ai_openai_model']) ? $settings['saswp_ai_openai_model'] : 'gpt-4o-mini';
        }

        if (empty($api_key)) {
            return array('success' => false, 'error' => esc_html__('API key is missing for the selected AI provider. Please configure it in settings.', 'schema-and-structured-data-for-wp'));
        }

        $provider = self::get_provider($provider_name);

        // Fetch real site & post metadata to feed into prompt
        $site_name      = get_bloginfo('name');
        $site_url       = get_home_url();
        $post_url       = $post_id ? get_permalink($post_id) : '';
        $author_name    = '';
        $author_url     = '';
        $date_published = '';
        $date_modified  = '';
        $image_url      = '';

        if ($post_id > 0) {
            $post_obj = get_post($post_id);
            if ($post_obj) {
                $author_id      = $post_obj->post_author;
                $author_name    = get_the_author_meta('display_name', $author_id);
                $author_url     = get_author_posts_url($author_id);
                $date_published = get_the_date('c', $post_id);
                $date_modified  = get_the_modified_date('c', $post_id);
                $thumbnail_id   = get_post_thumbnail_id($post_id);
                if ($thumbnail_id) {
                    $image_url = wp_get_attachment_image_url($thumbnail_id, 'full');
                }
            }
        }

        // Retrieve publisher logo
        $logo_url = '';
        if (isset($settings['sd_logo']['url']) && !empty($settings['sd_logo']['url'])) {
            $logo_url = $settings['sd_logo']['url'];
        } else {
            $custom_logo_id = get_theme_mod('custom_logo');
            if ($custom_logo_id) {
                $logo_data = wp_get_attachment_image_src($custom_logo_id, 'full');
                if ($logo_data && isset($logo_data[0])) {
                    $logo_url = $logo_data[0];
                }
            }
        }

        // Build contextual prompt
        $prompt = "You are a structured data expert. Analyze the following WordPress post content and generate a valid, optimized JSON-LD schema matching standard Schema.org specifications.\n\n";
        $prompt .= "REAL CONTEXT & METADATA (YOU MUST USE THESE EXACT VALUES AND NOT INVENT PLACEHOLDERS LIKE example.com OR Schema Generator Pro):\n";
        $prompt .= "- Site / Publisher Name: " . ($site_name ? $site_name : 'Website') . "\n";
        $prompt .= "- Site / Publisher URL: " . $site_url . "\n";
        if ($logo_url) {
            $prompt .= "- Publisher Logo URL: " . $logo_url . "\n";
        }
        if ($post_url) {
            $prompt .= "- Page / Post URL (@id & url): " . $post_url . "\n";
        }
        if ($author_name) {
            $prompt .= "- Author Name: " . $author_name . "\n";
        }
        if ($author_url) {
            $prompt .= "- Author Profile URL: " . $author_url . "\n";
        }
        if ($date_published) {
            $prompt .= "- Date Published (ISO 8601): " . $date_published . "\n";
        }
        if ($date_modified) {
            $prompt .= "- Date Modified (ISO 8601): " . $date_modified . "\n";
        }
        if ($image_url) {
            $prompt .= "- Featured Image URL: " . $image_url . "\n";
        }

        $prompt .= "\nPOST CONTENT TO ANALYZE:\n";
        $prompt .= "Post Title: " . $title . "\n";
        $prompt .= "Post Content: " . wp_strip_all_tags($content) . "\n\n";
        
        if (!empty($target_type) && $target_type !== 'auto') {
            $prompt .= "You MUST generate a schema of type: " . $target_type . ". Ensure all standard required properties for this schema type are included.\n";
        } else {
            $prompt .= "Dynamically auto-detect the best matching schema type (e.g. FAQPage, Article, Recipe, HowTo, Product, Event) based on the content. If the content has multiple questions and answers, generate an FAQPage schema or combine it if applicable.\n";
        }

        $prompt .= "CRITICAL INSTRUCTIONS:\n";
        $prompt .= "1. ALWAYS use the exact URLs, site name, author name, dates, and image URLs provided in REAL CONTEXT & METADATA above.\n";
        $prompt .= "2. DO NOT use generic placeholders like 'https://example.com', 'example.com', or fake author/publisher names.\n";
        $prompt .= "3. Return ONLY valid, raw JSON-LD markup starting with {\"@context\": \"https://schema.org\"...}. Do NOT wrap it in HTML <script> tags, markdown code blocks, backticks, or any conversational text. Return plain text JSON only.";

        $url = $provider->get_api_url($api_key, $model);
        $headers = $provider->get_headers($api_key);
        $body = $provider->get_body($prompt, $model);

        $response = wp_remote_post($url, array(
            'headers'     => $headers,
            'body'        => $body,
            'timeout'     => 30,
            'sslverify'   => false,
            'data_format' => 'body'
        ));

        if (is_wp_error($response)) {
            return array('success' => false, 'error' => $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);

        if ($response_code !== 200) {
            $decoded_err = json_decode($response_body, true);
            $err_msg = isset($decoded_err['error']['message']) ? $decoded_err['error']['message'] : esc_html__('API call failed with HTTP status code ', 'schema-and-structured-data-for-wp') . $response_code;
            return array('success' => false, 'error' => $err_msg);
        }

        $raw_text = $provider->parse_response($response_body);

        if (empty($raw_text)) {
            return array('success' => false, 'error' => esc_html__('Empty response received from AI provider.', 'schema-and-structured-data-for-wp'));
        }

        // Clean markdown backticks if provider returned them
        $clean_json = preg_replace('/^```(?:json)?\s*/i', '', trim($raw_text));
        $clean_json = preg_replace('/\s*```$/i', '', $clean_json);

        // Failsafe post-processing: replace any remaining example.com or placeholder strings with real site metadata
        if (!empty($site_url)) {
            $clean_json = str_replace(
                array('https://example.com', 'http://example.com', 'example.com'),
                array($site_url, $site_url, parse_url($site_url, PHP_URL_HOST)),
                $clean_json
            );
        }
        if (!empty($site_name)) {
            $clean_json = str_replace(
                array('Schema Generator Pro', 'Example Publisher', 'Example Organization'),
                array($site_name, $site_name, $site_name),
                $clean_json
            );
        }

        return array('success' => true, 'schema' => trim($clean_json));
    }
}
