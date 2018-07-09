<?php
namespace CloudVerve\ConditionalEditor;
use CloudVerve\ConditionalEditor\Plugin;

/**
 * Disable Gutenber based on configuration settings
 * @since 0.1.0
 */
class Core extends Plugin {

  private $disable_gutenberg;

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

    // Disable by Post Type and Template Files
    add_filter( 'gutenberg_can_edit_post_type', array( $this, 'disable_gutenberg' ), 10, 2 );

  }

  public function disable_gutenberg( $is_enabled, $post_type ) {

    // Disable by Post Type
    $post_types = $this->get_carbon_plugin_option( 'disabled_post_types' );
    if( $post_types && in_array( $post_type, $this->post_types ) ) return false;

    // Disable by User Role
    $current_user = wp_get_current_user();
    if( isset( $current_user->roles[0] ) && $current_user->roles[0] ) {
      $disabled_roles = $this->get_carbon_plugin_option( 'disabled_roles' );
      if( $disabled_roles && in_array( $current_user->roles[0], $disabled_roles ) ) return false;
    }

    // Disable Gutenberg editor by Template Files
    if( is_admin() && !is_network_admin() && isset( $_GET['post'] ) && intval( $_GET['post'] ) ) {

      $exclude_templates = $this->get_carbon_plugin_option( 'disabled_template_files' );

      if( $exclude_templates ) {
        $current_template = get_page_template_slug( $_GET['post'] );
        if( $current_template && in_array( $current_template, $exclude_templates ) ) return false;
      }

    }

    return $is_enabled;

  }

}
