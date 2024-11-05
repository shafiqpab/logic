<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_supplier")
{    	 
	//echo create_drop_down( "cbo_supplier", 100, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	echo create_drop_down( "cbo_supplier", 100, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "load_drop_down( 'requires/purchase_recap_report_controller2', this.value, 'load_drop_down_category', 'category_td' );",0 );


}

if($action == "load_drop_down_category")
{
	$supplier_res = sql_select("select c.supplier_name,c.id, b.party_type from  lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id  and c.status_active=1 and c.is_deleted=0 and c.id = $data order by c.supplier_name");
	foreach ($supplier_res as $val) 
	{
		$party_types[$val[csf("party_type")]] = $val[csf("party_type")];
	}

	$category="";
	if(empty($party_types)) 
	{
		echo create_drop_down( "cbo_item_category_id", 150, $blank_array,"", 1,"-- Select --",0,"" );
	}
	else 
	{	
		if($party_types["7"])
		{
			  
			  $item_category_all =  $item_category;
			  unset($item_category_all["1"]);
			  unset($item_category_all["2"]);
			  unset($item_category_all["3"]);
			  unset($item_category_all["4"]);
			  unset($item_category_all["5"]);
			  unset($item_category_all["6"]);
			  unset($item_category_all["7"]);
			  unset($item_category_all["9"]);
			  unset($item_category_all["10"]);
			  unset($item_category_all["11"]);
			  unset($item_category_all["13"]);
			  unset($item_category_all["14"]);
			  unset($item_category_all["31"]);
			  unset($item_category_all["32"]);
			  $category .= implode(",", array_keys($item_category_all));
		} 

		if( $party_types["2"])
		{
			if($category)  $category .= ",1"; else $category ="1";
		}
		if($party_types["9"])
		{
			if($category)  $category .=",2,3,13,14"; else $category ="2,3,13,14";
		}
		if($party_types["4"] || $party_types["5"])
		{
			if($category)  $category .=",4"; else $category ="4";
			
		}
		if($party_types["3"])
		{
			if($category) $category .=",5,6,7"; else $category ="5,6,7";
		}
		if($party_types["6"])
		{
			if($category) $category .=",9,10"; else $category ="9,10"; 
		}
		if($party_types["8"])
		{
			if($category) $category .=",11"; else $category ="11";  
		}
		if($party_types["20"] || $party_types["21"] ||$party_types["22"] ||$party_types["23"] ||$party_types["24"] ||$party_types["30"] ||$party_types["31"] ||$party_types["32"] ||$party_types["35"] ||$party_types["36"] ||$party_types["37"] ||$party_types["38"] ||$party_types["39"])
		{
			//if($category) $category .=",12,24,25"; else $category ="12,24,25";
		}
		if($party_types["26"])
		{
			//if($category) $category .= ",31"; else $category ="31";
		}
		if($party_types["92"])
		{
			if($category) $category .= ",32"; else $category ="32";
		}
		
		$category_arr1 = explode(",", $category); 
		$category_arr2 =  explode(",", "2,3,12,13,14,24,25,28,30");
		$show_category = array_diff($category_arr1, $category_arr2);
		//print_r($category);die;
		echo create_drop_down( "cbo_item_category_id", 150, $item_category,'', 1, '-- Select --',0,"",0,implode(",",$show_category),'','','');


	}
	//========================================
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');

	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);

	$txt_req_no=trim(str_replace("'","",$txt_req_no));
	
	$cbo_supplier=str_replace("'","",$cbo_supplier);
	if($cbo_supplier>0) $supplier_cond =" and a.supplier_id='$cbo_supplier' ";else $supplier_cond= "";
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_wo_po_no=trim(str_replace("'","",$txt_wo_po_no));
	$txt_pi_no=trim(str_replace("'","",$txt_pi_no));
	$cbo_surch_type=trim(str_replace("'","",$cbo_surch_type));
	

	if($db_type==0)
	{
		//$txt_date_from=change_date_format($txt_date_from,'YYYY-MM-DD');
		//$txt_date_to=change_date_format($txt_date_to,'YYYY-MM-DD');
		$txt_date_from = date("Y-m-d", strtotime(str_replace("'", "",  $txt_date_from)));
		$txt_date_to = date("Y-m-d", strtotime(str_replace("'", "",  $txt_date_to)));
		
	}
	else if($db_type==2)
	{
		//$txt_date_from=change_date_format($txt_date_from,'','',-1);
		//$txt_date_to=change_date_format($txt_date_to,'','',-1);
        if(str_replace("'", "",  $txt_date_from) != "" && str_replace("'", "",  $txt_date_to) != ""){
            $txt_date_from = date("d-M-Y", strtotime(str_replace("'", "",  $txt_date_from)));
            $txt_date_to = date("d-M-Y", strtotime(str_replace("'", "",  $txt_date_to)));
        }else{
            $txt_date_from = "";
            $txt_date_to = "";
        }
	}
	
	$prod_category_cond="";
	if($cbo_item_category_id>0) $prod_category_cond=" and item_category_id='$cbo_item_category_id' ";
	$prod_sql=sql_select("select id as PROD_ID, item_group_id as ITEM_GROUP_ID, sub_group_code as SUB_GROUP_CODE, sub_group_name as SUB_GROUP_NAME, item_code as ITEM_CODE, item_description as ITEM_DESCRIPTION, product_name_details as PRODUCT_NAME_DETAILS, unit_of_measure as UNIT_OF_MEASURE from product_details_master where status_active=1 and is_deleted=0 and company_id=$cbo_company_name $prod_category_cond");
	$prod_data_array=array();
	foreach($prod_sql as $row)
	{
		$prod_data_array[$row["PROD_ID"]]["prod_id"]=$row["PROD_ID"];
		$prod_data_array[$row["PROD_ID"]]["item_group_id"]=$row["ITEM_GROUP_ID"];
		$prod_data_array[$row["PROD_ID"]]["sub_group_code"]=$row["SUB_GROUP_CODE"];
		$prod_data_array[$row["PROD_ID"]]["sub_group_name"]=$row["SUB_GROUP_NAME"];
		$prod_data_array[$row["PROD_ID"]]["item_code"]=$row["ITEM_CODE"];
		$prod_data_array[$row["PROD_ID"]]["item_description"]=$row["ITEM_DESCRIPTION"];
		$prod_data_array[$row["PROD_ID"]]["product_name_details"]=$row["PRODUCT_NAME_DETAILS"];
		$prod_data_array[$row["PROD_ID"]]["unit_of_measure"]=$row["UNIT_OF_MEASURE"];
	}
	unset($prod_sql);
    if ($txt_pi_no != '' && $txt_wo_po_no==""  && $txt_req_no=="" && $txt_date_from == "" && $txt_date_to== "")
	{
		$gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
		if($gblDel)
		{
			oci_commit($con);
			//disconnect($con);
		}
		$temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
		if($temp_table_id=="") $temp_table_id=1;	
		//echo "here";
		$sql_cond="";
		$wo_pi_select="";
		if($cbo_company_name>0) $sql_cond.=" and a.importer_id='$cbo_company_name' ";		
		if($cbo_supplier>0) $sql_cond.=" and a.supplier_id='$cbo_supplier' ";
		
		if ($cbo_item_category_id>0) $sql_cond.=" and a.entry_form='".$category_wise_entry_form[$cbo_item_category_id]."' ";
		if ($txt_pi_no!='') 
		{
			//echo $cbo_surch_type;die;
			if($cbo_surch_type==1) $sql_cond.=" and a.pi_number ='$txt_pi_no'";
			elseif($cbo_surch_type==2) $sql_cond.=" and a.pi_number like '$txt_pi_no%'";
			elseif($cbo_surch_type==3) $sql_cond.=" and a.pi_number like '%$txt_pi_no'";
			elseif($cbo_surch_type==4) $sql_cond.=" and a.pi_number like '%$txt_pi_no%'";
			else $sql_cond.=" and a.pi_number like '%$txt_pi_no%'";
		}
		//if($cbo_supplier) $sql_pi_cond = " and a.supplier_id='$cbo_supplier'";

		$sql_pi = "SELECT a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, b.item_category_id as item_category_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, case when b.item_prod_id !='' or b.item_prod_id is not null then b.item_prod_id else 0 end as prod_id, b.work_order_no, b.uom, b.quantity, b.work_order_dtls_id, b.amount 
		from com_pi_master_details a, com_pi_item_details b 
		where a.id=b.pi_id and b.item_category_id not in (2,3,12,13,14,24,25,28,30) $sql_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		
//		echo $sql_pi;
		
		$sql_pi_result=sql_select($sql_pi);
		if(count($sql_pi_result) < 1)
		{
			echo "<span style='font-size:23;font-weight:bold;text-align:center;width:100%'>Data Not Found</span>";die;
		}
		$pi_data_arr=$pi_id_arr=$wo_num_arr=$wo_prod_arr=array();
		foreach($sql_pi_result as $row)
		{
			$pi_id_arr[$row[csf("pi_id")]]=$row[csf("pi_id")];
			$wo_num_arr[$row[csf("work_order_dtls_id")]]=$row[csf("work_order_dtls_id")];
			$wo_prod_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
			
			if($row[csf("pi_id")])
			{
				$refrID1=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",1,".$user_id.")");
				if(!$refrID1)
				{
					echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",1,".$user_id.")";oci_rollback($con);disconnect($con);die;
				}
				$temp_table_id++;
			}
			
			if($row[csf("work_order_dtls_id")])
			{
				$refrID2=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("work_order_dtls_id")].",2,".$user_id.")");
				if(!$refrID2)
				{
					echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("work_order_dtls_id")].",2,".$user_id.")";oci_rollback($con);disconnect($con);die;
				}
				$temp_table_id++;
			}
			
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["requisition_dtls_id"]=$row[csf("requisition_dtls_id")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_id"].=$row[csf("pi_id")].",";
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_number"].=$row[csf("pi_number")].",";
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_date"]=$row[csf("pi_date")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]=$row[csf("supplier_id")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["currency_id"]=$row[csf("currency_id")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["intendor_name"]=$row[csf("intendor_name")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["prod_id"].=$row[csf("prod_id")].",";
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["uom"]=$row[csf("uom")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["quantity"]+=$row[csf("quantity")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]+=$row[csf("amount")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("prod_id")]]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
		} 
//		unset($sql_pi_result);
		if($refrID1 && $refrID2)
		{
			oci_commit($con);
		}
		/*echo "<pre>";
		print_r($pi_data_arr[704]);
		die;*/
		//findind WO/PO if exists....

//		$wo_num_ids="";
//		foreach (array_filter(array_unique($wo_num_arr)) as $values)
//		{
//			$wo_num_ids.="'".$values."',";
//		}
//		$wo_num_ids=chop($wo_num_ids,",");
//
//		if($wo_num_ids=="") $wo_num_ids=0;
//		$woNumCond = $wo_num_cond = "";
//		$wo_num_arr=explode(",",$wo_num_ids);
//		if($db_type==2 && count($wo_num_arr)>999)
//		{
//			$wo_num_chunk=array_chunk($wo_num_arr,999) ;
//			foreach($wo_num_chunk as $chunk_arr)
//			{
//				$woNumCond.=" a.id in(".implode(",",$chunk_arr).") or ";
//			}
//
//			$wo_num_cond.=" and (".chop($woNumCond,'or ').")";
//		}
//		else
//		{
//
//			$wo_num_cond=" and a.id in($wo_num_ids)";
//		}
		

		if(count($wo_num_arr)>0)
		{
			$sql_wo=sql_select("SELECT a.id as wo_mst_id, a.wo_number, a.wo_number_prefix_num, a.wo_date, a.supplier_id, b.id as wo_dtls_id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name, b.requisition_dtls_id, b.item_id as prod_id, b.item_category_id, b.supplier_order_quantity, b.rate, b.amount 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b, gbl_temp_report_id c  
			where a.id=b.mst_id and b.id=c.ref_val and c.ref_from=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  "); //and a.wo_number in ($wo_num_ids)

			$wo_data_array=array();
			$req_dtls_id_arr=array();
			foreach($sql_wo as $row)
			{
				$req_dtls_id_arr[$row[csf("requisition_dtls_id")]]=$row[csf("requisition_dtls_id")];
				
				if($row[csf("requisition_dtls_id")])
				{
					$refrID3=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("requisition_dtls_id")].",3,".$user_id.")");
					if(!$refrID3)
					{
						echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("requisition_dtls_id")].",3,".$user_id.")";oci_rollback($con);disconnect($con);die;
					}
					$temp_table_id++;
				}

				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["requisition_dtls_id"]=$row[csf("requisition_dtls_id")];
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["wo_mst_id"].=$row[csf("wo_mst_id")].",";
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["wo_number"].=$row[csf("wo_number")].",";
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["wo_number_prefix_num"].=$row[csf("wo_number_prefix_num")].",";
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["wo_date"]=$row[csf("wo_date")];
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]=$row[csf("supplier_id")];
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["amount"]+=$row[csf("amount")];

				if ($cbo_item_category_id==1) 
				{	
					$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["item_description"]=$yarn_count_arr[$row[csf("yarn_count")]]." ".$composition[$row[csf("yarn_comp_type1st")]]." ".$row[csf("yarn_comp_percent1st")]." ".$yarn_type[$row[csf("yarn_type")]]." ".$color_name_arr[$row[csf("color_name")]];
				}
			}	
			unset($sql_wo);
			
			if($refrID3)
			{
				oci_commit($con);
			}
		}

		//Finding requisition data if exists....

		if(count($req_dtls_id_arr)>0)
		{
			$req_dtsl_ids=trim(implode(",", array_filter(array_unique($req_dtls_id_arr))),",");
			if($cbo_item_category_id) $res_sql_category_cond = "and b.item_category=$cbo_item_category_id"; else $res_sql_category_cond= "";
			$req_sql=sql_select("select a.id as req_id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date, b.item_category as item_category_id, b.id as req_dtsl_id, b.product_id as prod_id, b.cons_uom, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.color_id, b.required_for, b.quantity, b.rate, b.amount 
				from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, gbl_temp_report_id c 
				where a.id=b.mst_id and b.id=c.ref_val and c.ref_from=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name $res_sql_category_cond ");

			$req_data_array=array();
			foreach($req_sql as $row)
			{
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["req_id"]=$row[csf("req_id")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["requ_no"]=$row[csf("requ_no")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["requ_prefix_num"]=$row[csf("requ_prefix_num")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["requisition_date"]=$row[csf("requisition_date")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["store_name"]=$row[csf("store_name")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["pay_mode"]=$row[csf("pay_mode")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["cbo_currency"]=$row[csf("cbo_currency")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["delivery_date"]=$row[csf("delivery_date")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["required_for"]=$row[csf("required_for")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["quantity"]=$row[csf("quantity")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["rate"]=$row[csf("rate")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["amount"]=$row[csf("amount")];

				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["cons_uom"]=$row[csf("cons_uom")];
				if ($cbo_item_category_id==1) 
				{
					$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["item_description"]=$yarn_count_arr[$row[csf("count_id")]]." ".$composition[$row[csf("composition_id")]]." ".$row[csf("com_percent")]." ".$yarn_type[$row[csf("yarn_type_id")]]." ".$color_name_arr[$row[csf("color_id")]];
				}
			}	
			unset($req_sql);
		}


		//finding BTB Lc data....
		if(count($pi_id_arr)>0)
		{
			$sql_btb=sql_select("select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id 
			from com_btb_lc_master_details a, com_btb_lc_pi b, gbl_temp_report_id c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.ref_val and c.ref_from=1 and a.status_active=1 and a.is_deleted=0");
			/*echo "select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.status_active=1 and a.is_deleted=0 and b.pi_id in (".trim(implode(",",$pi_id_arr),",").")";*/
			$btb_data_array=array();
			foreach($sql_btb as $row)
			{
				$btb_data_array[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
				$btb_data_array[$row[csf("pi_id")]]["lc_id"]=$row[csf("lc_id")];
				$btb_data_array[$row[csf("pi_id")]]["lc_number"]=$row[csf("lc_number")];
				$btb_data_array[$row[csf("pi_id")]]["lc_date"]=$row[csf("lc_date")];
				$btb_data_array[$row[csf("pi_id")]]["payterm_id"]=$row[csf("payterm_id")];
				$btb_data_array[$row[csf("pi_id")]]["tenor"]=$row[csf("tenor")];
				$btb_data_array[$row[csf("pi_id")]]["lc_value"]=$row[csf("lc_value")];
				$btb_data_array[$row[csf("pi_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
				$btb_data_array[$row[csf("pi_id")]]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
			}
			unset($sql_btb);
			//Finding Invoice data....
			if($db_type==0)
			{
				$pi_cond="group_concat(a.pi_id) as pi_id";
			}
			else if($db_type==2)
			{
				$pi_cond="LISTAGG(CAST(a.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.pi_id) as pi_id";
			}
			
			$sql_invoice_pay=sql_select("select $pi_cond, b.id as invoice_id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, e.payterm_id, b.id as accept_id 
			from gbl_temp_report_id p, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_btb_lc_master_details e 
			where p.ref_val=a.pi_id and p.ref_from=1 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and e.id = a.com_btb_lc_master_details_id and b.status_active=1 and b.is_deleted=0 
			group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, e.payterm_id");
			
			$inv_pay_data_array=array();
			$temp_inv_array=$temp_accept_id=array();
			foreach($sql_invoice_pay as $row)
			{
				$all_pi_id=array_unique(explode(",",$row[csf("pi_id")]));
				foreach($all_pi_id as $pi_id)
				{
					$inv_pay_data_array[$pi_id]["pi_id"]=$pi_id;
					$inv_pay_data_array[$pi_id]["invoice_id"].=$row[csf("invoice_id")].",";
					$inv_pay_data_array[$pi_id]["invoice_no"].=$row[csf("invoice_no")].",";
					$inv_pay_data_array[$pi_id]["document_value"]+=$row[csf("document_value")];
					$inv_pay_data_array[$pi_id]["invoice_date"]=$row[csf("invoice_date")];
					$inv_pay_data_array[$pi_id]["inco_term"]=$row[csf("inco_term")];
					$inv_pay_data_array[$pi_id]["inco_term_place"]=$row[csf("inco_term_place")];
					$inv_pay_data_array[$pi_id]["bill_no"]=$row[csf("bill_no")];
					$inv_pay_data_array[$pi_id]["bill_date"]=$row[csf("bill_date")];
					$inv_pay_data_array[$pi_id]["mother_vessel"]=$row[csf("mother_vessel")];
					$inv_pay_data_array[$pi_id]["feeder_vessel"]=$row[csf("feeder_vessel")];
					$inv_pay_data_array[$pi_id]["container_no"]=$row[csf("container_no")];
					$inv_pay_data_array[$pi_id]["doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$inv_pay_data_array[$pi_id]["bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$inv_pay_data_array[$pi_id]["maturity_date"]=$row[csf("maturity_date")];

					if($temp_inv_array[$row[csf("invoice_id")]]=="")
					{
						$temp_inv_array[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
						$inv_pay_data_array[$pi_id]["pkg_quantity"]+=$row[csf("pkg_quantity")];
						
					}

					/*if($row[csf("payterm_id")]==1) //Pay Term = At sight
					{
						$inv_pay_data_array[$pi_id]["payment_date"]=$row[csf("invoice_date")];

						$cumulative_array=return_library_array("select pi_id, sum(current_acceptance_value) as accepted_ammount from com_import_invoice_dtls where pi_id=$pi_id and status_active=1 and is_deleted=0 group by pi_id",'pi_id','accepted_ammount'); 
						$inv_pay_data_array[$pi_id]["accepted_ammount"]=$cumulative_array[$pi_id];
					}*/
				}
				
			}

			//Finding invoice Payment data
			if ($db_type==0)
			{
				$sql_invoice_pay2= sql_select("select group_concat(a.pi_id) as pi_id, c.payment_date, c.id as accept_id, c.accepted_ammount 
				from gbl_temp_report_id p, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_import_payment c 
				where p.ref_val=a.pi_id and p.ref_from=1 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and b.id=c.invoice_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
				group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, c.payment_date, c.id, c.accepted_ammount");
			}
			else
			{
				$sql_invoice_pay2= sql_select("select LISTAGG(CAST(a.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.pi_id) as pi_id, c.payment_date, c.id as accept_id, c.accepted_ammount 
				from gbl_temp_report_id p, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_import_payment c 
				where p.ref_val=a.pi_id and p.ref_from=1 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and b.id=c.invoice_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
				group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, c.payment_date, c.id, c.accepted_ammount");
			}	
			foreach($sql_invoice_pay2 as $row)
			{
				$all_pi_id=array_unique(explode(",",$row[csf("pi_id")]));
				foreach($all_pi_id as $pi_id)
				{
					$inv_pay_data_array[$pi_id]["payment_date"]=$row[csf("payment_date")];
				}
				if($temp_accept_id[$row[csf("accept_id")]]=="")
				{
					$temp_accept_id[$row[csf("accept_id")]]=$row[csf("accept_id")];
					$inv_pay_data_array[$pi_id]["accepted_ammount"]+=$row[csf("accepted_ammount")];
				}
			}
			//----------------------------------End-------------------------------------------------------
		}

		if($cbo_item_category_id) $sql_receive_category_cond = " and b.item_category in($cbo_item_category_id)"; else $sql_receive_category_cond ="";
		if ($cbo_item_category_id==1) 
		{
			$sql_receive=sql_select("select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, c.product_name_details, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt 
			from inv_receive_master a, inv_transaction b, product_details_master c where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) $sql_receive_category_cond and b.prod_id=c.id and c.status_active=1 and c.is_deleted=0 group by a.receive_basis, b.prod_id,pi_wo_batch_no, c.product_name_details");
			$recv_data_array=array();
			foreach($sql_receive as $row)
			{
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["receive_basis"]=$row[csf("receive_basis")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["prod_id"]=$row[csf("prod_id")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["rcv_amt"]=$row[csf("rcv_amt")];
			}
		}
		else
		{
			$sql_receive=sql_select("select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt from inv_receive_master a, inv_transaction b where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) $sql_receive_category_cond group by a.receive_basis, b.prod_id,pi_wo_batch_no");
			$recv_data_array=array();
			foreach($sql_receive as $row)
			{
				$recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["receive_basis"]=$row[csf("receive_basis")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["rcv_amt"]=$row[csf("rcv_amt")];
			}
		}
		unset($sql_receive);
		$gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
		if($gblDel)
		{
			oci_commit($con);disconnect($con);
		}
		$item_group_array = return_library_array("select id,item_name from  lib_item_group ","id","item_name");
		$store_array = return_library_array("select id,store_name from  lib_store_location ","id","store_name");
		$suplier_array = return_library_array("select id,supplier_name from  lib_supplier ","id","supplier_name");
		$indentor_name_array = return_library_array("select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b where a.id = b.supplier_id and b.party_type = 40","id","supplier_name");
		ob_start();
		?>
			<div style="width:5660px; margin-left:10px">
	        <fieldset style="width:100%;">	 
	            <table width="5500" cellpadding="0" cellspacing="0" id="caption">
	                <tr>
	                	<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	                </tr> 
	                <tr>  
	                	<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
	                </tr>  
	            </table>
	        	<br />
	            
	                <?
					$i=1;
					$btb_tem_lc_array=$inv_temp_array=array();
					foreach ($sql_pi_result as  $row)
					{
						$wo_no=$wo_supplier="";$wo_qnty=$wo_rate=$wo_amount=$wo_balance=0;
						$pi_no=$pi_date=$pi_suplier=$pi_indore_name=$pi_id_all=$rcv_qnty=$rcv_value=$wo_mst_id_all=$pipe_line=$short_value=$pipe_pi_qnty=$pipe_wo_qnty=""; $pi_rate=0;
						$lc_date=$lc_no=$lc_pay_term=$lc_tenor=$lc_amt=$lc_ship_date=$lc_expire_date="";
						$invoice_id=$invoice_no=$invoice_date=$inco_term=$inco_term_place=$bl_no=$bl_date=$mother_vasel=$feder_vasel=$continer_no=$pakag_qnty=$doc_send_cnf=$bill_entry_no=$maturity_date=$maturity_month=$pay_date=$pay_amt="";
						
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						if (!in_array($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"], $checkCateArr))
						{
							$checkCateArr[] = $pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"];
							if($i>1)
							{
								?>
								</tbody>
	            				</table>
								<?
							}
							
							$j=1;



						?>
						<br>
						<table width="5660" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
	                <thead>
	                	<tr>
	                		<th colspan="63" style="text-align: left !important; color: black"><? echo $item_category[$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?> :</th>
	                	</tr>
	                    <tr>
	                        <th colspan="13" >Requisiton Details</th>
	                        <th colspan="12" >Work Order Details</th>
	                        <th colspan="10" >PI Details</th>
	                        <th colspan="7">L/C Details</th>
	                        <th colspan="13">Invoice Details</th>
	                        <th colspan="4">Payment Details</th>
	                        <th colspan="4">Store details</th>
	                    </tr>
	                    <tr>
	                        <!--1210 requisition details-->    
	                        <th width="30">SL</th>
	                        <th width="50">Req. No</th>
	                        <th width="70">Req. Date</th>
	                        <th width="150">Store Name</th>
	                        <th width="70">Delivery Date</th>
	                        <th width="100">Item Category</th>
	                        <th width="100">Item Group</th> 
	                        <th width="100">Item Sub. Group</th>
	                        <th width="80">Item Code</th>
	                        <th width="150">Item Description</th>
	                        <th width="100">Required For</th>
	                        <th width="70"> UOM</th>
	                        <th width="100">Req. Quantity </th>
	                        
	                        <!--1110 wo details-->
	                        <th width="50">WO No</th>
	                        <th width="100">Item Category</th>
	                        <th width="100">Item Group</th>
	                        <th width="100">Item Sub. Group</th>
	                        <th width="80">Item Code</th>
	                        <th width="150">Item Description</th>
	                        <th width="80">WO Qnty</th>
	                        <th width="80">Wo Rate</th>
	                        <th width="80">WO Amount</th>
	                        <th width="70">WO Date</th>
	                        <th width="80">WO Balance</th>
	                        <th width="150">Supplier</th>
	                        
	                        <!--840 pi details-->
	                        <th width="130">PI No</th>
	                        <th width="70">PI Date</th>
	                        <th width="150">Supplier</th>
	                        <th width="100">Item Category</th>
	                        <th width="70">UOM</th>
	                        <th width="80">PI Quantity</th>
	                        <th width="80">Unit Price</th>
	                        <th width="80">PI Value</th>
	                        <th width="70">Currency</th>
	                        <th width="100">Indentor Name</th>
	                        
	                        <!--550 lc details-->
	                        <th width="70">LC Date</th>
	                        <th width="120">LC No</th>
	                        <th width="80">Pay Term</th>
	                        <th width="50">Tenor</th>
	                        <th width="80">LC Amount</th>
	                        <th width="70">Shipment Date</th>
	                        <th width="80">Expiry Date</th>
	                        
	                        <!--1100 Invoice details-->
	                        <th width="150">Invoice No</th>
	                        <th width="70">Invoice Date</th>
	                        <th width="80">Incoterm</th>
	                        <th width="100">Incoterm Place</th>
	                        <th width="80">B/L No</th>
	                        <th width="70">BL Date</th>
	                        <th width="100">Mother Vassel</th>
	                        <th width="100">Feedar Vassel</th>
	                        <th width="100">Continer No</th>
	                        <th width="80">Pkg Qty</th>
	                        <th width="100">Doc Send to CNF</th>
	                        <th width="70">NN Doc Received Date</th>
	                        <th width="80">Bill Of Entry No</th>
	                        
	                        <!--290 Payment details-->
	                        <th width="70">Maturity Date</th>
	                        <th width="70">Maturity Month</th>
	                        <th width="70">Payment Date</th>
	                        <th width="80">Paid Amount</th>
	                        
	                        <!--340 MRR details-->
	                        <th width="80">MRR Qnty</th>
	                        <th width="80">MRR Value</th>
	                        <th width="80">Short Value</th>
	                        <th >Pipeline</th>
	                    </tr>
	                </thead>
	            </table>
	            <!-- <div style="width:5660px; max-height:300px; overflow-y:scroll" id="scroll_body"> -->
	            <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body">
	            	<tbody>
	            		<? 
	            			}
	            		?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

	                    	<? //------------------------------Requisition dtls start----------------------------------------- ?>
	                        <td width="30" align="center"><p><? echo $j; ?></p></td>
	                        <?
	                        	$requ_dtls_id=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["requisition_dtls_id"];
	                        ?>
	                        <td width="50" align="center"><p><? echo $req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["requ_prefix_num"]; ?></p></td>
	                        <td width="70" align="center"><p>
	                        	<? 
	                        	$req_date=$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["requisition_date"];
	                        	if($req_date!="" && $req_date!="0000-00-00") echo change_date_format($req_date); 
	                        	?></p>
	                        </td>
	                        <td width="150"><p><? echo $store_array[$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["store_name"]]; ?></p></td>
	                        <td width="70" align="center"><p>
	                        	<? 
	                        	$delivry_date=$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["delivery_date"];
	                        	if($delivry_date!="" && $delivry_date!="0000-00-00") echo change_date_format($delivry_date); 
	                        	?></p>
	                        </td>
							<td width="100"><p><? echo $item_category[$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>

							<? $prod_id=$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["prod_id"]; ?>

	                        <td width="100"><p><? echo $item_group_array[$prod_data_array[$prod_id]["item_group_id"]]; ?></p></td>
	                        <td width="100"><p><? echo $prod_data_array[$prod_id]["sub_group_name"]; ?></p></td>
	                        <td width="80"><p><? echo $prod_data_array[$prod_id]["item_code"]; ?></p></td>
	                        <td width="150"><p>
	                        	<? 
	                        		if($cbo_item_category_id==1)
	                        		{
	                        			echo $req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["item_description"];
	                        		}
	                        		else
	                        		{
	                        			echo $prod_data_array[$prod_id]["item_description"]; 
	                        		}
	                        	?>
	                        </p></td>
	                        <td width="100"><p><? echo $req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["required_for"]; ?></p></td>
	                        <td width="70"><p>
	                        	<? 
	                        		echo $unit_of_measurement[$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["cons_uom"]]; 
	                        	?>
	                        </p></td>
	                        <td width="100" align="right"><p>
	                        	<? 
	                        		$req_qty=$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["quantity"];
	                        		echo number_format($req_qty,2);
	                        		$total_req_qnty+=$req_qty; 
	                        	?>
	                        </p></td>
	                        <? //------------------------------WO dtls start------------------------------------------- ?>
	             
							<td width="50" align="center"><p>
							<?
							$wo_no=implode(",",array_unique(explode(",",chop($wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["wo_number_prefix_num"]," , "))));
							echo $wo_no;
							?>
							</p></td>
							<td width="100"><p><? echo $item_category[$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>

							<? $wo_prod_id=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["prod_id"]; ?>

							<td width="100"><p><? echo $item_group_array[$prod_data_array[$wo_prod_id]["item_group_id"]]; ?></p></td> 
							<td width="100"><p><? echo $prod_data_array[$wo_prod_id]["sub_group_name"]; ?></p></td>
							<td width="80"><p><? echo $prod_data_array[$wo_prod_id]["item_code"]; ?></p></td>
							<td width="150"><p>
								<? 
									if ($cbo_item_category_id==1) 
									{
										echo $wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_description"];
									}
									else
									{
										echo $prod_data_array[$wo_prod_id]["item_description"]; 
									}
								?>
							</p></td>
							<td width="80" align="right"><p>
								<? 
								echo number_format($wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"],2); 
								$total_wo_qnty+=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"]; 
								?>
							</p></td>
							<td width="80" align="right"><p>
								<? 
								$wo_rate=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]/$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"]; 
								echo number_format($wo_rate,2);  
								?>
							</p></td>
							<td width="80" align="right"><p>
								<? 
								echo number_format($wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"],2); 
								$total_wo_amt+=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]; 
								?>
							</p></td>
							<td width="70" title="last wo date" align="center"><p>
								<? 
								$wo_po_date=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["wo_date"];
								if($wo_po_date!="" && $wo_po_date!="0000-00-00") echo change_date_format($wo_po_date); 
								?>
							</p></td>
							<td width="80" align="right" title="Requisition Quantity-Wo Quantity"><p>
                            <?
                            if($req_qty !=0 || $req_qty !="")
                            {
								$wo_balance=$req_qty-$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"];
								echo number_format($wo_balance,2); 
							}
							$total_wo_balance+=$wo_balance;
							?>
                            </p></td>
							<td width="150"><p>
								<? $wo_supplier=$suplier_array[$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]]; 
								   echo $wo_supplier;
								?>
							</p></td>

							<? //------------------------------PI dtls start------------------------------------------- ?>
								<?
							if(chop($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_id"],",") !="")
							{
								?>
								<td width="130" align="center"><p>
								<?
								$pi_no=implode(",",array_unique(explode(",",chop($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_number"],","))));
								echo $pi_no;
								?>
								</p></td>
								<td width="70" align="center" title="Last PI Date"><p>
									<? 
									$pi_date_data=$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_date"];
									if($pi_date_data!="" && $pi_date_data!="0000-00-00") echo change_date_format($pi_date_data); 
									?>
								</p></td>
								<td width="150"><p>
								<? 
								$pi_suplier=$suplier_array[$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]]; 
								echo $pi_suplier; 
								?> 
								</p></td>
								<td width="100"><p><? echo $item_category[$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>
								<td width="70" align="center"><P><? echo $unit_of_measurement[$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["uom"]]; ?></P></td>
								<td width="80" align="right"><p>
									<? 
									echo number_format($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["quantity"],2); 
									$total_pi_qnty+=$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["quantity"]; 
									?>
								</p></td>
								<td width="80" align="right"><P>
									<? 
									$pi_rate=$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]/$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["quantity"]; 
									echo number_format($pi_rate,2); 
									?>
								</P></td>
								<td width="80" align="right"><p>
									<? 
									echo number_format($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"],2); 
									$total_pi_amt+=$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]; 
									?>
								</p></td>
								<td width="70"><P><? echo $currency[$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["currency_id"]]; ?></P></td>
								<td width="100"><p>
									<? 
									$pi_indore_name=$indentor_name_array[$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["intendor_name"]]; 
									echo $pi_indore_name; 
									?>
								</p></td>

								<? //------------------------------LC dtls start------------------------------------------- ?>


	                            <?
								$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_id"],",")));

								foreach($pi_id_arr as $piID)
								{	
									if(!in_array($btb_data_array[$piID]["lc_id"],$btb_tem_lc_array))
									{
										$btb_tem_lc_array[$btb_data_array[$piID]["lc_id"]]=$btb_data_array[$piID]["lc_id"];
										$lc_date=$btb_data_array[$piID]["lc_date"];
										$lc_no.=$btb_data_array[$piID]["lc_number"].",";
										$lc_pay_term=$pay_term[$btb_data_array[$piID]["payterm_id"]];
										$lc_tenor+=$btb_data_array[$piID]["tenor"];
										$lc_amt+=$btb_data_array[$piID]["lc_value"];
										$lc_ship_date=$btb_data_array[$piID]["last_shipment_date"];
										$lc_expire_date=$btb_data_array[$piID]["lc_expiry_date"];
									}
									
									if(!in_array($inv_pay_data_array[$piID]["invoice_id"],$inv_temp_array))
									{
										$inv_temp_array[$inv_pay_data_array[$piID]["invoice_id"]]=$inv_pay_data_array[$piID]["invoice_id"];
										$invoice_id.=$inv_pay_data_array[$piID]["invoice_id"].",";
										$invoice_no.=$inv_pay_data_array[$piID]["invoice_no"].",";
										$invoice_date=$inv_pay_data_array[$piID]["invoice_date"];
										$inco_term=$inv_pay_data_array[$piID]["inco_term"];
										$inco_term_place=$inv_pay_data_array[$piID]["inco_term_place"];
										$bl_no=$inv_pay_data_array[$piID]["bill_no"];
										$bl_date=$inv_pay_data_array[$piID]["bill_date"];
										$mother_vasel=$inv_pay_data_array[$piID]["mother_vessel"];
										$feder_vasel=$inv_pay_data_array[$piID]["feeder_vessel"];
										$continer_no=$inv_pay_data_array[$piID]["container_no"];
										$pakag_qnty=$inv_pay_data_array[$piID]["pkg_quantity"];
										$doc_send_cnf=$inv_pay_data_array[$piID]["doc_to_cnf"];
										$bill_entry_no=$inv_pay_data_array[$piID]["bill_of_entry_no"];
										$maturity_date=$inv_pay_data_array[$piID]["maturity_date"];
										$maturity_month=$inv_pay_data_array[$piID]["maturity_date"];
										
										$pay_date=$inv_pay_data_array[$piID]["payment_date"];
										$pay_amt+=$inv_pay_data_array[$piID]["accepted_ammount"];
									}	
								}
								?>
								<td width="70" align="center"><P><? if($lc_date!="" && $lc_date!="0000-00-00") echo change_date_format($lc_date); ?></P></td>
								<td width="120"><P><? echo $lc_no=implode(",",array_unique(explode(",",chop($lc_no," , ")))); ?></P></td>
								<td width="80"><P><? echo $lc_pay_term; ?></P></td>
								<td width="50" align="center"><P><? echo $lc_tenor; ?></P></td>
								<td width="80" align="right"><P><? echo number_format($lc_amt,2); $total_lc_amt+=$lc_amt; ?></P></td>
								<td width="70" align="center" title="Last Ship Date"><P><? if($lc_ship_date!="" && $lc_ship_date!="0000-00-00") echo change_date_format($lc_ship_date); ?></P></td>
								<td width="80"  align="center" title="Last Expire Date"><P><? if($lc_expire_date!="" && $lc_expire_date!="0000-00-00") echo change_date_format($lc_expire_date); ?></P></td>

								<? //------------------------------Invoice dtls start------------------------------------------- ?>
								
								<td width="150"><P><? echo $invoice_no=implode(",",array_unique(explode(",",chop($invoice_no," , ")))); ?></P></td>
								<td width="70" align="center" title="Last Invoice Date"><P><? if($invoice_date!="" && $invoice_date!="0000-00-00") echo change_date_format($invoice_date); ?></P></td>
								<td width="80"><P><? echo $inco_term; ?></P></td>
								<td width="100"><P><? echo $inco_term_place; ?></P></td>
								<td width="80"><P><? echo $bl_no; ?></P></td>
								<td width="70" align="center"><P><? if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?></P></td>
								<td width="100"><P><? echo $mother_vasel; ?></P></td>
								<td width="100"><P><? echo $feder_vasel; ?></P></td>
								<td width="100"><P><? echo $continer_no; ?></P></td>
								<td width="80" align="right"><P><? echo number_format($pakag_qnty,2); $total_pkg_qnty+=$pakag_qnty; ?></P></td>
								<td width="100" align="center"><P><? if($doc_send_cnf!="" && $doc_send_cnf!="0000-00-00") echo change_date_format($doc_send_cnf); ?></P></td>
								<td width="70"></td>
								<td width="80"><P><? echo $bill_entry_no; ?></P></td>
								
								<td width="70" align="center"><P><? if($maturity_date!="" && $maturity_date!="0000-00-00") echo change_date_format($maturity_date); ?></P></td>
								<td width="70" align="center"><P><? if($maturity_month!="" && $maturity_month!="0000-00-00") echo change_date_format($maturity_month); ?></P></td>
								<td width="70" align="center"><P><? if($pay_date!="" && $pay_date!="0000-00-00") echo change_date_format($pay_date); ?></P></td>
								<td width="80" align="right"><P><? echo number_format($pay_amt,2); $total_pay_amt+=$pay_amt; ?></P></td>
								<?
							}
							else
							{
								?>
								<td width="130"></td>
								<td width="70"></td>
								<td width="150"></td>
								<td width="100"></td>
								<td width="70"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="100"></td>
	                            
								<td width="70"></td>
								<td width="120"></td>
								<td width="80"></td>
								<td width="50"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="80"></td>
								
								<td width="150"></td>
								<td width="70"></td>
								<td width="80"></td>
								<td width="100"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="80"></td>
								<td width="100"></td>
								<td width="70"></td>
								<td width="80"></td>
								
								<td width="70"></td>
								<td width="70"></td>
								<td width="70"></td>
								<td width="80"></td>
								<?
							}

							//---------------------------------Store Details starts----------------------------------

							$pi_id=chop($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_id"],",");
							if ($pi_id=='') {
								$pi_id=chop($wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["wo_mst_id"],",");
							}

							if ($cbo_item_category_id==1) 
                        	{
                        		$item_desc=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_description"];
                        		$receive_basis=$recv_data_array[$pi_id][$item_desc]["receive_basis"];
                        		$prod_id=$recv_data_array[$pi_id][$item_desc]["prod_id"];
                        	}
                        	else
                        	{
                        		$receive_basis=$recv_data_array[$pi_id][$row[csf("prod_id")]]["receive_basis"];
                        		$prod_id=$row[csf("prod_id")];
                        	}
	
							$data=$pi_id."**".$receive_basis;
							?>
	                        <td width="80" align="right"><p><a href="##" onClick="openmypage_popup('<? echo $data; ?>','<? echo $prod_id; ?>','Receive Info','receive_popup');" > 
	                        	<? 
	                        		if ($cbo_item_category_id==1) 
	                        		{
	                        			$rcv_qnty=$recv_data_array[$pi_id][$item_desc]["rcv_qnty"];
	                        		}
	                        		else
	                        		{
	                        			$rcv_qnty=$recv_data_array[$pi_id][$row[csf("prod_id")]]["rcv_qnty"];
	                        		}             		

	                        		echo number_format($rcv_qnty,2); 
	                        		$total_mrr_qnty+=$rcv_qnty; 
	                        	?> 
	                        </a></p>
	                        </td>
	                        <td width="80" align="right"><p>
	                        	<? 
	                        		if ($cbo_item_category_id==1)
	                        		{
	                        			$rcv_value=$recv_data_array[$pi_id][$item_desc]["rcv_amt"];
	                        		}
	                        		else
	                        		{
	                        			$rcv_value=$recv_data_array[$pi_id][$row[csf("prod_id")]]["rcv_amt"];
	                        		}
	                        		
	                        		echo number_format($rcv_value,2); 
	                        		$total_mrr_amt+=$rcv_value; 
	                        	?></p>
	                        </td>
	                        <td align="right" title="Wo Value-Receive Value" width="80"><p>
	                        	<? 
	                        		$short_value=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]-$rcv_value;
	                        		echo number_format($short_value,2);  
	                        		$total_short_amt+=$short_value; 
	                        	?></p>
	                        </td>
	                        <?
	                        $pipe_pi_qnty=$pi_data_arr[$pi_id][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["quantity"];
	                       	$pipe_wo_qnty=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"];

							if($pipe_pi_qnty!="" && $pipe_wo_qnty =="")
							{
								$pipe_line=$pipe_pi_qnty-$rcv_qnty;
							}
							else
							{
								$pipe_line=$pipe_wo_qnty-$rcv_qnty;
							}
							?>
	                        <td  align="right"><P> 
	                        	<? 
	                        	echo number_format($pipe_line,2); 
	                        	$total_pipe_line+=$pipe_line;
	                        	?></P>
	                        </td>
	                    </tr>
	                    <?
						$i++;
						$pipe_wo_qnty=0;
						$j++;
					}
					?>
	                </tbody>
	            </table>
	            <!-- </div> -->
	            <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_footer">
	            	<tfoot>
	                    <tr>
	                        <!--1210 requisition details-->
	                        <th width="30"></th>
	                        <th width="50"></th>
	                        <th width="70"></th>
	                        <th width="150"></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="100"></th> 
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="150"></th>
	                        <th width="100"></th>
	                        <th width="70"> </th>
	                        <th width="100" id="value_total_req_qnty" align="right"><? echo number_format($total_req_qnty,0); ?> </th>
	                        
	                        <!--1110 wo details-->
	                        <th width="50"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="150"></th>
	                        <th width="80" id="value_total_wo_qnty" align="right"><? echo number_format($total_wo_qnty,0); ?></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_wo_amt" align="right"><? echo number_format($total_wo_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_wo_balance" align="right"><? echo number_format($total_wo_balanc,2); ?></th>
	                        <th width="150"></th>
	                        
	                        <!--840 pi details-->
	                        <th width="130"></th>
	                        <th width="70"></th>
	                        <th width="150"></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_pi_qnty" align="right"><? echo number_format($total_pi_qnty,0); ?></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_pi_amt" align="right"><? echo number_format($total_pi_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        
	                        <!--550 lc details-->
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_lc_amt" align="right"><? echo number_format($total_lc_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        
	                        <!--1100 Invoice details-->
	                        <th width="150"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="80" id="value_total_pkg_qnty" align="right"><? echo number_format($total_pkg_qnty,0); ?></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        
	                        <!--290 Payment details-->
	                        <th width="70"></th>
	                        <th width="70"></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_pay_amt" align="right"><? echo number_format($total_pay_amt,2); ?></th>
	                        
	                        <!--340 MRR details-->
	                        <th width="80" id="value_total_mrr_qnty" align="right"><? echo number_format($total_mrr_qnty,0); ?></th>
	                        <th width="80" id="value_total_mrr_amt" align="right"><? echo number_format($total_mrr_amt,2); ?></th>
	                        <th width="80" id="value_total_short_amt" align="right"><? echo number_format($total_short_amt,2); ?></th>
	                        <th id="value_total_pipe_line" align="right"><? echo number_format($total_pipe_line,2); ?></th>
	                    </tr>
	                </tfoot>
	            </table>
	        </fieldset>
	    	</div>
		<?
	}
	
	else if($txt_req_no != "" && $txt_wo_po_no == "" && $txt_pi_no == "")
	{	
		//echo "here "."$txt_req_no && $cbo_supplier==0 && $txt_wo_po_no=='' && $cbo_date_type!=1";die;
		
		$gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
		if($gblDel)
		{
			oci_commit($con);
			//disconnect($con);
		}
		$temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
		if($temp_table_id=="") $temp_table_id=1;
		
		$sql_cond="";
		if($cbo_company_name>0) $sql_cond.=" and a.company_id='$cbo_company_name' ";
		if($cbo_item_category_id>0) $sql_cond.=" and b.item_category='$cbo_item_category_id' ";
		if($txt_req_no!="") 
		{
			$sql_cond.=" and a.requ_prefix_num = '$txt_req_no' ";
		}
        if($cbo_date_type == 1 && $txt_date_from  != "" && $txt_date_to != "" ){
            $sql_cond.=" and a.requisition_date between  '$txt_date_from' and '$txt_date_to'";
        }
		$req_sql=sql_select("select a.id as req_id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date, a.item_category_id,b.item_category, b.id as req_dtsl_id, b.product_id as prod_id, b.required_for, b.quantity, b.rate, b.amount 
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond order by b.item_category,a.requ_no"); //and b.item_category not in (1,2,3,12,13,14,24,25,28,30)


		if(count($req_sql) < 1)
		{
			echo "<span style='font-size:23;font-weight:bold;text-align:center;width:100%'>Data Not Found</span>";die;
		}

		$req_data_array=$req_dtls_id_arr=array();
		foreach($req_sql as $row)
		{
			$req_dtls_id_arr[$row[csf("req_dtsl_id")]]=$row[csf("req_dtsl_id")];
			
			if($row[csf("req_dtsl_id")])
			{
				$refrID1=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("req_dtsl_id")].",1,".$user_id.")");
				if(!$refrID1)
				{
					echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("req_dtsl_id")].",1,".$user_id.")";oci_rollback($con);disconnect($con);die;
				}
				$temp_table_id++;
			}
			
			$req_data_array[$row[csf("req_dtsl_id")]]["req_id"]=$row[csf("req_id")];
			$req_data_array[$row[csf("req_dtsl_id")]]["requ_no"]=$row[csf("requ_no")];
			$req_data_array[$row[csf("req_dtsl_id")]]["requ_prefix_num"]=$row[csf("requ_prefix_num")];
			$req_data_array[$row[csf("req_dtsl_id")]]["requisition_date"]=$row[csf("requisition_date")];
			$req_data_array[$row[csf("req_dtsl_id")]]["store_name"]=$row[csf("store_name")];
			$req_data_array[$row[csf("req_dtsl_id")]]["pay_mode"]=$row[csf("pay_mode")];
			$req_data_array[$row[csf("req_dtsl_id")]]["cbo_currency"]=$row[csf("cbo_currency")];
			$req_data_array[$row[csf("req_dtsl_id")]]["delivery_date"]=$row[csf("delivery_date")];
			$req_data_array[$row[csf("req_dtsl_id")]]["item_category_id"]=$row[csf("item_category")];
			$req_data_array[$row[csf("req_dtsl_id")]]["prod_id"]=$row[csf("prod_id")];
			$req_data_array[$row[csf("req_dtsl_id")]]["required_for"]=$row[csf("required_for")];
			$req_data_array[$row[csf("req_dtsl_id")]]["quantity"]=$row[csf("quantity")];
			$req_data_array[$row[csf("req_dtsl_id")]]["rate"]=$row[csf("rate")];
			$req_data_array[$row[csf("req_dtsl_id")]]["amount"]=$row[csf("amount")];
		}
		unset($req_sql);
		
		if($refrID1)
		{
			oci_commit($con);
		}
		
		//var_dump($req_dtls_id_arr);die;
		if(!empty($req_dtls_id_arr))
		{
			//and b.requisition_dtls_id in(".implode(",",$req_dtls_id_arr).") 
			if($cbo_item_category_id) $wo_category_cond = "and b.item_category_id in($cbo_item_category_id) "; else $wo_category_cond = "";
			$sql_wo=sql_select("select a.id as wo_mst_id, a.wo_number, a.wo_number_prefix_num, a.wo_date, a.supplier_id, b.id as wo_dtls_id, b.requisition_dtls_id, b.item_id as prod_id, b.supplier_order_quantity, b.rate, b.amount 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b, gbl_temp_report_id c 
			where a.id=b.mst_id and b.requisition_dtls_id=c.ref_val and c.ref_from=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name $wo_category_cond and b.item_category_id not in (1,2,3,12,13,14,24,25,28,30) ");       //$supplier_cond
			$wo_data_array=array();
			foreach($sql_wo as $row)
			{
				$wo_data_array[$row[csf("requisition_dtls_id")]]["wo_mst_id"].=$row[csf("wo_mst_id")].",";
				$wo_data_array[$row[csf("requisition_dtls_id")]]["wo_number"].=$row[csf("wo_number")].",";
				$wo_data_array[$row[csf("requisition_dtls_id")]]["wo_number_prefix_num"].=$row[csf("wo_number_prefix_num")].",";
				$wo_data_array[$row[csf("requisition_dtls_id")]]["wo_date"]=$row[csf("wo_date")];
				$wo_data_array[$row[csf("requisition_dtls_id")]]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
				$wo_data_array[$row[csf("requisition_dtls_id")]]["supplier_id"]=$row[csf("supplier_id")];
				$wo_data_array[$row[csf("requisition_dtls_id")]]["prod_id"].=$row[csf("prod_id")].",";
				$wo_data_array[$row[csf("requisition_dtls_id")]]["supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
				$wo_data_array[$row[csf("requisition_dtls_id")]]["amount"]+=$row[csf("amount")];
			}
			unset($sql_wo);
			if ($cbo_item_category_id) $pi_category_cond = " and c.item_category_id in($cbo_item_category_id)";else $pi_category_cond = "";
			$sql_pi="select a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, c.item_category_id as item_category_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, b.item_prod_id as prod_id, b.uom, b.quantity, b.amount, c.id as wo_dtls_id, c.requisition_dtls_id 
			from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c, gbl_temp_report_id d 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.requisition_dtls_id=d.ref_val and d.ref_from=1 and c.item_category_id not in (1,2,3,12,13,14,24,25,28,30) $pi_category_cond ";
			//echo $sql_pi;
			$sql_pi_result=sql_select($sql_pi);
			$pi_data_arr=$pi_id_arr=array();$refrID2=1;
			foreach($sql_pi_result as $row)
			{
				$pi_id_arr[$row[csf("pi_id")]]=$row[csf("pi_id")];
				
				if($row[csf("pi_id")])
				{
					$refrID2=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",2,".$user_id.")");
					if(!$refrID2)
					{
						echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",2,".$user_id.")";oci_rollback($con);disconnect($con);die;
					}
					$temp_table_id++;
				}
			
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["requisition_dtls_id"]=$row[csf("requisition_dtls_id")];
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["pi_id"].=$row[csf("pi_id")].",";
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["pi_number"].=$row[csf("pi_number")].",";
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["pi_date"]=$row[csf("pi_date")];
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["supplier_id"]=$row[csf("supplier_id")];
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["item_category_id"]=$row[csf("item_category_id")];
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["currency_id"]=$row[csf("currency_id")];
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["intendor_name"]=$row[csf("intendor_name")];
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["prod_id"].=$row[csf("prod_id")].",";
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["uom"]=$row[csf("uom")];
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["quantity"]+=$row[csf("quantity")];
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["amount"]+=$row[csf("amount")];
				$pi_data_arr[$row[csf("requisition_dtls_id")]]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
			}
			
			unset($sql_pi_result); 
			if($refrID2)
			{
				oci_commit($con);
			}
		}
		
		//echo "sstt".count($pi_id_arr);die;
		
		if(!empty($pi_id_arr))
		{
			$sql_btb=sql_select("select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id 
			from com_btb_lc_master_details a, com_btb_lc_pi b, gbl_temp_report_id c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.ref_val and c.ref_from=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$btb_data_array=array();
			foreach($sql_btb as $row)
			{
				$btb_data_array[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
				$btb_data_array[$row[csf("pi_id")]]["lc_id"]=$row[csf("lc_id")];
				$btb_data_array[$row[csf("pi_id")]]["lc_number"]=$row[csf("lc_number")];
				$btb_data_array[$row[csf("pi_id")]]["lc_date"]=$row[csf("lc_date")];
				$btb_data_array[$row[csf("pi_id")]]["payterm_id"]=$row[csf("payterm_id")];
				$btb_data_array[$row[csf("pi_id")]]["tenor"]=$row[csf("tenor")];
				$btb_data_array[$row[csf("pi_id")]]["lc_value"]=$row[csf("lc_value")];
				$btb_data_array[$row[csf("pi_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
				$btb_data_array[$row[csf("pi_id")]]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
			}
			unset($sql_btb); 

			$sql_invoice_pay=sql_select(" select b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, c.accepted_ammount, c.payment_date 
			from com_import_invoice_mst a, com_import_invoice_dtls b, com_import_payment c, gbl_temp_report_id d 
			where a.id = b.import_invoice_id and a.id = c.invoice_id and b.pi_id=d.ref_val and d.ref_from=2 and a.status_active = 1 and b.status_active=1 and c.status_active=1");
			
			$inv_pay_data_array=array();
			$temp_inv_array=$temp_accept_id=array();
			foreach($sql_invoice_pay as $row)
			{
				$pi_id =$row[csf("pi_id")];
				//foreach($all_pi_id as $pi_id)
				//{
					$inv_pay_data_array[$pi_id]["pi_id"]=$pi_id;
					$inv_pay_data_array[$pi_id]["invoice_id"].=$row[csf("invoice_id")].",";
					$inv_pay_data_array[$pi_id]["invoice_no"].=$row[csf("invoice_no")].",";
					$inv_pay_data_array[$pi_id]["document_value"]+=$row[csf("document_value")];
					$inv_pay_data_array[$pi_id]["invoice_date"]=$row[csf("invoice_date")];
					$inv_pay_data_array[$pi_id]["inco_term"]=$row[csf("inco_term")];
					$inv_pay_data_array[$pi_id]["inco_term_place"]=$row[csf("inco_term_place")];
					$inv_pay_data_array[$pi_id]["bill_no"]=$row[csf("bill_no")];
					$inv_pay_data_array[$pi_id]["bill_date"]=$row[csf("bill_date")];
					$inv_pay_data_array[$pi_id]["mother_vessel"]=$row[csf("mother_vessel")];
					$inv_pay_data_array[$pi_id]["feeder_vessel"]=$row[csf("feeder_vessel")];
					$inv_pay_data_array[$pi_id]["container_no"]=$row[csf("container_no")];
					$inv_pay_data_array[$pi_id]["doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$inv_pay_data_array[$pi_id]["bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$inv_pay_data_array[$pi_id]["maturity_date"]=$row[csf("maturity_date")];
					$inv_pay_data_array[$pi_id]["payment_date"]=$row[csf("payment_date")];
					
					//if($temp_inv_array[$row[csf("invoice_id")]]=="")
					//{
					//	$temp_inv_array[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
						$inv_pay_data_array[$pi_id]["pkg_quantity"]+=$row[csf("pkg_quantity")];
						
					//}
					//if($temp_accept_id[$row[csf("accept_id")]]=="")
					//{
					//	$temp_accept_id[$row[csf("accept_id")]]=$row[csf("accept_id")];
						$inv_pay_data_array[$pi_id]["accepted_ammount"]+=$row[csf("accepted_ammount")];
						
					//}
				//}
				
			}
			
			unset($sql_invoice_pay); 
			
				
		}	
	
		if($cbo_item_category_id) $rcv_category_cond = " and b.item_category  in($cbo_item_category_id)"; else $rcv_category_cond= "";

		$sql_receive=sql_select("select a.receive_basis, a.booking_id, b.prod_id, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt 
		from inv_receive_master a, inv_transaction b 
		where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) $rcv_category_cond and b.item_category not in (1,2,3,12,13,14,24,25,28,30) 
		group by a.receive_basis, a.booking_id, b.prod_id");
		$recv_data_array=array();
		foreach($sql_receive as $row)
		{
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("prod_id")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
			$recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("prod_id")]]["rcv_amt"]=$row[csf("rcv_amt")];
		}
	
		if($cbo_item_category_id) $wo_qnty_arr_category_cond = " and  b.item_category_id in ($cbo_item_category_id)" ; else $wo_qnty_arr_category_cond= "";
		$wo_qty_arr=sql_select("select b.requisition_dtls_id, b.item_id as prod_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$cbo_company_name $wo_qnty_arr_category_cond and  b.item_category_id not in (1,2,3,12,13,14,24,25,28,30)  and a.wo_basis_id=1 and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.requisition_dtls_id>0 
		group by b.requisition_dtls_id,b.item_id");
		$wo_pipe_array=array();
		foreach($wo_qty_arr as $row)
		{
			$wo_pipe_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]] += $row[csf("qty")];
		}
		
		if ($cbo_item_category_id) $pi_qnty_arr_category_cond = "  and c.item_category_id in($cbo_item_category_id)"; else $pi_qnty_arr_category_cond = "";
		$pi_qty_arr=sql_select("select c.requisition_dtls_id, b.item_prod_id as prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c, wo_non_order_info_mst d  where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.mst_id=d.id  and a.importer_id=$cbo_company_name $pi_qnty_arr_category_cond and c.item_category_id not in (1,2,3,12,13,14,24,25,28,30) and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.requisition_dtls_id,b.item_prod_id"); 
		$pi_pipe_array=array();
		foreach($pi_qty_arr as $row)
		{
			$pi_pipe_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]=$row[csf("qty")];
		}
		
		$gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
		if($gblDel)
		{
			oci_commit($con);disconnect($con);
		}
		
		$item_group_array = return_library_array("select id,item_name from  lib_item_group ","id","item_name");
		$store_array = return_library_array("select id,store_name from  lib_store_location ","id","store_name");
		$suplier_array = return_library_array("select id,supplier_name from  lib_supplier ","id","supplier_name");
		$indentor_name_array = return_library_array("select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b where a.id = b.supplier_id and b.party_type = 40","id","supplier_name");
		ob_start();
		?>
	    <div style="width:5660px; margin-left:10px">
	        <fieldset style="width:100%;">	 
	            <table width="5500" cellpadding="0" cellspacing="0" id="caption">
	                <tr>
	                	<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	                </tr> 
	                <tr>  
	                	<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
	                </tr>  
	            </table>
	        	<br />
	            
	                <?
					$i=1;$j=1;
					$btb_tem_lc_array=$inv_temp_array=array();
					foreach($req_data_array as $req_dtls_id=>$row)
					{
						$wo_no=$wo_supplier="";$wo_qnty=$wo_rate=$wo_amount=$wo_balance=0;
						$pi_no=$pi_date=$pi_suplier=$pi_indore_name=$pi_id_all=$rcv_qnty=$rcv_value=$wo_mst_id_all=$pipe_line=$short_value=$pipe_pi_qnty=$pipe_wo_qnty=""; $pi_rate=0;
						$lc_date=$lc_no=$lc_pay_term=$lc_tenor=$lc_amt=$lc_ship_date=$lc_expire_date="";
						$invoice_id=$invoice_no=$invoice_date=$inco_term=$inco_term_place=$bl_no=$bl_date=$mother_vasel=$feder_vasel=$continer_no=$pakag_qnty=$doc_send_cnf=$bill_entry_no=$maturity_date=$maturity_month=$pay_date=$pay_amt="";
						
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";

						if (!in_array($row["item_category_id"], $checkCateArr)) {
							$checkCateArr[] = $row["item_category_id"];
							if($i>1)
							{
								?>
								</tbody>
	            				</table>
								<?
							}
							
							$j=1;
						?>
						<table width="5660" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
	                <thead>
	                	<tr>
	                		<th colspan="63" style="text-align: left !important; color: black"><? echo  $item_category[$row["item_category_id"]]; ?> :</th>
	                	</tr>
	                    <tr>
	                        <th colspan="13" >Requisiton Details</th>
	                        <th colspan="12" >Work Order Details</th>
	                        <th colspan="10" >PI Details</th>
	                        <th colspan="7">L/C Details</th>
	                        <th colspan="13">Invoice Details</th>
	                        <th colspan="4">Payment Details</th>
	                        <th colspan="4">Store details</th>
	                    </tr>
	                    <tr>
	                        <!--1210 requisition details-->    
	                        <th width="30">SL</th>
	                        <th width="50">Req. No</th>
	                        <th width="70">Req. Date</th>
	                        <th width="150">Store Name</th>
	                        <th width="70">Delivery Date</th>
	                        <th width="100">Item Category</th>
	                        <th width="100">Item Group</th> 
	                        <th width="100">Item Sub. Group</th>
	                        <th width="80">Item Code</th>
	                        <th width="150">Item Description</th>
	                        <th width="100">Required For</th>
	                        <th width="70"> UOM</th>
	                        <th width="100">Req. Quantity </th>
	                        
	                        <!--1110 wo details-->
	                        <th width="50">WO No</th>
	                        <th width="100">Item Category</th>
	                        <th width="100">Item Group</th>
	                        <th width="100">Item Sub. Group</th>
	                        <th width="80">Item Code</th>
	                        <th width="150">Item Description</th>
	                        <th width="80">WO Qnty</th>
	                        <th width="80">Wo Rate</th>
	                        <th width="80">WO Amount</th>
	                        <th width="70">WO Date</th>
	                        <th width="80">WO Balance</th>
	                        <th width="150">Supplier</th>
	                        
	                        <!--840 pi details-->
	                        <th width="130">PI No</th>
	                        <th width="70">PI Date</th>
	                        <th width="150">Supplier</th>
	                        <th width="100">Item Category</th>
	                        <th width="70">UOM</th>
	                        <th width="80">PI Quantity</th>
	                        <th width="80">Unit Price</th>
	                        <th width="80">PI Value</th>
	                        <th width="70">Currency</th>
	                        <th width="100">Indentor Name</th>
	                        
	                        <!--550 lc details-->
	                        <th width="70">LC Date</th>
	                        <th width="120">LC No</th>
	                        <th width="80">Pay Term</th>
	                        <th width="50">Tenor</th>
	                        <th width="80">LC Amount</th>
	                        <th width="70">Shipment Date</th>
	                        <th width="80">Expiry Date</th>
	                        
	                        <!--1100 Invoice details-->
	                        <th width="150">Invoice No</th>
	                        <th width="70">Invoice Date</th>
	                        <th width="80">Incoterm</th>
	                        <th width="100">Incoterm Place</th>
	                        <th width="80">B/L No</th>
	                        <th width="70">BL Date</th>
	                        <th width="100">Mother Vassel</th>
	                        <th width="100">Feedar Vassel</th>
	                        <th width="100">Continer No</th>
	                        <th width="80">Pkg Qty</th>
	                        <th width="100">Doc Send to CNF</th>
	                        <th width="70">NN Doc Received Date</th>
	                        <th width="80">Bill Of Entry No</th>
	                        
	                        <!--290 Payment details-->
	                        <th width="70">Maturity Date</th>
	                        <th width="70">Maturity Month</th>
	                        <th width="70">Payment Date</th>
	                        <th width="80">Paid Amount</th>
	                        
	                        <!--340 MRR details-->
	                        <th width="80">MRR Qnty</th>
	                        <th width="80">MRR Value</th>
	                        <th width="80">Short Value</th>
	                        <th >Pipeline</th>
	                    </tr>
	                </thead>
	            </table>
	            <!-- <div style="width:5660px; max-height:300px; overflow-y:scroll" id="scroll_body"> -->
	            <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body">
	            	<tbody>
	            		<?
	            			
	            			}
	            		?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="30" align="center"><p><? echo $j; ?></p></td>
	                        <td width="50" align="center"><p><? echo $row["requ_prefix_num"]; ?></p></td>
	                        <td width="70" align="center"><p><? if($row["requisition_date"]!="" && $row["requisition_date"]!="0000-00-00") echo change_date_format($row["requisition_date"]); ?></p></td>
	                        <td width="150"><p><? echo $store_array[$row["store_name"]]; ?></p></td>
	                        <td width="70" align="center"><p><? if($row["delivery_date"]!="" && $row["delivery_date"]!="0000-00-00") echo change_date_format($row["delivery_date"]); ?></p></td>
	                        <td width="100"><p><? echo $item_category[$row["item_category_id"]]; ?></p></td>
	                        <td width="100"><p><? echo $item_group_array[$prod_data_array[$row["prod_id"]]["item_group_id"]]; ?></p></td> 
	                        <td width="100"><p><? echo $prod_data_array[$row["prod_id"]]["sub_group_name"]; ?></p></td>
	                        <td width="80"><p><? echo $prod_data_array[$row["prod_id"]]["item_code"]; ?></p></td>
	                        <td width="150"><p><? echo $prod_data_array[$row["prod_id"]]["item_description"]; ?></p></td>
	                        <td width="100"><p><? echo $row["required_for"]; ?></p></td>
	                        <td width="70"><p><? echo $unit_of_measurement[$prod_data_array[$row["prod_id"]]["unit_of_measure"]]; ?></p></td>
	                        <td width="100" align="right"><p><? echo number_format($row["quantity"],2);$total_req_qnty+=$row["quantity"]; ?></p></td>
	                        
	                        <?
							if(chop($wo_data_array[$req_dtls_id]["wo_mst_id"]," , ")!="")
							{
								?>
								<td width="50" align="center"><p>
								<?
								$wo_no=implode(",",array_unique(explode(",",chop($wo_data_array[$req_dtls_id]["wo_number_prefix_num"]," , "))));
								echo $wo_no;
								?>
								</p></td>
								<td width="100"><p><? echo $item_category[$row["item_category_id"]]; ?></p></td>
								<td width="100"><p><? echo $item_group_array[$prod_data_array[$row["prod_id"]]["item_group_id"]]; ?></p></td> 
								<td width="100"><p><? echo $prod_data_array[$row["prod_id"]]["sub_group_name"]; ?></p></td>
								<td width="80"><p><? echo $prod_data_array[$row["prod_id"]]["item_code"]; ?></p></td>
								<td width="150"><p><? echo $prod_data_array[$row["prod_id"]]["item_description"]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($wo_data_array[$req_dtls_id]["supplier_order_quantity"],2); $total_wo_qnty+=$wo_data_array[$req_dtls_id]["supplier_order_quantity"]; ?></p></td>
								<td width="80" align="right"><p><? $wo_rate=$wo_data_array[$req_dtls_id]["amount"]/$wo_data_array[$req_dtls_id]["supplier_order_quantity"]; echo number_format($wo_rate,2);  ?></p></td>
								<td width="80" align="right"><p><? echo number_format($wo_data_array[$req_dtls_id]["amount"],2); $total_wo_amt+=$wo_data_array[$req_dtls_id]["amount"]; ?></p></td>
								<td width="70" title="last wo date" align="center"><p><? if($wo_data_array[$req_dtls_id]["wo_date"]!="" && $wo_data_array[$req_dtls_id]["wo_date"]!="0000-00-00") echo change_date_format($wo_data_array[$req_dtls_id]["wo_date"]); ?></p></td>
								<td width="80" align="right" title="Requisition Quantity-Wo Quantity"><p>
	                            <?
								$wo_balance=$row["quantity"]-$wo_data_array[$req_dtls_id]["supplier_order_quantity"];
								echo number_format($wo_balance,2); $total_wo_balance+=$wo_balance;
								?>
	                            </p></td>
								<td width="150"><p><? $wo_supplier=$suplier_array[$wo_data_array[$req_dtls_id]["supplier_id"]]; echo $wo_supplier; ?> </p></td>
								<?
							}
							else
							{
								?>
								<td width="50" align="center"></td>
								<td width="100"></td>
								<td width="100"></td> 
								<td width="100"></td>
								<td width="80"></td>
								<td width="150"></td>
								<td width="80" align="right"></td>
								<td width="80" align="right"></td>
								<td width="80" align="right"></td>
								<td width="70"></td>
								<td width="80"></td>
								<td width="150"></td>
								<?
							}
							
							if(chop($pi_data_arr[$req_dtls_id]["pi_id"]," , ") !="")
							{
								?>
								<td width="130" align="center"><p>
								<?
								$pi_no=implode(",",array_unique(explode(",",chop($pi_data_arr[$req_dtls_id]["pi_number"]," , "))));
								echo $pi_no;
								?>
								</p></td>
								<td width="70" align="center" title="Last PI Date"><p><? if($pi_data_arr[$req_dtls_id]["pi_date"]!="" && $pi_data_arr[$req_dtls_id]["pi_date"]!="0000-00-00") echo change_date_format($pi_data_arr[$req_dtls_id]["pi_date"]); ?></p></td>
								<td width="150"><p><? $pi_suplier=$suplier_array[$pi_data_arr[$req_dtls_id]["supplier_id"]]; echo $pi_suplier; ?> </p></td>
								<td width="100"><p><? echo $item_category[$row["item_category_id"]]; ?></p></td>
								<td width="70" align="center"><P><? echo $unit_of_measurement[$pi_data_arr[$req_dtls_id]["uom"]]; ?></P></td>
								<td width="80" align="right"><p><? echo number_format($pi_data_arr[$req_dtls_id]["quantity"],2); $total_pi_qnty+=$pi_data_arr[$req_dtls_id]["quantity"]; ?></p></td>
								<td width="80" align="right"><P><? $pi_rate=$pi_data_arr[$req_dtls_id]["amount"]/$pi_data_arr[$req_dtls_id]["quantity"]; echo number_format($pi_rate,2); ?></P></td>
								<td width="80" align="right"><p><? echo number_format($pi_data_arr[$req_dtls_id]["amount"],2); $total_pi_amt+=$pi_data_arr[$req_dtls_id]["amount"]; ?></p></td>
								<td width="70"><P><? echo $currency[$pi_data_arr[$req_dtls_id]["currency_id"]]; ?></P></td>
								<td width="100"><p><? $pi_indore_name=$indentor_name_array[$pi_data_arr[$req_dtls_id]["intendor_name"]]; echo $pi_indore_name; ?></p></td>
	                            <?
								$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$req_dtls_id]["pi_id"]," , ")));
								
								foreach($pi_id_arr as $piID)
								{	
									if(!in_array($btb_data_array[$piID]["lc_id"],$btb_tem_lc_array))
									{
										$btb_tem_lc_array[$btb_data_array[$piID]["lc_id"]]=$btb_data_array[$piID]["lc_id"];
										$lc_date=$btb_data_array[$piID]["lc_date"];
										$lc_no.=$btb_data_array[$piID]["lc_number"].",";
										$lc_pay_term=$pay_term[$btb_data_array[$piID]["payterm_id"]];
										$lc_tenor+=$btb_data_array[$piID]["tenor"];
										$lc_amt+=$btb_data_array[$piID]["lc_value"];
										$lc_ship_date=$btb_data_array[$piID]["last_shipment_date"];
										$lc_expire_date=$btb_data_array[$piID]["lc_expiry_date"];
									}
									
									if(!in_array($inv_pay_data_array[$piID]["invoice_id"],$inv_temp_array))
									{
										$inv_temp_array[$inv_pay_data_array[$piID]["invoice_id"]]=$inv_pay_data_array[$piID]["invoice_id"];
										$invoice_id.=$inv_pay_data_array[$piID]["invoice_id"].",";
										$invoice_no.=$inv_pay_data_array[$piID]["invoice_no"].",";
										$invoice_date=$inv_pay_data_array[$piID]["invoice_date"];
										$inco_term=$inv_pay_data_array[$piID]["inco_term"];
										$inco_term_place=$inv_pay_data_array[$piID]["inco_term_place"];
										$bl_no=$inv_pay_data_array[$piID]["bill_no"];
										$bl_date=$inv_pay_data_array[$piID]["bill_date"];
										$mother_vasel=$inv_pay_data_array[$piID]["mother_vessel"];
										$feder_vasel=$inv_pay_data_array[$piID]["feeder_vessel"];
										$continer_no=$inv_pay_data_array[$piID]["container_no"];
										$pakag_qnty=$inv_pay_data_array[$piID]["pkg_quantity"];
										$doc_send_cnf=$inv_pay_data_array[$piID]["doc_to_cnf"];
										$bill_entry_no=$inv_pay_data_array[$piID]["bill_of_entry_no"];
										$maturity_date=$inv_pay_data_array[$piID]["maturity_date"];
										$maturity_month=$inv_pay_data_array[$piID]["maturity_date"];
										$pay_date=$inv_pay_data_array[$piID]["payment_date"];
										$pay_amt+=$inv_pay_data_array[$piID]["accepted_ammount"];
									}	
								}
								?>
								<td width="70" align="center"><P><? if($lc_date!="" && $lc_date!="0000-00-00") echo change_date_format($lc_date); ?></P></td>
								<td width="120"><P><? echo $lc_no=implode(",",array_unique(explode(",",chop($lc_no," , ")))); ?></P></td>
								<td width="80"><P><? echo $lc_pay_term; ?></P></td>
								<td width="50" align="center"><P><? echo $lc_tenor; ?></P></td>
								<td width="80" align="right"><P><? echo number_format($lc_amt,2); $total_lc_amt+=$lc_amt; ?></P></td>
								<td width="70" align="center" title="Last Ship Date"><P><? if($lc_ship_date!="" && $lc_ship_date!="0000-00-00") echo change_date_format($lc_ship_date); ?></P></td>
								<td width="80"  align="center" title="Last Expire Date"><P><? if($lc_expire_date!="" && $lc_expire_date!="0000-00-00") echo change_date_format($lc_expire_date); ?></P></td>
								
								<td width="150"><P><? echo $invoice_no=implode(",",array_unique(explode(",",chop($invoice_no," , ")))); ?></P></td>
								<td width="70" align="center" title="Last Invoice Date"><P><? if($invoice_date!="" && $invoice_date!="0000-00-00") echo change_date_format($invoice_date); ?></P></td>
								<td width="80"><P><? echo $inco_term; ?></P></td>
								<td width="100"><P><? echo $inco_term_place; ?></P></td>
								<td width="80"><P><? echo $bl_no; ?></P></td>
								<td width="70" align="center"><P><? if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?></P></td>
								<td width="100"><P><? echo $mother_vasel; ?></P></td>
								<td width="100"><P><? echo $feder_vasel; ?></P></td>
								<td width="100"><P><? echo $continer_no; ?></P></td>
								<td width="80" align="right"><P><? echo number_format($pakag_qnty,2); $total_pkg_qnty+=$pakag_qnty; ?></P></td>
								<td width="100" align="center"><P><? if($doc_send_cnf!="" && $doc_send_cnf!="0000-00-00") echo change_date_format($doc_send_cnf); ?></P></td>
								<td width="70"></td>
								<td width="80"><P><? echo $bill_entry_no; ?></P></td>
								
								<td width="70" align="center"><P><? if($maturity_date!="" && $maturity_date!="0000-00-00") echo change_date_format($maturity_date); ?></P></td>
								<td width="70" align="center"><P><? if($maturity_month!="" && $maturity_month!="0000-00-00") echo change_date_format($maturity_month); ?></P></td>
								<td width="70" align="center"><P><? if($pay_date!="" && $pay_date!="0000-00-00") echo change_date_format($pay_date); ?></P></td>
								<td width="80" align="right"><P><? echo number_format($pay_amt,2); $total_pay_amt+=$pay_amt; ?></P></td>
								<?
							}
							else
							{
								?>
								<td width="130"></td>
								<td width="70"></td>
								<td width="150"></td>
								<td width="100"></td>
								<td width="70"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="100"></td>
	                            
								<td width="70"></td>
								<td width="120"></td>
								<td width="80"></td>
								<td width="50"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="80"></td>
								
								<td width="150"></td>
								<td width="70"></td>
								<td width="80"></td>
								<td width="100"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="80"></td>
								<td width="100"></td>
								<td width="70"></td>
								<td width="80"></td>
								
								<td width="70"></td>
								<td width="70"></td>
								<td width="70"></td>
								<td width="80"></td>
								<?
							}
							//$rcv_qnty=$rcv_value
							$pipe_pi_qnty=$pi_pipe_array[$req_dtls_id][$row[("prod_id")]];
							$pi_id_all=array_unique(explode(",",chop($pi_data_arr[$req_dtls_id]["pi_id"]," , ")));
							$wo_mst_id_all=array_unique(explode(",",chop($wo_data_array[$req_dtls_id]["wo_mst_id"]," , ")));
							$recv_pi_wo_req="";
							foreach($pi_id_all as $val)
							{
								
								$rcv_qnty+=$recv_data_array[1][$val][$row[("prod_id")]]["rcv_qnty"];
								$rcv_value+=$recv_data_array[1][$val][$row[("prod_id")]]["rcv_amt"];
								$recv_pi_wo_req.=$val.",";
							}
							$recv_pi_wo_req=chop($recv_pi_wo_req," , ");
							if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**1";
							if($rcv_qnty=="")
							{
								$recv_pi_wo_req="";
								foreach($wo_mst_id_all as $val)
								{
									$rcv_qnty+=$recv_data_array[2][$val][$row[("prod_id")]]["rcv_qnty"];
									$rcv_value+=$recv_data_array[2][$val][$row[("prod_id")]]["rcv_amt"];
									$recv_pi_wo_req.=$val.",";
								}
								$recv_pi_wo_req=chop($recv_pi_wo_req," , ");
								if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**2";
								
							}
							if($rcv_qnty=="")
							{
								$recv_pi_wo_req="";
								$rcv_qnty=$recv_data_array[7][$req_data_array[$req_dtls_id]["req_id"]][$row[("prod_id")]]["rcv_qnty"];
								$rcv_value=$recv_data_array[7][$req_data_array[$req_dtls_id]["req_id"]][$row[("prod_id")]]["rcv_amt"];
								$recv_pi_wo_req=$req_data_array[$req_dtls_id]["req_id"];
								if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**7";
							}
							?>
	                        <td width="80" align="right"><p><a href="##" onClick="openmypage_popup('<? echo $recv_pi_wo_req; ?>','<? echo $row[("prod_id")]; ?>','Receive Info','receive_popup');" > <? echo number_format($rcv_qnty,2); $total_mrr_qnty+=$rcv_qnty; ?> </a></p></td>
	                        <td width="80" align="right"><p><? echo number_format($rcv_value,2); $total_mrr_amt+=$rcv_value; ?></p></td>
	                        <td align="right" title="Wo Value-Receive Value" width="80"><p><? $short_value=$wo_data_array[$req_dtls_id]["amount"]-$rcv_value; echo number_format($short_value,2);  $total_short_amt+=$short_value; ?></p></td>
	                        <?
							$pipe_wo_qnty+=$wo_pipe_array[$req_dtls_id][$row[("prod_id")]];
							$pipe_line=(($pipe_wo_qnty+$pipe_pi_qnty)-$rcv_qnty);
							?>
	                        <td  align="right"><P> <? echo number_format($pipe_line,2); $total_pipe_line+=$pipe_line;?></P></td>
	                    </tr>
	                    <?
						$i++;
						$pipe_wo_qnty=0;
					

					/*if (!in_array($row["item_category_id"], $checkCateArr)) {
							$checkCateArr[] = $row["item_category_id"];
							
							
					?>



	                </tbody>
	            </table>
	            <br>
	            <?
	        			}*/
	            $j++;
						
	            	}
	            ?>
	                        <!--<td  align="right"><P><a href="##" onClick="openmypage_popup('<?// echo $recv_pi_wo_req; ?>','<?// echo $row[("prod_id")]; ?>','Pipe Line Info','pipe_line_popup');" > <?// echo number_format($pipe_wo_qnty,2); $total_pipe_line+=$pipe_line;?></a></P></td>-->
	            <!-- </div> -->
	            <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_footer">
	            	<tfoot>
	                    <tr>
	                        <!--1210 requisition details-->
	                        <th width="30"></th>
	                        <th width="50"></th>
	                        <th width="70"></th>
	                        <th width="150"></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="100"></th> 
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="150"></th>
	                        <th width="100"></th>
	                        <th width="70"> </th>
	                        <th width="100" id="value_total_req_qnty" align="right"><? echo number_format($total_req_qnty,0); ?> </th>
	                        
	                        <!--1110 wo details-->
	                        <th width="50"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="150"></th>
	                        <th width="80" id="value_total_wo_qnty" align="right"><? echo number_format($total_wo_qnty,0); ?></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_wo_amt" align="right"><? echo number_format($total_wo_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_wo_balance" align="right"><? echo number_format($total_wo_balanc,2); ?></th>
	                        <th width="150"></th>
	                        
	                        <!--840 pi details-->
	                        <th width="130"></th>
	                        <th width="70"></th>
	                        <th width="150"></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_pi_qnty" align="right"><? echo number_format($total_pi_qnty,0); ?></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_pi_amt" align="right"><? echo number_format($total_pi_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        
	                        <!--550 lc details-->
	                        <th width="70"></th>
	                        <th width="120"></th>
	                        <th width="80"></th>
	                        <th width="50"></th>
	                        <th width="80" id="value_total_lc_amt" align="right"><? echo number_format($total_lc_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        
	                        <!--1100 Invoice details-->
	                        <th width="150"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="80" id="value_total_pkg_qnty" align="right"><? echo number_format($total_pkg_qnty,0); ?></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        
	                        <!--290 Payment details-->
	                        <th width="70"></th>
	                        <th width="70"></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_pay_amt" align="right"><? echo number_format($total_pay_amt,2); ?></th>
	                        
	                        <!--340 MRR details-->
	                        <th width="80" id="value_total_mrr_qnty" align="right"><? echo number_format($total_mrr_qnty,0); ?></th>
	                        <th width="80" id="value_total_mrr_amt" align="right"><? echo number_format($total_mrr_amt,2); ?></th>
	                        <th width="80" id="value_total_short_amt" align="right"><? echo number_format($total_short_amt,2); ?></th>
	                        <th id="value_total_pipe_line" align="right"><? echo number_format($total_pipe_line,2); ?></th>
	                    </tr>
	                </tfoot>
	            </table>
	        </fieldset>
	    </div>
		<?
		
	}
	
	//else if(($cbo_date_type==2 &&  ($txt_date_from && $txt_date_to)) || $cbo_supplier>0 || $txt_wo_po_no)
	else if($txt_wo_po_no != ""  && $txt_pi_no == "")
	{	
		$sql_cond="";
		$requ_table="";
		if($cbo_company_name>0) $sql_cond.=" and a.company_name='$cbo_company_name' ";
		if($cbo_item_category_id>0) $sql_cond.=" and b.item_category_id='$cbo_item_category_id' ";
		if($cbo_supplier>0) $sql_cond.=" and a.supplier_id='$cbo_supplier' ";

		if($txt_req_no!="") 
		{
			$sql_cond.=" and c.requ_prefix_num = '$txt_req_no' and c.id=d.mst_id and b.requisition_dtls_id=d.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
			$requ_table=", inv_purchase_requisition_mst c, inv_purchase_requisition_dtls d ";
		}

		if($txt_wo_po_no!="") 
		{
			$sql_cond.=" and a.wo_number like '%$txt_wo_po_no%' ";
		}
		

        if($txt_date_from != "" && $txt_date_to != "" && $cbo_date_type == 2) $sql_cond.=" and a.wo_date between  '$txt_date_from' and '$txt_date_to'";


		//Finding WO/PO Data....
		$sql_wo=sql_select("select a.id as wo_mst_id, a.wo_number, a.wo_number_prefix_num, a.wo_date, a.supplier_id, b.id as wo_dtls_id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name, b.requisition_dtls_id, b.item_id as prod_id, b.item_category_id, b.supplier_order_quantity, b.rate, b.amount 
		from wo_non_order_info_mst a, wo_non_order_info_dtls b $requ_table 
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond and b.item_category_id not in (2,3,12,13,14,24,25,28,30) order by b.item_category_id, a.wo_number");
		/*echo "select a.id as wo_mst_id, a.wo_number, a.wo_number_prefix_num, a.wo_date, a.supplier_id, b.id as wo_dtls_id,
		b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name,
		b.requisition_dtls_id, b.item_id as prod_id, b.item_category_id, b.supplier_order_quantity, b.rate, b.amount from wo_non_order_info_mst a, wo_non_order_info_dtls b $requ_table where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond and b.item_category_id not in (2,3,12,13,14,24,25,28,30) order by b.item_category_id, a.wo_number";die;*/
		$wo_data_array=array();
		$req_dtls_id_arr=array();
		$wo_num_arr=array();
		$gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
		if($gblDel)
		{
			oci_commit($con);
			//disconnect($con);
		}
		$temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
		if($temp_table_id=="") $temp_table_id=1;
		$refrID1=$refrID2=1;
		foreach($sql_wo as $row)
		{
			if($row[csf("requisition_dtls_id")])
			{
				$refrID1=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("requisition_dtls_id")].",1,".$user_id.")");
				if(!$refrID1)
				{
					echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("requisition_dtls_id")].",1,".$user_id.")";oci_rollback($con);disconnect($con);die;
				}
				$temp_table_id++;
			}
			
			$refrID2=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("wo_dtls_id")].",2,".$user_id.")");
			if(!$refrID2)
			{
				echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("wo_dtls_id")].",2,".$user_id.")";oci_rollback($con);disconnect($con);die;
			}
			$temp_table_id++;
			
			$req_dtls_id_arr[$row[csf("requisition_dtls_id")]]=$row[csf("requisition_dtls_id")];
			$wo_num_arr[$row[csf("wo_number")]]=$row[csf("wo_number")];
			$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_mst_id"].=$row[csf("wo_mst_id")].",";
			$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_number"].=$row[csf("wo_number")].",";
			$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_number_prefix_num"].=$row[csf("wo_number_prefix_num")].",";
			$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_date"]=$row[csf("wo_date")];
			$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
			$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]=$row[csf("supplier_id")];
			$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
			$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
			$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"]+=$row[csf("amount")];

			if ($cbo_item_category_id==1) 
			{	
				$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_description"]=$yarn_count_arr[$row[csf("yarn_count")]]." ".$composition[$row[csf("yarn_comp_type1st")]]." ".$row[csf("yarn_comp_percent1st")]." ".$yarn_type[$row[csf("yarn_type")]]." ".$color_name_arr[$row[csf("color_name")]];
			}
		}
		
		if(count($sql_wo) < 1){
			echo "<span style='font-size:23;font-weight:bold;text-align:center;width:100%'>Data Not Found</span>";die;
		}
		
		//unset($sql_wo);
		
		if($refrID1 && $refrID2)
		{
			oci_commit($con);
		}
		//Finding Requisition Data....
		if(!empty($req_dtls_id_arr))
		{
			/*$req_dtsl_ids=implode(",", array_filter(array_unique($req_dtls_id_arr)));

			$dtls_id = $req_dtls_id_cond = ""; 
			$req_dtls_id_arr=explode(",",$req_dtsl_ids);
			if($db_type==2 && count($req_dtls_id_arr)>999)
			{
				$req_dtls_id_chunk=array_chunk($req_dtls_id_arr,999) ;
				foreach($req_dtls_id_chunk as $chunk_arr)
				{
					$dtls_id.=" b.id in(".implode(",",$chunk_arr).") or ";	
				}
						
				$req_dtls_id_cond.=" and (".chop($dtls_id,'or ').")";			
				
			}
			else
			{ 	
				
				$req_dtls_id_cond=" and b.id in($req_dtsl_ids)"; 
			}*/
			if($cbo_item_category_id) $req_sql_category_cond = " and b.item_category=$cbo_item_category_id"; else $req_sql_category_cond="";
			$req_sql=sql_select("select a.id as req_id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date, b.item_category as item_category_id, b.cons_uom, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.color_id, b.id as req_dtsl_id, b.product_id as prod_id, b.required_for, b.quantity, b.rate, b.amount 
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, gbl_temp_report_id c 
			where a.id=b.mst_id and b.id=c.ref_val and c.ref_from=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and b.item_category not in (2,3,12,13,14,24,25,28,30)"); //and b.id in ($req_dtsl_ids)

			$req_data_array=array();
			foreach($req_sql as $row)
			{
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["req_id"]=$row[csf("req_id")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["requ_no"]=$row[csf("requ_no")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["cons_uom"]=$row[csf("cons_uom")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["requ_prefix_num"]=$row[csf("requ_prefix_num")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["requisition_date"]=$row[csf("requisition_date")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["store_name"]=$row[csf("store_name")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["pay_mode"]=$row[csf("pay_mode")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["cbo_currency"]=$row[csf("cbo_currency")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["delivery_date"]=$row[csf("delivery_date")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["required_for"]=$row[csf("required_for")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["quantity"]=$row[csf("quantity")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["rate"]=$row[csf("rate")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["amount"]=$row[csf("amount")];

				if ($cbo_item_category_id==1) 
				{
					$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["item_description"]=$yarn_count_arr[$row[csf("count_id")]]." ".$composition[$row[csf("composition_id")]]." ".$row[csf("com_percent")]." ".$yarn_type[$row[csf("yarn_type_id")]]." ".$color_name_arr[$row[csf("color_id")]];
				}
			}	
		}
		
		unset($req_sql);
		//Finding PI data....
		$wo_num_ids="";
		if(!empty($wo_num_arr))
		{
			if($cbo_item_category_id) $sql_pi_category_cond = "and c.item_category_id=$cbo_item_category_id and b.item_category_id=$cbo_item_category_id"; else $sql_pi_category_cond = "";
			$sql_pi="SELECT a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, a.item_category_id as item_category_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, case when b.item_prod_id !='' or b.item_prod_id is not null then b.item_prod_id else 0 end as prod_id, b.work_order_no, b.work_order_dtls_id, b.uom, b.quantity, b.amount, c.id as wo_dtls_id, c.requisition_dtls_id 
			from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c, gbl_temp_report_id d 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.id=d.ref_val and d.ref_from=2 and a.importer_id=$cbo_company_name $sql_pi_category_cond";
		}
		/*foreach (array_unique($wo_num_arr) as $values) 
		{
			$wo_num_ids.="'".$values."',";
		}
		$wo_num_ids=chop($wo_num_ids,",");*/
		
			
		//echo $sql_pi;die;

		$sql_pi_result=sql_select($sql_pi);
		$pi_data_arr=$pi_id_arr=array();
		$refrID3=1;
		foreach($sql_pi_result as $row)
		{
			$refrID3=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",3,".$user_id.")");
			if(!$refrID3)
			{
				echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",3,".$user_id.")";oci_rollback($con);disconnect($con);die;
			}
			$temp_table_id++;
			
			$pi_id_arr[$row[csf("pi_id")]]=$row[csf("pi_id")];
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["requisition_dtls_id"]=$row[csf("requisition_dtls_id")];
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_id"].=$row[csf("pi_id")].",";
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_number"].=$row[csf("pi_number")].",";
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_date"]=$row[csf("pi_date")];
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]=$row[csf("supplier_id")];
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["currency_id"]=$row[csf("currency_id")];
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["intendor_name"]=$row[csf("intendor_name")];
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["prod_id"].=$row[csf("prod_id")].",";
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["uom"]=$row[csf("uom")];
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["quantity"]+=$row[csf("quantity")];
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]+=$row[csf("amount")];
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
		}
		
		if($refrID3)
		{
			oci_commit($con);
		} 
		
		unset($sql_pi_result);
		
		//finding BTB Lc data....
		if(!empty($pi_id_arr))
		{
			$sql_btb=sql_select("select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id 
			from com_btb_lc_master_details a, com_btb_lc_pi b, gbl_temp_report_id c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.ref_val and c.ref_from=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$btb_data_array=array();
			foreach($sql_btb as $row)
			{
				$btb_data_array[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
				$btb_data_array[$row[csf("pi_id")]]["lc_id"]=$row[csf("lc_id")];
				$btb_data_array[$row[csf("pi_id")]]["lc_number"]=$row[csf("lc_number")];
				$btb_data_array[$row[csf("pi_id")]]["lc_date"]=$row[csf("lc_date")];
				$btb_data_array[$row[csf("pi_id")]]["payterm_id"]=$row[csf("payterm_id")];
				$btb_data_array[$row[csf("pi_id")]]["tenor"]=$row[csf("tenor")];
				$btb_data_array[$row[csf("pi_id")]]["lc_value"]=$row[csf("lc_value")];
				$btb_data_array[$row[csf("pi_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
				$btb_data_array[$row[csf("pi_id")]]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
			}
			unset($sql_btb);
			//Finding Invoice data....
			if($db_type==0)
			{
				$pi_cond="group_concat(a.pi_id) as pi_id";
			}
			else if($db_type==2)
			{
				$pi_cond="LISTAGG(CAST(a.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.pi_id) as pi_id";
			}
			$sql_invoice_pay=sql_select("select $pi_cond, b.id as invoice_id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, e.payterm_id, b.id as accept_id 
			from gbl_temp_report_id c, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_btb_lc_master_details e 
			where c.ref_val=a.pi_id and c.ref_from=3 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and e.id = a.com_btb_lc_master_details_id and b.status_active=1 and b.is_deleted=0 
			group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, e.payterm_id");
			
			$inv_pay_data_array=array();
			$temp_inv_array=$temp_accept_id=array();
			foreach($sql_invoice_pay as $row)
			{
				$all_pi_id=array_unique(explode(",",$row[csf("pi_id")]));
				foreach($all_pi_id as $pi_id)
				{
					$inv_pay_data_array[$pi_id]["pi_id"]=$pi_id;
					$inv_pay_data_array[$pi_id]["invoice_id"].=$row[csf("invoice_id")].",";
					$inv_pay_data_array[$pi_id]["invoice_no"].=$row[csf("invoice_no")].",";
					$inv_pay_data_array[$pi_id]["document_value"]+=$row[csf("document_value")];
					$inv_pay_data_array[$pi_id]["invoice_date"]=$row[csf("invoice_date")];
					$inv_pay_data_array[$pi_id]["inco_term"]=$row[csf("inco_term")];
					$inv_pay_data_array[$pi_id]["inco_term_place"]=$row[csf("inco_term_place")];
					$inv_pay_data_array[$pi_id]["bill_no"]=$row[csf("bill_no")];
					$inv_pay_data_array[$pi_id]["bill_date"]=$row[csf("bill_date")];
					$inv_pay_data_array[$pi_id]["mother_vessel"]=$row[csf("mother_vessel")];
					$inv_pay_data_array[$pi_id]["feeder_vessel"]=$row[csf("feeder_vessel")];
					$inv_pay_data_array[$pi_id]["container_no"]=$row[csf("container_no")];
					$inv_pay_data_array[$pi_id]["doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$inv_pay_data_array[$pi_id]["bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$inv_pay_data_array[$pi_id]["maturity_date"]=$row[csf("maturity_date")];

					if($temp_inv_array[$row[csf("invoice_id")]]=="")
					{
						$temp_inv_array[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
						$inv_pay_data_array[$pi_id]["pkg_quantity"]+=$row[csf("pkg_quantity")];
						
					}

					/*if($row[csf("payterm_id")]==1) //Pay Term = At sight
					{
						$inv_pay_data_array[$pi_id]["payment_date"]=$row[csf("invoice_date")];

						$cumulative_array=return_library_array("select pi_id, sum(current_acceptance_value) as accepted_ammount from com_import_invoice_dtls where pi_id=$pi_id and status_active=1 and is_deleted=0 group by pi_id",'pi_id','accepted_ammount'); 
						$inv_pay_data_array[$pi_id]["accepted_ammount"]=$cumulative_array[$pi_id];
					}*/
				}
				
			}
			
			unset($sql_invoice_pay);
			
			//Finding invoice Payment data
			if ($db_type==0)
			{
				$sql_invoice_pay2= sql_select("select group_concat(a.pi_id) as pi_id, c.payment_date, c.id as accept_id, c.accepted_ammount 
				from gbl_temp_report_id m, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_import_payment c 
				where m.ref_val=a.pi_id and m.ref_from=3 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and b.id=c.invoice_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
				group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, c.payment_date, c.id, c.accepted_ammount");
			}
			else
			{
				$sql_invoice_pay2= sql_select("select LISTAGG(CAST(a.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.pi_id) as pi_id, c.payment_date, c.id as accept_id, c.accepted_ammount 
				from gbl_temp_report_id m, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_import_payment c 
				where m.ref_val=a.pi_id and m.ref_from=3 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and b.id=c.invoice_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
				group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, c.payment_date, c.id, c.accepted_ammount");
			}
			
			//echo "select LISTAGG(CAST(a.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.pi_id) as pi_id, c.payment_date, c.id as accept_id, c.accepted_ammount from com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_import_payment c where a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and b.id=c.invoice_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.pi_id in (".trim(implode(",",$pi_id_arr),",").") group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, c.payment_date, c.id, c.accepted_ammount";
			foreach($sql_invoice_pay2 as $row)
			{
				$all_pi_id=array_unique(explode(",",$row[csf("pi_id")]));
				foreach($all_pi_id as $pi_id)
				{
					$inv_pay_data_array[$pi_id]["payment_date"]=$row[csf("payment_date")];
				}
				if($temp_accept_id[$row[csf("accept_id")]]=="")
				{
					$temp_accept_id[$row[csf("accept_id")]]=$row[csf("accept_id")];
					$inv_pay_data_array[$pi_id]["accepted_ammount"]+=$row[csf("accepted_ammount")];
				}
			}
			
			unset($sql_invoice_pay2);
			//----------------------------------End-------------------------------------------------------
		}

		if($cbo_item_category_id) $sql_receive_category_cond = " and b.item_category in($cbo_item_category_id)"; else $sql_receive_category_cond="";
		if ($cbo_item_category_id==1) // For Yarn
		{
			/*echo "select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, c.product_name_details, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt from inv_receive_master a, inv_transaction b, product_details_master c where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) and b.prod_id=c.id and c.status_active=1 and c.is_deleted=0 group by a.receive_basis, b.prod_id,pi_wo_batch_no, c.product_name_details";die;*/
			$sql_receive=sql_select("select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, c.product_name_details, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt from inv_receive_master a, inv_transaction b, product_details_master c where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) and b.prod_id=c.id and c.status_active=1 and c.is_deleted=0 
			group by a.receive_basis, b.prod_id,pi_wo_batch_no, c.product_name_details");
			$recv_data_array=array();
			foreach($sql_receive as $row)
			{
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["receive_basis"]=$row[csf("receive_basis")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["prod_id"]=$row[csf("prod_id")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["rcv_amt"]=$row[csf("rcv_amt")];
			}
		}
		else
		{
			
			$sql_receive=sql_select("select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt from inv_receive_master a, inv_transaction b where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) group by a.receive_basis, b.prod_id,pi_wo_batch_no");
			$recv_data_array=array();
			foreach($sql_receive as $row)
			{
				$recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["receive_basis"]=$row[csf("receive_basis")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["rcv_amt"]=$row[csf("rcv_amt")];
			}
		}
		unset($sql_receive);
		//echo "<pre>";print_r($recv_data_array);die;
		$gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
		if($gblDel)
		{
			oci_commit($con);disconnect($con);
		}
		//echo "select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt from inv_receive_master a, inv_transaction b where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) group by a.receive_basis, b.prod_id,pi_wo_batch_no"; die;

		$item_group_array = return_library_array("select id,item_name from  lib_item_group ","id","item_name");
		$store_array = return_library_array("select id,store_name from  lib_store_location ","id","store_name");
		$suplier_array = return_library_array("select id,supplier_name from  lib_supplier ","id","supplier_name");
		$indentor_name_array = return_library_array("select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b where a.id = b.supplier_id and b.party_type = 40","id","supplier_name");
		ob_start();
		?>
			<div style="width:5660px; margin-left:10px">
	        <fieldset style="width:100%;">	 
	            <table width="5500" cellpadding="0" cellspacing="0" id="caption">
	                <tr>
	                	<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	                </tr> 
	                <tr>  
	                	<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
	                </tr>  
	            </table>
	        	<br />
	                <?
					$i=1;
					$btb_tem_lc_array=$inv_temp_array=array();
					//foreach($req_data_array as $req_dtls_id=>$row)
					foreach ($sql_wo as  $row) 
					{
						$wo_no=$wo_supplier="";$wo_qnty=$wo_rate=$wo_amount=$wo_balance=0;
						$pi_no=$pi_date=$pi_suplier=$pi_indore_name=$pi_id_all=$rcv_qnty=$rcv_value=$wo_mst_id_all=$pipe_line=$short_value=$pipe_pi_qnty=$pipe_wo_qnty=""; $pi_rate=0;
						$lc_date=$lc_no=$lc_pay_term=$lc_tenor=$lc_amt=$lc_ship_date=$lc_expire_date="";
						$invoice_id=$invoice_no=$invoice_date=$inco_term=$inco_term_place=$bl_no=$bl_date=$mother_vasel=$feder_vasel=$continer_no=$pakag_qnty=$doc_send_cnf=$bill_entry_no=$maturity_date=$maturity_month=$pay_date=$pay_amt="";
						
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						if (!in_array($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"], $checkCateArr)) 
						{
							$checkCateArr[] = $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"];
							if($i>1)
							{
								?>
								</tbody>
	            				</table>
								<?
							}
							
							$j=1;
						?>

						<table width="5660" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
	                		<thead>
                                <tr>
                                    <th colspan="63" style="text-align: left !important; color: black"><? echo  $item_category[$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?> :</th>
                                </tr>
                                <tr>
                                    <th colspan="13" >Requisiton Details</th>
                                    <th colspan="12" >Work Order Details</th>
                                    <th colspan="10" >PI Details</th>
                                    <th colspan="7">L/C Details</th>
                                    <th colspan="13">Invoice Details</th>
                                    <th colspan="4">Payment Details</th>
                                    <th colspan="4">Store details</th>
                                </tr>
                                <tr>
                                    <!--1210 requisition details-->    
                                    <th width="30">SL</th>
                                    <th width="50">Req. No</th>
                                    <th width="70">Req. Date</th>
                                    <th width="150">Store Name</th>
                                    <th width="70">Delivery Date</th>
                                    <th width="100">Item Category</th>
                                    <th width="100">Item Group</th> 
                                    <th width="100">Item Sub. Group</th>
                                    <th width="80">Item Code</th>
                                    <th width="150">Item Description</th>
                                    <th width="100">Required For</th>
                                    <th width="70"> UOM</th>
                                    <th width="100">Req. Quantity </th>
                                    
                                    <!--1110 wo details-->
                                    <th width="50">WO No</th>
                                    <th width="100">Item Category</th>
                                    <th width="100">Item Group</th>
                                    <th width="100">Item Sub. Group</th>
                                    <th width="80">Item Code</th>
                                    <th width="150">Item Description</th>
                                    <th width="80">WO Qnty</th>
                                    <th width="80">Wo Rate</th>
                                    <th width="80">WO Amount</th>
                                    <th width="70">WO Date</th>
                                    <th width="80">WO Balance</th>
                                    <th width="150">Supplier</th>
                                    
                                    <!--840 pi details-->
                                    <th width="130">PI No</th>
                                    <th width="70">PI Date</th>
                                    <th width="150">Supplier</th>
                                    <th width="100">Item Category</th>
                                    <th width="70">UOM</th>
                                    <th width="80">PI Quantity</th>
                                    <th width="80">Unit Price</th>
                                    <th width="80">PI Value</th>
                                    <th width="70">Currency</th>
                                    <th width="100">Indentor Name</th>
                                    
                                    <!--550 lc details-->
                                    <th width="70">LC Date</th>
                                    <th width="120">LC No</th>
                                    <th width="80">Pay Term</th>
                                    <th width="50">Tenor</th>
                                    <th width="80">LC Amount</th>
                                    <th width="70">Shipment Date</th>
                                    <th width="80">Expiry Date</th>
                                    
                                    <!--1100 Invoice details-->
                                    <th width="150">Invoice No</th>
                                    <th width="70">Invoice Date</th>
                                    <th width="80">Incoterm</th>
                                    <th width="100">Incoterm Place</th>
                                    <th width="80">B/L No</th>
                                    <th width="70">BL Date</th>
                                    <th width="100">Mother Vassel</th>
                                    <th width="100">Feedar Vassel</th>
                                    <th width="100">Continer No</th>
                                    <th width="80">Pkg Qty</th>
                                    <th width="100">Doc Send to CNF</th>
                                    <th width="70">NN Doc Received Date</th>
                                    <th width="80">Bill Of Entry No</th>
                                    
                                    <!--290 Payment details-->
                                    <th width="70">Maturity Date</th>
                                    <th width="70">Maturity Month</th>
                                    <th width="70">Payment Date</th>
                                    <th width="80">Paid Amount</th>
                                    
                                    <!--340 MRR details-->
                                    <th width="80">MRR Qnty</th>
                                    <th width="80">MRR Value</th>
                                    <th width="80">Short Value</th>
                                    <th >Pipeline</th>
                                </tr>
                            </thead>
                        </table>
	            <!-- <div style="width:5660px; max-height:300px; overflow-y:scroll" id="scroll_body"> -->
	            <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body">
	            	<tbody>
	            		<? 
	            		}
	            	 ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

	                    	<? //------------------------------Requisition dtls start----------------------------------------- ?>
	                        <td width="30" align="center"><p><? echo $j; ?></p></td>
	                        <td width="50" align="center"><p><? echo $req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["requ_prefix_num"]; ?></p></td>
	                        <td width="70" align="center"><p>
	                        	<? 
	                        	$req_date=$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["requisition_date"];
	                        	if($req_date!="" && $req_date!="0000-00-00") echo change_date_format($req_date); 
	                        	?></p>
	                        </td>
	                        <td width="150"><p><? echo $store_array[$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["store_name"]]; ?></p></td>
	                        <td width="70" align="center"><p>
	                        	<? 
	                        	$delivry_date=$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["delivery_date"];
	                        	if($delivry_date!="" && $delivry_date!="0000-00-00") echo change_date_format($delivry_date); 
	                        	?></p>
	                        </td>
							<td width="100"><p><? echo $item_category[$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>

							<? $prod_id=$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["prod_id"]; ?>

	                        <td width="100"><p><? echo $item_group_array[$prod_data_array[$prod_id]["item_group_id"]]; ?></p></td>
	                        <td width="100"><p><? echo $prod_data_array[$prod_id]["sub_group_name"]; ?></p></td>
	                        <td width="80"><p><? echo $prod_data_array[$prod_id]["item_code"]; ?></p></td>
	                        <td width="150"><p>
	                        	<? 
	                        	if ($cbo_item_category_id==1) 
	                        	{
	                        		echo $req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_description"];
	                        	}
	                        	else
	                        	{
	                        		echo $prod_data_array[$prod_id]["item_description"]; 
	                        	}
	                        	?>
	                        </p></td>
	                        <td width="100"><p><? echo $req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["required_for"]; ?></p></td>
	                        <td width="70"><p>
			                        	<? 
			                        		echo $unit_of_measurement[$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["cons_uom"]];
		            						//echo $unit_of_measurement[$prod_data_array[$prod_id]["unit_of_measure"]]; 
		            					?>
	                        </p></td>
	                        <td width="100" align="right" title="<? echo $req_qty;?>"><p>
	                        	<? 
	                        		$req_qty=$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["quantity"];
	                        		echo number_format($req_qty,2);
	                        		$total_req_qnty+=$req_qty; 
	                        		//echo "=================".$total_req_qnty;
	                        	?>
	                        </p></td>
	                        <? //------------------------------WO dtls start------------------------------------------- ?>
	             
							<td width="50" align="center"><p>
							<?
							$wo_no=implode(",",array_unique(explode(",",chop($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_number_prefix_num"]," , "))));
							echo $wo_no;
							?>
							</p></td>
							<td width="100"><p><? echo $item_category[$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>

							<? $wo_prod_id=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["prod_id"]; ?>

							<td width="100"><p><? echo $item_group_array[$prod_data_array[$wo_prod_id]["item_group_id"]]; ?></p></td> 
							<td width="100"><p><? echo $prod_data_array[$wo_prod_id]["sub_group_name"]; ?></p></td>
							<td width="80"><p><? echo $prod_data_array[$wo_prod_id]["item_code"]; ?></p></td>
							<td width="150"><p>
								<? 
									if ($cbo_item_category_id==1) 
									{
										echo $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_description"];
									}
									else
									{
										echo $prod_data_array[$wo_prod_id]["item_description"]; 
									}
								?>
							</p></td>
							<td width="80" align="right"><p>
								<? 
								echo number_format($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"],2); 
								$total_wo_qnty+=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"]; 
								?>
							</p></td>
							<td width="80" align="right"><p>
								<? 
								$wo_rate=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"]/$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"]; 
								echo number_format($wo_rate,2);  
								?>
							</p></td>
							<td width="80" align="right"><p>
								<? 
								echo number_format($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"],2); 
								$total_wo_amt+=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"]; 
								?>
							</p></td>
							<td width="70" title="last wo date" align="center"><p>
								<? 
								$wo_po_date=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_date"];
								if($wo_po_date!="" && $wo_po_date!="0000-00-00") echo change_date_format($wo_po_date); 
								?>
							</p></td>
							<td width="80" align="right" title="Requisition Quantity-Wo Quantity"><p>
                            <?
                            if($req_qty !=0 || $req_qty !="")
                            {
								$wo_balance=$req_qty-$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"];
								echo number_format($wo_balance,2); 
							}
							$total_wo_balance+=$wo_balance;
							?>
                            </p></td>
							<td width="150"><p>
								<? $wo_supplier=$suplier_array[$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]]; 
								   echo $wo_supplier;
								?>
							</p></td>

							<? //------------------------------PI dtls start------------------------------------------- ?>
								<?
							if(chop($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_id"]," , ") !="")
							{
								?>
								<td width="130" align="center"><p>
								<?
								$pi_no=implode(",",array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_number"]," , "))));
								echo $pi_no;
								?>
								</p></td>
								<td width="70" align="center" title="Last PI Date"><p>
									<? 
									$pi_date_data=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_date"];
									if($pi_date_data!="" && $pi_date_data!="0000-00-00") echo change_date_format($pi_date_data); 
									?>
								</p></td>
								<td width="150"><p>
								<? 
								$pi_suplier=$suplier_array[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]]; 
								echo $pi_suplier; 
								?> 
								</p></td>
								<td width="100"><p><? echo $item_category[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>
								<td width="70" align="center"><P><? echo $unit_of_measurement[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["uom"]]; ?></P></td>
								<td width="80" align="right"><p>
									<? 
									echo number_format($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["quantity"],2); 
									$total_pi_qnty+=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["quantity"]; 
									?>
								</p></td>
								<td width="80" align="right"><P>
									<? 
									$pi_rate=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["amount"]/$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["quantity"]; 
									echo number_format($pi_rate,2); 
									?>
								</P></td>
								<td width="80" align="right"><p>
									<? 
									echo number_format($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["amount"],2); 
									$total_pi_amt+=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["amount"]; 
									?>
								</p></td>
								<td width="70"><P><? echo $currency[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["currency_id"]]; ?></P></td>
								<td width="100"><p>
									<? 
									$pi_indore_name=$indentor_name_array[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["intendor_name"]]; 
									echo $pi_indore_name; 
									?>
								</p></td>

								<? //------------------------------LC dtls start------------------------------------------- ?>


	                            <?
								$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_id"]," , ")));

								foreach($pi_id_arr as $piID)
								{	
									if(!in_array($btb_data_array[$piID]["lc_id"],$btb_tem_lc_array))
									{
										$btb_tem_lc_array[$btb_data_array[$piID]["lc_id"]]=$btb_data_array[$piID]["lc_id"];
										$lc_date=$btb_data_array[$piID]["lc_date"];
										$lc_no.=$btb_data_array[$piID]["lc_number"].",";
										$lc_pay_term=$pay_term[$btb_data_array[$piID]["payterm_id"]];
										$lc_tenor+=$btb_data_array[$piID]["tenor"];
										$lc_amt+=$btb_data_array[$piID]["lc_value"];
										$lc_ship_date=$btb_data_array[$piID]["last_shipment_date"];
										$lc_expire_date=$btb_data_array[$piID]["lc_expiry_date"];
									}
									
									if(!in_array($inv_pay_data_array[$piID]["invoice_id"],$inv_temp_array))
									{
										$inv_temp_array[$inv_pay_data_array[$piID]["invoice_id"]]=$inv_pay_data_array[$piID]["invoice_id"];
										$invoice_id.=$inv_pay_data_array[$piID]["invoice_id"].",";
										$invoice_no.=$inv_pay_data_array[$piID]["invoice_no"].",";
										$invoice_date=$inv_pay_data_array[$piID]["invoice_date"];
										$inco_term=$inv_pay_data_array[$piID]["inco_term"];
										$inco_term_place=$inv_pay_data_array[$piID]["inco_term_place"];
										$bl_no=$inv_pay_data_array[$piID]["bill_no"];
										$bl_date=$inv_pay_data_array[$piID]["bill_date"];
										$mother_vasel=$inv_pay_data_array[$piID]["mother_vessel"];
										$feder_vasel=$inv_pay_data_array[$piID]["feeder_vessel"];
										$continer_no=$inv_pay_data_array[$piID]["container_no"];
										$pakag_qnty=$inv_pay_data_array[$piID]["pkg_quantity"];
										$doc_send_cnf=$inv_pay_data_array[$piID]["doc_to_cnf"];
										$bill_entry_no=$inv_pay_data_array[$piID]["bill_of_entry_no"];
										$maturity_date=$inv_pay_data_array[$piID]["maturity_date"];
										$maturity_month=$inv_pay_data_array[$piID]["maturity_date"];
										
										$pay_date=$inv_pay_data_array[$piID]["payment_date"];
										$pay_amt+=$inv_pay_data_array[$piID]["accepted_ammount"];
									}	
								}
								?>
								<td width="70" align="center"><P><? if($lc_date!="" && $lc_date!="0000-00-00") echo change_date_format($lc_date); ?></P></td>
								<td width="120"><P><? echo $lc_no=implode(",",array_unique(explode(",",chop($lc_no," , ")))); ?></P></td>
								<td width="80"><P><? echo $lc_pay_term; ?></P></td>
								<td width="50" align="center"><P><? echo $lc_tenor; ?></P></td>
								<td width="80" align="right"><P><? echo number_format($lc_amt,2); $total_lc_amt+=$lc_amt; ?></P></td>
								<td width="70" align="center" title="Last Ship Date"><P><? if($lc_ship_date!="" && $lc_ship_date!="0000-00-00") echo change_date_format($lc_ship_date); ?></P></td>
								<td width="80"  align="center" title="Last Expire Date"><P><? if($lc_expire_date!="" && $lc_expire_date!="0000-00-00") echo change_date_format($lc_expire_date); ?></P></td>

								<? //------------------------------Invoice dtls start------------------------------------------- ?>
								
								<td width="150"><P><? echo $invoice_no=implode(",",array_unique(explode(",",chop($invoice_no," , ")))); ?></P></td>
								<td width="70" align="center" title="Last Invoice Date"><P><? if($invoice_date!="" && $invoice_date!="0000-00-00") echo change_date_format($invoice_date); ?></P></td>
								<td width="80"><P><? echo $inco_term; ?></P></td>
								<td width="100"><P><? echo $inco_term_place; ?></P></td>
								<td width="80"><P><? echo $bl_no; ?></P></td>
								<td width="70" align="center"><P><? if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?></P></td>
								<td width="100"><P><? echo $mother_vasel; ?></P></td>
								<td width="100"><P><? echo $feder_vasel; ?></P></td>
								<td width="100"><P><? echo $continer_no; ?></P></td>
								<td width="80" align="right"><P><? echo number_format($pakag_qnty,2); $total_pkg_qnty+=$pakag_qnty; ?></P></td>
								<td width="100" align="center"><P><? if($doc_send_cnf!="" && $doc_send_cnf!="0000-00-00") echo change_date_format($doc_send_cnf); ?></P></td>
								<td width="70"></td>
								<td width="80"><P><? echo $bill_entry_no; ?></P></td>
								
								<td width="70" align="center"><P><? if($maturity_date!="" && $maturity_date!="0000-00-00") echo change_date_format($maturity_date); ?></P></td>
								<td width="70" align="center"><P><? if($maturity_month!="" && $maturity_month!="0000-00-00") echo change_date_format($maturity_month); ?></P></td>
								<td width="70" align="center"><P><? if($pay_date!="" && $pay_date!="0000-00-00") echo change_date_format($pay_date); ?></P></td>
								<td width="80" align="right"><P><? echo number_format($pay_amt,2); $total_pay_amt+=$pay_amt; ?></P></td>
								<?
							}
							else
							{
								?>
								<td width="130"></td>
								<td width="70"></td>
								<td width="150"></td>
								<td width="100"></td>
								<td width="70"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="100"></td>
	                            
								<td width="70"></td>
								<td width="120"></td>
								<td width="80"></td>
								<td width="50"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="80"></td>
								
								<td width="150"></td>
								<td width="70"></td>
								<td width="80"></td>
								<td width="100"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="80"></td>
								<td width="100"></td>
								<td width="70"></td>
								<td width="80"></td>
								
								<td width="70"></td>
								<td width="70"></td>
								<td width="70"></td>
								<td width="80"></td>
								<?
							}

							//---------------------------------Store Details starts----------------------------------

							$pi_id=chop($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_id"],",");
							if ($pi_id=='') {
								$pi_id=chop($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_mst_id"],",");
							}
							
							//echo $pi_id;
							if ($cbo_item_category_id==1) 
                        	{
                        		$item_desc=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_description"];
                        		$receive_basis=$recv_data_array[$pi_id][$item_desc]["receive_basis"];
                        		$prod_id=$recv_data_array[$pi_id][$item_desc]["prod_id"];
                        	}
                        	else
                        	{
                        		$receive_basis=$recv_data_array[$pi_id][$row[csf("prod_id")]]["receive_basis"];
                        		$prod_id=$row[csf("prod_id")];
                        	}

							$data=$pi_id."**".$receive_basis;
							?>
	                        <td width="80" align="right"><p><a href="##" onClick="openmypage_popup('<? echo $data; ?>','<? echo $prod_id; ?>','Receive Info','receive_popup');" > 
	                        	<? 
	                        		if ($cbo_item_category_id==1)
	                        		{
	                        			$rcv_qnty=$recv_data_array[$pi_id][$item_desc]["rcv_qnty"];
	                        		}
	                        		else
	                        		{
	                        			$rcv_qnty=$recv_data_array[$pi_id][$row[csf("prod_id")]]["rcv_qnty"];
	                        		}
	                        		echo number_format($rcv_qnty,2); 
	                        		$total_mrr_qnty+=$rcv_qnty; 
	                        	?> 
	                        </a></p>
	                        </td>
	                        <td width="80" align="right"><p>
	                        	<? 
	                        		if ($cbo_item_category_id==1)
	                        		{
	                        			$rcv_value=$recv_data_array[$pi_id][$item_desc]["rcv_amt"];
	                        		}
	                        		else
	                        		{
	                        			$rcv_value=$recv_data_array[$pi_id][$row[csf("prod_id")]]["rcv_amt"];
	                        		}
	                        		echo number_format($rcv_value,2); 
	                        		$total_mrr_amt+=$rcv_value; 
	                        	?></p>
	                        </td>
	                        <td align="right" title="Wo Value-Receive Value" width="80"><p>
	                        	<? 
	                        		$short_value=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"]-$rcv_value;
	                        		echo number_format($short_value,2);  
	                        		$total_short_amt+=$short_value; 
	                        	?></p>
	                        </td>
	                        <?
	                        $pipe_pi_qnty=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["quantity"];
	                       	$pipe_wo_qnty=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"];

							if($pipe_pi_qnty!="" && $pipe_wo_qnty =="")
							{
								$pipe_line=$pipe_pi_qnty-$rcv_qnty;
							}
							else
							{
								$pipe_line=$pipe_wo_qnty-$rcv_qnty;
							}
							?>
	                        <td  align="right"><P> 
	                        	<? 
	                        	echo number_format($pipe_line,2); 
	                        	$total_pipe_line+=$pipe_line;
	                        	?></P>
	                        </td>
	                    </tr>
	                    <?
						$i++;
						$pipe_wo_qnty=0;
						
						$j++;
					}
					?>
	                </tbody>
	            </table>
	            <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_footer">
	            	<tfoot>
	                    <tr>
	                        <!--1210 requisition details-->
	                        <th width="30"></th>
	                        <th width="50"></th>
	                        <th width="70"></th>
	                        <th width="150"></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="100"></th> 
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="150"></th>
	                        <th width="100"></th>
	                        <th width="70"> </th>
	                        <th width="100" id="value_total_req_qnty" align="right"><? echo number_format($total_req_qnty,0); ?> </th>
	                        
	                        <!--1110 wo details-->
	                        <th width="50"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="150"></th>
	                        <th width="80" id="value_total_wo_qnty" align="right"><? echo number_format($total_wo_qnty,0); ?></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_wo_amt" align="right"><? echo number_format($total_wo_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_wo_balance" align="right"><? echo number_format($total_wo_balanc,2); ?></th>
	                        <th width="150"></th>
	                        
	                        <!--840 pi details-->
	                        <th width="130"></th>
	                        <th width="70"></th>
	                        <th width="150"></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_pi_qnty" align="right"><? echo number_format($total_pi_qnty,0); ?></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_pi_amt" align="right"><? echo number_format($total_pi_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        
	                        <!--550 lc details-->
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_lc_amt" align="right"><? echo number_format($total_lc_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        
	                        <!--1100 Invoice details-->
	                        <th width="150"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="80" id="value_total_pkg_qnty" align="right"><? echo number_format($total_pkg_qnty,0); ?></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        
	                        <!--290 Payment details-->
	                        <th width="70"></th>
	                        <th width="70"></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_pay_amt" align="right"><? echo number_format($total_pay_amt,2); ?></th>
	                        
	                        <!--340 MRR details-->
	                        <th width="80" id="value_total_mrr_qnty" align="right"><? echo number_format($total_mrr_qnty,0); ?></th>
	                        <th width="80" id="value_total_mrr_amt" align="right"><? echo number_format($total_mrr_amt,2); ?></th>
	                        <th width="80" id="value_total_short_amt" align="right"><? echo number_format($total_short_amt,2); ?></th>
	                        <th id="value_total_pipe_line" align="right"><? echo number_format($total_pipe_line,2); ?></th>
	                    </tr>
	                </tfoot>
	            </table>
	        </fieldset>
	    	</div>
		<?
	}

	else if($txt_req_no == "" && $txt_wo_po_no == "" && $txt_pi_no == "" && ($cbo_date_type==3 || $cbo_date_type==4) && ($txt_date_from && $txt_date_to)) //&& ($txt_date_from && $txt_date_to)
	{	
		$gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
		if($gblDel)
		{
			oci_commit($con);
			//disconnect($con);
		}
		$temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
		if($temp_table_id=="") $temp_table_id=1;
		
		$sql_cond="";
		$addition_table_cond="";
		$wo_pi_select="";
		if($cbo_company_name>0) $sql_cond.=" and a.importer_id='$cbo_company_name' ";
		
		if($cbo_supplier>0) $sql_cond.=" and a.supplier_id='$cbo_supplier' ";

		if($cbo_date_type==3 || $txt_date_from=="" || $txt_date_to=="")
		{
			//if($cbo_item_category_id>0) $sql_cond.=" and a.item_category_id='$cbo_item_category_id' ";
			if ($cbo_item_category_id>0) $sql_cond.=" and a.entry_form='".$category_wise_entry_form[$cbo_item_category_id]."' ";
			$requ_cond=""; $requ_table="";
			if($txt_req_no!="") 
			{
				$requ_cond=" and e.id=f.mst_id and c.requisition_dtls_id=f.id and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.requ_no like '%$txt_req_no%'";
				$requ_table=", inv_purchase_requisition_mst e, inv_purchase_requisition_dtls f";
			}

			if($txt_wo_po_no!="" || $txt_req_no!="") 
			{
				$addition_table_cond.=" , wo_non_order_info_dtls c, wo_non_order_info_mst d $requ_table";
				if($cbo_item_category_id) $sql_cond_category_cond = " and c.item_category_id=$cbo_item_category_id"; else $sql_cond_category_cond="";
				$sql_cond.=" and b.work_order_dtls_id=c.id and c.mst_id=d.id $sql_cond_category_cond and b.work_order_no like '%$txt_wo_po_no%' $requ_cond";
				$wo_pi_select.=", c.id as wo_dtls_id, c.requisition_dtls_id";
			}
			
			
			if($txt_date_from!="" && $txt_date_to!="") $sql_cond.=" and a.pi_date between  '$txt_date_from' and '$txt_date_to'";
			

			//-----------------------------------------Starts With PI-----------------------------------------

			$sql_pi="SELECT a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, b.item_category_id as item_category_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, case when b.item_prod_id !='' or b.item_prod_id is not null then b.item_prod_id else 0 end as prod_id, b.work_order_no, b.uom, b.quantity, b.work_order_dtls_id, b.amount $wo_pi_select 
			from com_pi_master_details a, com_pi_item_details b $addition_table_cond where a.id=b.pi_id $sql_cond and b.item_category_id not in (2,3,12,13,14,24,25,28,30) and a.pi_basis_id <> 2";


			//if($cbo_supplier>0 && $txt_wo_po_no=="" || $txt_req_no=="")
			//{
				if($cbo_supplier) $sql_pi_cond = " and a.supplier_id='$cbo_supplier'";
				if($txt_date_from!="" && $txt_date_to!="") $piDateCond=" and a.pi_date between  '$txt_date_from' and '$txt_date_to'";
				$sql_pi .= " union all select a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, b.item_category_id as item_category_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, 
				case when b.item_prod_id !='' or b.item_prod_id is not null then b.item_prod_id else 0 end as prod_id, b.work_order_no, b.uom, b.quantity, b.work_order_dtls_id, b.amount 
				from com_pi_master_details a, com_pi_item_details b 
				where a.id=b.pi_id and a.importer_id='$cbo_company_name' $sql_pi_cond and b.item_category_id not in (2,3,12,13,14,24,25,28,30) $piDateCond and a.pi_basis_id = 2 order by item_category_id";
			//}

		}
		if($cbo_date_type==4 && ($txt_date_from!="" && $txt_date_to!=""))
		{

			if($txt_date_from!="" && $txt_date_to!="") $sql_cond.=" and a.lc_date between  '$txt_date_from' and '$txt_date_to'";
			$requ_cond=""; $requ_table="";

			if($txt_req_no!="") 
			{
				$requ_cond=" and e.id=f.mst_id and c.requisition_dtls_id=f.id and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.requ_no like '%$txt_req_no%'";
				$requ_table=", inv_purchase_requisition_mst e, inv_purchase_requisition_dtls f";
			}

			if($txt_wo_po_no!="" || $txt_req_no!="") 
			{
				if($cbo_item_category_id) $addi_sql_cond_category_cond = " and c.item_category_id=$cbo_item_category_id";else $addi_sql_cond_category_cond = "";

				
				$wo_requ_pi_res =  sql_select("select g.id as pi_id from com_pi_master_details g, com_pi_item_details h , wo_non_order_info_dtls c, wo_non_order_info_mst d $requ_table where g.id = h.pi_id and h.work_order_dtls_id = c.id and c.mst_id = d.id $addi_sql_cond_category_cond and h.work_order_no like '%$txt_wo_po_no%' $requ_cond");

				foreach ($wo_requ_pi_res as $value) 
				{
					if($value[csf("pi_id")])
					{
						$refrID1=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$value[csf("pi_id")].",4,".$user_id.")");
						if(!$refrID1)
						{
							echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$value[csf("pi_id")].",4,".$user_id.")";oci_rollback($con);disconnect($con);die;
						}
						$temp_table_id++;
					}
					$all_pi_arr[$value[csf("pi_id")]]=$value[csf("pi_id")];
				}
			}
			
			if($refrID1)
			{
				oci_commit($con);
			}
			$refrID1=1;
			//independent pi 

			if( $txt_wo_po_no=="" || $txt_req_no=="")
			{
				if($cbo_item_category_id) $addi_sql_pi_independent_cond = " and b.item_category_id=$cbo_item_category_id";else $addi_sql_pi_independent_cond = "";
				if($cbo_supplier) $sql_pi_independent = " and a.supplier_id='$cbo_supplier'";else $sql_pi_independent = "";
				$sql_pi_independent = sql_select("SELECT a.id as pi_id
				from com_pi_master_details a, com_pi_item_details b 
				where a.id=b.pi_id and a.importer_id='$cbo_company_name' and b.item_category_id not in (2,3,12,13,14,24,25,28,30) $addi_sql_pi_independent_cond and a.pi_basis_id = 2");

				foreach ($sql_pi_independent as $value) 
				{
					if($value[csf("pi_id")])
					{
						$refrID1=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$value[csf("pi_id")].",4,".$user_id.")");
						if(!$refrID1)
						{
							echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$value[csf("pi_id")].",4,".$user_id.")";oci_rollback($con);disconnect($con);die;
						}
						$temp_table_id++;
					}
					$all_pi_arr[$value[csf("pi_id")]]=$value[csf("pi_id")];
				}
			}

			if($refrID1)
			{
				oci_commit($con);
			}
			$refrID1=1;
			
			//$all_pi_ids = implode(",", array_filter($all_pi_arr));
			if(count($all_pi_arr)>0) 
			{
				/*$btbPiCond = $all_btb_pi_cond = ""; 
				$all_pi_arr=explode(",",$all_pi_ids);
				if($db_type==2 && count($all_pi_arr)>999)
				{
					$all_pi_chunk=array_chunk($all_pi_arr,999) ;
					foreach($all_pi_chunk as $chunk_arr)
					{
						$btbPiCond.=" b.pi_id in(".implode(",",$chunk_arr).") or ";	
					}
							
					$all_btb_pi_cond.=" and (".chop($btbPiCond,'or ').")";			
					
				}
				else
				{ 	
					
					$all_btb_pi_cond=" and b.pi_id in($all_pi_ids)";
					  
				}*/
				$all_lc_pi_arr=array();
				$all_lc_pi=sql_select("select b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b, gbl_temp_report_id c where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.ref_val and c.ref_from=4 and a.status_active=1 and a.is_deleted=0 $sql_cond");
				foreach ($all_lc_pi as $value) 
				{
					if($value[csf("pi_id")])
					{
						$refrID1=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$value[csf("pi_id")].",4,".$user_id.")");
						if(!$refrID1)
						{
							echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$value[csf("pi_id")].",4,".$user_id.")";oci_rollback($con);disconnect($con);die;
						}
						$temp_table_id++;
					}
					$all_lc_pi_arr[$value[csf("pi_id")]]=$value[csf("pi_id")];
				}
				
				if($refrID1)
				{
					oci_commit($con);
				}
				$refrID1=1;
			}
			
			$sql_pi="SELECT a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, b.item_category_id as item_category_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, case when b.item_prod_id !='' or b.item_prod_id is not null then b.item_prod_id else 0 end as prod_id, b.work_order_no, b.uom, b.quantity, b.work_order_dtls_id, b.amount 
			from com_pi_master_details a, com_pi_item_details b, gbl_temp_report_id c 
			where a.id=b.pi_id and b.pi_id=c.ref_val and c.ref_from=4 and b.item_category_id not in (2,3,12,13,14,24,25,28,30) order by item_category_id "; //and a.pi_basis_id <> 2

			
		}		
			
		//echo "<br>".$sql_pi;
		$refrID1=1;
		$sql_pi_result=sql_select($sql_pi);
		$pi_data_arr=$pi_id_arr=$wo_num_arr=$wo_prod_arr=array();
		foreach($sql_pi_result as $row)
		{
			$pi_id_arr[$row[csf("pi_id")]]=$row[csf("pi_id")];
			$wo_num_arr[$row[csf("work_order_dtls_id")]]=$row[csf("work_order_dtls_id")];
			$wo_prod_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
			
			if($row[csf("pi_id")])
			{
				$refrID1=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",1,".$user_id.")");
				if(!$refrID1)
				{
					echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",1,".$user_id.")";oci_rollback($con);disconnect($con);die;
				}
				$temp_table_id++;
			}
			
			if($row[csf("work_order_dtls_id")])
			{
				$refrID2=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("work_order_dtls_id")].",2,".$user_id.")");
				if(!$refrID2)
				{
					echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("work_order_dtls_id")].",2,".$user_id.")";oci_rollback($con);disconnect($con);die;
				}
				$temp_table_id++;
			}
			
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["requisition_dtls_id"]=$row[csf("requisition_dtls_id")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_id"].=$row[csf("pi_id")].",";
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_number"].=$row[csf("pi_number")].",";
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_date"]=$row[csf("pi_date")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]=$row[csf("supplier_id")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["currency_id"]=$row[csf("currency_id")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["intendor_name"]=$row[csf("intendor_name")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["prod_id"].=$row[csf("prod_id")].",";
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["uom"]=$row[csf("uom")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["quantity"]+=$row[csf("quantity")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]+=$row[csf("amount")];
			$pi_data_arr[$row[csf("pi_id")]][$row[csf("prod_id")]]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
		} 
		
		unset($sql_pi_result);
		
		if($refrID1 && $refrID2)
		{
			oci_commit($con);
		}
		
		/*echo "<pre>";
		print_r($pi_data_arr[704]);
		die;
		//findind WO/PO if exists....
		$wo_num_ids="";
		foreach (array_filter(array_unique($wo_num_arr)) as $values) 
		{
			$wo_num_ids.="'".$values."',";
		}
		$wo_num_ids=chop($wo_num_ids,",");

		if($wo_num_ids=="") $wo_num_ids=0;
		$woNumCond = $wo_num_cond = ""; 
		$wo_num_arr=explode(",",$wo_num_ids);
		if($db_type==2 && count($wo_num_arr)>999)
		{
			$wo_num_chunk=array_chunk($wo_num_arr,999) ;
			foreach($wo_num_chunk as $chunk_arr)
			{
				$woNumCond.=" a.wo_number in(".implode(",",$chunk_arr).") or ";	
			}
					
			$wo_num_cond.=" and (".chop($woNumCond,'or ').")";			
			
		}
		else
		{ 	
			
			$wo_num_cond=" and a.wo_number in($wo_num_ids)";
		}*/
		
		$refrID3=1;
		if(count($wo_num_arr)>0)
		{
			$sql_wo=sql_select("SELECT a.id as wo_mst_id, a.wo_number, a.wo_number_prefix_num, a.wo_date, a.supplier_id, b.id as wo_dtls_id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name, b.requisition_dtls_id, b.item_id as prod_id, b.item_category_id, b.supplier_order_quantity, b.rate, b.amount 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b, gbl_temp_report_id c 
			where a.id=b.mst_id and b.id=c.ref_val and c.ref_from=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_num_cond "); //and a.wo_number in ($wo_num_ids)

			$wo_data_array=array();
			$req_dtls_id_arr=array();
			foreach($sql_wo as $row)
			{
				$req_dtls_id_arr[$row[csf("requisition_dtls_id")]]=$row[csf("requisition_dtls_id")];
				
				if($row[csf("requisition_dtls_id")])
				{
					$refrID3=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("requisition_dtls_id")].",3,".$user_id.")");
					if(!$refrID3)
					{
						echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("requisition_dtls_id")].",3,".$user_id.")";oci_rollback($con);disconnect($con);die;
					}
					$temp_table_id++;
				}
				
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["requisition_dtls_id"]=$row[csf("requisition_dtls_id")];
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["wo_mst_id"].=$row[csf("wo_mst_id")].",";
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["wo_number"].=$row[csf("wo_number")].",";
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["wo_number_prefix_num"].=$row[csf("wo_number_prefix_num")].",";
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["wo_date"]=$row[csf("wo_date")];
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]=$row[csf("supplier_id")];
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
				$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["amount"]+=$row[csf("amount")];

				if ($cbo_item_category_id==1) 
				{	
					$wo_data_array[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["item_description"]=$yarn_count_arr[$row[csf("yarn_count")]]." ".$composition[$row[csf("yarn_comp_type1st")]]." ".$row[csf("yarn_comp_percent1st")]." ".$yarn_type[$row[csf("yarn_type")]]." ".$color_name_arr[$row[csf("color_name")]];
				}
			}
			
			unset($sql_wo);	
			if($refrID3)
			{
				oci_commit($con);
			}
		}

		//Finding requisition data if exists....

		if(count($req_dtls_id_arr)>0)
		{
			//$req_dtsl_ids=trim(implode(",", array_filter(array_unique($req_dtls_id_arr))),",");
			if($cbo_item_category_id) $res_sql_category_cond = "and b.item_category=$cbo_item_category_id"; else $res_sql_category_cond= "";
			$req_sql=sql_select("select a.id as req_id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date, b.item_category as item_category_id, b.id as req_dtsl_id, b.product_id as prod_id, b.cons_uom, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.color_id, b.required_for, b.quantity, b.rate, b.amount 
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, gbl_temp_report_id c  
			where a.id=b.mst_id and b.id=c.ref_val and c.ref_from=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name $res_sql_category_cond ");

			$req_data_array=array();
			foreach($req_sql as $row)
			{
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["req_id"]=$row[csf("req_id")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["requ_no"]=$row[csf("requ_no")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["requ_prefix_num"]=$row[csf("requ_prefix_num")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["requisition_date"]=$row[csf("requisition_date")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["store_name"]=$row[csf("store_name")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["pay_mode"]=$row[csf("pay_mode")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["cbo_currency"]=$row[csf("cbo_currency")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["delivery_date"]=$row[csf("delivery_date")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["required_for"]=$row[csf("required_for")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["quantity"]=$row[csf("quantity")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["rate"]=$row[csf("rate")];
				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["amount"]=$row[csf("amount")];

				$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["cons_uom"]=$row[csf("cons_uom")];
				if ($cbo_item_category_id==1) 
				{
					$req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["item_description"]=$yarn_count_arr[$row[csf("count_id")]]." ".$composition[$row[csf("composition_id")]]." ".$row[csf("com_percent")]." ".$yarn_type[$row[csf("yarn_type_id")]]." ".$color_name_arr[$row[csf("color_id")]];
				}
			}
			
			unset($req_sql);		
		}


		//finding BTB Lc data....
		if(count($pi_id_arr)>0)
		{
			$sql_btb=sql_select("select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id 
			from com_btb_lc_master_details a, com_btb_lc_pi b, gbl_temp_report_id c  
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.ref_val and c.ref_from=1 and a.status_active=1 and a.is_deleted=0");
			/*echo "select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.status_active=1 and a.is_deleted=0 and b.pi_id in (".trim(implode(",",$pi_id_arr),",").")";*/
			$btb_data_array=array();
			foreach($sql_btb as $row)
			{
				$btb_data_array[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
				$btb_data_array[$row[csf("pi_id")]]["lc_id"]=$row[csf("lc_id")];
				$btb_data_array[$row[csf("pi_id")]]["lc_number"]=$row[csf("lc_number")];
				$btb_data_array[$row[csf("pi_id")]]["lc_date"]=$row[csf("lc_date")];
				$btb_data_array[$row[csf("pi_id")]]["payterm_id"]=$row[csf("payterm_id")];
				$btb_data_array[$row[csf("pi_id")]]["tenor"]=$row[csf("tenor")];
				$btb_data_array[$row[csf("pi_id")]]["lc_value"]=$row[csf("lc_value")];
				$btb_data_array[$row[csf("pi_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
				$btb_data_array[$row[csf("pi_id")]]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
			}
			unset($sql_btb);
			//Finding Invoice data....
			if($db_type==0)
			{
				$pi_cond="group_concat(a.pi_id) as pi_id";
			}
			else if($db_type==2)
			{
				$pi_cond="LISTAGG(CAST(a.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.pi_id) as pi_id";
			}
			
			$sql_invoice_pay=sql_select("select $pi_cond, b.id as invoice_id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, e.payterm_id, b.id as accept_id 
			from gbl_temp_report_id c, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_btb_lc_master_details e 
			where c.ref_val=a.pi_id and c.ref_from=1 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and e.id = a.com_btb_lc_master_details_id and b.status_active=1 and b.is_deleted=0 
			group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, e.payterm_id");
			
			$inv_pay_data_array=array();
			$temp_inv_array=$temp_accept_id=array();
			foreach($sql_invoice_pay as $row)
			{
				$all_pi_id=array_unique(explode(",",$row[csf("pi_id")]));
				foreach($all_pi_id as $pi_id)
				{
					$inv_pay_data_array[$pi_id]["pi_id"]=$pi_id;
					$inv_pay_data_array[$pi_id]["invoice_id"].=$row[csf("invoice_id")].",";
					$inv_pay_data_array[$pi_id]["invoice_no"].=$row[csf("invoice_no")].",";
					$inv_pay_data_array[$pi_id]["document_value"]+=$row[csf("document_value")];
					$inv_pay_data_array[$pi_id]["invoice_date"]=$row[csf("invoice_date")];
					$inv_pay_data_array[$pi_id]["inco_term"]=$row[csf("inco_term")];
					$inv_pay_data_array[$pi_id]["inco_term_place"]=$row[csf("inco_term_place")];
					$inv_pay_data_array[$pi_id]["bill_no"]=$row[csf("bill_no")];
					$inv_pay_data_array[$pi_id]["bill_date"]=$row[csf("bill_date")];
					$inv_pay_data_array[$pi_id]["mother_vessel"]=$row[csf("mother_vessel")];
					$inv_pay_data_array[$pi_id]["feeder_vessel"]=$row[csf("feeder_vessel")];
					$inv_pay_data_array[$pi_id]["container_no"]=$row[csf("container_no")];
					$inv_pay_data_array[$pi_id]["doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$inv_pay_data_array[$pi_id]["bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$inv_pay_data_array[$pi_id]["maturity_date"]=$row[csf("maturity_date")];

					if($temp_inv_array[$row[csf("invoice_id")]]=="")
					{
						$temp_inv_array[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
						$inv_pay_data_array[$pi_id]["pkg_quantity"]+=$row[csf("pkg_quantity")];
						
					}

					/*if($row[csf("payterm_id")]==1) //Pay Term = At sight
					{
						$inv_pay_data_array[$pi_id]["payment_date"]=$row[csf("invoice_date")];

						$cumulative_array=return_library_array("select pi_id, sum(current_acceptance_value) as accepted_ammount from com_import_invoice_dtls where pi_id=$pi_id and status_active=1 and is_deleted=0 group by pi_id",'pi_id','accepted_ammount'); 
						$inv_pay_data_array[$pi_id]["accepted_ammount"]=$cumulative_array[$pi_id];
					}*/
				}
				
			}
			
			unset($sql_invoice_pay);

			//Finding invoice Payment data
			if ($db_type==0)
			{
				$sql_invoice_pay2= sql_select("select group_concat(a.pi_id) as pi_id, c.payment_date, c.id as accept_id, c.accepted_ammount 
				from gbl_temp_report_id p, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_import_payment c 
				where p.ref_val=a.pi_id and p.ref_from=1 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and b.id=c.invoice_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, c.payment_date, c.id, c.accepted_ammount");
			}
			else
			{
				$sql_invoice_pay2= sql_select("select LISTAGG(CAST(a.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.pi_id) as pi_id, c.payment_date, c.id as accept_id, c.accepted_ammount 
				from gbl_temp_report_id p, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_import_payment c 
				where p.ref_val=a.pi_id and p.ref_from=1 a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and b.id=c.invoice_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
				group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, c.payment_date, c.id, c.accepted_ammount");
			}
			
			foreach($sql_invoice_pay2 as $row)
			{
				$all_pi_id=array_unique(explode(",",$row[csf("pi_id")]));
				foreach($all_pi_id as $pi_id)
				{
					$inv_pay_data_array[$pi_id]["payment_date"]=$row[csf("payment_date")];
				}
				if($temp_accept_id[$row[csf("accept_id")]]=="")
				{
					$temp_accept_id[$row[csf("accept_id")]]=$row[csf("accept_id")];
					$inv_pay_data_array[$pi_id]["accepted_ammount"]+=$row[csf("accepted_ammount")];
				}
			}
			
			unset($sql_invoice_pay2);
			
			//----------------------------------End-------------------------------------------------------
		}

		if($cbo_item_category_id) $sql_receive_category_cond = " and b.item_category in($cbo_item_category_id)"; else $sql_receive_category_cond ="";
		if ($cbo_item_category_id==1) 
		{
			$sql_receive=sql_select("select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, c.product_name_details, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt 
			from inv_receive_master a, inv_transaction b, product_details_master c 
			where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) $sql_receive_category_cond and b.prod_id=c.id and c.status_active=1 and c.is_deleted=0 
			group by a.receive_basis, b.prod_id,pi_wo_batch_no, c.product_name_details");
			$recv_data_array=array();
			foreach($sql_receive as $row)
			{
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["receive_basis"]=$row[csf("receive_basis")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["prod_id"]=$row[csf("prod_id")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["rcv_amt"]=$row[csf("rcv_amt")];
			}
		}
		else
		{
			$sql_receive=sql_select("select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt from inv_receive_master a, inv_transaction b where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) $sql_receive_category_cond group by a.receive_basis, b.prod_id,pi_wo_batch_no");
			$recv_data_array=array();
			foreach($sql_receive as $row)
			{
				$recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["receive_basis"]=$row[csf("receive_basis")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["rcv_amt"]=$row[csf("rcv_amt")];
			}
		}
		unset($sql_receive);
		$gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
		if($gblDel)
		{
			oci_commit($con);disconnect($con);
		}
		$item_group_array = return_library_array("select id,item_name from  lib_item_group ","id","item_name");
		$store_array = return_library_array("select id,store_name from  lib_store_location ","id","store_name");
		$suplier_array = return_library_array("select id,supplier_name from  lib_supplier ","id","supplier_name");
		$indentor_name_array = return_library_array("select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b where a.id = b.supplier_id and b.party_type = 40","id","supplier_name");
		ob_start();
		?>
			<div style="width:5660px; margin-left:10px">
	        <fieldset style="width:100%;">	 
	            <table width="5500" cellpadding="0" cellspacing="0" id="caption">
	                <tr>
	                	<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	                </tr> 
	                <tr>  
	                	<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
	                </tr>  
	            </table>
	        	<br />
	            
	                <?
					$i=1;
					$btb_tem_lc_array=$inv_temp_array=array();
					foreach ($sql_pi_result as  $row) 
					{
						$wo_no=$wo_supplier="";$wo_qnty=$wo_rate=$wo_amount=$wo_balance=0;
						$pi_no=$pi_date=$pi_suplier=$pi_indore_name=$pi_id_all=$rcv_qnty=$rcv_value=$wo_mst_id_all=$pipe_line=$short_value=$pipe_pi_qnty=$pipe_wo_qnty=""; $pi_rate=0;
						$lc_date=$lc_no=$lc_pay_term=$lc_tenor=$lc_amt=$lc_ship_date=$lc_expire_date="";
						$invoice_id=$invoice_no=$invoice_date=$inco_term=$inco_term_place=$bl_no=$bl_date=$mother_vasel=$feder_vasel=$continer_no=$pakag_qnty=$doc_send_cnf=$bill_entry_no=$maturity_date=$maturity_month=$pay_date=$pay_amt="";
						
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						if (!in_array($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"], $checkCateArr))
						{
							$checkCateArr[] = $pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"];
							if($i>1)
							{
								?>
								</tbody>
	            				</table>
								<?
							}
							
							$j=1;



						?>
						<br>
						<table width="5660" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
	                <thead>
	                	<tr>
	                		<th colspan="63" style="text-align: left !important; color: black"><? echo $item_category[$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?> :</th>
	                	</tr>
	                    <tr>
	                        <th colspan="13" >Requisiton Details</th>
	                        <th colspan="12" >Work Order Details</th>
	                        <th colspan="10" >PI Details</th>
	                        <th colspan="7">L/C Details</th>
	                        <th colspan="13">Invoice Details</th>
	                        <th colspan="4">Payment Details</th>
	                        <th colspan="4">Store details</th>
	                    </tr>
	                    <tr>
	                        <!--1210 requisition details-->    
	                        <th width="30">SL</th>
	                        <th width="50">Req. No</th>
	                        <th width="70">Req. Date</th>
	                        <th width="150">Store Name</th>
	                        <th width="70">Delivery Date</th>
	                        <th width="100">Item Category</th>
	                        <th width="100">Item Group</th> 
	                        <th width="100">Item Sub. Group</th>
	                        <th width="80">Item Code</th>
	                        <th width="150">Item Description</th>
	                        <th width="100">Required For</th>
	                        <th width="70"> UOM</th>
	                        <th width="100">Req. Quantity </th>
	                        
	                        <!--1110 wo details-->
	                        <th width="50">WO No</th>
	                        <th width="100">Item Category</th>
	                        <th width="100">Item Group</th>
	                        <th width="100">Item Sub. Group</th>
	                        <th width="80">Item Code</th>
	                        <th width="150">Item Description</th>
	                        <th width="80">WO Qnty</th>
	                        <th width="80">Wo Rate</th>
	                        <th width="80">WO Amount</th>
	                        <th width="70">WO Date</th>
	                        <th width="80">WO Balance</th>
	                        <th width="150">Supplier</th>
	                        
	                        <!--840 pi details-->
	                        <th width="130">PI No</th>
	                        <th width="70">PI Date</th>
	                        <th width="150">Supplier</th>
	                        <th width="100">Item Category</th>
	                        <th width="70">UOM</th>
	                        <th width="80">PI Quantity</th>
	                        <th width="80">Unit Price</th>
	                        <th width="80">PI Value</th>
	                        <th width="70">Currency</th>
	                        <th width="100">Indentor Name</th>
	                        
	                        <!--550 lc details-->
	                        <th width="70">LC Date</th>
	                        <th width="120">LC No</th>
	                        <th width="80">Pay Term</th>
	                        <th width="50">Tenor</th>
	                        <th width="80">LC Amount</th>
	                        <th width="70">Shipment Date</th>
	                        <th width="80">Expiry Date</th>
	                        
	                        <!--1100 Invoice details-->
	                        <th width="150">Invoice No</th>
	                        <th width="70">Invoice Date</th>
	                        <th width="80">Incoterm</th>
	                        <th width="100">Incoterm Place</th>
	                        <th width="80">B/L No</th>
	                        <th width="70">BL Date</th>
	                        <th width="100">Mother Vassel</th>
	                        <th width="100">Feedar Vassel</th>
	                        <th width="100">Continer No</th>
	                        <th width="80">Pkg Qty</th>
	                        <th width="100">Doc Send to CNF</th>
	                        <th width="70">NN Doc Received Date</th>
	                        <th width="80">Bill Of Entry No</th>
	                        
	                        <!--290 Payment details-->
	                        <th width="70">Maturity Date</th>
	                        <th width="70">Maturity Month</th>
	                        <th width="70">Payment Date</th>
	                        <th width="80">Paid Amount</th>
	                        
	                        <!--340 MRR details-->
	                        <th width="80">MRR Qnty</th>
	                        <th width="80">MRR Value</th>
	                        <th width="80">Short Value</th>
	                        <th >Pipeline</th>
	                    </tr>
	                </thead>
	            </table>
	            <!-- <div style="width:5660px; max-height:300px; overflow-y:scroll" id="scroll_body"> -->
	            <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body">
	            	<tbody>
	            		<? 
	            			}
	            		?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

	                    	<? //------------------------------Requisition dtls start----------------------------------------- ?>
	                        <td width="30" align="center"><p><? echo $j; ?></p></td>
	                        <?
	                        	$requ_dtls_id=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["requisition_dtls_id"];
	                        ?>
	                        <td width="50" align="center"><p><? echo $req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["requ_prefix_num"]; ?></p></td>
	                        <td width="70" align="center"><p>
	                        	<? 
	                        	$req_date=$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["requisition_date"];
	                        	if($req_date!="" && $req_date!="0000-00-00") echo change_date_format($req_date); 
	                        	?></p>
	                        </td>
	                        <td width="150"><p><? echo $store_array[$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["store_name"]]; ?></p></td>
	                        <td width="70" align="center"><p>
	                        	<? 
	                        	$delivry_date=$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["delivery_date"];
	                        	if($delivry_date!="" && $delivry_date!="0000-00-00") echo change_date_format($delivry_date); 
	                        	?></p>
	                        </td>
							<td width="100"><p><? echo $item_category[$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>

							<? $prod_id=$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["prod_id"]; ?>

	                        <td width="100"><p><? echo $item_group_array[$prod_data_array[$prod_id]["item_group_id"]]; ?></p></td>
	                        <td width="100"><p><? echo $prod_data_array[$prod_id]["sub_group_name"]; ?></p></td>
	                        <td width="80"><p><? echo $prod_data_array[$prod_id]["item_code"]; ?></p></td>
	                        <td width="150"><p>
	                        	<? 
	                        		if($cbo_item_category_id==1)
	                        		{
	                        			echo $req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["item_description"];
	                        		}
	                        		else
	                        		{
	                        			echo $prod_data_array[$prod_id]["item_description"]; 
	                        		}
	                        	?>
	                        </p></td>
	                        <td width="100"><p><? echo $req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["required_for"]; ?></p></td>
	                        <td width="70"><p>
	                        	<? 
	                        		echo $unit_of_measurement[$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["cons_uom"]]; 
	                        	?>
	                        </p></td>
	                        <td width="100" align="right"><p>
	                        	<? 
	                        		$req_qty=$req_data_array[$requ_dtls_id][$row[csf("prod_id")]]["quantity"];
	                        		echo number_format($req_qty,2);
	                        		$total_req_qnty+=$req_qty; 
	                        	?>
	                        </p></td>
	                        <? //------------------------------WO dtls start------------------------------------------- ?>
	             
							<td width="50" align="center"><p>
							<?
							$wo_no=implode(",",array_unique(explode(",",chop($wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["wo_number_prefix_num"]," , "))));
							echo $wo_no;
							?>
							</p></td>
							<td width="100"><p><? echo $item_category[$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>

							<? $wo_prod_id=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["prod_id"]; ?>

							<td width="100"><p><? echo $item_group_array[$prod_data_array[$wo_prod_id]["item_group_id"]]; ?></p></td> 
							<td width="100"><p><? echo $prod_data_array[$wo_prod_id]["sub_group_name"]; ?></p></td>
							<td width="80"><p><? echo $prod_data_array[$wo_prod_id]["item_code"]; ?></p></td>
							<td width="150"><p>
								<? 
									if ($cbo_item_category_id==1) 
									{
										echo $wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_description"];
									}
									else
									{
										echo $prod_data_array[$wo_prod_id]["item_description"]; 
									}
								?>
							</p></td>
							<td width="80" align="right"><p>
								<? 
								echo number_format($wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"],2); 
								$total_wo_qnty+=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"]; 
								?>
							</p></td>
							<td width="80" align="right"><p>
								<? 
								$wo_rate=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]/$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"]; 
								echo number_format($wo_rate,2);  
								?>
							</p></td>
							<td width="80" align="right"><p>
								<? 
								echo number_format($wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"],2); 
								$total_wo_amt+=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]; 
								?>
							</p></td>
							<td width="70" title="last wo date" align="center"><p>
								<? 
								$wo_po_date=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["wo_date"];
								if($wo_po_date!="" && $wo_po_date!="0000-00-00") echo change_date_format($wo_po_date); 
								?>
							</p></td>
							<td width="80" align="right" title="Requisition Quantity-Wo Quantity"><p>
                            <?
                            if($req_qty !=0 || $req_qty !="")
                            {
								$wo_balance=$req_qty-$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"];
								echo number_format($wo_balance,2); 
							}
							$total_wo_balance+=$wo_balance;
							?>
                            </p></td>
							<td width="150"><p>
								<? $wo_supplier=$suplier_array[$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]]; 
								   echo $wo_supplier;
								?>
							</p></td>

							<? //------------------------------PI dtls start------------------------------------------- ?>
								<?
							if(chop($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_id"],",") !="")
							{
								?>
								<td width="130" align="center"><p>
								<?
								$pi_no=implode(",",array_unique(explode(",",chop($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_number"],","))));
								echo $pi_no;
								?>
								</p></td>
								<td width="70" align="center" title="Last PI Date"><p>
									<? 
									$pi_date_data=$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_date"];
									if($pi_date_data!="" && $pi_date_data!="0000-00-00") echo change_date_format($pi_date_data); 
									?>
								</p></td>
								<td width="150"><p>
								<? 
								$pi_suplier=$suplier_array[$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]]; 
								echo $pi_suplier; 
								?> 
								</p></td>
								<td width="100"><p><? echo $item_category[$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>
								<td width="70" align="center"><P><? echo $unit_of_measurement[$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["uom"]]; ?></P></td>
								<td width="80" align="right"><p>
									<? 
									echo number_format($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["quantity"],2); 
									$total_pi_qnty+=$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["quantity"]; 
									?>
								</p></td>
								<td width="80" align="right"><P>
									<? 
									$pi_rate=$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]/$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["quantity"]; 
									echo number_format($pi_rate,2); 
									?>
								</P></td>
								<td width="80" align="right"><p>
									<? 
									echo number_format($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"],2); 
									$total_pi_amt+=$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]; 
									?>
								</p></td>
								<td width="70"><P><? echo $currency[$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["currency_id"]]; ?></P></td>
								<td width="100"><p>
									<? 
									$pi_indore_name=$indentor_name_array[$pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["intendor_name"]]; 
									echo $pi_indore_name; 
									?>
								</p></td>

								<? //------------------------------LC dtls start------------------------------------------- ?>


	                            <?
								$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_id"],",")));

								foreach($pi_id_arr as $piID)
								{	
									if(!in_array($btb_data_array[$piID]["lc_id"],$btb_tem_lc_array))
									{
										$btb_tem_lc_array[$btb_data_array[$piID]["lc_id"]]=$btb_data_array[$piID]["lc_id"];
										$lc_date=$btb_data_array[$piID]["lc_date"];
										$lc_no.=$btb_data_array[$piID]["lc_number"].",";
										$lc_pay_term=$pay_term[$btb_data_array[$piID]["payterm_id"]];
										$lc_tenor+=$btb_data_array[$piID]["tenor"];
										$lc_amt+=$btb_data_array[$piID]["lc_value"];
										$lc_ship_date=$btb_data_array[$piID]["last_shipment_date"];
										$lc_expire_date=$btb_data_array[$piID]["lc_expiry_date"];
									}
									
									if(!in_array($inv_pay_data_array[$piID]["invoice_id"],$inv_temp_array))
									{
										$inv_temp_array[$inv_pay_data_array[$piID]["invoice_id"]]=$inv_pay_data_array[$piID]["invoice_id"];
										$invoice_id.=$inv_pay_data_array[$piID]["invoice_id"].",";
										$invoice_no.=$inv_pay_data_array[$piID]["invoice_no"].",";
										$invoice_date=$inv_pay_data_array[$piID]["invoice_date"];
										$inco_term=$inv_pay_data_array[$piID]["inco_term"];
										$inco_term_place=$inv_pay_data_array[$piID]["inco_term_place"];
										$bl_no=$inv_pay_data_array[$piID]["bill_no"];
										$bl_date=$inv_pay_data_array[$piID]["bill_date"];
										$mother_vasel=$inv_pay_data_array[$piID]["mother_vessel"];
										$feder_vasel=$inv_pay_data_array[$piID]["feeder_vessel"];
										$continer_no=$inv_pay_data_array[$piID]["container_no"];
										$pakag_qnty=$inv_pay_data_array[$piID]["pkg_quantity"];
										$doc_send_cnf=$inv_pay_data_array[$piID]["doc_to_cnf"];
										$bill_entry_no=$inv_pay_data_array[$piID]["bill_of_entry_no"];
										$maturity_date=$inv_pay_data_array[$piID]["maturity_date"];
										$maturity_month=$inv_pay_data_array[$piID]["maturity_date"];
										
										$pay_date=$inv_pay_data_array[$piID]["payment_date"];
										$pay_amt+=$inv_pay_data_array[$piID]["accepted_ammount"];
									}	
								}
								?>
								<td width="70" align="center"><P><? if($lc_date!="" && $lc_date!="0000-00-00") echo change_date_format($lc_date); ?></P></td>
								<td width="120"><P><? echo $lc_no=implode(",",array_unique(explode(",",chop($lc_no," , ")))); ?></P></td>
								<td width="80"><P><? echo $lc_pay_term; ?></P></td>
								<td width="50" align="center"><P><? echo $lc_tenor; ?></P></td>
								<td width="80" align="right"><P><? echo number_format($lc_amt,2); $total_lc_amt+=$lc_amt; ?></P></td>
								<td width="70" align="center" title="Last Ship Date"><P><? if($lc_ship_date!="" && $lc_ship_date!="0000-00-00") echo change_date_format($lc_ship_date); ?></P></td>
								<td width="80"  align="center" title="Last Expire Date"><P><? if($lc_expire_date!="" && $lc_expire_date!="0000-00-00") echo change_date_format($lc_expire_date); ?></P></td>

								<? //------------------------------Invoice dtls start------------------------------------------- ?>
								
								<td width="150"><P><? echo $invoice_no=implode(",",array_unique(explode(",",chop($invoice_no," , ")))); ?></P></td>
								<td width="70" align="center" title="Last Invoice Date"><P><? if($invoice_date!="" && $invoice_date!="0000-00-00") echo change_date_format($invoice_date); ?></P></td>
								<td width="80"><P><? echo $inco_term; ?></P></td>
								<td width="100"><P><? echo $inco_term_place; ?></P></td>
								<td width="80"><P><? echo $bl_no; ?></P></td>
								<td width="70" align="center"><P><? if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?></P></td>
								<td width="100"><P><? echo $mother_vasel; ?></P></td>
								<td width="100"><P><? echo $feder_vasel; ?></P></td>
								<td width="100"><P><? echo $continer_no; ?></P></td>
								<td width="80" align="right"><P><? echo number_format($pakag_qnty,2); $total_pkg_qnty+=$pakag_qnty; ?></P></td>
								<td width="100" align="center"><P><? if($doc_send_cnf!="" && $doc_send_cnf!="0000-00-00") echo change_date_format($doc_send_cnf); ?></P></td>
								<td width="70"></td>
								<td width="80"><P><? echo $bill_entry_no; ?></P></td>
								
								<td width="70" align="center"><P><? if($maturity_date!="" && $maturity_date!="0000-00-00") echo change_date_format($maturity_date); ?></P></td>
								<td width="70" align="center"><P><? if($maturity_month!="" && $maturity_month!="0000-00-00") echo change_date_format($maturity_month); ?></P></td>
								<td width="70" align="center"><P><? if($pay_date!="" && $pay_date!="0000-00-00") echo change_date_format($pay_date); ?></P></td>
								<td width="80" align="right"><P><? echo number_format($pay_amt,2); $total_pay_amt+=$pay_amt; ?></P></td>
								<?
							}
							else
							{
								?>
								<td width="130"></td>
								<td width="70"></td>
								<td width="150"></td>
								<td width="100"></td>
								<td width="70"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="100"></td>
	                            
								<td width="70"></td>
								<td width="120"></td>
								<td width="80"></td>
								<td width="50"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="80"></td>
								
								<td width="150"></td>
								<td width="70"></td>
								<td width="80"></td>
								<td width="100"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="80"></td>
								<td width="100"></td>
								<td width="70"></td>
								<td width="80"></td>
								
								<td width="70"></td>
								<td width="70"></td>
								<td width="70"></td>
								<td width="80"></td>
								<?
							}

							//---------------------------------Store Details starts----------------------------------

							$pi_id=chop($pi_data_arr[$row[csf("pi_id")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_id"],",");
							if ($pi_id=='') {
								$pi_id=chop($wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["wo_mst_id"],",");
							}

							if ($cbo_item_category_id==1) 
                        	{
                        		$item_desc=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_description"];
                        		$receive_basis=$recv_data_array[$pi_id][$item_desc]["receive_basis"];
                        		$prod_id=$recv_data_array[$pi_id][$item_desc]["prod_id"];
                        	}
                        	else
                        	{
                        		$receive_basis=$recv_data_array[$pi_id][$row[csf("prod_id")]]["receive_basis"];
                        		$prod_id=$row[csf("prod_id")];
                        	}
	
							$data=$pi_id."**".$receive_basis;
							?>
	                        <td width="80" align="right"><p><a href="##" onClick="openmypage_popup('<? echo $data; ?>','<? echo $prod_id; ?>','Receive Info','receive_popup');" > 
	                        	<? 
	                        		if ($cbo_item_category_id==1) 
	                        		{
	                        			$rcv_qnty=$recv_data_array[$pi_id][$item_desc]["rcv_qnty"];
	                        		}
	                        		else
	                        		{
	                        			$rcv_qnty=$recv_data_array[$pi_id][$row[csf("prod_id")]]["rcv_qnty"];
	                        		}             		

	                        		echo number_format($rcv_qnty,2); 
	                        		$total_mrr_qnty+=$rcv_qnty; 
	                        	?> 
	                        </a></p>
	                        </td>
	                        <td width="80" align="right"><p>
	                        	<? 
	                        		if ($cbo_item_category_id==1)
	                        		{
	                        			$rcv_value=$recv_data_array[$pi_id][$item_desc]["rcv_amt"];
	                        		}
	                        		else
	                        		{
	                        			$rcv_value=$recv_data_array[$pi_id][$row[csf("prod_id")]]["rcv_amt"];
	                        		}
	                        		
	                        		echo number_format($rcv_value,2); 
	                        		$total_mrr_amt+=$rcv_value; 
	                        	?></p>
	                        </td>
	                        <td align="right" title="Wo Value-Receive Value" width="80"><p>
	                        	<? 
	                        		$short_value=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]-$rcv_value;
	                        		echo number_format($short_value,2);  
	                        		$total_short_amt+=$short_value; 
	                        	?></p>
	                        </td>
	                        <?
	                        $pipe_pi_qnty=$pi_data_arr[$pi_id][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["quantity"];
	                       	$pipe_wo_qnty=$wo_data_array[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"];

							if($pipe_pi_qnty!="" && $pipe_wo_qnty =="")
							{
								$pipe_line=$pipe_pi_qnty-$rcv_qnty;
							}
							else
							{
								$pipe_line=$pipe_wo_qnty-$rcv_qnty;
							}
							?>
	                        <td  align="right"><P> 
	                        	<? 
	                        	echo number_format($pipe_line,2); 
	                        	$total_pipe_line+=$pipe_line;
	                        	?></P>
	                        </td>
	                    </tr>
	                    <?
						$i++;
						$pipe_wo_qnty=0;
						$j++;
					}
					?>
	                </tbody>
	            </table>
	            <!-- </div> -->
	            <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_footer">
	            	<tfoot>
	                    <tr>
	                        <!--1210 requisition details-->
	                        <th width="30"></th>
	                        <th width="50"></th>
	                        <th width="70"></th>
	                        <th width="150"></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="100"></th> 
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="150"></th>
	                        <th width="100"></th>
	                        <th width="70"> </th>
	                        <th width="100" id="value_total_req_qnty" align="right"><? echo number_format($total_req_qnty,0); ?> </th>
	                        
	                        <!--1110 wo details-->
	                        <th width="50"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="150"></th>
	                        <th width="80" id="value_total_wo_qnty" align="right"><? echo number_format($total_wo_qnty,0); ?></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_wo_amt" align="right"><? echo number_format($total_wo_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_wo_balance" align="right"><? echo number_format($total_wo_balanc,2); ?></th>
	                        <th width="150"></th>
	                        
	                        <!--840 pi details-->
	                        <th width="130"></th>
	                        <th width="70"></th>
	                        <th width="150"></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_pi_qnty" align="right"><? echo number_format($total_pi_qnty,0); ?></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_pi_amt" align="right"><? echo number_format($total_pi_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        
	                        <!--550 lc details-->
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_lc_amt" align="right"><? echo number_format($total_lc_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        
	                        <!--1100 Invoice details-->
	                        <th width="150"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="80" id="value_total_pkg_qnty" align="right"><? echo number_format($total_pkg_qnty,0); ?></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        
	                        <!--290 Payment details-->
	                        <th width="70"></th>
	                        <th width="70"></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_pay_amt" align="right"><? echo number_format($total_pay_amt,2); ?></th>
	                        
	                        <!--340 MRR details-->
	                        <th width="80" id="value_total_mrr_qnty" align="right"><? echo number_format($total_mrr_qnty,0); ?></th>
	                        <th width="80" id="value_total_mrr_amt" align="right"><? echo number_format($total_mrr_amt,2); ?></th>
	                        <th width="80" id="value_total_short_amt" align="right"><? echo number_format($total_short_amt,2); ?></th>
	                        <th id="value_total_pipe_line" align="right"><? echo number_format($total_pipe_line,2); ?></th>
	                    </tr>
	                </tfoot>
	            </table>
	        </fieldset>
	    	</div>
		<?
	}

    else if($txt_req_no == "" && $txt_wo_po_no == "" && $txt_pi_no == "" && $cbo_date_type==1 && ($txt_date_from && $txt_date_to))
    {
        //echo "here "."$txt_req_no && $cbo_supplier==0 && $txt_wo_po_no=='' && $cbo_date_type!=1";die;

        $gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
        if($gblDel)
        {
            oci_commit($con);
            //disconnect($con);
        }
        $temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
        if($temp_table_id=="") $temp_table_id=1;

        $sql_cond="";
        if($cbo_company_name>0) $sql_cond.=" and a.company_id='$cbo_company_name' ";
        if($cbo_item_category_id>0) $sql_cond.=" and b.item_category='$cbo_item_category_id' ";
        if($txt_req_no!="")
        {
            $sql_cond.=" and a.requ_prefix_num = '$txt_req_no' ";
        }
        if($cbo_date_type == 1 && $txt_date_from  != "" && $txt_date_to != "" ){
            $sql_cond.=" and a.requisition_date between  '$txt_date_from' and '$txt_date_to'";
        }
        $req_sql=sql_select("select a.id as req_id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date, a.item_category_id,b.item_category, b.id as req_dtsl_id, b.product_id as prod_id, b.required_for, b.quantity, b.rate, b.amount 
		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond  order by b.item_category,a.requ_no");


        if(count($req_sql) < 1)
        {
            echo "<span style='font-size:23;font-weight:bold;text-align:center;width:100%'>Data Not Found</span>";die;
        }

        $req_data_array=$req_dtls_id_arr=array();
        foreach($req_sql as $row)
        {
            $req_dtls_id_arr[$row[csf("req_dtsl_id")]]=$row[csf("req_dtsl_id")];

            if($row[csf("req_dtsl_id")])
            {
                $refrID1=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("req_dtsl_id")].",1,".$user_id.")");
                if(!$refrID1)
                {
                    echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("req_dtsl_id")].",1,".$user_id.")";oci_rollback($con);disconnect($con);die;
                }
                $temp_table_id++;
            }

            $req_data_array[$row[csf("req_dtsl_id")]]["req_id"]=$row[csf("req_id")];
            $req_data_array[$row[csf("req_dtsl_id")]]["requ_no"]=$row[csf("requ_no")];
            $req_data_array[$row[csf("req_dtsl_id")]]["requ_prefix_num"]=$row[csf("requ_prefix_num")];
            $req_data_array[$row[csf("req_dtsl_id")]]["requisition_date"]=$row[csf("requisition_date")];
            $req_data_array[$row[csf("req_dtsl_id")]]["store_name"]=$row[csf("store_name")];
            $req_data_array[$row[csf("req_dtsl_id")]]["pay_mode"]=$row[csf("pay_mode")];
            $req_data_array[$row[csf("req_dtsl_id")]]["cbo_currency"]=$row[csf("cbo_currency")];
            $req_data_array[$row[csf("req_dtsl_id")]]["delivery_date"]=$row[csf("delivery_date")];
            $req_data_array[$row[csf("req_dtsl_id")]]["item_category_id"]=$row[csf("item_category")];
            $req_data_array[$row[csf("req_dtsl_id")]]["prod_id"]=$row[csf("prod_id")];
            $req_data_array[$row[csf("req_dtsl_id")]]["required_for"]=$row[csf("required_for")];
            $req_data_array[$row[csf("req_dtsl_id")]]["quantity"]=$row[csf("quantity")];
            $req_data_array[$row[csf("req_dtsl_id")]]["rate"]=$row[csf("rate")];
            $req_data_array[$row[csf("req_dtsl_id")]]["amount"]=$row[csf("amount")];
        }
        unset($req_sql);

        if($refrID1)
        {
            oci_commit($con);
        }

        //var_dump($req_dtls_id_arr);die;
        if(!empty($req_dtls_id_arr))
        {
            //and b.requisition_dtls_id in(".implode(",",$req_dtls_id_arr).")
            if($cbo_item_category_id) $wo_category_cond = "and b.item_category_id in($cbo_item_category_id) "; else $wo_category_cond = "";
            $sql_wo=sql_select("select a.id as wo_mst_id, a.wo_number, a.wo_number_prefix_num, a.wo_date, a.supplier_id, b.id as wo_dtls_id, b.requisition_dtls_id, b.item_id as prod_id, b.supplier_order_quantity, b.rate, b.amount 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b, gbl_temp_report_id c 
			where a.id=b.mst_id and b.requisition_dtls_id=c.ref_val and c.ref_from=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name $wo_category_cond and b.item_category_id not in (1,2,3,12,13,14,24,25,28,30) ");       //$supplier_cond
            $wo_data_array=array();
            foreach($sql_wo as $row)
            {
                $wo_data_array[$row[csf("requisition_dtls_id")]]["wo_mst_id"].=$row[csf("wo_mst_id")].",";
                $wo_data_array[$row[csf("requisition_dtls_id")]]["wo_number"].=$row[csf("wo_number")].",";
                $wo_data_array[$row[csf("requisition_dtls_id")]]["wo_number_prefix_num"].=$row[csf("wo_number_prefix_num")].",";
                $wo_data_array[$row[csf("requisition_dtls_id")]]["wo_date"]=$row[csf("wo_date")];
                $wo_data_array[$row[csf("requisition_dtls_id")]]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
                $wo_data_array[$row[csf("requisition_dtls_id")]]["supplier_id"]=$row[csf("supplier_id")];
                $wo_data_array[$row[csf("requisition_dtls_id")]]["prod_id"].=$row[csf("prod_id")].",";
                $wo_data_array[$row[csf("requisition_dtls_id")]]["supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
                $wo_data_array[$row[csf("requisition_dtls_id")]]["amount"]+=$row[csf("amount")];
            }
            unset($sql_wo);
            if ($cbo_item_category_id) $pi_category_cond = " and c.item_category_id in($cbo_item_category_id)";else $pi_category_cond = "";
            $sql_pi="select a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, c.item_category_id as item_category_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, b.item_prod_id as prod_id, b.uom, b.quantity, b.amount, c.id as wo_dtls_id, c.requisition_dtls_id 
			from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c, gbl_temp_report_id d 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.requisition_dtls_id=d.ref_val and d.ref_from=1 and c.item_category_id not in (1,2,3,12,13,14,24,25,28,30) $pi_category_cond ";
            //echo $sql_pi;
            $sql_pi_result=sql_select($sql_pi);
            $pi_data_arr=$pi_id_arr=array();$refrID2=1;
            foreach($sql_pi_result as $row)
            {
                $pi_id_arr[$row[csf("pi_id")]]=$row[csf("pi_id")];

                if($row[csf("pi_id")])
                {
                    $refrID2=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",2,".$user_id.")");
                    if(!$refrID2)
                    {
                        echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",2,".$user_id.")";oci_rollback($con);disconnect($con);die;
                    }
                    $temp_table_id++;
                }

                $pi_data_arr[$row[csf("requisition_dtls_id")]]["requisition_dtls_id"]=$row[csf("requisition_dtls_id")];
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["pi_id"].=$row[csf("pi_id")].",";
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["pi_number"].=$row[csf("pi_number")].",";
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["pi_date"]=$row[csf("pi_date")];
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["supplier_id"]=$row[csf("supplier_id")];
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["item_category_id"]=$row[csf("item_category_id")];
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["currency_id"]=$row[csf("currency_id")];
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["intendor_name"]=$row[csf("intendor_name")];
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["prod_id"].=$row[csf("prod_id")].",";
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["uom"]=$row[csf("uom")];
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["quantity"]+=$row[csf("quantity")];
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["amount"]+=$row[csf("amount")];
                $pi_data_arr[$row[csf("requisition_dtls_id")]]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
            }

            unset($sql_pi_result);
            if($refrID2)
            {
                oci_commit($con);
            }
        }

        //echo "sstt".count($pi_id_arr);die;

        if(!empty($pi_id_arr))
        {
            $sql_btb=sql_select("select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id 
			from com_btb_lc_master_details a, com_btb_lc_pi b, gbl_temp_report_id c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.ref_val and c.ref_from=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
            $btb_data_array=array();
            foreach($sql_btb as $row)
            {
                $btb_data_array[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
                $btb_data_array[$row[csf("pi_id")]]["lc_id"]=$row[csf("lc_id")];
                $btb_data_array[$row[csf("pi_id")]]["lc_number"]=$row[csf("lc_number")];
                $btb_data_array[$row[csf("pi_id")]]["lc_date"]=$row[csf("lc_date")];
                $btb_data_array[$row[csf("pi_id")]]["payterm_id"]=$row[csf("payterm_id")];
                $btb_data_array[$row[csf("pi_id")]]["tenor"]=$row[csf("tenor")];
                $btb_data_array[$row[csf("pi_id")]]["lc_value"]=$row[csf("lc_value")];
                $btb_data_array[$row[csf("pi_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
                $btb_data_array[$row[csf("pi_id")]]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
            }
            unset($sql_btb);

            $sql_invoice_pay=sql_select(" select b.pi_id, a.invoice_no, a.invoice_date, a.inco_term, a.inco_term_place, a.bill_no,  a.bill_date, a.mother_vessel, a.feeder_vessel, a.container_no, a.pkg_quantity, a.doc_to_cnf, a.document_status, a.copy_doc_receive_date, a.original_doc_receive_date, a.edf_paid_date, a.maturity_date, a.retire_source, c.accepted_ammount, c.payment_date 
			from com_import_invoice_mst a, com_import_invoice_dtls b, com_import_payment c, gbl_temp_report_id d 
			where a.id = b.import_invoice_id and a.id = c.invoice_id and b.pi_id=d.ref_val and d.ref_from=2 and a.status_active = 1 and b.status_active=1 and c.status_active=1");

            $inv_pay_data_array=array();
            $temp_inv_array=$temp_accept_id=array();
            foreach($sql_invoice_pay as $row)
            {
                $pi_id =$row[csf("pi_id")];
                //foreach($all_pi_id as $pi_id)
                //{
                $inv_pay_data_array[$pi_id]["pi_id"]=$pi_id;
                $inv_pay_data_array[$pi_id]["invoice_id"].=$row[csf("invoice_id")].",";
                $inv_pay_data_array[$pi_id]["invoice_no"].=$row[csf("invoice_no")].",";
                $inv_pay_data_array[$pi_id]["document_value"]+=$row[csf("document_value")];
                $inv_pay_data_array[$pi_id]["invoice_date"]=$row[csf("invoice_date")];
                $inv_pay_data_array[$pi_id]["inco_term"]=$row[csf("inco_term")];
                $inv_pay_data_array[$pi_id]["inco_term_place"]=$row[csf("inco_term_place")];
                $inv_pay_data_array[$pi_id]["bill_no"]=$row[csf("bill_no")];
                $inv_pay_data_array[$pi_id]["bill_date"]=$row[csf("bill_date")];
                $inv_pay_data_array[$pi_id]["mother_vessel"]=$row[csf("mother_vessel")];
                $inv_pay_data_array[$pi_id]["feeder_vessel"]=$row[csf("feeder_vessel")];
                $inv_pay_data_array[$pi_id]["container_no"]=$row[csf("container_no")];
                $inv_pay_data_array[$pi_id]["doc_to_cnf"]=$row[csf("doc_to_cnf")];
                $inv_pay_data_array[$pi_id]["bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
                $inv_pay_data_array[$pi_id]["maturity_date"]=$row[csf("maturity_date")];
                $inv_pay_data_array[$pi_id]["payment_date"]=$row[csf("payment_date")];

                //if($temp_inv_array[$row[csf("invoice_id")]]=="")
                //{
                //	$temp_inv_array[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
                $inv_pay_data_array[$pi_id]["pkg_quantity"]+=$row[csf("pkg_quantity")];

                //}
                //if($temp_accept_id[$row[csf("accept_id")]]=="")
                //{
                //	$temp_accept_id[$row[csf("accept_id")]]=$row[csf("accept_id")];
                $inv_pay_data_array[$pi_id]["accepted_ammount"]+=$row[csf("accepted_ammount")];

                //}
                //}

            }

            unset($sql_invoice_pay);


        }

        if($cbo_item_category_id) $rcv_category_cond = " and b.item_category  in($cbo_item_category_id)"; else $rcv_category_cond= "";

        $sql_receive=sql_select("select a.receive_basis, a.booking_id, b.prod_id, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt 
		from inv_receive_master a, inv_transaction b 
		where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) $rcv_category_cond and b.item_category not in (1,2,3,12,13,14,24,25,28,30) 
		group by a.receive_basis, a.booking_id, b.prod_id");
        $recv_data_array=array();
        foreach($sql_receive as $row)
        {
            $recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("prod_id")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
            $recv_data_array[$row[csf("receive_basis")]][$row[csf("booking_id")]][$row[csf("prod_id")]]["rcv_amt"]=$row[csf("rcv_amt")];
        }

        if($cbo_item_category_id) $wo_qnty_arr_category_cond = " and  b.item_category_id in ($cbo_item_category_id)" ; else $wo_qnty_arr_category_cond= "";
        $wo_qty_arr=sql_select("select b.requisition_dtls_id, b.item_id as prod_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$cbo_company_name $wo_qnty_arr_category_cond and  b.item_category_id not in (1,2,3,12,13,14,24,25,28,30)  and a.wo_basis_id=1 and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.requisition_dtls_id>0 
		group by b.requisition_dtls_id,b.item_id");
        $wo_pipe_array=array();
        foreach($wo_qty_arr as $row)
        {
            $wo_pipe_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]] += $row[csf("qty")];
        }

        if ($cbo_item_category_id) $pi_qnty_arr_category_cond = "  and c.item_category_id in($cbo_item_category_id)"; else $pi_qnty_arr_category_cond = "";
        $pi_qty_arr=sql_select("select c.requisition_dtls_id, b.item_prod_id as prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c, wo_non_order_info_mst d  where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.mst_id=d.id  and a.importer_id=$cbo_company_name $pi_qnty_arr_category_cond and c.item_category_id not in (1,2,3,12,13,14,24,25,28,30) and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.requisition_dtls_id,b.item_prod_id");
        $pi_pipe_array=array();
        foreach($pi_qty_arr as $row)
        {
            $pi_pipe_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]=$row[csf("qty")];
        }

        $gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
        if($gblDel)
        {
            oci_commit($con);disconnect($con);
        }

        $item_group_array = return_library_array("select id,item_name from  lib_item_group ","id","item_name");
        $store_array = return_library_array("select id,store_name from  lib_store_location ","id","store_name");
        $suplier_array = return_library_array("select id,supplier_name from  lib_supplier ","id","supplier_name");
        $indentor_name_array = return_library_array("select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b where a.id = b.supplier_id and b.party_type = 40","id","supplier_name");
        ob_start();
        ?>
        <div style="width:5660px; margin-left:10px">
            <fieldset style="width:100%;">
                <table width="5500" cellpadding="0" cellspacing="0" id="caption">
                    <tr>
                        <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                    </tr>
                </table>
                <br />

                <?
                $i=1;$j=1;
                $btb_tem_lc_array=$inv_temp_array=array();
                foreach($req_data_array as $req_dtls_id=>$row)
                {
                $wo_no=$wo_supplier="";$wo_qnty=$wo_rate=$wo_amount=$wo_balance=0;
                $pi_no=$pi_date=$pi_suplier=$pi_indore_name=$pi_id_all=$rcv_qnty=$rcv_value=$wo_mst_id_all=$pipe_line=$short_value=$pipe_pi_qnty=$pipe_wo_qnty=""; $pi_rate=0;
                $lc_date=$lc_no=$lc_pay_term=$lc_tenor=$lc_amt=$lc_ship_date=$lc_expire_date="";
                $invoice_id=$invoice_no=$invoice_date=$inco_term=$inco_term_place=$bl_no=$bl_date=$mother_vasel=$feder_vasel=$continer_no=$pakag_qnty=$doc_send_cnf=$bill_entry_no=$maturity_date=$maturity_month=$pay_date=$pay_amt="";

                if ($i%2==0)
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";

                if (!in_array($row["item_category_id"], $checkCateArr)) {
                $checkCateArr[] = $row["item_category_id"];
                if($i>1)
                {
                    ?>
                    </tbody>
                    </table>
                    <?
                }

                $j=1;
                ?>
                <table width="5660" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <thead>
                    <tr>
                        <th colspan="63" style="text-align: left !important; color: black"><? echo  $item_category[$row["item_category_id"]]; ?> :</th>
                    </tr>
                    <tr>
                        <th colspan="13" >Requisiton Details</th>
                        <th colspan="12" >Work Order Details</th>
                        <th colspan="10" >PI Details</th>
                        <th colspan="7">L/C Details</th>
                        <th colspan="13">Invoice Details</th>
                        <th colspan="4">Payment Details</th>
                        <th colspan="4">Store details</th>
                    </tr>
                    <tr>
                        <!--1210 requisition details-->
                        <th width="30">SL</th>
                        <th width="50">Req. No</th>
                        <th width="70">Req. Date</th>
                        <th width="150">Store Name</th>
                        <th width="70">Delivery Date</th>
                        <th width="100">Item Category</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Sub. Group</th>
                        <th width="80">Item Code</th>
                        <th width="150">Item Description</th>
                        <th width="100">Required For</th>
                        <th width="70"> UOM</th>
                        <th width="100">Req. Quantity </th>

                        <!--1110 wo details-->
                        <th width="50">WO No</th>
                        <th width="100">Item Category</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Sub. Group</th>
                        <th width="80">Item Code</th>
                        <th width="150">Item Description</th>
                        <th width="80">WO Qnty</th>
                        <th width="80">Wo Rate</th>
                        <th width="80">WO Amount</th>
                        <th width="70">WO Date</th>
                        <th width="80">WO Balance</th>
                        <th width="150">Supplier</th>

                        <!--840 pi details-->
                        <th width="130">PI No</th>
                        <th width="70">PI Date</th>
                        <th width="150">Supplier</th>
                        <th width="100">Item Category</th>
                        <th width="70">UOM</th>
                        <th width="80">PI Quantity</th>
                        <th width="80">Unit Price</th>
                        <th width="80">PI Value</th>
                        <th width="70">Currency</th>
                        <th width="100">Indentor Name</th>

                        <!--550 lc details-->
                        <th width="70">LC Date</th>
                        <th width="120">LC No</th>
                        <th width="80">Pay Term</th>
                        <th width="50">Tenor</th>
                        <th width="80">LC Amount</th>
                        <th width="70">Shipment Date</th>
                        <th width="80">Expiry Date</th>

                        <!--1100 Invoice details-->
                        <th width="150">Invoice No</th>
                        <th width="70">Invoice Date</th>
                        <th width="80">Incoterm</th>
                        <th width="100">Incoterm Place</th>
                        <th width="80">B/L No</th>
                        <th width="70">BL Date</th>
                        <th width="100">Mother Vassel</th>
                        <th width="100">Feedar Vassel</th>
                        <th width="100">Continer No</th>
                        <th width="80">Pkg Qty</th>
                        <th width="100">Doc Send to CNF</th>
                        <th width="70">NN Doc Received Date</th>
                        <th width="80">Bill Of Entry No</th>

                        <!--290 Payment details-->
                        <th width="70">Maturity Date</th>
                        <th width="70">Maturity Month</th>
                        <th width="70">Payment Date</th>
                        <th width="80">Paid Amount</th>

                        <!--340 MRR details-->
                        <th width="80">MRR Qnty</th>
                        <th width="80">MRR Value</th>
                        <th width="80">Short Value</th>
                        <th >Pipeline</th>
                    </tr>
                    </thead>
                </table>
                <!-- <div style="width:5660px; max-height:300px; overflow-y:scroll" id="scroll_body"> -->
                <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body">
                    <tbody>
                    <?

                    }
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30" align="center"><p><? echo $j; ?></p></td>
                        <td width="50" align="center"><p><? echo $row["requ_prefix_num"]; ?></p></td>
                        <td width="70" align="center"><p><? if($row["requisition_date"]!="" && $row["requisition_date"]!="0000-00-00") echo change_date_format($row["requisition_date"]); ?></p></td>
                        <td width="150"><p><? echo $store_array[$row["store_name"]]; ?></p></td>
                        <td width="70" align="center"><p><? if($row["delivery_date"]!="" && $row["delivery_date"]!="0000-00-00") echo change_date_format($row["delivery_date"]); ?></p></td>
                        <td width="100"><p><? echo $item_category[$row["item_category_id"]]; ?></p></td>
                        <td width="100"><p><? echo $item_group_array[$prod_data_array[$row["prod_id"]]["item_group_id"]]; ?></p></td>
                        <td width="100"><p><? echo $prod_data_array[$row["prod_id"]]["sub_group_name"]; ?></p></td>
                        <td width="80"><p><? echo $prod_data_array[$row["prod_id"]]["item_code"]; ?></p></td>
                        <td width="150"><p><? echo $prod_data_array[$row["prod_id"]]["item_description"]; ?></p></td>
                        <td width="100"><p><? echo $row["required_for"]; ?></p></td>
                        <td width="70"><p><? echo $unit_of_measurement[$prod_data_array[$row["prod_id"]]["unit_of_measure"]]; ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row["quantity"],2);$total_req_qnty+=$row["quantity"]; ?></p></td>

                        <?
                        if(chop($wo_data_array[$req_dtls_id]["wo_mst_id"]," , ")!="")
                        {
                            ?>
                            <td width="50" align="center"><p>
                                    <?
                                    $wo_no=implode(",",array_unique(explode(",",chop($wo_data_array[$req_dtls_id]["wo_number_prefix_num"]," , "))));
                                    echo $wo_no;
                                    ?>
                                </p></td>
                            <td width="100"><p><? echo $item_category[$row["item_category_id"]]; ?></p></td>
                            <td width="100"><p><? echo $item_group_array[$prod_data_array[$row["prod_id"]]["item_group_id"]]; ?></p></td>
                            <td width="100"><p><? echo $prod_data_array[$row["prod_id"]]["sub_group_name"]; ?></p></td>
                            <td width="80"><p><? echo $prod_data_array[$row["prod_id"]]["item_code"]; ?></p></td>
                            <td width="150"><p><? echo $prod_data_array[$row["prod_id"]]["item_description"]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($wo_data_array[$req_dtls_id]["supplier_order_quantity"],2); $total_wo_qnty+=$wo_data_array[$req_dtls_id]["supplier_order_quantity"]; ?></p></td>
                            <td width="80" align="right"><p><? $wo_rate=$wo_data_array[$req_dtls_id]["amount"]/$wo_data_array[$req_dtls_id]["supplier_order_quantity"]; echo number_format($wo_rate,2);  ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($wo_data_array[$req_dtls_id]["amount"],2); $total_wo_amt+=$wo_data_array[$req_dtls_id]["amount"]; ?></p></td>
                            <td width="70" title="last wo date" align="center"><p><? if($wo_data_array[$req_dtls_id]["wo_date"]!="" && $wo_data_array[$req_dtls_id]["wo_date"]!="0000-00-00") echo change_date_format($wo_data_array[$req_dtls_id]["wo_date"]); ?></p></td>
                            <td width="80" align="right" title="Requisition Quantity-Wo Quantity"><p>
                                    <?
                                    $wo_balance=$row["quantity"]-$wo_data_array[$req_dtls_id]["supplier_order_quantity"];
                                    echo number_format($wo_balance,2); $total_wo_balance+=$wo_balance;
                                    ?>
                                </p></td>
                            <td width="150"><p><? $wo_supplier=$suplier_array[$wo_data_array[$req_dtls_id]["supplier_id"]]; echo $wo_supplier; ?> </p></td>
                            <?
                        }
                        else
                        {
                            ?>
                            <td width="50" align="center"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="80"></td>
                            <td width="150"></td>
                            <td width="80" align="right"></td>
                            <td width="80" align="right"></td>
                            <td width="80" align="right"></td>
                            <td width="70"></td>
                            <td width="80"></td>
                            <td width="150"></td>
                            <?
                        }

                        if(chop($pi_data_arr[$req_dtls_id]["pi_id"]," , ") !="")
                        {
                            ?>
                            <td width="130" align="center"><p>
                                    <?
                                    $pi_no=implode(",",array_unique(explode(",",chop($pi_data_arr[$req_dtls_id]["pi_number"]," , "))));
                                    echo $pi_no;
                                    ?>
                                </p></td>
                            <td width="70" align="center" title="Last PI Date"><p><? if($pi_data_arr[$req_dtls_id]["pi_date"]!="" && $pi_data_arr[$req_dtls_id]["pi_date"]!="0000-00-00") echo change_date_format($pi_data_arr[$req_dtls_id]["pi_date"]); ?></p></td>
                            <td width="150"><p><? $pi_suplier=$suplier_array[$pi_data_arr[$req_dtls_id]["supplier_id"]]; echo $pi_suplier; ?> </p></td>
                            <td width="100"><p><? echo $item_category[$row["item_category_id"]]; ?></p></td>
                            <td width="70" align="center"><P><? echo $unit_of_measurement[$pi_data_arr[$req_dtls_id]["uom"]]; ?></P></td>
                            <td width="80" align="right"><p><? echo number_format($pi_data_arr[$req_dtls_id]["quantity"],2); $total_pi_qnty+=$pi_data_arr[$req_dtls_id]["quantity"]; ?></p></td>
                            <td width="80" align="right"><P><? $pi_rate=$pi_data_arr[$req_dtls_id]["amount"]/$pi_data_arr[$req_dtls_id]["quantity"]; echo number_format($pi_rate,2); ?></P></td>
                            <td width="80" align="right"><p><? echo number_format($pi_data_arr[$req_dtls_id]["amount"],2); $total_pi_amt+=$pi_data_arr[$req_dtls_id]["amount"]; ?></p></td>
                            <td width="70"><P><? echo $currency[$pi_data_arr[$req_dtls_id]["currency_id"]]; ?></P></td>
                            <td width="100"><p><? $pi_indore_name=$indentor_name_array[$pi_data_arr[$req_dtls_id]["intendor_name"]]; echo $pi_indore_name; ?></p></td>
                            <?
                            $pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$req_dtls_id]["pi_id"]," , ")));

                            foreach($pi_id_arr as $piID)
                            {
                                if(!in_array($btb_data_array[$piID]["lc_id"],$btb_tem_lc_array))
                                {
                                    $btb_tem_lc_array[$btb_data_array[$piID]["lc_id"]]=$btb_data_array[$piID]["lc_id"];
                                    $lc_date=$btb_data_array[$piID]["lc_date"];
                                    $lc_no.=$btb_data_array[$piID]["lc_number"].",";
                                    $lc_pay_term=$pay_term[$btb_data_array[$piID]["payterm_id"]];
                                    $lc_tenor+=$btb_data_array[$piID]["tenor"];
                                    $lc_amt+=$btb_data_array[$piID]["lc_value"];
                                    $lc_ship_date=$btb_data_array[$piID]["last_shipment_date"];
                                    $lc_expire_date=$btb_data_array[$piID]["lc_expiry_date"];
                                }

                                if(!in_array($inv_pay_data_array[$piID]["invoice_id"],$inv_temp_array))
                                {
                                    $inv_temp_array[$inv_pay_data_array[$piID]["invoice_id"]]=$inv_pay_data_array[$piID]["invoice_id"];
                                    $invoice_id.=$inv_pay_data_array[$piID]["invoice_id"].",";
                                    $invoice_no.=$inv_pay_data_array[$piID]["invoice_no"].",";
                                    $invoice_date=$inv_pay_data_array[$piID]["invoice_date"];
                                    $inco_term=$inv_pay_data_array[$piID]["inco_term"];
                                    $inco_term_place=$inv_pay_data_array[$piID]["inco_term_place"];
                                    $bl_no=$inv_pay_data_array[$piID]["bill_no"];
                                    $bl_date=$inv_pay_data_array[$piID]["bill_date"];
                                    $mother_vasel=$inv_pay_data_array[$piID]["mother_vessel"];
                                    $feder_vasel=$inv_pay_data_array[$piID]["feeder_vessel"];
                                    $continer_no=$inv_pay_data_array[$piID]["container_no"];
                                    $pakag_qnty=$inv_pay_data_array[$piID]["pkg_quantity"];
                                    $doc_send_cnf=$inv_pay_data_array[$piID]["doc_to_cnf"];
                                    $bill_entry_no=$inv_pay_data_array[$piID]["bill_of_entry_no"];
                                    $maturity_date=$inv_pay_data_array[$piID]["maturity_date"];
                                    $maturity_month=$inv_pay_data_array[$piID]["maturity_date"];
                                    $pay_date=$inv_pay_data_array[$piID]["payment_date"];
                                    $pay_amt+=$inv_pay_data_array[$piID]["accepted_ammount"];
                                }
                            }
                            ?>
                            <td width="70" align="center"><P><? if($lc_date!="" && $lc_date!="0000-00-00") echo change_date_format($lc_date); ?></P></td>
                            <td width="120"><P><? echo $lc_no=implode(",",array_unique(explode(",",chop($lc_no," , ")))); ?></P></td>
                            <td width="80"><P><? echo $lc_pay_term; ?></P></td>
                            <td width="50" align="center"><P><? echo $lc_tenor; ?></P></td>
                            <td width="80" align="right"><P><? echo number_format($lc_amt,2); $total_lc_amt+=$lc_amt; ?></P></td>
                            <td width="70" align="center" title="Last Ship Date"><P><? if($lc_ship_date!="" && $lc_ship_date!="0000-00-00") echo change_date_format($lc_ship_date); ?></P></td>
                            <td width="80"  align="center" title="Last Expire Date"><P><? if($lc_expire_date!="" && $lc_expire_date!="0000-00-00") echo change_date_format($lc_expire_date); ?></P></td>

                            <td width="150"><P><? echo $invoice_no=implode(",",array_unique(explode(",",chop($invoice_no," , ")))); ?></P></td>
                            <td width="70" align="center" title="Last Invoice Date"><P><? if($invoice_date!="" && $invoice_date!="0000-00-00") echo change_date_format($invoice_date); ?></P></td>
                            <td width="80"><P><? echo $inco_term; ?></P></td>
                            <td width="100"><P><? echo $inco_term_place; ?></P></td>
                            <td width="80"><P><? echo $bl_no; ?></P></td>
                            <td width="70" align="center"><P><? if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?></P></td>
                            <td width="100"><P><? echo $mother_vasel; ?></P></td>
                            <td width="100"><P><? echo $feder_vasel; ?></P></td>
                            <td width="100"><P><? echo $continer_no; ?></P></td>
                            <td width="80" align="right"><P><? echo number_format($pakag_qnty,2); $total_pkg_qnty+=$pakag_qnty; ?></P></td>
                            <td width="100" align="center"><P><? if($doc_send_cnf!="" && $doc_send_cnf!="0000-00-00") echo change_date_format($doc_send_cnf); ?></P></td>
                            <td width="70"></td>
                            <td width="80"><P><? echo $bill_entry_no; ?></P></td>

                            <td width="70" align="center"><P><? if($maturity_date!="" && $maturity_date!="0000-00-00") echo change_date_format($maturity_date); ?></P></td>
                            <td width="70" align="center"><P><? if($maturity_month!="" && $maturity_month!="0000-00-00") echo change_date_format($maturity_month); ?></P></td>
                            <td width="70" align="center"><P><? if($pay_date!="" && $pay_date!="0000-00-00") echo change_date_format($pay_date); ?></P></td>
                            <td width="80" align="right"><P><? echo number_format($pay_amt,2); $total_pay_amt+=$pay_amt; ?></P></td>
                            <?
                        }
                        else
                        {
                            ?>
                            <td width="130"></td>
                            <td width="70"></td>
                            <td width="150"></td>
                            <td width="100"></td>
                            <td width="70"></td>
                            <td width="80"></td>
                            <td width="80"></td>
                            <td width="80"></td>
                            <td width="70"></td>
                            <td width="100"></td>

                            <td width="70"></td>
                            <td width="120"></td>
                            <td width="80"></td>
                            <td width="50"></td>
                            <td width="80"></td>
                            <td width="70"></td>
                            <td width="80"></td>

                            <td width="150"></td>
                            <td width="70"></td>
                            <td width="80"></td>
                            <td width="100"></td>
                            <td width="80"></td>
                            <td width="70"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="80"></td>
                            <td width="100"></td>
                            <td width="70"></td>
                            <td width="80"></td>

                            <td width="70"></td>
                            <td width="70"></td>
                            <td width="70"></td>
                            <td width="80"></td>
                            <?
                        }
                        //$rcv_qnty=$rcv_value
                        $pipe_pi_qnty=$pi_pipe_array[$req_dtls_id][$row[("prod_id")]];
                        $pi_id_all=array_unique(explode(",",chop($pi_data_arr[$req_dtls_id]["pi_id"]," , ")));
                        $wo_mst_id_all=array_unique(explode(",",chop($wo_data_array[$req_dtls_id]["wo_mst_id"]," , ")));
                        $recv_pi_wo_req="";
                        foreach($pi_id_all as $val)
                        {

                            $rcv_qnty+=$recv_data_array[1][$val][$row[("prod_id")]]["rcv_qnty"];
                            $rcv_value+=$recv_data_array[1][$val][$row[("prod_id")]]["rcv_amt"];
                            $recv_pi_wo_req.=$val.",";
                        }
                        $recv_pi_wo_req=chop($recv_pi_wo_req," , ");
                        if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**1";
                        if($rcv_qnty=="")
                        {
                            $recv_pi_wo_req="";
                            foreach($wo_mst_id_all as $val)
                            {
                                $rcv_qnty+=$recv_data_array[2][$val][$row[("prod_id")]]["rcv_qnty"];
                                $rcv_value+=$recv_data_array[2][$val][$row[("prod_id")]]["rcv_amt"];
                                $recv_pi_wo_req.=$val.",";
                            }
                            $recv_pi_wo_req=chop($recv_pi_wo_req," , ");
                            if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**2";

                        }
                        if($rcv_qnty=="")
                        {
                            $recv_pi_wo_req="";
                            $rcv_qnty=$recv_data_array[7][$req_data_array[$req_dtls_id]["req_id"]][$row[("prod_id")]]["rcv_qnty"];
                            $rcv_value=$recv_data_array[7][$req_data_array[$req_dtls_id]["req_id"]][$row[("prod_id")]]["rcv_amt"];
                            $recv_pi_wo_req=$req_data_array[$req_dtls_id]["req_id"];
                            if($recv_pi_wo_req!="") $recv_pi_wo_req=$recv_pi_wo_req."**7";
                        }
                        ?>
                        <td width="80" align="right"><p><a href="##" onClick="openmypage_popup('<? echo $recv_pi_wo_req; ?>','<? echo $row[("prod_id")]; ?>','Receive Info','receive_popup');" > <? echo number_format($rcv_qnty,2); $total_mrr_qnty+=$rcv_qnty; ?> </a></p></td>
                        <td width="80" align="right"><p><? echo number_format($rcv_value,2); $total_mrr_amt+=$rcv_value; ?></p></td>
                        <td align="right" title="Wo Value-Receive Value" width="80"><p><? $short_value=$wo_data_array[$req_dtls_id]["amount"]-$rcv_value; echo number_format($short_value,2);  $total_short_amt+=$short_value; ?></p></td>
                        <?
                        $pipe_wo_qnty+=$wo_pipe_array[$req_dtls_id][$row[("prod_id")]];
                        $pipe_line=(($pipe_wo_qnty+$pipe_pi_qnty)-$rcv_qnty);
                        ?>
                        <td  align="right"><P> <? echo number_format($pipe_line,2); $total_pipe_line+=$pipe_line;?></P></td>
                    </tr>
                    <?
                    $i++;
                    $pipe_wo_qnty=0;


                    /*if (!in_array($row["item_category_id"], $checkCateArr)) {
                            $checkCateArr[] = $row["item_category_id"];


                    ?>



                    </tbody>
                </table>
                <br>
                <?
                        }*/
                    $j++;

                    }
                    ?>
                    <!--<td  align="right"><P><a href="##" onClick="openmypage_popup('<?// echo $recv_pi_wo_req; ?>','<?// echo $row[("prod_id")]; ?>','Pipe Line Info','pipe_line_popup');" > <?// echo number_format($pipe_wo_qnty,2); $total_pipe_line+=$pipe_line;?></a></P></td>-->
                    <!-- </div> -->
                    <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_footer">
                        <tfoot>
                        <tr>
                            <!--1210 requisition details-->
                            <th width="30"></th>
                            <th width="50"></th>
                            <th width="70"></th>
                            <th width="150"></th>
                            <th width="70"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="80"></th>
                            <th width="150"></th>
                            <th width="100"></th>
                            <th width="70"> </th>
                            <th width="100" id="value_total_req_qnty" align="right"><? echo number_format($total_req_qnty,0); ?> </th>

                            <!--1110 wo details-->
                            <th width="50"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="80"></th>
                            <th width="150"></th>
                            <th width="80" id="value_total_wo_qnty" align="right"><? echo number_format($total_wo_qnty,0); ?></th>
                            <th width="80"></th>
                            <th width="80" id="value_total_wo_amt" align="right"><? echo number_format($total_wo_amt,2); ?></th>
                            <th width="70"></th>
                            <th width="80" id="value_total_wo_balance" align="right"><? echo number_format($total_wo_balanc,2); ?></th>
                            <th width="150"></th>

                            <!--840 pi details-->
                            <th width="130"></th>
                            <th width="70"></th>
                            <th width="150"></th>
                            <th width="100"></th>
                            <th width="70"></th>
                            <th width="80" id="value_total_pi_qnty" align="right"><? echo number_format($total_pi_qnty,0); ?></th>
                            <th width="80"></th>
                            <th width="80" id="value_total_pi_amt" align="right"><? echo number_format($total_pi_amt,2); ?></th>
                            <th width="70"></th>
                            <th width="100"></th>

                            <!--550 lc details-->
                            <th width="70"></th>
                            <th width="120"></th>
                            <th width="80"></th>
                            <th width="50"></th>
                            <th width="80" id="value_total_lc_amt" align="right"><? echo number_format($total_lc_amt,2); ?></th>
                            <th width="70"></th>
                            <th width="80"></th>

                            <!--1100 Invoice details-->
                            <th width="150"></th>
                            <th width="70"></th>
                            <th width="80"></th>
                            <th width="100"></th>
                            <th width="80"></th>
                            <th width="70"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="80" id="value_total_pkg_qnty" align="right"><? echo number_format($total_pkg_qnty,0); ?></th>
                            <th width="100"></th>
                            <th width="70"></th>
                            <th width="80"></th>

                            <!--290 Payment details-->
                            <th width="70"></th>
                            <th width="70"></th>
                            <th width="70"></th>
                            <th width="80" id="value_total_pay_amt" align="right"><? echo number_format($total_pay_amt,2); ?></th>

                            <!--340 MRR details-->
                            <th width="80" id="value_total_mrr_qnty" align="right"><? echo number_format($total_mrr_qnty,0); ?></th>
                            <th width="80" id="value_total_mrr_amt" align="right"><? echo number_format($total_mrr_amt,2); ?></th>
                            <th width="80" id="value_total_short_amt" align="right"><? echo number_format($total_short_amt,2); ?></th>
                            <th id="value_total_pipe_line" align="right"><? echo number_format($total_pipe_line,2); ?></th>
                        </tr>
                        </tfoot>
                    </table>
            </fieldset>
        </div>
        <?

    }
    else if($txt_req_no == "" && $txt_wo_po_no == "" && $txt_pi_no == "" && $cbo_date_type==2 && ($txt_date_from && $txt_date_to))
    {
        $sql_cond="";
        $requ_table="";
        if($cbo_company_name>0) $sql_cond.=" and a.company_name='$cbo_company_name' ";
        if($cbo_item_category_id>0) $sql_cond.=" and b.item_category_id='$cbo_item_category_id' ";
        if($cbo_supplier>0) $sql_cond.=" and a.supplier_id='$cbo_supplier' ";

        if($txt_req_no!="")
        {
            $sql_cond.=" and c.requ_prefix_num = '$txt_req_no' and c.id=d.mst_id and b.requisition_dtls_id=d.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
            $requ_table=", inv_purchase_requisition_mst c, inv_purchase_requisition_dtls d ";
        }

        if($txt_wo_po_no!="")
        {
            $sql_cond.=" and a.wo_number like '%$txt_wo_po_no%' ";
        }


        if($txt_date_from != "" && $txt_date_to != "" && $cbo_date_type == 2) $sql_cond.=" and a.wo_date between  '$txt_date_from' and '$txt_date_to'";


        //Finding WO/PO Data....
        $sql_wo=sql_select("select a.id as wo_mst_id, a.wo_number, a.wo_number_prefix_num, a.wo_date, a.supplier_id, b.id as wo_dtls_id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name, b.requisition_dtls_id, b.item_id as prod_id, b.item_category_id, b.supplier_order_quantity, b.rate, b.amount 
		from wo_non_order_info_mst a, wo_non_order_info_dtls b $requ_table 
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond and b.item_category_id not in (2,3,12,13,14,24,25,28,30) order by b.item_category_id, a.wo_number");
        /*echo "select a.id as wo_mst_id, a.wo_number, a.wo_number_prefix_num, a.wo_date, a.supplier_id, b.id as wo_dtls_id,
        b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name,
        b.requisition_dtls_id, b.item_id as prod_id, b.item_category_id, b.supplier_order_quantity, b.rate, b.amount from wo_non_order_info_mst a, wo_non_order_info_dtls b $requ_table where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond and b.item_category_id not in (2,3,12,13,14,24,25,28,30) order by b.item_category_id, a.wo_number";die;*/
        $wo_data_array=array();
        $req_dtls_id_arr=array();
        $wo_num_arr=array();
        $gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
        if($gblDel)
        {
            oci_commit($con);
            //disconnect($con);
        }
        $temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
        if($temp_table_id=="") $temp_table_id=1;
        $refrID1=$refrID2=1;
        foreach($sql_wo as $row)
        {
            if($row[csf("requisition_dtls_id")])
            {
                $refrID1=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("requisition_dtls_id")].",1,".$user_id.")");
                if(!$refrID1)
                {
                    echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("requisition_dtls_id")].",1,".$user_id.")";oci_rollback($con);disconnect($con);die;
                }
                $temp_table_id++;
            }

            $refrID2=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("wo_dtls_id")].",2,".$user_id.")");
            if(!$refrID2)
            {
                echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("wo_dtls_id")].",2,".$user_id.")";oci_rollback($con);disconnect($con);die;
            }
            $temp_table_id++;

            $req_dtls_id_arr[$row[csf("requisition_dtls_id")]]=$row[csf("requisition_dtls_id")];
            $wo_num_arr[$row[csf("wo_number")]]=$row[csf("wo_number")];
            $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_mst_id"].=$row[csf("wo_mst_id")].",";
            $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_number"].=$row[csf("wo_number")].",";
            $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_number_prefix_num"].=$row[csf("wo_number_prefix_num")].",";
            $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_date"]=$row[csf("wo_date")];
            $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
            $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]=$row[csf("supplier_id")];
            $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
            $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
            $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
            $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"]+=$row[csf("amount")];

            if ($cbo_item_category_id==1)
            {
                $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_description"]=$yarn_count_arr[$row[csf("yarn_count")]]." ".$composition[$row[csf("yarn_comp_type1st")]]." ".$row[csf("yarn_comp_percent1st")]." ".$yarn_type[$row[csf("yarn_type")]]." ".$color_name_arr[$row[csf("color_name")]];
            }
        }

        if(count($sql_wo) < 1){
            echo "<span style='font-size:23;font-weight:bold;text-align:center;width:100%'>Data Not Found</span>";die;
        }

        //unset($sql_wo);

        if($refrID1 && $refrID2)
        {
            oci_commit($con);
        }
        //Finding Requisition Data....
        if(!empty($req_dtls_id_arr))
        {
            /*$req_dtsl_ids=implode(",", array_filter(array_unique($req_dtls_id_arr)));

            $dtls_id = $req_dtls_id_cond = "";
            $req_dtls_id_arr=explode(",",$req_dtsl_ids);
            if($db_type==2 && count($req_dtls_id_arr)>999)
            {
                $req_dtls_id_chunk=array_chunk($req_dtls_id_arr,999) ;
                foreach($req_dtls_id_chunk as $chunk_arr)
                {
                    $dtls_id.=" b.id in(".implode(",",$chunk_arr).") or ";
                }

                $req_dtls_id_cond.=" and (".chop($dtls_id,'or ').")";

            }
            else
            {

                $req_dtls_id_cond=" and b.id in($req_dtsl_ids)";
            }*/
            if($cbo_item_category_id) $req_sql_category_cond = " and b.item_category=$cbo_item_category_id"; else $req_sql_category_cond="";
            $req_sql=sql_select("select a.id as req_id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date, b.item_category as item_category_id, b.cons_uom, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.color_id, b.id as req_dtsl_id, b.product_id as prod_id, b.required_for, b.quantity, b.rate, b.amount 
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, gbl_temp_report_id c 
			where a.id=b.mst_id and b.id=c.ref_val and c.ref_from=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and b.item_category not in (2,3,12,13,14,24,25,28,30)"); //and b.id in ($req_dtsl_ids)

            $req_data_array=array();
            foreach($req_sql as $row)
            {
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["req_id"]=$row[csf("req_id")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["requ_no"]=$row[csf("requ_no")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["cons_uom"]=$row[csf("cons_uom")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["requ_prefix_num"]=$row[csf("requ_prefix_num")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["requisition_date"]=$row[csf("requisition_date")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["store_name"]=$row[csf("store_name")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["pay_mode"]=$row[csf("pay_mode")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["cbo_currency"]=$row[csf("cbo_currency")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["delivery_date"]=$row[csf("delivery_date")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["required_for"]=$row[csf("required_for")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["quantity"]=$row[csf("quantity")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["rate"]=$row[csf("rate")];
                $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["amount"]=$row[csf("amount")];

                if ($cbo_item_category_id==1)
                {
                    $req_data_array[$row[csf("req_dtsl_id")]][$row[csf("prod_id")]]["item_description"]=$yarn_count_arr[$row[csf("count_id")]]." ".$composition[$row[csf("composition_id")]]." ".$row[csf("com_percent")]." ".$yarn_type[$row[csf("yarn_type_id")]]." ".$color_name_arr[$row[csf("color_id")]];
                }
            }
        }

        unset($req_sql);
        //Finding PI data....
        $wo_num_ids="";
        if(!empty($wo_num_arr))
        {
            if($cbo_item_category_id) $sql_pi_category_cond = "and c.item_category_id=$cbo_item_category_id and b.item_category_id=$cbo_item_category_id"; else $sql_pi_category_cond = "";
            $sql_pi="SELECT a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, a.item_category_id as item_category_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, case when b.item_prod_id !='' or b.item_prod_id is not null then b.item_prod_id else 0 end as prod_id, b.work_order_no, b.work_order_dtls_id, b.uom, b.quantity, b.amount, c.id as wo_dtls_id, c.requisition_dtls_id 
			from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c, gbl_temp_report_id d 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.id=d.ref_val and d.ref_from=2 and a.importer_id=$cbo_company_name $sql_pi_category_cond";
        }
        /*foreach (array_unique($wo_num_arr) as $values)
        {
            $wo_num_ids.="'".$values."',";
        }
        $wo_num_ids=chop($wo_num_ids,",");*/


        //echo $sql_pi;die;

        $sql_pi_result=sql_select($sql_pi);
        $pi_data_arr=$pi_id_arr=array();
        $refrID3=1;
        foreach($sql_pi_result as $row)
        {
            $refrID3=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",3,".$user_id.")");
            if(!$refrID3)
            {
                echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",3,".$user_id.")";oci_rollback($con);disconnect($con);die;
            }
            $temp_table_id++;

            $pi_id_arr[$row[csf("pi_id")]]=$row[csf("pi_id")];
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["requisition_dtls_id"]=$row[csf("requisition_dtls_id")];
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_id"].=$row[csf("pi_id")].",";
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_number"].=$row[csf("pi_number")].",";
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_date"]=$row[csf("pi_date")];
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]=$row[csf("supplier_id")];
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["currency_id"]=$row[csf("currency_id")];
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["intendor_name"]=$row[csf("intendor_name")];
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["prod_id"].=$row[csf("prod_id")].",";
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["uom"]=$row[csf("uom")];
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["quantity"]+=$row[csf("quantity")];
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["amount"]+=$row[csf("amount")];
            $pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
        }

        if($refrID3)
        {
            oci_commit($con);
        }

        unset($sql_pi_result);

        //finding BTB Lc data....
        if(!empty($pi_id_arr))
        {
            $sql_btb=sql_select("select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id 
			from com_btb_lc_master_details a, com_btb_lc_pi b, gbl_temp_report_id c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.ref_val and c.ref_from=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
            $btb_data_array=array();
            foreach($sql_btb as $row)
            {
                $btb_data_array[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
                $btb_data_array[$row[csf("pi_id")]]["lc_id"]=$row[csf("lc_id")];
                $btb_data_array[$row[csf("pi_id")]]["lc_number"]=$row[csf("lc_number")];
                $btb_data_array[$row[csf("pi_id")]]["lc_date"]=$row[csf("lc_date")];
                $btb_data_array[$row[csf("pi_id")]]["payterm_id"]=$row[csf("payterm_id")];
                $btb_data_array[$row[csf("pi_id")]]["tenor"]=$row[csf("tenor")];
                $btb_data_array[$row[csf("pi_id")]]["lc_value"]=$row[csf("lc_value")];
                $btb_data_array[$row[csf("pi_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
                $btb_data_array[$row[csf("pi_id")]]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
            }
            unset($sql_btb);
            //Finding Invoice data....
            if($db_type==0)
            {
                $pi_cond="group_concat(a.pi_id) as pi_id";
            }
            else if($db_type==2)
            {
                $pi_cond="LISTAGG(CAST(a.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.pi_id) as pi_id";
            }
            $sql_invoice_pay=sql_select("select $pi_cond, b.id as invoice_id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, e.payterm_id, b.id as accept_id 
			from gbl_temp_report_id c, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_btb_lc_master_details e 
			where c.ref_val=a.pi_id and c.ref_from=3 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and e.id = a.com_btb_lc_master_details_id and b.status_active=1 and b.is_deleted=0 
			group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, e.payterm_id");

            $inv_pay_data_array=array();
            $temp_inv_array=$temp_accept_id=array();
            foreach($sql_invoice_pay as $row)
            {
                $all_pi_id=array_unique(explode(",",$row[csf("pi_id")]));
                foreach($all_pi_id as $pi_id)
                {
                    $inv_pay_data_array[$pi_id]["pi_id"]=$pi_id;
                    $inv_pay_data_array[$pi_id]["invoice_id"].=$row[csf("invoice_id")].",";
                    $inv_pay_data_array[$pi_id]["invoice_no"].=$row[csf("invoice_no")].",";
                    $inv_pay_data_array[$pi_id]["document_value"]+=$row[csf("document_value")];
                    $inv_pay_data_array[$pi_id]["invoice_date"]=$row[csf("invoice_date")];
                    $inv_pay_data_array[$pi_id]["inco_term"]=$row[csf("inco_term")];
                    $inv_pay_data_array[$pi_id]["inco_term_place"]=$row[csf("inco_term_place")];
                    $inv_pay_data_array[$pi_id]["bill_no"]=$row[csf("bill_no")];
                    $inv_pay_data_array[$pi_id]["bill_date"]=$row[csf("bill_date")];
                    $inv_pay_data_array[$pi_id]["mother_vessel"]=$row[csf("mother_vessel")];
                    $inv_pay_data_array[$pi_id]["feeder_vessel"]=$row[csf("feeder_vessel")];
                    $inv_pay_data_array[$pi_id]["container_no"]=$row[csf("container_no")];
                    $inv_pay_data_array[$pi_id]["doc_to_cnf"]=$row[csf("doc_to_cnf")];
                    $inv_pay_data_array[$pi_id]["bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
                    $inv_pay_data_array[$pi_id]["maturity_date"]=$row[csf("maturity_date")];

                    if($temp_inv_array[$row[csf("invoice_id")]]=="")
                    {
                        $temp_inv_array[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
                        $inv_pay_data_array[$pi_id]["pkg_quantity"]+=$row[csf("pkg_quantity")];

                    }

                    /*if($row[csf("payterm_id")]==1) //Pay Term = At sight
                    {
                        $inv_pay_data_array[$pi_id]["payment_date"]=$row[csf("invoice_date")];

                        $cumulative_array=return_library_array("select pi_id, sum(current_acceptance_value) as accepted_ammount from com_import_invoice_dtls where pi_id=$pi_id and status_active=1 and is_deleted=0 group by pi_id",'pi_id','accepted_ammount');
                        $inv_pay_data_array[$pi_id]["accepted_ammount"]=$cumulative_array[$pi_id];
                    }*/
                }

            }

            unset($sql_invoice_pay);

            //Finding invoice Payment data
            if ($db_type==0)
            {
                $sql_invoice_pay2= sql_select("select group_concat(a.pi_id) as pi_id, c.payment_date, c.id as accept_id, c.accepted_ammount 
				from gbl_temp_report_id m, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_import_payment c 
				where m.ref_val=a.pi_id and m.ref_from=3 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and b.id=c.invoice_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
				group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, c.payment_date, c.id, c.accepted_ammount");
            }
            else
            {
                $sql_invoice_pay2= sql_select("select LISTAGG(CAST(a.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.pi_id) as pi_id, c.payment_date, c.id as accept_id, c.accepted_ammount 
				from gbl_temp_report_id m, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_import_payment c 
				where m.ref_val=a.pi_id and m.ref_from=3 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and b.id=c.invoice_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
				group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, c.payment_date, c.id, c.accepted_ammount");
            }

            //echo "select LISTAGG(CAST(a.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.pi_id) as pi_id, c.payment_date, c.id as accept_id, c.accepted_ammount from com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_import_payment c where a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and b.id=c.invoice_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.pi_id in (".trim(implode(",",$pi_id_arr),",").") group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, c.payment_date, c.id, c.accepted_ammount";
            foreach($sql_invoice_pay2 as $row)
            {
                $all_pi_id=array_unique(explode(",",$row[csf("pi_id")]));
                foreach($all_pi_id as $pi_id)
                {
                    $inv_pay_data_array[$pi_id]["payment_date"]=$row[csf("payment_date")];
                }
                if($temp_accept_id[$row[csf("accept_id")]]=="")
                {
                    $temp_accept_id[$row[csf("accept_id")]]=$row[csf("accept_id")];
                    $inv_pay_data_array[$pi_id]["accepted_ammount"]+=$row[csf("accepted_ammount")];
                }
            }

            unset($sql_invoice_pay2);
            //----------------------------------End-------------------------------------------------------
        }

        if($cbo_item_category_id) $sql_receive_category_cond = " and b.item_category in($cbo_item_category_id)"; else $sql_receive_category_cond="";
        if ($cbo_item_category_id==1) // For Yarn
        {
            /*echo "select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, c.product_name_details, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt from inv_receive_master a, inv_transaction b, product_details_master c where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) and b.prod_id=c.id and c.status_active=1 and c.is_deleted=0 group by a.receive_basis, b.prod_id,pi_wo_batch_no, c.product_name_details";die;*/
            $sql_receive=sql_select("select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, c.product_name_details, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt from inv_receive_master a, inv_transaction b, product_details_master c where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) and b.prod_id=c.id and c.status_active=1 and c.is_deleted=0 
			group by a.receive_basis, b.prod_id,pi_wo_batch_no, c.product_name_details");
            $recv_data_array=array();
            foreach($sql_receive as $row)
            {
                $recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["receive_basis"]=$row[csf("receive_basis")];
                $recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["prod_id"]=$row[csf("prod_id")];
                $recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
                $recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["rcv_amt"]=$row[csf("rcv_amt")];
            }
        }
        else
        {

            $sql_receive=sql_select("select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt from inv_receive_master a, inv_transaction b where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) group by a.receive_basis, b.prod_id,pi_wo_batch_no");
            $recv_data_array=array();
            foreach($sql_receive as $row)
            {
                $recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["receive_basis"]=$row[csf("receive_basis")];
                $recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
                $recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["rcv_amt"]=$row[csf("rcv_amt")];
            }
        }
        unset($sql_receive);
        //echo "<pre>";print_r($recv_data_array);die;
        $gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
        if($gblDel)
        {
            oci_commit($con);disconnect($con);
        }
        //echo "select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt from inv_receive_master a, inv_transaction b where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) group by a.receive_basis, b.prod_id,pi_wo_batch_no"; die;

        $item_group_array = return_library_array("select id,item_name from  lib_item_group ","id","item_name");
        $store_array = return_library_array("select id,store_name from  lib_store_location ","id","store_name");
        $suplier_array = return_library_array("select id,supplier_name from  lib_supplier ","id","supplier_name");
        $indentor_name_array = return_library_array("select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b where a.id = b.supplier_id and b.party_type = 40","id","supplier_name");
        ob_start();
        ?>
        <div style="width:5660px; margin-left:10px">
            <fieldset style="width:100%;">
                <table width="5500" cellpadding="0" cellspacing="0" id="caption">
                    <tr>
                        <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                    </tr>
                </table>
                <br />
                <?
                $i=1;
                $btb_tem_lc_array=$inv_temp_array=array();
                //foreach($req_data_array as $req_dtls_id=>$row)
                foreach ($sql_wo as  $row)
                {
                $wo_no=$wo_supplier="";$wo_qnty=$wo_rate=$wo_amount=$wo_balance=0;
                $pi_no=$pi_date=$pi_suplier=$pi_indore_name=$pi_id_all=$rcv_qnty=$rcv_value=$wo_mst_id_all=$pipe_line=$short_value=$pipe_pi_qnty=$pipe_wo_qnty=""; $pi_rate=0;
                $lc_date=$lc_no=$lc_pay_term=$lc_tenor=$lc_amt=$lc_ship_date=$lc_expire_date="";
                $invoice_id=$invoice_no=$invoice_date=$inco_term=$inco_term_place=$bl_no=$bl_date=$mother_vasel=$feder_vasel=$continer_no=$pakag_qnty=$doc_send_cnf=$bill_entry_no=$maturity_date=$maturity_month=$pay_date=$pay_amt="";

                if ($i%2==0)
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
                if (!in_array($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"], $checkCateArr))
                {
                $checkCateArr[] = $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"];
                if($i>1)
                {
                    ?>
                    </tbody>
                    </table>
                    <?
                }

                $j=1;
                ?>

                <table width="5660" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
                    <thead>
                    <tr>
                        <th colspan="63" style="text-align: left !important; color: black"><? echo  $item_category[$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?> :</th>
                    </tr>
                    <tr>
                        <th colspan="13" >Requisiton Details</th>
                        <th colspan="12" >Work Order Details</th>
                        <th colspan="10" >PI Details</th>
                        <th colspan="7">L/C Details</th>
                        <th colspan="13">Invoice Details</th>
                        <th colspan="4">Payment Details</th>
                        <th colspan="4">Store details</th>
                    </tr>
                    <tr>
                        <!--1210 requisition details-->
                        <th width="30">SL</th>
                        <th width="50">Req. No</th>
                        <th width="70">Req. Date</th>
                        <th width="150">Store Name</th>
                        <th width="70">Delivery Date</th>
                        <th width="100">Item Category</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Sub. Group</th>
                        <th width="80">Item Code</th>
                        <th width="150">Item Description</th>
                        <th width="100">Required For</th>
                        <th width="70"> UOM</th>
                        <th width="100">Req. Quantity </th>

                        <!--1110 wo details-->
                        <th width="50">WO No</th>
                        <th width="100">Item Category</th>
                        <th width="100">Item Group</th>
                        <th width="100">Item Sub. Group</th>
                        <th width="80">Item Code</th>
                        <th width="150">Item Description</th>
                        <th width="80">WO Qnty</th>
                        <th width="80">Wo Rate</th>
                        <th width="80">WO Amount</th>
                        <th width="70">WO Date</th>
                        <th width="80">WO Balance</th>
                        <th width="150">Supplier</th>

                        <!--840 pi details-->
                        <th width="130">PI No</th>
                        <th width="70">PI Date</th>
                        <th width="150">Supplier</th>
                        <th width="100">Item Category</th>
                        <th width="70">UOM</th>
                        <th width="80">PI Quantity</th>
                        <th width="80">Unit Price</th>
                        <th width="80">PI Value</th>
                        <th width="70">Currency</th>
                        <th width="100">Indentor Name</th>

                        <!--550 lc details-->
                        <th width="70">LC Date</th>
                        <th width="120">LC No</th>
                        <th width="80">Pay Term</th>
                        <th width="50">Tenor</th>
                        <th width="80">LC Amount</th>
                        <th width="70">Shipment Date</th>
                        <th width="80">Expiry Date</th>

                        <!--1100 Invoice details-->
                        <th width="150">Invoice No</th>
                        <th width="70">Invoice Date</th>
                        <th width="80">Incoterm</th>
                        <th width="100">Incoterm Place</th>
                        <th width="80">B/L No</th>
                        <th width="70">BL Date</th>
                        <th width="100">Mother Vassel</th>
                        <th width="100">Feedar Vassel</th>
                        <th width="100">Continer No</th>
                        <th width="80">Pkg Qty</th>
                        <th width="100">Doc Send to CNF</th>
                        <th width="70">NN Doc Received Date</th>
                        <th width="80">Bill Of Entry No</th>

                        <!--290 Payment details-->
                        <th width="70">Maturity Date</th>
                        <th width="70">Maturity Month</th>
                        <th width="70">Payment Date</th>
                        <th width="80">Paid Amount</th>

                        <!--340 MRR details-->
                        <th width="80">MRR Qnty</th>
                        <th width="80">MRR Value</th>
                        <th width="80">Short Value</th>
                        <th >Pipeline</th>
                    </tr>
                    </thead>
                </table>
                <!-- <div style="width:5660px; max-height:300px; overflow-y:scroll" id="scroll_body"> -->
                <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body">
                    <tbody>
                    <?
                    }
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

                        <? //------------------------------Requisition dtls start----------------------------------------- ?>
                        <td width="30" align="center"><p><? echo $j; ?></p></td>
                        <td width="50" align="center"><p><? echo $req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["requ_prefix_num"]; ?></p></td>
                        <td width="70" align="center"><p>
                                <?
                                $req_date=$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["requisition_date"];
                                if($req_date!="" && $req_date!="0000-00-00") echo change_date_format($req_date);
                                ?></p>
                        </td>
                        <td width="150"><p><? echo $store_array[$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["store_name"]]; ?></p></td>
                        <td width="70" align="center"><p>
                                <?
                                $delivry_date=$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["delivery_date"];
                                if($delivry_date!="" && $delivry_date!="0000-00-00") echo change_date_format($delivry_date);
                                ?></p>
                        </td>
                        <td width="100"><p><? echo $item_category[$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>

                        <? $prod_id=$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["prod_id"]; ?>

                        <td width="100"><p><? echo $item_group_array[$prod_data_array[$prod_id]["item_group_id"]]; ?></p></td>
                        <td width="100"><p><? echo $prod_data_array[$prod_id]["sub_group_name"]; ?></p></td>
                        <td width="80"><p><? echo $prod_data_array[$prod_id]["item_code"]; ?></p></td>
                        <td width="150"><p>
                                <?
                                if ($cbo_item_category_id==1)
                                {
                                    echo $req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_description"];
                                }
                                else
                                {
                                    echo $prod_data_array[$prod_id]["item_description"];
                                }
                                ?>
                            </p></td>
                        <td width="100"><p><? echo $req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["required_for"]; ?></p></td>
                        <td width="70"><p>
                                <?
                                echo $unit_of_measurement[$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["cons_uom"]];
                                //echo $unit_of_measurement[$prod_data_array[$prod_id]["unit_of_measure"]];
                                ?>
                            </p></td>
                        <td width="100" align="right" title="<? echo $req_qty;?>"><p>
                                <?
                                $req_qty=$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["quantity"];
                                echo number_format($req_qty,2);
                                $total_req_qnty+=$req_qty;
                                //echo "=================".$total_req_qnty;
                                ?>
                            </p></td>
                        <? //------------------------------WO dtls start------------------------------------------- ?>

                        <td width="50" align="center"><p>
                                <?
                                $wo_no=implode(",",array_unique(explode(",",chop($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_number_prefix_num"]," , "))));
                                echo $wo_no;
                                ?>
                            </p></td>
                        <td width="100"><p><? echo $item_category[$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>

                        <? $wo_prod_id=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["prod_id"]; ?>

                        <td width="100"><p><? echo $item_group_array[$prod_data_array[$wo_prod_id]["item_group_id"]]; ?></p></td>
                        <td width="100"><p><? echo $prod_data_array[$wo_prod_id]["sub_group_name"]; ?></p></td>
                        <td width="80"><p><? echo $prod_data_array[$wo_prod_id]["item_code"]; ?></p></td>
                        <td width="150"><p>
                                <?
                                if ($cbo_item_category_id==1)
                                {
                                    echo $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_description"];
                                }
                                else
                                {
                                    echo $prod_data_array[$wo_prod_id]["item_description"];
                                }
                                ?>
                            </p></td>
                        <td width="80" align="right"><p>
                                <?
                                echo number_format($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"],2);
                                $total_wo_qnty+=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"];
                                ?>
                            </p></td>
                        <td width="80" align="right"><p>
                                <?
                                $wo_rate=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"]/$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"];
                                echo number_format($wo_rate,2);
                                ?>
                            </p></td>
                        <td width="80" align="right"><p>
                                <?
                                echo number_format($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"],2);
                                $total_wo_amt+=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"];
                                ?>
                            </p></td>
                        <td width="70" title="last wo date" align="center"><p>
                                <?
                                $wo_po_date=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_date"];
                                if($wo_po_date!="" && $wo_po_date!="0000-00-00") echo change_date_format($wo_po_date);
                                ?>
                            </p></td>
                        <td width="80" align="right" title="Requisition Quantity-Wo Quantity"><p>
                                <?
                                if($req_qty !=0 || $req_qty !="")
                                {
                                    $wo_balance=$req_qty-$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"];
                                    echo number_format($wo_balance,2);
                                }
                                $total_wo_balance+=$wo_balance;
                                ?>
                            </p></td>
                        <td width="150"><p>
                                <? $wo_supplier=$suplier_array[$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]];
                                echo $wo_supplier;
                                ?>
                            </p></td>

                        <? //------------------------------PI dtls start------------------------------------------- ?>
                        <?
                        if(chop($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_id"]," , ") !="")
                        {
                            ?>
                            <td width="130" align="center"><p>
                                    <?
                                    $pi_no=implode(",",array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_number"]," , "))));
                                    echo $pi_no;
                                    ?>
                                </p></td>
                            <td width="70" align="center" title="Last PI Date"><p>
                                    <?
                                    $pi_date_data=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_date"];
                                    if($pi_date_data!="" && $pi_date_data!="0000-00-00") echo change_date_format($pi_date_data);
                                    ?>
                                </p></td>
                            <td width="150"><p>
                                    <?
                                    $pi_suplier=$suplier_array[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]];
                                    echo $pi_suplier;
                                    ?>
                                </p></td>
                            <td width="100"><p><? echo $item_category[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>
                            <td width="70" align="center"><P><? echo $unit_of_measurement[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["uom"]]; ?></P></td>
                            <td width="80" align="right"><p>
                                    <?
                                    echo number_format($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["quantity"],2);
                                    $total_pi_qnty+=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["quantity"];
                                    ?>
                                </p></td>
                            <td width="80" align="right"><P>
                                    <?
                                    $pi_rate=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["amount"]/$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["quantity"];
                                    echo number_format($pi_rate,2);
                                    ?>
                                </P></td>
                            <td width="80" align="right"><p>
                                    <?
                                    echo number_format($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["amount"],2);
                                    $total_pi_amt+=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["amount"];
                                    ?>
                                </p></td>
                            <td width="70"><P><? echo $currency[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["currency_id"]]; ?></P></td>
                            <td width="100"><p>
                                    <?
                                    $pi_indore_name=$indentor_name_array[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["intendor_name"]];
                                    echo $pi_indore_name;
                                    ?>
                                </p></td>

                            <? //------------------------------LC dtls start------------------------------------------- ?>


                            <?
                            $pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_id"]," , ")));

                            foreach($pi_id_arr as $piID)
                            {
                                if(!in_array($btb_data_array[$piID]["lc_id"],$btb_tem_lc_array))
                                {
                                    $btb_tem_lc_array[$btb_data_array[$piID]["lc_id"]]=$btb_data_array[$piID]["lc_id"];
                                    $lc_date=$btb_data_array[$piID]["lc_date"];
                                    $lc_no.=$btb_data_array[$piID]["lc_number"].",";
                                    $lc_pay_term=$pay_term[$btb_data_array[$piID]["payterm_id"]];
                                    $lc_tenor+=$btb_data_array[$piID]["tenor"];
                                    $lc_amt+=$btb_data_array[$piID]["lc_value"];
                                    $lc_ship_date=$btb_data_array[$piID]["last_shipment_date"];
                                    $lc_expire_date=$btb_data_array[$piID]["lc_expiry_date"];
                                }

                                if(!in_array($inv_pay_data_array[$piID]["invoice_id"],$inv_temp_array))
                                {
                                    $inv_temp_array[$inv_pay_data_array[$piID]["invoice_id"]]=$inv_pay_data_array[$piID]["invoice_id"];
                                    $invoice_id.=$inv_pay_data_array[$piID]["invoice_id"].",";
                                    $invoice_no.=$inv_pay_data_array[$piID]["invoice_no"].",";
                                    $invoice_date=$inv_pay_data_array[$piID]["invoice_date"];
                                    $inco_term=$inv_pay_data_array[$piID]["inco_term"];
                                    $inco_term_place=$inv_pay_data_array[$piID]["inco_term_place"];
                                    $bl_no=$inv_pay_data_array[$piID]["bill_no"];
                                    $bl_date=$inv_pay_data_array[$piID]["bill_date"];
                                    $mother_vasel=$inv_pay_data_array[$piID]["mother_vessel"];
                                    $feder_vasel=$inv_pay_data_array[$piID]["feeder_vessel"];
                                    $continer_no=$inv_pay_data_array[$piID]["container_no"];
                                    $pakag_qnty=$inv_pay_data_array[$piID]["pkg_quantity"];
                                    $doc_send_cnf=$inv_pay_data_array[$piID]["doc_to_cnf"];
                                    $bill_entry_no=$inv_pay_data_array[$piID]["bill_of_entry_no"];
                                    $maturity_date=$inv_pay_data_array[$piID]["maturity_date"];
                                    $maturity_month=$inv_pay_data_array[$piID]["maturity_date"];

                                    $pay_date=$inv_pay_data_array[$piID]["payment_date"];
                                    $pay_amt+=$inv_pay_data_array[$piID]["accepted_ammount"];
                                }
                            }
                            ?>
                            <td width="70" align="center"><P><? if($lc_date!="" && $lc_date!="0000-00-00") echo change_date_format($lc_date); ?></P></td>
                            <td width="120"><P><? echo $lc_no=implode(",",array_unique(explode(",",chop($lc_no," , ")))); ?></P></td>
                            <td width="80"><P><? echo $lc_pay_term; ?></P></td>
                            <td width="50" align="center"><P><? echo $lc_tenor; ?></P></td>
                            <td width="80" align="right"><P><? echo number_format($lc_amt,2); $total_lc_amt+=$lc_amt; ?></P></td>
                            <td width="70" align="center" title="Last Ship Date"><P><? if($lc_ship_date!="" && $lc_ship_date!="0000-00-00") echo change_date_format($lc_ship_date); ?></P></td>
                            <td width="80"  align="center" title="Last Expire Date"><P><? if($lc_expire_date!="" && $lc_expire_date!="0000-00-00") echo change_date_format($lc_expire_date); ?></P></td>

                            <? //------------------------------Invoice dtls start------------------------------------------- ?>

                            <td width="150"><P><? echo $invoice_no=implode(",",array_unique(explode(",",chop($invoice_no," , ")))); ?></P></td>
                            <td width="70" align="center" title="Last Invoice Date"><P><? if($invoice_date!="" && $invoice_date!="0000-00-00") echo change_date_format($invoice_date); ?></P></td>
                            <td width="80"><P><? echo $inco_term; ?></P></td>
                            <td width="100"><P><? echo $inco_term_place; ?></P></td>
                            <td width="80"><P><? echo $bl_no; ?></P></td>
                            <td width="70" align="center"><P><? if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?></P></td>
                            <td width="100"><P><? echo $mother_vasel; ?></P></td>
                            <td width="100"><P><? echo $feder_vasel; ?></P></td>
                            <td width="100"><P><? echo $continer_no; ?></P></td>
                            <td width="80" align="right"><P><? echo number_format($pakag_qnty,2); $total_pkg_qnty+=$pakag_qnty; ?></P></td>
                            <td width="100" align="center"><P><? if($doc_send_cnf!="" && $doc_send_cnf!="0000-00-00") echo change_date_format($doc_send_cnf); ?></P></td>
                            <td width="70"></td>
                            <td width="80"><P><? echo $bill_entry_no; ?></P></td>

                            <td width="70" align="center"><P><? if($maturity_date!="" && $maturity_date!="0000-00-00") echo change_date_format($maturity_date); ?></P></td>
                            <td width="70" align="center"><P><? if($maturity_month!="" && $maturity_month!="0000-00-00") echo change_date_format($maturity_month); ?></P></td>
                            <td width="70" align="center"><P><? if($pay_date!="" && $pay_date!="0000-00-00") echo change_date_format($pay_date); ?></P></td>
                            <td width="80" align="right"><P><? echo number_format($pay_amt,2); $total_pay_amt+=$pay_amt; ?></P></td>
                            <?
                        }
                        else
                        {
                            ?>
                            <td width="130"></td>
                            <td width="70"></td>
                            <td width="150"></td>
                            <td width="100"></td>
                            <td width="70"></td>
                            <td width="80"></td>
                            <td width="80"></td>
                            <td width="80"></td>
                            <td width="70"></td>
                            <td width="100"></td>

                            <td width="70"></td>
                            <td width="120"></td>
                            <td width="80"></td>
                            <td width="50"></td>
                            <td width="80"></td>
                            <td width="70"></td>
                            <td width="80"></td>

                            <td width="150"></td>
                            <td width="70"></td>
                            <td width="80"></td>
                            <td width="100"></td>
                            <td width="80"></td>
                            <td width="70"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="80"></td>
                            <td width="100"></td>
                            <td width="70"></td>
                            <td width="80"></td>

                            <td width="70"></td>
                            <td width="70"></td>
                            <td width="70"></td>
                            <td width="80"></td>
                            <?
                        }

                        //---------------------------------Store Details starts----------------------------------

                        $pi_id=chop($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_id"],",");
                        if ($pi_id=='') {
                            $pi_id=chop($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_mst_id"],",");
                        }

                        //echo $pi_id;
                        if ($cbo_item_category_id==1)
                        {
                            $item_desc=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_description"];
                            $receive_basis=$recv_data_array[$pi_id][$item_desc]["receive_basis"];
                            $prod_id=$recv_data_array[$pi_id][$item_desc]["prod_id"];
                        }
                        else
                        {
                            $receive_basis=$recv_data_array[$pi_id][$row[csf("prod_id")]]["receive_basis"];
                            $prod_id=$row[csf("prod_id")];
                        }

                        $data=$pi_id."**".$receive_basis;
                        ?>
                        <td width="80" align="right"><p><a href="##" onClick="openmypage_popup('<? echo $data; ?>','<? echo $prod_id; ?>','Receive Info','receive_popup');" >
                                    <?
                                    if ($cbo_item_category_id==1)
                                    {
                                        $rcv_qnty=$recv_data_array[$pi_id][$item_desc]["rcv_qnty"];
                                    }
                                    else
                                    {
                                        $rcv_qnty=$recv_data_array[$pi_id][$row[csf("prod_id")]]["rcv_qnty"];
                                    }
                                    echo number_format($rcv_qnty,2);
                                    $total_mrr_qnty+=$rcv_qnty;
                                    ?>
                                </a></p>
                        </td>
                        <td width="80" align="right"><p>
                                <?
                                if ($cbo_item_category_id==1)
                                {
                                    $rcv_value=$recv_data_array[$pi_id][$item_desc]["rcv_amt"];
                                }
                                else
                                {
                                    $rcv_value=$recv_data_array[$pi_id][$row[csf("prod_id")]]["rcv_amt"];
                                }
                                echo number_format($rcv_value,2);
                                $total_mrr_amt+=$rcv_value;
                                ?></p>
                        </td>
                        <td align="right" title="Wo Value-Receive Value" width="80"><p>
                                <?
                                $short_value=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"]-$rcv_value;
                                echo number_format($short_value,2);
                                $total_short_amt+=$short_value;
                                ?></p>
                        </td>
                        <?
                        $pipe_pi_qnty=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["quantity"];
                        $pipe_wo_qnty=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"];

                        if($pipe_pi_qnty!="" && $pipe_wo_qnty =="")
                        {
                            $pipe_line=$pipe_pi_qnty-$rcv_qnty;
                        }
                        else
                        {
                            $pipe_line=$pipe_wo_qnty-$rcv_qnty;
                        }
                        ?>
                        <td  align="right"><P>
                                <?
                                echo number_format($pipe_line,2);
                                $total_pipe_line+=$pipe_line;
                                ?></P>
                        </td>
                    </tr>
                    <?
                    $i++;
                    $pipe_wo_qnty=0;

                    $j++;
                    }
                    ?>
                    </tbody>
                </table>
                <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_footer">
                    <tfoot>
                    <tr>
                        <!--1210 requisition details-->
                        <th width="30"></th>
                        <th width="50"></th>
                        <th width="70"></th>
                        <th width="150"></th>
                        <th width="70"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="80"></th>
                        <th width="150"></th>
                        <th width="100"></th>
                        <th width="70"> </th>
                        <th width="100" id="value_total_req_qnty" align="right"><? echo number_format($total_req_qnty,0); ?> </th>

                        <!--1110 wo details-->
                        <th width="50"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="80"></th>
                        <th width="150"></th>
                        <th width="80" id="value_total_wo_qnty" align="right"><? echo number_format($total_wo_qnty,0); ?></th>
                        <th width="80"></th>
                        <th width="80" id="value_total_wo_amt" align="right"><? echo number_format($total_wo_amt,2); ?></th>
                        <th width="70"></th>
                        <th width="80" id="value_total_wo_balance" align="right"><? echo number_format($total_wo_balanc,2); ?></th>
                        <th width="150"></th>

                        <!--840 pi details-->
                        <th width="130"></th>
                        <th width="70"></th>
                        <th width="150"></th>
                        <th width="100"></th>
                        <th width="70"></th>
                        <th width="80" id="value_total_pi_qnty" align="right"><? echo number_format($total_pi_qnty,0); ?></th>
                        <th width="80"></th>
                        <th width="80" id="value_total_pi_amt" align="right"><? echo number_format($total_pi_amt,2); ?></th>
                        <th width="70"></th>
                        <th width="100"></th>

                        <!--550 lc details-->
                        <th width="70"></th>
                        <th width="100"></th>
                        <th width="70"></th>
                        <th width="80"></th>
                        <th width="80" id="value_total_lc_amt" align="right"><? echo number_format($total_lc_amt,2); ?></th>
                        <th width="70"></th>
                        <th width="80"></th>

                        <!--1100 Invoice details-->
                        <th width="150"></th>
                        <th width="70"></th>
                        <th width="80"></th>
                        <th width="100"></th>
                        <th width="80"></th>
                        <th width="70"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="80" id="value_total_pkg_qnty" align="right"><? echo number_format($total_pkg_qnty,0); ?></th>
                        <th width="100"></th>
                        <th width="70"></th>
                        <th width="80"></th>

                        <!--290 Payment details-->
                        <th width="70"></th>
                        <th width="70"></th>
                        <th width="70"></th>
                        <th width="80" id="value_total_pay_amt" align="right"><? echo number_format($total_pay_amt,2); ?></th>

                        <!--340 MRR details-->
                        <th width="80" id="value_total_mrr_qnty" align="right"><? echo number_format($total_mrr_qnty,0); ?></th>
                        <th width="80" id="value_total_mrr_amt" align="right"><? echo number_format($total_mrr_amt,2); ?></th>
                        <th width="80" id="value_total_short_amt" align="right"><? echo number_format($total_short_amt,2); ?></th>
                        <th id="value_total_pipe_line" align="right"><? echo number_format($total_pipe_line,2); ?></th>
                    </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
        <?
    }
    else if(($txt_date_from == "" || $txt_date_to== "") &&  $txt_wo_po_no=="" && $txt_req_no=="" && $cbo_supplier>0)
	{
		$gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
		if($gblDel)
		{
			oci_commit($con);
			//disconnect($con);
		}
		$temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
		if($temp_table_id=="") $temp_table_id=1;
			
		$sql_cond="";
		$requ_table="";
		if($cbo_company_name>0) $sql_cond.=" and a.company_name='$cbo_company_name' ";
		if($cbo_item_category_id>0) $sql_cond.=" and b.item_category_id='$cbo_item_category_id' ";
		if($cbo_supplier>0) $sql_cond.=" and a.supplier_id='$cbo_supplier' ";


		/*if($txt_wo_po_no!="") 
		{
			$sql_cond.=" and a.wo_number like '%$txt_wo_po_no%' ";
		}
		
		if($txt_wo_po_no=="" && $txt_req_no=="") 
		{
			if($txt_date_from!="" && $txt_date_to!="") $sql_cond.=" and a.wo_date between  '$txt_date_from' and '$txt_date_to'";
		}*/

		//Finding WO/PO Data....
		$sql_wo=sql_select("select a.id as wo_mst_id, a.wo_number, a.wo_number_prefix_num, a.wo_date, a.wo_basis_id, a.supplier_id, b.id as wo_dtls_id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name,b.uom, b.requisition_dtls_id,b.requisition_no, b.item_id as prod_id, b.item_category_id, b.supplier_order_quantity, b.rate, b.amount 
		from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond and b.item_category_id not in (2,3,12,13,14,24,25,28,30) order by b.item_category_id, a.wo_number");
		
			
		$wo_data_array=array();
		$req_dtls_id_arr=array();
		$wo_num_arr=array(); $data_array = array();
		foreach($sql_wo as $row)
		{
			$wo_num_arr[$row[csf("wo_dtls_id")]]=$row[csf("wo_dtls_id")];
			$req_dtls_id_arr[$row[csf("requisition_dtls_id")]]=$row[csf("requisition_dtls_id")];
			
			if($row[csf("requisition_dtls_id")])
			{
				$refrID1=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("requisition_dtls_id")].",1,".$user_id.")");
				if(!$refrID1)
				{
					echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("requisition_dtls_id")].",1,".$user_id.")";oci_rollback($con);disconnect($con);die;
				}
				$temp_table_id++;
			}
			
			if($row[csf("wo_dtls_id")])
			{
				$refrID2=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("wo_dtls_id")].",2,".$user_id.")");
				if(!$refrID2)
				{
					echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("wo_dtls_id")].",2,".$user_id.")";oci_rollback($con);disconnect($con);die;
				}
				$temp_table_id++;
			}

			$data_array[$row[csf("item_category_id")]][1][$row[csf("wo_number")]]["wo_number_prefix_num"] =$row[csf("wo_number_prefix_num")];
			$data_array[$row[csf("item_category_id")]][1][$row[csf("wo_number")]]["wo_date"] =$row[csf("wo_date")];
			$data_array[$row[csf("item_category_id")]][1][$row[csf("wo_number")]]["prod_id"] .=$row[csf("prod_id")].",";
			$data_array[$row[csf("item_category_id")]][1][$row[csf("wo_number")]]["supplier_id"] =$row[csf("supplier_id")];
			$data_array[$row[csf("item_category_id")]][1][$row[csf("wo_number")]]["amount"] +=$row[csf("amount")];
			$data_array[$row[csf("item_category_id")]][1][$row[csf("wo_number")]]["supplier_order_quantity"] +=$row[csf("supplier_order_quantity")];
			$data_array[$row[csf("item_category_id")]][1][$row[csf("wo_number")]]["uom"] .=$row[csf("uom")].",";
			$data_array[$row[csf("item_category_id")]][1][$row[csf("wo_number")]]["requisition_no"] .=$row[csf("requisition_no")].",";

		}
		
		if($refrID1 && $refrID2)
		{
			oci_commit($con);
		}

		//Finding Requisition Data....
		if(count($req_dtls_id_arr)>0)
		{
			/*$req_dtsl_ids=implode(",", array_filter(array_unique($req_dtls_id_arr)));

			$dtls_id = $req_dtls_id_cond = ""; 
			$req_dtls_id_arr=explode(",",$req_dtsl_ids);
			if($db_type==2 && count($req_dtls_id_arr)>999)
			{
				$req_dtls_id_chunk=array_chunk($req_dtls_id_arr,999) ;
				foreach($req_dtls_id_chunk as $chunk_arr)
				{
					$dtls_id.=" b.id in(".implode(",",$chunk_arr).") or ";	
				}
						
				$req_dtls_id_cond.=" and (".chop($dtls_id,'or ').")";			
				
			}
			else
			{ 	
				
				$req_dtls_id_cond=" and b.id in($req_dtsl_ids)"; 
			}*/

			if($cbo_item_category_id) $req_sql_category_cond = " and b.item_category=$cbo_item_category_id"; else $req_sql_category_cond="";
			$req_sql=sql_select("select a.id as req_id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date, b.item_category as item_category_id, b.cons_uom, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.color_id, b.id as req_dtsl_id, b.product_id as prod_id, b.required_for, b.quantity, b.rate, b.amount 
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, gbl_temp_report_id c  
			where a.id=b.mst_id and b.id=c.ref_val and c.ref_from=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and b.item_category not in (2,3,12,13,14,24,25,28,30)"); //and b.id in ($req_dtsl_ids)

			$req_data_array=array();
			foreach($req_sql as $row)
			{
				$req_data_array[$row[csf("requ_no")]]["req_id"]=$row[csf("req_id")];
				$req_data_array[$row[csf("requ_no")]]["cons_uom"] .=$row[csf("cons_uom")].",";
				$req_data_array[$row[csf("requ_no")]]["requ_prefix_num"]=$row[csf("requ_prefix_num")];
				$req_data_array[$row[csf("requ_no")]]["requisition_date"]=$row[csf("requisition_date")];
				$req_data_array[$row[csf("requ_no")]]["store_name"]=$row[csf("store_name")];
				$req_data_array[$row[csf("requ_no")]]["pay_mode"]=$row[csf("pay_mode")];
				$req_data_array[$row[csf("requ_no")]]["cbo_currency"]=$row[csf("cbo_currency")];
				$req_data_array[$row[csf("requ_no")]]["delivery_date"]=$row[csf("delivery_date")];
				$req_data_array[$row[csf("requ_no")]]["prod_id"].=$row[csf("prod_id")].",";
				$req_data_array[$row[csf("requ_no")]]["required_for"]=$row[csf("required_for")];
				$req_data_array[$row[csf("requ_no")]]["quantity"]+=$row[csf("quantity")];
				$req_data_array[$row[csf("requ_no")]]["rate"]=$row[csf("rate")];
				$req_data_array[$row[csf("requ_no")]]["amount"]+=$row[csf("amount")];

			}	
		}

		//Finding PI data....
		//$wo_num_ids="";
		/*$wo_num_ids="'".implode("','", array_filter(array_unique($wo_num_arr)))."'";
		$dtls_id = $wo_num_ids_cond = ""; 
		$wo_num_arr=explode(",",$wo_num_ids);
		if($db_type==2 && count($wo_num_arr)>999)
		{
			$wo_num_chunk=array_chunk($wo_num_arr,999) ;
			foreach($wo_num_chunk as $chunk_arr)
			{
				$dtls_id.=" b.work_order_no in(".implode(",",$chunk_arr).") or ";	
			}
					
			$wo_num_ids_cond.=" and (".chop($dtls_id,'or ').")";			
			
		}
		else
		{ 	
			
			$wo_num_ids_cond=" and b.work_order_no in($wo_num_ids)"; 
		}*/
		//if($cbo_item_category_id) $sql_pi_category_cond = "and c.item_category_id=$cbo_item_category_id and b.item_category_id=$cbo_item_category_id"; else $sql_pi_category_cond = "";
		
		$sql_pi="SELECT a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, b.item_category_id, a.currency_id, a.intendor_name, b.id as pi_dtls_id, case when b.item_prod_id !='' or b.item_prod_id is not null then b.item_prod_id else 0 end as prod_id, b.work_order_no, b.work_order_dtls_id, b.uom, b.quantity, b.amount, c.id as wo_dtls_id, c.requisition_dtls_id 
		from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls c, gbl_temp_report_id d 
		where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.id=d.ref_val and d.ref_from=2 and importer_id=$cbo_company_name and a.pi_basis_id = 1 and b.status_active = 1 and a.status_active = 1 and c.status_active = 1 ";
		//echo $sql_pi; die;
		$sql_pi_result=sql_select($sql_pi);
		$pi_data_arr=$pi_id_arr=array();
		foreach($sql_pi_result as $row)
		{
			$pi_id_arr[$row[csf("pi_id")]]=$row[csf("pi_id")];
			
			if($row[csf("pi_id")])
			{
				$refrID3=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",3,".$user_id.")");
				if(!$refrID3)
				{
					echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",3,".$user_id.")";oci_rollback($con);disconnect($con);die;
				}
				$temp_table_id++;
			}
			
			$pi_data_arr[$row[csf("work_order_no")]][$row[csf("work_order_dtls_id")]][$row[csf("prod_id")]]["requisition_dtls_id"]=$row[csf("requisition_dtls_id")];
			$pi_data_arr[$row[csf("work_order_no")]]["pi_id"].=$row[csf("pi_id")].",";
			$pi_data_arr[$row[csf("work_order_no")]]["pi_number"].=$row[csf("pi_number")].",";
			$pi_data_arr[$row[csf("work_order_no")]]["pi_date"]=$row[csf("pi_date")];
			$pi_data_arr[$row[csf("work_order_no")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
			$pi_data_arr[$row[csf("work_order_no")]]["supplier_id"]=$row[csf("supplier_id")];
			$pi_data_arr[$row[csf("work_order_no")]]["item_category_id"]=$row[csf("item_category_id")];
			$pi_data_arr[$row[csf("work_order_no")]]["currency_id"]=$row[csf("currency_id")];
			$pi_data_arr[$row[csf("work_order_no")]]["intendor_name"]=$row[csf("intendor_name")];
			$pi_data_arr[$row[csf("work_order_no")]]["pi_dtls_id"].=$row[csf("pi_dtls_id")].",";
			$pi_data_arr[$row[csf("work_order_no")]]["prod_id"].=$row[csf("prod_id")].",";
			$pi_data_arr[$row[csf("work_order_no")]]["uom"]=$row[csf("uom")];
			$pi_data_arr[$row[csf("work_order_no")]]["quantity"]+=$row[csf("quantity")];
			$pi_data_arr[$row[csf("work_order_no")]]["amount"]+=$row[csf("amount")];
			$pi_data_arr[$row[csf("work_order_no")]]["wo_dtls_id"].=$row[csf("wo_dtls_id")].",";
		}
		 
		if($refrID3)
		{
			oci_commit($con);
		}
		
		$independ_pi_sql  = sql_select("select a.id as pi_id, a.pi_number, a.pi_date, a.last_shipment_date, a.supplier_id, b.item_category_id, a.currency_id, a.intendor_name, case when b.item_prod_id !='' or b.item_prod_id is not null then b.item_prod_id else 0 end as prod_id, b.uom, b.quantity, b.amount 
		from com_pi_master_details a, com_pi_item_details b 
		where a.id=b.pi_id and importer_id=$cbo_company_name and a.supplier_id = '$cbo_supplier' and a.pi_basis_id = 2 and b.status_active = 1 and a.status_active = 1");
		foreach ($independ_pi_sql as $val) 
		{
			$pi_id_arr[$row[csf("pi_id")]]=$row[csf("pi_id")];
			if($row[csf("pi_id")])
			{
				$refrID3=execute_query("insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",3,".$user_id.")");
				if(!$refrID3)
				{
					echo "insert into gbl_temp_report_id (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("pi_id")].",3,".$user_id.")";oci_rollback($con);disconnect($con);die;
				}
				$temp_table_id++;
			}
			$data_array[$row[csf("item_category_id")]][2][$row[csf("pi_id")]]["pi_number"] =$row[csf("pi_number")];
			$data_array[$row[csf("item_category_id")]][2][$row[csf("pi_id")]]["pi_date"] =$row[csf("pi_date")];
			$data_array[$row[csf("item_category_id")]][2][$row[csf("pi_id")]]["supplier_id"] =$row[csf("supplier_id")];
			$data_array[$row[csf("item_category_id")]][2][$row[csf("pi_id")]]["currency_id"] =$row[csf("currency_id")];
			$data_array[$row[csf("item_category_id")]][2][$row[csf("pi_id")]]["intendor_name"] =$row[csf("intendor_name")];
			$data_array[$row[csf("item_category_id")]][2][$row[csf("pi_id")]]["quantity"] +=$row[csf("quantity")];
			$data_array[$row[csf("item_category_id")]][2][$row[csf("pi_id")]]["amount"] +=$row[csf("amount")];
		}
		
		if($refrID3)
		{
			oci_commit($con);
		}
		
		//finding BTB Lc data....
		//if(!empty(array_filter($pi_id_arr)))
		if(!empty($pi_id_arr))
		{
			/*$pi_ids="";
			$pi_ids="'".implode("','", array_filter(array_unique($pi_id_arr)))."'";
			$dtls_id = $pi_ids_cond = ""; 
			$dtls_id2 = $pi_ids_cond2 = ""; 
			$pi_id_arr=explode(",",$pi_ids);
			if($db_type==2 && count($pi_id_arr)>999)
			{
				$pi_id_chunk=array_chunk($pi_id_arr,999) ;
				foreach($pi_id_chunk as $chunk_arr)
				{
					$dtls_id.=" b.pi_id in(".implode(",",$chunk_arr).") or ";	
					$dtls_id2.=" a.pi_id in(".implode(",",$chunk_arr).") or ";	
				}
						
				$pi_ids_cond.=" and (".chop($dtls_id,'or ').")";			
				$pi_ids_cond2.=" and (".chop($dtls_id2,'or ').")";			
				
			}
			else
			{ 	
				
				$pi_ids_cond=" b.pi_id in($pi_ids)"; 
				$pi_ids_cond2=" a.pi_id in($pi_ids)"; 
			}*/

			$sql_btb=sql_select("select a.id as lc_id, a.lc_number, a.lc_date, a.payterm_id, a.tenor, a.lc_value, a.last_shipment_date, a.lc_expiry_date, b.pi_id 
			from com_btb_lc_master_details a, com_btb_lc_pi b, gbl_temp_report_id c 
			where a.id=b.com_btb_lc_master_details_id and b.pi_id=c.ref_val and c.ref_from=3 and a.status_active=1 and a.is_deleted=0 $pi_ids_cond");
			$btb_data_array=array();
			foreach($sql_btb as $row)
			{
				$btb_data_array[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
				$btb_data_array[$row[csf("pi_id")]]["lc_id"]=$row[csf("lc_id")];
				$btb_data_array[$row[csf("pi_id")]]["lc_number"]=$row[csf("lc_number")];
				$btb_data_array[$row[csf("pi_id")]]["lc_date"]=$row[csf("lc_date")];
				$btb_data_array[$row[csf("pi_id")]]["payterm_id"]=$row[csf("payterm_id")];
				$btb_data_array[$row[csf("pi_id")]]["tenor"]=$row[csf("tenor")];
				$btb_data_array[$row[csf("pi_id")]]["lc_value"]=$row[csf("lc_value")];
				$btb_data_array[$row[csf("pi_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
				$btb_data_array[$row[csf("pi_id")]]["lc_expiry_date"]=$row[csf("lc_expiry_date")];
			}

			//Finding Invoice data....
			if($db_type==0)
			{
				$pi_cond="group_concat(a.pi_id) as pi_id";
			}
			else if($db_type==2)
			{
				$pi_cond="LISTAGG(CAST(a.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.pi_id) as pi_id";
			}
			
			$sql_invoice_pay=sql_select("select $pi_cond, b.id as invoice_id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, e.payterm_id, b.id as accept_id 
			from gbl_temp_report_id p, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_btb_lc_master_details e 
			where p.ref_val=a.pi_id and p.ref_from=3 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and e.id = a.com_btb_lc_master_details_id and b.status_active=1 and b.is_deleted=0 
			group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, e.payterm_id");
			
			$inv_pay_data_array=array();
			$temp_inv_array=$temp_accept_id=array();
			foreach($sql_invoice_pay as $row)
			{
				$all_pi_id=array_unique(explode(",",$row[csf("pi_id")]));
				foreach($all_pi_id as $pi_id)
				{
					$inv_pay_data_array[$pi_id]["pi_id"]=$pi_id;
					$inv_pay_data_array[$pi_id]["invoice_id"].=$row[csf("invoice_id")].",";
					$inv_pay_data_array[$pi_id]["invoice_no"].=$row[csf("invoice_no")].",";
					$inv_pay_data_array[$pi_id]["document_value"]+=$row[csf("document_value")];
					$inv_pay_data_array[$pi_id]["invoice_date"]=$row[csf("invoice_date")];
					$inv_pay_data_array[$pi_id]["inco_term"]=$row[csf("inco_term")];
					$inv_pay_data_array[$pi_id]["inco_term_place"]=$row[csf("inco_term_place")];
					$inv_pay_data_array[$pi_id]["bill_no"]=$row[csf("bill_no")];
					$inv_pay_data_array[$pi_id]["bill_date"]=$row[csf("bill_date")];
					$inv_pay_data_array[$pi_id]["mother_vessel"]=$row[csf("mother_vessel")];
					$inv_pay_data_array[$pi_id]["feeder_vessel"]=$row[csf("feeder_vessel")];
					$inv_pay_data_array[$pi_id]["container_no"]=$row[csf("container_no")];
					$inv_pay_data_array[$pi_id]["doc_to_cnf"]=$row[csf("doc_to_cnf")];
					$inv_pay_data_array[$pi_id]["bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
					$inv_pay_data_array[$pi_id]["maturity_date"]=$row[csf("maturity_date")];

					if($temp_inv_array[$row[csf("invoice_id")]]=="")
					{
						$temp_inv_array[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
						$inv_pay_data_array[$pi_id]["pkg_quantity"]+=$row[csf("pkg_quantity")];
						
					}

					/*if($row[csf("payterm_id")]==1) //Pay Term = At sight
					{
						$inv_pay_data_array[$pi_id]["payment_date"]=$row[csf("invoice_date")];

						$cumulative_array=return_library_array("select pi_id, sum(current_acceptance_value) as accepted_ammount from com_import_invoice_dtls where pi_id=$pi_id and status_active=1 and is_deleted=0 group by pi_id",'pi_id','accepted_ammount'); 
						$inv_pay_data_array[$pi_id]["accepted_ammount"]=$cumulative_array[$pi_id];
					}*/
				}
				
			}

			//Finding invoice Payment data
			if ($db_type==0)
			{
				$sql_invoice_pay2= sql_select("select group_concat(a.pi_id) as pi_id, c.payment_date, c.id as accept_id, c.accepted_ammount 
				from gbl_temp_report_id p, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_import_payment c 
				where p.ref_val=a.pi_id and p.ref_from=3 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and b.id=c.invoice_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, c.payment_date, c.id, c.accepted_ammount");
			}
			else
			{
				$sql_invoice_pay2= sql_select("select LISTAGG(CAST(a.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.pi_id) as pi_id, c.payment_date, c.id as accept_id, c.accepted_ammount 
				from gbl_temp_report_id p, com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_import_payment c 
				where p.ref_val=a.pi_id and p.ref_from=3 and a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and b.id=c.invoice_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, c.payment_date, c.id, c.accepted_ammount");
			}
			
			//echo "select LISTAGG(CAST(a.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.pi_id) as pi_id, c.payment_date, c.id as accept_id, c.accepted_ammount from com_btb_lc_pi a, com_import_invoice_mst b, com_import_invoice_dtls d, com_import_payment c where a.com_btb_lc_master_details_id=d.btb_lc_id and b.id=d.import_invoice_id and b.id=c.invoice_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.pi_id in (".trim(implode(",",$pi_id_arr),",").") group by b.id, b.invoice_no, b.document_value, b.invoice_date, b.inco_term, b.inco_term_place, b.bill_no, b.bill_date, b.mother_vessel, b.feeder_vessel, b.container_no, b.pkg_quantity, b.doc_to_cnf, b.bill_of_entry_no, b.maturity_date, c.payment_date, c.id, c.accepted_ammount";
			foreach($sql_invoice_pay2 as $row)
			{
				$all_pi_id=array_unique(explode(",",$row[csf("pi_id")]));
				foreach($all_pi_id as $pi_id)
				{
					$inv_pay_data_array[$pi_id]["payment_date"]=$row[csf("payment_date")];
				}
				if($temp_accept_id[$row[csf("accept_id")]]=="")
				{
					$temp_accept_id[$row[csf("accept_id")]]=$row[csf("accept_id")];
					$inv_pay_data_array[$pi_id]["accepted_ammount"]+=$row[csf("accepted_ammount")];
				}
			}
			//----------------------------------End-------------------------------------------------------
		}

		if($cbo_item_category_id) $sql_receive_category_cond = " and b.item_category in($cbo_item_category_id)"; else $sql_receive_category_cond="";
		if ($cbo_item_category_id==1) // For Yarn
		{
			
			$sql_receive=sql_select("select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, c.product_name_details, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt from inv_receive_master a, inv_transaction b, product_details_master c where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) and b.prod_id=c.id and c.status_active=1 and c.is_deleted=0 group by a.receive_basis, b.prod_id,pi_wo_batch_no, c.product_name_details");
			$recv_data_array=array();
			foreach($sql_receive as $row)
			{
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["receive_basis"]=$row[csf("receive_basis")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["prod_id"]=$row[csf("prod_id")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("product_name_details")]]["rcv_amt"]=$row[csf("rcv_amt")];
			}
		}
		else
		{
			
			$sql_receive=sql_select("select a.receive_basis,b.pi_wo_batch_no as pi_id, b.prod_id, sum(b.order_qnty) as rcv_qnty, sum(b.order_amount) as rcv_amt from inv_receive_master a, inv_transaction b where a.id= b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name and a.receive_basis in(1,2,7) and b.transaction_type in(1) group by a.receive_basis, b.prod_id,pi_wo_batch_no");
			$recv_data_array=array();
			foreach($sql_receive as $row)
			{
				$recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["receive_basis"]=$row[csf("receive_basis")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
				$recv_data_array[$row[csf("pi_id")]][$row[csf("prod_id")]]["rcv_amt"]=$row[csf("rcv_amt")];
			}
		}
		
		$gblDel=execute_query("delete from gbl_temp_report_id where USER_ID=$user_id");
		if($gblDel)
		{
			oci_commit($con);disconnect($con);
		}
		
		$item_group_array = return_library_array("select id,item_name from  lib_item_group ","id","item_name");
		$store_array = return_library_array("select id,store_name from  lib_store_location ","id","store_name");
		$suplier_array = return_library_array("select id,supplier_name from  lib_supplier ","id","supplier_name");
		$indentor_name_array = return_library_array("select a.id,a.supplier_name from lib_supplier a,lib_supplier_party_type b where a.id = b.supplier_id and b.party_type = 40","id","supplier_name");
		ob_start();
		?>
			<div style="width:5660px; margin-left:10px">
	        <fieldset style="width:100%;">	 
	            <table width="5500" cellpadding="0" cellspacing="0" id="caption">
	                <tr>
	                	<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	                </tr> 
	                <tr>  
	                	<td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
	                </tr>  
	            </table>
	        	<br />
	        	
	            
	                <?
					$i=1;
					$btb_tem_lc_array=$inv_temp_array=array();
					//foreach($req_data_array as $req_dtls_id=>$row)
					foreach ($sql_wo as  $row) 
					{
						$wo_no=$wo_supplier="";$wo_qnty=$wo_rate=$wo_amount=$wo_balance=0;
						$pi_no=$pi_date=$pi_suplier=$pi_indore_name=$pi_id_all=$rcv_qnty=$rcv_value=$wo_mst_id_all=$pipe_line=$short_value=$pipe_pi_qnty=$pipe_wo_qnty=""; $pi_rate=0;
						$lc_date=$lc_no=$lc_pay_term=$lc_tenor=$lc_amt=$lc_ship_date=$lc_expire_date="";
						$invoice_id=$invoice_no=$invoice_date=$inco_term=$inco_term_place=$bl_no=$bl_date=$mother_vasel=$feder_vasel=$continer_no=$pakag_qnty=$doc_send_cnf=$bill_entry_no=$maturity_date=$maturity_month=$pay_date=$pay_amt="";
						
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						if (!in_array($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"], $checkCateArr)) 
						{
							$checkCateArr[] = $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"];
							if($i>1)
							{
								?>
								</tbody>
	            				</table>
								<?
							}
							
							$j=1;
						?>

						<table width="5660" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body1">
	                <thead>
	                	<tr>
	                		<th colspan="63" style="text-align: left !important; color: black"><? echo  $item_category[$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?> :</th>
	                	</tr>
	                    <tr>
	                        <th colspan="13" >Requisiton Details</th>
	                        <th colspan="12" >Work Order Details</th>
	                        <th colspan="10" >PI Details</th>
	                        <th colspan="7">L/C Details</th>
	                        <th colspan="13">Invoice Details</th>
	                        <th colspan="4">Payment Details</th>
	                        <th colspan="4">Store details</th>
	                    </tr>
	                    <tr>
	                        <!--1210 requisition details-->    
	                        <th width="30">SL</th>
	                        <th width="50">Req. No</th>
	                        <th width="70">Req. Date</th>
	                        <th width="150">Store Name</th>
	                        <th width="70">Delivery Date</th>
	                        <th width="100">Item Category</th>
	                        <th width="100">Item Group</th> 
	                        <th width="100">Item Sub. Group</th>
	                        <th width="80">Item Code</th>
	                        <th width="150">Item Description</th>
	                        <th width="100">Required For</th>
	                        <th width="70"> UOM</th>
	                        <th width="100">Req. Quantity </th>
	                        
	                        <!--1110 wo details-->
	                        <th width="50">WO No</th>
	                        <th width="100">Item Category</th>
	                        <th width="100">Item Group</th>
	                        <th width="100">Item Sub. Group</th>
	                        <th width="80">Item Code</th>
	                        <th width="150">Item Description</th>
	                        <th width="80">WO Qnty</th>
	                        <th width="80">Wo Rate</th>
	                        <th width="80">WO Amount</th>
	                        <th width="70">WO Date</th>
	                        <th width="80">WO Balance</th>
	                        <th width="150">Supplier</th>
	                        
	                        <!--840 pi details-->
	                        <th width="130">PI No</th>
	                        <th width="70">PI Date</th>
	                        <th width="150">Supplier</th>
	                        <th width="100">Item Category</th>
	                        <th width="70">UOM</th>
	                        <th width="80">PI Quantity</th>
	                        <th width="80">Unit Price</th>
	                        <th width="80">PI Value</th>
	                        <th width="70">Currency</th>
	                        <th width="100">Indentor Name</th>
	                        
	                        <!--550 lc details-->
	                        <th width="70">LC Date</th>
	                        <th width="120">LC No</th>
	                        <th width="80">Pay Term</th>
	                        <th width="50">Tenor</th>
	                        <th width="80">LC Amount</th>
	                        <th width="70">Shipment Date</th>
	                        <th width="80">Expiry Date</th>
	                        
	                        <!--1100 Invoice details-->
	                        <th width="150">Invoice No</th>
	                        <th width="70">Invoice Date</th>
	                        <th width="80">Incoterm</th>
	                        <th width="100">Incoterm Place</th>
	                        <th width="80">B/L No</th>
	                        <th width="70">BL Date</th>
	                        <th width="100">Mother Vassel</th>
	                        <th width="100">Feedar Vassel</th>
	                        <th width="100">Continer No</th>
	                        <th width="80">Pkg Qty</th>
	                        <th width="100">Doc Send to CNF</th>
	                        <th width="70">NN Doc Received Date</th>
	                        <th width="80">Bill Of Entry No</th>
	                        
	                        <!--290 Payment details-->
	                        <th width="70">Maturity Date</th>
	                        <th width="70">Maturity Month</th>
	                        <th width="70">Payment Date</th>
	                        <th width="80">Paid Amount</th>
	                        
	                        <!--340 MRR details-->
	                        <th width="80">MRR Qnty</th>
	                        <th width="80">MRR Value</th>
	                        <th width="80">Short Value</th>
	                        <th >Pipeline</th>
	                    </tr>
	                </thead>
	            </table>
	            <!-- <div style="width:5660px; max-height:300px; overflow-y:scroll" id="scroll_body"> -->
	            <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body">
	            	<tbody>
	            		<? 
	            		}
	            	 ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">

	                    	<? //------------------------------Requisition dtls start----------------------------------------- ?>
	                        <td width="30" align="center"><p><? echo $j; ?></p></td>
	                        <td width="50" align="center"><p><? echo $req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["requ_prefix_num"]; ?></p></td>
	                        <td width="70" align="center"><p>
	                        	<? 
	                        	$req_date=$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["requisition_date"];
	                        	if($req_date!="" && $req_date!="0000-00-00") echo change_date_format($req_date); 
	                        	?></p>
	                        </td>
	                        <td width="150"><p><? echo $store_array[$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["store_name"]]; ?></p></td>
	                        <td width="70" align="center"><p>
	                        	<? 
	                        	$delivry_date=$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["delivery_date"];
	                        	if($delivry_date!="" && $delivry_date!="0000-00-00") echo change_date_format($delivry_date); 
	                        	?></p>
	                        </td>
							<td width="100"><p><? echo $item_category[$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>

							<? $prod_id=$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["prod_id"]; ?>

	                        <td width="100"><p><? echo $item_group_array[$prod_data_array[$prod_id]["item_group_id"]]; ?></p></td>
	                        <td width="100"><p><? echo $prod_data_array[$prod_id]["sub_group_name"]; ?></p></td>
	                        <td width="80"><p><? echo $prod_data_array[$prod_id]["item_code"]; ?></p></td>
	                        <td width="150"><p>
	                        	<? 
	                        	if ($cbo_item_category_id==1) 
	                        	{
	                        		echo $req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_description"];
	                        	}
	                        	else
	                        	{
	                        		echo $prod_data_array[$prod_id]["item_description"]; 
	                        	}
	                        	?>
	                        </p></td>
	                        <td width="100"><p><? echo $req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["required_for"]; ?></p></td>
	                        <td width="70"><p>
			                        	<? 
			                        		echo $unit_of_measurement[$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["cons_uom"]];
		            						//echo $unit_of_measurement[$prod_data_array[$prod_id]["unit_of_measure"]]; 
		            					?>
	                        </p></td>
	                        <td width="100" align="right" title="<? echo $req_qty;?>"><p>
	                        	<? 
	                        		$req_qty=$req_data_array[$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["quantity"];
	                        		echo number_format($req_qty,2);
	                        		$total_req_qnty+=$req_qty; 
	                        		//echo "=================".$total_req_qnty;
	                        	?>
	                        </p></td>
	                        <? //------------------------------WO dtls start------------------------------------------- ?>
	             
							<td width="50" align="center"><p>
							<?
							$wo_no=implode(",",array_unique(explode(",",chop($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_number_prefix_num"]," , "))));
							echo $wo_no;
							?>
							</p></td>
							<td width="100"><p><? echo $item_category[$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>

							<? $wo_prod_id=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["prod_id"]; ?>

							<td width="100"><p><? echo $item_group_array[$prod_data_array[$wo_prod_id]["item_group_id"]]; ?></p></td> 
							<td width="100"><p><? echo $prod_data_array[$wo_prod_id]["sub_group_name"]; ?></p></td>
							<td width="80"><p><? echo $prod_data_array[$wo_prod_id]["item_code"]; ?></p></td>
							<td width="150"><p>
								<? 
									if ($cbo_item_category_id==1) 
									{
										echo $wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_description"];
									}
									else
									{
										echo $prod_data_array[$wo_prod_id]["item_description"]; 
									}
								?>
							</p></td>
							<td width="80" align="right"><p>
								<? 
								echo number_format($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"],2); 
								$total_wo_qnty+=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"]; 
								?>
							</p></td>
							<td width="80" align="right"><p>
								<? 
								$wo_rate=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"]/$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"]; 
								echo number_format($wo_rate,2);  
								?>
							</p></td>
							<td width="80" align="right"><p>
								<? 
								echo number_format($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"],2); 
								$total_wo_amt+=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"]; 
								?>
							</p></td>
							<td width="70" title="last wo date" align="center"><p>
								<? 
								$wo_po_date=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_date"];
								if($wo_po_date!="" && $wo_po_date!="0000-00-00") echo change_date_format($wo_po_date); 
								?>
							</p></td>
							<td width="80" align="right" title="Requisition Quantity-Wo Quantity"><p>
                            <?
                            if($req_qty !=0 || $req_qty !="")
                            {
								$wo_balance=$req_qty-$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"];
								echo number_format($wo_balance,2); 
							}
							$total_wo_balance+=$wo_balance;
							?>
                            </p></td>
							<td width="150"><p>
								<? $wo_supplier=$suplier_array[$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]]; 
								   echo $wo_supplier;
								?>
							</p></td>

							<? //------------------------------PI dtls start------------------------------------------- ?>
								<?
							if(chop($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_id"]," , ") !="")
							{
								?>
								<td width="130" align="center"><p>
								<?
								$pi_no=implode(",",array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_number"]," , "))));
								echo $pi_no;
								?>
								</p></td>
								<td width="70" align="center" title="Last PI Date"><p>
									<? 
									$pi_date_data=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_date"];
									if($pi_date_data!="" && $pi_date_data!="0000-00-00") echo change_date_format($pi_date_data); 
									?>
								</p></td>
								<td width="150"><p>
								<? 
								$pi_suplier=$suplier_array[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["supplier_id"]]; 
								echo $pi_suplier; 
								?> 
								</p></td>
								<td width="100"><p><? echo $item_category[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["item_category_id"]]; ?></p></td>
								<td width="70" align="center"><P><? echo $unit_of_measurement[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["uom"]]; ?></P></td>
								<td width="80" align="right"><p>
									<? 
									echo number_format($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["quantity"],2); 
									$total_pi_qnty+=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["quantity"]; 
									?>
								</p></td>
								<td width="80" align="right"><P>
									<? 
									$pi_rate=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["amount"]/$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["quantity"]; 
									echo number_format($pi_rate,2); 
									?>
								</P></td>
								<td width="80" align="right"><p>
									<? 
									echo number_format($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["amount"],2); 
									$total_pi_amt+=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["amount"]; 
									?>
								</p></td>
								<td width="70"><P><? echo $currency[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["currency_id"]]; ?></P></td>
								<td width="100"><p>
									<? 
									$pi_indore_name=$indentor_name_array[$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["intendor_name"]]; 
									echo $pi_indore_name; 
									?>
								</p></td>

								<? //------------------------------LC dtls start------------------------------------------- ?>


	                            <?
								$pi_id_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_id"]," , ")));

								foreach($pi_id_arr as $piID)
								{	
									if(!in_array($btb_data_array[$piID]["lc_id"],$btb_tem_lc_array))
									{
										$btb_tem_lc_array[$btb_data_array[$piID]["lc_id"]]=$btb_data_array[$piID]["lc_id"];
										$lc_date=$btb_data_array[$piID]["lc_date"];
										$lc_no.=$btb_data_array[$piID]["lc_number"].",";
										$lc_pay_term=$pay_term[$btb_data_array[$piID]["payterm_id"]];
										$lc_tenor+=$btb_data_array[$piID]["tenor"];
										$lc_amt+=$btb_data_array[$piID]["lc_value"];
										$lc_ship_date=$btb_data_array[$piID]["last_shipment_date"];
										$lc_expire_date=$btb_data_array[$piID]["lc_expiry_date"];
									}
									
									if(!in_array($inv_pay_data_array[$piID]["invoice_id"],$inv_temp_array))
									{
										$inv_temp_array[$inv_pay_data_array[$piID]["invoice_id"]]=$inv_pay_data_array[$piID]["invoice_id"];
										$invoice_id.=$inv_pay_data_array[$piID]["invoice_id"].",";
										$invoice_no.=$inv_pay_data_array[$piID]["invoice_no"].",";
										$invoice_date=$inv_pay_data_array[$piID]["invoice_date"];
										$inco_term=$inv_pay_data_array[$piID]["inco_term"];
										$inco_term_place=$inv_pay_data_array[$piID]["inco_term_place"];
										$bl_no=$inv_pay_data_array[$piID]["bill_no"];
										$bl_date=$inv_pay_data_array[$piID]["bill_date"];
										$mother_vasel=$inv_pay_data_array[$piID]["mother_vessel"];
										$feder_vasel=$inv_pay_data_array[$piID]["feeder_vessel"];
										$continer_no=$inv_pay_data_array[$piID]["container_no"];
										$pakag_qnty=$inv_pay_data_array[$piID]["pkg_quantity"];
										$doc_send_cnf=$inv_pay_data_array[$piID]["doc_to_cnf"];
										$bill_entry_no=$inv_pay_data_array[$piID]["bill_of_entry_no"];
										$maturity_date=$inv_pay_data_array[$piID]["maturity_date"];
										$maturity_month=$inv_pay_data_array[$piID]["maturity_date"];
										
										$pay_date=$inv_pay_data_array[$piID]["payment_date"];
										$pay_amt+=$inv_pay_data_array[$piID]["accepted_ammount"];
									}	
								}
								?>
								<td width="70" align="center"><P><? if($lc_date!="" && $lc_date!="0000-00-00") echo change_date_format($lc_date); ?></P></td>
								<td width="120"><P><? echo $lc_no=implode(",",array_unique(explode(",",chop($lc_no," , ")))); ?></P></td>
								<td width="80"><P><? echo $lc_pay_term; ?></P></td>
								<td width="50" align="center"><P><? echo $lc_tenor; ?></P></td>
								<td width="80" align="right"><P><? echo number_format($lc_amt,2); $total_lc_amt+=$lc_amt; ?></P></td>
								<td width="70" align="center" title="Last Ship Date"><P><? if($lc_ship_date!="" && $lc_ship_date!="0000-00-00") echo change_date_format($lc_ship_date); ?></P></td>
								<td width="80"  align="center" title="Last Expire Date"><P><? if($lc_expire_date!="" && $lc_expire_date!="0000-00-00") echo change_date_format($lc_expire_date); ?></P></td>

								<? //------------------------------Invoice dtls start------------------------------------------- ?>
								
								<td width="150"><P><? echo $invoice_no=implode(",",array_unique(explode(",",chop($invoice_no," , ")))); ?></P></td>
								<td width="70" align="center" title="Last Invoice Date"><P><? if($invoice_date!="" && $invoice_date!="0000-00-00") echo change_date_format($invoice_date); ?></P></td>
								<td width="80"><P><? echo $inco_term; ?></P></td>
								<td width="100"><P><? echo $inco_term_place; ?></P></td>
								<td width="80"><P><? echo $bl_no; ?></P></td>
								<td width="70" align="center"><P><? if($bl_date!="" && $bl_date!="0000-00-00") echo change_date_format($bl_date); ?></P></td>
								<td width="100"><P><? echo $mother_vasel; ?></P></td>
								<td width="100"><P><? echo $feder_vasel; ?></P></td>
								<td width="100"><P><? echo $continer_no; ?></P></td>
								<td width="80" align="right"><P><? echo number_format($pakag_qnty,2); $total_pkg_qnty+=$pakag_qnty; ?></P></td>
								<td width="100" align="center"><P><? if($doc_send_cnf!="" && $doc_send_cnf!="0000-00-00") echo change_date_format($doc_send_cnf); ?></P></td>
								<td width="70"></td>
								<td width="80"><P><? echo $bill_entry_no; ?></P></td>
								
								<td width="70" align="center"><P><? if($maturity_date!="" && $maturity_date!="0000-00-00") echo change_date_format($maturity_date); ?></P></td>
								<td width="70" align="center"><P><? if($maturity_month!="" && $maturity_month!="0000-00-00") echo change_date_format($maturity_month); ?></P></td>
								<td width="70" align="center"><P><? if($pay_date!="" && $pay_date!="0000-00-00") echo change_date_format($pay_date); ?></P></td>
								<td width="80" align="right"><P><? echo number_format($pay_amt,2); $total_pay_amt+=$pay_amt; ?></P></td>
								<?
							}
							else
							{
								?>
								<td width="130"></td>
								<td width="70"></td>
								<td width="150"></td>
								<td width="100"></td>
								<td width="70"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="100"></td>
	                            
								<td width="70"></td>
								<td width="120"></td>
								<td width="80"></td>
								<td width="50"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="80"></td>
								
								<td width="150"></td>
								<td width="70"></td>
								<td width="80"></td>
								<td width="100"></td>
								<td width="80"></td>
								<td width="70"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="100"></td>
								<td width="80"></td>
								<td width="100"></td>
								<td width="70"></td>
								<td width="80"></td>
								
								<td width="70"></td>
								<td width="70"></td>
								<td width="70"></td>
								<td width="80"></td>
								<?
							}

							//---------------------------------Store Details starts----------------------------------

							$pi_id=chop($pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["pi_id"],",");
							if ($pi_id=='') {
								$pi_id=chop($wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["wo_mst_id"],",");
							}

							if ($cbo_item_category_id==1) 
                        	{
                        		$item_desc=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["item_description"];
                        		$receive_basis=$recv_data_array[$pi_id][$item_desc]["receive_basis"];
                        		$prod_id=$recv_data_array[$pi_id][$item_desc]["prod_id"];
                        	}
                        	else
                        	{
                        		$receive_basis=$recv_data_array[$pi_id][$row[csf("prod_id")]]["receive_basis"];
                        		$prod_id=$row[csf("prod_id")];
                        	}

							$data=$pi_id."**".$receive_basis;
							?>
	                        <td width="80" align="right"><p><a href="##" onClick="openmypage_popup('<? echo $data; ?>','<? echo $prod_id; ?>','Receive Info','receive_popup');" > 
	                        	<? 
	                        		if ($cbo_item_category_id==1)
	                        		{
	                        			$rcv_qnty=$recv_data_array[$pi_id][$item_desc]["rcv_qnty"];
	                        		}
	                        		else
	                        		{
	                        			$rcv_qnty=$recv_data_array[$pi_id][$row[csf("prod_id")]]["rcv_qnty"];
	                        		}
	                        		echo number_format($rcv_qnty,2); 
	                        		$total_mrr_qnty+=$rcv_qnty; 
	                        	?> 
	                        </a></p>
	                        </td>
	                        <td width="80" align="right"><p>
	                        	<? 
	                        		if ($cbo_item_category_id==1)
	                        		{
	                        			$rcv_value=$recv_data_array[$pi_id][$item_desc]["rcv_amt"];
	                        		}
	                        		else
	                        		{
	                        			$rcv_value=$recv_data_array[$pi_id][$row[csf("prod_id")]]["rcv_amt"];
	                        		}
	                        		echo number_format($rcv_value,2); 
	                        		$total_mrr_amt+=$rcv_value; 
	                        	?></p>
	                        </td>
	                        <td align="right" title="Wo Value-Receive Value" width="80"><p>
	                        	<? 
	                        		$short_value=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["amount"]-$rcv_value;
	                        		echo number_format($short_value,2);  
	                        		$total_short_amt+=$short_value; 
	                        	?></p>
	                        </td>
	                        <?
	                        $pipe_pi_qnty=$pi_data_arr[$row[csf("wo_number")]][$row[csf("wo_dtls_id")]][$row[csf("prod_id")]]["quantity"];
	                       	$pipe_wo_qnty=$wo_data_array[$row[csf("wo_number")]][$row[csf("requisition_dtls_id")]][$row[csf("prod_id")]]["supplier_order_quantity"];

							if($pipe_pi_qnty!="" && $pipe_wo_qnty =="")
							{
								$pipe_line=$pipe_pi_qnty-$rcv_qnty;
							}
							else
							{
								$pipe_line=$pipe_wo_qnty-$rcv_qnty;
							}
							?>
	                        <td  align="right"><P> 
	                        	<? 
	                        	echo number_format($pipe_line,2); 
	                        	$total_pipe_line+=$pipe_line;
	                        	?></P>
	                        </td>
	                    </tr>
	                    <?
						$i++;
						$pipe_wo_qnty=0;


						
						$j++;
					}
					?>
	                </tbody>
	            </table>
	            <!-- </div> -->
	            <table width="5660" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_footer">
	            	<tfoot>
	                    <tr>
	                        <!--1210 requisition details-->
	                        <th width="30"></th>
	                        <th width="50"></th>
	                        <th width="70"></th>
	                        <th width="150"></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="100"></th> 
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="150"></th>
	                        <th width="100"></th>
	                        <th width="70"> </th>
	                        <th width="100" id="value_total_req_qnty" align="right"><? echo number_format($total_req_qnty,0); ?> </th>
	                        
	                        <!--1110 wo details-->
	                        <th width="50"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="150"></th>
	                        <th width="80" id="value_total_wo_qnty" align="right"><? echo number_format($total_wo_qnty,0); ?></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_wo_amt" align="right"><? echo number_format($total_wo_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_wo_balance" align="right"><? echo number_format($total_wo_balanc,2); ?></th>
	                        <th width="150"></th>
	                        
	                        <!--840 pi details-->
	                        <th width="130"></th>
	                        <th width="70"></th>
	                        <th width="150"></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_pi_qnty" align="right"><? echo number_format($total_pi_qnty,0); ?></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_pi_amt" align="right"><? echo number_format($total_pi_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        
	                        <!--550 lc details-->
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="80" id="value_total_lc_amt" align="right"><? echo number_format($total_lc_amt,2); ?></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        
	                        <!--1100 Invoice details-->
	                        <th width="150"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        <th width="100"></th>
	                        <th width="80"></th>
	                        <th width="70"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="100"></th>
	                        <th width="80" id="value_total_pkg_qnty" align="right"><? echo number_format($total_pkg_qnty,0); ?></th>
	                        <th width="100"></th>
	                        <th width="70"></th>
	                        <th width="80"></th>
	                        
	                        <!--290 Payment details-->
	                        <th width="70"></th>
	                        <th width="70"></th>
	                        <th width="70"></th>
	                        <th width="80" id="value_total_pay_amt" align="right"><? echo number_format($total_pay_amt,2); ?></th>
	                        
	                        <!--340 MRR details-->
	                        <th width="80" id="value_total_mrr_qnty" align="right"><? echo number_format($total_mrr_qnty,0); ?></th>
	                        <th width="80" id="value_total_mrr_amt" align="right"><? echo number_format($total_mrr_amt,2); ?></th>
	                        <th width="80" id="value_total_short_amt" align="right"><? echo number_format($total_short_amt,2); ?></th>
	                        <th id="value_total_pipe_line" align="right"><? echo number_format($total_pipe_line,2); ?></th>
	                    </tr>
	                </tfoot>
	            </table>
	        </fieldset>

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
	echo "$total_data****$filename";
	exit();
}

if($action=="receive_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$wo_pi_req=str_replace("'","",$wo_pi_req);
	$wo_pi_req_arr=explode("**",$wo_pi_req);
	$wo_pi_req_no=$wo_pi_req_arr[0];
	$rcv_basis=$wo_pi_req_arr[1];
	?>
	<script>
	function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
	</script>	
	<p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
    <br />
    <div id="report_container" align="center" style="width:700px">
	<fieldset style="width:700px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="700" cellpadding="0" cellspacing="0">
             	<thead>
                    <th width="50">Product Id</th>
                    <th width="140">Item Category</th>
                    <th width="100">Item Group</th>
                    <th width="100">Item Sub-group</th>
                    <th width="200">Item Description</th>
                    <th>Item Size</th>
                </thead>
                <tbody>
                <?
				$item_group_arr=return_library_array("select id, item_name from  lib_item_group","id","item_name");
				$sql="SELECT id,item_category_id,item_group_id,sub_group_name, case when item_category_id =1 then product_name_details else item_description end as item_description, item_size from  product_details_master where id=$prod_id";
				$result=sql_select($sql);
				foreach($result as $row)  
				{
					?>
					<tr>
						<td align="center"><? echo $row[csf('id')]; ?></td>
						<td><p><? echo $item_category[$row[csf('item_category_id')]]; ?></p></td>
						<td><? echo $item_group_arr[$row[csf('item_group_id')]]; ?></td>
						<td><? echo $row[csf('sub_group_name')]; ?></td>
						<td><? echo $row[csf('item_description')]; ?></td>
                        <td><? echo $row[csf('item_size')]; ?></td>
					</tr>
					<?
				}
				?>
                </tbody>   
            </table>
            <br />
            <table class="rpt_table" border="1" rules="all" width="700" cellpadding="0" cellspacing="0">
                <thead>
                    <th width="40">SL</th>
                    <th width="120">MRR No.</th>
                    <th width="80">Receive Date</th>
                    <th width="60">UOM</th>
                    <th width="70">Qty</th>
                    <th width="70">Rate</th>
                    <th width="80">Value</th>
                    <th>Remarks</th>
                    
                </thead>
                <tbody>
                <? 
                    $i=1; $total_qty=0; 
                    $sql_rcv="select a.recv_number, a.receive_date, a.remarks, b.cons_uom, b.order_qnty as qnty, b.order_rate, b.order_amount as rcv_amt 
					from inv_receive_master a, inv_transaction b 
					where a.id=b.mst_id and a.receive_basis=$rcv_basis and a.booking_id in($wo_pi_req_no) and b.prod_id=$prod_id and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
					//echo $sql_rcv;
                    $result_rcv=sql_select($sql_rcv);
                    foreach($result_rcv as $row)  
                    {
                       
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td><? echo $i; ?></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
							<td align="right"><? echo number_format($row[csf('qnty')],2);  $total_qty += $row[csf('qnty')]; ?></td>
                            <td align="right"><? echo number_format($row[csf('order_rate')],2);  ?></td>
							<td align="right"><? echo number_format($row[csf('rcv_amt')],2); $total_amt+=$row[csf('rcv_amt')]; ?></td>
                            <td><? echo $row[csf('remarks')]; ?></td>
						</tr>
						<?
                        $i++;
					}
					?>
                </tbody>
                
                 <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="4" align="right">Total</td>
                        <td align="right"><? echo number_format($total_qty,2); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($total_amt,2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>
	<?
    exit();

}

if($action=="pipe_line_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$prod_id=str_replace("'","",$prod_id);
	$wo_pi_req=str_replace("'","",$wo_pi_req);
	$wo_pi_req_arr=explode("**",$wo_pi_req);
	$wo_pi_req_no=$wo_pi_req_arr[0];
	$rcv_basis=$wo_pi_req_arr[1];
	
	$product_sql=sql_select("select id,  item_category_id, item_group_id, sub_group_name, item_description, product_name_details, item_size from product_details_master where id=$prod_id");
	$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	
	?>
	<script>
	function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
	</script>	
	<p><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></p>
    <br />
    <div id="report_container" style="width:700px">
    <table width="630" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="50">Product Id</th>
                <th width="120">Item Category</th>
                <th width="100">Item Group</th>
                <th width="100">Item Sub-group</th>
                <th width="150">Item Description</th>
                <th >Item Size</th>
            </tr>
        </thead>
        <tbody>
        	<tr>
            	<td align="center"><p><? echo $product_sql[0][csf("id")]; ?>&nbsp;</p></td>
                <td><p><? echo $item_category[$product_sql[0][csf("item_category_id")]]; ?>&nbsp;</p></td>
                <td><p><? echo $item_group_name; ?>&nbsp;</p></td>
                <td><p><? echo $product_sql[0][csf("sub_group_name")]; ?>&nbsp;</p></td>
                <td><p><? echo $product_sql[0][csf("item_description")]; ?>&nbsp;</p></td>
                <td><p><? echo $product_sql[0][csf("item_size")]; ?>&nbsp;</p></td>
            </tr>
        </tbody>
    </table>
    <br />
    <table width="680" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="70">WO/PI Date</th>
                <th width="100">WO/PI No</th>
                <th width="30">Type</th>
                <th width="70">Pay Mode</th>
                <th width="70">UOM</th>
                <th width="80">WO/PI Qty.</th>
                <th width="80">Rcv. Qnty</th>
                <th width="80">Balance</th>
                <th >Remarks</th>
            </tr>
        </thead>
        <tbody>
		<?
		
		$rcv_qnty_array=return_library_array("select a.booking_id, sum(b.cons_quantity) as cons_quantity from  inv_receive_master a,  inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2) and b.transaction_type=1 and b.prod_id=$prod_id and b.status_active=1 group by a.booking_id","booking_id","cons_quantity");
		
		if($rcv_basis==1)
		{
			$details_sql="select b.id as wo_po_id, b.pi_number as wo_po_no, b.pi_date as wo_po_date, 0 as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.quantity) as wo_po_qnty, 2 as type 
			from com_pi_master_details b, com_pi_item_details c 
			where b.id=c.pi_id and b.id in($wo_pi_req_no) and c.item_prod_id=$prod_id and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			group by b.id, b.pi_number, b.pi_date";
		}
		else if($rcv_basis==2)
		{
			$details_sql="select b.id as wo_po_id, b.wo_number as wo_po_no, b.wo_date as wo_po_date, b.pay_mode as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.supplier_order_quantity) as wo_po_qnty, 1 as type 
			from wo_non_order_info_mst b,  wo_non_order_info_dtls c 
			where b.id=c.mst_id and b.id in($wo_pi_req_no) and c.item_id=$prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and b.pay_mode<>2 and c.is_deleted=0 
			group by b.id, b.wo_number, b.wo_date, b.pay_mode";
		}
		
		
		/*$details_sql="select b.id as wo_po_id, b.wo_number as wo_po_no, b.wo_date as wo_po_date, b.pay_mode as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.supplier_order_quantity) as wo_po_qnty, 1 as type from wo_non_order_info_mst b,  wo_non_order_info_dtls c where b.id=c.mst_id and c.item_id=$prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and b.item_category in (5,6,7) and b.pay_mode<>2 and c.is_deleted=0 group by b.id, b.wo_number, b.wo_date, b.pay_mode
		union all
		select b.id as wo_po_id, b.pi_number as wo_po_no, b.pi_date as wo_po_date, 0 as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.quantity) as wo_po_qnty, 2 as type from com_pi_master_details b, com_pi_item_details c where b.id=c.pi_id and b.item_category_id in (5,6,7) and c.item_prod_id=$prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, b.pi_number, b.pi_date";*/
		//echo $details_sql;
		$sql_result=sql_select($details_sql);
		$i=1;
		foreach($sql_result as $row)
		{
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$rcv_qnty=$rcv_qnty_array[$row[csf("wo_po_id")]];
			$balance=$row[csf("wo_po_qnty")]-$rcv_qnty;
			if($row[csf("type")]==1) $type="WO"; else $type="PI";
			
        	?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
                <td><p><? if($row[csf("wo_po_date")]!="" && $row[csf("wo_po_date")]!="0000-00-00") echo change_date_format($row[csf("wo_po_date")]); ?>&nbsp;</p></td>
                <td><p><? echo $row[csf("wo_po_no")]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $type; ?>&nbsp;</p></td>
                <td><p><? echo $pay_mode[$row[csf("wo_po_mode")]]; ?>&nbsp;</p></td>
                <td><p><? echo $unit_of_measurement[$row[csf("wo_po_uom")]]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo number_format($row[csf("wo_po_qnty")],0); $total_wo_qnty+=$row[csf("wo_po_qnty")]; ?></p></td>
                <td align="right"><p><? echo number_format($rcv_qnty,0); $total_rcv_qnty+=$rcv_qnty; ?></p></td>
                <td align="right"><p><? echo number_format($balance,0); $total_bal_qnty+=$balance;  ?></p></td>
                <td><p><? echo $row[csf("")]; ?>&nbsp;</p></td>
            </tr>
            <?
			$i++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
            	<th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >Total</th>
                <th ><? echo number_format($total_wo_qnty,0); ?></th>
                <th ><? echo number_format($total_rcv_qnty,0); ?></th>
                <th ><? echo number_format($total_bal_qnty,0); ?></th>
                <th >&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    </div>
    <?
}

disconnect($con);
?>
