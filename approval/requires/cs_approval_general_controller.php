<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if ($action=="load_drop_down_item_category_new_user")
{
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd WHERE id=$data");
	$item_cate_id = $userCredential[0][csf('item_cate_id')];
	$permitted_item_category="";
	if($item_cate_id != "")	$permitted_item_category=$item_cate_id;

	echo create_drop_down( "cbo_item_category_id", 160, $item_category,"", 1, "-- Select Category --", $selected,"",0,$permitted_item_category,"","","1,2,3,12,13,14");
	exit();
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sequence_no='';
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$cbo_cs_year=str_replace("'","",$cbo_cs_year);
	$txt_cs_no=str_replace("'","",$txt_cs_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if ($txt_alter_user_id !="") $user_id=$txt_alter_user_id;

	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd WHERE id=$user_id");
    $item_cate_id = $userCredential[0][csf('item_cate_id')];
    $user_crediatial_item_cat_cond ="";
    if($item_cate_id != "") $user_crediatial_item_cat_cond = " and b.item_category_id in ($item_cate_id)";

	$approval_type=str_replace("'","",$cbo_approval_type);
	if ($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}

	$cs_date_cond=$cs_no_cond=$item_category_cond='';
	if ($cbo_item_category_id != 0) $item_category_cond=" and b.item_category_id=$cbo_item_category_id";
	if ($txt_cs_no != '') $cs_no_cond=" and a.sys_number_prefix_num=$txt_cs_no";

	if ($approval_type == 1) $approved_cond=" and a.approved in (1)";
	else $approved_cond=" and a.approved in (0,2)";

	if ($txt_date_from != '' && $txt_date_to != '')
	{
		if($db_type==0)
		{
			$txt_date_from = date("Y-m-d", strtotime($txt_date_from));
			$txt_date_to = date("Y-m-d", strtotime($txt_date_to));
			$cs_date_cond = " and a.cs_date between '".$txt_date_from."' and '".$txt_date_to."'";
		}
		else
		{
			$txt_date_from = date("d-M-Y", strtotime($txt_date_from));
			$txt_date_to = date("d-M-Y", strtotime($txt_date_to));
			$cs_date_cond = " and a.cs_date between '".$txt_date_from."' and '".$txt_date_to."'";
		}	
	}


	$cs_year_cond='';
	if($db_type==0)
	{
		if ($cbo_cs_year != 0) $cs_year_cond= " and year(a.insert_date)=$cbo_cs_year";
		$year_cond_prefix= "year(a.insert_date)";
		$year_field="YEAR(a.insert_date) as CS_YEAR";
	}
	else if($db_type==2)
	{
		if ($cbo_cs_year != 0) $cs_year_cond= " and TO_CHAR(a.insert_date,'YYYY')=$cbo_cs_year";
		$year_cond_prefix= "TO_CHAR(a.insert_date,'YYYY')";
		$year_field="to_char(a.insert_date,'YYYY') as CS_YEAR";
	}

	$brandArr = return_library_array("select id,brand_name from lib_buyer_brand ","id","brand_name");
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$itemGroup_library=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$menu_id and is_deleted = 0");
	//echo "select sequence_no from electronic_approval_setup where page_id=$menu_id and user_id=$user_id and is_deleted=0"."**".$user_sequence_no."**".$min_sequence_no."**".$menu_id;
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority.</font>";die;
	}

	if($previous_approved==1 && $approval_type==1)	//approval process with prevous approve start
	{
		$sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";
	
		$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
		from req_comparative_mst a, req_comparative_dtls b, approval_history c 
		where a.id=b.mst_id and a.id=c.mst_id and a.entry_form=481 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=49 and c.current_approval_status=1 $item_category_cond $user_crediatial_item_cat_cond $cs_no_cond $cs_date_cond $cs_year_cond $sequence_no_cond 
		group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin, c.id, c.approved_date
		order by a.id";
		//echo "$sql";
	}

	else if($approval_type==0)	// unapproval process start
	{

		if($user_sequence_no==$min_sequence_no)  // First user
		{

		 	$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN 
		 	from req_comparative_mst a, req_comparative_dtls b 
		 	where a.id=b.mst_id and a.entry_form=481 and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_category_cond $user_crediatial_item_cat_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 	group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin
			order by a.id";
			//echo $sql;
		}
		else // Next user
		{
			$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");

			if($sequence_no=="")  // bypass if previous user Yes
			{
				if($db_type==0)
				{
					
					$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup wherepage_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];
					
					$req_comp_id=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no in ($sequence_no_by) and c.entry_form=49 and c.current_approval_status=1 $cs_date_cond","req_comp_id");
					$req_comp_id=implode(",",array_unique(explode(",",$req_comp_id)));
					
					$req_comp_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no=$user_sequence_no and c.entry_form=49 and c.current_approval_status=1 ","req_comp_id");
					$req_comp_id_app_byuser=implode(",",array_unique(explode(",",$req_comp_id_app_byuser)));
				}
				else
				{

					$seqSql="select group_concat(sequence_no) as sequence_no_by  from electronic_approval_setup wherepage_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];
					
					$req_comp_id=return_field_value("group_concat(distinct(b.mst_id)) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no in ($sequence_no_by) and c.entry_form=49 and c.current_approval_status=1 $cs_date_cond ","req_comp_id");
					$req_comp_id=implode(",",array_unique(explode(",",$req_comp_id)));
					
					$req_comp_id_app_byuser=return_field_value("group_concat(distinct(b.mst_id)) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no=$user_sequence_no and c.entry_form=49 and c.current_approval_status=1 ","req_comp_id");
					$req_comp_id_app_byuser=implode(",",array_unique(explode(",",$req_comp_id_app_byuser)));
				}

				$result=array_diff(explode(',',$req_comp_id),explode(',',$req_comp_id_app_byuser));
				$req_comp_id=implode(",",$result);

				if($req_comp_id!="")
				{					
					$sql=" SELECT x.* from  (SELECT DISTINCT (a.id) as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN
					from req_comparative_mst a, req_comparative_dtls b
					where a.id=b.mst_id and a.entry_form=481 and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_category_cond $user_crediatial_item_cat_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 			group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin
					UNION ALL
					SELECT DISTINCT (a.id) as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN 
					from req_comparative_mst a, req_comparative_dtls b
					where a.id=b.mst_id and a.entry_form=481 and a.approved in(1,3) and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($req_comp_id) $item_category_cond $user_crediatial_item_cat_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 			group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin) x  order by x.ID";
					//echo $sql;
				}
				else
				{ 
					$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN 
					from req_comparative_mst a, req_comparative_dtls b
					where a.id=b.mst_id and a.entry_form=481 and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_category_cond $user_crediatial_item_cat_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 			group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin
		 			order by a.id";
					//echo $sql;
				}
				//echo $sql;
			}			
			else // if previous user bypass No 
			{
				$user_sequence_no=$user_sequence_no-1;
				if($sequence_no==$user_sequence_no) 
				{
					$sequence_no_by_pass=$sequence_no;
					$sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
				}
				else
				{
					if($db_type==0) 
					{
						$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
					}
					else if($db_type==2) 
					{
						$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
					}
					
					if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
					else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
				}

				$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
				from req_comparative_mst a, req_comparative_dtls b, approval_history c 
				where a.id=b.mst_id and a.id=c.mst_id and a.entry_form=481 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=49 and c.current_approval_status=1 $item_category_cond $user_crediatial_item_cat_cond $cs_no_cond $cs_date_cond $cs_year_cond $sequence_no_cond 
				group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin, c.id, c.approved_date
				order by a.id";
				//echo $sql;
			}
		}	

	}
	else // approval process start
	{
		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";

		$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
		from req_comparative_mst a, req_comparative_dtls b, approval_history c 
		where a.id=b.mst_id and a.id=c.mst_id and a.entry_form=481 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=49 and c.current_approval_status=1 $item_category_cond $user_crediatial_item_cat_cond $cs_no_cond $cs_date_cond $cs_year_cond $sequence_no_cond 
		group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin, c.id, c.approved_date
		order by a.id";
	}
	// echo $sql;
	
	$sql_res=sql_select($sql);

	foreach($sql_res as $row)
	{
		$mst_id.=$row["ID"].",";
		if ($row["PROD_ID"] != '') $selected_prod_id.=$row["PROD_ID"].",";
		$company_arr[$row["CS_NUMBER"]]=$row["COMPANY_ID"];
		$supplier_arr[$row["CS_NUMBER"]]=$row["SUPP_ID"];		
		$dtls_row_arr[$row["CS_NUMBER"]].=$row["DTLS_ID"].',';
		$cs_number_arr[$row["CS_NUMBER"]].=$row["CS_NUMBER"].',';
		$rowspan_arr[$row["CS_NUMBER"]]++;
	}
	$first_cs_count_row=count(explode(',',rtrim(reset($dtls_row_arr),',')));
	$supplierArr=array_unique(explode(",", implode(",", $supplier_arr)));
	$supplier_width=count($supplierArr)*240;
	//echo '<pre>';print_r($supplierArr);

	$mst_ids = implode(',', array_flip(array_flip(explode(',', rtrim($mst_id,',')))));
	$selected_prod_id=chop($selected_prod_id,",");

	$item_des_sql="SELECT b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.order_uom as ORDER_UOM, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_code as ITEM_CODE, b.item_size as ITEM_SIZE
	from product_details_master b
	where b.id in($selected_prod_id) and b.is_deleted=0 and b.status_active=1";
	$item_des_sql_result=sql_select($item_des_sql);
	$item_des_sql_arr=array();
	foreach($item_des_sql_result as $row)
	{
		$item_des_sql_arr[$row["PROD_ID"]]['PROD_ID']=$row["PROD_ID"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_DESCRIPTION']=$row["ITEM_DESCRIPTION"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_CODE']=$row["ITEM_CODE"];
		$item_des_sql_arr[$row["PROD_ID"]]['ORDER_UOM']=$row["ORDER_UOM"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_SIZE']=$row["ITEM_SIZE"];
	}
	//echo '<pre>';print_r($item_des_sql_arr);
	
	$supp_dtls="SELECT id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, supp_type as SUPP_TYPE, prod_id as PROD_ID, quoted_price as QUOTED_PRICE, neg_price as NEG_PRICE, con_price as CON_PRICE, last_approval_rate as LAST_APPROVAL_RATE, brand as BRAND, model as MODEL, origin as ORIGIN, approved as APPROVED from req_comparative_supp_dtls where mst_id in($mst_ids) and is_deleted=0 and status_active=1 order by id asc";
	$supp_dtls_res=sql_select($supp_dtls);
	$supp_dtls_arr=array();
	$supp_comp_dtls_arr=array();
	$supp_id_dtls_arr=array();
	$supp_iddtls_array=array();
	foreach ($supp_dtls_res as $row) {
		if ($row["SUPP_TYPE"] == 1)
		{
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['QUOTED_PRICE']=$row["QUOTED_PRICE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['LAST_APPROVAL_RATE']=$row["LAST_APPROVAL_RATE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['NEG_PRICE']=$row["NEG_PRICE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['BRAND']=$row["BRAND"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['MODEL']=$row["MODEL"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['ORIGIN']=$row["ORIGIN"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['SUPP_TYPE']=$row["SUPP_TYPE"];
			if ($row["APPROVED"] == 1) 
			{
				$supp_comp_dtls_arr[$row["DTLS_ID"]]['ID'].=$row["ID"].',';
				$supp_comp_dtls_arr[$row["DTLS_ID"]]['NAME'].=$supplier_library[$row["SUPP_ID"]].',';
				$supp_id_dtls_arr[$row["DTLS_ID"]].=$row["SUPP_ID"].',';
			}

			if ($row["CON_PRICE"]  != ""){
				$supp_iddtls_array[$row["DTLS_ID"]]['supp_id'].=$row["SUPP_ID"].',';
				$supp_iddtls_array[$row["DTLS_ID"]]['supp_name'].=$supplier_library[$row["SUPP_ID"]].',';
				$supp_iddtls_array[$row["DTLS_ID"]]['id'].=$row["ID"].',';
			}
		}
	}
	//echo '<pre>';print_r($supp_iddtls_array);die;
	$tbl_width=1280+$supplier_width*1;
	?>
	<script>

		function openmypage_supplier(sl, row_num, cs_number, dtls_id)
		{
			var txt_supp_comp_dtlsid= $('#txt_supp_comp_dtlsid_'+sl+'_'+row_num).val();
			var txt_dtlsid= $('#txt_dtlsid_'+sl+'_'+row_num).val();
			var page_link="requires/cs_approval_general_controller.php?action=supplier_popup&txt_supp_comp_dtlsid="+txt_supp_comp_dtlsid+"&txt_dtlsid="+txt_dtlsid+"&dtls_id="+dtls_id;

			var title="Supplier Info";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{

				var theemailID=this.contentDoc.getElementById("hid_supp_comp_dtlsid").value;	
				var theemailVAL=this.contentDoc.getElementById("hid_supp_comp_name").value;
				var theemailDtlsID=this.contentDoc.getElementById("hid_dtlsid").value;
				var theemailsuppID=this.contentDoc.getElementById("hid_supp_id").value;
				$('#txt_supp_comp_dtlsid_'+sl+'_'+row_num).val(theemailID);
				$('#txt_supp_comp_name_'+sl+'_'+row_num).val(theemailVAL);
				if (theemailDtlsID != ""){
					$('#txt_dtlsid_'+sl+'_'+row_num).val(theemailDtlsID);
				}				

				
				if (theemailsuppID != '')
				{
					var type=1;
					var supp_ids='';
					var supp_arr=theemailsuppID.split(',');
					var supp_arr_length=supp_arr.length;					
					for (s=0; s<supp_arr_length; s++)
					{
						var sid = supp_arr[s];
						if (supp_ids=="") supp_ids= sid; 
						else supp_ids +=','+sid;
					}
					$('#txt_supp_id_'+sl+'_'+row_num).val(supp_ids);
					get_php_form_data( sl+"**"+row_num+"**"+theemailDtlsID+"**"+supp_ids+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_general_controller" );				
				}
				else
				{
					var type=2;
					var txt_dtlsid=$('#txt_dtlsid_'+sl+'_'+row_num).val();
					var supp_ids=$('#txt_supp_id_'+sl+'_'+row_num).val();
					get_php_form_data( sl+"**"+row_num+"**"+txt_dtlsid+"**"+supp_ids+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_general_controller" );
				}	


				
				var tot_row=$('#tbl_cs_list tbody tr').length;

				if (theemailID != '')
				{
					if ($('#booking_no_'+sl+'_'+row_num).val() == cs_number){
						$('#tbl_'+sl).prop("checked", true);
					}
		
				}
			}
		}

		
		function fn_cs_check()
		{
			if ($(copy_cs).is(":checked"))
			{
				var type=1;
				var tbl_row_count = $("#first_cs_number_count").val();
				$('#tbl_1_1').prop("checked", true);

				var txtSuppCompName=$("#txt_supp_comp_name_1_1").val();		
				var txtSuppCompDtlsid=$("#txt_supp_comp_dtlsid_1_1").val();
				var txtDtlsid=$("#txt_dtlsid_1_1").val();
				var txtSuppid=$("#txt_supp_id_1_1").val();
				get_php_form_data( txtDtlsid+"**"+txtSuppid+"**"+txtSuppCompDtlsid+"**"+tbl_row_count+"**"+type, "populate_data_from_copy_cs_check", "requires/cs_approval_general_controller" );

				var sl=1;
				for( var i = 2; i <= tbl_row_count; i++ ) 
				{
					var row_num=i;
					var txtDtlsidWithoutFirstRow=$("#txt_dtlsid_1_"+i).val();
					var txtSuppidWithoutFirstRow=$("#txt_supp_id_1_"+i).val();
					get_php_form_data( sl+"**"+row_num+"**"+txtDtlsidWithoutFirstRow+"**"+txtSuppidWithoutFirstRow+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_general_controller" );
				}
			}
			else
			{
				var tbl_row_count = $("#first_cs_number_count").val();
				var sl=1;
				for( var i = 1; i <= tbl_row_count; i++ ) 
				{
					var type=2;
					var row_num=i;
					var txtDtlsid=$("#txt_dtlsid_1_"+i).val();
					var txtSuppid=$("#txt_supp_id_1_"+i).val();
					get_php_form_data( sl+"**"+row_num+"**"+txtDtlsid+"**"+txtSuppid+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_general_controller" );
					$("#txt_supp_comp_name_1_"+i).val('');
					$("#txt_supp_comp_dtlsid_1_"+i).val('');
					$("#txt_dtlsid_1_"+i).val('');
					$("#txt_supp_id_1_"+i).val('');
				}

				for( var i = 1; i <= tbl_row_count; i++ )
				{
					$('#tbl_'+i).prop("checked", false);			
				}				
			}	
		}
	</script>
	<style type="text/css">
		.wrd_brk{.word-break: break-all; word-wrap: break-word;}
	</style>
	<div id="xyz">
    <form name="csApproval_2" id="csApproval_2">
    	<fieldset style="width: <?= $tbl_width; ?>px; margin-top:10px;">
        <legend style="width: <?= $tbl_width; ?>px;">CS Approval [General]</legend>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $tbl_width; ?>" class="rpt_table" id="tbl_cs_list">
        	<thead>
        		<tr>
        			<th colspan="11">&nbsp;</th>
        			<? if ( $approval_type==1 ) { ?>
        			<th width="150"></th>
        			<? } else { ?>
        			<th width="150"><input type="checkbox" id="copy_cs" onchange="fn_cs_check()"/>&nbsp;&nbsp;Copy CS</th>
        			<? } ?>
        			<th width="80">&nbsp;</th>
        			<?
					foreach($supplierArr as $supp_id)
					{
						?>
	                	<th colspan="3" width="240">&nbsp;</th>
	                	<?
	                }
	                ?>
        			<th colspan="2">&nbsp;</th> 
        		</tr>
        		<tr>
        			<th width="30">&nbsp;</th>
	                <th width="30">SL</th>
	                <th width="80">CS No</th>
	                <th width="60">CS Year</th>
	                <th width="80">CS Date</th>
	                <th width="100">Item Category</th>
	                <th width="100">Items Group</th>
	                <th width="100">Items Code</th>
	                <th width="150">Items Description</th>
	               	<th width="80">Req. Qty.</th>
	               	<th width="60">UOM</th>
	                <th width="150" style="color: blue;">Supplier</th>
	                <th width="80">Status</th>
	                <?
					foreach($supplierArr as $supp_id)
					{
						?>
	                	<th colspan="3" width="240"><?= $supplier_library[$supp_id]; ?></th>
	                	<?
	                }
	                ?>                
	                <th width="80">Approved Date</th>
	               	<th width="100">CS Insert By</th>
        		</tr>      		
        	</thead>
        	<tbody>
                <?
                $row_num=0;
                $sl=0;
                //$del_row=1;
                foreach ($sql_res as $row) 
                {
                	$dtls_id=$row['DTLS_ID'];
                	$supp_comp_dtls_ids = rtrim($supp_comp_dtls_arr[$row["DTLS_ID"]]['ID'],',');
                	$supp_comp_name = rtrim($supp_comp_dtls_arr[$row["DTLS_ID"]]['NAME'],',');
                	$suppID = rtrim($supp_id_dtls_arr[$row["DTLS_ID"]],',');
                	$count_cs_dtls_row=$row['DTLS_ID'];

                	$ex_prod_id=explode(',', $row['PROD_ID']);
                	foreach ($ex_prod_id as $prod_id) {
                		$item_description=$item_des_sql_arr[$prod_id]['ITEM_DESCRIPTION'];
                		$item_code=$item_des_sql_arr[$prod_id]['ITEM_CODE'];
                		$order_uom=$item_des_sql_arr[$prod_id]['ORDER_UOM'];
                	}                	
                	
					//if ($sl%2==0) $bgcolor="#E9F3FF"; 
					//else $bgcolor="#FFFFFF";
					$bgcolor="#E9F3FF";
					//if ($row_num==0) $row_num=1;
                	?>    
					<tr bgcolor="<?= $bgcolor; ?>" id="tr_<?= $row_num; ?>" align="center">
						<? 
						$row_num++;
						if ($check_cs_number[$row['CS_NUMBER']]=="")
						{
							$sl++;
							?>
							<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]*4; ?>" width="30" align="center" style="vertical-align:top;"><input type="checkbox" id="tbl_<?= $sl; ?>" name="tbl[]" value="<?= $sl; ?>"/></td>
							<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]*4; ?>" width="30"  class="wrd_brk" style="vertical-align:top;" align="center"><?= $sl; ?></td>
                        	<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]*4; ?>" width="80" class="wrd_brk" style="vertical-align:top;"><p><? echo $row['CS_NUMBER_PREFIX_NUM']; ?></p></td>
                        	<!-- <a href='##' style='color:#000' onClick="print_report(<? //echo $row['ID']; ?>,'comparative_statement_print', '../commercial/work_order/requires/comparative_statement_controller')"><? //echo $row['CS_NUMBER_PREFIX_NUM']; ?></a> -->
                        	<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]*4; ?>" width="60" class="wrd_brk" style="vertical-align:top;" align="left"><p><?= $row['CS_YEAR']; ?></p></td>
                        	<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]*4; ?>" width="80"  class="wrd_brk" style="vertical-align:top;" align="left"><p><?= change_date_format($row['CS_DATE']); ?></p></td>
                        	<?                         	
                        	$check_cs_number[$row['CS_NUMBER']]=$row['CS_NUMBER']; 
                        	$row_num=1;
                        } 
                        ?>
                        <td rowspan="4" width="100" class="item_class_<?= $sl; ?>  wrd_brk" style="vertical-align:middle;" align="left" id="item_category_id_<?= $sl.'_'.$row_num; ?>" title="<?= $row['ITEM_CATEGORY_ID']; ?>"><p><?= $item_category[$row['ITEM_CATEGORY_ID']]; ?></p>
                        	<input id="first_cs_number_count" name="first_cs_number_count" type="hidden" value="<?= $first_cs_count_row; ?>" />
							<input id="booking_id_<?= $sl.'_'.$row_num; ?>" name="booking_id[]" type="hidden" value="<?=$row['ID']; ?>" />
							<input id="booking_no_<?= $sl.'_'.$row_num; ?>" name="booking_no[]" type="hidden" value="<?=$row['CS_NUMBER']; ?>" />
							<input id="booking_dtlsid_<?= $sl.'_'.$row_num; ?>" name="booking_dtlsid[]" type="hidden" value="<?= $row['DTLS_ID']; ?>" />
							<input id="approval_id_<?= $sl.'_'.$row_num; ?>" name="approval_id[]" type="hidden" value="<?= $row['APPROVAL_ID']; ?>" />				
							<input id="supplier_id_<?= $sl.'_'.$row_num; ?>" name="supplier_id[]" type="hidden" value="<?= $row['SUPP_ID']; ?>" />				
							<input id="brand_name_<?= $sl.'_'.$row_num; ?>" name="brand_name[]" type="hidden" value="<?= $row['BRAND']; ?>" />			
							<input id="model_name_<?= $sl.'_'.$row_num; ?>" name="model_name[]" type="hidden" value="<?= $row['MODEL']; ?>" />			
							<input id="origin_name_<?= $sl.'_'.$row_num; ?>" name="origin_name[]" type="hidden" value="<?= $row['ORIGIN']; ?>" />	
                        </td>
                        <td rowspan="4" width="100"  class="wrd_brk" style="vertical-align:middle;" align="left" id="item_group_id_<?= $sl.'_'.$row_num; ?>" title="<?= $row['ITEM_GROUP_ID']; ?>"><p><?= $itemGroup_library[$row['ITEM_GROUP_ID']]; ?></p></td>
                        <td rowspan="4" width="100" class="wrd_brk" style="vertical-align:middle;" align="left" id="item_code_<?= $sl.'_'.$row_num; ?>" title="<?= $item_code; ?>"><p><?= $item_code; ?></p></td>
                        <td rowspan="4" width="150"  class="wrd_brk" style="vertical-align:middle;" align="left" id="item_description_<?= $sl.'_'.$row_num; ?>" title="<?= $item_description; ?>"><p><?= $item_description; ?></p></td>
                        <td rowspan="4" width="80"  class="wrd_brk" style="vertical-align:middle;" align="right"><p><?= $row['REQ_QTY']; ?></p></td>
						<td rowspan="4" width="60"  class="wrd_brk" style="vertical-align:middle;" align="center" id="uom_<?= $sl.'_'.$row_num; ?>" title="<?= $order_uom; ?>"><p><?= $unit_of_measurement[$order_uom]; ?></p></td>

                    	<td rowspan="4" width="150"  class="wrd_brk" style="vertical-align:middle;" align="left"><p>
                        	<?
                        	$supp_comp_ids='';
                        	if ($row['SUPP_ID'] != '') $supp_comp_ids.=$row['SUPP_ID'];
                        	?>
							<input type="text" name="txt_supp_comp_name" id="txt_supp_comp_name_<?= $sl.'_'.$row_num; ?>" value="<?= $supp_comp_name; ?>" class="text_boxes" style="width:130px" placeholder="Browse Double Click" onDblClick="openmypage_supplier(<?= $sl; ?>,<?= $row_num; ?>,'<?= $row['CS_NUMBER']; ?>', '<?= $dtls_id; ?>');" readonly />
							<input type="hidden" name="txt_supp_id" id="txt_supp_id_<?= $sl.'_'.$row_num; ?>" value="<?= $row['SUPP_ID']; ?>"/>
							<input type="hidden" name="txt_supp_comp_dtlsid" id="txt_supp_comp_dtlsid_<?= $sl.'_'.$row_num; ?>" value="<?= $supp_comp_dtls_ids; ?>"/>
							<input type="hidden" name="txt_dtlsid" id="txt_dtlsid_<?= $sl.'_'.$row_num; ?>" value="<?= $dtls_id; ?>"/>

                        </p></td>

                        <td rowspan="2" width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Mode</p></td>
                        <?                        
						foreach ($supplierArr as $supp_id)
						{
							?>
		                	<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Brand</p></td>
		                	<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Model</p></td>
							<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Origin</p></td>							
		                	<?
		                }
		                ?>
						<td rowspan="4" width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p><?= change_date_format($row['APPROVED_DATE']) ?></p></td>
						<td rowspan="4" class="wrd_brk" style="vertical-align:middle;" align="center"><p><?= $user_arr[$row['CS_INSERT_BY']]; ?></p></td>
					</tr>
					<tr bgcolor="<?= $bgcolor; ?>" id="trbandrow_<?= $sl.'_'.$row_num; ?>">
						<?
						foreach ($supp_dtls_arr as $supp_dtl_id => $supp_data)
						{
							if ($dtls_id == $supp_dtl_id)
							{
								foreach ($supplierArr as $supplier_id) 
								{
									$k=0;
									foreach ($supp_data as $supp_id => $val)
									{
										if ($supplier_id==$supp_id)
										{
											$k++;
											?>
						                	<td width="80" class="wrd_brk" align="center"><p><?= $val['BRAND']; ?>&nbsp;</p></td>
											<td width="80" class="wrd_brk" align="center"><p><?= $val['MODEL']; ?>&nbsp;</p></td>
											<td width="80" class="wrd_brk" align="center"><p><?= $val['ORIGIN']; ?>&nbsp;</p></td>
											<?
				                		}	
				                	}
				                	if ($k==0)
				                	{
				                		?>
				                		<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>	
				                		<?
				                	}
				                }	
			                }
		                }	
		                ?>
					</tr>
					<tr bgcolor="<?= $bgcolor; ?>" id="trqutrow_<?= $sl.'_'.$row_num; ?>">
						<td rowspan="2" width="" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Price</p></td>
						<?
						foreach ($supplierArr as $supp_id)
						{	
							?>
		                	<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Quoted Price</p></td>
							<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Last Price</p></td>
							<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Con. Price</p></td>
		                	<?
		                }
		                ?>
					</tr>
					<tr bgcolor="<?= $bgcolor; ?>" id="trqutvalrow_<?= $sl.'_'.$row_num; ?>">
						<?
						foreach ($supp_dtls_arr as $supp_dtl_id => $supp_data)
						{
							if ($dtls_id == $supp_dtl_id)
							{
								foreach ($supplierArr as $supplier_id) 
								{
									$k=0;
									foreach ($supp_data as $supp_id => $val)
									{
										if ($supplier_id==$supp_id)
										{
											$k++;
											?>
						                	<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p><?= $val['QUOTED_PRICE']; ?>&nbsp;</p></td>
											<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p><?= $val['NEG_PRICE']; ?>&nbsp;</p></td>
											<? 
											if ($approval_type==1) 
											{	
												?>
												<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><?= $val['LAST_APPROVAL_RATE']; ?></td>
												<?
											}
											else
											{	
												?>
												<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">
													<input type="text" name="" id="txtApprovedPriceSpplier_<?= $sl.'_'.$row_num.'_'.$supp_id; ?>" class="text_boxes_numeric" style="width: 70px;" disabled value="<?= $val['LAST_APPROVAL_RATE']; ?>"/>
													<input type="hidden" name="" id="suppDtlsidSupplier_<?= $sl.'_'.$row_num.'_'.$supp_id; ?>" value="<?= $dtls_id; ?>"/>
												</td>
												<?
											}
											break;
										}	
				                	}
				                	if ($k==0)
				                	{
				                		?>
				                		<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>	
				                		<?
				                	}
				                }	
			                }
		                }	
		                ?>
					</tr>			
					<?
					//$del_row++;		
				}
				?>
				
            </tbody>
        </table>    
       <table cellspacing="0" cellpadding="0" width="200" align="left">
			<tfoot>
				<input type="hidden" id="item_wise_row_count" value="<? echo json_encode($arr); ?>">
                <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check',<?= $sl; ?>)" /></td>
                <td width="100" align="center"><input type="button" value="<? if ($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $sl; ?>,<? echo $approval_type; ?>)" /></td>
			</tfoot>
		</table>
        </fieldset>       
    </form>
    </div> 
	<?
	exit();
}


if($action=="supplier_popup")
{
  	echo load_html_head_contents("Supplier Info","../../", 1, 1,'','');
	extract($_REQUEST);
    ?>
    <script>
		$(document).ready(function(e) {
			setFilterGrid('tbl_list_search',-1);
		});
		var selected_id = new Array(); 
		var selected_name = new Array(); 
		var selected_dtlsid = new Array();
		var selected_suppid = new Array();

		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function set_all()
		{
			var old=document.getElementById('txt_supp_comp_dtlsid_row_id').value; 
			if(old!="")
			{   
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{   
					js_set_value( old[k] ) 
				} 
			}
		}
		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				selected_dtlsid.push( $('#txt_dtlsid' + str).val() );
				selected_suppid.push( $('#txt_individual_supp_id' + str).val() );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_dtlsid.splice( i, 1 );
				selected_suppid.splice( i, 1 );
			}
			var id = ''; var name = ''; var dtlsid=''; var suppid=''; var compid='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				dtlsid += selected_dtlsid[i] + ',';
				suppid += selected_suppid[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			dtlsid = dtlsid.substr( 0, dtlsid.length - 1 );
			suppid = suppid.substr( 0, suppid.length - 1 );
			//alert(id);
			$('#hid_supp_comp_dtlsid').val(id);
			$('#hid_supp_comp_name').val(name);
			$('#hid_dtlsid').val(dtlsid);
			$('#hid_supp_id').val(suppid);

		}
    </script>

	</head>
	<body>
	<div align="center">
		<fieldset style="width:370px;margin-left:10px">
	    	<input type="hidden" name="hid_supp_comp_dtlsid" id="hid_supp_comp_dtlsid">
	        <input type="hidden" name="hid_supp_comp_name" id="hid_supp_comp_name">
	        <input type="hidden" name="hid_dtlsid" id="hid_dtlsid">
	        <input type="hidden" name="hid_supp_id" id="hid_supp_id">
	        <form name="searchbuyerfrm_1"  id="searchbuyerfrm_1" autocomplete="off">
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" >
	                <thead>
	                    <th width="50">SL</th>
	                    <th>Supplier Name</th>
	                </thead>
	            </table>
	            <div style="width:350px; overflow-y:scroll; max-height:280px;" align="center">
	                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search" >
						<?
						$supplier_library=return_library_array( "select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0",'id','supplier_name');
						$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');

						if ($txtDtlsid != '') $supp_comp_dtls_id=$txtDtlsid;
						else $supp_comp_dtls_id=$dtls_id;
						$supp_dtls="SELECT a.approved as APPROVED_MST, c.id as ID, c.mst_id as MST_ID, c.dtls_id as DTLS_ID, c.supp_id as SUPP_ID, c.supp_type as SUPP_TYPE, c.approved as APPROVED from req_comparative_mst a, req_comparative_supp_dtls c where a.id=c.mst_id and a.entry_form=481 and dtls_id in($dtls_id) and c.con_price is not null and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 order by a.id asc";
						$supp_dtls_res=sql_select($supp_dtls);

						$i=1;
						$txt_supp_comp_dtlsid_row_id='';
						//echo $txtSuppCompDtlsid;
						$hidden_supp_comp_dtls_id=explode(",",$txt_supp_comp_dtlsid);
						//print_r($hidden_supplier_dtls_id);
						foreach($supp_dtls_res as $row)
						{
	                        if ($i%2==0) $bgcolor="#E9F3FF";
	                        else $bgcolor="#FFFFFF";

							$supp_name='';
							$supp_id='';
							if ($row['SUPP_TYPE']==1) 
							{
								$supp_name=$supplier_library[$row['SUPP_ID']];
								$supp_id=$row['SUPP_ID'];
							}	
							$row_id=$row['ID'];

							if(in_array($row_id,$hidden_supp_comp_dtls_id)) 
							{ 
								if($txt_supp_comp_dtlsid_row_id=="") $txt_supp_comp_dtlsid_row_id=$i; else $txt_supp_comp_dtlsid_row_id.=",".$i;
							}
					
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<?= $i; ?>');">
	                            <td width="50" align="center"><?= $i; ?>
	                            	<input type="hidden" name="txt_individual_id" id="txt_individual_id<?= $i; ?>" value="<?= $row['ID']; ?>"/>
	                            	 <input type="hidden" name="txt_individual" id="txt_individual<?= $i; ?>" value="<?= $supp_name; ?>"/>
	                            	 <input type="hidden" name="txt_dtlsid" id="txt_dtlsid<?= $i; ?>" value="<?= $row['DTLS_ID']; ?>"/>
	                                <input type="hidden" name="txt_individual_supp_id" id="txt_individual_supp_id<?= $i; ?>" value="<?= $supp_id; ?>"/>
	                                <input type="hidden" name="txt_individual_comp_id" id="txt_individual_comp_id<?= $i; ?>" value="<?= $comp_id; ?>"/>	                                
	                            </td>
	                            <td style="word-break:break-all"><?= $supp_name; ?></td>
	                        </tr>	                        	                        
	                        <?
	                        $i++;
	                    }
	                    ?>
	                    <input type="hidden" name="txt_supp_comp_dtlsid_row_id" id="txt_supp_comp_dtlsid_row_id" value="<?=$txt_supp_comp_dtlsid_row_id; ?>"/>
	                </table>
	            </div>
	            <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
	                <tr>
	                    <td align="center" height="30" valign="bottom">
	                        <div style="width:100%">
	                            <div style="width:50%; float:left" align="left">
	                                <input type="checkbox" name="check_all" id="check_alll" onClick="check_all_data();" /> Check / Uncheck All
	                            </div>
	                            <div style="width:50%; float:left" align="left">
	                                <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
	                            </div>
	                        </div>
	                    </td>
	                </tr>
	            </table>
	        </form>
	    </fieldset>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		//setFilterGrid('tbl_list_search',-1);
		set_all();
	</script>
	</html>
	<?
	exit();
}

if ($action=="populate_data_from_copy_cs_check")
{
	$data=explode("**", $data);
	$dtls_id=$data[0];
	$supp_ids_arr=explode(',', $data[1]);
	$txtSuppCompDtlsid=$data[2];
	$row_num=$data[3];
	//echo 'system';die;

	$supp_dtls_arr=array();
	//echo "SELECT id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, supp_type as SUPP_TYPE, approved as APPROVED from req_comparative_supp_dtls where dtls_id in($dtls_id) and supp_type=1 and con_price is not null and is_deleted=0 and status_active=1 order by id asc";
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$mst_id=return_field_value("mst_id as mst_id", "req_comparative_supp_dtls", "id in($txtSuppCompDtlsid) and status_active=1 and is_deleted=0", "mst_id");
	//echo "SELECT id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, supp_type as SUPP_TYPE, approved as APPROVED from req_comparative_supp_dtls where mst_id=$mst_id and dtls_id not in($dtls_id) and supp_type=1 and con_price is not null and is_deleted=0 and status_active=1 order by id asc";
	$supp_dtls_arr=sql_select("SELECT id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, supp_type as SUPP_TYPE, approved as APPROVED from req_comparative_supp_dtls where mst_id=$mst_id and dtls_id not in($dtls_id) and supp_type=1 and con_price is not null and is_deleted=0 and status_active=1 order by id asc");

	$supp_arr_length=count($supp_dtls_arr);
	//$i=1;
	if ($supp_arr_length > 0)
	{
		for( $i=2; $i<=$row_num; $i++ )
		{
			$id=$supp_ids=$supp_names="";
			foreach ($supp_dtls_arr as $val)
			{
				$supp_id=$val['SUPP_ID'];						
				foreach ($supp_ids_arr as $supplier_id)
				{
					if ($supp_id==$supplier_id){
						$ids.=$val['ID'].',';
						$dtls_id.=$val['DTLS_ID'];
						//echo $ids.'system';
						$supp_ids.=$supp_id.',';
						$supp_names.=$supplier_library[$supp_id].',';
						//echo "get_php_form_data( $sl.'**'.$row_num.'**'.$dtls_id."**".supp_ids, "populate_data_from_supplier_popup", "requires/cs_approval_general_controller" );";					
					}
				}	
			}
			
			$supp_ids=rtrim($supp_ids,',');
			$supp_names=rtrim($supp_names,',');
			$ids=rtrim($ids,',');

			echo "$('#txt_supp_id_1_".$i."').val('".$supp_ids."');\n";
			echo "$('#txt_supp_comp_name_1_".$i."').val('".$supp_names."');\n";
			echo "$('#txt_supp_comp_dtlsid_1_".$i."').val('".$ids."');\n";

		}		
	}
	//echo $supp_names;
	exit();
}

if ($action=="populate_data_from_supplier_popup")
{
	$data=explode("**", $data);
	$sl=$data[0];
	$row_num=$data[1];
	$dtls_id=$data[2];	
	$supp_ids_arr=explode(',', $data[3]);
	$type=$data[4];

	$supp_dtls_arr=array();
	//echo "SELECT id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, supp_type as SUPP_TYPE, approved as APPROVED from req_comparative_supp_dtls where dtls_id in($dtls_id) and supp_type=1 and neg_price is not null and is_deleted=0 and status_active=1 order by id asc";
	$supp_dtls_arr=sql_select("SELECT id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, supp_type as SUPP_TYPE, approved as APPROVED, con_price as CON_PRICE from req_comparative_supp_dtls where dtls_id in($dtls_id) and supp_type=1 and con_price is not null and is_deleted=0 and status_active=1 order by id asc");

	$supp_arr_length=count($supp_dtls_arr);
	if ($type==1)
	{
		if ($supp_arr_length > 0)
		{
			foreach ($supp_dtls_arr as $val)
			{
				$supp_id=$val['SUPP_ID'];
				echo "$('#txtApprovedPriceSpplier_".$sl."_".$row_num."_".$supp_id."').attr('disabled','disabled').val(".$val['CON_PRICE'].");\n";			
				foreach ($supp_ids_arr as $supplier_id) 
				{
					if ($supp_id==$supplier_id){
						echo "$('#txtApprovedPriceSpplier_".$sl."_".$row_num."_".$supp_id."').removeAttr('disabled');\n";
					}
				}	
			}		
		}
	}
	else
	{
		if ($supp_arr_length > 0)
		{
			foreach ($supp_dtls_arr as $val)
			{
				$supp_id=$val['SUPP_ID'];
				echo "$('#txtApprovedPriceSpplier_".$sl."_".$row_num."_".$supp_id."').attr('disabled','disabled').val(".$val['CON_PRICE'].");\n";	
			}		
		}
	}	
	
	exit();
}

if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();

	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if ($txt_alter_user_id!="") $user_id_approval=$txt_alter_user_id; 
	else $user_id_approval=$user_id;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup"," page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$menu_id and is_deleted=0");



	if($approval_type == 0)
	{
		//echo $booking_ids;die;
		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");		

		if ($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;

		$booking_ids_all = implode(",",array_unique(explode(",",$booking_ids)));
		$booking_ids_allArr = explode(",",$booking_ids_all);

		$max_approved_no_arr = return_library_array("SELECT mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids_all) and entry_form=49 group by mst_id","mst_id","approved_no");
		$approved_status_arr = return_library_array("SELECT id, is_approved from req_comparative_mst where id in($booking_ids_all)","id","approved");

		$id = return_next_id( "id","approval_history", 1);
		$field_array = "id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date";

		for($i=0; $i<count($booking_ids_allArr); $i++)
		{
			$booking_id = $booking_ids_allArr[$i];
			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];

			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
			}

			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",49,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')"; 
			$id=$id+1;
		}	   

		$field_array_up="approved*approved_by*approved_date";
		$data_array_up=$partial_approval."*".$user_id."*'".$pc_date_time."'";
		

		$id=return_next_id( "id", "lib_supplier_wise_rate", 1 );

		$field_array_prod="id, company_id, entry_form, item_category_id, item_group_id, item_code, item_description, unit_of_measure, brand_name, model, origin, item_size, inserted_by, insert_date, status_active, is_deleted";
		$field_array_supplier_wise_rate="id, prod_id, supplier_id, entry_form, item_category_id, item_group_id, effective_from, rate, is_supp_comp, inserted_by, insert_date, is_deleted";

		$sql_supp_dtls="select ID, DTLS_ID, SUPP_ID, SUPP_TYPE, LAST_APPROVAL_RATE from req_comparative_supp_dtls where dtls_id in($booking_dtlsids) and status_active=1 and is_deleted=0";
		$sql_supp_dtls_res=sql_select($sql_supp_dtls);
		$supp_dtls_arr=array();
		foreach ($sql_supp_dtls_res as $val) 
		{
			$supp_dtls_arr[$val['DTLS_ID']][$val['SUPP_ID']][$val['SUPP_TYPE']]['ID'] = $val['ID'];
		}

		$companyArr_library=return_library_array("select id from lib_company where status_active=1 and is_deleted=0","id","id");

		$product_data_arr=array();
		$product_company_arr=array();

		$sql_prod_res=sql_select("select ID, COMPANY_ID, ITEM_CATEGORY_ID, ITEM_GROUP_ID, ITEM_CODE, ITEM_DESCRIPTION, UNIT_OF_MEASURE, COLOR, GMTS_SIZE, ITEM_COLOR, ITEM_SIZE, brand_name as BRAND, MODEL, ORIGIN from product_details_master where status_active=1 and is_deleted=0 and item_category_id not in(1,2,3,12,13,14)");
		foreach ($sql_prod_res as $val) 
		{
			$key=$val['ITEM_CATEGORY_ID'].'**'.$val['ITEM_GROUP_ID'].'**'.$val['ITEM_CODE'].'**'.$val['ITEM_DESCRIPTION'].'**'.$val['UNIT_OF_MEASURE'].'**'.$val['BRAND'].'**'.$val['item_size'];
			$product_data_arr[$key].=$val['ID'].',';
			$product_company_arr[$val['ID']]=$val['COMPANY_ID'];
		}
		//echo '<pre>';print_r($product_data_arr);

		$sl_str_arr = explode(",",$sl_str);
		//echo '<pre>';print_r($sl_str_arr);

		foreach ($sl_str_arr as $i) 
		{
			$item_row="sl_wise_item_row_".$i;
			for($j=1; $j<=$$item_row; $j++)
			{
				$item_category_id="item_category_id_".$i."_".$j;
				if (str_replace("'", "", $$item_category_id) == 4 ) $entry_form_lib=20;
				else $entry_form_lib=0;
				$item_group_id="item_group_id_".$i."_".$j;
				$item_code="item_code_".$i."_".$j;
				$item_description="item_description_".$i."_".$j;
				$brand_name="brand_name_".$i."_".$j;		
				$model_name="model_name_".$i."_".$j;
				$origin_name="origin_name_".$i."_".$j;
				$uom="uom_".$i."_".$j;

				$item_size="";
				$booking_dtlsid="booking_dtlsid_".$i."_".$j;
				// All Supplier, All Company select part
				$all_supp_ids="supplier_id_".$i."_".$j;
				$all_supp_num_arr = explode(',',$$all_supp_ids);
				$all_supp_num_total_row=count($all_supp_num_arr);

				// Supplier, Company select part
				$txt_supp_id="txt_supp_id_".$i."_".$j;
				$supp_num_arr = explode(',',$$txt_supp_id);
				//echo $comp_num_total_row.'system5';

				// Supplier Part
				for($as=0; $as<$all_supp_num_total_row; $as++)
				{
					$is_supp_comp=1;
					$supp_id=$all_supp_num_arr[$as];
					$txtApprovedPriceSpplier= "txtApprovedPriceSpplier_".$i."_".$j."_".$supp_id;				

					if (in_array($supp_id, $supp_num_arr))
					{
								
						$supp_data=$$item_category_id.'**'.$$item_group_id.'**'.$$item_code.'**'.$$item_description.'**'.$$uom.'**'.$$brand_name.'**'.$item_size;
						//echo $supp_data.'system';
						$companyArr=array();
						$all_product_arr=array();
						if (array_key_exists($supp_data,$product_data_arr))
						{
							$product_ids=rtrim($product_data_arr[$supp_data],',');
							$exp_product_ids=explode(',',$product_ids);
							foreach ($exp_product_ids as $product_id) {
								$companyArr[] = $product_company_arr[$product_id];
								$all_product_arr[]=$product_id;
							}

							$company_diff_arr=array_diff($companyArr_library,$companyArr);
							foreach ($company_diff_arr as $compID) {
								$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
								$all_product_arr[]=$prod_id;
								if ($data_array_prod != '') $data_array_prod .=",";
								//$data_array_prod.="(".$prod_id.",".$compID.",".$entry_form_lib.",".$$item_category_id.",".$$item_group_id.",'".$$item_code."','".$$item_description."',".$$uom.",".$gmts_color_id.",".$gmts_size_id.",".$item_color_id.",'".$item_size."',".$user_id.",'".$pc_date_time."',1,0)";
								$data_array_prod.="(".$prod_id.",".$compID.",".$entry_form_lib.",".$$item_category_id.",".$$item_group_id.",'".$$item_code."','".$$item_description."',".$$uom.",'".$$brand_name."','".$$model_name."','".$$origin_name."','".$item_size."',".$user_id.",'".$pc_date_time."',1,0)";
								//echo $data_array_prod;
								//$prod_id = $prod_id+1;
							}							
							//echo "insert into lib_supplier_wise_rate($field_array_supplier_wise_rate)values".$data_array_supplier_wise_rate;
						}
						else
						{
							foreach ($companyArr_library as $compID) {
								$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
								$all_product_arr[]=$prod_id;
								if ($data_array_prod != '') $data_array_prod .=",";
								//$data_array_prod.="(".$prod_id.",".$compID.",".$entry_form_lib.",".$$item_category_id.",".$$item_group_id.",'".$$item_code."','".$$item_description."',".$$uom.",".$gmts_color_id.",".$gmts_size_id.",".$item_color_id.",'".$item_size."',".$user_id.",'".$pc_date_time."',1,0)";
								$data_array_prod.="(".$prod_id.",".$compID.",".$entry_form_lib.",".$$item_category_id.",".$$item_group_id.",'".$$item_code."','".$$item_description."',".$$uom.",'".$$brand_name."','".$$model_name."','".$$origin_name."','".$item_size."',".$user_id.",'".$pc_date_time."',1,0)";
								//echo $data_array_prod;
								//$prod_id = $prod_id+1;
							}				
							
						}

						// data details lib Supplier wise rate table
						foreach ($all_product_arr as $product_id) {
							if ($data_array_supplier_wise_rate != '') $data_array_supplier_wise_rate .=",";
							$data_array_supplier_wise_rate.="(".$id.",".$product_id.",".$supp_id.",481,".$$item_category_id.",".$$item_group_id.",'".$pc_date_time."','".$$txtApprovedPriceSpplier."',".$is_supp_comp.",".$user_id.",'".$pc_date_time."',0)";
							$id = $id+1;
						}

						// supplier dtls data
						$supp_dtls_arr[$$booking_dtlsid][$supp_id][$is_supp_comp]['APPROVAL_RATE'] = $$txtApprovedPriceSpplier;
						$supp_dtls_arr[$$booking_dtlsid][$supp_id][$is_supp_comp]['APPROVED'] = 1;					
					}
					else
					{
						// supplier dtls data
						$supp_dtls_arr[$$booking_dtlsid][$supp_id][$is_supp_comp]['APPROVAL_RATE'] = $$txtApprovedPriceSpplier;
						$supp_dtls_arr[$$booking_dtlsid][$supp_id][$is_supp_comp]['APPROVED'] = 0;
					}	
				}
			}	
		}

		//echo "21** insert into lib_supplier_wise_rate($field_array_supplier_wise_rate)values".$data_array_supplier_wise_rate;
		//echo "10**INSERT INTO product_details_master (".$field_array_prod.") VALUES ".$data_array_prod;die;

		$field_array_up_supp = "last_approval_rate*approved*updated_by*update_date";
		$data_array_up_supp=array();
		$supp_rowid_up_array=array();
		foreach ($supp_dtls_arr as $dtls_id => $supp_id_data) 
		{
			foreach ($supp_id_data as $supp_id => $supp_type_data) 
			{
				foreach ($supp_type_data as $supp_type => $row)
				{
					if ($row['ID'] != '' && $row['APPROVAL_RATE'] != '')
					{
						//echo $row['APPROVAL_RATE'].'system';
						$approval_rate=$row['APPROVAL_RATE'];
						$approved=$row['APPROVED'];
						$data_array_up_supp[$row['ID']] = explode("*",("".$approval_rate."*".$approved."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						$supp_rowid_up_array[]=$row['ID'];
					}	
				}
			}
		}

		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=49 and mst_id in ($booking_ids_all)";
		$rIDapp=execute_query($query,1);

		if($rIDapp) $flag=1; else $flag=0;
	    //echo "10** insert into approval_history($field_array)values".$data_array;die;
		$rID = sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}

		$rID2=sql_multirow_update("req_comparative_mst",$field_array_up,$data_array_up,"id",$booking_ids_all,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0;
		}

		//echo "10**INSERT INTO product_details_master (".$field_array_prod.") VALUES ".$data_array_prod;die;
		if ($data_array_prod != "")
		{
			$rID3 = sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}
		}
		

		if ($data_array_supplier_wise_rate != "")
		{
			$rID4 = sql_insert("lib_supplier_wise_rate",$field_array_supplier_wise_rate,$data_array_supplier_wise_rate,0);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			}
		}
       
		//echo bulk_update_sql_statement( "req_comparative_supp_dtls", "id", $field_array_up_supp, $data_array_up_supp, $supp_rowid_up_array );die;
		$rID5=execute_query(bulk_update_sql_statement( "req_comparative_supp_dtls", "id", $field_array_up_supp, $data_array_up_supp, $supp_rowid_up_array ));
        if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0;
		}

		//echo "10**$booking_ids_all";die;
		$response=$booking_ids_all;
		if($flag==1) $msg='19'; else $msg='21';
	}
	else
	{			 
		$booking_ids_all = implode(",",array_unique(explode(",",$booking_ids)));
		$booking_dtlsids_all = implode(",",array_unique(explode(",",$booking_dtlsids)));
		$approval_ids_all = implode(",",array_unique(explode(",",$approval_ids)));

	    $flag=1;
	    $rID=sql_multirow_update("req_comparative_mst","approved*ready_to_approved","0*0","id",$booking_ids_all,0);
		if($rID) $flag=1; else $flag=0;
		//echo "21**$flag";die;
	    //echo "21** insert into approval_history($field_array)values".$data_array;die;

		$rID2=sql_multirow_update("req_comparative_supp_dtls","approved","0","dtls_id",$booking_dtlsids_all,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		$data="0*".$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","current_approval_status*un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids_all,1);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}
		//echo "22**$booking_ids_all";
		$response=$booking_ids_all;
		if($flag==1) $msg='20'; else $msg='22';
	}
		
	if($db_type==0)
	{
		if($flag==1)
		{
			mysql_query("COMMIT");
			echo $msg."**".$response;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo $msg."**".$response;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con);
			echo $msg."**".$response;
		}
		else
		{
			oci_rollback($con);
			echo $msg."**".$response;
		}
	}
	disconnect($con);
	die;
}

if($action=="report_generate_after_approve_unapprove")
{
	$data=explode("**",$data);
	$cbo_item_category_id=$data[0];
	$cbo_cs_year=$data[1];
	$txt_cs_no=$data[2];
	$txt_date_from=$data[3];
	$txt_date_to=$data[4];
	$cbo_approval_type=$data[5];
	$txt_alter_user_id=$data[6];
	$all_cs_ids=$data[7];

	$sequence_no='';
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$cbo_cs_year=str_replace("'","",$cbo_cs_year);
	$txt_cs_no=str_replace("'","",$txt_cs_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if ($txt_alter_user_id !="") $user_id=$txt_alter_user_id;

	$approval_type=str_replace("'","",$cbo_approval_type);
	if ($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}

	$cs_date_cond=$cs_no_cond=$item_category_cond='';
	if ($cbo_item_category_id != 0) $item_category_cond=" and b.item_category_id=$cbo_item_category_id";
	if ($txt_cs_no != '') $cs_no_cond=" and a.sys_number_prefix_num=$txt_cs_no";

	if ($approval_type == 1) $approved_cond=" and a.approved in (1)";
	else $approved_cond=" and a.approved in (0,2)";

	if ($txt_date_from != '' && $txt_date_to != '')
	{
		if($db_type==0)
		{
			$txt_date_from = date("Y-m-d", strtotime($txt_date_from));
			$txt_date_to = date("Y-m-d", strtotime($txt_date_to));
			$cs_date_cond = " and a.cs_date between '".$txt_date_from."' and '".$txt_date_to."'";
		}
		else
		{
			$txt_date_from = date("d-M-Y", strtotime($txt_date_from));
			$txt_date_to = date("d-M-Y", strtotime($txt_date_to));
			$cs_date_cond = " and a.cs_date between '".$txt_date_from."' and '".$txt_date_to."'";
		}	
	}
	/*$not_in_all_cs_ids_cond="";
	if ($all_cs_ids != "") $not_in_all_cs_ids_cond=" a.id not in($all_cs_ids)";*/

	$cs_year_cond='';
	if($db_type==0)
	{
		if ($cbo_cs_year != 0) $cs_year_cond= " and year(a.insert_date)=$cbo_cs_year";
		$year_cond_prefix= "year(a.insert_date)";
		$year_field="YEAR(a.insert_date) as CS_YEAR";
	}
	else if($db_type==2)
	{
		if ($cbo_cs_year != 0) $cs_year_cond= " and TO_CHAR(a.insert_date,'YYYY')=$cbo_cs_year";
		$year_cond_prefix= "TO_CHAR(a.insert_date,'YYYY')";
		$year_field="to_char(a.insert_date,'YYYY') as CS_YEAR";
	}

	$brandArr = return_library_array("select id,brand_name from lib_buyer_brand ","id","brand_name");
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$itemGroup_library=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$menu_id and user_id=$user_id and is_deleted=0");

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$menu_id and is_deleted = 0");
	//echo "select sequence_no from electronic_approval_setup where page_id=$menu_id and user_id=$user_id and is_deleted=0"."**".$user_sequence_no."**".$min_sequence_no."**".$menu_id;
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority.</font>";die;
	}

	if($previous_approved==1 && $approval_type==1)	//approval process with prevous approve start
	{
		$sequence_no_cond=" and c.sequence_no<'$user_sequence_no'";
	
		$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
		from req_comparative_mst a, req_comparative_dtls b, approval_history c 
		where a.id=b.mst_id and a.id=c.mst_id and a.entry_form=481 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=49 and c.current_approval_status=1 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond $sequence_no_cond 
		group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin, c.id, c.approved_date
		order by a.id";
		//echo "$sql";
	}

	else if($approval_type==0)	// unapproval process start
	{

		if($user_sequence_no==$min_sequence_no)  // First user
		{

		 	$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN 
		 	from req_comparative_mst a, req_comparative_dtls b 
		 	where a.id=b.mst_id and a.entry_form=481 and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 	group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin
			order by a.id";
			//echo $sql;
		}
		else // Next user
		{
			$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");

			if($sequence_no=="")  // bypass if previous user Yes
			{
				if($db_type==0)
				{
					
					$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup wherepage_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];
					
					$req_comp_id=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no in ($sequence_no_by) and c.entry_form=49 and c.current_approval_status=1 $cs_date_cond","req_comp_id");
					$req_comp_id=implode(",",array_unique(explode(",",$req_comp_id)));
					
					$req_comp_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no=$user_sequence_no and c.entry_form=49 and c.current_approval_status=1 ","req_comp_id");
					$req_comp_id_app_byuser=implode(",",array_unique(explode(",",$req_comp_id_app_byuser)));
				}
				else
				{

					$seqSql="select group_concat(sequence_no) as sequence_no_by  from electronic_approval_setup wherepage_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];
					
					$req_comp_id=return_field_value("group_concat(distinct(b.mst_id)) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no in ($sequence_no_by) and c.entry_form=49 and c.current_approval_status=1 $cs_date_cond ","req_comp_id");
					$req_comp_id=implode(",",array_unique(explode(",",$req_comp_id)));
					
					$req_comp_id_app_byuser=return_field_value("group_concat(distinct(b.mst_id)) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no=$user_sequence_no and c.entry_form=49 and c.current_approval_status=1 ","req_comp_id");
					$req_comp_id_app_byuser=implode(",",array_unique(explode(",",$req_comp_id_app_byuser)));
				}

				$result=array_diff(explode(',',$req_comp_id),explode(',',$req_comp_id_app_byuser));
				$req_comp_id=implode(",",$result);

				if($req_comp_id!="")
				{					
					$sql=" SELECT x.* from  (SELECT DISTINCT (a.id) as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN
					from req_comparative_mst a, req_comparative_dtls b
					where a.id=b.mst_id and a.entry_form=481 and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 			group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin

					UNION ALL

					SELECT DISTINCT (a.id) as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN 
					from req_comparative_mst a, req_comparative_dtls b
					where a.id=b.mst_id and a.entry_form=481 and a.approved in(1,3) and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($req_comp_id) $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 			group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin) x  order by x.ID";
					//echo $sql;
				}
				else
				{ 
					$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN 
					from req_comparative_mst a, req_comparative_dtls b
					where a.id=b.mst_id and a.entry_form=481 and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 			group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin
		 			order by a.id";
					//echo $sql;
				}
				//echo $sql;
			}			
			else // if previous user bypass No 
			{
				$user_sequence_no=$user_sequence_no-1;
				if($sequence_no==$user_sequence_no) 
				{
					$sequence_no_by_pass=$sequence_no;
					$sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
				}
				else
				{
					if($db_type==0) 
					{
						$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
					}
					else if($db_type==2) 
					{
						$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
					}
					
					if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
					else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
				}

				$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
				from req_comparative_mst a, req_comparative_dtls b, approval_history c 
				where a.id=b.mst_id and a.id=c.mst_id and a.entry_form=481 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=49 and c.current_approval_status=1 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond $sequence_no_cond 
				group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin, c.id, c.approved_date
				order by a.id";
				//echo $sql;
			}
		}	

	}
	else // approval process start
	{
		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";

		$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field, a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.currency_id as CURRENCY_ID, a.cs_valid_date as CS_VALID_DATE, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.prod_id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.req_qty as REQ_QTY, b.brand as BRAND, b.model as MODEL, b.origin as ORIGIN, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
		from req_comparative_mst a, req_comparative_dtls b, approval_history c 
		where a.id=b.mst_id and a.id=c.mst_id and a.entry_form=481 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=49 and c.current_approval_status=1 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond $sequence_no_cond 
		group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.currency_id, a.cs_valid_date, a.inserted_by, b.id, b.item_category_id, b.prod_id, b.item_group_id, b.req_qty, b.brand, b.model, b.origin, c.id, c.approved_date
		order by a.id";
	}
	//echo $sql;
	
	$sql_res=sql_select($sql);

	foreach($sql_res as $row)
	{
		$mst_id.=$row["ID"].",";
		if ($row["PROD_ID"] != '') $selected_prod_id.=$row["PROD_ID"].",";
		$company_arr[$row["CS_NUMBER"]]=$row["COMPANY_ID"];
		$supplier_arr[$row["CS_NUMBER"]]=$row["SUPP_ID"];		
		$dtls_row_arr[$row["CS_NUMBER"]].=$row["DTLS_ID"].',';
		$cs_number_arr[$row["CS_NUMBER"]].=$row["CS_NUMBER"].',';
		$rowspan_arr[$row["CS_NUMBER"]]++;
	}
	$first_cs_count_row=count(explode(',',rtrim(reset($dtls_row_arr),',')));
	$supplierArr=array_unique(explode(",", implode(",", $supplier_arr)));
	$supplier_width=count($supplierArr)*240;
	//echo '<pre>';print_r($supplierArr);

	$mst_ids = implode(',', array_flip(array_flip(explode(',', rtrim($mst_id,',')))));
	$selected_prod_id=chop($selected_prod_id,",");

	$item_des_sql="SELECT b.id as PROD_ID, b.item_group_id as ITEM_GROUP_ID, b.order_uom as ORDER_UOM, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_code as ITEM_CODE, b.item_size as ITEM_SIZE
	from product_details_master b
	where b.id in($selected_prod_id) and b.is_deleted=0 and b.status_active=1";
	$item_des_sql_result=sql_select($item_des_sql);
	$item_des_sql_arr=array();
	foreach($item_des_sql_result as $row)
	{
		$item_des_sql_arr[$row["PROD_ID"]]['PROD_ID']=$row["PROD_ID"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_DESCRIPTION']=$row["ITEM_DESCRIPTION"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_CODE']=$row["ITEM_CODE"];
		$item_des_sql_arr[$row["PROD_ID"]]['ORDER_UOM']=$row["ORDER_UOM"];
		$item_des_sql_arr[$row["PROD_ID"]]['ITEM_SIZE']=$row["ITEM_SIZE"];
	}
	//echo '<pre>';print_r($item_des_sql_arr);
	
	$supp_dtls="SELECT id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, supp_type as SUPP_TYPE, prod_id as PROD_ID, quoted_price as QUOTED_PRICE, neg_price as NEG_PRICE, con_price as CON_PRICE, last_approval_rate as LAST_APPROVAL_RATE, brand as BRAND, model as MODEL, origin as ORIGIN, approved as APPROVED from req_comparative_supp_dtls where mst_id in($mst_ids) and is_deleted=0 and status_active=1 order by id asc";
	$supp_dtls_res=sql_select($supp_dtls);
	$supp_dtls_arr=array();
	$supp_comp_dtls_arr=array();
	$supp_id_dtls_arr=array();
	$supp_iddtls_array=array();
	foreach ($supp_dtls_res as $row) {
		if ($row["SUPP_TYPE"] == 1)
		{
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['QUOTED_PRICE']=$row["QUOTED_PRICE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['LAST_APPROVAL_RATE']=$row["LAST_APPROVAL_RATE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['NEG_PRICE']=$row["NEG_PRICE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['BRAND']=$row["BRAND"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['MODEL']=$row["MODEL"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['ORIGIN']=$row["ORIGIN"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['SUPP_TYPE']=$row["SUPP_TYPE"];
			if ($row["APPROVED"] == 1) 
			{
				$supp_comp_dtls_arr[$row["DTLS_ID"]]['ID'].=$row["ID"].',';
				$supp_comp_dtls_arr[$row["DTLS_ID"]]['NAME'].=$supplier_library[$row["SUPP_ID"]].',';
				$supp_id_dtls_arr[$row["DTLS_ID"]].=$row["SUPP_ID"].',';
			}

			if ($row["CON_PRICE"]  != ""){
				$supp_iddtls_array[$row["DTLS_ID"]]['supp_id'].=$row["SUPP_ID"].',';
				$supp_iddtls_array[$row["DTLS_ID"]]['supp_name'].=$supplier_library[$row["SUPP_ID"]].',';
				$supp_iddtls_array[$row["DTLS_ID"]]['id'].=$row["ID"].',';
			}
		}
	}
	//echo '<pre>';print_r($supp_iddtls_array);die;
	$tbl_width=1280+$supplier_width*1;
	?>
	<script>

		function openmypage_supplier(sl, row_num, cs_number, dtls_id)
		{
			var txt_supp_comp_dtlsid= $('#txt_supp_comp_dtlsid_'+sl+'_'+row_num).val();
			var txt_dtlsid= $('#txt_dtlsid_'+sl+'_'+row_num).val();
			var page_link="requires/cs_approval_general_controller.php?action=supplier_popup&txt_supp_comp_dtlsid="+txt_supp_comp_dtlsid+"&txt_dtlsid="+txt_dtlsid+"&dtls_id="+dtls_id;

			var title="Supplier Info";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{

				var theemailID=this.contentDoc.getElementById("hid_supp_comp_dtlsid").value;	
				var theemailVAL=this.contentDoc.getElementById("hid_supp_comp_name").value;
				var theemailDtlsID=this.contentDoc.getElementById("hid_dtlsid").value;
				var theemailsuppID=this.contentDoc.getElementById("hid_supp_id").value;
				$('#txt_supp_comp_dtlsid_'+sl+'_'+row_num).val(theemailID);
				$('#txt_supp_comp_name_'+sl+'_'+row_num).val(theemailVAL);
				if (theemailDtlsID != ""){
					$('#txt_dtlsid_'+sl+'_'+row_num).val(theemailDtlsID);
				}				

				
				if (theemailsuppID != '')
				{
					var type=1;
					var supp_ids='';
					var supp_arr=theemailsuppID.split(',');
					var supp_arr_length=supp_arr.length;					
					for (s=0; s<supp_arr_length; s++)
					{
						var sid = supp_arr[s];
						if (supp_ids=="") supp_ids= sid; 
						else supp_ids +=','+sid;
					}
					$('#txt_supp_id_'+sl+'_'+row_num).val(supp_ids);
					get_php_form_data( sl+"**"+row_num+"**"+theemailDtlsID+"**"+supp_ids+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_general_controller" );				
				}
				else
				{
					var type=2;
					var txt_dtlsid=$('#txt_dtlsid_'+sl+'_'+row_num).val();
					var supp_ids=$('#txt_supp_id_'+sl+'_'+row_num).val();
					get_php_form_data( sl+"**"+row_num+"**"+txt_dtlsid+"**"+supp_ids+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_general_controller" );
				}	


				
				var tot_row=$('#tbl_cs_list tbody tr').length;

				if (theemailID != '')
				{
					if ($('#booking_no_'+sl+'_'+row_num).val() == cs_number){
						$('#tbl_'+sl).prop("checked", true);
					}
		
				}
			}
		}

		
		function fn_cs_check()
		{
			if ($(copy_cs).is(":checked"))
			{
				var type=1;
				var tbl_row_count = $("#first_cs_number_count").val();
				$('#tbl_1_1').prop("checked", true);

				var txtSuppCompName=$("#txt_supp_comp_name_1_1").val();		
				var txtSuppCompDtlsid=$("#txt_supp_comp_dtlsid_1_1").val();
				var txtDtlsid=$("#txt_dtlsid_1_1").val();
				var txtSuppid=$("#txt_supp_id_1_1").val();
				get_php_form_data( txtDtlsid+"**"+txtSuppid+"**"+txtSuppCompDtlsid+"**"+tbl_row_count+"**"+type, "populate_data_from_copy_cs_check", "requires/cs_approval_general_controller" );

				var sl=1;
				for( var i = 2; i <= tbl_row_count; i++ ) 
				{
					var row_num=i;
					var txtDtlsidWithoutFirstRow=$("#txt_dtlsid_1_"+i).val();
					var txtSuppidWithoutFirstRow=$("#txt_supp_id_1_"+i).val();
					get_php_form_data( sl+"**"+row_num+"**"+txtDtlsidWithoutFirstRow+"**"+txtSuppidWithoutFirstRow+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_general_controller" );
				}
			}
			else
			{
				var tbl_row_count = $("#first_cs_number_count").val();
				var sl=1;
				for( var i = 1; i <= tbl_row_count; i++ ) 
				{
					var type=2;
					var row_num=i;
					var txtDtlsid=$("#txt_dtlsid_1_"+i).val();
					var txtSuppid=$("#txt_supp_id_1_"+i).val();
					get_php_form_data( sl+"**"+row_num+"**"+txtDtlsid+"**"+txtSuppid+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_general_controller" );
					$("#txt_supp_comp_name_1_"+i).val('');
					$("#txt_supp_comp_dtlsid_1_"+i).val('');
					$("#txt_dtlsid_1_"+i).val('');
					$("#txt_supp_id_1_"+i).val('');
				}

				for( var i = 1; i <= tbl_row_count; i++ )
				{
					$('#tbl_'+i).prop("checked", false);			
				}				
			}	
		}
	</script>
	<style type="text/css">
		.wrd_brk{.word-break: break-all; word-wrap: break-word;}
	</style>
	<div id="xyz">
    <form name="csApproval_2" id="csApproval_2">
    	<fieldset style="width: <?= $tbl_width; ?>px; margin-top:10px;">
        <legend style="width: <?= $tbl_width; ?>px;">CS Approval [General]</legend>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $tbl_width; ?>" class="rpt_table" id="tbl_cs_list">
        	<thead>
        		<tr>
        			<th colspan="11">&nbsp;</th>
        			<? if ( $approval_type==1 ) { ?>
        			<th width="150"></th>
        			<? } else { ?>
        			<th width="150"><input type="checkbox" id="copy_cs" onchange="fn_cs_check()"/>&nbsp;&nbsp;Copy CS</th>
        			<? } ?>
        			<th width="80">&nbsp;</th>
        			<?
					foreach($supplierArr as $supp_id)
					{
						?>
	                	<th colspan="3" width="240">&nbsp;</th>
	                	<?
	                }
	                ?>
        			<th colspan="2">&nbsp;</th> 
        		</tr>
        		<tr>
        			<th width="30">&nbsp;</th>
	                <th width="30">SL</th>
	                <th width="80">CS No</th>
	                <th width="60">CS Year</th>
	                <th width="80">CS Date</th>
	                <th width="100">Item Category</th>
	                <th width="100">Items Group</th>
	                <th width="100">Items Code</th>
	                <th width="150">Items Description</th>
	               	<th width="80">Req. Qty.</th>
	               	<th width="60">UOM</th>
	                <th width="150" style="color: blue;">Supplier</th>
	                <th width="80">Status</th>
	                <?
					foreach($supplierArr as $supp_id)
					{
						?>
	                	<th colspan="3" width="240"><?= $supplier_library[$supp_id]; ?></th>
	                	<?
	                }
	                ?>                
	                <th width="80">Approved Date</th>
	               	<th width="100">CS Insert By</th>
        		</tr>      		
        	</thead>
        	<tbody>
                <?
                $row_num=0;
                $sl=0;
                //$del_row=1;
                foreach ($sql_res as $row) 
                {
                	$dtls_id=$row['DTLS_ID'];
                	$supp_comp_dtls_ids = rtrim($supp_comp_dtls_arr[$row["DTLS_ID"]]['ID'],',');
                	$supp_comp_name = rtrim($supp_comp_dtls_arr[$row["DTLS_ID"]]['NAME'],',');
                	$suppID = rtrim($supp_id_dtls_arr[$row["DTLS_ID"]],',');
                	$count_cs_dtls_row=$row['DTLS_ID'];

                	$ex_prod_id=explode(',', $row['PROD_ID']);
                	foreach ($ex_prod_id as $prod_id) {
                		$item_description=$item_des_sql_arr[$prod_id]['ITEM_DESCRIPTION'];
                		$item_code=$item_des_sql_arr[$prod_id]['ITEM_CODE'];
                		$order_uom=$item_des_sql_arr[$prod_id]['ORDER_UOM'];
                	}                	
                	
					//if ($sl%2==0) $bgcolor="#E9F3FF"; 
					//else $bgcolor="#FFFFFF";
					$bgcolor="#E9F3FF";
					//if ($row_num==0) $row_num=1;
                	?>    
					<tr bgcolor="<?= $bgcolor; ?>" id="tr_<?= $row_num; ?>" align="center">
						<? 
						$row_num++;
						if ($check_cs_number[$row['CS_NUMBER']]=="")
						{
							$sl++;
							?>
							<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]*4; ?>" width="30" align="center" style="vertical-align:top;"><input type="checkbox" id="tbl_<?= $sl; ?>" name="tbl[]" value="<?= $sl; ?>"/></td>
							<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]*4; ?>" width="30"  class="wrd_brk" style="vertical-align:top;" align="center"><?= $sl; ?></td>
                        	<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]*4; ?>" width="80" class="wrd_brk" style="vertical-align:top;"><p><a href='##' style='color:#000' onClick="print_report(<? echo $row['ID']; ?>,'comparative_statement_print', '../commercial/work_order/requires/comparative_statement_controller')"><? echo $row['CS_NUMBER_PREFIX_NUM']; ?></a></p></td>
                        	<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]*4; ?>" width="60" class="wrd_brk" style="vertical-align:top;" align="left"><p><?= $row['CS_YEAR']; ?></p></td>
                        	<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]*4; ?>" width="80"  class="wrd_brk" style="vertical-align:top;" align="left"><p><?= change_date_format($row['CS_DATE']); ?></p></td>
                        	<?                         	
                        	$check_cs_number[$row['CS_NUMBER']]=$row['CS_NUMBER']; 
                        	$row_num=1;
                        } 
                        ?>
                        <td rowspan="4" width="100" class="item_class_<?= $sl; ?>  wrd_brk" style="vertical-align:middle;" align="left" id="item_category_id_<?= $sl.'_'.$row_num; ?>" title="<?= $row['ITEM_CATEGORY_ID']; ?>"><p><?= $item_category[$row['ITEM_CATEGORY_ID']]; ?></p>
                        	<input id="first_cs_number_count" name="first_cs_number_count" type="hidden" value="<?= $first_cs_count_row; ?>" />
							<input id="booking_id_<?= $sl.'_'.$row_num; ?>" name="booking_id[]" type="hidden" value="<?=$row['ID']; ?>" />
							<input id="booking_no_<?= $sl.'_'.$row_num; ?>" name="booking_no[]" type="hidden" value="<?=$row['CS_NUMBER']; ?>" />
							<input id="booking_dtlsid_<?= $sl.'_'.$row_num; ?>" name="booking_dtlsid[]" type="hidden" value="<?= $row['DTLS_ID']; ?>" />
							<input id="approval_id_<?= $sl.'_'.$row_num; ?>" name="approval_id[]" type="hidden" value="<?= $row['APPROVAL_ID']; ?>" />				
							<input id="supplier_id_<?= $sl.'_'.$row_num; ?>" name="supplier_id[]" type="hidden" value="<?= $row['SUPP_ID']; ?>" />				
							<input id="brand_name_<?= $sl.'_'.$row_num; ?>" name="brand_name[]" type="hidden" value="<?= $row['BRAND']; ?>" />			
							<input id="model_name_<?= $sl.'_'.$row_num; ?>" name="model_name[]" type="hidden" value="<?= $row['MODEL']; ?>" />			
							<input id="origin_name_<?= $sl.'_'.$row_num; ?>" name="origin_name[]" type="hidden" value="<?= $row['ORIGIN']; ?>" />	
                        </td>
                        <td rowspan="4" width="100"  class="wrd_brk" style="vertical-align:middle;" align="left" id="item_group_id_<?= $sl.'_'.$row_num; ?>" title="<?= $row['ITEM_GROUP_ID']; ?>"><p><?= $itemGroup_library[$row['ITEM_GROUP_ID']]; ?></p></td>
                        <td rowspan="4" width="100" class="wrd_brk" style="vertical-align:middle;" align="left" id="item_code_<?= $sl.'_'.$row_num; ?>" title="<?= $item_code; ?>"><p><?= $item_code; ?></p></td>
                        <td rowspan="4" width="150"  class="wrd_brk" style="vertical-align:middle;" align="left" id="item_description_<?= $sl.'_'.$row_num; ?>" title="<?= $item_description; ?>"><p><?= $item_description; ?></p></td>
                        <td rowspan="4" width="80"  class="wrd_brk" style="vertical-align:middle;" align="right"><p><?= $row['REQ_QTY']; ?></p></td>
						<td rowspan="4" width="60"  class="wrd_brk" style="vertical-align:middle;" align="center" id="uom_<?= $sl.'_'.$row_num; ?>" title="<?= $order_uom; ?>"><p><?= $unit_of_measurement[$order_uom]; ?></p></td>

                    	<td rowspan="4" width="150"  class="wrd_brk" style="vertical-align:middle;" align="left"><p>
                        	<?
                        	$supp_comp_ids='';
                        	if ($row['SUPP_ID'] != '') $supp_comp_ids.=$row['SUPP_ID'];
                        	?>
							<input type="text" name="txt_supp_comp_name" id="txt_supp_comp_name_<?= $sl.'_'.$row_num; ?>" value="<?= $supp_comp_name; ?>" class="text_boxes" style="width:130px" placeholder="Browse Double Click" onDblClick="openmypage_supplier(<?= $sl; ?>,<?= $row_num; ?>,'<?= $row['CS_NUMBER']; ?>', '<?= $dtls_id; ?>');" readonly />
							<input type="hidden" name="txt_supp_id" id="txt_supp_id_<?= $sl.'_'.$row_num; ?>" value="<?= $row['SUPP_ID']; ?>"/>
							<input type="hidden" name="txt_supp_comp_dtlsid" id="txt_supp_comp_dtlsid_<?= $sl.'_'.$row_num; ?>" value="<?= $supp_comp_dtls_ids; ?>"/>
							<input type="hidden" name="txt_dtlsid" id="txt_dtlsid_<?= $sl.'_'.$row_num; ?>" value="<?= $dtls_id; ?>"/>

                        </p></td>

                        <td rowspan="2" width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Mode</p></td>
                        <?                        
						foreach ($supplierArr as $supp_id)
						{
							?>
		                	<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Brand</p></td>
		                	<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Model</p></td>
							<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Origin</p></td>							
		                	<?
		                }
		                ?>
						<td rowspan="4" width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p><?= change_date_format($row['APPROVED_DATE']) ?></p></td>
						<td rowspan="4" class="wrd_brk" style="vertical-align:middle;" align="center"><p><?= $user_arr[$row['CS_INSERT_BY']]; ?></p></td>
					</tr>
					<tr bgcolor="<?= $bgcolor; ?>" id="trbandrow_<?= $sl.'_'.$row_num; ?>">
						<?
						foreach ($supp_dtls_arr as $supp_dtl_id => $supp_data)
						{
							if ($dtls_id == $supp_dtl_id)
							{
								foreach ($supplierArr as $supplier_id) 
								{
									$k=0;
									foreach ($supp_data as $supp_id => $val)
									{
										if ($supplier_id==$supp_id)
										{
											$k++;
											?>
						                	<td width="80" class="wrd_brk" align="center"><p><?= $val['BRAND']; ?>&nbsp;</p></td>
											<td width="80" class="wrd_brk" align="center"><p><?= $val['MODEL']; ?>&nbsp;</p></td>
											<td width="80" class="wrd_brk" align="center"><p><?= $val['ORIGIN']; ?>&nbsp;</p></td>
											<?
				                		}	
				                	}
				                	if ($k==0)
				                	{
				                		?>
				                		<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>	
				                		<?
				                	}
				                }	
			                }
		                }	
		                ?>
					</tr>
					<tr bgcolor="<?= $bgcolor; ?>" id="trqutrow_<?= $sl.'_'.$row_num; ?>">
						<td rowspan="2" width="" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Price</p></td>
						<?
						foreach ($supplierArr as $supp_id)
						{	
							?>
		                	<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Quoted Price</p></td>
							<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Last Price</p></td>
							<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Con. Price</p></td>
		                	<?
		                }
		                ?>
					</tr>
					<tr bgcolor="<?= $bgcolor; ?>" id="trqutvalrow_<?= $sl.'_'.$row_num; ?>">
						<?
						foreach ($supp_dtls_arr as $supp_dtl_id => $supp_data)
						{
							if ($dtls_id == $supp_dtl_id)
							{
								foreach ($supplierArr as $supplier_id) 
								{
									$k=0;
									foreach ($supp_data as $supp_id => $val)
									{
										if ($supplier_id==$supp_id)
										{
											$k++;
											?>
						                	<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p><?= $val['QUOTED_PRICE']; ?>&nbsp;</p></td>
											<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p><?= $val['NEG_PRICE']; ?>&nbsp;</p></td>
											<? 
											if ($approval_type==1) 
											{	
												?>
												<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><?= $val['LAST_APPROVAL_RATE']; ?></td>
												<?
											}
											else
											{	
												?>
												<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">
													<input type="text" name="" id="txtApprovedPriceSpplier_<?= $sl.'_'.$row_num.'_'.$supp_id; ?>" class="text_boxes_numeric" style="width: 70px;" disabled value="<?= $val['LAST_APPROVAL_RATE']; ?>"/>
													<input type="hidden" name="" id="suppDtlsidSupplier_<?= $sl.'_'.$row_num.'_'.$supp_id; ?>" value="<?= $dtls_id; ?>"/>
												</td>
												<?
											}
											break;
										}	
				                	}
				                	if ($k==0)
				                	{
				                		?>
				                		<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>	
				                		<?
				                	}
				                }	
			                }
		                }	
		                ?>
					</tr>			
					<?
					//$del_row++;		
				}
				?>
				
            </tbody>
        </table>    
       <table cellspacing="0" cellpadding="0" width="200" align="left">
			<tfoot>
				<input type="hidden" id="item_wise_row_count" value="<? echo json_encode($arr); ?>">
                <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check',<?= $sl; ?>)" /></td>
                <td width="100" align="center"><input type="button" value="<? if ($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $sl; ?>,<? echo $approval_type; ?>)" /></td>
			</tfoot>
		</table>
        </fieldset>       
    </form>
    </div> 
	<?
	exit();
}

if($action=='user_popup')
{
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	?>
	<script>
	// flowing script for multy select data------------------------------------------------------------------------------start;
	function js_set_value(id)
	{
		// alert(id)
		document.getElementById('selected_id').value=id;
		parent.emailwindow.hide();
	}
	// avobe script for multy select data------------------------------------------------------------------------------end;
	</script>
	<form>
        <input type="hidden" id="selected_id" name="selected_id" />
       <?php
        $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');
		$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=0 and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by b.sequence_no";
			//echo $sql;
		$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
	</form>
	<?
	exit();
}



?>