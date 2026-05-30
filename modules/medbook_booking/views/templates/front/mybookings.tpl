{extends file='page.tpl'}

{block name='page_content'}
<div class="snbooking-mybookings">
    <h1 class="snbooking-mybookings__title">{l s='My Bookings' d='Modules.Medbookbooking.Shop'}</h1>

    {if isset($cancel_success) && $cancel_success}
        <div class="snbooking-mybookings__alert snbooking-mybookings__alert--success">
            {l s='Your booking has been cancelled successfully.' d='Modules.Medbookbooking.Shop'}
        </div>
    {/if}

    {if isset($cancel_error) && $cancel_error}
        <div class="snbooking-mybookings__alert snbooking-mybookings__alert--error">
            {$cancel_error|escape:'htmlall':'UTF-8'}
        </div>
    {/if}

    {if $bookings && count($bookings) > 0}
        <div class="snbooking-mybookings__list">
            {foreach from=$bookings item=booking}
                <div class="snbooking-mybookings__card">
                    <div class="snbooking-mybookings__card-header">
                        <span class="snbooking-mybookings__reference">#{$booking.reference_code|escape:'htmlall':'UTF-8'}</span>
                        <span class="snbooking-mybookings__status snbooking-mybookings__status--{$booking.status|escape:'htmlall':'UTF-8'}">
                            {$booking.status|escape:'htmlall':'UTF-8'}
                        </span>
                    </div>
                    <div class="snbooking-mybookings__card-body">
                        <div class="snbooking-mybookings__detail">
                            <span class="snbooking-mybookings__label">{l s='Service' d='Modules.Medbookbooking.Shop'}:</span>
                            <span class="snbooking-mybookings__value">{$booking.resource_name|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        <div class="snbooking-mybookings__detail">
                            <span class="snbooking-mybookings__label">{l s='Date' d='Modules.Medbookbooking.Shop'}:</span>
                            <span class="snbooking-mybookings__value">{$booking.booking_date|escape:'htmlall':'UTF-8'}</span>
                        </div>
                        <div class="snbooking-mybookings__detail">
                            <span class="snbooking-mybookings__label">{l s='Time' d='Modules.Medbookbooking.Shop'}:</span>
                            <span class="snbooking-mybookings__value">{$booking.time_start|escape:'htmlall':'UTF-8'} - {$booking.time_end|escape:'htmlall':'UTF-8'}</span>
                        </div>
                    </div>
                    {if $booking.status == 'pending' || $booking.status == 'confirmed'}
                        <div class="snbooking-mybookings__card-footer">
                            <form method="post" action="">
                                <input type="hidden" name="id_booking" value="{$booking.id_booking|intval}" />
                                <button type="submit" name="cancelBooking" class="snbooking-mybookings__cancel-btn"
                                        onclick="return confirm('{l s='Are you sure you want to cancel this booking?' d='Modules.Medbookbooking.Shop'}');">
                                    {l s='Cancel Booking' d='Modules.Medbookbooking.Shop'}
                                </button>
                            </form>
                        </div>
                    {/if}
                </div>
            {/foreach}
        </div>
    {else}
        <div class="snbooking-mybookings__empty">
            <p>{l s='You have no bookings yet.' d='Modules.Medbookbooking.Shop'}</p>
            <a href="{$booking_page_url|escape:'htmlall':'UTF-8'}" class="snbooking-mybookings__book-btn">
                {l s='Book an Appointment' d='Modules.Medbookbooking.Shop'}
            </a>
        </div>
    {/if}
</div>
{/block}
