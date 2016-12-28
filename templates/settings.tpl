{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><a href="index.php"><img src="images/ft1_upgrade_icon.gif" width="34" height="34" border="0" /></a></td>
    <td class="title">{$L.phrase_form_tools1_db_settings|upper}</td>
  </tr>
  </table>

  {include file="messages.tpl"}

  <div class="margin_bottom_large">
    {$L.text_ft1_db_settings_summary}
  </div>

	<form action="{$same_page}" method="post" onsubmit="return rsv.validate(this, rules)">

    <table cellpadding="1" cellspacing="0">
    <tr>
      <td width="15" class="red">*</td>
      <td class="medium_grey" width="140">{$LANG.phrase_database_hostname}</td>
      <td><input type="text" size="20" name="db_hostname" value="{$module_settings.db_hostname}" /> {$LANG.phrase_often_localhost}</td>
    </tr>
    <tr>
      <td class="red">*</td>
      <td class="medium_grey">{$LANG.phrase_database_name}</td>
      <td><input type="text" size="20" name="db_name" value="{$module_settings.db_name}" /></td>
    </tr>
    <tr>
      <td class="red">*</td>
      <td class="medium_grey">{$LANG.phrase_database_username}</td>
      <td><input type="text" size="20" name="db_username" value="{$module_settings.db_username}" /></td>
    </tr>
    <tr>
      <td class="red"> </td>
      <td class="medium_grey">{$LANG.phrase_database_password}</td>
      <td><input type="text" size="20" name="db_password" value="{$module_settings.db_password}" /></td>
    </tr>
    <tr>
      <td class="red"> </td>
      <td class="medium_grey">{$LANG.phrase_database_table_prefix}</td>
      <td><input type="text" size="20" name="table_prefix" value="{$module_settings.table_prefix}" /></td>
    </tr>
    </table>

	  <p>
	    <input type="submit" name="check_settings" value="{$L.phrase_check_settings}" />
	  </p>

  </form>


{include file='modules_footer.tpl'}