<?php

/**
 * The admin-specific functionality of the plugin.
 */


class InvelityGlsParcelShopAdmin
{


    private $plugin_name;
    private $version;


    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action('admin_notices', [$this, 'customAdminNotices']);
    }

    public function enqueueStyles(){

    }

    public function customAdminNotices()
    {

        global $pagenow;

        if (isset($_GET['tab'])) {
            if ($pagenow == 'admin.php' && $_GET['page'] == 'wc-settings' && $_GET['tab'] == 'shipping') {

                global $wpdb;
                $table_name = $wpdb->prefix . 'inv_gls_parcel_shop';
                $result = $wpdb->get_results('SELECT * FROM ' . $table_name . '');
                if (!$result) {
                    echo "<div class=\"error\">";
                    echo "<p>'Chyba pri aktualizovaní pobočiek GlsParcelShop'</p>";
                    echo "</div>";

                } else {
                    echo "<div class=\"success\">";
                    echo "<p>'Pobočky GlsParcelShop pridané'</p>";
                    echo "</div>";
                }

            }
        }
    }

}
