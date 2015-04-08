<?php
/**
 * Author: Alin Marcu
 * Author URI: https://deconf.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if (! defined('ABSPATH'))
    exit();

if (! class_exists('GADWP_Backend_Item_Reports')) {

    final class GADWP_Backend_Item_Reports
    {
        private $gadwp;

        public function __construct()
        {
            $this->gadwp = GADWP(); 
            
            if (! GADWP_Tools::check_roles($this->gadwp->config->options['ga_dash_access_back']) or 0 == $this->gadwp->config->options['item_reports']) {
                return;
            }
            // Add custom column in Posts List
            add_filter('manage_posts_columns', array(
                $this,
                'add_stats_column'
            ));
            // Populate custom column in Posts List
            add_action('manage_posts_custom_column', array(
                $this,
                'display_item_stats'
            ), 10, 2);
            // Add custom column in Pages List
            add_filter('manage_pages_columns', array(
                $this,
                'add_stats_column'
            ));
            // Populate custom column in Pages List
            add_action('manage_pages_custom_column', array(
                $this,
                'display_item_stats'
            ), 10, 2);
        }

        public function display_item_stats($column, $id)
        {
            global $wp_version;
            
            if ($column != 'gadwp_stats') {
                return;
            }
            
            if (version_compare($wp_version, '3.8.0', '>=')) {
                echo '<a id="gadwp-' . $id . '" title="' . get_the_title($id) . '" href="#' . $id . '" class="gadwp-icon dashicons-before dashicons-chart-area"></a>';
            } else {
                echo '<a id="gadwp-' . $id . '" title="' . get_the_title($id) . '" href="#' . $id . '"><img class="gadwp-icon-oldwp" src="' . GADWP_URL . 'admin/images/gadash-icon.png"</a>';
            }
        }

        public function add_stats_column($columns)
        {
            return array_merge($columns, array(
                'gadwp_stats' => __('Analytics', 'ga-dash')
            ));
        }
    }
}
