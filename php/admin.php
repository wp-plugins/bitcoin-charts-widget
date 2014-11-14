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
function btccharts_config_page() {
    $styles = array();
    foreach (wp_load_alloptions() as $name => $val) {
        if (substr($name, 0, 16) == "btccharts:style#") {
            $styles[substr($name, 16)] = unserialize($val)->parameters;
        }
    }
    echo "<select size='20' style='height:120pt'>";
    foreach (array_map('htmlspecialchars', array_keys($styles)) as $htm) {
        echo "<option value=\"$htm\">$htm</option>\n";
    }
    echo "</select>";

    foreach ($styles as $name => $val) {
        echo "<div id='$name'>";
        echo "<label for='$name'>" . htmlspecialchars(__("Style name")) . "</label>";
        echo "<input id='style_name_$name' name='style_name[]' value='" . htmlspecialchars($name) . "' />";
        foreach (btccharts_settings_template() as $k => $def_v) {
            $v = @$val[$k];
            $id = htmlspecialchars($name . '_' . str_replace('-', '_', $k));
            $fldName = str_replace('-', '_', $k);
            echo "<div>";
            echo "<label for='$id'>" . htmlspecialchars($k) . "</label>";
            echo "<input id='$id' name='{$fldName}[]' value='" . htmlspecialchars($v) . "' />";
            echo "</div>";
        }
        echo "</div>";
    }
}
