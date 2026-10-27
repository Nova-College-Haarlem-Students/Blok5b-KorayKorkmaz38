### Bezoeker
1. Als bezoeker wil ik een overzicht van alle artikelen zien die nog te koop zijn. *(H1)*
2. Als bezoeker wil ik op een artikel klikken voor de detailpagina. Bestaat het artikel niet, dan zie ik een nette 404-pagina. *(H7)*
3. Als bezoeker wil ik filteren op categorie én staat tegelijk. De keuzelijst met categorieën komt uit de database, zonder dubbele waardes. *(H6, DISTINCT)*
4. Als bezoeker wil ik de artikelen sorteren op prijs en op datum van binnenkomst, oplopend en aflopend. *(ORDER BY)*

### Klant
5. Als bezoeker wil ik een account aanmaken; mijn wachtwoord wordt gehasht opgeslagen en het formulier wordt gecontroleerd. *(H2)*
6. Als klant wil ik inloggen en uitloggen. *(H2)*
7. Als klant wil ik met één klik een artikel reserveren, zonder dat de pagina herlaadt. Is het artikel al gereserveerd, dan krijg ik een melding. *(H4, AJAX)*
8. Als klant wil ik een overzicht van **mijn** reserveringen zien, met de naam, categorie en prijs van het artikel. *(H6, LEFT JOIN)*
9. Als klant wil ik mijn eigen gegevens kunnen aanpassen. *(H3)*

### Medewerker
10. Als medewerker wil ik inloggen op een apart beheergedeelte. Een klant die een beheerpagina opent, ziet een 403-pagina. *(H5, H7)*
11. Als medewerker wil ik een nieuw artikel toevoegen en optioneel een donateur kiezen; zonder donateur is het artikel anoniem. Lege of foute velden geven een foutmelding. *(H1, H2, NULL)*
12. Als medewerker wil ik de prijs van een artikel aanpassen en een artikel als verkocht markeren (verkoopdatum invullen). *(H3)*
13. Als medewerker wil ik een artikel uit de winkel halen zonder dat het echt verwijderd wordt, en het later terugzetten. *(H4)*
14. Als medewerker wil ik een overzicht van alle reserveringen, met de klant en het artikel erbij. *(H6, LEFT JOIN)*
15. Als medewerker wil ik een lijst van **alle** donateurs met het aantal gebrachte artikelen - ook donateurs met 0 artikelen. *(LEFT JOIN, COUNT, GROUP BY)*

### Admin (bedrijfsleider)
16. Als admin wil ik een statistiekenpagina met: het aantal artikelen per categorie, de gemiddelde, laagste en hoogste prijs, de totale verkoopopbrengst, en de categorieën met een gemiddelde prijs boven de 20 euro. *(COUNT, AVG, MIN, MAX, SUM, GROUP BY, HAVING)*
17. Als admin wil ik een logboek zien van wie wat heeft gedaan in het beheergedeelte. Een medewerker die de statistieken of het logboek opent, ziet een 403-pagina. *(H5, H7)*

### Developer
18. Als developer wil ik dat alle queries via PDO met prepared statements lopen en alle output via `htmlspecialchars()`. *(H1, H2)*
19. Als developer wil ik dat formulieren alleen via `POST` verwerkt worden en dat id's in de URL gecontroleerd worden met `is_numeric()`. *(H5)*
20. Als developer wil ik dat databasefouten worden afgevangen met `try`/`catch` en dat de gebruiker een 500-pagina ziet. *(H7)*
