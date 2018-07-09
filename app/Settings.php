<?php
namespace CloudVerve\ConditionalEditor;
use Carbon_Fields\Datastore\Datastore\Serialized_Theme_Options_Datastore;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Create network and site admin settings pages
 * @since 0.1.0
 */
class Settings extends Plugin {

  public function init() {

    if( is_multisite() ) $this->create_network_settings_page();

    //if( !is_multisite() || $this->get_carbon_network_option( 'allow_override' ) )
    $this->create_site_settings_page();

  }

  /**
    * Create network settings page in WP Network Admin > Settings
    *
    * @since 0.1.0
    */
  private function create_network_settings_page() {

    $container = Container::make( 'network', $this->prefix( 'settings' ), __( 'Conditional Editor', $this->textdomain ) )
      ->set_page_parent( 'settings.php' )
      ->add_fields([
        Field::make( 'text', $this->prefix( 'required_capability' ), __( 'Capatbility Required to Modify Sub-Site Settings', $this->textdomain ) )
          ->help_text( sprintf( __( 'See <a href="%s" target="_blank">Roles &amp; Capabilities</a> for a list of valid capabilities. Default: <tt>manage_options</tt>', $this->textdomain ), 'https://codex.wordpress.org/Roles_and_Capabilities#Roles' ) )
          ->set_default_value( 'manage_options' ),
        Field::make( 'separator', $this->prefix( 'network_settings_defaults' ), __( 'Global Defaults', $this->textdomain ) ),
        /*
        Field::make( 'checkbox', $this->prefix( 'allow_override' ), __( 'Allow Sub-Sites to Override Network Settings', $this->textdomain ) )
          ->help_text( __( 'Displays the Conditional Editor menu item in Settings of sub-sites. If unchecked, Network Settings will be used for all sub-sites.', $this->textdomain ) ),
        */
      ]);

    $container->add_fields( $this->create_common_settings_fields( true ) );

  }

  /**
    * Create network settings page in WP Network Admin > Settings
    *
    * @since 0.1.0
    */
  private function create_site_settings_page() {

    $required_user_capability = (array) $this->get_carbon_network_option( 'required_capability' );

    $container = Container::make( 'theme_options', $this->prefix( 'settings' ), __( 'Conditional Editor', $this->textdomain ) )
      ->where( 'current_user_capability', 'IN', $required_user_capability )
      ->set_page_parent( 'options-general.php' );
      /*
      ->add_fields([
        Field::make( 'checkbox', $this->prefix( 'allow_override' ), __( 'Override Network/Global Settings', $this->textdomain ) )
          ->help_text( __( 'If checked, settings define on this site/blog will be used instead of default network settings.', $this->textdomain ) ),
      ]);
      */

    $container->add_fields( $this->create_common_settings_fields( false ) );

  }

  /**
    * Create settings fields, common to Network and WP Site Admin
    *
    * @since 0.1.0
    */
  private function create_common_settings_fields( $network = false ) {

    $post_types = [];

    return [
      Field::make( 'checkbox', $this->prefix( 'disable_gutenberg' ), __( 'Completely Disable Gutenberg', $this->textdomain ) ),
      Field::make( 'checkbox', $this->prefix( 'disable_gutenberg_nag' ), __( 'Disable "Try Gutenberg" Notice/Nag', $this->textdomain ) )
        ->help_text( __( 'Removes the "Try Gutenberg" panel from the WP Admin Dashboard.', $this->textdomain ) )
        ->set_default_value( true ),
      Field::make( 'set', $this->prefix( 'disabled_roles' ), __( 'Disable for User Roles', $this->textdomain ) )
        ->help_text( $network ? __( 'Super Admins always have access to modify sub-site settings.', $this->textdomain ) : null )
        ->add_options( $this->get_user_roles( $network ) ),
    ];

    // TODO:
    // 1. Disable for Post Types
    // 2. Disable for specific Template Files - https://developer.wordpress.org/themes/basics/template-files/

  }

  /**
    * Get defined post types
    *
    * @since 0.1.0
    */
  private function get_post_types() {

    // TODO

  }

  /**
    * Get a list of defined user roles
    *
    * @since 0.1.0
    */
  private function get_user_roles( $network = false ) {

    global $wp_roles;
    $roles = [];

    foreach( $wp_roles->roles as $key => $role ) {
      $roles[ $key ] = $role['name'];
    }

    if( ( $roles && !$network ) || !is_multisite() ) unset( $roles['administrator'] );

    return $roles;

  }

}
