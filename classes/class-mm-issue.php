<?php

/**
 * MM_Issue class
 */
class MM_Issue {

	/**
	 * The HTML heading that appears over injected Issue content
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $issue_content_heading = '<h2>In This Issue</h2>';

	/**
	 * The text of the link to the PDF version of an Issue
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $issue_download_link_text = 'Download this issue as a PDF';

	public function __construct() {
		add_action( 'slt_cf_init', array( $this, 'register_slt_box' ) );
		add_filter( 'the_content', array( $this, 'inject_issue_content' ), 20 );
		add_filter( 'the_content', array( $this, 'inject_download_link' ), 40 );
	}

	/**
	 * Get plugin-related postmeta about this Issue
	 *
	 * Applies the meta prefix in use automatically. Only works on the post
	 * currently being viewed
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The name, without a prefix, of the postmeta field to check for
	 * @return mixed|false The meta value, or false if no postmeta was found
	 */
	private function get_post_meta( $name ) {
		global $post;

		$mm = MM::get_instance();
		$prefix = $mm->get_meta_prefix();

		$key = $prefix . $name;

		return get_post_meta( $post->ID, $key, true );
	}

	/**
	 * Gets the value of issue_content_heading
	 *
	 * @uses {apply_filters} on the HTML heading string.
	 * Also passes the post ID of the Issue
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_issue_content_heading() {
		global $post;
		return $this->issue_content_heading;
	}

	/**
	 * Getter for issue_download_link_text
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_issue_download_link_text() {
	    return $this->issue_download_link_text;
	}
	
	/**
	 * Display a Developers Custom Fields metabox on edit-Issue screens
	 *
	 * @since 1.0.0
	 */
	public function register_slt_box() {
		$mm = MM::get_instance();

		if ( function_exists( 'slt_cf_setting' ) )
			slt_cf_setting( 'prefix', $mm->get_meta_prefix() );

		if ( function_exists( 'slt_cf_register_box' ) ) {
			$args = array(
				'type' => 'post',
				'id' => 'issue',
				'title' => 'Issue PDF',
				'context' => 'advanced',
				'priority' => 'high',
				'fields' => array(
					array(
						'name' => $mm->get_issue_file_postmeta_key(),
						'label' => '',
						'type' => 'file',
						'scope' => array( $mm->get_issue_post_type_slug() ),
						'required' => true
					),
				)
			);
			$args = apply_filters( 'mm_register_slt_issue_box_args', $args );
			slt_cf_register_box( $args );
		}
	}

	/**
	 * Add Posts 2 Posts connections to the frontend display of Issue content
	 *
	 * @uses {apply_filters} on the HTML heading string
	 *
	 * @since 1.0.0
	 *
	 * @param string $content The original content of the post
	 * @return string The original content plus any output from the heading or the Posts 2 Posts shortcode
	 */
	public function inject_issue_content( $content ) {
		if ( ! shortcode_exists( 'p2p_connected' ) )
			return $content;

		global $post;

		$mm = MM::get_instance();

		if ( $mm->get_issue_post_type_slug() != $post->post_type )
			return $content;

		$connection_type = $mm->get_issue_content_connection_name();

		$issue_content = $this->get_issue_content_heading();
		$issue_content .= do_shortcode( '[p2p_connected type=' . $connection_type . ']' );

		if ( '' != $issue_content )
			$content .= $issue_content;

		return $content;
	}

	/**
	 * Add a link to a PDF of an Issue on the frontend display of the content
	 *
	 * @uses {apply_filters} on the HTML of the link, also passing the URL to the file
	 *
	 * @since 1.0.0
	 *
	 * @param string $content The original content of the post
	 * @return string The original content plus the link to the PDF, if one exists
	 */
	public function inject_download_link( $content ) {
		$mm = MM::get_instance();

		$file_id = $this->get_post_meta( $mm->get_issue_file_postmeta_key() );
		if ( ! $file_id )
			return $content;

		$file_url = wp_get_attachment_url( $file_id );
		if ( ! $file_url )
			return $content;

		$text = sprintf(
			'<a href="%1$s">%2$s</a>',
			$file_url,
			$this->get_issue_download_link_text()
		);
		$text = apply_filters( 'mm_issue_download_link_text', $text, $file_url );

		if ( '' != $text )
			$content .= wpautop( $text );

		return $content;
	}
}

$MM_issue = new MM_Issue;
