  <div id="upgrade_module_nav" style="float:left; border-right: 1px dotted #cccccc; width:180px; margin-right: 10px; padding-right: 10px;">

	  <ol style="margin-top: 0px; margin-left: 0px; padding-left: 18px;">
	    <li>
	      {if $step < 1}
	        <span class="light_grey">{$L.phrase_import_form_settings}</span>
	      {elseif $step == 1}
	        <b>{$L.phrase_import_form_settings}</b>
	      {else}
	        <span class="blue">{$L.phrase_import_form_settings}</span>
	      {/if}
	    </li>
	    <li>
	      {if $step < 2}
	        <span class="light_grey">{$L.phrase_import_form_data_nav}</span>
	      {elseif $step == 2}
	        <b>{$L.phrase_import_form_data_nav}</b>
	      {else}
	        <span class="blue">{$L.phrase_import_form_data_nav}</span>
	      {/if}
	    </li>
	    <li>
	      {if $step < 3}
	        <span class="light_grey">{$L.phrase_file_upload_settings}</span>
	      {elseif $step == 3}
	        <b>{$L.phrase_file_upload_settings}</b>
	      {else}
	        <span class="blue">{$L.phrase_file_upload_settings}</span>
	      {/if}
	    </li>
	    <li>
	      {if $step < 4}
	        <span class="light_grey">{$L.phrase_import_client_accounts_nav}</span>
	      {elseif $step == 4}
	        <b>{$L.phrase_import_client_accounts_nav}</b>
	      {else}
	        <span class="blue">{$L.phrase_import_client_accounts_nav}</span>
	      {/if}
	    </li>
	  </ol>

  </div>
