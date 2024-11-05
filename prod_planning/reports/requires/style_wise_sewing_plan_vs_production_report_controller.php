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
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "",0 );
	exit();     	 
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
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
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'style_wise_sewing_plan_vs_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} // Job Search end

if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data); 
	?>	
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#order_no_id").val(splitData[0]); 
			$("#order_no_val").val(splitData[1]); 
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="order_no_id" />
	<input type="hidden" id="order_no_val" />
	<?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$data[1]";
	if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
	$job_no=str_replace("'","",$txt_job_id);
	if($db_type==0)
	{
		if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and FIND_IN_SET(b.job_no_prefix_num,'$data[2]')";
	}
	else if($db_type==2)
	{
		if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and ',' || b.job_no_prefix_num || ',' LIKE '%$data[2]%' ";
	}
	
	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no";
	//echo $sql;
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$arr=array(1=>$buyer);
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "style_wise_sewing_plan_vs_production_report_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit(); 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$job_no=str_replace("'","",$txt_job_no);
	$order_no=str_replace("'","",$txt_order_no);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	
	$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$line_name_library=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	//--------------------------------------------------------------------------------------------------------------------
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	
	if ($job_no==""){ 
		$job_no_cond="";
	} else{ 
		$job_no_cond=" and a.job_no_prefix_num in ('$job_no') ";
	}
	if ($cbo_location_id==0){ 
		$location_id_cond="";
	} else{ 
		$location_id_cond=" and c.location_id=$cbo_location_id ";
	}
	if($order_no=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim($order_no)."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}
	$start_date_db=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
	
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
	
	if($cbo_date_type==2){
		$date_cond=" and pd.plan_date between '$start_date' and '$end_date'";
	}
	else
	{
		$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	
	if($db_type==0){
		 $lead_day="DATEDIFF(b.pub_shipment_date,b.po_received_date) as  date_diff";
	}
	else if($db_type==2){
		 $lead_day="(b.pub_shipment_date-b.po_received_date) as  date_diff";
	}
	
	if ($cbo_location_id==0){ 
		$location_id_cond_res="";
	} else {
		$location_id_cond_res=" and location_id=$cbo_location_id ";
	}
	if ($cbo_location_id==0){ 
		$location_id_cond_sewing="";
	} else{ 
		$location_id_cond_sewing=" and a.location=$cbo_location_id ";
	}
	
	
	$sql_data="select c.company_id,c.location_id,c.line_id,c.plan_qnty,c.plan_id,c.start_hour,c.end_hour,c.duration,$lead_day,c.comp_level,c.first_day_output,c.increment_qty,c.terget,c.day_wise_plan,c.company_id,c.item_number_id ,c.off_day_plan,c.extra_param,  b.id as po_id,b.pub_shipment_date,b.po_quantity,b.plan_cut,c.start_date,c.end_date, b.po_number,a.buyer_name,a.job_no_prefix_num,a.job_no,a.style_ref_no as style ,a.set_smv,pd.plan_qnty as pdplan_qnty,pd.plan_date,f.machine_line
	from  wo_po_break_down b,wo_po_details_master a,ppl_sewing_plan_board_powise pp,ppl_sewing_plan_board_dtls pd, ppl_sewing_plan_board c,lib_sewing_line e,wo_pre_cost_mst f where  a.job_no=b.job_no_mst and a.job_no=f.job_no and b.id=pp.po_break_down_id and pp.plan_id=pd.plan_id and pp.plan_id=c.plan_id and c.company_id in($company_id) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.line_id=e.id
	AND a.status_active = 1  AND a.is_deleted = 0 AND e.status_active = 1  AND e.is_deleted = 0  AND f.status_active = 1  AND f.is_deleted = 0	
	  $buyer_id_cond $po_cond $date_cond $job_no_cond $location_id_cond
	order by e.line_name,pd.plan_date
	";
	//echo $sql_data;die;
	$data_result=sql_select($sql_data);
	$sewing_data=array();
	$rowcount=count($data_result);
	$styleArr=array();
	$line_total=array();
	foreach( $data_result as $row)
	{
		$date_found[strtotime($row[csf("plan_date")])]=strtotime($row[csf("plan_date")]);
		$styleArr[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['po_qty'][$row[csf("po_id")]]=$row[csf("po_quantity")];
		$styleArr[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['plan_cut'][$row[csf("po_id")]]=$row[csf("plan_cut")];
		$sewing_data[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['plan_qnty']+=$row[csf("plan_qnty")];
		$sewing_data[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['item_id']=$row[csf("item_number_id")];
		$sewing_data[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['buyer']=$row[csf("buyer_name")];
		$sewing_data[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['job']=$row[csf("job_no_prefix_num")];
		$sewing_data[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['machine_line']=$row[csf("machine_line")];
		$sewing_data[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['style']=$row[csf("style")];
		$sewing_data[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['set_smv']=$row[csf("set_smv")];
		$sewing_data[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['po_number']=$row[csf("po_number")];
		$sewing_data[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['pub_shipment']=$row[csf("pub_shipment_date")];
		$sewing_data[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]][date("M-d",strtotime($row[csf("plan_date")]))]['day_wise']=$row[csf("pdplan_qnty")];
		//$sewing_data[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['pdplan_qntyLine']+=$row[csf("pdplan_qnty")];
		$sewing_data[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['pdplan_qntyLine'][$row[csf("plan_date")]]=$row[csf("pdplan_qnty")];
		
		
		$sewing_data[$row[csf("line_id")]][$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("job_no")]]['plan_date'][strtotime($row[csf("plan_date")])]=strtotime($row[csf("plan_date")]);
		$line_total[$row[csf("line_id")]][date("M-d",strtotime($row[csf("plan_date")]))]+=$row[csf("pdplan_qnty")];
		$grand_total[date("M-d",strtotime($row[csf("plan_date")]))]+=$row[csf("pdplan_qnty")];
	}
	$start_date_db=date("Y-m-d",min($date_found));
	$end_date_db=date("Y-m-d",max($date_found));
	
	/*$total_sewing_data_arr=array();
	$total_sewing_data_arr2=array();
	$total_sewing_data_arr3=array();
	
	$prod_res_arr=array();
	$prod_reso=sql_select("select id,line_number from  prod_resource_mst where company_id in($company_id)  and is_deleted=0 $location_id_cond_res order by id ");
	foreach($prod_reso as $row)
	{
		$line_ids=explode(",",$row[csf('line_number')]);
		$prod_res_arr[$row[csf('id')]]=$line_ids[0];
	}
	
	$tot_sewing_prod=sql_select( "select a.company_id,a.po_break_down_id,a.location,a.production_date,a.item_number_id,a.sewing_line,a.prod_reso_allo,a.production_type,a.production_quantity as prod_quantity, b.job_no_mst from pro_garments_production_mst a, wo_po_break_down b  where a.po_break_down_id=b.id and  a.company_id in($company_id) and a.status_active=1 and a.is_deleted=0 and a.production_type in(5,4) $location_id_cond_sewing");
	foreach($tot_sewing_prod as $row) 
	{
	$ddate=add_date($start_date_db,$d);
	$prod_date=date("M-d",strtotime($row[csf('production_date')]));
	$prod_reso_allo=$row[csf('prod_reso_allo')]; 
	
	if($row[csf('production_type')]==5){
		$total_sewing_data_arr3[$prod_date]+=$row[csf('prod_quantity')];
		$total_sewing_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('job_no_mst')]][$row[csf('item_number_id')]]['prod_reso_allo']=$row[csf('prod_reso_allo')];
		if($prod_reso_allo==0)
		{
			$total_sewing_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('job_no_mst')]][$row[csf('sewing_line')]][$row[csf('item_number_id')]][$prod_date]['prod_qty']=$row[csf('prod_quantity')];
			$sew_output_date_arr[$row[csf('company_id')]][$row[csf("location")]][$row[csf("job_no_mst")]][$row[csf('sewing_line')]][$row[csf("item_number_id")]]['production_date'][strtotime($row[csf("production_date")])]=$row[csf("production_date")];
		}
		else
		{ 
			$line_number=$prod_res_arr[$row[csf('sewing_line')]];
			$total_sewing_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('job_no_mst')]][$line_number][$row[csf('item_number_id')]][$prod_date]['prod_qty']=$row[csf('prod_quantity')];
			$sew_output_date_arr[$row[csf('company_id')]][$row[csf("location")]][$row[csf("job_no_mst")]][$line_number][$row[csf("item_number_id")]]['production_date'][strtotime($row[csf("production_date")])]=$row[csf("production_date")];
		}
	}
	if($row[csf('production_type')]==4){
		if($prod_reso_allo==0)
		{
			$total_sewing_input_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('job_no_mst')]][$row[csf('sewing_line')]][$row[csf('item_number_id')]]+=$row[csf('prod_quantity')];
			$sew_input_date_arr[$row[csf('company_id')]][$row[csf("location")]][$row[csf("job_no_mst")]][$row[csf("sewing_line")]][$row[csf("item_number_id")]]['production_date'][strtotime($row[csf("production_date")])]=$row[csf("production_date")];
		
		}
		else
		{ 
			$line_number=$prod_res_arr[$row[csf('sewing_line')]];
			$total_sewing_input_data_arr[$row[csf('company_id')]][$row[csf('location')]][$row[csf('job_no_mst')]][$line_number][$row[csf('item_number_id')]]+=$row[csf('prod_quantity')];
			$sew_input_date_arr[$row[csf('company_id')]][$row[csf("location")]][$row[csf("job_no_mst")]][$line_number][$row[csf("item_number_id")]]['production_date'][strtotime($row[csf("production_date")])]=$row[csf("production_date")];
		
		}
	}
	$production_days_data_arr[$row[csf('company_id')]][$row[csf("location")]][$row[csf("job_no_mst")]][$row[csf("sewing_line")]][$row[csf("item_number_id")]][$row[csf("production_date")]]=$row[csf("production_date")];
	}*/
	/*$sql_sewing_prod=sql_select( "select a.company_id,a.po_break_down_id,a.location, a.item_number_id, a.production_quantity as production_quantity,b.job_no_mst from pro_garments_production_mst a , wo_po_break_down b  where a.po_break_down_id=b.id and  a.status_active=1 and a.is_deleted=0 and production_type=5");//Output qty//Actual Qty
	foreach($sql_sewing_prod as $row)
	{
		$total_sewing_data_arr2[$row[csf('company_id')]][$row[csf('location')]][$row[csf('job_no_mst')]][$row[csf('item_number_id')]]['qty']+=$row[csf('production_quantity')];
	}*/
	$num_days=datediff("d",$start_date_db,$end_date_db);
	$total_day=$num_days;
	$width=$total_day*80+1020;
	ob_start();	
	?>
	<div>
	<fieldset style="width:<? echo $width+10; ?>px;">
	<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table"  >
	<thead>
	<th width="40">SL</th>
	<th width="100">Line No</th>
	<th width="90">Buyer</th>
	<th width="90">Style Ref/ Job No</th>
	<th width="90">Ship Date</th>
	<th width="70">Order Qty</th>
	<th width="70">Plan Cut Qty</th>
	<th width="70">Plan Qty</th>
	<th width="80">M/C</th>
	<th width="50">SMV</th>
	<th width="70">Sewing Start date</th>
	<th width="100">Sewing Complete date</th>
	<?
	for($m=0;$m<$num_days;$m++)
	{
	$ddate=add_date($start_date_db,$m)
	?>
	<th width="80"><? echo  date("M-d",strtotime($ddate)); ?></th>
	<?
	}
	?>
	</thead>
	</table>
	
	
	<div style="width:<? echo $width+20; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
	<table width="<? echo $width; ?>" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table">
	<?
	$j=1;$dayWiseMachineTotalArr=array();
	foreach($sewing_data as $line_key=>$company_arr){
		foreach($company_arr as $company_key=>$unit_data_arr) {
			$order_qty_line=0;
			$plan_cut_qty_line=0;
			$planQty_line=0;
			foreach($unit_data_arr as $unit_key=>$jobdata){
				foreach($jobdata as $job_no=>$pdata){
					if ($k%2==0)  
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$item_id=$sewing_data[$line_key][$company_key][$unit_key][$job_no]['item_id'];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>"> 
					<td width="40"    >
					<? echo $j; ?>
					</td>
					<td width="100"  align="center">
					<? 
					echo $line_name_library[$line_key]; 
					?> 
					</td>
					<td width="90"  align="center"  >
					<? 
					echo $buyer_arr[$pdata['buyer']]; 
					?> 
					</td>
					<td width="90"  align="center" style="word-wrap: break-word;word-break: break-all;" >
					<? 
					echo $pdata['style'].'<br>'.$pdata['job']; 
					?> 
					</td>
					<td width="90"  align="center" >
					<? 
					echo change_date_format($pdata['pub_shipment']);
					?> 
					</td>
					<td width="70"  align="right"  >
					<? 
					$order_qty+=array_sum($styleArr[$line_key][$company_key][$unit_key][$job_no]['po_qty']);
					$order_qty_line+=array_sum($styleArr[$line_key][$company_key][$unit_key][$job_no]['po_qty']);
					echo number_format(array_sum($styleArr[$line_key][$company_key][$unit_key][$job_no]['po_qty'])); 
					?> 
					</td>
					<td width="70"  align="right" >
					<? 
					$plan_cut_qty+=array_sum($styleArr[$line_key][$company_key][$unit_key][$job_no]['plan_cut']);
					$plan_cut_qty_line+=array_sum($styleArr[$line_key][$company_key][$unit_key][$job_no]['plan_cut']);
					echo number_format(array_sum($styleArr[$line_key][$company_key][$unit_key][$job_no]['plan_cut'])); 
					?> 
					</td>
					<td width="70"  align="right"  >
					<? 
					/*$planQty+=$sewing_data[$line_key][$company_key][$unit_key][$job_no]['pdplan_qntyLine'];
					$planQty_line+=$sewing_data[$line_key][$company_key][$unit_key][$job_no]['pdplan_qntyLine'];
					echo $sewing_data[$line_key][$company_key][$unit_key][$job_no]['pdplan_qntyLine']; */
					
					$planQty+=array_sum($sewing_data[$line_key][$company_key][$unit_key][$job_no]['pdplan_qntyLine']);
					$planQty_line+=array_sum($sewing_data[$line_key][$company_key][$unit_key][$job_no]['pdplan_qntyLine']);
					echo array_sum($sewing_data[$line_key][$company_key][$unit_key][$job_no]['pdplan_qntyLine']); 
					
					
					?>
					</td>
					<td width="80" align="right"> 								
					<? 
					echo $pdata['machine_line']; 
					?> 
					</td>
					<td width="50" align="center" > 
					<? echo $pdata['set_smv'] ?> 
					</td>
					
					<td width="70" align="center" >
					<? 
					if(min($sewing_data[$line_key][$company_key][$unit_key][$job_no]['plan_date'])){
					echo date("d-m-Y",min($sewing_data[$line_key][$company_key][$unit_key][$job_no]['plan_date']));
					}
					?> 
					</td>
					<td width="100" align="center"> 
					<?
					//echo $sewing_data[$line_key][$company_key][$unit_key][$job_no]['plan_date'];
					if(max($sewing_data[$line_key][$company_key][$unit_key][$job_no]['plan_date'])){
					echo date("d-m-Y",max($sewing_data[$line_key][$company_key][$unit_key][$job_no]['plan_date']));
					}
					?>
					</td>
					<?
					for($m=0;$m<$num_days;$m++)
					{
						?>
						<td align="right" width="80">
							<? 
                            $ddate=add_date($start_date_db,$m);
                            $pdate=date("M-d",strtotime($ddate));
                            $plan_qty=$sewing_data[$line_key][$company_key][$unit_key][$job_no][$pdate]['day_wise'];
                            if($plan_qty==0 || $plan_qty=="")
                            {
								echo "";	
                            }
                            else
                            {
								echo  number_format($plan_qty);
								$dayWiseMachineTotalArr[$pdate]+=$pdata['machine_line'];
                            }
                            ?>
						</td>
						<?
					}
					?>
					</tr>
					<?
					$j++;
				}
			}
			?>
			<tr  bgcolor="#ccc">
			<td align="left" colspan="5"><strong>Sub Total Line : <? echo $line_name_library[$line_key];  ?> </strong></td>
			<td width="70" align="right"><? echo  number_format($order_qty_line,0);?></td>
			<td width="70" align="right"><? echo  number_format($plan_cut_qty_line,0);?></td>
			<td width="70" align="right"><? echo  number_format($planQty_line,0);?></td>
			<td width="80"><? //echo  number_format($yet_prod_qty_grand,0);?></td>
			<td width="50"></td>
			<td width="70"></td>
			<td width="100"></td>
			<?
			for($m=0;$m<$num_days;$m++)
			{
			$ddate=add_date($start_date_db,$m);
			$pdate=date("M-d",strtotime($ddate));
			$lintot=$line_total[$line_key][$pdate];
			?>
			<td width="80" align="right"> 
			<? echo number_format($lintot,0); ?>
			</td>
			<?
			}
			?>
			</tr>
			<?
		}// Unit wise end
	}
	if($num_days!="") //Grand Total
	{
	?>				
	<tr class="tbl_bottom">
        <td colspan="5"> Grand Total </td>
        <td width="70"><? echo  number_format($order_qty,0);?></td>
        <td width="70"><? echo  number_format($plan_cut_qty,0);?></td>
        <td width="70"><? echo  number_format($planQty,0);?></td>
        <td width="80"><? echo  number_format($yet_prod_qty_grand,0);?></td>
        <td width="50"></td>
        <td width="70"></td>
        <td width="100"></td>
        <?
        for($m=0;$m<$num_days;$m++)
        {
        $ddate=add_date($start_date_db,$m);
        $pdate=date("M-d",strtotime($ddate));
        $grandTotal=$grand_total[$pdate]
        ?>
        <td width="80" align="right"> 
        <? echo number_format($grandTotal); ?>
        </td>
        <? } ?>
	</tr>
    
    
	<tr class="tbl_bottom">
        <td colspan="12">Machine Day Total</td>
        <?
        for($m=0;$m<$num_days;$m++)
        {
        $ddate=add_date($start_date_db,$m);
        $pdate=date("M-d",strtotime($ddate));
        ?>
        <td align="right"><? echo $dayWiseMachineTotalArr[$pdate]; ?></td>
        <? } ?>
	</tr>
    
	<?
	}
	?>
	</table>
	</div>
	</fieldset>
	</div>
	<?
	foreach (glob("$user_name*.xls") as $filename) 
	{
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}
?>
      
 