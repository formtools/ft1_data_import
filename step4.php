<?php

require_once("../../global/library.php");
ft_init_module_page();

$settings = ft_get_module_settings();

$page_vars = array();
$page_vars["step4_completed"] = false;
$page_vars["db_settings_confirmed"] = $settings["db_settings_confirmed"];

if ($page_vars["db_settings_confirmed"] == "no")
{
	$g_success = false;
	$g_message = $L["notify_please_confirm_db_settings"];
}


if (isset($_POST["continue"]))
{
	list($g_success, $g_message) = ft1_data_import_import_client_accounts();

	if ($g_success)
	  $page_vars["step4_completed"] = true;
}

ft_display_module_page("templates/step4.tpl", $page_vars);