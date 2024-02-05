<?php

namespace Myrotvorets\WordPress\SeoFrameworkIntegration;

use WildWolf\Utils\Singleton;

final class Metaboxen {
	use Singleton;

	private function __construct() {
		$this->admin_init();
	}

	private function admin_init(): void {
		add_action( 'add_meta_boxes_criminal', [ $this, 'add_meta_boxes_criminal' ] );
		add_filter( 'the_seo_framework_metabox_priority', [ $this, 'the_seo_framework_metabox_priority' ] );
	}

	public function add_meta_boxes_criminal(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			remove_meta_box( 'tsf-inpost-box', 'criminal', (string) apply_filters( 'the_seo_framework_metabox_context', 'normal' ) );
		}
	}

	public function the_seo_framework_metabox_priority(): string {
		return 'low';
	}
}
