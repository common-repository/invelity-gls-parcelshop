<?php

/**
 * The public-facing functionality of the plugin.
 */

class InvelityGlsParcelShopPublic
{


    private $plugin_name;
    private $version;


    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('woocommerce_after_shipping_rate', function ($method) {
            if (is_checkout()) {
                if ($method->get_id() == 'inv_gls_parcel_shop') {
                    if (WC()->session->chosen_shipping_methods[0] == 'inv_gls_parcel_shop') {
                        ?>
                        <div class="invelity-gls-parcelshop-box">
                            <div>
                                <img src="<?= plugin_dir_url(__FILE__) ?>img/gls-logo.png"
                                     style="width: 50px;height: auto">
                                <a id="gls_parcel_shop_map_init" class="gls_parcel_shop_map_init">Vybrať pobočku</a>
                            </div>
                            <div>
                                <span id="inv_gls_parcel_shop_picked_shop_name"></span>
                                <input type="hidden" name="inv_gls_picked_shop_id" value="">
                                <input type="hidden" name="inv_gls_picked_shop_name" value="">
                                <input type="hidden" name="inv_gls_picked_shop_address" value="">
                            </div>
                        </div>
                        <?php
                    }
                }
            }
        }, 20, 2);

        add_action('wp_head', function () {
            if (is_checkout()) {
                ?>
                <div id="inv_gls_map_init_container"></div>
                <?php
            }
        });

        add_action('woocommerce_checkout_process', [$this, 'checkField']);

        add_action('woocommerce_checkout_update_order_meta', [$this, 'updateOrderMetaParcelShopInformations'], 15, 2);
        add_action('wp_ajax_invGlsParcelShopOpenMap', [$this, 'invGlsParcelShopOpenMap']);
        add_action('wp_ajax_nopriv_invGlsParcelShopOpenMap', [$this, 'invGlsParcelShopOpenMap']);


    }

    public function invGlsParcelShopOpenMap()
    {

        if (!function_exists('displayGlsParcelShops')) {
            require_once(plugin_dir_url(__FILE__) . 'invelityGlsParcelShopShops.php');
        }

        $shops = displayGlsParcelShops();
        ob_start();
        ?>
        <div class="inv_gls_parcelshop_pop_up_wrapper">
            <div class="inv_gls_parcelshop_pop_up_container">
                <div class="inv_gls_parcelshop_pop-up_list">

                    <img src="<?= plugin_dir_url(__FILE__) ?>/img/gls-logo.png">
                    <h6>Vyberte si svoj ParcelShop</h6>
                    <div class="inv_gls_parcelshop_finder_field">
                        <input name="inv_gls_parcelshop_find" id="inv_gls_parcelshop_find"
                               placeholdee="Hľadať podľa vašej adresy ...">
                        <span class="gg-search"></span>
                    </div>
                    <div class="inv_geolocation">
                        <input id="inv_gls_geolocation" class="checkbox-custom" name="inv_gls_geolocation"
                               type="checkbox" checked>
                        <label for="inv_gls_geolocation" class="checkbox-custom-label"><i class="gg-pin"></i> Načítať
                            moju polohu</label>
                    </div>

                    <div class="inv_gls_parcelshop_list_shops_wrapper">
                        <ul class="inv_gls_parcelshop_list_shops" id="inv_gls_parcelshop_list_shops">
                            <li class="all">
                                <p>Zobraz všetky pobočky</p>
                            </li>
                            <?php
                            foreach ($shops as $shop) {

                                $hours = displayParcelShopData($shop->DATA);
                                ?>
                                <li class="inv_gls_parcelshop_list_shop" data-name="<?= $shop->NAME ?>"
                                    data-address="<?= $shop->ADDRESS ?>"
                                    data-shopid="<?= $shop->GLS_PARCEL_SHOP ?>" data-lat="<?= $shop->GEO_LAT ?>"
                                    data-long="<?= $shop->GEO_LONG ?>" data-status="<?= $shop->STATUS ?>">
                                    <div class="inv_gls_parcelshop_list_shop_header">
                                        <h6><?= $shop->PARCEL_SHOP ?></h6>
                                        <p><?= $shop->NAME ?></p>
                                        <i class="gg-chevron-down"></i>
                                        <i class="gg-chevron-up" style="display:none;"></i>
                                    </div>

                                    <div class="inv_gls_parcelshop_list_shop_additional_info">
                                        <p>
                                            <?php

                                            foreach ($hours as $day => $value) {

                                                if ($value['OpenHours'] !== null || $value['OpenHours'] !== '') {
                                                    printf(__('%s', 'invelity-gls-parcel-shop'), $day);
                                                    echo ': ' . $value['OpenHours'] . '<br>';
                                                }

                                            }
                                            ?>

                                        </p>
                                        <button type="submit" class="inv_gls_parcelshop_pick_shop"
                                                id="inv_gls_parcelshop_pick_shop">Vybrat pobočku
                                        </button>

                                    </div>

                                </li>
                            <?php }
                            ?>
                        </ul>
                    </div>

                </div>
                <div class="inv_gls_parcelshop_map_section_container">
                    <div class="inv_gls_parcelshop_map inv_gls_parcelshop_map_container" id="map" data-type="1"
                         data-lat="48.148115" data-lon="17.104946"></div>
                </div>


            </div>
            <span id="close-invelity-gls-parcel-shop-modal">Zavrieť <i class="gg-close-o"></i></span>
        </div>

        <?php
        echo ob_get_clean();
        wp_die();

    }


    public function enqueueStyles()
    {
        if (is_checkout()) {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/invelity-gls-parcel-shop-public.css', array(), $this->version, 'all');
            wp_enqueue_style('leaflet.css', plugin_dir_url(__FILE__) . 'css/leaflet.css', array(), $this->version, 'all');

        }
    }

    public function enqueueScripts()
    {


        if (is_checkout()) {
            wp_enqueue_script('leaflet', plugin_dir_url(__FILE__) . 'js/leaflet.js', array('jquery'), $this->version, true);
            wp_enqueue_script('leaflet-providers', plugin_dir_url(__FILE__) . 'js/leaflet-providers.js', array('jquery'), $this->version, true);
            wp_enqueue_script('online-gls', '//online.gls-hungary.com/psmap/psmap.js', array('jquery'), $this->version, true);
            wp_enqueue_script('googleapis-gls', '//maps.googleapis.com/maps/api/js?v=3&sensor=false', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/invelity-gls-parcel-shop-public.js', array('jquery'), $this->version, true);
            wp_localize_script($this->plugin_name, 'inv_globals', [
                'pluginUrl' => plugin_dir_url(__FILE__),
                'ajax_url' => admin_url('admin-ajax.php'),
            ]);
        }

    }

    public function checkField()
    {
        if (!empty(WC()->session->chosen_shipping_methods[0]) && WC()->session->chosen_shipping_methods[0] == 'inv_gls_parcel_shop') {
            if (!$_POST['inv_gls_picked_shop_id']) {
                wc_add_notice(__('Prosím, vyberte pobočku GLS Parcelshop.', $this->plugin_slug), 'error');
            }
        }
    }

    public function updateOrderMetaParcelShopInformations($order_id)
    {

        $doprava_name = explode('>', WC()->session->chosen_shipping_methods[0]);

        if (!empty($doprava_name[0]) && $doprava_name[0] == 'inv_gls_parcel_shop') {

            $saniteText = sanitize_text_field($_POST['inv_gls_picked_shop_id']);

            if (!empty($saniteText)) {

                global $wpdb;

                $query = "SELECT * FROM " . $wpdb->prefix . "inv_gls_parcel_shop WHERE GLS_PARCEL_SHOP = '" . $saniteText . "'";
                $data = $wpdb->prepare($query);
                $data = $wpdb->get_results($data);

                $street = $data[0]->ADDRESS;
                $postcode = $data[0]->ZIP;
                $city = $data[0]->CITY;

                update_post_meta($order_id, 'inv_gls_picked_shop_id', sanitize_text_field($_POST['inv_gls_picked_shop_id']));
                update_post_meta($order_id, 'inv_gls_picked_shop_name', sanitize_text_field($_POST['inv_gls_picked_shop_name']));
                update_post_meta($order_id, 'inv_gls_picked_shop_address', sanitize_text_field($_POST['inv_gls_picked_shop_address']));
                update_post_meta($order_id, 'inv_gls_parcelshop_id_dopravy', WC()->session->chosen_shipping_methods[0]);
                update_post_meta($order_id, '_shipping_city', sanitize_text_field($city));
                update_post_meta($order_id, '_shipping_postcode', sanitize_text_field($postcode));
                update_post_meta($order_id, '_shipping_address_1', sanitize_text_field($street));


            }
        }
    }


}
