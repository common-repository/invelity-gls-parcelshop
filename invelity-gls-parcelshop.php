<?php
/*
Plugin Name: Invelity GLS ParcelShop
Text Domain: invelity-gls-parcelshop
Domain Path:/languages
Plugin URI: https://www.invelity.com/sk/sluzby
Description:Plugin Invelity GLS ParcelShop
Author: Invelity
Author URI: https://www.invelity.com
Version: 1.0.2
*/


if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . '/wp-admin/includes/plugin.php');
}
$plugin_data = get_plugin_data( __FILE__ );
defined('ABSPATH') or die('No script kiddies please!');
define('INVELITY_GLS_PARCEL_SHOP_VERSION', $plugin_data['Version']);
define('INVELITY_GLS_PARCEL_PLUGIN_SLUG', $plugin_data['TextDomain']);

if (!class_exists('InvelityPluginsAdmin')) {
    require_once('admin/class.InvelityPluginsAdmin.php');
}

if (!function_exists('invelityGlsParcelShopCronAction')) {
    include_once(__DIR__ . '/invelityGlsParcelShopShops.php');
}

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}



function inv_gls_parcel_shop_check_requirements()
{
    if (is_plugin_active('woocommerce/woocommerce.php')) {
        return true;
    } else {
        add_action('admin_notices', 'invelity_gls_parcel_shop_missing_wc_notice');
        return false;
    }
}


function invelity_gls_parcel_shop_missing_wc_notice()
{
    $class = 'notice notice-error';
    $message = __('Invelity GLS Parcel Shop requires WooCommerce to be installed and active.', 'invelity-gls-parcel-shop');

    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}


function activateInvelityGlsParcelShopPlugin()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class.InvelityGlsParcelShopPluginActivator.php';
    InvelityGlsParcelShopPluginActivator::activate();
}

function deactivateInvelityGlsParcelShopPlugin()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class.InvelityGlsParcelShopPluginDeactivator.php';
    InvelityGlsParcelShopPluginDeactivator::deactivate();
}

add_action('plugins_loaded', 'inv_gls_parcel_shop_check_requirements');
add_action('wp', 'invelityGlsParcelShopCronActivation');


function invelityGlsParcelShopCronActivation() {
    if( !wp_next_scheduled( 'invelityGlsParcelShopCron' ) ) {
        wp_schedule_event( time(), 'daily', 'invelityGlsParcelShopCron' );
    }
}


function  invelityGlsParcelShopCronDeactivation() {
    $timestamp = wp_next_scheduled ('invelityGlsParcelShopCron');
    wp_unschedule_event ($timestamp, 'invelityGlsParcelShopCron');
}


function invelityGlsParcelShopCronJobAction() {
    invelityGlsParcelShopCronAction();
}

add_action ('invelityGlsParcelShopCron', 'invelityGlsParcelShopCronJobAction');



function activateInvelityGlsParcelShopPluginShowMessage() {
    set_transient( 'invelity-admin-message-gls-parcelshop', true, 5 );
}

add_action( 'admin_notices', 'invelityAdminMessageGlsParcelshop' );

function invelityAdminMessageGlsParcelshop(){

    if( get_transient( 'invelity-admin-message-gls-parcelshop' ) ){
        ?>
        <div class="updated notice is-dismissible">
            <p>Plugin úspešne nainštalovaný.Pridajte GLS ParcelShop<a href="<?= admin_url()?>admin.php?page=wc-settings&tab=shipping"> dopravnú metódu</a>.</p>
        </div>
        <?php
        delete_transient( 'invelity-admin-message-gls-parcelshop' );
    }
}

register_activation_hook(__FILE__, 'activateInvelityGlsParcelShopPlugin');
register_activation_hook( __FILE__, 'activateInvelityGlsParcelShopPluginShowMessage' );
register_deactivation_hook(__FILE__, 'deactivateInvelityGlsParcelShopPlugin');
register_deactivation_hook (__FILE__, 'invelityGlsParcelShopCronDeactivation');

require plugin_dir_path(__FILE__) . 'includes/class.InvelityGlsParcelShop.php';


/**
 * Begins execution of the plugin.
 * @since    1.0.0
 */
function runInvelityGlsParcelShop()
{
    if (inv_gls_parcel_shop_check_requirements()) {
        $plugin = new InvelityGlsParcelShop();
        $plugin->run();
        $plugin->displayInvelityPluginsAdmin();
    }

}

runInvelityGlsParcelShop();

