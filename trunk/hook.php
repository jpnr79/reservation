<?php


function plugin_reservation_install() {
  global $DB;
  if (!TableExists("glpi_plugin_reservation_manageresa")) {
    $query = "CREATE TABLE `glpi_plugin_reservation_manageresa` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `resaid` int(11) NOT NULL,
      `date_return` datetime,
      `date_theorique` datetime NOT NULL,
      PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"; 

	$DB->queryOrDie($query, $DB->error());
  }

    if (!TableExists("glpi_plugin_reservation_config")) 
        {
        // Création de la table config
        $query = "CREATE TABLE `glpi_plugin_reservation_config` (
        `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
        `jour` char(32) NOT NULL default '',
        `actif` int(1) NOT NULL default '1'
        )ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $DB->query($query) or die($DB->error());

         $query = "INSERT INTO `glpi_plugin_reservation_config` (`jour` , `actif`)
                VALUES (\"lundi\",1),
                       (\"mardi\",1),
                       (\"mercredi\",1),
                       (\"jeudi\",1),
                       (\"vendredi\",1),
                       (\"samedi\",0),
                       (\"dimanche\",0)";
                       
      $DB->queryOrDie($query) or die($DB->error());
        }


  $cron = new CronTask;
  if (!$cron->getFromDBbyName('PluginReservationTask','SurveilleResa'))
  {
    CronTask::Register('PluginReservationTask', 'SurveilleResa', 5*MINUTE_TIMESTAMP,array('param' => 24, 'mode' => 2, 'logs_lifetime'=> 10));
  }

  if (!$cron->getFromDBbyName('PluginReservationTask','MailUserDelayedResa'))
  {
    CronTask::Register('PluginReservationTask', 'MailUserDelayedResa', DAY_TIMESTAMP,array('hourmin' => 23, 'hourmax' => 24,  'mode' => 2, 'logs_lifetime'=> 30));

  }




  return true;
}

function plugin_reservation_uninstall() {
  global $DB;
  $tables = array("glpi_plugin_reservation_manageresa","glpi_plugin_reservation_config");
  foreach($tables as $table) 
  {$DB->query("DROP TABLE IF EXISTS `$table`;");}
  return true;
}



?>

