<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php $EW_ROOT_RELATIVE_PATH = ""; ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "t_userinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$r_neraca1_php = NULL; // Initialize page object first

class cr_neraca1_php {

	// Page ID
	var $PageID = 'custom';

	// Project ID
	var $ProjectID = "{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}";

	// Table name
	var $TableName = 'r_neraca1.php';

	// Page object name
	var $PageObjName = 'r_neraca1_php';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Methods to clear message
	function ClearMessage() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
	}

	function ClearFailureMessage() {
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
	}

	function ClearSuccessMessage() {
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
	}

	function ClearWarningMessage() {
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	function ClearMessages() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		if (method_exists($this, "Message_Showing"))
			$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $Token = "";
	var $TokenTimeout = 0;
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME], $this->TokenTimeout);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		global $UserTable, $UserTableConn;
		$GLOBALS["Page"] = &$this;
		$this->TokenTimeout = ew_SessionTimeoutTime();

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'custom', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'r_neraca1.php', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// User table object (t_user)
		if (!isset($UserTable)) {
			$UserTable = new ct_user();
			$UserTableConn = Conn($UserTable->DBID);
		}
	}

	//
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loaded();
		if (!$Security->CanReport()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			$this->Page_Terminate(ew_GetUrl("index.php"));
		}
		if ($Security->IsLoggedIn()) {
			$Security->UserID_Loading();
			$Security->LoadUserID();
			$Security->UserID_Loaded();
		}

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Create Token
		$this->CreateToken();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $gsExportFile, $gTmpImages;

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		 // Close connection

		ew_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}

	//
	// Page main
	//
	function Page_Main() {

		// Set up Breadcrumb
		$this->SetupBreadcrumb();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("custom", "r_neraca1_php", $url, "", "r_neraca1_php", TRUE);
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($r_neraca1_php)) $r_neraca1_php = new cr_neraca1_php();

// Page init
$r_neraca1_php->Page_Init();

// Page main
$r_neraca1_php->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();
?>
<?php include_once "header.php" ?>
<?php if (!@$gbSkipHeaderFooter) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<style>
	td {
		padding: 3px;
	}
	table {
		width: 50%;
	}
	tr:nth-child(even) {background-color: #f2f2f2}
</style>

<?php
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
?>

<h3>Laporan Neraca</h3>
<h4>Periode <?php echo ($_POST["bulan"] != 0 ? $a_namabln[$_POST["bulan"]] : "Tahun")." ".$_POST["tahun"];?></h4>
<br>

<table>

<?php
// akun aktiva
$aktiva = 0;
$q = "select * from v_akun_1_sum_nrc where ";
if ($_POST["bulan"] != 0) {$q .= "month(tgl) = ".$_POST["bulan"]." and ";}
$q .= "year(tgl) = ".$_POST["tahun"];
$rs = Conn()->Execute($q);
if (!$rs->EOF) {
	$level1_nama = $rs->fields["level1_nama"];
	?>
	<tr><td colspan="4"><b><?php echo $rs->fields["level1_nama"];?></b></td></tr>
	<?php
	while (!$rs->EOF) {
		$level4_id = $rs->fields["level4_id"];
		$nama_akun = $rs->fields["nama_akun"];
		$subtotal = 0;
		while($level4_id == $rs->fields["level4_id"] and !$rs->EOF) {
			$subtotal += $rs->fields["sm_debet"] - $rs->fields["sm_kredit"];
			$rs->MoveNext();
		}
		?>
		<tr><td>&nbsp;</td><td style="padding: 5px;"><?php echo $nama_akun;?></td><td align="right"><?php echo number_format($subtotal);?></td><td>&nbsp;</td></tr>
		<?php
		$aktiva += $subtotal;
	}
	?>
	<tr><td colspan="3"><b>Total Aktiva</b></td><td align="right"><b><?php echo number_format($aktiva);?></b></td></tr>
	<?php
}
?>

<tr><td colspan="4">&nbsp;</td></tr>

<?php
// akun pasiva
$pasiva = 0;
$q = "select * from v_akun_2_sum_nrc where ";
if ($_POST["bulan"] != 0) {$q .= "month(tgl) = ".$_POST["bulan"]." and ";}
$q .= "year(tgl) = ".$_POST["tahun"];
$rs = Conn()->Execute($q);
if (!$rs->EOF) {
	$level1_nama = $rs->fields["level1_nama"];
	?>
	<tr><td colspan="4"><b><?php echo $rs->fields["level1_nama"];?></b></td></tr>
	<?php
	while (!$rs->EOF) {
		$level4_id = $rs->fields["level4_id"];
		$nama_akun = $rs->fields["nama_akun"];
		$subtotal = 0;
		while($level4_id == $rs->fields["level4_id"] and !$rs->EOF) {
			$subtotal += $rs->fields["sm_debet"] - $rs->fields["sm_kredit"];
			$rs->MoveNext();
		}
		?>
		<tr><td>&nbsp;</td><td style="padding: 5px;"><?php echo $nama_akun;?></td><td align="right"><?php echo number_format($subtotal);?></td><td>&nbsp;</td></tr>
		<?php
		$pasiva += $subtotal;
	}
	?>
	<tr><td colspan="3"><b>Total Pasiva</b></td><td align="right"><b><?php echo number_format($pasiva);?></b></td></tr>
	<?php
}
?>

<tr><td colspan="4">&nbsp;</td></tr>

<?php
// akun modal
$modal = 0;
$q = "select * from v_akun_3_sum_nrc where ";
if ($_POST["bulan"] != 0) {$q .= "month(tgl) = ".$_POST["bulan"]." and ";}
$q .= "year(tgl) = ".$_POST["tahun"];
$rs = Conn()->Execute($q);
if (!$rs->EOF) {
	$level1_nama = $rs->fields["level1_nama"];
	?>
	<tr><td colspan="4"><b><?php echo $rs->fields["level1_nama"];?></b></td></tr>
	<?php
	while (!$rs->EOF) {
		$level4_id = $rs->fields["level4_id"];
		$nama_akun = $rs->fields["nama_akun"];
		$subtotal = 0;
		while($level4_id == $rs->fields["level4_id"] and !$rs->EOF) {
			$subtotal += $rs->fields["sm_debet"] - $rs->fields["sm_kredit"];
			$rs->MoveNext();
		}
		?>
		<tr><td>&nbsp;</td><td style="padding: 5px;"><?php echo $nama_akun;?></td><td align="right"><?php echo number_format($subtotal);?></td><td>&nbsp;</td></tr>
		<?php
		$modal += $subtotal;
	}
	?>
	<tr><td colspan="3"><b>Total Pasiva</b></td><td align="right"><b><?php echo number_format($modal);?></b></td></tr>
	<?php
}
?>
</table>
<?php if (EW_DEBUG_ENABLED) echo ew_DebugMsg(); ?>
<?php include_once "footer.php" ?>
<?php
$r_neraca1_php->Page_Terminate();
?>
