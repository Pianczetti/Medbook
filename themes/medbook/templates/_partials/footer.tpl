{**
 * MedBook - Footer partial
 * Clinic info, quick links, working hours
 *}
<footer class="medbook-footer">
  <div class="medbook-footer__grid">
    <div class="medbook-footer__col">
      <h4 class="medbook-footer__heading">MedBook</h4>
      <p>{l s='Profesjonalna platforma do rezerwacji wizyt lekarskich. Znajdz lekarza i umow wizyte online.' d='Shop.Theme.Medbook'}</p>
    </div>

    <div class="medbook-footer__col">
      <h4 class="medbook-footer__heading">{l s='Szybkie linki' d='Shop.Theme.Medbook'}</h4>
      <ul class="medbook-footer__links">
        <li><a href="{$urls.base_url}">{l s='Strona glowna' d='Shop.Theme.Medbook'}</a></li>
        <li><a href="{$link->getModuleLink('medbook_booking', 'doctorsearch')}">{l s='Szukaj lekarza' d='Shop.Theme.Medbook'}</a></li>
        <li><a href="{$urls.pages.contact}">{l s='Kontakt' d='Shop.Theme.Medbook'}</a></li>
      </ul>
    </div>

    <div class="medbook-footer__col">
      <h4 class="medbook-footer__heading">{l s='Informacje' d='Shop.Theme.Medbook'}</h4>
      <ul class="medbook-footer__links">
        <li><a href="#">{l s='O nas' d='Shop.Theme.Medbook'}</a></li>
        <li><a href="#">{l s='Regulamin' d='Shop.Theme.Medbook'}</a></li>
        <li><a href="#">{l s='Polityka prywatnosci' d='Shop.Theme.Medbook'}</a></li>
        <li><a href="#">{l s='RODO' d='Shop.Theme.Medbook'}</a></li>
      </ul>
    </div>

    <div class="medbook-footer__col">
      <h4 class="medbook-footer__heading">{l s='Godziny pracy' d='Shop.Theme.Medbook'}</h4>
      <ul class="medbook-footer__links">
        <li>{l s='Poniedzialek - Piatek: 7:00 - 20:00' d='Shop.Theme.Medbook'}</li>
        <li>{l s='Sobota: 8:00 - 14:00' d='Shop.Theme.Medbook'}</li>
        <li>{l s='Niedziela: Zamkniete' d='Shop.Theme.Medbook'}</li>
      </ul>
    </div>
  </div>

  <div class="medbook-footer__bottom">
    <p>&copy; {$smarty.now|date_format:"%Y"} MedBook. {l s='Wszelkie prawa zastrzezone.' d='Shop.Theme.Medbook'}</p>
  </div>
</footer>
