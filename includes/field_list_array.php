<?
//This function will return page wise array
function get_fieldlevel_arr( $index )
{
	global $fieldlevel_arr;
	$field_arr=array();
	foreach($fieldlevel_arr[$index] as $key=>$val)
	{
		$str = ($fieldlevel_arr['title'][$index][$key])?$fieldlevel_arr['title'][$index][$key]:ucwords (str_replace(array('cbo','txt','_'),' ',$val));
		$field_arr[$key]= $str;
	}
	return $field_arr;
}



// ##RULES## array[entry_form][sequence] = "Field Name"; ##

$fieldlevel_arr[18][1]="cbo_sales_order"; //Knit Finish Fabric Issue
$fieldlevel_arr[18][2]="txt_issue_date"; //Knit Finish Fabric Issue

$fieldlevel_arr[54][1]="cbo_is_sales"; //Finish Fabric Delivery to Store
$fieldlevel_arr[66][1]="txt_recv_date"; //Finish Fabric Delivery to Store
$fieldlevel_arr[68][1]="cbo_is_sales"; //Finish Fabric Roll Receive by Store
$fieldlevel_arr[70][1]="cbo_basis"; //yarn purchase requisition

// Yarn Dying with order
$fieldlevel_arr[41][1]="cbo_is_short";
// $fieldlevel_arr[41][2]="txt_dyeing_charge";


$fieldlevel_arr[727][1]="txt_receive_date";

$fieldlevel_arr[735][1]="cbo_ref_type";
$fieldlevel_arr['title'][735][1]="Reference Type";

$fieldlevel_arr[95][1]="txt_sewing_date";

$fieldlevel_arr[331][1]="txt_iron_date";
$fieldlevel_arr['title'][331][1]="Iron. Output Date";
 
$fieldlevel_arr[470][1]="txt_inp_date";
$fieldlevel_arr['title'][470][1]="Inp. Date";

$fieldlevel_arr[601][1]="txt_issue_date";

$fieldlevel_arr[681][1]="txt_cutting_date";
$fieldlevel_arr['title'][681][1]="Knitting Entry Date";
 
$fieldlevel_arr[682][1]="txt_sewing_date";
$fieldlevel_arr['title'][682][1]="Linking Date";

$fieldlevel_arr[683][1]="txt_trim_date";

$fieldlevel_arr[684][1]="txt_trim_date";
$fieldlevel_arr['title'][684][1]="Mend. Date";

$fieldlevel_arr[685][1]="txt_wash_date";

$fieldlevel_arr[712][1]="txt_poly_date";
$fieldlevel_arr['title'][712][1]="Atch Date";


$fieldlevel_arr[724][1]="txt_sewing_date";



$fieldlevel_arr[730][1]="txt_sewing_date";

$fieldlevel_arr[331][1]="txt_iron_date";

$fieldlevel_arr[686][1]="txt_finishing_date";

$fieldlevel_arr[326][1]="txt_issue_date";

$fieldlevel_arr[330][1]="txt_receive_date";

$fieldlevel_arr[726][1]="txt_trim_date";
$fieldlevel_arr['title'][726][1]="Get Up Date";
  
$fieldlevel_arr[725][1]="txt_trim_date";
$fieldlevel_arr['title'][725][1]="PQC. Date";

$fieldlevel_arr[723][1]="txt_inp_date";

$fieldlevel_arr[720][1]="txt_cutting_date";
// $fieldlevel_arr['title'][720][1]="Cutting QC Date";
 
$fieldlevel_arr[721][1]="txt_iron_date"; 
$fieldlevel_arr['title'][721][1]="Iron. Output Date"; 

$fieldlevel_arr[722][1]="txt_finishing_date"; 
 

// Main Fabric Booking
$fieldlevel_arr[86][1]="cbo_company_name";
$fieldlevel_arr[86][2]="cbo_buyer_name";
$fieldlevel_arr[86][3]="txt_job_no";
$fieldlevel_arr[86][4]="txt_booking_no";
$fieldlevel_arr[86][5]="cbo_fabric_natu";
$fieldlevel_arr[86][6]="cbo_fabric_source";
//$fieldlevel_arr[86][7]="cbo_currency";
//$fieldlevel_arr[86][8]="txt_exchange_rate";
$fieldlevel_arr[86][9]="cbo_pay_mode";
$fieldlevel_arr[86][10]="txt_booking_date";
//$fieldlevel_arr[86][11]="cbo_booking_month";
$fieldlevel_arr[86][12]="cbo_supplier_name";
$fieldlevel_arr[86][13]="cbo_supplier_name";
//$fieldlevel_arr[86][14]="txt_attention";
$fieldlevel_arr[86][15]="txt_delivery_date";
//$fieldlevel_arr[86][16]="cbo_source";
//$fieldlevel_arr[86][17]="cbo_booking_year";
//$fieldlevel_arr[86][18]="txt_booking_percent";
//$fieldlevel_arr[86][19]="txt_colar_excess_percent";
//$fieldlevel_arr[86][20]="txt_cuff_excess_percent";
//$fieldlevel_arr[86][21]="cbo_ready_to_approved";
$fieldlevel_arr[86][22]="processloss_breck_down";

//$fieldlevel_arr[86][24]="txt_intarnal_ref";
//$fieldlevel_arr[86][25]="txt_file_no";

//==knit========Multiple Job Wise Trims Booking V2===========02-02-2023======crm====2027==========

$fieldlevel_arr[87][1]="cbo_level";


// Short Fabric Booking
//$fieldlevel_arr[88][1]="txt_order_no_id";
//$fieldlevel_arr[88][2]="cbo_company_name";
//$fieldlevel_arr[88][3]="cbo_buyer_name";
//$fieldlevel_arr[88][4]="txt_job_no";
//$fieldlevel_arr[88][5]="txt_booking_no";
//$fieldlevel_arr[88][6]="cbo_fabric_natu";
//$fieldlevel_arr[88][7]="cbo_fabric_source";
//$fieldlevel_arr[88][8]="cbo_currency";
//$fieldlevel_arr[88][9]="txt_exchange_rate";
//$fieldlevel_arr[88][10]="cbo_pay_mode";
$fieldlevel_arr[88][11]="txt_booking_date";
//$fieldlevel_arr[88][12]="cbo_booking_month";
//$fieldlevel_arr[88][13]="cbo_supplier_name";
//$fieldlevel_arr[88][14]="txt_attention";
//$fieldlevel_arr[88][15]="txt_delivery_date";
//$fieldlevel_arr[88][16]="cbo_source";
//$fieldlevel_arr[88][17]="cbo_booking_year";
//$fieldlevel_arr[88][18]="cbo_ready_to_approved";
//$fieldlevel_arr[88][19]="cbo_order_id";
//$fieldlevel_arr[88][20]="cbo_fabricdescription_id";
//$fieldlevel_arr[88][1]="cbo_trim_type";
$fieldlevel_arr[88][21]="cbo_fabriccolor_id";
$fieldlevel_arr[88][22]="cbo_pay_mode";
//$fieldlevel_arr[88][22]="cbo_garmentscolor_id";
//$fieldlevel_arr[88][23]="txt_process_loss";
//$fieldlevel_arr[88][24]="txt_grey_qnty";
//$fieldlevel_arr[88][25]="txt_rate";
//$fieldlevel_arr[88][26]="txt_amount";
//$fieldlevel_arr[88][27]="txt_rmg_qty";
//$fieldlevel_arr[88][28]="cbo_responsible_dept";
//$fieldlevel_arr[88][29]="cbo_responsible_person";
//$fieldlevel_arr[88][30]="txt_reason";
//$fieldlevel_arr[88][31]="cbo_short_booking_type";
$fieldlevel_arr[88][32]="txt_department_no";
$fieldlevel_arr[88][33]="txt_profit_center";
$fieldlevel_arr[88][34]="txt_final_comment";




$fieldlevel_arr[89][1]="txt_order_no_id";
$fieldlevel_arr[89][2]="cbo_company_name";
$fieldlevel_arr[89][3]="cbo_buyer_name";
$fieldlevel_arr[89][4]="txt_job_no";
$fieldlevel_arr[89][5]="txt_booking_no";
$fieldlevel_arr[89][6]="cbo_fabric_natu";
$fieldlevel_arr[89][7]="cbo_fabric_source";
$fieldlevel_arr[89][8]="cbo_currency";
$fieldlevel_arr[89][9]="txt_exchange_rate";
$fieldlevel_arr[89][10]="cbo_pay_mode";
$fieldlevel_arr[89][11]="txt_booking_date";
$fieldlevel_arr[89][12]="cbo_booking_month";
$fieldlevel_arr[89][13]="cbo_supplier_name";
$fieldlevel_arr[89][14]="txt_attention";
$fieldlevel_arr[89][15]="txt_delivery_date";
$fieldlevel_arr[89][16]="cbo_source";
$fieldlevel_arr[89][17]="cbo_booking_year";
$fieldlevel_arr[89][18]="cbo_ready_to_approved";
$fieldlevel_arr[89][19]="cbo_ready_to_approved";


//Sample Fabric Booking -With order
$fieldlevel_arr[89][20]="cbo_order_id";
$fieldlevel_arr[89][21]="cbo_fabricdescription_id";
$fieldlevel_arr[89][22]="cbo_sample_type";
$fieldlevel_arr[89][23]="cbo_fabriccolor_id";
$fieldlevel_arr[89][24]="cbo_garmentscolor_id";
$fieldlevel_arr[89][25]="cbo_itemsize_id";
$fieldlevel_arr[89][26]="cbo_garmentssize_id";
$fieldlevel_arr[89][27]="txt_dia_width";
$fieldlevel_arr[89][28]="txt_finish_qnty";
$fieldlevel_arr[89][29]="txt_process_loss";
$fieldlevel_arr[89][30]="txt_grey_qnty";
$fieldlevel_arr[89][31]="txt_rate";
$fieldlevel_arr[89][32]="txt_amount";
$fieldlevel_arr[89][33]="txt_bh_qty";
$fieldlevel_arr[89][34]="txt_rf_qty";

// Sample Fabric Booking -Without order
$fieldlevel_arr[90][1]="cbo_company_name";
$fieldlevel_arr[90][2]="cbo_buyer_name";
$fieldlevel_arr[90][3]="txt_booking_no";
$fieldlevel_arr[90][4]="cbo_fabric_natu";
$fieldlevel_arr[90][5]="cbo_fabric_source";
$fieldlevel_arr[90][6]="cbo_currency";
//$fieldlevel_arr[90][7]="txt_exchange_rate";
$fieldlevel_arr[90][8]="cbo_pay_mode";
$fieldlevel_arr[90][9]="txt_booking_date";
$fieldlevel_arr[90][10]="cbo_supplier_name";
$fieldlevel_arr[90][11]="txt_attention";
$fieldlevel_arr[90][12]="txt_delivery_date";
$fieldlevel_arr[90][13]="cbo_source";
$fieldlevel_arr[90][14]="cbo_ready_to_approved";
$fieldlevel_arr[90][15]="cbo_team_leader";
$fieldlevel_arr[90][16]="cbo_dealing_merchant";
$fieldlevel_arr[90][17]="cbo_body_part";
$fieldlevel_arr[90][18]="cbo_color_type";
$fieldlevel_arr[90][19]="txt_style";
$fieldlevel_arr[90][20]="txt_style_des";
$fieldlevel_arr[90][21]="cbo_sample_type";
$fieldlevel_arr[90][22]="txt_fabricdescription";
$fieldlevel_arr[90][23]="txt_gsm";
$fieldlevel_arr[90][24]="txt_gmt_color";
$fieldlevel_arr[90][25]="cbo_itemsize_id";
$fieldlevel_arr[90][26]="txt_color";
$fieldlevel_arr[90][27]="txt_gmts_size";
$fieldlevel_arr[90][28]="txt_size";
$fieldlevel_arr[90][29]="txt_dia_width";
$fieldlevel_arr[90][30]="txt_finish_qnty";
$fieldlevel_arr[90][31]="txt_process_loss";
$fieldlevel_arr[90][32]="txt_grey_qnty";
$fieldlevel_arr[90][33]="txt_rate";
$fieldlevel_arr[90][34]="txt_amount";
$fieldlevel_arr[90][35]="txt_article_no";
$fieldlevel_arr[90][36]="txt_yarn_details";
$fieldlevel_arr[90][37]="txt_remarks";
$fieldlevel_arr[90][38]="cbo_body_type";
$fieldlevel_arr[90][39]="txt_item_qty";
$fieldlevel_arr[90][40]="txt_knitting_charge";
$fieldlevel_arr[90][41]="txt_bh_qty";
$fieldlevel_arr[90][42]="txt_rf_qty";


//94 = Yarn Service Work Order
$fieldlevel_arr[94][1]="cbo_is_sales_order";
$fieldlevel_arr[94][2]="cbo_with_order";
$fieldlevel_arr[94][3]="cbo_within_group";

$fieldlevel_arr[96][1]="cbo_working_company_name"; //Bundle Wise Sewing Input
$fieldlevel_arr[96][2]="cbo_input_date"; //Bundle Wise Sewing Input

//Knit Grey Fabric Issue
//$fieldlevel_arr[16][1]="cbo_basis"; 

// Erosin Entry
$fieldlevel_arr[741][1]="cbo_profit_center";
$fieldlevel_arr[741][2]="cbo_department";
$fieldlevel_arr[741][3]="txt_final_comments";


//98 = Knitting Production page....................................
$fieldlevel_arr[98][1]='txt_recieved_id';
$fieldlevel_arr[98][2]='cbo_company_id';
$fieldlevel_arr[98][3]='cbo_receive_basis';
$fieldlevel_arr[98][4]='txt_receive_date';
$fieldlevel_arr[98][5]='txt_receive_chal_no';
$fieldlevel_arr[98][6]='txt_booking_no_id';
$fieldlevel_arr[98][7]='txt_booking_no';
$fieldlevel_arr[98][8]='cbo_store_name';
$fieldlevel_arr[98][9]='cbo_knitting_source';
$fieldlevel_arr[98][10]='cbo_knitting_company';
$fieldlevel_arr[98][11]='cbo_location_name';
$fieldlevel_arr[98][12]='txt_yarn_issue_challan_no';
$fieldlevel_arr[98][13]='cbo_buyer_name';
$fieldlevel_arr[98][14]='txt_yarn_issued';
$fieldlevel_arr[98][15]='cbo_body_part';
$fieldlevel_arr[98][16]='txt_fabric_description';
$fieldlevel_arr[98][17]='fabric_desc_id';
$fieldlevel_arr[98][18]='txt_gsm';
$fieldlevel_arr[98][19]='txt_width';
$fieldlevel_arr[98][20]='cbo_floor_id';
$fieldlevel_arr[98][21]='cbo_machine_name';
$fieldlevel_arr[98][22]='txt_roll_no';
$fieldlevel_arr[98][23]='txt_remarks';
$fieldlevel_arr[98][24]='txt_receive_qnty';
$fieldlevel_arr[98][25]='txt_room';
$fieldlevel_arr[98][26]='txt_reject_fabric_recv_qnty';
$fieldlevel_arr[98][27]='txt_shift_name';
$fieldlevel_arr[98][28]='txt_rack';
$fieldlevel_arr[98][29]='cbo_uom';
$fieldlevel_arr[98][30]='txt_self';
$fieldlevel_arr[98][31]='txt_yarn_lot';
$fieldlevel_arr[98][32]='txt_binbox';
$fieldlevel_arr[98][33]='cbo_yarn_count';
$fieldlevel_arr[98][34]='txt_brand';
$fieldlevel_arr[98][35]='txt_color';
$fieldlevel_arr[98][36]='cbo_color_range';
$fieldlevel_arr[98][37]='txt_stitch_length';
$fieldlevel_arr[98][38]='txt_machine_dia';
$fieldlevel_arr[98][49]='txt_machine_gg';
$fieldlevel_arr[98][40]='fabric_store_auto_update';
$fieldlevel_arr[98][41]='knitting_charge_string';
$fieldlevel_arr[98][42]='cbo_sales_order'; 

//159 = SubCon Knitting Production page....................................
$fieldlevel_arr[159][1]='cbo_production_basis';

//Pro Forma Invoice
$fieldlevel_arr[104][1]="cbo_goods_rcv_status"; //Goods Rcv Status
//BTB/Margin LC
$fieldlevel_arr[105][1]="cbo_payterm_id"; //Pay Term
$fieldlevel_arr[105][2]="cbo_delevery_mode"; //Delivery Mode
$fieldlevel_arr[105][3]="cbo_inco_term_id"; //Incoterm
$fieldlevel_arr[105][4]="cbo_origin_id"; //Origin

//Export LC Entry
$fieldlevel_arr[106][1]="cbo_export_item_category"; //Export Item Category
$fieldlevel_arr[106][2]="txt_lc_value";
$fieldlevel_arr[106][3]="cbo_lc_type";
$fieldlevel_arr[106][4]="cbo_shipping_mode";
$fieldlevel_arr[106][5]="cbo_lc_source";

//Sales Contract Enty
$fieldlevel_arr[107][1]="cbo_export_item_category"; //Export Item Category
$fieldlevel_arr[107][2]="cbo_convertible_to_lc"; 
$fieldlevel_arr[107][3]="txt_contract_value"; 
//Partial Fabric Booking
$fieldlevel_arr[108][1]="cbouom"; //UOM
$fieldlevel_arr[108][2]="cbo_fabric_source"; //Fabric Source
$fieldlevel_arr[108][3]="cbo_pay_mode"; //Pay Mode 
$fieldlevel_arr[108][4]="cbo_fabric_natu";
$fieldlevel_arr[108][5]="txt_delivery_date"; //check tna
$fieldlevel_arr[108][6]="cbo_source"; //check Source
$fieldlevel_arr[108][7]="txt_booking_date"; //Booking Date





//woven partial fabric Booking
$fieldlevel_arr[271][1]="cbouom"; //UOM
$fieldlevel_arr[271][2]="cbo_fabric_source"; //Fabric Source
$fieldlevel_arr[271][3]="cbo_pay_mode"; //Pay Mode
$fieldlevel_arr[271][4]="cbo_fabric_natu";
$fieldlevel_arr[271][5]="txt_delivery_date"; //check tna
$fieldlevel_arr[271][6]="cbo_source"; //check Source
$fieldlevel_arr[271][7]="cbo_level"; //level

$fieldlevel_arr[109][1]="cbo_company_id";//Fabriic Sales Order Entry
$fieldlevel_arr[109][2]="cbo_within_group";
$fieldlevel_arr[109][3]="cbo_currency";
$fieldlevel_arr[109][4]="cbo_ship_mode";
$fieldlevel_arr[109][5]="cbo_sales_order_type";
$fieldlevel_arr[109][6]="cbo_location_name";

// Pre-Costing
//$fieldlevel_arr[111][1]="txt_common_oh_pre_cost"; 
//$fieldlevel_arr[111][2]="txt_depr_amor_pre_cost";

//Main fabric booking v2
$fieldlevel_arr[118][1]="cbo_pay_mode"; //pay mode
$fieldlevel_arr[118][2]="txt_delivery_date";
$fieldlevel_arr[118][3]="cbo_fabric_natu";
$fieldlevel_arr[118][4]="cbouom";
$fieldlevel_arr[118][5]="cbo_fabric_source";


$fieldlevel_arr[3][1]="cbo_sales_order"; // Yarn Requisition Search Form Field //txt_issue_date
$fieldlevel_arr[3][2]="txt_issue_date"; // Yarn Issue add issue id 7729
$fieldlevel_arr[3][3]="txt_btb_selection"; // Yarn Issue add issue id 7729
$fieldlevel_arr[3][4]="cbo_basis"; // Yarn Issue add issue id 5293
//$fieldlevel_arr[1][1]="txt_receive_date"; // Yarn Recv

$fieldlevel_arr[120][1]="cbo_within_group"; // Yarn Requisition Entry For Sales Pop up Within Group Field

$fieldlevel_arr[121][1]="cbo_produced_type"; // Cutting Entry
$fieldlevel_arr['title'][121][1]="Produced By"; // Cutting Entry
$fieldlevel_arr[121][2]="txt_cutting_date"; // Cutting Entry
 
//Order Update Entry
$fieldlevel_arr[122][1]="txt_Sewing_SMV"; 
$fieldlevel_arr[122][2]="txt_Cutting_SMV";
$fieldlevel_arr[122][3]="txt_Finish_SMV";
$fieldlevel_arr[122][4]="txt_avg_price";
$fieldlevel_arr[122][5]="cbo_order_status";
$fieldlevel_arr[122][6]="txt_po_no";
$fieldlevel_arr[122][7]="txt_pub_shipment_date";
$fieldlevel_arr[122][8]="txt_org_shipment_date";
//$fieldlevel_arr[122][9]="txt_extended_ship_date";

// Fabric Requisition For Batch 2
$fieldlevel_arr[123][1]="cbo_search_by"; //Order Update Entry
$fieldlevel_arr[553][1]="cbo_search_by"; //Fabric Requisition For Batch 3

// pre costing v2
$fieldlevel_arr[158][1]="cbo_fabric_costing_uom"; //uom fabric cost part
$fieldlevel_arr[158][2]="cbo_fabric_costing_fab_nature"; //Fab Nature fabric cost part
$fieldlevel_arr[158][3]="txt_sew_efficiency_per"; // Sew Efficiency %
$fieldlevel_arr[158][4]="cbo_costing_per"; // Sew Efficiency %
$fieldlevel_arr[158][5]="cbo_add_file"; // Add File
$fieldlevel_arr[158][6]="cbo_fabric_costing_fab_source"; // //Fab Source fabric cost part

// pre costing v3
$fieldlevel_arr[520][1]="cbo_fabric_costing_uom"; //uom fabric cost part
$fieldlevel_arr[520][2]="cbo_fabric_costing_fab_nature"; //Fab Nature fabric cost part
$fieldlevel_arr[520][3]="txt_sew_efficiency_per"; // Sew Efficiency %
$fieldlevel_arr[520][4]="cbo_costing_per"; // Sew Efficiency %
$fieldlevel_arr[520][5]="cbo_add_file"; // Add File
$fieldlevel_arr[520][6]="cbo_fabric_costing_fab_source"; // //Fab Source fabric cost part



// pre costing v2 Woven
$fieldlevel_arr[425][1]="cbo_fabric_costing_uom"; //uom fabric cost part
$fieldlevel_arr[425][2]="cbo_fabric_costing_fab_nature"; //Fab Nature fabric cost part
$fieldlevel_arr[425][3]="txt_sew_efficiency_per"; // Sew Efficiency %
$fieldlevel_arr[425][4]="cbo_costing_per"; // Sew Efficiency %
$fieldlevel_arr[425][5]="cbo_add_file"; // Add File
$fieldlevel_arr[425][6]="txt_costing_date"; // Add File

// pre costing v2- Sweater
$fieldlevel_arr[521][1]="cbo_fabric_costing_uom"; //uom fabric cost part
$fieldlevel_arr[521][2]="cbo_fabric_costing_fab_nature"; //Fab Nature fabric cost part
$fieldlevel_arr[521][3]="txt_sew_efficiency_per"; // Sew Efficiency %
$fieldlevel_arr[521][4]="cbo_costing_per"; // Sew Efficiency %
$fieldlevel_arr[521][5]="cbo_add_file"; // Add File
$fieldlevel_arr[521][6]="cbo_fabric_costing_fab_source"; // //Fab Source fabric cost part


// Sourcing Post Cost Sheet
$fieldlevel_arr[469][1]="txt_sourcing_date"; // txt_sourcing_date

// Batch Creation
$fieldlevel_arr[408][1]="cbo_double_dyeing"; 
$fieldlevel_arr[408][2]="txt_batch_date"; 




//Poly Entry
$fieldlevel_arr[164][1]="cbo_floor"; //sew.floor
// $fieldlevel_arr[164][2]="cbo_finishing_floor"; //fini.floor
$fieldlevel_arr[164][3]="cbo_color_type";
$fieldlevel_arr[164][4]="cbo_poly_line";

//Yarn Count Determination
$fieldlevel_arr[184][1]="cbo_fabric_nature"; //cbofabricnature

//Knitting Bill Issue
$fieldlevel_arr[186][1]="cbo_party_source"; //cbo_party_source


//Garments Delivery Entry
$fieldlevel_arr[198][1]="shipping_status";
$fieldlevel_arr[198][2]="txt_ex_factory_date";
$fieldlevel_arr[198][3]="cbo_delivery_location";
$fieldlevel_arr[198][4]="cbo_delivery_floor";

//Grey Fabric Roll Issue
$fieldlevel_arr[61][1]="cbo_dyeing_source";

//Garments Delivery Entry
$fieldlevel_arr[62][1]="txt_delivery_date";

//Purchase Requisition
$fieldlevel_arr[69][1]="txt_date_from";
$fieldlevel_arr['title'][69][1]="Req Date";
$fieldlevel_arr[69][2]="cbo_pay_mode";
$fieldlevel_arr[69][3]="cbo_currency_name";
$fieldlevel_arr[69][4]="itemsize_1";
$fieldlevel_arr['title'][69][4]="Item Size";
$fieldlevel_arr[69][5]="txtbrand_1";
$fieldlevel_arr['title'][69][5]="Brand";
$fieldlevel_arr[69][6]="txtmodelname_1";
$fieldlevel_arr['title'][69][6]="Model Name";

//General Item Receive Return
$fieldlevel_arr[26][1]="txt_return_date";
$fieldlevel_arr[26][2]="txt_challan_no";

//General Item Issue Return
$fieldlevel_arr[27][1]="txt_return_date";

//General Item Issue
$fieldlevel_arr[21][1]="cbo_department";
$fieldlevel_arr[21][2]="cbo_issue_source";
$fieldlevel_arr[21][3]="txt_issue_date";


//Others Purchase Order
//$fieldlevel_arr[103][1]="txt_wo_date";
$fieldlevel_arr[147][1]="cbo_pay_mode";
$fieldlevel_arr[147][2]="txt_wo_date";

//General Item Receive
$fieldlevel_arr[20][1]="txt_receive_date";
$fieldlevel_arr[20][2]="cbo_receive_basis";

//Gate pass entry
$fieldlevel_arr[251][1]="cbo_roll_by";
$fieldlevel_arr[251][2]="cbo_issue_purpose";
$fieldlevel_arr[251][3]="cbo_returnable";
$fieldlevel_arr[251][4]="txt_delivery_company";
$fieldlevel_arr[251][5]="txt_vhicle_number";
$fieldlevel_arr[251][6]="cbo_group";
$fieldlevel_arr[251][7]="cbo_basis";
$fieldlevel_arr[251][8]="txt_rece_date";
$fieldlevel_arr[251][9]="txt_start_hours";
$fieldlevel_arr[251][10]="txt_start_minuties";


// Finished Goods Order To Order Transfer
$fieldlevel_arr[485][1]="cbo_within_group";


//Gate In Entry
$fieldlevel_arr[363][1]="cbo_party_type";
$fieldlevel_arr[363][2]="cbo_out_company";
$fieldlevel_arr[363][3]="txt_receive_from";
$fieldlevel_arr[363][4]="cbo_group";
$fieldlevel_arr[363][5]="txt_start_hours";
$fieldlevel_arr[363][6]="txt_start_minuties"; 

//Export Invoice
$fieldlevel_arr[270][1]="shipping_mode";
$fieldlevel_arr[270][2]="cbo_country";
$fieldlevel_arr[270][3]="ex_factory_date";
// $fieldlevel_arr[270][4]="actual_po_wise_color_and_size";

//Planning Info Entry For Sales Order
$fieldlevel_arr[282][1]="cbo_within_group";
$fieldlevel_arr[282][2]="txt_batch_no";
$fieldlevel_arr[282][3]="txt_tube_ref_no";
$fieldlevel_arr[282][4]="cbo_knitting_source";
$fieldlevel_arr[282][5]="cbo_knitting_party";
$fieldlevel_arr[282][6]="txt_color";
$fieldlevel_arr[282][7]="cbo_color_range";
$fieldlevel_arr[282][8]="txt_machine_dia";
$fieldlevel_arr[282][9]="txt_fabric_dia";
$fieldlevel_arr[282][10]="txt_program_qnty";
$fieldlevel_arr[282][11]="txt_program_date";
$fieldlevel_arr[282][12]="txt_stitch_length";
$fieldlevel_arr[282][13]="txt_spandex_stitch_length";
$fieldlevel_arr[282][14]="txt_draft_ratio";
$fieldlevel_arr[282][15]="txt_machine_no";
$fieldlevel_arr[282][16]="txt_start_date";
$fieldlevel_arr[282][17]="txt_end_date";
$fieldlevel_arr[282][18]="cbo_knitting_status";
$fieldlevel_arr[282][19]="cbo_feeder";
$fieldlevel_arr[282][20]="txt_remarks";
$fieldlevel_arr[282][21]="cbo_location_name";
$fieldlevel_arr[282][22]="txt_no_of_ply"; 
$fieldlevel_arr[282][23]="txt_machine_gg";
$fieldlevel_arr[282][24]="cbo_dia_width_type";

 
//Price Quotation
//$fieldlevel_arr[314][1]="txt_common_oh_pre_cost";
//$fieldlevel_arr[314][2]="txt_income_tax_pre_cost";
$fieldlevel_arr[314][3]="cbo_costing_per";
$fieldlevel_arr[314][4]="txt_quotation_date";
$fieldlevel_arr[314][5]="txt_confirm_date_pre_cost";


//Singeing
//$fieldlevel_arr[47][1]="txt_process_date";
//$fieldlevel_arr[47][2]="txt_end_hours";
//$fieldlevel_arr[47][3]="txt_end_minutes";


// Stentering
//$fieldlevel_arr[48][1]="txt_process_date";
//$fieldlevel_arr[48][2]="txt_end_hours";
//$fieldlevel_arr[48][3]="txt_end_minutes";


//Slitting/Squeezing
//$fieldlevel_arr[30][1]="txt_process_date";
//$fieldlevel_arr[30][2]="txt_end_hours";
//$fieldlevel_arr[30][3]="txt_end_minutes";

// Drying
//$fieldlevel_arr[31][1]="txt_process_date";
//$fieldlevel_arr[31][2]="txt_end_hours";
//$fieldlevel_arr[31][3]="txt_end_minutes";

// Compacting
//$fieldlevel_arr[33][1]="txt_process_date";
//$fieldlevel_arr[33][2]="txt_end_hours";
//$fieldlevel_arr[33][3]="txt_end_minutes";

// 	Embellishment Work Order V2
// $fieldlevel_arr[161][1]="calculation_basis";
$fieldlevel_arr[161][2]="cbo_is_short";

$fieldlevel_arr[43][1]="cbo_pay_mode";

//Woven short fabric booking
$fieldlevel_arr[275][1]="txt_order_no_id";
$fieldlevel_arr[275][2]="cbo_company_name";
//$fieldlevel_arr[275][3]="cbo_buyer_name";
//$fieldlevel_arr[275][4]="txt_job_no";
//$fieldlevel_arr[275][5]="txt_booking_no";
$fieldlevel_arr[275][6]="cbo_fabric_natu";
$fieldlevel_arr[275][7]="cbo_fabric_source";
$fieldlevel_arr[275][8]="cbo_currency";
//$fieldlevel_arr[275][9]="txt_exchange_rate";
$fieldlevel_arr[275][10]="cbo_pay_mode";
$fieldlevel_arr[275][11]="txt_booking_date";
$fieldlevel_arr[275][12]="cbo_booking_month";
$fieldlevel_arr[275][13]="cbo_supplier_name";
$fieldlevel_arr[275][14]="txt_attention";
$fieldlevel_arr[275][15]="txt_delivery_date";
$fieldlevel_arr[275][16]="cbo_source";
$fieldlevel_arr[275][17]="cbo_booking_year";
$fieldlevel_arr[275][18]="cbo_ready_to_approved";

$fieldlevel_arr[275][19]="cbo_order_id";
$fieldlevel_arr[275][20]="cbo_fabricdescription_id";
$fieldlevel_arr[275][21]="cbo_fabriccolor_id";
$fieldlevel_arr[275][22]="cbo_garmentscolor_id";
$fieldlevel_arr[275][23]="txt_process_loss";
$fieldlevel_arr[275][24]="txt_grey_qnty";
$fieldlevel_arr[275][25]="txt_rate";
$fieldlevel_arr[275][26]="txt_amount";
$fieldlevel_arr[275][27]="txt_rmg_qty";
$fieldlevel_arr[275][28]="cbo_responsible_dept";
$fieldlevel_arr[275][29]="cbo_responsible_person";
$fieldlevel_arr[275][30]="txt_reason";
$fieldlevel_arr[275][31]="cbo_short_booking_type";


$fieldlevel_arr[22][1]="txt_receive_date";  // knit gray fabric receive

$fieldlevel_arr[16][1]="txt_issue_date";  // knit gray fabric Issue
$fieldlevel_arr[16][2]="cbo_basis";  // knit gray fabric Issue

$fieldlevel_arr[225][1]="txt_receive_date";  // Knit Finish Fabric Receive By Garments

$fieldlevel_arr[24][1]="txt_receive_date";  // Trims Receive
$fieldlevel_arr[24][2]="txt_challan_date";  // Trims Receive
//$fieldlevel_arr[24][3]="cbo_payment_over_recv";  // Trims Receive

$fieldlevel_arr[25][1]="txt_issue_date";  // Trims Issue 
$fieldlevel_arr[25][2]="cbo_basis";  // Trims Issue 

$fieldlevel_arr[350][1]="txt_receive_date";  // Trims Receive Entry Multi Ref.
$fieldlevel_arr[350][2]="cbo_receive_basis";  // Trims Receive Entry Multi Ref.

// Trims Receive Entry Multi Ref. V3
$fieldlevel_arr[631][1]="cbo_receive_basis";

//Service Booking For AOP V2
$fieldlevel_arr[162][1]="txt_fab_booking";
$fieldlevel_arr[162][2]="cbo_pay_mode";
$fieldlevel_arr[162][3]="cbo_is_short";

//Sample AOP With Order
$fieldlevel_arr[404][1]="cbo_is_short";

//Yarn Dyeing Work Order Without Lot
$fieldlevel_arr[125][1]="cbo_is_short";

// Order entry
$fieldlevel_arr[163][1]="txt_file_no";  // Order entry
$fieldlevel_arr[163][2]="txt_sc_lc"; // Order entry
$fieldlevel_arr[163][3]="cbo_design_source_id"; // Order entry
$fieldlevel_arr[163][4]="cbo_qltyLabel"; // Order entry
$fieldlevel_arr[163][5]="txt_item_catgory"; // Order entry
$fieldlevel_arr[163][6]="cbo_working_company_id"; // Order entry cbo_packing
$fieldlevel_arr[163][7]="txt_factory_rec_date"; // Order entry
$fieldlevel_arr[163][8]="cbo_packing";
$fieldlevel_arr[163][9]="cbo_packing_po_level";
$fieldlevel_arr[163][10]="actpoNo";
$fieldlevel_arr[163][11]="txt_po_rcv_date";
$fieldlevel_arr[163][12]="txt_po_shipment_date";
$fieldlevel_arr[163][13]="txt_rcv_ship_date";

 // Order Entry By Matrix Woven.
$fieldlevel_arr[351][1]="txt_grouping"; // Order Entry By Matrix Woven.
$fieldlevel_arr[351][2]="cbo_breakdown_type";
$fieldlevel_arr[351][3]="txt_factory_rec_date";
$fieldlevel_arr[351][4]="cbo_team_leader";
$fieldlevel_arr[351][5]="cbo_packing";
$fieldlevel_arr[351][6]="cbo_client";
$fieldlevel_arr[351][7]="cbo_projected_po";
$fieldlevel_arr[351][8]="cbo_packing_po_level";
$fieldlevel_arr[351][9]="cbo_brand_id";
// $fieldlevel_arr[351][9]="txt_file_no";
// $fieldlevel_arr[351][10]="cbo_season_id";


// Dyeing Production.
$fieldlevel_arr[35][1]="txt_process_start_date"; 
$fieldlevel_arr[35][2]="txt_start_hours"; 
$fieldlevel_arr[35][3]="txt_start_minutes";
$fieldlevel_arr[35][4]="cbo_fabric_type";
$fieldlevel_arr[35][5]="txt_process_date";
$fieldlevel_arr[35][6]="txt_end_hours";
$fieldlevel_arr[35][7]="txt_end_minutes";

//Dyes and chemical Receive
$fieldlevel_arr[4][1]="txt_expire_date";  
$fieldlevel_arr[4][2]="cbo_receive_basis";  

//Dyes and chemical Issue
$fieldlevel_arr[5][1]="cbo_issue_purpose";  
$fieldlevel_arr[5][2]="cbo_issue_basis"; 

// Order Entry By Matrix
$fieldlevel_arr[365][1]="txt_sc_lc";  
$fieldlevel_arr[365][2]="txt_item_catgory";
$fieldlevel_arr[365][3]="cbo_team_leader";
$fieldlevel_arr[365][4]="cbo_style_owner";
$fieldlevel_arr[365][5]="cbo_brand_id";

$fieldlevel_arr[31][1]="txt_process_name";  // Drying 

// Woven Finish Fabric Receive
$fieldlevel_arr[17][1]="cbo_distribiution_method";
$fieldlevel_arr[17][2]="cbo_receive_basis";
$fieldlevel_arr[17][3]="txt_challan_date";
$fieldlevel_arr[17][4]="txt_receive_date";


// Woven Finish Fabric issue // mahbub
$fieldlevel_arr[19][1]="cbo_distribiution_method";
$fieldlevel_arr[19][2]="cbo_issue_basis";


// Item Account Creation
$fieldlevel_arr[420][1]="txt_reorder_label";
$fieldlevel_arr[420][2]="txt_min_label";
$fieldlevel_arr[420][3]="txt_max_label";


// Yarn Receive
$fieldlevel_arr[1][1]="txt_no_bag";
$fieldlevel_arr[1][2]="txt_cone_per_bag";
$fieldlevel_arr[1][3]="txt_no_loose_cone";
$fieldlevel_arr[1][4]="txt_weight_per_bag";
$fieldlevel_arr[1][5]="txt_weight_per_cone";
$fieldlevel_arr[1][6]="cbo_currency";
$fieldlevel_arr[1][7]="cbo_source";
$fieldlevel_arr[1][8]="txt_receive_date";

//Stationary Purchase Order
$fieldlevel_arr[146][1]="cbo_wo_basis";
$fieldlevel_arr[146][2]="cbo_pay_mode";




// Yarn Transfer Entry
$fieldlevel_arr[10][1]="txt_no_of_bag";
$fieldlevel_arr[10][2]="txt_no_of_cone";
$fieldlevel_arr[10][3]="txt_weight_per_bag";

// Pro Forma Invoice v2
$fieldlevel_arr[405][1]="pi_mst_file";
$fieldlevel_arr[405][2]="txt_discount";
$fieldlevel_arr[405][3]="txt_upcharge";
$fieldlevel_arr[405][4]="cbo_source_id";
$fieldlevel_arr[405][5]="cbo_pi_basis_id";
$fieldlevel_arr[405][6]="cbo_goods_rcv_status";
$fieldlevel_arr[405][7]="pi_date";

//Multiple Job Wise Print Booking
$fieldlevel_arr[201][1]="cbo_pay_mode";
$fieldlevel_arr[201][2]="cbo_level"; //level


//Multiple Job Wise Embellishment Work Order
$fieldlevel_arr[403][1]="cbo_pay_mode";
$fieldlevel_arr[403][2]="cbo_level"; //level
$fieldlevel_arr[403][3]="cbo_isshort"; //Is Short


//Fabric Determination
$fieldlevel_arr[426][1]="txtrdno";

//Trims Order Receive
$fieldlevel_arr[255][1]="cbo_within_group";
$fieldlevel_arr[255][2]="cbo_currency";
$fieldlevel_arr[255][3]="txt_order_receive_date";

//Trims Delivery
$fieldlevel_arr[208][1]="cbo_within_group";
$fieldlevel_arr[208][2]="txt_delivery_date";

//Trims Bill issue
$fieldlevel_arr[276][1]="cbo_within_group";
$fieldlevel_arr[276][2]="cbo_currency";

//Job Card Preperation
$fieldlevel_arr[257][1]="cbo_within_group";

//Trims Production
$fieldlevel_arr[269][1]="cbo_within_group";
$fieldlevel_arr[269][2]="txt_prod_date";

// Printing Order Entry
$fieldlevel_arr[204][1]="cbo_within_group";
$fieldlevel_arr[204][2]="cbo_currency";

// Printing Material Receive
$fieldlevel_arr[205][1]="cbo_from_company_name";
$fieldlevel_arr[205][2]="cbo_from_location_name";
$fieldlevel_arr[205][3]="cbo_within_group";

//Planning Info Entry
$fieldlevel_arr[429][1]="txt_fabric_dia";
$fieldlevel_arr[429][2]="txt_program_date";

// Wash Order Entry
$fieldlevel_arr[295][1]="txt_rate_disabled";  // Wash Order Entry

$fieldlevel_arr[296][1]="cbo_within_group"; //Wash Material Receive
$fieldlevel_arr[297][1]="cbo_within_group"; //Wash Material issue
$fieldlevel_arr[303][1]="cbo_within_group"; //Wash Delivery


//Fabric Sales Order Entry v2
$fieldlevel_arr[472][1]="cbo_company_id";
$fieldlevel_arr[472][2]="cbo_within_group";
$fieldlevel_arr[472][3]="cbo_currency";
$fieldlevel_arr[472][4]="cbo_ship_mode";
$fieldlevel_arr[472][5]="cbo_sales_order_type";

//Program Wise Priority
$fieldlevel_arr[478][1]="cbo_withing_group";

//Roll Wise Finish Fabric Transfer Entry
// $fieldlevel_arr[505][1]="cbo_transfer_criteria";


//Stripe Measurement - Sales Order
$fieldlevel_arr[480][1]="cbo_within_group";

//Yarn Dyeing Work Order Sales
$fieldlevel_arr[135][1]="cbo_within_group";
$fieldlevel_arr[135][2]="cbo_pay_mode";
$fieldlevel_arr[135][3]="cbo_is_short";


//Sub Con Work Order Entry
$fieldlevel_arr[450][1]="cbo_currency";

$fieldlevel_arr[475][1]="txt_allocation_date";

 
//Woven Multi Job Wise Short Trims Booking
$fieldlevel_arr[273][1]="cbo_pay_mode";
$fieldlevel_arr[273][2]="cbo_source";

//Woven Multiple Job Wise Trims Booking V2
$fieldlevel_arr[492][1]="cbo_pay_mode";
$fieldlevel_arr[492][2]="cbo_source";
$fieldlevel_arr[492][3]="cbo_level";

// Export Pro Forma Invoice
$fieldlevel_arr[152][1]="cbo_within_group";

// Bill Processing Entry
$fieldlevel_arr[496][1]="cbo_within_group";

// Finish Fabric Multi Issue Challan
$fieldlevel_arr[231][1]="cbo_within_group";



$fieldlevel_arr[493][1]="txt_sc_lc";  // Order Entry By Matrix V2
$fieldlevel_arr[493][2]="txt_item_catgory";
$fieldlevel_arr[493][3]="cbo_order_uom";
$fieldlevel_arr[493][4]="cbo_breakdown_type";
// Raw Material Issue Requisition
$fieldlevel_arr[427][1]="cbo_section"; 
$fieldlevel_arr[500][1]="txt_sewing_date";

// Service Requisition
$fieldlevel_arr[540][1]="cbo_section";

// Yarn Dyeing Material Receive
$fieldlevel_arr[556][1]="cbo_within_group";

// Finish Fabric Production Entry
$fieldlevel_arr[7][1]="cbo_batch_status";

// Knitting Production Entry
//$fieldlevel_arr[2][1]="cbo_receive_basis";
$fieldlevel_arr[2][2]="txt_receive_date";

//Short Quotation V6
$fieldlevel_arr[634][1]="cbo_costing_per";

//Short Quotation [Sweater]
$fieldlevel_arr[511][1]="cbo_costing_per";

// Woven Finish Fabric Receive Return
$fieldlevel_arr[202][1]="txt_return_date";

// Woven Finish Fabric Issue Return
$fieldlevel_arr[209][1]="txt_issue_date";

// Woven Finish Fabric Transfer Entry
$fieldlevel_arr[258][1]="txt_transfer_date";

// Woven Finish Fabric Requisition for Cutting
$fieldlevel_arr[507][1]="txt_requisition_date";

$fieldlevel_arr[316][1]="cbo_operation";


// Dyeing Work Order
$fieldlevel_arr[418][1]="cbo_wo_basis";



?>