# Kaarten – uitleg voor de docent

Het project wordt in 6 kaarten uitgedeeld als GitHub Issues, door `.github/workflows/kaarten.yml`.

## Eenmalig instellen

1. Maak in de studenten-organisatie een Actions-variabele aan: **Settings → Secrets and variables → Actions → Variables → New organization variable**.
   - Naam: `DOCENTEN`
   - Waarde: de GitHub-gebruikersnamen van de docenten, gescheiden door komma's, bijvoorbeeld `docent1,docent2`
   - Repository access: alle repositories
2. Controleer bij **Settings → Actions → General** van de organisatie dat Actions aan staat.
3. Bij **Settings → Member privileges**:
   - **Base permissions**: **No permission** of **Read** (anders kunnen studenten in elkaars repository pushen)
   - **Repository creation**: **Public** aanvinken (anders kunnen studenten geen repository uit de template maken)

## Maandag (vervangende docent)

Studenten hebben nog geen toegang tot GitHub. Deel de nummers uit. Studenten vullen het [aanmeldformulier](https://forms.cloud.microsoft/Pages/ResponsePage.aspx?id=vyw7T26xI0KVVMZ_usI9afL2mzJ0VrJNsJ-qckXhwwNURVVLVzBPOVk2MzVKNzNQNFhYU01UTEdHMi4u) in (nummer, GitHub-gebruikersnaam, thema) en werken aan kaart 1 (de ERD) - zie het maandagblok bovenaan de README.

**Excel-lijst**: open het formulier in Forms → **Antwoorden** → **Openen in Excel**. Voeg rechts een kolom **ERD afgetekend** toe. Controleer per rij of het thema klopt met het nummer in `Studentverdeling.md`.

**Aftekenen**: bekijk de ERD op het scherm van de student aan de hand van *Klaar als* op [kaart 1](kaarten/1.md). Goed? Zet `ja` in de kolom **ERD afgetekend**. Twijfel? Laat de kolom leeg en zet er een korte notitie bij. De vaste docent gebruikt dezelfde Excel dinsdag om iedereen uit te nodigen.

## Dinsdag (vaste docent)

Studenten met een vinkje typen `/klaar` zodra hun ERD in hun repository staat. Sluit hun kaart 1 dan meteen als **completed** - beoordelen is maandag al gebeurd.

## Vóór dinsdag (vaste docent)

Nodig elke gebruikersnaam uit via **People → Invite member** (rol: Member). Studenten accepteren dinsdag de uitnodiging en volgen dan de setup in de README.

Alleen wie in `DOCENTEN` staat, kan een kaart aftekenen. Sluit een student zelf een kaart, dan heropent de workflow hem.

## Tijdens het project

**Je wachtrij** - zet deze zoekopdracht als bladwijzer:

```
https://github.com/issues?q=org:Nova-College-Haarlem-Students+is:issue+is:open+label:aftekenen+sort:created-asc
```   

Wie bovenaan staat, wacht het langst.

**Aftekenen** - sluit de kaart met **Close as completed**. De volgende kaart verschijnt meteen.

**Nog niet goed** - laat een reactie achter en haal het label `aftekenen` weg. De student past aan en typt opnieuw `/klaar`. Sluiten met **Close as not planned** levert ook geen nieuwe kaart op.

**Voortgang** - wie op welke kaart zit:

```
https://github.com/issues?q=org:Nova-College-Haarlem-Students+is:issue+is:open+in:title+Kaart
```

## Hoe het werkt

| Gebeurtenis | Wat de workflow doet |
|-------------|----------------------|
| Student pusht (eerste keer) | Opent kaart 1 |
| Student typt `/klaar` | Controleert `thema.txt`, zet label `aftekenen` |
| Docent sluit kaart als completed | Opent de volgende kaart |
| Student sluit kaart zelf | Heropent de kaart |

- Kaartteksten: `.github/kaarten/1.md` t/m `6.md`. De eerste regel is de titel.
- In `Studentverdeling.md` staan alleen nummers (privacy). De lijst welke student welk nummer heeft, houdt de docent zelf bij, buiten GitHub.
- Userstories per thema: `.github/kaarten/userstories/`. Ze komen op kaart 3 (bezoeker + developer), 4 (klant) en 5 (medewerker + admin), op basis van `thema.txt`.
- De kaarten staan in de repository van de student. Wie in `.github` gaat zoeken, kan vooruitlezen.
- Kaart 1 komt niet? Laat de student bij **Actions → Kaarten → Run workflow** de workflow handmatig starten.
