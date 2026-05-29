<div class="snbooking-product-btn mt-3">
  <a href="{$booking_url|escape:'htmlall':'UTF-8'}" class="btn btn-primary btn-lg w-100">
    <i class="material-icons">event_available</i>
    {l s='Book appointment' d='Modules.Medbookbooking.Shop'}
    {if $booking_resource_name}
      - {$booking_resource_name|escape:'htmlall':'UTF-8'}
    {/if}
  </a>
</div>
