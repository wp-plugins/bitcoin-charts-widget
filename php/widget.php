<?php
/**
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
class BtcChartsWidget extends WP_Widget {

    public function __construct() {
        parent::__construct("btcharts", "Bitcoin Charts", array('description' => __('Bitcoin Charts Widget', 'text_domain')));
    }

    public function form($instance) {
        $serviceUrl = "blockchain.info";
        $chartType = "market-price";
        $chartStyle = "default";
        $domain = "";
        $chartTypeOptions = json_decode(file_get_contents(dirname(__FILE__) . "/../js/chart-types.json"), true);
        $chartStyles = json_decode(file_get_contents(dirname(__FILE__) . "/../js/styles.json"), true);

        if ($instance) {
            $this->formData = $instance;
            $serviceUrl = $instance['serviceUrl'];
            $chartType = $instance['chartType'];
            $chartStyle = $instance['style'];
            $domain = $instance['domain'];
        }
        ?>
        <div>
            <div><?php echo __('Service', 'text_domain'); ?></div>
            <select size="1" id="<?php echo $this->get_field_id("serviceUrl"); ?>" name="<?php echo $this->get_field_name("serviceUrl"); ?>">
                <?php
                foreach ($chartTypeOptions as $type => $opt) {
                    $sel = ($type == $serviceUrl) ? " selected=\"selected\"" : "";
                    echo "<option value=\"" . esc_attr($type) . "\"$sel>" . esc_html($opt['label']) . "</option>\n";
                }
                ?>
            </select>
        </div>
        <div>
            <div><?php echo __('Chart', 'text_domain'); ?></div>
            <select size="1" id="<?php echo $this->get_field_id("chartType"); ?>" name="<?php echo $this->get_field_name("chartType"); ?>">
                <?php
                foreach ($chartTypeOptions as $chartService => $data) {
                    foreach ($data['$chart-type'] as $type => $opt) {
                        $sel = ($type == $chartType) ? " selected=\"selected\"" : "";
                        echo "<option value=\"" . esc_attr($type) . "\"$sel>" . esc_html($opt['label']) . "</option>\n";
                    }
                }
                ?>
            </select>
        </div>
        <div>
            <div><?php echo __('Style', 'text_domain'); ?></div>
            <select size="1" id="<?php echo $this->get_field_id("style"); ?>" name="<?php echo $this->get_field_name("style"); ?>">
                <?php
                foreach ($chartStyles as $styleName => $data) {
                    $sel = ($styleName == $chartStyle) ? " selected=\"selected\"" : "";
                    echo "<option value=\"" . esc_attr($styleName) . "\"$sel>" . esc_html($styleName) . "</option>\n";
                }
                ?>
            </select>
        </div>
        <!--<div>
            <div><?php echo __('Domain', 'text_domain'); ?></div>
        <?php $this->input("domain"); ?>
        </div>-->
        <!--<div>
            <div><?php echo __('Title (Diagram)', 'text_domain'); ?></div>
        <?php echo 'bitcoin-welt.com'; /* $this->input("title"); */ ?>
        </div>-->
        <!--<div>
            <div><?php echo __('Label (Data)', 'text_domain'); ?></div>
        <?php $this->input("label"); ?>
        </div>-->
        <!-- <div>
        <div><?php echo __('Width/Height', 'text_domain'); ?></div>
        <?php $this->input("width", 4); ?>/<?php $this->input("height", 4); ?>
        </div>-->
        <?php
    }

    private function input($name, $width = null) {
        $v = @$this->formData[$name];
        echo '<input id="' . $this->get_field_id($name) . '" name="' . $this->get_field_name($name) . '" value="'
        . ($v ? esc_attr($v) : '') . '"';
        if ($width)
            echo ' style="width:' . ($width * 6) . 'pt"';
        echo ' />';
    }

    public function widget($args, $instance) {
        echo "<aside class='widget btccharts_widget'>";
        echo btccharts_render_chart($instance['domain'], $instance['serviceUrl'], $instance['chartType'], $instance);
        echo "</aside>";
    }

}

function btccharts_register_widget() {
    register_widget('BtcChartsWidget');
}

add_action('widgets_init', 'btccharts_register_widget');
