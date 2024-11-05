<?
include('../includes/common.php');
$con = connect();
$commercial_head = array(1 => "Negotiation Loan/Liability", 5 => "BTB Margin/DFC/BLO/DAD/RAD/FBPAR A/C", 6 => "ERQ A/C", 10 => "CD Account", 11 => "STD A/C", 15 => "CC Account", 16 => "OD A/C", 20 => "Packing Credit", 21 => "Bi-Salam/PC", 22 => "Export Cash Credit", 30 => "EDF A/C", 31 => "PAD", 32 => " LTR/MPI", 33 => "FTT/FDD/TR", 34 => "LIM", 35 => "Term Loan", 36 => "Force Loan", 40 => "ABP Liability", 45 => "Bank Charge", 46 => "SWIFT Charge", 47 => "Postage Charge", 48 => "Handling Charge", 49 => "Source Tax", 50 => "Excise Duty", 51 => "Foreign Collection Charge", 60 => "Other Charge", 61 => "Foreign Commission", 62 => "Local  Commission", 63 => "Penalty on Doc Discrepancy", 64 => "Penalty on Goods Discrepancy", 65 => "FDBC Commission", 70 => "Interest", 71 => "Import Margin A/C", 75 => "Discount A/C", 76 => "Advance A/C", 80 => "HPSM", 81 => "Sundry A/C", 82 => "MDA Special", 83 => "MDA UR", 84 => "Vat On Bank Commission", 85 => "FDR Build up", 86 => "Miscellaneous Charge", 87 => "others Fund[sinking]/Free Fund", 88 => "Bank Commission", 89 => "VAT", 90 => "Insurance Coverage", 91 => "Add Confirmation Change", 92 => "MDA Normal", 93 => "Settlement A/C", 94 => "Cash Security A/C", 95 => "Loan A/C", 96 => "Courier Charge", 97 => "Telex Charge", 98 => "Application Form Fee", 99 => "UPAS", 100 => "Offshore", 101 => "Stationary", 102 => "Stamp Charge", 103 => "Amendment Charge", 104 => "Long Term Loan-Secured", 105 => "Long Term Loan-Unsecured", 106 => "Demand Loan", 107 => "SOD", 108 => "Pre-Shipment Finance", 109 => "Post-Shipment Finance", 110 => "Pre-Import Finance", 111 => "Bank Guarantee Charge", 112 => "VAT on SWIFT Charge", 114 => "VAT on Add Confirmation Charge", 115 => "VAT on LC Application Form Fee", 116 => "VAT on Stamp Charge", 117 => "VAT on Bank Guarantee Charge", 118 => "VAT on Miscellaneous Charge", 119 => "Post-Import Finance", 120 => "Cash Incentive loan", 121 => "Additional Tax", 122 => "Exp Charge", 123 => "Special Notice Deposit [SND]", 124 => "Local Collection Charge", 125 => "Central Fund", 126 => "Re-Imbursement Payment", 127 => "Retirement", 128 => "Overdue interest", 129 => "RMG", 130 => "Export Reserve Margin", 131 => "BTB Margin[Foreign]", 132 => "BTB Margin[Local]", 133 => "BTB Margin [BUP]", 134 => "Advance Income Tax [AIT]", 135 => "Interest For Factoringg", 136 => "Late shipment penalty", 137 => "Late presentation charges", 138 => "Security For factoring", 139 => "LC Goods Releasing NOC Charge",140 => "TT/DD Charge",141 => "Accept Comm. Charge",142 => "UPASS / MIX UPASS",143 => "Outstanding Claim",144 => "Discounted to Buyer",145 => "CBM Discrepency",146 => "Late Inspection penalty",147 => "Short Realize/Shipment",148 => "Air Release Charges for Document delay",149 => "Buyer Discripency Fee",150 => "Negotiation Charge",151 => "Trade Sourcing Fee [TSF]",152 => "Product Liability Insurance [PLI]",153 => "Trade Commission for Service [TCS]",154 => "Shipment Endorsement fee/FCR Endorsement Fee",155 => "Online Transfer Charge",156 => "Commission In Lieu of Exchange [CILE]",157 => "Usance Commission",158 => "LC Transferring Charge",159 => "Document Examination Fee",160 => "Azo free cert/Te-Test report",161 => "Document Tracer charge",162 => "IBB A/C",163 => "MTR A/C",164 => "CC HYPO A/C",165 => "General A/C",166 => "Inspection Charge",167 => "Portal Charge",168 => "Libor Interest",169 => "LC Expire Charge",170 => "SFC A/C",171 => "SFC Special A/C",172 => "VAT on Courier",173 => "VAT on Commission",174 => "VAT on Postage Charge",176 => "Special Security Fund [SSF]",177 => "Cash In Advance",178 => "Document Processing Fees",179 => "Vat on Document Processing Fees",180 => "Collection Charge on LDBC",181 => "Vat on Collection Charge", 182 => "Reimbursement Charge", 183 => "Acceptance Commission", 184 => "Negotiation/Collection/Lodgment Commission",185=>"Xs Margin",186=>"SND",187=>"FDR Build Up",188=>"FCAD A/C",189=>"DFC Local",190=>"FC[Exporter]",191=>"Welfare Fund",192=>"Tax on Local Commission" ,193=>"Trade Advance",194=>"Bill Discount",195=>"Short Term Loan",196=>"Time Loan",197=>"CM/Purchase" );

$insertCatID=true;
$id=return_next_id("id","lib_comm_head_list",1);
$i=1;

foreach($commercial_head as $head_id=>$head_name)
{
	//short_name, category_type, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, is_inventory, ac_period_dtls_id, period_ending_date
	$insertCatID=execute_query("insert into lib_comm_head_list (id, acc_head_id, actual_head_name, short_name, head_type, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, is_inventory, ac_period_dtls_id) values (".$id.",'".$head_id."','".$head_name."','".$head_name."','0','1','".$pc_date_time."','','',1,0,'0','0')");

	if($insertCatID){ $insertCatID=1; } else {echo "insert into lib_comm_head_list (id, acc_head_id, actual_head_name, short_name, head_type, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, is_inventory, ac_period_dtls_id) values (".$id.",'".$head_id."','".$head_name."','".$head_name."','0','1','".$pc_date_time."','','',1,0,'0','0')";oci_rollback($con);die;}
	$id++;$i++;
}
//echo count($tst_data)."<pre>";print_r($tst_data);die;
if($db_type==2)
{
	if($insertCatID)
	{
		oci_commit($con); 
		echo "Insert Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Insert Failed";
		die;
	}
}
?>