<?php

namespace Myrotvorets\WordPress\SeoFrameworkIntegration;

use WildWolf\Utils\Singleton;

final class Metadata {
	use Singleton;

	private function __construct() {
		$this->init();
	}

	public function init(): void {
		add_filter( 'the_seo_framework_title_from_generation', [ $this, 'the_seo_framework_title_from_generation' ], 10, 2 );
		add_filter( 'the_seo_framework_meta_render_data', [ $this, 'the_seo_framework_meta_render_data' ], PHP_INT_MAX );
	}

	public function the_seo_framework_title_from_generation( $title, ?array $args ): string {
		if ( null === $args && is_singular( 'criminal' ) ) {
			$title = get_the_title();
		} elseif ( null !== $args && ! empty( $args['id'] ) ) {
			$title = get_the_title( $args['id'] );
		}

		return $title;
	}

	public function the_seo_framework_meta_render_data( array $data ): array {
		if ( isset( $data['canonical']['attributes']['href'] ) ) {
			/** @psalm-suppress MixedArrayAssignment, MixedArrayAccess */
			$data['canonical']['attributes']['href'] = preg_replace( '!^https?://[^/]++(/|$)!i', 'https://myrotvorets.center/', (string) $data['canonical']['attributes']['href'] );
		}

		if ( is_singular( 'criminal' ) || is_post_type_archive( 'criminal' ) ) {
			unset( $data['prev'], $data['next'] );
		}

		$data['search'] = [
			'tag'        => 'link',
			'attributes' => [
				'rel'   => 'search',
				'type'  => 'application/opensearchdescription+xml',
				'title' => 'Пошук у Чистилищі «Миротоворця»',
				'href'  => site_url( 'searchcriminals.xml' ),
			],
		];

		if ( is_front_page() ) {
			$data['og:video'] = [
				'attributes' => [
					'property' => 'og:video',
					'content'  => 'https://cdn.myrotvorets.center/m/myrotvorets.mp4',
				],
			];
		}

		unset( $data['twitter:title'], $data['twitter:description'] );

		return $data;
	}
}
