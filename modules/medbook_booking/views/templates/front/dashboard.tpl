{**
 * MedBook - Patient dashboard (module view)
 * Loaded by dashboard controller
 *}
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
<section class="medbook-section">
  <h1>{l s='Twoj panel pacjenta' d='Modules.Medbookbooking.Front'}</h1>

  <div class="dashboard-tabs">
    <button type="button" class="dashboard-tabs__tab dashboard-tabs__tab--active" data-tab="upcoming">
      {l s='Nadchodzace wizyty' d='Modules.Medbookbooking.Front'}
    </button>
    <button type="button" class="dashboard-tabs__tab" data-tab="history">
      {l s='Historia wizyt' d='Modules.Medbookbooking.Front'}
    </button>
    <button type="button" class="dashboard-tabs__tab" data-tab="favorites">
      {l s='Ulubieni lekarze' d='Modules.Medbookbooking.Front'}
    </button>
    <button type="button" class="dashboard-tabs__tab" data-tab="documents">
      {l s='Dokumenty' d='Modules.Medbookbooking.Front'}
    </button>
  </div>

  <div class="dashboard-panel" data-panel="upcoming">
    {if isset($upcoming_appointments) && $upcoming_appointments|count > 0}
      {foreach $upcoming_appointments as $appointment}
        <div class="appointment-card">
          <div class="appointment-card__date">
            <div class="appointment-card__date-day">{$appointment.day}</div>
            <div class="appointment-card__date-month">{$appointment.month}</div>
          </div>
          <div class="appointment-card__info">
            <div class="appointment-card__doctor">{$appointment.doctor_name|escape:'html':'UTF-8'}</div>
            <div class="appointment-card__detail">
              {$appointment.time|escape:'html':'UTF-8'} &bull; {$appointment.clinic_name|escape:'html':'UTF-8'}
            </div>
          </div>
          <div>
            <span class="medbook-badge medbook-badge--confirmed">{l s='Potwierdzona' d='Modules.Medbookbooking.Front'}</span>
          </div>
        </div>
      {/foreach}
    {else}
      <div class="medbook-card medbook-text-center">
        <p>{l s='Nie masz nadchodzacych wizyt.' d='Modules.Medbookbooking.Front'}</p>
        <a href="{$link->getModuleLink('medbook_booking', 'doctorsearch')}" class="medbook-btn medbook-btn--primary medbook-mt-2">
          {l s='Umow wizyte' d='Modules.Medbookbooking.Front'}
        </a>
      </div>
    {/if}
  </div>

  <div class="dashboard-panel medbook-hidden" data-panel="history">
    {if isset($history_appointments) && $history_appointments|count > 0}
      {foreach $history_appointments as $appointment}
        <div class="appointment-card">
          <div class="appointment-card__date">
            <div class="appointment-card__date-day">{$appointment.day}</div>
            <div class="appointment-card__date-month">{$appointment.month}</div>
          </div>
          <div class="appointment-card__info">
            <div class="appointment-card__doctor">{$appointment.doctor_name|escape:'html':'UTF-8'}</div>
            <div class="appointment-card__detail">{$appointment.time|escape:'html':'UTF-8'}</div>
          </div>
          <div>
            {if $appointment.status == 'completed'}
              <span class="medbook-badge medbook-badge--completed">{l s='Zakonczona' d='Modules.Medbookbooking.Front'}</span>
            {elseif $appointment.status == 'cancelled'}
              <span class="medbook-badge medbook-badge--cancelled">{l s='Anulowana' d='Modules.Medbookbooking.Front'}</span>
            {/if}
          </div>
        </div>
      {/foreach}
    {else}
      <div class="medbook-card medbook-text-center">
        <p class="medbook-text-muted">{l s='Brak historii wizyt.' d='Modules.Medbookbooking.Front'}</p>
      </div>
    {/if}
  </div>

  <div class="dashboard-panel medbook-hidden" data-panel="favorites">
    {if isset($favorite_doctors) && $favorite_doctors|count > 0}
      <div class="medbook-grid medbook-grid--2">
        {foreach $favorite_doctors as $doctor}
          <div class="medbook-card doctor-card">
            <div class="doctor-card__info">
              <h3 class="doctor-card__name">
                <a href="{$doctor.profile_url|escape:'html':'UTF-8'}">{$doctor.name|escape:'html':'UTF-8'}</a>
              </h3>
              {if $doctor.rating}
                <span class="doctor-card__rating">&#9733; {$doctor.rating|escape:'html':'UTF-8'}</span>
              {/if}
            </div>
          </div>
        {/foreach}
      </div>
    {else}
      <div class="medbook-card medbook-text-center">
        <p class="medbook-text-muted">{l s='Nie masz jeszcze ulubionych lekarzy.' d='Modules.Medbookbooking.Front'}</p>
      </div>
    {/if}
  </div>

  <div class="dashboard-panel medbook-hidden" data-panel="documents">
    {if isset($documents) && $documents|count > 0}
      {foreach $documents as $document}
        <div class="medbook-card medbook-mb-1">
          <strong>{$document.name|escape:'html':'UTF-8'}</strong>
          <br><small class="medbook-text-muted">{$document.date|escape:'html':'UTF-8'}</small>
          <a href="{$document.download_url|escape:'html':'UTF-8'}" class="medbook-btn medbook-btn--outline medbook-btn--sm">
            {l s='Pobierz' d='Modules.Medbookbooking.Front'}
          </a>
        </div>
      {/foreach}
    {else}
      <div class="medbook-card medbook-text-center">
        <p class="medbook-text-muted">{l s='Brak dokumentow.' d='Modules.Medbookbooking.Front'}</p>
      </div>
    {/if}
  </div>
</section>
{/block}
