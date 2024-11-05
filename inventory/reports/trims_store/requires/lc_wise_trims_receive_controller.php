<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 150, "select a.id, a.store_name from lib_store_location a,lib_store_location_category b  where a.id=b.store_location_id and a.company_id='$data' and b.category_type in(1) and a.status_active=1 and a.is_deleted=0 order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "" );
	exit();
	//select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type=$data[1] order by a.store_name
}


$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
$group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );


if($action=="pinumber_popup")
{
  	echo load_html_head_contents("PI Number Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(str)
	{
		var splitData = str.split("_");
		$("#pi_id").val(splitData[0]);
		$("#pi_no").val(splitData[1]);
		parent.emailwindow.hide();
	}
	</script>

	</head>

	<body>
	<div align="center" style="width:100%; margin-top:5px" >
	<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
		<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th>Supplier</th>
						<th id="search_by_td_up">Enter PI Number</th>
						<th>Enter PI Date</th>
						<th>
							<input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
							<input type="hidden" id="pi_id" value="" />
							<input type="hidden" id="pi_no" value="" />
						</th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td>
							<?
								echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center" id="search_by_td">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;" placeholder="From Date" readonly />
							To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;" placeholder="To Date" readonly />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_pi_search_list_view', 'search_div', 'lc_wise_trims_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="4" align="center"><? echo load_month_buttons(1); ?></td>
					</tr>
				</tbody>
			</table>
			<div align="center" style="margin-top:10px" id="search_div"> </div>
			</form>
	</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_pi_search_list_view")
{
	$ex_data = explode("_",$data);

	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	if( $from_date!="" && $to_date!="")
	{
		if($db_type==0)
		{
			$pi_date_cond= " and pi_date between '".change_date_format($from_date,"yyyy-mm-dd")."' and '".change_date_format($to_date,"yyyy-mm-dd")."'";
		}
		else
		{
			$pi_date_cond= " and pi_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
		}
	}
	else $pi_date_cond="";

	$sql= "select id, pi_number, supplier_id, importer_id, pi_date, last_shipment_date, total_amount from com_pi_master_details where importer_id=$company and supplier_id like '$cbo_supplier' and pi_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 $pi_date_cond";

	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "PI No, Importer, Supplier Name, PI Date, Last Shipment Date, PI Value","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,pi_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "pi_number,importer_id,supplier_id,pi_date,last_shipment_date,total_amount", "",'','0,0,0,3,3,2') ;
	exit();
}

if($action=="btbLc_popup")
{
  	echo load_html_head_contents("BTB LC Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
     
<script>
	function js_set_value(str)
	{
		var splitData = str.split("_");		 
		$("#btbLc_id").val(splitData[0]); 
		$("#btbLc_no").val(splitData[1]); 
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%; margin-top:5px" >
<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
	<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th>Supplier</th>
                    <th id="search_by_td_up">Enter BTB LC Number</th>
                    <th>
                    	<input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
                        <input type="hidden" id="btbLc_id" value="" />
                        <input type="hidden" id="btbLc_no" value="" />
                    </th>           
                </tr>
            </thead>
            <tbody>
                <tr align="center">
                    <td>
                        <?  
							echo create_drop_down( "cbo_supplier_id", 160,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=5",'id,supplier_name', 1, '-- All Supplier --',0,'',0);
                        ?>
                    </td>
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>, 'create_lc_search_list_view', 'search_div', 'lc_wise_trims_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
           	 	</tr> 
            </tbody>         
        </table>    
        <div align="center" style="margin-top:10px" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

}


if($action=="create_lc_search_list_view")
{
	$ex_data = explode("_",$data);
	
	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	
	$sql= "select id, lc_number, supplier_id, importer_id, lc_date, last_shipment_date, lc_value from com_btb_lc_master_details where importer_id=$company and (item_category_id=4 or pi_entry_form=167) and supplier_id like '$cbo_supplier' and lc_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1";
	
	//echo $sql;
	
	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "LC No, Importer, Supplier Name, LC Date, Last Shipment Date, LC Value","120,140,200,80,80","780","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "lc_number,importer_id,supplier_id,lc_date,last_shipment_date,lc_value", "",'','0,0,0,3,3,2') ;	
	exit();
	
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	//if(str_replace("'","",$cbo_store_name)!="") $store_cond=" and b.store_id in(".str_replace("'","",$cbo_store_name).")";
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$pi_no_cond=str_replace("'","",$txt_pi_no);
	$btbLc_id=str_replace("'","",$btbLc_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$sql_cond="";
	// if($pi_no_cond=='') $pi_cond=""; else $pi_cond="and b.pi_number='$pi_no_cond'";
	if($pi_no_cond=='') $pi_cond_btb=""; else $pi_cond_btb="and d.pi_number='$pi_no_cond'";
	if($btbLc_id!="") $sql_cond=" and a.id = $btbLc_id";
	if($txt_date_from !="" && $txt_date_to !="") $sql_cond.=" and a.lc_date between '$txt_date_from' and '$txt_date_to'";
	
	$btb_pi_sql="SELECT a.id as btb_id, a.importer_id, a.lc_number, a.lc_date, a.last_shipment_date, a.supplier_id, a.lc_value, d.id as pi_id, d.pi_number, d.pi_date, c.id as pi_dtls_id, c.item_group, c.item_description, c.quantity, c.amount, c.net_pi_amount, c.remarks, d.goods_rcv_status, d.after_goods_source, c.work_order_id, c.booking_without_order 
	from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_item_details c, com_pi_master_details d
	where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.pi_id=d.id and d.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.importer_id=$cbo_company_name $sql_cond $pi_cond_btb";
	// echo $btb_pi_sql;die;
	$btb_pi_result=sql_select($btb_pi_sql);
	if($db_type==0)
	{
		$conversion_date=date("Y-m-d");
	}
	else
	{
		$conversion_date=date("d-M-y");
	}
	$exchange_rate=set_conversion_rate( 2, $conversion_date );
	
	$btb_data=$pi_data=$all_pi_id=array();$pi_num_arr=array();$exchange_rate_arr=array();
	foreach($btb_pi_result as $row)
	{
		
		if($db_type==0)
		{
			$conversion_date=date("Y-m-d",strtotime($row[csf("pi_date")]));
		}
		else
		{
			$conversion_date=date("d-M-y",strtotime($row[csf("pi_date")]));
		}
		$exchange_rate=set_conversion_rate( 2, $conversion_date );
		
		if($row[csf("goods_rcv_status")]==1)
		{
			if($row[csf("booking_without_order")]==1)
			{
				if($wo_b_check[$row[csf("work_order_id")]]=="")
				{
					$wo_b_check[$row[csf("work_order_id")]]=$row[csf("work_order_id")];
					$all_wo_withoutPO_id.=$row[csf("work_order_id")].",";
				}
				
			}
			else
			{
				$all_wo_id[$row[csf("work_order_id")]]=$row[csf("work_order_id")];
			}
			
			$exchange_rate_arr[$row[csf("work_order_id")]]=$exchange_rate;
		}
		else
		{
			$all_pi_id[$row[csf("pi_id")]]=$row[csf("pi_id")];
			$exchange_rate_arr[$row[csf("pi_id")]]=$exchange_rate;
		}
		
		$acceptance_pi_id[$row[csf("pi_id")]]=$row[csf("pi_id")];
		
		$pi_num_arr[$row[csf("pi_id")]]=$row[csf("pi_number")];
		
		if($btb_check[$row[csf("btb_id")]]=="")
		{
			$btb_check[$row[csf("btb_id")]]=$row[csf("btb_id")];
			$btb_data[$row[csf("btb_id")]]["importer_id"]=$row[csf("importer_id")];
			$btb_data[$row[csf("btb_id")]]["supplier_id"]=$row[csf("supplier_id")];
			$btb_data[$row[csf("btb_id")]]["lc_number"]=$row[csf("lc_number")];
			$btb_data[$row[csf("btb_id")]]["lc_date"]=$row[csf("lc_date")];
			$btb_data[$row[csf("btb_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
			$btb_data[$row[csf("btb_id")]]["lc_value"]=$row[csf("lc_value")];
		}
		
		if($pi_check[$row[csf("pi_dtls_id")]]=="")
		{
			$pi_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
			$pi_data[$row[csf("pi_id")]][$row[csf("item_group")]][$row[csf("item_description")]]["pi_number"]=$row[csf("pi_number")];
			$pi_data[$row[csf("pi_id")]][$row[csf("item_group")]][$row[csf("item_description")]]["pi_date"]=$row[csf("pi_date")];
			$pi_data[$row[csf("pi_id")]][$row[csf("item_group")]][$row[csf("item_description")]]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
			$pi_data[$row[csf("pi_id")]][$row[csf("item_group")]][$row[csf("item_description")]]["item_group"]=$row[csf("item_group")];
			$pi_data[$row[csf("pi_id")]][$row[csf("item_group")]][$row[csf("item_description")]]["item_description"]=$row[csf("item_description")];
			$pi_data[$row[csf("pi_id")]][$row[csf("item_group")]][$row[csf("item_description")]]["supplier_id"]=$row[csf("supplier_id")];
			$pi_data[$row[csf("pi_id")]][$row[csf("item_group")]][$row[csf("item_description")]]["remarks"]=$row[csf("remarks")];
			
			$pi_data[$row[csf("pi_id")]][$row[csf("item_group")]][$row[csf("item_description")]]["quantity"]+=$row[csf("quantity")];
			$pi_data[$row[csf("pi_id")]][$row[csf("item_group")]][$row[csf("item_description")]]["amount"]+=$row[csf("amount")];
			$pi_data[$row[csf("pi_id")]][$row[csf("item_group")]][$row[csf("item_description")]]["net_pi_amount"]+=$row[csf("net_pi_amount")];
			$pi_data[$row[csf("pi_id")]][$row[csf("item_group")]][$row[csf("item_description")]]["lc_value"]=$row[csf("lc_value")];
		}
		
	}
	
	if(count($all_pi_id)>0 || count($all_wo_id)>0)
	{
		if(count($all_pi_id)>0)
		{
			$mrr_pi_cond="";
			if($db_type==2 && count($all_pi_id)>999)
			{
				$mrr_pi_cond.=" and (";
				$all_pi_id_chunk=array_chunk($all_pi_id,999);
				foreach($all_pi_id_chunk as $pi_id)
				{
					$mrr_pi_cond.=" c.pi_wo_batch_no in(".implode(',',$pi_id).") or";
				}
				$mrr_pi_cond=chop($mrr_pi_cond,"or");
				$mrr_pi_cond.=")";
			}
			else
			{
				$mrr_pi_cond.=" and c.pi_wo_batch_no in(".implode(',',$all_pi_id).")";
			}
		}
		
		
		if(count($all_wo_id)>0)
		{
			$mrr_wo_cond="";
			if($db_type==2 && count($all_wo_id)>999)
			{
				$mrr_wo_cond.=" and (";
				$all_wo_id_chunk=array_chunk($all_wo_id,999);
				foreach($all_wo_id_chunk as $wo_id)
				{
					$mrr_wo_cond.=" c.pi_wo_batch_no in(".implode(',',$wo_id).") or";
				}
				$mrr_wo_cond=chop($mrr_wo_cond,"or");
				$mrr_wo_cond.=")";
			}
			else
			{
				$mrr_wo_cond.=" and c.pi_wo_batch_no in(".implode(',',$all_wo_id).")";
			}
		}
		
		
		if(count($all_pi_id)>0 && count($all_wo_id)>0)
		{
			$all_wo_withoutPO_id=chop($all_wo_withoutPO_id,",");
			if($all_wo_withoutPO_id!="")
			{
				$mrr_sql=" select a.id as mst_id, a.recv_number, a.receive_date, a.challan_no, a.remarks, b.booking_id, a.supplier_id, b.id as dtls_id, b.item_group_id, b.item_description, b.receive_qnty, b.amount, c.payment_over_recv  
				from inv_receive_master a, inv_transaction c, inv_trims_entry_dtls b 
				where a.id=c.mst_id and c.id=b.trans_id and a.entry_form=24 and c.receive_basis in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $mrr_pi_cond
				union all
				select a.id as mst_id, a.recv_number, a.receive_date, a.challan_no, a.remarks, b.booking_id, a.supplier_id, b.id as dtls_id, b.item_group_id, b.item_description, b.receive_qnty, b.amount , c.payment_over_recv 
				from inv_receive_master a, inv_transaction c, inv_trims_entry_dtls b 
				where a.id=c.mst_id and c.id=b.trans_id and a.entry_form=24 and c.receive_basis in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_without_order=0 $mrr_wo_cond
				union all
				select a.id as mst_id, a.recv_number, a.receive_date, a.challan_no, a.remarks, b.booking_id, a.supplier_id, b.id as dtls_id, b.item_group_id, b.item_description, b.receive_qnty, b.amount , c.payment_over_recv 
				from inv_receive_master a, inv_transaction c, inv_trims_entry_dtls b 
				where a.id=c.mst_id and c.id=b.trans_id and a.entry_form=24 and c.receive_basis in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_without_order=1 and b.booking_id in($all_wo_withoutPO_id)";
			}
			else
			{
				$mrr_sql=" select a.id as mst_id, a.recv_number, a.receive_date, a.challan_no, a.remarks, b.booking_id, a.supplier_id, b.id as dtls_id, b.item_group_id, b.item_description, b.receive_qnty, b.amount, c.payment_over_recv  
				from inv_receive_master a, inv_transaction c, inv_trims_entry_dtls b 
				where a.id=c.mst_id and c.id=b.trans_id and a.entry_form=24 and c.receive_basis in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $mrr_pi_cond
				union all
				select a.id as mst_id, a.recv_number, a.receive_date, a.challan_no, a.remarks, b.booking_id, a.supplier_id, b.id as dtls_id, b.item_group_id, b.item_description, b.receive_qnty, b.amount, c.payment_over_recv  
				from inv_receive_master a, inv_transaction c, inv_trims_entry_dtls b 
				where a.id=c.mst_id and c.id=b.trans_id and a.entry_form=24 and c.receive_basis in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_without_order=0 $mrr_wo_cond";
			}
			
		}
		else if(count($all_pi_id)>0 && count($all_wo_id)<1)
		{
			$mrr_sql=" select a.id as mst_id, a.recv_number, a.receive_date, a.challan_no, a.remarks, b.booking_id, a.supplier_id, b.id as dtls_id, b.item_group_id, b.item_description, b.receive_qnty, b.amount, c.payment_over_recv  
			from inv_receive_master a, inv_transaction c, inv_trims_entry_dtls b 
			where a.id=c.mst_id and c.id=b.trans_id and a.entry_form=24 and c.receive_basis in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $mrr_pi_cond";
		}
		else if(count($all_pi_id)<1 && count($all_wo_id)>0)
		{
			$all_wo_withoutPO_id=chop($all_wo_withoutPO_id,",");
			if($all_wo_withoutPO_id!="")
			{
				$mrr_sql="select a.id as mst_id, a.recv_number, a.receive_date, a.challan_no, a.remarks, b.booking_id, a.supplier_id, b.id as dtls_id, b.item_group_id, b.item_description, b.receive_qnty, b.amount, c.payment_over_recv 
				from inv_receive_master a, inv_transaction c, inv_trims_entry_dtls b 
				where a.id=c.mst_id and c.id=b.trans_id and a.entry_form=24 and c.receive_basis in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_without_order=0 $mrr_wo_cond
				union all 
				select a.id as mst_id, a.recv_number, a.receive_date, a.challan_no, a.remarks, b.booking_id, a.supplier_id, b.id as dtls_id, b.item_group_id, b.item_description, b.receive_qnty, b.amount, c.payment_over_recv  
				from inv_receive_master a, inv_transaction c, inv_trims_entry_dtls b 
				where a.id=c.mst_id and c.id=b.trans_id and a.entry_form=24 and c.receive_basis in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_without_order=1 and b.booking_id in($all_wo_withoutPO_id)";
			}
			else
			{
				$mrr_sql="select a.id as mst_id, a.recv_number, a.receive_date, a.challan_no, a.remarks, b.booking_id, a.supplier_id, b.id as dtls_id, b.item_group_id, b.item_description, b.receive_qnty, b.amount, c.payment_over_recv 
				from inv_receive_master a, inv_transaction c, inv_trims_entry_dtls b 
				where a.id=c.mst_id and c.id=b.trans_id and a.entry_form=24 and c.receive_basis in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_without_order=0 $mrr_wo_cond";
			}
			
			
		}
		
		
		// echo $mrr_sql;die;
		
		$mrr_result=sql_select($mrr_sql);
		$mrr_data=$pi_mrr_data=array();
		foreach($mrr_result as $row)
		{
			$mrr_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["recv_number"]=$row[csf("recv_number")];
			$mrr_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["receive_date"]=$row[csf("receive_date")];
			$mrr_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["challan_no"]=$row[csf("challan_no")];
			$mrr_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["remarks"]=$row[csf("remarks")];
			$mrr_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["booking_id"]=$row[csf("booking_id")];
			$mrr_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["dtls_id"].=$row[csf("dtls_id")].",";
			$mrr_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["item_group_id"]=$row[csf("item_group_id")];
			$mrr_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["item_description"]=$row[csf("item_description")];
			$mrr_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["receive_qnty"]+=$row[csf("receive_qnty")];
			$mrr_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["receive_amount"]+=$row[csf("amount")];
			$mrr_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["supplier_id"]=$row[csf("supplier_id")];
			
			$pi_mrr_data[$row[csf("booking_id")]]["amount"]+=$row[csf("amount")];
			if($row[csf("payment_over_recv")]!=1)
			{
				$pi_mrr_data[$row[csf("booking_id")]]["pay_amount"]+=$row[csf("amount")];
			}
			else
			{
				$pi_mrr_data[$row[csf("booking_id")]]["no_pay_amount"]+=$row[csf("amount")];
				$pi_mrr_data[$row[csf("booking_id")]]["no_recv_number"].=$row[csf("recv_number")].",";
			}
		}
		
		// echo "<pre>"; print_r($pi_mrr_data); echo "== <pre>"; print_r($all_wo_id);die;
		
		$return_pi_cond="";
		if(count($all_pi_id)>0)
		{
			if($db_type==2 && count($all_pi_id)>999)
			{
				$return_pi_cond.=" and (";
				$all_pi_id=array_chunk($all_pi_id,999);
				foreach($all_pi_id as $pi_id)
				{
					$return_pi_cond.=" b.pi_wo_batch_no in(".implode(',',$pi_id).") or";
				}
				$return_pi_cond=chop($return_pi_cond,"or");
				$return_pi_cond.=")";
			}
			else
			{
				$return_pi_cond.=" and b.pi_wo_batch_no in(".implode(',',$all_pi_id).")";
			}
		}
		
		
		$return_wo_cond="";
		if(count($all_wo_id)>0)
		{
			if($db_type==2 && count($all_wo_id)>999)
			{
				$return_wo_cond.=" and (";
				$all_wo_id=array_chunk($all_wo_id,999);
				foreach($all_wo_id as $wo_id)
				{
					$return_wo_cond.=" b.pi_wo_batch_no in(".implode(',',$wo_id).") or";
				}
				$return_wo_cond=chop($return_wo_cond,"or");
				$return_wo_cond.=")";
			}
			else
			{
				$return_wo_cond.=" and b.pi_wo_batch_no in(".implode(',',$all_wo_id).")";
			}
		}
		
		
		if(count($all_pi_id)>0 && count($all_wo_id)>0 && ($return_pi_cond!="" || $return_wo_cond!=""))
		{
			$return_sql="select a.id as mst_id, a.issue_number, a.issue_date, a.remarks, b.id as trans_id, b.receive_basis, b.pi_wo_batch_no, c.id as dtls_id, c.item_group_id, c.item_description, c.issue_qnty, c.amount
			from inv_issue_master a, inv_transaction b, inv_trims_issue_dtls c
			where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=49 and b.transaction_type=3 and b.item_category=4 and b.receive_basis in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $return_pi_cond
			union all
			select a.id as mst_id, a.issue_number, a.issue_date, a.remarks, b.id as trans_id, b.receive_basis, b.pi_wo_batch_no, c.id as dtls_id, c.item_group_id, c.item_description, c.issue_qnty, c.amount
			from inv_issue_master a, inv_transaction b, inv_trims_issue_dtls c
			where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=49 and b.transaction_type=3 and b.item_category=4 and b.receive_basis in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $return_wo_cond";
		}
		else if(count($all_pi_id)>0 && count($all_wo_id)<1 && $return_pi_cond!="")
		{
			$return_sql="select a.id as mst_id, a.issue_number, a.issue_date, a.remarks, b.id as trans_id, b.receive_basis, b.pi_wo_batch_no, c.id as dtls_id, c.item_group_id, c.item_description, c.issue_qnty, c.amount
			from inv_issue_master a, inv_transaction b, inv_trims_issue_dtls c
			where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=49 and b.transaction_type=3 and b.item_category=4 and b.receive_basis in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $return_pi_cond";
		}
		else if(count($all_pi_id)<1 && count($all_wo_id)>0 && $return_wo_cond!="")
		{
			$return_sql="select a.id as mst_id, a.issue_number, a.issue_date, a.remarks, b.id as trans_id, b.receive_basis, b.pi_wo_batch_no, c.id as dtls_id, c.item_group_id, c.item_description, c.issue_qnty, c.amount
			from inv_issue_master a, inv_transaction b, inv_trims_issue_dtls c
			where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=49 and b.transaction_type=3 and b.item_category=4 and b.receive_basis in(2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $return_wo_cond";
		}
		
		
		
		// echo $return_sql;die;
		
		$return_result=sql_select($return_sql);
		$return_data=$pi_return_data=array();
		foreach($return_result as $row)
		{
			$return_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["issue_number"]=$row[csf("issue_number")];
			$return_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["issue_date"]=$row[csf("issue_date")];
			$return_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["remarks"]=$row[csf("remarks")];
			
			$return_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["trans_id"].=$row[csf("trans_id")].",";
			$return_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["dtls_id"].=$row[csf("dtls_id")].",";
			
			$return_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["pi_wo_batch_no"]=$row[csf("pi_wo_batch_no")];
			
			$return_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["item_group_id"]=$row[csf("item_group_id")];
			$return_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["item_description"]=$row[csf("item_description")];
			$return_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["issue_qnty"]+=$row[csf("issue_qnty")];
			$return_data[$row[csf("mst_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]]["issue_amount"]+=$row[csf("amount")]/$exchange_rate_arr[$row[csf("pi_wo_batch_no")]];
			
			$pi_return_data[$row[csf("pi_wo_batch_no")]]+=$row[csf("amount")]/$exchange_rate_arr[$row[csf("pi_wo_batch_no")]];
		}
		
		
		$acceptance_pi_cond="";
		if($db_type==2 && count($acceptance_pi_id)>999)
		{
			$acceptance_pi_cond.=" and (";
			$acceptance_pi_id=array_chunk($acceptance_pi_id,999);
			foreach($acceptance_pi_id as $pi_id)
			{
				$acceptance_pi_cond.=" c.pi_id in(".implode(',',$pi_id).") or";
			}
			$acceptance_pi_cond=chop($acceptance_pi_cond,"or");
			$acceptance_pi_cond.=")";
		}
		else
		{
			$acceptance_pi_cond.=" and c.pi_id in(".implode(',',$acceptance_pi_id).")";
		}
		
		
		$acceptance_sql=" select a.id as inv_id, a.invoice_no, a.invoice_date, b.id as acc_dtls_id, c.pi_id, b.current_acceptance_value, d.goods_rcv_status, d.supplier_id, c.work_order_id 
		from  com_pi_master_details d, com_pi_item_details c
		left join com_import_invoice_dtls b on c.pi_id=b.pi_id and b.status_active=1
		left join  com_import_invoice_mst a on b.import_invoice_id=a.id and a.status_active =1
		where c.pi_id=d.id and d.item_category_id=4  $acceptance_pi_cond";
		
		// echo $acceptance_sql;die;
		//echo "";print_r($pi_mrr_data);
		$acceptance_result=sql_select($acceptance_sql);
		$acceptance_data=array();
		foreach($acceptance_result as $row)
		{
			$acceptance_data[$row[csf("pi_id")]]["inv_id"]=$row[csf("inv_id")];
			$acceptance_data[$row[csf("pi_id")]]["invoice_no"]=$row[csf("invoice_no")];
			$acceptance_data[$row[csf("pi_id")]]["invoice_date"]=$row[csf("invoice_date")];
			$acceptance_data[$row[csf("pi_id")]]["pi_num"]=$pi_num_arr[$row[csf("pi_id")]];
			$acceptance_data[$row[csf("pi_id")]]["supplier_id"]=$row[csf("supplier_id")];
			if($accep_pi_check[$row[csf("pi_id")]][$row[csf("acc_dtls_id")]]=="")
			{
				$accep_pi_check[$row[csf("pi_id")]][$row[csf("acc_dtls_id")]]=$row[csf("pi_id")];
				$acceptance_data[$row[csf("pi_id")]]["current_acceptance_value"]+=$row[csf("current_acceptance_value")];
			}
			if($row[csf("goods_rcv_status")]==1 && $wo_pi_check[$row[csf("work_order_id")]]=="")
			{
				$wo_pi_check[$row[csf("work_order_id")]]=$row[csf("work_order_id")];
				$acceptance_data[$row[csf("pi_id")]]["mrr_value"]+=$pi_mrr_data[$row[csf("work_order_id")]]["amount"];
				$acceptance_data[$row[csf("pi_id")]]["pay_mrr_value"]+=$pi_mrr_data[$row[csf("work_order_id")]]["pay_amount"];
				$acceptance_data[$row[csf("pi_id")]]["return_mrr_value"]+=$pi_return_data[$row[csf("work_order_id")]];
				$acceptance_data[$row[csf("pi_id")]]["no_pay_amount"]+=$pi_mrr_data[$row[csf("work_order_id")]]["no_pay_amount"];
				$acceptance_data[$row[csf("pi_id")]]["no_recv_number"].=$pi_mrr_data[$row[csf("work_order_id")]]["no_recv_number"].",";
				
			}
			if($row[csf("goods_rcv_status")]==2 && $acp_pi_check[$row[csf("pi_id")]]=="")
			{
				$acp_pi_check[$row[csf("pi_id")]]=$row[csf("pi_id")];
				$acceptance_data[$row[csf("pi_id")]]["mrr_value"]+=$pi_mrr_data[$row[csf("pi_id")]]["amount"];
				$acceptance_data[$row[csf("pi_id")]]["pay_mrr_value"]+=$pi_mrr_data[$row[csf("pi_id")]]["pay_amount"];
				$acceptance_data[$row[csf("pi_id")]]["return_mrr_value"]+=$pi_return_data[$row[csf("pi_id")]];
				$acceptance_data[$row[csf("pi_id")]]["no_pay_amount"]+=$pi_mrr_data[$row[csf("pi_id")]]["no_pay_amount"];
				$acceptance_data[$row[csf("pi_id")]]["no_recv_number"].=$pi_mrr_data[$row[csf("pi_id")]]["no_recv_number"].",";
			}
		}
		//echo "<pre>";print_r($acceptance_data);die;
	}
	
	if(count($btb_data)<1) {echo "No Data Found";die;}
	ob_start();
	?>
    	<div style="width:1010px; margin-left:10px;" align="left">
            <table width="1000" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
            </table>
            <table width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                	 <tr>
                        <th colspan="7">BTB LC Details</th>
                    </tr>
                	<tr>
                        <th width="50">SL</th>
                        <th width="190">Importer</th>
                        <th width="220">Supplier</th>
                        <th width="190">BTB LC No</th>
                        <th width="110">LC Date</th>
                        <th width="110">Last Shipment Date</th>  
                        <th>LC Value</th> 
                    </tr>
                </thead>
				<tbody id="tbl_btb_lc_details">
                <?
				$i=1;$p=1;
				foreach($btb_data as $btb_id=>$value)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $p;?>','<? echo $bgcolor;?>')" id="tr<? echo $p;?>">
                        <td align="center"><p><? echo $i; ?></p></td>
                        <td><p><? echo $company_arr[$value["importer_id"]]; ?>&nbsp;</p></td>
                        <td><p><? echo $supplier_arr[$value["supplier_id"]]; ?>&nbsp;</p></td>
                        <td><p><? echo $value["lc_number"]; ?>&nbsp;</p></td>
                        <td align="center"><p><? if($value["lc_date"]!="" && $value["lc_date"]!= "0000-00-00") echo change_date_format($value["lc_date"]); ?>&nbsp;</p></td>
                        <td align="center"><p><? if($value["last_shipment_date"]!="" && $value["last_shipment_date"]!= "0000-00-00") echo change_date_format($value["last_shipment_date"]); ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($value["lc_value"],2); ?></td>
                    </tr>
                	<?
					$i++;$p++;
				}
				?>
				</tbody>
            </table>
            <br>
            <table width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
                <thead>
                    <tr>
                        <th colspan="11">PI Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">PI Number</th>
                        <th width="100">Supplier Name</th>
                        <th width="80">PI Date</th>
                        <th width="100">Item Group</th>
                        <th width="120">Item Description</th>
                        <th width="100">Qnty</th>
                        <th width="80">Rate</th>                            
                        <th width="100">Value</th>
                        <th width="100">Net Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody id="tbl_pi_details">
                <?
				$i=1; 	
				foreach($pi_data as $pi_id=>$pi_val)
				{
					foreach($pi_val as $item_group_id=>$group_val)
					{
						foreach($group_val as $description=>$value)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $p;?>','<? echo $bgcolor;?>')" id="tr<? echo $p;?>">
								<td align="center"><? echo $i; ?></td>
								<td><p><? echo $value['pi_number']; ?>&nbsp;</p></td>
								<td><p><? echo $supplier_arr[$value['supplier_id']]; ?>&nbsp;</p></td>
								<td align="center"><p><? if($value['pi_date']!="" && $value['pi_date']!="0000-00-00") echo change_date_format($value['pi_date']); ?></p></td>
								<td><p><? echo $group_arr[$value['item_group']]; ?>&nbsp;</p></td>
								<td><p><? echo $value['item_description']; ?>&nbsp;</p></td>
								<td align="right"><? echo number_format($value['quantity'],2,'.',''); ?></td>
								<td align="right"><? if($value['amount']>0 && $value['quantity']>0) {echo number_format(($value['amount']/$value['quantity']),6,'.','');} else echo "0.00"; ?></td>
								<td align="right"><? echo number_format($value['amount'],2,'.',''); ?></td>
                                <td align="right"><? echo number_format($value['net_pi_amount'],2,'.',''); ?></td>
								<td><p><? echo $value['remarks']; ?>&nbsp;</p></td>
							</tr>
							<?
							$all_pi_ids[$pi_id]=$pi_id;
							$tot_pi_qnty+=$value['quantity']; 
							$tot_pi_amnt+=$value['amount'];
							$tot_pi_net_amnt+=$value['net_pi_amount'];
							
							$i++;$p++;
						}
					}
				}
				
                ?>
                </tbody>
                <tfoot>
                	<tr>
                        <th colspan="6" align="right">Total</th>
                        <th align="right" id="tot_pi_qnty"> <?php echo number_format($tot_pi_qnty,2,'.','');?></th>
                        <th>&nbsp;</th>
                        <th align="right" id="tot_pi_amnt"> <?php echo number_format($tot_pi_amnt,2,'.','');?></th>
                        <th align="right" id="tot_pi_net_amnt"> <?php echo number_format($tot_pi_net_amnt,2,'.','');?></th>
                        <th>&nbsp;</th>
                    </tr>
                    <tr>
                        <th colspan="9" align="right">Upcharge/Discount</th>
                        <th><input type="button" name="Details" id="Details" value="Details" onClick="openmypage_pinumber_details('<?= implode(",",$all_pi_ids);?>')" style="width:100px" class="formbutton" /></th>
                    </tr>
                    
                </tfoot>
            </table>
            <br>
            <table width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <tr>
                      <th colspan="11">Trims Store Receive</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">MRR No</th>
                        <th width="100">Supplier Name</th>
                        <th width="80">Recv. Date</th>
                        <th width="80">Challan No</th>
                        <th width="100">Item Group</th>
                        <th width="120">Item Description</th>
                        <th width="100">Qnty</th>
                        <th width="80">Rate</th>                            
                        <th width="100">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
				<tbody id="tbl_trims_store_receive">
                <?
				if(count($mrr_data)>0)
				{
					foreach($mrr_data as $mrr_id=>$mrr_val)
					{
						foreach($mrr_val as $item_group_id=>$group_val)
						{
							foreach($group_val as $description=>$value)
							{
								
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $p;?>','<? echo $bgcolor;?>')" id="tr<? echo $p;?>">
									<td align="center"><? echo $i; ?></td>
									<td><p><? echo $value['recv_number']; ?>&nbsp;</p></td>
									<td><p><? echo $supplier_arr[$value['supplier_id']]; ?>&nbsp;</p></td>
									<td align="center"><p><? if($value['receive_date']!="" && $value['receive_date']!="0000-00-00") echo change_date_format($value['receive_date']); ?></p></td>
									<td><p><? echo $value['challan_no']; ?>&nbsp;</p></td>
									<td><p><? echo $group_arr[$value['item_group_id']]; ?>&nbsp;</p></td>
									<td><p><? echo $value['item_description']; ?>&nbsp;</p></td>
									<td align="right"><? echo number_format($value['receive_qnty'],2,'.',''); ?></td>
									<td align="right"><? if($value['receive_amount']>0 && $value['receive_qnty']>0) {echo number_format(($value['receive_amount']/$value['receive_qnty']),6,'.','');} else echo "0.00"; ?></td>
									<td align="right"><? echo number_format($value['receive_amount'],2,'.',''); ?></td>
									<td><p><? echo $value['remarks']; ?>&nbsp;</p></td>
								</tr>
								<?
							
								$tot_recv_qnty+=$value['receive_qnty']; 
								$tot_recv_amnt+=$value['receive_amount'];
								
								$i++;$p++;
							}
						}
					}
				}
				
                       
                ?>
				</tbody>
                <tfoot>
                    <th colspan="7" align="right">Total</th>
                    <th align="right" id="tot_recv_qnty"><?php echo number_format($tot_recv_qnty,2,'.','');?></th>
                    <th>&nbsp;</th>
                    <th align="right" id="tot_recv_amnt"><?php echo number_format($tot_recv_amnt,2,'.','');?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            <br>
            <table width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
                <thead>
                    <tr>
                        <th colspan="9">Trims Store Receive Return</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Return No</th>
                        <th width="80">Return Date</th>
                        <th width="120">Item Group</th>
                        <th width="200">Item Description</th>
                        <th width="100">Qnty</th>
                        <th width="80">Rate</th>                            
                        <th width="100">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody id="tbl_trims_store_receive_return">
                <?
				$i=1;
				if(count($return_data)>0)
				{
					foreach($return_data as $return_id=>$return_val)
					{
						foreach($return_val as $item_group_id=>$group_val)
						{
							foreach($group_val as $description=>$value)
							{
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $p;?>','<? echo $bgcolor;?>')" id="tr<? echo $p;?>">
									<td align="center"><? echo $i; ?></td>
									<td><p><? echo $value['issue_number']; ?>&nbsp;</p></td>
									<td align="center"><p><? if($value['issue_date']!="" && $value['issue_date']!="0000-00-00") echo change_date_format($value['issue_date']); ?></p></td>
									<td><p><? echo $group_arr[$value['item_group_id']]; ?>&nbsp;</p></td>
									<td><p><? echo $value['item_description']; ?>&nbsp;</p></td>
									<td align="right"><? echo number_format($value['issue_qnty'],2,'.',''); ?></td>
									<td align="right"><? if($value['issue_amount']>0 && $value['issue_qnty']>0) {echo number_format(($value['issue_amount']/$value['issue_qnty']),4,'.','');} else echo "0.00"; ?></td>
									<td align="right"><? echo number_format($value['issue_amount'],2,'.',''); ?></td>
									<td><p><? echo $value['remarks']; ?>&nbsp;</p></td>
								</tr>
							<?
							
								$tot_return_qnty+=$value['issue_qnty']; 
								$tot_return_amnt+=$value['issue_amount'];
								
								$i++;$p++;
							}
						}
					}
				}
				
				
                ?>
                </tbody>
                <tfoot>
                    <th colspan="5" align="right">Total</th>
                    <th align="right" id="tot_return_qnty"> <?php echo number_format($tot_return_qnty,2,'.','');?></th>
                    <th>&nbsp;</th>
                    <th align="right" id="tot_return_amnt"> <?php echo number_format($tot_return_amnt,2,'.','');?></th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            <br>
            <table width="1000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                	<tr>
                        <th colspan="9">Acceptance Details</th>
                    </tr>
                    <tr>
                        <th width="50">SL</th>
                        <th width="140">PI Number</th>
                        <th width="120">Supplier Name</th>
                        <th width="120">Receive Value</th>
                        <th width="120">Return Value</th>
                        <th width="120">Payable Value</th>
                        <th width="120">Acceptance Date</th>
                        <th width="120">Acceptance Given</th>
                        <th>Yet To Accept</th>
                    </tr>
                </thead>
				<tbody id="tbl_acceptance_details">
                <?
				$i=1;
				foreach($acceptance_data as $pi_id=>$value)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $p;?>','<? echo $bgcolor;?>')" id="tr<? echo $p;?>">
						<td align="center"><p><? echo $i; ?></p></td>
                        <td><p><? echo $value["pi_num"]; ?></p></td>
                        <td><p><? echo $supplier_arr[$value["supplier_id"]]; ?></p></td>
                        <td align="right"><? echo number_format($value["mrr_value"],2,'.',''); ?></td>
                        <td align="right"><? echo number_format($value["return_mrr_value"],2,'.',''); ?></td>
                        
                        <td align="right" title="Payment Over Receive No MRR No: <? echo chop($value["no_recv_number"],",").". Value=".$value["no_pay_amount"];?>"><? $payable_value=$value["pay_mrr_value"]-$value["return_mrr_value"]; echo number_format($payable_value,2,'.',''); ?></td>
						<td align="center"><p><? if($value["invoice_date"]!="" && $value["invoice_date"]!="0000-00-00") echo change_date_format($value["invoice_date"]);?>&nbsp;</p></td>
						<td align="right"><? echo number_format($value["current_acceptance_value"],2,'.',''); ?></td>
						<td align="right"><? $yet_to_acc=$payable_value-$value["current_acceptance_value"]; echo number_format($yet_to_acc,2,'.',''); ?></td>
					</tr>
					<?
					$i++;$p++;
					$total_receive_value+=$value["mrr_value"];
					$total_return_value+=$value["return_mrr_value"];
					$tot_payble_value+=$payable_value;
					$tot_accept_value+=$value["current_acceptance_value"];
					$tot_yet_to_accept+=$yet_to_acc;
				}
                ?>
				</tbody>
                <tfoot>
					<tr>
                    <td align="right" colspan="3">Total</td>
                    <td align="right" id="total_receive_value"><?php echo number_format($total_receive_value,2,'.',''); ?></td>
                    <td align="right" id="total_return_value"><?php echo number_format($total_return_value,2,'.',''); ?></td>
                    <td align="right" id="tot_payble_value"><?php echo number_format($tot_payble_value,2,'.',''); ?></td>
                    <td align="right">&nbsp;</td>
                    <td align="right" id="tot_accept_value"><?php echo number_format($tot_accept_value,2,'.',''); ?></td>
                    <td align="right" id="tot_yet_to_accept"><?php echo number_format($tot_yet_to_accept,2,'.',''); ?></td>
					</tr>
                </tfoot>
            </table>
        	 <?
				echo signature_table(238, str_replace("'","",$cbo_company_name), "900px");
			 ?>
        </div>      
	<?
    foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}


if($action=="pi_dtls_popup")
{
	echo load_html_head_contents("PI Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	if($pi_ids==""){echo "No Pi Found";die;}
	//echo $pi_ids;die;

	$sql= "select id, pi_number, supplier_id, importer_id, pi_date, total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id in($pi_ids) and is_deleted=0 and status_active=1";
    //echo $sql;die;
	$sql_result=sql_select($sql);
	?>
    <table width="780" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
    <thead>
        <tr>
            <th width="40">SL</th>
            <th width="150">PI Number</th>
            <th width="150">Supplier</th>
            <th width="100">PI Amount</th>
            <th width="100">Upcharge</th>
            <th width="100">Discount</th>
            <th>Net PI Amount</th>
        </tr>
    </thead>
    <tbody>
    	<?
		$i=1;
		foreach($sql_result as $row)
		{
			if($i%2==0){
				$bgcolor = '#FFFFFF';
			}else{
				$bgcolor = '#E9F3FF';
			}
			?>
            <tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i;?>','<? echo $bgcolor;?>')" >
            	<td align="center"><? echo $i; ?></td>
                <td><? echo $row[csf("pi_number")]; ?></td>
                <td><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("total_amount")],2); ?></td>
                <td align="right"><? echo number_format($row[csf("upcharge")],2); ?></td>
                <td align="right"><? echo number_format($row[csf("discount")],2); ?></td>
                <td align="right"><? echo number_format($row[csf("net_total_amount")],2); ?></td>
            </tr>
            <?
			$i++;
		}
		?>
    </tbody>
    <tfoot>
    	<tr>
        	<th colspan="7" align="center"><input type="button" name="search" id="search" value="Close" onClick="parent.emailwindow.hide();" style="width:100px" class="formbutton" /></th>
        </tr>
    </tfoot>
</table>
    <?
	exit();
}
?>
