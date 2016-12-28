<?php

require_once("../../global/library.php");
ft_init_module_page();

if (isset($_POST["delete_data"]))
	list($g_success, $g_message) = ft1_data_import_purge_database();

$page_vars = array();
$page_vars["head_js"] =<<<EOF
var rules = [];
rules.push("required,confirmation,{$L["confirm_purge"]}");
EOF;

ft_display_module_page("templates/purge.tpl", $page_vars);