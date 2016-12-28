{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0" class="margin_bottom_large">
  <tr>
    <td width="45"><img src="images/ft1_upgrade_icon.gif" width="34" height="34" /></td>
    <td class="title">{$L.phrase_import_data|upper}</td>
  </tr>
  </table>

  {if $db_settings_confirmed == "no"}

	  {include file="messages.tpl"}

  {else}

	  <div>
	    {$L.text_import_summary}
	  </div>

	  {include file="messages.tpl"}

	  {assign var="step" value="2"}
	  {include file="../../modules/ft1_data_import/templates/progress.tpl"}

	  {if $step2_completed}

      <input type="button" value="{$LANG.word_continue_rightarrow}" onclick="window.location='step3.php'" />

	  {else}

		  <form action="{$same_page}" method="post">

	      <div>
	        {$L.text_import_submissions_summary}
	      </div>

			  <p>
			    <input type="submit" name="continue" id="continue" value="{$L.phrase_import_form_data}" />
			  </p>

		  </form>

	  {/if}

  {/if}

{include file='modules_footer.tpl'}