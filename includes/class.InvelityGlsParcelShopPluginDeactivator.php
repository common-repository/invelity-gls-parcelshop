<?php

/**
 * Fired during plugin deactivation
 */

class InvelityGlsParcelShopPluginDeactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate() {

        global $wpdb;
        $wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "inv_gls_parcel_shop" );
    }

}
