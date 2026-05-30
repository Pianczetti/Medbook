{**
 * MedBook - Krok 2: Wybierz lekarza
 * Booking flow step 2
 *}
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
<section class="medbook-section">
  <h1 class="medbook-text-center">{l s='Rezerwacja wizyty' d='Shop.Theme.Medbook'}</h1>

  {* Step indicators *}
  <div class="booking-steps">
    <div class="booking-steps__step booking-steps__step--completed">{l s='Specjalizacja' d='Shop.Theme.Medbook'}</div>
    <div class="booking-steps__step booking-steps__step--active">{l s='Lekarz' d='Shop.Theme.Medbook'}</div>
    <div class="booking-steps__step">{l s='Termin' d='Shop.Theme.Medbook'}</div>
    <div class="booking-steps__step">{l s='Potwierdzenie' d='Shop.Theme.Medbook'}</div>
  </div>

  <div class="medbook-card">
    <h2>{l s='Wybierz lekarza' d='Shop.Theme.Medbook'}</h2>
    <p class="medbook-text-muted medbook-mb-2">
      {if isset($selected_specialization_name)}
        {l s='Specjalizacja:' d='Shop.Theme.Medbook'} <strong>{$selected_specialization_name|escape:'html':'UTF-8'}</strong>
      {/if}
    </p>

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
              <div class="doctor-card">
                {if $doctor.photo_url}
                  <img src="{$doctor.photo_url|escape:'html':'UTF-8'}" alt="{$doctor.name|escape:'html':'UTF-8'}" class="doctor-card__photo">
                {else}
                  <div class="doctor-card__photo doctor-card__photo--placeholder">
                    {$doctor.name|truncate:1:'':true|escape:'html':'UTF-8'}
                  </div>
                {/if}
                <div class="doctor-card__info">
                  <h3 class="doctor-card__name">{$doctor.name|escape:'html':'UTF-8'}</h3>
                  <div class="doctor-card__specialization">{$doctor.specialization|escape:'html':'UTF-8'}</div>
                  <div class="doctor-card__meta">
                    {if $doctor.rating}
                      <span class="doctor-card__rating">&#9733; {$doctor.rating|escape:'html':'UTF-8'}</span>
                    {/if}
                    {if $doctor.next_slot}
                      <span class="doctor-card__slot">{l s='Najblizszy termin:' d='Shop.Theme.Medbook'} {$doctor.next_slot|escape:'html':'UTF-8'}</span>
                    {/if}
                    {if $doctor.price}
                      <span class="doctor-card__price">{l s='od' d='Shop.Theme.Medbook'} {$doctor.price|escape:'html':'UTF-8'} {l s='zl' d='Shop.Theme.Medbook'}</span>
                    {/if}
                  </div>
                </div>
              </div>
            </label>
          {/foreach}
        </div>

        <div class="medbook-text-center medbook-mt-3">
          <a href="{$link->getModuleLink('medbook_booking', 'bookingflow', ['step' => 1])}" class="medbook-btn medbook-btn--outline">
            {l s='Wstecz' d='Shop.Theme.Medbook'}
          </a>
          <button type="submit" class="medbook-btn medbook-btn--primary medbook-btn--lg">
            {l s='Dalej - wybierz termin' d='Shop.Theme.Medbook'}
          </button>
        </div>
      </form>
    {else}
      <div class="medbook-text-center">
        <p class="medbook-text-muted">{l s='Brak dostepnych lekarzy w wybranej specjalizacji.' d='Shop.Theme.Medbook'}</p>
        <a href="{$link->getModuleLink('medbook_booking', 'bookingflow', ['step' => 1])}" class="medbook-btn medbook-btn--outline medbook-mt-2">
          {l s='Wstecz' d='Shop.Theme.Medbook'}
        </a>
      </div>
    {/if}
  </div>
</section>
{/block}
