{**
 * MedBook - Slot picker widget for module views
 *}
<div class="slot-picker" data-resource-id="{$resource_id|intval}" data-slots-url="{$slots_ajax_url|escape:'html':'UTF-8'}">
  <div class="slot-picker__header">
    <h4 class="slot-picker__title">{l s='Wybierz termin' d='Modules.Medbookbooking.Front'}</h4>
    <div class="slot-picker__nav">
      <button type="button" class="slot-picker__nav-btn" data-direction="prev">&lsaquo;</button>
      <button type="button" class="slot-picker__nav-btn" data-direction="next">&rsaquo;</button>
    </div>
  </div>

  <div class="slot-picker__days">
    <div class="slot-picker__day-header">Pon</div>
    <div class="slot-picker__day-header">Wt</div>
    <div class="slot-picker__day-header">Sr</div>
    <div class="slot-picker__day-header">Czw</div>
    <div class="slot-picker__day-header">Pt</div>
    <div class="slot-picker__day-header">Sob</div>
    <div class="slot-picker__day-header">Nd</div>

    {if isset($calendar_days)}
      {foreach $calendar_days as $day}
        <div class="slot-picker__day{if $day.is_today} slot-picker__day--today{/if}{if $day.is_selected} slot-picker__day--selected{/if}{if !$day.is_available} slot-picker__day--disabled{/if}"
             data-date="{$day.date|escape:'html':'UTF-8'}">
          {$day.day_number}
        </div>
      {/foreach}
    {/if}
  </div>

  <div class="slot-picker__times">
    {if isset($available_slots) && $available_slots|count > 0}
      {foreach $available_slots as $slot}
        <div class="slot-picker__time" data-time="{$slot.time|escape:'html':'UTF-8'}">
          {$slot.time|escape:'html':'UTF-8'}
        </div>
      {/foreach}
    {else}
      <div class="medbook-text-center medbook-text-muted">
        {l s='Wybierz date, aby zobaczyc dostepne terminy' d='Modules.Medbookbooking.Front'}
      </div>
    {/if}
  </div>

  <input type="hidden" name="booking_date" value="{if isset($selected_date)}{$selected_date|escape:'html':'UTF-8'}{/if}">
  <input type="hidden" name="time_start" value="">
</div>
