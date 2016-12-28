<?php

require_once("../../global/library.php");
ft_init_module_page();

$form_id_mapping = $_SESSION["ft"]["ft1_data_import"]["form_id_mapping"];
$form_id_mapping_is_same = true;
while (list($original_form_id, $new_form_id) = each($form_id_mapping))
{
	if ($original_form_id != $new_form_id)
	{
		$form_id_mapping_is_same = false;
		break;
	}
}

$page_vars = array();
$page_vars["form_id_mapping_is_same"] = $form_id_mapping_is_same;
$page_vars["form_id_mapping"] = $form_id_mapping;

ft_display_module_page("templates/complete.tpl", $page_vars);