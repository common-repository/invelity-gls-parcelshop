<?php

/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    InvelityGLSParcelShop
 * @author    Invelity
 */
class InvelityGlsParcelShop
{

    protected $loader;
    protected $plugin_name;
    protected $version;


    public function __construct()
    {
        if (defined('INVELITY_GLS_PARCEL_SHOP_VERSION')) {
            $this->version = INVELITY_GLS_PARCEL_SHOP_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = INVELITY_GLS_PARCEL_PLUGIN_SLUG;

        $this->loadDependencies();
        $this->setLocale();
        $this->definePublicHooks();
        $this->addShippingMethod();
        $this->createTable();

    }


    private function loadDependencies()
    {

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class.InvelityGlsParcelShopLoader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/WC_Gls_Parcel_Shop_Shipping_Method.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class.InvelityGlsParcelShopi18n.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class.InvelityGlsParcelShopAdmin.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class.InvelityGlsParcelShopPublic.php';


        $this->loader = new InvelityGlsParcelShopLoader();

    }


    private function setLocale()
    {

        $plugin_i18n = new InvelityGlsParcelShopi18N();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }



    private function definePublicHooks()
    {

        $plugin_public = new InvelityGlsParcelShopPublic($this->getPluginName(), $this->getVersion());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueueStyles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueueScripts');

    }

    public function run()
    {
        $this->loader->run();
    }


    public function getPluginName()
    {
        return $this->plugin_name;
    }

    public function getLoader()
    {
        return $this->loader;
    }


    public function getVersion()
    {
        return $this->version;
    }

    public function displayInvelityPluginsAdmin()
    {

        new InvelityPluginsAdmin($this);


    }

    public function getPluginUrl()
    {
        return plugin_dir_url(__FILE__);
    }


    public function createTable() {


        add_action('init',function (){
            global $wpdb;

            $wpdb->hide_errors();

            $collate = '';

            if ( $wpdb->has_cap( 'collation' ) ) {
                if ( ! empty($wpdb->charset ) ) {
                    $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
                }
                if ( ! empty($wpdb->collate ) ) {
                    $collate .= " COLLATE $wpdb->collate";
                }
            }

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            $table = "
			CREATE TABLE {$wpdb->prefix}inv_gls_parcel_shop (
      			`ID` bigint(21) NOT NULL AUTO_INCREMENT,
				`ID_PARCEL_SHOP` bigint(21),
				`GLS_PARCEL_SHOP` varchar(100) COLLATE utf8_czech_ci NOT NULL,
      			`NAME` varchar(100) COLLATE utf8_czech_ci NOT NULL,
      			`PARCEL_SHOP` varchar(200) COLLATE utf8_czech_ci NOT NULL,
      			`ADDRESS` varchar(100) COLLATE utf8_czech_ci NOT NULL,
      			`CP` varchar(10) COLLATE utf8_czech_ci NOT NULL,
      			`CITY` varchar(100) COLLATE utf8_czech_ci NOT NULL,
      			`ZIP` varchar(10) COLLATE utf8_czech_ci NOT NULL,
      			`COUNTRY` varchar(10) COLLATE utf8_czech_ci NOT NULL,
      			`GEO_LAT` varchar(20) COLLATE utf8_czech_ci NOT NULL,
      			`GEO_LONG` varchar(20) COLLATE utf8_czech_ci NOT NULL,
      			`STATUS` varchar(10) COLLATE utf8_czech_ci NOT NULL,
      			`DATA` longtext COLLATE utf8_czech_ci NOT NULL,
      			PRIMARY KEY (`ID`)
			) $collate;
		";
            dbDelta( $table );
        });


    }


    public function addShippingMethod()
    {

        add_action('woocommerce_shipping_init', 'wooGlsParcelShopShippingInit');

        add_filter('woocommerce_shipping_methods', function ($methods) {
            $methods['inv_gls_parcel_shop'] = 'WC_Gls_Parcel_Shop_Shipping_Method';
            return $methods;
        });

        add_action('wp_ajax_woocommerce_shipping_zone_add_method', 'invelityGlsParcelShopFirstInitShops');
    }

}
