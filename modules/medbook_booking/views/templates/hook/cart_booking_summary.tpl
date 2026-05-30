<div class="snbooking-cart-summary">
    <h4 class="snbooking-cart-summary__title">{l s='Booking Details' d='Modules.Medbookbooking.Shop'}</h4>
    <div class="snbooking-cart-summary__details">
        {if $booking_resource_name}
            <div class="snbooking-cart-summary__row">
                <span class="snbooking-cart-summary__label">{l s='Service' d='Modules.Medbookbooking.Shop'}:</span>
                <span class="snbooking-cart-summary__value">{$booking_resource_name|escape:'htmlall':'UTF-8'}</span>
            </div>
        {/if}
        <div class="snbooking-cart-summary__row">
            <span class="snbooking-cart-summary__label">{l s='Date' d='Modules.Medbookbooking.Shop'}:</span>
            <span class="snbooking-cart-summary__value">{$booking_date|escape:'htmlall':'UTF-8'}</span>
        </div>
        <div class="snbooking-cart-summary__row">
            <span class="snbooking-cart-summary__label">{l s='Time' d='Modules.Medbookbooking.Shop'}:</span>
            <span class="snbooking-cart-summary__value">{$booking_time_start|escape:'htmlall':'UTF-8'} - {$booking_time_end|escape:'htmlall':'UTF-8'}</span>
        </div>
        {if $booking_addons && $booking_addons|count > 0}
            <div class="snbooking-cart-summary__row">
                <span class="snbooking-cart-summary__label">{l s='Add-ons' d='Modules.Medbookbooking.Shop'}:</span>
                <span class="snbooking-cart-summary__value">
                    <ul class="snbooking-cart-summary__addons-list">
                        {foreach from=$booking_addons item=addon}
                            <li>{$addon.name|escape:'htmlall':'UTF-8'} ({$addon.price|escape:'htmlall':'UTF-8'})</li>
                        {/foreach}
                    </ul>
                </span>
            </div>
        {/if}
        <div class="snbooking-cart-summary__row snbooking-cart-summary__row--total">
            <span class="snbooking-cart-summary__label">{l s='Total' d='Modules.Medbookbooking.Shop'}:</span>
            <span class="snbooking-cart-summary__value">{$booking_total_price|number_format:2}</span>
        </div>
        {if $booking_deposit_amount > 0 && $booking_deposit_amount < $booking_total_price}
            <div class="snbooking-cart-summary__row snbooking-cart-summary__row--deposit">
                <span class="snbooking-cart-summary__label">{l s='Deposit due now' d='Modules.Medbookbooking.Shop'}:</span>
                <span class="snbooking-cart-summary__value">{$booking_deposit_amount|number_format:2}</span>
            </div>
        {/if}
    </div>
</div>
