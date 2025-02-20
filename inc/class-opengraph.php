<?php

namespace Myrotvorets\WordPress\SeoFrameworkIntegration;

use WildWolf\Utils\Singleton;
use WP_Post;

final class OpenGraph {
	use Singleton;

	private function __construct() {
		$this->init();
	}

	public function init(): void {
		add_filter( 'the_seo_framework_description_excerpt', [ $this, 'the_seo_framework_description_excerpt' ], 10, 3 );
		add_filter( 'the_seo_framework_custom_image_details', [ $this, 'the_seo_framework_custom_image_details' ] );
	}

	/**
	 * @param string $excerpt
	 */
	public function the_seo_framework_description_excerpt( $excerpt ): string {
		if ( is_singular( 'criminal' ) ) {
			if ( empty( $excerpt ) ) {
				/** @var WP_Post|null */
				$post = get_post();
				if ( $post ) {
					$excerpt = wp_strip_all_tags( $post->post_content );
				}
			}

			$pos = strpos( $excerpt, 'Описание:' );
			if ( false !== $pos ) {
				$excerpt = trim( substr( $excerpt, $pos + strlen( 'Описание:' ) ) );
			}

			$excerpt = preg_replace( '/\s+/', ' ', $excerpt );
		}

		return (string) $excerpt;
	}

	/**
	 * @psalm-param array<array{url: string, id: int, width: int, height: int, alt: string, caption: string, filesize: int}> $details
	 * @psalm-return array<array{url: string, id: int, width: int, height: int, alt: string, caption: string, filesize: int}>
	 */
	public function the_seo_framework_custom_image_details( array $details ): array {
		if ( is_singular( 'criminal' ) ) {
			$images = get_attached_media( 'image' );

			if ( $images ) {
				$ids = array_map( fn ( WP_Post $image ): int => $image->ID, $images );

				update_postmeta_cache( $ids );

				$details = array_map(
					/**
					 * @param int $id
					 * @psalm-return array{url: string, id: int, width: int, height: int, alt: string, caption: string, filesize: int}|false
					 */
					function ( $id ) {
						/** @psalm-var array{string,int,int,bool}|false */
						$attrs = wp_get_attachment_image_src( $id, 'full' );
						return is_array( $attrs ) ? [
							'url'      => esc_url( $attrs[0] ),
							'id'       => $id,
							'width'    => $attrs[1],
							'height'   => $attrs[2],
							'alt'      => '',
							'caption'  => '',
							'filesize' => 0,
						] : false;
					}, $ids
				);
			}
		}

		return array_filter( $details );
	}
}
