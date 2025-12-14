<?php

/**
 * Install hook
 *
 * @return boolean
 */
function plugin_reservation_install()
{
    $migration = new Migration(100);
    $migration->executeMigration();
    return true;
}

/**
 * Uninstall hook
 *
 * @return boolean
 */
function plugin_reservation_uninstall()
{
    global $DB;
    $tables = ["glpi_plugin_reservation_reservations", "glpi_plugin_reservation_configs", "glpi_plugin_reservation_categories", "glpi_plugin_reservation_categories_items"];
    foreach ($tables as $table) {
        $DB->query("DROP TABLE IF EXISTS `$table`");
    }
    CronTask::unregister("Reservation");
    return true;
}

/**
 * hook : add Plugin reservation when a GLPI reservation is added
 *
 * @return void
 */
function plugin_item_add_reservation($reservation)
{
    global $DB;

    $DB->insertOrDie('glpi_plugin_reservation_reservations', [
        'reservations_id' => $reservation->fields['id'],
        'baselinedate' => $reservation->fields['end'],
    ]);
    Toolbox::logInFile('reservations_plugin', "plugin_item_add_reservation : " . json_encode($reservation) . "\n", $force = false);

    $config = new PluginReservationConfig();
    if ($config->getConfigurationValue("checkin", 0) == 1 && $config->getConfigurationValue("auto_checkin", 0) == 1) {
        $time = time();
        $until_auto_checkin = $config->getConfigurationValue("auto_checkin_time", 1) * MINUTE_TIMESTAMP;
        $until = date("Y-m-d H:i:s", $time + $until_auto_checkin);
        if ($reservation->fields['begin'] <= $until) {
            Toolbox::logInFile('reservations_plugin', "auto-checkin enable : reservation " . json_encode($reservation->fields['id']) . " is checkin automatically\n", $force = false);
            PluginReservationReservation::checkinReservation($reservation->fields['id']);
        }
    }
}

/**
 * hook : update plugin reservation when a GLPI reservation is updated
 *
 * @return void
 */
function plugin_item_update_reservation($reservation)
{
    global $DB;

    $end = $reservation->fields['end'];

    $query = 'SELECT `effectivedate`
            FROM glpi_plugin_reservation_reservations
            WHERE `reservations_id` = ' . $reservation->fields['id'];
    // maybe the reservation is over
    $resume = false;
    foreach ($DB->request($query) as $data) {
        if ($end >= $data['effectivedate']) {
            $resume = true;
        }
    }

    if ($resume) {
        $DB->updateOrDie(
            'glpi_plugin_reservation_reservations',
            [
                'baselinedate' => $end,
                'effectivedate' => 'NULL',
            ],
            [
                'reservations_id' => $reservation->fields["id"],
            ]
        );
    } else {
        $DB->updateOrDie(
            'glpi_plugin_reservation_reservations',
            [
                'baselinedate' => $end,
            ],
            [
                'reservations_id' => $reservation->fields["id"],
            ]
        );
    }
    Toolbox::logInFile('reservations_plugin', "plugin_item_update_reservation : " . json_encode($reservation) . "\n", $force = false);
}

/**
 * hook : delete Plugin reservation when a GLPI reservation is delete
 *
 * @return void
 */
function plugin_item_purge_reservation($reservation)
{
    global $DB;
    Toolbox::logInFile('reservations_plugin', "plugin_item_purge_reservation : " . json_encode($reservation) . "\n", $force = false);
    $DB->delete(
        'glpi_plugin_reservation_reservations',
        [
            'reservations_id' => $reservation->fields["id"],
        ]
    );
}
