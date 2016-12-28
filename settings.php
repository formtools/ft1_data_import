<?php

require_once("../../global/library.php");
ft_init_module_page();

$page_vars = array();
$page_vars["step1_completed"] = false;

if (isset($_POST["check_settings"]))
	list($g_success, $g_message) = ft1_data_import_check_db_settings($_POST);

$page_vars["module_settings"] = ft_get_module_settings();
$page_vars["head_js"] =<<< EOF

var rules = [];
rules.push("required,db_hostname,{$L["validation_no_db_hostname"]}");
rules.push("required,db_name,{$L["validation_no_db_name"]}");
rules.push("required,db_username,{$L["validation_no_db_username"]}");
EOF;

ft_display_module_page("templates/settings.tpl", $page_vars);