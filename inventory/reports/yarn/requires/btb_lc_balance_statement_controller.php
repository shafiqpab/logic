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
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+'<? echo $btbLc_id; ?>', 'create_pi_search_list_view', 'search_div_pi', 'btb_lc_balance_statement_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>+'_'+'<? echo $pi_id; ?>', 'create_lc_search_list_view', 'search_div', 'btb_lc_balance_statement_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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
	//and a.ref_closing_status=0
	if($pi_id!="")
	{
		$sql= "select a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value 
		from com_btb_lc_master_details a, com_btb_lc_pi b 
		where a.id=b.com_btb_lc_master_details_id and b.pi_id=$pi_id and a.importer_id=$company and pi_entry_form=165 and a.supplier_id like '$cbo_supplier' and a.lc_number like '%".$txt_search_common."%' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
		group by a.id, a.lc_number, a.supplier_id, a.importer_id, a.lc_date, a.last_shipment_date, a.lc_value";
	}
	else
	{
		$sql= "select id, lc_number, supplier_id, importer_id, lc_date, last_shipment_date, lc_value from com_btb_lc_master_details where importer_id=$company and pi_entry_form=165 and supplier_id like '$cbo_supplier' and lc_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1";
	}
	//echo $sql;
	
	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "LC No, Importer, Supplier Name, LC Date, Last Shipment Date, LC Value","120,150,150,80,80","780","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "lc_number,importer_id,supplier_id,lc_date,last_shipment_date,lc_value", "",'','0,0,0,3,3,2') ;	
	exit();	
}

if ($action=="utilized_unutilized_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo '<pre>';print_r($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$pi_id=str_replace("'","",$pi_id);
	$un_uti_val=str_replace("'","",$un_utilize_val);
	$lc_val=str_replace("'","",$lc_value);
	$yarn_count_arr=return_library_array("SELECT id,yarn_count FROM lib_yarn_count","id","yarn_count");
	?>
    <div id="report_container" align="center" style="width:1050px">
		<fieldset style="width:1050px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="1050" cellpadding="0" cellspacing="0">
             	<thead>
             		<tr>
                        <th colspan="10">Block Purchase Order</th>
                    </tr>    
                	<tr>
                        <th width="50">SL</th>
                        <th width="120">PI No.</th>
                        <th width="120">Y/PO No.</th>
                        <th width="80">PO Date</th>
                        <th width="80">Count</th>
                        <th width="200">Composition</th>
                        <th width="80">Type</th>
                        <th width="100">PO Qty</th>
                        <th width="70">Rate</th>
                        <th>PO Value</th></th>                      
                    </tr>
                </thead>
                <tbody>
                <?						
				
				$sql="select a.id as pi_id, a.pi_number, b.count_name, b.yarn_composition_item1, b.yarn_type,  sum(b.quantity) as net_pi_amount, b.work_order_no, b.net_pi_rate, c.wo_date
				from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_mst c
				where a.id=b.pi_id and c.id=b.work_order_id and a.id in($pi_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.order_source=5
				group by a.id, a.pi_number, b.count_name, b.yarn_composition_item1, b.yarn_type, b.work_order_no, b.net_pi_rate, c.wo_date";
				//echo $sql;
				$result=sql_select($sql);$i=1;
				$tot_po_qty=0;$tot_total_po_val=0;
				foreach($result as $row)  
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    	<td  align="center"><p><? echo $i; ?>&nbsp;</p></td>
                        <td><p><? echo $row[csf('pi_number')]; ?>&nbsp;</p></td>
                        <td><p><? echo $row[csf('work_order_no')]; ?></p></td>
                        <td><p><? if($row[csf('wo_date')]!="" && $row[csf('wo_date')]!="0000-00-00") echo change_date_format($row[csf('wo_date')]);?>&nbsp;</p></p></td>
                        <td><p><? echo $yarn_count_arr[$row[csf('count_name')]]; ?>&nbsp;</p></td>
                        <td><p><? echo $composition[$row[csf("yarn_composition_item1")]]; ?>&nbsp;</p></td>
                        <td><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf("net_pi_amount")],2); ?></td>
                        <td align="right"><? echo number_format($row[csf("net_pi_rate")],2);?></td>
                        <td align="right"><? $total_po_val=$row[csf("net_pi_amount")]*$row[csf("net_pi_rate")]; echo number_format($row[csf("net_pi_amount")]*$row[csf("net_pi_rate")],2); ?></td>
                    </tr>
                    <?
                    $tot_po_qty+=$row[csf("net_pi_amount")];
                    $tot_total_po_val+=$total_po_val;
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
                        <th>Total :</th>
                        <th align="right"><? echo number_format($tot_po_qty,2) ; ?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($tot_total_po_val,2) ; ?></th>
                    </tr>
                </tfoot>  
            </table>
        </fieldset>
        <br>
        <fieldset style="width:1050px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="1050" cellpadding="0" cellspacing="0">
             	<thead>
             		<tr>
                        <th colspan="10">Utilized Purchase Order</th>
                    </tr>    
                	<tr>
                        <th width="50">SL</th>
                        <th width="120">PI No.</th>
                        <th width="120">Y/PO No.</th>
                        <th width="80">PO Date</th>
                        <th width="80">Count</th>
                        <th width="200">Composition</th>
                        <th width="80">Type</th>
                        <th width="100">PO Qty</th>
                        <th width="70">Rate</th>
                        <th>PO Value</th></th>                      
                    </tr>
                </thead>
                <tbody>
                <?						
				
				$sql_unutilize="select a.id as pi_id, a.pi_number, b.count_name, b.yarn_composition_item1, b.yarn_type,  sum(b.quantity) as net_pi_amount, b.work_order_no, b.net_pi_rate, c.wo_date
				from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_mst c
				where a.id=b.pi_id and c.id=b.work_order_id and a.id in($pi_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.order_source<>5
				group by a.id, a.pi_number, b.count_name, b.yarn_composition_item1, b.yarn_type, b.work_order_no, b.net_pi_rate, c.wo_date";
				//echo $sql;
				$sql_unutilize_res=sql_select($sql_unutilize);$j=1;
				$tot_po_qty=0;$tot_total_po_val=0;
				foreach($sql_unutilize_res as $row)  
				{
					if ($j%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trds_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $j; ?>">
                    	<td  align="center"><p><? echo $j; ?>&nbsp;</p></td>
                        <td><p><? echo $row[csf('pi_number')]; ?>&nbsp;</p></td>
                        <td><p><? echo $row[csf('work_order_no')]; ?></p></td>
                        <td><p><? if($row[csf('wo_date')]!="" && $row[csf('wo_date')]!="0000-00-00") echo change_date_format($row[csf('wo_date')]);?>&nbsp;</p></p></td>
                        <td><p><? echo $yarn_count_arr[$row[csf('count_name')]]; ?>&nbsp;</p></td>
                        <td><p><? echo $composition[$row[csf("yarn_composition_item1")]]; ?>&nbsp;</p></td>
                        <td><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf("net_pi_amount")],2); ?></td>
                        <td align="right"><? echo number_format($row[csf("net_pi_rate")],2);?></td>
                        <td align="right"><? $total_po_val=$row[csf("net_pi_amount")]*$row[csf("net_pi_rate")]; echo number_format($row[csf("net_pi_amount")]*$row[csf("net_pi_rate")],2); ?></td>
                    </tr>
                    <?
                    $tot_po_qty+=$row[csf("net_pi_amount")];
                    $tot_total_po_val+=$total_po_val;
                    $j++;
				}
				?>
                </tbody> 
                <tfoot>
                	<tr>
                        <th colspan="7" align="right">Total Utilized:</th>
                        <th align="right"><? echo number_format($tot_po_qty,2) ; ?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($tot_total_po_val,2) ; ?></th>
                    </tr>
                    <tr>
                    	<th colspan="9" align="right">LC Value</th>
                    	<th align="right"><? echo number_format($lc_val,2) ; ?></th>
                    </tr>
                    <tr>
                    	<th colspan="9" align="right">Un-utilized</th>
                    	<th align="right"><? $tot=$lc_val-$tot_total_po_val; echo number_format($tot,2); ?></th>
                    </tr>
                </tfoot>  
            </table>
        </fieldset>
    </div>
	<?
    exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
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
	
	$exchange_rate=set_conversion_rate( 2, $conversion_date );
	if($rpt_type==1)
	{
		$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "con_date=(select max(con_date) as con_date   from currency_conversion_rate)" , "conversion_rate" );
		$sql_cond="";
		$btbLc_id_str=str_replace("'","",$btbLc_id);
		if($cbo_company_name!="") $company_cond=" and a.company_id in ($cbo_company_name)";
		if($cbo_company_name!="") $sql_cond.=" and c.importer_id in ($cbo_company_name)";
		if($cbo_suppler_name>0) $sql_cond.=" and c.supplier_id=$cbo_suppler_name";
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

		// sql Utilize Un-utilize
		if($pi_id !="") $pi_condition.=" and a.id=$pi_id"; else $pi_condition="";
		$sql="SELECT b.net_pi_amount, b.order_source, d.id as btb_id FROM com_pi_master_details a, com_pi_item_details b,  com_btb_lc_pi c, com_btb_lc_master_details d WHERE a.id = b.pi_id AND a.id = c.pi_id AND c.com_btb_lc_master_details_id = d.id AND a.id = c.pi_id AND a.entry_form = 165 AND d.pi_entry_form=165 AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0 AND b.status_active = 1 AND c.is_deleted = 0 AND c.status_active = 1 AND d.is_deleted = 0 AND d.status_active = 1 $pi_condition";
		$sql_res=sql_select($sql);
		$utilize_arr=array();
		$un_utilize_arr=array();
		foreach ($sql_res as  $value)
		{
			if ($value[csf("order_source")] == 5)
			{
				$un_utilize_arr[$value[csf("btb_id")]]['un-utilize'] = $value[csf("net_pi_amount")];
			}
			else
			{
				$utilize_arr[$value[csf("btb_id")]]['utilize'] += $value[csf("net_pi_amount")];
			}			
		}
		
		//LISTAGG(CAST(doc_submission_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY doc_submission_mst_id) as id
		$ref_close_cond="";
		if($cbo_receiving_status==4) $ref_close_cond=" and c.ref_closing_status=0";
		if($db_type==0)
		{
			$btb_sql=sql_select("select  group_concat(a.id) as pi_id, c.id as btb_id, c.importer_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.item_category_id, c.supplier_id, c.lc_value as btb_value, c.lc_category, c.last_shipment_date, d.is_lc_sc, group_concat(d.lc_sc_id) as lc_sc_id, a.goods_rcv_status
			from com_pi_master_details a, com_btb_lc_pi b, com_btb_lc_master_details c left join com_btb_export_lc_attachment d on c.id=d.import_mst_id and d.is_deleted=0 and d.status_active=1 
			where a.id=b.pi_id and b.com_btb_lc_master_details_id=c.id and pi_entry_form=165 and c.is_deleted=0 and c.status_active=1 $ref_close_cond $sql_cond
			group by c.id, c.importer_id, c.lc_number, c.lc_date, c.item_category_id, c.supplier_id, c.lc_value, c.lc_category, c.last_shipment_date, d.is_lc_sc, a.goods_rcv_status
			order by c.lc_number");
		}
		else
		{
			$btb_sql=sql_select("select LISTAGG(CAST(a.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.id) as pi_id, c.id as btb_id, c.importer_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.item_category_id, c.supplier_id, c.lc_value as btb_value, c.lc_category, c.last_shipment_date, d.is_lc_sc, LISTAGG(CAST(d.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.lc_sc_id) as lc_sc_id, a.goods_rcv_status
			from com_pi_master_details a, com_btb_lc_pi b, com_btb_lc_master_details c left join com_btb_export_lc_attachment d on c.id=d.import_mst_id and d.is_deleted=0 and d.status_active=1 
			where a.id=b.pi_id and b.com_btb_lc_master_details_id=c.id and pi_entry_form=165 and c.is_deleted=0 and c.status_active=1 $ref_close_cond $sql_cond
			group by c.id, c.importer_id, c.lc_number, c.lc_date, c.item_category_id, c.supplier_id, c.lc_value, c.lc_category, c.last_shipment_date, d.is_lc_sc, a.goods_rcv_status");
		}
		
		//echo "select  c.id as btb_id, c.importer_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.item_category_id, c.supplier_id, c.lc_value as btb_value, c.lc_category, c.last_shipment_date, LISTAGG(CAST(d.is_lc_sc AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.is_lc_sc) as is_lc_sc, LISTAGG(CAST(d.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.lc_sc_id) as lc_sc_id	from com_pi_master_details a,  com_btb_lc_pi b, com_btb_lc_master_details c left join com_btb_export_lc_attachment d on c.id=d.import_mst_id and d.is_deleted=0 and d.status_active=1 where a.id=b.pi_id and b.com_btb_lc_master_details_id=c.id and c.item_category_id=1 and c.importer_id=$cbo_company_name and c.is_deleted=0 and c.status_active=1 $sql_cond group by c.id, c.importer_id, c.lc_number, c.lc_date, c.item_category_id, c.supplier_id, c.lc_value, c.lc_category, c.last_shipment_date";die;
		$all_pi_id=$all_btb_id=$all_lc_id=$all_sc_id="";
		foreach($btb_sql as $row)
		{
			$all_btb_id.=$row[csf("btb_id")].",";
			if($row[csf("is_lc_sc")]==0)
			{
				if($row[csf("lc_sc_id")]) $all_lc_id.=implode(",",array_unique(explode(",",$row[csf("lc_sc_id")]))).",";
			}
			else
			{
				if($row[csf("lc_sc_id")]) $all_sc_id.=implode(",",array_unique(explode(",",$row[csf("lc_sc_id")]))).",";
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
			
			$sql_lc=sql_select("select id, export_lc_no, internal_file_no from  com_export_lc where beneficiary_name in($cbo_company_name) $lc_id_cond");
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
			
			$sql_sc=sql_select("select id, contract_no, internal_file_no from com_sales_contract where beneficiary_name in($cbo_company_name) $sc_id_cond");
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
		$pi_sql=sql_select("select a.pi_id as pi_id, a.id as pi_dtls_id, a.quantity as pi_qnty, a.net_pi_amount, b.com_btb_lc_master_details_id as btb_lc_id, a.work_order_id, p.goods_rcv_status, c.id as inv_id, c.current_acceptance_value as accep_value 
		from com_pi_master_details p, com_pi_item_details a, com_btb_lc_pi b left join com_import_invoice_dtls c on b.pi_id=c.pi_id and b.com_btb_lc_master_details_id=c.btb_lc_id and c.status_active=1 and c.is_deleted=0
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
			if($pi_acep_check[$row[csf("btb_lc_id")]][$row[csf("inv_id")]]=="")
			{
				$pi_acep_check[$row[csf("btb_lc_id")]][$row[csf("inv_id")]]=$row[csf("inv_id")];
				$pi_data_arr[$row[csf("btb_lc_id")]]["accep_value"]+=$row[csf("accep_value")];
			}
			if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
			{
				$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				$pi_data_arr[$row[csf("btb_lc_id")]]["pi_qnty"]+=$row[csf("pi_qnty")];
			}
		}
		
		//echo $all_pi_id=chop($all_wo_id,",").test;die;
		$all_pi_id_arr=array_unique(explode(",",chop($all_pi_id,",")));
		$all_wo_id_arr=array_unique(explode(",",chop($all_wo_id,",")));
		$pi_ids=$pi_ids_cond=$pi_return_ids=$pi_ids_return_cond="";$wo_id_conds="";
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
						
				$pi_ids_cond=" and ((".chop($pi_ids,'or ').") and a.receive_basis=1)";	
				//$pi_ids_return_cond=" and (".chop($pi_return_ids,'or ').")";			
			}
			else
			{ 	
				$pi_ids_cond=" and (a.booking_id in(".chop($all_pi_id,",").") and a.receive_basis=1)"; 
				//$pi_ids_return_cond=" and a.pi_id in($all_pi_id)"; 
			}
		}
		//echo $pi_ids_cond;die;
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
						
				$wo_id_conds=" and (".chop($pi_ids,'or ').") and a.receive_basis=2";	
				//$pi_ids_return_cond=" and (".chop($pi_return_ids,'or ').")";			
			}
			else
			{ 	
				$wo_id_conds=" and a.booking_id in(".chop($all_wo_id,",").") and a.receive_basis=2"; 
				//$pi_ids_return_cond=" and a.pi_id in($all_pi_id)"; 
			}
		}
		//echo $pi_ids_cond."=".$wo_id_conds;die;
		if($pi_ids_cond!="")
		{
			$receive_sql="select a.id, a.receive_basis, a.booking_id, a.receive_basis, b.order_qnty as order_qnty, b.order_amount as order_amount, b.order_rate, b.mst_id, b.prod_id
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id and a.receive_basis in(1,2) and a.entry_form=1 and b.item_category=1 and b.transaction_type=1 $company_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $pi_ids_cond ";
		}
		
		if($wo_id_conds!="" && $pi_ids_cond!="")
		{
			$receive_sql.="union all select a.id, a.receive_basis, a.booking_id, a.receive_basis, b.order_qnty as order_qnty, b.order_amount as order_amount, b.order_rate, b.mst_id, b.prod_id 
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id and a.receive_basis in(1,2) and a.entry_form=1 and b.item_category=1 and b.transaction_type=1 $company_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_id_conds";
		}
		else if($wo_id_conds!="" && $pi_ids_cond=="")
		{
			$receive_sql="select a.id, a.receive_basis, a.booking_id, a.receive_basis, b.order_qnty as order_qnty, b.order_amount as order_amount, b.order_rate, b.mst_id, b.prod_id 
			from inv_receive_master a, inv_transaction b 
			where a.id=b.mst_id and a.receive_basis in(1,2) and a.entry_form=1 and b.item_category=1 and b.transaction_type=1 $company_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_id_conds";
		}
		
		//echo $receive_sql;//die;
		$receive_resut=sql_select($receive_sql);
		$recv_data_arr=array();
		foreach($receive_resut as $row)
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
			$rcv_wise_rate[$row[csf("mst_id")]][$row[csf("prod_id")]]=$row[csf("order_rate")];			
		}
		
		if($db_type==2)
		{
			$all_rcv_id_chunk=array_chunk($all_rcv_id,'950');
			$all_pi_id_arr_chunk=array_chunk($all_pi_id_arr,'950');
			$receive_return_sql="select a.received_id, b.transaction_date, b.cons_quantity as cons_quantity, b.cons_amount as cons_amount, b.rcv_amount as rcv_amount, b.prod_id, a.pi_id, 1 as type 
			from inv_issue_master a, inv_transaction b 
			where a.id=b.mst_id and a.entry_form=8 and b.item_category=1 and b.transaction_type=3 $company_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			
			
			$rcv_cond="";$rcv_conds="";
			foreach($all_rcv_id_chunk as $rcv_id)
			{
				$rcv_cond.=" a.received_id in(".implode(",",$rcv_id).") or";
				$rcv_conds.=" a.received_id not in(".implode(",",$rcv_id).") and";
			}
			$rcv_cond=chop($rcv_cond,"or");
			$rcv_conds=chop($rcv_conds,"and");
			$pi_conds="";
			foreach($all_pi_id_arr_chunk as $pi_ids)
			{
				$pi_conds.=" a.pi_id in(".implode(",",$pi_ids).") or";
			}
			$pi_conds=chop($pi_conds,"or");
			$receive_return_sql.=" and ($rcv_cond) 
			union all 
			select a.received_id, b.transaction_date, b.cons_quantity as cons_quantity, b.cons_amount as cons_amount, b.rcv_amount as rcv_amount, b.prod_id, a.pi_id, 2 as type 
			from inv_issue_master a, inv_transaction b 
			where a.id=b.mst_id and a.entry_form=8 and b.item_category=1 and b.transaction_type=3 $company_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			
			$receive_return_sql.=" and ($pi_conds) and ($rcv_conds)";
		}
		else
		{
			if(implode(",",$all_rcv_id)!="")  
			{
				$receive_return_sql="select a.received_id, b.transaction_date, b.cons_quantity as cons_quantity, b.cons_amount as cons_amount, b.rcv_amount as rcv_amount, b.prod_id, a.pi_id, 1 as type 
				from inv_issue_master a, inv_transaction b 
				where a.id=b.mst_id and a.entry_form=8 and b.item_category=1 and b.transaction_type=3 $company_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.received_id in(".implode(",",$all_rcv_id).")
				union all 
				select a.received_id, b.transaction_date, b.cons_quantity as cons_quantity, b.cons_amount as cons_amount, b.rcv_amount as rcv_amount, b.prod_id, a.pi_id, 2 as type 
				from inv_issue_master a, inv_transaction b 
				where a.id=b.mst_id and a.entry_form=8 and b.item_category=1 and b.transaction_type=3 $company_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id in(".implode(",",$all_pi_id_arr).") and a.received_id not in(".implode(",",$all_rcv_id).")";
			}
		}		
		
		//echo $receive_return_sql;
		$receive_return_result=sql_select($receive_return_sql);
		$recv_rtn_data_arr=array();
		foreach($receive_return_result as $row)
		{
			if($db_type==0)
			{

				$conversion_date=date("Y-m-d",strtotime($row[csf("transaction_date")]));
			}
			else
			{
				$conversion_date=date("d-M-y",strtotime($row[csf("transaction_date")]));
			}
			
			if($row[csf("type")]==1)
			{
				$wo_pi_ref=explode("__",$rcv_pi_wo_id[$row[csf("received_id")]]);
				$recv_rtn_data_arr[$wo_pi_ref[0]][$wo_pi_ref[1]]["rcv_qnty"]+=$row[csf("cons_quantity")];
				$recv_rtn_data_arr[$wo_pi_ref[0]][$wo_pi_ref[1]]["rcv_amt"]+=$row[csf("cons_quantity")]*$rcv_wise_rate[$row[csf("received_id")]][$row[csf("prod_id")]];
			}
			else
			{
				$recv_rtn_data_arr[$row[csf("pi_id")]][1]["rcv_qnty"]+=$row[csf("cons_quantity")];
				$recv_rtn_data_arr[$row[csf("pi_id")]][1]["rcv_amt"]+=$row[csf("cons_amount")]/$exchange_rate;
			}
			
			
			/*if($row[csf("cons_amount")]>0)
			{
				$recv_rtn_data_arr[$wo_pi_ref[0]][$wo_pi_ref[1]]["rcv_amt"]+=$row[csf("cons_amount")]/$exchange_rate;
			}
			else
			{
				$recv_rtn_data_arr[$wo_pi_ref[0]][$wo_pi_ref[1]]["rcv_amt"]+=$row[csf("rcv_amount")]/$exchange_rate;
			}*/			
		}
		//echo $cbo_receiving_status;die;
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
				if(number_format($row[csf("btb_value")],2,'.','')>number_format($rcv_btb_value,2,'.','') && $rcv_btb_value>0 )
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
				
				if(number_format($rcv_btb_value,2,'.','') >= number_format($row[csf("btb_value")],2,'.',''))
				{
					$test_data.=number_format($rcv_btb_value,2)."=".number_format($row[csf("btb_value")],2)."uuu";
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
				//else $test_data.=number_format($rcv_btb_value,2)."=".number_format($row[csf("btb_value")],2)."=";
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
				if((number_format($row[csf("btb_value")],2,'.','')>number_format($rcv_btb_value,2,'.','') && $rcv_btb_value>0) || $rcv_btb_value==0 || $rcv_btb_value=="")
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
		//echo $test_data;//die;
		//echo "<pre>";print_r($btb_data_arr);
		?>
		<div style="width:2120px; margin-left:10px;" align="left">
			<table width="2100" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
				<tr>
				   <td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
				</tr>
			</table>
			<table width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="40">SL</th>
						<th width="70">LC Date</th>
						<th width="160">Supplier</th>
						<th width="100">BTB LC No.</th>
						<th width="100">LC Value</th>
						<th width="100">Utilized Value</th>
						<th width="100">Un-Utilized Value</th>
						<th width="70">Shipment Date</th>
						<th width="100">Import Source</th>
						<th width="200">File No. & <br> (Export LC/SC No.)</th>  
						<th width="140">Receiving Company</th>
						<th width="80">LC/PI Qty</th>
						<th width="80">Total Received Qty</th>
						<th width="80">Receive Return Qty</th>
						<th width="80">Balance Qty</th>
						<th width="100">Received Value</th> 
                        <th width="100">Rcv.Rtn Value</th>
                        <th width="100">Payable Value</th>
						<th width="100">Balance Value</th>
                        <th width="100">Accp. Value</th>
                        <th>Accp. Balance Value</th>
					</tr>
				</thead>
			</table>
			<div style="width:2120px; overflow-y:scroll; max-height:350px; overflow-x:hidden;" id="scroll_body" align="left">
			<table width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_bodyy">   
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
					$utilize_val= $utilize_arr[$val["btb_id"]]['utilize'];
					$un_utilize_val= $un_utilize_arr[$val["btb_id"]]['un-utilize'];
					//echo $all_rcv_ids;die;
					//$all_booking_pi_id[$row[csf("booking_id")]."_".$row[csf("receive_basis")]].=$row[csf("id")].",";
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="40" align="center" title="<? echo $val["rcv_btb_value"] ?>"><? echo $i; ?></td>
						<td width="70" align="center"><p><? if($val["btb_lc_date"]!="" && $val["btb_lc_date"]!="0000-00-00") echo change_date_format($val["btb_lc_date"]); ?>&nbsp;</p></td>
						<td width="160"><p><? echo $supplier_arr[$val["supplier_id"]]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $val["btb_lc_number"]; ?>&nbsp;</p></td>
						<td width="100" align="right"><p><? echo number_format($val["btb_value"],2); ?>&nbsp;</p></td>
						<td width="100" align="right"><p><a href="##" onClick="openmypage_popup_utilize_unutilize('<? echo $val["importer_id"]; ?>','<? echo $btb_pi_id; ?>','<? echo $un_utilize_val; ?>','<? echo $val["btb_value"]; ?>','Utilized Info','utilized_unutilized_popup');" ><? echo number_format($utilize_val,2); ?></a></p></td>
						<td width="100" align="right"><p><a href="##" onClick="openmypage_popup_utilize_unutilize('<? echo $val["importer_id"]; ?>','<? echo $btb_pi_id; ?>','<? echo $un_utilize_val; ?>','<? echo $val["btb_value"]; ?>','Un-utilized Info','utilized_unutilized_popup');" ><? echo number_format($val["btb_value"]-$utilize_val,2); ?></a></p></td>
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
						<td width="80" align="right"><a href="##" onClick="openmypage_popup('<? echo $val["importer_id"]."_".$all_pi_id; ?>','<? echo chop($all_rcv_ids,","); ?>','Receive Return Info','receive_return_popup');"><? echo number_format($rcv_return_qnty,2); $total_rcv_return_qnty+=$rcv_return_qnty; ?></a></td>
						<td width="80" align="right"><a href="##" onClick="openmypage_popup('<? echo $val["importer_id"]."_".$rcv_basis; ?>','<? echo $all_pi_id; ?>','Receive Info','balance_popup');" ><? echo number_format($balance_qnty,2);  $total_balance_qnty+=$balance_qnty; ?></a></td>
						<td width="100" align="right"><? echo number_format($rcv_value,2); $total_rcv_value+=$rcv_value; ?></td> 
                        <td width="100" align="right"><? echo number_format($rcv_return_value,2); $total_rcv_return_value+=$rcv_return_value; ?></td>  
                        <td width="100" align="right"><a href="##" onClick="openmypage_popup('<? echo $val["importer_id"]; ?>','<? echo $all_pi_id; ?>','Payable Info','payable_popup');"><? $payable_value=$rcv_value-$rcv_return_value; echo number_format($payable_value,2); $total_payable_value+=$payable_value; ?></a></td>  
						<td width="100" align="right" title="<? echo $val["btb_value"]."=".$rcv_value."=".$rcv_return_value; ?>"><? echo number_format($balance_value,2); $total_balance_value+=$balance_value; ?></td> 
                        <td width="100" align="right" title="<? echo $val["btb_value"]."=".$rcv_value."=".$rcv_return_value; ?>"><a href="##" onClick="openmypage_popup('<? echo $val["importer_id"]; ?>','<? echo $all_pi_id; ?>','Acceptance Info','accep_popup');"><? $accep_value=$pi_data_arr[$val["btb_id"]]["accep_value"];  echo number_format($accep_value,2); $total_accep_value+=$accep_value; ?></a></td> 
                        <td align="right" title="<? echo "(payable value-accep value)".$payable_value."=".$accep_value; ?>"><? $accep_balance_value=$payable_value-$accep_value; echo number_format($accep_balance_value,2); $total_accep_balance_value+=$accep_balance_value; ?></td>
					</tr>
					<?
					$i++;
					$tot_btb_value += $val["btb_value"];
					$tot_utilize_val += $utilize_val;
					$tot_un_utilize_val += $un_utilize_val;
				}
				?>
				</tbody>         	
			</table>
			</div>
			<table width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<tfoot>
					<tr>
						<th width="40">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="160">&nbsp;</th>
						<th width="100">Total</th>
						<th width="100" align="right" id="value_total_btb_value"><? echo number_format($tot_btb_value,2); ?></th>
						<th width="100" align="right" id="value_tot_utilize_val"><? echo number_format($tot_utilize_val,2); ?></th>
						<th width="100" align="right" id="value_tot_un_utilize_val"><? echo number_format($tot_un_utilize_val,2); ?></th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="200">&nbsp;</th>  
						<th width="140">&nbsp;</th>
						<th width="80" align="right" id="value_total_pi_qnty"><? echo number_format($total_pi_qnty,2); ?></th>
						<th width="80" align="right" id="value_total_rcv_qnty"><? echo number_format($total_rcv_qnty,2); ?></th>
						<th width="80" align="right" id="value_total_rcv_return_qnty"><? echo number_format($total_rcv_return_qnty,2); ?></th>
						<th width="80" align="right" id="value_total_balance_qty"><? echo number_format($total_balance_qnty,2); ?></th>
						<th width="100" align="right" id="value_total_rcev_value"><? echo number_format($total_rcv_value,2); ?></th>
                        <th width="100" align="right" id="value_total_rcv_return_value"><? echo number_format($total_rcv_return_value,2); ?></th>
                        <th width="100" align="right" id="value_total_payable_value"><? echo number_format($total_payable_value,2); ?></th> 
						<th width="100" align="right" id="value_total_bal_value"><? echo number_format($total_balance_value,2); ?></th>
                        <th width="100" align="right" id="value_total_accep_value"><? echo number_format($total_accep_value,2); ?></th>
                        <th align="right" id="value_total_accep_balance_value"><? echo number_format($total_accep_balance_value,2); ?></th>
					</tr>
				</tfoot>
			</table>
		</div>      
		<?
	}
	else
	{
		$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
		$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
		$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "con_date=(select max(con_date) as con_date from currency_conversion_rate)" , "conversion_rate" );
		$sql_cond="";
		$btbLc_id_str=str_replace("'","",$btbLc_id);
		if($cbo_company_name!="") $company_cond=" and a.company_id in ($cbo_company_name)";
		if($cbo_company_name!="") $sql_cond.=" and a.importer_id in ($cbo_company_name)";
		if($cbo_suppler_name>0) $sql_cond.=" and a.supplier_id=$cbo_suppler_name";
		if($btbLc_id!="") $sql_cond.=" and c.id=$btbLc_id";
		if($pi_id!="") $sql_cond.=" and a.id=$pi_id";
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
		
		
		
		$sql_sc=sql_select("select id, contract_no, internal_file_no from com_sales_contract where beneficiary_name in($cbo_company_name)");
		$sc_data_arr=array();
		foreach($sql_sc as $row)
		{
			$sc_data_arr[$row[csf("id")]]['lc_sc_id']=$row[csf("id")];
			$sc_data_arr[$row[csf("id")]]['lc_sc_no']=$row[csf("contract_no")];
			$sc_data_arr[$row[csf("id")]]['internal_file_no']=$row[csf("internal_file_no")];
		}
		
		$sql_lc=sql_select("select id, export_lc_no, internal_file_no from  com_export_lc where beneficiary_name in($cbo_company_name)");
		$lc_data_arr=array();
		foreach($sql_lc as $row)
		{
			$lc_data_arr[$row[csf("id")]]['lc_sc_id']=$row[csf("id")];
			$lc_data_arr[$row[csf("id")]]['lc_sc_no']=$row[csf("export_lc_no")];
			$lc_data_arr[$row[csf("id")]]['internal_file_no']=$row[csf("internal_file_no")];
			
		}
		
		
		
		
		//LISTAGG(CAST(doc_submission_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY doc_submission_mst_id) as id
		$ref_close_cond="";
		if($cbo_receiving_status==4) $ref_close_cond=" and c.ref_closing_status=0";
		if($db_type==0)
		{
			$btb_sql="select a.id as pi_id, a.goods_rcv_status, a.importer_id, a.supplier_id, a.item_category_id, a.pi_number, a.pi_date, a.net_total_amount as pi_value, a.remarks, a.source as lc_category, a.last_shipment_date, p.id as pi_dtls_id, p.count_name, p.yarn_composition_item1, p.yarn_type, p.color_id, c.id as btb_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, p.quantity as pi_item_qtny, p.net_pi_amount as pi_item_value, group_concat(d.is_lc_sc) as is_lc_sc, group_concat(d.lc_sc_id) as lc_sc_id, group_concat(p.work_order_id) as work_order_id
			from com_pi_item_details p, com_pi_master_details a left join com_btb_lc_pi b on a.id=b.pi_id left join  com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id left join com_btb_export_lc_attachment d on c.id=d.import_mst_id and d.is_deleted=0 and d.status_active=1 
			where p.pi_id=a.id and a.entry_form=165 and a.is_deleted=0 and a.status_active=1 and p.is_deleted=0 and p.status_active=1 $ref_close_cond $sql_cond
			group by a.id, a.goods_rcv_status, a.importer_id, a.supplier_id, a.item_category_id, a.pi_number, a.pi_date, a.net_total_amount, a.remarks, a.source, a.last_shipment_date, p.id, p.count_name, p.yarn_composition_item1, p.yarn_type, p.color_id, c.id, c.lc_number, c.lc_date , p.quantity, p.net_pi_amount order by a.id";
		}
		else
		{
			$btb_sql="select a.id as pi_id, a.goods_rcv_status, a.importer_id, a.supplier_id, a.item_category_id, a.pi_number, a.pi_date, a.net_total_amount as pi_value, a.remarks, a.source as lc_category, a.last_shipment_date, p.id as pi_dtls_id, p.count_name, p.yarn_composition_item1, p.yarn_type, p.color_id, c.id as btb_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, p.quantity as pi_item_qtny, p.net_pi_amount as pi_item_value, rtrim(xmlagg(xmlelement(e,d.is_lc_sc,',').extract('//text()') order by d.is_lc_sc).GetClobVal(),',') as is_lc_sc, rtrim(xmlagg(xmlelement(e,d.lc_sc_id,',').extract('//text()') order by d.lc_sc_id).GetClobVal(),',') as lc_sc_id, listagg(cast(p.work_order_id as varchar(4000)),',') within group (order by p.id) as work_order_id
			from com_pi_item_details p, com_pi_master_details a left join com_btb_lc_pi b on a.id=b.pi_id left join  com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id left join com_btb_export_lc_attachment d on c.id=d.import_mst_id and d.is_deleted=0 and d.status_active=1 
			where p.pi_id=a.id and a.entry_form=165 and a.is_deleted=0 and a.status_active=1 and p.is_deleted=0 and p.status_active=1 $ref_close_cond $sql_cond
			group by a.id, a.goods_rcv_status, a.importer_id, a.supplier_id, a.item_category_id, a.pi_number, a.pi_date, a.net_total_amount, a.remarks, a.source, a.last_shipment_date, p.id, p.count_name, p.yarn_composition_item1, p.yarn_type, p.color_id, c.id, c.lc_number, c.lc_date , p.quantity, p.net_pi_amount order by a.id";
		}
		
		//echo $btb_sql;//die;
		
		
		$btb_result=sql_select($btb_sql);
		$btb_result=sql_select($btb_sql); $btb_val_arr=array();
		foreach($btb_result as $row)
		{
			$btb_val_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_item_qtny"]+=$row[csf("pi_item_qtny")];
			$btb_val_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_item_value"]+=$row[csf("pi_item_value")];
			
		}
		//echo "<pre>";print_r($btb_val_arr);
		$receive_sql=sql_select("select a.id as rcv_id, a.booking_id as pi_id, a.receive_basis, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color, b.order_qnty as order_qnty, b.order_amount as order_amount, c.id as prod_id, b.order_rate
		from inv_receive_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and a.receive_basis in(1,2) and a.entry_form=1 and b.item_category=1 and c.item_category_id=1 $company_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$recv_data_arr=array();
		foreach($receive_sql as $row)
		{
			$recv_data_arr[$row[csf("pi_id")]][$row[csf("receive_basis")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_qnty"]+=$row[csf("order_qnty")];
			$recv_data_arr[$row[csf("pi_id")]][$row[csf("receive_basis")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_amt"]+=$row[csf("order_amount")];
			$rcv_book_id[$row[csf("rcv_id")]]=$row[csf("pi_id")]."__".$row[csf("receive_basis")];
			$rcv_item_rate[$row[csf("rcv_id")]][$row[csf("prod_id")]]=$row[csf("order_rate")];
			if($rcv_id_check[$row[csf("rcv_id")]]=="")
			{
				$rcv_id_check[$row[csf("rcv_id")]]=$row[csf("rcv_id")];
				$book_rcv_id[$row[csf("pi_id")]."__".$row[csf("receive_basis")]].=$row[csf("rcv_id")].",";
			}
		}
		
		//echo "<pre>"; print_r( $recv_data_arr[14120][1]);die;
		
		$receive_return_sql=sql_select("select a.pi_id, a.received_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color, b.cons_quantity as cons_quantity, b.rcv_amount, c.id as prod_id, b.cons_amount, c.id as prod_id
		from inv_issue_master a, inv_transaction b, product_details_master c  
		where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and c.item_category_id=1  and a.entry_form=8 $company_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$recv_rtn_data_arr=array();
		foreach($receive_return_sql as $row)
		{
			$rcv_data_ref=explode("__",$rcv_book_id[$row[csf("received_id")]]);
			$wo_pi_id=$rcv_data_ref[0];
			$receive_basis=$rcv_data_ref[1];
			if($wo_pi_id=="" && $row[csf("pi_id")] > 0)
			{
				$wo_pi_id=$row[csf("pi_id")];
				$receive_basis=1;
				$book_rcv_id[$row[csf("pi_id")]."__1"].=$row[csf("received_id")].",";
				$recv_rtn_data_arr[$wo_pi_id][$receive_basis][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_qnty"]+=$row[csf("cons_quantity")];
				//$recv_rtn_data_arr[$wo_pi_id][$receive_basis][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_amt"]+=$row[csf("cons_amount")];
				//if($wo_pi_id==17679)echo $row[csf("cons_amount")]."=".$exchange_rate;
				$recv_rtn_data_arr[$wo_pi_id][$receive_basis][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_amt"]+=$row[csf("cons_amount")]/$exchange_rate;
			}
			else
			{
				$recv_rtn_data_arr[$wo_pi_id][$receive_basis][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_qnty"]+=$row[csf("cons_quantity")];
				//$recv_rtn_data_arr[$wo_pi_id][$receive_basis][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_amt"]+=$row[csf("cons_amount")];
				$recv_rtn_data_arr[$wo_pi_id][$receive_basis][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]["rcv_amt"]+=$row[csf("cons_quantity")]*$rcv_item_rate[$row[csf("received_id")]][$row[csf("prod_id")]];
			}
		}
		
		//echo "<pre>";print_r($recv_data_arr[14120][1][15][485]);die;
		
		$btb_data_arr=array();
		foreach($btb_result as $row)
		{
			$btb_val=$rcv_btb_qnty=$rcv_btb_value=$rtn_btb_qnty=$rtn_btb_value=$bal_rcv_value="";
			//echo $row[csf("pi_id")]."-".$row[csf("count_name")]."-".$row[csf("yarn_composition_item1")]."-".$row[csf("yarn_type")]."-".$row[csf("color_id")]."<br>";
			$btb_val=$btb_val_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_item_value"];
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
			
			/*if($row[csf("count_name")]==15 && $row[csf("yarn_composition_item1")]==485 && $row[csf("yarn_type")]==39)
			{
				echo $rcv_btb_qnty."=".$receive_basis;die;
			}*/
			//echo "<pre>"; print_r($pi_id_arr);echo "======".$row[csf("btb_id")]."======".$rcv_btb_value;die;
			$bal_rcv_value=="";
			//$rtn_btb_value=$rtn_btb_value/$currency_rate;
			$bal_rcv_value=$rcv_btb_value-$rtn_btb_value; 
			
			
			$data_appear=0;
			if($cbo_receiving_status==1) 
			{
				if($bal_rcv_value=="" || $bal_rcv_value==0)
				{
					$data_appear=1;
				}
			}
			else if($cbo_receiving_status==2 ) 
			{
				if($btb_val>$bal_rcv_value && $bal_rcv_value>0 )
				{
					$data_appear=1;
				}
			}
			else if($cbo_receiving_status==3) 
			{
				if($btb_val<=$bal_rcv_value  && $bal_rcv_value>0)
				{
					$data_appear=1;
				}
			}
			else if($cbo_receiving_status==4) 
			{
				if(($btb_val>$bal_rcv_value && $bal_rcv_value>0) || $bal_rcv_value=="")
				{
					$data_appear=1;
				}
			}
			else 
			{
				$data_appear=1;
			}
			
			//echo "$btb_val , $cbo_receiving_status , $bal_rcv_value, $data_appear, $test_data "."<br>";
			if($data_appear)
			{
				$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["book_rcv_ids"]=$book_rcv_ids;
				$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["receive_basis"]=$receive_basis;
				$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["goods_rcv_status"]=$row[csf("goods_rcv_status")];
				$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["wo_pi_id"]=$wo_pi_id;
				$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_id"]=$row[csf("pi_id")];
				$btb_data_arr[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]]["pi_number"]=$row[csf("pi_number")];
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
		
		//echo "<pre>". count($btb_data_arr);die;
		//echo "<pre>";print_r($btb_data_arr); die;
		//die;
		//echo $btb_sql;die;
		
		ob_start();
		?>
		<div style="width:2020px; margin-left:10px;" align="left">
			<table width="2000" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
				<tr>
				   <td align="center" width="100%" colspan="24" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
				</tr>
			</table>
			<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="70">LC Date</th>
                        <th width="70">PI Date</th>
						<th width="130">Supplier</th>
						<th width="100">PI NO</th>
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
						<th width="80">Received Value</th>
						<th width="80">Balance Value</th>
						<th>Remarks</th>
					</tr>
				</thead>
			</table>
			<div style="width:2020px; overflow-y:scroll; max-height:350px; overflow-x:hidden;" id="scroll_body" align="left">
			<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">   
            <tbody>
            <?
            $i=1;
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
                                
                                $pi_value=$pi_num=$pi_date=$pi_supplier=$lc_num=$lc_data=$lc_ship_date=$lc_source=$lc_file_ref=$lc_receive_com="";
                                if($pi_check[$pi_id]== "")
                                {
                                    $pi_check[$pi_id]=$pi_id;
                                    $pi_value=$val["pi_value"];
                                    $pi_num=$val["pi_number"];
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
                                }
                                
                                ?>
                                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                    <td width="30" align="center" title="<? echo $val["rcv_btb_value"] ?>"><? echo $i; ?></td>
                                    <td width="70" align="center"><p><? if($lc_data!="" && $lc_data!="0000-00-00") echo change_date_format($lc_data); ?>&nbsp;</p></td>
                                    <td width="70" align="center"><p><? if($pi_date!="" && $pi_date!="0000-00-00") echo change_date_format($pi_date); ?>&nbsp;</p></td>
                                    <td width="130"><p><? echo $supplier_arr[$pi_supplier]; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $pi_num; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $lc_num; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><? echo number_format($pi_value,2);?></td>
                                    <td width="70" align="center"><p><? if($lc_ship_date!="" && $lc_ship_date!="0000-00-00") echo change_date_format($lc_ship_date); ?>&nbsp;</p></td>
                                    <td width="70"><p><? echo $seource_des_array[$lc_source]; ?>&nbsp;</p></td>
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
                                    <td width="70" align="right" title="<? echo "pi qnty=".$val["pi_item_qtny"]." rcv qnty=".$val["rcv_btb_qnty"]." rcv rtn qnty=".$val["rtn_btb_qnty"]?>"><? $balance_qnty=0; $balance_qnty=($val["pi_item_qtny"]-($val["rcv_btb_qnty"]-$val["rtn_btb_qnty"])); echo number_format($balance_qnty,2);  ?></td>
                                    <td width="80" align="right" title="<? echo "rcv value=".$val["rcv_btb_value"]." rcv rtn value=".$val["rtn_btb_value"] ?>" ><? $rcv_value=0; $rcv_value=($val["rcv_btb_value"]);  echo number_format($rcv_value,2); //-$val["rtn_btb_value"] ?></td> 
                                    <td width="80" align="right" title="<? echo "pi value=".$val["pi_item_value"]." rcv value=".$rcv_value." rcv rtn value=".$val["rtn_btb_value"]; ?>"><? $balance_value=0; $balance_value=($val["pi_item_value"]-($rcv_value-$val["rtn_btb_value"])); echo number_format($balance_value,2); ?></td> 
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
                                $total_rcv_value+=$rcv_value;
                                $total_balance_value+=$balance_value;
                            }
                        }
                    }
                }
                
            }
            ?>
            </tbody>         	
			</table>
			</div>
			<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <tfoot>
                <tr>
                    <th width="30">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="130">&nbsp;</th>
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
                    <th width="80" id="value_total_rcv_value"><? echo number_format($total_rcv_value,2); ?></th>
                    <th width="80" id="value_total_balance_value"><? echo number_format($total_balance_value,2); ?></th>
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
				$sql="select c.id as pi_id, c.pi_number, c.pi_date, b.id as receive_id,b.recv_number, b.receive_date, b.challan_no,b.remarks, a.order_qnty,a.order_rate ,a.order_amount,a.prod_id,d.product_name_details,d.lot,d.yarn_count_id,d.yarn_comp_type1st,d.yarn_comp_percent1st,d.yarn_type,d.color 
				from inv_transaction a, inv_receive_master b, com_pi_master_details c, product_details_master d 
				where a.mst_id=b.id and b.booking_id=c.id and a.prod_id=d.id and b.entry_form=1 and b.receive_basis=$rcv_basis and  b.booking_id in($pi_id) and b.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
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
					$yarn_type_id=chop($prod_data_all[$row[csf("pi_id")]]["yarn_type"],",");
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td><p><? echo $row[csf('pi_number')]; ?>&nbsp;</p></td>
						<td align="center"><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
						<td><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
						<td><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
						<td><p><? echo $row[csf('lot')]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]]; ?>&nbsp;</p></td>
						<td align="center"><p><? echo $composition[$row[csf("yarn_comp_type1st")]].' '.$row[csf("yarn_comp_percent1st")]; ?>&nbsp;</p></td>
						<td align="center" title="<?= $row[csf("yarn_type")];?>"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
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
	$company_ref=explode("_",str_replace("'","",$company_id));
	$company_id=$company_ref[0];
	$trans_pi_id=$company_ref[1];
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
				
				$rcv_exchange_rate=return_library_array("SELECT id, exchange_rate FROM inv_receive_master where id in($pi_id)","id","exchange_rate");
				
				$sql="select a.pi_id, a.issue_number, a.issue_date, b.prod_id, b.cons_quantity, b.rcv_rate as cons_rate, b.rcv_amount as cons_amount, a.remarks, a.received_id, 1 as type 
				from inv_issue_master a, inv_transaction b 
				where a.id=b.mst_id and a.entry_form=8 and a.received_id in($pi_id) and a.company_id=$company_id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				union all
				select a.pi_id,a.issue_number,a.issue_date,b.prod_id, b.cons_quantity, b.rcv_rate as cons_rate, b.rcv_amount as cons_amount, a.remarks, a.received_id, 2 as type 
				from inv_issue_master a, inv_transaction b 
				where a.id=b.mst_id and a.entry_form=8 and a.pi_id in($trans_pi_id) and a.received_id not in($pi_id) and a.company_id=$company_id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				
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
					if($db_type==0)
					{
						$conversion_date=date("Y-m-d",strtotime($row[csf("issue_date")]));
					}
					else
					{
						$conversion_date=date("d-M-y",strtotime($row[csf("issue_date")]));
					}
					$exchange_rate=set_conversion_rate( 2, $conversion_date );
					
					if($row[csf('type')]==1)
					{
						$rcv_amt=$row[csf('cons_amount')]/$rcv_exchange_rate[$row[csf('received_id')]];
					}
					else
					{
						$rcv_amt=$row[csf('cons_amount')]/$exchange_rate;
					}
					$rate=$rcv_amt/$row[csf('cons_quantity')];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center"><p><? if($row[csf('issue_date')]!="" && $row[csf('issue_date')]!="0000-00-00") echo change_date_format($row[csf('issue_date')]); ?>&nbsp;</p></td>
						<td><p><? echo $row[csf('issue_number')]; ?>&nbsp;</p></td>
						<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row[csf('cons_quantity')],2); $total_pi_qnty+=$row[csf('cons_quantity')];  ?></td>
                        <td align="right"><? echo number_format($rate,2);?></td>
                        <td align="right"><? echo number_format($rcv_amt,2); $total_pi_amt+=$rcv_amt;  ?></td>
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

if($action=="balance_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_ref=explode("_",str_replace("'","",$company_id));
	$company_id=$company_ref[0];
	$rcv_basis=$company_ref[1];
	//echo $company_id."=".$rcv_basis;die;
	$pi_id=str_replace("'","",$pi_id);
	?>
    <div id="report_container" align="center" style="width:1050px">
	<fieldset style="width:1050px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="1050" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                        <th width="100">PI No.</th>
                        <th width="70">PI Date</th>
                        <th width="150">Supplier</th>
                        <th width="60">Count</th>
                        <th width="180">Composition</th>
                        <th width="100">Type</th>
                        <th width="100">Color</th>
                        <th width="80">Qty</th>
                        <th width="70">Rate</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                <?
				
				$yarn_count_arr=return_library_array("SELECT id,yarn_count FROM lib_yarn_count","id","yarn_count");
				$color_name_arr=return_library_array("SELECT id,color_name FROM lib_color","id","color_name");
				 
				$receive_sql="select b.booking_id as pi_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_type, d.color, a.order_qnty, b.id as rcv_id
				from inv_transaction a, inv_receive_master b, product_details_master d 
				where a.mst_id=b.id and a.prod_id=d.id and b.entry_form=1 and b.receive_basis=$rcv_basis and b.booking_id in($pi_id) and b.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				$receive_result=sql_select($receive_sql);
				$receive_data=array();$all_rcv_id="";
				foreach($receive_result as $row)
				{
					$all_rcv_id.=$row[csf("rcv_id")].",";
					$receive_data[$row[csf("pi_id")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]+=$row[csf("order_qnty")];
				}
				
				$all_rcv_id=implode(",",array_unique(explode(",",chop($all_rcv_id,","))));
				if($all_rcv_id!="")
				{
					$rcv_rtn_sql="select a.pi_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color, b.cons_quantity
					from inv_issue_master a, inv_transaction b, product_details_master c
					where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and c.item_category_id=1 and a.entry_form=8 and a.received_id in($all_rcv_id) and a.company_id=$company_id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					union all
					select a.pi_id, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_type, c.color, b.cons_quantity
					from inv_issue_master a, inv_transaction b, product_details_master c
					where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and c.item_category_id=1 and a.entry_form=8 and a.pi_id in($pi_id) and a.received_id not in($all_rcv_id) and a.company_id=$company_id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
					$receive_rtn_result=sql_select($rcv_rtn_sql);
					$receive_rtn_data=array();
					foreach($receive_rtn_result as $row)
					{
						$receive_rtn_data[$row[csf("pi_id")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_type")]][$row[csf("color")]]+=$row[csf("cons_quantity")];
					}
				}
				
				$sql="select a.id as pi_id, a.pi_number, a.pi_date, a.supplier_id, b.count_name, b.yarn_composition_item1, b.yarn_type, b.color_id, sum(b.quantity) as quantity, sum(b.net_pi_amount) as net_pi_amount
				from com_pi_master_details a, com_pi_item_details b
				where a.id=b.pi_id and a.id in($pi_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				group by a.id, a.pi_number, a.pi_date, a.supplier_id, b.count_name, b.yarn_composition_item1, b.yarn_type, b.color_id";
				$total_pi_qnty=0; $total_pi_amt=0;
				//echo $sql;die;
				$result=sql_select($sql);$i=1;
				foreach($result as $row)  
				{
					
					$net_pi_rate=$row[csf("net_pi_amount")]/$row[csf('quantity')];
					$rcv_qnty=$receive_data[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]];
					$rcv_rtn_qnty=$receive_rtn_data[$row[csf("pi_id")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]][$row[csf("yarn_type")]][$row[csf("color_id")]];
					$bal_qnty=(($row[csf('quantity')]+$rcv_rtn_qnty)-$rcv_qnty);
					$bal_amt=$bal_qnty*$net_pi_rate;
					if($bal_qnty>0)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td><p><? echo $row[csf('pi_number')]; ?>&nbsp;</p></td>
                            <td align="center"><p><? if($row[csf('pi_date')]!="" && $row[csf('pi_date')]!="0000-00-00") echo change_date_format($row[csf('pi_date')]);?>&nbsp;</p></td>
                            <td><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo $yarn_count_arr[$row[csf('count_name')]]; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo $composition[$row[csf("yarn_composition_item1")]]; ?>&nbsp;</p></td>
                            <td align="center"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
                            <td><p><? echo $color_name_arr[$row[csf('color_id')]]; ?>&nbsp;</p></td>
                            <td align="right"><? echo number_format($bal_qnty,2); $total_bal_qnty+=$bal_qnty;  ?></td>
                            <td align="right"><? echo number_format($net_pi_rate,2);?></td>
                            <td align="right"><? echo number_format($bal_amt,2); $total_bal_amt+=$bal_amt;  ?></td>
                        </tr>
                        <?
                        $i++;
					}
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
                        <th>Total :</th>
                        <th align="right"><? echo number_format($total_bal_qnty,2) ; ?></th>
                        <th>&nbsp;</th>
                        <th align="right"><? echo number_format($total_bal_amt,2) ; ?></th>
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
    <div id="report_container" align="center" style="width:930px">
	<fieldset style="width:930px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="920" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                        <th width="150">PI No.</th>
                        <th width="100">PI Date</th>
                        <th width="120">PI Value</th>
                        <th width="120">Receive Value</th>
                        <th width="120">Return Value</th>
                        <th width="120">Payable Value</th>
                        <th>Balance Value</th>
                    </tr>
                </thead>
                <tbody>
                <?
				
				
				$sql="select c.pi_id, c.id as pi_dtls_id, a.id as trans_id, a.order_amount as order_amount, c.net_pi_amount as net_pi_amount, a.mst_id, a.prod_id, a.order_rate
				from inv_transaction a, com_pi_item_details c
				where a.pi_wo_batch_no=c.pi_id and a.item_category in(1) and a.transaction_type=1 and a.receive_basis=1 and a.pi_wo_batch_no in($pi_id) and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0";
				//echo $sql;//die;
				$total_pi_qnty=0; $total_pi_amt=0;
				//echo $sql;//die;
				$result=sql_select($sql);
				$dtls_data=array();
				foreach($result as $row)  
				{
					if($trans_check[$row[csf("trans_id")]]=="")
					{
						$trans_check[$row[csf("trans_id")]]=$row[csf("trans_id")];
						$dtls_data[$row[csf("pi_id")]]["order_amount"]+=$row[csf("order_amount")];
					}
					if($pi_dtls_check[$row[csf("pi_dtls_id")]]=="")
					{
						$pi_dtls_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
						$dtls_data[$row[csf("pi_id")]]["net_pi_amount"]+=$row[csf("net_pi_amount")];
					}
					$dtls_data[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
					$rcv_wise_rate[$row[csf("mst_id")]][$row[csf("prod_id")]]=$row[csf("order_rate")];
					//$rcv_wise_rate
				}
				
				$sql_rtn=sql_select(" select a.pi_id, a.received_id, b.cons_quantity as cons_quantity, b.prod_id
				from inv_issue_master a, inv_transaction b 
				where a.id=b.mst_id and a.pi_id>0 and b.item_category in(1) and a.pi_id in($pi_id) and a.company_id=$company_id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				$return_data=array();
				foreach($sql_rtn as $row)
				{
					$return_data[$row[csf("pi_id")]]+=$row[csf("cons_quantity")]*$rcv_wise_rate[$row[csf("received_id")]][$row[csf("prod_id")]];
				}
				$pi_name_val=sql_select(" select a.id, a.pi_number, a.pi_date from com_pi_master_details a where a.status_active=1 and a.is_deleted=0 and a.id in($pi_id)");
				$pi_data=array();
				foreach($pi_name_val as $row)
				{
					$pi_data[$row[csf("id")]]["pi_number"]=$row[csf("pi_number")];
					$pi_data[$row[csf("id")]]["pi_date"]=$row[csf("pi_date")];
				}
				
				//echo "<pre>";print_r($rcv_dtls_data);die;
				$i=1;
				foreach($dtls_data as $row)  
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td><p><? echo $pi_data[$row[("pi_id")]]["pi_number"]; ?>&nbsp;</p></td>
						<td align="center"><p><? if($pi_data[$row[("pi_id")]]["pi_date"]!="" && $pi_data[$row[("pi_id")]]["pi_date"]!="0000-00-00") echo change_date_format($pi_data[$row[("pi_id")]]["pi_date"]);?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[('net_pi_amount')],2); $total_pi_amt+=$row[('net_pi_amount')];  ?></td>
                        <td align="right"><? echo number_format($row[('order_amount')],2); $total_rcv_amt+=$row[('order_amount')];  ?></td>
                        <td align="right"><? echo number_format($return_data[$row[("pi_id")]],2); $total_return_amt+=$return_data[$row[("pi_id")]];  ?></td>
                        <td align="right"><p><? $payable_amt=$row[('order_amount')]-$return_data[$row[("pi_id")]]; echo number_format($payable_amt,2); ?>&nbsp;</p></td>
                        <td align="right" title="<? echo "(net_pi_amount-payable_amt)".$row[('net_pi_amount')]."=".$payable_amt; ?>"><p><? $balance_amt=$row[('net_pi_amount')]-$payable_amt; echo number_format($balance_amt,2); ?>&nbsp;</p></td>
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
				
				$sql_receive="select b.booking_id, a.order_amount as order_amount, a.mst_id, a.prod_id, a.order_rate
				from inv_transaction a, inv_receive_master b
				where a.mst_id=b.id and a.item_category in(1) and a.transaction_type=1 and a.receive_basis=1 and b.receive_basis=1 and b.booking_id in($pi_id) and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0";
				$sql_rcv_result=sql_select($sql_receive);
				foreach($sql_rcv_result as $row)
				{
					$rcv_data[$row[csf("booking_id")]]["order_amount"]+=$row[csf("order_amount")];
					$rcv_wise_rate[$row[csf("mst_id")]][$row[csf("prod_id")]]=$row[csf("order_rate")];
					
				}
				$sql_rtn="select a.pi_id, a.received_id, b.cons_quantity as cons_quantity, b.prod_id
				from inv_issue_master a, inv_transaction b 
				where a.id=b.mst_id and a.pi_id>0 and b.item_category in(1) and a.pi_id in($pi_id) and a.company_id=$company_id and b.transaction_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				//echo $sql_rtn;die;
				$sql_rtn_result=sql_select($sql_rtn);
				$return_data=array();
				foreach($sql_rtn_result as $row)
				{
					$return_data[$row[csf("pi_id")]]=$row[csf("cons_quantity")]*$rcv_wise_rate[$row[csf("received_id")]][$row[csf("prod_id")]];
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
				$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "con_date=(select max(con_date) as con_date   from currency_conversion_rate)" , "conversion_rate" );
				$sql="select a.id as mrr_id, a.issue_number as recv_number, a.issue_date as receive_date, a.remarks, sum(b.cons_quantity) as qnty, sum(b.rcv_amount) as amt
					from inv_issue_master a, inv_transaction b, product_details_master c 
					where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=8 and b.item_category=1 and b.transaction_type=3 and a.company_id=$company_id and a.received_id in($receive_ids) and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition_id and c.yarn_type=$type_id and c.color=$color_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by a.id, a.issue_number, a.issue_date, a.remarks";
				//echo $sql;die;	
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
                        <td align="right"><? echo number_format($rate,2);?></td>
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
	where a.id=b.pi_id and a.id in($pi_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
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
