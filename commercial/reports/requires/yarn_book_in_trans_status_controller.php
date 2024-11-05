<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
if($db_type==0) $select_concat="group";
if($db_type==2) $select_concat="wm";



// if ($action=="load_drop_down_supplier")
// {    	 
// 	echo create_drop_down( "cbo_supplier", 100, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
// 	// echo create_drop_down( "cbo_supplier", 100, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "load_drop_down( 'requires/purchase_recap_report_controller2', this.value, 'load_drop_down_category', 'category_td' );",0 );
// }

if ($action == "load_drop_down_supplier")
{
	if($data){$companyCon=" and a.tag_company='$data'";}
	else{$companyCon="";}
	echo create_drop_down("cbo_supplier", 100, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $companyCon and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "eval_multi_select")
{
	echo "set_multiselect('cbo_supplier','0','0','','0');\n";
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$item_category= 1;
	$year_cond = ""; $month_cond = "";
	$company_cond = "";
	$company_cond2 = "";
	$company_cond3 = "";

	if(str_replace("'","",$cbo_company_id) != 0)
	{
		$company_cond = "and c.company_name=$cbo_company_id";
		$company_cond2 = "and a.company_id = $cbo_company_id";
		$company_cond3 = " and b.importer_id = $cbo_company_id";
		$company_cond4 = " and D.importer_id = $cbo_company_id";
	}
	if(str_replace("'","",$cbo_year) != 0)
	{
		if($db_type==0) $year_cond=" and year(c.wo_date) = $cbo_year";
		if($db_type==2) $year_cond=" and to_char(c.wo_date,'YYYY') = $cbo_year";
	}
	if(str_replace("'","",$cbo_month) != 0)
	{
		if($db_type==0) $month_cond=" and month(c.wo_date) = $cbo_month";
		if($db_type==2) $month_cond=" and to_char(c.wo_date,'MM') = $cbo_month";
	}
	if(str_replace("'","",$cbo_supplier)!="" && str_replace("'","",$cbo_supplier)!=0)
	{
		$cbo_supplier = str_replace("'","",$cbo_supplier);
		$supplier_cond=" and c.SUPPLIER_ID in ($cbo_supplier)";
		$supplier_cond2 = " and b.SUPPLIER_ID in ($cbo_supplier)";
		$supplier_cond3 = " and a.SUPPLIER_ID in ($cbo_supplier)";
	} 
	if(str_replace("'","",$cbo_yarn_count)!="" && str_replace("'","",$cbo_yarn_count)!=0)
	{
		$cbo_yarn_count = str_replace("'","",$cbo_yarn_count);
		$count_cond=" and d.yarn_count in ($cbo_yarn_count)";
		$count_cond3=" and c.YARN_COUNT_ID in ($cbo_yarn_count)";
	} 
	if(str_replace("'","",$cbo_yarn_type)!="" && str_replace("'","",$cbo_yarn_type)!=0)
	{
		$cbo_yarn_type = str_replace("'","",$cbo_yarn_type);
		$type_cond=" and d.yarn_type in ($cbo_yarn_type)";
		$type_cond3=" and c.yarn_type in ($cbo_yarn_type)";
	} 
	if(str_replace("'","",$cbo_composition)!="" && str_replace("'","",$cbo_composition)!=0)
	{
		$cbo_composition = str_replace("'","",$cbo_composition);
		$composition_cond=" and d.YARN_COMP_TYPE1ST in ($cbo_composition)";
		$composition_cond3=" and c.YARN_COMP_TYPE1ST in ($cbo_composition)";
	} 

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$wo_date_cond="";
	$invoice_date_cond="";
	// if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and c.wo_date between ".$txt_date_from." and ".$txt_date_to."";
	if(str_replace("'","",$cbo_date_type)==2)
	{
		if( $date_from==0 && $date_to==0 ) $invoice_date_cond=""; else $invoice_date_cond= " and a.invoice_date between ".$txt_date_from." and ".$txt_date_to."";
		if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and c.wo_date between ".$txt_date_from." and ".$txt_date_to."";

	}
	else if(str_replace("'","",$cbo_date_type)==1)
	{
		if( $date_from==0 && $date_to==0 ) $wo_date_cond=""; else $wo_date_cond= " and c.wo_date between ".$txt_date_from." and ".$txt_date_to."";
	}

	$company_arr=return_library_array( "select id, company_name from lib_company","id","company_name");
	$yarn_count_arr=return_library_array("select id,yarn_count from lib_yarn_count",'id','yarn_count');
	// $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier","id","supplier_name");
	$color_arr=return_library_array( "select id, color_name from lib_color","id","color_name");

	$tbl_width=1110;
	$colspan=18;
	
	ob_start();
	?>
    <fieldset style="width:<? echo $tbl_width+20; ?>px;">
        <table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="<? echo $colspan; ?>" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="<? echo $colspan; ?>" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
            </tr>
            <tr  class="form_caption" style="border:none;">
               <td align="center" width="100%" colspan="<? echo $colspan; ?>" style="font-size:14px"><strong> <? if($date_from!="") echo "Date From : ".change_date_format(str_replace("'","",$txt_date_from)).' To '.change_date_format(str_replace("'","",$txt_date_to)) ;?></strong></td>
            </tr>
        </table>
        <table width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="90">WO No</th>
                    <th width="90">WO Date</th>
                    <th width="90">Supplier</th>
                    <th width="90">Count</th>
                    <th width="90">Composition</th>
                    <th width="90">Type</th>
                    <th width="90">Color</th>
                    <th width="90">Work Order Qty</th>
                    <th width="90">Invoice Package Qty</th>
                    <th width="90">Total Rcv Qty</th>
                    <th width="90">Booked Qty</th>
                    <th width="90">In Transit Qty.</th>
                </tr>
            </thead>
        </table> 
        <div style="width:<? echo $tbl_width+19; ?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
		<?	
			if ($wo_basis==3)
			{
				if($db_type==0) $select_groby="group by a.job_no_prefix_num, b.po_number, c.wo_number_prefix_num, d.id order by a.buyer_name, a.job_no_prefix_num, b.po_number, c.wo_number_prefix_num";
				else $select_groby=" group by a.job_no_prefix_num, b.po_number, c.wo_number_prefix_num, d.id,a.id, a.company_name,a.buyer_name, a.style_ref_no, b.id,a.job_no,c.wo_number, c.wo_date, c.supplier_id, c.delivery_date, d.po_breakdown_id, d.item_id, d.yarn_count, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.color_name, d.supplier_order_quantity, d.rate, d.amount, d.remarks order by a.buyer_name, a.job_no_prefix_num, b.po_number, c.wo_number_prefix_num";
				
				$sql_query="Select a.id, a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, $select_concat"."_concat(distinct(b.po_number)) as po_number,
				c.wo_number_prefix_num, c.wo_number, c.wo_date, c.supplier_id, c.delivery_date, d.po_breakdown_id, d.item_id, d.yarn_count, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.color_name, d.supplier_order_quantity, d.rate, d.amount, d.remarks
				from  wo_po_details_master a, wo_po_break_down b, wo_non_order_info_mst c, wo_non_order_info_dtls d 
				where a.job_no=b.job_no_mst and b.job_no_mst=c.wo_number and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				and c.id=d.mst_id and d.item_category_id='$item_category' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
				$company_cond $year_cond $month_cond $wo_basis_cond $wo_date_cond $buyer_id_cond $job_no_cond $order_id_cond $wo_cond $select_groby";
				//echo $sql_query;//die;
			}
			elseif($wo_basis==1)
			{
				$new_sql_cond="";
				if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0)
				{
					$new_sql_cond.=" and e.buyer_id=$cbo_buyer_id";
				}
				if($job_no !="")
				{
					$new_sql_cond.=" and e.job_no like '%$job_no%'";
				}
						
				if($db_type==0) $select_grpby="group by c.wo_number_prefix_num, d.id order by c.wo_number_prefix_num";
				if($db_type==2) $select_grpby="group by c.wo_number_prefix_num, d.id,c.id, c.wo_number_prefix_num, c.wo_number, c.wo_date, c.supplier_id, c.delivery_date, d.po_breakdown_id, d.item_id, d.yarn_count, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.color_name, d.supplier_order_quantity, d.rate, d.amount, d.remarks, d.requisition_dtls_id order by c.wo_number_prefix_num";
				else $select_grpby="";
				
				$sql_query="SELECT c.id, c.wo_number_prefix_num, c.wo_number, c.wo_date, c.supplier_id, c.delivery_date, d.po_breakdown_id, d.item_id, d.yarn_count, d.yarn_comp_type1st, d.requisition_dtls_id, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.color_name, d.supplier_order_quantity, d.rate, d.amount, d.remarks 
					from wo_non_order_info_mst c, wo_non_order_info_dtls d, inv_purchase_requisition_dtls e
					where c.id=d.mst_id and d.item_category_id='$item_category' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
					and e.id=d.requisition_dtls_id  and e.status_active=1 and e.is_deleted=0
					$company_cond $new_sql_cond $wo_basis_cond $year_cond $month_cond $wo_date_cond $wo_cond $select_grpby";

				//-------------------  Dtls for job data start-----------------------------
				$requisition_dtls_id_arr=array();
				$result=sql_select($sql_query);
				foreach ($result as $value) 
				{
					$requisition_dtls_id_arr[]=$value[csf('requisition_dtls_id')];
				}
				$req_dtls_ids=implode(",", array_unique($requisition_dtls_id_arr));
				$job_dtls_data_arr=sql_select("select c.id as requisition_dtls_id, a.job_no_prefix_num, b.po_number, a.buyer_name, a.style_ref_no  from wo_po_details_master a, wo_po_break_down b, inv_purchase_requisition_dtls c where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and (c.job_id=a.id or a.job_no=c.job_no) and c.id in ($req_dtls_ids) and c.status_active=1 and c.is_deleted=0");

				$job_data_info_arr=array();
				foreach ($job_dtls_data_arr as $rows) 
				{
					$job_data_info_arr[$rows[csf('requisition_dtls_id')]]['job_no_prefix_num']=$rows[csf('job_no_prefix_num')];
					//$job_data_info_arr[$rows[csf('requisition_dtls_id')]]['po_number']=$rows[csf('po_number')];
					$job_data_info_arr[$rows[csf('requisition_dtls_id')]]['buyer_name']=$rows[csf('buyer_name')];
					$job_data_info_arr[$rows[csf('requisition_dtls_id')]]['style_ref_no']=$rows[csf('style_ref_no')];
				}
				//----------------------end---------------------------------------------------------------------
			
			}
			else
			{
				// print_r($wo_basis);
				if($db_type==0) $select_grpby="group by c.wo_number_prefix_num, d.id order by c.wo_number_prefix_num";
				if($db_type==2) $select_grpby="group by  d.id,c.id,  c.wo_number, c.wo_date, c.supplier_id, c.delivery_date, d.yarn_count, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.color_name, d.supplier_order_quantity,  d.YARN_TYPE, d.id order by c.wo_number";
				else $select_grpby="";
				
				$sql_query="Select c.id, d.id as dtls_id,  c.wo_number, c.wo_date, c.supplier_id, c.delivery_date,  d.yarn_count, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.color_name, d.supplier_order_quantity,  d.YARN_TYPE from wo_non_order_info_mst c, wo_non_order_info_dtls d where c.id=d.mst_id and d.item_category_id='$item_category' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond $wo_basis_cond $year_cond $month_cond $supplier_cond $count_cond $type_cond $composition_cond $wo_date_cond $select_grpby";
			}
			// echo $sql_query;
			
			$i=1; 
			$nameArray=sql_select( $sql_query );  
			$invoice_pack_data = array();

			// invoice package qty 

			$sql_invoice = sql_select("SELECT a.invoice_no, c.COUNT_NAME,  c.WORK_ORDER_NO, a.PKG_QUANTITY_BREAKDOWN 
			FROM COM_IMPORT_INVOICE_MST a 
				JOIN COM_IMPORT_INVOICE_DTLS b ON B.IMPORT_INVOICE_ID = A.ID
				JOIN COM_PI_ITEM_DETAILS c ON c.pi_id = b.pi_id
				JOIN COM_PI_MASTER_DETAILS D ON D.ID = C.pi_id
			where  
				a.status_active = 1 and b.status_active = 1 and c.status_active = 1 and  d.status_active = 1
				and a.is_deleted = 0 and b.is_deleted = 0 and c.is_deleted = 0 and  d.is_deleted = 0
				$company_cond4 $supplier_cond2
			group by 
				a.invoice_no,
    			c.COUNT_NAME,
                c.WORK_ORDER_NO,
                a.PKG_QUANTITY_BREAKDOWN");
			//echo $sql_invoice; exit();
			$pkg_data=array();
			$breakdown_arr = array();
			foreach($sql_invoice as $row)
			{
				$pkg_quantity_breakdown = $row['PKG_QUANTITY_BREAKDOWN'];
				$pkg_quantity_breakdown_arr=explode("__",$pkg_quantity_breakdown);
				
				foreach($pkg_quantity_breakdown_arr as $pkz_val)
				{
					$pkz_val_arr=explode("_",$pkz_val);
					$pkg_data[$pkz_val_arr[0]][$pkz_val_arr[1]][$row['COUNT_NAME']][$row['WORK_ORDER_NO']]+=$pkz_val_arr[2];
					$breakdown_arr[$pkz_val_arr[0]][$pkz_val_arr[1]]['BRKDWN'] = $pkg_quantity_breakdown;
				}
			}
			// echo "<pre>";
			// print_r($breakdown_arr);

			$sql_dtls = sql_select("select b.PI_ID, b.ID ,a.id as dtls_id from wo_non_order_info_dtls a, COM_PI_ITEM_DETAILS b where b.WORK_ORDER_DTLS_ID = a.id and a.status_active = 1 and b.status_active = 1");
			$pi_dtls = array();
			foreach($sql_dtls as $row)
			{
				$pi_dtls[$row['DTLS_ID']]['PI_ID'] = $row['PI_ID'];
				$pi_dtls[$row['DTLS_ID']]['ID'] = $row['ID'];
			}
			// invoice package end 

			//receive qunatity 
			$sql_receive = sql_select("select a.booking_no as wo_number, c.YARN_COUNT_ID, c.YARN_COMP_TYPE1ST, c.YARN_COMP_PERCENT1ST, c.YARN_TYPE, c.COLOR, sum(b.CONS_QUANTITY) as receive_qnty from INV_RECEIVE_MASTER a, INV_TRANSACTION  b, PRODUCT_DETAILS_MASTER c where a.id = b.mst_id and b.prod_id = c.id and a.receive_basis = 2 $supplier_cond3 $count_cond3 $type_cond3 $composition_cond3 $company_cond2 group by a.booking_no, c.YARN_COUNT_ID, c.YARN_COMP_TYPE1ST, c.YARN_COMP_PERCENT1ST, c.YARN_TYPE, c.COLOR");
			 
			$receive_arr = array();
			foreach($sql_receive as $row)
			{
				$receive_arr[$row['WO_NUMBER']][$row['YARN_COUNT_ID']][$row['YARN_COMP_TYPE1ST']][$row['YARN_COMP_PERCENT1ST']][$row['YARN_TYPE']][$row['COLOR']]['RECEIVE_QNTY'] = $row['RECEIVE_QNTY'];
			}
			// echo "<pre>";
			// print_r($receive_arr);

			//receive qunatity end
			
			$total_invoice = 0;
			$receive_qnty = 0;
			$total_booked_qty = 0;
			$total_in_transit_qty = 0;
			foreach ($nameArray as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";				
				$yarn_comp_type1st=$row[csf("yarn_comp_type1st")];
				$yarn_comp_percent1st=$row[csf("yarn_comp_percent1st")];
				$yarn_comp_type2nd=$row[csf("yarn_comp_type2nd")];
				$yarn_comp_percent2nd=$row[csf("yarn_comp_percent2nd")];
				
				$yarndtls='';
				if ($yarn_comp_type1st!=0 && $yarn_comp_percent1st!='' && $yarn_comp_type2nd!=0 && $yarn_comp_percent2nd!='')
				{
					$yarndtls=$composition[$yarn_comp_type1st].'  '.$yarn_comp_percent1st.' %, '.$composition[$yarn_comp_type2nd].' '.$yarn_comp_percent2nd.' %'; 
				}
				else if($yarn_comp_type1st!=0 && $yarn_comp_percent1st!='' && $yarn_comp_type2nd!=0)
				{
					$yarndtls=$composition[$yarn_comp_type1st].' '.$yarn_comp_percent1st.' %, '.$composition[$yarn_comp_type2nd]; 
				}
				else if($yarn_comp_type1st!=0 && $yarn_comp_percent1st!='' )
				{
					$yarndtls=$composition[$yarn_comp_type1st].' '.$yarn_comp_percent1st.' %'; 
				}
				else if($yarn_comp_type1st!=0)
				{
					$yarndtls=$composition[$yarn_comp_type1st]; 
				}
				else
				{
					$yarndtls=''; 
				}		
						
				?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                    <td width="30"><? echo $i; ?></td>	
                    <td width="90" align="center"><p><? echo $row[csf("wo_number")]; ?></p></td>
                    <td width="90" align="center"><p><? echo change_date_format($row[csf("wo_date")]); ?></p></td>
                    <td width="90" align="center"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
					<td width="90" align="center"><p><? echo $yarn_count_arr[$row[csf("yarn_count")]]; ?></p></td>
					<td width="90" align="center"><p><? echo $yarndtls; ?></p></td>
                    <td width="90" align="center"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?></p></td>
					<td width="90" align="center"><p><? echo $color_arr[$row[csf("color_name")]]; ?></p></td>
					<td width="90" align="right"><p><? echo number_format($row[csf("supplier_order_quantity")],2,'.','');   ?>&nbsp;</p></td>			
                    <td width="90" align="center"><p><a onclick="invoice_popup('<?= $pi_dtls[$row['DTLS_ID']]['PI_ID'];?>', '<?= $pi_dtls[$row['DTLS_ID']]['ID'];?>', '<?= $row[csf('wo_number')]?>');" href="##">
					<? 
						$invoice_qnty =  $pkg_data[$pi_dtls[$row['DTLS_ID']]['PI_ID']][$pi_dtls[$row['DTLS_ID']]['ID']][$row[csf("yarn_count")]][$row[csf("wo_number")]];
						if($invoice_qnty == '') $invoice_qnty = 0;
						echo number_format($invoice_qnty,2,'.','');
						$total_invoice += $invoice_qnty;
					?></a></p></td>       
					<td width="90" align="center"><p><a onclick="mrr_popup('<?= $row['YARN_COUNT'];?>', '<?= $row[csf('yarn_comp_type1st')];?>','<?= $row[csf('yarn_comp_percent1st')];?>','<?= $row[csf('yarn_type')];?>','<?= $row[csf('color_name')];?>', '<?= $row[csf('wo_number')]?>');" href="##">
					<?
						$receive_qnty =  $receive_arr[$row[csf("wo_number")]][$row[csf("yarn_count")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_comp_percent1st")]][$row[csf("yarn_type")]][$row[csf("color_name")]]['RECEIVE_QNTY']; 
						if($receive_qnty == '') $receive_qnty = 0;
						echo number_format($receive_qnty,2,'.','');
						$total_receive += $receive_qnty;
					 ?></p></td>
                    <td width="90" align="right"><p>
					<?
						$booked_qty = $row[csf("supplier_order_quantity")] - $invoice_qnty;
						$total_booked_qty += $booked_qty;
						echo number_format($booked_qty,2,'.',''); 
					?></p></td>               
                    <td width="90" align="right"><p>
					<? 
						$in_transit_qty = $invoice_qnty - $receive_qnty;
						$total_in_transit_qty+=$in_transit_qty;
						echo number_format($in_transit_qty,2,'.','');
					?></p></td>
                </tr>
			<?
			$total_qty+=$row[csf("supplier_order_quantity")];
            $i++;
		}
		?>
            </table>
            <table width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" > 
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="90" align="right"><? echo number_format($total_qty,2,'.',''); ?></th>
                    <th width="90" align="right"><? echo number_format($total_invoice,2,'.',''); ?></th>                   
                    <th width="90" align="right"><? echo number_format($total_receive,2,'.',''); ?></th>
                    <th width="90" align="right"><? echo number_format($total_booked_qty,2,'.',''); ?></th>
                    <th width="90" align="right"><? echo number_format($total_in_transit_qty,2,'.',''); ?></th>
                </tfoot>
            </table> 
        </div>
    </fieldset>
    <?

	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("$user_id*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html****$filename"; 
	
}
if ($action == "invoice_popup") 
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	//$started = microtime(true);
	extract($_REQUEST);
	

	$sql_invoice = sql_select("SELECT distinct c.pi_id, c.id, a.INVOICE_NO, a.INVOICE_DATE, b.LC_NUMBER, a.PKG_QUANTITY_BREAKDOWN, d.pi_number FROM COM_IMPORT_INVOICE_MST a JOIN COM_BTB_LC_MASTER_DETAILS b ON TO_CHAR(a.BTB_LC_ID) = TO_CHAR(b.ID)  JOIN COM_PI_ITEM_DETAILS c ON TO_CHAR(b.pi_id) in (TO_CHAR(c.pi_id)) join COM_PI_MASTER_DETAILS d on c.pi_id = d.id where c.work_order_no = '$wo_number' and a.status_active = 1 and b.status_active = 1 and c.status_active = 1 and c.id = $dtls_id and c.pi_id in ($all_pi_ids)");
	$data_arr = array();
	$pkg_data=array();
	$breakdown_arr = array();
	foreach($sql_invoice as $row)
	{
		$data_arr[$row['PI_ID']][$row['INVOICE_NO']]['PI_ID'] = $row['PI_ID'];
		$data_arr[$row['PI_ID']][$row['INVOICE_NO']]['LC_NUMBER'] = $row['LC_NUMBER'];
		$data_arr[$row['PI_ID']][$row['INVOICE_NO']]['INVOICE_NO'] = $row['INVOICE_NO'];
		$data_arr[$row['PI_ID']][$row['INVOICE_NO']]['INVOICE_DATE'] = $row['INVOICE_DATE'];
		$data_arr[$row['PI_ID']][$row['INVOICE_NO']]['PI_NUMBER'] = $row['PI_NUMBER'];

		$pkg_quantity_breakdown = $row['PKG_QUANTITY_BREAKDOWN'];
		$pkg_quantity_breakdown_arr=explode("__",$pkg_quantity_breakdown);
		foreach($pkg_quantity_breakdown_arr as $pkz_val)
		{
			$pkz_val_arr=explode("_",$pkz_val);
			if($pkz_val_arr[0] == $row['PI_ID'] && $pkz_val_arr[1] == $dtls_id)
			{
				$data_arr[$row['PI_ID']][$row['INVOICE_NO']]['PACK_QNTY'] = $pkz_val_arr[2];
			}
		}
		
	}

	?>
	<div align="center" style="width:600px;">
           <table cellpadding="0" cellspacing="0" width="600" class="rpt_table" border="1" rules="all" id="pkz_table">
                <thead>
                    <th width="50">SL</th>
                    <th width="110">PI No</th>
                    <th width="110">LC No</th>
                    <th width="110">Invoice No</th>
                    <th width="110">Invoice Date</th>
                    <th width="110">Package Qty</th>
                </thead>
                <tbody>
                	<?
					$i = 1;
					foreach($data_arr as $val)
					{
						foreach($val as $row)
						{
							if($row['PACK_QNTY'] != '' && $row['PACK_QNTY'] > 0)
							{
								if ($i%2==0) $bgcolor="#E9F3FF";
								else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>" style="text-decoration:none; cursor:pointer">
									<td align="center"><?= $i; ?></td>	
									<td align="center"><p><? echo $row['PI_NUMBER']; ?></p></td>
									<td align="center"><p><? echo $row['LC_NUMBER']; ?>&nbsp;</p></td>
									<td align="center"><p><? echo $row['INVOICE_NO']; ?>&nbsp;</p></td>
									<td align="center"><p><? echo $row['INVOICE_DATE']; ?></p></td>	
									<td align="right"><? echo $row['PACK_QNTY']; $total_pack_qnty += $row['PACK_QNTY'];?></td>
								</tr>
								<?
								$i++;
							}
						}
					}
					?>
                </tbody>
                <tfoot>
                	<th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="center">Total:</th>
                    <th align="right"><?echo $total_pack_qnty; ?></th>
                </tfoot>
            </table>
        </div>
	<?
	
}

if ($action == "mrr_popup") 
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	//$started = microtime(true);
	extract($_REQUEST);
	
	$sql_receive = sql_select("SELECT a.RECV_NUMBER, a.RECEIVE_DATE, a.booking_no, a.CHALLAN_NO, b.CONS_QUANTITY     AS receive_qnty FROM INV_RECEIVE_MASTER a, INV_TRANSACTION b, PRODUCT_DETAILS_MASTER c WHERE a.id = b.mst_id AND b.prod_id = c.id AND a.receive_basis = 2 and a.booking_no = '$wo_number' and c.YARN_COUNT_ID = $count and c.YARN_COMP_TYPE1ST = $comp and c.YARN_COMP_PERCENT1ST = $percent and c.YARN_TYPE = $type and c.COLOR = $color");
	// echo "<pre>";
	// print_r($sql_receive);

	?>
	<div align="center" style="width:600px;">
           <table cellpadding="0" cellspacing="0" width="600" class="rpt_table" border="1" rules="all" id="pkz_table">
                <thead>
                    <th width="50">SL</th>
                    <th width="110">MRR No</th>
                    <th width="110">RCV Date</th>
                    <th width="110">WO/PI/FSO</th>
                    <th width="110">Challan No</th>
                    <th width="110">RCV Qty</th>
                </thead>
                <tbody>
                	<?
					$i = 1;
					foreach($sql_receive as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>" style="text-decoration:none; cursor:pointer">
							<td align="center"><?= $i; ?></td>	
							<td align="center"><p><? echo $row['RECV_NUMBER']; ?></p></td>
							<td align="center"><p><? echo $row['RECEIVE_DATE']; ?>&nbsp;</p></td>
							<td align="center"><p><? echo $row['BOOKING_NO']; ?>&nbsp;</p></td>
							<td align="center"><p><? echo $row['CHALLAN_NO']; ?></p></td>	
							<td align="right"><? echo $row['RECEIVE_QNTY']; $total_rcv_qnty += $row['RECEIVE_QNTY'];?></td>
						</tr>
						<?
						$i++;
					}
					?>
                </tbody>
                <tfoot>
                	<th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="center">Total:</th>
                    <th align="right"><?echo $total_rcv_qnty; ?></th>
                </tfoot>
            </table>
        </div>
	<?
	
}

disconnect($con);
?>
