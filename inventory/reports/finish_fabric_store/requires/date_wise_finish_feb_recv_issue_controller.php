<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=186 and is_deleted=0 and status_active=1");
	
	//echo $print_report_format; disconnect($con); die;
	
	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#search1').hide();\n";
	echo "$('#search2').hide();\n";
	echo "$('#search3').hide();\n";
	
	
	

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			
			if($id==726){echo "$('#search1').show();\n";}
			if($id==727){echo "$('#search2').show();\n";}
			if($id==149){echo "$('#search3').show();\n";}
			
		}
	}
	exit();	
}




if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	$company_id = $data[0];
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($company_id) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_id", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data' and  b.category_type in(2,3)  group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}

if($action=="load_drop_down_stores")
{
    extract($_REQUEST);
	$data=explode("_",$data);
    $company_ids = str_replace("'","",$data[0]); 
	$cbo_catgoryBy = str_replace("'","",$data[1]); 
	echo create_drop_down( "cbo_store_id", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id in($company_ids) and b.category_type in(2,3)  group by a.id,a.store_name","id,store_name", 0, "", 0, "",$disable );
	exit();
}
if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$exchange_rate=set_conversion_rate( $data[0], $conversion_date, $data[2] );
	echo $exchange_rate;
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(str)
	{
		var splitData = str.split("_");
		//alert (splitData[1]);
		$("#hide_job_id").val(splitData[0]); 
		$("#hide_job_no").val(splitData[1]); 
		parent.emailwindow.hide();
	}
	</script>
    <input type='hidden' id='hide_job_no' name="hide_job_no" />
    <input type='hidden' id='hide_job_id' name="hide_job_id" />
	<?
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$company_id=str_replace("'","",$companyID);
	$buyer_id=str_replace("'","",$buyer_name);
	$year_id=str_replace("'","",$cbo_year_id);
	$search_type=str_replace("'","",$search_type);
	//$month_id=$data[5];
	//echo $month_id;
	$sql_cond="";
	if($buyer_id>0) $sql_cond .=" and a.buyer_name=$buyer_id";
	if($buyer_id>0) $sql_cond2 =" and a.buyer_id=$buyer_id";
	
	if($db_type==0) $year_field_by="year(a.insert_date)";
	else if($db_type==2) $year_field_by="to_char(a.insert_date,'YYYY')";
	else $year_field_by="";
	
	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";
	
	if($search_type==1)
	{
		$arr=array (0=>$buyer_arr);
		$sql= "select a.id as id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, $year_field_by as year from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $sql_cond $year_cond order by id DESC";
		echo create_list_view("list_view", "Buyer Name,Job No,Year,Style Ref. No", "170,130,100","610","350",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,job_no_prefix_num,year,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0','') ;
		exit();
	}
	else if($search_type==2)
	{
		$arr=array (0=>$buyer_arr);
		$sql= "select a.id as id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, $year_field_by as year, b.id as po_id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id $sql_cond $year_cond order by id DESC";
		echo create_list_view("list_view", "Buyer Name,Job No,Year,Style Ref. No,Order No", "170,70,70,120","610","350",0, $sql , "js_set_value", "po_id,po_number", "", 1, "buyer_name,0,0,0,0", $arr , "buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "","setFilterGrid('list_view',-1)",'0,0,0,0,0','') ;
		exit();
	}
	else if($search_type==3)
	{
		$arr=array (0=>$buyer_arr);
		$sql= "select  x.id,x.job_no, x.job_no_prefix_num,x.buyer_name,x.style_ref_no,x.year,x.booking_id,x.booking_no,x.booking_no_prefix_num from ( select a.id as id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, $year_field_by as year, c.id as booking_id, c.booking_no, c.booking_no_prefix_num from wo_po_details_master a, wo_booking_dtls b, wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and b.booking_type=1 and c.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_name=$company_id $sql_cond $year_cond
		group by a.id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.insert_date, c.id, c.booking_no, c.booking_no_prefix_num 
		union all 
		select null as id, null as job_no, null as job_no_prefix_num, a.buyer_id, null as style_ref_no, null as year, a.id as booking_id, a.booking_no, a.booking_no_prefix_num 
		from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b 
		where a.booking_no=b.booking_no and a.booking_type = 4 and a.company_id =$company_id $sql_cond2  $year_cond 
		group by a.buyer_id, a.id, a.booking_no, a.booking_no_prefix_num )
		x group by x.id,x.job_no, x.job_no_prefix_num,x.buyer_name,x.style_ref_no,x.year,x.booking_id,x.booking_no,x.booking_no_prefix_num 
		order by x.booking_id DESC";
		echo create_list_view("list_view", "Buyer Name,Job No,Year,Style Ref. No,Booking No", "170,70,70,120","610","350",0, $sql , "js_set_value", "booking_id,booking_no_prefix_num", "", 1, "buyer_name,0,0,0,0", $arr , "buyer_name,job_no_prefix_num,year,style_ref_no,booking_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0','') ;
		exit();
	}
}


if($action=="pi_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(str)
	{
		var splitData = str.split("_");
		//alert (splitData[1]);
		$("#hide_pi_id").val(splitData[0]); 
		$("#hide_pi_no").val(splitData[1]); 
		parent.emailwindow.hide();
	}
	</script>
    <input type='hidden' id='hide_pi_no' name="hide_pi_no" />
    <input type='hidden' id='hide_pi_id' name="hide_pi_id" />
	<?
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$company_id=str_replace("'","",$companyID);
	$buyer_id=str_replace("'","",$buyer_name);
	$year_id=str_replace("'","",$cbo_year_id);
	$search_type=str_replace("'","",$search_type);
	//$month_id=$data[5];
	//echo $month_id;
	if($db_type==0) $year_field_by="year(a.insert_date)";
	else if($db_type==2) $year_field_by="to_char(a.insert_date,'YYYY')";
	else $year_field_by="";
	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";
	$arr=array (0=>$supplier_arr);
	$sql= "select a.id as id, a.pi_number, a.pi_date, a.supplier_id, $year_field_by as year from com_pi_master_details a where a.status_active=1 and a.is_deleted=0 and a.item_category_id=2 and a.importer_id=$company_id $year_cond order by id DESC";
	echo create_list_view("list_view", "Supplier Name,PI No,Year,PI Date", "170,150,100","610","350",0, $sql , "js_set_value", "id,pi_number", "", 1, "supplier_id,0,0,0", $arr , "supplier_id,pi_number,year,pi_date", "","setFilterGrid('list_view',-1)",'0,0,0,3','') ;
	exit();
}


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$rpt_type=str_replace("'","",$rpt_type);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_store_id=str_replace("'","",$cbo_store_id);
	//echo $cbo_company_id."=".$cbo_store_id;die;
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_booking_id=str_replace("'","",$txt_booking_id);
	$txt_pi_no=str_replace("'","",$txt_pi_no);
	$txt_pi_id=str_replace("'","",$txt_pi_id);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_exchange_rate=str_replace("'","",$txt_exchange_rate);
	$cbo_category_type=str_replace("'","",$cbo_category_type);
	
	if($txt_exchange_rate=="" || $txt_exchange_rate==0) $txt_exchange_rate=1;
	$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");


	if($cbo_category_type==1)
	{


		$sql_cond="";
		//if($cbo_buyer_id > 0) $sql_cond=" and a.buyer_name=$cbo_buyer_id";
		$all_batch_id_arr=array();
		if($txt_booking_no !="" || $txt_job_no != "" )
		{
			if($txt_booking_no !="")
			{
				if($cbo_year)
				{
					if($db_type==0)
					{
						if($cbo_year==0) $year_cond=""; else $year_cond="and YEAR(b.insert_date)=$cbo_year";
					}
					else if($db_type==2)
					{
						if($cbo_year==0) $year_cond=""; else $year_cond=" and to_char(b.insert_date,'YYYY')=$cbo_year";
					}
				}
				$batch_sql="select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short, b.short_booking_type,a.booking_without_order,b.booking_type from pro_batch_create_mst a, wo_booking_mst b where a.booking_no=b.booking_no and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.company_id in ($cbo_company_id) and a.booking_no like '%$txt_booking_no%' $year_cond";
			}
			else if($txt_job_no !="")
			{
				if($cbo_year)
				{
					if($db_type==0)
					{
						if($cbo_year==0) $year_cond=""; else $year_cond="and YEAR(d.insert_date)=$cbo_year";
					}
					else if($db_type==2)
					{
						if($cbo_year==0) $year_cond=""; else $year_cond=" and to_char(d.insert_date,'YYYY')=$cbo_year";
					}
				}
				$batch_sql="select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short, b.short_booking_type,a.booking_without_order from pro_batch_create_mst a, wo_booking_mst b, wo_booking_dtls c, wo_po_details_master d
				where a.booking_no=b.booking_no and b.booking_no=c.booking_no and a.booking_no=c.booking_no and c.job_no=d.job_no  and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id in ($cbo_company_id) $year_cond and c.job_no like '%$txt_job_no%'";
				/*$batch_sql="select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short, b.short_booking_type,a.booking_without_order from pro_batch_create_mst a, wo_booking_dtls b, wo_booking_dtls c
				where a.booking_no=b.booking_no and b.booking_no=c.booking_no and a.booking_no=c.booking_no and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$txt_job_no'";*/
			}
			$batch_result=sql_select($batch_sql);

			if(empty($batch_result)) 
			{
				echo "Data Not Found";die;
			}

			$batch_data=array();
			foreach($batch_result as $row)
			{
				$all_batch_id_arr[$row[csf("id")]]=$row[csf("id")];
				$batch_data[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
				$batch_data[$row[csf("id")]]["batch_date"]=$row[csf("batch_date")];
				$batch_data[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
				$batch_data[$row[csf("id")]]["is_short"]=$row[csf("is_short")];
				$batch_data[$row[csf("id")]]["short_booking_type"]=$row[csf("short_booking_type")];
				$batch_data[$row[csf("id")]]["booking_without_order"]=$row[csf("booking_without_order")];
				$batch_data[$row[csf("id")]]["booking_type"]=$row[csf("booking_type")];
			}
			
		}
		else
		{
			$batch_sql="select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short, b.short_booking_type,0 as booking_without_order,b.booking_type,b.entry_form  from pro_batch_create_mst a, wo_booking_mst b where a.booking_no=b.booking_no and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.company_id in($cbo_company_id) 
			union all 
			select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short,null as short_booking_type,1 as booking_without_order,b.booking_type,b.entry_form_id as entry_form  
			from pro_batch_create_mst a, wo_non_ord_samp_booking_mst b 
			where a.booking_no=b.booking_no and b.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.company_id in($cbo_company_id) ";

			$batch_result=sql_select($batch_sql);
			$batch_data=array();
			$bookingNos="";$booking_typeArr=array();
			foreach($batch_result as $row)
			{
				if($row[csf("booking_without_order")]==1)
				{
					$bookingNos.="'".$row[csf("booking_no")]."',";
				}
				$batch_data[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
				$batch_data[$row[csf("id")]]["batch_date"]=$row[csf("batch_date")];
				$batch_data[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
				$batch_data[$row[csf("id")]]["is_short"]=$row[csf("is_short")];
				$batch_data[$row[csf("id")]]["short_booking_type"]=$row[csf("short_booking_type")];
				$batch_data[$row[csf("id")]]["booking_without_order"]=$row[csf("booking_without_order")];
				$batch_data[$row[csf("id")]]["booking_type"]=$row[csf("booking_type")];

				$bookingType="";
				if($row[csf('booking_type')] == 4 && $row[csf("booking_without_order")]==0)
				{
					$booking_typeArr[$row[csf("booking_no")]]="Sample Without Order";
				}
				else if ($row[csf("booking_without_order")]==1) {
					$booking_typeArr[$row[csf("booking_no")]]="Sample Without Order";
				}
				else {
					$booking_typeArr[$row[csf("booking_no")]]=$booking_type_arr[$row[csf("entry_form")]];
				}
			}


		}
		unset($batch_result);
		$bookingNos=chop($bookingNos,",");
		if($bookingNos!="")
		{
			//booking_chunk
			$all_booking_arr = array_unique(explode(",",$bookingNos));
			$all_booking_arr = array_filter($all_booking_arr);
			$all_booking_ids=implode(",",$all_booking_arr);
			$all_booking_cond=""; $bookingCond="";
			if($db_type==2 && count($all_booking_arr)>999)
			{
				$all_booking_arr_chunk=array_chunk($all_booking_arr,999) ;
				foreach($all_booking_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$bookingCond.="  a.booking_no in($chunk_arr_value) or ";
				}

				$all_booking_cond.=" and (".chop($bookingCond,'or ').")";
			}
			else
			{
				$all_booking_cond=" and a.booking_no in($bookingNos)";
			}

			$bookingInfoNonOrd=sql_select("select a.booking_no,a.buyer_id from wo_non_ord_samp_booking_mst a where a.status_active=1 and a.is_deleted=0 $all_booking_cond");
			foreach($bookingInfoNonOrd as $row)
			{
				$nonOrdbookingArr[$row[csf("booking_no")]]["buyer_id"]=$row[csf("buyer_id")];
			}
		}
		$all_ord_trans_id_arr=array();
		
		if($txt_order_no!="" || $cbo_buyer_id > 0)
		{
			$job_cond="";
			if($txt_order_no!="") $job_cond .=" and b.po_number='$txt_order_no'";
			if($cbo_buyer_id > 0) $job_cond .=" and c.buyer_name='$cbo_buyer_id'";
			$ord_sql="select a.trans_id, b.id as po_id, b.po_number, c.job_no, to_char(c.insert_date,'YYYY') as job_year, c.buyer_name, c.season_buyer_wise, c.style_ref_no, c.client_id, b.grouping 
			from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c
			where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.entry_form in(37,52,14,18,46) $job_cond";
			$ord_result=sql_select($ord_sql);

			if(empty($ord_result)) 
			{
				echo "Data Not Found";die;
			}
			
			$job_trans_data=array();
			if($rpt_type==3)
			{
				foreach($ord_result as $row)
				{
					if(!$all_trans_id_arr[$row[csf("trans_id")]])
					{
						$all_trans_id_arr[$row[csf("trans_id")]] = $row[csf("trans_id")];
					}

					$all_ord_trans_id_arr[$row[csf("trans_id")]]=$row[csf("trans_id")];
					$job_trans_data[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
					$job_trans_data[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
					$job_trans_data[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
					$job_trans_data[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
					$job_trans_data[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
					$job_trans_data[$row[csf("po_id")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
					$job_trans_data[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$job_trans_data[$row[csf("po_id")]]["client_id"]=$row[csf("client_id")];
					$job_trans_data[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
				}
			}
			else
			{
				foreach($ord_result as $row)
				{
					if(!$all_trans_id_arr[$row[csf("trans_id")]])
					{
						$all_trans_id_arr[$row[csf("trans_id")]] = $row[csf("trans_id")];
					}
					$all_ord_trans_id_arr[$row[csf("trans_id")]]=$row[csf("trans_id")];
					if($ord_check[$row[csf("trans_id")]][$row[csf("po_id")]]=="")
					{
						$ord_check[$row[csf("trans_id")]][$row[csf("po_id")]]=$row[csf("po_id")];
						$job_trans_data[$row[csf("trans_id")]]["po_id"].=$row[csf("po_id")].",";
					}
					$job_trans_data[$row[csf("trans_id")]]["job_no"]=$row[csf("job_no")];
					$job_trans_data[$row[csf("trans_id")]]["job_year"]=$row[csf("job_year")];
					$job_trans_data[$row[csf("trans_id")]]["buyer_name"]=$row[csf("buyer_name")];
					$job_trans_data[$row[csf("trans_id")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
					$job_trans_data[$row[csf("trans_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$job_trans_data[$row[csf("trans_id")]]["client_id"]=$row[csf("client_id")];
					$job_trans_data[$row[csf("trans_id")]]["grouping"]=$row[csf("grouping")];
				}
			}
		}
		else
		{
			$ord_sql="select a.trans_id, b.id as po_id, b.po_number, c.job_no, to_char(c.insert_date,'YYYY') as job_year, c.buyer_name, c.season_buyer_wise, c.style_ref_no, c.client_id, b.grouping 
			from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c
			where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.entry_form in(37,52,14,18,46) and c.company_name in($cbo_company_id)";
			//echo $ord_sql;die;
			$ord_result=sql_select($ord_sql);
			$job_trans_data=array();
			if($rpt_type==3)
			{
				foreach($ord_result as $row)
				{
					$job_trans_data[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
					$job_trans_data[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
					$job_trans_data[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
					$job_trans_data[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
					$job_trans_data[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
					$job_trans_data[$row[csf("po_id")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
					$job_trans_data[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$job_trans_data[$row[csf("po_id")]]["client_id"]=$row[csf("client_id")];
					$job_trans_data[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
				}
			}
			else
			{
				foreach($ord_result as $row)
				{
					if($ord_check[$row[csf("trans_id")]][$row[csf("po_id")]]=="")
					{
						$ord_check[$row[csf("trans_id")]][$row[csf("po_id")]]=$row[csf("po_id")];
						$job_trans_data[$row[csf("trans_id")]]["po_id"].=$row[csf("po_id")].",";
					}
					$job_trans_data[$row[csf("trans_id")]]["job_no"]=$row[csf("job_no")];
					$job_trans_data[$row[csf("trans_id")]]["job_year"]=$row[csf("job_year")];
					$job_trans_data[$row[csf("trans_id")]]["buyer_name"]=$row[csf("buyer_name")];
					$job_trans_data[$row[csf("trans_id")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
					$job_trans_data[$row[csf("trans_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$job_trans_data[$row[csf("trans_id")]]["client_id"]=$row[csf("client_id")];
					$job_trans_data[$row[csf("trans_id")]]["grouping"]=$row[csf("grouping")];
				}
			}
		}
		//echo "<pre>";print_r($job_trans_data);die;

		$con = connect();
		execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=147");
		oci_commit($con);
		$con = connect();
		if (!empty($all_trans_id_arr) && $user_id>0) 
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 147, 1,$all_trans_id_arr, $empty_arr);
		}
		/*if (!empty($program_arr) && $user_id>0) 
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 147, 2,$program_arr, $empty_arr);
		}*/
		oci_commit($con);


		$sql_cond="";
		if($cbo_store_id != "") $sql_cond.=" and b.store_id in($cbo_store_id)";
		if($txt_pi_no != "") $sql_cond.=" and a.booking_no='$txt_pi_no' and a.receive_basis=1";
		if(count($all_batch_id_arr)>0) $sql_cond.=" and b.pi_wo_batch_no in(".implode(",",$all_batch_id_arr).")";
		//if(count($all_ord_trans_id_arr)>0) $sql_cond.=" and b.id in(".implode(",",$all_ord_trans_id_arr).")";
		/*if(count($all_ord_trans_id_arr)>0)
		{
			$all_ord_trans_id_arr=implode(",",array_filter(array_unique($all_ord_trans_id_arr)));
			if($all_ord_trans_id_arr!="")
			{
				$all_ord_trans_id_arr=explode(",",$all_ord_trans_id_arr);  
				$all_ord_trans_id_arr_chnk=array_chunk($all_ord_trans_id_arr,999);
				$sql_cond=" and";
				foreach($all_ord_trans_id_arr_chnk as $dtls_id)
				{
				if($sql_cond==" and")  $sql_cond.="(b.id in(".implode(',',$dtls_id).")"; else $sql_cond.=" or b.id in(".implode(',',$dtls_id).")";
				}
				$sql_cond.=")";
				//echo $sql_cond;die;
			}
			//$sql_cond.=" and b.id in(".implode(",",$all_ord_trans_id_arr).")";
		}*/
		if( $txt_date_from !="" && $txt_date_to !="")
		{
			if($cbo_based_on==1) $sql_cond.= " and b.transaction_date between '$txt_date_from' and '$txt_date_to'"; else $sql_cond.= " and b.insert_date between '$txt_date_from' and '$txt_date_to'";
		}
		$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		$user_arr=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  );
		$store_arr=return_library_array( "select id,store_name from lib_store_location", "id", "store_name"  );
		$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
		
		$composition_arr=array(); $constructtion_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array_deter=sql_select($sql_deter);
		foreach( $data_array_deter as $row )
		{
			$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
			$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
		}
		unset($data_array_deter);
		
		$rcv_sup_sql="select a.knitting_source, a.knitting_company, b.pi_wo_batch_no as batch_id,d.remarks 
		from inv_receive_master a, pro_finish_fabric_rcv_dtls d, inv_transaction b
		where  a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and a.entry_form in(37) and b.transaction_type in(1) and b.item_category=2 and a.company_id in($cbo_company_id) and b.company_id in($cbo_company_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$rcv_sup_data=sql_select($rcv_sup_sql);
		$rcv_supp_arr=array();
		foreach($rcv_sup_data as $row)
		{
			if($row[csf("knitting_source")]==1) $supplier_name=$company_arr[$row[csf("knitting_company")]]; else $supplier_name=$supplier_arr[$row[csf("knitting_company")]];
			$rcv_supp_arr[$row[csf("batch_id")]]=$supplier_name;
			$remarks_arr[$row[csf("batch_id")]]=$row[csf("remarks")];

		}
		unset($rcv_sup_data);
	}
	else
	{
		$sql_cond="";
		//if($cbo_buyer_id > 0) $sql_cond=" and a.buyer_name=$cbo_buyer_id";
		$all_batch_id_arr=array();
		if($txt_booking_no !="" || $txt_job_no != "" )
		{
			if($txt_booking_no !="")
			{
				if($cbo_year)
				{
					if($db_type==0)
					{
						if($cbo_year==0) $year_cond=""; else $year_cond="and YEAR(b.insert_date)=$cbo_year";
					}
					else if($db_type==2)
					{
						if($cbo_year==0) $year_cond=""; else $year_cond=" and to_char(b.insert_date,'YYYY')=$cbo_year";
					}
				}
				$batch_sql="select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short, b.short_booking_type,a.booking_without_order,b.booking_type from pro_batch_create_mst a, wo_booking_mst b where a.booking_no=b.booking_no and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.company_id in ($cbo_company_id) and a.booking_no like '%$txt_booking_no%' $year_cond";
			}
			else if($txt_job_no !="")
			{
				if($cbo_year)
				{
					if($db_type==0)
					{
						if($cbo_year==0) $year_cond=""; else $year_cond="and YEAR(d.insert_date)=$cbo_year";
					}
					else if($db_type==2)
					{
						if($cbo_year==0) $year_cond=""; else $year_cond=" and to_char(d.insert_date,'YYYY')=$cbo_year";
					}
				}
				$batch_sql="select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short, b.short_booking_type,a.booking_without_order from pro_batch_create_mst a, wo_booking_mst b, wo_booking_dtls c, wo_po_details_master d
				where a.booking_no=b.booking_no and b.booking_no=c.booking_no and a.booking_no=c.booking_no and c.job_no=d.job_no  and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.company_id in ($cbo_company_id) $year_cond and c.job_no like '%$txt_job_no%'";
				/*$batch_sql="select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short, b.short_booking_type,a.booking_without_order from pro_batch_create_mst a, wo_booking_dtls b, wo_booking_dtls c
				where a.booking_no=b.booking_no and b.booking_no=c.booking_no and a.booking_no=c.booking_no and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.job_no='$txt_job_no'";*/
			}
			$batch_result=sql_select($batch_sql);

			if(empty($batch_result)) 
			{
				echo "Data Not Found";die;
			}

			$batch_data=array();
			foreach($batch_result as $row)
			{
				$all_batch_id_arr[$row[csf("id")]]=$row[csf("id")];
				$batch_data[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
				$batch_data[$row[csf("id")]]["batch_date"]=$row[csf("batch_date")];
				$batch_data[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
				$batch_data[$row[csf("id")]]["is_short"]=$row[csf("is_short")];
				$batch_data[$row[csf("id")]]["short_booking_type"]=$row[csf("short_booking_type")];
				$batch_data[$row[csf("id")]]["booking_without_order"]=$row[csf("booking_without_order")];
				$batch_data[$row[csf("id")]]["booking_type"]=$row[csf("booking_type")];
			}
			
		}
		else
		{
			$batch_sql="select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short, b.short_booking_type,0 as booking_without_order,b.booking_type,b.entry_form  from pro_batch_create_mst a, wo_booking_mst b where a.booking_no=b.booking_no and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.company_id in($cbo_company_id) 
			union all 
			select a.id, a.batch_no, a.batch_date, a.booking_no, b.is_short,null as short_booking_type,1 as booking_without_order,b.booking_type,b.entry_form_id as entry_form  
			from pro_batch_create_mst a, wo_non_ord_samp_booking_mst b 
			where a.booking_no=b.booking_no and b.booking_type=4 and a.status_active=1 and a.is_deleted=0 and b.company_id in($cbo_company_id) ";

			$batch_result=sql_select($batch_sql);
			$batch_data=array();
			$bookingNos="";$booking_typeArr=array();
			foreach($batch_result as $row)
			{
				if($row[csf("booking_without_order")]==1)
				{
					$bookingNos.="'".$row[csf("booking_no")]."',";
				}
				$batch_data[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
				$batch_data[$row[csf("id")]]["batch_date"]=$row[csf("batch_date")];
				$batch_data[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
				$batch_data[$row[csf("id")]]["is_short"]=$row[csf("is_short")];
				$batch_data[$row[csf("id")]]["short_booking_type"]=$row[csf("short_booking_type")];
				$batch_data[$row[csf("id")]]["booking_without_order"]=$row[csf("booking_without_order")];
				$batch_data[$row[csf("id")]]["booking_type"]=$row[csf("booking_type")];

				$bookingType="";
				if($row[csf('booking_type')] == 4 && $row[csf("booking_without_order")]==0)
				{
					$booking_typeArr[$row[csf("booking_no")]]="Sample Without Order";
				}
				else if ($row[csf("booking_without_order")]==1) {
					$booking_typeArr[$row[csf("booking_no")]]="Sample Without Order";
				}
				else {
					$booking_typeArr[$row[csf("booking_no")]]=$booking_type_arr[$row[csf("entry_form")]];
				}
			}


		}
		unset($batch_result);
		$bookingNos=chop($bookingNos,",");
		if($bookingNos!="")
		{
			//booking_chunk
			$all_booking_arr = array_unique(explode(",",$bookingNos));
			$all_booking_arr = array_filter($all_booking_arr);
			$all_booking_ids=implode(",",$all_booking_arr);
			$all_booking_cond=""; $bookingCond="";
			if($db_type==2 && count($all_booking_arr)>999)
			{
				$all_booking_arr_chunk=array_chunk($all_booking_arr,999) ;
				foreach($all_booking_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$bookingCond.="  a.booking_no in($chunk_arr_value) or ";
				}

				$all_booking_cond.=" and (".chop($bookingCond,'or ').")";
			}
			else
			{
				$all_booking_cond=" and a.booking_no in($bookingNos)";
			}

			$bookingInfoNonOrd=sql_select("select a.booking_no,a.buyer_id from wo_non_ord_samp_booking_mst a where a.status_active=1 and a.is_deleted=0 $all_booking_cond");
			foreach($bookingInfoNonOrd as $row)
			{
				$nonOrdbookingArr[$row[csf("booking_no")]]["buyer_id"]=$row[csf("buyer_id")];
			}
		}
		$all_ord_trans_id_arr=array();
		
		if($txt_order_no!="" || $cbo_buyer_id > 0)
		{
			$job_cond="";
			if($txt_order_no!="") $job_cond .=" and b.po_number='$txt_order_no'";
			if($cbo_buyer_id > 0) $job_cond .=" and c.buyer_name='$cbo_buyer_id'";
			$ord_sql="select a.trans_id, b.id as po_id, b.po_number, c.job_no, to_char(c.insert_date,'YYYY') as job_year, c.buyer_name, c.season_buyer_wise, c.style_ref_no, c.client_id, b.grouping 
			from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c
			where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.entry_form in(37,52,14,18,46) $job_cond";
			$ord_result=sql_select($ord_sql);

			if(empty($ord_result)) 
			{
				echo "Data Not Found";die;
			}
			
			$job_trans_data=array();
			if($rpt_type==3)
			{
				foreach($ord_result as $row)
				{

					if(!$all_trans_id_arr[$row[csf("trans_id")]])
					{
						$all_trans_id_arr[$row[csf("trans_id")]] = $row[csf("trans_id")];
					}

					$all_ord_trans_id_arr[$row[csf("trans_id")]]=$row[csf("trans_id")];
					$job_trans_data[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
					$job_trans_data[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
					$job_trans_data[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
					$job_trans_data[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
					$job_trans_data[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
					$job_trans_data[$row[csf("po_id")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
					$job_trans_data[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$job_trans_data[$row[csf("po_id")]]["client_id"]=$row[csf("client_id")];
					$job_trans_data[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
				}
			}
			else
			{
				foreach($ord_result as $row)
				{

					if(!$all_trans_id_arr[$row[csf("trans_id")]])
					{
						$all_trans_id_arr[$row[csf("trans_id")]] = $row[csf("trans_id")];
					}
				
					$all_ord_trans_id_arr[$row[csf("trans_id")]]=$row[csf("trans_id")];
					if($ord_check[$row[csf("trans_id")]][$row[csf("po_id")]]=="")
					{
						$ord_check[$row[csf("trans_id")]][$row[csf("po_id")]]=$row[csf("po_id")];
						$job_trans_data[$row[csf("trans_id")]]["po_id"].=$row[csf("po_id")].",";
					}
					$job_trans_data[$row[csf("trans_id")]]["job_no"]=$row[csf("job_no")];
					$job_trans_data[$row[csf("trans_id")]]["job_year"]=$row[csf("job_year")];
					$job_trans_data[$row[csf("trans_id")]]["buyer_name"]=$row[csf("buyer_name")];
					$job_trans_data[$row[csf("trans_id")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
					$job_trans_data[$row[csf("trans_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$job_trans_data[$row[csf("trans_id")]]["client_id"]=$row[csf("client_id")];
					$job_trans_data[$row[csf("trans_id")]]["grouping"]=$row[csf("grouping")];
				}
			}
		}
		else
		{
			$ord_sql="select a.trans_id, b.id as po_id, b.po_number, c.job_no, to_char(c.insert_date,'YYYY') as job_year, c.buyer_name, c.season_buyer_wise, c.style_ref_no, c.client_id, b.grouping 
			from order_wise_pro_details a, wo_po_break_down b, wo_po_details_master c
			where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0 and a.entry_form in(17,19,202,209,258) and c.company_name in($cbo_company_id)";
			//echo $ord_sql;die;
			$ord_result=sql_select($ord_sql);
			$job_trans_data=array();
			if($rpt_type==3)
			{
				foreach($ord_result as $row)
				{
					$job_trans_data[$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
					$job_trans_data[$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
					$job_trans_data[$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
					$job_trans_data[$row[csf("po_id")]]["job_year"]=$row[csf("job_year")];
					$job_trans_data[$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
					$job_trans_data[$row[csf("po_id")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
					$job_trans_data[$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$job_trans_data[$row[csf("po_id")]]["client_id"]=$row[csf("client_id")];
					$job_trans_data[$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
				}
			}
			else
			{
				foreach($ord_result as $row)
				{
					if($ord_check[$row[csf("trans_id")]][$row[csf("po_id")]]=="")
					{
						$ord_check[$row[csf("trans_id")]][$row[csf("po_id")]]=$row[csf("po_id")];
						$job_trans_data[$row[csf("trans_id")]]["po_id"].=$row[csf("po_id")].",";
					}
					$job_trans_data[$row[csf("trans_id")]]["job_no"]=$row[csf("job_no")];
					$job_trans_data[$row[csf("trans_id")]]["job_year"]=$row[csf("job_year")];
					$job_trans_data[$row[csf("trans_id")]]["buyer_name"]=$row[csf("buyer_name")];
					$job_trans_data[$row[csf("trans_id")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
					$job_trans_data[$row[csf("trans_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					$job_trans_data[$row[csf("trans_id")]]["client_id"]=$row[csf("client_id")];
					$job_trans_data[$row[csf("trans_id")]]["grouping"]=$row[csf("grouping")];
				}
			}
		}
		//echo "<pre>";print_r($job_trans_data);die;

		$con = connect();
		execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=147");
		oci_commit($con);
		$con = connect();
		if (!empty($all_trans_id_arr) && $user_id>0) 
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 147, 1,$all_trans_id_arr, $empty_arr);
		}
		/*if (!empty($program_arr) && $user_id>0) 
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 147, 2,$program_arr, $empty_arr);
		}*/
		oci_commit($con);
		if($txt_order_no!="" || $cbo_buyer_id > 0)
		{
			$tmpTable=",GBL_TEMP_ENGINE e";
			$whereCondition="and b.id=e.ref_val and e.user_id=$user_id and e.entry_form=147 and e.ref_from=1";
		}
		else
		{
			$tmpTable="";
			$whereCondition="";
		}



		$sql_cond="";$sql_cond2="";
		if($cbo_store_id != "") $sql_cond.=" and b.store_id in($cbo_store_id)";
		if($cbo_store_id != "") $sql_cond2.=" and b.store_id in($cbo_store_id)";
		if($txt_pi_no != "") $sql_cond.=" and a.booking_no='$txt_pi_no' and a.receive_basis=1";
		if($txt_pi_no != "") $sql_cond2.=" and a.booking_no='$txt_pi_no' and a.receive_basis=1";
		if(count($all_batch_id_arr)>0) $sql_cond.=" and b.pi_wo_batch_no in(".implode(",",$all_batch_id_arr).")";
		if(count($all_batch_id_arr)>0) $sql_cond2.=" and b.batch_id in(".implode(",",$all_batch_id_arr).")";
		//if(count($all_ord_trans_id_arr)>0) $sql_cond.=" and b.id in(".implode(",",$all_ord_trans_id_arr).")";
		/*if(count($all_ord_trans_id_arr)>0)
		{
			$all_ord_trans_id_arr=implode(",",array_filter(array_unique($all_ord_trans_id_arr)));
			if($all_ord_trans_id_arr!="")
			{
				$all_ord_trans_id_arr=explode(",",$all_ord_trans_id_arr);  
				$all_ord_trans_id_arr_chnk=array_chunk($all_ord_trans_id_arr,999);
				$sql_cond=" and";
				foreach($all_ord_trans_id_arr_chnk as $dtls_id)
				{
				if($sql_cond==" and")  $sql_cond.="(b.id in(".implode(',',$dtls_id).")"; else $sql_cond.=" or b.id in(".implode(',',$dtls_id).")";
				}
				$sql_cond.=")";
				//echo $sql_cond;die;
			}
			//$sql_cond.=" and b.id in(".implode(",",$all_ord_trans_id_arr).")";
		}*/
		if( $txt_date_from !="" && $txt_date_to !="")
		{
			if($cbo_based_on==1) $sql_cond.= " and b.transaction_date between '$txt_date_from' and '$txt_date_to'"; else $sql_cond.= " and b.insert_date between '$txt_date_from' and '$txt_date_to'";

			if($cbo_based_on==1) $sql_cond2.= " and b.transaction_date between '$txt_date_from' and '$txt_date_to'"; else $sql_cond2.= " and b.insert_date between '$txt_date_from' and '$txt_date_to'";
		}
		$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
		$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
		$user_arr=return_library_array( "select id,user_name from user_passwd", "id", "user_name"  );
		$store_arr=return_library_array( "select id,store_name from lib_store_location", "id", "store_name"  );
		$season_arr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name"  );
		
		$composition_arr=array(); $constructtion_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array_deter=sql_select($sql_deter);
		foreach( $data_array_deter as $row )
		{
			$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
			$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
		}
		unset($data_array_deter);
		
		$rcv_sup_sql="select a.knitting_source, a.knitting_company, b.pi_wo_batch_no as batch_id,d.remarks 
		from inv_receive_master a, pro_finish_fabric_rcv_dtls d, inv_transaction b
		where  a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and a.entry_form in(37) and b.transaction_type in(1) and b.item_category=2 and a.company_id in($cbo_company_id) and b.company_id in($cbo_company_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$rcv_sup_data=sql_select($rcv_sup_sql);
		$rcv_supp_arr=array();
		foreach($rcv_sup_data as $row)
		{
			if($row[csf("knitting_source")]==1) $supplier_name=$company_arr[$row[csf("knitting_company")]]; else $supplier_name=$supplier_arr[$row[csf("knitting_company")]];
			$rcv_supp_arr[$row[csf("batch_id")]]=$supplier_name;
			$remarks_arr[$row[csf("batch_id")]]=$row[csf("remarks")];

		}
		unset($rcv_sup_data);
	
	}
	
	if($rpt_type==1) // receive button
	{

		if($cbo_category_type==1) // knit finish recv
		{

			$trans_batch_sql="select a.id, b.batch_id,b.remarks 
			from inv_transaction a, inv_item_transfer_dtls b
			where a.mst_id=b.mst_id and a.prod_id=b.to_prod_id and a.transaction_type in(5) and a.item_category=2 and a.company_id in($cbo_company_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			//echo $trans_batch_sql;die;
			$trans_batch_data=sql_select($trans_batch_sql);
			$trans_batch_arr=array();
			foreach($trans_batch_data as $row)
			{
				$trans_batch_arr[$row[csf("id")]]=$row[csf("batch_id")];
				$remarks_arr[$row[csf("id")]]=$row[csf("remarks")];
			}
			unset($trans_batch_data);
			
			//echo "<pre>";print_r($rcv_supp_arr);die;
			
			$sql_rcv_issue="select a.id as mst_id, a.entry_form, a.recv_number as trans_number, a.receive_basis, a.booking_id as wo_pi_prod_id, a.booking_no as wo_pi_prod_no, a.knitting_source, a.knitting_company, a.challan_no, b.id as trans_id, b.pi_wo_batch_no as batch_id, b.prod_id, b.transaction_type, b.transaction_date, b.store_id, b.body_part_id, b.fabric_shade, c.detarmination_id as fabric_description_id, c.gsm, c.dia_width as width, c.color as color_id, b.remarks, b.cons_uom, b.cons_quantity, b.cons_rate, b.cons_amount,b.order_rate,b.order_amount, b.inserted_by, b.insert_date, a.lc_sc_no,a.booking_without_order,a.issue_id   
			from inv_receive_master a, inv_transaction b, product_details_master c $tmpTable 
			where a.id=b.mst_id and b.prod_id=c.id $whereCondition and a.entry_form in(37,52) and b.item_category=2 and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in($cbo_company_id) and b.company_id in($cbo_company_id) $sql_cond
			union all
			select a.id as mst_id, a.entry_form, a.transfer_system_id  as trans_number, 0 as receive_basis, 0 as wo_pi_prod_id, null as wo_pi_prod_no, 0 as knitting_source, 0 as knitting_company, a.challan_no, b.id as trans_id, b.pi_wo_batch_no as batch_id, b.prod_id, b.transaction_type, b.transaction_date, b.store_id, b.body_part_id, b.fabric_shade, c.detarmination_id as fabric_description_id, c.gsm, c.dia_width as width, c.color as color_id, b.remarks, b.cons_uom, b.cons_quantity, b.cons_rate, b.cons_amount,b.order_rate,b.order_amount, b.inserted_by, b.insert_date, null as lc_sc_no,null as booking_without_order,null as issue_id    
			from inv_item_transfer_mst a, inv_transaction b, product_details_master c $tmpTable 
			where a.id=b.mst_id and b.prod_id=c.id $whereCondition and a.entry_form in(14,306) and b.item_category=2 and b.transaction_type in(5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in($cbo_company_id) and b.company_id in($cbo_company_id) $sql_cond
			order by trans_id";
			//echo $sql_rcv_issue;die;
			$dtls_data=sql_select($sql_rcv_issue);
			$issue_ids="";$issue_rtn_prod_ids="";$issue_rtn_batch_ids="";
			foreach ($dtls_data as $row)
			{
				if ($row[csf("transaction_type")]==4) 
				{
					$issue_ids.=$row[csf("issue_id")].",";
					$issue_rtn_prod_ids.=$row[csf("prod_id")].",";
					$issue_rtn_batch_ids.=$row[csf("batch_id")].",";
				}
			}
			$issue_ids=chop($issue_ids,",");
			$issue_rtn_prod_ids=chop($issue_rtn_prod_ids,",");
			$issue_rtn_batch_ids=chop($issue_rtn_batch_ids,",");
			$sql_issue_rate_amnt=sql_select("select pi_wo_batch_no,prod_id,mst_id,order_rate,order_amount from inv_transaction where pi_wo_batch_no in($issue_rtn_batch_ids) and prod_id in($issue_rtn_prod_ids) and transaction_type=1 and status_active=1 and is_deleted=0");
			foreach ($sql_issue_rate_amnt as $row)
			{
				$issue_rate_amnt_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_rate"]=$row[csf("order_rate")];
				$issue_rate_amnt_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_amount"]=$row[csf("order_amount")];
			}
			//print_r($issue_rate_amnt_arr);
			ob_start();
			?>
			<fieldset style="width:4060px;">
				<table cellpadding="0" cellspacing="0" width="4050">
					<tr class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="43" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<!--<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="42" style="font-size:16px"><strong><?// echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <?// if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
					</tr>-->
				</table>
				<table width="4050" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
					<thead>
						<tr>
							<th width="30" rowspan="2">SL</th><!--730 == 9-->
							<th width="50" rowspan="2">Prod. Id</th>
							<th width="70" rowspan="2">Trans. Date</th>
							<th width="110" rowspan="2">Trans. Ref.</th>
							<th width="80" rowspan="2">Challan No</th>
							<th width="120" rowspan="2">Receive Basis</th> 
							<th width="100" rowspan="2">WO/PI/Production No</th>
							<th width="100" rowspan="2">LC Number</th>
							<th width="110" rowspan="2">Store Name</th>
							
							<th width="50" rowspan="2">Year</th><!--1010 == 11-->
							<th width="100" rowspan="2">Buyer</th>
							<th width="100" rowspan="2">Buyer Client</th>
							<th width="100" rowspan="2">Style No</th>
							<th width="80" rowspan="2">Season</th>
							<th width="100" rowspan="2">Job</th>
							<th width="80" rowspan="2">PO Number</th>
							<th width="100" rowspan="2">Fabric Booking</th>
							<th width="70" rowspan="2">Booking Type</th>
							<th width="80" rowspan="2">Short Booking Type</th>
							<th width="120" rowspan="2">Supplier Name</th>
							
							<th width="135" rowspan="2">Body Part</th><!--730 == 8-->
							<th width="100" rowspan="2">Construction</th>
							<th width="150" rowspan="2">Composition</th>
							<th width="100" rowspan="2">Internal Ref.</th>
							<th width="60" rowspan="2">GSM</th>
							<th width="60" rowspan="2">Dia</th>
							<th width="100" rowspan="2">Batch No</th>
							<th width="80" rowspan="2">Color</th>
							<th width="80" rowspan="2">Shade</th>
							
							<th width="240" colspan="3">Kg</th><!--720 == 9-->
							<th width="240" colspan="3">Yds</th>
							<th width="240" colspan="3">Mtr</th>
							
							<th width="80" rowspan="2">Rate(Taka)</th> <!--530 == 5-->
							<th width="100" rowspan="2">Amount(Taka)</th>
							<th width="100" rowspan="2">Rate (USD)</th>
							<th width="100" rowspan="2">Amount (USD)</th>
							<th width="80" rowspan="2">User</th>
							<th width="120" rowspan="2">Insert Date & Time</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="80">Receive Qty</th>
							<th width="80">Issue Return Qty</th>
							<th width="80">Trans. In Qty</th>
							<th width="80">Receive Qty</th>
							<th width="80">Issue Return Qty</th>
							<th width="80">Trans. In Qty</th>
							<th width="80">Receive Qty</th>
							<th width="80">Issue Return Qty</th>
							<th width="80">Trans. In Qty</th>
							
						</tr>
					</thead>
				</table>
				<div style="width:4050px; max-height:350px; overflow-y:scroll;" id="scroll_body">
					<table width="4030" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
						<?
						$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$issue_trns_out_qnty=0;$rec_trns_in_qnty=0;
						//$fin_color_array=array(); $fin_color_data_arr=array();$checkbuyerArr=array(); $po_break_id_arr=array(); $booking_po_data_arr=array();
						//print_r($po_break_id_arr);
						
						
						//print_r($booking_po_data_arr);
						foreach ($dtls_data as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$rcv_qnty_kg=$issue_rtn_kg=$transfe_in_kg=$rcv_qnty_yds=$issue_rtn_yds=$transfe_in_yds=$rcv_qnty_mtr=$issue_rtn_mtr=$transfe_in_mtr=0;
							if($row[csf("cons_uom")]==12)
							{
								if($row[csf("transaction_type")]==1)
								{
									$rcv_qnty_kg=$row[csf("cons_quantity")];
									$total_rcv_qnty_kg+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==4)
								{
									$issue_rtn_kg=$row[csf("cons_quantity")];
									$total_issue_rtn_kg+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==5)
								{
									$transfe_in_kg=$row[csf("cons_quantity")];
									$total_transfe_in_kg+=$row[csf("cons_quantity")];
								}
							}
							elseif($row[csf("cons_uom")]==27)
							{
								if($row[csf("transaction_type")]==1)
								{
									$rcv_qnty_yds=$row[csf("cons_quantity")];
									$total_rcv_qnty_yds+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==4)
								{
									$issue_rtn_yds=$row[csf("cons_quantity")];
									$total_issue_rtn_yds+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==5)
								{
									$transfe_in_yds=$row[csf("cons_quantity")];
									$total_transfe_in_yds+=$row[csf("cons_quantity")];
								}
							}
							elseif($row[csf("cons_uom")]==23)
							{
								if($row[csf("transaction_type")]==1)
								{
									$rcv_qnty_mtr=$row[csf("cons_quantity")];
									$total_rcv_qnty_mtr+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==4)
								{
									$issue_rtn_mtr=$row[csf("cons_quantity")];
									$total_issue_rtn_mtr+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==5)
								{
									$transfe_in_mtr=$row[csf("cons_quantity")];
									$total_transfe_in_mtr+=$row[csf("cons_quantity")];
								}
							}
							$supplier_name="";
							if($row[csf("transaction_type")]==1)
							{
								if($row[csf("knitting_source")]==1) $supplier_name=$company_arr[$row[csf("knitting_company")]]; else $supplier_name=$supplier_arr[$row[csf("knitting_company")]];
									$remarks=$remarks_arr[$row[csf("batch_id")]];
								//$batch_wise_supplier[$row[csf("batch_id")]]=$supplier_name;
							}
							else
							{
								if($row[csf("transaction_type")]==4)
								{
									$supplier_name=$rcv_supp_arr[$row[csf("batch_id")]];
									$remarks=$remarks_arr[$row[csf("batch_id")]];
								}
								else
								{
									$supplier_name=$rcv_supp_arr[$trans_batch_arr[$row[csf("trans_id")]]];
									$remarks=$remarks_arr[$row[csf("trans_id")]];

								}
							}
							
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30" align="center" title="<? echo $row[csf("trans_id")]; ?>"><? echo $i; ?></td>
								<td width="50" align="center"><? echo $row[csf("prod_id")]; ?></td>
								<td width="70" align="center"><p><? echo change_date_format($row[csf("transaction_date")]); ?>&nbsp;</p></td>
								<td width="110"><p><? echo $row[csf("trans_number")]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
								<td width="120"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?>&nbsp;</p></td> 
								<td width="100"><p><? echo $row[csf("wo_pi_prod_no")]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $row[csf("lc_sc_no")]; ?>&nbsp;</p></td>
								<td width="110"><p><? echo $store_arr[$row[csf("store_id")]]; ?>&nbsp;</p></td>
								
								<td width="50" align="center"><p><? echo $job_trans_data[$row[csf("trans_id")]]["job_year"]; ?>&nbsp;</p></td>
								<td width="100"><p><? 
									
									if($row[csf("booking_without_order")]==1 || $batch_data[$row[csf("batch_id")]]["booking_without_order"]==1)
									{
										echo $buyer_arr[$nonOrdbookingArr[$batch_data[$row[csf("batch_id")]]["booking_no"]]["buyer_id"]]; 
									}
									else
									{
										echo $buyer_arr[$job_trans_data[$row[csf("trans_id")]]["buyer_name"]]; 
									} 
									?>&nbsp;</p></td>
								<td width="100"><p><? echo $buyer_arr[$job_trans_data[$row[csf("trans_id")]]["client_id"]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $job_trans_data[$row[csf("trans_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $season_arr[$job_trans_data[$row[csf("trans_id")]]["season_buyer_wise"]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $job_trans_data[$row[csf("trans_id")]]["job_no"]; ?>&nbsp;</p></td>
								<td width="80" align="center"><a href="##" onClick="openmypage_po('<? echo chop($job_trans_data[$row[csf("trans_id")]]["po_id"],",");?>','po_popup')">View</a></td>
								<td width="100"><p><? echo $batch_data[$row[csf("batch_id")]]["booking_no"]; ?>&nbsp;</p></td>
								<td width="70" align="center"><p><?
									echo $booking_typeArr[$batch_data[$row[csf("batch_id")]]["booking_no"]];
								// echo $booking_type[$batch_data[$row[csf("batch_id")]]["booking_type"]]; //if($batch_data[$row[csf("batch_id")]]["is_short"]==1) echo "Short"; else echo "Main"; ?>&nbsp;</p></td>
								<td width="80"><p><? if($batch_data[$row[csf("batch_id")]]["is_short"]==1) echo $short_booking_type[$batch_data[$row[csf("batch_id")]]["short_booking_type"]];?>&nbsp;</p></td>
								<td width="120" title="<? echo $row[csf("batch_id")]."=".$trans_batch_arr[$row[csf("trans_id")]]; ?>"><p><? echo $supplier_name; ?>&nbsp;</p></td>
								
								<td width="135"><p><? echo $body_part[$row[csf("body_part_id")]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $constructtion_arr[$row[csf("fabric_description_id")]]; ?>&nbsp;</p></td>
								<td width="150"><p><? echo $composition_arr[$row[csf("fabric_description_id")]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $job_trans_data[$row[csf("trans_id")]]["grouping"]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("gsm")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("width")]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $batch_data[$row[csf("batch_id")]]["batch_no"]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $fabric_shade[$row[csf("fabric_shade")]]; ?>&nbsp;</p></td>
								<td width="80" align="right"><? echo number_format($rcv_qnty_kg,2); ?></td>
								<td width="80" align="right"><? echo number_format($issue_rtn_kg,2); ?></td>
								<td width="80" align="right"><? echo number_format($transfe_in_kg,2); ?></td>
								<td width="80" align="right"><? echo number_format($rcv_qnty_yds,2); ?></td>
								<td width="80" align="right"><? echo number_format($issue_rtn_yds,2); ?></td>
								<td width="80" align="right"><? echo number_format($transfe_in_yds,2); ?></td>
								<td width="80" align="right"><? echo number_format($rcv_qnty_mtr,2); ?></td>
								<td width="80" align="right"><? echo number_format($issue_rtn_mtr,2); ?></td>
								<td width="80" align="right"><? echo number_format($transfe_in_mtr,2); ?></td>
								
								<td width="80" align="right"><? echo number_format($row[csf("cons_rate")],2); ?></td>
								<td width="100" align="right"><? echo number_format($row[csf("cons_amount")],2); ?></td>
							
								<td width="100" align="right"><? 
									if($row[csf("transaction_type")]==4)
									{
										//echo number_format($issue_rate_amnt_arr[$row[csf("issue_id")]]["cons_rate"],2);
										echo number_format($issue_rate_amnt_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["order_rate"],2);
									}
									else if($row[csf("transaction_type")]==5)
									{
										echo number_format($row[csf("cons_rate")]/$txt_exchange_rate,2); 
									}
									else
									{
										echo number_format($row[csf("order_rate")],2); 
									}
								


								?></td>
								<td width="100" align="right"><? 
									if($row[csf("transaction_type")]==4)
									{
										//echo number_format($issue_rate_amnt_arr[$row[csf("issue_id")]]["cons_amount"],2);
										echo number_format($issue_rate_amnt_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["order_amount"],2);

									}
									else if($row[csf("transaction_type")]==5)
									{
										$excngRate=$row[csf("cons_rate")]/$txt_exchange_rate;

										$excngAmt=$excngRate*($transfe_in_mtr+$transfe_in_kg+$transfe_in_yds);
										//echo number_format($row[csf("cons_amount")]/$txt_exchange_rate,2); 
										echo number_format($excngAmt,2); 
									}
									else
									{
										echo number_format($row[csf("order_amount")],2); 
									}

								?></td>

								<td width="80" align="center"><p><? echo $user_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td>
								<td width="120"><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td>
								<td><p><? echo $remarks;//$row[csf("remarks")]; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;	
							$total_cons_amount+=$row[csf("cons_amount")]/$txt_exchange_rate;
							$total_order_amount+=$row[csf("order_amount")];
						}
						?>
						</table>
					<table width="4030" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" > 
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="110">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th> 
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="110">&nbsp;</th>
							
							<th width="50">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th>
							
							<th width="135">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="150">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80"></th>
							<th width="80">Total</th>
							
							<th width="80" align="right" id="value_total_rcv_qnty_kg"><? echo number_format($total_rcv_qnty_kg,2); ?></th>
							<th width="80" align="right" id="value_total_issue_rtn_kg"><? echo number_format($total_issue_rtn_kg,2); ?></th>
							<th width="80" align="right" id="value_total_transfe_in_kg"><? echo number_format($total_transfe_in_kg,2); ?></th>
							<th width="80" align="right" id="value_total_rcv_qnty_yds"><? echo number_format($total_rcv_qnty_yds,2); ?></th>
							<th width="80" align="right" id="value_total_issue_rtn_yds"><? echo number_format($total_issue_rtn_yds,2); ?></th>
							<th width="80" align="right" id="value_total_transfe_in_yds"><? echo number_format($total_transfe_in_yds,2); ?></th>
							<th width="80" align="right" id="value_total_rcv_qnty_mtr"><? echo number_format($total_rcv_qnty_mtr,2); ?></th>
							<th width="80" align="right" id="value_total_issue_rtn_mtr"><? echo number_format($total_issue_rtn_mtr,2); ?></th>
							<th width="80" align="right" id="value_total_transfe_in_mtr"><? echo number_format($total_transfe_in_mtr,2); ?></th> 
							<th width="80">&nbsp;</th>
							<th width="100" align="right" id="value_total_cons_amount"><? echo number_format($total_cons_amount,2); ?></th> 
							<th width="100">&nbsp;</th>
							<th width="100" align="right" id="value_total_order_amount"><? echo number_format($total_order_amount,2); ?></th> 
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th>&nbsp;</th>
						</tfoot>
					</table> 
				</div>  
			</fieldset>
			<?
		}
		else //woven recv
		{
			
			$trans_batch_sql="select a.id, b.batch_id,b.remarks 
			from inv_transaction a, inv_item_transfer_dtls b
			where a.mst_id=b.mst_id and a.prod_id=b.to_prod_id and a.transaction_type in(5) and a.item_category=3 and a.company_id in($cbo_company_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			//echo $trans_batch_sql;die;
			$trans_batch_data=sql_select($trans_batch_sql);
			$trans_batch_arr=array();
			foreach($trans_batch_data as $row)
			{
				$trans_batch_arr[$row[csf("id")]]=$row[csf("batch_id")];
				$remarks_arr[$row[csf("id")]]=$row[csf("remarks")];
			}
			unset($trans_batch_data);
			
			//echo "<pre>";print_r($rcv_supp_arr);die;
			
			$sql_rcv_issue="select a.id as mst_id, a.entry_form, a.recv_number as trans_number, a.receive_basis, a.booking_id as wo_pi_prod_id, a.booking_no as wo_pi_prod_no, a.knitting_source, a.knitting_company, a.challan_no, b.id as trans_id, to_number(b.batch_id) as batch_id, b.prod_id, b.transaction_type, b.transaction_date, b.store_id, b.body_part_id, b.fabric_shade, c.detarmination_id as fabric_description_id, c.gsm, c.dia_width as width, c.color as color_id, b.remarks, b.cons_uom, b.cons_quantity, b.cons_rate, b.cons_amount,b.order_rate,b.order_amount, b.inserted_by, b.insert_date, a.lc_sc_no,a.booking_without_order,a.issue_id   
			from inv_receive_master a, inv_transaction b, product_details_master c  
			where a.id=b.mst_id and b.prod_id=c.id and a.entry_form in(17,209) and b.item_category=3 and b.transaction_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in($cbo_company_id) and b.company_id in($cbo_company_id) $sql_cond2
			union all
			select a.id as mst_id, a.entry_form, a.transfer_system_id  as trans_number, 0 as receive_basis, 0 as wo_pi_prod_id, null as wo_pi_prod_no, 0 as knitting_source, 0 as knitting_company, a.challan_no, b.id as trans_id, b.pi_wo_batch_no as batch_id, b.prod_id, b.transaction_type, b.transaction_date, b.store_id, b.body_part_id, b.fabric_shade, c.detarmination_id as fabric_description_id, c.gsm, c.dia_width as width, c.color as color_id, b.remarks, b.cons_uom, b.cons_quantity, b.cons_rate, b.cons_amount,b.order_rate,b.order_amount, b.inserted_by, b.insert_date, null as lc_sc_no,null as booking_without_order,null as issue_id    
			from inv_item_transfer_mst a, inv_transaction b, product_details_master c $tmpTable   
			where a.id=b.mst_id and b.prod_id=c.id  $whereCondition and a.entry_form in(258) and b.item_category=3 and b.transaction_type in(5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in($cbo_company_id) and b.company_id in($cbo_company_id) $sql_cond
			order by trans_id";
			//echo $sql_rcv_issue;die;
			$dtls_data=sql_select($sql_rcv_issue);
			$issue_ids="";$issue_rtn_prod_ids="";$issue_rtn_batch_ids="";
			foreach ($dtls_data as $row)
			{
				if ($row[csf("transaction_type")]==4) 
				{
					$issue_ids.=$row[csf("issue_id")].",";
					$issue_rtn_prod_ids.=$row[csf("prod_id")].",";
					$issue_rtn_batch_ids.=$row[csf("batch_id")].",";
				}
			}
			$issue_ids=chop($issue_ids,",");
			$issue_rtn_prod_ids=chop($issue_rtn_prod_ids,",");
			$issue_rtn_batch_ids=chop($issue_rtn_batch_ids,",");
			$sql_issue_rate_amnt=sql_select("select pi_wo_batch_no,prod_id,mst_id,order_rate,order_amount from inv_transaction where pi_wo_batch_no in($issue_rtn_batch_ids) and prod_id in($issue_rtn_prod_ids) and transaction_type=1 and status_active=1 and is_deleted=0");
			foreach ($sql_issue_rate_amnt as $row)
			{
				$issue_rate_amnt_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_rate"]=$row[csf("order_rate")];
				$issue_rate_amnt_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_amount"]=$row[csf("order_amount")];
			}
			//print_r($issue_rate_amnt_arr);
			ob_start();
			?>
			<fieldset style="width:4060px;">
				<table cellpadding="0" cellspacing="0" width="4050">
					<tr class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="43" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<!--<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="42" style="font-size:16px"><strong><?// echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <?// if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
					</tr>-->
				</table>
				<table width="4050" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
					<thead>
						<tr>
							<th width="30" rowspan="2">SL</th><!--730 == 9-->
							<th width="50" rowspan="2">Prod. Id</th>
							<th width="70" rowspan="2">Trans. Date</th>
							<th width="110" rowspan="2">Trans. Ref.</th>
							<th width="80" rowspan="2">Challan No</th>
							<th width="120" rowspan="2">Receive Basis</th> 
							<th width="100" rowspan="2">WO/PI/Production No</th>
							<th width="100" rowspan="2">LC Number</th>
							<th width="110" rowspan="2">Store Name</th>
							
							<th width="50" rowspan="2">Year</th><!--1010 == 11-->
							<th width="100" rowspan="2">Buyer</th>
							<th width="100" rowspan="2">Buyer Client</th>
							<th width="100" rowspan="2">Style No</th>
							<th width="80" rowspan="2">Season</th>
							<th width="100" rowspan="2">Job</th>
							<th width="80" rowspan="2">PO Number</th>
							<th width="100" rowspan="2">Fabric Booking</th>
							<th width="70" rowspan="2">Booking Type</th>
							<th width="80" rowspan="2">Short Booking Type</th>
							<th width="120" rowspan="2">Supplier Name</th>
							
							<th width="135" rowspan="2">Body Part</th><!--730 == 8-->
							<th width="100" rowspan="2">Construction</th>
							<th width="150" rowspan="2">Composition</th>
							<th width="100" rowspan="2">Internal Ref.</th>
							<th width="60" rowspan="2">GSM</th>
							<th width="60" rowspan="2">Dia</th>
							<th width="100" rowspan="2">Batch No</th>
							<th width="80" rowspan="2">Color</th>
							<th width="80" rowspan="2">Shade</th>
							
							<th width="240" colspan="3">Kg</th><!--720 == 9-->
							<th width="240" colspan="3">Yds</th>
							<th width="240" colspan="3">Mtr</th>
							
							<th width="80" rowspan="2">Rate(Taka)</th> <!--530 == 5-->
							<th width="100" rowspan="2">Amount(Taka)</th>
							<th width="100" rowspan="2">Rate (USD)</th>
							<th width="100" rowspan="2">Amount (USD)</th>
							<th width="80" rowspan="2">User</th>
							<th width="120" rowspan="2">Insert Date & Time</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="80">Receive Qty</th>
							<th width="80">Issue Return Qty</th>
							<th width="80">Trans. In Qty</th>
							<th width="80">Receive Qty</th>
							<th width="80">Issue Return Qty</th>
							<th width="80">Trans. In Qty</th>
							<th width="80">Receive Qty</th>
							<th width="80">Issue Return Qty</th>
							<th width="80">Trans. In Qty</th>
							
						</tr>
					</thead>
				</table>
				<div style="width:4050px; max-height:350px; overflow-y:scroll;" id="scroll_body">
					<table width="4030" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
						<?
						$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$issue_trns_out_qnty=0;$rec_trns_in_qnty=0;
						//$fin_color_array=array(); $fin_color_data_arr=array();$checkbuyerArr=array(); $po_break_id_arr=array(); $booking_po_data_arr=array();
						//print_r($po_break_id_arr);
						
						
						//print_r($booking_po_data_arr);
						foreach ($dtls_data as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$rcv_qnty_kg=$issue_rtn_kg=$transfe_in_kg=$rcv_qnty_yds=$issue_rtn_yds=$transfe_in_yds=$rcv_qnty_mtr=$issue_rtn_mtr=$transfe_in_mtr=0;
							if($row[csf("cons_uom")]==12)
							{
								if($row[csf("transaction_type")]==1)
								{
									$rcv_qnty_kg=$row[csf("cons_quantity")];
									$total_rcv_qnty_kg+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==4)
								{
									$issue_rtn_kg=$row[csf("cons_quantity")];
									$total_issue_rtn_kg+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==5)
								{
									$transfe_in_kg=$row[csf("cons_quantity")];
									$total_transfe_in_kg+=$row[csf("cons_quantity")];
								}
							}
							elseif($row[csf("cons_uom")]==27)
							{
								if($row[csf("transaction_type")]==1)
								{
									$rcv_qnty_yds=$row[csf("cons_quantity")];
									$total_rcv_qnty_yds+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==4)
								{
									$issue_rtn_yds=$row[csf("cons_quantity")];
									$total_issue_rtn_yds+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==5)
								{
									$transfe_in_yds=$row[csf("cons_quantity")];
									$total_transfe_in_yds+=$row[csf("cons_quantity")];
								}
							}
							elseif($row[csf("cons_uom")]==23)
							{
								if($row[csf("transaction_type")]==1)
								{
									$rcv_qnty_mtr=$row[csf("cons_quantity")];
									$total_rcv_qnty_mtr+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==4)
								{
									$issue_rtn_mtr=$row[csf("cons_quantity")];
									$total_issue_rtn_mtr+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==5)
								{
									$transfe_in_mtr=$row[csf("cons_quantity")];
									$total_transfe_in_mtr+=$row[csf("cons_quantity")];
								}
							}
							$supplier_name="";
							if($row[csf("transaction_type")]==1)
							{
								if($row[csf("knitting_source")]==1) $supplier_name=$company_arr[$row[csf("knitting_company")]]; else $supplier_name=$supplier_arr[$row[csf("knitting_company")]];
									$remarks=$remarks_arr[$row[csf("batch_id")]];
								//$batch_wise_supplier[$row[csf("batch_id")]]=$supplier_name;
							}
							else
							{
								if($row[csf("transaction_type")]==4)
								{
									$supplier_name=$rcv_supp_arr[$row[csf("batch_id")]];
									$remarks=$remarks_arr[$row[csf("batch_id")]];
								}
								else
								{
									$supplier_name=$rcv_supp_arr[$trans_batch_arr[$row[csf("trans_id")]]];
									$remarks=$remarks_arr[$row[csf("trans_id")]];

								}
							}
							
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30" align="center" title="<? echo $row[csf("trans_id")]; ?>"><? echo $i; ?></td>
								<td width="50" align="center"><? echo $row[csf("prod_id")]; ?></td>
								<td width="70" align="center"><p><? echo change_date_format($row[csf("transaction_date")]); ?>&nbsp;</p></td>
								<td width="110"><p><? echo $row[csf("trans_number")]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
								<td width="120"><p><? echo $receive_basis_arr[$row[csf("receive_basis")]]; ?>&nbsp;</p></td> 
								<td width="100"><p><? echo $row[csf("wo_pi_prod_no")]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $row[csf("lc_sc_no")]; ?>&nbsp;</p></td>
								<td width="110"><p><? echo $store_arr[$row[csf("store_id")]]; ?>&nbsp;</p></td>
								
								<td width="50" align="center"><p><? echo $job_trans_data[$row[csf("trans_id")]]["job_year"]; ?>&nbsp;</p></td>
								<td width="100"><p><? 
									
									if($row[csf("booking_without_order")]==1 || $batch_data[$row[csf("batch_id")]]["booking_without_order"]==1)
									{
										echo $buyer_arr[$nonOrdbookingArr[$batch_data[$row[csf("batch_id")]]["booking_no"]]["buyer_id"]]; 
									}
									else
									{
										echo $buyer_arr[$job_trans_data[$row[csf("trans_id")]]["buyer_name"]]; 
									} 
									?>&nbsp;</p></td>
								<td width="100"><p><? echo $buyer_arr[$job_trans_data[$row[csf("trans_id")]]["client_id"]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $job_trans_data[$row[csf("trans_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $season_arr[$job_trans_data[$row[csf("trans_id")]]["season_buyer_wise"]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $job_trans_data[$row[csf("trans_id")]]["job_no"]; ?>&nbsp;</p></td>
								<td width="80" align="center"><a href="##" onClick="openmypage_po('<? echo chop($job_trans_data[$row[csf("trans_id")]]["po_id"],",");?>','po_popup')">View</a></td>
								<td width="100"><p><? echo $batch_data[$row[csf("batch_id")]]["booking_no"]; ?>&nbsp;</p></td>
								<td width="70" align="center"><p><?
									echo $booking_typeArr[$batch_data[$row[csf("batch_id")]]["booking_no"]];
								// echo $booking_type[$batch_data[$row[csf("batch_id")]]["booking_type"]]; //if($batch_data[$row[csf("batch_id")]]["is_short"]==1) echo "Short"; else echo "Main"; ?>&nbsp;</p></td>
								<td width="80"><p><? if($batch_data[$row[csf("batch_id")]]["is_short"]==1) echo $short_booking_type[$batch_data[$row[csf("batch_id")]]["short_booking_type"]];?>&nbsp;</p></td>
								<td width="120" title="<? echo $row[csf("batch_id")]."=".$trans_batch_arr[$row[csf("trans_id")]]; ?>"><p><? echo $supplier_name; ?>&nbsp;</p></td>
								
								<td width="135"><p><? echo $body_part[$row[csf("body_part_id")]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $constructtion_arr[$row[csf("fabric_description_id")]]; ?>&nbsp;</p></td>
								<td width="150"><p><? echo $composition_arr[$row[csf("fabric_description_id")]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $job_trans_data[$row[csf("trans_id")]]["grouping"]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("gsm")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("width")]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $batch_data[$row[csf("batch_id")]]["batch_no"]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $fabric_shade[$row[csf("fabric_shade")]]; ?>&nbsp;</p></td>
								<td width="80" align="right"><? echo number_format($rcv_qnty_kg,2); ?></td>
								<td width="80" align="right"><? echo number_format($issue_rtn_kg,2); ?></td>
								<td width="80" align="right"><? echo number_format($transfe_in_kg,2); ?></td>
								<td width="80" align="right"><? echo number_format($rcv_qnty_yds,2); ?></td>
								<td width="80" align="right"><? echo number_format($issue_rtn_yds,2); ?></td>
								<td width="80" align="right"><? echo number_format($transfe_in_yds,2); ?></td>
								<td width="80" align="right"><? echo number_format($rcv_qnty_mtr,2); ?></td>
								<td width="80" align="right"><? echo number_format($issue_rtn_mtr,2); ?></td>
								<td width="80" align="right"><? echo number_format($transfe_in_mtr,2); ?></td>
								
								<td width="80" align="right"><? echo number_format($row[csf("cons_rate")],2); ?></td>
								<td width="100" align="right"><? echo number_format($row[csf("cons_amount")],2); ?></td>
							
								<td width="100" align="right"><? 
									if($row[csf("transaction_type")]==4)
									{
										//echo number_format($issue_rate_amnt_arr[$row[csf("issue_id")]]["cons_rate"],2);
										echo number_format($issue_rate_amnt_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["order_rate"],2);
									}
									else if($row[csf("transaction_type")]==5)
									{
										echo number_format($row[csf("cons_rate")]/$txt_exchange_rate,2); 
									}
									else
									{
										echo number_format($row[csf("order_rate")],2); 
									}
								


								?></td>
								<td width="100" align="right"><? 
									if($row[csf("transaction_type")]==4)
									{
										//echo number_format($issue_rate_amnt_arr[$row[csf("issue_id")]]["cons_amount"],2);
										echo number_format($issue_rate_amnt_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["order_amount"],2);

									}
									else if($row[csf("transaction_type")]==5)
									{
										$excngRate=$row[csf("cons_rate")]/$txt_exchange_rate;

										$excngAmt=$excngRate*($transfe_in_mtr+$transfe_in_kg+$transfe_in_yds);
										//echo number_format($row[csf("cons_amount")]/$txt_exchange_rate,2); 
										echo number_format($excngAmt,2); 
									}
									else
									{
										echo number_format($row[csf("order_amount")],2); 
									}

								?></td>

								<td width="80" align="center"><p><? echo $user_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td>
								<td width="120"><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td>
								<td><p><? echo $remarks;//$row[csf("remarks")]; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;	
							$total_cons_amount+=$row[csf("cons_amount")]/$txt_exchange_rate;
							$total_order_amount+=$row[csf("order_amount")];
						}
						?>
						</table>
					<table width="4030" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" > 
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="110">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th> 
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="110">&nbsp;</th>
							
							<th width="50">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th>
							
							<th width="135">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="150">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80"></th>
							<th width="80">Total</th>
							
							<th width="80" align="right" id="value_total_rcv_qnty_kg"><? echo number_format($total_rcv_qnty_kg,2); ?></th>
							<th width="80" align="right" id="value_total_issue_rtn_kg"><? echo number_format($total_issue_rtn_kg,2); ?></th>
							<th width="80" align="right" id="value_total_transfe_in_kg"><? echo number_format($total_transfe_in_kg,2); ?></th>
							<th width="80" align="right" id="value_total_rcv_qnty_yds"><? echo number_format($total_rcv_qnty_yds,2); ?></th>
							<th width="80" align="right" id="value_total_issue_rtn_yds"><? echo number_format($total_issue_rtn_yds,2); ?></th>
							<th width="80" align="right" id="value_total_transfe_in_yds"><? echo number_format($total_transfe_in_yds,2); ?></th>
							<th width="80" align="right" id="value_total_rcv_qnty_mtr"><? echo number_format($total_rcv_qnty_mtr,2); ?></th>
							<th width="80" align="right" id="value_total_issue_rtn_mtr"><? echo number_format($total_issue_rtn_mtr,2); ?></th>
							<th width="80" align="right" id="value_total_transfe_in_mtr"><? echo number_format($total_transfe_in_mtr,2); ?></th> 
							<th width="80">&nbsp;</th>
							<th width="100" align="right" id="value_total_cons_amount"><? echo number_format($total_cons_amount,2); ?></th> 
							<th width="100">&nbsp;</th>
							<th width="100" align="right" id="value_total_order_amount"><? echo number_format($total_order_amount,2); ?></th> 
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th>&nbsp;</th>
						</tfoot>
					</table> 
				</div>  
			</fieldset>
			<?
	
		}
	}
	else if($rpt_type==2) // issue button
	{
		if($cbo_category_type==1) // Knit finish issue
		{
			$cutting_unit_arr=return_library_array( "select trans_id, cutting_unit from inv_finish_fabric_issue_dtls where status_active=1 and is_deleted=0", "trans_id", "cutting_unit"  );
				$floor_name_arr=return_library_array( "select id,floor_name from lib_prod_floor where company_id in($cbo_company_id) and production_process=1 and status_active=1 and is_deleted=0", "id", "floor_name"  );
				
				
				$sql_rcv_issue="select a.id as mst_id, a.entry_form, a.issue_number as trans_number, a.issue_purpose, a.knit_dye_source as knitting_source, a.knit_dye_company as knitting_company, b.id as trans_id, b.pi_wo_batch_no as batch_id, b.prod_id, b.transaction_type, b.transaction_date, b.store_id, b.body_part_id, b.fabric_shade, c.detarmination_id as fabric_description_id, c.gsm, c.dia_width as width, c.color as color_id, b.cons_uom, b.cons_quantity, b.cons_amount, b.cons_rate,b.order_rate,b.order_amount, b.inserted_by, b.insert_date,d.remarks 
				from inv_issue_master a,inv_finish_fabric_issue_dtls d, inv_transaction b, product_details_master c $tmpTable   
				where  a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and  b.prod_id=c.id $whereCondition  and a.entry_form in(18,46) and b.item_category=2 and b.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in($cbo_company_id) and b.company_id in($cbo_company_id) $sql_cond
				union all
				select a.id as mst_id, a.entry_form, a.transfer_system_id as trans_number, 0 as issue_purpose, 0 as knitting_source, 0 as knitting_company, b.id as trans_id, b.pi_wo_batch_no as batch_id, b.prod_id, b.transaction_type, b.transaction_date, b.store_id, b.body_part_id, b.fabric_shade, c.detarmination_id as fabric_description_id, c.gsm, c.dia_width as width, c.color as color_id, b.cons_uom, b.cons_quantity, b.cons_amount, b.cons_rate,b.order_rate,b.order_amount, b.inserted_by, b.insert_date,b.remarks as remarks 
				from inv_item_transfer_mst a, inv_transaction b, product_details_master c $tmpTable  
				where a.id=b.mst_id and b.prod_id=c.id $whereCondition and a.entry_form in(14) and b.item_category=2 and b.transaction_type in(6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in($cbo_company_id) and b.company_id in($cbo_company_id) $sql_cond
				order by trans_id";
				//echo $sql_rcv_issue;
				$dtls_data=sql_select($sql_rcv_issue);


				$issue_batch_ids="";$issue_prod_ids="";
				foreach ($dtls_data as $row)
				{
					if ($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3) 
					{
						$issue_batch_ids.=$row[csf("batch_id")].",";
						$issue_prod_ids.=$row[csf("prod_id")].",";
					}
				}
				$issue_batch_ids=chop($issue_batch_ids,",");
				$issue_prod_ids=chop($issue_prod_ids,",");
				if($issue_batch_ids!="")
				{
					//booking_chunk
					$issue_batch_ids_arr = array_unique(explode(",",$issue_batch_ids));
					$issue_batch_ids_arr = array_filter($issue_batch_ids_arr);
					$issue_batch_ids=implode(",",$issue_batch_ids_arr);
					$all_issue_batch_ids_cond=""; $issue_batch_idsCond="";
					if($db_type==2 && count($issue_batch_ids_arr)>999)
					{
						$all_issue_batch_ids_arr_chunk=array_chunk($issue_batch_ids_arr,999) ;
						foreach($all_issue_batch_ids_arr_chunk as $chunk_arr)
						{
							$chunk_arr_values=implode(",",$chunk_arr);
							$issue_batch_idsCond.="  pi_wo_batch_no in($chunk_arr_values) or ";
						}

						$all_issue_batch_ids_cond.=" and (".chop($issue_batch_idsCond,'or ').")";
					}
					else
					{
						$all_issue_batch_ids_cond=" and pi_wo_batch_no in($issue_batch_ids)";
					}
				}

				$sql_sup_source="select a.knitting_source, a.knitting_company, b.pi_wo_batch_no, b.prod_id, b.body_part_id, b.fabric_shade
				from inv_receive_master a, inv_transaction b, product_details_master c  
				where a.id=b.mst_id and b.prod_id=c.id and a.entry_form in(37,52) and b.item_category=2 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in($cbo_company_id) and b.company_id in($cbo_company_id) $all_issue_batch_ids_cond";

				$sql_sup_source_data=sql_select($sql_sup_source);
				$supplier_source=array();
				foreach ($sql_sup_source_data as $row)
				{
					$supplier_source[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]]["knitting_source"]=$row[csf("knitting_source")];
					$supplier_source[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]]["knitting_company"]=$row[csf("knitting_company")];
				}
				
				if($issue_prod_ids!="")
				{
					//booking_chunk
					$issue_prod_ids_arr = array_unique(explode(",",$issue_prod_ids));
					$issue_prod_ids_arr = array_filter($issue_prod_ids_arr);
					$issue_prod_ids=implode(",",$issue_prod_ids_arr);
					$all_issue_prod_ids_cond=""; $issue_batch_idsCond="";
					if($db_type==2 && count($issue_prod_ids_arr)>999)
					{
						$all_issue_prod_ids_ids_arr_chunk=array_chunk($issue_prod_ids_arr,999) ;
						foreach($all_issue_prod_ids_ids_arr_chunk as $chunk_arr)
						{
							$chunk_arr_value=implode(",",$chunk_arr);
							$issue_batch_idsCond.="  prod_id in($chunk_arr_value) or ";
						}

						$all_issue_prod_ids_cond.=" and (".chop($issue_batch_idsCond,'or ').")";
					}
					else
					{
						$all_issue_prod_ids_cond=" and prod_id in($issue_prod_ids)";
					}
				}

				//$sql_recv_rate_amnt=sql_select("select pi_wo_batch_no,prod_id,order_rate,order_amount from inv_transaction where pi_wo_batch_no in($issue_batch_ids) and prod_id in($issue_prod_ids) and transaction_type=1 and status_active=1 and is_deleted=0");
				$sql_recv_rate_amnt=sql_select("select pi_wo_batch_no,prod_id,order_rate,order_amount,transaction_type,cons_rate,cons_amount from inv_transaction where transaction_type in(1) $all_issue_prod_ids_cond $all_issue_batch_ids_cond  and status_active=1 and is_deleted=0");
				foreach ($sql_recv_rate_amnt as $row)
				{
					if ($row[csf("transaction_type")]==2) 
					{
						$recv_rate_amnt_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_rate"]=$row[csf("cons_rate")];
						$recv_rate_amnt_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_amount"]=$row[csf("cons_amount")];
					}
					else
					{
						$recv_rate_amnt_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_rate"]=$row[csf("order_rate")];
						$recv_rate_amnt_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_amount"]=$row[csf("order_amount")];
					}
					
				}

				ob_start();
				?>
				<fieldset style="width:4260px;">
					<table cellpadding="0" cellspacing="0" width="4050">
						<tr class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="42" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
						</tr>
						<!--<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="42" style="font-size:16px"><strong><?// echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
						</tr>
						<tr  class="form_caption" style="border:none;">
							<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <?// if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
						</tr>-->
					</table>
					<table width="4350" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
						<thead>
							<tr>
								<th width="30" rowspan="2">SL</th><!--850 == 10-->
								<th width="50" rowspan="2">Prod. Id</th>
								<th width="100" rowspan="2">Trans. Date</th>
								<th width="110" rowspan="2">Trans. Ref.</th>
								<th width="110" rowspan="2">Store Name</th>
								<th width="80" rowspan="2">Challan No</th>
								<th width="120" rowspan="2">Issue Purpose</th> 
								<th width="80" rowspan="2">Service Source</th>
								<th width="120" rowspan="2">Service Company</th>
								<th width="100" rowspan="2">Cutting Unit Name</th>
								
								<th width="50" rowspan="2">Year</th><!--980 == 11-->
								<th width="100" rowspan="2">Buyer</th>
								<th width="100" rowspan="2">Buyer Client</th>
								<th width="100" rowspan="2">Style No</th>
								<th width="80" rowspan="2">Season</th>
								<th width="100" rowspan="2">Job</th>
								<th width="80" rowspan="2">PO Number</th>
								<th width="120" rowspan="2">Fabric Booking</th>
								<th width="70" rowspan="2">Booking Type</th>
								<th width="80" rowspan="2">Short Booking Type</th>
								<th width="120" rowspan="2">Supplier Name</th>
								
								<th width="150" rowspan="2">Body Part</th><!--770 == 8-->
								<th width="100" rowspan="2">Construction</th>
								<th width="150" rowspan="2">Composition</th>
								<th width="100" rowspan="2">Internal Ref.</th>
								<th width="60" rowspan="2">GSM</th>
								<th width="60" rowspan="2">Dia</th>
								<th width="100" rowspan="2">Batch No</th>
								<th width="80" rowspan="2">Color</th>
								<th width="80" rowspan="2">Shade</th>
								
								<th width="240" colspan="3">Kg</th><!--720 == 9-->
								<th width="240" colspan="3">Yds</th>
								<th width="240" colspan="3">Mtr</th>
								
								<th width="80" rowspan="2">Rate(Taka)</th> <!--530 == 5-->
								<th width="100" rowspan="2">Amount(Taka)</th>
								<th width="100" rowspan="2">Rate (USD)</th> <!--530 == 5-->
								<th width="100" rowspan="2">Amount (USD)</th>
								<th width="80" rowspan="2">User</th>
								<th width="120" rowspan="2">Insert Date & Time</th>
								<th rowspan="2">Remarks</th>
							</tr>
							<tr>
								<th width="80">Issue Qty</th>
								<th width="80">Receive Return Qty</th>
								<th width="80">Trans. Out Qty</th>
								<th width="80">Issue Qty</th>
								<th width="80">Receive Return Qty</th>
								<th width="80">Trans. Out Qty</th>
								<th width="80">Issue Qty</th>
								<th width="80">Receive Return Qty</th>
								<th width="80">Trans. Out Qty</th>
								
							</tr>
						</thead>
					</table>
					<div style="width:4350px; max-height:350px; overflow-y:scroll;" id="scroll_body">
						<table width="4330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
							<?
							$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$issue_trns_out_qnty=0;$rec_trns_in_qnty=0;
							//$fin_color_array=array(); $fin_color_data_arr=array();$checkbuyerArr=array(); $po_break_id_arr=array(); $booking_po_data_arr=array();
							//print_r($po_break_id_arr);
							
							
							//print_r($booking_po_data_arr);
							foreach ($dtls_data as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$issue_qnty_kg=$rcv_rtn_kg=$transfe_out_kg=$issue_qnty_yds=$rcv_rtn_yds=$transfe_out_yds=$issue_qnty_mtr=$rcv_rtn_mtr=$transfe_out_mtr=0;
								if($row[csf("cons_uom")]==12)
								{
									if($row[csf("transaction_type")]==2)
									{
										$issue_qnty_kg=$row[csf("cons_quantity")];
										$total_issue_qnty_kg+=$row[csf("cons_quantity")];
									}
									elseif($row[csf("transaction_type")]==3)
									{
										$rcv_rtn_kg=$row[csf("cons_quantity")];
										$total_rcv_rtn_kg+=$row[csf("cons_quantity")];
									}
									elseif($row[csf("transaction_type")]==6)
									{
										$transfe_out_kg=$row[csf("cons_quantity")];
										$total_transfe_out_kg+=$row[csf("cons_quantity")];
									}
								}
								elseif($row[csf("cons_uom")]==27)
								{
									if($row[csf("transaction_type")]==2)
									{
										$issue_qnty_yds=$row[csf("cons_quantity")];
										$total_issue_qnty_yds+=$row[csf("cons_quantity")];
									}
									elseif($row[csf("transaction_type")]==3)
									{
										$rcv_rtn_yds=$row[csf("cons_quantity")];
										$total_rcv_rtn_yds+=$row[csf("cons_quantity")];
									}
									elseif($row[csf("transaction_type")]==6)
									{
										$transfe_out_yds=$row[csf("cons_quantity")];
										$total_transfe_out_yds+=$row[csf("cons_quantity")];
									}
								}
								elseif($row[csf("cons_uom")]==23)
								{
									if($row[csf("transaction_type")]==2)
									{
										$issue_qnty_mtr=$row[csf("cons_quantity")];
										$total_issue_qnty_mtr+=$row[csf("cons_quantity")];
									}
									elseif($row[csf("transaction_type")]==3)
									{
										$rcv_rtn_mtr=$row[csf("cons_quantity")];
										$total_rcv_rtn_mtr+=$row[csf("cons_quantity")];
									}
									elseif($row[csf("transaction_type")]==6)
									{
										$transfe_out_mtr=$row[csf("cons_quantity")];
										$total_transfe_out_mtr+=$row[csf("cons_quantity")];
									}
								}
								
								$supplier_name="";
								if($row[csf("transaction_type")]==2)
								{
									$sup_knitting_source = $supplier_source[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]]["knitting_source"];
									$sup_knitting_company = $supplier_source[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]]["knitting_company"];
									
									if($sup_knitting_source==1) 
										$supplier_name=$company_arr[$row[csf("knitting_company")]]; 
									else 
										$supplier_name=$supplier_arr[$sup_knitting_company];
								}
								else
								{
									$supplier_name=$rcv_supp_arr[$row[csf("batch_id")]];
								}
								$tot_receive_rate=0;
									if($tot_receive>0)
									{
										$tot_receive_rate 	= $tot_receive_amount/$tot_receive;
									}
								$total_issue  = $issue_qnty_kg +$rcv_rtn_kg + $transfe_out_kg + $issue_qnty_yds + $rcv_rtn_yds + $transfe_out_yds +$issue_qnty_mtr + $rcv_rtn_mtr + $transfe_out_mtr;


								$total_issue = number_format($issue_qnty_kg,2,'.', '')+ number_format($rcv_rtn_kg,2,'.', '')+number_format($transfe_out_kg,2,'.', '')+number_format($issue_qnty_yds,2,'.', '')+ number_format($rcv_rtn_yds,2,'.', '')+ number_format($transfe_out_yds,2,'.', '')+number_format($issue_qnty_mtr,2,'.', '')+number_format($rcv_rtn_mtr,2,'.', '')+number_format($transfe_out_mtr,2,'.', '');

								$totalAmount = $total_issue*number_format(($row[csf("cons_rate")]),2,'.', '');
								
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30" align="center" title="<? echo $row[csf("trans_id")]; ?>"><? echo $i; ?></td>
									<td width="50" align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
									<td width="100" align="center"><p><? echo change_date_format($row[csf("transaction_date")]); ?>&nbsp;</p></td>
									<td width="110"><p><? echo $row[csf("trans_number")]; ?>&nbsp;</p></td>
									<td width="110"><p><? echo $store_arr[$row[csf("store_id")]]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
									<td width="120"><p><? echo $yarn_issue_purpose[$row[csf("issue_purpose")]]; ?>&nbsp;</p></td> 
									<td width="80"><p><? echo $knitting_source[$row[csf("knitting_source")]]; ?>&nbsp;</p></td>
									<td width="120"><p><? if($row[csf("knitting_source")]==1) echo $company_arr[$row[csf("knitting_company")]]; else echo $supplier_arr[$row[csf("knitting_company")]];?>&nbsp;</p></td>
									<td width="100"><p><? echo $floor_name_arr[$cutting_unit_arr[$row[csf("trans_id")]]]; ?>&nbsp;</p></td>
									
									
									<td width="50" align="center"><p><? echo $job_trans_data[$row[csf("trans_id")]]["job_year"]; ?>&nbsp;</p></td>
									<td width="100"><p><? 
									if($job_trans_data[$row[csf("trans_id")]]["buyer_name"]!=""){echo $buyer_arr[$job_trans_data[$row[csf("trans_id")]]["buyer_name"]]; }else{echo $buyer_arr[$nonOrdbookingArr[$batch_data[$row[csf("batch_id")]]["booking_no"]]["buyer_id"]];}
									?>&nbsp;</p></td>
									<td width="100"><p><? echo $buyer_arr[$job_trans_data[$row[csf("trans_id")]]["client_id"]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $job_trans_data[$row[csf("trans_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo $season_arr[$job_trans_data[$row[csf("trans_id")]]["season_buyer_wise"]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $job_trans_data[$row[csf("trans_id")]]["job_no"]; ?>&nbsp;</p></td>
									<td width="80" align="center"><a href="##" onClick="openmypage_po('<? echo chop($job_trans_data[$row[csf("trans_id")]]["po_id"],",");?>','po_popup')">View</a></td>
									<td width="120"><p><? echo $batch_data[$row[csf("batch_id")]]["booking_no"]; ?>&nbsp;</p></td>
									<td width="70" align="center"><p><? 
										echo $booking_typeArr[$batch_data[$row[csf("batch_id")]]["booking_no"]];//$booking_type[$batch_data[$row[csf("batch_id")]]["booking_type"]];
										//if($batch_data[$row[csf("batch_id")]]["is_short"]==1) echo "Short"; else echo "Main"; 

									?>&nbsp;</p></td>
									<td width="80"><p><? if($batch_data[$row[csf("batch_id")]]["is_short"]==1) echo $short_booking_type[$batch_data[$row[csf("batch_id")]]["short_booking_type"]];?>&nbsp;</p></td>
									<td width="120" title="<? echo $row[csf("batch_id")]; ?>"><p><? echo $supplier_name; ?>&nbsp;</p></td>
									
									<td width="150"><p><? echo $body_part[$row[csf("body_part_id")]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $constructtion_arr[$row[csf("fabric_description_id")]]; ?>&nbsp;</p></td>
									<td width="150"><p><? echo $composition_arr[$row[csf("fabric_description_id")]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $job_trans_data[$row[csf("trans_id")]]["grouping"];; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $row[csf("gsm")]; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $row[csf("width")]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $batch_data[$row[csf("batch_id")]]["batch_no"]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo $fabric_shade[$row[csf("fabric_shade")]]; ?>&nbsp;</p></td>


									<td width="80" align="right"><? echo number_format($issue_qnty_kg,2,'.', ''); ?></td>
									<td width="80" align="right"><? echo number_format($rcv_rtn_kg,2,'.', ''); ?></td>
									<td width="80" align="right"><? echo number_format($transfe_out_kg,2,'.', ''); ?></td>
									<td width="80" align="right"><? echo number_format($issue_qnty_yds,2,'.', ''); ?></td>
									<td width="80" align="right"><? echo number_format($rcv_rtn_yds,2,'.', ''); ?></td>
									<td width="80" align="right"><? echo number_format($transfe_out_yds,2,'.', ''); ?></td>
									<td width="80" align="right"><? echo number_format($issue_qnty_mtr,2,'.', ''); ?></td>
									<td width="80" align="right"><? echo number_format($rcv_rtn_mtr,2,'.', ''); ?></td>
									<td width="80" align="right"><? echo number_format($transfe_out_mtr,2,'.', ''); ?></td>
									
									<td width="80" align="right"><? echo number_format(($row[csf("cons_rate")]),2,'.', ''); //echo number_format(($row[csf("cons_rate")]/$txt_exchange_rate),2); ?></td>
									<td width="100" align="right"><? echo number_format($totalAmount,2,'.', ''); //echo number_format(($row[csf("cons_amount")]/$txt_exchange_rate),2); ?></td>


									<td width="100" align="right"><?
										if($txt_exchange_rate>1)
										{
											if ($row[csf("entry_form")]==14 ) 
											{
												//echo number_format($row[csf("cons_rate")],2); 
												$calculateConsRate=number_format(($row[csf("cons_rate")]),2,'.', '')/$txt_exchange_rate;
												echo number_format($calculateConsRate,4,'.', ''); 
											}
											else if($row[csf("entry_form")]==18 || $row[csf("entry_form")]==46 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3)
											{
												//echo number_format($recv_rate_amnt_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["order_rate"],2);
												$calculateConsRate=number_format(($row[csf("cons_rate")]),2,'.', '')/$txt_exchange_rate;
												echo number_format($calculateConsRate,4,'.', '');
											}
										
											else
											{
												//echo number_format($row[csf("order_rate")],2);  
												$calculateConsRate=number_format(($row[csf("cons_rate")]),2,'.', '')/$txt_exchange_rate;
												echo number_format($calculateConsRate,4,'.', '');
											}
										}
									?></td>
									<td width="100" align="right"><? 
										if($txt_exchange_rate>1)
										{
											if ($row[csf("entry_form")]==14 ) 
											{
												//echo number_format($row[csf("cons_amount")],2); 
												$calculateConsAmount=number_format(($totalAmount),2,'.', '')/$txt_exchange_rate;
												echo number_format($calculateConsAmount,2,'.', ''); 
											}
											else if($row[csf("entry_form")]==18 || $row[csf("entry_form")]==46 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3)
											{
												//echo number_format($recv_rate_amnt_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["order_amount"],2);
												$calculateConsAmount=number_format(($totalAmount),2,'.', '')/$txt_exchange_rate;
												echo number_format($calculateConsAmount,2,'.', ''); 
											}
											else
											{
												//echo number_format($row[csf("order_amount")],2);  
												$calculateConsAmount=number_format(($totalAmount),2,'.', '')/$txt_exchange_rate;
												echo number_format($calculateConsAmount,2,'.', ''); 
											}
										}
									?></td>
									<td width="80" align="center"><p><? echo $user_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td>
									<td width="120"><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td>
									<td><p><? echo $row[csf("remarks")]; ?>&nbsp;</p></td>
								</tr>
								<?
								$i++;	
								$total_cons_amount+=$row[csf("cons_amount")]/$txt_exchange_rate;
								$total_order_amount+=$row[csf("order_amount")];
							}
							?>
							</table>
						<table width="4330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" > 
							<tfoot>
								<th width="30">&nbsp;</th>
								<th width="50">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="110">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="120">&nbsp;</th> 
								<th width="80">&nbsp;</th>
								<th width="120">&nbsp;</th>
								<th width="100">&nbsp;</th>
								
								<th width="50">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="120">&nbsp;</th>
								<th width="70">&nbsp;</th>
								<th width="80">&nbsp;</th>
								<th width="120">&nbsp;</th>
								
								<th width="150">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="150">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="60">&nbsp;</th>
								<th width="60">&nbsp;</th>
								<th width="100">&nbsp;</th>
								<th width="80"></th>
								<th width="80">Total</th>
								
								
								<th width="80" align="right" id="value_issue_qnty_kg"><? echo number_format($total_issue_qnty_kg,2); ?></th>
								<th width="80" align="right" id="value_rcv_rtn_kg"><? echo number_format($total_rcv_rtn_kg,2); ?></th>
								<th width="80" align="right" id="value_transfe_out_kg"><? echo number_format($total_transfe_out_kg,2); ?></th>
								<th width="80" align="right" id="value_issue_qnty_yds"><? echo number_format($total_issue_qnty_yds,2); ?></th>
								<th width="80" align="right" id="value_rcv_rtn_yds"><? echo number_format($total_rcv_rtn_yds,2); ?></th>
								<th width="80" align="right" id="value_transfe_out_yds"><? echo number_format($total_transfe_out_yds,2); ?></th>
								<th width="80" align="right" id="value_issue_qnty_mtr"><? echo number_format($total_issue_qnty_mtr,2); ?></th>
								<th width="80" align="right" id="value_rcv_rtn_mtr"><? echo number_format($total_rcv_rtn_mtr,2); ?></th>
								<th width="80" align="right" id="value_transfe_out_mtr"><? echo number_format($total_transfe_out_mtr,2); ?></th> 
								<th width="80">&nbsp;</th>
								<th width="100" align="right" id="value_total_cons_amount"><? echo number_format($total_cons_amount,2); ?></th> 

								<th width="100">&nbsp;</th>
								<th width="100" align="right" id="value_total_order_amount"><? echo number_format($total_order_amount,2); ?></th>

								<th width="80">&nbsp;</th>
								<th width="120">&nbsp;</th>
								<th>&nbsp;</th>
							</tfoot>
						</table> 
					</div>  
				</fieldset>
				<?
		}
		else // woven issue
		{
			$cutting_unit_arr=return_library_array( "select trans_id, cutting_unit from inv_finish_fabric_issue_dtls where status_active=1 and is_deleted=0", "trans_id", "cutting_unit"  );
			$floor_name_arr=return_library_array( "select id,floor_name from lib_prod_floor where company_id in($cbo_company_id) and production_process=1 and status_active=1 and is_deleted=0", "id", "floor_name"  );
			
			
			$sql_rcv_issue="select a.id as mst_id, a.entry_form, a.issue_number as trans_number, a.issue_purpose, a.knit_dye_source as knitting_source, a.knit_dye_company as knitting_company, b.id as trans_id,  to_number(b.batch_id) as batch_id, b.prod_id, b.transaction_type, b.transaction_date, b.store_id, b.body_part_id, b.fabric_shade, c.detarmination_id as fabric_description_id, c.gsm, c.dia_width as width, c.color as color_id, b.cons_uom, b.cons_quantity, b.cons_amount, b.cons_rate,b.order_rate,b.order_amount, b.inserted_by, b.insert_date,d.remarks 
			from inv_issue_master a,INV_WVN_FINISH_FAB_ISS_DTLS d, inv_transaction b, product_details_master c 
			where  a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and  b.prod_id=c.id and a.entry_form in(19) and b.item_category=3 and b.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in($cbo_company_id) and b.company_id in($cbo_company_id) $sql_cond2 

			union all
			select a.id as mst_id, a.entry_form, a.issue_number as trans_number, a.issue_purpose, a.knit_dye_source as knitting_source, a.knit_dye_company as knitting_company, b.id as trans_id, b.pi_wo_batch_no as batch_id, b.prod_id, b.transaction_type, b.transaction_date, b.store_id, b.body_part_id, b.fabric_shade, c.detarmination_id as fabric_description_id, c.gsm, c.dia_width as width, c.color as color_id, b.cons_uom, b.cons_quantity, b.cons_amount, b.cons_rate,b.order_rate,b.order_amount, b.inserted_by, b.insert_date,d.remarks 
			from inv_issue_master a,inv_finish_fabric_issue_dtls d, inv_transaction b, product_details_master c $tmpTable  
			where  a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and  b.prod_id=c.id $whereCondition and a.entry_form in(202) and b.item_category=3 and b.transaction_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in($cbo_company_id) and b.company_id in($cbo_company_id) $sql_cond 
			union all
			select a.id as mst_id, a.entry_form, a.transfer_system_id as trans_number, 0 as issue_purpose, 0 as knitting_source, 0 as knitting_company, b.id as trans_id, b.pi_wo_batch_no as batch_id, b.prod_id, b.transaction_type, b.transaction_date, b.store_id, b.body_part_id, b.fabric_shade, c.detarmination_id as fabric_description_id, c.gsm, c.dia_width as width, c.color as color_id, b.cons_uom, b.cons_quantity, b.cons_amount, b.cons_rate,b.order_rate,b.order_amount, b.inserted_by, b.insert_date,b.remarks as remarks 
			from inv_item_transfer_mst a, inv_transaction b, product_details_master c $tmpTable 
			where a.id=b.mst_id and b.prod_id=c.id $whereCondition and a.entry_form in(258) and b.item_category=3 and b.transaction_type in(6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in($cbo_company_id) and b.company_id in($cbo_company_id) $sql_cond
			order by trans_id";
			//echo $sql_rcv_issue;
			$dtls_data=sql_select($sql_rcv_issue);


			$issue_batch_ids="";$issue_prod_ids="";
			foreach ($dtls_data as $row)
			{
				if ($row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3) 
				{
					$issue_batch_ids.=$row[csf("batch_id")].",";
					$issue_prod_ids.=$row[csf("prod_id")].",";
				}
			}
			$issue_batch_ids=chop($issue_batch_ids,",");
			$issue_prod_ids=chop($issue_prod_ids,",");
			if($issue_batch_ids!="")
			{
				//booking_chunk
				$issue_batch_ids_arr = array_unique(explode(",",$issue_batch_ids));
				$issue_batch_ids_arr = array_filter($issue_batch_ids_arr);
				$issue_batch_ids=implode(",",$issue_batch_ids_arr);
				$all_issue_batch_ids_cond=""; $issue_batch_idsCond="";
				if($db_type==2 && count($issue_batch_ids_arr)>999)
				{
					$all_issue_batch_ids_arr_chunk=array_chunk($issue_batch_ids_arr,999) ;
					foreach($all_issue_batch_ids_arr_chunk as $chunk_arr)
					{
						$chunk_arr_values=implode(",",$chunk_arr);
						$issue_batch_idsCond.="  pi_wo_batch_no in($chunk_arr_values) or ";
					}

					$all_issue_batch_ids_cond.=" and (".chop($issue_batch_idsCond,'or ').")";
				}
				else
				{
					$all_issue_batch_ids_cond=" and pi_wo_batch_no in($issue_batch_ids)";
				}
			}

			$sql_sup_source="select a.knitting_source, a.knitting_company, b.pi_wo_batch_no, b.prod_id, b.body_part_id, b.fabric_shade
			from inv_receive_master a, inv_transaction b, product_details_master c  
			where a.id=b.mst_id and b.prod_id=c.id and a.entry_form in(37,52) and b.item_category=2 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in($cbo_company_id) and b.company_id in($cbo_company_id) $all_issue_batch_ids_cond";

			$sql_sup_source_data=sql_select($sql_sup_source);
			$supplier_source=array();
			foreach ($sql_sup_source_data as $row)
			{
				$supplier_source[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]]["knitting_source"]=$row[csf("knitting_source")];
				$supplier_source[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]]["knitting_company"]=$row[csf("knitting_company")];
			}
			
			if($issue_prod_ids!="")
			{
				//booking_chunk
				$issue_prod_ids_arr = array_unique(explode(",",$issue_prod_ids));
				$issue_prod_ids_arr = array_filter($issue_prod_ids_arr);
				$issue_prod_ids=implode(",",$issue_prod_ids_arr);
				$all_issue_prod_ids_cond=""; $issue_batch_idsCond="";
				if($db_type==2 && count($issue_prod_ids_arr)>999)
				{
					$all_issue_prod_ids_ids_arr_chunk=array_chunk($issue_prod_ids_arr,999) ;
					foreach($all_issue_prod_ids_ids_arr_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$issue_batch_idsCond.="  prod_id in($chunk_arr_value) or ";
					}

					$all_issue_prod_ids_cond.=" and (".chop($issue_batch_idsCond,'or ').")";
				}
				else
				{
					$all_issue_prod_ids_cond=" and prod_id in($issue_prod_ids)";
				}
			}

			//$sql_recv_rate_amnt=sql_select("select pi_wo_batch_no,prod_id,order_rate,order_amount from inv_transaction where pi_wo_batch_no in($issue_batch_ids) and prod_id in($issue_prod_ids) and transaction_type=1 and status_active=1 and is_deleted=0");
			$sql_recv_rate_amnt=sql_select("select pi_wo_batch_no,prod_id,order_rate,order_amount,transaction_type,cons_rate,cons_amount from inv_transaction where transaction_type in(1) $all_issue_prod_ids_cond $all_issue_batch_ids_cond  and status_active=1 and is_deleted=0");
			foreach ($sql_recv_rate_amnt as $row)
			{
				if ($row[csf("transaction_type")]==2) 
				{
					$recv_rate_amnt_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_rate"]=$row[csf("cons_rate")];
					$recv_rate_amnt_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_amount"]=$row[csf("cons_amount")];
				}
				else
				{
					$recv_rate_amnt_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_rate"]=$row[csf("order_rate")];
					$recv_rate_amnt_arr[$row[csf("pi_wo_batch_no")]][$row[csf("prod_id")]]["order_amount"]=$row[csf("order_amount")];
				}
				
			}

			ob_start();
			?>
			<fieldset style="width:4260px;">
				<table cellpadding="0" cellspacing="0" width="4050">
					<tr class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="42" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<!--<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="42" style="font-size:16px"><strong><?// echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <?// if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
					</tr>-->
				</table>
				<table width="4350" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
					<thead>
						<tr>
							<th width="30" rowspan="2">SL</th><!--850 == 10-->
							<th width="50" rowspan="2">Prod. Id</th>
							<th width="100" rowspan="2">Trans. Date</th>
							<th width="110" rowspan="2">Trans. Ref.</th>
							<th width="110" rowspan="2">Store Name</th>
							<th width="80" rowspan="2">Challan No</th>
							<th width="120" rowspan="2">Issue Purpose</th> 
							<th width="80" rowspan="2">Service Source</th>
							<th width="120" rowspan="2">Service Company</th>
							<th width="100" rowspan="2">Cutting Unit Name</th>
							
							<th width="50" rowspan="2">Year</th><!--980 == 11-->
							<th width="100" rowspan="2">Buyer</th>
							<th width="100" rowspan="2">Buyer Client</th>
							<th width="100" rowspan="2">Style No</th>
							<th width="80" rowspan="2">Season</th>
							<th width="100" rowspan="2">Job</th>
							<th width="80" rowspan="2">PO Number</th>
							<th width="120" rowspan="2">Fabric Booking</th>
							<th width="70" rowspan="2">Booking Type</th>
							<th width="80" rowspan="2">Short Booking Type</th>
							<th width="120" rowspan="2">Supplier Name</th>
							
							<th width="150" rowspan="2">Body Part</th><!--770 == 8-->
							<th width="100" rowspan="2">Construction</th>
							<th width="150" rowspan="2">Composition</th>
							<th width="100" rowspan="2">Internal Ref.</th>
							<th width="60" rowspan="2">GSM</th>
							<th width="60" rowspan="2">Dia</th>
							<th width="100" rowspan="2">Batch No</th>
							<th width="80" rowspan="2">Color</th>
							<th width="80" rowspan="2">Shade</th>
							
							<th width="240" colspan="3">Kg</th><!--720 == 9-->
							<th width="240" colspan="3">Yds</th>
							<th width="240" colspan="3">Mtr</th>
							
							<th width="80" rowspan="2">Rate(Taka)</th> <!--530 == 5-->
							<th width="100" rowspan="2">Amount(Taka)</th>
							<th width="100" rowspan="2">Rate (USD)</th> <!--530 == 5-->
							<th width="100" rowspan="2">Amount (USD)</th>
							<th width="80" rowspan="2">User</th>
							<th width="120" rowspan="2">Insert Date & Time</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="80">Issue Qty</th>
							<th width="80">Receive Return Qty</th>
							<th width="80">Trans. Out Qty</th>
							<th width="80">Issue Qty</th>
							<th width="80">Receive Return Qty</th>
							<th width="80">Trans. Out Qty</th>
							<th width="80">Issue Qty</th>
							<th width="80">Receive Return Qty</th>
							<th width="80">Trans. Out Qty</th>
							
						</tr>
					</thead>
				</table>
				<div style="width:4350px; max-height:350px; overflow-y:scroll;" id="scroll_body">
					<table width="4330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
						<?
						$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$issue_trns_out_qnty=0;$rec_trns_in_qnty=0;
						//$fin_color_array=array(); $fin_color_data_arr=array();$checkbuyerArr=array(); $po_break_id_arr=array(); $booking_po_data_arr=array();
						//print_r($po_break_id_arr);
						
						
						//print_r($booking_po_data_arr);
						foreach ($dtls_data as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$issue_qnty_kg=$rcv_rtn_kg=$transfe_out_kg=$issue_qnty_yds=$rcv_rtn_yds=$transfe_out_yds=$issue_qnty_mtr=$rcv_rtn_mtr=$transfe_out_mtr=0;
							if($row[csf("cons_uom")]==12)
							{
								if($row[csf("transaction_type")]==2)
								{
									$issue_qnty_kg=$row[csf("cons_quantity")];
									$total_issue_qnty_kg+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==3)
								{
									$rcv_rtn_kg=$row[csf("cons_quantity")];
									$total_rcv_rtn_kg+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==6)
								{
									$transfe_out_kg=$row[csf("cons_quantity")];
									$total_transfe_out_kg+=$row[csf("cons_quantity")];
								}
							}
							elseif($row[csf("cons_uom")]==27)
							{
								if($row[csf("transaction_type")]==2)
								{
									$issue_qnty_yds=$row[csf("cons_quantity")];
									$total_issue_qnty_yds+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==3)
								{
									$rcv_rtn_yds=$row[csf("cons_quantity")];
									$total_rcv_rtn_yds+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==6)
								{
									$transfe_out_yds=$row[csf("cons_quantity")];
									$total_transfe_out_yds+=$row[csf("cons_quantity")];
								}
							}
							elseif($row[csf("cons_uom")]==23)
							{
								if($row[csf("transaction_type")]==2)
								{
									$issue_qnty_mtr=$row[csf("cons_quantity")];
									$total_issue_qnty_mtr+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==3)
								{
									$rcv_rtn_mtr=$row[csf("cons_quantity")];
									$total_rcv_rtn_mtr+=$row[csf("cons_quantity")];
								}
								elseif($row[csf("transaction_type")]==6)
								{
									$transfe_out_mtr=$row[csf("cons_quantity")];
									$total_transfe_out_mtr+=$row[csf("cons_quantity")];
								}
							}
							
							$supplier_name="";
							if($row[csf("transaction_type")]==2)
							{
								$sup_knitting_source = $supplier_source[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]]["knitting_source"];
								$sup_knitting_company = $supplier_source[$row[csf("batch_id")]][$row[csf("prod_id")]][$row[csf("body_part_id")]][$row[csf("fabric_shade")]]["knitting_company"];
								
								if($sup_knitting_source==1) 
									$supplier_name=$company_arr[$row[csf("knitting_company")]]; 
								else 
									$supplier_name=$supplier_arr[$sup_knitting_company];
							}
							else
							{
								$supplier_name=$rcv_supp_arr[$row[csf("batch_id")]];
							}
							$tot_receive_rate=0;
								if($tot_receive>0)
								{
									$tot_receive_rate 	= $tot_receive_amount/$tot_receive;
								}
							$total_issue  = $issue_qnty_kg +$rcv_rtn_kg + $transfe_out_kg + $issue_qnty_yds + $rcv_rtn_yds + $transfe_out_yds +$issue_qnty_mtr + $rcv_rtn_mtr + $transfe_out_mtr;


							$total_issue = number_format($issue_qnty_kg,2,'.', '')+ number_format($rcv_rtn_kg,2,'.', '')+number_format($transfe_out_kg,2,'.', '')+number_format($issue_qnty_yds,2,'.', '')+ number_format($rcv_rtn_yds,2,'.', '')+ number_format($transfe_out_yds,2,'.', '')+number_format($issue_qnty_mtr,2,'.', '')+number_format($rcv_rtn_mtr,2,'.', '')+number_format($transfe_out_mtr,2,'.', '');

							$totalAmount = $total_issue*number_format(($row[csf("cons_rate")]),2,'.', '');
							
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="30" align="center" title="<? echo $row[csf("trans_id")]; ?>"><? echo $i; ?></td>
								<td width="50" align="center"><p><? echo $row[csf("prod_id")]; ?></p></td>
								<td width="100" align="center"><p><? echo change_date_format($row[csf("transaction_date")]); ?>&nbsp;</p></td>
								<td width="110"><p><? echo $row[csf("trans_number")]; ?>&nbsp;</p></td>
								<td width="110"><p><? echo $store_arr[$row[csf("store_id")]]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
								<td width="120"><p><? echo $yarn_issue_purpose[$row[csf("issue_purpose")]]; ?>&nbsp;</p></td> 
								<td width="80"><p><? echo $knitting_source[$row[csf("knitting_source")]]; ?>&nbsp;</p></td>
								<td width="120"><p><? if($row[csf("knitting_source")]==1) echo $company_arr[$row[csf("knitting_company")]]; else echo $supplier_arr[$row[csf("knitting_company")]];?>&nbsp;</p></td>
								<td width="100"><p><? echo $floor_name_arr[$cutting_unit_arr[$row[csf("trans_id")]]]; ?>&nbsp;</p></td>
								
								
								<td width="50" align="center"><p><? echo $job_trans_data[$row[csf("trans_id")]]["job_year"]; ?>&nbsp;</p></td>
								<td width="100"><p><? 
								if($job_trans_data[$row[csf("trans_id")]]["buyer_name"]!=""){echo $buyer_arr[$job_trans_data[$row[csf("trans_id")]]["buyer_name"]]; }else{echo $buyer_arr[$nonOrdbookingArr[$batch_data[$row[csf("batch_id")]]["booking_no"]]["buyer_id"]];}
								?>&nbsp;</p></td>
								<td width="100"><p><? echo $buyer_arr[$job_trans_data[$row[csf("trans_id")]]["client_id"]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $job_trans_data[$row[csf("trans_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $season_arr[$job_trans_data[$row[csf("trans_id")]]["season_buyer_wise"]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $job_trans_data[$row[csf("trans_id")]]["job_no"]; ?>&nbsp;</p></td>
								<td width="80" align="center"><a href="##" onClick="openmypage_po('<? echo chop($job_trans_data[$row[csf("trans_id")]]["po_id"],",");?>','po_popup')">View</a></td>
								<td width="120"><p><? echo $batch_data[$row[csf("batch_id")]]["booking_no"]; ?>&nbsp;</p></td>
								<td width="70" align="center"><p><? 
									echo $booking_typeArr[$batch_data[$row[csf("batch_id")]]["booking_no"]];//$booking_type[$batch_data[$row[csf("batch_id")]]["booking_type"]];
									//if($batch_data[$row[csf("batch_id")]]["is_short"]==1) echo "Short"; else echo "Main"; 

								?>&nbsp;</p></td>
								<td width="80"><p><? if($batch_data[$row[csf("batch_id")]]["is_short"]==1) echo $short_booking_type[$batch_data[$row[csf("batch_id")]]["short_booking_type"]];?>&nbsp;</p></td>
								<td width="120" title="<? echo $row[csf("batch_id")]; ?>"><p><? echo $supplier_name; ?>&nbsp;</p></td>
								
								<td width="150"><p><? echo $body_part[$row[csf("body_part_id")]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $constructtion_arr[$row[csf("fabric_description_id")]]; ?>&nbsp;</p></td>
								<td width="150"><p><? echo $composition_arr[$row[csf("fabric_description_id")]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $job_trans_data[$row[csf("trans_id")]]["grouping"];; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("gsm")]; ?>&nbsp;</p></td>
								<td width="60" align="center"><p><? echo $row[csf("width")]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $batch_data[$row[csf("batch_id")]]["batch_no"]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
								<td width="80"><p><? echo $fabric_shade[$row[csf("fabric_shade")]]; ?>&nbsp;</p></td>


								<td width="80" align="right"><? echo number_format($issue_qnty_kg,2,'.', ''); ?></td>
								<td width="80" align="right"><? echo number_format($rcv_rtn_kg,2,'.', ''); ?></td>
								<td width="80" align="right"><? echo number_format($transfe_out_kg,2,'.', ''); ?></td>
								<td width="80" align="right"><? echo number_format($issue_qnty_yds,2,'.', ''); ?></td>
								<td width="80" align="right"><? echo number_format($rcv_rtn_yds,2,'.', ''); ?></td>
								<td width="80" align="right"><? echo number_format($transfe_out_yds,2,'.', ''); ?></td>
								<td width="80" align="right"><? echo number_format($issue_qnty_mtr,2,'.', ''); ?></td>
								<td width="80" align="right"><? echo number_format($rcv_rtn_mtr,2,'.', ''); ?></td>
								<td width="80" align="right"><? echo number_format($transfe_out_mtr,2,'.', ''); ?></td>
								
								<td width="80" align="right"><? echo number_format(($row[csf("cons_rate")]),2,'.', ''); //echo number_format(($row[csf("cons_rate")]/$txt_exchange_rate),2); ?></td>
								<td width="100" align="right"><? echo number_format($totalAmount,2,'.', ''); //echo number_format(($row[csf("cons_amount")]/$txt_exchange_rate),2); ?></td>


								<td width="100" align="right"><?
									if($txt_exchange_rate>1)
									{
										if ($row[csf("entry_form")]==14 ) 
										{
											//echo number_format($row[csf("cons_rate")],2); 
											$calculateConsRate=number_format(($row[csf("cons_rate")]),2,'.', '')/$txt_exchange_rate;
											echo number_format($calculateConsRate,4,'.', ''); 
										}
										else if($row[csf("entry_form")]==18 || $row[csf("entry_form")]==46 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3)
										{
											//echo number_format($recv_rate_amnt_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["order_rate"],2);
											$calculateConsRate=number_format(($row[csf("cons_rate")]),2,'.', '')/$txt_exchange_rate;
											echo number_format($calculateConsRate,4,'.', '');
										}
									
										else
										{
											//echo number_format($row[csf("order_rate")],2);  
											$calculateConsRate=number_format(($row[csf("cons_rate")]),2,'.', '')/$txt_exchange_rate;
											echo number_format($calculateConsRate,4,'.', '');
										}
									}
								?></td>
								<td width="100" align="right"><? 
									if($txt_exchange_rate>1)
									{
										if ($row[csf("entry_form")]==14 ) 
										{
											//echo number_format($row[csf("cons_amount")],2); 
											$calculateConsAmount=number_format(($totalAmount),2,'.', '')/$txt_exchange_rate;
											echo number_format($calculateConsAmount,2,'.', ''); 
										}
										else if($row[csf("entry_form")]==18 || $row[csf("entry_form")]==46 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3)
										{
											//echo number_format($recv_rate_amnt_arr[$row[csf("batch_id")]][$row[csf("prod_id")]]["order_amount"],2);
											$calculateConsAmount=number_format(($totalAmount),2,'.', '')/$txt_exchange_rate;
											echo number_format($calculateConsAmount,2,'.', ''); 
										}
										else
										{
											//echo number_format($row[csf("order_amount")],2);  
											$calculateConsAmount=number_format(($totalAmount),2,'.', '')/$txt_exchange_rate;
											echo number_format($calculateConsAmount,2,'.', ''); 
										}
									}
								?></td>
								<td width="80" align="center"><p><? echo $user_arr[$row[csf("inserted_by")]]; ?>&nbsp;</p></td>
								<td width="120"><p><? echo $row[csf("insert_date")]; ?>&nbsp;</p></td>
								<td><p><? echo $row[csf("remarks")]; ?>&nbsp;</p></td>
							</tr>
							<?
							$i++;	
							$total_cons_amount+=$row[csf("cons_amount")]/$txt_exchange_rate;
							$total_order_amount+=$row[csf("order_amount")];
						}
						?>
						</table>
					<table width="4330" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" > 
						<tfoot>
							<th width="30">&nbsp;</th>
							<th width="50">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="110">&nbsp;</th>
							<th width="110">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th> 
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="100">&nbsp;</th>
							
							<th width="50">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th>
							
							<th width="150">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="150">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80"></th>
							<th width="80">Total</th>
							
							
							<th width="80" align="right" id="value_issue_qnty_kg"><? echo number_format($total_issue_qnty_kg,2); ?></th>
							<th width="80" align="right" id="value_rcv_rtn_kg"><? echo number_format($total_rcv_rtn_kg,2); ?></th>
							<th width="80" align="right" id="value_transfe_out_kg"><? echo number_format($total_transfe_out_kg,2); ?></th>
							<th width="80" align="right" id="value_issue_qnty_yds"><? echo number_format($total_issue_qnty_yds,2); ?></th>
							<th width="80" align="right" id="value_rcv_rtn_yds"><? echo number_format($total_rcv_rtn_yds,2); ?></th>
							<th width="80" align="right" id="value_transfe_out_yds"><? echo number_format($total_transfe_out_yds,2); ?></th>
							<th width="80" align="right" id="value_issue_qnty_mtr"><? echo number_format($total_issue_qnty_mtr,2); ?></th>
							<th width="80" align="right" id="value_rcv_rtn_mtr"><? echo number_format($total_rcv_rtn_mtr,2); ?></th>
							<th width="80" align="right" id="value_transfe_out_mtr"><? echo number_format($total_transfe_out_mtr,2); ?></th> 
							<th width="80">&nbsp;</th>
							<th width="100" align="right" id="value_total_cons_amount"><? echo number_format($total_cons_amount,2); ?></th> 

							<th width="100">&nbsp;</th>
							<th width="100" align="right" id="value_total_order_amount"><? echo number_format($total_order_amount,2); ?></th>

							<th width="80">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th>&nbsp;</th>
						</tfoot>
					</table> 
				</div>  
			</fieldset>
			<?
		}
	}
	else if($rpt_type==3)
	{
		$sql_issue=sql_select("select id, knit_dye_source, knit_dye_company from inv_issue_master where status_active=1 and is_deleted=0 and company_id in($cbo_company_id)");
		$issue_data=array();
		foreach($sql_issue as $row)
		{
			$issue_data[$row[csf("id")]]["knit_dye_source"]=$row[csf("knit_dye_source")];
			$issue_data[$row[csf("id")]]["knit_dye_company"]=$row[csf("knit_dye_company")];
		}
		$sql_cond="";
		if($cbo_store_id != "") $sql_cond.=" and b.store_id in($cbo_store_id)";
		if(count($all_batch_id_arr)>0) $sql_cond.=" and b.pi_wo_batch_no in(".implode(",",$all_batch_id_arr).")";
		//if(count($all_ord_trans_id_arr)>0) $sql_cond.=" and b.id in(".implode(",",$all_ord_trans_id_arr).")";
		/*if(count($all_ord_trans_id_arr)>0)
		{
			$all_ord_trans_id_arr=implode(",",array_filter(array_unique($all_ord_trans_id_arr)));
		    if($all_ord_trans_id_arr!="")
		    {
		        $all_ord_trans_id_arr=explode(",",$all_ord_trans_id_arr);  
		        $all_ord_trans_id_arr_chnk=array_chunk($all_ord_trans_id_arr,999);
		        $sql_cond=" and";
		        foreach($all_ord_trans_id_arr_chnk as $dtls_id)
		        {
		        if($sql_cond==" and")  $sql_cond.="(b.id in(".implode(',',$dtls_id).")"; else $sql_cond.=" or b.id in(".implode(',',$dtls_id).")";
		        }
		        $sql_cond.=")";
		        //echo $sql_cond;die;
		    }
			//$sql_cond.=" and b.id in(".implode(",",$all_ord_trans_id_arr).")";
		}*/
		if( $txt_date_from !="" && $txt_date_to !="")
		{
			if($cbo_based_on==1) $sql_cond.= " and b.transaction_date between '$txt_date_from' and '$txt_date_to'"; else $sql_cond.= " and b.insert_date between '$txt_date_from' and '$txt_date_to'";
		}
		$sql_summery="select a.po_breakdown_id as po_id, b.mst_id, b.id as trans_id, b.pi_wo_batch_no as batch_id, b.prod_id, b.transaction_type, b.transaction_date, b.store_id, b.body_part_id, b.fabric_shade, c.detarmination_id as fabric_description_id, c.gsm, c.dia_width as width, c.color as color_id, b.remarks, b.cons_uom, a.quantity
		from order_wise_pro_details a, inv_transaction b, product_details_master c $tmpTable      
		where a.trans_id=b.id and b.prod_id=c.id and a.prod_id=c.id $whereCondition and a.entry_form in(37,52,14,46,18) and b.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.company_id in($cbo_company_id) $sql_cond";
		//echo $sql_summery;
		
		$dtls_data=sql_select($sql_summery);
		ob_start();
		?>
		<fieldset style="width:1820px;">
			<table cellpadding="0" cellspacing="0" width="1800">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="42" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<!--<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="42" style="font-size:16px"><strong><?// echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <?// if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>-->
			</table>
			<table width="1820" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
				<thead>
                	<tr>
                    	<th width="30" rowspan="2">SL</th><!--620 == 7-->
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="100" rowspan="2">Style No</th>
                        <th width="100" rowspan="2">Fabric Booking</th>
                        <th width="70" rowspan="2">Booking Type</th>
                        <th width="100" rowspan="2">Order Number</th>
                        <th width="120" rowspan="2">Dyeing Company</th>
                        
                        <th width="140" rowspan="2">Body Part</th><!--600 == 5-->
                        <th width="100" rowspan="2">Construction</th>
                        <th width="150" rowspan="2">Composition</th>
                        <th width="100" rowspan="2">Internal Ref.</th>
                        <th width="100" rowspan="2">Batch No</th>
                        <th width="100" rowspan="2">Color</th>
                         
                        <th width="160" colspan="2">Kg</th><!--480 == 6-->
                        <th width="160" colspan="2">Yds</th>
                        <th width="160" colspan="2">Mtr</th>
                    </tr>
					<tr>
                    	<th width="80">Receive Qty</th>
                        <th width="80">Issue Qty</th>
                        <th width="80">Receive Qty</th>
                        <th width="80">Issue Qty</th>
                        <th width="80">Receive Qty</th>
                        <th>Issue Qty</th>
                    </tr>
				</thead>
			</table>
			<div style="width:1820px; max-height:350px; overflow-y:scroll;" id="scroll_body">
            <table width="1800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
                <?
                $i=1;
                foreach ($dtls_data as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $rcv_qnty_kg=$issue_qnty_kg=$rcv_qnty_yds=$issue_qnty_yds=$rcv_qnty_mtr=$issue_qnty_mtr=0;
                    if($row[csf("cons_uom")]==12)
                    {
                        if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 ||  $row[csf("transaction_type")]==5)
                        {
                            $rcv_qnty_kg=$row[csf("quantity")];
                            $total_rcv_qnty_kg+=$row[csf("quantity")];
                        }
                        else
                        {
                            $issue_qnty_kg=$row[csf("quantity")];
                            $total_issue_qnty_kg+=$row[csf("quantity")];
                        }
                    }
                    elseif($row[csf("cons_uom")]==27)
                    {
                        if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 ||  $row[csf("transaction_type")]==5)
                        {
                            $rcv_qnty_yds=$row[csf("quantity")];
                            $total_rcv_qnty_yds+=$row[csf("quantity")];
                        }
                        else
                        {
                            $issue_qnty_yds=$row[csf("quantity")];
                            $total_issue_qnty_yds+=$row[csf("quantity")];
                        }
                    }
                    elseif($row[csf("cons_uom")]==23)
                    {
                        if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==4 ||  $row[csf("transaction_type")]==5)
                        {
                            $rcv_qnty_mtr=$row[csf("quantity")];
                            $total_rcv_qnty_mtr+=$row[csf("quantity")];
                        }
                        else
                        {
                            $issue_qnty_mtr=$row[csf("quantity")];
                            $total_issue_qnty_mtr+=$row[csf("quantity")];
                        }
                    }
					
					$knit_dye_com="";//mst_id
					if($row[csf("transaction_type")]==2)
					{
						if($issue_data[$row[csf("mst_id")]]["knit_dye_source"]==1) $knit_dye_com=$company_arr[$issue_data[$row[csf("mst_id")]]["knit_dye_company"]];
						else $knit_dye_com=$supplier_arr[$issue_data[$row[csf("mst_id")]]["knit_dye_company"]];
					}
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="100"><p><? echo $buyer_arr[$job_trans_data[$row[csf("po_id")]]["buyer_name"]]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $job_trans_data[$row[csf("po_id")]]["style_ref_no"]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $batch_data[$row[csf("batch_id")]]["booking_no"]; ?>&nbsp;</p></td>
                        <td width="70" align="center"><p><? if($batch_data[$row[csf("batch_id")]]["is_short"]==1) echo "Short"; else echo "Main"; ?>&nbsp;</p></td>
                        <td width="100" align="center"><p><? echo $job_trans_data[$row[csf("po_id")]]["po_number"]; ?>&nbsp;</p></td>
                        <td width="120" title="<? echo $issue_data[$row[csf("mst_id")]]["knit_dye_source"]; ?>"><p><? echo $knit_dye_com;?>&nbsp;</p></td>
                        
                        <td width="140"><p><? echo $body_part[$row[csf("body_part_id")]]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $constructtion_arr[$row[csf("fabric_description_id")]]; ?>&nbsp;</p></td>
                        <td width="150"><p><? echo $composition_arr[$row[csf("fabric_description_id")]]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $job_trans_data[$row[csf("po_id")]]["grouping"];; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $batch_data[$row[csf("batch_id")]]["batch_no"]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $color_arr[$row[csf("color_id")]]; ?>&nbsp;</p></td>
                        
                        <td width="80" align="right"><? echo number_format($rcv_qnty_kg,2); ?></td>
                        <td width="80" align="right"><? echo number_format($issue_qnty_kg,2); ?></td>
                        <td width="80" align="right"><? echo number_format($rcv_qnty_yds,2); ?></td>
                        <td width="80" align="right"><? echo number_format($issue_qnty_yds,2); ?></td>
                        <td width="80" align="right"><? echo number_format($rcv_qnty_mtr,2); ?></td>
                        <td align="right"><? echo number_format($issue_qnty_mtr,2); ?></td>
                    </tr>
                    <?
                    $i++;	
                }
                ?>
                </table>
                <table width="1800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" > 
                    <tfoot>
                        <th width="30" rowspan="2">&nbsp;</th>
                        <th width="100" rowspan="2">&nbsp;</th>
                        <th width="100" rowspan="2">&nbsp;</th>
                        <th width="100" rowspan="2">&nbsp;</th>
                        <th width="70" rowspan="2">&nbsp;</th>
                        <th width="100" rowspan="2">&nbsp;</th>
                        <th width="120" rowspan="2">&nbsp;</th>
                        
                        <th width="140" rowspan="2">&nbsp;</th>
                        <th width="100" rowspan="2">&nbsp;</th>
                        <th width="150" rowspan="2">&nbsp;</th>
                        <th width="100" rowspan="2">&nbsp;</th>
                        <th width="100" rowspan="2">&nbsp;</th>
                        <th width="100" rowspan="2">Total:</th>
                        
                        <th width="80" align="right" id="value_rcv_qnty_kg"><? echo number_format($total_rcv_qnty_kg,2); ?></th>
                        <th width="80" align="right" id="value_issue_qnty_kg"><? echo number_format($total_issue_qnty_kg,2); ?></th>
                        <th width="80" align="right" id="value_rcv_qnty_yds"><? echo number_format($total_rcv_qnty_yds,2); ?></th>
                        <th width="80" align="right" id="value_issue_qnty_yds"><? echo number_format($total_issue_qnty_yds,2); ?></th>
                        <th width="80" align="right" id="value_rcv_qnty_mtr"><? echo number_format($total_rcv_qnty_mtr,2); ?></th>
                        <th align="right" id="value_issue_qnty_mtr"><? echo number_format($total_issue_qnty_mtr,2); ?></th>
                    </tfoot>
                </table> 
            </div>  
		</fieldset>
        <?
	}
	
    $html = ob_get_contents();
    ob_clean();
    foreach (glob($user_id."*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$rpt_type";
    exit();
}

if($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "test";die();
	?>
	<fieldset style="width:300px;">
        <table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center" >
            <thead>
                <tr>
                    <th width="50">Sl</th>
                    <th width="150">PO Number</th>
                    <th>Ship Date</th>
                </tr>
            </thead>
            <tbody id="tbl_list_search">
                <?
                $po_sql="select a.id as po_id, a.po_number, a.pub_shipment_date
                from  wo_po_break_down a
                where a.is_deleted=0 and a.status_active=1 and a.id in($po_id)";
                
                $i=1;
                //echo $mrr_sql;//die;
                
                $dtlsArray=sql_select($po_sql);
                
                foreach($dtlsArray as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td align="center"><p><? echo $i; ?></p></td>
                        <td><p><? echo $row[csf('po_number')]; ?></p></td> 
                        <td align="center"><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?>&nbsp;</p></td>
                    </tr>
                    <?
                    $i++;
                }
                
                ?>
            </tbody>
        </table>
	</fieldset>
	<?
	exit();
}//Knit Finish end

?>