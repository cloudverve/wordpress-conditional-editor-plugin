<?php
namespace CloudVerve\ConditionalEditor;
use CloudVerve\ConditionalEditor\Plugin;

/**
 * Perform plugin logic
 * @since 0.1.0
 */
class Core extends Plugin {

  private static $disable_gutenberg;

  public function init() {

    $this->disable_gutenberg = $this->get_carbon_plugin_option( 'disable_gutenberg' );

    // Disable Gutenberg editor completely
    if( $this->disable_gutenberg )
      add_filter( 'gutenberg_can_edit_post_type', '__return_false' );

    // Disable "Try Gutenberg" notice/nag
    if( $this->disable_gutenberg || $this->get_carbon_plugin_option( 'disable_gutenberg_nag' ) )
      remove_filter( 'try_gutenberg_panel', 'wp_try_gutenberg_panel' );

  }

}
