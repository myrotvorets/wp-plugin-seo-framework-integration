<?php

namespace Myrotvorets\WordPress\SeoFrameworkIntegration;

use WildWolf\Utils\Singleton;

final class Schema {
	use Singleton;

	private function __construct() {
		$this->init();
	}

	public function init(): void {
		add_filter( 'the_seo_framework_schema_graph_data', [ $this, 'the_seo_framework_schema_graph_data' ], 100 );
	}

	public function the_seo_framework_schema_graph_data( array $graph ): array {
		/** @var array $data */
		foreach ( $graph as &$data ) {
			/** @var string */
			$type = $data['@type'] ?? '';

			if ( 'WebSite' === $type ) {
				$this->update_website_data( $data );
			} elseif ( 'Organization' === $type ) {
				$this->update_organization_data( $data );
			}
		}

		unset( $data );
		return $graph;
	}

	private function update_website_data( array &$data ): void {
		if ( isset( $data['potentialAction'] ) && is_array( $data['potentialAction'] ) ) {
			$data['potentialAction'] = [ $data['potentialAction'] ];
		} else {
			$data['potentialAction'] = [];
		}

		$data['potentialAction'][] = [
			'@type'       => 'SearchAction',
			'target'      => site_url( '/criminal/?cf[name]={search_term_string}' ),
			'query-input' => 'required name=search_term_string',
		];
	}

	private function update_organization_data( array &$data ): void {
		$data['email']                = (string) get_option( 'admin_email' );
		$data['description']          = get_bloginfo( 'description' );
		$data['knowsLanguage']        = [ 'uk', 'en', 'ru', 'pl' ];
		$data['sameAs']               = [
			'https://www.facebook.com/MyrotvoretsUA/',
			'https://uk.wikipedia.org/wiki/%D0%A6%D0%B5%D0%BD%D1%82%D1%80_%C2%AB%D0%9C%D0%B8%D1%80%D0%BE%D1%82%D0%B2%D0%BE%D1%80%D0%B5%D1%86%D1%8C%C2%BB',
		];
		$data['logo']                 = [
			'@type'  => 'ImageObject',
			'url'    => 'https://cdn.myrotvorets.center/m/logos/myrotvorets.png',
			'width'  => 109,
			'height' => 109,
		];
		$data['correctionsPolicy']    = [
			'https://report.myrotvorets.center/',
			'https://myrotvorets.center/skarga/',
		];
		$data['ethicsPolicy']         = 'https://myrotvorets.center/about/pidstavi-diyalnosti-centru/';
		$data['publishingPrinciples'] = 'https://myrotvorets.center/about/zlochini/';
	}
}
