{**
 * MedBook - Krok 4: Potwierdzenie rezerwacji
 * Booking flow step 4
 *}
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
<section class="medbook-section">
  <h1 class="medbook-text-center">{l s='Rezerwacja wizyty' d='Shop.Theme.Medbook'}</h1>

  {* Step indicators *}
  <div class="booking-steps">
    <div class="booking-steps__step booking-steps__step--completed">{l s='Specjalizacja' d='Shop.Theme.Medbook'}</div>
    <div class="booking-steps__step booking-steps__step--completed">{l s='Lekarz' d='Shop.Theme.Medbook'}</div>
    <div class="booking-steps__step booking-steps__step--completed">{l s='Termin' d='Shop.Theme.Medbook'}</div>
    <div class="booking-steps__step booking-steps__step--active">{l s='Potwierdzenie' d='Shop.Theme.Medbook'}</div>
  </div>

  {if isset($booking_success) && $booking_success}
    {* Success state *}
    <div class="medbook-card medbook-text-center">
      <div style="font-size:3rem;color:var(--medbook-secondary);">&#10003;</div>
      <h2>{l s='Wizyta zarezerwowana!' d='Shop.Theme.Medbook'}</h2>
      <p class="medbook-text-muted">
        {l s='Twoja rezerwacja zostala potwierdzona. Otrzymasz potwierdzenie na email.' d='Shop.Theme.Medbook'}
      </p>
      {if isset($reference_code)}
        <p><strong>{l s='Numer rezerwacji:' d='Shop.Theme.Medbook'}</strong> {$reference_code|escape:'html':'UTF-8'}</p>
      {/if}
      <div class="medbook-mt-3">
        <a href="{$link->getModuleLink('medbook_booking', 'dashboard')}" class="medbook-btn medbook-btn--primary">
          {l s='Przejdz do panelu' d='Shop.Theme.Medbook'}
        </a>
      </div>
    </div>
  {else}
    {* Confirmation form *}
    <div class="medbook-card">
      <h2>{l s='Potwierdzenie rezerwacji' d='Shop.Theme.Medbook'}</h2>

      {if isset($booking_errors)}
        <div class="medbook-alert medbook-alert--error medbook-mb-2">
          {foreach $booking_errors as $error}
            <p>{$error|escape:'html':'UTF-8'}</p>
          {/foreach}
        </div>
      {/if}

      {* Summary *}
      <div class="medbook-card medbook-card--flat medbook-mb-2" style="background:var(--medbook-bg);">
        <h3>{l s='Podsumowanie wizyty' d='Shop.Theme.Medbook'}</h3>
        <table style="width:100%;border-collapse:collapse;">
          <tr>
            <td class="medbook-text-muted" style="padding:0.5rem 0;">{l s='Lekarz:' d='Shop.Theme.Medbook'}</td>
            <td style="padding:0.5rem 0;"><strong>{$selected_doctor_name|escape:'html':'UTF-8'}</strong></td>
          </tr>
          <tr>
            <td class="medbook-text-muted" style="padding:0.5rem 0;">{l s='Specjalizacja:' d='Shop.Theme.Medbook'}</td>
            <td style="padding:0.5rem 0;">{$selected_specialization_name|escape:'html':'UTF-8'}</td>
          </tr>
          <tr>
            <td class="medbook-text-muted" style="padding:0.5rem 0;">{l s='Data:' d='Shop.Theme.Medbook'}</td>
            <td style="padding:0.5rem 0;">{$selected_date|escape:'html':'UTF-8'}</td>
          </tr>
          <tr>
            <td class="medbook-text-muted" style="padding:0.5rem 0;">{l s='Godzina:' d='Shop.Theme.Medbook'}</td>
            <td style="padding:0.5rem 0;">{$selected_time|escape:'html':'UTF-8'}</td>
          </tr>
          <tr>
            <td class="medbook-text-muted" style="padding:0.5rem 0;">{l s='Typ wizyty:' d='Shop.Theme.Medbook'}</td>
            <td style="padding:0.5rem 0;">
              {if $selected_visit_type == 'online'}{l s='Online' d='Shop.Theme.Medbook'}{else}{l s='Stacjonarna' d='Shop.Theme.Medbook'}{/if}
            </td>
          </tr>
          <tr>
            <td class="medbook-text-muted" style="padding:0.5rem 0;">{l s='Ubezpieczenie:' d='Shop.Theme.Medbook'}</td>
            <td style="padding:0.5rem 0;">
              {if $selected_insurance_type == 'nfz'}NFZ{elseif $selected_insurance_type == 'private'}{l s='Prywatne' d='Shop.Theme.Medbook'}{else}{l s='Pakiet' d='Shop.Theme.Medbook'}{/if}
            </td>
          </tr>
          {if isset($price)}
            <tr>
              <td class="medbook-text-muted" style="padding:0.5rem 0;">{l s='Cena:' d='Shop.Theme.Medbook'}</td>
              <td style="padding:0.5rem 0;"><strong>{$price|escape:'html':'UTF-8'} {l s='zl' d='Shop.Theme.Medbook'}</strong></td>
            </tr>
          {/if}
        </table>
      </div>

      {* Patient data form *}
      <form method="post" action="{$link->getModuleLink('medbook_booking', 'bookingflow')}">
        <input type="hidden" name="submitBooking" value="1">
        <input type="hidden" name="specialization_id" value="{$selected_specialization|intval}">
        <input type="hidden" name="doctor_id" value="{$selected_doctor_id|intval}">
        <input type="hidden" name="resource_id" value="{$resource_id|intval}">
        <input type="hidden" name="booking_date" value="{$selected_date|escape:'html':'UTF-8'}">
        <input type="hidden" name="time_start" value="{$selected_time|escape:'html':'UTF-8'}">
        <input type="hidden" name="visit_type" value="{$selected_visit_type|escape:'html':'UTF-8'}">
        <input type="hidden" name="insurance_type" value="{$selected_insurance_type|escape:'html':'UTF-8'}">

        {if !$customer.is_logged}
          <h3>{l s='Dane pacjenta' d='Shop.Theme.Medbook'}</h3>

          <div class="medbook-form-group">
            <label for="customer_name">{l s='Imie i nazwisko' d='Shop.Theme.Medbook'}</label>
            <input type="text" name="customer_name" id="customer_name" class="medbook-input" required>
          </div>

          <div class="medbook-form-group">
            <label for="customer_email">{l s='Adres email' d='Shop.Theme.Medbook'}</label>
            <input type="email" name="customer_email" id="customer_email" class="medbook-input" required>
          </div>

          <div class="medbook-form-group">
            <label for="customer_phone">{l s='Numer telefonu' d='Shop.Theme.Medbook'}</label>
            <input type="tel" name="customer_phone" id="customer_phone" class="medbook-input">
          </div>
        {/if}

        <div class="medbook-form-group">
          <label for="notes">{l s='Uwagi dla lekarza (opcjonalne)' d='Shop.Theme.Medbook'}</label>
          <textarea name="notes" id="notes" class="medbook-input" rows="3" placeholder="{l s='Opisz krotko powod wizyty...' d='Shop.Theme.Medbook'}"></textarea>
        </div>

        <div class="medbook-text-center medbook-mt-3">
          <a href="{$link->getModuleLink('medbook_booking', 'bookingflow', ['step' => 3, 'doctor_id' => $selected_doctor_id, 'specialization_id' => $selected_specialization])}" class="medbook-btn medbook-btn--outline">
            {l s='Wstecz' d='Shop.Theme.Medbook'}
          </a>
          <button type="submit" class="medbook-btn medbook-btn--secondary medbook-btn--lg">
            {l s='Potwierdz rezerwacje' d='Shop.Theme.Medbook'}
          </button>
        </div>
      </form>
    </div>
  {/if}
</section>
{/block}
