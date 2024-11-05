<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.washes.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$colorname_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

if ($action=="load_drop_down_buyer")
{
	
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 


if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";


if ($action == "job_no_search_popup") 
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');

	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job(str) {
			

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
		}

	</script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Company</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th> Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name", "id,company_name", 1, "--Select--", $company, "", 0);
								?>
							</td>                 
							<td align="center">	
								<?
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref",3=> "Order No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'style_wise_budget_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_job_no_search_list_view") 
{
	$data = explode('**', $data);
	$company_id = $data[0];
	$cbo_year = "";

	$company_con='';
	if(empty($company_id))
	{
		echo "Select Company First";die;
	}else{
		$company_con=" and b.company_name=$company_id";
	}

	$search_by = $data[1];
	$search_string = "'%" . trim($data[2]) . "%'";
	$search_field='';
	if(!empty($data[2]))
	{
		if ($search_by == 1)
			$search_field = " and b.job_no_prefix_num =$data[2]";
		else if ($search_by == 2)
			$search_field = " and b.style_ref_no like ".$search_string;
		else if($search_by == 3)
			$search_field = " and a.po_number like ".$search_string;
	}
	

	$start_date = $data[3];
	$end_date = $data[4];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_arr, 1 => $buyer_short_library);

	if ($db_type == 0)
	{
		$year_field = " ,YEAR(b.insert_date) as year";
        $year_groupby = ", b.insert_date";
	}
	else if ($db_type == 2)
	{
		$year_field = " ,to_char(b.insert_date,'YYYY') as year";
    //$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
		$year_groupby = ", b.insert_date";
	}
	else
	{
		$year_field = "";
  		 // $year_cond = "";
		$year_groupby="";
    } 
    
  
  	$sql = "SELECT  b.id ,b.job_no,a.po_number,a.id as po_id,b.job_no_prefix_num, b.company_name, b.buyer_name $year_field
		    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,a.po_number,b.job_no_prefix_num, b.company_name, b.buyer_name,a.id $year_groupby order by job_no";

  	

	

	 //echo $sql;

	$conclick="id,job_no_prefix_num";
	 $style=$data[5];
	if($style==1)
	{
		$conclick="po_id,po_number";
	}

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Order No", "150,130,140,100", "760", "320", 0, $sql, "js_set_value_job", $conclick, "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,job_no_prefix_num,year,po_number", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}





if($action=="generate_report")
{ 
    $process = array( &$_POST );

   // print_r($process);die;
    extract(check_magic_quote_gpc( $process ));
	//$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	//$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');

	$supplier_arr=return_library_array( "select id,supplier_name from lib_supplier ",'id','supplier_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$approve_arr=return_library_array( "select job_no, approved from wo_pre_cost_mst", "job_no", "approved");
	$team_member_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
	$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

	$job_cond_id=""; 
	$style_cond="";
	$order_cond="";
	$type=str_replace("'","",$type);
   	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=".str_replace("'","",$cbo_company_name)."";

	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		 $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";

	if(str_replace("'","",$hidden_job_id)!="")  $job_cond_id=where_con_using_array(explode(",", str_replace("'","",$hidden_job_id)),0,"a.id");

	else  if (str_replace("'","",$txt_job_no)=="") $job_cond_id=""; else $job_cond_id="and a.job_no_prefix_num like '%".str_replace("'","",$txt_job_no)."%'";

	if(str_replace("'","",$hidden_order_id)!="")  $order_cond= where_con_using_array(explode(",", str_replace("'","",$hidden_order_id)),0,"b.id");

	else  if (str_replace("'","",$txt_order_no)=="") $order_cond=""; else $order_cond="and b.po_number like '%".str_replace("'","",$txt_order_no)."%' ";
	$working_company_cond="";
	$company_cond="";

	if(str_replace("'","",$cbo_working_company)>0)
	{
		$working_company_cond=" and d.working_company_id=$cbo_working_company";
	}
	if(($cbo_company_name)>0)
	{
		$company_cond=" and d.company_id=$cbo_company_name ";
	}


	$cbo_date_type=str_replace("'", "", $cbo_date_type);
	$cbo_company_name=str_replace("'", "", $cbo_company_name);
	 
	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));

	if($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='$txt_int_ref' ";
	if($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='$txt_file_no' ";
	
	
	
  	if($type==1)
  	{


		 $po_number_data=array();
		 $production_data_arr=array();
		 $po_number_id=array();

	 	 
			  $date_cond="";

			  if($cbo_date_type==1)
			  {
			  	if(trim($txt_date_from)=="" || trim($txt_date_to)=="") $date_cond="";
			  	else $date_cond=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
			  }
			  else
			  {
			  	if(trim($txt_date_from)=="" || trim($txt_date_to)=="") $date_cond="";
			  	else $date_cond=" and b.shipment_date between '$txt_date_from' and '$txt_date_to'";
			  }

			
		    $year_cond="";
			$cbo_year=str_replace("'","",$cbo_year_selection);
			if(trim($cbo_year)!=0)
			{
				if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
				else $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			}

			if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";
			  
		 //     $sql_po="SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year,a.gmts_item_id,a.style_ref_no,a.season_buyer_wise, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty,b.unit_price, b.plan_cut,b.po_quantity, b.pub_shipment_date,b.shipment_date,b.file_no,b.grouping,b.po_received_date, c.color_number_id,c.order_quantity as order_quantity,c.plan_cut_qnty,c.color_number_id as color,c.item_number_id as item_id,c.order_rate,c.order_total
			// 	from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c 
			// 	where a.id=b.job_id and a.id=c.job_id  and b.id=c.po_break_down_id   and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1  and c.is_deleted=0   $buyer_id_cond  $job_cond_id $order_cond $date_cond order by b.id ";

			  
		 //    //echo $sql_po;die;
			// $pro_date_sql=sql_select($sql_po);

				$sql_budget="SELECT a.job_no_prefix_num, b.insert_date, a.order_uom,a.job_no, a.buyer_name, a.style_ref_no, b.is_confirmed, a.agent_name, a.avg_unit_price, 
			a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date,
			b.po_quantity, b.po_total_price, b.unit_price, b.grouping, b.file_no,b.shipment_date,a.remarks
			from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c  where a.job_no=b.job_no_mst and a.job_no=c.job_no and c.job_no=b.job_no_mst and c.entry_from=237 and a.company_name in ($cbo_company_name) and a.status_active=1 and
			a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $date_cond $buyer_id_cond  $job_cond_id  $order_cond  $internal_ref_cond $file_no_cond";

		   // echo $sql_budget;


    			$condition= new condition();
    			 $condition->company_name("in($cbo_company_name)");
    			 if(str_replace("'","",$cbo_buyer_name)>0){
    				  $condition->buyer_name("=$cbo_buyer_name");
    			 }
    			 if(str_replace("'","",$txt_job_no) !=''){
    			 	   $txt_job_no=implode(",", explode("*", str_replace("'", "", $txt_job_no)));
    				  $condition->job_no_prefix_num("in($txt_job_no)");
    			 }
    			 if(str_replace("'","",$hidden_job_id) !=''){
    			 	  $hidden_job_id=str_replace("'","",$hidden_job_id);
    				  $condition->jobid_in($hidden_job_id);
    			 }
    			 $condition->is_confirmed("in(1,2)");
    			 if(str_replace("'","",$cbo_date_type) ==1 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
    				 //$condition->country_ship_date(" between '$start_date' and '$end_date'");
    				  $condition->pub_shipment_date(" between '$txt_date_from' and '$txt_date_to'");
    			 }
    			 if(str_replace("'","",$cbo_date_type) ==2 && str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
    			 {
    				 $condition->shipment_date(" between '$txt_date_to' and '$txt_date_to'");
    				 //and b.po_received_date between '$start_date' and '$end_date' 
    				// echo 'FFGG';
    			 }
    			
    			
    			 if(str_replace("'","",$txt_file_no)!='')
    			 {
    				$condition->file_no("=$txt_file_no"); 
    			 }
    			 if(str_replace("'","",$txt_internal_ref)!='')
    			 {
    				$condition->grouping("=$txt_internal_ref"); 
    			 }
    			 if(str_replace("'","",$hidden_order_id)!='')
    			 {
    			 	$hidden_order_id=str_replace("'","",$hidden_order_id);
    				$condition->po_id("in($hidden_order_id)"); 
    			 }
    			 else if(str_replace("'","",$txt_order_no)!='')
    			 {
    			 	$txt_order_no=implode(",", explode("*", str_replace("'", "", $txt_order_no)));
    				$condition->po_number("=$txt_order_no"); 
    			 }
    			 if(str_replace("'","",$txt_season)!='')
    			 {
    				//$condition->season("=$txt_season"); 
    			 }
    			 $condition->init();
    			$yarn= new yarn($condition);
    			//echo $yarn->getQuery(); die;
    			
             
    			$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
    			
    			$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
    			
    			$yarn_des_data=$yarn->getOrderCountCompositionPercentAndTypeWiseYarnQtyAndAmountArray();
    			$yarn_data_array=$yarn->get_By_Precostdtlsid_YarnQtyAmountArray();
    		 	$conversion= new conversion($condition);
    		//	echo $conversion->getQuery(); die;
    		 	//getOrderCountCompositionAndTypeWiseYarnQtyAndAmountArray
    			$conversion_costing_arr=$conversion->getAmountArray_by_order();
    			
    			$conversion= new conversion($condition);
    			$conversion_costing_arr_process=$conversion->getAmountArray_by_order();
    			//$conversion_costing_arr_process=$conversion->getQtyArray_by_order();
    			//echo $conversion->getQuery(); die;
    		   //print_r($conversion_costing_arr_process);
    		 	$trims= new trims($condition);
    			$trims_costing_arr=$trims->getAmountArray_by_order();
    			
    			$fabric= new fabric($condition);
    			$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
    			//	print_r($fabric_costing_arr);
    			$emblishment= new emblishment($condition);
    			$emblishment_costing_arr=$emblishment->getAmountArray_by_order();
    			
    			
    			$emblishment= new emblishment($condition);
    			$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
    			$wash= new wash($condition);
    			$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
    			
    			$commercial= new commercial($condition);
    			$commercial_costing_arr=$commercial->getAmountArray_by_order();
    			 
    			$commission= new commision($condition);
    			$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
    			$other= new other($condition);
    			$other_costing_arr=$other->getAmountArray_by_order(); 
    			//var_dump($other_costing_arr);die;
    			
    		/*	$knit_cost_arr=array(1,2,3,4);$fabric_dyeingCost_arr=array(25,31,32,60,61,62,63,72,80,81,84,85,86,87,38,74,78,79);
    			$fab_finish_cost_arr=array(34,65,66,67,68,69,70,71,73,75,76,77,88,90,91,92,93,100,125,127,128,129);
    			$washing_cost_arr=array(64,82,89);$aop_cost_arr=array(35,36,37);*/
    			$knit_cost_arr=array(1,2,3,4);
    			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
    			$aop_cost_arr=array(35,36,37,40);
    			$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
    			$washing_cost_arr=array(140,142,148,64);
			
			$result_sql_budget=sql_select($sql_budget); 
			$job_arr=array();
			foreach ($result_sql_budget as $value) {
				array_push($job_arr, $value[csf('job_no')]);
			}
			
		

			$count_id_arr=array();

			

			foreach($yarn_des_data as $po_id=>$po_data)
			{
				foreach($po_data as $count=>$count_value)
				{
					
					array_push($count_id_arr,$count );
				}
			} 
			$count_id_arr=array_unique($count_id_arr);
		
		  ob_start();
		  	$job_cond=where_con_using_array(array_unique($job_arr),1,"f.job_no");
		    $sql_yarn="select  f.cons_qnty,f.amount,f.job_no,f.id from wo_pre_cost_fab_yarn_cost_dtls f where  f.is_deleted=0 and f.status_active=1  $job_cond ";
		   // echo $sql_yarn;
			$data_arr_yarn=sql_select($sql_yarn);
			$YarnData=array();
			foreach($data_arr_yarn as $row)
			{
				$YarnData[$row[csf('job_no')]]['cons_qnty']+=$row[csf("cons_qnty")];
				$YarnData[$row[csf('job_no')]]['amount']+=$yarn_data_array[$row[csf("id")]]['amount'];
			}
	  
			$com_arr=explode(",", $cbo_company_name);
			$company_name_str='';
			foreach ($com_arr as $key => $value) {
				$company_name_str.=$company_arr[$value].",";
			}
		 ?>
		 <style type="text/css">
		 	hr {
			  border-top: 1px solid black;
			}
		 </style>
  		<fieldset style="width:3330px;">
        	   <table  cellspacing="0" style="justify-content: center;text-align: center;width: 3320px;" >
                    <tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
                           <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" ><?php echo $report_title; ?></td>
                    </tr>
                    <tr style="border:none;justify-content: center;text-align: center;">
                           <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
                            Company Name:<? echo chop($company_name_str,","); ?>                                
                           </td>
                     </tr>
                     <tr style="border:none;justify-content: center;text-align: center;">
                           <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? echo "Production date ".change_date_format(str_replace("'","",$txt_date_from))." to ". change_date_format(str_replace("'","",$txt_date_to)) ;?>
                           </td>
                     </tr>
              </table>
             <br />	


            

             <br>

         
            
             <div style="width:3320px; margin-left:18px" >
             		
                    <table cellspacing="0" border="1" class="rpt_table"  width="3300" rules="all"   style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body">
                    	  <thead>
             				<tr>
             					<th width="40" rowspan="2">SL</th>
             					<th width="110" rowspan="2">Buyer</th>
             					<th width="110" rowspan="2">Job No</th>
             					<th width="110" rowspan="2">Order No</th>
             					<th width="110" rowspan="2">Approve Status</th>
             					<th width="110" rowspan="2">Order Status</th>
             					<th width="110" rowspan="2">Repeat No</th>
             					<th width="110" rowspan="2">Style</th>
             					<th width="110" rowspan="2">Item Name</th>
             					<th width="110" rowspan="2">Dealing</th>
             					<th width="110" rowspan="2">Ship. Date</th>
             					<th width="110" rowspan="2">Job  Qty</th>
             					<th width="110" rowspan="2">UOM</th>
             					<th width="110" rowspan="2">Total Plan Order Qnty(Pcs)</th>
             					<th width="110" rowspan="2">Total Order Qnty(Pcs)</th>
             					<th width="110" rowspan="2">Avg Unit Price</th>
             					<th width="110" rowspan="2">Order Value</th>
             					

             					<th colspan="5" width="560">Fabric Cost</th>

             					<th width="110" rowspan="2">Trim Cost</th>

             					<th colspan="5" width="560">Embell. Cost</th>


             					<th width="110" rowspan="2">Commercial Cost</th>

             					<th colspan="2" width="215">Commission</th>


             					<th width="110" rowspan="2">Testing Cost</th>
             					<th width="110" rowspan="2">Total Cost</th>
             					<th width="110" rowspan="2">Total Margin</th>
             					<th width="110" rowspan="2">Margin CM</th>
             					<th width="110" rowspan="2">Composition</th>
             					<th width="110" rowspan="2">Type </th>

             					
             					<th colspan="<?=count($count_id_arr);?>" width="<?=count($count_id_arr)*115;?>">Yarn Count</th>

             					<th width="110" rowspan="2">Total Yarn </th>
             					<th width="110" rowspan="2">Remarks </th>
             					
             				</tr>
             				<tr >
		                       

		                       <th width="100" >Yarn Req.</th>
		                       <th width="115" >Total Yarn Cons (Pcs)</th>
		                       <th width="115" >Fabric Rate (KG)</th>
		                       <th width="115" >Fabric Amount (Pcs)</th>
		                       <th width="115" >Total Fabric Amount</th>

		                       <th width="100" >Printing</th>
		                       <th width="115" >Embroidery</th>
		                       <th width="115" >Special Works</th>
		                       <th width="115" >Wash Cost</th>
		                       <th width="115" >Other</th>

		                       <th width="100" >Foreign</th>
		                       <th width="115" >Local</th>

		                      
		                      

		                       <?php foreach ($count_id_arr as $count_id): ?>
		                       		 <th width="115" ><?php echo $yarn_count_library[$count_id] ?></th>
		                       <?php endforeach ?>

		                      
		                       
		                      
		                    </tr>
             			</thead>
		                	
                    	
		                <tbody >

		                	
                    	
							
		                	<?
		                		$i=1;
		                		$style_wise_data=array();
		                		$style_count_com_data=array();
		                		$style_com_type_data=array();
		                		foreach ($result_sql_budget as $row) 
		                		{

		                			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

		                			$approve_status=$approve_arr[$row[csf('job_no')]];

		                			
		                			$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
		                			
		                			
		                			$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];

		                			$yarn_descrip_data=$yarn_des_data[$row[csf('po_id')]];
                					$yarn_required_qnty=0; $amount=0;
                					$yarn_description_data_arr=array();
                					$total_yarn=0;
                					$yarn_costing=0;
                					foreach($yarn_descrip_data as $count=>$count_value)
                					{
                						foreach($count_value as $Composition=>$composition_value)
                						{
                							foreach($composition_value as $percent=>$percent_value)
                							{	
                								foreach($percent_value as $type=>$qty_amt)
                								{
                									$count_id=$count;//$yarnRow[0];
                									$copm_one_id=$Composition;//$yarnRow[1];
                									$percent_one=$percent;//$yarnRow[2];
                									$type_id=$type;//$yarnRow[5];
                									$yarn_required_qnty+=$qty_amt['qty'];
                									$amount=$qty_amt['amount'];
                									
                									//$yarn_description_data_arr[$count_id]['qty']+=$yarn_required_qnty;
                									//$yarn_description_data_arr[$count_id]['amount']+=$amount;

                									$yarn_description_data_arr[$copm_one_id][$type_id][$count_id]['qty']+=$qty_amt['qty'];
													$yarn_description_data_arr[$copm_one_id][$type_id][$count_id]['amount']+=$amount;
													//$yarn_costing+=$YarnData[$row[csf('job_no')]][$copm_one_id][$type_id][$count_id]['cons_qnty'];

													$total_yarn+=$qty_amt['qty'];
                								}
                							}
                						}
                					} 
                					$cov_cost=0;
                					$conv_cost_arr=$conversion_costing_arr_process[$row[csf('po_id')]];
                					// echo "<pre>";
                					// print_r($conv_cost_arr);
                					// echo "</pre>";

                					foreach($conv_cost_arr as $process => $process_qnty) 
                					{
                						//echo "<pre>$process=$process_qnty</pre>";
                						$cov_cost+=$process_qnty;
                					}
                					//echo "<pre>cov_cost=$cov_cost</pre>";

                					$yarn_costing=$YarnData[$row[csf('job_no')]]['cons_qnty'];

                					$yarn_cost_amt=$YarnData[$row[csf('job_no')]]['amount'];
                					//$yarn_costing_arr[$row[csf('po_id')]];

                					$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]);
                					if(is_infinite($fab_purchase_knit) || is_nan($fab_purchase_knit)){$fab_purchase_knit=0;}
                					$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
                					if(is_infinite($fab_purchase_woven) || is_nan($fab_purchase_woven)){$fab_purchase_woven=0;}
                					//echo  $fab_purchase_knit.'='. $fab_purchase_woven.'<br>';
                					$fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
                					//echo "<pre>fab_purchase=$fab_purchase</pre>";


                					$all_cost=$fab_purchase+$cov_cost;

                					//fabric_rate=Fabric cost+Yarn Cost+ All type of conversion cost] / Yarn Req.
                					if(!empty($yarn_required_qnty)){
                						$fabric_rate=$all_cost/$yarn_required_qnty;
                					}
                					else{
                						$fabric_rate=0;
                					}

                					$trim_amount= $trims_costing_arr[$row[csf('po_id')]];
									if(is_infinite($trim_amount) || is_nan($trim_amount)){$trim_amount=0;}

									$print_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][1];
									if(is_infinite($print_amount) || is_nan($print_amount)){$print_amount=0;}
									$embroidery_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][2];
									if(is_infinite($embroidery_amount) || is_nan($embroidery_amount)){$embroidery_amount=0;}
									$special_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][4];
									if(is_infinite($special_amount) || is_nan($special_amount)){$special_amount=0;}
									$wash_cost=$emblishment_costing_arr_name_wash[$row[csf('po_id')]][3];
									if(is_infinite($wash_cost) || is_nan($wash_cost)){$wash_cost=0;}
									$other_amount=$emblishment_costing_arr_name[$row[csf('po_id')]][5];
									if(is_infinite($other_amount) || is_nan($other_amount)){$other_amount=0;}
									$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
									if(is_infinite($foreign) || is_nan($foreign)){$foreign=0;}
									$local=$commission_costing_arr[$row[csf('po_id')]][2];
									if(is_infinite($local) || is_nan($local)){$local=0;}
									$test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
									if(is_infinite($test_cost) || is_nan($test_cost)){$test_cost=0;}
									$freight_cost= $other_costing_arr[$row[csf('po_id')]]['freight'];
									if(is_infinite($freight_cost) || is_nan($freight_cost)){$freight_cost=0;}
									$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
									if(is_infinite($inspection) || is_nan($inspection)){$inspection=0;}
									$certificate_cost=$other_costing_arr[$row[csf('po_id')]]['certificate_pre_cost'];
									if(is_infinite($certificate_cost) || is_nan($certificate_cost)){$certificate_cost=0;}
									$common_oh=$other_costing_arr[$row[csf('po_id')]]['common_oh'];
									if(is_infinite($common_oh) || is_nan($common_oh)){$common_oh=0;}
									$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];
									if(is_infinite($currier_cost) || is_nan($currier_cost)){$currier_cost=0;}

                					$commercial_cost=$commercial_costing_arr[$row[csf('po_id')]];

                					$total_cost=$trim_amount+($print_amount+$embroidery_amount+$special_amount+$wash_cost+$other_amount)+$commercial_cost+$foreign+$local+$test_cost;

                					$total_margin=$row[csf('po_total_price')]-$total_cost;
                					$margin_cm=0;
                					if($row[csf('po_total_price')]>0)
                					{
                						$margin_cm=$total_margin/$row[csf('po_total_price')];
                					}
                					
		                			$span=0;

		                			foreach ($yarn_description_data_arr as $copm_one_id => $com_data) 
		                			{
		                				foreach ($com_data as $type_id => $type_data) 
		                				{
		                					$span++;
		                				}
		                			}
		                			$row_span=0;
		                			
		                			foreach ($yarn_description_data_arr as $copm_one_id => $com_data) 
		                			{
		                				foreach ($com_data as $type_id => $type_data) 
		                				{


											if($approve_status==1) $is_approve="Approved"; else $is_approve="No";
				                			
				                			 
				                			 	if ($row_span==0)
				                			 	{ 
				                			 		$gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
													foreach($gmts_item_id as $item_id)
													{
														if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
													}
				                			 		$style_wise_data[$row[csf('style_ref_no')]]['buyer_name'].=$buyer_short_library[$row[csf('buyer_name')]]."***";
		                							$style_wise_data[$row[csf('style_ref_no')]]['job_no_prefix_num'].=$row[csf('job_no_prefix_num')]."***";
		                							$style_wise_data[$row[csf('style_ref_no')]]['po_number'].=$row[csf('po_number')]."***";
		                							$style_wise_data[$row[csf('style_ref_no')]]['is_approve'].=$is_approve."***";
		                							$style_wise_data[$row[csf('style_ref_no')]]['order_status'].=$order_status[$row[csf('is_confirmed')]]."***";
		                							$style_wise_data[$row[csf('style_ref_no')]]['repeat_no'].=$row[csf('repeat_no')]."***";
		                							$style_wise_data[$row[csf('style_ref_no')]]['style_ref_no'].=$row[csf('style_ref_no')]."***";
		                							$style_wise_data[$row[csf('style_ref_no')]]['gmts_item'].=$gmts_item."***";
		                							$style_wise_data[$row[csf('style_ref_no')]]['dealing_marchant'].=$team_member_arr[$row[csf('dealing_marchant')]]."***";
		                							$style_wise_data[$row[csf('style_ref_no')]]['shipment_date'].=change_date_format($row[csf('shipment_date')])."***";
		                							$style_wise_data[$row[csf('style_ref_no')]]['po_quantity']+=$row[csf('po_quantity')];
		                							$style_wise_data[$row[csf('style_ref_no')]]['order_uom'].=$unit_of_measurement[$row[csf('order_uom')]]."***";
		                							$style_wise_data[$row[csf('style_ref_no')]]['plan_cut_qnty']+=$plan_cut_qnty;
		                							$style_wise_data[$row[csf('style_ref_no')]]['order_qty_pcs']+=$order_qty_pcs;
		                							$style_wise_data[$row[csf('style_ref_no')]]['po_total_price']+=$row[csf('po_total_price')];
		                							$style_wise_data[$row[csf('style_ref_no')]]['avg_unit_price']+=$row[csf('avg_unit_price')];
		                							$style_wise_data[$row[csf('style_ref_no')]]['yarn_required_qnty']+=$yarn_required_qnty;
		                							$style_wise_data[$row[csf('style_ref_no')]]['yarn_costing']=$yarn_costing;
		                							$style_wise_data[$row[csf('style_ref_no')]]['fabric_rate']+=$fabric_rate;
		                							$style_wise_data[$row[csf('style_ref_no')]]['fabric_amount']+=$yarn_costing*$fabric_rate;
		                							$style_wise_data[$row[csf('style_ref_no')]]['total_fabric_amount']+=$yarn_costing*$fabric_rate*$plan_cut_qnty;
		                							$style_wise_data[$row[csf('style_ref_no')]]['trim_amount']+=$trim_amount;
		                							$style_wise_data[$row[csf('style_ref_no')]]['print_amount']+=$print_amount;
		                							$style_wise_data[$row[csf('style_ref_no')]]['embroidery_amount']+=$embroidery_amount;
		                							$style_wise_data[$row[csf('style_ref_no')]]['special_amount']+=$special_amount;
		                							$style_wise_data[$row[csf('style_ref_no')]]['wash_cost']+=$wash_cost;
		                							$style_wise_data[$row[csf('style_ref_no')]]['other_amount']+=$other_amount;
		                							$style_wise_data[$row[csf('style_ref_no')]]['commercial_cost']+=$commercial_cost;
		                							$style_wise_data[$row[csf('style_ref_no')]]['foreign']+=$foreign;
		                							$style_wise_data[$row[csf('style_ref_no')]]['local']+=$local;
		                							$style_wise_data[$row[csf('style_ref_no')]]['test_cost']+=$test_cost;
		                							$style_wise_data[$row[csf('style_ref_no')]]['total_cost']+=$total_cost;
		                							$style_wise_data[$row[csf('style_ref_no')]]['total_margin']+=$total_margin;
		                							$style_wise_data[$row[csf('style_ref_no')]]['margin_cm']+=$margin_cm;
		                							$style_wise_data[$row[csf('style_ref_no')]]['span']+=$span;
		                							$style_wise_data[$row[csf('style_ref_no')]]['coversation_cost'].=$all_cost.",";
		                							$style_wise_data[$row[csf('style_ref_no')]]['all_cost']+=$all_cost;
		                							$style_wise_data[$row[csf('style_ref_no')]]['yarn_cost_amt']=$yarn_cost_amt;
		                							

				                			 		
							                    
							                      } 
							                     
		                     					  foreach ($count_id_arr as $count_id)
		                     					  { 
		                     					  		
		                     					  		 $style_count_com_data[$row[csf('style_ref_no')]][$copm_one_id][$type_id][$count_id]['qty']+=$yarn_description_data_arr[$copm_one_id][$type_id][$count_id]['qty'];
		                     					   } 
		 
			                     				 if ($row_span==0)
			                     				{ 
								                     $style_wise_data[$row[csf('style_ref_no')]]['total_yarn']+=$total_yarn;
								                     $style_wise_data[$row[csf('style_ref_no')]]['remarks'].=$row[csf('remarks')]."***";
								                     $row_span++; 
							                      } 

							                 
							                    
				                			
				                		}
		                			}
		                			
		                		}
		                		$i=1;
		                		foreach ($style_count_com_data as $style_ref_no => $style_data) 
		                		{
		                			
		                			$span=0;
		                			foreach ($style_data as $copm_one_id => $comp_data) 
		                			{
		                				foreach ($comp_data as $type_id => $type_data) 
		                				{
		                					$span++;
		                				}
		                			}
		                			$style_wise_data[$style_ref_no]['span']=$span;
		                		}
		                		foreach ($style_count_com_data as $style_ref_no => $style_data) 
		                		{
		                			$span=$style_wise_data[$style_ref_no]['span'];
		                			$row_span=0;
		                			$all_cost=$style_wise_data[$style_ref_no]['all_cost'];
		                			$yarn_cost_amt=$style_wise_data[$style_ref_no]['yarn_cost_amt'];
		                			$yarn_required_qnty=$style_wise_data[$style_ref_no]['yarn_required_qnty'];
		                			$cost=$all_cost+$yarn_cost_amt;


		                			$fab_rate=0;
		                			if($yarn_required_qnty>0)
		                			{
		                				$fab_rate=$cost/$yarn_required_qnty;

		                			}
		                			

		                			foreach ($style_data as $copm_one_id => $comp_data) 
		                			{
		                				foreach ($comp_data as $type_id => $type_data) 
		                				{
		                					
		                					
		                					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

		                					?>
		                						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
		                							<?php if ($row_span==0)
				                			 		{
				                			 			?>
	        						                     <td  rowspan="<?=$span;?>"><? echo $i++; ?></td>
	        						                     <td  rowspan="<?=$span;?>"><p><? echo implode(", ", array_unique(explode("***", chop($style_wise_data[$style_ref_no]['buyer_name'],"***")))); ?></p></td>
	        						                     <td  rowspan="<?=$span;?>"><p><? echo implode(", ", array_unique(explode("***", chop($style_wise_data[$style_ref_no]['job_no_prefix_num'],"***")))); ?></p></td>
	        						                     <td  rowspan="<?=$span;?>"><p><? echo implode(", ", array_unique(explode("***", chop($style_wise_data[$style_ref_no]['po_number'],"***")))); ?></p> </td>
	        						                     <td  rowspan="<?=$span;?>"><p><? echo implode(", ", array_unique(explode("***", chop($style_wise_data[$style_ref_no]['is_approve'],"***"))));  ?></p></td>
	        						                     <td  rowspan="<?=$span;?>"><p><? echo implode(", ", array_unique(explode("***", chop($style_wise_data[$style_ref_no]['order_status'],"***"))));  ?></p></td>
	        						                     <td  rowspan="<?=$span;?>"><p><? echo implode(", ", array_unique(explode("***", chop($style_wise_data[$style_ref_no]['repeat_no'],"***"))));  ?></p></td>
	        						                     <td  rowspan="<?=$span;?>"><p><? echo $style_ref_no; ?></p></td>
	        						                     <td  rowspan="<?=$span;?>">
	        						                     	<p>
	        											        <? echo  implode(", ", array_unique(explode("***", chop($style_wise_data[$style_ref_no]['gmts_item'],"***")))); ?>
	        													
	        												</p>
	        											</td>
	        						                     <td  rowspan="<?=$span;?>"><p><? echo implode(", ", array_unique(explode("***", chop($style_wise_data[$style_ref_no]['dealing_marchant'],"***"))));  ?></p></td>
	        						                     
	        						                     <td  rowspan="<?=$span;?>"><p><? echo implode(", ", array_unique(explode("***", chop($style_wise_data[$style_ref_no]['shipment_date'],"***"))));  ?>&nbsp; </p></td>
	        						                     <td rowspan="<?=$span;?>" align="right"><p><? echo number_format($style_wise_data[$style_ref_no]['po_quantity']); ?></p></td>
	        						                     <td  rowspan="<?=$span;?>"><p><? echo implode(", ", array_unique(explode("***", chop($style_wise_data[$style_ref_no]['order_uom'],"***"))));  ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['plan_cut_qnty']); ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['order_qty_pcs']); ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['avg_unit_price'],4); ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['po_total_price'],2); ?></p></td>

	        						                     <!-- fabric start -->
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['yarn_required_qnty'],2); ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['yarn_costing'],4); ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" title="[ Fabric cost+Yarn Cost+ All type of conversion cost] / Yarn Req."><p><? echo number_format($fab_rate,4); ?> </p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" title="Total Yarn Cons(Pcs)*Fabric Rate"><p><? echo number_format($style_wise_data[$style_ref_no]['yarn_costing']*$fab_rate,2); ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" title="Total Plan Order Qnty(Pcs)*Fabric Amount (Pcs)">
	        						                     	<p><? echo number_format($style_wise_data[$style_ref_no]['yarn_costing']*$fab_rate*$style_wise_data[$style_ref_no]['plan_cut_qnty'],2); ?></p>
	        						                     </td>
	        						                      <!-- fabric end -->

	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['trim_amount'],2); ?></p></td>


	        						                     <!-- emblishment start -->
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['print_amount'],2); ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['embroidery_amount'],2); ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['special_amount'] ,2); ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['wash_cost'],2); ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['other_amount'],2); ?></p></td>
	        						                    
	        						                     
	        						                       <!-- emblishment end -->

	        						                    
	        						                   
	        						                   	 <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['commercial_cost'],2); ?></p></td>

	        						                   	  <!-- Commission start -->
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['foreign'],2); ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['local'],2); ?></p></td>
	        						                       <!-- Commission end -->
	        						                       

	        						                        <!-- Testing Cost start -->
	        						                     <td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['test_cost'],2); ?></p></td>
	        						                    
	        						                       <!-- Testing Cost end -->
	        						                    
	        						                   	<?php 

	        						                   		$total_cost=$style_wise_data[$style_ref_no]['total_cost']+($style_wise_data[$style_ref_no]['yarn_costing']*$fab_rate*$style_wise_data[$style_ref_no]['plan_cut_qnty']);
	        						                   		$total_margin=$style_wise_data[$style_ref_no]['po_total_price']-$total_cost;
	        						                   		$margin_cm=$total_margin/$style_wise_data[$style_ref_no]['po_quantity'];
	        						                   	 ?>
	        						                    
	        						                     <td rowspan="<?=$span;?>" align="right" title="Total cost=Total Fabric Amount+Trims Cost+Embellis. Cost+Commercial Cost+Commosion+Testing Cost"><p><? echo number_format($total_cost,2); ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right"  title="Total Margin=Order Value-Total cost"><p><? echo number_format($total_margin,2); ?></p></td>
	        						                     <td rowspan="<?=$span;?>" align="right" title="Margin CM=Total Margin/Order Qnty"><p><? echo number_format($margin_cm ,2); ?></p></td>

	        						                		<?
	        						                }
	        						                	?>

	        						                	<td ><p><? echo $composition[$copm_one_id];  ?></p></td>
	        						                	<td ><p><? echo $yarn_type[$type_id]; ?></p></td>

        						                     <?


        						                     	foreach ($count_id_arr as $count_id) 
        						                     	{
        						                     		?>
        						                     		<td align="right"><p><? echo number_format($type_data[$count_id]['qty'],2);?></p></td>
        						                     		<?
        						                     	}

        						                     if ($row_span==0)
				                			 		{

				                			 			?>
				                			 			<td rowspan="<?=$span;?>" align="right" ><p><? echo number_format($style_wise_data[$style_ref_no]['total_yarn'],2); ?></p></td>
				                			 			<td rowspan="<?=$span;?>" ><p><? echo implode(", ", array_unique(explode("***", chop($style_wise_data[$style_ref_no]['remarks'],"***"))));  ?></p></td>
				                			 			<?

				                			 		}

		                					
		                					$row_span++;
		                				}
		                			}
		                		}


		                	?>
	                  			

								

	                    </tbody>
	                   
	                     
	                         
									    
	                </table> 
	                <table cellspacing="0" border="1" class="rpt_table" width="3300" rules="all">
	                	
	                	 <tfoot>
	                    	<tr>
			                	
		                   </tr>
	                    </tfoot>
	                </table>

	                
	           </div>     
	  		</div>
	 
	  		</fieldset>
	  		
	 		<?	

		foreach (glob("$user_id*.xls") as $filename) 
		{
			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$contents.= ob_get_flush();
		$is_created = fwrite($create_new_doc,$contents);
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
		exit(); 
	}
	
}




