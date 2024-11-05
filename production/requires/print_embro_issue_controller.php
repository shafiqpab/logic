<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
//------------------------------------------------------------------------------------------------------
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

if($db_type==0) $select_field="group";
else if($db_type==2) $select_field="wm";
else $select_field="";//defined Later

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 200, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/print_embro_issue_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}
 
if ($action=="load_drop_down_floor")
{
 	echo create_drop_down( "cbo_floor", 200, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (7,8,9) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
	exit();
}

if ($action == "load_drop_down_lc_location") {
 echo create_drop_down("cbo_location_lc", 200, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name ", "id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/print_embro_issue_controller', this.value, 'load_drop_down_lc_floor', 'floor_lc_td' );", 0);
 exit();
}

if ($action == "load_drop_down_lc_floor") {
 echo create_drop_down("cbo_floor_lc", 200, "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name", "id,floor_name", 1, "-- Select Floor --", $selected, "", 0);
 exit();
}

if($action=="print_button_variable_setting") //Print Button
{
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=21 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();
}

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("printing_emb_production")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=33 and page_category_id=28","is_control");
	echo "document.getElementById('variable_is_controll').value='".$variable_is_control."';\n";

	echo "$('#wip_valuation_for_accounts').val(0);\n";
	$wip_valuation_for_accounts = return_field_value("allow_fin_fab_rcv", "variable_settings_production", "company_name=$data and variable_list=76 and status_active=1 and is_deleted=0");
	echo "$('#wip_valuation_for_accounts').val($wip_valuation_for_accounts);\n";
	if($wip_valuation_for_accounts==1)
	{
		echo "$('#wip_valuation_for_accounts_button').show();\n";
	}
	else
	{
		echo "$('#wip_valuation_for_accounts_button').hide();\n";
	}

	

 	exit();
}

if($action=="show_cost_details")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$lib_color=return_library_array( "select id, color_name from lib_color",'id','color_name');
	// $sqlResult =sql_select("SELECT b.po_number,a.country_id,a.item_number_id, a.cost_of_fab_per_pcs,a.cut_oh_per_pcs,a.trims_cost_per_pcs,a.cost_per_pcs from pro_garments_production_mst a,wo_po_break_down b,lib_country c where b.id=a.po_break_down_id and a.country_id=c.id and a.po_break_down_id='$sys_id' and a.status_active=1 and a.is_deleted=0 and a.production_type=2 and a.embel_name=3");

	$sqlResult =sql_select("SELECT b.id as po_id,b.po_number,c.item_number_id,c.color_number_id, a.cost_of_fab_per_pcs,a.cut_oh_per_pcs,a.cost_per_pcs,a.fab_rate_per_pcs,a.trims_cost_per_pcs from pro_garments_production_mst e, pro_garments_production_dtls a,WO_PO_COLOR_SIZE_BREAKDOWN c,wo_po_break_down b,lib_country d where  e.id=a.mst_id and a.COLOR_SIZE_BREAK_DOWN_ID=c.id and b.id=c.po_break_down_id  and c.country_id=d.id and c.po_break_down_id=e.po_break_down_id and c.po_break_down_id='$sys_id' and a.status_active=1 and a.is_deleted=0 and a.production_type=2  and e.embel_name=3 and a.cost_per_pcs is not null");

	if(count($sqlResult)==0)
	{
		?>
		<div class="alert alert-danger">Data not found!</div>
		<?
		die;
	}
	$data_array = array();
	$po_id_arr = array();
	$itm_id_arr = array();
	$color_id_arr = array();
	foreach ($sqlResult as $v)
	{
		$po_id_arr[$v['PO_ID']] = $v['PO_ID'];
		$itm_id_arr[$v['ITEM_NUMBER_ID']] = $v['ITEM_NUMBER_ID'];
		$color_id_arr[$v['COLOR_NUMBER_ID']] = $v['COLOR_NUMBER_ID'];

		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['fab_rate_per_pcs'] = $v['FAB_RATE_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['trims_cost_per_pcs'] = $v['TRIMS_COST_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['po_number'] = $v['PO_NUMBER'];
	}
	$poIds = where_con_using_array($po_id_arr,0,"c.po_break_down_id");
	$itmIds = where_con_using_array($itm_id_arr,0,"c.item_number_id");
	$colorIds = where_con_using_array($color_id_arr,0,"c.color_number_id");

	$sqlResult =sql_select("SELECT b.id as po_id,b.po_number,c.item_number_id,c.color_number_id, a.cost_of_fab_per_pcs,a.cut_oh_per_pcs,a.cost_per_pcs,a.fab_rate_per_pcs,a.trims_cost_per_pcs from pro_garments_production_dtls a,WO_PO_COLOR_SIZE_BREAKDOWN c,wo_po_break_down b,lib_country d where a.COLOR_SIZE_BREAK_DOWN_ID=c.id and b.id=c.po_break_down_id  and c.country_id=d.id and a.status_active=1 and a.is_deleted=0 and a.production_type=5 $poIds $itmIds $colorIds");// and a.embel_name=2
	$trims_rate = array();
	foreach ($sqlResult as $v)
	{
		$trims_rate[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']] = $v['TRIMS_COST_PER_PCS'];
	}

	?>
 		<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="100">PO</th>
				<th width="100">Item</th>
				<th width="100">Color</th>
				<th width="90">Sew Output Rate</th>
				<th width="90">Trims Cost</th>
				<th width="90">Sewing OH</th>
				<th width="90">Cost Per Pcs</th>
			</thead>
			<tbody>
				<?
				$i=1;
				foreach ($data_array as $po_id=>$po_data)
				{
					foreach ($po_data as $itm_id=>$itm_data)
					{
						foreach ($itm_data as $color_id=>$v)
						{
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>">
								<td><?=$v['po_number'];?></td>
								<td><?=$garments_item[$itm_id];?></td>
								<td><?=$lib_color[$color_id];?></td>
								<td align="right"><?=$v['cost_of_fab_per_pcs'];?></td>
								<td align="right"><?=$trims_rate[$po_id][$itm_id][$color_id];?></td>
								<td align="right"><?=$v['cut_oh_per_pcs'];?></td>
								<td align="right"><?=$v['cost_per_pcs'];?></td>
							</tr>
							<?
						}
					}
				}
				?>
			</tbody>
		</table>
	<?

	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "txt_search_common", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

if($action=="load_drop_down_embro_issue_source")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];


	if($data==3)
	{
		/*if($db_type==0)
		{
			echo create_drop_down( "cbo_emb_company", 200, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(23,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "load_location();",0,0 );
		}
		else
		{*/
			echo create_drop_down( "cbo_emb_company", 200, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "fnc_workorder_search(this.value);" );
		//}
	}
	else if($data==1)
		echo create_drop_down( "cbo_emb_company", 200, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_location();fnc_workorder_search(this.value);",0,0 );
	else
		echo create_drop_down( "cbo_emb_company", 200, $blank_array,"", 1, "--- Select ---", $selected, "load_location();fnc_workorder_search(this.value)",0,0 );

	exit();
}

if($action=="load_drop_down_embel_name")
{
	//echo $data;
    echo create_drop_down( "cbo_embel_name", 200, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "load_drop_down( 'requires/print_embro_issue_controller', this.value+'**'+$('#hidden_po_break_down_id').val(), 'load_drop_down_embro_issue_type', 'embro_type_td'); get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#sewing_production_variable').val()+'**'+$('#styleOrOrderWisw').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val()+'**'+$('#cbo_company_name').val()+'**'+$('#cbo_embel_type').val()+'**'+$('#country_ship_date').val(), 'color_and_size_level', 'requires/print_embro_issue_controller' ); show_list_view($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(),'show_dtls_listview','printing_production_list_view','requires/print_embro_issue_controller','setFilterGrid(\'tbl_search\',-1)'); ","",$data );// get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(), 'populate_data_from_search_popup', 'requires/print_embro_issue_controller' );
}

if($action=="load_drop_down_embro_issue_type")
{
	$data=explode("**",$data);
	$emb_name=$data[0];
	$po_id=$data[1];
	/*if($data==1)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "" );
	elseif($data==2)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_embroy_type,"", 1, "--- Select Embroidery---", $selected, "" );
	elseif($data==3)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_wash_type,"", 1, "--- Select wash---", $selected, "" );
	elseif($data==4)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_spwork_type,"", 1, "--- Select Special Works---", $selected, "" );
	elseif($data==5)
		echo create_drop_down( "cbo_embel_type", 200, $blank_array,"", 1, "--- Select---", $selected, "" );
	else
		echo create_drop_down( "cbo_embel_type", 200, $blank_array,"", 1, "--- Select---", $selected, "" );	*/

	if($db_type==0) $embel_name_cond="group_concat(c.emb_type) as emb_type";
	else if($db_type==2) $embel_name_cond="LISTAGG(c.emb_type,',') WITHIN GROUP ( ORDER BY c.emb_type) as emb_type";
	$embl_type=return_field_value("$embel_name_cond","wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c","a.job_id=b.id and b.id=c.job_id and a.id=$po_id and c.emb_name=$emb_name","emb_type");

	if($emb_name==1)
	{
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "fn_generate_color_size_break_down(this.value)" ,"","$embl_type");}
	elseif($emb_name==2)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_embroy_type,"", 1, "--- Select Embroidery---", $selected ,"fn_generate_color_size_break_down(this.value)","","$embl_type" );
	elseif($emb_name==3)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_wash_type,"", 1, "--- Select wash---", $selected,"fn_generate_color_size_break_down(this.value)","","$embl_type" );
	elseif($emb_name==4)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_spwork_type,"", 1, "--- Select Special Works---", $selected,"fn_generate_color_size_break_down(this.value)","","$embl_type" );
	elseif($emb_name==5)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_gmts_type,"", 1, "--- Select---", $selected,"fn_generate_color_size_break_down(this.value)","","$embl_type" );
	else
		echo create_drop_down( "cbo_embel_type", 200, $blank_array,"", 1, "--- Select---", $selected, "fn_generate_color_size_break_down(this.value)" );
}
if ($action=="load_drop_down_workorder")
{
	$explode_data = explode("_",$data);
	$company_id=$explode_data[0];
	$supplier_id=$explode_data[1];
	$po_break_down_id=$explode_data[2];
	$emblishment_name=$explode_data[3];
	$gmt_item=$explode_data[4];
	$sql= "SELECT a.id,a.booking_no  from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c,wo_pre_cos_emb_co_avg_con_dtls d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and c.id=d.pre_cost_emb_cost_dtls_id and b.po_break_down_id=d.po_break_down_id and  a.booking_type=6 and a.is_short=2 and  a.status_active=1  and a.is_deleted=0 and a.supplier_id=".$supplier_id." and b.gmt_item= ".$gmt_item." and c.emb_name=".$emblishment_name." and a.company_id=".$company_id." and d.po_break_down_id=$po_break_down_id  group by a.id,a.booking_no  order by a.booking_no";
	// echo $sql;die;
	 echo create_drop_down( "cbo_work_order", 200, $sql,"id,booking_no", 2, "-- Select Work Order --", $selected, "fnc_workorder_rate('$data',this.value)",0);
}
if($action=="populate_workorder_rate")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$supplier_id=$data[1];
	$po_break_down_id=$data[2];
	$emblishment_name=$data[3];
	$gmt_item=$data[4];
	$sql= "select a.id,a.exchange_rate,a.currency_id ,sum(b.amount)/sum(b.wo_qnty) as rate from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c where a.id=".$data[5]." and a.company_id=".$company_id." and a.job_no=b.job_no and  a.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_type=6 and a.is_short=2 and  a.status_active=1  and 	a.is_deleted=0 and a.supplier_id=".$supplier_id." and b.gmt_item= ".$gmt_item." and c.emb_name=".$emblishment_name."  group by a.id,a.exchange_rate,a.currency_id";
	//echo $sql;
	$result=sql_select($sql);
	//print_r($result);
	$rate=$result[0][csf('rate')]/12;

	echo "$('#workorder_rate_td').text('');\n";
	echo "$('#hidden_currency_id').val('".$result[0][csf('currency_id')]."');\n";
	echo "$('#hidden_exchange_rate').val('".$result[0][csf('exchange_rate')]."');\n";
	echo "$('#hidden_piece_rate').val('".$rate."');\n";
	$rate_string='';


	if(trim($rate)!="" && trim($rate)>0)
	{
		$rate_string=$rate." ".$currency[$result[0][csf('currence')]];
		$rate_string="Work Order Rate ".$rate_string." /Pcs";
		echo "$('#workorder_rate_td').text('".$rate_string."');\n";
	}
	//echo "$('#workorder_rate_td').text('".$rate."');\n";
	//echo "$('#txt_style_no').val('".$sql[0][csf('style_ref_no')]."');\n";
}
if ($action=="create_booking_search_list_view")
{

	$data=explode('_',$data);
	 //echo "<pre>";print_r($data);
	if ($data[0]!=0) $company=" and c.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $buyer=" and c.buyer_name='$data[1]'"; else $buyer="";

    if($db_type==0)
    {
    	if ($data[2]!="" &&  $data[3]!="") $wo_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $wo_date ="";
    }

    if($db_type==2)
    {
    	if ($data[2]!="" &&  $data[3]!="") $wo_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $wo_date ="";
    }
    //echo $data[8];
    if($data[6]==1)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num='$data[5]'    "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num='$data[4]'  "; else  $job_cond="";
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no='$data[8]'  "; else  $style_cond="";
    }
    if($data[6]==4 || $data[6]==0)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond="";
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]%'  "; else  $style_cond="";
    }

    if($data[6]==2)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '$data[5]%'  $booking_year_cond  "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond="";
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '$data[8]%'  "; else  $style_cond="";
    }

    if($data[6]==3)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '%$data[5]'  $booking_year_cond  "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond="";
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]'  "; else  $style_cond="";
    }

    if ($data[9]!="")
    {
    	foreach(explode(",", $data[9]) as $bok){
    		$bookingnos .= "'".$bok."',";
    	}
    	$bookingnos = chop($bookingnos,",");
		if( $service_source!=1)
		{
    	$preBookingNos_1 = " and a.booking_no not in (".$bookingnos.")";
    	$preBookingNos_2 = " and a.wo_no not in (".$bookingnos.")";
		}
    }
    if ($data[10]!="")
    {
    	$po_number_cond = " and d.po_number = '$data[10]'";
    }
    if ($data[11]!="")
    {
    	$internal_ref_cond = " and d.grouping = '$data[11]'";
    }
    if ($data[12]!="")
    {
    	$file_cond = " and d.file_no = '$data[12]'";
    }
    if ($data[14]!="")
    {
    	$po_id_cond = " and d.id = '$data[14]'";
    }


    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
    $po_no=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');

    $arr=array (2=>$comp,3=>$conversion_cost_head_array,4=>$buyer_arr,7=>$po_no,8=>$item_category,9=>$fabric_source,10=>$suplier);

	$sql= "SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.wo_date, c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no, c.job_no, b.po_id, d.file_no,d.po_number,d.grouping
	from garments_service_wo_mst a, garments_service_wo_dtls b, wo_po_details_master c ,wo_po_break_down d
	where a.id = b.mst_id  and b.po_id=d.id and c.id=d.job_id $company $buyer $wo_date $wo_cond $style_cond $file_cond $po_number_cond $internal_ref_cond $po_id_cond and a.status_active=1 and a.is_deleted=0 and b.rate_for=30 $job_cond
	group by a.id, a.sys_number_prefix_num, a.sys_number, a.wo_date, c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no, c.job_no, b.po_id, d.file_no,d.po_number,d.grouping";
   	 //echo $sql;
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table">
    	<thead>
    		<tr>
    			<th width="20">SL No.</th>
    			<th width="120">WO No</th>
    			<th width="60">WO Date</th>
    			<th width="80">Company</th>
    			<th width="100">Buyer</th>
    			<th width="50">Job No</th>

    			<th width="70">Internal Ref.</th>
    			<th width="70">File No</th>


    			<th width="100">Style No.</th>
    			<th width="100">PO number</th>
    		</tr>
    	</thead>
    </table>
    <div style="width:1288px; max-height:400px; overflow-y:scroll;" >
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" id="tbl_list_search" >
    		<tbody>
    			<?
    			$result = sql_select($sql);
	    		$i=1;
	            foreach($result as $row)
	            {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                     <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('sys_number')]; ?>');">

						<td width="20"><? echo $i; ?></td>
						<td width="120"><p><? echo $row[csf('sys_number')]; ?></p></td>
						<td width="60"><p><? echo change_date_format($row[csf('wo_date')]); ?></p></td>
						<td width="80"><p><? echo $comp[$row[csf('company_name')]]; ?></p></td>

						<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
						<td width="50"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>


						<td width="70"><p><? echo $row[csf('grouping')]; ?></p></td>
						<td width="70"><p><? echo $row[csf('file_no')]; ?></p></td>

						<td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>

					</tr>
					<?
					$i++;
    			}
    			?>
    		</tbody>
    	</table>
    </div>
    <script type="text/javascript">
    	setFilterGrid("tbl_list_search",-1);
    </script>
    <?

    exit();
}

if ($action=="service_booking_popup")
{
	echo load_html_head_contents("Booking Search","../../", 1, 1, $unicode);
	extract($_REQUEST);
	$preBookingNos = 0;
	?>

	<script>

		function js_set_value(booking_no)
		{
			// alert(booking_no);
			document.getElementById('selected_booking').value=booking_no; //return;
	 	 	parent.emailwindow.hide();
		}

	</script>

	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="1300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<tr>
					<td align="center" width="100%">
						<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                        	 <input type="hidden" id="selected_batchDtls" class="text_boxes" style="width:70px" value="<? echo $txt_batch_dtls;?>">
                              <input type="hidden" id="booking_no" class="text_boxes" style="width:70px" value="">
                              <input type="hidden" id="booking_id" class="text_boxes" style="width:70px">


							<thead>
								<th  colspan="11">
									<?
									echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",1 );
									?>
								</th>
							</thead>
							<thead>
								<th width="150">Company Name</th>
								<th width="150">Supplier Name</th>
								<th width="150">Buyer  Name</th>
								<th width="100">Job  No</th>
								<th width="100">Order No</th>
								<th width="100">Internal Ref.</th>
								<th width="100">File No</th>
								<th width="100">Style No.</th>
								<th width="100">WO No</th>
								<th width="200">Date Range</th>
								<th></th>
							</thead>
							<tr>
								<td> <input type="hidden" id="selected_booking">
									<?
									echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", "".$company_id."", "load_drop_down( 'fabric_issue_to_finishing_process_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
									?>
								</td>
								<td>
									<?php
									if($cbo_service_source==3)
									{
										echo create_drop_down( "cbo_supplier_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and b.party_type in (21,24,25) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "".$supplier_id."", "",1 );
									}
									else
									{
										echo create_drop_down( "cbo_supplier_name", 152, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name", 1, "-- Select --", "".$supplier_id."", "",1 );
									}
									?>
								</td>
								<td id="buyer_td">
									<?
									echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
									?>
								</td>
								<td>
									<input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px">
								</td>


								<td>
									<input name="txt_order_number" id="txt_order_number" class="text_boxes" style="width:70px" readonly value="<?php echo $po_order_no;?>">
								</td>
								<td>
									<input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px">
								</td>
								<td>
									<input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px">
								</td>



								<td>
									<input name="txt_style" id="txt_style" class="text_boxes" style="width:70px">
								</td>
								<td>
									<input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px">
								</td>
								<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px">
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px">
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_style').value+'_'+<? echo $preBookingNos;?>+'_'+document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+<? echo $cbo_service_source;?>+'_'+<?php echo $po_order_id;?>, 'create_booking_search_list_view', 'search_div', 'print_embro_issue_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?>
						</td>
					</tr>

   </table>
   <div style="width:100%; margin-top:5px" id="search_div" align="left"></div>

	</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}


if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
?>
	<script>
	$(document).ready(function(e) {
		$("#txt_search_common").focus();
		$("#company_search_by").val(<?php echo $_REQUEST['company'] ?>);
	});

	function search_populate(str)
	{
		//alert(str);
		if(str==0)
		{
			document.getElementById('search_by_th_up').innerHTML="Order No";
			document.getElementById('search_by_td').innerHTML='<input onkeydown="getActionOnEnter(event)" type="text" name="txt_search_common" style="width:142px " class="text_boxes" id="txt_search_common"	value=""  />';
		}
		else if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
			document.getElementById('search_by_td').innerHTML='<input type="text" onkeydown="getActionOnEnter(event)" name="txt_search_common" style="width:142px " class="text_boxes" id="txt_search_common" value=""/>';
		}
		else if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="File no";
			document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:142px" class="text_boxes" id="txt_search_common" value=""/>';
		}
		else if(str==4)
		{
			document.getElementById('search_by_th_up').innerHTML="Internal Ref.";
			document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:142px" class="text_boxes" id="txt_search_common" value=""  />';
		}
		else if(str==5)
		{
			document.getElementById('search_by_th_up').innerHTML="Job No";
			document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:142px" class="text_boxes" id="txt_search_common" value=""/>';
		}

		else //if(str==2)
		{
			load_drop_down('print_embro_issue_controller',document.getElementById('company_search_by').value,'load_drop_down_buyer', 'search_by_td');
			document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
		}
	}

	function js_set_value(id,item_id,po_qnty,plan_qnty,country_id,ship_date)
	{
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id);
		$("#hidden_po_qnty").val(po_qnty);
		$("#hidden_country_id").val(country_id);
		$("#hidden_company_id").val(document.getElementById('company_search_by').value);
		$("#hid_country_ship_date").val(ship_date);
  		parent.emailwindow.hide();
 	}
</script>
</head>
<body>
<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
        <table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
                    <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                   		 <thead>
                        	<th width="130">Company</th>
                        	<th width="130">Search By</th>
                        	<th  width="130" align="center" id="search_by_th_up">Enter Order Number</th>
                        	<th width="200">Date Range</th>
                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    	</thead>
        				<tr>
        				    <td width="130">
        						<?
                                echo create_drop_down( "company_search_by", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "", 0 );
                                ?>
							</td>
                    		<td width="130">
							<?
							$searchby_arr=array(5=>"Job No",0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"File No",4=>"Internal Ref");
							echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
  							?>
                    		</td>
                   			<td width="130" align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
            				</td>
                    		<td align="center">
                            	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
					  			<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 		</td>
            		 		<td align="center">
                     			<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('company_search_by').value+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_po_search_list_view', 'search_div', 'print_embro_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                            </td>
        				</tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td  align="center" height="40" valign="middle">
					<? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_mst_id">
                    <input type="hidden" id="hidden_grmtItem_id">
                    <input type="hidden" id="hidden_po_qnty">
                    <input type="hidden" id="hidden_country_id">
                    <input type="hidden" id="hidden_company_id">
					<input type="hidden" id="hid_country_ship_date">
          		</td>
            </tr>
    	</table>
        <div style="margin-top:10px" id="search_div"></div>
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}
 

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	if($ex_data[4]== 0)
	{
		//print_r ($data);die;
		echo "Please Select Company First."; die;
	}
	if($ex_data[1]=="" && $ex_data[3]=="" )
	{
     echo "Please Select Search By OR Date Range Field"; die;
	}
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
 	$garments_nature = $ex_data[5];
	$year=$ex_data[6];
	 if($year !=0)
	 {
		 if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$year";}
	 }

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";
		else if(trim($txt_search_by)==3)
			$sql_cond = " and b.file_no=trim('$txt_search_common')";
		else if(trim($txt_search_by)==4)
			$sql_cond =  " and b.grouping like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==5)
			$sql_cond =  " and a.job_no_prefix_num like '%".trim($txt_search_common)."%'";
 	}
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";

	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$company");
    $projected_po_cond = ($is_projected_po_allow==2) ? " and b.is_confirmed=1" : "";
	if($year !=0)
	{
		if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$year";}
	}
	/*$sql = "select b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
			from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_embe_cost_dtls c
			where
			a.job_no = b.job_no_mst and a.job_no = c.job_no and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond
			group by b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut order by b.shipment_date"; */
		/*$sql = "select b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
			from wo_po_details_master a, wo_po_break_down b
			where a.job_no = b.job_no_mst and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond";*/ //old

 	  $sql = "SELECT b.id, a.job_no_prefix_num,a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c,wo_pre_cost_dtls d where a.id = b.job_id and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond $projected_po_cond $year_cond and a.id=c.job_id and c.job_no=d.job_no and ( d.embel_cost !=0 or d.wash_cost !=0 ) and b.shiping_status!=3 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by b.id desc"; //and b.shiping_status <> 3
    //    echo $sql;


	$result = sql_select($sql);
	$po_id_arr = array();
	foreach ($result as $val)
	{
		$po_id_arr[$val[csf('id')]] = $val[csf('id')];
	}
	$allPoIds = implode(",", $po_id_arr);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	//$po_country_arr=return_library_array( "select po_break_down_id, $select_field"."_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	if($db_type==0)
	{
		$po_country_arr=return_library_array( "SELECT po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "SELECT po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by po_break_down_id",'po_break_down_id','country');
	}

	$po_country_data_arr=array();
	$poCountryData=sql_select( "SELECT country_ship_date,pack_type, po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty,color_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by country_ship_date,pack_type,po_break_down_id, item_number_id, country_id,color_number_id");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('pack_type')]]['po_qnty'] +=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('pack_type')]]['plan_cut_qnty'] +=$row[csf('plan_cut_qnty')];
		$po_color_arr[$row[csf("po_break_down_id")]].=','.$row[csf("color_number_id")];
	}
	// echo "<pre>";
	// print_r($po_country_data_arr);die;

	$total_issu_qty_data_arr=array();
	$total_issu_qty_arr=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=2 and po_break_down_id in($allPoIds) group by po_break_down_id, item_number_id, country_id");

	foreach($total_issu_qty_arr as $row)
	{
		$total_issu_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
	}

	$total_cutting_qty_sql=( "SELECT po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=1 and po_break_down_id in($allPoIds) group by po_break_down_id, item_number_id, country_id");
	// echo $total_cutting_qty_sql;die;

	$cutting_sql=sql_select($total_cutting_qty_sql);

	$total_cutting_qty_data_arr=array();
	foreach($cutting_sql as $row)
	{
		$total_cutting_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
	}
	// echo "<pre>";
	// print_r($total_cutting_qty_data_arr);die;

	?>
    <div style="width:1270px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Shipment Date</th>
                <th width="50">Job No</th>
                <th width="100">Order No</th>
                <th width="80">Buyer</th>
                <th width="100">Style</th>
                <th width="80">File No</th>
                <th width="80">Internal Ref</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
				<th width="80"> Total Cutting Qty</th>
                <th width="80">Total Issue Qty</th>
                <th width="80">Balance</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1270px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1252" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));
				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
				$numOfCountry = count($country);

				$color=array_unique(explode(",",$po_color_arr[$row[csf("id")]]));
				// print_r($color_arr);
				$color_name = '';
				foreach ($color as $key => $value)
				{
					if ($color_name !='')
					{
						$color_name .=','.$color_arr[$value];
					}
					else
					{
					 	$color_name = $color_arr[$value];
					}
				}

				for($k=0;$k<$numOfItem;$k++)
				{
					if($row["total_set_qnty"]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}else
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}

					foreach($country as $country_id)
					{
						foreach ($po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id] as $coun_ship_date=>$coun_ship_date_data)
						{
							//$country_ship_date = $coun_ship_date_data;
							foreach ($coun_ship_date_data as $pack_type=>$pack_data)
							{
								$country_ship_date = $coun_ship_date;
								if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
								$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date][$pack_type]['po_qnty'];
								$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date][$pack_type]['plan_cut_qnty'];

						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>','<?  echo $country_ship_date  ?>');" >
							<td width="30" align="center"><?php echo $i; ?></td>
							<td width="60" align="center"><?php echo change_date_format($row[csf("shipment_date")]);?></td>
							<td width="50" align="center"><?php echo $row[csf("job_no_prefix_num")];?></td>
							<td width="100" title="<?=$color_name?$color_name:''?>"><p><?php echo $row[csf("po_number")]; ?></p></td>
							<td width="80"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
							<td width="100"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
                            <td width="80"><p><?php echo $row[csf("file_no")]; ?></p></td>
                            <td width="80"><p><?php echo $row[csf("grouping")]; ?></p></td>
							<td width="140"><p><?php  echo $garments_item[$grmts_item];?></p></td>
							<td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
							<td width="80" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
							<td width="80" align="right">
							<?php
								echo $total_cutting_qty=$total_cutting_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]
                             ?> &nbsp;
                           </td>
                            <td width="80" align="right">
							<?php
								echo $total_cut_qty=$total_issu_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]
                             ?> &nbsp;
                           </td>
						   
                           <td width="80" align="right">
							<?php
                             $balance=$po_qnty-$total_cut_qty;
                             echo $balance;
                             ?>&nbsp;
                           </td>
							<td><?php  echo $company_arr[$row[csf("company_name")]];?> </td>
						</tr>
						<?
						$i++;
					}
				}
			}
		}
      }
   		?>
        </table>
    </div>
	<?
	exit();
}

if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$embel_name = $dataArr[2];
	$country_id = $dataArr[3];

	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

	$cutData=sql_select("SELECT production_source, serving_company, location from pro_garments_production_mst WHERE po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and production_type=1");
	$cutt_comapny=""; $cutt_location="";
	foreach($cutData as $row)
	{
		if($row[csf('production_source')]==1)
		{
			if($cutt_comapny=="") $cutt_comapny=$comp[$row[csf('serving_company')]]; else $cutt_comapny.='**'.$comp[$row[csf('serving_company')]];
			if($cutt_location=="") $cutt_location=$location_arr[$row[csf('location')]]; else $cutt_location.='**'.$location_arr[$row[csf('location')]];
		}
		else
		{
			if($cutt_comapny=="") $cutt_comapny=$suplier[$row[csf('serving_company')]]; else $cutt_comapny.='**'.$suplier[$row[csf('serving_company')]];
		}
	}
	unset($cutData);

	$cutt_comapny = implode(",",array_filter(array_unique(explode("**",$cutt_comapny))));
	$cutt_location = implode(",",array_filter(array_unique(explode("**",$cutt_location))));



	if($db_type==0)
	{
		$embel_name_cond="group_concat(c.emb_name)";
	}
	else if ($db_type==2)
	{
		$embel_name_cond="LISTAGG(c.emb_name,',') WITHIN GROUP ( ORDER BY c.emb_name)";
	}

	$res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name, $embel_name_cond as emb_name
			from wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c
			where c.cons_dzn_gmts>0 and a.job_id=b.id and b.id=c.job_id and a.id=$po_id group by a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name");

 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";

		//echo "$('#cbo_embel_name').val('".$result[csf('emb_name')]."');\n";
		echo "load_drop_down( 'requires/print_embro_issue_controller', '".$result[csf('emb_name')]."', 'load_drop_down_embel_name', 'embel_name_td' );\n";
		//echo "get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(), 'populate_data_from_search_popup', 'requires/print_embro_issue_controller' );\n";
		//echo "$('#cbo_embel_type').val('".$result[csf('emb_type')]."');\n";

  		$dataArray=sql_select("SELECT SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalcutting,SUM(CASE WHEN production_type=2 and embel_name in(".$result[csf('emb_name')].") THEN production_quantity ELSE 0 END) as totalprinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0");
 		foreach($dataArray as $row)
		{
			echo "$('#txt_cutting_qty').val('".$row[csf('totalcutting')]."');\n";
			echo "$('#txt_cumul_issue_qty').attr('placeholder','".$row[csf('totalprinting')]."');\n";
			echo "$('#txt_cumul_issue_qty').val('".$row[csf('totalprinting')]."');\n";
			$yet_to_produced = $row[csf('totalcutting')]-$row[csf('totalprinting')];
			echo "$('#txt_yet_to_issue').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_to_issue').val('".$yet_to_produced."');\n";

			echo "$('#cutcompany').text('" . $cutt_comapny . "');\n";
			echo "$('#cutloaction').text('" . $cutt_location . "');\n";
		}
  	}
 	exit();
}

if($action=="color_and_size_level")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	$styleOrOrderWisw = $dataArr[3];
	$embelName = $dataArr[4];
	$country_id = $dataArr[5];
	$company  = $dataArr[6];
	$emb_type  = $dataArr[7];
	$country_ship_date=$dataArr[8];
	if( $country_ship_date=='') $country_ship_date_cond=''; else $country_ship_date_cond=" and c.country_ship_date='$country_ship_date'";
		if( $country_ship_date=='') $country_ship_date_cond2=''; else $country_ship_date_cond2=" and a.country_ship_date='$country_ship_date'";
    //print_r($data);

	if($embelName==3)// wash
	{
		echo "$('#sewing_production_variable').val(0);\n";
		$sql_result =sql_select("SELECT service_process_id  from variable_settings_production where company_name=$dataArr[6] and variable_list=1 and status_active=1");
		//echo $sql_result;die;
		foreach($sql_result as $result)
		{
			echo "$('#sewing_production_variable').val(".$result[csf("service_process_id")].");\n";
		}

		$variableSettings = $result[csf("service_process_id")];
	}

	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

	//#############################################################################################//
	//order wise - color level, color and size level

	//$variableSettings=2;

	$pr_type = ($embelName==3) ? 5 : 1;
	if($embelName==3) $tdcut_sew="Sewing Out Qty"; else $tdcut_sew="Cutt. Qty";

	$dataArray=sql_select("SELECT SUM(CASE WHEN production_type=$pr_type THEN production_quantity END) as totalcutting,SUM(CASE WHEN production_type=2 and embel_name='$embelName' and embel_type=$emb_type THEN production_quantity ELSE 0 END) as totalprinting from pro_garments_production_mst WHERE po_break_down_id=$po_id and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0");
	echo "$('#tdcut_sew').text('".$tdcut_sew."');\n";
 		foreach($dataArray as $row)
		{
 			echo "$('#txt_cutting_qty').val('".$row[csf('totalcutting')]."');\n";
			echo "$('#txt_cumul_issue_qty').attr('placeholder','".$row[csf('totalprinting')]."');\n";
			echo "$('#txt_cumul_issue_qty').val('".$row[csf('totalprinting')]."');\n";
			$yet_to_produced = $row[csf('totalcutting')]-$row[csf('totalprinting')];
			echo "$('#txt_yet_to_issue').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_to_issue').val('".$yet_to_produced."');\n";
		}

	if( $variableSettings==2 ) // color level
	{
		if($embelName==3)
		{
			if($db_type==0)
			{
				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=5 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and mst.embel_type=$emb_type and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by color_number_id";
			}
			else
			{
				$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type=5 then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=2 and c.embel_name='$embelName' and c.embel_type=$emb_type then b.production_qnty ELSE 0 END) as cur_production_qnty
						from wo_po_color_size_breakdown a
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						left join pro_garments_production_mst c on c.id=b.mst_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.item_number_id, a.color_number_id";

			}
		}
		else
		{
			if($db_type==0)
			{
				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and mst.embel_type=$emb_type and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 and mst.status_active=1 group by color_number_id";
			}
			else
			{
				$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=2 and c.embel_name='$embelName' and c.embel_type=$emb_type then b.production_qnty ELSE 0 END) as cur_production_qnty
						from wo_po_color_size_breakdown a
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						left join pro_garments_production_mst c on c.id=b.mst_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.item_number_id, a.color_number_id";

			}
		}


		$colorResult = sql_select($sql);
	}
	else if( $variableSettings==3 ) //color and size level
	{

		/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/
		if($embelName==3)
		{
			$dtlsData = sql_select("select a.color_size_break_down_id,
								sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
								sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' and b.embel_type=$emb_type then a.production_qnty ELSE 0 END) as cur_production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b
								where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(5,2) group by a.color_size_break_down_id");
		}
		else
		{
			$dtlsData = sql_select("select a.color_size_break_down_id,
								sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
								sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' and b.embel_type=$emb_type then a.production_qnty ELSE 0 END) as cur_production_qnty
								from pro_garments_production_dtls a,pro_garments_production_mst b
								where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2) group by a.color_size_break_down_id");
		}


		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
		}
		//print_r($color_size_qnty_array);

		$sql = "select a.color_order, a.id,a.size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
			from wo_po_color_size_breakdown a
			where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' $country_ship_date_cond2 and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order,a.size_order";
		$colorResult = sql_select($sql);
	}

	/*	else // by default color and size level
	{
		$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where  mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";// order by color_number_id,size_number_id
	}
	*/
	//$colorResult = sql_select($sql);
	//print_r($sql);

	$colorHTML="";
	$colorID='';
	$chkColor = array();
	$i=0;$totalQnty=0;
	foreach($colorResult as $color)
	{
		if( $variableSettings==2 ) // color level
		{
			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onkeyup="fn_colorlevel_total('.($i+1).')"> <input type="text" name="colorSizeBundleQnty" id="colorSizeBundleQnty_'.$color[csf("color_number_id")].($i+1).'" placeholder="Bundle Qty"  class="text_boxes_numeric" style="width:80px"></td></tr>';
			$totalQnty += $color[csf("production_qnty")]-$color[csf("cur_production_qnty")];
			$colorID .= $color[csf("color_number_id")].",";
		}
		else //color and size level
		{
			if( !in_array( $color[csf("color_number_id")], $chkColor ) )
			{
				if( $i!=0 ) $colorHTML .= "</table></div>";
				$i=0;
				$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
				$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"> <div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div> <table id="table_'.$color[csf("color_number_id")].'">';
				$chkColor[] = $color[csf("color_number_id")];
			}
			//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
			$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

			$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
			$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];



			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).')" ><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" value="'.$color[csf("order_quantity")].'"  disabled><input type="text" name="colorSizeBundleQnty" id="colorSizeBundleQnty_'.$color[csf("color_number_id")].($i+1).'" placeholder="Bundle Qty"  class="text_boxes_numeric" style="width:80px"></td></tr>';
		}
		$i++;
	}
	//echo $colorHTML;die;
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
	exit();
}

if($action=="body_part_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	extract($_REQUEST);

    ?>
    <script>
		function add_share_row( i, table_id, tr_id )
		{
			var prefix=tr_id.substr(0, tr_id.length-1);
			var row_num = $('#tbl_share_details_entry tbody tr').length;
			//alert(i+"**"+table_id+"**"+tr_id+"**"+row_num);
			row_num++;
			var clone= $("#"+tr_id+i).clone();
			clone.attr({
				id: tr_id + row_num,
			});

			clone.find("input,select").each(function(){

				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
					//'name': function(_, name) { var name=name.split("_"); return name[0] },
					'name': function(_, name) { return name },
					'value': function(_, value) { return value }
				});
			}).end();
			$("#"+tr_id+i).after(clone);

			//$('#decreaseset_'+row_num).removeAttr("disabled");
			$('#increaseset_'+row_num).removeAttr("onclick").attr("onclick","add_share_row("+row_num+",'"+table_id+"','"+tr_id+"');");
			$('#decreaseset_'+row_num).removeAttr("onclick").attr("onclick","fn_deletebreak_down_tr("+row_num+",'"+table_id+"','"+tr_id+"');");
			$('#txtorderquantity_'+row_num).val( '' );

			for(k=1;k<=$('#tbl_share_details_entry tbody tr').length;k++)
			{
				var j=k-1;
				$("#tbl_share_details_entry tbody tr:eq("+j+") td:eq(0)").text(k);
			}

			//$('#txtsize_'+row_num).val('');
			//$('#hiddenid_'+row_num).val('');
			set_all_onclick();
			//sum_total_qnty(row_num);
		}

		function fn_deletebreak_down_tr(rowNo,table_id,tr_id)
		{
			var numRow = $('#'+table_id+' tbody tr').length;
			var prefix=tr_id.substr(0, tr_id.length-1);
			var total_row=$('#'+prefix+'_tot_row').val();

			var numRow = $('table#tbl_share_details_entry tbody tr').length;
			if(numRow!=1)
			{
				/*var updateIdDtls=$('#hiddenid_'+rowNo).val();
				var txt_deleted_id=$('#txtDeletedId').val();
				var selected_id='';
				if(updateIdDtls!='')
				{
					if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
					$('#txtDeletedId').val( selected_id );
				}
				*/
				$("#"+tr_id+rowNo).remove();
				$('#'+prefix+'_tot_row').val(total_row-1);
				for(k=1;k<=$('#tbl_share_details_entry tbody tr').length;k++)
				{
					var j=k-1;
					$("#tbl_share_details_entry tbody tr:eq("+j+") td:eq(0)").text(k);
				}
				set_all_onclick();
				//sum_total_qnty(numRow);
				//calculate_total_amount(1);
			}
		}


		function fnc_close()
		{
			var check_field=0;
			var data_break_down="";
			$("#tbl_share_details_entry tbody tr").each(function()
			{
				var cboBodyPart 	= $(this).find('select[name="cboBodyPart[]"]').val();
				var txtorderquantity = $(this).find('input[name="txtorderquantity[]"]').val();
				//alert(cboSection);

				if( txtorderquantity ==''  || txtorderquantity ==0 )
				{
					alert('Please Fill up Qty ');
					check_field=1 ; return;
				}

				if(check_field==0)
				{
					if(data_break_down=="")
					{
						data_break_down+=cboBodyPart+'_'+txtorderquantity;
					}
					else
					{
						data_break_down+="="+cboBodyPart+'_'+txtorderquantity;
					}
				}
			});
			$('#hidden_break_tot_row').val( data_break_down );
			//alert(tot_row);//return;
			parent.emailwindow.hide();
		}

		function sum_total_qnty(id)
		{
			var ddd={ dec_type:5, comma:0, currency:''};
			var qty=0; var amt=0;
			var orderquantity=$('#txtorderquantity_'+id).val()*1;
			var orderrate=$('#txtorderrate_'+id).val()*1;
			var amount =orderquantity*orderrate;
			//alert(amount);
			$("#txtorderamount_"+id).val( number_format(amount,4,'.','' ) );

			$("#tbl_share_details_entry tbody tr").each(function()
			{
				var txtorderquantity = $(this).find('input[name="txtorderquantity[]"]').val()*1;
				var txtorderrate = $(this).find('input[name="txtorderrate[]"]').val()*1;
				var txtorderamount 	= $(this).find('input[name="txtorderamount[]"]').val()*1;
				qty+=txtorderquantity*1;
				amt+=txtorderamount*1;
				//alert(qty);
			});
			var rate=amt/qty;
			$("#txt_total_order_qnty").val( number_format(qty,4,'.','' ) );
			$("#txt_total_order_amount").val( number_format(amt,4,'.','' ) );
			$("#txt_average_rate").val( number_format(rate,4,'.','' ) );
		}

	</script>
</head>
<body>
	<div align="center" style="width:480px;" >
		<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
			<table class="rpt_table" width="480px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
					<th width="30">Sl.</th>
					<th width="240">Body Part</th>
					<th width="130">Quantity</th>
					<th>&nbsp;</th>
				</thead>
				<tbody>
					<input type="hidden" name="txtDeletedId" id="txtDeletedId" class="text_boxes_numeric" style="width:90px" readonly />
					<input type="hidden" name="hidden_break_tot_row" id="hidden_break_tot_row" class="text_boxes" style="width:90px" />
					<?
					//echo "<pre>".$hid_body_part."</pre>";
					if($hid_body_part!=''){
						$data_array=explode("=",$hid_body_part);
						$is_available_datas=count($data_array);
					}
					else
					{
						$is_available_datas=0;
					}
					//echo $within_group;
					$k=0;
					//echo count($data_array);
					//if($within_group==1) $disabled="disabled"; else $disabled="";

					if($po_break_down_id!='')
					{
						$body_part_sql ="SELECT c.id,c.body_part_id from wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c where c.cons_dzn_gmts>0 and a.job_id=b.id and b.id=c.job_id and a.id=$po_break_down_id group by c.id,c.body_part_id";
						$res = sql_select($body_part_sql);
						foreach($res as $row)
						{
							$body_part_ids.=$row[csf("body_part_id")].',';
						}
						$body_part_ids=chop($body_part_ids,',');
						$body_part_ids=implode(", ",array_unique(explode(",",$body_part_ids)));
					}
					else{
						$body_part_ids=0;
					}


					if($is_available_datas>0)
					{
						foreach($data_array as $row)
						{
							$data=explode('_',$row);
							$k++;
							//if(($data[7]=='' || $data[7]==0 ) && $within_group==1) $styleRef=$txtstyleRef; else $styleRef=$data[10];
							?>
							<tr id="row_<? echo $k;?>">
								<td ><? echo $k;?></td>


								<td><? echo create_drop_down( "cboBodyPart_".$k, 240, $body_part,"", 1, "-- Select --","$data[0]","",0,"$body_part_ids",'','','','','',"cboBodyPart[]"); ?></td>
								<td>
									<input type="text" id="txtorderquantity_<? echo $k;?>" name="txtorderquantity[]" class="text_boxes_numeric" style="width:117px" onKeyUp="sum_total_qnty(<? echo $k;?>);" value="<? echo number_format($data[1],4,'.',''); ?>" <? echo $disabled; ?> />
									<input type="hidden" id="hiddenOrderQuantity_<? echo $k;?>" name="hiddenOrderQuantity[]" class="text_boxes_numeric" style="width:70px" value="<? echo $data[1]; ?>"  />
								</td>
								<td>
									<input type="button" id="increaseset_<? echo $k;?>" name="increaseset[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_share_row(<? echo $k;?>,'tbl_share_details_entry','row_')"  <? echo $disabled; ?>  />
									<input type="button" id="decreaseset_<? echo $k;?>" name="decreaseset[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(<? echo $k;?>,'tbl_share_details_entry','row_');"  <? echo $disabled; ?>  />
								</td>
							</tr>
							<?
						}
					}
					else
					{
						?>
                        <tr id="row_1">
                        	<td>1</td>
								<td><? echo create_drop_down( "cboBodyPart_1", 240, $body_part,"", 1, "-- Select --","$data[0]","",0,"$body_part_ids",'','','','','',"cboBodyPart[]"); ?></td>
								<td>
									<input type="text" id="txtorderquantity_1" name="txtorderquantity[]" class="text_boxes_numeric" style="width:117px" value="" />
									<input type="hidden" id="hiddenOrderQuantity_1" name="hiddenOrderQuantity[]" class="text_boxes_numeric" style="width:70px" value=""  />
								</td>
							<td>
								<input type="button" id="increaseset_1" name="increaseset[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_share_row(1,'tbl_share_details_entry','row_')"  <? echo $disabled; ?>  />
									<input type="button" id="decreaseset_1" name="decreaseset[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deletebreak_down_tr(1,'tbl_share_details_entry','row_');"  <? echo $disabled; ?>  />
							</td>
                        </tr>
						<?
					}
					?>
				</tbody>
			</table>
			<table>
				<tr>
					<td align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
				</tr>
			</table>
		</form>
	</div>
</body>
<script>sum_total_qnty(0);</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="show_dtls_listview")
{
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$color_name=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[3];
	$type = $dataArr[4];
	if($type==1) $embel_name="%%"; else $embel_name = $dataArr[2];
	ob_start();
 ?>
    <div style="width:980px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table">
            <thead>
	            <tr>
	                <th width="30"><p>SL</p></th>
	                <th width="150" align="center"><p>Item Name</p></th>
	                <th width="120" align="center"><p>Country</p></th>
	                <th width="80" align="center"><p>Production Date</p></th>
	                <th width="80" align="center"><p>Production Qnty</p></th>
					<th width="100" align="center"><p>Embel.Name</p></th>
	                <th width="150" align="center"><p>Serving Company</p></th>
	                <th width="120" align="center"><p>Location</p></th>
					<th width="100" align="center"><p>Color</p></th>
	                <th width="80" align="center"><p>Color Type</p></th>
	                <th width="60" align="center"><p>Issue ID</p></th>
					<th width="100" align="center"><p>Work Order No</p></th>
	                <th width="70" align="center"><p>Challan No</p></th>
	             </tr>
            </thead>
        </table>
    </div>
	<div style="width:1000px;max-height:280px; overflow-y:auto;" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="980px" class="rpt_table" id="tbl_search">
		<?php
			$i=1;
			$total_production_qnty=0;
			$sqlResult =sql_select("SELECT id,po_break_down_id,item_number_id,company_id,country_id,production_date,production_quantity,reject_qnty,production_source,serving_company,location,challan_no,embel_name,embel_type,WO_ORDER_ID from pro_garments_production_mst where po_break_down_id='$po_id'  and production_type='2' and status_active=1 and is_deleted=0 order by id"); //and embel_name like '$embel_name' and item_number_id='$item_id' and country_id='$country_id' change in 29/10/2019 for libas
			$sql_color_type=sql_select("SELECT a.id,b.color_type_id from pro_garments_production_mst a , pro_garments_production_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.production_type=2  and a.production_type=2 and a.po_break_down_id='$po_id' group by a.id,b.color_type_id");

			$wo_order_id_array=array();
			foreach ($sqlResult as $r) 
			{
				$wo_order_id_array[$r['WO_ORDER_ID']]=$r['WO_ORDER_ID'];
			}
			$wo_order_id_cond = implode(",",$wo_order_id_array);
			$wo_order_sql= sql_select("SELECT ID as WO_ID , BOOKING_NO from WO_BOOKING_MST where  STATUS_ACTIVE=1 and IS_DELETED=0 and id in ($wo_order_id_cond) ");
		   // echo $wo_order_sql;die;
		   $wo_order_no_arry=array();
		   foreach ($wo_order_sql as $v) 
			{
				$wo_order_no_arry[$v['WO_ID']]=$v['BOOKING_NO'];
			}
			// echo "<pre>";print_r($wo_order_id_array);die;
			$order_cond = array();
			foreach($sqlResult as $result)
			{
				
				$order_cond[$result['PO_BREAK_DOWN_ID']]  = $result['PO_BREAK_DOWN_ID'];
			}
		
			
			$order_cond_id = implode(",",$order_cond);
			$newSql = "SELECT b.PRODUCTION_QNTY, b.mst_id, a.PO_BREAK_DOWN_ID , a.ITEM_NUMBER_ID , a.COUNTRY_ID , a.COLOR_NUMBER_ID FROM WO_PO_COLOR_SIZE_BREAKDOWN a,pro_garments_production_dtls b WHERE a.PO_BREAK_DOWN_ID in ($order_cond_id) and b.COLOR_SIZE_BREAK_DOWN_ID = a.id AND b.status_active=1 AND b.is_deleted=0";
			//echo $newSql;die;
			$mysqli_ex_query = sql_select($newSql);

			$Color_Item_arr = array();
			foreach($mysqli_ex_query as $data)
			{
				if($data['PRODUCTION_QNTY']>0) 
				{
			      $Color_Item_arr[$data['MST_ID']][$data['COLOR_NUMBER_ID']] = $color_name[$data['COLOR_NUMBER_ID']]; 
				}
			}
			// echo "<pre>";print_r($Color_Item_arr);die;
	 		foreach($sql_color_type as $key=>$value)
	 		{
	 			$color_type_arrs[$value[csf("id")]]=$value[csf("color_type_id")];
	 		}
			foreach($sqlResult as $selectResult)
			{

				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
               	$total_production_qnty+=$selectResult[csf('production_quantity')];
 				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" >
                    <td width="30" align="center"> &nbsp;
                    <input type="checkbox" id="tbl_<? echo $i; ?>"  onClick="fnc_checkbox_check(<? echo $i; ?>);"  />&nbsp;
                   <input type="hidden" id="mstidall_<? echo $i; ?>" value="<? echo $selectResult[csf('id')]; ?>" style="width:30px"/>
                   <input type="hidden" id="emblname_<? echo $i; ?>" name="emblname[]"   value="<? echo $selectResult[csf('embel_name')]; ?>" />
                   <input type="hidden" id="embltype_<? echo $i; ?>" name="embltype[]"   value="<? echo $selectResult[csf('embel_type')]; ?>" />
                    <input type="hidden" id="productionsource_<? echo $i; ?>"   value="<? echo $selectResult[csf('production_source')]; ?>" />

                    <input type="hidden" id="serving_company_<? echo $i; ?>"   value="<? echo $selectResult[csf('serving_company')]; ?>" />
                    <input type="hidden" id="location_<? echo $i; ?>"   value="<? echo $selectResult[csf('location')]; ?>" />

                    </td>
                    <td width="150" align="center" onClick="fnc_load_from_dtls('<? echo $selectResult[csf('id')].'**'.$selectResult[csf('embel_name')]; ?>');"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="120" align="center" onClick="fnc_load_from_dtls('<? echo $selectResult[csf('id')].'**'.$selectResult[csf('embel_name')]; ?>');"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<? echo $selectResult[csf('id')].'**'.$selectResult[csf('embel_name')]; ?>');"><p><?php echo change_date_format($selectResult[csf('production_date')]); ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<? echo $selectResult[csf('id')].'**'.$selectResult[csf('embel_name')]; ?>');"><p><?php  echo $selectResult[csf('production_quantity')]; ?></p></td>
					<td width="100" align="center" onClick="fnc_load_from_dtls('<? echo $selectResult[csf('id')].'**'.$selectResult[csf('embel_name')]; ?>');"><p><?php  echo $emblishment_name_array[$selectResult[csf('embel_name')]]; ?></p></td>

                    <?php
                            $source= $selectResult[csf('production_source')];
                            if($source==3) $serving_company= $supplier_arr[$selectResult[csf('serving_company')]];
                            else $serving_company= $company_arr[$selectResult[csf('serving_company')]];
                     ?>
                    <td width="150" align="center" onClick="fnc_load_from_dtls('<? echo $selectResult[csf('id')].'**'.$selectResult[csf('embel_name')]; ?>');"><p><?php echo $serving_company; ?></p></td>
                    <td width="120" align="center" onClick="fnc_load_from_dtls('<? echo $selectResult[csf('id')].'**'.$selectResult[csf('embel_name')]; ?>');"><p><? echo $location_arr[$selectResult[csf('location')]]; ?></p></td>

					<td width="100" align="center" onClick="fnc_load_from_dtls('<? echo $selectResult[csf('id')].'**'.$selectResult[csf('embel_name')]; ?>');"><p><? echo implode(',',$Color_Item_arr[$selectResult['ID']]); ?></p></td>
                  
					<td width="80" align="center" onClick="fnc_load_from_dtls('<? echo $selectResult[csf('id')].'**'.$selectResult[csf('embel_name')]; ?>');"><p><? echo $color_type[$color_type_arrs[$selectResult[csf("id")]]]; ?></p></td>
                    <td width="60" align="center" onClick="fnc_load_from_dtls('<? echo $selectResult[csf('id')].'**'.$selectResult[csf('embel_name')]; ?>');"><p><? echo $selectResult[csf('id')]; ?></p></td>
					<td width="100" align="center" onClick="fnc_load_from_dtls('<? echo $selectResult[csf('id')].'**'.$selectResult[csf('embel_name')]; ?>');"><p><? echo $wo_order_no_arry[$selectResult['WO_ORDER_ID']]; ?></p></td>
                    <td  width="70"  align="center" onClick="fnc_load_from_dtls('<? echo $selectResult[csf('id')].'**'.$selectResult[csf('embel_name')]; ?>');"><p><?php echo $selectResult[csf('challan_no')]; ?>&nbsp;</p></td>
                </tr>
            <?php
                $i++;
			}
			?>

		</table>
         <script>
			setFilterGrid("tbl_search",-1);
		</script>
        </div>
	<?
	exit();
}

if($action=="show_country_listview")
{
?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="390" class="rpt_table">
        <thead>
           <th width="20">SL</th>
            <th width="100">Item Name</th>
            <th width="80">Country</th>
            <th width="60">Shipment Date</th>
            <th width="70">Plan Cut Qty.</th>
            <th>Issue Qty.</th>
        </thead>
		<?
		$i=1;

		$issue_qnty_arr=sql_select("select a.po_break_down_id, a.item_number_id, a.country_id, b.production_qnty as cutting_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$data' and a.production_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$issue_data_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]+=$row[csf("cutting_qnty")];
		}

		// $sqlResult =sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");

		$sqlResult = sql_select("SELECT po_break_down_id, item_number_id, country_id, country_ship_date,pack_type, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty, max(cutup) as cutup from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active in(1,2,3) and is_deleted=0 group by po_break_down_id, item_number_id, country_id,country_ship_date,pack_type order by country_ship_date");
		foreach($sqlResult as $row)
		{


			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$cutting_qnty=0;
			$issue_qnty=$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')];?>, '<?=$row[csf('country_ship_date')]; ?>' )">
				<td width="20" align="center"><? echo $i; ?></td>
				<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="60" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right" width="70"><?  echo $row[csf('order_qnty')]; ?></td>
                <td align="right"><?  echo $issue_qnty; ?></td>
			</tr>
		<?
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="populate_issue_form_data")
{
	$data=explode("**",$data);
	//production type=2 come from array
	$sqlResult =sql_select("SELECT country_ship_date, id,garments_nature,company_id,challan_no,man_cutt_no,po_break_down_id,item_number_id,country_id,production_source,serving_company,location,embel_name,embel_type,production_date,production_quantity,production_source,production_type,entry_break_down_type,production_hour,sewing_line,supervisor,carton_qty,remarks,floor_id,alter_qnty,reject_qnty,total_produced,yet_to_produced,sending_location,sending_company,body_part_info, location_lc, floor_lc,wo_order_id  from pro_garments_production_mst where id='". $data[0] ."' and production_type='2' and status_active=1 and is_deleted=0 order by id");
	$wo_order_id_array=array();
	foreach ($sqlResult as $r) 
	{
		$wo_order_id_array[$r['WO_ORDER_ID']]=$r['WO_ORDER_ID'];
	}
	$wo_order_id_cond = implode(",",$wo_order_id_array);
	$wo_order_sql= sql_select("SELECT ID as WO_ID , BOOKING_NO from WO_BOOKING_MST where  STATUS_ACTIVE=1 and IS_DELETED=0 and id in ($wo_order_id_cond) ");
	// echo $wo_order_sql;die;
	$wo_order_no_arry=array();
	foreach ($wo_order_sql as $v) 
	{
		$wo_order_no_arry[$v['WO_ID']]=$v['BOOKING_NO'];
	}
	// echo "<pre>";print_r($wo_order_no_arry);die;
	$country_ship_date = $sqlResult[0][csf('country_ship_date')];
	if($country_ship_date=='') $country_ship_date_cond=""; else $country_ship_date_cond="and a.country_ship_date='$country_ship_date'";
	$wo_order_id_cond= 

	$color_type_val=sql_select("SELECT b.color_type_id,b.remarks_dtls  from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=2 and b.production_type=2 and a.status_active=1 and b.status_active=1 and  a.id='$data[0]' group by b.color_type_id,b.remarks_dtls  ");

	if($data[1]==3) $tdcut_sew="Sewing Out Qty"; else $tdcut_sew="Cutt. Qty";
  	//echo "sdfds".$sqlResult;die;
	foreach($sqlResult as $result)
	{
		echo "$('#txt_issue_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/print_embro_issue_controller', ".$result[csf('production_source')].", 'load_drop_down_embro_issue_source', 'emb_company_td' );\n";
		echo "$('#cbo_emb_company').val('".$result[csf('serving_company')]."');\n";
		echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";
		echo "$('#txt_manual_cut_no').val('".$result[csf('man_cutt_no')]."');\n";
		echo "$('#hid_body_part').val('".$result[csf('body_part_info')]."');\n";
		echo "$('#txt_body_part').val('".$result[csf('body_part_info')]."');\n";
		echo "load_drop_down( 'requires/print_embro_issue_controller', ".$result[csf('po_break_down_id')].", 'load_drop_down_color_type', 'color_type_td' );\n";

		echo "load_drop_down( 'requires/print_embro_issue_controller', '".$result[csf('location_lc')]."', 'load_drop_down_lc_floor', 'floor_lc_td' );\n";
		echo "$('#cbo_color_type').val('".$color_type_val[0][csf("color_type_id")]."');\n";
		echo "$('#cbo_location_lc').val('" . $result[csf('location_lc')] . "');\n";
		echo "$('#cbo_floor_lc').val('" . $result[csf('floor_lc')] . "');\n";
		$location_company=0;
		if($result[csf('production_source')]==1)
		{
			$location_company=$result[csf('serving_company')];
		}
		else
		{
			$location_company=$result[csf('company_id')];
		}
	    echo "load_drop_down( 'requires/print_embro_issue_controller', ".$location_company.", 'load_drop_down_location', 'location_td' );\n";
		echo "load_drop_down( 'requires/print_embro_issue_controller', '".$result[csf('location')]."', 'load_drop_down_floor', 'floor_td' );\n";
		echo "document.getElementById('cbo_location').value  = '".($result[csf("location")])."';\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";
		echo "load_drop_down( 'requires/print_embro_issue_controller', '".$result[csf('company_id')]."_".$result[csf('serving_company')]."_".$result[csf('po_break_down_id')]."_".$result[csf('embel_name')]."_".$result[csf('item_number_id')]."', 'load_drop_down_workorder', 'workorder_td' );\n";
		echo "$('#cbo_work_order').val('".$wo_order_no_arry[$result[csf('wo_order_id')]]."');\n";
		echo "$('#cbo_embel_name').val('".$result[csf('embel_name')]."');\n";
		echo "$('#cbo_sending_location').val('".$result[csf('sending_location')]."*".$result[csf('sending_company')]."');\n";
		echo "load_drop_down( 'requires/print_embro_issue_controller', '".$result[csf('embel_name')].'**'.$result[csf('po_break_down_id')]."', 'load_drop_down_embro_issue_type', 'embro_type_td' );\n";
		//$result[csf('po_break_down_id')]
		echo "$('#cbo_embel_type').val('".$result[csf('embel_type')]."');\n";

  		echo "$('#txt_issue_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
		echo "$('#txt_iss_id').val('".$result[csf('id')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
  		echo "$('#txt_remark_dtls').val('".$color_type_val[0][csf("remarks_dtls")]."');\n";
		echo "$('#tdcut_sew').text('".$tdcut_sew."');\n";

			$dataArray=sql_select("select SUM(CASE WHEN production_type=1 THEN production_quantity END) as totalCutting,SUM(CASE WHEN production_type=2 and embel_name=".$result[csf('embel_name')]." THEN production_quantity ELSE 0 END) as totalPrinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and is_deleted=0");
 		foreach($dataArray as $row)
		{
 			echo "$('#txt_cutting_qty').val('".$row[csf('totalCutting')]."');\n";
			echo "$('#txt_cumul_issue_qty').val('".$row[csf('totalPrinting')]."');\n";
			$yet_to_produced = $row[csf('totalCutting')]-$row[csf('totalPrinting')];
			echo "$('#txt_yet_to_issue').val('".$yet_to_produced."');\n";
		}

		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);\n";

		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		$embel_type = $result[csf('embel_type')];
		if( $variableSettings!=1 ) // gross level
		{
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];

			$sql_dtls = sql_select("SELECT color_size_break_down_id,production_qnty,size_number_id, color_number_id, bundle_qty from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id='". $data[0] ."' and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
			foreach($sql_dtls as $row)
			{
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
			  	$bundleArr[$index] = $row[csf('bundle_qty')];
			}

			//$variableSettings=2;



			if( $variableSettings==2 ) // color level
			{
				if($data[1]==3)
				{
					if($db_type==0)
					{

					$sql="SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type=5 then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=2 and c.embel_name='". $data[1] ."' then b.production_qnty ELSE 0 END) as cur_production_qnty
						from wo_po_color_size_breakdown a
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						left join pro_garments_production_mst c on c.id=b.mst_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";
					}
					else
					{
						$sql="select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type=5 then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=2 and c.embel_name='". $data[1] ."' and c.embel_type = $embel_type then b.production_qnty ELSE 0 END) as cur_production_qnty
						from wo_po_color_size_breakdown a
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						left join pro_garments_production_mst c on c.id=b.mst_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

					}
				}
				else
				{
					if($db_type==0)
					{

					$sql="select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=2 and c.embel_name='". $data[1] ."' and c.embel_type = $embel_type then b.production_qnty ELSE 0 END) as cur_production_qnty
						from wo_po_color_size_breakdown a
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						left join pro_garments_production_mst c on c.id=b.mst_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";
					}
					else
					{
						$sql="select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type=1 then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=2 and c.embel_name='". $data[1] ."' and c.embel_type = $embel_type then b.production_qnty ELSE 0 END) as cur_production_qnty
						from wo_po_color_size_breakdown a
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						left join pro_garments_production_mst c on c.id=b.mst_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

					}
				}

			}
			else if( $variableSettings==3 ) //color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=1 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=2 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/

				if($data[1]==3)
				{
					$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='". $data[1] ."' and b.embel_type = $embel_type then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(5,2) group by a.color_size_break_down_id");
				}
				else
				{
					$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='". $data[1] ."' and b.embel_type = $embel_type then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2) group by a.color_size_break_down_id");
				}


				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['bundle_qty']= $row[csf('bundle_qty')];
				}
				//print_r($color_size_qnty_array);

				$sql = "SELECT a.color_order, a.id, a.size_order,a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
					from wo_po_color_size_breakdown a
					where a.po_break_down_id='$po_id' $country_ship_date_cond and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order,a.size_order";

			}
			else // by default color and size level
			{
				if($data[1]==3)
				{
					$dtlsData = sql_select("select a.color_size_break_down_id,
									sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
									sum(CASE WHEN a.production_type=2 and b.embel_name='". $data[1] ."' and b.embel_type = $embel_type then a.production_qnty ELSE 0 END) as cur_production_qnty
									from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(5,2) group by a.color_size_break_down_id");
				}
				else
				{
					$dtlsData = sql_select("select a.color_size_break_down_id,
									sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
									sum(CASE WHEN a.production_type=2 and b.embel_name='". $data[1] ."' and b.embel_type = $embel_type then a.production_qnty ELSE 0 END) as cur_production_qnty
									from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(1,2) group by a.color_size_break_down_id");
				}


				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				}
				//print_r($color_size_qnty_array);

				$sql = "SELECT a.color_order, a.id,a.size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
					from wo_po_color_size_breakdown a
					where a.po_break_down_id='$po_id' $country_ship_date_cond and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order,a.size_order";
			}

 			$colorResult = sql_select($sql);
 			//print_r($sql);die;
			$colorHTML="";
			$colorID='';
			$chkColor = array();
			$i=0;$totalQnty=0;$colorWiseTotal=0;
			foreach($colorResult as $color)
			{

				if( $variableSettings==2 ) // color level
				{
			     	$bundle_qnty=$bundleArr[$color[csf("color_number_id")]];

					$amount = $amountArr[$color[csf("color_number_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onkeyup="fn_colorlevel_total('.($i+1).')"><input type="text" name="colorSizeBundleQnty" id="colorSizeBundleQnty_'.$color[csf("color_number_id")].($i+1).'" placeholder="Bundle Qty" value="'.$bundle_qnty.'"  class="text_boxes_numeric" style="width:80px"></td></tr>';
					$totalQnty += $amount;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{
					$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$amount = $amountArr[$index];
					$bundle_qnty = $bundleArr[$index];
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;$colorWiseTotal=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
					// $bundle_qnty=$color_size_qnty_array[$color[csf('id')]]['bundle_qty'];


					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).');fn_chk_next_process_qty('.$color[csf("color_number_id")].','.($i+1).','.$color[csf("size_number_id")].')" onkeyup="" value="'.$amount.'" ><input type="hidden" name="colorSizeUpQty" id="colSizeUpQty_'.$color[csf("color_number_id")].($i+1).'" value="'.$amount.'" ><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" value="'.$color[csf("order_quantity")].'" disabled > <input type="text" name="colorSizeBundleQnty" id="colorSizeBundleQnty_'.$color[csf("color_number_id")].($i+1).'" placeholder="Bundle Qty" value="'.$bundle_qnty.'"  class="text_boxes_numeric" style="width:80px"> </td></tr>';
					$colorWiseTotal += $amount;
				}
				$i++;
			}
			//echo $colorHTML;die;
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		}//end if condtion
		//#############################################################################################//
	}
 	exit();
}

if($action=="chk_next_process_qty")
{
	extract($_REQUEST);
	// $col_size_id = explode("*", str_replace("'", "", $hidden_colorSizeID));
	$sql = "SELECT sum(case when a.production_type=2 then b.production_qnty else 0 end) as issue_qty,sum(case when a.production_type=3 then b.production_qnty else 0 end) as receive_qty from pro_garments_production_mst a,pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_number_id=$cbo_item_name and a.country_id=$cbo_country_name and c.color_number_id=$colorId and c.size_number_id=$sizeId and c.status_active=1 and c.is_deleted=0 and a.po_break_down_id=$hidden_po_break_down_id and a.production_type in(2,3) and c.id=b.color_size_break_down_id";
	// echo $sql;
	$sql_res = sql_select($sql);
	echo $sql_res[0]['RECEIVE_QTY']."****".$sql_res[0]['ISSUE_QTY'];
	die();
}
//pro_garments_production_mst
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$cbo_company_name");
	if($is_projected_po_allow ==2)
	{
		$is_projected_po=return_field_value("is_confirmed","wo_po_break_down","status_active in(1,2,3) and id=$hidden_po_break_down_id");
		if($is_projected_po==2)
		{
			echo "786**Projected PO is not allowed to production. Please check variable settings";die();
		}
	}

	$is_control=return_field_value("is_control","variable_settings_production","company_name=$cbo_company_name and variable_list=33 and page_category_id=28","is_control");



	/* ======================================================================== /
	/							check variable setting							/
	========================================================================= */
	$wip_valuation_for_accounts = return_field_value("allow_fin_fab_rcv", "variable_settings_production", "company_name=$cbo_company_name and variable_list=76 and status_active=1 and is_deleted=0");
	if($wip_valuation_for_accounts==1)
	{
		/* ================================= get fabric cost =================================== */

		// $sql = "SELECT c.po_break_down_id as po_id,c.item_number_id,c.color_number_id,b.production_type,a.embel_name,b.cost_of_fab_per_pcs,b.cut_oh_per_pcs,b.cost_per_pcs,b.trims_cost_per_pcs from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and b.production_type=5 and c.po_break_down_id=$hidden_po_break_down_id and c.item_number_id=$cbo_item_name order by a.production_type asc";

		$sql = "SELECT c.po_break_down_id as po_id,c.item_number_id,c.color_number_id,b.cost_of_fab_per_pcs,b.cut_oh_per_pcs,b.cost_per_pcs,b.bundle_no,b.production_qnty,(b.cost_per_pcs*b.production_qnty) as amount from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and b.production_type=5  and c.po_break_down_id=$hidden_po_break_down_id and c.item_number_id=$cbo_item_name";
		// echo "10**".$sql;die;
		$res = sql_select($sql);
		$fab_cost_array = array();
		foreach ($res as $v)
		{
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['trims_cost_per_pcs'] = $v['TRIMS_COST_PER_PCS'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['prod_qty'] += $v['PRODUCTION_QNTY'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['amount'] += $v['AMOUNT'];

		}


		/* $trims_cost = number_format($trims_cost,$dec_place[3],'.','');
		$finish_oh = $finishing_qty*$cpm*$item_smv;
		$sewing_oh = number_format($sewing_oh,$dec_place[3],'.','');
		$cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.',''); */
		/* ================================== end fabric cost ========================================= */
	}
	// echo "10**";print_r($fab_cost_array);die;
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}

		$cbo_sending_location = explode("*",str_replace("'","",$cbo_sending_location));
		$sending_location = $cbo_sending_location[0];
		$sending_company = $cbo_sending_location[1];

		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );

		//production_type array
		$field_array1="id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id, total_produced, yet_to_produced, inserted_by, insert_date,sending_location,sending_company,man_cutt_no,body_part_info,location_lc,floor_lc,wo_order_id,cost_of_fab_per_pcs,cut_oh_per_pcs,trims_cost_per_pcs,cost_per_pcs,country_ship_date";
		$data_array1="(".$id.",".$cbo_company_name.",".$garments_nature.",".$txt_challan.",".$hidden_po_break_down_id.", ".$cbo_item_name.",".$cbo_country_name.", ".$cbo_source.",".$cbo_emb_company.",".$cbo_location.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_issue_date.",".$txt_issue_qty.",2,".$sewing_production_variable.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_issue_qty.",".$txt_yet_to_issue.",".$user_id.",'".$pc_date_time."','".$sending_location."','".$sending_company."',".$txt_manual_cut_no.",".$hid_body_part.",".$cbo_location_lc.",".$cbo_floor_lc.",".$cbo_work_order.",'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost."','".$cost_per_pcs."',".$country_ship_date.")";

		// echo "10**INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;

 		//$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);

		//echo $data_array."---".$rID;die;

  		// pro_garments_production_dtls table entry here ----------------------------------///


		$embelName=str_replace("'","",$cbo_embel_name);

		if($embelName==3)
		{
			$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(5,2)
										group by a.color_size_break_down_id");
		}
		else
		{
			$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(1,2)
										group by a.color_size_break_down_id");
		}

		$color_pord_data=array();
		foreach($dtlsData as $row)
		{
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}

		$field_array="id, mst_id,production_type,color_size_break_down_id,production_qnty,bundle_qty,color_type_id,remarks_dtls,cost_of_fab_per_pcs,cut_oh_per_pcs,trims_cost_per_pcs,cost_per_pcs";
  		$dtlsrID=true;
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name   and status_active=1 and is_deleted=0  order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}

			$rowExBundle = array_filter(explode("**",$colorBundleVal));
			foreach($rowExBundle as $rowR=>$valR)
			{
				$colorSizeBunIDArr = explode("*",$valR);
				//echo $colorSizeBunIDArr[0]; die;
				$BunQtyArr[$colorSizeBunIDArr[0]]=$colorSizeBunIDArr[1];
			}


			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
 			$rowEx = array_filter(explode("**",$colorIDvalue));
 			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				/*if($is_control==1 && $user_level!=2)
				{
					if($colorSizeNumberIDArr[1]>0)
					{
						if(($colorSizeNumberIDArr[1]*1)>($color_pord_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]*1))
						{
							echo "35**Embellishment Quantity Not Over Cutting Qnty";
							//check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}*/

				if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
				{
					$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_of_fab_per_pcs'];
					$sewing_oh = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cut_oh_per_pcs'];
					$trims_cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['trims_cost_per_pcs'];
					// $cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_per_pcs'];

					$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['prod_qty'];
					$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['amount'];

					$cost_per_pcs = $amount/$prod_qty;
					$cost_per_pcs = fn_number_format($cost_per_pcs,$dec_place[3],'.','');


					//2 for Issue to Print / Emb Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$BunQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.",".$txt_remark_dtls.",'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost_per_pcs."','".$cost_per_pcs."')";
					else $data_array .= ",(".$dtls_id.",".$id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$BunQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.",".$txt_remark_dtls.",'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost_per_pcs."','".$cost_per_pcs."')";
					//$dtls_id=$dtls_id+1;
	 				$j++;
	 			}
	 			else
	 			{
	 				echo "420**";die();
	 			}
			}
 		}

		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{
			$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0  order by size_number_id,color_number_id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf('id')];
			}

			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//

			$rowExBundle = array_filter(explode("***",$colorBundleVal));
			foreach($rowExBundle as $rowR=>$valR)
			{
				$colorAndSizeBun_arr = explode("*",$valR);
				$sizeID = $colorAndSizeBun_arr[0];
				$colorID = $colorAndSizeBun_arr[1];
				$colorSizeBun = $colorAndSizeBun_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;
				$BunQtyArr[$index]=$colorSizeBun;
			}

 			$rowEx = array_filter(explode("***",$colorIDvalue));
			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$colorID;

				/*if($is_control==1 && $user_level!=2)
				{
					if($colorSizeValue>0)
					{
						if(($colorSizeValue*1)>($color_pord_data[$colSizeID_arr[$index]]*1))
						{
							echo "35**Embellishment Quantity Not Over Cutting Qnty";
							//check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}*/
				$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_of_fab_per_pcs'];
				$sewing_oh = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cut_oh_per_pcs'];
				$trims_cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['trims_cost_per_pcs'];
				// $cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_per_pcs'];

				$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['prod_qty'];
				$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['amount'];

				$cost_per_pcs = $amount/$prod_qty;
				$cost_per_pcs = fn_number_format($cost_per_pcs,$dec_place[3],'.','');

				if($colSizeID_arr[$index]!="")
				{
					//2 for Issue to Print / Emb Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$BunQtyArr[$index]."',".$cbo_color_type.",".$txt_remark_dtls.",'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost_per_pcs."','".$cost_per_pcs."')";
					else $data_array .= ",(".$dtls_id.",".$id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$BunQtyArr[$index]."',".$cbo_color_type.",".$txt_remark_dtls.",'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost_per_pcs."','".$cost_per_pcs."')";
					//$dtls_id=$dtls_id+1;
	 				$j++;
	 			}
	 			else
	 			{
	 				echo "420**";die();
	 			}
			}
		}

		$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);

		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
			//echo "10**INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array;die;
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}

		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					mysql_query("COMMIT");
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		if($db_type==1 || $db_type==2 )
		{

			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID )
				{
					oci_commit($con);
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					oci_commit($con);
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}

		$cbo_sending_location = explode("*",str_replace("'","",$cbo_sending_location));
		$sending_location = $cbo_sending_location[0];
		$sending_company = $cbo_sending_location[1];

		// pro_garments_production_mst table data entry here
 		$field_array1="production_source*serving_company*location*embel_name*embel_type*production_date*production_quantity*production_type*entry_break_down_type*challan_no*remarks*floor_id*total_produced*yet_to_produced*updated_by*update_date*sending_location*sending_company*man_cutt_no*body_part_info*location_lc*floor_lc*wo_order_id*cost_of_fab_per_pcs*cut_oh_per_pcs*trims_cost_per_pcs*cost_per_pcs";


		$data_array1="".$cbo_source."*".$cbo_emb_company."*".$cbo_location."*".$cbo_embel_name."*".$cbo_embel_type."*".$txt_issue_date."*".$txt_issue_qty."*2*".$sewing_production_variable."*".$txt_challan."*".$txt_remark."*".$cbo_floor."*".$txt_cumul_issue_qty."*".$txt_yet_to_issue."*".$user_id."*'".$pc_date_time."'*'".$sending_location."'*'".$sending_company."'*".$txt_manual_cut_no."*".$hid_body_part."*".$cbo_location_lc."*".$cbo_floor_lc."*".$cbo_work_order."*'".$cost_of_fab_per_pcs."'*'".$sewing_oh."'*'".$trims_cost."'*'".$cost_per_pcs."'";
 		//$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		//echo $data_array1;die;

		//echo "10**INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;

		// pro_garments_production_dtls table data entry here
		$embelName=str_replace("'","",$cbo_embel_name);
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' )// check is not gross level
		{
			if($embelName==3)
			{
				$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(5,2) and b.id<>$txt_mst_id
										group by a.color_size_break_down_id");
			}
			else
			{
				$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=1 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=2 and b.embel_name='$embelName' then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(1,2) and b.id<>$txt_mst_id
										group by a.color_size_break_down_id");
			}

			$color_pord_data=array();
			foreach($dtlsData as $row)
			{
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}


 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty,bundle_qty,remarks_dtls,color_type_id,cost_of_fab_per_pcs,cut_oh_per_pcs,trims_cost_per_pcs,cost_per_pcs";

			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name   and status_active=1 and is_deleted=0  order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}

				$rowExBundle = array_filter(explode("**",$colorBundleVal));
				foreach($rowExBundle as $rowR=>$valR)
				{
					$colorSizeBunIDArr = explode("*",$valR);
					//echo $colorSizeBunIDArr[0]; die;
					$BunQtyArr[$colorSizeBunIDArr[0]]=$colorSizeBunIDArr[1];
				}

				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = array_filter(explode("**",$colorIDvalue));
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					/*if($is_control==1 && $user_level!=2)
					{
						if($colorSizeNumberIDArr[1]>0)
						{
							if(($colorSizeNumberIDArr[1]*1)>($color_pord_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]*1))
							{
								echo "35**Embellishment Quantity Not Over Cutting Qnty";
								//check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}*/
					$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_of_fab_per_pcs'];
					$sewing_oh = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cut_oh_per_pcs'];
					$trims_cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['trims_cost_per_pcs'];
					// $cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_per_pcs'];

					$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['prod_qty'];
					$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['amount'];

					$cost_per_pcs = $amount/$prod_qty;
					$cost_per_pcs = fn_number_format($cost_per_pcs,$dec_place[3],'.','');

					if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
					{
						//2 for Issue to Print / Emb Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$BunQtyArr[$colorSizeBunIDArr[0]]."',".$txt_remark_dtls.",".$cbo_color_type.",'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost."','".$cost_per_pcs."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$BunQtyArr[$colorSizeBunIDArr[0]]."',".$txt_remark_dtls.",".$cbo_color_type.",'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost."','".$cost_per_pcs."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
					else
					{
						echo "420**";die();
					}
				}
			}

			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{
				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0  order by size_number_id,color_number_id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}

				$rowExBundle = array_filter(explode("***",$colorBundleVal));
				foreach($rowExBundle as $rowR=>$valR)
				{
					$colorAndSizeBun_arr = explode("*",$valR);
					$sizeID = $colorAndSizeBun_arr[0];
					$colorID = $colorAndSizeBun_arr[1];
					$colorSizeBun = $colorAndSizeBun_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;
					$BunQtyArr[$index]=$colorSizeBun;
				}

				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
				$rowEx = array_filter(explode("***",$colorIDvalue));
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$colorID;

					/*if($is_control==1 && $user_level!=2)
					{
						if($colorSizeValue>0)
						{
							if(($colorSizeValue*1)>($color_pord_data[$colSizeID_arr[$index]]*1))
							{
								echo "35**Embellishment Quantity Not Over Cutting Qnty";
								//check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}*/
					$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_of_fab_per_pcs'];
					$sewing_oh = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cut_oh_per_pcs'];
					$trims_cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['trims_cost_per_pcs'];
					// $cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_per_pcs'];

					$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['prod_qty'];
					$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['amount'];

					$cost_per_pcs = $amount/$prod_qty;
					// echo "10**$amount/$prod_qty";die;
					$cost_per_pcs = fn_number_format($cost_per_pcs,$dec_place[3],'.','');

					if($colSizeID_arr[$index]!="")
					{
						//2 for Issue to Print / Emb Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$BunQtyArr[$index]."',".$txt_remark_dtls.",".$cbo_color_type.",'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost."','".$cost_per_pcs."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$BunQtyArr[$index]."',".$txt_remark_dtls.",".$cbo_color_type.",'".$cost_of_fab_per_pcs."','".$sewing_oh."','".$trims_cost."','".$cost_per_pcs."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
					else
					{
						echo "420**";die();
					}
				}
			}

 			//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}//end cond
		//echo "10**INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;
		$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
		$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);


		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			// echo "10**INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array;die;
		}
		// echo "10**".$rID.'='.$dtlsrID."=".$dtlsrDelete;die;

		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrDelete && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}else{
				if($rID)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID )
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		disconnect($con);
		die;
	}



	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);

 		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$hidden_po_break_down_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$hidden_po_break_down_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
		disconnect($con);
		die;
	}
}

function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit,$return_query='')
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);
	echo "10**".count ($arrUpdateFields).'=='.count ($arrUpdateValues) ; die;
	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	if($return_query==1){return $strQuery ;}

		return "10**".$strQuery;die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);

	if ($exestd){user_activities($exestd);}
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}

if($action=="emblishment_issue_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//$mst_id=implode(',',explode("_",$data[1]));
	//print_r ($mst_id);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

	$order_array=array();
	$order_sql="SELECT a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
	}
	//var_dump($order_array);

	$sql="SELECT id, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type,production_date, production_quantity, production_type, remarks, floor_id from pro_garments_production_mst where production_type=2 and id in($data[1]) and status_active=1 and is_deleted=0 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$sql_color_type=sql_select("SELECT color_type_id from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id='$data[1]' and production_type=2");
	$color_type_id=$sql_color_type[0][csf("color_type_id")];


?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
        	<?
            $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
            <td  align="left" rowspan="3" colspan="2">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='60' width='200' align="middle" />
                    <?
                }
                ?>
           </td>
            <td colspan="4" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="4" align="center" style="font-size:14px">
				<?

					echo show_company($data[0],'','');//Aziz
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
					?>
						<? if ($result[csf('plot_no')]!="") echo $result[csf('plot_no')].',&nbsp;&nbsp;';
						if ($result[csf('level_no')]!="") echo $result[csf('level_no')].',&nbsp;&nbsp;';
						if ($result[csf('road_no')]!="") echo $result[csf('road_no')].',&nbsp;&nbsp;';
						if ($result[csf('block_no')]!="") echo $result[csf('block_no')].',&nbsp;&nbsp;';
						if ($result[csf('city')]!="") echo $result[csf('city')].',&nbsp;&nbsp;';
						if ($result[csf('zip_code')]!="") echo $result[csf('zip_code')].',&nbsp;&nbsp;';
						if ($result[csf('province')]!="") echo $result[csf('province')].',&nbsp;&nbsp;';
						if ($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email : <? echo $result[csf('email')].',&nbsp;&nbsp;';?>
						Web : <? echo $result[csf('website')];
					}*/
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" align="center" style="font-size:20px"><u><strong>Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
				//echo $address;
            ?>
        	<td width="100" rowspan="4" valign="top" colspan="2"><p><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></p></td>
            <td width="125"><strong>Issue ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('id')]; ?></td>
            <td width="125"><strong>Buyer :</strong></td><td width="175px"><? echo $buyer_library[$order_array[$dataArray[0][csf('po_break_down_id')]]['buyer_name']]; ?></td>
        </tr>
        <tr>
        <td> <strong>Job No</strong></td> <td> <? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['job']; ?></td>
         <td><strong>Order No :</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_number']; ?></td>
        </tr>
        <tr>

           <td><strong>Order Qty:</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_quantity']; ?></td>
        	<td><strong>Style Ref. :</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>
        </tr>
        <tr>
        	<td><strong>Item :</strong></td><td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        	<td><strong>Color Type :</strong></td><td><? echo $color_type[$color_type_id]; ?></td>
        </tr>
        <tr>
            <td><strong>Emb. Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Embel. Name :</strong></td><td><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Emb. Type:</strong></td><td><? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]]; ?></td>
            <td><strong>Issue Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
            <td><strong>Challan No:</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
    </table>
         <br>
        <?
			$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}

			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";

			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?>

	<div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
				<?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Issue Qnty.</th>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            $tot_qnty=array();
                foreach($color_array as $cid)
                {
                    if ($i%2==0)
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                            $tot_qnty[$cid]+=$qun_array[$cid][$sizval];
							$tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$production_quantity+=$tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $production_quantity; ?></td>
        </tr>
    </table>
    &nbsp;<br>
    <table align="right" cellspacing="0" width="900" >
        <tr>
            <td width="80"><strong>Remarks : </strong></td>
            <td align="left"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
        </tr>
    </table>
        <br>
		 <?
            echo signature_table(26, $data[0], "900px");
         ?>
	</div>
	</div>
<?
exit();
}
//1st Print End

if($action=="emblishment_without_print") //Start here
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	//$mst_id=implode(',',explode("_",$data[1]));
	//print_r( $mst_id);die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");

	$order_array=array();
	$order_sql="SELECT a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
	}
	//var_dump($order_array);

	$sql="select id, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, remarks, floor_id from pro_garments_production_mst where production_type=2 and id in($data[1]) and status_active=1 and is_deleted=0 ";
	  $sel_col=($db_type!=0) ? " listagg(color_type_id,',') within group(order by color_type_id) as color_type_id  " : " group_concat(color_type_id) as color_type_id";

	$sql_color_type=sql_select("SELECT $sel_col  from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id in($data[1]) and production_type=2");
	$color_type_id=$sql_color_type[0][csf("color_type_id")];
	$type_ids=array_unique(explode(",", $color_type_id));
	$color_tp="";
	foreach($type_ids as $key=>$val)
	{
		if($color_tp=="")
		{
			$color_tp=$color_type[$val];
		}
		else
		{
			$color_tp .=','.$color_type[$val];
		}

	}


	//echo $sql;
	$dataArray=sql_select($sql);




?>
<div style="width:930px; ">
    <table width="900" cellspacing="0" align="right">
        <tr>
         <?
            $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
            <td  align="left" width="200" rowspan="3" colspan="2">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='60' width='200' align="middle" />
                    <?
                }
                ?>
           </td>
            <td colspan="4" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">

        	<td colspan="4" align="center" style="font-size:14px;">
				<b style=" ">
				<?

					echo show_company($data[0],'','');//Aziz
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
					?>
						<? if ($result[csf('plot_no')]!="") echo $result[csf('plot_no')].',&nbsp;&nbsp;';
						if ($result[csf('level_no')]!="") echo $result[csf('level_no')].',&nbsp;&nbsp;';
						if ($result[csf('road_no')]!="") echo $result[csf('road_no')].',&nbsp;&nbsp;';
						if ($result[csf('block_no')]!="") echo $result[csf('block_no')].',&nbsp;&nbsp;';
						if ($result[csf('city')]!="") echo $result[csf('city')].',&nbsp;&nbsp;';
						if ($result[csf('zip_code')]!="") echo $result[csf('zip_code')].',&nbsp;&nbsp;';
						if ($result[csf('province')]!="") echo $result[csf('province')].',&nbsp;&nbsp;';
						if ($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email : <? echo $result[csf('email')].',&nbsp;&nbsp;';?>
						Web : <? echo $result[csf('website')];
					}*/
                ?>
                </b>
            </td>
        </tr>
        <tr>
            <td colspan="4" align="center" style="font-size:20px"><u><strong><? //echo $data[2];  ?> Embellishment Delivery Challan</strong></u></td>
        </tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
				//echo $address;
            ?>
        	<td width="100" rowspan="4" valign="top" colspan="2"><p><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong><br>
            <b>Location: </b> <? echo $location_library[$dataArray[0][csf('location')]];?>;
            </p></td>
            <td width="125"><strong>Sys.Challan No</strong></td> <td width="175px"><strong>: </strong>&nbsp;<? echo $dataArray[0][csf('id')]; ?></td>
            <td width="125"><strong>Buyer </strong></td><td width="175px"><strong>: </strong>&nbsp;<? echo $buyer_library[$order_array[$dataArray[0][csf('po_break_down_id')]]['buyer_name']]; ?></td>
        </tr>
        <tr>
        <td> <strong>Job No</strong></td> <td><strong>: </strong>&nbsp; <? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['job']; ?></td>
         <td><strong>Order No </strong></td><td><strong>: </strong>&nbsp;<? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_number']; ?></td>
        </tr>
        <tr>

           <td><strong>Order Qty</strong></td><td><strong>: </strong>&nbsp;<? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_quantity']; ?></td>
        	<td><strong>Style Ref. </strong></td><td><strong>: </strong>&nbsp;<? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>
        </tr>
        <tr>
        	<td><strong>Item </strong></td><td><strong>: </strong>&nbsp;<? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        	<td><strong>Embel. Name </strong></td><td><strong>: </strong>&nbsp;<? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
        </tr>

        <tr>
            <td><strong>Emb. Type</strong></td><td><strong>: </strong>&nbsp;<? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]];  ?></td>
            <td><strong>Challan Date</strong></td><td><strong>: </strong>&nbsp;<? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
            <td><strong>Challan No</strong></td><td><strong>: </strong>&nbsp; <? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
        <tr>
        	<td><strong>Color Type</strong></td>
        	<td><? echo $color_tp; ?></td>
        </tr>
    </table>
         <br>
        <?
			$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}

			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";

			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?>

	<div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
				<?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Qty.</th>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            $tot_qnty=array();
                foreach($color_array as $cid)
                {
                    if ($i%2==0)
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                            $tot_qnty[$cid]+=$qun_array[$cid][$sizval];
							$tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$production_quantity+=$tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $production_quantity; ?></td>
        </tr>
    </table>
    &nbsp;<br>
    <table align="right" cellspacing="0" width="900" >
        <tr>
            <td width="80"><strong>Remarks : </strong></td>
            <td align="left"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
        </tr>
    </table>
        <br>
		 <?
            echo signature_table(26, $data[0], "900px");
         ?>
	</div>
	</div>
<?
exit();
}
if($action=="emblishment_issue_print2") // Print 2 Start.
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$mst_id=implode(',',explode("_",$data[1]));
	//print_r ($mst_id);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");

	$order_array=array();
	$order_sql="SELECT a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
	}
	//var_dump($order_array);

	$sql="SELECT id, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_quantity, production_type, remarks, floor_id,body_part_info from pro_garments_production_mst where production_type=2 and id in($mst_id) and status_active=1 and is_deleted=0 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$issue_date='';
	foreach($dataArray as $row)
	{
		if($issue_date!='') $issue_date.=", ".change_date_format($row[csf('production_date')]);else  $issue_date=change_date_format($row[csf('production_date')]);
		if($body_part_info!='') $body_part_info.=", ".$row[csf('body_part_info')];else  $body_part_info=$row[csf('body_part_info')];
	}
	$body_part_info=array_unique(explode(", ",$body_part_info));

	$sql_color_type=sql_select("SELECT color_type_id from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id='$mst_id' and production_type=2");
	$color_type_id=$sql_color_type[0][csf("color_type_id")];

	//echo $issue_dates=implode(", ",array_unique(explode(", ",$issue_date)));
	?>
	<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>

        	<?
            $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
                ?>
            <td  align="left" rowspan="3">
                <?
                foreach($data_array as $img_row)
                {
					?>
                    <img src='../<? echo $img_row[csf('image_location')]; ?>' height='60' width='200' align="middle" />
                    <?
                }
                ?>
           </td>
            <td colspan="4" align="center" style="font-size:24px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            <td></td>
        </tr>
        <tr class="form_caption">

        	<td colspan="4" align="center" style="font-size:14px">
				<?

					echo show_company($data[0],'','');//Aziz
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
					?>
						<? if ($result[csf('plot_no')]!="") echo $result[csf('plot_no')].',&nbsp;&nbsp;';
						if ($result[csf('level_no')]!="") echo $result[csf('level_no')].',&nbsp;&nbsp;';
						if ($result[csf('road_no')]!="") echo $result[csf('road_no')].',&nbsp;&nbsp;';
						if ($result[csf('block_no')]!="") echo $result[csf('block_no')].',&nbsp;&nbsp;';
						if ($result[csf('city')]!="") echo $result[csf('city')].',&nbsp;&nbsp;';
						if ($result[csf('zip_code')]!="") echo $result[csf('zip_code')].',&nbsp;&nbsp;';
						if ($result[csf('province')]!="") echo $result[csf('province')].',&nbsp;&nbsp;';
						if ($result[csf('country_id')]!=0) echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email : <? echo $result[csf('email')].',&nbsp;&nbsp;';?>
						Web : <? echo $result[csf('website')];
					}*/
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="4" align="center" style="font-size:20px"><u><strong>Challan/Gate Pass</strong></u></td>
        </tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
				//echo $address;
            ?>
        	<td width="100" rowspan="4" valign="top" colspan="2"><p><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></p>
        		<p><strong>Location : </strong> <? echo $location_library[$dataArray[0][csf('location')]]; ?></p>
        	</td>
            <td width="125"><strong>Issue ID:</strong></td> <td width="175px"><? echo $mst_id;//$dataArray[0][csf('id')]; ?></td>
            <td width="125"><strong>Buyer :</strong></td><td width="175px"><? echo $buyer_library[$order_array[$dataArray[0][csf('po_break_down_id')]]['buyer_name']]; ?></td>
        </tr>
        <tr>
        <td> <strong>Job No</strong></td> <td> <? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['job']; ?></td>
         <td><strong>Order No :</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_number']; ?></td>
        </tr>
        <tr>

           <td><strong>Order Qty:</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_quantity']; ?></td>
        	<td><strong>Style Ref. :</strong></td><td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>
        </tr>
        <tr>
        	<td><strong>Item :</strong></td>
        	<td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        	<td><strong>Color Type:</strong></td>
        	<td><? echo $color_type[$color_type_id]; ?></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Emb. Source:</strong><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Embel. Name :</strong></td><td><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
        <td><strong>Emb. Type:</strong></td><td><? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]]; ?></td>
        </tr>
        <tr>

            <!--<td><strong>Issue Date:</strong></td><td><? //echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
            <td><strong>Challan No:</strong></td><td><? //echo $dataArray[0][csf('challan_no')]; ?></td>-->
        </tr>
    </table>
         <br>
        <?
			//$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id,b.size_order order by b.size_order";
			// echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}
			//pro_garments_production_mst c, c.id=a.mst_id and

			$sql="SELECT sum(a.production_qnty) as production_qnty,c.production_date as issue_date,c.challan_no, b.color_number_id from pro_garments_production_mst c,pro_garments_production_dtls a, wo_po_color_size_breakdown b where c.id=a.mst_id and a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id,c.production_date,c.challan_no ";

			//echo $sql;
			// and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();$issue_data_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$issue_data_array[$row[csf('color_number_id')]]['issue_date'].=",".$row[csf('issue_date')];
				$issue_data_array[$row[csf('color_number_id')]]['chal_no'].=",".$row[csf('challan_no')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?>

	<div style="width:100%;">
	    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="70">Issue Date</th>
	            <th width="70">Chal. No</th>
	            <th width="80" align="center">Color/Size</th>
					<?
	                foreach ($size_array as $sizid)
	                {
						//$size_count=count($sizid);
	                    ?>
	                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
	                    <?
	                }
	                ?>
	            <th width="80" align="center">Total Issue Qnty.</th>
	        </thead>
	        <tbody>
				<?
	            //$mrr_no=$dataArray[0][csf('issue_number')];
	            $i=1;
	            $tot_qnty=array();
	                foreach($color_array as $cid)
	                {
	                    if ($i%2==0)
	                        $bgcolor="#E9F3FF";
	                    else
	                        $bgcolor="#FFFFFF";
						$color_count=count($cid);
						$issue_date=ltrim($issue_data_array[$cid]['issue_date'],',');
						$challn_no=ltrim($issue_data_array[$cid]['chal_no'],',');
						$date_pro=array_unique(explode(",",$issue_date));
						$all_date='';
						foreach($date_pro as $date_val)
						{
							if($all_date=='') $all_date=change_date_format($date_val);else $all_date.=",".change_date_format($date_val);
						}
						//print_r($date_pro);
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td><? echo $i;  ?></td>
	                        <td><? echo $all_date;  ?></td>
	                        <td><? echo $challn_no;  ?></td>
	                        <td><? echo $colorarr[$cid]; ?></td>
	                        <?
	                        foreach ($size_array as $sizval)
	                        {
								$size_count=count($sizval);
	                            ?>
	                            <td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
	                            <?
	                            $tot_qnty[$cid]+=$qun_array[$cid][$sizval];
								$tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
	                        }
	                        ?>
	                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
	                    </tr>
	                    <?
						$production_quantity+=$tot_qnty[$cid];
						$i++;
	                }
	            ?>
	        </tbody>
	        <tr>
	            <td colspan="4" align="right"><strong>Grand Total :</strong></td>
	            <?
					foreach ($size_array as $sizval)
					{
						?>
	                    <td align="right"><?php echo $tot_qnty_size[$sizval]; ?></td>
	                    <?
					}
				?>
	            <td align="right"><?php echo $production_quantity; ?></td>
	        </tr>
	    </table>
	    &nbsp;<br>
	    <table align="right" cellspacing="0" width="900" >
	        <tr>
	            <td width="80"><strong>Remarks : </strong></td>
	            <td align="left"><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
	        </tr>
	    </table>
	    &nbsp;<br>
	    <table align="right" cellspacing="0" width="400"  border="1" rules="all" class="rpt_table">
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="250">Body Part</th>
	            <th >Quantity</th>
	        </thead>
	        <tbody>
				<?
	            //$mrr_no=$dataArray[0][csf('issue_number')];
	            $i=1;
	                foreach($body_part_info as $body_info)
	                {
	                	$single_body_info=explode("=",$body_info);
	                	for($i=0; $i<count($single_body_info); $i++){
	                		$actual_body_info=explode("_",$single_body_info[$i]);
	            			if ($i%2==0)
	                        	$bgcolor="#E9F3FF";
		                    else
		                        $bgcolor="#FFFFFF";
	            			?>
		                    <tr bgcolor="<? echo $bgcolor; ?>">
		                        <td><? echo $i+1;  ?></td>
		                        <td><? echo $body_part[$actual_body_info[0]];  ?></td>
		                        <td align="right"><? echo number_format($actual_body_info[1],4);  ?></td>
		                    </tr>
		                    <?
	                	}
	                }
	            ?>
	        </tbody>
	    </table>
	    &nbsp;<br>
	        <br>
			 <?
	            echo signature_table(26, $data[0], "900px");
	         ?>
		</div>
	</div>
<?
exit();
}


if($action=="emblishment_issue_print3") // Print 3 Start.
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$mst_id=implode(',',explode("_",$data[1]));
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
	$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$country_shortname_arr=return_library_array( "select id, short_name from lib_country", "id", "short_name");
	$floor_arr = return_library_array("SELECT id,floor_name from lib_prod_floor", "id", "floor_name");
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	$order_array=array();
	$order_sql="SELECT a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	$order_sql_result=sql_select($order_sql);
	foreach ($order_sql_result as $row)
	{
		$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$order_array[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
		$order_array[$row[csf('id')]]['job']=$row[csf('job_no')];
	}
	$sel_col=($db_type!=0) ? " listagg(color_type_id,',') within group(order by color_type_id) as color_type_id  " : " group_concat(color_type_id) as color_type_id";

	$sql="SELECT id, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location,location_lc, embel_name, embel_type, production_quantity, production_type, remarks, floor_id,floor_lc from pro_garments_production_mst where production_type=2 and id in($mst_id) and status_active=1 and is_deleted=0 ";

	//echo $sql;die;

	$sql_color_type=sql_select("SELECT $sel_col from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id in ($mst_id) and production_type=2");
	$color_type_id=$sql_color_type[0][csf("color_type_id")];
	$type_ids=array_unique(explode(",", $color_type_id));
	$color_tp="";
	foreach($type_ids as $key=>$val)
	{
		if($color_tp=="")
		{
			$color_tp=$color_type[$val];
		}
		else
		{
			$color_tp .=','.$color_type[$val];
		}
	}
	$dataArray=sql_select($sql);
	$po_id=$dataArray[0][csf("po_break_down_id")];
	$item_id=$dataArray[0][csf("item_number_id")];
	$country_id=$dataArray[0][csf("country_id")];
	$issue_date='';
	foreach($dataArray as $row)
	{
		$country=$row[csf('country_id')];
		if($issue_date!='') $issue_date.=", ".change_date_format($row[csf('production_date')]);else  $issue_date=change_date_format($row[csf('production_date')]);

		$remarks_all .= $row[csf('remarks')].',';
	}

	$cutData=sql_select("SELECT production_source, serving_company, location from pro_garments_production_mst WHERE po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and production_type=1");
	$cutt_comapny=""; $cutt_location="";
	foreach($cutData as $row)
	{
		if($row[csf('production_source')]==1)
		{
			if($cutt_comapny=="") $cutt_comapny=$comp[$row[csf('serving_company')]]; else $cutt_comapny.='**'.$comp[$row[csf('serving_company')]];
			if($cutt_location=="") $cutt_location=$location_arr[$row[csf('location')]]; else $cutt_location.='**'.$location_arr[$row[csf('location')]];
		}
		else
		{
			if($cutt_comapny=="") $cutt_comapny=$supplier_library[$row[csf('serving_company')]]; else $cutt_comapny.='**'.$supplier_library[$row[csf('serving_company')]];
		}
	}
	unset($cutData);

	$cutt_comapny = implode(",",array_filter(array_unique(explode("**",$cutt_comapny))));
	$cutt_location = implode(",",array_filter(array_unique(explode("**",$cutt_location))));
	?>
	<div style="width:930px; margin-left:120px">
    <table width="900" cellspacing="0" align="right">
        <tr>
					<?

					$data_array=sql_select("SELECT image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
					?>
					<td  align="left" rowspan="3" colspan="2">
						 <?
							foreach($data_array as $img_row)
							{
						?>
						<img src='../<? echo $img_row[csf('image_location')]; ?>' height='60' width='200' align="middle" />
						<?
                }
            ?>
           </td>
            <td colspan="4"  width="300" align="center" style="font-size:24px;padding-right:300px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
			<td align="center"><strong>Location :</strong></td>
			<td style="padding-right: 50px">
				<? echo $location_arr[$dataArray[0][csf('location_lc')]];?>
         	</td>
        </tr>
		<tr class="form_caption">
			<td align="center"><strong>Floor/Unit :</strong></td>
			<td style="padding-right: 50px">
				<? echo $floor_arr[$dataArray[0][csf('floor_lc')]];?>
			</td>
        </tr>
        <tr>
            <td colspan="8" width="300" align="center" style="font-size:18px;padding-right:10px"><u><strong>Embellishment Issue Challan <? // echo $country_arr[$country]; ?> </strong></u></td>
        </tr>

        <tr>
					<?
				$nameArray=sql_select( "select country_id from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];
                }
            ?>
        	<td width="100" valign="top" colspan="2">
        		<p><strong>To</strong></p>
        	</td>
        	<td width="125">
        		<strong>Buyer :</strong>
        	</td>
        	<td width="175px"><? echo $buyer_library[$order_array[$dataArray[0][csf('po_break_down_id')]]['buyer_name']]; ?></td>
        	<td>
        		<strong>Item :</strong>
        	</td>
        	<td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>


				</tr>

        <tr>
        	<td colspan="2">
        		<p><strong><? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]]; //.'<br>'.$address;  ?>
        		</strong></p>
        	</td>
					<td><strong>Int. Reff :</strong></td>
        	<td>
        		<?
        			$internal_ref=return_field_value("f.grouping"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$dataArray[0][csf('po_break_down_id')],"grouping");
        			echo $internal_ref;
        		?>
        	</td>
        	<td><strong>Style Ref. :</strong></td>
        	<td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>

        </tr>

        <tr>
        	<!-- <td colspan="2"> -->
        		<?
        			// //echo $dataArray[0][csf('production_source')];
        			// if($dataArray[0][csf('production_source')]==1)
        			// 	{
        			// 		$nameArray=sql_select( "SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id='".$dataArray[0][csf('serving_company')]."' and status_active=1 and is_deleted=0");
							// foreach ($nameArray as $result)
							// {
							// 	if ($result[csf('plot_no')]!="") echo $result[csf('plot_no')].',&nbsp;&nbsp;';
							// 	if ($result[csf('level_no')]!="") echo $result[csf('level_no')].',&nbsp;&nbsp;';
							// 	if ($result[csf('road_no')]!="") echo $result[csf('road_no')].',&nbsp;&nbsp;';
							// 	if ($result[csf('block_no')]!="") echo $result[csf('block_no')].',&nbsp;&nbsp;';
							// 	if ($result[csf('city')]!="") echo $result[csf('city')];
							// }
        			// 	}
        			// else if($dataArray[0][csf('production_source')]==3)
        			// 	echo $address;
        			// 	/*echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;*/
        		?>
        	<!-- </td> -->
					 <td><strong>Location :</strong></td>
         <td style="padding-right: 50px">
         	<? echo $location_arr[$dataArray[0][csf('location')]];  ?>
        	<td> <strong>Job No</strong></td>
        	<td> <? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['job']; ?></td>
        	<td><strong>Order No :</strong></td>
         <td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_number']; ?></td>
        </tr>

        <tr>
         <td><strong>Floor/Unit :</strong></td>
         <td style="padding-right: 50px">
         	<? echo $floor_arr[$dataArray[0][csf('floor_id')]];  ?>
         </td>

				 <td><strong>Order Qty:</strong></td>
            <td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_quantity']; ?></td>
							<td><strong>Emb. Source:</strong></td>
        		<td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
        </tr>

        <tr>
					<!-- optional========== -->
					<td><strong>Emb. Type:</strong></td>
	        <td>
	        	<? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]];
	        	?>
	        </td>
         <td><strong>Emb.Name :</strong></td>
         <td><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
				 <td><strong>Color Type:</strong></td>
            <td><? echo $color_tp;?></td>
        </tr>
        <tr>
        	<td><strong>Cut. Company:</strong></td>
            <td colspan="2" style="word-break:break-all"><?=$cutt_comapny; ?></td>
            <td><strong>Cutting Location:</strong></td>
            <td colspan="2" style="word-break:break-all"><?=$cutt_location; ?></td>
        </tr>
        <tr>
        	 <td><strong>Remarks:</strong></td>
            <td><? echo implode(',',array_unique(explode(',',rtrim($remarks_all,',')))); ?></td>
        </tr>
		<?
			$sql="SELECT a.id, a.production_date, a.man_cutt_no, a.challan_no, a.floor_id, a.country_id, b.production_qnty, c.color_number_id, c.size_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id in($mst_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 order by c.size_order,c.color_number_id";
			// echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$color_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$qun_array[$row[csf('id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?>

	<div style="width:100%;">
	    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="60">Issue Date</th>
	            <th width="60">Issue ID</th>
				<th width="60">Challan No</th>
	            <th width="60">Manual Cut No</th>
	            <th width="60">Country Name</th>
	            <th width="60">Country Short Name</th>

	            <th width="80" align="center">Color/Size</th>
					<?
	                foreach ($size_array as $sizid)
	                {
	                    ?>
	                        <th width="50"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
	                    <?
	                }
	                ?>
	            <th width="80" align="center">Total Issue Qty.</th>
	            <th width="80" align="center">Challan No</th>
	            <th align="center">Remarks</th>
	        </thead>
	        <tbody>
	        	<?
	        	 $sql_prod="SELECT a.id, a.production_date, a.man_cutt_no, a.challan_no, a.floor_id, b.remarks_dtls, a.country_id, c.color_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id in($mst_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.id, a.production_date, a.man_cutt_no, a.challan_no, a.floor_id, b.remarks_dtls, a.country_id, c.color_number_id order by a.production_date";
	        		$result_prod=sql_select($sql_prod);
					$i=1;
					$tot_specific_size_qnty=array();
					//$grand_tot_color_size_qty=0;
					foreach ($result_prod as $val)
					{
						$tot_color_size_qty=0;
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
						<tr>
	                        <td> <? echo $i;  ?> </td>
	                        <td> <? echo change_date_format($val[csf("production_date")]);?> </td>
	                        <td> <?	echo $val[csf("id")]; ?> </td>
							<td> <?	echo $val[csf("challan_no")]; ?> </td>
	                        <td> <? echo $val[csf("man_cutt_no")]; ?> </td>
	                        <td> <? echo $country_arr[$val[csf("country_id")]]; ?> </td>
	                        <td> <? echo $country_shortname_arr[$val[csf("country_id")]]; ?> </td>
	                        <td> <? echo $colorarr[$val[csf("color_number_id")]]; ?> </td>
	                        <?
	                        foreach ($size_array as $sizval)
	                        {
	                        ?>
	                            <td align="right"><? echo $qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval]; ?></td>
	                        <?
	                           $tot_color_size_qty+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
	                           $tot_specific_size_qnty[$sizval]+=$qun_array[$val[csf("id")]][$val[csf("color_number_id")]][$sizval];
	                        }
	                        ?>
	                        <td align="right">
	                        	<?
	                        	echo $tot_color_size_qty;
	                        	?>
	                        </td>
							<td align="right"><? echo $val[csf("challan_no")];?></td>
							<td align="right"><? echo $val[csf("remarks_dtls")];?></td>

	                    </tr>
	            			<?
								$i++;
								}
							?>
	        		</tbody>
	        <tr>
	            <td colspan="8" align="right"><strong>Grand Total : &nbsp;</strong></td>
	            <?
					foreach ($size_array as $sizval)
					{
						?>
	                    <td align="right"><?php echo $tot_specific_size_qnty[$sizval]; ?></td>
	                    <?
					}
				?>
	            <td align="right"><?php echo array_sum($tot_specific_size_qnty); //$grand_tot_color_size_qty; ?></td>
				<td></td>
				<td></td>
	        </tr>
	        <tr>
	        	<td colspan="9"> <strong>In Word : </strong> <? echo number_to_words( array_sum($tot_specific_size_qnty), 'Pc\'s' ); ?> </td>
	        </tr>
	    </table>
        <br>
		 <?
            echo signature_table(26, $data[0], "900px");
         ?>
	</div>
	</div>
<?
exit();
}


if ($action=="load_drop_down_color_type")
{

	$sql="SELECT b.color_type_id from  wo_po_break_down a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c where   a.job_no_mst=b.job_no  and b.id=c.pre_cost_fabric_cost_dtls_id and a.id=c.po_break_down_id and b.job_no=c.job_no  and   a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id='$data' and c.cons>0  group by b.color_type_id";
	foreach(sql_select($sql) as $key=>$vals)
	{
		$color_type_arr[$vals[csf("color_type_id")]]=$color_type[$vals[csf("color_type_id")]];
	}


	if(count(sql_select($sql))>1)
	{
		echo create_drop_down( "cbo_color_type", 110, $color_type_arr,"", 0, "Select Type", $selected,"");
	}
	else
	{
		echo create_drop_down( "cbo_color_type", 110, $color_type_arr,"", 0, "Select Type", $selected,"");
	}


	exit();
}

?>
<script type="text/javascript">
	function getActionOnEnter(event){
			if (event.keyCode == 13){
				document.getElementById('btn_show').click();
			}

	}
</script>
