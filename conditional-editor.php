<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Conditional Editor
 * Plugin URI:        https://github.com/dmhendricks/wordpress-conditional-editor-plugin/
 * Description:       Choose between classic and Gutenberg editor based on conditions.
 * Version:           0.1.0
 * Author:            Daniel M. Hendricks
 * Author URI:        https://www.danhendricks.com/
 * License:           GPL-2.0
 * License URI:       https://github.com/dmhendricks/wordpress-conditional-editor-plugin/blob/master/LICENSE
 */

if( !defined('ABSPATH') ) die();

require( __DIR__ . '/vendor/autoload.php' );

new CloudVerve\ConditionalEditor\Plugin( __FILE__ );
