{**
 * MedBook - Krok 3: Wybierz termin
 * Booking flow step 3
 *}
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
<section class="medbook-section">
  <h1 class="medbook-text-center">{l s='Rezerwacja wizyty' d='Shop.Theme.Medbook'}</h1>

  {* Step indicators *}
  <div class="booking-steps">
    <div class="booking-steps__step booking-steps__step--completed">{l s='Specjalizacja' d='Shop.Theme.Medbook'}</div>
    <div class="booking-steps__step booking-steps__step--completed">{l s='Lekarz' d='Shop.Theme.Medbook'}</div>
    <div class="booking-steps__step booking-steps__step--active">{l s='Termin' d='Shop.Theme.Medbook'}</div>
    <div class="booking-steps__step">{l s='Potwierdzenie' d='Shop.Theme.Medbook'}</div>
  </div>

  <div class="medbook-card">
    <h2>{l s='Wybierz termin' d='Shop.Theme.Medbook'}</h2>

    {if isset($selected_doctor_name)}
      <p class="medbook-text-muted medbook-mb-2">
        {l s='Lekarz:' d='Shop.Theme.Medbook'} <strong>{$selected_doctor_name|escape:'html':'UTF-8'}</strong>
      </p>
    {/if}

    <form method="post" action="{$link->getModuleLink('medbook_booking', 'bookingflow')}">
      <input type="hidden" name="step" value="4">
      <input type="hidden" name="specialization_id" value="{$selected_specialization|intval}">
      <input type="hidden" name="doctor_id" value="{$selected_doctor_id|intval}">
      <input type="hidden" name="resource_id" value="{$resource_id|intval}">
      <input type="hidden" name="visit_type" value="{$selected_visit_type|escape:'html':'UTF-8'}">
      <input type="hidden" name="insurance_type" value="{$selected_insurance_type|escape:'html':'UTF-8'}">

      {* Slot picker widget *}
      {include file='_partials/slot-picker.tpl'}

      <div class="medbook-text-center medbook-mt-3">
        <a href="{$link->getModuleLink('medbook_booking', 'bookingflow', ['step' => 2, 'specialization_id' => $selected_specialization])}" class="medbook-btn medbook-btn--outline">
          {l s='Wstecz' d='Shop.Theme.Medbook'}
        </a>
        <button type="submit" class="medbook-btn medbook-btn--primary medbook-btn--lg">
          {l s='Dalej - potwierdzenie' d='Shop.Theme.Medbook'}
        </button>
      </div>
    </form>
  </div>
</section>
{/block}
