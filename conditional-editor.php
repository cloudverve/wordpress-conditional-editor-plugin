<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Conditional Editor
 * Plugin URI:        https://github.com/cloudverve/wordpress-conditional-editor-plugin/
 * Description:       Choose between classic and Gutenberg editor based on conditions.
 * Version:           0.2.0
 * Author:            CloudVerve
 * Author URI:        https://www.cloudverve.com/
 * License:           GPL-2.0
 * License URI:       https://github.com/cloudverve/wordpress-conditional-editor-plugin/blob/master/LICENSE
 */

if( !defined('ABSPATH') ) die();

require( __DIR__ . '/vendor/autoload.php' );

new CloudVerve\ConditionalEditor\Plugin();
