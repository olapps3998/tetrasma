<?php

// Global variable for table object
$v_memorial = NULL;

//
// Table class for v_memorial
//
class cv_memorial extends cTable {
	var $detailm_id;
	var $jurnalm_id;
	var $no_buktim;
	var $tglm;
	var $ketm;
	var $akunm_id;
	var $nilaim_debet;
	var $nilaim_kredit;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'v_memorial';
		$this->TableName = 'v_memorial';
		$this->TableType = 'VIEW';

		// Update Table
		$this->UpdateTable = "`v_memorial`";
		$this->DBID = 'DB';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->ExportExcelPageOrientation = ""; // Page orientation (PHPExcel only)
		$this->ExportExcelPageSize = ""; // Page size (PHPExcel only)
		$this->DetailAdd = FALSE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->DetailView = FALSE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = 5;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// detailm_id
		$this->detailm_id = new cField('v_memorial', 'v_memorial', 'x_detailm_id', 'detailm_id', '`detailm_id`', '`detailm_id`', 3, -1, FALSE, '`detailm_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'NO');
		$this->detailm_id->Sortable = TRUE; // Allow sort
		$this->detailm_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['detailm_id'] = &$this->detailm_id;

		// jurnalm_id
		$this->jurnalm_id = new cField('v_memorial', 'v_memorial', 'x_jurnalm_id', 'jurnalm_id', '`jurnalm_id`', '`jurnalm_id`', 3, -1, FALSE, '`jurnalm_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->jurnalm_id->Sortable = TRUE; // Allow sort
		$this->jurnalm_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['jurnalm_id'] = &$this->jurnalm_id;

		// no_buktim
		$this->no_buktim = new cField('v_memorial', 'v_memorial', 'x_no_buktim', 'no_buktim', '`no_buktim`', '`no_buktim`', 200, -1, FALSE, '`no_buktim`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->no_buktim->Sortable = TRUE; // Allow sort
		$this->fields['no_buktim'] = &$this->no_buktim;

		// tglm
		$this->tglm = new cField('v_memorial', 'v_memorial', 'x_tglm', 'tglm', '`tglm`', ew_CastDateFieldForLike('`tglm`', 0, "DB"), 133, 0, FALSE, '`tglm`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->tglm->Sortable = TRUE; // Allow sort
		$this->tglm->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EW_DATE_FORMAT"], $Language->Phrase("IncorrectDate"));
		$this->fields['tglm'] = &$this->tglm;

		// ketm
		$this->ketm = new cField('v_memorial', 'v_memorial', 'x_ketm', 'ketm', '`ketm`', '`ketm`', 201, -1, FALSE, '`ketm`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXTAREA');
		$this->ketm->Sortable = TRUE; // Allow sort
		$this->fields['ketm'] = &$this->ketm;

		// akunm_id
		$this->akunm_id = new cField('v_memorial', 'v_memorial', 'x_akunm_id', 'akunm_id', '`akunm_id`', '`akunm_id`', 3, -1, FALSE, '`akunm_id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->akunm_id->Sortable = TRUE; // Allow sort
		$this->akunm_id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['akunm_id'] = &$this->akunm_id;

		// nilaim_debet
		$this->nilaim_debet = new cField('v_memorial', 'v_memorial', 'x_nilaim_debet', 'nilaim_debet', '`nilaim_debet`', '`nilaim_debet`', 20, -1, FALSE, '`nilaim_debet`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->nilaim_debet->Sortable = TRUE; // Allow sort
		$this->nilaim_debet->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['nilaim_debet'] = &$this->nilaim_debet;

		// nilaim_kredit
		$this->nilaim_kredit = new cField('v_memorial', 'v_memorial', 'x_nilaim_kredit', 'nilaim_kredit', '`nilaim_kredit`', '`nilaim_kredit`', 20, -1, FALSE, '`nilaim_kredit`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->nilaim_kredit->Sortable = TRUE; // Allow sort
		$this->nilaim_kredit->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['nilaim_kredit'] = &$this->nilaim_kredit;
	}

	// Set Field Visibility
	function SetFieldVisibility($fldparm) {
		global $Security;
		return $this->$fldparm->Visible; // Returns original value
	}

	// Multiple column sort
	function UpdateSort(&$ofld, $ctrl) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			if ($ctrl) {
				$sOrderBy = $this->getSessionOrderBy();
				if (strpos($sOrderBy, $sSortField . " " . $sLastSort) !== FALSE) {
					$sOrderBy = str_replace($sSortField . " " . $sLastSort, $sSortField . " " . $sThisSort, $sOrderBy);
				} else {
					if ($sOrderBy <> "") $sOrderBy .= ", ";
					$sOrderBy .= $sSortField . " " . $sThisSort;
				}
				$this->setSessionOrderBy($sOrderBy); // Save to Session
			} else {
				$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
			}
		} else {
			if (!$ctrl) $ofld->setSort("");
		}
	}

	// Table level SQL
	var $_SqlFrom = "";

	function getSqlFrom() { // From
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`v_memorial`";
	}

	function SqlFrom() { // For backward compatibility
		return $this->getSqlFrom();
	}

	function setSqlFrom($v) {
		$this->_SqlFrom = $v;
	}
	var $_SqlSelect = "";

	function getSqlSelect() { // Select
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
		return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
		$this->_SqlSelect = $v;
	}
	var $_SqlWhere = "";

	function getSqlWhere() { // Where
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
		$this->TableFilter = "";
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlWhere() { // For backward compatibility
		return $this->getSqlWhere();
	}

	function setSqlWhere($v) {
		$this->_SqlWhere = $v;
	}
	var $_SqlGroupBy = "";

	function getSqlGroupBy() { // Group By
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "";
	}

	function SqlGroupBy() { // For backward compatibility
		return $this->getSqlGroupBy();
	}

	function setSqlGroupBy($v) {
		$this->_SqlGroupBy = $v;
	}
	var $_SqlHaving = "";

	function getSqlHaving() { // Having
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}

	function SqlHaving() { // For backward compatibility
		return $this->getSqlHaving();
	}

	function setSqlHaving($v) {
		$this->_SqlHaving = $v;
	}
	var $_SqlOrderBy = "";

	function getSqlOrderBy() { // Order By
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "";
	}

	function SqlOrderBy() { // For backward compatibility
		return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
		$this->_SqlOrderBy = $v;
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = EW_USER_ID_ALLOW;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$sFilter, $sSort);
	}

	// Table SQL with List page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$this->Recordset_Selecting($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(),
			$this->getSqlHaving(), $this->getSqlOrderBy(), $sFilter, $sSort);
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->getSqlOrderBy(), "", $sSort);
	}

	// Try to get record count
	function TryGetRecordCount($sSql) {
		$cnt = -1;
		if (($this->TableType == 'TABLE' || $this->TableType == 'VIEW' || $this->TableType == 'LINKTABLE') && preg_match("/^SELECT \* FROM/i", $sSql)) {
			$sSql = "SELECT COUNT(*) FROM" . preg_replace('/^SELECT\s([\s\S]+)?\*\sFROM/i', "", $sSql);
			$sOrderBy = $this->GetOrderBy();
			if (substr($sSql, strlen($sOrderBy) * -1) == $sOrderBy)
				$sSql = substr($sSql, 0, strlen($sSql) - strlen($sOrderBy)); // Remove ORDER BY clause
		} else {
			$sSql = "SELECT COUNT(*) FROM (" . $sSql . ") EW_COUNT_TABLE";
		}
		$conn = &$this->Connection();
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($sFilter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $sFilter;
		$this->Recordset_Selecting($this->CurrentFilter);

		//$sSql = $this->SQL();
		$sSql = $this->GetSQL($this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function SelectRecordCount() {
		$sSql = $this->SelectSQL();
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			$conn = &$this->Connection();
			if ($rs = $conn->Execute($sSql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// INSERT statement
	function InsertSQL(&$rs) {
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]) || $this->fields[$name]->FldIsCustom)
				continue;
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType, $this->DBID) . ",";
		}
		while (substr($names, -1) == ",")
			$names = substr($names, 0, -1);
		while (substr($values, -1) == ",")
			$values = substr($values, 0, -1);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		$conn = &$this->Connection();
		$bInsert = $conn->Execute($this->InsertSQL($rs));
		if ($bInsert) {

			// Get insert id if necessary
			$this->detailm_id->setDbValue($conn->Insert_ID());
			$rs['detailm_id'] = $this->detailm_id->DbValue;
		}
		return $bInsert;
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "", $curfilter = TRUE) {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]) || $this->fields[$name]->FldIsCustom)
				continue;
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType, $this->DBID) . ",";
		}
		while (substr($sql, -1) == ",")
			$sql = substr($sql, 0, -1);
		$filter = ($curfilter) ? $this->CurrentFilter : "";
		if (is_array($where))
			$where = $this->ArrayToFilter($where);
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "", $rsold = NULL, $curfilter = TRUE) {
		$conn = &$this->Connection();
		$bUpdate = $conn->Execute($this->UpdateSQL($rs, $where, $curfilter));
		return $bUpdate;
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "", $curfilter = TRUE) {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if (is_array($where))
			$where = $this->ArrayToFilter($where);
		if ($rs) {
			if (array_key_exists('detailm_id', $rs))
				ew_AddFilter($where, ew_QuotedName('detailm_id', $this->DBID) . '=' . ew_QuotedValue($rs['detailm_id'], $this->detailm_id->FldDataType, $this->DBID));
		}
		$filter = ($curfilter) ? $this->CurrentFilter : "";
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "", $curfilter = TRUE) {
		$conn = &$this->Connection();
		$bDelete = $conn->Execute($this->DeleteSQL($rs, $where, $curfilter));
		return $bDelete;
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`detailm_id` = @detailm_id@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->detailm_id->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@detailm_id@", ew_AdjustSql($this->detailm_id->CurrentValue, $this->DBID), $sKeyFilter); // Replace key value
		return $sKeyFilter;
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "v_memoriallist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "v_memoriallist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("v_memorialview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("v_memorialview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "v_memorialadd.php?" . $this->UrlParm($parm);
		else
			$url = "v_memorialadd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("v_memorialedit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("v_memorialadd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("v_memorialdelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		return $url;
	}

	function KeyToJson() {
		$json = "";
		$json .= "detailm_id:" . ew_VarToJson($this->detailm_id->CurrentValue, "number", "'");
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->detailm_id->CurrentValue)) {
			$sUrl .= "detailm_id=" . urlencode($this->detailm_id->CurrentValue);
		} else {
			return "javascript:ew_Alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort());
			return $this->AddMasterUrl(ew_CurrentPage() . "?" . $sUrlParm);
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
		} elseif (!empty($_GET) || !empty($_POST)) {
			$isPost = ew_IsHttpPost();
			if ($isPost && isset($_POST["detailm_id"]))
				$arKeys[] = ew_StripSlashes($_POST["detailm_id"]);
			elseif (isset($_GET["detailm_id"]))
				$arKeys[] = ew_StripSlashes($_GET["detailm_id"]);
			else
				$arKeys = NULL; // Do not setup

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		if (is_array($arKeys)) {
			foreach ($arKeys as $key) {
				if (!is_numeric($key))
					continue;
				$ar[] = $key;
			}
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->detailm_id->CurrentValue = $key;
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$conn = &$this->Connection();
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
		$this->detailm_id->setDbValue($rs->fields('detailm_id'));
		$this->jurnalm_id->setDbValue($rs->fields('jurnalm_id'));
		$this->no_buktim->setDbValue($rs->fields('no_buktim'));
		$this->tglm->setDbValue($rs->fields('tglm'));
		$this->ketm->setDbValue($rs->fields('ketm'));
		$this->akunm_id->setDbValue($rs->fields('akunm_id'));
		$this->nilaim_debet->setDbValue($rs->fields('nilaim_debet'));
		$this->nilaim_kredit->setDbValue($rs->fields('nilaim_kredit'));
	}

	// Render list row values
	function RenderListRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// detailm_id
		// jurnalm_id
		// no_buktim
		// tglm
		// ketm
		// akunm_id
		// nilaim_debet
		// nilaim_kredit
		// detailm_id

		$this->detailm_id->ViewValue = $this->detailm_id->CurrentValue;
		$this->detailm_id->ViewCustomAttributes = "";

		// jurnalm_id
		$this->jurnalm_id->ViewValue = $this->jurnalm_id->CurrentValue;
		$this->jurnalm_id->ViewCustomAttributes = "";

		// no_buktim
		$this->no_buktim->ViewValue = $this->no_buktim->CurrentValue;
		$this->no_buktim->ViewCustomAttributes = "";

		// tglm
		$this->tglm->ViewValue = $this->tglm->CurrentValue;
		$this->tglm->ViewValue = ew_FormatDateTime($this->tglm->ViewValue, 0);
		$this->tglm->ViewCustomAttributes = "";

		// ketm
		$this->ketm->ViewValue = $this->ketm->CurrentValue;
		$this->ketm->ViewCustomAttributes = "";

		// akunm_id
		$this->akunm_id->ViewValue = $this->akunm_id->CurrentValue;
		$this->akunm_id->ViewCustomAttributes = "";

		// nilaim_debet
		$this->nilaim_debet->ViewValue = $this->nilaim_debet->CurrentValue;
		$this->nilaim_debet->ViewCustomAttributes = "";

		// nilaim_kredit
		$this->nilaim_kredit->ViewValue = $this->nilaim_kredit->CurrentValue;
		$this->nilaim_kredit->ViewCustomAttributes = "";

		// detailm_id
		$this->detailm_id->LinkCustomAttributes = "";
		$this->detailm_id->HrefValue = "";
		$this->detailm_id->TooltipValue = "";

		// jurnalm_id
		$this->jurnalm_id->LinkCustomAttributes = "";
		$this->jurnalm_id->HrefValue = "";
		$this->jurnalm_id->TooltipValue = "";

		// no_buktim
		$this->no_buktim->LinkCustomAttributes = "";
		$this->no_buktim->HrefValue = "";
		$this->no_buktim->TooltipValue = "";

		// tglm
		$this->tglm->LinkCustomAttributes = "";
		$this->tglm->HrefValue = "";
		$this->tglm->TooltipValue = "";

		// ketm
		$this->ketm->LinkCustomAttributes = "";
		$this->ketm->HrefValue = "";
		$this->ketm->TooltipValue = "";

		// akunm_id
		$this->akunm_id->LinkCustomAttributes = "";
		$this->akunm_id->HrefValue = "";
		$this->akunm_id->TooltipValue = "";

		// nilaim_debet
		$this->nilaim_debet->LinkCustomAttributes = "";
		$this->nilaim_debet->HrefValue = "";
		$this->nilaim_debet->TooltipValue = "";

		// nilaim_kredit
		$this->nilaim_kredit->LinkCustomAttributes = "";
		$this->nilaim_kredit->HrefValue = "";
		$this->nilaim_kredit->TooltipValue = "";

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Render edit row values
	function RenderEditRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

		// detailm_id
		$this->detailm_id->EditAttrs["class"] = "form-control";
		$this->detailm_id->EditCustomAttributes = "";
		$this->detailm_id->EditValue = $this->detailm_id->CurrentValue;
		$this->detailm_id->ViewCustomAttributes = "";

		// jurnalm_id
		$this->jurnalm_id->EditAttrs["class"] = "form-control";
		$this->jurnalm_id->EditCustomAttributes = "";
		$this->jurnalm_id->EditValue = $this->jurnalm_id->CurrentValue;
		$this->jurnalm_id->PlaceHolder = ew_RemoveHtml($this->jurnalm_id->FldCaption());

		// no_buktim
		$this->no_buktim->EditAttrs["class"] = "form-control";
		$this->no_buktim->EditCustomAttributes = "";
		$this->no_buktim->EditValue = $this->no_buktim->CurrentValue;
		$this->no_buktim->PlaceHolder = ew_RemoveHtml($this->no_buktim->FldCaption());

		// tglm
		$this->tglm->EditAttrs["class"] = "form-control";
		$this->tglm->EditCustomAttributes = "";
		$this->tglm->EditValue = ew_FormatDateTime($this->tglm->CurrentValue, 8);
		$this->tglm->PlaceHolder = ew_RemoveHtml($this->tglm->FldCaption());

		// ketm
		$this->ketm->EditAttrs["class"] = "form-control";
		$this->ketm->EditCustomAttributes = "";
		$this->ketm->EditValue = $this->ketm->CurrentValue;
		$this->ketm->PlaceHolder = ew_RemoveHtml($this->ketm->FldCaption());

		// akunm_id
		$this->akunm_id->EditAttrs["class"] = "form-control";
		$this->akunm_id->EditCustomAttributes = "";
		$this->akunm_id->EditValue = $this->akunm_id->CurrentValue;
		$this->akunm_id->PlaceHolder = ew_RemoveHtml($this->akunm_id->FldCaption());

		// nilaim_debet
		$this->nilaim_debet->EditAttrs["class"] = "form-control";
		$this->nilaim_debet->EditCustomAttributes = "";
		$this->nilaim_debet->EditValue = $this->nilaim_debet->CurrentValue;
		$this->nilaim_debet->PlaceHolder = ew_RemoveHtml($this->nilaim_debet->FldCaption());

		// nilaim_kredit
		$this->nilaim_kredit->EditAttrs["class"] = "form-control";
		$this->nilaim_kredit->EditCustomAttributes = "";
		$this->nilaim_kredit->EditValue = $this->nilaim_kredit->CurrentValue;
		$this->nilaim_kredit->PlaceHolder = ew_RemoveHtml($this->nilaim_kredit->FldCaption());

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {

		// Call Row Rendered event
		$this->Row_Rendered();
	}
	var $ExportDoc;

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;
		if (!$Doc->ExportCustom) {

			// Write header
			$Doc->ExportTableHeader();
			if ($Doc->Horizontal) { // Horizontal format, write header
				$Doc->BeginExportRow();
				if ($ExportPageType == "view") {
					if ($this->detailm_id->Exportable) $Doc->ExportCaption($this->detailm_id);
					if ($this->jurnalm_id->Exportable) $Doc->ExportCaption($this->jurnalm_id);
					if ($this->no_buktim->Exportable) $Doc->ExportCaption($this->no_buktim);
					if ($this->tglm->Exportable) $Doc->ExportCaption($this->tglm);
					if ($this->ketm->Exportable) $Doc->ExportCaption($this->ketm);
					if ($this->akunm_id->Exportable) $Doc->ExportCaption($this->akunm_id);
					if ($this->nilaim_debet->Exportable) $Doc->ExportCaption($this->nilaim_debet);
					if ($this->nilaim_kredit->Exportable) $Doc->ExportCaption($this->nilaim_kredit);
				} else {
					if ($this->detailm_id->Exportable) $Doc->ExportCaption($this->detailm_id);
					if ($this->jurnalm_id->Exportable) $Doc->ExportCaption($this->jurnalm_id);
					if ($this->no_buktim->Exportable) $Doc->ExportCaption($this->no_buktim);
					if ($this->tglm->Exportable) $Doc->ExportCaption($this->tglm);
					if ($this->akunm_id->Exportable) $Doc->ExportCaption($this->akunm_id);
					if ($this->nilaim_debet->Exportable) $Doc->ExportCaption($this->nilaim_debet);
					if ($this->nilaim_kredit->Exportable) $Doc->ExportCaption($this->nilaim_kredit);
				}
				$Doc->EndExportRow();
			}
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				if (!$Doc->ExportCustom) {
					$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
					if ($ExportPageType == "view") {
						if ($this->detailm_id->Exportable) $Doc->ExportField($this->detailm_id);
						if ($this->jurnalm_id->Exportable) $Doc->ExportField($this->jurnalm_id);
						if ($this->no_buktim->Exportable) $Doc->ExportField($this->no_buktim);
						if ($this->tglm->Exportable) $Doc->ExportField($this->tglm);
						if ($this->ketm->Exportable) $Doc->ExportField($this->ketm);
						if ($this->akunm_id->Exportable) $Doc->ExportField($this->akunm_id);
						if ($this->nilaim_debet->Exportable) $Doc->ExportField($this->nilaim_debet);
						if ($this->nilaim_kredit->Exportable) $Doc->ExportField($this->nilaim_kredit);
					} else {
						if ($this->detailm_id->Exportable) $Doc->ExportField($this->detailm_id);
						if ($this->jurnalm_id->Exportable) $Doc->ExportField($this->jurnalm_id);
						if ($this->no_buktim->Exportable) $Doc->ExportField($this->no_buktim);
						if ($this->tglm->Exportable) $Doc->ExportField($this->tglm);
						if ($this->akunm_id->Exportable) $Doc->ExportField($this->akunm_id);
						if ($this->nilaim_debet->Exportable) $Doc->ExportField($this->nilaim_debet);
						if ($this->nilaim_kredit->Exportable) $Doc->ExportField($this->nilaim_kredit);
					}
					$Doc->EndExportRow();
				}
			}

			// Call Row Export server event
			if ($Doc->ExportCustom)
				$this->Row_Export($Recordset->fields);
			$Recordset->MoveNext();
		}
		if (!$Doc->ExportCustom) {
			$Doc->ExportTableFooter();
		}
	}

	// Get auto fill value
	function GetAutoFill($id, $val) {
		$rsarr = array();
		$rowcnt = 0;

		// Output
		if (is_array($rsarr) && $rowcnt > 0) {
			$fldcnt = count($rsarr[0]);
			for ($i = 0; $i < $rowcnt; $i++) {
				for ($j = 0; $j < $fldcnt; $j++) {
					$str = strval($rsarr[$i][$j]);
					$str = ew_ConvertToUtf8($str);
					if (isset($post["keepCRLF"])) {
						$str = str_replace(array("\r", "\n"), array("\\r", "\\n"), $str);
					} else {
						$str = str_replace(array("\r", "\n"), array(" ", " "), $str);
					}
					$rsarr[$i][$j] = $str;
				}
			}
			return ew_ArrayToJson($rsarr);
		} else {
			return FALSE;
		}
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here	
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Grid Inserting event
	function Grid_Inserting() {

		// Enter your code here
		// To reject grid insert, set return value to FALSE

		return TRUE;
	}

	// Grid Inserted event
	function Grid_Inserted($rsnew) {

		//echo "Grid Inserted";
	}

	// Grid Updating event
	function Grid_Updating($rsold) {

		// Enter your code here
		// To reject grid update, set return value to FALSE

		return TRUE;
	}

	// Grid Updated event
	function Grid_Updated($rsold, $rsnew) {

		//echo "Grid Updated";
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		//var_dump($fld->FldName, $fld->LookupFilters, $filter); // Uncomment to view the filter
		// Enter your code here

	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
