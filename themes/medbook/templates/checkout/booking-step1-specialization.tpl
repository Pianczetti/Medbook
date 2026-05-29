{**
 * MedBook - Krok 1: Wybierz specjalizacje
 * Booking flow step 1
 *}
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
<section class="medbook-section">
  <h1 class="medbook-text-center">{l s='Rezerwacja wizyty' d='Shop.Theme.Medbook'}</h1>

  {* Step indicators *}
  <div class="booking-steps">
    <div class="booking-steps__step booking-steps__step--active">{l s='Specjalizacja' d='Shop.Theme.Medbook'}</div>
    <div class="booking-steps__step">{l s='Lekarz' d='Shop.Theme.Medbook'}</div>
    <div class="booking-steps__step">{l s='Termin' d='Shop.Theme.Medbook'}</div>
    <div class="booking-steps__step">{l s='Potwierdzenie' d='Shop.Theme.Medbook'}</div>
  </div>

  <div class="medbook-card">
    <h2>{l s='Wybierz specjalizacje' d='Shop.Theme.Medbook'}</h2>
    <p class="medbook-text-muted medbook-mb-2">{l s='Wybierz rodzaj konsultacji, jakiej potrzebujesz' d='Shop.Theme.Medbook'}</p>

    <form method="get" action="{$link->getModuleLink('medbook_booking', 'bookingflow')}">
      <input type="hidden" name="step" value="2">

      <div class="medbook-form-group">
        <label for="visit_type">{l s='Typ wizyty' d='Shop.Theme.Medbook'}</label>
        <select name="visit_type" id="visit_type" class="medbook-select">
          <option value="stationary">{l s='Stacjonarna' d='Shop.Theme.Medbook'}</option>
          <option value="online">{l s='Online' d='Shop.Theme.Medbook'}</option>
        </select>
      </div>

      <div class="medbook-form-group">
        <label for="insurance_type">{l s='Ubezpieczenie' d='Shop.Theme.Medbook'}</label>
        <select name="insurance_type" id="insurance_type" class="medbook-select">
          <option value="nfz">NFZ</option>
          <option value="private">{l s='Prywatne' d='Shop.Theme.Medbook'}</option>
          <option value="package">{l s='Pakiet' d='Shop.Theme.Medbook'}</option>
        </select>
      </div>

      <div class="medbook-form-group">
        <label>{l s='Wybierz specjalizacje' d='Shop.Theme.Medbook'}</label>
        {if isset($specializations)}
          <div class="medbook-grid medbook-grid--3">
            {foreach $specializations as $spec}
              <label class="medbook-card medbook-card--flat" style="cursor:pointer;">
                <input type="radio" name="specialization_id" value="{$spec.id|intval}" {if isset($selected_specialization) && $selected_specialization == $spec.id}checked{/if}>
                <strong>{$spec.name|escape:'html':'UTF-8'}</strong>
                {if isset($spec.doctors_count)}
                  <br><small class="medbook-text-muted">{$spec.doctors_count} {l s='lekarzy' d='Shop.Theme.Medbook'}</small>
                {/if}
              </label>
            {/foreach}
          </div>
        {/if}
      </div>

      <div class="medbook-text-center medbook-mt-3">
        <button type="submit" class="medbook-btn medbook-btn--primary medbook-btn--lg">
          {l s='Dalej - wybierz lekarza' d='Shop.Theme.Medbook'}
        </button>
      </div>
    </form>
  </div>
</section>
{/block}
