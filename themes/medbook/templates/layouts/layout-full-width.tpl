{**
 * MedBook - Platforma Medyczna
 * Layout: Pelna szerokosc
 *}

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$page.meta.title|escape:'html':'UTF-8'} - MedBook</title>
  {if $page.meta.description}
    <meta name="description" content="{$page.meta.description|escape:'html':'UTF-8'}">
  {/if}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  {block name='head'}
    {include file='_partials/head.tpl'}
  {/block}
</head>
<body class="medbook-body medbook-layout-full-width {$page.body_classes|classnames}">

  {block name='hook_after_body_opening_tag'}
    {hook h='displayAfterBodyOpeningTag'}
  {/block}

  <div class="trust-bar">
    <div class="trust-bar__items">
      <span>Bezpieczna rezerwacja online</span>
      <span>Ponad 500 lekarzy</span>
      <span>Potwierdzenie w 5 minut</span>
    </div>
  </div>

  {block name='header'}
    {include file='_partials/header.tpl'}
  {/block}

  <main class="medbook-main">
    <div class="medbook-container">
      {block name='notifications'}
        {include file='_partials/notifications.tpl'}
      {/block}

      {block name='content'}
        <p>Tresc strony</p>
      {/block}
    </div>
  </main>

  {block name='footer'}
    {include file='_partials/footer.tpl'}
  {/block}

  {block name='javascript_bottom'}
    {hook h='displayBeforeBodyClosingTag'}
  {/block}

</body>
</html>
