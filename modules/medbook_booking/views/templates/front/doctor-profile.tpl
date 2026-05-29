{**
 * MedBook - Doctor profile template (module view)
 * Loaded by doctorprofile controller
 *}
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
<section class="medbook-section">
  <div class="doctor-profile">
    <aside class="doctor-profile__sidebar">
      <div class="medbook-card">
        {if isset($doctor.photo_url) && $doctor.photo_url}
          <img src="{$doctor.photo_url|escape:'html':'UTF-8'}" alt="{$doctor.name|escape:'html':'UTF-8'}" class="doctor-profile__photo">
        {else}
          <div class="doctor-profile__photo doctor-card__photo--placeholder">
            {$doctor.name|truncate:1:'':true|escape:'html':'UTF-8'}
          </div>
        {/if}

        <h1 class="medbook-text-center">{$doctor.name|escape:'html':'UTF-8'}</h1>
        <p class="medbook-text-center medbook-text-muted">{$doctor.specialization|escape:'html':'UTF-8'}</p>

        {if isset($doctor.rating) && $doctor.rating}
          <div class="medbook-text-center medbook-mb-2">
            <span class="doctor-card__rating">&#9733; {$doctor.rating|escape:'html':'UTF-8'}</span>
            <span class="medbook-text-muted">({$doctor.reviews_count} {l s='opinii' d='Modules.Medbookbooking.Front'})</span>
          </div>
        {/if}

        {if isset($doctor.pwz_number) && $doctor.pwz_number}
          <p class="medbook-text-muted" style="font-size:0.85rem;">
            {l s='Numer PWZ:' d='Modules.Medbookbooking.Front'} {$doctor.pwz_number|escape:'html':'UTF-8'}
          </p>
        {/if}

        <a href="#booking-section" class="medbook-btn medbook-btn--primary medbook-btn--block medbook-mt-2">
          {l s='Umow wizyte' d='Modules.Medbookbooking.Front'}
        </a>
      </div>
    </aside>

    <div class="doctor-profile__main">
      {if isset($doctor.bio)}
        <div class="doctor-profile__section">
          <h2 class="doctor-profile__section-title">{l s='O lekarzu' d='Modules.Medbookbooking.Front'}</h2>
          <p>{$doctor.bio|escape:'html':'UTF-8'}</p>
        </div>
      {/if}

      {if isset($doctor.specializations) && $doctor.specializations|count > 0}
        <div class="doctor-profile__section">
          <h2 class="doctor-profile__section-title">{l s='Specjalizacje' d='Modules.Medbookbooking.Front'}</h2>
          <ul>
            {foreach $doctor.specializations as $spec}
              <li>{$spec.name|escape:'html':'UTF-8'}</li>
            {/foreach}
          </ul>
        </div>
      {/if}

      {if isset($doctor.education) && $doctor.education|count > 0}
        <div class="doctor-profile__section">
          <h2 class="doctor-profile__section-title">{l s='Wyksztalcenie' d='Modules.Medbookbooking.Front'}</h2>
          <ul>
            {foreach $doctor.education as $edu}
              <li>{$edu|escape:'html':'UTF-8'}</li>
            {/foreach}
          </ul>
        </div>
      {/if}

      {if isset($doctor.certifications) && $doctor.certifications|count > 0}
        <div class="doctor-profile__section">
          <h2 class="doctor-profile__section-title">{l s='Certyfikaty' d='Modules.Medbookbooking.Front'}</h2>
          <ul>
            {foreach $doctor.certifications as $cert}
              <li>{$cert|escape:'html':'UTF-8'}</li>
            {/foreach}
          </ul>
        </div>
      {/if}

      {if isset($doctor.experience)}
        <div class="doctor-profile__section">
          <h2 class="doctor-profile__section-title">{l s='Doswiadczenie' d='Modules.Medbookbooking.Front'}</h2>
          <p>{$doctor.experience|escape:'html':'UTF-8'}</p>
        </div>
      {/if}

      {if isset($doctor.clinics) && $doctor.clinics|count > 0}
        <div class="doctor-profile__section">
          <h2 class="doctor-profile__section-title">{l s='Lokalizacje' d='Modules.Medbookbooking.Front'}</h2>
          <div class="medbook-grid medbook-grid--2">
            {foreach $doctor.clinics as $clinic}
              <div class="medbook-card medbook-card--flat">
                <strong>{$clinic.name|escape:'html':'UTF-8'}</strong>
                <p class="medbook-text-muted">{$clinic.address|escape:'html':'UTF-8'}</p>
              </div>
            {/foreach}
          </div>
        </div>
      {/if}

      <div class="doctor-profile__section" id="booking-section">
        <h2 class="doctor-profile__section-title">{l s='Dostepne terminy' d='Modules.Medbookbooking.Front'}</h2>
        {include file='module:medbook_booking/views/templates/front/slot-picker-widget.tpl'}
        <form method="post" action="{$link->getModuleLink('medbook_booking', 'bookingflow')}" class="medbook-mt-2">
          <input type="hidden" name="doctor_id" value="{$doctor.id|intval}">
          <input type="hidden" name="resource_id" value="{$doctor.resource_id|intval}">
          <input type="hidden" name="step" value="4">
          <button type="submit" class="medbook-btn medbook-btn--primary medbook-btn--lg medbook-btn--block">
            {l s='Potwierdz termin i rezerwuj' d='Modules.Medbookbooking.Front'}
          </button>
        </form>
      </div>
    </div>
  </div>
</section>
{/block}
