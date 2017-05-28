-- untuk keperluan view akun keseluruhan
CREATE VIEW `v_akun_jurnal` AS
Select t_level4.level4_id As level4_id,
  Concat(t_level1.level1_no, '.', t_level2.level2_no, '.', t_level3.level3_no,
  '.', t_level4.level4_no) As no_akun,
  t_level4.level4_nama As nama_akun,
  Concat(t_level1.level1_no, '.', t_level2.level2_no, '.', t_level3.level3_no,
  '.', t_level4.level4_no, ' - ', t_level4.level4_nama) As no_nama_akun,
  t_level4.jurnal As jurnal,
  t_level4.jurnal_kode As jurnal_kode
From ((t_level4
  Join t_level1 On t_level4.level1_id = t_level1.level1_id)
  Join t_level2 On t_level4.level2_id = t_level2.level2_id)
  Join t_level3 On t_level4.level3_id = t_level3.level3_id;

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

-- untuk laporan buku besar, tapi keliatannya masih salah
CREATE VIEW `v_bukubesar` AS
Select a.detail_id As detail_id,
  a.jurnal_id As jurnal_id,
  a.no_bukti As no_bukti,
  a.tgl As tgl,
  a.ket As ket,
  a.akun_id As akun_id,
  a.debet As debet,
  a.kredit As kredit,
  c.no_akun As no_akun,
  c.nama_akun As nama_akun,
  c.no_nama_akun As no_nama_akun,
  b.saldo_awal As saldo_awal,
  b.saldo As saldo
From (v_kasbank_memorial a
  Left Join t_level4 b On a.akun_id = b.level4_id)
  Left Join v_akun_jurnal c On a.akun_id = c.level4_id;
  
create view v_saldo_mutasi as
select
	akun_id
    , sum(debet) - sum(kredit) as saldo_mutasi
from
	v_kasbank_memorial
group by
	akun_id;

create view v_summary_bukubesar_1 as
Select c.level1_nama As level1_nama,
  a.nama_akun As nama_akun,
  (Case When isnull(b.saldo_mutasi) Then 0 Else b.saldo_mutasi
  End) As saldo_mutasi,
  b.akun_id
From (v_akun_jurnal a
  Left Join v_saldo_mutasi b On a.level4_id = b.akun_id)
  Left Join t_level1 c On c.level1_id = Left(a.no_akun, 1)
Where Left(a.no_akun, 1) = '1'
Order By a.no_akun;

create view v_summary_bukubesar_6 as
Select c.level1_nama As level1_nama,
  a.nama_akun As nama_akun,
  (Case When isnull(b.saldo_mutasi) Then 0 Else b.saldo_mutasi
  End) As saldo_mutasi,
  b.akun_id
From (v_akun_jurnal a
  Left Join v_saldo_mutasi b On a.level4_id = b.akun_id)
  Left Join t_level1 c On c.level1_id = Left(a.no_akun, 1)
Where Left(a.no_akun, 1) = '6'
Order By a.no_akun;

create view v_summary_bukubesar_3 as
Select c.level1_nama As level1_nama,
  a.nama_akun As nama_akun,
  (Case When isnull(b.saldo_mutasi) Then 0 Else b.saldo_mutasi
  End) As saldo_mutasi,
  b.akun_id
From (v_akun_jurnal a
  Left Join v_saldo_mutasi b On a.level4_id = b.akun_id)
  Left Join t_level1 c On c.level1_id = Left(a.no_akun, 1)
Where Left(a.no_akun, 1) = '3'
Order By a.no_akun;

create view v_summary_bukubesar_4 as
Select c.level1_nama As level1_nama,
  a.nama_akun As nama_akun,
  (Case When isnull(b.saldo_mutasi) Then 0 Else b.saldo_mutasi
  End) As saldo_mutasi,
  b.akun_id
From (v_akun_jurnal a
  Left Join v_saldo_mutasi b On a.level4_id = b.akun_id)
  Left Join t_level1 c On c.level1_id = Left(a.no_akun, 1)
Where Left(a.no_akun, 1) = '4'
Order By a.no_akun;

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