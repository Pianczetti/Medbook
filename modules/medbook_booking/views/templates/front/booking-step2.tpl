{**
 * MedBook - Booking flow step 2 (module view)
 *}
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
<section class="medbook-section">
  <h1 class="medbook-text-center">{l s='Rezerwacja wizyty' d='Modules.Medbookbooking.Front'}</h1>

  <div class="booking-steps">
    <div class="booking-steps__step booking-steps__step--completed">{l s='Specjalizacja' d='Modules.Medbookbooking.Front'}</div>
    <div class="booking-steps__step booking-steps__step--active">{l s='Lekarz' d='Modules.Medbookbooking.Front'}</div>
    <div class="booking-steps__step">{l s='Termin' d='Modules.Medbookbooking.Front'}</div>
    <div class="booking-steps__step">{l s='Potwierdzenie' d='Modules.Medbookbooking.Front'}</div>
  </div>

  <div class="medbook-card">
    <h2>{l s='Wybierz lekarza' d='Modules.Medbookbooking.Front'}</h2>
    {if isset($selected_specialization_name)}
      <p class="medbook-text-muted">{l s='Specjalizacja:' d='Modules.Medbookbooking.Front'} <strong>{$selected_specialization_name|escape:'html':'UTF-8'}</strong></p>
    {/if}

    {if isset($doctors) && $doctors|count > 0}
      <form method="get" action="{$link->getModuleLink('medbook_booking', 'bookingflow')}">
        <input type="hidden" name="step" value="3">
        <input type="hidden" name="specialization_id" value="{$selected_specialization|intval}">
        <input type="hidden" name="visit_type" value="{$selected_visit_type|escape:'html':'UTF-8'}">
        <input type="hidden" name="insurance_type" value="{$selected_insurance_type|escape:'html':'UTF-8'}">

        <div class="medbook-grid medbook-grid--2">
          {foreach $doctors as $doctor}
            <label class="medbook-card" style="cursor:pointer;">
              <input type="radio" name="doctor_id" value="{$doctor.id|intval}">
              <h3 class="doctor-card__name">{$doctor.name|escape:'html':'UTF-8'}</h3>
              {if $doctor.rating}
                <span class="doctor-card__rating">&#9733; {$doctor.rating|escape:'html':'UTF-8'}</span>
              {/if}
              {if $doctor.price}
                <span class="doctor-card__price">{l s='od' d='Modules.Medbookbooking.Front'} {$doctor.price|escape:'html':'UTF-8'} {l s='zl' d='Modules.Medbookbooking.Front'}</span>
              {/if}
            </label>
          {/foreach}
        </div>

        <div class="medbook-text-center medbook-mt-3">
          <a href="{$link->getModuleLink('medbook_booking', 'bookingflow', ['step' => 1])}" class="medbook-btn medbook-btn--outline">
            {l s='Wstecz' d='Modules.Medbookbooking.Front'}
          </a>
          <button type="submit" class="medbook-btn medbook-btn--primary medbook-btn--lg">
            {l s='Dalej - wybierz termin' d='Modules.Medbookbooking.Front'}
          </button>
        </div>
      </form>
    {else}
      <p class="medbook-text-muted medbook-text-center">{l s='Brak dostepnych lekarzy w wybranej specjalizacji.' d='Modules.Medbookbooking.Front'}</p>
    {/if}
  </div>
</section>
{/block}
