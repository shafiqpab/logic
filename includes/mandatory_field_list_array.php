<?
//This function will return page wise array
// function get_fieldlevel_arr( $index )
// {
// 	global $fieldlevel_arr;
// 	$field_arr=array();
// 	foreach($fieldlevel_arr[$index] as $key=>$val)
// 	{
// 		$value=explode("_",$val);
// 		$i=0;
// 		$str='';
// 		foreach($value as $k=>$v)
// 		{
// 			if(count($value)==1){$str =" ".ucwords (str_replace(array('cbo','txt'),'',$v));}
// 			else if($i!=0)$str .=" ".ucwords ($v);

// 			// if(count($value)==1)
// 			// {
// 			// 	$str = ($fieldlevel_arr['title'][$index][$k])?$fieldlevel_arr['title'][$index][$k]:ucwords (str_replace(array('cbo','txt'),'',$v));
// 			// }
// 			// else if($i!=0)$str .=" ".ucwords ($v);

// 			$i++;
// 		}
// 		$field_arr[$key]= $str;
// 	}
// 	return $field_arr;
// }

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

//Recipe Entry
$fieldlevel_arr[59][1]="cbo_color_range";


$fieldlevel_arr[18][1]="cbo_sales_order"; //Knit Finish Fabric Issue
$fieldlevel_arr[18][2]="txt_issue_date"; //Knit Finish Fabric Issue
$fieldlevel_arr[18][3]="cbo_sewing_company_location"; //Knit Finish Fabric Issue
$fieldlevel_arr[18][4]="txt_no_of_roll"; //Knit Finish Fabric Issue

$fieldlevel_arr[19][1]="cbo_cutting_floor"; //woven Finish Fabric Issue

$fieldlevel_arr[37][1]="txt_no_of_roll"; //Knit Finish Fabric Garments Receive
$fieldlevel_arr[14][1]="txt_no_of_roll"; //Knit Finish Fabric Transfer Entry
$fieldlevel_arr[52][1]="txt_no_of_roll"; //Knit Finish Fabric Transfer Entry
$fieldlevel_arr[46][1]="txt_roll"; //Knit Finish Fabric Transfer Entry


$fieldlevel_arr[555][1]="txt_remarks"; //Multiple Job Wise Additional Trims Booking

$fieldlevel_arr[676][1]="cbo_gmt_item"; //Style Ref Entry 
$fieldlevel_arr[676][2]="txt_offer_qnty"; //Style Ref Entry 

$fieldlevel_arr[54][1]="cbo_is_sales"; //Finish Fabric Delivery to Store
$fieldlevel_arr[54][2]="txt_batch_no"; //Finish Fabric Delivery to Store
$fieldlevel_arr[54][3]="cbo_deli_company_id"; //Finish Fabric Delivery to Store
$fieldlevel_arr[54][4]="cbo_deli_location_id"; //Finish Fabric Delivery to Store
//Finish Fabric Roll Receive by Store
$fieldlevel_arr[68][1]="cbo_is_sales";
$fieldlevel_arr[68][2]="txt_boe_mushak_challan_no";
$fieldlevel_arr[68][3]="txt_boe_mushak_challan_date";
//$fieldlevel_arr[70][1]="cbo_basis"; //yarn purchase requisition

$fieldlevel_arr[41][1]="txt_dyeing_charge"; // Yarn Dying with order

// Yarn Dyeing Bill Entry
$fieldlevel_arr[256][1]="multiple_file_field";

// Sub-Contact Order Entry
$fieldlevel_arr[238][1]="cbo_team_leader";
$fieldlevel_arr[238][2]="txt_style_ref";
$fieldlevel_arr[238][3]="txt_efficiency_per";
$fieldlevel_arr[238][4]="txt_smv";
$fieldlevel_arr[238][5]="txt_material_recv_date";

$fieldlevel_arr[700][1]="txt_salesrate";


// Operation Resource Entry
$fieldlevel_arr[680][1]="txt_consumption_factor";
$fieldlevel_arr[680][2]="txt_needle_thread";
$fieldlevel_arr[680][3]="txt_bobbin_thread";

// Knitting Complete[Sweater Garments]
$fieldlevel_arr[681][1]="txt_wo_no";
 

// Knitting Complete[Sweater Garments]
$fieldlevel_arr[681][1]="txt_wo_no";

// Linking Complete[Sweater Garments]
$fieldlevel_arr[682][1]="txt_wo_no";

// Trimming Complete[Sweater Garments]
$fieldlevel_arr[683][1]="txt_wo_no";

// Mending Complete[Sweater Garments]
$fieldlevel_arr[684][1]="txt_wo_no";

// Wash Complete[Sweater Garments]
$fieldlevel_arr[685][1]="txt_wo_no";

// Packing and Finishing [Sweater Garments]
$fieldlevel_arr[686][1]="txt_wo_no";

 

// Yarn Dyeing Bill Entry
$fieldlevel_arr[330][1]="multiple_file_field";
$fieldlevel_arr[330][2]="txt_wo_no";

//Dyeing And Finishing Bill Entry
$fieldlevel_arr[623][1]="multiple_file_field";

//Multiple Job Wise Trims Booking V2
$fieldlevel_arr[87][1]="txtddate";
$fieldlevel_arr[87][2]="cbo_nominated_id";
$fieldlevel_arr[87][3]="cbo_pay_term";
$fieldlevel_arr[87][4]="txt_tenor";
$fieldlevel_arr[87][5]="delivery_address";

//Woven Multiple Job Wise Trims Booking V2
$fieldlevel_arr[492][1]="cbo_payterm_id";
$fieldlevel_arr[492][2]="txt_tenor";
$fieldlevel_arr[492][3]="show_textdelivery_address";

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
//$fieldlevel_arr[86][13]="cbo_supplier_name";
//$fieldlevel_arr[86][14]="txt_attention";
$fieldlevel_arr[86][15]="txt_delivery_date";
$fieldlevel_arr[86][16]="cbo_source";
//$fieldlevel_arr[86][17]="cbo_booking_year";
//$fieldlevel_arr[86][18]="txt_booking_percent";
//$fieldlevel_arr[86][19]="txt_colar_excess_percent";
//$fieldlevel_arr[86][20]="txt_cuff_excess_percent";
//$fieldlevel_arr[86][21]="cbo_ready_to_approved";
$fieldlevel_arr[86][22]="processloss_breck_down";
//$fieldlevel_arr[86][23]="txt_fabriccomposition";
//$fieldlevel_arr[86][24]="txt_intarnal_ref";
//$fieldlevel_arr[86][25]="txt_file_no";


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
//$fieldlevel_arr[88][11]="txt_booking_date";
//$fieldlevel_arr[88][12]="cbo_booking_month";
//$fieldlevel_arr[88][13]="cbo_supplier_name";
//$fieldlevel_arr[88][14]="txt_attention";
//$fieldlevel_arr[88][15]="txt_delivery_date";
//$fieldlevel_arr[88][16]="cbo_source";
//$fieldlevel_arr[88][17]="cbo_booking_year";
//$fieldlevel_arr[88][18]="cbo_ready_to_approved";
//$fieldlevel_arr[88][19]="cbo_order_id";
//$fieldlevel_arr[88][20]="cbo_fabricdescription_id";
$fieldlevel_arr[88][21]="cbo_fabriccolor_id";
//$fieldlevel_arr[88][22]="cbo_garmentscolor_id";
//$fieldlevel_arr[88][23]="txt_process_loss";
//$fieldlevel_arr[88][24]="txt_grey_qnty";
$fieldlevel_arr[88][25]="txt_rate";
//$fieldlevel_arr[88][26]="txt_amount";
//$fieldlevel_arr[88][27]="txt_rmg_qty";
//$fieldlevel_arr[88][28]="cbo_responsible_dept";
//$fieldlevel_arr[88][29]="cbo_responsible_person";
//$fieldlevel_arr[88][30]="txt_reason";
$fieldlevel_arr[88][31]="cbo_short_booking_type";
$fieldlevel_arr[88][32]="cbo_garmentssize_id";
$fieldlevel_arr[88][33]="cbo_provider_name";
$fieldlevel_arr[88][33]="cbo_department";



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
$fieldlevel_arr[90][19]="txt_style_no";
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
$fieldlevel_arr[90][43]="cbo_brand_name";


//Fabric Service Receive
$fieldlevel_arr[92][1]="txt_boe_mushak_challan_no";
$fieldlevel_arr[92][2]="txt_boe_mushak_challan_date";
$fieldlevel_arr[92][3]="txt_receive_challan";


//94 = Yarn Service Work Order
$fieldlevel_arr[94][1]="cbo_is_sales_order";
$fieldlevel_arr[94][2]="cbo_with_order";

$fieldlevel_arr[96][1]="cbo_working_company_name"; //Bundle Wise Sewing Input

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
$fieldlevel_arr[98][43]='txt_service_booking';
$fieldlevel_arr[98][44]='cbo_receive_basis';
$fieldlevel_arr[98][45]='cbo_knitting_source';

//Pro Forma Invoice
$fieldlevel_arr[104][1]="cbo_goods_rcv_status"; //Goods Rcv Status
//Export LC Entry
$fieldlevel_arr[106][1]="cbo_export_item_category"; //Export Item Category
$fieldlevel_arr[106][2]="txt_max_btb_limit"; //BTB Limit %
$fieldlevel_arr[106][3]="txt_lien_date"; //LC Date
$fieldlevel_arr[106][4]="txt_tenor"; //LC Date

//Sales Contract Entry
$fieldlevel_arr[107][1]="cbo_export_item_category";
$fieldlevel_arr[107][2]="txt_max_btb_limit";
$fieldlevel_arr[107][3]="txt_estimated_sc_qnty";
$fieldlevel_arr[107][4]="txt_lien_date";
$fieldlevel_arr[107][5]="txt_port_of_discharge";
$fieldlevel_arr[107][6]="txt_tenor";
$fieldlevel_arr[107][7]="txt_file";

$fieldlevel_arr[449][1]="txt_bhmerchant";

 
// txt_wo_no
//Partial Fabric Booking
$fieldlevel_arr[108][1]="cbouom"; //UOM
$fieldlevel_arr[108][2]="cbo_fabric_source"; //Fabric Source
$fieldlevel_arr[108][3]="cbo_pay_mode"; //Pay Mode
$fieldlevel_arr[108][4]="cbo_fabric_natu";
$fieldlevel_arr[108][5]="txt_delivery_date"; //check tna
$fieldlevel_arr[108][6]="cbo_source"; //check Source
$fieldlevel_arr[108][7]="cbo_brand_id";
$fieldlevel_arr[108][8]="txt_fabric_start_date";

//woven partial fabric Booking
$fieldlevel_arr[271][1]="cbouom"; //UOM
$fieldlevel_arr[271][2]="cbo_fabric_source"; //Fabric Source
$fieldlevel_arr[271][3]="cbo_pay_mode"; //Pay Mode
$fieldlevel_arr[271][4]="cbo_fabric_natu";
$fieldlevel_arr[271][5]="txt_delivery_date"; //check tna
$fieldlevel_arr[271][6]="cbo_source"; //check Source
$fieldlevel_arr[271][7]="cbo_season_year"; //check season year
$fieldlevel_arr[271][8]="cbo_season_id"; //check season
$fieldlevel_arr[271][9]="cbo_brand_id"; //check brand

// Fabric Sales Order Entry
$fieldlevel_arr[109][1]="cbo_company_id";
$fieldlevel_arr[109][2]="Main Process";
$fieldlevel_arr[109][3]="Sub Process";
$fieldlevel_arr[109][4]="txt_delivery_date";
$fieldlevel_arr[109][5]="cbo_buyer_name";
$fieldlevel_arr[109][6]="cbo_cust_buyer_name";

// Pre-Costing
//$fieldlevel_arr[111][1]="txt_common_oh_pre_cost";
//$fieldlevel_arr[111][2]="txt_depr_amor_pre_cost";

//Main fabric booking v2
$fieldlevel_arr[118][1]="cbo_pay_mode"; //pay mode
$fieldlevel_arr[118][2]="txt_delivery_date";
$fieldlevel_arr[118][3]="txt_processloss_breck_down";
$fieldlevel_arr[118][4]="txt_excess_input_per";//Excess Input
$fieldlevel_arr[118][5]="cbo_source";//SOURCE



$fieldlevel_arr[3][1]="cbo_sales_order"; // Yarn Requisition Search Form Field //txt_issue_date
$fieldlevel_arr[3][2]="txt_pi_selection"; // Yarn Issue
//$fieldlevel_arr[1][1]="txt_receive_date"; // Yarn Recv

$fieldlevel_arr[120][1]="cbo_within_group"; // Yarn Requisition Entry For Sales Pop up Within Group Field
$fieldlevel_arr[121][1]="cbo_produced_by"; // Cutting Entry

$fieldlevel_arr[122][1]="txt_Sewing_SMV"; //Order Update Entry
$fieldlevel_arr[122][2]="txt_Cutting_SMV"; //Order Update Entry
$fieldlevel_arr[122][3]="txt_Finish_SMV"; //Order Update Entry

//$fieldlevel_arr[122][4]="txt_excess_cut"; //Order Update Entry
$fieldlevel_arr[122][5]="cbo_order_status";
$fieldlevel_arr[122][6]="txt_po_no";
$fieldlevel_arr[122][7]="txt_pub_shipment_date";
$fieldlevel_arr[122][8]="txt_org_shipment_date";
//$fieldlevel_arr[122][9]="txt_extended_ship_date";

// Fabric Requisition For Batch 2
$fieldlevel_arr[123][1]="cbo_search_by"; //Order Update Entry
$fieldlevel_arr[553][1]="cbo_search_by"; //Fabric Requisition For Batch 3

// pre costing v2
//$fieldlevel_arr[158][1]="cbo_fabric_costing_uom"; //uom fabric cost part
//$fieldlevel_arr[158][2]="cbo_fabric_costing_fab_nature"; //Fab Nature fabric cost part
// Who add top 2 those?
$fieldlevel_arr[158][3]="txt_sew_efficiency_per"; // Sew Efficiency %
$fieldlevel_arr[158][4]="cbo_costing_per"; // Sew Efficiency %
$fieldlevel_arr[158][5]="cbo_add_file"; // Add File
$fieldlevel_arr[158][6]="txt_prod_line_hr"; // Prod/Line/Hr
$fieldlevel_arr[158][7]="txt_machine_line"; // Machine/Line

// pre costing v3
$fieldlevel_arr[520][1]="cbo_fabric_costing_uom"; //uom fabric cost part
$fieldlevel_arr[520][2]="cbo_fabric_costing_fab_nature"; //Fab Nature fabric cost part
$fieldlevel_arr[520][3]="txt_sew_efficiency_per"; // Sew Efficiency %
$fieldlevel_arr[520][4]="cbo_costing_per"; // Sew Efficiency %
$fieldlevel_arr[520][5]="cbo_add_file"; // Add File


// pre costing v2 Woven
$fieldlevel_arr[425][1]="cbo_fabric_costing_uom"; //uom fabric cost part
$fieldlevel_arr[425][2]="cbo_fabric_costing_fab_nature"; //Fab Nature fabric cost part
$fieldlevel_arr[425][3]="txt_sew_efficiency_per"; // Sew Efficiency %
$fieldlevel_arr[425][4]="cbo_costing_per"; // Sew Efficiency %
$fieldlevel_arr[425][5]="cbo_add_file"; // Add File
$fieldlevel_arr[425][6]="txt_prod_line_hr"; // Add File

// pre costing v2-Sweater
$fieldlevel_arr[521][1]="cbo_fabric_costing_uom"; //uom fabric cost part
$fieldlevel_arr[521][2]="cbo_fabric_costing_fab_nature"; //Fab Nature fabric cost part
$fieldlevel_arr[521][3]="txt_sew_efficiency_per"; // Sew Efficiency %
$fieldlevel_arr[521][4]="cbo_costing_per"; // Sew Efficiency %
$fieldlevel_arr[521][5]="cbo_add_file"; // Add File

// Gmts. Issue to Wash
$fieldlevel_arr[415][1]="cbo_sending_location";

// Gmts. Issue to Wash
$fieldlevel_arr[416][1]="cbo_sending_location";

//Operation Bulletin Entry
$fieldlevel_arr[149][1]="cbo_bulletin_type";
$fieldlevel_arr[149][2]="txt_internal_ref";
$fieldlevel_arr[149][3]="txt_applicable_period";

//Poly Entry
$fieldlevel_arr[164][1]="cbo_floor"; //sew.floor
// $fieldlevel_arr[164][2]="cbo_finishing_floor"; //fini.floor
$fieldlevel_arr[164][3]="cbo_color_type";
$fieldlevel_arr[164][4]="cbo_poly_line";

//Yarn Count Determination
$fieldlevel_arr[184][1]="cbo_fabric_nature"; //cbofabricnature

//Knitting Bill Issue
$fieldlevel_arr[186][1]="cbo_party_source"; //cbo_party_source
$fieldlevel_arr[186][2]="multiple_file_field"; //cbo_party_source

//622 Knitting Bill Entry
$fieldlevel_arr[622][1]="multiple_file_field";

//621 Service Requisition
$fieldlevel_arr[621][1]="multiple_file_field";

//Garments Delivery Entry
$fieldlevel_arr[198][1]="shipping_status";
$fieldlevel_arr[198][2]="txt_ex_factory_date";
$fieldlevel_arr[198][3]="cbo_delivery_location";
$fieldlevel_arr[198][4]="cbo_delivery_floor";
$fieldlevel_arr[198][5]="txt_invoice_no";
$fieldlevel_arr[198][6]="multiple_file_field";
$fieldlevel_arr[198][7]="cbo_shipping_mode";
$fieldlevel_arr[198][8]="txt_truck_no";

//Grey Fabric Roll Issue
$fieldlevel_arr[61][1]="cbo_dyeing_source";

//Garments Delivery Entry
$fieldlevel_arr[62][1]="txt_delivery_date";

//Purchase Requisition
$fieldlevel_arr[69][1]="txt_date_from";
$fieldlevel_arr[69][2]="cbo_pay_mode";
$fieldlevel_arr[69][3]="cbo_currency_name";
$fieldlevel_arr[69][4]="cbo_division_name";
$fieldlevel_arr[69][5]="cbo_department_name";
$fieldlevel_arr[69][6]="cbo_section_name";
$fieldlevel_arr[69][7]="txt_req_by";

//cut and lay entry roll wise
$fieldlevel_arr[77][1]="txt_table_no";
 
//General Item Receive Return
$fieldlevel_arr[26][1]="txt_return_date";
$fieldlevel_arr[26][2]="txt_challan_no";
$fieldlevel_arr[26][3]="txt_pi_no";



//General Item Issue Return
$fieldlevel_arr[27][1]="txt_return_date";

//General Item Issue
//$fieldlevel_arr[21][1]="txt_issue_date";
$fieldlevel_arr[21][1]="cbo_department";
$fieldlevel_arr[21][2]="cbo_issue_purpose";
$fieldlevel_arr[21][3]="txt_buyer_order";
$fieldlevel_arr[21][4]="cbo_division";
 

//Others Purchase Order
$fieldlevel_arr[103][1]="txt_wo_date";

//General Item Receive
$fieldlevel_arr[20][1]="txt_receive_date";
$fieldlevel_arr[20][2]="txt_challan_no";
$fieldlevel_arr[20][3]="txt_addi_info";
$fieldlevel_arr[20][4]="txt_challan_date_mst";
$fieldlevel_arr[20][5]="txt_boe_mushak_challan_no";
$fieldlevel_arr[20][6]="txt_boe_mushak_challan_date";
$fieldlevel_arr[20][7]="multiple_file_field";

//General Item Accessories Receive
$fieldlevel_arr[590][1]="txt_boe_mushak_challan_no";
$fieldlevel_arr[590][2]="txt_boe_mushak_challan_date";

//General Item Spare Parts and Machineries Receive
$fieldlevel_arr[591][1]="txt_boe_mushak_challan_no";
$fieldlevel_arr[592][2]="txt_boe_mushak_challan_date";

//General Item Stationeries Receive
$fieldlevel_arr[592][1]="txt_boe_mushak_challan_no";
$fieldlevel_arr[592][2]="txt_boe_mushak_challan_date";

//General Item Electrical Receive
$fieldlevel_arr[593][1]="txt_boe_mushak_challan_no";
$fieldlevel_arr[593][2]="txt_boe_mushak_challan_date";

//General Item Maintenance Receive
$fieldlevel_arr[594][1]="txt_boe_mushak_challan_no";
$fieldlevel_arr[594][2]="txt_boe_mushak_challan_date";

//General Item Medical Receive
$fieldlevel_arr[595][1]="txt_boe_mushak_challan_no";
$fieldlevel_arr[595][2]="txt_boe_mushak_challan_date";

//General Item ICT Receive
$fieldlevel_arr[596][1]="txt_boe_mushak_challan_no";
$fieldlevel_arr[596][2]="txt_boe_mushak_challan_date";

//General Item Utilities and Lubricants Receive
$fieldlevel_arr[597][1]="txt_boe_mushak_challan_no";
$fieldlevel_arr[597][2]="txt_boe_mushak_challan_date";

//General Item Construction Materials Receive
$fieldlevel_arr[598][1]="txt_boe_mushak_challan_no";
$fieldlevel_arr[598][2]="txt_boe_mushak_challan_date";

//General Item Printing Chemicals and Dyes Receive Receive
$fieldlevel_arr[599][1]="txt_boe_mushak_challan_no";
$fieldlevel_arr[599][2]="txt_boe_mushak_challan_date";

//Sample Requisition With Booking
$fieldlevel_arr[203][1]="txt_qrr_date";
$fieldlevel_arr[203][2]="cbo_brand_id";
$fieldlevel_arr[203][3]="cbo_pay_mode";
$fieldlevel_arr[203][4]="cbo_supplier_name";
$fieldlevel_arr[203][5]="cbo_fabric_source";
$fieldlevel_arr[203][6]="cbo_qltyLabel";
$fieldlevel_arr[203][7]="multiple_file_field";
$fieldlevel_arr[203][8]="txt_fabric_Dia";
$fieldlevel_arr[203][9]="txt_internal_ref";
$fieldlevel_arr[203][10]="txt_bhmerchant";
$fieldlevel_arr[203][11]="cbo_season_year";


//Gate pass entry
$fieldlevel_arr[251][1]="cbo_roll_by";
$fieldlevel_arr[251][2]="cbo_issue_purpose";
$fieldlevel_arr[251][3]="cbo_returnable";
$fieldlevel_arr[251][4]="txt_delivery_company";
$fieldlevel_arr[251][5]="txt_vhicle_number";
$fieldlevel_arr[251][6]="txt_attention";
$fieldlevel_arr[251][7]="cbo_delevery_as";
$fieldlevel_arr[251][8]="cbo_location";

//Gate In Entry
$fieldlevel_arr[363][1]="cbo_party_type";
$fieldlevel_arr[363][2]="cbo_out_company";
$fieldlevel_arr[363][3]="txt_receive_from";
$fieldlevel_arr[363][4]="txt_challan_no";

//AOP Dyes Chemical Issue
$fieldlevel_arr[308][1]="cbo_issue_purpose";

//Printing Dyes Chemical Issue
$fieldlevel_arr[250][1]="cbo_issue_purpose";

//Left Over Garments Receive
$fieldlevel_arr[587][1]="cbo_category_id";

//Export Invoice
$fieldlevel_arr[270][1]="shipping_mode";
$fieldlevel_arr[270][2]="cbo_country";
$fieldlevel_arr[270][3]="ex_factory_date";
$fieldlevel_arr[270][4]="txt_exp_form_no";
$fieldlevel_arr[270][5]="txt_exp_form_date";
$fieldlevel_arr[270][6]="shipping_bill_no";
$fieldlevel_arr[270][7]="ship_bl_date";

//Planning Info Entry For Sales Order
$fieldlevel_arr[282][1]="cbo_within_group"; //Within Group
$fieldlevel_arr[282][2]="txt_batch_no"; //Batch No
$fieldlevel_arr[282][3]="txt_tube_ref_no"; //Tube/Ref. No
$fieldlevel_arr[282][4]="txt_stitch_length"; //Stitch Length
$fieldlevel_arr[282][5]="txt_machine_no"; //Machine No
$fieldlevel_arr[282][6]="cbo_location_name"; //Location
$fieldlevel_arr[282][7]="cbo_color_range"; //Location 
$fieldlevel_arr[282][8]="txt_start_date"; //start date 
$fieldlevel_arr[282][9]="txt_end_date"; //end date

//Price Quotation
//$fieldlevel_arr[314][1]="txt_common_oh_pre_cost";
//$fieldlevel_arr[314][2]="txt_income_tax_pre_cost";
$fieldlevel_arr[314][1]="cbo_costing_per";
$fieldlevel_arr[314][2]="cbo_pord_dept";
$fieldlevel_arr[314][3]="txt_est_ship_date";
$fieldlevel_arr[314][4]="txt_confirm_date_pre_cost";
$fieldlevel_arr[314][5]="cbo_season_name";
$fieldlevel_arr[314][6]="image_button";
$fieldlevel_arr['title'][314][6]="Image";
$fieldlevel_arr[314][7]="file_uploaded";
$fieldlevel_arr['title'][314][7]="File";




//Singeing
//$fieldlevel_arr[47][1]="txt_process_date";
//$fieldlevel_arr[47][2]="txt_end_hours";
//$fieldlevel_arr[47][3]="txt_end_minutes";

// Singeing
$fieldlevel_arr[47][1]="cbo_floor";
$fieldlevel_arr[47][2]="cbo_machine_name";


// Stentering
//$fieldlevel_arr[48][1]="txt_process_date";
//$fieldlevel_arr[48][2]="txt_end_hours";
//$fieldlevel_arr[48][3]="txt_end_minutes";
// Stentering
$fieldlevel_arr[48][1]="cbo_floor";
$fieldlevel_arr[48][2]="cbo_machine_name";


//Slitting/Squeezing
//$fieldlevel_arr[30][1]="txt_process_date";
//$fieldlevel_arr[30][2]="txt_end_hours";
//$fieldlevel_arr[30][3]="txt_end_minutes";

// Slitting/Squeezing
$fieldlevel_arr[30][1]="cbo_floor";
$fieldlevel_arr[30][2]="cbo_machine_name";

// Drying
//$fieldlevel_arr[31][1]="txt_process_date";
//$fieldlevel_arr[31][2]="txt_end_hours";
//$fieldlevel_arr[31][3]="txt_end_minutes";

// Compacting
//$fieldlevel_arr[33][1]="txt_process_date";
//$fieldlevel_arr[33][2]="txt_end_hours";
//$fieldlevel_arr[33][3]="txt_end_minutes";

// Compacting
$fieldlevel_arr[33][1]="cbo_floor";
$fieldlevel_arr[33][2]="cbo_machine_name";

// 	Embellishment Work Order V2
//$fieldlevel_arr[161][1]="calculation_basis";

$fieldlevel_arr[43][1]="cbo_pay_mode";

// Trims Batch Creation
$fieldlevel_arr[136][1]="cbo_working_company_id";



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
$fieldlevel_arr[22][2]="txt_boe_mushak_challan_no"; // knit gray fabric receive
$fieldlevel_arr[22][3]="txt_boe_mushak_challan_date"; // knit gray fabric receive

$fieldlevel_arr[58][1]="txt_boe_mushak_challan_no"; // knit gray fabric roll receive
$fieldlevel_arr[58][2]="txt_boe_mushak_challan_date"; // knit gray fabric roll receive

$fieldlevel_arr[16][1]="txt_issue_date";  // knit gray fabric Issue

$fieldlevel_arr[225][1]="txt_receive_date";  // Knit Finish Fabric Receive By Garments
$fieldlevel_arr[225][2]="txt_no_of_roll";

$fieldlevel_arr[224][1]="txt_no_of_roll"; // Textile Delivery to garments
$fieldlevel_arr[287][1]="txt_roll"; // Textile Receive Return

$fieldlevel_arr[263][1]="txt_boe_mushak_challan_no"; // Raw Material Receive
$fieldlevel_arr[263][2]="txt_boe_mushak_challan_date"; // Raw Material Receive


$fieldlevel_arr[24][1]="txt_receive_date";  // Trims Receive
$fieldlevel_arr[24][2]="txt_boe_mushak_challan_no";  // Trims Receive
$fieldlevel_arr[24][3]="txt_boe_mushak_challan_date";  // Trims Receive
$fieldlevel_arr[24][4]="multiple_file_field";  // Trims Receive

$fieldlevel_arr[25][1]="txt_issue_date";  // Trims Issue
$fieldlevel_arr[25][2]="txt_issue_chal_no";
$fieldlevel_arr[25][3]="cbo_floor_swing";
$fieldlevel_arr[25][4]="cbo_sewing_line";

$fieldlevel_arr[208][1]="txt_cust_location";  // Trims Delivery


$fieldlevel_arr[350][1]="txt_receive_date";  // Trims Receive Entry Multi Ref.

// Order entry
$fieldlevel_arr[163][1]="txt_file_no";  // Order entry
$fieldlevel_arr[163][2]="txt_sc_lc"; // Order entry
$fieldlevel_arr[163][3]="cbo_design_source_id"; // Order entry
$fieldlevel_arr[163][4]="cbo_sub_dept"; // Order entry
$fieldlevel_arr[163][5]="cbo_order_nature";  // Order entry
$fieldlevel_arr[163][6]="sustainability_standard"; // Order entry
$fieldlevel_arr[163][7]="cbo_packing_po_level"; // Order entry
$fieldlevel_arr[163][8]="txt_etd_ldd"; // Order entry
$fieldlevel_arr[163][9]="cbo_packing"; // Order entry
$fieldlevel_arr[163][10]="cbo_po_tna_lead_time"; // Order entry
$fieldlevel_arr[163][11]="cbo_factory_merchant"; // Factory Merchandiser


//===============Order Entry By Matrix Woven.=================
$fieldlevel_arr[351][1]="txt_grouping";
$fieldlevel_arr[351][2]="txt_file_year";
$fieldlevel_arr[351][3]="txt_file_no";
$fieldlevel_arr[351][4]="txt_phd";
$fieldlevel_arr[351][5]="cbo_season_year";
$fieldlevel_arr[351][6]="cbo_brand_id";
$fieldlevel_arr[351][7]="txt_bodywashColor"; // Order entry for issue 7385
$fieldlevel_arr[351][8]="cbo_working_factory"; // Order entry for issue 7385
$fieldlevel_arr[351][9]="cbo_working_location_id";
$fieldlevel_arr[351][10]="txt_style_description";
//$fieldlevel_arr[351][11]="image_button_front";
$fieldlevel_arr[351][12]="cbo_season_id";
$fieldlevel_arr[351][13]="cbo_region";
$fieldlevel_arr[351][14]="txt_req_no";





//=================Dyeing Production.=====================
$fieldlevel_arr[35][1]="txt_process_start_date";
$fieldlevel_arr[35][2]="txt_start_hours";
$fieldlevel_arr[35][3]="txt_start_minutes";
$fieldlevel_arr[35][4]="cbo_fabric_type";
//$fieldlevel_arr[35][5]="txt_end_hours";
//$fieldlevel_arr[35][6]="txt_end_minutes";

//===============Dyes and chemical Receive=================
$fieldlevel_arr[4][1]="txt_expire_date";
$fieldlevel_arr[4][2]="cbo_zero_discharge";
$fieldlevel_arr[4][3]="cbo_pay_mode";
$fieldlevel_arr[4][4]="txt_boe_mushak_challan_no";
$fieldlevel_arr[4][5]="txt_boe_mushak_challan_date";

//===============Dyes and chemical Issue=================
$fieldlevel_arr[5][1]="cbo_division_name";
$fieldlevel_arr[5][2]="cbo_department_name";
$fieldlevel_arr[5][3]="cbo_section_name";

$fieldlevel_arr[5][4]="txt_batch_no";
$fieldlevel_arr[5][5]="txt_challan_no";

//===========Order Entry By Matrix===================
$fieldlevel_arr[365][1]="txt_sc_lc";
$fieldlevel_arr[365][2]="txt_file_year";
$fieldlevel_arr[365][3]="txt_file_no";
$fieldlevel_arr[365][4]="cbo_qltyLabel";
$fieldlevel_arr[365][5]="cbo_brand_id";
$fieldlevel_arr[365][6]="cbo_factory_merchant";
$fieldlevel_arr[365][7]="cbo_order_nature";
$fieldlevel_arr[365][8]="txt_grouping";
$fieldlevel_arr[365][9]="txt_org_shipment_date";
$fieldlevel_arr[365][10]="cbo_fit_id";
$fieldlevel_arr[365][11]="cbo_season_year";
$fieldlevel_arr[365][12]="txt_style_description";
$fieldlevel_arr[365][13]="cbo_agent";
$fieldlevel_arr[365][14]="cbo_product_group";
$fieldlevel_arr[365][15]="cbo_region";
$fieldlevel_arr[365][16]="txt_phd";

//===========Drying ===================
$fieldlevel_arr[31][1]="txt_process_name";
$fieldlevel_arr[31][2]="cbo_floor";
$fieldlevel_arr[31][3]="cbo_machine_name";

// /Woven Finish Fabric Receive
$fieldlevel_arr[17][1]="cbo_distribiution_method";
$fieldlevel_arr[17][2]="txt_boe_mushak_challan_no";
$fieldlevel_arr[17][3]="txt_boe_mushak_challan_date";
$fieldlevel_arr[17][4]="multiple_file_field";

// /Bundle Receive From Print
$fieldlevel_arr[536][1]="txt_boe_mushak_challan_no";
$fieldlevel_arr[536][2]="txt_boe_mushak_challan_date";


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
$fieldlevel_arr[1][6]="txt_boe_mushak_challan_no";
$fieldlevel_arr[1][7]="txt_boe_mushak_challan_date";
$fieldlevel_arr[1][8]="cbo_buyer_name";
$fieldlevel_arr[1][9]="multiple_file_field";


// Yarn Transfer Entry
$fieldlevel_arr[10][1]="txt_no_of_bag";
$fieldlevel_arr[10][2]="txt_no_of_cone";
$fieldlevel_arr[10][3]="txt_weight_per_bag";
$fieldlevel_arr[10][4]="cbo_buyer_name";

//Order Entry by Matrix Sweater Garments
$fieldlevel_arr[510][1]="cbo_season_id";
$fieldlevel_arr[510][2]="cbo_season_year";
$fieldlevel_arr[510][3]="cbo_brand_id";
$fieldlevel_arr[510][4]="cbo_mc_brand";


// Pro Forma Invoice v2
$fieldlevel_arr[405][1]="txt_file";
$fieldlevel_arr[405][2]="cbo_location_name";
$fieldlevel_arr[405][3]="txt_internal_file_no";
$fieldlevel_arr[405][4]="cbo_payterm_id";
$fieldlevel_arr[405][5]="cbo_pi_for";
$fieldlevel_arr[405][6]="cbo_buyer_name";
$fieldlevel_arr[405][7]="cbo_priority";
$fieldlevel_arr[405][8]="txt_remarks";
$fieldlevel_arr[405][9]="pi_validity_date";
$fieldlevel_arr[405][10]="cbo_payterm_id";
$fieldlevel_arr[405][11]="last_shipment_date";
$fieldlevel_arr[405][12]="txt_order_file_no";
$fieldlevel_arr[405][13]="txt_tenor";

// Batch Creation
$fieldlevel_arr[408][1]="txt_dyeing_pdo";
$fieldlevel_arr[408][2]="cbo_floor";
$fieldlevel_arr[408][3]="cbo_machine_name";
$fieldlevel_arr[408][4]="txt_du_req_hr";
$fieldlevel_arr[408][5]="txt_du_req_min";
$fieldlevel_arr[408][6]="txt_exp_load_hr";
$fieldlevel_arr[408][7]="txt_exp_load_min";
$fieldlevel_arr[408][8]="cbo_double_dyeing";
$fieldlevel_arr[408][9]="cbo_shift_name";

//Planning .................
$fieldlevel_arr[428][1]="txt_machine_no";

$fieldlevel_arr[429][1]="txt_machine_no";
$fieldlevel_arr[429][2]="cbo_machine_group";
$fieldlevel_arr[429][3]="txt_knitting_pdo";

// Heat Setting
$fieldlevel_arr[32][1]="cbo_floor";
$fieldlevel_arr[32][2]="cbo_machine_name";

// De Oiling
$fieldlevel_arr[310][1]="cbo_floor";
$fieldlevel_arr[310][2]="cbo_machine_name";

// Dry Slitting
$fieldlevel_arr[323][1]="cbo_floor";
$fieldlevel_arr[323][2]="cbo_machine_name";

// Special Finish
$fieldlevel_arr[34][1]="cbo_floor";
$fieldlevel_arr[34][2]="cbo_machine_name";

// Wash/AOP Wash
$fieldlevel_arr[424][1]="cbo_floor";
$fieldlevel_arr[424][2]="cbo_machine_name";

$fieldlevel_arr[295][1]="is_rate_mandetory";


$fieldlevel_arr[154][1]="cbo_department_name";
$fieldlevel_arr[154][2]="cbo_section_name";
$fieldlevel_arr[154][3]="cbo_division_name";

//Fabric Determination
$fieldlevel_arr[426][1]="txtrdno";
$fieldlevel_arr[426][2]="txtweight";
$fieldlevel_arr[426][3]="cboweighttype";


$fieldlevel_arr[44][1]="txt_processloss_breck_down";

//AOP Batch Creation
$fieldlevel_arr[281][1]="txt_process_id"; 

//Buyer Inquiry Woven
$fieldlevel_arr[434][1]="cbo_season_name";
$fieldlevel_arr[434][2]="txt_color";
$fieldlevel_arr[434][3]="txt_is_file_uploaded";
$fieldlevel_arr[434][4]="txt_offer_qty";
$fieldlevel_arr[434][5]="txt_est_ship_date";
$fieldlevel_arr[434][6]="txt_buyer_submit_price";
$fieldlevel_arr[434][7]="cbo_season_year";
$fieldlevel_arr[434][8]="cbo_brand";

//Buyer Inquiry Knit
$fieldlevel_arr[433][1]="cbo_season_name";
$fieldlevel_arr[433][2]="txt_color";
$fieldlevel_arr[433][4]="txt_offer_qty";
$fieldlevel_arr[433][6]="txt_buyer_submit_price";
$fieldlevel_arr[433][7]="cbo_season_year";
$fieldlevel_arr[433][8]="cbo_brand_id";

//Printing Recipe Entry
$fieldlevel_arr[220][1]="txt_recipe_des";

//Bundle Wise Sewing Output
$fieldlevel_arr[460][1]="txt_alter_reject_record";
$fieldlevel_arr[460][2]="txt_spot_reject_record";
$fieldlevel_arr[460][3]="cbo_shift_name";

//Bundle Wise Sewing Input
$fieldlevel_arr[96][1]="cbo_shift_name";


//Yarn Dyeing Work Order Without Order
$fieldlevel_arr[42][1]="cbo_source";
$fieldlevel_arr[42][2]="txt_dyeing_charge";

//Printing Material Receive
$fieldlevel_arr[465][1]="cbo_from_company_name";
$fieldlevel_arr[465][2]="cbo_from_location_name";
$fieldlevel_arr[465][3]="txt_boe_mushak_challan_date";
$fieldlevel_arr[465][4]="txt_boe_mushak_challan_no";

// BTB/Margin LC
$fieldlevel_arr[105][1]="txt_lc_serial";
$fieldlevel_arr[105][2]="txt_file";
// BTB/Margin LC
$fieldlevel_arr[470][1]="cbo_country_id";

//Embellishment work order without order
$fieldlevel_arr[399][1]="cbo_buyer_name";
$fieldlevel_arr[399][2]="cbo_currency";

//Multi Job Wise Print Booking
$fieldlevel_arr[201][1]="cbo_buyer_name";
$fieldlevel_arr[201][2]="cbo_currency";

//Multi Job Wise Embellishment work order
$fieldlevel_arr[403][1]="cbo_currency";

//Trims Order Receive
$fieldlevel_arr[255][1]="txt_is_file_uploaded";
$fieldlevel_arr[255][2]="txt_order_no";
$fieldlevel_arr[255][3]="cbo_team_leader";
$fieldlevel_arr[255][4]="cbo_team_member";
$fieldlevel_arr[255][5]="cbo_payterm_id";

// Woven Cut And Lay Entry Ratio Wise
$fieldlevel_arr[289][1]="txt_marker_cons";
//Cut and Lay Entry Ratio Wise 3
$fieldlevel_arr[490][1]="txt_table_no";
$fieldlevel_arr[490][2]="txt_marker_cons";
$fieldlevel_arr[490][3]="cbo_location_name";
$fieldlevel_arr[490][4]="txt_marker_length";
$fieldlevel_arr[490][5]="txt_marker_width";
$fieldlevel_arr[490][6]="txt_fabric_width";
$fieldlevel_arr[490][7]="cbo_width_dia";
$fieldlevel_arr[490][8]="cbo_shift_name";
$fieldlevel_arr[490][9]="txt_other_fabric_weight";

//Cutting QC V2
$fieldlevel_arr[604][1]="cbo_shift_name";


//Multi Job Wise Service Booking Knitting
$fieldlevel_arr[228][1]="txt_program_no";
$fieldlevel_arr[228][2]="cbo_supplier_name";

//Multi Job Wise Service Booking dying
$fieldlevel_arr[229][1]="cbo_supplier_name";


//Comparative Statement
$fieldlevel_arr[481][1]="cbo_source";

$fieldlevel_arr[452][1]="txt_reporting_hour";
$fieldlevel_arr[452][2]="cbo_poly_line";

// Order Entry By Matrix v2
$fieldlevel_arr[493][1]="txt_sc_lc";
$fieldlevel_arr[493][2]="txt_file_year";
$fieldlevel_arr[493][3]="txt_file_no";
$fieldlevel_arr[493][4]="cbo_qltyLabel";
$fieldlevel_arr[493][5]="cbo_brand_id";


// Sewing Output
$fieldlevel_arr[500][1]="cbo_shift_name";
$fieldlevel_arr[500][2]="txt_wo_no";

// Buyer Inquiry Sweater
$fieldlevel_arr[457][1]="cbo_product_department";
$fieldlevel_arr[457][2]="cbo_brand_id";

// Short Quatation sweater
$fieldlevel_arr[511][1]="cbo_brand";
$fieldlevel_arr[511][2]="txt_offerQty";
$fieldlevel_arr[511][3]="txt_quotedPrice";

// Quick Costing Woven
$fieldlevel_arr[430][1]="cbo_brand";
$fieldlevel_arr[430][2]="cbo_company_id";
$fieldlevel_arr[430][3]="cbo_location_id";
$fieldlevel_arr[430][4]="txt_bodywashcolor";
$fieldlevel_arr[430][5]="txtmarign";


//Cut and Lay Entry Ratio Wise 4
$fieldlevel_arr[509][1]="txt_marker_cons";

//Sample Sewing Output
$fieldlevel_arr[629][1]="cbo_location";

//Embroidery Production

$fieldlevel_arr[315][1]="cbo_floor_id";

//Printing Production
$fieldlevel_arr[222][1]="cbo_floor_id";

//Bundle Wise Sewing Output
$fieldlevel_arr[273][1]="cbo_responsible_person";
$fieldlevel_arr[273][2]="txt_reason";
$fieldlevel_arr[273][3]="cbo_responsible_dept";

//Stationary Purchase Order
$fieldlevel_arr[146][1]="cbo_location";


// Order Entry By Matrix v2
$fieldlevel_arr[518][1]="cbo_brand_id";
$fieldlevel_arr[518][2]="cbo_factory_merchant";

//YD Order Entry
$fieldlevel_arr[374][1]="txt_delivery_date";
$fieldlevel_arr[374][2]="txt_order_no";
$fieldlevel_arr[374][3]="cbo_pro_type";
$fieldlevel_arr[374][4]="cbo_order_type";
$fieldlevel_arr[374][5]="cbo_yd_type";
$fieldlevel_arr[374][6]="cbo_yd_process";
$fieldlevel_arr[374][7]="cbo_team_leader";
$fieldlevel_arr[374][8]="cbo_team_member";

//Doc. Submission to Buyer
$fieldlevel_arr[39][1]="txt_possible_reali_date";


//Doc. Submission to Bank
$fieldlevel_arr[40][1]="txt_possible_reali_date";
$fieldlevel_arr[40][2]="txt_bank_ref";




//Export Pro Forma Invoice
$fieldlevel_arr[152][1]="hs_code";
$fieldlevel_arr[152][2]="pi_validity_date";
$fieldlevel_arr[152][3]="cbo_advising_bank";
$fieldlevel_arr[152][4]="cbo_pay_term";

//Quotation/Buyer Costing
$fieldlevel_arr[471][1]="cbofabuom";

//Short Quotation V6
$fieldlevel_arr[634][1]="cbo_costing_per";
$fieldlevel_arr[634][2]="cbo_brand";
$fieldlevel_arr[634][3]="cbo_company_id";
$fieldlevel_arr[634][4]="cbo_location_id";
$fieldlevel_arr[634][5]="txtmarign";
$fieldlevel_arr[634][5]="cbo_season_id";

//Yarn Dyeing Work Order Sales
$fieldlevel_arr[135][1]="txt_dyeing_charge";


// Bundle Receive From Embroidery
$fieldlevel_arr[537][1]="txt_boe_mushak_challan_no";
$fieldlevel_arr[537][2]="txt_boe_mushak_challan_date";

// Finish Fabric Production Entry
$fieldlevel_arr[7][1]="cbo_fabric_shade";

// Supplier Profile
$fieldlevel_arr[527][1]="cbo_country";

// Import Document Acceptance Bank
$fieldlevel_arr[532][1]="txt_document_value";

//Quotation Inquery
$fieldlevel_arr[545][1]="txt_possible_order_con_date";
$fieldlevel_arr[545][2]="txt_offer_qty";
$fieldlevel_arr[545][3]="txt_target_req_cout_date";
$fieldlevel_arr[545][4]="txt_target_samp_date";
$fieldlevel_arr[545][5]="txt_req_quot_date";
$fieldlevel_arr[545][6]="cbo_customer_year";
$fieldlevel_arr[545][7]="cbo_week";

// subcon Knitting Production
$fieldlevel_arr[159][1]="cbo_shift_id";


// Raw Material Issue
$fieldlevel_arr[265][1]="cbo_issue_source";
$fieldlevel_arr[265][2]="cbo_issue_to";


// Dyes And Chemical Issue Requisition
$fieldlevel_arr[156][1]="txt_machine_no";

// Cutting Delivery To Input Challan
$fieldlevel_arr[589][1]="cbo_cutting_company";
$fieldlevel_arr[589][2]="cbo_cut_com_location";
$fieldlevel_arr[589][3]="cbo_sewing_company";
$fieldlevel_arr[589][4]="cbo_sew_com_location";

// AOP Order Entry
$fieldlevel_arr[278][1]="multiple_file_field";

// Embellishment Issue Entry
$fieldlevel_arr[601][1]="cbo_work_order";
$fieldlevel_arr[601][2]="cbo_sending_location";

// Service Category
$fieldlevel_arr[732][1]="txt_service_code";
$fieldlevel_arr[732][2]="txt_service_group";

//$fieldlevel_arr[727][1]="cbo_sending_location";
//$fieldlevel_arr['title'][727][1]="Receiving Location";
// Embellishment Issue Entry
$fieldlevel_arr[602][1]="cbo_work_order";
$fieldlevel_arr[602][2]="cbo_file";
$fieldlevel_arr[602][3]="cbo_sending_location";
$fieldlevel_arr['title'][602][3]="Receiving Location";
//Sample Embellishment Issue
$fieldlevel_arr[338][1]="cbo_status_id";
//Sample Embellishment Receive
$fieldlevel_arr[128][1]="cbo_status_id";
//Fabric Requisition
$fieldlevel_arr[611][1]="cbo_brand_id";
// Gmts. Issue to Wash V2
$fieldlevel_arr[645][1]="cbo_sending_location";
$fieldlevel_arr[645][2]="cbo_floor";
// Gmts. Receive From Wash V2
$fieldlevel_arr[648][1]="cbo_received_location";
$fieldlevel_arr[648][2]="cbo_floor";

//Style Ref Entry
$fieldlevel_arr[649][1]="cbo_gmt_item";

//Time And Weight Record
$fieldlevel_arr[245][1]="txt_efficiency";
 
//Color Ingredients
$fieldlevel_arr[701][1]="txt_colorDesc";
$fieldlevel_arr[701][2]="txt_pantone";

// Cut and Lay Entry Ratio Wise 2
$fieldlevel_arr[711][1]="txt_marker_length";
$fieldlevel_arr[711][2]="txt_marker_width";
$fieldlevel_arr[711][3]="txt_fabric_width"; 
$fieldlevel_arr[711][4]="txt_gsm";
$fieldlevel_arr[711][5]="txt_marker_cons";

//SCM->Material Purchase [General]->Others Purchase Order
$fieldlevel_arr[147][1]="select_cbonature_1";
$fieldlevel_arr['title'][147][1]="Nature";
$fieldlevel_arr[147][2]="select_cboProfitCanter_1";
$fieldlevel_arr['title'][147][2]="Profit Canter";

$fieldlevel_arr[728][1]="cbonature_1";
$fieldlevel_arr['title'][728][1]="Nature";
$fieldlevel_arr[728][2]="cboProfitCanter_1";
$fieldlevel_arr['title'][728][2]="Profit Center";


?>