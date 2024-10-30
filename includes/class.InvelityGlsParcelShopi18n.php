<?php

/**
 * Define the internationalization functionality
 */


class InvelityGlsParcelShopi18N {


    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain(
            'invelity-gls-parcel-shop',
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );

    }



}
