

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
CREATE VIEW `v_akun_1` AS select `a`.`level1_nama` AS `level1_nama`,`b`.`nama_akun` AS `nama_akun`,`b`.`level4_id` AS `level4_id`,`c`.`sa_debet` AS `sa_debet`,`c`.`sa_kredit` AS `sa_kredit` from ((`t_level1` `a` left join `v_akun_jurnal` `b` on((`a`.`level1_no` = left(`b`.`no_akun`,1)))) left join `t_level4` `c` on((`b`.`level4_id` = `c`.`level4_id`))) where (`a`.`level1_no` = 1) order by `b`.`no_akun`;


-- 19
CREATE VIEW `v_akun_1_nrc` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`a`.`sa_debet` AS `sa_debet`,`a`.`sa_kredit` AS `sa_kredit` from (`v_akun_1` `a` left join `t_level4` `b` on((`a`.`level4_id` = `b`.`level4_id`))) where (`b`.`neraca` = 1);


-- 20
CREATE VIEW `v_akun_1_sum` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`a`.`sa_debet` AS `sa_debet`,`a`.`sa_kredit` AS `sa_kredit`,`b`.`akun_id` AS `akun_id`,`b`.`tgl` AS `tgl`,`b`.`sm_debet` AS `sm_debet`,`b`.`sm_kredit` AS `sm_kredit` from (`v_akun_1` `a` left join `v_saldo_mutasi_tgl` `b` on((`a`.`level4_id` = `b`.`akun_id`)));


-- 21
CREATE VIEW `v_akun_1_nrc_sum` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`a`.`sa_debet` AS `sa_debet`,`a`.`sa_kredit` AS `sa_kredit`,`a`.`akun_id` AS `akun_id`,`a`.`tgl` AS `tgl`,`a`.`sm_debet` AS `sm_debet`,`a`.`sm_kredit` AS `sm_kredit` from (`v_akun_1_sum` `a` left join `t_level4` `b` on((`a`.`level4_id` = `b`.`level4_id`))) where (`b`.`neraca` = 1);


-- 22
CREATE VIEW `v_akun_2` AS select `a`.`level1_nama` AS `level1_nama`,`b`.`nama_akun` AS `nama_akun`,`b`.`level4_id` AS `level4_id`,`c`.`sa_debet` AS `sa_debet`,`c`.`sa_kredit` AS `sa_kredit` from ((`t_level1` `a` left join `v_akun_jurnal` `b` on((`a`.`level1_no` = left(`b`.`no_akun`,1)))) left join `t_level4` `c` on((`b`.`level4_id` = `c`.`level4_id`))) where (`a`.`level1_no` = 2) order by `b`.`no_akun`;


-- 23
CREATE VIEW `v_akun_2_nrc` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`a`.`sa_debet` AS `sa_debet`,`a`.`sa_kredit` AS `sa_kredit` from (`v_akun_2` `a` left join `t_level4` `b` on((`a`.`level4_id` = `b`.`level4_id`))) where (`b`.`neraca` = 1);


-- 24
CREATE VIEW `v_akun_2_sum` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`a`.`sa_debet` AS `sa_debet`,`a`.`sa_kredit` AS `sa_kredit`,`b`.`akun_id` AS `akun_id`,`b`.`tgl` AS `tgl`,`b`.`sm_debet` AS `sm_debet`,`b`.`sm_kredit` AS `sm_kredit` from (`v_akun_2` `a` left join `v_saldo_mutasi_tgl` `b` on((`a`.`level4_id` = `b`.`akun_id`)));


-- 25
CREATE VIEW `v_akun_2_nrc_sum` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`a`.`sa_debet` AS `sa_debet`,`a`.`sa_kredit` AS `sa_kredit`,`a`.`akun_id` AS `akun_id`,`a`.`tgl` AS `tgl`,`a`.`sm_debet` AS `sm_debet`,`a`.`sm_kredit` AS `sm_kredit` from (`v_akun_2_sum` `a` left join `t_level4` `b` on((`a`.`level4_id` = `b`.`level4_id`))) where (`b`.`neraca` = 1);


-- 26
CREATE VIEW `v_akun_3` AS select `a`.`level1_nama` AS `level1_nama`,`b`.`nama_akun` AS `nama_akun`,`b`.`level4_id` AS `level4_id`,`c`.`sa_debet` AS `sa_debet`,`c`.`sa_kredit` AS `sa_kredit` from ((`t_level1` `a` left join `v_akun_jurnal` `b` on((`a`.`level1_no` = left(`b`.`no_akun`,1)))) left join `t_level4` `c` on((`b`.`level4_id` = `c`.`level4_id`))) where (`a`.`level1_no` = 3) order by `b`.`no_akun`;


-- 27
CREATE VIEW `v_akun_3_nrc` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`a`.`sa_debet` AS `sa_debet`,`a`.`sa_kredit` AS `sa_kredit` from (`v_akun_3` `a` left join `t_level4` `b` on((`a`.`level4_id` = `b`.`level4_id`))) where (`b`.`neraca` = 1);


-- 28
CREATE VIEW `v_akun_3_sum` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`a`.`sa_debet` AS `sa_debet`,`a`.`sa_kredit` AS `sa_kredit`,`b`.`akun_id` AS `akun_id`,`b`.`tgl` AS `tgl`,`b`.`sm_debet` AS `sm_debet`,`b`.`sm_kredit` AS `sm_kredit` from (`v_akun_3` `a` left join `v_saldo_mutasi_tgl` `b` on((`a`.`level4_id` = `b`.`akun_id`)));


-- 29
CREATE VIEW `v_akun_3_nrc_sum` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`a`.`sa_debet` AS `sa_debet`,`a`.`sa_kredit` AS `sa_kredit`,`a`.`akun_id` AS `akun_id`,`a`.`tgl` AS `tgl`,`a`.`sm_debet` AS `sm_debet`,`a`.`sm_kredit` AS `sm_kredit` from (`v_akun_3_sum` `a` left join `t_level4` `b` on((`a`.`level4_id` = `b`.`level4_id`))) where (`b`.`neraca` = 1);


-- 30
create view v_akun_4 as
SELECT a.level1_nama AS level1_nama,
  b.nama_akun AS nama_akun,
  b.level4_id AS level4_id
FROM t_level1 a
  LEFT JOIN v_akun_jurnal b ON a.level1_no = Left(b.no_akun, 1)
WHERE a.level1_no = 4
ORDER BY b.no_akun;


-- 31
create view v_akun_4_sum as
SELECT a.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  a.level4_id AS level4_id,
  b.akun_id AS akun_id,
  b.tgl AS tgl,
  b.sm_debet AS sm_debet,
  b.sm_kredit AS sm_kredit
FROM v_akun_4 a
  LEFT JOIN v_saldo_mutasi_tgl b ON a.level4_id = b.akun_id;

  
-- 32
create view v_akun_4_sum_lr as
SELECT a.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  a.level4_id AS level4_id,
  a.akun_id AS akun_id,
  a.tgl AS tgl,
  a.sm_debet AS sm_debet,
  a.sm_kredit AS sm_kredit
FROM v_akun_4_sum a
  LEFT JOIN t_level4 b ON a.level4_id = b.level4_id
WHERE b.labarugi = 1;


-- 33
create view v_akun_5 as
SELECT a.level1_nama AS level1_nama,
  b.nama_akun AS nama_akun,
  b.level4_id AS level4_id
FROM t_level1 a
  LEFT JOIN v_akun_jurnal b ON a.level1_no = Left(b.no_akun, 1)
WHERE a.level1_no = 5
ORDER BY b.no_akun;


-- 34
create view v_akun_5_sum as
SELECT a.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  a.level4_id AS level4_id,
  b.akun_id AS akun_id,
  b.tgl AS tgl,
  b.sm_debet AS sm_debet,
  b.sm_kredit AS sm_kredit
FROM v_akun_5 a
  LEFT JOIN v_saldo_mutasi_tgl b ON a.level4_id = b.akun_id;

  
-- 35
create view v_akun_5_sum_lr as
SELECT a.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  a.level4_id AS level4_id,
  a.akun_id AS akun_id,
  a.tgl AS tgl,
  a.sm_debet AS sm_debet,
  a.sm_kredit AS sm_kredit
FROM v_akun_5_sum a
  LEFT JOIN t_level4 b ON a.level4_id = b.level4_id
WHERE b.labarugi = 1;


-- 36
create view v_akun_6 as
SELECT a.level1_nama AS level1_nama,
  b.nama_akun AS nama_akun,
  b.level4_id AS level4_id
FROM t_level1 a
  LEFT JOIN v_akun_jurnal b ON a.level1_no = Left(b.no_akun, 1)
WHERE a.level1_no = 6
ORDER BY b.no_akun;


-- 37
create view v_akun_6_sum as
SELECT a.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  a.level4_id AS level4_id,
  b.akun_id AS akun_id,
  b.tgl AS tgl,
  b.sm_debet AS sm_debet,
  b.sm_kredit AS sm_kredit
FROM v_akun_6 a
  LEFT JOIN v_saldo_mutasi_tgl b ON a.level4_id = b.akun_id;

  
-- 38
create view v_akun_6_sum_lr as
SELECT a.level1_nama AS level1_nama,
  a.nama_akun AS nama_akun,
  a.level4_id AS level4_id,
  a.akun_id AS akun_id,
  a.tgl AS tgl,
  a.sm_debet AS sm_debet,
  a.sm_kredit AS sm_kredit
FROM v_akun_6_sum a
  LEFT JOIN t_level4 b ON a.level4_id = b.level4_id
WHERE b.labarugi = 1;


-- 39
create view v_labarugi as
select * from v_akun_4_sum_lr
union
select * from v_akun_5_sum_lr
union
select * from v_akun_6_sum_lr;


CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_akun_4` AS select `a`.`level1_nama` AS `level1_nama`,`b`.`nama_akun` AS `nama_akun`,`b`.`level4_id` AS `level4_id` from (`t_level1` `a` left join `v_akun_jurnal` `b` on((`a`.`level1_no` = left(`b`.`no_akun`,1)))) where (`a`.`level1_no` = 4) order by `b`.`no_akun`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_akun_4_sum` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`b`.`akun_id` AS `akun_id`,`b`.`tgl` AS `tgl`,`b`.`sm_debet` AS `sm_debet`,`b`.`sm_kredit` AS `sm_kredit` from (`v_akun_4` `a` left join `v_saldo_mutasi_tgl` `b` on((`a`.`level4_id` = `b`.`akun_id`)));

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_akun_4_sum_lr` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`a`.`akun_id` AS `akun_id`,`a`.`tgl` AS `tgl`,`a`.`sm_debet` AS `sm_debet`,`a`.`sm_kredit` AS `sm_kredit` from (`v_akun_4_sum` `a` left join `t_level4` `b` on((`a`.`level4_id` = `b`.`level4_id`))) where (`b`.`labarugi` = 1);

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_akun_5` AS select `a`.`level1_nama` AS `level1_nama`,`b`.`nama_akun` AS `nama_akun`,`b`.`level4_id` AS `level4_id` from (`t_level1` `a` left join `v_akun_jurnal` `b` on((`a`.`level1_no` = left(`b`.`no_akun`,1)))) where (`a`.`level1_no` = 5) order by `b`.`no_akun`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_akun_5_sum` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`b`.`akun_id` AS `akun_id`,`b`.`tgl` AS `tgl`,`b`.`sm_debet` AS `sm_debet`,`b`.`sm_kredit` AS `sm_kredit` from (`v_akun_5` `a` left join `v_saldo_mutasi_tgl` `b` on((`a`.`level4_id` = `b`.`akun_id`)));

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_akun_5_sum_lr` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`a`.`akun_id` AS `akun_id`,`a`.`tgl` AS `tgl`,`a`.`sm_debet` AS `sm_debet`,`a`.`sm_kredit` AS `sm_kredit` from (`v_akun_5_sum` `a` left join `t_level4` `b` on((`a`.`level4_id` = `b`.`level4_id`))) where (`b`.`labarugi` = 1);

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_akun_6` AS select `a`.`level1_nama` AS `level1_nama`,`b`.`nama_akun` AS `nama_akun`,`b`.`level4_id` AS `level4_id` from (`t_level1` `a` left join `v_akun_jurnal` `b` on((`a`.`level1_no` = left(`b`.`no_akun`,1)))) where (`a`.`level1_no` = 6) order by `b`.`no_akun`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_akun_6_sum` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`b`.`akun_id` AS `akun_id`,`b`.`tgl` AS `tgl`,`b`.`sm_debet` AS `sm_debet`,`b`.`sm_kredit` AS `sm_kredit` from (`v_akun_6` `a` left join `v_saldo_mutasi_tgl` `b` on((`a`.`level4_id` = `b`.`akun_id`)));

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_akun_6_sum_lr` AS select `a`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,`a`.`level4_id` AS `level4_id`,`a`.`akun_id` AS `akun_id`,`a`.`tgl` AS `tgl`,`a`.`sm_debet` AS `sm_debet`,`a`.`sm_kredit` AS `sm_kredit` from (`v_akun_6_sum` `a` left join `t_level4` `b` on((`a`.`level4_id` = `b`.`level4_id`))) where (`b`.`labarugi` = 1);

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_akun_jurnal` AS select `t_level4`.`level4_id` AS `level4_id`,`t_level1`.`level1_nama` AS `level1_nama`,concat(`t_level1`.`level1_no`,'.',`t_level2`.`level2_no`,'.',`t_level3`.`level3_no`,'.',`t_level4`.`level4_no`) AS `no_akun`,`t_level4`.`level4_nama` AS `nama_akun`,concat(`t_level1`.`level1_no`,'.',`t_level2`.`level2_no`,'.',`t_level3`.`level3_no`,'.',`t_level4`.`level4_no`,' - ',`t_level4`.`level4_nama`) AS `no_nama_akun`,`t_level4`.`jurnal` AS `jurnal`,`t_level4`.`jurnal_kode` AS `jurnal_kode`,`t_level4`.`neraca` AS `neraca`,`t_level4`.`labarugi` AS `labarugi`,(`t_level4`.`sa_debet` - `t_level4`.`sa_kredit`) AS `saldo_awal` from (((`t_level4` join `t_level1` on((`t_level4`.`level1_id` = `t_level1`.`level1_id`))) join `t_level2` on((`t_level4`.`level2_id` = `t_level2`.`level2_id`))) join `t_level3` on((`t_level4`.`level3_id` = `t_level3`.`level3_id`)));

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_akun_saldo` AS select `z`.`level4_id` AS `level4_id`,`z`.`level1_nama` AS `level1_nama`,`z`.`no_akun` AS `no_akun`,`z`.`nama_akun` AS `nama_akun`,`z`.`no_nama_akun` AS `no_nama_akun`,`z`.`jurnal` AS `jurnal`,`z`.`jurnal_kode` AS `jurnal_kode`,`z`.`neraca` AS `neraca`,`z`.`labarugi` AS `labarugi`,`z`.`saldo_awal` AS `saldo_awal`,(case when isnull(`a`.`saldo`) then 0 else `a`.`saldo` end) AS `saldo_01`,(case when isnull(`b`.`saldo`) then 0 else `b`.`saldo` end) AS `saldo_02`,(case when isnull(`c`.`saldo`) then 0 else `c`.`saldo` end) AS `saldo_03`,(case when isnull(`d`.`saldo`) then 0 else `d`.`saldo` end) AS `saldo_04`,(case when isnull(`e`.`saldo`) then 0 else `e`.`saldo` end) AS `saldo_05`,(case when isnull(`f`.`saldo`) then 0 else `f`.`saldo` end) AS `saldo_06`,(case when isnull(`g`.`saldo`) then 0 else `g`.`saldo` end) AS `saldo_07`,(case when isnull(`h`.`saldo`) then 0 else `h`.`saldo` end) AS `saldo_08`,(case when isnull(`i`.`saldo`) then 0 else `i`.`saldo` end) AS `saldo_09`,(case when isnull(`j`.`saldo`) then 0 else `j`.`saldo` end) AS `saldo_10`,(case when isnull(`k`.`saldo`) then 0 else `k`.`saldo` end) AS `saldo_11`,(case when isnull(`l`.`saldo`) then 0 else `l`.`saldo` end) AS `saldo_12` from ((((((((((((`v_akun_jurnal` `z` left join `v_saldo_mutasi_tgl_01` `a` on((`z`.`level4_id` = `a`.`akun_id`))) left join `v_saldo_mutasi_tgl_02` `b` on((`z`.`level4_id` = `b`.`akun_id`))) left join `v_saldo_mutasi_tgl_03` `c` on((`z`.`level4_id` = `c`.`akun_id`))) left join `v_saldo_mutasi_tgl_04` `d` on((`z`.`level4_id` = `d`.`akun_id`))) left join `v_saldo_mutasi_tgl_05` `e` on((`z`.`level4_id` = `e`.`akun_id`))) left join `v_saldo_mutasi_tgl_06` `f` on((`z`.`level4_id` = `f`.`akun_id`))) left join `v_saldo_mutasi_tgl_07` `g` on((`z`.`level4_id` = `g`.`akun_id`))) left join `v_saldo_mutasi_tgl_08` `h` on((`z`.`level4_id` = `h`.`akun_id`))) left join `v_saldo_mutasi_tgl_09` `i` on((`z`.`level4_id` = `i`.`akun_id`))) left join `v_saldo_mutasi_tgl_10` `j` on((`z`.`level4_id` = `j`.`akun_id`))) left join `v_saldo_mutasi_tgl_11` `k` on((`z`.`level4_id` = `k`.`akun_id`))) left join `v_saldo_mutasi_tgl_12` `l` on((`z`.`level4_id` = `l`.`akun_id`)));

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_bukubesar` AS select `a`.`detail_id` AS `detail_id`,`a`.`jurnal_id` AS `jurnal_id`,`a`.`no_bukti` AS `no_bukti`,`a`.`tgl` AS `tgl`,`a`.`ket` AS `ket`,`a`.`akun_id` AS `akun_id`,`a`.`debet` AS `debet`,`a`.`kredit` AS `kredit`,`c`.`no_akun` AS `no_akun`,`c`.`nama_akun` AS `nama_akun`,`c`.`no_nama_akun` AS `no_nama_akun`,`b`.`sa_debet` AS `sa_debet`,`b`.`sa_kredit` AS `sa_kredit`,`b`.`sm_debet` AS `sm_debet`,`b`.`sm_kredit` AS `sm_kredit` from ((`v_kasbank_memorial` `a` left join `t_level4` `b` on((`a`.`akun_id` = `b`.`level4_id`))) left join `v_akun_jurnal` `c` on((`a`.`akun_id` = `c`.`level4_id`)));

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_kasbank` AS select `v_kasbank_jurnal`.`detail_id` AS `detail_id`,`v_kasbank_jurnal`.`jurnal_id` AS `jurnal_id`,`v_kasbank_jurnal`.`no_bukti` AS `no_bukti`,`v_kasbank_jurnal`.`tgl` AS `tgl`,`v_kasbank_jurnal`.`ket` AS `ket`,`v_kasbank_jurnal`.`akun_id` AS `akun_id`,`v_kasbank_jurnal`.`debet` AS `debet`,`v_kasbank_jurnal`.`kredit` AS `kredit` from `v_kasbank_jurnal` union select `v_kasbank_detail`.`detail_id` AS `detail_id`,`v_kasbank_detail`.`jurnal_id` AS `jurnal_id`,`v_kasbank_detail`.`no_bukti` AS `no_bukti`,`v_kasbank_detail`.`tgl` AS `tgl`,`v_kasbank_detail`.`ket` AS `ket`,`v_kasbank_detail`.`akun_id` AS `akun_id`,`v_kasbank_detail`.`debet` AS `debet`,`v_kasbank_detail`.`kredit` AS `kredit` from `v_kasbank_detail`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_kasbank_detail` AS select `a`.`detail_id` AS `detail_id`,`a`.`jurnal_id` AS `jurnal_id`,`b`.`no_bukti` AS `no_bukti`,`b`.`tgl` AS `tgl`,`b`.`ket` AS `ket`,`a`.`akun_id` AS `akun_id`,(case when (`a`.`dk` = 0) then `a`.`nilai` else 0 end) AS `debet`,(case when (`a`.`dk` = 1) then `a`.`nilai` else 0 end) AS `kredit` from (`t_detail` `a` left join `t_jurnal` `b` on((`a`.`jurnal_id` = `b`.`jurnal_id`)));

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_kasbank_jurnal` AS select NULL AS `detail_id`,`t_jurnal`.`jurnal_id` AS `jurnal_id`,`t_jurnal`.`no_bukti` AS `no_bukti`,`t_jurnal`.`tgl` AS `tgl`,`t_jurnal`.`ket` AS `ket`,`t_jurnal`.`akun_id` AS `akun_id`,(case when (`t_jurnal`.`jenis_jurnal` = 'M') then `t_jurnal`.`nilai` else 0 end) AS `debet`,(case when (`t_jurnal`.`jenis_jurnal` <> 'M') then `t_jurnal`.`nilai` else 0 end) AS `kredit` from `t_jurnal`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_kasbank_memorial` AS select `v_kasbank`.`detail_id` AS `detail_id`,`v_kasbank`.`jurnal_id` AS `jurnal_id`,`v_kasbank`.`no_bukti` AS `no_bukti`,`v_kasbank`.`tgl` AS `tgl`,`v_kasbank`.`ket` AS `ket`,`v_kasbank`.`akun_id` AS `akun_id`,`v_kasbank`.`debet` AS `debet`,`v_kasbank`.`kredit` AS `kredit` from `v_kasbank` union select `v_memorial`.`detailm_id` AS `detailm_id`,`v_memorial`.`jurnalm_id` AS `jurnalm_id`,`v_memorial`.`no_buktim` AS `no_buktim`,`v_memorial`.`tglm` AS `tglm`,`v_memorial`.`ketm` AS `ketm`,`v_memorial`.`akunm_id` AS `akunm_id`,`v_memorial`.`nilaim_debet` AS `nilaim_debet`,`v_memorial`.`nilaim_kredit` AS `nilaim_kredit` from `v_memorial`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_labarugi` AS select `v_akun_4_sum_lr`.`level1_nama` AS `level1_nama`,`v_akun_4_sum_lr`.`nama_akun` AS `nama_akun`,`v_akun_4_sum_lr`.`level4_id` AS `level4_id`,`v_akun_4_sum_lr`.`akun_id` AS `akun_id`,`v_akun_4_sum_lr`.`tgl` AS `tgl`,`v_akun_4_sum_lr`.`sm_debet` AS `sm_debet`,`v_akun_4_sum_lr`.`sm_kredit` AS `sm_kredit` from `v_akun_4_sum_lr` union select `v_akun_5_sum_lr`.`level1_nama` AS `level1_nama`,`v_akun_5_sum_lr`.`nama_akun` AS `nama_akun`,`v_akun_5_sum_lr`.`level4_id` AS `level4_id`,`v_akun_5_sum_lr`.`akun_id` AS `akun_id`,`v_akun_5_sum_lr`.`tgl` AS `tgl`,`v_akun_5_sum_lr`.`sm_debet` AS `sm_debet`,`v_akun_5_sum_lr`.`sm_kredit` AS `sm_kredit` from `v_akun_5_sum_lr` union select `v_akun_6_sum_lr`.`level1_nama` AS `level1_nama`,`v_akun_6_sum_lr`.`nama_akun` AS `nama_akun`,`v_akun_6_sum_lr`.`level4_id` AS `level4_id`,`v_akun_6_sum_lr`.`akun_id` AS `akun_id`,`v_akun_6_sum_lr`.`tgl` AS `tgl`,`v_akun_6_sum_lr`.`sm_debet` AS `sm_debet`,`v_akun_6_sum_lr`.`sm_kredit` AS `sm_kredit` from `v_akun_6_sum_lr`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_memorial` AS select `a`.`detailm_id` AS `detailm_id`,`a`.`jurnalm_id` AS `jurnalm_id`,`b`.`no_buktim` AS `no_buktim`,`b`.`tglm` AS `tglm`,`b`.`ketm` AS `ketm`,`a`.`akunm_id` AS `akunm_id`,`a`.`nilaim_debet` AS `nilaim_debet`,`a`.`nilaim_kredit` AS `nilaim_kredit` from (`t_detailm` `a` left join `t_jurnalm` `b` on((`a`.`jurnalm_id` = `b`.`jurnalm_id`)));

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi` AS select `v_kasbank_memorial`.`akun_id` AS `akun_id`,(case when ((sum(`v_kasbank_memorial`.`debet`) - sum(`v_kasbank_memorial`.`kredit`)) >= 0) then (sum(`v_kasbank_memorial`.`debet`) - sum(`v_kasbank_memorial`.`kredit`)) else 0 end) AS `sm_debet`,(case when ((sum(`v_kasbank_memorial`.`debet`) - sum(`v_kasbank_memorial`.`kredit`)) < 0) then abs((sum(`v_kasbank_memorial`.`debet`) - sum(`v_kasbank_memorial`.`kredit`))) else 0 end) AS `sm_kredit` from `v_kasbank_memorial` group by `v_kasbank_memorial`.`akun_id`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi_tgl` AS select `v_kasbank_memorial`.`akun_id` AS `akun_id`,`v_kasbank_memorial`.`tgl` AS `tgl`,(case when ((sum(`v_kasbank_memorial`.`debet`) - sum(`v_kasbank_memorial`.`kredit`)) >= 0) then (sum(`v_kasbank_memorial`.`debet`) - sum(`v_kasbank_memorial`.`kredit`)) else 0 end) AS `sm_debet`,(case when ((sum(`v_kasbank_memorial`.`debet`) - sum(`v_kasbank_memorial`.`kredit`)) < 0) then abs((sum(`v_kasbank_memorial`.`debet`) - sum(`v_kasbank_memorial`.`kredit`))) else 0 end) AS `sm_kredit` from `v_kasbank_memorial` group by `v_kasbank_memorial`.`akun_id`,`v_kasbank_memorial`.`tgl`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi_tgl_01` AS select `v_saldo_mutasi_tgl`.`akun_id` AS `akun_id`,(sum(`v_saldo_mutasi_tgl`.`sm_debet`) - sum(`v_saldo_mutasi_tgl`.`sm_kredit`)) AS `saldo` from `v_saldo_mutasi_tgl` where ((month(`v_saldo_mutasi_tgl`.`tgl`) = 1) and (year(`v_saldo_mutasi_tgl`.`tgl`) = 2017)) group by `v_saldo_mutasi_tgl`.`akun_id`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi_tgl_02` AS select `v_saldo_mutasi_tgl`.`akun_id` AS `akun_id`,(sum(`v_saldo_mutasi_tgl`.`sm_debet`) - sum(`v_saldo_mutasi_tgl`.`sm_kredit`)) AS `saldo` from `v_saldo_mutasi_tgl` where ((month(`v_saldo_mutasi_tgl`.`tgl`) = 2) and (year(`v_saldo_mutasi_tgl`.`tgl`) = 2017)) group by `v_saldo_mutasi_tgl`.`akun_id`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi_tgl_03` AS select `v_saldo_mutasi_tgl`.`akun_id` AS `akun_id`,(sum(`v_saldo_mutasi_tgl`.`sm_debet`) - sum(`v_saldo_mutasi_tgl`.`sm_kredit`)) AS `saldo` from `v_saldo_mutasi_tgl` where ((month(`v_saldo_mutasi_tgl`.`tgl`) = 3) and (year(`v_saldo_mutasi_tgl`.`tgl`) = 2017)) group by `v_saldo_mutasi_tgl`.`akun_id`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi_tgl_04` AS select `v_saldo_mutasi_tgl`.`akun_id` AS `akun_id`,(sum(`v_saldo_mutasi_tgl`.`sm_debet`) - sum(`v_saldo_mutasi_tgl`.`sm_kredit`)) AS `saldo` from `v_saldo_mutasi_tgl` where ((month(`v_saldo_mutasi_tgl`.`tgl`) = 4) and (year(`v_saldo_mutasi_tgl`.`tgl`) = 2017)) group by `v_saldo_mutasi_tgl`.`akun_id`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi_tgl_05` AS select `v_saldo_mutasi_tgl`.`akun_id` AS `akun_id`,(sum(`v_saldo_mutasi_tgl`.`sm_debet`) - sum(`v_saldo_mutasi_tgl`.`sm_kredit`)) AS `saldo` from `v_saldo_mutasi_tgl` where ((month(`v_saldo_mutasi_tgl`.`tgl`) = 5) and (year(`v_saldo_mutasi_tgl`.`tgl`) = 2017)) group by `v_saldo_mutasi_tgl`.`akun_id`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi_tgl_06` AS select `v_saldo_mutasi_tgl`.`akun_id` AS `akun_id`,(sum(`v_saldo_mutasi_tgl`.`sm_debet`) - sum(`v_saldo_mutasi_tgl`.`sm_kredit`)) AS `saldo` from `v_saldo_mutasi_tgl` where ((month(`v_saldo_mutasi_tgl`.`tgl`) = 6) and (year(`v_saldo_mutasi_tgl`.`tgl`) = 2017)) group by `v_saldo_mutasi_tgl`.`akun_id`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi_tgl_07` AS select `v_saldo_mutasi_tgl`.`akun_id` AS `akun_id`,(sum(`v_saldo_mutasi_tgl`.`sm_debet`) - sum(`v_saldo_mutasi_tgl`.`sm_kredit`)) AS `saldo` from `v_saldo_mutasi_tgl` where ((month(`v_saldo_mutasi_tgl`.`tgl`) = 7) and (year(`v_saldo_mutasi_tgl`.`tgl`) = 2017)) group by `v_saldo_mutasi_tgl`.`akun_id`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi_tgl_08` AS select `v_saldo_mutasi_tgl`.`akun_id` AS `akun_id`,(sum(`v_saldo_mutasi_tgl`.`sm_debet`) - sum(`v_saldo_mutasi_tgl`.`sm_kredit`)) AS `saldo` from `v_saldo_mutasi_tgl` where ((month(`v_saldo_mutasi_tgl`.`tgl`) = 8) and (year(`v_saldo_mutasi_tgl`.`tgl`) = 2017)) group by `v_saldo_mutasi_tgl`.`akun_id`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi_tgl_09` AS select `v_saldo_mutasi_tgl`.`akun_id` AS `akun_id`,(sum(`v_saldo_mutasi_tgl`.`sm_debet`) - sum(`v_saldo_mutasi_tgl`.`sm_kredit`)) AS `saldo` from `v_saldo_mutasi_tgl` where ((month(`v_saldo_mutasi_tgl`.`tgl`) = 9) and (year(`v_saldo_mutasi_tgl`.`tgl`) = 2017)) group by `v_saldo_mutasi_tgl`.`akun_id`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi_tgl_10` AS select `v_saldo_mutasi_tgl`.`akun_id` AS `akun_id`,(sum(`v_saldo_mutasi_tgl`.`sm_debet`) - sum(`v_saldo_mutasi_tgl`.`sm_kredit`)) AS `saldo` from `v_saldo_mutasi_tgl` where ((month(`v_saldo_mutasi_tgl`.`tgl`) = 10) and (year(`v_saldo_mutasi_tgl`.`tgl`) = 2017)) group by `v_saldo_mutasi_tgl`.`akun_id`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi_tgl_11` AS select `v_saldo_mutasi_tgl`.`akun_id` AS `akun_id`,(sum(`v_saldo_mutasi_tgl`.`sm_debet`) - sum(`v_saldo_mutasi_tgl`.`sm_kredit`)) AS `saldo` from `v_saldo_mutasi_tgl` where ((month(`v_saldo_mutasi_tgl`.`tgl`) = 11) and (year(`v_saldo_mutasi_tgl`.`tgl`) = 2017)) group by `v_saldo_mutasi_tgl`.`akun_id`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_saldo_mutasi_tgl_12` AS select `v_saldo_mutasi_tgl`.`akun_id` AS `akun_id`,(sum(`v_saldo_mutasi_tgl`.`sm_debet`) - sum(`v_saldo_mutasi_tgl`.`sm_kredit`)) AS `saldo` from `v_saldo_mutasi_tgl` where ((month(`v_saldo_mutasi_tgl`.`tgl`) = 12) and (year(`v_saldo_mutasi_tgl`.`tgl`) = 2017)) group by `v_saldo_mutasi_tgl`.`akun_id`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_summary_bukubesar` AS select `v_summary_bukubesar_1`.`level1_nama` AS `level1_nama`,`v_summary_bukubesar_1`.`nama_akun` AS `nama_akun`,`v_summary_bukubesar_1`.`sm_debet` AS `sm_debet`,`v_summary_bukubesar_1`.`sm_kredit` AS `sm_kredit`,`v_summary_bukubesar_1`.`akun_id` AS `akun_id` from `v_summary_bukubesar_1` union select `v_summary_bukubesar_6`.`level1_nama` AS `level1_nama`,`v_summary_bukubesar_6`.`nama_akun` AS `nama_akun`,`v_summary_bukubesar_6`.`sm_debet` AS `sm_debet`,`v_summary_bukubesar_6`.`sm_kredit` AS `sm_kredit`,`v_summary_bukubesar_6`.`akun_id` AS `akun_id` from `v_summary_bukubesar_6` union select `v_summary_bukubesar_3`.`level1_nama` AS `level1_nama`,`v_summary_bukubesar_3`.`nama_akun` AS `nama_akun`,`v_summary_bukubesar_3`.`sm_debet` AS `sm_debet`,`v_summary_bukubesar_3`.`sm_kredit` AS `sm_kredit`,`v_summary_bukubesar_3`.`akun_id` AS `akun_id` from `v_summary_bukubesar_3` union select `v_summary_bukubesar_4`.`level1_nama` AS `level1_nama`,`v_summary_bukubesar_4`.`nama_akun` AS `nama_akun`,`v_summary_bukubesar_4`.`sm_debet` AS `sm_debet`,`v_summary_bukubesar_4`.`sm_kredit` AS `sm_kredit`,`v_summary_bukubesar_4`.`akun_id` AS `akun_id` from `v_summary_bukubesar_4`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_summary_bukubesar_1` AS select `c`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,(case when isnull(`b`.`sm_debet`) then 0 else `b`.`sm_debet` end) AS `sm_debet`,(case when isnull(`b`.`sm_kredit`) then 0 else `b`.`sm_kredit` end) AS `sm_kredit`,`b`.`akun_id` AS `akun_id` from ((`v_akun_jurnal` `a` left join `v_saldo_mutasi` `b` on((`a`.`level4_id` = `b`.`akun_id`))) left join `t_level1` `c` on((`c`.`level1_id` = left(`a`.`no_akun`,1)))) where (left(`a`.`no_akun`,1) = '1') order by `a`.`no_akun`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_summary_bukubesar_3` AS select `c`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,(case when isnull(`b`.`sm_debet`) then 0 else `b`.`sm_debet` end) AS `sm_debet`,(case when isnull(`b`.`sm_kredit`) then 0 else `b`.`sm_kredit` end) AS `sm_kredit`,`b`.`akun_id` AS `akun_id` from ((`v_akun_jurnal` `a` left join `v_saldo_mutasi` `b` on((`a`.`level4_id` = `b`.`akun_id`))) left join `t_level1` `c` on((`c`.`level1_id` = left(`a`.`no_akun`,1)))) where (left(`a`.`no_akun`,1) = '3') order by `a`.`no_akun`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_summary_bukubesar_4` AS select `c`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,(case when isnull(`b`.`sm_debet`) then 0 else `b`.`sm_debet` end) AS `sm_debet`,(case when isnull(`b`.`sm_kredit`) then 0 else `b`.`sm_kredit` end) AS `sm_kredit`,`b`.`akun_id` AS `akun_id` from ((`v_akun_jurnal` `a` left join `v_saldo_mutasi` `b` on((`a`.`level4_id` = `b`.`akun_id`))) left join `t_level1` `c` on((`c`.`level1_id` = left(`a`.`no_akun`,1)))) where (left(`a`.`no_akun`,1) = '4') order by `a`.`no_akun`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_summary_bukubesar_6` AS select `c`.`level1_nama` AS `level1_nama`,`a`.`nama_akun` AS `nama_akun`,(case when isnull(`b`.`sm_debet`) then 0 else `b`.`sm_debet` end) AS `sm_debet`,(case when isnull(`b`.`sm_kredit`) then 0 else `b`.`sm_kredit` end) AS `sm_kredit`,`b`.`akun_id` AS `akun_id` from ((`v_akun_jurnal` `a` left join `v_saldo_mutasi` `b` on((`a`.`level4_id` = `b`.`akun_id`))) left join `t_level1` `c` on((`c`.`level1_id` = left(`a`.`no_akun`,1)))) where (left(`a`.`no_akun`,1) = '6') order by `a`.`no_akun`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_summary_lr_4` AS select `a`.`level1_nama` AS `level1_nama`,`b`.`nama_akun` AS `nama_akun`,`b`.`level4_id` AS `level4_id` from (`t_level1` `a` left join `v_akun_jurnal` `b` on((`a`.`level1_no` = left(`b`.`no_akun`,1)))) where ((`a`.`level1_no` = 4) and (`b`.`labarugi` = 1)) order by `b`.`no_akun`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_summary_lr_5` AS select `a`.`level1_nama` AS `level1_nama`,`b`.`nama_akun` AS `nama_akun`,`b`.`level4_id` AS `level4_id` from (`t_level1` `a` left join `v_akun_jurnal` `b` on((`a`.`level1_no` = left(`b`.`no_akun`,1)))) where ((`a`.`level1_no` = 5) and (`b`.`labarugi` = 1)) order by `b`.`no_akun`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_summary_lr_6` AS select `a`.`level1_nama` AS `level1_nama`,`b`.`nama_akun` AS `nama_akun`,`b`.`level4_id` AS `level4_id` from (`t_level1` `a` left join `v_akun_jurnal` `b` on((`a`.`level1_no` = left(`b`.`no_akun`,1)))) where ((`a`.`level1_no` = 6) and (`b`.`labarugi` = 1)) order by `b`.`no_akun`;
