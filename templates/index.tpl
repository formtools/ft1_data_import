{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><img src="images/ft1_upgrade_icon.gif" width="34" height="34" /></td>
    <td class="title">{$L.module_name|upper}</td>
  </tr>
  </table>

  <p>
    This module imports data from your old Form Tools 1 installation into Form Tools 2, leaving the
    original installation and data untouched. Form Tools 1 and 2 are very different scripts with different database
    structures; because of this, a standard upgrade wasn't possible. Importing the data is the
    simplest, safest solution. If things go wrong, you can always delete all Form Tools 2 data using the
    <a href="purge.php">Purge Data</a> page in this module, to start anew with a fresh Form Tools 2 database.
  </p>

  <p>
    For further information on this module including its requirements, limitations and usage instructions,
    <a href="http://modules.formtools.org/ft1_data_import/">please read the online user documentation</a>. <b>We strongly
    recommend you read the documentation!</b> Importing the data in this module is only half the step: to fully migrate
    to Form Tools 2 you will need to make changes to your forms and/or the old installation to divert all future
    form submissions to new Form Tools 2.
  </p>

  <p>
    To get started, you'll need to first <a href="settings.php">specify your Form Tools 1.5.x database settings</a>.
  </p>


{include file='modules_footer.tpl'}