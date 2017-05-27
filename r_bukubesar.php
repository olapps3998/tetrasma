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

$r_bukubesar_php = NULL; // Initialize page object first

class cr_bukubesar_php {

	// Page ID
	var $PageID = 'custom';

	// Project ID
	var $ProjectID = "{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}";

	// Table name
	var $TableName = 'r_bukubesar.php';

	// Page object name
	var $PageObjName = 'r_bukubesar_php';

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
			define("EW_TABLE_NAME", 'r_bukubesar.php', TRUE);

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
		$Breadcrumb->Add("custom", "r_bukubesar_php", $url, "", "r_bukubesar_php", TRUE);
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($r_bukubesar_php)) $r_bukubesar_php = new cr_bukubesar_php();

// Page init
$r_bukubesar_php->Page_Init();

// Page main
$r_bukubesar_php->Page_Main();

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
<?php
$conn =& DbHelper();

$q = "select saldo_awal from t_level4 where level4_id = '".$_GET["akun_id"]."'";
$rs = $conn->Execute($q);

$saldo_awal = ($rs && $rs->RecordCount() > 0 && $rs->fields["saldo_awal"] != null ? $rs->fields["saldo_awal"] : 0);
$saldo = $saldo_awal;

$q = "select * from v_kasbank_memorial where akun_id = '".$_GET["akun_id"]."'";
$rs = $conn->Execute($q);
?>
<table class="table ewTable">
	<tr class="ewTableHeader">
		<th>Tanggal</th>
		<th>Keterangan</th>
		<th>Debet</th>
		<th>Kredit</th>
		<th>Saldo</th>
	</tr>
	<tr class="ewTableRow">
		<td>&nbsp;</td>
		<td>Saldo Awal</td>
		<td><?php echo $saldo_awal;?></td>
		<td>&nbsp;</td>
		<td><?php echo $saldo;?></td>
	</tr><?php $baris = 1;?>
<?php
while (!$rs->EOF) {
	?>
	<tr class=<?php ($baris % 2 == 0 ? "ewTableRow" : "ewTableAltRow"); ?>>
		<td><?php echo $rs->fields["tgl"];?></td>
		<td><?php echo $rs->fields["ket"];?></td>
		<td><?php echo $rs->fields["debet"];?></td>
		<td><?php echo $rs->fields["kredit"];?></td>
		<?php $saldo += $rs->fields["debet"] - $rs->fields["kredit"];?>
		<td><?php echo $saldo;?></td>
	</tr><?php $baris++;?>
	<?php
	$rs->MoveNext();
}
?>
</table>

<table id="tbl_t_anggotalist" class="table ewTable">
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<th class="ewListOptionHeader" data-name="checkbox">
	<span id="elh_t_anggota_checkbox" class="t_anggota_checkbox">
		<input type="checkbox" name="key" id="key" onclick="ew_SelectAllKey(this);">
	</span>
</th>
<th class="ewListOptionHeader" data-name="button">&nbsp;
</th>
<th class="ewListOptionHeader" style="white-space: nowrap;" data-name="sequence">
	<span id="elh_t_anggota_sequence" class="t_anggota_sequence">&nbsp;
	</span>
</th>			
<th data-name="no_anggota"><div class="ewPointer" onclick="ew_Sort(event,'t_anggotalist.php?order=no_anggota&amp;ordertype=ASC',2);"><div id="elh_t_anggota_no_anggota" class="t_anggota_no_anggota">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption">No. Anggota</span><span class="ewTableHeaderSort"></span></div>
		</div></div>
</th>
			
			<th data-name="nama"><div class="ewPointer" onclick="ew_Sort(event,'t_anggotalist.php?order=nama&amp;ordertype=ASC',2);"><div id="elh_t_anggota_nama" class="t_anggota_nama">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption">Nama</span><span class="ewTableHeaderSort"></span></div>
		</div></div></th>
			
			<th data-name="tgl_masuk"><div class="ewPointer" onclick="ew_Sort(event,'t_anggotalist.php?order=tgl_masuk&amp;ordertype=ASC',2);"><div id="elh_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption">Tgl. Masuk</span><span class="ewTableHeaderSort"></span></div>
		</div></div></th>
			
			<th data-name="alamat"><div class="ewPointer" onclick="ew_Sort(event,'t_anggotalist.php?order=alamat&amp;ordertype=ASC',2);"><div id="elh_t_anggota_alamat" class="t_anggota_alamat">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption">Alamat</span><span class="ewTableHeaderSort"></span></div>
		</div></div></th>
			
			<th data-name="kota"><div class="ewPointer" onclick="ew_Sort(event,'t_anggotalist.php?order=kota&amp;ordertype=ASC',2);"><div id="elh_t_anggota_kota" class="t_anggota_kota">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption">Kota</span><span class="ewTableHeaderSort"></span></div>
		</div></div></th>
			
			<th data-name="no_telp"><div class="ewPointer" onclick="ew_Sort(event,'t_anggotalist.php?order=no_telp&amp;ordertype=ASC',2);"><div id="elh_t_anggota_no_telp" class="t_anggota_no_telp">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption">No. Telepon / No. HP</span><span class="ewTableHeaderSort"></span></div>
		</div></div></th>
			
			<th data-name="pekerjaan"><div class="ewPointer" onclick="ew_Sort(event,'t_anggotalist.php?order=pekerjaan&amp;ordertype=ASC',2);"><div id="elh_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption">Pekerjaan</span><span class="ewTableHeaderSort"></span></div>
		</div></div></th>
			
			<th data-name="jns_pengenal"><div class="ewPointer" onclick="ew_Sort(event,'t_anggotalist.php?order=jns_pengenal&amp;ordertype=ASC',2);"><div id="elh_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption">Jenis Pengenal</span><span class="ewTableHeaderSort"></span></div>
		</div></div></th>
			
			<th data-name="no_pengenal" class="ewTableLastCol"><div class="ewPointer" onclick="ew_Sort(event,'t_anggotalist.php?order=no_pengenal&amp;ordertype=ASC',2);"><div id="elh_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption">No. Pengenal</span><span class="ewTableHeaderSort"></span></div>
		</div></div></th>
			
	</tr>
</thead>
<tbody>
	<tr data-rowindex="1" id="r1_t_anggota" data-rowtype="1" class="ewTableRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el1_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="4" onclick="ew_ClickMultiCheckbox(event);"></span></td>
<td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=4" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=4" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=4" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el1_t_anggota_sequence" class="t_anggota_sequence">1.</span></td>			
<td data-name="no_anggota">
<span id="el1_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
1.01.10.2009</span>
</span>
<a id="t_anggota_list_row_1"></a></td>
				<td data-name="nama">
<span id="el1_t_anggota_nama" class="t_anggota_nama">
<span>
Sigit Warsito</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el1_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el1_t_anggota_alamat" class="t_anggota_alamat">
<span>
jl.Gunung Sari Indah YY-6 Sby</span>
</span>
</td>
				<td data-name="kota">
<span id="el1_t_anggota_kota" class="t_anggota_kota">
<span>
Surabaya</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el1_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O81553791188</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el1_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
IPT REA KALTIM PLANTATION</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el1_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el1_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="2" id="r2_t_anggota" data-rowtype="1" class="ewTableAltRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el2_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="5" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=5" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=5" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=5" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el2_t_anggota_sequence" class="t_anggota_sequence">2.</span></td>			<td data-name="no_anggota">
<span id="el2_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
2.01.10.2009</span>
</span>
<a id="t_anggota_list_row_2"></a></td>
				<td data-name="nama">
<span id="el2_t_anggota_nama" class="t_anggota_nama">
<span>
Resmi Setyaningsih</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el2_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el2_t_anggota_alamat" class="t_anggota_alamat">
<span>
JL.Griya Pesona Asri Blok C-50 Sby</span>
</span>
</td>
				<td data-name="kota">
<span id="el2_t_anggota_kota" class="t_anggota_kota">
<span>
Surabaya</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el2_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O81331501861</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el2_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
Guru SMA 4, Sby</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el2_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el2_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="3" id="r3_t_anggota" data-rowtype="1" class="ewTableRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el3_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="6" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=6" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=6" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=6" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el3_t_anggota_sequence" class="t_anggota_sequence">3.</span></td>			<td data-name="no_anggota">
<span id="el3_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
3.03.10.2009</span>
</span>
<a id="t_anggota_list_row_3"></a></td>
				<td data-name="nama">
<span id="el3_t_anggota_nama" class="t_anggota_nama">
<span>
Wenny Williarso</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el3_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el3_t_anggota_alamat" class="t_anggota_alamat">
<span>
Puri Taman Asri C-14 Sby</span>
</span>
</td>
				<td data-name="kota">
<span id="el3_t_anggota_kota" class="t_anggota_kota">
<span>
Surabaya</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el3_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O318275800</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el3_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
Bank Indonesia</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el3_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el3_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="4" id="r4_t_anggota" data-rowtype="1" class="ewTableAltRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el4_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="7" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=7" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=7" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=7" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el4_t_anggota_sequence" class="t_anggota_sequence">4.</span></td>			<td data-name="no_anggota">
<span id="el4_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
4.02.10.2009</span>
</span>
<a id="t_anggota_list_row_4"></a></td>
				<td data-name="nama">
<span id="el4_t_anggota_nama" class="t_anggota_nama">
<span>
Ira Wati</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el4_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el4_t_anggota_alamat" class="t_anggota_alamat">
<span>
Puri Taman Asri C-14 Sby</span>
</span>
</td>
				<td data-name="kota">
<span id="el4_t_anggota_kota" class="t_anggota_kota">
<span>
Surabaya</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el4_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O318275800</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el4_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
IRT</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el4_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el4_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="5" id="r5_t_anggota" data-rowtype="1" class="ewTableRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el5_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="8" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=8" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=8" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=8" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el5_t_anggota_sequence" class="t_anggota_sequence">5.</span></td>			<td data-name="no_anggota">
<span id="el5_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
5.03.11.2009</span>
</span>
<a id="t_anggota_list_row_5"></a></td>
				<td data-name="nama">
<span id="el5_t_anggota_nama" class="t_anggota_nama">
<span>
Agus Laswiyana</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el5_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el5_t_anggota_alamat" class="t_anggota_alamat">
<span>
Perum Permata Pekayon Bolk i-10 bekasi</span>
</span>
</td>
				<td data-name="kota">
<span id="el5_t_anggota_kota" class="t_anggota_kota">
<span>
Jakarta</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el5_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O8131995411</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el5_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
Wiraswasta</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el5_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el5_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="6" id="r6_t_anggota" data-rowtype="1" class="ewTableAltRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el6_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="9" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=9" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=9" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=9" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el6_t_anggota_sequence" class="t_anggota_sequence">6.</span></td>			<td data-name="no_anggota">
<span id="el6_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
6.03.10.2009</span>
</span>
<a id="t_anggota_list_row_6"></a></td>
				<td data-name="nama">
<span id="el6_t_anggota_nama" class="t_anggota_nama">
<span>
Gunawan Wibisono</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el6_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el6_t_anggota_alamat" class="t_anggota_alamat">
<span>
x</span>
</span>
</td>
				<td data-name="kota">
<span id="el6_t_anggota_kota" class="t_anggota_kota">
<span>
Jakarta</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el6_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
X</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el6_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
X</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el6_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el6_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="7" id="r7_t_anggota" data-rowtype="1" class="ewTableRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el7_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="10" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=10" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=10" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=10" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el7_t_anggota_sequence" class="t_anggota_sequence">7.</span></td>			<td data-name="no_anggota">
<span id="el7_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
7.09.10.2009</span>
</span>
<a id="t_anggota_list_row_7"></a></td>
				<td data-name="nama">
<span id="el7_t_anggota_nama" class="t_anggota_nama">
<span>
Endang Rahayu</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el7_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el7_t_anggota_alamat" class="t_anggota_alamat">
<span>
Jl Tanjung Pura RT 28 No 2 Balikpapan</span>
</span>
</td>
				<td data-name="kota">
<span id="el7_t_anggota_kota" class="t_anggota_kota">
<span>
Balikpapan</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el7_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O811530517</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el7_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
PT.Chevron</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el7_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el7_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="8" id="r8_t_anggota" data-rowtype="1" class="ewTableAltRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el8_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="11" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=11" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=11" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=11" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el8_t_anggota_sequence" class="t_anggota_sequence">8.</span></td>			<td data-name="no_anggota">
<span id="el8_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
8.03.10.2009</span>
</span>
<a id="t_anggota_list_row_8"></a></td>
				<td data-name="nama">
<span id="el8_t_anggota_nama" class="t_anggota_nama">
<span>
Edi Suratman</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el8_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el8_t_anggota_alamat" class="t_anggota_alamat">
<span>
x</span>
</span>
</td>
				<td data-name="kota">
<span id="el8_t_anggota_kota" class="t_anggota_kota">
<span>
Jakarta</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el8_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
X</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el8_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
X</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el8_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el8_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="9" id="r9_t_anggota" data-rowtype="1" class="ewTableRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el9_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="12" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=12" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=12" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=12" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el9_t_anggota_sequence" class="t_anggota_sequence">9.</span></td>			<td data-name="no_anggota">
<span id="el9_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
9.01.10.2009</span>
</span>
<a id="t_anggota_list_row_9"></a></td>
				<td data-name="nama">
<span id="el9_t_anggota_nama" class="t_anggota_nama">
<span>
Wiken Sukesi</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el9_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el9_t_anggota_alamat" class="t_anggota_alamat">
<span>
Jl Pandugo Timur 8/18</span>
</span>
</td>
				<td data-name="kota">
<span id="el9_t_anggota_kota" class="t_anggota_kota">
<span>
Surabaya</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el9_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O318705211</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el9_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
Swasta</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el9_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el9_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="10" id="r10_t_anggota" data-rowtype="1" class="ewTableAltRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el10_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="13" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=13" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=13" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=13" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el10_t_anggota_sequence" class="t_anggota_sequence">10.</span></td>			<td data-name="no_anggota">
<span id="el10_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
10.03.10.2009</span>
</span>
<a id="t_anggota_list_row_10"></a></td>
				<td data-name="nama">
<span id="el10_t_anggota_nama" class="t_anggota_nama">
<span>
Ibnu Perabowo</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el10_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el10_t_anggota_alamat" class="t_anggota_alamat">
<span>
x</span>
</span>
</td>
				<td data-name="kota">
<span id="el10_t_anggota_kota" class="t_anggota_kota">
<span>
Jakarta</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el10_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
X</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el10_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
X</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el10_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el10_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="11" id="r11_t_anggota" data-rowtype="1" class="ewTableRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el11_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="14" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=14" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=14" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=14" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el11_t_anggota_sequence" class="t_anggota_sequence">11.</span></td>			<td data-name="no_anggota">
<span id="el11_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
11.01.10.2009</span>
</span>
<a id="t_anggota_list_row_11"></a></td>
				<td data-name="nama">
<span id="el11_t_anggota_nama" class="t_anggota_nama">
<span>
Setyari Pangastuti</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el11_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el11_t_anggota_alamat" class="t_anggota_alamat">
<span>
Jl. Kutisari Indah Selatan I/135</span>
</span>
</td>
				<td data-name="kota">
<span id="el11_t_anggota_kota" class="t_anggota_kota">
<span>
Sidoarjo</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el11_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O318435247</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el11_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
PNS PT Telkom</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el11_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el11_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="12" id="r12_t_anggota" data-rowtype="1" class="ewTableAltRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el12_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="15" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=15" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=15" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=15" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el12_t_anggota_sequence" class="t_anggota_sequence">12.</span></td>			<td data-name="no_anggota">
<span id="el12_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
12.02.10.2009</span>
</span>
<a id="t_anggota_list_row_12"></a></td>
				<td data-name="nama">
<span id="el12_t_anggota_nama" class="t_anggota_nama">
<span>
Didik Widianto</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el12_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el12_t_anggota_alamat" class="t_anggota_alamat">
<span>
Jl Kawi 9 Pepelegi Indah</span>
</span>
</td>
				<td data-name="kota">
<span id="el12_t_anggota_kota" class="t_anggota_kota">
<span>
Surabaya</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el12_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O318537791</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el12_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
PT PAL Indonesia</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el12_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el12_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="13" id="r13_t_anggota" data-rowtype="1" class="ewTableRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el13_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="16" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=16" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=16" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=16" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el13_t_anggota_sequence" class="t_anggota_sequence">13.</span></td>			<td data-name="no_anggota">
<span id="el13_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
13.03.10.2009</span>
</span>
<a id="t_anggota_list_row_13"></a></td>
				<td data-name="nama">
<span id="el13_t_anggota_nama" class="t_anggota_nama">
<span>
Husain</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el13_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el13_t_anggota_alamat" class="t_anggota_alamat">
<span>
Komp. DepKop Jl. Gas Alam Blok B/4 Cimanggis Depok</span>
</span>
</td>
				<td data-name="kota">
<span id="el13_t_anggota_kota" class="t_anggota_kota">
<span>
Jakarta</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el13_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O218732559</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el13_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
Kementrian Negkop</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el13_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el13_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="14" id="r14_t_anggota" data-rowtype="1" class="ewTableAltRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el14_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="17" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=17" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=17" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=17" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el14_t_anggota_sequence" class="t_anggota_sequence">14.</span></td>			<td data-name="no_anggota">
<span id="el14_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
14.03.10.2009</span>
</span>
<a id="t_anggota_list_row_14"></a></td>
				<td data-name="nama">
<span id="el14_t_anggota_nama" class="t_anggota_nama">
<span>
Didi Hasan Putra</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el14_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el14_t_anggota_alamat" class="t_anggota_alamat">
<span>
Jl Ciater III/12 Puri Cinere Depok</span>
</span>
</td>
				<td data-name="kota">
<span id="el14_t_anggota_kota" class="t_anggota_kota">
<span>
Jakarta</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el14_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O217543941</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el14_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
Perus Jawa Bali</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el14_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el14_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="15" id="r15_t_anggota" data-rowtype="1" class="ewTableRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el15_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="18" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=18" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=18" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=18" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el15_t_anggota_sequence" class="t_anggota_sequence">15.</span></td>			<td data-name="no_anggota">
<span id="el15_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
15.03.10.2009</span>
</span>
<a id="t_anggota_list_row_15"></a></td>
				<td data-name="nama">
<span id="el15_t_anggota_nama" class="t_anggota_nama">
<span>
Ari Priyo Widagdo</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el15_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el15_t_anggota_alamat" class="t_anggota_alamat">
<span>
Kav Ardhy Karya No 8 RT 5/2 Rangkapan Jaya Baru-Pa</span>
</span>
</td>
				<td data-name="kota">
<span id="el15_t_anggota_kota" class="t_anggota_kota">
<span>
Jakarta</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el15_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O811412060</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el15_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
PT Adhi Karya (Persero) T</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el15_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el15_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="16" id="r16_t_anggota" data-rowtype="1" class="ewTableAltRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el16_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="19" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=19" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=19" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=19" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el16_t_anggota_sequence" class="t_anggota_sequence">16.</span></td>			<td data-name="no_anggota">
<span id="el16_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
16.03.10.2009</span>
</span>
<a id="t_anggota_list_row_16"></a></td>
				<td data-name="nama">
<span id="el16_t_anggota_nama" class="t_anggota_nama">
<span>
Miko</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el16_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el16_t_anggota_alamat" class="t_anggota_alamat">
<span>
Jl Pangadegan Utara 32Cikoko Paneoran, Jakarta</span>
</span>
</td>
				<td data-name="kota">
<span id="el16_t_anggota_kota" class="t_anggota_kota">
<span>
Jakarta</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el16_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
X</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el16_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
X</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el16_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el16_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="17" id="r17_t_anggota" data-rowtype="1" class="ewTableRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el17_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="20" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=20" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=20" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=20" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el17_t_anggota_sequence" class="t_anggota_sequence">17.</span></td>			<td data-name="no_anggota">
<span id="el17_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
17.03.10.2009</span>
</span>
<a id="t_anggota_list_row_17"></a></td>
				<td data-name="nama">
<span id="el17_t_anggota_nama" class="t_anggota_nama">
<span>
Very</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el17_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el17_t_anggota_alamat" class="t_anggota_alamat">
<span>
x</span>
</span>
</td>
				<td data-name="kota">
<span id="el17_t_anggota_kota" class="t_anggota_kota">
<span>
Jakarta</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el17_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
X</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el17_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
X</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el17_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el17_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="18" id="r18_t_anggota" data-rowtype="1" class="ewTableAltRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el18_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="21" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=21" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=21" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=21" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el18_t_anggota_sequence" class="t_anggota_sequence">18.</span></td>			<td data-name="no_anggota">
<span id="el18_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
18.06.10.2009</span>
</span>
<a id="t_anggota_list_row_18"></a></td>
				<td data-name="nama">
<span id="el18_t_anggota_nama" class="t_anggota_nama">
<span>
Udi Triastoto</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el18_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el18_t_anggota_alamat" class="t_anggota_alamat">
<span>
Kom Mekar Baru F1 Jl Jabaru II Ciomas Bogor</span>
</span>
</td>
				<td data-name="kota">
<span id="el18_t_anggota_kota" class="t_anggota_kota">
<span>
Bogor</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el18_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O8881725162</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el18_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
Pusdiklat Kehutanan</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el18_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el18_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="19" id="r19_t_anggota" data-rowtype="1" class="ewTableRow">
<td class="ewListOptionBody" data-name="checkbox"><span id="el19_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="22" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=22" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=22" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=22" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody" style="white-space: nowrap;" data-name="sequence"><span id="el19_t_anggota_sequence" class="t_anggota_sequence">19.</span></td>			<td data-name="no_anggota">
<span id="el19_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
19.03.10.2009</span>
</span>
<a id="t_anggota_list_row_19"></a></td>
				<td data-name="nama">
<span id="el19_t_anggota_nama" class="t_anggota_nama">
<span>
Niken</span>
</span>
</td>
				<td data-name="tgl_masuk">
<span id="el19_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat">
<span id="el19_t_anggota_alamat" class="t_anggota_alamat">
<span>
Barata Tama II/152 Karang Tengah Tangerang</span>
</span>
</td>
				<td data-name="kota">
<span id="el19_t_anggota_kota" class="t_anggota_kota">
<span>
Jakarta</span>
</span>
</td>
				<td data-name="no_telp">
<span id="el19_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O8129554240</span>
</span>
</td>
				<td data-name="pekerjaan">
<span id="el19_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
X</span>
</span>
</td>
				<td data-name="jns_pengenal">
<span id="el19_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol">
<span id="el19_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
	<tr data-rowindex="20" id="r20_t_anggota" data-rowtype="1" class="ewTableAltRow">
<td class="ewListOptionBody ewTableLastRow" data-name="checkbox"><span id="el20_t_anggota_checkbox" class="t_anggota_checkbox"><input type="checkbox" name="key_m[]" value="23" onclick="ew_ClickMultiCheckbox(event);"></span></td><td class="ewListOptionBody ewTableLastRow" data-name="button"><div class="btn-group ewButtonDropdown"><button class="dropdown-toggle btn btn-default btn-sm" title="" data-toggle="dropdown" data-original-title="Options"><span data-phrase="ButtonListOptions" class="icon-options ewIcon" data-caption="Options"></span><b class="caret"></b></button><ul class="dropdown-menu ewMenu"><li><a class="ewRowLink ewView" data-caption="View" href="t_anggotaview.php?showdetail=&amp;anggota_id=23" data-original-title="" title=""><span data-phrase="ViewLink" class="icon-view ewIcon" data-caption="View"></span>&nbsp;&nbsp;View</a></li><li><a class="ewRowLink ewEdit" data-caption="Edit" href="t_anggotaedit.php?anggota_id=23" data-original-title="" title=""><span data-phrase="EditLink" class="icon-edit ewIcon" data-caption="Edit"></span>&nbsp;&nbsp;Edit</a></li><li><a class="ewRowLink ewCopy" data-caption="Copy" href="t_anggotaadd.php?anggota_id=23" data-original-title="" title=""><span data-phrase="CopyLink" class="icon-copy ewIcon" data-caption="Copy"></span>&nbsp;&nbsp;Copy</a></li></ul></div></td><td class="ewListOptionBody ewTableLastRow" style="white-space: nowrap;" data-name="sequence"><span id="el20_t_anggota_sequence" class="t_anggota_sequence">20.</span></td>			<td data-name="no_anggota" class="ewTableLastRow">
<span id="el20_t_anggota_no_anggota" class="t_anggota_no_anggota">
<span>
20.03.10.2009</span>
</span>
<a id="t_anggota_list_row_20"></a></td>
				<td data-name="nama" class="ewTableLastRow">
<span id="el20_t_anggota_nama" class="t_anggota_nama">
<span>
Ani Herawati</span>
</span>
</td>
				<td data-name="tgl_masuk" class="ewTableLastRow">
<span id="el20_t_anggota_tgl_masuk" class="t_anggota_tgl_masuk">
<span>
0000-00-00</span>
</span>
</td>
				<td data-name="alamat" class="ewTableLastRow">
<span id="el20_t_anggota_alamat" class="t_anggota_alamat">
<span>
Jl Anggrek Garuda Blok E19 Slipi Jakarta Barat</span>
</span>
</td>
				<td data-name="kota" class="ewTableLastRow">
<span id="el20_t_anggota_kota" class="t_anggota_kota">
<span>
Jakarta</span>
</span>
</td>
				<td data-name="no_telp" class="ewTableLastRow">
<span id="el20_t_anggota_no_telp" class="t_anggota_no_telp">
<span>
O816816247</span>
</span>
</td>
				<td data-name="pekerjaan" class="ewTableLastRow">
<span id="el20_t_anggota_pekerjaan" class="t_anggota_pekerjaan">
<span>
X</span>
</span>
</td>
				<td data-name="jns_pengenal" class="ewTableLastRow">
<span id="el20_t_anggota_jns_pengenal" class="t_anggota_jns_pengenal">
<span>
KTP</span>
</span>
</td>
				<td data-name="no_pengenal" class="ewTableLastCol ewTableLastRow">
<span id="el20_t_anggota_no_pengenal" class="t_anggota_no_pengenal">
<span>
x</span>
</span>
</td>
		</tr>
</tbody>
</table>
<?php if (EW_DEBUG_ENABLED) echo ew_DebugMsg(); ?>
<?php include_once "footer.php" ?>
<?php
$r_bukubesar_php->Page_Terminate();
?>
