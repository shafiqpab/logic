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
							echo create_drop_down( "cbo_supplier_id", 160,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=3",'id,supplier_name', 1, '-- All Supplier --',$supplierID,'',0);
                        ?>
                    </td>
                    <td align="center">				
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+'<? echo $btbLc_id; ?>', 'create_pi_search_list_view', 'search_div_pi', 'btb_lc_balance_statement_general_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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
		where a.id=b.pi_id and b.com_btb_lc_master_details_id=$btbLc_id and a.importer_id=$company and a.entry_form=172 and a.ITEM_CATEGORY_ID in(".implode(",",array_flip($general_item_category)).") and a.supplier_id like '$cbo_supplier' and a.pi_number like '%".$txt_search_common."%' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
		group by a.id, a.pi_number, a.supplier_id, a.importer_id, a.pi_date, a.last_shipment_date, a.net_total_amount";
	}
	else
	{
		$sql= "select id, pi_number, supplier_id, importer_id, pi_date, last_shipment_date, net_total_amount from com_pi_master_details where importer_id=$company and entry_form=172 and ITEM_CATEGORY_ID in(".implode(",",array_flip($general_item_category)).") and supplier_id like '$cbo_supplier' and pi_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1";
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
		<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th>Supplier</th>
						<th>Enter BTB LC Number</th>
						<th>PI Number</th>
						<th>PI Date</th>
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
								echo create_drop_down( "cbo_supplier_id", 160,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID'",'id,supplier_name', 1, '-- All Supplier --',$supplierID,'',0);
							?>
						</td>
						<td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td>
						<td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_pi_no" id="txt_pi_no" />	
						</td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:55px" placeholder="From Date" readonly>
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"placeholder="To Date" readonly>
						</td> 
						 <td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+'<? echo $pi_id; ?>'+'_'+document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_lc_search_list_view', 'search_div', 'btb_lc_balance_statement_general_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" valign="bottom"><? echo load_month_buttons(1);  ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
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
	$txt_pi_no = trim($ex_data[4]);
	$txt_date_from = change_date_format(trim($ex_data[5]),"","",1);
	$txt_date_to = change_date_format(trim($ex_data[6]),"","",1);
	//echo $txt_date_from."=".$txt_date_to;die;
	$sql_conds="";
	if($txt_pi_no!="") $sql_conds=" and c.PI_NUMBER='$txt_pi_no'";
	if($txt_date_from!="" && $txt_date_to!="") $sql_conds=" and c.PI_DATE between '$txt_date_from' and '$txt_date_to'";
	
	$sql= "select a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value 
	from com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c 
	where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and a.importer_id=$company and a.pi_entry_form=172 and a.supplier_id like '$cbo_supplier' and a.lc_number like '%".$txt_search_common."%' $sql_conds and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0 
	group by a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value";
	// echo $sql;
	
	//if($pi_id!="") 
	//	{
	//		$sql= "select a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value 
	//		from com_btb_lc_master_details a, com_btb_lc_pi b 
	//		where a.id=b.com_btb_lc_master_details_id and b.pi_id=$pi_id and a.importer_id=$company and pi_entry_form=227 and a.supplier_id like '$cbo_supplier' and a.lc_number like '%".$txt_search_common."%' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.ref_closing_status=0
	//		group by a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value";
	//	}
	//	else
	//	{
	//		$sql= "select id, lc_number, supplier_id, importer_id, lc_date, last_shipment_date, lc_value from com_btb_lc_master_details where importer_id=$company and pi_entry_form=227 and supplier_id like '$cbo_supplier' and lc_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1 and ref_closing_status=0";
	//	}
	
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
	$cbo_item_category=str_replace("'","",$cbo_item_category);
	
	//echo $rpt_type;die;
	
	//if(str_replace("'","",$cbo_suppler_name)!="") $store_cond=" and b.store_id in(".str_replace("'","",$cbo_suppler_name).")";
	//select conversion_rate from currency_conversion_rate where con_date=(select max(con_date) as con_date   from currency_conversion_rate)
	
	
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "con_date=(select max(con_date) as con_date   from currency_conversion_rate)" , "conversion_rate" );
	$sql_cond="";
	$btbLc_id_str=str_replace("'","",$btbLc_id);
	if($cbo_suppler_name>0) $sql_cond=" and c.supplier_id=$cbo_suppler_name";
	if($cbo_item_category>0) $sql_cond.=" and a.item_category_id=$cbo_item_category";
	if($btbLc_id!="") $sql_cond.=" and c.id=$btbLc_id";
	if($pi_id!="") $sql_cond.=" and a.id=$pi_id";
	if($txt_date_from_pi!="" && $txt_date_to_pi!="")
	{
		$sql_cond.="  and a.pi_date between '".$txt_date_from_pi."' and '".$txt_date_to_pi."'";
	}
	
	if($txt_date_from!="" && $txt_date_to!="")
	{
		$sql_cond.="  and c.lc_date between '".$txt_date_from."' and '".$txt_date_to."'";
	}
	
	
	//LISTAGG(CAST(doc_submission_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY doc_submission_mst_id) as id
	if($db_type==0)
	{
		$btb_sql="select  group_concat(a.id) as pi_id, a.item_category_id, c.id as btb_id, c.importer_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.supplier_id, c.lc_value as btb_value, c.lc_category, c.last_shipment_date, d.is_lc_sc, group_concat(d.lc_sc_id) as lc_sc_id
		from com_pi_master_details a, com_btb_lc_pi b, com_btb_lc_master_details c left join com_btb_export_lc_attachment d on c.id=d.import_mst_id and d.is_deleted=0 and d.status_active=1 
		where a.id=b.pi_id and b.com_btb_lc_master_details_id=c.id and c.importer_id=$cbo_company_name and c.is_deleted=0 and c.status_active=1 and c.ref_closing_status=0 and c.PI_ENTRY_FORM=172 and a.ITEM_CATEGORY_ID in(".implode(",",array_flip($general_item_category)).") $sql_cond
		group by a.item_category_id, c.id, c.importer_id, c.lc_number, c.lc_date, c.item_category_id, c.supplier_id, c.lc_value, c.lc_category, c.last_shipment_date, d.is_lc_sc";
	}
	else
	{
		/* $btb_sql="select LISTAGG(CAST(a.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.id) as pi_id, a.item_category_id, c.id as btb_id, c.importer_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.supplier_id, c.lc_value as btb_value, c.lc_category, c.last_shipment_date, d.is_lc_sc, LISTAGG(CAST(d.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.lc_sc_id) as lc_sc_id
		from com_pi_master_details a, com_btb_lc_pi b, com_btb_lc_master_details c left join com_btb_export_lc_attachment d on c.id=d.import_mst_id and d.is_deleted=0 and d.status_active=1 
		where a.id=b.pi_id and b.com_btb_lc_master_details_id=c.id and c.importer_id=$cbo_company_name and c.is_deleted=0 and c.status_active=1 and c.ref_closing_status=0 and c.PI_ENTRY_FORM=172 and a.ITEM_CATEGORY_ID in(".implode(",",array_flip($general_item_category)).") $sql_cond
		group by a.item_category_id, c.id, c.importer_id, c.lc_number, c.lc_date, c.item_category_id, c.supplier_id, c.lc_value, c.lc_category, c.last_shipment_date, d.is_lc_sc"; */

		$btb_sql="SELECT a.id as pi_id, a.item_category_id, c.id as btb_id, c.importer_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.supplier_id, c.lc_value as btb_value, c.lc_category, c.last_shipment_date, d.is_lc_sc, d.lc_sc_id as lc_sc_id
		from com_pi_master_details a, com_btb_lc_pi b, com_btb_lc_master_details c 
		left join com_btb_export_lc_attachment d on c.id=d.import_mst_id and d.is_deleted=0 and d.status_active=1 
		where a.id=b.pi_id and b.com_btb_lc_master_details_id=c.id and c.importer_id=$cbo_company_name and c.is_deleted=0 and c.status_active=1 and c.ref_closing_status=0 and c.PI_ENTRY_FORM=172 and a.ITEM_CATEGORY_ID in(".implode(",",array_flip($general_item_category)).") $sql_cond ";
	}
	//echo $btb_sql;//die;
	$btb_sql_result=sql_select($btb_sql);
	$all_pi_id=$all_btb_id=$all_lc_id=$all_sc_id="";
	foreach($btb_sql_result as $row)
	{
		$all_pi_id.=implode(",",array_unique(explode(",",$row[csf("pi_id")]))).",";
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
		
		$sql_lc=sql_select("select id, export_lc_no, internal_file_no from com_export_lc where beneficiary_name=$cbo_company_name $lc_id_cond");
		$lc_data_arr=array();
		foreach($sql_lc as $row)
		{
			$lc_data_arr[$row[csf("id")]]['lc_sc_id']=$row[csf("id")];
			$lc_data_arr[$row[csf("id")]]['lc_sc_no']=$row[csf("export_lc_no")];
			$lc_data_arr[$row[csf("id")]]['internal_file_no']=$row[csf("internal_file_no")];
		}
		
	}
	
	$all_sc_id=implode(",",array_unique(explode(",",chop($all_sc_id,","))));
	//echo $all_sc_id.test;die;
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
	$pi_sql=sql_select("select a.pi_id as pi_id, a.quantity as pi_qnty, a.net_pi_amount, b.com_btb_lc_master_details_id as btb_lc_id, c.current_acceptance_value as accep_value, a.id as pi_dtls_id 
	from com_pi_item_details a, com_btb_lc_pi b left join com_import_invoice_dtls c on b.pi_id=c.pi_id and b.com_btb_lc_master_details_id=c.btb_lc_id
	where a.pi_id=b.pi_id and a.status_active=1 and a.is_deleted=0 and a.ITEM_CATEGORY_ID in(".implode(",",array_flip($general_item_category)).") $btb_ids_cond");
	$pi_data_arr=array();
	$pi_id_check=array();
	foreach($pi_sql as $row)
	{
		if($pi_btb_id_check[$row[csf("btb_lc_id")]][$row[csf("pi_id")]]=="")
		{
			$pi_btb_id_check[$row[csf("btb_lc_id")]][$row[csf("pi_id")]]=$row[csf("pi_id")];
			$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
			$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("pi_id")]]["accep_value"]+=$row[csf("accep_value")];
		}
		
		if($pi_id_check[$row[csf("pi_dtls_id")]]=="")
		{
			$pi_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
			$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("pi_id")]]["pi_qnty"]+=$row[csf("pi_qnty")];
			$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("pi_id")]]["pi_amount"]+=$row[csf("net_pi_amount")];
		}
		
	}
	
	
	
	$all_pi_id=chop($all_pi_id,",");
	$all_pi_id_arr=explode(",",$all_pi_id);
	$pi_ids=$pi_ids_cond=$pi_return_ids=$pi_ids_return_cond="";
	if($db_type==2 && count($all_pi_id_arr)>999)
	{
		$all_pi_id_chunk=array_chunk($all_pi_id_arr,999) ;
		foreach($all_pi_id_chunk as $chunk_arr)
		{
			$pi_ids.=" a.booking_id in(".implode(",",$chunk_arr).") or ";
			$pi_return_ids.=" a.pi_id in(".implode(",",$chunk_arr).") or ";	
		}
				
		$pi_ids_cond=" and (".chop($pi_ids,'or ').")";	
		$pi_ids_return_cond=" and (".chop($pi_return_ids,'or ').")";			
	}
	else
	{ 	
		$pi_ids_cond=" and a.booking_id in($all_pi_id)"; 
		$pi_ids_return_cond=" and a.pi_id in($all_pi_id)"; 
	}
	
	
	$receive_sql=sql_select("select a.booking_id, sum(b.order_qnty) as order_qnty, sum(b.order_amount) as order_amount 
	from inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and a.receive_basis=1 and a.entry_form=20 and b.transaction_type=1 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $pi_ids_cond group by a.booking_id");
	
	$recv_data_arr=array();
	foreach($receive_sql as $row)
	{
		$recv_data_arr[$row[csf("booking_id")]]["rcv_qnty"]=$row[csf("order_qnty")];
		$recv_data_arr[$row[csf("booking_id")]]["rcv_amt"]=$row[csf("order_amount")];
	}
	
	$receive_return_sql=sql_select("select a.pi_id, b.transaction_date, b.cons_quantity as cons_quantity, b.cons_amount as cons_amount, b.rcv_amount as rcv_amount 
	from inv_issue_master a, inv_transaction b 
	where a.id=b.mst_id and a.pi_id>0 and a.entry_form=28 and b.transaction_type=3 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $pi_ids_return_cond");		
	
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
		$recv_rtn_data_arr[$row[csf("pi_id")]]["rcv_qnty"]+=$row[csf("cons_quantity")];
		if($row[csf("cons_amount")]>0)
		{
			$recv_rtn_data_arr[$row[csf("pi_id")]]["rcv_amt"]+=$row[csf("cons_amount")]/$exchange_rate;
		}
		else
		{
			$recv_rtn_data_arr[$row[csf("pi_id")]]["rcv_amt"]+=$row[csf("rcv_amount")]/$exchange_rate;
		}
		
	}
	
	$btb_data_arr=array();
	if($cbo_receiving_status==1)
	{
		foreach($btb_sql_result as $row)
		{
			if(chop($pi_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["pi_id"],",")!="")
			{
				$rcv_btb_value=0;
				$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["pi_id"],",")));
				
				
				foreach($pi_id_arr as $pi_id)
				{
					$rcv_btb_value+=$recv_data_arr[$pi_id]["rcv_amt"]-$recv_rtn_data_arr[$pi_id]["rcv_amt"];
				}
				//echo "<pre>"; print_r($pi_id_arr);echo "======".$row[csf("btb_id")]."======".$rcv_btb_value;die;
				if($rcv_btb_value==0 || $rcv_btb_value=="")
				{
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["item_category_id"]=$row[csf("item_category_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_id"]=$row[csf("btb_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["importer_id"]=$row[csf("importer_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_lc_number"]=$row[csf("btb_lc_number")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_lc_date"]=$row[csf("btb_lc_date")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["supplier_id"]=$row[csf("supplier_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_value"]=$row[csf("btb_value")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["lc_category"]=$row[csf("lc_category")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["rcv_btb_value"]=$rcv_btb_value;
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
				}
			}
			
		}
	}		
	else if($cbo_receiving_status==2)
	{
		foreach($btb_sql_result as $row)
		{
			if(chop($pi_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["pi_id"],",")!="")
			{
				//echo "jahid";die;
				$rcv_btb_value=0;
				$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["pi_id"],",")));
				foreach($pi_id_arr as $pi_id)
				{
					
					$rcv_btb_value+=$recv_data_arr[$pi_id]["rcv_amt"]-$recv_rtn_data_arr[$pi_id]["rcv_amt"];
					
				}
				//echo "<pre>"; echo $row[csf("btb_value")]."==".$rcv_btb_value;die;
				//echo "<pre>"; print_r($pi_id_arr);echo "======".$row[csf("btb_id")]."======".$rcv_btb_value;die;
				if($row[csf("btb_value")]>$rcv_btb_value && $rcv_btb_value>0 )
				{
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["item_category_id"]=$row[csf("item_category_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_id"]=$row[csf("btb_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["importer_id"]=$row[csf("importer_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_lc_number"]=$row[csf("btb_lc_number")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_lc_date"]=$row[csf("btb_lc_date")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["supplier_id"]=$row[csf("supplier_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_value"]=$row[csf("btb_value")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["lc_category"]=$row[csf("lc_category")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["rcv_btb_value"]=$rcv_btb_value;
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
				}
			}
			
		}
	}
	else if($cbo_receiving_status==3)
	{
		foreach($btb_sql_result as $row)
		{
			if(chop($pi_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["pi_id"],",")!="")
			{
				$rcv_btb_value=0;
				$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["pi_id"],",")));
				foreach($pi_id_arr as $pi_id)
				{
					
					$rcv_btb_value+=$recv_data_arr[$pi_id]["rcv_amt"]-$recv_rtn_data_arr[$pi_id]["rcv_amt"];
					
				}
				
				if($row[csf("btb_value")]<=$rcv_btb_value)
				{
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["item_category_id"]=$row[csf("item_category_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_id"]=$row[csf("btb_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["importer_id"]=$row[csf("importer_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_lc_number"]=$row[csf("btb_lc_number")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_lc_date"]=$row[csf("btb_lc_date")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["supplier_id"]=$row[csf("supplier_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_value"]=$row[csf("btb_value")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["lc_category"]=$row[csf("lc_category")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["rcv_btb_value"]=$rcv_btb_value;
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
				}
			}
		}
	}
	else if($cbo_receiving_status==4)
	{
		foreach($btb_sql_result as $row)
		{
			if(chop($pi_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["pi_id"],",")!="")
			{
				//echo "jahid";die;
				$rcv_btb_value=0;
				$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["pi_id"],",")));
				foreach($pi_id_arr as $pi_id)
				{
					
					$rcv_btb_value+=$recv_data_arr[$pi_id]["rcv_amt"]-$recv_rtn_data_arr[$pi_id]["rcv_amt"];
					
				}
				if(($row[csf("btb_value")]>$rcv_btb_value && $rcv_btb_value>0) || $rcv_btb_value==0 || $rcv_btb_value=="")
				{
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["item_category_id"]=$row[csf("item_category_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_id"]=$row[csf("btb_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["importer_id"]=$row[csf("importer_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_lc_number"]=$row[csf("btb_lc_number")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_lc_date"]=$row[csf("btb_lc_date")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["supplier_id"]=$row[csf("supplier_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_value"]=$row[csf("btb_value")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["lc_category"]=$row[csf("lc_category")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["rcv_btb_value"]=$rcv_btb_value;
					$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
				}
			}
			
		}
	}
	else
	{
		foreach($btb_sql_result as $row)
		{
			$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["item_category_id"]=$row[csf("item_category_id")];
			$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_id"]=$row[csf("btb_id")];
			$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["importer_id"]=$row[csf("importer_id")];
			$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_lc_number"]=$row[csf("btb_lc_number")];
			$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_lc_date"]=$row[csf("btb_lc_date")];
			$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["supplier_id"]=$row[csf("supplier_id")];
			$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["btb_value"]=$row[csf("btb_value")];
			$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["lc_category"]=$row[csf("lc_category")];
			$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
			$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
			$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["lc_sc_id"]=$row[csf("lc_sc_id")];
			$btb_data_arr[$row[csf("btb_id")]][$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
		}
	}
	//echo "<pre>";print_r($btb_data_arr);die;
	// echo "<pre>";print_r($pi_data_arr);die;
	ob_start();
	?>
	<div style="width:1870px; margin-left:10px;" align="left">
		<table width="1850" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
			<tr>
			   <td align="center" width="100%" colspan="19" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
			</tr>
		</table>
		<table width="1850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="40">SL</th>
					<th width="120">Item Category</th>
					<th width="160">Supplier Name</th>
					<th width="70">LC Date</th>
					<th width="100">BTB LC No.</th>
					<th width="100">LC Value</th>
					<th width="70">Shipment Date</th>
					<th width="100">Import Source</th>
					<th width="80">File No.</th> 
					<th width="120">Export LC/SC No.</th> 
					<th width="100">PI Value</th>
					<th width="100">Received Value</th>
					<th width="100">Return Value</th>
					<th width="100">Payable Value</th>
					<th width="100">Balance Value</th>
					<th width="100">Accept. Value</th> 
					<th width="100">Accept.Bl. Value</th>
					<th>Receiving Company</th>
				</tr>
			</thead>
		</table>
		<div style="width:1870px; overflow-y:scroll; max-height:350px; overflow-x:hidden;" id="scroll_body" align="left">
		<table width="1850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_bodyy">   
			<tbody>
			<?
			$i=1; 	
			foreach($btb_data_arr as $btb_id=>$pi_data)
			{
				foreach($pi_data as $val){
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				
				$all_pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$val["btb_id"]][$val["pi_id"]]["pi_id"],",")));
				$all_pi_id=implode(',',$all_pi_id_arr);
				?>
				<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
					<td width="40" align="center" title="<? echo $val["rcv_btb_value"]; ?>"><? echo $i; ?></td>
                    <td width="120" title="<? echo $val["item_category_id"]; ?>"><p><? echo $item_category[$val["item_category_id"]]; ?>&nbsp;</p></td>
                    <td width="160"><p><? echo $supplier_arr[$val["supplier_id"]]; ?>&nbsp;</p></td>
					<td width="70" align="center"><p><? if($val["btb_lc_date"]!="" && $val["btb_lc_date"]!="0000-00-00") echo change_date_format($val["btb_lc_date"]); ?>&nbsp;</p></td>
					<td width="100"><p><? echo $val["btb_lc_number"]; ?>&nbsp;</p></td>
					<td width="100" align="right"><p><? echo number_format($val["btb_value"],2); ?>&nbsp;</p></td>
					<td width="70" align="center"><p><? if($val["last_shipment_date"]!="" && $val["last_shipment_date"]!="0000-00-00") echo change_date_format($val["last_shipment_date"]); ?>&nbsp;</p></td>
					<td width="100"><p><? echo $seource_des_array[$val["lc_category"]*1]; ?>&nbsp;</p></td>
					<td width="80"><p>
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
					if($all_file!="") echo "File No: ".$all_file;
					?>&nbsp;</p></td>
                    <td width="120"><p><? if($all_lc!="") echo "Lc No: ".$all_lc." ";if($all_sc!="") echo "Sc No: ".$all_sc." ";?>&nbsp;</p></td>
					<td width="100" align="right"><a href="##" onClick="openmypage_popup('<? echo $val["importer_id"]; ?>','<? echo $all_pi_id; ?>','<? echo $val["item_category_id"]; ?>','PI Info','pi_popup');" ><? echo number_format($pi_data_arr[$val["btb_id"]][$val["pi_id"]]["pi_amount"],2); $total_pi_amount+=$pi_data_arr[$val["btb_id"]][$val["pi_id"]]["pi_amount"]; ?></a></td>
					<td width="100" align="right"><a href="##" onClick="openmypage_popup('<? echo $val["importer_id"]; ?>','<? echo $all_pi_id; ?>','<? echo $val["item_category_id"]; ?>','Receive Info','receive_popup');" >
					<? 
					$rcv_qnty=$rcv_return_qnty=$rcv_value=$rcv_return_value=0;
					foreach($all_pi_id_arr as $pi_id)
					{
						$rcv_qnty+=$recv_data_arr[$pi_id]["rcv_qnty"];
						$rcv_value+=$recv_data_arr[$pi_id]["rcv_amt"];
						$rcv_return_qnty+=$recv_rtn_data_arr[$pi_id]["rcv_qnty"];
						$rcv_return_value+=$recv_rtn_data_arr[$pi_id]["rcv_amt"];
					}
					echo number_format($rcv_value,2);
					$balance_qnty=($pi_data_arr[$val["btb_id"]][$val["pi_id"]]["pi_qnty"]-($rcv_qnty-$rcv_return_qnty));//$currency_rate
					$balance_value=($val["btb_value"]-($rcv_value-$rcv_return_value));
					$total_rcv_value+=$rcv_value;
					?>
					</a>
					</td>
					<td width="100" align="right"><a href="##" onClick="openmypage_popup('<? echo $val["importer_id"]; ?>','<? echo $all_pi_id; ?>','<? echo $val["item_category_id"]; ?>','Receive Return Info','receive_return_popup');"><? echo number_format($rcv_return_value,2); $total_rcv_return_value+=$rcv_return_value; ?></a></td>
                    <td width="100" align="right"><a href="##" onClick="openmypage_popup('<? echo $val["importer_id"]; ?>','<? echo $all_pi_id; ?>','<? echo $val["item_category_id"]; ?>','Payable Info','payable_popup');"><? $payable_value=$rcv_value-$rcv_return_value; echo number_format($payable_value,2);  $total_payable_value+=$payable_value; ?></a></td>
					<td width="100" align="right" title="<? echo $val["btb_value"]."=".$rcv_value."=".$rcv_return_value; ?>"><? echo number_format($balance_value,2);  $total_balance_value+=$balance_value; ?></td>
					<td width="100" align="right"><a href="##" onClick="openmypage_popup('<? echo $val["importer_id"]; ?>','<? echo $all_pi_id; ?>','<? echo $val["item_category_id"]; ?>','Acceptance Info','accep_popup');"><? $accep_value=$pi_data_arr[$val["btb_id"]][$val["pi_id"]]["accep_value"]; echo number_format($accep_value,2); $total_accep_value+=$accep_value; ?></a></td> 
					<td width="100" align="right" title="<? echo "(payable value- accep value)".$payable_value."=".$accep_value; ?>"><? $accep_balance_value=$payable_value-$accep_value; echo number_format($accep_balance_value,2); $total_accep_balance_value+=$accep_balance_value; ?></td>
                    <td align="center"><p><? echo $company_arr[$val["importer_id"]];?>&nbsp;</p></td> 
				</tr>
				<?
				$i++;
				}
			}
			?>
			</tbody>         	
		</table>
		</div>
		<table width="1850" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<tfoot>
				<tr>
					<th width="40">&nbsp;</th>
					<th width="120">&nbsp;</th>
					<th width="160">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="80">&nbsp;</th> 
					<th width="120">Total:</th> 
					<th width="100" align="right" id="value_total_pi_amount"><? echo number_format($total_pi_amount,2); ?></th>
                    <th width="100" align="right" id="value_total_rcv_value"><? echo number_format($total_rcv_value,2); ?></th>
					<th width="100" align="right" id="value_total_rcv_return_value"><? echo number_format($total_rcv_return_value,2); ?></th>
					<th width="100" align="right" id="value_total_payable_value"><? echo number_format($total_payable_value,2); ?></th>
					<th width="100" align="right" id="value_total_balance_value"><? echo number_format($total_balance_value,2); ?></th>
					<th width="100" align="right" id="value_total_accep_value"><? echo number_format($total_accep_value,2); ?></th>
					<th width="100" align="right" id="value_total_accep_balance_value"><? echo number_format($total_accep_balance_value,2); ?></th>
					<th>&nbsp;</th>
				</tr>
			</tfoot>
		</table>
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
	echo "$total_data####$filename####$rpt_type";
	exit();
}

if($action=="pi_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$pi_id=str_replace("'","",$pi_id);
	$yarn_count_arr=return_library_array("SELECT id,yarn_count FROM lib_yarn_count","id","yarn_count");
	$pi_dtsl_sql=("select a.id as pi_id , a.pi_number, a.pi_date, b.item_description, b.uom, sum(b.quantity) as pi_quantity, sum(b.net_pi_amount) as pi_amount
	from com_pi_master_details a, com_pi_item_details b 
	where a.id=b.pi_id and a.id in($pi_id) and b.status_active=1 and b.is_deleted=0
	group by  a.id, a.pi_number, a.pi_date, b.item_description, b.uom order by a.id");
	
	//echo $pi_dtsl_sql;//die;
	?>
    <div id="report_container" align="center" style="width:700px">
	<fieldset style="width:700px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="700" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                    	<th width="50">SL</th>
                        <th width="100">PI No.</th>
                        <th width="70">PI Date</th>
                        <th width="160">Item Description</th>
                        <th width="60">UOM</th>
                        <th width="70">PI Qnty</th>
                        <th width="70">Rate</th>
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
						<td><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
						<td><p><? echo $unit_of_measurement[$row[csf("uom")]]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('pi_quantity')],2); $total_pi_qnty+=$row[csf('pi_quantity')];  ?>&nbsp;</p></td>
                        <td><p>
						<?
						$pi_rate=0; 
						if($row[csf('pi_amount')]>0 && $row[csf('pi_quantity')]>0) $pi_rate=$row[csf('pi_amount')]/$row[csf('pi_quantity')];  
						echo number_format($pi_rate,2); ?>&nbsp;</p></td>
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
                        <th>Total :</th>
                        <th align="right"><? echo number_format($total_pi_qnty,2) ; ?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_pi_value,2) ; ?></th>
                    </tr>
                </tfoot>  
            </table>
        </fieldset>
    </div>
	<?
    exit();

}

if($action=="receive_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$pi_id=str_replace("'","",$pi_id);
	$item_category_id=str_replace("'","",$item_category_id);
	
	?>
    <div id="report_container" align="center" style="width:1030px">
	<fieldset style="width:1030px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="1020" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                        <th width="100">PI No.</th>
                        <th width="70">PI Date</th>
                        <th width="70">Recv. Date</th>
                        <th width="130">MRR No</th>
                        <th width="200">Item Description</th>
                        <th width="80">UOM</th>
                        <th width="80">Qty</th>
                        <th width="70">Rate</th>
                        <th width="100">Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$sql="select c.id as pi_id, c.pi_number, c.pi_date, b.id as receive_id, b.recv_number, b.receive_date, b.challan_no, b.remarks, a.order_uom, a.order_qnty, a.order_rate, a.order_amount, a.prod_id, d.product_name_details
				from inv_transaction a, inv_receive_master b, com_pi_master_details c, product_details_master d 
				where a.mst_id=b.id and b.booking_id=c.id and a.prod_id=d.id and a.item_category in($item_category_id) and a.transaction_type=1 and b.receive_basis=1 and b.booking_id in($pi_id) and b.company_id=$company_id and a.status_active=1 and a.is_deleted=0";
				$total_pi_qnty=0; $total_pi_amt=0;
				//echo $sql;//die;
				$result=sql_select($sql);$i=1;
				foreach($result as $row)  
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td><p><? echo $row[csf('pi_number')]; ?>&nbsp;</p></td>
						<td align="center"><p><? if($row[csf('pi_date')]!="" && $row[csf('pi_date')]!="0000-00-00") echo change_date_format($row[csf('pi_date')]);?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
						<td><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
						<td><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>&nbsp;</p></td>
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
	$item_category_id=str_replace("'","",$item_category_id);
	?>
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
				
				$product_arr=return_library_array("SELECT id,product_name_details FROM product_details_master","id","product_name_details");
				/*$color_name_arr=return_library_array("SELECT id,color_name FROM lib_color","id","color_name");
				$prod_dtsl_sql=sql_select("select a.pi_wo_batch_no as pi_id, b.yarn_count_id as count_name, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type,b.lot from  product_details_master b,inv_transaction a where a.prod_id=b.id and a.pi_wo_batch_no in($pi_id) and a.item_category=1 and a.transaction_type=1");
				$prod_data_all=array();
				foreach($prod_dtsl_sql as $row)
				{
					$prod_data_all[$row[csf("pi_id")]]["count_name"].=$yarn_count_arr[$row[csf("count_name")]].',';
					$prod_data_all[$row[csf("pi_id")]]["comp_name"].=$composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")].',';
					$prod_data_all[$row[csf("pi_id")]]["yarn_type"].=$yarn_type[$row[csf("yarn_type")]].',';
					
				}*/
				
				$sql=" select a.pi_id,a.issue_number,a.issue_date,b.prod_id, b.cons_quantity, b.rcv_rate as cons_rate, b.rcv_amount as cons_amount, a.remarks 
				from inv_issue_master a, inv_transaction b 
				where a.id=b.mst_id and a.pi_id>0 and b.item_category in($item_category_id) and a.pi_id in($pi_id) and a.company_id=$company_id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				//$receive_return_sql=sql_select("select a.pi_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount from inv_issue_master a,  inv_transaction b where a.id=b.mst_id and a.pi_id>0 and a.entry_form=8 and a.company_id=$cbo_company_name and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pi_id");
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

if($action=="payable_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$pi_id=str_replace("'","",$pi_id);
	$item_category_id=str_replace("'","",$item_category_id);
	
	?>
    <div id="report_container" align="center" style="width:1030px">
	<fieldset style="width:1030px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="1020" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                        <th width="150">PI No.</th>
                        <th width="100">PI Date</th>
                        <th width="200">Item Description</th>
                        <th width="120">PI Value</th>
                        <th width="120">Receive Value</th>
                        <th width="120">Return Value</th>
                        <th width="120">Payable Value</th>
                        <th>Balance Value</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$sql_rtn=sql_select(" select a.pi_id, b.prod_id, sum(b.cons_quantity) as cons_quantity
				from inv_issue_master a, inv_transaction b 
				where a.id=b.mst_id and a.pi_id>0 and b.item_category in($item_category_id) and a.pi_id in($pi_id) and a.company_id=$company_id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by a.pi_id, b.prod_id");
				$return_data=array();
				foreach($sql_rtn as $row)
				{
					$return_data[$row[csf("pi_id")]][$row[csf("prod_id")]]=$row[csf("cons_quantity")];
				}
				$pi_name_val=sql_select(" select a.id, a.pi_number, a.pi_date from com_pi_master_details a where a.status_active=1 and a.is_deleted=0 and a.id in($pi_id)");
				$pi_data=array();
				foreach($pi_name_val as $row)
				{
					$pi_data[$row[csf("id")]]["pi_number"]=$row[csf("pi_number")];
					$pi_data[$row[csf("id")]]["pi_date"]=$row[csf("pi_date")];
				}
				//echo $item_category_id.test;
				if($item_category_id==4)
				{
					$sql="select c.pi_id, a.prod_id, d.product_name_details, sum(a.order_amount) as order_amount, sum(c.net_pi_amount) as net_pi_amount
					from inv_transaction a, com_pi_item_details c, product_details_master d
					where a.pi_wo_batch_no=c.pi_id and a.prod_id=d.id and c.item_group=d.item_group_id and c.item_description=d.item_description and c.item_color=d.item_color and c.item_size=d.item_size and c.color_id=d.color and c.size_id=d.gmts_size and a.item_category in($item_category_id) and a.transaction_type=1 and a.receive_basis=1 and a.pi_wo_batch_no in($pi_id) and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and d.entry_form=24
					group by c.pi_id, a.prod_id, d.product_name_details
					union all
					select c.pi_id, a.prod_id, d.product_name_details, sum(a.order_amount) as order_amount, sum(c.net_pi_amount) as net_pi_amount
					from inv_transaction a, com_pi_item_details c, product_details_master d
					where a.pi_wo_batch_no=c.pi_id and a.prod_id=d.id and c.item_prod_id=d.id and a.item_category in($item_category_id) and a.transaction_type=1 and a.receive_basis=1 and a.pi_wo_batch_no in($pi_id) and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and d.entry_form=20
					group by c.pi_id, a.prod_id, d.product_name_details";
				}
				else if($item_category_id==2 || $item_category_id==3)
				{
					$sql="select c.pi_id, a.prod_id, d.product_name_details, sum(a.order_amount) as order_amount, sum(c.net_pi_amount) as net_pi_amount
					from inv_receive_master b, inv_transaction a, com_pi_item_details c, product_details_master d
					where b.id=c.mst_id and b.booking_id=c.pi_id and a.prod_id=d.id and c.determination_id=d.detarmination_id and c.gsm=d.gsm and c.dia_width=d.dia_width and c.color_id=d.color and c.uom=d.unit_of_measure and a.item_category in($item_category_id) and a.transaction_type=1 and a.receive_basis=1 and b.receive_basis=1 and b.booking_id in($pi_id) and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0
					group by c.pi_id, a.prod_id, d.product_name_details";
				}
				else if($item_category_id==13 || $item_category_id==14)
				{
					$sql="select c.pi_id, a.prod_id, d.product_name_details, sum(a.order_amount) as order_amount, sum(c.net_pi_amount) as net_pi_amount
					from inv_receive_master b, inv_transaction a, com_pi_item_details c, product_details_master d
					where b.id=c.mst_id and b.booking_id=c.pi_id and a.prod_id=d.id and c.determination_id=d.detarmination_id and c.gsm=d.gsm and c.dia_width=d.dia_width and a.item_category in($item_category_id) and a.transaction_type=1 and a.receive_basis=1 and b.receive_basis=1 and b.booking_id in($pi_id) and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0
					group by c.pi_id, a.prod_id, d.product_name_details";
				}
				else
				{
					$sql="select c.pi_id, a.prod_id, d.product_name_details, sum(a.order_amount) as order_amount, sum(c.net_pi_amount) as net_pi_amount
					from inv_transaction a, com_pi_item_details c, product_details_master d
					where a.pi_wo_batch_no=c.pi_id and a.prod_id=d.id and c.item_prod_id=d.id and a.item_category in($item_category_id) and a.transaction_type=1 and a.receive_basis=1 and a.pi_wo_batch_no in($pi_id) and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0
					group by c.pi_id, a.prod_id, d.product_name_details";
				}
				$total_pi_qnty=0; $total_pi_amt=0;
				//echo $sql;//die;
				$result=sql_select($sql);
				//echo "<pre>";print_r($rcv_dtls_data);die;
				$i=1;
				foreach($result as $row)  
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td><p><? echo $pi_data[$row[csf("pi_id")]]["pi_number"]; ?>&nbsp;</p></td>
						<td align="center"><p><? if($pi_data[$row[csf("pi_id")]]["pi_date"]!="" && $pi_data[$row[csf("pi_id")]]["pi_date"]!="0000-00-00") echo change_date_format($pi_data[$row[csf("pi_id")]]["pi_date"]);?>&nbsp;</p></td>
                        <td><p><? echo $row[csf("product_name_details")]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('net_pi_amount')],2); $total_pi_amt+=$row[csf('net_pi_amount')];  ?></td>
                        <td align="right"><? echo number_format($row[csf('order_amount')],2); $total_rcv_amt+=$row[csf('order_amount')];  ?></td>
                        <td align="right"><? echo number_format($return_data[$row[csf("pi_id")]],2); $total_return_amt+=$return_data[$row[csf("pi_id")]];  ?></td>
                        <td align="right"><p><? $payable_amt=$row[csf('order_amount')]-$return_data[$row[csf("pi_id")]]; echo number_format($payable_amt,2); ?>&nbsp;</p></td>
                        <td align="right"><p><? $balance_amt=$row[csf('net_pi_amount')]-$payable_amt; echo number_format($balance_amt,2); ?>&nbsp;</p></td>
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
                        <th align="right"><? echo number_format($total_pi_amt,2) ; ?></th>
                        <th align="right"><? echo number_format($total_rcv_amt,2) ; ?></th>
                        <th align="right"><? echo number_format($total_return_amt,2) ; ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>  
            </table>
        </fieldset>
    </div>
	<?
    exit();

}

if($action=="accep_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$pi_id=str_replace("'","",$pi_id);
	$item_category_id=str_replace("'","",$item_category_id);
	?>
    <div id="report_container" align="center" style="width:800px">
	<fieldset style="width:800px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="800" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                        <th width="150">PI No.</th>
                        <th width="100">PI Date</th>
                        <th width="140">Payable Value</th>
                        <th width="100">Acceptance Date</th>
                        <th width="140">Accept. Given Value</th>
                        <th>Accept. Balance</th>
                    </tr>
                </thead>
                <tbody>
                <?
				
				$pi_name_val=sql_select(" select a.id, a.pi_number, a.pi_date from com_pi_master_details a where a.status_active=1 and a.is_deleted=0 and a.id in($pi_id)");
				$pi_data=array();
				foreach($pi_name_val as $row)
				{
					$pi_data[$row[csf("id")]]["pi_number"]=$row[csf("pi_number")];
					$pi_data[$row[csf("id")]]["pi_date"]=$row[csf("pi_date")];
				}
				
				$sql_receive="select b.booking_id, sum(a.order_amount) as order_amount
				from inv_transaction a, inv_receive_master b
				where a.mst_id=b.id and a.item_category in($item_category_id) and a.transaction_type=1 and a.receive_basis=1 and b.receive_basis=1 and b.booking_id in($pi_id) and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0
				group by b.booking_id";
				$sql_rcv_result=sql_select($sql_receive);
				foreach($sql_rcv_result as $row)
				{
					$rcv_data[$row[csf("booking_id")]]["order_amount"]=$row[csf("order_amount")];
				}
				$sql_rtn="select a.pi_id, sum(b.cons_quantity) as cons_quantity
				from inv_issue_master a, inv_transaction b 
				where a.id=b.mst_id and a.pi_id>0 and b.item_category in($item_category_id) and a.pi_id in($pi_id) and a.company_id=$company_id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by a.pi_id";
				//echo $sql_rtn;die;
				$sql_rtn_result=sql_select($sql_rtn);
				$return_data=array();
				foreach($sql_rtn_result as $row)
				{
					$return_data[$row[csf("pi_id")]]=$row[csf("cons_quantity")];
				}
				//echo $sql_receive;die;
				$sql_accep=" select a.pi_id, b.invoice_date, sum(a.current_acceptance_value) as current_acceptance_value
				from com_import_invoice_dtls a, com_import_invoice_mst b 
				where a.import_invoice_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id in($pi_id)
				group by a.pi_id, b.invoice_date
				order by a.pi_id";
				//echo $sql_accep;die;
				//$receive_return_sql=sql_select("select a.pi_id, sum(b.cons_quantity) as cons_quantity, sum(b.cons_amount) as cons_amount from inv_issue_master a,  inv_transaction b where a.id=b.mst_id and a.pi_id>0 and a.entry_form=8 and a.company_id=$cbo_company_name and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.pi_id");
				$total_pi_qnty=0; $total_pi_amt=0;
				//echo $sql;die; Return Date	Return No	Item Description	Qty	Rate	Value	Remarks
				$result=sql_select($sql_accep);$i=1;
				$accep_bal=array();
				foreach($result as $row)  
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					
					if($accep_pi_check[$row[csf("pi_id")]]=="")
					{
						$accep_pi_check[$row[csf("pi_id")]]=$row[csf("pi_id")];
						$payable_value=$rcv_data[$row[csf("pi_id")]]["order_amount"]-$return_data[$row[csf("pi_id")]];
						$accep_bal[$row[csf("pi_id")]]=$payable_value-$row[csf('current_acceptance_value')];
					}
					else
					{
						$payable_value=0;
						$accep_bal[$row[csf("pi_id")]]=$accep_bal[$row[csf("pi_id")]]-$row[csf('current_acceptance_value')];
					}
					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						
						<td><p><? echo $pi_data[$row[csf("pi_id")]]["pi_number"]; ?>&nbsp;</p></td>
                        <td align="center"><p><? if($pi_data[$row[csf("pi_id")]]["pi_date"]!="" && $pi_data[$row[csf("pi_id")]]["pi_date"]!="0000-00-00") echo change_date_format($pi_data[$row[csf("pi_id")]]["pi_date"]); ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($payable_value,2); $total_payable_value+=$payable_value;  ?></td>
                         <td align="center"><p><? if($row[csf("invoice_date")]!="" && $row[csf("invoice_date")]!="0000-00-00") echo change_date_format($row[csf("invoice_date")]); ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('current_acceptance_value')],2); $total_acceptance_value+=$row[csf('current_acceptance_value')];  ?></td>
						<td align="right"><p><? echo  number_format($accep_bal[$row[csf("pi_id")]],2); ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
                </tbody> 
                <tfoot>
                	<tr>
                        <th>&nbsp;</th>
                        <th>Total :</th>
                        <th align="right"><? echo number_format($total_payable_value,2) ; ?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_acceptance_value,2) ; ?></th>
                        <th>&nbsp;</th>

                    </tr>
                </tfoot>  
            </table>
        </fieldset>
    </div>
	<?
    exit();

}



?>
