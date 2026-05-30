{**
 * MedBook - Wyszukiwarka lekarzy
 * Doctor search/listing page
 *}
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
<section class="medbook-section">
  <h1 class="medbook-text-center">{l s='Szukaj lekarza' d='Shop.Theme.Medbook'}</h1>
  <p class="medbook-text-center medbook-text-muted medbook-mb-3">
    {l s='Znajdz specjaliste i umow wizyte online' d='Shop.Theme.Medbook'}
  </p>

  {* Search filters *}
  <div class="medbook-search-bar">
    <form method="get" action="{$link->getModuleLink('medbook_booking', 'doctorsearch')}" class="medbook-search-form">
      <div class="medbook-search-bar__row">
        <div class="medbook-search-bar__field">
          <label for="specialization">{l s='Specjalizacja' d='Shop.Theme.Medbook'}</label>
          <select name="specialization" id="specialization" class="medbook-select">
            <option value="">{l s='Wszystkie specjalizacje' d='Shop.Theme.Medbook'}</option>
            {if isset($specializations)}
              {foreach $specializations as $spec}
                <option value="{$spec.id|intval}"{if isset($selected_specialization) && $selected_specialization == $spec.id} selected{/if}>
                  {$spec.name|escape:'html':'UTF-8'}
                </option>
              {/foreach}
            {/if}
          </select>
        </div>

        <div class="medbook-search-bar__field">
          <label for="city">{l s='Miasto' d='Shop.Theme.Medbook'}</label>
          <input type="text" name="city" id="city" class="medbook-input"
                 placeholder="{l s='Wpisz miasto...' d='Shop.Theme.Medbook'}"
                 value="{if isset($selected_city)}{$selected_city|escape:'html':'UTF-8'}{/if}">
        </div>

        <div class="medbook-search-bar__field">
          <label for="availability">{l s='Dostepnosc' d='Shop.Theme.Medbook'}</label>
          <input type="date" name="availability" id="availability" class="medbook-input"
                 value="{if isset($selected_date)}{$selected_date|escape:'html':'UTF-8'}{/if}">
        </div>

        <div class="medbook-search-bar__field">
          <label for="visit_type">{l s='Typ wizyty' d='Shop.Theme.Medbook'}</label>
          <select name="visit_type" id="visit_type" class="medbook-select">
            <option value="">{l s='Wszystkie' d='Shop.Theme.Medbook'}</option>
            <option value="stationary"{if isset($selected_visit_type) && $selected_visit_type == 'stationary'} selected{/if}>{l s='Stacjonarna' d='Shop.Theme.Medbook'}</option>
            <option value="online"{if isset($selected_visit_type) && $selected_visit_type == 'online'} selected{/if}>{l s='Online' d='Shop.Theme.Medbook'}</option>
          </select>
        </div>

        <div class="medbook-search-bar__field">
          <label for="insurance">{l s='Ubezpieczenie' d='Shop.Theme.Medbook'}</label>
          <select name="insurance" id="insurance" class="medbook-select">
            <option value="">{l s='Wszystkie' d='Shop.Theme.Medbook'}</option>
            <option value="nfz"{if isset($selected_insurance) && $selected_insurance == 'nfz'} selected{/if}>NFZ</option>
            <option value="private"{if isset($selected_insurance) && $selected_insurance == 'private'} selected{/if}>{l s='Prywatne' d='Shop.Theme.Medbook'}</option>
            <option value="package"{if isset($selected_insurance) && $selected_insurance == 'package'} selected{/if}>{l s='Pakiet' d='Shop.Theme.Medbook'}</option>
          </select>
        </div>

        <div class="medbook-search-bar__field">
          <label>&nbsp;</label>
          <button type="submit" class="medbook-btn medbook-btn--primary medbook-btn--block">
            {l s='Szukaj' d='Shop.Theme.Medbook'}
          </button>
        </div>
      </div>
    </form>
  </div>

  {* Search results *}
  <div class="doctor-search-results">
    {if isset($doctors) && $doctors|count > 0}
      <p class="medbook-text-muted medbook-mb-2">
        {l s='Znaleziono' d='Shop.Theme.Medbook'} {$doctors|count} {l s='lekarzy' d='Shop.Theme.Medbook'}
      </p>
      <div class="medbook-grid medbook-grid--2">
        {foreach $doctors as $doctor}
          {include file='_partials/doctor-card.tpl' doctor=$doctor}
        {/foreach}
      </div>
    {elseif isset($search_performed) && $search_performed}
      <div class="medbook-card medbook-text-center">
        <p>{l s='Nie znaleziono lekarzy spelniajacych kryteria wyszukiwania.' d='Shop.Theme.Medbook'}</p>
        <p class="medbook-text-muted">{l s='Sprobuj zmienic filtry lub wyszukaj ponownie.' d='Shop.Theme.Medbook'}</p>
      </div>
    {else}
      <div class="medbook-card medbook-text-center">
        <h3>{l s='Popularne specjalizacje' d='Shop.Theme.Medbook'}</h3>
        <p class="medbook-text-muted">{l s='Wybierz specjalizacje, aby zobaczyc dostepnych lekarzy' d='Shop.Theme.Medbook'}</p>
        {if isset($specializations)}
          <div class="medbook-grid medbook-grid--4 medbook-mt-2">
            {foreach $specializations as $spec}
              <a href="{$link->getModuleLink('medbook_booking', 'doctorsearch', ['specialization' => $spec.id])}" class="medbook-card medbook-card--flat medbook-text-center">
                <strong>{$spec.name|escape:'html':'UTF-8'}</strong>
                {if isset($spec.doctors_count)}
                  <br><small class="medbook-text-muted">{$spec.doctors_count} {l s='lekarzy' d='Shop.Theme.Medbook'}</small>
                {/if}
              </a>
            {/foreach}
          </div>
        {/if}
      </div>
    {/if}
  </div>
</section>
{/block}
