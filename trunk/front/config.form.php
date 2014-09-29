<?php



// Définition de la variable GLPI_ROOT obligatoire pour l'instanciation des class
define('GLPI_ROOT', getAbsolutePath());
// Récupération du fichier includes de GLPI, permet l'accès au cœur
include (GLPI_ROOT."inc/includes.php");


$plugin = new Plugin();
if ($plugin->isActivated("reservation")) {
   $PluginReservationConfig = new PluginReservationConfig();
   Session::checkRight("config", "w");
   if (isset($_POST["week"])) {
      
      $PluginReservationConfig->setConfiguration($_POST["week"]);
      Html::back();
   } else {
      Html::header(PluginReservationReservation::getTypeName(2), '', "plugins", "Reservation");
      $PluginReservationConfig->showForm();
      Html::footer();
   }
} else {
   Html::header(__('Setup'), '', "config", "plugins");
   echo "<div class='center'><br><br>".
         "<img src=\"".$CFG_GLPI["root_doc"]."/pics/warning.png\" alt='warning'><br><br>";
   echo "<b>".__('Please activate the plugin','addressing')."</b></div>";
   Html::footer();
}

function getAbsolutePath()
    {return str_replace("plugins/reservation/front/config.form.php", "", $_SERVER['SCRIPT_FILENAME']);}

?>
