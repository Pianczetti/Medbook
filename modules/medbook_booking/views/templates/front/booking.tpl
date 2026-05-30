{extends file='page.tpl'}

{block name='page_content'}
<div class="snbooking-page">

    {if isset($booking_success) && $booking_success}
        <div class="snbooking-confirmation">
            <div class="snbooking-confirmation__icon">&#10003;</div>
            <h2 class="snbooking-confirmation__title">{l s='Booking Confirmed' d='Modules.Medbookbooking.Shop'}</h2>
            <p class="snbooking-confirmation__text">
                {l s='Your booking has been registered successfully.' d='Modules.Medbookbooking.Shop'}
            </p>
            <div class="snbooking-confirmation__reference">
                <span class="snbooking-confirmation__label">{l s='Reference Code' d='Modules.Medbookbooking.Shop'}:</span>
                <strong class="snbooking-confirmation__code">{$reference_code|escape:'htmlall':'UTF-8'}</strong>
            </div>
            <p class="snbooking-confirmation__note">
                {l s='Please save this reference code for your records.' d='Modules.Medbookbooking.Shop'}
            </p>
            <a href="{$booking_url|escape:'htmlall':'UTF-8'}" class="snbooking-confirmation__btn">{l s='Make Another Booking' d='Modules.Medbookbooking.Shop'}</a>
        </div>
    {else}
        <h1 class="snbooking-page__title">{l s='Book an Appointment' d='Modules.Medbookbooking.Shop'}</h1>

        {if isset($booking_errors) && $booking_errors}
            <div class="snbooking-errors">
                <ul class="snbooking-errors__list">
                    {foreach from=$booking_errors item=error}
                        <li class="snbooking-errors__item">{$error|escape:'htmlall':'UTF-8'}</li>
                    {/foreach}
                </ul>
            </div>
        {/if}

        <form method="post" action="{$booking_url|escape:'htmlall':'UTF-8'}" class="snbooking-form" id="snbooking-form"
              data-cartbooking-url="{$cartbooking_url|escape:'htmlall':'UTF-8'}"
              data-cart-integration="{if $cart_integration_active}1{else}0{/if}"
              data-show-prices="{if $show_prices}1{else}0{/if}"
              data-secure-key="{$cart_secure_key|escape:'htmlall':'UTF-8'}">

            {* Step 1: Resource Selection *}
            <section class="snbooking-resources">
                <h2 class="snbooking-resources__title">{l s='Select a Service' d='Modules.Medbookbooking.Shop'}</h2>
                <div class="snbooking-resources__grid">
                    {foreach from=$resources item=resource}
                        <label class="snbooking-resource-card{if $selected_resource_id == $resource.id_resource} snbooking-resource-card--selected{/if}">
                            <input type="radio" name="resource_id" value="{$resource.id_resource|intval}"
                                   class="snbooking-resource-card__input"
                                   {if $selected_resource_id == $resource.id_resource}checked{/if}
                                   data-duration="{$resource.duration_minutes|intval}"
                                   data-min-duration="{$resource.min_duration_minutes|intval}"
                                   data-max-duration="{$resource.max_duration_minutes|intval}"
                                   data-base-price="{$resource.base_price|escape:'htmlall':'UTF-8'}" />
                            <span class="snbooking-resource-card__color" style="background-color: {$resource.color|escape:'htmlall':'UTF-8'};"></span>
                            <span class="snbooking-resource-card__name">{$resource.name|escape:'htmlall':'UTF-8'}</span>
                            <span class="snbooking-resource-card__duration">{$resource.duration_minutes|intval} {l s='min' d='Modules.Medbookbooking.Shop'}</span>
                            {if $resource.description}
                                <span class="snbooking-resource-card__desc">{$resource.description|escape:'htmlall':'UTF-8'}</span>
                            {/if}
                        </label>
                    {/foreach}
                </div>
            </section>

            {* Step 2: Date Picker Calendar *}
            <section class="snbooking-calendar" id="snbooking-calendar"
                     data-month="{$calendar_month|intval}"
                     data-year="{$calendar_year|intval}"
                     data-today="{$today|escape:'htmlall':'UTF-8'}"
                     data-max-days="{$max_days_ahead|intval}">
                <h2 class="snbooking-calendar__title">{l s='Select a Date' d='Modules.Medbookbooking.Shop'}</h2>
                <div class="snbooking-calendar__nav">
                    <button type="button" class="snbooking-calendar__btn snbooking-calendar__btn--prev" id="snbooking-cal-prev">&laquo;</button>
                    <span class="snbooking-calendar__month-label" id="snbooking-cal-label"></span>
                    <button type="button" class="snbooking-calendar__btn snbooking-calendar__btn--next" id="snbooking-cal-next">&raquo;</button>
                </div>
                <div class="snbooking-calendar__grid" id="snbooking-cal-grid">
                    <div class="snbooking-calendar__day-header">{l s='Mon' d='Modules.Medbookbooking.Shop'}</div>
                    <div class="snbooking-calendar__day-header">{l s='Tue' d='Modules.Medbookbooking.Shop'}</div>
                    <div class="snbooking-calendar__day-header">{l s='Wed' d='Modules.Medbookbooking.Shop'}</div>
                    <div class="snbooking-calendar__day-header">{l s='Thu' d='Modules.Medbookbooking.Shop'}</div>
                    <div class="snbooking-calendar__day-header">{l s='Fri' d='Modules.Medbookbooking.Shop'}</div>
                    <div class="snbooking-calendar__day-header">{l s='Sat' d='Modules.Medbookbooking.Shop'}</div>
                    <div class="snbooking-calendar__day-header">{l s='Sun' d='Modules.Medbookbooking.Shop'}</div>
                </div>
                <input type="hidden" name="date" id="snbooking-selected-date" value="{$selected_date|escape:'htmlall':'UTF-8'}" />
                <input type="hidden" name="booking_date" id="snbooking-booking-date" value="{$selected_date|escape:'htmlall':'UTF-8'}" />

                {* No-JS fallback: simple date input *}
                <noscript>
                    <div class="snbooking-calendar__nojs">
                        <label for="snbooking-date-input">{l s='Date (YYYY-MM-DD)' d='Modules.Medbookbooking.Shop'}:</label>
                        <input type="date" name="booking_date" id="snbooking-date-input" value="{$selected_date|escape:'htmlall':'UTF-8'}" required />
                        <button type="submit" name="loadSlots" class="snbooking-calendar__nojs-btn">{l s='Show Available Slots' d='Modules.Medbookbooking.Shop'}</button>
                    </div>
                </noscript>
            </section>

            {* Step 3: Time Slots *}
            <section class="snbooking-slots" id="snbooking-slots">
                <h2 class="snbooking-slots__title">{l s='Select a Time' d='Modules.Medbookbooking.Shop'}</h2>
                <div class="snbooking-slots__grid" id="snbooking-slots-grid">
                    {if $available_slots}
                        {foreach from=$available_slots item=slot}
                            <label class="snbooking-slot snbooking-slot--available">
                                <input type="radio" name="time_start" value="{$slot|escape:'htmlall':'UTF-8'}" class="snbooking-slot__input" />
                                <span class="snbooking-slot__time">{$slot|escape:'htmlall':'UTF-8'}</span>
                            </label>
                        {/foreach}
                    {else}
                        <p class="snbooking-slots__empty">{l s='Select a resource and date to see available time slots.' d='Modules.Medbookbooking.Shop'}</p>
                    {/if}
                </div>
                <input type="hidden" name="time_end" id="snbooking-time-end" value="" />
            </section>

            {* Step 3.5: Add-ons Picker *}
            {if $addons && $addons|count > 0}
            <section class="snbooking-addons" id="snbooking-addons">
                <h2 class="snbooking-addons__title">{l s='Add-on Services' d='Modules.Medbookbooking.Shop'}</h2>
                <div class="snbooking-addons__grid">
                    {foreach from=$addons item=addon}
                        <label class="snbooking-addon-card">
                            <input type="checkbox" name="addon_ids[]" value="{$addon.id_addon|intval}" class="snbooking-addon-card__input" data-price="{$addon.price|escape:'htmlall':'UTF-8'}" />
                            <span class="snbooking-addon-card__name">{$addon.name|escape:'htmlall':'UTF-8'}</span>
                            {if $addon.description}<span class="snbooking-addon-card__desc">{$addon.description|escape:'htmlall':'UTF-8'}</span>{/if}
                            {if $show_prices}<span class="snbooking-addon-card__price">{$addon.price|escape:'htmlall':'UTF-8'}</span>{/if}
                        </label>
                    {/foreach}
                </div>
            </section>
            {/if}

            {* Pricing Breakdown *}
            {if $show_prices}
            <section class="snbooking-pricing" id="snbooking-pricing">
                <h2 class="snbooking-pricing__title">{l s='Price Summary' d='Modules.Medbookbooking.Shop'}</h2>
                <div class="snbooking-pricing__breakdown">
                    <div class="snbooking-pricing__row"><span>{l s='Base price' d='Modules.Medbookbooking.Shop'}</span><span id="snbooking-price-base">-</span></div>
                    <div class="snbooking-pricing__row"><span>{l s='Add-ons' d='Modules.Medbookbooking.Shop'}</span><span id="snbooking-price-addons">0.00</span></div>
                    <div class="snbooking-pricing__row snbooking-pricing__row--total"><span>{l s='Total' d='Modules.Medbookbooking.Shop'}</span><span id="snbooking-price-total">-</span></div>
                    {if $deposit_rule}<div class="snbooking-pricing__row snbooking-pricing__row--deposit"><span>{l s='Deposit due now' d='Modules.Medbookbooking.Shop'}</span><span id="snbooking-price-deposit">-</span></div>{/if}
                </div>
            </section>
            {/if}

            {* Refund Policy *}
            {if $refund_policy && $refund_policy|count > 0}
            <section class="snbooking-refund-policy">
                <h2 class="snbooking-refund-policy__title">{l s='Cancellation Policy' d='Modules.Medbookbooking.Shop'}</h2>
                <ul class="snbooking-refund-policy__list">
                    {foreach from=$refund_policy item=rule}
                        <li class="snbooking-refund-policy__item">{$rule|escape:'htmlall':'UTF-8'}</li>
                    {/foreach}
                </ul>
            </section>
            {/if}

            {* Step 4: Contact Information *}
            <section class="snbooking-contact">
                <h2 class="snbooking-contact__title">{l s='Your Information' d='Modules.Medbookbooking.Shop'}</h2>
                <div class="snbooking-contact__fields">
                    <div class="snbooking-contact__field">
                        <label for="snbooking-name" class="snbooking-contact__label">{l s='Full Name' d='Modules.Medbookbooking.Shop'} *</label>
                        <input type="text" id="snbooking-name" name="customer_name"
                               value="{$customer_name|escape:'htmlall':'UTF-8'}" required
                               class="snbooking-contact__input" />
                    </div>
                    <div class="snbooking-contact__field">
                        <label for="snbooking-email" class="snbooking-contact__label">{l s='Email' d='Modules.Medbookbooking.Shop'} *</label>
                        <input type="email" id="snbooking-email" name="customer_email"
                               value="{$customer_email|escape:'htmlall':'UTF-8'}" required
                               class="snbooking-contact__input" />
                    </div>
                    <div class="snbooking-contact__field">
                        <label for="snbooking-phone" class="snbooking-contact__label">{l s='Phone' d='Modules.Medbookbooking.Shop'}</label>
                        <input type="tel" id="snbooking-phone" name="customer_phone"
                               value="" class="snbooking-contact__input" />
                    </div>
                    <div class="snbooking-contact__field snbooking-contact__field--full">
                        <label for="snbooking-notes" class="snbooking-contact__label">{l s='Notes' d='Modules.Medbookbooking.Shop'}</label>
                        <textarea id="snbooking-notes" name="notes" rows="3"
                                  class="snbooking-contact__textarea"></textarea>
                    </div>
                </div>
            </section>

            {* Submit *}
            <div class="snbooking-submit">
                {if $cart_integration_active}
                    <button type="submit" name="submitBooking" class="snbooking-submit__btn" id="snbooking-add-to-cart-btn">
                        {l s='Add to Cart' d='Modules.Medbookbooking.Shop'}
                    </button>
                {else}
                    <button type="submit" name="submitBooking" class="snbooking-submit__btn">
                        {l s='Confirm Booking' d='Modules.Medbookbooking.Shop'}
                    </button>
                {/if}
            </div>

        </form>
    {/if}

</div>
{/block}
