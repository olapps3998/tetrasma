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

$t_level4_list = NULL; // Initialize page object first

class ct_level4_list extends ct_level4 {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{D8E5AA29-C8A1-46A6-8DFF-08A223163C5D}";

	// Table name
	var $TableName = 't_level4';

	// Page object name
	var $PageObjName = 't_level4_list';

	// Grid form hidden field names
	var $FormName = 'ft_level4list';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Custom export
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "t_level4add.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "t_level4delete.php";
		$this->MultiUpdateUrl = "t_level4update.php";

		// Table object (t_user)
		if (!isset($GLOBALS['t_user'])) $GLOBALS['t_user'] = new ct_user();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

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

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "div";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";

		// Filter options
		$this->FilterOptions = new cListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption ft_level4listsrch";

		// List actions
		$this->ListActions = new cListActions();
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
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage(ew_DeniedMsg()); // Set no permission
			$this->Page_Terminate(ew_GetUrl("index.php"));
		}
		if ($Security->IsLoggedIn()) {
			$Security->UserID_Loading();
			$Security->LoadUserID();
			$Security->UserID_Loaded();
		}

		// Create form object
		$objForm = new cFormObj();

		// Get export parameters
		$custom = "";
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
			$custom = @$_GET["custom"];
		} elseif (@$_POST["export"] <> "") {
			$this->Export = $_POST["export"];
			$custom = @$_POST["custom"];
		} elseif (ew_IsHttpPost()) {
			if (@$_POST["exporttype"] <> "")
				$this->Export = $_POST["exporttype"];
			$custom = @$_POST["custom"];
		} else {
			$this->setExportReturnUrl(ew_CurrentUrl());
		}
		$gsExportFile = $this->TableVar; // Get export file, used in header

		// Get custom export parameters
		if ($this->Export <> "" && $custom <> "") {
			$this->CustomExport = $this->Export;
			$this->Export = "print";
		}
		$gsCustomExport = $this->CustomExport;
		$gsExport = $this->Export; // Get export parameter, used in header

		// Update Export URLs
		if (defined("EW_USE_PHPEXCEL"))
			$this->ExportExcelCustom = FALSE;
		if ($this->ExportExcelCustom)
			$this->ExportExcelUrl .= "&amp;custom=1";
		if (defined("EW_USE_PHPWORD"))
			$this->ExportWordCustom = FALSE;
		if ($this->ExportWordCustom)
			$this->ExportWordUrl .= "&amp;custom=1";
		if ($this->ExportPdfCustom)
			$this->ExportPdfUrl .= "&amp;custom=1";
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

		// Setup export options
		$this->SetupExportOptions();
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

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
		}

		// Create Token
		$this->CreateToken();

		// Setup other options
		$this->SetupOtherOptions();

		// Set up custom action (compatible with old version)
		foreach ($this->CustomActions as $name => $action)
			$this->ListActions->Add($name, $action);

		// Show checkbox column if multiple action
		foreach ($this->ListActions->Items as $listaction) {
			if ($listaction->Select == EW_ACTION_MULTIPLE && $listaction->Allow) {
				$this->ListOptions->Items["checkbox"]->Visible = TRUE;
				break;
			}
		}
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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $OtherOptions = array(); // Other options
	var $FilterOptions; // Filter options
	var $ListActions; // List actions
	var $SelectedCount = 0;
	var $SelectedIndex = 0;
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $DefaultSearchWhere = ""; // Default search WHERE clause
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $MultiColumnClass;
	var $MultiColumnEditClass = "col-sm-12";
	var $MultiColumnCnt = 12;
	var $MultiColumnEditCnt = 12;
	var $GridCnt = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $DetailPages;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process list action first
			if ($this->ProcessListAction()) // Ajax request
				$this->Page_Terminate();

			// Set up records per page
			$this->SetUpDisplayRecs();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			if ($this->Export == "")
				$this->SetupBreadcrumb();

			// Check QueryString parameters
			if (@$_GET["a"] <> "") {
				$this->CurrentAction = $_GET["a"];

				// Clear inline mode
				if ($this->CurrentAction == "cancel")
					$this->ClearInlineMode();

				// Switch to grid edit mode
				if ($this->CurrentAction == "gridedit")
					$this->GridEditMode();

				// Switch to inline edit mode
				if ($this->CurrentAction == "edit")
					$this->InlineEditMode();

				// Switch to inline add mode
				if ($this->CurrentAction == "add" || $this->CurrentAction == "copy")
					$this->InlineAddMode();

				// Switch to grid add mode
				if ($this->CurrentAction == "gridadd")
					$this->GridAddMode();
			} else {
				if (@$_POST["a_list"] <> "") {
					$this->CurrentAction = $_POST["a_list"]; // Get action

					// Grid Update
					if (($this->CurrentAction == "gridupdate" || $this->CurrentAction == "gridoverwrite") && @$_SESSION[EW_SESSION_INLINE_MODE] == "gridedit") {
						if ($this->ValidateGridForm()) {
							$bGridUpdate = $this->GridUpdate();
						} else {
							$bGridUpdate = FALSE;
							$this->setFailureMessage($gsFormError);
						}
						if (!$bGridUpdate) {
							$this->EventCancelled = TRUE;
							$this->CurrentAction = "gridedit"; // Stay in Grid Edit mode
						}
					}

					// Inline Update
					if (($this->CurrentAction == "update" || $this->CurrentAction == "overwrite") && @$_SESSION[EW_SESSION_INLINE_MODE] == "edit")
						$this->InlineUpdate();

					// Insert Inline
					if ($this->CurrentAction == "insert" && @$_SESSION[EW_SESSION_INLINE_MODE] == "add")
						$this->InlineInsert();

					// Grid Insert
					if ($this->CurrentAction == "gridinsert" && @$_SESSION[EW_SESSION_INLINE_MODE] == "gridadd") {
						if ($this->ValidateGridForm()) {
							$bGridInsert = $this->GridInsert();
						} else {
							$bGridInsert = FALSE;
							$this->setFailureMessage($gsFormError);
						}
						if (!$bGridInsert) {
							$this->EventCancelled = TRUE;
							$this->CurrentAction = "gridadd"; // Stay in Grid Add mode
						}
					}
				}
			}

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide options
			if ($this->Export <> "" || $this->CurrentAction <> "") {
				$this->ExportOptions->HideAllOptions();
				$this->FilterOptions->HideAllOptions();
			}

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Show grid delete link for grid add / grid edit
			if ($this->AllowAddDeleteRow) {
				if ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
					$item = $this->ListOptions->GetItem("griddelete");
					if ($item) $item->Visible = TRUE;
				}
			}

			// Get default search criteria
			ew_AddFilter($this->DefaultSearchWhere, $this->BasicSearchWhere(TRUE));

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Process filter list
			$this->ProcessFilterList();

			// Restore search parms from Session if not searching / reset / export
			if (($this->Export <> "" || $this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall") && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Export data only
		if ($this->CustomExport == "" && in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
			$this->ExportData();
			$this->Page_Terminate(); // Terminate response
			exit();
		}

		// Load record count first
		if (!$this->IsAddOrEdit()) {
			$bSelectLimit = $this->UseSelectLimit;
			if ($bSelectLimit) {
				$this->TotalRecs = $this->SelectRecordCount();
			} else {
				if ($this->Recordset = $this->LoadRecordset())
					$this->TotalRecs = $this->Recordset->RecordCount();
			}
		}

		// Search options
		$this->SetupSearchOptions();
	}

	// Set up number of records displayed per page
	function SetUpDisplayRecs() {
		$sWrk = @$_GET[EW_TABLE_REC_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayRecs = intval($sWrk);
			} else {
				if (strtolower($sWrk) == "all") { // Display all records
					$this->DisplayRecs = -1;
				} else {
					$this->DisplayRecs = 20; // Non-numeric, load default
				}
			}
			$this->setRecordsPerPage($this->DisplayRecs); // Save to Session

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	//  Exit inline mode
	function ClearInlineMode() {
		$this->setKey("level4_id", ""); // Clear inline edit key
		$this->sa_debet->FormValue = ""; // Clear form value
		$this->sa_kredit->FormValue = ""; // Clear form value
		$this->LastAction = $this->CurrentAction; // Save last action
		$this->CurrentAction = ""; // Clear action
		$_SESSION[EW_SESSION_INLINE_MODE] = ""; // Clear inline mode
	}

	// Switch to Grid Add mode
	function GridAddMode() {
		$_SESSION[EW_SESSION_INLINE_MODE] = "gridadd"; // Enabled grid add
	}

	// Switch to Grid Edit mode
	function GridEditMode() {
		$_SESSION[EW_SESSION_INLINE_MODE] = "gridedit"; // Enable grid edit
	}

	// Switch to Inline Edit mode
	function InlineEditMode() {
		global $Security, $Language;
		if (!$Security->CanEdit())
			$this->Page_Terminate("login.php"); // Go to login page
		$bInlineEdit = TRUE;
		if (@$_GET["level4_id"] <> "") {
			$this->level4_id->setQueryStringValue($_GET["level4_id"]);
		} else {
			$bInlineEdit = FALSE;
		}
		if ($bInlineEdit) {
			if ($this->LoadRow()) {
				$this->setKey("level4_id", $this->level4_id->CurrentValue); // Set up inline edit key
				$_SESSION[EW_SESSION_INLINE_MODE] = "edit"; // Enable inline edit
			}
		}
	}

	// Perform update to Inline Edit record
	function InlineUpdate() {
		global $Language, $objForm, $gsFormError;
		$objForm->Index = 1; 
		$this->LoadFormValues(); // Get form values

		// Validate form
		$bInlineUpdate = TRUE;
		if (!$this->ValidateForm()) {	
			$bInlineUpdate = FALSE; // Form error, reset action
			$this->setFailureMessage($gsFormError);
		} else {
			$bInlineUpdate = FALSE;
			$rowkey = strval($objForm->GetValue($this->FormKeyName));
			if ($this->SetupKeyValues($rowkey)) { // Set up key values
				if ($this->CheckInlineEditKey()) { // Check key
					$this->SendEmail = TRUE; // Send email on update success
					$bInlineUpdate = $this->EditRow(); // Update record
				} else {
					$bInlineUpdate = FALSE;
				}
			}
		}
		if ($bInlineUpdate) { // Update success
			if ($this->getSuccessMessage() == "")
				$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Set up success message
			$this->ClearInlineMode(); // Clear inline edit mode
		} else {
			if ($this->getFailureMessage() == "")
				$this->setFailureMessage($Language->Phrase("UpdateFailed")); // Set update failed message
			$this->EventCancelled = TRUE; // Cancel event
			$this->CurrentAction = "edit"; // Stay in edit mode
		}
	}

	// Check Inline Edit key
	function CheckInlineEditKey() {

		//CheckInlineEditKey = True
		if (strval($this->getKey("level4_id")) <> strval($this->level4_id->CurrentValue))
			return FALSE;
		return TRUE;
	}

	// Switch to Inline Add mode
	function InlineAddMode() {
		global $Security, $Language;
		if (!$Security->CanAdd())
			$this->Page_Terminate("login.php"); // Return to login page
		if ($this->CurrentAction == "copy") {
			if (@$_GET["level4_id"] <> "") {
				$this->level4_id->setQueryStringValue($_GET["level4_id"]);
				$this->setKey("level4_id", $this->level4_id->CurrentValue); // Set up key
			} else {
				$this->setKey("level4_id", ""); // Clear key
				$this->CurrentAction = "add";
			}
		}
		$_SESSION[EW_SESSION_INLINE_MODE] = "add"; // Enable inline add
	}

	// Perform update to Inline Add/Copy record
	function InlineInsert() {
		global $Language, $objForm, $gsFormError;
		$this->LoadOldRecord(); // Load old recordset
		$objForm->Index = 0;
		$this->LoadFormValues(); // Get form values

		// Validate form
		if (!$this->ValidateForm()) {
			$this->setFailureMessage($gsFormError); // Set validation error message
			$this->EventCancelled = TRUE; // Set event cancelled
			$this->CurrentAction = "add"; // Stay in add mode
			return;
		}
		$this->SendEmail = TRUE; // Send email on add success
		if ($this->AddRow($this->OldRecordset)) { // Add record
			if ($this->getSuccessMessage() == "")
				$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up add success message
			$this->ClearInlineMode(); // Clear inline add mode
		} else { // Add failed
			$this->EventCancelled = TRUE; // Set event cancelled
			$this->CurrentAction = "add"; // Stay in add mode
		}
	}

	// Perform update to grid
	function GridUpdate() {
		global $Language, $objForm, $gsFormError;
		$bGridUpdate = TRUE;

		// Get old recordset
		$this->CurrentFilter = $this->BuildKeyFilter();
		if ($this->CurrentFilter == "")
			$this->CurrentFilter = "0=1";
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		if ($rs = $conn->Execute($sSql)) {
			$rsold = $rs->GetRows();
			$rs->Close();
		}

		// Call Grid Updating event
		if (!$this->Grid_Updating($rsold)) {
			if ($this->getFailureMessage() == "")
				$this->setFailureMessage($Language->Phrase("GridEditCancelled")); // Set grid edit cancelled message
			return FALSE;
		}

		// Begin transaction
		$conn->BeginTrans();
		if ($this->AuditTrailOnEdit) $this->WriteAuditTrailDummy($Language->Phrase("BatchUpdateBegin")); // Batch update begin
		$sKey = "";

		// Update row index and get row key
		$objForm->Index = -1;
		$rowcnt = strval($objForm->GetValue($this->FormKeyCountName));
		if ($rowcnt == "" || !is_numeric($rowcnt))
			$rowcnt = 0;

		// Update all rows based on key
		for ($rowindex = 1; $rowindex <= $rowcnt; $rowindex++) {
			$objForm->Index = $rowindex;
			$rowkey = strval($objForm->GetValue($this->FormKeyName));
			$rowaction = strval($objForm->GetValue($this->FormActionName));

			// Load all values and keys
			if ($rowaction <> "insertdelete") { // Skip insert then deleted rows
				$this->LoadFormValues(); // Get form values
				if ($rowaction == "" || $rowaction == "edit" || $rowaction == "delete") {
					$bGridUpdate = $this->SetupKeyValues($rowkey); // Set up key values
				} else {
					$bGridUpdate = TRUE;
				}

				// Skip empty row
				if ($rowaction == "insert" && $this->EmptyRow()) {

					// No action required
				// Validate form and insert/update/delete record

				} elseif ($bGridUpdate) {
					if ($rowaction == "delete") {
						$this->CurrentFilter = $this->KeyFilter();
						$bGridUpdate = $this->DeleteRows(); // Delete this row
					} else if (!$this->ValidateForm()) {
						$bGridUpdate = FALSE; // Form error, reset action
						$this->setFailureMessage($gsFormError);
					} else {
						if ($rowaction == "insert") {
							$bGridUpdate = $this->AddRow(); // Insert this row
						} else {
							if ($rowkey <> "") {
								$this->SendEmail = FALSE; // Do not send email on update success
								$bGridUpdate = $this->EditRow(); // Update this row
							}
						} // End update
					}
				}
				if ($bGridUpdate) {
					if ($sKey <> "") $sKey .= ", ";
					$sKey .= $rowkey;
				} else {
					break;
				}
			}
		}
		if ($bGridUpdate) {
			$conn->CommitTrans(); // Commit transaction

			// Get new recordset
			if ($rs = $conn->Execute($sSql)) {
				$rsnew = $rs->GetRows();
				$rs->Close();
			}

			// Call Grid_Updated event
			$this->Grid_Updated($rsold, $rsnew);
			if ($this->AuditTrailOnEdit) $this->WriteAuditTrailDummy($Language->Phrase("BatchUpdateSuccess")); // Batch update success
			if ($this->getSuccessMessage() == "")
				$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Set up update success message
			$this->ClearInlineMode(); // Clear inline edit mode
		} else {
			$conn->RollbackTrans(); // Rollback transaction
			if ($this->AuditTrailOnEdit) $this->WriteAuditTrailDummy($Language->Phrase("BatchUpdateRollback")); // Batch update rollback
			if ($this->getFailureMessage() == "")
				$this->setFailureMessage($Language->Phrase("UpdateFailed")); // Set update failed message
		}
		return $bGridUpdate;
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->level4_id->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->level4_id->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Perform Grid Add
	function GridInsert() {
		global $Language, $objForm, $gsFormError;
		$rowindex = 1;
		$bGridInsert = FALSE;
		$conn = &$this->Connection();

		// Call Grid Inserting event
		if (!$this->Grid_Inserting()) {
			if ($this->getFailureMessage() == "") {
				$this->setFailureMessage($Language->Phrase("GridAddCancelled")); // Set grid add cancelled message
			}
			return FALSE;
		}

		// Begin transaction
		$conn->BeginTrans();

		// Init key filter
		$sWrkFilter = "";
		$addcnt = 0;
		if ($this->AuditTrailOnAdd) $this->WriteAuditTrailDummy($Language->Phrase("BatchInsertBegin")); // Batch insert begin
		$sKey = "";

		// Get row count
		$objForm->Index = -1;
		$rowcnt = strval($objForm->GetValue($this->FormKeyCountName));
		if ($rowcnt == "" || !is_numeric($rowcnt))
			$rowcnt = 0;

		// Insert all rows
		for ($rowindex = 1; $rowindex <= $rowcnt; $rowindex++) {

			// Load current row values
			$objForm->Index = $rowindex;
			$rowaction = strval($objForm->GetValue($this->FormActionName));
			if ($rowaction <> "" && $rowaction <> "insert")
				continue; // Skip
			$this->LoadFormValues(); // Get form values
			if (!$this->EmptyRow()) {
				$addcnt++;
				$this->SendEmail = FALSE; // Do not send email on insert success

				// Validate form
				if (!$this->ValidateForm()) {
					$bGridInsert = FALSE; // Form error, reset action
					$this->setFailureMessage($gsFormError);
				} else {
					$bGridInsert = $this->AddRow($this->OldRecordset); // Insert this row
				}
				if ($bGridInsert) {
					if ($sKey <> "") $sKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
					$sKey .= $this->level4_id->CurrentValue;

					// Add filter for this record
					$sFilter = $this->KeyFilter();
					if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
					$sWrkFilter .= $sFilter;
				} else {
					break;
				}
			}
		}
		if ($addcnt == 0) { // No record inserted
			$this->setFailureMessage($Language->Phrase("NoAddRecord"));
			$bGridInsert = FALSE;
		}
		if ($bGridInsert) {
			$conn->CommitTrans(); // Commit transaction

			// Get new recordset
			$this->CurrentFilter = $sWrkFilter;
			$sSql = $this->SQL();
			if ($rs = $conn->Execute($sSql)) {
				$rsnew = $rs->GetRows();
				$rs->Close();
			}

			// Call Grid_Inserted event
			$this->Grid_Inserted($rsnew);
			if ($this->AuditTrailOnAdd) $this->WriteAuditTrailDummy($Language->Phrase("BatchInsertSuccess")); // Batch insert success
			if ($this->getSuccessMessage() == "")
				$this->setSuccessMessage($Language->Phrase("InsertSuccess")); // Set up insert success message
			$this->ClearInlineMode(); // Clear grid add mode
		} else {
			$conn->RollbackTrans(); // Rollback transaction
			if ($this->AuditTrailOnAdd) $this->WriteAuditTrailDummy($Language->Phrase("BatchInsertRollback")); // Batch insert rollback
			if ($this->getFailureMessage() == "") {
				$this->setFailureMessage($Language->Phrase("InsertFailed")); // Set insert failed message
			}
		}
		return $bGridInsert;
	}

	// Check if empty row
	function EmptyRow() {
		global $objForm;
		if ($objForm->HasValue("x_level1_id") && $objForm->HasValue("o_level1_id") && $this->level1_id->CurrentValue <> $this->level1_id->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_level2_id") && $objForm->HasValue("o_level2_id") && $this->level2_id->CurrentValue <> $this->level2_id->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_level3_id") && $objForm->HasValue("o_level3_id") && $this->level3_id->CurrentValue <> $this->level3_id->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_level4_no") && $objForm->HasValue("o_level4_no") && $this->level4_no->CurrentValue <> $this->level4_no->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_level4_nama") && $objForm->HasValue("o_level4_nama") && $this->level4_nama->CurrentValue <> $this->level4_nama->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_sa_debet") && $objForm->HasValue("o_sa_debet") && $this->sa_debet->CurrentValue <> $this->sa_debet->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_sa_kredit") && $objForm->HasValue("o_sa_kredit") && $this->sa_kredit->CurrentValue <> $this->sa_kredit->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_jurnal") && $objForm->HasValue("o_jurnal") && $this->jurnal->CurrentValue <> $this->jurnal->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_jurnal_kode") && $objForm->HasValue("o_jurnal_kode") && $this->jurnal_kode->CurrentValue <> $this->jurnal_kode->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_neraca") && $objForm->HasValue("o_neraca") && $this->neraca->CurrentValue <> $this->neraca->OldValue)
			return FALSE;
		if ($objForm->HasValue("x_labarugi") && $objForm->HasValue("o_labarugi") && $this->labarugi->CurrentValue <> $this->labarugi->OldValue)
			return FALSE;
		return TRUE;
	}

	// Validate grid form
	function ValidateGridForm() {
		global $objForm;

		// Get row count
		$objForm->Index = -1;
		$rowcnt = strval($objForm->GetValue($this->FormKeyCountName));
		if ($rowcnt == "" || !is_numeric($rowcnt))
			$rowcnt = 0;

		// Validate all records
		for ($rowindex = 1; $rowindex <= $rowcnt; $rowindex++) {

			// Load current row values
			$objForm->Index = $rowindex;
			$rowaction = strval($objForm->GetValue($this->FormActionName));
			if ($rowaction <> "delete" && $rowaction <> "insertdelete") {
				$this->LoadFormValues(); // Get form values
				if ($rowaction == "insert" && $this->EmptyRow()) {

					// Ignore
				} else if (!$this->ValidateForm()) {
					return FALSE;
				}
			}
		}
		return TRUE;
	}

	// Get all form values of the grid
	function GetGridFormValues() {
		global $objForm;

		// Get row count
		$objForm->Index = -1;
		$rowcnt = strval($objForm->GetValue($this->FormKeyCountName));
		if ($rowcnt == "" || !is_numeric($rowcnt))
			$rowcnt = 0;
		$rows = array();

		// Loop through all records
		for ($rowindex = 1; $rowindex <= $rowcnt; $rowindex++) {

			// Load current row values
			$objForm->Index = $rowindex;
			$rowaction = strval($objForm->GetValue($this->FormActionName));
			if ($rowaction <> "delete" && $rowaction <> "insertdelete") {
				$this->LoadFormValues(); // Get form values
				if ($rowaction == "insert" && $this->EmptyRow()) {

					// Ignore
				} else {
					$rows[] = $this->GetFieldValues("FormValue"); // Return row as array
				}
			}
		}
		return $rows; // Return as array of array
	}

	// Restore form values for current row
	function RestoreCurrentRowFormValues($idx) {
		global $objForm;

		// Get row based on current index
		$objForm->Index = $idx;
		$this->LoadFormValues(); // Load form values
	}

	// Get list of filters
	function GetFilterList() {
		global $UserProfile;

		// Load server side filters
		if (EW_SEARCH_FILTER_OPTION == "Server") {
			$sSavedFilterList = $UserProfile->GetSearchFilters(CurrentUserName(), "ft_level4listsrch");
		} else {
			$sSavedFilterList = "";
		}

		// Initialize
		$sFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->level4_id->AdvancedSearch->ToJSON(), ","); // Field level4_id
		$sFilterList = ew_Concat($sFilterList, $this->level1_id->AdvancedSearch->ToJSON(), ","); // Field level1_id
		$sFilterList = ew_Concat($sFilterList, $this->level2_id->AdvancedSearch->ToJSON(), ","); // Field level2_id
		$sFilterList = ew_Concat($sFilterList, $this->level3_id->AdvancedSearch->ToJSON(), ","); // Field level3_id
		$sFilterList = ew_Concat($sFilterList, $this->level4_no->AdvancedSearch->ToJSON(), ","); // Field level4_no
		$sFilterList = ew_Concat($sFilterList, $this->level4_nama->AdvancedSearch->ToJSON(), ","); // Field level4_nama
		$sFilterList = ew_Concat($sFilterList, $this->sa_debet->AdvancedSearch->ToJSON(), ","); // Field sa_debet
		$sFilterList = ew_Concat($sFilterList, $this->sa_kredit->AdvancedSearch->ToJSON(), ","); // Field sa_kredit
		$sFilterList = ew_Concat($sFilterList, $this->jurnal->AdvancedSearch->ToJSON(), ","); // Field jurnal
		$sFilterList = ew_Concat($sFilterList, $this->jurnal_kode->AdvancedSearch->ToJSON(), ","); // Field jurnal_kode
		$sFilterList = ew_Concat($sFilterList, $this->neraca->AdvancedSearch->ToJSON(), ","); // Field neraca
		$sFilterList = ew_Concat($sFilterList, $this->labarugi->AdvancedSearch->ToJSON(), ","); // Field labarugi
		if ($this->BasicSearch->Keyword <> "") {
			$sWrk = "\"" . EW_TABLE_BASIC_SEARCH . "\":\"" . ew_JsEncode2($this->BasicSearch->Keyword) . "\",\"" . EW_TABLE_BASIC_SEARCH_TYPE . "\":\"" . ew_JsEncode2($this->BasicSearch->Type) . "\"";
			$sFilterList = ew_Concat($sFilterList, $sWrk, ",");
		}
		$sFilterList = preg_replace('/,$/', "", $sFilterList);

		// Return filter list in json
		if ($sFilterList <> "")
			$sFilterList = "\"data\":{" . $sFilterList . "}";
		if ($sSavedFilterList <> "") {
			if ($sFilterList <> "")
				$sFilterList .= ",";
			$sFilterList .= "\"filters\":" . $sSavedFilterList;
		}
		return ($sFilterList <> "") ? "{" . $sFilterList . "}" : "null";
	}

	// Process filter list
	function ProcessFilterList() {
		global $UserProfile;
		if (@$_POST["ajax"] == "savefilters") { // Save filter request (Ajax)
			$filters = ew_StripSlashes(@$_POST["filters"]);
			$UserProfile->SetSearchFilters(CurrentUserName(), "ft_level4listsrch", $filters);

			// Clean output buffer
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			echo ew_ArrayToJson(array(array("success" => TRUE))); // Success
			$this->Page_Terminate();
			exit();
		} elseif (@$_POST["cmd"] == "resetfilter") {
			$this->RestoreFilterList();
		}
	}

	// Restore list of filters
	function RestoreFilterList() {

		// Return if not reset filter
		if (@$_POST["cmd"] <> "resetfilter")
			return FALSE;
		$filter = json_decode(ew_StripSlashes(@$_POST["filter"]), TRUE);
		$this->Command = "search";

		// Field level4_id
		$this->level4_id->AdvancedSearch->SearchValue = @$filter["x_level4_id"];
		$this->level4_id->AdvancedSearch->SearchOperator = @$filter["z_level4_id"];
		$this->level4_id->AdvancedSearch->SearchCondition = @$filter["v_level4_id"];
		$this->level4_id->AdvancedSearch->SearchValue2 = @$filter["y_level4_id"];
		$this->level4_id->AdvancedSearch->SearchOperator2 = @$filter["w_level4_id"];
		$this->level4_id->AdvancedSearch->Save();

		// Field level1_id
		$this->level1_id->AdvancedSearch->SearchValue = @$filter["x_level1_id"];
		$this->level1_id->AdvancedSearch->SearchOperator = @$filter["z_level1_id"];
		$this->level1_id->AdvancedSearch->SearchCondition = @$filter["v_level1_id"];
		$this->level1_id->AdvancedSearch->SearchValue2 = @$filter["y_level1_id"];
		$this->level1_id->AdvancedSearch->SearchOperator2 = @$filter["w_level1_id"];
		$this->level1_id->AdvancedSearch->Save();

		// Field level2_id
		$this->level2_id->AdvancedSearch->SearchValue = @$filter["x_level2_id"];
		$this->level2_id->AdvancedSearch->SearchOperator = @$filter["z_level2_id"];
		$this->level2_id->AdvancedSearch->SearchCondition = @$filter["v_level2_id"];
		$this->level2_id->AdvancedSearch->SearchValue2 = @$filter["y_level2_id"];
		$this->level2_id->AdvancedSearch->SearchOperator2 = @$filter["w_level2_id"];
		$this->level2_id->AdvancedSearch->Save();

		// Field level3_id
		$this->level3_id->AdvancedSearch->SearchValue = @$filter["x_level3_id"];
		$this->level3_id->AdvancedSearch->SearchOperator = @$filter["z_level3_id"];
		$this->level3_id->AdvancedSearch->SearchCondition = @$filter["v_level3_id"];
		$this->level3_id->AdvancedSearch->SearchValue2 = @$filter["y_level3_id"];
		$this->level3_id->AdvancedSearch->SearchOperator2 = @$filter["w_level3_id"];
		$this->level3_id->AdvancedSearch->Save();

		// Field level4_no
		$this->level4_no->AdvancedSearch->SearchValue = @$filter["x_level4_no"];
		$this->level4_no->AdvancedSearch->SearchOperator = @$filter["z_level4_no"];
		$this->level4_no->AdvancedSearch->SearchCondition = @$filter["v_level4_no"];
		$this->level4_no->AdvancedSearch->SearchValue2 = @$filter["y_level4_no"];
		$this->level4_no->AdvancedSearch->SearchOperator2 = @$filter["w_level4_no"];
		$this->level4_no->AdvancedSearch->Save();

		// Field level4_nama
		$this->level4_nama->AdvancedSearch->SearchValue = @$filter["x_level4_nama"];
		$this->level4_nama->AdvancedSearch->SearchOperator = @$filter["z_level4_nama"];
		$this->level4_nama->AdvancedSearch->SearchCondition = @$filter["v_level4_nama"];
		$this->level4_nama->AdvancedSearch->SearchValue2 = @$filter["y_level4_nama"];
		$this->level4_nama->AdvancedSearch->SearchOperator2 = @$filter["w_level4_nama"];
		$this->level4_nama->AdvancedSearch->Save();

		// Field sa_debet
		$this->sa_debet->AdvancedSearch->SearchValue = @$filter["x_sa_debet"];
		$this->sa_debet->AdvancedSearch->SearchOperator = @$filter["z_sa_debet"];
		$this->sa_debet->AdvancedSearch->SearchCondition = @$filter["v_sa_debet"];
		$this->sa_debet->AdvancedSearch->SearchValue2 = @$filter["y_sa_debet"];
		$this->sa_debet->AdvancedSearch->SearchOperator2 = @$filter["w_sa_debet"];
		$this->sa_debet->AdvancedSearch->Save();

		// Field sa_kredit
		$this->sa_kredit->AdvancedSearch->SearchValue = @$filter["x_sa_kredit"];
		$this->sa_kredit->AdvancedSearch->SearchOperator = @$filter["z_sa_kredit"];
		$this->sa_kredit->AdvancedSearch->SearchCondition = @$filter["v_sa_kredit"];
		$this->sa_kredit->AdvancedSearch->SearchValue2 = @$filter["y_sa_kredit"];
		$this->sa_kredit->AdvancedSearch->SearchOperator2 = @$filter["w_sa_kredit"];
		$this->sa_kredit->AdvancedSearch->Save();

		// Field jurnal
		$this->jurnal->AdvancedSearch->SearchValue = @$filter["x_jurnal"];
		$this->jurnal->AdvancedSearch->SearchOperator = @$filter["z_jurnal"];
		$this->jurnal->AdvancedSearch->SearchCondition = @$filter["v_jurnal"];
		$this->jurnal->AdvancedSearch->SearchValue2 = @$filter["y_jurnal"];
		$this->jurnal->AdvancedSearch->SearchOperator2 = @$filter["w_jurnal"];
		$this->jurnal->AdvancedSearch->Save();

		// Field jurnal_kode
		$this->jurnal_kode->AdvancedSearch->SearchValue = @$filter["x_jurnal_kode"];
		$this->jurnal_kode->AdvancedSearch->SearchOperator = @$filter["z_jurnal_kode"];
		$this->jurnal_kode->AdvancedSearch->SearchCondition = @$filter["v_jurnal_kode"];
		$this->jurnal_kode->AdvancedSearch->SearchValue2 = @$filter["y_jurnal_kode"];
		$this->jurnal_kode->AdvancedSearch->SearchOperator2 = @$filter["w_jurnal_kode"];
		$this->jurnal_kode->AdvancedSearch->Save();

		// Field neraca
		$this->neraca->AdvancedSearch->SearchValue = @$filter["x_neraca"];
		$this->neraca->AdvancedSearch->SearchOperator = @$filter["z_neraca"];
		$this->neraca->AdvancedSearch->SearchCondition = @$filter["v_neraca"];
		$this->neraca->AdvancedSearch->SearchValue2 = @$filter["y_neraca"];
		$this->neraca->AdvancedSearch->SearchOperator2 = @$filter["w_neraca"];
		$this->neraca->AdvancedSearch->Save();

		// Field labarugi
		$this->labarugi->AdvancedSearch->SearchValue = @$filter["x_labarugi"];
		$this->labarugi->AdvancedSearch->SearchOperator = @$filter["z_labarugi"];
		$this->labarugi->AdvancedSearch->SearchCondition = @$filter["v_labarugi"];
		$this->labarugi->AdvancedSearch->SearchValue2 = @$filter["y_labarugi"];
		$this->labarugi->AdvancedSearch->SearchOperator2 = @$filter["w_labarugi"];
		$this->labarugi->AdvancedSearch->Save();
		$this->BasicSearch->setKeyword(@$filter[EW_TABLE_BASIC_SEARCH]);
		$this->BasicSearch->setType(@$filter[EW_TABLE_BASIC_SEARCH_TYPE]);
	}

	// Return basic search SQL
	function BasicSearchSQL($arKeywords, $type) {
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->level1_id, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->level2_id, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->level3_id, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->level4_no, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->level4_nama, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->jurnal, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->jurnal_kode, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->neraca, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->labarugi, $arKeywords, $type);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSQL(&$Where, &$Fld, $arKeywords, $type) {
		global $EW_BASIC_SEARCH_IGNORE_PATTERN;
		$sDefCond = ($type == "OR") ? "OR" : "AND";
		$arSQL = array(); // Array for SQL parts
		$arCond = array(); // Array for search conditions
		$cnt = count($arKeywords);
		$j = 0; // Number of SQL parts
		for ($i = 0; $i < $cnt; $i++) {
			$Keyword = $arKeywords[$i];
			$Keyword = trim($Keyword);
			if ($EW_BASIC_SEARCH_IGNORE_PATTERN <> "") {
				$Keyword = preg_replace($EW_BASIC_SEARCH_IGNORE_PATTERN, "\\", $Keyword);
				$ar = explode("\\", $Keyword);
			} else {
				$ar = array($Keyword);
			}
			foreach ($ar as $Keyword) {
				if ($Keyword <> "") {
					$sWrk = "";
					if ($Keyword == "OR" && $type == "") {
						if ($j > 0)
							$arCond[$j-1] = "OR";
					} elseif ($Keyword == EW_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NULL";
					} elseif ($Keyword == EW_NOT_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NOT NULL";
					} elseif ($Fld->FldIsVirtual) {
						$sWrk = $Fld->FldVirtualExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING, $this->DBID), $this->DBID);
					} elseif ($Fld->FldDataType != EW_DATATYPE_NUMBER || is_numeric($Keyword)) {
						$sWrk = $Fld->FldBasicSearchExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING, $this->DBID), $this->DBID);
					}
					if ($sWrk <> "") {
						$arSQL[$j] = $sWrk;
						$arCond[$j] = $sDefCond;
						$j += 1;
					}
				}
			}
		}
		$cnt = count($arSQL);
		$bQuoted = FALSE;
		$sSql = "";
		if ($cnt > 0) {
			for ($i = 0; $i < $cnt-1; $i++) {
				if ($arCond[$i] == "OR") {
					if (!$bQuoted) $sSql .= "(";
					$bQuoted = TRUE;
				}
				$sSql .= $arSQL[$i];
				if ($bQuoted && $arCond[$i] <> "OR") {
					$sSql .= ")";
					$bQuoted = FALSE;
				}
				$sSql .= " " . $arCond[$i] . " ";
			}
			$sSql .= $arSQL[$cnt-1];
			if ($bQuoted)
				$sSql .= ")";
		}
		if ($sSql <> "") {
			if ($Where <> "") $Where .= " OR ";
			$Where .=  "(" . $sSql . ")";
		}
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere($Default = FALSE) {
		global $Security;
		$sSearchStr = "";
		if (!$Security->CanSearch()) return "";
		$sSearchKeyword = ($Default) ? $this->BasicSearch->KeywordDefault : $this->BasicSearch->Keyword;
		$sSearchType = ($Default) ? $this->BasicSearch->TypeDefault : $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				$ar = array();

				// Match quoted keywords (i.e.: "...")
				if (preg_match_all('/"([^"]*)"/i', $sSearch, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						$p = strpos($sSearch, $match[0]);
						$str = substr($sSearch, 0, $p);
						$sSearch = substr($sSearch, $p + strlen($match[0]));
						if (strlen(trim($str)) > 0)
							$ar = array_merge($ar, explode(" ", trim($str)));
						$ar[] = $match[1]; // Save quoted keyword
					}
				}

				// Match individual keywords
				if (strlen(trim($sSearch)) > 0)
					$ar = array_merge($ar, explode(" ", trim($sSearch)));

				// Search keyword in any fields
				if (($sSearchType == "OR" || $sSearchType == "AND") && $this->BasicSearch->BasicSearchAnyFields) {
					foreach ($ar as $sKeyword) {
						if ($sKeyword <> "") {
							if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
							$sSearchStr .= "(" . $this->BasicSearchSQL(array($sKeyword), $sSearchType) . ")";
						}
					}
				} else {
					$sSearchStr = $this->BasicSearchSQL($ar, $sSearchType);
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL(array($sSearch), $sSearchType);
			}
			if (!$Default) $this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->level1_id, $bCtrl); // level1_id
			$this->UpdateSort($this->level2_id, $bCtrl); // level2_id
			$this->UpdateSort($this->level3_id, $bCtrl); // level3_id
			$this->UpdateSort($this->level4_no, $bCtrl); // level4_no
			$this->UpdateSort($this->level4_nama, $bCtrl); // level4_nama
			$this->UpdateSort($this->sa_debet, $bCtrl); // sa_debet
			$this->UpdateSort($this->sa_kredit, $bCtrl); // sa_kredit
			$this->UpdateSort($this->jurnal, $bCtrl); // jurnal
			$this->UpdateSort($this->jurnal_kode, $bCtrl); // jurnal_kode
			$this->UpdateSort($this->neraca, $bCtrl); // neraca
			$this->UpdateSort($this->labarugi, $bCtrl); // labarugi
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->getSqlOrderBy() <> "") {
				$sOrderBy = $this->getSqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
				$this->level1_id->setSort("ASC");
				$this->level2_id->setSort("ASC");
				$this->level3_id->setSort("ASC");
				$this->level4_no->setSort("ASC");
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->setSessionOrderByList($sOrderBy);
				$this->level1_id->setSort("");
				$this->level2_id->setSort("");
				$this->level3_id->setSort("");
				$this->level4_no->setSort("");
				$this->level4_nama->setSort("");
				$this->sa_debet->setSort("");
				$this->sa_kredit->setSort("");
				$this->jurnal->setSort("");
				$this->jurnal_kode->setSort("");
				$this->neraca->setSort("");
				$this->labarugi->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// "griddelete"
		if ($this->AllowAddDeleteRow) {
			$item = &$this->ListOptions->Add("griddelete");
			$item->CssStyle = "white-space: nowrap;";
			$item->OnLeft = TRUE;
			$item->Visible = FALSE; // Default hidden
		}

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = TRUE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanView();
		$item->OnLeft = TRUE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanEdit();
		$item->OnLeft = TRUE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanAdd();
		$item->OnLeft = TRUE;

		// List actions
		$item = &$this->ListOptions->Add("listactions");
		$item->CssStyle = "white-space: nowrap;";
		$item->OnLeft = TRUE;
		$item->Visible = FALSE;
		$item->ShowInButtonGroup = FALSE;
		$item->ShowInDropDown = FALSE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = $Security->CanDelete();
		$item->OnLeft = TRUE;
		$item->Header = "<input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\">";
		$item->MoveTo(0);
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseImageAndText = TRUE;
		$this->ListOptions->UseDropDownButton = TRUE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		if ($this->ListOptions->UseButtonGroup && ew_IsMobile())
			$this->ListOptions->UseDropDownButton = TRUE;
		$this->ListOptions->ButtonClass = "btn-sm"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$this->SetupListOptionsExt();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// Set up row action and key
		if (is_numeric($this->RowIndex) && $this->CurrentMode <> "view") {
			$objForm->Index = $this->RowIndex;
			$ActionName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormActionName);
			$OldKeyName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormOldKeyName);
			$KeyName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormKeyName);
			$BlankRowName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormBlankRowName);
			if ($this->RowAction <> "")
				$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $ActionName . "\" id=\"" . $ActionName . "\" value=\"" . $this->RowAction . "\">";
			if ($this->RowAction == "delete") {
				$rowkey = $objForm->GetValue($this->FormKeyName);
				$this->SetupKeyValues($rowkey);
			}
			if ($this->RowAction == "insert" && $this->CurrentAction == "F" && $this->EmptyRow())
				$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $BlankRowName . "\" id=\"" . $BlankRowName . "\" value=\"1\">";
		}

		// "delete"
		if ($this->AllowAddDeleteRow) {
			if ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$option = &$this->ListOptions;
				$option->UseButtonGroup = TRUE; // Use button group for grid delete button
				$option->UseImageAndText = TRUE; // Use image and text for grid delete button
				$oListOpt = &$option->Items["griddelete"];
				if (!$Security->CanDelete() && is_numeric($this->RowIndex) && ($this->RowAction == "" || $this->RowAction == "edit")) { // Do not allow delete existing record
					$oListOpt->Body = "&nbsp;";
				} else {
					$oListOpt->Body = "<a class=\"ewGridLink ewGridDelete\" title=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" onclick=\"return ew_DeleteGridRow(this, " . $this->RowIndex . ");\">" . $Language->Phrase("DeleteLink") . "</a>";
				}
			}
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if (($this->CurrentAction == "add" || $this->CurrentAction == "copy") && $this->RowType == EW_ROWTYPE_ADD) { // Inline Add/Copy
			$this->ListOptions->CustomItem = "copy"; // Show copy column only
			$cancelurl = $this->AddMasterUrl($this->PageUrl() . "a=cancel");
			$oListOpt->Body = "<div" . (($oListOpt->OnLeft) ? " style=\"text-align: right\"" : "") . ">" .
				"<a class=\"ewGridLink ewInlineInsert\" title=\"" . ew_HtmlTitle($Language->Phrase("InsertLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("InsertLink")) . "\" href=\"\" onclick=\"return ewForms(this).Submit('" . $this->PageName() . "');\">" . $Language->Phrase("InsertLink") . "</a>&nbsp;" .
				"<a class=\"ewGridLink ewInlineCancel\" title=\"" . ew_HtmlTitle($Language->Phrase("CancelLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CancelLink")) . "\" href=\"" . $cancelurl . "\">" . $Language->Phrase("CancelLink") . "</a>" .
				"<input type=\"hidden\" name=\"a_list\" id=\"a_list\" value=\"insert\"></div>";
			return;
		}

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($this->CurrentAction == "edit" && $this->RowType == EW_ROWTYPE_EDIT) { // Inline-Edit
			$this->ListOptions->CustomItem = "edit"; // Show edit column only
			$cancelurl = $this->AddMasterUrl($this->PageUrl() . "a=cancel");
				$oListOpt->Body = "<div" . (($oListOpt->OnLeft) ? " style=\"text-align: right\"" : "") . ">" .
					"<a class=\"ewGridLink ewInlineUpdate\" title=\"" . ew_HtmlTitle($Language->Phrase("UpdateLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("UpdateLink")) . "\" href=\"\" onclick=\"return ewForms(this).Submit('" . ew_GetHashUrl($this->PageName(), $this->PageObjName . "_row_" . $this->RowCnt) . "');\">" . $Language->Phrase("UpdateLink") . "</a>&nbsp;" .
					"<a class=\"ewGridLink ewInlineCancel\" title=\"" . ew_HtmlTitle($Language->Phrase("CancelLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CancelLink")) . "\" href=\"" . $cancelurl . "\">" . $Language->Phrase("CancelLink") . "</a>" .
					"<input type=\"hidden\" name=\"a_list\" id=\"a_list\" value=\"update\"></div>";
			$oListOpt->Body .= "<input type=\"hidden\" name=\"k" . $this->RowIndex . "_key\" id=\"k" . $this->RowIndex . "_key\" value=\"" . ew_HtmlEncode($this->level4_id->CurrentValue) . "\">";
			return;
		}

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		$viewcaption = ew_HtmlTitle($Language->Phrase("ViewLink"));
		if ($Security->CanView()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewView\" title=\"" . $viewcaption . "\" data-caption=\"" . $viewcaption . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		$editcaption = ew_HtmlTitle($Language->Phrase("EditLink"));
		if ($Security->CanEdit()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "</a>";
			$oListOpt->Body .= "<a class=\"ewRowLink ewInlineEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("InlineEditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("InlineEditLink")) . "\" href=\"" . ew_HtmlEncode(ew_GetHashUrl($this->InlineEditUrl, $this->PageObjName . "_row_" . $this->RowCnt)) . "\">" . $Language->Phrase("InlineEditLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		$copycaption = ew_HtmlTitle($Language->Phrase("CopyLink"));
		if ($Security->CanAdd()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewCopy\" title=\"" . $copycaption . "\" data-caption=\"" . $copycaption . "\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("CopyLink") . "</a>";
			$oListOpt->Body .= "<a class=\"ewRowLink ewInlineCopy\" title=\"" . ew_HtmlTitle($Language->Phrase("InlineCopyLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("InlineCopyLink")) . "\" href=\"" . ew_HtmlEncode($this->InlineCopyUrl) . "\">" . $Language->Phrase("InlineCopyLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// Set up list action buttons
		$oListOpt = &$this->ListOptions->GetItem("listactions");
		if ($oListOpt && $this->Export == "" && $this->CurrentAction == "") {
			$body = "";
			$links = array();
			foreach ($this->ListActions->Items as $listaction) {
				if ($listaction->Select == EW_ACTION_SINGLE && $listaction->Allow) {
					$action = $listaction->Action;
					$caption = $listaction->Caption;
					$icon = ($listaction->Icon <> "") ? "<span class=\"" . ew_HtmlEncode(str_replace(" ewIcon", "", $listaction->Icon)) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\"></span> " : "";
					$links[] = "<li><a class=\"ewAction ewListAction\" data-action=\"" . ew_HtmlEncode($action) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({key:" . $this->KeyToJson() . "}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . $listaction->Caption . "</a></li>";
					if (count($links) == 1) // Single button
						$body = "<a class=\"ewAction ewListAction\" data-action=\"" . ew_HtmlEncode($action) . "\" title=\"" . ew_HtmlTitle($caption) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({key:" . $this->KeyToJson() . "}," . $listaction->ToJson(TRUE) . "));return false;\">" . $Language->Phrase("ListActionButton") . "</a>";
				}
			}
			if (count($links) > 1) { // More than one buttons, use dropdown
				$body = "<button class=\"dropdown-toggle btn btn-default btn-sm ewActions\" title=\"" . ew_HtmlTitle($Language->Phrase("ListActionButton")) . "\" data-toggle=\"dropdown\">" . $Language->Phrase("ListActionButton") . "<b class=\"caret\"></b></button>";
				$content = "";
				foreach ($links as $link)
					$content .= "<li>" . $link . "</li>";
				$body .= "<ul class=\"dropdown-menu" . ($oListOpt->OnLeft ? "" : " dropdown-menu-right") . "\">". $content . "</ul>";
				$body = "<div class=\"btn-group\">" . $body . "</div>";
			}
			if (count($links) > 0) {
				$oListOpt->Body = $body;
				$oListOpt->Visible = TRUE;
			}
		}

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->level4_id->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event);'>";
		if ($this->CurrentAction == "gridedit" && is_numeric($this->RowIndex)) {
			$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $KeyName . "\" id=\"" . $KeyName . "\" value=\"" . $this->level4_id->CurrentValue . "\">";
		}
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["addedit"];

		// Add
		$item = &$option->Add("add");
		$addcaption = ew_HtmlTitle($Language->Phrase("AddLink"));
		$item->Body = "<a class=\"ewAddEdit ewAdd\" title=\"" . $addcaption . "\" data-caption=\"" . $addcaption . "\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("AddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());

		// Inline Add
		$item = &$option->Add("inlineadd");
		$item->Body = "<a class=\"ewAddEdit ewInlineAdd\" title=\"" . ew_HtmlTitle($Language->Phrase("InlineAddLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("InlineAddLink")) . "\" href=\"" . ew_HtmlEncode($this->InlineAddUrl) . "\">" .$Language->Phrase("InlineAddLink") . "</a>";
		$item->Visible = ($this->InlineAddUrl <> "" && $Security->CanAdd());
		$item = &$option->Add("gridadd");
		$item->Body = "<a class=\"ewAddEdit ewGridAdd\" title=\"" . ew_HtmlTitle($Language->Phrase("GridAddLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("GridAddLink")) . "\" href=\"" . ew_HtmlEncode($this->GridAddUrl) . "\">" . $Language->Phrase("GridAddLink") . "</a>";
		$item->Visible = ($this->GridAddUrl <> "" && $Security->CanAdd());

		// Add grid edit
		$option = $options["addedit"];
		$item = &$option->Add("gridedit");
		$item->Body = "<a class=\"ewAddEdit ewGridEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("GridEditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("GridEditLink")) . "\" href=\"" . ew_HtmlEncode($this->GridEditUrl) . "\">" . $Language->Phrase("GridEditLink") . "</a>";
		$item->Visible = ($this->GridEditUrl <> "" && $Security->CanEdit());
		$option = $options["action"];

		// Add multi delete
		$item = &$option->Add("multidelete");
		$item->Body = "<a class=\"ewAction ewMultiDelete\" title=\"" . ew_HtmlTitle($Language->Phrase("DeleteSelectedLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteSelectedLink")) . "\" href=\"\" onclick=\"ew_SubmitAction(event,{f:document.ft_level4list,url:'" . $this->MultiDeleteUrl . "'});return false;\">" . $Language->Phrase("DeleteSelectedLink") . "</a>";
		$item->Visible = ($Security->CanDelete());

		// Set up options default
		foreach ($options as &$option) {
			$option->UseImageAndText = TRUE;
			$option->UseDropDownButton = TRUE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-sm"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"ft_level4listsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"ft_level4listsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton;
		$this->FilterOptions->DropDownButtonPhrase = $Language->Phrase("Filters");

		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		if ($this->CurrentAction <> "gridadd" && $this->CurrentAction <> "gridedit") { // Not grid add/edit mode
			$option = &$options["action"];

			// Set up list action buttons
			foreach ($this->ListActions->Items as $listaction) {
				if ($listaction->Select == EW_ACTION_MULTIPLE) {
					$item = &$option->Add("custom_" . $listaction->Action);
					$caption = $listaction->Caption;
					$icon = ($listaction->Icon <> "") ? "<span class=\"" . ew_HtmlEncode($listaction->Icon) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\"></span> " : $caption;
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.ft_level4list}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
					$item->Visible = $listaction->Allow;
				}
			}

			// Hide grid edit and other options
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$option->HideAllOptions();
			}
		} else { // Grid add/edit mode

			// Hide all options first
			foreach ($options as &$option)
				$option->HideAllOptions();
			if ($this->CurrentAction == "gridadd") {
				if ($this->AllowAddDeleteRow) {

					// Add add blank row
					$option = &$options["addedit"];
					$option->UseDropDownButton = FALSE;
					$option->UseImageAndText = TRUE;
					$item = &$option->Add("addblankrow");
					$item->Body = "<a class=\"ewAddEdit ewAddBlankRow\" title=\"" . ew_HtmlTitle($Language->Phrase("AddBlankRow")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("AddBlankRow")) . "\" href=\"javascript:void(0);\" onclick=\"ew_AddGridRow(this);\">" . $Language->Phrase("AddBlankRow") . "</a>";
					$item->Visible = $Security->CanAdd();
				}
				$option = &$options["action"];
				$option->UseDropDownButton = FALSE;
				$option->UseImageAndText = TRUE;

				// Add grid insert
				$item = &$option->Add("gridinsert");
				$item->Body = "<a class=\"ewAction ewGridInsert\" title=\"" . ew_HtmlTitle($Language->Phrase("GridInsertLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("GridInsertLink")) . "\" href=\"\" onclick=\"return ewForms(this).Submit('" . $this->PageName() . "');\">" . $Language->Phrase("GridInsertLink") . "</a>";

				// Add grid cancel
				$item = &$option->Add("gridcancel");
				$cancelurl = $this->AddMasterUrl($this->PageUrl() . "a=cancel");
				$item->Body = "<a class=\"ewAction ewGridCancel\" title=\"" . ew_HtmlTitle($Language->Phrase("GridCancelLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("GridCancelLink")) . "\" href=\"" . $cancelurl . "\">" . $Language->Phrase("GridCancelLink") . "</a>";
			}
			if ($this->CurrentAction == "gridedit") {
				if ($this->AllowAddDeleteRow) {

					// Add add blank row
					$option = &$options["addedit"];
					$option->UseDropDownButton = FALSE;
					$option->UseImageAndText = TRUE;
					$item = &$option->Add("addblankrow");
					$item->Body = "<a class=\"ewAddEdit ewAddBlankRow\" title=\"" . ew_HtmlTitle($Language->Phrase("AddBlankRow")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("AddBlankRow")) . "\" href=\"javascript:void(0);\" onclick=\"ew_AddGridRow(this);\">" . $Language->Phrase("AddBlankRow") . "</a>";
					$item->Visible = $Security->CanAdd();
				}
				$option = &$options["action"];
				$option->UseDropDownButton = FALSE;
				$option->UseImageAndText = TRUE;
					$item = &$option->Add("gridsave");
					$item->Body = "<a class=\"ewAction ewGridSave\" title=\"" . ew_HtmlTitle($Language->Phrase("GridSaveLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("GridSaveLink")) . "\" href=\"\" onclick=\"return ewForms(this).Submit('" . $this->PageName() . "');\">" . $Language->Phrase("GridSaveLink") . "</a>";
					$item = &$option->Add("gridcancel");
					$cancelurl = $this->AddMasterUrl($this->PageUrl() . "a=cancel");
					$item->Body = "<a class=\"ewAction ewGridCancel\" title=\"" . ew_HtmlTitle($Language->Phrase("GridCancelLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("GridCancelLink")) . "\" href=\"" . $cancelurl . "\">" . $Language->Phrase("GridCancelLink") . "</a>";
			}
		}
	}

	// Process list action
	function ProcessListAction() {
		global $Language, $Security;
		$userlist = "";
		$user = "";
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {

			// Check permission first
			$ActionCaption = $UserAction;
			if (array_key_exists($UserAction, $this->ListActions->Items)) {
				$ActionCaption = $this->ListActions->Items[$UserAction]->Caption;
				if (!$this->ListActions->Items[$UserAction]->Allow) {
					$errmsg = str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionNotAllowed"));
					if (@$_POST["ajax"] == $UserAction) // Ajax
						echo "<p class=\"text-danger\">" . $errmsg . "</p>";
					else
						$this->setFailureMessage($errmsg);
					return FALSE;
				}
			}
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$this->CurrentAction = $UserAction;

			// Call row action event
			if ($rs && !$rs->EOF) {
				$conn->BeginTrans();
				$this->SelectedCount = $rs->RecordCount();
				$this->SelectedIndex = 0;
				while (!$rs->EOF) {
					$this->SelectedIndex++;
					$row = $rs->fields;
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
					$rs->MoveNext();
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionFailed")));
					}
				}
			}
			if ($rs)
				$rs->Close();
			$this->CurrentAction = ""; // Clear action
			if (@$_POST["ajax"] == $UserAction) { // Ajax
				if ($this->getSuccessMessage() <> "") {
					echo "<p class=\"text-success\">" . $this->getSuccessMessage() . "</p>";
					$this->ClearSuccessMessage(); // Clear message
				}
				if ($this->getFailureMessage() <> "") {
					echo "<p class=\"text-danger\">" . $this->getFailureMessage() . "</p>";
					$this->ClearFailureMessage(); // Clear message
				}
				return TRUE;
			}
		}
		return FALSE; // Not ajax request
	}

	// Set up search options
	function SetupSearchOptions() {
		global $Language;
		$this->SearchOptions = new cListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Search button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = ($this->SearchWhere <> "") ? " active" : " active";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"ft_level4listsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Button group for search
		$this->SearchOptions->UseDropDownButton = FALSE;
		$this->SearchOptions->UseImageAndText = TRUE;
		$this->SearchOptions->UseButtonGroup = TRUE;
		$this->SearchOptions->DropDownButtonPhrase = $Language->Phrase("ButtonSearch");

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide search options
		if ($this->Export <> "" || $this->CurrentAction <> "")
			$this->SearchOptions->HideAllOptions();
		global $Security;
		if (!$Security->CanSearch()) {
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}
	}

	function SetupListOptionsExt() {
		global $Security, $Language;
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Load default values
	function LoadDefaultValues() {
		$this->level1_id->CurrentValue = NULL;
		$this->level1_id->OldValue = $this->level1_id->CurrentValue;
		$this->level2_id->CurrentValue = NULL;
		$this->level2_id->OldValue = $this->level2_id->CurrentValue;
		$this->level3_id->CurrentValue = NULL;
		$this->level3_id->OldValue = $this->level3_id->CurrentValue;
		$this->level4_no->CurrentValue = NULL;
		$this->level4_no->OldValue = $this->level4_no->CurrentValue;
		$this->level4_nama->CurrentValue = NULL;
		$this->level4_nama->OldValue = $this->level4_nama->CurrentValue;
		$this->sa_debet->CurrentValue = NULL;
		$this->sa_debet->OldValue = $this->sa_debet->CurrentValue;
		$this->sa_kredit->CurrentValue = 0.00;
		$this->sa_kredit->OldValue = $this->sa_kredit->CurrentValue;
		$this->jurnal->CurrentValue = 0;
		$this->jurnal->OldValue = $this->jurnal->CurrentValue;
		$this->jurnal_kode->CurrentValue = NULL;
		$this->jurnal_kode->OldValue = $this->jurnal_kode->CurrentValue;
		$this->neraca->CurrentValue = 0;
		$this->neraca->OldValue = $this->neraca->CurrentValue;
		$this->labarugi->CurrentValue = 0;
		$this->labarugi->OldValue = $this->labarugi->CurrentValue;
	}

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->level1_id->FldIsDetailKey) {
			$this->level1_id->setFormValue($objForm->GetValue("x_level1_id"));
		}
		$this->level1_id->setOldValue($objForm->GetValue("o_level1_id"));
		if (!$this->level2_id->FldIsDetailKey) {
			$this->level2_id->setFormValue($objForm->GetValue("x_level2_id"));
		}
		$this->level2_id->setOldValue($objForm->GetValue("o_level2_id"));
		if (!$this->level3_id->FldIsDetailKey) {
			$this->level3_id->setFormValue($objForm->GetValue("x_level3_id"));
		}
		$this->level3_id->setOldValue($objForm->GetValue("o_level3_id"));
		if (!$this->level4_no->FldIsDetailKey) {
			$this->level4_no->setFormValue($objForm->GetValue("x_level4_no"));
		}
		$this->level4_no->setOldValue($objForm->GetValue("o_level4_no"));
		if (!$this->level4_nama->FldIsDetailKey) {
			$this->level4_nama->setFormValue($objForm->GetValue("x_level4_nama"));
		}
		$this->level4_nama->setOldValue($objForm->GetValue("o_level4_nama"));
		if (!$this->sa_debet->FldIsDetailKey) {
			$this->sa_debet->setFormValue($objForm->GetValue("x_sa_debet"));
		}
		$this->sa_debet->setOldValue($objForm->GetValue("o_sa_debet"));
		if (!$this->sa_kredit->FldIsDetailKey) {
			$this->sa_kredit->setFormValue($objForm->GetValue("x_sa_kredit"));
		}
		$this->sa_kredit->setOldValue($objForm->GetValue("o_sa_kredit"));
		if (!$this->jurnal->FldIsDetailKey) {
			$this->jurnal->setFormValue($objForm->GetValue("x_jurnal"));
		}
		$this->jurnal->setOldValue($objForm->GetValue("o_jurnal"));
		if (!$this->jurnal_kode->FldIsDetailKey) {
			$this->jurnal_kode->setFormValue($objForm->GetValue("x_jurnal_kode"));
		}
		$this->jurnal_kode->setOldValue($objForm->GetValue("o_jurnal_kode"));
		if (!$this->neraca->FldIsDetailKey) {
			$this->neraca->setFormValue($objForm->GetValue("x_neraca"));
		}
		$this->neraca->setOldValue($objForm->GetValue("o_neraca"));
		if (!$this->labarugi->FldIsDetailKey) {
			$this->labarugi->setFormValue($objForm->GetValue("x_labarugi"));
		}
		$this->labarugi->setOldValue($objForm->GetValue("o_labarugi"));
		if (!$this->level4_id->FldIsDetailKey && $this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->level4_id->setFormValue($objForm->GetValue("x_level4_id"));
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		if ($this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->level4_id->CurrentValue = $this->level4_id->FormValue;
		$this->level1_id->CurrentValue = $this->level1_id->FormValue;
		$this->level2_id->CurrentValue = $this->level2_id->FormValue;
		$this->level3_id->CurrentValue = $this->level3_id->FormValue;
		$this->level4_no->CurrentValue = $this->level4_no->FormValue;
		$this->level4_nama->CurrentValue = $this->level4_nama->FormValue;
		$this->sa_debet->CurrentValue = $this->sa_debet->FormValue;
		$this->sa_kredit->CurrentValue = $this->sa_kredit->FormValue;
		$this->jurnal->CurrentValue = $this->jurnal->FormValue;
		$this->jurnal_kode->CurrentValue = $this->jurnal_kode->FormValue;
		$this->neraca->CurrentValue = $this->neraca->FormValue;
		$this->labarugi->CurrentValue = $this->labarugi->FormValue;
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("level4_id")) <> "")
			$this->level4_id->CurrentValue = $this->getKey("level4_id"); // level4_id
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$this->OldRecordset = ew_LoadRecordset($sSql, $conn);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// level1_id
			$this->level1_id->EditAttrs["class"] = "form-control";
			$this->level1_id->EditCustomAttributes = "";
			$this->level1_id->EditValue = ew_HtmlEncode($this->level1_id->CurrentValue);
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
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
					$this->level1_id->EditValue = $this->level1_id->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->level1_id->EditValue = ew_HtmlEncode($this->level1_id->CurrentValue);
				}
			} else {
				$this->level1_id->EditValue = NULL;
			}
			$this->level1_id->PlaceHolder = ew_RemoveHtml($this->level1_id->FldCaption());

			// level2_id
			$this->level2_id->EditAttrs["class"] = "form-control";
			$this->level2_id->EditCustomAttributes = "";
			$this->level2_id->EditValue = ew_HtmlEncode($this->level2_id->CurrentValue);
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
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
					$this->level2_id->EditValue = $this->level2_id->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->level2_id->EditValue = ew_HtmlEncode($this->level2_id->CurrentValue);
				}
			} else {
				$this->level2_id->EditValue = NULL;
			}
			$this->level2_id->PlaceHolder = ew_RemoveHtml($this->level2_id->FldCaption());

			// level3_id
			$this->level3_id->EditAttrs["class"] = "form-control";
			$this->level3_id->EditCustomAttributes = "";
			$this->level3_id->EditValue = ew_HtmlEncode($this->level3_id->CurrentValue);
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
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
					$this->level3_id->EditValue = $this->level3_id->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->level3_id->EditValue = ew_HtmlEncode($this->level3_id->CurrentValue);
				}
			} else {
				$this->level3_id->EditValue = NULL;
			}
			$this->level3_id->PlaceHolder = ew_RemoveHtml($this->level3_id->FldCaption());

			// level4_no
			$this->level4_no->EditAttrs["class"] = "form-control";
			$this->level4_no->EditCustomAttributes = "";
			$this->level4_no->EditValue = ew_HtmlEncode($this->level4_no->CurrentValue);
			$this->level4_no->PlaceHolder = ew_RemoveHtml($this->level4_no->FldCaption());

			// level4_nama
			$this->level4_nama->EditAttrs["class"] = "form-control";
			$this->level4_nama->EditCustomAttributes = "";
			$this->level4_nama->EditValue = ew_HtmlEncode($this->level4_nama->CurrentValue);
			$this->level4_nama->PlaceHolder = ew_RemoveHtml($this->level4_nama->FldCaption());

			// sa_debet
			$this->sa_debet->EditAttrs["class"] = "form-control";
			$this->sa_debet->EditCustomAttributes = "";
			$this->sa_debet->EditValue = ew_HtmlEncode($this->sa_debet->CurrentValue);
			$this->sa_debet->PlaceHolder = ew_RemoveHtml($this->sa_debet->FldCaption());
			if (strval($this->sa_debet->EditValue) <> "" && is_numeric($this->sa_debet->EditValue)) {
			$this->sa_debet->EditValue = ew_FormatNumber($this->sa_debet->EditValue, -2, -2, -2, -1);
			$this->sa_debet->OldValue = $this->sa_debet->EditValue;
			}

			// sa_kredit
			$this->sa_kredit->EditAttrs["class"] = "form-control";
			$this->sa_kredit->EditCustomAttributes = "";
			$this->sa_kredit->EditValue = ew_HtmlEncode($this->sa_kredit->CurrentValue);
			$this->sa_kredit->PlaceHolder = ew_RemoveHtml($this->sa_kredit->FldCaption());
			if (strval($this->sa_kredit->EditValue) <> "" && is_numeric($this->sa_kredit->EditValue)) {
			$this->sa_kredit->EditValue = ew_FormatNumber($this->sa_kredit->EditValue, -2, -2, -2, -2);
			$this->sa_kredit->OldValue = $this->sa_kredit->EditValue;
			}

			// jurnal
			$this->jurnal->EditCustomAttributes = "";
			$this->jurnal->EditValue = $this->jurnal->Options(FALSE);

			// jurnal_kode
			$this->jurnal_kode->EditCustomAttributes = "";
			$this->jurnal_kode->EditValue = $this->jurnal_kode->Options(FALSE);

			// neraca
			$this->neraca->EditCustomAttributes = "";
			$this->neraca->EditValue = $this->neraca->Options(FALSE);

			// labarugi
			$this->labarugi->EditCustomAttributes = "";
			$this->labarugi->EditValue = $this->labarugi->Options(FALSE);

			// Add refer script
			// level1_id

			$this->level1_id->LinkCustomAttributes = "";
			$this->level1_id->HrefValue = "";

			// level2_id
			$this->level2_id->LinkCustomAttributes = "";
			$this->level2_id->HrefValue = "";

			// level3_id
			$this->level3_id->LinkCustomAttributes = "";
			$this->level3_id->HrefValue = "";

			// level4_no
			$this->level4_no->LinkCustomAttributes = "";
			$this->level4_no->HrefValue = "";

			// level4_nama
			$this->level4_nama->LinkCustomAttributes = "";
			$this->level4_nama->HrefValue = "";

			// sa_debet
			$this->sa_debet->LinkCustomAttributes = "";
			$this->sa_debet->HrefValue = "";

			// sa_kredit
			$this->sa_kredit->LinkCustomAttributes = "";
			$this->sa_kredit->HrefValue = "";

			// jurnal
			$this->jurnal->LinkCustomAttributes = "";
			$this->jurnal->HrefValue = "";

			// jurnal_kode
			$this->jurnal_kode->LinkCustomAttributes = "";
			$this->jurnal_kode->HrefValue = "";

			// neraca
			$this->neraca->LinkCustomAttributes = "";
			$this->neraca->HrefValue = "";

			// labarugi
			$this->labarugi->LinkCustomAttributes = "";
			$this->labarugi->HrefValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// level1_id
			$this->level1_id->EditAttrs["class"] = "form-control";
			$this->level1_id->EditCustomAttributes = "";
			$this->level1_id->EditValue = ew_HtmlEncode($this->level1_id->CurrentValue);
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
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
					$this->level1_id->EditValue = $this->level1_id->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->level1_id->EditValue = ew_HtmlEncode($this->level1_id->CurrentValue);
				}
			} else {
				$this->level1_id->EditValue = NULL;
			}
			$this->level1_id->PlaceHolder = ew_RemoveHtml($this->level1_id->FldCaption());

			// level2_id
			$this->level2_id->EditAttrs["class"] = "form-control";
			$this->level2_id->EditCustomAttributes = "";
			$this->level2_id->EditValue = ew_HtmlEncode($this->level2_id->CurrentValue);
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
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
					$this->level2_id->EditValue = $this->level2_id->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->level2_id->EditValue = ew_HtmlEncode($this->level2_id->CurrentValue);
				}
			} else {
				$this->level2_id->EditValue = NULL;
			}
			$this->level2_id->PlaceHolder = ew_RemoveHtml($this->level2_id->FldCaption());

			// level3_id
			$this->level3_id->EditAttrs["class"] = "form-control";
			$this->level3_id->EditCustomAttributes = "";
			$this->level3_id->EditValue = ew_HtmlEncode($this->level3_id->CurrentValue);
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
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
					$this->level3_id->EditValue = $this->level3_id->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->level3_id->EditValue = ew_HtmlEncode($this->level3_id->CurrentValue);
				}
			} else {
				$this->level3_id->EditValue = NULL;
			}
			$this->level3_id->PlaceHolder = ew_RemoveHtml($this->level3_id->FldCaption());

			// level4_no
			$this->level4_no->EditAttrs["class"] = "form-control";
			$this->level4_no->EditCustomAttributes = "";
			$this->level4_no->EditValue = ew_HtmlEncode($this->level4_no->CurrentValue);
			$this->level4_no->PlaceHolder = ew_RemoveHtml($this->level4_no->FldCaption());

			// level4_nama
			$this->level4_nama->EditAttrs["class"] = "form-control";
			$this->level4_nama->EditCustomAttributes = "";
			$this->level4_nama->EditValue = ew_HtmlEncode($this->level4_nama->CurrentValue);
			$this->level4_nama->PlaceHolder = ew_RemoveHtml($this->level4_nama->FldCaption());

			// sa_debet
			$this->sa_debet->EditAttrs["class"] = "form-control";
			$this->sa_debet->EditCustomAttributes = "";
			$this->sa_debet->EditValue = ew_HtmlEncode($this->sa_debet->CurrentValue);
			$this->sa_debet->PlaceHolder = ew_RemoveHtml($this->sa_debet->FldCaption());
			if (strval($this->sa_debet->EditValue) <> "" && is_numeric($this->sa_debet->EditValue)) {
			$this->sa_debet->EditValue = ew_FormatNumber($this->sa_debet->EditValue, -2, -2, -2, -1);
			$this->sa_debet->OldValue = $this->sa_debet->EditValue;
			}

			// sa_kredit
			$this->sa_kredit->EditAttrs["class"] = "form-control";
			$this->sa_kredit->EditCustomAttributes = "";
			$this->sa_kredit->EditValue = ew_HtmlEncode($this->sa_kredit->CurrentValue);
			$this->sa_kredit->PlaceHolder = ew_RemoveHtml($this->sa_kredit->FldCaption());
			if (strval($this->sa_kredit->EditValue) <> "" && is_numeric($this->sa_kredit->EditValue)) {
			$this->sa_kredit->EditValue = ew_FormatNumber($this->sa_kredit->EditValue, -2, -2, -2, -2);
			$this->sa_kredit->OldValue = $this->sa_kredit->EditValue;
			}

			// jurnal
			$this->jurnal->EditCustomAttributes = "";
			$this->jurnal->EditValue = $this->jurnal->Options(FALSE);

			// jurnal_kode
			$this->jurnal_kode->EditCustomAttributes = "";
			$this->jurnal_kode->EditValue = $this->jurnal_kode->Options(FALSE);

			// neraca
			$this->neraca->EditCustomAttributes = "";
			$this->neraca->EditValue = $this->neraca->Options(FALSE);

			// labarugi
			$this->labarugi->EditCustomAttributes = "";
			$this->labarugi->EditValue = $this->labarugi->Options(FALSE);

			// Edit refer script
			// level1_id

			$this->level1_id->LinkCustomAttributes = "";
			$this->level1_id->HrefValue = "";

			// level2_id
			$this->level2_id->LinkCustomAttributes = "";
			$this->level2_id->HrefValue = "";

			// level3_id
			$this->level3_id->LinkCustomAttributes = "";
			$this->level3_id->HrefValue = "";

			// level4_no
			$this->level4_no->LinkCustomAttributes = "";
			$this->level4_no->HrefValue = "";

			// level4_nama
			$this->level4_nama->LinkCustomAttributes = "";
			$this->level4_nama->HrefValue = "";

			// sa_debet
			$this->sa_debet->LinkCustomAttributes = "";
			$this->sa_debet->HrefValue = "";

			// sa_kredit
			$this->sa_kredit->LinkCustomAttributes = "";
			$this->sa_kredit->HrefValue = "";

			// jurnal
			$this->jurnal->LinkCustomAttributes = "";
			$this->jurnal->HrefValue = "";

			// jurnal_kode
			$this->jurnal_kode->LinkCustomAttributes = "";
			$this->jurnal_kode->HrefValue = "";

			// neraca
			$this->neraca->LinkCustomAttributes = "";
			$this->neraca->HrefValue = "";

			// labarugi
			$this->labarugi->LinkCustomAttributes = "";
			$this->labarugi->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->level1_id->FldIsDetailKey && !is_null($this->level1_id->FormValue) && $this->level1_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->level1_id->FldCaption(), $this->level1_id->ReqErrMsg));
		}
		if (!$this->level2_id->FldIsDetailKey && !is_null($this->level2_id->FormValue) && $this->level2_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->level2_id->FldCaption(), $this->level2_id->ReqErrMsg));
		}
		if (!$this->level3_id->FldIsDetailKey && !is_null($this->level3_id->FormValue) && $this->level3_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->level3_id->FldCaption(), $this->level3_id->ReqErrMsg));
		}
		if (!$this->level4_no->FldIsDetailKey && !is_null($this->level4_no->FormValue) && $this->level4_no->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->level4_no->FldCaption(), $this->level4_no->ReqErrMsg));
		}
		if (!$this->level4_nama->FldIsDetailKey && !is_null($this->level4_nama->FormValue) && $this->level4_nama->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->level4_nama->FldCaption(), $this->level4_nama->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->sa_debet->FormValue)) {
			ew_AddMessage($gsFormError, $this->sa_debet->FldErrMsg());
		}
		if (!ew_CheckNumber($this->sa_kredit->FormValue)) {
			ew_AddMessage($gsFormError, $this->sa_kredit->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
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
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteSuccess")); // Batch delete success
		} else {
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Update record based on key values
	function EditRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$conn = &$this->Connection();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// level1_id
			$this->level1_id->SetDbValueDef($rsnew, $this->level1_id->CurrentValue, 0, $this->level1_id->ReadOnly);

			// level2_id
			$this->level2_id->SetDbValueDef($rsnew, $this->level2_id->CurrentValue, 0, $this->level2_id->ReadOnly);

			// level3_id
			$this->level3_id->SetDbValueDef($rsnew, $this->level3_id->CurrentValue, 0, $this->level3_id->ReadOnly);

			// level4_no
			$this->level4_no->SetDbValueDef($rsnew, $this->level4_no->CurrentValue, "", $this->level4_no->ReadOnly);

			// level4_nama
			$this->level4_nama->SetDbValueDef($rsnew, $this->level4_nama->CurrentValue, "", $this->level4_nama->ReadOnly);

			// sa_debet
			$this->sa_debet->SetDbValueDef($rsnew, $this->sa_debet->CurrentValue, NULL, $this->sa_debet->ReadOnly);

			// sa_kredit
			$this->sa_kredit->SetDbValueDef($rsnew, $this->sa_kredit->CurrentValue, NULL, $this->sa_kredit->ReadOnly);

			// jurnal
			$this->jurnal->SetDbValueDef($rsnew, $this->jurnal->CurrentValue, NULL, $this->jurnal->ReadOnly);

			// jurnal_kode
			$this->jurnal_kode->SetDbValueDef($rsnew, $this->jurnal_kode->CurrentValue, NULL, $this->jurnal_kode->ReadOnly);

			// neraca
			$this->neraca->SetDbValueDef($rsnew, $this->neraca->CurrentValue, NULL, $this->neraca->ReadOnly);

			// labarugi
			$this->labarugi->SetDbValueDef($rsnew, $this->labarugi->CurrentValue, NULL, $this->labarugi->ReadOnly);

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();
		return $EditRow;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $Language, $Security;
		$conn = &$this->Connection();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// level1_id
		$this->level1_id->SetDbValueDef($rsnew, $this->level1_id->CurrentValue, 0, FALSE);

		// level2_id
		$this->level2_id->SetDbValueDef($rsnew, $this->level2_id->CurrentValue, 0, FALSE);

		// level3_id
		$this->level3_id->SetDbValueDef($rsnew, $this->level3_id->CurrentValue, 0, FALSE);

		// level4_no
		$this->level4_no->SetDbValueDef($rsnew, $this->level4_no->CurrentValue, "", FALSE);

		// level4_nama
		$this->level4_nama->SetDbValueDef($rsnew, $this->level4_nama->CurrentValue, "", FALSE);

		// sa_debet
		$this->sa_debet->SetDbValueDef($rsnew, $this->sa_debet->CurrentValue, NULL, strval($this->sa_debet->CurrentValue) == "");

		// sa_kredit
		$this->sa_kredit->SetDbValueDef($rsnew, $this->sa_kredit->CurrentValue, NULL, strval($this->sa_kredit->CurrentValue) == "");

		// jurnal
		$this->jurnal->SetDbValueDef($rsnew, $this->jurnal->CurrentValue, NULL, strval($this->jurnal->CurrentValue) == "");

		// jurnal_kode
		$this->jurnal_kode->SetDbValueDef($rsnew, $this->jurnal_kode->CurrentValue, NULL, FALSE);

		// neraca
		$this->neraca->SetDbValueDef($rsnew, $this->neraca->CurrentValue, NULL, strval($this->neraca->CurrentValue) == "");

		// labarugi
		$this->labarugi->SetDbValueDef($rsnew, $this->labarugi->CurrentValue, NULL, strval($this->labarugi->CurrentValue) == "");

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}
		return $AddRow;
	}

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\" class=\"ewExportLink ewPrint\" title=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\" class=\"ewExportLink ewExcel\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\" class=\"ewExportLink ewWord\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = TRUE;

		// Export to Html
		$item = &$this->ExportOptions->Add("html");
		$item->Body = "<a href=\"" . $this->ExportHtmlUrl . "\" class=\"ewExportLink ewHtml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\">" . $Language->Phrase("ExportToHtml") . "</a>";
		$item->Visible = TRUE;

		// Export to Xml
		$item = &$this->ExportOptions->Add("xml");
		$item->Body = "<a href=\"" . $this->ExportXmlUrl . "\" class=\"ewExportLink ewXml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\">" . $Language->Phrase("ExportToXml") . "</a>";
		$item->Visible = TRUE;

		// Export to Csv
		$item = &$this->ExportOptions->Add("csv");
		$item->Body = "<a href=\"" . $this->ExportCsvUrl . "\" class=\"ewExportLink ewCsv\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\">" . $Language->Phrase("ExportToCsv") . "</a>";
		$item->Visible = TRUE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\" class=\"ewExportLink ewPdf\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = "";
		$item->Body = "<button id=\"emf_t_level4\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_t_level4',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.ft_level4list,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
		$item->Visible = TRUE;

		// Drop down button for export
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = TRUE;
		$this->ExportOptions->UseDropDownButton = TRUE;
		if ($this->ExportOptions->UseButtonGroup && ew_IsMobile())
			$this->ExportOptions->UseDropDownButton = TRUE;
		$this->ExportOptions->DropDownButtonPhrase = $Language->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = $this->UseSelectLimit;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if (!$this->Recordset)
				$this->Recordset = $this->LoadRecordset();
			$rs = &$this->Recordset;
			if ($rs)
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;

		// Export all
		if ($this->ExportAll) {
			set_time_limit(EW_EXPORT_ALL_TIME_LIMIT);
			$this->DisplayRecs = $this->TotalRecs;
			$this->StopRec = $this->TotalRecs;
		} else { // Export one page only
			$this->SetUpStartRec(); // Set up start record position

			// Set the last record to display
			if ($this->DisplayRecs <= 0) {
				$this->StopRec = $this->TotalRecs;
			} else {
				$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
			}
		}
		if ($bSelectLimit)
			$rs = $this->LoadRecordset($this->StartRec-1, $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs);
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$this->ExportDoc = ew_ExportDocument($this, "h");
		$Doc = &$this->ExportDoc;
		if ($bSelectLimit) {
			$this->StartRec = 1;
			$this->StopRec = $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs;
		} else {

			//$this->StartRec = $this->StartRec;
			//$this->StopRec = $this->StopRec;

		}

		// Call Page Exporting server event
		$this->ExportDoc->ExportCustom = !$this->Page_Exporting();
		$ParentTable = "";
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		$Doc->Text .= $sHeader;
		$this->ExportDocument($Doc, $rs, $this->StartRec, $this->StopRec, "");
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		$Doc->Text .= $sFooter;

		// Close recordset
		$rs->Close();

		// Call Page Exported server event
		$this->Page_Exported();

		// Export header and footer
		$Doc->ExportHeaderAndFooter();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Write debug message if enabled
		if (EW_DEBUG_ENABLED && $this->Export <> "pdf")
			echo ew_DebugMsg();

		// Output data
		if ($this->Export == "email") {
			echo $this->ExportEmail($Doc->Text);
		} else {
			$Doc->Export();
		}
	}

	// Export email
	function ExportEmail($EmailContent) {
		global $gTmpImages, $Language;
		$sSender = @$_POST["sender"];
		$sRecipient = @$_POST["recipient"];
		$sCc = @$_POST["cc"];
		$sBcc = @$_POST["bcc"];
		$sContentType = @$_POST["contenttype"];

		// Subject
		$sSubject = ew_StripSlashes(@$_POST["subject"]);
		$sEmailSubject = $sSubject;

		// Message
		$sContent = ew_StripSlashes(@$_POST["message"]);
		$sEmailMessage = $sContent;

		// Check sender
		if ($sSender == "") {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterSenderEmail") . "</p>";
		}
		if (!ew_CheckEmail($sSender)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperSenderEmail") . "</p>";
		}

		// Check recipient
		if (!ew_CheckEmailList($sRecipient, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperRecipientEmail") . "</p>";
		}

		// Check cc
		if (!ew_CheckEmailList($sCc, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperCcEmail") . "</p>";
		}

		// Check bcc
		if (!ew_CheckEmailList($sBcc, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperBccEmail") . "</p>";
		}

		// Check email sent count
		if (!isset($_SESSION[EW_EXPORT_EMAIL_COUNTER]))
			$_SESSION[EW_EXPORT_EMAIL_COUNTER] = 0;
		if (intval($_SESSION[EW_EXPORT_EMAIL_COUNTER]) > EW_MAX_EMAIL_SENT_COUNT) {
			return "<p class=\"text-danger\">" . $Language->Phrase("ExceedMaxEmailExport") . "</p>";
		}

		// Send email
		$Email = new cEmail();
		$Email->Sender = $sSender; // Sender
		$Email->Recipient = $sRecipient; // Recipient
		$Email->Cc = $sCc; // Cc
		$Email->Bcc = $sBcc; // Bcc
		$Email->Subject = $sEmailSubject; // Subject
		$Email->Format = ($sContentType == "url") ? "text" : "html";
		if ($sEmailMessage <> "") {
			$sEmailMessage = ew_RemoveXSS($sEmailMessage);
			$sEmailMessage .= ($sContentType == "url") ? "\r\n\r\n" : "<br><br>";
		}
		if ($sContentType == "url") {
			$sUrl = ew_ConvertFullUrl(ew_CurrentPage() . "?" . $this->ExportQueryString());
			$sEmailMessage .= $sUrl; // Send URL only
		} else {
			foreach ($gTmpImages as $tmpimage)
				$Email->AddEmbeddedImage($tmpimage);
			$sEmailMessage .= ew_CleanEmailContent($EmailContent); // Send HTML
		}
		$Email->Content = $sEmailMessage; // Content
		$EventArgs = array();
		if ($this->Recordset) {
			$this->RecCnt = $this->StartRec - 1;
			$this->Recordset->MoveFirst();
			if ($this->StartRec > 1)
				$this->Recordset->Move($this->StartRec - 1);
			$EventArgs["rs"] = &$this->Recordset;
		}
		$bEmailSent = FALSE;
		if ($this->Email_Sending($Email, $EventArgs))
			$bEmailSent = $Email->Send();

		// Check email sent status
		if ($bEmailSent) {

			// Update email sent count
			$_SESSION[EW_EXPORT_EMAIL_COUNTER]++;

			// Sent email success
			return "<p class=\"text-success\">" . $Language->Phrase("SendEmailSuccess") . "</p>"; // Set up success message
		} else {

			// Sent email failure
			return "<p class=\"text-danger\">" . $Email->SendErrDescription . "</p>";
		}
	}

	// Export QueryString
	function ExportQueryString() {

		// Initialize
		$sQry = "export=html";

		// Build QueryString for search
		if ($this->BasicSearch->getKeyword() <> "") {
			$sQry .= "&" . EW_TABLE_BASIC_SEARCH . "=" . urlencode($this->BasicSearch->getKeyword()) . "&" . EW_TABLE_BASIC_SEARCH_TYPE . "=" . urlencode($this->BasicSearch->getType());
		}

		// Build QueryString for pager
		$sQry .= "&" . EW_TABLE_REC_PER_PAGE . "=" . urlencode($this->getRecordsPerPage()) . "&" . EW_TABLE_START_REC . "=" . urlencode($this->getStartRecordNumber());
		return $sQry;
	}

	// Add search QueryString
	function AddSearchQueryString(&$Qry, &$Fld) {
		$FldSearchValue = $Fld->AdvancedSearch->getValue("x");
		$FldParm = substr($Fld->FldVar,2);
		if (strval($FldSearchValue) <> "") {
			$Qry .= "&x_" . $FldParm . "=" . urlencode($FldSearchValue) .
				"&z_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("z"));
		}
		$FldSearchValue2 = $Fld->AdvancedSearch->getValue("y");
		if (strval($FldSearchValue2) <> "") {
			$Qry .= "&v_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("v")) .
				"&y_" . $FldParm . "=" . urlencode($FldSearchValue2) .
				"&w_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("w"));
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_level1_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `level1_id` AS `LinkFld`, `level1_no` AS `DispFld`, `level1_nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `t_level1`";
			$sWhereWrk = "{filter}";
			$this->level1_id->LookupFilters = array("dx1" => '`level1_no`', "dx2" => '`level1_nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`level1_id` = {filter_value}', "t0" => "3", "fn0" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->level1_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `level1_no` ASC";
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_level2_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `level2_id` AS `LinkFld`, `level2_no` AS `DispFld`, `level2_nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `t_level2`";
			$sWhereWrk = "{filter}";
			$this->level2_id->LookupFilters = array("dx1" => '`level2_no`', "dx2" => '`level2_nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`level2_id` = {filter_value}', "t0" => "3", "fn0" => "", "f1" => '`level1_id` IN ({filter_value})', "t1" => "3", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->level2_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `level2_no` ASC";
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_level3_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `level3_id` AS `LinkFld`, `level3_no` AS `DispFld`, `level3_nama` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `t_level3`";
			$sWhereWrk = "{filter}";
			$this->level3_id->LookupFilters = array("dx1" => '`level3_no`', "dx2" => '`level3_nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f0" => '`level3_id` = {filter_value}', "t0" => "3", "fn0" => "", "f1" => '`level2_id` IN ({filter_value})', "t1" => "3", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->level3_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `level3_no` ASC";
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld, $pageId = null) {
		global $gsLanguage;
		$pageId = $pageId ?: $this->PageID;
		switch ($fld->FldVar) {
		case "x_level1_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `level1_id`, `level1_no` AS `DispFld`, `level1_nama` AS `Disp2Fld` FROM `t_level1`";
			$sWhereWrk = "`level1_no` LIKE '{query_value}%' OR CONCAT(`level1_no`,'" . ew_ValueSeparator(1, $this->level1_id) . "',`level1_nama`) LIKE '{query_value}%'";
			$this->level1_id->LookupFilters = array("dx1" => '`level1_no`', "dx2" => '`level1_nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->level1_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `level1_no` ASC";
			$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_level2_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `level2_id`, `level2_no` AS `DispFld`, `level2_nama` AS `Disp2Fld` FROM `t_level2`";
			$sWhereWrk = "(`level2_no` LIKE '{query_value}%' OR CONCAT(`level2_no`,'" . ew_ValueSeparator(1, $this->level2_id) . "',`level2_nama`) LIKE '{query_value}%') AND ({filter})";
			$this->level2_id->LookupFilters = array("dx1" => '`level2_no`', "dx2" => '`level2_nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f1" => "`level1_id` IN ({filter_value})", "t1" => "3", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->level2_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `level2_no` ASC";
			$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
		case "x_level3_id":
			$sSqlWrk = "";
			$sSqlWrk = "SELECT `level3_id`, `level3_no` AS `DispFld`, `level3_nama` AS `Disp2Fld` FROM `t_level3`";
			$sWhereWrk = "(`level3_no` LIKE '{query_value}%' OR CONCAT(`level3_no`,'" . ew_ValueSeparator(1, $this->level3_id) . "',`level3_nama`) LIKE '{query_value}%') AND ({filter})";
			$this->level3_id->LookupFilters = array("dx1" => '`level3_no`', "dx2" => '`level3_nama`');
			$fld->LookupFilters += array("s" => $sSqlWrk, "d" => "", "f1" => "`level2_id` IN ({filter_value})", "t1" => "3", "fn1" => "");
			$sSqlWrk = "";
			$this->Lookup_Selecting($this->level3_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `level3_no` ASC";
			$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
			if ($sSqlWrk <> "")
				$fld->LookupFilters["s"] .= $sSqlWrk;
			break;
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}

	// Page Exporting event
	// $this->ExportDoc = export document object
	function Page_Exporting() {

		//$this->ExportDoc->Text = "my header"; // Export header
		//return FALSE; // Return FALSE to skip default export and use Row_Export event

		return TRUE; // Return TRUE to use default export and skip Row_Export event
	}

	// Row Export event
	// $this->ExportDoc = export document object
	function Row_Export($rs) {

		//$this->ExportDoc->Text .= "my content"; // Build HTML with field value: $rs["MyField"] or $this->MyField->ViewValue
	}

	// Page Exported event
	// $this->ExportDoc = export document object
	function Page_Exported() {

		//$this->ExportDoc->Text .= "my footer"; // Export footer
		//echo $this->ExportDoc->Text;

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($t_level4_list)) $t_level4_list = new ct_level4_list();

// Page init
$t_level4_list->Page_Init();

// Page main
$t_level4_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$t_level4_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($t_level4->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = ft_level4list = new ew_Form("ft_level4list", "list");
ft_level4list.FormKeyCountName = '<?php echo $t_level4_list->FormKeyCountName ?>';

// Validate form
ft_level4list.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
		var checkrow = (gridinsert) ? !this.EmptyRow(infix) : true;
		if (checkrow) {
			addcnt++;
			elm = this.GetElements("x" + infix + "_level1_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $t_level4->level1_id->FldCaption(), $t_level4->level1_id->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_level2_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $t_level4->level2_id->FldCaption(), $t_level4->level2_id->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_level3_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $t_level4->level3_id->FldCaption(), $t_level4->level3_id->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_level4_no");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $t_level4->level4_no->FldCaption(), $t_level4->level4_no->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_level4_nama");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $t_level4->level4_nama->FldCaption(), $t_level4->level4_nama->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_sa_debet");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($t_level4->sa_debet->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_sa_kredit");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($t_level4->sa_kredit->FldErrMsg()) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
		} // End Grid Add checking
	}
	if (gridinsert && addcnt == 0) { // No row added
		ew_Alert(ewLanguage.Phrase("NoAddRecord"));
		return false;
	}
	return true;
}

// Check empty row
ft_level4list.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "level1_id", false)) return false;
	if (ew_ValueChanged(fobj, infix, "level2_id", false)) return false;
	if (ew_ValueChanged(fobj, infix, "level3_id", false)) return false;
	if (ew_ValueChanged(fobj, infix, "level4_no", false)) return false;
	if (ew_ValueChanged(fobj, infix, "level4_nama", false)) return false;
	if (ew_ValueChanged(fobj, infix, "sa_debet", false)) return false;
	if (ew_ValueChanged(fobj, infix, "sa_kredit", false)) return false;
	if (ew_ValueChanged(fobj, infix, "jurnal", false)) return false;
	if (ew_ValueChanged(fobj, infix, "jurnal_kode", false)) return false;
	if (ew_ValueChanged(fobj, infix, "neraca", false)) return false;
	if (ew_ValueChanged(fobj, infix, "labarugi", false)) return false;
	return true;
}

// Form_CustomValidate event
ft_level4list.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ft_level4list.ValidateRequired = true;
<?php } else { ?>
ft_level4list.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
ft_level4list.Lists["x_level1_id"] = {"LinkField":"x_level1_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_level1_no","x_level1_nama","",""],"ParentFields":[],"ChildFields":["x_level2_id"],"FilterFields":[],"Options":[],"Template":"","LinkTable":"t_level1"};
ft_level4list.Lists["x_level2_id"] = {"LinkField":"x_level2_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_level2_no","x_level2_nama","",""],"ParentFields":["x_level1_id"],"ChildFields":["x_level3_id"],"FilterFields":["x_level1_id"],"Options":[],"Template":"","LinkTable":"t_level2"};
ft_level4list.Lists["x_level3_id"] = {"LinkField":"x_level3_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_level3_no","x_level3_nama","",""],"ParentFields":["x_level2_id"],"ChildFields":[],"FilterFields":["x_level2_id"],"Options":[],"Template":"","LinkTable":"t_level3"};
ft_level4list.Lists["x_jurnal"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
ft_level4list.Lists["x_jurnal"].Options = <?php echo json_encode($t_level4->jurnal->Options()) ?>;
ft_level4list.Lists["x_jurnal_kode"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
ft_level4list.Lists["x_jurnal_kode"].Options = <?php echo json_encode($t_level4->jurnal_kode->Options()) ?>;
ft_level4list.Lists["x_neraca"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
ft_level4list.Lists["x_neraca"].Options = <?php echo json_encode($t_level4->neraca->Options()) ?>;
ft_level4list.Lists["x_labarugi"] = {"LinkField":"","Ajax":null,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
ft_level4list.Lists["x_labarugi"].Options = <?php echo json_encode($t_level4->labarugi->Options()) ?>;

// Form object for search
var CurrentSearchForm = ft_level4listsrch = new ew_Form("ft_level4listsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($t_level4->Export == "") { ?>
<div class="ewToolbar">
<?php if ($t_level4->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($t_level4_list->TotalRecs > 0 && $t_level4_list->ExportOptions->Visible()) { ?>
<?php $t_level4_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($t_level4_list->SearchOptions->Visible()) { ?>
<?php $t_level4_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($t_level4_list->FilterOptions->Visible()) { ?>
<?php $t_level4_list->FilterOptions->Render("body") ?>
<?php } ?>
<?php if ($t_level4->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
if ($t_level4->CurrentAction == "gridadd") {
	$t_level4->CurrentFilter = "0=1";
	$t_level4_list->StartRec = 1;
	$t_level4_list->DisplayRecs = $t_level4->GridAddRowCount;
	$t_level4_list->TotalRecs = $t_level4_list->DisplayRecs;
	$t_level4_list->StopRec = $t_level4_list->DisplayRecs;
} else {
	$bSelectLimit = $t_level4_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($t_level4_list->TotalRecs <= 0)
			$t_level4_list->TotalRecs = $t_level4->SelectRecordCount();
	} else {
		if (!$t_level4_list->Recordset && ($t_level4_list->Recordset = $t_level4_list->LoadRecordset()))
			$t_level4_list->TotalRecs = $t_level4_list->Recordset->RecordCount();
	}
	$t_level4_list->StartRec = 1;
	if ($t_level4_list->DisplayRecs <= 0 || ($t_level4->Export <> "" && $t_level4->ExportAll)) // Display all records
		$t_level4_list->DisplayRecs = $t_level4_list->TotalRecs;
	if (!($t_level4->Export <> "" && $t_level4->ExportAll))
		$t_level4_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$t_level4_list->Recordset = $t_level4_list->LoadRecordset($t_level4_list->StartRec-1, $t_level4_list->DisplayRecs);

	// Set no record found message
	if ($t_level4->CurrentAction == "" && $t_level4_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$t_level4_list->setWarningMessage(ew_DeniedMsg());
		if ($t_level4_list->SearchWhere == "0=101")
			$t_level4_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$t_level4_list->setWarningMessage($Language->Phrase("NoRecord"));
	}

	// Audit trail on search
	if ($t_level4_list->AuditTrailOnSearch && $t_level4_list->Command == "search" && !$t_level4_list->RestoreSearch) {
		$searchparm = ew_ServerVar("QUERY_STRING");
		$searchsql = $t_level4_list->getSessionWhere();
		$t_level4_list->WriteAuditTrailOnSearch($searchparm, $searchsql);
	}
}
$t_level4_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($t_level4->Export == "" && $t_level4->CurrentAction == "") { ?>
<form name="ft_level4listsrch" id="ft_level4listsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($t_level4_list->SearchWhere <> "") ? " in" : " in"; ?>
<div id="ft_level4listsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="t_level4">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($t_level4_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($t_level4_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $t_level4_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($t_level4_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($t_level4_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($t_level4_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($t_level4_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
		</ul>
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
</div>
	</div>
</div>
</form>
<?php } ?>
<?php } ?>
<?php $t_level4_list->ShowPageHeader(); ?>
<?php
$t_level4_list->ShowMessage();
?>
<?php if ($t_level4_list->TotalRecs > 0 || $t_level4->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid t_level4">
<?php if ($t_level4->Export == "") { ?>
<div class="panel-heading ewGridUpperPanel">
<?php if ($t_level4->CurrentAction <> "gridadd" && $t_level4->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="form-inline ewForm ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($t_level4_list->Pager)) $t_level4_list->Pager = new cPrevNextPager($t_level4_list->StartRec, $t_level4_list->DisplayRecs, $t_level4_list->TotalRecs) ?>
<?php if ($t_level4_list->Pager->RecordCount > 0 && $t_level4_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($t_level4_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $t_level4_list->PageUrl() ?>start=<?php echo $t_level4_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($t_level4_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $t_level4_list->PageUrl() ?>start=<?php echo $t_level4_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $t_level4_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($t_level4_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $t_level4_list->PageUrl() ?>start=<?php echo $t_level4_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($t_level4_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $t_level4_list->PageUrl() ?>start=<?php echo $t_level4_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $t_level4_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $t_level4_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $t_level4_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $t_level4_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
<?php if ($t_level4_list->TotalRecs > 0 && (!EW_AUTO_HIDE_PAGE_SIZE_SELECTOR || $t_level4_list->Pager->Visible)) { ?>
<div class="ewPager">
<input type="hidden" name="t" value="t_level4">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="form-control input-sm ewTooltip" title="<?php echo $Language->Phrase("RecordsPerPage") ?>" onchange="this.form.submit();">
<option value="10"<?php if ($t_level4_list->DisplayRecs == 10) { ?> selected<?php } ?>>10</option>
<option value="20"<?php if ($t_level4_list->DisplayRecs == 20) { ?> selected<?php } ?>>20</option>
<option value="50"<?php if ($t_level4_list->DisplayRecs == 50) { ?> selected<?php } ?>>50</option>
<option value="100"<?php if ($t_level4_list->DisplayRecs == 100) { ?> selected<?php } ?>>100</option>
<option value="200"<?php if ($t_level4_list->DisplayRecs == 200) { ?> selected<?php } ?>>200</option>
<option value="ALL"<?php if ($t_level4->getRecordsPerPage() == -1) { ?> selected<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($t_level4_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
<form name="ft_level4list" id="ft_level4list" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($t_level4_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $t_level4_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="t_level4">
<div id="gmp_t_level4" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($t_level4_list->TotalRecs > 0 || $t_level4->CurrentAction == "add" || $t_level4->CurrentAction == "copy" || $t_level4->CurrentAction == "gridedit") { ?>
<table id="tbl_t_level4list" class="table ewTable">
<?php echo $t_level4->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$t_level4_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$t_level4_list->RenderListOptions();

// Render list options (header, left)
$t_level4_list->ListOptions->Render("header", "left");
?>
<?php if ($t_level4->level1_id->Visible) { // level1_id ?>
	<?php if ($t_level4->SortUrl($t_level4->level1_id) == "") { ?>
		<th data-name="level1_id"><div id="elh_t_level4_level1_id" class="t_level4_level1_id"><div class="ewTableHeaderCaption"><?php echo $t_level4->level1_id->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="level1_id"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $t_level4->SortUrl($t_level4->level1_id) ?>',2);"><div id="elh_t_level4_level1_id" class="t_level4_level1_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $t_level4->level1_id->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($t_level4->level1_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($t_level4->level1_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($t_level4->level2_id->Visible) { // level2_id ?>
	<?php if ($t_level4->SortUrl($t_level4->level2_id) == "") { ?>
		<th data-name="level2_id"><div id="elh_t_level4_level2_id" class="t_level4_level2_id"><div class="ewTableHeaderCaption"><?php echo $t_level4->level2_id->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="level2_id"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $t_level4->SortUrl($t_level4->level2_id) ?>',2);"><div id="elh_t_level4_level2_id" class="t_level4_level2_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $t_level4->level2_id->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($t_level4->level2_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($t_level4->level2_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($t_level4->level3_id->Visible) { // level3_id ?>
	<?php if ($t_level4->SortUrl($t_level4->level3_id) == "") { ?>
		<th data-name="level3_id"><div id="elh_t_level4_level3_id" class="t_level4_level3_id"><div class="ewTableHeaderCaption"><?php echo $t_level4->level3_id->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="level3_id"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $t_level4->SortUrl($t_level4->level3_id) ?>',2);"><div id="elh_t_level4_level3_id" class="t_level4_level3_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $t_level4->level3_id->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($t_level4->level3_id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($t_level4->level3_id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($t_level4->level4_no->Visible) { // level4_no ?>
	<?php if ($t_level4->SortUrl($t_level4->level4_no) == "") { ?>
		<th data-name="level4_no"><div id="elh_t_level4_level4_no" class="t_level4_level4_no"><div class="ewTableHeaderCaption"><?php echo $t_level4->level4_no->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="level4_no"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $t_level4->SortUrl($t_level4->level4_no) ?>',2);"><div id="elh_t_level4_level4_no" class="t_level4_level4_no">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $t_level4->level4_no->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($t_level4->level4_no->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($t_level4->level4_no->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($t_level4->level4_nama->Visible) { // level4_nama ?>
	<?php if ($t_level4->SortUrl($t_level4->level4_nama) == "") { ?>
		<th data-name="level4_nama"><div id="elh_t_level4_level4_nama" class="t_level4_level4_nama"><div class="ewTableHeaderCaption"><?php echo $t_level4->level4_nama->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="level4_nama"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $t_level4->SortUrl($t_level4->level4_nama) ?>',2);"><div id="elh_t_level4_level4_nama" class="t_level4_level4_nama">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $t_level4->level4_nama->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($t_level4->level4_nama->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($t_level4->level4_nama->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($t_level4->sa_debet->Visible) { // sa_debet ?>
	<?php if ($t_level4->SortUrl($t_level4->sa_debet) == "") { ?>
		<th data-name="sa_debet"><div id="elh_t_level4_sa_debet" class="t_level4_sa_debet"><div class="ewTableHeaderCaption"><?php echo $t_level4->sa_debet->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="sa_debet"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $t_level4->SortUrl($t_level4->sa_debet) ?>',2);"><div id="elh_t_level4_sa_debet" class="t_level4_sa_debet">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $t_level4->sa_debet->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($t_level4->sa_debet->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($t_level4->sa_debet->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($t_level4->sa_kredit->Visible) { // sa_kredit ?>
	<?php if ($t_level4->SortUrl($t_level4->sa_kredit) == "") { ?>
		<th data-name="sa_kredit"><div id="elh_t_level4_sa_kredit" class="t_level4_sa_kredit"><div class="ewTableHeaderCaption"><?php echo $t_level4->sa_kredit->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="sa_kredit"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $t_level4->SortUrl($t_level4->sa_kredit) ?>',2);"><div id="elh_t_level4_sa_kredit" class="t_level4_sa_kredit">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $t_level4->sa_kredit->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($t_level4->sa_kredit->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($t_level4->sa_kredit->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($t_level4->jurnal->Visible) { // jurnal ?>
	<?php if ($t_level4->SortUrl($t_level4->jurnal) == "") { ?>
		<th data-name="jurnal"><div id="elh_t_level4_jurnal" class="t_level4_jurnal"><div class="ewTableHeaderCaption"><?php echo $t_level4->jurnal->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="jurnal"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $t_level4->SortUrl($t_level4->jurnal) ?>',2);"><div id="elh_t_level4_jurnal" class="t_level4_jurnal">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $t_level4->jurnal->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($t_level4->jurnal->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($t_level4->jurnal->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($t_level4->jurnal_kode->Visible) { // jurnal_kode ?>
	<?php if ($t_level4->SortUrl($t_level4->jurnal_kode) == "") { ?>
		<th data-name="jurnal_kode"><div id="elh_t_level4_jurnal_kode" class="t_level4_jurnal_kode"><div class="ewTableHeaderCaption"><?php echo $t_level4->jurnal_kode->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="jurnal_kode"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $t_level4->SortUrl($t_level4->jurnal_kode) ?>',2);"><div id="elh_t_level4_jurnal_kode" class="t_level4_jurnal_kode">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $t_level4->jurnal_kode->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($t_level4->jurnal_kode->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($t_level4->jurnal_kode->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($t_level4->neraca->Visible) { // neraca ?>
	<?php if ($t_level4->SortUrl($t_level4->neraca) == "") { ?>
		<th data-name="neraca"><div id="elh_t_level4_neraca" class="t_level4_neraca"><div class="ewTableHeaderCaption"><?php echo $t_level4->neraca->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="neraca"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $t_level4->SortUrl($t_level4->neraca) ?>',2);"><div id="elh_t_level4_neraca" class="t_level4_neraca">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $t_level4->neraca->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($t_level4->neraca->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($t_level4->neraca->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($t_level4->labarugi->Visible) { // labarugi ?>
	<?php if ($t_level4->SortUrl($t_level4->labarugi) == "") { ?>
		<th data-name="labarugi"><div id="elh_t_level4_labarugi" class="t_level4_labarugi"><div class="ewTableHeaderCaption"><?php echo $t_level4->labarugi->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="labarugi"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $t_level4->SortUrl($t_level4->labarugi) ?>',2);"><div id="elh_t_level4_labarugi" class="t_level4_labarugi">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $t_level4->labarugi->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($t_level4->labarugi->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($t_level4->labarugi->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$t_level4_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
	if ($t_level4->CurrentAction == "add" || $t_level4->CurrentAction == "copy") {
		$t_level4_list->RowIndex = 0;
		$t_level4_list->KeyCount = $t_level4_list->RowIndex;
		if ($t_level4->CurrentAction == "copy" && !$t_level4_list->LoadRow())
				$t_level4->CurrentAction = "add";
		if ($t_level4->CurrentAction == "add")
			$t_level4_list->LoadDefaultValues();
		if ($t_level4->EventCancelled) // Insert failed
			$t_level4_list->RestoreFormValues(); // Restore form values

		// Set row properties
		$t_level4->ResetAttrs();
		$t_level4->RowAttrs = array_merge($t_level4->RowAttrs, array('data-rowindex'=>0, 'id'=>'r0_t_level4', 'data-rowtype'=>EW_ROWTYPE_ADD));
		$t_level4->RowType = EW_ROWTYPE_ADD;

		// Render row
		$t_level4_list->RenderRow();

		// Render list options
		$t_level4_list->RenderListOptions();
		$t_level4_list->StartRowCnt = 0;
?>
	<tr<?php echo $t_level4->RowAttributes() ?>>
<?php

// Render list options (body, left)
$t_level4_list->ListOptions->Render("body", "left", $t_level4_list->RowCnt);
?>
	<?php if ($t_level4->level1_id->Visible) { // level1_id ?>
		<td data-name="level1_id">
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level1_id" class="form-group t_level4_level1_id">
<?php
$wrkonchange = trim("ew_UpdateOpt.call(this); " . @$t_level4->level1_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$t_level4->level1_id->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $t_level4_list->RowIndex ?>_level1_id" style="white-space: nowrap; z-index: <?php echo (9000 - $t_level4_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="sv_x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo $t_level4->level1_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->level1_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($t_level4->level1_id->getPlaceHolder()) ?>"<?php echo $t_level4->level1_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level1_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $t_level4->level1_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo ew_HtmlEncode($t_level4->level1_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="q_x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo $t_level4->level1_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ft_level4list.CreateAutoSuggest({"id":"x<?php echo $t_level4_list->RowIndex ?>_level1_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($t_level4->level1_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $t_level4_list->RowIndex ?>_level1_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="s_x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo $t_level4->level1_id->LookupFilterQuery(false) ?>">
</span>
<input type="hidden" data-table="t_level4" data-field="x_level1_id" name="o<?php echo $t_level4_list->RowIndex ?>_level1_id" id="o<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo ew_HtmlEncode($t_level4->level1_id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->level2_id->Visible) { // level2_id ?>
		<td data-name="level2_id">
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level2_id" class="form-group t_level4_level2_id">
<?php
$wrkonchange = trim("ew_UpdateOpt.call(this); " . @$t_level4->level2_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$t_level4->level2_id->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $t_level4_list->RowIndex ?>_level2_id" style="white-space: nowrap; z-index: <?php echo (9000 - $t_level4_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="sv_x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo $t_level4->level2_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->level2_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($t_level4->level2_id->getPlaceHolder()) ?>"<?php echo $t_level4->level2_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level2_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $t_level4->level2_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo ew_HtmlEncode($t_level4->level2_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="q_x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo $t_level4->level2_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ft_level4list.CreateAutoSuggest({"id":"x<?php echo $t_level4_list->RowIndex ?>_level2_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($t_level4->level2_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $t_level4_list->RowIndex ?>_level2_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="s_x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo $t_level4->level2_id->LookupFilterQuery(false) ?>">
</span>
<input type="hidden" data-table="t_level4" data-field="x_level2_id" name="o<?php echo $t_level4_list->RowIndex ?>_level2_id" id="o<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo ew_HtmlEncode($t_level4->level2_id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->level3_id->Visible) { // level3_id ?>
		<td data-name="level3_id">
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level3_id" class="form-group t_level4_level3_id">
<?php
$wrkonchange = trim(" " . @$t_level4->level3_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$t_level4->level3_id->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $t_level4_list->RowIndex ?>_level3_id" style="white-space: nowrap; z-index: <?php echo (9000 - $t_level4_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="sv_x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo $t_level4->level3_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->level3_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($t_level4->level3_id->getPlaceHolder()) ?>"<?php echo $t_level4->level3_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level3_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $t_level4->level3_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo ew_HtmlEncode($t_level4->level3_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="q_x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo $t_level4->level3_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ft_level4list.CreateAutoSuggest({"id":"x<?php echo $t_level4_list->RowIndex ?>_level3_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($t_level4->level3_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $t_level4_list->RowIndex ?>_level3_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="s_x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo $t_level4->level3_id->LookupFilterQuery(false) ?>">
</span>
<input type="hidden" data-table="t_level4" data-field="x_level3_id" name="o<?php echo $t_level4_list->RowIndex ?>_level3_id" id="o<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo ew_HtmlEncode($t_level4->level3_id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->level4_no->Visible) { // level4_no ?>
		<td data-name="level4_no">
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level4_no" class="form-group t_level4_level4_no">
<input type="text" data-table="t_level4" data-field="x_level4_no" name="x<?php echo $t_level4_list->RowIndex ?>_level4_no" id="x<?php echo $t_level4_list->RowIndex ?>_level4_no" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($t_level4->level4_no->getPlaceHolder()) ?>" value="<?php echo $t_level4->level4_no->EditValue ?>"<?php echo $t_level4->level4_no->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level4_no" name="o<?php echo $t_level4_list->RowIndex ?>_level4_no" id="o<?php echo $t_level4_list->RowIndex ?>_level4_no" value="<?php echo ew_HtmlEncode($t_level4->level4_no->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->level4_nama->Visible) { // level4_nama ?>
		<td data-name="level4_nama">
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level4_nama" class="form-group t_level4_level4_nama">
<input type="text" data-table="t_level4" data-field="x_level4_nama" name="x<?php echo $t_level4_list->RowIndex ?>_level4_nama" id="x<?php echo $t_level4_list->RowIndex ?>_level4_nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($t_level4->level4_nama->getPlaceHolder()) ?>" value="<?php echo $t_level4->level4_nama->EditValue ?>"<?php echo $t_level4->level4_nama->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level4_nama" name="o<?php echo $t_level4_list->RowIndex ?>_level4_nama" id="o<?php echo $t_level4_list->RowIndex ?>_level4_nama" value="<?php echo ew_HtmlEncode($t_level4->level4_nama->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->sa_debet->Visible) { // sa_debet ?>
		<td data-name="sa_debet">
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_sa_debet" class="form-group t_level4_sa_debet">
<input type="text" data-table="t_level4" data-field="x_sa_debet" name="x<?php echo $t_level4_list->RowIndex ?>_sa_debet" id="x<?php echo $t_level4_list->RowIndex ?>_sa_debet" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->sa_debet->getPlaceHolder()) ?>" value="<?php echo $t_level4->sa_debet->EditValue ?>"<?php echo $t_level4->sa_debet->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_sa_debet" name="o<?php echo $t_level4_list->RowIndex ?>_sa_debet" id="o<?php echo $t_level4_list->RowIndex ?>_sa_debet" value="<?php echo ew_HtmlEncode($t_level4->sa_debet->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->sa_kredit->Visible) { // sa_kredit ?>
		<td data-name="sa_kredit">
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_sa_kredit" class="form-group t_level4_sa_kredit">
<input type="text" data-table="t_level4" data-field="x_sa_kredit" name="x<?php echo $t_level4_list->RowIndex ?>_sa_kredit" id="x<?php echo $t_level4_list->RowIndex ?>_sa_kredit" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->sa_kredit->getPlaceHolder()) ?>" value="<?php echo $t_level4->sa_kredit->EditValue ?>"<?php echo $t_level4->sa_kredit->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_sa_kredit" name="o<?php echo $t_level4_list->RowIndex ?>_sa_kredit" id="o<?php echo $t_level4_list->RowIndex ?>_sa_kredit" value="<?php echo ew_HtmlEncode($t_level4->sa_kredit->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->jurnal->Visible) { // jurnal ?>
		<td data-name="jurnal">
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_jurnal" class="form-group t_level4_jurnal">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_jurnal" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_jurnal" data-value-separator="<?php echo $t_level4->jurnal->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_jurnal" id="x<?php echo $t_level4_list->RowIndex ?>_jurnal" value="{value}"<?php echo $t_level4->jurnal->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_jurnal" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->jurnal->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_jurnal") ?>
</div></div>
</span>
<input type="hidden" data-table="t_level4" data-field="x_jurnal" name="o<?php echo $t_level4_list->RowIndex ?>_jurnal" id="o<?php echo $t_level4_list->RowIndex ?>_jurnal" value="<?php echo ew_HtmlEncode($t_level4->jurnal->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->jurnal_kode->Visible) { // jurnal_kode ?>
		<td data-name="jurnal_kode">
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_jurnal_kode" class="form-group t_level4_jurnal_kode">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_jurnal_kode" data-value-separator="<?php echo $t_level4->jurnal_kode->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" id="x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" value="{value}"<?php echo $t_level4->jurnal_kode->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->jurnal_kode->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_jurnal_kode") ?>
</div></div>
</span>
<input type="hidden" data-table="t_level4" data-field="x_jurnal_kode" name="o<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" id="o<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" value="<?php echo ew_HtmlEncode($t_level4->jurnal_kode->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->neraca->Visible) { // neraca ?>
		<td data-name="neraca">
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_neraca" class="form-group t_level4_neraca">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_neraca" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_neraca" data-value-separator="<?php echo $t_level4->neraca->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_neraca" id="x<?php echo $t_level4_list->RowIndex ?>_neraca" value="{value}"<?php echo $t_level4->neraca->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_neraca" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->neraca->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_neraca") ?>
</div></div>
</span>
<input type="hidden" data-table="t_level4" data-field="x_neraca" name="o<?php echo $t_level4_list->RowIndex ?>_neraca" id="o<?php echo $t_level4_list->RowIndex ?>_neraca" value="<?php echo ew_HtmlEncode($t_level4->neraca->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->labarugi->Visible) { // labarugi ?>
		<td data-name="labarugi">
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_labarugi" class="form-group t_level4_labarugi">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_labarugi" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_labarugi" data-value-separator="<?php echo $t_level4->labarugi->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_labarugi" id="x<?php echo $t_level4_list->RowIndex ?>_labarugi" value="{value}"<?php echo $t_level4->labarugi->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_labarugi" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->labarugi->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_labarugi") ?>
</div></div>
</span>
<input type="hidden" data-table="t_level4" data-field="x_labarugi" name="o<?php echo $t_level4_list->RowIndex ?>_labarugi" id="o<?php echo $t_level4_list->RowIndex ?>_labarugi" value="<?php echo ew_HtmlEncode($t_level4->labarugi->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$t_level4_list->ListOptions->Render("body", "right", $t_level4_list->RowCnt);
?>
<script type="text/javascript">
ft_level4list.UpdateOpts(<?php echo $t_level4_list->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
<?php
if ($t_level4->ExportAll && $t_level4->Export <> "") {
	$t_level4_list->StopRec = $t_level4_list->TotalRecs;
} else {

	// Set the last record to display
	if ($t_level4_list->TotalRecs > $t_level4_list->StartRec + $t_level4_list->DisplayRecs - 1)
		$t_level4_list->StopRec = $t_level4_list->StartRec + $t_level4_list->DisplayRecs - 1;
	else
		$t_level4_list->StopRec = $t_level4_list->TotalRecs;
}

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($t_level4_list->FormKeyCountName) && ($t_level4->CurrentAction == "gridadd" || $t_level4->CurrentAction == "gridedit" || $t_level4->CurrentAction == "F")) {
		$t_level4_list->KeyCount = $objForm->GetValue($t_level4_list->FormKeyCountName);
		$t_level4_list->StopRec = $t_level4_list->StartRec + $t_level4_list->KeyCount - 1;
	}
}
$t_level4_list->RecCnt = $t_level4_list->StartRec - 1;
if ($t_level4_list->Recordset && !$t_level4_list->Recordset->EOF) {
	$t_level4_list->Recordset->MoveFirst();
	$bSelectLimit = $t_level4_list->UseSelectLimit;
	if (!$bSelectLimit && $t_level4_list->StartRec > 1)
		$t_level4_list->Recordset->Move($t_level4_list->StartRec - 1);
} elseif (!$t_level4->AllowAddDeleteRow && $t_level4_list->StopRec == 0) {
	$t_level4_list->StopRec = $t_level4->GridAddRowCount;
}

// Initialize aggregate
$t_level4->RowType = EW_ROWTYPE_AGGREGATEINIT;
$t_level4->ResetAttrs();
$t_level4_list->RenderRow();
$t_level4_list->EditRowCnt = 0;
if ($t_level4->CurrentAction == "edit")
	$t_level4_list->RowIndex = 1;
if ($t_level4->CurrentAction == "gridadd")
	$t_level4_list->RowIndex = 0;
if ($t_level4->CurrentAction == "gridedit")
	$t_level4_list->RowIndex = 0;
while ($t_level4_list->RecCnt < $t_level4_list->StopRec) {
	$t_level4_list->RecCnt++;
	if (intval($t_level4_list->RecCnt) >= intval($t_level4_list->StartRec)) {
		$t_level4_list->RowCnt++;
		if ($t_level4->CurrentAction == "gridadd" || $t_level4->CurrentAction == "gridedit" || $t_level4->CurrentAction == "F") {
			$t_level4_list->RowIndex++;
			$objForm->Index = $t_level4_list->RowIndex;
			if ($objForm->HasValue($t_level4_list->FormActionName))
				$t_level4_list->RowAction = strval($objForm->GetValue($t_level4_list->FormActionName));
			elseif ($t_level4->CurrentAction == "gridadd")
				$t_level4_list->RowAction = "insert";
			else
				$t_level4_list->RowAction = "";
		}

		// Set up key count
		$t_level4_list->KeyCount = $t_level4_list->RowIndex;

		// Init row class and style
		$t_level4->ResetAttrs();
		$t_level4->CssClass = "";
		if ($t_level4->CurrentAction == "gridadd") {
			$t_level4_list->LoadDefaultValues(); // Load default values
		} else {
			$t_level4_list->LoadRowValues($t_level4_list->Recordset); // Load row values
		}
		$t_level4->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($t_level4->CurrentAction == "gridadd") // Grid add
			$t_level4->RowType = EW_ROWTYPE_ADD; // Render add
		if ($t_level4->CurrentAction == "gridadd" && $t_level4->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$t_level4_list->RestoreCurrentRowFormValues($t_level4_list->RowIndex); // Restore form values
		if ($t_level4->CurrentAction == "edit") {
			if ($t_level4_list->CheckInlineEditKey() && $t_level4_list->EditRowCnt == 0) { // Inline edit
				$t_level4->RowType = EW_ROWTYPE_EDIT; // Render edit
			}
		}
		if ($t_level4->CurrentAction == "gridedit") { // Grid edit
			if ($t_level4->EventCancelled) {
				$t_level4_list->RestoreCurrentRowFormValues($t_level4_list->RowIndex); // Restore form values
			}
			if ($t_level4_list->RowAction == "insert")
				$t_level4->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$t_level4->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($t_level4->CurrentAction == "edit" && $t_level4->RowType == EW_ROWTYPE_EDIT && $t_level4->EventCancelled) { // Update failed
			$objForm->Index = 1;
			$t_level4_list->RestoreFormValues(); // Restore form values
		}
		if ($t_level4->CurrentAction == "gridedit" && ($t_level4->RowType == EW_ROWTYPE_EDIT || $t_level4->RowType == EW_ROWTYPE_ADD) && $t_level4->EventCancelled) // Update failed
			$t_level4_list->RestoreCurrentRowFormValues($t_level4_list->RowIndex); // Restore form values
		if ($t_level4->RowType == EW_ROWTYPE_EDIT) // Edit row
			$t_level4_list->EditRowCnt++;

		// Set up row id / data-rowindex
		$t_level4->RowAttrs = array_merge($t_level4->RowAttrs, array('data-rowindex'=>$t_level4_list->RowCnt, 'id'=>'r' . $t_level4_list->RowCnt . '_t_level4', 'data-rowtype'=>$t_level4->RowType));

		// Render row
		$t_level4_list->RenderRow();

		// Render list options
		$t_level4_list->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($t_level4_list->RowAction <> "delete" && $t_level4_list->RowAction <> "insertdelete" && !($t_level4_list->RowAction == "insert" && $t_level4->CurrentAction == "F" && $t_level4_list->EmptyRow())) {
?>
	<tr<?php echo $t_level4->RowAttributes() ?>>
<?php

// Render list options (body, left)
$t_level4_list->ListOptions->Render("body", "left", $t_level4_list->RowCnt);
?>
	<?php if ($t_level4->level1_id->Visible) { // level1_id ?>
		<td data-name="level1_id"<?php echo $t_level4->level1_id->CellAttributes() ?>>
<?php if ($t_level4->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level1_id" class="form-group t_level4_level1_id">
<?php
$wrkonchange = trim("ew_UpdateOpt.call(this); " . @$t_level4->level1_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$t_level4->level1_id->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $t_level4_list->RowIndex ?>_level1_id" style="white-space: nowrap; z-index: <?php echo (9000 - $t_level4_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="sv_x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo $t_level4->level1_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->level1_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($t_level4->level1_id->getPlaceHolder()) ?>"<?php echo $t_level4->level1_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level1_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $t_level4->level1_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo ew_HtmlEncode($t_level4->level1_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="q_x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo $t_level4->level1_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ft_level4list.CreateAutoSuggest({"id":"x<?php echo $t_level4_list->RowIndex ?>_level1_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($t_level4->level1_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $t_level4_list->RowIndex ?>_level1_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="s_x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo $t_level4->level1_id->LookupFilterQuery(false) ?>">
</span>
<input type="hidden" data-table="t_level4" data-field="x_level1_id" name="o<?php echo $t_level4_list->RowIndex ?>_level1_id" id="o<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo ew_HtmlEncode($t_level4->level1_id->OldValue) ?>">
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level1_id" class="form-group t_level4_level1_id">
<?php
$wrkonchange = trim("ew_UpdateOpt.call(this); " . @$t_level4->level1_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$t_level4->level1_id->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $t_level4_list->RowIndex ?>_level1_id" style="white-space: nowrap; z-index: <?php echo (9000 - $t_level4_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="sv_x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo $t_level4->level1_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->level1_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($t_level4->level1_id->getPlaceHolder()) ?>"<?php echo $t_level4->level1_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level1_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $t_level4->level1_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo ew_HtmlEncode($t_level4->level1_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="q_x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo $t_level4->level1_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ft_level4list.CreateAutoSuggest({"id":"x<?php echo $t_level4_list->RowIndex ?>_level1_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($t_level4->level1_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $t_level4_list->RowIndex ?>_level1_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="s_x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo $t_level4->level1_id->LookupFilterQuery(false) ?>">
</span>
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level1_id" class="t_level4_level1_id">
<span<?php echo $t_level4->level1_id->ViewAttributes() ?>>
<?php echo $t_level4->level1_id->ListViewValue() ?></span>
</span>
<?php } ?>
<a id="<?php echo $t_level4_list->PageObjName . "_row_" . $t_level4_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-table="t_level4" data-field="x_level4_id" name="x<?php echo $t_level4_list->RowIndex ?>_level4_id" id="x<?php echo $t_level4_list->RowIndex ?>_level4_id" value="<?php echo ew_HtmlEncode($t_level4->level4_id->CurrentValue) ?>">
<input type="hidden" data-table="t_level4" data-field="x_level4_id" name="o<?php echo $t_level4_list->RowIndex ?>_level4_id" id="o<?php echo $t_level4_list->RowIndex ?>_level4_id" value="<?php echo ew_HtmlEncode($t_level4->level4_id->OldValue) ?>">
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_EDIT || $t_level4->CurrentMode == "edit") { ?>
<input type="hidden" data-table="t_level4" data-field="x_level4_id" name="x<?php echo $t_level4_list->RowIndex ?>_level4_id" id="x<?php echo $t_level4_list->RowIndex ?>_level4_id" value="<?php echo ew_HtmlEncode($t_level4->level4_id->CurrentValue) ?>">
<?php } ?>
	<?php if ($t_level4->level2_id->Visible) { // level2_id ?>
		<td data-name="level2_id"<?php echo $t_level4->level2_id->CellAttributes() ?>>
<?php if ($t_level4->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level2_id" class="form-group t_level4_level2_id">
<?php
$wrkonchange = trim("ew_UpdateOpt.call(this); " . @$t_level4->level2_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$t_level4->level2_id->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $t_level4_list->RowIndex ?>_level2_id" style="white-space: nowrap; z-index: <?php echo (9000 - $t_level4_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="sv_x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo $t_level4->level2_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->level2_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($t_level4->level2_id->getPlaceHolder()) ?>"<?php echo $t_level4->level2_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level2_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $t_level4->level2_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo ew_HtmlEncode($t_level4->level2_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="q_x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo $t_level4->level2_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ft_level4list.CreateAutoSuggest({"id":"x<?php echo $t_level4_list->RowIndex ?>_level2_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($t_level4->level2_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $t_level4_list->RowIndex ?>_level2_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="s_x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo $t_level4->level2_id->LookupFilterQuery(false) ?>">
</span>
<input type="hidden" data-table="t_level4" data-field="x_level2_id" name="o<?php echo $t_level4_list->RowIndex ?>_level2_id" id="o<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo ew_HtmlEncode($t_level4->level2_id->OldValue) ?>">
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level2_id" class="form-group t_level4_level2_id">
<?php
$wrkonchange = trim("ew_UpdateOpt.call(this); " . @$t_level4->level2_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$t_level4->level2_id->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $t_level4_list->RowIndex ?>_level2_id" style="white-space: nowrap; z-index: <?php echo (9000 - $t_level4_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="sv_x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo $t_level4->level2_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->level2_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($t_level4->level2_id->getPlaceHolder()) ?>"<?php echo $t_level4->level2_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level2_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $t_level4->level2_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo ew_HtmlEncode($t_level4->level2_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="q_x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo $t_level4->level2_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ft_level4list.CreateAutoSuggest({"id":"x<?php echo $t_level4_list->RowIndex ?>_level2_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($t_level4->level2_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $t_level4_list->RowIndex ?>_level2_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="s_x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo $t_level4->level2_id->LookupFilterQuery(false) ?>">
</span>
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level2_id" class="t_level4_level2_id">
<span<?php echo $t_level4->level2_id->ViewAttributes() ?>>
<?php echo $t_level4->level2_id->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($t_level4->level3_id->Visible) { // level3_id ?>
		<td data-name="level3_id"<?php echo $t_level4->level3_id->CellAttributes() ?>>
<?php if ($t_level4->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level3_id" class="form-group t_level4_level3_id">
<?php
$wrkonchange = trim(" " . @$t_level4->level3_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$t_level4->level3_id->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $t_level4_list->RowIndex ?>_level3_id" style="white-space: nowrap; z-index: <?php echo (9000 - $t_level4_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="sv_x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo $t_level4->level3_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->level3_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($t_level4->level3_id->getPlaceHolder()) ?>"<?php echo $t_level4->level3_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level3_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $t_level4->level3_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo ew_HtmlEncode($t_level4->level3_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="q_x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo $t_level4->level3_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ft_level4list.CreateAutoSuggest({"id":"x<?php echo $t_level4_list->RowIndex ?>_level3_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($t_level4->level3_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $t_level4_list->RowIndex ?>_level3_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="s_x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo $t_level4->level3_id->LookupFilterQuery(false) ?>">
</span>
<input type="hidden" data-table="t_level4" data-field="x_level3_id" name="o<?php echo $t_level4_list->RowIndex ?>_level3_id" id="o<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo ew_HtmlEncode($t_level4->level3_id->OldValue) ?>">
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level3_id" class="form-group t_level4_level3_id">
<?php
$wrkonchange = trim(" " . @$t_level4->level3_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$t_level4->level3_id->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $t_level4_list->RowIndex ?>_level3_id" style="white-space: nowrap; z-index: <?php echo (9000 - $t_level4_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="sv_x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo $t_level4->level3_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->level3_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($t_level4->level3_id->getPlaceHolder()) ?>"<?php echo $t_level4->level3_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level3_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $t_level4->level3_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo ew_HtmlEncode($t_level4->level3_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="q_x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo $t_level4->level3_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ft_level4list.CreateAutoSuggest({"id":"x<?php echo $t_level4_list->RowIndex ?>_level3_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($t_level4->level3_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $t_level4_list->RowIndex ?>_level3_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="s_x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo $t_level4->level3_id->LookupFilterQuery(false) ?>">
</span>
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level3_id" class="t_level4_level3_id">
<span<?php echo $t_level4->level3_id->ViewAttributes() ?>>
<?php echo $t_level4->level3_id->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($t_level4->level4_no->Visible) { // level4_no ?>
		<td data-name="level4_no"<?php echo $t_level4->level4_no->CellAttributes() ?>>
<?php if ($t_level4->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level4_no" class="form-group t_level4_level4_no">
<input type="text" data-table="t_level4" data-field="x_level4_no" name="x<?php echo $t_level4_list->RowIndex ?>_level4_no" id="x<?php echo $t_level4_list->RowIndex ?>_level4_no" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($t_level4->level4_no->getPlaceHolder()) ?>" value="<?php echo $t_level4->level4_no->EditValue ?>"<?php echo $t_level4->level4_no->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level4_no" name="o<?php echo $t_level4_list->RowIndex ?>_level4_no" id="o<?php echo $t_level4_list->RowIndex ?>_level4_no" value="<?php echo ew_HtmlEncode($t_level4->level4_no->OldValue) ?>">
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level4_no" class="form-group t_level4_level4_no">
<input type="text" data-table="t_level4" data-field="x_level4_no" name="x<?php echo $t_level4_list->RowIndex ?>_level4_no" id="x<?php echo $t_level4_list->RowIndex ?>_level4_no" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($t_level4->level4_no->getPlaceHolder()) ?>" value="<?php echo $t_level4->level4_no->EditValue ?>"<?php echo $t_level4->level4_no->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level4_no" class="t_level4_level4_no">
<span<?php echo $t_level4->level4_no->ViewAttributes() ?>>
<?php echo $t_level4->level4_no->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($t_level4->level4_nama->Visible) { // level4_nama ?>
		<td data-name="level4_nama"<?php echo $t_level4->level4_nama->CellAttributes() ?>>
<?php if ($t_level4->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level4_nama" class="form-group t_level4_level4_nama">
<input type="text" data-table="t_level4" data-field="x_level4_nama" name="x<?php echo $t_level4_list->RowIndex ?>_level4_nama" id="x<?php echo $t_level4_list->RowIndex ?>_level4_nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($t_level4->level4_nama->getPlaceHolder()) ?>" value="<?php echo $t_level4->level4_nama->EditValue ?>"<?php echo $t_level4->level4_nama->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level4_nama" name="o<?php echo $t_level4_list->RowIndex ?>_level4_nama" id="o<?php echo $t_level4_list->RowIndex ?>_level4_nama" value="<?php echo ew_HtmlEncode($t_level4->level4_nama->OldValue) ?>">
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level4_nama" class="form-group t_level4_level4_nama">
<input type="text" data-table="t_level4" data-field="x_level4_nama" name="x<?php echo $t_level4_list->RowIndex ?>_level4_nama" id="x<?php echo $t_level4_list->RowIndex ?>_level4_nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($t_level4->level4_nama->getPlaceHolder()) ?>" value="<?php echo $t_level4->level4_nama->EditValue ?>"<?php echo $t_level4->level4_nama->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_level4_nama" class="t_level4_level4_nama">
<span<?php echo $t_level4->level4_nama->ViewAttributes() ?>>
<?php echo $t_level4->level4_nama->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($t_level4->sa_debet->Visible) { // sa_debet ?>
		<td data-name="sa_debet"<?php echo $t_level4->sa_debet->CellAttributes() ?>>
<?php if ($t_level4->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_sa_debet" class="form-group t_level4_sa_debet">
<input type="text" data-table="t_level4" data-field="x_sa_debet" name="x<?php echo $t_level4_list->RowIndex ?>_sa_debet" id="x<?php echo $t_level4_list->RowIndex ?>_sa_debet" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->sa_debet->getPlaceHolder()) ?>" value="<?php echo $t_level4->sa_debet->EditValue ?>"<?php echo $t_level4->sa_debet->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_sa_debet" name="o<?php echo $t_level4_list->RowIndex ?>_sa_debet" id="o<?php echo $t_level4_list->RowIndex ?>_sa_debet" value="<?php echo ew_HtmlEncode($t_level4->sa_debet->OldValue) ?>">
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_sa_debet" class="form-group t_level4_sa_debet">
<input type="text" data-table="t_level4" data-field="x_sa_debet" name="x<?php echo $t_level4_list->RowIndex ?>_sa_debet" id="x<?php echo $t_level4_list->RowIndex ?>_sa_debet" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->sa_debet->getPlaceHolder()) ?>" value="<?php echo $t_level4->sa_debet->EditValue ?>"<?php echo $t_level4->sa_debet->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_sa_debet" class="t_level4_sa_debet">
<span<?php echo $t_level4->sa_debet->ViewAttributes() ?>>
<?php echo $t_level4->sa_debet->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($t_level4->sa_kredit->Visible) { // sa_kredit ?>
		<td data-name="sa_kredit"<?php echo $t_level4->sa_kredit->CellAttributes() ?>>
<?php if ($t_level4->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_sa_kredit" class="form-group t_level4_sa_kredit">
<input type="text" data-table="t_level4" data-field="x_sa_kredit" name="x<?php echo $t_level4_list->RowIndex ?>_sa_kredit" id="x<?php echo $t_level4_list->RowIndex ?>_sa_kredit" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->sa_kredit->getPlaceHolder()) ?>" value="<?php echo $t_level4->sa_kredit->EditValue ?>"<?php echo $t_level4->sa_kredit->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_sa_kredit" name="o<?php echo $t_level4_list->RowIndex ?>_sa_kredit" id="o<?php echo $t_level4_list->RowIndex ?>_sa_kredit" value="<?php echo ew_HtmlEncode($t_level4->sa_kredit->OldValue) ?>">
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_sa_kredit" class="form-group t_level4_sa_kredit">
<input type="text" data-table="t_level4" data-field="x_sa_kredit" name="x<?php echo $t_level4_list->RowIndex ?>_sa_kredit" id="x<?php echo $t_level4_list->RowIndex ?>_sa_kredit" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->sa_kredit->getPlaceHolder()) ?>" value="<?php echo $t_level4->sa_kredit->EditValue ?>"<?php echo $t_level4->sa_kredit->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_sa_kredit" class="t_level4_sa_kredit">
<span<?php echo $t_level4->sa_kredit->ViewAttributes() ?>>
<?php echo $t_level4->sa_kredit->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($t_level4->jurnal->Visible) { // jurnal ?>
		<td data-name="jurnal"<?php echo $t_level4->jurnal->CellAttributes() ?>>
<?php if ($t_level4->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_jurnal" class="form-group t_level4_jurnal">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_jurnal" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_jurnal" data-value-separator="<?php echo $t_level4->jurnal->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_jurnal" id="x<?php echo $t_level4_list->RowIndex ?>_jurnal" value="{value}"<?php echo $t_level4->jurnal->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_jurnal" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->jurnal->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_jurnal") ?>
</div></div>
</span>
<input type="hidden" data-table="t_level4" data-field="x_jurnal" name="o<?php echo $t_level4_list->RowIndex ?>_jurnal" id="o<?php echo $t_level4_list->RowIndex ?>_jurnal" value="<?php echo ew_HtmlEncode($t_level4->jurnal->OldValue) ?>">
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_jurnal" class="form-group t_level4_jurnal">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_jurnal" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_jurnal" data-value-separator="<?php echo $t_level4->jurnal->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_jurnal" id="x<?php echo $t_level4_list->RowIndex ?>_jurnal" value="{value}"<?php echo $t_level4->jurnal->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_jurnal" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->jurnal->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_jurnal") ?>
</div></div>
</span>
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_jurnal" class="t_level4_jurnal">
<span<?php echo $t_level4->jurnal->ViewAttributes() ?>>
<?php echo $t_level4->jurnal->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($t_level4->jurnal_kode->Visible) { // jurnal_kode ?>
		<td data-name="jurnal_kode"<?php echo $t_level4->jurnal_kode->CellAttributes() ?>>
<?php if ($t_level4->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_jurnal_kode" class="form-group t_level4_jurnal_kode">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_jurnal_kode" data-value-separator="<?php echo $t_level4->jurnal_kode->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" id="x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" value="{value}"<?php echo $t_level4->jurnal_kode->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->jurnal_kode->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_jurnal_kode") ?>
</div></div>
</span>
<input type="hidden" data-table="t_level4" data-field="x_jurnal_kode" name="o<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" id="o<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" value="<?php echo ew_HtmlEncode($t_level4->jurnal_kode->OldValue) ?>">
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_jurnal_kode" class="form-group t_level4_jurnal_kode">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_jurnal_kode" data-value-separator="<?php echo $t_level4->jurnal_kode->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" id="x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" value="{value}"<?php echo $t_level4->jurnal_kode->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->jurnal_kode->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_jurnal_kode") ?>
</div></div>
</span>
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_jurnal_kode" class="t_level4_jurnal_kode">
<span<?php echo $t_level4->jurnal_kode->ViewAttributes() ?>>
<?php echo $t_level4->jurnal_kode->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($t_level4->neraca->Visible) { // neraca ?>
		<td data-name="neraca"<?php echo $t_level4->neraca->CellAttributes() ?>>
<?php if ($t_level4->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_neraca" class="form-group t_level4_neraca">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_neraca" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_neraca" data-value-separator="<?php echo $t_level4->neraca->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_neraca" id="x<?php echo $t_level4_list->RowIndex ?>_neraca" value="{value}"<?php echo $t_level4->neraca->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_neraca" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->neraca->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_neraca") ?>
</div></div>
</span>
<input type="hidden" data-table="t_level4" data-field="x_neraca" name="o<?php echo $t_level4_list->RowIndex ?>_neraca" id="o<?php echo $t_level4_list->RowIndex ?>_neraca" value="<?php echo ew_HtmlEncode($t_level4->neraca->OldValue) ?>">
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_neraca" class="form-group t_level4_neraca">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_neraca" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_neraca" data-value-separator="<?php echo $t_level4->neraca->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_neraca" id="x<?php echo $t_level4_list->RowIndex ?>_neraca" value="{value}"<?php echo $t_level4->neraca->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_neraca" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->neraca->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_neraca") ?>
</div></div>
</span>
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_neraca" class="t_level4_neraca">
<span<?php echo $t_level4->neraca->ViewAttributes() ?>>
<?php echo $t_level4->neraca->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($t_level4->labarugi->Visible) { // labarugi ?>
		<td data-name="labarugi"<?php echo $t_level4->labarugi->CellAttributes() ?>>
<?php if ($t_level4->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_labarugi" class="form-group t_level4_labarugi">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_labarugi" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_labarugi" data-value-separator="<?php echo $t_level4->labarugi->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_labarugi" id="x<?php echo $t_level4_list->RowIndex ?>_labarugi" value="{value}"<?php echo $t_level4->labarugi->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_labarugi" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->labarugi->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_labarugi") ?>
</div></div>
</span>
<input type="hidden" data-table="t_level4" data-field="x_labarugi" name="o<?php echo $t_level4_list->RowIndex ?>_labarugi" id="o<?php echo $t_level4_list->RowIndex ?>_labarugi" value="<?php echo ew_HtmlEncode($t_level4->labarugi->OldValue) ?>">
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_labarugi" class="form-group t_level4_labarugi">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_labarugi" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_labarugi" data-value-separator="<?php echo $t_level4->labarugi->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_labarugi" id="x<?php echo $t_level4_list->RowIndex ?>_labarugi" value="{value}"<?php echo $t_level4->labarugi->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_labarugi" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->labarugi->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_labarugi") ?>
</div></div>
</span>
<?php } ?>
<?php if ($t_level4->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $t_level4_list->RowCnt ?>_t_level4_labarugi" class="t_level4_labarugi">
<span<?php echo $t_level4->labarugi->ViewAttributes() ?>>
<?php echo $t_level4->labarugi->ListViewValue() ?></span>
</span>
<?php } ?>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$t_level4_list->ListOptions->Render("body", "right", $t_level4_list->RowCnt);
?>
	</tr>
<?php if ($t_level4->RowType == EW_ROWTYPE_ADD || $t_level4->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
ft_level4list.UpdateOpts(<?php echo $t_level4_list->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($t_level4->CurrentAction <> "gridadd")
		if (!$t_level4_list->Recordset->EOF) $t_level4_list->Recordset->MoveNext();
}
?>
<?php
	if ($t_level4->CurrentAction == "gridadd" || $t_level4->CurrentAction == "gridedit") {
		$t_level4_list->RowIndex = '$rowindex$';
		$t_level4_list->LoadDefaultValues();

		// Set row properties
		$t_level4->ResetAttrs();
		$t_level4->RowAttrs = array_merge($t_level4->RowAttrs, array('data-rowindex'=>$t_level4_list->RowIndex, 'id'=>'r0_t_level4', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($t_level4->RowAttrs["class"], "ewTemplate");
		$t_level4->RowType = EW_ROWTYPE_ADD;

		// Render row
		$t_level4_list->RenderRow();

		// Render list options
		$t_level4_list->RenderListOptions();
		$t_level4_list->StartRowCnt = 0;
?>
	<tr<?php echo $t_level4->RowAttributes() ?>>
<?php

// Render list options (body, left)
$t_level4_list->ListOptions->Render("body", "left", $t_level4_list->RowIndex);
?>
	<?php if ($t_level4->level1_id->Visible) { // level1_id ?>
		<td data-name="level1_id">
<span id="el$rowindex$_t_level4_level1_id" class="form-group t_level4_level1_id">
<?php
$wrkonchange = trim("ew_UpdateOpt.call(this); " . @$t_level4->level1_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$t_level4->level1_id->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $t_level4_list->RowIndex ?>_level1_id" style="white-space: nowrap; z-index: <?php echo (9000 - $t_level4_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="sv_x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo $t_level4->level1_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->level1_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($t_level4->level1_id->getPlaceHolder()) ?>"<?php echo $t_level4->level1_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level1_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $t_level4->level1_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo ew_HtmlEncode($t_level4->level1_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="q_x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo $t_level4->level1_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ft_level4list.CreateAutoSuggest({"id":"x<?php echo $t_level4_list->RowIndex ?>_level1_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($t_level4->level1_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $t_level4_list->RowIndex ?>_level1_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $t_level4_list->RowIndex ?>_level1_id" id="s_x<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo $t_level4->level1_id->LookupFilterQuery(false) ?>">
</span>
<input type="hidden" data-table="t_level4" data-field="x_level1_id" name="o<?php echo $t_level4_list->RowIndex ?>_level1_id" id="o<?php echo $t_level4_list->RowIndex ?>_level1_id" value="<?php echo ew_HtmlEncode($t_level4->level1_id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->level2_id->Visible) { // level2_id ?>
		<td data-name="level2_id">
<span id="el$rowindex$_t_level4_level2_id" class="form-group t_level4_level2_id">
<?php
$wrkonchange = trim("ew_UpdateOpt.call(this); " . @$t_level4->level2_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$t_level4->level2_id->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $t_level4_list->RowIndex ?>_level2_id" style="white-space: nowrap; z-index: <?php echo (9000 - $t_level4_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="sv_x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo $t_level4->level2_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->level2_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($t_level4->level2_id->getPlaceHolder()) ?>"<?php echo $t_level4->level2_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level2_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $t_level4->level2_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo ew_HtmlEncode($t_level4->level2_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="q_x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo $t_level4->level2_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ft_level4list.CreateAutoSuggest({"id":"x<?php echo $t_level4_list->RowIndex ?>_level2_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($t_level4->level2_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $t_level4_list->RowIndex ?>_level2_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $t_level4_list->RowIndex ?>_level2_id" id="s_x<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo $t_level4->level2_id->LookupFilterQuery(false) ?>">
</span>
<input type="hidden" data-table="t_level4" data-field="x_level2_id" name="o<?php echo $t_level4_list->RowIndex ?>_level2_id" id="o<?php echo $t_level4_list->RowIndex ?>_level2_id" value="<?php echo ew_HtmlEncode($t_level4->level2_id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->level3_id->Visible) { // level3_id ?>
		<td data-name="level3_id">
<span id="el$rowindex$_t_level4_level3_id" class="form-group t_level4_level3_id">
<?php
$wrkonchange = trim(" " . @$t_level4->level3_id->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$t_level4->level3_id->EditAttrs["onchange"] = "";
?>
<span id="as_x<?php echo $t_level4_list->RowIndex ?>_level3_id" style="white-space: nowrap; z-index: <?php echo (9000 - $t_level4_list->RowCnt * 10) ?>">
	<input type="text" name="sv_x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="sv_x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo $t_level4->level3_id->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->level3_id->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($t_level4->level3_id->getPlaceHolder()) ?>"<?php echo $t_level4->level3_id->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level3_id" data-multiple="0" data-lookup="1" data-value-separator="<?php echo $t_level4->level3_id->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo ew_HtmlEncode($t_level4->level3_id->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<input type="hidden" name="q_x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="q_x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo $t_level4->level3_id->LookupFilterQuery(true) ?>">
<script type="text/javascript">
ft_level4list.CreateAutoSuggest({"id":"x<?php echo $t_level4_list->RowIndex ?>_level3_id","forceSelect":true});
</script>
<button type="button" title="<?php echo ew_HtmlEncode(str_replace("%s", ew_RemoveHtml($t_level4->level3_id->FldCaption()), $Language->Phrase("LookupLink", TRUE))) ?>" onclick="ew_ModalLookupShow({lnk:this,el:'x<?php echo $t_level4_list->RowIndex ?>_level3_id',m:0,n:10,srch:false});" class="ewLookupBtn btn btn-default btn-sm"><span class="glyphicon glyphicon-search ewIcon"></span></button>
<input type="hidden" name="s_x<?php echo $t_level4_list->RowIndex ?>_level3_id" id="s_x<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo $t_level4->level3_id->LookupFilterQuery(false) ?>">
</span>
<input type="hidden" data-table="t_level4" data-field="x_level3_id" name="o<?php echo $t_level4_list->RowIndex ?>_level3_id" id="o<?php echo $t_level4_list->RowIndex ?>_level3_id" value="<?php echo ew_HtmlEncode($t_level4->level3_id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->level4_no->Visible) { // level4_no ?>
		<td data-name="level4_no">
<span id="el$rowindex$_t_level4_level4_no" class="form-group t_level4_level4_no">
<input type="text" data-table="t_level4" data-field="x_level4_no" name="x<?php echo $t_level4_list->RowIndex ?>_level4_no" id="x<?php echo $t_level4_list->RowIndex ?>_level4_no" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($t_level4->level4_no->getPlaceHolder()) ?>" value="<?php echo $t_level4->level4_no->EditValue ?>"<?php echo $t_level4->level4_no->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level4_no" name="o<?php echo $t_level4_list->RowIndex ?>_level4_no" id="o<?php echo $t_level4_list->RowIndex ?>_level4_no" value="<?php echo ew_HtmlEncode($t_level4->level4_no->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->level4_nama->Visible) { // level4_nama ?>
		<td data-name="level4_nama">
<span id="el$rowindex$_t_level4_level4_nama" class="form-group t_level4_level4_nama">
<input type="text" data-table="t_level4" data-field="x_level4_nama" name="x<?php echo $t_level4_list->RowIndex ?>_level4_nama" id="x<?php echo $t_level4_list->RowIndex ?>_level4_nama" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($t_level4->level4_nama->getPlaceHolder()) ?>" value="<?php echo $t_level4->level4_nama->EditValue ?>"<?php echo $t_level4->level4_nama->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_level4_nama" name="o<?php echo $t_level4_list->RowIndex ?>_level4_nama" id="o<?php echo $t_level4_list->RowIndex ?>_level4_nama" value="<?php echo ew_HtmlEncode($t_level4->level4_nama->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->sa_debet->Visible) { // sa_debet ?>
		<td data-name="sa_debet">
<span id="el$rowindex$_t_level4_sa_debet" class="form-group t_level4_sa_debet">
<input type="text" data-table="t_level4" data-field="x_sa_debet" name="x<?php echo $t_level4_list->RowIndex ?>_sa_debet" id="x<?php echo $t_level4_list->RowIndex ?>_sa_debet" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->sa_debet->getPlaceHolder()) ?>" value="<?php echo $t_level4->sa_debet->EditValue ?>"<?php echo $t_level4->sa_debet->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_sa_debet" name="o<?php echo $t_level4_list->RowIndex ?>_sa_debet" id="o<?php echo $t_level4_list->RowIndex ?>_sa_debet" value="<?php echo ew_HtmlEncode($t_level4->sa_debet->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->sa_kredit->Visible) { // sa_kredit ?>
		<td data-name="sa_kredit">
<span id="el$rowindex$_t_level4_sa_kredit" class="form-group t_level4_sa_kredit">
<input type="text" data-table="t_level4" data-field="x_sa_kredit" name="x<?php echo $t_level4_list->RowIndex ?>_sa_kredit" id="x<?php echo $t_level4_list->RowIndex ?>_sa_kredit" size="30" placeholder="<?php echo ew_HtmlEncode($t_level4->sa_kredit->getPlaceHolder()) ?>" value="<?php echo $t_level4->sa_kredit->EditValue ?>"<?php echo $t_level4->sa_kredit->EditAttributes() ?>>
</span>
<input type="hidden" data-table="t_level4" data-field="x_sa_kredit" name="o<?php echo $t_level4_list->RowIndex ?>_sa_kredit" id="o<?php echo $t_level4_list->RowIndex ?>_sa_kredit" value="<?php echo ew_HtmlEncode($t_level4->sa_kredit->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->jurnal->Visible) { // jurnal ?>
		<td data-name="jurnal">
<span id="el$rowindex$_t_level4_jurnal" class="form-group t_level4_jurnal">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_jurnal" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_jurnal" data-value-separator="<?php echo $t_level4->jurnal->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_jurnal" id="x<?php echo $t_level4_list->RowIndex ?>_jurnal" value="{value}"<?php echo $t_level4->jurnal->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_jurnal" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->jurnal->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_jurnal") ?>
</div></div>
</span>
<input type="hidden" data-table="t_level4" data-field="x_jurnal" name="o<?php echo $t_level4_list->RowIndex ?>_jurnal" id="o<?php echo $t_level4_list->RowIndex ?>_jurnal" value="<?php echo ew_HtmlEncode($t_level4->jurnal->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->jurnal_kode->Visible) { // jurnal_kode ?>
		<td data-name="jurnal_kode">
<span id="el$rowindex$_t_level4_jurnal_kode" class="form-group t_level4_jurnal_kode">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_jurnal_kode" data-value-separator="<?php echo $t_level4->jurnal_kode->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" id="x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" value="{value}"<?php echo $t_level4->jurnal_kode->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->jurnal_kode->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_jurnal_kode") ?>
</div></div>
</span>
<input type="hidden" data-table="t_level4" data-field="x_jurnal_kode" name="o<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" id="o<?php echo $t_level4_list->RowIndex ?>_jurnal_kode" value="<?php echo ew_HtmlEncode($t_level4->jurnal_kode->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->neraca->Visible) { // neraca ?>
		<td data-name="neraca">
<span id="el$rowindex$_t_level4_neraca" class="form-group t_level4_neraca">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_neraca" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_neraca" data-value-separator="<?php echo $t_level4->neraca->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_neraca" id="x<?php echo $t_level4_list->RowIndex ?>_neraca" value="{value}"<?php echo $t_level4->neraca->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_neraca" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->neraca->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_neraca") ?>
</div></div>
</span>
<input type="hidden" data-table="t_level4" data-field="x_neraca" name="o<?php echo $t_level4_list->RowIndex ?>_neraca" id="o<?php echo $t_level4_list->RowIndex ?>_neraca" value="<?php echo ew_HtmlEncode($t_level4->neraca->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($t_level4->labarugi->Visible) { // labarugi ?>
		<td data-name="labarugi">
<span id="el$rowindex$_t_level4_labarugi" class="form-group t_level4_labarugi">
<div id="tp_x<?php echo $t_level4_list->RowIndex ?>_labarugi" class="ewTemplate"><input type="radio" data-table="t_level4" data-field="x_labarugi" data-value-separator="<?php echo $t_level4->labarugi->DisplayValueSeparatorAttribute() ?>" name="x<?php echo $t_level4_list->RowIndex ?>_labarugi" id="x<?php echo $t_level4_list->RowIndex ?>_labarugi" value="{value}"<?php echo $t_level4->labarugi->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $t_level4_list->RowIndex ?>_labarugi" data-repeatcolumn="5" class="ewItemList" style="display: none;"><div>
<?php echo $t_level4->labarugi->RadioButtonListHtml(FALSE, "x{$t_level4_list->RowIndex}_labarugi") ?>
</div></div>
</span>
<input type="hidden" data-table="t_level4" data-field="x_labarugi" name="o<?php echo $t_level4_list->RowIndex ?>_labarugi" id="o<?php echo $t_level4_list->RowIndex ?>_labarugi" value="<?php echo ew_HtmlEncode($t_level4->labarugi->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$t_level4_list->ListOptions->Render("body", "right", $t_level4_list->RowCnt);
?>
<script type="text/javascript">
ft_level4list.UpdateOpts(<?php echo $t_level4_list->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($t_level4->CurrentAction == "add" || $t_level4->CurrentAction == "copy") { ?>
<input type="hidden" name="<?php echo $t_level4_list->FormKeyCountName ?>" id="<?php echo $t_level4_list->FormKeyCountName ?>" value="<?php echo $t_level4_list->KeyCount ?>">
<?php } ?>
<?php if ($t_level4->CurrentAction == "gridadd") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $t_level4_list->FormKeyCountName ?>" id="<?php echo $t_level4_list->FormKeyCountName ?>" value="<?php echo $t_level4_list->KeyCount ?>">
<?php echo $t_level4_list->MultiSelectKey ?>
<?php } ?>
<?php if ($t_level4->CurrentAction == "edit") { ?>
<input type="hidden" name="<?php echo $t_level4_list->FormKeyCountName ?>" id="<?php echo $t_level4_list->FormKeyCountName ?>" value="<?php echo $t_level4_list->KeyCount ?>">
<?php } ?>
<?php if ($t_level4->CurrentAction == "gridedit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $t_level4_list->FormKeyCountName ?>" id="<?php echo $t_level4_list->FormKeyCountName ?>" value="<?php echo $t_level4_list->KeyCount ?>">
<?php echo $t_level4_list->MultiSelectKey ?>
<?php } ?>
<?php if ($t_level4->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($t_level4_list->Recordset)
	$t_level4_list->Recordset->Close();
?>
<?php if ($t_level4->Export == "") { ?>
<div class="panel-footer ewGridLowerPanel">
<?php if ($t_level4->CurrentAction <> "gridadd" && $t_level4->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($t_level4_list->Pager)) $t_level4_list->Pager = new cPrevNextPager($t_level4_list->StartRec, $t_level4_list->DisplayRecs, $t_level4_list->TotalRecs) ?>
<?php if ($t_level4_list->Pager->RecordCount > 0 && $t_level4_list->Pager->Visible) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($t_level4_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $t_level4_list->PageUrl() ?>start=<?php echo $t_level4_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($t_level4_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $t_level4_list->PageUrl() ?>start=<?php echo $t_level4_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $t_level4_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($t_level4_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $t_level4_list->PageUrl() ?>start=<?php echo $t_level4_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($t_level4_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $t_level4_list->PageUrl() ?>start=<?php echo $t_level4_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $t_level4_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $t_level4_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $t_level4_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $t_level4_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
<?php if ($t_level4_list->TotalRecs > 0 && (!EW_AUTO_HIDE_PAGE_SIZE_SELECTOR || $t_level4_list->Pager->Visible)) { ?>
<div class="ewPager">
<input type="hidden" name="t" value="t_level4">
<select name="<?php echo EW_TABLE_REC_PER_PAGE ?>" class="form-control input-sm ewTooltip" title="<?php echo $Language->Phrase("RecordsPerPage") ?>" onchange="this.form.submit();">
<option value="10"<?php if ($t_level4_list->DisplayRecs == 10) { ?> selected<?php } ?>>10</option>
<option value="20"<?php if ($t_level4_list->DisplayRecs == 20) { ?> selected<?php } ?>>20</option>
<option value="50"<?php if ($t_level4_list->DisplayRecs == 50) { ?> selected<?php } ?>>50</option>
<option value="100"<?php if ($t_level4_list->DisplayRecs == 100) { ?> selected<?php } ?>>100</option>
<option value="200"<?php if ($t_level4_list->DisplayRecs == 200) { ?> selected<?php } ?>>200</option>
<option value="ALL"<?php if ($t_level4->getRecordsPerPage() == -1) { ?> selected<?php } ?>><?php echo $Language->Phrase("AllRecords") ?></option>
</select>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($t_level4_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($t_level4_list->TotalRecs == 0 && $t_level4->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($t_level4_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($t_level4->Export == "") { ?>
<script type="text/javascript">
ft_level4listsrch.FilterList = <?php echo $t_level4_list->GetFilterList() ?>;
ft_level4listsrch.Init();
ft_level4list.Init();
</script>
<?php } ?>
<?php
$t_level4_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($t_level4->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$t_level4_list->Page_Terminate();
?>
