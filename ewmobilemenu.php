<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(22, "mmi_home_php", $Language->MenuPhrase("22", "MenuText"), "home.php", -1, "", AllowListMenu('{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}home.php'), FALSE, TRUE);
$RootMenu->AddMenuItem(10065, "mmci_Setup", $Language->MenuPhrase("10065", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(1, "mmi_t_anggota", $Language->MenuPhrase("1", "MenuText"), "t_anggotalist.php", 10065, "", AllowListMenu('{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}t_anggota'), FALSE, FALSE);
$RootMenu->AddMenuItem(19, "mmi_view_akun_php", $Language->MenuPhrase("19", "MenuText"), "view_akun.php", 10065, "", AllowListMenu('{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}view_akun.php'), FALSE, TRUE);
$RootMenu->AddMenuItem(7, "mmi_t_level1", $Language->MenuPhrase("7", "MenuText"), "t_level1list.php", 19, "", AllowListMenu('{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}t_level1'), FALSE, FALSE);
$RootMenu->AddMenuItem(8, "mmi_t_level2", $Language->MenuPhrase("8", "MenuText"), "t_level2list.php", 19, "", AllowListMenu('{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}t_level2'), FALSE, FALSE);
$RootMenu->AddMenuItem(9, "mmi_t_level3", $Language->MenuPhrase("9", "MenuText"), "t_level3list.php", 19, "", AllowListMenu('{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}t_level3'), FALSE, FALSE);
$RootMenu->AddMenuItem(10, "mmi_t_level4", $Language->MenuPhrase("10", "MenuText"), "t_level4list.php", 19, "", AllowListMenu('{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}t_level4'), FALSE, FALSE);
$RootMenu->AddMenuItem(16, "mmi_t_user", $Language->MenuPhrase("16", "MenuText"), "t_userlist.php", 10065, "", AllowListMenu('{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}t_user'), FALSE, FALSE);
$RootMenu->AddMenuItem(10106, "mmci_Input", $Language->MenuPhrase("10106", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(20, "mmi_t_jurnal", $Language->MenuPhrase("20", "MenuText"), "t_jurnallist.php", 10106, "", AllowListMenu('{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}t_jurnal'), FALSE, FALSE);
$RootMenu->AddMenuItem(27, "mmi_t_jurnalm", $Language->MenuPhrase("27", "MenuText"), "t_jurnalmlist.php", 10106, "", AllowListMenu('{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}t_jurnalm'), FALSE, FALSE);
$RootMenu->AddMenuItem(10128, "mmci_Laporan", $Language->MenuPhrase("10128", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(10142, "mmi_r_bukubesar0_php", $Language->MenuPhrase("10142", "MenuText"), "r_bukubesar0.php", 10128, "", AllowListMenu('{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}r_bukubesar0.php'), FALSE, TRUE);
$RootMenu->AddMenuItem(-2, "mmi_changepwd", $Language->Phrase("ChangePwd"), "changepwd.php", -1, "", IsLoggedIn() && !IsSysAdmin());
$RootMenu->AddMenuItem(-1, "mmi_logout", $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, "mmi_login", $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
<!-- End Main Menu -->
