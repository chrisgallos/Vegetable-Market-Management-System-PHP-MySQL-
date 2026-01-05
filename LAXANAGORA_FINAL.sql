-- --------------------------Ερώτηση 2 ------------------------------------------------
-- Δημιουργία βάσης δεδομένων
CREATE DATABASE IF NOT EXISTS LAXANAGORA;
USE LAXANAGORA;

-- 1. Πίνακας ADEIA 
CREATE TABLE IF NOT EXISTS ADEIA (
  ID_ADEIAS INT ,
  HM_ENARXIS DATE NOT NULL,
  HM_LIXIS DATE NOT NULL,
  PRIMARY KEY (ID_ADEIAS)
);

-- 2. Πίνακας PARAGOGOS 
CREATE TABLE IF NOT EXISTS PARAGOGOS (
  ID_PARAGOGOU CHAR(4),
  ONOMATEPONIMO VARCHAR(40) NOT NULL ,
  PERIOXI VARCHAR(20) NOT NULL,
  ID_ADEIAS INT  NOT NULL UNIQUE,
  PRIMARY KEY (ID_PARAGOGOU),
  FOREIGN KEY (ID_ADEIAS) REFERENCES ADEIA(ID_ADEIAS)
);

-- 3. Πίνακας AGROTEMAXIO 
CREATE TABLE IF NOT EXISTS AGROTEMAXIO (
  ID_AGROTEMAXIOU INT,
  EKTASI DECIMAL(6,2) NOT NULL,
  KATALLILOTITA VARCHAR(30) NOT NULL,
  ID_PARAGOGOU CHAR(4) NOT NULL,
  PRIMARY KEY (ID_AGROTEMAXIOU),
  FOREIGN KEY (ID_PARAGOGOU) REFERENCES PARAGOGOS(ID_PARAGOGOU)
);

-- 4. Πίνακας PROIONTA 
CREATE TABLE IF NOT EXISTS PROIONTA (
  ID_PROIONTOS INT,
  ONOMA_PROIONTOS VARCHAR(30) NOT NULL,
  MONADA_METRISIS CHAR(3) NOT NULL,
  TIMI DOUBLE NOT NULL,
  PRIMARY KEY (ID_PROIONTOS)
);

-- 5. Πίνακας KATIGORIA_PROIONTOS 
CREATE TABLE IF NOT EXISTS KATIGORIA_PROIONTOS (
  ID_PROIONTOS INT NOT NULL,
  ONOMA_KAT VARCHAR(40) NOT NULL,
  FPA DECIMAL(4,2) DEFAULT 24.00,
  FOREIGN KEY (ID_PROIONTOS) REFERENCES PROIONTA(ID_PROIONTOS)
);

-- 6. Πίνακας PARAGOGOS_PROIONTA 
CREATE TABLE IF NOT EXISTS PARAGOGOS_PROIONTA (
  ID_PARAGOGOU CHAR(4) NOT NULL,
  ID_PROIONTOS INT NOT NULL,
  PERIODOS_PARAGOGIS VARCHAR(30),
  PRIMARY KEY (ID_PARAGOGOU, ID_PROIONTOS),
  FOREIGN KEY (ID_PARAGOGOU) REFERENCES PARAGOGOS(ID_PARAGOGOU),
  FOREIGN KEY (ID_PROIONTOS) REFERENCES PROIONTA(ID_PROIONTOS)
);

-- 7. Πίνακας PARALABI 
CREATE TABLE IF NOT EXISTS PARALABI (
  ID_PARALABIS INT,
  POSOTITA DOUBLE NOT NULL,
  HMEROMINIA DATE NOT NULL,
  ID_PARAGOGOU CHAR(4) NOT NULL,
  ID_PROIONTOS INT NOT NULL,
  TROPOS_PARALABIS VARCHAR(30) NOT NULL,
  PRIMARY KEY (ID_PARALABIS),
  FOREIGN KEY (ID_PARAGOGOU) REFERENCES PARAGOGOS(ID_PARAGOGOU),
  FOREIGN KEY (ID_PROIONTOS) REFERENCES PROIONTA(ID_PROIONTOS)
);

-- 8. Πίνακας KATASTIMATOS
CREATE TABLE IF NOT EXISTS KATASTIMATOS (
  ID_KATASTIMATOS INT,
  ONOMA_KAT VARCHAR(40) NOT NULL,
  POLI VARCHAR(30) NOT NULL,
  PRIMARY KEY (ID_KATASTIMATOS)
);

-- 9. Πίνακας PELATIS
CREATE TABLE IF NOT EXISTS PELATIS (
  AFM CHAR(9),
  EMAIL VARCHAR(50) NOT NULL,
  ONOMATEPONIMO VARCHAR(50) NOT NULL,
  TILEFONO1 CHAR(10) NOT NULL,
  TILEFONO2 CHAR(10),
  TILEFONO3 CHAR(10),
  ID_PROIONTOS INT NOT NULL,
  ID_KATASTIMATOS INT NOT NULL,
  PRIMARY KEY (AFM),
  FOREIGN KEY (ID_PROIONTOS) REFERENCES PROIONTA(ID_PROIONTOS),
  FOREIGN KEY (ID_KATASTIMATOS) REFERENCES KATASTIMATOS(ID_KATASTIMATOS)
);

-- 10. Πίνακας PROION_PELATIS 
CREATE TABLE IF NOT EXISTS PROION_PELATIS (
  AFM CHAR(9) NOT NULL,
  ID_PROIONTOS INT NOT NULL,
  KATASTASI_PARAGELIAS VARCHAR(30) NOT NULL,
  PRIMARY KEY (AFM, ID_PROIONTOS),
  FOREIGN KEY (AFM) REFERENCES PELATIS(AFM),
  FOREIGN KEY (ID_PROIONTOS) REFERENCES PROIONTA(ID_PROIONTOS)
);


-- -----------------------------------------------------------------------


-- ---------------------------Ερώτηση 3 - INSERT----------------------------------

-- 1 Πίνακας ADEIA
INSERT INTO ADEIA VALUES
(1,'2024-01-10','2025-01-10'),
(2,'2023-03-01','2024-03-01'),
(3,'2024-05-15','2025-05-15'),
(4,'2022-11-10','2023-11-10'),
(5,'2025-01-01','2026-01-01');

-- 2 Πίνακας PARAGOGOS
INSERT INTO PARAGOGOS VALUES
('P001','Georgiou Antonis','Xanthi',1),
('P002','Nikolaou Maria','Drama',2),
('P003','Ioannou Kostas','Kavala',3),
('P004','Papadopoulou Eleni','Komotinh',4),
('P005','Athanasiou Giorgos','Alexandroypolh',5);

-- 3 Πίνακας AGROTEMAXIO
INSERT INTO AGROTEMAXIO VALUES
(1, 2.50, 'Biologiki kalliergeia', 'P001'),
(2, 1.80, 'Thermokipio',            'P001'),
(3, 3.20, 'Anoixth kalliergeia',  'P002'),
(4, 4.00, 'Biologiki kalliergeia', 'P003'),
(5, 2.00, 'Thermokipio',            'P004');


-- 4 Πίνακας PROIONTA
INSERT INTO PROIONTA VALUES
(1,'Ntomata','kg',1.50),
(2,'Aggouri','kg',1.20),
(3,'Piperia','kg',1.80),
(4,'Marouli','tem',0.90),
(5,'Kremidi','kg',1.10);

-- 5 Πίνακας KATIGORIA_PROIONTOS
INSERT INTO KATIGORIA_PROIONTOS VALUES
(1, 'Laxanika', 13.00),
(2, 'Laxanika', 13.00),
(3, 'Laxanika', 24.00),
(4, 'Prasina', 24.00),
(5, 'Volvoi', 13.00);

-- 6 Πίνακας PARAGOGOS_PROIONTA
INSERT INTO PARAGOGOS_PROIONTA VALUES
('P001', 1, 'Xeimonas 2024'),
('P001', 2, 'Xeimonas 2024'),
('P002', 1, 'Xeimonas 2024'),
('P002', 3, 'Anoixi 2025'),
('P003', 4, 'Anoixi 2025'),
('P004', 3, 'Kalokairi 2025'),
('P005', 5, 'Kalokairi 2025'),
('P005', 1, 'Anoixi 2025');

-- 7 Πίνακας PARALABI
INSERT INTO PARALABI VALUES
(1, 100, '2025-01-10', 'P001', 1, 'Paralabi sto Katastima'),
(2,  80, '2025-01-12', 'P001', 2, 'Apostoli ston pelath'),
(3,  50, '2025-01-15', 'P002', 3, 'Apostoli ston pelath'),
(4, 120, '2025-01-18', 'P002', 1, 'Paralabi sto Katastima'),
(5, 200, '2025-01-20', 'P003', 4, 'Paralabi sto Katastima');

-- 8 Πίνακας KATASTIMATOS
INSERT INTO KATASTIMATOS VALUES
(1,'Green Market','Xnathi'),
(2,'BioFoods','Kavala'),
(3,'FreshLand','Drama'),
(4,'LocalMart','Komotinh'),
(5,'AgroShop','Alexandroupolh');

-- 9 Πίνακας PELATIS
INSERT INTO PELATIS VALUES
('123456789','giannis@gmail.com','Papadopoulow Giannis','6981112233', '6981115580', '6981112968',1,1),
('223456789','maria@yahoo.com','Konstantinou Maria','6981234567', '6981230025', '6981239635',2,2),
('323456789','kostas@hotmail.com','Ioannou Kostas','6970000000', '6970000052', NULL,3,3),
('423456789','eleni@gmail.com','Papadopoulou Eleni','6991234567', '6991234852', '6991234500',4,4),
('523456789','giorgos@yahoo.com','Athanasiou Giorgos','6983334444', NULL, NULL,5,5);

-- 10 Πίνακας PROION_PELATIS
INSERT INTO PROION_PELATIS VALUES
('123456789', 1, 'Olokliromenh'),
('123456789', 2, 'Se Exelixh'),
('223456789', 3, 'Olokliromenh'),
('323456789', 1, 'Akuromenh'),
('423456789', 5, 'Olokliromenh'),
('523456789', 4, 'Se Exelixh'),
('523456789', 5, 'Akuromenh');




-- --------------------------------Ερώτηση 4 - SELECT-----------------------------------------

-- 4α)
SELECT ID_PARAGOGOU, ONOMATEPONIMO, PERIOXI
FROM PARAGOGOS
WHERE ONOMATEPONIMO LIKE 'G%';



-- 4β)

SELECT ID_PROIONTOS, ONOMA_PROIONTOS, MONADA_METRISIS, TIMI
FROM PROIONTA
WHERE TIMI > 1.00
ORDER BY TIMI DESC, ONOMA_PROIONTOS ASC;


-- 4c) 
SELECT AFM,ID_PROIONTOS,KATASTASI_PARAGELIAS
FROM PROION_PELATIS
WHERE (ID_PROIONTOS = 1 OR ID_PROIONTOS = 5)
AND KATASTASI_PARAGELIAS = 'Olokliromenh';



-- --------------------------Ερώτηση 5------------------------------------------------

-- 5α) 
-- Συνολική ποσότητα και πλήθος παραλαβών για κάθε προϊόν
SELECT ID_PROIONTOS,
SUM(POSOTITA) AS synoliki_posotita,
COUNT(*)      AS arithmos_paralavon
FROM PARALABI
GROUP BY ID_PROIONTOS;


-- 5β)
-- Μέση, συνολική έκταση και πλήθος αγροτεμαχίων για κάθε παραγωγό
SELECT
  ID_PARAGOGOU,
  CAST(AVG(EKTASI) AS DECIMAL(10,2)) AS mesi_ektasi,
  CAST(SUM(EKTASI) AS DECIMAL(10,2)) AS synoliki_ektasi,
  COUNT(*) AS plithos_agrotemaxion
FROM AGROTEMAXIO
GROUP BY ID_PARAGOGOU;


-- -----------------------------Ερώτηση 6--------------------------------------------
-- 6α)
SELECT p.ONOMATEPONIMO      AS Onoma_Paragogou,
       pr.ONOMA_PROIONTOS   AS Onoma_Proiontos, pa.POSOTITA, pa.HMEROMINIA
FROM PARALABI pa
INNER JOIN PARAGOGOS p ON pa.ID_PARAGOGOU = p.ID_PARAGOGOU
INNER JOIN PROIONTA pr ON pa.ID_PROIONTOS = pr.ID_PROIONTOS
ORDER BY pa.HMEROMINIA, p.ONOMATEPONIMO;

-- 6β)
SELECT pg.ID_PARAGOGOU, pg.ONOMATEPONIMO, pr.ID_PROIONTOS, pr.ONOMA_PROIONTOS
FROM PARAGOGOS pg
LEFT JOIN PARAGOGOS_PROIONTA pp ON pg.ID_PARAGOGOU = pp.ID_PARAGOGOU
LEFT JOIN PROIONTA pr ON pp.ID_PROIONTOS = pr.ID_PROIONTOS
ORDER BY pg.ID_PARAGOGOU, pr.ID_PROIONTOS;

-- ---------------------------------Ερώτηση 7------------------------------------------
-- 7) 
CREATE VIEW VW_PARALAVES_APLO AS
SELECT pa.ID_PARALABIS, pa.HMEROMINIA, pa.POSOTITA, p.ONOMATEPONIMO, pr.ONOMA_PROIONTOS
FROM PARALABI pa
INNER JOIN PARAGOGOS p ON pa.ID_PARAGOGOU = p.ID_PARAGOGOU
INNER JOIN PROIONTA pr ON pa.ID_PROIONTOS = pr.ID_PROIONTOS;

 
SELECT *
FROM VW_PARALAVES_APLO;

-- ------------------------------Ερώτηση 8----------------------------------------------
USE LAXANAGORA;
DELIMITER //

CREATE PROCEDURE add_pelatis (
    IN p_afm           CHAR(9),
    IN p_email         VARCHAR(50),
    IN p_onoma         VARCHAR(50),
    IN p_tilefono      CHAR(10),
    IN p_id_proiontos  INT,
    IN p_id_katastimatos INT
)
BEGIN
    INSERT INTO PELATIS (AFM, EMAIL, ONOMATEPONIMO, TILEFONO1, ID_PROIONTOS, ID_KATASTIMATOS)
    VALUES (p_afm, p_email, p_onoma, p_tilefono, p_id_proiontos, p_id_katastimatos);
END //
DELIMITER ;


CALL add_pelatis('623456789', 'newcustomer@mail.com', 'Neos Pelatis', '6990000000', 1, 2);


-- ---------------------------------Ερώτηση 9----------------------------------------------

-- Καθαρισμός παλιού trigger (αν υπάρχει)
DROP TRIGGER IF EXISTS trg_paralabi_before_insert;
DELIMITER //

CREATE TRIGGER trg_paralabi_before_insert
BEFORE INSERT ON PARALABI
FOR EACH ROW
BEGIN
  -- 1) Η ποσότητα πρέπει να είναι θετική
  IF NEW.POSOTITA IS NULL OR NEW.POSOTITA <= 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'H POSOTITA prepei na einai thetikh.';
  END IF;

  -- 2) Αν δεν δοθεί ημερομηνία, βάλε τη σημερινή
  IF NEW.HMEROMINIA IS NULL THEN
    SET NEW.HMEROMINIA = CURRENT_DATE();
  END IF;

  -- 3) (Προαιρετικό) Κανονικοποίηση τρόπου παραλαβής
  -- Επιτρεπτές τιμές: 'Παραλαβή στο Κατάστημα', 'Αποστολή στον Πελάτη'
  IF NEW.TROPOS_PARALABIS IS NULL OR
     NEW.TROPOS_PARALABIS NOT IN ('Paralabi sto Katastima','Apostoli ston pelath') THEN
     SET NEW.TROPOS_PARALABIS = 'Paralabi sto Katastima';
  END IF;
END //
DELIMITER ;

INSERT INTO PARALABI (ID_PARALABIS, POSOTITA, HMEROMINIA, ID_PARAGOGOU, ID_PROIONTOS, TROPOS_PARALABIS)
VALUES (7, 50, NULL, 'P001', 1, 'Allo tropo');


SELECT * FROM PARALABI WHERE ID_PARALABIS = 7;