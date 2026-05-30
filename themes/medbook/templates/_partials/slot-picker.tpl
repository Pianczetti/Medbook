{**
 * MedBook - Reusable slot picker widget
 * Calendar / time slot selection
 * Variables expected:
 *   $resource_id - ID of the doctor/resource
 *   $slots_ajax_url - URL for AJAX slot loading
 *   $selected_date - currently selected date (Y-m-d)
 *   $available_slots - array of time slots
 *   $calendar_days - array of day objects for the week view
 *}
<div class="slot-picker" data-resource-id="{$resource_id|intval}" data-slots-url="{$slots_ajax_url|escape:'html':'UTF-8'}">
  <div class="slot-picker__header">
    <h4 class="slot-picker__title">{l s='Wybierz termin' d='Shop.Theme.Medbook'}</h4>
    <div class="slot-picker__nav">
      <button type="button" class="slot-picker__nav-btn" data-direction="prev">&lsaquo;</button>
      <button type="button" class="slot-picker__nav-btn" data-direction="next">&rsaquo;</button>
    </div>
  </div>

  <div class="slot-picker__days">
    <div class="slot-picker__day-header">{l s='Pon' d='Shop.Theme.Medbook'}</div>
    <div class="slot-picker__day-header">{l s='Wt' d='Shop.Theme.Medbook'}</div>
    <div class="slot-picker__day-header">{l s='Sr' d='Shop.Theme.Medbook'}</div>
    <div class="slot-picker__day-header">{l s='Czw' d='Shop.Theme.Medbook'}</div>
    <div class="slot-picker__day-header">{l s='Pt' d='Shop.Theme.Medbook'}</div>
    <div class="slot-picker__day-header">{l s='Sob' d='Shop.Theme.Medbook'}</div>
    <div class="slot-picker__day-header">{l s='Nd' d='Shop.Theme.Medbook'}</div>

    {if isset($calendar_days)}
      {foreach $calendar_days as $day}
        <div class="slot-picker__day{if $day.is_today} slot-picker__day--today{/if}{if $day.is_selected} slot-picker__day--selected{/if}{if !$day.is_available} slot-picker__day--disabled{/if}"
             data-date="{$day.date|escape:'html':'UTF-8'}">
          {$day.day_number}
        </div>
      {/foreach}
    {/if}
  </div>

  <div class="slot-picker__times-label medbook-mb-1">
    <strong>{l s='Dostepne godziny' d='Shop.Theme.Medbook'}:</strong>
  </div>

  <div class="slot-picker__times">
    {if isset($available_slots) && $available_slots|count > 0}
      {foreach $available_slots as $slot}
        <div class="slot-picker__time{if isset($selected_time) && $selected_time == $slot.time} slot-picker__time--selected{/if}"
             data-time="{$slot.time|escape:'html':'UTF-8'}">
          {$slot.time|escape:'html':'UTF-8'}
          {if isset($slot.formatted_price)}
            <br><small>{$slot.formatted_price|escape:'html':'UTF-8'}</small>
          {/if}
        </div>
      {/foreach}
    {else}
      <div class="medbook-text-center medbook-text-muted">
        {l s='Wybierz date, aby zobaczyc dostepne terminy' d='Shop.Theme.Medbook'}
      </div>
    {/if}
  </div>

  <input type="hidden" name="booking_date" value="{if isset($selected_date)}{$selected_date|escape:'html':'UTF-8'}{/if}">
  <input type="hidden" name="time_start" value="">
</div>
