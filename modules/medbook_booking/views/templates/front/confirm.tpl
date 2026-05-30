{extends file='page.tpl'}

{block name='page_content'}
<div class="snbooking-confirm-page">

    {if $confirm_success}
        <div class="snbooking-confirm-result snbooking-confirm-result--success">
            {if $confirm_action == 'confirm'}
                <div class="snbooking-confirm-result__icon" style="color:#27ae60;font-size:48px;">&#10003;</div>
                <h2 class="snbooking-confirm-result__title">{l s='Booking Confirmed' d='Modules.Medbookbooking.Shop'}</h2>
                <p class="snbooking-confirm-result__text">
                    {l s='Your booking has been confirmed successfully. We look forward to seeing you!' d='Modules.Medbookbooking.Shop'}
                </p>
            {else}
                <div class="snbooking-confirm-result__icon" style="color:#e74c3c;font-size:48px;">&#10007;</div>
                <h2 class="snbooking-confirm-result__title">{l s='Booking Cancelled' d='Modules.Medbookbooking.Shop'}</h2>
                <p class="snbooking-confirm-result__text">
                    {l s='Your booking has been cancelled. If you change your mind, feel free to book again.' d='Modules.Medbookbooking.Shop'}
                </p>
            {/if}
        </div>
    {else}
        <div class="snbooking-confirm-result snbooking-confirm-result--error">
            <div class="snbooking-confirm-result__icon" style="color:#e74c3c;font-size:48px;">&#9888;</div>
            <h2 class="snbooking-confirm-result__title">{l s='Error' d='Modules.Medbookbooking.Shop'}</h2>
            <p class="snbooking-confirm-result__text">
                {$confirm_error|escape:'htmlall':'UTF-8'}
            </p>
        </div>
    {/if}

</div>
{/block}
