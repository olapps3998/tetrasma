<?php

// Global user functions
// Page Loading event
function Page_Loading() {

	//echo "Page Loading";
}

// Page Rendering event
function Page_Rendering() {

	//echo "Page Rendering";
}

// Page Unloaded event
function Page_Unloaded() {

	//echo "Page Unloaded";
}

function GetNextKodeJenis() {
	$sNextKode = "";
	$sLastKode = "";
	$value = ew_ExecuteScalar("SELECT Kode FROM t_jenis ORDER BY Kode DESC");
	if ($value != "") { // jika sudah ada, langsung ambil dan proses...
		$sLastKode = intval(substr($value, 3, 3)); // ambil 3 digit terakhir
		$sLastKode = intval($sLastKode) + 1; // konversi ke integer, lalu tambahkan satu
		$sNextKode = "JNS" . sprintf('%03s', $sLastKode); // format hasilnya dan tambahkan prefix
		if (strlen($sNextKode) > 6) {
			$sNextKode = "JNS999";
		}
	} else { // jika belum ada, gunakan kode yang pertama
		$sNextKode = "JNS001";
	}
	return $sNextKode;
}

function GetNextNo_buktim() {
	$sNextKode = "";
	$sLastKode = "";
	$value = ew_ExecuteScalar("SELECT no_buktim FROM t_jurnalm ORDER BY no_buktim DESC");
	if ($value != "") { // jika sudah ada, langsung ambil dan proses...
		$sLastKode = intval(substr($value, 2, 3)); // ambil 3 digit terakhir
		$sLastKode = intval($sLastKode) + 1; // konversi ke integer, lalu tambahkan satu
		$sNextKode = "JM" . sprintf('%03s', $sLastKode).date('my'); // format hasilnya dan tambahkan prefix
		if (strlen($sNextKode) > 9) {
			$sNextKode = "JM001".date('my');
		}
	} else { // jika belum ada, gunakan kode yang pertama
		$sNextKode = "JM001".date('my');
	}
	return $sNextKode;
}

function tgl_indo($tgl) {
	$a_namabln = array(
		1 => "Jan",
		"Feb",
		"Mar",
		"Apr",
		"Mei",
		"Jun",
		"Jul",
		"Ags",
		"Sep",
		"Okt",
		"Nov",
		"Des");
	$a_hari = array(
		"Min",
		"Sen",
		"Sel",
		"Rab",
		"Kam",
		"Jum",
		"Sab");
	$tgl_data = strtotime($tgl);

	//$tgl_data = $tgl;
	$tanggal = date("d", $tgl_data);
	$bulan = $a_namabln[intval(date("m", $tgl_data))];
	$tahun = date("Y", $tgl_data);

	//$hari = date("w", $tgl);
	// return $a_hari[date("w", $tgl_data)].", ".$tanggal." ".$bulan." ".$tahun;

	return $tanggal." ".$bulan." ".$tahun;
}
?>
