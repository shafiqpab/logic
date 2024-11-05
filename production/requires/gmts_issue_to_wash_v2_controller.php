<?
session_start();
include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];

if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
if (!function_exists('pre')) 
{
	function pre($arr)
	{
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
	}
}
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$country_library = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
$location_id = $userCredential[0]['LOCATION_ID'];
$location_credential_cond = "";

if ($location_id != '') {
	$location_credential_cond = " and id in($location_id)";
}

//************************************ Drop downs **************************************************
if ($action == "load_drop_down_po_location") 
{
	echo create_drop_down("cbo_location_name", 170, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "");
	exit();
}
if($action=="load_drop_down_wash_company")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
        echo create_drop_down( "cbo_emb_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", 0, "" );	
	}
	else if($data==1)
		echo create_drop_down( "cbo_emb_company", 170, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_drop_down( 'requires/gmts_issue_to_wash_v2_controller', this.value, 'load_drop_down_wash_location', 'wash_location_td' );",0,0 );
	else
		echo create_drop_down( "cbo_emb_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0 );

	exit();
}

if ($action == "load_drop_down_wash_location") 
{
    echo create_drop_down( "cbo_wash_location", 172, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
}

if ($action=="load_drop_down_floor")
{
	$explode_data = explode("*",$data);
	$sending_location = $explode_data[0];
	$sending_company = $explode_data[1]; 
 	echo create_drop_down( "cbo_floor", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id=$sending_location and production_process=5 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
	exit();
}

if($action=="load_drop_down_embro_issue_type")
{
	$data=explode("**",$data);
	$emb_name=$data[0];
	$po_id=$data[1];
 
	$embel_name_cond="LISTAGG(c.emb_type,',') WITHIN GROUP ( ORDER BY c.emb_type) as emb_type";
	$embl_type=return_field_value("$embel_name_cond","wo_po_break_down a, wo_po_details_master b, wo_pre_cost_embe_cost_dtls c","a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id=$po_id and c.emb_name=3","emb_type");

	if($emb_name==1) $conArr=$emblishment_print_type;
	else if($emb_name==2) $conArr=$emblishment_embroy_type;
	else if($emb_name==3) $conArr=$emblishment_wash_type;
	else if($emb_name==4) $conArr=$emblishment_spwork_type;
	else if($emb_name==5) $conArr=$emblishment_gmts_type;
	else $conArr=$blank_array;  
	if(count(explode(',',$embl_type))==1)
	{
		$selected =$embl_type;
	} 
	echo create_drop_down( "cbo_embel_type", 160, $conArr,"", 1, "--- Select Wash ---", $selected, "" ,"","$embl_type");

	exit();
}

if ($action=="load_drop_down_buyer")
{  
	echo create_drop_down( "cbo_buyer_name", 170, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type NOT IN (2)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- Select Buyer -", $selected, "" );     	 
	exit();
}

//============================================================================
									// POPUP START HERE
//============================================================================

//ORDER POPUP
if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
			$("#company_search_by").val(<?php echo $_REQUEST['company']; ?>);

        });

		function search_populate(str)
		{
			//alert(str);
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Order No";
				document.getElementById('search_by_td').innerHTML='<input onkeydown="getActionOnEnter(event)" type="text" name="txt_search_common" style="width:130px" class="text_boxes" id="txt_search_common" value="" />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				document.getElementById('search_by_td').innerHTML='<input	type="text" onkeydown="getActionOnEnter(event)"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==2)
			{
				document.getElementById('search_by_th_up').innerHTML="File no";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==3)
			{
				document.getElementById('search_by_th_up').innerHTML="Internal Ref.";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==4)
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
				document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}  
		}

		function js_set_value(id,item_id,po_qnty,plan_qnty,country_id)
		{
			$("#hidden_mst_id").val(id);
			$("#hidden_grmtItem_id").val(item_id);
			$("#hidden_po_qnty").val(po_qnty);
			$("#hidden_country_id").val(country_id);
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
						<th width="130" class="must_entry_caption">Company</th>
						<th width="130" class="must_entry_caption">Buyer Name</th>
						<th width="130">Search By</th>
						<th width="130" align="center" id="search_by_th_up">Enter Order Number</th>
						<th width="130" colspan="2">Shipment Date Range</th>
						<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
					</thead>
					<tr class="general">
						<td><? echo create_drop_down( "company_search_by", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "", 0 ); ?></td>
						<td><? echo create_drop_down( "txt_buyer_name", 130, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_id, "",1,0 ); ?></td> 
						<td>
							<?
							$searchby_arr=array(4=>"Job No",0=>"Order No",1=>"Style Ref.",2=>"File No",3=>"Internal Ref");
							echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
							?>
						</td>
						<td id="search_by_td"><input type="text" style="width:120px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" /></td>
						<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
						<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>
						<td>
							<input type="button" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('company_search_by').value+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('txt_buyer_name').value, 'create_po_search_list_view', 'search_div', 'gmts_issue_to_wash_v2_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" valign="middle" colspan="6">
							<? echo load_month_buttons(1);  ?>
							<input type="hidden" id="hidden_mst_id">
							<input type="hidden" id="hidden_grmtItem_id">
							<input type="hidden" id="hidden_po_qnty">
							<input type="hidden" id="hidden_country_id">
							<input type="hidden" id="hidden_company_id">
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
	if($ex_data[4]== 0)
	{
		echo "Please Select Company First."; die;
	}
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$buyer = $ex_data[6];
 	// $garments_nature = $ex_data[5];	
 	$garments_nature = 3;	
    $color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$country_library = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and b.file_no=trim('$txt_search_common')";
		else if(trim($txt_search_by)==3)
			$sql_cond =  " and b.grouping like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==4)
			$sql_cond =  " and a.job_no_prefix_num like '%".trim($txt_search_common)."%'";
 	}
	if($txt_date_from!="" || $txt_date_to!="")
	{
		$sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
	}
	if(trim($company)!="") $sql_cond .= " and a.company_name=$company";
	if(trim($buyer)!="") $sql_cond 	.= " and a.buyer_name=$buyer";

	$sql = "SELECT b.id, a.job_no_prefix_num, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.file_no, b.grouping, b.po_quantity, b.plan_cut
	from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_embe_cost_dtls c
	where
	a.id = b.job_id and a.id = c.job_id and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.shiping_status <>3 and c.emb_name=3 and c.cons_dzn_gmts>0 and a.garments_nature=$garments_nature $sql_cond
	group by b.id, a.job_no_prefix_num, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.file_no, b.grouping, b.po_quantity, b.plan_cut order by b.shipment_date ASC"; 
	$result = sql_select($sql);
	$po_id_arr = array();
	foreach ($result as $val)
	{
		$po_id_arr[$val['ID']] = $val['ID'];
	}
	$allPoIds = implode(",", $po_id_arr);
 	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');

	//$po_country_arr=return_library_array( "select po_break_down_id, $select_field"."_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	
	$countryCond="listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id)";

	$po_country_arr=return_library_array( "SELECT po_break_down_id, $countryCond as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by po_break_down_id",'po_break_down_id','country');
	


	$po_country_data_arr=array();
	$poCountryData=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty,color_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 and po_break_down_id in($allPoIds) group by po_break_down_id, item_number_id, country_id,color_number_id");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']]['po_qnty']+=$row['QNTY'];
		$po_country_data_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']]['plan_cut_qnty']+=$row['PLAN_CUT_QNTY'];
		$po_color_arr[$row["PO_BREAK_DOWN_ID"]].=','.$row["COLOR_NUMBER_ID"];
	}
	unset($poCountryData);

	$total_issu_qty_data_arr=array();
	$total_issu_qty_arr=sql_select( "SELECT po_break_down_id, item_number_id, country_id,production_type, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type in(2,5) and embel_name in(0,3) and po_break_down_id in($allPoIds) group by po_break_down_id, item_number_id, country_id,production_type");

	foreach($total_issu_qty_arr as $row)
	{
		$total_issu_qty_data_arr[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']][$row['PRODUCTION_TYPE']]+=$row['PRODUCTION_QUANTITY'];
	}
	?>
    <div style="width:1290px;">
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
                <th width="100">Sewing Output</th>
                <th width="80">Total Issue Qty</th>
                <th width="80">Balance</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:1290px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1272" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row["SET_BREAK_DOWN"]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

				$color=array_unique(explode(",",$po_color_arr[$row["ID"]]));
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

				$country=array_unique(explode(",",$po_country_arr[$row["ID"]]));
				//$country=explode(",",$po_country_arr[$row["ID"]]);
				$numOfCountry = count($country);

				for($k=0; $k<$numOfItem; $k++)
				{
					if($row["total_set_qnty"]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}
					else
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}

					foreach($country as $country_id)
					{
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						//$po_qnty=$row["PO_QUANTITY"]; $plan_cut_qnty=$row["PLAN_CUT"];
						$po_qnty=$po_country_data_arr[$row['ID']][$grmts_item][$country_id]['po_qnty'];
						$plan_cut_qnty=$po_country_data_arr[$row['ID']][$grmts_item][$country_id]['plan_cut_qnty'];

						?>
						<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<?=$row["ID"];?>,'<?=$grmts_item;?>','<?=$po_qnty;?>','<?=$plan_cut_qnty;?>','<?=$country_id;?>');" >
							<td width="30" align="center"><?=$i; ?></td>
							<td width="60" align="center"><?=change_date_format($row["SHIPMENT_DATE"]);?></td>
							<td width="50" align="center"><?=$row["JOB_NO_PREFIX_NUM"];?></td>
							<td width="100" title="<?=$color_name? $color_name:""?>" style="word-break: break-all;"><?=$row["PO_NUMBER"]; ?></td>
							<td width="80" style="word-break: break-all;"><?=$buyer_arr[$row["BUYER_NAME"]]; ?></td>
							<td width="100" style="word-break: break-all;"><?=$row["STYLE_REF_NO"]; ?></td>
                            <td width="80" style="word-break: break-all;"><?=$row["FILE_NO"]; ?></td>
                            <td width="80" style="word-break: break-all;"><?=$row["GROUPING"]; ?></td>
							<td width="140" style="word-break: break-all;"><?=$garments_item[$grmts_item];?></td>
							<td width="100" style="word-break: break-all;"><?=$country_library[$country_id]; ?>&nbsp;</td>
							<td width="80" align="right"><?=$po_qnty; ?>&nbsp;</td>
							<td width="100" align="right"><?echo $total_sewing_out = $total_issu_qty_data_arr[$row['ID']][$grmts_item][$country_id][5]; ?>&nbsp;</td>
                            <td width="80" align="right"><?=$total_cut_qty=$total_issu_qty_data_arr[$row['ID']][$grmts_item][$country_id][2]; ?>&nbsp;</td>
                            <td width="80" align="right"><?php $balance=$total_sewing_out-$total_cut_qty; echo $balance; ?>&nbsp;</td>
							<td style="word-break: break-all;"><?=$company_arr[$row["COMPANY_NAME"]];?> </td>
						</tr>
						<?
						$i++;
					}
				}
            }
   		?>
        </table>
    </div>
	<?
	exit();
}
if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("SELECT service_process_id,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result["SERVICE_PROCESS_ID"].");\n";
		echo "$('#styleOrOrderWisw').val(".$result["PRODUCTION_ENTRY"].");\n";
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

if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$embel_name = $dataArr[2];
	$country_id = $dataArr[3];
	$order_sql = "SELECT a.id, a.po_quantity, a.plan_cut, a.po_number,a.grouping, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, c.item_number_id as gmts_item_id, b.order_uom, b.job_no,a.packing, b.location_name,c.country_ship_date, d.emb_name
	from wo_po_break_down a, wo_po_details_master b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d
	where d.cons_dzn_gmts>0 and a.job_id=b.id and b.id=d.job_id and a.id=c.po_break_down_id and a.id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and d.emb_name=3 and a.status_active=1  and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0  group by a.id, a.po_quantity, a.plan_cut, a.po_number,a.grouping, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, c.item_number_id, b.order_uom, b.job_no,a.packing, b.location_name,c.country_ship_date, d.emb_name";
	// echo $order_sql ; die;
	$res = sql_select($order_sql);
	foreach($dtlsData as $row)
	{
		$color_size_qnty_array[$row['COLOR_SIZE_BREAK_DOWN_ID']]['iss']= $row['PRODUCTION_QNTY'];
		$color_size_qnty_array[$row['COLOR_SIZE_BREAK_DOWN_ID']]['rcv']= $row['CUR_PRODUCTION_QNTY'];
	}
	
 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result['PO_NUMBER']."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result['ID']."');\n";
		echo "$('#cbo_buyer_name').attr('disabled','disabled');\n";
		echo "$('#txt_style_no').val('".$result['STYLE_REF_NO']."');\n";
		echo "$('#txt_job_no').val('".$result['JOB_NO']."');\n";
		echo "$('#txt_ir').val('".$result['GROUPING']."');\n";
		echo "$('#txt_country_ship_date').val('".$result['COUNTRY_SHIP_DATE']."');\n";
		echo "$('#txt_pack_type').val('".$result['PACKING']."');\n";  
  	}
	$data_sql = "SELECT 
		SUM ( CASE WHEN a.production_type = 1 THEN a.production_qnty ELSE 0 END) AS total_cut_qty,
		SUM ( CASE WHEN a.production_type = 5 THEN a.production_qnty ELSE 0 END) AS total_sew_out,
		SUM ( CASE WHEN a.production_type = 2 AND b.embel_name = 3 and a.entry_form=645 THEN a.production_qnty ELSE 0 END) AS total_wash
		FROM pro_garments_production_dtls a, pro_garments_production_mst b
		WHERE a.status_active = 1
		and b.status_active=1  and a.is_deleted=0 and  b.is_deleted=0 and a.mst_id=b.id and b.po_break_down_id=$po_id and b.item_number_id=$item_id and b.country_id=$country_id and a.production_type in(1,2,5) " ;
	$dataArray=sql_select($data_sql); 
	foreach($dataArray as $row)
	{	
		$ttl_cut_qty = $row['TOTAL_CUT_QTY'];
		$ttl_sew_out = $row['TOTAL_SEW_OUT'];
		$ttl_wash	 = $row['TOTAL_WASH'];
		echo "$('#txt_cut_qty').val('".$ttl_cut_qty."');\n";
		echo "$('#txt_sewing_qty').val('".$ttl_sew_out."');\n";
		echo "$('#txt_cum_issue_qty').attr('placeholder','".$ttl_wash."');\n";
		echo "$('#txt_cum_issue_qty').val('".$ttl_wash."');\n";
		$yet_to_wash = $ttl_sew_out-$ttl_wash;
		echo "$('#yet_to_issue').attr('placeholder','".$yet_to_wash."');\n";
		echo "$('#yet_to_issue').val('".$yet_to_wash."');\n";
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

	// if($variableSettings==1)
	// {
	// 	die;
	// }

	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

	//#############################################################################################//
	//order wise - color level, color and size level

	//$variableSettings=2;

	if( $variableSettings==2 ) // color level
	{
		
		$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
				sum(CASE WHEN c.production_type=5 then b.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN c.production_type=2 and c.embel_name=3  and c.entry_form=645 then b.production_qnty ELSE 0 END) as cur_production_qnty
				from wo_po_color_size_breakdown a
				left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
				left join pro_garments_production_mst c on c.id=b.mst_id
				where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.item_number_id, a.color_number_id";
		$colorResult = sql_select($sql);
	}
	else if( $variableSettings==3 ) //color and size level
	{
		$dtlsData =sql_select("SELECT a.color_size_break_down_id,
		SUM (
			CASE WHEN a.production_type = 5 THEN a.production_qnty ELSE 0 END)
			AS production_qnty,
		SUM (
			CASE
				WHEN a.production_type = 2 AND b.embel_name = '3'
				THEN
					a.production_qnty
				ELSE
					0
			END)
			AS cur_production_qnty
			  FROM pro_garments_production_dtls a, pro_garments_production_mst b
			  WHERE a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(5,2) and a.status_active = 1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.color_size_break_down_id" );

		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row['COLOR_SIZE_BREAK_DOWN_ID']]['iss']= $row['PRODUCTION_QNTY'];
			$color_size_qnty_array[$row['COLOR_SIZE_BREAK_DOWN_ID']]['rcv']= $row['CUR_PRODUCTION_QNTY'];
		}
		unset($dtlsData);
		//print_r($color_size_qnty_array);

		$sql = "SELECT id,size_order, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";
		$colorResult = sql_select($sql);
	}
	else
	{
		$dtlsData =sql_select("SELECT a.color_size_break_down_id,
		SUM (
			CASE WHEN a.production_type = 5 THEN a.production_qnty ELSE 0 END)
			AS production_qnty,
		SUM (
			CASE
				WHEN a.production_type = 2 AND b.embel_name = '3'
				THEN
					a.production_qnty
				ELSE
					0
			END)
			AS cur_production_qnty
			  FROM pro_garments_production_dtls a, pro_garments_production_mst b
			  WHERE a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(5,2) and a.status_active = 1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.color_size_break_down_id" );

		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row['COLOR_SIZE_BREAK_DOWN_ID']]['iss']= $row['PRODUCTION_QNTY'];
			$color_size_qnty_array[$row['COLOR_SIZE_BREAK_DOWN_ID']]['rcv']= $row['CUR_PRODUCTION_QNTY'];
		}
		unset($dtlsData);
		//print_r($color_size_qnty_array);

		$sql = "SELECT id,size_order, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
			from wo_po_color_size_breakdown
			where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";
		$colorResult = sql_select($sql);
	}
	//print_r($sql);

	$colorHTML=""; $colorID=''; $i=0; $totalQnty=0; $chkColor = array();
	foreach($colorResult as $color)
	{
		if( $variableSettings==2 ) // color level
		{
			$colorHTML .='<tr><td>'.$color_library[$color["COLOR_NUMBER_ID"]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color["PRODUCTION_QNTY"]-$color["CUR_PRODUCTION_QNTY"]).'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';
			$totalQnty += $color["PRODUCTION_QNTY"]-$color["CUR_PRODUCTION_QNTY"];
			$colorID .= $color["COLOR_NUMBER_ID"].",";
		}
		else //color and size level
		{
			if( !in_array( $color["COLOR_NUMBER_ID"], $chkColor ) )
			{
				if( $i!=0 ) $colorHTML .= "</table></div>";
				$i=0;
				$colorHTML .= '<h3 align="left" id="accordion_h'.$color["COLOR_NUMBER_ID"].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color["COLOR_NUMBER_ID"].'\', \'\',1)"> <span id="accordion_h'.$color["COLOR_NUMBER_ID"].'span">+</span>'.$color_library[$color["COLOR_NUMBER_ID"]].' : <span id="total_'.$color["COLOR_NUMBER_ID"].'"></span> </h3>';
				//$colorHTML .= '<div id="content_search_panel_'.$color["COLOR_NUMBER_ID"].'" style="display:none" class="accord_close"><table id="table_'.$color["COLOR_NUMBER_ID"].'">';
				$colorHTML .= '<div id="content_search_panel_'.$color["COLOR_NUMBER_ID"].'" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_'.$color["COLOR_NUMBER_ID"].'">';
				$chkColor[] = $color["COLOR_NUMBER_ID"];
			}
			//$index = $color["SIZE_NUMBER_ID"].$color["COLOR_NUMBER_ID"];
			$colorID .= $color["SIZE_NUMBER_ID"]."*".$color["COLOR_NUMBER_ID"].",";

			$iss_qnty=$color_size_qnty_array[$color['ID']]['iss'];
			$rcv_qnty=$color_size_qnty_array[$color['ID']]['rcv'];

			$colorHTML .='<tr><td>'.$size_library[$color["SIZE_NUMBER_ID"]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color["COLOR_NUMBER_ID"].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color["COLOR_NUMBER_ID"].','.($i+1).')"><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color["COLOR_NUMBER_ID"].($i+1).'"  class="text_boxes_numeric" style="width:80px" value="'.$color["ORDER_QUANTITY"].'" readonly disabled></td></tr>';
		}
		$i++;
	}
	//echo $colorHTML;die;
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><tr><th width="100">Color</th><th width="80">Quantity</th></tr><tr> <th colspan="2"><div style="padding-left: 30px;text-align:left"><input type="checkbox" onClick="active_placeholder_qty_color(' . $color[csf("color_number_id")] . ')" id="set_all">&nbsp;<label for="set_all">Available Qty Auto Fill</label></div></th> </tr></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
	exit();
}

/* if($action=="show_dtls_listview")
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
	$sql = "SELECT a.id, a.po_break_down_id, a.item_number_id, a.company_id, a.country_id, a.production_date, a.production_quantity, a.reject_qnty, a.production_source, a.serving_company, a.location, a.embel_name, a.embel_type,b.po_number,c.style_ref_no,a.delivery_mst_id from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and c.id=b.job_id and a.po_break_down_id=$po_id and a.production_type=2 and a.entry_form=645 and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id";
	// echo $sql;
	// pre($emblishment_wash_type);die;
	$sqlResult =sql_select($sql);
	 
	unset($sql_color_type);
	if(count($sqlResult)<1) die;
	ob_start();
	?>
    <div style="width:1000px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
            <thead>
	            <tr>
	                <th width="30">SL</th>
	                <th width="100">Style</th>
	                <th width="80">Order</th>
	                <th width="150">Item Name</th>
	                <th width="120">Country</th>
	                <th width="80">Production Date</th>
	                <th width="80">Production Qty</th>
	                <th width="150">Serving Company</th>
	                <th width="120" >Location</th> 
	                <th width="80" >Wash Type</th> 
	             </tr>
            </thead>
        </table>
    </div>
	<div style="width:1000px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000px" class="rpt_table" id="tbl_search">
		<?php
			$i=1;
			$total_production_qnty=0; 
			foreach($sqlResult as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
               	$total_production_qnty+=$row['PRODUCTION_QUANTITY'];
 				?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" >
                    <td width="30" align="center"> <?=$i; ?>
						<!-- &nbsp;
						<input type="checkbox" id="tbl_<?=$i; ?>" onClick="fnc_checkbox_check(<?=$i; ?>);"  />&nbsp; -->
						<input type="hidden" id="mstidall_<?=$i; ?>" value="<?=$row['ID']; ?>" style="width:30px"/>
						<input type="hidden" id="emblname_<?=$i; ?>" name="emblname[]" value="<?=$row['EMBEL_NAME']; ?>" />
						<input type="hidden" id="embltype_<?=$i; ?>" name="embltype[]" value="<?=$row['EMBEL_TYPE']; ?>" />
						<input type="hidden" id="productionsource_<?=$i; ?>" value="<?=$row['PRODUCTION_SOURCE']; ?>" />

						<input type="hidden" id="serving_company_<?=$i; ?>" value="<?=$row['SERVING_COMPANY']; ?>" />
						<input type="hidden" id="location_<?=$i; ?>" value="<?=$row['LOCATION']; ?>" />

                    </td>
					<td width="100" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?= $row['STYLE_REF_NO']?></p></td>
					<td width="80" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?= $row['PO_NUMBER']?></p></td> 
                    <td width="150" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=$garments_item[$row['ITEM_NUMBER_ID']]; ?></p></td>
                    <td width="120" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=$country_library[$row['COUNTRY_ID']]; ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=change_date_format($row['PRODUCTION_DATE']); ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=$row['PRODUCTION_QUANTITY']; ?></p></td>
                    <?php
                            $source= $row['PRODUCTION_SOURCE'];
                            if($source==3) $serving_company= $supplier_arr[$row['SERVING_COMPANY']];
                            else $serving_company= $company_arr[$row['SERVING_COMPANY']];
                     ?>
                    <td width="150" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=$serving_company; ?></p></td>
                    <td width="120" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=$location_arr[$row['LOCATION']]; ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=$emblishment_wash_type[$row['EMBEL_TYPE']]; ?></p></td>
                     
                </tr>
            <?php
                $i++;
			}
			?>
		</table>
        <script>setFilterGrid("tbl_search",-1); </script>
        </div>
	<?
	exit();
} */
if($action=="show_dtls_listview_from_sys_popup")
{
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');

	$delivery_mst_id = $data; 
	if($type==1) $embel_name="%%"; else $embel_name = $dataArr[2];
	$sql = "SELECT a.id, a.po_break_down_id, a.item_number_id, a.company_id, a.country_id, a.production_date, a.production_quantity, a.reject_qnty, a.production_source, a.serving_company, a.location, a.embel_name, a.embel_type,b.po_number,c.style_ref_no,a.delivery_mst_id from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and c.id=b.job_id and a.delivery_mst_id=$delivery_mst_id and a.production_type=2 and a.entry_form=645 and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id";
	// echo $sql; production_quantity
	// pre($emblishment_wash_type);die;
	$sqlResult =sql_select($sql);
	 
	unset($sql_color_type);
	if(count($sqlResult)<1) die;
	ob_start();
	?>
    <div style="width:1000px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
            <thead>
	            <tr>
	                <th width="30">SL</th>
	                <th width="100">Style</th>
	                <th width="80">Order</th>
	                <th width="150">Item Name</th>
	                <th width="120">Country</th>
	                <th width="80">Production Date</th>
	                <th width="80">Production Qty</th>
	                <th width="150">Serving Company</th>
	                <th width="120">Location</th>
	                <th width="80">Wash Type</th> 
	             </tr>
            </thead>
        </table>
    </div>
	<div style="width:1000px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000px" class="rpt_table" id="tbl_search">
		<?php
			$i=1;
			$total_production_qnty=0; 
			foreach($sqlResult as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
               	$total_production_qnty+=$row['PRODUCTION_QUANTITY'];
 				?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" >
                    <td width="30" align="center"> <?=$i; ?>
						<!-- &nbsp;
						<input type="checkbox" id="tbl_<?=$i; ?>" onClick="fnc_checkbox_check(<?=$i; ?>);"  />&nbsp; -->
						<input type="hidden" id="mstidall_<?=$i; ?>" value="<?=$row['ID']; ?>" style="width:30px"/>
						<input type="hidden" id="emblname_<?=$i; ?>" name="emblname[]" value="<?=$row['EMBEL_NAME']; ?>" />
						<input type="hidden" id="embltype_<?=$i; ?>" name="embltype[]" value="<?=$row['EMBEL_TYPE']; ?>" />
						<input type="hidden" id="productionsource_<?=$i; ?>" value="<?=$row['PRODUCTION_SOURCE']; ?>" />

						<input type="hidden" id="serving_company_<?=$i; ?>" value="<?=$row['SERVING_COMPANY']; ?>" />
						<input type="hidden" id="location_<?=$i; ?>" value="<?=$row['LOCATION']; ?>" />

                    </td>
					<td width="100" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?= $row['STYLE_REF_NO']?></p></td>
					<td width="80" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?= $row['PO_NUMBER']?></p></td> 
                    <td width="150" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=$garments_item[$row['ITEM_NUMBER_ID']]; ?></p></td>
                    <td width="120" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=$country_library[$row['COUNTRY_ID']]; ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=change_date_format($row['PRODUCTION_DATE']); ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=$row['PRODUCTION_QUANTITY']; ?></p></td>
                    <?php
                            $source= $row['PRODUCTION_SOURCE'];
                            if($source==3) $serving_company= $supplier_arr[$row['SERVING_COMPANY']];
                            else $serving_company= $company_arr[$row['SERVING_COMPANY']];
                     ?>
                    <td width="150" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=$serving_company; ?></p></td>
                    <td width="120" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=$location_arr[$row['LOCATION']]; ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<?=$row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['DELIVERY_MST_ID']; ?>');"><p><?=$emblishment_wash_type[$row['EMBEL_TYPE']]; ?></p></td>
                     
                </tr>
            <?php
                $i++;
			}
			?>
		</table>
        <script>setFilterGrid("tbl_search",-1); </script>
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

		$issue_qnty_arr=sql_select("SELECT a.po_break_down_id, a.item_number_id, a.country_id, b.production_qnty as cutting_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$data' and a.production_type=2 and a.embel_name=3 and a.entry_form=645 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$issue_data_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			$issue_data_arr[$row["PO_BREAK_DOWN_ID"]][$row["COUNTRY_ID"]][$row["ITEM_NUMBER_ID"]]+=$row["CUTTING_QNTY"];
		}
		unset($issue_qnty_arr);

		$sqlResult =sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$cutting_qnty=0;
			$issue_qnty=$issue_data_arr[$row["PO_BREAK_DOWN_ID"]][$row["COUNTRY_ID"]][$row["ITEM_NUMBER_ID"]];
			?>
			<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<?=$row['PO_BREAK_DOWN_ID'].",".$row['ITEM_NUMBER_ID'].",".$row['COUNTRY_ID'].",".$row['ORDER_QNTY'].",".$row['PLAN_CUT_QNTY']; ?>);">
				<td width="20" align="center"><?=$i; ?></td>
				<td width="100"><p><?=$garments_item[$row['ITEM_NUMBER_ID']]; ?></p></td>
				<td width="80"><p><?=$country_library[$row['COUNTRY_ID']]; ?>&nbsp;</p></td>
				<td width="60" align="center"><? if($row['COUNTRY_SHIP_DATE']!="0000-00-00") echo change_date_format($row['COUNTRY_SHIP_DATE']); ?>&nbsp;</td>
				<td align="right" width="70"><?=$row['ORDER_QNTY']; ?></td>
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



if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	// pre($process); die;
	extract(check_magic_quote_gpc( $process ));
	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$cbo_company_name");
	if($is_projected_po_allow ==2)
	{
		$is_projected_po=return_field_value("is_confirmed","wo_po_break_down","status_active in(1,2,3) and id=$hidden_po_break_down_id");
		if($is_projected_po==2)
		{
			echo "786**Projected PO is not allowed to production. Please check variable settings";
			die();
		}
	}

	$is_control=return_field_value("is_control","variable_settings_production","company_name=$cbo_company_name and variable_list=33 and page_category_id=28","is_control"); 


	if ($operation==0) // Insert Here----------------------------------------------------------
	{  
		
		$con = connect(); 
		$cbo_sending_location = explode("*",str_replace("'","",$cbo_sending_location));
		$sending_location = $cbo_sending_location[0];
		$sending_company = $cbo_sending_location[1];
		// ========================================================================================
										//Create System ID 
		// ========================================================================================
		if (str_replace("'", "", $txt_system_no) == "") 
		{
            $year_cond="to_char(insert_date,'YYYY')";

          	$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_name,'GIW',645,date("Y",time()),0,0,2,3,0 ));
          	$field_array_delivery = "id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id,buyer_id, production_type,embel_name, production_source, delivery_date,entry_form,working_company_id,working_location_id,location_id,floor_id,sending_company,sending_location,remarks,inserted_by, insert_date";
            $mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq", "pro_gmts_delivery_mst", $con );
            $data_array_delivery = "(" . $mst_id . ",'" . $new_sys_number[1] . "','" .(int) $new_sys_number[2] . "','" . $new_sys_number[0] . "', " . $cbo_company_name . ", " . $cbo_buyer_name . ",2,3," . $cbo_source . "," . $txt_issue_date . ",645,".$cbo_emb_company.",".$cbo_wash_location.",".$cbo_location_name."," . $cbo_floor.",'". $sending_company."',". $sending_location."," . $txt_remark . "," . $user_id . ",'" . $pc_date_time . "')";
            $challan_no =(int) $new_sys_number[2];
            $txt_system_no = $new_sys_number[0];

        } 
        else 
        {
            $mst_id = str_replace("'", "", $txt_system_id);
            $txt_chal_no = explode("-", str_replace("'", "", $txt_system_no));
            $challan_no = (int)$txt_chal_no[3];

            $field_array_delivery = "company_id*production_source*delivery_date*working_company_id*working_location_id*location_id*floor_id*sending_company*sending_location*remarks*updated_by*update_date";
            $data_array_delivery = "".$cbo_company_name."*". $cbo_source."*". $txt_issue_date."*".$cbo_emb_company."*".$cbo_wash_location."*".$cbo_location_name."*" . $cbo_floor."*". $sending_company."*". $sending_location."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";

        }

		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq", "pro_garments_production_mst", $con );
		// ========================================================================================
										//INSERT DATA INTO  PRO_GARMENTS_PRODUCTION_MST
		// ========================================================================================
		$field_array1="id,delivery_mst_id, company_id, garments_nature, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_type, entry_break_down_type, remarks, floor_id, total_produced, yet_to_produced, inserted_by, insert_date, sending_location, sending_company, entry_form,pack_type, status_active, is_deleted";
		$data_array1="(".$id.",".$mst_id.",".$cbo_company_name.",".$garments_nature.",".$challan_no.",".$hidden_po_break_down_id.", ".$cbo_item_name.",".$cbo_country_name.", ".$cbo_source.",".$cbo_emb_company.",".$cbo_wash_location.",3,".$cbo_embel_type.",".$txt_issue_date.",".$txt_issue_qty.",2,".$sewing_production_variable.",".$txt_remark.",".$cbo_floor.",".$txt_cum_issue_qty.",".$yet_to_issue.",".$user_id.",'".$pc_date_time."','".$sending_location."','".$sending_company."',645,".$txt_pack_type.",1,0)";
 
		// echo $data_array."---".$rID;die;\

		$dtlsData = sql_select("select a.color_size_break_down_id,
			sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
			sum(CASE WHEN a.production_type=2 and b.embel_name=$embelName and b.entry_form=645 then a.production_qnty ELSE 0 END) as cur_production_qnty
			from pro_garments_production_dtls a,pro_garments_production_mst b
			where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(5,2)
			group by a.color_size_break_down_id");

		$color_pord_data=array();
		foreach($dtlsData as $row)
		{
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}
		unset($dtlsData);

		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty,entry_form, status_active, is_deleted";
  		$dtlsrID=true;
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			$color_sizeID_arr=sql_select( "select id, color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0 order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
 			$rowEx = explode("**",$colorIDvalue);
 			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				
				if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
				{

					//2 for Issue to Print / Emb Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
					if($j==0) $data_array = "(".$dtls_id.",".$id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."',645,1,0)";
					else $data_array .= ",(".$dtls_id.",".$id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."',645,1,0)";
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
				$colSizeID_arr[$index]=$val[csf('id')];
			}

			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
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

				if($colSizeID_arr[$index]!="")
				{

					//2 for Issue to Print / Emb Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."',645,1,0)";
					else $data_array .= ",(".$dtls_id.",".$id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."',645,1,0)";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
		}
		// echo $txt_system_id.'**'; die; 
		if (str_replace("'", "", $txt_system_id) == "") 
		{ 
            $challanrID = sql_insert("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, 1);
        } 
        else 
        {
            $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
        }
		// echo  $challanrID ; die;
		$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);

		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}  
		if(str_replace("'","",$sewing_production_variable)!=1)
		{
			if($rID && $dtlsrID && $challanrID)
			{
				oci_commit($con);
				echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".$mst_id."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id)."**mst*".$rID ."**dtls*". $dtlsrID ."**cln*". $challanrID;
			}
		}
		else
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".$mst_id;
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
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();

		$cbo_sending_location = explode("*",str_replace("'","",$cbo_sending_location));
		$sending_location = $cbo_sending_location[0];
		$sending_company = $cbo_sending_location[1];
		// =============================================================================================
		//											Update Muster Data
		// =============================================================================================
		$field_array_delivery = "company_id*production_source*delivery_date*working_company_id*working_location_id*location_id*floor_id*sending_company*sending_location*remarks*updated_by*update_date"; 
		$data_array_delivery = "".$cbo_company_name."*". $cbo_source."*". $txt_issue_date."*".$cbo_emb_company."*".$cbo_wash_location."*".$cbo_location_name."*" . $cbo_floor."*'". $sending_company."'*'". $sending_location."'*".$txt_remark."*".$user_id."*'".$pc_date_time."'";

		// pro_garments_production_mst table data entry here
		
 		$field_array1="production_source*serving_company*location*embel_type*production_date*production_quantity*production_type*entry_break_down_type*remarks*floor_id*total_produced*yet_to_produced*updated_by*update_date*sending_location*sending_company*pack_type";
		

		$data_array1="".$cbo_source."*".$cbo_emb_company."*".$cbo_wash_location."*".$cbo_embel_type."*".$txt_issue_date."*".$txt_issue_qty."*2*".$sewing_production_variable."*".$txt_remark."*".$cbo_floor."*".$txt_cum_issue_qty."*".$yet_to_issue."*".$user_id."*'".$pc_date_time."'*'".$sending_location."'*'".$sending_company."'*".$txt_pack_type."";
 		//$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		// echo $data_array1;die;

		// echo "INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;

		// pro_garments_production_dtls table data entry here
		$embelName=str_replace("'","",$cbo_embel_name);
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' )// check is not gross level
		{
			$dtlsData = sql_select("select a.color_size_break_down_id,
				sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=2 and b.embel_name=3 and b.entry_form=645 then a.production_qnty ELSE 0 END) as cur_production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b
				where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(5,2) and b.id<>$txt_mst_id
				group by a.color_size_break_down_id");

			$color_pord_data=array();
			foreach($dtlsData as $row)
			{
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}
			unset($dtlsData);

 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, entry_form, status_active, is_deleted";

			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{	
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name   and status_active=1 and is_deleted=0  order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}
				unset($color_sizeID_arr); 
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = explode("**",$colorIDvalue); 
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val); 
					if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
					{ 
						//2 for Issue to Print / Emb Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."',645,1,0)";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."',645,1,0)";
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
				unset($color_sizeID_arr);

				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
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

					if($colSizeID_arr[$index]!="")
					{
						//2 for Issue to Print / Emb Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."',645,1,0)";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",2,'".$colSizeID_arr[$index]."','".$colorSizeValue."',645,1,0)";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
				}
			}
		}//end cond
		// echo $data_array; die;
		$dtlsrDelete = execute_query("UPDATE pro_garments_production_dtls SET STATUS_ACTIVE=0,IS_DELETED=1 where mst_id=$txt_mst_id",1);
		$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		// echo "10**".$field_array1."__".$data_array1."__".$data_array1;die;
		// echo $rID; die;

		$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id","".$txt_system_id."",1);
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1); 
		}
		// echo "10**".$rID ."=". $dtlsrID ."=". $challanrID;die;

		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);

		if(str_replace("'","",$sewing_production_variable)!=1)
		{
			if($rID && $dtlsrID && $challanrID)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".$txt_system_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id)."**mst*".$rID ."**dtls*". $dtlsrID ."**cln*". $challanrID;;
			}
		}
		else
		{
			if($rID)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".$mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id)."**mst*".$rID ."**dtls*". $dtlsrID ."**cln*". $challanrID;;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		$con = connect();
		 
		$challanrData=sql_select("SELECT id,po_break_down_id from pro_garments_production_mst where delivery_mst_id=$txt_system_id and status_active=1 and is_deleted=0");

		$po_id = $challanrData[0]['PO_BREAK_DOWN_ID'];
		$wash_receive_data=sql_select("SELECT id,po_break_down_id,production_quantity as prod_qty,reject_qnty from pro_garments_production_mst where po_break_down_id=$po_id and embel_name=3 and production_type=3 and entry_form=648 and status_active=1 and is_deleted=0");

		$receive_qty=$reject_qty=$receive_data=0;
		foreach ($wash_receive_data as $v) 
		{
			$receive_qty += $v['PROD_QTY'];
			$reject_qty += $v['REJECT_QNTY'];
			$receive_data ++;
		}
		
		if ($receive_data==0 ) //Gmts. Receive From Wash V2 Exist Check 
		{ 
			if(count($challanrData)==1)
			{
				$challanrID = sql_delete("pro_gmts_delivery_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id',$txt_system_id,1);
				$resetLoad=1;

			}
			else{
				$challanrID = 1;
				$resetLoad=2;
			}
			
			$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id',$txt_mst_id,1);

			$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);

			if($rID && $dtlsrID && $challanrID)
			{
				oci_commit($con);   
				echo "2**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$txt_system_id."**".$operation; 
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id); 
			}
			disconnect($con);
			die;
		}
		else
		{
			echo "99**".$receive_qty."**".$reject_qty ; 
		}

		
	}
}

if($action=="populate_issue_form_data")
{
	$data=explode("**",$data); 
	$sqlResult =sql_select("SELECT id, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, embel_name, embel_type, production_date, production_quantity, production_source, production_type, entry_break_down_type, remarks, floor_id, total_produced, yet_to_produced, sending_location, sending_company from pro_garments_production_mst where id='".$data[0]."' and production_type='2' and status_active=1 and is_deleted=0 order by id"); 

	//print_r($sqlResult);die;
	foreach($sqlResult as $result)
	{
		$sending_location 	= $result['SENDING_LOCATION'];
		$sending_company 	= $result['SENDING_COMPANY'];
		$cbo_sending_location = "$sending_location"."*"."$sending_company" ;
		echo "$('#txt_issue_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/gmts_issue_to_wash_v2_controller', ".$result[csf('production_source')].", 'load_drop_down_wash_company', 'emb_company_td' );\n";
		echo "$('#cbo_emb_company').val('".$result[csf('serving_company')]."');\n";
		echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n"; 
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";
		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";


	    echo "load_drop_down( 'requires/gmts_issue_to_wash_v2_controller', ".$result['COMPANY_ID'].", 'load_drop_down_po_location', 'location_td' );\n";
		echo "load_drop_down( 'requires/gmts_issue_to_wash_v2_controller', `".$cbo_sending_location."`, 'load_drop_down_floor', 'floor_td' );\n";
		echo"load_drop_down( 'requires/gmts_issue_to_wash_v2_controller', ".$result['SERVING_COMPANY'].", 'load_drop_down_wash_location', 'wash_location_td' );\n";
		echo "$('#cbo_location_name').attr('disabled', 'disabled');\n";
		echo "$('#cbo_company_name').attr('disabled', 'disabled');\n";
		
		echo "$('#cbo_embel_name').val('".$result[csf('embel_name')]."');\n";
		echo "$('#cbo_sending_location').val(`".$cbo_sending_location."`);\n";
		echo "load_drop_down( 'requires/gmts_issue_to_wash_v2_controller', '".$result[csf('embel_name')].'**'.$result[csf('po_break_down_id')]."', 'load_drop_down_embro_issue_type', 'embro_type_td' );\n";
		//$result[csf('po_break_down_id')]
		echo "$('#cbo_embel_type').val('".$result[csf('embel_type')]."');\n";
		
		echo "$('#txt_issue_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
		
		echo "$('#txt_iss_id').val('".$result[csf('id')]."');\n";
		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		echo "$('#txt_remark_dtls').val('".$color_type_val[0][csf("remarks_dtls")]."');\n"; 
		$po_id = $result['PO_BREAK_DOWN_ID'];
		$item_id = $result['ITEM_NUMBER_ID'];
		$country_id = $result['COUNTRY_ID'];
		$variableSettings = $result[csf('entry_break_down_type')];
		echo "$('#sewing_production_variable').val('".$variableSettings."');\n";
		 
	} 

	//print_r($variableSettings);die;
	$system_arr = sql_select("SELECT id,sys_number,location_id,working_location_id,floor_id from pro_gmts_delivery_mst where id=$data[2] and production_type=2 and embel_name=3  and entry_form=645 and status_active=1 and is_deleted=0");
	echo "$('#txt_system_id').val('".$system_arr[0]['ID']."');\n";
	echo "$('#txt_system_no').val('".$system_arr[0]['SYS_NUMBER']."');\n";
	echo "$('#cbo_wash_location').val('".$system_arr[0]['WORKING_LOCATION_ID']."');\n"; 
	echo "$('#cbo_location_name').val('".$system_arr[0]['LOCATION_ID']."');\n"; 
	echo "$('#cbo_floor').val('".$system_arr[0]['FLOOR_ID']."');\n";

	$data_sql = "SELECT 
		SUM ( CASE WHEN a.production_type = 1 THEN a.production_qnty ELSE 0 END) AS total_cut_qty,
		SUM ( CASE WHEN a.production_type = 5 THEN a.production_qnty ELSE 0 END) AS total_sew_out,
		SUM ( CASE WHEN a.production_type = 2 AND b.embel_name = 3 and a.entry_form=645 THEN a.production_qnty ELSE 0 END) AS total_wash
		FROM pro_garments_production_dtls a, pro_garments_production_mst b
		WHERE a.status_active = 1
		and b.status_active=1  and a.is_deleted=0 and  b.is_deleted=0 and a.mst_id=b.id and b.po_break_down_id=$po_id and b.item_number_id=$item_id and b.country_id=$country_id and a.production_type in(1,2,5) " ;
	$dataArray=sql_select($data_sql); 
	foreach($dataArray as $row)
	{	
		$ttl_cut_qty = $row['TOTAL_CUT_QTY'];
		$ttl_sew_out = $row['TOTAL_SEW_OUT'];
		$ttl_wash	 = $row['TOTAL_WASH'];
		echo "$('#txt_cut_qty').val('".$ttl_cut_qty."');\n";
		echo "$('#txt_sewing_qty').val('".$ttl_sew_out."');\n";
		echo "$('#txt_cum_issue_qty').attr('placeholder','".$ttl_wash."');\n";
		echo "$('#txt_cum_issue_qty').val('".$ttl_wash."');\n";
		$yet_to_wash = $ttl_sew_out-$ttl_wash;
		echo "$('#yet_to_issue').attr('placeholder','".$yet_to_wash."');\n";
		echo "$('#yet_to_issue').val('".$yet_to_wash."');\n";
	}

	echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";

	// =============================================================================================
	//											Order Data
	// =============================================================================================
	$order_sql = "SELECT a.id, a.po_quantity, a.plan_cut, a.po_number,a.grouping, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, c.item_number_id as gmts_item_id, b.job_no,b.packing, b.location_name,c.country_ship_date, d.emb_name
	from wo_po_break_down a, wo_po_details_master b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d
	where d.cons_dzn_gmts>0 and a.job_id=b.id and b.id=d.job_id and a.id=c.po_break_down_id and a.id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id  and d.emb_name=3 and a.status_active=1  and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1  and c.is_deleted=0  group by a.id, a.po_quantity, a.plan_cut, a.po_number,a.grouping, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, c.item_number_id,b.job_no,b.packing, b.location_name,c.country_ship_date, d.emb_name";
	// echo $order_sql ; die;
	$res = sql_select($order_sql);
	
 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result['PO_NUMBER']."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result['ID']."');\n"; 
		echo "$('#txt_style_no').val('".$result['STYLE_REF_NO']."');\n";
		echo "$('#txt_job_no').val('".$result['JOB_NO']."');\n";
		echo "$('#txt_ir').val('".$result['GROUPING']."');\n";
		echo "$('#txt_country_ship_date').val('".$result['COUNTRY_SHIP_DATE']."');\n";
		echo "$('#txt_pack_type').val('".$result['PACKING']."');\n";  
  	}
	echo "$('#txt_issue_date').attr('disabled','disabled');\n";
	echo "set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);\n";  

	

	//break down of color and size------------------------------------------
	//#############################################################################################//
	// order wise - color level, color and size level
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

	//echo $variableSettings;die;
	
	if( $variableSettings!=1 ) // gross level
	{  
		$sql_dtls = sql_select("select color_size_break_down_id, production_qnty, size_number_id, color_number_id from pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id='". $data[0] ."' and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
		foreach($sql_dtls as $row)
		{
			if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$row[csf('color_number_id')];
			$amountArr[$index] = $row[csf('production_qnty')];
		}
		//$variableSettings=2;

		if( $variableSettings==2 ) // color level
		{
			
			$sql="SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
				sum(CASE WHEN c.production_type=5 then b.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN c.production_type=2 and c.embel_name=3 and c.entry_form=645 then b.production_qnty ELSE 0 END) as cur_production_qnty
				from wo_po_color_size_breakdown a
				left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
				left join pro_garments_production_mst c on c.id=b.mst_id
				where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.item_number_id, a.color_number_id";
		}
		else if( $variableSettings==3 ) //color and size level
		{
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
				sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=2 and b.embel_name=3 and b.entry_form=645 then a.production_qnty ELSE 0 END) as cur_production_qnty
				from pro_garments_production_dtls a,pro_garments_production_mst b where a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(5,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   group by a.color_size_break_down_id ");
				// print_r($dtlsData) ;die;
			
			foreach($dtlsData as $row) 
			{
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
			}
			unset($dtlsData);
			
			//echo "<pre>";print_r($color_size_qnty_array);die;

			$sql = "SELECT id, size_order, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, size_order";//echo $sql;die;

		}
		else // by default color and size level
		{
			$$dtlsData = sql_select("SELECT a.color_size_break_down_id,
			sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as production_qnty,
			sum(CASE WHEN a.production_type=2 and b.embel_name=3 and b.entry_form=645 then a.production_qnty ELSE 0 END) as cur_production_qnty
			from pro_garments_production_dtls a,pro_garments_production_mst b where a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(5,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0  group by a.color_size_break_down_id ");

			foreach($dtlsData as $row)
			{
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
			}
			unset($dtlsData);
			//print_r($color_size_qnty_array);

			$sql = "SELECT id, size_order, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, size_order";
		}

		$colorResult = sql_select($sql);
		//print_r($sql);die;
		$colorHTML=""; $colorID=''; $chkColor = array(); $i=0; $totalQnty=0; $colorWiseTotal=0;
		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{
				$amount = $amountArr[$color[csf("color_number_id")]];
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';
				$totalQnty += $amount;
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
					$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
					//$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
					$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_'.$color[csf("color_number_id")].'">';
					$chkColor[] = $color[csf("color_number_id")];
					$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
				}
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

				$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
				$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
				// echo $iss_qnty."<br>"."***".$rcv_qnty;

				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).');fn_chk_next_process_qty('.$color[csf("color_number_id")].','.($i+1).','.$color[csf("size_number_id")].')" onkeyup="" value="'.$amount.'" ><input type="hidden" name="colorSizeUpQty" id="colSizeUpQty_'.$color[csf("color_number_id")].($i+1).'" value="'.$amount.'" ><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';
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
 	exit();
}

if($action=="system_number_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
	<script>		
		function js_set_value(str)
		{
			$("#hidden_search_data").val(str);
			parent.emailwindow.hide();
		}
    </script>
	<?
		$wash_comp = array();
		if($source==3)
		{
			$wash_comp = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", 'id', 'supplier_name'); 
		}
		else if($source==1)
		{  
			$wash_comp =return_library_array("select id,company_name from lib_company comp where is_deleted=0 and status_active=1 order by company_name", 'id', 'company_name');  
		}
		// pre($wash_comp); die;
		$is_disabled = ($buyer_id) ? 1: 0;
	?>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead>
	                <tr>
	                    <th>Company Name</th>
	                    <th>Buyer Name</th>
	                    <th>Wash Company</th>
	                    <th>Job No</th>
	                    <th>Issue No</th>
	                    <th>Order No</th>
	                    <th width="200">Date Range</th>
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
	                    <td>
							<?
							echo create_drop_down( "cbo_company_name", 170, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$company_id, "",1);?>
	                    </td>
	                    
	                    <td>
							<?
							echo create_drop_down( "cbo_buyer_name", 120, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_id, "",$is_disabled ,0 );
							?> 
	                    <td> 
	                    	<? echo create_drop_down( "wash_company", 120,$wash_comp ,"", 1, "-- Select --", $selected, "","",0 ); ?>
	                    </td>
	                    <td>
							<input type="text" style="width:100px" class="text_boxes"  name="txt_job_no" id="txt_job_no" />
	                    	
	                    </td>
						<td>
							<input type="text" style="width:100px" class="text_boxes"  name="txt_issue_no" id="txt_issue_no" />
	                    	
	                    </td>
						<td>
							<input type="text" style="width:100px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />
	                    </td>
	                    <td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
	                    </td>
	                    <td align="center"> 
	                    	<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('wash_company').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_issue_no').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<?= $source?>, 'create_system_number_list_view', 'search_div', 'gmts_issue_to_wash_v2_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
	                    </td>
	                </tr>
	            </tbody>
	            <tfoot>
	                <tr>
	                    <td align="center" valign="middle" colspan="9" style="background-image: -moz-linear-gradient(bottom, rgb(136,170,214) 7%, rgb(194,220,255) 10%, rgb(136,170,214) 96%);">
	                    <?=load_month_buttons(1);  ?>
	                    <input type="hidden" id="hidden_search_data">
	                    </td>
	                </tr>
	            </tfoot>
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

if($action=="create_system_number_list_view")
{
 	$ex_data 	= explode("_",$data);
    $company 	= trim($ex_data[0]);
    $buyer_id 	= trim($ex_data[1]);
    $wash_comp 	= trim($ex_data[2]);
	$job_no 	= trim($ex_data[3]);
    $issue_id 	= trim($ex_data[4]);
	$order_no 	= trim($ex_data[5]);
    $txt_date_from = $ex_data[6];
	$txt_date_to = $ex_data[7];  
	$source = $ex_data[8];  

	if ($source==3) 
	{
		$company_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", 'id', 'supplier_name');  
	}
	else if($source==1)
	{  
		$company_arr = return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer",'id','buyer_name');

	$supplier_arr=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name",'id','supplier_name');
	
	$sql_cond="";
	
	
	if($buyer_id!='0')
	{
		$sql_cond .= " and c.buyer_name=$buyer_id";
	}
	if($wash_comp!='0')
	{
		$sql_cond .= " and a.working_company_id=$wash_comp";
	}
	if($issue_id!="")
	{
		$sql_cond .= " and a.sys_number like '%".$issue_id."'";
	}
	if($order_no!="")
	{
		$sql_cond .= " and b.po_number like '%".$order_no."'";
	}
	if($job_no!="")
	{
		$sql_cond .= " and c.job_no like '%".$job_no."'";
	}
	if($company!='0')
	{
		$sql_cond .= " and a.company_id=$company";
	}
	/* if($source !='0')
	{
		$sql_cond .= " and a.production_source=$source";
	}
 	*/
	if($txt_date_from!="" && $txt_date_to!="")
	{
		$sql_cond .= " and d.production_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
	}
	else if($txt_date_from=="" && $txt_date_to!=""){
		$sql_cond .= " and d.production_date <=  '".date("j-M-Y",strtotime($txt_date_to))."'";
	}
	else if($txt_date_from!="" && $txt_date_to==""){
		$sql_cond .= " and d.production_date >= '".date("j-M-Y",strtotime($txt_date_from))."'";
	}

	$sql ="SELECT a.id,a.company_id,c.buyer_name as buyer,c.job_no,c.style_ref_no as style,b.po_number,a.sys_number,a.sys_number_prefix_num as issue_id,d.production_quantity as issue_qty,d.serving_company as wash_comp,d.production_date as issue_date  from pro_gmts_delivery_mst a, wo_po_break_down b, wo_po_details_master c,pro_garments_production_mst d where a.id=d.delivery_mst_id and b.job_id=c.id and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.production_type=2 and a.embel_name='3' and a.entry_form=645 $sql_cond  order by a.id DESC";
	//group by a.id,a.company_id,c.buyer_name,c.job_no,c.style_ref_no,b.po_number,a.sys_number_prefix_num,a.sys_number,d.production_quantity,d.serving_company,d.production_date
	// echo $sql;die();
	/* foreach($dataArray as $row)
	{
		$job_no=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"job_no");
		if($row[csf('source')] == 3)
		{
			$finData[$row[csf('id')]] = $com[$row[csf('supplier_id')]];
		}
		
	} */
	$arr=array(0=>$buyer_arr,6=>$company_arr); 
	echo create_list_view("list_view", "Buyer,Job No,Style,Order No,Issue ID, Issue Qty,Wash Company,Issue Date","80,100,100,100,60,60,100,100","770","340",0, $sql , "js_set_value","id,sys_number,company_id", "",1, "buyer,0,0,0,0,0,wash_comp,0", $arr,"buyer,job_no,style,po_number,issue_id,issue_qty,wash_comp,issue_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,0,0") ;
	exit();
}

if($action=="populate_mst_form_data")
{
	$sql ="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_source, delivery_date,working_company_id,location_id,working_location_id,floor_id,remarks,sending_company,sending_location,buyer_id from pro_gmts_delivery_mst a where a.id=$data and production_type=2 and embel_name=3 and entry_form=645";
	$result =sql_select($sql);
	
	$sending_location 	= $result[0]['SENDING_LOCATION'];
	$sending_company 	= $result[0]['SENDING_COMPANY'];
	$cbo_sending_location = $sending_location."*".$sending_company ;
	echo"load_drop_down( 'requires/gmts_issue_to_wash_v2_controller', ".$result[0]['PRODUCTION_SOURCE'].", 'load_drop_down_wash_company', 'cbo_emb_company' );\n";
	echo"load_drop_down( 'requires/gmts_issue_to_wash_v2_controller', ".$result[0]['WORKING_COMPANY_ID'].", 'load_drop_down_wash_location', 'cbo_wash_location' );\n"; 
	echo"load_drop_down( 'requires/gmts_issue_to_wash_v2_controller',`".$cbo_sending_location."`, 'load_drop_down_floor', 'cbo_floor' );\n";
	echo "load_drop_down( 'requires/gmts_issue_to_wash_v2_controller', `".$result[0]['COMPANY_ID']."`, 'load_drop_down_buyer', 'buyer_td' );\n";
	echo "$('#txt_system_no').val(`".$result[0]['SYS_NUMBER']."`);\n";
	echo "$('#txt_system_id').val(`".$result[0]['ID']."`);\n";
	echo "$('#txt_mst_id').val(`".$result[0]['ID']."`);\n";
	echo "$('#cbo_company_name').val(`".$result[0]['COMPANY_ID']."`);\n";
	echo "$('#cbo_location_name').val(`".$result[0]['LOCATION_ID']."`);\n";
	echo "$('#cbo_source').val(`".$result[0]['PRODUCTION_SOURCE']."`);\n";
	echo "$('#txt_issue_date').val(`".change_date_format($result[0]['DELIVERY_DATE'])."`);\n";
	echo "$('#cbo_emb_company').val(`".$result[0]['WORKING_COMPANY_ID']."`);\n";
	echo "$('#cbo_wash_location').val(`".$result[0]['WORKING_LOCATION_ID']."`);\n";
	echo "$('#cbo_sending_location').val(`".$cbo_sending_location."`);\n";
	echo "$('#cbo_floor').val(`".$result[0]['FLOOR_ID']."`);\n";
	echo "$('#txt_remark').val(`".$result[0]['REMARKS']."`);\n";
	echo "$('#cbo_buyer_name').val(`".$result[0]['BUYER_ID']."`);\n";
	echo "$('#cbo_buyer_name').attr('disabled','disabled');\n";
	echo "$('#cbo_company_name').attr('disabled','disabled');\n";
	echo "$('#cbo_location_name').attr('disabled','disabled');\n";
	echo "$('#txt_issue_date').attr('disabled','disabled');\n";
	echo "set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);\n";
 	exit();
}
 if($action=="wash_issue_print")
{
	extract($_REQUEST);
	$data			= explode('*',$data);
	$lc_company 	= $data[0];
	$sys_id 		= $data[1];
	$report_title 	= $data[2];

	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$supplier_address=return_library_array( "select id,address_1 from  lib_supplier", "id","address_1"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$address_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

	$sql = "SELECT a.embel_type,a.item_number_id as item,a.sending_company,a.sending_location,a.serving_company as wash_comp, a.location as wash_location, a.entry_break_down_type as prod_variable,a.remarks,b.production_qnty as prod_qty,c.id as po_id,c.po_number,d.color_number_id as color,d.size_number_id,e.company_name as lc_company,e.buyer_name,e.job_no,e.style_ref_no,f.sys_number,f.production_source as source,f.delivery_date from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_break_down c,wo_po_color_size_breakdown d,wo_po_details_master e,pro_gmts_delivery_mst f where a.id=b.mst_id and a.delivery_mst_id=f.id and a.po_break_down_id=c.id and c.id=d.po_break_down_id and e.id=c.job_id and b.color_size_break_down_id=d.id and f.id=$sys_id and a.production_type=2 and a.entry_form=645 and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 order by a.id,d.size_number_id";
	// echo $sql;die;
	$sql_res =sql_select($sql);
	$size_arr = array();
	$prod_arr = array();
	$data_arr = array();
	$total_arr = array();
	$color_width =0;
	foreach ($sql_res as  $v) 
	{
		$sys_no 		 	= $v['SYS_NUMBER'];
		$wash_comp			= $v['WASH_COMP'];
		$lc_company			= $v['LC_COMPANY'];
		$wash_location		= $v['WASH_LOCATION'];
		$sending_company 	= $v['SENDING_COMPANY'];
		$sending_location 	= $v['SENDING_LOCATION'];
		$delivery_date 		= $v['DELIVERY_DATE'];
		$source 			= $v['SOURCE'];
		$prod_variable 		= $v['PROD_VARIABLE'];
		$remarks 			= $v['REMARKS'];
		
		$data_arr[$v['PO_ID']][$v['COLOR']]['LC_COMPANY'] 	= $v['LC_COMPANY'];
		$data_arr[$v['PO_ID']][$v['COLOR']]['BUYER_NAME'] 	= $v['BUYER_NAME'];
		$data_arr[$v['PO_ID']][$v['COLOR']]['JOB_NO'] 		= $v['JOB_NO'];
		$data_arr[$v['PO_ID']][$v['COLOR']]['STYLE_REF_NO'] = $v['STYLE_REF_NO'];
		$data_arr[$v['PO_ID']][$v['COLOR']]['PO_NUMBER'] 	= $v['PO_NUMBER']; 
		$data_arr[$v['PO_ID']][$v['COLOR']]['ITEM'] 		= $v['ITEM'];
		$data_arr[$v['PO_ID']][$v['COLOR']]['EMBEL_TYPE'] 	= $v['EMBEL_TYPE'];
		$data_arr[$v['PO_ID']][$v['COLOR']]['ISS_QTY'] 		+= $v['PROD_QTY'];

		if ($prod_variable ==2) 	//color Level
		{
			$color_width = 100;
		}
		elseif ($prod_variable ==3) //color and Size Level
		{
			$size_arr[$v['SIZE_NUMBER_ID']]=$v['SIZE_NUMBER_ID'];
			$prod_arr[$v['PO_ID']][$v['COLOR']][$v['SIZE_NUMBER_ID']]['ISS_QTY'] += $v['PROD_QTY'];
			$total_arr[$v['SIZE_NUMBER_ID']] += $v['PROD_QTY'];
			$color_width = 100;
		}
		

	}

	if($source == 1){
		$company = $company_library[$wash_comp];
		$wash_address = $address_arr[$wash_location];
	}else if($source == 3){
		$company = $supplier_library[$wash_comp];
		$wash_address = "";
	}
	// pre($prod_arr);die;
	$size_count = count($size_arr);
	$width = 900+$color_width+($size_count*50);

	?>
	<div style="width:<?= $width+20?>px;">
		<div style="display: flex;">
			<div class="logo" style="width:100px">
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
							
					</tr>
			</div>
			<div style="text-align:center;width:<?= $width-100?>px">
				<p style="font-size:22px;margin:0;"><strong><? echo $company_library[$sending_company]; ?></strong></p>
				<p style="font-size:16px;margin:0;"><?= $address_arr[$sending_location]?></p>
				<p style="font-size:18px;margin:0;"><strong>Issue Delivery  Challan-<?= $sys_no ?></strong></p>
			</div> 
		</div>
		<div style="display: flex; margin-left:30px;">
			<div style="text-align:center;width:<?= ($width*0.75)?>px;">
				<table>
					<tr>
						<td style="font-size:20px"><strong>Wash Company</strong> </td>
						<td align="left" ><strong>: </strong> <?= $company; ?></td>
					</tr>	
					<tr>
						<td  style="font-size:20px"><strong>Address</strong> </td>
						<td align="left"><strong>: </strong> <?= $wash_address; ?></td>
					</tr> 
					<tr>
						<td  style="font-size:20px"><strong>Remarks</strong> </td>
						<td align="left"><strong>: </strong> <?= $remarks; ?></td>
					</tr> 
				</table>
			</div> 
			<div style="text-align:center;width:<?= ($width*0.25)?>px">
				<table>
					<tr>
						<td style="font-size:20px;margin-left:30px;"><strong>Delivery Date</strong></td>
						<td><strong>: </strong> <?=$delivery_date?></td>
					</tr>	
					<tr>
						<td style="font-size:20px;margin-left:30px;"><strong>Source</strong></td>
						<td><strong>: </strong> <?= $knitting_source[$source] ?></td>
					</tr> 
				</table>
			</div> 
		</div>
		<table width="<?= $width+20?>" cellspacing="0" align="right" border="1" cellspacing="0"  style="margin-top: 30px;">

			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="120">Lc Company</th>
					<th width="100">Buyer Name</th>
					<th width="100">Job No</th>
					<th width="130">Style Ref.</th>
					<th width="100">Order No.</th>
					<th width="100">Wash.Type</th>
					<th width="100">Item</th>
					<?
						if ( in_array($prod_variable,[2,3])  ) 
						{
							?>
								<th width="100">Color/Size</th>
							<?	
						}
					?>
					<?
						if ($prod_variable==3) //Color Size Level
						{
							foreach ($size_arr as $size_id) 
							{ 
								?>
									<th width="50"><?= $size_library[$size_id] ?></th>
								<?
							}
								
						}
					?> 
					
					<th width="120">Issue.Qty</th> 
				</tr>
			</thead>
			<tbody>
				<?
					$i=0;
					$total_prod_qty =0;
					foreach ($data_arr as $po_id => $po_arr) 
					{
						foreach ($po_arr as $color_id => $v) 
						{	
							$total_prod_qty +=$v['ISS_QTY'];
							?>
								<tr>
									<td align="center"><?= ++$i; ?></td>
									<td><?= $company_library[$v['LC_COMPANY']] ?></td> 
									<td><?= $buyer_library[$v['BUYER_NAME']] ?></td> 
									<td><?= $v['JOB_NO'] ?></td> 
									<td><?= $v['STYLE_REF_NO'] ?></td>  
									<td><?= $v['PO_NUMBER'] ?></td>   
									<td><?= $emblishment_wash_type[$v['EMBEL_TYPE']] ?></td>  
									<td><?= $garments_item[$v['ITEM']] ?></td>  
									<?
										if ( in_array($prod_variable,[2,3])  ) 
										{
											?>
												<td><?= $color_library[$color_id] ?></td>  
											<?	
										}
									?>
									<?
										if ($prod_variable==3) //Color Size Level
										{
											foreach ($size_arr as $size) 
											{
												$size_qty = $prod_arr[$po_id][$color_id][$size]['ISS_QTY'];
												?>
													<th align="center"><?=$size_qty?></th>
												<?
											}
												
										}
									?>
									<td align="center"><?= $v['ISS_QTY']?> </td>
								</tr>
							<?
						}
						 
					}	

				?>	
			</tbody>
			<tfoot>
				<th colspan="7"></th>
				<th >Total</th>
				<?
					if ( in_array($prod_variable,[2,3])  ) 
					{
						?>
							<th></th>
						<?	
					}
				?>
				<?
					if ($prod_variable==3) //Color Size Level
					{
						foreach ($size_arr as $size) 
						{
							$ttlsize_qty = $total_arr[$size];
							?>
								<th align="center"><?=$ttlsize_qty?></th>
							<?
						}
							
					}
				?>
				<th align="center"><?= $total_prod_qty; ?></th>
			</tfoot>
		</table>	
		<caption style="caption-side:bottom; margin-top: 30px;"><b>In words:</b> <?=number_to_words($total_prod_qty)." Pcs";?></caption>
		
		<br>
		 <?
            echo signature_table(321, $data[0], "900px","",10,$inserted_by);
         ?>
	</div>
	<?
	exit();
}
?>