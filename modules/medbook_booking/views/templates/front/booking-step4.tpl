{**
 * MedBook - Booking flow step 4 - confirmation (module view)
 *}
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
<section class="medbook-section">
  <h1 class="medbook-text-center">{l s='Rezerwacja wizyty' d='Modules.Medbookbooking.Front'}</h1>

  <div class="booking-steps">
    <div class="booking-steps__step booking-steps__step--completed">{l s='Specjalizacja' d='Modules.Medbookbooking.Front'}</div>
    <div class="booking-steps__step booking-steps__step--completed">{l s='Lekarz' d='Modules.Medbookbooking.Front'}</div>
    <div class="booking-steps__step booking-steps__step--completed">{l s='Termin' d='Modules.Medbookbooking.Front'}</div>
    <div class="booking-steps__step booking-steps__step--active">{l s='Potwierdzenie' d='Modules.Medbookbooking.Front'}</div>
  </div>

  {if isset($booking_success) && $booking_success}
    <div class="medbook-card medbook-text-center">
      <div style="font-size:3rem;color:var(--medbook-secondary);">&#10003;</div>
      <h2>{l s='Wizyta zarezerwowana!' d='Modules.Medbookbooking.Front'}</h2>
      <p class="medbook-text-muted">{l s='Twoja rezerwacja zostala potwierdzona. Otrzymasz potwierdzenie na email.' d='Modules.Medbookbooking.Front'}</p>
      {if isset($reference_code)}
        <p><strong>{l s='Numer rezerwacji:' d='Modules.Medbookbooking.Front'}</strong> {$reference_code|escape:'html':'UTF-8'}</p>
      {/if}
      <a href="{$link->getModuleLink('medbook_booking', 'dashboard')}" class="medbook-btn medbook-btn--primary medbook-mt-2">
        {l s='Przejdz do panelu' d='Modules.Medbookbooking.Front'}
      </a>
    </div>
  {else}
    <div class="medbook-card">
      <h2>{l s='Potwierdzenie rezerwacji' d='Modules.Medbookbooking.Front'}</h2>

      {if isset($booking_errors)}
        <div class="medbook-alert medbook-alert--error medbook-mb-2">
          {foreach $booking_errors as $error}
            <p>{$error|escape:'html':'UTF-8'}</p>
          {/foreach}
        </div>
      {/if}

      <form method="post" action="{$link->getModuleLink('medbook_booking', 'bookingflow')}">
        <input type="hidden" name="submitBooking" value="1">
        <input type="hidden" name="step" value="4">
        <input type="hidden" name="resource_id" value="{$resource_id|intval}">
        <input type="hidden" name="booking_date" value="{$selected_date|escape:'html':'UTF-8'}">
        <input type="hidden" name="time_start" value="{$selected_time|escape:'html':'UTF-8'}">

        <table style="width:100%;margin-bottom:1.5rem;">
          <tr>
            <td class="medbook-text-muted">{l s='Lekarz:' d='Modules.Medbookbooking.Front'}</td>
            <td><strong>{$selected_doctor_name|escape:'html':'UTF-8'}</strong></td>
          </tr>
          <tr>
            <td class="medbook-text-muted">{l s='Data:' d='Modules.Medbookbooking.Front'}</td>
            <td>{$selected_date|escape:'html':'UTF-8'}</td>
          </tr>
          <tr>
            <td class="medbook-text-muted">{l s='Godzina:' d='Modules.Medbookbooking.Front'}</td>
            <td>{$selected_time|escape:'html':'UTF-8'}</td>
          </tr>
        </table>

        {if !isset($customer) || !$customer.is_logged}
          <div class="medbook-form-group">
            <label>{l s='Imie i nazwisko' d='Modules.Medbookbooking.Front'}</label>
            <input type="text" name="customer_name" class="medbook-input" required>
          </div>
          <div class="medbook-form-group">
            <label>{l s='Adres email' d='Modules.Medbookbooking.Front'}</label>
            <input type="email" name="customer_email" class="medbook-input" required>
          </div>
          <div class="medbook-form-group">
            <label>{l s='Numer telefonu' d='Modules.Medbookbooking.Front'}</label>
            <input type="tel" name="customer_phone" class="medbook-input">
          </div>
        {/if}

        <div class="medbook-form-group">
          <label>{l s='Uwagi dla lekarza' d='Modules.Medbookbooking.Front'}</label>
          <textarea name="notes" class="medbook-input" rows="3"></textarea>
        </div>

        <div class="medbook-text-center medbook-mt-3">
          <button type="submit" class="medbook-btn medbook-btn--secondary medbook-btn--lg">
            {l s='Potwierdz rezerwacje' d='Modules.Medbookbooking.Front'}
          </button>
        </div>
      </form>
    </div>
  {/if}
</section>
{/block}
