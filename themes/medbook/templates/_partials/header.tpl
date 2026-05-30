{**
 * MedBook - Header partial
 * Navigation in Polish with medical booking focus
 *}
<header class="medbook-header">
  <div class="medbook-header__inner">
    <a href="{$urls.base_url}" class="medbook-header__logo">
      <span class="medbook-header__logo-icon">+</span>
      <span>MedBook</span>
    </a>

    <button class="medbook-nav-toggle" aria-label="Menu">&#9776;</button>

    <nav>
      <ul class="medbook-nav">
        <li>
          <a href="{$urls.base_url}" class="medbook-nav__link">
            {l s='Strona glowna' d='Shop.Theme.Medbook'}
          </a>
        </li>
        <li>
          <a href="{$link->getModuleLink('medbook_booking', 'doctorsearch')}" class="medbook-nav__link">
            {l s='Lekarze' d='Shop.Theme.Medbook'}
          </a>
        </li>
        <li>
          <a href="{$link->getModuleLink('medbook_booking', 'doctorsearch', ['view' => 'specializations'])}" class="medbook-nav__link">
            {l s='Specjalizacje' d='Shop.Theme.Medbook'}
          </a>
        </li>
        {if $customer.is_logged}
          <li>
            <a href="{$link->getModuleLink('medbook_booking', 'dashboard')}" class="medbook-nav__link">
              {l s='Moje wizyty' d='Shop.Theme.Medbook'}
            </a>
          </li>
        {/if}
        <li>
          <a href="{$urls.pages.contact}" class="medbook-nav__link">
            {l s='Kontakt' d='Shop.Theme.Medbook'}
          </a>
        </li>
      </ul>
    </nav>

    <div class="medbook-header__actions">
      {if $customer.is_logged}
        <div class="medbook-header__user">
          <a href="{$urls.pages.my_account}" class="medbook-btn medbook-btn--outline medbook-btn--sm">
            {$customer.firstname} {$customer.lastname|truncate:1:'.'|escape:'html':'UTF-8'}
          </a>
        </div>
      {else}
        <a href="{$urls.pages.authentication}" class="medbook-btn medbook-btn--primary medbook-btn--sm">
          {l s='Zaloguj sie' d='Shop.Theme.Medbook'}
        </a>
      {/if}
    </div>
  </div>
</header>
