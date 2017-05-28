<?php

// Global variable for table object
$r_bukubesar2 = NULL;

//
// Table class for r_bukubesar2
//
class crr_bukubesar2 extends crTableBase {
	var $ShowGroupHeaderAsRow = FALSE;
	var $ShowCompactSummaryFooter = TRUE;
	var $detail_id;
	var $jurnal_id;
	var $no_bukti;
	var $tgl;
	var $ket;
	var $akun_id;
	var $debet;
	var $kredit;
	var $no_akun;
	var $nama_akun;
	var $no_nama_akun;
	var $sa_debet;
	var $sm_debet;
	var $sa_kredit;
	var $sm_kredit;

	//
	// Table class constructor
	//
	function __construct() {
		global $ReportLanguage, $gsLanguage;
		$this->TableVar = 'r_bukubesar2';
		$this->TableName = 'r_bukubesar2';
		$this->TableType = 'REPORT';
		$this->DBID = 'DB';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0;

		// detail_id
		$this->detail_id = new crField('r_bukubesar2', 'r_bukubesar2', 'x_detail_id', 'detail_id', '`detail_id`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->detail_id->Sortable = TRUE; // Allow sort
		$this->detail_id->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['detail_id'] = &$this->detail_id;
		$this->detail_id->DateFilter = "";
		$this->detail_id->SqlSelect = "";
		$this->detail_id->SqlOrderBy = "";

		// jurnal_id
		$this->jurnal_id = new crField('r_bukubesar2', 'r_bukubesar2', 'x_jurnal_id', 'jurnal_id', '`jurnal_id`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->jurnal_id->Sortable = TRUE; // Allow sort
		$this->jurnal_id->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['jurnal_id'] = &$this->jurnal_id;
		$this->jurnal_id->DateFilter = "";
		$this->jurnal_id->SqlSelect = "";
		$this->jurnal_id->SqlOrderBy = "";

		// no_bukti
		$this->no_bukti = new crField('r_bukubesar2', 'r_bukubesar2', 'x_no_bukti', 'no_bukti', '`no_bukti`', 200, EWR_DATATYPE_STRING, -1);
		$this->no_bukti->Sortable = TRUE; // Allow sort
		$this->fields['no_bukti'] = &$this->no_bukti;
		$this->no_bukti->DateFilter = "";
		$this->no_bukti->SqlSelect = "";
		$this->no_bukti->SqlOrderBy = "";

		// tgl
		$this->tgl = new crField('r_bukubesar2', 'r_bukubesar2', 'x_tgl', 'tgl', '`tgl`', 133, EWR_DATATYPE_DATE, 0);
		$this->tgl->Sortable = TRUE; // Allow sort
		$this->tgl->FldDefaultErrMsg = str_replace("%s", $GLOBALS["EWR_DATE_FORMAT"], $ReportLanguage->Phrase("IncorrectDate"));
		$this->fields['tgl'] = &$this->tgl;
		$this->tgl->DateFilter = "";
		$this->tgl->SqlSelect = "";
		$this->tgl->SqlOrderBy = "";

		// ket
		$this->ket = new crField('r_bukubesar2', 'r_bukubesar2', 'x_ket', 'ket', '`ket`', 201, EWR_DATATYPE_MEMO, -1);
		$this->ket->Sortable = TRUE; // Allow sort
		$this->fields['ket'] = &$this->ket;
		$this->ket->DateFilter = "";
		$this->ket->SqlSelect = "";
		$this->ket->SqlOrderBy = "";

		// akun_id
		$this->akun_id = new crField('r_bukubesar2', 'r_bukubesar2', 'x_akun_id', 'akun_id', '`akun_id`', 3, EWR_DATATYPE_NUMBER, -1);
		$this->akun_id->Sortable = TRUE; // Allow sort
		$this->akun_id->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['akun_id'] = &$this->akun_id;
		$this->akun_id->DateFilter = "";
		$this->akun_id->SqlSelect = "";
		$this->akun_id->SqlOrderBy = "";

		// debet
		$this->debet = new crField('r_bukubesar2', 'r_bukubesar2', 'x_debet', 'debet', '`debet`', 20, EWR_DATATYPE_NUMBER, -1);
		$this->debet->Sortable = TRUE; // Allow sort
		$this->debet->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['debet'] = &$this->debet;
		$this->debet->DateFilter = "";
		$this->debet->SqlSelect = "";
		$this->debet->SqlOrderBy = "";

		// kredit
		$this->kredit = new crField('r_bukubesar2', 'r_bukubesar2', 'x_kredit', 'kredit', '`kredit`', 20, EWR_DATATYPE_NUMBER, -1);
		$this->kredit->Sortable = TRUE; // Allow sort
		$this->kredit->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectInteger");
		$this->fields['kredit'] = &$this->kredit;
		$this->kredit->DateFilter = "";
		$this->kredit->SqlSelect = "";
		$this->kredit->SqlOrderBy = "";

		// no_akun
		$this->no_akun = new crField('r_bukubesar2', 'r_bukubesar2', 'x_no_akun', 'no_akun', '`no_akun`', 200, EWR_DATATYPE_STRING, -1);
		$this->no_akun->Sortable = TRUE; // Allow sort
		$this->fields['no_akun'] = &$this->no_akun;
		$this->no_akun->DateFilter = "";
		$this->no_akun->SqlSelect = "";
		$this->no_akun->SqlOrderBy = "";

		// nama_akun
		$this->nama_akun = new crField('r_bukubesar2', 'r_bukubesar2', 'x_nama_akun', 'nama_akun', '`nama_akun`', 200, EWR_DATATYPE_STRING, -1);
		$this->nama_akun->Sortable = TRUE; // Allow sort
		$this->fields['nama_akun'] = &$this->nama_akun;
		$this->nama_akun->DateFilter = "";
		$this->nama_akun->SqlSelect = "";
		$this->nama_akun->SqlOrderBy = "";

		// no_nama_akun
		$this->no_nama_akun = new crField('r_bukubesar2', 'r_bukubesar2', 'x_no_nama_akun', 'no_nama_akun', '`no_nama_akun`', 200, EWR_DATATYPE_STRING, -1);
		$this->no_nama_akun->Sortable = TRUE; // Allow sort
		$this->no_nama_akun->GroupingFieldId = 1;
		$this->no_nama_akun->ShowGroupHeaderAsRow = $this->ShowGroupHeaderAsRow;
		$this->no_nama_akun->ShowCompactSummaryFooter = $this->ShowCompactSummaryFooter;
		$this->fields['no_nama_akun'] = &$this->no_nama_akun;
		$this->no_nama_akun->DateFilter = "";
		$this->no_nama_akun->SqlSelect = "";
		$this->no_nama_akun->SqlOrderBy = "";
		$this->no_nama_akun->FldGroupByType = "";
		$this->no_nama_akun->FldGroupInt = "0";
		$this->no_nama_akun->FldGroupSql = "";

		// sa_debet
		$this->sa_debet = new crField('r_bukubesar2', 'r_bukubesar2', 'x_sa_debet', 'sa_debet', '`sa_debet`', 4, EWR_DATATYPE_NUMBER, -1);
		$this->sa_debet->Sortable = TRUE; // Allow sort
		$this->sa_debet->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectFloat");
		$this->fields['sa_debet'] = &$this->sa_debet;
		$this->sa_debet->DateFilter = "";
		$this->sa_debet->SqlSelect = "";
		$this->sa_debet->SqlOrderBy = "";

		// sm_debet
		$this->sm_debet = new crField('r_bukubesar2', 'r_bukubesar2', 'x_sm_debet', 'sm_debet', '`sm_debet`', 4, EWR_DATATYPE_NUMBER, -1);
		$this->sm_debet->Sortable = TRUE; // Allow sort
		$this->sm_debet->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectFloat");
		$this->fields['sm_debet'] = &$this->sm_debet;
		$this->sm_debet->DateFilter = "";
		$this->sm_debet->SqlSelect = "";
		$this->sm_debet->SqlOrderBy = "";

		// sa_kredit
		$this->sa_kredit = new crField('r_bukubesar2', 'r_bukubesar2', 'x_sa_kredit', 'sa_kredit', '`sa_kredit`', 4, EWR_DATATYPE_NUMBER, -1);
		$this->sa_kredit->Sortable = TRUE; // Allow sort
		$this->sa_kredit->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectFloat");
		$this->fields['sa_kredit'] = &$this->sa_kredit;
		$this->sa_kredit->DateFilter = "";
		$this->sa_kredit->SqlSelect = "";
		$this->sa_kredit->SqlOrderBy = "";

		// sm_kredit
		$this->sm_kredit = new crField('r_bukubesar2', 'r_bukubesar2', 'x_sm_kredit', 'sm_kredit', '`sm_kredit`', 4, EWR_DATATYPE_NUMBER, -1);
		$this->sm_kredit->Sortable = TRUE; // Allow sort
		$this->sm_kredit->FldDefaultErrMsg = $ReportLanguage->Phrase("IncorrectFloat");
		$this->fields['sm_kredit'] = &$this->sm_kredit;
		$this->sm_kredit->DateFilter = "";
		$this->sm_kredit->SqlSelect = "";
		$this->sm_kredit->SqlOrderBy = "";
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
			if ($ofld->GroupingFieldId == 0) {
				if ($ctrl) {
					$sOrderBy = $this->getDetailOrderBy();
					if (strpos($sOrderBy, $sSortField . " " . $sLastSort) !== FALSE) {
						$sOrderBy = str_replace($sSortField . " " . $sLastSort, $sSortField . " " . $sThisSort, $sOrderBy);
					} else {
						if ($sOrderBy <> "") $sOrderBy .= ", ";
						$sOrderBy .= $sSortField . " " . $sThisSort;
					}
					$this->setDetailOrderBy($sOrderBy); // Save to Session
				} else {
					$this->setDetailOrderBy($sSortField . " " . $sThisSort); // Save to Session
				}
			}
		} else {
			if ($ofld->GroupingFieldId == 0 && !$ctrl) $ofld->setSort("");
		}
	}

	// Get Sort SQL
	function SortSql() {
		$sDtlSortSql = $this->getDetailOrderBy(); // Get ORDER BY for detail fields from session
		$argrps = array();
		foreach ($this->fields as $fld) {
			if ($fld->getSort() <> "") {
				$fldsql = $fld->FldExpression;
				if ($fld->GroupingFieldId > 0) {
					if ($fld->FldGroupSql <> "")
						$argrps[$fld->GroupingFieldId] = str_replace("%s", $fldsql, $fld->FldGroupSql) . " " . $fld->getSort();
					else
						$argrps[$fld->GroupingFieldId] = $fldsql . " " . $fld->getSort();
				}
			}
		}
		$sSortSql = "";
		foreach ($argrps as $grp) {
			if ($sSortSql <> "") $sSortSql .= ", ";
			$sSortSql .= $grp;
		}
		if ($sDtlSortSql <> "") {
			if ($sSortSql <> "") $sSortSql .= ", ";
			$sSortSql .= $sDtlSortSql;
		}
		return $sSortSql;
	}

	// Table level SQL
	// From

	var $_SqlFrom = "";

	function getSqlFrom() {
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`v_bukubesar`";
	}

	function SqlFrom() { // For backward compatibility
		return $this->getSqlFrom();
	}

	function setSqlFrom($v) {
		$this->_SqlFrom = $v;
	}

	// Select
	var $_SqlSelect = "";

	function getSqlSelect() {
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
		return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
		$this->_SqlSelect = $v;
	}

	// Where
	var $_SqlWhere = "";

	function getSqlWhere() {
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
		return $sWhere;
	}

	function SqlWhere() { // For backward compatibility
		return $this->getSqlWhere();
	}

	function setSqlWhere($v) {
		$this->_SqlWhere = $v;
	}

	// Group By
	var $_SqlGroupBy = "";

	function getSqlGroupBy() {
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "";
	}

	function SqlGroupBy() { // For backward compatibility
		return $this->getSqlGroupBy();
	}

	function setSqlGroupBy($v) {
		$this->_SqlGroupBy = $v;
	}

	// Having
	var $_SqlHaving = "";

	function getSqlHaving() {
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}

	function SqlHaving() { // For backward compatibility
		return $this->getSqlHaving();
	}

	function setSqlHaving($v) {
		$this->_SqlHaving = $v;
	}

	// Order By
	var $_SqlOrderBy = "";

	function getSqlOrderBy() {
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "`no_nama_akun` ASC";
	}

	function SqlOrderBy() { // For backward compatibility
		return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
		$this->_SqlOrderBy = $v;
	}

	// Table Level Group SQL
	// First Group Field

	var $_SqlFirstGroupField = "";

	function getSqlFirstGroupField() {
		return ($this->_SqlFirstGroupField <> "") ? $this->_SqlFirstGroupField : "`no_nama_akun`";
	}

	function SqlFirstGroupField() { // For backward compatibility
		return $this->getSqlFirstGroupField();
	}

	function setSqlFirstGroupField($v) {
		$this->_SqlFirstGroupField = $v;
	}

	// Select Group
	var $_SqlSelectGroup = "";

	function getSqlSelectGroup() {
		return ($this->_SqlSelectGroup <> "") ? $this->_SqlSelectGroup : "SELECT DISTINCT " . $this->getSqlFirstGroupField() . " FROM " . $this->getSqlFrom();
	}

	function SqlSelectGroup() { // For backward compatibility
		return $this->getSqlSelectGroup();
	}

	function setSqlSelectGroup($v) {
		$this->_SqlSelectGroup = $v;
	}

	// Order By Group
	var $_SqlOrderByGroup = "";

	function getSqlOrderByGroup() {
		return ($this->_SqlOrderByGroup <> "") ? $this->_SqlOrderByGroup : "`no_nama_akun` ASC";
	}

	function SqlOrderByGroup() { // For backward compatibility
		return $this->getSqlOrderByGroup();
	}

	function setSqlOrderByGroup($v) {
		$this->_SqlOrderByGroup = $v;
	}

	// Select Aggregate
	var $_SqlSelectAgg = "";

	function getSqlSelectAgg() {
		return ($this->_SqlSelectAgg <> "") ? $this->_SqlSelectAgg : "SELECT SUM(`debet`) AS `sum_debet`, SUM(`kredit`) AS `sum_kredit` FROM " . $this->getSqlFrom();
	}

	function SqlSelectAgg() { // For backward compatibility
		return $this->getSqlSelectAgg();
	}

	function setSqlSelectAgg($v) {
		$this->_SqlSelectAgg = $v;
	}

	// Aggregate Prefix
	var $_SqlAggPfx = "";

	function getSqlAggPfx() {
		return ($this->_SqlAggPfx <> "") ? $this->_SqlAggPfx : "";
	}

	function SqlAggPfx() { // For backward compatibility
		return $this->getSqlAggPfx();
	}

	function setSqlAggPfx($v) {
		$this->_SqlAggPfx = $v;
	}

	// Aggregate Suffix
	var $_SqlAggSfx = "";

	function getSqlAggSfx() {
		return ($this->_SqlAggSfx <> "") ? $this->_SqlAggSfx : "";
	}

	function SqlAggSfx() { // For backward compatibility
		return $this->getSqlAggSfx();
	}

	function setSqlAggSfx($v) {
		$this->_SqlAggSfx = $v;
	}

	// Select Count
	var $_SqlSelectCount = "";

	function getSqlSelectCount() {
		return ($this->_SqlSelectCount <> "") ? $this->_SqlSelectCount : "SELECT COUNT(*) FROM " . $this->getSqlFrom();
	}

	function SqlSelectCount() { // For backward compatibility
		return $this->getSqlSelectCount();
	}

	function setSqlSelectCount($v) {
		$this->_SqlSelectCount = $v;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {

			//$sUrlParm = "order=" . urlencode($fld->FldName) . "&ordertype=" . $fld->ReverseSort();
			$sUrlParm = "order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort();
			return ewr_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Setup lookup filters of a field
	function SetupLookupFilters($fld) {
		global $gsLanguage;
		switch ($fld->FldVar) {
		}
	}

	// Setup AutoSuggest filters of a field
	function SetupAutoSuggestFilters($fld) {
		global $gsLanguage;
		switch ($fld->FldVar) {
		}
	}

	// Table level events
	// Page Selecting event
	function Page_Selecting(&$filter) {

		// Enter your code here
	}

	// Page Breaking event
	function Page_Breaking(&$break, &$content) {

		// Example:
		//$break = FALSE; // Skip page break, or
		//$content = "<div style=\"page-break-after:always;\">&nbsp;</div>"; // Modify page break content

	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here
	}

	// Cell Rendered event
	function Cell_Rendered(&$Field, $CurrentValue, &$ViewValue, &$ViewAttrs, &$CellAttrs, &$HrefValue, &$LinkAttrs) {

		//$ViewValue = "xxx";
		//$ViewAttrs["style"] = "xxx";

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

	// Load Filters event
	function Page_FilterLoad() {

		// Enter your code here
		// Example: Register/Unregister Custom Extended Filter
		//ewr_RegisterFilter($this-><Field>, 'StartsWithA', 'Starts With A', 'GetStartsWithAFilter'); // With function, or
		//ewr_RegisterFilter($this-><Field>, 'StartsWithA', 'Starts With A'); // No function, use Page_Filtering event
		//ewr_UnregisterFilter($this-><Field>, 'StartsWithA');

	}

	// Page Filter Validated event
	function Page_FilterValidated() {

		// Example:
		//$this->MyField1->SearchValue = "your search criteria"; // Search value

	}

	// Page Filtering event
	function Page_Filtering(&$fld, &$filter, $typ, $opr = "", $val = "", $cond = "", $opr2 = "", $val2 = "") {

		// Note: ALWAYS CHECK THE FILTER TYPE ($typ)! Example:
		//if ($typ == "dropdown" && $fld->FldName == "MyField") // Dropdown filter
		//	$filter = "..."; // Modify the filter
		//if ($typ == "extended" && $fld->FldName == "MyField") // Extended filter
		//	$filter = "..."; // Modify the filter
		//if ($typ == "popup" && $fld->FldName == "MyField") // Popup filter
		//	$filter = "..."; // Modify the filter
		//if ($typ == "custom" && $opr == "..." && $fld->FldName == "MyField") // Custom filter, $opr is the custom filter ID
		//	$filter = "..."; // Modify the filter

	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		// Enter your code here
	}
}
?>
