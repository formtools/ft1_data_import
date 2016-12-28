<?php


/**
 * This attempts to make contact with the Form Tools 1 database using the DB information supplied by the
 * user. If it manages to connect, it tries to locate the settings table and check the program_version
 * setting. If the setting says the FT version is 1.5.0 or 1.5.1, all is well - the info is stored in
 * sessions and it notifies the user before continuing to the next step.
 *
 * Once everything checks out, it updates the db_settings_confirmed setting.
 *
 * @param array $info
 */
function ft1_data_import_check_db_settings($info)
{
  global $LANG, $g_db_name, $L;

  // first, update the latest settings
  $settings = array(
    "db_hostname" => $info["db_hostname"],
    "db_name" => $info["db_name"],
    "db_username" => $info["db_username"],
    "db_password" => $info["db_password"],
    "table_prefix" => $info["table_prefix"]
  );
  ft_set_module_settings($settings);


  // now set about checking the values
  $db_connection_error = "";
  $db_select_error     = "";

  $hostname = $info["db_hostname"];
  $db_name  = $info["db_name"];
  $username = $info["db_username"];
  $password = $info["db_password"];
  $table_prefix = $info["table_prefix"];

  $tmp_link = @mysql_connect($hostname, $username, $password, true) or $db_connection_error = mysql_error();

  if ($db_connection_error)
  {
    $placeholders = array("db_connection_error" => $db_connection_error);
    $error = ft_eval_smarty_string($LANG["notify_install_invalid_db_info"], $placeholders);
    return array(false, $error);
  }
  else if (!$tmp_link)
  {
    $error = $L["notify_db_login_info_incorrect"];
    return array(false, $error);
  }
  else
  {
    mysql_select_db($db_name)
      or $db_select_error = mysql_error();

    if ($db_select_error)
    {
      $placeholders = array("db_select_error" => $db_select_error);
      $error = ft_eval_smarty_string($LANG["notify_install_no_db_connection"], $placeholders);
      return array(false, $error);
    }
    else
    {
      // so far so good! Now let's see if the {prefix}settings database table exists with a valid
      // Form Tools version
      $result = mysql_query("SELECT * FROM {$table_prefix}settings WHERE setting_name = 'program_version'");

      if (!$result)
      {
        // close this connection and reconnect
        @mysql_close($tmp_link);
        ft_db_connect();
        mysql_select_db($g_db_name);
        return array(false, $L["notify_settings_table_not_found"]);
      }

      $data = mysql_fetch_assoc($result);

      if (!isset($data["setting_value"]) || $data["setting_value"] != "1.5.0" && $data["setting_value"] != "1.5.1")
      {
        @mysql_close($tmp_link);
        ft_db_connect();
        mysql_select_db($g_db_name);
        return array(false, $L["notify_invalid_ft1_version"]);
      }
    }
  }

  @mysql_close($tmp_link);
  ft_db_connect();
  mysql_select_db($g_db_name);

  ft_set_module_settings(array("db_settings_confirmed" => "yes"));

  return array(true, $L["notify_ft1_db_connected"]);
}


/**
 * Creates new database tables and all related data (form field options, etc) based on the forms found
 * in the Form Tools 1 database. Exciting stuff!
 *
 * @return array [0] boolean [1] message
 */
function ft1_data_import_import_forms()
{
  global $g_table_prefix, $L;

  // potentially this operation can take a long time. Max it out at 10 minutes, though.
  @set_time_limit(600);

  $settings = ft_get_module_settings();

  $ft1_table_prefix = $settings["table_prefix"];

  $tmp_link = ft1_data_import_db_connect();

  // get all old form information
  $query = mysql_query("
    SELECT *
    FROM   {$ft1_table_prefix}forms
    ORDER BY form_id
      ") or die(mysql_error());

  $forms = array();
  while ($row = mysql_fetch_assoc($query))
  {
    $form_id = $row["form_id"];

    $templates_query = mysql_query("
      SELECT *
      FROM   {$ft1_table_prefix}form_templates
      WHERE  form_id = $form_id
      ORDER BY list_order
        ");

    $template_info = array();
    while ($row2 = mysql_fetch_assoc($templates_query))
    {
      $field_id = $row2["field_id"];

      $field_options_query = mysql_query("
        SELECT *
        FROM   {$ft1_table_prefix}field_options
        WHERE  field_id = $field_id
          ");

      $field_option_info = array();
      while ($row3 = mysql_fetch_assoc($field_options_query))
        $field_option_info[] = $row3;

      $row2["field_options"] = $field_option_info;

      $template_info[] = $row2;
    }

    $row["template_info"] = $template_info;
    $forms[] = $row;
  }

  ft1_data_import_db_disconnect($tmp_link);


  // in case we can't use the same form IDs, keep a map of what form IDs have changed
  $form_id_mapping = array();

  foreach ($forms as $form_info)
  {
    // STEP 1: import the form to the forms table
    $form_id         = $form_info["form_id"];
    $access_type     = "private";
    $submission_type = $form_info["form_type"];
    $date_created    = $form_info["date_created"];
    $is_active       = $form_info["is_active"];
    $is_initialized  = $form_info["is_initialized"];
    $is_complete     = $form_info["is_complete"];
    $is_multi_page_form = "no";
    $form_name       = mysql_real_escape_string($form_info["form_name"]);
    $form_url        = $form_info["form_url"];
    $redirect_url    = $form_info["redirect_url"];
    $user_email_field      = $form_info["user_email_field"];
    $user_first_name_field = $form_info["user_first_name_field"];
    $user_last_name_field  = $form_info["user_last_name_field"];
    $auto_delete_submission_files = $form_info["auto_delete_submission_files"];
    $submission_strip_tags = $form_info["submission_strip_tags"];

    // first, try to add the form with the same form ID as the original form. This may or may not work, depending
    // on whether the Form Tools 2 database is empty or not.
    $result = @mysql_query("
      INSERT INTO {$g_table_prefix}forms (form_id, access_type, submission_type, date_created, is_active,
          is_initialized, is_complete, is_multi_page_form, form_name, form_url, redirect_url,
          auto_delete_submission_files, submission_strip_tags, edit_submission_page_label)
      VALUES ($form_id, '$access_type', '$submission_type', '$date_created', '$is_active',
          '$is_initialized', '$is_complete', '$is_multi_page_form', '$form_name', '$form_url', '$redirect_url',
          '$auto_delete_submission_files', '$submission_strip_tags', 'Edit Submission')
        ");

    // no such luck! This is probably due to the form ID being already taken. Try again, this time using whatever
    // form ID is available
    if (!$result)
    {
      $result2 = @mysql_query("
        INSERT INTO {$g_table_prefix}forms (access_type, submission_type, date_created, is_active,
            is_initialized, is_complete, is_multi_page_form, form_name, form_url, redirect_url,
            auto_delete_submission_files, submission_strip_tags, edit_submission_page_label)
        VALUES ('$access_type', '$submission_type', '$date_created', '$is_active',
            '$is_initialized', '$is_complete', '$is_multi_page_form', '$form_name', '$form_url', '$redirect_url',
            '$auto_delete_submission_files', '$submission_strip_tags', 'Edit Submission')
          ");

      if (!$result2)
        return array(false, $L["notify_form_import_failure"] . "<i>" . mysql_error() . "</i>");
      else
        $form_id_mapping[$form_id] = mysql_insert_id();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // *** From here until the rest of the function, any time a database query fails - for whatever reason - the
    //     ft1_data_import_rollback_form() function is called to clean up the dud form data. ***
    // -----------------------------------------------------------------------------------------------------------------

    if (!isset($form_id_mapping[$form_id]))
      $form_id_mapping[$form_id] = $form_id;

    $ft2_form_id = $form_id_mapping[$form_id];

    // STEP 2: Add the form field information
    foreach ($form_info["template_info"] as $form_field)
    {
      $field_name       = $form_field["field_name"];
      $field_test_value = mysql_real_escape_string($form_field["field_test_value"]);
      $field_size    	  = $form_field["field_size"];
      $field_type       = $form_field["field_type"];

      // in case $field_type is "other", we need to figure out whether it should be a textbox or textarea. For this,
      // decide based on the field_size. Anything over Medium is a textarea.
      if ($field_type == "other")
      {
        $field_type = "textbox";

        if ($field_size == "large" || $field_size == "very_large")
          $field_type = "textarea";
      }

      $data_type   = $form_field["data_type"];
      $field_title = mysql_real_escape_string($form_field["field_title"]);
      $col_name    = $form_field["col_name"];
      $list_order  = $form_field["list_order"];
      $include_on_redirect = $form_field["include_on_redirect"];

      $result = @mysql_query("
        INSERT INTO {$g_table_prefix}form_fields (form_id, field_name, field_test_value, field_size, field_type, data_type,
            field_title, col_name, list_order, include_on_redirect)
        VALUES ($ft2_form_id, '$field_name', '$field_test_value', '$field_size', '$field_type', '$data_type',
            '$field_title', '$col_name', '$list_order', '$include_on_redirect')
      ");

      if (!$result)
      {
        ft1_data_import_rollback_form($ft2_form_id);
        return array(false, $L["notify_field_option_import_failure"] . "<i>" . mysql_error() . "</i>");
      }

      $field_id = mysql_insert_id();

      // now, if the field had them, add the field options. For this, we use the handy ft_create_unique_option_group function()
      // which does the dirty work of figuring out whether an existing group contained the same list of options
      if (!empty($form_field["field_options"]))
      {
        // Form Tools 1 ALWAYS set this value, even if it's not needed for the field
        $option_group_info = array();
        $option_group_info["orientation"] = $form_field["option_orientation"];

        // for the group name, just use the field title. This value is NOT checked when comparing this option
        // group list to others, so we're okay
        $option_group_info["group_name"]  = mysql_real_escape_string($form_field["field_title"]);

        // convert the old Form Tools 1 options into something understood by ft_create_unique_option_group()
        $option_group_info["options"] = array();
        foreach ($form_field["field_options"] as $option_info)
        {
          $option_group_info["options"][] = array(
            "value" => ft_sanitize($option_info["option_value"]),
            "text"  => ft_sanitize($option_info["option_name"])
          );
        }

        $field_group_id = ft_create_unique_option_group($ft2_form_id, $option_group_info);

        if ($field_group_id)
        {
          $group_id_qry = @mysql_query("
            UPDATE {$g_table_prefix}form_fields
            SET    field_group_id = $field_group_id
            WHERE  field_id = $field_id
              ");

          if (!$group_id_qry)
          {
            ft1_data_import_rollback_form($ft2_form_id);
            return array(false, $L["notify_failure_adding_import_group"] . "<i>" . mysql_error() . "</i>");
          }
        }
      }
    }

    // finally, add the new "Last Modified" field. Just tack it on the end, they can re-sort it at their leisure
    $next_list_order = count($form_info["template_info"]) + 1;
    $result = @mysql_query("
      INSERT INTO {$g_table_prefix}form_fields (form_id, field_name, field_test_value, field_size, field_type, data_type,
         field_title, col_name, list_order, include_on_redirect)
      VALUES ($ft2_form_id, 'Last Modified', '', 'small', 'system', 'date', 'Last Modified', 'last_modified_date',
        '$next_list_order', 'no')
    ");

    if (!$result)
    {
      ft1_data_import_rollback_form($ft2_form_id);
      return array(false, $L["notify_failure_adding_last_modified_date"] . "<i>" . mysql_error() . "</i>");
    }


    // STEP 3: create the actual form table. This does the job of automatically creating the default View
    ft_finalize_form($ft2_form_id);
  }

  // store the form ID mappings for use in the later steps
  $_SESSION["ft"]["ft1_data_import"]["form_id_mapping"] = $form_id_mapping;

  return array(true, $L["notify_form_settings_added"]);
}


/**
 * This function is called during the import form settings step. In case of error, this function is called
 * to clean up any database additions that were just made for a form.
 *
 * @param integer $form_id
 */
function ft1_data_import_rollback_form($form_id)
{
  global $g_table_prefix;

  @mysql_query("DELETE FROM {$g_table_prefix}forms WHERE form_id = $form_id");
  @mysql_query("DELETE FROM {$g_table_prefix}form_fields WHERE form_id = $form_id");

  // hmmmm. what about field options? They get orphaned...
  @mysql_query("DELETE FROM {$g_table_prefix}field_option_groups WHERE original_form_id = $form_id");
}


/**
 * This function does all the dirty work of retrieving the Form Tools 1 form information & settings and porting
 * them over to Form Tools 2.
 *
 * @return array [0] success/failure (T/F), [1] message
 */
function ft1_data_import_import_form_data()
{
  global $g_table_prefix, $L;

  $form_id_mappings = $_SESSION["ft"]["ft1_data_import"]["form_id_mapping"];
  $original_form_ids = array_keys($form_id_mappings);
  $settings = ft_get_module_settings();
  $ft1_table_prefix = $settings["table_prefix"];

  foreach ($original_form_ids as $original_form_id)
  {
    $tmp_link = ft1_data_import_db_connect();

    $query = @mysql_query("
      SELECT *
      FROM   {$ft1_table_prefix}form_{$original_form_id}
      ORDER BY submission_date ASC
        ") or die(mysql_error());

    ft1_data_import_db_disconnect($tmp_link);

    if (!$query)
      return array(false, $L["notify_failure_adding_form_data"] . "<i>" . mysql_error() . "</i>");

    // now add the submissions
    $new_form_id = $form_id_mappings[$original_form_id];
    while ($row = mysql_fetch_assoc($query))
    {
      $insert_rows = array();
      $insert_values = array();
      $submission_date = "";
      while (list($col_name, $value) = each($row))
      {
        $insert_rows[] = $col_name;
        $insert_values[] = "'" . mysql_real_escape_string($value) . "'";

        if ($col_name == "submission_date")
          $submission_date = $value;
      }
      $insert_rows[]   = "last_modified_date";
      $insert_values[] = "'$submission_date'";

      $insert_rows_str = join(", ", $insert_rows);
      $insert_values_str = join(", ", $insert_values);

      $query2 = mysql_query("INSERT INTO {$g_table_prefix}form_{$new_form_id} ($insert_rows_str) VALUES ($insert_values_str)");

      if (!$query2)
         return array(false, $L["notify_failure_adding_form_submission"] . "<i>" . mysql_error() . "</i>");
    }
  }

  return array(true, $L["notify_form_submissions_added"]);
}


/**
 * This function is usable after step 1 has been successfully passed. Step 1 stores the valid database
 * values in sessions, used by this function to connect to the old database.
 *
 * @return database connection
 */
function ft1_data_import_db_connect()
{
  $settings = ft_get_module_settings();

  $ft1_hostname = $settings["db_hostname"];
  $ft1_db_name  = $settings["db_name"];
  $ft1_username = $settings["db_username"];
  $ft1_password = $settings["db_password"];

  $tmp_link = mysql_connect($ft1_hostname, $ft1_username, $ft1_password, true)
    or die(mysql_error());

  mysql_select_db($ft1_db_name);

	// *should* be okay for all scripts - even non-UTF8
	mysql_query("SET NAMES 'utf8'", $tmp_link);

  return $tmp_link;
}


/**
 * Disconnects from the Form Tools 1 database and reconnects to Form Tools 2.
 *
 * @param unknown_type $link
 */
function ft1_data_import_db_disconnect($link)
{
  global $g_db_name;

  @mysql_close($tmp_link);
  ft_db_connect();
  mysql_select_db($g_db_name);
}


function ft1_data_import__install($module_id)
{
  global $g_table_prefix, $LANG;

  $queries = array();
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('db_hostname', '', 'ft1_data_import')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('db_name', '', 'ft1_data_import')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('db_username', '', 'ft1_data_import')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('db_password', '', 'ft1_data_import')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('table_prefix', '', 'ft1_data_import')";
  $queries[] = "INSERT INTO {$g_table_prefix}settings (setting_name, setting_value, module) VALUES ('db_settings_confirmed', 'no', 'ft1_data_import')";

  $has_problem = false;
  foreach ($queries as $query)
  {
    $result = @mysql_query($query);
    if (!$result)
    {
      $has_problem = true;
      break;
    }
  }

  // if there was a problem, remove all the table and return an error
  $success = true;
  $message = "";
  if ($has_problem)
  {
    $success = false;
    $mysql_error = mysql_error();
    $message     = ft_eval_smarty_string($LANG["ft1_data_import"]["notify_problem_installing"], array("error" => $mysql_error));
  }

  return array($success, $message);
}


function ft1_data_import__uninstall($module_id)
{
  global $g_table_prefix;
  mysql_query("DELETE FROM {$g_table_prefix}settings WHERE module = 'ft1_data_import'");

  return array(true, "");
}


function ft1_data_import_purge_database()
{
  global $g_table_prefix, $L;

  @mysql_query("DELETE FROM {$g_table_prefix}accounts WHERE account_id != 1");
  @mysql_query("DELETE FROM {$g_table_prefix}account_settings WHERE account_id != 1");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}client_forms");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}email_templates");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}email_template_edit_submission_views");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}email_template_recipients");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}field_options");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}field_option_groups");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}field_settings");

  // get all forms IDs so we know which custom form tables to delete
  $forms = ft_get_forms();
  foreach ($forms as $form_info)
  {
    $form_id = $form_info["form_id"];

    // this may or may not exist, depending on whether it's completed or an error occurred during creation. Just
    // suppress all error messages
    @mysql_query("DROP TABLE {$g_table_prefix}form_{$form_id}");
  }

  @mysql_query("TRUNCATE TABLE {$g_table_prefix}forms");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}form_fields");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}multi_page_form_urls");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}public_form_omit_list");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}public_view_omit_list");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}views");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}view_fields");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}view_filters");
  @mysql_query("TRUNCATE TABLE {$g_table_prefix}view_tabs");

  _ft_cache_form_stats();

  return array(true, $L["notify_purge_complete"]);
}


/**
 * Imports all old client accounts and associates them with the forms that were added in the
 * current session.
 *
 * @return array [0] boolean [1] message
 */
function ft1_data_import_import_client_accounts()
{
  global $g_table_prefix, $L;

  // potentially this operation can take a long time. Max it out at 10 minutes, though.
  @set_time_limit(600);

  $module_settings = ft_get_module_settings();

  $ft1_table_prefix = $module_settings["table_prefix"];
  $tmp_link = ft1_data_import_db_connect();

  // get all old client information
  $query = mysql_query("
    SELECT *
    FROM   {$ft1_table_prefix}user_accounts
    WHERE  account_type = 'client'
      ") or die(mysql_error());

  $client_forms_query = mysql_query("
    SELECT *
    FROM   {$ft1_table_prefix}client_forms
      ") or die(mysql_error());

  $client_forms = array();
  while ($row = mysql_fetch_assoc($client_forms_query))
  {
    $account_id = $row["user_id"];
    $form_id = $row["form_id"];
    if (!isset($client_forms[$account_id]))
      $client_forms[$account_id] = array();

    $client_forms[$account_id][] = $form_id;
  }

  ft1_data_import_db_disconnect($tmp_link);


  $form_id_mappings = $_SESSION["ft"]["ft1_data_import"]["form_id_mapping"];

  $client_id_mappings = array();
  while ($row = mysql_fetch_assoc($query))
  {
    // STEP 1: add the new client account. Just in case, we give first name, last name
    // and email a single space IF they are empty. FT2 requires a value for those
    // fields, unlike FT1. The user will have to enter real values for them later
    // when they edit the client account
    $account_id           = $row["user_id"];
    $info["first_name"]   = !empty($row["first_name"]) ? $row["first_name"] : " ";
    $info["last_name"]    = !empty($row["last_name"]) ? $row["last_name"] : " ";
    $info["email"]        = !empty($row["email"]) ? $row["email"] : " ";
    $info["username"]     = $row["username"];
    $info["password"]     = $row["password"];
    $info["password_2"]   = $row["password"];

    list($success, $message, $new_user_id) = ft_add_client($info);

    if (!$success)
      return array(false, $L["notify_problem_adding_client"] . "<i>$message</i>");


    // now set the custom values
    $client_id_mappings[$account_id] = $new_user_id;

    $account_status = ($row["active"] == "yes") ? "active" : "disabled";

    @mysql_query("
      UPDATE {$g_table_prefix}accounts
      SET    account_status = '$account_status'
      WHERE  account_id = $new_user_id
    ");

    $account_settings = array(
      "company_name"         => mysql_real_escape_string($row["company"]),
      "page_titles"          => mysql_real_escape_string($row["page_titles"]),
      "footer_text"          => mysql_real_escape_string($row["footer_text"])
    );
    ft_set_account_settings($new_user_id, $account_settings);

    // STEP 2: add the client-form mappings
    if (isset($client_forms[$account_id]) && is_array($client_forms[$account_id]))
    {
      foreach ($client_forms[$account_id] as $original_form_id)
      {
        // there was a bug in FT1 where old client-form mappings weren't always deleted. This ensures the mapping is valid
        if (isset($form_id_mappings[$original_form_id]))
        {
          $new_form_id = $form_id_mappings[$original_form_id];

          $result = mysql_query("
            INSERT INTO {$g_table_prefix}client_forms (account_id, form_id) VALUES ($new_user_id, $new_form_id)
          ");
        }
      }
    }
  }

  return array(true, $L["notify_clients_added"]);
}


function ft1_data_import_update_file_upload_settings()
{
  global $g_table_prefix, $L;

  // find all file fields and their forms in Form Tools 1 installation
  $module_settings = ft_get_module_settings();
  $ft1_table_prefix = $module_settings["table_prefix"];
  $tmp_link = ft1_data_import_db_connect();

  $query = mysql_query("
    SELECT *
    FROM   {$ft1_table_prefix}form_templates
    WHERE  field_type = 'file'
      ");

  $file_fields = array();
  while ($row = mysql_fetch_assoc($query))
    $file_fields[] = $row;

  ft1_data_import_db_disconnect($tmp_link);


  // now update the file upload settings in Form Tools 2
  $form_id_mappings = $_SESSION["ft"]["ft1_data_import"]["form_id_mapping"];
  foreach ($file_fields as $file_field_info)
  {
    $form_id         = $file_field_info["form_id"];
    $file_upload_dir = $file_field_info["file_upload_dir"];
    $file_upload_url = $file_field_info["file_upload_url"];
    $file_upload_max_size = $file_field_info["file_upload_max_size"];
    $file_upload_types    = $file_field_info["file_upload_types"];

    $new_form_id = $form_id_mappings[$form_id];

    // find the new Field ID for this field
    $field_info = ft_get_form_field_by_colname($form_id, $file_field_info["col_name"]);
    $field_id = $field_info["field_id"];

    // add the settings
    mysql_query("
      INSERT INTO {$g_table_prefix}field_settings (field_id, setting_name, setting_value, module)
      VALUES ($field_id, 'file_upload_dir', '$file_upload_dir', 'core')
    ");
    mysql_query("
      INSERT INTO {$g_table_prefix}field_settings (field_id, setting_name, setting_value, module)
      VALUES ($field_id, 'file_upload_url', '$file_upload_url', 'core')
    ");

/*
    // don't include the file size setting. The mapping too weird.
    mysql_query("
      INSERT INTO {$g_table_prefix}field_settings (field_id, setting_name, setting_value, module)
      VALUES ($field_id, 'file_upload_max_size', '$file_upload_max_size', 'core')
    ");
*/

    // note this field has been renamed
    mysql_query("
      INSERT INTO {$g_table_prefix}field_settings (field_id, setting_name, setting_value, module)
      VALUES ($field_id, 'file_upload_filetypes', '$file_upload_types', 'core')
    ");
  }

  if (empty($file_fields))
    return array(true, $L["notify_no_file_fields"]);
  else
    return array(true, $L["notify_file_fields_updated"]);
}


