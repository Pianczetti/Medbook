{**
 * MedBook - Booking flow step 1 (module view)
 *}
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
<section class="medbook-section">
  <h1 class="medbook-text-center">{l s='Rezerwacja wizyty' d='Modules.Medbookbooking.Front'}</h1>

  <div class="booking-steps">
    <div class="booking-steps__step booking-steps__step--active">{l s='Specjalizacja' d='Modules.Medbookbooking.Front'}</div>
    <div class="booking-steps__step">{l s='Lekarz' d='Modules.Medbookbooking.Front'}</div>
    <div class="booking-steps__step">{l s='Termin' d='Modules.Medbookbooking.Front'}</div>
    <div class="booking-steps__step">{l s='Potwierdzenie' d='Modules.Medbookbooking.Front'}</div>
  </div>

  <div class="medbook-card">
    <h2>{l s='Wybierz specjalizacje' d='Modules.Medbookbooking.Front'}</h2>

    <form method="get" action="{$link->getModuleLink('medbook_booking', 'bookingflow')}">
      <input type="hidden" name="step" value="2">

      <div class="medbook-form-group">
        <label for="visit_type">{l s='Typ wizyty' d='Modules.Medbookbooking.Front'}</label>
        <select name="visit_type" id="visit_type" class="medbook-select">
          <option value="stationary">{l s='Stacjonarna' d='Modules.Medbookbooking.Front'}</option>
          <option value="online">{l s='Online' d='Modules.Medbookbooking.Front'}</option>
        </select>
      </div>

      <div class="medbook-form-group">
        <label for="insurance_type">{l s='Ubezpieczenie' d='Modules.Medbookbooking.Front'}</label>
        <select name="insurance_type" id="insurance_type" class="medbook-select">
          <option value="nfz">NFZ</option>
          <option value="private">{l s='Prywatne' d='Modules.Medbookbooking.Front'}</option>
          <option value="package">{l s='Pakiet' d='Modules.Medbookbooking.Front'}</option>
        </select>
      </div>

      {if isset($specializations)}
        <div class="medbook-grid medbook-grid--3">
          {foreach $specializations as $spec}
            <label class="medbook-card medbook-card--flat" style="cursor:pointer;">
              <input type="radio" name="specialization_id" value="{$spec.id|intval}">
              <strong>{$spec.name|escape:'html':'UTF-8'}</strong>
            </label>
          {/foreach}
        </div>
      {/if}

      <div class="medbook-text-center medbook-mt-3">
        <button type="submit" class="medbook-btn medbook-btn--primary medbook-btn--lg">
          {l s='Dalej - wybierz lekarza' d='Modules.Medbookbooking.Front'}
        </button>
      </div>
    </form>
  </div>
</section>
{/block}
