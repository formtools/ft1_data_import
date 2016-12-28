{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><a href="index.php"><img src="images/ft1_upgrade_icon.gif" width="34" height="34" border="0" /></a></td>
    <td class="title">{$L.phrase_purge_data|upper}</td>
  </tr>
  </table>

  {include file="messages.tpl"}

  <div class="margin_bottom_large">
    {$L.text_purge_para1}
    <ul>
      <li>{$L.text_purge_para2}</li>
      <li>{$L.text_purge_para3}</li>
      <li>{$L.text_purge_para4}</li>
    </ul>

    {$L.text_purge_para5}
  </div>


	<form action="{$same_page}" method="post" onsubmit="return rsv.validate(this, rules)">

    <div class="error">
      <div style="padding: 6px">
		    <input type="checkbox" name="confirmation" id="confirmation" />
	      <label for="confirmation">
	        {$L.text_confirm_purge}
	      </label>
		    <br />
		    <br />

		    <input type="submit" value="{$L.phrase_delete_data|upper}" name="delete_data" class="red" />
      </div>
    </div>
  </form>


{include file='modules_footer.tpl'}