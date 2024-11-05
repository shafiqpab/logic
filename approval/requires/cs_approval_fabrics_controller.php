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

	$construction_arr=array(); $composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			$construction_arr[$row[csf('id')]]=$row[csf('construction')];

			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}
	unset($data_array);

	//$main_group_library=return_library_array("select id, main_group_name from lib_main_group where status_active=1","id","main_group_name");
	//$item_group_library=return_library_array("select id, item_name from lib_item_group where status_active=1 and item_category=4","id","item_name");

	$sql_company=sql_select("select id, company_name, contact_no from lib_company");
	$company_library=array();
	$company_contactNo_library=array();
	foreach ($sql_company as $val) {
		$company_library[$val[csf('id')]]=$val[csf('company_name')];
	}

	$sql_supplier=sql_select("select id, supplier_name, contact_no from lib_supplier where status_active=1 and is_deleted=0");
	$supplier_library=array();
	$supplier_contactNo_library=array();
	foreach ($sql_supplier as $val) {
		$supplier_library[$val[csf('id')]]=$val[csf('supplier_name')];
	}
	//$supplier_library=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
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
	
		$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
		from req_comparative_mst a, req_comparative_dtls b, approval_history c 
		where a.id=b.mst_id and a.id=c.mst_id and a.entry_form=512 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=57 and c.current_approval_status=1 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond $sequence_no_cond
		group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.uom, b.weight, b.detarmination_id, b.req_rate, b.req_qty, c.id, c.approved_date
		order by a.id";
		//echo "$sql";
	}
	else if($approval_type==0)	// unapproval process start
	{

		if($user_sequence_no==$min_sequence_no)  // First user
		{

		 	$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.req_item_dtls_id as REQ_ITEM_DTLS_ID, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY 
		 	from req_comparative_mst a, req_comparative_dtls b 
		 	where a.id=b.mst_id and a.entry_form=512 and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 	group by a.id, a.sys_number, a.req_item_dtls_id, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.uom, b.weight, b.detarmination_id, b.req_rate,b.req_qty
			order by a.id";
			//echo $sql;
		}
		else // Next user
		{
			$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");

			if($sequence_no=="")  // bypass if previous user Yes
			{
				$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup where page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
				$seqData=sql_select($seqSql);
				
				$sequence_no_by=$seqData[0][csf('sequence_no_by')];
				
				$req_comp_id=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no in ($sequence_no_by) and c.entry_form=57 and c.current_approval_status=1 $cs_date_cond","req_comp_id");
				$req_comp_id=implode(",",array_unique(explode(",",$req_comp_id)));
				
				$req_comp_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no=$user_sequence_no and c.entry_form=57 and c.current_approval_status=1","req_comp_id");
				$req_comp_id_app_byuser=implode(",",array_unique(explode(",",$req_comp_id_app_byuser)));

				$result=array_diff(explode(',',$req_comp_id),explode(',',$req_comp_id_app_byuser));
				$req_comp_id=implode(",",$result);

				if($req_comp_id!="")
				{					
					$sql=" SELECT x.* from  (SELECT DISTINCT (a.id) as ID, a.sys_number as CS_NUMBER, a.req_item_dtls_id as REQ_ITEM_DTLS_ID, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY
					from req_comparative_mst a, req_comparative_dtls b
					where a.id=b.mst_id and a.entry_form=512 and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 			group by a.id, a.sys_number, a.req_item_dtls_id, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.uom, b.weight, b.detarmination_id, b.req_rate,b.req_qty

					UNION ALL

					SELECT DISTINCT (a.id) as ID, a.sys_number as CS_NUMBER, a.req_item_dtls_id as REQ_ITEM_DTLS_ID, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY 
					from req_comparative_mst a, req_comparative_dtls b
					where a.id=b.mst_id and a.entry_form=512 and a.approved in(1,3) and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($req_comp_id) $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 			group by a.id, a.sys_number, a.req_item_dtls_id, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.uom, b.weight, b.detarmination_id, b.req_rate, b.req_qty) x  order by x.ID";
					//echo $sql;
				}
				else
				{ 
					$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.req_item_dtls_id as REQ_ITEM_DTLS_ID, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY 
					from req_comparative_mst a, req_comparative_dtls b
					where a.id=b.mst_id and a.entry_form=512 and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 			group by a.id, a.sys_number, a.req_item_dtls_id, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.uom, b.weight, b.detarmination_id, b.req_rate, b.req_qty
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

				$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.req_item_dtls_id as REQ_ITEM_DTLS_ID, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
				from req_comparative_mst a, req_comparative_dtls b, approval_history c 
				where a.id=b.mst_id and a.id=c.mst_id and a.entry_form=512 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=57 and c.current_approval_status=1 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond $sequence_no_cond
				group by a.id, a.sys_number, a.req_item_dtls_id, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.uom, b.weight, b.detarmination_id, b.req_rate, b.req_qty, c.id, c.approved_date
				order by a.id";
				//echo $sql;
			}
		}	

	}
	else // approval process start
	{
		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";

		$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.req_item_dtls_id as REQ_ITEM_DTLS_ID, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
		from req_comparative_mst a, req_comparative_dtls b, approval_history c 
		where a.id=b.mst_id and a.id=c.mst_id and a.entry_form=512 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=57 and c.current_approval_status=1 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond $sequence_no_cond
		group by a.id, a.sys_number, a.req_item_dtls_id, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.weight, b.uom, b.detarmination_id, b.req_rate, b.req_qty, c.id, c.approved_date
		order by a.id";
	}

	//echo $sql;
	
	$sql_res=sql_select($sql);
	$company_arr=array();
	$selected_prod_id="";
	foreach($sql_res as $row)
	{
		$mst_id.=$row["ID"].",";
		if ($row["COMPANY_ID"] != ""){
			$company_arr[$row["CS_NUMBER"]]=$row["COMPANY_ID"];
		}
		
		$supplier_arr[$row["CS_NUMBER"]]=$row["SUPP_ID"];
		$dtls_row_arr[$row["CS_NUMBER"]].=$row["DTLS_ID"].',';
		$cs_number_arr[$row["CS_NUMBER"]].=$row["CS_NUMBER"].',';
		$req_item_dtls_id.=$row["REQ_ITEM_DTLS_ID"].',';
		$rowspan_arr[$row["CS_NUMBER"]]++;
	}
	//echo implode(",", $company_arr);
	//echo '<pre>';print_r($company_arr);

	$first_cs_count_row=count(explode(',',rtrim(reset($dtls_row_arr),',')));
	if (!empty($company_arr)){
		$companyArr=array_unique(explode(",", implode(",", $company_arr)));
		$company_count=count($companyArr);
	}
	
	//echo '<pre>'; print_r($companyArr);
	if ($company_count > 0) $company_width=$company_count*320;
	else $company_width=0;

	$supplierArr=array_unique(explode(",", implode(",", $supplier_arr)));
	$supplier_width=count($supplierArr)*320;
	//echo count($supplierArr);

	$mst_ids = implode(',', array_flip(array_flip(explode(',', rtrim($mst_id,',')))));
	$req_item_dtls_ids = implode(',', array_flip(array_flip(explode(',', rtrim($req_item_dtls_id,',')))));

	$sql_fabrics_res=sql_select("SELECT a.id as ID, a.item_category_id as ITEM_CATEGORY_ID, a.detarmination_id as DETARMINATION_ID, a.weight as WEIGHT, a.uom as UOM, b.id as FABRIC_COST_ID, b.nominated_supp_multi as NOMINATED_SUPPLIER from req_comparative_dtls a, wo_pre_cost_fabric_cost_dtls b where a.fabric_cost_dtls_id=b.id and a.mst_id in($mst_ids) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.id asc");
	foreach ($sql_fabrics_res as $row) {
		//$key=$row['ITEM_CATEGORY_ID'].'**'.$row['DETARMINATION_ID'].'**'.$row['WEIGHT'].'**'.$row['UOM'];
		if ($row["NOMINATED_SUPPLIER"] != ""){
			$nominated_supp.=$row["NOMINATED_SUPPLIER"].',';
		}		
	}
	$nominated_supp_arr=explode(",",rtrim($nominated_supp,','));
	//echo '<pre>';print_r($nominate_supplier_arr);
	
	$supp_dtls="SELECT b.req_qty as REQ_QTY, c.id as ID, c.mst_id as MST_ID, c.dtls_id as DTLS_ID, c.supp_id as SUPP_ID, c.supp_type as SUPP_TYPE, c.prod_id as PROD_ID, c.quoted_price as QUOTED_PRICE, c.neg_price as NEG_PRICE, c.con_price as CON_PRICE, c.last_approval_rate as LAST_APPROVAL_RATE, c.brand as BRAND, c.model as MODEL, c.origin as ORIGIN, c.approved as APPROVED, c.is_recommend as IS_RECOMMEND from req_comparative_dtls b, req_comparative_supp_dtls c where b.id=c.dtls_id and b.is_deleted=0 and b.status_active=1 and c.mst_id in($mst_ids) and c.is_deleted=0 and c.status_active=1 order by c.id asc";	
	$supp_dtls_res=sql_select($supp_dtls);
	$supp_dtls_arr=array();
	$comp_dtls_arr=array();
	$supp_comp_dtls_arr=array();
	$supp_id_dtls_arr=array();
	$comp_id_dtls_arr=array();
	$recommend_supplier_arr=array();
	foreach ($supp_dtls_res as $row) {
		// SUPP_TYPE=2(company) and SUPP_TYPE=1(supplier)        
		if ($row["SUPP_TYPE"] == 1)
		{
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['QUOTED_PRICE']=$row["QUOTED_PRICE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['LAST_APPROVAL_RATE']=$row["LAST_APPROVAL_RATE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['CON_PRICE']=$row["CON_PRICE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['NEG_PRICE']=$row["NEG_PRICE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['REQ_QTY']=$row["REQ_QTY"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['TOTAL_VALUE']=$row["NEG_PRICE"]*$row["REQ_QTY"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['SUPP_TYPE']=$row["SUPP_TYPE"];
			if ($row["APPROVED"] == 1) 
			{
				$supp_comp_dtls_arr[$row["DTLS_ID"]]['ID'].=$row["ID"].',';
				$supp_comp_dtls_arr[$row["DTLS_ID"]]['NAME'].=$supplier_library[$row["SUPP_ID"]].',';
				$supp_id_dtls_arr[$row["DTLS_ID"]].=$row["SUPP_ID"].',';
			}

			$recommend_supplier_arr[$row["SUPP_ID"]]=$row["IS_RECOMMEND"];
		}
		else if ($row["SUPP_TYPE"] == 2)
		{
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['QUOTED_PRICE']=$row["QUOTED_PRICE"];
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['LAST_APPROVAL_RATE']=$row["LAST_APPROVAL_RATE"];
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['CON_PRICE']=$row["CON_PRICE"];
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['NEG_PRICE']=$row["NEG_PRICE"];
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['REQ_QTY']=$row["REQ_QTY"];
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['TOTAL_VALUE']=$row["NEG_PRICE"]*$row["REQ_QTY"];
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['SUPP_TYPE']=$row["SUPP_TYPE"];
			if ($row["APPROVED"] == 1) 
			{
				$supp_comp_dtls_arr[$row["DTLS_ID"]]['ID'].=$row["ID"].',';
				$supp_comp_dtls_arr[$row["DTLS_ID"]]['NAME'].=$company_library[$row["SUPP_ID"]].',';
				$comp_id_dtls_arr[$row["DTLS_ID"]].=$row["SUPP_ID"].',';
			}
		}		
	}
	//echo '<pre>';print_r($supp_comp_dtls_arr);die;
	$tbl_width=988+$company_width*1+$supplier_width*1;
	?>
	<script>

		function openmypage_supplier(sl, row_num, cs_number, dtls_id)
		{
			var txt_supp_comp_dtlsid= $('#txt_supp_comp_dtlsid_'+sl+'_'+row_num).val();
			var txt_dtlsid= $('#txt_dtlsid_'+sl+'_'+row_num).val();
			var page_link="requires/cs_approval_fabrics_controller.php?action=supplier_popup&txt_supp_comp_dtlsid="+txt_supp_comp_dtlsid+"&txt_dtlsid="+txt_dtlsid+"&dtls_id="+dtls_id;

			var title="Supplier Info";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{

				var theemailID=this.contentDoc.getElementById("hid_supp_comp_dtlsid").value;	
				var theemailVAL=this.contentDoc.getElementById("hid_supp_comp_name").value;
				var theemailDtlsID=this.contentDoc.getElementById("hid_dtlsid").value;
				var theemailsuppID=this.contentDoc.getElementById("hid_supp_id").value;
				var theemailcompID=this.contentDoc.getElementById("hid_comp_id").value;
				$('#txt_supp_comp_dtlsid_'+sl+'_'+row_num).val(theemailID);
				$('#txt_supp_comp_name_'+sl+'_'+row_num).val(theemailVAL);
				if (theemailDtlsID != ""){
					$('#txt_dtlsid_'+sl+'_'+row_num).val(theemailDtlsID);
				}

				if (theemailsuppID != '' || theemailcompID != '')
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

					var comp_ids='';
					var comp_arr=theemailcompID.split(',');
					var comp_arr_length=comp_arr.length;						
					for (c=0; c<comp_arr_length; c++)
					{
						var cid = comp_arr[c];
						if (comp_ids=="") comp_ids= cid; 
						else comp_ids +=','+cid;
					}				
					$('#txt_comp_id_'+sl+'_'+row_num).val(comp_ids);
					

					get_php_form_data( sl+"**"+row_num+"**"+theemailDtlsID+"**"+supp_ids+"**"+comp_ids+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_fabrics_controller" );				
				}
				else
				{
					var type=2;
					var txt_dtlsid=$('#txt_dtlsid_'+sl+'_'+row_num).val();
					var supp_ids=$('#txt_supp_id_'+sl+'_'+row_num).val();
					var comp_ids=$('#txt_comp_id_'+sl+'_'+row_num).val();
					get_php_form_data( sl+"**"+row_num+"**"+txt_dtlsid+"**"+supp_ids+"**"+comp_ids+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_fabrics_controller" );
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
				var txtCompid=$("#txt_comp_id_1_1").val();
				get_php_form_data( txtDtlsid+"**"+txtSuppid+"**"+txtCompid+"**"+txtSuppCompDtlsid+"**"+tbl_row_count+"**"+type, "populate_data_from_copy_cs_check", "requires/cs_approval_fabrics_controller" );

				var sl=1;
				for( var i = 2; i <= tbl_row_count; i++ ) 
				{					
					var row_num=i;
					var txtDtlsidWithoutFirstRow=$("#txt_dtlsid_1_"+i).val();
					var txtSuppidWithoutFirstRow=$("#txt_supp_id_1_"+i).val();
					var txtCompidWithoutFirstRow=$("#txt_comp_id_1_"+i).val();
					get_php_form_data( sl+"**"+row_num+"**"+txtDtlsidWithoutFirstRow+"**"+txtSuppidWithoutFirstRow+"**"+txtCompidWithoutFirstRow+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_fabrics_controller" );
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
					var txtCompid=$("#txt_comp_id_1_"+i).val();
					get_php_form_data( sl+"**"+row_num+"**"+txtDtlsid+"**"+txtSuppid+"**"+txtCompid+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_fabrics_controller" );
					$("#txt_supp_comp_name_1_"+i).val('');
					$("#txt_supp_comp_dtlsid_1_"+i).val('');
					$("#txt_dtlsid_1_"+i).val('');
					$("#txt_supp_id_1_"+i).val('');
					$("#txt_comp_id_1_"+i).val('');
				}

				for( var i = 1; i <= tbl_row_count; i++ )
				{
					$('#tbl_'+i).prop("checked", false);			
				}				
			}	
		}

		function calculate_value()
	    {
			$('input[name="tbl[]"]:checked').each(function()
			{
				var i=$(this).attr("value");
				var item_row=$(".item_class_"+i).length;
				for(j=1; j<=item_row; j++)
				{
					var supplier_id = $('#txt_supp_id_'+i+'_'+j).val();
					var col_num_arr = supplier_id.split(',');
		        	var col_num = col_num_arr.length; 

					var company_name_id = $('#txt_comp_id_'+i+'_'+j).val();
					var company_num_arr = company_name_id.split(',');
		        	if(company_name_id!=''){var company_num = company_num_arr.length}else{var company_num =0;}	        

					var supp_neg_price=com_neg_price="";
					var total_value="";
	            	var req_qty = $('#txtreqqty_'+i+'_'+j).attr('title')*1;

					for (var m=0; m<col_num; m++)
					{
						var mm=col_num_arr[m];
	               		supp_neg_price = $('#txtApprovedPriceSpplier_'+ i+'_'+j+'_'+ mm).val()*1;
						total_value = supp_neg_price*req_qty;
						if (total_value==0) $('#txttotalvalue_'+ i+'_'+j+'_'+ mm).html("");
	                	else $('#txttotalvalue_'+ i+'_'+j+'_'+ mm).html(total_value.toFixed(2));
					}

					for (var m=0; m<company_num; m++)
					{
						var mm=company_num_arr[m];
						com_neg_price = $('#txtApprovedPriceCompany_'+ i+'_'+j+'_'+ mm).val()*1;
						total_value = com_neg_price*req_qty;
						if (total_value==0) $('#txtCompanyTotalValue_'+ i+'_'+j+'_'+ mm).html("");
	                	else $('#txtCompanyTotalValue_'+ i+'_'+j+'_'+ mm).html(total_value.toFixed(2));
					}
				}
			});
	    }

		function chkNegPrice(val,id,data)
		{
			var dataInfo=data.split('**');
			var sl=dataInfo[0]*1;
			var row_num=dataInfo[1]*1;
			var supp_comp_id=dataInfo[2]*1;
			var costPrice=dataInfo[3]*1;
			var reqQuantity=$("#txtreqqty_"+sl+"_"+row_num).val()*1;

			if(id==1) //Company
			{
				if(val>costPrice)
				{
					alert("Costing Price will be equal or less than … not allow higher");
					$("#txtApprovedPriceCompany_"+sl+"_"+row_num+"_"+supp_comp_id).val('');
					$("#txtCompanyTotalValue_"+sl+"_"+row_num+"_"+supp_comp_id).val('');
				}
				
				var companyTotalValue=reqQuantity*$("#txtApprovedPriceCompany_"+sl+"_"+row_num+"_"+supp_comp_id).val();
				$("#txtCompanyTotalValue_"+sl+"_"+row_num+"_"+supp_comp_id).val(companyTotalValue.toFixed(2));
			}
			if(id==2) //Supplier
			{
				if(val>costPrice)
				{
					alert("Costing Price will be equal or less than … not allow higher");
					$("#txtApprovedPriceSpplier_"+sl+"_"+row_num+"_"+supp_comp_id).val('');
					$("#txttotalvalue_"+sl+"_"+row_num+"_"+supp_comp_id).val('');
				}

				var totalvalue=reqQuantity*$("#txtApprovedPriceSpplier_"+sl+"_"+row_num+"_"+supp_comp_id).val();
				$("#txttotalvalue_"+sl+"_"+row_num+"_"+supp_comp_id).val(totalvalue.toFixed(2));
			}
		}

		function fn_price_check(type,id)
		{
			var rownum = $('#tbl_details tbody tr').length;
			//alert(rownum);
			if (type==1)  //company
			{
				var company_check = $("#txt_company_check_"+id).is(":checked");
				for( var i=1; i<=rownum; i++)
				{
					var costPrice=$("#txtRate_"+i).val()*1;
					var reqQuantity=$("#txtQty_"+i).val()*1;
					var txtCompanyNeg=$("#txtCompanyNeg_"+i+'_'+id).val()*1;

					var companyTotalValue=reqQuantity*txtCompanyNeg;
					$("#txtCompanyTotalValue_"+i+'_'+id).val(companyTotalValue.toFixed(2));

					if (company_check==false){
						if(txtCompanyNeg>costPrice){                        
							alert("Costing Price will be equal or less than … not allow higher");
							$("#txtCompanyNeg_"+i+'_'+id).val('');
							$("#txtCompanyTotalValue_"+i+'_'+id).val('');
						}                   
					}         
					
				}
			}
			else  //Supplier
			{
				var supplier_check = $("#txt_supplier_check_"+id).is(":checked");
				for( var i=1; i<=rownum; i++)
				{
					var costPrice=$("#txtRate_"+i).val()*1;
					var reqQuantity=$("#txtQty_"+i).val()*1;
					var txtneg=$("#txtneg_"+i+'_'+id).val()*1;

					var totalvalue=reqQuantity*txtneg;
					$("#txttotalvalue_"+i+'_'+id).val(totalvalue.toFixed(2));
					
					if (supplier_check==false){
						if(txtneg>costPrice){
							alert("Costing Price will be equal or less than … not allow higher");
							$("#txtneg_"+i+'_'+id).val('');
							$("#txttotalvalue_"+i+'_'+id).val('');
						}                   
					}                
				}
			}
		}
	</script>
	<style type="text/css">
		.wrd_brk{.word-break: break-all; word-wrap: break-word;}
	</style>
    <form name="csApproval_2" id="csApproval_2">
    	<fieldset style="width: <?= $tbl_width; ?>px; margin-top:10px;">
        <legend style="width: <?= $tbl_width; ?>px;">CS Approval [Fabrics]</legend>
        <table cellspacing="0" cellpadding="0" rules="all" width="<?= $tbl_width; ?>" class="rpt_table" align="left" id="tbl_cs_list">
        	<thead>
        		<tr>
	        		<th colspan="9">&nbsp;</th>
	        		<? if ( $approval_type==1 ) { ?>
	        		<th width="150"></th>
	        		<? } else { ?>
	        		<th width="150"><input type="checkbox" id="copy_cs" onchange="fn_cs_check()"/>&nbsp;&nbsp;Copy CS</th>
	        		<?
	        		}
	        		if ($company_count > 0)
	        		{
		                foreach($companyArr as $comp_id)
						{
							?>
		                	<th colspan="4" width="320"><?= $company_library[$comp_id]; ?></th>
		                	<?
		                }
	            	}
					foreach($supplierArr as $supp_id)
					{
						?>
	                	<th colspan="4" width="320"><?= $supplier_library[$supp_id]; ?>&nbsp;&nbsp;<? if ($recommend_supplier_arr[$supp_id]==1) echo "Recommend"; ?></th>
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
	                <th width="150">Items Description</th>
	               	<th width="80">Req. Qty.</th>
	               	<th width="60">UOM</th>
					<th width="60">Costing Price</th>
	                <th width="150" style="color: blue;">Supplier</th> 
					<?
	                if ($company_count > 0 )
					{
	                    foreach ($companyArr as $comp_id)
						{
							?>
		                	<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Quoted Price</p></th>
							<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Last Price</p></th>
							<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Neg. Price</p></th>
							<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Total Value</p></th>
		                	<?
		                }
	            	}
					foreach ($supplierArr as $supp_id)
					{
						?>
	                	<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Quoted Price</p></th>
						<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Last Price</p></th>
						<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Neg. Price</p></th>
						<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Total Value</p></th>
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
                $dtls_idArr_itemWise=array();
                foreach ($sql_res as $row) 
                {
                	$dtls_id=$row['DTLS_ID'];
                	$supp_comp_dtls_ids = rtrim($supp_comp_dtls_arr[$row["DTLS_ID"]]['ID'],',');
                	$supp_comp_name = rtrim($supp_comp_dtls_arr[$row["DTLS_ID"]]['NAME'],',');
                	$suppID = rtrim($supp_id_dtls_arr[$row["DTLS_ID"]],',');
                	$compID = rtrim($comp_id_dtls_arr[$row["DTLS_ID"]],',');
                	$count_cs_dtls_row=$row['DTLS_ID'];
                	//$item=$row["ITEM_GROUP_ID"]."**".$row["ITEM_REF"]."**".$row["ITEM_DESCRIPTION"]."**".$row["UOM"];
                	//if ($item==$nominate_supplier_arr[$item])
					$req_rate=$row['REQ_RATE'];
                	
					$bgcolor="#E9F3FF"; 					
                	?>
					<tr bgcolor="<?= $bgcolor; ?>" id="tr_<?= $i; ?>" align="center" >
						<?
						$row_num++;
						if ($check_cs_number[$row['CS_NUMBER']]=="")
						{
							$sl++;
							?>
							<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]; ?>" width="30" align="center" valign="middle" style="vertical-align:top;"><input type="checkbox" id="tbl_<?= $sl; ?>" name="tbl[]" value="<?= $sl; ?>"/></td>
							<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]; ?>" width="30"  class="wrd_brk" align="center" style="vertical-align:top;"><?= $i; ?></td>
							<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]; ?>" width="80" class="wrd_brk" style="vertical-align:top;"><p><a href='##' style='color:#000' onClick="print_report(<? echo $row['ID']; ?>,'comparative_statement_print', '../commercial/work_order/requires/comparative_statement_fabrics_controller')"><? echo $row['CS_NUMBER_PREFIX_NUM']; ?></a></p></td>
							<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]; ?>" width="60" class="wrd_brk" style="vertical-align:top;" align="left"><p><?= $row['CS_YEAR']; ?></p></td>                        
                        	<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]; ?>" width="80"  class="wrd_brk" style="vertical-align:top;" align="left"><p><?= change_date_format($row['CS_DATE']); ?></p></td>
							<?                         	
                        	$check_cs_number[$row['CS_NUMBER']]=$row['CS_NUMBER']; 
                        	$row_num=1;
                        } 
                        ?>                      
                        <td width="150" class="item_class_<?= $sl; ?>" style="vertical-align:middle;" align="left" id="item_description_<?= $sl.'_'.$row_num; ?>" title="<?= $construction_arr[$row['DETARMINATION_ID']].', '.$composition_arr[$row['DETARMINATION_ID']].', '.$row['WEIGHT']; ?>"><p><?= $row['ITEM_DESCRIPTION']; ?></p>
                        	<input id="first_cs_number_count" name="first_cs_number_count" type="hidden" value="<?= $first_cs_count_row; ?>" />
							<input id="booking_id_<?= $sl.'_'.$row_num; ?>" name="booking_id[]" type="hidden" value="<?= $row['ID']; ?>" />
							<input id="booking_no_<?= $sl.'_'.$row_num; ?>" name="booking_no[]" type="hidden" value="<?= $row['CS_NUMBER']; ?>" />
							<input id="booking_dtlsid_<?= $sl.'_'.$row_num; ?>" name="booking_dtlsid[]" type="hidden" value="<?= $row['DTLS_ID']; ?>" />
							<input id="approval_id_<?= $sl.'_'.$row_num; ?>" name="approval_id[]" type="hidden" value="<?= $row['APPROVAL_ID']; ?>" />
							<input id="detarmination_id_<?= $sl.'_'.$row_num; ?>" name="detarmination_id[]" type="hidden" value="<?= $row['DETARMINATION_ID']; ?>" />
							<input id="txtweight_<?= $sl.'_'.$row_num; ?>" name="txtweight[]" type="hidden" value="<?= $row['WEIGHT']; ?>" />
							<input id="supplier_id_<?= $sl.'_'.$row_num; ?>" name="supplier_id[]" type="hidden" value="<?= $row['SUPP_ID']; ?>" />
							<input id="company_id_<?= $sl.'_'.$row_num; ?>" name="company_id[]" type="hidden" value="<?= $row['COMPANY_ID']; ?>" />
						</td>
                        <td width="80" class="wrd_brk" style="vertical-align:middle;" align="right" id="txtreqqty_<?= $sl.'_'.$row_num; ?>" title="<?= $row['REQ_QTY']; ?>"><p><?= number_format($row['REQ_QTY'],2,'.',''); ?></p></td>
						<td width="60" class="wrd_brk" style="vertical-align:middle;" align="center" id="uom_<?= $sl.'_'.$row_num; ?>" title="<?= $row['UOM']; ?>"><p><?= $unit_of_measurement[$row['UOM']]; ?></p></td>
						<td width="60" class="wrd_brk" style="vertical-align:middle;" align="center" id="costingprice_<?= $sl.'_'.$row_num; ?>" title="<?= $req_rate; ?>"><p><?= $req_rate; ?></p></td>

                        <td width="150"  class="wrd_brk" style="vertical-align:middle;" align="left"><p>
                        	<?
                        	$supp_comp_ids='';
                        	if ($row['SUPP_ID'] != '') $supp_comp_ids.=$row['SUPP_ID'];
                        	if ($row['COMPANY_ID'] != '') $supp_comp_ids.=','.$row['COMPANY_ID'];
                        	?>
							<input type="text" name="txt_supp_comp_name[]" id="txt_supp_comp_name_<?= $sl.'_'.$row_num; ?>" value="<?= $supp_comp_name; ?>" class="text_boxes" style="width:130px" placeholder="Browse Double Click" onDblClick="openmypage_supplier(<?= $sl; ?>,<?= $row_num; ?>,'<?= $row['CS_NUMBER']; ?>', '<?= $dtls_id; ?>');" readonly />
							<input type="hidden" name="txt_supp_id[]" id="txt_supp_id_<?= $sl.'_'.$row_num; ?>" value="<?= $suppID; ?>"/>
							<input type="hidden" name="txt_comp_id[]" id="txt_comp_id_<?= $sl.'_'.$row_num; ?>" value="<?= $compID; ?>"/>
							<input type="hidden" name="txt_supp_comp_dtlsid[]" id="txt_supp_comp_dtlsid_<?= $sl.'_'.$row_num; ?>" value="<?= $supp_comp_dtls_ids; ?>"/>
							<input type="hidden" name="txt_dtlsid[]" id="txt_dtlsid_<?= $sl.'_'.$row_num; ?>" value="<?= $dtls_id; ?>"/>
                        </p></td>
                        <?                        
						if ($company_count > 0)
						{
							foreach ($comp_dtls_arr as $comp_dtl_id => $comp_data)
							{
								if ($dtls_id == $comp_dtl_id)
								{
									foreach ($companyArr as $company_id) 
									{
										$k=0;											
										foreach ($comp_data as $comp_id => $val)
										{
											if ($company_id==$comp_id)
											{
												$k++;
												?>
							                	<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><?= $val['QUOTED_PRICE']; ?>&nbsp;</td>
												<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><?= $val['CON_PRICE']; ?>&nbsp;</td>
												<? 
												if ($approval_type==1) 
												{	
													?>
													<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><?= $val['LAST_APPROVAL_RATE']; ?></td>
													<?
												}
												else
												{
													//if ($val['NEG_PRICE'] == "") $disabled ="disabled";
													?>
													<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">
														<input type="text" name="" id="txtApprovedPriceCompany_<?= $sl.'_'.$row_num.'_'.$comp_id; ?>" class="text_boxes_numeric" style="width: 70px;" disabled value="<?= $val['LAST_APPROVAL_RATE']; ?>" onKeyUp="chkNegPrice(this.value,1,'<? echo $sl.'**'.$row_num.'**'.$comp_id.'**'.$req_rate; ?>');"/></td>
														<input type="hidden" name="" id="suppDtlsidCompany_<?= $sl.'_'.$row_num.'_'.$comp_id; ?>" value="<?= $dtls_id; ?>" />
													</td>	
													<?
												}
												?>
												<td width="80" class="wrd_brk" style="vertical-align:middle;" id="txtCompanyTotalValue_<?= $sl.'_'.$row_num.'_'.$comp_id; ?>"  align="center"><? if ($val['TOTAL_VALUE'] != 0) echo number_format($val['TOTAL_VALUE'],2,'.',''); else echo ""; ?>&nbsp;</td>
												<?
						                		//break;
						                	}				                		

					                	}
					                	if ($k==0)
					                	{
					                		?>
					                		<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
											<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
											<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
											<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>	
					                		<?
					                	}						                		
					                }	
				                }					                	
			                } 
		            	}

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
											$background_color="";
											if (in_array($supp_id,$nominated_supp_arr)) $background_color = "background-color: yellow";
											//if ($val['NEG_PRICE'] == "") $disabled ="disabled";											
											?>
						                	<td width="80" class="wrd_brk" style="vertical-align:middle; <? echo $background_color; ?>" align="center" ><?= $val['QUOTED_PRICE']; ?>&nbsp;</td>
											<td width="80" class="wrd_brk" style="vertical-align:middle; <? echo $background_color; ?>" align="center"><?= $val['CON_PRICE']; ?>&nbsp;</td>
											<? 
											if ($approval_type==1) 
											{	
												?>
												<td width="80" class="wrd_brk" style="vertical-align:middle; <? echo $background_color; ?>" align="center"><?= $val['LAST_APPROVAL_RATE']; ?></td>
												<?
											}
											else
											{	
												?>
												<td width="80" class="wrd_brk" style="vertical-align:middle; <? echo $background_color; ?>" align="center">
													<input type="text" name="" id="txtApprovedPriceSpplier_<?= $sl.'_'.$row_num.'_'.$supp_id; ?>" class="text_boxes_numeric" style="width: 70px; <? echo $background_color; ?>" disabled value="<?= $val['LAST_APPROVAL_RATE']; ?>" onKeyUp="chkNegPrice(this.value,2,'<? echo $sl.'**'.$row_num.'**'.$supp_id.'**'.$req_rate; ?>');"/>
													<input type="hidden" name="" id="suppDtlsidSupplier_<?= $sl.'_'.$row_num.'_'.$supp_id; ?>" value="<?= $dtls_id; ?>"/>
												</td>
												<?
											}
						                	?>
						                	<td width="80" class="wrd_brk" style="vertical-align:middle; <? echo $background_color; ?>" align="center" id="txttotalvalue_<?= $sl.'_'.$row_num.'_'.$supp_id; ?>"><? if ($val['TOTAL_VALUE'] != 0) echo number_format($val['TOTAL_VALUE'],2,'.',''); else echo ""; ?>&nbsp;</td>
						                	<?
						                }	
				                	}
				                	if ($k==0)
				                	{
				                		?>
				                		<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>	
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>	
				                		<?
				                	}
				                }	
			                }
		                }	
		                ?>
						<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p><?= change_date_format($row['APPROVED_DATE']); ?></p></td>
						<td class="wrd_brk" style="vertical-align:middle;" align="center"><p><?= $user_arr[$row['CS_INSERT_BY']]; ?></p></td>
					</tr>									
					<?
				}				
				?>
			</tbody>
        </table>
        <table cellspacing="0" cellpadding="0" width="200" align="left">
			<tfoot>
                <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check',<?= $sl; ?>)" /></td>
                <td width="100" align="center"><input type="button" value="<? if ($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $sl; ?>,<? echo $approval_type; ?>)"/></td>
			</tfoot>
		</table>
        </fieldset>           
    </form>
    <!-- <script src="../../includes/functions_bottom.js" type="text/javascript"></script> -->
    <script>//set_multiselect('cbo_suplier','0','0','','0');</script>
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
		var selected_compid = new Array();

		function check_all_data() 
		 {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 
			var approved=document.getElementById('txt_approved').value;
			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i, approved );
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
		function js_set_value( str, approved ) 
		{
			//alert(approved);
			if (approved == 1 || approved == 3) 
            {
                alert('Supplier is not Changeable!!');
                return;
            }

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				selected_dtlsid.push( $('#txt_dtlsid' + str).val() );
				selected_suppid.push( $('#txt_individual_supp_id' + str).val() );
				selected_compid.push( $('#txt_individual_comp_id' + str).val() );
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
				selected_compid.splice( i, 1 );
			}
			var id = ''; var name = ''; var dtlsid=''; var suppid=''; var compid='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				dtlsid += selected_dtlsid[i] + ',';
				if (selected_suppid[i] != '') suppid += selected_suppid[i] + ',';				
				if (selected_compid[i] != '') compid += selected_compid[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			dtlsid = dtlsid.substr( 0, dtlsid.length - 1 );
			suppid = suppid.substr( 0, suppid.length - 1 );
			compid = compid.substr( 0, compid.length - 1 );

			$('#hid_supp_comp_dtlsid').val(id);
			$('#hid_supp_comp_name').val(name);
			$('#hid_dtlsid').val(dtlsid);
			$('#hid_supp_id').val(suppid);
			$('#hid_comp_id').val(compid);

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
	        <input type="hidden" name="hid_comp_id" id="hid_comp_id">
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

						if ($txt_dtls_id != '') $supp_comp_dtlsid=$txt_dtls_id;
						else $supp_comp_dtlsid=$dtls_id;
						$supp_dtls="SELECT a.approved as APPROVED_MST, c.id as ID, c.mst_id as MST_ID, c.dtls_id as DTLS_ID, c.supp_id as SUPP_ID, c.supp_type as SUPP_TYPE, c.approved as APPROVED from req_comparative_mst a, req_comparative_supp_dtls c where a.id=c.mst_id and a.entry_form=512 and a.status_active=1 and a.is_deleted=0 and c.dtls_id in($supp_comp_dtlsid) and c.neg_price is not null and c.is_deleted=0 and c.status_active=1 order by c.id asc";
						$supp_dtls_res=sql_select($supp_dtls);

						$i=1;
						$txt_supp_comp_dtlsid_row_id='';
						//echo $txt_supplier_dtls_id;
						$hidden_supp_comp_dtlsid=explode(",",$txt_supp_comp_dtlsid);
						//print_r($hidden_supplier_dtls_id);
						foreach($supp_dtls_res as $row)
						{
	                        if ($i%2==0) $bgcolor="#E9F3FF";
	                        else $bgcolor="#FFFFFF";

							$supp_name='';
							$supp_id='';
							$comp_id='';
							$approved=$row['APPROVED_MST'];
							if ($row['SUPP_TYPE']==1) 
							{
								$supp_name=$supplier_library[$row['SUPP_ID']];
								$supp_id=$row['SUPP_ID'];
							}	
							if ($row['SUPP_TYPE']==2)
							{
								$supp_name=$company_library[$row['SUPP_ID']];
								$comp_id=$row['SUPP_ID'];
							}
							$row_id=$row['ID'];

							if(in_array($row_id,$hidden_supp_comp_dtlsid)) 
							{ 
								if($txt_supp_comp_dtlsid_row_id=="") $txt_supp_comp_dtlsid_row_id=$i; else $txt_supp_comp_dtlsid_row_id.=",".$i;
							}
					
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<?= $i; ?>', '<?= $approved; ?>');">
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
	                    <input type="hidden" name="txt_approved" id="txt_approved" value="<?= $approved; ?>"/>
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
	$comp_ids_arr=explode(',', $data[2]);
	$txtSuppCompDtlsid=$data[3];
	$row_num=$data[4];
	//echo 'system';die;
	//txtDtlsid+"**"+txtSuppid+"**"+txtCompid+"**"+txtSuppCompDtlsid+"**"+tbl_row_count+"**"+type
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$company_library=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$mst_id=return_field_value("mst_id as mst_id", "req_comparative_supp_dtls", "id in($txtSuppCompDtlsid) and status_active=1 and is_deleted=0", "mst_id");

	$supp_dtls_arr=array();
	$supp_dtls_arr=sql_select("SELECT id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, supp_type as SUPP_TYPE, approved as APPROVED from req_comparative_supp_dtls where mst_id=$mst_id and dtls_id not in($dtls_id) and supp_type=1 and neg_price is not null and is_deleted=0 and status_active=1 order by id asc");

	$comp_dtls_arr=array();
	$comp_dtls_arr=sql_select("SELECT id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, supp_type as SUPP_TYPE, approved as APPROVED from req_comparative_supp_dtls where mst_id=$mst_id and dtls_id not in($dtls_id) and supp_type=2 and neg_price is not null and is_deleted=0 and status_active=1 order by id asc");

	$supp_arr_length=count($supp_dtls_arr);
	$comp_arr_length=count($comp_dtls_arr);
	//$i=1;
	$ids="";
	$supp_ids="";
	$comp_ids="";
	$supp_comp_names="";
	if ($supp_arr_length > 0)
	{
		for( $i=2; $i<=$row_num; $i++ )
		{			
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
						$supp_comp_names.=$supplier_library[$supp_id].',';
			
					}
				}	
			}
			
			echo "$('#txt_supp_id_1_".$i."').val('".rtrim($supp_ids,',')."');\n";
			echo "$('#txt_supp_comp_name_1_".$i."').val('".rtrim($supp_comp_names,',')."');\n";
			echo "$('#txt_supp_comp_dtlsid_1_".$i."').val('".rtrim($ids,',')."');\n";

		}		
	}

	if ($comp_arr_length > 0)
	{
		for( $i=2; $i<=$row_num; $i++ )
		{			
			foreach ($comp_dtls_arr as $val)
			{
				$comp_id=$val['SUPP_ID'];						
				foreach ($comp_ids_arr as $company_id)
				{
					if ($comp_id==$company_id){
						$ids.=$val['ID'].',';
						$dtls_id.=$val['DTLS_ID'];
						$comp_ids.=$comp_id.',';
						$supp_comp_names.=$company_library[$comp_id].',';			
					}
				}	
			}
			//echo $comp_ids.'system';

			echo "$('#txt_comp_id_1_".$i."').val('".rtrim($comp_ids,',')."');\n";
			echo "$('#txt_supp_comp_name_1_".$i."').val('".rtrim($supp_comp_names,',')."');\n";
			echo "$('#txt_supp_comp_dtlsid_1_".$i."').val('".rtrim($ids,',')."');\n";

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
	$comp_ids_arr=explode(',', $data[4]);
	$type=$data[5];

	$supp_dtls_arr=array();
	$supp_dtls_arr=sql_select("SELECT id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, supp_type as SUPP_TYPE, approved as APPROVED, neg_price as NEG_PRICE from req_comparative_supp_dtls where dtls_id in($dtls_id) and supp_type=1 and neg_price is not null and is_deleted=0 and status_active=1 order by id asc");

	$comp_dtls_arr=array();
	$comp_dtls_arr=sql_select("SELECT id as ID, mst_id as MST_ID, dtls_id as DTLS_ID, supp_id as SUPP_ID, supp_type as SUPP_TYPE, approved as APPROVED, neg_price as NEG_PRICE from req_comparative_supp_dtls where dtls_id in($dtls_id) and supp_type=2 and neg_price is not null and is_deleted=0 and status_active=1 order by id asc");	

	$supp_arr_length=count($supp_dtls_arr);
	$comp_arr_length=count($comp_dtls_arr);
	if ($type==1)
	{
		if ($supp_arr_length > 0)
		{
			foreach ($supp_dtls_arr as $val)
			{
				$supp_id=$val['SUPP_ID'];
				echo "$('#txtApprovedPriceSpplier_".$sl."_".$row_num."_".$supp_id."').attr('disabled','disabled').val(".$val['NEG_PRICE'].");\n";			
				foreach ($supp_ids_arr as $supplier_id) 
				{
					if ($supp_id==$supplier_id){
						echo "$('#txtApprovedPriceSpplier_".$sl."_".$row_num."_".$supp_id."').removeAttr('disabled');\n";
					}
				}	
			}		
		}

		if ($comp_arr_length > 0)
		{
			foreach ($comp_dtls_arr as $val)
			{
				$comp_id=$val['SUPP_ID'];
				echo "$('#txtApprovedPriceCompany_".$sl."_".$row_num."_".$comp_id."').attr('disabled','disabled').val(".$val['NEG_PRICE'].");\n";			
				foreach ($comp_ids_arr as $company_id) 
				{
					if ($comp_id==$company_id){
						echo "$('#txtApprovedPriceCompany_".$sl."_".$row_num."_".$comp_id."').removeAttr('disabled');\n";
					}
				}	
			}		
		}

	}
	else  // type=2
	{
		if ($supp_arr_length > 0)
		{
			foreach ($supp_dtls_arr as $val)
			{
				$supp_id=$val['SUPP_ID'];
				echo "$('#txtApprovedPriceSpplier_".$sl."_".$row_num."_".$supp_id."').attr('disabled','disabled').val(".$val['NEG_PRICE'].");\n";	
			}		
		}

		if ($comp_arr_length > 0)
		{
			foreach ($comp_dtls_arr as $val)
			{
				$comp_id=$val['SUPP_ID'];
				echo "$('#txtApprovedPriceCompany_".$sl."_".$row_num."_".$comp_id."').attr('disabled','disabled').val(".$val['NEG_PRICE'].");\n";	
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

		
		$id = return_next_id( "id","approval_history", 1);
		$field_array = "id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date"; 
			
		$booking_ids_all = implode(",",array_unique(explode(",",$booking_ids)));
		$booking_ids_allArr = explode(",",$booking_ids_all);

		$max_approved_no_arr = return_library_array("SELECT mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids_all) and entry_form=57 group by mst_id","mst_id","approved_no");
		$approved_status_arr = return_library_array("SELECT id, approved from req_comparative_mst where id in($booking_ids_all)","id","approved");

		//$booking_ids_all = implode(",",array_unique(explode(",",$booking_ids)));
		//echo '<pre>';print_r($booking_ids_all);
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
			$data_array.="(".$id.",57,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')"; 
			$id=$id+1;
		}

		$field_array_up="approved*approved_by*approved_date";
		$data_array_up=$partial_approval."*".$user_id."*'".$pc_date_time."'";
		

		$id=return_next_id( "id", "lib_supplier_wise_rate", 1 );
		
		$field_array_prod="id, company_id, item_category_id, detarmination_id, product_name_details, unit_of_measure, weight, dia_width, color, inserted_by, insert_date, status_active, is_deleted";
		$field_array_supplier_wise_rate="id, prod_id, supplier_id, entry_form, item_category_id, effective_from, rate, is_supp_comp, inserted_by, insert_date, is_deleted";

		$sql_supp_dtls="select ID, DTLS_ID, SUPP_ID, SUPP_TYPE, LAST_APPROVAL_RATE, APPROVED from req_comparative_supp_dtls where dtls_id in($booking_dtlsids) and status_active=1 and is_deleted=0";
		$sql_supp_dtls_res=sql_select($sql_supp_dtls);
		$supp_dtls_arr=array();
		foreach ($sql_supp_dtls_res as $val) 
		{
			$supp_dtls_arr[$val['DTLS_ID']][$val['SUPP_ID']][$val['SUPP_TYPE']]['ID'] = $val['ID'];
		}		

		$companyArr_library=return_library_array("select id from lib_company where status_active=1 and is_deleted=0","id","id");

		$product_data_arr=array();
		$product_company_arr=array();
		$sql_prod_res=sql_select("select ID, COMPANY_ID, ITEM_CATEGORY_ID, DETARMINATION_ID, PRODUCT_NAME_DETAILS, UNIT_OF_MEASURE, WEIGHT, DIA_WIDTH, COLOR from product_details_master where item_category_id=3 and status_active=1 and is_deleted=0");
		foreach ($sql_prod_res as $val)
		{
			$key=$val['ITEM_CATEGORY_ID'].'**'.$val['DETARMINATION_ID'].'**'.$val['WEIGHT'].'**'.$val['UNIT_OF_MEASURE'];
			$product_data_arr[$key].=$val['ID'].',';
			$product_company_arr[$val['ID']]=$val['COMPANY_ID'];
		}
		//echo '<pre>';print_r($product_data_arr);die;
		$sl_str_arr = explode(",",$sl_str);
		//$companyArr=array();
		foreach ($sl_str_arr as $i) 
		{
			$item_row="sl_wise_item_row_".$i;
			for($j=1; $j<=$$item_row; $j++)
			{
				$item_category_id=3;
				$item_description="item_description_".$i."_".$j;
				$detarmination_id="detarmination_id_".$i."_".$j;
				$txtweight="txtweight_".$i."_".$j;
				$uom="uom_".$i."_".$j;
				$dia_width=""; 
				$color=0;

				$booking_dtlsid="booking_dtlsid_".$i."_".$j;
				// All Supplier, All Company select part
				$all_supp_ids="supplier_id_".$i."_".$j;
				$all_supp_num_arr = explode(',',$$all_supp_ids);
				$all_supp_num_total_row=count($all_supp_num_arr);
				$all_comp_ids="company_id_".$i."_".$j;
				$all_comp_num_arr = explode(',',$$all_comp_ids);
				$all_comp_num_total_row=count($all_comp_num_arr);
				//echo $$all_comp_ids.'system4';

				// Supplier, Company select part
				$txt_supp_id="txt_supp_id_".$i."_".$j;
				$supp_num_arr = explode(',',$$txt_supp_id);
				$txt_comp_id="txt_comp_id_".$i."_".$j;
				$comp_num_arr = explode(',',$$txt_comp_id);
				//echo $comp_num_total_row.'system5';

				// Supplier Part
				for($as=0; $as<$all_supp_num_total_row; $as++)
				{
					$is_supp_comp=1;
					$supp_id=$all_supp_num_arr[$as];
					$txtApprovedPriceSpplier= "txtApprovedPriceSpplier_".$i."_".$j."_".$supp_id;

					if (in_array($supp_id, $supp_num_arr))
					{
								
						$supp_data=$item_category_id.'**'.$$detarmination_id.'**'.$$txtweight.'**'.$$uom;
						//echo $supp_data;
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
								$data_array_prod.="(".$prod_id.",".$compID.",".$item_category_id.",".$$detarmination_id.",'".$$item_description."',".$$uom.",'".$$txtweight."','".$dia_width."',".$color.",".$user_id.",'".$pc_date_time."',1,0)";
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
								$data_array_prod.="(".$prod_id.",".$compID.",".$item_category_id.",".$$detarmination_id.",'".$$item_description."',".$$uom.",'".$$txtweight."','".$dia_width."',".$color.",".$user_id.",'".$pc_date_time."',1,0)";
								//$prod_id = $prod_id+1;
							}				
							
						}

						// data details lib Supplier wise rate table
						foreach ($all_product_arr as $product_id) {
							if ($data_array_supplier_wise_rate != '') $data_array_supplier_wise_rate .=",";
							$data_array_supplier_wise_rate.="(".$id.",".$product_id.",".$supp_id.",512,".$item_category_id.",'".$pc_date_time."','".$$txtApprovedPriceSpplier."',".$is_supp_comp.",".$user_id.",'".$pc_date_time."',0)";
							$id = $id+1;
						}

						// supplier dtls data
						$supp_dtls_arr[$$booking_dtlsid][$supp_id][$is_supp_comp]['APPROVAL_RATE'] = $$txtApprovedPriceSpplier;
						$supp_dtls_arr[$$booking_dtlsid][$supp_id][$is_supp_comp]['APPROVED'] = 1;					
					}
					else
					{
						// supplier dtls dataq
						$supp_dtls_arr[$$booking_dtlsid][$supp_id][$is_supp_comp]['APPROVAL_RATE'] = $$txtApprovedPriceSpplier;
						$supp_dtls_arr[$$booking_dtlsid][$supp_id][$is_supp_comp]['APPROVED'] = 0;
					}	
				}


				// Company Part
				if ($$all_comp_ids != "")
				{
					for($ac=0; $ac<$all_comp_num_total_row; $ac++)
					{
						$is_supp_comp=2;
						$comp_id=$all_comp_num_arr[$ac];
						$txtApprovedPriceCompany= "txtApprovedPriceCompany_".$i."_".$j."_".$comp_id;

						if (in_array($comp_id, $comp_num_arr))
						{

							$comp_data=$item_category_id.'**'.$$detarmination_id.'**'.$$txtweight.'**'.$$uom;
							//echo $comp_data;
							$companyArr=array();
							$all_product_arr=array();
							if (array_key_exists($comp_data,$product_data_arr))
							{
								$product_ids=rtrim($product_data_arr[$comp_data],',');
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
									$data_array_prod.="(".$prod_id.",".$compID.",".$item_category_id.",".$$detarmination_id.",'".$$item_description."',".$$uom.",'".$$txtweight."','".$dia_width."',".$color.",".$user_id.",'".$pc_date_time."',1,0)";
									//$prod_id = $prod_id+1;
								}
							}
							else
							{
								foreach ($companyArr_library as $compID) {
									$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
									$all_product_arr[]=$prod_id;
									if ($data_array_prod != '') $data_array_prod .=",";
									$data_array_prod.="(".$prod_id.",".$compID.",".$item_category_id.",".$$detarmination_id.",'".$$item_description."',".$$uom.",'".$$txtweight."','".$dia_width."',".$color.",".$user_id.",'".$pc_date_time."',1,0)";
									//$prod_id = $prod_id+1;
								}
							}

							// data details lib Supplier wise rate table
							foreach ($all_product_arr as $product_id) {
								if ($data_array_supplier_wise_rate != '') $data_array_supplier_wise_rate .=",";
								$data_array_supplier_wise_rate.="(".$id.",".$product_id.",".$comp_id.",512,".$item_category_id.",'".$pc_date_time."','".$$txtApprovedPriceCompany."',".$is_supp_comp.",".$user_id.",'".$pc_date_time."',0)";
								$id = $id+1;
							}

							// supplier dtls data
							$supp_dtls_arr[$$booking_dtlsid][$comp_id][$is_supp_comp]['APPROVAL_RATE'] = $$txtApprovedPriceCompany;
							$supp_dtls_arr[$$booking_dtlsid][$comp_id][$is_supp_comp]['APPROVED'] = 1;					
						}
						else
						{
							// supplier dtls data
							$supp_dtls_arr[$$booking_dtlsid][$comp_id][$is_supp_comp]['APPROVAL_RATE'] = $$txtApprovedPriceCompany;
							$supp_dtls_arr[$$booking_dtlsid][$comp_id][$is_supp_comp]['APPROVED'] = 0;
						}	
					}
				}	
			}
		}	
		//echo '<pre>';print_r($supp_dtls_arr);
		//echo "insert into lib_supplier_wise_rate($field_array_supplier_wise_rate)values".$data_array_supplier_wise_rate;
		//echo "10** insert into product_details_master($field_array_prod)values".$data_array_prod;die;

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

		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=57 and mst_id in ($booking_ids_all)";
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

       
		$rID5=execute_query(bulk_update_sql_statement( "req_comparative_supp_dtls", "id", $field_array_up_supp, $data_array_up_supp, $supp_rowid_up_array ));
        if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0;
		}
		//echo "10**$flag".'system';die; 
		$response=$booking_ids_all;
		if($flag==1) $msg='19'; else $msg='21';	
	}
	else
	{
		$booking_ids_all = implode(",",array_unique(explode(",",$booking_ids)));
		$booking_dtlsids_all = implode(",",array_unique(explode(",",$booking_dtlsids)));
		$approval_ids_all = implode(",",array_unique(explode(",",$approval_ids)));

	    $rID=sql_multirow_update("req_comparative_mst","approved*ready_to_approved","0*0","id",$booking_ids_all,0);
		if($rID) $flag=1; else $flag=0;
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

	$construction_arr=array(); $composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			$construction_arr[$row[csf('id')]]=$row[csf('construction')];

			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}
	unset($data_array);

	//$main_group_library=return_library_array("select id, main_group_name from lib_main_group where status_active=1","id","main_group_name");
	//$item_group_library=return_library_array("select id, item_name from lib_item_group where status_active=1 and item_category=4","id","item_name");

	$sql_company=sql_select("select id, company_name, contact_no from lib_company");
	$company_library=array();
	$company_contactNo_library=array();
	foreach ($sql_company as $val) {
		$company_library[$val[csf('id')]]=$val[csf('company_name')];
	}

	$sql_supplier=sql_select("select id, supplier_name, contact_no from lib_supplier where status_active=1 and is_deleted=0");
	$supplier_library=array();
	$supplier_contactNo_library=array();
	foreach ($sql_supplier as $val) {
		$supplier_library[$val[csf('id')]]=$val[csf('supplier_name')];
	}
	//$supplier_library=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
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
	
		$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
		from req_comparative_mst a, req_comparative_dtls b, approval_history c 
		where a.id=b.mst_id and a.id=c.mst_id and a.entry_form=512 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=57 and c.current_approval_status=1 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond $sequence_no_cond
		group by a.id, a.sys_number, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.uom, b.weight, b.detarmination_id, b.req_rate, b.req_qty, c.id, c.approved_date
		order by a.id";
		//echo "$sql";
	}
	else if($approval_type==0)	// unapproval process start
	{

		if($user_sequence_no==$min_sequence_no)  // First user
		{

		 	$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.req_item_dtls_id as REQ_ITEM_DTLS_ID, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY 
		 	from req_comparative_mst a, req_comparative_dtls b 
		 	where a.id=b.mst_id and a.entry_form=512 and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 	group by a.id, a.sys_number, a.req_item_dtls_id, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.uom, b.weight, b.detarmination_id, b.req_rate, b.req_qty
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
					
					$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup where page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];
					
					$req_comp_id=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no in ($sequence_no_by) and c.entry_form=57 and c.current_approval_status=1 $cs_date_cond","req_comp_id");
					$req_comp_id=implode(",",array_unique(explode(",",$req_comp_id)));
					
					$req_comp_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no=$user_sequence_no and c.entry_form=57 and c.current_approval_status=1","req_comp_id");
					$req_comp_id_app_byuser=implode(",",array_unique(explode(",",$req_comp_id_app_byuser)));
				}
				else
				{

					$seqSql="select group_concat(sequence_no) as sequence_no_by from electronic_approval_setup where page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];
					
					$req_comp_id=return_field_value("group_concat(distinct(b.mst_id)) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no in ($sequence_no_by) and c.entry_form=57 and c.current_approval_status=1 $cs_date_cond","req_comp_id");
					$req_comp_id=implode(",",array_unique(explode(",",$req_comp_id)));
					
					$req_comp_id_app_byuser=return_field_value("group_concat(distinct(b.mst_id)) as req_comp_id","req_comparative_mst a, req_comparative_dtls b, approval_history c","a.id=b.mst_id and a.id=c.mst_id and a.ready_to_approved=1  and a.approved in (3,1) and a.is_deleted=0 and c.sequence_no=$user_sequence_no and c.entry_form=57 and c.current_approval_status=1","req_comp_id");
					$req_comp_id_app_byuser=implode(",",array_unique(explode(",",$req_comp_id_app_byuser)));
				}

				$result=array_diff(explode(',',$req_comp_id),explode(',',$req_comp_id_app_byuser));
				$req_comp_id=implode(",",$result);

				if($req_comp_id!="")
				{					
					$sql=" SELECT x.* from  (SELECT DISTINCT (a.id) as ID, a.sys_number as CS_NUMBER, a.req_item_dtls_id as REQ_ITEM_DTLS_ID, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY
					from req_comparative_mst a, req_comparative_dtls b
					where a.id=b.mst_id and a.entry_form=512 and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 			group by a.id, a.sys_number, a.req_item_dtls_id, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.uom, b.weight, b.detarmination_id, b.req_rate, b.req_qty

					UNION ALL

					SELECT DISTINCT (a.id) as ID, a.sys_number as CS_NUMBER, a.req_item_dtls_id as REQ_ITEM_DTLS_ID, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY 
					from req_comparative_mst a, req_comparative_dtls b
					where a.id=b.mst_id and a.entry_form=512 and a.approved in(1,3) and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($req_comp_id) $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 			group by a.id, a.sys_number, a.req_item_dtls_id, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.uom, b.weight, b.detarmination_id, b.req_rate, b.req_qty) x  order by x.ID";
					//echo $sql;
				}
				else
				{ 
					$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.req_item_dtls_id as REQ_ITEM_DTLS_ID, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY 
					from req_comparative_mst a, req_comparative_dtls b
					where a.id=b.mst_id and a.entry_form=512 and a.approved=$approval_type and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond 
		 			group by a.id, a.sys_number, a.req_item_dtls_id, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.uom, b.weight, b.detarmination_id, b.req_rate, b.req_qty
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

				$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.req_item_dtls_id as REQ_ITEM_DTLS_ID, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
				from req_comparative_mst a, req_comparative_dtls b, approval_history c 
				where a.id=b.mst_id and a.id=c.mst_id and a.entry_form=512 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=57 and c.current_approval_status=1 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond $sequence_no_cond
				group by a.id, a.sys_number, a.req_item_dtls_id, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.uom, b.weight, b.detarmination_id, b.req_rate, b.req_qty, c.id, c.approved_date
				order by a.id";
				//echo $sql;
			}
		}	

	}
	else // approval process start
	{
		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";

		$sql="SELECT a.id as ID, a.sys_number as CS_NUMBER, a.req_item_dtls_id as REQ_ITEM_DTLS_ID, a.sys_number_prefix_num as CS_NUMBER_PREFIX_NUM, $year_field,  a.cs_date as CS_DATE, a.supp_id as SUPP_ID, a.company_id as COMPANY_ID, a.inserted_by as CS_INSERT_BY, b.id as DTLS_ID, b.item_category_id as ITEM_CATEGORY_ID, b.item_description as ITEM_DESCRIPTION, b.item_group_id as ITEM_GROUP_ID, b.main_group_id as MAIN_GROUP_ID, b.brand_supplier as ITEM_REF, b.uom as UOM, b.weight as WEIGHT, b.detarmination_id as DETARMINATION_ID, b.req_rate as REQ_RATE, b.req_qty as REQ_QTY, c.id as APPROVAL_ID, c.approved_date as APPROVED_DATE 
		from req_comparative_mst a, req_comparative_dtls b, approval_history c 
		where a.id=b.mst_id and a.id=c.mst_id and a.entry_form=512 and a.approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=57 and c.current_approval_status=1 $item_category_cond $cs_no_cond $cs_date_cond $cs_year_cond $sequence_no_cond
		group by a.id, a.sys_number, a.req_item_dtls_id, a.sys_number_prefix_num, $year_cond_prefix, a.cs_date, a.supp_id, a.company_id, a.inserted_by, b.id, b.item_category_id, b.item_description, b.item_group_id, b.main_group_id, b.brand_supplier, b.uom, b.weight, b.detarmination_id, b.req_rate, b.req_qty, c.id, c.approved_date
		order by a.id";
	}

	//echo $sql;
	
	$sql_res=sql_select($sql);
	$company_arr=array();
	$selected_prod_id="";
	foreach($sql_res as $row)
	{
		$mst_id.=$row["ID"].",";
		if ($row["COMPANY_ID"] != ""){
			$company_arr[$row["CS_NUMBER"]]=$row["COMPANY_ID"];
		}
		
		$supplier_arr[$row["CS_NUMBER"]]=$row["SUPP_ID"];		
		$dtls_row_arr[$row["CS_NUMBER"]].=$row["DTLS_ID"].',';
		$cs_number_arr[$row["CS_NUMBER"]].=$row["CS_NUMBER"].',';
		$req_item_dtls_id.=$row["REQ_ITEM_DTLS_ID"].',';
		$rowspan_arr[$row["CS_NUMBER"]]++;
	}
	//echo implode(",", $company_arr);
	//echo '<pre>';print_r($company_arr);

	$first_cs_count_row=count(explode(',',rtrim(reset($dtls_row_arr),',')));
	if (!empty($company_arr)){
		$companyArr=array_unique(explode(",", implode(",", $company_arr)));	
		$company_count=count($companyArr);
	}
	
	//echo '<pre>'; print_r($companyArr);
	if ($company_count > 0) $company_width=$company_count*320;
	else $company_width=0;

	$supplierArr=array_unique(explode(",", implode(",", $supplier_arr)));
	$supplier_width=count($supplierArr)*320;
	//echo count($supplierArr);

	$mst_ids = implode(',', array_flip(array_flip(explode(',', rtrim($mst_id,',')))));
	$req_item_dtls_ids = implode(',', array_flip(array_flip(explode(',', rtrim($req_item_dtls_id,',')))));

	$sql_fabrics_res=sql_select("SELECT a.id as ID, a.item_category_id as ITEM_CATEGORY_ID, a.detarmination_id as DETARMINATION_ID, a.weight as WEIGHT, a.uom as UOM, b.id as FABRIC_COST_ID, b.nominated_supp_multi as NOMINATED_SUPPLIER from req_comparative_dtls a, wo_pre_cost_fabric_cost_dtls b where a.fabric_cost_dtls_id=b.id and a.mst_id in($mst_ids) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.id asc");
	foreach ($sql_fabrics_res as $row) {
		if ($row["NOMINATED_SUPPLIER"] != ""){
			$nominated_supp.=$row["NOMINATED_SUPPLIER"].',';
		}		
	}
	$nominated_supp_arr=explode(",",rtrim($nominated_supp,','));
	
	$supp_dtls="SELECT b.req_qty as REQ_QTY, c.id as ID, c.mst_id as MST_ID, c.dtls_id as DTLS_ID, c.supp_id as SUPP_ID, c.supp_type as SUPP_TYPE, c.prod_id as PROD_ID, c.quoted_price as QUOTED_PRICE, c.neg_price as NEG_PRICE, c.con_price as CON_PRICE, c.last_approval_rate as LAST_APPROVAL_RATE, c.brand as BRAND, c.model as MODEL, c.origin as ORIGIN, c.approved as APPROVED from req_comparative_dtls b, req_comparative_supp_dtls c where b.id=c.dtls_id and b.is_deleted=0 and b.status_active=1 and c.mst_id in($mst_ids) and c.is_deleted=0 and c.status_active=1 order by c.id asc";
	/*$supp_dtls="SELECT a.sys_number as CS_NUMBER, b.item_group_id as ITEM_GROUP_ID, b.brand_supplier as ITEM_REF, b.item_description as ITEM_DESCRIPTION, b.uom as UOM, c.id as ID, c.mst_id as MST_ID, c.dtls_id as DTLS_ID, c.supp_id as SUPP_ID, c.supp_type as SUPP_TYPE, c.prod_id as PROD_ID, c.quoted_price as QUOTED_PRICE, c.neg_price as NEG_PRICE, c.con_price as CON_PRICE, c.last_approval_rate as LAST_APPROVAL_RATE, c.brand as BRAND, c.model as MODEL, c.origin as ORIGIN, c.approved as APPROVED 
		from req_comparative_mst a, req_comparative_dtls b, req_comparative_supp_dtls c where a.id=b.mst_id and b.id=c.dtls_id and a.id in(126) and a.entry_form=512 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
		order by c.id asc";*/
	$supp_dtls_res=sql_select($supp_dtls);
	$supp_dtls_arr=array();
	$comp_dtls_arr=array();
	$supp_comp_dtls_arr=array();
	$supp_id_dtls_arr=array();
	$comp_id_dtls_arr=array();
	$recommend_supplier_arr=array();
	foreach ($supp_dtls_res as $row) {
		// SUPP_TYPE=2(company) and SUPP_TYPE=1(supplier)        
		if ($row["SUPP_TYPE"] == 1)
		{
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['QUOTED_PRICE']=$row["QUOTED_PRICE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['LAST_APPROVAL_RATE']=$row["LAST_APPROVAL_RATE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['CON_PRICE']=$row["CON_PRICE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['NEG_PRICE']=$row["NEG_PRICE"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['REQ_QTY']=$row["REQ_QTY"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['TOTAL_VALUE']=$row["NEG_PRICE"]*$row["REQ_QTY"];
			$supp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['SUPP_TYPE']=$row["SUPP_TYPE"];
			if ($row["APPROVED"] == 1) 
			{
				$supp_comp_dtls_arr[$row["DTLS_ID"]]['ID'].=$row["ID"].',';
				$supp_comp_dtls_arr[$row["DTLS_ID"]]['NAME'].=$supplier_library[$row["SUPP_ID"]].',';
				$supp_id_dtls_arr[$row["DTLS_ID"]].=$row["SUPP_ID"].',';
			}
			$recommend_supplier_arr[$row["SUPP_ID"]]=$row["IS_RECOMMEND"];
		}
		else if ($row["SUPP_TYPE"] == 2)
		{
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['QUOTED_PRICE']=$row["QUOTED_PRICE"];
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['LAST_APPROVAL_RATE']=$row["LAST_APPROVAL_RATE"];
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['CON_PRICE']=$row["CON_PRICE"];
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['NEG_PRICE']=$row["NEG_PRICE"];
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['REQ_QTY']=$row["REQ_QTY"];
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['TOTAL_VALUE']=$row["NEG_PRICE"]*$row["REQ_QTY"];
			$comp_dtls_arr[$row["DTLS_ID"]][$row["SUPP_ID"]]['SUPP_TYPE']=$row["SUPP_TYPE"];
			if ($row["APPROVED"] == 1) 
			{
				$supp_comp_dtls_arr[$row["DTLS_ID"]]['ID'].=$row["ID"].',';
				$supp_comp_dtls_arr[$row["DTLS_ID"]]['NAME'].=$company_library[$row["SUPP_ID"]].',';
				$comp_id_dtls_arr[$row["DTLS_ID"]].=$row["SUPP_ID"].',';
			}			
		}		
	}
	//echo '<pre>';print_r($supp_comp_dtls_arr);die;
	$tbl_width=988+$company_width*1+$supplier_width*1;
	?>
	<script>

		function openmypage_supplier(sl, row_num, cs_number, dtls_id)
		{
			var txt_supp_comp_dtlsid= $('#txt_supp_comp_dtlsid_'+sl+'_'+row_num).val();
			var txt_dtlsid= $('#txt_dtlsid_'+sl+'_'+row_num).val();
			var page_link="requires/cs_approval_fabrics_controller.php?action=supplier_popup&txt_supp_comp_dtlsid="+txt_supp_comp_dtlsid+"&txt_dtlsid="+txt_dtlsid+"&dtls_id="+dtls_id;

			var title="Supplier Info";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{

				var theemailID=this.contentDoc.getElementById("hid_supp_comp_dtlsid").value;	
				var theemailVAL=this.contentDoc.getElementById("hid_supp_comp_name").value;
				var theemailDtlsID=this.contentDoc.getElementById("hid_dtlsid").value;
				var theemailsuppID=this.contentDoc.getElementById("hid_supp_id").value;
				var theemailcompID=this.contentDoc.getElementById("hid_comp_id").value;
				$('#txt_supp_comp_dtlsid_'+sl+'_'+row_num).val(theemailID);
				$('#txt_supp_comp_name_'+sl+'_'+row_num).val(theemailVAL);
				if (theemailDtlsID != ""){
					$('#txt_dtlsid_'+sl+'_'+row_num).val(theemailDtlsID);
				}

				if (theemailsuppID != '' || theemailcompID != '')
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

					var comp_ids='';
					var comp_arr=theemailcompID.split(',');
					var comp_arr_length=comp_arr.length;						
					for (c=0; c<comp_arr_length; c++)
					{
						var cid = comp_arr[c];
						if (comp_ids=="") comp_ids= cid; 
						else comp_ids +=','+cid;
					}				
					$('#txt_comp_id_'+sl+'_'+row_num).val(comp_ids);
					

					get_php_form_data( sl+"**"+row_num+"**"+theemailDtlsID+"**"+supp_ids+"**"+comp_ids+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_fabrics_controller" );				
				}
				else
				{
					var type=2;
					var txt_dtlsid=$('#txt_dtlsid_'+sl+'_'+row_num).val();
					var supp_ids=$('#txt_supp_id_'+sl+'_'+row_num).val();
					var comp_ids=$('#txt_comp_id_'+sl+'_'+row_num).val();
					get_php_form_data( sl+"**"+row_num+"**"+txt_dtlsid+"**"+supp_ids+"**"+comp_ids+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_fabrics_controller" );
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
				var txtCompid=$("#txt_comp_id_1_1").val();
				get_php_form_data( txtDtlsid+"**"+txtSuppid+"**"+txtCompid+"**"+txtSuppCompDtlsid+"**"+tbl_row_count+"**"+type, "populate_data_from_copy_cs_check", "requires/cs_approval_fabrics_controller" );

				var sl=1;
				for( var i = 2; i <= tbl_row_count; i++ ) 
				{					
					var row_num=i;
					var txtDtlsidWithoutFirstRow=$("#txt_dtlsid_1_"+i).val();
					var txtSuppidWithoutFirstRow=$("#txt_supp_id_1_"+i).val();
					var txtCompidWithoutFirstRow=$("#txt_comp_id_1_"+i).val();
					get_php_form_data( sl+"**"+row_num+"**"+txtDtlsidWithoutFirstRow+"**"+txtSuppidWithoutFirstRow+"**"+txtCompidWithoutFirstRow+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_fabrics_controller" );
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
					var txtCompid=$("#txt_comp_id_1_"+i).val();
					get_php_form_data( sl+"**"+row_num+"**"+txtDtlsid+"**"+txtSuppid+"**"+txtCompid+"**"+type, "populate_data_from_supplier_popup", "requires/cs_approval_fabrics_controller" );
					$("#txt_supp_comp_name_1_"+i).val('');
					$("#txt_supp_comp_dtlsid_1_"+i).val('');
					$("#txt_dtlsid_1_"+i).val('');
					$("#txt_supp_id_1_"+i).val('');
					$("#txt_comp_id_1_"+i).val('');
				}

				for( var i = 1; i <= tbl_row_count; i++ )
				{
					$('#tbl_'+i).prop("checked", false);			
				}				
			}	
		}

		function calculate_value()
	    {
			$('input[name="tbl[]"]:checked').each(function()
			{
				var i=$(this).attr("value");
				var item_row=$(".item_class_"+i).length;
				for(j=1; j<=item_row; j++)
				{
					var supplier_id = $('#txt_supp_id_'+i+'_'+j).val();
					var col_num_arr = supplier_id.split(',');
		        	var col_num = col_num_arr.length; 

					var company_name_id = $('#txt_comp_id_'+i+'_'+j).val();
					var company_num_arr = company_name_id.split(',');
		        	if(company_name_id!=''){var company_num = company_num_arr.length}else{var company_num =0;}	        

					var supp_neg_price=com_neg_price="";
					var total_value="";
	            	var req_qty = $('#txtreqqty_'+i+'_'+j).attr('title')*1;

					for (var m=0; m<col_num; m++)
					{
						var mm=col_num_arr[m];
	               		supp_neg_price = $('#txtApprovedPriceSpplier_'+ i+'_'+j+'_'+ mm).val()*1;
						total_value = supp_neg_price*req_qty;
						if (total_value==0) $('#txttotalvalue_'+ i+'_'+j+'_'+ mm).html("");
	                	else $('#txttotalvalue_'+ i+'_'+j+'_'+ mm).html(total_value.toFixed(2));
					}

					for (var m=0; m<company_num; m++)
					{
						var mm=company_num_arr[m];
						com_neg_price = $('#txtApprovedPriceCompany_'+ i+'_'+j+'_'+ mm).val()*1;
						total_value = com_neg_price*req_qty;
						if (total_value==0) $('#txtCompanyTotalValue_'+ i+'_'+j+'_'+ mm).html("");
	                	else $('#txtCompanyTotalValue_'+ i+'_'+j+'_'+ mm).html(total_value.toFixed(2));
					}
				}
			});
	    }

		function chkNegPrice(val,id,data)
		{
			var dataInfo=data.split('**');
			var sl=dataInfo[0]*1;
			var row_num=dataInfo[1]*1;
			var supp_comp_id=dataInfo[2]*1;
			var costPrice=dataInfo[3]*1;
			var reqQuantity=$("#txtreqqty_"+sl+"_"+row_num).val()*1;

			if(id==1) //Company
			{
				if(val>costPrice)
				{
					alert("Costing Price will be equal or less than … not allow higher");
					$("#txtApprovedPriceCompany_"+sl+"_"+row_num+"_"+supp_comp_id).val('');
					$("#txtCompanyTotalValue_"+sl+"_"+row_num+"_"+supp_comp_id).val('');
				}
				
				var companyTotalValue=reqQuantity*$("#txtApprovedPriceCompany_"+sl+"_"+row_num+"_"+supp_comp_id).val();
				$("#txtCompanyTotalValue_"+sl+"_"+row_num+"_"+supp_comp_id).val(companyTotalValue.toFixed(2));
			}
			if(id==2) //Supplier
			{
				if(val>costPrice)
				{
					alert("Costing Price will be equal or less than … not allow higher");
					$("#txtApprovedPriceSpplier_"+sl+"_"+row_num+"_"+supp_comp_id).val('');
					$("#txttotalvalue_"+sl+"_"+row_num+"_"+supp_comp_id).val('');
				}

				var totalvalue=reqQuantity*$("#txtApprovedPriceSpplier_"+sl+"_"+row_num+"_"+supp_comp_id).val();
				$("#txttotalvalue_"+sl+"_"+row_num+"_"+supp_comp_id).val(totalvalue.toFixed(2));
			}
		}
	</script>
	<style type="text/css">
		.wrd_brk{.word-break: break-all; word-wrap: break-word;}
	</style>
    <form name="csApproval_2" id="csApproval_2">
    	<fieldset style="width: <?= $tbl_width; ?>px; margin-top:10px;">
        <legend style="width: <?= $tbl_width; ?>px;">CS Approval [Accessories]</legend>
        <table cellspacing="0" cellpadding="0" rules="all" width="<?= $tbl_width; ?>" class="rpt_table" align="left" id="tbl_cs_list">
        	<thead>
        		<tr>
	        		<th colspan="9">&nbsp;</th>
	        		<? if ( $approval_type==1 ) { ?>
	        		<th width="150"></th>
	        		<? } else { ?>
	        		<th width="150"><input type="checkbox" id="copy_cs" onchange="fn_cs_check()"/>&nbsp;&nbsp;Copy CS</th>
	        		<?
	        		}
	        		if ($company_count > 0)
	        		{
		                foreach($companyArr as $comp_id)
						{
							?>
							<th colspan="4" width="320"><?= $company_library[$comp_id]; ?></th>
		                	<?
		                }
	            	}
					foreach($supplierArr as $supp_id)
					{
						?>
	                	<th colspan="4" width="320"><?= $supplier_library[$supp_id]; ?>&nbsp;&nbsp;<? if ($recommend_supplier_arr[$supp_id]==1) echo "Recommend"; ?></th>
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
	                <th width="150">Items Description</th>
	               	<th width="80">Req. Qty.</th>
	               	<th width="60">UOM</th>
					<th width="60">Costing Price</th>
	                <th width="150" style="color: blue;">Supplier</th>
					<?
	                if ($company_count > 0 )
					{
	                    foreach ($companyArr as $comp_id)
						{
							?>
		                	<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Quoted Price</p></th>
							<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Last Price</p></th>
							<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Neg. Price</p></th>
							<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Total Value</p></th>
		                	<?
		                }
	            	}
					foreach ($supplierArr as $supp_id)
					{
						?>
	                	<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Quoted Price</p></th>
						<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Last Price</p></th>
						<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Neg. Price</p></th>
						<th width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p>Total Value</p></th>
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
                $dtls_idArr_itemWise=array();
                foreach ($sql_res as $row) 
                {
					$req_rate=$row['REQ_RATE'];
                	$dtls_id=$row['DTLS_ID'];
                	$supp_comp_dtls_ids = rtrim($supp_comp_dtls_arr[$row["DTLS_ID"]]['ID'],',');
                	$supp_comp_name = rtrim($supp_comp_dtls_arr[$row["DTLS_ID"]]['NAME'],',');
                	$suppID = rtrim($supp_id_dtls_arr[$row["DTLS_ID"]],',');
                	$compID = rtrim($comp_id_dtls_arr[$row["DTLS_ID"]],',');
                	$count_cs_dtls_row=$row['DTLS_ID'];
                	$items=$row["ITEM_CATEGORY_ID"]."**".$row["DETARMINATION_ID"]."**".$row["WEIGHT"]."**".$row["UOM"];
                	//if ($item==$nominate_supplier_arr[$item])
                	
					$bgcolor="#E9F3FF"; 					
                	?>
					<tr bgcolor="<?= $bgcolor; ?>" id="tr_<?= $i; ?>" align="center" >
						<?
						$row_num++;
						if ($check_cs_number[$row['CS_NUMBER']]=="")
						{
							$sl++;
							?>
							<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]; ?>" width="30" align="center" valign="middle" style="vertical-align:top;"><input type="checkbox" id="tbl_<?= $sl; ?>" name="tbl[]" value="<?= $sl; ?>"/></td>
							<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]; ?>" width="30"  class="wrd_brk" align="center" style="vertical-align:top;"><?= $i; ?></td>
							<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]; ?>" width="80" class="wrd_brk" style="vertical-align:top;"><p><a href='##' style='color:#000' onClick="print_report(<? echo $row['ID']; ?>,'comparative_statement_print', '../commercial/work_order/requires/comparative_statement_fabrics_controller')"><? echo $row['CS_NUMBER_PREFIX_NUM']; ?></a></p></td>
							<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]; ?>" width="60" class="wrd_brk" style="vertical-align:top;" align="left"><p><?= $row['CS_YEAR']; ?></p></td>                        
                        	<td rowspan="<?= $rowspan_arr[$row['CS_NUMBER']]; ?>" width="80"  class="wrd_brk" style="vertical-align:top;" align="left"><p><?= change_date_format($row['CS_DATE']); ?></p></td>
							<?                         	
                        	$check_cs_number[$row['CS_NUMBER']]=$row['CS_NUMBER']; 
                        	$row_num=1;
                        } 
                        ?>
                        <td width="150" class="item_class_<?= $sl; ?>" style="vertical-align:middle;" align="left" id="item_description_<?= $sl.'_'.$row_num; ?>" title="<?= $construction_arr[$row['DETARMINATION_ID']].', '.$composition_arr[$row['DETARMINATION_ID']].', '.$row['WEIGHT']; ?>"><p><?= $row['ITEM_DESCRIPTION']; ?></p>
                        	<input id="first_cs_number_count" name="first_cs_number_count" type="hidden" value="<?= $first_cs_count_row; ?>" />
							<input id="booking_id_<?= $sl.'_'.$row_num; ?>" name="booking_id[]" type="hidden" value="<?= $row['ID']; ?>" />
							<input id="booking_no_<?= $sl.'_'.$row_num; ?>" name="booking_no[]" type="hidden" value="<?= $row['CS_NUMBER']; ?>" />
							<input id="booking_dtlsid_<?= $sl.'_'.$row_num; ?>" name="booking_dtlsid[]" type="hidden" value="<?= $row['DTLS_ID']; ?>" />
							<input id="approval_id_<?= $sl.'_'.$row_num; ?>" name="approval_id[]" type="hidden" value="<?= $row['APPROVAL_ID']; ?>" />
							<input id="detarmination_id_<?= $sl.'_'.$row_num; ?>" name="detarmination_id[]" type="hidden" value="<?= $row['DETARMINATION_ID']; ?>" />
							<input id="txtweight_<?= $sl.'_'.$row_num; ?>" name="txtweight[]" type="hidden" value="<?= $row['WEIGHT']; ?>" />
							<input id="supplier_id_<?= $sl.'_'.$row_num; ?>" name="supplier_id[]" type="hidden" value="<?= $row['SUPP_ID']; ?>" />
							<input id="company_id_<?= $sl.'_'.$row_num; ?>" name="company_id[]" type="hidden" value="<?= $row['COMPANY_ID']; ?>" />
                        </td>
                        <td width="80" class="wrd_brk" style="vertical-align:middle;" align="right" id="txtreqqty_<?= $sl.'_'.$row_num; ?>" title="<?= $row['REQ_QTY']; ?>"><p><?= number_format($row['REQ_QTY'],2,'.',''); ?></p></td>
						<td width="60" class="wrd_brk" style="vertical-align:middle;" align="center" id="uom_<?= $sl.'_'.$row_num; ?>" title="<?= $row['UOM']; ?>"><p><?= $unit_of_measurement[$row['UOM']]; ?></p></td>
						<td width="60" class="wrd_brk" style="vertical-align:middle;" align="center" id="costingprice_<?= $sl.'_'.$row_num; ?>" title="<?= $req_rate; ?>"><p><?= $req_rate; ?></p></td>

                        <td width="150"  class="wrd_brk" style="vertical-align:middle;" align="left"><p>
                        	<?
                        	$supp_comp_ids='';
                        	if ($row['SUPP_ID'] != '') $supp_comp_ids.=$row['SUPP_ID'];
                        	if ($row['COMPANY_ID'] != '') $supp_comp_ids.=','.$row['COMPANY_ID'];
                        	?>
							<input type="text" name="txt_supp_comp_name" id="txt_supp_comp_name_<?= $sl.'_'.$row_num; ?>" value="<?= $supp_comp_name; ?>" class="text_boxes" style="width:130px" placeholder="Browse Double Click" onDblClick="openmypage_supplier(<?= $sl; ?>,<?= $row_num; ?>,'<?= $row['CS_NUMBER']; ?>', '<?= $dtls_id; ?>');" readonly />
							<input type="hidden" name="txt_supp_id" id="txt_supp_id_<?= $sl.'_'.$row_num; ?>" value="<?= $suppID; ?>"/>
							<input type="hidden" name="txt_comp_id" id="txt_comp_id_<?= $sl.'_'.$row_num; ?>" value="<?= $compID; ?>"/>
							<input type="hidden" name="txt_supp_comp_dtlsid" id="txt_supp_comp_dtlsid_<?= $sl.'_'.$row_num; ?>" value="<?= $supp_comp_dtls_ids; ?>"/>
							<input type="hidden" name="txt_dtlsid" id="txt_dtlsid_<?= $sl.'_'.$row_num; ?>" value="<?= $dtls_id; ?>"/>
                        </p></td>
                        <?                        
						if ($company_count > 0)
						{
							foreach ($comp_dtls_arr as $comp_dtl_id => $comp_data)
							{
								if ($dtls_id == $comp_dtl_id)
								{
									foreach ($companyArr as $company_id) 
									{
										$k=0;											
										foreach ($comp_data as $comp_id => $val)
										{
											if ($company_id==$comp_id)
											{
												$k++;
												?>
							                	<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><?= $val['QUOTED_PRICE']; ?>&nbsp;</td>
												<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><?= $val['CON_PRICE']; ?>&nbsp;</td>
												<? 
												if ($approval_type==1) 
												{	
													?>
													<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><?= $val['LAST_APPROVAL_RATE']; ?></td>
													<?
												}
												else
												{
													//if ($val['NEG_PRICE'] == "") $disabled ="disabled";
													?>
													<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">
														<input type="text" name="" id="txtApprovedPriceCompany_<?= $sl.'_'.$row_num.'_'.$comp_id; ?>" class="text_boxes_numeric" style="width: 70px;" disabled value="<?= $val['LAST_APPROVAL_RATE']; ?>" onKeyUp="calculate_value()"/></td>
														<input type="hidden" name="" id="suppDtlsidCompany_<?= $sl.'_'.$row_num.'_'.$comp_id; ?>" value="<?= $dtls_id; ?>" />
													</td>	
													<?
												}
												?>
												<td width="80" class="wrd_brk" style="vertical-align:middle;" id="txtCompanyTotalValue_<?= $sl.'_'.$row_num.'_'.$comp_id; ?>"  align="center"><? if ($val['TOTAL_VALUE'] != 0) echo number_format($val['TOTAL_VALUE'],2,'.',''); else echo ""; ?>&nbsp;</td>
												<?
						                		//break;
						                	}				                		

					                	}
					                	if ($k==0)
					                	{
					                		?>
					                		<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
											<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
											<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
											<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>	
					                		<?
					                	}						                		
					                }	
				                }					                	
			                } 
		            	}

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
											$background_color="";
											if (in_array($supp_id,$nominated_supp_arr)) $background_color = "background-color: yellow";
											//if ($val['NEG_PRICE'] == "") $disabled ="disabled";										
											?>
						                	<td width="80" class="wrd_brk" style="vertical-align:middle; <? echo $background_color; ?>" align="center" ><?= $val['QUOTED_PRICE']; ?>&nbsp;</td>
											<td width="80" class="wrd_brk" style="vertical-align:middle; <? echo $background_color; ?>" align="center"><?= $val['CON_PRICE']; ?>&nbsp;</td>
											<? 
											if ($approval_type==1) 
											{	
												?>
												<td width="80" class="wrd_brk" style="vertical-align:middle; <? echo $background_color; ?>" align="center"><?= $val['LAST_APPROVAL_RATE']; ?></td>
												<?
											}
											else
											{	
												?>
												<td width="80" class="wrd_brk" style="vertical-align:middle; <? echo $background_color; ?>" align="center">
													<input type="text" name="" id="txtApprovedPriceSpplier_<?= $sl.'_'.$row_num.'_'.$supp_id; ?>" class="text_boxes_numeric" style="width: 70px; <? echo $background_color; ?>" disabled value="<?= $val['LAST_APPROVAL_RATE']; ?>" onKeyUp="calculate_value()"/>
													<input type="hidden" name="" id="suppDtlsidSupplier_<?= $sl.'_'.$row_num.'_'.$supp_id; ?>" value="<?= $dtls_id; ?>"/>
												</td>
												<?
											}
						                	?>
						                	<td width="80" class="wrd_brk" style="vertical-align:middle; <? echo $background_color; ?>" align="center" id="txttotalvalue_<?= $sl.'_'.$row_num.'_'.$supp_id; ?>"><? if ($val['TOTAL_VALUE'] != 0) echo number_format($val['TOTAL_VALUE'],2,'.',''); else echo ""; ?>&nbsp;</td>
						                	<?
						                }	
				                	}
				                	if ($k==0)
				                	{
				                		?>
				                		<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>	
										<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center">&nbsp;</td>	
				                		<?
				                	}
				                }	
			                }
		                }	
		                ?>
						<td width="80" class="wrd_brk" style="vertical-align:middle;" align="center"><p><?= change_date_format($row['APPROVED_DATE']); ?></p></td>
						<td class="wrd_brk" style="vertical-align:middle;" align="center"><p><?= $user_arr[$row['CS_INSERT_BY']]; ?></p></td>
					</tr>									
					<?
				}				
				?>
			</tbody>
        </table>
        <table cellspacing="0" cellpadding="0" width="200" align="left">
			<tfoot>
                <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check',<?= $sl; ?>)" /></td>
                <td width="100" align="center"><input type="button" value="<? if ($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $sl; ?>,<? echo $approval_type; ?>)"/></td>
			</tfoot>
		</table>
        </fieldset>           
    </form>
    <!-- <script src="../../includes/functions_bottom.js" type="text/javascript"></script> -->
    <script>//set_multiselect('cbo_suplier','0','0','','0');</script>
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
