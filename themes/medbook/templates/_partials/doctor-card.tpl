{**
 * MedBook - Reusable doctor card component
 * Variables expected:
 *   $doctor.name - full name (e.g. dr Jan Kowalski)
 *   $doctor.specialization - specialization name
 *   $doctor.photo_url - photo URL or empty
 *   $doctor.rating - numeric rating (e.g. 4.8)
 *   $doctor.reviews_count - number of reviews
 *   $doctor.next_slot - nearest available slot text
 *   $doctor.price - consultation price
 *   $doctor.city - city name
 *   $doctor.profile_url - link to profile
 *}
<div class="medbook-card doctor-card">
  <div class="doctor-card__photo-wrap">
    {if $doctor.photo_url}
      <img src="{$doctor.photo_url|escape:'html':'UTF-8'}" alt="{$doctor.name|escape:'html':'UTF-8'}" class="doctor-card__photo">
    {else}
      <div class="doctor-card__photo doctor-card__photo--placeholder">
        {$doctor.name|truncate:1:'':true|escape:'html':'UTF-8'}
      </div>
    {/if}
  </div>

  <div class="doctor-card__info">
    <h3 class="doctor-card__name">
      <a href="{$doctor.profile_url|escape:'html':'UTF-8'}">{$doctor.name|escape:'html':'UTF-8'}</a>
    </h3>
    <div class="doctor-card__specialization">{$doctor.specialization|escape:'html':'UTF-8'}</div>

    <div class="doctor-card__meta">
      {if $doctor.rating}
        <span class="doctor-card__rating">&#9733; {$doctor.rating|escape:'html':'UTF-8'} ({$doctor.reviews_count} {l s='opinii' d='Shop.Theme.Medbook'})</span>
      {/if}
      {if $doctor.city}
        <span class="doctor-card__city">{$doctor.city|escape:'html':'UTF-8'}</span>
      {/if}
      {if $doctor.next_slot}
        <span class="doctor-card__slot">{l s='Najblizszy termin:' d='Shop.Theme.Medbook'} {$doctor.next_slot|escape:'html':'UTF-8'}</span>
      {/if}
      {if $doctor.price}
        <span class="doctor-card__price">{l s='od' d='Shop.Theme.Medbook'} {$doctor.price|escape:'html':'UTF-8'} {l s='zl' d='Shop.Theme.Medbook'}</span>
      {/if}
    </div>

    <div class="doctor-card__actions medbook-mt-2">
      <a href="{$doctor.profile_url|escape:'html':'UTF-8'}" class="medbook-btn medbook-btn--primary medbook-btn--sm">
        {l s='Umow wizyte' d='Shop.Theme.Medbook'}
      </a>
      <a href="{$doctor.profile_url|escape:'html':'UTF-8'}" class="medbook-btn medbook-btn--outline medbook-btn--sm">
        {l s='Zobacz profil' d='Shop.Theme.Medbook'}
      </a>
    </div>
  </div>
</div>
