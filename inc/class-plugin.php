<?php

namespace Myrotvorets\WordPress\SeoFrameworkIntegration;

use WildWolf\Utils\Singleton;

final class Plugin {
	use Singleton;

	private function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	public function init(): void {
		add_filter( 'the_seo_framework_indicator', '__return_false' );

		if ( is_admin() ) {
			add_action( 'admin_init', [ Metaboxen::class, 'instance' ] );
		} else {
			Metadata::instance();
			Schema::instance();
			OpenGraph::instance();
		}
	}
}
