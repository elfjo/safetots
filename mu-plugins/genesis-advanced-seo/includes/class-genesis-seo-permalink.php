<?php
/**
 * Modify the permalinks for SEO.
 *
 * @since 0.9.0
 *
 * @link https://wordpress.org/plugins/remove-category-url/
 * @link https://wordpress.org/plugins/auto-clean-url-seo/
 */
class Genesis_SEO_Permalink {

	public function __construct() {

		add_filter( 'name_save_pre', array( $this, 'filter_slug_stop_words' ), 0 );
		add_action( 'wp_ajax_sample-permalink', array( $this, 'ajax_permalink' ), 0 );

		add_action( 'init', array( $this, 'remove_category_base' ) );

		add_action( 'created_category', array( $this, 'flush_rules' ) );
		add_action( 'delete_category',  array( $this, 'flush_rules' ) );
		add_action( 'edited_category',  array( $this, 'flush_rules' ) );

		add_filter( 'category_rewrite_rules', array( $this, 'category_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'category_redirect_var' ) );
		add_filter( 'request', array( $this, 'category_redirect' ) );

	}

	/**
	 * Remove stop words from slug before saving.
	 */
	public function filter_slug_stop_words( $slug ) {

		// Don't change existing slugs
		if ( $slug ) {
			return $slug;
		}

		// Don't change slug if post title is empty
		if ( empty( $_POST['post_title'] ) ) {
			return $slug;
		}

		if ( 'draft' == $_POST['post_status'] ) {
			return $slug;
		}

		$slug = sanitize_title( stripslashes( $_POST['post_title'] ) );

		return $this->remove_stop_words( $slug );

	}

	/**
	 * Remove stop words from slug when AJAX saving.
	 */
	public function ajax_permalink( $data ) {

		// Don't change slug if one already exists
		if ( ! empty( $_POST['new_slug'] ) ) {
			return;
		}

		// Don't generate new slug if title is empty
		if ( empty( $_POST['new_title'] ) ) {
			return;
		}

		$slug = sanitize_title( stripslashes( $_POST['new_title'] ) );

		$_POST['new_slug'] = $this->remove_stop_words( $slug );

	}

	/**
	 * Helper method for removing stop words.
	 */
	public function remove_stop_words( $slug ) {

		$new_slug = array_diff( explode( '-', $slug ), $this->get_stop_words() );

		// Require minimum slug length
		if ( count( $new_slug ) < 3 ) {
			return $slug;
		}

		return join( '-', $new_slug );

	}

	/**
	 * Return array of stop words.
	 */
	public function get_stop_words() {

		return explode( ',', __( "a,about,above,after,again,against,all,am,an,and,any,are,as,at,be,because,been,before,being,below,between,both,but,by,could,did,do,does,doing,down,during,each,few,for,from,further,had,has,have,having,he,he'd,he'll,he's,her,here,here's,hers,herself,him,himself,his,how,how's,i,i'd,i'll,i'm,i've,if,in,into,is,it,it's,its,itself,let's,me,more,most,my,myself,nor,of,on,once,only,or,other,ought,our,ours,ourselves,out,over,own,same,she,she'd,she'll,she's,should,so,some,such,than,that,that's,the,their,theirs,them,themselves,then,there,there's,these,they,they'd,they'll,they're,they've,this,those,through,to,too,under,until,up,very,was,we,we'd,we'll,we're,we've,were,what,what's,when,when's,where,where's,which,while,who,who's,whom,why,why's,with,would,you,you'd,you'll,you're,you've,your,yours,yourself,yourselves", 'genesis-advanced-seo' ) );

	}

	/**
	 * Remove category base from permastructure.
	 */
	public function remove_category_base() {

		global $wp_rewrite;

		$wp_rewrite->extra_permastructs['category']['struct'] = '%category%';

	}

	/**
	 * Flush rewrite rules.
	 */
	function flush_rules() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	/**
	 * Rewrite rules for category base removal.
	 */
	public function category_rewrite_rules( $category_rewrite ) {

		global $wp_rewrite;

		$category_rewrite = array();

		/* WPML is present: temporary disable terms_clauses filter to get all categories for rewrite */
		if ( class_exists( 'Sitepress' ) ) {
			global $sitepress;

			remove_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
			$categories = get_categories( array( 'hide_empty' => false, '_icl_show_all_langs' => true ) );
			add_filter( 'terms_clauses', array( $sitepress, 'terms_clauses' ) );
		} else {
			$categories = get_categories( array( 'hide_empty' => false ) );
		}

		foreach ( $categories as $category ) {
			$category_nicename = $category->slug;
			if (  $category->parent == $category->cat_ID ) {
				$category->parent = 0;
			} elseif ( 0 != $category->parent ) {
				$category_nicename = get_category_parents(  $category->parent, false, '/', true  ) . $category_nicename;
			}
			$category_rewrite[ '(' . $category_nicename . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$' ] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
			$category_rewrite[ '(' . $category_nicename . ')/page/?([0-9]{1,})/?$' ] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
			$category_rewrite[ '(' . $category_nicename . ')/?$' ] = 'index.php?category_name=$matches[1]';
		}

		// Redirect support from Old Category Base
		$old_category_base = get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category';
		$old_category_base = trim( $old_category_base, '/' );
		$category_rewrite[ $old_category_base . '/(.*)$' ] = 'index.php?category_redirect=$matches[1]';

		return $category_rewrite;

	}

	/**
	 * Add category_redirect query var.
	 */
	public function category_redirect_var( $public_query_vars ) {
		$public_query_vars[] = 'category_redirect';
		return $public_query_vars;
	}

	/**
	 * Redirect category URL, if necessary.
	 */
	public function category_redirect( $query_vars ) {

		if ( isset( $query_vars['category_redirect'] ) ) {
			$catlink = trailingslashit( get_option( 'home' ) ) . user_trailingslashit( $query_vars['category_redirect'], 'category' );
			status_header( 301 );
			header( "Location: $catlink" );
			exit;
		}

		return $query_vars;

	}

}
