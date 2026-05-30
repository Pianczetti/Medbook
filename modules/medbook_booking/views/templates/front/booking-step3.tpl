{**
 * MedBook - Booking flow step 3 (module view)
 *}
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
<section class="medbook-section">
  <h1 class="medbook-text-center">{l s='Rezerwacja wizyty' d='Modules.Medbookbooking.Front'}</h1>

  <div class="booking-steps">
    <div class="booking-steps__step booking-steps__step--completed">{l s='Specjalizacja' d='Modules.Medbookbooking.Front'}</div>
    <div class="booking-steps__step booking-steps__step--completed">{l s='Lekarz' d='Modules.Medbookbooking.Front'}</div>
    <div class="booking-steps__step booking-steps__step--active">{l s='Termin' d='Modules.Medbookbooking.Front'}</div>
    <div class="booking-steps__step">{l s='Potwierdzenie' d='Modules.Medbookbooking.Front'}</div>
  </div>

  <div class="medbook-card">
    <h2>{l s='Wybierz termin' d='Modules.Medbookbooking.Front'}</h2>
    {if isset($selected_doctor_name)}
      <p class="medbook-text-muted">{l s='Lekarz:' d='Modules.Medbookbooking.Front'} <strong>{$selected_doctor_name|escape:'html':'UTF-8'}</strong></p>
    {/if}

    <form method="post" action="{$link->getModuleLink('medbook_booking', 'bookingflow')}">
      <input type="hidden" name="step" value="4">
      <input type="hidden" name="specialization_id" value="{$selected_specialization|intval}">
      <input type="hidden" name="doctor_id" value="{$selected_doctor_id|intval}">
      <input type="hidden" name="resource_id" value="{$resource_id|intval}">
      <input type="hidden" name="visit_type" value="{$selected_visit_type|escape:'html':'UTF-8'}">
      <input type="hidden" name="insurance_type" value="{$selected_insurance_type|escape:'html':'UTF-8'}">

      {include file='module:medbook_booking/views/templates/front/slot-picker-widget.tpl'}

      <div class="medbook-text-center medbook-mt-3">
        <a href="{$link->getModuleLink('medbook_booking', 'bookingflow', ['step' => 2, 'specialization_id' => $selected_specialization])}" class="medbook-btn medbook-btn--outline">
          {l s='Wstecz' d='Modules.Medbookbooking.Front'}
        </a>
        <button type="submit" class="medbook-btn medbook-btn--primary medbook-btn--lg">
          {l s='Dalej - potwierdzenie' d='Modules.Medbookbooking.Front'}
        </button>
      </div>
    </form>
  </div>
</section>
{/block}
