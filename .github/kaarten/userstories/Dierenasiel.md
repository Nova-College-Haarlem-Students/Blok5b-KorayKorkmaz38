### Bezoeker
1. Als bezoeker wil ik een overzicht van alle beschikbare dieren zien, zodat ik kan bladeren door het aanbod. *(H1)*
2. Als bezoeker wil ik op een dier klikken voor de detailpagina. Bestaat het dier niet, dan zie ik een nette 404-pagina. *(H7)*
3. Als bezoeker wil ik filteren op soort én geslacht tegelijk. De keuzelijst met soorten komt uit de database, zonder dubbele waardes. *(H6, DISTINCT)*
4. Als bezoeker wil ik de dieren sorteren op prijs (laag-hoog en hoog-laag) en op naam. *(ORDER BY)*

### Lid
5. Als bezoeker wil ik een account aanmaken; mijn wachtwoord wordt gehasht opgeslagen en het formulier wordt gecontroleerd. *(H2)*
6. Als lid wil ik inloggen en uitloggen. *(H2)*
7. Als lid wil ik op de detailpagina met één klik een adoptie-aanvraag indienen, zonder dat de pagina herlaadt. Heb ik al een aanvraag voor dit dier, dan krijg ik daar een melding van. *(H4, AJAX)*
8. Als lid wil ik een overzicht van **mijn** aanvragen zien, met de naam en soort van het dier en de status. *(H6, LEFT JOIN)*
9. Als lid wil ik mijn eigen gegevens kunnen aanpassen. *(H3)*

### Medewerker (verzorger)
10. Als verzorger wil ik inloggen op een apart beheergedeelte. Een lid dat een beheerpagina opent, ziet een 403-pagina. *(H5, H7)*
11. Als verzorger wil ik een nieuw dier toevoegen; lege of foute velden geven een foutmelding. Geboortedatum en chipnummer mogen leeg blijven. *(H1, H2, NULL)*
12. Als verzorger wil ik de gegevens van een dier en de status van een aanvraag kunnen aanpassen. *(H3)*
13. Als verzorger wil ik een dier uit de lijst halen zonder dat het echt verwijderd wordt, en het later kunnen terugzetten. *(H4)*
14. Als verzorger wil ik een overzicht van alle aanvragen, met het dier en de aanvrager erbij. *(H6, LEFT JOIN)*
15. Als verzorger wil ik een lijst van dieren **zonder aanvragen** zien. *(LEFT JOIN)*

### Admin (coördinator)
16. Als admin wil ik een statistiekenpagina met: het aantal dieren per soort, de gemiddelde, laagste en hoogste adoptieprijs, de opbrengst van goedgekeurde adopties, en de soorten met meer dan drie aanvragen. *(COUNT, AVG, MIN, MAX, SUM, GROUP BY, HAVING)*
17. Als admin wil ik een logboek zien van wie wat heeft gedaan in het beheergedeelte. Een medewerker die de statistieken of het logboek opent, ziet een 403-pagina. *(H5, H7)*

### Developer
18. Als developer wil ik dat alle queries via PDO met prepared statements lopen en alle output via `htmlspecialchars()`. *(H1, H2)*
19. Als developer wil ik dat formulieren alleen via `POST` verwerkt worden en dat id's in de URL gecontroleerd worden met `is_numeric()`. *(H5)*
20. Als developer wil ik dat databasefouten worden afgevangen met `try`/`catch` en dat de gebruiker een 500-pagina ziet. *(H7)*
