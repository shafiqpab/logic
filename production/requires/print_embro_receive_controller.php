<?
session_start();
include('../../includes/common.php');
require_once('../../includes/class4/class.conditions.php');
require_once('../../includes/class4/class.reports.php');
require_once('../../includes/class4/class.washes.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];

$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$buyer_name_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
if($db_type==0) $select_field="group";
else if($db_type==2) $select_field="wm";
else $select_field="";//defined Later

//------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 200, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/print_embro_receive_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 200, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (7,8,9) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
}
 

if($action=="print_button_variable_setting") //Print Button
{
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=123 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
	exit();
}

if ($action=="load_variable_settings")
{ 
	// echo "setFieldLevelAccess($data);\n";

	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select printing_emb_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("printing_emb_production")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=33 and page_category_id=28","is_control");
	if($variable_is_control=="") $variable_is_control=0;
	echo "document.getElementById('variable_is_controll').value=".$variable_is_control.";\n";

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
	// $sqlResult =sql_select("SELECT b.po_number,a.country_id,a.item_number_id, a.cost_of_fab_per_pcs,a.cost_per_pcs from pro_garments_production_mst a,wo_po_break_down b,lib_country c where b.id=a.po_break_down_id and a.country_id=c.id and a.po_break_down_id='$sys_id' and a.status_active=1 and a.is_deleted=0 and a.production_type=3 and a.embel_name=3");

	$sqlResult =sql_select("SELECT b.id as po_id,b.po_number,c.item_number_id,c.color_number_id, a.cost_of_fab_per_pcs,a.cut_oh_per_pcs,a.cost_per_pcs,a.fab_rate_per_pcs,a.trims_cost_per_pcs from pro_garments_production_mst e, pro_garments_production_dtls a,WO_PO_COLOR_SIZE_BREAKDOWN c,wo_po_break_down b,lib_country d where e.id=a.mst_id and a.COLOR_SIZE_BREAK_DOWN_ID=c.id and b.id=c.po_break_down_id  and c.country_id=d.id and c.po_break_down_id=e.po_break_down_id and c.po_break_down_id='$sys_id' and a.status_active=1 and a.is_deleted=0 and a.production_type=3 and e.embel_name=3 and a.cost_per_pcs is not null");
	if(count($sqlResult)==0)
	{
		?>
		<div class="alert alert-danger">Data not found!</div>
		<?
		die;
	}
	$data_array = array();
	foreach ($sqlResult as $v)
	{
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['fab_rate_per_pcs'] = $v['FAB_RATE_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['trims_cost_per_pcs'] = $v['TRIMS_COST_PER_PCS'];
		$data_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['po_number'] = $v['PO_NUMBER'];
	}
	?>
 		<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
			<thead>
				<th width="150">PO</th>
				<th width="150">Item</th>
				<th width="150">Color</th>
				<th width="100">Wash Rate Per Pcs</th>
				<th width="100">Cost Per Pcs</th>
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

if ($action=="load_variable_settings_reject")
{
	echo "$('#embro_production_variable').val(0);\n";
	$sql_result = sql_select("select printing_emb_production from variable_settings_production where company_name=$data and variable_list=28 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#embro_production_variable').val(".$result[csf("printing_emb_production")].");\n";
		echo "$('#txt_reject_qty').removeAttr('readonly','readonly');\n";
		if($result[csf("printing_emb_production")]==3) //Color and Size
		{
				echo "$('#txt_reject_qty').attr('readonly','readonly');\n";
		}
		else
		{
			echo "$('#txt_reject_qty').removeAttr('readonly','readonly');\n";
		}
	}
 	exit();
}

if($action=="load_drop_down_emb_receive")
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
			echo create_drop_down( "cbo_emb_company", 200, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "load_location();" ); //Removed fnc_workorder_search(this.value); from load_location(); 's right side
		//}
	}
	else if($data==1)
		echo create_drop_down( "cbo_emb_company", 200, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_location();",0,0 );
	else
		echo create_drop_down( "cbo_emb_company", 200, $blank_array,"", 1, "--- Select ---", $selected, "load_location();",0,0 ); //Removed fnc_workorder_search(this.value); from load_location(); 's right side

	exit();
}


if ($action=="load_drop_down_workorder")
{
	$explode_data = explode("_",$data);
	$company_id=$explode_data[0];
	$supplier_id=$explode_data[1];
	$po_break_down_id=$explode_data[2];
	$emblishment_name=$explode_data[3];
	$gmt_item=$explode_data[4];
	//$sql= "select b.po_break_down_id,b.gmt_item,b.pre_cost_fabric_cost_dtls_id,b.gmts_color_id,b.wo_qnty,sum(b.wo_qnty) as cu_woq ,c.emb_name,c.emb_type from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c where a.job_no=b.job_no and  a.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no='$txt_job_no' and a.booking_type=6 and a.is_short=2 and  a.status_active=1  and 	a.is_deleted=0  group by b.po_break_down_id,b.gmt_item,b.pre_cost_fabric_cost_dtls_id,c.emb_name,c.emb_type  order by b.pre_cost_fabric_cost_dtls_id";

	//$sql = "select a.id,a.sys_number from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=".$explode_data[2]." and a.company_id=$explode_data[0]  and a.rate_for=20 and a.service_provider_id=$explode_data[1]   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number order by a.id";
	//echo $sql;

	// $sql= "SELECT a.id,a.booking_no  from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c where a.company_id=".$company_id." and a.job_no=b.job_no and  a.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_type=6 and a.is_short=2 and  a.status_active=1  and 	a.is_deleted=0 and a.supplier_id=".$supplier_id." and b.gmt_item= ".$gmt_item." and c.emb_name=".$emblishment_name." group by a.id,a.booking_no  order by a.booking_no";
	$sql= "SELECT a.id,a.booking_no  from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c,wo_pre_cos_emb_co_avg_con_dtls d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and c.id=d.pre_cost_emb_cost_dtls_id and b.po_break_down_id=d.po_break_down_id and  a.booking_type=6 and a.is_short=2 and  a.status_active=1  and a.is_deleted=0 and a.supplier_id=".$supplier_id." and b.gmt_item= ".$gmt_item." and c.emb_name=".$emblishment_name." and a.company_id=".$company_id." and d.po_break_down_id=$po_break_down_id  group by a.id,a.booking_no  order by a.booking_no";
	// echo $sql; exit();
	$dropdown = '<input type="text" name="cbo_work_order_booking_no" id="cbo_work_order_booking_no" class="text_boxes" style="width:187px;" placeholder="Browse" onDblClick="openmypage_work_order();" /><input type="hidden" id="cbo_work_order" name="cbo_work_order" value="0" />';
	echo $dropdown;
	//echo create_drop_down( "cbo_work_order", 200, $sql,"id,booking_no", 1, "-- Select Work Order --", $selected, "fnc_workorder_rate('$data',this.value)",0 );
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
	where a.id = b.mst_id  and b.po_id=d.id and c.id=d.job_id $company $buyer $wo_date $wo_cond $style_cond $file_cond $po_number_cond $internal_ref_cond $po_id_cond and a.status_active=1 and a.is_deleted=0 and b.rate_for='30' $job_cond
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
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_style').value+'_'+<? echo $preBookingNos;?>+'_'+document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+<? echo $cbo_service_source;?>+'_'+<?php echo $po_order_id;?>, 'create_booking_search_list_view', 'search_div', 'print_embro_receive_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
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

if ($action=="create_work_order_search_list_view")
{
	$data=explode('_',$data);
	//echo "<pre>";print_r($data); exit();
	$filter = '';
	if ($data[0]!=0) $filter .= " a.company_name='$data[0]' "; else { echo "Please Select Company First."; die; }
	if($data[9]!=0) $year_cond = " and TO_CHAR(a.insert_date, 'YYYY') = '$data[9]' "; else { echo "Please Select Year."; die; }
	if ($data[1]!=0) $filter .= " and d.SUPPLIER_ID = '$data[1]' "; else $filter .="";
	if ($data[2]!=0) $filter .= " and a.buyer_name='$data[2]' "; else $filter .=" ";
	if(!empty($data[3])){
		$filter .= "  and a.job_no_prefix_num = '$data[3]'  $year_cond  ";
	}else{
		$filter .= " $year_cond  ";
	}
	if(!empty($data[4])) $filter .= " and b.po_number='$data[4]' "; else { $filter .= ""; }
	if(!empty($data[5])) $filter .= " and a.style_ref_no='$data[5]' "; else { $filter .= ""; }
	if(!empty($data[6])) $filter .= " and d.BOOKING_NO_PREFIX_NUM='$data[6]' "; else { $filter .= ""; }
	if(!empty($data[13])) $filter .= " and b.id='$data[13]'"; else { $filter .= ""; } //order_id
	if(!empty($data[14])) $filter .= " and e.emb_name='$data[14]'"; else { $filter .= ""; } //cbo_embel_name


	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');

    //$po_no=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	//$sql= "SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.wo_date, c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no, c.job_no, b.po_id, d.file_no,d.po_number,d.grouping from garments_service_wo_mst a, garments_service_wo_dtls b, wo_po_details_master c ,wo_po_break_down d where a.id = b.mst_id  and b.po_id=d.id and c.id=d.job_id $company $buyer $wo_date $wo_cond $style_cond $file_cond $po_number_cond $internal_ref_cond $po_id_cond and a.status_active=1 and a.is_deleted=0 and b.rate_for='30' $job_cond group by a.id, a.sys_number_prefix_num, a.sys_number, a.wo_date, c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no, c.job_no, b.po_id, d.file_no,d.po_number,d.grouping";
   	//echo $sql;
	$newsql = "SELECT a.company_name, a.buyer_name, a.job_no_prefix_num, a.style_ref_no, a.job_no,
				b.file_no, b.po_number, b.grouping, d.booking_no, d.booking_date, d.buyer_id, d.id, e.emb_name

			FROM wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d, wo_pre_cost_embe_cost_dtls e
			WHERE a.id = b.job_id and c.po_break_down_id = b.id and c.booking_mst_id = d.id and e.id = c.pre_cost_fabric_cost_dtls_id and e.job_id = a.id and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and  e.status_active=1 and e.is_deleted=0 
				and  $filter  and d.entry_form in (574, 201)
				group by a.company_name, a.buyer_name, a.job_no_prefix_num, a.style_ref_no, a.job_no,
				b.file_no, b.po_number, b.grouping, d.booking_no, d.booking_date, d.buyer_id, d.id, e.emb_name";
				//echo $newsql; exit();
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
    			$result = sql_select($newsql);
	    		$i=1;
	            foreach($result as $row)
	            {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                     <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="work_order_set_value('<? echo $row[csf('id')].'_'.$row[csf('booking_no')]; ?>');">

						<td width="20"><? echo $i; ?></td>
						<td width="120"><p><? echo $row[csf('BOOKING_NO')]; ?></p></td>
						<td width="60"><p><? echo change_date_format($row[csf('BOOKING_DATE')]); ?></p></td>
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

if ($action=="work_order_popup")
{

	echo load_html_head_contents("Booking Search","../../", 1, 1, $unicode);
	extract($_REQUEST); //echo $cbo_embel_name; exit();
	$preBookingNos = 0;
	?>

	<script>

		function js_set_value(booking_no)
		{
			// alert(booking_no);
			document.getElementById('selected_booking').value=booking_no; //return;
	 	 	parent.emailwindow.hide();
		}

		function work_order_set_value(info)
		{

			$("#idNbookingNo").val(info);
			//$("#booking_id").val(id);
			//$("#booking_no").val(booking_no);
			//$("#cbo_work_order").val(id);
			//$("#cbo_work_order_booking_no").val(booking_no);
			parent.emailwindow.hide();
			//Working on it. need set value or browese work order
			/* $("#hidden_mst_id").val(id);
			$("#hidden_grmtItem_id").val(item_id);
			$("#hidden_po_qnty").val(po_qnty);
			$("#hidden_country_id").val(country_id);
			$("#hidden_company_id").val(document.getElementById('company_search_by').value);
				parent.emailwindow.hide(); */
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
                              <input type="hidden" id="cbo_embel_name" class="text_boxes" style="width:70px" value="<?=$cbo_embel_name;?>">


							<thead>
								<th  colspan="11">
									<?
									echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",1 );
									?>
								</th>
							</thead>
							<thead>
								<th width="150">Company Name</th>
								<th width="150">Supplier Name<?//=$supplier_id?></th>
								<th width="150">Buyer  Name</th>
								<th width="100">Job  No</th>
								<th width="100">Order No</th>
								<!-- <th width="100">Internal Ref.</th>
								<th width="100">File No</th> -->
								<th width="100">Style No.</th>
								<th width="100">WO No</th>
								<th width="200">Date Range</th>
								<th></th>
							</thead>
							<tr>
								<td>
									<input type="hidden" id="selected_booking">
									<input type="hidden" id="idNbookingNo" />
									<?
									echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", "".$company_id."", "load_drop_down( 'fabric_issue_to_finishing_process_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
									?>
								</td>
								<td>
									<?php
									if($cbo_service_source==3)
									{
										echo create_drop_down( "cbo_supplier_name", 152, "select a.id,a.supplier_name from lib_supplier a where a.status_active=1 ","id,supplier_name", 1, "-- Select --", "".$supplier_id."", "",1 );
										//echo create_drop_down( "cbo_supplier_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and b.party_type in (21,24,25) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "".$supplier_id."", "",1 );
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
								<!-- <td>
									<input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px">
								</td>
								<td>
									<input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px">
								</td> -->



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
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $preBookingNos;?>+'_'+<? echo $cbo_service_source;?>+'_'+<?php echo $po_order_id;?>+'_'+document.getElementById('cbo_embel_name').value, 'create_work_order_search_list_view', 'search_div', 'print_embro_receive_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?>
						</td>
					</tr>

   </table>
   <div style="width:100%; margin-top:5px" id="search_div" align="center"></div>

	</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="defect_data")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	$caption_name="";

    if($type==1) $caption_name="Reject Qty";

	?>
    <script>
		function fnc_close()
		{
			var save_string='';	var tot_defect_qnty='';
			var defect_id_array = new Array();
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				var txtDefectId=$(this).find('input[name="txtDefectId[]"]').val();
				var txtDefectQnty=$(this).find('input[name="txtDefectQnty[]"]').val();
				var txtDefectUpdateId=$(this).find('input[name="txtDefectUpdateId[]"]').val();
				tot_defect_qnty=tot_defect_qnty*1+txtDefectQnty*1;
				//
				if(txtDefectQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtDefectUpdateId+"**"+txtDefectId+"**"+txtDefectQnty;
					}
					else
					{
						save_string+=","+txtDefectUpdateId+"**"+txtDefectId+"**"+txtDefectQnty;
					}

					if( jQuery.inArray( txtDefectId, defect_id_array) == -1 )
					{
						defect_id_array.push(txtDefectId);
					}
				}
			});
			//alert (save_string);
			//var defect_type_id=
			$('#defect_type_id').val();
			$('#save_string').val( save_string );
			$('#tot_defectQnty').val( tot_defect_qnty );
			$('#all_defect_id').val( defect_id_array );
			parent.emailwindow.hide();
		}

		function calculate_rejecttype1()
		{
			var reject_qty=0;
			$("#tbl_list_search").find('tbody tr').each(function()
				{
				reject_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;
				// alert(reject_qty);
				});
			$("#reject_qty_td").text(reject_qty);
		}

	</script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="defect_1"  id="defect_1" autocomplete="off">
			<? //echo load_freeze_divs ("../../",$permission,1); ?>
            <fieldset style="width:350px;">
                <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="<? echo $save_data; ?>">
                <input type="hidden" name="tot_defectQnty" id="tot_defectQnty" class="text_boxes" value="<? echo $defect_qty; ?>">
                <input type="hidden" name="all_defect_id" id="all_defect_id" class="text_boxes" value="<? echo $all_defect_id; ?>">
                <input type="hidden" name="defect_type_id" id="defect_type_id" class="text_boxes" value="<? echo $type; ?>">

                   <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="340">
            	<thead>
                	<tr><th colspan="3"><? echo $caption_name; ?></th></tr>
                	<tr><th width="40">SL</th><th width="150">Reject Name</th><th>Reject Qty</th></tr>
                </thead>
            </table>

            <div style="width:340px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320" id="tbl_list_search">
				 <tbody>
                    <?



		         		if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_reject_type_for_arr as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>" onKeyUp="calculate_rejecttype3()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }


                    ?>
				 <tfoot>
                        <tr class="tbl_bottom">
                            <td align="right" colspan="2">Total</td>

                            <td align="right"  id="reject_qty_td" style="padding-right:20px"> <? echo $total_reject; ?></td>
                        </tr>
                 </tfoot>
                </table>
            </div>
			<table width="320" id="table_id">
				 <tr>
					<td align="center" colspan="3">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
					</td>
				</tr>
			</table>
            </fieldset>
        </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
  <?
  exit();
}



if($action=="load_drop_down_emb_receive_type")
{
	$data=explode("**",$data);
	$emb_name=$data[0];
	$po_id=$data[1];

	if($db_type==0) $embel_name_cond="group_concat(c.emb_type) as emb_type";
		else if($db_type==2) $embel_name_cond="LISTAGG(c.emb_type,',') WITHIN GROUP ( ORDER BY c.emb_type) as emb_type";
			$embl_type=return_field_value("$embel_name_cond","wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c","a.job_id=b.id and b.id=c.job_id and a.id=$po_id and c.emb_name=$emb_name","emb_type");

	//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
	if($emb_name==1)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "fn_generate_color_size_break_down(this.value)","","$embl_type");
	elseif($emb_name==2)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_embroy_type,"", 1, "--- Select Embroidery---", $selected, "fn_generate_color_size_break_down(this.value)" ,"","$embl_type" );
	elseif($emb_name==3)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_wash_type,"", 1, "--- Select wash---", $selected, "fn_generate_color_size_break_down(this.value)","","$embl_type" );
	elseif($emb_name==4)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_spwork_type,"", 1, "--- Select Special Works---", $selected, "fn_generate_color_size_break_down(this.value)","","$embl_type" );
	elseif($emb_name==5)
		echo create_drop_down( "cbo_embel_type", 200, $emblishment_gmts_type,"", 1, "--- Select Gmts Dyeing---", $selected, "fn_generate_color_size_break_down(this.value)","","$embl_type" );
	else
		echo create_drop_down( "cbo_embel_type", 200, $blank_array,"", 1, "--- Select---", $selected, "fn_generate_color_size_break_down(this.value)" );
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
					document.getElementById('search_by_td').innerHTML='<input onkeydown="getActionOnEnter(event)"	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
				}
				else if(str==1)
				{
					document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
					document.getElementById('search_by_td').innerHTML='<input onkeydown="getActionOnEnter(event)"	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
				}
				else if(str==3)
				{
					document.getElementById('search_by_th_up').innerHTML="File no";
					document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
				}
				else if(str==4)
				{
					document.getElementById('search_by_th_up').innerHTML="Internal Ref.";
					document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
				}
				else if(str==5)
				{
					document.getElementById('search_by_th_up').innerHTML="Job No";
					document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
				}
				else if(str==6)
				{
					document.getElementById('search_by_th_up').innerHTML="Challan No";
					document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
				}
				else //if(str==2)
				{
					load_drop_down( 'print_embro_receive_controller',document.getElementById('company_search_by').value,'load_drop_down_buyer', 'search_by_td' );
					document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				}
			}

		function js_set_value(id,item_id,po_qnty,plan_qnty,country_id,country_ship_date)
		{
			$("#hidden_mst_id").val(id);
			$("#hidden_grmtItem_id").val(item_id);
			$("#hidden_po_qnty").val(po_qnty);
			$("#hidden_country_id").val(country_id);
			$("#hidden_company_id").val(document.getElementById('company_search_by').value);

			$("#hidden_country_ship_date").val(country_ship_date);
				parent.emailwindow.hide();
		}



</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
                    <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                   		 <thead>
                        	<th>Company</th>
                        	<th width="130">Search By</th>
                        	<th width="130" align="center" id="search_by_th_up">Enter Order Number</th>
                        	<th width="200">Date Range</th>
                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    	</thead>
        				<tr>
        				<td width="130">
        				 	<?
        				 		$company_arr=return_library_array( "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name",'id','company_name');

        				 		if(count($company_arr)==1)
        				 		{
        				 			echo create_drop_down( "company_search_by", 200,$company_arr,"", 0, "-- Select --", $selected, "",0 );
        				 		}
        				 		else
        				 		{
        				 			echo create_drop_down( "company_search_by", 200,$company_arr,"", 1, "-- Select --", $selected, "",0 );
        				 		}

                           ?>
						</td>
                    		<td width="130">
							<?
							$searchby_arr=array(5=>"Job No",0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"File No",4=>" Internal Ref.",6=>"Challan No");
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
                     			<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('company_search_by').value+'_'+'<? echo $garments_nature."_".$order_id; ?>'+'_'+document.getElementById('cbo_year_selection').value, 'create_po_search_list_view', 'search_div', 'print_embro_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
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
					<input type="hidden" id="hidden_country_ship_date">
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
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$year = $ex_data[7];
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	if($company == 0)
	{
		//print_r ($data);die;
		echo "Please Select Company First."; die;
	}
 	$garments_nature = $ex_data[5];
 	$order_id = $ex_data[6];

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
			$sql_cond = " and b.grouping like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==5)
			$sql_cond = " and a.job_no_prefix_num like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==6)
			$sql_cond = " and e.challan_no = '".trim($txt_search_common)."'";
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

	$sql = "SELECT b.id, a.job_no_prefix_num,a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut, a.season_buyer_wise, a.season_year, a.brand_id
			from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c,wo_pre_cost_dtls d
			where a.id = b.job_id and a.status_active=1  and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond $projected_po_cond $year_cond and a.id=c.job_id and c.job_no=d.job_no and (d.embel_cost !=0 or d.wash_cost !=0 ) and b.shiping_status!=3 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ";//and b.shiping_status <> 3
			// echo $sql;die;


	if(trim($txt_search_common)!="" && trim($txt_search_by)==6) // when search by challan no
	{
		$sql = "SELECT b.id, a.job_no_prefix_num,a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut, a.season_buyer_wise, a.season_year, a.brand_id
				from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c,wo_pre_cost_dtls d, pro_garments_production_mst e
				where a.id = b.job_id and b.id=e.po_break_down_id and a.status_active=1  and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond $projected_po_cond and a.id=c.job_id and c.job_no=d.job_no and (d.embel_cost !=0 or d.wash_cost !=0 ) and b.shiping_status!=3 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ";//and b.shiping_status <> 3
	}

	// echo $sql;die;
	$result = sql_select($sql);
    if(count($result)==0)
    {
        ?>
        <center>
            <div style="width: 80%;" class="alert alert-danger">Data not found.Please try again.</div>
        </center>
        <?
        die();
    }
	$po_id_arr = array();
	foreach ($result as $val)
	{
		$po_id_arr[$val[csf('id')]] = $val[csf('id')];
	}
	$allPoIds = implode(",", $po_id_arr);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$season_arr = return_library_array("select id, season_name from lib_buyer_season", 'id', 'season_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');




	if($db_type==0)
	{
		$po_country_arr=return_library_array( "SELECT po_break_down_id, group_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "SELECT po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by po_break_down_id",'po_break_down_id','country');
	}

	$po_country_data_arr=array(); $po_color_arr=array();
	$poCountryData=sql_select( "SELECT  country_ship_date,pack_type,po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty,color_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by country_ship_date,pack_type,po_break_down_id, item_number_id, country_id,color_number_id");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('pack_type')]]['po_qnty'] +=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('pack_type')]]['plan_cut_qnty'] +=$row[csf('plan_cut_qnty')];
		$po_color_arr[$row[csf("po_break_down_id")]].=','.$row[csf("color_number_id")];
	}



	$total_rec_qty_data_arr=array();
	$total_rec_qty_arr=sql_select( "select po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=3 and po_break_down_id in($allPoIds) group by po_break_down_id, item_number_id, country_id");

	foreach($total_rec_qty_arr as $row)
	{
		$total_rec_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
	}


	$total_issue_qty_sql=( "select po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=2 and po_break_down_id in($allPoIds) group by po_break_down_id, item_number_id, country_id");
	$issue_qnty_sql=sql_select($total_issue_qty_sql);
	$total_issue_qty_arr=array();
	foreach($issue_qnty_sql as $row)
	{
		$total_issue_qty_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
	}
	?>
    <div style="width:1510px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Shipment Date</th>
                <th width="50">Job No</th>
                <th width="100">Order No</th>
                <th width="80">Buyer</th>
				<th width="80">Brand</th>
                <th width="80">Season</th>
                <th width="80">Season Year</th>
                <th width="100">Style</th>
                <th width="80">File No</th>
                <th width="80">Internal Ref.</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
				<th width="80">Total Issue Qty</th>
                <th width="80">Total Rec.Qty</th>
                <th width="80">Balance</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1510px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));
				$numOfCountry = count($country);

				$color=array_unique(explode(",",$po_color_arr[$row[csf("id")]]));
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
					if($row[csf("total_set_qnty")]>1)
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

					foreach ($coun_ship_date_data as $pack_type=>$pack_data)
					{
						$country_ship_date = $coun_ship_date;
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
						$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date][$pack_type]['po_qnty'];
						$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date][$pack_type]['plan_cut_qnty'];

						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>','<?echo $country_ship_date?>');" >
                            <td width="30" align="center"><?php echo $i; ?></td>
                            <td width="60" align="center"><?php echo change_date_format($row[csf("shipment_date")]);?></td>
                            <td width="50" align="center"><?php echo $row[csf("job_no_prefix_num")];?></td>
                            <td width="100" title="<?=$color_name?$color_name:''?>" ><p><?php echo $row[csf("po_number")]; ?></p></td>
                            <td width="80"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>

							<td width="80"><p><?php echo $brand_arr[$row[csf("brand_id")]];?></p></td>
							<td width="80"><p><?php echo $season_arr[$row[csf("season_buyer_wise")]];?></p></td>
							<td width="80"><p><?php echo $row[csf("season_year")];?></p></td>

                            <td width="100"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
                            <td width="80"><p><?php echo $row[csf("file_no")]; ?></p></td>
                            <td width="80"><p><?php echo $row[csf("grouping")]; ?></p></td>
                            <td width="140"><p><?php  echo $garments_item[$grmts_item];?></p></td>
                            <td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
                            <td width="80" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp; </td>
							<td width="80" align="right">
							<?php
							 echo $total_issue_qty=$total_issue_qty_arr[$row[csf('id')]][$grmts_item][$country_id];
                             ?> &nbsp;
                           </td>
                            <td width="80" align="right">
							<?php
							 echo $total_cut_qty=$total_rec_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id];
                             ?> &nbsp;
                           </td>
                           <td width="80" align="right">
							<?php
                             $balance=$po_qnty-$total_cut_qty;
                             echo $balance;
                             ?>&nbsp;
                           </td>
                            <td><?php echo $company_arr[$row[csf("company_name")]];?> </td>
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
	if($dataArr[2]==0) $embel_name = "%%"; else $embel_name = $dataArr[2];
	$country_id =$dataArr[3];


	if($db_type==0)
	{
		$embel_name_cond="group_concat(c.emb_name)";
	}
	else if ($db_type==2)
	{
		$embel_name_cond="LISTAGG(c.emb_name,',') WITHIN GROUP ( ORDER BY c.emb_name)";
	}
	$res = sql_select("SELECT a.id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name , $embel_name_cond as emb_name from wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c where c.cons_dzn_gmts>0 and  a.job_id=b.id and a.id=$po_id and b.id=c.job_id group by a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name");

	 //print_r($res);die;
 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";

		echo "load_drop_down( 'requires/print_embro_receive_controller', '".$result[csf('emb_name')]."', 'load_drop_down_embel_name', 'embel_name_td' );\n";


  		$dataArray=sql_select("SELECT SUM(CASE WHEN production_type=2 THEN production_quantity END) as totalcutting,SUM(CASE WHEN production_type=3 THEN production_quantity ELSE 0 END) as totalprinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and embel_name like '$embel_name' and country_id='$country_id' and is_deleted=0");
 		foreach($dataArray as $row)
		{
 			echo "$('#txt_issue_qty').val('".$row[csf('totalcutting')]."');\n";
			echo "$('#txt_cumul_receive_qty').attr('placeholder','".$row[csf('totalprinting')]."');\n";
			echo "$('#txt_cumul_receive_qty').val('".$row[csf('totalprinting')]."');\n";
			$yet_to_produced = $row[csf('totalcutting')]-$row[csf('totalprinting')];
			echo "$('#txt_yet_to_receive').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_to_receive').val('".$yet_to_produced."');\n";
		}
  	}
 	exit();
}

if($action=="load_drop_down_embel_name")
{
	 //echo $data;die;
   /* echo create_drop_down( "cbo_embel_name", 200, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "load_drop_down( 'requires/print_embro_receive_controller', this.value+'**'+$('#hidden_po_break_down_id').val(), 'load_drop_down_embro_issue_type', 'embro_type_td'); get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#sewing_production_variable').val()+'**'+$('#styleOrOrderWisw').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(), 'color_and_size_level', 'requires/print_embro_receive_controller' ); show_list_view($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(),'show_dtls_listview','printing_production_list_view','requires/print_embro_receive_controller','setFilterGrid(\'tbl_search\',-1)'); ","",$data );// get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(), 'populate_data_from_search_popup', 'requires/print_embro_receive_controller' );*/


    echo create_drop_down( "cbo_embel_name", 200, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "load_drop_down( 'requires/print_embro_receive_controller', this.value+'**'+$('#hidden_po_break_down_id').val(), 'load_drop_down_emb_receive_type', 'emb_type_td' );  get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#sewing_production_variable').val()+'**'+$('#styleOrOrderWisw').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val()+'**'+$('#cbo_company_name').val()+'**'+$('#embro_production_variable').val()+'**'+$('#cbo_embel_type').val()+'**'+$('#country_ship_date').val(), 'color_and_size_level', 'requires/print_embro_receive_controller' ); show_list_view($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(),'show_dtls_listview','printing_production_list_view','requires/print_embro_receive_controller','setFilterGrid(\'tbl_search\',-1)'); ","" ,$data);


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
			$('#txtRejorderquantity_'+row_num).val( '' );

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
				var txtRejorderquantity = $(this).find('input[name="txtRejorderquantity[]"]').val();
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
						data_break_down+=cboBodyPart+'_'+txtorderquantity+'_'+txtRejorderquantity;
					}
					else
					{
						data_break_down+="="+cboBodyPart+'_'+txtorderquantity+'_'+txtRejorderquantity;
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
	<div align="center" style="width:580px;" >
		<form name="qntypopup_1"  id="qntypopup_1" autocomplete="off">
			<table class="rpt_table" width="580px" cellspacing="0" cellpadding="0" rules="all" id="tbl_share_details_entry">
				<thead>
					<th width="30">Sl.</th>
					<th width="240">Body Part</th>
					<th width="100">Quantity</th>
					<th width="100">Reject Quantity</th>
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
									<input type="text" id="txtorderquantity_<? echo $k;?>" name="txtorderquantity[]" class="text_boxes_numeric" style="width:87px"  value="<? echo number_format($data[1],4,'.',''); ?>" <? echo $disabled; ?> />
									<input type="hidden" id="hiddenOrderQuantity_<? echo $k;?>" name="hiddenOrderQuantity[]" class="text_boxes_numeric" style="width:70px" value="<? echo $data[1]; ?>"  />
								</td>
								<td>
									<input type="text" id="txtRejorderquantity_<? echo $k;?>" name="txtRejorderquantity[]" class="text_boxes_numeric" style="width:87px" value="<? echo number_format($data[2],4,'.',''); ?>" <? echo $disabled; ?> />
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
									<input type="text" id="txtorderquantity_1" name="txtorderquantity[]" class="text_boxes_numeric" style="width:87px" value="" />
									<input type="hidden" id="hiddenOrderQuantity_1" name="hiddenOrderQuantity[]" class="text_boxes_numeric" style="width:70px" value=""  />
								</td>
								<td>
									<input type="text" id="txtRejorderquantity_1" name="txtRejorderquantity[]" class="text_boxes_numeric" style="width:87px" value="" />
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
	$variableSettingsRej = $dataArr[7];
	$embel_type = $dataArr[8];
    $country_ship_date = $dataArr[9];
	if( $country_ship_date=='') $country_ship_date_cond=''; else $country_ship_date_cond=" and c.country_ship_date='$country_ship_date'";
	if( $country_ship_date=='') $country_ship_date_cond2=''; else $country_ship_date_cond2=" and a.country_ship_date='$country_ship_date'";

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

	if($variableSettings==1)
	{
		die;
	}

		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
		//#############################################################################################//
		// order wise - color level, color and size level

		//$variableSettings=2;

		if( $variableSettings==2 ) // color level
		{
			if($db_type==0)
			{

				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls, pro_garments_production_mst mst where mst.id=pdtls.mst_id and mst.embel_name='$embelName' and pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and mst.embel_type=$embel_type and cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and mst.embel_type=$embel_type and cur.production_type=3 and cur.is_deleted=0 ) as reject_qty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
			}
			else
			{
				$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type=2 and c.embel_name='$embelName' and c.embel_type=$embel_type then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=3 and c.embel_name='$embelName' and c.embel_type=$embel_type then b.production_qnty ELSE 0 END) as cur_production_qnty,
						sum(CASE WHEN c.production_type=3 and c.embel_name='$embelName' and c.embel_type=$embel_type then b.reject_qty ELSE 0 END) as reject_qty
						from wo_po_color_size_breakdown a
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
						left join pro_garments_production_mst c on c.id=b.mst_id
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.item_number_id, a.color_number_id";

			}

			$colorResult = sql_select($sql);

		}
		else if( $variableSettings==3 ) //color and size level
		{

			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls, pro_garments_production_mst mst where mst.id=pdtls.mst_id and mst.embel_name='$embelName' and pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/

			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.embel_name='$embelName' and b.embel_type=$embel_type and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");

			foreach($dtlsData as $row)
			{
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
			}
			//print_r($color_size_qnty_array);

			$sql = "SELECT a.color_order,a.id,a.size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
				from wo_po_color_size_breakdown a
				where a.po_break_down_id='$po_id' and a.item_number_id='$item_id'  $country_ship_date_cond2 and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order,a.size_order";


			$colorResult = sql_select($sql);
		}
		else // by default color and size level
		{
			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls, pro_garments_production_mst mst where mst.id=pdtls.mst_id and mst.embel_name='$embelName' and pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";// order by color_number_id,size_number_id*/


			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.embel_name='$embelName' and b.embel_type=$embel_type and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");

			foreach($dtlsData as $row)
			{
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
			}
			//print_r($color_size_qnty_array);

			$sql = "SELECT a.color_order,a.id,size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
				from wo_po_color_size_breakdown a
				where a.po_break_down_id='$po_id' $country_ship_date_cond2 and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order,a.size_order";

			$colorResult = sql_select($sql);
		}

		//$colorResult = sql_select($sql);
 		//print_r($sql);
		if($variableSettingsRej!=1)
		{
			$disable="";
		}
		else
		{
			$disable="disabled";
		}
  		$colorHTML="";
		$colorID='';
		$chkColor = array();
		$i=0;$totalQnty=0;
 		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onkeyup="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onkeyup="fn_colorRej_total('.($i+1).')  '.$disable.'"> <input type="text" name="colorSizeBundleQnty" id="colorSizeBundleQnty_'.$color[csf("color_number_id")].($i+1).'" placeholder="Bundle Qty"  class="text_boxes_numeric" style="width:60px"></td></tr>';
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


 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:45px" placeholder="'.($iss_qnty-$rcv_qnty).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:45px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:45px" value="'.$color[csf("order_quantity")].'" readonly disabled><input type="text" name="colorSizeBundleQnty" id="colorSizeBundleQnty_'.$color[csf("color_number_id")].($i+1).'" placeholder="Bundle Qty"  class="text_boxes_numeric" style="width:55px"></td></tr>';
			}

			$i++;
		}
		//echo $colorHTML;die;
		if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Rej.</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="Rej." class="text_boxes_numeric" style="width:60px" '.$disable.' ></th></tr></tfoot></table>'; }
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		//#############################################################################################//
		exit();
}


if($action=="show_dtls_listview")
{
	// echo "show_dtls_listview";die;
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$color_name=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	// $work_library=return_library_array("select id,booking_no from wo_booking_mst",'id','booking_no');


	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[3];
	$type = $dataArr[4];

	if($type==1) $embel_name="%%"; else $embel_name = $dataArr[2];
    ?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="960px" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="30">ID</th>
                <th width="140" align="center">Item Name</th>
                <th width="110" align="center">Country</th>
                <th width="80" align="center">Production Date</th>
                <th width="80" align="center">Production Qty</th>
                <th width="80" align="center">Reject Qnty</th>
                <th width="80" align="center">Embel Name</th>
                <th width="120" align="center">Serving Company</th>
                <th width="100" align="center">Location</th>
				<th width="100" align="center">Color</th>
                <th width="50" align="center">Color Type</th>
                <th width="50" align="center">Challan No</th>
				<th width="100" align="center">Work Order No</th>
            </thead>
        </table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="960px" class="rpt_table" id="tbl_search">
		<?php
           // echo "SELECT id, po_break_down_id,embel_name,production_source, item_number_id, country_id, production_date, production_quantity, reject_qnty, production_source, serving_company, location, challan_no,wo_order_id from pro_garments_production_mst where po_break_down_id='$po_id'  and production_type='3' and status_active=1 and is_deleted=0 order by id";die;

		   // and embel_name like '$embel_name'
 			$i=1;
			$total_production_qnty=0;
			$sqlResult = sql_select("SELECT id, po_break_down_id,embel_name,production_source, item_number_id, country_id, production_date, production_quantity, reject_qnty, production_source, serving_company, location, challan_no,wo_order_id from pro_garments_production_mst where po_break_down_id='$po_id'  and production_type='3' and status_active=1 and is_deleted=0 order by id");//and item_number_id='$item_id' and country_id='$country_id' change in 29/10/2019 for libas
			$sql_color_type = sql_select("SELECT a.id,b.color_type_id from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id='$po_id' and b.production_type=3 and a.production_type=3 and b.status_active=1 and b.is_deleted=0  group by a.id,b.color_type_id ");
			$order_cond = array();
            foreach($sqlResult as $result)
			{
				$order_cond[$result['PO_BREAK_DOWN_ID']]  = $result['PO_BREAK_DOWN_ID'];
			}
			$order_cond_id = implode(",",$order_cond);
			$newSql = "SELECT b.mst_id, a.PO_BREAK_DOWN_ID , a.ITEM_NUMBER_ID , a.COUNTRY_ID , a.COLOR_NUMBER_ID FROM WO_PO_COLOR_SIZE_BREAKDOWN a,pro_garments_production_dtls b WHERE a.PO_BREAK_DOWN_ID in ($order_cond_id) and b.COLOR_SIZE_BREAK_DOWN_ID = a.id and b.status_active=1 AND b.is_deleted=0";
			$mysqli_ex_query = sql_select($newSql);

			$Color_Item_arr = array();
			foreach($mysqli_ex_query as $data)
			{
			   $Color_Item_arr[$data['MST_ID']][$data['COLOR_NUMBER_ID']] = $color_name[$data['COLOR_NUMBER_ID']]; 
			}
			foreach($sql_color_type as $k=>$v)
			{
				$mst_id_wise_color[$v[csf("id")]]=$color_type[$v[csf("color_type_id")]];
			}
			//$color_type_id=$sql_color_type[0][csf("color_type_id")];
			$po_id_arr = array();
			$wo_order_id_arr = array();
			foreach($sqlResult as $v)
			{
				$po_id_arr[$v['PO_BREAK_DOWN_ID']] = $v['PO_BREAK_DOWN_ID'];
				$wo_order_id_arr[$v['WO_ORDER_ID']] = $v['WO_ORDER_ID'];
			}
			// ===================== getting WORK ORDER nO ==================
			$order_id_cond = where_con_using_array($po_id_arr,0,"b.po_break_down_id");
			//echo $order_id_cond;die;
			if(count($wo_order_id_arr)>0)
			{ 
				$wo_order_ids = implode(",", $wo_order_id_arr);
				$wo_order_id  = rtrim($wo_order_ids, ',');
				// print_r($data);die;
				// $wo_order_id_cond = where_con_using_array($wo_order_id_arr,0,"a.id");
				$wo_order_id_cond = " and a.id in($wo_order_id)";
				//echo $wo_order_id_cond;die;
				$sql = "SELECT a.id ,a.booking_no from wo_booking_mst a where  a.status_active=1 and a.is_deleted=0 $wo_order_id_cond ";
				//echo $sql;die;
				$sql_result=sql_select($sql);
				$wo_order_array=array();
				foreach($sql_result as $v)
				{
					$wo_order_array[$v["ID"]]=$v["BOOKING_NO"];
				}
			}

			foreach($sqlResult as $selectResult){

				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
                $total_production_qnty+=$selectResult[csf('production_quantity')];
				//
 		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="text-decoration:none; cursor:pointer"   >
            	<td width="30" align="center"><input type="checkbox" id="tbl_<? echo $i; ?>"  onClick="fnc_checkbox_check(<? echo $i; ?>);"  />&nbsp;&nbsp;&nbsp; <? //echo $i; ?>


                   <input type="hidden" id="mstidall_<? echo $i; ?>" value="<? echo $selectResult[csf('id')]; ?>" style="width:30px"/>
                   <input type="hidden" id="emblname_<? echo $i; ?>" name="emblname[]"   width="30" value="<? echo $selectResult[csf('embel_name')]; ?>" />
                    <input type="hidden" id="productionsource_<? echo $i; ?>"   width="30" value="<? echo $selectResult[csf('production_source')]; ?>" />
                </td>
				<td width="30" align="center" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_embro_receive_controller');" ><? echo $selectResult[csf('id')]; ?>

                </td>
                <td width="110" align="center" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_embro_receive_controller');" ><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></td>
                <td width="110" align="center" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_embro_receive_controller');" ><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                <td width="80" align="center" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_embro_receive_controller');" ><? echo change_date_format($selectResult[csf('production_date')]); ?></td>
                <td width="80" align="center" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_embro_receive_controller');" ><?  echo $selectResult[csf('production_quantity')]; ?></td>
                <td width="80" align="center" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_embro_receive_controller');" ><?  echo $selectResult[csf('reject_qnty')]; ?></td>
                <td width="80" align="center" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_embro_receive_controller');" ><?  echo $emblishment_name_array[$selectResult[csf('embel_name')]]; ?></td>
				<?
                       	$source= $selectResult[csf('production_source')];
					   	if($source==1) $serving_company= $company_arr[$selectResult[csf('serving_company')]];
						else $serving_company= $supplier_arr[$selectResult[csf('serving_company')]];
                 ?>
                <td width="120" align="center" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_embro_receive_controller');" ><p><? echo $serving_company; ?></p></td>
                <td width="100" align="center" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_embro_receive_controller');" ><p><? echo $location_arr[$selectResult[csf('location')]]; ?></p></td>
				<td width="100" align="center" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_embro_receive_controller');" ><p><? echo implode(',',$Color_Item_arr[$result['ID']]); ?></p></td>
				
                <td width="50" align="center" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_embro_receive_controller');" ><p><? echo $mst_id_wise_color[$selectResult[csf("id")]]; ?></p></td>
                <td width="50" align="center" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_embro_receive_controller');" ><p><? echo $selectResult[csf('challan_no')]; ?></p></td>
				<td width="100" align="center" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/print_embro_receive_controller');"><p><? echo $wo_order_array[$selectResult[csf('wo_order_id')]]; ?></p></td>


			</tr>

			<?
			$i++;
			}
			?>

            <!--<tfoot>
            	<tr>
                	<th colspan="3"></th>
                    <th><!? echo $total_production_qnty; ?></th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>-->

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
    <table cellspacing="0" cellpadding="0" border="1" style="margin-left:10px !important" rules="all" width="380" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="100">Item Name</th>
            <th width="80">Country</th>
            <th width="60">Shipment Date</th>
            <th width="65">Order Qty.</th>
            <th>Rcv. Qty.</th>
        </thead>
		<?
		$issue_qnty_arr=sql_select("select a.po_break_down_id, a.item_number_id, a.country_id, b.production_qnty as cutting_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$data' and a.production_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$issue_data_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]+=$row[csf("cutting_qnty")];
		}
		$i=1;
		// $sqlResult =sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		$sqlResult = sql_select("SELECT po_break_down_id, item_number_id, country_id, country_ship_date,pack_type, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty, max(cutup) as cutup from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active in(1,2,3) and is_deleted=0 group by po_break_down_id, item_number_id, country_id,country_ship_date,pack_type order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$issue_qnty=$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>,'<?=$row[csf('country_ship_date')]; ?>');">
				<td width="20"><? echo $i; ?></td>
				<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
				<td width="60" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right" width="65"><?php  echo $row[csf('order_qnty')]; ?></td>
                <td align="right"><?php  echo $issue_qnty; ?></td>
			</tr>
		<?
			$i++;
		}
		?>
	</table>
	<?
	exit();
}


if($action=="populate_receive_form_data")
{
	$company_id_sql=sql_select("SELECT company_id,po_break_down_id,item_number_id,country_id FROM pro_garments_production_mst WHERE id=$data and status_active=1 and is_deleted=0 ");
	$company_id=$company_id_sql[0][csf("company_id")];
	$po_break_down_id = $company_id_sql[0][csf('po_break_down_id')];
	$item_number_id = $company_id_sql[0][csf('item_number_id')];
	$country_id = $company_id_sql[0][csf('country_id')];

	// ======================= get country ship date and update prod mst table ====================
	$con = connect();
	$sql_colsize ="SELECT a.mst_id,b.pack_type,b.country_ship_date from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.color_size_break_down_id=b.id and a.mst_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.po_break_down_id='$po_break_down_id' and b.item_number_id='$item_number_id' and b.country_id='$country_id'";
	// echo $sql_colsize;die;
	$colsize_res = sql_select($sql_colsize);
	
	$country_ship_date = $colsize_res[0][csf('country_ship_date')];
	$pack_type = $colsize_res[0][csf('pack_type')];
	$update_shidate = execute_query("UPDATE pro_garments_production_mst set country_ship_date='$country_ship_date' WHERE id=$data");
	// echo $update_shidate;die;
	if($update_shidate)
	{		
		oci_commit($con); 
	}
	else
	{
		oci_rollback($con);
	}
	disconnect($con);
	/* 
	@end
	*/

	 
	
	//production type=2 come from array
	$sqlResult =sql_select("SELECT country_ship_date, id,company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_source, production_type, entry_break_down_type, break_down_type_rej, production_hour, sewing_line, supervisor, carton_qty, remarks, floor_id, alter_qnty, reject_qnty, total_produced, yet_to_produced, wo_order_id,currency_id,exchange_rate,rate,sending_location,sending_company,body_part_info,is_posted_account , get_entry_date,get_entry_no from pro_garments_production_mst where id='$data' and production_type='3' and status_active=1 and is_deleted=0 order by id");

	$po_break_down_id =  $sqlResult[0][csf('po_break_down_id')];
  
	$country_ship_date = $sqlResult[0][csf('country_ship_date')];
	//echo 'fff'. $country_ship_date;die;
	if($country_ship_date=='') $country_ship_date_cond =""; else $country_ship_date_cond =" and a.country_ship_date='$country_ship_date'";

	//echo "date =" .$country_ship_date ;
	$color_type_val=sql_select("SELECT b.color_type_id  from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=3 and b.production_type=3 and a.status_active=1 and b.status_active=1 and  a.id='$data' group by b.color_type_id ");


	$file_input = '';
	$photo_sql = sql_select("SELECT id,master_tble_id,image_location FROM COMMON_PHOTO_LIBRARY where FORM_NAME='embellishment_receive_entry' and master_tble_id='$po_break_down_id'");
 
	if(count($photo_sql)>0){
		$file_input = 1;
	}
		
	echo "$('#txt_file').val('".$file_input."');\n";

  	//echo "sdfds".$sqlResult;die; cbo_work_order_booking_no cbo_work_order_booking_no
	foreach($sqlResult as $result)
	{
		echo "$('#txt_receive_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/print_embro_receive_controller', ".$result[csf('production_source')].", 'load_drop_down_emb_receive', 'emb_company_td' );\n";
		echo "$('#cbo_emb_company').val('".$result[csf('serving_company')]."');\n";
		echo "$('#hid_body_part').val('".$result[csf('body_part_info')]."');\n";
		echo "$('#txt_body_part').val('".$result[csf('body_part_info')]."');\n";
		echo "load_drop_down( 'requires/print_embro_receive_controller', ".$result[csf('po_break_down_id')].", 'load_drop_down_color_type', 'color_type_td' );\n";
		echo "$('#cbo_color_type').val('".$color_type_val[0][csf("color_type_id")]."');\n";
		echo "$('#get_entry_no').val('".$result[csf('get_entry_no')]."');\n";
		echo "$('#get_entry_date').val('".$result[csf('get_entry_date')]."');\n";

		$location_company=0;
		if($result[csf('production_source')]==1)
		{
			$location_company=$result[csf('serving_company')];
		}
		else
		{
			$location_company=$result[csf('company_id')];
		}
		echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";
		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";
		echo "load_drop_down( 'requires/print_embro_receive_controller',".$location_company.", 'load_drop_down_location', 'location_td' );";
		echo "$('#cbo_location').val('".$result[csf('location')]."');\n";
		echo "load_drop_down( 'requires/print_embro_receive_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_embel_name').val('".$result[csf('embel_name')]."');\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";
		echo "$('#cbo_sending_location').val('".$result[csf('sending_location')]."*".$result[csf('sending_company')]."');\n";
		echo "load_drop_down( 'requires/print_embro_receive_controller', '".$result[csf('embel_name')].'**'.$result[csf('po_break_down_id')]."', 'load_drop_down_emb_receive_type', 'emb_type_td' );\n";
		echo "$('#cbo_embel_type').val('".$result[csf('embel_type')]."');\n";

 		echo "$('#txt_receive_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_reject_qty').val('".$result[csf('reject_qnty')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";

 

	
		

  		
 

		$bookingId = $result[csf('wo_order_id')];
		$getBookingNo = sql_select("SELECT id, booking_no from wo_booking_mst where  status_active=1 and is_deleted=0 and id='$bookingId'");
		echo "$('#cbo_work_order_booking_no').val('".$getBookingNo[0]['BOOKING_NO']."');\n";

		echo "$('#cbo_work_order').val('".$result[csf('wo_order_id')]."');\n";
		
		if($result[csf('production_source')]==3)
		{
			//echo "load_drop_down( 'requires/print_embro_receive_controller', '".$result[csf('company_id')]."_".$result[csf('serving_company')]."_".$result[csf('po_break_down_id')]."_".$result[csf('embel_name')]."_".$result[csf('item_number_id')]."', 'load_drop_down_workorder', 'workorder_td' );\n";

			// $bookingId = $result[csf('wo_order_id')];
			// $getBookingNo = sql_select("SELECT id, booking_no from wo_booking_mst where  status_active=1 and is_deleted=0 and id='$bookingId'");
			// print_r($getBookingNo); exit();
			// echo "$('#cbo_work_order_booking_no').val('".$getBookingNo[0]['BOOKING_NO']."');\n";
			
			echo "$('#cbo_work_order').val('".$result[csf('wo_order_id')]."');\n";
			echo "$('#hidden_currency_id').val('".$result[csf('currency_id')]."');\n";
			echo "$('#hidden_exchange_rate').val('".$result[csf('exchange_rate')]."');\n";
			echo "$('#hidden_piece_rate').val('".$result[csf('rate')]."');\n";
			$rate_string=$result[csf('rate')]." ".$currency[$result[csf('currency_id')]];
			if(trim($rate_string)!="")
			{
				$rate_string="Work Order Rate ".$rate_string." /Pcs";
				echo "$('#workorder_rate_td').text('".$rate_string."');\n";
			}
			else
			{
				echo "$('#workorder_rate_td').text('');\n";
			}
		}

		$embel_type = $result[csf('embel_type')];

		$dataArray=sql_select("select SUM(CASE WHEN production_type=2 THEN production_quantity END) as totalCutting,SUM(CASE WHEN production_type=3 THEN production_quantity ELSE 0 END) as totalPrinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and embel_name=".$result[csf('embel_name')]." and embel_type=$embel_type and country_id=".$result[csf('country_id')]." and is_deleted=0");
 		foreach($dataArray as $row)
		{
			echo "$('#txt_issue_qty').attr('placeholder','".$row[csf('totalCutting')]."');\n";
 			echo "$('#txt_issue_qty').val('".$row[csf('totalCutting')]."');\n";
			echo "$('#txt_cumul_receive_qty').val('".$row[csf('totalPrinting')]."');\n";
			$yet_to_produced = $row[csf('totalCutting')]-$row[csf('totalPrinting')];
			echo "$('#txt_yet_to_receive').val('".$yet_to_produced."');\n";
		}


		$defect_sql=sql_select("SELECT id, po_break_down_id, defect_type_id, defect_point_id, defect_qty from pro_gmts_prod_dft where mst_id='".$result[csf('id')]."' and status_active=1 and is_deleted=0 and embel_name=".$result[csf('embel_name')]." and embel_type=$embel_type and production_type='3'");
		foreach($defect_sql as $dft_row)
		{
			if($dft_row[csf('defect_type_id')]==1)
			{
				if($Reject_save_data=="") $Reject_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $Reject_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($Rejectpoint_id=="") $Rejectpoint_id=$dft_row[csf('defect_point_id')]; else $Rejectpoint_id.=','.$dft_row[csf('defect_point_id')];
				$RejectType_id=$dft_row[csf('defect_type_id')];
			}
		}
		echo "$('#save_dataReject').val('".$Reject_save_data."');\n";
		echo "$('#allReject_defect_id').val('".$Rejectpoint_id."');\n";
		echo "$('#defectReject_type_id').val('".$RejectType_id."');\n";


		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
		echo "$('#txt_sys_chln').val('".$result[csf('id')]."');\n";
		//echo "$('#txt_mst_id_all').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_receive_print_embroidery_entry',1,1);\n";


		$is_posted_account = $result[csf('is_posted_account')];
		echo "$('#is_posted_account').val(" . $is_posted_account . ");\n";
		if($is_posted_account==1)
		{
		 	echo "$('#posted_account_msg').text('Already Posted In Accounting');\n";
		}

		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		$variableSettingsRej = $result[csf('break_down_type_rej')];
		if( $variableSettings!=1 ) // gross level
		{
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];

			$sql_dtls = sql_select("select color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id, a. bundle_qty from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
			foreach($sql_dtls as $row)
			{
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
				$rejectArr[$index] = $row[csf('reject_qty')];
				$bundleArr[$index] = $row[csf('bundle_qty')];
				$rejectArr2[$index][$row[csf('color_size_break_down_id')]] = $row[csf('reject_qty')];
			}

			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{

					$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as reject_qty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
				}
				else
				{
					$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
							sum(CASE WHEN c.production_type=2 then b.production_qnty ELSE 0 END) as production_qnty,
							sum(CASE WHEN c.production_type=3 then b.production_qnty ELSE 0 END) as cur_production_qnty,
							sum(CASE WHEN c.production_type=3 then b.reject_qty ELSE 0 END) as reject_qty
							from wo_po_color_size_breakdown a
							left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
							left join pro_garments_production_mst c on c.id=b.mst_id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

				}

			}
			else if( $variableSettings==3 ) //color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/


				$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				}

				$sql = "SELECT a.color_order,a.id,size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
					from wo_po_color_size_breakdown a
					where a.po_break_down_id='$po_id' $country_ship_date_cond and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order,a.size_order";


			}
			else // by default color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/


				$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				}
				//print_r($color_size_qnty_array);

				$sql = "SELECT  a.color_order,a.id,a.size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
					from wo_po_color_size_breakdown a
					where a.po_break_down_id='$po_id' $country_ship_date_cond and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order,a.size_order";


			}
			//echo $sql;
			if($variableSettingsRej!=1)
			{
				$disable="";
			}
			else
			{
				$disable="disabled";
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
					$amount = $amountArr[$color[csf("color_number_id")]];
					$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
					$bundle_qnty=$bundleArr[$color[csf("color_number_id")]];

					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onkeyup="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onkeyup="fn_colorRej_total('.($i+1).')"> <input type="text" name="colorSizeBundleQnty" id="colorSizeBundleQnty_'.$color[csf("color_number_id")].($i+1).'" placeholder="Bundle Qty" value="'.$bundle_qnty.'"  class="text_boxes_numeric" style="width:80px"> </td></tr>';
					$totalQnty += $amount;
					$totalRejQnty += $rejectAmt;
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
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"> <div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div> <table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
					//$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
					$rej_qnty=$rejectArr2[$index][$color[csf('id')]];

					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:60px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onkeyup="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:40px" placeholder="Rej. Qty" onkeyup="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" value="'.$color[csf("order_quantity")].'" readonly disabled> <input type="text" name="colorSizeBundleQnty" id="colorSizeBundleQnty_'.$color[csf("color_number_id")].($i+1).'" placeholder="Bundle Qty" value="'.$bundle_qnty.'"  class="text_boxes_numeric" style="width:60px"> </td></tr>';
					$colorWiseTotal += $amount;
				}
				$i++;
			}
			//echo $colorHTML;die;
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		}//end if condtion
		//#############################################################################################//
	}
 	exit();
}

if($action=="file_upload")
{ 
	header("Content-Type: application/json");
	$filename = time().$_FILES['file']['name'];
	$location = "../../file_upload/".$filename;
	$uploadOk = 1;
	if(empty($mst_id))
	{
		$mst_id=$_GET['mst_id'];
	}
	if(move_uploaded_file($_FILES['file']['tmp_name'], $location))
	{
		$uploadOk = 1;
	}
	else
	{
		$uploadOk=0;
	}
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$id=return_next_id( "id","COMMON_PHOTO_LIBRARY", 1 ) ;
	$data_array .="(".$id.",".$mst_id.",'embellishment_receive_entry','file_upload/".$filename."','2','".$filename."','".$pc_date_time."')";
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name,insert_date";
	$rID=sql_insert("COMMON_PHOTO_LIBRARY",$field_array,$data_array,1);

	if($db_type==0)
	{
		if($rID==1 && $uploadOk==1)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$mst_id;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID==1 && $uploadOk==1)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$rID."**".$uploadOk."**INSERT INTO COMMON_PHOTO_LIBRARY(".$field_array.") VALUES ".$data_array;
		}
	}
	disconnect($con);
	die;
}

//pro_garments_production_mst
if ($action=="save_update_delete")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo $txt_receive_date; exit(); cbo_work_order
	$get_entry_date = $txt_receive_date;
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
	// die('10**'.$wip_valuation_for_accounts);
	$cost_of_fab = 0;
	$cutting_oh = 0;
	$cost_per_pcs = 0;


	// ========================== exchange rate ===================================
	$sql = "SELECT a.exchange_rate,b.costing_per_id,c.id as po_id from WO_PRE_COST_MST a,WO_PRE_COST_DTLS b, WO_PO_BREAK_DOWN c where a.job_id=b.job_id and b.job_id=c.job_id and a.job_id=c.job_id and c.id=$hidden_po_break_down_id and a.status_active=1 and b.status_active=1";
	$res = sql_select($sql);
	$exchange_rate_array = array();
	foreach ($res as $v)
	{
		$exchange_rate_array[$v['PO_ID']]['exchange_rate'] = $v['EXCHANGE_RATE'];
		$exchange_rate_array[$v['PO_ID']]['costing_per_id'] = $v['COSTING_PER_ID'];
	}
	$hidden_po_id = str_replace("'","",$hidden_po_break_down_id);
	$costingPerQty=0;
	if($exchange_rate_array[$hidden_po_id]['costing_per_id']==1) $costingPerQty=12;
	elseif($exchange_rate_array[$hidden_po_id]['costing_per_id']==2) $costingPerQty=1;
	elseif($exchange_rate_array[$hidden_po_id]['costing_per_id']==3) $costingPerQty=24;
	elseif($exchange_rate_array[$hidden_po_id]['costing_per_id']==4) $costingPerQty=36;
	elseif($exchange_rate_array[$hidden_po_id]['costing_per_id']==5) $costingPerQty=48;
	else $costingPerQty=0;

	if($wip_valuation_for_accounts==1)
	{
		$condition= new condition();
		$condition->po_id_in($hidden_po_break_down_id);
		$condition->init();
		$wash = new wash($condition);
		//  echo "10**".$wash->getQuery(); die;
		$wash_budget_qty_arr=$wash->getQtyArray_by_orderAndEmbname();
		$wash_budget_amount_arr=$wash->getAmountArray_by_orderAndEmbname();
		// echo "10**<pre>";print_r($wash_budget_amount_arr);die;

		/* ================================= get fabric cost =================================== */
		// $sql = "SELECT po_break_down_id as po_id,item_number_id,country_id,cost_of_fab_per_pcs,cut_oh_per_pcs,cost_per_pcs from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=2 and embel_name=3 and po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name";

		$sql = "SELECT c.po_break_down_id as po_id,c.item_number_id,c.color_number_id,b.cost_of_fab_per_pcs,b.cut_oh_per_pcs,b.cost_per_pcs,b.bundle_no,b.production_qnty,(b.cost_per_pcs*b.production_qnty) as amount from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and b.production_type=2 and a.embel_name=3 and c.po_break_down_id=$hidden_po_break_down_id and c.item_number_id=$cbo_item_name order by a.production_type asc";
		// echo "10**".$sql;die;
		$res = sql_select($sql);
		foreach ($res as $v)
		{
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_of_fab_per_pcs'] = $v['COST_OF_FAB_PER_PCS'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cut_oh_per_pcs'] = $v['CUT_OH_PER_PCS'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['trims_cost_per_pcs'] = $v['TRIMS_COST_PER_PCS'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['cost_per_pcs'] = $v['COST_PER_PCS'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['prod_qty'] += $v['PRODUCTION_QNTY'];
			$fab_cost_array[$v['PO_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']]['amount'] += $v['AMOUNT'];
		}


		$wash_rate = ($wash_budget_qty_arr[$hidden_po_id][3]) ? $wash_budget_amount_arr[$hidden_po_id][3] / $wash_budget_qty_arr[$hidden_po_id][3] : 0;
		// echo "10**".$hidden_po_id."==". $wash_budget_amount_arr[$hidden_po_id][3] ."/". $wash_budget_qty_arr[$hidden_po_id][3]."<br>";die;
		$wash_rate = ($wash_rate/$costingPerQty)*$exchange_rate_array[$hidden_po_id]['exchange_rate'];
		$wash_rate = fn_number_format($wash_rate,$dec_place[3],'.','');/*
		$wash_cost = $qty*$cost_per_pcs;
		$wash_charge = $qty*$wash_rate;
		$cost_per_pcs = $cost_per_pcs + $wash_rate;
		$cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.',''); */
		// echo "10**".$cost_per_pcs ."+". $wash_rate."==".$hidden_po_id."<br>";die;
		$color_data_array = array();
		if(str_replace("'","",$sewing_production_variable)==2)
		{
			$rowEx = array_filter(explode("**",$colorIDvalue));
			foreach ($rowEx as $v)
			{
				$colorSizeNumberIDArr = explode("*",$v);
				$color_data_array[$colorSizeNumberIDArr[0]]['ok']+=$colorSizeNumberIDArr[1];
			}
			// ===========================
			$rowEx = array_filter(explode("**",$colorIDvalueRej));
			foreach ($rowEx as $v)
			{
				$colorSizeNumberIDArr = explode("*",$v);
				$color_data_array[$colorSizeNumberIDArr[0]]['rej']+=$colorSizeNumberIDArr[1];
			}
		}

		if(str_replace("'","",$sewing_production_variable)==3)
		{
			$rowEx = array_filter(explode("***",$colorIDvalue));
			foreach ($rowEx as $v)
			{
				$colorSizeNumberIDArr = explode("*",$v);
				$color_data_array[$colorSizeNumberIDArr[1]]['ok']+=$colorSizeNumberIDArr[2];
			}
			// ===========================
			$rowEx = array_filter(explode("***",$colorIDvalueRej));
			foreach ($rowEx as $v)
			{
				$colorSizeNumberIDArr = explode("*",$v);
				$color_data_array[$colorSizeNumberIDArr[1]]['rej']+=$colorSizeNumberIDArr[2];
			}
		}
	}
	// echo "10**";print_r($color_data_array);die;

	// ======================== emb workorder rate =============================
	if(str_replace("'","",$cbo_work_order))
	{
		$embSql = "SELECT b.PO_BREAK_DOWN_ID,b.GMT_ITEM,b.GMTS_COLOR_ID,(b.RATE*b.EXCHANGE_RATE) as rate from WO_BOOKING_MST a,WO_BOOKING_DTLS b, wo_pre_cost_embe_cost_dtls c where a.booking_no=b.booking_no and c.id=b.pre_cost_fabric_cost_dtls_id and b.PO_BREAK_DOWN_ID=$hidden_po_break_down_id and b.GMT_ITEM=$cbo_item_name and c.EMB_NAME=$cbo_embel_name and b.status_active=1 and b.is_deleted=0 and a.id=$cbo_work_order";
		// echo "10**$embSql";die;
		$embRes = sql_select($embSql);
		$emb_wo_rate_array = array();
		foreach ($embRes as $v)
		{
			if($v['RATE']!=0)
			{
				$emb_wo_rate_array[$v['PO_BREAK_DOWN_ID']][$v['GMT_ITEM']] = number_format(($v['RATE']/$costingPerQty),8);
			}
		}
	}

	if ($operation==0) // Insert Here----------------------------------------------------------
	{ 
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;} cbo_work_order_booking_no

		$color_sizeID_arr=sql_select( "select id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and status_active=1 and is_deleted=0 order by id" );
		$colSizeID_arr=array();$i=0;
		foreach($color_sizeID_arr as $val){
			$colSizeID_arr[$i++]=$val[csf("id")];
		}

		$cbo_sending_location = explode("*", str_replace("'", "", $cbo_sending_location));
		$sending_location     = $cbo_sending_location[0];
		$sending_company      = $cbo_sending_location[1];

		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		$id= return_next_id_by_sequence("pro_gar_production_mst_seq", "pro_garments_production_mst", $con);
		//3 means print receive array
 		$field_array1="id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, entry_break_down_type, break_down_type_rej, remarks, floor_id, reject_qnty, total_produced, yet_to_produced,wo_order_id,currency_id,exchange_rate,rate,amount, inserted_by, insert_date,sending_location,sending_company,cost_of_fab_per_pcs,cost_per_pcs,body_part_info,get_entry_date,get_entry_no,country_ship_date";
		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_receive_qty);}
		else {$amount="";}
		$data_array1="(".$id.",".$cbo_company_name.",".$garments_nature.",".$txt_challan.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_emb_company.",".$cbo_location.",".$cbo_embel_name.",".$cbo_embel_type.",".$txt_receive_date.",".$txt_receive_qty.",3,".$sewing_production_variable.",".$embro_production_variable.",".$txt_remark.",".$cbo_floor.",".$txt_reject_qty.",".$txt_cumul_receive_qty.",".$txt_yet_to_receive.",".$cbo_work_order.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."',".$user_id.",'".$pc_date_time."','".$sending_location."','".$sending_company."','".$wash_rate."','".$cost_per_pcs."',".$hid_body_part.",".$get_entry_date.",".$get_entry_no.",".$country_ship_date.")";
		// echo "10** insert into pro_garments_production_mst($field_array1)values".$data_array1;die;
 		//$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);

		//echo $data_array1;die;
		// pro_garments_production_dtls table entry here ----------------------------------/// cbo_file



		$embelName=str_replace("'", "", $cbo_embel_name);


		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b
										where a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and b.embel_name='$embelName' and a.status_active=1 and a.production_type in(2,3) group by a.color_size_break_down_id");
		$color_pord_data=array();
		foreach($dtlsData as $row)
		{
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}


		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty, bundle_qty, color_type_id,cost_of_fab_per_pcs,cost_per_pcs,wo_rate_pcs";

		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name   and status_active=1 and is_deleted=0  order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}

			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
			$rowExRej = array_filter(explode("**",$colorIDvalueRej));
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorSizeRejIDArr = explode("*",$valR);

				//echo $colorSizeRejIDArr[0]; die;
				$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
			}

			$rowExBundle = array_filter(explode("**",$colorBundleVal));
			foreach($rowExBundle as $rowR=>$valR)
			{
				$colorSizeBunIDArr = explode("*",$valR);
				//echo $colorSizeBunIDArr[0]; die;
				$BunQtyArr[$colorSizeBunIDArr[0]]=$colorSizeBunIDArr[1];
			}

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
							echo "35**Embellishment Receive Quantity Not Over Embellishment Issue Qnty";
							//check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}*/
				$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_of_fab_per_pcs'];
				$cut_oh_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cut_oh_per_pcs'];
				$trims_cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['trims_cost_per_pcs'];
				// $cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_per_pcs'];
				$emb_wo_rate = $emb_wo_rate_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)];

				$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['prod_qty'];
				$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['amount'];

				$cost_per_pcs = $amount/$prod_qty;
				// $cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.','');

				$wash_cost = $color_data_array[$colorSizeNumberIDArr[0]]['ok']*$cost_per_pcs;
				$wash_charge = $color_data_array[$colorSizeNumberIDArr[0]]['ok']*$wash_rate;
				$cost_per_pcs = $cost_per_pcs + $wash_rate;
				$cost_per_pcs = fn_number_format($cost_per_pcs,$dec_place[3],'.','');

				if($colSizeID_arr[$colorSizeNumberIDArr[0]])
				{
					//3 for Receive Print / Emb. Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );

					if($j==0)$data_array = "(".$dtls_id.",".$id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$BunQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.",'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
					else $data_array .= ",(".$dtls_id.",".$id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$BunQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.",'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
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
			$rowExRej = array_filter(explode("***",$colorIDvalueRej));
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				$sizeID = $colorAndSizeRej_arr[0];
				$colorID = $colorAndSizeRej_arr[1];
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $sizeID.$colorID;
				$rejQtyArr[$index]=$colorSizeRej;
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

 			$rowEx = array_filter(explode("***",$colorIDvalue));
 			//echo "10**".$colorIDvalue;
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
							echo "35**Embellishment Receive Quantity Not Over Embellishment Issue Qnty";
							//check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}*/
				$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_of_fab_per_pcs'];
				$cut_oh_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cut_oh_per_pcs'];
				$trims_cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['trims_cost_per_pcs'];
				// $cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_per_pcs'];
				$emb_wo_rate = $emb_wo_rate_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)];

				$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['prod_qty'];
				$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['amount'];

				$cost_per_pcs = $amount/$prod_qty;
				// $cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.','');
				$wash_cost = $color_data_array[$colorID]['ok']*$cost_per_pcs;
				$wash_charge = $color_data_array[$colorID]['ok']*$wash_rate;
				$cost_per_pcs = $cost_per_pcs + $wash_rate;
				$cost_per_pcs = fn_number_format($cost_per_pcs,$dec_place[3],'.','');

				if($colSizeID_arr[$index]!="")
				{
					//3 for Receive Print / Emb. Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

					if($j==0)$data_array = "(".$dtls_id.",".$id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."','".$BunQtyArr[$index]."',".$cbo_color_type.",'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
					else $data_array .= ",(".$dtls_id.",".$id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."','".$BunQtyArr[$index]."',".$cbo_color_type.",'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
					//$dtls_id=$dtls_id+1;
	 				$j++;
	 			}
	 			else
	 			{
					// echo "222222222222";
	 				echo "420**";die();
	 			}
			}
		}

		// Reject Part
		$defectReject=true;
		$data_array_defect_reject="";
		$save_dataReject=explode(",",str_replace("'","",$save_dataReject));
		if(count($save_dataReject)>0 && str_replace("'","",$save_dataReject)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$field_array_defectsp="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty,embel_name, inserted_by, insert_date";
 			$defectReject_array=array();
			for($i=0;$i<count($save_dataReject);$i++)
			{
				$order_dtls=explode("**",$save_dataReject[$i]);
				$defect_update_id=$order_dtls[0];
				$defectsp_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectsp_point_id,$defectReject_array) )
				{
					$defectReject_array[$defectsp_point_id]=$defect_qnty;
				}
				else
				{
					$defectReject_array[$defectsp_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defectReject_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defect_reject.=",";
				//if( $dftSp_id=="" ) $dftSp_id=return_next_id("id", "pro_gmts_prod_dft", 1); else $dftSp_id = $dftSp_id+1;
				$dftSp_id=return_next_id_by_sequence("PRO_GMTS_PROD_DFT_SEQ", "pro_gmts_prod_dft", $con);
				$defectspPointId=$keysp;
				$defectsp_qty=$valsp;
				$data_array_defect_reject.="(".$dftSp_id.",".$id.",3,".$hidden_po_break_down_id.",".$defectReject_type_id.",".$defectspPointId.",'".$defectsp_qty."',$cbo_embel_name,".$user_id.",'".$pc_date_time."')";
				$i++;
			}

			if($data_array_defect_reject!="")
			{
				// echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectsp.") VALUES ".$data_array_defect_reject.""; die;
				//echo "DELETE FROM pro_gmts_prod_dft WHERE mst_id='$id' and defect_type_id=2";die;
				// $query4=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$id and defect_type_id=3 and production_type=5",1);
				$defectReject=sql_insert("pro_gmts_prod_dft",$field_array_defectsp,$data_array_defect_reject,1);
			}
		}

		//echo "INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array;die;
		$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);

		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}

		// $mst_id = $id;
		// $id=return_next_id("id", "COMMON_PHOTO_LIBRARY", 1);
		// $data_array2 .="(".$id.",".$mst_id.",'embellishment_receive_entry','file_upload/".$filename."','2','".$filename."','".$pc_date_time."')";
		// $field_array2="id,master_tble_id,form_name,image_location,file_type,real_file_name,insert_date";
		// $rID=sql_insert("COMMON_PHOTO_LIBRARY", $field_array2,$data_array2, 1);
		
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		// echo "10**".$rID.'**'.$dtlsrID.'**'.$sewing_production_variable; die;
		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".$id;
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
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".$id;
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
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".$id;
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
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".$id;
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

		if(str_replace("'","",$is_posted_account)==1)
		{
			echo "421**Already Posted In Accounting! update and delete not allow.";disconnect($con);die;
		}

		// pro_garments_production_mst table data entry here //3 means print receive
 		$field_array1="production_source*serving_company*location*embel_name*embel_type*production_date*production_quantity*production_type*entry_break_down_type*break_down_type_rej*remarks*floor_id*reject_qnty*challan_no*total_produced*yet_to_produced*wo_order_id*currency_id *exchange_rate *rate*amount*updated_by*update_date*sending_location*sending_company*cost_of_fab_per_pcs*cost_per_pcs*body_part_info*get_entry_date*get_entry_no";
		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_receive_qty);}
		else {$amount="";}
		$data_array1="".$cbo_source."*".$cbo_emb_company."*".$cbo_location."*".$cbo_embel_name."*".$cbo_embel_type."*".$txt_receive_date."*".$txt_receive_qty."*3*".$sewing_production_variable."*".$embro_production_variable."*".$txt_remark."*".$cbo_floor."*".$txt_reject_qty."*".$txt_challan."*".$txt_cumul_receive_qty."*".$txt_yet_to_receive."*".$cbo_work_order."*".$hidden_currency_id."*".$hidden_exchange_rate."*'".$rate."'*'".$amount."'*".$user_id."*'".$pc_date_time."'*'".$sending_location."'*'".$sending_company."'*'".$wash_rate."'*'".$cost_per_pcs."'*".$hid_body_part."*".$get_entry_date."*".$get_entry_no."";

		// echo "10**".$cbo_work_order;die;
 		//$rID=sql_update("pro_garments_production_mst",$field_array,$data_array,"id","".$txt_mst_id."",1);
		//echo $data_array;die;
		// pro_garments_production_dtls table data entry here
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) // check is not gross level
		{

			$embelName=str_replace("'","",$cbo_embel_name);


			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
											sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty
											from pro_garments_production_dtls a,pro_garments_production_mst b
											where a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and b.embel_name='$embelName' and a.production_type in(2,3) and a.status_active=1 and  b.id<>$txt_mst_id
											group by a.color_size_break_down_id");
			$color_pord_data=array();
			foreach($dtlsData as $row)
			{
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}


 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,color_type_id,cost_of_fab_per_pcs,cost_per_pcs,wo_rate_pcs";

			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name   and status_active=1 and is_deleted=0  order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}

				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//

				$rowExRej = array_filter(explode("**",$colorIDvalueRej));
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorSizeRejIDArr = explode("*",$valR);
					//echo $colorSizeRejIDArr[0]; die;
					$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
				}

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
								echo "35**Embellishment Receive Quantity Not Over Embellishment Issue Qnty";
								//check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}
					*/
					//3 for Receive Print / Emb. Entry

					$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_of_fab_per_pcs'];
					$cut_oh_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cut_oh_per_pcs'];
					$trims_cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['trims_cost_per_pcs'];
					// $cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['cost_per_pcs'];
					$emb_wo_rate = $emb_wo_rate_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)];

					$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['prod_qty'];
					$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorSizeNumberIDArr[0]]['amount'];

					$cost_per_pcs = $amount/$prod_qty;
					// $cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.','');

					$wash_cost = $color_data_array[$colorSizeNumberIDArr[0]]['ok']*$cost_per_pcs;
					$wash_charge = $color_data_array[$colorSizeNumberIDArr[0]]['ok']*$wash_rate;
					$cost_per_pcs = $cost_per_pcs + $wash_rate;
					$cost_per_pcs = fn_number_format($cost_per_pcs,$dec_place[3],'.','');

					if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
					{
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.",'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.",'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
					else
					{
						//echo "33333";
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

				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
				$rowExRej = array_filter(explode("***",$colorIDvalueRej));
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorAndSizeRej_arr = explode("*",$valR);
					$sizeID = $colorAndSizeRej_arr[0];
					$colorID = $colorAndSizeRej_arr[1];
					$colorSizeRej = $colorAndSizeRej_arr[2];
					$index = $sizeID.$colorID;
					$rejQtyArr[$index]=$colorSizeRej;
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
								echo "35**Embellishment Receive Quantity Not Over Embellishment Issue Qnty";
							//	check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}*/

					$cost_of_fab_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_of_fab_per_pcs'];
					$cut_oh_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cut_oh_per_pcs'];
					$trims_cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['trims_cost_per_pcs'];
					// $cost_per_pcs = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['cost_per_pcs'];
					$emb_wo_rate = $emb_wo_rate_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)];

					$prod_qty = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['prod_qty'];
					$amount = $fab_cost_array[str_replace("'","",$hidden_po_break_down_id)][str_replace("'","",$cbo_item_name)][$colorID]['amount'];

					$cost_per_pcs = $amount/$prod_qty;
					// $cost_per_pcs = number_format($cost_per_pcs,$dec_place[3],'.','');

					$wash_cost = $color_data_array[$colorID]['ok']*$cost_per_pcs;
					$wash_charge = $color_data_array[$colorID]['ok']*$wash_rate;
					$cost_per_pcs = $cost_per_pcs + $wash_rate;
					$cost_per_pcs = fn_number_format($cost_per_pcs,$dec_place[3],'.','');

					if($colSizeID_arr[$index]!="")
					{
						//3 for Receive Print / Emb. Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.",'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.",'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
					else
					{
						//echo "44444";
						echo "420**";die();
					}
				}
			}
 			//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}//end cond

		$dtlsrDelete = execute_query("UPDATE pro_garments_production_dtls SET STATUS_ACTIVE=0,IS_DELETED=0 where mst_id=$txt_mst_id",1);
		//echo "$field_array1++++++++++$data_array1"; exit();
		$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1); //echo $rID;die;
      //  echo $rID ;
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}


		// Reject Part
		$defectReject=true;
		$data_array_defect_reject="";
		$save_dataReject=explode(",",str_replace("'","",$save_dataReject));
		if(count($save_dataReject)>0 && str_replace("'","",$save_dataReject)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$field_array_defectsp="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty,embel_name, inserted_by, insert_date";
 			$defectReject_array=array();
			for($i=0;$i<count($save_dataReject);$i++)
			{
				$order_dtls=explode("**",$save_dataReject[$i]);
				$defect_update_id=$order_dtls[0];
				$defectsp_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectsp_point_id,$defectReject_array) )
				{
					$defectReject_array[$defectsp_point_id]=$defect_qnty;
				}
				else
				{
					$defectReject_array[$defectsp_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defectReject_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defect_reject.=",";
				//if( $dftSp_id=="" ) $dftSp_id=return_next_id("id", "pro_gmts_prod_dft", 1); else $dftSp_id = $dftSp_id+1;
				$dftSp_id=return_next_id_by_sequence("PRO_GMTS_PROD_DFT_SEQ", "pro_gmts_prod_dft", $con);
				$defectspPointId=$keysp;
				$defectsp_qty=$valsp;
				$data_array_defect_reject.="(".$dftSp_id.",".$txt_mst_id.",3,".$hidden_po_break_down_id.",".$defectReject_type_id.",".$defectspPointId.",'".$defectsp_qty."',$cbo_embel_name,".$user_id.",'".$pc_date_time."')";
				$i++;
			}

			if($data_array_defect_reject!="")
			{
				// echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectsp.") VALUES ".$data_array_defect_reject.""; die;
				//echo "DELETE FROM pro_gmts_prod_dft WHERE mst_id='$txt_mst_id' and defect_type_id=2";die;
				$query4=execute_query("UPDATE pro_gmts_prod_dft SET STATUS_ACTIVE=0,IS_DELETED=1 WHERE mst_id=$txt_mst_id and defect_type_id=1 and production_type=3",1);
				$defectReject=sql_insert("pro_gmts_prod_dft",$field_array_defectsp,$data_array_defect_reject,1);
			}
		}

		//echo "10**".$rID ."=". $dtlsrID."==".$dtlsrDelete;die;
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
			}
			else
			{
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


		if(str_replace("'","",$is_posted_account)==1)
		{
			echo "421**Already Posted In Accounting! update and delete not allow.";disconnect($con);die;
		}

		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);

 		if($db_type==0)
		{
			if($rID && $dtlsrID)
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

if($action=="emblishment_receive_print")// 29/10/2019
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$mst_id=implode(',',explode("_",$data[1]));
	//print_r( $mst_id);die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$order_library=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );
	$sel_col=($db_type!=0) ? " listagg(color_type_id,',') within group(order by color_type_id) as color_type_id  " : " group_concat(color_type_id) as color_type_id";

	$sql_color_type=sql_select("SELECT $sel_col  from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id in ($mst_id) and production_type=3");
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

	if($db_type==0)
	{
		$body_part_info_cond=",group_concat(body_part_info) as body_part_info";
	}
	else if($db_type==2)
	{
		$body_part_info_cond=",LISTAGG(CAST(body_part_info AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as body_part_info";
	}

// 	$sql="SELECT min(id) as id, min(company_id) as company_id, sum(production_quantity) as production_quantity,sum(reject_qnty) as  reject_qnty,  min(challan_no) as challan_no,min( po_break_down_id) as po_break_down_id, min(item_number_id) as item_number_id,min(entry_break_down_type) as entry_break_down_type,min(break_down_type_rej) as break_down_type_rej, min(country_id) as country_id,
//  min(production_source) as production_source, min(serving_company) as serving_company, min( location) as location ,
// 	min(embel_name) as embel_name, min(embel_type) as embel_type, min(production_date) as production_date, min( production_type) as production_type, min(remarks) as remarks $body_part_info_cond from pro_garments_production_mst where production_type=3 and id in($mst_id) and status_active=1 and is_deleted=0";

	$sql="SELECT min(id) as id, min(company_id) as company_id, sum(production_quantity) as production_quantity,sum(reject_qnty) as  reject_qnty,  min(challan_no) as challan_no,min( po_break_down_id) as po_break_down_id, min(item_number_id) as item_number_id,min(entry_break_down_type) as entry_break_down_type,min(break_down_type_rej) as break_down_type_rej, min(country_id) as country_id,
	min(production_source) as production_source, min(serving_company) as serving_company, min( location) as location ,
	   min(embel_name) as embel_name, min(embel_type) as embel_type, min(production_date) as production_date, min( production_type) as production_type, min(remarks) as remarks ,body_part_info from pro_garments_production_mst where production_type=3 and id in($mst_id) and status_active=1 and is_deleted=0 group by body_part_info";
	// echo $sql;die;
	$dataArray=sql_select($sql);

	/*foreach($dataArray as $row)
	{
		if($body_part_info!='') $body_part_info.=", ".$row[csf('body_part_info')];else  $body_part_info=$row[csf('body_part_info')];
	}*/


	$break_down_type_reject=$dataArray[0][csf('break_down_type_rej')];
	$entry_break_down_type=$dataArray[0][csf('entry_break_down_type')];

	?>
	<div style="width:930px;">


    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:12px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
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
        	<td width="100" rowspan="7" valign="top" colspan="2"><p><strong>Embel. Company : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></p></td>
            <td width="125"><strong>Order No :</strong></td><td width="175px"><? echo $order_library[$dataArray[0][csf('po_break_down_id')]]; ?></td>
            <?
				foreach($dataArray as $row)
				{
					$style_job=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"job_no");
					$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"style_ref_no");
					$buyer_id=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"buyer_name");
					$order_qnty=return_field_value("po_quantity"," wo_po_break_down","id=".$row[csf("po_break_down_id")],"po_quantity");
				}
			?>
        	<td width="125"><strong>Style Ref. :</strong></td><td width="175px"><? echo $style_val; ?></td>
        </tr>
        <tr>
        	<td><strong>Order Qty :</strong></td><td><? echo $order_qnty; ?></td>
        	<td><strong>Item :</strong></td><td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Emb. Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
           <td><strong>Emb. Type :</strong></td><td ><? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Embel. Name :</strong></td>
            <td><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td><strong>Receive Date :</strong></td>
            <td><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>

        </tr>
        <tr>
            <td><strong>Challan No :</strong></td>
            <td><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Buyer :</strong></td>
            <td><? echo $buyer_name_library[$buyer_id]; ?></td>

        </tr>
        <tr>
            <td><strong>Job No :</strong></td>
            <td><? echo $style_job; ?></td>
            <td><strong>Color Type :</strong></td>
            <td><? echo $color_tp;?></td>
        </tr>
		<tr>
			<td><strong>Int. Reff :</strong></td>
			<td>
				<?
					$internal_ref=return_field_value("f.grouping"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$dataArray[0][csf('po_break_down_id')],"grouping");
					echo $internal_ref;
				?>
			</td>
        </tr>
        <tr>
        	<td colspan="4" ><strong>Remarks :  <? echo $dataArray[0][csf('remarks')]; ?></strong></td>
        </tr>

        <tr>
         <?
		if($entry_break_down_type==1)
		{
		?>

        	<td colspan="3" ><strong>Receive Qnty :  <? echo $dataArray[0][csf('production_quantity')]; ?></strong></td>
       <?
		}
		 if($break_down_type_reject==1)
		{
  ?>
            <td colspan="3" ><strong>Reject Qnty:  <? echo $dataArray[0][csf('reject_qnty')]; ?></strong></td>
       <? }
		   else
		 { ?>
			   <td colspan="6">&nbsp;</td>
	<?   }
	 ?>
        </tr>

    </table>
         <br>
        <?
		if($entry_break_down_type!=1)
		{
			//$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qty,b.item_number_id, b.color_number_id, b.size_number_id,c.body_part_info,c.challan_no from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.id=a.mst_id and a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id,b.item_number_id, b.color_number_id,c.body_part_info,c.challan_no ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			$color_array=array ();
			$body_part_info=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
				//$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$body_part_info[$row[csf('challan_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('body_part_info')]] = $row[csf('body_part_info')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
			$country_id=return_field_value("b.country_id"," pro_garments_production_dtls a, wo_po_color_size_breakdown b","a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ","country_id");
		?>

	<div style="width:100%;">
    <div style="margin-left:30px;"><strong> Goods Qty.</strong></div>
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
              <th width="80" align="center">Country</th>
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
                         <td><? echo $country_library[$country_id]; ?></td>
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
            <td colspan="3" align="right"><strong>Grand Total :</strong></td>
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
      <br>

		 <?
		}
		if($break_down_type_reject!=1)
		{
			//$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			 $sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.reject_qty>0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			$reject_qun_array=array ();
			$color_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				//$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
				$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");


				$country_id2=return_field_value("b.country_id"," pro_garments_production_dtls a, wo_po_color_size_breakdown b","a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","country_id");
		?>

	<div style="width:100%;">
    <div style="width:900px; margin-left:30px;">
    </div>
    &nbsp;<br>
    <?
	// echo "<pre>sdfs";print_r($body_part_info);die;
    if(count($body_part_info)>0)
	{
    ?>
	    <table style="margin-top: 20px;" align="right" cellspacing="0" width="500"  border="1" rules="all" class="rpt_table">


		<tr>
		<thead  align="center" >
		<th align="center" colspan="7"><b>Embellishment Receive Challan</b></th>

		</thead>
		</tr>
		<tr>
		<th align="center" colspan="7">&nbsp;</th>
		</tr>
		<tr>
		   <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="250">Body Part</th>
				<th width="100">Garment Item</th>
				<th width="100">Color</th>
				<th width="100">Challan</th>
	            <th width="100">Quantity</th>
	            <th>Reject Quantity</th>
			</thead>

	        <tbody>
	    </tr>
				<?

	            $k=1;
				$grandTotal  =0;
				foreach($body_part_info as $challan=>$challan_info)
				{
					foreach($challan_info as $item_id=>$item_info)
					{
						foreach($item_info as $color_id=>$color_info)
						{
							foreach($color_info as $body_info)
							{

								$subtotal = 0;
								 $single_body_info=explode("=",$body_info);
								for($i=0; $i<count($single_body_info); $i++){
									$actual_body_info=explode("_",$single_body_info[$i]);
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>">
										<td><? echo $k+1;  ?></td>
										<td><? echo $body_part[$actual_body_info[0]];  ?></td>
										<td align="left"><?=$garments_item[$item_id];  ?></td>
										<td align="left"><?=$colorarr[$color_id];  ?></td>
										<td align="center"><?=$challan;?></td>
										<td align="right"><? echo number_format($actual_body_info[1],4);  ?></td>
										<td align="right"><? echo number_format($actual_body_info[2],4);  ?></td>

									</tr>

									<?
									$subtotal+= number_format($actual_body_info[1],4);
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">
										<td colspan="5" align="center"><strong>Color wise sub Total</strong></td>
										<td align="right"> <? echo $subtotal; ?></td>
										<td align="right"></td>

									</tr>
								<?php
								$k++;
								$grandTotal+=$subtotal;
							}

						}
					}
				}
	            ?>
			<tr>
			 <td colspan="5" align="right"><strong>Grand Total</strong></td>
			 <td  align="right"><strong><? echo $grandTotal;?></strong></td>
			 </tr>
	        </tbody>
	    </table>
	    &nbsp;<br>
	<?
	}
	//echo $size_arr=count($size_array).'azz';
	if(count($size_array)>0)
	{

	?>
	<table align="right" cellspacing="0" width="900"  border="0" rules="all" class="rpt_table">
    <tr><td><strong> Reject Qty:</strong></td></tr>
    </table>
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
              <th width="80" align="center">Country</th>
				<?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Reject Qnty.</th>
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
                         <td><? echo $country_library[$country_id2]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $reject_qun_array[$cid][$sizval]; ?></td>
                            <?
                            $reject_tot_qnty[$cid]+=$reject_qun_array[$cid][$sizval];
							$reject_tot_qnty_size[$sizval]+=$reject_qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $reject_tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$reject_production_quantity+=$reject_tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="3" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $reject_tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $reject_production_quantity; ?></td>
        </tr>
    </table>
	<?
		}

		?>
		<br>
		<? }
            echo signature_table(27, $data[0], "900px");
         ?>
	</div>
	</div>
	<?
	exit();
}
/**Added New Button
 *
 * Developer : Fiz
 * Start Date: 8-6-2023
 * added new print button with report
 *
 */
if($action=="emblishment_receive_print5")// 29/10/2019
{

	extract($_REQUEST);
	$data=explode('*',$data);
	$mst_id=implode(',',explode("_",$data[1]));
	//print_r( $mst_id);die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$order_library=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );
	$sel_col=($db_type!=0) ? " listagg(color_type_id,',') within group(order by color_type_id) as color_type_id  " : " group_concat(color_type_id) as color_type_id";

	$sql_color_type=sql_select("SELECT $sel_col  from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id in ($mst_id) and production_type=3");
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

	if($db_type==0)
	{
		$body_part_info_cond=",group_concat(body_part_info) as body_part_info";
	}
	else if($db_type==2)
	{
		$body_part_info_cond=",LISTAGG(CAST(body_part_info AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as body_part_info";
	}


	$sql="SELECT min(id) as id, min(company_id) as company_id, sum(production_quantity) as production_quantity,sum(reject_qnty) as  reject_qnty, min(challan_no) as challan_no,min( po_break_down_id) as po_break_down_id, min(item_number_id) as item_number_id,min(entry_break_down_type) as entry_break_down_type,min(break_down_type_rej) as break_down_type_rej, min(country_id) as country_id,
	min(production_source) as production_source, min(serving_company) as serving_company, min( location) as location ,
	   min(embel_name) as embel_name, min(embel_type) as embel_type, min(production_date) as production_date, min( production_type) as production_type, min(remarks) as remarks , min(get_entry_no) as entry_no,
          min(get_entry_date) as entry_date,
	   body_part_info from pro_garments_production_mst where production_type=3 and id in($mst_id) and status_active=1 and is_deleted=0 group by body_part_info";
	// echo $sql;die;
	$dataArray=sql_select($sql);




	$break_down_type_reject=$dataArray[0][csf('break_down_type_rej')];
	$entry_break_down_type=$dataArray[0][csf('entry_break_down_type')];

	?>
	<div style="width:930px;">


    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:12px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
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


        	<td width="100" rowspan="7" valign="top" colspan="2"><p><strong>Embel. Company : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong>


	        		<?
	        			//echo $dataArray[0][csf('production_source')];
	        			if($dataArray[0][csf('production_source')]==1)
	        				{
	        					$nameArray=sql_select( "SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id='".$dataArray[0][csf('serving_company')]."' and status_active=1 and is_deleted=0");
								foreach ($nameArray as $result)
								{
									if ($result[csf('plot_no')]!="") echo $result[csf('plot_no')].',&nbsp;&nbsp;';
									if ($result[csf('level_no')]!="") echo $result[csf('level_no')].',&nbsp;&nbsp;';
									if ($result[csf('road_no')]!="") echo $result[csf('road_no')].',&nbsp;&nbsp;';
									if ($result[csf('block_no')]!="") echo $result[csf('block_no')].',&nbsp;&nbsp;';
									if ($result[csf('city')]!="") echo $result[csf('city')];
								}
	        				}
	        			else if($dataArray[0][csf('production_source')]==3)
	        				echo $address;
	        				/*echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;*/
	        		?>
	        	</p>
		</td>

			<td width="125"><strong>Order No :</strong></td><td width="175px"><? echo $order_library[$dataArray[0][csf('po_break_down_id')]]; ?></td>
            <?
				foreach($dataArray as $row)
				{
					$style_job=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"job_no");
					$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"style_ref_no");
					$buyer_id=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"buyer_name");
					$order_qnty=return_field_value("po_quantity"," wo_po_break_down","id=".$row[csf("po_break_down_id")],"po_quantity");
				}
			?>
        	<td width="125"><strong>Style Ref. :</strong></td><td width="175px"><? echo $style_val; ?></td>
        </tr>
        <tr>
        	<td><strong>Order Qty :</strong></td><td><? echo $order_qnty; ?></td>
        	<td><strong>Item :</strong></td><td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Emb. Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
           <td><strong>Emb. Type :</strong></td><td ><? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Embel. Name :</strong></td>
            <td><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td><strong>Receive Date :</strong></td>
            <td><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>

        </tr>
        <tr>
		    <td><strong>Job No :</strong></td>
            <td><? echo $style_job; ?></td>
            <td><strong>Buyer :</strong></td>
            <td><? echo $buyer_name_library[$buyer_id]; ?></td>

        </tr>
        <tr>
		    <td><strong>Gate Entry no. :</strong></td>
			<td>
				<?

					echo $dataArray[0][csf('entry_no')];
				?>
			</td>
            <td><strong>Color Type :</strong></td>
            <td><? echo $color_tp;?></td>
        </tr>
		<tr>
			
			<td><strong>Gate Entry Date :</strong></td>
			<td>
				<?
					echo change_date_format($dataArray[0][csf('entry_date')]);
				?>
			</td>
        </tr>
        <tr>
        	<td colspan="4" ><strong>Remarks :  <? echo $dataArray[0][csf('remarks')]; ?></strong></td>
        </tr>

        <tr>
         <?
		if($entry_break_down_type==1)
		{
		?>

        	<td colspan="3" ><strong>Receive Qnty :  <? echo $dataArray[0][csf('production_quantity')]; ?></strong></td>
       <?
		}
		 if($break_down_type_reject==1)
		{
  ?>
            <td colspan="3" ><strong>Reject Qnty:  <? echo $dataArray[0][csf('reject_qnty')]; ?></strong></td>
       <? }
		   else
		 { ?>
			   <td colspan="6">&nbsp;</td>
	<?   }
	 ?>
        </tr>

    </table>
         <br>
        <?
		if($entry_break_down_type!=1)
		{
			//$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT c.id,sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qty,b.item_number_id, b.color_number_id, b.size_number_id,c.body_part_info,c.challan_no from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c where c.id=a.mst_id and a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.id, b.size_number_id,b.item_number_id, b.color_number_id,c.body_part_info,c.challan_no ";
			// echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			$color_array=array ();
			$body_part_info=array ();
			$reject_qun_array = array();
			$sys_number_array=array();
			$challan_no_array=array();
			// $sys_number=$result[0][csf('id')];
			// $challan_no=$result[0][csf('challan_no')];
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
				$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
				$sys_number_array[$row[csf('color_number_id')]]['id']=$row[csf('id')];
				$challan_no_array[$row[csf('color_number_id')]]['challan_no']=$row[csf('challan_no')];
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$body_part_info[$row[csf('challan_no')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('body_part_info')]] = $row[csf('body_part_info')];
			}
			// echo "<pre>";
			// print_r($row);die;

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
			$country_id=return_field_value("b.country_id"," pro_garments_production_dtls a, wo_po_color_size_breakdown b","a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ","country_id");
		?>

	<div style="width:100%;">
    <div style="margin-left:30px;"><strong> Goods Quantity Description:</strong></div>
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
	<thead  style="font-weight:bold" align="center">
            <tr>
                <td width="80" rowspan="2">SL</td>
                <td width="80" rowspan="2">System Id</td>
                <td width="80" rowspan="2">Challan No</td>
                <td width="80" rowspan="2">Color</td>
                <td width="80" rowspan="2">Country</td>
                <?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <td colspan="2"  width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></td>
                    <?
                }
                ?>
                <td rowspan="2">Total Receive Quantity</td>
                <td rowspan="2">Total Reject Quantity</td>
                <td rowspan="2">Balance/Short  Quantity</td>
            </tr>
            <tr>
			<?
			foreach ($size_array as $sizid)
                {
               ?>
                  <td>Receive Qty</td>
                   <td>Reject Qty</td>
				<?
                }
                ?>
             </tr>
        </thead>
        <tbody>


		<?


//$mrr_no=$dataArray[0][csf('issue_number')];
$i=1;
$tot_qnty=array();
$rec_t_qnty=array();
$reject_production_quantity=0;
$grand_total = $grand_rejct = 0 ;
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
                <td><? echo $sys_number_array[$cid]['id'];  ?></td>
                <td><? echo $challan_no_array[$cid]['challan_no'];  ?></td>
                <td><? echo $colorarr[$cid]; ?></td>
                <td><? echo $country_library[$country_id]; ?></td>
                <?
			foreach ($size_array as $sizid)
                {
					$size_count=count($sizid);
               ?>
	            <td><? echo $qun_array[$cid][$sizid]?></td>
                <td><?  echo  $reject_qun_array[$cid][$sizid] ?></td>


              <?
			      $reject_tot_qnty[$sizid]+=$reject_qun_array[$cid][$sizid];
				  $rcv_tot_qty[$sizid] += $qun_array[$cid][$sizid];

				  $rec_t_qnty[$cid] +=  $qun_array[$cid][$sizid];
				  $rej_t_qtny[$cid] += $reject_qun_array[$cid][$sizid];
                }
                ?>

                <td><? echo $rec_t_qnty[$cid];  ?></td>
                <td><? echo $rej_t_qtny[$cid]; ?></td>
                <td></td>

			<?



		$grand_total += $rec_t_qnty[$cid];
		$grand_rejct += $rej_t_qtny[$cid];
		$i++;
	}


        ?>


            <tr>
                <td style="font-weight:bold" colspan="5">Grand Total:</td>
				<?
				foreach ($size_array as $sizid)
                {
               ?>
			      <td><?  echo $rcv_tot_qty[$sizid] ; ?></td>
                  <td><? echo $reject_tot_qnty[$sizid]  ?></td>
			   <?
                }
                ?>


                <td><?  echo $grand_total;  ?></td>
				<td><? echo $grand_rejct ?></td>

            </tr>
        </tbody>

    </table>
      <br>

		 <?
		}
		if($break_down_type_reject!=1)
		{
			//$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			 $sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.reject_qty>0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			$reject_qun_array=array ();
			$color_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				//$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
				$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");


				$country_id2=return_field_value("b.country_id"," pro_garments_production_dtls a, wo_po_color_size_breakdown b","a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","country_id");
		?>

	<div style="width:100%;">
    <div style="width:900px; margin-left:30px;">
    </div>
    &nbsp;<br><br><br><br><br><br>

    <?
	// echo "<pre>sdfs";print_r($body_part_info);die;

	//echo $size_arr=count($size_array).'azz';
	if(count($size_array)>0)
	{

	?>


	<?
		}

		?>
		<br>
		<? }
         //   echo signature_table(27, $data[0], "900px");
         ?>
	</div>
	<br><br><br><br><br>
	</div>
	<style>
	 .sign{
            text-align: center;
			float: left;
            margin-right: 10px;
			margin-left: 40px;
			font-weight:bold;
        }
</style>
	<div class="sign">
        <hr width="90px">
        Prepared By
    </div>
    <div class="sign">
        <hr width="110px">
       Concern Department
    </div>
    <div class="sign">
        <hr width="80px">
       Store Head
    </div>
    <div class="sign">
        <hr width="80px">
       Approved By
    </div>
    <div class="sign">
        <hr width="80px">
       Recived By
    </div>
    <div class="sign">
        <hr width="80px">
       Security
    </div>
	<?
	exit();
}















if($action=="emblishment_receive_print_2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$mst_id=implode(',',explode("_",$data[1]));
	//print_r( $mst_id);die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$order_library=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );
	$sel_col=($db_type!=0) ? " listagg(color_type_id,',') within group(order by color_type_id) as color_type_id  " : " group_concat(color_type_id) as color_type_id";

	$sql_color_type=sql_select("SELECT $sel_col  from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id in ($mst_id) and production_type=3");
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

	if($db_type==0)
	{
		$body_part_info_cond=",group_concat(body_part_info) as body_part_info";
	}
	else if($db_type==2)
	{
		$body_part_info_cond=",LISTAGG(CAST(body_part_info AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as body_part_info";
	}

	$sql="SELECT min(id) as id, min(company_id) as company_id, sum(production_quantity) as production_quantity,sum(reject_qnty) as  reject_qnty,  min(challan_no) as challan_no,min( po_break_down_id) as po_break_down_id, min(item_number_id) as item_number_id,min(entry_break_down_type) as entry_break_down_type,min(break_down_type_rej) as break_down_type_rej, min(country_id) as country_id,
 min(production_source) as production_source, min(serving_company) as serving_company, min( location) as location ,
	min(embel_name) as embel_name, min(embel_type) as embel_type, min(production_date) as production_date, min( production_type) as production_type, min(remarks) as remarks $body_part_info_cond from pro_garments_production_mst where production_type=3 and id in($mst_id) and status_active=1 and is_deleted=0";
	// $sql="select id, company_id, challan_no, po_break_down_id, item_number_id,entry_break_down_type,break_down_type_rej, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, reject_qnty, production_type, remarks, floor_id from pro_garments_production_mst where production_type=3 and id in($mst_id) and status_active=1 and is_deleted=0 group by  production_source,embel_name,po_break_down_id";
	//echo $sql;die;
	$dataArray=sql_select($sql);

	/*foreach($dataArray as $row)
	{
		if($body_part_info!='') $body_part_info.=", ".$row[csf('body_part_info')];else  $body_part_info=$row[csf('body_part_info')];
	}*/
	if($dataArray[0][csf('body_part_info')]!='')
	{
		//echo 1111;
		$body_part_info=array_unique(explode(",",$dataArray[0][csf('body_part_info')]));
	}

	$break_down_type_reject=$dataArray[0][csf('break_down_type_rej')];
	$entry_break_down_type=$dataArray[0][csf('entry_break_down_type')];

	?>
	<div style="width:930px;">


    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:12px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo "Receive Entry Challan"; ?></u></strong></td>
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
        	<td width="100" rowspan="6" valign="top" colspan="2"><p><strong>Embel. Company : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></p></td>
            <td width="125"><strong>Order No :</strong></td><td width="175px"><? echo $order_library[$dataArray[0][csf('po_break_down_id')]]; ?></td>
            <?
				foreach($dataArray as $row)
				{
					$style_job=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"job_no");
					$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"style_ref_no");
					$buyer_id=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"buyer_name");
					$order_qnty=return_field_value("po_quantity"," wo_po_break_down","id=".$row[csf("po_break_down_id")],"po_quantity");
				}
			?>
        	<td width="125"><strong>Style Ref. :</strong></td><td width="175px"><? echo $style_val; ?></td>
        </tr>
        <tr>
        	<td><strong>Order Qty :</strong></td><td><? echo $order_qnty; ?></td>
        	<td><strong>Item :</strong></td><td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Emb. Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
           <td><strong>Emb. Type1 :</strong></td><td ><? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Embel. Name :</strong></td>
            <td><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td><strong>Receive Date :</strong></td>
            <td><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>

        </tr>
        <tr>
            <td><strong>Challan No :</strong></td>
            <td><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Buyer :</strong></td>
            <td><? echo $buyer_name_library[$buyer_id]; ?></td>

        </tr>
        <tr>
            <td><strong>Job No :</strong></td>
            <td><? echo $style_job; ?></td>
            <td><strong>Color Type :</strong></td>
            <td><? echo $color_tp;?></td>
        </tr>
        <tr>
        	<td colspan="4" ><strong>Remarks :  <? echo $dataArray[0][csf('remarks')]; ?></strong></td>
        </tr>

        <tr>
         <?
		if($entry_break_down_type==1)
		{
		?>

        	<td colspan="3" ><strong>Receive Qnty :  <? echo $dataArray[0][csf('production_quantity')]; ?></strong></td>
       <?
		}
		 if($break_down_type_reject==1)
		{
  ?>
            <td colspan="3" ><strong>Reject Qnty:  <? echo $dataArray[0][csf('reject_qnty')]; ?></strong></td>
       <? }
		   else
		 { ?>
			   <td colspan="6">&nbsp;</td>
	<?   }
	 ?>
        </tr>

    </table>
         <br>
        <?
		if($entry_break_down_type!=1)
		{
			//$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			//$reject_qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
				//$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
			}

			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id,a.mst_id ";
			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
			$country_id=return_field_value("b.country_id"," pro_garments_production_dtls a, wo_po_color_size_breakdown b","a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ","country_id");
		?>

	<div style="width:100%;">
    <div style="margin-left:30px;"><strong> Goods Qty.</strong></div>
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
              <th width="80" align="center">Country</th>
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
                         <td><? echo $country_library[$country_id]; ?></td>
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
            <td colspan="3" align="right"><strong>Grand Total :</strong></td>
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
      <br>

		 <?
		}
		if($break_down_type_reject!=1)
		{ ?>
        <?
			//$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			 $sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.reject_qty>0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			$reject_qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				//$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
				$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
			}

			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id)  and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";
			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");


				$country_id2=return_field_value("b.country_id"," pro_garments_production_dtls a, wo_po_color_size_breakdown b","a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","country_id");
		?>

	<div style="width:100%;">
    <div style="width:900px; margin-left:30px;">
   <table align="right" cellspacing="0" width="900"  border="0" rules="all" class="rpt_table">
    <tr><td><strong> Reject Qty:</strong></td></tr>
    </table>
    </div>
    &nbsp;<br>
    <?
    if($body_part_info!='')
	{
    ?>
	    <table align="right" cellspacing="0" width="500"  border="1" rules="all" class="rpt_table">
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="250">Body Part</th>
	            <th width="100">Quantity</th>
	            <th>Reject Quantity</th>
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
		                        <td align="right"><? echo number_format($actual_body_info[2],4);  ?></td>
		                    </tr>
		                    <?
	                	}
	                }
	            ?>
	        </tbody>
	    </table>
	    &nbsp;<br>
	<?
	}
	//echo $size_arr=count($size_array).'azz';
	if(count($size_array)>0)
	{

	?>
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
              <th width="80" align="center">Country</th>
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
                         <td><? echo $country_library[$country_id2]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $reject_qun_array[$cid][$sizval]; ?></td>
                            <?
                            $reject_tot_qnty[$cid]+=$reject_qun_array[$cid][$sizval];
							$reject_tot_qnty_size[$sizval]+=$reject_qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $reject_tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$reject_production_quantity+=$reject_tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="3" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $reject_tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $reject_production_quantity; ?></td>
        </tr>
    </table>
	<?
		}

		?>
		<br>
		<? }
            echo signature_table(27, $data[0], "900px");
         ?>
	</div>
	</div>
	<?
	exit();
}


if($action=="emblishment_receive_print_new")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$mst_id=implode(',',explode("_",$data[1]));
	//print_r( $mst_id);die;
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$order_library=return_library_array( "select id, po_number from  wo_po_break_down", "id", "po_number"  );
	$sel_col=($db_type!=0) ? " listagg(color_type_id,',') within group(order by color_type_id) as color_type_id  " : " group_concat(color_type_id) as color_type_id";

	$sql_color_type=sql_select("SELECT $sel_col  from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id in ($mst_id) and production_type=3");
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


	$sql="SELECT id, company_id, sum(production_quantity) as production_quantity,sum(reject_qnty) as  reject_qnty,  challan_no, po_break_down_id, item_number_id, entry_break_down_type, break_down_type_rej, country_id, production_source, serving_company, location , embel_name,embel_type,production_date,  production_type, remarks from pro_garments_production_mst where production_type=3 and id in($mst_id) and status_active=1 and is_deleted=0 group by id, company_id,challan_no,po_break_down_id, item_number_id, entry_break_down_type, break_down_type_rej, country_id, production_source, serving_company, location , embel_name,embel_type,production_date,  production_type, remarks";
	// echo $sql;
	// $sql="select id, company_id, challan_no, po_break_down_id, item_number_id,entry_break_down_type,break_down_type_rej, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, reject_qnty, production_type, remarks, floor_id from pro_garments_production_mst where production_type=3 and id in($mst_id) and status_active=1 and is_deleted=0 group by  production_source,embel_name,po_break_down_id";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$break_down_type_reject=$dataArray[0][csf('break_down_type_rej')];
	$entry_break_down_type=$dataArray[0][csf('entry_break_down_type')];

	?>
	<div style="width:930px;">


    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:12px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
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
        	<td width="100" rowspan="6" valign="top" colspan="2"><p><strong>Embel. Company : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></p></td>
            <td width="125"><strong>Order No :</strong></td><td width="175px"><? echo $order_library[$dataArray[0][csf('po_break_down_id')]]; ?></td>
            <?
				foreach($dataArray as $row)
				{
					$style_job=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"job_no");
					$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"style_ref_no");
					$buyer_id=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$row[csf("po_break_down_id")],"buyer_name");
					$order_qnty=return_field_value("po_quantity"," wo_po_break_down","id=".$row[csf("po_break_down_id")],"po_quantity");
				}
			?>
        	<td width="125"><strong>Style Ref. :</strong></td><td width="175px"><? echo $style_val; ?></td>
        </tr>
        <tr>
        	<td><strong>Order Qty :</strong></td><td><? echo $order_qnty; ?></td>
        	<td><strong>Item :</strong></td><td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Emb. Source :</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
           <td><strong>Emb. Type :</strong></td><td ><? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Embel. Name :</strong></td>
            <td><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
            <td><strong>Receive Date :</strong></td>
            <td><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>

        </tr>
        <tr>
            <td><strong>Challan No :</strong></td>
            <td><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Buyer :</strong></td>
            <td><? echo $buyer_name_library[$buyer_id]; ?></td>

        </tr>
        <tr>
            <td><strong>Job No :</strong></td>
            <td><? echo $style_job; ?></td>
            <td><strong>Color Type :</strong></td>
            <td><? echo $color_tp;?></td>
        </tr>
        <tr>
        	<td colspan="4" ><strong>Remarks :  <? echo $dataArray[0][csf('remarks')]; ?></strong></td>
        </tr>

        <tr>
         <?
		if($entry_break_down_type==1)
		{
		?>

        	<td colspan="3" ><strong>Receive Qnty :  <? echo $dataArray[0][csf('production_quantity')]; ?></strong></td>
       <?
		}
		 if($break_down_type_reject==1)
		{
  ?>
            <td colspan="3" ><strong>Reject Qnty:  <? echo $dataArray[0][csf('reject_qnty')]; ?></strong></td>
       <? }
		   else
		 { ?>
			   <td colspan="6">&nbsp;</td>
	<?   }
	 ?>
        </tr>

    </table>
         <br>
        <?
		if($entry_break_down_type!=1)
		{
			//$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			//$reject_qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
				//$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
			}

			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id,a.mst_id ";
			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
			$country_id=return_field_value("b.country_id"," pro_garments_production_dtls a, wo_po_color_size_breakdown b","a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ","country_id");
		?>

	<div style="width:100%;">
    <div style="margin-left:30px;"><strong> Goods Qty.</strong></div>
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
              <th width="80" align="center">Country</th>
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
                         <td><? echo $country_library[$country_id]; ?></td>
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
            <td colspan="3" align="right"><strong>Grand Total :</strong></td>
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
      <br>

		 <?
		}
		if($break_down_type_reject!=1)
		{ ?>
        <?
			//$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			 $sql="SELECT sum(a.production_qnty) as production_qnty,sum(a.reject_qty) as reject_qty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.reject_qty>0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			$reject_qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				//$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
				$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
			}

			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id)  and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";
			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");


				$country_id2=return_field_value("b.country_id"," pro_garments_production_dtls a, wo_po_color_size_breakdown b","a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","country_id");
		?>

	<div style="width:100%;">
    <div style="width:900px; margin-left:30px;">
   <table align="right" cellspacing="0" width="900"  border="0" rules="all" class="rpt_table">
    <tr><td><strong> Reject Qty:</strong></td></tr>
    </table>
    </div>
	<?
	//echo $size_arr=count($size_array).'azz';
	if(count($size_array)>0)
	{

	?>
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
              <th width="80" align="center">Country</th>
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
                         <td><? echo $country_library[$country_id2]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $reject_qun_array[$cid][$sizval]; ?></td>
                            <?
                            $reject_tot_qnty[$cid]+=$reject_qun_array[$cid][$sizval];
							$reject_tot_qnty_size[$sizval]+=$reject_qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $reject_tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$reject_production_quantity+=$reject_tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="3" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $reject_tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $reject_production_quantity; ?></td>
        </tr>
    </table>
	<?
		}
		?>
		<br>
		<? }
            echo signature_table(27, $data[0], "900px");
         ?>
	</div>
	</div>
	<?
	exit();
}

if ($action=="load_drop_down_color_type")
{
	$sql="SELECT b.color_type_id from  wo_po_break_down a, wo_pre_cost_fabric_cost_dtls b, wo_pre_cos_fab_co_avg_con_dtls c where a.job_no_mst=b.job_no  and b.id=c.pre_cost_fabric_cost_dtls_id and a.id=c.po_break_down_id and b.job_no=c.job_no and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id='$data' and c.cons>0  group by b.color_type_id";
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
<?
if($action=="emblishment_issue_print2") // Print 3 Start.
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

	$sql="SELECT id, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_quantity, production_type, remarks, floor_id from pro_garments_production_mst where production_type=3 and id in($mst_id) and status_active=1 and is_deleted=0 ";
	$sql_color_type=sql_select("SELECT $sel_col from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id in ($mst_id) and production_type=3");
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
	$issue_date='';
	foreach($dataArray as $row)
	{
		$country=$row[csf('country_id')];
		if($issue_date!='') $issue_date.=", ".change_date_format($row[csf('production_date')]);else  $issue_date=change_date_format($row[csf('production_date')]);
	}
	?>
	<div style="width:930px;">
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
	            <td colspan="4"  width="300" align="center" style="font-size:24px;padding-left:120px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">

	        	<td colspan="4" align="center" style="font-size:14px;padding-right:312px;">

					<?
	 					$nameArray=sql_select( "select country_id from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
	 					//$country=$country_arr[$nameArray[0][csf("country_id")]];

	                ?>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="4" width="300" align="center" style="font-size:18px;padding-left:120px"><u><strong>Embellishment Receive Challan <? //echo $country_arr[$country]; ?></strong></u></td>
	        </tr>

	        <tr>
				<?
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
	        	<td><strong>Style Ref. :</strong></td>
	        	<td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>
	        	<td><strong>Emb. Source:</strong></td>
	        	<td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>

	        </tr>



	        <tr>
	        	<td colspan="2">
	        		<?
	        			//echo $dataArray[0][csf('production_source')];
	        			if($dataArray[0][csf('production_source')]==1)
	        				{
	        					$nameArray=sql_select( "SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id='".$dataArray[0][csf('serving_company')]."' and status_active=1 and is_deleted=0");
								foreach ($nameArray as $result)
								{
									if ($result[csf('plot_no')]!="") echo $result[csf('plot_no')].',&nbsp;&nbsp;';
									if ($result[csf('level_no')]!="") echo $result[csf('level_no')].',&nbsp;&nbsp;';
									if ($result[csf('road_no')]!="") echo $result[csf('road_no')].',&nbsp;&nbsp;';
									if ($result[csf('block_no')]!="") echo $result[csf('block_no')].',&nbsp;&nbsp;';
									if ($result[csf('city')]!="") echo $result[csf('city')];
								}
	        				}
	        			else if($dataArray[0][csf('production_source')]==3)
	        				echo $address;
	        				/*echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;*/
	        		?>
	        	</td>
	        	<td> <strong>Job No</strong></td>
	        	<td> <? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['job']; ?></td>
				<!-- <td></td>
                <td></td> -->
	        	<td><strong>Emb. Type:</strong></td>
		        <td>
		        	<? if ($dataArray[0][csf('embel_name')]==1) echo $emblishment_print_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==2) echo $emblishment_embroy_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==3) echo $emblishment_wash_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==4) echo $emblishment_spwork_type[$dataArray[0][csf('embel_type')]]; elseif ($dataArray[0][csf('embel_name')]==5) echo $emblishment_gmts_type[$dataArray[0][csf('embel_type')]];
		        	?>
		        </td>
	        </tr>

	        <tr>
	         <td><strong>Location :</strong></td>
	         <td style="padding-right: 50px">
	         	<? echo $location_arr[$dataArray[0][csf('location')]]; ?>
	         </td>
	         <td><strong>Order No :</strong></td>
	         <td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_number']; ?></td>
	         <td><strong>Emb.Name :</strong></td>
	         <td><? echo $emblishment_name_array[$dataArray[0][csf('embel_name')]]; ?></td>
	        </tr>

	        <tr>
	        	<td><strong>Int. Reff :</strong></td>
	        	<td>
	        		<?
	        			$internal_ref=return_field_value("f.grouping"," wo_po_break_down f, wo_po_details_master h","f.job_id=h.id and f.id=".$dataArray[0][csf('po_break_down_id')],"grouping");
	        			echo $internal_ref;
	        		?>
	        	</td>
	            <td><strong>Order Qty:</strong></td>
	            <td><? echo $order_array[$dataArray[0][csf('po_break_down_id')]]['po_quantity']; ?></td>
	            <td><strong>Color Type:</strong></td>
	            <td><? echo $color_tp;?></td>
	        </tr>
			<?
				$sql="SELECT a.id, a.production_date, a.man_cutt_no, a.challan_no, a.floor_id, a.remarks, a.country_id, b.production_qnty, c.color_number_id, c.size_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id in($mst_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 order by c.color_number_id, c.id";

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
		            <th width="60">Receive Date</th>
		            <th width="60">Receive ID</th>
		            <th width="60">Challan No</th>
		            <th width="60">Manual Cut No</th>

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
		            <th width="80" align="center">Total Receive Qty.</th>
					<th width="120">Remarks</th>
		        </thead>
		        <tbody>
		        	<?
		        		$sql_prod="SELECT a.id, a.production_date, a.man_cutt_no, a.challan_no, a.floor_id, a.remarks, a.country_id, c.color_number_id from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c where a.id in($mst_id) and a.id=b.mst_id and a.po_break_down_id=c.po_break_down_id and b.color_size_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.id, a.production_date, a.man_cutt_no, a.challan_no, a.floor_id, a.remarks, a.country_id, c.color_number_id";
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
		                        <td> <? echo change_date_format($val[csf("production_date")]);  ?> </td>
		                        <td> <?	echo $val[csf("id")]; ?> </td>
		                        <td> <?	echo $val[csf("challan_no")]; ?> </td>
		                        <td> <? echo $val[csf("man_cutt_no")]; ?> </td>

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
								<td> <? echo $val[csf("remarks")]?> </td>
		                     </tr>
		            <?
						$i++;
						}
					?>
		        </tbody>
		        <tr>
		            <td colspan="7" align="right"><strong>Grand Total : &nbsp;</strong></td>
		            <?
						foreach ($size_array as $sizval)
						{
							?>
		                    <td align="right"><?php echo $tot_specific_size_qnty[$sizval]; ?></td>
		                    <?
						}
					?>
		            <td align="right"><?php echo array_sum($tot_specific_size_qnty); //$grand_tot_color_size_qty; ?></td>
		        </tr>
		        <tr>
		        	<td colspan="10"> <strong>In Word : </strong> <? echo number_to_words( array_sum($tot_specific_size_qnty), 'Pc\'s' ); ?> </td>
		        </tr>
		    </table>
	        <br>
			 <?
	            echo signature_table(27, $data[0], "900px");

	         ?>
		</div>
		</div>
	<?
	exit();
}
