{*This is an example script - it goes nowhere and it's not the way to do it, if you are going to collect this information with a custom module, then this should be the datatype view override for the frontend site-accesses.*}

<form action={$node.url_alias|ezurl} method="post">
	{foreach $node.data_map as $optionmatrix}
		{if and(eq($optionmatrix.data_type_string,'optionmatrix'),gt($optionmatrix.content.rowCount,0))}
			{set $label=$optionmatrix.contentclass_attribute_identifier}
			<label for="value{$label}">{$optionmatrix.content.name} {if eq($optionmatrix.content.required_value,"on")} *{/if}</label>
			{switch match=$optionmatrix.content.type_value}
				{case match="1"} {*checkbox*}
					{foreach $optionmatrix.content.rows.sequential as $row}
						{$row.columns.1|wash( xhtml )}&nbsp;<input name="{$optionmatrix.contentclass_attribute_identifier}" id="{$optionmatrix.contentclass_attribute_identifier}" type="checkbox" value="{$row.columns.0|wash( xhtml )}"{if eq($row.columns.2|wash( xhtml ),'default')} checked{/if}/></br>
					{/foreach}
				{/case}
				{case match="2"} {*dropdown*}
					<select id="select{$optionmatrix.contentclass_attribute_identifier}" name="{$optionmatrix.contentclass_attribute_identifier}" class="required">
					{foreach $optionmatrix.content.rows.sequential as $row}
						<option value="{$row.columns.0|wash( xhtml )}"{if eq($row.columns.2|wash( xhtml ),'default')} selected{/if}>{$row.columns.1|wash( xhtml )}</option>
					{/foreach}
					</select>
				{/case}
				{case} {*radio*}
					{foreach $optionmatrix.content.rows.sequential as $row}
						<input name="{$optionmatrix.contentclass_attribute_identifier}" id="{$optionmatrix.contentclass_attribute_identifier}" type="radio" value="{$row.columns.0|wash( xhtml )}"{if eq($row.columns.2|wash( xhtml ),'default')} checked{/if} />&nbsp;{$row.columns.1|wash( xhtml )}<br/>
					{/foreach}
				{/case}
			{/switch}
			<br/>
		{/if}
	{/foreach}
</form>