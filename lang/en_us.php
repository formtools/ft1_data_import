<?php

/*
Form Tools - Module Language File
---------------------------------

File created: Oct 24th, 2:46 AM

If you would like to help translate this module, please visit:
http://www.formtools.org/
*/


$L = array();

// required
$L["module_name"] = "Form Tools 1 Data Import";
$L["module_description"] = "This module lets you import your old Form Tools 1.5.x data and settings into Form Tools 2.";

$L["word_about"] = "About";

$L["phrase_import_data"] = "Import Data";
$L["phrase_form_tools1_db_settings"] = "Form Tools 1 DB Settings";
$L["phrase_check_settings"] = "Check Settings";
$L["phrase_purge_data"] = "Purge Data";
$L["phrase_import_complete"] = "Data Import Complete";
$L["phrase_original_form_id"] = "Original Form ID";
$L["phrase_new_form_id"] = "New Form ID";
$L["phrase_import_form_data"] = "Import Form Data &raquo;";
$L["phrase_import_form_data_nav"] = "Import Form Data";
$L["phrase_import_file_upload_settings"] = "Import File Upload Settings &raquo;";
$L["phrase_import_client_accounts"] = "Import Client Accounts &raquo;";
$L["phrase_delete_data"] = "Delete Data";
$L["phrase_file_upload_settings"] = "File Upload Settings";
$L["phrase_import_form_settings_rightarrow"] = "Import Form Settings &raquo;";
$L["phrase_import_form_settings"] = "Import Form Settings";
$L["phrase_import_client_accounts_nav"] = "Import Client Accounts";


$L["text_import_complete"] = "The data import is complete!";
$L["text_complete_form_id_mapping_is_same"] = "The form IDs of your forms in Form Tools 2 are the same as in Form Tools 1: this is good! Less to configure later!";
$L["text_complete_form_id_mapping_not_same"] = "The form IDs of your forms have changed. The following table lists the ID changes. Please store this information! You will need it to update your forms when you want to update them to store their submissions in Form Tools 2.";
$L["text_final_remarks"] = "Now, all that remains is for you to update your forms to point them to Form Tools 2. Please refer to the <a href=\"http://modules.formtools.org/ft1_data_import/\">help documentation</a> for more information.";
$L["text_import_summary"] = "This section imports your old Form Tools 1 data sequentially to ensure that the original data mappings (e.g. client-form associations) are retained. Be sure to complete all the steps in one sitting. It shouldn't take too long!";
$L["text_import_submissions_summary"] = "Now that your form tables have been created, this step imports all the form submissions. Again, depending on the number and size of your forms and the volume of submissions, this could take some time.";
$L["text_import_file_settings"] = "This step configures any file upload fields in Form Tools 2 to point to the same folder specified by Form Tools 1. This does NOT move the files, just ensures that the linkage is correct.";
$L["text_import_clients"] = "This step imports your old client accounts into the new database and associates them with the appropriate forms.";
$L["text_ft1_db_settings_summary"] = "Use the form below to enter your Form Tools 1.5.x database settings. Note: these values are stored permanently in the Form Tools 2 database, so if you're concerned about security, you'll want to uninstall this module when you have finished importing your data.";
$L["text_confirm_purge"] = "Yes, I'm totally serious. Delete ALL form and client data in the Form Tools 2 database. I know this can't be undone.";
$L["text_purge_para1"] = "The button below deletes all <b>form</b> and <b>client account</b> data in your Form Tools 2 database. So be very, very careful! This functionality is included for the following reasons:";
$L["text_purge_para2"] = "To ensure the imported forms contain the same form IDs, you may need to have a fresh database - this script won't be able to give your forms the same IDs if they're already taken by forms already added to Form Tools 2.";
$L["text_purge_para3"] = "In case anything goes wrong with the upgrade script. this option gives you a way to empty the FT2 database for a clean start.";
$L["text_purge_para4"] = "It lets people \"test drive\" the Form Tools 2 script before deciding they want to upgrade. This lets them dump all of their tests before importing their Form Tools 1 data.";
$L["text_purge_para5"] = "Note: this module does NOT remove files. This is because when the module runs, files uploaded through Form Tools 1 are <b>linked</b> to in Form Tools 2. Deleting them may accidentally remove files that you want to keep.";
$L["text_form_import_summary"] = "This step import your old form configurations into Form Tools 2. Depending on the number and size of your forms, this may take some time.";

$L["notify_problem_installing"] = "There following error occurred when trying to add the database data for this module: <b>{\$error}</b>";
$L["notify_please_confirm_db_settings"] = "Before importing the data, you must first <a href=\"settings.php\">supply and confirm the Form Tools 1 database settings</a>.";
$L["notify_db_login_info_incorrect"] = "We were able to connect to the database, but it appears the username / password information is incorrect.";
$L["notify_settings_table_not_found"] = "The database information appears to be correct, but we couldn't find a table called <b>{\$table_prefix}settings</b>. Please check the table prefix setting.";
$L["notify_invalid_ft1_version"] = "The database information you supplied is correct, but the Form Tools version you're attempting to connect to is not version 1.5.0 or 1.5.1. This installation script will not work with earlier versions of Form Tools.";
$L["notify_ft1_db_connected"] = "The database information you supplied was valid, and we could connect to the Form Tools 1 database. Now you can start <a href=\"import.php\">import data from the old database</a>.";
$L["notify_form_import_failure"] = "Sorry, we couldn't import your old form. MySQL gave the following error: ";
$L["notify_field_option_import_failure"] = "Sorry, there was a problem importing the form fields for one of your forms. The MySQL error was: ";
$L["notify_failure_adding_import_group"] = "There was a problem configuring the new field option group your form's fields. The MySQL error was: ";
$L["notify_failure_adding_last_modified_date"] = "There was a problem adding the new Last Modified Date field to your new form in Form Tools. The MySQL error was: ";
$L["notify_form_settings_added"] = "The forms have been successfully added. Click the button below to continue.";
$L["notify_failure_adding_form_data"] = "Sorry, we couldn't import the form data for one of your forms. The error MySQL gave was: ";
$L["notify_failure_adding_form_submission"] = "Sorry, we couldn't import a form submission for one of your forms. The error MySQL gave was: ";
$L["notify_form_submissions_added"] = "The form data for your forms have been added. Click the button below to continue.";
$L["notify_purge_complete"] = "The Form Tools 2 database has had all forms and client-related information deleted.";
$L["notify_problem_adding_client"] = "There was a problem adding one of your clients: ";
$L["notify_clients_added"] = "The client accounts have been imported and assigned to the forms. Click the button below to continue.";
$L["notify_no_file_fields"] = "There were no file fields found in your original installation. Click the button below to continue.";
$L["notify_file_fields_updated"] = "The file field settings have been copied over. Click the button below to continue.";

$L["confirm_purge"] = "Please confirm you want to purge the Form Tools 2 database.";

$L["validation_no_db_hostname"] = "Please enter the database hostname.";
$L["validation_no_db_name"] = "Please enter the database name.";
$L["validation_no_db_username"] = "Please enter the database username.";
