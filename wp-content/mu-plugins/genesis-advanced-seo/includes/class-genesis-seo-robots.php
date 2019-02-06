<?php
/**
 * Robots.txt file generator.
 *
 * @since 0.9.0
 */
class Genesis_SEO_Robots {

	/**
	 * The robots.txt filename. This really shouldn't change.
	 */
	public $filename = 'robots.txt';

	public function disallow() {

		$disallow = '';

		if ( genesis_get_seo_option( 'noindex_cat_archive' ) ) {

			$category_base  = get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category';
			$disallow .= "Disallow: /{$category_base} \n";

		} else {

			// Pull all top level categories with posts
			$categories = get_terms( array(
				'taxonomy'   => 'category',
				'parent'     => 0,
				'meta_key'   => '_genesis_noindex',
				'meta_value' => 1,
			) );

			if ( $categories ) {
				$category_base  = get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category';

				foreach ( $categories as $category ) {
					$disallow .= "Disallow: /{$category_base}/{$category->slug} \n";
				}
			}

		}

		if ( genesis_get_seo_option( 'noindex_tag_archive' ) ) {

			$tag_base  = get_option( 'tag_base' ) ? get_option( 'tag_base' ) : 'tag';
			$disallow .= "Disallow: /{$tag_base} \n";

		} else {

			// Pull all tags with posts
			$tags = get_terms( array(
				'taxonomy'   => 'post_tag',
				'meta_key'   => '_genesis_noindex',
				'meta_value' => 1,
			) );

			if ( $tags ) {
				$tag_base  = get_option( 'tag_base' ) ? get_option( 'tag_base' ) : 'tag';

				foreach ( $tags as $tag ) {
					$disallow .= "Disallow: /{$tag_base}/{$tag->slug} \n";
				}
			}

		}

		if ( genesis_get_seo_option( 'noindex_author_archive' ) ) {

			$disallow .= "Disallow: /author \n";

		}

		if ( genesis_get_seo_option( 'noindex_search_archive' ) ) {

			$disallow .= "Disallow: /?s=* \n";

		}

		$posts = get_posts( array(
			'post_type'      => array( 'post', 'page', 'product' ),
			'posts_per_page' => 50000,
			'meta_key'       => '_genesis_noindex',
			'meta_value'     => 1,
		) );

		if ( ! $posts ) {
			return $disallow;
		}

		foreach ( $posts as $post ) {

			if ( 'product' == $post->post_type ) {
				$disallow .= "Disallow: /product/{$post->post_name} \n";
			}
			else {
				$disallow .= "Disallow: /{$post->post_name} \n";
			}

		}

		return $disallow;

	}

	public function write_file() {

		if ( ! $this->disallow() ) {
			return;
		}

		$content = "User-Agent: *\n" . $this->disallow();

		insert_with_markers( ABSPATH . $this->filename, "GENESIS SEO", $content );

	}


}
