<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}


//--------------------------------------------------------------------------------------------------------------------

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

 	$company_name=str_replace("'","",$cbo_company_name);

 	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$brand_name_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$supplierArr=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name");

	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	if($cbo_buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and c.buyer_id=$cbo_buyer_name"; 	
	}

	$cbo_year=str_replace("'","",$cbo_year);
	$year_cond="";
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}

	$txt_prog_no=trim(str_replace("'","",$txt_prog_no));
	$txt_req_no=trim(str_replace("'","",$txt_req_no));
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_internal=trim(str_replace("'","",$txt_ref_no));
	$txt_sales_order_no=trim(str_replace("'","",$txt_sales_order_no));
	$txt_fabric_booking_no=trim(str_replace("'","",$txt_fabric_booking_no));

	$txt_demand_no=trim(str_replace("'","",$txt_demand_no));

	$demand_con = '';
	if($txt_demand_no!=''){
		$demand_con = "and a.demand_system_no LIKE '%$txt_demand_no%'";
	}

	if($txt_file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='$txt_file_no' ";
	if($txt_internal=="") $internal_cond=""; else $internal_cond=" and b.grouping='$txt_internal' ";
	if($txt_prog_no=="") $prog_cond=""; else $prog_cond=" and b.knit_id in($txt_prog_no) ";
	if($txt_req_no=="") $req_cond=""; else $req_cond=" and b.requisition_no in($txt_req_no)";
	if($txt_fabric_booking_no=="") $booking_cond=""; else $booking_cond=" and e.booking_no like('%$txt_fabric_booking_no')";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	
	$date_cond="";
	if(str_replace("'","",$txt_date_from)!="" || str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		$date_cond=" and a.demand_date between '$start_date' and '$end_date'";
	}

	$requsition_date_cond="";
	if(str_replace("'","",$txt_requisition_date_from	)!="" || str_replace("'","",$txt_requisition_date_to)!="")
	{
		if($db_type==0)
		{
			$requisition_start_date=change_date_format(str_replace("'","",$txt_requisition_date_from),"yyyy-mm-dd","");
			$requsition_end_date=change_date_format(str_replace("'","",$txt_requisition_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$requisition_start_date=change_date_format(str_replace("'","",$txt_requisition_date_from),"","",1);
			$requisition_end_date=change_date_format(str_replace("'","",$txt_requisition_date_to),"","",1);				
		}
		$requsition_date_cond = " and b.requisition_date BETWEEN  TO_DATE('$requisition_start_date','dd/mon/yyyy') and  TO_DATE('$requisition_end_date','dd/mon/yyyy')"; 
	}

	if($txt_sales_order_no!="")
	{
		$sales_result=sql_select("select id as po_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and job_no LIKE '%$txt_sales_order_no%'");
		$sales_po_id="";
		foreach($sales_result as $row)
		{
			if($sales_po_id=='') $sales_po_id=$row[csf('po_id')]; else $sales_po_id.=",".$row[csf('po_id')];
		}		
	}

	$po_array=array();

	if ($txt_file_no != '' || $txt_internal != '')
	{
		$po_sql="SELECT a.job_no,$year_field, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $year_cond  $internal_cond $file_no_cond";
		$po_sql_data=sql_select($po_sql);
		$all_po_id = '';
		foreach($po_sql_data as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
			$po_array[$row[csf('id')]]['year']=$row[csf('year')];
			$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];		
			$po_array[$row[csf('id')]]['ref_no']=$row[csf('grouping')];
			if($all_po_id=='') $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
		}	
	}

	if($date_cond!="")
	{
		$sql_demand="select b.requisition_no, a.id as demand_id
		from ppl_yarn_demand_entry_dtls b, ppl_yarn_demand_entry_mst a
		where  b.mst_id=a.id $date_cond and a.company_id=$company_name $demand_con and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
		$demand_data = sql_select($sql_demand);
		foreach($demand_data as $row)
		{
			$demand_requ_id.=$row[csf('requisition_no')].",";	
		}
	}
	
	$req_id_cond="";
	if($demand_requ_id!="") 
	{
		$demand_requ_id=substr($demand_requ_id,0,-1);
		$demand_requ_id=implode(",",array_filter(array_unique(explode(",",$demand_requ_id))));
		if($db_type==0) $req_id_cond="and b.requisition_no in(".$demand_requ_id.")";
		else
		{
			$req_ids=explode(",",$demand_requ_id);
			if(count($req_ids)>990)
			{
				$req_id_cond="and (";
				$req_ids=array_chunk($req_ids,990);
				$z=0;
				foreach($req_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $req_id_cond.=" b.requisition_no in(".$id.")";
					else $req_id_cond.=" or b.requisition_no in(".$id.")";
					$z++;
				}
				$req_id_cond.=")";
			}
			else $req_id_cond="and b.requisition_no in(".$demand_requ_id.")";
		}
	}

	$program_po_cond = ($all_po_id!="")?" and c.po_id in(".$all_po_id.")":"";
	$sales_program_po_cond = ($sales_po_id!="")?" and c.po_id in(".$sales_po_id.")":"";
	//$pro_sql="SELECT b.requisition_no, d.knitting_source, d.knitting_party, d.location_id,d.id as prog_no,d.program_qnty,c.buyer_id,c.po_id from ppl_yarn_requisition_entry b, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls c where b.knit_id=d.id and d.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $buyer_id_cond $req_cond $prog_cond $prog_id_cond $program_po_cond $sales_program_po_cond $req_id_cond group by d.id, b.requisition_no, d.knitting_source, d.knitting_party, d.location_id, c.buyer_id,c.po_id,d.program_qnty";

	$pro_sql="SELECT e.booking_no, b.requisition_no, d.knitting_source, d.knitting_party, d.location_id, d.id as prog_no, d.program_qnty, c.buyer_id, c.po_id, e.within_group from ppl_yarn_requisition_entry b, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_mst e where e.id=d.mst_id and b.knit_id=d.id and d.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $booking_cond $buyer_id_cond $req_cond $prog_cond $prog_id_cond $program_po_cond $sales_program_po_cond $req_id_cond group by e.booking_no,d.id, b.requisition_no, d.knitting_source, d.knitting_party, d.location_id, c.buyer_id,c.po_id,d.program_qnty, e.within_group";
	//within_group
	//echo $pro_sql;die;
	$pro_sql_data = sql_select($pro_sql);

	$po_id="";
	$plan_details_arr = array();
	$prog_booking = array();
	foreach($pro_sql_data as $row)
	{
		if($row[csf('within_group')] == 1)
		{
			$prog_booking[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}
		
		$plan_details_arr[$row[csf('prog_no')]]['program_qnty']+=$row[csf('program_qnty')];
		$plan_details_arr[$row[csf('prog_no')]]['buyer']=$row[csf('buyer_id')];
		$plan_details_arr[$row[csf('prog_no')]]['source']=$row[csf('knitting_source')];
		$plan_details_arr[$row[csf('prog_no')]]['knitting_party']=$row[csf('knitting_party')];
		$plan_details_arr[$row[csf('prog_no')]]['location']=$row[csf('location_id')];
		$plan_details_arr[$row[csf('prog_no')]]['within_group']=$row[csf('within_group')];
		$plan_details_arr[$row[csf('prog_no')]]['booking_no']=$row[csf('booking_no')];
		$plan_details_arr[$row[csf('prog_no')]]['po_id'].=$row[csf('po_id')].",";

		if($po_id=='') $po_id=$row[csf('po_id')]; else $po_id.=",".$row[csf('po_id')];
		//if($requisition_nos=='') $requisition_nos=$row[csf('requisition_no')]; else $requisition_nos.=",".$row[csf('requisition_no')];
		$requ_id.=$row[csf('requisition_no')].",";
	}
	unset($pro_sql_data);

	$req_id_cond="";
	if($requ_id!="") 
	{
		$requ_id=substr($requ_id,0,-1);
		$requ_id=implode(",",array_filter(array_unique(explode(",",$requ_id))));
		if($db_type==0) $req_id_cond="and b.requisition_no in(".$requ_id.")";
		else
		{
			$req_ids=explode(",",$requ_id);
			if(count($req_ids)>990)
			{
				$req_id_cond="and (";
				$req_ids=array_chunk($req_ids,990);
				$z=0;
				foreach($req_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $req_id_cond.=" b.requisition_no in(".$id.")";
					else $req_id_cond.=" or b.requisition_no in(".$id.")";
					$z++;
				}
				$req_id_cond.=")";
			}
			else $req_id_cond="and b.requisition_no in(".$requ_id.")";
		}
	}
	
	if ($txt_file_no == '' && $txt_internal == '')
	{
		$po_sql="SELECT a.job_no,$year_field, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($po_id) $year_cond";
		$po_sql_data=sql_select($po_sql);
		$all_po_id = '';
		foreach($po_sql_data as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
			$po_array[$row[csf('id')]]['year']=$row[csf('year')];
			$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];		
			$po_array[$row[csf('id')]]['ref_no']=$row[csf('grouping')];
		}	
	}

	//$requisition_nos_cond
	//$requisition_nos_cond = ($requisition_nos!="")?" and b.requisition_no in(".$requisition_nos.")":"";
	/*
	$sqlyarn = "SELECT b.knit_id,b.requisition_date, b.requisition_no,sum(b.yarn_qnty) as yarn_qnty,d.yarn_count_id, d.yarn_type, d.color as yarn_color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id,a.demand_prefix_number,a.demand_date,sum(c.yarn_demand_qnty) as demand_qnty
		from product_details_master d,ppl_yarn_requisition_entry b 
		left join ppl_yarn_demand_reqsn_dtls c on b.id=c.requisition_id and c.status_active=1 and c.is_deleted=0
		left join ppl_yarn_demand_entry_mst a on a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 where d.id=b.prod_id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		$requsition_date_cond $prog_cond $date_cond $req_cond $req_id_cond
		group by b.knit_id,b.requisition_date, b.requisition_no,d.yarn_count_id, d.yarn_type, d.color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id,a.demand_prefix_number,a.demand_date order by b.requisition_no";*/

	if($txt_demand_no!=''){
		$sqlyarn = "SELECT a.demand_system_no, b.knit_id,b.requisition_date, b.requisition_no,b.yarn_qnty,d.yarn_count_id, d.yarn_type, d.color as yarn_color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id ,
		RTRIM(XMLAGG(XMLELEMENT(e,a.demand_prefix_number,',').EXTRACT('//text()') ORDER BY a.demand_prefix_number).GETCLOBVAL(),',') AS demand_prefix_number,sum(c.yarn_demand_qnty) as demand_qnty from product_details_master d,ppl_yarn_requisition_entry b 
		left join ppl_yarn_demand_reqsn_dtls c on b.id=c.requisition_id and c.status_active=1 and c.is_deleted=0
		left join ppl_yarn_demand_entry_mst a on a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 
		where d.id=b.prod_id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 
		and d.is_deleted=0 $requsition_date_cond $prog_cond $date_cond $req_cond $req_id_cond $demand_con group by a.demand_system_no, b.knit_id,b.requisition_date, b.requisition_no,b.yarn_qnty,d.yarn_count_id, d.yarn_type, d.color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id order by b.requisition_no";
	}
	else{
		$sqlyarn = "SELECT b.knit_id,b.requisition_date, b.requisition_no,b.yarn_qnty,d.yarn_count_id, d.yarn_type, d.color as yarn_color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id ,
		RTRIM(XMLAGG(XMLELEMENT(e,a.demand_prefix_number,',').EXTRACT('//text()') ORDER BY a.demand_prefix_number).GETCLOBVAL(),',') AS demand_prefix_number,sum(c.yarn_demand_qnty) as demand_qnty from product_details_master d,ppl_yarn_requisition_entry b 
		left join ppl_yarn_demand_reqsn_dtls c on b.id=c.requisition_id and c.status_active=1 and c.is_deleted=0
		left join ppl_yarn_demand_entry_mst a on a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 
		where d.id=b.prod_id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 
		and d.is_deleted=0 $requsition_date_cond $prog_cond $date_cond $req_cond $req_id_cond $demand_con group by  b.knit_id,b.requisition_date, b.requisition_no,b.yarn_qnty,d.yarn_count_id, d.yarn_type, d.color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id order by b.requisition_no";
	}
	// echo $sqlyarn; die();
	$sql_yarn_result=sql_select($sqlyarn);

	$requ_id='';
	$prod_id="";
	$prog_id="";
	$yarn_color_id="";
	foreach($sql_yarn_result as $row)
	{
		$requ_id.=$row[csf('requisition_no')].",";
		$prod_id.=$row[csf('prod_id')].",";
		$prog_id.=$row[csf('knit_id')].",";
		$yarn_color_id.=$row[csf('yarn_color')].",";

	}

	$yarn_color_id = chop($yarn_color_id," , ");
	if($yarn_color_id!="")
	{
		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 and id in($yarn_color_id)", "id", "color_name" );
	}
	
	// requsition cond 
	$req_id_cond="";
	if($requ_id!="") 
	{
		$requ_id=substr($requ_id,0,-1);
		$requ_id=implode(",",array_filter(array_unique(explode(",",$requ_id))));
		if($db_type==0) $req_id_cond="and b.requisition_no in(".$requ_id.")";
		else
		{
			$req_ids=explode(",",$requ_id);
			if(count($req_ids)>990)
			{
				$req_id_cond="and (";
				$req_ids=array_chunk($req_ids,990);
				$z=0;
				foreach($req_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $req_id_cond.=" b.requisition_no in(".$id.")";
					else $req_id_cond.=" or b.requisition_no in(".$id.")";
					$z++;
				}
				$req_id_cond.=")";
			}
			else $req_id_cond="and b.requisition_no in(".$requ_id.")";
		}
	}

	// product cond 
	$prod_id_cond="";
	$demand_prod_id_cond="";
	if($prod_id!="") 
	{
		$prod_id=substr($prod_id,0,-1);
		$prod_id=implode(",",array_filter(array_unique(explode(",",$prod_id))));

		if($db_type==0) {
			$prod_id_cond="and c.id in(".$prod_id.")";
			$demand_prod_id_cond="and c.prod_id in(".$prod_id.")";
		}
		else
		{
			$prod_ids=explode(",",$prod_id);
			if(count($prod_ids)>990)
			{
				$prod_id_cond="and (";
				$demand_prod_id_cond="and (";
				$prod_ids=array_chunk($prod_ids,990);
				$z=0;
				foreach($prod_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) {
						$prod_id_cond.=" c.id in(".$id.")";
						$demand_prod_id_cond.=" c.prod_id in(".$id.")";
					}
					else {
						$prod_id_cond.=" or c.id in(".$id.")";
						$demand_prod_id_cond.=" or c.prod_id in(".$id.")";
					}
					$z++;
				}
				$prod_id_cond.=")";
				$demand_prod_id_cond.=")";
			}
			else {
				$prod_id_cond="and c.id in(".$prod_id.")";
				$demand_prod_id_cond="and c.prod_id in(".$prod_id.")";
			}
		}
	}
	//echo $prod_id_cond;

	// prog cond 
	$prog_id_cond="";
	if($prog_id!="") 
	{
		$prog_id=substr($prog_id,0,-1);
		$prog_id=implode(",",array_filter(array_unique(explode(",",$prog_id))));

		if($db_type==0) {
			$prog_id_cond="and b.knit_id in(".$prog_id.")";
		}
		else
		{
			$prog_ids=explode(",",$prog_id);
			if(count($prog_ids)>990)
			{
				$prog_id_cond="and (";

				$prog_ids=array_chunk($prog_ids,990);
				$z=0;
				foreach($prog_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) {
						$prog_id_cond.=" b.knit_id in(".$id.")";
					}
					else {
						$prog_id_cond.=" or b.knit_id in(".$id.")";
					}
					$z++;
				}
				$prog_id_cond.=")";
			}
			else {
				$prog_id_cond="and b.knit_id in(".$prog_id.")";
			}
		}
	}

	$po_id_cond="";
	if($po_id!="") 
	{
		$po_id=substr($po_id,0,-1);
		if($db_type==0) $po_id_cond="and b.id in(".$po_id.")";
		else
		{
			$po_ids=explode(",",$po_id);
			if(count($po_ids)>1000)
			{
				$po_id_cond="and (";
				$po_ids=array_chunk($po_ids,1000);
				$z=0;
				foreach($po_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $po_id_cond.=" b.id in(".$id.")";
					else $po_id_cond.=" or b.id in(".$id.")";
					$z++;
				}
				$po_id_cond.=")";
			}
			else $po_id_cond="and b.id in(".$po_id.")";
		}
	}

	if($prod_id_cond != "")
	{
		$product_sql= "select c.id, c.product_name_details, c.lot, c.supplier_id from product_details_master c where c.company_id=$company_name and c.item_category_id=1 $prod_id_cond";
		$product_data = sql_select($product_sql);
		$product_details_arr=array();
		foreach($product_data as $row)
		{
			$product_details_arr[$row[csf('id')]]['desc']=$row[csf('product_name_details')];
			$product_details_arr[$row[csf('id')]]['lot']=$row[csf('lot')]; 
			$product_details_arr[$row[csf('id')]]['supplier']=$row[csf('supplier_id')]; 
		}
		unset($product_data);
	}
	

	if($txt_demand_no!=''){
		$yarn_sql="SELECT  a.remarks,b.requisition_no as requ_no, b.demand_no, b.store_id, b.brand_id, b.cons_quantity as issue_qnty, b.return_qnty,c.yarn_type, c.yarn_count_id, c.lot, c.color,b.prod_id from inv_issue_master a, inv_transaction b, product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$company_name and a.issue_basis in (3,8) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $req_id_cond $prod_id_cond";
	}
	else{
		$yarn_sql="SELECT  a.remarks,b.requisition_no as requ_no, b.store_id, b.brand_id, b.cons_quantity as issue_qnty, b.return_qnty,c.yarn_type, c.yarn_count_id, c.lot, c.color,b.prod_id from inv_issue_master a, inv_transaction b, product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$company_name and a.issue_basis in (3,8) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $req_id_cond $prod_id_cond";
	}

	//echo $yarn_sql; die;
	$yarn_sql_data = sql_select($yarn_sql);
	$yarn_issue_details_arr=array();
	$yarn_issue_remark_arr=array();
	foreach($yarn_sql_data as $row)
	{

		$yarn_issue_details_arr1[$row[csf('requ_no')]][$row[csf('prod_id')]][$row[csf('demand_no')]]['qty1']=$row[csf('issue_qnty')];

		$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['qty']+=$row[csf('issue_qnty')];
		$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['ret_qty']+=$row[csf('return_qnty')];
		$yarn_issue_remark_arr[$row[csf('requ_no')]][$row[csf('yarn_count_id')]][$row[csf('yarn_type')]][$row[csf('color')]][$row[csf('lot')]]['remark']=$row[csf('remarks')];
	}
	unset($yarn_sql_data);
	
	$bkn_no_cond="";
	if(!empty($prog_booking)) 
	{
		if(count($prog_booking)>990)
		{
			$bkn_no_cond=" and (";
			$bkn_ids=array_chunk($prog_booking,990);
			$z=0;
			foreach($bkn_ids as $id)
			{
				$id="'".implode("','",$id)."'";
				if($z==0)
					$bkn_no_cond.=" booking_no in(".$id.")";
				else
					$bkn_no_cond.=" or booking_no in(".$id.")";
				$z++;
			}
			$bkn_no_cond.=")";
		}
		else
		{
			$bkn_no_cond=" and booking_no in('".implode("','", $prog_booking)."')";
		}
		
		$sql_bkn = sql_select("select booking_no, buyer_id from wo_booking_mst where status_active = 1 and is_deleted = 0".$bkn_no_cond);
		$bkn_buyer = array();
		foreach($sql_bkn as $row)
		{
			$bkn_buyer[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
		}
	}

	ob_start();
	?>
	<fieldset style="width:1795px;">
		<table cellpadding="0" cellspacing="0" width="1770">
			<tr>
			   <td align="center" width="100%" style="font-size:16px"><strong>Requisition Against Demand Status</strong><br><b>
                   <? if($start_date!='') echo change_date_format($start_date).' To '.change_date_format($end_date);else echo '';?></b>
               </td>
			</tr>
		</table>	
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1790" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">Buyer</th>
                <th width="80">File No</th>
                <th width="80">Ref. No</th>
				<th width="80">Prog. No</th>
                <th width="100">Knitting Company</th>
                <th width="80">Req. Date</th>
				<th width="80">Req. No</th>
				
				<th width="80">Y. Count</th>
				<th width="120">Y Composition</th>
				<th width="80">Y. Type</th>
				<th width="100">Brand</th>
				<th width="80">Y. Color</th>
                <th width="60">Lot</th>
				<th width="70">Program Qty.</th>
				<th width="70">Requisition Qty.</th>
                <th width="70">Demand Qty.</th>
				
				<th width="70">Issue Qty</th>
				<th width="70">Balance Qty</th>
				<th width="70">Returnble Qty</th>
				<th width="">Remarks</th>
			</thead>
		</table>
		<div style="width:1795px; overflow-y:scroll; max-height:330px; margin-left: -20px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1770" class="rpt_table" id="tbl_list_search">
                <?				
				$i=1;$k=0;$z=0;$m=0;$s=0;$req_row='';$tt=1;
				$total_program_qty=0;$total_demand_qty=0;$total_demand_issue=0;$total_demand_blanace=0;
				$balance_qty = array();
				$requsition_id_array = array();
				$prog_requsition_id_array = array();
				$yarn_requsition_id_array = array();
				$yarn_remark_id_array = array();
				
                foreach($sql_yarn_result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$prog_no  = $row[csf('knit_id')];
					$req_no   = $row[csf('requisition_no')];
					$req_date = $row[csf('requisition_date')];
					$prod_id  = $row[csf('prod_id')];
					//echo count($req_row);
					$knit_source = "";
					$knit_source = $plan_details_arr[$prog_no]['source'];
					$po_ids      = array_unique(explode(",", chop($plan_details_arr[$prog_no]['po_id'] ,",") ));
					$knit_company="";
					if($knit_source==1) $knit_company=$location_library[$plan_details_arr[$prog_no]['location']];
					else if($knit_source==3) $knit_company=$supplierArr[$plan_details_arr[$prog_no]['knitting_party']];
					
					$file_no='';	$ref_no='';
					foreach($po_ids as $row_id)
					{
						if($file_no=='') $file_no=$po_array[$row_id]['file_no']; else  $file_no.=",".$po_array[$row_id]['file_no'];
						if($ref_no=='') $ref_no=$po_array[$row_id]['ref_no']; else  $ref_no.=",".$po_array[$row_id]['ref_no'];
					}

					$ref_no   = implode(",",array_unique(explode(",", chop($ref_no, ","))) );
					$file_no  = implode(",",array_unique(explode(",", $file_no)));
					$prog_qty = $plan_details_arr[$prog_no]['program_qnty'];
					
					//for buyer
					if ($plan_details_arr[$prog_no]['within_group'] == 1)
					{
						$bkn = $plan_details_arr[$prog_no]['booking_no'];
						$buyer = $bkn_buyer[$bkn]['buyer_id'];
					}
					else
					{
						$buyer    = $plan_details_arr[$prog_no]['buyer'];
					}
					
					$issue_qty = $yarn_issue_details_arr[$req_no][$prod_id]['qty'];					
					$issue_returnble_qty = $yarn_issue_details_arr[$req_no][$prod_id]['ret_qty'];
					$issue_remark = $yarn_issue_remark_arr[$req_no][$row[csf('yarn_count_id')]][$row[csf('yarn_type')]][$row[csf('yarn_color')]][$row[csf('lot')]]['remark'];
					$demand_hidden_id = '';

					if($txt_demand_no!=''){
					
						$demand_hidden_id = $row[csf('demand_system_no')];

						$issue_qty = $yarn_issue_details_arr1[$req_no][$prod_id][$row[csf('demand_system_no')]]['qty1'];
					}
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <?
                        if ($txt_internal == "")
                        {
	                      	if (!in_array($req_no,$requsition_id_array) )
							{
								$k++;
								?>
	                            <td width="30" align="center"><? echo $k; ?></td>
	                            <td width="100" align="center"><p><? echo $buyer_name_arr[$buyer]; ?></p></td>
	                            <td width="80" align="center"><p><? echo $file_no; ?></p></td>
	                            <td width="80" align="center"><div style="width:80px; word-wrap:break-word;"><? echo $ref_no; ?></div></td>
	                            <td width="80" align="center"><p><? echo $prog_no; ?></p></td>
	                            <td width="100" align="center"><p><? echo $knit_company; ?></p></td>
	                            <td width="80" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
	                            <td width="80" align="center"><p><? echo $req_no; ?></p></td>
	                            <?
								$requsition_id_array[] = $req_no;
							}
							else
							{
								?>
								<td width="30" align="center"></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80"><p></p></td>
	                            <td width="80"><p></p></td>
							    <?
							}
							?>
	                      
	                        <td width="80" ><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
	                        <td width="120"><div style="width:120px; word-wrap:break-word;"><? echo $composition[$row[csf('yarn_comp_type1st')]]; ?></div></td>
	                        <td width="80" align="center"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td width="100" align="center"><p><? echo $brand_name_arr[$row[csf('brand')]]; ?></p></td>
	                        <td width="80" align="center"><p><? echo $color_library[$row[csf('yarn_color')]]; ?></p></td>
	                        <td width="60" align="center"><p><? echo $row[csf('lot')];?></p></td>
	                        <?
	                        if (!in_array($req_no,$prog_requsition_id_array) )
							{
								$z++;
								$balance_qty[$prog_no]=$prog_qty;
								$total_program_qty+=$prog_qty;
						  	    ?>
	                      	    <td width="70" align="right"><p><? echo number_format($prog_qty,2); ?></p></td>
	                      	    <?
						  	    $prog_requsition_id_array[]=$req_no;
							}
							else
							{ 
								?>
							 	<td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
							    <?	
							}
							?>
						    <td width="70" align="right"><p>
						  		<? 
						  		$yarn_requsition_qnty = $row[csf('yarn_qnty')];
						  		echo number_format($yarn_requsition_qnty,2); 
						  		?>
						    </p></td>

	                        <td width="70" align="right" ><a href="##" onClick="openmy_popup(<? echo $row[csf("requisition_no")];?>,<? echo $prod_id;?>,'demand_popup','<? echo $start_date;?>','<? echo $end_date;?>')" ><? echo number_format($row[csf("demand_qnty")],2); ?></a>
	                        	<input type="hidden" name="txt_hidden_demand_no_<?php echo $row[csf("requisition_no")];?>" id="txt_hidden_demand_no_<?php echo $row[csf("requisition_no")];?>" class="text_boxes" value="<?php echo $demand_hidden_id;?>">
	                        </td>
	                       
	                        <?
	                        if (!in_array($req_no,$yarn_requsition_id_array) )
							{
								$m++;							
						 		?>
	                            <td width="70" align="right"><p><a href="##" onClick="openmy_popup(<? echo $req_no;?>,<? echo $prod_id;?>,'issue_popup','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $company_name;?>')">
	                           	    <? echo number_format($issue_qty,2); ?></a>
	                            </p></td>
	                            <td width="70" align="right"><p>
	                          	    <? 
	                          	    if($txt_demand_no!=''){
	                          	    	$balance_quantity = ($row[csf("demand_qnty")]-$issue_qty);
	                          	    }
	                          	    else{
	                          	    	$balance_quantity = ($yarn_requsition_qnty-$issue_qty);
	                          	    }
	                          	    echo number_format($balance_quantity,2); ?>                          	
	                            </p></td>
	                            <td width="70" align="right"><p><? echo number_format($issue_returnble_qty,2); ?></p></td>
	                            <td width=""><div style=" word-break:break-all"><? echo $issue_remark; ?></div></td>
	                      	    <?
							}
							else
							{ 
							    ?>
							 	<td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
	                            <td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
	                            <td width=""><div style=" word-break:break-all"><? //echo $issue_remark; ?></div></td>
							    <?	
							}
						}
						else if ($txt_internal != "" && $txt_internal == $ref_no)
						{
	                      	if (!in_array($req_no,$requsition_id_array) )
							{
								$k++;
								?>
	                            <td width="30" align="center"><? echo $k; ?></td>
	                            <td width="100" align="center"><p><? echo $buyer_name_arr[$buyer]; ?></p></td>
	                            <td width="80" align="center"><p><? echo $file_no; ?></p></td>
	                            <td width="80" align="center"><div style="width:80px; word-wrap:break-word;"><? echo $ref_no; ?></div></td>
	                            <td width="80" align="center"><p><? echo $prog_no; ?></p></td>
	                            <td width="100" align="center"><p><? echo $knit_company; ?></p></td>
	                            <td width="80" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
	                            <td width="80" align="center"><p><? echo $req_no; ?></p></td>
	                            <?
								$requsition_id_array[] = $req_no;
							}
							else
							{
								?>
								<td width="30" align="center"></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80"><p></p></td>
	                            <td width="80"><p></p></td>
							    <?
							}
							?>
	                      
	                        <td width="80" ><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
	                        <td width="120"><div style="width:120px; word-wrap:break-word;"><? echo $composition[$row[csf('yarn_comp_type1st')]]; ?></div></td>
	                        <td width="80" align="center"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td width="100" align="center"><p><? echo $brand_name_arr[$row[csf('brand')]]; ?></p></td>
	                        <td width="80" align="center"><p><? echo $color_library[$row[csf('yarn_color')]]; ?></p></td>
	                        <td width="60" align="center"><p><? echo $row[csf('lot')];?></p></td>
	                        <?
	                        if (!in_array($req_no,$prog_requsition_id_array) )
							{
								$z++;
								$balance_qty[$prog_no]=$prog_qty;
								$total_program_qty+=$prog_qty;
						  	    ?>
	                      	    <td width="70" align="right"><p><? echo number_format($prog_qty,2); ?></p></td>
	                      	    <?
						  	    $prog_requsition_id_array[]=$req_no;
							}
							else
							{ 
								?>
							 	<td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
							    <?	
							}
							?>
						    <td width="70" align="right"><p>
						  		<? 
						  		$yarn_requsition_qnty = $row[csf('yarn_qnty')];
						  		echo number_format($yarn_requsition_qnty,2); 
						  		?>
						    </p></td>

	                        <td width="70" align="right" ><a href="##" onClick="openmy_popup(<? echo $row[csf("requisition_no")];?>,<? echo $prod_id;?>,'demand_popup','<? echo $start_date;?>','<? echo $end_date;?>')" ><? echo number_format($row[csf("demand_qnty")],2); ?></a></td>
	                       
	                        <?
	                        if (!in_array($req_no,$yarn_requsition_id_array) )
							{
								$m++;							
						 		?>
	                            <td width="70" align="right"><p><a href="##" onClick="openmy_popup(<? echo $req_no;?>,<? echo $prod_id;?>,'issue_popup','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $company_name;?>')">
	                           	    <? echo number_format($issue_qty,2); ?></a>
	                            </p></td>
	                            <td width="70" align="right"><p>
	                          	    <? 
	                          	    $balance_quantity = ($yarn_requsition_qnty-$issue_qty);
	                          	    echo number_format($balance_quantity,2); ?>                          	
	                            </p></td>
	                            <td width="70" align="right"><p><? echo number_format($issue_returnble_qty,2); ?></p></td>
	                            <td width=""><div style=" word-break:break-all"><? echo $issue_remark; ?></div></td>
	                      	    <?
							}
							else
							{ 
							    ?>
							 	<td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
	                            <td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
	                            <td width=""><div style=" word-break:break-all"><? //echo $issue_remark; ?></div></td>
							    <?	
							}
						}	
						?>
                    </tr>
                    <?							
					$i++;
					$total_requsition_qnty += $row[csf('yarn_qnty')];
					$total_demand_qty += $row[csf("demand_qnty")];
					$total_demand_blanace += $prog_qty-$row[csf("demand_qnty")];
					$total_demand_issue += $issue_qty;
					$total_balance_quantity += ($row[csf('yarn_qnty')]-$issue_qty);
					$total_issue_returnble_qty += $issue_returnble_qty;
				}
				?>
			</table>
            <table width="1770"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	            <tfoot>
	                <tr>
	               		<th width="30">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80">&nbsp;</th>                            
	                    <th width="80">&nbsp;</th>
	                    <th width="120">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
						<th width="60">Total</th>
						<th width="70" id="value_tot_prog"><?php echo $total_program_qty;?></th>
						<th width="70" id="value_tot_demand"><?php echo $total_demand_qty;?></th>
	                    <th width="70" id="value_tot_requsition"><?php echo $total_requsition_qnty;?></th>  
	                    <th width="70" id="value_tot_issue"><?php echo $total_demand_issue;?></th>
	                    <th width="70" id="value_tot_balance3"><?php echo number_format($total_balance_quantity,2);?></th>
	                    <th width="70" id ="value_to_issue_return"><? echo number_format($total_issue_returnble_qty,2); ?> </th>
	                    <th></th>
	                </tr>
				</tfoot>
            </table>
		</div>
	</fieldset>      
	<?	
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="requires/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 	
}

if($action=="report_generate_xl")
{
	// echo "<h1>what</h1>"; die;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

 	$company_name=str_replace("'","",$cbo_company_name);

 	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$brand_name_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$supplierArr=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name");

	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	if($cbo_buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and c.buyer_id=$cbo_buyer_name"; 	
	}

	$cbo_year=str_replace("'","",$cbo_year);
	$year_cond="";
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}

	$txt_prog_no=trim(str_replace("'","",$txt_prog_no));
	$txt_req_no=trim(str_replace("'","",$txt_req_no));
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_internal=trim(str_replace("'","",$txt_ref_no));
	$txt_sales_order_no=trim(str_replace("'","",$txt_sales_order_no));
	$txt_fabric_booking_no=trim(str_replace("'","",$txt_fabric_booking_no));

	$txt_demand_no=trim(str_replace("'","",$txt_demand_no));

	$demand_con = '';
	if($txt_demand_no!=''){
		$demand_con = "and a.demand_system_no LIKE '%$txt_demand_no%'";
	}

	if($txt_file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='$txt_file_no' ";
	if($txt_internal=="") $internal_cond=""; else $internal_cond=" and b.grouping='$txt_internal' ";
	if($txt_prog_no=="") $prog_cond=""; else $prog_cond=" and b.knit_id in($txt_prog_no) ";
	if($txt_req_no=="") $req_cond=""; else $req_cond=" and b.requisition_no in($txt_req_no)";
	if($txt_fabric_booking_no=="") $booking_cond=""; else $booking_cond=" and e.booking_no like('%$txt_fabric_booking_no')";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	
	$date_cond="";
	if(str_replace("'","",$txt_date_from)!="" || str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		$date_cond=" and a.demand_date between '$start_date' and '$end_date'";
	}

	$requsition_date_cond="";
	if(str_replace("'","",$txt_requisition_date_from	)!="" || str_replace("'","",$txt_requisition_date_to)!="")
	{
		if($db_type==0)
		{
			$requisition_start_date=change_date_format(str_replace("'","",$txt_requisition_date_from),"yyyy-mm-dd","");
			$requsition_end_date=change_date_format(str_replace("'","",$txt_requisition_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$requisition_start_date=change_date_format(str_replace("'","",$txt_requisition_date_from),"","",1);
			$requisition_end_date=change_date_format(str_replace("'","",$txt_requisition_date_to),"","",1);				
		}
		$requsition_date_cond = " and b.requisition_date BETWEEN  TO_DATE('$requisition_start_date','dd/mon/yyyy') and  TO_DATE('$requisition_end_date','dd/mon/yyyy')"; 
	}

	if($txt_sales_order_no!="")
	{
		$sales_result=sql_select("select id as po_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and job_no LIKE '%$txt_sales_order_no%'");
		$sales_po_id="";
		foreach($sales_result as $row)
		{
			if($sales_po_id=='') $sales_po_id=$row[csf('po_id')]; else $sales_po_id.=",".$row[csf('po_id')];
		}		
	}

	$po_array=array();

	if ($txt_file_no != '' || $txt_internal != '')
	{
		$po_sql="SELECT a.job_no,$year_field, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $year_cond  $internal_cond $file_no_cond";
		$po_sql_data=sql_select($po_sql);
		$all_po_id = '';
		foreach($po_sql_data as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
			$po_array[$row[csf('id')]]['year']=$row[csf('year')];
			$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];		
			$po_array[$row[csf('id')]]['ref_no']=$row[csf('grouping')];
			if($all_po_id=='') $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
		}	
	}

	if($date_cond!="")
	{
		$sql_demand="select b.requisition_no, a.id as demand_id
		from ppl_yarn_demand_entry_dtls b, ppl_yarn_demand_entry_mst a
		where  b.mst_id=a.id $date_cond and a.company_id=$company_name $demand_con and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
		$demand_data = sql_select($sql_demand);
		foreach($demand_data as $row)
		{
			$demand_requ_id.=$row[csf('requisition_no')].",";	
		}
	}
	
	$req_id_cond="";
	if($demand_requ_id!="") 
	{
		$demand_requ_id=substr($demand_requ_id,0,-1);
		$demand_requ_id=implode(",",array_filter(array_unique(explode(",",$demand_requ_id))));
		if($db_type==0) $req_id_cond="and b.requisition_no in(".$demand_requ_id.")";
		else
		{
			$req_ids=explode(",",$demand_requ_id);
			if(count($req_ids)>990)
			{
				$req_id_cond="and (";
				$req_ids=array_chunk($req_ids,990);
				$z=0;
				foreach($req_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $req_id_cond.=" b.requisition_no in(".$id.")";
					else $req_id_cond.=" or b.requisition_no in(".$id.")";
					$z++;
				}
				$req_id_cond.=")";
			}
			else $req_id_cond="and b.requisition_no in(".$demand_requ_id.")";
		}
	}

	$program_po_cond = ($all_po_id!="")?" and c.po_id in(".$all_po_id.")":"";
	$sales_program_po_cond = ($sales_po_id!="")?" and c.po_id in(".$sales_po_id.")":"";
	//$pro_sql="SELECT b.requisition_no, d.knitting_source, d.knitting_party, d.location_id,d.id as prog_no,d.program_qnty,c.buyer_id,c.po_id from ppl_yarn_requisition_entry b, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls c where b.knit_id=d.id and d.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $buyer_id_cond $req_cond $prog_cond $prog_id_cond $program_po_cond $sales_program_po_cond $req_id_cond group by d.id, b.requisition_no, d.knitting_source, d.knitting_party, d.location_id, c.buyer_id,c.po_id,d.program_qnty";

	$pro_sql="SELECT e.booking_no, b.requisition_no, d.knitting_source, d.knitting_party, d.location_id, d.id as prog_no, d.program_qnty, c.buyer_id, c.po_id, e.within_group from ppl_yarn_requisition_entry b, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_mst e where e.id=d.mst_id and b.knit_id=d.id and d.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $booking_cond $buyer_id_cond $req_cond $prog_cond $prog_id_cond $program_po_cond $sales_program_po_cond $req_id_cond group by e.booking_no,d.id, b.requisition_no, d.knitting_source, d.knitting_party, d.location_id, c.buyer_id,c.po_id,d.program_qnty, e.within_group";
	//within_group
	//echo $pro_sql;die;
	$pro_sql_data = sql_select($pro_sql);

	$po_id="";
	$plan_details_arr = array();
	$prog_booking = array();
	foreach($pro_sql_data as $row)
	{
		if($row[csf('within_group')] == 1)
		{
			$prog_booking[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}
		
		$plan_details_arr[$row[csf('prog_no')]]['program_qnty']+=$row[csf('program_qnty')];
		$plan_details_arr[$row[csf('prog_no')]]['buyer']=$row[csf('buyer_id')];
		$plan_details_arr[$row[csf('prog_no')]]['source']=$row[csf('knitting_source')];
		$plan_details_arr[$row[csf('prog_no')]]['knitting_party']=$row[csf('knitting_party')];
		$plan_details_arr[$row[csf('prog_no')]]['location']=$row[csf('location_id')];
		$plan_details_arr[$row[csf('prog_no')]]['within_group']=$row[csf('within_group')];
		$plan_details_arr[$row[csf('prog_no')]]['booking_no']=$row[csf('booking_no')];
		$plan_details_arr[$row[csf('prog_no')]]['po_id'].=$row[csf('po_id')].",";

		if($po_id=='') $po_id=$row[csf('po_id')]; else $po_id.=",".$row[csf('po_id')];
		//if($requisition_nos=='') $requisition_nos=$row[csf('requisition_no')]; else $requisition_nos.=",".$row[csf('requisition_no')];
		$requ_id.=$row[csf('requisition_no')].",";
	}
	unset($pro_sql_data);

	$req_id_cond="";
	if($requ_id!="") 
	{
		$requ_id=substr($requ_id,0,-1);
		$requ_id=implode(",",array_filter(array_unique(explode(",",$requ_id))));
		if($db_type==0) $req_id_cond="and b.requisition_no in(".$requ_id.")";
		else
		{
			$req_ids=explode(",",$requ_id);
			if(count($req_ids)>990)
			{
				$req_id_cond="and (";
				$req_ids=array_chunk($req_ids,990);
				$z=0;
				foreach($req_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $req_id_cond.=" b.requisition_no in(".$id.")";
					else $req_id_cond.=" or b.requisition_no in(".$id.")";
					$z++;
				}
				$req_id_cond.=")";
			}
			else $req_id_cond="and b.requisition_no in(".$requ_id.")";
		}
	}
	
	if ($txt_file_no == '' && $txt_internal == '')
	{
		$po_sql="SELECT a.job_no,$year_field, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($po_id) $year_cond";
		$po_sql_data=sql_select($po_sql);
		$all_po_id = '';
		foreach($po_sql_data as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
			$po_array[$row[csf('id')]]['year']=$row[csf('year')];
			$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];		
			$po_array[$row[csf('id')]]['ref_no']=$row[csf('grouping')];
		}	
	}


	if($txt_demand_no!=''){
		$sqlyarn = "SELECT a.demand_system_no, b.knit_id,b.requisition_date, b.requisition_no,b.yarn_qnty,d.yarn_count_id, d.yarn_type, d.color as yarn_color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id ,
		RTRIM(XMLAGG(XMLELEMENT(e,a.demand_prefix_number,',').EXTRACT('//text()') ORDER BY a.demand_prefix_number).GETCLOBVAL(),',') AS demand_prefix_number,sum(c.yarn_demand_qnty) as demand_qnty from product_details_master d,ppl_yarn_requisition_entry b 
		left join ppl_yarn_demand_reqsn_dtls c on b.id=c.requisition_id and c.status_active=1 and c.is_deleted=0
		left join ppl_yarn_demand_entry_mst a on a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 
		where d.id=b.prod_id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 
		and d.is_deleted=0 $requsition_date_cond $prog_cond $date_cond $req_cond $req_id_cond $demand_con group by a.demand_system_no, b.knit_id,b.requisition_date, b.requisition_no,b.yarn_qnty,d.yarn_count_id, d.yarn_type, d.color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id order by b.requisition_no";
	}
	else{
		$sqlyarn = "SELECT b.knit_id,b.requisition_date, b.requisition_no,b.yarn_qnty,d.yarn_count_id, d.yarn_type, d.color as yarn_color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id ,
		RTRIM(XMLAGG(XMLELEMENT(e,a.demand_prefix_number,',').EXTRACT('//text()') ORDER BY a.demand_prefix_number).GETCLOBVAL(),',') AS demand_prefix_number,sum(c.yarn_demand_qnty) as demand_qnty from product_details_master d,ppl_yarn_requisition_entry b 
		left join ppl_yarn_demand_reqsn_dtls c on b.id=c.requisition_id and c.status_active=1 and c.is_deleted=0
		left join ppl_yarn_demand_entry_mst a on a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 
		where d.id=b.prod_id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 
		and d.is_deleted=0 $requsition_date_cond $prog_cond $date_cond $req_cond $req_id_cond $demand_con group by  b.knit_id,b.requisition_date, b.requisition_no,b.yarn_qnty,d.yarn_count_id, d.yarn_type, d.color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id order by b.requisition_no";
	}
	// echo $sqlyarn; die();
	$sql_yarn_result=sql_select($sqlyarn);

	$requ_id='';
	$prod_id="";
	$prog_id="";
	$yarn_color_id="";
	foreach($sql_yarn_result as $row)
	{
		$requ_id.=$row[csf('requisition_no')].",";
		$prod_id.=$row[csf('prod_id')].",";
		$prog_id.=$row[csf('knit_id')].",";
		$yarn_color_id.=$row[csf('yarn_color')].",";

	}

	$yarn_color_id = chop($yarn_color_id," , ");
	if($yarn_color_id!="")
	{
		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 and id in($yarn_color_id)", "id", "color_name" );
	}
	
	// requsition cond 
	$req_id_cond="";
	if($requ_id!="") 
	{
		$requ_id=substr($requ_id,0,-1);
		$requ_id=implode(",",array_filter(array_unique(explode(",",$requ_id))));
		if($db_type==0) $req_id_cond="and b.requisition_no in(".$requ_id.")";
		else
		{
			$req_ids=explode(",",$requ_id);
			if(count($req_ids)>990)
			{
				$req_id_cond="and (";
				$req_ids=array_chunk($req_ids,990);
				$z=0;
				foreach($req_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $req_id_cond.=" b.requisition_no in(".$id.")";
					else $req_id_cond.=" or b.requisition_no in(".$id.")";
					$z++;
				}
				$req_id_cond.=")";
			}
			else $req_id_cond="and b.requisition_no in(".$requ_id.")";
		}
	}

	// product cond 
	$prod_id_cond="";
	$demand_prod_id_cond="";
	if($prod_id!="") 
	{
		$prod_id=substr($prod_id,0,-1);
		$prod_id=implode(",",array_filter(array_unique(explode(",",$prod_id))));

		if($db_type==0) {
			$prod_id_cond="and c.id in(".$prod_id.")";
			$demand_prod_id_cond="and c.prod_id in(".$prod_id.")";
		}
		else
		{
			$prod_ids=explode(",",$prod_id);
			if(count($prod_ids)>990)
			{
				$prod_id_cond="and (";
				$demand_prod_id_cond="and (";
				$prod_ids=array_chunk($prod_ids,990);
				$z=0;
				foreach($prod_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) {
						$prod_id_cond.=" c.id in(".$id.")";
						$demand_prod_id_cond.=" c.prod_id in(".$id.")";
					}
					else {
						$prod_id_cond.=" or c.id in(".$id.")";
						$demand_prod_id_cond.=" or c.prod_id in(".$id.")";
					}
					$z++;
				}
				$prod_id_cond.=")";
				$demand_prod_id_cond.=")";
			}
			else {
				$prod_id_cond="and c.id in(".$prod_id.")";
				$demand_prod_id_cond="and c.prod_id in(".$prod_id.")";
			}
		}
	}
	//echo $prod_id_cond;

	// prog cond 
	$prog_id_cond="";
	if($prog_id!="") 
	{
		$prog_id=substr($prog_id,0,-1);
		$prog_id=implode(",",array_filter(array_unique(explode(",",$prog_id))));

		if($db_type==0) {
			$prog_id_cond="and b.knit_id in(".$prog_id.")";
		}
		else
		{
			$prog_ids=explode(",",$prog_id);
			if(count($prog_ids)>990)
			{
				$prog_id_cond="and (";

				$prog_ids=array_chunk($prog_ids,990);
				$z=0;
				foreach($prog_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) {
						$prog_id_cond.=" b.knit_id in(".$id.")";
					}
					else {
						$prog_id_cond.=" or b.knit_id in(".$id.")";
					}
					$z++;
				}
				$prog_id_cond.=")";
			}
			else {
				$prog_id_cond="and b.knit_id in(".$prog_id.")";
			}
		}
	}

	$po_id_cond="";
	if($po_id!="") 
	{
		$po_id=substr($po_id,0,-1);
		if($db_type==0) $po_id_cond="and b.id in(".$po_id.")";
		else
		{
			$po_ids=explode(",",$po_id);
			if(count($po_ids)>1000)
			{
				$po_id_cond="and (";
				$po_ids=array_chunk($po_ids,1000);
				$z=0;
				foreach($po_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $po_id_cond.=" b.id in(".$id.")";
					else $po_id_cond.=" or b.id in(".$id.")";
					$z++;
				}
				$po_id_cond.=")";
			}
			else $po_id_cond="and b.id in(".$po_id.")";
		}
	}

	if($prod_id_cond != "")
	{
		$product_sql= "select c.id, c.product_name_details, c.lot, c.supplier_id from product_details_master c where c.company_id=$company_name and c.item_category_id=1 $prod_id_cond";
		$product_data = sql_select($product_sql);
		$product_details_arr=array();
		foreach($product_data as $row)
		{
			$product_details_arr[$row[csf('id')]]['desc']=$row[csf('product_name_details')];
			$product_details_arr[$row[csf('id')]]['lot']=$row[csf('lot')]; 
			$product_details_arr[$row[csf('id')]]['supplier']=$row[csf('supplier_id')]; 
		}
		unset($product_data);
	}
	

	if($txt_demand_no!=''){
		$yarn_sql="SELECT  a.remarks,b.requisition_no as requ_no, b.demand_no, b.store_id, b.brand_id, b.cons_quantity as issue_qnty, b.return_qnty,c.yarn_type, c.yarn_count_id, c.lot, c.color,b.prod_id from inv_issue_master a, inv_transaction b, product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$company_name and a.issue_basis in (3,8) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $req_id_cond $prod_id_cond";
	}
	else{
		$yarn_sql="SELECT  a.remarks,b.requisition_no as requ_no, b.store_id, b.brand_id, b.cons_quantity as issue_qnty, b.return_qnty,c.yarn_type, c.yarn_count_id, c.lot, c.color,b.prod_id from inv_issue_master a, inv_transaction b, product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$company_name and a.issue_basis in (3,8) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $req_id_cond $prod_id_cond";
	}

	//echo $yarn_sql; die;
	$yarn_sql_data = sql_select($yarn_sql);
	$yarn_issue_details_arr=array();
	$yarn_issue_remark_arr=array();
	foreach($yarn_sql_data as $row)
	{

		$yarn_issue_details_arr1[$row[csf('requ_no')]][$row[csf('prod_id')]][$row[csf('demand_no')]]['qty1']=$row[csf('issue_qnty')];

		$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['qty']+=$row[csf('issue_qnty')];
		$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['ret_qty']+=$row[csf('return_qnty')];
		$yarn_issue_remark_arr[$row[csf('requ_no')]][$row[csf('yarn_count_id')]][$row[csf('yarn_type')]][$row[csf('color')]][$row[csf('lot')]]['remark']=$row[csf('remarks')];
	}
	unset($yarn_sql_data);
	
	$bkn_no_cond="";
	if(!empty($prog_booking)) 
	{
		if(count($prog_booking)>990)
		{
			$bkn_no_cond=" and (";
			$bkn_ids=array_chunk($prog_booking,990);
			$z=0;
			foreach($bkn_ids as $id)
			{
				$id="'".implode("','",$id)."'";
				if($z==0)
					$bkn_no_cond.=" booking_no in(".$id.")";
				else
					$bkn_no_cond.=" or booking_no in(".$id.")";
				$z++;
			}
			$bkn_no_cond.=")";
		}
		else
		{
			$bkn_no_cond=" and booking_no in('".implode("','", $prog_booking)."')";
		}
		
		$sql_bkn = sql_select("select booking_no, buyer_id from wo_booking_mst where status_active = 1 and is_deleted = 0".$bkn_no_cond);
		$bkn_buyer = array();
		foreach($sql_bkn as $row)
		{
			$bkn_buyer[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
		}
	}

	// ob_start();
	$html = '';
	$html = '
	<table cellpadding="0" cellspacing="0" width="1770">
		<tr>
		   <td align="center" width="100%" style="font-size:16px"><strong>Requisition Against Demand Status</strong><br><b>';
			if($start_date!=''){
				$html .=  change_date_format($start_date).' To '.change_date_format($end_date);
			}
			$html .= '</b>
			</td>
		 </tr>
	 </table>	
	 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1790" class="rpt_table">
		 <thead>
			 <th width="30">SL</th>
			 <th width="100">Buyer</th>
			 <th width="80">File No</th>
			 <th width="80">Ref. No</th>
			 <th width="80">Prog. No</th>
			 <th width="100">Knitting Company</th>
			 <th width="80">Req. Date</th>
			 <th width="80">Req. No</th>
			 
			 <th width="80">Y. Count</th>
			 <th width="120">Y Composition</th>
			 <th width="80">Y. Type</th>
			 <th width="100">Brand</th>
			 <th width="80">Y. Color</th>
			 <th width="60">Lot</th>
			 <th width="70">Program Qty.</th>
			 <th width="70">Requisition Qty.</th>
			 <th width="70">Demand Qty.</th>
			 
			 <th width="70">Issue Qty</th>
			 <th width="70">Balance Qty</th>
			 <th width="70">Returnble Qty</th>
			 <th width="">Remarks</th>
		 </thead>
	 </table>
	 <div style="width:1795px; overflow-y:scroll; max-height:330px; margin-left: -20px;" id="scroll_body">
		 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1770" class="rpt_table" id="tbl_list_search">';
				
			$i=1;$k=0;$z=0;$m=0;$s=0;$req_row='';$tt=1;
			$total_program_qty=0;$total_demand_qty=0;$total_demand_issue=0;$total_demand_blanace=0;
			$balance_qty = array();
			$requsition_id_array = array();
			$prog_requsition_id_array = array();
			$yarn_requsition_id_array = array();
			$yarn_remark_id_array = array();
			
			foreach($sql_yarn_result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$prog_no  = $row[csf('knit_id')];
				$req_no   = $row[csf('requisition_no')];
				$req_date = $row[csf('requisition_date')];
				$prod_id  = $row[csf('prod_id')];
				//echo count($req_row);
				$knit_source = "";
				$knit_source = $plan_details_arr[$prog_no]['source'];
				$po_ids      = array_unique(explode(",", chop($plan_details_arr[$prog_no]['po_id'] ,",") ));
				$knit_company="";
				if($knit_source==1) $knit_company=$location_library[$plan_details_arr[$prog_no]['location']];
				else if($knit_source==3) $knit_company=$supplierArr[$plan_details_arr[$prog_no]['knitting_party']];
				
				$file_no='';	$ref_no='';
				foreach($po_ids as $row_id)
				{
					if($file_no=='') $file_no=$po_array[$row_id]['file_no']; else  $file_no.=",".$po_array[$row_id]['file_no'];
					if($ref_no=='') $ref_no=$po_array[$row_id]['ref_no']; else  $ref_no.=",".$po_array[$row_id]['ref_no'];
				}

				$ref_no   = implode(",",array_unique(explode(",", chop($ref_no, ","))) );
				$file_no  = implode(",",array_unique(explode(",", $file_no)));
				$prog_qty = $plan_details_arr[$prog_no]['program_qnty'];
					
					//for buyer
				if ($plan_details_arr[$prog_no]['within_group'] == 1)
				{
					$bkn = $plan_details_arr[$prog_no]['booking_no'];
					$buyer = $bkn_buyer[$bkn]['buyer_id'];
				}
				else
				{
					$buyer    = $plan_details_arr[$prog_no]['buyer'];
				}
				
				$issue_qty = $yarn_issue_details_arr[$req_no][$prod_id]['qty'];					
				$issue_returnble_qty = $yarn_issue_details_arr[$req_no][$prod_id]['ret_qty'];
				$issue_remark = $yarn_issue_remark_arr[$req_no][$row[csf('yarn_count_id')]][$row[csf('yarn_type')]][$row[csf('yarn_color')]][$row[csf('lot')]]['remark'];
				$demand_hidden_id = '';

				if($txt_demand_no!=''){
				
					$demand_hidden_id = $row[csf('demand_system_no')];

					$issue_qty = $yarn_issue_details_arr1[$req_no][$prod_id][$row[csf('demand_system_no')]]['qty1'];
				}



				$html .= '<tr onClick="" id="tr_'.$i.'">';
                        if ($txt_internal == "")
                        {
	                      	if (!in_array($req_no,$requsition_id_array) )
							{
								$k++;
								$html .= '<td width="30" align="center">'.$k.'</td>';
								$html .= '<td width="100" align="center"><p>'. $buyer_name_arr[$buyer].'</p></td>';
								$html .= '<td width="80" align="center"><p>'. $file_no.'</p></td>';
								$html .= '<td width="80" align="center"><div style="width:80px; word-wrap:break-word;">'.$ref_no.'</div></td>';
								$html .= '<td width="80" align="center"><p>'.$prog_no.'</p></td>';
								$html .= '<td width="100" align="center"><p>'.$knit_company.'</p></td>';
								$html .= '<td width="80" align="center"><p>'.change_date_format($row[csf('requisition_date')]).'</p></td>';
								$html .= '<td width="80" align="center"><p>'.$req_no.'</p></td>'; 
								$requsition_id_array[] = $req_no;
							}
							else
							{
								$html .= '<td width="30" align="center"></td>';
								$html .= '<td width="100" align="center"><p></p></td>';
								$html .= '<td width="80" align="center"><p></p></td>';
								$html .= '<td width="80" align="center"><p></p></td>';
								$html .= '<td width="80" align="center"><p></p></td>';
								$html .= '<td width="100" align="center"><p></p></td>';
								$html .= '<td width="80"><p></p></td>
	                            			<td width="80"><p></p></td>';
							}
							$html .= '<td width="80" ><p>'.$yarn_count_arr[$row[csf('yarn_count_id')]].'</p></td>
	                        <td width="120"><div style="width:120px; word-wrap:break-word;">'.$composition[$row[csf('yarn_comp_type1st')]].'</div></td>
	                        <td width="80" align="center"><p>'.$yarn_type[$row[csf('yarn_type')]].'</p></td>
	                        <td width="100" align="center"><p>'.$brand_name_arr[$row[csf('brand')]].'</p></td>
	                        <td width="80" align="center"><p>'.$color_library[$row[csf('yarn_color')]].'</p></td>
	                        <td width="60" align="center"><p>'.$row[csf('lot')].'</p></td>';
						
	                        if (!in_array($req_no,$prog_requsition_id_array) )
							{
								$z++;
								$balance_qty[$prog_no]=$prog_qty;
								$total_program_qty+=$prog_qty;
								$html .= '<td width="70" align="right"><p>'.number_format($prog_qty,2).'</p></td>';
						  	    $prog_requsition_id_array[]=$req_no;
							}
							else
							{ 
								$html .= '<td width="70" align="right"><p> </p></td>';	
							}
							$html .= '<td width="70" align="right"><p>';
						
						  		$yarn_requsition_qnty = $row[csf('yarn_qnty')];
						  		$html .= number_format($yarn_requsition_qnty,2); 
								$html .=  '</p></td>';
								$html .= '<td width="70" align="right" >'.number_format($row[csf("demand_qnty")],2).'
	                        	<input type="hidden" name="txt_hidden_demand_no_'.$row[csf("requisition_no")].'" id="txt_hidden_demand_no_'.$row[csf("requisition_no")].'" class="text_boxes" value="'.$demand_hidden_id.'"></td>';
	
	                        if (!in_array($req_no,$yarn_requsition_id_array) )
							{
								$m++;		
								$html .= '<td width="70" align="right"><p>'.number_format($issue_qty,2).'
	                            </p></td>
	                            <td width="70" align="right"><p>';				
						 		
	                          	    if($txt_demand_no!=''){
	                          	    	$balance_quantity = ($row[csf("demand_qnty")]-$issue_qty);
	                          	    }
	                          	    else{
	                          	    	$balance_quantity = ($yarn_requsition_qnty-$issue_qty);
	                          	    }
	                          	    $html .= number_format($balance_quantity,2); 
									$html .= '</p></td>
									<td width="70" align="right"><p>'.number_format($issue_returnble_qty,2).'</p></td>
									<td width=""><div style=" word-break:break-all">'.$issue_remark.'</div></td>';
									
							}
							else
							{ 
								$html .= '<td width="70" align="right"><p></p></td>
	                            <td width="70" align="right"><p></p></td>
	                            <td width=""><div style=" word-break:break-all"></div></td>';
							    
							}
						}
						else if ($txt_internal != "" && $txt_internal == $ref_no)
						{
	                      	if (!in_array($req_no,$requsition_id_array) )
							{
 								$k++;
								$html .= '<td width="30" align="center">'.$k.'</td>
	                            <td width="100" align="center"><p>'.$buyer_name_arr[$buyer].'</p></td>
	                            <td width="80" align="center"><p>'.$file_no.'</p></td>
	                            <td width="80" align="center"><div style="width:80px; word-wrap:break-word;">'.$ref_no.'</div></td>
	                            <td width="80" align="center"><p>'.$prog_no.'</p></td>
	                            <td width="100" align="center"><p>'.$knit_company.'</p></td>
	                            <td width="80" align="center"><p>'.change_date_format($row[csf('requisition_date')]).'</p></td>
	                            <td width="80" align="center"><p>'.$req_no.'</p></td>';
								$requsition_id_array[] = $req_no;
							}
							else
							{
								$html .= '<td width="30" align="center"></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80"><p></p></td>
	                            <td width="80"><p></p></td>';
							}
							$html .= '<td width="80" ><p>'.$yarn_count_arr[$row[csf('yarn_count_id')]].'</p></td>
	                        <td width="120"><div style="width:120px; word-wrap:break-word;">'.$composition[$row[csf('yarn_comp_type1st')]].'</div></td>
	                        <td width="80" align="center"><p>'.$yarn_type[$row[csf('yarn_type')]].'</p></td>
	                        <td width="100" align="center"><p>'.$brand_name_arr[$row[csf('brand')]].'</p></td>
	                        <td width="80" align="center"><p>'.$color_library[$row[csf('yarn_color')]].'</p></td>
	                        <td width="60" align="center"><p>'.$row[csf('lot')].'</p></td>';
	                        if (!in_array($req_no,$prog_requsition_id_array) )
							{
								$z++;
								$balance_qty[$prog_no]=$prog_qty;
								$total_program_qty+=$prog_qty;
								$html .= '<td width="70" align="right"><p>'.number_format($prog_qty,2).'</p></td>';
						  	    
						  	    $prog_requsition_id_array[]=$req_no;
							}
							else
							{ 
								$html .= '<td width="70" align="right"><p></p></td>';
									
							}
							$html .= '<td width="70" align="right"><p>';
							
						  		$yarn_requsition_qnty = $row[csf('yarn_qnty')];
						  		$html .= number_format($yarn_requsition_qnty,2); 
								$html .= '</p></td>';
								$html .= '<td width="70" align="right" ><a href="##" onClick="" >'.number_format($row[csf("demand_qnty")],2).'</a></td>';
								
	                        if (!in_array($req_no,$yarn_requsition_id_array) )
							{
								$m++;	
								$html .= '<td width="70" align="right"><p><a href="##" onClick="">
								'.number_format($issue_qty,2).'</a>
								</p></td>
								<td width="70" align="right"><p>';						
						 		
	                          	    $balance_quantity = ($yarn_requsition_qnty-$issue_qty);
								$html .= number_format($balance_quantity,2);
								$html .= '</p></td>
	                            <td width="70" align="right"><p>'.number_format($issue_returnble_qty,2).'</p></td>
	                            <td width=""><div style=" word-break:break-all">'.$issue_remark.'</div></td>';
							}
							else
							{ 
								$html .= '<td width="70" align="right"><p></p></td>
	                            <td width="70" align="right"><p></p></td>
	                            <td width=""><div style=" word-break:break-all"></div></td>';
							    
							}
						}	
						$html .= '</tr>';
											
					$i++;
					$total_requsition_qnty += $row[csf('yarn_qnty')];
					$total_demand_qty += $row[csf("demand_qnty")];
					$total_demand_blanace += $prog_qty-$row[csf("demand_qnty")];
					$total_demand_issue += $issue_qty;
					$total_balance_quantity += ($row[csf('yarn_qnty')]-$issue_qty);
					$total_issue_returnble_qty += $issue_returnble_qty;
				}
				$html .= '</table>
				<table width="1770"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<tfoot>
						<tr>
							   <th width="30">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>                            
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="60">Total</th>
							<th width="70" id="value_tot_prog">'.$total_program_qty.'</th>
							<th width="70" id="value_tot_requsition">'.$total_requsition_qnty.'</th> 
							<th width="70" id="value_tot_demand">'.$total_demand_qty.'</th>
							<th width="70" id="value_tot_issue">'.$total_demand_issue.'</th>
							<th width="70" id="value_tot_balance3">'.number_format($total_balance_quantity,2).'</th>
							<th width="70" id ="value_to_issue_return">'.number_format($total_issue_returnble_qty,2).' </th>
							<th></th>
						</tr>
					</tfoot>
				</table>';

	foreach (glob("swgfsr_*.xls") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename="swgfsr_".$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$filename";
	exit();
			
	
}
if($action=="report_generate_13022022")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

 	$company_name=str_replace("'","",$cbo_company_name);

 	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$brand_name_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$supplierArr=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name");

	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	if($cbo_buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and c.buyer_id=$cbo_buyer_name"; 	
	}

	$cbo_year=str_replace("'","",$cbo_year);
	$year_cond="";
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}

	$txt_prog_no=trim(str_replace("'","",$txt_prog_no));
	$txt_req_no=trim(str_replace("'","",$txt_req_no));
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_internal=trim(str_replace("'","",$txt_ref_no));
	$txt_sales_order_no=trim(str_replace("'","",$txt_sales_order_no));
	$txt_fabric_booking_no=trim(str_replace("'","",$txt_fabric_booking_no));

	if($txt_file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='$txt_file_no' ";
	if($txt_internal=="") $internal_cond=""; else $internal_cond=" and b.grouping='$txt_internal' ";
	if($txt_prog_no=="") $prog_cond=""; else $prog_cond=" and b.knit_id in($txt_prog_no) ";
	if($txt_req_no=="") $req_cond=""; else $req_cond=" and b.requisition_no in($txt_req_no)";
	if($txt_fabric_booking_no=="") $booking_cond=""; else $booking_cond=" and e.booking_no like('%$txt_fabric_booking_no')";

	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	
	$date_cond="";
	if(str_replace("'","",$txt_date_from)!="" || str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		$date_cond=" and a.demand_date between '$start_date' and '$end_date'";
	}

	$requsition_date_cond="";
	if(str_replace("'","",$txt_requisition_date_from	)!="" || str_replace("'","",$txt_requisition_date_to)!="")
	{
		if($db_type==0)
		{
			$requisition_start_date=change_date_format(str_replace("'","",$txt_requisition_date_from),"yyyy-mm-dd","");
			$requsition_end_date=change_date_format(str_replace("'","",$txt_requisition_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$requisition_start_date=change_date_format(str_replace("'","",$txt_requisition_date_from),"","",1);
			$requisition_end_date=change_date_format(str_replace("'","",$txt_requisition_date_to),"","",1);				
		}
		$requsition_date_cond = " and b.requisition_date BETWEEN  TO_DATE('$requisition_start_date','dd/mon/yyyy') and  TO_DATE('$requisition_end_date','dd/mon/yyyy')"; 
	}

	if($txt_sales_order_no!="")
	{
		$sales_result=sql_select("select id as po_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and job_no LIKE '%$txt_sales_order_no%'");
		$sales_po_id="";
		foreach($sales_result as $row)
		{
			if($sales_po_id=='') $sales_po_id=$row[csf('po_id')]; else $sales_po_id.=",".$row[csf('po_id')];
		}		
	}

	$po_array=array();

	if ($txt_file_no != '' || $txt_internal != '')
	{
		$po_sql="SELECT a.job_no,$year_field, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $year_cond  $internal_cond $file_no_cond";
		$po_sql_data=sql_select($po_sql);
		$all_po_id = '';
		foreach($po_sql_data as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
			$po_array[$row[csf('id')]]['year']=$row[csf('year')];
			$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];		
			$po_array[$row[csf('id')]]['ref_no']=$row[csf('grouping')];
			if($all_po_id=='') $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
		}	
	}

	if($date_cond!="")
	{
		$sql_demand="select b.requisition_no, a.id as demand_id
		from ppl_yarn_demand_entry_dtls b, ppl_yarn_demand_entry_mst a
		where  b.mst_id=a.id $date_cond and a.company_id=$company_name and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
		$demand_data = sql_select($sql_demand);
		foreach($demand_data as $row)
		{
			$demand_requ_id.=$row[csf('requisition_no')].",";	
		}
	}
	
	$req_id_cond="";
	if($demand_requ_id!="") 
	{
		$demand_requ_id=substr($demand_requ_id,0,-1);
		$demand_requ_id=implode(",",array_filter(array_unique(explode(",",$demand_requ_id))));
		if($db_type==0) $req_id_cond="and b.requisition_no in(".$demand_requ_id.")";
		else
		{
			$req_ids=explode(",",$demand_requ_id);
			if(count($req_ids)>990)
			{
				$req_id_cond="and (";
				$req_ids=array_chunk($req_ids,990);
				$z=0;
				foreach($req_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $req_id_cond.=" b.requisition_no in(".$id.")";
					else $req_id_cond.=" or b.requisition_no in(".$id.")";
					$z++;
				}
				$req_id_cond.=")";
			}
			else $req_id_cond="and b.requisition_no in(".$demand_requ_id.")";
		}
	}

	$program_po_cond = ($all_po_id!="")?" and c.po_id in(".$all_po_id.")":"";
	$sales_program_po_cond = ($sales_po_id!="")?" and c.po_id in(".$sales_po_id.")":"";
	//$pro_sql="SELECT b.requisition_no, d.knitting_source, d.knitting_party, d.location_id,d.id as prog_no,d.program_qnty,c.buyer_id,c.po_id from ppl_yarn_requisition_entry b, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls c where b.knit_id=d.id and d.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $buyer_id_cond $req_cond $prog_cond $prog_id_cond $program_po_cond $sales_program_po_cond $req_id_cond group by d.id, b.requisition_no, d.knitting_source, d.knitting_party, d.location_id, c.buyer_id,c.po_id,d.program_qnty";

	$pro_sql="SELECT e.booking_no,b.requisition_no, d.knitting_source, d.knitting_party, d.location_id,d.id as prog_no,d.program_qnty,c.buyer_id,c.po_id from ppl_yarn_requisition_entry b, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls c,ppl_planning_info_entry_mst e where e.id=d.mst_id and b.knit_id=d.id and d.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $booking_cond $buyer_id_cond $req_cond $prog_cond $prog_id_cond $program_po_cond $sales_program_po_cond $req_id_cond group by e.booking_no,d.id, b.requisition_no, d.knitting_source, d.knitting_party, d.location_id, c.buyer_id,c.po_id,d.program_qnty";

	//echo $pro_sql;die;
	$pro_sql_data = sql_select($pro_sql);

	$po_id="";
	$plan_details_arr = array();
	foreach($pro_sql_data as $row)
	{
		$plan_details_arr[$row[csf('prog_no')]]['program_qnty']+=$row[csf('program_qnty')];
		$plan_details_arr[$row[csf('prog_no')]]['buyer']=$row[csf('buyer_id')];
		$plan_details_arr[$row[csf('prog_no')]]['buyer']=$row[csf('buyer_id')];
		$plan_details_arr[$row[csf('prog_no')]]['source']=$row[csf('knitting_source')];
		$plan_details_arr[$row[csf('prog_no')]]['knitting_party']=$row[csf('knitting_party')];
		$plan_details_arr[$row[csf('prog_no')]]['location']=$row[csf('location_id')];
		$plan_details_arr[$row[csf('prog_no')]]['po_id'].=$row[csf('po_id')].",";

		if($po_id=='') $po_id=$row[csf('po_id')]; else $po_id.=",".$row[csf('po_id')];
		//if($requisition_nos=='') $requisition_nos=$row[csf('requisition_no')]; else $requisition_nos.=",".$row[csf('requisition_no')];
		$requ_id.=$row[csf('requisition_no')].",";
	}
	unset($pro_sql_data);

	$req_id_cond="";
	if($requ_id!="") 
	{
		$requ_id=substr($requ_id,0,-1);
		$requ_id=implode(",",array_filter(array_unique(explode(",",$requ_id))));
		if($db_type==0) $req_id_cond="and b.requisition_no in(".$requ_id.")";
		else
		{
			$req_ids=explode(",",$requ_id);
			if(count($req_ids)>990)
			{
				$req_id_cond="and (";
				$req_ids=array_chunk($req_ids,990);
				$z=0;
				foreach($req_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $req_id_cond.=" b.requisition_no in(".$id.")";
					else $req_id_cond.=" or b.requisition_no in(".$id.")";
					$z++;
				}
				$req_id_cond.=")";
			}
			else $req_id_cond="and b.requisition_no in(".$requ_id.")";
		}
	}
	
	if ($txt_file_no == '' && $txt_internal == '')
	{
		$po_sql="SELECT a.job_no,$year_field, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($po_id) $year_cond";
		$po_sql_data=sql_select($po_sql);
		$all_po_id = '';
		foreach($po_sql_data as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
			$po_array[$row[csf('id')]]['year']=$row[csf('year')];
			$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];		
			$po_array[$row[csf('id')]]['ref_no']=$row[csf('grouping')];
		}	
	}

	//$requisition_nos_cond
	//$requisition_nos_cond = ($requisition_nos!="")?" and b.requisition_no in(".$requisition_nos.")":"";
	/*
	$sqlyarn = "SELECT b.knit_id,b.requisition_date, b.requisition_no,sum(b.yarn_qnty) as yarn_qnty,d.yarn_count_id, d.yarn_type, d.color as yarn_color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id,a.demand_prefix_number,a.demand_date,sum(c.yarn_demand_qnty) as demand_qnty
		from product_details_master d,ppl_yarn_requisition_entry b 
		left join ppl_yarn_demand_reqsn_dtls c on b.id=c.requisition_id and c.status_active=1 and c.is_deleted=0
		left join ppl_yarn_demand_entry_mst a on a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 where d.id=b.prod_id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		$requsition_date_cond $prog_cond $date_cond $req_cond $req_id_cond
		group by b.knit_id,b.requisition_date, b.requisition_no,d.yarn_count_id, d.yarn_type, d.color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id,a.demand_prefix_number,a.demand_date order by b.requisition_no";*/

	$sqlyarn = "SELECT b.knit_id,b.requisition_date, b.requisition_no,b.yarn_qnty,d.yarn_count_id, d.yarn_type, d.color as yarn_color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id ,
		RTRIM(XMLAGG(XMLELEMENT(e,a.demand_prefix_number,',').EXTRACT('//text()') ORDER BY a.demand_prefix_number).GETCLOBVAL(),',') AS demand_prefix_number,sum(c.yarn_demand_qnty) as demand_qnty from product_details_master d,ppl_yarn_requisition_entry b 
		left join ppl_yarn_demand_reqsn_dtls c on b.id=c.requisition_id and c.status_active=1 and c.is_deleted=0
		left join ppl_yarn_demand_entry_mst a on a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 
		where d.id=b.prod_id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 
		and d.is_deleted=0 $requsition_date_cond $prog_cond $date_cond $req_cond $req_id_cond group by b.knit_id,b.requisition_date, b.requisition_no,b.yarn_qnty,d.yarn_count_id, d.yarn_type, d.color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id order by b.requisition_no";
	// echo $sqlyarn; die();
	$sql_yarn_result=sql_select($sqlyarn);

	$requ_id='';
	$prod_id="";
	$prog_id="";
	$yarn_color_id="";
	foreach($sql_yarn_result as $row)
	{
		$requ_id.=$row[csf('requisition_no')].",";
		$prod_id.=$row[csf('prod_id')].",";
		$prog_id.=$row[csf('knit_id')].",";
		$yarn_color_id.=$row[csf('yarn_color')].",";

	}

	$yarn_color_id = chop($yarn_color_id," , ");
	if($yarn_color_id!="")
	{
		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 and id in($yarn_color_id)", "id", "color_name" );
	}
	
	// requsition cond 
	$req_id_cond="";
	if($requ_id!="") 
	{
		$requ_id=substr($requ_id,0,-1);
		$requ_id=implode(",",array_filter(array_unique(explode(",",$requ_id))));
		if($db_type==0) $req_id_cond="and b.requisition_no in(".$requ_id.")";
		else
		{
			$req_ids=explode(",",$requ_id);
			if(count($req_ids)>990)
			{
				$req_id_cond="and (";
				$req_ids=array_chunk($req_ids,990);
				$z=0;
				foreach($req_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $req_id_cond.=" b.requisition_no in(".$id.")";
					else $req_id_cond.=" or b.requisition_no in(".$id.")";
					$z++;
				}
				$req_id_cond.=")";
			}
			else $req_id_cond="and b.requisition_no in(".$requ_id.")";
		}
	}

	// product cond 
	$prod_id_cond="";
	$demand_prod_id_cond="";
	if($prod_id!="") 
	{
		$prod_id=substr($prod_id,0,-1);
		$prod_id=implode(",",array_filter(array_unique(explode(",",$prod_id))));

		if($db_type==0) {
			$prod_id_cond="and c.id in(".$prod_id.")";
			$demand_prod_id_cond="and c.prod_id in(".$prod_id.")";
		}
		else
		{
			$prod_ids=explode(",",$prod_id);
			if(count($prod_ids)>990)
			{
				$prod_id_cond="and (";
				$demand_prod_id_cond="and (";
				$prod_ids=array_chunk($prod_ids,990);
				$z=0;
				foreach($prod_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) {
						$prod_id_cond.=" c.id in(".$id.")";
						$demand_prod_id_cond.=" c.prod_id in(".$id.")";
					}
					else {
						$prod_id_cond.=" or c.id in(".$id.")";
						$demand_prod_id_cond.=" or c.prod_id in(".$id.")";
					}
					$z++;
				}
				$prod_id_cond.=")";
				$demand_prod_id_cond.=")";
			}
			else {
				$prod_id_cond="and c.id in(".$prod_id.")";
				$demand_prod_id_cond="and c.prod_id in(".$prod_id.")";
			}
		}
	}
	//echo $prod_id_cond;

	// prog cond 
	$prog_id_cond="";
	if($prog_id!="") 
	{
		$prog_id=substr($prog_id,0,-1);
		$prog_id=implode(",",array_filter(array_unique(explode(",",$prog_id))));

		if($db_type==0) {
			$prog_id_cond="and b.knit_id in(".$prog_id.")";
		}
		else
		{
			$prog_ids=explode(",",$prog_id);
			if(count($prog_ids)>990)
			{
				$prog_id_cond="and (";

				$prog_ids=array_chunk($prog_ids,990);
				$z=0;
				foreach($prog_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) {
						$prog_id_cond.=" b.knit_id in(".$id.")";
					}
					else {
						$prog_id_cond.=" or b.knit_id in(".$id.")";
					}
					$z++;
				}
				$prog_id_cond.=")";
			}
			else {
				$prog_id_cond="and b.knit_id in(".$prog_id.")";
			}
		}
	}

	$po_id_cond="";
	if($po_id!="") 
	{
		$po_id=substr($po_id,0,-1);
		if($db_type==0) $po_id_cond="and b.id in(".$po_id.")";
		else
		{
			$po_ids=explode(",",$po_id);
			if(count($po_ids)>1000)
			{
				$po_id_cond="and (";
				$po_ids=array_chunk($po_ids,1000);
				$z=0;
				foreach($po_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $po_id_cond.=" b.id in(".$id.")";
					else $po_id_cond.=" or b.id in(".$id.")";
					$z++;
				}
				$po_id_cond.=")";
			}
			else $po_id_cond="and b.id in(".$po_id.")";
		}
	}

	if($prod_id_cond != "")
	{
		$product_sql= "select c.id, c.product_name_details, c.lot, c.supplier_id from product_details_master c where c.company_id=$company_name and c.item_category_id=1 $prod_id_cond";
		$product_data = sql_select($product_sql);
		$product_details_arr=array();
		foreach($product_data as $row)
		{
			$product_details_arr[$row[csf('id')]]['desc']=$row[csf('product_name_details')];
			$product_details_arr[$row[csf('id')]]['lot']=$row[csf('lot')]; 
			$product_details_arr[$row[csf('id')]]['supplier']=$row[csf('supplier_id')]; 
		}
		unset($product_data);
	}
		
	$yarn_sql="SELECT  a.remarks,b.requisition_no as requ_no, b.store_id, b.brand_id, b.cons_quantity as issue_qnty, b.return_qnty,c.yarn_type, c.yarn_count_id, c.lot, c.color,b.prod_id from inv_issue_master a, inv_transaction b, product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$company_name and a.issue_basis in (3,8) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $req_id_cond $prod_id_cond";
	//echo $yarn_sql; die;
	$yarn_sql_data = sql_select($yarn_sql);
	$yarn_issue_details_arr=array();
	$yarn_issue_remark_arr=array();
	foreach($yarn_sql_data as $row)
	{
		$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['qty']+=$row[csf('issue_qnty')];
		$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['ret_qty']+=$row[csf('return_qnty')];
		$yarn_issue_remark_arr[$row[csf('requ_no')]][$row[csf('yarn_count_id')]][$row[csf('yarn_type')]][$row[csf('color')]][$row[csf('lot')]]['remark']=$row[csf('remarks')];
	}
	unset($yarn_sql_data);
	ob_start();
	?>
	<fieldset style="width:1795px;">
		<table cellpadding="0" cellspacing="0" width="1770">
			<tr>
			   <td align="center" width="100%" style="font-size:16px"><strong>Requisition Against Demand Status</strong><br><b>
                   <? if($start_date!='') echo change_date_format($start_date).' To '.change_date_format($end_date);else echo '';?></b>
               </td>
			</tr>
		</table>	
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1790" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">Buyer</th>
                <th width="80">File No</th>
                <th width="80">Ref. No</th>
				<th width="80">Prog. No</th>
                <th width="100">Knitting Company</th>
                <th width="80">Req. Date</th>
				<th width="80">Req. No</th>
				
				<th width="80">Y. Count</th>
				<th width="120">Y Composition</th>
				<th width="80">Y. Type</th>
				<th width="100">Brand</th>
				<th width="80">Y. Color</th>
                <th width="60">Lot</th>
				<th width="70">Program Qty.</th>
				<th width="70">Requisition Qty.</th>
                <th width="70">Demand Qty.</th>
				
				<th width="70">Issue Qty</th>
				<th width="70">Balance Qty</th>
				<th width="70">Returnble Qty</th>
				<th width="">Remarks</th>
			</thead>
		</table>
		<div style="width:1795px; overflow-y:scroll; max-height:330px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1770" class="rpt_table" id="tbl_list_search">
                <?				
				$i=1;$k=0;$z=0;$m=0;$s=0;$req_row='';$tt=1;
				$total_program_qty=0;$total_demand_qty=0;$total_demand_issue=0;$total_demand_blanace=0;
				$balance_qty = array();
				$requsition_id_array = array();
				$prog_requsition_id_array = array();
				$yarn_requsition_id_array = array();
				$yarn_remark_id_array = array();
				
                foreach($sql_yarn_result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$prog_no  = $row[csf('knit_id')];
					$req_no   = $row[csf('requisition_no')];
					$req_date = $row[csf('requisition_date')];
					$prod_id  = $row[csf('prod_id')];
					//echo count($req_row);
					$knit_source = "";
					$knit_source = $plan_details_arr[$prog_no]['source'];
					$po_ids      = array_unique(explode(",", chop($plan_details_arr[$prog_no]['po_id'] ,",") ));
					$knit_company="";
					if($knit_source==1) $knit_company=$location_library[$plan_details_arr[$prog_no]['location']];
					else if($knit_source==3) $knit_company=$supplierArr[$plan_details_arr[$prog_no]['knitting_party']];
					
					$file_no='';	$ref_no='';
					foreach($po_ids as $row_id)
					{
						if($file_no=='') $file_no=$po_array[$row_id]['file_no']; else  $file_no.=",".$po_array[$row_id]['file_no'];
						if($ref_no=='') $ref_no=$po_array[$row_id]['ref_no']; else  $ref_no.=",".$po_array[$row_id]['ref_no'];
					}

					$ref_no   = implode(",",array_unique(explode(",", chop($ref_no, ","))) );
					$file_no  = implode(",",array_unique(explode(",", $file_no)));
					$prog_qty = $plan_details_arr[$prog_no]['program_qnty'];
					$buyer    = $plan_details_arr[$prog_no]['buyer'];
					
					$issue_qty = $yarn_issue_details_arr[$req_no][$prod_id]['qty'];					
					$issue_returnble_qty = $yarn_issue_details_arr[$req_no][$prod_id]['ret_qty'];
					$issue_remark = $yarn_issue_remark_arr[$req_no][$row[csf('yarn_count_id')]][$row[csf('yarn_type')]][$row[csf('yarn_color')]][$row[csf('lot')]]['remark'];
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <?
                        if ($txt_internal == "")
                        {
	                      	if (!in_array($req_no,$requsition_id_array) )
							{
								$k++;
								?>
	                            <td width="30" align="center"><? echo $k; ?></td>
	                            <td width="100" align="center"><p><? echo $buyer_name_arr[$buyer]; ?></p></td>
	                            <td width="80" align="center"><p><? echo $file_no; ?></p></td>
	                            <td width="80" align="center"><div style="width:80px; word-wrap:break-word;"><? echo $ref_no; ?></div></td>
	                            <td width="80" align="center"><p><? echo $prog_no; ?></p></td>
	                            <td width="100" align="center"><p><? echo $knit_company; ?></p></td>
	                            <td width="80" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
	                            <td width="80" align="center"><p><? echo $req_no; ?></p></td>
	                            <?
								$requsition_id_array[] = $req_no;
							}
							else
							{
								?>
								<td width="30" align="center"></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80"><p></p></td>
	                            <td width="80"><p></p></td>
							    <?
							}
							?>
	                      
	                        <td width="80" ><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
	                        <td width="120"><div style="width:120px; word-wrap:break-word;"><? echo $composition[$row[csf('yarn_comp_type1st')]]; ?></div></td>
	                        <td width="80" align="center"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td width="100" align="center"><p><? echo $brand_name_arr[$row[csf('brand')]]; ?></p></td>
	                        <td width="80" align="center"><p><? echo $color_library[$row[csf('yarn_color')]]; ?></p></td>
	                        <td width="60" align="center"><p><? echo $row[csf('lot')];?></p></td>
	                        <?
	                        if (!in_array($req_no,$prog_requsition_id_array) )
							{
								$z++;
								$balance_qty[$prog_no]=$prog_qty;
								$total_program_qty+=$prog_qty;
						  	    ?>
	                      	    <td width="70" align="right"><p><? echo number_format($prog_qty,2); ?></p></td>
	                      	    <?
						  	    $prog_requsition_id_array[]=$req_no;
							}
							else
							{ 
								?>
							 	<td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
							    <?	
							}
							?>
						    <td width="70" align="right"><p>
						  		<? 
						  		$yarn_requsition_qnty = $row[csf('yarn_qnty')];
						  		echo number_format($yarn_requsition_qnty,2); 
						  		?>
						    </p></td>

	                        <td width="70" align="right" ><a href="##" onClick="openmy_popup(<? echo $row[csf("requisition_no")];?>,<? echo $prod_id;?>,'demand_popup','<? echo $start_date;?>','<? echo $end_date;?>')" ><? echo number_format($row[csf("demand_qnty")],2); ?></a></td>
	                       
	                        <?
	                        if (!in_array($req_no,$yarn_requsition_id_array) )
							{
								$m++;							
						 		?>
	                            <td width="70" align="right"><p><a href="##" onClick="openmy_popup(<? echo $req_no;?>,<? echo $prod_id;?>,'issue_popup','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $company_name;?>')">
	                           	    <? echo number_format($issue_qty,2); ?></a>
	                            </p></td>
	                            <td width="70" align="right"><p>
	                          	    <? 
	                          	    $balance_quantity = ($yarn_requsition_qnty-$issue_qty);
	                          	    echo number_format($balance_quantity,2); ?>                          	
	                            </p></td>
	                            <td width="70" align="right"><p><? echo number_format($issue_returnble_qty,2); ?></p></td>
	                            <td width=""><div style=" word-break:break-all"><? echo $issue_remark; ?></div></td>
	                      	    <?
							}
							else
							{ 
							    ?>
							 	<td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
	                            <td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
	                            <td width=""><div style=" word-break:break-all"><? //echo $issue_remark; ?></div></td>
							    <?	
							}
						}
						else if ($txt_internal != "" && $txt_internal == $ref_no)
						{
	                      	if (!in_array($req_no,$requsition_id_array) )
							{
								$k++;
								?>
	                            <td width="30" align="center"><? echo $k; ?></td>
	                            <td width="100" align="center"><p><? echo $buyer_name_arr[$buyer]; ?></p></td>
	                            <td width="80" align="center"><p><? echo $file_no; ?></p></td>
	                            <td width="80" align="center"><div style="width:80px; word-wrap:break-word;"><? echo $ref_no; ?></div></td>
	                            <td width="80" align="center"><p><? echo $prog_no; ?></p></td>
	                            <td width="100" align="center"><p><? echo $knit_company; ?></p></td>
	                            <td width="80" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
	                            <td width="80" align="center"><p><? echo $req_no; ?></p></td>
	                            <?
								$requsition_id_array[] = $req_no;
							}
							else
							{
								?>
								<td width="30" align="center"></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80"><p></p></td>
	                            <td width="80"><p></p></td>
							    <?
							}
							?>
	                      
	                        <td width="80" ><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
	                        <td width="120"><div style="width:120px; word-wrap:break-word;"><? echo $composition[$row[csf('yarn_comp_type1st')]]; ?></div></td>
	                        <td width="80" align="center"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td width="100" align="center"><p><? echo $brand_name_arr[$row[csf('brand')]]; ?></p></td>
	                        <td width="80" align="center"><p><? echo $color_library[$row[csf('yarn_color')]]; ?></p></td>
	                        <td width="60" align="center"><p><? echo $row[csf('lot')];?></p></td>
	                        <?
	                        if (!in_array($req_no,$prog_requsition_id_array) )
							{
								$z++;
								$balance_qty[$prog_no]=$prog_qty;
								$total_program_qty+=$prog_qty;
						  	    ?>
	                      	    <td width="70" align="right"><p><? echo number_format($prog_qty,2); ?></p></td>
	                      	    <?
						  	    $prog_requsition_id_array[]=$req_no;
							}
							else
							{ 
								?>
							 	<td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
							    <?	
							}
							?>
						    <td width="70" align="right"><p>
						  		<? 
						  		$yarn_requsition_qnty = $row[csf('yarn_qnty')];
						  		echo number_format($yarn_requsition_qnty,2); 
						  		?>
						    </p></td>

	                        <td width="70" align="right" ><a href="##" onClick="openmy_popup(<? echo $row[csf("requisition_no")];?>,<? echo $prod_id;?>,'demand_popup','<? echo $start_date;?>','<? echo $end_date;?>')" ><? echo number_format($row[csf("demand_qnty")],2); ?></a></td>
	                       
	                        <?
	                        if (!in_array($req_no,$yarn_requsition_id_array) )
							{
								$m++;							
						 		?>
	                            <td width="70" align="right"><p><a href="##" onClick="openmy_popup(<? echo $req_no;?>,<? echo $prod_id;?>,'issue_popup','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $company_name;?>')">
	                           	    <? echo number_format($issue_qty,2); ?></a>
	                            </p></td>
	                            <td width="70" align="right"><p>
	                          	    <? 
	                          	    $balance_quantity = ($yarn_requsition_qnty-$issue_qty);
	                          	    echo number_format($balance_quantity,2); ?>                          	
	                            </p></td>
	                            <td width="70" align="right"><p><? echo number_format($issue_returnble_qty,2); ?></p></td>
	                            <td width=""><div style=" word-break:break-all"><? echo $issue_remark; ?></div></td>
	                      	    <?
							}
							else
							{ 
							    ?>
							 	<td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
	                            <td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
	                            <td width=""><div style=" word-break:break-all"><? //echo $issue_remark; ?></div></td>
							    <?	
							}
						}	
						?>
                    </tr>
                    <?							
					$i++;
					$total_requsition_qnty += $row[csf('yarn_qnty')];
					$total_demand_qty += $row[csf("demand_qnty")];
					$total_demand_blanace += $prog_qty-$row[csf("demand_qnty")];
					$total_demand_issue += $issue_qty;
					$total_balance_quantity += ($row[csf('yarn_qnty')]-$issue_qty);
					$total_issue_returnble_qty += $issue_returnble_qty;
				}
				?>
			</table>
            <table width="1770"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	            <tfoot>
	                <tr>
	               		<th width="30">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80">&nbsp;</th>                            
	                    <th width="80">&nbsp;</th>
	                    <th width="120">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
						<th width="60">Total</th>
						<th width="70" id="value_tot_prog"><?php echo $total_program_qty;?></th>
						<th width="70" id="value_tot_demand"><?php echo $total_demand_qty;?></th>
	                    <th width="70" id="value_tot_requsition"><?php echo $total_requsition_qnty;?></th>  
	                    <th width="70" id="value_tot_issue"><?php echo $total_demand_issue;?></th>
	                    <th width="70" id="value_tot_balance3"><?php echo number_format($total_balance_quantity,2);?></th>
	                    <th width="70" id ="value_to_issue_return"><? echo number_format($total_issue_returnble_qty,2); ?> </th>
	                    <th></th>
	                </tr>
				</tfoot>
            </table>
		</div>
	</fieldset>      
	<?	
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="requires/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 	
}

if($action=="report_generate_requistion")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
 	$company_name=str_replace("'","",$cbo_company_name);
 	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$brand_name_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
	$supplierArr=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name");

	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	if($cbo_buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and c.buyer_id=$cbo_buyer_name"; 	
	}

	$cbo_year=str_replace("'","",$cbo_year);
	$year_cond="";
	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}

	$txt_prog_no=trim(str_replace("'","",$txt_prog_no));
	$txt_req_no=trim(str_replace("'","",$txt_req_no));
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_internal=trim(str_replace("'","",$txt_ref_no));
	$txt_sales_order_no=trim(str_replace("'","",$txt_sales_order_no));
	$txt_fabric_booking_no=trim(str_replace("'","",$txt_fabric_booking_no));
	//echo $txt_fabric_booking_no;die;
	if($txt_file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='$txt_file_no' ";
	if($txt_internal=="") $internal_cond=""; else $internal_cond=" and b.grouping='$txt_internal' ";
	if($txt_prog_no=="") $prog_cond=""; else $prog_cond=" and b.knit_id in($txt_prog_no) ";
	if($txt_req_no=="") $req_cond=""; else $req_cond=" and b.requisition_no in($txt_req_no)";
	if($txt_fabric_booking_no=="") $booking_cond=""; else $booking_cond=" and e.booking_no like('%$txt_fabric_booking_no')";
	//echo $booking_cond;die;
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	
	$date_cond="";
	if(str_replace("'","",$txt_date_from)!="" || str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		$date_cond=" and a.demand_date between '$start_date' and '$end_date'";
	}

	$requsition_date_cond="";
	if(str_replace("'","",$txt_requisition_date_from	)!="" || str_replace("'","",$txt_requisition_date_to)!="")
	{
		if($db_type==0)
		{
			$requisition_start_date=change_date_format(str_replace("'","",$txt_requisition_date_from),"yyyy-mm-dd","");
			$requsition_end_date=change_date_format(str_replace("'","",$txt_requisition_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$requisition_start_date=change_date_format(str_replace("'","",$txt_requisition_date_from),"","",1);
			$requisition_end_date=change_date_format(str_replace("'","",$txt_requisition_date_to),"","",1);				
		}
		$requsition_date_cond = " and b.requisition_date BETWEEN  TO_DATE('$requisition_start_date','dd/mon/yyyy') and  TO_DATE('$requisition_end_date','dd/mon/yyyy')"; 
	}

	if($txt_sales_order_no!="")
	{
		$sales_result=sql_select("select id as po_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and job_no LIKE '%$txt_sales_order_no%'");
		$sales_po_id="";
		foreach($sales_result as $row)
		{
			if($sales_po_id=='')
				$sales_po_id=$row[csf('po_id')];
			else
				$sales_po_id.=",".$row[csf('po_id')];
		}		
	}

	$po_array=array();

	if ($txt_file_no != '' || $txt_internal != '')
	{
		$po_sql="SELECT a.job_no,$year_field, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $year_cond  $internal_cond $file_no_cond";
		$po_sql_data=sql_select($po_sql);
		$all_po_id = '';
		foreach($po_sql_data as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
			$po_array[$row[csf('id')]]['year']=$row[csf('year')];
			$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];		
			$po_array[$row[csf('id')]]['ref_no']=$row[csf('grouping')];
			if($all_po_id=='') $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')];
		}	
	}

	if($date_cond!="")
	{
		$sql_demand="select b.requisition_no, a.id as demand_id
		from ppl_yarn_demand_entry_dtls b, ppl_yarn_demand_entry_mst a
		where  b.mst_id=a.id $date_cond and a.company_id=$company_name and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";
		$demand_data = sql_select($sql_demand);
		foreach($demand_data as $row)
		{
			$demand_requ_id.=$row[csf('requisition_no')].",";	
		}
	}
	
	$req_id_cond="";
	if($demand_requ_id!="") 
	{
		$demand_requ_id=substr($demand_requ_id,0,-1);
		$demand_requ_id=implode(",",array_filter(array_unique(explode(",",$demand_requ_id))));
		if($db_type==0) $req_id_cond="and b.requisition_no in(".$demand_requ_id.")";
		else
		{
			$req_ids=explode(",",$demand_requ_id);
			if(count($req_ids)>990)
			{
				$req_id_cond="and (";
				$req_ids=array_chunk($req_ids,990);
				$z=0;
				foreach($req_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $req_id_cond.=" b.requisition_no in(".$id.")";
					else $req_id_cond.=" or b.requisition_no in(".$id.")";
					$z++;
				}
				$req_id_cond.=")";
			}
			else $req_id_cond="and b.requisition_no in(".$demand_requ_id.")";
		}
	}

	$program_po_cond = ($all_po_id!="")?" and c.po_id in(".$all_po_id.")":"";
	$sales_program_po_cond = ($sales_po_id!="")?" and c.po_id in(".$sales_po_id.")":"";
	//$pro_sql="SELECT b.requisition_no, d.knitting_source, d.knitting_party, d.location_id,d.id as prog_no,d.program_qnty,c.buyer_id,c.po_id from ppl_yarn_requisition_entry b, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls c where b.knit_id=d.id and d.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $buyer_id_cond $req_cond $prog_cond $prog_id_cond $program_po_cond $sales_program_po_cond $req_id_cond group by d.id, b.requisition_no, d.knitting_source, d.knitting_party, d.location_id, c.buyer_id,c.po_id,d.program_qnty";

	$pro_sql="SELECT e.booking_no,b.requisition_no, d.knitting_source, d.knitting_party, d.location_id,d.id as prog_no,d.program_qnty,c.buyer_id,c.po_id from ppl_yarn_requisition_entry b, ppl_planning_info_entry_dtls d, ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_mst e where e.id=d.mst_id and b.knit_id=d.id and d.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $booking_cond $buyer_id_cond $req_cond $prog_cond $prog_id_cond $program_po_cond $sales_program_po_cond $req_id_cond group by e.booking_no,d.id, b.requisition_no, d.knitting_source, d.knitting_party, d.location_id, c.buyer_id,c.po_id,d.program_qnty";
	 //echo $pro_sql;die;
	$pro_sql_data = sql_select($pro_sql);
	$po_id="";
	$plan_details_arr = array();
	foreach($pro_sql_data as $row)
	{
		$plan_details_arr[$row[csf('prog_no')]]['program_qnty']+=$row[csf('program_qnty')];
		$plan_details_arr[$row[csf('prog_no')]]['buyer']=$row[csf('buyer_id')];
		$plan_details_arr[$row[csf('prog_no')]]['buyer']=$row[csf('buyer_id')];
		$plan_details_arr[$row[csf('prog_no')]]['source']=$row[csf('knitting_source')];
		$plan_details_arr[$row[csf('prog_no')]]['knitting_party']=$row[csf('knitting_party')];
		$plan_details_arr[$row[csf('prog_no')]]['location']=$row[csf('location_id')];
		$plan_details_arr[$row[csf('prog_no')]]['po_id'].=$row[csf('po_id')].",";
		$plan_details_arr[$row[csf('prog_no')]]['booking_no'][$row[csf('booking_no')]]=$row[csf('booking_no')];

		if($po_id=='') $po_id=$row[csf('po_id')]; else $po_id.=",".$row[csf('po_id')];
		//if($requisition_nos=='') $requisition_nos=$row[csf('requisition_no')]; else $requisition_nos.=",".$row[csf('requisition_no')];
		$requ_id.=$row[csf('requisition_no')].",";
	}
	unset($pro_sql_data);

	$req_id_cond="";
	if($requ_id!="") 
	{
		$requ_id=substr($requ_id,0,-1);
		$requ_id=implode(",",array_filter(array_unique(explode(",",$requ_id))));
		if($db_type==0) $req_id_cond="and b.requisition_no in(".$requ_id.")";
		else
		{
			$req_ids=explode(",",$requ_id);
			if(count($req_ids)>990)
			{
				$req_id_cond="and (";
				$req_ids=array_chunk($req_ids,990);
				$z=0;
				foreach($req_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $req_id_cond.=" b.requisition_no in(".$id.")";
					else $req_id_cond.=" or b.requisition_no in(".$id.")";
					$z++;
				}
				$req_id_cond.=")";
			}
			else $req_id_cond="and b.requisition_no in(".$requ_id.")";
		}
	}
	
	if ($txt_file_no == '' && $txt_internal == '')
	{
		$po_sql="SELECT a.job_no,$year_field, a.style_ref_no, a.buyer_name, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($po_id) $year_cond";
		$po_sql_data=sql_select($po_sql);
		$all_po_id = '';
		foreach($po_sql_data as $row)
		{
			$po_array[$row[csf('id')]]['no']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['ship_date']=$row[csf('pub_shipment_date')];
			$po_array[$row[csf('id')]]['year']=$row[csf('year')];
			$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];		
			$po_array[$row[csf('id')]]['ref_no']=$row[csf('grouping')];
		}	
	}

	$sqlyarn = "SELECT b.knit_id,b.requisition_date, b.requisition_no,b.yarn_qnty,d.yarn_count_id, d.yarn_type, d.color as yarn_color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id ,
		RTRIM(XMLAGG(XMLELEMENT(e,a.demand_prefix_number,',').EXTRACT('//text()') ORDER BY a.demand_prefix_number).GETCLOBVAL(),',') AS demand_prefix_number,sum(c.yarn_demand_qnty) as demand_qnty from product_details_master d,ppl_yarn_requisition_entry b 
		left join ppl_yarn_demand_reqsn_dtls c on b.id=c.requisition_id and c.status_active=1 and c.is_deleted=0
		left join ppl_yarn_demand_entry_mst a on a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 
		where d.id=b.prod_id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 
		and d.is_deleted=0 $requsition_date_cond $prog_cond $date_cond $req_cond $req_id_cond group by b.knit_id,b.requisition_date, b.requisition_no,b.yarn_qnty,d.yarn_count_id, d.yarn_type, d.color,d.yarn_comp_type1st,d.brand,d.lot,b.prod_id order by b.requisition_no";
	//echo $sqlyarn; die();
	$sql_yarn_result=sql_select($sqlyarn);
	$requ_id='';
	$prod_id="";
	$prog_id="";
	$yarn_color_id="";
	foreach($sql_yarn_result as $row)
	{
		$requ_id.=$row[csf('requisition_no')].",";
		$prod_id.=$row[csf('prod_id')].",";
		$prog_id.=$row[csf('knit_id')].",";
		$yarn_color_id.=$row[csf('yarn_color')].",";
	}

	$yarn_color_id = chop($yarn_color_id," , ");
	if($yarn_color_id!="")
	{
		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0 and id in($yarn_color_id)", "id", "color_name" );
	}
	
	// requsition cond 
	$req_id_cond="";
	if($requ_id!="") 
	{
		$requ_id=substr($requ_id,0,-1);
		$requ_id=implode(",",array_filter(array_unique(explode(",",$requ_id))));
		if($db_type==0) $req_id_cond="and b.requisition_no in(".$requ_id.")";
		else
		{
			$req_ids=explode(",",$requ_id);
			if(count($req_ids)>990)
			{
				$req_id_cond="and (";
				$req_ids=array_chunk($req_ids,990);
				$z=0;
				foreach($req_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $req_id_cond.=" b.requisition_no in(".$id.")";
					else $req_id_cond.=" or b.requisition_no in(".$id.")";
					$z++;
				}
				$req_id_cond.=")";
			}
			else $req_id_cond="and b.requisition_no in(".$requ_id.")";
		}
	}

	// product cond 
	$prod_id_cond="";
	$demand_prod_id_cond="";
	if($prod_id!="") 
	{
		$prod_id=substr($prod_id,0,-1);
		$prod_id=implode(",",array_filter(array_unique(explode(",",$prod_id))));

		if($db_type==0) {
			$prod_id_cond="and c.id in(".$prod_id.")";
			$demand_prod_id_cond="and c.prod_id in(".$prod_id.")";
			$demand_prod_id_cond="and c.prod_id in(".$prod_id.")";
		}
		else
		{
			$prod_ids=explode(",",$prod_id);
			if(count($prod_ids)>990)
			{
				$prod_id_cond="and (";
				$demand_prod_id_cond="and (";
				$prod_ids=array_chunk($prod_ids,990);
				$z=0;
				foreach($prod_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) {
						$prod_id_cond.=" c.id in(".$id.")";
						$demand_prod_id_cond.=" c.prod_id in(".$id.")";
					}
					else {
						$prod_id_cond.=" or c.id in(".$id.")";
						$demand_prod_id_cond.=" or c.prod_id in(".$id.")";
					}
					$z++;
				}
				$prod_id_cond.=")";
				$demand_prod_id_cond.=")";
			}
			else {
				$prod_id_cond="and c.id in(".$prod_id.")";
				$demand_prod_id_cond="and c.prod_id in(".$prod_id.")";
			}
		}
	}
	//echo $prod_id_cond;

	// prog cond 
	$prog_id_cond="";
	if($prog_id!="") 
	{
		$prog_id=substr($prog_id,0,-1);
		$prog_id=implode(",",array_filter(array_unique(explode(",",$prog_id))));

		if($db_type==0) {
			$prog_id_cond="and b.knit_id in(".$prog_id.")";
		}
		else
		{
			$prog_ids=explode(",",$prog_id);
			if(count($prog_ids)>990)
			{
				$prog_id_cond="and (";

				$prog_ids=array_chunk($prog_ids,990);
				$z=0;
				foreach($prog_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) {
						$prog_id_cond.=" b.knit_id in(".$id.")";
					}
					else {
						$prog_id_cond.=" or b.knit_id in(".$id.")";
					}
					$z++;
				}
				$prog_id_cond.=")";
			}
			else {
				$prog_id_cond="and b.knit_id in(".$prog_id.")";
			}
		}
	}

	$po_id_cond="";
	if($po_id!="") 
	{
		$po_id=substr($po_id,0,-1);
		if($db_type==0) $po_id_cond="and b.id in(".$po_id.")";
		else
		{
			$po_ids=explode(",",$po_id);
			if(count($po_ids)>1000)
			{
				$po_id_cond="and (";
				$po_ids=array_chunk($po_ids,1000);
				$z=0;
				foreach($po_ids as $id)
				{
					$id=implode(",",$id);
					if($z==0) $po_id_cond.=" b.id in(".$id.")";
					else $po_id_cond.=" or b.id in(".$id.")";
					$z++;
				}
				$po_id_cond.=")";
			}
			else $po_id_cond="and b.id in(".$po_id.")";
		}
	}

	if($prod_id_cond != "")
	{
		$product_sql= "select c.id, c.product_name_details, c.lot, c.supplier_id from product_details_master c where c.company_id=$company_name and c.item_category_id=1 $prod_id_cond";
		$product_data = sql_select($product_sql);
		$product_details_arr=array();
		foreach($product_data as $row)
		{
			$product_details_arr[$row[csf('id')]]['desc']=$row[csf('product_name_details')];
			$product_details_arr[$row[csf('id')]]['lot']=$row[csf('lot')]; 
			$product_details_arr[$row[csf('id')]]['supplier']=$row[csf('supplier_id')]; 
		}
		unset($product_data);
	}
	//echo $req_sql;die;
		
	$yarn_sql="SELECT  a.remarks,b.requisition_no as requ_no, b.store_id, b.brand_id, b.cons_quantity as issue_qnty, b.return_qnty,c.yarn_type, c.yarn_count_id, c.lot, c.color,b.prod_id from inv_issue_master a, inv_transaction b, product_details_master c where a.item_category=1 and a.entry_form=3 and a.company_id=$company_name and a.issue_basis in (3,8) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $req_id_cond $prod_id_cond";
	//echo $yarn_sql; die;
	$yarn_sql_data = sql_select($yarn_sql);
	$yarn_issue_details_arr=array();
	$yarn_issue_remark_arr=array();
	foreach($yarn_sql_data as $row)
	{
		$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['qty']+=$row[csf('issue_qnty')];
		$yarn_issue_details_arr[$row[csf('requ_no')]][$row[csf('prod_id')]]['ret_qty']+=$row[csf('return_qnty')];
		$yarn_issue_remark_arr[$row[csf('requ_no')]][$row[csf('yarn_count_id')]][$row[csf('yarn_type')]][$row[csf('color')]][$row[csf('lot')]]['remark']=$row[csf('remarks')];
	}
	unset($yarn_sql_data);
	$width=2000;
	ob_start();
	?>
	<fieldset style="width:<?= $width+25;?>px;">
		<table cellpadding="0" cellspacing="0" width="<?= $width;?>">
			<tr>
			   <td align="center" width="100%" style="font-size:16px"><strong>Requisition Against Demand Status</strong><br><b>
                   <? if($start_date!='') echo change_date_format($start_date).' To '.change_date_format($end_date);else echo '';?></b>
               </td>
			</tr>
		</table>	
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width+20;?>" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">Buyer</th>
                <th width="80">File No</th>
                <th width="80">Ref. No</th>
                 <th width="100">FB no.</th>	
                 <th width="100">Job no.</th>
                 <th width="100">Order</th>	
                 <th width="100">Style</th>
				<th width="80">Prog. No</th>
                <th width="100">Knitting Company</th>
                <th width="80">Req. Date</th>
				<th width="80">Req. No</th>
				
				<th width="80">Y. Count</th>
				<th width="120">Y Composition</th>
				<th width="80">Y. Type</th>
				<th width="100">Brand</th>
				<th width="80">Y. Color</th>
                <th width="60">Lot</th>
				<th width="70">Program Qty.</th>
				<th width="70">Requisition Qty.</th>
				
				<th width="70">Issue Qty</th>
				<th width="70">Balance Qty</th>
				<th width="">Remarks</th>
			</thead>
		</table>
		<div style="width:<?= $width+25;?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width;?>" class="rpt_table" id="tbl_list_search">
                <?				
				$i=1;$k=0;$z=0;$m=0;$s=0;$req_row='';$tt=1;
				$total_program_qty=0;$total_demand_qty=0;$total_demand_issue=0;$total_demand_blanace=0;
				$balance_qty = array();
				$requsition_id_array = array();
				$prog_requsition_id_array = array();
				$yarn_requsition_id_array = array();
				$yarn_remark_id_array = array();
				
                foreach($sql_yarn_result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$prog_no  = $row[csf('knit_id')];
					$req_no   = $row[csf('requisition_no')];
					$req_date = $row[csf('requisition_date')];
					$prod_id  = $row[csf('prod_id')];
					//echo count($req_row);
					$knit_source = "";
					$knit_source = $plan_details_arr[$prog_no]['source'];
					$po_ids      = array_unique(explode(",", chop($plan_details_arr[$prog_no]['po_id'] ,",") ));
					$knit_company="";
					if($knit_source==1) $knit_company=$location_library[$plan_details_arr[$prog_no]['location']];
					else if($knit_source==3) $knit_company=$supplierArr[$plan_details_arr[$prog_no]['knitting_party']];
					
					$file_no='';	$ref_no='';
					$po_numberArr=array();
					$job_noArr=array();
					$style_ref_noArr=array();
					foreach($po_ids as $row_id)
					{
						if($file_no=='') $file_no=$po_array[$row_id]['file_no']; else  $file_no.=",".$po_array[$row_id]['file_no'];
						if($ref_no=='') $ref_no=$po_array[$row_id]['ref_no']; else  $ref_no.=",".$po_array[$row_id]['ref_no'];

						$po_numberArr[$po_array[$row_id]['no']]=$po_array[$row_id]['no'];
						$job_noArr[$po_array[$row_id]['job_no']]=$po_array[$row_id]['job_no'];
						$style_ref_noArr[$po_array[$row_id]['style_ref']]=$po_array[$row_id]['style_ref'];
					}

					$ref_no   = implode(",",array_unique(explode(",", chop($ref_no, ","))) );
					$file_no  = implode(",",array_unique(explode(",", $file_no)));
					$prog_qty = $plan_details_arr[$prog_no]['program_qnty'];
					$buyer    = $plan_details_arr[$prog_no]['buyer'];
					
					$issue_qty = $yarn_issue_details_arr[$req_no][$prod_id]['qty'];					
					$issue_returnble_qty = $yarn_issue_details_arr[$req_no][$prod_id]['ret_qty'];
					$issue_remark = $yarn_issue_remark_arr[$req_no][$row[csf('yarn_count_id')]][$row[csf('yarn_type')]][$row[csf('yarn_color')]][$row[csf('lot')]]['remark'];
					$fb_booking_no=implode(',',$plan_details_arr[$prog_no]['booking_no']);
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <?
                        if ($txt_internal == "")
                        {
	                      	if (!in_array($req_no,$requsition_id_array) )
							{
								$k++;
								?>
	                            <td width="30" align="center"><? echo $k; ?></td>
	                            <td width="100" align="center"><p><? echo $buyer_name_arr[$buyer]; ?></p></td>
	                            <td width="80" align="center"><p><? echo $file_no; ?></p></td>
	                            <td width="80" align="center"><div style="width:80px; word-wrap:break-word;"><? echo $ref_no; ?></div></td>
                                <td width="100"><p><? echo $fb_booking_no; ?></p></td>
                                <td width="100"><p><? echo implode(',',$job_noArr); ?></p></td>
                                <td width="100"><p><? echo implode(',',$po_numberArr); ?></p></td>
                                <td width="100"><p><? echo implode(',',$style_ref_noArr); ?></p></td>
                                <td width="80" align="center"><p><? echo $prog_no; ?></p></td>
	                            <td width="100" align="center"><p><? echo $knit_company; ?></p></td>
	                            <td width="80" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
	                            <td width="80" align="center"><p><? echo $req_no; ?></p></td>
	                            <?
								$requsition_id_array[] = $req_no;
							}
							else
							{
								?>
								<td width="30" align="center"></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
                                <td width="100"><p><? echo $fb_booking_no; ?></p></td>
                                <td width="100"><p><? echo implode(',',$job_noArr); ?></p></td>
                                <td width="100"><p><? echo implode(',',$po_numberArr); ?></p></td>
                                <td width="100"><p><? echo implode(',',$style_ref_noArr); ?></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80"><p></p></td>
	                            <td width="80"><p></p></td>
							    <?
							}
							?>
	                      
	                        <td width="80" ><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
	                        <td width="120"><div style="width:120px; word-wrap:break-word;"><? echo $composition[$row[csf('yarn_comp_type1st')]]; ?></div></td>
	                        <td width="80" align="center"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td width="100" align="center"><p><? echo $brand_name_arr[$row[csf('brand')]]; ?></p></td>
	                        <td width="80" align="center"><p><? echo $color_library[$row[csf('yarn_color')]]; ?></p></td>
	                        <td width="60" align="center"><p><? echo $row[csf('lot')];?></p></td>
	                        <?
	                        if (!in_array($req_no,$prog_requsition_id_array) )
							{
								$z++;
								$balance_qty[$prog_no]=$prog_qty;
								$total_program_qty+=$prog_qty;
						  	    ?>
	                      	    <td width="70" align="right"><p><? echo number_format($prog_qty,2); ?></p></td>
	                      	    <?
						  	    $prog_requsition_id_array[]=$req_no;
							}
							else
							{ 
								?>
							 	<td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
							    <?	
							}
							?>
						    <td width="70" align="right"><p>
						  		<? 
						  		$yarn_requsition_qnty = $row[csf('yarn_qnty')];
						  		echo number_format($yarn_requsition_qnty,2); 
						  		?>
						    </p></td>

	                       
	                        <?
	                        if (!in_array($req_no,$yarn_requsition_id_array) )
							{
								$m++;							
						 		?>
	                            <td width="70" align="right"><p><a href="##" onClick="openmy_popup(<? echo $req_no;?>,<? echo $prod_id;?>,'issue_popup','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $company_name;?>')">
	                           	    <? echo number_format($issue_qty,2); ?></a>
	                            </p></td>
								<input type="hidden" name="txt_hidden_demand_no_<?php echo $row[csf("requisition_no")];?>" id="txt_hidden_demand_no_<?php echo $row[csf("requisition_no")];?>" class="text_boxes" >
	                            <td width="70" align="right"><p>
	                          	    <? 
	                          	    $balance_quantity = ($yarn_requsition_qnty-$issue_qty);
	                          	    echo number_format($balance_quantity,2); ?>                          	
	                            </p></td>
	                            <td width=""><div style=" word-break:break-all"><? echo $issue_remark; ?></div></td>
	                      	    <?
							}
							else
							{ 
							    ?>
							 	<td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
	                            <td width=""><div style=" word-break:break-all"><? //echo $issue_remark; ?></div></td>
							    <?	
							}
						}
						else if ($txt_internal != "" && $txt_internal == $ref_no)
						{
	                      	if (!in_array($req_no,$requsition_id_array) )
							{
								$k++;
								?>
	                            <td width="30" align="center"><? echo $k; ?></td>
	                            <td width="100" align="center"><p><? echo $buyer_name_arr[$buyer]; ?></p></td>
	                            <td width="80" align="center"><p><? echo $file_no; ?></p></td>
	                            <td width="80" align="center"><div style="width:80px; word-wrap:break-word;"><? echo $ref_no; ?></div></td>
	                            
                                <td width="100"><p><? echo $fb_booking_no; ?></p></td>
                                <td width="100"><p><? echo implode(',',$job_noArr); ?></p></td>
                                <td width="100"><p><? echo implode(',',$po_numberArr); ?></p></td>
                                <td width="100"><p><? echo implode(',',$style_ref_noArr); ?></p></td>
                                
                                
                                
                                <td width="80" align="center"><p><? echo $prog_no; ?></p></td>
	                            <td width="100" align="center"><p><? echo $knit_company; ?></p></td>
	                            <td width="80" align="center"><p><? echo change_date_format($row[csf('requisition_date')]); ?></p></td>
	                            <td width="80" align="center"><p><? echo $req_no; ?></p></td>
	                            <?
								$requsition_id_array[] = $req_no;
							}
							else
							{
								?>
								<td width="30" align="center"></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
	                            <td width="80" align="center"><p></p></td>
                                <td width="100"><p><? echo $fb_booking_no; ?></p></td>
                                <td width="100"><p><? echo implode(',',$job_noArr); ?></p></td>
                                <td width="100"><p><? echo implode(',',$po_numberArr); ?></p></td>
                                <td width="100"><p><? echo implode(',',$style_ref_noArr); ?></p></td>
                                
	                            <td width="80" align="center"><p></p></td>
	                            <td width="100" align="center"><p></p></td>
	                            <td width="80"><p></p></td>
	                            <td width="80"><p></p></td>
							    <?
							}
							?>
	                      
	                        <td width="80" ><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
	                        <td width="120"><div style="width:120px; word-wrap:break-word;"><? echo $composition[$row[csf('yarn_comp_type1st')]]; ?></div></td>
	                        <td width="80" align="center"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td width="100" align="center"><p><? echo $brand_name_arr[$row[csf('brand')]]; ?></p></td>
	                        <td width="80" align="center"><p><? echo $color_library[$row[csf('yarn_color')]]; ?></p></td>
	                        <td width="60" align="center"><p><? echo $row[csf('lot')];?></p></td>
	                        <?
	                        if (!in_array($req_no,$prog_requsition_id_array) )
							{
								$z++;
								$balance_qty[$prog_no]=$prog_qty;
								$total_program_qty+=$prog_qty;
						  	    ?>
	                      	    <td width="70" align="right"><p><? echo number_format($prog_qty,2); ?></p></td>
	                      	    <?
						  	    $prog_requsition_id_array[]=$req_no;
							}
							else
							{ 
								?>
							 	<td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
							    <?	
							}
							?>
						    <td width="70" align="right"><p>
						  		<? 
						  		$yarn_requsition_qnty = $row[csf('yarn_qnty')];
						  		echo number_format($yarn_requsition_qnty,2); 
						  		?>
						    </p></td>
	                        <?
	                        if (!in_array($req_no,$yarn_requsition_id_array) )
							{
								$m++;							
						 		?>
	                            <td width="70" align="right"><p><a href="##" onClick="openmy_popup(<? echo $req_no;?>,<? echo $prod_id;?>,'issue_popup','<? echo $start_date;?>','<? echo $end_date;?>','<? echo $company_name;?>')">
	                           	    <? echo number_format($issue_qty,2); ?></a>
	                            </p></td>
								<input type="hidden" name="txt_hidden_demand_no_<?php echo $row[csf("requisition_no")];?>" id="txt_hidden_demand_no_<?php echo $row[csf("requisition_no")];?>" class="text_boxes" >
	                            <td width="70" align="right"><p>
	                          	    <? 
	                          	    $balance_quantity = ($yarn_requsition_qnty-$issue_qty);
	                          	    echo number_format($balance_quantity,2); ?>                          	
	                            </p></td>
	                            <td width=""><div style=" word-break:break-all"><? echo $issue_remark; ?></div></td>
	                      	    <?
							}
							else
							{ 
							    ?>
							 	<td width="70" align="right"><p><? //echo number_format($prog_qty,2); ?></p></td>
	                            <td width=""><div style=" word-break:break-all"><? //echo $issue_remark; ?></div></td>
							    <?	
							}
						}	
						?>
                    </tr>
                    <?							
					$i++;
					$total_requsition_qnty += $row[csf('yarn_qnty')];
					$total_demand_qty += $row[csf("demand_qnty")];
					$total_demand_blanace += $prog_qty-$row[csf("demand_qnty")];
					$total_demand_issue += $issue_qty;
					$total_balance_quantity += ($row[csf('yarn_qnty')]-$issue_qty);
					$total_issue_returnble_qty += $issue_returnble_qty;
				}
				?>
			</table>
            <table width="<?= $width;?>"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	            <tfoot>
	                <tr>
	               		<th width="30">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80">&nbsp;</th>                            
	                    <th width="80">&nbsp;</th>
	                    <th width="120">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
						<th width="60">Total</th>
						<th width="70" id="value_tot_prog"><?php echo $total_program_qty;?></th>
	                    <th width="70" id="value_tot_requsition"><?php echo $total_requsition_qnty;?></th>  
	                    <th width="70" id="value_tot_issue"><?php echo $total_demand_issue;?></th>
	                    <th width="70" id="value_tot_balance3"><?php echo number_format($total_balance_quantity,2);?></th>
	                    <th></th>
	                </tr>
				</tfoot>
            </table>
		</div>
	</fieldset>      
	<?	
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="requires/".$user_name."_".$name.".xls";
	echo "$total_data####$filename####2";
	exit(); 	
}

if($action=="issue_popup")
{
	echo load_html_head_contents("Issue PopUp", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $yarn_count_id;die;
	if($yarn_count_id!='') $count_cond=" and c.yarn_count_id=$yarn_count_id";else $count_cond="";
	if($yarn_type!='') $type_cond=" and c.yarn_type=$yarn_type";else $type_cond="";
	if($color!='') $color_cond=" and c.color=$color";else $color_cond="";
	if($lots!='') $lots_cond=" and c.lot='$lots'";else $lots_cond="";
	if($date_from && $date_to!=""){ $dateCond="and a.issue_date between '$date_from' and '$date_to'";}else{$dateCond="";}
	//echo $lots_cond;
	$prog_sql=sql_select("select b.requisition_no,b.knit_id from ppl_yarn_requisition_entry b where b.status_active=1 and b.is_deleted=0");
	$prog_arr=array();
	foreach($prog_sql as $row)
	{
		$prog_arr[$row[csf('requisition_no')]]['plan']=$row[csf('knit_id')];	
	}


	$demand_con = '';
	if($demand_no!=''){
		$demand_con = "and b.demand_no= '$demand_no'";
	}


	$issue_sql=sql_select("select  a.issue_date,a.issue_number,b.requisition_no as requ_no, b.cons_quantity as issue_qnty, b.return_qnty from inv_issue_master a, inv_transaction b,product_details_master c where c.id=b.prod_id and c.id=$prod_id and a.item_category=1 and a.entry_form=3 and b.requisition_no=$requ_id and a.issue_basis in (3,8) and a.id=b.mst_id and a.company_id=$company_id and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dateCond $demand_con"); 

	?>
    <div style=" width:460px">
	    <table id="" cellspacing="0" cellpadding="0" border="1" rules="all" width="460px" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <tr>
                    <th colspan="6">Issue Details</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="80">Issue Date</th>
                    <th width="110">Issue ID</th>
                    <th width="80">Requ. No</th>
                    <th width="80">Prog. No.</th>
                    <th width="80">Issue Qty</th>
                </tr>
            </thead>
	    </table>
        <div style="width:480px;" id="scroll_body">
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="460" class="rpt_table" id="table_body">
	            <tbody>
		            <?
					$i=1;$total_issue_qty=0;
		            foreach( $issue_sql as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
		                    <td width="30"><? echo $i; ?></td>
		                    <td width="80"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
		                    <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
		                    <td width="80"><p><? echo $row[csf('requ_no')]; ?></p></td>
		                    <td width="80" align="center"><? echo $prog_arr[$row[csf('requ_no')]]['plan']; ?></td>
		                    <td align="right" width="80"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
	                    </tr>
	                    <?
						$i++;
						$total_issue_qty+=$row[csf('issue_qnty')];
					}
					?>
	            </tbody>
	            <tfoot>
		            <tr>
			            <th colspan="5" align="right"> Total </th>
			            <th align="right"><? echo number_format($total_issue_qty,2); ?> </th>
		            </tr>
	            </tfoot>
	        </table>
        </div>
        <script>setFilterGrid("table_body",-1);</script>
    </div>
    <?
}

if($action=="demand_popup")
{
	echo load_html_head_contents("Issue PopUp", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	
	if($date_from && $date_to!=""){ $dateCond="and c.demand_date between '$date_from' and '$date_to'";}else{$dateCond="";}
	
	$demand_con = '';
	if($demand_no!=''){
		$demand_con = "and c.demand_system_no= '$demand_no'";
	}

	$sql_demand="select b.requisition_no, b.mst_id as demand_id, b.demand_qnty, b.save_string, c.demand_system_no, c.demand_date  
	from ppl_yarn_demand_entry_dtls b, ppl_yarn_demand_entry_mst c 
	where  b.mst_id=c.id and b.requisition_no=$requ_id $dateCond $demand_con  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		
	$sql_demand_result=sql_select($sql_demand);
	$demand_pop_data=array();
	$i=0;
	foreach($sql_demand_result as $row)
	{
		$save_ref=explode(",",$row[csf("save_string")]);
		foreach($save_ref as $data)
		{
			$data_ref=explode("_",$data);
			if($data_ref[0]==$prod_id)
			{
				$demand_pop_data[$i]["demand_qnty"]=$data_ref[2];
				$demand_pop_data[$i]["demand_system_no"]=$row[csf("demand_system_no")];
				$demand_pop_data[$i]["demand_date"]=$row[csf("demand_date")];
				$i++;
			}
		}
	}
	?>
	<div style=" width:460px">
		<table id="" cellspacing="0" cellpadding="0" border="1" rules="all" width="460px" class="rpt_table" >
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th colspan="4">Demand Details</th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="200">Demand No</th>
					<th width="100">Demand Date</th>
					<th >Demand Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:480px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="460" class="rpt_table" id="table_body">
				<tbody>
					<?
					$i=1;$total_issue_qty=0;
					foreach( $demand_pop_data as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="200"><p><? echo $row[('demand_system_no')]; ?></p></td>
							<td width="100" align="center"><p><? if($row[('demand_date')]!="" && $row[('demand_date')]!="0000-00-00") echo change_date_format($row[('demand_date')]); ?></p></td>
							<td align="right"><? echo number_format($row[('demand_qnty')],2); ?></td>
						</tr>
						<?
						$i++;
						$total_issue_qty+=$row[('demand_qnty')];
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="3" align="right"> Total </th>
						<th align="right"><? echo number_format($total_issue_qty,2); ?> </th>
					</tr>
				</tfoot>
			</table>
		</div>
		<script>setFilterGrid("table_body",-1);</script>
	</div>
	<?
}
?>