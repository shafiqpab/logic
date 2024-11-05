<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$seource_des_array=array(3=>"Non EPZ",4=>"Non EPZ",5=>"Abroad",6=>"Abroad",11=>"EPZ",12=>"EPZ");

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_suppler_name", 150, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b  where a.id=b.supplier_id and b.tag_company='$data' and a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "-- Select Store --", 0, "" );
	exit();
	//select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type=$data[1] order by a.store_name
}


$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );



if($action=="pi_searce_popup")
{
  	echo load_html_head_contents("PI Popup Info","../../../../", 1, 1, $unicode);
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
		<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th>Supplier</th>
						<th>Enter PI Number</th>
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
								echo create_drop_down( "cbo_supplier_id", 160,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2",'id,supplier_name', 1, '-- All Supplier --',$supplierID,'',0);
							?>
						</td>
						<td align="center">				
							<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+'<? echo $btbLc_id; ?>', 'create_pi_search_list_view', 'search_div_pi', 'pi_balance_statement_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
						</td>
					</tr> 
				</tbody>         
			</table>    
			<div align="center" style="margin-top:10px" id="search_div_pi"> </div> 
			</form>
	</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}


if($action=="create_pi_search_list_view")
{
	$ex_data = explode("_",$data);
	
	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = trim($ex_data[0]);
	$txt_search_common = trim($ex_data[1]);
	$company = trim($ex_data[2]);
	$btbLc_id = trim($ex_data[3]);
	if($btbLc_id!="")
	{
		$sql= "select a.id, a.pi_number, a.supplier_id, a.importer_id, a.pi_date, a.last_shipment_date, a.net_total_amount 
		from com_pi_master_details a, com_btb_lc_pi b 
		where a.id=b.pi_id and b.com_btb_lc_master_details_id=$btbLc_id and a.importer_id=$company and a.entry_form=165 and a.supplier_id like '$cbo_supplier' and a.pi_number like '%".$txt_search_common."%' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
		group by a.id, a.pi_number, a.supplier_id, a.importer_id, a.pi_date, a.last_shipment_date, a.net_total_amount";
	}
	else
	{
		$sql= "select id, pi_number, supplier_id, importer_id, pi_date, last_shipment_date, net_total_amount from com_pi_master_details where importer_id=$company and entry_form=165 and supplier_id like '$cbo_supplier' and pi_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1";
	}
	
	//echo $sql;
	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "PI No, Importer, Supplier Name, PI Date, Last Shipment Date, PI Value","120,150,150,80,80","780","260",0, $sql , "js_set_value", "id,pi_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "pi_number,importer_id,supplier_id,pi_date,last_shipment_date,net_total_amount", "",'','0,0,0,3,3,2') ;	
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
								echo create_drop_down( "cbo_supplier_id", 160,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2",'id,supplier_name', 1, '-- All Supplier --',$supplierID,'',0);
							?>
						</td>
						<td align="center" id="search_by_td">				
							<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+'<? echo $pi_id; ?>', 'create_lc_search_list_view', 'search_div', 'pi_balance_statement_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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
	
	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = trim($ex_data[0]);
	$txt_search_common = trim($ex_data[1]);
	$company = trim($ex_data[2]);
	$pi_id = trim($ex_data[3]);
	if($pi_id!="")
	{
		$sql= "select a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value 
		from com_btb_lc_master_details a,  com_btb_lc_pi b 
		where a.id=b.com_btb_lc_master_details_id and b.pi_id=$pi_id and a.importer_id=$company and pi_entry_form=165 and a.supplier_id like '$cbo_supplier' and a.lc_number like '%".$txt_search_common."%' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0
		group by a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value";
	}
	else
	{
		$sql= "select id, lc_number, supplier_id, importer_id, lc_date, last_shipment_date, lc_value from com_btb_lc_master_details where importer_id=$company and pi_entry_form=165 and supplier_id like '$cbo_supplier' and lc_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 and ref_closing_status=0";
	}
	
	
	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "LC No, Importer, Supplier Name, LC Date, Last Shipment Date, LC Value","120,150,150,80,80","780","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "lc_number,importer_id,supplier_id,lc_date,last_shipment_date,lc_value", "",'','0,0,0,3,3,2') ;	
	exit();
	
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_suppler_name=str_replace("'","",$cbo_suppler_name);
	$pi_id=str_replace("'","",$pi_id);
	$btbLc_id=str_replace("'","",$btbLc_id);
	$txt_date_from_pi=str_replace("'","",$txt_date_from_pi);
	$txt_date_to_pi=str_replace("'","",$txt_date_to_pi);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_receiving_status=str_replace("'","",$cbo_receiving_status);
	$rpt_type=str_replace("'","",$rpt_type);
	
	//echo $rpt_type;die;
	
	//if(str_replace("'","",$cbo_suppler_name)!="") $store_cond=" and b.store_id in(".str_replace("'","",$cbo_suppler_name).")";
	//select conversion_rate from currency_conversion_rate where con_date=(select max(con_date) as con_date   from currency_conversion_rate)
	
	
	if($rpt_type==1)
	{
		$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and id=(select max(id) as id from currency_conversion_rate where currency=2)" , "conversion_rate" );
		$sql_cond="";
		$btbLc_id_str=str_replace("'","",$btbLc_id);
		if($cbo_suppler_name>0) $sql_cond=" and c.supplier_id=$cbo_suppler_name";
		if($btbLc_id!="") $sql_cond.=" and c.id=$btbLc_id";
		if($pi_id!="") $sql_cond.=" and a.id=$pi_id";
		if($txt_date_from_pi!="" && $txt_date_to_pi!="")
		{
			$sql_cond.="  and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
			/*if($db_type==0)
			{
				$sql_cond.="  and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
			}
			else
			{
				$sql_cond.="  and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
			}*/
		}
		
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond.="  and c.lc_date between '".$txt_date_from."' and '".$txt_date_to."'";
			/*if($db_type==0)
			{
				$sql_cond.="  and c.lc_date between '".$txt_date_from."' and '".$txt_date_to."'";
			}
			else
			{
				$sql_cond.="  and c.lc_date between '".$txt_date_from."' and '".$txt_date_to."'";
			}*/
		}
		
		
		//LISTAGG(CAST(doc_submission_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY doc_submission_mst_id) as id
		if($db_type==0)
		{
			$btb_sql=sql_select("select  group_concat(a.id) as pi_id, c.id as btb_id, c.importer_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.item_category_id, c.supplier_id, c.lc_value as btb_value, c.lc_category, c.last_shipment_date, d.is_lc_sc, group_concat(d.lc_sc_id) as lc_sc_id, a.goods_rcv_status
			from com_pi_master_details a, com_btb_lc_pi b, com_btb_lc_master_details c left join com_btb_export_lc_attachment d on c.id=d.import_mst_id and d.is_deleted=0 and d.status_active=1 
			where a.id=b.pi_id and b.com_btb_lc_master_details_id=c.id and pi_entry_form=165 and c.importer_id=$cbo_company_name and c.is_deleted=0 and c.status_active=1 and c.ref_closing_status=0  $sql_cond
			group by c.id, c.importer_id, c.lc_number, c.lc_date, c.item_category_id, c.supplier_id, c.lc_value, c.lc_category, c.last_shipment_date, d.is_lc_sc, a.goods_rcv_status");
		}
		else
		{
			$btb_sql=sql_select("select LISTAGG(CAST(a.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.id) as pi_id, c.id as btb_id, c.importer_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.item_category_id, c.supplier_id, c.lc_value as btb_value, c.lc_category, c.last_shipment_date, d.is_lc_sc, LISTAGG(CAST(d.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.lc_sc_id) as lc_sc_id, a.goods_rcv_status
			from com_pi_master_details a, com_btb_lc_pi b, com_btb_lc_master_details c left join com_btb_export_lc_attachment d on c.id=d.import_mst_id and d.is_deleted=0 and d.status_active=1 
			where a.id=b.pi_id and b.com_btb_lc_master_details_id=c.id and pi_entry_form=165 and c.importer_id=$cbo_company_name and c.is_deleted=0 and c.status_active=1 and c.ref_closing_status=0  $sql_cond
			group by c.id, c.importer_id, c.lc_number, c.lc_date, c.item_category_id, c.supplier_id, c.lc_value, c.lc_category, c.last_shipment_date, d.is_lc_sc, a.goods_rcv_status");
		}
		
		//echo "select  c.id as btb_id, c.importer_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.item_category_id, c.supplier_id, c.lc_value as btb_value, c.lc_category, c.last_shipment_date, LISTAGG(CAST(d.is_lc_sc AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.is_lc_sc) as is_lc_sc, LISTAGG(CAST(d.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.lc_sc_id) as lc_sc_id	from com_pi_master_details a,  com_btb_lc_pi b, com_btb_lc_master_details c left join com_btb_export_lc_attachment d on c.id=d.import_mst_id and d.is_deleted=0 and d.status_active=1 where a.id=b.pi_id and b.com_btb_lc_master_details_id=c.id and c.item_category_id=1 and c.importer_id=$cbo_company_name and c.is_deleted=0 and c.status_active=1 $sql_cond group by c.id, c.importer_id, c.lc_number, c.lc_date, c.item_category_id, c.supplier_id, c.lc_value, c.lc_category, c.last_shipment_date";die;
		$all_pi_id=$all_btb_id=$all_lc_id=$all_sc_id="";
		foreach($btb_sql as $row)
		{
			$all_btb_id.=$row[csf("btb_id")].",";
			if($row[csf("is_lc_sc")]==0)
			{
				$all_lc_id.=implode(",",array_unique(explode(",",$row[csf("lc_sc_id")]))).",";
			}
			else
			{
				$all_sc_id.=implode(",",array_unique(explode(",",$row[csf("lc_sc_id")]))).",";
			}
		}
		//echo $all_lc_id."jahid".$all_sc_id;die;
		
		
		$all_lc_id=implode(",",array_unique(explode(",",chop($all_lc_id,","))));
		if($all_lc_id!="")
		{
			$all_lc_id_arr=explode(",",$all_lc_id);
			$lc_id=$lc_id_cond="";
			if($db_type==2 && count($all_lc_id_arr)>999)
			{
				$all_lc_id_chunk=array_chunk($all_lc_id_arr,999) ;
				
				foreach($all_lc_id_chunk as $chunk_arr)
				{
					$lc_id.=" id in(".implode(",",$chunk_arr).") or ";	
				}
				$lc_id_cond.=" and (".chop($lc_id,'or ').")";
				
			}
			else
			{
				$lc_id_cond=" and id in($all_lc_id)"; 
			}
			
			$sql_lc=sql_select("select id, export_lc_no, internal_file_no from  com_export_lc where beneficiary_name=$cbo_company_name $lc_id_cond");
			$lc_data_arr=array();
			foreach($sql_lc as $row)
			{
				$lc_data_arr[$row[csf("id")]]['lc_sc_id']=$row[csf("id")];
				$lc_data_arr[$row[csf("id")]]['lc_sc_no']=$row[csf("export_lc_no")];
				$lc_data_arr[$row[csf("id")]]['internal_file_no']=$row[csf("internal_file_no")];
			}
			
		}
		
		$all_sc_id=implode(",",array_unique(explode(",",chop($all_sc_id,","))));
		if($all_sc_id!="")
		{
			$all_sc_id_arr=explode(",",$all_sc_id);
			$sc_id=$sc_id_cond="";
			if($db_type==2 && count($all_lc_id_arr)>999)
			{
				$all_sc_id_chunk=array_chunk($all_sc_id_arr,999) ;
				
				foreach($all_sc_id_chunk as $chunk_arr)
				{
					$sc_id.=" id in(".implode(",",$chunk_arr).") or ";	
				}
				$sc_id_cond.=" and (".chop($sc_id,'or ').")";
				
			}
			else
			{
				$sc_id_cond=" and id in($all_lc_id)"; 
			}
			
			$sql_sc=sql_select("select id, contract_no, internal_file_no from com_sales_contract where beneficiary_name=$cbo_company_name $sc_id_cond");
			$sc_data_arr=array();
			foreach($sql_sc as $row)
			{
				$sc_data_arr[$row[csf("id")]]['lc_sc_id']=$row[csf("id")];
				$sc_data_arr[$row[csf("id")]]['lc_sc_no']=$row[csf("contract_no")];
				$sc_data_arr[$row[csf("id")]]['internal_file_no']=$row[csf("internal_file_no")];
			}
			
		}
		
		
		$all_btb_id=chop($all_btb_id,",");
		$all_btb_id_arr=explode(",",$all_btb_id);
		$btb_ids=$btb_ids_cond="";
		if($db_type==2 && count($all_btb_id_arr)>999)
		{
			$all_btb_id_chunk=array_chunk($all_btb_id_arr,999) ;
			foreach($all_btb_id_chunk as $chunk_arr)
			{
				$btb_ids.=" b.com_btb_lc_master_details_id in(".implode(",",$chunk_arr).") or ";	
			}
					
			$btb_ids_cond=" and (".chop($btb_ids,'or ').")";			
		}
		else
		{ 	
			$btb_ids_cond=" and b.com_btb_lc_master_details_id in($all_btb_id)"; 
		}
		// com_pi_master_details
		$pi_sql=sql_select("select a.pi_id as pi_id, a.quantity as pi_qnty, b.com_btb_lc_master_details_id as btb_lc_id, a.work_order_id, p.goods_rcv_status 
		from com_pi_master_details p, com_pi_item_details a, com_btb_lc_pi b 
		where p.id=a.pi_id and a.pi_id=b.pi_id and a.status_active=1 and a.is_deleted=0 $btb_ids_cond");
		$pi_data_arr=array();
		$pi_id_check=array();
		foreach($pi_sql as $row)
		{
			
			if($row[csf("goods_rcv_status")]==2 && $pi_id_check[$row[csf("pi_id")]]=="")
			{
				$pi_id_check[$row[csf("pi_id")]]=$row[csf("pi_id")];
				$pi_data_arr[$row[csf("btb_lc_id")]]["pi_id"].=$row[csf("pi_id")].",";
				$all_pi_id.=implode(",",array_unique(explode(",",$row[csf("pi_id")]))).",";
				$all_pi_ids[$row[csf("btb_lc_id")]].=$row[csf("pi_id")].",";
			}
			if($row[csf("goods_rcv_status")]==1 && $pi_wo_id_check[$row[csf("work_order_id")]]=="")
			{
				$pi_wo_id_check[$row[csf("work_order_id")]]=$row[csf("work_order_id")];
				$pi_data_arr[$row[csf("btb_lc_id")]]["work_order_id"].=$row[csf("work_order_id")].",";
				$all_wo_id.=implode(",",array_unique(explode(",",$row[csf("work_order_id")]))).",";
				$all_pi_ids[$row[csf("btb_lc_id")]].=$row[csf("pi_id")].",";
			}
			$pi_data_arr[$row[csf("btb_lc_id")]]["pi_qnty"]+=$row[csf("pi_qnty")];
		}
		
		//$all_pi_id=chop($all_pi_wo_id,",");
		$all_pi_id_arr=explode(",",chop($all_pi_id,","));
		$all_wo_id_arr=explode(",",chop($all_wo_id,","));
		$pi_ids=$pi_ids_cond=$pi_return_ids=$pi_ids_return_cond="";
		if(chop($all_pi_id,",") !="")
		{
			if($db_type==2 && count($all_pi_id_arr)>999)
			{
				$all_pi_id_chunk=array_chunk($all_pi_id_arr,999) ;
				foreach($all_pi_id_chunk as $chunk_arr)
				{
					$pi_ids.=" a.booking_id in(".implode(",",$chunk_arr).") or ";
					$pi_return_ids.=" a.pi_id in(".implode(",",$chunk_arr).") or ";	
				}
						
				$pi_ids_cond=" and (".chop($pi_ids,'or ').") and a.receive_basis=1";	
				//$pi_ids_return_cond=" and (".chop($pi_return_ids,'or ').")";			
			}
			else
			{ 	
				$pi_ids_cond=" and a.booking_id in($all_pi_id)  and a.receive_basis=1"; 
				//$pi_ids_return_cond=" and a.pi_id in($all_pi_id)"; 
			}
		}
		
		if(chop($all_wo_id,",") !="")
		{
			if($db_type==2 && count($all_wo_id_arr)>999)
			{
				$all_pi_id_chunk=array_chunk($all_wo_id_arr,999) ;
				foreach($all_pi_id_chunk as $chunk_arr)
				{
					$pi_ids.=" a.booking_id in(".implode(",",$chunk_arr).") or ";
					$pi_return_ids.=" a.pi_id in(".implode(",",$chunk_arr).") or ";	
				}
						
				$pi_ids_cond=" and (".chop($pi_ids,'or ').") and a.receive_basis=2";	
				//$pi_ids_return_cond=" and (".chop($pi_return_ids,'or ').")";			
			}
			else
			{ 	
				$pi_ids_cond=" and a.booking_id in(".chop($all_wo_id,",").")  and a.receive_basis=2"; 
				//$pi_ids_return_cond=" and a.pi_id in($all_pi_id)"; 
			}
		}
		
		$receive_sql=sql_select("select a.id, a.receive_basis, a.booking_id, a.receive_basis, b.order_qnty as order_qnty, b.order_amount as order_amount 
		from inv_receive_master a, inv_transaction b 
		where a.id=b.mst_id and a.receive_basis in(1,2) and a.entry_form=1 and b.item_category=1 and b.transaction_type=1 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $pi_ids_cond");
		
		$recv_data_arr=array();
		foreach($receive_sql as $row)
		{
			$recv_data_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]]["rcv_qnty"]+=$row[csf("order_qnty")];
			$recv_data_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]]["rcv_amt"]+=$row[csf("order_amount")];
			$rcv_pi_wo_id[$row[csf("id")]]=$row[csf("booking_id")]."__".$row[csf("receive_basis")];
			$all_rcv_id[$row[csf("id")]]=$row[csf("id")];
			if($rcv_dup_check[$row[csf("id")]]=="")
			{
				$rcv_dup_check[$row[csf("id")]]=$row[csf("id")];
				$all_booking_pi_id[$row[csf("booking_id")]."_".$row[csf("receive_basis")]].=$row[csf("id")].",";
			}
			
		}
		$receive_return_sql=sql_select("select a.received_id, b.transaction_date, b.cons_quantity as cons_quantity, b.cons_amount as cons_amount, b.rcv_amount as rcv_amount 
		from inv_issue_master a, inv_transaction b 
		where a.id=b.mst_id and a.entry_form=8 and b.item_category=1 and b.transaction_type=3 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.received_id in(".implode(",",$all_rcv_id).")");		
		
		$recv_rtn_data_arr=array();
		foreach($receive_return_sql as $row)
		{
			if($db_type==0)
			{
				$conversion_date=date("Y-m-d",strtotime($row[csf("transaction_date")]));
			}
			else
			{
				$conversion_date=date("d-M-y",strtotime($row[csf("transaction_date")]));
			}
			$exchange_rate=set_conversion_rate( 2, $conversion_date );
			$wo_pi_ref=explode("__",$rcv_pi_wo_id[$row[csf("received_id")]]);
			
			$recv_rtn_data_arr[$wo_pi_ref[0]][$wo_pi_ref[1]]["rcv_qnty"]+=$row[csf("cons_quantity")];
			if($row[csf("cons_amount")]>0)
			{
				$recv_rtn_data_arr[$wo_pi_ref[0]][$wo_pi_ref[1]]["rcv_amt"]+=$row[csf("cons_amount")]/$exchange_rate;
			}
			else
			{
				$recv_rtn_data_arr[$wo_pi_ref[0]][$wo_pi_ref[1]]["rcv_amt"]+=$row[csf("rcv_amount")]/$exchange_rate;
			}
			
		}
		//echo "<pre>";print_r($recv_rtn_data_arr);die;
		$btb_data_arr=array();
		if($cbo_receiving_status==1)
		{
			foreach($btb_sql as $row)
			{
				
				$rcv_btb_value=0;
				if($row[csf("goods_rcv_status")]==2)
				{
					$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("btb_id")]]["pi_id"],",")));
					foreach($pi_id_arr as $pi_id)
					{
						$rcv_btb_value+=$recv_data_arr[$pi_id][1]["rcv_amt"]-$recv_rtn_data_arr[$pi_id][1]["rcv_amt"];
					}
				}
				else
				{
					$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("btb_id")]]["work_order_id"],",")));
					foreach($pi_id_arr as $pi_id)
					{
						$rcv_btb_value+=$recv_data_arr[$pi_id][2]["rcv_amt"]-$recv_rtn_data_arr[$pi_id][2]["rcv_amt"];
					}
				}
				
				//echo "<pre>"; print_r($pi_id_arr);echo "======".$row[csf("btb_id")]."======".$rcv_btb_value;die;
				if($rcv_btb_value==0 || $rcv_btb_value=="")
				{
					$btb_data_arr[$row[csf("btb_id")]]["btb_id"]=$row[csf("btb_id")];
					$btb_data_arr[$row[csf("btb_id")]]["importer_id"]=$row[csf("importer_id")];
					$btb_data_arr[$row[csf("btb_id")]]["btb_lc_number"]=$row[csf("btb_lc_number")];
					$btb_data_arr[$row[csf("btb_id")]]["btb_lc_date"]=$row[csf("btb_lc_date")];
					$btb_data_arr[$row[csf("btb_id")]]["supplier_id"]=$row[csf("supplier_id")];
					$btb_data_arr[$row[csf("btb_id")]]["btb_value"]=$row[csf("btb_value")];
					$btb_data_arr[$row[csf("btb_id")]]["lc_category"]=$row[csf("lc_category")];
					$btb_data_arr[$row[csf("btb_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
					$btb_data_arr[$row[csf("btb_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
					$btb_data_arr[$row[csf("btb_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
					$btb_data_arr[$row[csf("btb_id")]]["rcv_btb_value"]=$rcv_btb_value;
					$btb_data_arr[$row[csf("btb_id")]]["goods_rcv_status"]=$row[csf("goods_rcv_status")];
				}
				
			}
		}		
		else if($cbo_receiving_status==2)
		{
			foreach($btb_sql as $row)
			{
				$rcv_btb_value=0;
				if($row[csf("goods_rcv_status")]==2)
				{
					$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("btb_id")]]["pi_id"],",")));
					foreach($pi_id_arr as $pi_id)
					{
						$rcv_btb_value+=$recv_data_arr[$pi_id][1]["rcv_amt"]-$recv_rtn_data_arr[$pi_id][1]["rcv_amt"];
					}
				}
				else
				{
					$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("btb_id")]]["work_order_id"],",")));
					foreach($pi_id_arr as $pi_id)
					{
						$rcv_btb_value+=$recv_data_arr[$pi_id][2]["rcv_amt"]-$recv_rtn_data_arr[$pi_id][2]["rcv_amt"];
					}
				}
				//echo "<pre>"; echo $row[csf("btb_value")]."==".$rcv_btb_value;die;
				//echo "<pre>"; print_r($pi_id_arr);echo "======".$row[csf("btb_id")]."======".$rcv_btb_value;die;
				if($row[csf("btb_value")]>$rcv_btb_value && $rcv_btb_value>0 )
				{
					$btb_data_arr[$row[csf("btb_id")]]["btb_id"]=$row[csf("btb_id")];
					$btb_data_arr[$row[csf("btb_id")]]["importer_id"]=$row[csf("importer_id")];
					$btb_data_arr[$row[csf("btb_id")]]["btb_lc_number"]=$row[csf("btb_lc_number")];
					$btb_data_arr[$row[csf("btb_id")]]["btb_lc_date"]=$row[csf("btb_lc_date")];
					$btb_data_arr[$row[csf("btb_id")]]["supplier_id"]=$row[csf("supplier_id")];
					$btb_data_arr[$row[csf("btb_id")]]["btb_value"]=$row[csf("btb_value")];
					$btb_data_arr[$row[csf("btb_id")]]["lc_category"]=$row[csf("lc_category")];
					$btb_data_arr[$row[csf("btb_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
					$btb_data_arr[$row[csf("btb_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
					$btb_data_arr[$row[csf("btb_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
					$btb_data_arr[$row[csf("btb_id")]]["rcv_btb_value"]=$rcv_btb_value;
					$btb_data_arr[$row[csf("btb_id")]]["goods_rcv_status"]=$row[csf("goods_rcv_status")];
				}
				
			}
		}
		else if($cbo_receiving_status==3)
		{
			foreach($btb_sql as $row)
			{
				$rcv_btb_value=0;
				if($row[csf("goods_rcv_status")]==2)
				{
					$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("btb_id")]]["pi_id"],",")));
					foreach($pi_id_arr as $pi_id)
					{
						$rcv_btb_value+=$recv_data_arr[$pi_id][1]["rcv_amt"]-$recv_rtn_data_arr[$pi_id][1]["rcv_amt"];
					}
				}
				else
				{
					$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("btb_id")]]["work_order_id"],",")));
					foreach($pi_id_arr as $pi_id)
					{
						$rcv_btb_value+=$recv_data_arr[$pi_id][2]["rcv_amt"]-$recv_rtn_data_arr[$pi_id][2]["rcv_amt"];
					}
				}
				
				if($row[csf("btb_value")]<=$rcv_btb_value)
				{
					$btb_data_arr[$row[csf("btb_id")]]["btb_id"]=$row[csf("btb_id")];
					$btb_data_arr[$row[csf("btb_id")]]["importer_id"]=$row[csf("importer_id")];
					$btb_data_arr[$row[csf("btb_id")]]["btb_lc_number"]=$row[csf("btb_lc_number")];
					$btb_data_arr[$row[csf("btb_id")]]["btb_lc_date"]=$row[csf("btb_lc_date")];
					$btb_data_arr[$row[csf("btb_id")]]["supplier_id"]=$row[csf("supplier_id")];
					$btb_data_arr[$row[csf("btb_id")]]["btb_value"]=$row[csf("btb_value")];
					$btb_data_arr[$row[csf("btb_id")]]["lc_category"]=$row[csf("lc_category")];
					$btb_data_arr[$row[csf("btb_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
					$btb_data_arr[$row[csf("btb_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
					$btb_data_arr[$row[csf("btb_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
					$btb_data_arr[$row[csf("btb_id")]]["rcv_btb_value"]=$rcv_btb_value;
					$btb_data_arr[$row[csf("btb_id")]]["goods_rcv_status"]=$row[csf("goods_rcv_status")];
				}
			}
		}
		else if($cbo_receiving_status==4)
		{
			foreach($btb_sql as $row)
			{
				$rcv_btb_value=0;
				if($row[csf("goods_rcv_status")]==2)
				{
					$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("btb_id")]]["pi_id"],",")));
					foreach($pi_id_arr as $pi_id)
					{
						$rcv_btb_value+=$recv_data_arr[$pi_id][1]["rcv_amt"]-$recv_rtn_data_arr[$pi_id][1]["rcv_amt"];
					}
				}
				else
				{
					$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("btb_id")]]["work_order_id"],",")));
					foreach($pi_id_arr as $pi_id)
					{
						$rcv_btb_value+=$recv_data_arr[$pi_id][2]["rcv_amt"]-$recv_rtn_data_arr[$pi_id][2]["rcv_amt"];
					}
				}
				if(($row[csf("btb_value")]>$rcv_btb_value && $rcv_btb_value>0) || $rcv_btb_value==0 || $rcv_btb_value=="")
				{
					$btb_data_arr[$row[csf("btb_id")]]["btb_id"]=$row[csf("btb_id")];
					$btb_data_arr[$row[csf("btb_id")]]["importer_id"]=$row[csf("importer_id")];
					$btb_data_arr[$row[csf("btb_id")]]["btb_lc_number"]=$row[csf("btb_lc_number")];
					$btb_data_arr[$row[csf("btb_id")]]["btb_lc_date"]=$row[csf("btb_lc_date")];
					$btb_data_arr[$row[csf("btb_id")]]["supplier_id"]=$row[csf("supplier_id")];
					$btb_data_arr[$row[csf("btb_id")]]["btb_value"]=$row[csf("btb_value")];
					$btb_data_arr[$row[csf("btb_id")]]["lc_category"]=$row[csf("lc_category")];
					$btb_data_arr[$row[csf("btb_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
					$btb_data_arr[$row[csf("btb_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
					$btb_data_arr[$row[csf("btb_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
					$btb_data_arr[$row[csf("btb_id")]]["rcv_btb_value"]=$rcv_btb_value;
					$btb_data_arr[$row[csf("btb_id")]]["goods_rcv_status"]=$row[csf("goods_rcv_status")];
				}
				
			}
		}
		else
		{
			foreach($btb_sql as $row)
			{
				$btb_data_arr[$row[csf("btb_id")]]["btb_id"]=$row[csf("btb_id")];
				$btb_data_arr[$row[csf("btb_id")]]["importer_id"]=$row[csf("importer_id")];
				$btb_data_arr[$row[csf("btb_id")]]["btb_lc_number"]=$row[csf("btb_lc_number")];
				$btb_data_arr[$row[csf("btb_id")]]["btb_lc_date"]=$row[csf("btb_lc_date")];
				$btb_data_arr[$row[csf("btb_id")]]["supplier_id"]=$row[csf("supplier_id")];
				$btb_data_arr[$row[csf("btb_id")]]["btb_value"]=$row[csf("btb_value")];
				$btb_data_arr[$row[csf("btb_id")]]["lc_category"]=$row[csf("lc_category")];
				$btb_data_arr[$row[csf("btb_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
				$btb_data_arr[$row[csf("btb_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
				$btb_data_arr[$row[csf("btb_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
				$btb_data_arr[$row[csf("btb_id")]]["goods_rcv_status"]=$row[csf("goods_rcv_status")];
			}
		}
		
		ob_start();
		?>
		<div style="width:1520px; margin-left:10px;" align="left">
			<table width="1500" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
				<tr>
				   <td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
				</tr>
			</table>
			<table width="1500" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="40">SL</th>
						<th width="70">LC Date</th>
						<th width="160">Supplier</th>
						<th width="100">BTB LC No.</th>
						<th width="100">LC Value</th>
						<th width="70">Shipment Date</th>
						<th width="100">Import Source</th>
						<th width="200">File No. & <br> (Export LC/SC No.)</th>  
						<th width="140">Receiving Company</th>
						<th width="80">LC/PI Qty</th>
						<th width="80">Total Received Qty</th>
						<th width="80">Receive Return Qty</th>
						<th width="80">Balance Qty</th>
						<th width="100">Received Value</th> 
						<th>Balance Value</th>
					</tr>
				</thead>
			</table>
			<div style="width:1520px; overflow-y:scroll; max-height:350px; overflow-x:hidden;" id="scroll_body" align="left">
			<table width="1500" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_bodyy">   
				<tbody>
				<?
				$i=1; 	
				foreach($btb_data_arr as $val)
				{
					//echo $val["goods_rcv_status"];die;
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					if($val["goods_rcv_status"]==2)
					{
						$all_pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$val["btb_id"]]["pi_id"],",")));
						$rcv_basis=1;
					}
					else
					{
						$all_pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$val["btb_id"]]["work_order_id"],",")));
						$rcv_basis=2;
					}
					
					$all_pi_id=implode(',',$all_pi_id_arr);
					$btb_pi_id=chop($all_pi_ids[$val["btb_id"]],",");
					$all_rcv_ids="";
					foreach($all_pi_id_arr as $book_pi)
					{
						$all_rcv_ids.=$all_booking_pi_id[$book_pi."_".$rcv_basis];
					}
					//echo $all_rcv_ids;die;
					//$all_booking_pi_id[$row[csf("booking_id")]."_".$row[csf("receive_basis")]].=$row[csf("id")].",";
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="40" align="center" title="<? echo $val["rcv_btb_value"] ?>"><? echo $i; ?></td>
						<td width="70" align="center"><p><? if($val["btb_lc_date"]!="" && $val["btb_lc_date"]!="0000-00-00") echo change_date_format($val["btb_lc_date"]); ?>&nbsp;</p></td>
						<td width="160"><p><? echo $supplier_arr[$val["supplier_id"]]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $val["btb_lc_number"]; ?>&nbsp;</p></td>
						<td width="100" align="right"><p><? echo number_format($val["btb_value"],2); ?>&nbsp;</p></td>
						<td width="70" align="center"><p><? if($val["last_shipment_date"]!="" && $val["last_shipment_date"]!="0000-00-00") echo change_date_format($val["last_shipment_date"]); ?>&nbsp;</p></td>
						<td width="100"><p><? echo $seource_des_array[$val["lc_category"]*1]; ?>&nbsp;</p></td>
						<td width="200"><p>
						<? 
						
						$is_lc_sc_arr=explode(",",$val["is_lc_sc"]);
						$lc_sc_id_arr=explode(",",$val["lc_sc_id"]);
						$k=0;
						$all_file=$all_lc=$all_sc="";
						foreach($lc_sc_id_arr as $lc_id)
						{
							if($is_lc_sc_arr[$k]==0)
							{
								$all_file.=$lc_data_arr[$lc_id]['internal_file_no'].",";
								$all_lc.=$lc_data_arr[$lc_id]['lc_sc_no'].",";
							}
							else
							{
								$all_file.=$sc_data_arr[$lc_id]['internal_file_no'].",";
								$all_sc.=$sc_data_arr[$lc_id]['lc_sc_no'].",";
							}
							$k++;
							
						}
						$all_file=chop($all_file,",");$all_lc=chop($all_lc,",");$all_sc=chop($all_sc,",");
						if($all_file!="") echo "File No: ".$all_file."<br/>";
						if($all_lc!="") echo "Lc No: ".$all_lc." ";if($all_sc!="") echo "Sc No: ".$all_sc." ";
						?>&nbsp;</p></td>  
						<td width="140"><p><? echo $company_arr[$val["importer_id"]];?>&nbsp;</p></td>
						<td width="80" align="right"><a href="##" onClick="openmypage_popup('<? echo $val["importer_id"]; ?>','<? echo $btb_pi_id; ?>','PI Info','pi_popup');" ><? echo number_format($pi_data_arr[$val["btb_id"]]["pi_qnty"],2); $total_pi_qnty+=$pi_data_arr[$val["btb_id"]]["pi_qnty"]; ?></a></td>
						<td width="80" align="right"><a href="##" onClick="openmypage_popup('<? echo $val["importer_id"]."_".$rcv_basis; ?>','<? echo $all_pi_id; ?>','Receive Info','receive_popup');" >
						<? 
						$rcv_qnty=$rcv_return_qnty=$rcv_value=$rcv_return_value=0;
						foreach($all_pi_id_arr as $pi_id)
						{
							$rcv_qnty+=$recv_data_arr[$pi_id][$rcv_basis]["rcv_qnty"];
							$rcv_value+=$recv_data_arr[$pi_id][$rcv_basis]["rcv_amt"];
							$rcv_return_qnty+=$recv_rtn_data_arr[$pi_id][$rcv_basis]["rcv_qnty"];
							$rcv_return_value+=$recv_rtn_data_arr[$pi_id][$rcv_basis]["rcv_amt"];
						}
						echo number_format($rcv_qnty,2);
						$balance_qnty=($pi_data_arr[$val["btb_id"]]["pi_qnty"]-($rcv_qnty-$rcv_return_qnty));//$currency_rate
						$balance_value=($val["btb_value"]-($rcv_value-$rcv_return_value));
						$total_rcv_qnty+=$rcv_qnty;
						?>
						</a>
						</td>
						<td width="80" align="right"><a href="##" onClick="openmypage_popup('<? echo $val["importer_id"]; ?>','<? echo chop($all_rcv_ids,","); ?>','Receive Return Info','receive_return_popup');"><? echo number_format($rcv_return_qnty,2); $total_rcv_return_qnty+=$rcv_return_qnty; ?></a></td>
						<td width="80" align="right"><? echo number_format($balance_qnty,2);  $total_balance_qnty+=$balance_qnty; ?></td>
						<td width="100" align="right"><? echo number_format($rcv_value,2); $total_rcv_value+=$rcv_value; ?></td> 
						<td align="right" title="<? echo $val["btb_value"]."=".$rcv_value."=".$rcv_return_value; ?>"><? echo number_format($balance_value,2); $total_balance_value+=$balance_value; ?></td> 
					</tr>
					<?
					$i++;
				}
				?>
				</tbody>         	
			</table>
			</div>
			<table width="1500" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<tfoot>
					<tr>
						<th width="40">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="160">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="200">&nbsp;</th>  
						<th width="140">Total</th>
						<th width="80" align="right" id="value_total_pi_qnty"><? echo number_format($total_pi_qnty,2); ?></th>
						<th width="80" align="right" id="value_total_rcv_qnty"><? echo number_format($total_rcv_qnty,2); ?></th>
						<th width="80" align="right" id="value_total_rcv_return_qnty"><? echo number_format($total_rcv_return_qnty,2); ?></th>
						<th width="80" align="right" id="value_total_balance_qty"><? echo number_format($total_balance_qnty,2); ?></th>
						<th width="100" align="right" id="value_total_rcev_value"><? echo number_format($total_rcv_value,2); ?></th> 
						<th align="right" id="value_total_bal_value"><? echo number_format($total_balance_value,2); ?></th>
					</tr>
				</tfoot>
			</table>
		</div>      
		<?
	}
	else
	{
		$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer ",'id','buyer_name');
		$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
		$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
		$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and id=(select max(id) as id from currency_conversion_rate where currency=2)" , "conversion_rate" );
		$sql_cond=""; $pi_closing_cond=""; $lc_closing_cond="";
		$btbLc_id_str=str_replace("'","",$btbLc_id);
		$cbo_closing_status=str_replace("'","",$cbo_closing_status);
		if($cbo_suppler_name>0) $sql_cond=" and a.supplier_id=$cbo_suppler_name";
		if($btbLc_id!="") $sql_cond.=" and c.id=$btbLc_id";
		if($pi_id!="") $sql_cond.=" and a.id=$pi_id";
		
		/*if($cbo_closing_status==2) {
			$pi_closing_cond=" and a.ref_closing_status=1";
			$lc_closing_cond=" and c.ref_closing_status=1";
		} else if($cbo_closing_status==3)  {
			$pi_closing_cond=" and a.ref_closing_status=0";
			$lc_closing_cond=" and c.ref_closing_status=0";
		}
		else {
			$pi_closing_cond="";
			$lc_closing_cond="";
		}*/
		if($txt_date_from_pi!="" && $txt_date_to_pi!="")
		{
			$sql_cond.="  and a.pi_date between '".$txt_date_from_pi."' and '".$txt_date_to_pi."'";
			/*if($db_type==0)
			{
				$sql_cond.="  and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
			}
			else
			{
				$sql_cond.="  and a.pi_date between '".$txt_date_from."' and '".$txt_date_to."'";
			}*/
		}
		
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond.="  and c.lc_date between '".$txt_date_from."' and '".$txt_date_to."'";
			/*if($db_type==0)
			{
				$sql_cond.="  and c.lc_date between '".$txt_date_from."' and '".$txt_date_to."'";
			}
			else
			{
				$sql_cond.="  and c.lc_date between '".$txt_date_from."' and '".$txt_date_to."'";
			}*/
		}
		
		
		
		$sql_sc=sql_select("select id, contract_no, internal_file_no from com_sales_contract where beneficiary_name=$cbo_company_name");
		$sc_data_arr=array();
		foreach($sql_sc as $row)
		{
			$sc_data_arr[$row[csf("id")]]['lc_sc_id']=$row[csf("id")];
			$sc_data_arr[$row[csf("id")]]['lc_sc_no']=$row[csf("contract_no")];
			$sc_data_arr[$row[csf("id")]]['internal_file_no']=$row[csf("internal_file_no")];
		}
		
		$sql_lc=sql_select("select id, export_lc_no, internal_file_no from  com_export_lc where beneficiary_name=$cbo_company_name");
		$lc_data_arr=array();
		foreach($sql_lc as $row)
		{
			$lc_data_arr[$row[csf("id")]]['lc_sc_id']=$row[csf("id")];
			$lc_data_arr[$row[csf("id")]]['lc_sc_no']=$row[csf("export_lc_no")];
			$lc_data_arr[$row[csf("id")]]['internal_file_no']=$row[csf("internal_file_no")];
			
		}
		
		//LISTAGG(CAST(doc_submission_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY doc_submission_mst_id) as id
		if($db_type==0)
		{
			$btb_sql="SELECT a.id as pi_id, a.goods_rcv_status, a.importer_id, a.supplier_id, a.item_category_id, a.pi_number, a.buyer_id, a.pi_date, a.net_total_amount as pi_value, a.remarks, a.source as lc_category, c.ref_closing_status as lc_closing, a.ref_closing_status as pi_closing, a.last_shipment_date, p.id as pi_dtls_id, p.count_name, p.yarn_composition_item1, p.yarn_type, p.color_id, c.id as btb_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, p.quantity as pi_item_qtny, p.net_pi_amount as pi_item_value, group_concat(d.is_lc_sc) as is_lc_sc, group_concat(d.lc_sc_id) as lc_sc_id, group_concat(p.work_order_id) as work_order_id
			from com_pi_item_details p, com_pi_master_details a left join com_btb_lc_pi b on a.id=b.pi_id left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id left join com_btb_export_lc_attachment d on c.id=d.import_mst_id and d.is_deleted=0 and d.status_active=1 $lc_closing_cond
			where p.pi_id=a.id and a.entry_form=165 and a.importer_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and p.is_deleted=0 and p.status_active=1  $sql_cond $pi_closing_cond
			group by a.id, a.goods_rcv_status, a.importer_id, a.supplier_id, a.item_category_id, a.pi_number, a.buyer_id, a.pi_date, a.net_total_amount, a.remarks, a.source, a.ref_closing_status, c.ref_closing_status, a.last_shipment_date, p.id, p.count_name, p.yarn_composition_item1, p.yarn_type, p.color_id, c.id, c.lc_number, c.lc_date , p.quantity, p.net_pi_amount order by a.id";
		}
		else
		{
			$btb_sql="SELECT a.id as pi_id, a.goods_rcv_status, a.importer_id, a.supplier_id, a.item_category_id, a.pi_number, a.buyer_id, a.pi_date, a.net_total_amount as pi_value, a.remarks, a.source as lc_category, c.ref_closing_status as lc_closing , a.ref_closing_status as pi_closing, a.last_shipment_date, p.id as pi_dtls_id, p.count_name, p.yarn_composition_item1, p.yarn_type, p.color_id, c.id as btb_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, p.quantity as pi_item_qtny, p.net_pi_amount as pi_item_value, rtrim(xmlagg(xmlelement(e,d.is_lc_sc,',').extract('//text()') order by d.is_lc_sc).GetClobVal(),',') as is_lc_sc, rtrim(xmlagg(xmlelement(e,d.lc_sc_id,',').extract('//text()') order by d.lc_sc_id).GetClobVal(),',') as lc_sc_id, listagg(cast(p.work_order_id as varchar(4000)),',') within group (order by p.id) as work_order_id
			from com_pi_item_details p, com_pi_master_details a left join com_btb_lc_pi b on a.id=b.pi_id left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id left join com_btb_export_lc_attachment d on c.id=d.import_mst_id and d.is_deleted=0 and d.status_active=1 $lc_closing_cond
			where p.pi_id=a.id and a.entry_form=165 and a.importer_id=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and p.is_deleted=0 and p.status_active=1 $sql_cond $pi_closing_cond
			group by a.id, a.goods_rcv_status, a.importer_id, a.supplier_id, a.item_category_id, a.pi_number, a.buyer_id, a.pi_date, a.net_total_amount, a.remarks, a.source, a.ref_closing_status, c.ref_closing_status, a.last_shipment_date, p.id, p.count_name, p.yarn_composition_item1, p.yarn_type, p.color_id, c.id, c.lc_number, c.lc_date , p.quantity, p.net_pi_amount order by a.id";
		}
		
		//echo $btb_sql;//die;
		
		$btb_result=sql_select($btb_sql);
		$btb_result=sql_select($btb_sql); $btb_val_arr=array();
		foreach($btb_result as $row)
		{
			$btb_val_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_item_qtny"]+=$row[csf("pi_item_qtny")];
			$btb_val_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_item_value"]+=$row[csf("pi_item_value")];
		}
		$receive_sql=sql_select("select a.id as rcv_id, a.booking_id as pi_id, a.receive_basis, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color, b.order_qnty as order_qnty, b.order_amount as order_amount 
		from inv_receive_master a,  inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis in(1,2) and a.entry_form=1 and b.item_category=1 and c.item_category_id=1 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$recv_data_arr=array();
		foreach($receive_sql as $row)
		{
			$recv_data_arr[$row[csf("pi_id")]][$row[csf("receive_basis")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_qnty"]+=$row[csf("order_qnty")];
			$recv_data_arr[$row[csf("pi_id")]][$row[csf("receive_basis")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_amt"]+=$row[csf("order_amount")];
			$rcv_book_id[$row[csf("rcv_id")]]=$row[csf("pi_id")]."__".$row[csf("receive_basis")];
			if($rcv_id_check[$row[csf("rcv_id")]]=="")
			{
				$rcv_id_check[$row[csf("rcv_id")]]=$row[csf("rcv_id")];
				$book_rcv_id[$row[csf("pi_id")]."__".$row[csf("receive_basis")]].=$row[csf("rcv_id")].",";
			}
		}
		
		$receive_return_sql=sql_select("select a.pi_id, a.received_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color, b.cons_quantity as cons_quantity, b.rcv_amount as cons_amount 
		from inv_issue_master a,  inv_transaction b, product_details_master c  
		where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and c.item_category_id=1  and a.entry_form=8 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$recv_rtn_data_arr=array();
		foreach($receive_return_sql as $row)
		{
			$rcv_data_ref=explode("__",$rcv_book_id[$row[csf("received_id")]]);
			$wo_pi_id=$rcv_data_ref[0];
			$receive_basis=$rcv_data_ref[1];
			$recv_rtn_data_arr[$wo_pi_id][$receive_basis][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_qnty"]+=$row[csf("cons_quantity")];
			$recv_rtn_data_arr[$wo_pi_id][$receive_basis][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_amt"]+=$row[csf("cons_amount")];
		}
		
		//echo "<pre>";print_r($btb_val_arr);die;
		
		$btb_data_arr=array();
		foreach($btb_result as $row)
		{
			$btb_val=$rcv_btb_qnty=$rcv_btb_value=$rtn_btb_qnty=$rtn_btb_value=$bal_rcv_value="";
			$btb_val=$btb_val_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_item_value"];
			//echo $row[csf("pi_id")]."=".$row[csf("count_name")]."=".$row[csf("yarn_composition_item1")]."=".$row[csf("yarn_type")]."=".$row[csf("color_id")]."**".$btb_val;die;
			$wo_pi_id="";
			if($row[csf("goods_rcv_status")]==2)
			{
				$rcv_btb_qnty=$recv_data_arr[$row[csf("pi_id")]][1][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["rcv_qnty"];
				$rcv_btb_value=$recv_data_arr[$row[csf("pi_id")]][1][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["rcv_amt"];
				
				$rtn_btb_qnty=$recv_rtn_data_arr[$row[csf("pi_id")]][1][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["rcv_qnty"];
				$rtn_btb_value=$recv_rtn_data_arr[$row[csf("pi_id")]][1][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["rcv_amt"];
				$receive_basis=1;
				$wo_pi_id=$row[csf("pi_id")];
				$book_rcv_ids=chop($book_rcv_id[$row[csf("pi_id")]."__1"],",");
			}
			else
			{
				$wo_pi_id=implode(",",array_unique(explode(",",$row[csf("work_order_id")])));
				$work_order_id_arr=array_unique(explode(",",$row[csf("work_order_id")]));
				foreach($work_order_id_arr as $wo_id)
				{
					$book_rcv_ids.=chop($book_rcv_id[$wo_id."__2"],",").",";
					$rcv_btb_qnty=$recv_data_arr[$wo_id][2][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["rcv_qnty"];
					$rcv_btb_value=$recv_data_arr[$wo_id][2][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["rcv_amt"];
					
					$rtn_btb_qnty=$recv_rtn_data_arr[$wo_id][2][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["rcv_qnty"];
					$rtn_btb_value=$recv_rtn_data_arr[$wo_id][2][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["rcv_amt"];
				}
				$receive_basis=2;
				$book_rcv_ids=chop($book_rcv_ids,",");
			}
			//echo "<pre>"; print_r($pi_id_arr);echo "======".$row[csf("btb_id")]."======".$rcv_btb_value;die;
			$bal_rcv_value=="";
			$rtn_btb_value=$rtn_btb_value/$currency_rate;
			$bal_rcv_value=$rcv_btb_value-$rtn_btb_value; 
			$data_appear=0;
			//echo $cbo_receiving_status."=".$btb_val."=".$bal_rcv_value."=".test;die;
			if($cbo_receiving_status==1 && $bal_rcv_value=="") $data_appear=1;
			else if($cbo_receiving_status==2 && $btb_val>$bal_rcv_value && $bal_rcv_value>0 ) $data_appear=1;
			else if($cbo_receiving_status==3 && $btb_val<=$bal_rcv_value  && $bal_rcv_value>0) $data_appear=1;
			else if($cbo_receiving_status==4 && ($btb_val>$bal_rcv_value)) $data_appear=1;
			else if($cbo_receiving_status==0) $data_appear=1;
			//echo $cbo_receiving_status."=".$data_appear."=".$bal_rcv_value."=".test;die;
			if($data_appear)
			{
				if ($check_pi_dtls_id[$row[csf("pi_dtls_id")]]=='')
				{
					$check_pi_dtls_id[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["book_rcv_ids"]=$book_rcv_ids;
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["receive_basis"]=$receive_basis;
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["goods_rcv_status"]=$row[csf("goods_rcv_status")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["wo_pi_id"]=$wo_pi_id;
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_id"]=$row[csf("pi_id")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["lc_closing"]=$row[csf("lc_closing")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_closing"]=$row[csf("pi_closing")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_number"]=$row[csf("pi_number")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_buyer"]=$row[csf("buyer_id")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_date"]=$row[csf("pi_date")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_value"]=$row[csf("pi_value")];
					
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["count_name"]=$row[csf("count_name")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["yarn_composition_item1"]=$row[csf("yarn_composition_item1")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["yarn_type"]=$row[csf("yarn_type")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["color_id"]=$row[csf("color_id")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["btb_id"]=$row[csf("btb_id")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["importer_id"]=$row[csf("importer_id")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["btb_lc_number"]=$row[csf("btb_lc_number")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["btb_lc_date"]=$row[csf("btb_lc_date")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["supplier_id"]=$row[csf("supplier_id")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["btb_value"]=$row[csf("btb_value")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["lc_category"]=$row[csf("lc_category")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")]->load();
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")]->load();
					
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_item_qtny"]+=$row[csf("pi_item_qtny")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_item_rate"]=$row[csf("pi_item_value")]/$row[csf("pi_item_qtny")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_item_value"]+=$row[csf("pi_item_value")];
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["remarks"]=$row[csf("remarks")];
					
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["rcv_btb_qnty"]=$rcv_btb_qnty;
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["rcv_btb_value"]=$rcv_btb_value;
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["rtn_btb_qnty"]=$rtn_btb_qnty;
					$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["rtn_btb_value"]=$rtn_btb_value;
				}	
			}
			
		}
		
		//echo "<pre>". count($btb_data_arr);
		//echo "<pre>";print_r($btb_data_arr);die;
		//echo $btb_sql;die;
		
		ob_start();
		?>
		<div style="width:2300px; margin-left:10px;" align="left">
			<table width="2280" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
				<tr>
				   <td align="center" width="100%" colspan="24" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
				</tr>
			</table>
			<table width="2280" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="70">LC Date</th>
                        <th width="70">PI Date</th>
						<th width="130">Supplier</th>
						<th width="100">PI NO</th>
						<th width="100">Buyer</th>
                        <th width="100">BTB LC No.</th>
						<th width="80">LC Value</th>
						<th width="70">Shipment Date</th>
						<th width="70">Import Source</th>
						<th width="140">File No. & <br> (Export LC/SC No.)</th>  
						<th width="120">Receiving Company</th>
                        <th width="50">Count</th>
						<th width="140">Composition</th>
						<th width="60">Yarn Type</th>
						<th width="80">Color</th>
						<th width="70">LC/PI Qty</th>
						<th width="70">Rate</th>
						<th width="80">PI Value</th>
						<th width="70">Total Received Qty</th>
						<th width="70">Receive Return Qty</th>  
                        <th width="70">Balance Qty.</th>
                        <th width="80">Pipeline Qty.</th>
						<th width="80">Received Value</th>
						<th width="80">Balance Value</th>
						<th width="100">Closing Status</th>
						<th>Remarks</th>
					</tr>
				</thead>
			</table>
			<div style="width:2300px; overflow-y:scroll; max-height:350px; overflow-x:hidden;" id="scroll_body" align="left">
			<table width="2280" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">   
            <tbody>
            <?
            $i=1; $closeing_status=array(0=>"Pending",1=>"Closed");
            foreach($btb_data_arr as $pi_id=>$pi_val)
            {
                foreach($pi_val as $count_id=>$count_val)
                {
                    foreach($count_val as $composition_id=>$composition_val)
                    {
                        foreach($composition_val as $type_id=>$type_val)
                        {
                            foreach($type_val as $color_id=>$val)
                            {
                                if ($i%2==0)  
                                    $bgcolor="#E9F3FF";
                                else
                                    $bgcolor="#FFFFFF";
                               	$closing_condition=0; //2=closed 3=pending
                            	if($cbo_closing_status==2){
                            		if($val["pi_closing"]==1 || $val["lc_closing"]==1){
                            			$closing_condition=1;
                            		}
                            	}
                            	else if($cbo_closing_status==3){
                            		if($val["pi_closing"]==0 && $val["lc_closing"]==0){
                            			$closing_condition=1;
                            		}
                            	}
                            	else{
                            		if($val["pi_closing"]==1 || $val["pi_closing"]==0){
                            			$closing_condition=1;
                            		}
                            	}
                                
                            	if($closing_condition==1)
                            	{
                                	//echo $val["pi_closing"].'=='.$val["lc_closing"];
	                                $pi_value=$pi_num=$pi_date=$pi_supplier=$lc_num=$lc_data=$lc_ship_date=$lc_source=$lc_file_ref=$lc_receive_com="";
	                                if($pi_check[$pi_id]== "")
	                                {
	                                    $pi_check[$pi_id]=$pi_id;
	                                    $pi_value=$val["pi_value"];
	                                }
									
									$pi_num=$val["pi_number"];
									$pi_buyer=$buyer_name_arr[$val["pi_buyer"]];
									$pi_date=$val["pi_date"];
									$pi_supplier=$val["supplier_id"];
									$lc_num=$val["btb_lc_number"];
									$lc_data=$val["btb_lc_date"];
									$lc_ship_date=$val["last_shipment_date"];
									$lc_source=$val["lc_category"]*1;
									$lc_receive_com=$val["importer_id"];
									
									$is_lc_sc_arr=explode(",",$val["is_lc_sc"]);
									$lc_sc_id_arr=explode(",",$val["lc_sc_id"]);
									$k=0;
									$all_file=$all_lc=$all_sc="";
									foreach($lc_sc_id_arr as $lc_id)
									{
										if($is_lc_sc_arr[$k]==0)
										{
											$all_file.=$lc_data_arr[$lc_id]['internal_file_no'].",";
											$all_lc.=$lc_data_arr[$lc_id]['lc_sc_no'].",";
										}
										else
										{
											$all_file.=$sc_data_arr[$lc_id]['internal_file_no'].",";
											$all_sc.=$sc_data_arr[$lc_id]['lc_sc_no'].",";
										}
										$k++;
									}
									$all_file=chop($all_file,",");$all_lc=chop($all_lc,",");$all_sc=chop($all_sc,",");
									if($all_file!="") $lc_file_ref="File No: ".$all_file."<br/>";
									if($all_lc!="") $lc_file_ref.="Lc No: ".$all_lc." ";
									if($all_sc!="") $lc_file_ref.="Sc No: ".$all_sc." ";
	                                
	                                $balance_qnty=0; $balance_value=0; $rcv_value=0; $lc_pi_closed=0; 
	                                $rcv_value=($val["rcv_btb_value"]-$val["rtn_btb_value"]);
	                                $balance_qnty=$pipeline_qnty=($val["pi_item_qtny"]-($val["rcv_btb_qnty"]-$val["rtn_btb_qnty"]));
	                                $balance_value=($val["pi_item_value"]-$rcv_value);
	                                  
	                                if($val["pi_closing"]==1 || $val["lc_closing"]==1) {
	                                	$lc_pi_closed=1;$pipeline_qnty=0;
	                                }
	                                ?>
	                                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
	                                    <td width="30" align="center" title="<? echo $val["rcv_btb_value"] ?>"><? echo $i; ?></td>
	                                    <td width="70" align="center"><p><? if($lc_data!="" && $lc_data!="0000-00-00") echo change_date_format($lc_data); ?>&nbsp;</p></td>
	                                    <td width="70" align="center"><p><? if($pi_date!="" && $pi_date!="0000-00-00") echo change_date_format($pi_date); ?>&nbsp;</p></td>
	                                    <td width="130"><p><? echo $supplier_arr[$pi_supplier]; ?>&nbsp;</p></td>
	                                    <td width="100"><p><? echo $pi_num; ?>&nbsp;</p></td>
	                                    <td width="100"><p><? echo $pi_buyer; ?>&nbsp;</p></td>
	                                    <td width="100"><p><? echo $lc_num; ?>&nbsp;</p></td>
	                                    <td width="80" align="right"><? echo number_format($val["pi_item_value"],2);?></td>
	                                    <td width="70" align="center"><p><? if($lc_ship_date!="" && $lc_ship_date!="0000-00-00") echo change_date_format($lc_ship_date); ?>&nbsp;</p></td>
	                                    <td width="70" title="<? echo $lc_source; ?>"><p><? echo $source[$lc_source]; ?>&nbsp;</p></td>
	                                    <td width="140"><p><? echo $lc_file_ref; ?>&nbsp;</p></td>  
	                                    <td width="120"><p><? echo $company_arr[$lc_receive_com];?>&nbsp;</p></td>
	                                    <td width="50" align="center"><p><? echo $yarn_count_arr[$val["count_name"]]; ?>&nbsp;</p></td>
	                                    <td width="140"><p><? echo $composition[$val["yarn_composition_item1"]]; ?>&nbsp;</p></td>
	                                    <td width="60"><p><? echo $yarn_type[$val["yarn_type"]]; ?>&nbsp;</p></td>
	                                    <td width="80"><p><? echo $color_arr[$val["color_id"]]; ?>&nbsp;</p></td>
	                                    <td width="70" align="right"><? echo number_format($val["pi_item_qtny"],2); ?></td>
	                                    <td width="70" align="right"><? echo number_format($val["pi_item_value"]/$val["pi_item_qtny"],2); //number_format($val["pi_item_rate"],2); ?></td>
	                                    <td width="80" align="right"><? echo number_format($val["pi_item_value"],2); ?></td>
	                                    <td width="70" align="right"><a href="##" onClick="openmypage_mrr('<? echo $val["importer_id"]; ?>','<? echo $val["wo_pi_id"]."__".$val["receive_basis"]; ?>','<? echo $count_id; ?>','<? echo $composition_id; ?>','<? echo $type_id; ?>','<? echo $color_id; ?>','Receive Info','receive_mrr_popup');"><?  echo number_format($val["rcv_btb_qnty"],2);?></a></td>
	                                    <td width="70" align="right"><a href="##" onClick="openmypage_mrr('<? echo $val["importer_id"]; ?>','<? echo $val["book_rcv_ids"]; ?>','<? echo $count_id; ?>','<? echo $composition_id; ?>','<? echo $type_id; ?>','<? echo $color_id; ?>','Return Info','return_mrr_popup');"><? echo number_format($val["rtn_btb_qnty"],2); ?></a></td>
	                                    <td width="70" align="right"><? echo number_format($balance_qnty,2);  ?></td>
	                                    <td width="80" align="right"><? echo number_format($pipeline_qnty,2);  ?></td>
	                                    <td width="80" align="right"><? echo number_format($rcv_value,2); ?></td> 
	                                    <td width="80" align="right"><? echo number_format($balance_value,2); ?></td>
	                                    <td width="100"><p><? echo $closeing_status[$lc_pi_closed]; ?>&nbsp;</p></td> 
	                                    <td><p><? echo $val["remarks"]; ?>&nbsp;</p></td>

	                                    
	                                </tr>
	                                <?
	                                $i++;
	                                $total_pi_value+=$pi_value;
	                                $total_pi_item_qtny+=$val["pi_item_qtny"];
	                                $total_pi_item_value+=$val["pi_item_value"];
	                                $total_rcv_btb_qnty+=$val["rcv_btb_qnty"];
	                                $total_rtn_btb_qnty+=$val["rtn_btb_qnty"];
	                                $total_balance_qnty+=$balance_qnty;
	                                $total_pipeline_qnty+=$pipeline_qnty;
	                                $total_rcv_value+=$rcv_value;
	                                $total_balance_value+=$balance_value;
                            	}
                            }
                        }
                    }
                }
                
            }
            ?>
            </tbody>         	
			</table>
			</div>
			<table width="2280" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <tfoot>
                <tr>
                    <th width="30">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="130">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80" id="value_total_pi_value"><? echo number_format($total_pi_value,2); ?></th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="140">&nbsp;</th>  
                    <th width="120">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="140">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="70" id="value_total_pi_item_qtny"><? echo number_format($total_pi_item_qtny,2); ?></th>
                    <th width="70">&nbsp;</th>
                    <th width="80" id="value_total_pi_item_value"><? echo number_format($total_pi_item_value,2); ?></th>
                    <th width="70" id="value_total_rcv_btb_qnty"><? echo number_format($total_rcv_btb_qnty,2); ?></th>
                    <th width="70" id="value_total_rtn_btb_qnty"><? echo number_format($total_rtn_btb_qnty,2); ?></th> 
                    <th width="70" id="value_total_balance_qnty"><? echo number_format($total_balance_qnty,2); ?></th>
                    <th width="80" id="value_total_pipeline_qnty"><? echo number_format($total_pipeline_qnty,2); ?></th>
                    <th width="80" id="value_total_rcv_value"><? echo number_format($total_rcv_value,2); ?></th>
                    <th width="80" id="value_total_balance_value"><? echo number_format($total_balance_value,2); ?></th>
                    <th width="100"></th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
			</table>
		</div>      
		<?
	}
	
	
	
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
	echo "$total_data####$filename####$rpt_type";
	exit();
}


if($action=="receive_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_ref=explode("_",str_replace("'","",$company_id));
	$company_id=$company_ref[0];
	$rcv_basis=$company_ref[1];
	//echo $company_id."=".$rcv_basis;die;
	$pi_id=str_replace("'","",$pi_id);
	
	
	/*$pi_dtsl_sql=sql_select("select pi_id, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, yarn_composition_percentage2, yarn_type from  com_pi_item_details where pi_id in($pi_id)");
	$pi_data_all=array();
	foreach($pi_dtsl_sql as $row)
	{
		$pi_data_all[$row[csf("pi_id")]]["count_name"].=$row[csf("count_name")].',';
		$pi_data_all[$row[csf("pi_id")]]["yarn_composition_item1"]=$row[csf("yarn_composition_item1")];
		$pi_data_all[$row[csf("pi_id")]]["yarn_composition_percentage1"]=$row[csf("yarn_composition_percentage1")];
		$pi_data_all[$row[csf("pi_id")]]["yarn_composition_item2"]=$row[csf("yarn_composition_item2")];
		$pi_data_all[$row[csf("pi_id")]]["yarn_composition_percentage2"]=$row[csf("yarn_composition_percentage2")];
		$pi_data_all[$row[csf("pi_id")]]["yarn_type"]=$row[csf("yarn_type")];
	}
	$prod_dtsl_sql=sql_select("select a.pi_wo_batch_no as pi_id,b.yarn_count_id as count_name from  product_details_master b,inv_transaction a where a.prod_id=b.id and a.pi_wo_batch_no in($pi_id) and a.item_category=1 and a.transaction_type=1 group by a.pi_wo_batch_no,b.yarn_count_id ");
	$prod_data_all=array();
	foreach($prod_dtsl_sql as $row)
	{
		$prod_data_all[$row[csf("pi_id")]]["count_name"].=$row[csf("count_name")].',';
		
	}*/
	
	//var_dump($composition[1]);die;
	//echo $pi_dtsl_sql;die;
	?>
	<script>
	
	/*function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}*/	
	
	</script>	
    <div id="report_container" align="center" style="width:1050px">
	<fieldset style="width:1050px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="1050" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                        <th width="100">PI No.</th>
                        <th width="70">Recv. Date</th>
                        <th width="130">MRR No</th>
                        <th width="80">Challan No</th>
                        <th width="70">Lot No</th>
                        <th width="60">Count</th>
                        <th width="160">Composition</th>
                        <th width="80">Type</th>
                        <th width="80">Color</th>
                        <th width="70">Qty</th>
                        <th width="70">Rate</th>
                        <th width="70">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?
				
				$yarn_count_arr=return_library_array("SELECT id,yarn_count FROM lib_yarn_count","id","yarn_count");
				$color_name_arr=return_library_array("SELECT id,color_name FROM lib_color","id","color_name");
				$prod_dtsl_sql=sql_select("select a.pi_wo_batch_no as pi_id, b.yarn_count_id as count_name, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type,b.lot from  product_details_master b,inv_transaction a where a.prod_id=b.id and a.pi_wo_batch_no in($pi_id) and a.receive_basis=$rcv_basis and a.item_category=1 and a.transaction_type=1");
				$prod_data_all=array();
				foreach($prod_dtsl_sql as $row)
				{
					$prod_data_all[$row[csf("pi_id")]]["count_name"].=$yarn_count_arr[$row[csf("count_name")]].',';
					$prod_data_all[$row[csf("pi_id")]]["comp_name"].=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].',';
					$prod_data_all[$row[csf("pi_id")]]["yarn_type"].=$yarn_type[$row[csf("yarn_type")]].',';
					
				}
				
				/*if($db_type==0)
				{
					$sql="select c.id as pi_id, c.pi_number, c.pi_date, b.id as receive_id, b.receive_date, b.challan_no, sum(a.order_qnty) as qnty, group_concat(a.prod_id) as prod_id
					from inv_transaction a,  inv_receive_master b, com_pi_master_details c 
					where a.mst_id=b.id and b.booking_id=c.id and b.entry_form=1 and a.item_category=1 and b.receive_basis=1 and  b.booking_id in($pi_id) and b.company_id=$company_id
					group by c.id, c.pi_number, c.pi_date, b.id, b.receive_date, b.challan_no";
				}
				else
				{
					$sql="select c.id as pi_id, c.pi_number, c.pi_date, b.id as receive_id, b.receive_date, b.challan_no, sum(a.order_qnty) as qnty, LISTAGG(CAST(a.prod_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.prod_id) as prod_id
					from inv_transaction a,  inv_receive_master b, com_pi_master_details c 
					where a.mst_id=b.id and b.booking_id=c.id and b.entry_form=1 and a.item_category=1 and b.receive_basis=1 and  b.booking_id in($pi_id) and b.company_id=$company_id

					group by c.id, c.pi_number, c.pi_date, b.id, b.receive_date, b.challan_no";
				}*/
				
				// select c.id as pi_id, c.pi_number, c.pi_date, b.id as receive_id,b.recv_number, b.receive_date, b.challan_no, a.order_qnty,a.prod_id,d.product_name_details,d.lot,d.yarn_count_id,d.yarn_comp_type1st,d.yarn_comp_percent1st,d.yarn_type,d.color from inv_transaction a, inv_receive_master b, com_pi_master_details c, product_details_master d where a.mst_id=b.id and b.booking_id=c.id and a.prod_id=d.id and b.entry_form=1 and a.item_category=1 and b.receive_basis=1  and b.company_id=3 
				$sql="select c.id as pi_id, c.pi_number, c.pi_date, b.id as receive_id,b.recv_number, b.receive_date, b.challan_no,b.remarks, a.order_qnty,a.order_rate ,a.order_amount,a.prod_id,d.product_name_details,d.lot,d.yarn_count_id,d.yarn_comp_type1st,d.yarn_comp_percent1st,d.yarn_type,d.color from inv_transaction a, inv_receive_master b, com_pi_master_details c, product_details_master d where a.mst_id=b.id and b.booking_id=c.id and a.prod_id=d.id and b.entry_form=1  and b.receive_basis=$rcv_basis and  b.booking_id in($pi_id) and b.company_id=$company_id";
				$total_pi_qnty=0; $total_pi_amt=0;
				//echo $sql;die;
				$result=sql_select($sql);$i=1;
				foreach($result as $row)  
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$composition_data="";
					//if($pi_data_all[$row[csf("pi_id")]]["yarn_composition_item1"]>0) $composition_data=$composition[$pi_data_all[$row[csf("pi_id")]]["yarn_composition_item1"]]." ".$pi_data_all[$row[csf("pi_id")]]["yarn_composition_percentage1"]."%";
					//if($pi_data_all[$row[csf("pi_id")]]["yarn_composition_item2"]>0) $composition_data.=" ".$composition[$pi_data_all[$row[csf("pi_id")]]["yarn_composition_item2"]]." ".$pi_data_all[$row[csf("pi_id")]]["yarn_composition_percentage2"]."%";
					$yarn_count=chop($prod_data_all[$row[csf("pi_id")]]["count_name"],",");
					$yarn_comp=chop($prod_data_all[$row[csf("pi_id")]]["comp_name"],",");
					$yarn_type=chop($prod_data_all[$row[csf("pi_id")]]["yarn_type"],",");
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td><p><? echo $row[csf('pi_number')]; ?>&nbsp;</p></td>
						<td align="center"><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
						<td><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
						<td><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
						<td><p><? echo $row[csf('lot')]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
						<td><p><? echo $color_name_arr[$row[csf('color')]]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('order_qnty')],2); $total_pi_qnty+=$row[csf('order_qnty')];  ?></td>
                        <td align="right"><? echo number_format($row[csf('order_rate')],2);?></td>
                        <td align="right"><? echo number_format($row[csf('order_amount')],2); $total_pi_amt+=$row[csf('order_amount')];  ?></td>
                        <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
                </tbody> 
                <tfoot>
                	<tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>Total :</th>
                        <th align="right"><? echo number_format($total_pi_qnty,2) ; ?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_pi_amt,2) ; ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>  
            </table>
        </fieldset>
    </div>
	<?
    exit();

}

if($action=="receive_return_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$pi_id=str_replace("'","",$pi_id);
	
	
	/*$pi_dtsl_sql=sql_select("select pi_id, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, yarn_composition_percentage2, yarn_type from  com_pi_item_details where pi_id in($pi_id)");
	$pi_data_all=array();
	foreach($pi_dtsl_sql as $row)
	{
		$pi_data_all[$row[csf("pi_id")]]["count_name"].=$row[csf("count_name")].',';
		$pi_data_all[$row[csf("pi_id")]]["yarn_composition_item1"]=$row[csf("yarn_composition_item1")];
		$pi_data_all[$row[csf("pi_id")]]["yarn_composition_percentage1"]=$row[csf("yarn_composition_percentage1")];
		$pi_data_all[$row[csf("pi_id")]]["yarn_composition_item2"]=$row[csf("yarn_composition_item2")];
		$pi_data_all[$row[csf("pi_id")]]["yarn_composition_percentage2"]=$row[csf("yarn_composition_percentage2")];
		$pi_data_all[$row[csf("pi_id")]]["yarn_type"]=$row[csf("yarn_type")];
	}
	$prod_dtsl_sql=sql_select("select a.pi_wo_batch_no as pi_id,b.yarn_count_id as count_name from  product_details_master b,inv_transaction a where a.prod_id=b.id and a.pi_wo_batch_no in($pi_id) and a.item_category=1 and a.transaction_type=1 group by a.pi_wo_batch_no,b.yarn_count_id ");
	$prod_data_all=array();
	foreach($prod_dtsl_sql as $row)
	{
		$prod_data_all[$row[csf("pi_id")]]["count_name"].=$row[csf("count_name")].',';
		
	}*/
	
	//var_dump($composition[1]);die;
	//echo $pi_dtsl_sql;die;
	?>
	<script>
	
	/*function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}*/	
	
	</script>	
    <div id="report_container" align="center" style="width:800px">
	<fieldset style="width:800px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="800" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                        <th width="100">Return Date</th>
                        <th width="130">Return No</th>
                        <th width="200">Item Description</th>
                        <th width="70">Qty</th>
                        <th width="70">Rate</th>
                        <th width="90">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?
				
				$product_arr=return_library_array("SELECT id,product_name_details FROM product_details_master where item_category_id=1","id","product_name_details");
				/*$color_name_arr=return_library_array("SELECT id,color_name FROM lib_color","id","color_name");
				$prod_dtsl_sql=sql_select("select a.pi_wo_batch_no as pi_id, b.yarn_count_id as count_name, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type,b.lot from  product_details_master b,inv_transaction a where a.prod_id=b.id and a.pi_wo_batch_no in($pi_id) and a.item_category=1 and a.transaction_type=1");
				$prod_data_all=array();
				foreach($prod_dtsl_sql as $row)
				{
					$prod_data_all[$row[csf("pi_id")]]["count_name"].=$yarn_count_arr[$row[csf("count_name")]].',';
					$prod_data_all[$row[csf("pi_id")]]["comp_name"].=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].',';
					$prod_data_all[$row[csf("pi_id")]]["yarn_type"].=$yarn_type[$row[csf("yarn_type")]].',';
					
				}*/
				
				$sql=" select a.pi_id,a.issue_number,a.issue_date,b.prod_id, b.cons_quantity, b.rcv_rate as cons_rate, b.rcv_amount as cons_amount, a.remarks from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=8 and a.received_id in($pi_id) and a.company_id=$company_id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				//echo $sql;
				$total_pi_qnty=0; $total_pi_amt=0;
				//echo $sql;die; Return Date	Return No	Item Description	Qty	Rate	Value	Remarks
				$result=sql_select($sql);$i=1;
				foreach($result as $row)  
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center"><p><? if($row[csf('issue_date')]!="" && $row[csf('issue_date')]!="0000-00-00") echo change_date_format($row[csf('issue_date')]); ?>&nbsp;</p></td>
						<td><p><? echo $row[csf('issue_number')]; ?>&nbsp;</p></td>
						<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row[csf('cons_quantity')],2); $total_pi_qnty+=$row[csf('cons_quantity')];  ?></td>
                        <td align="right"><? echo number_format($row[csf('cons_rate')],2);?></td>
                        <td align="right"><? echo number_format($row[csf('cons_amount')],2); $total_pi_amt+=$row[csf('cons_amount')];  ?></td>
						<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
                </tbody> 
                <tfoot>
                	<tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>Total :</th>
                        <th align="right"><? echo number_format($total_pi_qnty,2) ; ?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_pi_amt,2) ; ?></th>
                        <th>&nbsp;</th>

                    </tr>
                </tfoot>  
            </table>
        </fieldset>
    </div>
	<?
    exit();

}


if($action=="receive_mrr_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$pi_id_ref=explode("__",str_replace("'","",$pi_id));
	$book_pi_id=$pi_id_ref[0];
	$receive_basis=$pi_id_ref[1];
	$count_id=str_replace("'","",$count_id);
	$composition_id=str_replace("'","",$composition_id);
	$type_id=str_replace("'","",$type_id);
	$color_id=str_replace("'","",$color_id);
	$yarn_count_arr=return_library_array("SELECT id,yarn_count FROM lib_yarn_count","id","yarn_count");
	?>
	<script>
	
	/*function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}*/	
	
	</script>	
    <div id="report_container" align="center" style="width:660px">
	<fieldset style="width:660px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="650" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                        <th width="80">Receive Date</th>
                        <th width="120">MRR Number</th>
                        <th width="80">Challan No</th>
                        <th width="80">Receive Qnty</th>
                        <th width="80">Rate</th>
                        <th width="100">Value</th>
                        <th >Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?
				
				$sql="select a.id as mrr_id, a.recv_number, a.receive_date, a.challan_no, a.remarks, sum(b.order_qnty) as qnty, sum(b.order_amount) as amt
					from inv_receive_master a,  inv_transaction b, product_details_master c 
					where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=1 and b.item_category=1 and a.receive_basis=$receive_basis and b.transaction_type=1 and a.company_id=$company_id and a.booking_id in($book_pi_id) and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition_id and c.yarn_type=$type_id and c.color=$color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					group by a.id, a.recv_number, a.receive_date, a.challan_no, a.remarks";
					
				$result=sql_select($sql);$i=1;
				foreach($result as $row)  
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center"><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
						<td align="center"><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2);  ?></td>
                        <td align="right"><? $rate=$row[csf('amt')]/$row[csf('qnty')]; echo number_format($rate,2);?></td>
                        <td align="right"><? echo number_format($row[csf('amt')],2);?></td>
                        <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
					$total_qnty+=$row[csf('qnty')];
					$total_amt+=$row[csf('amt')];
				}
				?>
                </tbody> 
                <tfoot>
                	<tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>Total :</th>
                        <th align="right"><? echo number_format($total_qnty,2) ; ?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_amt,2) ; ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>  
            </table>
        </fieldset>
    </div>
	<?
    exit();

}


if($action=="return_mrr_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$receive_ids=str_replace("'","",$pi_id);
	$count_id=str_replace("'","",$count_id);
	$composition_id=str_replace("'","",$composition_id);
	$type_id=str_replace("'","",$type_id);
	$color_id=str_replace("'","",$color_id);
	$yarn_count_arr=return_library_array("SELECT id,yarn_count FROM lib_yarn_count","id","yarn_count");
	?>
	<script>
	
	/*function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}*/	
	
	</script>	
    <div id="report_container" align="center" style="width:650px">
	<fieldset style="width:650px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="630" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                        <th width="80">Return Date</th>
                        <th width="120">Return Number</th>
                        <th width="100">Return Qnty</th>
                        <th width="100">Rate</th>
                        <th width="100">Value</th>
                        <th >Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and id=(select max(id) as id from currency_conversion_rate where currency=2)" , "conversion_rate" );
				$sql="select a.id as mrr_id, a.issue_number as recv_number, a.issue_date as receive_date, a.remarks, sum(b.cons_quantity) as qnty, sum(b.rcv_amount) as amt
					from inv_issue_master a,  inv_transaction b, product_details_master c 
					where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=8 and b.item_category=1 and b.transaction_type=3 and a.company_id=$company_id and a.received_id in($receive_ids) and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition_id and c.yarn_type=$type_id and c.color=$color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					group by a.id, a.issue_number, a.issue_date, a.remarks";
				//echo $sql;//die;	
				$result=sql_select($sql);$i=1;
				foreach($result as $row)  
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$amount=$row[csf('amt')]/$currency_rate;
					$rate=$amount/$row[csf('qnty')];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center"><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
						<td align="center"><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2);  ?></td>
                        <td align="right" title="<? echo $currency_rate; ?>"><? echo number_format($rate,2);?></td>
                        <td align="right"><? echo number_format($amount,2);?></td>
                        <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
					$total_qnty+=$row[csf('qnty')];
					$total_amt+=$amount;
				}
				?>
                </tbody> 
                <tfoot>
                	<tr>
                        <th>&nbsp;</th>
                        <th>Total :</th>
                        <th align="right"><? echo number_format($total_qnty,2) ; ?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_amt,2) ; ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>  
            </table>
        </fieldset>
    </div>
	<?
    exit();

}

if($action=="pi_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$pi_id=str_replace("'","",$pi_id);
	$yarn_count_arr=return_library_array("SELECT id,yarn_count FROM lib_yarn_count","id","yarn_count");
	$pi_dtsl_sql=("select a.id as pi_id , a.pi_number, a.pi_date, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, sum(b.quantity) as pi_quantity, sum(b.amount) as pi_amount
	from com_pi_master_details a, com_pi_item_details b 
	where a.id=b.pi_id and a.id in($pi_id)
	group by  a.id, a.pi_number, a.pi_date, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type order by a.id");
	
	//echo $pi_dtsl_sql;die;
	?>
	<script>
	
	/*function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}*/	
	
	</script>	
    <div id="report_container" align="center" style="width:700px">
	<fieldset style="width:700px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="700" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                    	<th width="50">SL</th>
                        <th width="100">PI No.</th>
                        <th width="70">PI Date</th>
                        <th width="60">Count</th>
                        <th width="160">Composition</th>
                        <th width="80">Type</th>
                        <th width="70">PI Qnty</th>
                        <th>PI Value</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$total_pi_qnty=0;
				$result=sql_select($pi_dtsl_sql);$i=1;$pi_test=array();
				foreach($result as $row)  
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$composition_data="";
					if($row[csf("yarn_composition_item1")]>0) $composition_data=$composition[$row[csf("yarn_composition_item1")]]." ".$row[csf("yarn_composition_percentage1")]."%";
					if($row[csf("yarn_composition_item2")]>0) $composition_data.=" ".$composition[$row[csf("yarn_composition_item2")]]." ".$row[csf("yarn_composition_percentage2")]."%";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    	<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
						<td><p><? if($pi_test[$row[csf('pi_number')]]=="") echo $row[csf('pi_number')]; ?>&nbsp;</p></td>
						<td><p>
						<?
						if($pi_test[$row[csf('pi_number')]]=="")
						{
							$pi_test[$row[csf('pi_number')]]=$row[csf('pi_number')];
							if($row[csf('pi_date')]!="" && $row[csf('pi_date')]!="0000-00-00") echo change_date_format($row[csf('pi_date')]);
						}
						?>&nbsp;</p></td>
						<td><p><? echo $yarn_count_arr[$row[csf("count_name")]]; ?>&nbsp;</p></td>
						<td><p><? echo $composition_data; ?>&nbsp;</p></td>
						<td><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('pi_quantity')],2); $total_pi_qnty+=$row[csf('pi_quantity')];  ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('pi_amount')],2); $total_pi_value+=$row[csf('pi_amount')];  ?></td>
					</tr>
					<?
					$i++;
				}
				?>
                </tbody> 
                <tfoot>
                	<tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>Total :</th>
                        <th align="right"><? echo number_format($total_pi_qnty,2) ; ?></th>
                        <th align="right"><? echo number_format($total_pi_value,2) ; ?></th>
                    </tr>
                </tfoot>  
            </table>
        </fieldset>
    </div>
	<?
    exit();

}

?>
