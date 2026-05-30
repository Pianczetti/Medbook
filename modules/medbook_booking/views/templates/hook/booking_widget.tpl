{if $booking_slots && count($booking_slots) > 0}
<div class="snbooking-widget">
    <h3 class="snbooking-widget__title">{l s='Upcoming Available Slots' d='Modules.Medbookbooking.Shop'}</h3>
    <ul class="snbooking-widget__list">
        {foreach from=$booking_slots item=slot name=slotloop}
            {if $smarty.foreach.slotloop.index < 3}
                <li class="snbooking-widget__item">
                    <span class="snbooking-widget__color" style="background-color: {$slot.color|escape:'htmlall':'UTF-8'};"></span>
                    <span class="snbooking-widget__resource">{$slot.resource_name|escape:'htmlall':'UTF-8'}</span>
                    <span class="snbooking-widget__date">{$slot.date|escape:'htmlall':'UTF-8'}</span>
                    <span class="snbooking-widget__time">{$slot.time|escape:'htmlall':'UTF-8'}</span>
                </li>
            {/if}
        {/foreach}
    </ul>
    <a href="{$booking_link|escape:'htmlall':'UTF-8'}" class="snbooking-widget__cta">
        {l s='Book Now' d='Modules.Medbookbooking.Shop'}
    </a>
</div>
{/if}
