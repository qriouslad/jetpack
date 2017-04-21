<?php

class Jetpack_JITM_Config {
	private static $config = array(
		'version' => JETPACK__VERSION
	);

	private $rule;

	static function verify_array( &$array, $key ) {
		if ( ! isset( $array[ $key ] ) ) {
			$array[ $key ] = array();
		}
	}

	function __construct( $name ) {
		$this->rule            = array();
		self::$config[ $name ] = &$this->rule;
	}

	function user_in_role( $role ) {
		$this->rule['min_user_level'] = $role;

		return $this;
	}

	function user_can_activate_modules() {
		$this->rule['module_activator'] = true;

		return $this;
	}

	function user_can( $capability ) {
		self::verify_array( $this->rule, 'caps' );

		$this->rule['caps'][] = $capability;

		return $this;
	}

	function plugin_active( $plugin ) {
		self::verify_array( $this->rule, 'active_plugins' );

		$this->rule['active_plugins'][] = $plugin;

		return $this;
	}

	function plugin_inactive( $plugin ) {
		self::verify_array( $this->rule, 'inactive_plugins' );

		$this->rule['inactive_plugins'][] = $plugin;

		return $this;
	}

	function show( $message ) {
		self::verify_array( $this->rule, 'message' );

		$this->rule['message']['content'] = $message;

		return $this;
	}

	function in_admin( $screen ) {
		self::verify_array( $this->rule, 'message' );
		self::verify_array( $this->rule['message'], 'screens' );

		$this->rule['message']['screens'][] = $screen;

		return $this;
	}

	function with_CTA( $message, $hook ) {
		self::verify_array( $this->rule, 'message' );

		$this->rule['message']['CTA'] = array(
			'message' => $message,
			'hook'    => $hook
		);

		return $this;
	}

	function with_emblem( $svg = false ) {
		self::verify_array( $this->rule, 'message' );

		$this->rule['message']['emblem'] = $svg || true;

		return $this;
	}

	function track( $group, $detail ) {
		$this->rule['tracker'] = array(
			$group,
			$detail
		);

		return $this;
	}

	function has_query_parameter( $key, $expected_value = null ) {
		self::verify_array( $this->rule, 'has_query' );

		$this->rule['has_query'][] = array(
			'key'   => $key,
			'value' => $expected_value
		);

		return $this;
	}

	static function default_config() {
		$rule = new Jetpack_JITM_Config( 'akismet_msg' );
		$rule->plugin_inactive( 'akismet/akismet.php' )
		     ->user_can_activate_modules()
		     ->show( esc_html__( "Spam affects your site's legitimacy, protect your site with Akismet.", 'jetpack' ) )
		     ->in_admin( 'edit-comments' )
		     ->with_CTA( esc_html__( 'Automate Spam Blocking', 'jetpack' ), 'jetpack_akismet_redirect' )
		     ->with_emblem()
		     ->track( 'jitm', 'akismet-viewed-' . JETPACK__VERSION );

		$rule = new Jetpack_JITM_Config( 'backups_after_publish' );
		$rule->plugin_inactive( 'vaultpress/vaultpress.php' )
		     ->user_can_activate_modules()
		     ->has_query_parameter( 'message', 6 )
		     ->show( esc_html__( "Great job! Now let's make sure your hard work is never lost, backup everything with VaultPress.", 'jetpack' ) )
		     ->in_admin( 'post' )
		     ->with_emblem()
		     ->with_CTA( esc_html__( 'Enable Backups', 'jetpack' ), 'jetpack_vaultpress_redirect' )
		     ->track( 'jitm', 'vaultpress-publish-viewed-' . JETPACK__VERSION );

		$rule = new Jetpack_JITM_Config( 'update-core-msg' );
		$rule->plugin_inactive( 'vaultpress/vaultpress.php' )
		     ->user_can_activate_modules()
		     ->show( esc_html__( 'Backups are recommended to protect your site before you make any changes.', 'jetpack' ) )
		     ->in_admin( 'update-core' )
		     ->with_emblem()
		     ->with_CTA( 'Enable VaultPress Backups', 'jetpack_vaultpress_update_redirect' )
		     ->track( 'jitm', 'vaultpress-updates-viewed-' . JETPACK__VERSION );

		$rule = new Jetpack_JITM_Config( 'woo-services' );
		$rule->plugin_inactive( 'woocommerce-services/woocommerce-services.php' )
		     ->user_can( 'manage_woocommerce' )
		     ->user_can( 'install_plugins' )
		     ->show( array( 'hook' => 'jetpack_woo_message' ) )
		     ->in_admin( 'woocommerce_page_wc-settings' )
		     ->in_admin( 'edit-shop_order' )
		     ->in_admin( 'shop_order' )
		     ->with_emblem( "<svg xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" version=\"1.1\" id=\"Layer_1\" x=\"0\" y=\"0\" viewBox=\"0 0 24 24\" enable-background=\"new 0 0 24 24\" xml:space=\"preserve\">
					<path d=\"M18,8h-2V7c0-1.105-0.895-2-2-2H4C2.895,5,2,5.895,2,7v10h2c0,1.657,1.343,3,3,3s3-1.343,3-3h4c0,1.657,1.343,3,3,3s3-1.343,3-3h2v-5L18,8z M7,18.5c-0.828,0-1.5-0.672-1.5-1.5s0.672-1.5,1.5-1.5s1.5,0.672,1.5,1.5S7.828,18.5,7,18.5z M4,14V7h10v7H4z M17,18.5c-0.828,0-1.5-0.672-1.5-1.5s0.672-1.5,1.5-1.5s1.5,0.672,1.5,1.5S17.828,18.5,17,18.5z\" />
				</svg>" )
		     ->with_CTA( 'Activate WooCommerce Services', 'jetpack_woo_services_redirect' )
		     ->track( 'jitm', 'wooservices-viewed-' . JETPACK__VERSION );
	}
}
