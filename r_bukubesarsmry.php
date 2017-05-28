<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
?>
<?php include_once "phprptinc/ewrcfg10.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "phprptinc/ewmysql.php") ?>
<?php include_once "phprptinc/ewrfn10.php" ?>
<?php include_once "phprptinc/ewrusrfn10.php" ?>
<?php include_once "r_bukubesarsmryinfo.php" ?>
<?php

//
// Page class
//

$r_bukubesar_summary = NULL; // Initialize page object first

class crr_bukubesar_summary extends crr_bukubesar {

	// Page ID
	var $PageID = 'summary';

	// Project ID
	var $ProjectID = "{0947E56A-59DA-4545-A2FF-20A7F7239C7D}";

	// Page object name
	var $PageObjName = 'r_bukubesar_summary';

	// Page name
	function PageName() {
		return ewr_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ewr_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Export URLs
	var $ExportPrintUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportPdfUrl;
	var $ReportTableClass;
	var $ReportTableStyle = "";

	// Custom export
	var $ExportPrintCustom = TRUE;
	var $ExportExcelCustom = TRUE;
	var $ExportWordCustom = TRUE;
	var $ExportPdfCustom = TRUE;
	var $ExportEmailCustom = TRUE;

	// Message
	function getMessage() {
		return @$_SESSION[EWR_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EWR_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EWR_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EWR_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_WARNING_MESSAGE], $v);
	}

		// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EWR_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EWR_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EWR_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EWR_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog ewDisplayTable\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") // Header exists, display
			echo $sHeader;
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") // Fotoer exists, display
			echo $sFooter;
	}

	// Validate page request
	function IsPageRequest() {
		if ($this->UseTokenInUrl) {
			if (ewr_IsHttpPost())
				return ($this->TableVar == @$_POST("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == @$_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $CheckToken = EWR_CHECK_TOKEN;
	var $CheckTokenFn = "ewr_CheckToken";
	var $CreateTokenFn = "ewr_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ewr_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EWR_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EWR_TOKEN_NAME]);
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
		global $conn, $ReportLanguage;
		global $UserTable, $UserTableConn;

		// Language object
		$ReportLanguage = new crLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (r_bukubesar)
		if (!isset($GLOBALS["r_bukubesar"])) {
			$GLOBALS["r_bukubesar"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["r_bukubesar"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";

		// Page ID
		if (!defined("EWR_PAGE_ID"))
			define("EWR_PAGE_ID", 'summary', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EWR_TABLE_NAME"))
			define("EWR_TABLE_NAME", 'r_bukubesar', TRUE);

		// Start timer
		$GLOBALS["gsTimer"] = new crTimer();

		// Open connection
		if (!isset($conn)) $conn = ewr_Connect($this->DBID);

		// User table object (t_user)
		if (!isset($UserTable)) {
			$UserTable = new crt_user();
			$UserTableConn = ReportConn($UserTable->DBID);
		}

		// Export options
		$this->ExportOptions = new crListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Search options
		$this->SearchOptions = new crListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Filter options
		$this->FilterOptions = new crListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption fr_bukubesarsummary";

		// Generate report options
		$this->GenerateOptions = new crListOptions();
		$this->GenerateOptions->Tag = "div";
		$this->GenerateOptions->TagClassName = "ewGenerateOption";
	}

	//
	// Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $gsEmailContentType, $ReportLanguage, $Security;
		global $gsCustomExport;

		// Security
		$Security = new crAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin(); // Auto login
		$Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . 'r_bukubesar');
		$Security->TablePermission_Loaded();
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($ReportLanguage->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ewr_GetUrl("index.php"));
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		if ($Security->IsLoggedIn() && strval($Security->CurrentUserID()) == "") {
			$Security->SaveLastUrl();
			$this->setFailureMessage($ReportLanguage->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ewr_GetUrl("login.php"));
		}

		// Get export parameters
		if (@$_GET["export"] <> "")
			$this->Export = strtolower($_GET["export"]);
		elseif (@$_POST["export"] <> "")
			$this->Export = strtolower($_POST["export"]);

		// Get custom export parameters
		if ($this->Export <> "" && @$_GET["custom"] <> "") {
			$this->CustomExport = $this->Export;
			$this->Export = "print";
		}
		$gsCustomExport = $this->CustomExport;

		// Custom export (post back from ewr_ApplyTemplate), export and terminate page
		if (@$_POST["customexport"] <> "") {
			$this->CustomExport = $_POST["customexport"];
			$this->Export = $this->CustomExport;
			$this->Page_Terminate();
			exit();
		}
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header
		$gsEmailContentType = @$_POST["contenttype"]; // Get email content type

		// Setup placeholder
		$this->tgl->PlaceHolder = $this->tgl->FldCaption();

		// Setup export options
		$this->SetupExportOptions();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $ReportLanguage->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Create Token
		$this->CreateToken();
	}

	// Set up export options
	function SetupExportOptions() {
		global $Security, $ReportLanguage, $ReportOptions;
		$exportid = session_id();
		$ReportTypes = array();

		// Update Export URLs
		if ($this->ExportPrintCustom)
			$this->ExportPrintUrl .= "&amp;custom=1";

		//if (defined("EWR_USE_PHPEXCEL"))
		//	$this->ExportExcelCustom = FALSE;

		if ($this->ExportExcelCustom)
			$this->ExportExcelUrl .= "&amp;custom=1";

		//if (defined("EWR_USE_PHPWORD"))
		//	$this->ExportWordCustom = FALSE;

		if ($this->ExportWordCustom)
			$this->ExportWordUrl .= "&amp;custom=1";
		if ($this->ExportPdfCustom)
			$this->ExportPdfUrl .= "&amp;custom=1";

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		if ($this->ExportPrintCustom)
			$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" href=\"javascript:void(0);\" onclick=\"ewr_ExportCharts(this, '" . $this->ExportPrintUrl . "', '" . $exportid . "');\">" . $ReportLanguage->Phrase("PrinterFriendly") . "</a>";
		else
			$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly"), TRUE) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" href=\"" . $this->ExportPrintUrl . "\">" . $ReportLanguage->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;
		$ReportTypes["print"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormPrint") : "";

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		if ($this->ExportExcelCustom)
			$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" href=\"javascript:void(0);\" onclick=\"ewr_ExportCharts(this, '" . $this->ExportExcelUrl . "', '" . $exportid . "');\">" . $ReportLanguage->Phrase("ExportToExcel") . "</a>";
		else
			$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" href=\"" . $this->ExportExcelUrl . "\">" . $ReportLanguage->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;
		$ReportTypes["excel"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormExcel") : "";

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		if ($this->ExportWordCustom)
			$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" href=\"javascript:void(0);\" onclick=\"ewr_ExportCharts(this, '" . $this->ExportWordUrl . "', '" . $exportid . "');\">" . $ReportLanguage->Phrase("ExportToWord") . "</a>";
		else
			$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" href=\"" . $this->ExportWordUrl . "\">" . $ReportLanguage->Phrase("ExportToWord") . "</a>";

		//$item->Visible = TRUE;
		$item->Visible = TRUE;
		$ReportTypes["word"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormWord") : "";

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"" . $this->ExportPdfUrl . "\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Uncomment codes below to show export to Pdf link
//		$item->Visible = TRUE;

		$ReportTypes["pdf"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormPdf") : "";

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = $this->PageUrl() . "export=email";
		if ($this->ExportEmailCustom)
			$url .= "&amp;custom=1";
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_r_bukubesar\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_r_bukubesar',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
		$item->Visible = TRUE;
		$ReportTypes["email"] = $item->Visible ? $ReportLanguage->Phrase("ReportFormEmail") : "";
		$ReportOptions["ReportTypes"] = $ReportTypes;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = TRUE;
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = $this->ExportOptions->UseDropDownButton;
		$this->ExportOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fr_bukubesarsummary\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fr_bukubesarsummary\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton; // v8
		$this->FilterOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("Filters");

		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Set up options (extended)
		$this->SetupExportOptionsExt();

		// Hide options for export
		if ($this->Export <> "") {
			$this->ExportOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}

		// Set up table class
		if ($this->Export == "word" || $this->Export == "excel" || $this->Export == "pdf")
			$this->ReportTableClass = "ewTable";
		else
			$this->ReportTableClass = "table ewTable";

		// Hide main table for custom layout
		if ($this->Export <> "" || $this->UseCustomTemplate)
			$this->ReportTableStyle = " style=\"display: none;\"";
	}

	// Set up search options
	function SetupSearchOptions() {
		global $ReportLanguage;

		// Filter panel button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = $this->FilterApplied ? " active" : " active";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fr_bukubesarsummary\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Reset filter
		$item = &$this->SearchOptions->Add("resetfilter");
		$item->Body = "<button type=\"button\" class=\"btn btn-default\" title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" onclick=\"location='" . ewr_CurrentPage() . "?cmd=reset'\">" . $ReportLanguage->Phrase("ResetAllFilter") . "</button>";
		$item->Visible = TRUE && $this->FilterApplied;

		// Button group for reset filter
		$this->SearchOptions->UseButtonGroup = TRUE;

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide options for export
		if ($this->Export <> "")
			$this->SearchOptions->HideAllOptions();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $ReportLanguage, $EWR_EXPORT, $gsExportFile;
		if (@$_POST["customexport"] == "") {

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		}

		// Export
		if ($this->Export <> "" && array_key_exists($this->Export, $EWR_EXPORT)) {
			if (@$_POST["data"] <> "") {
				$sContent = $_POST["data"];
				$gsExportFile = @$_POST["filename"];
				if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			} else {
				$sContent = ob_get_contents();
			}
			if (ob_get_length())
				ob_end_clean();

			// Remove all <div data-tagid="..." id="orig..." class="hide">...</div> (for customviewtag export, except "googlemaps")
			if (preg_match_all('/<div\s+data-tagid=[\'"]([\s\S]*?)[\'"]\s+id=[\'"]orig([\s\S]*?)[\'"]\s+class\s*=\s*[\'"]hide[\'"]>([\s\S]*?)<\/div\s*>/i', $sContent, $divmatches, PREG_SET_ORDER)) {
				foreach ($divmatches as $divmatch) {
					if ($divmatch[1] <> "googlemaps")
						$sContent = str_replace($divmatch[0], '', $sContent);
				}
			}
			$fn = $EWR_EXPORT[$this->Export];
			if ($this->Export == "email") { // Email
				if (@$this->GenOptions["reporttype"] == "email") {
					$saveResponse = $this->$fn($sContent, $this->GenOptions);
					$this->WriteGenResponse($saveResponse);
				} else {
					echo $this->$fn($sContent, array());
				}
				$url = ""; // Avoid redirect
			} else {
				$saveToFile = $this->$fn($sContent, $this->GenOptions);
				if (@$this->GenOptions["reporttype"] <> "") {
					$saveUrl = ($saveToFile <> "") ? ewr_ConvertFullUrl($saveToFile) : $ReportLanguage->Phrase("GenerateSuccess");
					$this->WriteGenResponse($saveUrl);
					$url = ""; // Avoid redirect
				}
			}
		}

		 // Close connection
		ewr_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EWR_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}

	// Initialize common variables
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $FilterOptions; // Filter options

	// Paging variables
	var $RecIndex = 0; // Record index
	var $RecCount = 0; // Record count
	var $StartGrp = 0; // Start group
	var $StopGrp = 0; // Stop group
	var $TotalGrps = 0; // Total groups
	var $GrpCount = 0; // Group count
	var $GrpCounter = array(); // Group counter
	var $DisplayGrps = 20; // Groups per page
	var $GrpRange = 10;
	var $Sort = "";
	var $Filter = "";
	var $PageFirstGroupFilter = "";
	var $UserIDFilter = "";
	var $DrillDown = FALSE;
	var $DrillDownInPanel = FALSE;
	var $DrillDownList = "";

	// Clear field for ext filter
	var $ClearExtFilter = "";
	var $PopupName = "";
	var $PopupValue = "";
	var $FilterApplied;
	var $SearchCommand = FALSE;
	var $ShowHeader;
	var $GrpColumnCount = 0;
	var $SubGrpColumnCount = 0;
	var $DtlColumnCount = 0;
	var $Cnt, $Col, $Val, $Smry, $Mn, $Mx, $GrandCnt, $GrandSmry, $GrandMn, $GrandMx;
	var $TotCount;
	var $GrandSummarySetup = FALSE;
	var $GrpIdx;
	var $DetailRows = array();

	//
	// Page main
	//
	function Page_Main() {
		global $rs;
		global $rsgrp;
		global $Security;
		global $gsFormError;
		global $gbDrillDownInPanel;
		global $ReportBreadcrumb;
		global $ReportLanguage;

		// Set field visibility for detail fields
		$this->tgl->SetVisibility();
		$this->no_bukti->SetVisibility();
		$this->ket->SetVisibility();
		$this->debet->SetVisibility();
		$this->kredit->SetVisibility();
		$this->sa_debet->SetVisibility();
		$this->sm_debet->SetVisibility();
		$this->sa_kredit->SetVisibility();
		$this->sm_kredit->SetVisibility();

		// Aggregate variables
		// 1st dimension = no of groups (level 0 used for grand total)
		// 2nd dimension = no of fields

		$nDtls = 10;
		$nGrps = 2;
		$this->Val = &ewr_InitArray($nDtls, 0);
		$this->Cnt = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Smry = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Mn = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->Mx = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->GrandCnt = &ewr_InitArray($nDtls, 0);
		$this->GrandSmry = &ewr_InitArray($nDtls, 0);
		$this->GrandMn = &ewr_InitArray($nDtls, NULL);
		$this->GrandMx = &ewr_InitArray($nDtls, NULL);

		// Set up array if accumulation required: array(Accum, SkipNullOrZero)
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(TRUE,FALSE), array(TRUE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();
		$this->tgl->SelectionList = "";
		$this->tgl->DefaultSelectionList = "";
		$this->tgl->ValueList = "";

		// Check if search command
		$this->SearchCommand = (@$_GET["cmd"] == "search");

		// Load default filter values
		$this->LoadDefaultFilters();

		// Load custom filters
		$this->Page_FilterLoad();

		// Set up popup filter
		$this->SetupPopup();

		// Load group db values if necessary
		$this->LoadGroupDbValues();

		// Handle Ajax popup
		$this->ProcessAjaxPopup();

		// Extended filter
		$sExtendedFilter = "";

		// Restore filter list
		$this->RestoreFilterList();

		// Build extended filter
		$sExtendedFilter = $this->GetExtendedFilter();
		ewr_AddFilter($this->Filter, $sExtendedFilter);

		// Build popup filter
		$sPopupFilter = $this->GetPopupFilter();

		//ewr_SetDebugMsg("popup filter: " . $sPopupFilter);
		ewr_AddFilter($this->Filter, $sPopupFilter);

		// Check if filter applied
		$this->FilterApplied = $this->CheckFilter();

		// Call Page Selecting event
		$this->Page_Selecting($this->Filter);

		// Requires search criteria
		if (($this->Filter == $this->UserIDFilter || $gsFormError != "") && !$this->DrillDown)
			$this->Filter = "0=101";

		// Search options
		$this->SetupSearchOptions();

		// Get sort
		$this->Sort = $this->GetSort($this->GenOptions);

		// Get total group count
		$sGrpSort = ewr_UpdateSortFields($this->getSqlOrderByGroup(), $this->Sort, 2); // Get grouping field only
		$sSql = ewr_BuildReportSql($this->getSqlSelectGroup(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderByGroup(), $this->Filter, $sGrpSort);
		$this->TotalGrps = $this->GetGrpCnt($sSql);
		if ($this->DisplayGrps <= 0 || $this->DrillDown) // Display all groups
			$this->DisplayGrps = $this->TotalGrps;
		$this->StartGrp = 1;

		// Show header
		$this->ShowHeader = TRUE;

		// Set up start position if not export all
		if ($this->ExportAll && $this->Export <> "")
			$this->DisplayGrps = $this->TotalGrps;
		else
			$this->SetUpStartGroup($this->GenOptions);

		// Set no record found message
		if ($this->TotalGrps == 0) {
			if ($Security->CanList()) {
				if ($this->Filter == "0=101") {
					$this->setWarningMessage($ReportLanguage->Phrase("EnterSearchCriteria"));
				} else {
					$this->setWarningMessage($ReportLanguage->Phrase("NoRecord"));
				}
			} else {
				$this->setWarningMessage($ReportLanguage->Phrase("NoPermission"));
			}
		}

		// Hide export options if export
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();

		// Hide search/filter options if export/drilldown
		if ($this->Export <> "" || $this->DrillDown) {
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
			$this->GenerateOptions->HideAllOptions();
		}

		// Get current page groups
		$rsgrp = $this->GetGrpRs($sSql, $this->StartGrp, $this->DisplayGrps);

		// Init detail recordset
		$rs = NULL;
		$this->SetupFieldCount();
	}

	// Get summary count
	function GetSummaryCount($lvl, $curValue = TRUE) {
		$cnt = 0;
		foreach ($this->DetailRows as $row) {
			$wrkno_nama_akun = $row["no_nama_akun"];
			if ($lvl >= 1) {
				$val = $curValue ? $this->no_nama_akun->CurrentValue : $this->no_nama_akun->OldValue;
				$grpval = $curValue ? $this->no_nama_akun->GroupValue() : $this->no_nama_akun->GroupOldValue();
				if (is_null($val) && !is_null($wrkno_nama_akun) || !is_null($val) && is_null($wrkno_nama_akun) ||
					$grpval <> $this->no_nama_akun->getGroupValueBase($wrkno_nama_akun))
				continue;
			}
			$cnt++;
		}
		return $cnt;
	}

	// Check level break
	function ChkLvlBreak($lvl) {
		switch ($lvl) {
			case 1:
				return (is_null($this->no_nama_akun->CurrentValue) && !is_null($this->no_nama_akun->OldValue)) ||
					(!is_null($this->no_nama_akun->CurrentValue) && is_null($this->no_nama_akun->OldValue)) ||
					($this->no_nama_akun->GroupValue() <> $this->no_nama_akun->GroupOldValue());
		}
	}

	// Accummulate summary
	function AccumulateSummary() {
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				if ($this->Col[$iy][0]) { // Accumulate required
					$valwrk = $this->Val[$iy];
					if (is_null($valwrk)) {
						if (!$this->Col[$iy][1])
							$this->Cnt[$ix][$iy]++;
					} else {
						$accum = (!$this->Col[$iy][1] || !is_numeric($valwrk) || $valwrk <> 0);
						if ($accum) {
							$this->Cnt[$ix][$iy]++;
							if (is_numeric($valwrk)) {
								$this->Smry[$ix][$iy] += $valwrk;
								if (is_null($this->Mn[$ix][$iy])) {
									$this->Mn[$ix][$iy] = $valwrk;
									$this->Mx[$ix][$iy] = $valwrk;
								} else {
									if ($this->Mn[$ix][$iy] > $valwrk) $this->Mn[$ix][$iy] = $valwrk;
									if ($this->Mx[$ix][$iy] < $valwrk) $this->Mx[$ix][$iy] = $valwrk;
								}
							}
						}
					}
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0]++;
		}
	}

	// Reset level summary
	function ResetLevelSummary($lvl) {

		// Clear summary values
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				$this->Cnt[$ix][$iy] = 0;
				if ($this->Col[$iy][0]) {
					$this->Smry[$ix][$iy] = 0;
					$this->Mn[$ix][$iy] = NULL;
					$this->Mx[$ix][$iy] = NULL;
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0] = 0;
		}

		// Reset record count
		$this->RecCount = 0;
	}

	// Accummulate grand summary
	function AccumulateGrandSummary() {
		$this->TotCount++;
		$cntgs = count($this->GrandSmry);
		for ($iy = 1; $iy < $cntgs; $iy++) {
			if ($this->Col[$iy][0]) {
				$valwrk = $this->Val[$iy];
				if (is_null($valwrk) || !is_numeric($valwrk)) {
					if (!$this->Col[$iy][1])
						$this->GrandCnt[$iy]++;
				} else {
					if (!$this->Col[$iy][1] || $valwrk <> 0) {
						$this->GrandCnt[$iy]++;
						$this->GrandSmry[$iy] += $valwrk;
						if (is_null($this->GrandMn[$iy])) {
							$this->GrandMn[$iy] = $valwrk;
							$this->GrandMx[$iy] = $valwrk;
						} else {
							if ($this->GrandMn[$iy] > $valwrk) $this->GrandMn[$iy] = $valwrk;
							if ($this->GrandMx[$iy] < $valwrk) $this->GrandMx[$iy] = $valwrk;
						}
					}
				}
			}
		}
	}

	// Get group count
	function GetGrpCnt($sql) {
		$conn = &$this->Connection();
		$rsgrpcnt = $conn->Execute($sql);
		$grpcnt = ($rsgrpcnt) ? $rsgrpcnt->RecordCount() : 0;
		if ($rsgrpcnt) $rsgrpcnt->Close();
		return $grpcnt;
	}

	// Get group recordset
	function GetGrpRs($wrksql, $start = -1, $grps = -1) {
		$conn = &$this->Connection();
		$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
		$rswrk = $conn->SelectLimit($wrksql, $grps, $start - 1);
		$conn->raiseErrorFn = '';
		return $rswrk;
	}

	// Get group row values
	function GetGrpRow($opt) {
		global $rsgrp;
		if (!$rsgrp)
			return;
		if ($opt == 1) { // Get first group

			//$rsgrp->MoveFirst(); // NOTE: no need to move position
			$this->no_nama_akun->setDbValue(""); // Init first value
		} else { // Get next group
			$rsgrp->MoveNext();
		}
		if (!$rsgrp->EOF)
			$this->no_nama_akun->setDbValue($rsgrp->fields[0]);
		if ($rsgrp->EOF) {
			$this->no_nama_akun->setDbValue("");
		}
	}

	// Get detail recordset
	function GetDetailRs($wrksql) {
		$conn = &$this->Connection();
		$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
		$rswrk = $conn->Execute($wrksql);
		$dbtype = ewr_GetConnectionType($this->DBID);
		if ($dbtype == "MYSQL" || $dbtype == "POSTGRESQL") {
			$this->DetailRows = ($rswrk) ? $rswrk->GetRows() : array();
		} else { // Cannot MoveFirst, use another recordset
			$rstmp = $conn->Execute($wrksql);
			$this->DetailRows = ($rstmp) ? $rstmp->GetRows() : array();
			$rstmp->Close();
		}
		$conn->raiseErrorFn = "";
		return $rswrk;
	}

	// Get row values
	function GetRow($opt) {
		global $rs;
		if (!$rs)
			return;
		if ($opt == 1) { // Get first row
			$rs->MoveFirst(); // Move first
			if ($this->GrpCount == 1) {
				$this->FirstRowData = array();
				$this->FirstRowData['detail_id'] = ewr_Conv($rs->fields('detail_id'), 3);
				$this->FirstRowData['jurnal_id'] = ewr_Conv($rs->fields('jurnal_id'), 3);
				$this->FirstRowData['akun_id'] = ewr_Conv($rs->fields('akun_id'), 3);
				$this->FirstRowData['no_akun'] = ewr_Conv($rs->fields('no_akun'), 200);
				$this->FirstRowData['nama_akun'] = ewr_Conv($rs->fields('nama_akun'), 200);
				$this->FirstRowData['no_nama_akun'] = ewr_Conv($rs->fields('no_nama_akun'), 200);
				$this->FirstRowData['tgl'] = ewr_Conv($rs->fields('tgl'), 133);
				$this->FirstRowData['no_bukti'] = ewr_Conv($rs->fields('no_bukti'), 200);
				$this->FirstRowData['debet'] = ewr_Conv($rs->fields('debet'), 20);
				$this->FirstRowData['kredit'] = ewr_Conv($rs->fields('kredit'), 20);
				$this->FirstRowData['sa_debet'] = ewr_Conv($rs->fields('sa_debet'), 4);
				$this->FirstRowData['sm_debet'] = ewr_Conv($rs->fields('sm_debet'), 4);
				$this->FirstRowData['sa_kredit'] = ewr_Conv($rs->fields('sa_kredit'), 4);
				$this->FirstRowData['sm_kredit'] = ewr_Conv($rs->fields('sm_kredit'), 4);
			}
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->detail_id->setDbValue($rs->fields('detail_id'));
			$this->jurnal_id->setDbValue($rs->fields('jurnal_id'));
			$this->akun_id->setDbValue($rs->fields('akun_id'));
			$this->no_akun->setDbValue($rs->fields('no_akun'));
			$this->nama_akun->setDbValue($rs->fields('nama_akun'));
			if ($opt <> 1) {
				if (is_array($this->no_nama_akun->GroupDbValues))
					$this->no_nama_akun->setDbValue(@$this->no_nama_akun->GroupDbValues[$rs->fields('no_nama_akun')]);
				else
					$this->no_nama_akun->setDbValue(ewr_GroupValue($this->no_nama_akun, $rs->fields('no_nama_akun')));
			}
			$this->tgl->setDbValue($rs->fields('tgl'));
			$this->no_bukti->setDbValue($rs->fields('no_bukti'));
			$this->ket->setDbValue($rs->fields('ket'));
			$this->debet->setDbValue($rs->fields('debet'));
			$this->kredit->setDbValue($rs->fields('kredit'));
			$this->sa_debet->setDbValue($rs->fields('sa_debet'));
			$this->sm_debet->setDbValue($rs->fields('sm_debet'));
			$this->sa_kredit->setDbValue($rs->fields('sa_kredit'));
			$this->sm_kredit->setDbValue($rs->fields('sm_kredit'));
			$this->Val[1] = $this->tgl->CurrentValue;
			$this->Val[2] = $this->no_bukti->CurrentValue;
			$this->Val[3] = $this->ket->CurrentValue;
			$this->Val[4] = $this->debet->CurrentValue;
			$this->Val[5] = $this->kredit->CurrentValue;
			$this->Val[6] = $this->sa_debet->CurrentValue;
			$this->Val[7] = $this->sm_debet->CurrentValue;
			$this->Val[8] = $this->sa_kredit->CurrentValue;
			$this->Val[9] = $this->sm_kredit->CurrentValue;
		} else {
			$this->detail_id->setDbValue("");
			$this->jurnal_id->setDbValue("");
			$this->akun_id->setDbValue("");
			$this->no_akun->setDbValue("");
			$this->nama_akun->setDbValue("");
			$this->no_nama_akun->setDbValue("");
			$this->tgl->setDbValue("");
			$this->no_bukti->setDbValue("");
			$this->ket->setDbValue("");
			$this->debet->setDbValue("");
			$this->kredit->setDbValue("");
			$this->sa_debet->setDbValue("");
			$this->sm_debet->setDbValue("");
			$this->sa_kredit->setDbValue("");
			$this->sm_kredit->setDbValue("");
		}
	}

	// Set up starting group
	function SetUpStartGroup($options = array()) {

		// Exit if no groups
		if ($this->DisplayGrps == 0)
			return;
		$startGrp = (@$options["start"] <> "") ? $options["start"] : @$_GET[EWR_TABLE_START_GROUP];
		$pageNo = (@$options["pageno"] <> "") ? $options["pageno"] : @$_GET["pageno"];

		// Check for a 'start' parameter
		if ($startGrp != "") {
			$this->StartGrp = $startGrp;
			$this->setStartGroup($this->StartGrp);
		} elseif ($pageNo != "") {
			$nPageNo = $pageNo;
			if (is_numeric($nPageNo)) {
				$this->StartGrp = ($nPageNo-1)*$this->DisplayGrps+1;
				if ($this->StartGrp <= 0) {
					$this->StartGrp = 1;
				} elseif ($this->StartGrp >= intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1) {
					$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1;
				}
				$this->setStartGroup($this->StartGrp);
			} else {
				$this->StartGrp = $this->getStartGroup();
			}
		} else {
			$this->StartGrp = $this->getStartGroup();
		}

		// Check if correct start group counter
		if (!is_numeric($this->StartGrp) || $this->StartGrp == "") { // Avoid invalid start group counter
			$this->StartGrp = 1; // Reset start group counter
			$this->setStartGroup($this->StartGrp);
		} elseif (intval($this->StartGrp) > intval($this->TotalGrps)) { // Avoid starting group > total groups
			$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to last page first group
			$this->setStartGroup($this->StartGrp);
		} elseif (($this->StartGrp-1) % $this->DisplayGrps <> 0) {
			$this->StartGrp = intval(($this->StartGrp-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to page boundary
			$this->setStartGroup($this->StartGrp);
		}
	}

	// Load group db values if necessary
	function LoadGroupDbValues() {
		$conn = &$this->Connection();
	}

	// Process Ajax popup
	function ProcessAjaxPopup() {
		global $ReportLanguage;
		$conn = &$this->Connection();
		$fld = NULL;
		if (@$_GET["popup"] <> "") {
			$popupname = $_GET["popup"];

			// Check popup name
			// Build distinct values for tgl

			if ($popupname == 'r_bukubesar_tgl') {
				$bNullValue = FALSE;
				$bEmptyValue = FALSE;
				$sFilter = $this->Filter;

				// Call Page Filtering event
				$this->Page_Filtering($this->tgl, $sFilter, "popup");
				$sSql = ewr_BuildReportSql($this->tgl->SqlSelect, $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->tgl->SqlOrderBy, $sFilter, "");
				$rswrk = $conn->Execute($sSql);
				while ($rswrk && !$rswrk->EOF) {
					$this->tgl->setDbValue($rswrk->fields[0]);
					$this->tgl->ViewValue = @$rswrk->fields[1];
					if (is_null($this->tgl->CurrentValue)) {
						$bNullValue = TRUE;
					} elseif ($this->tgl->CurrentValue == "") {
						$bEmptyValue = TRUE;
					} else {
						ewr_SetupDistinctValues($this->tgl->ValueList, $this->tgl->CurrentValue, $this->tgl->ViewValue, FALSE, $this->tgl->FldDelimiter);
					}
					$rswrk->MoveNext();
				}
				if ($rswrk)
					$rswrk->Close();
				if ($bEmptyValue)
					ewr_SetupDistinctValues($this->tgl->ValueList, EWR_EMPTY_VALUE, $ReportLanguage->Phrase("EmptyLabel"), FALSE);
				if ($bNullValue)
					ewr_SetupDistinctValues($this->tgl->ValueList, EWR_NULL_VALUE, $ReportLanguage->Phrase("NullLabel"), FALSE);
				$fld = &$this->tgl;
			}

			// Output data as Json
			if (!is_null($fld)) {
				$jsdb = ewr_GetJsDb($fld, $fld->FldType);
				if (ob_get_length())
					ob_end_clean();
				echo $jsdb;
				exit();
			}
		}
	}

	// Set up popup
	function SetupPopup() {
		global $ReportLanguage;
		$conn = &$this->Connection();
		if ($this->DrillDown)
			return;

		// Process post back form
		if (ewr_IsHttpPost()) {
			$sName = @$_POST["popup"]; // Get popup form name
			if ($sName <> "") {
				$cntValues = (is_array(@$_POST["sel_$sName"])) ? count($_POST["sel_$sName"]) : 0;
				if ($cntValues > 0) {
					$arValues = ewr_StripSlashes($_POST["sel_$sName"]);
					if (trim($arValues[0]) == "") // Select all
						$arValues = EWR_INIT_VALUE;
					$this->PopupName = $sName;
					if (ewr_IsAdvancedFilterValue($arValues) || $arValues == EWR_INIT_VALUE)
						$this->PopupValue = $arValues;
					if (!ewr_MatchedArray($arValues, $_SESSION["sel_$sName"])) {
						if ($this->HasSessionFilterValues($sName))
							$this->ClearExtFilter = $sName; // Clear extended filter for this field
					}
					$_SESSION["sel_$sName"] = $arValues;
					$_SESSION["rf_$sName"] = ewr_StripSlashes(@$_POST["rf_$sName"]);
					$_SESSION["rt_$sName"] = ewr_StripSlashes(@$_POST["rt_$sName"]);
					$this->ResetPager();
				}
			}

		// Get 'reset' command
		} elseif (@$_GET["cmd"] <> "") {
			$sCmd = $_GET["cmd"];
			if (strtolower($sCmd) == "reset") {
				$this->ClearSessionSelection('tgl');
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
		// Get tgl selected values

		if (is_array(@$_SESSION["sel_r_bukubesar_tgl"])) {
			$this->LoadSelectionFromSession('tgl');
		} elseif (@$_SESSION["sel_r_bukubesar_tgl"] == EWR_INIT_VALUE) { // Select all
			$this->tgl->SelectionList = "";
		}
	}

	// Reset pager
	function ResetPager() {

		// Reset start position (reset command)
		$this->StartGrp = 1;
		$this->setStartGroup($this->StartGrp);
	}

	// Set up number of groups displayed per page
	function SetUpDisplayGrps() {
		$sWrk = @$_GET[EWR_TABLE_GROUP_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayGrps = intval($sWrk);
			} else {
				if (strtoupper($sWrk) == "ALL") { // Display all groups
					$this->DisplayGrps = -1;
				} else {
					$this->DisplayGrps = 20; // Non-numeric, load default
				}
			}
			$this->setGroupPerPage($this->DisplayGrps); // Save to session

			// Reset start position (reset command)
			$this->StartGrp = 1;
			$this->setStartGroup($this->StartGrp);
		} else {
			if ($this->getGroupPerPage() <> "") {
				$this->DisplayGrps = $this->getGroupPerPage(); // Restore from session
			} else {
				$this->DisplayGrps = 20; // Load default
			}
		}
	}

	// Render row
	function RenderRow() {
		global $rs, $Security, $ReportLanguage;
		$conn = &$this->Connection();
		if (!$this->GrandSummarySetup) { // Get Grand total
			$bGotCount = FALSE;
			$bGotSummary = FALSE;

			// Get total count from sql directly
			$sSql = ewr_BuildReportSql($this->getSqlSelectCount(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
			$rstot = $conn->Execute($sSql);
			if ($rstot) {
				$this->TotCount = ($rstot->RecordCount()>1) ? $rstot->RecordCount() : $rstot->fields[0];
				$rstot->Close();
				$bGotCount = TRUE;
			} else {
				$this->TotCount = 0;
			}

			// Get total from sql directly
			$sSql = ewr_BuildReportSql($this->getSqlSelectAgg(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
			$sSql = $this->getSqlAggPfx() . $sSql . $this->getSqlAggSfx();
			$rsagg = $conn->Execute($sSql);
			if ($rsagg) {
				$this->GrandCnt[1] = $this->TotCount;
				$this->GrandCnt[2] = $this->TotCount;
				$this->GrandCnt[3] = $this->TotCount;
				$this->GrandCnt[4] = $this->TotCount;
				$this->GrandSmry[4] = $rsagg->fields("sum_debet");
				$this->GrandCnt[5] = $this->TotCount;
				$this->GrandSmry[5] = $rsagg->fields("sum_kredit");
				$this->GrandCnt[6] = $this->TotCount;
				$this->GrandCnt[7] = $this->TotCount;
				$this->GrandCnt[8] = $this->TotCount;
				$this->GrandCnt[9] = $this->TotCount;
				$rsagg->Close();
				$bGotSummary = TRUE;
			}

			// Accumulate grand summary from detail records
			if (!$bGotCount || !$bGotSummary) {
				$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
				$rs = $conn->Execute($sSql);
				if ($rs) {
					$this->GetRow(1);
					while (!$rs->EOF) {
						$this->AccumulateGrandSummary();
						$this->GetRow(2);
					}
					$rs->Close();
				}
			}
			$this->GrandSummarySetup = TRUE; // No need to set up again
		}

		// Call Row_Rendering event
		$this->Row_Rendering();

		//
		// Render view codes
		//

		if ($this->RowType == EWR_ROWTYPE_TOTAL && !($this->RowTotalType == EWR_ROWTOTAL_GROUP && $this->RowTotalSubType == EWR_ROWTOTAL_HEADER)) { // Summary row
			ewr_PrependClass($this->RowAttrs["class"], ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel); // Set up row class
			if ($this->RowTotalType == EWR_ROWTOTAL_GROUP) $this->RowAttrs["data-group"] = $this->no_nama_akun->GroupOldValue(); // Set up group attribute

			// no_nama_akun
			$this->no_nama_akun->GroupViewValue = $this->no_nama_akun->GroupOldValue();
			$this->no_nama_akun->CellAttrs["class"] = "ewRptGrpField1";
			$this->no_nama_akun->GroupViewValue = ewr_DisplayGroupValue($this->no_nama_akun, $this->no_nama_akun->GroupViewValue);
			$this->no_nama_akun->GroupSummaryOldValue = $this->no_nama_akun->GroupSummaryValue;
			$this->no_nama_akun->GroupSummaryValue = $this->no_nama_akun->GroupViewValue;
			$this->no_nama_akun->GroupSummaryViewValue = ($this->no_nama_akun->GroupSummaryOldValue <> $this->no_nama_akun->GroupSummaryValue) ? $this->no_nama_akun->GroupSummaryValue : "&nbsp;";

			// debet
			$this->debet->SumViewValue = $this->debet->SumValue;
			$this->debet->SumViewValue = ewr_FormatNumber($this->debet->SumViewValue, 0, -2, -2, -2);
			$this->debet->CellAttrs["style"] = "text-align:right;";
			$this->debet->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// kredit
			$this->kredit->SumViewValue = $this->kredit->SumValue;
			$this->kredit->SumViewValue = ewr_FormatNumber($this->kredit->SumViewValue, 0, -2, -2, -2);
			$this->kredit->CellAttrs["style"] = "text-align:right;";
			$this->kredit->CellAttrs["class"] = ($this->RowTotalType == EWR_ROWTOTAL_PAGE || $this->RowTotalType == EWR_ROWTOTAL_GRAND) ? "ewRptGrpAggregate" : "ewRptGrpSummary" . $this->RowGroupLevel;

			// no_nama_akun
			$this->no_nama_akun->HrefValue = "";

			// tgl
			$this->tgl->HrefValue = "";

			// no_bukti
			$this->no_bukti->HrefValue = "";

			// ket
			$this->ket->HrefValue = "";

			// debet
			$this->debet->HrefValue = "";

			// kredit
			$this->kredit->HrefValue = "";

			// sa_debet
			$this->sa_debet->HrefValue = "";

			// sm_debet
			$this->sm_debet->HrefValue = "";

			// sa_kredit
			$this->sa_kredit->HrefValue = "";

			// sm_kredit
			$this->sm_kredit->HrefValue = "";
		} else {
			if ($this->RowTotalType == EWR_ROWTOTAL_GROUP && $this->RowTotalSubType == EWR_ROWTOTAL_HEADER) {
			$this->RowAttrs["data-group"] = $this->no_nama_akun->GroupValue(); // Set up group attribute
			} else {
			$this->RowAttrs["data-group"] = $this->no_nama_akun->GroupValue(); // Set up group attribute
			}

			// no_nama_akun
			$this->no_nama_akun->GroupViewValue = $this->no_nama_akun->GroupValue();
			$this->no_nama_akun->CellAttrs["class"] = "ewRptGrpField1";
			$this->no_nama_akun->GroupViewValue = ewr_DisplayGroupValue($this->no_nama_akun, $this->no_nama_akun->GroupViewValue);
			if ($this->no_nama_akun->GroupValue() == $this->no_nama_akun->GroupOldValue() && !$this->ChkLvlBreak(1))
				$this->no_nama_akun->GroupViewValue = "&nbsp;";

			// tgl
			$this->tgl->ViewValue = $this->tgl->CurrentValue;
			$this->tgl->ViewValue = ewr_FormatDateTime($this->tgl->ViewValue, 0);
			$this->tgl->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// no_bukti
			$this->no_bukti->ViewValue = $this->no_bukti->CurrentValue;
			$this->no_bukti->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// ket
			$this->ket->ViewValue = $this->ket->CurrentValue;
			$this->ket->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// debet
			$this->debet->ViewValue = $this->debet->CurrentValue;
			$this->debet->ViewValue = ewr_FormatNumber($this->debet->ViewValue, 0, -2, -2, -2);
			$this->debet->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";
			$this->debet->CellAttrs["style"] = "text-align:right;";

			// kredit
			$this->kredit->ViewValue = $this->kredit->CurrentValue;
			$this->kredit->ViewValue = ewr_FormatNumber($this->kredit->ViewValue, 0, -2, -2, -2);
			$this->kredit->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";
			$this->kredit->CellAttrs["style"] = "text-align:right;";

			// sa_debet
			$this->sa_debet->ViewValue = $this->sa_debet->CurrentValue;
			$this->sa_debet->ViewValue = ewr_FormatNumber($this->sa_debet->ViewValue, $this->sa_debet->DefaultDecimalPrecision, -1, 0, 0);
			$this->sa_debet->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// sm_debet
			$this->sm_debet->ViewValue = $this->sm_debet->CurrentValue;
			$this->sm_debet->ViewValue = ewr_FormatNumber($this->sm_debet->ViewValue, $this->sm_debet->DefaultDecimalPrecision, -1, 0, 0);
			$this->sm_debet->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// sa_kredit
			$this->sa_kredit->ViewValue = $this->sa_kredit->CurrentValue;
			$this->sa_kredit->ViewValue = ewr_FormatNumber($this->sa_kredit->ViewValue, $this->sa_kredit->DefaultDecimalPrecision, -1, 0, 0);
			$this->sa_kredit->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// sm_kredit
			$this->sm_kredit->ViewValue = $this->sm_kredit->CurrentValue;
			$this->sm_kredit->ViewValue = ewr_FormatNumber($this->sm_kredit->ViewValue, $this->sm_kredit->DefaultDecimalPrecision, -1, 0, 0);
			$this->sm_kredit->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// no_nama_akun
			$this->no_nama_akun->HrefValue = "";

			// tgl
			$this->tgl->HrefValue = "";

			// no_bukti
			$this->no_bukti->HrefValue = "";

			// ket
			$this->ket->HrefValue = "";

			// debet
			$this->debet->HrefValue = "";

			// kredit
			$this->kredit->HrefValue = "";

			// sa_debet
			$this->sa_debet->HrefValue = "";

			// sm_debet
			$this->sm_debet->HrefValue = "";

			// sa_kredit
			$this->sa_kredit->HrefValue = "";

			// sm_kredit
			$this->sm_kredit->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row

			// no_nama_akun
			$CurrentValue = $this->no_nama_akun->GroupViewValue;
			$ViewValue = &$this->no_nama_akun->GroupViewValue;
			$ViewAttrs = &$this->no_nama_akun->ViewAttrs;
			$CellAttrs = &$this->no_nama_akun->CellAttrs;
			$HrefValue = &$this->no_nama_akun->HrefValue;
			$LinkAttrs = &$this->no_nama_akun->LinkAttrs;
			$this->Cell_Rendered($this->no_nama_akun, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// debet
			$CurrentValue = $this->debet->SumValue;
			$ViewValue = &$this->debet->SumViewValue;
			$ViewAttrs = &$this->debet->ViewAttrs;
			$CellAttrs = &$this->debet->CellAttrs;
			$HrefValue = &$this->debet->HrefValue;
			$LinkAttrs = &$this->debet->LinkAttrs;
			$this->Cell_Rendered($this->debet, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// kredit
			$CurrentValue = $this->kredit->SumValue;
			$ViewValue = &$this->kredit->SumViewValue;
			$ViewAttrs = &$this->kredit->ViewAttrs;
			$CellAttrs = &$this->kredit->CellAttrs;
			$HrefValue = &$this->kredit->HrefValue;
			$LinkAttrs = &$this->kredit->LinkAttrs;
			$this->Cell_Rendered($this->kredit, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		} else {

			// no_nama_akun
			$CurrentValue = $this->no_nama_akun->GroupValue();
			$ViewValue = &$this->no_nama_akun->GroupViewValue;
			$ViewAttrs = &$this->no_nama_akun->ViewAttrs;
			$CellAttrs = &$this->no_nama_akun->CellAttrs;
			$HrefValue = &$this->no_nama_akun->HrefValue;
			$LinkAttrs = &$this->no_nama_akun->LinkAttrs;
			$this->Cell_Rendered($this->no_nama_akun, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// tgl
			$CurrentValue = $this->tgl->CurrentValue;
			$ViewValue = &$this->tgl->ViewValue;
			$ViewAttrs = &$this->tgl->ViewAttrs;
			$CellAttrs = &$this->tgl->CellAttrs;
			$HrefValue = &$this->tgl->HrefValue;
			$LinkAttrs = &$this->tgl->LinkAttrs;
			$this->Cell_Rendered($this->tgl, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// no_bukti
			$CurrentValue = $this->no_bukti->CurrentValue;
			$ViewValue = &$this->no_bukti->ViewValue;
			$ViewAttrs = &$this->no_bukti->ViewAttrs;
			$CellAttrs = &$this->no_bukti->CellAttrs;
			$HrefValue = &$this->no_bukti->HrefValue;
			$LinkAttrs = &$this->no_bukti->LinkAttrs;
			$this->Cell_Rendered($this->no_bukti, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// ket
			$CurrentValue = $this->ket->CurrentValue;
			$ViewValue = &$this->ket->ViewValue;
			$ViewAttrs = &$this->ket->ViewAttrs;
			$CellAttrs = &$this->ket->CellAttrs;
			$HrefValue = &$this->ket->HrefValue;
			$LinkAttrs = &$this->ket->LinkAttrs;
			$this->Cell_Rendered($this->ket, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// debet
			$CurrentValue = $this->debet->CurrentValue;
			$ViewValue = &$this->debet->ViewValue;
			$ViewAttrs = &$this->debet->ViewAttrs;
			$CellAttrs = &$this->debet->CellAttrs;
			$HrefValue = &$this->debet->HrefValue;
			$LinkAttrs = &$this->debet->LinkAttrs;
			$this->Cell_Rendered($this->debet, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// kredit
			$CurrentValue = $this->kredit->CurrentValue;
			$ViewValue = &$this->kredit->ViewValue;
			$ViewAttrs = &$this->kredit->ViewAttrs;
			$CellAttrs = &$this->kredit->CellAttrs;
			$HrefValue = &$this->kredit->HrefValue;
			$LinkAttrs = &$this->kredit->LinkAttrs;
			$this->Cell_Rendered($this->kredit, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// sa_debet
			$CurrentValue = $this->sa_debet->CurrentValue;
			$ViewValue = &$this->sa_debet->ViewValue;
			$ViewAttrs = &$this->sa_debet->ViewAttrs;
			$CellAttrs = &$this->sa_debet->CellAttrs;
			$HrefValue = &$this->sa_debet->HrefValue;
			$LinkAttrs = &$this->sa_debet->LinkAttrs;
			$this->Cell_Rendered($this->sa_debet, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// sm_debet
			$CurrentValue = $this->sm_debet->CurrentValue;
			$ViewValue = &$this->sm_debet->ViewValue;
			$ViewAttrs = &$this->sm_debet->ViewAttrs;
			$CellAttrs = &$this->sm_debet->CellAttrs;
			$HrefValue = &$this->sm_debet->HrefValue;
			$LinkAttrs = &$this->sm_debet->LinkAttrs;
			$this->Cell_Rendered($this->sm_debet, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// sa_kredit
			$CurrentValue = $this->sa_kredit->CurrentValue;
			$ViewValue = &$this->sa_kredit->ViewValue;
			$ViewAttrs = &$this->sa_kredit->ViewAttrs;
			$CellAttrs = &$this->sa_kredit->CellAttrs;
			$HrefValue = &$this->sa_kredit->HrefValue;
			$LinkAttrs = &$this->sa_kredit->LinkAttrs;
			$this->Cell_Rendered($this->sa_kredit, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// sm_kredit
			$CurrentValue = $this->sm_kredit->CurrentValue;
			$ViewValue = &$this->sm_kredit->ViewValue;
			$ViewAttrs = &$this->sm_kredit->ViewAttrs;
			$CellAttrs = &$this->sm_kredit->CellAttrs;
			$HrefValue = &$this->sm_kredit->HrefValue;
			$LinkAttrs = &$this->sm_kredit->LinkAttrs;
			$this->Cell_Rendered($this->sm_kredit, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		}

		// Call Row_Rendered event
		$this->Row_Rendered();
		$this->SetupFieldCount();
	}

	// Setup field count
	function SetupFieldCount() {
		$this->GrpColumnCount = 0;
		$this->SubGrpColumnCount = 0;
		$this->DtlColumnCount = 0;
		if ($this->no_nama_akun->Visible) $this->GrpColumnCount += 1;
		if ($this->tgl->Visible) $this->DtlColumnCount += 1;
		if ($this->no_bukti->Visible) $this->DtlColumnCount += 1;
		if ($this->ket->Visible) $this->DtlColumnCount += 1;
		if ($this->debet->Visible) $this->DtlColumnCount += 1;
		if ($this->kredit->Visible) $this->DtlColumnCount += 1;
		if ($this->sa_debet->Visible) $this->DtlColumnCount += 1;
		if ($this->sm_debet->Visible) $this->DtlColumnCount += 1;
		if ($this->sa_kredit->Visible) $this->DtlColumnCount += 1;
		if ($this->sm_kredit->Visible) $this->DtlColumnCount += 1;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $ReportBreadcrumb;
		$ReportBreadcrumb = new crBreadcrumb();
		$url = substr(ewr_CurrentUrl(), strrpos(ewr_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$ReportBreadcrumb->Add("summary", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}

	function SetupExportOptionsExt() {
		global $ReportLanguage, $ReportOptions;
		$ReportTypes = $ReportOptions["ReportTypes"];
		$item =& $this->ExportOptions->GetItem("pdf");
		$item->Visible = TRUE;
		if ($item->Visible)
			$ReportTypes["pdf"] = $ReportLanguage->Phrase("ReportFormPdf");
		$exportid = session_id();
		$url = $this->ExportPdfUrl;
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"javascript:void(0);\" onclick=\"ewr_ExportCharts(this, '" . $url . "', '" . $exportid . "');\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
		$ReportOptions["ReportTypes"] = $ReportTypes;
	}

	// Return extended filter
	function GetExtendedFilter() {
		global $gsFormError;
		$sFilter = "";
		if ($this->DrillDown)
			return "";
		$bPostBack = ewr_IsHttpPost();
		$bRestoreSession = TRUE;
		$bSetupFilter = FALSE;

		// Reset extended filter if filter changed
		if ($bPostBack) {

			// Clear extended filter for field tgl
			if ($this->ClearExtFilter == 'r_bukubesar_tgl')
				$this->SetSessionFilterValues('', '=', 'AND', '', '=', 'tgl');

		// Reset search command
		} elseif (@$_GET["cmd"] == "reset") {

			// Load default values
			$this->SetSessionFilterValues($this->tgl->SearchValue, $this->tgl->SearchOperator, $this->tgl->SearchCondition, $this->tgl->SearchValue2, $this->tgl->SearchOperator2, 'tgl'); // Field tgl

			//$bSetupFilter = TRUE; // No need to set up, just use default
		} else {
			$bRestoreSession = !$this->SearchCommand;

			// Field tgl
			if ($this->GetFilterValues($this->tgl)) {
				$bSetupFilter = TRUE;
			}
			if (!$this->ValidateForm()) {
				$this->setFailureMessage($gsFormError);
				return $sFilter;
			}
		}

		// Restore session
		if ($bRestoreSession) {
			$this->GetSessionFilterValues($this->tgl); // Field tgl
		}

		// Call page filter validated event
		$this->Page_FilterValidated();

		// Build SQL
		$this->BuildExtendedFilter($this->tgl, $sFilter, FALSE, TRUE); // Field tgl

		// Save parms to session
		$this->SetSessionFilterValues($this->tgl->SearchValue, $this->tgl->SearchOperator, $this->tgl->SearchCondition, $this->tgl->SearchValue2, $this->tgl->SearchOperator2, 'tgl'); // Field tgl

		// Setup filter
		if ($bSetupFilter) {

			// Field tgl
			$sWrk = "";
			$this->BuildExtendedFilter($this->tgl, $sWrk);
			ewr_LoadSelectionFromFilter($this->tgl, $sWrk, $this->tgl->SelectionList);
			$_SESSION['sel_r_bukubesar_tgl'] = ($this->tgl->SelectionList == "") ? EWR_INIT_VALUE : $this->tgl->SelectionList;
		}
		return $sFilter;
	}

	// Build dropdown filter
	function BuildDropDownFilter(&$fld, &$FilterClause, $FldOpr, $Default = FALSE, $SaveFilter = FALSE) {
		$FldVal = ($Default) ? $fld->DefaultDropDownValue : $fld->DropDownValue;
		$sSql = "";
		if (is_array($FldVal)) {
			foreach ($FldVal as $val) {
				$sWrk = $this->GetDropDownFilter($fld, $val, $FldOpr);

				// Call Page Filtering event
				if (substr($val, 0, 2) <> "@@") $this->Page_Filtering($fld, $sWrk, "dropdown", $FldOpr, $val);
				if ($sWrk <> "") {
					if ($sSql <> "")
						$sSql .= " OR " . $sWrk;
					else
						$sSql = $sWrk;
				}
			}
		} else {
			$sSql = $this->GetDropDownFilter($fld, $FldVal, $FldOpr);

			// Call Page Filtering event
			if (substr($FldVal, 0, 2) <> "@@") $this->Page_Filtering($fld, $sSql, "dropdown", $FldOpr, $FldVal);
		}
		if ($sSql <> "") {
			ewr_AddFilter($FilterClause, $sSql);
			if ($SaveFilter) $fld->CurrentFilter = $sSql;
		}
	}

	function GetDropDownFilter(&$fld, $FldVal, $FldOpr) {
		$FldName = $fld->FldName;
		$FldExpression = $fld->FldExpression;
		$FldDataType = $fld->FldDataType;
		$FldDelimiter = $fld->FldDelimiter;
		$FldVal = strval($FldVal);
		if ($FldOpr == "") $FldOpr = "=";
		$sWrk = "";
		if (ewr_SameStr($FldVal, EWR_NULL_VALUE)) {
			$sWrk = $FldExpression . " IS NULL";
		} elseif (ewr_SameStr($FldVal, EWR_NOT_NULL_VALUE)) {
			$sWrk = $FldExpression . " IS NOT NULL";
		} elseif (ewr_SameStr($FldVal, EWR_EMPTY_VALUE)) {
			$sWrk = $FldExpression . " = ''";
		} elseif (ewr_SameStr($FldVal, EWR_ALL_VALUE)) {
			$sWrk = "1 = 1";
		} else {
			if (substr($FldVal, 0, 2) == "@@") {
				$sWrk = $this->GetCustomFilter($fld, $FldVal, $this->DBID);
			} elseif ($FldDelimiter <> "" && trim($FldVal) <> "" && ($FldDataType == EWR_DATATYPE_STRING || $FldDataType == EWR_DATATYPE_MEMO)) {
				$sWrk = ewr_GetMultiSearchSql($FldExpression, trim($FldVal), $this->DBID);
			} else {
				if ($FldVal <> "" && $FldVal <> EWR_INIT_VALUE) {
					if ($FldDataType == EWR_DATATYPE_DATE && $FldOpr <> "") {
						$sWrk = ewr_DateFilterString($FldExpression, $FldOpr, $FldVal, $FldDataType, $this->DBID);
					} else {
						$sWrk = ewr_FilterString($FldOpr, $FldVal, $FldDataType, $this->DBID);
						if ($sWrk <> "") $sWrk = $FldExpression . $sWrk;
					}
				}
			}
		}
		return $sWrk;
	}

	// Get custom filter
	function GetCustomFilter(&$fld, $FldVal, $dbid = 0) {
		$sWrk = "";
		if (is_array($fld->AdvancedFilters)) {
			foreach ($fld->AdvancedFilters as $filter) {
				if ($filter->ID == $FldVal && $filter->Enabled) {
					$sFld = $fld->FldExpression;
					$sFn = $filter->FunctionName;
					$wrkid = (substr($filter->ID,0,2) == "@@") ? substr($filter->ID,2) : $filter->ID;
					if ($sFn <> "")
						$sWrk = $sFn($sFld, $dbid);
					else
						$sWrk = "";
					$this->Page_Filtering($fld, $sWrk, "custom", $wrkid);
					break;
				}
			}
		}
		return $sWrk;
	}

	// Build extended filter
	function BuildExtendedFilter(&$fld, &$FilterClause, $Default = FALSE, $SaveFilter = FALSE) {
		$sWrk = ewr_GetExtendedFilter($fld, $Default, $this->DBID);
		if (!$Default)
			$this->Page_Filtering($fld, $sWrk, "extended", $fld->SearchOperator, $fld->SearchValue, $fld->SearchCondition, $fld->SearchOperator2, $fld->SearchValue2);
		if ($sWrk <> "") {
			ewr_AddFilter($FilterClause, $sWrk);
			if ($SaveFilter) $fld->CurrentFilter = $sWrk;
		}
	}

	// Get drop down value from querystring
	function GetDropDownValue(&$fld) {
		$parm = substr($fld->FldVar, 2);
		if (ewr_IsHttpPost())
			return FALSE; // Skip post back
		if (isset($_GET["so_$parm"]))
			$fld->SearchOperator = ewr_StripSlashes(@$_GET["so_$parm"]);
		if (isset($_GET["sv_$parm"])) {
			$fld->DropDownValue = ewr_StripSlashes(@$_GET["sv_$parm"]);
			return TRUE;
		}
		return FALSE;
	}

	// Get filter values from querystring
	function GetFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		if (ewr_IsHttpPost())
			return; // Skip post back
		$got = FALSE;
		if (isset($_GET["sv_$parm"])) {
			$fld->SearchValue = ewr_StripSlashes(@$_GET["sv_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["so_$parm"])) {
			$fld->SearchOperator = ewr_StripSlashes(@$_GET["so_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["sc_$parm"])) {
			$fld->SearchCondition = ewr_StripSlashes(@$_GET["sc_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["sv2_$parm"])) {
			$fld->SearchValue2 = ewr_StripSlashes(@$_GET["sv2_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["so2_$parm"])) {
			$fld->SearchOperator2 = ewr_StripSlashes($_GET["so2_$parm"]);
			$got = TRUE;
		}
		return $got;
	}

	// Set default ext filter
	function SetDefaultExtFilter(&$fld, $so1, $sv1, $sc, $so2, $sv2) {
		$fld->DefaultSearchValue = $sv1; // Default ext filter value 1
		$fld->DefaultSearchValue2 = $sv2; // Default ext filter value 2 (if operator 2 is enabled)
		$fld->DefaultSearchOperator = $so1; // Default search operator 1
		$fld->DefaultSearchOperator2 = $so2; // Default search operator 2 (if operator 2 is enabled)
		$fld->DefaultSearchCondition = $sc; // Default search condition (if operator 2 is enabled)
	}

	// Apply default ext filter
	function ApplyDefaultExtFilter(&$fld) {
		$fld->SearchValue = $fld->DefaultSearchValue;
		$fld->SearchValue2 = $fld->DefaultSearchValue2;
		$fld->SearchOperator = $fld->DefaultSearchOperator;
		$fld->SearchOperator2 = $fld->DefaultSearchOperator2;
		$fld->SearchCondition = $fld->DefaultSearchCondition;
	}

	// Check if Text Filter applied
	function TextFilterApplied(&$fld) {
		return (strval($fld->SearchValue) <> strval($fld->DefaultSearchValue) ||
			strval($fld->SearchValue2) <> strval($fld->DefaultSearchValue2) ||
			(strval($fld->SearchValue) <> "" &&
				strval($fld->SearchOperator) <> strval($fld->DefaultSearchOperator)) ||
			(strval($fld->SearchValue2) <> "" &&
				strval($fld->SearchOperator2) <> strval($fld->DefaultSearchOperator2)) ||
			strval($fld->SearchCondition) <> strval($fld->DefaultSearchCondition));
	}

	// Check if Non-Text Filter applied
	function NonTextFilterApplied(&$fld) {
		if (is_array($fld->DropDownValue)) {
			if (is_array($fld->DefaultDropDownValue)) {
				if (count($fld->DefaultDropDownValue) <> count($fld->DropDownValue))
					return TRUE;
				else
					return (count(array_diff($fld->DefaultDropDownValue, $fld->DropDownValue)) <> 0);
			} else {
				return TRUE;
			}
		} else {
			if (is_array($fld->DefaultDropDownValue))
				return TRUE;
			else
				$v1 = strval($fld->DefaultDropDownValue);
			if ($v1 == EWR_INIT_VALUE)
				$v1 = "";
			$v2 = strval($fld->DropDownValue);
			if ($v2 == EWR_INIT_VALUE || $v2 == EWR_ALL_VALUE)
				$v2 = "";
			return ($v1 <> $v2);
		}
	}

	// Get dropdown value from session
	function GetSessionDropDownValue(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->DropDownValue, 'sv_r_bukubesar_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_r_bukubesar_' . $parm);
	}

	// Get filter values from session
	function GetSessionFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->SearchValue, 'sv_r_bukubesar_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_r_bukubesar_' . $parm);
		$this->GetSessionValue($fld->SearchCondition, 'sc_r_bukubesar_' . $parm);
		$this->GetSessionValue($fld->SearchValue2, 'sv2_r_bukubesar_' . $parm);
		$this->GetSessionValue($fld->SearchOperator2, 'so2_r_bukubesar_' . $parm);
	}

	// Get value from session
	function GetSessionValue(&$sv, $sn) {
		if (array_key_exists($sn, $_SESSION))
			$sv = $_SESSION[$sn];
	}

	// Set dropdown value to session
	function SetSessionDropDownValue($sv, $so, $parm) {
		$_SESSION['sv_r_bukubesar_' . $parm] = $sv;
		$_SESSION['so_r_bukubesar_' . $parm] = $so;
	}

	// Set filter values to session
	function SetSessionFilterValues($sv1, $so1, $sc, $sv2, $so2, $parm) {
		$_SESSION['sv_r_bukubesar_' . $parm] = $sv1;
		$_SESSION['so_r_bukubesar_' . $parm] = $so1;
		$_SESSION['sc_r_bukubesar_' . $parm] = $sc;
		$_SESSION['sv2_r_bukubesar_' . $parm] = $sv2;
		$_SESSION['so2_r_bukubesar_' . $parm] = $so2;
	}

	// Check if has Session filter values
	function HasSessionFilterValues($parm) {
		return ((@$_SESSION['sv_' . $parm] <> "" && @$_SESSION['sv_' . $parm] <> EWR_INIT_VALUE) ||
			(@$_SESSION['sv_' . $parm] <> "" && @$_SESSION['sv_' . $parm] <> EWR_INIT_VALUE) ||
			(@$_SESSION['sv2_' . $parm] <> "" && @$_SESSION['sv2_' . $parm] <> EWR_INIT_VALUE));
	}

	// Dropdown filter exist
	function DropDownFilterExist(&$fld, $FldOpr) {
		$sWrk = "";
		$this->BuildDropDownFilter($fld, $sWrk, $FldOpr);
		return ($sWrk <> "");
	}

	// Extended filter exist
	function ExtendedFilterExist(&$fld) {
		$sExtWrk = "";
		$this->BuildExtendedFilter($fld, $sExtWrk);
		return ($sExtWrk <> "");
	}

	// Validate form
	function ValidateForm() {
		global $ReportLanguage, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EWR_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!ewr_CheckDateDef($this->tgl->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->tgl->FldErrMsg();
		}
		if (!ewr_CheckDateDef($this->tgl->SearchValue2)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->tgl->FldErrMsg();
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			$gsFormError .= ($gsFormError <> "") ? "<p>&nbsp;</p>" : "";
			$gsFormError .= $sFormCustomError;
		}
		return $ValidateForm;
	}

	// Clear selection stored in session
	function ClearSessionSelection($parm) {
		$_SESSION["sel_r_bukubesar_$parm"] = "";
		$_SESSION["rf_r_bukubesar_$parm"] = "";
		$_SESSION["rt_r_bukubesar_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->FieldByParm($parm);
		$fld->SelectionList = @$_SESSION["sel_r_bukubesar_$parm"];
		$fld->RangeFrom = @$_SESSION["rf_r_bukubesar_$parm"];
		$fld->RangeTo = @$_SESSION["rt_r_bukubesar_$parm"];
	}

	// Load default value for filters
	function LoadDefaultFilters() {
		/**
		* Set up default values for non Text filters
		*/
		/**
		* Set up default values for extended filters
		* function SetDefaultExtFilter(&$fld, $so1, $sv1, $sc, $so2, $sv2)
		* Parameters:
		* $fld - Field object
		* $so1 - Default search operator 1
		* $sv1 - Default ext filter value 1
		* $sc - Default search condition (if operator 2 is enabled)
		* $so2 - Default search operator 2 (if operator 2 is enabled)
		* $sv2 - Default ext filter value 2 (if operator 2 is enabled)
		*/

		// Field tgl
		$this->SetDefaultExtFilter($this->tgl, "BETWEEN", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->tgl);
		$sWrk = "";
		$this->BuildExtendedFilter($this->tgl, $sWrk, TRUE);
		ewr_LoadSelectionFromFilter($this->tgl, $sWrk, $this->tgl->DefaultSelectionList);
		if (!$this->SearchCommand) $this->tgl->SelectionList = $this->tgl->DefaultSelectionList;
		/**
		* Set up default values for popup filters
		*/

		// Field tgl
		// $this->tgl->DefaultSelectionList = array("val1", "val2");

	}

	// Check if filter applied
	function CheckFilter() {

		// Check tgl text filter
		if ($this->TextFilterApplied($this->tgl))
			return TRUE;

		// Check tgl popup filter
		if (!ewr_MatchedArray($this->tgl->DefaultSelectionList, $this->tgl->SelectionList))
			return TRUE;
		return FALSE;
	}

	// Show list of filters
	function ShowFilterList($showDate = FALSE) {
		global $ReportLanguage;

		// Initialize
		$sFilterList = "";

		// Field tgl
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->tgl, $sExtWrk);
		if (is_array($this->tgl->SelectionList))
			$sWrk = ewr_JoinArray($this->tgl->SelectionList, ", ", EWR_DATATYPE_DATE, 0, $this->DBID);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->tgl->FldCaption() . "</span>" . $sFilter . "</div>";
		$divstyle = ($this->Export <> "" || $this->UseCustomTemplate) ? " style=\"display: none;\"" : "";
		$divdataclass = ($this->Export <> "" || $this->UseCustomTemplate) ? " data-class=\"tp_current_filters\"" : "";

		// Show Filters
		if ($sFilterList <> "" || $showDate) {
			$sMessage = "<div" . $divstyle . $divdataclass . "><div id=\"ewrFilterList\" class=\"alert alert-info ewDisplayTable\">";
			if ($showDate)
				$sMessage .= "<div id=\"ewrCurrentDate\">" . $ReportLanguage->Phrase("ReportGeneratedDate") . ewr_FormatDateTime(date("Y-m-d H:i:s"), 1) . "</div>";
			if ($sFilterList <> "")
				$sMessage .= "<div id=\"ewrCurrentFilters\">" . $ReportLanguage->Phrase("CurrentFilters") . "</div>" . $sFilterList;
			$sMessage .= "</div></div>";
			$this->Message_Showing($sMessage, "");
			echo $sMessage;
		} else {
			echo "<span" . $divdataclass . "></span>"; // Show dummy span
		}
	}

	// Get list of filters
	function GetFilterList() {

		// Initialize
		$sFilterList = "";

		// Field tgl
		$sWrk = "";
		if ($this->tgl->SearchValue <> "" || $this->tgl->SearchValue2 <> "") {
			$sWrk = "\"sv_tgl\":\"" . ewr_JsEncode2($this->tgl->SearchValue) . "\"," .
				"\"so_tgl\":\"" . ewr_JsEncode2($this->tgl->SearchOperator) . "\"," .
				"\"sc_tgl\":\"" . ewr_JsEncode2($this->tgl->SearchCondition) . "\"," .
				"\"sv2_tgl\":\"" . ewr_JsEncode2($this->tgl->SearchValue2) . "\"," .
				"\"so2_tgl\":\"" . ewr_JsEncode2($this->tgl->SearchOperator2) . "\"";
		}
		if ($sWrk == "") {
			$sWrk = ($this->tgl->SelectionList <> EWR_INIT_VALUE) ? $this->tgl->SelectionList : "";
			if (is_array($sWrk))
				$sWrk = implode("||", $sWrk);
			if ($sWrk <> "")
				$sWrk = "\"sel_tgl\":\"" . ewr_JsEncode2($sWrk) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Return filter list in json
		if ($sFilterList <> "")
			return "{" . $sFilterList . "}";
		else
			return "null";
	}

	// Restore list of filters
	function RestoreFilterList() {

		// Return if not reset filter
		if (@$_POST["cmd"] <> "resetfilter")
			return FALSE;
		$filter = json_decode(ewr_StripSlashes(@$_POST["filter"]), TRUE);
		return $this->SetupFilterList($filter);
	}

	// Setup list of filters
	function SetupFilterList($filter) {
		if (!is_array($filter))
			return FALSE;

		// Field tgl
		$bRestoreFilter = FALSE;
		if (array_key_exists("sv_tgl", $filter) || array_key_exists("so_tgl", $filter) ||
			array_key_exists("sc_tgl", $filter) ||
			array_key_exists("sv2_tgl", $filter) || array_key_exists("so2_tgl", $filter)) {
			$this->SetSessionFilterValues(@$filter["sv_tgl"], @$filter["so_tgl"], @$filter["sc_tgl"], @$filter["sv2_tgl"], @$filter["so2_tgl"], "tgl");
			$bRestoreFilter = TRUE;
		}
		if (array_key_exists("sel_tgl", $filter)) {
			$sWrk = $filter["sel_tgl"];
			$sWrk = explode("||", $sWrk);
			$this->tgl->SelectionList = $sWrk;
			$_SESSION["sel_r_bukubesar_tgl"] = $sWrk;
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "tgl"); // Clear extended filter
			$bRestoreFilter = TRUE;
		}
		if (!$bRestoreFilter) { // Clear filter
			$this->SetSessionFilterValues("", "=", "AND", "", "=", "tgl");
			$this->tgl->SelectionList = "";
			$_SESSION["sel_r_bukubesar_tgl"] = "";
		}
		return TRUE;
	}

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
		if (!$this->ExtendedFilterExist($this->tgl)) {
			if (is_array($this->tgl->SelectionList)) {
				$sFilter = ewr_FilterSQL($this->tgl, "`tgl`", EWR_DATATYPE_DATE, $this->DBID);

				// Call Page Filtering event
				$this->Page_Filtering($this->tgl, $sFilter, "popup");
				$this->tgl->CurrentFilter = $sFilter;
				ewr_AddFilter($sWrk, $sFilter);
			}
		}
		return $sWrk;
	}

	//-------------------------------------------------------------------------------
	// Function GetSort
	// - Return Sort parameters based on Sort Links clicked
	// - Variables setup: Session[EWR_TABLE_SESSION_ORDER_BY], Session["sort_Table_Field"]
	function GetSort($options = array()) {
		if ($this->DrillDown)
			return "`tgl` ASC, `no_bukti` ASC";
		$bResetSort = @$options["resetsort"] == "1" || @$_GET["cmd"] == "resetsort";
		$orderBy = (@$options["order"] <> "") ? @$options["order"] : ewr_StripSlashes(@$_GET["order"]);
		$orderType = (@$options["ordertype"] <> "") ? @$options["ordertype"] : ewr_StripSlashes(@$_GET["ordertype"]);

		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");

		// Check for a resetsort command
		if ($bResetSort) {
			$this->setOrderBy("");
			$this->setStartGroup(1);
			$this->no_nama_akun->setSort("");
			$this->tgl->setSort("");
			$this->no_bukti->setSort("");
			$this->ket->setSort("");
			$this->debet->setSort("");
			$this->kredit->setSort("");
			$this->sa_debet->setSort("");
			$this->sm_debet->setSort("");
			$this->sa_kredit->setSort("");
			$this->sm_kredit->setSort("");

		// Check for an Order parameter
		} elseif ($orderBy <> "") {
			$this->CurrentOrder = $orderBy;
			$this->CurrentOrderType = $orderType;
			$this->UpdateSort($this->no_nama_akun, $bCtrl); // no_nama_akun
			$this->UpdateSort($this->tgl, $bCtrl); // tgl
			$this->UpdateSort($this->no_bukti, $bCtrl); // no_bukti
			$this->UpdateSort($this->ket, $bCtrl); // ket
			$this->UpdateSort($this->debet, $bCtrl); // debet
			$this->UpdateSort($this->kredit, $bCtrl); // kredit
			$this->UpdateSort($this->sa_debet, $bCtrl); // sa_debet
			$this->UpdateSort($this->sm_debet, $bCtrl); // sm_debet
			$this->UpdateSort($this->sa_kredit, $bCtrl); // sa_kredit
			$this->UpdateSort($this->sm_kredit, $bCtrl); // sm_kredit
			$sSortSql = $this->SortSql();
			$this->setOrderBy($sSortSql);
			$this->setStartGroup(1);
		}

		// Set up default sort
		if ($this->getOrderBy() == "") {
			$this->setOrderBy("`tgl` ASC, `no_bukti` ASC");
			$this->tgl->setSort("ASC");
			$this->no_bukti->setSort("ASC");
		}
		return $this->getOrderBy();
	}

	// Export email
	function ExportEmail($EmailContent, $options = array()) {
		global $gTmpImages, $ReportLanguage;
		$bGenRequest = @$options["reporttype"] == "email";
		$sFailRespPfx = $bGenRequest ? "" : "<p class=\"text-error\">";
		$sSuccessRespPfx = $bGenRequest ? "" : "<p class=\"text-success\">";
		$sRespPfx = $bGenRequest ? "" : "</p>";
		$sContentType = (@$options["contenttype"] <> "") ? $options["contenttype"] : @$_POST["contenttype"];
		$sSender = (@$options["sender"] <> "") ? $options["sender"] : @$_POST["sender"];
		$sRecipient = (@$options["recipient"] <> "") ? $options["recipient"] : @$_POST["recipient"];
		$sCc = (@$options["cc"] <> "") ? $options["cc"] : @$_POST["cc"];
		$sBcc = (@$options["bcc"] <> "") ? $options["bcc"] : @$_POST["bcc"];

		// Subject
		$sEmailSubject = (@$options["subject"] <> "") ? $options["subject"] : ewr_StripSlashes(@$_POST["subject"]);

		// Message
		$sEmailMessage = (@$options["message"] <> "") ? $options["message"] : ewr_StripSlashes(@$_POST["message"]);

		// Check sender
		if ($sSender == "")
			return $sFailRespPfx . $ReportLanguage->Phrase("EnterSenderEmail") . $sRespPfx;
		if (!ewr_CheckEmail($sSender))
			return $sFailRespPfx . $ReportLanguage->Phrase("EnterProperSenderEmail") . $sRespPfx;

		// Check recipient
		if (!ewr_CheckEmailList($sRecipient, EWR_MAX_EMAIL_RECIPIENT))
			return $sFailRespPfx . $ReportLanguage->Phrase("EnterProperRecipientEmail") . $sRespPfx;

		// Check cc
		if (!ewr_CheckEmailList($sCc, EWR_MAX_EMAIL_RECIPIENT))
			return $sFailRespPfx . $ReportLanguage->Phrase("EnterProperCcEmail") . $sRespPfx;

		// Check bcc
		if (!ewr_CheckEmailList($sBcc, EWR_MAX_EMAIL_RECIPIENT))
			return $sFailRespPfx . $ReportLanguage->Phrase("EnterProperBccEmail") . $sRespPfx;

		// Check email sent count
		$emailcount = $bGenRequest ? 0 : ewr_LoadEmailCount();
		if (intval($emailcount) >= EWR_MAX_EMAIL_SENT_COUNT)
			return $sFailRespPfx . $ReportLanguage->Phrase("ExceedMaxEmailExport") . $sRespPfx;
		if ($sEmailMessage <> "") {
			if (EWR_REMOVE_XSS) $sEmailMessage = ewr_RemoveXSS($sEmailMessage);
			$sEmailMessage .= ($sContentType == "url") ? "\r\n\r\n" : "<br><br>";
		}
		$sAttachmentContent = ewr_AdjustEmailContent($EmailContent);
		$sAppPath = ewr_FullUrl();
		$sAppPath = substr($sAppPath, 0, strrpos($sAppPath, "/")+1);
		if (strpos($sAttachmentContent, "<head>") !== FALSE)
			$sAttachmentContent = str_replace("<head>", "<head><base href=\"" . $sAppPath . "\">", $sAttachmentContent); // Add <base href> statement inside the header
		else
			$sAttachmentContent = "<base href=\"" . $sAppPath . "\">" . $sAttachmentContent; // Add <base href> statement as the first statement

		//$sAttachmentFile = $this->TableVar . "_" . Date("YmdHis") . ".html";
		$sAttachmentFile = $this->TableVar . "_" . Date("YmdHis") . "_" . ewr_Random() . ".html";
		if ($sContentType == "url") {
			ewr_SaveFile(EWR_UPLOAD_DEST_PATH, $sAttachmentFile, $sAttachmentContent);
			$sAttachmentFile = EWR_UPLOAD_DEST_PATH . $sAttachmentFile;
			$sUrl = $sAppPath . $sAttachmentFile;
			$sEmailMessage .= $sUrl; // Send URL only
			$sAttachmentFile = "";
			$sAttachmentContent = "";
		} else {
			$sEmailMessage .= $sAttachmentContent;

			// Replace images in custom template
			if (preg_match_all('/<img([^>]*)>/i', $sEmailMessage, $matches, PREG_SET_ORDER)) {
				foreach ($matches as $match) {
					if (preg_match('/\s+src\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $match[1], $submatches)) { // Match src='src'
						$src = $submatches[1];

						// Add embedded temp image if not in gTmpImages
						if (substr($src,0,4) == "cid:") {
							$tmpimage = substr($src,4);
							if (substr($tmpimage,0,3) == "tmp") {

								// Add file extension
								$addimage = FALSE;
								if (file_exists(ewr_AppRoot() . EWR_UPLOAD_DEST_PATH . $tmpimage . ".gif")) {
									$tmpimage .= ".gif";
									$addimage = TRUE;
								} elseif (file_exists(ewr_AppRoot() . EWR_UPLOAD_DEST_PATH . $tmpimage . ".jpg")) {
									$tmpimage .= ".jpg";
									$addimage = TRUE;
								} elseif (file_exists(ewr_AppRoot() . EWR_UPLOAD_DEST_PATH . $tmpimage . ".png")) {
									$tmpimage .= ".png";
									$addimage = TRUE;
								}

								// Add to gTmpImages
								if ($addimage) {
									foreach ($gTmpImages as $tmpimage2)
										if ($tmpimage == $tmpimage2)
											$addimage = FALSE;
									if ($addimage)
										$gTmpImages[] = $tmpimage;
								}
							}

						// Not embedded image, create temp image
						} else {
							$data = @file_get_contents($src);
							if ($data <> "")
								$sEmailMessage = str_replace($match[0], "<img src=\"" . ewr_TmpImage($data) . "\">", $sEmailMessage);
						}
					}
				}
			}
			$sAttachmentFile = "";
			$sAttachmentContent = "";
		}

		// Send email
		$Email = new crEmail();
		$Email->Sender = $sSender; // Sender
		$Email->Recipient = $sRecipient; // Recipient
		$Email->Cc = $sCc; // Cc
		$Email->Bcc = $sBcc; // Bcc
		$Email->Subject = $sEmailSubject; // Subject
		$Email->Content = $sEmailMessage; // Content
		if ($sAttachmentFile <> "")
			$Email->AddAttachment($sAttachmentFile, $sAttachmentContent);
		if ($sContentType <> "url") {
			foreach ($gTmpImages as $tmpimage)
				$Email->AddEmbeddedImage($tmpimage);
		}
		$Email->Format = ($sContentType == "url") ? "text" : "html";
		$Email->Charset = EWR_EMAIL_CHARSET;
		$EventArgs = array();
		$bEmailSent = FALSE;
		if ($this->Email_Sending($Email, $EventArgs))
			$bEmailSent = $Email->Send();
		ewr_DeleteTmpImages($EmailContent);

		// Check email sent status
		if ($bEmailSent) {

			// Update email sent count and write log
			ewr_AddEmailLog($sSender, $sRecipient, $sEmailSubject, $sEmailMessage);

			// Sent email success
			return $sSuccessRespPfx . $ReportLanguage->Phrase("SendEmailSuccess") . $sRespPfx; // Set up success message
		} else {

			// Sent email failure
			return $sFailRespPfx . $Email->SendErrDescription . $sRespPfx;
		}
	}

	// Export to HTML
	function ExportHtml($html, $options = array()) {

		//global $gsExportFile;
		//header('Content-Type: text/html' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		//header('Content-Disposition: attachment; filename=' . $gsExportFile . '.html');

		$folder = @$this->GenOptions["folder"];
		$fileName = @$this->GenOptions["filename"];
		$responseType = @$options["responsetype"];
		$saveToFile = "";

		// Save generate file for print
		if ($folder <> "" && $fileName <> "" && ($responseType == "json" || $responseType == "file" && EWR_REPORT_SAVE_OUTPUT_ON_SERVER)) {
			$baseTag = "<base href=\"" . ewr_BaseUrl() . "\">";
			$html = preg_replace('/<head>/', '<head>' . $baseTag, $html);
			ewr_SaveFile($folder, $fileName, $html);
			$saveToFile = ewr_UploadPathEx(FALSE, $folder) . $fileName;
		}
		if ($saveToFile == "" || $responseType == "file")
			echo $html;
		return $saveToFile;
	}

	// Export to WORD
	function ExportWord($html, $options = array()) {
		global $gsExportFile;
		$folder = @$options["folder"];
		$fileName = @$options["filename"];
		$responseType = @$options["responsetype"];
		$saveToFile = "";

		// Replace images in custom template to hyperlinks
		if (preg_match_all('/<img([^>]*)>/i', $html, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				if (preg_match('/\s+src\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $match[1], $submatches)) { // Match src='src'
					$src = $submatches[1];
					$html = str_replace($match[0], "<a class=\"ewExportLink\" href=\"" . ewr_ConvertFullUrl($src) . "\">" . $src . "</a>", $html);
				}
			}
		}
		if ($folder <> "" && $fileName <> "" && ($responseType == "json" || $responseType == "file" && EWR_REPORT_SAVE_OUTPUT_ON_SERVER)) {
		 	ewr_SaveFile(ewr_PathCombine(ewr_AppRoot(), $folder, TRUE), $fileName, $html);
			$saveToFile = ewr_UploadPathEx(FALSE, $folder) . $fileName;
		}
		if ($saveToFile == "" || $responseType == "file") {
			header('Content-Type: application/vnd.ms-word' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
			header('Content-Disposition: attachment; filename=' . $gsExportFile . '.doc');
			echo $html;
		}
		return $saveToFile;
	}

	// Export to EXCEL
	function ExportExcel($html, $options = array()) {
		global $gsExportFile;
		$folder = @$options["folder"];
		$fileName = @$options["filename"];
		$responseType = @$options["responsetype"];
		$saveToFile = "";

		// Replace images in custom template to hyperlinks
		if (preg_match_all('/<img([^>]*)>/i', $html, $matches, PREG_SET_ORDER)) {
			foreach ($matches as $match) {
				if (preg_match('/\s+src\s*=\s*[\'"]([\s\S]*?)[\'"]/i', $match[1], $submatches)) { // Match src='src'
					$src = $submatches[1];
					$html = str_replace($match[0], "<a class=\"ewExportLink\" href=\"" . ewr_ConvertFullUrl($src) . "\">" . $src . "</a>", $html);
				}
			}
		}
		if ($folder <> "" && $fileName <> "" && ($responseType == "json" || $responseType == "file" && EWR_REPORT_SAVE_OUTPUT_ON_SERVER)) {
		 	ewr_SaveFile(ewr_PathCombine(ewr_AppRoot(), $folder, TRUE), $fileName, $html);
			$saveToFile = ewr_UploadPathEx(FALSE, $folder) . $fileName;
		}
		if ($saveToFile == "" || $responseType == "file") {
			header('Content-Type: application/vnd.ms-excel' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
			header('Content-Disposition: attachment; filename=' . $gsExportFile . '.xls');
			echo $html;
		}
		return $saveToFile;
	}

	// Export PDF
	function ExportPdf($html, $options = array()) {
		global $gsExportFile;
		@ini_set("memory_limit", EWR_PDF_MEMORY_LIMIT);
		set_time_limit(EWR_PDF_TIME_LIMIT);
		if (EWR_DEBUG_ENABLED) // Add debug message
			$html = str_replace("</body>", ewr_DebugMsg() . "</body>", $html);
		$dompdf = new \Dompdf\Dompdf(array("pdf_backend" => "Cpdf"));
		$doc = new DOMDocument();
		@$doc->loadHTML('<?xml encoding="uft-8">' . ewr_ConvertToUtf8($html)); // Convert to utf-8
		$spans = $doc->getElementsByTagName("span");
		foreach ($spans as $span) {
			if ($span->getAttribute("class") == "ewFilterCaption")
				$span->parentNode->insertBefore($doc->createElement("span", ":&nbsp;"), $span->nextSibling);
		}
		$html = $doc->saveHTML();
		$html = ewr_ConvertFromUtf8($html);
		$dompdf->load_html($html);
		$dompdf->set_paper("a4", "portrait");
		$dompdf->render();
		$folder = @$options["folder"];
		$fileName = @$options["filename"];
		$responseType = @$options["responsetype"];
		$saveToFile = "";
		if ($folder <> "" && $fileName <> "" && ($responseType == "json" || $responseType == "file" && EWR_REPORT_SAVE_OUTPUT_ON_SERVER)) {
			ewr_SaveFile(ewr_PathCombine(ewr_AppRoot(), $folder, TRUE), $fileName, $dompdf->output());
			$saveToFile = ewr_UploadPathEx(FALSE, $folder) . $fileName;
		}
		if ($saveToFile == "" || $responseType == "file") {
			$sExportFile = strtolower(substr($gsExportFile, -4)) == ".pdf" ? $gsExportFile : $gsExportFile . ".pdf";
			$dompdf->stream($sExportFile, array("Attachment" => 1)); // 0 to open in browser, 1 to download
		}
		ewr_DeleteTmpImages($html);
		return $saveToFile;
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ewr_Header(FALSE) ?>
<?php

// Create page object
if (!isset($r_bukubesar_summary)) $r_bukubesar_summary = new crr_bukubesar_summary();
if (isset($Page)) $OldPage = $Page;
$Page = &$r_bukubesar_summary;

// Page init
$Page->Page_Init();

// Page main
$Page->Page_Main();

// Global Page Rendering event (in ewrusrfn*.php)
Page_Rendering();

// Page Rendering event
$Page->Page_Render();
?>
<?php include_once "header.php" ?>
<?php include_once "phprptinc/header.php" ?>
<?php if ($Page->Export == "" || $Page->Export == "print" || $Page->Export == "email" && @$gsEmailContentType == "url") { ?>
<script type="text/javascript">

// Create page object
var r_bukubesar_summary = new ewr_Page("r_bukubesar_summary");

// Page properties
r_bukubesar_summary.PageID = "summary"; // Page ID
var EWR_PAGE_ID = r_bukubesar_summary.PageID;

// Extend page with Chart_Rendering function
r_bukubesar_summary.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
r_bukubesar_summary.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Form object
var CurrentForm = fr_bukubesarsummary = new ewr_Form("fr_bukubesarsummary");

// Validate method
fr_bukubesarsummary.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	var elm = fobj.sv_tgl;
	if (elm && !ewr_CheckDateDef(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->tgl->FldErrMsg()) ?>"))
			return false;
	}
	var elm = fobj.sv2_tgl;
	if (elm && !ewr_CheckDateDef(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->tgl->FldErrMsg()) ?>"))
			return false;
	}

	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate method
fr_bukubesarsummary.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid.
 	return true;
 }
<?php if (EWR_CLIENT_VALIDATE) { ?>
fr_bukubesarsummary.ValidateRequired = true; // Uses JavaScript validation
<?php } else { ?>
fr_bukubesarsummary.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Use Ajax
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown || $Page->Export <> "" && $Page->CustomExport <> "") { ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($Page->Export == "") { ?>
<!-- container (begin) -->
<div id="ewContainer" class="ewContainer">
<!-- top container (begin) -->
<div id="ewTop" class="ewTop">
<a id="top"></a>
<?php } ?>
<?php if (@$Page->GenOptions["showfilter"] == "1") { ?>
<?php $Page->ShowFilterList(TRUE) ?>
<?php } ?>
<!-- top slot -->
<div class="ewToolbar">
<?php if ($Page->Export == "" && (!$Page->DrillDown || !$Page->DrillDownInPanel)) { ?>
<?php if ($ReportBreadcrumb) $ReportBreadcrumb->Render(); ?>
<?php } ?>
<?php
if (!$Page->DrillDownInPanel) {
	$Page->ExportOptions->Render("body");
	$Page->SearchOptions->Render("body");
	$Page->FilterOptions->Render("body");
	$Page->GenerateOptions->Render("body");
}
?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<?php echo $ReportLanguage->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php $Page->ShowPageHeader(); ?>
<?php $Page->ShowMessage(); ?>
<?php if ($Page->Export == "") { ?>
</div>
<!-- top container (end) -->
	<!-- left container (begin) -->
	<div id="ewLeft" class="ewLeft">
<?php } ?>
	<!-- Left slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- left container (end) -->
	<!-- center container - report (begin) -->
	<div id="ewCenter" class="ewCenter">
<?php } ?>
	<!-- center slot -->
<!-- summary report starts -->
<?php if ($Page->Export <> "pdf") { ?>
<div id="report_summary">
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<!-- Search form (begin) -->
<form name="fr_bukubesarsummary" id="fr_bukubesarsummary" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : " in"; ?>
<div id="fr_bukubesarsummary_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<div id="r_1" class="ewRow">
<div id="c_tgl" class="ewCell form-group">
	<label for="sv_tgl" class="ewSearchCaption ewLabel"><?php echo $Page->tgl->FldCaption() ?></label>
	<span class="ewSearchOperator"><?php echo $ReportLanguage->Phrase("BETWEEN"); ?><input type="hidden" name="so_tgl" id="so_tgl" value="BETWEEN"></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->tgl->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="r_bukubesar" data-field="x_tgl" id="sv_tgl" name="sv_tgl" placeholder="<?php echo $Page->tgl->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->tgl->SearchValue) ?>" data-calendar="true" data-formatid="0"<?php echo $Page->tgl->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_tgl"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_tgl">
<?php ewr_PrependClass($Page->tgl->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" data-table="r_bukubesar" data-field="x_tgl" id="sv2_tgl" name="sv2_tgl" placeholder="<?php echo $Page->tgl->PlaceHolder ?>" value="<?php echo ewr_HtmlEncode($Page->tgl->SearchValue2) ?>" data-calendar="true" data-formatid="0"<?php echo $Page->tgl->EditAttributes() ?>>
</span>
</div>
</div>
<div class="ewRow"><input type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary" value="<?php echo $ReportLanguage->Phrase("Search") ?>">
<input type="reset" name="btnreset" id="btnreset" class="btn hide" value="<?php echo $ReportLanguage->Phrase("Reset") ?>"></div>
</div>
</form>
<script type="text/javascript">
fr_bukubesarsummary.Init();
fr_bukubesarsummary.FilterList = <?php echo $Page->GetFilterList() ?>;
</script>
<!-- Search form (end) -->
<?php } ?>
<?php if ($Page->ShowCurrentFilter) { ?>
<?php $Page->ShowFilterList() ?>
<?php } ?>
<?php

// Set the last group to display if not export all
if ($Page->ExportAll && $Page->Export <> "") {
	$Page->StopGrp = $Page->TotalGrps;
} else {
	$Page->StopGrp = $Page->StartGrp + $Page->DisplayGrps - 1;
}

// Stop group <= total number of groups
if (intval($Page->StopGrp) > intval($Page->TotalGrps))
	$Page->StopGrp = $Page->TotalGrps;
$Page->RecCount = 0;
$Page->RecIndex = 0;

// Get first row
if ($Page->TotalGrps > 0) {
	$Page->GetGrpRow(1);
	$Page->GrpCount = 1;
}
$Page->GrpIdx = ewr_InitArray($Page->StopGrp - $Page->StartGrp + 1, -1);
while ($rsgrp && !$rsgrp->EOF && $Page->GrpCount <= $Page->DisplayGrps || $Page->ShowHeader) {

	// Show dummy header for custom template
	// Show header

	if ($Page->ShowHeader) {
?>
<?php if ($Page->GrpCount > 1) { ?>
</tbody>
</table>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php if ($Page->TotalGrps > 0) { ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php include "r_bukubesarsmrypager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<span data-class="tpb<?php echo $Page->GrpCount-1 ?>_r_bukubesar"><?php echo $Page->PageBreakContent ?></span>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
<?php if ($Page->Export == "word" || $Page->Export == "excel") { ?>
<div class="ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } else { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php include "r_bukubesarsmrypager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<!-- Report grid (begin) -->
<?php if ($Page->Export <> "pdf") { ?>
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php } ?>
<table class="<?php echo $Page->ReportTableClass ?>">
<thead>
	<!-- Table header -->
	<tr class="ewTableHeader">
<?php if ($Page->no_nama_akun->Visible) { ?>
	<?php if ($Page->no_nama_akun->ShowGroupHeaderAsRow) { ?>
	<td data-field="no_nama_akun">&nbsp;</td>
	<?php } else { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="no_nama_akun"><div class="r_bukubesar_no_nama_akun"><span class="ewTableHeaderCaption"><?php echo $Page->no_nama_akun->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="no_nama_akun">
<?php if ($Page->SortUrl($Page->no_nama_akun) == "") { ?>
		<div class="ewTableHeaderBtn r_bukubesar_no_nama_akun">
			<span class="ewTableHeaderCaption"><?php echo $Page->no_nama_akun->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer r_bukubesar_no_nama_akun" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->no_nama_akun) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->no_nama_akun->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->no_nama_akun->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->no_nama_akun->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
	<?php } ?>
<?php } ?>
<?php if ($Page->tgl->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="tgl"><div class="r_bukubesar_tgl"><span class="ewTableHeaderCaption"><?php echo $Page->tgl->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="tgl">
<?php if ($Page->SortUrl($Page->tgl) == "") { ?>
		<div class="ewTableHeaderBtn r_bukubesar_tgl">
			<span class="ewTableHeaderCaption"><?php echo $Page->tgl->FldCaption() ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'r_bukubesar_tgl', false, '<?php echo $Page->tgl->RangeFrom; ?>', '<?php echo $Page->tgl->RangeTo; ?>');" id="x_tgl<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer r_bukubesar_tgl" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->tgl) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->tgl->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->tgl->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->tgl->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
			<a class="ewTableHeaderPopup" title="<?php echo $ReportLanguage->Phrase("Filter"); ?>" onclick="ewr_ShowPopup.call(this, event, 'r_bukubesar_tgl', false, '<?php echo $Page->tgl->RangeFrom; ?>', '<?php echo $Page->tgl->RangeTo; ?>');" id="x_tgl<?php echo $Page->Cnt[0][0]; ?>"><span class="icon-filter"></span></a>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->no_bukti->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="no_bukti"><div class="r_bukubesar_no_bukti"><span class="ewTableHeaderCaption"><?php echo $Page->no_bukti->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="no_bukti">
<?php if ($Page->SortUrl($Page->no_bukti) == "") { ?>
		<div class="ewTableHeaderBtn r_bukubesar_no_bukti">
			<span class="ewTableHeaderCaption"><?php echo $Page->no_bukti->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer r_bukubesar_no_bukti" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->no_bukti) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->no_bukti->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->no_bukti->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->no_bukti->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->ket->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="ket"><div class="r_bukubesar_ket"><span class="ewTableHeaderCaption"><?php echo $Page->ket->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="ket">
<?php if ($Page->SortUrl($Page->ket) == "") { ?>
		<div class="ewTableHeaderBtn r_bukubesar_ket">
			<span class="ewTableHeaderCaption"><?php echo $Page->ket->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer r_bukubesar_ket" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->ket) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->ket->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->ket->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->ket->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->debet->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="debet"><div class="r_bukubesar_debet" style="text-align: right;"><span class="ewTableHeaderCaption"><?php echo $Page->debet->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="debet">
<?php if ($Page->SortUrl($Page->debet) == "") { ?>
		<div class="ewTableHeaderBtn r_bukubesar_debet" style="text-align: right;">
			<span class="ewTableHeaderCaption"><?php echo $Page->debet->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer r_bukubesar_debet" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->debet) ?>',2);" style="text-align: right;">
			<span class="ewTableHeaderCaption"><?php echo $Page->debet->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->debet->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->debet->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->kredit->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="kredit"><div class="r_bukubesar_kredit" style="text-align: right;"><span class="ewTableHeaderCaption"><?php echo $Page->kredit->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="kredit">
<?php if ($Page->SortUrl($Page->kredit) == "") { ?>
		<div class="ewTableHeaderBtn r_bukubesar_kredit" style="text-align: right;">
			<span class="ewTableHeaderCaption"><?php echo $Page->kredit->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer r_bukubesar_kredit" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->kredit) ?>',2);" style="text-align: right;">
			<span class="ewTableHeaderCaption"><?php echo $Page->kredit->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->kredit->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->kredit->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->sa_debet->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="sa_debet"><div class="r_bukubesar_sa_debet"><span class="ewTableHeaderCaption"><?php echo $Page->sa_debet->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="sa_debet">
<?php if ($Page->SortUrl($Page->sa_debet) == "") { ?>
		<div class="ewTableHeaderBtn r_bukubesar_sa_debet">
			<span class="ewTableHeaderCaption"><?php echo $Page->sa_debet->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer r_bukubesar_sa_debet" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->sa_debet) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->sa_debet->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->sa_debet->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->sa_debet->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->sm_debet->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="sm_debet"><div class="r_bukubesar_sm_debet"><span class="ewTableHeaderCaption"><?php echo $Page->sm_debet->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="sm_debet">
<?php if ($Page->SortUrl($Page->sm_debet) == "") { ?>
		<div class="ewTableHeaderBtn r_bukubesar_sm_debet">
			<span class="ewTableHeaderCaption"><?php echo $Page->sm_debet->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer r_bukubesar_sm_debet" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->sm_debet) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->sm_debet->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->sm_debet->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->sm_debet->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->sa_kredit->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="sa_kredit"><div class="r_bukubesar_sa_kredit"><span class="ewTableHeaderCaption"><?php echo $Page->sa_kredit->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="sa_kredit">
<?php if ($Page->SortUrl($Page->sa_kredit) == "") { ?>
		<div class="ewTableHeaderBtn r_bukubesar_sa_kredit">
			<span class="ewTableHeaderCaption"><?php echo $Page->sa_kredit->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer r_bukubesar_sa_kredit" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->sa_kredit) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->sa_kredit->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->sa_kredit->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->sa_kredit->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->sm_kredit->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="sm_kredit"><div class="r_bukubesar_sm_kredit"><span class="ewTableHeaderCaption"><?php echo $Page->sm_kredit->FldCaption() ?></span></div></td>
<?php } else { ?>
	<td data-field="sm_kredit">
<?php if ($Page->SortUrl($Page->sm_kredit) == "") { ?>
		<div class="ewTableHeaderBtn r_bukubesar_sm_kredit">
			<span class="ewTableHeaderCaption"><?php echo $Page->sm_kredit->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer r_bukubesar_sm_kredit" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->sm_kredit) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->sm_kredit->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->sm_kredit->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->sm_kredit->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
	</tr>
</thead>
<tbody>
<?php
		if ($Page->TotalGrps == 0) break; // Show header only
		$Page->ShowHeader = FALSE;
	}

	// Build detail SQL
	$sWhere = ewr_DetailFilterSQL($Page->no_nama_akun, $Page->getSqlFirstGroupField(), $Page->no_nama_akun->GroupValue(), $Page->DBID);
	if ($Page->PageFirstGroupFilter <> "") $Page->PageFirstGroupFilter .= " OR ";
	$Page->PageFirstGroupFilter .= $sWhere;
	if ($Page->Filter != "")
		$sWhere = "($Page->Filter) AND ($sWhere)";
	$sSql = ewr_BuildReportSql($Page->getSqlSelect(), $Page->getSqlWhere(), $Page->getSqlGroupBy(), $Page->getSqlHaving(), $Page->getSqlOrderBy(), $sWhere, $Page->Sort);
	$rs = $Page->GetDetailRs($sSql);
	$rsdtlcnt = ($rs) ? $rs->RecordCount() : 0;
	if ($rsdtlcnt > 0)
		$Page->GetRow(1);
	$Page->GrpIdx[$Page->GrpCount] = $rsdtlcnt;
	while ($rs && !$rs->EOF) { // Loop detail records
		$Page->RecCount++;
		$Page->RecIndex++;
?>
<?php if ($Page->no_nama_akun->Visible && $Page->ChkLvlBreak(1) && $Page->no_nama_akun->ShowGroupHeaderAsRow) { ?>
<?php

		// Render header row
		$Page->ResetAttrs();
		$Page->RowType = EWR_ROWTYPE_TOTAL;
		$Page->RowTotalType = EWR_ROWTOTAL_GROUP;
		$Page->RowTotalSubType = EWR_ROWTOTAL_HEADER;
		$Page->RowGroupLevel = 1;
		$Page->no_nama_akun->Count = $Page->GetSummaryCount(1);
		$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->no_nama_akun->Visible) { ?>
		<td data-field="no_nama_akun"<?php echo $Page->no_nama_akun->CellAttributes(); ?>><span class="ewGroupToggle icon-collapse"></span></td>
<?php } ?>
		<td data-field="no_nama_akun" colspan="<?php echo ($Page->GrpColumnCount + $Page->DtlColumnCount - 1) ?>"<?php echo $Page->no_nama_akun->CellAttributes() ?>>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
		<span class="ewSummaryCaption r_bukubesar_no_nama_akun"><span class="ewTableHeaderCaption"><?php echo $Page->no_nama_akun->FldCaption() ?></span></span>
<?php } else { ?>
	<?php if ($Page->SortUrl($Page->no_nama_akun) == "") { ?>
		<span class="ewSummaryCaption r_bukubesar_no_nama_akun">
			<span class="ewTableHeaderCaption"><?php echo $Page->no_nama_akun->FldCaption() ?></span>
		</span>
	<?php } else { ?>
		<span class="ewTableHeaderBtn ewPointer ewSummaryCaption r_bukubesar_no_nama_akun" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->no_nama_akun) ?>',2);">
			<span class="ewTableHeaderCaption"><?php echo $Page->no_nama_akun->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->no_nama_akun->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->no_nama_akun->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</span>
	<?php } ?>
<?php } ?>
		<?php echo $ReportLanguage->Phrase("SummaryColon") ?>
<span data-class="tpx<?php echo $Page->GrpCount ?>_r_bukubesar_no_nama_akun"<?php echo $Page->no_nama_akun->ViewAttributes() ?>><?php echo $Page->no_nama_akun->GroupViewValue ?></span>
		<span class="ewSummaryCount">(<span class="ewAggregateCaption"><?php echo $ReportLanguage->Phrase("RptCnt") ?></span><?php echo $ReportLanguage->Phrase("AggregateEqual") ?><span class="ewAggregateValue"><?php echo ewr_FormatNumber($Page->no_nama_akun->Count,0,-2,-2,-2) ?></span>)</span>
		</td>
	</tr>
<?php } ?>
<?php

		// Render detail row
		$Page->ResetAttrs();
		$Page->RowType = EWR_ROWTYPE_DETAIL;
		$Page->RenderRow();
?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->no_nama_akun->Visible) { ?>
	<?php if ($Page->no_nama_akun->ShowGroupHeaderAsRow) { ?>
		<td data-field="no_nama_akun"<?php echo $Page->no_nama_akun->CellAttributes(); ?>>&nbsp;</td>
	<?php } else { ?>
		<td data-field="no_nama_akun"<?php echo $Page->no_nama_akun->CellAttributes(); ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_r_bukubesar_no_nama_akun"<?php echo $Page->no_nama_akun->ViewAttributes() ?>><?php echo $Page->no_nama_akun->GroupViewValue ?></span></td>
	<?php } ?>
<?php } ?>
<?php if ($Page->tgl->Visible) { ?>
		<td data-field="tgl"<?php echo $Page->tgl->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_r_bukubesar_tgl"<?php echo $Page->tgl->ViewAttributes() ?>><?php echo $Page->tgl->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->no_bukti->Visible) { ?>
		<td data-field="no_bukti"<?php echo $Page->no_bukti->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_r_bukubesar_no_bukti"<?php echo $Page->no_bukti->ViewAttributes() ?>><?php echo $Page->no_bukti->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->ket->Visible) { ?>
		<td data-field="ket"<?php echo $Page->ket->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_r_bukubesar_ket"<?php echo $Page->ket->ViewAttributes() ?>><?php echo $Page->ket->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->debet->Visible) { ?>
		<td data-field="debet"<?php echo $Page->debet->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_r_bukubesar_debet"<?php echo $Page->debet->ViewAttributes() ?>><?php echo $Page->debet->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->kredit->Visible) { ?>
		<td data-field="kredit"<?php echo $Page->kredit->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_r_bukubesar_kredit"<?php echo $Page->kredit->ViewAttributes() ?>><?php echo $Page->kredit->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->sa_debet->Visible) { ?>
		<td data-field="sa_debet"<?php echo $Page->sa_debet->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_r_bukubesar_sa_debet"<?php echo $Page->sa_debet->ViewAttributes() ?>><?php echo $Page->sa_debet->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->sm_debet->Visible) { ?>
		<td data-field="sm_debet"<?php echo $Page->sm_debet->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_r_bukubesar_sm_debet"<?php echo $Page->sm_debet->ViewAttributes() ?>><?php echo $Page->sm_debet->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->sa_kredit->Visible) { ?>
		<td data-field="sa_kredit"<?php echo $Page->sa_kredit->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_r_bukubesar_sa_kredit"<?php echo $Page->sa_kredit->ViewAttributes() ?>><?php echo $Page->sa_kredit->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->sm_kredit->Visible) { ?>
		<td data-field="sm_kredit"<?php echo $Page->sm_kredit->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->GrpCount ?>_<?php echo $Page->RecCount ?>_r_bukubesar_sm_kredit"<?php echo $Page->sm_kredit->ViewAttributes() ?>><?php echo $Page->sm_kredit->ListViewValue() ?></span></td>
<?php } ?>
	</tr>
<?php

		// Accumulate page summary
		$Page->AccumulateSummary();

		// Get next record
		$Page->GetRow(2);

		// Show Footers
?>
<?php
	} // End detail records loop
?>
<?php
		if ($Page->no_nama_akun->Visible) {
?>
<?php
			$Page->no_nama_akun->Count = $Page->GetSummaryCount(1, FALSE);
			$Page->debet->Count = $Page->Cnt[1][4];
			$Page->debet->SumValue = $Page->Smry[1][4]; // Load SUM
			$Page->kredit->Count = $Page->Cnt[1][5];
			$Page->kredit->SumValue = $Page->Smry[1][5]; // Load SUM
			$Page->ResetAttrs();
			$Page->RowType = EWR_ROWTYPE_TOTAL;
			$Page->RowTotalType = EWR_ROWTOTAL_GROUP;
			$Page->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
			$Page->RowGroupLevel = 1;
			$Page->RenderRow();
?>
<?php if ($Page->no_nama_akun->ShowCompactSummaryFooter) { ?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->no_nama_akun->Visible) { ?>
		<td data-field="no_nama_akun"<?php echo $Page->no_nama_akun->CellAttributes() ?>>
	<?php if ($Page->no_nama_akun->ShowGroupHeaderAsRow) { ?>
		&nbsp;
	<?php } elseif ($Page->RowGroupLevel <> 1) { ?>
		&nbsp;
	<?php } else { ?>
		<span class="ewSummaryCount"><span class="ewAggregateCaption"><?php echo $ReportLanguage->Phrase("RptCnt") ?></span><?php echo $ReportLanguage->Phrase("AggregateEqual") ?><span class="ewAggregateValue"><?php echo ewr_FormatNumber($Page->no_nama_akun->Count,0,-2,-2,-2) ?></span></span>
	<?php } ?>
		</td>
<?php } ?>
<?php if ($Page->tgl->Visible) { ?>
		<td data-field="tgl"<?php echo $Page->no_nama_akun->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->no_bukti->Visible) { ?>
		<td data-field="no_bukti"<?php echo $Page->no_nama_akun->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->ket->Visible) { ?>
		<td data-field="ket"<?php echo $Page->no_nama_akun->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->debet->Visible) { ?>
		<td data-field="debet"<?php echo $Page->no_nama_akun->CellAttributes() ?>><span class="ewAggregateCaption"><?php echo $ReportLanguage->Phrase("RptSum") ?></span><?php echo $ReportLanguage->Phrase("AggregateEqual") ?><span class="ewAggregateValue"><span data-class="tpgs<?php echo $Page->GrpCount ?>_r_bukubesar_debet"<?php echo $Page->debet->ViewAttributes() ?>><?php echo $Page->debet->SumViewValue ?></span></span></td>
<?php } ?>
<?php if ($Page->kredit->Visible) { ?>
		<td data-field="kredit"<?php echo $Page->no_nama_akun->CellAttributes() ?>><span class="ewAggregateCaption"><?php echo $ReportLanguage->Phrase("RptSum") ?></span><?php echo $ReportLanguage->Phrase("AggregateEqual") ?><span class="ewAggregateValue"><span data-class="tpgs<?php echo $Page->GrpCount ?>_r_bukubesar_kredit"<?php echo $Page->kredit->ViewAttributes() ?>><?php echo $Page->kredit->SumViewValue ?></span></span></td>
<?php } ?>
<?php if ($Page->sa_debet->Visible) { ?>
		<td data-field="sa_debet"<?php echo $Page->no_nama_akun->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->sm_debet->Visible) { ?>
		<td data-field="sm_debet"<?php echo $Page->no_nama_akun->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->sa_kredit->Visible) { ?>
		<td data-field="sa_kredit"<?php echo $Page->no_nama_akun->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->sm_kredit->Visible) { ?>
		<td data-field="sm_kredit"<?php echo $Page->no_nama_akun->CellAttributes() ?>></td>
<?php } ?>
	</tr>
<?php } else { ?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->GrpColumnCount + $Page->DtlColumnCount > 0) { ?>
		<td colspan="<?php echo ($Page->GrpColumnCount + $Page->DtlColumnCount) ?>"<?php echo $Page->sm_kredit->CellAttributes() ?>><?php echo str_replace(array("%v", "%c"), array($Page->no_nama_akun->GroupViewValue, $Page->no_nama_akun->FldCaption()), $ReportLanguage->Phrase("RptSumHead")) ?> <span class="ewDirLtr">(<?php echo ewr_FormatNumber($Page->Cnt[1][0],0,-2,-2,-2) ?><?php echo $ReportLanguage->Phrase("RptDtlRec") ?>)</span></td>
<?php } ?>
	</tr>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->GrpColumnCount > 0) { ?>
		<td colspan="<?php echo ($Page->GrpColumnCount - 0) ?>"<?php echo $Page->no_nama_akun->CellAttributes() ?>><?php echo $ReportLanguage->Phrase("RptSum") ?></td>
<?php } ?>
<?php if ($Page->tgl->Visible) { ?>
		<td data-field="tgl"<?php echo $Page->no_nama_akun->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->no_bukti->Visible) { ?>
		<td data-field="no_bukti"<?php echo $Page->no_nama_akun->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->ket->Visible) { ?>
		<td data-field="ket"<?php echo $Page->no_nama_akun->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->debet->Visible) { ?>
		<td data-field="debet"<?php echo $Page->sm_kredit->CellAttributes() ?>>
<span data-class="tpgs<?php echo $Page->GrpCount ?>_r_bukubesar_debet"<?php echo $Page->debet->ViewAttributes() ?>><?php echo $Page->debet->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->kredit->Visible) { ?>
		<td data-field="kredit"<?php echo $Page->sm_kredit->CellAttributes() ?>>
<span data-class="tpgs<?php echo $Page->GrpCount ?>_r_bukubesar_kredit"<?php echo $Page->kredit->ViewAttributes() ?>><?php echo $Page->kredit->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->sa_debet->Visible) { ?>
		<td data-field="sa_debet"<?php echo $Page->no_nama_akun->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->sm_debet->Visible) { ?>
		<td data-field="sm_debet"<?php echo $Page->no_nama_akun->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->sa_kredit->Visible) { ?>
		<td data-field="sa_kredit"<?php echo $Page->no_nama_akun->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->sm_kredit->Visible) { ?>
		<td data-field="sm_kredit"<?php echo $Page->no_nama_akun->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
	</tr>
<?php } ?>
<?php

			// Reset level 1 summary
			$Page->ResetLevelSummary(1);
		} // End show footer check
?>
<?php

	// Next group
	$Page->GetGrpRow(2);

	// Show header if page break
	if ($Page->Export <> "")
		$Page->ShowHeader = ($Page->ExportPageBreakCount == 0) ? FALSE : ($Page->GrpCount % $Page->ExportPageBreakCount == 0);

	// Page_Breaking server event
	if ($Page->ShowHeader)
		$Page->Page_Breaking($Page->ShowHeader, $Page->PageBreakContent);
	$Page->GrpCount++;

	// Handle EOF
	if (!$rsgrp || $rsgrp->EOF)
		$Page->ShowHeader = FALSE;
} // End while
?>
<?php if ($Page->TotalGrps > 0) { ?>
</tbody>
<tfoot>
<?php if (($Page->StopGrp - $Page->StartGrp + 1) <> $Page->TotalGrps) { ?>
<?php
	$Page->debet->Count = $Page->Cnt[0][4];
	$Page->debet->SumValue = $Page->Smry[0][4]; // Load SUM
	$Page->kredit->Count = $Page->Cnt[0][5];
	$Page->kredit->SumValue = $Page->Smry[0][5]; // Load SUM
	$Page->ResetAttrs();
	$Page->RowType = EWR_ROWTYPE_TOTAL;
	$Page->RowTotalType = EWR_ROWTOTAL_PAGE;
	$Page->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
	$Page->RowAttrs["class"] = "ewRptPageSummary";
	$Page->RenderRow();
?>
<?php if ($Page->no_nama_akun->ShowCompactSummaryFooter) { ?>
	<tr<?php echo $Page->RowAttributes(); ?>><td colspan="<?php echo ($Page->GrpColumnCount + $Page->DtlColumnCount) ?>"><?php echo $ReportLanguage->Phrase("RptPageSummary") ?> (<span class="ewAggregateCaption"><?php echo $ReportLanguage->Phrase("RptCnt") ?></span><?php echo $ReportLanguage->Phrase("AggregateEqual") ?><span class="ewAggregateValue"><?php echo ewr_FormatNumber($Page->Cnt[0][0],0,-2,-2,-2) ?></span>)</td></tr>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->GrpColumnCount > 0) { ?>
		<td colspan="<?php echo $Page->GrpColumnCount ?>" class="ewRptGrpAggregate">&nbsp;</td>
<?php } ?>
<?php if ($Page->tgl->Visible) { ?>
		<td data-field="tgl"<?php echo $Page->tgl->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->no_bukti->Visible) { ?>
		<td data-field="no_bukti"<?php echo $Page->no_bukti->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->ket->Visible) { ?>
		<td data-field="ket"<?php echo $Page->ket->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->debet->Visible) { ?>
		<td data-field="debet"<?php echo $Page->debet->CellAttributes() ?>><?php echo $ReportLanguage->Phrase("RptSum") ?>=<span data-class="tpps_r_bukubesar_debet"<?php echo $Page->debet->ViewAttributes() ?>><?php echo $Page->debet->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->kredit->Visible) { ?>
		<td data-field="kredit"<?php echo $Page->kredit->CellAttributes() ?>><?php echo $ReportLanguage->Phrase("RptSum") ?>=<span data-class="tpps_r_bukubesar_kredit"<?php echo $Page->kredit->ViewAttributes() ?>><?php echo $Page->kredit->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->sa_debet->Visible) { ?>
		<td data-field="sa_debet"<?php echo $Page->sa_debet->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->sm_debet->Visible) { ?>
		<td data-field="sm_debet"<?php echo $Page->sm_debet->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->sa_kredit->Visible) { ?>
		<td data-field="sa_kredit"<?php echo $Page->sa_kredit->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->sm_kredit->Visible) { ?>
		<td data-field="sm_kredit"<?php echo $Page->sm_kredit->CellAttributes() ?>></td>
<?php } ?>
	</tr>
<?php } else { ?>
	<tr<?php echo $Page->RowAttributes(); ?>><td colspan="<?php echo ($Page->GrpColumnCount + $Page->DtlColumnCount) ?>"><?php echo $ReportLanguage->Phrase("RptPageSummary") ?> <span class="ewDirLtr">(<?php echo ewr_FormatNumber($Page->Cnt[0][0],0,-2,-2,-2); ?><?php echo $ReportLanguage->Phrase("RptDtlRec") ?>)</span></td></tr>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->GrpColumnCount > 0) { ?>
		<td colspan="<?php echo $Page->GrpColumnCount ?>" class="ewRptGrpAggregate"><?php echo $ReportLanguage->Phrase("RptSum") ?></td>
<?php } ?>
<?php if ($Page->tgl->Visible) { ?>
		<td data-field="tgl"<?php echo $Page->tgl->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->no_bukti->Visible) { ?>
		<td data-field="no_bukti"<?php echo $Page->no_bukti->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->ket->Visible) { ?>
		<td data-field="ket"<?php echo $Page->ket->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->debet->Visible) { ?>
		<td data-field="debet"<?php echo $Page->debet->CellAttributes() ?>>
<span data-class="tpps_r_bukubesar_debet"<?php echo $Page->debet->ViewAttributes() ?>><?php echo $Page->debet->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->kredit->Visible) { ?>
		<td data-field="kredit"<?php echo $Page->kredit->CellAttributes() ?>>
<span data-class="tpps_r_bukubesar_kredit"<?php echo $Page->kredit->ViewAttributes() ?>><?php echo $Page->kredit->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->sa_debet->Visible) { ?>
		<td data-field="sa_debet"<?php echo $Page->sa_debet->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->sm_debet->Visible) { ?>
		<td data-field="sm_debet"<?php echo $Page->sm_debet->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->sa_kredit->Visible) { ?>
		<td data-field="sa_kredit"<?php echo $Page->sa_kredit->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->sm_kredit->Visible) { ?>
		<td data-field="sm_kredit"<?php echo $Page->sm_kredit->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
	</tr>
<?php } ?>
<?php } ?>
<?php
	$Page->debet->Count = $Page->GrandCnt[4];
	$Page->debet->SumValue = $Page->GrandSmry[4]; // Load SUM
	$Page->kredit->Count = $Page->GrandCnt[5];
	$Page->kredit->SumValue = $Page->GrandSmry[5]; // Load SUM
	$Page->ResetAttrs();
	$Page->RowType = EWR_ROWTYPE_TOTAL;
	$Page->RowTotalType = EWR_ROWTOTAL_GRAND;
	$Page->RowTotalSubType = EWR_ROWTOTAL_FOOTER;
	$Page->RowAttrs["class"] = "ewRptGrandSummary";
	$Page->RenderRow();
?>
<?php if ($Page->no_nama_akun->ShowCompactSummaryFooter) { ?>
	<tr<?php echo $Page->RowAttributes() ?>><td colspan="<?php echo ($Page->GrpColumnCount + $Page->DtlColumnCount) ?>"><?php echo $ReportLanguage->Phrase("RptGrandSummary") ?> (<span class="ewAggregateCaption"><?php echo $ReportLanguage->Phrase("RptCnt") ?></span><?php echo $ReportLanguage->Phrase("AggregateEqual") ?><span class="ewAggregateValue"><?php echo ewr_FormatNumber($Page->TotCount,0,-2,-2,-2) ?></span>)</td></tr>
	<tr<?php echo $Page->RowAttributes() ?>>
<?php if ($Page->GrpColumnCount > 0) { ?>
		<td colspan="<?php echo $Page->GrpColumnCount ?>" class="ewRptGrpAggregate">&nbsp;</td>
<?php } ?>
<?php if ($Page->tgl->Visible) { ?>
		<td data-field="tgl"<?php echo $Page->tgl->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->no_bukti->Visible) { ?>
		<td data-field="no_bukti"<?php echo $Page->no_bukti->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->ket->Visible) { ?>
		<td data-field="ket"<?php echo $Page->ket->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->debet->Visible) { ?>
		<td data-field="debet"<?php echo $Page->debet->CellAttributes() ?>><?php echo $ReportLanguage->Phrase("RptSum") ?>=<span data-class="tpts_r_bukubesar_debet"<?php echo $Page->debet->ViewAttributes() ?>><?php echo $Page->debet->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->kredit->Visible) { ?>
		<td data-field="kredit"<?php echo $Page->kredit->CellAttributes() ?>><?php echo $ReportLanguage->Phrase("RptSum") ?>=<span data-class="tpts_r_bukubesar_kredit"<?php echo $Page->kredit->ViewAttributes() ?>><?php echo $Page->kredit->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->sa_debet->Visible) { ?>
		<td data-field="sa_debet"<?php echo $Page->sa_debet->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->sm_debet->Visible) { ?>
		<td data-field="sm_debet"<?php echo $Page->sm_debet->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->sa_kredit->Visible) { ?>
		<td data-field="sa_kredit"<?php echo $Page->sa_kredit->CellAttributes() ?>></td>
<?php } ?>
<?php if ($Page->sm_kredit->Visible) { ?>
		<td data-field="sm_kredit"<?php echo $Page->sm_kredit->CellAttributes() ?>></td>
<?php } ?>
	</tr>
<?php } else { ?>
	<tr<?php echo $Page->RowAttributes() ?>><td colspan="<?php echo ($Page->GrpColumnCount + $Page->DtlColumnCount) ?>"><?php echo $ReportLanguage->Phrase("RptGrandSummary") ?> <span class="ewDirLtr">(<?php echo ewr_FormatNumber($Page->TotCount,0,-2,-2,-2); ?><?php echo $ReportLanguage->Phrase("RptDtlRec") ?>)</span></td></tr>
	<tr<?php echo $Page->RowAttributes() ?>>
<?php if ($Page->GrpColumnCount > 0) { ?>
		<td colspan="<?php echo $Page->GrpColumnCount ?>" class="ewRptGrpAggregate"><?php echo $ReportLanguage->Phrase("RptSum") ?></td>
<?php } ?>
<?php if ($Page->tgl->Visible) { ?>
		<td data-field="tgl"<?php echo $Page->tgl->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->no_bukti->Visible) { ?>
		<td data-field="no_bukti"<?php echo $Page->no_bukti->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->ket->Visible) { ?>
		<td data-field="ket"<?php echo $Page->ket->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->debet->Visible) { ?>
		<td data-field="debet"<?php echo $Page->debet->CellAttributes() ?>>
<span data-class="tpts_r_bukubesar_debet"<?php echo $Page->debet->ViewAttributes() ?>><?php echo $Page->debet->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->kredit->Visible) { ?>
		<td data-field="kredit"<?php echo $Page->kredit->CellAttributes() ?>>
<span data-class="tpts_r_bukubesar_kredit"<?php echo $Page->kredit->ViewAttributes() ?>><?php echo $Page->kredit->SumViewValue ?></span></td>
<?php } ?>
<?php if ($Page->sa_debet->Visible) { ?>
		<td data-field="sa_debet"<?php echo $Page->sa_debet->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->sm_debet->Visible) { ?>
		<td data-field="sm_debet"<?php echo $Page->sm_debet->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->sa_kredit->Visible) { ?>
		<td data-field="sa_kredit"<?php echo $Page->sa_kredit->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
<?php if ($Page->sm_kredit->Visible) { ?>
		<td data-field="sm_kredit"<?php echo $Page->sm_kredit->CellAttributes() ?>>&nbsp;</td>
<?php } ?>
	</tr>
<?php } ?>
	</tfoot>
<?php } elseif (!$Page->ShowHeader && TRUE) { // No header displayed ?>
<?php if ($Page->Export <> "pdf") { ?>
<?php if ($Page->Export == "word" || $Page->Export == "excel") { ?>
<div class="ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } else { ?>
<div class="panel panel-default ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-heading ewGridUpperPanel">
<?php include "r_bukubesarsmrypager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<!-- Report grid (begin) -->
<?php if ($Page->Export <> "pdf") { ?>
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php } ?>
<table class="<?php echo $Page->ReportTableClass ?>">
<?php } ?>
<?php if ($Page->TotalGrps > 0 || TRUE) { // Show footer ?>
</table>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php if ($Page->TotalGrps > 0) { ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php include "r_bukubesarsmrypager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php if ($Page->Export <> "" || $Page->UseCustomTemplate) { ?>
<div id="tpd_r_bukubesarsummary"></div>
<script id="tpm_r_bukubesarsummary" type="text/html">
<div id="ct_r_bukubesar_summary"><table class="table ewTable">
	<tr>
		<th>Account</th>
		<th>Tgl.</th>
		<th>No. Bukti</th>
		<th>Keterangan</th>
		<th>Debet</th>
		<th>Kredit</th>
	</tr>
<?php
$cnt = count($Page->GrpIdx) - 1;
for ($i = 1; $i <= $cnt; $i++) {
?>
<tr>
	<td>{{include tmpl="#tpx<?php echo $i ?>_r_bukubesar_no_nama_akun"/}}</td>
	<td colspan="5">&nbsp;</td>
</tr>
<?php
for ($j = 1; $j <= @$Page->GrpIdx[$i]; $j++) {
?>
<tr>
	<td>&nbsp;</td><td>{{include tmpl="#tpx<?php echo $i ?>_<?php echo $j ?>_r_bukubesar_tgl"/}}</td><td>{{include tmpl="#tpx<?php echo $i ?>_<?php echo $j ?>_r_bukubesar_no_bukti"/}}</td><td>{{include tmpl="#tpx<?php echo $i ?>_<?php echo $j ?>_r_bukubesar_ket"/}}</td><td>{{include tmpl="#tpx<?php echo $i ?>_<?php echo $j ?>_r_bukubesar_debet"/}}</td><td>{{include tmpl="#tpx<?php echo $i ?>_<?php echo $j ?>_r_bukubesar_kredit"/}}</td>
</tr>
<?php
}
?>
<tr>
	<td align="right" colspan="4">Saldo Akhir</td>
	<td>{{include tmpl="#tpgs<?php echo $i ?>_r_bukubesar_debet"/}}</td>
	<td>{{include tmpl="#tpgs<?php echo $i ?>_r_bukubesar_kredit"/}}</td>
</tr>
<?php
if ($r_bukubesar->ExportPageBreakCount > 0) {
if ($i % $r_bukubesar->ExportPageBreakCount == 0 && $i < $cnt) {
?>
{{include tmpl="#tpb<?php echo $i ?>_r_bukubesar"/}}
<?php
}
}
}
?>
</table>
</div>
</script>
<?php } ?>
<!-- Summary Report Ends -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- center container - report (end) -->
	<!-- right container (begin) -->
	<div id="ewRight" class="ewRight">
<?php } ?>
	<!-- Right slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- right container (end) -->
<div class="clearfix"></div>
<!-- bottom container (begin) -->
<div id="ewBottom" class="ewBottom">
<?php } ?>
	<!-- Bottom slot -->
<?php if ($Page->Export == "") { ?>
	</div>
<!-- Bottom Container (End) -->
</div>
<!-- Table Container (End) -->
<?php } ?>
<?php $Page->ShowPageFooter(); ?>
<?php if (EWR_DEBUG_ENABLED) echo ewr_DebugMsg(); ?>
<?php

// Close recordsets
if ($rsgrp) $rsgrp->Close();
if ($rs) $rs->Close();
?>
<?php if ($Page->Export == "" && !$Page->DrillDown || $Page->Export <> "" && $Page->CustomExport <> "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php if ($Page->Export <> "" || $Page->UseCustomTemplate) { ?>
<script type="text/javascript">
ewr_ApplyTemplate("tpd_r_bukubesarsummary", "tpm_r_bukubesarsummary", "r_bukubesarsummary", "<?php echo $Page->CustomExport ?>", <?php echo ewr_JsonEncode($Page->FirstRowData) ?>);
</script>
<?php } ?>
<?php include_once "phprptinc/footer.php" ?>
<?php include_once "footer.php" ?>
<?php
$Page->Page_Terminate();
if (isset($OldPage)) $Page = $OldPage;
?>
