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

    if( is_multisite() ) self::create_network_settings_page();

    if( !is_multisite() || self::get_carbon_network_option( 'allow_override' ) )
      self::create_site_settings_page();

  }

  /**
    * Create network settings page in WP Network Admin > Settings
    *
    * @since 0.1.0
    */
  private static function create_network_settings_page() {

    $container = Container::make( 'network', self::prefix( 'settings' ), __( 'Conditional Editor', self::$textdomain ) )
      ->set_page_parent( 'settings.php' )
      ->add_fields([
        Field::make( 'checkbox', self::prefix( 'allow_override' ), __( 'Allow Sub-Sites to Override Network Settings', self::$textdomain ) )
          ->help_text( __( 'Displays the Conditional Editor menu item in Settings of sub-sites. If unchecked, Network Settings will be used for all sub-sites.', self::$textdomain ) ),
      ]);

    $container->add_fields( self::create_common_settings_fields( true ) );

  }

  /**
    * Create network settings page in WP Network Admin > Settings
    *
    * @since 0.1.0
    */
  private static function create_site_settings_page() {

    Container::make( 'theme_options', self::prefix( 'settings' ), __( 'Conditional Editor', self::$textdomain ) )
      ->set_page_parent( 'options-general.php' )
      ->add_fields( self::create_common_settings_fields() );

  }

  /**
    * Create settings fields, common to Network and WP Site Admin
    *
    * @since 0.1.0
    */
  private static function create_common_settings_fields( $network = false ) {

    return [
      Field::make( 'checkbox', self::prefix( 'disable_gutenberg' ), __( 'Completely Disable Gutenberg', self::$textdomain ) ),
      Field::make( 'checkbox', self::prefix( 'disable_gutenberg_nag' ), __( 'Disable "Try Gutenberg" Notice/Nag', self::$textdomain ) )
        ->help_text( __( 'Removes the "Try Gutenberg" panel from the WP Admin Dashboard', self::$textdomain ) )
        ->set_default_value( true ),
      // Disable for Post Types
      // Disable for specific Template Files - https://developer.wordpress.org/themes/basics/template-files/
      // Disable by user role
      // Disable for specific sub-sites (if allow-override is not checked)
    ];

  }

}
?>
