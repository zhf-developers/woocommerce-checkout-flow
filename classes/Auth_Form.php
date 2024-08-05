<?php

namespace FlowDigital\WC_Checkout_Flow;

use FlowDigital\WC_Checkout_Flow\Common\Hooker_Trait;
use FlowDigital\WC_Checkout_Flow\functions as h;

class Auth_Form {
	use Hooker_Trait;

	public function add_hooks() {
		$this->add_action( 'wp', 'validate_page', 0 );
		$this->add_action( 'woocommerce_before_checkout_form', 'remove_hooks', 0 );
		$this->add_action( 'woocommerce_before_checkout_form', 'show_auth_form', 10 );
		$this->add_filter( 'woocommerce_checkout_fields', 'remove_checkout_fields', \PHP_INT_MAX );
		$this->add_filter( 'body_class', 'add_body_class' );
	}

	public function validate_page() {
		if ( ! \is_checkout() ) {
			return;
		}
		if ( \is_user_logged_in() ) {
			return;
		}
		if ( isset( $_GET[ h\prefix( 'hash' ) ] ) ) {
			return;
		}

		define( 'WC_CHECKOUT_FLOW_AUTH', true );
	}

	public function remove_hooks() {
		\remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
		\remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
		\remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
		\remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
		\remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20 );
		\remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_terms_and_conditions_page_content', 30 );
	}

	public function show_auth_form() {
		if ( ! defined( 'WC_CHECKOUT_FLOW_AUTH' ) ) {
			return;
		}

		$this->print_css();
		$this->print_form();
	}

	public function add_body_class( $classes ) {
		if ( defined( 'WC_CHECKOUT_FLOW_AUTH' ) ) {
			$classes[] = h\config_get( 'SLUG' );
		}

		return $classes;
	}

	public function remove_checkout_fields( $fields ) {
		if ( defined( 'WC_CHECKOUT_FLOW_AUTH' ) ) {
			return [];
		}

		return $fields;
	}

	public function print_css() {
		?>
        <style>
            .woocommerce-checkout form.checkout {
                display: none !important;
            }
        </style>
		<?php
	}

	public function print_form() {
		wc_get_template(
			'pre-checkout/auth-form.php',
			[
				// data
			],
			'',
			h\config_get( 'WC_TEMPLATE_PATH' )
		);
	}
}
