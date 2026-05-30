# MedBook - Polska platforma do rezerwacji wizyt lekarskich

MedBook to fork systemu PrestaShop 9, przeksztalcony w kompletna platforme do rezerwacji wizyt lekarskich dla polskiego rynku medycznego.

## Opis

MedBook laczy sprawdzona architekture e-commerce PrestaShop z funkcjonalnoscia dedykowana placowkom medycznym. Platforma umozliwia pacjentom rezerwacje wizyt lekarskich online, zarzadzanie dokumentacja medyczna oraz komunikacje z lekarzami.

## Glowne funkcje

### Rezerwacja wizyt
- Wyszukiwanie lekarzy wedlug specjalizacji, lokalizacji i dostepnosci
- Rezerwacja wizyt stacjonarnych i online (wideokonferencja)
- Kalendarz dostepnosci z widokiem dziennym/tygodniowym
- Potwierdzenie rezerwacji przez e-mail
- Przypomnienia o wizytach (24h przed wizyta)
- Lista oczekujacych z automatycznym powiadamianiem

### Zarzadzanie lekarzami
- Profile lekarzy z numer PWZ, specjalizacjami i certyfikatami
- Harmonogramy pracy z obsluga wyjatkow
- Przypisanie do wielu przychodni
- Konsultacje online i stacjonarne

### Panel pacjenta
- Dashboard z nadchodzacymi wizytami
- Historia wizyt
- Dokumenty medyczne (recepty, skierowania, wyniki badan)
- Zarzadzanie zgodami RODO
- Eksport danych osobowych (prawo do przenoszenia danych)

### E-recepty
- Generowanie recept w formacie PDF zgodnym z polskimi standardami
- Dane pacjenta (imie, nazwisko, PESEL)
- Dane lekarza (imie, nazwisko, numer PWZ)
- Lista lekow z dawkowaniem, iloscia i informacja o refundacji
- Kod diagnozy ICD-10
- Powiadomienie pacjenta o gotowej recepcie

### Wizyty online
- Automatyczne generowanie linkow do wideokonferencji
- Link wysylany w e-mailu potwierdzajacym
- Obsluga wizyt typu "online" w calym systemie

### Zgodnosc z RODO
- Zarzadzanie zgodami na przetwarzanie danych medycznych
- Zgoda na komunikacje marketingowa
- Zgoda na udostepnianie danych podmiotom trzecim
- Historia udzielonych i wycofanych zgod
- Eksport danych pacjenta w formacie JSON
- Prawo do bycia zapomnianym

### System cenowy
- Ceny bazowe per lekarz/usluga
- Reguly cenowe (procentowe, kwotowe) z priorytetami
- Obsluga NFZ, wizyt prywatnych i pakietow
- Zaliczki i reguly zwrotow

### Powiadomienia
- E-mail: potwierdzenie rezerwacji, przypomnienie, anulowanie, recepta gotowa
- SMS (z mozliwoscia integracji z bramka SMS)
- Powiadomienia z listy oczekujacych

## Wymagania systemowe

- PHP 8.1 lub nowszy
- MySQL 5.7+ lub MariaDB 10.2+
- Composer 2.x
- Node.js 16+ (budowanie assetow front-end)
- Serwer HTTP (Apache/Nginx)

## Instalacja

### 1. Klonowanie repozytorium

```bash
git clone https://github.com/pfrancik/PrestaShop.git medbook
cd medbook
```

### 2. Instalacja zaleznosci PHP

```bash
composer install
```

### 3. Konfiguracja bazy danych

Utworz baze danych MySQL i skonfiguruj polaczenie w pliku `.env`:

```
DATABASE_HOST=localhost
DATABASE_NAME=medbook
DATABASE_USER=root
DATABASE_PASSWORD=
DATABASE_PREFIX=ps_
```

### 4. Uruchomienie instalatora

Przejdz do `/install-dev/` w przegladarce i postepuj zgodnie z instrukcjami.

### 5. Aktywacja modulu

Modul `medbook_booking` jest instalowany automatycznie z domyslnymi danymi (specjalizacje, przykladowa przychodnia).

## Konfiguracja

### Panel administracyjny

Po zalogowaniu do panelu admina, przejdz do zakladki **MedBook** w menu:

- **Rezerwacje** - zarzadzanie rezerwacjami wizyt
- **Lekarze** - profile lekarzy, specjalizacje, harmonogramy
- **Przychodnie** - dane przychodni
- **Specjalizacje** - lista specjalizacji medycznych
- **Dokumenty** - dokumentacja medyczna pacjentow
- **Harmonogram** - grafiki pracy lekarzy
- **Reguly Cenowe** - konfiguracja cen wizyt
- **Dodatki** - dodatkowe uslugi do wizyt
- **Reguly Zwrotow** - polityka anulowania
- **Reguly Zaliczek** - konfiguracja zaliczek
- **Ustawienia** - ogolna konfiguracja platformy
- **Kalendarz** - widok kalendarza wizyt
- **Wizyty Cykliczne** - zarzadzanie sugerowanymi wizytami
- **Lista Oczekujacych** - zarzadzanie lista oczekujacych

### Ustawienia kluczowe

| Ustawienie | Opis | Domyslna wartosc |
|---|---|---|
| MEDBOOK_BOOKING_CONFIRM_MODE | Tryb potwierdzania (auto/manual) | auto |
| MEDBOOK_BOOKING_MAX_DAYS_AHEAD | Maks. dni na przod do rezerwacji | 30 |
| MEDBOOK_BOOKING_MIN_HOURS_ADVANCE | Min. godziny przed wizyta | 2 |
| MEDBOOK_ALLOW_ONLINE_VISITS | Obsluga wizyt online | 1 (tak) |
| MEDBOOK_INSURANCE_TYPES | Dostepne typy ubezpieczenia | NFZ,prywatne,pakiet |

## Motyw

MedBook uzywa dedykowanego motywu `medbook` opartego na `_core` z PrestaShop 9, dostosowanego do potrzeb platformy medycznej.

## Struktura modulu

```
modules/medbook_booking/
├── config/
│   ├── routes.yml          # Trasy administracyjne
│   ├── services.yml        # Rejestracja serwisow DI
│   └── admin/services.yml  # Serwisy administracyjne (Grid)
├── controllers/front/      # Kontrolery front-office
├── mails/pl/               # Szablony e-mail (polski)
├── sql/                    # Skrypty SQL
├── src/
│   ├── Controller/Admin/   # Kontrolery panelu admina
│   ├── Entity/             # Encje Doctrine
│   ├── Form/               # Formularze Symfony
│   ├── Grid/               # System Grid (listy, filtry)
│   └── Service/            # Logika biznesowa
└── views/templates/        # Szablony (Twig admin, Smarty front)
```

## Licencja

- Pliki rdzenia: OSL-3.0
- Modul medbook_booking: AFL-3.0

## Autor

MedBook Team
