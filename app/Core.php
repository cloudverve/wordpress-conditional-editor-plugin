<?php
namespace CloudVerve\ConditionalEditor;
use CloudVerve\ConditionalEditor\Plugin;

/**
 * Perform plugin logic
 * @since 0.1.0
 */
class Core extends Plugin {

  private $disable_gutenberg;
  private $post_types;

  public function init() {

    $this->disable_gutenberg = $this->get_carbon_plugin_option( 'disable_gutenberg' );

    // Disable "Try Gutenberg" notice/nag
    if( $this->disable_gutenberg || $this->get_carbon_plugin_option( 'disable_gutenberg_nag' ) )
      remove_filter( 'try_gutenberg_panel', 'wp_try_gutenberg_panel' );

    // Disable Gutenberg editor completely
    if( $this->disable_gutenberg ) {
      add_filter( 'gutenberg_can_edit_post_type', '__return_false' );
      return;
    }

    // Disable for Post Types
    $this->post_types = $this->get_carbon_plugin_option( 'disabled_post_types' );
    if( $this->post_types ) {
      add_filter('gutenberg_can_edit_post_type', array( $this, 'post_types_classic_editor' ) );
    }

  }

  public function post_types_classic_editor( $is_enabled, $post_type ) {

    if( in_array( $post_type, $this->post_types ) ) return false;
    return $is_enabled;

  }

}
