{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><a href="index.php"><img src="images/ft1_upgrade_icon.gif" width="34" height="34" border="0" /></a></td>
    <td class="title">{$L.phrase_import_complete|upper}</td>
  </tr>
  </table>

  {include file="messages.tpl"}

  <div class="margin_bottom_large">
    {$L.text_import_complete}

	  {if $form_id_mapping_is_same}
	    {$L.text_complete_form_id_mapping_is_same}
	  {else}
      {$L.text_complete_form_id_mapping_not_same}
	  {/if}
  </div>

  {if !$form_id_mapping_is_same}
    <table cellspacing="0" cellpadding="1" class="margin_bottom_large">
    <tr>
      <td width="120" class="bold">{$L.phrase_original_form_id}</td>
      <td class="bold">{$L.phrase_new_form_id}</td>
    </tr>
		{foreach from=$form_id_mapping key=k item=v}
		<tr>
		  <td>{$k}</td>
		  <td>{$v}</td>
		</tr>
		{/foreach}
    </table>
  {/if}

  <div class="margin_bottom_large">
    {$L.text_final_remarks}
  </div>

{include file='modules_footer.tpl'}