<?php

/**
 * This embeds the Google Analytics tracking code into your layouts.
 * Add the following code to your layouts and add your site ID under
 * Tools > Analytics.
 *
 * Usage:
 *
 *     {# analytics/code #}
 */

$settings = parse_ini_file ('conf/app.analytics.' . ELEFANT_ENV . '.php');
echo $tpl->render ('analytics/code', $settings);

?>