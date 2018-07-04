<?php
namespace CloudVerve\ConditionalEditor;

/**
 * Plugin loader and dependency checker.
 * @since 0.1.0
 */
class Core {

  public $plugin_file;
  public $plugin_identifier;
  public $config;
  public $prefix;

  function __construct() {

    $this->plugin_file = trailingslashit( dirname( __DIR__ ) ) . 'conditional-editor.php';
    $this->plugin_identifier = $this->get_plugin_identifier();
    $this->config = $this->get_plugin_config();
    $this->prefix = $this->config->prefix;

    // Check dependencies
    register_activation_hook( $this->plugin_identifier, array( $this, 'activate' ) );

    // Load plugin after Carbon Fields is initialized
    add_action( 'carbon_fields_fields_registered', array( $this, 'load_plugin' ) );

  }

  /**
    * Load the plugin
    *
    * @since 0.1.0
    */
  public function load_plugin() {

    // Create settings page(s)
    new Settings();

    // Perform plugin logic
    new Plugin();

  }

  /**
    * Check plugin dependencies on activation.
    *
    * @since 0.1.0
    */
  public function activate() {

    $notices = [];

    // Check PHP version
    if( version_compare( phpversion(), $this->config->dependencies->php, '<' ) ) {
      $notices[] = __( 'This plugin is not supported on versions of PHP below', 'conditional-editor' ) . ' ' . $this->config->dependencies->php . '.' ;
    }

    // Check Carbon Fields version
    $cf_version = defined('\\Carbon_Fields\\VERSION') ? current( explode( '-', \Carbon_Fields\VERSION ) ) : null;
    if ( $cf_version && version_compare( $cf_version, $version, '<' ) ) {
      $notices[] = __( 'An outdated version of Carbon Fields has been detected:', 'conditional-editor' ) . ' ' . $cf_version . ' (&gt;= ' . self::$config->get( $this->config->dependencies->php ) . ' ' . __( 'required', 'conditional-editor' ) . ').' . ' <strong>' . $this->get_plugin_meta( 'Name' ) . '</strong> ' . __( 'deactivated.', 'conditional-editor' ) ;
    }

    if( $notices ) {

      deactivate_plugins( $this->plugin_identifier );

      $notices = '<ul><li>' . implode( "</li>\n<li>", $notices ) . '</li></ul>';
      die( $notices );

    }

  }

  /**
    * Get plugin header meta field value(s)
    *
    * @param string $field The field label of the value to retrieve
    * @return mixed The field value string or an array of all values
    * @since 0.1.0
    */
  private function get_plugin_identifier() {

    $file = explode( DIRECTORY_SEPARATOR, $this->plugin_file );
    return implode( DIRECTORY_SEPARATOR, array_slice( $file, -2, 2 ) );

  }

  /**
    * Get plugin configuration from plugin.json
    *
    * @param string $field The field label of the value to retrieve
    * @since 0.1.0
    */
  private function get_plugin_config() {

    $config_file = trailingslashit( dirname( $this->plugin_file ) ) . 'plugin.json';
    return json_decode( file_get_contents( $config_file ) );

  }

  /**
    * Get plugin header meta field value(s)
    *
    * @param string $field The field label of the value to retrieve
    * @return mixed The field value string or an array of all values
    * @since 0.1.0
    */
  public function get_plugin_meta( $field = null ) {

    $plugin_data = get_plugin_data( $this->plugin_file );
    return $field ? $plugin_data[$field] : $plugin_data;

  }

}
?>
