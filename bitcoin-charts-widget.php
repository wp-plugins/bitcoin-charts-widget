<?php

/**
 * Plugin Name: Bitcoin Charts Widget
 * Plugin URI: http://www.bitcoin-welt.com/
 * Description: Displays bitcoin charts as widget with data from external services.
 * Author: Both Interact GmbH
 * Author URI: http://www.both-interact.com
 * Version: 0.3.6
 * License: GPL2 or later
 *
 * Copyright 2014 Both Interact GmbH (office@both-interact.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
defined('ABSPATH') or die("No script kiddies please!");

$btccharts_version = "0.3.6";

require_once 'php/chart.php';
require_once 'php/activate.php';
require_once 'php/widget.php';
require_once 'php/shortcode.php';
require_once 'php/admin.php';

function btccharts_plugin_action_links($links, $file) {
    if ($file == plugin_basename(dirname(__FILE__) . '/bitcoin-charts-widget.php')) {
        // NOT YET DONE $links[] = '<a href="admin.php?page=btccharts-key-config">'.__('Settings').'</a>';
    }
    return $links;
}

function btccharts_admin_menu() {
    /* NOT YET DONE add_submenu_page('plugins.php', 
      __('Bitcoin Charts Configuration'), __('Bitcoin Charts Configuration'),
      'manage_options', 'btccharts-key-config', 'btccharts_config_page'); */
}

// always reload after version change
if (get_option("btccharts:version") != $btccharts_version) {
    btccharts_deactive();
    btccharts_activate();
}

register_activation_hook(__FILE__, "btccharts_activate");
register_deactivation_hook(__FILE__, "btccharts_deactive");
add_filter('plugin_action_links', 'btccharts_plugin_action_links', 10, 2);
add_action('admin_menu', 'btccharts_admin_menu');

