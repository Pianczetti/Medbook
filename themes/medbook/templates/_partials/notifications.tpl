{**
 * MedBook - Notifications partial
 *}
{if isset($notifications)}
  {if $notifications.error}
    <div class="medbook-alert medbook-alert--error">
      {foreach $notifications.error as $notif}
        <p>{$notif}</p>
      {/foreach}
    </div>
  {/if}
  {if $notifications.warning}
    <div class="medbook-alert medbook-alert--warning">
      {foreach $notifications.warning as $notif}
        <p>{$notif}</p>
      {/foreach}
    </div>
  {/if}
  {if $notifications.success}
    <div class="medbook-alert medbook-alert--success">
      {foreach $notifications.success as $notif}
        <p>{$notif}</p>
      {/foreach}
    </div>
  {/if}
  {if $notifications.info}
    <div class="medbook-alert medbook-alert--info">
      {foreach $notifications.info as $notif}
        <p>{$notif}</p>
      {/foreach}
    </div>
  {/if}
{/if}
