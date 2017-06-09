
-- 01
-- data list akun
CREATE  VIEW `v_akun_jurnal` AS select `t_level4`.`level4_id` AS `level4_id`,concat(`t_level1`.`level1_no`,'.',`t_level2`.`level2_no`,'.',`t_level3`.`level3_no`,'.',`t_level4`.`level4_no`) AS `no_akun`,`t_level4`.`level4_nama` AS `nama_akun`,concat(`t_level1`.`level1_no`,'.',`t_level2`.`level2_no`,'.',`t_level3`.`level3_no`,'.',`t_level4`.`level4_no`,' - ',`t_level4`.`level4_nama`) AS `no_nama_akun`,`t_level4`.`jurnal` AS `jurnal`,`t_level4`.`jurnal_kode` AS `jurnal_kode`,`t_level4`.`neraca` AS `neraca`,`t_level4`.`labarugi` AS `labarugi` from (((`t_level4` join `t_level1` on((`t_level4`.`level1_id` = `t_level1`.`level1_id`))) join `t_level2` on((`t_level4`.`level2_id` = `t_level2`.`level2_id`))) join `t_level3` on((`t_level4`.`level3_id` = `t_level3`.`level3_id`)));


-- 02, 03, 04
-- data jurnal kas & bank
CREATE  VIEW `v_kasbank_jurnal` AS select NULL AS `detail_id`,`t_jurnal`.`jurnal_id` AS `jurnal_id`,`t_jurnal`.`no_bukti` AS `no_bukti`,`t_jurnal`.`tgl` AS `tgl`,`t_jurnal`.`ket` AS `ket`,`t_jurnal`.`akun_id` AS `akun_id`,(case when (`t_jurnal`.`jenis_jurnal` = 'M') then `t_jurnal`.`nilai` else 0 end) AS `debet`,(case when (`t_jurnal`.`jenis_jurnal` <> 'M') then `t_jurnal`.`nilai` else 0 end) AS `kredit` from `t_jurnal`;
CREATE  VIEW `v_kasbank_detail` AS select `a`.`detail_id` AS `detail_id`,`a`.`jurnal_id` AS `jurnal_id`,`b`.`no_bukti` AS `no_bukti`,`b`.`tgl` AS `tgl`,`b`.`ket` AS `ket`,`a`.`akun_id` AS `akun_id`,(case when (`a`.`dk` = 0) then `a`.`nilai` else 0 end) AS `debet`,(case when (`a`.`dk` = 1) then `a`.`nilai` else 0 end) AS `kredit` from (`t_detail` `a` left join `t_jurnal` `b` on((`a`.`jurnal_id` = `b`.`jurnal_id`)));
CREATE  VIEW `v_kasbank` AS select `v_kasbank_jurnal`.`detail_id` AS `detail_id`,`v_kasbank_jurnal`.`jurnal_id` AS `jurnal_id`,`v_kasbank_jurnal`.`no_bukti` AS `no_bukti`,`v_kasbank_jurnal`.`tgl` AS `tgl`,`v_kasbank_jurnal`.`ket` AS `ket`,`v_kasbank_jurnal`.`akun_id` AS `akun_id`,`v_kasbank_jurnal`.`debet` AS `debet`,`v_kasbank_jurnal`.`kredit` AS `kredit` from `v_kasbank_jurnal` union select `v_kasbank_detail`.`detail_id` AS `detail_id`,`v_kasbank_detail`.`jurnal_id` AS `jurnal_id`,`v_kasbank_detail`.`no_bukti` AS `no_bukti`,`v_kasbank_detail`.`tgl` AS `tgl`,`v_kasbank_detail`.`ket` AS `ket`,`v_kasbank_detail`.`akun_id` AS `akun_id`,`v_kasbank_detail`.`debet` AS `debet`,`v_kasbank_detail`.`kredit` AS `kredit` from `v_kasbank_detail`;


-- 05
-- data jurnal memorial
CREATE  VIEW `v_memorial` AS select `a`.`detailm_id` AS `detailm_id`,`a`.`jurnalm_id` AS `jurnalm_id`,`b`.`no_buktim` AS `no_buktim`,`b`.`tglm` AS `tglm`,`b`.`ketm` AS `ketm`,`a`.`akunm_id` AS `akunm_id`,`a`.`nilaim_debet` AS `nilaim_debet`,`a`.`nilaim_kredit` AS `nilaim_kredit` from (`t_detailm` `a` left join `t_jurnalm` `b` on((`a`.`jurnalm_id` = `b`.`jurnalm_id`)));


-- 06
-- data jurnal => kas & bank dan memorial
CREATE  VIEW `v_kasbank_memorial` AS select `v_kasbank`.`detail_id` AS `detail_id`,`v_kasbank`.`jurnal_id` AS `jurnal_id`,`v_kasbank`.`no_bukti` AS `no_bukti`,`v_kasbank`.`tgl` AS `tgl`,`v_kasbank`.`ket` AS `ket`,`v_kasbank`.`akun_id` AS `akun_id`,`v_kasbank`.`debet` AS `debet`,`v_kasbank`.`kredit` AS `kredit` from `v_kasbank` union select `v_memorial`.`detailm_id` AS `detailm_id`,`v_memorial`.`jurnalm_id` AS `jurnalm_id`,`v_memorial`.`no_buktim` AS `no_buktim`,`v_memorial`.`tglm` AS `tglm`,`v_memorial`.`ketm` AS `ketm`,`v_memorial`.`akunm_id` AS `akunm_id`,`v_memorial`.`nilaim_debet` AS `nilaim_debet`,`v_memorial`.`nilaim_kredit` AS `nilaim_kredit` from `v_memorial`;


-- 07
-- data saldo mutasi dari data jurnal
CREATE  VIEW `v_mutasi` AS select `v_kasbank_memorial`.`akun_id` AS `akun_id`,`v_kasbank_memorial`.`tgl` AS `tgl`,(case when ((sum(`v_kasbank_memorial`.`debet`) - sum(`v_kasbank_memorial`.`kredit`)) >= 0) then (sum(`v_kasbank_memorial`.`debet`) - sum(`v_kasbank_memorial`.`kredit`)) else 0 end) AS `sm_debet`,(case when ((sum(`v_kasbank_memorial`.`debet`) - sum(`v_kasbank_memorial`.`kredit`)) < 0) then abs((sum(`v_kasbank_memorial`.`debet`) - sum(`v_kasbank_memorial`.`kredit`))) else 0 end) AS `sm_kredit` from `v_kasbank_memorial` group by `v_kasbank_memorial`.`akun_id`,`v_kasbank_memorial`.`tgl`;


