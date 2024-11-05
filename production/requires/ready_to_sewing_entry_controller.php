<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');
include('../../includes/class4/class.conditions.php');
include('../../includes/class4/class.reports.php');
include('../../includes/class4/class.trims.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//$_SESSION['page_permission']=$permission;

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select sewing_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("sewing_production")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}	
 	exit();
}

if($action=="trims_popup")
{
	echo load_html_head_contents("Trims Info", "../../", 1, 1,$unicode,0,0);
	extract($_REQUEST);
	//echo $permission;die;
	$_SESSION['page_permission']=$permission;
	$update_id=str_replace("'","",$update_id);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$tot_seq_qnty=str_replace("'","",$tot_seq_qnty);
	/*echo "select a.job_no, a.company_name, a.location_name, a.buyer_name, a.dealing_marchant, b.id as po_id, b.po_number, b.shipment_date, b.po_quantity, c.id as req_id, c.ready_sewing_date, sum(d.sewing_qnty) as sewing_qnty 
	from wo_po_details_master a, wo_po_break_down b, ready_to_sewing_mst c, ready_to_sewing_dtls d 
	where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.id=d.mst_id and c.id=$update_id and c.po_break_down_id=$txt_order_id and c.status_active=1 and d.status_active=1
	group by a.job_no, a.company_name, a.location_name, a.buyer_name, a.dealing_marchant, b.id, b.po_number, b.shipment_date, b.po_quantity, c.id, c.ready_sewing_date";*/
	$mst_sql=sql_select("select a.job_no, a.company_name, a.location_name, a.buyer_name, a.dealing_marchant, b.id as po_id, b.po_number, b.shipment_date, b.po_quantity, c.id as req_id, c.ready_sewing_date, sum(d.sewing_qnty) as sewing_qnty 
	from wo_po_details_master a, wo_po_break_down b, ready_to_sewing_mst c, ready_to_sewing_dtls d 
	where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.id=d.mst_id and c.id=$update_id and c.po_break_down_id=$txt_order_id and c.status_active=1 and d.status_active=1
	group by a.job_no, a.company_name, a.location_name, a.buyer_name, a.dealing_marchant, b.id, b.po_number, b.shipment_date, b.po_quantity, c.id, c.ready_sewing_date");
	//echo $mst_sql;die;
?> 
	<script>
		function js_set_value() 
		{
			//$('#hidden_data').val( str );
			parent.emailwindow.hide();
		}
		
		function fnc_ready_to_sewing_reqsn(operation)
		{
			//alert(operation)
			var tot_row=$('#tbl_body tbody tr').length-1;
			if(tot_row<1) { alert("Trims Cost Not Found."); return;}
			var dataStr=""; var j=0;
			for (var i=1; i<=tot_row; i++)
			{
				var hidddata=$('#hidddata_'+i).val();
				var reqQnty=$('#reqQnty_'+i).val();
				j++;
				dataStr+='&hidddata'+j+'='+hidddata+'&reqQnty'+j+'='+reqQnty;
			}
			var hidddta=$('#hiddmstdata').val();
			//alert(hidddta);return;
			var data="action=save_update_delete_reqsn&operation="+operation+'&row_num='+j+get_submitted_data_string('hiddmstdata', "../../")+dataStr;
			//alert(data);return;
			freeze_window(operation);
			
			http.open("POST","ready_to_sewing_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_ready_to_sewing_reqsn_response;
		}
		
		function fnc_ready_to_sewing_reqsn_response()
		{
			if(http.readyState == 4) 
			{
				//alert(http.responseText);release_freezing();return;
				var response=trim(http.responseText).split('**');
				show_msg(response[0]);
				if (response[0]==0) alert("Data is Save Successfully");
				else if (response[0]==1) alert("Data is Update Successfully");
				release_freezing();
			}
		}
    </script>

</head>

<body onLoad="set_hotkey();">
<div align="center" style="width:900px;">
	<div style="display:none"><? echo load_freeze_divs ("../../",$permission); ?></div>
    <fieldset style="width:900px;">
    <legend>Trims Issue Requisition</legend>           
        <table cellpadding="0" cellspacing="1" width="900" >
            <tr>
                <td align="right">Company &nbsp;<input type="hidden" id="hiddmstdata" name="hiddmstdata" value="<? echo $update_id.'***'.$txt_order_id; ?>" ></td>
                <td>
                <? 
                    echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where id=".$mst_sql[0][csf("company_name")]."","id,company_name", 1, "-- Select Company --", $mst_sql[0][csf("company_name")], "",0 );
                ?>
                </td>
                <td align="right">Location &nbsp;</td>
                <td colspan="3">
                <? 
                    echo create_drop_down( "cbo_location", 140, "select id, location_name from lib_location where id=".$mst_sql[0][csf("location_name")]." ","id,location_name", 1, "-- Select Location --", $mst_sql[0][csf("location_name")], "",0 );
                ?>
                </td>
            </tr>
            <tr>
                <td width="100" align="right">Buyer &nbsp;</td>
                <td width="140">
                <?
                    echo create_drop_down( "cbo_buyer_name", 140, "select id, buyer_name from lib_buyer  where id=".$mst_sql[0][csf("buyer_name")]." ","id,buyer_name", 1, "-- All Buyer --", $mst_sql[0][csf("buyer_name")], "","" ); 
                ?>
                </td>
                <td width="100" align="right">Reqsn. No &nbsp;</td>
                <td width="150"><input type="text" id="txt_req_no" name="txt_req_no" class="text_boxes" style="width:138px;" value="<? echo $mst_sql[0][csf("req_id")];?>" disabled ></td>
                <td width="100"  align="right" >Reqsn. Date &nbsp;</td>
                <td ><input type="text" id="txt_req_date" name="txt_req_date" class="datepicker" style="width:120px;" value="<? if($mst_sql[0][csf("ready_sewing_date")]!="" && $mst_sql[0][csf("req_id")]!="0000-00-00") echo change_date_format($mst_sql[0][csf("ready_sewing_date")]);?>" disabled ></td>
                
            </tr>
            <tr>
                <td  align="right">Job No &nbsp;</td>
                <td><input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:138px;" value="<? echo $mst_sql[0][csf("job_no")];?>" disabled ></td>
                <td align="right">Order No &nbsp;</td>
                <td><input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:138px;" value="<? echo $mst_sql[0][csf("po_number")];?>" disabled ></td>
                <td align="right" >Dealing Merchant &nbsp;</td>
                <td>
                <?
                    echo create_drop_down( "cbo_dealing_merchant", 122, "select id,team_member_name from lib_mkt_team_member_info where id=".$mst_sql[0][csf("dealing_marchant")],"id,team_member_name", 1, "-- Select Team Member --", $mst_sql[0][csf("dealing_marchant")], "" );
                ?>
                </td>
            </tr>
            <tr>
                <td  align="right">Ship Date &nbsp;</td>
                <td><input type="text" id="txt_ship_date" name="txt_ship_date" class="datepicker" style="width:138px;" value="<? if($mst_sql[0][csf("shipment_date")]!="" && $mst_sql[0][csf("req_id")]!="0000-00-00") echo change_date_format($mst_sql[0][csf("shipment_date")]);?>" disabled ></td>
                <td align="right">Order Qty (Pcs)&nbsp;</td>
                <td><input type="text" id="txt_req_no" name="txt_req_no" class="text_boxes_numeric" style="width:138px;" value="<? echo number_format($mst_sql[0][csf("po_quantity")],0);?>" disabled ></td>
                <td align="right" >Ready to Sew Qty&nbsp;</td>
                <td><input type="text" id="txt_req_no" name="txt_req_no" class="text_boxes_numeric" style="width:120px;" value="<? echo number_format($mst_sql[0][csf("sewing_qnty")],0);?>" disabled ></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="150">Order No</th>
                <th width="60">Product Id</th>
                <th width="120">Trims Name</th>
                <th width="150">Trims Description</th>                
                <th width="100">Brand/Sup Ref</th>
                <th width="60">Cons UOM</th>
                <th width="70">Stock Qnty</th>
                <th>Req. Qnty</th>
            </thead>
        </table>
        <div style="width:900px; max-height:230px; overflow-y:scroll" id="list_container_batch">	 
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="882" class="rpt_table" id="tbl_body">  
				<?
                /*$lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name", "id", "item_name");
				
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
				$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
				$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
				
				$dtls_data_arr=array(); $itme_str=""; $color_str=""; $country_str="";
				$sql_dtls_data=sql_select("select id, garments_itme_id, color_id, country_id from ready_to_sewing_dtls where mst_id=$update_id and status_active=1 and status_active=1");
				foreach($sql_dtls_data as $row)
				{
					if($itme_str=="") $itme_str=$row[csf("garments_itme_id")]; else $itme_str.=','.$row[csf("garments_itme_id")]; 
					if($color_str=="") $color_str=$row[csf("color_id")]; else $color_str.=','.$row[csf("color_id")]; 
					if($country_str=="") $country_str=$row[csf("country_id")]; else $country_str.=','.$row[csf("country_id")];
				}
                unset($sql_dtls_data);
				
				$rqsn_dtls_arr=array();
				$sql_rqsn="select precost_trim_dtls_id, size_sensivity from ready_to_sewing_reqsn where mst_id=$update_id and po_id='$txt_order_id' group by precost_trim_dtls_id, size_sensivity";
				$sql_rqsn_res=sql_select($sql_rqsn);
				$count_rqsn=count($sql_rqsn_res);
				foreach($sql_rqsn_res as $rrow)
				{
					$rqsn_dtls_arr[$rrow[csf("precost_trim_dtls_id")]]=$rrow[csf("size_sensivity")];
				}
				unset($sql_rqsn_res);
				//$field_arr="id, mst_id, po_id, dtls_id, color_id, size_id, precost_trim_dtls_id, size_sensivity, trim_group, cons_uom, size_input_qty, cons, req_bom, trim_iss, reqsn_qty, inserted_by, insert_date, status_active, is_deleted";
				
				$po_qty_arr=array(); 
				$po_sql="select id, po_break_down_id, country_id, item_number_id, color_number_id, size_number_id, order_quantity from wo_po_color_size_breakdown where is_deleted=0 and status_active=1 and po_break_down_id='$txt_order_id' and country_id in ($country_str) and item_number_id in ($itme_str) and color_number_id in ($color_str)";
				$po_sql_res=sql_select($po_sql);
				foreach($po_sql_res as $row)
				{
					$po_qty_arr[$row[csf("id")]]['country_id']=$row[csf("country_id")];
					$po_qty_arr[$row[csf("id")]]['item_number_id']=$row[csf("item_number_id")];
					$po_qty_arr[$row[csf("id")]]['color_number_id']=$row[csf("color_number_id")];
					$po_qty_arr[$row[csf("id")]]['size_number_id']=$row[csf("size_number_id")];
					$country_po.=$row[csf("country_id")].',';
				}
				unset($po_sql_res);
				$country_po=implode(",",array_unique(explode(",",$country_po)));
                $country_po=chop($country_po,',');
                $trim_issue_data=sql_select("select b.po_breakdown_id, a.item_group_id, b.quantity from inv_trims_issue_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id=$txt_order_id and a.status_active=1 and b.status_active=1 and b.trans_type in(2,3)");
                foreach($trim_issue_data as $row)
                {
                    $trim_issue_data_arr[$row[csf("po_breakdown_id")]][$row[csf("item_group_id")]]+=$row[csf("quantity")];
                }
				unset($trim_issue_data);
				
                // $req_sql="select a.id, a.mst_id, a.po_id, a.color_size_table_id, a.precost_trim_dtls_id, a.cons_uom, a.cons, a.req_bom, a.trim_iss, a.reqsn_qty, b.item_name, b.trim_type from ready_to_sewing_reqsn a, lib_item_group b where a.trim_group=b.id and a.mst_id=$update_id order by b.trim_type ASC";
			    $trims_sql="select d.prod_id, a.id as po_id, b.seq, b.id as pre_id, b.trim_group, b.description, b.brand_sup_ref, b.cons_dzn_gmts, b.cons_uom, b.country, b.nominated_supp,
				sum(case when d.TRANS_TYPE in(1) then d.QUANTITY else 0 end ) as RCV_QNTY,
				sum((case when d.TRANS_TYPE in(1,4,5) then d.QUANTITY else 0 end )-(case when d.TRANS_TYPE in(2,3,6) then d.QUANTITY else 0 end )) as STOCK_QNTY 
				from order_wise_pro_details d, wo_po_break_down a, wo_pre_cost_trim_cost_dtls b, lib_item_group c 
				where d.PO_BREAKDOWN_ID=a.id and a.job_no_mst=b.job_no and b.trim_group=c.id and c.trim_type=1 and c.item_category=4 and a.id=$txt_order_id and b.status_active=1 and b.is_deleted=0 and d.entry_form in(24,25,49,73,78) and d.status_active=1 and d.is_deleted=0
				group by d.prod_id, a.id, b.seq, b.id, b.trim_group, b.description, b.brand_sup_ref, b.cons_dzn_gmts, b.cons_uom, b.country, b.nominated_supp";*///$cost_trim_dtls_id_cond
				//, (a.TOTAL_SET_QNTY*b.PO_QUANTITY) as PO_QNTY_IN_PCS
				$sql_rqsn="select precost_trim_dtls_id, size_sensivity from ready_to_sewing_reqsn where mst_id=$update_id and po_id='$txt_order_id' group by precost_trim_dtls_id, size_sensivity";
				$sql_rqsn_res=sql_select($sql_rqsn);
				$count_rqsn=count($sql_rqsn_res);
				foreach($sql_rqsn_res as $rrow)
				{
					$rqsn_dtls_arr[$rrow[csf("precost_trim_dtls_id")]]=$rrow[csf("size_sensivity")];
				}
				$pre_cons_sql=sql_select("select COSTING_PER from wo_pre_cost_mst a, wo_pre_cost_trim_co_cons_dtls b where a.JOB_NO=b.JOB_NO and b.PO_BREAK_DOWN_ID=$txt_order_id");
				$pre_cons_per=$pre_cons_sql[0]["COSTING_PER"];
				//echo $pre_cons_per.test;die;
				$cos_per_pcs_arr=array(1=>12,2=>1,3=>24,4=>36,5=>48);
				$cons_uom_qnty=$cos_per_pcs_arr[$pre_cons_per];
				//echo $cons_uom_qnty."=".$tot_seq_qnty;
				$trims_sql="SELECT b.ID as PO_ID, b.PO_NUMBER, b.PO_QUANTITY, d.ID as PRODUCT_ID, d.ITEM_GROUP_ID, d.ITEM_DESCRIPTION, d.ITEM_COLOR, d.ITEM_SIZE, d.BRAND_SUPPLIER, a.CONS_UOM, a.CONS_DZN_GMTS, a.ID as PRE_DTLS_ID, e.ITEM_NAME,
				sum(case when c.TRANS_TYPE in(1) then c.QUANTITY else 0 end ) as RCV_QNTY, 
				sum((case when c.TRANS_TYPE in(1,4,5) then c.QUANTITY else 0 end )-(case when c.TRANS_TYPE in(2,3,6) then c.QUANTITY else 0 end )) as STOCK_QNTY
				from wo_pre_cost_trim_cost_dtls a, wo_po_break_down b, order_wise_pro_details c, product_details_master d, lib_item_group e 
				where a.job_no=b.job_no_mst and b.id=c.PO_BREAKDOWN_ID and c.prod_id=d.id and d.ITEM_GROUP_ID=e.id and a.TRIM_GROUP=d.ITEM_GROUP_ID and e.trim_type=1 and e.item_category=4 and c.entry_form in(24,25,49,73,78) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.id = $txt_order_id
				group by b.ID, b.PO_NUMBER, b.PO_QUANTITY, d.ID, d.ITEM_GROUP_ID, d.ITEM_DESCRIPTION, d.ITEM_COLOR, d.ITEM_SIZE, d.BRAND_SUPPLIER, a.CONS_UOM, a.CONS_DZN_GMTS, a.ID, e.ITEM_NAME";
                //echo $trims_sql;
				$trim_item_data_arr=array();
                $trim_sql_result=sql_select($trims_sql); 
				$i=1;
                foreach ($trim_sql_result as $tval)
                {
                    //$cons_per_dzn=(($trims_costing_arr_data[$row[csf("po_id")]][$row[csf("pre_id")]]/$order_qnty)*12);
					$cons_per_dzn=$tval["cons_dzn"];
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$req_qnty=$tval['CONS_DZN_GMTS']/$cons_uom_qnty*$tot_seq_qnty;
					$str_data="";
					$str_data=$tval['PRE_DTLS_ID'].'***'.$tval['ITEM_GROUP_ID'].'***'.$tval['CONS_UOM'].'***'.$tval['PRODUCT_ID'].'***'.$tval['STOCK_QNTY'];
					
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>"> 
                        <td width="30" align="center"> <? echo $i; ?><input type="hidden" id="hidddata_<? echo $i; ?>" name="hidddata[]" value="<? echo $str_data; ?>" ></td>
                        <td width="150"><p><? echo $tval['PO_NUMBER']; ?></p></td>
                        <td width="60" align="center"><p><? echo $tval['PRODUCT_ID']; ?></p></td>
                        <td width="120"><p><? echo $tval['ITEM_NAME']; ?></p></td>
                        <td width="150"><p><? echo $tval['ITEM_DESCRIPTION']; ?></p></td>
                        <td width="100"><p><? echo $tval['BRAND_SUPPLIER']; ?></p></td>
                        <td width="60" align="center"><p><? echo $unit_of_measurement[$tval['CONS_UOM']]; ?></p></td>
                        <td width="70" align="right"><? echo number_format($tval['STOCK_QNTY'],2); ?></td>
                        <td align="center"><input type="text" id="reqQnty_<? echo $i; ?>" name="reqQnty[]" class="text_boxes_numeric" style="width:100px;" value="<? echo number_format($req_qnty,2,'.',''); ?>" placeholder="<? echo number_format($req_qnty,2,'.',''); ?>" /></td>
                    </tr>
                    <?
                    $i++;
                }
				if($count_rqsn>0) $sup_mode=1; else $sup_mode=0;
                ?>
             </table>
             </div>
             <table cellspacing="0" cellpadding="0" width="900">  
             	<tr>
                	<td colspan="10" align="center" class="button_container"><? echo load_submit_buttons( $_SESSION['page_permission'], "fnc_ready_to_sewing_reqsn", $sup_mode,0,"reset_form('','','','','')",1);
            ?>
                </tr>
                <tr>
                	<td colspan="10" align="center"><input type="button" id="btn_close" value="Close" class="formbutton" style="width:100px;" onClick="js_set_value();" ></td>
                </tr>
            </table>
        <script>setFilterGrid('tbl_body',-1); </script>
    </fieldset>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="save_update_delete_reqsn")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here
	{
		//echo "10**";
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		
		$exmst_data=explode("***",str_replace("'","",$hiddmstdata));
		$mst_id=$exmst_data[0];
		$po_id=$exmst_data[1];
		
		/*$po_qty_arr=array(); $pocolor_arr=array(); $colorpoqty_arr=array();
		$po_sql="select id, country_id, item_number_id, color_number_id, size_number_id, order_quantity from wo_po_color_size_breakdown where is_deleted=0 and status_active=1 and po_break_down_id=$po_id";
		$po_sql_res=sql_select($po_sql);
		foreach($po_sql_res as $row)
		{
			$po_qty_arr[$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['size_qty']=$row[csf("order_quantity")];
			$po_qty_arr[$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['row_id']=$row[csf("id")];
			$colorpoqty_arr[$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]['color_qty']=$row[csf("order_quantity")];
			$pocolor_arr[$row[csf("id")]]['qty']+=$row[csf("order_quantity")];
			$pocolor_arr[$row[csf("id")]]['size']=$row[csf("size_number_id")];
		}
		unset($po_sql_res);
		
		$trim_cons_arr=array();
		$trim_dtls_sql=sql_select("select wo_pre_cost_trim_cost_dtls_id, country_id, item_number_id, color_number_id, tot_cons, color_size_table_id, size_number_id from wo_pre_cost_trim_co_cons_dtls where po_break_down_id='$po_id'");
		foreach($trim_dtls_sql as $row)
		{
			$trim_cons_arr[$row[csf("wo_pre_cost_trim_cost_dtls_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]].=$row[csf("tot_cons")].'___'.$row[csf("size_number_id")].',';
		}
		unset($trim_dtls_sql);
		
		$sql_sew="select a.po_quantity, b.po_break_down_id, c.country_id, c.garments_itme_id, c.color_id, c.sewing_qnty, c.id from wo_po_break_down a, ready_to_sewing_mst b, ready_to_sewing_dtls c where a.id=b.po_break_down_id and b.id=c.mst_id and b.id='$mst_id' and a.id='$po_id' and a.status_active=1 and a.status_active=1 and b.status_active=1 and b.status_active=1 and c.status_active=1 and c.status_active=1 ";
		$sql_sew_res=sql_select($sql_sew);
		$colsize_arr=array(); $po_qty=0;
		foreach($sql_sew_res as $row)
		{
			$po_qty=$row[csf("po_quantity")];
			$colsize_arr[$row[csf("id")]]['sewing_qnty']=$row[csf("sewing_qnty")];
			$colsize_arr[$row[csf("id")]]['item_id']=$row[csf("garments_itme_id")];
			$colsize_arr[$row[csf("id")]]['color_id']=$row[csf("color_id")];
		}
		unset($sql_sew_res);*/
		
		execute_query( "delete from ready_to_sewing_reqsn where mst_id ='$mst_id'",1);
		//$field_arr="id, mst_id, po_id, dtls_id, color_id, size_id, precost_trim_dtls_id, trim_group, cons_uom, size_input_qty, cons, req_bom, trim_iss, reqsn_qty, product_id, stock_qnty, inserted_by, insert_date, status_active, is_deleted";
		$field_arr="id, mst_id, po_id, entry_form, precost_trim_dtls_id, trim_group, cons_uom, reqsn_qty, product_id, stock_qnty, inserted_by, insert_date, status_active, is_deleted";
		$idreq=return_next_id( "id", "ready_to_sewing_reqsn", 1);
		$q=1; $data_arr_req="";
		for ($i=1; $i<=$row_num; $i++)
		{
			//$str_data=$tval['PRE_DTLS_ID'].'***'.$tval['ITEM_GROUP_ID'].'***'.$tval['CONS_UOM'].'***'.$tval['PRODUCT_ID'].'***'.$tval['STOCK_QNTY'];
			//dataStr+='&hidddata'+j+'='+hidddata+'&reqQnty'+j+'='+reqQnty;
			$hidddata="hidddata".$i;
			$reqQnty="reqQnty".$i;
			$ex_data=explode("***",$$hidddata);
			$pre_id=$ex_data[0];
			$trim_group=$ex_data[1];
			$cons_uom=$ex_data[2];
			$prod_id=$ex_data[3];
			$stock_qnty=$ex_data[4];
			$country_data=explode(",",$ex_data[4]);
			$trim_issue=$trim_issue_data_arr[$po_id][$trim_group];
			if($data_arr_req!="") $data_arr_req.=",";
			$data_arr_req.="(".$idreq.",'".$mst_id."','".$po_id."',377,'".$pre_id."','".$trim_group."','".$cons_uom."','".str_replace("'","",$$reqQnty)."','".$prod_id."','".$stock_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$idreq = $idreq+1;
		}
		
		/*for ($i=1; $i<=$row_num; $i++)
		{
			//$str_data=$tval['PRE_DTLS_ID'].'***'.$tval['ITEM_GROUP_ID'].'***'.$tval['CONS_UOM'].'***'.$tval['PRODUCT_ID'].'***'.$tval['STOCK_QNTY'];
			//dataStr+='&hidddata'+j+'='+hidddata+'&reqQnty'+j+'='+reqQnty;
			$hidddata="hidddata".$i;
			$reqQnty="reqQnty".$i;
			$ex_data=explode("***",$$hidddata);
			$pre_id=$ex_data[0];
			$trim_group=$ex_data[1];
			$cons_uom=$ex_data[2];
			$prod_id=$ex_data[3];
			$stock_qnty=$ex_data[4];
			$country_data=explode(",",$ex_data[4]);
			$trim_issue=$trim_issue_data_arr[$po_id][$trim_group];
			if(str_replace("'","",$$cbosizesen)==2)
			{
				foreach($colsize_arr as $dtlsid=>$val)
				{
					$ready_to_sewing=$val['sewing_qnty'];
					$color_id=$val['color_id'];
					$item_id=$val['item_id'];
					foreach($country_data as $country)
					{
						$budget_size=chop($trim_cons_arr[$pre_id][$country][$itme_id][$color_id],",");
						$pre_size_data=explode(",",$budget_size);
						foreach($pre_size_data as $budget_val)
						{
							$posize_qty=$sizeready_to_sewing=$reqsn_qty=$reqire_bom=$color_qty=0; 
							$ex_budget=explode("___",$budget_val);
							$cons=$ex_budget[0];
							$gmt_size=$ex_budget[1];
							
							$posize_qty=$po_qty_arr[$country][$item_id][$color_id][$gmt_size]['size_qty'];
							$color_qty=$colorpoqty_arr[$country][$item_id][$color_id]['color_qty'];
							$sizeready_to_sewing=($ready_to_sewing/$color_qty)*$posize_qty;
							$reqsn_qty=($cons/12)*$sizeready_to_sewing;
							$reqire_bom=($cons/12)*$posize_qty;
							
							if($data_arr_req!="") $data_arr_req.=",";
							$data_arr_req.="(".$idreq.",'".$mst_id."','".$po_id."','".$dtlsid."','".$color_id."','".$gmt_size."','".$pre_id."','".$trim_group."','".$cons_uom."','".$sizeready_to_sewing."','".$cons."','".$reqire_bom."','".$trim_issue."','".str_replace("'","",$$reqQnty)."','".$prod_id."','".$stock_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
							//$data_arr_req.="(".$sizeready_to_sewing.",'".$ready_to_sewing."','".$color_qty."','".$posize_qty."',1,0)";
							$idreq = $idreq+1;
							$q++;
						}
					}
				}
			}
			else
			{
				$cons=$ex_data[3];
				foreach($colsize_arr as $dtlsid=>$val)
				{
					$ready_to_sewing=$val['sewing_qnty'];
					$color_id=$val['color_id'];
					$item_id=$val['item_id'];
					foreach($country_data as $country)
					{
						$color_qty=0;
						$color_qty=$colorpoqty_arr[$country][$item_id][$color_id]['color_qty'];//$pocolor_arr[$country][$item_id][$color_id]['color_qty'];
						
						$reqsn_qty=($cons/12)*$ready_to_sewing;
						$reqire_bom=($cons/12)*$color_qty;
						
						//$sizeready_to_sewing=($ready_to_sewing/$color_qty)*$sizeval['size_qty'];
						if($data_arr_req!="") $data_arr_req.=",";
						$data_arr_req.="(".$idreq.",'".$mst_id."','".$po_id."','".$dtlsid."','".$color_id."','0','".$pre_id."',".$$cbosizesen.",'".$trim_group."','".$cons_uom."','0','".$cons."','".$reqire_bom."','".$trim_issue."','".str_replace("'","",$$reqQnty)."','".$prod_id."','".$stock_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$idreq = $idreq+1;
						$q++;
					}
				}
			}
		}*/
		//echo '10**'.$data_arr_req; die;
		//echo "5**insert into ready_to_sewing_reqsn ($field_arr) values $data_arr_req";die;
		$rID=sql_insert("ready_to_sewing_reqsn",$field_arr,$data_arr_req,1);

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "0**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // update Here
	{
		//echo "10**";
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$idreq=return_next_id( "id", "ready_to_sewing_reqsn", 1);
		
		$exmst_data=explode("***",str_replace("'","",$hiddmstdata));
		$mst_id=$exmst_data[0];
		$po_id=$exmst_data[1];
		
		/*$po_qty_arr=array(); $pocolor_arr=array(); $colorpoqty_arr=array();
		$po_sql="select id, country_id, item_number_id, color_number_id, size_number_id, order_quantity from wo_po_color_size_breakdown where is_deleted=0 and status_active=1 and po_break_down_id=$po_id";
		$po_sql_res=sql_select($po_sql);
		foreach($po_sql_res as $row)
		{
			$po_qty_arr[$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['size_qty']+=$row[csf("order_quantity")];
			$po_qty_arr[$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['row_id']=$row[csf("id")];
			$colorpoqty_arr[$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]['color_qty']+=$row[csf("order_quantity")];
			$pocolor_arr[$row[csf("id")]]['qty']+=$row[csf("order_quantity")];
			$pocolor_arr[$row[csf("id")]]['size']=$row[csf("size_number_id")];
		}
		unset($po_sql_res);
		//print_r($pocolor_arr); die;
		
		$trim_cons_arr=array();
		$trim_dtls_sql=sql_select("select wo_pre_cost_trim_cost_dtls_id, country_id, item_number_id, color_number_id, tot_cons, color_size_table_id, size_number_id from wo_pre_cost_trim_co_cons_dtls where po_break_down_id='$po_id'");
		foreach($trim_dtls_sql as $row)
		{
			$trim_cons_arr[$row[csf("wo_pre_cost_trim_cost_dtls_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]].=$row[csf("tot_cons")].'___'.$row[csf("size_number_id")].',';
		}
		unset($trim_dtls_sql);
		
		$sql_sew="select a.po_quantity, b.po_break_down_id, c.country_id, c.garments_itme_id, c.color_id, c.sewing_qnty, c.id from wo_po_break_down a, ready_to_sewing_mst b, ready_to_sewing_dtls c where a.id=b.po_break_down_id and b.id=c.mst_id and b.id='$mst_id' and a.id='$po_id' and a.status_active=1 and a.status_active=1 and b.status_active=1 and b.status_active=1 and c.status_active=1 and c.status_active=1 ";
		$sql_sew_res=sql_select($sql_sew);
		$colsize_arr=array(); $po_qty=0;
		foreach($sql_sew_res as $row)
		{
			$po_qty=$row[csf("po_quantity")];
			$colsize_arr[$row[csf("id")]]['sewing_qnty']=$row[csf("sewing_qnty")];
			$colsize_arr[$row[csf("id")]]['item_id']=$row[csf("garments_itme_id")];
			$colsize_arr[$row[csf("id")]]['color_id']=$row[csf("color_id")];
		}
		unset($sql_sew_res);*/
		
		execute_query( "delete from ready_to_sewing_reqsn where mst_id ='$mst_id'",1);
		
		$field_arr="id, mst_id, po_id, entry_form, precost_trim_dtls_id, trim_group, cons_uom, reqsn_qty, product_id, stock_qnty, inserted_by, insert_date, status_active, is_deleted";
		$idreq=return_next_id( "id", "ready_to_sewing_reqsn", 1);
		$q=1; $data_arr_req="";
		for ($i=1; $i<=$row_num; $i++)
		{
			$hidddata="hidddata".$i;
			$reqQnty="reqQnty".$i;
			$ex_data=explode("***",$$hidddata);
			$pre_id=$ex_data[0];
			$trim_group=$ex_data[1];
			$cons_uom=$ex_data[2];
			$prod_id=$ex_data[3];
			$stock_qnty=$ex_data[4];
			$country_data=explode(",",$ex_data[4]);
			$trim_issue=$trim_issue_data_arr[$po_id][$trim_group];
			if($data_arr_req!="") $data_arr_req.=",";
			$data_arr_req.="(".$idreq.",'".$mst_id."','".$po_id."',377,'".$pre_id."','".$trim_group."','".$cons_uom."','".str_replace("'","",$$reqQnty)."','".$prod_id."','".$stock_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$idreq = $idreq+1;
		}
		
		/*$field_arr="id, mst_id, po_id, dtls_id, color_id, size_id, precost_trim_dtls_id, size_sensivity, trim_group, cons_uom, size_input_qty, cons, req_bom, trim_iss, reqsn_qty, inserted_by, insert_date, status_active, is_deleted";
		
		$q=1; $data_arr_req="";
		for ($i=1; $i<=$row_num; $i++)
		{
			$hidddata="hidddata".$i;
			$cbosizesen="cbosizesen".$i;
			$ex_data=explode("***",$$hidddata);
			$pre_id=$ex_data[0];
			$trim_group=$ex_data[1];
			$cons_uom=$ex_data[2];
			$country_data=explode(",",$ex_data[4]);
			$trim_issue=$trim_issue_data_arr[$po_id][$trim_group];
			if(str_replace("'","",$$cbosizesen)==2)
			{
				foreach($colsize_arr as $dtlsid=>$val)
				{
					$ready_to_sewing=$val['sewing_qnty'];
					$color_id=$val['color_id'];
					$item_id=$val['item_id'];
					foreach($country_data as $country)
					{
						$budget_size=chop($trim_cons_arr[$pre_id][$country][$item_id][$color_id],",");
						$pre_size_data=explode(",",$budget_size);
						foreach($pre_size_data as $budget_val)
						{
							$posize_qty=$sizeready_to_sewing=$reqsn_qty=$reqire_bom=$color_qty=0; 
							$ex_budget=explode("___",$budget_val);
							$cons=$ex_budget[0];
							$gmt_size=$ex_budget[1];
							
							$posize_qty=$po_qty_arr[$country][$item_id][$color_id][$gmt_size]['size_qty'];
							$color_qty=$colorpoqty_arr[$country][$item_id][$color_id]['color_qty'];
							$sizeready_to_sewing=($ready_to_sewing/$color_qty)*$posize_qty;
							$reqsn_qty=($cons/12)*$sizeready_to_sewing;
							$reqire_bom=($cons/12)*$posize_qty;
							
							if($data_arr_req!="") $data_arr_req.=",";
							$data_arr_req.="(".$idreq.",'".$mst_id."','".$po_id."','".$dtlsid."','".$color_id."','".$gmt_size."','".$pre_id."',".$$cbosizesen.",'".$trim_group."','".$cons_uom."','".$sizeready_to_sewing."','".$cons."','".$reqire_bom."','".$trim_issue."','".$reqsn_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
							//$data_arr_req.="(".$sizeready_to_sewing.",'".$ready_to_sewing."','".$color_qty."','".$posize_qty."','".$color_id."','".$gmt_size."')";
							$idreq = $idreq+1;
							$q++;
						}
					}
				}
			}
			else
			{
				$cons=$ex_data[3];
				foreach($colsize_arr as $dtlsid=>$val)
				{
					$ready_to_sewing=$val['sewing_qnty'];
					$color_id=$val['color_id'];
					$item_id=$val['item_id'];
					foreach($country_data as $country)
					{
						$color_qty=0;
						$color_qty=$colorpoqty_arr[$country][$item_id][$color_id]['color_qty'];//$pocolor_arr[$country][$item_id][$color_id]['color_qty'];
						
						$reqsn_qty=($cons/12)*$ready_to_sewing;
						$reqire_bom=($cons/12)*$color_qty;
						
						//$sizeready_to_sewing=($ready_to_sewing/$color_qty)*$sizeval['size_qty'];
						if($data_arr_req!="") $data_arr_req.=",";
						$data_arr_req.="(".$idreq.",'".$mst_id."','".$po_id."','".$dtlsid."','".$color_id."','0','".$pre_id."',".$$cbosizesen.",'".$trim_group."','".$cons_uom."','0','".$cons."','".$reqire_bom."','".$trim_issue."','".$reqsn_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$idreq = $idreq+1;
						$q++;
					}
				}
			}
		}*/
		//echo '10**'.$data_arr_req; die;
		$rID=sql_insert("ready_to_sewing_reqsn",$field_arr,$data_arr_req,1);

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "1**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="po_popup")
{
	echo load_html_head_contents("Order Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_year=str_replace("'","",$cbo_year);
	?> 
	<script>
		function js_set_value( str) 
		{
			$('#hidden_data').val( str );
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:760px;">
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:760px;">
                <table cellpadding="0" cellspacing="0" border="1" rules="all" width="750" class="rpt_table">
                    <thead>
                        <th width="150">Buyer</th>
                        <th width="80">Job Year</th>
                        <th width="80">Job No</th>
                        <th width="80">Order No</th>
                        <th width="80">Style Ref</th>
                        <th colspan="2" width="130">Ship Date</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
                            <input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value=""> 
                        </th> 
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $cbo_buyer_name, "","" ); 
                            ?>       
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_year", 80, create_year_array(),"", 1,"-- All --", $cbo_year, "",0,"" );
                            ?>
                        </td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes_numeric" style="width:80px" /></td>
                        <td><input type="text" name="txt_order_no" id="txt_order_no" style="width:80px" class="text_boxes" /></td>
                        <td><input type="text" name="txt_style_ref" id="txt_style_ref" style="width:80px" class="text_boxes" /></td> 	
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td> 	
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td>					
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_ref').value, 'create_order_search_list_view', 'search_div', 'ready_to_sewing_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:70px;" />
                    </tr>
                    <tr>
                        <td align="center" height="40" colspan="8"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
                <div id="search_div"></div> 
            </fieldset>
        </form>
    </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_order_search_list_view")
{
	$data = explode("_",$data);
	
	$company_id =$data[0];
	$buyer_id =$data[1];
	$cbo_year=trim($data[2]);
	$job_no=trim($data[3]);
	$order_no=trim($data[4]);
	$from_date=trim($data[5]);
	$to_date=trim($data[6]);
	$style_ref=trim($data[7]);
	
	if($buyer_id==0)
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
		$buyer_id_cond=" and a.buyer_name=$buyer_id";
	}
	
	$buyer_short_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	$sql_cond="";
	if($cbo_year!=0) 
	{
		if($db_type==0) $sql_cond.=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $sql_cond.=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}
	if($job_no!="") $sql_cond.=" and a.job_no_prefix_num=$job_no";
	if($order_no!="") $sql_cond.=" and b.po_number like '".$order_no."%'";
	if($style_ref!="") $sql_cond.=" and a.style_ref_no like '".$style_ref."%'";
	
	if($db_type==2) 
	{
		 $year="TO_CHAR(a.insert_date,'YYYY') as year ";
		 $null_cond="NVL";
	}
    else if($db_type==0) 
	{ 
		$year="year(a.insert_date) as year ";
		$null_cond="IFNULL";
	}
	
	$ship_date_cond="";
	
	if($db_type==0)
	{
		if($from_date!="" && $to_date!="") $ship_date_cond="and b.pub_shipment_date between '".change_date_format($from_date,"yyyy-mm-dd","-")."' and '".change_date_format($to_date,"yyyy-mm-dd", "-")."'"; else $ship_date_cond ="";
	}
	else if($db_type==2)
	{
		if($from_date!="" && $to_date!="") $ship_date_cond="and b.pub_shipment_date between '".change_date_format($from_date,"yyyy-mm-dd","-",1)."' and '".change_date_format($to_date,"yyyy-mm-dd","-",1)."'"; else $ship_date_cond ="";
	}
	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$sql = "SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, $year, b.id as po_id, b.po_number, b.po_quantity, b.pub_shipment_date, c.item_number_id as item_id, c.color_number_id as color_id, sum(c.order_quantity) as color_qnty
	from lib_item_group p, wo_pre_cost_trim_cost_dtls q, wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
	where p.id=q.trim_group and q.job_no=a.job_no and a.job_no=b.job_no_mst and b.id=c.po_break_down_id and p.item_category=4 and p.trim_type=1 and a.is_deleted=0 and a.status_active=1 and a.company_name=$company_id $buyer_id_cond $ship_date_cond $sql_cond
	group by a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.insert_date, b.id, b.po_number, b.po_quantity, b.pub_shipment_date, c.item_number_id, c.color_number_id
	order by b.id DESC";		  
	//echo $sql;
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="768" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="120">Buyer</th>
            <th width="50">Job Year</th>
			<th width="60">Job No</th>
			<th width="120">Order No</th>
			<th width="70">Style Ref</th>
            <th width="70">Ship Date</th>
            <th width="110">Gmts. Item</th>
            <th width="50">Color</th>
            <th>Qty</th>
		</thead>
	</table>
	<div style="width:768px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table" id="tbl_list_search" align="left">  
		<?
			$i=1;
			foreach ($result as $row)
			{  
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value('<? echo $row[csf("po_id")].'**'.$row[csf("po_number")].'**'.$row[csf("buyer_name")].'**'.$row[csf("job_no")];?>')"> 
					<td width="30"><? echo $i; ?></td>
					<td width="120"><p><? echo $buyer_short_name_arr[$row[csf('buyer_name')]]; ?></p></td>
					<td width="50" align="center"><? echo $row[csf('year')]; ?></td>
					<td width="60" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
					<td width="120"><p><? echo $row[csf('po_number')]; ?></p></td>
                    <td width="70"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                    <td width="70"><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
                    <td width="110"><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
                    <td width="50"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td align="right"><p><? echo number_format($row[csf('po_quantity')],0); ?></p></td>
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

if($action=="order_details_list")
{
	$data=explode("**",$data);
	if($data[1]>0)
	{
		$update_status=1;
		$job_no_ref=return_field_value( "job_no_mst","wo_po_break_down","id=".$data[0],"job_no_mst");
	} 
	else 
	{
		$update_status=0;
		$job_no_ref=$data[2];
	}
	
	$pp_meeting_date=return_field_value( "pp_meeting_date","wo_po_break_down","id=".$data[0],"pp_meeting_date");
	if($db_type==0)
	{
		$imge_location=return_field_value( "group_concat(image_location) as image_location","common_photo_library","form_name='knit_order_entry' and master_tble_id="."'".$job_no_ref."'","image_location");
	}
	else
	{
		$imge_location=return_field_value( "listagg(cast(image_location as varchar(4000)),',') within group(order by image_location)  as image_location","common_photo_library","form_name='knit_order_entry' and master_tble_id="."'".$job_no_ref."'","image_location");
	}
	
	$prod_data_array=sql_select("SELECT a.po_break_down_id, a.production_type, a.embel_name, a.country_id, c.item_number_id, c.color_number_id,c.size_number_id, b.production_qnty
							from  pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
							where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in (1,2,4,5,8)");
	$prod_data=array();
	$prod_data_size=array();
	foreach($prod_data_array as $row)
	{
		if($row[csf("embel_name")]=="") $row[csf("embel_name")]=0;
		$prod_data[$row[csf("po_break_down_id")]][$row[csf("production_type")]][$row[csf("embel_name")]][$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]]+=$row[csf("production_qnty")];

		$prod_data_size[$row[csf("po_break_down_id")]][$row[csf("production_type")]][$row[csf("embel_name")]][$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]+=$row[csf("production_qnty")];
	}
	
	$ready_to_sewing=sql_select("SELECT id as dtls_id, mst_id, po_break_down_id, country_id, garments_itme_id, color_id,size_id, sewing_qnty from ready_to_sewing_dtls where status_active=1 and is_deleted=0 and mst_id=$data[1]");
	$update_data=array();
	$update_data_size=array();
	foreach($ready_to_sewing as $row)
	{
		$update_data[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("garments_itme_id")]][$row[csf("color_id")]]["dtls_id"]=$row[csf("dtls_id")];
		$update_data[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("garments_itme_id")]][$row[csf("color_id")]]["sewing_qty"]+=$row[csf("sewing_qnty")];

		// =======================
		$update_data_size[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("garments_itme_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["dtls_id"]=$row[csf("dtls_id")];
		$update_data_size[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("garments_itme_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["sewing_qty"]+=$row[csf("sewing_qnty")];
	}
	//var_dump($update_data);
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');	

	$job_id=return_field_value( "job_id","wo_po_break_down","id=".$data[0],"job_id");
	$company_id=return_field_value( "company_name","wo_po_details_master","id=".$job_id,"company_name");

	$entry_break_down_type = return_field_value("sewing_production","variable_settings_production","company_name=$company_id and variable_list=1 and status_active=1","sewing_production");
	
	/*===============================================================================/
	/				generate entry break down with variable setting					 /
	/===============================================================================*/ 	
	if($entry_break_down_type==3) // color and size level
	{
		$sql = "SELECT a.id as job_id, a.job_no, b.id as po_id, b.po_number, b.po_quantity, c.country_id, c.item_number_id as item_id, c.color_number_id as color_id,c.size_number_id as size_id,c.size_order, sum(c.order_quantity) as color_qnty
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id=$data[0]
		group by a.id, a.job_no, b.id, b.po_number, b.po_quantity, c.country_id, c.item_number_id, c.color_number_id,c.size_number_id,c.size_order order by c.size_order";
		// echo $sql;
		?>
	    <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all" id="order_table">
	        <thead >
	            <tr>
	            	<th width="100">Country</th>
	                <th width="90">Gmts. Item</th>
	                <th width="90">Color</th>
	                <th width="60">Size</th>
	                <th width="60">Order Qty </th>
	                <th width="60">Cut Qty </th>
	                <th width="60">Sew. In Qty.</th>
	                <th width="60">Sew. Out Qty.</th>
	                <th>Ready To Sew</th>
	            </tr> 
	        </thead>
	        <tbody>
	        <?
			//echo $sql;die;
			$result=sql_select($sql);	
	        $i=1;
	        $color_qty=0;
	        foreach($result as $row)
	        {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$sewing_in_qty=$sewing_out_qty=$cut_qc_qnty=0; $dtls_id=''; $sewing_issue_qty="";
				$cut_qc_qnty=$prod_data_size[$row[csf("po_id")]][1][0][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]];
				$sewing_in_qty=$prod_data_size[$row[csf("po_id")]][4][0][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]];
				$sewing_out_qty=$prod_data_size[$row[csf("po_id")]][5][0][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]];
				$dtls_id=$update_data_size[$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["dtls_id"];
				$sewing_issue_qty=$update_data_size[$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["sewing_qty"];
	            ?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	            	<td><p><? echo $country_arr[$row[csf('country_id')]]; ?>&nbsp;<input type="hidden" id="hiddcountryid_<? echo $i; ?>" name="hiddcountryid[]" value="<? echo $row[csf('country_id')]; ?>" ></p></td>
	                <td><p><? echo $garments_item[$row[csf('item_id')]]; ?>&nbsp;<input type="hidden" id="gramentitem_<? echo $i; ?>" name="gramentitem[]" value="<? echo $row[csf('item_id')]; ?>" ></p></td>
	                <td><p><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;<input type="hidden" id="color_<? echo $i; ?>" name="color[]" value="<? echo  $row[csf('color_id')];  ?>" ></p></td>

	                <td><p><? echo $size_arr[$row[csf('size_id')]]; ?>&nbsp;<input type="hidden" id="size_<? echo $i; ?>" name="size[]" value="<? echo  $row[csf('size_id')];  ?>" ></p></td>

	                <td align="right" id="tdcolorqty_<? echo $i; ?>"><? echo number_format($row[csf('color_qnty')],0); ?>&nbsp;<input type="hidden" id="dtlsid_<? echo $i; ?>" name="dtlsid[]" value="<? echo $dtls_id;?>" ></td>
	                <td  align="right"><? if($cut_qc_qnty>0) echo number_format($cut_qc_qnty,0); else echo "&nbsp;"; ?>&nbsp;</td>
	                <td  align="right"><? if($sewing_in_qty>0) echo number_format($sewing_in_qty,0); else echo "&nbsp;"; ?>&nbsp;</td>
	                <td  align="right"><? if($sewing_out_qty>0) echo number_format($sewing_out_qty,0); else echo "&nbsp;"; ?>&nbsp;</td>
	                <td  align="center" id="td_<? echo $i; ?>"><input type="text" id="sewingissu_<? echo $i;  ?>" name="sewingissu[]" class="text_boxes_numeric" style="width:60px;" value="<? echo $sewing_issue_qty; ?>" ></td>
	            </tr>
	            <?       
	            $i++;
				
				$tot_order_qty+=$row[csf('color_qnty')];
				$tot_cut_qc_qnty+=$cut_qc_qnty;
				$tot_sewing_in_qty+=$sewing_in_qty;
				$tot_sewing_out_qty+=$sewing_out_qty;
				$tot_sewing_issue_qty+=$sewing_issue_qty;
	        }
	        ?>
	        </tbody>
	        <tfoot>
	        	<tr>
	            	<th>&nbsp;</th>
	            	<th>&nbsp;</th>
	            	<th>&nbsp;</th>
	                <th align="right">Total:</th>
	                <th align="right"><? echo number_format($tot_order_qty,0); ?>&nbsp;</th>
	                <th align="right"><? echo number_format($tot_cut_qc_qnty,0); ?>&nbsp;</th>
	                <th align="right"><? echo number_format($tot_sewing_in_qty,0); ?>&nbsp;</th>
	                <th align="right"><? echo number_format($tot_sewing_out_qty,0); ?>&nbsp;</th>
	                <th align="right" id="tot_seq_qnty"><? echo number_format($tot_sewing_issue_qty,0); ?>&nbsp;</th>
	            </tr>
	        </tfoot>
	    </table>
	     <table width="650" cellpadding="0" cellspacing="2" align="center">
	       <tr>
	           <td colspan="9" align="center" class="button_container">
	            <? 
	               echo load_submit_buttons( $_SESSION['page_permission'], "fnc_ready_to_sewing", $update_status,0,"reset_form('','','','','disable_enable_fields(\'cbo_company_id*cbo_buyer_name*txt_order_no*cbo_year\',0)')",1);
	            ?>
	            <input type="button" name="print" id="print" value="Print" onClick="fnc_ready_to_sewing_print(0)" style="width:70px;" class="formbutton">&nbsp;<input type="button" class="formbutton" style="width:150px;" value="Trims Issue Requisition" onClick="fn_trims_issue_req();" >
	            </td>
	      </tr>
	    </table> 
	    <br />
	    <fieldset style="width:656px;">
	    <legend>RMG Production Progress</legend>
	    <table cellpadding="0" cellspacing="0" width="655" class="rpt_table" border="1" rules="all" id="prod_prog">
	        <thead >
	            <tr>
	            	<th width="80">Country</th>
	                <th width="90">Gmts. Item</th>
	                <th width="70">Color</th>
	                <th width="55">Size</th>
	                <th width="60">Ord. Qty.</th>
	                <th width="50">Cut Qty</th>
	                <th width="50">Print Qty </th>
	                <th width="50">Emb. Qty</th>
	                <th width="50">Spl. Wrk. Qty.</th>
	                <th width="50">Sew. Qty.</th>
	                <th>Fin. Qty.</th>
	            </tr> 
	        </thead>
	        <tbody>
	        <?
			//echo $sql;die;
			$result=sql_select($sql);	
	        $i=1;
	        foreach($result as $row)
	        {
	            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$cutting_qty=0; $prin_qty=0; $emb_qty=0; $special_qty=0; $sewing_in_qty=0; $finish_qty=0;
				$cutting_qty=$prod_data_size[$row[csf("po_id")]][1][0][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]];
				$prin_qty=$prod_data_size[$row[csf("po_id")]][2][1][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]];
				$emb_qty=$prod_data_size[$row[csf("po_id")]][2][2][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]];
				$special_qty=$prod_data_size[$row[csf("po_id")]][2][4][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]];
				$sewing_out_qty=$prod_data_size[$row[csf("po_id")]][5][0][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]];
				$finish_qty=$prod_data_size[$row[csf("po_id")]][8][0][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]][$row[csf("size_id")]];
				
	            ?>
	            <tr bgcolor="<? echo $bgcolor;?>">
	            	<td><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
	                <td><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
	                <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
	                <td><p><? echo $size_arr[$row[csf('size_id')]]; ?></p></td>
	                <td align="right"><? echo number_format($row[csf('color_qnty')],0); ?></td>
	                <td align="right"><? if($cutting_qty>0) echo number_format($cutting_qty,0); else echo "&nbsp;"; ?></td>
	                <td align="right"><? if($prin_qty>0) echo number_format($prin_qty,0); else echo "&nbsp;"; ?></td>
	                <td align="right"><? if($emb_qty>0) echo number_format($emb_qty,0);else echo "&nbsp;"; ?></td>
	                <td align="right"><? if($special_qty>0) echo number_format($special_qty,0); else echo "&nbsp;"; ?></td>
	                <td align="right"><? if($sewing_out_qty>0) echo number_format($sewing_out_qty,0); else echo "&nbsp;"; ?></td>
	                <td align="right"><? if($finish_qty>0) echo number_format($finish_qty,0); else echo "&nbsp;"; ?></td>
	            </tr>
	            <?       
	            $i++;
				$tot_orderqty+=$row[csf('color_qnty')];
				$tot_cutting_qty+=$cutting_qty;
				$tot_prin_qty+=$prin_qty;
				$tot_emb_qty+=$emb_qty;
				$tot_special_qty+=$special_qty;
				$tot_sewing_outqty+=$sewing_out_qty;
				$tot_finish_qty+=$finish_qty;
	        }
	        ?>
	        </tbody>
	        <tfoot>
	        	<tr>
	            	<th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <th align="right">Total:</th>
	                <th align="right"><? echo $tot_orderqty; ?>&nbsp;</th>
	                <th align="right"><? echo $tot_cutting_qty; ?>&nbsp;</th>
	                <th align="right"><? echo $tot_prin_qty; ?>&nbsp;</th>
	                <th align="right"><? echo $tot_emb_qty; ?>&nbsp;</th>
	                <th align="right"><? echo $tot_special_qty; ?>&nbsp;</th>
	                <th align="right"><? echo $tot_sewing_outqty; ?>&nbsp;</th>
	                <th align="right"><? echo $tot_finish_qty; ?>&nbsp;</th>
	            </tr>
	        </tfoot>
	    </table>
	    <br>
	    <table cellpadding="0" cellspacing="0" width="330"  border="0">
	    	<tr>
	        	<td width="110">PP Meeting Done:</td>
	            <td width="70"><input type="text" id="pp_meeting_date" name="pp_meeting_date" class="datepicker" style="width:70px;" value="<? if($pp_meeting_date!="" && $pp_meeting_date!='0000-00-00') echo change_date_format($pp_meeting_date); ?>" disabled ></td>
	            <td onClick="openmypage_image('requires/ready_to_sewing_entry_controller.php?action=show_image&job_no=<? echo $job_no_ref; ?>', 'Image View')">
				<? $imge_location=array_unique(explode(",",$imge_location)); $all_image="";
					foreach($imge_location as $img_path)
					{
						$all_image.='&nbsp;<img src="../'.$img_path.'" height="40" width="50" />';
					}
					echo $all_image; ?></td>
	        </tr>
		</table>
	    </fieldset>
		<?
	}
	else // color level
	{
		$sql = "SELECT a.id as job_id, a.job_no, b.id as po_id, b.po_number, b.po_quantity, c.country_id, c.item_number_id as item_id, c.color_number_id as color_id, sum(c.order_quantity) as color_qnty
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
		where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id=$data[0]
		group by a.id, a.job_no, b.id, b.po_number, b.po_quantity, c.country_id, c.item_number_id, c.color_number_id";
		//echo $sql;
		?>
	    <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all" id="order_table">
	        <thead >
	            <tr>
	            	<th width="100">Country</th>
	                <th width="90">Gmts. Item</th>
	                <th width="90">Color</th>
	                <th width="70">Order Qty </th>
	                <th width="70">Cut Qty </th>
	                <th width="70">Sew. In Qty.</th>
	                <th width="70">Sew. Out Qty.</th>
	                <th>Ready To Sew</th>
	            </tr> 
	        </thead>
	        <tbody>
	        <?
			//echo $sql;die;
			$result=sql_select($sql);	
	        $i=1;
	        $color_qty=0;
	        foreach($result as $row)
	        {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$sewing_in_qty=$sewing_out_qty=$cut_qc_qnty=0; $dtls_id=''; $sewing_issue_qty="";
				$cut_qc_qnty=$prod_data[$row[csf("po_id")]][1][0][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]];
				$sewing_in_qty=$prod_data[$row[csf("po_id")]][4][0][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]];
				$sewing_out_qty=$prod_data[$row[csf("po_id")]][5][0][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]];
				$dtls_id=$update_data[$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]]["dtls_id"];
				$sewing_issue_qty=$update_data[$row[csf("po_id")]][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]]["sewing_qty"];
	            ?>
	            <tr bgcolor="<? echo $bgcolor; ?>">
	            	<td><p><? echo $country_arr[$row[csf('country_id')]]; ?>&nbsp;<input type="hidden" id="hiddcountryid_<? echo $i; ?>" name="hiddcountryid[]" value="<? echo $row[csf('country_id')]; ?>" ></p></td>
	                <td><p><? echo $garments_item[$row[csf('item_id')]]; ?>&nbsp;<input type="hidden" id="gramentitem_<? echo $i; ?>" name="gramentitem[]" value="<? echo $row[csf('item_id')]; ?>" ></p></td>
	                <td><p><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;<input type="hidden" id="color_<? echo $i; ?>" name="color[]" value="<? echo  $row[csf('color_id')];  ?>" ></p></td>
	                <td align="right" id="tdcolorqty_<? echo $i; ?>"><? echo number_format($row[csf('color_qnty')],0); ?>&nbsp;<input type="hidden" id="dtlsid_<? echo $i; ?>" name="dtlsid[]" value="<? echo $dtls_id;?>" ></td>
	                <td  align="right"><? if($cut_qc_qnty>0) echo number_format($cut_qc_qnty,0); else echo "&nbsp;"; ?>&nbsp;</td>
	                <td  align="right"><? if($sewing_in_qty>0) echo number_format($sewing_in_qty,0); else echo "&nbsp;"; ?>&nbsp;</td>
	                <td  align="right"><? if($sewing_out_qty>0) echo number_format($sewing_out_qty,0); else echo "&nbsp;"; ?>&nbsp;</td>
	                <td  align="center" id="td_<? echo $i; ?>"><input type="text" id="sewingissu_<? echo $i;  ?>" name="sewingissu[]" class="text_boxes_numeric" style="width:60px;" value="<? echo $sewing_issue_qty; ?>" ></td>
	            </tr>
	            <?       
	            $i++;
				
				$tot_order_qty+=$row[csf('color_qnty')];
				$tot_cut_qc_qnty+=$cut_qc_qnty;
				$tot_sewing_in_qty+=$sewing_in_qty;
				$tot_sewing_out_qty+=$sewing_out_qty;
				$tot_sewing_issue_qty+=$sewing_issue_qty;
	        }
	        ?>
	        </tbody>
	        <tfoot>
	        	<tr>
	            	<th>&nbsp;</th>
	            	<th>&nbsp;</th>
	                <th align="right">Total:</th>
	                <th align="right"><? echo number_format($tot_order_qty,0); ?>&nbsp;</th>
	                <th align="right"><? echo number_format($tot_cut_qc_qnty,0); ?>&nbsp;</th>
	                <th align="right"><? echo number_format($tot_sewing_in_qty,0); ?>&nbsp;</th>
	                <th align="right"><? echo number_format($tot_sewing_out_qty,0); ?>&nbsp;</th>
	                <th align="right" id="tot_seq_qnty"><? echo number_format($tot_sewing_issue_qty,0); ?>&nbsp;</th>
	            </tr>
	        </tfoot>
	    </table>
	     <table width="650" cellpadding="0" cellspacing="2" align="center">
	       <tr>
	           <td colspan="9" align="center" class="button_container">
	            <? 
	               echo load_submit_buttons( $_SESSION['page_permission'], "fnc_ready_to_sewing", $update_status,0,"reset_form('','','','','disable_enable_fields(\'cbo_company_id*cbo_buyer_name*txt_order_no*cbo_year\',0)')",1);
	            ?>
	            <input type="button" name="print" id="print" value="Print" onClick="fnc_ready_to_sewing_print(0)" style="width:70px;" class="formbutton">&nbsp;<input type="button" class="formbutton" style="width:150px;" value="Trims Issue Requisition" onClick="fn_trims_issue_req();" >
	            </td>
	      </tr>
	    </table> 
	    <br />
	    <fieldset style="width:656px;">
	    <legend>RMG Production Progress</legend>
	    <table cellpadding="0" cellspacing="0" width="655" class="rpt_table" border="1" rules="all" id="prod_prog">
	        <thead >
	            <tr>
	            	<th width="90">Country</th>
	                <th width="100">Gmts. Item</th>
	                <th width="80">Color</th>
	                <th width="60">Ord. Qty.</th>
	                <th width="55">Cut Qty</th>
	                <th width="55">Print Qty </th>
	                <th width="55">Emb. Qty</th>
	                <th width="55">Spl. Wrk. Qty.</th>
	                <th width="55">Sew. Qty.</th>
	                <th>Fin. Qty.</th>
	            </tr> 
	        </thead>
	        <tbody>
	        <?
			//echo $sql;die;
			$result=sql_select($sql);	
	        $i=1;
	        foreach($result as $row)
	        {
	            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$cutting_qty=0; $prin_qty=0; $emb_qty=0; $special_qty=0; $sewing_in_qty=0; $finish_qty=0;
				$cutting_qty=$prod_data[$row[csf("po_id")]][1][0][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]];
				$prin_qty=$prod_data[$row[csf("po_id")]][2][1][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]];
				$emb_qty=$prod_data[$row[csf("po_id")]][2][2][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]];
				$special_qty=$prod_data[$row[csf("po_id")]][2][4][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]];
				$sewing_out_qty=$prod_data[$row[csf("po_id")]][5][0][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]];
				$finish_qty=$prod_data[$row[csf("po_id")]][8][0][$row[csf("country_id")]][$row[csf("item_id")]][$row[csf("color_id")]];
				
	            ?>
	            <tr bgcolor="<? echo $bgcolor;?>">
	            	<td><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
	                <td><p><? echo $garments_item[$row[csf('item_id')]]; ?></p></td>
	                <td><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
	                <td align="right"><? echo number_format($row[csf('color_qnty')],0); ?></td>
	                <td align="right"><? if($cutting_qty>0) echo number_format($cutting_qty,0); else echo "&nbsp;"; ?></td>
	                <td align="right"><? if($prin_qty>0) echo number_format($prin_qty,0); else echo "&nbsp;"; ?></td>
	                <td align="right"><? if($emb_qty>0) echo number_format($emb_qty,0);else echo "&nbsp;"; ?></td>
	                <td align="right"><? if($special_qty>0) echo number_format($special_qty,0); else echo "&nbsp;"; ?></td>
	                <td align="right"><? if($sewing_out_qty>0) echo number_format($sewing_out_qty,0); else echo "&nbsp;"; ?></td>
	                <td align="right"><? if($finish_qty>0) echo number_format($finish_qty,0); else echo "&nbsp;"; ?></td>
	            </tr>
	            <?       
	            $i++;
				$tot_orderqty+=$row[csf('color_qnty')];
				$tot_cutting_qty+=$cutting_qty;
				$tot_prin_qty+=$prin_qty;
				$tot_emb_qty+=$emb_qty;
				$tot_special_qty+=$special_qty;
				$tot_sewing_outqty+=$sewing_out_qty;
				$tot_finish_qty+=$finish_qty;
	        }
	        ?>
	        </tbody>
	        <tfoot>
	        	<tr>
	            	<th>&nbsp;</th>
	                <th>&nbsp;</th>
	                <th align="right">Total:</th>
	                <th align="right"><? echo $tot_orderqty; ?>&nbsp;</th>
	                <th align="right"><? echo $tot_cutting_qty; ?>&nbsp;</th>
	                <th align="right"><? echo $tot_prin_qty; ?>&nbsp;</th>
	                <th align="right"><? echo $tot_emb_qty; ?>&nbsp;</th>
	                <th align="right"><? echo $tot_special_qty; ?>&nbsp;</th>
	                <th align="right"><? echo $tot_sewing_outqty; ?>&nbsp;</th>
	                <th align="right"><? echo $tot_finish_qty; ?>&nbsp;</th>
	            </tr>
	        </tfoot>
	    </table>
	    <br>
	    <table cellpadding="0" cellspacing="0" width="330"  border="0">
	    	<tr>
	        	<td width="110">PP Meeting Done:</td>
	            <td width="70"><input type="text" id="pp_meeting_date" name="pp_meeting_date" class="datepicker" style="width:70px;" value="<? if($pp_meeting_date!="" && $pp_meeting_date!='0000-00-00') echo change_date_format($pp_meeting_date); ?>" disabled ></td>
	            <td onClick="openmypage_image('requires/ready_to_sewing_entry_controller.php?action=show_image&job_no=<? echo $job_no_ref; ?>', 'Image View')">
				<? $imge_location=array_unique(explode(",",$imge_location)); $all_image="";
					foreach($imge_location as $img_path)
					{
						$all_image.='&nbsp;<img src="../'.$img_path.'" height="40" width="50" />';
					}
					echo $all_image; ?></td>
	        </tr>
		</table>
	    </fieldset>

		<?
	}
	
   
	exit();
}

if ($action=="show_image") 
{
    echo load_html_head_contents("Image View", "../../", 1, 1, $unicode);
    extract($_REQUEST);
    //echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
    $data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
    ?>
    <table width="1000">
        <tr>
        <? foreach ($data_array as $row) { ?>
        	<td><img src='../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
        <? } ?>
        </tr>
    </table>
    <?
    exit();
}

if($action=="trims_status_list")
{
	//echo $data;
	$condition= new condition();
	if(str_replace("'","",$data)!="")
	{
		$condition->po_id("=$data"); 
	}
	$condition->init();
	$trims= new trims($condition);
	//$trims_costing_arr=$trims->getQtyArray_by_orderAndItemid();
	$trim_qty=$trims->getQtyArray_by_precostdtlsid();
	//echo '<pre>';print_r($trims_costing_arr);
	$trim_rcv_data=sql_select("select b.po_breakdown_id, a.item_group_id, b.quantity from  inv_trims_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id=$data and a.status_active=1 and b.status_active=1 and b.trans_type in(1,4)");
	foreach($trim_rcv_data as $row)
	{
		$trim_rcv_data_arr[$row[csf("po_breakdown_id")]][$row[csf("item_group_id")]]+=$row[csf("quantity")];
	}
	$trim_issue_data=sql_select("select b.po_breakdown_id, a.item_group_id, b.quantity from inv_trims_issue_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id=$data and a.status_active=1 and b.status_active=1 and b.trans_type in(2,3)");
	foreach($trim_issue_data as $row)
	{
		$trim_issue_data_arr[$row[csf("po_breakdown_id")]][$row[csf("item_group_id")]]+=$row[csf("quantity")];
	}
	$trims_sql="select a.id as po_id, b.id as pre_cost_id, b.trim_group, b.cons_uom, c.item_name as trims_group_name
	from wo_po_break_down a, wo_pre_cost_trim_cost_dtls b, lib_item_group c 
	where a.job_no_mst=b.job_no and b.trim_group=c.id and c.trim_type=1 and a.id=$data and b.status_active=1 and b.is_deleted=0";
	//echo $trims_sql;
	$trims_result=sql_select($trims_sql); 
	?>
    <fieldset style="width:650px;" >
    <legend> Sewing Trims Received Status</legend>
    <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all" id="order_table_<? echo $j; ?>">
        <thead >
            <tr>
                <th width="30">SL</th>
                <th width="180">Trims Name</th>
                <th width="80">UOM</th>
                <th width="80">Required</th>
                <th width="80">Received</th>
                <th width="80">Issued</th>
                <th >Stock</th>
            </tr> 
        </thead>
        <tbody>
        <?
        $i=1;
		//$trim_grp_arr=return_library_array("select id,item_name from lib_item_group where item_category=4","id","item_name");
        foreach($trims_result as $row)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			//$trm_require=$trims_costing_arr[$row[csf('po_id')]][$row[csf('trim_group')]];
			$trm_require = $trim_qty[$row[csf('pre_cost_id')]];
			$trm_rcv=$trim_rcv_data_arr[$row[csf('po_id')]][$row[csf('trim_group')]];
			$trm_issue=$trim_issue_data_arr[$row[csf('po_id')]][$row[csf('trim_group')]];
			$trm_bal=$trm_rcv-$trm_issue;
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><p><? echo $i; ?></p></td>
                <td><p><? echo $row[csf('trims_group_name')]; ?></p></td>
                <td align="center"><p><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></p></td>
                <td align="right"><? echo number_format($trm_require,2); ?></td>
                <td align="right"><? echo number_format($trm_rcv,2); ?></td>
                <td align="right"><? echo number_format($trm_issue,2); ?></td>
                <td align="right"><? echo number_format($trm_bal,2); ?></td>
            </tr>
            <? 
			$trm_require=$trm_rcv=$trm_issue=$trm_bal=0;     
            $i++;
        }
        ?>
        </tbody>
    </table>
    </fieldset>
    <?
	exit();
}

//approval_status_list
if($action=="approval_status_list")
{
	$sample_sql="select a.id as sam_id, a.sample_type_id, a.po_break_down_id, a.color_number_id, a.approval_status, a.sample_comments from wo_po_sample_approval_info a where  a.po_break_down_id=$data and a.approval_status=3 and a.status_active=1";
	//echo $sample_sql;
	$sample_result=sql_select($sample_sql); 
	?>
    <fieldset style="width:650px;" >
    <legend> Approval Status</legend>
    <p style="font-size:16px; font-weight:bold;">Sample Approval : </p>
    <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all" id="order_table_<? echo $j; ?>">
        <thead >
            <tr>
                <th width="50">SL</th>
                <th width="200">Sample Name</th>
                <th width="150">Color</th>
                <th width="100">Status</th>
                <th >Comments</th>
            </tr> 
        </thead>
        <tbody>
        <?
        $i=1;
		$color_size_tbl_color=return_library_array("select color_number_id,id from wo_po_color_size_breakdown where is_deleted=0 and status_active=1 and po_break_down_id=$data","id","color_number_id");
		$sample_arr=return_library_array("select sample_name,id from lib_sample where is_deleted=0 and status_active=1","id","sample_name");
		$color_arr=return_library_array("select color_name,id from  lib_color","id","color_name");
		$trim_grp_arr=return_library_array("select id,item_name from lib_item_group where item_category=4","id","item_name");
        foreach($sample_result as $row)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><p><? echo $i; ?></p></td>
                <td><p><? echo $sample_arr[$row[csf('sample_type_id')]]; ?>&nbsp;</p></td>
                <td><p><? echo  $color_arr[$color_size_tbl_color[$row[csf('color_number_id')]]]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $approval_status[$row[csf('approval_status')]]; ?>&nbsp;</p></td>
                <td><p><? echo $row[csf('sample_comments')]; ?>&nbsp;</p></td>
            </tr>
            <? 
            $i++;
        }
        ?>
        </tbody>
    </table>
    <p style="font-size:16px; font-weight:bold;">Sewing Trims Approval : </p>
    <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all" id="order_table_<? echo $j; ?>">
        <thead >
            <tr>
                <th width="50">SL</th>
                <th width="200">Sew Trims Name</th>
                <th width="100">Status</th>
                <th >Comments</th>
            </tr> 
        </thead>
        <tbody>
        <?
        $i=1;
		$trim_approv_sql="select a.id as trim_approv_id, a.accessories_type_id, a.po_break_down_id, a.approval_status, a.accessories_comments from wo_po_trims_approval_info a where  a.po_break_down_id=$data and a.approval_status=3 and a.status_active=1";
		$trim_result=sql_select($trim_approv_sql); 
        foreach($trim_result as $row)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><p><? echo $i; ?></p></td>
                <td><p><? echo $trim_grp_arr[$row[csf('accessories_type_id')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $approval_status[$row[csf('approval_status')]]; ?>&nbsp;</p></td>
                <td><p><? echo $row[csf('accessories_comments')]; ?>&nbsp;</p></td>
            </tr>
            <? 
            $i++;
        }
        ?>
        </tbody>
    </table>
    
     <p style="font-size:16px; font-weight:bold;">Embellishment Approval : </p>
    <table cellpadding="0" cellspacing="0" width="650" class="rpt_table" border="1" rules="all" id="order_table_<? echo $j; ?>">
        <thead >
            <tr>
                <th width="50">SL</th>
                <th width="200">Embl. Name</th>
                <th width="150">Color</th>
                <th width="100">Status</th>
                <th >Comments</th>
            </tr> 
        </thead>
        <tbody>
        <?
        $i=1;
		$emblish_sql="select a.id as emb_id, a.embellishment_type_id, a.po_break_down_id, a.color_name_id, a.approval_status, a.embellishment_comments from wo_po_embell_approval a where  a.po_break_down_id=$data and a.approval_status=3 and a.status_active=1";
		$emblish_result=sql_select($emblish_sql); 
        foreach($emblish_result as $row)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><p><? echo $i; ?>&nbsp;</p></td>
                <td><p><? echo $emblishment_name_array[$row[csf('embellishment_type_id')]]; ?>&nbsp;</p></td>
                <td><p><? echo  $color_arr[$row[csf('color_name_id')]]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $approval_status[$row[csf('approval_status')]]; ?>&nbsp;</p></td>
                <td><p><? echo $row[csf('embellishment_comments')]; ?>&nbsp;</p></td>
            </tr>
            <? 
            $i++;
        }
        ?>
        </tbody>
    </table>
    </fieldset>
    <?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "ready_to_sewing_mst", 1) ;
		
		if ($db_type == 0) $year_cond = "YEAR(insert_date)";
		else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
		else $year_cond = "";
				 
		$new_mrr_number = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_id), '', 'RSE', date("Y", time()), 5, "select sew_number_prefix, sew_number_prefix_num from ready_to_sewing_mst where company_id=$cbo_company_id and $year_cond=".date('Y', time())." order by id DESC", "sew_number_prefix", "sew_number_prefix_num"));
		
		$field_array="id, sew_number_prefix, sew_number_prefix_num, sew_number, company_id, po_break_down_id, buyer_id, ready_sewing_date, status_active, is_deleted, inserted_by, insert_date ";
		$data_array="(".$id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',".$cbo_company_id.",".$txt_order_id.",".$cbo_buyer_name.",".$txt_sewing_date.",1,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		if( str_replace( "'", "", $sewing_production_variable) == 3) // color and size level
		{
			$field_array_dtls="id, mst_id, po_break_down_id, country_id, garments_itme_id, color_id, sewing_qnty ,size_id , entry_break_down_type, status_active, is_deleted, inserted_by, insert_date";
			$dtls_id = return_next_id( "id", "ready_to_sewing_dtls", 1);
			$data_array_dtls=""; $color_qty_arr=array(); $color=""; $countryid=""; $gmtsitem="";
			for($j=1;$j<=$tot_row;$j++)
			{
				$hiddcountryid="hiddcountryid".$j;
				$gramentitem="gramentitem".$j;
				$colorid="colorid".$j;
				$sizeid="sizeid".$j;
				$colorpoqty="colorpoqty".$j;
				$sewingissu="sewingissu".$j;
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",".$txt_order_id.",'".$$hiddcountryid."','".$$gramentitem."','".$$colorid."','".$$sewingissu."','".$$sizeid."',".$sewing_production_variable.",1,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$dtls_id = $dtls_id+1;
			}
		}
		else // color level
		{
			$field_array_dtls="id, mst_id, po_break_down_id, country_id, garments_itme_id, color_id,sewing_qnty,entry_break_down_type, status_active, is_deleted, inserted_by, insert_date";
			$dtls_id = return_next_id( "id", "ready_to_sewing_dtls", 1);
			$data_array_dtls=""; $color_qty_arr=array(); $color=""; $countryid=""; $gmtsitem="";
			for($j=1;$j<=$tot_row;$j++)
			{
				$hiddcountryid="hiddcountryid".$j;
				$gramentitem="gramentitem".$j;
				$colorid="colorid".$j;
				$sizeid="sizeid".$j;
				$colorpoqty="colorpoqty".$j;
				$sewingissu="sewingissu".$j;
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",".$txt_order_id.",'".$$hiddcountryid."','".$$gramentitem."','".$$colorid."','".$$sewingissu."',".$sewing_production_variable.",1,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$dtls_id = $dtls_id+1;
			}
		}
		//echo "10**";
		
		// echo "10**insert into ready_to_sewing_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID=sql_insert("ready_to_sewing_mst",$field_array,$data_array,0);
		$rID2=sql_insert("ready_to_sewing_dtls",$field_array_dtls,$data_array_dtls,0);
		// echo "10**".$rID."&&".$rID2;die;

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".str_replace("'","",$txt_order_id)."**".str_replace("'","",$new_mrr_number[0]);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "0**".$id."**".str_replace("'","",$txt_order_id)."**".str_replace("'","",$new_mrr_number[0]);
			}
			else
			{
				oci_rollback($con);
				echo "5**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//echo "10**";
		$field_array="po_break_down_id*buyer_id*ready_sewing_date*update_by*update_date";
		$data_array=$txt_order_id."*".$cbo_buyer_name."*".$txt_sewing_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		if( str_replace( "'", "", $sewing_production_variable) == 3) // color and size level
		{
			$field_array_dtls="id, mst_id, po_break_down_id, country_id, garments_itme_id, color_id, sewing_qnty, size_id, entry_break_down_type, inserted_by, insert_date";
			$dtls_id = return_next_id( "id", "ready_to_sewing_dtls", 1 );
			$field_array_update="sewing_qnty*update_by*update_date";
			$deleted_id='';$data_array_dtls=''; $color=""; $countryid=""; $gmtsitem=""; $color_qty_arr=array();
			$id=str_replace("'","",$update_id);
			for($j=1;$j<=$tot_row;$j++)
			{
				$hiddcountryid="hiddcountryid".$j;	
				$gramentitem="gramentitem".$j;
				$colorid="colorid".$j;
				$sizeid="sizeid".$j;
				$sewingissu="sewingissu".$j;
				$colorpoqty="colorpoqty".$j;
				$dtlsId="dtlsid".$j;
				
				if($$dtlsId>0)
				{
					if($$sewingissu>0)
					{
						$dtlsId_arr[]=$$dtlsId;
						$data_array_update[$$dtlsId]=explode("*",("'".$$sewingissu."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
				}
				else
				{
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$dtls_id.",".$id.",".$txt_order_id.",'".$$hiddcountryid."','".$$gramentitem."','".$$colorid."','".$$sewingissu."','".$$sizeid."',".$sewing_production_variable.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$dtls_id = $dtls_id+1;	
				}
			}
		}
		else
		{
			$field_array_dtls="id, mst_id, po_break_down_id, country_id, garments_itme_id, color_id, sewing_qnty,entry_break_down_type, inserted_by, insert_date";
			$dtls_id = return_next_id( "id", "ready_to_sewing_dtls", 1 );
			$field_array_update="sewing_qnty*update_by*update_date";
			$deleted_id='';$data_array_dtls=''; $color=""; $countryid=""; $gmtsitem=""; $color_qty_arr=array();
			$id=str_replace("'","",$update_id);
			for($j=1;$j<=$tot_row;$j++)
			{
				$hiddcountryid="hiddcountryid".$j;	
				$gramentitem="gramentitem".$j;
				$colorid="colorid".$j;
				$sewingissu="sewingissu".$j;
				$colorpoqty="colorpoqty".$j;
				$dtlsId="dtlsid".$j;
				
				if($$dtlsId>0)
				{
					if($$sewingissu>0)
					{
						$dtlsId_arr[]=$$dtlsId;
						$data_array_update[$$dtlsId]=explode("*",("'".$$sewingissu."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
				}
				else
				{
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$dtls_id.",".$id.",".$txt_order_id.",'".$$hiddcountryid."','".$$gramentitem."','".$$colorid."','".$$sewingissu."',".$sewing_production_variable.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$dtls_id = $dtls_id+1;	
				}
			}
		}
		
		
		//echo "10**";
		$activedtls_id=implode(",",$dtlsId_arr); //$deleted_id_arr=array();
		if($activedtls_id!="")
		{
			$sql_dtls=sql_select("select id from ready_to_sewing_dtls where mst_id='$id' and id not in ($activedtls_id) and status_active=1 and is_deleted=0");
			//echo "select id from ready_to_sewing_dtls where mst_id='$id' and id not in ($activedtls_id) and status_active=1 and is_deleted=0";
			foreach($sql_dtls as $rdel)
			{
				$deleted_id_arr[]=$rdel[csf("id")];
			}
		}
		
		$rID=$rID2=$rID3=$statusChange=$rID4=true;
		$rID=sql_update("ready_to_sewing_mst",$field_array,$data_array,"id",$id,0);
		if(count($data_array_update)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement( "ready_to_sewing_dtls", "id", $field_array_update, $data_array_update, $dtlsId_arr ));
		}
		
		if($data_array_dtls!="")
		{
			$rID3=sql_insert("ready_to_sewing_dtls",$field_array_dtls,$data_array_dtls,1);
		}
		//echo $rID2.'='.$rID3.'='.$statusChange;
		$deleted_id=implode(",",$deleted_id_arr);
		$field_del_arr="status_active*is_deleted*update_by*update_date";
		$datadel_arr="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		foreach($deleted_id_arr as $id_val)
		{
			$statusChange=sql_update("ready_to_sewing_dtls",$field_del_arr,$datadel_arr,"id","".$id_val."",1);
			//if($statusChange) $flag=1; else $flag=0;
		}
		//echo "6**".$rID ."&&". $rID2 ."&&". $rID3 ."&&". $statusChange;die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $statusChange)
			{
				mysql_query("COMMIT");  
				echo "1**".$id."**".str_replace("'","",$txt_order_id)."**".str_replace("'","",$txt_sewing_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $statusChange)
			{
				oci_commit($con);  
				echo "1**".$id."**".str_replace("'","",$txt_order_id)."**".str_replace("'","",$txt_sewing_no);
			}
			else
			{
				oci_rollback($con);
				echo "6**".$id;
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="sewing_popup")
{
	echo load_html_head_contents("Sewing Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		function js_set_value(data)
		{
			$('#hidden_reqn_id').val(data);
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:750px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:750px; margin-left:2px">
            <table cellpadding="0" cellspacing="0" width="740" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Sewing Date Range</th>
                    <th id="search_by_td_up" width="180">Requisition No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="hidden_reqn_id" id="hidden_reqn_id">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" readonly>
					</td>
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_reqn_no" id="txt_reqn_no" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_reqn_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>, 'create_reqn_search_list_view', 'search_div', 'ready_to_sewing_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px;" id="search_div" align="center"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_location_id').val(0);
</script>
</html>
<?
exit();
}

if($action=="create_reqn_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string=trim($data[0]);
	$start_date =$data[1];
	$end_date =$data[2];
	$company_id =$data[3];

	$sql_cond="";
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$sql_cond="and a.ready_sewing_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$sql_cond="and a.ready_sewing_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	
	if($search_string!="")
	{
		$sql_cond.="and a.sew_number_prefix_num=$search_string";
	}
	
	if ($db_type == 0) $year_field = "YEAR(a.insert_date)";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY')";
	else $year_field = "";
	
	$sql = "select a.id, a.company_id, $year_field as year, a.sew_number_prefix_num, a.po_break_down_id, a.buyer_id, a.ready_sewing_date, b.po_number from ready_to_sewing_mst a, wo_po_break_down b where a.po_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $sql_cond  order by a.id desc"; 
	//echo $sql;
	$com_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$arr=array(3=>$buyer_arr);
	
	echo create_list_view("tbl_list_search", "Year,Reqsn. No,Reqsn. Date, Buyer, Order No", "70,70,80,150,150","550","250",0, $sql, "js_set_value", "id,po_break_down_id", "", 1, "0,0,0,buyer_id,0", $arr, "year,sew_number_prefix_num,ready_sewing_date,buyer_id,po_number","","",'0,0,3,0,0','');
	
	exit();
}

if($action=='populate_data_from_requisition')
{
	if($db_type==0) $select_year="year(insert_date) as year"; else $select_year="to_char(insert_date,'YYYY') as year";
	$po_arr=return_library_array( "select id, po_number from  wo_po_break_down",'id','po_number');
	$data_array=sql_select("select id, sew_number, company_id, po_break_down_id, buyer_id, ready_sewing_date, $select_year from ready_to_sewing_mst where id=$data");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_sewing_no').value 			= '".$row[csf("sew_number")]."';\n";
		echo "document.getElementById('update_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 			= ".$row[csf("company_id")].";\n";
		echo "document.getElementById('cbo_buyer_name').value 			= ".$row[csf("buyer_id")].";\n";
		echo "document.getElementById('cbo_year').value 				= ".$row[csf("year")].";\n";
		echo "document.getElementById('txt_sewing_date').value 			= '".change_date_format($row[csf("ready_sewing_date")])."';\n";
		echo "document.getElementById('txt_order_no').value 			= '".$po_arr[$row[csf("po_break_down_id")]]."';\n";
		echo "document.getElementById('txt_order_id').value 			= '".$row[csf("po_break_down_id")]."';\n";
		echo "disable_enable_fields('cbo_company_id*cbo_buyer_name*txt_order_no*cbo_year',1);\n";
		exit();
	}
	exit();
}

if($action=="ready_to_sewing_print")
{
	extract($_REQUEST);
	echo load_html_head_contents("Ready To Sewing Print", "../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	$company_id=$data[0];
	$sewing_no=$data[1];
	$report_title=$data[2];
	$update_id=$data[3];
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$dealing_marchant_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id","team_member_name");
	
	$mst_sql=sql_select("select a.job_no, a.company_name, a.location_name, a.buyer_name, a.style_ref_no, a.dealing_marchant, b.id as po_id, b.po_number, b.shipment_date, b.po_quantity, c.id as req_id, c.sew_number, c.ready_sewing_date, sum(d.sewing_qnty) as sewing_qty 
	from wo_po_details_master a, wo_po_break_down b, ready_to_sewing_mst c, ready_to_sewing_dtls d 
	where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.id=d.mst_id and c.id=$update_id and c.status_active=1 and d.status_active=1
	group by a.job_no, a.company_name, a.location_name, a.buyer_name, a.style_ref_no, a.dealing_marchant, b.id, b.po_number, b.shipment_date, b.po_quantity, c.id, c.sew_number, c.ready_sewing_date");
	//echo $mst_sql;die;
?> 
    <div style="width:930px;">
    <table width="100%" cellpadding="0" cellspacing="0" >
        <tr>
            <td width="70" align="right"> 
            	<img  src='../../<? echo $imge_arr[str_replace("'","",$company_id)]; ?>' height='100%' width='100%' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$company_id]; ?></strong></td>
                    </tr>
                    <tr class="form_caption">
                        <td  align="center" style="font-size:14px"><? echo show_company($company_id,'',''); ?></td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
     <table width="930" cellspacing="0" align="" border="0">
        <tr>
            <td width="130"><strong>Reqsn. No :</strong></td><td width="175"><? echo $mst_sql[0][csf('sew_number')]; ?></td>
            <td width="130"><strong>Entry Date: </strong></td><td width="175px"> <? echo change_date_format($mst_sql[0][csf('ready_sewing_date')]); ?></td>
            <td width="130"><strong>PO No :</strong></td><td width="175"><? echo $mst_sql[0][csf('po_number')]; ?></td>
        </tr>
        <tr>
            <td width="130"><strong>Job No :</strong></td><td width="175"><? echo $mst_sql[0][csf('job_no')]; ?></td>
            <td width="130"><strong>Buyer: </strong></td><td width="175px"> <? echo $buyer_library[$mst_sql[0][csf('buyer_name')]]; ?></td>
            <td width="130"><strong>Style Ref. :</strong></td><td width="175"><? echo $mst_sql[0][csf('style_ref_no')]; ?></td>
        </tr>
        <tr>
            <td width="130"><strong>Dealing Merchant :</strong></td><td width="175"><? echo $dealing_marchant_arr[$mst_sql[0][csf('dealing_marchant')]]; ?></td>
            <td width="130"><strong>Ship Date: </strong></td><td width="175px"> <? echo change_date_format($mst_sql[0][csf('shipment_date')]); ?></td>
            <td width="130"><strong>Order Qty (Pcs):</strong></td><td width="175"><? echo number_format($mst_sql[0][csf('po_quantity')],0); ?></td>
        </tr>
        <tr>
            <td width="130"><strong>Ready to Sew Qty:</strong></td><td width="175"><? echo number_format($mst_sql[0][csf('sewing_qty')],0); ?></td>
            <td width="130"><strong>&nbsp;</strong></td><td width="175px">&nbsp;</td>
            <td width="130"><strong>&nbsp;</strong></td><td width="175">&nbsp;</td>
        </tr>
    </table>
         <br>
		<div style="width:100%;">
		<table align="right" cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="100">Trims Name</th>
                <th width="120">Trims Description</th>
                <th width="80">Country</th>
                <th width="100">Gmts. Item</th>
                <th width="80">Gmts. Color</th>
                <th width="60">Gmts. Size</th>
                <th width="50">UOM</th>
                <th width="60">Input Qty.</th>
                <th width="60">Cons/Dzn As Per Budget</th>
                <th width="60">Required (BOM)</th>
                <th width="60">Previous Issued</th>
                <th>Reqsn. Qty.</th>
            </thead>
            <tbody>
			<?
               // $lib_item_group_arr=return_library_array( "select item_name,id from lib_item_group where item_category=4 and is_deleted=0 and status_active=1 order by item_name", "id", "item_name");
				$desc_arr=return_library_array( "select id, description from wo_pre_cost_trim_cost_dtls where is_deleted=0 and status_active=1", "id", "description");
				
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
				$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
				$size_arr=return_library_array( "select id, size_name from lib_size", "id", "size_name");
				$txt_order_id=$mst_sql[0][csf('po_id')];
				
				/*$po_qty_arr=array();
				$po_sql="select id, country_id, item_number_id, color_number_id, size_number_id, order_quantity from wo_po_color_size_breakdown where is_deleted=0 and status_active=1 and po_break_down_id='$txt_order_id'";
				$po_sql_res=sql_select($po_sql);
				foreach($po_sql_res as $row)
				{
					$po_qty_arr[$row[csf("id")]]['country_id']=$row[csf("country_id")];
					$po_qty_arr[$row[csf("id")]]['item_number_id']=$row[csf("item_number_id")];
					$po_qty_arr[$row[csf("id")]]['color_number_id']=$row[csf("color_number_id")];
					$po_qty_arr[$row[csf("id")]]['size_number_id']=$row[csf("size_number_id")];
				}
				unset($po_sql_res);*/
				$dtls_data_arr=array();
				$sql_dtls_data=sql_select("select id, garments_itme_id, color_id, country_id from ready_to_sewing_dtls where mst_id=$update_id and status_active=1 and status_active=1");
				foreach($sql_dtls_data as $row)
				{
					$dtls_data_arr[$row[csf("id")]]['itme_id']=$row[csf("garments_itme_id")];
					$dtls_data_arr[$row[csf("id")]]['color_id']=$row[csf("color_id")];
					$dtls_data_arr[$row[csf("id")]]['country_id']=$row[csf("country_id")];
				}
                unset($sql_dtls_data);
				
                $trim_issue_data=sql_select("select b.po_breakdown_id, a.item_group_id, b.quantity from inv_trims_issue_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id=$txt_order_id and a.status_active=1 and b.status_active=1 and b.trans_type in(2,3)");
                foreach($trim_issue_data as $row)
                {
                    $trim_issue_data_arr[$row[csf("po_breakdown_id")]][$row[csf("item_group_id")]]+=$row[csf("quantity")];
                }
				// $req_sql="select id, mst_id, po_id, color_size_table_id, precost_trim_dtls_id, trim_group, cons_uom, cons, req_bom, trim_iss, reqsn_qty from ready_to_sewing_reqsn where mst_id=$update_id";
				$req_sql="select a.id, a.mst_id, a.po_id, a.dtls_id, a.color_id, a.size_id, a.color_size_table_id, a.precost_trim_dtls_id, a.cons_uom, a.size_input_qty, a.cons, a.req_bom, a.trim_iss, a.reqsn_qty, b.item_name, b.trim_type from ready_to_sewing_reqsn a, lib_item_group b where a.trim_group=b.id and a.mst_id=$update_id order by b.trim_type ASC";
                //echo $trims_sql;
                $trims_result=sql_select($req_sql);$i=1;
                foreach ($trims_result as $row)
                {
                    //$cons_per_dzn=(($trims_costing_arr_data[$row[csf("po_id")]][$row[csf("pre_id")]]/$order_qnty)*12);
					$cons_per_dzn=$row[csf("cons")];
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if (!in_array($row[csf('trim_type')],$trims_type_arr) )
					{
						?>
						<tr bgcolor="#EFEFEF">
							<td colspan="13" align="left" ><b>Trims Type: <? echo $trim_type[$row[csf('trim_type')]]; ?></b></td>
						</tr>
						<?
						$trims_type_arr[]=$row[csf('trim_type')];            
						$k++;
						
					}
					$country_id=$dtls_data_arr[$row[csf("dtls_id")]]['country_id'];
					$color_id=$dtls_data_arr[$row[csf("dtls_id")]]['color_id'];
					$itme_id=$dtls_data_arr[$row[csf("dtls_id")]]['itme_id'];
					
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;"> 
                        <td width="30"> <? echo $i; ?></td>
                        <td width="100"><p><? echo $row[csf('item_name')]; ?></p></td>
                        <td width="120"><p><? echo $desc_arr[$row[csf('precost_trim_dtls_id')]]; ?></p>&nbsp;</td>
                        <td width="80"><p><? echo $country_arr[$country_id]; ?></p></td>
                        <td width="100"><p><? echo $garments_item[$itme_id]; ?></p></td>
                        <td width="80"><p><? echo $color_arr[$color_id]; ?></p></td>
                        <td width="60"><p><? echo $size_arr[$row[csf("size_id")]]; ?></p></td>
                        <td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></p></td>
                        <td width="60" align="right"><? echo number_format($row[csf('size_input_qty')],0); ?></td>
                        <td width="60" align="right"><? echo number_format($cons_per_dzn,4); ?></td>
                        <td width="60" align="right"><p><? echo number_format($row[csf('req_bom')],4); ?></p></td>
                        <td width="60" align="right"><p><? echo number_format($trim_issue_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]],2); ?></p></td>
                        <td align="right"><? echo number_format($row[csf('reqsn_qty')],4); ?></td>
                    </tr>
                    <?
                    $i++;
                }
               ?>
            </tbody>
        </table>
        </div>
        <br>
         <?
            echo signature_table(120, $company_id, "930px");
         ?>
    	</div>
    </div>
	<?
    exit();
}
?>
