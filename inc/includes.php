<?php

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

$toolTipConfig = ["tooltip", "comment", "location", "serial", "inventory", "group", "man_model", "status"];
$tabConfig = ["tabmine", "tabcurrent", "tabcoming"];


function logIfDebug($message = '', $data = '')
{
    if (!empty($_SESSION['glpi_use_mode'])) {
        $__msg = $message . " : " . json_encode($data) . "\n";
        if (class_exists('Toolbox') && method_exists('Toolbox', 'logInFile')) {
            Toolbox::logInFile('reservations_plugin', $__msg, $force = false);
        } else {
            error_log('[reservations_plugin] ' . $__msg);
        }
    }
}
