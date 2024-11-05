<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action==='load_drop_down_supplier')
{
	echo create_drop_down( "cbo_supplier_id", 150,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id=b.supplier_id and a.tag_company='$data'  and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --','','',0);
	exit();
}

if($action==='report_generate')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$cbo_supplier_id  = str_replace("'","",$cbo_supplier_id);
	$txt_pi_no        = trim(str_replace("'","",$txt_pi_no));
	// $p=1;  $piIds ="";
	// $txt_pi_arr=explode(",",$txt_pi_no );
	// foreach($txt_pi_arr as $val){
	// 	if($p==1){

	// 		$piIds .="'".$val."'";
	// 		$p++;
	// 		}else{
	// 			$piIds .=",'".$val."'";
	// 		}
	// }
	// echo $piIds ; die;

	$txt_pi_id        = str_replace("'","",$txt_pi_id);
	$txt_btb_lc_no    = trim(str_replace("'","",$txt_btb_lc_no));
	$txt_btb_lc_id    = str_replace("'","",$txt_btb_lc_id);
	$pi_pending_type  = str_replace("'","",$pi_pending_type);
	$txt_date_from    = str_replace("'","",$txt_date_from);
	$txt_date_to      = str_replace("'","",$txt_date_to);

	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1", 'id','company_name');
	$buyer_arr   = return_library_array("select id,short_name from lib_buyer where status_active=1", 'id','short_name');
	$supplier_arr= return_library_array("select id,supplier_name from lib_supplier where status_active=1", 'id','supplier_name');

	$pi_no_cond='';
	// if ( $piIds != '') $pi_no_cond=" and a.pi_number in($piIds )";
	if ($txt_pi_id != '') $pi_no_cond.=" and a.id in($txt_pi_id)";

	$btb_lc_no_cond='';
	// if ($txt_btb_lc_no != '') $btb_lc_no_cond=" and d.lc_number in($txt_btb_lc_no)";
	if ($txt_btb_lc_id != '') $btb_lc_no_cond.=" and d.id in($txt_btb_lc_id)";	

	$company_cond=$buyer_cond=$supplier_cond='';
	if ($cbo_company_name != 0) $company_cond=" and a.importer_id=$cbo_company_name";
	if ($cbo_supplier_id != 0) $supplier_cond=" and a.supplier_id=$cbo_supplier_id";

	$pi_date_cond='';
	if ($txt_date_from != '' && $txt_date_to != '')
	{
		if ($db_type == 0)
		{
			$txt_date_from = date('Y-m-d', strtotime($txt_date_from));
			$txt_date_to   = date('Y-m-d', strtotime($txt_date_to));
			$pi_date_cond=" and a.pi_date between '$txt_date_from' and '$txt_date_to'";
		} 
		else
		{
			$txt_date_from = date('d-M-Y', strtotime($txt_date_from));
			$txt_date_to = date('d-M-Y', strtotime($txt_date_to));
			$pi_date_cond=" and a.pi_date between '$txt_date_from' and '$txt_date_to'";
		}
	}

	if ($pi_pending_type==1)  // All
	{
		$sql="SELECT a.id as PI_ID, a.importer_id as IMPORTER_ID, a.item_category_id as ITEM_CATEGORY_ID, a.supplier_id as SUPPLIER_ID, a.pi_number as PI_NUMBER, a.pi_date as PI_DATE, a.approved as APPROVED, a.approved_date as APPROVED_DATE, a.net_total_amount as PI_VALUE, d.id as BTB_ID, d.btb_system_id as BTB_SYSTEM_ID, d.application_date as BTB_LC_OPEN_DATE, d.lc_number as BTB_LC_NUMBER, d.lc_value as BTB_VALUE, a.pi_basis_id as PI_BASIS_ID, a.INTERNAL_FILE_NO
		from com_pi_master_details a 
		left join com_btb_lc_pi c on a.id=c.pi_id and c.status_active=1 and c.is_deleted=0 
		left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 and d.is_deleted=0
		where a.net_total_amount>0 and a.status_active=1 and a.is_deleted=0 $btb_lc_no_cond $company_cond $supplier_cond $pi_no_cond $pi_date_cond
		order by a.id desc";

		// $sql="SELECT a.id as PI_ID, a.importer_id as IMPORTER_ID, a.item_category_id as ITEM_CATEGORY_ID, a.supplier_id as SUPPLIER_ID, a.pi_number as PI_NUMBER, a.pi_date as PI_DATE, a.approved as APPROVED, a.approved_date as APPROVED_DATE, a.net_total_amount as PI_VALUE, d.id as BTB_ID, d.btb_system_id as BTB_SYSTEM_ID, d.application_date as BTB_LC_OPEN_DATE, d.lc_number as BTB_LC_NUMBER, d.lc_value as BTB_VALUE, a.pi_basis_id as PI_BASIS_ID, f.INTERNAL_FILE_NO
		// from com_pi_master_details a 
		// left join com_btb_lc_pi c on a.id=c.pi_id and c.status_active=1 and c.is_deleted=0 
		// left join com_btb_lc_master_details d on c.com_btb_lc_master_details_id=d.id and d.status_active=1 and d.is_deleted=0
		// left join com_btb_export_lc_attachment e on d.id=e.IMPORT_MST_ID and e.status_active=1 and e.is_deleted=0
		// left join com_sales_contract f on f.id=e.lc_sc_id and f.status_active=1 and f.is_deleted=0
		// where a.net_total_amount>0 and a.status_active=1 and a.is_deleted=0 $company_cond $supplier_cond $pi_no_cond $pi_date_cond
		// order by a.id desc";

		// echo $sql;


		$sql_res=sql_select($sql);
		$pi_id_arr=array();
		$btb_id_arr=array();
		$btb_ids='';
		foreach ($sql_res as $val)
		{
			$pi_id_arr[$val['PI_ID']]['IMPORTER_ID']=$val['IMPORTER_ID'];
			$pi_id_arr[$val['PI_ID']]['ITEM_CATEGORY_ID']=$val['ITEM_CATEGORY_ID'];
			$pi_id_arr[$val['PI_ID']]['SUPPLIER_ID']=$val['SUPPLIER_ID'];
			$pi_id_arr[$val['PI_ID']]['PI_NUMBER']=$val['PI_NUMBER'];
			$pi_id_arr[$val['PI_ID']]['INTERNAL_FILE_NO']=$val['INTERNAL_FILE_NO'];
			$pi_id_arr[$val['PI_ID']]['PI_DATE']=$val['PI_DATE'];
			$pi_id_arr[$val['PI_ID']]['APPROVED']=$val['APPROVED'];
			$pi_id_arr[$val['PI_ID']]['APPROVED_DATE']=$val['APPROVED_DATE'];
			$pi_id_arr[$val['PI_ID']]['PI_VALUE']=$val['PI_VALUE'];	
			$pi_id_arr[$val['PI_ID']]['BTB_LC_OPEN_DATE']=$val['BTB_LC_OPEN_DATE'];
			$pi_id_arr[$val['PI_ID']]['BTB_LC_NUMBER']=$val['BTB_LC_NUMBER'];
			$pi_id_arr[$val['PI_ID']]['BTB_VALUE']=$val['BTB_VALUE'];
			$pi_id_arr[$val['PI_ID']]['BTB_SYSTEM_ID']=$val['BTB_SYSTEM_ID'];
			$btb_id_arr[$val['BTB_ID']]=$val['PI_ID'];
            $pi_id_arr[$val['PI_ID']]['BASIS']=$pi_basis[$val['PI_BASIS_ID']];

            $pi_ids.=$val['PI_ID'].',';
			if ($val['BTB_ID'] !='') {
				$btb_ids.=$val['BTB_ID'].',';				
			}
		}
		unset($sql_res);

		if ($pi_ids != '')
	    {
	        $pi_ids_arr = array_flip(array_flip(explode(',', rtrim($pi_ids,','))));
	        $pi_id_cond = '';

	        if ($db_type==2 && count($pi_ids_arr)>1000)
	        {
	            $pi_id_cond = ' and (';
	            $piIdArr = array_chunk($pi_ids_arr,999);
	            foreach($piIdArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $pi_id_cond .= " and booking_id in($ids) or ";
	            }
	            $pi_id_cond = rtrim($pi_id_cond,'or ');
	            $pi_id_cond .= ')';
	        }
	        else
	        {
	            $pi_ids = implode(',', $pi_ids_arr);
	            $pi_id_cond=" and booking_id in ($pi_ids)";
	        }

	        // Approval Comment Part
	        $approval_arr=array();			
			$sql_approval=sql_select("SELECT max(id) as ID, booking_id as PI_ID, approval_cause as APPROVAL_CAUSE from fabric_booking_approval_cause where entry_form=27 and page_id=867 and approval_type=0 $pi_id_cond group by booking_id, approval_cause");
			foreach ($sql_approval as $val) {
				$approval_arr[$val['PI_ID']]['APPROVAL_CAUSE']=$val['APPROVAL_CAUSE'];
			}
			unset($sql_approval);

			$un_approval_arr=array();
			$sql_unapproval=sql_select("SELECT max(id) as ID, booking_id as PI_ID, not_approval_cause as NOT_APPROVAL_CAUSE from fabric_booking_approval_cause where entry_form=27 and page_id=867 and approval_type=1 $pi_id_cond group by booking_id, NOT_APPROVAL_CAUSE");
			foreach ($sql_unapproval as $val) {
				$un_approval_arr[$val['PI_ID']]['NOT_APPROVAL_CAUSE']=$val['NOT_APPROVAL_CAUSE'];
			}
			unset($sql_unapproval);
			//echo '<pre>';print_r($un_approval_arr);
	    }

	    if ($btb_ids != '')
	    {
	        $btb_ids_arr = array_flip(array_flip(explode(',', rtrim($btb_ids,','))));
	        $btb_id_cond = '';

	        if($db_type==2 && count($btb_ids_arr)>1000)
	        {
	            $btb_id_cond = ' and (';
	            $btbIdArr = array_chunk($btb_ids_arr,999);
	            foreach($btbIdArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $btb_id_cond .= " b.import_mst_id in($ids) or ";
	            }
	            $btb_id_cond = rtrim($pi_id_cond,'or ');
	            $btb_id_cond .= ')';
	        }
	        else
	        {
	            $btb_ids = implode(',', $btb_ids_arr);
	            $btb_id_cond=" and b.import_mst_id in ($btb_ids)";
	        }

	        // LC SC Part
	        $sql_lc_sc="SELECT a.export_lc_no as LC_SC_NO, a.last_shipment_date as LC_SC_SHIP_DATE, b.lc_sc_id as LC_SC_ID, b.import_mst_id as BTB_ID 
			from com_export_lc a, com_btb_export_lc_attachment b 
			where a.id=b.lc_sc_id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $btb_id_cond
			union all
			SELECT a.contract_no as LC_SC_NO, a.last_shipment_date as LC_SC_SHIP_DATE, b.lc_sc_id as LC_SC_ID, b.import_mst_id as BTB_ID  
			from com_sales_contract a, com_btb_export_lc_attachment b 
			where a.id=b.lc_sc_id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $btb_id_cond";
			$sql_lc_sc_res=sql_select($sql_lc_sc);
			$ls_sc_arr=array();
			foreach ($sql_lc_sc_res as $val)
			{
				$ls_sc_arr[$btb_id_arr[$val['BTB_ID']]]['LC_SC_NO']=$val['LC_SC_NO'];
				$ls_sc_arr[$btb_id_arr[$val['BTB_ID']]]['LC_SC_SHIP_DATE']=$val['LC_SC_SHIP_DATE'];
			}
			unset($sql_lc_sc_res);
	    }

		// PI File
		$pi_data_file=sql_select("SELECT image_location as IMAGE_LOCATION, master_tble_id as PI_ID from common_photo_library where form_name='proforma_invoice' and is_deleted=0 and file_type=2");
		$pi_file_arr=array();
		foreach($pi_data_file as $row)
		{
			$pi_file_arr[$row['PI_ID']]['FILE']=$row['IMAGE_LOCATION'];
		}
		unset($pi_data_file);

		// BTB File
		$btb_data_file=sql_select("SELECT image_location as IMAGE_LOCATION, master_tble_id as BTB_SYSTEM_ID from common_photo_library where form_name='BTBMargin LC' and is_deleted=0 and file_type=2");
		$btb_file_arr=array();
		foreach($btb_data_file as $row)
		{
			$btb_file_arr[$row['BTB_SYSTEM_ID']]['FILE']=$row['IMAGE_LOCATION'];
		}
		unset($btb_data_file);
	}
	else if ($pi_pending_type==2)  // Done
	{
		$sql="SELECT a.id as PI_ID, a.importer_id as IMPORTER_ID, a.item_category_id as ITEM_CATEGORY_ID, a.supplier_id as SUPPLIER_ID, a.pi_number as PI_NUMBER, a.pi_date as PI_DATE, a.approved as APPROVED, a.approved_date as APPROVED_DATE, a.net_total_amount as PI_VALUE, d.id as BTB_ID, d.btb_system_id as BTB_SYSTEM_ID, d.application_date as BTB_LC_OPEN_DATE, d.lc_number as BTB_LC_NUMBER, d.lc_value as BTB_VALUE, a.pi_basis_id as PI_BASIS_ID
		from com_pi_master_details a, com_btb_lc_pi c, com_btb_lc_master_details d
		where a.id=c.pi_id and c.com_btb_lc_master_details_id=d.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond $supplier_cond $pi_no_cond $pi_date_cond
		order by a.id desc";
		$sql_res=sql_select($sql);
		$pi_id_arr=array();
		$btb_id_arr=array();
		foreach ($sql_res as $val) 
		{
			$pi_id_arr[$val['PI_ID']]['IMPORTER_ID']=$val['IMPORTER_ID'];
			$pi_id_arr[$val['PI_ID']]['ITEM_CATEGORY_ID']=$val['ITEM_CATEGORY_ID'];
			$pi_id_arr[$val['PI_ID']]['SUPPLIER_ID']=$val['SUPPLIER_ID'];
			$pi_id_arr[$val['PI_ID']]['PI_NUMBER']=$val['PI_NUMBER'];
			$pi_id_arr[$val['PI_ID']]['PI_DATE']=$val['PI_DATE'];
			$pi_id_arr[$val['PI_ID']]['APPROVED']=$val['APPROVED'];
			$pi_id_arr[$val['PI_ID']]['APPROVED_DATE']=$val['APPROVED_DATE'];
			$pi_id_arr[$val['PI_ID']]['PI_VALUE']=$val['PI_VALUE'];			
			$pi_id_arr[$val['PI_ID']]['BTB_LC_OPEN_DATE']=$val['BTB_LC_OPEN_DATE'];
			$pi_id_arr[$val['PI_ID']]['BTB_LC_NUMBER']=$val['BTB_LC_NUMBER'];
			$pi_id_arr[$val['PI_ID']]['BTB_VALUE']=$val['BTB_VALUE'];
			$pi_id_arr[$val['PI_ID']]['BTB_SYSTEM_ID']=$val['BTB_SYSTEM_ID'];
			$btb_id_arr[$val['BTB_ID']]=$val['PI_ID'];
            $pi_id_arr[$val['PI_ID']]['BASIS']=$pi_basis[$val['PI_BASIS_ID']];

            $btb_ids.=$val['BTB_ID'].',';
			$pi_ids.=$val['PI_ID'].',';
		}
		unset($sql_res);

		
		// Approval Comment Part
		if ($pi_ids !='')
		{
			$pi_ids_arr = array_flip(array_flip(explode(',', rtrim($pi_ids,','))));
	        $pi_id_cond = '';

	        if($db_type==2 && count($pi_ids_arr)>1000)
	        {
	            $pi_id_cond = ' and (';
	            $piIdArr = array_chunk($pi_ids_arr,999);
	            foreach($piIdArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $pi_id_cond .= " and booking_id in($ids) or ";
	            }
	            $pi_id_cond = rtrim($pi_id_cond,'or ');
	            $pi_id_cond .= ')';
	        }
	        else
	        {
	            $pi_ids = implode(',', $pi_ids_arr);
	            $pi_id_cond=" and booking_id in ($pi_ids)";
	        }
	        
			$approval_arr=array();
			$sql_approval=sql_select("SELECT max(id) as ID, booking_id as PI_ID, approval_cause as APPROVAL_CAUSE from fabric_booking_approval_cause where entry_form=27 and page_id=867 and approval_type=0 $pi_id_cond group by booking_id, approval_cause");
			foreach ($sql_approval as $val) {
				$approval_arr[$val['PI_ID']]['APPROVAL_CAUSE']=$val['APPROVAL_CAUSE'];
			}
			unset($sql_approval);

			$un_approval_arr=array();
			$sql_unapproval=sql_select("SELECT max(id) as ID, booking_id as PI_ID, not_approval_cause as NOT_APPROVAL_CAUSE from fabric_booking_approval_cause where entry_form=27 and page_id=867 and approval_type=1 $pi_id_cond group by booking_id, NOT_APPROVAL_CAUSE");
			foreach ($sql_unapproval as $val) {
				$un_approval_arr[$val['PI_ID']]['NOT_APPROVAL_CAUSE']=$val['NOT_APPROVAL_CAUSE'];
			}
			unset($sql_unapproval);
			//echo '<pre>';print_r($un_approval_arr);	
		}


		// LC SC Part
		if ($btb_ids !='')
		{
			$btb_ids_arr = array_flip(array_flip(explode(',', rtrim($btb_ids,','))));
	        $btb_id_cond = '';

	        if($db_type==2 && count($btb_ids_arr)>1000)
	        {
	            $btb_id_cond = ' and (';
	            $btbIdArr = array_chunk($btb_ids_arr,999);
	            foreach($btbIdArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $btb_id_cond .= " b.import_mst_id in($ids) or ";
	            }
	            $btb_id_cond = rtrim($pi_id_cond,'or ');
	            $btb_id_cond .= ')';
	        }
	        else
	        {
	            $btb_ids = implode(',', $btb_ids_arr);
	            $btb_id_cond=" and b.import_mst_id in ($btb_ids)";
	        }


			$sql_lc_sc="SELECT a.export_lc_no as LC_SC_NO, a.last_shipment_date as LC_SC_SHIP_DATE, b.lc_sc_id as LC_SC_ID, b.import_mst_id as BTB_ID 
			from com_export_lc a, com_btb_export_lc_attachment b 
			where a.id=b.lc_sc_id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $btb_id_cond
			union all
			SELECT a.contract_no as LC_SC_NO, a.last_shipment_date as LC_SC_SHIP_DATE, b.lc_sc_id as LC_SC_ID, b.import_mst_id as BTB_ID  
			from com_sales_contract a, com_btb_export_lc_attachment b 
			where a.id=b.lc_sc_id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $btb_id_cond";
			$sql_lc_sc_res=sql_select($sql_lc_sc);
			$ls_sc_arr=array();
			foreach ($sql_lc_sc_res as $val) 
			{
				$ls_sc_arr[$btb_id_arr[$val['BTB_ID']]]['LC_SC_NO']=$val['LC_SC_NO'];
				$ls_sc_arr[$btb_id_arr[$val['BTB_ID']]]['LC_SC_SHIP_DATE']=$val['LC_SC_SHIP_DATE'];
			}
			unset($sql_lc_sc_res);
		}

		// PI File
		$pi_data_file=sql_select("SELECT image_location as IMAGE_LOCATION, master_tble_id as PI_ID from common_photo_library where form_name='proforma_invoice' and is_deleted=0 and file_type=2");
		$pi_file_arr=array();
		foreach($pi_data_file as $row)
		{
			$pi_file_arr[$row['PI_ID']]['FILE']=$row['IMAGE_LOCATION'];
		}
		unset($pi_data_file);

		// BTB File
		$btb_data_file=sql_select("SELECT image_location as IMAGE_LOCATION, master_tble_id as BTB_SYSTEM_ID from common_photo_library where form_name='BTBMargin LC' and is_deleted=0 and file_type=2");
		$btb_file_arr=array();
		foreach($btb_data_file as $row)
		{
			$btb_file_arr[$row['BTB_SYSTEM_ID']]['FILE']=$row['IMAGE_LOCATION'];
		}
		unset($btb_data_file);
	}
	else  // Pending
	{
		$sql_pi=sql_select("SELECT a.id as PI_ID
		from com_pi_master_details a, com_btb_lc_pi c, com_btb_lc_master_details d
		where a.id=c.pi_id and c.com_btb_lc_master_details_id=d.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond $supplier_cond $pi_no_cond $pi_date_cond
		group by a.id");
		foreach ($sql_pi as $val) {
			$not_in_pi_ids.=$val['PI_ID'].',';
		}
		unset($sql_pi);

		if ($not_in_pi_ids != '')
		{
			$notin_piIds_arr = array_flip(array_flip(explode(',', rtrim($not_in_pi_ids,','))));
	        $not_in_pi_cond = '';

	        if($db_type==2 && count($notin_piIds_arr)>1000)
	        {
	            $not_in_pi_cond = ' and (';
	            $notInPiIdArr = array_chunk($notin_piIds_arr,999);
	            foreach($notInPiIdArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $not_in_pi_cond .= " a.id not in($ids) and ";
	            }
	            $not_in_pi_cond = rtrim($pi_id_cond,'and ');
	            $not_in_pi_cond .= ')';
	        }
	        else
	        {
	            $notInPi_ids = implode(',', $notin_piIds_arr);
	            $not_in_pi_cond=" and a.id not in ($notInPi_ids)";
	        }
	    }
		//echo $not_in_pi_cond;die;

		$sql="SELECT a.id as PI_ID, a.IMPORTER_ID as IMPORTER_ID, a.item_category_id as ITEM_CATEGORY_ID, a.SUPPLIER_ID as SUPPLIER_ID, a.pi_number as PI_NUMBER, a.PI_DATE as PI_DATE, a.approved as APPROVED, a.APPROVED_DATE as APPROVED_DATE, a.net_total_amount as PI_VALUE, a.pi_basis_id as PI_BASIS_ID
		from com_pi_master_details a, com_pi_item_details b
		where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $supplier_cond $pi_no_cond $pi_date_cond $not_in_pi_cond
		order by a.id desc";

		$sql_res=sql_select($sql);
		$pi_id_arr=array();
		foreach ($sql_res as $val)
		{
			$pi_id_arr[$val['PI_ID']]['IMPORTER_ID']=$val['IMPORTER_ID'];
			$pi_id_arr[$val['PI_ID']]['ITEM_CATEGORY_ID']=$val['ITEM_CATEGORY_ID'];
			$pi_id_arr[$val['PI_ID']]['SUPPLIER_ID']=$val['SUPPLIER_ID'];
			$pi_id_arr[$val['PI_ID']]['PI_NUMBER']=$val['PI_NUMBER'];
			$pi_id_arr[$val['PI_ID']]['PI_DATE']=$val['PI_DATE'];
			$pi_id_arr[$val['PI_ID']]['APPROVED']=$val['APPROVED'];
			$pi_id_arr[$val['PI_ID']]['APPROVED_DATE']=$val['APPROVED_DATE'];
			$pi_id_arr[$val['PI_ID']]['PI_VALUE']=$val['PI_VALUE'];
			$pi_id_arr[$val['PI_ID']]['BASIS']=$pi_basis[$val['PI_BASIS_ID']];
			$pi_ids.=$val['PI_ID'].',';
		}
		unset($sql_res);


		// Approval Comment Part
		if ($pi_ids !='')
		{
			$pi_ids_arr = array_flip(array_flip(explode(',', rtrim($pi_ids,','))));
	        $pi_id_cond = '';

	        if($db_type==2 && count($pi_ids_arr)>1000)
	        {
	            $pi_id_cond = ' and (';
	            $piIdArr = array_chunk($pi_ids_arr,999);
	            foreach($piIdArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $pi_id_cond .= " and booking_id in($ids) or ";
	            }
	            $pi_id_cond = rtrim($pi_id_cond,'or ');
	            $pi_id_cond .= ')';
	        }
	        else
	        {
	            $pi_ids = implode(',', $pi_ids_arr);
	            $pi_id_cond=" and booking_id in ($pi_ids)";
	        }

			$approval_arr=array();
			$sql_approval=sql_select("SELECT max(id) as ID, booking_id as PI_ID, approval_cause as APPROVAL_CAUSE from fabric_booking_approval_cause where entry_form=27 and page_id=867 and approval_type=0 $pi_id_cond group by booking_id, approval_cause");
			foreach ($sql_approval as $val) {
				$approval_arr[$val['PI_ID']]['APPROVAL_CAUSE']=$val['APPROVAL_CAUSE'];
			}
			unset($sql_approval);

			$un_approval_arr=array();
			$sql_unapproval=sql_select("SELECT max(id) as ID, booking_id as PI_ID, not_approval_cause as NOT_APPROVAL_CAUSE from fabric_booking_approval_cause where entry_form=27 and page_id=867 and approval_type=1 $pi_id_cond group by booking_id, NOT_APPROVAL_CAUSE");
			foreach ($sql_unapproval as $val) {
				$un_approval_arr[$val['PI_ID']]['NOT_APPROVAL_CAUSE']=$val['NOT_APPROVAL_CAUSE'];
			}
			unset($sql_unapproval);
			//echo '<pre>';print_r($un_approval_arr);	
		}

		// PI File
		$pi_data_file=sql_select("SELECT image_location as IMAGE_LOCATION, master_tble_id as PI_ID from common_photo_library where form_name='proforma_invoice' and is_deleted=0 and file_type=2");
		$pi_file_arr=array();
		foreach($pi_data_file as $row)
		{
			$pi_file_arr[$row['PI_ID']]['FILE']=$row['IMAGE_LOCATION'];
		}
		unset($pi_data_file);
	}	

	$width=1990;
	ob_start();
	?>
	<div width="<?= $width; ?>">
		<table class="rpt_table" border="1" rules="all" width="<?= $width; ?>" cellpadding="0" cellspacing="0" style="margin-left: 2px;">
			<thead>
				<tr>
					<th width="100"></th>
					<th width="130">Factory Name</th>
	                <th width="130">Supplier Name</th>
	                <th width="110">Item Category</th>
					<th width="120">PI No</th>
					<th width="120">File No</th>
                    <th width="110">PI Basis</th>
	                <th width="80">PI Date</th>
	                <th width="80">Total PI Value</th>
	                <th width="100">Approval Status</th>
	                <th width="130">Approval Date & Time</th>
	                <th width="130">Comments</th>
	                <th width="120">SC/LC No</th>
	                <th width="80">BTB LC Open Date</th>
	                <th width="120">BTB LC No</th>
	                <th width="80">BTB LC Value</th>
	                <th width="100">LC Ship Date</th>
	                <th width="80">PI Attach</th>
	                <th width="80">BTB Attach</th>
				</tr>
			</thead>
		</table>

		<div style="width:<?= $width+20; ?>px; overflow-y:scroll; max-height:350px; margin-left: 2px;" id="scroll_body">
		    <table class="rpt_table" border="1" rules="all" width="<?= $width; ?>" cellpadding="0" cellspacing="0" id="table_body" align="left">
		        <tbody>
		        	<?
		        	$i=1;
		        	foreach ($pi_id_arr as $pi_id => $row) 
		        	{
		        		if ($i%2==0) $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";

		        		if ($row['APPROVED']==1)
		        		{
		        			$app_unapp_msg='Approved';
		        			$app_unapp_date=$row['APPROVED_DATE'];
		        			$app_unapp_comments=$approval_arr[$pi_id]['APPROVAL_CAUSE'];
		        		}
		        		else
		        		{
		        			$app_unapp_msg='Un-approved';
		        			$app_unapp_date='';
		        			$app_unapp_comments=$un_approval_arr[$pi_id]['NOT_APPROVAL_CAUSE'];
		        		}		
		        		?>
			        	<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>" style="text-decoration:none; cursor:pointer">
				        	<td width="100" align="center"><input type="button" class="image_uploader" id="btbfileno_<? echo $i;?>" style="width:80px" value="View Details" onClick="openmypage_piItemmDescription('<?= $row['IMPORTER_ID']; ?>','<?= $pi_id; ?>','<?= $row['ITEM_CATEGORY_ID']; ?>','<?= $ls_sc_arr[$pi_id]['LC_SC_NO']; ?>','pi_item_description')"/></td>
							<td width="130"><p><?= $company_arr[$row['IMPORTER_ID']]; ?></p></td>
			                <td width="130"><p><?= $supplier_arr[$row['SUPPLIER_ID']]; ?></p></td>
			                <td width="110"><p><?= $item_category[$row['ITEM_CATEGORY_ID']]; ?></p></td>
							<td width="120"><p><?= $row['PI_NUMBER']; ?></p></td>
							<td width="120"><p><?= $row['INTERNAL_FILE_NO']; ?></p></td>
							<td width="110"><p><?= $row['BASIS']; ?></p></td>
			                <td width="80" align="center"><p><?= change_date_format($row['PI_DATE']); ?></p></td>
			                <td width="80" align="right"><p><?= number_format($row['PI_VALUE'],2); ?></p></td>
			                <td width="100" align="center"><p><?= $app_unapp_msg; ?></p></td>
			                <td width="130" align="center"><p><?= $app_unapp_date; ?></p></td>
			                <td width="130"><p><?= $app_unapp_comments; ?></p></td>
			                <td width="120"><p><?= $ls_sc_arr[$pi_id]['LC_SC_NO']; ?></p></td>
			                <td width="80" align="center"><p><?= change_date_format($row['BTB_LC_OPEN_DATE']); ?></p></td>
			                <td width="120"><p><?= $row['BTB_LC_NUMBER']; ?></p></td>
			                <td width="80" align="right"><p><?= number_format($row['BTB_VALUE'],2); ?></p></td>
			                <td width="100" align="center"><p><?= change_date_format($ls_sc_arr[$pi_id]['LC_SC_SHIP_DATE']); ?></p></td>
			                <td width="80" align="center"><p>
			                	<?
				                $pi_file_name=$pi_file_arr[$pi_id]['FILE'];
								if ($pi_file_name != '')
								{
									?>
				                    <input type="button" class="image_uploader" id="pifileno_<? echo $i;?>" style="width:60px" value="File" onClick="openmypage_file('<?= $pi_id; ?>','pi_show_file')"/>
				                    <?
								}
							    ?>
							</p></td>
			                <td width="80" align="center"><p>
			                	<?
				                $btb_file_name=$btb_file_arr[$row['BTB_SYSTEM_ID']]['FILE'];        
								if ($btb_file_name != '')
								{
									?>
				                    <input type="button" class="image_uploader" id="btbfileno_<? echo $i; ?>" style="width:60px" value="File" onClick="openmypage_file('<?= $row['BTB_SYSTEM_ID']; ?>','btb_show_file')"/>
				                    <?
								}
							    ?>
			                </p></td>		
			        	</tr>
			        	<?
			        	$i++;
			        	$tot_pi_value+=$row['PI_VALUE'];
			        	$tot_btb_value+=$row['BTB_VALUE'];
			        }
			        ?>	
		        	<tr class="tbl_bottom">
		                <td colspan="8" align="right">Total:&nbsp;</td>
		                <td width="80" align="right"><? echo number_format($tot_pi_value,2); ?></td>
		                <td width="100">&nbsp;</td>
		                <td width="130" align="right"></td>
		                <td width="130" align="right"></td>
		                <td width="120">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="120" align="right"></td>
		                <td width="80" align="right"><? echo number_format($tot_btb_value,2); ?></td>
		                <td width="100">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		            </tr>
		        </tbody>
		    </table>
		</div>
	</div>	        	
	<?
	foreach (glob("$user_id*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename";
	exit();
}

if($action==='pi_item_description')
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$company_arr = return_library_array("select id,company_name from lib_company where status_active=1", 'id','company_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1",'id','color_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer where status_active=1",'id','short_name');

	if ($item_category_id==1)  // Yarn
	{
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count",'id','yarn_count');
		$sql_pi="SELECT a.id as PI_ID, a.item_category_id as ITEM_CATEGORY_ID, a.pay_term as PAY_TERM, b.item_description as ITEM_DESCRIPTION, b.count_name as COUNT_NAME, b.yarn_composition_item1 as YARN_COMPOSITION_ITEM1, b.yarn_composition_percentage1 as YARN_COMPOSITION_PERCENTAGE1, b.yarn_type as YARN_TYPE, b.color_id as COLOR_ID, b.net_pi_amount as PI_VALUE, c.job_no as JOB_NO
		from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c
		where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.importer_id=$company_id and a.id in($pi_id) and a.item_category_id=$item_category_id and b.item_category_id=$item_category_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.id, a.item_category_id, a.pay_term, b.item_description, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_type, b.color_id, b.net_pi_amount, c.job_no";
		$sql_pi_res=sql_select($sql_pi);
		foreach ($sql_pi_res as $val) {
			$job_nos.="'".$val['JOB_NO']."'".',';
		}
		
		$job_NOs=implode(',',array_flip(array_flip(explode(',',rtrim($job_nos,',')))));

		$sql_costing="SELECT job_no as JOB_NO, costing_per as COSTING_PER from wo_pre_cost_mst where status_active=1 and is_deleted=0 and job_no in($job_NOs)";
		$sql_costing_res=sql_select($sql_costing);
		$costing_per_arr=array();
		foreach ($sql_costing_res as $val) {
			if ( $val['COSTING_PER'] == 1) $costing_per = 12;
			else if ( $val['COSTING_PER'] == 2) $costing_per = 1;
			else if ( $val['COSTING_PER'] == 3) $costing_per = 24;
			else if ( $val['COSTING_PER'] == 4) $costing_per = 36;
			else if ( $val['COSTING_PER'] == 5) $costing_per = 48;
			$costing_per_arr[$val['JOB_NO']] = $costing_per;
		}
		unset($sql_costing_res);
		//echo '<pre>';print_r($costing_per_arr);die;

		$sql_order="SELECT a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.style_ref_no as STYLE_REF_NO, b.id as ORDER_ID, b.PO_NUMBER as PO_NUMBER, c.order_quantity as ORDER_QUANTITY, c.plan_cut_qnty as PLAN_CUT_QNTY, f.id AS YARN_ID, f.count_id as COUNT_ID, f.copm_one_id as COPM_ONE_ID, f.percent_one as PERCENT_ONE, f.type_id as TYPE_ID, f.color as COLOR, f.amount as AMOUNT
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_fab_yarn_cost_dtls f
		where a.company_name=$company_id and a.job_no in($job_NOs) and a.id=b.job_id and a.id=c.job_id and a.id=f.job_id and b.id=c.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and f.is_deleted=0 and f.status_active=1";
		$sql_order_res=sql_select($sql_order);
		$job_order_arr=array();
		foreach ($sql_order_res as $row) 
		{
			$key=$yarn_count_arr[$row['COUNT_ID']]." ".$composition[$row['COPM_ONE_ID']]." ".$row['PERCENT_ONE']."% ".$yarn_type[$row['TYPE_ID']];
			$job_order_arr[$row['JOB_NO']][$key]['BUYER_NAME']=$row['BUYER_NAME'];
			$job_order_arr[$row['JOB_NO']][$key]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
			$job_order_arr[$row['JOB_NO']][$key]['PO_NUMBER']=$row['PO_NUMBER'];
			$job_order_arr[$row['JOB_NO']][$key]['ORDER_QUANTITY']=$row['ORDER_QUANTITY'];
			$job_order_arr[$row['JOB_NO']][$key]['PLAN_CUT_QNTY']=$row['PLAN_CUT_QNTY'];
			$job_order_arr[$row['JOB_NO']][$key]['AMOUNT']+=$row['AMOUNT'];
		}
		unset($sql_order_res);
		//echo '<pre>';print_r($job_order_arr);die;
	}
	else if($item_category_id==2) // Knit Finish Fabric
	{
		$sql_pi="SELECT a.id as PI_ID, a.item_category_id as ITEM_CATEGORY_ID, a.pay_term as PAY_TERM, b.item_description as ITEM_DESCRIPTION, b.fabric_construction as FABRIC_CONSTRUCTION, b.fabric_composition as FABRIC_COMPOSITION, b.color_id as COLOR_ID, b.gsm as GSM, b.dia_width as DIA, b.net_pi_amount as PI_VALUE, c.job_no as JOB_NO
		from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c
		where a.id=b.pi_id and b.work_order_no=c.booking_no and a.importer_id=$company_id and a.id in($pi_id) and a.item_category_id=$item_category_id and b.item_category_id=$item_category_id and c.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.id, a.item_category_id, a.pay_term, b.item_description, b.fabric_construction, b.fabric_composition, b.COLOR_ID, b.gsm, b.dia_width, b.net_pi_amount, c.job_no";
		$sql_pi_res=sql_select($sql_pi);
		foreach ($sql_pi_res as $val) {
			$job_nos.="'".$val['JOB_NO']."'".',';
		}
		$job_NOs=implode(',',array_flip(array_flip(explode(',',rtrim($job_nos,',')))));

		$sql_costing="SELECT job_no as JOB_NO, costing_per as COSTING_PER from wo_pre_cost_mst where status_active=1 and is_deleted=0 and job_no in($job_NOs)";
		$sql_costing_res=sql_select($sql_costing);
		$costing_per_arr=array();
		foreach ($sql_costing_res as $val) {
			if ( $val['COSTING_PER'] == 1) $costing_per = 12;
			else if ( $val['COSTING_PER'] == 2) $costing_per = 1;
			else if ( $val['COSTING_PER'] == 3) $costing_per = 24;
			else if ( $val['COSTING_PER'] == 4) $costing_per = 36;
			else if ( $val['COSTING_PER'] == 5) $costing_per = 48;
			$costing_per_arr[$val['JOB_NO']] = $costing_per;
		}
		unset($sql_costing_res);
		//echo '<pre>';print_r($costing_per_arr);die;

		$sql_order="SELECT a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.style_ref_no as STYLE_REF_NO, b.id as ORDER_ID, b.po_number as PO_NUMBER, c.order_quantity as ORDER_QUANTITY, c.plan_cut_qnty as PLAN_CUT_QNTY, d.construction as CONSTRUCTION, d.composition as COMPOSITION, d.gsm_weight as GSM, e.color_number_id as COLOR_ID, e.cons as AMOUNT 
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e
		where a.company_name=$company_id and a.job_no in($job_NOs) and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.item_number_id=d.item_number_id and c.po_break_down_id=e.po_break_down_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.fabric_source=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1";
		$sql_order_res=sql_select($sql_order);
		$job_order_arr=array();
		foreach ($sql_order_res as $row) 
		{
			$key=$row['CONSTRUCTION']." ".$row['COMPOSITION']." ".$color_arr[$row['COLOR_ID']]." ".$row['GSM'];
			$job_order_arr[$row['JOB_NO']][$key]['BUYER_NAME']=$row['BUYER_NAME'];
			$job_order_arr[$row['JOB_NO']][$key]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
			$job_order_arr[$row['JOB_NO']][$key]['PO_NUMBER']=$row['PO_NUMBER'];
			$job_order_arr[$row['JOB_NO']][$key]['ORDER_QUANTITY']=$row['ORDER_QUANTITY'];
			$job_order_arr[$row['JOB_NO']][$key]['PLAN_CUT_QNTY']=$row['PLAN_CUT_QNTY'];
			$job_order_arr[$row['JOB_NO']][$key]['AMOUNT']+=$row['AMOUNT'];
		}
		unset($sql_order_res);
		//echo '<pre>';print_r($job_order_arr);die;
	}
	else if($item_category_id==3) // Woven Fabric
	{
		$sql_pi="SELECT a.id as PI_ID, a.item_category_id as ITEM_CATEGORY_ID, a.pay_term as PAY_TERM, b.item_description as ITEM_DESCRIPTION, b.fabric_construction as FABRIC_CONSTRUCTION, b.fabric_composition as FABRIC_COMPOSITION, b.color_id as COLOR_ID, b.gsm as GSM, b.dia_width as DIA, b.fab_weight as FAB_WEIGHT, b.net_pi_amount as PI_VALUE, c.job_no as JOB_NO
		from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c
		where a.id=b.pi_id and b.work_order_no=c.booking_no and a.importer_id=$company_id and a.id in($pi_id) and a.item_category_id=$item_category_id and b.item_category_id=$item_category_id and c.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.id, a.item_category_id, a.pay_term, b.item_description, b.fabric_construction, b.fabric_composition, b.color_id, b.gsm, b.dia_width, b.fab_weight, b.net_pi_amount, c.job_no";
		// echo $sql_pi;
		$sql_pi_res=sql_select($sql_pi);
		foreach ($sql_pi_res as $val) {
			$job_nos.="'".$val['JOB_NO']."'".',';
		}
		$job_NOs=implode(',',array_flip(array_flip(explode(',',rtrim($job_nos,',')))));

		$sql_costing="SELECT job_no as JOB_NO, costing_per as COSTING_PER from wo_pre_cost_mst where status_active=1 and is_deleted=0 and job_no in($job_NOs)";
		$sql_costing_res=sql_select($sql_costing);
		$costing_per_arr=array();
		foreach ($sql_costing_res as $val) {
			if ( $val['COSTING_PER'] == 1) $costing_per = 12;
			else if ( $val['COSTING_PER'] == 2) $costing_per = 1;
			else if ( $val['COSTING_PER'] == 3) $costing_per = 24;
			else if ( $val['COSTING_PER'] == 4) $costing_per = 36;
			else if ( $val['COSTING_PER'] == 5) $costing_per = 48;
			$costing_per_arr[$val['JOB_NO']] = $costing_per;
		}
		unset($sql_costing_res);
		//echo '<pre>';print_r($costing_per_arr);die;

		$sql_order="SELECT a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.style_ref_no as STYLE_REF_NO, b.id as ORDER_ID, b.po_number as PO_NUMBER, c.order_quantity as ORDER_QUANTITY, c.plan_cut_qnty as PLAN_CUT_QNTY, d.construction as CONSTRUCTION, d.composition as COMPOSITION, d.gsm_weight as GSM, e.color_number_id as COLOR_ID, e.cons as AMOUNT 
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_fabric_cost_dtls d, wo_pre_cos_fab_co_avg_con_dtls e
		where a.company_name=$company_id and a.job_no in($job_NOs) and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.item_number_id=d.item_number_id and c.po_break_down_id=e.po_break_down_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.fab_nature_id=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1";
		// echo $sql_order;
		$sql_order_res=sql_select($sql_order);
		$job_order_arr=array();
		foreach ($sql_order_res as $row)
		{
			$key=$row['CONSTRUCTION']." ".$row['COMPOSITION']." ".$color_arr[$row['COLOR_ID']]." ".$row['GSM'];
			$job_order_arr[$row['JOB_NO']][$key]['BUYER_NAME']=$row['BUYER_NAME'];
			$job_order_arr[$row['JOB_NO']][$key]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
			$job_order_arr[$row['JOB_NO']][$key]['PO_NUMBER']=$row['PO_NUMBER'];
			$job_order_arr[$row['JOB_NO']][$key]['ORDER_QUANTITY']=$row['ORDER_QUANTITY'];
			$job_order_arr[$row['JOB_NO']][$key]['PLAN_CUT_QNTY']=$row['PLAN_CUT_QNTY'];
			$job_order_arr[$row['JOB_NO']][$key]['AMOUNT']+=$row['AMOUNT'];
		}
		unset($sql_order_res);
		//echo '<pre>';print_r($job_order_arr);die;
	}
	else if($item_category_id==4)  // Accessories
	{
		$item_group_arr=return_library_array("select id, item_name from lib_item_group where status_active=1",'id','item_name');
		$sql_pi="SELECT a.id as PI_ID, a.item_category_id as ITEM_CATEGORY_ID, a.pay_term as PAY_TERM, b.id as PI_DTLS_ID, b.item_description as ITEM_DESCRIPTION, b.item_group as ITEM_GROUP, b.net_pi_amount as PI_VALUE, c.job_no as JOB_NO
		from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c
		where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.importer_id=$company_id and a.id in($pi_id) and a.item_category_id=$item_category_id and b.item_category_id=$item_category_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.id, a.item_category_id, a.pay_term, b.id, b.item_description, b.item_group, b.net_pi_amount, c.job_no";
		$sql_pi_res=sql_select($sql_pi);
		foreach ($sql_pi_res as $val) {
			$job_nos.="'".$val['JOB_NO']."'".',';
		}
		$job_NOs=implode(',',array_flip(array_flip(explode(',',rtrim($job_nos,',')))));

		$sql_costing="SELECT job_no as JOB_NO, costing_per as COSTING_PER from wo_pre_cost_mst where status_active=1 and is_deleted=0 and job_no in($job_NOs)";
		$sql_costing_res=sql_select($sql_costing);
		$costing_per_arr=array();
		foreach ($sql_costing_res as $val) {
			if ( $val['COSTING_PER'] == 1) $costing_per = 12;
			else if ( $val['COSTING_PER'] == 2) $costing_per = 1;
			else if ( $val['COSTING_PER'] == 3) $costing_per = 24;
			else if ( $val['COSTING_PER'] == 4) $costing_per = 36;
			else if ( $val['COSTING_PER'] == 5) $costing_per = 48;
			$costing_per_arr[$val['JOB_NO']] = $costing_per;
		}
		unset($sql_costing_res);
		//echo '<pre>';print_r($costing_per_arr);die;

		$sql_order="SELECT a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.style_ref_no as STYLE_REF_NO, b.id as ORDER_ID, b.po_number as PO_NUMBER, c.order_quantity as ORDER_QUANTITY, c.plan_cut_qnty as PLAN_CUT_QNTY, d.trim_group as TRIM_GROUP, e.amount as AMOUNT
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e
		where a.company_name=$company_id and a.job_no in($job_NOs) and a.id=b.job_id and b.id=c.po_break_down_id and a.id=d.job_id and d.id=e.wo_pre_cost_trim_cost_dtls_id and b.id=e.po_break_down_id and c.item_number_id= e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and e.cons>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1";
		// echo $sql_order;
		$sql_order_res=sql_select($sql_order);
		$job_order_arr=array();
		foreach ($sql_order_res as $row)
		{
			$key=$row['TRIM_GROUP'];
			$job_order_arr[$row['JOB_NO']][$key]['BUYER_NAME']=$row['BUYER_NAME'];
			$job_order_arr[$row['JOB_NO']][$key]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
			$job_order_arr[$row['JOB_NO']][$key]['PO_NUMBER']=$row['PO_NUMBER'];
			$job_order_arr[$row['JOB_NO']][$key]['ORDER_QUANTITY']=$row['ORDER_QUANTITY'];
			$job_order_arr[$row['JOB_NO']][$key]['PLAN_CUT_QNTY']=$row['PLAN_CUT_QNTY'];
			$job_order_arr[$row['JOB_NO']][$key]['AMOUNT']+=$row['AMOUNT'];
		}
		unset($sql_order_res);
		//echo '<pre>';print_r($job_order_arr);die;
	}
	else if($item_category_id==25)  // Services - Embellishment
	{
		$item_group_arr=return_library_array("select id, item_name from lib_item_group where status_active=1",'id','item_name');
		$sql_pi="SELECT a.id as PI_ID, a.item_category_id as ITEM_CATEGORY_ID, a.pay_term as PAY_TERM, b.item_description as ITEM_DESCRIPTION, b.item_group as ITEM_GROUP, b.net_pi_amount as PI_VALUE, c.job_no as JOB_NO, c.pre_cost_fabric_cost_dtls_id as EMBE_COST_DTLS_ID, c.PO_BREAK_DOWN_ID
		from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c
		where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.importer_id=$company_id and a.id in($pi_id) and a.item_category_id=$item_category_id and b.item_category_id=$item_category_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.id, a.item_category_id, a.pay_term, b.item_description, b.item_group, b.net_pi_amount, c.job_no, c.pre_cost_fabric_cost_dtls_id, c.po_break_down_id";die;
		$sql_pi_res=sql_select($sql_pi);
		foreach ($sql_pi_res as $val) {
			$job_no.="'".$val['JOB_NO']."'".',';
			$embe_cost_dtls_id.=$val['EMBE_COST_DTLS_ID'].',';
		}
		$job_nos=implode(',',array_flip(array_flip(explode(',',rtrim($job_no,',')))));
		$embe_cost_dtls_ids=implode(',',array_flip(array_flip(explode(',',rtrim($embe_cost_dtls_id,',')))));

		$sql_costing="SELECT job_no as JOB_NO, costing_per as COSTING_PER from wo_pre_cost_mst where status_active=1 and is_deleted=0 and job_no in($job_nos)";
		$sql_costing_res=sql_select($sql_costing);
		$costing_per_arr=array();
		foreach ($sql_costing_res as $val) {
			if ( $val['COSTING_PER'] == 1) $costing_per = 12;
			else if ( $val['COSTING_PER'] == 2) $costing_per = 1;
			else if ( $val['COSTING_PER'] == 3) $costing_per = 24;
			else if ( $val['COSTING_PER'] == 4) $costing_per = 36;
			else if ( $val['COSTING_PER'] == 5) $costing_per = 48;
			$costing_per_arr[$val['JOB_NO']] = $costing_per;
		}
		unset($sql_costing_res);
		//echo '<pre>';print_r($costing_per_arr);die;
		/*$sql_order="SELECT a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.style_ref_no as STYLE_REF_NO, b.id as ORDER_ID, b.po_number as PO_NUMBER, c.order_quantity as ORDER_QUANTITY, c.plan_cut_qnty as PLAN_CUT_QNTY, d.EMB_NAME, d.EMB_TYPE, e.ITEM_NUMBER_ID, e.amount as AMOUNT
		from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_embe_cost_dtls d, wo_pre_cos_emb_co_avg_con_dtls e
		where a.company_name=$company_id and a.job_no in($job_nos) and d.id in($embe_cost_dtls_ids) and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and b.id=c.po_break_down_id and c.item_number_id=e.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.size_number_id and d.id=e.pre_cost_emb_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0";*/

		$sql_order="SELECT b.job_no as JOB_NO, c.EMB_NAME, c.EMB_TYPE, d.ITEM_NUMBER_ID, d.COLOR_NUMBER_ID, sum(e.amount) as AMOUNT
		from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c, wo_pre_cos_emb_co_avg_con_dtls d
		where a.company_id=$company_id and a.job_no in($job_nos) and d.id in($embe_cost_dtls_ids) and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.id=d.pre_cost_emb_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
		group by b.job_no, c.emb_name, c.emb_type, d.item_number_id, d.color_number_id";die;
		$sql_order_res=sql_select($sql_order);
		$job_order_arr=array();
		foreach ($sql_order_res as $row)
		{
			$key=$row['TRIM_GROUP'];
			$job_order_arr[$row['JOB_NO']][$key]['BUYER_NAME']=$row['BUYER_NAME'];
			$job_order_arr[$row['JOB_NO']][$key]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
			$job_order_arr[$row['JOB_NO']][$key]['PO_NUMBER']=$row['PO_NUMBER'];
			$job_order_arr[$row['JOB_NO']][$key]['ORDER_QUANTITY']=$row['ORDER_QUANTITY'];
			$job_order_arr[$row['JOB_NO']][$key]['PLAN_CUT_QNTY']=$row['PLAN_CUT_QNTY'];
			$job_order_arr[$row['JOB_NO']][$key]['AMOUNT']+=$row['AMOUNT'];
		}
		unset($sql_order_res);
		//echo '<pre>';print_r($job_order_arr);die;
	}	
	else
	{
		echo '<p style="font-weight:bold; color:red; font-size:18px;">Will Develop Later!!</p>';die;
	}	
	
	$table_width=890;
	?>
    <script>
    	function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}

		function change_color(v_id,e_color)
		{
			if (document.getElementById(v_id).bgColor=='#33CC00')
				document.getElementById(v_id).bgColor=e_color;
			else
				document.getElementById(v_id).bgColor='#33CC00';
		}
    </script>
    <body>
    	<div style="<? echo $table_width; ?>px; margin: 0 auto;">
	        <div style="float: left;">
	            <input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/> &nbsp;
	        </div>            
	        <div id="report_container" style="float: left;"></div>
	    </div>
		<!-- <th width="70">Order Qty</th>
		<th width="70">PI for Qty (Plan Cut)</th>               
		<th width="70">Cost Value ($)</th>
		<td width="70" align="right"><p><?= $order_qty; ?></p></td>
		<td width="70" align="right"><p><?= $plan_cut_qty; ?></p></td>
		<td width="70" align="right" title="(Amount/Costing Per)*Order Qty"><p><?= number_format($cost_value,2); ?></p></td> -->
	    <?	ob_start();	?>
    	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">		
			<div style="width:100%" id="report_div">			
				<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<td colspan="13" style="font-size:16px" width="100%" align="center"><strong><? echo $company_arr[$company_id]; ?></strong>
							</td>
						</tr>
						<tr>
							<td colspan="13" style="font-size:16px" width="100%" align="center"><strong>Item Category:&nbsp;<? echo $item_category[$item_category_id]; ?></strong>
							</td>
						</tr>
					</thead>
				</table>
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
					<thead>
						<tr>
		                    <th width="30">SL</th>
		                    <th width="100">Buyer Name</th>
		                    <th width="100">Style No</th>
		                    <th width="100">PO No</th>
		                    <th width="160">Item Description</th>
		                    <th width="70">PI Value ($)</th>              	
		                    <th width="70">Balance ($)</th>
		                    <th width="70">Item Value/PC</th>
		                    <th width="80">Payment Terms</th>
		                    <th width="100">Sales Contract No</th>
	                    </tr>
					</thead>				
	            </table>
	            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
					<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
		                <tbody>
		                	<?
		                	$i=1;
		                	foreach ($sql_pi_res as $row) 
		                	{
		                		if ($i%2==0) $bgcolor="#E9F3FF";
								else $bgcolor="#FFFFFF";
								

			                	if($row['ITEM_CATEGORY_ID']==1)
				                {
				                	$key = $yarn_count_arr[$row['COUNT_NAME']]." ".$composition[$row['YARN_COMPOSITION_ITEM1']]." ".$row['YARN_COMPOSITION_PERCENTAGE1']."% ".$yarn_type[$row['YARN_TYPE']];
				                	$description = $yarn_count_arr[$row['COUNT_NAME']]." ".$composition[$row['YARN_COMPOSITION_ITEM1']]." ".$row['YARN_COMPOSITION_PERCENTAGE1']."% ".$yarn_type[$row['YARN_TYPE']]." ".$color_arr[$row['COLOR_ID']];
				                }
				                else if($row['ITEM_CATEGORY_ID']==2)
				                {
				                	$key=$row['FABRIC_CONSTRUCTION']." ".$row['FABRIC_COMPOSITION']." ".$color_arr[$row['COLOR_ID']]." ".$row['GSM'];
				                	$description=$row['FABRIC_CONSTRUCTION']." ".$row['FABRIC_COMPOSITION']." ".$color_arr[$row['COLOR_ID']]." ".$row['GSM']." ".$row['DIA'];		                	
				                }
				                else if($row['ITEM_CATEGORY_ID']==3)
				                {
				                	$key=$row['FABRIC_CONSTRUCTION']." ".$row['FABRIC_COMPOSITION']." ".$color_arr[$row['COLOR_ID']]." ".$row['FAB_WEIGHT'];
				                	$description=$row['FABRIC_CONSTRUCTION']." ".$row['FABRIC_COMPOSITION']." ".$color_arr[$row['COLOR_ID']]." ".$row['FAB_WEIGHT']." ".$row['DIA'];		                	
				                }
				                else if($row['ITEM_CATEGORY_ID']==4)
				                {
				                	$key=$row['ITEM_GROUP'];
				                	$description=$item_group_arr[$row['ITEM_GROUP']];
				                }	

				                $order_qty=$job_order_arr[$row['JOB_NO']][$key]['ORDER_QUANTITY'];
				                $plan_cut_qty=$job_order_arr[$row['JOB_NO']][$key]['PLAN_CUT_QNTY'];
				                $cost_value = ($job_order_arr[$row['JOB_NO']][$key]['AMOUNT']/$costing_per_arr[$val['JOB_NO']]) * $order_qty;
				                $item_value=$plan_cut_qty/$row['PI_VALUE'];
				                $balance=$cost_value-$row['PI_VALUE'];
				                ?>
								<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>" style="text-decoration:none; cursor:pointer">
									<td width="30" align="center"><?= $i; ?></td>
									<td width="100"><p><?= $buyer_arr[$job_order_arr[$row['JOB_NO']][$key]['BUYER_NAME']]; ?></p></td>
		                            <td width="100"><p><?= $job_order_arr[$row['JOB_NO']][$key]['STYLE_REF_NO']; ?></p></td>
		                            <td width="100"><p><?= $job_order_arr[$row['JOB_NO']][$key]['PO_NUMBER']; ?></p></td>
		                            <td width="160"><p>&nbsp;<?= $description; ?></p></td>
		                            <td width="70" align="right"><p><?= number_format($row['PI_VALUE'],2); ?></p></td>
		                            <td width="70" align="right"><p><?= number_format($balance,2); ?></p></td>
		                            <td width="70" align="right"><p><?= number_format($item_value,2); ?></p></td>
		                            <td width="80" align="center"><p><?= $pay_term[$row['PAY_TERM']]; ?></p></td>
		                            <td width="100" align="center"><p><?= $lc_sc_no; ?></p></td>
		                        </tr>
		                        <?
		                        $i++;
		                        $tot_cost_value += $cost_value;
		                        $tot_pi_value += $row['PI_VALUE'];
		                    }    
		                    ?>
		                    <tr class="tbl_bottom">
				                <td colspan="5" align="right">Total:&nbsp;</td>
				                <td width="70" align="right"><? echo number_format($tot_pi_value,2); ?></td>
				                <td width="70">&nbsp;</td>
				                <td width="70">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="100">&nbsp;</td>
				            </tr>  			
		                </tbody>
		            </table>	           
		        </div>
		    </div>
		</fieldset>
	</body>     
	
    <?
	foreach (glob("$user_id*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//echo "$total_data****$filename";
	//exit();
	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$filename; ?>" />
    <script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $filename?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:100px;font-size:12px"/></a>&nbsp;&nbsp;';
        }); 
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>
	<?
	exit();
}

if($action==='pi_popup')
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push(str);					
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 ); 
				selected_no.splice( i, 1 );
			}
			var id = ''; var name = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 ); 
			
			$('#txt_selected_id').val( id );
			$('#txt_selected_no').val( name ); 
		}

		// function set_caption(id)
		// {
		// if(id==1)  document.getElementById('search_by_td_up').innerHTML='Please Enter PI No';
		// if(id==2)  document.getElementById('search_by_td_up').innerHTML='Enter File No';
		// }
		
    </script>
    <body>
	<div align="center" style="width:100%;" >
		<form name="searchpiform" id="searchpiform" autocomplete="off">
		<table width="500" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
			<thead>
				<tr>                	 
					<th>Search By</th>
					<th align="center" width="200" id="search_by_td_up">Please Enter PI No</th>
						<th>
                   		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" />
                        <input type='hidden' id='txt_selected_id' />
						<input type='hidden' id='txt_selected_no' />
                    </th>
				</tr>
			</thead>
			<tbody>
				<tr align="center">
					<td align="center">
						<?  
							$search_by = array(1=>'PI No',2=>"File No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../')";
							echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", "", $dd, 0);
						?>
					</td>
					<td width="180" align="center" id="search_by_td">				
						<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
					</td> 
					<td align="center">
						<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>, 'create_pi_search_list_view', 'search_div', 'pi_pending_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;"/>				
					</td>
				</tr>
			</tbody>        
		</table>    
		<div align="center" valign="top" style="margin-top:5px" id="search_div"></div> 
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>
    <?
	exit();
}

if($action==='create_pi_search_list_view')
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1",'id','company_name');
	$suplayer_arr=return_library_array( "select id, SUPPLIER_NAME from lib_supplier where status_active=1",'id','SUPPLIER_NAME');
	if($txt_search_by==1)
	{
		$sql_cond='';
		if($txt_search_common != '') $sql_cond=" and a.pi_number = '$txt_search_common'";
		// $sql="select id as system_id, importer_id, pi_number from com_pi_master_details where importer_id=$company and status_active=1 and is_deleted=0 $sql_cond"; 	
	}else {
		if($txt_search_common != '') $sql_cond=" and a.internal_file_no = '$txt_search_common'";
	}

	// $sql="SELECT a.id as system_id, a.importer_id, a.pi_number,d.internal_file_no, a.supplier_id from com_pi_master_details a, com_btb_lc_pi b, com_btb_export_lc_attachment c, com_export_lc d where a.id=b.pi_id and c.IMPORT_MST_ID=b.COM_BTB_LC_MASTER_DETAILS_ID and  d.id=c.lc_sc_id and c.is_lc_sc=0 and c.status_active=1 and a.status_active=1 and b.status_active=1 and d.status_active=1  $sql_cond 
	// union all 
	// SELECT a.id as system_id, a.importer_id, a.pi_number,d.internal_file_no, a.supplier_id from  com_pi_master_details a, com_btb_lc_pi b, com_btb_export_lc_attachment c, com_sales_contract d where a.id=b.pi_id and c.IMPORT_MST_ID=b.COM_BTB_LC_MASTER_DETAILS_ID and d.id=c.lc_sc_id and c.is_lc_sc=1 and c.status_active=1  and a.status_active=1 and b.status_active=1 and d.status_active=1  $sql_cond";
	// echo $sql;die;

	// $sql="SELECT a.id as system_id, a.importer_id, a.pi_number,e.internal_file_no from 
	// com_pi_master_details a left join com_btb_lc_pi b on a.id=b.pi_id and b.status_active=1 and b.is_deleted=0 
	// left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id and c.status_active=1 and c.is_deleted=0
	// left join com_btb_export_lc_attachment d on c.id=d.IMPORT_MST_ID and d.status_active=1 and d.is_deleted=0
	// left join com_export_lc e on e.id=d.lc_sc_id and e.status_active=1 and e.is_deleted=0
	// where a.status_active=1 and a.is_deleted=0 $sql_cond
	// union all 
	// SELECT a.id as system_id, a.importer_id, a.pi_number,e.internal_file_no from 
	// com_pi_master_details a left join com_btb_lc_pi b on a.id=b.pi_id and b.status_active=1 and b.is_deleted=0 
	// left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id and c.status_active=1 and c.is_deleted=0
	// left join com_btb_export_lc_attachment d on c.id=d.IMPORT_MST_ID and d.status_active=1 and d.is_deleted=0
	// left join com_sales_contract e on e.id=d.lc_sc_id and e.status_active=1 and e.is_deleted=0
	// where a.status_active=1 and a.is_deleted=0 $sql_cond";

	 $sql="SELECT a.id as system_id, a.importer_id, a.pi_number,a.INTERNAL_FILE_NO, a.supplier_id from com_pi_master_details a where a.status_active=1 and a.importer_id=$company  $sql_cond";

	$arr=array(0=>$company_arr,1=>$suplayer_arr);
	echo create_list_view("list_view", "Company,Supplier,PI No,File No,System ID","150,100,100,100,150","600","260",0, $sql , "js_set_value", "system_id,pi_number,internal_file_no", "", 1, "importer_id,supplier_id,0,0", $arr, "importer_id,supplier_id,pi_number,internal_file_no,system_id", "","","0,0,0,0,0","",1);
	exit();	
}	

if($action==='btb_lc_popup')
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon);return;
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push(str);					
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 ); 
				selected_no.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 ); 
			
			$('#txt_selected_id').val( id );
			$('#txt_selected_no').val( name ); 
		}
		
    </script>
    <body>
	<div align="center" style="width:100%;" >
		<form name="searchbtblcform"  id="searchbtblcform" autocomplete="off">
		<table width="500" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
			<thead>
				<tr>                	 
					<th>Search By</th>
					<th align="center" width="200" id="search_by_td_up">Please Enter LC No</th>
						<th>
                   		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" />
                        <input type='hidden' id='txt_selected_id' />
						<input type='hidden' id='txt_selected_no' />
                    </th>
				</tr>
			</thead>
			<tbody>
				<tr align="center">
					<td align="center">
						<?  
							$search_by = array(1=>'LC No');
							$dd="change_search_event(this.value, '0', '0', '../../')";
							echo create_drop_down( "cbo_search_by", 150, $search_by, "", 0, "--Select--", "", $dd, 0);
						?>
					</td>
					<td width="180" align="center" id="search_by_td">				
						<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
					</td> 
					<td align="center">
						<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>, 'create_btb_lcSc_search_list_view', 'search_div', 'pi_pending_report_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;"/>				
					</td>
				</tr>
			</tbody>        
		</table>    
		<div align="center" valign="top" style="margin-top:5px" id="search_div"></div> 
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
	</html>
    <?
	exit();
}

if($action==='create_btb_lcSc_search_list_view')
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1",'id','company_name');
	if($txt_search_by==1)
	{
		$sql_cond='';
		if($txt_search_common != '') $sql_cond=" and lc_number LIKE '%$txt_search_common%'";
		
		$sql="select id, importer_id, lc_number, lc_value from com_btb_lc_master_details where importer_id=$company and status_active=1 and is_deleted=0 $sql_cond"; 
		
	}
	//echo $sql;die;
	$arr=array(0=>$company_arr);
	echo create_list_view("list_view", "Company,Lc No,Value","150,100","600","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "importer_id,0,0", $arr, "importer_id,lc_number,lc_value", "","","0,0,0,0,2","",1);
	exit();	
}

if($action==='pi_show_file')
{
	echo load_html_head_contents("Invoice File","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$data_array=sql_select("SELECT IMAGE_LOCATION, REAL_FILE_NAME from common_photo_library where master_tble_id='$mst_id' and form_name='proforma_invoice' and is_deleted=0 and file_type=2");
	?>
	<style type="text/css">
		li { list-style: none; font-size: 9pt; margin-top: 0px; margin-left: 7px; float: left; width: 89px;}
	</style>
    <table width="100%">
        <tr>
        	<td width="100%" height="250" style="vertical-align: top;">
	        <?
	        foreach ($data_array as $row)
	        {
	        	?>
	        	<li>
	        		<a href="../../../<? echo $row['IMAGE_LOCATION']; ?>" target="_new">
	        		<img src="../../../file_upload/blank_file.png" height="97" width="89"></a>
	        		<br>
	        		<p style="width: 89px; word-break: break-all; margin-top: 1px;"><? echo $row['REAL_FILE_NAME']; ?></p>
	        	</li>
	        	<?
	        }
	        ?>
        	</td>
        </tr>
    </table>
    <?
}

if($action==='btb_show_file')
{
	echo load_html_head_contents("BTB LC File","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$data_array=sql_select("SELECT IMAGE_LOCATION, REAL_FILE_NAME from common_photo_library where master_tble_id='$mst_id' and form_name='BTBMargin LC' and is_deleted=0 and file_type=2");
	?>
	<style type="text/css">
		li { list-style: none; font-size: 9pt; margin-top: 0px; margin-left: 7px; float: left; width: 89px;}
	</style>
    <table width="100%">
        <tr>
        	<td width="100%" height="250" style="vertical-align: top;">
	        <?
	        foreach ($data_array as $row)
	        {
	        	?>
	        	<li>
	        		<a href="../../../<? echo $row['IMAGE_LOCATION']; ?>" target="_new">
	        		<img src="../../../file_upload/blank_file.png" height="97" width="89"></a>
	        		<br>
	        		<p style="width: 89px; word-break: break-all; margin-top: 1px;"><? echo $row['REAL_FILE_NAME']; ?></p>
	        	</li>
	        	<?
	        }
	        ?>
        	</td>
        </tr>
    </table>
    <?
}