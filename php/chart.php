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
$btccharts_show_timings = false;

class btccharts_Option {

    /** @var int $id  technical id */
    public $id;

    /** @var string $class Option classification
     * service 
     * domain
     * style 
     * {service-id}
     */
    public $class;

    /** @var string $code Option indentification (based on classification) */
    public $code;

    /** @var array $parameters Option parameters. An array of values or arrays */
    public $parameters;

    public function __construct($id, $class, $code, $parameters) {
        $this->id = $id;
        $this->class = $class;
        $this->code = $code;
        $this->parameters = $parameters;
    }

}

function btccharts_get_option($class, $code) {
    if ($code == null)
        return get_option("btccharts:$class");
    return get_option("btccharts:$class#$code");
}

function btccharts_get_options($class) {
    $ret = array();
    foreach (wp_load_alloptions() as $name => $opt) {
        if (substr($name, 0, 10) == 'btccharts:') {
            if ($opt->class == $class)
                $ret[] = $opt;
        }
    }
    return $ret;
}

function btccharts_cache($uid, $file) {
    set_transient("btccharts:$uid", file_get_contents($file), 60 * 60 * 24);
}

function btccharts_get_cached($uid) {
    get_transient("btccharts:$uid");
}

function btccharts_get_url($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function btccharts_settings_template() {
    return array(
        "width" => 400,
        "height" => 300,
        "title" => "",
        "label" => "",
        "image-href" => "",
        "curve_color" => "#FF0000",
        "background_color" => "#f7931a,Dash:#f7931a",
        "background_gradient" => "Start:#f7931a,End:#D88116",
        "title_font" => "calibri.ttf,8,#FFF",
        "default_font" => "verdana.ttf,6,#000",
        "x-axis-label" => "",
        "x-axis-label-rotation" => 40,
        "x-zoom" => 1.0,
        "x-axis-date-format" => "M.Y",
        "y-axis-label" => "",
        "y-zoom" => 1.0,
        "label-pos" => "-100,9", // left, top
        "title-pos" => "10,16", // left, top
        "graph-area" => "70,30,-10,-60" // left, top, right, bottom; -x .. distance from bottom/right border
    );
}

function btccharts_filter_settings($settings) {
    $template = btccharts_settings_template();
    $filtered = array();
    // filter out everything not in template
    foreach ($settings as $k => $v) {
        if (key_exists($k, $template) && !empty($v)) {
            if ($k == "width" && (0 + $v < 120 || 0 + $v > 2000)) {
                continue;
            } elseif ($k == "height" && (0 + $v < 150 || 0 + $v > 2000)) {
                continue;
            } elseif ($k == "x-axis-label-rotation" && (0 + $v < 0 || 0 + $v > 360)) {
                continue;
            }
            $filtered[$k] = $v;
        }
    }
    return $filtered;
}

/**
 * 
 * @param array $settings
 * @return array style attributes
 */
function btccharts_style($settings) {

    if (key_exists('style', $settings)) {
        $style = btccharts_get_option('style', $settings['style']);
        if (is_object($style))
            $default = $style->parameters;
    }
    if (!isset($default) || !is_array($default) || count($default) == 0) {
        $default = btccharts_settings_template();
    }
    foreach (btccharts_filter_settings($settings) as $k => $v) {
        $default[$k] = $v;
    }
    return $default;
}

/** Create a HTML snippet with a link to the chart image.
 * 
 * @param unknown $serviceId
 * @param unknown $chartType
 * @param unknown $settings
 * @return string
 */
function btccharts_render_chart($domain, $serviceId, $chartType, $settings) {
    $service = btccharts_get_option("service", $serviceId)->parameters;
    $opts = btccharts_get_option($serviceId, $chartType)->parameters;
    $html = "";
    $html .= "<div class='btcchart'>";
    $s = btccharts_style(array_merge($opts, $settings));
    $imgLink = $s['image-href'] ? $s['image-href'] : $service['image-href'];

    $imgUrl = btccharts_render_chart_image(false, $domain, $serviceId, $chartType, $s);

    $html .= "<div>";
    if ($imgLink)
        $html .= '<a href="' . htmlspecialchars($imgLink) . '">';
    $html .= "<img src=\"$imgUrl\"/>";
    if ($imgLink)
        $html .= "</a>";
    $html .= "</div>";
    $html .= "</div>";
    return $html;
}

function btccharts_render_chart_image($writeToStdout, $domain, $serviceUrl, $chartType, $settings = array()) {
    $t0 = microtime(true);
    if ($writeToStdout)
        ob_start();
    //$chartTypeOptions = json_decode(file_get_contents(dirname(__FILE__)."/../js/chart-types.json"), true);
    //$serviceDesc = $chartTypeOptions[$serviceUrl];
    //$opts = $serviceDesc['$chart-type'][$chartType];

    $cache_id = btccharts_get_option("cache_id", "");
    $cache_timeout = btccharts_get_option("cache_timeout", 60);
    $serviceDesc = btccharts_get_option("service", $serviceUrl)->parameters;
    $opts = btccharts_get_option($serviceUrl, $chartType)->parameters;
    $service = str_replace('$chart-type', $chartType, $serviceDesc['service-url']);
    $s = btccharts_style(array_merge($opts, $settings));
    $id = md5($domain . $chartType . serialize($s) . serialize($serviceDesc) . $cache_id);
    //user_error("btccharts_render_chart_image id ($id)", E_USER_WARNING);
    $cached = get_transient("btccharts:$id");
    if (!is_null($cached) && strlen($cached) > 0) {
        if ($writeToStdout) {
            $img = base64_decode($cached);
            if ($GLOBALS['btccharts_show_timings'])
                user_error("btccharts_render_chart_image from cache: t0=" . (microtime(true) - $t0), E_USER_WARNING);
            ob_end_clean();
            header("Expires: " . date('r', time() + $cache_timeout));
            header("Content-Type: image/png");

            echo($img);
            return;
        } else {
            if ($GLOBALS['btccharts_show_timings'])
                user_error("btccharts_render_chart_image from cache: t0=" . (microtime(true) - $t0), E_USER_WARNING);
            return "data:image/png;base64," . $cached;
        }
    }

    $t1 = microtime(true);
    if ($GLOBALS['btccharts_show_timings'])
        user_error("btccharts_render_chart_image init: t=" . ($t1 - $t0), E_USER_WARNING);
    $data = json_decode(btccharts_get_url($service));
    $t2 = microtime(true);
    if ($GLOBALS['btccharts_show_timings'])
        user_error("btccharts_render_chart_image service: t=" . ($t2 - $t1), E_USER_WARNING);
    /* CAT:Line chart */

    /* pChart library inclusions */
    $pChartRoot = dirname(__FILE__) . '/../3rdparty/pChart/';
    require_once $pChartRoot . 'class/pData.class.php';
    require_once $pChartRoot . 'class/pDraw.class.php';
    require_once $pChartRoot . 'class/pImage.class.php';

    $x_zoom = @$opts['x-zoom'] ? $opts['x-zoom'] : 1.0;
    $y_zoom = $s['y-zoom'];
    $date_axis = array();
    $value_axis = array();
    foreach ($data->values as $pt) {
        $date_axis[] = $pt->x / $x_zoom;
        $value_axis[] = $pt->y / $y_zoom;
    }
    /* Create and populate the pData object */
    $MyData = new pData();
    $MyData->addPoints($value_axis, $s['label']);
    $MyData->addPoints($date_axis, "Dates");
    $MyData->setSerieWeight($s['label'], 0);
    $MyData->setSerieShape($s['label'], 0);
    $MyData->setAxisName(0, $s['y-axis-label']);
    $MyData->Data["Series"][$s['label']]["Color"] = rgb($s['curve_color']);
    $MyData->setAbscissaName($s['x-axis-label']);
    $MyData->setAbscissa("Dates");
    $MyData->setXAxisDisplay(AXIS_FORMAT_TIME, $s["x-axis-date-format"]);

    /* Create the pChart object */
    $myPicture = new pImage($s['width'], $s['height'], $MyData);

    /* Turn of Antialiasing */
    $myPicture->Antialias = TRUE;

    /* Draw the background */
    //f7931a 247 147 26
    $Settings = rgb($s['background_color'], array("Dash" => 1), 50);
    $myPicture->drawFilledRectangle(0, 0, $s['width'], $s['height'], $Settings);

    /* Overlay with a gradient */
    $Settings = rgb($s['background_gradient'], array(), 50);
    $myPicture->drawGradientArea(0, 0, $s['width'], $s['height'], DIRECTION_VERTICAL, $Settings);
    $myPicture->drawGradientArea(0, 0, $s['width'], 20, DIRECTION_VERTICAL, rgb("Start:#000,End:#323232", array("Alpha" => 80)));

    /* Add a border to the picture */
    $myPicture->drawRectangle(0, 0, $s['width'] - 1, $s['height'] - 1, rgb("#000"));

    /* Write the chart title */
    $title = empty($s['title']) ? $serviceDesc['label'] : $s['title'];
    if (trim($title) != '') {
        $myPicture->setFontProperties(font($s['title_font']));
        $g = btccharts_pos('title-pos', $s);
        $myPicture->drawText($g[0], $g[1], $title, array("Align" => TEXT_ALIGN_BOTTOMLEFT));
    }
    /* Set the default font */
    $myPicture->setFontProperties(font($s['default_font']));

    /* Define the chart area */
    // $s['graph-area'] == "<left>,<top>,<right>,<bottom>"
    $g = array_map('trim', explode(',', $s['graph-area']));
    while ($g[0] < 0)
        $g[0] += $s['width'];
    while ($g[1] < 0)
        $g[1] += $s['height'];
    while ($g[2] < 0)
        $g[2] += $s['width'];
    while ($g[3] < 0)
        $g[3] += $s['height'];
    $myPicture->setGraphArea($g[0], $g[1], $g[2], $g[3]);

    /* Draw the scale */
    $scaleSettings = rgb("Grid:#C8C8C8", array("XMargin" => 10, "YMargin" => 10, "Floating" => TRUE, "RemoveSkippedAxis" => TRUE,
        "ManualScale" => array(0 => array("Min" => 10, "Max" => 100)),
        "DrawSubTicks" => FALSE, "CycleBackground" => TRUE,
        "LabelingMethod" => LABELING_DIFFERENT,
        "LabelRotation" => $s['x-axis-label-rotation']));
    //$scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"RemoveSkippedAxis"=>TRUE,"DrawSubTicks"=>FALSE,"Mode"=>SCALE_MODE_START0,"LabelingMethod"=>LABELING_DIFFERENT);
    $myPicture->drawScale($scaleSettings);

    /* Turn on Antialiasing */
    $myPicture->Antialias = TRUE;

    /* Enable shadow computing */
    $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));

    /* Draw the line chart */
    $myPicture->drawLineChart();
    $myPicture->drawPlotChart(array(
        "DisplayValues" => FALSE, "PlotBorder" => TRUE, "BorderSize" => 2,
        "Surrounding" => -60, "BorderAlpha" => 80));

    /* Write the chart legend */
    $g = btccharts_pos('label-pos', $s);
    $myPicture->drawLegend($g[0], $g[1], rgb("Font:#FFF", array(
        "Style" => LEGEND_NOBORDER, "Mode" => LEGEND_HORIZONTAL)));

    /* Render the picture (choose the best way) */
    $fn = tempnam(sys_get_temp_dir(), 'btccharts');
    @$myPicture->render($fn);
    $img = file_get_contents($fn);
    if ($GLOBALS['btccharts_show_timings'])
        user_error("btccharts_render_chart_image image render: t=" . (microtime(true) - $t2), E_USER_WARNING);
    set_transient("btccharts:$id", base64_encode($img), $cache_timeout);
    if (!$writeToStdout) {
        $ret = "data:image/png;base64," . base64_encode($img);
    } else {
        unlink($fn);
        ob_end_clean();
        header("Expires: " . date('r', time() + $cache_timeout));
        header("Content-Type: image/png");
        echo $img;
        $ret = null;
    }
    $t3 = microtime(true);
    if ($GLOBALS['btccharts_show_timings'])
        user_error("btccharts_render_chart_image image: t=" . ($t3 - $t2), E_USER_WARNING);
    return $ret;
}

function btccharts_pos($field, &$settings) {
    $g = array_map('trim', explode(',', $settings[$field]));
    for ($i = 0; $i + 1 < count($g); $i += 2) {
        while ($g[$i + 0] < 0)
            $g[$i + 0] += $settings['width'];
        while ($g[$i + 1] < 0)
            $g[$i + 1] += $settings['height'];
    }
    return $g;
}

function rgb($webs, $dest = array(), $def_alpha = 100) {
    foreach (array_map('trim', explode(',', $webs)) as $web) {
        $prefix = "";
        if (strpos($web, ':') !== false)
            list($prefix, $web) = array_map('trim', explode(':', $web));
        if ($prefix == "Alpha") {
            $dest['Alpha'] = (int) $web;
        }
        $ofs = strlen($web) % 3;
        $len = (int) (strlen($web) / 3);
        foreach (array("R", "G", "B") as $i => $c) {
            $dest[$prefix . $c] = hexdec(substr($web, $ofs + $i * $len, $len)) * ($len == 1 ? 16 : 1);
        }
    }
    if (!key_exists('Alpha', $dest))
        $dest['Alpha'] = $def_alpha;

    return $dest;
}

function font($spec, $dest = array()) {
    list($filename, $pixelSize, $color) = explode(",", $spec);
    $fontDir = dirname(__FILE__) . '/../3rdparty/pChart/fonts';
    return rgb($color, array_merge($dest, array("FontName" => "$fontDir/$filename", "FontSize" => $pixelSize)));
}
