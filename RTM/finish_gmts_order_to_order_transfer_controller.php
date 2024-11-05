<?
session_start();
include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//************************************ Start *************************************************

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select smv_source,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("smv_source")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}
 	exit();
}

$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

if ($action=="order_popup")
{
	echo load_html_head_contents("Order Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "<pre>";print_r($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			$('#order_data').val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:980px;">
		<form name="searchdescfrm" id="searchdescfrm">
			<fieldset style="width:970px;margin-left:3px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table" border="1" rules="all">
	                <thead>
	                    <th>Buyer Name</th>
	                    <th width="130">Search By</th>
	                    <th width="180" align="center" id="search_by_td_up">Enter PO No</th>
	                    <th width="230">Shipment Date Range</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="order_data" id="order_data" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
								echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
							?>
	                    </td>
	                    <td align="center">
	                    	<?
								$searchby_arr=array(1=>"PO No",2=>"Style Ref.",3=>"Job No",4=>"File No",5=>" Internal Ref.");
								$dd="change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 130, $searchby_arr,"",0, "--Select--", 1,$dd,0 );
							?>
	                    </td>
	                    <td id="search_by_td">
	                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td>
	                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
	                    </td>
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>', 'create_po_search_list_view', 'search_div', 'finish_gmts_order_to_order_transfer_controller', 'setFilterGrid(\'tbl_po_list\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	            </table>
	        	<div style="margin-top:10px" id="search_div"></div>
			</fieldset>
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
 	$data=explode('_',$data);


	if($data[0]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name='".$data[0]."'";
	}

	$search_by=$data[1];
	$txt_search_common=trim($data[2]);
	$company_id=$data[3];

	if ($data[4]!="" &&  $data[5]!="")
	{
		if($db_type==0)
		{
			$shipment_date = "and c.ACC_SHIP_DATE between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and c.ACC_SHIP_DATE between '".change_date_format($data[4],'','',1)."' and '".change_date_format($data[5],'','',1)."'";
		}
	}
	else
		$shipment_date ="";

	$sql_cond="";
	if($txt_search_common!="")
	{
		if($search_by==1)
			$sql_cond = " and c.acc_po_no like '%".trim($txt_search_common)."%'";
		else if($search_by==2)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if($search_by==3)
			$sql_cond = " and a.JOB_NO_PREFIX_NUM = $txt_search_common";
		else if($search_by==4)
			$sql_cond = " and b.file_no like '".trim($txt_search_common)."%'";
		else if($search_by==5)
			$sql_cond = " and b.grouping like '%".trim($txt_search_common)."%'";
 	}

	$type=$data[6];
	if($type=="from") $status_cond=" and b.status_active in(1,2,3)"; else $status_cond=" and b.status_active in (1,2,3)";

	$sql = "SELECT b.id,  a.buyer_name,  a.job_no, a.style_ref_no, c.ACC_SHIP_DATE,c.id as po_id, c.acc_po_no, b.grouping,d.gmts_item,d.country_id, d.po_qty from wo_po_details_master a, wo_po_break_down b, WO_PO_ACC_PO_INFO c,WO_PO_ACC_PO_INFO_DTLS d where a.id = b.job_id and b.id=c.po_break_down_id and c.id = d.mst_id and a.company_name=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $sql_cond $shipment_date $buyer_id_cond order by c.ACC_SHIP_DATE desc";
	// echo $sql;die;
	$result = sql_select($sql);
	$data_array = array();
	foreach ($result as $v)
	{
		$data_array[$v['ID']][$v['PO_ID']][$v['COUNTRY_ID']][$v['GMTS_ITEM']]['buyer_name'] = $v['BUYER_NAME'];
		$data_array[$v['ID']][$v['PO_ID']][$v['COUNTRY_ID']][$v['GMTS_ITEM']]['job_no'] = $v['JOB_NO'];
		$data_array[$v['ID']][$v['PO_ID']][$v['COUNTRY_ID']][$v['GMTS_ITEM']]['style'] = $v['STYLE_REF_NO'];
		$data_array[$v['ID']][$v['PO_ID']][$v['COUNTRY_ID']][$v['GMTS_ITEM']]['ship_date'] = $v['ACC_SHIP_DATE'];
		$data_array[$v['ID']][$v['PO_ID']][$v['COUNTRY_ID']][$v['GMTS_ITEM']]['po_no'] = $v['ACC_PO_NO'];
		$data_array[$v['ID']][$v['PO_ID']][$v['COUNTRY_ID']][$v['GMTS_ITEM']]['grouping'] = $v['GROUPING'];
		$data_array[$v['ID']][$v['PO_ID']][$v['COUNTRY_ID']][$v['GMTS_ITEM']]['po_qty'] += $v['PO_QTY'];
	}

 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" align="left">
        <thead>
            <th width="40">SL</th>
            <th width="70">Ship. Date</th>
            <th width="100">Order No</th>
            <th width="65">Buyer</th>
            <th width="120">Style</th>
            <th width="90">Job No</th>
            <th width="80">Internal Ref.</th>
            <th width="130">Item</th>
            <th width="90">Country</th>
            <th width="80">Order Qty</th>
        </thead>
	</table>
	<div style="width:960px; max-height:220px;overflow-y:scroll;float:left;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" id="tbl_po_list" align="left">
			<?
			$i=1;
            foreach( $data_array as $order_id => $order_data )
            {
				foreach ($order_data as $po_id => $po_data) 
				{
					foreach ($po_data as $country_id => $country_data) 
					{
						foreach ($country_data as $item_id => $r) 
						{
				
							if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$shipDate=change_date_format($r["ship_date"]);
							$data=$po_id."_".$r["po_no"]."_".$r["buyer_name"]."_".$r["style"]."_".$r["job_no"]."_".$r["po_qty"]."_".$item_id."_".$r["ship_date"]."_".$country_id."_".$r["grouping"]."_".$order_id;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $data; ?>');" >
								<td width="40"><?php echo $i; ?></td>
								<td width="70" align="center"><?php echo $shipDate; ?></td>
								<td width="100"><p><?php echo $r["po_no"]; ?></p></td>
								<td width="65"><p><?php echo $buyer_arr[$r["buyer_name"]]; ?></p></td>
								<td width="120"><p><?php echo $r["style"]; ?></p></td>
								<td width="90"><p><?php echo $r["job_no"]; ?></p></td>
								<td width="80"><p><?php echo $r["grouping"]; ?>&nbsp;</p></td>
								<td width="130"><p><?php echo $garments_item[$item_id];?></p></td>
								<td width="90"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
								<td width="80" align="right"><?php echo $r["po_qty"];?>&nbsp;</td>
							</tr>
							<?
							$i++;
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

// ============ req popup ===============
if ($action=="requisition_popup")
{
	echo load_html_head_contents("Order Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	// echo "<pre>";print_r($_REQUEST);
	// echo "<pre>";print_r($data);
	?>
	<script>
		//alert('#order_data');
		function js_set_value(data)
		{
			$('#order_data').val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:980px;">
		<form name="searchdescfrm" id="searchdescfrm">
			<fieldset style="width:970px;margin-left:3px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table" border="1" rules="all">
	                <thead>
	                    <th>Buyer Name</th>
	                    <th width="130">Search By</th>
	                    <th width="180" align="center" id="search_by_td_up">Enter Order Number</th>
	                    <th width="230">Shipment Date Range</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="order_data" id="order_data" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
								echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
							?>
	                    </td>
	                    <td align="center">
	                    	<?
								$searchby_arr=array(1=>"Requisition No");
								$dd="change_search_event(this.value, '0', '0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 130, $searchby_arr,"",1, "--Select--", 1,$dd,0 );
							?>
	                    </td>
	                    <td id="search_by_td">
	                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
	                    <td>
	                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
	                    </td>
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>', 'create_requisition_search_list_view', 'search_div', 'finish_gmts_order_to_order_transfer_controller', 'setFilterGrid(\'tbl_po_list\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	            </table>
	        	<div style="margin-top:10px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_requisition_search_list_view")
{
 	 $data=explode('_',$data);
	// echo "<pre>";print_r($data);die;


	if($data[0]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name='".$data[0]."'";
	}

	$search_by=$data[1];
	$txt_search_common=trim($data[2]);
	$company_id=$data[3];

	if ($data[4]!="" &&  $data[5]!="")
	{
		if($db_type==0)
		{
			$shipment_date = "and a.ESTIMATED_SHIPDATE between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and a.ESTIMATED_SHIPDATE between '".change_date_format($data[4],'','',1)."' and '".change_date_format($data[5],'','',1)."'";
		}
	}
	else
		$shipment_date ="";

	$sql_cond="";
	if($txt_search_common!="")
	{
		if($search_by==1)
			$sql_cond = " and a.REQUISITION_NUMBER_PREFIX_NUM like '%".trim($txt_search_common)."%'";
 	}

	$type=$data[6];

	$sql = "SELECT a.id, a.buyer_name,a.requisition_number,a.style_ref_no,a.ESTIMATED_SHIPDATE,b.color_id,b.gmts_item_id,b.sample_prod_qty,b.sample_name  from SAMPLE_DEVELOPMENT_MST a, SAMPLE_DEVELOPMENT_DTLS b where a.id = b.sample_mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0  $sql_cond $shipment_date $buyer_id_cond order by a.ESTIMATED_SHIPDATE";
	// echo $sql;die;
	$result = sql_select($sql);

 	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
 	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$sample_arr=return_library_array( "select id, SAMPLE_NAME from LIB_SAMPLE",'id','SAMPLE_NAME');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" align="left">
        <thead>
            <th width="40">SL</th>
            <th width="70">Ship. Date</th>
            <th width="100">Req. No</th>
            <th width="100">Booking No</th>
            <th width="100">Buyer</th>
            <th width="120">Style</th>
            <th width="130">Color</th>
            <th width="130">Item</th>
            <th>Req. Qty</th>
        </thead>
	</table>
	<div style="width:960px; max-height:220px;overflow-y:auto;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" id="tbl_po_list" align="left">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$data=$row[csf("id")]."_".$row[csf("requisition_number")]."_".$row[csf("buyer_name")]."_".$row[csf("style_ref_no")]."_".$row[csf("color_id")]."_".$row[csf("gmts_item_id")]."_".$row[csf("sample_prod_qty")]."_".$buyer_arr[$row[csf("buyer_name")]]."_".$garments_item[$row[csf("gmts_item_id")]]."_".$row[csf("ESTIMATED_SHIPDATE")]."_".$row[csf("SAMPLE_NAME")];
				// echo "<pre>";print_r($data);
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $data; ?>');" >
					<td width="40"><?php echo $i; ?></td>
					<td width="70" align="center"><?php echo $row[csf("ESTIMATED_SHIPDATE")]; ?></td>
					<td width="100"><p><?php echo $row[csf("requisition_number")]; ?></p></td>
					<td width="100"><p><?php //echo $row[csf("job_no")]; ?></p></td>
					<td width="100"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
					<td width="120"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
					<td width="130"><p><?php echo $color_arr[$row[csf("color_id")]];?></p></td>
					<td width="130"><p><?php echo $garments_item[$row[csf("gmts_item_id")]];?></p></td>
					<td align="right"><?php echo $row[csf("sample_prod_qty")];?></td>
				</tr>
				<?
				$i++;

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
	$country_id = $dataArr[2];
	$company_id = $dataArr[3];

	$productionVariable= return_field_value("is_control","variable_settings_production","company_name=$company_id and variable_list=33 and page_category_id=32 ");

	/* $productionData = sql_select("SELECT
	sum(case when production_type=8 then production_quantity end) as finish_qnty,
	sum(case when production_type=10 then production_quantity end) as finish_transfer_qnty,
	from pro_garments_production_mst
	where po_break_down_id=".$po_id." and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0"); */

	$dtlsData = sql_select("SELECT
	sum(CASE WHEN b.production_type=8 then a.production_qnty ELSE 0 END) as finish_qnty,
	sum(CASE WHEN b.production_type=10 and b.trans_type=5 then a.production_qnty ELSE 0 END) as trans_in_qty,
	sum(CASE WHEN b.production_type=10 and b.trans_type=6 then a.production_qnty ELSE 0 END) as trans_out_qty,
	sum(CASE WHEN b.production_type=14 then a.production_qnty ELSE 0 END) as del_qty,
	sum(CASE WHEN b.production_type=84 then a.production_qnty ELSE 0 END) as rcv_rtn_qty
	from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.production_type in (8,10,14,84) and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and b.production_type in(8,10,14,84) ");

	//===================== getting exfact qty ====================
	// if($productionVariable==1)
	// {
		$del_qty=$dtlsData[0][csf('del_qty')];
		//$finish_qty=$dtlsData[0][csf('finish_qnty')] + $dtlsData[0][csf('rcv_rtn_qty')] - $del_qty + $dtlsData[0][csf('trans_in_qty')];
		$finish_qty=$dtlsData[0][csf('finish_qnty')];
		if($finish_qty=="") $finish_qty=0;

		$totalDel=$dtlsData[0][csf('del_qty')];
		$trans_in_qty=$dtlsData[0][csf('trans_in_qty')];
		$trans_out_qty=$dtlsData[0][csf('trans_out_qty')];
		$rcv_rtn_qty=$dtlsData[0][csf('rcv_rtn_qty')];
	// }
	// else
	// {
		$exfactData = sql_select("SELECT sum(d.production_qnty) as ex_fact_qty from pro_ex_factory_mst c, pro_ex_factory_dtls d, pro_ex_factory_delivery_mst e where c.id=d.mst_id and e.id=c.delivery_mst_id and c.po_break_down_id=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0");
		$exfact_qty=$exfactData[0][csf('ex_fact_qty')];
		//$finish_qty=$dtlsData[0][csf('finish_qnty')] + $dtlsData[0][csf('rcv_rtn_qty')] - $exfact_qty+$dtlsData[0][csf('trans_in_qty')];
		$finish_qty=$dtlsData[0][csf('finish_qnty')];
		if($finish_qty=="") $finish_qty=0;
	// }

	// $total_transfer_qnty = $productionData[0][csf('finish_transfer_qnty')];
	$total_transfer_qnty = $dtlsData[0][csf('trans_out_qty')];
	if($total_transfer_qnty=="") $total_transfer_qnty=0;

	echo "$('#txt_finish_quantity').val('".$finish_qty."');\n";

	echo "$('#txt_cumul_quantity').attr('placeholder','".$total_transfer_qnty."');\n";
	echo "$('#txt_cumul_quantity').val('".$total_transfer_qnty."');\n";

	$yet_to_produced = ($finish_qty+$rcv_rtn_qty+$trans_in_qty)-($exfact_qty+$totalDel+$trans_out_qty);
	$yettoTitel="Total Finish Qty:".$finish_qty."-Total Delivery Qty:".$totalDel."+( Trans In:".$trans_in_qty."- Trans Out:".$trans_out_qty."+ Rec. Return:".$rcv_rtn_qty.")";

	echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
	echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
	echo "$('#txt_yet_quantity').attr('title','".$yettoTitel."');\n";

 	exit();
}

if($action=="populate_sample_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$req_id = $dataArr[0];
	$item_id = $dataArr[1];
	// $country_id = $dataArr[2];
	

	// =================== prod qty ==================
	$productionData = sql_select("SELECT sum(case when b.entry_form_id=130 then b.qc_pass_qty end) as finish_qnty from SAMPLE_SEWING_OUTPUT_MST a, SAMPLE_SEWING_OUTPUT_DTLS b where a.id=b.SAMPLE_SEWING_OUTPUT_MST_ID and a.SAMPLE_DEVELOPMENT_ID=".$req_id." and b.item_number_id='$item_id' and b.status_active=1 and b.is_deleted=0");

	//===================== getting exfact qty ====================
	$exfactData = sql_select("SELECT sum(ex_factory_qty) as ex_fact_qty from SAMPLE_EX_FACTORY_DTLS where SAMPLE_DEVELOPMENT_ID=$req_id and gmts_item_id=$item_id and entry_form_id=132 and status_active=1 and is_deleted=0");

	// ====================== transfer qty =======================
	// $productionData = sql_select("SELECT sum(case when a.production_type=10 then production_quantity end) as finish_transfer_qnty from PRO_GMTS_DELIVERY_MST a, pro_garments_production_mst b where a.id=b.delivery_mst_id and po_break_down_id=".$po_id." and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0");

 	$exfact_qty=$exfactData[0][csf('ex_fact_qty')];
	$finish_qty=$productionData[0][csf('finish_qnty')] - $exfact_qty;
	if($finish_qty=="") $finish_qty=0;

	$total_transfer_qnty = $productionData[0][csf('finish_transfer_qnty')];
	if($total_transfer_qnty=="") $total_transfer_qnty=0;

	echo "$('#txt_finish_quantity').val('".$finish_qty."');\n";
	echo "$('#txt_cumul_quantity').attr('placeholder','".$total_transfer_qnty."');\n";
	echo "$('#txt_cumul_quantity').val('".$total_transfer_qnty."');\n";
	$yet_to_produced = $finish_qty-$total_transfer_qnty;
	echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
	echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";

 	exit();
}

if($action=="populate_design_view")
{
	if($data==1) // order to order
	{
		?>
		<div id="order_to_order">
				<table width="920" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
					<tr>
						<td width="49%" valign="top">
							<fieldset>
								<legend>From Order</legend>
								<table id="from_order_info" cellpadding="0" cellspacing="1" width="100%">
									<tr>
										<td width="80" class="must_entry_caption">Order No</td>
										<td width="140">
											<input type="text" name="txt_from_order_no" id="txt_from_order_no" class="text_boxes" style="width:120px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('from');" readonly />
											<input type="hidden" name="txt_from_order_id" id="txt_from_order_id" readonly>
											<input type="hidden" name="txt_from_plan_cut_qty" id="txt_from_plan_cut_qty" readonly>
											<input type="hidden" name="txt_from_job_no" id="txt_from_job_no" readonly>
											<input type="hidden" name="txt_from_hid_po_id" id="txt_from_hid_po_id" readonly>

										</td>
										<td>Order Qnty</td>
										<td width="140">
											<input type="text" name="txt_from_po_qnty" id="txt_from_po_qnty" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
									</tr>
									<tr>
										<td>Buyer</td>
										<td>
											<?
												echo create_drop_down( "cbo_from_buyer_name", 132, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);
											?>
										</td>
										<td>Style Ref.</td>
										<td>
											<input type="text" name="txt_from_style_ref" id="txt_from_style_ref" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
									</tr>
									<tr>
										<td>Ref. No</td>
										<td>
											<input type="text" name="txt_from_ref_no" id="txt_from_ref_no" class="text_boxes" style="width:120px" disabled="disabled" placeholder="Display" />
										</td>
										<td>Gmts Item</td>
										<td>
											<?
												echo create_drop_down('cbo_from_gmts_item',132,$garments_item,'',1,'Display','','',1);
											?>
										</td>
									</tr>
									<tr>
										<td>Shipment Date</td>
										<td>
											<input type="text" name="txt_from_shipment_date" id="txt_from_shipment_date" class="datepicker" style="width:120px" disabled="disabled" placeholder="Display" />
										</td>
										<td>Country</td>
										<td>
											<?
												echo create_drop_down('cbo_from_country_name',132,'select id,country_name from lib_country','id,country_name',1,'Display','','',1);
											?>
										</td>
									</tr>
								</table>
							</fieldset>
						</td>
						<td width="2%" valign="top"></td>
						<td width="49%" valign="top">
							<fieldset>
								<legend>To Order</legend>
								<table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >
									<tr>
										<td width="80" class="must_entry_caption">Order No</td>
										<td width="140">
											<input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:120px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('to');" readonly />
											<input type="hidden" name="txt_to_order_id" id="txt_to_order_id" readonly>
											<input type="hidden" name="txt_to_plan_cut_qty" id="txt_to_plan_cut_qty" readonly>
											<input type="hidden" name="txt_to_job_no" id="txt_to_job_no" readonly>
											<input type="hidden" name="txt_to_hid_po_id" id="txt_to_hid_po_id" readonly>
										</td>
										<td>Order Qnty</td>
										<td width="140">
											<input type="text" name="txt_to_po_qnty" id="txt_to_po_qnty" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
									</tr>
									<tr>
										<td>Buyer</td>
										<td>
											<?
												echo create_drop_down( "cbo_to_buyer_name", 132, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);
											?>
										</td>
										<td>Style Ref.</td>
										<td>
											<input type="text" name="txt_to_style_ref" id="txt_to_style_ref" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
									</tr>
									<tr>
										<td>Ref. No</td>
										<td>
											<input type="text" name="txt_to_ref_no" id="txt_to_ref_no" class="text_boxes" style="width:120px" disabled="disabled" placeholder="Display"/>
										</td>
										<td>Gmts Item</td>
										<td>
											<?
												echo create_drop_down('cbo_to_gmts_item',132,$garments_item,'',1,'Display','','',1);
											?>
										</td>
									</tr>
									<tr>
										<td>Shipment Date</td>
										<td>
											<input type="text" name="txt_to_shipment_date" id="txt_to_shipment_date" class="datepicker" style="width:120px" disabled="disabled" placeholder="Display" />
										</td>
										<td>Country</td>
										<td>
											<?
												echo create_drop_down('cbo_to_country_name',132,'select id,country_name from lib_country','id,country_name',1,'Display','','',1);
											?>
										</td>
									<tr>
										<td>Is PO Ok</td>
										<td>
											<?
												echo create_drop_down('cbo_po_ok',132,$yes_no,'',1,'-- Select --','2','','');
											?>
										</td>
									</tr>
									</tr>
								</table>
						</fieldset>
						</td>
					</tr>
				</table>
			</div>
		<?
	}
	else if($data==2) // order to sample
	{
		?>
		<div id="order_to_sample">
				<table width="920" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
					<tr>
						<td width="49%" valign="top">
							<fieldset>
								<legend>From Order</legend>
								<table id="from_order_info" cellpadding="0" cellspacing="1" width="100%">
									<tr>
										<td width="80" class="must_entry_caption">Order No</td>
										<td width="140">
											<input type="text" name="txt_from_order_no" id="txt_from_order_no" class="text_boxes" style="width:120px;" placeholder="Double click to search" onDblClick="openmypage_orderNo('from');" readonly />
											<input type="hidden" name="txt_from_order_id" id="txt_from_order_id" readonly>
											<input type="hidden" name="txt_from_plan_cut_qty" id="txt_from_plan_cut_qty" readonly>
										</td>
										<td>Order Qnty</td>
										<td width="140">
											<input type="text" name="txt_from_po_qnty" id="txt_from_po_qnty" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
									</tr>
									<tr>
										<td>Buyer</td>
										<td>
											<?
												echo create_drop_down( "cbo_from_buyer_name", 132, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);
											?>
										</td>
										<td>Style Ref.</td>
										<td>
											<input type="text" name="txt_from_style_ref" id="txt_from_style_ref" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
									</tr>
									<tr>
										<td>Job No</td>
										<td>
											<input type="text" name="txt_from_job_no" id="txt_from_job_no" class="text_boxes" style="width:120px" disabled="disabled" placeholder="Display" />
										</td>
										<td>Gmts Item</td>
										<td>
											<?
												echo create_drop_down('cbo_from_gmts_item',132,$garments_item,'',1,'Display','','',1);
											?>
										</td>
									</tr>
									<tr>
										<td>Shipment Date</td>
										<td>
											<input type="text" name="txt_from_shipment_date" id="txt_from_shipment_date" class="datepicker" style="width:120px" disabled="disabled" placeholder="Display" />
										</td>
										<td>Country</td>
										<td>
											<?
												echo create_drop_down('cbo_from_country_name',132,'select id,country_name from lib_country','id,country_name',1,'Display','','',1);
											?>
										</td>
									</tr>
								</table>
							</fieldset>
						</td>
						<td width="2%" valign="top"></td>
						<td width="49%" valign="top">
							<fieldset>
								<legend>To Sample</legend>
								<table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >
									<tr>
										<td width="80" class="must_entry_caption">Req. No</td>
										<td width="140">
											<input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:120px;" placeholder="Double click to search" onDblClick="openmypage_reqNo('to');" readonly />
											<input type="hidden" name="txt_to_order_id" id="txt_to_order_id" readonly>
											<input type="hidden" name="txt_to_plan_cut_qty" id="txt_to_plan_cut_qty" readonly>
										</td>
										<td>Req. Qnty</td>
										<td width="140">
											<input type="text" name="txt_to_po_qnty" id="txt_to_po_qnty" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
									</tr>
									<tr>
										<td>Buyer</td>
										<td>
											<?
												echo create_drop_down( "cbo_to_buyer_name", 132, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);
											?>
										</td>
										<td>Style Ref.</td>
										<td>
											<input type="text" name="txt_to_style_ref" id="txt_to_style_ref" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
									</tr>
									<tr>

										<td>Shipment Date</td>
										<td>
											<input type="text" name="txt_to_shipment_date" id="txt_to_shipment_date" class="datepicker" style="width:120px" disabled="disabled" placeholder="Display" />

											<input type="hidden" name="txt_to_job_no" id="txt_to_job_no" class="text_boxes" style="width:120px" disabled="disabled"/>
											<input type="hidden" name="cbo_to_country_name" id="cbo_to_country_name" class="text_boxes" style="width:120px" disabled="disabled"/>
										</td>
										<td>Gmts Item</td>
										<td>
											<?
												echo create_drop_down('cbo_to_gmts_item',132,$garments_item,'',1,'Display','','',1);
											?>
										</td>

									</tr>
										<tr>
										<td>Sample</td>
										<td>
											
											<?
											
												echo create_drop_down('cbo_to_sample_name',132,"select id, SAMPLE_NAME from LIB_SAMPLE","id,SAMPLE_NAME",1,'Display','','',1);
											?>
										
										</td>
										</tr>
									
								</table>
						</fieldset>
						</td>
					</tr>
				</table>
			</div>
		<?
	}
	else if($data==3) // sample to sample
	{
		?>
		<div id="sample_to_sample">
			<table width="920" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
				<tr>
					<td width="49%" valign="top">
						<fieldset>
							<legend>From Sample</legend>
							<table id="from_order_info" cellpadding="0" cellspacing="1" width="100%">
								<tr>
									<td width="80" class="must_entry_caption">Req. No</td>
									<td width="140">
										<input type="text" name="txt_from_order_no" id="txt_from_order_no" class="text_boxes" style="width:120px;" placeholder="Double click to search" onDblClick="openmypage_reqNo('from');" readonly />
										<input type="hidden" name="txt_from_order_id" id="txt_from_order_id" readonly>
										<input type="hidden" name="txt_from_plan_cut_qty" id="txt_from_plan_cut_qty" readonly>
									</td>
									<td>Req. Qnty</td>
									<td width="140">
										<input type="text" name="txt_from_po_qnty" id="txt_from_po_qnty" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
								</tr>
								<tr>
									<td>Buyer</td>
									<td>
										<?
											echo create_drop_down( "cbo_from_buyer_name", 132, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);
										?>
									</td>
									<td>Style Ref.</td>
									<td>
										<input type="text" name="txt_from_style_ref" id="txt_from_style_ref" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
								</tr>
								<tr>
									<td>Shipment Date</td>
									<td>
										<input type="text" name="txt_from_shipment_date" id="txt_from_shipment_date" class="datepicker" style="width:120px" disabled="disabled" placeholder="Display" />
										<input type="hidden" name="txt_from_job_no" id="txt_from_job_no" disabled="disabled"/>
										<input type="hidden" name="cbo_from_country_name" id="cbo_from_country_name" disabled="disabled"/>
									</td>
									<td>Gmts Item</td>
									<td>
										<?
											echo create_drop_down('cbo_from_gmts_item',132,$garments_item,'',1,'Display','','',1);
										?>
									</td>
									
								</tr>
								<tr>
									<td>Sample</td>
									<td>
										<?
												echo create_drop_down('cbo_from_sample_name',132,"select id, SAMPLE_NAME from LIB_SAMPLE","id,SAMPLE_NAME",1,'Display','','',1);
										?>
									</td>
									</tr>
							</table>
						</fieldset>
					</td>
					<td width="2%" valign="top"></td>
					<td width="49%" valign="top">
						<fieldset>
							<legend>To Sample</legend>
							<table id="to_order_info"  cellpadding="0" cellspacing="1" width="100%" >
								<tr>
									<td width="80" class="must_entry_caption">Req. No</td>
									<td width="140">
										<input type="text" name="txt_to_order_no" id="txt_to_order_no" class="text_boxes" style="width:120px;" placeholder="Double click to search" onDblClick="openmypage_reqNo('to');" readonly />
										<input type="hidden" name="txt_to_order_id" id="txt_to_order_id" readonly>
										<input type="hidden" name="txt_to_plan_cut_qty" id="txt_to_plan_cut_qty" readonly>
									</td>
									<td>Req. Qnty</td>
									<td width="140">
										<input type="text" name="txt_to_po_qnty" id="txt_to_po_qnty" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
								</tr>
								<tr>
									<td>Buyer</td>
									<td>
										<?
											echo create_drop_down( "cbo_to_buyer_name", 132, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "Display", '', "" ,1);
										?>
									</td>
									<td>Style Ref.</td>
									<td>
										<input type="text" name="txt_to_style_ref" id="txt_to_style_ref" class="text_boxes" style="width:120px;" disabled="disabled" placeholder="Display" /></td>
								</tr>
								<tr>
									<td>Shipment Date</td>
									<td>
										<input type="text" name="txt_to_shipment_date" id="txt_to_shipment_date" class="datepicker" style="width:120px" disabled="disabled" placeholder="Display" />

										<input type="hidden" name="txt_to_job_no" id="txt_to_job_no"/>
										<input type="hidden" name="cbo_to_country_name" id="cbo_to_country_name"/>
									</td>
									<td>Gmts Item</td>
									<td>
										<?
											echo create_drop_down('cbo_to_gmts_item',132,$garments_item,'',1,'Display','','',1);
										?>
									</td>
									
								</tr>
							
								<tr>
									<td>Sample</td>
									<td>
										<?
												echo create_drop_down('cbo_to_sample_name',132,"select id, SAMPLE_NAME from LIB_SAMPLE","id,SAMPLE_NAME",1,'Display','','',1);
										?>
									</td>
								</tr>
							</table>
					</fieldset>
					</td>
				</tr>
			</table>
		</div>
		<?
	}
	exit;
}

if($action=="color_and_size_level")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	$styleOrOrderWisw = $dataArr[3];
	$country_id = $dataArr[4];
	$company_id = $dataArr[5];

	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$productionVariable= return_field_value("is_control","variable_settings_production","company_name=$company_id and variable_list=33 and page_category_id=32 ");

	//#############################################################################################//

	$exfactData = sql_select("SELECT sum(d.EX_FACT_QTY) as ex_fact_qty, d.ACTUAL_PO_DTLS_ID as color_size_break_down_id from pro_ex_factory_mst c, PRO_EX_FACTORY_ACTUAL_PO_DETAILS d where c.id=d.mst_id and d.ACTUAL_PO_ID=$po_id and c.item_number_id=$item_id and c.country_id=$country_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by d.ACTUAL_PO_DTLS_ID");
	$color_size_exfactData_array=array();
	foreach($exfactData as $row)
	{
		$color_size_exfactData_array[$row[csf('color_size_break_down_id')]]['exfact'] += $row[csf('ex_fact_qty')];
	}

	// order wise - color level, color and size level
	//$variableSettings=2;
	if( $variableSettings==2 ) // color level
	{
		$sql = "SELECT a.item_number_id, a.color_number_id,
		sum(a.order_quantity) as order_quantity,
		sum(a.plan_cut_qnty) as plan_cut_qnty,
		sum(CASE WHEN c.production_type=8 then b.production_qnty ELSE 0 END) as production_qnty,
		sum(CASE WHEN c.production_type=10 and b.trans_type=5 then b.production_qnty ELSE 0 END) as trans_in_qty,
		sum(CASE WHEN c.production_type=10 and b.trans_type=6 then b.production_qnty ELSE 0 END) as trans_out_qty,
		sum(CASE WHEN c.production_type=14 then b.production_qnty ELSE 0 END) as del_qty,
		sum(CASE WHEN c.production_type=84 then b.production_qnty ELSE 0 END) as rcv_rtn_qty
		from wo_po_color_size_breakdown a
		left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.is_deleted=0 and b.status_active=1
		left join pro_garments_production_mst c on c.id=b.mst_id and c.is_deleted=0 and c.status_active=1
		where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 group by a.item_number_id, a.color_number_id";

	}
	else if( $variableSettings==3 ) //color and size level
	{

		$dtlsData = sql_select("SELECT a.ACTUAL_PO_DTLS_ID as color_size_break_down_id,
		sum(CASE WHEN b.production_type=8 then a.prod_qty ELSE 0 END) as prod_qty,
		sum(CASE WHEN b.production_type=10 and b.trans_type=5 then a.prod_qty ELSE 0 END) as trans_in_qty,
		sum(CASE WHEN b.production_type=10 and b.trans_type=6 then a.prod_qty ELSE 0 END) as trans_out_qty,
		sum(CASE WHEN b.production_type=14 then a.prod_qty ELSE 0 END) as del_qty,
		sum(CASE WHEN b.production_type=84 then a.prod_qty ELSE 0 END) as rcv_rtn_qty
		from WO_PO_ACC_PO_INFO_DTLS c, PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS a,pro_garments_production_mst b where c.id=a.ACTUAL_PO_DTLS_ID and a.mst_id=b.id and a.ACTUAL_PO_ID='$po_id' and c.gmts_item='$item_id' and c.country_id='$country_id' and a.ACTUAL_PO_DTLS_ID!=0 and a.production_type in(8,10,14,84) and a.status_active=1 and c.status_active=1 and b.status_active=1 and b.IS_RCV_ACKN=1 group by a.ACTUAL_PO_DTLS_ID");

		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('prod_qty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['in']= $row[csf('trans_in_qty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['out']= $row[csf('trans_out_qty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['del_qty']= $row[csf('del_qty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn_qty']= $row[csf('rcv_rtn_qty')];
		}

		$sql = "SELECT id, gmts_item as item_number_id, gmts_size_id as size_number_id, gmts_color_id as color_number_id, po_qty as order_quantity, po_qty as plan_cut_qnty
			from WO_PO_ACC_PO_INFO_DTLS
			where mst_id='$po_id' and gmts_item='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0 order by gmts_color_id, id";


	}
	else // by default color and size level
	{

		$dtlsData = sql_select("SELECT a.ACTUAL_PO_DTLS_ID as color_size_break_down_id,
		sum(CASE WHEN b.production_type=8 then a.prod_qty ELSE 0 END) as prod_qty,
		sum(CASE WHEN b.production_type=10 and b.trans_type=5 then a.prod_qty ELSE 0 END) as trans_in_qty,
		sum(CASE WHEN b.production_type=10 and b.trans_type=6 then a.prod_qty ELSE 0 END) as trans_out_qty,
		sum(CASE WHEN b.production_type=14 then a.prod_qty ELSE 0 END) as del_qty,
		sum(CASE WHEN b.production_type=84 then a.prod_qty ELSE 0 END) as rcv_rtn_qty
		from WO_PO_ACC_PO_INFO_DTLS c, PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS a,pro_garments_production_mst b where c.id=a.ACTUAL_PO_DTLS_ID and a.mst_id=b.id and a.ACTUAL_PO_ID='$po_id' and c.gmts_item='$item_id' and c.country_id='$country_id' and a.ACTUAL_PO_DTLS_ID!=0 and a.production_type in(8,10,14,84) and a.status_active=1 and c.status_active=1 and b.status_active=1 and b.IS_RCV_ACKN=1 group by a.ACTUAL_PO_DTLS_ID");

		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('prod_qty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['in']= $row[csf('trans_in_qty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['out']= $row[csf('trans_out_qty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['del_qty']= $row[csf('del_qty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn_qty']= $row[csf('rcv_rtn_qty')];
		}

		$sql = "SELECT id, gmts_item as item_number_id, gmts_size_id as size_number_id, gmts_color_id as color_number_id, po_qty as order_quantity, po_qty as plan_cut_qnty
			from WO_PO_ACC_PO_INFO_DTLS
			where mst_id='$po_id' and gmts_item='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0 order by gmts_color_id, id";

	}

	// echo $sql;die;

	$colorResult = sql_select($sql);
	//print_r($sql);
	$colorHTML="";
	$colorID='';
	$chkColor = array();
	$i=0;$totalQnty=0;
	foreach($colorResult as $color)
	{
		if( $variableSettings==2 ) // color level
		{
			//echo "shajjad_".$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]];

			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]+$color[csf('trans_in_qty')]-$color[csf("trans_out_qty")]-$color[csf("del_qty")]+$color[csf("rcv_rtn_qty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';
			$totalQnty += $color[csf("production_qnty")]+$color[csf("trans_in_qty")]-$color[csf("trans_out_qty")]-$color[csf("del_qty")]+$color[csf("rcv_rtn_qty")];
			$colorID .= $color[csf("color_number_id")].",";
		}
		else //color and size level
		{
			if( !in_array( $color[csf("color_number_id")], $chkColor ) )
			{
				if( $i!=0 ) $colorHTML .= "</table></div>";
				$i=0;
				$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
				$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
				$chkColor[] = $color[csf("color_number_id")];
			}
			//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
			$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")]."*".$color[csf("id")].",";

			$pro_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
			$trans_in_qty=$color_size_qnty_array[$color[csf('id')]]['in'];
			$trans_out_qty=$color_size_qnty_array[$color[csf('id')]]['out'];
			$rcv_rtn_qty=$color_size_qnty_array[$color[csf('id')]]['rcv_rtn_qty'];
			/* if($productionVariable==1)
			{
				$del_qty=$color_size_qnty_array[$color[csf('id')]]['del_qty'];
			}
			else
			{
				$del_qty=$color_size_exfactData_array[$color[csf('id')]]['exfact']*1;
			} */
			$del_qty=$color_size_exfactData_array[$color[csf('id')]]['exfact']*1+$color_size_qnty_array[$color[csf('id')]]['del_qty'];

			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" title="'.($pro_qnty."_".$trans_in_qty."_".$trans_out_qty."_".$del_qty."_".$rcv_rtn_qty).'" placeholder="'.(($pro_qnty+$trans_in_qty+$rcv_rtn_qty)-($trans_out_qty+$del_qty)).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"></td></tr>';
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

if($action=="color_and_size_level_sample")
{
	$dataArr = explode("**",$data);
	$req_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	$styleOrOrderWisw = $dataArr[3];
	// $country_id = $dataArr[4];

	$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
	//#############################################################################################//
	// order wise - color level, color and size level
	//$variableSettings=2;
	if( $variableSettings==2 ) // color level
	{
		$sql = "SELECT a.id, a.gmts_item_id as item_number_id, a.sample_color as color_number_id, a.sample_prod_qty as order_quantity
		from SAMPLE_DEVELOPMENT_DTLS a
		where a.sample_MST_ID='$req_id' and a.gmts_item_id='$item_id' and b.status_active=1 and b.is_deleted=0 order by a.sample_color, a.id";

		$dtlsData = sql_select("SELECT c.COLOR_ID,c.size_id, sum(case when b.entry_form_id=130 then c.SIZE_PASS_QTY end) as finish_qnty from SAMPLE_SEWING_OUTPUT_MST a, SAMPLE_SEWING_OUTPUT_DTLS b,SAMPLE_SEWING_OUTPUT_COLORSIZE c where a.id=b.SAMPLE_SEWING_OUTPUT_MST_ID and a.id=c.SAMPLE_SEWING_OUTPUT_MST_ID and b.id=c.SAMPLE_SEWING_OUTPUT_DTLS_ID and a.SAMPLE_DEVELOPMENT_ID=$req_id and b.item_number_id='$item_id' and b.status_active=1 and b.is_deleted=0 group by c.COLOR_ID,c.size_id");

		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row['COLOR_ID']] += $row[csf('finish_qnty')];
		}

		//===================== getting exfact qty ====================
		$exfactData = sql_select("SELECT b.COLOR_ID, sum(a.ex_factory_qty) as ex_fact_qty from SAMPLE_EX_FACTORY_DTLS a, SAMPLE_EX_FACTORY_COLORSIZE b  where a.id=b.SAMPLE_EX_FACTORY_DTLS_ID and a.SAMPLE_DEVELOPMENT_ID=$req_id and a.gmts_item_id=$item_id and a.entry_form_id=132 and b.status_active=1 and b.is_deleted=0 group by b.COLOR_ID");
		foreach($exfactData as $row)
		{
			$color_size_ex_qnty_array[$row['COLOR_ID']] += $row[csf('ex_fact_qty')];
		}
		// ============ transfer data ============
		$sql_trns = sql_select("SELECT
		sum(CASE WHEN a.production_type=10 and a.trans_type=5 then a.production_qnty ELSE 0 END) as trans_in_qty,
		sum(CASE WHEN a.production_type=10 and a.trans_type=6 then a.production_qnty ELSE 0 END) as trans_out_qty, a.color_id from  pro_garments_production_dtls a, pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$req_id' and b.item_number_id='$item_id'  and a.status_active=1 and a.is_deleted=0 and a.production_type=10 group by a.color_id");
		foreach($sql_trns as $row)
		{
			$color_size_trns_qnty_array[$row['COLOR_ID']]['trans_in_qty'] += $row[csf('trans_in_qty')];
			$color_size_trns_qnty_array[$row['COLOR_ID']]['trans_out_qty'] += $row[csf('trans_out_qty')];
		}
	}
	else if( $variableSettings==3 ) //color and size level
	{
		$dtlsData = sql_select("SELECT c.COLOR_ID,c.size_id, sum(case when b.entry_form_id=130 then c.SIZE_PASS_QTY end) as finish_qnty from SAMPLE_SEWING_OUTPUT_MST a, SAMPLE_SEWING_OUTPUT_DTLS b,SAMPLE_SEWING_OUTPUT_COLORSIZE c where a.id=b.SAMPLE_SEWING_OUTPUT_MST_ID and a.id=c.SAMPLE_SEWING_OUTPUT_MST_ID and b.id=c.SAMPLE_SEWING_OUTPUT_DTLS_ID and a.SAMPLE_DEVELOPMENT_ID=$req_id and b.item_number_id='$item_id' and b.status_active=1 and b.is_deleted=0 group by c.COLOR_ID,c.size_id");

		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row['COLOR_ID']][$row['SIZE_ID']] = $row[csf('finish_qnty')];
		}

		//===================== getting exfact qty ====================
		$exfactData = sql_select("SELECT b.COLOR_ID,b.size_id, sum(a.ex_factory_qty) as ex_fact_qty from SAMPLE_EX_FACTORY_DTLS a, SAMPLE_EX_FACTORY_COLORSIZE b  where a.id=b.SAMPLE_EX_FACTORY_DTLS_ID and a.SAMPLE_DEVELOPMENT_ID=$req_id and a.gmts_item_id=$item_id and a.entry_form_id=132 and b.status_active=1 and b.is_deleted=0 group by b.COLOR_ID,b.size_id");
		foreach($exfactData as $row)
		{
			$color_size_ex_qnty_array[$row['COLOR_ID']][$row['SIZE_ID']] += $row[csf('ex_fact_qty')];
		}
		// ============ transfer data ============
		$sql_trns = sql_select("SELECT
		sum(CASE WHEN a.production_type=10 and a.trans_type=5 then a.production_qnty ELSE 0 END) as trans_in_qty,
		sum(CASE WHEN a.production_type=10 and a.trans_type=6 then a.production_qnty ELSE 0 END) as trans_out_qty, a.color_id,a.size_id from  pro_garments_production_dtls a, pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$req_id' and b.item_number_id='$item_id'  and a.status_active=1 and a.is_deleted=0 and a.production_type=10 group by a.color_id");
		foreach($sql_trns as $row)
		{
			$color_size_trns_qnty_array[$row['COLOR_ID']][$row['SIZE_ID']]['trans_in_qty'] += $row[csf('trans_in_qty')];
			$color_size_trns_qnty_array[$row['COLOR_ID']][$row['SIZE_ID']]['trans_out_qty'] += $row[csf('trans_out_qty')];
		}

		$sql = "SELECT b.id, a.gmts_item_id as item_number_id, b.SIZE_ID as size_number_id, a.sample_color as color_number_id, b.total_qty as order_quantity
		from SAMPLE_DEVELOPMENT_DTLS a,SAMPLE_DEVELOPMENT_SIZE b
		where a.id=b.dtls_id and b.MST_ID='$req_id' and a.gmts_item_id='$item_id' and b.status_active=1 and b.is_deleted=0 order by a.sample_color, b.id";


	}
	else // by default color and size level
	{
		$dtlsData = sql_select("SELECT c.COLOR_ID,c.size_id, sum(case when b.entry_form_id=130 then c.SIZE_PASS_QTY end) as finish_qnty from SAMPLE_SEWING_OUTPUT_MST a, SAMPLE_SEWING_OUTPUT_DTLS b,SAMPLE_SEWING_OUTPUT_COLORSIZE c where a.id=b.SAMPLE_SEWING_OUTPUT_MST_ID and a.id=c.SAMPLE_SEWING_OUTPUT_MST_ID and b.id=c.SAMPLE_SEWING_OUTPUT_DTLS_ID and a.SAMPLE_DEVELOPMENT_ID=$req_id and b.item_number_id='$item_id' and b.status_active=1 and b.is_deleted=0 group by c.COLOR_ID,c.size_id");

		foreach($dtlsData as $row)
		{
			$color_size_qnty_array[$row['COLOR_ID']][$row['SIZE_ID']] = $row[csf('finish_qnty')];
		}

		//===================== getting exfact qty ====================
		$exfactData = sql_select("SELECT b.COLOR_ID,b.size_id, sum(a.ex_factory_qty) as ex_fact_qty from SAMPLE_EX_FACTORY_DTLS a, SAMPLE_EX_FACTORY_COLORSIZE b  where a.id=b.SAMPLE_EX_FACTORY_DTLS_ID and a.SAMPLE_DEVELOPMENT_ID=$req_id and a.gmts_item_id=$item_id and a.entry_form_id=132 and b.status_active=1 and b.is_deleted=0 group by b.COLOR_ID,b.size_id");
		foreach($exfactData as $row)
		{
			$color_size_ex_qnty_array[$row['COLOR_ID']][$row['SIZE_ID']] += $row[csf('ex_fact_qty')];
		}
		// ============ transfer data ============
		$sql_trns = sql_select("SELECT
		sum(CASE WHEN a.production_type=10 and a.trans_type=5 then a.production_qnty ELSE 0 END) as trans_in_qty,
		sum(CASE WHEN a.production_type=10 and a.trans_type=6 then a.production_qnty ELSE 0 END) as trans_out_qty, a.color_id,a.size_id from  pro_garments_production_dtls a, pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$req_id' and b.item_number_id='$item_id'  and a.status_active=1 and a.is_deleted=0 and a.production_type=10 group by a.color_id");
		foreach($sql_trns as $row)
		{
			$color_size_trns_qnty_array[$row['COLOR_ID']][$row['SIZE_ID']]['trans_in_qty'] += $row[csf('trans_in_qty')];
			$color_size_trns_qnty_array[$row['COLOR_ID']][$row['SIZE_ID']]['trans_out_qty'] += $row[csf('trans_out_qty')];
		}

		$sql = "SELECT b.id, a.gmts_item_id as item_number_id, b.SIZE_ID as size_number_id, a.sample_color as color_number_id, b.total_qty as order_quantity
		from SAMPLE_DEVELOPMENT_DTLS a,SAMPLE_DEVELOPMENT_SIZE b
		where a.id=b.dtls_id and b.MST_ID='$req_id' and a.gmts_item_id='$item_id' and b.status_active=1 and b.is_deleted=0 order by a.sample_color, b.id";

	}

	// echo $sql;die;

	$colorResult = sql_select($sql);
	//print_r($sql);
	$colorHTML="";
	$colorID='';
	$chkColor = array();
	$i=0;$totalQnty=0;
	foreach($colorResult as $color)
	{
		if( $variableSettings==2 ) // color level
		{
			//echo "shajjad_".$ex_fac_value[$color[csf('item_number_id')]][$color[csf('color_number_id')]];

			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color_size_qnty_array[$color[csf("color_number_id")]]-$color_size_ex_qnty_array[$color[csf("color_number_id")]]-$color_size_trns_qnty_array[$color[csf("color_number_id")]]).'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';
			$totalQnty += $color_size_qnty_array[$color[csf("color_number_id")]]-$color_size_ex_qnty_array[$color[csf("color_number_id")]]-$color_size_trns_qnty_array[$color[csf("color_number_id")]];
			$colorID .= $color[csf("color_number_id")].",";
		}
		else //color and size level
		{
			if( !in_array( $color[csf("color_number_id")], $chkColor ) )
			{
				if( $i!=0 ) $colorHTML .= "</table></div>";
				$i=0;
				$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
				$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
				$chkColor[] = $color[csf("color_number_id")];
			}
			//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
			$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

			$pro_qnty=$color_size_qnty_array[$color[csf("color_number_id")]][$color[csf("size_number_id")]];
			// $trans_in_qty=$color_size_qnty_array[$color[csf('id')]]['in'];
			// $trans_out_qty=$color_size_qnty_array[$color[csf('id')]]['out'];
			// $del_qty=$color_size_qnty_array[$color[csf('id')]]['del_qty'];
			// $rcv_rtn_qty=$color_size_qnty_array[$color[csf('id')]]['rcv_rtn_qty'];

			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($color_size_qnty_array[$color[csf("color_number_id")]][$color[csf("size_number_id")]]-$color_size_ex_qnty_array[$color[csf("color_number_id")]][$color[csf("size_number_id")]]-$color_size_trns_qnty_array[$color[csf("color_number_id")]][$color[csf("size_number_id")]]).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"></td></tr>';
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

if($action=="populate_transfer_form_data")
{
	$sql_dtls ="SELECT a.id,a.from_po_id,a.to_po_id, a.entry_break_down_type, a.item_number_id, a.country_id, a.production_quantity, a.remarks,b.transfer_criteria,b.company_id from pro_gmts_delivery_dtls a,pro_gmts_delivery_mst b where b.id=a.mst_id and a.id='$data'";

	$sql_dtls_from_to="SELECT b.po_break_down_id,b.country_id,b.item_number_id,b.trans_type,b.is_po_ok from pro_gmts_delivery_dtls a,pro_garments_production_mst b where a.id=b.dtls_id and a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=10 and b.production_type=10";
	$dtls_from_to_array=array();
  	foreach(sql_select($sql_dtls_from_to) as $keys=>$value)
  	{
  		$dtls_from_to_array[$value[csf("trans_type")]]["item"]=$value[csf("item_number_id")];
  		$dtls_from_to_array[$value[csf("trans_type")]]["country"]=$value[csf("country_id")];
  		$dtls_from_to_array[$value[csf("trans_type")]]["is_po_ok"]=$value[csf("is_po_ok")];
  		$order_id_array[$value[csf("po_break_down_id")]] = $value[csf("po_break_down_id")];

  	}
	$sqlResult =sql_select($sql_dtls);
	$company_id = $sqlResult[0]["COMPANY_ID"];
	$from_po_id = $sqlResult[0]["FROM_PO_ID"];
	$item_id = $sqlResult[0]["ITEM_NUMBER_ID"];
	$country_id = $sqlResult[0]["COUNTRY_ID"];
	$productionVariable= return_field_value("is_control","variable_settings_production","company_name=$company_id and variable_list=33 and page_category_id=32 ");
	$productionVariable = 3;
	// echo $productionVariable;die;

	$dtlsData = sql_select("SELECT
	sum(CASE WHEN b.production_type=8 then a.prod_qty ELSE 0 END) as finish_qnty,
	sum(CASE WHEN b.production_type=10 and b.trans_type=5 then a.prod_qty ELSE 0 END) as trans_in_qty,
	sum(CASE WHEN b.production_type=10 and b.trans_type=6 then a.prod_qty ELSE 0 END) as trans_out_qty,
	sum(CASE WHEN b.production_type=14 then a.prod_qty ELSE 0 END) as del_qty,
	sum(CASE WHEN b.production_type=84 then a.prod_qty ELSE 0 END) as rcv_rtn_qty
	from WO_PO_ACC_PO_INFO_DTLS c,PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS a,pro_garments_production_mst b where  c.id = a.ACTUAL_PO_DTLS_ID and a.mst_id=b.id and b.production_type in (8,10,14,84) and a.actual_po_id='$from_po_id' and c.gmts_item='$item_id' and c.country_id='$country_id' and a.ACTUAL_PO_DTLS_ID !=0 and b.is_rcv_ackn=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 ");

	// echo $dtlsData;die;
	$finish_qty=$totalDel=$total_transferin_qnty =$total_transferout_qnty =$rcv_rtn_qty=0;
	if($productionVariable==1)
	{
		//$finish_qty=$dtlsData[0][csf('finish_qnty')]+$dtlsData[0][csf('rcv_rtn_qty')] - $dtlsData[0][csf('del_qty')]+$dtlsData[0][csf('trans_in_qty')];
		$finish_qty=$dtlsData[0][csf('finish_qnty')];
		$totalDel=$dtlsData[0][csf('del_qty')];
		$total_transferin_qnty = $dtlsData[0][csf('trans_in_qty')];
		$total_transferout_qnty = $dtlsData[0][csf('trans_out_qty')];
		$rcv_rtn_qty = $dtlsData[0][csf('rcv_rtn_qty')];
		if($total_transferout_qnty=="") $total_transferout_qnty=0;
		if($finish_qty=="") $finish_qty=0;
	}
	else if($productionVariable==2 || $productionVariable==3 )
	{
		$finish_qty=$dtlsData[0][csf('finish_qnty')];
		$totalDel=$dtlsData[0][csf('del_qty')];
		$total_transferin_qnty = $dtlsData[0][csf('trans_in_qty')];
		$total_transferout_qnty = $dtlsData[0][csf('trans_out_qty')];
		$rcv_rtn_qty = $dtlsData[0][csf('rcv_rtn_qty')];
		if($total_transferout_qnty=="") $total_transferout_qnty=0;
		if($finish_qty=="") $finish_qty=0;
	}
	else
	{
		$exfactData = sql_select("SELECT sum(d.EX_FACT_QTY) as ex_fact_qty from pro_ex_factory_mst c, PRO_EX_FACTORY_ACTUAL_PO_DETAILS d, WO_PO_ACC_PO_INFO_DTLS e where c.id=d.mst_id and d.ACTUAL_PO_DTLS_ID=e.id and d.ACTUAL_PO_ID=$from_po_id and e.gmts_item=$item_id and e.country_id=$country_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0");
		//$finish_qty=$dtlsData[0][csf('finish_qnty')]+$dtlsData[0][csf('rcv_rtn_qty')] - $exfactData[0][csf('ex_fact_qty')]+$dtlsData[0][csf('trans_in_qty')];
		$finish_qty=$dtlsData[0][csf('finish_qnty')];
		if($finish_qty=="") $finish_qty=0;
	}

	// $total_transfer_qnty = $productionData[0][csf('finish_transfer_qnty')];

	//$yet_to_produced = $finish_qty-$total_transfer_qnty;

	// echo $total_transferout_qnty;die;
	$orderIds = implode(",",$order_id_array);
	foreach($sqlResult as $result)
	{
		// echo $result['TRANSFER_CRITERIA'];die;
		if($result['TRANSFER_CRITERIA']==1) // order to order
		{
			$poIds=$result[csf("from_po_id")].",".$result[csf("to_po_id")];
			$poQtyArr=return_library_array( "SELECT mst_id as po_break_down_id, sum(po_qty) as qnty, SUM (po_qty) AS plan_qnty from WO_PO_ACC_PO_INFO_DTLS where   mst_id in($poIds) and gmts_item='".$result[csf("item_number_id")]."' and country_id='".$result[csf("country_id")]."' and status_active=1 and is_deleted=0 group by mst_id","po_break_down_id","qnty");
			$poArr=array();
			$poData = sql_select("SELECT a.buyer_name, a.job_no, a.style_ref_no, b.id as order_id,b.grouping,c.id, c.ACC_SHIP_DATE as pub_shipment_date, c.ACC_PO_NO as po_number from wo_po_details_master a, wo_po_break_down b, WO_PO_ACC_PO_INFO c where a.id = b.job_id and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.is_deleted=0 and c.id in($poIds)");
			foreach($poData as $row)
			{
				$poArr[$row[csf('id')]][1]=$row[csf('buyer_name')];
				$poArr[$row[csf('id')]][2]=$row[csf('job_no')];
				$poArr[$row[csf('id')]][3]=$row[csf('style_ref_no')];
				$poArr[$row[csf('id')]][4]=$row[csf('po_number')];
				$poArr[$row[csf('id')]][5]=$row[csf('pub_shipment_date')];
				$poArr[$row[csf('id')]][6]=$row[csf('grouping')];
				$poArr[$row[csf('id')]][7]=$row[csf('order_id')];
			}
		}
		else
		{
			$poIds=$result[csf("from_po_id")];
			$reqIds=$result[csf("to_po_id")];
			$poQtyArr=return_library_array( "SELECT po_break_down_id, sum(order_quantity) as qnty, SUM (plan_cut_qnty) AS plan_qnty  from WO_PO_ACC_PO_INFO_DTLS where   po_break_down_id in($poIds) and item_number_id='".$result[csf("item_number_id")]."' and country_id='".$result[csf("country_id")]."' and status_active=1 and is_deleted=0 group by po_break_down_id","po_break_down_id","qnty");
			$poArr=array();
			$poData = sql_select("SELECT a.buyer_name, a.job_no, a.style_ref_no, b.id, b.pub_shipment_date, b.po_number from wo_po_details_master a, wo_po_break_down b where a.id = b.job_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.id in($poIds)");
			foreach($poData as $row)
			{
				$poArr[$row[csf('id')]][1]=$row[csf('buyer_name')];
				$poArr[$row[csf('id')]][2]=$row[csf('job_no')];
				$poArr[$row[csf('id')]][3]=$row[csf('style_ref_no')];
				$poArr[$row[csf('id')]][4]=$row[csf('po_number')];
				$poArr[$row[csf('id')]][5]=$row[csf('pub_shipment_date')];
			}
			// ====================== req data ================
			$reqQtyArr=return_library_array( "SELECT a.sample_mst_id as po_break_down_id, sum(b.TOTAL_QTY) as qnty from SAMPLE_DEVELOPMENT_DTLS a,SAMPLE_DEVELOPMENT_SIZE b where a.id=b.DTLS_ID and a.sample_mst_id in($reqIds) and a.gmts_item_id='".$result[csf("item_number_id")]."' and b.status_active=1 and b.is_deleted=0 group by sample_mst_id","po_break_down_id","qnty");
			$reqArr=array();
			$sql = "SELECT a.id, a.buyer_name,a.requisition_number,a.style_ref_no,a.ESTIMATED_SHIPDATE,b.color_id,b.gmts_item_id,b.sample_prod_qty,b.sample_name  from SAMPLE_DEVELOPMENT_MST a, SAMPLE_DEVELOPMENT_DTLS b where a.id = b.sample_mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id in($reqIds)";
			// echo $sql;die;
			$poData = sql_select($sql);
			foreach($poData as $row)
			{
				$reqArr[$row[csf('id')]][1]=$row[csf('buyer_name')];
				$reqArr[$row[csf('id')]][2]=$row[csf('requisition_number')];
				$reqArr[$row[csf('id')]][3]=$row[csf('style_ref_no')];
				$reqArr[$row[csf('id')]][4]=$row[csf('gmts_item_id')];
				$reqArr[$row[csf('id')]][5]=$row[csf('ESTIMATED_SHIPDATE')];
				$reqArr[$row[csf('id')]][6]+=$row[csf('sample_prod_qty')];
				$reqArr[$row[csf('id')]][7]=$row[csf('sample_name')];
			}
			//echo "<pre>";print_r($reqArr);die;
		}

		//$mst_id=return_field_value("id","pro_garments_production_mst","dtls_id=$data and production_type=10 and trans_type=6");
		$update_dtls_issue_id=''; $update_dtls_recv_id='';
		$mstData=sql_select("SELECT id, trans_type from pro_garments_production_mst where dtls_id=$data and production_type=10 and status_active=1 and is_deleted=0");
		foreach($mstData as $row)
		{
			if($row[csf('trans_type')]==6)
			{
				$update_dtls_issue_id=$row[csf('id')];
			}
			else
			{
				$update_dtls_recv_id=$row[csf('id')];
			}
		}
		echo "document.getElementById('cbo_transfer_criteria').value 			= '".$result["TRANSFER_CRITERIA"]."';\n";
		echo "document.getElementById('txt_from_order_id').value 			= '".$result[csf("from_po_id")]."';\n";
		echo "document.getElementById('txt_from_order_no').value 			= '".$poArr[$result[csf("from_po_id")]][4]."';\n";
		echo "document.getElementById('txt_from_po_qnty').value 			= '".$poQtyArr[$result[csf("from_po_id")]]."';\n";
		echo "document.getElementById('cbo_from_buyer_name').value 			= '".$poArr[$result[csf("from_po_id")]][1]."';\n";
		echo "document.getElementById('txt_from_style_ref').value 			= '".$poArr[$result[csf("from_po_id")]][3]."';\n";
		echo "document.getElementById('txt_from_job_no').value 				= '".$poArr[$result[csf("from_po_id")]][2]."';\n";
		echo "document.getElementById('txt_from_ref_no').value 				= '".$poArr[$result[csf("from_po_id")]][6]."';\n";
		echo "document.getElementById('txt_from_hid_po_id').value 			= '".$poArr[$result[csf("from_po_id")]][7]."';\n";
		echo "document.getElementById('cbo_from_gmts_item').value 			= '".$dtls_from_to_array[6]["item"]."';\n";
		echo "document.getElementById('cbo_from_country_name').value 		= '".$dtls_from_to_array[6]["country"]."';\n";
		echo "document.getElementById('txt_from_shipment_date').value 		= '".change_date_format($poArr[$result[csf("from_po_id")]]
		[5])."';\n";
		echo "document.getElementById('txt_to_plan_cut_qty').value 			= '".$poQtyArr[$result[csf("from_po_id")]]."';\n";
		// echo "document.getElementById('cbo_from_sample_name').value 			= '".$reqArr[$result[csf("from_po_id")]][7]."';\n";

		if($result['TRANSFER_CRITERIA']==1)
		{
			echo "document.getElementById('txt_to_order_id').value 				= '".$result[csf("to_po_id")]."';\n";
			echo "document.getElementById('txt_to_order_no').value 				= '".$poArr[$result[csf("to_po_id")]][4]."';\n";
			echo "document.getElementById('txt_to_po_qnty').value 				= '".$poQtyArr[$result[csf("to_po_id")]]."';\n";
			echo "document.getElementById('cbo_to_buyer_name').value 			= '".$poArr[$result[csf("to_po_id")]][1]."';\n";
			echo "document.getElementById('txt_to_style_ref').value 			= '".$poArr[$result[csf("to_po_id")]][3]."';\n";
			echo "document.getElementById('txt_to_job_no').value 				= '".$poArr[$result[csf("to_po_id")]][2]."';\n";
			echo "document.getElementById('txt_to_ref_no').value 				= '".$poArr[$result[csf("to_po_id")]][6]."';\n";
			echo "document.getElementById('txt_to_hid_po_id').value 			= '".$poArr[$result[csf("to_po_id")]][7]."';\n";
			echo "document.getElementById('cbo_to_gmts_item').value 			= '".$dtls_from_to_array[6]["item"]."';\n";
			echo "document.getElementById('cbo_to_country_name').value 			= '".$dtls_from_to_array[5]["country"]."';\n";
			echo "document.getElementById('cbo_po_ok').value 					= '".$dtls_from_to_array[6]["is_po_ok"]."';\n";
			echo "document.getElementById('txt_to_shipment_date').value 		= '".change_date_format($poArr[$result[csf("to_po_id")]][5])."';\n";

			echo "document.getElementById('txt_to_plan_cut_qty').value 			= '".$poQtyArr[$result[csf("from_po_id")]]."';\n";
		}
		else
		{
			echo "document.getElementById('txt_to_order_id').value 				= '".$result[csf("to_po_id")]."';\n";
			echo "document.getElementById('txt_to_order_no').value 				= '".$reqArr[$result[csf("to_po_id")]][2]."';\n";
			echo "document.getElementById('txt_to_po_qnty').value 				= '".$reqArr[$result[csf("to_po_id")]][6]."';\n";
			echo "document.getElementById('cbo_to_buyer_name').value 			= '".$reqArr[$result[csf("to_po_id")]][1]."';\n";
			echo "document.getElementById('txt_to_style_ref').value 			= '".$reqArr[$result[csf("to_po_id")]][3]."';\n";
			// echo "document.getElementById('txt_to_job_no').value 				= '".$reqArr[$result[csf("to_po_id")]][2]."';\n";
			echo "document.getElementById('cbo_to_gmts_item').value 			= '".$dtls_from_to_array[6]["item"]."';\n";
			// echo "document.getElementById('cbo_to_country_name').value 			= '".$dtls_from_to_array[5]["country"]."';\n";
			echo "document.getElementById('txt_to_shipment_date').value 		= '".change_date_format($reqArr[$result[csf("to_po_id")]][5])."';\n";
			echo "document.getElementById('cbo_to_sample_name').value 			= '".$reqArr[$result[csf("to_po_id")]][7]."';\n";

		}

		echo "$('#txt_transfer_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#update_dtls_id').val('".$result[csf('id')]."');\n";
		echo "$('#update_dtls_issue_id').val('".$update_dtls_issue_id."');\n";
		echo "$('#update_dtls_recv_id').val('".$update_dtls_recv_id."');\n";
		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";

		/* $productionData = sql_select("SELECT
		sum(case when production_type=8 then production_quantity else 0 end) as finish_qnty,
		sum(case when production_type=10 then production_quantity else 0 end) as finish_transfer_qnty
		from pro_garments_production_mst
		where po_break_down_id=".$result[csf('from_po_id')]." and item_number_id='".$result[csf('item_number_id')]."' and country_id='".$result[csf('country_id')]."' and status_active=1 and is_deleted=0"); */

		$yet_to_produced = $finish_qty-$totalDel+($total_transferin_qnty-$total_transferout_qnty+$rcv_rtn_qty);
		$yettoTitel="Total Finish Qty:".$finish_qty."-Total Delivery Qty:".$totalDel."+( Trans In:".$total_transferin_qnty."- Trans Out:".$total_transferout_qnty."+ Rec. Return:".$rcv_rtn_qty.")";

		// $finish_qty=$productionData[0][csf('finish_qnty')];
		// if($finish_qty=="") $finish_qty=0;

		// $total_transfer_qnty = $productionData[0][csf('finish_transfer_qnty')]; ;
		// if($total_transfer_qnty=="") $total_transfer_qnty=0;

		echo "$('#txt_finish_quantity').val('".$finish_qty."');\n";
		echo "$('#txt_cumul_quantity').attr('placeholder','".$total_transferout_qnty."');\n";
		echo "$('#txt_cumul_quantity').val('".$total_transferout_qnty."');\n";
		// $yet_to_produced = $finish_qty-$total_transfer_qnty;
		echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
		// echo "$('#txt_transfer_qty').attr('placeholder','".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
		echo "$('#txt_yet_quantity').attr('title','".$yettoTitel."');\n";
		echo "$('#txt_transfer_qty').removeAttr('readonly')\n";
 		echo "set_button_status(1, permission, 'fnc_transfer_entry',1,1);\n";
		echo "$('#isUpdateMood').val('1');\n";

		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		if($variableSettings!=1 )
		{
			$po_id = $result[csf('from_po_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];

			$exfactData = sql_select("SELECT sum(d.EX_FACT_QTY) as ex_fact_qty, d.ACTUAL_PO_DTLS_ID as color_size_break_down_id from pro_ex_factory_mst c, PRO_EX_FACTORY_ACTUAL_PO_DETAILS d, WO_PO_ACC_PO_INFO_DTLS e where c.id=d.mst_id and d.ACTUAL_PO_DTLS_ID=e.id and d.ACTUAL_PO_ID=$po_id and e.gmts_item=$item_id and e.country_id=$country_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  group by d.ACTUAL_PO_DTLS_ID");
			$color_size_exfactData_array=array();
			foreach($exfactData as $row)
			{
				$color_size_exfactData_array[$row[csf('color_size_break_down_id')]]['exfact']= $row[csf('ex_fact_qty')];
			}

			$sql_dtls = sql_select("SELECT b.ACTUAL_PO_DTLS_ID as color_size_break_down_id, b.PROD_QTY, a.gmts_size_id as size_number_id, a.gmts_color_id as color_number_id from  WO_PO_ACC_PO_INFO_DTLS a, PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS b where b.mst_id=$update_dtls_issue_id and a.status_active=1 and a.id=b.ACTUAL_PO_DTLS_ID and b.ACTUAL_PO_ID='$po_id' and a.gmts_item='$item_id' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 ");
			foreach($sql_dtls as $row)
			{
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$row[csf('color_number_id')];
			  	$amountArr[$index] += $row[csf('PROD_QTY')];
			}

			if($variableSettings==2 ) // color level
			{
				if($db_type==0)
				{
					$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=8 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=10 and cur.is_deleted=0 ) as trans_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0  group by color_number_id";
				}
				else
				{
					$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
							sum(CASE WHEN c.production_type=8 then b.production_qnty ELSE 0 END) as production_qnty,
							sum(CASE WHEN c.production_type=10 and c.trans_type=5 then b.production_qnty ELSE 0 END) as trans_in_qty,
							sum(CASE WHEN c.production_type=10 and c.trans_type=6 then b.production_qnty ELSE 0 END) as trans_out_qty,
							sum(CASE WHEN c.production_type=14 then b.production_qnty ELSE 0 END) as del_qty,
							sum(CASE WHEN c.production_type=84 then b.production_qnty ELSE 0 END) as rcv_rtn_qty
							from wo_po_color_size_breakdown a
							left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.is_deleted=0 and b.status_active=1
							left join pro_garments_production_mst c on c.id=b.mst_id and c.is_deleted=0 and c.status_active=1
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0  group by a.item_number_id, a.color_number_id";
				}
			}
			else if( $variableSettings==3 ) //color and size level
			{
				$dtlsData = sql_select("SELECT a.ACTUAL_PO_DTLS_ID as color_size_break_down_id,
							sum(CASE WHEN a.production_type=8 then a.prod_qty ELSE 0 END) as prod_qty,
							sum(CASE WHEN a.production_type=10 and b.trans_type=5 then a.prod_qty ELSE 0 END) as trans_in_qty,
							sum(CASE WHEN a.production_type=10 and b.trans_type=6 then a.prod_qty ELSE 0 END) as trans_out_qty,
							sum(CASE WHEN a.production_type=14 then a.prod_qty ELSE 0 END) as del_qty,
							sum(CASE WHEN a.production_type=84 then a.prod_qty ELSE 0 END) as rcv_rtn_qty
							from WO_PO_ACC_PO_INFO_DTLS c,PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS a,pro_garments_production_mst b where c.id=a.ACTUAL_PO_DTLS_ID and a.mst_id=b.id and a.status_active=1 and  a.ACTUAL_PO_ID='$po_id' and c.gmts_item='$item_id' and c.country_id='$country_id' and a.ACTUAL_PO_DTLS_ID!=0 and a.production_type in(8,10,14,84) and b.is_rcv_ackn=1 group by a.ACTUAL_PO_DTLS_ID");


				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('prod_qty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['in']= $row[csf('trans_in_qty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['out']= $row[csf('trans_out_qty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['del_qty']= $row[csf('del_qty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn_qty']= $row[csf('rcv_rtn_qty')];
				}

				$sql = "SELECT id, gmts_item as item_number_id, gmts_size_id as size_number_id, gmts_color_id as color_number_id, po_qty as order_quantity, po_qty as plan_cut_qnty
					from WO_PO_ACC_PO_INFO_DTLS
					where mst_id='$po_id' and gmts_item='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0 order by gmts_color_id, id";
			}
			else // by default color and size level
			{

				$dtlsData = sql_select("SELECT a.ACTUAL_PO_DTLS_ID as color_size_break_down_id,
							sum(CASE WHEN a.production_type=8 then a.prod_qty ELSE 0 END) as prod_qty,
							sum(CASE WHEN a.production_type=10 and b.trans_type=5 then a.prod_qty ELSE 0 END) as trans_in_qty,
							sum(CASE WHEN a.production_type=10 and b.trans_type=6 then a.prod_qty ELSE 0 END) as trans_out_qty,
							sum(CASE WHEN a.production_type=14 then a.prod_qty ELSE 0 END) as del_qty,
							sum(CASE WHEN a.production_type=84 then a.prod_qty ELSE 0 END) as rcv_rtn_qty
							from WO_PO_ACC_PO_INFO_DTLS c,PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS a,pro_garments_production_mst b where c.id=a.ACTUAL_PO_DTLS_ID and a.mst_id=b.id and a.status_active=1 and  a.ACTUAL_PO_ID='$po_id' and c.gmts_item='$item_id' and c.country_id='$country_id' and a.ACTUAL_PO_DTLS_ID!=0 and a.production_type in(8,10,14,84) and b.is_rcv_ackn=1 group by a.ACTUAL_PO_DTLS_ID");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['in']= $row[csf('trans_in_qty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['out']= $row[csf('trans_out_qty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['del_qty']= $row[csf('del_qty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn_qty']= $row[csf('rcv_rtn_qty')];
				}

				$sql = "SELECT id, gmts_item as item_number_id, gmts_size_id as size_number_id, gmts_color_id as color_number_id, po_qty as order_quantity, po_qty as plan_cut_qnty
					from WO_PO_ACC_PO_INFO_DTLS
					where mst_id='$po_id' and gmts_item='$item_id' and country_id='$country_id' and status_active=1 and is_deleted=0 order by gmts_color_id, id";
			}
			// echo $sql ;die;
			$colorResult = sql_select($sql);
			//print_r($sql);
			$colorHTML=""; $colorID=''; $chkColor = array(); $i=0; $totalQnty=0;
			foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{
					$amount = $amountArr[$color[csf("color_number_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]+$color[csf("trans_in_qty")]-$color[csf("trans_out_qty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';
					//$totalQnty += $color[csf("production_qnty")]-$color[csf("trans_production_qnty")];
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
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
					//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")]."*".$color[csf("id")].",";

					$pro_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					
					$trans_in_qty=$color_size_qnty_array[$color[csf('id')]]['in'];
					$trans_out_qty=$color_size_qnty_array[$color[csf('id')]]['out'];
					$rcv_rtn_qty=$color_size_qnty_array[$color[csf('id')]]['rcv_rtn_qty'];
					if($productionVariable==1)
					{
						$del_qty=$color_size_qnty_array[$color[csf('id')]]['del_qty'];
					}
					else
					{
						$del_qty=$color_size_exfactData_array[$color[csf('id')]]['exfact']*1;
					}
					// echo $color[csf('id')]."==".$del_qty."<br>";
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($pro_qnty+$trans_in_qty+$rcv_rtn_qty-$del_qty-$trans_out_qty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'"></td></tr>';
					$colorWiseTotal += $amount;
				}
				$i++;
			}
			//echo $colorHTML;die;
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }

			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )
			{
				echo "$totalFn;\n";
			}

			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		}

		exit();
	}
}

if($action=="populate_sample_transfer_form_data")
{
	$sql_dtls ="SELECT a.id,a.from_po_id,a.to_po_id, a.entry_break_down_type, a.item_number_id, a.country_id, a.production_quantity, a.remarks,b.transfer_criteria from pro_gmts_delivery_dtls a,pro_gmts_delivery_mst b where b.id=a.mst_id and a.id='$data'";

	$sql_dtls_from_to="SELECT b.country_id,b.item_number_id,b.trans_type from pro_gmts_delivery_dtls a,pro_garments_production_mst b where a.id=b.dtls_id and a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type=10 and b.production_type=10";
	$dtls_from_to_array=array();
  	foreach(sql_select($sql_dtls_from_to) as $keys=>$value)
  	{
  		$dtls_from_to_array[$value[csf("trans_type")]]["item"]=$value[csf("item_number_id")];
  		$dtls_from_to_array[$value[csf("trans_type")]]["country"]=$value[csf("country_id")];

  	}
	$sqlResult =sql_select($sql_dtls);
	foreach($sqlResult as $result)
	{
		$reqIds=$result[csf("from_po_id")].",".$result[csf("to_po_id")];
		$reqQtyArr=return_library_array( "SELECT a.sample_mst_id as po_break_down_id, sum(b.TOTAL_QTY) as qnty from SAMPLE_DEVELOPMENT_DTLS a,SAMPLE_DEVELOPMENT_SIZE b where a.id=b.DTLS_ID and a.sample_mst_id in($reqIds) and a.gmts_item_id='".$result[csf("item_number_id")]."' and b.status_active=1 and b.is_deleted=0 group by sample_mst_id","po_break_down_id","qnty");
		$reqArr=array();
		$sql = "SELECT a.id, a.buyer_name,a.requisition_number,a.style_ref_no,a.ESTIMATED_SHIPDATE,b.color_id,b.gmts_item_id,b.sample_prod_qty  from SAMPLE_DEVELOPMENT_MST a, SAMPLE_DEVELOPMENT_DTLS b where a.id = b.sample_mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id in($reqIds)";
		$poData = sql_select($sql);
		foreach($poData as $row)
		{
			$reqArr[$row[csf('id')]][1]=$row[csf('buyer_name')];
			$reqArr[$row[csf('id')]][2]=$row[csf('requisition_number')];
			$reqArr[$row[csf('id')]][3]=$row[csf('style_ref_no')];
			$reqArr[$row[csf('id')]][4]=$row[csf('gmts_item_id')];
			$reqArr[$row[csf('id')]][5]=$row[csf('ESTIMATED_SHIPDATE')];
			$reqArr[$row[csf('id')]][6]+=$row[csf('sample_prod_qty')];
		}

		//$mst_id=return_field_value("id","pro_garments_production_mst","dtls_id=$data and production_type=10 and trans_type=6");
		$update_dtls_issue_id=''; $update_dtls_recv_id='';
		$mstData=sql_select("select id, trans_type from pro_garments_production_mst where dtls_id=$data and production_type=10 and status_active=1 and is_deleted=0");
		foreach($mstData as $row)
		{
			if($row[csf('trans_type')]==6)
			{
				$update_dtls_issue_id=$row[csf('id')];
			}
			else
			{
				$update_dtls_recv_id=$row[csf('id')];
			}
		}

		echo "document.getElementById('txt_from_order_id').value 				= '".$result[csf("from_po_id")]."';\n";
		echo "document.getElementById('txt_from_order_no').value 				= '".$reqArr[$result[csf("from_po_id")]][2]."';\n";
		echo "document.getElementById('txt_from_po_qnty').value 				= '".$reqArr[$result[csf("from_po_id")]][6]."';\n";
		echo "document.getElementById('cbo_from_buyer_name').value 			= '".$reqArr[$result[csf("from_po_id")]][1]."';\n";
		echo "document.getElementById('txt_from_style_ref').value 			= '".$reqArr[$result[csf("from_po_id")]][3]."';\n";
		// echo "document.getElementById('txt_from_job_no').value 				= '".$reqArr[$result[csf("from_po_id")]][2]."';\n";
		echo "document.getElementById('cbo_from_gmts_item').value 			= '".$dtls_from_to_array[6]["item"]."';\n";
		// echo "document.getElementById('cbo_from_country_name').value 			= '".$dtls_from_from_array[5]["country"]."';\n";
		echo "document.getElementById('txt_from_shipment_date').value 		= '".change_date_format($reqArr[$result[csf("from_po_id")]][5])."';\n";

		echo "document.getElementById('txt_to_order_id').value 				= '".$result[csf("to_po_id")]."';\n";
		echo "document.getElementById('txt_to_order_no').value 				= '".$reqArr[$result[csf("to_po_id")]][2]."';\n";
		echo "document.getElementById('txt_to_po_qnty').value 				= '".$reqArr[$result[csf("to_po_id")]][6]."';\n";
		echo "document.getElementById('cbo_to_buyer_name').value 			= '".$reqArr[$result[csf("to_po_id")]][1]."';\n";
		echo "document.getElementById('txt_to_style_ref').value 			= '".$reqArr[$result[csf("to_po_id")]][3]."';\n";
		// echo "document.getElementById('txt_to_job_no').value 				= '".$reqArr[$result[csf("to_po_id")]][2]."';\n";
		echo "document.getElementById('cbo_to_gmts_item').value 			= '".$dtls_from_to_array[6]["item"]."';\n";
		// echo "document.getElementById('cbo_to_country_name').value 			= '".$dtls_from_to_array[5]["country"]."';\n";
		echo "document.getElementById('txt_to_shipment_date').value 		= '".change_date_format($reqArr[$result[csf("to_po_id")]][5])."';\n";

		echo "$('#txt_transfer_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#update_dtls_id').val('".$result[csf('id')]."');\n";
		echo "$('#update_dtls_issue_id').val('".$update_dtls_issue_id."');\n";
		echo "$('#update_dtls_recv_id').val('".$update_dtls_recv_id."');\n";
		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		echo "$('#cbo_transfer_criteria').val('".$result[csf('transfer_criteria')]."');\n";

		$req_id = $result[csf('from_po_id')];
		$item_id = $result[csf('item_number_id')];
		$productionData = sql_select("SELECT sum(case when b.entry_form_id=130 then b.prod_qty end) as finish_qnty from SAMPLE_SEWING_OUTPUT_MST a, SAMPLE_SEWING_OUTPUT_DTLS b where a.id=b.SAMPLE_SEWING_OUTPUT_MST_ID and a.SAMPLE_DEVELOPMENT_ID=$req_id and b.item_number_id='$item_id' and b.status_active=1 and b.is_deleted=0");

		$finish_qty=$productionData[0][csf('finish_qnty')];
		if($finish_qty=="") $finish_qty=0;

		$total_transfer_qnty = $productionData[0][csf('finish_transfer_qnty')]; ;
		if($total_transfer_qnty=="") $total_transfer_qnty=0;

		echo "$('#txt_finish_quantity').val('".$finish_qty."');\n";
		echo "$('#txt_cumul_quantity').attr('placeholder','".$total_transfer_qnty."');\n";
		echo "$('#txt_cumul_quantity').val('".$total_transfer_qnty."');\n";
		$yet_to_produced = $finish_qty-$total_transfer_qnty;
		echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
		echo "$('#txt_transfer_qty').attr('placeholder','".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
		echo "$('#txt_transfer_qty').removeAttr('readonly')\n";
 		echo "set_button_status(1, permission, 'fnc_transfer_entry',1,1);\n";
		echo "$('#isUpdateMood').val('1');\n";

		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		if($variableSettings!=1 )
		{
			$req_id = $result[csf('from_po_id')];
			$item_id = $result[csf('item_number_id')];

			$sql_dtls = sql_select("SELECT a.production_qnty, a.size_id, a.color_id from  pro_garments_production_dtls a, pro_garments_production_mst b where a.mst_id=$update_dtls_issue_id and a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$req_id' and b.item_number_id='$item_id'  and a.status_active=1 and a.is_deleted=0 ");
			foreach($sql_dtls as $row)
			{
				if( $variableSettings==2 ) $index = $row[csf('color_id')]; else $index = $row[csf('size_id')].$row[csf('color_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
			}

			if($variableSettings==2 ) // color level
			{
				$sql = "SELECT a.id, a.gmts_item_id as item_number_id, a.sample_color as color_number_id, a.sample_prod_qty as order_quantity
				from SAMPLE_DEVELOPMENT_DTLS a
				where a.sample_MST_ID='$req_id' and a.gmts_item_id='$item_id' and b.status_active=1 and b.is_deleted=0 order by a.sample_color, a.id";

				$dtlsData = sql_select("SELECT c.COLOR_ID,c.size_id, sum(case when b.entry_form_id=130 then c.SIZE_PASS_QTY end) as finish_qnty from SAMPLE_SEWING_OUTPUT_MST a, SAMPLE_SEWING_OUTPUT_DTLS b,SAMPLE_SEWING_OUTPUT_COLORSIZE c where a.id=b.SAMPLE_SEWING_OUTPUT_MST_ID and a.id=c.SAMPLE_SEWING_OUTPUT_MST_ID and b.id=c.SAMPLE_SEWING_OUTPUT_DTLS_ID and a.SAMPLE_DEVELOPMENT_ID=$req_id and b.item_number_id='$item_id' and b.status_active=1 and b.is_deleted=0 group by c.COLOR_ID,c.size_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row['COLOR_ID']] += $row[csf('finish_qnty')];
				}

				//===================== getting exfact qty ====================
				$exfactData = sql_select("SELECT b.COLOR_ID, sum(a.ex_factory_qty) as ex_fact_qty from SAMPLE_EX_FACTORY_DTLS a, SAMPLE_EX_FACTORY_COLORSIZE b  where a.id=b.SAMPLE_EX_FACTORY_DTLS_ID and a.SAMPLE_DEVELOPMENT_ID=$req_id and a.gmts_item_id=$item_id and a.entry_form_id=132 and b.status_active=1 and b.is_deleted=0 group by b.COLOR_ID");
				foreach($exfactData as $row)
				{
					$color_size_ex_qnty_array[$row['COLOR_ID']] += $row[csf('ex_fact_qty')];
				}

			}
			else if( $variableSettings==3 ) //color and size level
			{
				$dtlsData = sql_select("SELECT c.COLOR_ID,c.size_id, sum(case when b.entry_form_id=130 then c.SIZE_PASS_QTY end) as finish_qnty from SAMPLE_SEWING_OUTPUT_MST a, SAMPLE_SEWING_OUTPUT_DTLS b,SAMPLE_SEWING_OUTPUT_COLORSIZE c where a.id=b.SAMPLE_SEWING_OUTPUT_MST_ID and a.id=c.SAMPLE_SEWING_OUTPUT_MST_ID and b.id=c.SAMPLE_SEWING_OUTPUT_DTLS_ID and a.SAMPLE_DEVELOPMENT_ID=$req_id and b.item_number_id='$item_id' and b.status_active=1 and b.is_deleted=0 group by c.COLOR_ID,c.size_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row['COLOR_ID']][$row['SIZE_ID']] = $row[csf('finish_qnty')];
				}

				//===================== getting exfact qty ====================
				$exfactData = sql_select("SELECT b.COLOR_ID,b.size_id, sum(a.ex_factory_qty) as ex_fact_qty from SAMPLE_EX_FACTORY_DTLS a, SAMPLE_EX_FACTORY_COLORSIZE b  where a.id=b.SAMPLE_EX_FACTORY_DTLS_ID and a.SAMPLE_DEVELOPMENT_ID=$req_id and a.gmts_item_id=$item_id and a.entry_form_id=132 and b.status_active=1 and b.is_deleted=0 group by b.COLOR_ID,b.size_id");
				foreach($exfactData as $row)
				{
					$color_size_ex_qnty_array[$row['COLOR_ID']][$row['SIZE_ID']] += $row[csf('ex_fact_qty')];
				}

				$sql = "SELECT b.id, a.gmts_item_id as item_number_id, b.SIZE_ID as size_number_id, a.sample_color as color_number_id, b.total_qty as order_quantity
				from SAMPLE_DEVELOPMENT_DTLS a,SAMPLE_DEVELOPMENT_SIZE b
				where a.id=b.dtls_id and b.MST_ID='$req_id' and a.gmts_item_id='$item_id' and b.status_active=1 and b.is_deleted=0 order by a.sample_color, b.id";
			}
			else // by default color and size level
			{
				$dtlsData = sql_select("SELECT c.COLOR_ID,c.size_id, sum(case when b.entry_form_id=130 then c.SIZE_PASS_QTY end) as finish_qnty from SAMPLE_SEWING_OUTPUT_MST a, SAMPLE_SEWING_OUTPUT_DTLS b,SAMPLE_SEWING_OUTPUT_COLORSIZE c where a.id=b.SAMPLE_SEWING_OUTPUT_MST_ID and a.id=c.SAMPLE_SEWING_OUTPUT_MST_ID and b.id=c.SAMPLE_SEWING_OUTPUT_DTLS_ID and a.SAMPLE_DEVELOPMENT_ID=$req_id and b.item_number_id='$item_id' and b.status_active=1 and b.is_deleted=0 group by c.COLOR_ID,c.size_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row['COLOR_ID']][$row['SIZE_ID']] = $row[csf('finish_qnty')];
				}

				//===================== getting exfact qty ====================
				$exfactData = sql_select("SELECT b.COLOR_ID,b.size_id, sum(a.ex_factory_qty) as ex_fact_qty from SAMPLE_EX_FACTORY_DTLS a, SAMPLE_EX_FACTORY_COLORSIZE b  where a.id=b.SAMPLE_EX_FACTORY_DTLS_ID and a.SAMPLE_DEVELOPMENT_ID=$req_id and a.gmts_item_id=$item_id and a.entry_form_id=132 and b.status_active=1 and b.is_deleted=0 group by b.COLOR_ID,b.size_id");
				foreach($exfactData as $row)
				{
					$color_size_ex_qnty_array[$row['COLOR_ID']][$row['SIZE_ID']] += $row[csf('ex_fact_qty')];
				}

				$sql = "SELECT b.id, a.gmts_item_id as item_number_id, b.SIZE_ID as size_number_id, a.sample_color as color_number_id, b.total_qty as order_quantity
				from SAMPLE_DEVELOPMENT_DTLS a,SAMPLE_DEVELOPMENT_SIZE b
				where a.id=b.dtls_id and b.MST_ID='$req_id' and a.gmts_item_id='$item_id' and b.status_active=1 and b.is_deleted=0 order by a.sample_color, b.id";
			}

			$colorResult = sql_select($sql);
			//print_r($sql);
			$colorHTML=""; $colorID=''; $chkColor = array(); $i=0; $totalQnty=0;
			foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{
					$amount = $amountArr[$color[csf("color_number_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:80px"  class="text_boxes_numeric" placeholder="'.($color_size_qnty_array[$color[csf("color_number_id")]]-$color_size_ex_qnty_array[$color[csf("color_number_id")]]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')"></td></tr>';
					//$totalQnty += $color[csf("production_qnty")]-$color[csf("trans_production_qnty")];
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
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
					//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

					$pro_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$trans_in_qty=$color_size_qnty_array[$color[csf('id')]]['in'];
					$trans_out_qty=$color_size_qnty_array[$color[csf('id')]]['out'];

					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" placeholder="'.($color_size_qnty_array[$color[csf("color_number_id")]][$color[csf("size_number_id")]]-$color_size_ex_qnty_array[$color[csf("color_number_id")]][$color[csf("size_number_id")]]+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'"></td></tr>';
					$colorWiseTotal += $amount;
				}
				$i++;
			}
			//echo $colorHTML;die;
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="100">Color</th><th width="80">Quantity</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:80px" ></th></tr></tfoot></table>'; }

			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )
			{
				echo "$colorHTML;\n";
			}

			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		}

		exit();
	}
}

if($action=="show_dtls_listview")
{
	$data_ex = explode("**",$data);
	$delivery_mst_id = $data_ex[0];
	$transfer_criteria = $data_ex[1];
	// echo $transfer_criteria;die;

	$po_arr=return_library_array( "SELECT c.id, c.ACC_PO_NO as po_number from pro_garments_production_mst a, PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS b,WO_PO_ACC_PO_INFO c where a.id=b.mst_id and b.actual_po_id=c.id and a.delivery_mst_id=$delivery_mst_id and a.production_type=10 group by c.id, c.ACC_PO_NO",'id','po_number');
	// echo "SELECT b.id, b.po_number from pro_garments_production_mst a, wo_po_break_down b where a.po_break_down_id=b.id and a.delivery_mst_id=$delivery_mst_id and a.production_type=10 group by b.id, b.po_number";die;
	$req_arr=return_library_array( "SELECT b.id, b.requisition_number from pro_garments_production_mst a, SAMPLE_DEVELOPMENT_MST b where a.po_break_down_id=b.id and a.delivery_mst_id=$delivery_mst_id and a.production_type=10 group by b.id, b.requisition_number",'id','requisition_number');

	?>
	<div style="width:930px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="40">SL</th>
				<? if($transfer_criteria==2){?>
                <th width="120" align="center">From Order No</th>
                <th width="120" align="center">To Req. No</th>
				<?}?>
				<? if($transfer_criteria==3){?>
                <th width="120" align="center">From Req No</th>
                <th width="120" align="center">To Req No</th>
				<?}?>
				<? if($transfer_criteria==1){?>
                <th width="120" align="center">From Order No</th>
                <th width="120" align="center">To Order No</th>
				<?}?>
                <th width="150" align="center">Item Name</th>
                <th width="110" align="center">Country</th>
                <th width="110" align="center">Transfer Qnty</th>
                <th align="center">Remarks</th>
            </thead>
    	</table>
    </div>
	<div style="width:930px;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<?php
			$i=1;
			$total_production_qnty=0;
			$sqlResult =sql_select("SELECT id, from_po_id, to_po_id, item_number_id, country_id, production_quantity, remarks from pro_gmts_delivery_dtls where mst_id=$delivery_mst_id and production_type=10 and status_active=1 and is_deleted=0 order by id");
 			foreach($sqlResult as $selectResult)
			{
 				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($transfer_criteria==3)
				{
					$_action = "populate_sample_transfer_form_data";
				}
				else
				{
					$_action = "populate_transfer_form_data";
				}
 			?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'<?=$_action;?>','requires/finish_gmts_order_to_order_transfer_controller');" >
                    <td width="40"><? echo $i; ?></td>
					<? if($transfer_criteria==2){?>
                    <td width="120" ><p><?php echo $po_arr[$selectResult[csf('from_po_id')]]; ?>&nbsp;</p></td>
                    <td width="120" ><p><?php echo $req_arr[$selectResult[csf('to_po_id')]]; ?>&nbsp;</p></td>
					<?}?>
					<? if($transfer_criteria==3){?>
                    <td width="120" ><p><?php echo $req_arr[$selectResult[csf('from_po_id')]]; ?>&nbsp;</p></td>
                    <td width="120" ><p><?php echo $req_arr[$selectResult[csf('to_po_id')]]; ?>&nbsp;</p></td>
					<?}?>
					<? if($transfer_criteria==1){?>
                    <td width="120" ><p><?php echo $po_arr[$selectResult[csf('from_po_id')]]; ?>&nbsp;</p></td>
                    <td width="120" ><p><?php echo $po_arr[$selectResult[csf('to_po_id')]]; ?>&nbsp;</p></td>
					<?}?>
                    <td width="150"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                    <td width="110"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?>&nbsp;</p></td>
                    <td width="110" align="right"><p><?php echo $selectResult[csf('production_quantity')]; ?></p></td>
                    <td><p><?php echo $selectResult[csf('remarks')]; ?>&nbsp;</p></td>
                </tr>
			<?php
			$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

//transfer entry
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		if(str_replace("'","",$update_id)=="")
		{
			//$mst_id=return_next_id("id", "pro_gmts_delivery_mst", 1);
		    $mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq","pro_gmts_delivery_mst", $con );


			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later


			$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_id,'FGT',0,date("Y",time()),0,0,10,0,0 ));


			$field_array_delivery="id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, delivery_basis, delivery_date, challan_no, transfer_criteria, inserted_by, insert_date";
			$data_array_delivery="(".$mst_id.",'".$new_sys_number[1]."',".$new_sys_number[2].",'".$new_sys_number[0]."',".str_replace("'","",$cbo_company_id).",10,1,".$txt_transfer_date.",".$txt_challan_no.",".$cbo_transfer_criteria.",".$user_id.",'".$pc_date_time."')";
		}
		else
		{
			$mst_id=str_replace("'","",$update_id);
			$field_array_delivery="delivery_date*challan_no*transfer_criteria*updated_by*update_date";
			$data_array_delivery=$txt_transfer_date."*".$txt_challan_no."*".$cbo_transfer_criteria."*".$user_id."*'".$pc_date_time."'";
		}

		if(str_replace("'","",$sewing_production_variable)=="" || str_replace("'","",$sewing_production_variable)==0)
		{
			$sewing_production_variable = 3;
		}

		//$delivery_dtlsId=return_next_id("id", "pro_gmts_delivery_dtls", 1);
		$delivery_dtlsId=return_next_id_by_sequence("PRO_GMTS_DELIVERY_DTLS_PK_SEQ", "pro_gmts_delivery_dtls", $con);

		$field_array_delivery_dtls="id,mst_id,entry_break_down_type,production_type,from_po_id,to_po_id,item_number_id,country_id,production_quantity,remarks,inserted_by,insert_date";
		$data_array_delivery_dtls="(".$delivery_dtlsId.",".$mst_id.",".str_replace("'","",$sewing_production_variable).",10,".str_replace("'","",$txt_from_order_id).",".str_replace("'","",$txt_to_order_id).",".str_replace("'","",$cbo_from_gmts_item).",".str_replace("'","",$cbo_from_country_name).",".str_replace("'","",$txt_transfer_qty).",".$txt_remark.",".$user_id.",'".$pc_date_time."')";

		//$id=return_next_id("id", "pro_garments_production_mst", 1);
		 $id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
		 $id_recv= return_next_id_by_sequence(  "pro_gar_production_mst_seq",   "pro_garments_production_mst", $con );

		//$id_recv=$id+1;
		// echo "10**".str_replace("'","",$sewing_production_variable); die;
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
			if(str_replace("'","",$cbo_transfer_criteria)==2) // order to sample
			{
				// pro_garments_production_dtls table entry here ----------------------------------///
				// $field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id, production_qnty";
				$txt_transfer_qty=0;

				if(str_replace("'","",$sewing_production_variable)==2)//color level wise
				{
					$field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id, production_qnty,delivery_mst_id";

					$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_from_order_id and item_number_id=$cbo_from_gmts_item and country_id=$cbo_from_country_name and color_mst_id!=0 and status_active=1 and is_deleted=0 order by id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("color_number_id")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}
					// ==============================================
					$color_sizeID_arr=sql_select( "SELECT id,sample_color from SAMPLE_DEVELOPMENT_DTLS where sample_mst_id=$txt_to_order_id and gmts_item_id=$cbo_to_gmts_item and status_active=1 and is_deleted=0 order by id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("sample_color")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}

					$rowEx = array_filter(explode("**",$colorIDvalue));
					// echo "10**"; print_r($rowEx);die();
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array_dtls="";
					foreach($rowEx as $rowE=>$val)
					{
						$colorSizeNumberIDArr = explode(",", implode(",", array_filter(explode("*",$val))));
						// echo "10**".sizeof($colorSizeNumberIDArr)."<br>";
						// echo "10**";print_r($colorSizeNumberIDArr);
						if(sizeof($colorSizeNumberIDArr)==2)
						{
							// echo "10**".$colSizeID_arr[$colorSizeNumberIDArr[0]];
							if($colSizeID_arr[$colorSizeNumberIDArr[0]]>0)
							{

								$txt_transfer_qty+=$colorSizeNumberIDArr[1];
								$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );


								if($data_array_dtls!="") $data_array_dtls.=",";
								$data_array_dtls.= "(".$dtls_id.",".$id.",10,6,'".$colSizeID_from_arr[$colorSizeNumberIDArr[0]]."',".$colorSizeNumberIDArr[0].",'".$colorSizeNumberIDArr[1]."',".$mst_id.")";

								// echo "ID Receive = ".$id_recv;
								$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
								$data_array_dtls.= "(".$dtls_id.",".$id_recv.",10,5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."',".$colorSizeNumberIDArr[0].",'".$colorSizeNumberIDArr[1]."',,".$mst_id.")";
								//$dtls_id=$dtls_id+1;
							}
						}
					}
				}//color level wise
				else if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
				{
					$field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id,size_id, production_qnty,delivery_mst_id";

					$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_from_order_id and item_number_id=$cbo_from_gmts_item and country_id=$cbo_from_country_name and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}
					// ====================================
					$color_sizeID_arr=sql_select( "SELECT a.id, a.sample_color as color_number_id,b.SIZE_ID as size_number_id from SAMPLE_DEVELOPMENT_DTLS a, SAMPLE_DEVELOPMENT_SIZE b where a.id=b.dtls_id and a.sample_mst_id=$txt_to_order_id and a.gmts_item_id=$cbo_to_gmts_item and b.status_active=1 and b.is_deleted=0 order by a.sample_color,b.SIZE_ID,a.id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}
					$rowEx = explode("***",$colorIDvalue);
					//print_r($rowEx); die;
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array_dtls=""; $j=0;
					foreach($rowEx as $rowE=>$valE)
					{
						$colorAndSizeAndValue_arr = explode("*",$valE);
						$sizeID = $colorAndSizeAndValue_arr[0];
						$colorID = $colorAndSizeAndValue_arr[1];
						$colorSizeValue = $colorAndSizeAndValue_arr[2];
						$index = $sizeID.$colorID;
						//echo $colSizeID_arr[$index]; die;
						if($colSizeID_arr[$index]>0)
						{
							$txt_transfer_qty+=$colorSizeValue;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
							// $id_recv= return_next_id_by_sequence(  "pro_gar_production_mst_seq",   "pro_garments_production_mst", $con );
							// echo "ID Receive = ".$id_recv;
							// $field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id,size_id, production_qnty";
							if($data_array_dtls!="") $data_array_dtls.=",";
							$data_array_dtls.= "(".str_replace("'","",$dtls_id).",".str_replace("'","",$id).",10,6,'".str_replace("'","",$colSizeID_from_arr[$index])."',".$colorID.",".$sizeID.",'".str_replace("'","",$colorSizeValue)."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

							$data_array_dtls.= "(".str_replace("'","",$dtls_id).",".str_replace("'","",$id_recv).",10,5,'".str_replace("'","",$colSizeID_arr[$index])."',".$colorID.",".$sizeID.",'".str_replace("'","",$colorSizeValue)."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;
						}
					}
				}//color and size wise
				// echo "10**$txt_transfer_qty";die();
				if($txt_transfer_qty<=0)
				{
					echo "30**Please Select Same Color And Size.$txt_transfer_qty";disconnect($con);die;
				}
			}
			elseif(str_replace("'","",$cbo_transfer_criteria)==3) // sample to sample
			{
				// pro_garments_production_dtls table entry here ----------------------------------///
				// $field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id,size_id, production_qnty";
				$txt_transfer_qty=0;

				if(str_replace("'","",$sewing_production_variable)==2)//color level wise
				{
					$field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id, production_qnty,delivery_mst_id";

					$color_sizeID_arr=sql_select( "SELECT id,sample_color from SAMPLE_DEVELOPMENT_DTLS where sample_mst_id=$txt_from_order_id and gmts_item_id=$cbo_from_gmts_item and status_active=1 and is_deleted=0 order by id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("sample_color")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}
					// ==================================
					$color_sizeID_arr=sql_select( "SELECT id,sample_color from SAMPLE_DEVELOPMENT_DTLS where sample_mst_id=$txt_to_order_id and gmts_item_id=$cbo_to_gmts_item and status_active=1 and is_deleted=0 order by id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("sample_color")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}

					$rowEx = array_filter(explode("**",$colorIDvalue));
					// echo "10**"; print_r($rowEx);die();
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array_dtls="";
					foreach($rowEx as $rowE=>$val)
					{
						$colorSizeNumberIDArr = explode(",", implode(",", array_filter(explode("*",$val))));
						// echo "10**".sizeof($colorSizeNumberIDArr)."<br>";
						// echo "10**";print_r($colorSizeNumberIDArr);
						if(sizeof($colorSizeNumberIDArr)==2)
						{
							// echo "10**".$colSizeID_arr[$colorSizeNumberIDArr[0]];
							if($colSizeID_arr[$colorSizeNumberIDArr[0]]>0)
							{

								$txt_transfer_qty+=$colorSizeNumberIDArr[1];
								$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );


								if($data_array_dtls!="") $data_array_dtls.=",";
								$data_array_dtls.= "(".$dtls_id.",".$id.",10,6,'".$colSizeID_from_arr[$colorSizeNumberIDArr[0]]."',".$colorSizeNumberIDArr[0].",'".$colorSizeNumberIDArr[1]."',".$mst_id.")";

								// echo "ID Receive = ".$id_recv;
								$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
								$data_array_dtls.= "(".$dtls_id.",".$id_recv.",10,5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."',".$colorSizeNumberIDArr[0].",'".$colorSizeNumberIDArr[1]."',".$mst_id.")";
								//$dtls_id=$dtls_id+1;
							}
						}
					}
				}//color level wise
				else if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
				{
					$field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id,size_id, production_qnty,delivery_mst_id";
					$color_sizeID_arr=sql_select( "SELECT a.id, a.sample_color as color_number_id,b.SIZE_ID as size_number_id from SAMPLE_DEVELOPMENT_DTLS a, SAMPLE_DEVELOPMENT_SIZE b where a.id=b.dtls_id and a.sample_mst_id=$txt_from_order_id and a.gmts_item_id=$cbo_from_gmts_item and b.status_active=1 and b.is_deleted=0 order by a.sample_color,b.SIZE_ID,a.id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}

					$color_sizeID_arr=sql_select( "SELECT a.id, a.sample_color as color_number_id,b.SIZE_ID as size_number_id from SAMPLE_DEVELOPMENT_DTLS a, SAMPLE_DEVELOPMENT_SIZE b where a.id=b.dtls_id and a.sample_mst_id=$txt_to_order_id and a.gmts_item_id=$cbo_to_gmts_item and b.status_active=1 and b.is_deleted=0 order by a.sample_color,b.SIZE_ID,a.id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}

					$rowEx = explode("***",$colorIDvalue);
					//print_r($rowEx); die;
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array_dtls=""; $j=0;
					foreach($rowEx as $rowE=>$valE)
					{
						$colorAndSizeAndValue_arr = explode("*",$valE);
						$sizeID = $colorAndSizeAndValue_arr[0];
						$colorID = $colorAndSizeAndValue_arr[1];
						$colorSizeValue = $colorAndSizeAndValue_arr[2];
						$index = $sizeID.$colorID;
						//echo $colSizeID_arr[$index]; die;
						if($colSizeID_arr[$index]>0)
						{
							$txt_transfer_qty+=$colorSizeValue;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
							// $id_recv= return_next_id_by_sequence(  "pro_gar_production_mst_seq",   "pro_garments_production_mst", $con );
							// echo "ID Receive = ".$id_recv;
							// $field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id, production_qnty";
							if($data_array_dtls!="") $data_array_dtls.=",";
							$data_array_dtls.= "(".str_replace("'","",$dtls_id).",".str_replace("'","",$id).",10,6,'".str_replace("'","",$colSizeID_from_arr[$index])."',".$colorID.",".$sizeID.",'".str_replace("'","",$colorSizeValue)."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

							$data_array_dtls.= "(".str_replace("'","",$dtls_id).",".str_replace("'","",$id_recv).",10,5,'".str_replace("'","",$colSizeID_arr[$index])."',".$colorID.",".$sizeID.",'".str_replace("'","",$colorSizeValue)."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;
						}
					}
				}//color and size wise
				// echo "10**$txt_transfer_qty";die();
				if($txt_transfer_qty<=0)
				{
					echo "30**Please Select Same Color And Size.$txt_transfer_qty";disconnect($con);die;
				}
			}
			else // order to order
			{
				// pro_garments_production_dtls table entry here ----------------------------------///
				$field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id, production_qnty,delivery_mst_id";
				$txt_transfer_qty=0;

				if(str_replace("'","",$sewing_production_variable)==2)//color level wise
				{
					$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_from_order_id and item_number_id=$cbo_from_gmts_item and country_id=$cbo_from_country_name and color_mst_id!=0 and status_active=1 and is_deleted=0 order by id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("color_number_id")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}

					$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_to_order_id and item_number_id=$cbo_to_gmts_item and country_id=$cbo_to_country_name and color_mst_id!=0 and status_active=1 and is_deleted=0 order by id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}

					$rowEx = array_filter(explode("**",$colorIDvalue));
					// echo "10**"; print_r($rowEx);die();
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array_dtls="";
					foreach($rowEx as $rowE=>$val)
					{
						$colorSizeNumberIDArr = explode(",", implode(",", array_filter(explode("*",$val))));
						// echo "10**".sizeof($colorSizeNumberIDArr)."<br>";
						// echo "10**";print_r($colorSizeNumberIDArr);
						if(sizeof($colorSizeNumberIDArr)==2)
						{
							// echo "10**".$colSizeID_arr[$colorSizeNumberIDArr[0]];
							if($colSizeID_arr[$colorSizeNumberIDArr[0]]>0)
							{

								$txt_transfer_qty+=$colorSizeNumberIDArr[1];
								$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );


								if($data_array_dtls!="") $data_array_dtls.=",";
								$data_array_dtls.= "(".$dtls_id.",".$id.",10,6,'".$colSizeID_from_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."',".$mst_id.")";

								// echo "ID Receive = ".$id_recv;
								$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
								$data_array_dtls.= "(".$dtls_id.",".$id_recv.",10,5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."',".$mst_id.")";
								//$dtls_id=$dtls_id+1;
							}
						}
					}
				}//color level wise
				else if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
				{
					$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_from_hid_po_id and item_number_id=$cbo_from_gmts_item and country_id=$cbo_from_country_name and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}

					$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_to_hid_po_id and item_number_id=$cbo_to_gmts_item and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );// and country_id=$cbo_to_country_name
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}

					$color_sizeID_arr=sql_select( "SELECT id,gmts_size_id as size_number_id,gmts_color_id as color_number_id from WO_PO_ACC_PO_INFO_DTLS where mst_id=$txt_to_order_id and gmts_item=$cbo_to_gmts_item  and status_active=1 and is_deleted=0 order by gmts_size_id,gmts_color_id" );//and country_id=$cbo_to_country_name
					$colSizeActID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeActID_arr[$index]=$val[csf("id")];
					}

					$rowEx = explode("***",$colorIDvalue);
					// echo "10**";print_r($rowEx); disconnect($con); die;
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id, production_qnty,delivery_mst_id";
					$field_actual = "id,mst_id,actual_po_id,actual_po_dtls_id,production_type,trans_type,prod_qty,inserted_by,insert_date,status_active,is_deleted";
					$act_id = return_next_id("id", "PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS", 1);
					$data_array_dtls=""; 
					$act_data=""; 
					$j=0;
					foreach($rowEx as $rowE=>$valE)
					{
						$colorAndSizeAndValue_arr = explode("*",$valE);
						$sizeID = $colorAndSizeAndValue_arr[0];
						$colorID = $colorAndSizeAndValue_arr[1];
						$actPoDtlsId = $colorAndSizeAndValue_arr[2];
						$colorSizeValue = $colorAndSizeAndValue_arr[3];
						$index = $sizeID.$colorID;
						// echo "10**".$colSizeID_arr[$index]; die;
						if($colSizeID_arr[$index]>0)
						{
							$txt_transfer_qty += $colorSizeValue;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
							// $id_recv= return_next_id_by_sequence(  "pro_gar_production_mst_seq",   "pro_garments_production_mst", $con );
							// echo "ID Receive = ".$id_recv;
							
							if($data_array_dtls!="") $data_array_dtls.=",";
							$data_array_dtls.= "(".str_replace("'","",$dtls_id).",".str_replace("'","",$id).",10,6,'".str_replace("'","",$colSizeID_from_arr[$index])."','".str_replace("'","",$colorSizeValue)."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

							$data_array_dtls.= "(".str_replace("'","",$dtls_id).",".str_replace("'","",$id_recv).",10,5,'".str_replace("'","",$colSizeID_arr[$index])."','".str_replace("'","",$colorSizeValue)."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;


							// ============== actual po wise ===================
							if($act_data!="") $act_data.=",";
							$act_data.= "(".str_replace("'","",$act_id).",".str_replace("'","",$id).",".$txt_from_order_id.",'".$actPoDtlsId."',10,6,'".str_replace("'","",$colorSizeValue)."',".$user_id.",'".$pc_date_time."',1,0)";
							$act_id = $act_id+1;

							$act_data.= "(".str_replace("'","",$act_id).",".str_replace("'","",$id_recv).",".$txt_to_order_id.",'".str_replace("'","",$colSizeActID_arr[$index])."',10,5,'".str_replace("'","",$colorSizeValue)."',".$user_id.",'".$pc_date_time."',1,0)";

							$act_id = $act_id+1;

						}
					}
				}//color and size wise
				// echo "10**$txt_transfer_qty";die();
				if($txt_transfer_qty<=0)
				{
					echo "30**Please Select Same Color And Size.$txt_transfer_qty";disconnect($con);die;
				}
			}
		}

		$field_array1="id, delivery_mst_id, dtls_id, garments_nature, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_date, production_quantity, production_type, trans_type, entry_break_down_type, remarks,is_po_ok,IS_PREFINAL_ACKN,IS_RCV_ACKN, inserted_by, insert_date";

		$data_array1="(".$id.",".$mst_id.",".$delivery_dtlsId.",".str_replace("'","",$garments_nature).",".str_replace("'","",$cbo_company_id).",".$txt_challan_no.",".str_replace("'","",$txt_from_hid_po_id).",".str_replace("'","",$cbo_from_gmts_item).",".str_replace("'","",$cbo_from_country_name).",".$txt_transfer_date.",".str_replace("'","",$txt_transfer_qty).",10,6,".str_replace("'","",$sewing_production_variable).",".$txt_remark.",".$cbo_po_ok.",1,1,".$user_id.",'".$pc_date_time."')";

		if($id_recv != "" && str_replace("'","",$sewing_production_variable)!=1)
		{
			$data_array1.=",(".$id_recv.",".$mst_id.",".$delivery_dtlsId.",".str_replace("'","",$garments_nature).",".str_replace("'","",$cbo_company_id).",".$txt_challan_no.",".str_replace("'","",$txt_to_hid_po_id).",".str_replace("'","",$cbo_to_gmts_item).",".str_replace("'","",$cbo_to_country_name).",".$txt_transfer_date.",".str_replace("'","",$txt_transfer_qty).",10,5,".str_replace("'","",$sewing_production_variable).",".$txt_remark.",".$cbo_po_ok.",1,1,".$user_id.",'".$pc_date_time."')";
		}
		else
		{
			$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
			$data_array1.=",(".$id.",".$mst_id.",".$delivery_dtlsId.",".str_replace("'","",$garments_nature).",".str_replace("'","",$cbo_company_id).",".$txt_challan_no.",".str_replace("'","",$txt_to_hid_po_id).",".str_replace("'","",$cbo_to_gmts_item).",".str_replace("'","",$cbo_to_country_name).",".$txt_transfer_date.",".str_replace("'","",$txt_transfer_qty).",10,5,".str_replace("'","",$sewing_production_variable).",".$txt_remark.",".$cbo_po_ok.",1,1,".$user_id.",'".$pc_date_time."')";
		}

		if(str_replace("'","",$txt_system_id)=="")
		{
			$challanrID=sql_insert("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,1);
		}
		else
		{
			$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$update_id,1);
		}

		// echo $field_array_delivery."<br>";
		// echo $data_array_delivery."<br>";

		//echo $field_array_delivery_dtls."<br>";
		//echo $data_array_delivery_dtls."<br>";

		// echo $field_array1."<br>";
		// echo $data_array1."<br>";

		// echo $field_array_dtls."<br>";
		// echo $data_array_dtls."<br>";

		$rIDDtls=sql_insert("pro_gmts_delivery_dtls",$field_array_delivery_dtls,$data_array_delivery_dtls,1);
		// echo "10**INSERT INTO pro_gmts_delivery_dtls (".$field_array_delivery_dtls.")VALUES $data_array_delivery_dtls"; die;
		$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);

		$dtlsrID=true;
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		}

		$dtlsrActID=true;
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
 			$dtlsrActID=sql_insert("PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS",$field_actual,$act_data,1);
		}
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		//oci_rollback($con);
		// echo "10**INSERT INTO PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS (".$field_actual.") VALUES ".$act_data.""; die;

		// echo "10**".$challanrID ."&&". $rIDDtls ."&&". $rID ."&&". $dtlsrID."&&". $dtlsrActID;disconnect($con);die;
		 
		
		if($challanrID && $rIDDtls && $rID && $dtlsrID && $dtlsrActID)
		{
			oci_commit($con);
			echo "0**".$mst_id."**".$new_sys_number[0]."**".str_replace("'","",$cbo_transfer_criteria);
		}
		else
		{
			oci_rollback($con);
			echo "10**";
		}
		
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		$mst_id=str_replace("'","",$update_id);
		$field_array_delivery="delivery_date*challan_no*transfer_criteria*updated_by*update_date";
		$data_array_delivery=$txt_transfer_date."*".$txt_challan_no."*".$cbo_transfer_criteria."*".$user_id."*'".$pc_date_time."'";

		$field_array_delivery_dtls="from_po_id*to_po_id*item_number_id*country_id*production_quantity*remarks*updated_by*update_date";
		$data_array_delivery_dtls=$txt_from_order_id."*".$txt_to_order_id."*".$cbo_from_gmts_item."*".$cbo_from_country_name."*".$txt_transfer_qty."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";
		if(str_replace("'","",$sewing_production_variable)=="" || str_replace("'","",$sewing_production_variable)==0)
		{
			$sewing_production_variable = 3;
		}

		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
			if(str_replace("'","",$cbo_transfer_criteria)==2) // order to sample
			{
				// pro_garments_production_dtls table entry here ----------------------------------///
				// $field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id, production_qnty";
				$txt_transfer_qty=0;

				if(str_replace("'","",$sewing_production_variable)==2)//color level wise
				{
					$field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id, production_qnty,delivery_mst_id";

					$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_from_order_id and item_number_id=$cbo_from_gmts_item and country_id=$cbo_from_country_name and color_mst_id!=0 and status_active=1 and is_deleted=0 order by id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("color_number_id")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}
					// ==============================================
					$color_sizeID_arr=sql_select( "SELECT id,sample_color from SAMPLE_DEVELOPMENT_DTLS where sample_mst_id=$txt_to_order_id and gmts_item_id=$cbo_to_gmts_item and status_active=1 and is_deleted=0 order by id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("sample_color")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}

					$rowEx = array_filter(explode("**",$colorIDvalue));
					// echo "10**"; print_r($rowEx);die();
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array_dtls="";
					foreach($rowEx as $rowE=>$val)
					{
						$colorSizeNumberIDArr = explode(",", implode(",", array_filter(explode("*",$val))));
						// echo "10**".sizeof($colorSizeNumberIDArr)."<br>";
						// echo "10**";print_r($colorSizeNumberIDArr);
						if(sizeof($colorSizeNumberIDArr)==2)
						{
							// echo "10**".$colSizeID_arr[$colorSizeNumberIDArr[0]];
							if($colSizeID_arr[$colorSizeNumberIDArr[0]]>0)
							{

								$txt_transfer_qty+=$colorSizeNumberIDArr[1];
								$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );


								if($data_array_dtls!="") $data_array_dtls.=",";
								$data_array_dtls.= "(".$dtls_id.",".$update_dtls_issue_id.",10,6,'".$colSizeID_from_arr[$colorSizeNumberIDArr[0]]."',".$colorSizeNumberIDArr[0].",'".$colorSizeNumberIDArr[1]."',".$mst_id.")";

								// echo "ID Receive = ".$id_recv;
								$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
								$data_array_dtls.= "(".$dtls_id.",".$update_dtls_recv_id.",10,5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."',".$colorSizeNumberIDArr[0].",'".$colorSizeNumberIDArr[1]."',".$mst_id.")";
								//$dtls_id=$dtls_id+1;
							}
						}
					}
				}//color level wise
				else if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
				{
					$field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id,size_id, production_qnty,delivery_mst_id";

					$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_from_order_id and item_number_id=$cbo_from_gmts_item and country_id=$cbo_from_country_name and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}
					// ====================================
					$color_sizeID_arr=sql_select( "SELECT a.id, a.sample_color as color_number_id,b.SIZE_ID as size_number_id from SAMPLE_DEVELOPMENT_DTLS a, SAMPLE_DEVELOPMENT_SIZE b where a.id=b.dtls_id and a.sample_mst_id=$txt_to_order_id and a.gmts_item_id=$cbo_to_gmts_item and b.status_active=1 and b.is_deleted=0 order by a.sample_color,b.SIZE_ID,a.id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}
					$rowEx = explode("***",$colorIDvalue);
					//print_r($rowEx); die;
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array_dtls=""; $j=0;
					foreach($rowEx as $rowE=>$valE)
					{
						$colorAndSizeAndValue_arr = explode("*",$valE);
						$sizeID = $colorAndSizeAndValue_arr[0];
						$colorID = $colorAndSizeAndValue_arr[1];
						$colorSizeValue = $colorAndSizeAndValue_arr[2];
						$index = $sizeID.$colorID;
						//echo $colSizeID_arr[$index]; die;
						if($colSizeID_arr[$index]>0)
						{
							$txt_transfer_qty+=$colorSizeValue;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
							// $id_recv= return_next_id_by_sequence(  "pro_gar_production_mst_seq",   "pro_garments_production_mst", $con );
							// echo "ID Receive = ".$id_recv;
							// $field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id,size_id, production_qnty";
							if($data_array_dtls!="") $data_array_dtls.=",";
							$data_array_dtls.= "(".str_replace("'","",$dtls_id).",".str_replace("'","",$update_dtls_issue_id).",10,6,'".str_replace("'","",$colSizeID_from_arr[$index])."',".$colorID.",".$sizeID.",'".str_replace("'","",$colorSizeValue)."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

							$data_array_dtls.= "(".str_replace("'","",$dtls_id).",".str_replace("'","",$update_dtls_recv_id).",10,5,'".str_replace("'","",$colSizeID_arr[$index])."',".$colorID.",".$sizeID.",'".str_replace("'","",$colorSizeValue)."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;
						}
					}
				}//color and size wise
				// echo "10**$txt_transfer_qty";die();
				if($txt_transfer_qty<=0)
				{
					echo "30**Please Select Same Color And Size.$txt_transfer_qty";disconnect($con);die;
				}
			}
			elseif(str_replace("'","",$cbo_transfer_criteria)==3) // sample to sample
			{
				// pro_garments_production_dtls table entry here ----------------------------------///
				// $field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id,size_id, production_qnty";
				$txt_transfer_qty=0;

				if(str_replace("'","",$sewing_production_variable)==2)//color level wise
				{
					$field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id, production_qnty,delivery_mst_id";

					$color_sizeID_arr=sql_select( "SELECT id,sample_color from SAMPLE_DEVELOPMENT_DTLS where sample_mst_id=$txt_from_order_id and gmts_item_id=$cbo_from_gmts_item and status_active=1 and is_deleted=0 order by id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("sample_color")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}
					// ==================================
					$color_sizeID_arr=sql_select( "SELECT id,sample_color from SAMPLE_DEVELOPMENT_DTLS where sample_mst_id=$txt_to_order_id and gmts_item_id=$cbo_to_gmts_item and status_active=1 and is_deleted=0 order by id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("sample_color")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}

					$rowEx = array_filter(explode("**",$colorIDvalue));
					// echo "10**"; print_r($rowEx);die();
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array_dtls="";
					foreach($rowEx as $rowE=>$val)
					{
						$colorSizeNumberIDArr = explode(",", implode(",", array_filter(explode("*",$val))));
						// echo "10**".sizeof($colorSizeNumberIDArr)."<br>";
						// echo "10**";print_r($colorSizeNumberIDArr);
						if(sizeof($colorSizeNumberIDArr)==2)
						{
							// echo "10**".$colSizeID_arr[$colorSizeNumberIDArr[0]];
							if($colSizeID_arr[$colorSizeNumberIDArr[0]]>0)
							{

								$txt_transfer_qty+=$colorSizeNumberIDArr[1];
								$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );


								if($data_array_dtls!="") $data_array_dtls.=",";
								$data_array_dtls.= "(".$dtls_id.",".$update_dtls_issue_id.",10,6,'".$colSizeID_from_arr[$colorSizeNumberIDArr[0]]."',".$colorSizeNumberIDArr[0].",'".$colorSizeNumberIDArr[1]."',".$mst_id.")";

								// echo "ID Receive = ".$id_recv;
								$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
								$data_array_dtls.= "(".$dtls_id.",".$update_dtls_recv_id.",10,5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."',".$colorSizeNumberIDArr[0].",'".$colorSizeNumberIDArr[1]."',".$mst_id.")";
								//$dtls_id=$dtls_id+1;
							}
						}
					}
				}//color level wise
				else if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
				{
					$field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id,color_id,size_id, production_qnty,delivery_mst_id";
					$color_sizeID_arr=sql_select( "SELECT a.id, a.sample_color as color_number_id,b.SIZE_ID as size_number_id from SAMPLE_DEVELOPMENT_DTLS a, SAMPLE_DEVELOPMENT_SIZE b where a.id=b.dtls_id and a.sample_mst_id=$txt_from_order_id and a.gmts_item_id=$cbo_from_gmts_item and b.status_active=1 and b.is_deleted=0 order by a.sample_color,b.SIZE_ID,a.id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}

					$color_sizeID_arr=sql_select( "SELECT a.id, a.sample_color as color_number_id,b.SIZE_ID as size_number_id from SAMPLE_DEVELOPMENT_DTLS a, SAMPLE_DEVELOPMENT_SIZE b where a.id=b.dtls_id and a.sample_mst_id=$txt_to_order_id and a.gmts_item_id=$cbo_to_gmts_item and b.status_active=1 and b.is_deleted=0 order by a.sample_color,b.SIZE_ID,a.id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val)
					{
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}

					$rowEx = explode("***",$colorIDvalue);
					//print_r($rowEx); die;
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array_dtls=""; $j=0;
					foreach($rowEx as $rowE=>$valE)
					{
						$colorAndSizeAndValue_arr = explode("*",$valE);
						$sizeID = $colorAndSizeAndValue_arr[0];
						$colorID = $colorAndSizeAndValue_arr[1];
						$colorSizeValue = $colorAndSizeAndValue_arr[2];
						$index = $sizeID.$colorID;
						//echo $colSizeID_arr[$index]; die;
						if($colSizeID_arr[$index]>0)
						{
							$txt_transfer_qty+=$colorSizeValue;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
							// $id_recv= return_next_id_by_sequence(  "pro_gar_production_mst_seq",   "pro_garments_production_mst", $con );
							// echo "ID Receive = ".$id_recv;
							// $field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id, production_qnty";
							if($data_array_dtls!="") $data_array_dtls.=",";
							$data_array_dtls.= "(".str_replace("'","",$dtls_id).",".str_replace("'","",$update_dtls_issue_id).",10,6,'".str_replace("'","",$colSizeID_from_arr[$index])."',".$colorID.",".$sizeID.",'".str_replace("'","",$colorSizeValue)."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

							$data_array_dtls.= "(".str_replace("'","",$dtls_id).",".str_replace("'","",$update_dtls_recv_id).",10,5,'".str_replace("'","",$colSizeID_arr[$index])."',".$colorID.",".$sizeID.",'".str_replace("'","",$colorSizeValue)."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;
						}
					}
				}//color and size wise
				// echo "10**$txt_transfer_qty";die();
				if($txt_transfer_qty<=0)
				{
					echo "30**Please Select Same Color And Size.$txt_transfer_qty";disconnect($con);die;
				}
			}
			else // order to order
			{
				// pro_garments_production_dtls table entry here ----------------------------------///
				$field_array_dtls="id, mst_id, production_type, trans_type, color_size_break_down_id, production_qnty,delivery_mst_id";
				$txt_transfer_qty=0;
				if(str_replace("'","",$sewing_production_variable)==2)//color level wise
				{
					$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_from_order_id and item_number_id=$cbo_from_gmts_item and country_id=$cbo_from_country_name and color_mst_id!=0 and status_active=1 and is_deleted=0 order by id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("color_number_id")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}

					$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_to_order_id and item_number_id=$cbo_to_gmts_item and country_id=$cbo_to_country_name and color_mst_id!=0 and status_active=1 and is_deleted=0 order by id" );
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}

					$rowEx = array_filter(explode("**",$colorIDvalue));
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$data_array_dtls="";
					foreach($rowEx as $rowE=>$val)
					{
						$colorSizeNumberIDArr = explode(",", implode(",", array_filter(explode("*",$val))));
						if(sizeof($colorSizeNumberIDArr)==2)
						{
							if($colSizeID_arr[$colorSizeNumberIDArr[0]]>0)
							{
								$txt_transfer_qty+=$colorSizeNumberIDArr[1];
								$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

								if($data_array_dtls!="") $data_array_dtls.=",";
								$data_array_dtls.= "(".$dtls_id.",".$update_dtls_issue_id.",10,6,'".$colSizeID_from_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."',".$mst_id.")";

								$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

								$data_array_dtls.= "(".$dtls_id.",".$update_dtls_recv_id.",10,5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."',".$mst_id.")";
								//	$dtls_id=$dtls_id+1;
							}
						}
					}
				}//color level wise
				else if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
				{
					$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_from_hid_po_id and item_number_id=$cbo_from_gmts_item  and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );//and country_id=$cbo_from_country_name
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_from_arr[$index]=$val[csf("id")];
					}

					$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$txt_to_hid_po_id and item_number_id=$cbo_to_gmts_item  and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );//and country_id=$cbo_to_country_name
					$colSizeID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeID_arr[$index]=$val[csf("id")];
					}
					
					$color_sizeID_arr=sql_select( "SELECT id,gmts_size_id as size_number_id,gmts_color_id as color_number_id from WO_PO_ACC_PO_INFO_DTLS where mst_id=$txt_to_order_id and gmts_item=$cbo_to_gmts_item and country_id=$cbo_to_country_name and status_active=1 and is_deleted=0 order by gmts_size_id,gmts_color_id" );

					// echo "10**SELECT id,gmts_size_id as size_number_id,gmts_color_id as color_number_id from WO_PO_ACC_PO_INFO_DTLS where mst_id=$txt_to_order_id and gmts_item=$cbo_to_gmts_item and country_id=$cbo_to_country_name and status_active=1 and is_deleted=0 order by gmts_size_id,gmts_color_id";disconnect($con); die;
					$colSizeActID_arr=array();
					foreach($color_sizeID_arr as $val){
						$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
						$colSizeActID_arr[$index]=$val[csf("id")];
					}

					$rowEx = explode("***",$colorIDvalue);
					// echo "10**";print_r($rowEx);disconnect($con);die;
					//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
					$field_actual = "id,mst_id,actual_po_id,actual_po_dtls_id,production_type,trans_type,prod_qty,inserted_by,insert_date,status_active,is_deleted";
					$act_id = return_next_id("id", "PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS", 1);
					$act_data=""; 
					$data_array_dtls=""; $j=0;
					foreach($rowEx as $rowE=>$valE)
					{
						$colorAndSizeAndValue_arr = explode("*",$valE);
						$sizeID = $colorAndSizeAndValue_arr[0];
						$colorID = $colorAndSizeAndValue_arr[1];
						$actPoDtlsId = $colorAndSizeAndValue_arr[2];
						$colorSizeValue = $colorAndSizeAndValue_arr[3];
						$index = $sizeID.$colorID;

						if($colSizeActID_arr[$index]>0)
						{
							$txt_transfer_qty+=$colorSizeValue;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );

							if($data_array_dtls!="") $data_array_dtls.=",";
							$data_array_dtls.= "(".$dtls_id.",".$update_dtls_issue_id.",10,6,'".$colSizeID_from_arr[$index]."','".$colorSizeValue."',".$mst_id.")";
							//	$dtls_id=$dtls_id+1;
							$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

							$data_array_dtls.= "(".$dtls_id.",".$update_dtls_recv_id.",10,5,'".$colSizeID_arr[$index]."','".$colorSizeValue."',".$mst_id.")";
							//$dtls_id=$dtls_id+1;

							// ============== actual po wise ===================
							if($act_data!="") $act_data.=",";
							$act_data.= "(".str_replace("'","",$act_id).",".str_replace("'","",$update_dtls_issue_id).",".$txt_from_order_id.",'".$actPoDtlsId."',10,6,'".str_replace("'","",$colorSizeValue)."',".$user_id.",'".$pc_date_time."',1,0)";
							$act_id = $act_id+1;

							$act_data.= "(".str_replace("'","",$act_id).",".str_replace("'","",$update_dtls_recv_id).",".$txt_to_order_id.",".str_replace("'","",$colSizeActID_arr[$index]).",10,5,'".str_replace("'","",$colorSizeValue)."',".$user_id.",'".$pc_date_time."',1,0)";

							$act_id = $act_id+1;
						}
					}
				}//color and size wise

				if($txt_transfer_qty<=0)
				{
					echo "30**Please Select Same Color And Size.";disconnect($con);die;
				}
			}
		}

		$field_array_update="challan_no*po_break_down_id*item_number_id*country_id*production_date*production_quantity*remarks*is_po_ok*updated_by*update_date";
		$updateId_array=array();
		$update_dtls_issue_id=str_replace("'","",$update_dtls_issue_id);
		$update_dtls_recv_id=str_replace("'","",$update_dtls_recv_id);

		if($update_dtls_issue_id != ''){
			$updateId_array[]=$update_dtls_issue_id;
			$updateID_data[$update_dtls_issue_id]=explode("*",("".$txt_challan_no."*".$txt_from_hid_po_id."*".$cbo_from_gmts_item."*".$cbo_from_country_name."*".$txt_transfer_date."*".$txt_transfer_qty."*".$txt_remark."*".$cbo_po_ok."*".$user_id."*'".$pc_date_time."'"));
		}
		if($update_dtls_recv_id != ''){
			$updateId_array[]=$update_dtls_recv_id;
			$updateID_data[$update_dtls_recv_id]=explode("*",("".$txt_challan_no."*".$txt_to_hid_po_id."*".$cbo_to_gmts_item."*".$cbo_to_country_name."*".$txt_transfer_date."*".$txt_transfer_qty."*".$txt_remark."*".$cbo_po_ok."*".$user_id."*'".$pc_date_time."'"));
		}

		$rID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id",$update_id,1);
		$rID2=sql_update("pro_gmts_delivery_dtls",$field_array_delivery_dtls,$data_array_delivery_dtls,"id",$update_dtls_id,1);
		//print_r($updateId_array); die;
		$rID3=execute_query(bulk_update_sql_statement("pro_garments_production_mst","id",$field_array_update,$updateID_data,$updateId_array));

		//$mst_ids=$update_dtls_issue_id.",".$update_dtls_recv_id;
		$mst_ids = implode (", ", $updateId_array);
		

		$dtlsrID=true;
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
			$rID4=execute_query("DELETE from pro_garments_production_dtls where mst_id in($mst_ids)",1);
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_dtls,$data_array_dtls,1);
		}

		
		$dtlsrActID = true;
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
			$rID5=execute_query("DELETE from PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS where mst_id in($mst_ids)",1);
			$dtlsrActID=sql_insert("PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS",$field_actual,$act_data,1);
		}
		//check_table_status( $_SESSION['menu_id'],0);
		//oci_rollback($con);
		// echo "10**$sewing_production_variable ** INSERT INTO pro_garments_production_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; die;
		// echo "10**$mst_id ** rID=".$rID ." && rID2=". $rID2 ." && rID3=". $rID3 ." && rID4=". $rID4 ." && dtlsrID=". $dtlsrID;die;
		
		if($rID && $rID2 && $rID3 && $rID4 && $dtlsrID && $rID5 && $dtlsrActID)
		{
			oci_commit($con);
			echo "1**".$mst_id."**".str_replace("'","",$txt_system_id)."**".str_replace("'","",$cbo_transfer_criteria);
		}
		else
		{
			oci_rollback($con);
			echo "10**";
		}
		

		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// ============== check ex-factory =======================
		$poData = sql_select("SELECT id, po_break_down_id as po_id, item_number_id as item_id,country_id from pro_garments_production_mst where delivery_mst_id=$update_id and status_active=1 and is_deleted=0");
		$poId = $poData[0]['PO_ID'];
		$itmId = $poData[0]['ITEM_ID'];
		$countyId = $poData[0]['COUNTRY_ID'];
		foreach ($poData as $v) 
		{
			$mstIdArr[$v['ID']] = $v['ID']; 
		}
		$mstIds = implode(",",$mstIdArr);
		$exSql = sql_select("SELECT sum(ex_factory_qnty) as qty from pro_ex_factory_mst where po_break_down_id=$poId and item_number_id=$itmId and country_id=$countyId and status_active=1 and is_deleted=0");
		$exQty = $exSql[0]['QTY'];
		if($exQty > 0)
		{
			echo "11**Ex-factory qnty found! So, you can not delete.";
			disconnect($con);
			exit();
		}

		// =========delete from delivery ==========================
		$DrID = sql_delete("pro_gmts_delivery_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$update_id,1);
		$DdtlsrID = sql_delete("pro_gmts_delivery_dtls","status_active*is_deleted","0*1",'mst_id',$update_id,1);
		// =========delete from production ==========================
 		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'delivery_mst_id ',$update_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'delivery_mst_id',$update_id,1);
		$dtlsrActID = execute_query("UPDATE PRO_GARMENTS_PROD_ACTUAL_PO_DETAILS set status_active=0,is_deleted=1 where mst_id in($mstIds)",1);
 		
		if($rID && $dtlsrID && $DrID && $DdtlsrID)
		{
			oci_commit($con);
			echo "2**".str_replace("'","",$hidden_po_break_down_id);
		}
		else
		{
			oci_rollback($con);
			echo "10**".str_replace("'","",$hidden_po_break_down_id);
		}
		
		disconnect($con);
		die;
	}
}

if ($action=="orderToorderTransfer_popup")
{
	echo load_html_head_contents("Order To Order Transfer Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			$('#transfer_data').val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:680px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:650px;margin-left:10px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all">
	                <thead>
	                    <th>Search By</th>
	                    <th width="240" id="search_by_td_up">Please Enter Transfer ID</th>
						<th colspan="2">Date Range</th>
	                    <th>

	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="transfer_data" id="transfer_data" class="text_boxes" value="">
	                    </th>
	                </thead>
	                <tr class="general">
	                    <td>
							<?
								$search_by_arr=array(1=>"Transfer ID",2=>"Challan No.",3=>"Job No.",4=>"Style Ref.",5=>"Order No");
								$dd="change_search_event(this.value, '0*0*0*0*0', '0*0*0*0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
	                        ?>
	                    </td>
	                    <td id="search_by_td">
	                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
	                    </td>
						<td align="center">
                            <input type="text" style="width:80px;" class="datepicker"  name="txt_date_from" id="txt_date_from" readonly />
                        </td>
                        <td align="center">
                            <input type="text" style="width:80px;" class="datepicker"  name="txt_date_to" id="txt_date_to" readonly />
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_transfer_search_list_view', 'search_div', 'finish_gmts_order_to_order_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	            </table>
				<tr>
                    <td  align="center"  valign="middle">
                        <?=load_month_buttons(1);?>
                    </td>
                </tr>
	        	<div style="margin-top:10px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);

	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$txt_date_from=$data[3];
	$txt_date_to=$data[4];
	//echo $txt_date_to;

	$search_field="";
	if($search_by==1)
	{
		$search_field="a.sys_number";
	}else if($search_by==2)
	{
		$search_field="a.challan_no";

	}else if($search_by==3)
	{
		$search_field="c.job_no_prefix_num";

	}else if($search_by==4)
	{
		$search_field="c.style_ref_no";

	}else if($search_by==5)
	{
		$search_field="d.po_number";

	}



	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date) as year";
		$delivery_date="DATE_FORMAT(a.delivery_date, '%d-%m-%Y')";
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		$delivery_date="to_char(a.delivery_date,'DD-MM-YYYY')";
	}
	 else $year_field="";//defined Later

	 $date_con="";
	 if($txt_date_from!='' && $txt_date_to!=''){

            $txt_date_from=change_date_format($txt_date_from,'','',-1);
            $txt_date_to=change_date_format($txt_date_to,'','',-1);

        $date_con=" and a.delivery_date BETWEEN '$txt_date_from' and '$txt_date_to'";
    }
    else
    {
        $date_con="";
    }

 	$sql="SELECT a.id, a.sys_number_prefix_num, $year_field, a.sys_number, a.challan_no, a.company_id, c.job_no_prefix_num,c.style_ref_no,d.po_number, a. delivery_date,a.transfer_criteria from pro_gmts_delivery_mst a,pro_garments_production_mst b,wo_po_details_master c,wo_po_break_down d  where
	a.id=b.delivery_mst_id and d.id=b.po_break_down_id and c.id=d.job_id  and a.production_type=10 and a.company_id=$company_id and $search_field like '$search_string' $date_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 order by a.id desc";
	// echo $sql; die;
	$sql_result=sql_select($sql);
	$data_array=array();
	foreach($sql_result as $v)
	{  $data_array[$v['ID']]['sys_number']=$v['SYS_NUMBER'];
		$data_array[$v['ID']]['sys_number_prefix_num']=$v['SYS_NUMBER_PREFIX_NUM'];
		$data_array[$v['ID']]['year']=$v['YEAR'];
		$data_array[$v['ID']]['challan_no']=$v['CHALLAN_NO'];
		$data_array[$v['ID']]['company_id']=$v['COMPANY_ID'];
		$data_array[$v['ID']]['delivery_date']=  date("d-m-Y", strtotime($v['DELIVERY_DATE']));
		$data_array[$v['ID']]['transfer_criteria']=$v['TRANSFER_CRITERIA'];
	}
	// echo "<pre>";print_r($data_array);die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	?>
	<div style="width:500px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" width="100%" class="rpt_table">
            <thead>
				<th width="30">SL</th>
                <th width="100">Transfer ID</th>
                <th width="70">YEAR</th>
                <th width="100">Challan No</th>
                <th width="100">Company</th>
				<th width="100">Transfer Date</th>

            </thead>
     	</table>
     </div>
	 <div style="width:500px; max-height:240px;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $data_array as $sys_id=>$row )
            {

						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $sys_id;?>_<? echo $row["sys_number"];?>_<? echo $row["delivery_date"];?>_<? echo $row["challan_no"];?>_<? echo $row["transfer_criteria"];?>');" >
								<td width="30" align="center"><?php echo $i; ?></td>
								<td width="100" align="center"><?=$row['sys_number_prefix_num']; ?></td>
								<td width="70"><?=$row['year']?></td>
								<td width="100"><?=$row['challan_no']?></td>
								<td width="100"><?=$company_arr[$row['company_id']]?></td>
        						<td width="100"><?=$row['delivery_date']?></td>

							</tr>
						<?
						$i++;
					}
			?>
        </table>
    </div>
	 <?




	// echo create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date", "80,70,100,110","550","250",0, $sql, "js_set_value", "id,sys_number,delivery_date,challan_no,transfer_criteria", "", 1, "0,0,0,company_id,0", $arr, "sys_number_prefix_num,year,challan_no,company_id,delivery_date", '','','0,0,0,0,3');

	// exit();
}
?>