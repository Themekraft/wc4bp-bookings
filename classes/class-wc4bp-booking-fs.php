<?php
/**
 * @package        WordPress
 * @subpackage     BuddyPress, Woocommerce, WC4BP
 * @author         ThemKraft Dev Team
 * @copyright      2017, Themekraft
 * @link           http://themekraft.com/store/woocommerce-buddypress-integration-wordpress-plugin/
 * @license        http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class wc4bp_bookings_fs {

    /**
     * Instance of this class.
     *
     * @var object
     */
    protected static $instance = null;

    public function __construct() {
        if ( $this->wc4bp_bookings_fs_is_parent_active_and_loaded() ) {
            // If parent already included, init add-on.
            $this->wc4bp_bookings_fs_init();
        } else if ( $this->wc4bp_bookings_fs_is_parent_active() ) {
            // Init add-on only after the parent is loaded.
            add_action( 'wc4bp_core_fs_loaded', array( $this, 'wc4bp_bookings_fs_init' ) );
        } else {
            // Even though the parent is not activated, execute add-on for activation / uninstall hooks.
            $this->wc4bp_bookings_fs_init();
        }
    }

    public function wc4bp_bookings_fs_is_parent_active_and_loaded() {
        // Check if the parent's init SDK method exists.
        return method_exists( 'WC4BP_Loader', 'wc4bp_fs' );
    }

    public function wc4bp_bookings_fs_is_parent_active() {
        $active_plugins_basenames = get_option( 'active_plugins' );

        foreach ( $active_plugins_basenames as $plugin_basename ) {
            if ( 0 === strpos( $plugin_basename, 'wc4bp/' ) ||
                0 === strpos( $plugin_basename, 'wc4bp-premium/' )
            ) {
                return true;
            }
        }

        return false;
    }

    public function wc4bp_bookings_fs_init() {
        if ( $this->wc4bp_bookings_fs_is_parent_active_and_loaded() ) {
            // Init Freemius.
            $this->start_freemius();
        }
    }

    public function start_freemius() {
        global $wc4bp_bookings_fs;

        if ( ! isset( $wc4bp_bookings_fs ) ) {
            // Include Freemius SDK.
            require_once WC4BP_ABSPATH_CLASS_PATH . 'includes/freemius/start.php';

	        try {
		        $wc4bp_bookings_fs = fs_dynamic_init( array(
			        'id'                  => '2225',
			        'slug'                => 'wc4bp-bookings',
			        'type'                => 'plugin',
			        'public_key'          => 'pk_6a342256996424892f2248475ceec',
			        'is_premium'          => true,
			        'is_premium_only'     => true,
			        // If your addon is a serviceware, set this option to false.
			        'has_premium_version' => true,
			        'has_paid_plans'      => true,
			        'is_org_compliant'    => false,
			        'trial'               => array(
				        'days'               => 7,
				        'is_require_payment' => false,
			        ),
			        'parent'              => array(
				        'id'         => '425',
				        'slug'       => 'wc4bp',
				        'public_key' => 'pk_71d28f28e3e545100e9f859cf8554',
				        'name'       => 'WC4BP',
			        ),
			        'menu'                => array(
				        'first-path'     => 'plugins.php',
				        'support'        => false,
			        ),
		        ) );
	        } catch ( Freemius_Exception $e ) {
		        $wc4bp_bookings_fs = false;
	        }
        }

        return $wc4bp_bookings_fs;
    }

    /**
     * @return Freemius
     */
    public static function getFreemius() {
        global $wc4bp_bookings_fs;

        return $wc4bp_bookings_fs;
    }

    /**
     * Return an instance of this class.
     *
     * @return object A single instance of this class.
     */
    public static function get_instance() {
        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}