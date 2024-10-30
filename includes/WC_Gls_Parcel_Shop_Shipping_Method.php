<?php
function wooGlsParcelShopShippingInit()
{
    if (!class_exists('WC_Shipping_Method'))
        return;
    if (!class_exists('WC_Gls_Parcel_Shop_Shipping_Method')) {
        class WC_Gls_Parcel_Shop_Shipping_Method extends WC_Shipping_Method
        {


            public function __construct( $instance_id = 0)
            {

                $this->id = 'inv_gls_parcel_shop';
                $this->method_title = __('GLS Parcel Shop', 'invelity-gls-parcel-shop');
                $this->method_description = __('GLS Parcel Shop', 'invelity-gls-parcel-shop');
                $this->enabled = 'yes';
                $this->tax_status           = 'taxable';
                $this->instance_id        = absint( $instance_id );
                $this->title = 'GLS Parcel Shop';
                $this->supports = array(
                    'shipping-zones',
                    'instance-settings',
                    'instance-settings-modal',
                );

                $this->init();

                add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
            }


             function init()
            {

                $this->init_form_fields();
                $this->init_settings();

                $this->title        = $this->get_option( 'title' );
                $this->cost         = $this->get_option( 'cost' );
                $this->tax_status         = $this->get_option( 'tax_status' );


            }

            public function init_form_fields() {
                $this->instance_form_fields  = array(

                    'title' => array(
                        'title'         => __( 'Method Title', 'woocommerce' ),
                        'type'          => 'text',
                        'description'   => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                        'default'       => __( 'GLS ParcelShop', 'woocommerce' ),
                    ),

                    'cost'       => array(
                        'title'             => __( 'Cost', 'woocommerce' ),
                        'type'              => 'text',
                        'placeholder'       => '',
                        'description'       => '',
                        'default'           => '0',
                        'desc_tip'          => true,
                    ),

                    'tax_status' => array(
                        'title'   => __( 'Tax status', 'woocommerce' ),
                        'type'    => 'select',
                        'class'   => 'wc-enhanced-select',
                        'default' => 'taxable',
                        'options' => array(
                            'taxable' => __( 'Taxable', 'woocommerce' ),
                            'none'    => _x( 'None', 'Tax status', 'woocommerce' ),
                        ),
                    ),
                );
            }

            public function calculate_shipping( $package = array() ) {

                $rates = array(
                    'id'      => $this->id,
                    'label'   => $this->title,
                    'cost'    =>  $this->get_option( 'cost' ),

                );


                $has_costs = false;
                $cost      = $this->get_option( 'cost' );

                $this->add_rate( $rates );
            }

        }

    }


}


?>