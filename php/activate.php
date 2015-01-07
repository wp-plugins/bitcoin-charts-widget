<?php
/**
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
function btccharts_activate() {
    add_option("btccharts:version", $GLOBALS['btccharts_version']);
    // timeout in seconds (for wp transient objects)
    add_option("btccharts:cache_timeout", 3600);
    //make new cache ids after active 
    add_option("btccharts:cache_id", rand());

    $chartTypeOptions = json_decode(file_get_contents(dirname(__FILE__) . "/../js/chart-types.json"), true);
    $id = 0;
    foreach ($chartTypeOptions as $service => $serviceOpts) {
        $types = $serviceOpts['$chart-type'];
        unset($serviceOpts['$chart-type']);
        $o = new btccharts_Option( ++$id, 'service', $service, $serviceOpts);
        add_option("btccharts:service#$service", $o);
        foreach ($types as $chartName => $chartOpts) {
            $o = new btccharts_Option( ++$id, $service, $chartName, $chartOpts);
            add_option("btccharts:$service#$chartName", $o);
        }
    }
    $styles = json_decode(file_get_contents(dirname(__FILE__) . "/../js/styles.json"), true);
    foreach ($styles as $styleName => $styleOpts) {
        $o = new btccharts_Option($id++, 'style', $styleName, $styleOpts);
        add_option("btccharts:style#$styleName", $o);
    }
}

function btccharts_deactive() {
    foreach (wp_load_alloptions() as $name => $val) {
        if (substr($name, 0, 10) == "btccharts:") {
            delete_option($name);
        }
    }
}
