=== Bitcoin Charts Widget ===
Tags: bitcoin, charts, widget, bitcoin-welt, blockchain.info, service
Requires at least: 3.5.0
Tested up to: 4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Stable tag: trunk
Plugin URI: http://www.bitcoin-welt.com/wordpress-plugin-bitcoin-charts-als-widget/
Author URI: http://www.both-interact.com

Displays bitcoin charts as widget with data from external services.

== Description ==

This widget displays bitcoin charts with data from different external services. 
Currently, the widget supports blockchain.info but can be extended easily.

As usual, you can adjust the widget's settings to your taste through the admin backend. 

Additionally, you can include the widget using shortcode.

= Short code =

The shortcode is [btcchart style="btc-500x330" chart="trade-volume"]

* chart is one of:
	* total-bitcoins
	* market-cap
	* transaction-fees
	* transaction-fees-usd
	* network-deficit
	* n-transactions
	* n-transactions-total
	* n-transactions-excluding-popular
	* n-unique-addresses
	* n-transactions-per-block
	* n-orphaned-blocks
	* output-volume
	* estimated-transaction-volume
	* estimated-transaction-volume-usd
	* trade-volume
	* market-price
	* cost-per-transaction-percent
	* cost-per-transaction
	* hash-rate
	* difficulty
	* miners-revenue
	* avg-confirmation-time
	* bitcoin-days-destroyed-cumulative
	* bitcoin-days-destroyed
	* bitcoin-days-destroyed-min-week
	* bitcoin-days-destroyed-min-month
	* bitcoin-days-destroyed-min-year
	* blocks-size
	* my-wallet-transaction-volume
	* my-wallet-n-users
	* my-wallet-n-tx

* style is one of (see styles.json for predefined ones)
	* default	
	* btc-162x150
	* btc-250x165
	* btc-500x330
	* btc-700x450
	* orange-162x150
	* orange-250x165
	* orange-500x330
	* orange-700x450
	* blue-162x150
	* blue-250x165
	* blue-500x330
	* blue-700x450
	* (162x150 indicates the width in pixel)

* there are the following additional styling options (overriding the style):
	* width: pixel
	* height: pixel
	* curve_color: #RRGGBB
	* background_color: #RRGGBB
	* background_gradient: Start:#RRGGBB,End:#RRGGBB
	* title_font: fontfile.ttf,height_in_pixel,#RRGGBB
	* default_font: fontfile.ttf,height_in_pixel,#RRGGBB
	* x-axis-label-rotation: degrees
	* x-axis-date-format: format string for date (M.Y or Y, etc.)
	* graph-area: left,top,right,bottom (all in pixel)
	* label: Data curve legend
	
        #RRGGBB is a hexadecimal color code red, green, blue from 00 to FF

== Installation ==

In order to install and operate this widget the following requirements must be met.

= Requirements: =
  * PHP 5.4+
  * cUrl
  * WordPress 3.5+
  * pChart2.1.4 (included in 3rdparty folder)

== Changelog ==

= 0.3.6 =

+ compatibel to offical Wordpress plugin repository

= 0.3.5 =

* fixed labels

= 0.3.0 =

* "title" option is now static text

= 0.2.0 =

* added options to configuration

= 0.1.0 =

initial release

== Frequently Asked Questions ==

= The plugin does not have a valid header = 

If you receive this error message when trying to activate the plugin directly 
after the installation please navigate back to the plugin list page and activate 
the plugin from there.

== Upgrade Notice ==

Currently we don't have upgrade notices for you.

== Screenshots ==

For screenshots please have a look at the official website (http://www.bitcoin-welt.com/)
