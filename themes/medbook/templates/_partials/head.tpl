{**
 * MedBook - Head partial
 *}
{if isset($css_files)}
  {foreach from=$css_files key=css_uri item=media}
    <link rel="stylesheet" href="{$css_uri}" media="{$media}">
  {/foreach}
{/if}
{if isset($js_files)}
  {foreach from=$js_files item=js_uri}
    <script src="{$js_uri}"></script>
  {/foreach}
{/if}
{hook h='displayHeader'}
