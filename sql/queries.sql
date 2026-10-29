-- SQL-dossier Fietsverhuur Trapkracht
-- Boven elke query staat welke vraag van de opdrachtgever hij beantwoordt.

-- ============================================================
-- Kaart 3 - Bezoeker
-- ============================================================

-- Vraag: welke fietsen zijn beschikbaar, met type, merk, framemaat, foto en dagprijs? (userstory 1)
SELECT fietsen.*, fiets_types.naam AS type_naam
FROM fietsen
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
WHERE fietsen.deleted_at IS NULL
ORDER BY fiets_types.naam, fietsen.merk;

-- Vraag: wat zijn de gegevens van één fiets voor de detailpagina? (userstory 2, hier fiets 1)
SELECT fietsen.*, fiets_types.naam AS type_naam
FROM fietsen
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
WHERE fietsen.id = 1;

-- Vraag: welke types kan een bezoeker kiezen in het filter, zonder dubbele waardes? (userstory 3)
SELECT DISTINCT fiets_types.id, fiets_types.naam
FROM fietsen
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
WHERE fietsen.deleted_at IS NULL
ORDER BY fiets_types.naam;

-- Vraag: welke framematen kan een bezoeker kiezen in het filter, zonder dubbele waardes? (userstory 3)
SELECT DISTINCT framemaat
FROM fietsen
WHERE deleted_at IS NULL
ORDER BY framemaat;

-- Vraag: welke beschikbare e-bikes (type 2) zijn er in framemaat 57? (userstory 3, twee filters tegelijk)
SELECT fietsen.*, fiets_types.naam AS type_naam
FROM fietsen
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
WHERE fietsen.deleted_at IS NULL
  AND fietsen.fiets_type_id = 2
  AND fietsen.framemaat = '57';

-- Vraag: welke fietsen zijn beschikbaar, van goedkoop naar duur? (userstory 4)
SELECT fietsen.*, fiets_types.naam AS type_naam
FROM fietsen
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
WHERE fietsen.deleted_at IS NULL
ORDER BY fietsen.dagprijs ASC;

-- Vraag: welke fietsen zijn beschikbaar, van duur naar goedkoop? (userstory 4)
SELECT fietsen.*, fiets_types.naam AS type_naam
FROM fietsen
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
WHERE fietsen.deleted_at IS NULL
ORDER BY fietsen.dagprijs DESC;

-- ============================================================
-- Kaart 4 - Klant
-- ============================================================

-- Vraag: bestaat er al een account met dit e-mailadres? (userstory 5)
SELECT id FROM gebruikers WHERE email = 'lisa@example.com';

-- Vraag: welk id heeft de rol klant? Een nieuwe gebruiker krijgt altijd deze rol. (userstory 5)
SELECT id FROM rollen WHERE naam = 'klant';

-- Vraag: maak een nieuw klantaccount aan. Het wachtwoord is gehasht met password_hash(). (userstory 5)
INSERT INTO gebruikers (rol_id, naam, email, wachtwoord, telefoon, created_at, updated_at)
VALUES (1, 'Test Klant', 'test@example.com', '$2y$12$binlEH1G0e/tmEae2hmsqeFGM5extiGNLd/kv0Vo.gpRKtvDxJ0te', NULL, NOW(), NOW());

-- Vraag: wie probeert in te loggen en welke rol heeft die gebruiker? (userstory 6, LEFT JOIN)
SELECT gebruikers.*, rollen.naam AS rol
FROM gebruikers
LEFT JOIN rollen ON gebruikers.rol_id = rollen.id
WHERE gebruikers.email = 'lisa@example.com';

-- Vraag: is fiets 1 vrij van 1 t/m 3 juni 2027? 0 = vrij, meer dan 0 = al gereserveerd. (userstory 7 en 8)
SELECT COUNT(*)
FROM reserveringen
WHERE fiets_id = 1
  AND ophaaldatum <= '2027-06-03'
  AND terugbrengdatum >= '2027-06-01';

-- Vraag: sla een reservering op. De totaalprijs (3 dagen x dagprijs) is in PHP berekend. (userstory 7)
INSERT INTO reserveringen (gebruiker_id, fiets_id, ophaaldatum, terugbrengdatum, totaalprijs, created_at, updated_at)
VALUES (4, 1, '2027-06-01', '2027-06-03', 37.50, NOW(), NOW());

-- Vraag: welke reserveringen heeft klant 4, met het type en merk van de fiets? (userstory 9, LEFT JOIN)
SELECT reserveringen.*, fietsen.merk, fiets_types.naam AS type_naam
FROM reserveringen
LEFT JOIN fietsen ON reserveringen.fiets_id = fietsen.id
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
WHERE reserveringen.gebruiker_id = 4
ORDER BY reserveringen.ophaaldatum DESC;

-- Vraag: gebruikt een ander account (niet klant 4) dit e-mailadres al? (userstory 10)
SELECT id FROM gebruikers WHERE email = 'lisa@example.com' AND id != 4;

-- Vraag: pas de eigen gegevens van klant 4 aan. (userstory 10)
UPDATE gebruikers
SET naam = 'Lisa Jansen', email = 'lisa@example.com', telefoon = '0634567890', updated_at = NOW()
WHERE id = 4;

-- ============================================================
-- Kaart 5 - Medewerker en admin
-- ============================================================

-- Vraag: voeg een nieuwe fiets toe aan het aanbod. (userstory 12)
INSERT INTO fietsen (fiets_type_id, merk, framemaat, foto, dagprijs, created_at, updated_at)
VALUES (1, 'Cortina', '53', NULL, 11.00, NOW(), NOW());

-- Vraag: pas de dagprijs van fiets 4 aan. (userstory 13)
UPDATE fietsen
SET dagprijs = 29.95, updated_at = NOW()
WHERE id = 4;

-- Vraag: vul bij reservering 12 de inleverdatum en een opmerking in. Beide mogen ook NULL blijven. (userstory 13)
UPDATE reserveringen
SET inleverdatum = '2026-10-29', opmerking = 'lekke band achter', updated_at = NOW()
WHERE id = 12;

-- Vraag: haal fiets 10 uit de verhuur zonder hem echt te verwijderen (soft delete). (userstory 14)
UPDATE fietsen
SET deleted_at = NOW()
WHERE id = 10 AND deleted_at IS NULL;

-- Vraag: welke fietsen zijn uit de verhuur gehaald? (userstory 14)
SELECT fietsen.*, fiets_types.naam AS type_naam
FROM fietsen
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
WHERE fietsen.deleted_at IS NOT NULL
ORDER BY fietsen.deleted_at DESC;

-- Vraag: zet fiets 10 terug in de verhuur. (userstory 14)
UPDATE fietsen
SET deleted_at = NULL
WHERE id = 10 AND deleted_at IS NOT NULL;

-- Vraag: welke reserveringen zijn er, met de klant en de fiets erbij? (userstory 15, LEFT JOIN)
SELECT reserveringen.*, gebruikers.naam AS klant_naam, gebruikers.email AS klant_email,
       fietsen.merk, fiets_types.naam AS type_naam
FROM reserveringen
LEFT JOIN gebruikers ON reserveringen.gebruiker_id = gebruikers.id
LEFT JOIN fietsen ON reserveringen.fiets_id = fietsen.id
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
ORDER BY reserveringen.ophaaldatum DESC;

-- Vraag: welke fietsen zijn nog nooit verhuurd? (userstory 16, LEFT JOIN met rijen ZONDER koppeling)
-- LEFT JOIN houdt alle fietsen, ook als er geen reservering bij hoort.
-- Bij een fiets zonder reservering zijn alle kolommen van reserveringen NULL,
-- want er is geen rij om ze mee te vullen. WHERE reserveringen.id IS NULL
-- houdt precies die fietsen over (in de testdata: fiets 8 en 11).
SELECT fietsen.*, fiets_types.naam AS type_naam
FROM fietsen
LEFT JOIN reserveringen ON reserveringen.fiets_id = fietsen.id
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
WHERE reserveringen.id IS NULL
ORDER BY fiets_types.naam, fietsen.merk;

-- Vraag: hoeveel fietsen zijn er, en wat is de gemiddelde, laagste en hoogste dagprijs? (userstory 17, COUNT AVG MIN MAX)
SELECT COUNT(id) AS aantal, AVG(dagprijs) AS gemiddeld, MIN(dagprijs) AS laagste, MAX(dagprijs) AS hoogste
FROM fietsen;

-- Vraag: hoeveel fietsen zijn er per type, en wat kosten ze gemiddeld, minimaal en maximaal? (userstory 17, GROUP BY)
-- LEFT JOIN vanaf fiets_types: een type zonder fietsen komt er ook in. COUNT(fietsen.id) is dan 0
-- en AVG/MIN/MAX zijn NULL, omdat er geen dagprijzen zijn om mee te rekenen.
SELECT fiets_types.naam, COUNT(fietsen.id) AS aantal,
       AVG(fietsen.dagprijs) AS gemiddeld, MIN(fietsen.dagprijs) AS laagste, MAX(fietsen.dagprijs) AS hoogste
FROM fiets_types
LEFT JOIN fietsen ON fietsen.fiets_type_id = fiets_types.id
GROUP BY fiets_types.id, fiets_types.naam
ORDER BY fiets_types.naam;

-- Vraag: wat is de omzet per type? (userstory 17, SUM en GROUP BY)
-- Een type waarvan nog niets verhuurd is, krijgt omzet NULL (geen totaalprijzen om op te tellen).
SELECT fiets_types.naam, COUNT(reserveringen.id) AS verhuurd, SUM(reserveringen.totaalprijs) AS omzet
FROM fiets_types
LEFT JOIN fietsen ON fietsen.fiets_type_id = fiets_types.id
LEFT JOIN reserveringen ON reserveringen.fiets_id = fietsen.id
GROUP BY fiets_types.id, fiets_types.naam
ORDER BY omzet DESC;

-- Vraag: welke types zijn meer dan vijf keer verhuurd? (userstory 17, GROUP BY + HAVING)
-- HAVING en niet WHERE, omdat je filtert op de uitkomst van COUNT, en die bestaat pas na GROUP BY.
SELECT fiets_types.naam, COUNT(reserveringen.id) AS verhuurd
FROM reserveringen
LEFT JOIN fietsen ON reserveringen.fiets_id = fietsen.id
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
GROUP BY fiets_types.id, fiets_types.naam
HAVING COUNT(reserveringen.id) > 5
ORDER BY verhuurd DESC;

-- Vraag: zet een beheeractie in het logboek, door medewerker 2. (userstory 18)
INSERT INTO logboek (gebruiker_id, actie, created_at)
VALUES (2, 'Fiets 10 (Urban Arrow) terug in de verhuur gezet', NOW());

-- Vraag: wie heeft wat gedaan in het beheergedeelte, nieuwste eerst? (userstory 18, LEFT JOIN)
SELECT logboek.*, gebruikers.naam AS gebruiker_naam, rollen.naam AS rol
FROM logboek
LEFT JOIN gebruikers ON logboek.gebruiker_id = gebruikers.id
LEFT JOIN rollen ON gebruikers.rol_id = rollen.id
ORDER BY logboek.created_at DESC, logboek.id DESC;
