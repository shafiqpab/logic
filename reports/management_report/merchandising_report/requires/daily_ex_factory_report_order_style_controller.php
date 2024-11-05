<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
//$job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master",'job_no','set_smv');

//return_field_value("sum(a.ex_factory_qnty) as po_quantity"," pro_ex_factory_mst a, wo_po_break_down b","a.po_break_down_id=b.id and b.id='".$row[csf("po_id")]."' and a.is_deleted=0 and a.status_active=1","po_quantity");
//$lc_sc=return_field_value("b.contract_no as export_lc_no"," com_sales_contract b"," b.id in($sc_lc_id)' ","export_lc_no");
//$lc_sc=return_field_value("b.export_lc_no as export_lc_no","com_export_lc b"," b.id in($sc_lc_id) ","export_lc_no");
//$lc_type=return_field_value("is_lc","com_export_invoice_ship_mst","id in(".$row[csf('invoice_no')].")","is_lc");
//$last_ex_factory_date=return_field_value(" max(ex_factory_date) as ex_factory_date","pro_ex_factory_mst","po_break_down_id in(".$row[csf('po_id')].")","ex_factory_date");

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=215 and is_deleted=0 and status_active=1");
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$reportType=str_replace("'","",$reportType);
	$txt_job=str_replace("'","",$txt_job);
	$txt_order=str_replace("'","",$txt_order);
	$txt_style=str_replace("'","",$txt_style);
	$txt_int_ref=str_replace("'","",$txt_int_ref);
	$job_year=str_replace("'","",$cbo_year);
	if($job_year!=0)
	{
		if($db_type==0)
		{
			$job_year_cond=" and year(c.insert_date)=".$job_year.""; 
		}
		else
		{
			$job_year_cond=" and to_char(c.insert_date,'YYYY')=".$job_year."";
		}
	}
	else
	{
		$job_year_cond="";
	}

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$str_cond="and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to ' ";
	}
	else
	{
		$str_cond="";
	}
	if($txt_job!="")
	{
		// $job_cond="and c.job_no_prefix_num like '%".trim($txt_job)."%' ";
		$job_cond="and c.job_no_prefix_num=$txt_job";
	}
	else
	{
		$job_cond="";
	}
	if($txt_style!="")
	{
		// $style_cond="and c.style_ref_no like '%".trim($txt_style)."%' ";
		$style_cond="and c.style_ref_no='$txt_style'";
	}
	else
	{
		$style_cond="";
	}
	if($txt_order!="")
	{
		// $order_cond="and b.po_number like '%".trim($txt_order)."%'  ";
		$order_cond="and b.po_number='$txt_order'";
	}
	else
	{
		$order_cond="";
	}
	if($txt_int_ref!="")
	{
		// $int_ref_cond="and b.grouping like '%".trim($txt_int_ref)."%'  ";
		$int_ref_cond="and b.grouping='$txt_int_ref'";
	}
	else
	{
		$int_ref_cond="";
	}
	
	$details_report="";
	$master_data=array();
	$current_date=date("Y-m-d");
	$date=date("Y-m-d");$break_id=0;$sc_lc_id=0;
	$sy = date('Y',strtotime($txt_date_from));
	$basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst where year=$sy",'comapny_id','basic_smv');
	ob_start();

	if($reportType==1) // details button
	{
		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id, b.grouping, max(a.lc_sc_no) as lc_sc_arr_no, group_concat(distinct a.invoice_no) as invoice_no, group_concat(distinct a.item_number_id) as itm_num_id, 
			 
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, a.ex_factory_date  as ex_factory_date, group_concat(distinct  a.lc_sc_no) as lc_sc_no, group_concat(distinct a.delivery_mst_id) as challan_id,  b.shipment_date, b.po_number, (b.po_quantity*c.total_set_qnty) as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv,c.total_set_qnty,
			group_concat(distinct a.shiping_mode) as shiping_mode
			
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c 
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$cbo_company_name' $job_cond $int_ref_cond $str_cond $style_cond $order_cond $job_year_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3) and c.is_deleted=0 and c.status_active=1
			group by 
					b.id, b.grouping, b.shipment_date, b.po_number, b.unit_price, c.id, c.company_name, c.buyer_name, c.style_ref_no, c.style_description, c.set_smv ,a.ex_factory_date
			order by c.buyer_name, b.shipment_date ASC";
		}
		else if($db_type==2)
		{
			 $sql= "SELECT c.id as job_id,b.id as po_id, b.grouping, max(a.lc_sc_no) as lc_sc_arr_no, 
			LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no,
			LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id, 
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, a.ex_factory_date as ex_factory_date,  
			LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no, 
			LISTAGG(CAST( a.delivery_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.delivery_mst_id) as challan_id,
			b.shipment_date, b.po_number, (b.po_quantity*c.total_set_qnty) as po_quantity,(b.unit_price/c.total_set_qnty) as unit_price,b.shiping_status, 
			c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv,c.total_set_qnty,
			LISTAGG(CAST( a.shiping_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.shiping_mode) as shiping_mode 
			
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c 
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$cbo_company_name' $job_cond $int_ref_cond  $str_cond $style_cond $order_cond $job_year_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3) and c.is_deleted=0 and c.status_active=1 
			group by 
			c.id,b.id, b.grouping, b.shipment_date, b.po_number, b.unit_price,b.po_quantity,b.shiping_status,c.total_set_qnty,c.id,c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, c.insert_date, c.style_ref_no, c.style_description,c.total_set_qnty, c.set_smv,a.ex_factory_date
			order by c.buyer_name,b.id, b.shipment_date ASC";
		}
		
		// echo $sql;die;
		$sql_result=sql_select($sql);
		if(count($sql_result)==0)
		{
			echo '<div style="text-align:center;color:red;">Data not found!</div>';die;
		}

		$job_id_arr = array();
		$po_id_arr = array();
		$inv_id_arr = array();
		$lc_sc_id_arr = array();
		$rowspan_arr = array();
		foreach ($sql_result as $v) 
		{
			$po_id_arr[$v['PO_ID']] = $v['PO_ID'];
			$job_id_arr[$v['JOB_ID']] = $v['JOB_ID'];
			$invoce_id_arr=array_unique(explode(",",$v['INVOICE_NO']));
			foreach ($invoce_id_arr as $r) 
			{
				$inv_id_arr[$r] = $r;
			}
			$lc_sc_no_id_arr=array_unique(explode(",",$v['LC_SC_NO']));
			foreach ($lc_sc_no_id_arr as $r) 
			{
				$lc_sc_id_arr[$r] = $r;
			}	
			
			$rowspan_arr[$v['PO_ID']]++;
		}
		// echo "<pre>";print_r($rowspan_arr);

		// ============================= store data in gbl table ==============================
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in(1,2,3,4) and ENTRY_FORM=107");
		oci_commit($con);
		
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 107, 1, $po_id_arr, $empty_arr);//Po ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 107, 2, $inv_id_arr, $empty_arr);//invoice ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 107, 3, $lc_sc_id_arr, $empty_arr);//lc/sc ID
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 107, 4, $job_id_arr, $empty_arr);//job ID
		disconnect($con);

		unset($po_id_arr);
		unset($inv_id_arr);
		unset($lc_sc_id_arr);
		unset($job_id_arr);

		$print_report_format=return_library_array( "select template_name, format_id from lib_report_template where module_id=7 and report_id=86 and is_deleted=0 and status_active=1",'template_name','format_id');

		$exfact_sql=sql_select("SELECT po_break_down_id,
		
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty, 
		sum(total_carton_qnty) as carton_qnty from pro_ex_factory_mst a,GBL_TEMP_ENGINE tmp where a.id=tmp.ref_val and tmp.entry_form=107  and tmp.user_id=$user_id and tmp.ref_from=1 and  status_active=1 and is_deleted=0 group by po_break_down_id");
		$exfact_qty_arr=$exfact_return_qty_arr=$exfact_cartoon_arr=array();
		foreach($exfact_sql as $row)
		{
			$exfact_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
			$exfact_cartoon_arr[$row[csf("po_break_down_id")]]=$row[csf("carton_qnty")];
			$exfact_return_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("ex_factory_return_qnty")];
		}
		$inspection_date_arr=return_library_array( "SELECT po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection a,GBL_TEMP_ENGINE tmp where a.po_break_down_id=tmp.ref_val and tmp.entry_form=107  and tmp.user_id=$user_id and tmp.ref_from=1 and status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		$invoice_array=return_library_array( "SELECT id,invoice_no from com_export_invoice_ship_mst a,GBL_TEMP_ENGINE tmp where a.id=tmp.ref_val and tmp.entry_form=107  and tmp.user_id=$user_id and tmp.ref_from=2", "id", "invoice_no"  );
		//$shipping_mode_array=return_library_array( "select id,shipping_mode from com_export_invoice_ship_mst", "id", "shipping_mode"  );
		$lc_sc_type_arr=return_library_array( "SELECT id,is_lc from com_export_invoice_ship_mst a,GBL_TEMP_ENGINE tmp where a.id=tmp.ref_val and tmp.entry_form=107  and tmp.user_id=$user_id and tmp.ref_from=2", "id", "is_lc"  );
		$lc_sc_id_array=return_library_array( "SELECT id,lc_sc_id from com_export_invoice_ship_mst a,GBL_TEMP_ENGINE tmp where a.id=tmp.ref_val and tmp.entry_form=107  and tmp.user_id=$user_id and tmp.ref_from=2", "id", "lc_sc_id"  );

		$lc_num_arr=return_library_array( "SELECT id,export_lc_no from com_export_lc a,GBL_TEMP_ENGINE tmp where a.id=tmp.ref_val and tmp.entry_form=107  and tmp.user_id=$user_id and tmp.ref_from=3", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "SELECT id,contract_no from com_sales_contract a,GBL_TEMP_ENGINE tmp where a.id=tmp.ref_val and tmp.entry_form=107  and tmp.user_id=$user_id and tmp.ref_from=3", "id", "contract_no"  );
		
		$forwarder_arr=return_library_array( "SELECT id,supplier_name from lib_supplier", "id", "supplier_name");
		$costing_per_arr = return_library_array("SELECT job_no, costing_per from wo_pre_cost_mst a,GBL_TEMP_ENGINE tmp where a.job_id=tmp.ref_val and tmp.entry_form=107  and tmp.user_id=$user_id and tmp.ref_from=4","job_no","costing_per"); 
		$tot_cost_arr = return_library_array("SELECT job_no, cm_for_sipment_sche from wo_pre_cost_dtls a,GBL_TEMP_ENGINE tmp where a.job_id=tmp.ref_val and tmp.entry_form=107  and tmp.user_id=$user_id and tmp.ref_from=4","job_no","cm_for_sipment_sche"); 
		
		
		$challan_mst_arr=array();
		$challan_sql="SELECT a.id, a.sys_number_prefix_num,a.sys_number, a.delivery_company_id as del_company, a.forwarder, a.truck_no, a.mobile_no, b.po_break_down_id from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and b.po_break_down_id=tmp.ref_val and tmp.entry_form=107  and tmp.user_id=$user_id and tmp.ref_from=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		//echo $challan_sql;
		$challan_sql_result=sql_select($challan_sql);
		foreach($challan_sql_result as $row)
		{
			//$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['challan']=$row[csf("sys_number_prefix_num")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['challan']=$row[csf("sys_number")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['del_company']=$row[csf("del_company")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['forwarder']=$row[csf("forwarder")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['truck_no']=$row[csf("truck_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['mobile_no']=$row[csf("mobile_no")];
		}
		$details_report .='<table width="3230" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2">';
		$i=1;
	
		$con = connect();
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in(1,2,3,4) and ENTRY_FORM=107");
		oci_commit($con);
		disconnect($con);
		
		//print_r($sql_result);die;
		$po_chk_arr = array();
		foreach($sql_result as $row)
		{
			if($po_chk_arr[$row['PO_ID']]=="")
			{
				$k=0;
				$po_chk_arr[$row['PO_ID']] = $row['PO_ID'];
			}
			
			
			$costing_per=$costing_per_arr[$row[csf('job_no')]];
			if($costing_per==1) $dzn_qnty=12;
			else if($costing_per==3) $dzn_qnty=12*2;
			else if($costing_per==4) $dzn_qnty=12*3;
			else if($costing_per==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$dzn_qnty=$dzn_qnty*$row[csf('total_set_qnty')];			
			$cm_per_pcs=$tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty;
			
			//$last_ex_factory_date=return_field_value(" max(ex_factory_date) as ex_factory_date","pro_ex_factory_mst","po_break_down_id in(".$row[csf('po_id')].")","ex_factory_date");
			// $ex_fact_date_range=$txt_date_from."*".$txt_date_to."_". 1;
			$ex_fact_date_range=$row[csf("ex_factory_date")]."_". 1;
			$total_exface_qnty=$txt_date_from."*".$txt_date_to."_". 2;
			if($break_id==0) $break_id=$row[csf('po_id')]; else $break_id=$break_id.",".$row[csf('po_id')];
			if($sc_lc_id==0) $sc_lc_id=$row[csf('lc_sc_no')]; else $sc_lc_id=$sc_lc_id.",".$row[csf('lc_sc_no')];
			//$lc_type=return_field_value("is_lc","com_export_invoice_ship_mst","id in(".$row[csf('invoice_no')].")","is_lc");


			$invoce_id_arr=array_unique(explode(",",$row[csf('invoice_no')]));
			$challan_id=array_unique(explode(",",$row[csf("challan_id")]));
			$challan_no=""; $forwarder=""; $vehi_no=""; $mobile_no="";

			
			$diff=($row[csf('shiping_status')]!=3)?datediff("d",$current_date, $row[csf("shipment_date")])-1:datediff("d",$row[csf("ex_factory_date")], $row[csf("shipment_date")])-1;// Count Days in Hand Update By REZA;	
			list($first_button)=explode(',',$print_report_format[$row[csf("company_name")]]);
			
			foreach($challan_id as $val)
			{
				//echo $val;
				$del_company=$challan_mst_arr[$val][$row[csf('po_id')]]['del_company'];

				$fv=$first_button.",".$val.",".$row[csf("company_name")].",".$del_company.",'".$row[csf('ex_factory_date')]."'";
				$challanFunction='<a href="##" onclick="fn_generate_print('.$fv.')">'.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'].'</a>';
				if($challan_no==""){$challan_no=$challanFunction;}else {$challan_no.=', '.$challanFunction;}

				//if($challan_no=="") $challan_no=$challan_mst_arr[$val][$row[csf('po_id')]]['challan']; else $challan_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'];
				if($forwarder=="") $forwarder=$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']]; else $forwarder.=','.$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no'];
				if($mobile_no=="") $mobile_no=$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']; else $mobile_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no'];
			}

			if ($i%2==0)  
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";	
			$comapny_id=$row[csf("company_name")];
			
			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td width="40" align="center">'.$i.'</td>
								<td width="60" align="center" ><p>'.$row[csf("job_no_prefix_num")].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("year")].'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("buyer_name")]].'</p></td>
								<td width="110" align="center"><p>'.$row[csf("po_number")].'</p></td>
								<td width="80" align="center"><p>'.$row[csf("grouping")].'</p></td>
								<td width="120" align="center"><p>'.$challan_no.'</p></td>
								<td width="100" align="center"><p>';
								$inv_id=""; $lc_sc_no=""; $ship_mode="";
								foreach($invoce_id_arr as $invoice_id)
								{
									if($inv_id=="") $inv_id=$invoice_array[$invoice_id]; else $inv_id=$inv_id.",".$invoice_array[$invoice_id];
									/*if($ship_mode=="") $ship_mode=$shipment_mode[$shipping_mode_array[$invoice_id]]; else $ship_mode=$ship_mode.",".$shipment_mode[$shipping_mode_array[$invoice_id]];*/
									$ship_mode_arr=array();
									foreach(explode(',',$row[csf("shiping_mode")]) as $sm){
										$ship_mode_arr[$sm]=$shipment_mode[$sm];
									}
									$ship_mode=implode(',',$ship_mode_arr);
									
									
									if($lc_sc_type_arr[$invoice_id]==1)
									{
										if($lc_sc_no=="") $lc_sc_no=$lc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no=$lc_sc_no.",".$lc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
									else
									{
										if($lc_sc_no=="") $lc_sc_no=$sc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no=$lc_sc_no.",".$sc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
								}
								
			$details_report .=$inv_id.'</p></td>
								<td width="100" align="center"><p>'.$lc_sc_no.'</p></td>
								<td width="100"><p>'.$row[csf("style_ref_no")].'</p></td>
								<td width="100"><p>'.$row[csf("style_description")].'</p></td>
								<td width="110" align="center"><p>';//$garments_item
								$item_name_arr=explode(",",$row[csf("itm_num_id")]);
								$item_name_arr=array_unique($item_name_arr);
								if(!empty($item_name_arr))
								{
									$p=1;
									foreach($item_name_arr as $item_id)
									{
										if($p!=1) $item_name_all .=",";
										$item_name_all .=$garments_item[$item_id];
										$p++;
									}
								}
								
								$total_ex_fact_qty=$exfact_qty_arr[$row[csf("po_id")]];
								$total_cartoon_qty=$exfact_cartoon_arr[$row[csf("po_id")]];
								$po_quantity=$row[csf("po_quantity")];
								$unit_price=$row[csf("unit_price")];
								$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]];
								$basic_qnty=($total_ex_fact_qty*$row[csf("set_smv")])/$basic_smv_arr[$row[csf("company_name")]];
								$cm_per_pcs_tot+=($cm_per_pcs*$current_ex_Fact_Qty);
								
			$details_report .=$item_name_all.'</p></td>
								<td width="80" align="center"><p>'.$row[csf("set_smv")].'</p></td>
								<td width="70" align="center"><p>'.change_date_format($row[csf("shipment_date")]).'</p></td>
								<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$ex_fact_date_range."','ex_date_popup'".')">'.change_date_format($row[csf('ex_factory_date')]).'</a></td>
								<td width="70" align="center"><p>'.$ship_mode.'</p></td>
								<td width="60" align="center"><p>'.$diff.'</p></td>';
								if($k==0)
								{
									$details_report .='<td rowspan="'.$rowspan_arr[$row[csf("po_id")]].'" width="80" align="right"><p>'. number_format($po_quantity,0,'', '').'</p></td>';
									$total_po_qty+=$row[csf("po_quantity")];
									
									$master_data[$row[csf("buyer_name")]]['po_qnty'] +=$row[csf("po_quantity")];
									$master_data[$row[csf("buyer_name")]]['po_value'] +=$row[csf("po_quantity")]*$row[csf("unit_price")];
								}

								$details_report .='<td width="70" align="right"><p>'. number_format($unit_price,4).'</p></td>
								<td width="100" align="right"><p>'. number_format(($po_quantity*$unit_price),2).'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$ex_fact_date_range."','ex_date_popup'".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2).'</p></td>
								<td width="80" align="right"><p>'. number_format($row[csf("carton_qnty")],0,'.', '').'</p></td>';
								if($k==0)
								{
									$details_report .='<td rowspan="'.$rowspan_arr[$row[csf("po_id")]].'" width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$total_exface_qnty."','ex_date_popup'".')">'.number_format($total_ex_fact_qty,0,'.', '').'</a></p></td>
									<td rowspan="'.$rowspan_arr[$row[csf("po_id")]].'" width="100" align="right"><p>'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2).'</p></td>';
								}
								$details_report .='<td width="80" align="right"><p>'.number_format($total_cartoon_qty,0,'.', '').'</p></td>
								<td width="100" align="right" title="Current Ex.Qty*SMV"><p>'. number_format($total_sales_minutes=$current_ex_Fact_Qty*$row[csf("set_smv")]).'</p></td>
								
								
								<td width="80" align="right"><p>'.number_format($basic_qnty,0,'','').'</p></td>
								<td width="80" align="right"><p>'. number_format($excess_shortage_qty=$po_quantity-$total_ex_fact_qty,0,'', '').'</p></td>
								<td width="100" align="right"><p>'. number_format($excess_shortage_value=$excess_shortage_qty*$unit_price,2).'</p></td>
								<td align="center" width="80"><p>'. number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0).'</p></td>
								<td width="50" align="right" title="CM per pcs: '.$cm_per_pcs.'">'.number_format($cm_per_pcs*$current_ex_Fact_Qty,2).'</td>
								<td width="100" align="center"><p>'.$forwarder.'</p></td>
								<td width="80" align="center"><p>'.$vehi_no.'</p></td>
								<td width="80" align="center"><p>'.$mobile_no.'</p></td>
								<td width="70" align="center"><p>'.(change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '0000-00-00' || change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row[csf('po_id')]]))).'</p></td>
								<td align="center"><p>'.$shipment_status[$row[csf('shiping_status')]].'</p></td>
							</tr>';
							
			$master_data[$row[csf("buyer_name")]]['b_id']=$row[csf("buyer_name")];	
			$master_data[$row[csf("buyer_name")]]['basic_qnty'] +=$basic_qnty;
			$master_data[$row[csf("buyer_name")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]];
			$master_data[$row[csf("buyer_name")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]])*$row[csf("unit_price")];
			if($k==0)
			{
				$master_data[$row[csf("buyer_name")]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
				$master_data[$row[csf("buyer_name")]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row[csf("unit_price")];
				$g_total_ex_qty+=$total_ex_fact_qty;				
				$g_total_ex_val+=$total_ex_fact_qty*$row[csf("unit_price")];
			}
			
			
			$total_basic_qty+=$basic_qnty;
			$total_po_valu+=$row[csf("po_quantity")]*$row[csf("unit_price")];
			$total_ex_qty+=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]];
			$total_crtn_qty+=$row[csf("carton_qnty")];
			$total_ex_valu=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]])*$row[csf("unit_price")];
			
			$g_total_ex_crtn+=$total_cartoon_qty;
			$g_sales_minutes+=$current_ex_Fact_Qty*$row[csf("set_smv")];
			$total_eecess_storage_qty+=$excess_shortage_qty;
			$total_eecess_storage_val+=$excess_shortage_value;
			
			$i++;$item_name_all="";
			$k++;
		} 
		
	
		
		$details_report .='
						</table>';
							
		foreach($master_data as $rows)
		{
			$total_po_val+=$rows[po_value];
		}
							
		?>
        <div style="width:3250x;">
            <div style="width:1220px" >
                <table width="1190"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="10" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="10" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report</strong></td>
                    </tr>
                     <tr align="center">
                        <td colspan="10" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                    </tr>
                    </table>
                    <table width="1190" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                    <thead>
                        <th width="40" height="34">SL</th>
                        <th width="130">Buyer Name</th>
                        <th width="100">PO Qty.</th>
                        <th width="130">PO Value</th>
                        <th width="100">PO Value(%)</th>
                        <th width="100">Current Ex-Fact. Qty.</th>
                        <th width="130">Current Ex-Fact. Value</th>
                        <th width="100">Total Ex-Fact. Qty.</th>
                        <th width="130">Total Ex-Fact. Value </th>
                        <th width="100">Total Ex-Fact. (Basic Qty)</th>
                        <th >Total Ex-Fact. Value %</th>
                    </thead>
                 </table>
                 <table width="1190" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
                 <?
                 $m=1;
                foreach($master_data as $rows)
                {
                    if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                    else
                    $bgcolor="#FFFFFF";
                     ?>
                  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" >
                        <td width="40" align="center"><? echo $m; ?></td>
                        <td width="130">
                        <p><?
                        echo $buyer_arr[$rows[b_id]];
                        ?></p>
                        </td>
                        <td width="100" align="right"><p><?  $po_quantity=$rows[po_qnty];echo number_format($po_quantity,0); $total_buyer_po_quantity+=$po_quantity; ?></p></td>
                        <td width="130" align="right" ><p  id="value_<? echo $i ; ?>"><? $buyer_po_value=$rows[po_value]; echo number_format($buyer_po_value,2 ,'.', '');  $total_buyer_po_value+=$buyer_po_value; ?></p></td>
                        <td width="100" align="right">
                         <? echo number_format(($buyer_po_value/$total_po_val)*100,2,'.','');$parcentages+=($buyer_po_value/$total_po_val)*100; ?>
                        </td>
                        <td width="100" align="right">
                        <p><?
                         $current_ex_Fact_Qty=$rows[ex_factory_qnty];  echo number_format($current_ex_Fact_Qty,0,'',''); $total_current_ex_Fact_Qty +=$current_ex_Fact_Qty;
                        ?></p>
                        </td>
                        <td width="130" align="right">
                        <p><?
                        $current_ex_fact_value=$rows[ex_factory_value]; echo number_format($current_ex_fact_value,2,'.',''); $total_current_ex_fact_value+=$current_ex_fact_value;
                        ?></p>
                        </td>
                        <td align="right" width="100">
                        <p><?
                         $total_ex_fact_qty=$rows[total_ex_fact_qty]; echo number_format($total_ex_fact_qty,0,'',''); $mt_total_ex_fact_qty+=$total_ex_fact_qty;
                        ?></p>
                        </td>
                        <td align="right" width="130">
                        <p><?
                         $total_ex_fact_value=$rows[total_ex_fact_value];  echo  number_format($total_ex_fact_value,2,'.',''); $mt_total_ex_fact_value+=$total_ex_fact_value;
                        ?></p>
                        </td>
                        <td width="100" align="right">
                        <p><?
                         $buyer_basic_qnty=$rows[basic_qnty];  echo number_format($buyer_basic_qnty,0,'',''); $total_buyer_basic_qnty +=$buyer_basic_qnty;
                        ?></p>
                        </td>
                        <td align="right">
                        <p><?
                        $total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
                        echo number_format($total_ex_fact_value_parcentage,0)
                        ?> %</p>
                        </td>
                    </tr>
                    <?
                    $i++;$m++;
                    $buyer_po_quantity=0;
                    $buyer_po_value=0;
                    $current_ex_Fact_Qty=0;
                    $current_ex_fact_value=0;
                    $total_ex_fact_qty=0;
                    $total_ex_fact_value=0;
                    
                }
                    ?>
                    <input type="hidden" name="total_i" id="total_i" value="<? echo $i; ?>" />
                    <tfoot>
                        <th align="right" colspan="2"><b>Total:</b></th>
                        <th  align="right" id="total_buyer_po_quantity"><? echo number_format($total_buyer_po_quantity,0);  ?></th>
                        <th  align="right" id="value_total_buyer_po_value"><? echo number_format($total_buyer_po_value,2 ,'.', ''); ?> </th>
                        <th align="right" id="parcentages"><? echo ceil($parcentages); ?></th>
                        <th  align="right" id="total_current_ex_Fact_Qty"><? echo number_format($total_current_ex_Fact_Qty,0); ?></th>
                        <th  align="right" id="value_total_current_ex_fact_value"><? echo  number_format($total_current_ex_fact_value,2); ?></th>
                        <th align="right" id="mt_total_ex_fact_qty"><? echo number_format($mt_total_ex_fact_qty,0); ?></th>
                        <th align="right" id="value_mt_total_ex_fact_value"><? echo number_format($mt_total_ex_fact_value,2); ?></th>
                        <th  align="right" id="total_buyer_basic_qnty"><? echo number_format($total_buyer_basic_qnty,0); ?></th>
                        <th align="right"></th>
                    </tfoot>
                </table>
            </div>
            <br />
            <div>
                <table width="3230"  >
                    <tr>
                    <td colspan="30" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="3230" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="40">SL</th>
                        <th width="60">Job</th>
                        <th width="60">Year</th>
                        <th width="100">Buyer Name</th>
                        <th width="110">Order NO</th>
                        <th width="80">Internal Ref.</th>
                        <th width="120">Challan NO</th>
                        <th width="100" >Invoice NO</th>
                        <th width="100" >LC/SC NO</th>
                        <th width="100">Style Ref. no.</th>
                        <th width="100">Style Description</th>
                        <th width="110">Item Name</th>
                        <th width="80">Item SMV</th>
                        <th width="70">Shipment Date</th>
                        <th width="70">Ex-Fac. Date</th>
                        <th width="70">Shipping Mode</th>
                        <th width="60">Days in Hand</th>
                        <th width="80">PO Qtny. (pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">PO Value</th>
                        <th width="80">Current Ex-Fact. Qty (pcs)</th>
                        <th width="100">Current Ex-Fact. Value</th>
                        <th width="80">Current cartoon Qty</th>
                        <th width="80">Total Ex-Fact. Qty.</th>
                        <th width="100">Total Ex-Fact. Value</th>
                        <th width="80">Total cartoon Qty</th>
                        <th width="100">Sales Minute</th>
                        <th width="80">Total Ex-Fact. (Basic Qty)</th>
                        <th width="80">Excess/ Shortage Qty</th>
                        <th width="100">Excess/ Shortage Value</th>
                        <th width="80">Total Ex-Fact. Qty. %</th>
                        <th width="50">Sales CM</th>
                        <th width="100">C & F Name</th>
                        <th width="80">Vehicle No</th>
                        <th width="80">Car Mobile No</th>
                        <th width="70">Inspaction Date</th>
                        <th>Ex-Fact Status</th>
                    </thead>
                </table>
            <div style="width:3250px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
            <? echo $details_report; ?>
            <table width="3230" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="40">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60" align="right"><strong>Total</strong></th>
                        <th width="80" id="total_po_qty" align="right"><? echo  number_format($total_po_qty,0);?></th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="100" align="right" id="value_total_po_valu"><? echo  number_format($total_po_valu,2); ?></th>
                        <th width="80" align="right" id="total_ex_qty"><? echo number_format($total_ex_qty,0); ?></th>
                        <th width="100" align="right" id="value_total_ex_valu"><? echo number_format($total_ex_valu,2);?></th>
                        <th width="80" align="right" id="total_crtn_qty"><? echo number_format($total_crtn_qty,0); ?></th>
                        <th width="80" align="right" id="g_total_ex_qty"><? echo number_format($g_total_ex_qty,0);?></th>
                        <th width="100" align="right" id="value_g_total_ex_val"><? echo number_format($g_total_ex_val,2);?></th>
                        <th width="80" align="right" id="g_total_ex_crtn"><? echo number_format($g_total_ex_crtn,0);?></th>
                        <th width="100" align="right" id="value_sales_minutes"><? echo number_format($g_sales_minutes);?></th>
                        
                        <th width="80" align="right" id="total_basic_qty"><? echo number_format($total_basic_qty,0); ?></th>
                        <th width="80" align="right" id="total_eecess_storage_qty"><? echo number_format($total_eecess_storage_qty,0);?></th>
                        <th width="100" align="right" id="value_total_eecess_storage_val"><? echo number_format($total_eecess_storage_val,0);?></th>
                        <th width="80">&nbsp;</th>
                        <th width="50" align="right" id="value_cm_per_pcs_tot"><? echo number_format($cm_per_pcs_tot,2);?></th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
        </div>
	   
		<?
		unset($sql_result);
	}
	else if($reportType==2)
	{
		/*$cbo_company_name=str_replace("'","",$cbo_company_name);
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);*/

		$target_basic_qnty=array();
		$month_id_start = date('m',strtotime($txt_date_from));
		$month_id_end = date('m',strtotime($txt_date_to));
		$year_id_start = date('Y',strtotime($txt_date_from));
		$year_id_end = date('Y',strtotime($txt_date_to));
		$month_date_cond="";
		
		if($year_id_start==$year_id_end)
		{
			 $month_date_cond=" (a.year_id=$year_id_start AND d.month_id between $month_id_start and $month_id_end";
		}
		else
		{
			$year_deve=$year_id_end-$year_id_start;
			if($year_deve>0)
			{
				for($i=0;$i<=$year_deve;$i++)
				{
					$cross_year_month_start=$cross_year_month_end="";
					if($i>0) $month_id_start=1;
					for($k=$month_id_start;$k<=12;$k++)
					{
						if($cross_year_month_start=="") $cross_year_month_start=$month_id_start;
						if($i==$year_deve){ $cross_year_month_end=($month_id_end*1);} else{ if($month_id_start==12) $cross_year_month_end=$month_id_start;}
						$month_id_start=$month_id_start+1;
					}
					if($month_date_cond=="")$month_date_cond.=" ((a.year_id=$year_id_start AND d.month_id between $cross_year_month_start and $cross_year_month_end )"; else $month_date_cond.=" or(a.year_id=$year_id_start AND d.month_id between $cross_year_month_start and $cross_year_month_end )";
					$year_id_start=$year_id_start+1;
					
				}
			}
		}
		$month_date_cond.=")";
		//echo $month_date_cond;die;
		
	
		 $sql_con = "SELECT  b.buyer_id, d.month_id, a.year_id, SUM((d.capacity_month_pcs* b.allocation_percentage)/100) AS cap_qnty FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b, lib_capacity_calc_mst c,  lib_capacity_year_dtls d
		WHERE 
		a.id=b.mst_id AND 
		a.year_id=c.year AND 
		a.month_id=d.month_id AND 
		c.id=d.mst_id AND 
		a.company_id=$cbo_company_name AND 
		c.comapny_id=$cbo_company_name AND
		$month_date_cond  AND
		a.status_active=1 and 
		a.is_deleted=0 and 
		b.status_active=1 and 
		b.is_deleted=0 and 
		c.status_active=1 and 
		c.is_deleted=0  
		GROUP BY b.buyer_id, d.month_id, a.year_id";
		
		//echo $sql_con;die;
		$buyer_wisi_data=array();
		$sql_data=sql_select($sql_con);
		foreach( $sql_data as $row)
		{
			
			$target_basic_qnty[$row[csf("buyer_id")]][$row[csf("year_id")].'-'.str_pad($row[csf("month_id")],2,"0",STR_PAD_LEFT)]+=$row[csf("cap_qnty")];
			if($row[csf("cap_qnty")]>0)
			{
			$buyer_tem_arr[$row[csf("buyer_id")]]=$row[csf("buyer_id")];
			$buyer_wisi_data[$row[csf("buyer_id")]]['lib_basic_qnty']+=$row[csf("cap_qnty")];
			}
		}
		//var_dump($target_basic_qnty);die;
		
		$tot_commision_rate_arr = return_library_array("select job_no, commission from wo_pre_cost_dtls","job_no","commission");
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche");
		$sql_res=sql_select("select b.po_break_down_id as po_id, 
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty 
		from pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");
		$ex_factory_qty_arr=array();
		foreach($sql_res as $row)
		{
			$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('return_qnty')];
		}
		
		$sql= "SELECT b.id as po_id, (b.unit_price/c.total_set_qnty) as unit_price, c.total_set_qnty, c.id as job_id, c.job_no, c.buyer_name, c.company_name, c.set_smv, a.ex_factory_qnty as ex_factory_qnty,a.ex_factory_date
		from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c 
		where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name =$cbo_company_name $job_cond $int_ref_cond $str_cond $job_year_cond and a.entry_form!=85  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3) and c.is_deleted=0 and c.status_active=1 
		order by a.ex_factory_date ASC ";
		
		//echo $sql; and c.buyer_name=73
		$sql_result=sql_select($sql);
		//print_r($sql_result);die;
		foreach($sql_result as $row)
		{
			$cm_val=0;
			//if(!in_array($row[csf('job_no')],$temp_arr)){
			
				$dzn_qnty=0; $cm_value=$cm_value_rate=0;
				if($costing_per_arr[$row[csf('job_no')]]==1) $dzn_qnty=12;
				else if($costing_per_arr[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
				else if($costing_per_arr[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
				else if($costing_per_arr[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				$dzn_qnty=$dzn_qnty*$row[csf('total_set_qnty')];
				$commision_per_pic=$tot_commision_rate_arr[$row[csf('job_no')]]/$dzn_qnty;
				$cm_value_rate=($tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty);
				//$temp_arr[]=$row[csf('job_no')];
			//}
			
			
			
			$exfactreturn_qty=$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty'];
			$basic_qnty=($row[csf("ex_factory_qnty")]*$row[csf("set_smv")])/$basic_smv_arr[$row[csf("company_name")]];
			$cm_val=$cm_value_rate*$row[csf("ex_factory_qnty")];
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['cm_value'] +=$cm_val;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfactreturn_qty;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['basic_qnty'] +=$basic_qnty;
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['lib_basic_qnty'] =$target_basic_qnty[$row[csf("buyer_name")]][date("Y-m",strtotime($row[csf("ex_factory_date")]))];
			
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]*$row[csf("unit_price")]);
			$buyer_tem_arr[$row[csf("buyer_name")]]=$row[csf("buyer_name")];
			
			$result_data_arr[date("Y-m",strtotime($row[csf("ex_factory_date")]))][$row[csf("buyer_name")]]['commision_cost'] +=($commision_per_pic*($row[csf("ex_factory_qnty")]-$exfactreturn_qty));	
		}
		//print_r($test_data_arr);die;
		$total_month=count($result_data_arr);
		$width=($total_month*600)+100; 
		$colspan=$total_month*6;
		$main_data="";$i=1;
		
		foreach($buyer_tem_arr as $buyer_id=>$val)
        {
			if ($i%2==0)  
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$main_data.='<tr bgcolor="'.$bgcolor.'" onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'" >
			<td width="100">'.$buyer_arr[$buyer_id].'</td>';
			$tot_lib_basic_qnty=$tot_basic_qnty=$tot_ex_factory_qnty=$tot_ex_factory_value=$tot_cm_value=$tot_commision=0;
			foreach($result_data_arr as $month_id=>$result)
			{
				$ex_factory_qnty=$result_data_arr[$month_id][$buyer_id]['ex_factory_qnty'];
				$ex_factory_value=$result_data_arr[$month_id][$buyer_id]['ex_factory_value'];
				$cm_value=$result_data_arr[$month_id][$buyer_id]['cm_value'];
				if($result_data_arr[$month_id][$buyer_id]['lib_basic_qnty']>0)
				{
				$lib_basic_qnty=$result_data_arr[$month_id][$buyer_id]['lib_basic_qnty'];
				}
				else
				{
				$lib_basic_qnty=$target_basic_qnty[$buyer_id][$month_id];
				}
				$basic_qnty=$result_data_arr[$month_id][$buyer_id]['basic_qnty'];
				
				$commision_cost=$result_data_arr[$month_id][$buyer_id]['commision_cost'];
				$main_data.='<td width="100" align="right">'. number_format($lib_basic_qnty,0).' </td>
				<td width="100" align="right">'.  number_format($ex_factory_qnty,0).' </td>
				<td width="100" align="right">'.  number_format($ex_factory_value,2).' </td>
				<td width="100" align="right">'.  number_format($basic_qnty,0).' </td>
				<td width="100" align="right">'.  number_format($ex_factory_value-$commision_cost,2).' </td>
				<td width="100" align="right">'.  number_format($cm_value,2).' </td>';
				
				
				
				$total_mon_data[$month_id]['lib_basic_qnty'] += $lib_basic_qnty;
				$total_mon_data[$month_id]['basic_qnty'] += $basic_qnty;
				$total_mon_data[$month_id]['ex_factory_qnty'] += $ex_factory_qnty;
				$total_mon_data[$month_id]['ex_factory_value'] += $ex_factory_value;
				$total_mon_data[$month_id]['cm_val'] += $cm_value;
				$total_mon_data[$month_id]['commision_cost'] += ($ex_factory_value-$commision_cost);
				$tot_lib_basic_qnty+=$lib_basic_qnty;
				$tot_basic_qnty+=$basic_qnty;
				$tot_ex_factory_qnty+=$ex_factory_qnty;
				$tot_ex_factory_value+=$ex_factory_value;
				$tot_cm_value+=$cm_value;
				$tot_commision+=($ex_factory_value-$commision_cost);
			}
			
			//$buyer_wisi_data[$buyer_id]['lib_basic_qnty'] += $tot_lib_basic_qnty;
			$buyer_wisi_data[$buyer_id]['basic_qnty'] += $tot_basic_qnty;
			$buyer_wisi_data[$buyer_id]['ex_factory_qnty'] += $tot_ex_factory_qnty;
			$buyer_wisi_data[$buyer_id]['ex_factory_value'] += $tot_ex_factory_value;
			$buyer_wisi_data[$buyer_id]['cm_val'] += $tot_cm_value;
			$buyer_wisi_data[$buyer_id]['commision_cost'] += $tot_commision;
			$main_data.='</tr>'; 
			$i++;
        }
		//echo $main_data;die
		//echo $total_month;die;
		ob_start();
		
		?>
        <div id="scroll_body">
        	<fieldset style="width:700px;">
            <table width="700"  cellspacing="0" align="left">
                <tr>
                    <td align="center" colspan="7" class="form_caption">
                    <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                	</td>
                </tr>
                <tr class="form_caption">
                	<td colspan="7" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report Summary</strong></td>
                </tr>
            </table>
            <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="" align="left">
            	<thead>
                	<tr>
                        <th width="100">Buyer</th>
                        <th width="100">Allocated Basic Qty</th>
                        <th width="100">Exfactory Qty</th>
                        <th width="100">Exfactory Value</th>
                        <th width="100">Ex factory Basic qty</th>
                        <th width="100">Ex-Fac value without comm</th>
                        <th >CM Value</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$p=1;
				foreach($buyer_wisi_data as $buyer_id_ref=>$row)
				{
					if ($p%2==0)  
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td><? echo $buyer_arr[$buyer_id_ref]; ?></td>
                        <td align="right"><? echo number_format($row["lib_basic_qnty"],0); ?></td>
                        <td align="right"><? echo number_format($row["ex_factory_qnty"],0); ?></td>
                        <td align="right"><? echo number_format($row["ex_factory_value"],2); ?></td>
                        <td align="right"><? echo number_format($row["basic_qnty"],0); ?></td>
                        <td align="right"><? echo number_format($row["commision_cost"],2); ?></td>
                        <td align="right"><? echo number_format($row["cm_val"],2); ?></td>
                    </tr>
                    <?
					$p++;
					$gt_lib_basic_qnty+=$row["lib_basic_qnty"];
					$gt_ex_factory_qnty+=$row["ex_factory_qnty"];
					$gt_ex_factory_value+=$row["ex_factory_value"];
					$gt_basic_qnty+=$row["basic_qnty"];
					$gt_cm_val+=$row["cm_val"];
					$gt_commision_cost+=$row["commision_cost"];
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                        <th>Grand Total:</th>
                        <th><? echo number_format($gt_lib_basic_qnty,0); ?></th>
                        <th><? echo number_format($gt_ex_factory_qnty,0); ?></th>
                        <th><? echo number_format($gt_ex_factory_value,2); ?></th>
                        <th><? echo number_format($gt_basic_qnty,0); ?></th>
                        <th><? echo number_format($gt_commision_cost,2); ?></th>
                        <th><? echo number_format($gt_cm_val,2); ?></th>
                    </tr>
                </tfoot>
            </table>
            <table width="700" align="left">
            	<tr><td>&nbsp;</td></tr>
            </table>
            <div>
            <table width="<? echo $width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="" align="left">
            <thead>
                <tr>
					<?
                    $m=1;
                    foreach($result_data_arr as $yearMonth=>$vale)
                    {
						$month_arr=explode("-",$yearMonth);
						$month_val=($month_arr[1]*1);
						if($m==1)
						{ 
							?>
							<th width="700" colspan="7"><? echo $months[$month_val]; ?></th>
							<?
						}
						else
						{
							?>
							<th width="600" colspan="6"><? echo $months[$month_val]; ?></th>
							<?
						}
						$m++;
                    }
                    ?>
                </tr>
               <tr>
                    <th width="100">Buyer</th>
                     <?
                    foreach($result_data_arr as $yearMonth=>$vale)
                    {
                        $month_arr=explode("-",$yearMonth);
                        ?>
                        <th width="100">Allocated Basic Qty</th>
                        <th width="100">Exfactory Qty</th>
                        <th width="100">Exfactory Value</th>
                        <th width="100">Ex factory Basic qty</th>
                        <th width="100">Ex-Fac value without comm</th>
                        <th width="100">CM Value</th>
                        <?
                    }
                    ?>
               </tr>
            </thead>
         <!-- </table>
        <table width="<? //echo $width;?>" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" id="" align="left"> -->
        	<tbody>
			<?
            echo $main_data;
			
            
            ?>
            </tbody>
            <tfoot>
                <th>Total:&nbsp;</th>
                <?
                foreach($total_mon_data as $row)
                {
                    ?>
                    <th><? echo number_format($row['lib_basic_qnty'],0); ?></th>
                    <th><? echo number_format($row['ex_factory_qnty'],0); ?></th>
                    <th><? echo number_format($row['ex_factory_value'],2); ?></th>
                    <th><? echo number_format($row['basic_qnty'],0); ?></th>
                    <th><? echo number_format($row['commision_cost'],2); ?></th>
                    <th><? echo number_format($row['cm_val'],2); ?></th>
                    <?
                }
                ?>
            </tfoot>

        </table>
        </table>
    	</div>
    	</fieldset>
        </div>
		<?
	}
	else if($reportType==3)
	{

		
		$exfact_sql=sql_select("select po_break_down_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty,
		 sum(total_carton_qnty) as carton_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id");
		$exfact_qty_arr=$exfact_return_qty_arr=$exfact_cartoon_arr=array();
		foreach($exfact_sql as $row)
		{
			$exfact_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("ex_factory_qnty")]-$row[csf("return_qnty")];
			$exfact_return_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("return_qnty")];
			$exfact_cartoon_arr[$row[csf("po_break_down_id")]]=$row[csf("carton_qnty")];
		}
		$inspection_date_arr=return_library_array( "select po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		$invoice_array=return_library_array( "select id,invoice_no from com_export_invoice_ship_mst", "id", "invoice_no"  );
		$shipping_mode_array=return_library_array( "select id,shipping_mode from com_export_invoice_ship_mst", "id", "shipping_mode"  );
		$lc_sc_type_arr=return_library_array( "select id,is_lc from com_export_invoice_ship_mst", "id", "is_lc"  );
		$lc_sc_id_array=return_library_array( "select id,lc_sc_id from com_export_invoice_ship_mst", "id", "lc_sc_id"  );
		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );
		
		$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
		$location_arr=return_library_array( "select id,location_name from lib_location", "id", "location_name");

		$challan_mst_arr=array();
		$challan_sql="select a.id, a.sys_number_prefix_num,a.sys_number, a.forwarder, a.truck_no, a.mobile_no, a.location_id, a.transport_supplier, a.lock_no, b.remarks, b.po_break_down_id 
		from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b 
		where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		//echo $challan_sql;
		$challan_sql_result=sql_select($challan_sql);
		foreach($challan_sql_result as $row)
		{
			//$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['challan']=$row[csf("sys_number_prefix_num")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['challan']=$row[csf("sys_number")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['forwarder']=$row[csf("forwarder")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['truck_no']=$row[csf("truck_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['mobile_no']=$row[csf("mobile_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['location_id']=$row[csf("location_id")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['transport_supplier']=$row[csf("transport_supplier")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['lock_no']=$row[csf("lock_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['remarks']=$row[csf("remarks")];
		}
		
				
		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id, max(a.lc_sc_no) as lc_sc_arr_no, group_concat(distinct a.invoice_no) as invoice_no, group_concat(distinct a.item_number_id) as itm_num_id, 
			
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,  
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date)  as ex_factory_date, group_concat(distinct  a.lc_sc_no) as lc_sc_no, group_concat(distinct a.delivery_mst_id) as challan_id,  b.shipment_date, b.po_number, (b.po_quantity*c.total_set_qnty) as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num, YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv,group_concat(distinct a.shiping_mode) as shiping_mode
			
			
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c 
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$cbo_company_name' $job_cond $int_ref_cond $str_cond $job_year_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3) and c.is_deleted=0 and c.status_active=1
			group by 
					b.id , b.shipment_date, b.po_number, b.unit_price, c.id, c.company_name, c.buyer_name, c.style_ref_no, c.style_description, c.set_smv
			order by c.buyer_name, b.shipment_date ASC";
		}
		else if($db_type==2)
		{
			$sql= "SELECT b.id as po_id,max(a.lc_sc_no) as lc_sc_arr_no, 
			LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no,
			LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id, 
			
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty, 
			sum(a.total_carton_qnty) as carton_qnty, max(a.ex_factory_date) as ex_factory_date,  
			LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no, 
			LISTAGG(CAST( a.delivery_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.delivery_mst_id) as challan_id,
			b.shipment_date, b.po_number, (b.po_quantity*c.total_set_qnty) as po_quantity,(b.unit_price/c.total_set_qnty) as unit_price,b.shiping_status, 
			c.id, c.company_name, c.buyer_name, c.job_no_prefix_num, to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv,
			LISTAGG(CAST( a.shiping_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.shiping_mode) as shiping_mode
			
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c 
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$cbo_company_name' $job_cond $int_ref_cond $str_cond $job_year_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3) and c.is_deleted=0 and c.status_active=1 
			group by 
					b.id , b.shipment_date, b.po_number, b.unit_price,b.po_quantity,b.shiping_status,c.total_set_qnty,c.id,c.company_name, c.buyer_name, c.job_no_prefix_num, c.insert_date, c.style_ref_no, c.style_description,c.total_set_qnty, c.set_smv
			order by c.buyer_name, b.shipment_date ASC";
		}
		
		//echo $sql;
		
		$i=1;$s=1;
		
		$details_report .='<table width="3380" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body3" align="left">';
		
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
		 $challan_id=implode(',',array_unique(explode(",",$row[csf('challan_id')])));
		  $data_arr[$challan_id][]=array(
				'po_id'=>$row[csf('po_id')],
				'lc_sc_no'=>$row[csf('lc_sc_no')],
				'invoice_no'=>$row[csf('invoice_no')],
				'challan_id'=>$row[csf('challan_id')],
				'shiping_status'=>$row[csf('shiping_status')],
				'shipment_date'=>$row[csf('shipment_date')],
				'ex_factory_date'=>$row[csf('ex_factory_date')],
				'job_no_prefix_num'=>$row[csf('job_no_prefix_num')],
				'year'=>$row[csf('year')],
				'buyer_name'=>$row[csf('buyer_name')],
				'company_name'=>$row[csf('company_name')],
				'po_number'=>$row[csf('po_number')],
				'style_ref_no'=>$row[csf('style_ref_no')],
				'style_description'=>$row[csf('style_description')],
				'po_quantity'=>$row[csf('po_quantity')],
				'unit_price'=>$row[csf('unit_price')],
				'ex_factory_qnty'=>$row[csf('ex_factory_qnty')]-$exfact_return_qty_arr[$row[csf("po_id")]],
				'set_smv'=>$row[csf('set_smv')],
				'shiping_mode'=>$row[csf('shiping_mode')]
			);

		}
		
		
		
		
		
		
		$tmp_challan_no_arr=array();
		foreach($data_arr as $challan=>$sql_result)
		{
			$s=1;
			
			foreach($sql_result as $row)
			{
			
			$ex_fact_date_range=$txt_date_from."*".$txt_date_to."_". 1;
			$total_exface_qnty=$txt_date_from."*".$txt_date_to."_". 2;
			if($break_id==0) $break_id=$row['po_id']; else $break_id=$break_id.",".$row['po_id'];
			if($sc_lc_id==0) $sc_lc_id=$row['lc_sc_no']; else $sc_lc_id=$sc_lc_id.",".$row['lc_sc_no'];
			
			$invoce_id_arr=array_unique(explode(",",$row['invoice_no']));
			$challan_id=array_unique(explode(",",$row["challan_id"]));
			$challan_no=$forwarder=$vehi_no=$mobile_no=$location=$transfort_com=$lock_no=$remarks="";
			
			$diff=($row['shiping_status']!=3)?datediff("d",$current_date, $row["shipment_date"])-1:datediff("d",$row["ex_factory_date"], $row["shipment_date"])-1;// Count Days in Hand Update By REZA;	
		
			
			foreach($challan_id as $val)
			{
				//echo $val;
				if($challan_no=="") $challan_no=$challan_mst_arr[$val][$row['po_id']]['challan']; else $challan_no.=','.$challan_mst_arr[$val][$row['po_id']]['challan'];
				if($forwarder=="") $forwarder=$forwarder_arr[$challan_mst_arr[$val][$row['po_id']]['forwarder']]; else $forwarder.=','.$forwarder_arr[$challan_mst_arr[$val][$row['po_id']]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row['po_id']]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row['po_id']]['truck_no'];
				if($mobile_no=="") $mobile_no=$challan_mst_arr[$val][$row['po_id']]['mobile_no']; else $mobile_no.=','.$challan_mst_arr[$val][$row['po_id']]['mobile_no'];
				if($location=="") $location=$location_arr[$challan_mst_arr[$val][$row['po_id']]['location_id']]; else $location.=','.$location_arr[$challan_mst_arr[$val][$row['po_id']]['location_id']];
				if($transfort_com=="") $transfort_com=$forwarder_arr[$challan_mst_arr[$val][$row['po_id']]['transport_supplier']]; else $transfort_com.=','.$forwarder_arr[$challan_mst_arr[$val][$row['po_id']]['transport_supplier']];
				if($lock_no=="") $lock_no=$challan_mst_arr[$val][$row['po_id']]['lock_no']; else $lock_no.=','.$challan_mst_arr[$val][$row['po_id']]['lock_no'];
				if($remarks=="") $remarks=$challan_mst_arr[$val][$row['po_id']]['remarks']; else $remarks.=','.$challan_mst_arr[$val][$row['po_id']]['remarks'];
			}
			
			$challan_no=implode(",",array_unique(explode(",",$challan_no)));
			$forwarder=implode(",",array_unique(explode(",",$forwarder)));
			$vehi_no=implode(",",array_unique(explode(",",$vehi_no)));
			$mobile_no=implode(",",array_unique(explode(",",$mobile_no)));
			$location=implode(",",array_unique(explode(",",$location)));
			$transfort_com=implode(",",array_unique(explode(",",$transfort_com)));
			$lock_no=implode(",",array_unique(explode(",",$lock_no)));
			$remarks=implode(",",array_unique(explode(",",$remarks)));

			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	  
			$comapny_id=$row["company_name"];
			if(!in_array($challan_no,$tmp_challan_no_arr))
			{
			$details_report .='<tr><td colspan="38" bgcolor="#CCCCCC"> Challan NO: '.$challan_no.'</td></tr>';	
			}
			$tmp_challan_no_arr[]=$challan_no;
			
			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td width="40" align="center">'.$s.'</td>
								<td width="60" align="center" ><p>'.$row["job_no_prefix_num"].'</p></td>
								<td width="60" align="center" ><p>'.$row["year"].'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row["buyer_name"]].'</p></td>
								<td width="110" align="center"><p>'.$row["po_number"].'</p></td>
								<td width="100" align="center"><p>';
								$inv_id=""; $lc_sc_no=""; $ship_mode="";
								foreach($invoce_id_arr as $invoice_id)
								{
									if($inv_id=="") $inv_id=$invoice_array[$invoice_id]; else $inv_id=$inv_id.",".$invoice_array[$invoice_id];
									/*if($ship_mode=="") $ship_mode=$shipment_mode[$shipping_mode_array[$invoice_id]]; else $ship_mode=$ship_mode.",".$shipment_mode[$shipping_mode_array[$invoice_id]];*/
									
									//$ship_mode=$shipment_mode[$row["shiping_mode"]];
									$ship_mode_arr=array();
									foreach(explode(',',$row[csf("shiping_mode")]) as $sm){
										$ship_mode_arr[$sm]=$shipment_mode[$sm];
									}
									$ship_mode=implode(',',$ship_mode_arr);
									
									
									
									if($lc_sc_type_arr[$invoice_id]==1)
									{
										if($lc_sc_no=="") $lc_sc_no=$lc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no=$lc_sc_no.",".$lc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
									else
									{
										if($lc_sc_no=="") $lc_sc_no=$sc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no=$lc_sc_no.",".$sc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
								}
								
			$details_report .=$inv_id.'</p></td>
								<td width="100" align="center"><p>'.$lc_sc_no.'</p></td>
								<td width="100"><p>'.$row["style_ref_no"].'</p></td>
								<td width="100"><p>'.$row["style_description"].'</p></td>
								<td width="110" align="center"><p>';//$garments_item
								$item_name_arr=explode(",",$row["itm_num_id"]);
								$item_name_arr=array_unique($item_name_arr);
								if(!empty($item_name_arr))
								{
									$p=1;
									foreach($item_name_arr as $item_id)
									{
										if($p!=1) $item_name_all .=",";
										$item_name_all .=$garments_item[$item_id];
										$p++;
									}
								}
								$total_ex_fact_qty=$exfact_qty_arr[$row["po_id"]]-$exfact_return_qty_arr[$row[csf("po_id")]];
								$total_cartoon_qty=$exfact_cartoon_arr[$row["po_id"]];
								$po_quantity=$row["po_quantity"];
								$unit_price=$row["unit_price"];
								$current_ex_Fact_Qty=$row["ex_factory_qnty"]-$exfact_return_qty_arr[$row[csf("po_id")]];
								$basic_qnty=($total_ex_fact_qty*$row["set_smv"])/$basic_smv_arr[$row["company_name"]];
			$details_report .=$item_name_all.'</p></td>
								<td width="80" align="center"><p>'.$row["set_smv"].'</p></td>
								<td width="70" align="center"><p>'.change_date_format($row["shipment_date"]).'</p></td>
								<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row['po_id']."','".$ex_fact_date_range."','ex_date_popup'".')">'.change_date_format($row['ex_factory_date']).'</a></td>
								<td width="70" align="center"><p>'.$ship_mode.'</p></td>
								<td width="60" align="center"><p>'.$diff.'</p></td>
								<td width="80" align="right"><p>'. number_format($po_quantity,0,'', '').'</p></td>
								<td width="70" align="right"><p>'. number_format($unit_price,4).'</p></td>
								<td width="100" align="right"><p>'. number_format(($po_quantity*$unit_price),2).'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row['po_id']."','".$ex_fact_date_range."','ex_date_popup'".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2).'</p></td>
								<td width="80" align="right"><p>'. number_format($row["carton_qnty"],0,'.', '').'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row['po_id']."','".$total_exface_qnty."','ex_date_popup'".')">'.number_format($total_ex_fact_qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2).'</p></td>
								<td width="80" align="right"><p>'.number_format($total_cartoon_qty,0,'.', '').'</p></td>
								<td width="100" align="right" title="Current Ex.Qty*SMV"><p>'. number_format($total_sales_minutes=$current_ex_Fact_Qty*$row["set_smv"]).'</p></td>
								
								
								<td width="80" align="right"><p>'.number_format($basic_qnty,0,'','').'</p></td>
								<td width="80" align="right"><p>'. number_format($excess_shortage_qty=$po_quantity-$total_ex_fact_qty,0,'', '').'</p></td>
								<td width="100" align="right"><p>'. number_format($excess_shortage_value=$excess_shortage_qty*$unit_price,2).'</p></td>
								<td align="center" width="80"><p>'. number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0).'</p></td>
								<td width="110"><p>'.$location.'</p></td>
								<td width="110"><p>'.$transfort_com.'</p></td>
								<td width="80" align="center"><p>'.$lock_no.'</p></td>
								<td width="100" align="center"><p>'.$forwarder.'</p></td>
								<td width="80" align="center"><p>'.$vehi_no.'</p></td>
								<td width="80" align="center"><p>'.$mobile_no.'</p></td>
								<td width="70" align="center"><p>'.(change_date_format($inspection_date_arr[$row['po_id']]) == '0000-00-00' || change_date_format($inspection_date_arr[$row['po_id']]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row['po_id']]))).'</p></td>
								<td width="120"><p>'.$shipment_status[$row['shiping_status']].'</p></td>
								<td><p>'.$remarks.'</p></td>
							</tr>';
							
			$master_data[$row["buyer_name"]]['b_id']=$row["buyer_name"];	
			$master_data[$row["buyer_name"]]['po_qnty'] +=$row["po_quantity"];
			$master_data[$row["buyer_name"]]['po_value'] +=$row["po_quantity"]*$row["unit_price"];
			$master_data[$row["buyer_name"]]['basic_qnty'] +=$basic_qnty;
			$master_data[$row["buyer_name"]]['ex_factory_qnty'] +=$row["ex_factory_qnty"]-$exfact_return_qty_arr[$row[csf("po_id")]];
			$master_data[$row["buyer_name"]]['ex_factory_value'] +=$row["ex_factory_qnty"]*$row["unit_price"];
			$master_data[$row["buyer_name"]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
			$master_data[$row["buyer_name"]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row["unit_price"];
			
			$total_po_qty+=$row["po_quantity"];
			$total_basic_qty+=$basic_qnty;
			$total_po_valu+=$row["po_quantity"]*$row["unit_price"];
			$total_ex_qty+=$row["ex_factory_qnty"]-$exfact_return_qty_arr[$row[csf("po_id")]];
			$total_crtn_qty+=$row["carton_qnty"];
			$total_ex_valu=($row["ex_factory_qnty"]-$exfact_return_qty_arr[$row[csf("po_id")]])*$row["unit_price"];
			$g_total_ex_qty+=$total_ex_fact_qty;
			$g_total_ex_crtn+=$total_cartoon_qty;
			$g_total_ex_val+=$total_ex_fact_qty*$row["unit_price"];
			$g_sales_minutes+=$current_ex_Fact_Qty*$row["set_smv"];
			$total_eecess_storage_qty+=$excess_shortage_qty;
			$total_eecess_storage_val+=$excess_shortage_value;
			
			$s++;$item_name_all="";
		   }
			$i++;
		} 
		
		/*echo "SELECT b.export_lc_no as export_lc_no from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.wo_po_break_down_id in($break_id) and b.id in($sc_lc_id)"."<br>";
		echo "SELECT b.contract_no as export_lc_no from com_export_lc_order_info a, com_sales_contract b where a.com_export_lc_id=b.id and a.wo_po_break_down_id in($break_id) and b.id in($sc_lc_id)"."<br>";*/
		//print_r($master_data);
		
		$details_report .='
						</table>';
							
		foreach($master_data as $rows)
		{
			$total_po_val+=$rows[po_value];
		}
							
		?>
        <div style="width:3100x;">
            <div style="width:1220px" >
                <table width="1190"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="10" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="10" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report</strong></td>
                    </tr>
                     <tr align="center">
                        <td colspan="10" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                    </tr>
                    </table>
                    <table width="1190" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                    <thead>
                        <th width="40" height="34">SL</th>
                        <th width="130">Buyer Name</th>
                        <th width="100">PO Qty.</th>
                        <th width="130">PO Value</th>
                        <th width="100">PO Value(%)</th>
                        <th width="100">Current Ex-Fact. Qty.</th>
                        <th width="130">Current Ex-Fact. Value</th>
                        <th width="100">Total Ex-Fact. Qty.</th>
                        <th width="130">Total Ex-Fact. Value </th>
                        <th width="100">Total Ex-Fact. (Basic Qty)</th>
                        <th >Total Ex-Fact. Value %</th>
                    </thead>
                 </table>
                 <table width="1190" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
                 <?
                 $m=1;
                foreach($master_data as $rows)
                {
                    if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                    else
                    $bgcolor="#FFFFFF";
                     ?>
                  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" >
                        <td width="40" align="center"><? echo $m; ?></td>
                        <td width="130">
                        <p><?
                        echo $buyer_arr[$rows[b_id]];
                        ?></p>
                        </td>
                        <td width="100" align="right"><p><?  $po_quantity=$rows[po_qnty];echo number_format($po_quantity,0); $total_buyer_po_quantity+=$po_quantity; ?></p></td>
                        <td width="130" align="right" ><p  id="value_<? echo $i ; ?>"><? $buyer_po_value=$rows[po_value]; echo number_format($buyer_po_value,2 ,'.', '');  $total_buyer_po_value+=$buyer_po_value; ?></p></td>
                        <td width="100" align="right">
                         <? echo number_format(($buyer_po_value/$total_po_val)*100,2,'.','');$parcentages+=($buyer_po_value/$total_po_val)*100; ?>
                        </td>
                        <td width="100" align="right">
                        <p><?
                         $current_ex_Fact_Qty=$rows[ex_factory_qnty];  echo number_format($current_ex_Fact_Qty,0,'',''); $total_current_ex_Fact_Qty +=$current_ex_Fact_Qty;
                        ?></p>
                        </td>
                        <td width="130" align="right">
                        <p><?
                        $current_ex_fact_value=$rows[ex_factory_value]; echo number_format($current_ex_fact_value,2,'.',''); $total_current_ex_fact_value+=$current_ex_fact_value;
                        ?></p>
                        </td>
                        <td align="right" width="100">
                        <p><?
                         $total_ex_fact_qty=$rows[total_ex_fact_qty]; echo number_format($total_ex_fact_qty,0,'',''); $mt_total_ex_fact_qty+=$total_ex_fact_qty;
                        ?></p>
                        </td>
                        <td align="right" width="130">
                        <p><?
                         $total_ex_fact_value=$rows[total_ex_fact_value];  echo  number_format($total_ex_fact_value,2,'.',''); $mt_total_ex_fact_value+=$total_ex_fact_value;
                        ?></p>
                        </td>
                        <td width="100" align="right">
                        <p><?
                         $buyer_basic_qnty=$rows[basic_qnty];  echo number_format($buyer_basic_qnty,0,'',''); $total_buyer_basic_qnty +=$buyer_basic_qnty;
                        ?></p>
                        </td>
                        <td align="right">
                        <p><?
                        $total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
                        echo number_format($total_ex_fact_value_parcentage,0)
                        ?> %</p>
                        </td>
                    </tr>
                    <?
                    $i++;$m++;
                    $buyer_po_quantity=0;
                    $buyer_po_value=0;
                    $current_ex_Fact_Qty=0;
                    $current_ex_fact_value=0;
                    $total_ex_fact_qty=0;
                    $total_ex_fact_value=0;
                    
                }
                    ?>
                    <input type="hidden" name="total_i" id="total_i" value="<? echo $i; ?>" />
                    <tfoot>
                        <th align="right" colspan="2"><b>Total:</b></th>
                        <th  align="right" id="total_buyer_po_quantity"><? echo number_format($total_buyer_po_quantity,0);  ?></th>
                        <th  align="right" id="value_total_buyer_po_value"><? echo number_format($total_buyer_po_value,2 ,'.', ''); ?> </th>
                        <th align="right" id="parcentages"><? echo ceil($parcentages); ?></th>
                        <th  align="right" id="total_current_ex_Fact_Qty"><? echo number_format($total_current_ex_Fact_Qty,0); ?></th>
                        <th  align="right" id="value_total_current_ex_fact_value"><? echo  number_format($total_current_ex_fact_value,2); ?></th>
                        <th align="right" id="mt_total_ex_fact_qty"><? echo number_format($mt_total_ex_fact_qty,0); ?></th>
                        <th align="right" id="value_mt_total_ex_fact_value"><? echo number_format($mt_total_ex_fact_value,2); ?></th>
                        <th  align="right" id="total_buyer_basic_qnty"><? echo number_format($total_buyer_basic_qnty,0); ?></th>
                        <th align="right"></th>
                    </tfoot>
                </table>
            </div>
            <br />
            <div>
                <table width="3380"  >
                    <tr>
                    <td colspan="28" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="3380" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="40">SL</th>
                        <th width="60">Job</th>
                        <th width="60">Year</th>
                        <th width="100">Buyer Name</th>
                        <th width="110">Order NO</th>
                        <th width="100" >Invoice NO</th>
                        <th width="100" >LC/SC NO</th>
                        <th width="100">Style Ref. no.</th>
                        <th width="100">Style Description</th>
                        <th width="110">Item Name</th>
                        <th width="80">Item SMV</th>
                        <th width="70">Shipment Date</th>
                        <th width="70">Ex-Fac. Date</th>
                        <th width="70">Shipping Mode</th>
                        <th width="60">Days in Hand</th>
                        <th width="80">PO Qtny. (pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">PO Value</th>
                        <th width="80">Current Ex-Fact. Qty (pcs)</th>
                        <th width="100">Current Ex-Fact. Value</th>
                        <th width="80">Current cartoon Qty</th>
                        <th width="80">Total Ex-Fact. Qty.</th>
                        <th width="100">Total Ex-Fact. Value</th>
                        <th width="80">Total cartoon Qty</th>
                        <th width="100">Sales Minute</th>
                        <th width="80">Total Ex-Fact. (Basic Qty)</th>
                        <th width="80">Excess/ Shortage Qty</th>
                        <th width="100">Excess/ Shortage Value</th>
                        <th width="80">Total Ex-Fact. Qty. %</th>
                        <th width="110">Location</th>
                        <th width="110">Transport Company</th>
                        <th width="80">Lock No</th>
                        <th width="100">C & F Name</th>
                        <th width="80">Vehicle No</th>
                        <th width="80">Car Mobile No</th>
                        <th width="70">Inspaction Date</th>
                        <th width="120">Ex-Fact Status</th>
                        <th>Remarks</th>
                    </thead>
                </table>
            <div style="width:3400px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
            <? echo $details_report; ?>
            <table width="3380" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="40">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60" align="right"><strong>Total</strong></th>
                        <th width="80" id="total_po_qty" align="right"><? echo  number_format($total_po_qty,0);?></th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="100" align="right" id="value_total_po_valu"><? echo  number_format($total_po_valu,2); ?></th>
                        <th width="80" align="right" id="total_ex_qty"><? echo number_format($total_ex_qty,0); ?></th>
                        <th width="100" align="right" id="value_total_ex_valu"><? echo number_format($total_ex_valu,2);?></th>
                        <th width="80" align="right" id="total_crtn_qty"><? echo number_format($total_crtn_qty,0); ?></th>
                        <th width="80" align="right" id="g_total_ex_qty"><? echo number_format($g_total_ex_qty,0);?></th>
                        <th width="100" align="right" id="value_g_total_ex_val"><? echo number_format($g_total_ex_val,2);?></th>
                        <th width="80" align="right" id="g_total_ex_crtn"><? echo number_format($g_total_ex_crtn,0);?></th>
                        <th width="100" align="right" id="value_sales_minutes"><? echo number_format($g_sales_minutes);?></th>
                        
                        <th width="80" align="right" id="total_basic_qty"><? echo number_format($total_basic_qty,0); ?></th>
                        <th width="80" align="right" id="total_eecess_storage_qty"><? echo number_format($total_eecess_storage_qty,0);?></th>
                        <th width="100" align="right" id="value_total_eecess_storage_val"><? echo number_format($total_eecess_storage_val,0);?></th>
                        <th width="80">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
        </div>
	   
		<?
	}
	else if($reportType==4) //details2 button
	{

		$print_report_format=return_library_array( "select template_name, format_id from lib_report_template where module_id=7 and report_id=86 and is_deleted=0 and status_active=1",'template_name','format_id');

		$exfact_sql=sql_select("select po_break_down_id,
		
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty, 
		sum(total_carton_qnty) as carton_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id");
		$exfact_qty_arr=$exfact_return_qty_arr=$exfact_cartoon_arr=array();
		foreach($exfact_sql as $row)
		{
			$exfact_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
			$exfact_cartoon_arr[$row[csf("po_break_down_id")]]=$row[csf("carton_qnty")];
			$exfact_return_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("ex_factory_return_qnty")];
		}
		$inspection_date_arr=return_library_array( "select po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		$invoice_array=return_library_array( "select id,invoice_no from com_export_invoice_ship_mst", "id", "invoice_no"  );
		$invoice_qty_array=return_library_array( "select id,invoice_quantity from com_export_invoice_ship_mst", "id", "invoice_quantity" );
		//$shipping_mode_array=return_library_array( "select id,shipping_mode from com_export_invoice_ship_mst", "id", "shipping_mode"  );
		$lc_sc_type_arr=return_library_array( "select id,is_lc from com_export_invoice_ship_mst", "id", "is_lc"  );
		$lc_sc_id_array=return_library_array( "select id,lc_sc_id from com_export_invoice_ship_mst", "id", "lc_sc_id"  );
		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );
		
		$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
		$color_arr = return_library_array("select id, color_name from lib_color","id","color_name"); 
		
		
		$challan_mst_arr=array();
		$challan_sql="select a.id, a.sys_number_prefix_num,a.sys_number, a.delivery_company_id as del_company, a.forwarder, a.truck_no, a.mobile_no, b.po_break_down_id from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		//echo $challan_sql;
		$challan_sql_result=sql_select($challan_sql);
		foreach($challan_sql_result as $row)
		{
			//$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['challan']=$row[csf("sys_number_prefix_num")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['challan']=$row[csf("sys_number")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['del_company']=$row[csf("del_company")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['forwarder']=$row[csf("forwarder")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['truck_no']=$row[csf("truck_no")];
			$challan_mst_arr[$row[csf("id")]][$row[csf("po_break_down_id")]]['mobile_no']=$row[csf("mobile_no")];
		}
		$details_report .='<table width="3440" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2" align="left">';
		$i=1;
	
				
		if($db_type==0)
		{
			$sql= "SELECT b.id as po_id, b.grouping, max(a.lc_sc_no) as lc_sc_arr_no, group_concat(distinct a.invoice_no) as invoice_no, group_concat(distinct a.item_number_id) as itm_num_id, 
			 
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, a.ex_factory_date  as ex_factory_date, group_concat(distinct  a.lc_sc_no) as lc_sc_no, group_concat(distinct a.delivery_mst_id) as challan_id,  b.shipment_date, b.po_number, (b.po_quantity*c.total_set_qnty) as po_quantity, (b.unit_price/c.total_set_qnty) as unit_price, b.shiping_status, c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, YEAR(c.insert_date) as year, c.style_ref_no, c.style_description, c.set_smv,c.total_set_qnty,
			group_concat(distinct a.shiping_mode) as shiping_mode
			
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c 
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$cbo_company_name' $job_cond $int_ref_cond $str_cond $style_cond $order_cond $job_year_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3) and c.is_deleted=0 and c.status_active=1
			group by 
					b.id, b.grouping, b.shipment_date, b.po_number, b.unit_price, c.id, c.company_name, c.buyer_name, c.style_ref_no, c.style_description, c.set_smv ,a.ex_factory_date
			order by c.buyer_name, b.shipment_date ASC";
		}
		else if($db_type==2)
		{
			 $sql= "SELECT b.id as po_id, b.grouping, max(a.lc_sc_no) as lc_sc_arr_no, 
			LISTAGG(CAST( a.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.invoice_no) as invoice_no,
			LISTAGG(CAST( a.item_number_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.item_number_id) as itm_num_id, 
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
			sum(a.total_carton_qnty) as carton_qnty, a.ex_factory_date as ex_factory_date,  
			LISTAGG(CAST( a.lc_sc_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_no) as lc_sc_no, 
			LISTAGG(CAST( a.delivery_mst_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.delivery_mst_id) as challan_id,
			b.shipment_date, b.po_number, (b.po_quantity*c.total_set_qnty) as po_quantity,(b.unit_price/c.total_set_qnty) as unit_price,b.shiping_status, 
			c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv,c.total_set_qnty,
			LISTAGG(CAST( a.shiping_mode AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.shiping_mode) as shiping_mode 
			
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c 
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$cbo_company_name' $job_cond $int_ref_cond  $str_cond $style_cond $order_cond $job_year_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3) and c.is_deleted=0 and c.status_active=1 
			group by 
					b.id, b.grouping, b.shipment_date, b.po_number, b.unit_price,b.po_quantity,b.shiping_status,c.total_set_qnty,c.id,c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, c.insert_date, c.style_ref_no, c.style_description,c.total_set_qnty, c.set_smv,a.ex_factory_date
			order by c.buyer_name, b.shipment_date ASC";
		}
		
		//echo $sql;
		$sql_result=sql_select($sql);
		//print_r($sql_result);die;
		$po_id_arr = array();
		foreach($sql_result as $row)
		{
			$po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		}
		$poIds = implode(",", $po_id_arr);
		$sql = "SELECT PO_BREAK_DOWN_ID, COLOR_NUMBER_ID from wo_po_color_size_breakdown where po_break_down_id in($poIds)";
		$res = sql_select($sql);
		$gmts_color_arr = array();
		foreach ($res as $val) 
		{
			$gmts_color_arr[$val['PO_BREAK_DOWN_ID']] .=  $color_arr[$val['COLOR_NUMBER_ID']].",";
		}
		// echo "<pre>";print_r($gmts_color_arr);
				
		$poIds_in = where_con_using_array($po_id_arr,0,'po_break_down_id');
		$booking_sql = "SELECT PO_BREAK_DOWN_ID, BOOKING_NO from wo_booking_dtls where booking_type=1 and status_active=1 $poIds_in";
		$booking_res = sql_select($booking_sql);
		$booking_arr = array();
		foreach ($booking_res as $val) 
		{
			$booking_arr[$val['PO_BREAK_DOWN_ID']] .= $val['BOOKING_NO'].",";
		}

		foreach($sql_result as $row)
		{
			
			$costing_per=$costing_per_arr[$row[csf('job_no')]];
			if($costing_per==1) $dzn_qnty=12;
			else if($costing_per==3) $dzn_qnty=12*2;
			else if($costing_per==4) $dzn_qnty=12*3;
			else if($costing_per==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$dzn_qnty=$dzn_qnty*$row[csf('total_set_qnty')];			
			$cm_per_pcs=$tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty;
			
			//$last_ex_factory_date=return_field_value(" max(ex_factory_date) as ex_factory_date","pro_ex_factory_mst","po_break_down_id in(".$row[csf('po_id')].")","ex_factory_date");
			// $ex_fact_date_range=$txt_date_from."*".$txt_date_to."_". 1;
			$ex_fact_date_range=$row[csf("ex_factory_date")]."_". 1;
			$total_exface_qnty=$txt_date_from."*".$txt_date_to."_". 2;
			if($break_id==0) $break_id=$row[csf('po_id')]; else $break_id=$break_id.",".$row[csf('po_id')];
			if($sc_lc_id==0) $sc_lc_id=$row[csf('lc_sc_no')]; else $sc_lc_id=$sc_lc_id.",".$row[csf('lc_sc_no')];
			//$lc_type=return_field_value("is_lc","com_export_invoice_ship_mst","id in(".$row[csf('invoice_no')].")","is_lc");
			$invoce_id_arr=array_unique(explode(",",$row[csf('invoice_no')]));
			$challan_id=array_unique(explode(",",$row[csf("challan_id")]));
			$challan_no=""; $forwarder=""; $vehi_no=""; $mobile_no="";
			
			$diff=($row[csf('shiping_status')]!=3)?datediff("d",$current_date, $row[csf("shipment_date")])-1:datediff("d",$row[csf("ex_factory_date")], $row[csf("shipment_date")])-1;// Count Days in Hand Update By REZA;	

			list($first_button)=explode(',',$print_report_format[$row[csf("company_name")]]);

			
			foreach($challan_id as $val)
			{
				//echo $val;

				$del_company=$challan_mst_arr[$val][$row[csf('po_id')]]['del_company'];

				$fv=$first_button.",".$val.",".$row[csf("company_name")].",".$del_company.",'".$row[csf('ex_factory_date')]."'";
				$challanFunction='<a href="##" onclick="fn_generate_print('.$fv.')">'.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'].'</a>';
				if($challan_no==""){$challan_no=$challanFunction;}else {$challan_no.=', '.$challanFunction;}


				//if($challan_no=="") $challan_no=$challan_mst_arr[$val][$row[csf('po_id')]]['challan']; else $challan_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['challan'];
				if($forwarder=="") $forwarder=$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']]; else $forwarder.=','.$forwarder_arr[$challan_mst_arr[$val][$row[csf('po_id')]]['forwarder']];
				if($vehi_no=="") $vehi_no=$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no']; else $vehi_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['truck_no'];
				if($mobile_no=="") $mobile_no=$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no']; else $mobile_no.=','.$challan_mst_arr[$val][$row[csf('po_id')]]['mobile_no'];
			}

			if ($i%2==0)  
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";	
			$comapny_id=$row[csf("company_name")];
			
			$onclick=" change_color('tr_".$i."','".$bgcolor."')";
			$details_report .='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$i.'">';
			$details_report .='<td width="40" align="center">'.$i.'</td>
								<td width="60" align="center" ><p>'.$row[csf("job_no_prefix_num")].'</p></td>
								<td width="60" align="center" ><p>'.$row[csf("year")].'</p></td>
								<td width="100" align="center" ><p>'.$buyer_arr[$row[csf("buyer_name")]].'</p></td>
								<td width="110" align="center"><p>'.$row[csf("po_number")].'</p></td>
								<td width="100" align="center"><p>'.implode(", ",array_unique(array_filter(explode(",",$booking_arr[$row[csf('po_id')]])))).'</p></td>
								<td width="80" align="center"><p>'.$row[csf("grouping")].'</p></td>
								<td width="120" align="center"><p>'.$challan_no.'</p></td>
								<td width="100" align="center"><p>';
								$inv_id=""; $lc_sc_no=""; $ship_mode="";
								foreach($invoce_id_arr as $invoice_id)
								{
									if($inv_id=="") $inv_id=$invoice_array[$invoice_id]; else $inv_id=$inv_id.",".$invoice_array[$invoice_id];
									/*if($ship_mode=="") $ship_mode=$shipment_mode[$shipping_mode_array[$invoice_id]]; else $ship_mode=$ship_mode.",".$shipment_mode[$shipping_mode_array[$invoice_id]];*/
									$ship_mode_arr=array();
									foreach(explode(',',$row[csf("shiping_mode")]) as $sm){
										$ship_mode_arr[$sm]=$shipment_mode[$sm];
									}
									$ship_mode=implode(',',$ship_mode_arr);
									
									
									if($lc_sc_type_arr[$invoice_id]==1)
									{
										if($lc_sc_no=="") $lc_sc_no=$lc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no=$lc_sc_no.",".$lc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
									else
									{
										if($lc_sc_no=="") $lc_sc_no=$sc_num_arr[$lc_sc_id_array[$invoice_id]]; else $lc_sc_no=$lc_sc_no.",".$sc_num_arr[$lc_sc_id_array[$invoice_id]];
									}
								}
								
			$details_report .=$inv_id.'</p></td>
								<td width="100" align="center"><p>'.$invoice_qty_array[$invoice_id].'</p></td>
								<td width="100" align="center"><p>'.$lc_sc_no.'</p></td>
								<td width="100"><p>'.$row[csf("style_ref_no")].'</p></td>
								<td width="100"><p>'.$row[csf("style_description")].'</p></td>
								<td width="110" align="center"><p>';//$garments_item
								$item_name_arr=explode(",",$row[csf("itm_num_id")]);
								$item_name_arr=array_unique($item_name_arr);
								if(!empty($item_name_arr))
								{
									$p=1;
									foreach($item_name_arr as $item_id)
									{
										if($p!=1) $item_name_all .=",";
										$item_name_all .=$garments_item[$item_id];
										$p++;
									}
								}
								
								$total_ex_fact_qty=$exfact_qty_arr[$row[csf("po_id")]];
								$total_cartoon_qty=$exfact_cartoon_arr[$row[csf("po_id")]];
								$po_quantity=$row[csf("po_quantity")];
								$unit_price=$row[csf("unit_price")];
								$current_ex_Fact_Qty=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]];
								$basic_qnty=($total_ex_fact_qty*$row[csf("set_smv")])/$basic_smv_arr[$row[csf("company_name")]];
								$cm_per_pcs_tot+=($cm_per_pcs*$current_ex_Fact_Qty);
								
			$details_report .=$item_name_all.'</p></td>
								<td width="110" align="left"><p>'.implode(",",array_unique(array_filter(explode(",", $gmts_color_arr[$row[csf('po_id')]])))).'</p></td>
								<td width="80" align="center"><p>'.$row[csf("set_smv")].'</p></td>
								<td width="70" align="center"><p>'.change_date_format($row[csf("shipment_date")]).'</p></td>
								<td width="70" align="center"><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$ex_fact_date_range."','ex_date_popup'".')">'.change_date_format($row[csf('ex_factory_date')]).'</a></td>
								<td width="70" align="center"><p>'.$ship_mode.'</p></td>
								<td width="60" align="center"><p>'.$diff.'</p></td>
								<td width="80" align="right"><p>'. number_format($po_quantity,0,'', '').'</p></td>
								<td width="70" align="right"><p>'. number_format($unit_price,4).'</p></td>
								<td width="100" align="right"><p>'. number_format(($po_quantity*$unit_price),2).'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$ex_fact_date_range."','ex_date_popup'".')">'. number_format($current_ex_Fact_Qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format( $current_ex_fact_value=$current_ex_Fact_Qty*$unit_price,2).'</p></td>
								<td width="80" align="right"><p>'. number_format($row[csf("carton_qnty")],0,'.', '').'</p></td>
								<td width="80" align="right"><p><a href="##" onclick="openmypage_ex_date('.$comapny_id.",'".$row[csf('po_id')]."','".$total_exface_qnty."','ex_date_popup'".')">'.number_format($total_ex_fact_qty,0,'.', '').'</a></p></td>
								<td width="100" align="right"><p>'. number_format($total_ex_fact_value=$total_ex_fact_qty*$unit_price,2).'</p></td>
								<td width="80" align="right"><p>'.number_format($total_cartoon_qty,0,'.', '').'</p></td>
								<td width="100" align="right" title="Current Ex.Qty*SMV"><p>'. number_format($total_sales_minutes=$current_ex_Fact_Qty*$row[csf("set_smv")]).'</p></td>
								
								
								<td width="80" align="right"><p>'.number_format($basic_qnty,0,'','').'</p></td>
								<td width="80" align="right"><p>'. number_format($excess_shortage_qty=$po_quantity-$total_ex_fact_qty,0,'', '').'</p></td>
								<td width="100" align="right"><p>'. number_format($excess_shortage_value=$excess_shortage_qty*$unit_price,2).'</p></td>
								<td align="center" width="80"><p>'. number_format($total_ex_fact_qty_parcent=($total_ex_fact_qty/$po_quantity)*100,0).'</p></td>
								<td width="50" align="right" title="CM per pcs: '.$cm_per_pcs.'">'.number_format($cm_per_pcs*$current_ex_Fact_Qty,2).'</td>
								<td width="100" align="center"><p>'.$forwarder.'</p></td>
								<td width="80" align="center"><p>'.$vehi_no.'</p></td>
								<td width="80" align="center"><p>'.$mobile_no.'</p></td>
								<td width="70" align="center"><p>'.(change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '0000-00-00' || change_date_format($inspection_date_arr[$row[csf('po_id')]]) == '' ? '' : change_date_format(change_date_format($inspection_date_arr[$row[csf('po_id')]]))).'</p></td>
								<td align="center"><p>'.$shipment_status[$row[csf('shiping_status')]].'</p></td>
							</tr>';
							
			$master_data[$row[csf("buyer_name")]]['b_id']=$row[csf("buyer_name")];	
			$master_data[$row[csf("buyer_name")]]['po_qnty'] +=$row[csf("po_quantity")];
			$master_data[$row[csf("buyer_name")]]['po_value'] +=$row[csf("po_quantity")]*$row[csf("unit_price")];
			$master_data[$row[csf("buyer_name")]]['basic_qnty'] +=$basic_qnty;
			$master_data[$row[csf("buyer_name")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]];
			$master_data[$row[csf("buyer_name")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]])*$row[csf("unit_price")];
			$master_data[$row[csf("buyer_name")]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
			$master_data[$row[csf("buyer_name")]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row[csf("unit_price")];
			
			$total_po_qty+=$row[csf("po_quantity")];
			$total_basic_qty+=$basic_qnty;
			$total_po_valu+=$row[csf("po_quantity")]*$row[csf("unit_price")];
			$total_ex_qty+=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]];
			$total_crtn_qty+=$row[csf("carton_qnty")];
			$total_ex_valu=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]])*$row[csf("unit_price")];
			$g_total_ex_qty+=$total_ex_fact_qty;
			$g_total_ex_crtn+=$total_cartoon_qty;
			$g_total_ex_val+=$total_ex_fact_qty*$row[csf("unit_price")];
			$g_sales_minutes+=$current_ex_Fact_Qty*$row[csf("set_smv")];
			$total_eecess_storage_qty+=$excess_shortage_qty;
			$total_eecess_storage_val+=$excess_shortage_value;
			
			$i++;$item_name_all="";
		} 
		
	
		
		$details_report .='
						</table>';
							
		foreach($master_data as $rows)
		{
			$total_po_val+=$rows[po_value];
		}
							
		?>
        <div style="width:3460x;">
            <div style="width:1220px" >
                <table width="1190"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="10" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="10" align="center" class="form_caption"> <strong style="font-size:15px;">Ex-Factory Report</strong></td>
                    </tr>
                     <tr align="center">
                        <td colspan="10" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                    </tr>
                    </table>
                    <table width="1190" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                    <thead>
                        <th width="40" height="34">SL</th>
                        <th width="130">Buyer Name</th>
                        <th width="100">PO Qty.</th>
                        <th width="130">PO Value</th>
                        <th width="100">PO Value(%)</th>
                        <th width="100">Current Ex-Fact. Qty.</th>
                        <th width="130">Current Ex-Fact. Value</th>
                        <th width="100">Total Ex-Fact. Qty.</th>
                        <th width="130">Total Ex-Fact. Value </th>
                        <th width="100">Total Ex-Fact. (Basic Qty)</th>
                        <th >Total Ex-Fact. Value %</th>
                    </thead>
                 </table>
                 <table width="1190" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
                 <?
                 $m=1;
                foreach($master_data as $rows)
                {
                    if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                    else
                    $bgcolor="#FFFFFF";
                     ?>
                  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" >
                        <td width="40" align="center"><? echo $m; ?></td>
                        <td width="130">
                        <p><?
                        echo $buyer_arr[$rows[b_id]];
                        ?></p>
                        </td>
                        <td width="100" align="right"><p><?  $po_quantity=$rows[po_qnty];echo number_format($po_quantity,0); $total_buyer_po_quantity+=$po_quantity; ?></p></td>
                        <td width="130" align="right" ><p  id="value_<? echo $i ; ?>"><? $buyer_po_value=$rows[po_value]; echo number_format($buyer_po_value,2 ,'.', '');  $total_buyer_po_value+=$buyer_po_value; ?></p></td>
                        <td width="100" align="right">
                         <? echo number_format(($buyer_po_value/$total_po_val)*100,2,'.','');$parcentages+=($buyer_po_value/$total_po_val)*100; ?>
                        </td>
                        <td width="100" align="right">
                        <p><?
                         $current_ex_Fact_Qty=$rows[ex_factory_qnty];  echo number_format($current_ex_Fact_Qty,0,'',''); $total_current_ex_Fact_Qty +=$current_ex_Fact_Qty;
                        ?></p>
                        </td>
                        <td width="130" align="right">
                        <p><?
                        $current_ex_fact_value=$rows[ex_factory_value]; echo number_format($current_ex_fact_value,2,'.',''); $total_current_ex_fact_value+=$current_ex_fact_value;
                        ?></p>
                        </td>
                        <td align="right" width="100">
                        <p><?
                         $total_ex_fact_qty=$rows[total_ex_fact_qty]; echo number_format($total_ex_fact_qty,0,'',''); $mt_total_ex_fact_qty+=$total_ex_fact_qty;
                        ?></p>
                        </td>
                        <td align="right" width="130">
                        <p><?
                         $total_ex_fact_value=$rows[total_ex_fact_value];  echo  number_format($total_ex_fact_value,2,'.',''); $mt_total_ex_fact_value+=$total_ex_fact_value;
                        ?></p>
                        </td>
                        <td width="100" align="right">
                        <p><?
                         $buyer_basic_qnty=$rows[basic_qnty];  echo number_format($buyer_basic_qnty,0,'',''); $total_buyer_basic_qnty +=$buyer_basic_qnty;
                        ?></p>
                        </td>
                        <td align="right">
                        <p><?
                        $total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
                        echo number_format($total_ex_fact_value_parcentage,0)
                        ?> %</p>
                        </td>
                    </tr>
                    <?
                    $i++;$m++;
                    $buyer_po_quantity=0;
                    $buyer_po_value=0;
                    $current_ex_Fact_Qty=0;
                    $current_ex_fact_value=0;
                    $total_ex_fact_qty=0;
                    $total_ex_fact_value=0;
                    
                }
                    ?>
                    <input type="hidden" name="total_i" id="total_i" value="<? echo $i; ?>" />
                    <tfoot>
                        <th align="right" colspan="2"><b>Total:</b></th>
                        <th  align="right" id="total_buyer_po_quantity"><? echo number_format($total_buyer_po_quantity,0);  ?></th>
                        <th  align="right" id="value_total_buyer_po_value"><? echo number_format($total_buyer_po_value,2 ,'.', ''); ?> </th>
                        <th align="right" id="parcentages"><? echo ceil($parcentages); ?></th>
                        <th  align="right" id="total_current_ex_Fact_Qty"><? echo number_format($total_current_ex_Fact_Qty,0); ?></th>
                        <th  align="right" id="value_total_current_ex_fact_value"><? echo  number_format($total_current_ex_fact_value,2); ?></th>
                        <th align="right" id="mt_total_ex_fact_qty"><? echo number_format($mt_total_ex_fact_qty,0); ?></th>
                        <th align="right" id="value_mt_total_ex_fact_value"><? echo number_format($mt_total_ex_fact_value,2); ?></th>
                        <th  align="right" id="total_buyer_basic_qnty"><? echo number_format($total_buyer_basic_qnty,0); ?></th>
                        <th align="right"></th>
                    </tfoot>
                </table>
            </div>
            <br />
            <div>
                <table width="3440"  >
                    <tr>
                    <td colspan="31" class="form_caption"><strong style="font-size:16px;">Shipped Out Order Details Report</strong></td>
                    </tr>
                </table>
                <table width="3440" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="40">SL</th>
                        <th width="60">Job</th>
                        <th width="60">Year</th>
                        <th width="100">Buyer Name</th>
                        <th width="110">Order NO</th>
                        <th width="100">Fabric Booking No</th>
                        <th width="80">Internal Ref.</th>
                        <th width="120">Challan NO</th>
                        <th width="100" >Invoice NO</th>
                        <th width="100" >Invoice Qty</th>
                        <th width="100" >LC/SC NO</th>
                        <th width="100">Style Ref. no.</th>
                        <th width="100">Style Description</th>
                        <th width="110">Item Name</th>
                        <th width="110">Gmt. Color</th>
                        <th width="80">Item SMV</th>
                        <th width="70">Shipment Date</th>
                        <th width="70">Ex-Fac. Date</th>
                        <th width="70">Shipping Mode</th>
                        <th width="60">Days in Hand</th>
                        <th width="80">PO Qtny. (pcs)</th>
                        <th width="70">Unit Price</th>
                        <th width="100">PO Value</th>
                        <th width="80">Current Ex-Fact. Qty (pcs)</th>
                        <th width="100">Current Ex-Fact. Value</th>
                        <th width="80">Current cartoon Qty</th>
                        <th width="80">Total Ex-Fact. Qty.</th>
                        <th width="100">Total Ex-Fact. Value</th>
                        <th width="80">Total cartoon Qty</th>
                        <th width="100">Sales Minute</th>
                        <th width="80">Total Ex-Fact. (Basic Qty)</th>
                        <th width="80">Excess/ Shortage Qty</th>
                        <th width="100">Excess/ Shortage Value</th>
                        <th width="80">Total Ex-Fact. Qty. %</th>
                        <th width="50">Sales CM</th>
                        <th width="100">C & F Name</th>
                        <th width="80">Vehicle No</th>
                        <th width="80">Car Mobile No</th>
                        <th width="70">Inspaction Date</th>
                        <th>Ex-Fact Status</th>
                    </thead>
                </table>
            <div style="width:3460px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
            <? echo $details_report; ?>
            <table width="3440" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer" align="left">
                <tfoot>
                    <tr>
                    	<th width="40">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100" >&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60" align="right"><strong>Total</strong></th>
                        <th width="80" id="total_po_qty" align="right"><p><? echo  number_format($total_po_qty,0);?></p></th>
                        <th width="70" align="right">&nbsp;</th>
                        <th width="100" align="right" id="value_total_po_valu"><p><? echo  number_format($total_po_valu,2); ?></p></th>
                        <th width="80" align="right" id="total_ex_qty"><p><? echo number_format($total_ex_qty,0); ?></p></th>
                        <th width="100" align="right" id="value_total_ex_valu"><p><? echo number_format($total_ex_valu,2);?></p></th>
                        <th width="80" align="right" id="total_crtn_qty"><? echo number_format($total_crtn_qty,0); ?></th>
                        <th width="80" align="right" id="g_total_ex_qty"><p><? echo number_format($g_total_ex_qty,0);?><p></th>
                        <th width="100" align="right" id="value_g_total_ex_val"><p><? echo number_format($g_total_ex_val,2);?></p></th>
                        <th width="80" align="right" id="g_total_ex_crtn"><p><? echo number_format($g_total_ex_crtn,0);?></p></th>
                        <th width="100" align="right" id="value_sales_minutes"><p><? echo number_format($g_sales_minutes);?></p></th>
                        
                        <th width="80" align="right" id="total_basic_qty"><p><? echo number_format($total_basic_qty,0); ?></p></th>
                        <th width="80" align="right" id="total_eecess_storage_qty"><p><? echo number_format($total_eecess_storage_qty,0);?><p></th>
                        <th width="100" align="right" id="value_total_eecess_storage_val"><p><? echo number_format($total_eecess_storage_val,0);?></p></th>
                        <th width="80">&nbsp;</th>
                        <th width="50" align="right" id="value_cm_per_pcs_tot"><p><? echo number_format($cm_per_pcs_tot,2);?></p></th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
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
	echo "$total_data####$filename####$reportType";
	exit();
}

if($action=="ex_date_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_factory_date=str_replace("'","",$ex_factory_date);
	$ex_factory_date_ref=explode("_",$ex_factory_date);
	$exfact_date=explode("*",$ex_factory_date_ref[0]);
	//echo $ex_factory_date."***".$company_id."***".$order_id;
	$country_arr=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Date Details</strong></div><br />
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Date</th>
                        <th width="100">Challan</th>
                        <th width="100">Country</th>
                        <th width="100">Delv. Qty</th>
                        <th width="">Return Qty</th>
                     </tr>   
                </thead>
                <tbody>	 	
					<?
						$sql_res=sql_select("select b.po_break_down_id as po_id, 
						
						sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty 
						from  pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");
						$ex_factory_qty_arr=array();
						foreach($sql_res as $row)
						{
						
							$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('return_qnty')];
						}
						
						$i=1;
						if($ex_factory_date_ref[1]==2)
						{ 
							$sql_qnty="Select po_break_down_id,ex_factory_date,challan_no,country_id, 
							sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
							sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_ret_qnty
							
							 from pro_ex_factory_mst where po_break_down_id=$order_id and status_active=1 and is_deleted=0 group by po_break_down_id,ex_factory_date,challan_no,country_id order by ex_factory_date ";
						}
						else
						{
							 $sql_qnty="Select po_break_down_id,ex_factory_date,challan_no,country_id, 
							
							sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty
							
							from pro_ex_factory_mst where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and ex_factory_date='$ex_factory_date_ref[0]' group by po_break_down_id,ex_factory_date,challan_no,country_id order by ex_factory_date ";
							
							/*$sql_qnty="Select c.ex_factory_date, sum(c.ex_factory_qnty) as ex_factory_qnty,c.challan_no,c.country_id 
							from wo_po_details_master a, wo_po_break_down b,  pro_ex_factory_mst c
							where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name=$company_id and c.po_break_down_id=$order_id and c.status_active=1 and c.is_deleted=0 and c.ex_factory_date between  '$exfact_date[0]' and '$exfact_date[1]' 
							group by c.ex_factory_date,c.challan_no,c.country_id order by c.ex_factory_date ";*/
						}
						//echo $sql_qnty;
						$sql_dtls=sql_select($sql_qnty);
						foreach($sql_dtls as $row_real)
						{ 
							 if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF"; 
							 if($ex_factory_date_ref[1]==2)
							 {
								 $return_qty=$row_real[csf("ex_factory_ret_qnty")];
							 }
							 else
							 {
								$return_qty=$ex_factory_qty_arr[$row_real[csf("po_break_down_id")]]['return_qty']; 
							 }
							  
							
							 ?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td><? echo $i; ?></td> 
									<td  align="center"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                                    <td ><? echo $row_real[csf("challan_no")]; ?></td>
                                    <td ><? echo $country_arr[$row_real[csf("country_id")]]; ?></td>
									<td width="100" align="right"><? echo number_format($row_real[csf("ex_factory_qnty")]-$return_qty,2); ?>&nbsp;</td>
                                    <td width="" align="right"><? echo number_format($return_qty,2); ?>&nbsp;</td>
								</tr>
							<? 
							$total_ex_qnty+=$row_real[csf("ex_factory_qnty")];
							$total_return_ex_qnty+=$return_qty;
							$i++;
						}
                    ?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="4" align="right"><strong>Total :</strong></th>
                        <th align="right"><? echo number_format($total_ex_qnty,2); ?></th>
                        <th align="right"><? echo number_format($total_return_ex_qnty,2); ?> </th>
                    </tr>
                    <tr>
                    	<th colspan="4" align="right"><strong>Total Balance:</strong></th>
                        <th align="right" colspan="2"><? echo number_format($total_ex_qnty-$total_return_ex_qnty,2); ?></th>
                        
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>    
    <?	
}
disconnect($con);
?>
