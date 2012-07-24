{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{def $content=$attribute.content
     $attribute_base=ContentClass}

<div class="block">
<label>{'Default name'|i18n( 'design/standard/class/datatype' )}:</label>
<input class="box" type="text" name="{$attribute_base}_optionmatrix_name_{$class_attribute.id}" value="{$content.name|wash}" size="30" maxlength="60" />
</div>

<div class="block">
<label>{'Default number of rows'|i18n( 'design/standard/class/datatype' )}:</label>
<input type="text" name="{$attribute_base}_optionmatrix_default_num_rows_{$class_attribute.id}" value="{$class_attribute.data_int1|wash}"  size="8" maxlength="20" />
</div>

<div class="block">
<label>{'Default type'|i18n( 'design/standard/class/datatype' )}:</label>
    <select id="default_selection_{$class_attribute.id}" name="{$attribute_base}_optionmatrix_type_{$class_attribute.id}" title="{'Select type'|i18n( 'design/standard/class/datatype' )}">
		<option value="0" {eq( $class_attribute.data_text2, 0 )|choose( '', 'selected="selected"' )}>{'Radio'|i18n( 'design/standard/class/datatype' )}</option>
		<option value="1" {eq( $class_attribute.data_text2, 1 )|choose( '', 'selected="selected"' )}>{'Checkbox'|i18n( 'design/standard/class/datatype' )}</option>
		<option value="2" {eq( $class_attribute.data_text2, 2 )|choose( '', 'selected="selected"' )}>{'Drop-down list'|i18n( 'design/standard/class/datatype' )}</option>
    </select>
</div>

<div class="block">
    <label for="ContentClass_optionmatrix_required_{$class_attribute.id}">{'Default required'|i18n( 'design/standard/class/datatype' )}:</label>
    <input type="checkbox" id="ContentClass_optionmatrix_required_{$class_attribute.id}" name="ContentClass_optionmatrix_required_{$class_attribute.id}" {$class_attribute.data_text3|choose( '', 'checked="checked"' )} />
    <input type="hidden" name="ContentClass_optionmatrix_required_{$class_attribute.id}_exists" value="1" />
</div>

<div class="block">
<fieldset>
<legend>{'Columns'|i18n( 'design/standard/class/datatype' )}</legend>

{section show=$class_attribute.content.columns}
<table class="list" cellspacing="0">
<tr>
    <th class="tight">&nbsp;</th>
    <th>{'Matrix column'|i18n( 'design/standard/class/datatype' )}</th>
    <th>{'Identifier'|i18n( 'design/standard/class/datatype' )}</th>
</tr>
{section var=Columns loop=$class_attribute.content.columns sequence=array( bglight, bgdark )}
<tr class="{$Columns.sequence}">

    {* Remove. *}
    <td><input type="checkbox" name="{$attribute_base}_data_optionmatrix_column_remove_{$class_attribute.id}[]" value="{$Columns.index}" /></td>

    {* Column. *}
    <td><input class="box" type="text" name="{$attribute_base}_data_optionmatrix_column_name_{$class_attribute.id}[]" value="{$Columns.item.name|wash}" size="10" maxlength="255" /></td>

    {* Identifier. *}
    <td><input class="box" type="text" name="{$attribute_base}_data_optionmatrix_column_id_{$class_attribute.id}[]" value="{$Columns.item.identifier|wash}" size="10" maxlength="255" /></td>

</tr>
{/section}
</table>
{section-else}
<p>
{'The matrix does not have any columns.'|i18n( 'design/standard/class/datatype' )}
</p>
{/section}

{if $class_attribute.content.columns}
<input class="button" type="submit" name="CustomActionButton[{$class_attribute.id}_remove_selected]" value="{'Remove selected'|i18n('design/standard/class/datatype')}" title="{'Remove selected columns.'|i18n( 'design/standard/class/datatype' )}" />
{else}
<input class="button-disabled" type="submit" name="CustomActionButton[{$class_attribute.id}_remove_selected]" value="{'Remove selected'|i18n('design/standard/class/datatype')}" disabled="disabled" />
{/if}

<input class="button" type="submit" name="CustomActionButton[{$class_attribute.id}_new_optionmatrix_column]" value="{'New column'|i18n('design/standard/class/datatype')}" title="{'Add a new column.'|i18n( 'design/standard/class/datatype' )}" />
</fieldset>
</div>
