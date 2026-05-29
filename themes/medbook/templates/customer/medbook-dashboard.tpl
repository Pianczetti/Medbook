{**
 * MedBook - Panel pacjenta
 * Patient dashboard: upcoming appointments, history, favorites, documents
 *}
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
<section class="medbook-section">
  <h1>{l s='Twoj panel pacjenta' d='Shop.Theme.Medbook'}</h1>

  {* Dashboard tabs *}
  <div class="dashboard-tabs">
    <button type="button" class="dashboard-tabs__tab dashboard-tabs__tab--active" data-tab="upcoming">
      {l s='Nadchodzace wizyty' d='Shop.Theme.Medbook'}
    </button>
    <button type="button" class="dashboard-tabs__tab" data-tab="history">
      {l s='Historia wizyt' d='Shop.Theme.Medbook'}
    </button>
    <button type="button" class="dashboard-tabs__tab" data-tab="favorites">
      {l s='Ulubieni lekarze' d='Shop.Theme.Medbook'}
    </button>
    <button type="button" class="dashboard-tabs__tab" data-tab="documents">
      {l s='Dokumenty' d='Shop.Theme.Medbook'}
    </button>
  </div>

  {* Upcoming appointments *}
  <div class="dashboard-panel" data-panel="upcoming">
    {if isset($upcoming_appointments) && $upcoming_appointments|count > 0}
      {foreach $upcoming_appointments as $appointment}
        <div class="appointment-card">
          <div class="appointment-card__date">
            <div class="appointment-card__date-day">{$appointment.day|escape:'html':'UTF-8'}</div>
            <div class="appointment-card__date-month">{$appointment.month|escape:'html':'UTF-8'}</div>
          </div>
          <div class="appointment-card__info">
            <div class="appointment-card__doctor">{$appointment.doctor_name|escape:'html':'UTF-8'}</div>
            <div class="appointment-card__detail">
              {$appointment.specialization|escape:'html':'UTF-8'} &bull;
              {$appointment.time|escape:'html':'UTF-8'} &bull;
              {$appointment.clinic_name|escape:'html':'UTF-8'}
            </div>
            <div class="appointment-card__detail medbook-mt-1">
              {l s='Typ wizyty:' d='Shop.Theme.Medbook'}
              {if $appointment.visit_type == 'online'}
                {l s='Online' d='Shop.Theme.Medbook'}
              {else}
                {l s='Stacjonarna' d='Shop.Theme.Medbook'}
              {/if}
            </div>
          </div>
          <div class="appointment-card__actions">
            <span class="medbook-badge medbook-badge--confirmed">{l s='Potwierdzona' d='Shop.Theme.Medbook'}</span>
            <br>
            <a href="#" class="medbook-btn medbook-btn--danger medbook-btn--sm medbook-mt-1" onclick="return confirm('{l s='Czy na pewno chcesz anulowac wizyte?' d='Shop.Theme.Medbook'}');">
              {l s='Anuluj wizyte' d='Shop.Theme.Medbook'}
            </a>
          </div>
        </div>
      {/foreach}
    {else}
      <div class="medbook-card medbook-text-center">
        <p>{l s='Nie masz nadchodzacych wizyt.' d='Shop.Theme.Medbook'}</p>
        <a href="{$link->getModuleLink('medbook_booking', 'doctorsearch')}" class="medbook-btn medbook-btn--primary medbook-mt-2">
          {l s='Umow wizyte' d='Shop.Theme.Medbook'}
        </a>
      </div>
    {/if}
  </div>

  {* History *}
  <div class="dashboard-panel medbook-hidden" data-panel="history">
    {if isset($history_appointments) && $history_appointments|count > 0}
      {foreach $history_appointments as $appointment}
        <div class="appointment-card">
          <div class="appointment-card__date">
            <div class="appointment-card__date-day">{$appointment.day|escape:'html':'UTF-8'}</div>
            <div class="appointment-card__date-month">{$appointment.month|escape:'html':'UTF-8'}</div>
          </div>
          <div class="appointment-card__info">
            <div class="appointment-card__doctor">{$appointment.doctor_name|escape:'html':'UTF-8'}</div>
            <div class="appointment-card__detail">
              {$appointment.specialization|escape:'html':'UTF-8'} &bull;
              {$appointment.time|escape:'html':'UTF-8'}
            </div>
          </div>
          <div class="appointment-card__actions">
            {if $appointment.status == 'completed'}
              <span class="medbook-badge medbook-badge--completed">{l s='Zakonczona' d='Shop.Theme.Medbook'}</span>
            {elseif $appointment.status == 'cancelled'}
              <span class="medbook-badge medbook-badge--cancelled">{l s='Anulowana' d='Shop.Theme.Medbook'}</span>
            {else}
              <span class="medbook-badge medbook-badge--pending">{$appointment.status|escape:'html':'UTF-8'}</span>
            {/if}
          </div>
        </div>
      {/foreach}
    {else}
      <div class="medbook-card medbook-text-center">
        <p class="medbook-text-muted">{l s='Brak historii wizyt.' d='Shop.Theme.Medbook'}</p>
      </div>
    {/if}
  </div>

  {* Favorite doctors *}
  <div class="dashboard-panel medbook-hidden" data-panel="favorites">
    {if isset($favorite_doctors) && $favorite_doctors|count > 0}
      <div class="medbook-grid medbook-grid--2">
        {foreach $favorite_doctors as $doctor}
          {include file='_partials/doctor-card.tpl' doctor=$doctor}
        {/foreach}
      </div>
    {else}
      <div class="medbook-card medbook-text-center">
        <p class="medbook-text-muted">{l s='Nie masz jeszcze ulubionych lekarzy.' d='Shop.Theme.Medbook'}</p>
      </div>
    {/if}
  </div>

  {* Documents *}
  <div class="dashboard-panel medbook-hidden" data-panel="documents">
    {if isset($documents) && $documents|count > 0}
      {foreach $documents as $document}
        <div class="medbook-card medbook-mb-1">
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <div>
              <strong>{$document.name|escape:'html':'UTF-8'}</strong>
              <br><small class="medbook-text-muted">{$document.date|escape:'html':'UTF-8'} &bull; {$document.type|escape:'html':'UTF-8'}</small>
            </div>
            <a href="{$document.download_url|escape:'html':'UTF-8'}" class="medbook-btn medbook-btn--outline medbook-btn--sm">
              {l s='Pobierz' d='Shop.Theme.Medbook'}
            </a>
          </div>
        </div>
      {/foreach}
    {else}
      <div class="medbook-card medbook-text-center">
        <p class="medbook-text-muted">{l s='Brak dokumentow.' d='Shop.Theme.Medbook'}</p>
      </div>
    {/if}
  </div>
</section>
{/block}
