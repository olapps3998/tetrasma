<?php
if (session_id() == "") session_start(); // Init session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg13.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql13.php") ?>
<?php include_once "phpfn13.php" ?>
<?php include_once "t_level4info.php" ?>
<?php include_once "t_userinfo.php" ?>
<?php include_once "userfn13.php" ?>
<?php

//
// Page class
//

$t_level4_delete = NULL; // Initialize page object first

class ct_level4_delete extends ct_level4 {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}";

	// Table name
	var $TableName = 't_level4';

	// Page object name
	var $PageObjName = 't_level4_delete';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
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
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
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

		// Parent constuctor
		parent::__construct();

		// Table object (t_level4)
		if (!isset($GLOBALS["t_level4"]) || get_class($GLOBALS["t_level4"]) == "ct_level4") {
			$GLOBALS["t_level4"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["t_level4"];
		}

		// Table object (t_user)
		if (!isset($GLOBALS['t_user'])) $GLOBALS['t_user'] = new ct_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 't_level4', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);

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
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("t_level4list.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}
		if ($Security->IsLoggedIn()) {
			$Security->UserID_Loading();
			$Security->LoadUserID();
			$Security->UserID_Loaded();
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->level1_id->SetVisibility();
		$this->level2_id->SetVisibility();
		$this->level3_id->SetVisibility();
		$this->level4_no->SetVisibility();
		$this->level4_nama->SetVisibility();
		$this->sa_debet->SetVisibility();
		$this->sa_kredit->SetVisibility();
		$this->jurnal->SetVisibility();
		$this->jurnal_kode->SetVisibility();
		$this->neraca->SetVisibility();
		$this->labarugi->SetVisibility();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

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

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $t_level4;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($t_level4);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("t_level4list.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in t_level4 class, t_level4info.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} elseif (@$_GET["a_delete"] == "1") {
			$this->CurrentAction = "D"; // Delete record directly
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		if ($this->CurrentAction == "D") {
			$this->SendEmail = TRUE; // Send email on delete success
			if ($this->DeleteRows()) { // Delete rows
				if ($this->getSuccessMessage() == "")
					$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
				$this->Page_Terminate($this->getReturnUrl()); // Return to caller
			} else { // Delete failed
				$this->CurrentAction = "I"; // Display record
			}
		}
		if ($this->CurrentAction == "I") { // Load records for display
			if ($this->Recordset = $this->LoadRecordset())
				$this->TotalRecs = $this->Recordset->RecordCount(); // Get record count
			if ($this->TotalRecs <= 0) { // No record found, exit
				if ($this->Recordset)
					$this->Recordset->Close();
				$this->Page_Terminate("t_level4list.php"); // Return to list
			}
		}
	}

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {

		// Load List page SQL
		$sSql = $this->SelectSQL();
		$conn = &$this->Connection();

		// Load recordset
		$dbtype = ew_GetConnectionType($this->DBID);
		if ($this->UseSelectLimit) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			if ($dbtype == "MSSQL") {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset, array("_hasOrderBy" => trim($this->getOrderBy()) || trim($this->getSessionOrderByList())));
			} else {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset);
			}
			$conn->raiseErrorFn = '';
		} else {
			$rs = ew_LoadRecordset($sSql, $conn);
		}

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql, $conn);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->level4_id->setDbValue($rs->fields('level4_id'));
		$this->level1_id->setDbValue($rs->fields('level1_id'));
		if (array_key_exists('EV__level1_id', $rs->fields)) {
			$this->level1_id->VirtualValue = $rs->fields('EV__level1_id'); // Set up virtual field value
		} else {
			$this->level1_id->VirtualValue = ""; // Clear value
		}
		$this->level2_id->setDbValue($rs->fields('level2_id'));
		if (array_key_exists('EV__level2_id', $rs->fields)) {
			$this->level2_id->VirtualValue = $rs->fields('EV__level2_id'); // Set up virtual field value
		} else {
			$this->level2_id->VirtualValue = ""; // Clear value
		}
		$this->level3_id->setDbValue($rs->fields('level3_id'));
		if (array_key_exists('EV__level3_id', $rs->fields)) {
			$this->level3_id->VirtualValue = $rs->fields('EV__level3_id'); // Set up virtual field value
		} else {
			$this->level3_id->VirtualValue = ""; // Clear value
		}
		$this->level4_no->setDbValue($rs->fields('level4_no'));
		$this->level4_nama->setDbValue($rs->fields('level4_nama'));
		$this->sa_debet->setDbValue($rs->fields('sa_debet'));
		$this->sa_kredit->setDbValue($rs->fields('sa_kredit'));
		$this->jurnal->setDbValue($rs->fields('jurnal'));
		$this->jurnal_kode->setDbValue($rs->fields('jurnal_kode'));
		$this->sm_debet->setDbValue($rs->fields('sm_debet'));
		$this->sm_kredit->setDbValue($rs->fields('sm_kredit'));
		$this->neraca->setDbValue($rs->fields('neraca'));
		$this->labarugi->setDbValue($rs->fields('labarugi'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->level4_id->DbValue = $row['level4_id'];
		$this->level1_id->DbValue = $row['level1_id'];
		$this->level2_id->DbValue = $row['level2_id'];
		$this->level3_id->DbValue = $row['level3_id'];
		$this->level4_no->DbValue = $row['level4_no'];
		$this->level4_nama->DbValue = $row['level4_nama'];
		$this->sa_debet->DbValue = $row['sa_debet'];
		$this->sa_kredit->DbValue = $row['sa_kredit'];
		$this->jurnal->DbValue = $row['jurnal'];
		$this->jurnal_kode->DbValue = $row['jurnal_kode'];
		$this->sm_debet->DbValue = $row['sm_debet'];
		$this->sm_kredit->DbValue = $row['sm_kredit'];
		$this->neraca->DbValue = $row['neraca'];
		$this->labarugi->DbValue = $row['labarugi'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Convert decimal values if posted back

		if ($this->sa_debet->FormValue == $this->sa_debet->CurrentValue && is_numeric(ew_StrToFloat($this->sa_debet->CurrentValue)))
			$this->sa_debet->CurrentValue = ew_StrToFloat($this->sa_debet->CurrentValue);

		// Convert decimal values if posted back
		if ($this->sa_kredit->FormValue == $this->sa_kredit->CurrentValue && is_numeric(ew_StrToFloat($this->sa_kredit->CurrentValue)))
			$this->sa_kredit->CurrentValue = ew_StrToFloat($this->sa_kredit->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// level4_id
		// level1_id
		// level2_id
		// level3_id
		// level4_no
		// level4_nama
		// sa_debet
		// sa_kredit
		// jurnal
		// jurnal_kode
		// sm_debet

		$this->sm_debet->CellCssStyle = "white-space: nowrap;";

		// sm_kredit
		$this->sm_kredit->CellCssStyle = "white-space: nowrap;";

		// neraca
		// labarugi

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// level1_id
		if ($this->level1_id->VirtualValue <> "") {
			$this->level1_id->ViewValue = $this->level1_id->VirtualValue;
		} else {
			$this->level1_id->ViewValue = $this->level1_id->CurrentValue;
		if (strval($this->level1_id->CurrentValue) <> "") {
			$sFilterWrk = "`level1_id`" . ew_SearchString("=", $this->level1_id->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `level1_id`, `level1_no` AS `DispFld`, `level1_nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `t_level1`";
		$sWhereWrk = "";
		$this->level1_id->LookupFilters = array("dx1" => '`level1_no`', "dx2" => '`level1_nama`');
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->level1_id, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `level1_no` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->level1_id->ViewValue = $this->level1_id->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->level1_id->ViewValue = $this->level1_id->CurrentValue;
			}
		} else {
			$this->level1_id->ViewValue = NULL;
		}
		}
		$this->level1_id->ViewCustomAttributes = "";

		// level2_id
		if ($this->level2_id->VirtualValue <> "") {
			$this->level2_id->ViewValue = $this->level2_id->VirtualValue;
		} else {
			$this->level2_id->ViewValue = $this->level2_id->CurrentValue;
		if (strval($this->level2_id->CurrentValue) <> "") {
			$sFilterWrk = "`level2_id`" . ew_SearchString("=", $this->level2_id->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `level2_id`, `level2_no` AS `DispFld`, `level2_nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `t_level2`";
		$sWhereWrk = "";
		$this->level2_id->LookupFilters = array("dx1" => '`level2_no`', "dx2" => '`level2_nama`');
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->level2_id, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `level2_no` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->level2_id->ViewValue = $this->level2_id->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->level2_id->ViewValue = $this->level2_id->CurrentValue;
			}
		} else {
			$this->level2_id->ViewValue = NULL;
		}
		}
		$this->level2_id->ViewCustomAttributes = "";

		// level3_id
		if ($this->level3_id->VirtualValue <> "") {
			$this->level3_id->ViewValue = $this->level3_id->VirtualValue;
		} else {
			$this->level3_id->ViewValue = $this->level3_id->CurrentValue;
		if (strval($this->level3_id->CurrentValue) <> "") {
			$sFilterWrk = "`level3_id`" . ew_SearchString("=", $this->level3_id->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `level3_id`, `level3_no` AS `DispFld`, `level3_nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `t_level3`";
		$sWhereWrk = "";
		$this->level3_id->LookupFilters = array("dx1" => '`level3_no`', "dx2" => '`level3_nama`');
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->level3_id, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `level3_no` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->level3_id->ViewValue = $this->level3_id->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->level3_id->ViewValue = $this->level3_id->CurrentValue;
			}
		} else {
			$this->level3_id->ViewValue = NULL;
		}
		}
		$this->level3_id->ViewCustomAttributes = "";

		// level4_no
		$this->level4_no->ViewValue = $this->level4_no->CurrentValue;
		$this->level4_no->ViewCustomAttributes = "";

		// level4_nama
		$this->level4_nama->ViewValue = $this->level4_nama->CurrentValue;
		$this->level4_nama->ViewCustomAttributes = "";

		// sa_debet
		$this->sa_debet->ViewValue = $this->sa_debet->CurrentValue;
		$this->sa_debet->ViewValue = ew_FormatNumber($this->sa_debet->ViewValue, 0, -2, -2, -1);
		$this->sa_debet->CellCssStyle .= "text-align: right;";
		$this->sa_debet->ViewCustomAttributes = "";

		// sa_kredit
		$this->sa_kredit->ViewValue = $this->sa_kredit->CurrentValue;
		$this->sa_kredit->ViewValue = ew_FormatNumber($this->sa_kredit->ViewValue, 0, -2, -2, -2);
		$this->sa_kredit->CellCssStyle .= "text-align: right;";
		$this->sa_kredit->ViewCustomAttributes = "";

		// jurnal
		if (strval($this->jurnal->CurrentValue) <> "") {
			$this->jurnal->ViewValue = $this->jurnal->OptionCaption($this->jurnal->CurrentValue);
		} else {
			$this->jurnal->ViewValue = NULL;
		}
		$this->jurnal->ViewCustomAttributes = "";

		// jurnal_kode
		if (strval($this->jurnal_kode->CurrentValue) <> "") {
			$this->jurnal_kode->ViewValue = $this->jurnal_kode->OptionCaption($this->jurnal_kode->CurrentValue);
		} else {
			$this->jurnal_kode->ViewValue = NULL;
		}
		$this->jurnal_kode->ViewCustomAttributes = "";

		// neraca
		if (strval($this->neraca->CurrentValue) <> "") {
			$this->neraca->ViewValue = $this->neraca->OptionCaption($this->neraca->CurrentValue);
		} else {
			$this->neraca->ViewValue = NULL;
		}
		$this->neraca->ViewCustomAttributes = "";

		// labarugi
		if (strval($this->labarugi->CurrentValue) <> "") {
			$this->labarugi->ViewValue = $this->labarugi->OptionCaption($this->labarugi->CurrentValue);
		} else {
			$this->labarugi->ViewValue = NULL;
		}
		$this->labarugi->ViewCustomAttributes = "";

			// level1_id
			$this->level1_id->LinkCustomAttributes = "";
			$this->level1_id->HrefValue = "";
			$this->level1_id->TooltipValue = "";

			// level2_id
			$this->level2_id->LinkCustomAttributes = "";
			$this->level2_id->HrefValue = "";
			$this->level2_id->TooltipValue = "";

			// level3_id
			$this->level3_id->LinkCustomAttributes = "";
			$this->level3_id->HrefValue = "";
			$this->level3_id->TooltipValue = "";

			// level4_no
			$this->level4_no->LinkCustomAttributes = "";
			$this->level4_no->HrefValue = "";
			$this->level4_no->TooltipValue = "";

			// level4_nama
			$this->level4_nama->LinkCustomAttributes = "";
			$this->level4_nama->HrefValue = "";
			$this->level4_nama->TooltipValue = "";

			// sa_debet
			$this->sa_debet->LinkCustomAttributes = "";
			$this->sa_debet->HrefValue = "";
			$this->sa_debet->TooltipValue = "";

			// sa_kredit
			$this->sa_kredit->LinkCustomAttributes = "";
			$this->sa_kredit->HrefValue = "";
			$this->sa_kredit->TooltipValue = "";

			// jurnal
			$this->jurnal->LinkCustomAttributes = "";
			$this->jurnal->HrefValue = "";
			$this->jurnal->TooltipValue = "";

			// jurnal_kode
			$this->jurnal_kode->LinkCustomAttributes = "";
			$this->jurnal_kode->HrefValue = "";
			$this->jurnal_kode->TooltipValue = "";

			// neraca
			$this->neraca->LinkCustomAttributes = "";
			$this->neraca->HrefValue = "";
			$this->neraca->TooltipValue = "";

			// labarugi
			$this->labarugi->LinkCustomAttributes = "";
			$this->labarugi->HrefValue = "";
			$this->labarugi->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $Language, $Security;
		if (!$Security->CanDelete()) {
			$this->setFailureMessage($Language->Phrase("NoDeletePermission")); // No delete permission
			return FALSE;
		}
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$rows = ($rs) ? $rs->GetRows() : array();
		$conn->BeginTrans();
		if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteBegin")); // Batch delete begin

		// Clone old rows
		$rsold = $rows;
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['level4_id'];
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteSuccess")); // Batch delete success
		} else {
			$conn->RollbackTrans(); // Rollback changes
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteRollback")); // Batch delete rollback
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, $this->AddMasterUrl("t_level4list.php"), "", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, $url);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		}
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($t_level4_delete)) $t_level4_delete = new ct_level4_delete();

// Page init
$t_level4_delete->Page_Init();

// Page main
$t_level4_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$t_level4_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "delete";
var CurrentForm = ft_level4delete = new ew_Form("ft_level4delete", "delete");

// Form_CustomValidate event
ft_level4delete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ft_level4delete.ValidateRequired = true;
<?php } else { ?>
ft_level4delete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ft_level4delete.Lists["x_level1_id"] = {"LinkField":"x_level1_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_level1_no","x_level1_nama","",""],"ParentFields":[],"ChildFields":["x_level2_id"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"t_level1"};
ft_level4delete.Lists["x_level2_id"] = {"LinkField":"x_level2_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_level2_no","x_level2_nama","",""],"ParentFields":[],"ChildFields":["x_level3_id"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"t_level2"};
ft_level4delete.Lists["x_level3_id"] = {"LinkField":"x_level3_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_level3_no","x_level3_nama","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":"","LinkTable":"t_level3"};
ft_level4delete.Lists["x_jurnal"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
ft_level4delete.Lists["x_jurnal"].Options = <?php echo json_encode($t_level4->jurnal->Options()) ?>;
ft_level4delete.Lists["x_jurnal_kode"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
ft_level4delete.Lists["x_jurnal_kode"].Options = <?php echo json_encode($t_level4->jurnal_kode->Options()) ?>;
ft_level4delete.Lists["x_neraca"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
ft_level4delete.Lists["x_neraca"].Options = <?php echo json_encode($t_level4->neraca->Options()) ?>;
ft_level4delete.Lists["x_labarugi"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
ft_level4delete.Lists["x_labarugi"].Options = <?php echo json_encode($t_level4->labarugi->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $t_level4_delete->ShowPageHeader(); ?>
<?php
$t_level4_delete->ShowMessage();
?>
<form name="ft_level4delete" id="ft_level4delete" class="form-inline ewForm ewDeleteForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($t_level4_delete->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $t_level4_delete->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="t_level4">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($t_level4_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="ewGrid">
<div class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="table ewTable">
<?php echo $t_level4->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($t_level4->level1_id->Visible) { // level1_id ?>
		<th><span id="elh_t_level4_level1_id" class="t_level4_level1_id"><?php echo $t_level4->level1_id->FldCaption() ?></span></th>
<?php } ?>
<?php if ($t_level4->level2_id->Visible) { // level2_id ?>
		<th><span id="elh_t_level4_level2_id" class="t_level4_level2_id"><?php echo $t_level4->level2_id->FldCaption() ?></span></th>
<?php } ?>
<?php if ($t_level4->level3_id->Visible) { // level3_id ?>
		<th><span id="elh_t_level4_level3_id" class="t_level4_level3_id"><?php echo $t_level4->level3_id->FldCaption() ?></span></th>
<?php } ?>
<?php if ($t_level4->level4_no->Visible) { // level4_no ?>
		<th><span id="elh_t_level4_level4_no" class="t_level4_level4_no"><?php echo $t_level4->level4_no->FldCaption() ?></span></th>
<?php } ?>
<?php if ($t_level4->level4_nama->Visible) { // level4_nama ?>
		<th><span id="elh_t_level4_level4_nama" class="t_level4_level4_nama"><?php echo $t_level4->level4_nama->FldCaption() ?></span></th>
<?php } ?>
<?php if ($t_level4->sa_debet->Visible) { // sa_debet ?>
		<th><span id="elh_t_level4_sa_debet" class="t_level4_sa_debet"><?php echo $t_level4->sa_debet->FldCaption() ?></span></th>
<?php } ?>
<?php if ($t_level4->sa_kredit->Visible) { // sa_kredit ?>
		<th><span id="elh_t_level4_sa_kredit" class="t_level4_sa_kredit"><?php echo $t_level4->sa_kredit->FldCaption() ?></span></th>
<?php } ?>
<?php if ($t_level4->jurnal->Visible) { // jurnal ?>
		<th><span id="elh_t_level4_jurnal" class="t_level4_jurnal"><?php echo $t_level4->jurnal->FldCaption() ?></span></th>
<?php } ?>
<?php if ($t_level4->jurnal_kode->Visible) { // jurnal_kode ?>
		<th><span id="elh_t_level4_jurnal_kode" class="t_level4_jurnal_kode"><?php echo $t_level4->jurnal_kode->FldCaption() ?></span></th>
<?php } ?>
<?php if ($t_level4->neraca->Visible) { // neraca ?>
		<th><span id="elh_t_level4_neraca" class="t_level4_neraca"><?php echo $t_level4->neraca->FldCaption() ?></span></th>
<?php } ?>
<?php if ($t_level4->labarugi->Visible) { // labarugi ?>
		<th><span id="elh_t_level4_labarugi" class="t_level4_labarugi"><?php echo $t_level4->labarugi->FldCaption() ?></span></th>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$t_level4_delete->RecCnt = 0;
$i = 0;
while (!$t_level4_delete->Recordset->EOF) {
	$t_level4_delete->RecCnt++;
	$t_level4_delete->RowCnt++;

	// Set row properties
	$t_level4->ResetAttrs();
	$t_level4->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$t_level4_delete->LoadRowValues($t_level4_delete->Recordset);

	// Render row
	$t_level4_delete->RenderRow();
?>
	<tr<?php echo $t_level4->RowAttributes() ?>>
<?php if ($t_level4->level1_id->Visible) { // level1_id ?>
		<td<?php echo $t_level4->level1_id->CellAttributes() ?>>
<span id="el<?php echo $t_level4_delete->RowCnt ?>_t_level4_level1_id" class="t_level4_level1_id">
<span<?php echo $t_level4->level1_id->ViewAttributes() ?>>
<?php echo $t_level4->level1_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($t_level4->level2_id->Visible) { // level2_id ?>
		<td<?php echo $t_level4->level2_id->CellAttributes() ?>>
<span id="el<?php echo $t_level4_delete->RowCnt ?>_t_level4_level2_id" class="t_level4_level2_id">
<span<?php echo $t_level4->level2_id->ViewAttributes() ?>>
<?php echo $t_level4->level2_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($t_level4->level3_id->Visible) { // level3_id ?>
		<td<?php echo $t_level4->level3_id->CellAttributes() ?>>
<span id="el<?php echo $t_level4_delete->RowCnt ?>_t_level4_level3_id" class="t_level4_level3_id">
<span<?php echo $t_level4->level3_id->ViewAttributes() ?>>
<?php echo $t_level4->level3_id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($t_level4->level4_no->Visible) { // level4_no ?>
		<td<?php echo $t_level4->level4_no->CellAttributes() ?>>
<span id="el<?php echo $t_level4_delete->RowCnt ?>_t_level4_level4_no" class="t_level4_level4_no">
<span<?php echo $t_level4->level4_no->ViewAttributes() ?>>
<?php echo $t_level4->level4_no->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($t_level4->level4_nama->Visible) { // level4_nama ?>
		<td<?php echo $t_level4->level4_nama->CellAttributes() ?>>
<span id="el<?php echo $t_level4_delete->RowCnt ?>_t_level4_level4_nama" class="t_level4_level4_nama">
<span<?php echo $t_level4->level4_nama->ViewAttributes() ?>>
<?php echo $t_level4->level4_nama->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($t_level4->sa_debet->Visible) { // sa_debet ?>
		<td<?php echo $t_level4->sa_debet->CellAttributes() ?>>
<span id="el<?php echo $t_level4_delete->RowCnt ?>_t_level4_sa_debet" class="t_level4_sa_debet">
<span<?php echo $t_level4->sa_debet->ViewAttributes() ?>>
<?php echo $t_level4->sa_debet->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($t_level4->sa_kredit->Visible) { // sa_kredit ?>
		<td<?php echo $t_level4->sa_kredit->CellAttributes() ?>>
<span id="el<?php echo $t_level4_delete->RowCnt ?>_t_level4_sa_kredit" class="t_level4_sa_kredit">
<span<?php echo $t_level4->sa_kredit->ViewAttributes() ?>>
<?php echo $t_level4->sa_kredit->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($t_level4->jurnal->Visible) { // jurnal ?>
		<td<?php echo $t_level4->jurnal->CellAttributes() ?>>
<span id="el<?php echo $t_level4_delete->RowCnt ?>_t_level4_jurnal" class="t_level4_jurnal">
<span<?php echo $t_level4->jurnal->ViewAttributes() ?>>
<?php echo $t_level4->jurnal->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($t_level4->jurnal_kode->Visible) { // jurnal_kode ?>
		<td<?php echo $t_level4->jurnal_kode->CellAttributes() ?>>
<span id="el<?php echo $t_level4_delete->RowCnt ?>_t_level4_jurnal_kode" class="t_level4_jurnal_kode">
<span<?php echo $t_level4->jurnal_kode->ViewAttributes() ?>>
<?php echo $t_level4->jurnal_kode->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($t_level4->neraca->Visible) { // neraca ?>
		<td<?php echo $t_level4->neraca->CellAttributes() ?>>
<span id="el<?php echo $t_level4_delete->RowCnt ?>_t_level4_neraca" class="t_level4_neraca">
<span<?php echo $t_level4->neraca->ViewAttributes() ?>>
<?php echo $t_level4->neraca->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($t_level4->labarugi->Visible) { // labarugi ?>
		<td<?php echo $t_level4->labarugi->CellAttributes() ?>>
<span id="el<?php echo $t_level4_delete->RowCnt ?>_t_level4_labarugi" class="t_level4_labarugi">
<span<?php echo $t_level4->labarugi->ViewAttributes() ?>>
<?php echo $t_level4->labarugi->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$t_level4_delete->Recordset->MoveNext();
}
$t_level4_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</div>
<div>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $t_level4_delete->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</form>
<script type="text/javascript">
ft_level4delete.Init();
</script>
<?php
$t_level4_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$t_level4_delete->Page_Terminate();
?>
