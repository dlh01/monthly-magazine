<?php

/**
 * Monthly Magazine class
 */
class MM {

	/**
	 * Instance of this class
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Textdomain for use with translations
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $textdomain = 'monthly-magazine';

	/**
	 * Prefix to use with any postmeta
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $meta_prefix = 'mm_';

	/**
	 * The slug of the Issues post type
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $post_type_slug = 'mm_issue';

	/**
	 * The label of the post type to use for Issues
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $post_type_label = 'Issue';

	/**
	 * The post fields that the Issue post type will support
	 *
	 * @see {@link add_post_type_support()} for documentation
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $post_type_supports = array( 'title', 'thumbnail', 'revisions' );

	/**
	 * The postmeta key to use when storing an uploaded PDF of an issue
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $issue_file_postmeta_key = 'file';

	/**
	 * Slug for naming the Posts 2 Posts connection for Issue content
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $issue_content_connection_name = 'issue_content';

	/**
	 * Post types that make up an Issue's content
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $issue_content_post_types = array( 'post' );

	/**
	 * The label on the P2P connection metabox on an Issue edit page
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $issue_content_connection_from_title = 'Issue Content';

	/**
	 * The label on the P2P connection metabox on Issue content edit pages
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $issue_content_connection_to_title = 'In Issue';

	public function __construct() {
		add_action( 'init', array( $this, 'mm_issue_init' ) );
		add_filter( 'post_updated_messages', array( $this, 'mm_issue_updated_messages' ) );
		add_action( 'p2p_init', array( $this, 'issue_content_connection' ) );
	}

	/**
	 * Get the value of textdomain
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_textdomain() {
		return $this->textdomain;
	}

	/**
	 * Get the value of meta_prefix
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_meta_prefix() {
		return $this->meta_prefix;
	}

	/**
	 * Return an instance of this class
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Gets the value of issue_dates
	 *
	 * @return array
	 */
	public function get_issue_dates() {
		return apply_filters( 'mm_issue_dates', $this->issue_dates );
	}

	/**
	 * Gets the value of post_type_slug
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_issue_post_type_slug() {
		return $this->post_type_slug;
	}

	/**
	 * Gets the value of post_type_supports
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_post_type_supports() {
		return apply_filters( 'mm_post_type_supports', $this->post_type_supports );
	}

	/**
	 * Gets the value of issue_file_postmeta_key
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_issue_file_postmeta_key() {
		return $this->issue_file_postmeta_key;
	}

	/**
	 * Gets the value of post_type_label
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_post_type_label() {
		return $this->post_type_label;
	}

	/**
	 * Gets the value of issue_content_connection_name
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_issue_content_connection_name() {
		return $this->issue_content_connection_name;
	}

	/**
	 * Gets the value of issue_content_connection_from_title
	 *
	 * @uses apply_filters
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_issue_content_connection_from_title() {
		return apply_filters( 'mm_issue_content_connection_from_title', $this->issue_content_connection_from_title );
	}

	/**
	 * Gets the value of issue_content_connection_to_title
	 *
	 * @uses apply_filters
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_issue_content_connection_to_title() {
		return apply_filters( 'mm_issue_content_connection_to_title', $this->issue_content_connection_to_title );
	}

	/**
	 * Gets the value of issue_content_post_types
	 *
	 * {@uses apply_filters} Allows alternate or additional post types
	 *
	 * @return array
	 */
	public function get_issue_content_post_types() {
		$types = $this->issue_content_post_types;
		$types = apply_filters( 'mm_issue_content_post_types', $types );

		if ( ! is_array( $types ) )
			return $this->issue_content_post_types;

		return $types;
	}

	/**
	 * Initialize the Issue post type
	 *
	 * @since 1.0.0
	 */
	public function mm_issue_init() {
		register_post_type( $this->get_issue_post_type_slug(), array(
			'hierarchical'        => false,
			'public'              => true,
			'show_in_nav_menus'   => true,
			'show_ui'             => true,
			'supports'            => $this->get_post_type_supports(),
			'has_archive'         => true,
			'query_var'           => 'issues',
			'rewrite'             => array( 'slug' => 'issues' ),
			'labels'              => array(
				'name'                => __( 'Issues', 'monthly-magazine' ),
				'singular_name'       => __( 'Issue', 'monthly-magazine' ),
				'add_new'             => __( 'Add new Issue', 'monthly-magazine' ),
				'all_items'           => __( 'Issues', 'monthly-magazine' ),
				'add_new_item'        => __( 'Add new Issue', 'monthly-magazine' ),
				'edit_item'           => __( 'Edit Issue', 'monthly-magazine' ),
				'new_item'            => __( 'New Issue', 'monthly-magazine' ),
				'view_item'           => __( 'View Issue', 'monthly-magazine' ),
				'search_items'        => __( 'Search Issues', 'monthly-magazine' ),
				'not_found'           => __( 'No Issues found', 'monthly-magazine' ),
				'not_found_in_trash'  => __( 'No Issues found in trash', 'monthly-magazine' ),
				'parent_item_colon'   => __( 'Parent Issue', 'monthly-magazine' ),
				'menu_name'           => __( 'Issues', 'monthly-magazine' ),
			),
		) );
	}


	/**
	 * Update the messages that display when an Issue is updated
	 *
	 * @since 1.0.0
	 *
	 * @param array $messages The default messages
	 * @return array The updated messages
	 */
	public function mm_issue_updated_messages( $messages ) {
		global $post;

		$permalink = get_permalink( $post );

		$messages[$this->get_issue_post_type_slug()] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __('Issue updated. <a target="_blank" href="%s">View Issue</a>', 'monthly-magazine'), esc_url( $permalink ) ),
			2 => __('Custom field updated.', 'monthly-magazine'),
			3 => __('Custom field deleted.', 'monthly-magazine'),
			4 => __('Issue updated.', 'monthly-magazine'),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Issue restored to revision from %s', 'monthly-magazine'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Issue published. <a href="%s">View Issue</a>', 'monthly-magazine'), esc_url( $permalink ) ),
			7 => __('Issue saved.', 'monthly-magazine'),
			8 => sprintf( __('Issue submitted. <a target="_blank" href="%s">Preview Issue</a>', 'monthly-magazine'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
			9 => sprintf( __('Issue scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Issue</a>', 'monthly-magazine'),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
			10 => sprintf( __('Issue draft updated. <a target="_blank" href="%s">Preview Issue</a>', 'monthly-magazine'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		);

		return $messages;
	}

	/**
	 * Register a Posts to Posts connection between Issues and other post types
	 *
	 * @since 1.0.0
	 */
	public function issue_content_connection() {
		p2p_register_connection_type( array(
			'name' => $this->get_issue_content_connection_name(),
			'from' => $this->get_issue_post_type_slug(),
			'to' => $this->get_issue_content_post_types(),
			'admin_box' => array(
				'context' => 'advanced'
			),
			'title' => array(
				'from' => __( $this->get_issue_content_connection_from_title(), $this->get_textdomain() ),
				'to' => __( $this->get_issue_content_connection_to_title(), $this->get_textdomain() )
			),
			'cardinality' => 'one-to-many',
			'sortable' => 'from'
		) );
	}

}
