{**
 * MedBook - CMS page template
 *}
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
<section class="medbook-section">
  <div class="medbook-card">
    <h1>{$cms.meta_title|escape:'html':'UTF-8'}</h1>
    <div class="medbook-cms-content">
      {$cms.content nofilter}
    </div>
  </div>
</section>
{/block}
