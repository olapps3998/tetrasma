

-- 1
-- untuk keperluan view akun keseluruhan
CREATE VIEW `v_akun_jurnal` AS
Select t_level4.level4_id As level4_id,
  Concat(t_level1.level1_no, '.', t_level2.level2_no, '.', t_level3.level3_no,
  '.', t_level4.level4_no) As no_akun,
  t_level4.level4_nama As nama_akun,
  Concat(t_level1.level1_no, '.', t_level2.level2_no, '.', t_level3.level3_no,
  '.', t_level4.level4_no, ' - ', t_level4.level4_nama) As no_nama_akun,
  t_level4.jurnal As jurnal,
  t_level4.jurnal_kode As jurnal_kode,
  neraca,
  labarugi
From ((t_level4
  Join t_level1 On t_level4.level1_id = t_level1.level1_id)
  Join t_level2 On t_level4.level2_id = t_level2.level2_id)
  Join t_level3 On t_level4.level3_id = t_level3.level3_id;

  
-- 2
-- untuk data jurnal kas dan bank
CREATE VIEW `v_kasbank_jurnal` AS  
Select Null As detail_id,
  t_jurnal.jurnal_id As jurnal_id,
  t_jurnal.no_bukti As no_bukti,
  t_jurnal.tgl As tgl,
  t_jurnal.ket As ket,
  t_jurnal.akun_id As akun_id,
  (Case When (t_jurnal.jenis_jurnal = 'M') Then t_jurnal.nilai Else 0
  End) As debet,
  (Case When (t_jurnal.jenis_jurnal <> 'M') Then t_jurnal.nilai Else 0
  End) As kredit
From t_jurnal;


-- 3
CREATE VIEW `v_kasbank_detail` AS
Select a.detail_id As detail_id,
  a.jurnal_id As jurnal_id,
  b.no_bukti As no_bukti,
  b.tgl As tgl,
  b.ket As ket,
  a.akun_id As akun_id,
  (Case When (a.dk = 0) Then a.nilai Else 0 End) As debet,
  (Case When (a.dk = 1) Then a.nilai Else 0 End) As kredit
From t_detail a
  Left Join t_jurnal b On a.jurnal_id = b.jurnal_id;

  
-- 4
CREATE VIEW `v_kasbank` AS
Select v_kasbank_jurnal.detail_id As detail_id,
  v_kasbank_jurnal.jurnal_id As jurnal_id,
  v_kasbank_jurnal.no_bukti As no_bukti,
  v_kasbank_jurnal.tgl As tgl,
  v_kasbank_jurnal.ket As ket,
  v_kasbank_jurnal.akun_id As akun_id,
  v_kasbank_jurnal.debet As debet,
  v_kasbank_jurnal.kredit As kredit
From v_kasbank_jurnal
union
Select v_kasbank_detail.detail_id As detail_id,
  v_kasbank_detail.jurnal_id As jurnal_id,
  v_kasbank_detail.no_bukti As no_bukti,
  v_kasbank_detail.tgl As tgl,
  v_kasbank_detail.ket As ket,
  v_kasbank_detail.akun_id As akun_id,
  v_kasbank_detail.debet As debet,
  v_kasbank_detail.kredit As kredit
From v_kasbank_detail;


-- 5
-- untuk detail data jurnal memorial
CREATE VIEW `v_memorial` AS
Select a.detailm_id As detailm_id,
  a.jurnalm_id As jurnalm_id,
  b.no_buktim As no_buktim,
  b.tglm As tglm,
  b.ketm As ketm,
  a.akunm_id As akunm_id,
  a.nilaim_debet As nilaim_debet,
  a.nilaim_kredit As nilaim_kredit
From t_detailm a
  Left Join t_jurnalm b On a.jurnalm_id = b.jurnalm_id;

  
-- 6
-- untuk semua data jurnal, gabungan antara jurnal kas & bank dan jurnal memorial
CREATE VIEW `v_kasbank_memorial` AS
Select v_kasbank.detail_id As detail_id,
  v_kasbank.jurnal_id As jurnal_id,
  v_kasbank.no_bukti As no_bukti,
  v_kasbank.tgl As tgl,
  v_kasbank.ket As ket,
  v_kasbank.akun_id As akun_id,
  v_kasbank.debet As debet,
  v_kasbank.kredit As kredit
From v_kasbank
union
Select v_memorial.detailm_id As detailm_id,
  v_memorial.jurnalm_id As jurnalm_id,
  v_memorial.no_buktim As no_buktim,
  v_memorial.tglm As tglm,
  v_memorial.ketm As ketm,
  v_memorial.akunm_id As akunm_id,
  v_memorial.nilaim_debet As nilaim_debet,
  v_memorial.nilaim_kredit As nilaim_kredit
From v_memorial;


-- 7
-- untuk laporan buku besar, tapi keliatannya masih salah
CREATE VIEW `v_bukubesar` AS
SELECT a.detail_id AS detail_id,
  a.jurnal_id AS jurnal_id,
  a.no_bukti AS no_bukti,
  a.tgl AS tgl,
  a.ket AS ket,
  a.akun_id AS akun_id,
  a.debet AS debet,
  a.kredit AS kredit,
  c.no_akun AS no_akun,
  c.nama_akun AS nama_akun,
  c.no_nama_akun AS no_nama_akun,
  b.sa_debet AS sa_debet,
  b.sa_kredit AS sa_kredit,
  b.sm_debet AS sm_debet,
  b.sm_kredit AS sm_kredit
FROM (v_kasbank_memorial a
  LEFT JOIN t_level4 b ON a.akun_id = b.level4_id)
  LEFT JOIN v_akun_jurnal c ON a.akun_id = c.level4_id;

  
-- 8  
create view v_saldo_mutasi as
SELECT v_kasbank_memorial.akun_id AS akun_id,
  (CASE
    WHEN ((Sum(v_kasbank_memorial.debet) - Sum(v_kasbank_memorial.kredit)) >=
    0) THEN (Sum(v_kasbank_memorial.debet) - Sum(v_kasbank_memorial.kredit))
    ELSE 0 END) AS sm_debet,
  (CASE
    WHEN ((Sum(v_kasbank_memorial.debet) - Sum(v_kasbank_memorial.kredit)) <
    0) THEN abs((Sum(v_kasbank_memorial.debet) -
    Sum(v_kasbank_memorial.kredit))) ELSE 0 END) AS sm_kredit
FROM v_kasbank_memorial
GROUP BY v_kasbank_memorial.akun_id;


-- 9
create view v_summary_bukubesar_1 as
SELECT c.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  (CASE WHEN isnull(b.sm_debet) THEN 0 ELSE b.sm_debet END) AS sm_debet,
  (CASE WHEN isnull(b.sm_kredit) THEN 0 ELSE b.sm_kredit END) AS sm_kredit,
  b.akun_id AS akun_id
FROM (v_akun_jurnal a
  LEFT JOIN v_saldo_mutasi b ON a.level4_id = b.akun_id)
  LEFT JOIN t_level1 c ON c.level1_id = Left(a.no_akun, 1)
WHERE Left(a.no_akun, 1) = '1'
ORDER BY a.no_akun;


-- 10
create view v_summary_bukubesar_6 as
SELECT c.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  (CASE WHEN isnull(b.sm_debet) THEN 0 ELSE b.sm_debet END) AS sm_debet,
  (CASE WHEN isnull(b.sm_kredit) THEN 0 ELSE b.sm_kredit END) AS sm_kredit,
  b.akun_id AS akun_id
FROM (v_akun_jurnal a
  LEFT JOIN v_saldo_mutasi b ON a.level4_id = b.akun_id)
  LEFT JOIN t_level1 c ON c.level1_id = Left(a.no_akun, 1)
WHERE Left(a.no_akun, 1) = '6'
ORDER BY a.no_akun;


-- 11
create view v_summary_bukubesar_3 as
SELECT c.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  (CASE WHEN isnull(b.sm_debet) THEN 0 ELSE b.sm_debet END) AS sm_debet,
  (CASE WHEN isnull(b.sm_kredit) THEN 0 ELSE b.sm_kredit END) AS sm_kredit,
  b.akun_id AS akun_id
FROM (v_akun_jurnal a
  LEFT JOIN v_saldo_mutasi b ON a.level4_id = b.akun_id)
  LEFT JOIN t_level1 c ON c.level1_id = Left(a.no_akun, 1)
WHERE Left(a.no_akun, 1) = '3'
ORDER BY a.no_akun;


-- 12
create view v_summary_bukubesar_4 as
SELECT c.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  (CASE WHEN isnull(b.sm_debet) THEN 0 ELSE b.sm_debet END) AS sm_debet,
  (CASE WHEN isnull(b.sm_kredit) THEN 0 ELSE b.sm_kredit END) AS sm_kredit,
  b.akun_id AS akun_id
FROM (v_akun_jurnal a
  LEFT JOIN v_saldo_mutasi b ON a.level4_id = b.akun_id)
  LEFT JOIN t_level1 c ON c.level1_id = Left(a.no_akun, 1)
WHERE Left(a.no_akun, 1) = '4'
ORDER BY a.no_akun;


-- 13
create view v_summary_bukubesar as
Select *
From v_summary_bukubesar_1
union
Select *
From v_summary_bukubesar_6
union
Select *
From v_summary_bukubesar_3
union
Select *
From v_summary_bukubesar_4;


-- 14
create view v_saldo_mutasi_tgl as
SELECT v_kasbank_memorial.akun_id AS akun_id,
  v_kasbank_memorial.tgl AS tgl,
  (CASE
    WHEN ((Sum(v_kasbank_memorial.debet) - Sum(v_kasbank_memorial.kredit)) >=
    0) THEN (Sum(v_kasbank_memorial.debet) - Sum(v_kasbank_memorial.kredit))
    ELSE 0 END) AS sm_debet,
  (CASE
    WHEN ((Sum(v_kasbank_memorial.debet) - Sum(v_kasbank_memorial.kredit)) <
    0) THEN abs((Sum(v_kasbank_memorial.debet) -
    Sum(v_kasbank_memorial.kredit))) ELSE 0 END) AS sm_kredit
FROM v_kasbank_memorial
GROUP BY v_kasbank_memorial.akun_id,
  v_kasbank_memorial.tgl;

  
-- 15  
create view v_summary_lr_4 as
SELECT a.level1_nama AS level1_nama,
  b.nama_akun AS nama_akun,
  b.level4_id AS level4_id
FROM t_level1 a
  LEFT JOIN v_akun_jurnal b ON a.level1_no = Left(b.no_akun, 1)
WHERE a.level1_no = 4 AND b.labarugi = 1
ORDER BY b.no_akun;


-- 16
create view v_summary_lr_6 as
SELECT a.level1_nama AS level1_nama,
  b.nama_akun AS nama_akun,
  b.level4_id AS level4_id
FROM t_level1 a
  LEFT JOIN v_akun_jurnal b ON a.level1_no = Left(b.no_akun, 1)
WHERE a.level1_no = 6 AND b.labarugi = 1
ORDER BY b.no_akun;


-- 17
create view v_summary_lr_5 as
SELECT a.level1_nama AS level1_nama,
  b.nama_akun AS nama_akun,
  b.level4_id AS level4_id
FROM t_level1 a
  LEFT JOIN v_akun_jurnal b ON a.level1_no = Left(b.no_akun, 1)
WHERE a.level1_no = 5 AND b.labarugi = 1
ORDER BY b.no_akun;


-- 18
create view v_akun_1 as
SELECT a.level1_nama AS level1_nama,
  b.nama_akun AS nama_akun,
  b.level4_id AS level4_id
FROM t_level1 a
  LEFT JOIN v_akun_jurnal b ON a.level1_no = Left(b.no_akun, 1)
WHERE a.level1_no = 1
ORDER BY b.no_akun;


-- 19
create view v_akun_1_sum as
SELECT a.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  a.level4_id AS level4_id,
  b.akun_id AS akun_id,
  b.tgl AS tgl,
  b.sm_debet AS sm_debet,
  b.sm_kredit AS sm_kredit
FROM v_akun_1 a
  LEFT JOIN v_saldo_mutasi_tgl b ON a.level4_id = b.akun_id;

  
-- 20  
create view v_akun_1_sum_nrc as
SELECT a.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  a.level4_id AS level4_id,
  a.akun_id AS akun_id,
  a.tgl AS tgl,
  a.sm_debet AS sm_debet,
  a.sm_kredit AS sm_kredit
FROM v_akun_1_sum a
  LEFT JOIN t_level4 b ON a.level4_id = b.level4_id
WHERE b.neraca = 1;


-- 21
create view v_akun_2 as
SELECT a.level1_nama AS level1_nama,
  b.nama_akun AS nama_akun,
  b.level4_id AS level4_id
FROM t_level1 a
  LEFT JOIN v_akun_jurnal b ON a.level1_no = Left(b.no_akun, 1)
WHERE a.level1_no = 2
ORDER BY b.no_akun;


-- 22
create view v_akun_2_sum as
SELECT a.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  a.level4_id AS level4_id,
  b.akun_id AS akun_id,
  b.tgl AS tgl,
  b.sm_debet AS sm_debet,
  b.sm_kredit AS sm_kredit
FROM v_akun_2 a
  LEFT JOIN v_saldo_mutasi_tgl b ON a.level4_id = b.akun_id;

  
-- 23
create view v_akun_2_sum_nrc as
SELECT a.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  a.level4_id AS level4_id,
  a.akun_id AS akun_id,
  a.tgl AS tgl,
  a.sm_debet AS sm_debet,
  a.sm_kredit AS sm_kredit
FROM v_akun_2_sum a
  LEFT JOIN t_level4 b ON a.level4_id = b.level4_id
WHERE b.neraca = 1;


-- 24
create view v_akun_3 as
SELECT a.level1_nama AS level1_nama,
  b.nama_akun AS nama_akun,
  b.level4_id AS level4_id
FROM t_level1 a
  LEFT JOIN v_akun_jurnal b ON a.level1_no = Left(b.no_akun, 1)
WHERE a.level1_no = 3
ORDER BY b.no_akun;


-- 25
create view v_akun_3_sum as
SELECT a.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  a.level4_id AS level4_id,
  b.akun_id AS akun_id,
  b.tgl AS tgl,
  b.sm_debet AS sm_debet,
  b.sm_kredit AS sm_kredit
FROM v_akun_3 a
  LEFT JOIN v_saldo_mutasi_tgl b ON a.level4_id = b.akun_id;

  
-- 26  
create view v_akun_3_sum_nrc as
SELECT a.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  a.level4_id AS level4_id,
  a.akun_id AS akun_id,
  a.tgl AS tgl,
  a.sm_debet AS sm_debet,
  a.sm_kredit AS sm_kredit
FROM v_akun_3_sum a
  LEFT JOIN t_level4 b ON a.level4_id = b.level4_id
WHERE b.neraca = 1;