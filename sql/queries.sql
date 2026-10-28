-- SQL-dossier Fietsverhuur Trapkracht
-- Boven elke query staat welke vraag van de opdrachtgever hij beantwoordt.

-- ============================================================
-- Kaart 3 - Bezoeker
-- ============================================================

-- Vraag: welke fietsen zijn beschikbaar, met type, merk, framemaat, foto en dagprijs? (userstory 1)
SELECT fietsen.*, fiets_types.naam AS type_naam
FROM fietsen
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
WHERE fietsen.in_verhuur = 1
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
WHERE fietsen.in_verhuur = 1
ORDER BY fiets_types.naam;

-- Vraag: welke framematen kan een bezoeker kiezen in het filter, zonder dubbele waardes? (userstory 3)
SELECT DISTINCT framemaat
FROM fietsen
WHERE in_verhuur = 1
ORDER BY framemaat;

-- Vraag: welke beschikbare e-bikes (type 2) zijn er in framemaat 57? (userstory 3, twee filters tegelijk)
SELECT fietsen.*, fiets_types.naam AS type_naam
FROM fietsen
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
WHERE fietsen.in_verhuur = 1
  AND fietsen.fiets_type_id = 2
  AND fietsen.framemaat = '57';

-- Vraag: welke fietsen zijn beschikbaar, van goedkoop naar duur? (userstory 4)
SELECT fietsen.*, fiets_types.naam AS type_naam
FROM fietsen
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
WHERE fietsen.in_verhuur = 1
ORDER BY fietsen.dagprijs ASC;

-- Vraag: welke fietsen zijn beschikbaar, van duur naar goedkoop? (userstory 4)
SELECT fietsen.*, fiets_types.naam AS type_naam
FROM fietsen
LEFT JOIN fiets_types ON fietsen.fiets_type_id = fiets_types.id
WHERE fietsen.in_verhuur = 1
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
