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
        Field::make( 'text', $this->prefix( 'required_capability' ), __( 'Capability Required to Modify Sub-Site Settings', $this->textdomain ) )
          ->help_text( sprintf( __( 'See <a href="%s" target="_blank">Roles &amp; Capabilities</a> for a list of valid capabilities. Set to <tt>manage_network</tt> to disable for site administrators. Default: <tt>manage_options</tt>.', $this->textdomain ), 'https://codex.wordpress.org/Roles_and_Capabilities#Roles' ) )
          ->set_default_value( 'manage_options' ),
        Field::make( 'separator', $this->prefix( 'network_settings_defaults' ), __( 'Global Defaults', $this->textdomain ) )
          ->help_text( __( 'The setting below are <strong>defaults</strong> for sub-sites and may be overridden if the user has the capability defined above. Post Types and Template Files are not included here since they vary by theme.', $this->textdomain ) )
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

    $container->add_fields( $this->create_common_settings_fields( false ) );

  }

  /**
    * Create settings fields, common to Network and WP Site Admin
    *
    * @since 0.1.0
    */
  private function create_common_settings_fields( $network = false ) {

    $post_types = [];

    $fields = [
      Field::make( 'checkbox', $this->prefix( 'disable_gutenberg' ), __( 'Completely Disable Gutenberg', $this->textdomain ) )
        ->help_text( __( 'If checked, the options below will be ignored (but saved for future use).', $this->textdomain ) )
        ->set_default_value( $network ? false : $this->get_carbon_network_option( 'disable_gutenberg' ) ),
      Field::make( 'checkbox', $this->prefix( 'disable_gutenberg_nag' ), __( 'Disable "Try Gutenberg" Notice/Nag', $this->textdomain ) )
        ->help_text( __( 'Removes the "Try Gutenberg" panel from the WP Admin Dashboard.', $this->textdomain ) )
        ->set_default_value( $network ?: $this->get_carbon_network_option( 'disable_gutenberg_nag' ) ),
      Field::make( 'set', $this->prefix( 'disabled_roles' ), __( 'Limit User Roles to Classic Editor', $this->textdomain ) )
        ->set_datastore( new Serialized_Theme_Options_Datastore() )
        //->help_text( $network ? __( 'Super Admins always have access to modify sub-site settings.', $this->textdomain ) : null )
        ->set_default_value( $network ? null : $this->get_carbon_network_option( 'disabled_roles' ) )
        ->add_options( $this->get_user_roles( $network ) )
    ];

    if( !is_network_admin() ) {

      // Disable for Post Types
      $fields[] = Field::make( 'set', $this->prefix( 'disabled_post_types' ), __( 'Use Classic Editor for Post Types', $this->textdomain ) )
        ->set_datastore( new Serialized_Theme_Options_Datastore() )
        ->add_options( $this->get_post_types() );

      // Disable Template Files
      $page_templates = wp_get_theme()->get_page_templates();
      if( $page_templates ) {
        $fields[] = Field::make( 'set', $this->prefix( 'disabled_template_files' ), __( 'Use Classic Editor for Template Files', $this->textdomain ) )
          ->set_datastore( new Serialized_Theme_Options_Datastore() )
          ->help_text( sprintf( __( '<a href="%s">Page Templates</a> are usually defined by your theme and sometimes plugins.', $this->textdomain ), 'https://developer.wordpress.org/themes/basics/template-files/' ) )
          ->add_options( $page_templates );
      }

    }

    return $fields;

  }

  /**
    * Get defined post types
    *
    * @since 0.1.0
    */
  private function get_post_types() {

    $post_types = [];
    $types = get_post_types( [ 'public' => true ], 'objects' );

    foreach( $types as $key => $type ) {
      $post_types[ $key ] = sprintf( '%s (%s)', $type->label, $key );
    }

    return $post_types;

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

    //if( ( $roles && !$network ) || !is_multisite() ) unset( $roles['administrator'] );

    return $roles;

  }

}
