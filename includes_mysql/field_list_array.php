<?
//This function will return page wise array
function get_fieldlevel_arr( $index )
{
	global $fieldlevel_arr;
	$field_arr=array();
	foreach($fieldlevel_arr[$index] as $key=>$val)
	{
		$value=explode("_",$val);
		$i=0;
		$str='';
		foreach($value as $k=>$v)
		{
			if($i!=0) $str .=" ".ucwords ($v);
			$i++;
		}
		$field_arr[$key]= $str;
	}
	return $field_arr;
}

$fieldlevel_arr[18][1]="cbo_sales_order"; //Knit Finish Fabric Issue
$fieldlevel_arr[18][2]="txt_issue_date"; //Knit Finish Fabric Issue

$fieldlevel_arr[86][1]="cbo_company_name";
$fieldlevel_arr[86][2]="cbo_buyer_name";
$fieldlevel_arr[86][3]="txt_job_no";
$fieldlevel_arr[86][4]="txt_booking_no";
$fieldlevel_arr[86][5]="cbo_fabric_natu";
$fieldlevel_arr[86][6]="cbo_fabric_source";
$fieldlevel_arr[86][7]="cbo_currency";
$fieldlevel_arr[86][8]="txt_exchange_rate";
$fieldlevel_arr[86][9]="cbo_pay_mode";
$fieldlevel_arr[86][10]="txt_booking_date";
$fieldlevel_arr[86][11]="cbo_booking_month";
$fieldlevel_arr[86][12]="cbo_supplier_name";
$fieldlevel_arr[86][13]="cbo_supplier_name";
$fieldlevel_arr[86][14]="txt_attention";
$fieldlevel_arr[86][15]="txt_delivery_date";
$fieldlevel_arr[86][16]="cbo_source";
$fieldlevel_arr[86][17]="cbo_booking_year";
$fieldlevel_arr[86][18]="txt_booking_percent";
$fieldlevel_arr[86][19]="txt_colar_excess_percent";
$fieldlevel_arr[86][20]="txt_cuff_excess_percent";
$fieldlevel_arr[86][21]="cbo_ready_to_approved";
$fieldlevel_arr[86][22]="processloss_breck_down";
$fieldlevel_arr[86][23]="txt_fabriccomposition";
$fieldlevel_arr[86][24]="txt_intarnal_ref";
$fieldlevel_arr[86][25]="txt_file_no";



$fieldlevel_arr[88][1]="txt_order_no_id";
$fieldlevel_arr[88][2]="cbo_company_name";
$fieldlevel_arr[88][3]="cbo_buyer_name";
$fieldlevel_arr[88][4]="txt_job_no";
$fieldlevel_arr[88][5]="txt_booking_no";
$fieldlevel_arr[88][6]="cbo_fabric_natu";
$fieldlevel_arr[88][7]="cbo_fabric_source";
$fieldlevel_arr[88][8]="cbo_currency";
$fieldlevel_arr[88][9]="txt_exchange_rate";
$fieldlevel_arr[88][10]="cbo_pay_mode";
$fieldlevel_arr[88][11]="txt_booking_date";
$fieldlevel_arr[88][12]="cbo_booking_month";
$fieldlevel_arr[88][13]="cbo_supplier_name";
$fieldlevel_arr[88][14]="txt_attention";
$fieldlevel_arr[88][15]="txt_delivery_date";
$fieldlevel_arr[88][16]="cbo_source";
$fieldlevel_arr[88][17]="cbo_booking_year";
$fieldlevel_arr[88][18]="cbo_ready_to_approved";

$fieldlevel_arr[88][19]="cbo_order_id";
$fieldlevel_arr[88][20]="cbo_fabricdescription_id";
$fieldlevel_arr[88][21]="cbo_fabriccolor_id";
$fieldlevel_arr[88][22]="cbo_garmentscolor_id";
$fieldlevel_arr[88][23]="txt_process_loss";
$fieldlevel_arr[88][24]="txt_grey_qnty";
$fieldlevel_arr[88][25]="txt_rate";
$fieldlevel_arr[88][26]="txt_amount";
$fieldlevel_arr[88][27]="txt_rmg_qty";
$fieldlevel_arr[88][28]="cbo_responsible_dept";
$fieldlevel_arr[88][29]="cbo_responsible_person";
$fieldlevel_arr[88][30]="txt_reason";




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



$fieldlevel_arr[90][1]="cbo_company_name";
$fieldlevel_arr[90][2]="cbo_buyer_name";
$fieldlevel_arr[90][3]="txt_booking_no";
$fieldlevel_arr[90][4]="cbo_fabric_natu";
$fieldlevel_arr[90][5]="cbo_fabric_source";
$fieldlevel_arr[90][6]="cbo_currency";
$fieldlevel_arr[90][7]="txt_exchange_rate";
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
$fieldlevel_arr[3][1]="cbo_sales_order"; // Yarn Requisition Search Form Field //txt_issue_date
$fieldlevel_arr[3][2]="txt_issue_date"; // Yarn Issue 
$fieldlevel_arr[1][1]="txt_receive_date"; // Yarn Recv

$fieldlevel_arr[109][1]="cbo_company_id";//Fabriic Sales Order Entry
$fieldlevel_arr[109][2]="cbo_within_group";
$fieldlevel_arr[109][3]="cbo_currency";
$fieldlevel_arr[109][4]="cbo_ship_mode";


//Knitting Bill Issue
$fieldlevel_arr[186][1]="cbo_party_source"; //cbo_party_source
//Garments Delivery Entry
$fieldlevel_arr[198][1]="shipping_status";

//Multiple Job Wise Embellishment Work Order
$fieldlevel_arr[201][1]="cbo_pay_mode";
$fieldlevel_arr[201][2]="cbo_level"; //level


//Multiple Job Wise Embellishment Work Order
$fieldlevel_arr[403][1]="cbo_pay_mode";
$fieldlevel_arr[403][2]="cbo_level"; //level

// Batch Creation
$fieldlevel_arr[408][1]="cbo_double_dyeing"; 


$fieldlevel_arr[22][1]="txt_receive_date";  // knit gray fabric receive

$fieldlevel_arr[16][1]="txt_issue_date";  // knit gray fabric Issue

$fieldlevel_arr[225][1]="txt_receive_date";  // Knit Finish Fabric Receive By Garments

$fieldlevel_arr[24][1]="txt_receive_date";  // Trims Receive

$fieldlevel_arr[25][1]="txt_issue_date";  // Trims Issue 

$fieldlevel_arr[250][1]="txt_receive_date";  // Trims Receive Entry Multi Ref.
$fieldlevel_arr[163][1]="txt_file_no";  // Order entry
$fieldlevel_arr[163][2]="txt_sc_lc"; // Order entry

$fieldlevel_arr[351][1]="txt_grouping"; // Order Entry By Matrix Woven.
$fieldlevel_arr[351][2]="cbo_breakdown_type";
$fieldlevel_arr[351][3]="txt_factory_rec_date";


$fieldlevel_arr[493][1]="txt_sc_lc";  // Order Entry By Matrix V2
$fieldlevel_arr[493][2]="txt_item_catgory";
$fieldlevel_arr[493][3]="cbo_order_uom";
$fieldlevel_arr[493][4]="cbo_breakdown_type";

//woven partial fabric Booking
$fieldlevel_arr[271][1]="cbouom"; //UOM
$fieldlevel_arr[271][2]="cbo_fabric_source"; //Fabric Source
$fieldlevel_arr[271][3]="cbo_pay_mode"; //Pay Mode
$fieldlevel_arr[271][4]="cbo_fabric_natu";
$fieldlevel_arr[271][5]="txt_delivery_date"; //check tna
$fieldlevel_arr[271][6]="cbo_source"; //check Source
$fieldlevel_arr[271][7]="cbo_level"; //level


$fieldlevel_arr[365][1]="txt_sc_lc";  // Order Entry By Matrix
$fieldlevel_arr[365][2]="txt_item_catgory";
$fieldlevel_arr[365][3]="cbo_team_leader";


// pre costing v2 Woven
$fieldlevel_arr[425][1]="cbo_fabric_costing_uom"; //uom fabric cost part
$fieldlevel_arr[425][2]="cbo_fabric_costing_fab_nature"; //Fab Nature fabric cost part
$fieldlevel_arr[425][3]="txt_sew_efficiency_per"; // Sew Efficiency %
$fieldlevel_arr[425][4]="cbo_costing_per"; // Sew Efficiency %
$fieldlevel_arr[425][5]="cbo_add_file"; // Add File
$fieldlevel_arr[425][6]="txt_costing_date"; // Add File


// Sourcing Post Cost Sheet
$fieldlevel_arr[469][1]="txt_sourcing_date"; // txt_sourcing_date


?>