{extends file='page.tpl'}

{block name='page_title'}
    Zarzadzanie zgodami (RODO)
{/block}

{block name='page_content'}
<div class="medbook-consent-page">
    <div class="consent-intro">
        <p>Zgodnie z Rozporzadzeniem o Ochronie Danych Osobowych (RODO), masz prawo do zarzadzania swoimi zgodami na przetwarzanie danych osobowych.</p>
    </div>

    <div class="consent-section">
        <h3>Zgoda na przetwarzanie danych medycznych</h3>
        <p>Wyrazam zgode na przetwarzanie moich danych medycznych w celu realizacji uslugi rezerwacji wizyt lekarskich.</p>
        {if $consents.medical_data}
            <span class="badge badge-success">Udzielona</span>
            <form method="post" action="{$consent_action_url}">
                <input type="hidden" name="action" value="revoke">
                <input type="hidden" name="consent_type" value="medical_data">
                <input type="hidden" name="token" value="{$consent_token}">
                <button type="submit" class="btn btn-outline-danger btn-sm mt-2">Wycofaj zgode</button>
            </form>
        {else}
            <span class="badge badge-secondary">Nieudzielona</span>
            <form method="post" action="{$consent_action_url}">
                <input type="hidden" name="action" value="grant">
                <input type="hidden" name="consent_type" value="medical_data">
                <input type="hidden" name="token" value="{$consent_token}">
                <button type="submit" class="btn btn-primary btn-sm mt-2">Udziel zgody</button>
            </form>
        {/if}
    </div>

    <div class="consent-section mt-4">
        <h3>Zgoda na komunikacje marketingowa</h3>
        <p>Wyrazam zgode na otrzymywanie informacji marketingowych dotyczacych ofert zdrowotnych i promocji.</p>
        {if $consents.marketing}
            <span class="badge badge-success">Udzielona</span>
            <form method="post" action="{$consent_action_url}">
                <input type="hidden" name="action" value="revoke">
                <input type="hidden" name="consent_type" value="marketing">
                <input type="hidden" name="token" value="{$consent_token}">
                <button type="submit" class="btn btn-outline-danger btn-sm mt-2">Wycofaj zgode</button>
            </form>
        {else}
            <span class="badge badge-secondary">Nieudzielona</span>
            <form method="post" action="{$consent_action_url}">
                <input type="hidden" name="action" value="grant">
                <input type="hidden" name="consent_type" value="marketing">
                <input type="hidden" name="token" value="{$consent_token}">
                <button type="submit" class="btn btn-primary btn-sm mt-2">Udziel zgody</button>
            </form>
        {/if}
    </div>

    <div class="consent-section mt-4">
        <h3>Zgoda na udostepnianie danych podmiotom trzecim</h3>
        <p>Wyrazam zgode na udostepnianie moich danych osobowych wspolpracujacym placowkom medycznym.</p>
        {if $consents.third_party}
            <span class="badge badge-success">Udzielona</span>
            <form method="post" action="{$consent_action_url}">
                <input type="hidden" name="action" value="revoke">
                <input type="hidden" name="consent_type" value="third_party">
                <input type="hidden" name="token" value="{$consent_token}">
                <button type="submit" class="btn btn-outline-danger btn-sm mt-2">Wycofaj zgode</button>
            </form>
        {else}
            <span class="badge badge-secondary">Nieudzielona</span>
            <form method="post" action="{$consent_action_url}">
                <input type="hidden" name="action" value="grant">
                <input type="hidden" name="consent_type" value="third_party">
                <input type="hidden" name="token" value="{$consent_token}">
                <button type="submit" class="btn btn-primary btn-sm mt-2">Udziel zgody</button>
            </form>
        {/if}
    </div>

    <div class="consent-rights mt-5">
        <h3>Twoje prawa</h3>
        <ul>
            <li><strong>Prawo do bycia zapomnianym</strong> - mozesz zadac usuniecia swoich danych kontaktujac sie z nami.</li>
            <li><strong>Eksport danych</strong> - mozesz pobrac wszystkie swoje dane w formacie JSON.</li>
        </ul>
        <a href="{$consent_action_url}?action=export" class="btn btn-secondary">
            Eksport danych
        </a>
    </div>

    {if $consent_history|count > 0}
    <div class="consent-history mt-5">
        <h3>Historia zgod</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Typ zgody</th>
                    <th>Data udzielenia</th>
                    <th>Data wycofania</th>
                    <th>Wersja</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$consent_history item=entry}
                <tr>
                    <td>
                        {if $entry.consent_type == 'medical_data'}Dane medyczne
                        {elseif $entry.consent_type == 'marketing'}Marketing
                        {elseif $entry.consent_type == 'third_party'}Podmioty trzecie
                        {else}{$entry.consent_type}{/if}
                    </td>
                    <td>{$entry.granted_at}</td>
                    <td>{if $entry.revoked_at}{$entry.revoked_at}{else}-{/if}</td>
                    <td>{$entry.version}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {/if}
</div>
{/block}
