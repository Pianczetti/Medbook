{extends file='page.tpl'}

{block name='page_title'}
    Moje dokumenty
{/block}

{block name='page_content'}
<div class="medbook-documents-page">

    {if $prescriptions|count > 0}
    <div class="documents-section">
        <h3>Recepty</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Data dodania</th>
                    <th>Pobierz</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$prescriptions item=doc}
                <tr>
                    <td>{$doc.original_name}</td>
                    <td>{$doc.date_add}</td>
                    <td><a href="{$doc.download_url}" class="btn btn-sm btn-primary">Pobierz</a></td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {/if}

    {if $referrals|count > 0}
    <div class="documents-section mt-4">
        <h3>Skierowania</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Data dodania</th>
                    <th>Pobierz</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$referrals item=doc}
                <tr>
                    <td>{$doc.original_name}</td>
                    <td>{$doc.date_add}</td>
                    <td><a href="{$doc.download_url}" class="btn btn-sm btn-primary">Pobierz</a></td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {/if}

    {if $results|count > 0}
    <div class="documents-section mt-4">
        <h3>Wyniki badan</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Data dodania</th>
                    <th>Pobierz</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$results item=doc}
                <tr>
                    <td>{$doc.original_name}</td>
                    <td>{$doc.date_add}</td>
                    <td><a href="{$doc.download_url}" class="btn btn-sm btn-primary">Pobierz</a></td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {/if}

    {if $other_documents|count > 0}
    <div class="documents-section mt-4">
        <h3>Inne dokumenty</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Data dodania</th>
                    <th>Pobierz</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$other_documents item=doc}
                <tr>
                    <td>{$doc.original_name}</td>
                    <td>{$doc.date_add}</td>
                    <td><a href="{$doc.download_url}" class="btn btn-sm btn-primary">Pobierz</a></td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {/if}

    {if $prescriptions|count == 0 && $referrals|count == 0 && $results|count == 0 && $other_documents|count == 0}
    <div class="alert alert-info">
        Nie masz jeszcze zadnych dokumentow.
    </div>
    {/if}

</div>
{/block}
