<?php

/**
 * Module file: Form Tools 1 Data Import
 */

$MODULE["author"]          = "Encore Web Studios";
$MODULE["author_email"]    = "formtools@encorewebstudios.com";
$MODULE["author_link"]     = "http://modules.formtools.org";
$MODULE["version"]         = "1.0.1";
$MODULE["date"]            = "2010-10-05";
$MODULE["origin_language"] = "en_us";
$MODULE["supports_ft_versions"] = "2.0.0";

// define the module navigation - the keys are keys defined in the language file. This lets
// the navigation - like everything else - be customized to the users language
$MODULE["nav"] = array(
  "word_about" => array("index.php", false),
  "phrase_form_tools1_db_settings" => array("settings.php", false),
  "phrase_import_data" => array("import.php", false),
  "phrase_purge_data" => array("purge.php", false)
    );