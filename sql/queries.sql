-- SQL-dossier Fietsverhuur Trapkracht
-- Boven elke query staat welke vraag van de opdrachtgever hij beantwoordt.

-- ============================================================
-- Kaart 3 - Bezoeker
-- ============================================================

-- Vraag: welke fietsen zijn beschikbaar, met type, merk, framemaat, foto en dagprijs? (userstory 1)
SELECT f.id, f.merk, f.framemaat, f.foto, f.dagprijs, t.naam AS type
FROM fietsen f
LEFT JOIN fiets_types t ON t.id = f.fiets_type_id
WHERE f.in_verhuur = 1
ORDER BY t.naam, f.merk;

-- Vraag: wat zijn de gegevens van één fiets voor de detailpagina? (userstory 2, hier fiets 1)
SELECT f.id, f.merk, f.framemaat, f.foto, f.dagprijs, f.in_verhuur, t.naam AS type
FROM fietsen f
LEFT JOIN fiets_types t ON t.id = f.fiets_type_id
WHERE f.id = 1;

-- Vraag: welke types kan een bezoeker kiezen in het filter, zonder dubbele waardes? (userstory 3)
SELECT DISTINCT t.id, t.naam
FROM fietsen f
LEFT JOIN fiets_types t ON t.id = f.fiets_type_id
WHERE f.in_verhuur = 1
ORDER BY t.naam;

-- Vraag: welke framematen kan een bezoeker kiezen in het filter, zonder dubbele waardes? (userstory 3)
SELECT DISTINCT framemaat
FROM fietsen
WHERE in_verhuur = 1
ORDER BY framemaat;

-- Vraag: welke beschikbare e-bikes (type 2) zijn er in framemaat 57? (userstory 3, twee filters tegelijk)
SELECT f.id, f.merk, f.framemaat, f.dagprijs, t.naam AS type
FROM fietsen f
LEFT JOIN fiets_types t ON t.id = f.fiets_type_id
WHERE f.in_verhuur = 1
  AND f.fiets_type_id = 2
  AND f.framemaat = '57';

-- Vraag: welke fietsen zijn beschikbaar, van goedkoop naar duur? (userstory 4)
SELECT f.id, f.merk, f.dagprijs, t.naam AS type
FROM fietsen f
LEFT JOIN fiets_types t ON t.id = f.fiets_type_id
WHERE f.in_verhuur = 1
ORDER BY f.dagprijs ASC;

-- Vraag: welke fietsen zijn beschikbaar, van duur naar goedkoop? (userstory 4)
SELECT f.id, f.merk, f.dagprijs, t.naam AS type
FROM fietsen f
LEFT JOIN fiets_types t ON t.id = f.fiets_type_id
WHERE f.in_verhuur = 1
ORDER BY f.dagprijs DESC;
