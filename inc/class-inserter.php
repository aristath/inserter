<?php
/**
 * The main Inserter class.
 *
 * @since 1.0
 * @package Inserter
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The main Inserter class.
 *
 * @since 1.0
 */
class Inserter {

	/**
	 * Library path.
	 *
	 * @since 1.0
	 * @access private
	 * @var string
	 */
	private $path;

	/**
	 * Library url.
	 *
	 * @since 1.0
	 * @access private
	 * @var string
	 */
	private $url;

	/**
	 * The templates we'll be loading.
	 *
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $templates = array();

	/**
	 * The data we'll be loading.
	 *
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $data = array();

	/**
	 * The elements each template will be replacing.
	 *
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $elements = array();

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {

		// Init.
		add_action( 'after_setup_theme', array( $this, 'init' ) );

		// Run queries on wp_head.
		add_action( 'wp', array( $this, 'query' ) );

		// Add templates to the footer.
		add_action( 'wp_footer', array( $this, 'add_templates' ) );

		// Enqueue scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Init.
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function init() {

		// Make sure the path is set.
		if ( ! $this->path ) {
			$this->set_path();
		}

		// Set the URL.
		$this->set_url();
	}

	/**
	 * Query posts and add templates as-needed.
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function query() {
		global $post;
		$args = array(
			'post_type'      => 'inserter_template',
			'posts_per_page' => -1,
		);

		$query = new WP_Query( $args );
		foreach ( $query->posts as $template ) {

			// Check the post-type filter.
			$post_type = get_post_meta( $template->ID, 'inserter_template_post_type', true );
			if ( $post_type && get_post_type() !== $post_type ) {
				continue;
			}

			// Check the post-ID filter.
			$post_id = get_post_meta( $template->ID, 'inserter_template_post_id', true );
			if ( $post_id && get_the_ID() !== $post_id ) {
				continue;
			}

			// Get the selected data-type.
			$data_type = get_post_meta( $template->ID, 'inserter_template_data_type', true );
			if ( 'rest' !== $data_type && 'custom' !== $data_type && 'post' !== $data_type ) {
				$data_type = 'post';
			}

			// Get the data.
			$data = json_decode( get_post_meta( $template->ID, 'inserter_template_data', true ) );
			if ( 'rest' === $data_type ) {
				$data = array(
					'inserterDataType' => 'REST',
					'id'                => $post->ID,
				);
			} elseif ( 'post' === $data_type ) {
				$data = $post;
			}

			// Add the template.
			$this->add_template_string(
				'inserter-template-' . absint( $template->ID ),
				get_post_meta( $template->ID, 'inserter_template_el', true ),
				get_post_meta( $template->ID, 'inserter_template', true ),
				$data
			);
		}
	}

	/**
	 * Add a template from a file.
	 *
	 * @access public
	 * @since 1.0
	 * @param string       $handle  The template handle.
	 * @param string       $element The element our template will be replacing.
	 * @param string       $path    The template file to use.
	 * @param array|object $data    The data that will be passed-on to the underscore.js template.
	 * @return void
	 */
	public function add_template_file( $handle, $element, $path, $data ) {

		// Build the template.
		$template = '<script type="text/html" id="tmpl-' . $handle . '">';
		if ( file_exists( $path ) ) {
			ob_start();
			include $path;
			$template .= ob_get_clean();
		}
		$template .= '</script>';

		// Add the template.
		$this->templates[ $handle ] = $template;

		// Add the data.
		$this->data[ $handle ] = $data;

		// Add the elements.
		$this->elements[ $handle ] = $element;
	}

	/**
	 * Add a template from a string.
	 *
	 * @access public
	 * @since 1.0
	 * @param string       $handle   The template handle.
	 * @param string       $element  The element our template will be replacing.
	 * @param string       $template The template contents.
	 * @param array|object $data     The data that will be passed-on to the underscore.js template.
	 * @return void
	 */
	public function add_template_string( $handle, $element, $template, $data ) {

		// Add the template.
		$this->templates[ $handle ] = '<script type="text/html" id="tmpl-' . $handle . '">' . $template . '</script>';

		// Add the data.
		$this->data[ $handle ] = $data;

		// Add the elements.
		$this->elements[ $handle ] = $element;
	}

	/**
	 * Adds the templates to the footer.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function add_templates() {
		foreach ( $this->templates as $template ) {
			echo $template; // WPCS: XSS ok.
		}
	}

	/**
	 * Sets the library path.
	 *
	 * @since 1.0
	 * @access public
	 * @param string $path The path to set. If empty uses the parent dir's path.
	 * @return void
	 */
	public function set_path( $path = '' ) {

		// If path is not set, use the parent dir.
		if ( '' === $path ) {
			$this->path = dirname( dirname( __FILE__ ) );
			$this->path = trailingslashit( $this->path );
			return;
		}
		$this->path = trailingslashit( wp_normalize_path( $path ) );
	}

	/**
	 * Properly set the URL.
	 *
	 * @access protected
	 * @since 1.0
	 * @return void
	 */
	protected function set_url() {

		// Get correct URL and path to wp-content.
		$content_url = untrailingslashit( dirname( dirname( get_stylesheet_directory_uri() ) ) );
		$content_dir = wp_normalize_path( untrailingslashit( WP_CONTENT_DIR ) );

		$this->url = str_replace( $content_dir, $content_url, wp_normalize_path( $this->path ) );

		// Make sure the right protocol is used.
		$this->url = trailingslashit( set_url_scheme( $this->url ) );
	}

	/**
	 * Get $this->url.
	 *
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function get_url() {
		if ( ! $this->url ) {
			$this->set_url();
		}
		return $this->url;
	}

	/**
	 * Enqueues scripts.
	 *
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'inserter-app', $this->url . 'js/app.js', array( 'wp-util', 'wp-api', 'backbone', 'underscore' ), false, true );
		wp_localize_script( 'inserter-app', 'inserterAppData', $this->data );
		wp_localize_script( 'inserter-app', 'inserterAppEl', $this->elements );
	}
}
