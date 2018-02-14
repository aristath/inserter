<?php
/**
 * The main plugin class.
 *
 * @since 1.0
 * @package Inserter
 */

/**
 * The main plugin class.
 *
 * @since 1.0.0
 */
class Inserter_Admin {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		// Register the custom post-type.
		add_action( 'init', array( $this, 'register_post_type' ) );

		// Add the metabox.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		// Enqueue admin scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Save our custom post-meta.
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	/**
	 * Register the custom post-type.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function register_post_type() {
		register_post_type( 'inserter_template',
			array(
				'public'              => true,
				'has_archive'         => true,
				'exclude_from_search' => true,
				'show_in_admin_bar'   => false,
				'menu_icon'           => 'dashicons-media-code',
				'hierarchical'        => false,
				'supports'            => array( 'title', 'revisions' ),
				'labels'              => array(
					'name'          => __( 'Templates', 'inserter' ),
					'singular_name' => __( 'Template', 'inserter' ),
				),
			)
		);
	}

	/**
	 * Add metabox to our custom post-type.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function add_meta_box() {
		add_meta_box(
			'inserter_template',
			esc_attr__( 'Template', 'inserter' ),
			array( $this, 'metabox_template' ),
			'inserter_template',
			'normal',
			'high'
		);
		add_meta_box(
			'inserter_template_data',
			esc_attr__( 'Template Data', 'inserter' ),
			array( $this, 'metabox_template_data' ),
			'inserter_template',
			'normal',
			'default'
		);
		add_meta_box(
			'inserter_template_el',
			esc_attr__( 'Element', 'inserter' ),
			array( $this, 'metabox_el' ),
			'inserter_template',
			'side',
			'default'
		);
		add_meta_box(
			'inserter_template_conditions',
			esc_attr__( 'Conditions', 'inserter' ),
			array( $this, 'metabox_conditions' ),
			'inserter_template',
			'side',
			'default'
		);
	}

	/**
	 * Callback that adds the form inside our metabox.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $post The post object.
	 * @return void
	 */
	public function metabox_template( $post ) {
		?>
		<p><?php esc_attr_e( 'Enter the contents of your template.', 'inserter' ); ?></p>
		<textarea cols="70" rows="30" name="inserter-template" id="inserter-template" aria-describedby="editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4"><?php echo get_post_meta( $post->ID, 'inserter_template', true ); // WPCS: XSS ok. ?></textarea>
		<?php
	}

	/**
	 * Callback that adds the form inside our metabox.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $post The post object.
	 * @return void
	 */
	public function metabox_template_data( $post ) {
		$data_type = get_post_meta( $post->ID, 'inserter_template_data_type', true );
		if ( 'rest' !== $data_type && 'custom' !== $data_type && 'post' !== $data_type ) {
			$data_type = 'post';
		}

		?>
		<p><?php esc_attr_e( 'Select the data-type for this template.', 'inserter' ); ?></p>
		<div id="inserter-data-type-wrapper">
			<label>
				<input type="radio" name="inserter-data-type" value="rest"<?php echo ( 'rest' === $data_type ) ? ' checked' : ''; ?>>
				<?php esc_attr_e( 'REST API', 'inserter' ); ?>
			</label>
			<br>
			<label>
				<input type="radio" name="inserter-data-type" value="post"<?php echo ( 'post' === $data_type ) ? ' checked' : ''; ?>>
				<?php _e( 'global <code>$post</code> object', 'inserter' ); ?>
			</label>
			<br>
			<label>
				<input type="radio" name="inserter-data-type" value="custom"<?php echo ( 'custom' === $data_type ) ? ' checked' : ''; ?>>
				<?php esc_attr_e( 'Custom Data', 'inserter' ); ?></input>
			</label>
		</div>
		<div id="inserter-custom-template-data-wrapper">
			<hr>
			<p><?php esc_attr_e( 'If you selected "custom" above, please enter a JSON-formatted object that you want to pass-on to your template.', 'inserter' ); ?></p>
			<textarea cols="70" rows="30" name="inserter-template-data" id="inserter-template-data" aria-describedby="editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4"><?php echo get_post_meta( $post->ID, 'inserter_template_data', true ); // WPCS: XSS ok. ?></textarea>
		</div>
		<?php
	}

	/**
	 * Callback that adds the form inside our metabox.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $post The post object.
	 * @return void
	 */
	public function metabox_el( $post ) {
		$mode = get_post_meta( $post->ID, 'inserter_template_element_mode', true );
		if ( 'replace' !== $mode && 'append' !== $mode && 'prepend' !== $mode ) {
			$mode = 'replace';
		}
		?>
		<p><?php esc_attr_e( 'Please enter the CSS selector of the element where this template will be rendered.', 'inserter' ); ?></p>
		<input name="inserter-template-el" id="inserter-template-el" value="<?php echo get_post_meta( $post->ID, 'inserter_template_el', true ); // WPCS: XSS ok. ?>">
		<p><?php esc_attr_e( 'Select the template behaviour in regards to the element.', 'inserter' ); ?>
		<p>
			<label>
				<input type="radio" name="inserter-element-mode" value="replace"<?php echo ( 'replace' === $mode ) ? ' checked' : ''; ?>>
				<?php esc_attr_e( 'Replace Contents', 'inserter' ); ?>
			</label>
			<br>
			<label>
				<input type="radio" name="inserter-element-mode" value="prepend"<?php echo ( 'prepend' === $mode ) ? ' checked' : ''; ?>>
				<?php esc_attr_e( 'Prepend to contents', 'inserter' ); ?>
			</label>
			<br>
			<label>
				<input type="radio" name="inserter-element-mode" value="append"<?php echo ( 'append' === $mode ) ? ' checked' : ''; ?>>
				<?php esc_attr_e( 'Append to contents', 'inserter' ); ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Callback that adds the form inside our metabox.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param object $post The post object.
	 * @return void
	 */
	public function metabox_conditions( $post ) {

		// Get an array of the public post-types.
		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);

		$selected_post_type = get_post_meta( $post->ID, 'inserter_template_post_type', true );
		?>
		<p><?php esc_attr_e( 'If you want this template to only be used in a specific post-type, select it below. Select "none" to not filter by post-type.', 'inserter' ); ?></p>
		<p>
			<select id="template-post-type" name="template-post-type">
				<option value=""<?php echo ( ! $selected_post_type ) ? ' selected' : ''; ?>><?php esc_attr_e( 'None', 'inserter' ); ?></option>
				<?php foreach ( $post_types as $post_type ) : ?>
					<?php if ( 'inserter_template' === $post_type->name ) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<option value="<?php echo esc_attr( $post_type->name ); ?>"<?php echo ( $post_type->name === $selected_post_type ) ? ' selected' : ''; ?>>
						<?php echo esc_html( $post_type->labels->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<hr>
		<p><?php esc_attr_e( 'Enter a post-ID if this template should only be aplied to a specific post, or set to 0 (zero).', 'inserter' ); ?></p>
		<p><input name="inserter-template-post-id" id="inserter-template-post-id" value="<?php echo absint( get_post_meta( $post->ID, 'inserter_template_post_id', true ) ); // WPCS: XSS ok. ?>"></p>
		<?php
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_enqueue_scripts() {

		// Check the current screen and only add to our custom post-type.
		$current_screen = get_current_screen();
		if ( 'post' !== $current_screen->base && 'inserter_template' !== $current_screen->post_type ) {
			return;
		}

		// Enqueue code editor and settings for manipulating HTML.
		$settings = wp_enqueue_code_editor( array( 'type' => 'php' ) );

		// Bail if user disabled CodeMirror.
		if ( false === $settings ) {
			return;
		}
		wp_add_inline_script( 'code-editor', sprintf( 'jQuery( function() { wp.codeEditor.initialize( "inserter-template", %s ); } );', wp_json_encode( $settings ) ) );

		// Enqueue code editor and settings for manipulating HTML.
		$settings = wp_enqueue_code_editor( array( 'type' => 'javascript' ) );

		// Bail if user disabled CodeMirror.
		if ( false === $settings ) {
			return;
		}
		wp_add_inline_script( 'code-editor', sprintf( 'jQuery( function() { wp.codeEditor.initialize( "inserter-template-data", %s ); } );', wp_json_encode( $settings ) ) );
		wp_enqueue_script( 'inserter-admin-app', inserter()->get_url() . 'js/admin-app.js', array( 'jquery' ), false, true );
		wp_localize_script( 'inserter-admin-app', 'inserterL10n', array(
			'requireElement' => esc_attr__( 'The "element" field is required.', 'inserter' ),
		) );
		wp_enqueue_style( 'inserter-admin-styles', inserter()->get_url() . 'css/admin.css' );
	}

	/**
	 * Save our custom post-meta.
	 *
	 * @access public
	 * @since 1.0.0
	 * @param int $post_id The post-ID.
	 * @return void
	 */
	public function save_post( $post_id ) {

		// Save the template.
		if ( isset( $_POST['inserter-template'] ) ) {
			update_post_meta( $post_id, 'inserter_template', wp_unslash( $_POST['inserter-template'] ) );
		}

		// Save the custom data.
		if ( isset( $_POST['inserter-template-data'] ) ) {
			update_post_meta( $post_id, 'inserter_template_data', sanitize_text_field( wp_unslash( $_POST['inserter-template-data'] ) ) );
		}

		// Save the CSS-Selector.
		if ( isset( $_POST['inserter-template-el'] ) ) {
			$css_selector = sanitize_text_field( wp_unslash( $_POST['inserter-template-el'] ) );
			update_post_meta( $post_id, 'inserter_template_el', $css_selector );
		}

		// Save the post-type.
		if ( isset( $_POST['template-post-type'] ) ) {
			$post_type = sanitize_text_field( wp_unslash( $_POST['template-post-type'] ) );
			update_post_meta( $post_id, 'inserter_template_post_type', $post_type );
		}

		// Save the data-type.
		if ( isset( $_POST['inserter-data-type'] ) ) {
			$data_type = sanitize_text_field( wp_unslash( $_POST['inserter-data-type'] ) );
			if ( 'rest' !== $data_type && 'custom' !== $data_type && 'post' !== $data_type ) {
				$data_type = 'post';
			}
			update_post_meta( $post_id, 'inserter_template_data_type', $data_type );
		}

		// Save the post-ID.
		if ( isset( $_POST['inserter-template-post-id'] ) ) {
			$post_id_to_save = absint( wp_unslash( $_POST['inserter-template-post-id'] ) );
			update_post_meta( $post_id, 'inserter_template_post_id', $post_id_to_save );
		}

		// Save the data-type.
		if ( isset( $_POST['inserter-element-mode'] ) ) {
			$mode = sanitize_text_field( wp_unslash( $_POST['inserter-element-mode'] ) );
			if ( 'replace' !== $mode && 'prepend' !== $mode && 'append' !== $mode ) {
				$mode = 'replace';
			}
			update_post_meta( $post_id, 'inserter_template_element_mode', $mode );
		}
	}
}
