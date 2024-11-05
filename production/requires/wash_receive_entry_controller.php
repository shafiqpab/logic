<?
session_start();
include('../../includes/common.php');
require_once('../../includes/class4/class.conditions.php');
require_once('../../includes/class4/class.reports.php');
require_once('../../includes/class4/class.emblishments.php');
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
	echo create_drop_down( "cbo_location", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/wash_receive_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (7,8,9) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
}

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select service_process_id,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("service_process_id")].");\n";
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

	$sqlResult =sql_select("SELECT b.id as po_id,b.po_number,c.item_number_id,c.color_number_id, a.cost_of_fab_per_pcs,a.cut_oh_per_pcs,a.cost_per_pcs,a.fab_rate_per_pcs,a.trims_cost_per_pcs from pro_garments_production_mst e, pro_garments_production_dtls a,WO_PO_COLOR_SIZE_BREAKDOWN c,wo_po_break_down b,lib_country d where e.id=a.mst_id and a.COLOR_SIZE_BREAK_DOWN_ID=c.id and b.id=c.po_break_down_id  and c.country_id=d.id and c.po_break_down_id=e.po_break_down_id and c.po_break_down_id='$sys_id' and a.status_active=1 and a.is_deleted=0 and a.production_type=3 and e.embel_name=3 and a.cost_per_pcs is not null and a.entry_form=416");
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
		echo create_drop_down( "cbo_emb_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "load_location();fnc_workorder_search(this.value);" );
	}
	else if($data==1)
		echo create_drop_down( "cbo_emb_company", 170, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_location();",0,0 );
	else
		echo create_drop_down( "cbo_emb_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "load_location();fnc_workorder_search(this.value)",0,0 );
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

	$sql= "select a.id,a.booking_no  from wo_booking_mst a, wo_booking_dtls b,  wo_pre_cost_embe_cost_dtls c where a.company_id=".$company_id." and a.job_no=b.job_no and  a.job_no=c.job_no and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and  a.booking_type=6 and a.is_short=2 and  a.status_active=1  and 	a.is_deleted=0 and a.supplier_id=".$supplier_id." and b.gmt_item= ".$gmt_item." and c.emb_name=".$emblishment_name." group by a.id,a.booking_no  order by a.booking_no";
	//echo $sql;
	echo create_drop_down( "cbo_work_order", 170, $sql,"id,booking_no", 1, "-- Select Work Order --", $selected, "fnc_workorder_rate('$data',this.value)",0 );
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



if($action=="load_drop_down_emb_receive_type")
{
	$data=explode("**",$data);
	$emb_name=$data[0];
	$po_id=$data[1];

	if($db_type==0) $embel_name_cond="group_concat(c.emb_type) as emb_type";
		else if($db_type==2) $embel_name_cond="LISTAGG(c.emb_type,',') WITHIN GROUP ( ORDER BY c.emb_type) as emb_type";
			$embl_type=return_field_value("$embel_name_cond","wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c","a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id=$po_id and c.emb_name=3","emb_type");

	//$emblishment_name_array=array(1=>"Printing",2=>"Embroidery",3=>"Wash",4=>"Special Works",5=>"Others");
	if($emb_name==1)
		echo create_drop_down( "cbo_embel_type", 170, $emblishment_print_type,"", 1, "--- Select Printing ---", $selected, "","","$embl_type" );
	elseif($emb_name==2)
		echo create_drop_down( "cbo_embel_type", 170, $emblishment_embroy_type,"", 1, "--- Select Embroidery---", $selected, "" ,"","$embl_type" );
	elseif($emb_name==3)
		echo create_drop_down( "cbo_embel_type", 170, $emblishment_wash_type,"", 1, "--- Select wash---", $selected, "","","$embl_type" );
	elseif($emb_name==4)
		echo create_drop_down( "cbo_embel_type", 170, $emblishment_spwork_type,"", 1, "--- Select Special Works---", $selected, "","","$embl_type" );
	elseif($emb_name==5)
		echo create_drop_down( "cbo_embel_type", 170, $emblishment_gmts_type,"", 1, "--- Select---", $selected, "","","$embl_type" );
	else
		echo create_drop_down( "cbo_embel_type", 170, $blank_array,"", 1, "--- Select---", $selected, "" );
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
				else //if(str==2)
				{
					load_drop_down( 'wash_receive_entry_controller',document.getElementById('company_search_by').value,'load_drop_down_buyer', 'search_by_td' );
					document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				}
			}

			function js_set_value(id,item_id,po_qnty,plan_qnty,country_id,country_ship_date)
			{
				$("#hidden_mst_id").val(id);
				$("#hidden_grmtItem_id").val(item_id);
				$("#hidden_po_qnty").val(po_qnty);
				$("#hidden_country_id").val(country_id);
				$("#country_ship_date").val(country_ship_date);
				$("#hidden_company_id").val(document.getElementById('company_search_by').value);
				parent.emailwindow.hide();
			}
		</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="780" ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<th width="150" class="must_entry_caption">Company</th>
					<th width="130">Search By</th>
					<th width="130" align="center" id="search_by_th_up">Enter Order Number</th>
					<th width="130" colspan="2">Shipment Date Range</th>
					<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
				</thead>
				<tr class="general">
					<td><? echo create_drop_down( "company_search_by", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "",0 ); ?></td>
					<td><?
						$searchby_arr=array(5=>"Job No",0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"File No",4=>" Internal Ref.");
						echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
						?>
					</td>
					<td id="search_by_td"><input type="text" style="width:120px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" /></td>
					<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date"></td>
					<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date"></td>
					<td>
						<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('company_search_by').value+'_'+<? echo $garments_nature; ?>, 'create_po_search_list_view', 'search_div', 'wash_receive_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" valign="middle">
						<? echo load_month_buttons(1);  ?>
						<input type="hidden" id="hidden_mst_id">
						<input type="hidden" id="hidden_grmtItem_id">
						<input type="hidden" id="hidden_po_qnty">
						<input type="hidden" id="hidden_country_id">
						<input type="hidden" id="hidden_company_id">
						<input type="hidden" id="country_ship_date">
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
	exit();
}

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
    $color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	if($company == 0)
	{
		echo "Please Select Company First."; die;
	}
 	$garments_nature = $ex_data[5];

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
 	}
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}


	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";

 	/*$sql = "select b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.file_no,b.grouping,b.po_quantity ,b.plan_cut
			from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c
			where
			a.job_no = b.job_no_mst and
			a.status_active=1 and
			a.is_deleted=0 and
			b.status_active=1 and
			b.is_deleted=0 and
			a.garments_nature=$garments_nature
			$sql_cond  and a.job_no=c.job_no and c.status_active=1 and c.is_deleted=0 order by b.shipment_date"; */

	/*$sql = "SELECT b.id, a.job_no_prefix_num,a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
	from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c, wo_pre_cost_dtls d
	where a.job_no = b.job_no_mst and a.status_active=1 and b.shiping_status <> 3 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond  and a.job_no=c.job_no and c.job_no=d.job_no and (d.embel_cost !=0 or d.wash_cost !=0 )and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ";*/
	$sql = "SELECT b.id, a.job_no_prefix_num, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.file_no, b.grouping, b.po_quantity, b.plan_cut
			from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_embe_cost_dtls c
			where
			a.id = b.job_id and a.id = c.job_id and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.shiping_status <>3 and c.emb_name=3 and c.cons_dzn_gmts>0 and a.garments_nature=$garments_nature $sql_cond
			group by b.id, a.job_no_prefix_num, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.file_no, b.grouping, b.po_quantity, b.plan_cut order by b.shipment_date ASC";

	//echo $sql;die;
	$result = sql_select($sql);
	$po_id_arr = array();
	foreach ($result as $val)
	{
		$po_id_arr[$val[csf('id')]] = $val[csf('id')];
	}
	$allPoIds = implode(",", $po_id_arr);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($db_type==0) $countryCond="group_concat(distinct(country_id))";
	else if($db_type==2) $countryCond="listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id)";

	$po_country_arr=return_library_array( "SELECT po_break_down_id, $countryCond as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by po_break_down_id",'po_break_down_id','country');

	$po_country_data_arr=array();
	$poCountryData=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty,color_number_id,country_ship_date,pack_type from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by po_break_down_id, item_number_id, country_id,color_number_id,country_ship_date,pack_type");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('pack_type')]]['po_qnty']+=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('pack_type')]]['plan_cut_qnty']+=$row[csf('plan_cut_qnty')];
		$po_color_arr[$row[csf("po_break_down_id")]].=','.$row[csf("color_number_id")];
	}
	// echo "<pre>";
	// print_r($po_country_data_arr);

	$total_rec_qty_data_arr=array();
	$total_rec_qty_arr=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=3 and embel_name=3 and po_break_down_id in($allPoIds) group by po_break_down_id, item_number_id, country_id");

	foreach($total_rec_qty_arr as $row)
	{
		$total_rec_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
	}
	?>
    <div style="width:1190px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Shipment Date</th>
                <th width="50">Job No</th>
                <th width="100">Order No</th>
                <th width="80">Buyer</th>
                <th width="100">Style</th>
                <th width="80">File No</th>
                <th width="80">Internal Ref.</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
                <th width="80">Total Rec.Qty</th>
                <th width="80">Balance</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1190px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1172" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

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

				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));
				$numOfCountry = count($country);

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
					//$country_ship_date = $coun_ship_date_data; 
					foreach ($coun_ship_date_data as $pack_type=>$pack_data) 
					{
								$country_ship_date = $coun_ship_date;
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
						$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date][$pack_type]['po_qnty'];
						$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date][$pack_type]['plan_cut_qnty'];

						?>
                        <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<?=$row[csf("id")];?>,'<?=$grmts_item;?>','<?=$po_qnty;?>','<?=$plan_cut_qnty;?>','<?=$country_id;?>','<?  echo $country_ship_date ?>');" >
                            <td width="30" align="center"><?=$i; ?></td>
                            <td width="60" align="center"><?=change_date_format($row[csf("shipment_date")]);?></td>
                            <td width="50" align="center"><?=$row[csf("job_no_prefix_num")];?></td>
                            <td width="100" title="<?=$color_name?$color_name:""?>" style="word-break: break-all;"><?=$row[csf("po_number")]; ?></td>
                            <td width="80" style="word-break: break-all;"><?=$buyer_arr[$row[csf("buyer_name")]]; ?></td>
                            <td width="100" style="word-break: break-all;"><?=$row[csf("style_ref_no")]; ?></td>
                            <td width="80" style="word-break: break-all;"><?=$row[csf("file_no")]; ?></td>
                            <td width="80" style="word-break: break-all;"><?=$row[csf("grouping")]; ?></td>
                            <td width="140" style="word-break: break-all;"><?=$garments_item[$grmts_item];?></td>
                            <td width="100" style="word-break: break-all;"><?=$country_library[$country_id]; ?>&nbsp;</td>
                            <td width="80" align="right"><?=$po_qnty; //$po_qnty*$set_qty;?>&nbsp; </td>
                            <td width="80" align="right"><?=$total_cut_qty=$total_rec_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]; ?>&nbsp;</td>
                            <td width="80" align="right"><?php $balance=$po_qnty-$total_cut_qty; echo $balance; ?>&nbsp; </td>
                            <td style="word-break: break-all;"><?=$company_arr[$row[csf("company_name")]];?> </td>
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
	/*$res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name, $embel_name_cond as emb_name from wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c where c.cons_dzn_gmts>0 and  a.job_no_mst=b.job_no and a.id=$po_id and b.job_no=c.job_no group by a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name");*/

	$res = sql_select("select a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name, c.emb_name
			from wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c
			where c.cons_dzn_gmts>0 and a.shiping_status <> 3  and a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id=$po_id and c.emb_name=3 group by a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name, c.emb_name ");

	 //print_r($res);die;
 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";

		//echo "load_drop_down( 'requires/wash_receive_entry_controller', '".$result[csf('emb_name')]."', 'load_drop_down_embel_name', 'embel_name_td' );\n";
  		$dataArray=sql_select("SELECT SUM(CASE WHEN production_type=2 THEN production_quantity END) as totalcutting, SUM(CASE WHEN production_type=3 and embel_name=3 THEN production_quantity ELSE 0 END) as totalprinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and embel_name=3 and country_id='$country_id' and is_deleted=0");
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

/*if($action=="load_drop_down_embel_name")
{
    echo create_drop_down( "cbo_embel_name", 200, $emblishment_name_array,"", 1, "-- Select Embel.Name --", $selected, "load_drop_down( 'requires/wash_receive_entry_controller', this.value+'**'+$('#hidden_po_break_down_id').val(), 'load_drop_down_emb_receive_type', 'emb_type_td' );  get_php_form_data($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#sewing_production_variable').val()+'**'+$('#styleOrOrderWisw').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val()+'**'+$('#embro_production_variable').val(), 'color_and_size_level', 'requires/wash_receive_entry_controller' ); show_list_view($('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_embel_name').val()+'**'+$('#cbo_country_name').val(),'show_dtls_listview','printing_production_list_view','requires/wash_receive_entry_controller','setFilterGrid(\'tbl_search\',-1)'); ","" ,$data);
}
*/
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

if($action=="color_and_size_level")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	$styleOrOrderWisw = $dataArr[3];
	$embelName = $dataArr[4];
	$country_id = $dataArr[5];
	$variableSettingsRej = $dataArr[6];
	$country_ship_date = $dataArr[7];
	if( $country_ship_date=='') $country_ship_date_cond=''; else $country_ship_date_cond=" and c.country_ship_date='$country_ship_date'";
	if( $country_ship_date=='') $country_ship_date_cond=''; else $country_ship_date_cond2=" and a.country_ship_date='$country_ship_date'";
  
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
			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls, pro_garments_production_mst mst where mst.id=pdtls.mst_id and mst.embel_name='$embelName' and pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name=3 and cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur, pro_garments_production_mst mst where mst.id=cur.mst_id and mst.embel_name='$embelName' and cur.production_type=3 and cur.is_deleted=0 ) as reject_qty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
		}
		else
		{
			$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
					sum(CASE WHEN c.production_type=2 then b.production_qnty ELSE 0 END) as production_qnty,
					sum(CASE WHEN c.production_type=3 and c.embel_name=3 then b.production_qnty ELSE 0 END) as cur_production_qnty,
					sum(CASE WHEN c.production_type=3 then b.reject_qty ELSE 0 END) as reject_qty
					from wo_po_color_size_breakdown a
					left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
					left join pro_garments_production_mst c on c.id=b.mst_id
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";
		}
		$colorResult = sql_select($sql);
	}
	else if( $variableSettings==3 ) //color and size level
	{
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
			sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
			sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
			sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty
			from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.embel_name=3 and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");

		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
		}
		unset($dtlsData);
		//print_r($color_size_qnty_array);

		$sql = "SELECT a.color_order,  a.id,size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
			from wo_po_color_size_breakdown a
			where a.po_break_down_id='$po_id' $country_ship_date_cond2 and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order,a.size_order";
		$colorResult = sql_select($sql);
	}
	else // by default color and size level
	{
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
			sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
			sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty
			from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.embel_name=3 and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");

		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
		}
		unset($dtlsData);
		//print_r($color_size_qnty_array);

		$sql = "SELECT a.color_order, a.id,a.size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
			from wo_po_color_size_breakdown a
			where a.po_break_down_id='$po_id' $country_ship_date_cond2 and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order,a.size_order";

		$colorResult = sql_select($sql);
	}
	// echo $sql;die();
	//$colorResult = sql_select($sql);
	//print_r($sql);
	if($variableSettingsRej!=1) $disable=""; else $disable="disabled";

	$colorHTML=""; $colorID=''; $chkColor = array(); $i=0; $totalQnty=0;
	foreach($colorResult as $color)
	{
		if( $variableSettings==2 ) // color level
		{
			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onblur="fn_colorRej_total('.($i+1).')  '.$disable.'"></td></tr>';
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
				//$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
				$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_'.$color[csf("color_number_id")].'">';
				$chkColor[] = $color[csf("color_number_id")];
			}
			//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
			$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

			$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
			$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];

			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';
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
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');

	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[3];
	$type = $dataArr[4];

	if($type==1) $embel_name="%%"; else $embel_name = $dataArr[2];
	?>

    <div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="30">ID</th>
                <th width="140">Item Name</th>
                <th width="110">Country</th>
                <th width="80">Production Date</th>
                <th width="80">Production Qty</th>
                <th width="80" >Reject Qnty</th>
                <th width="120">Serving Company</th>
                <th width="100">Location</th>
                <th width="50">Color Type</th>
                <th>Challan No</th>
            </thead>
        </table>
    </div>
	<div style="width:100%;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_search">
		<?php

		 //and embel_name like '$embel_name'
 			$i=1;
			$total_production_qnty=0;
			$sqlResult =sql_select("SELECT id, po_break_down_id,embel_name,production_source, item_number_id, country_id, production_date, production_quantity, reject_qnty, production_source, serving_company, location, challan_no from pro_garments_production_mst where po_break_down_id='$po_id' and production_type='3' and embel_name=3 and status_active=1 and is_deleted=0 order by id");//and item_number_id='$item_id' and country_id='$country_id' change in 29/10/2019 for libas

			$sql_color_type=sql_select("SELECT a.id,b.color_type_id from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id='$po_id' and a.embel_name=3  and b.production_type=3 and a.production_type=3 and b.status_active=1 and b.is_deleted=0  group by a.id,b.color_type_id ");
			foreach($sql_color_type as $k=>$v)
			{
				$mst_id_wise_color[$v[csf("id")]]=$color_type[$v[csf("color_type_id")]];
			}
			//$color_type_id=$sql_color_type[0][csf("color_type_id")];
			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                $total_production_qnty+=$selectResult[csf('production_quantity')];
				//
				?>
				<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<?=$selectResult[csf('id')]; ?>,'populate_receive_form_data','requires/wash_receive_entry_controller');"  >
					<td width="30" align="center"><input type="checkbox" id="tbl_<?=$i; ?>" onClick="fnc_checkbox_check(<?=$i; ?>);" />&nbsp;&nbsp;&nbsp;<? //echo $i; ?>
					   <input type="hidden" id="mstidall_<?=$i; ?>" value="<?=$selectResult[csf('id')]; ?>" style="width:30px"/>
					   <input type="hidden" id="emblname_<?=$i; ?>" name="emblname[]" width="30" value="<?=$selectResult[csf('embel_name')]; ?>" />
						<input type="hidden" id="productionsource_<?=$i; ?>" width="30" value="<?=$selectResult[csf('production_source')]; ?>" />
					</td>
					<td width="30" align="center"><?=$selectResult[csf('id')]; ?></td>
					<td width="140" style="word-break: break-all;"><?=$garments_item[$selectResult[csf('item_number_id')]]; ?></td>
					<td width="110" style="word-break: break-all;"><?=$country_library[$selectResult[csf('country_id')]]; ?></td>
					<td width="80" style="word-break: break-all;"><?=change_date_format($selectResult[csf('production_date')]); ?></td>
					<td width="80" align="center"><?=$selectResult[csf('production_quantity')]; ?></td>
					<td width="80" align="center"><?=$selectResult[csf('reject_qnty')]; ?></td>
					<?
						$source= $selectResult[csf('production_source')];
						if($source==1) $serving_company= $company_arr[$selectResult[csf('serving_company')]];
						else $serving_company= $supplier_arr[$selectResult[csf('serving_company')]];
					 ?>
					<td width="120" style="word-break: break-all;"><?=$serving_company; ?></td>
					<td width="100" style="word-break: break-all;"><?=$location_arr[$selectResult[csf('location')]]; ?></td>
					<td width="50" style="word-break: break-all;"><?=$mst_id_wise_color[$selectResult[csf("id")]]; ?></td>
					<td style="word-break: break-all;"><?=$selectResult[csf('challan_no')]; ?></td>
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
        <script>setFilterGrid("tbl_search",-1); </script>
	</div>
	<?
	exit();
}

if($action=="show_country_listview")
{
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="100">Item Name</th>
            <th width="80">Country</th>
            <th width="60">Country Ship Date</th>
            <th width="65">Order Qty.</th>
            <th>Rcv. Qty.</th>
        </thead>
		<?
		$issue_qnty_arr=sql_select("SELECT a.po_break_down_id, a.item_number_id, a.country_id, b.production_qnty as cutting_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$data' and a.production_type=3 and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$issue_data_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]+=$row[csf("cutting_qnty")];
		}
		$i=1;
		// $sqlResult =sql_select("SELECT po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		$sqlResult = sql_select("SELECT po_break_down_id, item_number_id, country_id, country_ship_date,pack_type, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty, max(cutup) as cutup from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active in(1,2,3) and is_deleted=0 group by po_break_down_id, item_number_id, country_id,country_ship_date,pack_type order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$issue_qnty=$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			?>
			<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<?=$row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>,'<? echo $row[csf('country_ship_date')] ?>' );">
				<td width="20"><?=$i; ?></td>
				<td width="100" style="word-break: break-all;"><?=$garments_item[$row[csf('item_number_id')]]; ?></td>
				<td width="80" style="word-break: break-all;"><?=$country_library[$row[csf('country_id')]]; ?>&nbsp;</td>
				<td width="60" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right" width="65"><?=$row[csf('order_qnty')]; ?></td>
                <td align="right"><?=$issue_qnty; ?></td>
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
	//production type=2 come from array
	$sqlResult =sql_select("SELECT country_ship_date, id,company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_source, production_type, entry_break_down_type, break_down_type_rej, production_hour, sewing_line, supervisor, carton_qty, remarks, floor_id, alter_qnty, reject_qnty, total_produced, yet_to_produced, wo_order_id, currency_id, exchange_rate, rate, sending_location, sending_company from pro_garments_production_mst where id='$data' and production_type='3' and status_active=1 and is_deleted=0 order by id");
    
	$color_type_val=sql_select("SELECT b.color_type_id  from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.production_type=3 and b.production_type=3 and a.status_active=1 and b.status_active=1 and  a.id='$data' group by b.color_type_id ");
	$country_ship_date = $sqlResult[0][csf('country_ship_date')];
	if($country_ship_date=='') $country_ship_date_cond=""; else $country_ship_date_cond="and a.country_ship_date='$country_ship_date'";
   
  	//echo "sdfds".$sqlResult;die;
	foreach($sqlResult as $result)
	{
		echo "$('#txt_receive_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/wash_receive_entry_controller', ".$result[csf('production_source')].", 'load_drop_down_emb_receive', 'emb_company_td' );\n";
		echo "$('#cbo_emb_company').val('".$result[csf('serving_company')]."');\n";
		echo "load_drop_down( 'requires/wash_receive_entry_controller', ".$result[csf('po_break_down_id')].", 'load_drop_down_color_type', 'color_type_td' );\n";
		echo "$('#cbo_color_type').val('".$color_type_val[0][csf("color_type_id")]."');\n";

		$location_company=0;
		if($result[csf('production_source')]==1) $location_company=$result[csf('serving_company')];
		else $location_company=$result[csf('company_id')];

		echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";
		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";
		echo "load_drop_down( 'requires/wash_receive_entry_controller',".$location_company.", 'load_drop_down_location', 'location_td' );";
		echo "$('#cbo_location').val('".$result[csf('location')]."');\n";
		echo "load_drop_down( 'requires/wash_receive_entry_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_embel_name').val('".$result[csf('embel_name')]."');\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";
		echo "$('#cbo_sending_location').val('".$result[csf('sending_location')]."*".$result[csf('sending_company')]."');\n";
		echo "load_drop_down( 'requires/wash_receive_entry_controller', '".$result[csf('embel_name')].'**'.$result[csf('po_break_down_id')]."', 'load_drop_down_emb_receive_type', 'emb_type_td' );\n";
		echo "$('#cbo_embel_type').val('".$result[csf('embel_type')]."');\n";

 		echo "$('#txt_receive_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_reject_qty').val('".$result[csf('reject_qnty')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";

		if($result[csf('production_source')]==3)
		{
			echo "load_drop_down( 'requires/wash_receive_entry_controller', '".$result[csf('company_id')]."_".$result[csf('serving_company')]."_".$result[csf('po_break_down_id')]."_".$result[csf('embel_name')]."_".$result[csf('item_number_id')]."', 'load_drop_down_workorder', 'workorder_td' );\n";

			//load_drop_down( 'requires/cutting_update_controller', company+"_"+supplier_id+"_"+po_break_down_id, 'load_drop_down_workorder', 'workorder_td' );
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
				echo "$('#workorder_rate_td').text('');\n";
		}

		$dataArray=sql_select("select SUM(CASE WHEN production_type=2 THEN production_quantity END) as totalCutting,SUM(CASE WHEN production_type=3 THEN production_quantity ELSE 0 END) as totalPrinting from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and embel_name=3 and country_id=".$result[csf('country_id')]." and is_deleted=0");
 		foreach($dataArray as $row)
		{
			echo "$('#txt_issue_qty').attr('placeholder','".$row[csf('totalCutting')]."');\n";
 			echo "$('#txt_issue_qty').val('".$row[csf('totalCutting')]."');\n";
			echo "$('#txt_cumul_receive_qty').val('".$row[csf('totalPrinting')]."');\n";
			$yet_to_produced = $row[csf('totalCutting')]-$row[csf('totalPrinting')];
			echo "$('#txt_yet_to_receive').val('".$yet_to_produced."');\n";
		}
		
		$defect_sql=sql_select("SELECT id, po_break_down_id, defect_type_id, defect_point_id, defect_qty from pro_gmts_prod_dft where mst_id='".$result[csf('id')]."' and status_active=1 and is_deleted=0 and embel_name=".$result[csf('embel_name')]." and production_type='3'");
		// echo "SELECT id, po_break_down_id, defect_type_id, defect_point_id, defect_qty from pro_gmts_prod_dft where mst_id='".$result[csf('id')]."' and status_active=1 and is_deleted=0 and embel_name=".$result[csf('embel_name')]." and production_type='3'";die;
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

			$sql_dtls = sql_select("select color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
			foreach($sql_dtls as $row)
			{
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
				$rejectArr[$index] = $row[csf('reject_qty')];
				$rejectArr2[$index][$row[csf('color_size_break_down_id')]] = $row[csf('reject_qty')];
			}

			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{
					$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=2 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=3 and cur.is_deleted=0 ) as reject_qty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
				}
				else
				{
					$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
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
				$dtlsData = sql_select("select a.color_size_break_down_id,
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
				unset($dtlsData);

				$sql = "select a.color_order, a.id, a.size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
					from wo_po_color_size_breakdown a
					where a.po_break_down_id='$po_id' $country_ship_date_cond and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order, a.size_order";
			}
			else // by default color and size level
			{
				$dtlsData = sql_select("select a.color_size_break_down_id,
					sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
					sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty,
					sum(CASE WHEN a.production_type=3 then a.reject_qty ELSE 0 END) as reject_qty
					from pro_garments_production_dtls a, pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(2,3) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				}
				unset($dtlsData);
				//print_r($color_size_qnty_array);

				$sql = "select a.color_order,a.id,a.size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
					from wo_po_color_size_breakdown a
					where a.po_break_down_id='$po_id' $country_ship_date_cond and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order,a.size_order";
			}

			if($variableSettingsRej!=1) $disable=""; else $disable="disabled";

 			$colorResult = sql_select($sql);
 			//print_r($sql);die;
			$colorHTML=""; $colorID=''; $chkColor = array(); $i=0; $totalQnty=0; $colorWiseTotal=0;
			foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{
					$amount = $amountArr[$color[csf("color_number_id")]];
					$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onblur="fn_colorRej_total('.($i+1).')"></td></tr>';
					$totalQnty += $amount;
					$totalRejQnty += $rejectAmt;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{
					$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$amount = $amountArr[$index];
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;$colorWiseTotal=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						//$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
					//$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
					$rej_qnty=$rejectArr2[$index][$color[csf('id')]];

					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';
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

//pro_garments_production_mst 
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
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

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**2"; die;}

		$color_sizeID_arr=sql_select( "select id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and status_active=1 and is_deleted=0 order by id" );
		$colSizeID_arr=array();$i=0;
		foreach($color_sizeID_arr as $val){
			$colSizeID_arr[$i++]=$val[csf("id")];
		}

		$cbo_sending_location = explode("*",str_replace("'","",$cbo_sending_location));
		$sending_location = $cbo_sending_location[0];
		$sending_company = $cbo_sending_location[1];

		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
		//3 means print receive array
 		$field_array1="id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, entry_break_down_type, break_down_type_rej, remarks, floor_id, reject_qnty, total_produced, yet_to_produced, wo_order_id, currency_id, exchange_rate, rate, amount, inserted_by, insert_date, sending_location, sending_company, entry_form, status_active, is_deleted,country_ship_date";
		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_receive_qty);}
		else {$amount="";}
		$data_array1="(".$id.",".$cbo_company_name.",".$garments_nature.",".$txt_challan.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_emb_company.",".$cbo_location.",3,".$cbo_embel_type.",".$txt_receive_date.",".$txt_receive_qty.",3,".$sewing_production_variable.",".$embro_production_variable.",".$txt_remark.",".$cbo_floor.",".$txt_reject_qty.",".$txt_cumul_receive_qty.",".$txt_yet_to_receive.",".$cbo_work_order.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."',".$user_id.",'".$pc_date_time."','".$sending_location."','".$sending_company."',416,1,0,".$country_ship_date.")";
		//echo "10** insert into pro_garments_production_mst($field_array1)values".$data_array1;die;
 		//$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		//echo $data_array1;die;
		// pro_garments_production_dtls table entry here ----------------------------------///

		$embelName=3;


		$dtlsData = sql_select("select a.color_size_break_down_id,
			sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
			sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty
			from pro_garments_production_dtls a,pro_garments_production_mst b
			where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and b.embel_name='$embelName' and a.production_type in(2,3) group by a.color_size_break_down_id");
		$color_pord_data=array();
		foreach($dtlsData as $row)
		{
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}
		unset($dtlsData);

		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty, color_type_id, entry_form, status_active, is_deleted,cost_of_fab_per_pcs,cost_per_pcs,wo_rate_pcs";

		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			$color_sizeID_arr=sql_select( "select id, color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0 order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}

			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
			$rowExRej = explode("**",$colorIDvalueRej);
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorSizeRejIDArr = explode("*",$valR);
				//echo $colorSizeRejIDArr[0]; die;
				$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
			}

 			$rowEx = explode("**",$colorIDvalue);
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
				if($colSizeID_arr[$colorSizeNumberIDArr[0]])
				{
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

					//3 for Receive Print / Emb. Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );

					if($j==0)$data_array = "(".$dtls_id.",".$id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.",416,1,0,'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
					else $data_array .= ",(".$dtls_id.",".$id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.",416,1,0,'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
 		}

		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{
			$color_sizeID_arr=sql_select( "select id, size_number_id, color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0 order by size_number_id, color_number_id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf('id')];
			}
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
			$rowExRej = explode("***",$colorIDvalueRej);
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				$sizeID = $colorAndSizeRej_arr[0];
				$colorID = $colorAndSizeRej_arr[1];
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $sizeID.$colorID;
				$rejQtyArr[$index]=$colorSizeRej;
			}

 			$rowEx = explode("***",$colorIDvalue);
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
				if($colSizeID_arr[$index]!="")
				{
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
					//3 for Receive Print / Emb. Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

					if($j==0)$data_array = "(".$dtls_id.",".$id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.",416,1,0,'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
					else $data_array .= ",(".$dtls_id.",".$id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.",416,1,0,'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
		}
		//echo "INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array;die;
		$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);

		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
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
				$data_array_defect_reject.="(".$dftSp_id.",".$id.",3,".$hidden_po_break_down_id.",".$defectReject_type_id.",".$defectspPointId.",'".$defectsp_qty."',3,".$user_id.",'".$pc_date_time."')";
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
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".$wip_valuation_for_accounts;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID )
				{
					oci_commit($con);
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".$wip_valuation_for_accounts;
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

		// pro_garments_production_mst table data entry here //3 means print receive
 		$field_array1="production_source*serving_company*location*embel_name*embel_type*production_date*production_quantity*production_type*entry_break_down_type*break_down_type_rej*remarks*floor_id*reject_qnty*challan_no*total_produced*yet_to_produced*wo_order_id*currency_id *exchange_rate *rate*amount*updated_by*update_date*sending_location*sending_company";
		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_receive_qty);}
		else {$amount="";}
		$data_array1="".$cbo_source."*".$cbo_emb_company."*".$cbo_location."*".$cbo_embel_name."*".$cbo_embel_type."*".$txt_receive_date."*".$txt_receive_qty."*3*".$sewing_production_variable."*".$embro_production_variable."*".$txt_remark."*".$cbo_floor."*".$txt_reject_qty."*".$txt_challan."*".$txt_cumul_receive_qty."*".$txt_yet_to_receive."*".$cbo_work_order."*".$hidden_currency_id."*".$hidden_exchange_rate."*'".$rate."'*'".$amount."'*".$user_id."*'".$pc_date_time."'*'".$sending_location."'*'".$sending_company."'";


 		//$rID=sql_update("pro_garments_production_mst",$field_array,$data_array,"id","".$txt_mst_id."",1);
		//echo $data_array;die;
		// pro_garments_production_dtls table data entry here
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) // check is not gross level
		{
			$embelName=3;
			$dtlsData = sql_select("select a.color_size_break_down_id,
				sum(CASE WHEN a.production_type=2 then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=3 then a.production_qnty ELSE 0 END) as cur_production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b
				where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and b.embel_name='$embelName' and a.production_type in(2,3) and b.id<>$txt_mst_id group by a.color_size_break_down_id");
			$color_pord_data=array();
			foreach($dtlsData as $row)
			{
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}
			unset($dtlsData);

 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty, color_type_id, entry_form, status_active, is_deleted,cost_of_fab_per_pcs,cost_per_pcs,wo_rate_pcs";

			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id, color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0 order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//

				$rowExRej = explode("**",$colorIDvalueRej);
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorSizeRejIDArr = explode("*",$valR);
					//echo $colorSizeRejIDArr[0]; die;
					$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
				}

				$rowEx = explode("**",$colorIDvalue);
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
					if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
					{
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
						//3 for Receive Print / Emb. Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.",416,1,0,'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.",416,1,0,'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
						//$dtls_id=$dtls_id+1;
						$j++;
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
				$rowExRej = explode("***",$colorIDvalueRej);
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorAndSizeRej_arr = explode("*",$valR);
					$sizeID = $colorAndSizeRej_arr[0];
					$colorID = $colorAndSizeRej_arr[1];
					$colorSizeRej = $colorAndSizeRej_arr[2];
					$index = $sizeID.$colorID;
					$rejQtyArr[$index]=$colorSizeRej;
				}

				$rowEx = explode("***",$colorIDvalue);
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
					if($colSizeID_arr[$index]!="")
					{
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

						//3 for Receive Print / Emb. Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.",416,1,0,'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",3,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.",416,1,0,'".$wash_rate."','".$cost_per_pcs."','".$emb_wo_rate."')";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
				}
			}

 			//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);

		}//end cond

		$dtlsrDelete = execute_query("UPDATE pro_garments_production_dtls SET STATUS_ACTIVE=0,IS_DELETED=1 where mst_id=$txt_mst_id",1);

		$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1); //echo $rID;die;

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
				$data_array_defect_reject.="(".$dftSp_id.",".$txt_mst_id.",3,".$hidden_po_break_down_id.",".$defectReject_type_id.",".$defectspPointId.",'".$defectsp_qty."',3,".$user_id.",'".$pc_date_time."')";
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
		else if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID )
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".$wip_valuation_for_accounts;
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


	$sql="SELECT min(id) as id, min(company_id) as company_id, sum(production_quantity) as production_quantity,sum(reject_qnty) as  reject_qnty,  min(challan_no) as challan_no,min( po_break_down_id) as po_break_down_id, min(item_number_id) as item_number_id,min(entry_break_down_type) as entry_break_down_type,min(break_down_type_rej) as break_down_type_rej, min(country_id) as country_id,
 min(production_source) as production_source, min(serving_company) as serving_company, min( location) as location ,
	min(embel_name) as embel_name, min(embel_type) as embel_type, min(production_date) as production_date, min( production_type) as production_type, min(remarks) as remarks from pro_garments_production_mst where production_type=3 and id in($mst_id) and status_active=1 and is_deleted=0";
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
					$style_job=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"job_no");
					$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"style_ref_no");
					$buyer_id=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"buyer_name");
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
					$style_job=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"job_no");
					$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"style_ref_no");
					$buyer_id=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"buyer_name");
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
	$order_sql="SELECT a.style_ref_no, a.job_no,a.buyer_name, b.id, b.po_number, b.po_quantity from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
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
	            <td colspan="4"  width="300" align="center" style="font-size:24px;padding-right:300px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
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
	            <td colspan="4" width="300" align="center" style="font-size:14px;padding-right:312px"><u><strong>Challan/Gate Pass (<? echo $country_arr[$country]; ?>) </strong></u></td>
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
	        			$internal_ref=return_field_value("f.grouping"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$dataArray[0][csf('po_break_down_id')],"grouping");
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
		            <th width="60">Manual Cut No</th>
		            <th width="120">Remarks</th>
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
		                        <td> <? echo $val[csf("man_cutt_no")]; ?> </td>
		                        <td> <? echo $val[csf("remarks")]?> </td>
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
		        	<td colspan="8"> <strong>In Word : </strong> <? echo number_to_words( array_sum($tot_specific_size_qnty), 'Pc\'s' ); ?> </td>
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
