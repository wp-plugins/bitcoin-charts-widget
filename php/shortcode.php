<?php
/**
 * Plugin Name: Bitcoin Charts Widget
 * Plugin URI: http://www.bitcoin-welt.com/wordpress-plugin-bitcoin-charts-als-widget/
 * Author: Both Interact GmbH
 * Author URI: http://www.both-interact.com
 * Version: 0.3.7
 * Tags: bitcoin, charts, widget, bitcoin-welt, blockchain.info, service
 * Requires at least: 3.5.0
 * Tested up to: 4.0
 * License: GPL2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Description: Displays bitcoin charts as widget using data from external 
 *              services such as blockchain.info.
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
add_shortcode('btcchart', 'btccharts_shortcode_btcchart');

function btccharts_shortcode_btcchart($args) {
    $a = array_merge(array(
        "service" => "blockchain.info",
        "chart" => "market-price",
            ), $args);

    return //"hi<pre>".htmlspecialchars(var_export($a, true))."</pre>".
            btccharts_render_chart($a['domain'], $a['service'], $a['chart'], $a);
}
