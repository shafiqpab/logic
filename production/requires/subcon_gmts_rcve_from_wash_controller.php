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
if ($action == "load_drop_down_party_name") 
{
	echo create_drop_down( "cbo_party_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",'', "",0);  
}
if ($action == "load_drop_down_wash_location") 
{
    echo create_drop_down( "cbo_wash_location", 172, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
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
        });
	
		function js_set_value(id,item_id)
		{
			$("#hidden_mst_id").val(id);
			$("#hidden_grmtItem_id").val(item_id);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
            	<tr>
                    <th width="150" align="center">Buyer Name</th>               	 
                    <th width="100" align="center">Job Search</th>
                    <th width="100" align="center">Style Search</th>
                    <th  width="100" align="center">Order No</th>
                    <th width="170">Date Range</th>
                    <th ><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                </tr>
            </thead>
            <tbody>
                <tr>
					<td>  
						<? 
							echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond","id,buyer_name", 1, "-- Select Party --",$buyer_id, "",1 );  
							//and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (9,3)) order by buyer_name 	 
                        ?>
                    </td>
                    <td width="100" align="center">				
                        <input type="text" style="width:95px" class="text_boxes"  name="txt_search_job" id="txt_search_job" placeholder="Job Search" />			
                    </td>
                    <td width="100" align="center">				
                        <input type="text" style="width:95px" class="text_boxes"  name="txt_search_style" id="txt_search_style" placeholder="Style Search" />			
                    </td>
                    <td width="100" align="center">				
                        <input type="text" style="width:95px" class="text_boxes"  name="txt_search_order" id="txt_search_order" placeholder="Order Search" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />			
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"> To
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td> 
                    <td align="center">
                        <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_style').value+'_'+document.getElementById('txt_search_order').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_string_search_type').value, 'create_po_search_list_view', 'search_div', 'subcon_gmts_rcve_from_wash_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                    </td>
                </tr>
				<tr>
					<td colspan="6" align="center" valign="middle">
						<? echo load_month_buttons(1);  ?>
						<input type="hidden" id="hidden_mst_id">
						<input type="hidden" id="hidden_grmtItem_id">
					</td>
				</tr>
            </tbody>
		</table>    
		</form>
        <div id="search_div"></div>
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
	$party = $ex_data[0];
	$search_job = $ex_data[1];
	$search_style = $ex_data[2];
	$search_order = $ex_data[3];
	$date_from = $ex_data[4];
	$date_to = $ex_data[5];
	$company = $ex_data[6];
 	$garments_nature = $ex_data[7];
	$search_type= $ex_data[8];

	if($party!=0) $party_cond=" and a.party_id='$party'"; else $party_cond="";
	
	if($search_type==1)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num='$search_job'"; else $search_job_cond="";
		if($search_style!='') $search_style_cond=" and b.cust_style_ref='$search_style'"; else $search_style_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no='$search_order'"; else $search_order_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '%$search_job%'"; else $search_job_cond="";
		if($search_style!='') $search_style_cond=" and b.cust_style_ref like '%$search_style%'"; else $search_style_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no like '%$search_order%'"; else $search_order_cond="";
	}
	else if($search_type==2)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '$search_job%'"; else $search_job_cond="";
		if($search_style!='') $search_style_cond=" and b.cust_style_ref like '$search_style%'"; else $search_style_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no like '$search_order%'"; else $search_order_cond="";
	}
	else if($search_type==3)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '%$search_job'"; else $search_job_cond="";
		if($search_style!='') $search_style_cond=" and b.cust_style_ref like '%$search_style'"; else $search_style_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no like '%$search_order'"; else $search_order_cond="";
	}
	
	if ($date_from!="" &&  $date_to!="") $delivery_date_cond = "and b.delivery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'"; else $delivery_date_cond ="";
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		
	
	$year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
	
	
 	$sql_sew = "select a.subcon_job, a.job_no_prefix_num, $year_cond, a.party_id, b.id, b.order_no, b.main_process_id, b.process_id, b.order_uom, b.cust_buyer, b.cust_style_ref, b.delivery_date, c.item_id, sum(c.qnty) as order_quantity from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and a.id=c.mst_id and b.id=c.order_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$company' $delivery_date_cond $search_job_cond $search_style_cond $search_order_cond $party_cond  and b.process_id like '%188%' group by a.id, b.id, b.order_uom, b.cust_buyer, b.cust_style_ref, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.party_id, b.main_process_id, b.process_id, b.delivery_date, b.order_no,c.item_id order by a.id DESC ";
	
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="815" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="50">Job </th>
                <th width="50">Year</th>
                <th width="100">Party</th>
                <th width="90">Style No</th>
                <th width="90">PO No</th>
                <th width="90">Process</th>
                <th width="80">PO Qty</th>
                <th width="120">Item</th>
                <th>Delivery Date</th>
            </thead>
        </table>
        <div style="width:815px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="795" class="rpt_table" id="tbl_po_list" >
            <?
				$i=1;
				$sql_sew_result=sql_select($sql_sew);
				foreach($sql_sew_result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>,<? echo $row[csf('item_id')]; ?>)"> 
							<td width="30" align="center"><? echo $i; ?></td>	
							<td width="50" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
							<td width="50" align="center"><? echo $row[csf('year')]; ?></td>
							<td width="100"><p><? echo $party_arr[$row[csf('party_id')]]; ?></p></td>
							<td width="90"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
							<td width="90"><p><? echo $row[csf('order_no')]; ?></p></td>
							<td width="90"><p><? echo $production_process[$row[csf('main_process_id')]]; ?></p></td>
							<td width="80" align="right"><? echo number_format( $row[csf('order_quantity')],2); ?>&nbsp;</td> 
							<td width="120" align="center"><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
							<td align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
						</tr>
					<?
					$i++;
				}
			?>
            </table>
        </div>
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
	
	
	$order_sql = "select a.id, a.delivery_date,a.order_no, a.order_quantity, a.cust_style_ref ,b.subcon_job, b.company_id, b.party_id, c.order_id as order_id, c.item_id as item_id, sum(c.qnty) as qnty from  subcon_ord_dtls a, subcon_ord_mst b, subcon_ord_breakdown c where b.id=c.mst_id and a.id=c.order_id and a.status_active=1 and a.is_deleted=0 and a.job_no_mst=b.subcon_job and a.id=$po_id and c.item_id=$item_id group by a.id, a.delivery_date,a.order_no, a.order_quantity, a.cust_style_ref ,b.subcon_job, b.company_id, b.party_id, c.order_id, c.item_id"; 
	$res = sql_select($order_sql);
 	foreach($res as $v)
	{
		echo "$('#txt_order_no').val('".$v['ORDER_NO']."');\n";
		echo "$('#hidden_po_break_down_id').val('".$v['ID']."');\n";
		echo "$('#cbo_party_name').val('".$v['PARTY_ID']."');\n";
		echo "$('#txt_style_no').val('".$v['CUST_STYLE_REF']."');\n";
		echo "$('#txt_job_no').val('".$v['SUBCON_JOB']."');\n";
		echo "$('#txt_delivery_date').val('".$v['DELIVERY_DATE']."');\n";
  	}


	$data_sql = "SELECT 
		SUM ( CASE WHEN a.production_type = 1 THEN a.production_qnty ELSE 0 END) AS total_cut_qty,
		SUM ( CASE WHEN a.production_type = 2 THEN a.production_qnty ELSE 0 END) AS total_sew_out,
		SUM ( CASE WHEN a.production_type = 9 AND a.embel_name=3 and a.entry_form=651 THEN a.production_qnty ELSE 0 END) AS total_wash_issue,
		SUM ( CASE WHEN a.production_type = 10 AND a.embel_name=3 and a.entry_form=652 THEN a.production_qnty ELSE 0 END) AS total_wash
		FROM subcon_gmts_prod_dtls a
		WHERE a.status_active=1 and a.is_deleted=0 and a.order_id=$po_id and a.gmts_item_id=$item_id and a.production_type in(1,2,9,10)" ;
	$dataArray=sql_select($data_sql); 
	foreach($dataArray as $row)
	{	
		$ttl_cut_qty = $row['TOTAL_CUT_QTY']??0;
		$ttl_sew_out = $row['TOTAL_SEW_OUT']??0;
		$ttl_issue	 = $row['TOTAL_WASH_ISSUE']??0;
		$ttl_wash	 = $row['TOTAL_WASH']??0;
		echo "$('#txt_cut_qty').val('".$ttl_cut_qty."');\n";
		echo "$('#txt_sewing_qty').val('".$ttl_sew_out."');\n";
		echo "$('#txt_cum_rcve_qty').attr('placeholder','".$ttl_wash."');\n";
		echo "$('#ttl_issue_qty').val('".$ttl_issue."');\n";
		echo "$('#txt_cum_rcve_qty').val('".$ttl_wash."');\n";
		$yet_to_wash = $ttl_issue-$ttl_wash;
		echo "$('#txt_yet_to_rcve').attr('placeholder','".$yet_to_wash."');\n";
		echo "$('#txt_yet_to_rcve').val('".$yet_to_wash."');\n";
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
		
		$sql = "SELECT a.item_id as gmts_item_id, a.color_id as color_number_id, sum(a.qnty) as order_quantity, sum(a.plan_cut) as plan_cut_qnty,
				sum(CASE WHEN c.production_type=9 and c.embel_name=3 and c.entry_form=651 then b.prod_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN c.production_type=10 and c.embel_name=3 and c.entry_form=652 then b.prod_qnty ELSE 0 END) as cur_production_qnty,
				sum(CASE WHEN c.production_type=10 and c.entry_form=652 then b.reject_qty ELSE 0 END) as reject_qty
				from subcon_ord_breakdown a
				left join subcon_gmts_prod_col_sz b on a.id=b.ord_color_size_id
				left join subcon_gmts_prod_dtls c on c.id=b.dtls_id
				where a.order_id=$po_id and a.item_id=$item_id and a.is_deleted=0 and a.status_active=1  group by a.item_id, a.color_id";//and b.is_deleted=0 and b.status_active=1
		$colorResult = sql_select($sql);
	}
	else if( $variableSettings==3 ) //color and size level
	{
		$dtls_sql = "SELECT a.ord_color_size_id,
		SUM ( CASE WHEN a.production_type = 9  AND b.embel_name =3  and b.entry_form=651 THEN a.prod_qnty ELSE 0 END) AS production_qnty,
		SUM ( CASE WHEN a.production_type = 10 AND b.embel_name =3  and b.entry_form=652 THEN a.prod_qnty ELSE 0 END) AS  cur_production_qnty,
		SUM ( CASE WHEN a.production_type = 10 AND b.embel_name =3  and b.entry_form=652 THEN a.reject_qty ELSE 0 END) AS  reject_qty
			FROM subcon_gmts_prod_col_sz a, subcon_gmts_prod_dtls b
			WHERE a.dtls_id=b.id and b.status_active=1  and  b.is_deleted=0 and a.status_active=1  and  a.is_deleted=0 and b.order_id=$po_id and b.gmts_item_id=$item_id and a.ord_color_size_id!=0 and a.production_type in(9,10) group by a.ord_color_size_id";
			 
		$dtlsData =sql_select($dtls_sql);
		
		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row['ORD_COLOR_SIZE_ID']]['iss']= $row['PRODUCTION_QNTY'];
			$color_size_qnty_array[$row['ORD_COLOR_SIZE_ID']]['rcv']= $row['CUR_PRODUCTION_QNTY'];
			$color_size_qnty_array[$row['ORD_COLOR_SIZE_ID']]['rej']= $row[csf('reject_qty')];
		}
		unset($dtlsData);
		//print_r($color_size_qnty_array); 

		$sql = "SELECT id,item_id as gmts_item_id, size_id as size_number_id,color_id as color_number_id,  qnty as order_quantity,plan_cut as plan_cut_qnty
			from subcon_ord_breakdown
			where order_id='$po_id' and item_id='$item_id' and is_deleted=0 and status_active=1 order by color_id,size_id";
		$colorResult = sql_select($sql);
	}
	//print_r($sql); 
	$colorHTML=""; $colorID=''; $i=0; $totalQnty=0; $chkColor = array();
	foreach($colorResult as $color)
	{
		if( $variableSettings==2 ) // color level
		{
			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]-$color[csf("reject_qty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onblur="fn_colorRej_total('.($i+1).')  '.$disable.'"></td></tr>';
			$totalQnty += $color[csf("production_qnty")]-$color[csf("cur_production_qnty")]-$color[csf("reject_qty")];
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
			$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];

			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty-$rej_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';
		}
		$i++;
	}
	//echo $colorHTML;die;
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><tr><th width="70">Color</th><th width="60">Quantity</th><th width="60">Rej.</th></tr><tr> <th colspan="3"><div style="padding-left: 30px;text-align:left"><input type="checkbox" onClick="active_placeholder_qty_color(' . $color[csf("color_number_id")] . ')" id="set_all">&nbsp;<label for="set_all">Available Qty Auto Fill</label></div></th> </tr></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="Rej." class="text_boxes_numeric" style="width:60px" '.$disable.' ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
	exit();
}

if($action=="show_dtls_listview_from_sys_popup")
{
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');

	$system_mst_id = $data; 
	$sql = "SELECT a.id, a.order_id, a.gmts_item_id, a.company_id, a.production_date, a.production_qnty,a.serving_company, a.location_id, a.embel_name,b.order_no,b.cust_style_ref,a.system_mst_id,a.reject_qnty from subcon_gmts_prod_dtls a,subcon_ord_dtls b,subcon_ord_mst c where a.order_id=b.id and c.id=b.mst_id and a.system_mst_id=$system_mst_id and a.production_type=10 and a.entry_form=652 and a.embel_name=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id";  
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
	                <th width="80">Production Date</th>
	                <th width="80">Production Qty</th>
	                <th width="80">Reject Qty</th>
	                <th width="150">Serving Company</th>
	                <th width="120" >Location</th> 
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

					$parameter = $row['ID'].'**'.$row['EMBEL_NAME'].'**'.$row['SYSTEM_MST_ID'];
 				?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" >
                    <td width="30" align="center"> <?=$i; ?>
						<input type="hidden" id="mstidall_<?=$i; ?>" value="<?=$row['ID']; ?>" style="width:30px"/>
						<input type="hidden" id="emblname_<?=$i; ?>" name="emblname[]" value="3" />

						<input type="hidden" id="serving_company_<?=$i; ?>" value="<?=$row['SERVING_COMPANY']; ?>" />
						<input type="hidden" id="location_<?=$i; ?>" value="<?=$row['LOCATION_ID']; ?>" />

                    </td>
					<td width="100" align="center" onClick="fnc_load_from_dtls('<?=$parameter ?>');"><p><?= $row['CUST_STYLE_REF']?></p></td>
					<td width="80" align="center" onClick="fnc_load_from_dtls('<?=$parameter ?>');"><p><?= $row['ORDER_NO']?></p></td> 
                    <td width="150" align="center" onClick="fnc_load_from_dtls('<?=$parameter ?>');"><p><?=$garments_item[$row['GMTS_ITEM_ID']]; ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<?=$parameter ?>');"><p><?=change_date_format($row['PRODUCTION_DATE']); ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<?=$parameter ?>');"><p><?=$row['PRODUCTION_QNTY']; ?></p></td>
                    <td width="80" align="center" onClick="fnc_load_from_dtls('<?=$parameter ?>');"><p><?=$row['REJECT_QNTY']; ?></p></td>
                    <td width="150" align="center" onClick="fnc_load_from_dtls('<?=$parameter ?>');"><p><?=$company_arr[$row['SERVING_COMPANY']]; ?></p></td>
                    <td width="120" align="center" onClick="fnc_load_from_dtls('<?=$parameter ?>');"><p><?=$location_arr[$row['LOCATION_ID']]; ?></p></td>
                     
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

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0) // Insert Here----------------------------------------------------------
	{  
		
		$con = connect();
		// ========================================================================================
										//Create System ID 
		// ========================================================================================
		if (str_replace("'", "", $txt_system_no) == "") 
		{
            $year_cond="to_char(insert_date,'YYYY')";

          	$new_sys_number = explode("*", return_next_id_by_sequence("", "subcon_pro_gmts_system_mst",$con,1,$cbo_company_name,'SGRW',652,date("Y",time()),0,0,10,3,0 ));
          	$field_array_delivery = "id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id,location_id, production_type,embel_name,delivery_date,entry_form,working_company_id,working_location_id,remarks,buyer_id, inserted_by, insert_date";
            $mst_id = return_next_id_by_sequence(  "subcon_pro_gmts_system_mst_seq", "subcon_pro_gmts_system_mst", $con );
            $data_array_delivery = "(" . $mst_id . ",'" . $new_sys_number[1] . "','" .(int) $new_sys_number[2] . "','" . $new_sys_number[0] . "', " . $cbo_company_name . "," . $cbo_location_name . ",10,3," . $txt_rcve_date . ",652,".$cbo_emb_company.",".$cbo_wash_location."," . $txt_remark . "," .$cbo_party_name . "," .  $user_id . ",'" . $pc_date_time . "')";
            $challan_no =(int) $new_sys_number[2];
            $txt_system_no = $new_sys_number[0];

        } 
        else 
        {
            $mst_id = str_replace("'", "", $txt_system_id);
            $txt_chal_no = explode("-", str_replace("'", "", $txt_system_no));
            $challan_no = (int)$txt_chal_no[3];

            $field_array_delivery = "company_id*location_id*delivery_date*working_company_id*working_location_id*remarks*updated_by*update_date";
            $data_array_delivery = "".$cbo_company_name."*". $cbo_location_name."*". $txt_rcve_date."*".$cbo_emb_company."*".$cbo_wash_location."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";

        }
		
		$id= return_next_id("id","subcon_gmts_prod_dtls", 1);
		// ========================================================================================
										//INSERT DATA INTO  SUBCON_GMTS_PROD_DTLS
		// ========================================================================================
		$field_array1="id,system_mst_id, company_id,order_id, gmts_item_id,serving_company, location_id, embel_name, production_date, production_qnty,reject_qnty,production_type, entry_break_down_type, remarks, total_produced, yet_to_produced, inserted_by, insert_date,entry_form,status_active, is_deleted";
		$data_array1="(".$id.",".$mst_id.",".$cbo_company_name.",".$hidden_po_break_down_id.", ".$cbo_item_name.",".$cbo_emb_company.",".$cbo_wash_location.",3,".$txt_rcve_date.",".$txt_rcve_qty.",".$txt_reject_qty.",10,".$sewing_production_variable.",".$txt_remark.",".$txt_cum_rcve_qty.",".$txt_yet_to_rcve.",".$user_id.",'".$pc_date_time."',652,1,0)";
 
		// echo $data_array."---".$rID;die;\ 

		// ========================================================================================
										//INSERT DATA INTO  SUBCON_GMTS_PROD_COL_SZ
		// ========================================================================================
		$field_array="id, dtls_id,gmts_item_id,production_type, ord_color_size_id,prod_qnty,reject_qty,status_active, is_deleted";
  		$dtlsrID=true;
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			$color_sizeID_arr=sql_select( "select id, color_id from subcon_ord_breakdown where order_id=$hidden_po_break_down_id and item_id=$cbo_item_name  and status_active=1 and is_deleted=0 order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_id")];
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
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
 			$rowEx = explode("**",$colorIDvalue);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				
				if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
				{
					$dtls_id=return_next_id("id", "subcon_gmts_prod_col_sz", 1);
					if($j==0) $data_array = "(".$dtls_id.",".$id.",".$cbo_item_name.",10,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',1,0)";
					else $data_array .= ",(".$dtls_id.",".$id.",".$cbo_item_name.",10,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',1,0)";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
 		}

		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{
			$color_sizeID_arr=sql_select( "select id,size_id,color_id from subcon_ord_breakdown where order_id=$hidden_po_break_down_id and item_id=$cbo_item_name  and status_active=1 and is_deleted=0  order by size_id,color_id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_id")].$val[csf("color_id")];
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

			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
			$rowEx = explode("***",$colorIDvalue);
			//$dtls_id=return_next_id("id", "subcon_gmts_prod_col_sz", 1);
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
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "subcon_gmts_prod_col_sz", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$id.",".$cbo_item_name.",10,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',1,0)";
					else $data_array .= ",(".$dtls_id.",".$id.",".$cbo_item_name.",10,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',1,0)";
					$j++;
				}
			}
		}
		// echo $txt_system_id.'**'; die; 
		if (str_replace("'", "", $txt_system_id) == "") 
		{ 
            $challanrID = sql_insert("subcon_pro_gmts_system_mst", $field_array_delivery, $data_array_delivery, 1);
        } 
        else 
        {
            $challanrID = sql_update("subcon_pro_gmts_system_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
        }
		// echo  $challanrID ; die;
		$rID=sql_insert("subcon_gmts_prod_dtls",$field_array1,$data_array1,1);
		
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
 			$dtlsrID=sql_insert("subcon_gmts_prod_col_sz",$field_array,$data_array,1);
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
		// =============================================================================================
		//											Update Muster Data
		// =============================================================================================
		 $field_array_delivery = "company_id*location_id*delivery_date*working_company_id*working_location_id*remarks*updated_by*update_date";
        $data_array_delivery = "".$cbo_company_name."*". $cbo_location_name."*". $txt_rcve_date."*".$cbo_emb_company."*".$cbo_wash_location."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";

		// =============================================================================================
		//											Update SUBCON_GMTS_PROD_DTLS DATA
		// =============================================================================================
		
 		$field_array1="serving_company*location_id*production_date*production_qnty*reject_qnty*entry_break_down_type*remarks*total_produced*yet_to_produced*updated_by*update_date";
		

		$data_array1="".$cbo_emb_company."*".$cbo_wash_location."*".$txt_rcve_date."*".$txt_rcve_qty."*".$txt_reject_qty."*".$sewing_production_variable."*".$txt_remark."*".$txt_cum_rcve_qty."*".$txt_yet_to_rcve."*".$user_id."*'".$pc_date_time."'"; 

		// =============================================================================================
		//											UPDATE SUBCON_GMTS_PROD_COL_SZ DATA
		// =============================================================================================
		$embelName=str_replace("'","",$cbo_embel_name);
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' )// check is not gross level
		{
			$field_array="id, dtls_id,gmts_item_id,production_type, ord_color_size_id,prod_qnty,reject_qty,status_active, is_deleted";
  			$dtlsrID=true;
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id, color_id from subcon_ord_breakdown where order_id=$hidden_po_break_down_id and item_id=$cbo_item_name  and status_active=1 and is_deleted=0 order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val)
				{
					$index = $val[csf("color_id")];
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
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowEx = explode("**",$colorIDvalue);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					
					if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
					{
						$dtls_id=return_next_id("id", "subcon_gmts_prod_col_sz", 1);
						if($j==0) $data_array = "(".$dtls_id.",".$txt_mst_id.",".$cbo_item_name.",10,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',1,0)";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",".$cbo_item_name.",10,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',1,0)";
						$j++;
					}
				}
			}

			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{
				$color_sizeID_arr=sql_select( "select id,size_id,color_id from subcon_ord_breakdown where order_id=$hidden_po_break_down_id and item_id=$cbo_item_name  and status_active=1 and is_deleted=0  order by size_id,color_id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_id")].$val[csf("color_id")];
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

				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
				$rowEx = explode("***",$colorIDvalue);
				//$dtls_id=return_next_id("id", "subcon_gmts_prod_col_sz", 1);
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
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "subcon_gmts_prod_col_sz", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",".$cbo_item_name.",10,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',1,0)";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",".$cbo_item_name.",10,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',1,0)";
						$j++;
					}
				}
			}
		}
		$dtlsrDelete = execute_query("DELETE subcon_gmts_prod_col_sz where dtls_id=$txt_mst_id");
		$rID=sql_update("subcon_gmts_prod_dtls",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		// echo "10**".$field_array1."__".$data_array1."__".$data_array1;die;
		$challanrID=sql_update("subcon_pro_gmts_system_mst",$field_array_delivery,$data_array_delivery,"id","".$txt_system_id."",1);
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("subcon_gmts_prod_col_sz",$field_array,$data_array,1); 
		}
		// echo "10**".$rID ."=". $dtlsrID ."=". $challanrID;die;
		// echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".$txt_system_id."**"."insert into subcon_gmts_prod_col_sz (".$field_array.") values ".$data_array; 

		  

		if(str_replace("'","",$sewing_production_variable)!=1)
		{
			if($rID && $dtlsrID && $challanrID && $dtlsrDelete)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".$txt_system_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id)."**mst*".$txt_mst_id ."**dtls*". $dtlsrID ."**cln*". $challanrID."**delete*". $dtlsrDelete; 
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
				echo "10**".str_replace("'","",$hidden_po_break_down_id)."**mst*".$txt_mst_id ."**dtls*". $dtlsrID ."**cln*". $challanrID;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		$con = connect();
		 
		$challanrData=sql_select("SELECT id,order_id from subcon_gmts_prod_dtls where system_mst_id=$txt_system_id and status_active=1 and is_deleted=0");

		$po_id = $challanrData[0]['ORDER_ID']; 
		if(count($challanrData)==1)
		{
			$challanrID = sql_delete("subcon_pro_gmts_system_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id',$txt_system_id,1);
			$resetLoad=1;

		}
		else{
			$challanrID = 1;
			$resetLoad=2;
		}
		
		$rID = sql_delete("subcon_gmts_prod_dtls","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id',$txt_mst_id,1);

		$dtlsrID =  execute_query("DELETE subcon_gmts_prod_col_sz where dtls_id=$txt_mst_id");

		if($rID && $dtlsrID && $challanrID)
		{
			oci_commit($con);   
			echo "2**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$txt_system_id."**".$operation; 
		}
		else
		{
			oci_rollback($con);
			echo "10**".str_replace("'","",$hidden_po_break_down_id); 
			echo $rID ."=". $dtlsrID ."=". $challanrID;die;
		}
		disconnect($con);
		die;
		

		
	}
}

if($action=="populate_receive_form_data")
{
	$data=explode("**",$data); 
	$sql = "SELECT id, company_id,order_id, gmts_item_id, serving_company, location_id, embel_name,production_date,production_qnty,reject_qnty, production_type, entry_break_down_type, remarks, total_produced, yet_to_produced from subcon_gmts_prod_dtls where id='".$data[0]."' and production_type=10 and status_active=1 and is_deleted=0 order by id"; 
	$sqlResult =sql_select($sql); 

	foreach($sqlResult as $result)
	{
		echo "$('#txt_rcve_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_emb_company').val('".$result[csf('serving_company')]."');\n";
		echo "$('#cbo_company_name').val('".$result[csf('company_id')]."');\n"; 
		echo "$('#cbo_item_name').val('".$result[csf('gmts_item_id')]."');\n";


	    echo "load_drop_down( 'requires/subcon_gmts_rcve_from_wash_controller', ".$result['COMPANY_ID'].", 'load_drop_down_po_location', 'location_td' );\n";
		echo"load_drop_down( 'requires/subcon_gmts_rcve_from_wash_controller', ".$result['SERVING_COMPANY'].", 'load_drop_down_wash_location', 'wash_location_td' );\n";
		
		echo "$('#cbo_embel_name').val('".$result[csf('embel_name')]."');\n";
		
		echo "$('#txt_rcve_qty').val('".$result[csf('production_qnty')]."');\n";
		echo "$('#txt_reject_qty').val('".$result[csf('reject_qnty')]."');\n";
		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		$po_id = $result['ORDER_ID'];
		$item_id = $result['GMTS_ITEM_ID']; 
		$variableSettings = $result[csf('entry_break_down_type')];
		echo "$('#sewing_production_variable').val('".$variableSettings."');\n";
		echo "$('#embro_production_variable').val(".$variableSettings.");\n";
		
	} 
	$sys_sql = "SELECT id,sys_number,location_id,working_location_id from subcon_pro_gmts_system_mst where id=$data[2] and production_type=10 and embel_name=3  and entry_form=652 and status_active=1 and is_deleted=0";
	$system_arr = sql_select($sys_sql);
	echo "$('#txt_system_id').val('".$system_arr[0]['ID']."');\n";
	echo "$('#txt_system_no').val('".$system_arr[0]['SYS_NUMBER']."');\n";
	echo "document.getElementById('cbo_wash_location').value  = '".($system_arr[0]['WORKING_LOCATION_ID'])."';\n";
	echo "$('#cbo_location_name').val('".$system_arr[0]['LOCATION_ID']."');\n"; 

	$data_sql = "SELECT 
	SUM ( CASE WHEN a.production_type = 1 THEN a.production_qnty ELSE 0 END) AS total_cut_qty,
	SUM ( CASE WHEN a.production_type = 2 THEN a.production_qnty ELSE 0 END) AS total_sew_out,
	SUM ( CASE WHEN a.production_type = 9 AND a.embel_name=3 and a.entry_form=651 THEN a.production_qnty ELSE 0 END) AS total_issue,
	SUM ( CASE WHEN a.production_type = 10 AND a.embel_name=3 and a.entry_form=652 THEN a.production_qnty ELSE 0 END) AS total_receive,
	SUM ( CASE WHEN a.production_type = 10 AND a.embel_name = 3 and a.entry_form=652 THEN a.reject_qnty ELSE 0 END) AS total_reject
	FROM subcon_gmts_prod_dtls a
	WHERE a.status_active=1 and a.is_deleted=0 and a.order_id=$po_id and a.gmts_item_id=$item_id and a.production_type in(1,2,9,10)";

	$dataArray=sql_select($data_sql); 
	foreach($dataArray as $row)
	{	
		$ttl_cut_qty = $row['TOTAL_CUT_QTY'];
		$ttl_sew_out = $row['TOTAL_SEW_OUT'];
		$ttl_issue	 = $row['TOTAL_ISSUE'];
		$ttl_rcve	 = $row['TOTAL_RECEIVE'];
		$ttl_reject	 = $row['TOTAL_REJECT'];
		echo "$('#txt_cut_qty').val('".$ttl_cut_qty."');\n";
		echo "$('#txt_sewing_qty').val('".$ttl_sew_out."');\n";
		echo "$('#txt_cum_rcve_qty').attr('placeholder','".$ttl_rcve."');\n";
		echo "$('#txt_cum_rcve_qty').val('".$ttl_rcve."');\n";
		echo "$('#ttl_issue_qty').val('".$ttl_issue."');\n";
		$yet_to_rcve = $ttl_issue-$ttl_rcve-$ttl_reject;
		echo "$('#txt_yet_to_rcve').attr('placeholder','".$yet_to_rcve."');\n";
		echo "$('#txt_yet_to_rcve').val('".$yet_to_rcve."');\n";
	}

	echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";

	// =============================================================================================
	//											Order Data
	// =============================================================================================
	$order_sql = "select a.id, a.delivery_date,a.order_no, a.order_quantity, a.cust_style_ref ,b.subcon_job, b.company_id, b.party_id, c.order_id as order_id, c.item_id as item_id, sum(c.qnty) as qnty from  subcon_ord_dtls a, subcon_ord_mst b, subcon_ord_breakdown c where b.id=c.mst_id and a.id=c.order_id and a.status_active=1 and a.is_deleted=0 and a.job_no_mst=b.subcon_job and a.id=$po_id and c.item_id=$item_id group by a.id, a.delivery_date,a.order_no, a.order_quantity, a.cust_style_ref ,b.subcon_job, b.company_id, b.party_id, c.order_id, c.item_id"; 
	$res = sql_select($order_sql);
 	foreach($res as $v)
	{
		echo "$('#txt_order_no').val('".$v['ORDER_NO']."');\n";
		echo "$('#hidden_po_break_down_id').val('".$v['ID']."');\n";
		echo "$('#cbo_party_name').val('".$v['PARTY_ID']."');\n";
		echo "$('#txt_style_no').val('".$v['CUST_STYLE_REF']."');\n";
		echo "$('#txt_job_no').val('".$v['SUBCON_JOB']."');\n";
		echo "$('#txt_delivery_date').val('".$v['DELIVERY_DATE']."');\n";
  	}

	echo "set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);\n";  

	

	//======================================================================================								
							// ORDER AND COLOR LEVEL
	//======================================================================================
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

	
	if( $variableSettings!=1 ) // gross level
	{  
		$sql_dtls = sql_select("select a.ord_color_size_id,a.prod_qnty,a.reject_qty, size_id, color_id from subcon_gmts_prod_col_sz a,subcon_ord_breakdown b where a.dtls_id=". $data[0] ."  and a.ord_color_size_id=b.id and b.order_id=$po_id and b.item_id=$item_id");
		foreach($sql_dtls as $row)
		{
			if( $variableSettings==2 ) $index = $row['COLOR_ID']; else $index = $row['SIZE_ID'].$row['COLOR_ID'];
			$amountArr[$index] = $row['PROD_QNTY'];
			$rejectArr[$index] = $row['REJECT_QTY'];
			$rejectArr2[$index][$row['ORD_COLOR_SIZE_ID']] = $row['REJECT_QTY'];
		}

		if( $variableSettings==2 ) // color level
		{
			$sql = "SELECT a.item_id as gmts_item_id, a.color_id as color_number_id, sum(a.qnty) as order_quantity, sum(a.plan_cut) as plan_cut_qnty,
				sum(CASE WHEN c.production_type=9 and c.embel_name=3  and c.entry_form=651 then b.prod_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN c.production_type=10 and c.embel_name=3  and c.entry_form=652 then b.prod_qnty ELSE 0 END) as cur_production_qnty,
				sum(CASE WHEN c.production_type=10 and c.embel_name=3  and c.entry_form=652 then b.reject_qty ELSE 0 END) as reject_qty
				from subcon_ord_breakdown a
				left join subcon_gmts_prod_col_sz b on a.id=b.ord_color_size_id
				left join subcon_gmts_prod_dtls c on c.id=b.dtls_id
				where a.order_id=$po_id and a.item_id=$item_id and a.is_deleted=0 and a.status_active=1 group by a.item_id, a.color_id";	
		}
		else if( $variableSettings==3 ) //color and size level
		{
			$dtlsData =sql_select("SELECT a.ord_color_size_id,
				SUM (CASE WHEN a.production_type=9 AND b.embel_name=3 and b.entry_form=651 THEN a.prod_qnty ELSE 0 END)AS production_qnty,
				SUM (CASE WHEN a.production_type=10 AND b.embel_name=3 and b.entry_form=652 THEN a.prod_qnty ELSE 0 END) AS cur_production_qnty,
				sum(CASE WHEN a.production_type=10 and b.embel_name=3  and b.entry_form=652 then a.reject_qty ELSE 0 END) as reject_qty
					FROM subcon_gmts_prod_col_sz a, subcon_gmts_prod_dtls b
					WHERE  a.dtls_id=b.id and b.status_active=1  and b.is_deleted=0 and b.order_id=$po_id and b.gmts_item_id=$item_id and a.ord_color_size_id!=0 and a.production_type in(9,10) group by a.ord_color_size_id" );
				//echo $dtlsData;die;
			
			foreach($dtlsData as $row) 
			{
				$color_size_qnty_array[$row['ORD_COLOR_SIZE_ID']]['iss']= $row['PRODUCTION_QNTY'];
				$color_size_qnty_array[$row['ORD_COLOR_SIZE_ID']]['rcv']= $row['CUR_PRODUCTION_QNTY'];
				$color_size_qnty_array[$row['ORD_COLOR_SIZE_ID']]['rej']= $row['REJECT_QTY'];
			}
			unset($dtlsData);
			// echo "<pre>";print_r($color_size_qnty_array);

			$sql = "SELECT id,item_id as gmts_item_id, size_id as size_number_id,color_id as color_number_id,qnty as order_quantity,plan_cut as plan_cut_qnty
			from subcon_ord_breakdown
			where order_id=$po_id and item_id=$item_id and is_deleted=0 and status_active=1 order by color_id,size_id";

		}
		else // by default color and size level
		{
			$dtlsData =sql_select("SELECT a.ord_color_size_id,
				SUM (CASE WHEN a.production_type=9 AND b.embel_name=3 and b.entry_form=651 THEN a.prod_qnty ELSE 0 END)AS production_qnty,
				SUM (CASE WHEN a.production_type=10 AND b.embel_name=3 and b.entry_form=652 THEN a.prod_qnty ELSE 0 END) AS cur_production_qnty,
				sum(CASE WHEN a.production_type=10 and b.embel_name=3  and b.entry_form=652 then a.reject_qty ELSE 0 END) as reject_qty
					FROM subcon_gmts_prod_col_sz a, subcon_gmts_prod_dtls b
					WHERE  a.dtls_id=b.id and b.status_active=1  and b.is_deleted=0 and b.order_id=$po_id and b.gmts_item_id=$item_id and a.ord_color_size_id!=0 and a.production_type in(9,10) group by a.ord_color_size_id" );
				//echo $dtlsData;die;
			
			foreach($dtlsData as $row) 
			{
				$color_size_qnty_array[$row['ORD_COLOR_SIZE_ID']]['iss']= $row['PRODUCTION_QNTY'];
				$color_size_qnty_array[$row['ORD_COLOR_SIZE_ID']]['rcv']= $row['CUR_PRODUCTION_QNTY'];
				$color_size_qnty_array[$row['ORD_COLOR_SIZE_ID']]['rej']= $row['REJECT_QTY'];
			}
			unset($dtlsData);
			//echo "<pre>";print_r($color_size_qnty_array);

			$sql = "SELECT id,item_id as gmts_item_id, size_id as size_number_id,color_id as color_number_id,  qnty as order_quantity,plan_cut as plan_cut_qnty
			from subcon_ord_breakdown
			where order_id=$po_id and item_id=$item_id and is_deleted=0 and status_active=1 order by color_id,size_id";
		}

		$colorResult = sql_select($sql);
		//print_r($sql);die;
		$colorHTML=""; $colorID=''; $chkColor = array(); $i=0; $totalQnty=0; $colorWiseTotal=0;
		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{
				$amount = $amountArr[$color[csf("color_number_id")]];
				$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]-$rejectAmt).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej."  value="'.$rejectAmt.'" onblur="fn_colorRej_total('.($i+1).')  '.$disable.'"></td></td></tr>';
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
				$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
				// $rej_qnty=$rejectArr2[$index][$color[csf('id')]];

				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" ><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';
				$colorWiseTotal += $amount;
			}
			$i++;
		}
		//echo $colorHTML;die;
		if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><tr><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></tr><tr> <th colspan="3"><div style="padding-left: 30px;text-align:left"><input type="checkbox" onClick="active_placeholder_qty_color(' . $color[csf("color_number_id")] . ')" id="set_all">&nbsp;<label for="set_all">Available Qty Auto Fill</label></div></th> </tr></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		if( $variableSettings==3 )echo "$totalFn;\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	}
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
		$wash_comp =return_library_array("select id,company_name from lib_company comp where is_deleted=0 and status_active=1 order by company_name", 'id', 'company_name'); 
	?>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead>
	                <tr>
	                    <th>Buyer Name</th>
	                    <th>Wash Company</th>
	                    <th>Job No</th>
	                    <th>Receive No</th>
	                    <th>Order No</th>
	                    <th width="200">Date Range</th>
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
						<td>
							<?  
								$is_disabled = $party_name ? 1: 0;
								echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (9,3)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $party_name, "",$is_disabled,0 ); 
							?> 
						</td>	
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
	                    	<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('cbo_party_name').value+'_'+document.getElementById('wash_company').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_issue_no').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_system_number_list_view', 'search_div', 'subcon_gmts_rcve_from_wash_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
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
    $party_id 	= trim($ex_data[0]);
    $wash_comp 	= trim($ex_data[1]);
	$job_no 	= trim($ex_data[2]);
    $issue_id 	= trim($ex_data[3]);
	$order_no 	= trim($ex_data[4]);
    $txt_date_from = $ex_data[5];
	$txt_date_to = $ex_data[6];  

	$buyer_arr=return_library_array( "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (9,3)) order by buyer_name",'id','buyer_name');

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$sql_cond="";
	
	
	if($party_id!='0')
	{
		$sql_cond .= " and c.party_id=$party_id";
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
		$sql_cond .= " and b.order_no like '%".$order_no."'";
	}
	if($job_no!="")
	{
		$sql_cond .= " and c.subcon_job like '%".$job_no."'";
	}
	

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

	$sql ="SELECT a.id,a.company_id,c.party_id as buyer,c.subcon_job,b.cust_style_ref as style,b.order_no,a.sys_number,a.sys_number_prefix_num as receive_id,d.production_qnty as receive_qty,d.serving_company as wash_comp,d.production_date as receive_date  from subcon_pro_gmts_system_mst a, subcon_ord_dtls b, subcon_ord_mst c,subcon_gmts_prod_dtls d where  b.mst_id=c.id and b.id=d.order_id and a.id=d.system_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.production_type=10 and a.embel_name='3' and a.entry_form=652 $sql_cond group by a.id,a.company_id,c.party_id,c.subcon_job,b.cust_style_ref,b.order_no,a.sys_number_prefix_num,a.sys_number,d.production_qnty,d.serving_company,d.production_date order by a.id DESC";
	
	$arr=array(0=>$buyer_arr,6=>$company_arr); 
	echo create_list_view("list_view", "Buyer,Job No,Style,Order No,Receive ID, Receive Qty,Wash Company,Receive Date","80,100,100,100,100,100,120,100","870","340",0, $sql , "js_set_value","id,sys_number,company_id", "",1, "buyer,0,0,0,0,0,wash_comp,0", $arr,"buyer,subcon_job,style,order_no,receive_id,receive_qty,wash_comp,receive_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,0,0,0") ;
	exit();
}

if($action=="populate_mst_form_data")
{
	$sql ="SELECT id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, delivery_date,working_company_id,location_id,working_location_id,buyer_id,remarks from subcon_pro_gmts_system_mst a where a.id=$data and production_type=10 and embel_name=3 and entry_form=652";
	$result =sql_select($sql);
	 
	echo"load_drop_down( 'requires/subcon_gmts_rcve_from_wash_controller', ".$result[0]['WORKING_COMPANY_ID'].", 'load_drop_down_wash_location', 'cbo_wash_location' );\n";

	echo "$('#txt_system_no').val(`".$result[0]['SYS_NUMBER']."`);\n";
	echo "$('#txt_system_id').val(`".$result[0]['ID']."`);\n";
	echo "$('#txt_mst_id').val(`".$result[0]['ID']."`);\n";
	echo "$('#cbo_company_name').val(`".$result[0]['COMPANY_ID']."`);\n";
	echo "$('#cbo_party_name').val(`".$result[0]['BUYER_ID']."`);\n";
	echo "$('#cbo_location_name').val(`".$result[0]['LOCATION_ID']."`);\n";
	echo "$('#txt_rcve_date').val(`".change_date_format($result[0]['DELIVERY_DATE'])."`);\n";
	echo "$('#cbo_emb_company').val(`".$result[0]['WORKING_COMPANY_ID']."`);\n";
	echo "$('#cbo_wash_location').val(`".$result[0]['WORKING_LOCATION_ID']."`);\n";
	echo "$('#txt_remark').val(`".$result[0]['REMARKS']."`);\n";
	echo "$('#cbo_party_name').attr('disabled','disabled');\n";
	echo "set_button_status(0, permission, 'fnc_issue_print_embroidery_entry',1,0);\n";
 	exit();
}
if($action=="wash_recv_print")
{
	extract($_REQUEST);
	$data			= explode('*',$data);
	$company 	= $data[0];
	$sys_id 		= $data[1];
	$report_title 	= $data[2];

	//print_r ($sys_id); exit();
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	$address_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');


	$sql ="SELECT a.id,a.company_id, a.location_id, a.working_company_id, a.working_location_id, c.party_id as buyer,c.subcon_job,b.cust_style_ref as style,b.order_no,a.sys_number,a.sys_number_prefix_num as issue_id,f.prod_qnty as issue_qty,d.serving_company as wash_comp,d.production_date as issue_date, e.item_id, e.color_id, e.size_id, e.qnty,d.entry_break_down_type  AS prod_variable, a.delivery_date from subcon_pro_gmts_system_mst a, subcon_ord_dtls b, subcon_ord_mst c,subcon_gmts_prod_dtls d, subcon_ord_breakdown e, subcon_gmts_prod_col_sz f where a.id = $sys_id and b.mst_id=c.id and b.id=d.order_id and a.id=d.system_mst_id and e.mst_id=c.id and e.order_id = b.id and f.dtls_id=d.id and f.ord_color_size_id = e.id and f.status_active=1 and f.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.production_type=10 and a.embel_name='3' and a.entry_form=652  group by a.id,
	a.company_id,
	a.location_id,
	a.working_company_id,
	a.working_location_id,
	c.party_id,
	c.subcon_job,
	b.cust_style_ref,
	b.order_no,
	a.sys_number_prefix_num,
	a.sys_number,
	f.prod_qnty,
	d.serving_company,
	d.production_date,
	e.item_id,
	e.color_id,
	e.size_id,
	e.qnty,
	d.entry_break_down_type, a.delivery_date order by a.id DESC";

	//echo $sql; exit();
	$sql_res =sql_select($sql);
	$size_arr = array();
	$prod_arr = array();
	$data_arr = array();
	$total_arr = array();
	$color_width =0;
	foreach ($sql_res as  $v) 
	{
		$sys_no 		 	= $v['SYS_NUMBER'];
		$wash_comp			= $v['WORKING_COMPANY_ID'];
		$wash_location		= $v['WORKING_LOCATION_ID'];
		$comp_location		= $v['LOCATION_ID'];
		$delivery_date 		= $v['DELIVERY_DATE'];
		//$source 			= $v['SOURCE'];
		$prod_variable 		= $v['PROD_VARIABLE'];
		
		$data_arr[$v['ORDER_NO']][$v['COLOR_ID']]['COMPANY_ID'] 	= $v['COMPANY_ID'];
		$data_arr[$v['ORDER_NO']][$v['COLOR_ID']]['BUYER_NAME'] 	= $v['BUYER'];
		$data_arr[$v['ORDER_NO']][$v['COLOR_ID']]['JOB_NO'] 		= $v['SUBCON_JOB'];
		$data_arr[$v['ORDER_NO']][$v['COLOR_ID']]['STYLE_REF_NO'] 	= $v['STYLE'];
		$data_arr[$v['ORDER_NO']][$v['COLOR_ID']]['PO_NUMBER'] 		= $v['ORDER_NO']; 
		$data_arr[$v['ORDER_NO']][$v['COLOR_ID']]['ITEM'] 			= $v['ITEM_ID'];
		$data_arr[$v['ORDER_NO']][$v['COLOR_ID']]['ISS_QTY'] 		+= $v['ISSUE_QTY'];

		if ($prod_variable ==2) 	//color Level
		{
			$color_width = 100;
		}
		elseif ($prod_variable ==3) //color and Size Level
		{
			$size_arr[$v['SIZE_ID']]=$v['SIZE_ID'];
			$prod_arr[$v['ORDER_NO']][$v['ITEM_ID']][$v['COLOR_ID']][$v['SIZE_ID']]['ISS_QTY'] += $v['ISSUE_QTY'];
			$total_arr[$v['SIZE_ID']] += $v['ISSUE_QTY'];
			$color_width = 100;
		}
		

	}
	// pre($prod_arr);die;
	$size_count = count($size_arr);
	$width = 650+$color_width+($size_count*50);

	?>
	<div style="width:<?= $width+20?>px;">
		<div style="display: flex;">
			<div class="logo" style="width:100px">
				<!-- <img src="<?//= $_SESSION['logic_erp']["group_logo"]; ?>" alt="Logo" width="auto" height="45" align="Left" style="margin:3px;"> -->
			</div>
			<div style="text-align:center;width:920px">
				<p style="font-size:22px;margin:0;"><strong><? echo $company_library[$company]; ?></strong></p>
				<p style="font-size:16px;margin:0;"><?= $address_arr[$comp_location]?></p>
				<p style="font-size:16px;margin:0;"><strong>Challan-<?= $sys_no ?></strong></p>
			</div> 
		</div>
		<div style="display: flex;">
			<div style="width:450px;">
				<table>
					<tr>
						<td style="font-size:14px"><strong>Wash Company</strong> </td>
						<td align="left" ><strong>: </strong> <?= $company_library[$wash_comp]; ?></td>
					</tr>	
					<tr>
						<td  style="font-size:14px"><strong>Address</strong> </td>
						<td align="left"><strong>: </strong> <?= $address_arr[$wash_location]; ?></td>
					</tr> 
				</table>
			</div> 
			<div style="width:500px;">
				<table width="100%">
					<tr>
						<td style="font-size:14px;text-align:right; width:80%"><strong>Delivery Date</strong></td>
						<td style="text-align:right; width:20%;"><strong>: </strong> <?=$delivery_date?></td>
					</tr>	
					<tr>
						<!-- <td style="font-size:20px;margin-left:80px;"><strong>Source</strong></td>
						<td><strong>: </strong> <?//= $knitting_source[$source] ?></td> -->
					</tr> 
				</table>
			</div> 
		</div>
		<table width="<?= $width+20?>" cellspacing="0" align="right" border="1" cellspacing="0"  style="margin-top: 30px;">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="120">Buyer Name</th>
					<th width="100">Job No</th>
					<th width="100">Style Ref.</th>
					<th width="130">Order No.</th>
					<th width="100">Item</th>
					<th width="100">Color/Size</th>
					<?
						foreach ($size_arr as $size_id) 
						{ 
							?>
								<th width="50"><?= $size_library[$size_id] ?></th>
							<?
						}
					?>
					
					<th width="70">Issue.Qty</th> 
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
									<td><?= $buyer_library[$v['BUYER_NAME']] ?></td> 
									<td><?= $v['JOB_NO'] ?></td> 
									<td><?= $v['STYLE_REF_NO'] ?></td> 
									<td><?= $v['PO_NUMBER'] ?></td>   
									<td><?= $garments_item[$v['ITEM']] ?></td>  
									<td><?= $color_library[$color_id]; ?></td>  
									<?
										foreach ($size_arr as $size) 
										{
											$size_qty = $prod_arr[$po_id][$v['ITEM']][$color_id][$size]['ISS_QTY'];
											?>
												<th align="right"><?=$size_qty?></th>
											<?
										}
									?>
									<td align="right"><?= $v['ISS_QTY']?> </td>
								</tr>
							<?
						}
						 
					}	

				?>	
			</tbody>
			<tfoot>
				<th colspan="7">Total</th>
				<?
					foreach ($size_arr as $size) 
					{
						$ttlsize_qty = $total_arr[$size];
						?>
							<th align="right"><?=$ttlsize_qty?></th>
						<?
					}
				?>
				<th align="right"><?= $total_prod_qty ?></th>
			</tfoot>
		</table>	
			 
	</div>
	<?
	exit();
}
?>