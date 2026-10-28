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
