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

  function __construct() {

    if( is_multisite() ) $this->create_network_settings_page();

    $this->create_site_settings_page();

  }

  /**
    * Create network settings page in WP Network Admin > Settings
    *
    * @since 0.1.0
    */
  private function create_network_settings_page() {

  }

  /**
    * Create network settings page in WP Network Admin > Settings
    *
    * @since 0.1.0
    */
  private function create_site_settings_page() {

  }

}
?>
