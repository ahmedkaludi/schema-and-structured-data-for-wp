<?php
/**
 * Black Friday / Cyber Monday Promotional Banner Class
 * Replicates the exact banner from Ads for WP (https://www.adsforwp.com/)
 * All classes and IDs prefixed with saswp-
 *
 * @author   Magazine3
 * @category Admin
 * @package  SASWP
 * @version  1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SASWP_Black_Friday_Banner {

    /**
     * Expiry timestamp for the campaign matching adsforwp.com exactly.
     */
    const EXPIRY_DATE = 'Dec 31, 2026 23:59:59 GMT+0530';

    /**
     * Coupon code used in the campaign.
     */
    const COUPON_CODE = 'BFCM2026';

    /**
     * User meta key for tracking dismissal timestamp.
     */
    const DISMISS_META_KEY = 'saswp_bfcm_banner_dismissed_time';

    /**
     * Initialize hooks.
     */
    public static function init() {
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
        add_action( 'wp_ajax_saswp_dismiss_black_friday_banner', array( __CLASS__, 'ajax_dismiss' ) );
    }

    /**
     * Check if the banner should be active.
     *
     * @return bool
     */
    public static function is_banner_active() {
        $current_time = time();
        $expiry_time  = strtotime( self::EXPIRY_DATE );

        // 1. Check if campaign date has passed (after 31st Dec 2026).
        if ( $current_time > $expiry_time ) {
            return false;
        }

        // 2. Check if dismissed within the last 7 days.
        $user_id = get_current_user_id();
        if ( $user_id ) {
            $dismissed_time = get_user_meta( $user_id, self::DISMISS_META_KEY, true );
            if ( ! empty( $dismissed_time ) ) {
                $days_passed = ( $current_time - (int) $dismissed_time ) / DAY_IN_SECONDS;
                if ( $days_passed < 7 ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if current screen is the Schema Settings page.
     *
     * @return bool
     */
    public static function is_settings_screen() {
        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

        if ( $screen && 'saswp_page_structured_data_options' === $screen->id ) {
            return true;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ( isset( $_GET['page'] ) && 'structured_data_options' === $_GET['page'] ) {
            return true;
        }

        return false;
    }

    /**
     * Enqueue styles and scripts for the banner.
     *
     * @param string $hook Screen hook.
     */
    public static function enqueue_assets( $hook ) {
        if ( ! self::is_settings_screen() || ! self::is_banner_active() ) {
            return;
        }

        $css_ver = file_exists( SASWP_DIR_NAME . '/admin_section/css/saswp-black-friday-banner.css' )
            ? filemtime( SASWP_DIR_NAME . '/admin_section/css/saswp-black-friday-banner.css' )
            : SASWP_VERSION;

        $js_ver = file_exists( SASWP_DIR_NAME . '/admin_section/js/saswp-black-friday-banner.js' )
            ? filemtime( SASWP_DIR_NAME . '/admin_section/js/saswp-black-friday-banner.js' )
            : SASWP_VERSION;

        wp_enqueue_style(
            'saswp-bf-banner-css',
            SASWP_PLUGIN_URL . 'admin_section/css/saswp-black-friday-banner.css',
            array(),
            $css_ver
        );

        wp_enqueue_script(
            'saswp-bf-banner-js',
            SASWP_PLUGIN_URL . 'admin_section/js/saswp-black-friday-banner.js',
            array( 'jquery' ),
            $js_ver,
            true
        );

        wp_localize_script(
            'saswp-bf-banner-js',
            'saswp_bf_banner_data',
            array(
                'ajax_url'    => admin_url( 'admin-ajax.php' ),
                'nonce'       => wp_create_nonce( 'saswp_bf_banner_nonce' ),
                'expiry_date' => self::EXPIRY_DATE,
                'coupon_code' => self::COUPON_CODE,
            )
        );
    }

    /**
     * AJAX handler to record banner dismissal for 7 days.
     */
    public static function ajax_dismiss() {
        check_ajax_referer( 'saswp_bf_banner_nonce', 'security' );

        if ( function_exists( 'saswp_current_user_can' ) && ! current_user_can( saswp_current_user_can() ) ) {
            wp_send_json_error( array( 'message' => esc_html__( 'Unauthorized', 'schema-and-structured-data-for-wp' ) ), 403 );
        }

        $user_id = get_current_user_id();
        if ( $user_id ) {
            update_user_meta( $user_id, self::DISMISS_META_KEY, time() );
            wp_send_json_success( array( 'message' => esc_html__( 'Banner dismissed for 7 days.', 'schema-and-structured-data-for-wp' ) ) );
        }

        wp_send_json_error( array( 'message' => esc_html__( 'Failed to record dismissal.', 'schema-and-structured-data-for-wp' ) ) );
    }

    /**
     * Render the Black Friday / Cyber Monday promotional banner HTML.
     */
    public static function render_banner() {
        if ( ! self::is_settings_screen() || ! self::is_banner_active() ) {
            return;
        }

        $current_time = time();
        $expiry_time  = strtotime( self::EXPIRY_DATE );
        $diff         = max( 0, $expiry_time - $current_time );

        $days    = str_pad( floor( $diff / DAY_IN_SECONDS ), 2, '0', STR_PAD_LEFT );
        $hours   = str_pad( floor( ( $diff % DAY_IN_SECONDS ) / HOUR_IN_SECONDS ), 2, '0', STR_PAD_LEFT );
        $minutes = str_pad( floor( ( $diff % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS ), 2, '0', STR_PAD_LEFT );
        $seconds = str_pad( floor( $diff % MINUTE_IN_SECONDS ), 2, '0', STR_PAD_LEFT );

        $pricing_url = esc_url( 'https://structured-data-for-wp.com/bfcm-2026/' );
        ?>
        <div class="wrap saswp-bf-banner-container">
            <div id="saswp-bf-discount-bar" class="saswp-bf-discount-bar saswp-bf-theme-universal-luxury" role="region" aria-label="Black Friday 60% Discount Banner">
                <input id="saswp-bf-coupon-expiry" type="hidden" value="<?php echo esc_attr( self::EXPIRY_DATE ); ?>">
                <button type="button" class="saswp-bf-bar-close-btn" aria-label="<?php esc_attr_e( 'Dismiss Black Friday banner for 7 days', 'schema-and-structured-data-for-wp' ); ?>">&times;</button>
                
                <div class="saswp-bf-bar-inner">
                    <!-- Zone 1: Offer Text & 60% Hook -->
                    <div class="saswp-bf-col-offer">
                        <div class="saswp-bf-urgency-row">
                            <span class="saswp-bf-urgency-pill">
                                <span class="saswp-bf-pulse-dot"></span>
                                ⚡ <?php esc_html_e( 'BLACK FRIDAY / CYBER MONDAY / CHRISTMAS SALE', 'schema-and-structured-data-for-wp' ); ?>
                            </span>
                        </div>
                        <div class="saswp-bf-headline-group">
                            <div class="saswp-bf-badge-60">
                                <span class="saswp-bf-num"><?php esc_html_e( '60%', 'schema-and-structured-data-for-wp' ); ?></span>
                                <span class="saswp-bf-off"><?php esc_html_e( 'OFF', 'schema-and-structured-data-for-wp' ); ?></span>
                            </div>
                            <div class="saswp-bf-text-details">
                                <div class="saswp-bf-main-title"><?php esc_html_e( 'BIGGEST SALE IN OUR HISTORY', 'schema-and-structured-data-for-wp' ); ?> <span class="saswp-bf-highlight-gold"><?php esc_html_e( '— NEVER BEFORE SEEN!', 'schema-and-structured-data-for-wp' ); ?></span></div>
                                <div class="saswp-bf-sub-title"><?php esc_html_e( 'Sale is here! 60% off on Everything for a very limited time!!!', 'schema-and-structured-data-for-wp' ); ?></div>
                            </div>
                            <div class="saswp-bf-coupon-badge" title="<?php esc_attr_e( 'Click to copy coupon code', 'schema-and-structured-data-for-wp' ); ?>">
                                <span class="saswp-bf-coupon-label"><?php esc_html_e( 'CODE:', 'schema-and-structured-data-for-wp' ); ?></span>
                                <strong class="saswp-bf-coupon-code"><?php echo esc_html( self::COUPON_CODE ); ?></strong>
                                <span class="saswp-bf-copy-icon">📋</span>
                                <span class="saswp-bf-copy-tooltip"><?php esc_html_e( 'Copied!', 'schema-and-structured-data-for-wp' ); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Zone 2: High-Urgency Countdown Clock -->
                    <div class="saswp-bf-col-clock">
                        <div class="saswp-bf-clock-header">
                            <span>⏳ <?php esc_html_e( 'OFFER EXPIRES IN:', 'schema-and-structured-data-for-wp' ); ?></span>
                        </div>
                        <div id="saswp-bf-clockdiv" class="saswp-bf-clockdiv" aria-live="polite">
                            <div class="saswp-bf-clock-unit">
                                <span class="saswp-bf-clock-num saswp-bf-days"><?php echo esc_html( $days ); ?></span>
                                <span class="saswp-bf-clock-label"><?php esc_html_e( 'Days', 'schema-and-structured-data-for-wp' ); ?></span>
                            </div>
                            <span class="saswp-bf-clock-sep">:</span>
                            <div class="saswp-bf-clock-unit">
                                <span class="saswp-bf-clock-num saswp-bf-hours"><?php echo esc_html( $hours ); ?></span>
                                <span class="saswp-bf-clock-label"><?php esc_html_e( 'Hours', 'schema-and-structured-data-for-wp' ); ?></span>
                            </div>
                            <span class="saswp-bf-clock-sep">:</span>
                            <div class="saswp-bf-clock-unit">
                                <span class="saswp-bf-clock-num saswp-bf-minutes"><?php echo esc_html( $minutes ); ?></span>
                                <span class="saswp-bf-clock-label"><?php esc_html_e( 'Mins', 'schema-and-structured-data-for-wp' ); ?></span>
                            </div>
                            <span class="saswp-bf-clock-sep">:</span>
                            <div class="saswp-bf-clock-unit">
                                <span class="saswp-bf-clock-num saswp-bf-seconds"><?php echo esc_html( $seconds ); ?></span>
                                <span class="saswp-bf-clock-label"><?php esc_html_e( 'Secs', 'schema-and-structured-data-for-wp' ); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Zone 3: High-Converting CTA Button -->
                    <div class="saswp-bf-col-cta">
                        <a class="saswp-bf-bar-btn" href="<?php echo $pricing_url; ?>" target="_blank" rel="noopener noreferrer">
                            <span class="saswp-bf-btn-text"><?php esc_html_e( 'Claim The Offer Now', 'schema-and-structured-data-for-wp' ); ?></span>
                            <span class="saswp-bf-btn-icon">⚡</span>
                        </a>
                        <span class="saswp-bf-btn-sub">🔒 <?php esc_html_e( '30 Days Money Back Guarantee', 'schema-and-structured-data-for-wp' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

// Initialize banner hooks.
SASWP_Black_Friday_Banner::init();
