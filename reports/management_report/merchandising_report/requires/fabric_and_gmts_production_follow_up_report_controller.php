<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
//require_once('../../../../includes/class4/class.conversions.php');
//require_once('../../../../includes/class4/class.emblishments.php');
//require_once('../../../../includes/class4/class.commisions.php');
//require_once('../../../../includes/class4/class.commercials.php');
//require_once('../../../../includes/class4/class.others.php');
//require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
//require_once('../../../../includes/class4/class.washes.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$colorname_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

if ($action=="load_drop_down_buyer")
{
	
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'fabric_and_gmts_production_follow_up_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	$supplier_arr=return_library_array( "select id,supplier_name from lib_supplier ",'id','supplier_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );

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

	if(!empty(str_replace("'","",$cbo_working_company)))
	{
		$working_company_cond=" and d.working_company_id=$cbo_working_company";
	}
	if(!empty($cbo_company_name))
	{
		$company_cond=" and d.company_id=$cbo_company_name ";
	}


	$cbo_date_type=str_replace("'", "", $cbo_date_type);
	 
	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
	
	
	
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
			  
		     $sql_po="SELECT a.buyer_name, a.job_no, a.job_no_prefix_num, $select_job_year,a.gmts_item_id,a.style_ref_no,a.season_buyer_wise, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty,b.unit_price, b.plan_cut,b.po_quantity, b.pub_shipment_date,b.shipment_date,b.file_no,b.grouping,b.po_received_date, c.color_number_id,c.order_quantity as order_quantity,c.plan_cut_qnty,c.color_number_id as color,c.item_number_id as item_id,c.order_rate,c.order_total
				from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c 
				where a.id=b.job_id and a.id=c.job_id  and b.id=c.po_break_down_id   and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1  and c.is_deleted=0   $buyer_id_cond  $job_cond_id $order_cond $date_cond order by b.id ";

			  
		    //echo $sql_po;die;
			$pro_date_sql=sql_select($sql_po);
			 
			  $po_id_marge=array();
			  $po_country_arr=array();
			  $po_plan_cutqty_arr=array();
			  $job_no_arr=array();
			  $job_no_list=array();
			  $po_number_id=array();
			  foreach($pro_date_sql as $row)
			  {
				  $job_no_arr[$row[csf('po_id')]]=$row[csf('job_no')];

					$job_no=$row[csf("job_no")];

				 	$po_color_data_arr[$job_no][$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["gmts_item_id"]=$row[csf("gmts_item_id")];
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["order_quantity"]+=$row[csf("order_quantity")];
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["plan_cut_qnty"]+=$row[csf("plan_cut_qnty")];
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["order_rate"]=$row[csf("order_rate")];
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["order_total"]+=$row[csf("order_total")];
					
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["job_no_prefix"]=$row[csf("job_no_prefix_num")];
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["file_no"]=$row[csf("file_no")];
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["grouping"]=$row[csf("grouping")];
					
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
					
					
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["po_received_date"]=$row[csf("po_received_date")];
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["shipment_date"]=$row[csf("shipment_date")];
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["item_id"].=$garments_item[$row[csf("item_number_id")]].',';
					$po_color_data_arr[$job_no][$row[csf("po_id")]]["season"]=$season_arr[$row[csf("season_buyer_wise")]];
					
				  array_push( $po_number_id, $row[csf('po_id')]);
				  array_push( $job_no_list, $row[csf('job_no')]);
			  }

			  $sql_yarn = "SELECT c.id as y_id,a.sequence_no,a.id,c.yarn_count,a.construction,b.copmposition_id,b.percent  from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b,lib_yarn_count c where a.id=b.mst_id and b.count_id=c.id  order by a.id, a.sequence_no";
			$data_arr_ycount=sql_select($sql_yarn);
			foreach($data_arr_ycount as $row)
			{
				$precost_yarnCount_arr[$row[csf("y_id")]]['count']=$row[csf("yarn_count")];
			}
			unset($data_arr_ycount);

			
		
			// $data_array=sql_select("select id as pri_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty,supplier_id, rate, amount,status_active from wo_pri_quo_fab_yarn_cost_dtls where quotation_id='$data[1]'  and status_active=1 and is_deleted=0 order by id");
			// $save_update=0;
					

			  $po_cond_for_in=where_con_using_array($po_number_id,0,"c.po_break_down_id");

			  $sql_yarn="SELECT d.job_no,c.po_break_down_id as po_id,c.color_number_id as color,d.composition,f.copm_one_id,f.count_id,f.percent_one,f.type_id,sum(f.cons_qnty) as budget
				from wo_po_color_size_breakdown c ,wo_pre_cost_fabric_cost_dtls d, wo_pre_cost_fab_yarn_cost_dtls f
				where  d.job_id=c.job_id and d.id=f.fabric_cost_dtls_id and c.item_number_id=d.item_number_id and c.status_active=1  and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $po_cond_for_in group by d.job_no,c.po_break_down_id ,c.color_number_id,d.composition,f.copm_one_id,f.count_id,f.percent_one,f.type_id order by d.job_no";
				//echo $sql_yarn;die;
			 
				$sql_yarn_result=sql_select($sql_yarn);
				 
				foreach($sql_yarn_result as $row)
				{
						
						
						$yarn_data_arr[$row[csf("po_id")]][$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("type_id")]][$row[csf('percent_one')]]+=$row[csf("budget")];
						
				}
				unset($sql_yarn_result);

			$po_cond_for_in=str_replace("c.po_break_down_id", "c.po_breakdown_id", $po_cond_for_in);

			$sql_yarn_iss = "SELECT c.po_breakdown_id as po_id,sum(c.quantity) cons_quantity, a.yarn_count_id, a.yarn_type,a.yarn_comp_type1st as composition, d.supplier_id,a.yarn_comp_percent1st from order_wise_pro_details c ,inv_transaction d,product_details_master a   where d.id=c.trans_id and a.id=c.prod_id and a.id=d.prod_id and c.trans_type=2 and c.entry_form=3 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $po_cond_for_in group by c.po_breakdown_id, a.yarn_count_id, a.yarn_type,a.yarn_comp_type1st, d.supplier_id,a.yarn_comp_percent1st";
			//echo "<pre>".$sql_yarn_iss."</pre>";
			$dataArrayIssue = sql_select($sql_yarn_iss);
			
			
			foreach ($dataArrayIssue as $row) 
			{ 
				//[$row[csf('yarn_count_id')]]

				
				$yarn_issue_details_arr[$row[csf('po_id')]][$row[csf("yarn_count_id")]][$row[csf("composition")]][$row[csf('yarn_comp_percent1st')]][$row[csf("yarn_type")]]['issue_qnty']+=$row[csf('cons_quantity')];
				$yarn_issue_details_arr[$row[csf('po_id')]][$row[csf("yarn_count_id")]][$row[csf("composition")]][$row[csf('yarn_comp_percent1st')]][$row[csf("yarn_type")]]['supplier_id'].=$supplier_arr[$row[csf('supplier_id')]]."***";
				

			}

			unset($sql_yarn_iss);
			unset($dataArrayIssue);

			$po_cond_for_in=str_replace("c.po_breakdown_id", "c.po_break_down_id", $po_cond_for_in);
			$booking_data_array=array();
			$color_wise_booking_data_array=array();
			$booking_data=sql_select("SELECT  c.po_break_down_id AS po_id,
									         sum(c.grey_fab_qnty) AS grey_fab_qnty,
									          sum ( c.fin_fab_qnty) as fin_fab_qnty,
									         c.color_type,
									         c.construction,
									         c.copmposition,
									         c.fabric_color_id
									 from wo_booking_mst b,wo_booking_dtls c,wo_pre_cost_fabric_cost_dtls d where b.booking_no=c.booking_no and c.pre_cost_fabric_cost_dtls_id=d.id and c.job_no=d.job_no and   c.booking_type in(1,4)  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $po_cond_for_in 
									 group by c.po_break_down_id ,
								
									         c.color_type,
									         c.construction,
									         c.copmposition,
									          c.fabric_color_id
									order by c.po_break_down_id,c.fabric_color_id");
			
			
			
			 
			foreach($booking_data as $row)
			{
				
				$booking_data_array[$row[csf('po_id')]][$row[csf('construction')]]['color_type'].=$color_type[$row[csf('color_type')]]."***";
				$booking_data_array[$row[csf('po_id')]][$row[csf('construction')]]['copmposition'].=$row[csf('copmposition')]."***";
				$booking_data_po_wise_array[$row[csf('po_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
				
				
				

				$color_wise_booking_data_array[$row[csf('po_id')]][$row[csf('fabric_color_id')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
				$color_wise_booking_data_array[$row[csf('po_id')]][$row[csf('fabric_color_id')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
				
			}
			unset($booking_data);
			
			$knit_data_array=array();
			$po_cond_for_in=str_replace("c.po_break_down_id", "c.po_breakdown_id", $po_cond_for_in);
			
		 	$knit_data=sql_select("SELECT 
									       c.po_breakdown_id as po_id,
									       sum(c.quantity) as quantity
								   FROM  order_wise_pro_details c
								   WHERE    
									        c.entry_form = 2
									       and c.trans_type = 1
									       and c.is_deleted = 0
									       and c.status_active = 1
									       $po_cond_for_in
									group by c.po_breakdown_id  ");

			
			foreach($knit_data as $row)
			{
				$knit_data_array[$row[csf('po_id')]]['knitQty']+=$row[csf('quantity')];;
				
			}
			unset($knit_data);
			$batch_po_cond=str_replace("c.po_breakdown_id", "b.po_id", $po_cond_for_in);
			 $sql_dye = "SELECT a.color_id,b.po_id,sum(b.batch_qnty) as dyeing_qty from pro_batch_create_mst a, pro_batch_create_dtls b, pro_fab_subprocess c,product_details_master d where a.id=b.mst_id and a.id=c.batch_id and b.prod_id=d.id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $batch_po_cond group by a.color_id,b.po_id order by a.color_id";
			//echo $sql_dye;
			$resultDye = sql_select($sql_dye);
			foreach ($resultDye as $row) 
			{
			
				$dyeing_qnty_arr[$row[csf('po_id')]][$row[csf('color_id')]]['dyeing_qty']+= $row[csf('dyeing_qty')];
				
			}
			unset($sql_dye);
			unset($resultDye);

			$po_cond_for_qc=str_replace("b.po_id", "c.po_breakdown_id", $batch_po_cond);
			$sql_finish_qc="SELECT 
			         c.po_breakdown_id,
			         sum(c.qnty) qnty,
			        sum( c.qc_pass_qnty) qc_pass_qnty ,
			         b.color_id
				    from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c ,pro_qc_result_mst d
				   where     a.id = b.mst_id
				         and b.id = c.dtls_id
				         and d.barcode_no=c.barcode_no
				         
				         and a.entry_form = 66
				         and c.entry_form = 66
				         and c.status_active = 1
				         and c.is_deleted = 0
				         and d.is_deleted=0
				         $po_cond_for_qc
				    group by   c.po_breakdown_id,
				         b.color_id";

			$resultFinishQc = sql_select($sql_finish_qc);
			$$finish_qc_qnty_arr=array();
			foreach ($resultFinishQc as $row) 
			{
			
				$finish_qc_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['qnty']+= $row[csf('qnty')];
				$finish_qc_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['qc_pass_qnty']+= $row[csf('qc_pass_qnty')];
				
			}
			unset($sql_finish_qc);
			unset($resultFinishQc);

			$po_cond_for_finish=str_replace("c.po_breakdown_id", "b.po_breakdown_id", $po_cond_for_qc);
			$sql_finish="SELECT c.color_id,
						         sum(c.production_qty) as production_qty,
						         sum(c.receive_qnty) as receive_qnty,
						         sum(c.reject_qty) as reject_qty,
						         b.po_breakdown_id
						    FROM inv_receive_master a,
						         pro_roll_details b,
						         pro_finish_fabric_rcv_dtls c  
						   WHERE     a.id = b.mst_id
						         AND a.id = c.mst_id
						         AND a.entry_form = 66
						         AND b.entry_form = 66
						         and b.barcode_no=c.barcode_no
						         AND a.status_active = 1
						         AND a.is_deleted = 0
						         AND b.status_active = 1
						         AND b.is_deleted = 0
						           AND c.status_active = 1
						         AND c.is_deleted = 0
						        	$po_cond_for_finish
						   group by c.color_id,b.po_breakdown_id
							ORDER BY c.color_id";
			//echo $sql_finish;
			$resultFinish = sql_select($sql_finish);
			$$finish_qnty_arr=array();
			foreach ($resultFinish as $row) 
			{
			
				$finish_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['production_qty']+= $row[csf('production_qty')];
				$finish_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['receive_qnty']+= $row[csf('receive_qnty')];
				$finish_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['reject_qty']+= $row[csf('reject_qty')];
				
			}
			unset($sql_finish);
			unset($resultFinish);
			$cut_po_cond=str_replace("b.po_breakdown_id", "c.order_id", $po_cond_for_finish);

			 $sql_dtls_cut=sql_select("select c.order_id,c.size_qty,a.color_id from ppl_cut_lay_dtls a, ppl_cut_lay_mst b,ppl_cut_lay_bundle c where b.id=a.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id=c.mst_id and a.id=c.dtls_id $cut_po_cond order by a.color_id");
				foreach($sql_dtls_cut as $row)
				{
					$cut_lay_first_arr[$row[csf('order_id')]][$row[csf('color_id')]]['size_qty']+=$row[csf('size_qty')];
					
				}


				$po_cond_for_in=str_replace("c.order_id", "c.po_break_down_id", $cut_po_cond);
	 			 $SqlgmtsProdData="select  c.po_break_down_id as po_id,b.color_number_id as color,
					
					
					sum(CASE WHEN c.production_type=5 and c.production_source=1 THEN d.production_qnty ELSE 0 END) AS sew_recv_qnty_in,
					
					sum(CASE WHEN c.production_type=8 and c.production_source=1 THEN d.production_qnty ELSE 0 END) AS finish_qnty_in
					
					from pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown b where c.id=d.mst_id and b.id=d.color_size_break_down_id and c.is_deleted=0 and c.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in group by c.po_break_down_id,b.color_number_id order by c.po_break_down_id,b.color_number_id";



				$garment_prod_data_arr=array();
				//echo $SqlgmtsProdData;
				$gmtsProdDataArr=sql_select($SqlgmtsProdData);
				foreach($gmtsProdDataArr as $row)
				{ 
					
					
					$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['sew_recv_qnty_in']=$row[csf("sew_recv_qnty_in")];
					
					$garment_prod_data_arr[$row[csf("po_id")]][$row[csf("color")]]['finish_qnty_in']=$row[csf("finish_qnty_in")];
					
					
				}
				unset($gmtsProdDataArr);

					
			  
			$exfactory_data_array=array();
			$ex_sql="select b.color_number_id as color_id,c.po_break_down_id as po_id,
			sum(CASE WHEN c.entry_form!=85 THEN d.production_qnty ELSE 0 END) as ex_factory_qnty
			
			 from pro_ex_factory_mst c ,pro_ex_factory_dtls d,wo_po_color_size_breakdown b where c.id=d.mst_id and b.id=d.color_size_break_down_id and c.po_break_down_id=b.po_break_down_id and c.status_active=1 and c.is_deleted=0 $po_cond_for_in group by b.color_number_id,c.po_break_down_id order by c.po_break_down_id,b.color_number_id";
			$exfactory_data=sql_select($ex_sql);

			//echo $ex_sql;
			foreach($exfactory_data as $row)
			{
					$exfactory_data_array[$row[csf('po_id')]][$row[csf('color_id')]]['ex_factory_qnty']+=$row[csf('ex_factory_qnty')];
					
			}
			unset($exfactory_data);

			

			 $condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			
			 //job_year
			 if(trim(str_replace("'","",$txt_order_no))!="")
			{
				if(str_replace("'","",$hidden_order_id)!="")
				{
					//echo $txt_order_id.'AAAAAA';
					//$order_cond=" and b.id in(".str_replace("'","",$txt_order_id).")";
					$condition->po_id("in($hidden_order_id)"); 
				}
				else
				{
					//$order_cond=" and b.po_number = '".trim(str_replace("'","",$txt_order))."'";
					$condition->po_number("=$txt_order_no"); 
				}
			}
			if(trim(str_replace("'","",$txt_job_no))!="")
			{
				if(str_replace("'","",$hidden_job_id)!="")
				{
					 //$hidden_job_id.'AAAAAA';
					 $order_ids=str_replace("'","",$hidden_job_id);
					//$order_cond=" and b.id in(".str_replace("'","",$txt_order_id).")";
					$condition->jobid_in("in($order_ids)"); 
				}
				else
				{
					//$order_cond=" and b.po_number = '".trim(str_replace("'","",$txt_order))."'";
					$job_no=str_replace("'","",$txt_job_no);
					$condition->job_no_prefix_num("like '%$job_no%'"); 
				}
			}
			
	
			if($txt_date_from!="" && $txt_date_to!="")
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

			  if($cbo_date_type==1)
			  {
			  	
			  	 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			  }
			  else
			  {
			  	
			  	 $condition->shipment_date(" between '$start_date' and '$end_date'");
			  }
				//$ship_date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
				
	
			}
		
			  $condition->init();
			  $yarn= new yarn($condition);
			//  echo  $yarn->getQuery();die;
			 // $fabric_costing_arr=$fabric->getQtyArray_by_orderAndGmtsitem_knitAndwoven_greyAndfinish();
			  //getCountCompositionAndTypeWiseYarnQty
			  $yarn_qty_arr=$yarn->getOrderCountCompositionPercentAndTypeWiseYarnQtyArray();

			   // echo "<pre>";
			   // print_r( $yarn_qty_arr);
			   // echo "</pre>";

			
		   		$knitting_issue_total=0;
		   		$knitting_receive_total=0;
		   		$knitting_receive_weight_total=0;
		   		$balance_total=0;
		  ob_start();
	  

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
                            Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
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

         
            
             <div style="width:4020px; margin-left:18px" >
             		<table cellspacing="0" border="1" class="rpt_table" width="4000" rules="all">
             			<thead>
             				<tr>
             					<th width="40" rowspan="2">SL</th>
             					<th colspan="8" width="680">Order Details</th>
             					<th colspan="6" width="870">Yarn Details</th>
             					<th colspan="4" width="578">Fab. Details As-per Booking</th>
             					<th colspan="2" width="136">Knitting Production</th>
             					<th colspan="4">Fabric Dyeing Production</th>
             					<th colspan="4">Finish Fabric Production</th>
             					<th colspan="4">Finish Fabric QC Production</th>
             					<th colspan="5">Garments Production</th>

             				</tr>
             				<tr >
		                       

		                       <th width="100" >Buyer Name</th>
		                       <th width="115" >Job No</th>
		                       <th width="115" >Order No</th>
		                       <th width="115" >Style Name</th>
		                       <th width="110" >Internal Ref</th>
		                       <th width="70"> File No</th>
		                       <th width="80" >Order Qnty(Pcs)</th>
		                       <th width="90" >Shipment Date</th>

		                       <th width="150" >Count</th>
		                       <th width="260" >Composition</th>                        
		                       <th width="150" >Type</th>
		                       <th width="80">Required Qty</th>
		                       <th width="80" >Issue Qty</th>
		                       <th width="150" >Yarn Supplier</th>

		                       <th width="180">Construction</th>
		                       <th width="180">Composition</th>
		                       <th width="150">Color Type</th>
		                       <th width="68">Grey Req. Qty.</th>

		                       <th width="68">Knit Qnty</th>
		                       <th width="68">Knit Balance Qnty</th>

		                       <th width="130" >Fabric Color</th>
		                       <th width="80" >Req. Qnty</th>
		                       <th width="80" >Prod. Qnty</th>
		                       <th width="80" >Blance Qnty</th>

		                       <th width="130" >Fabric Color</th>
		                       <th width="80" >Req. Qnty</th>
		                       <th width="80" >Prod. Qnty</th>
		                       <th width="80" >Blance Qnty</th>

		                       <th width="130" >Fabric Color</th>
		                       <th width="80" >Req. Qnty</th>
		                       <th width="80" >QC Qnty.</th>
		                       <th width="80" >Blance Qnty</th>

		                        <th width="130" >Garments Color</th>
		                       <th width="80" >Cutting</th>
		                       <th width="80" >Sewing Out</th>
		                       <th width="80" >Packing/ Finishing</th>
		                       <th>Ex-Factory</th>
		                       
		                      
		                    </tr>
             			</thead>
             		</table>
                    <table cellspacing="0" border="1" class="rpt_table"  width="4000" rules="all"   style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body">
                    	<?
                    		$contents.= ob_get_flush();


                    	?>
                    	<thead>
                    		<tr>
			                	<td  width="40"></td>
			                    <td width="100" ></td>
			                    <td width="115" ></td>
			                    <td width="115" ></td>
			                    <td width="115" ></td>
			                    <td width="110" ></td>
			                    <td width="70" ></td>
			                    <td width="80" ></td>
			                    <td width="90" ></td>



		                      
			                   <td width="150" ></td>
		                       <td width="260" ></td>                        
		                       <td width="150" ></td>
		                       <td width="80"> </td>
		                       <td width="80" > </td>
		                       <td width="150" > </td>

		                       <td width="180"></td>
		                       <td width="180"></td>
		                       <td width="150"> </td>
		                       <td width="68"> </td>

		                       <td width="68"> </td>
		                       <td width="68">  </td>

		                       <td width="130" > </td>
		                       <td width="80" > </td>
		                       <td width="80" > </td>
		                       <td width="80" > </td>

		                       <td width="130" > </td>
		                       <td width="80" > </td>
		                       <td width="80" > </td>
		                       <td width="80" > </td>

		                       <td width="130" > </td>
		                       <td width="80" > </td>
		                       <td width="80" > </td>
		                       <td width="80" > </td>

		                       <td width="130" > </td>
		                       <td width="80" ></td>
		                       <td width="80" > </td>
		                       <td width="80" > </td>
		                       <td></td>
		                   </tr>
                    	</thead>

                    	<?
                    		ob_start();


                    	?>
                    	
		                <tbody >
		                	
		                	
	                  			<?

	                  			

	                  			

	                  			 $job_rowspan_arr=array();	$po_rowspan_arr2=array();$po_item_rowspan_arr=array();$po_item_color_rowspan_arr=array();
	               				 foreach($po_color_data_arr as $job_no=>$job_data)	
								 {
								 	$job_rowspan=0;
								 	foreach($job_data as $po_id=>$po_data)
									{
										
										
										
										$job_rowspan++;
										$job_rowspan_arr[$job_no]=$job_rowspan;
										
									}
								}
	                     		 $i=0;
				                 $knit=0;
								$knitting_issue=$knitting_receive=$knitting_receive_weight=0;
								 foreach($po_color_data_arr as $job_no=>$job_data)	
								 {
								 	$job_rowspan=0;
								 	$i++;
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								 	
								 	foreach($job_data as $po_id=>$val)
									{
										

										
										
												$balance=$knitting_issue-$knitting_receive;

												$yarn_descrip_data = $yarn_data_arr[$po_id];


												$qnty = 0;
												$yarn_data_array=array();
												$yarn_desc_array=array();
												$s = 1;
												foreach ($yarn_descrip_data as $count => $count_value) {
												    foreach ($count_value as $Composition => $composition_value) {
												        foreach ($composition_value as $percent => $percent_value) {
												            foreach ($percent_value as $typee => $type_value) {
												                //$yarnRow=explode("**",$yarnRow);
												                $count_id = $count; //$yarnRow[0];
												                $copm_one_id = $Composition; //$yarnRow[1];
												                $percent_one = $percent; //$yarnRow[2];
												                $copm_two_id = 0;
												                $percent_two = 0;
												                $type_id = $typee; //$yarnRow[5];
												                $qnty = $type_value; //$yarnRow[6];

												                $mkt_required = $qnty; //$plan_cut_qnty*($qnty/$dzn_qnty);
												                $mkt_required_array[$s] = $mkt_required;
												                $job_mkt_required += $mkt_required;

												                $yarn_data_array['count'][$s] = $precost_yarnCount_arr[$count_id]['count'];
												                $yarn_data_array['type'][$s] = $yarn_type[$percent];
												                $yarn_data_array['supplier_id'][$s] = '';
												                $yarn_data_array['issue_qnty'][$s]+= 0;
												                $yarn_data_array['budget'][$s]+= $type_value;
												                $yarn_data_array['required'][$s]+=$yarn_qty_arr[$po_id][$count_id][$copm_one_id][$type_id][$percent_one];
												               // $yarn_c = $po_id."__".$count_id . "__" . $copm_one_id . "__" . $type_id. "__" . $percent_one."=". $yarn_data_array['required'][$s];
												               // echo "<pre>$yarn_c </pre>";



												              
												                $compos = $composition[$copm_one_id] ;

												                $yarn_data_array['comp'][$s] = $compos;

												             
												              	$count_comp_type = $count_id . "__" . $copm_one_id . "__" . $percent_one. "__" . $type_id;
												              	$yarn_data_array['count_comp_type'][$s]=$count_comp_type;

												                $s++;
												            }
												        }
												    }
												}

												
												

												$issue_data=$yarn_issue_details_arr[$po_id];
												$issue_flag=array();
												foreach ($issue_data as $count_id => $count_value) 
												{
													foreach ($count_value as $copmposition_id => $composition_value) 
													{
														foreach ($composition_value as $percent_one => $percent_value) 
														{
															foreach ($percent_value as $type_id => $type_value) 
															{
																$yarn_data_array['count'][$s] = $precost_yarnCount_arr[$count_id]['count'];
																$yarn_data_array['type'][$s] = $yarn_type[$type_id];
																$yarn_data_array['supplier_id'][$s] = $type_value['supplier_id'];
																$yarn_data_array['issue_qnty'][$s]+= $type_value['issue_qnty'];
																$yarn_data_array['budget'][$s]+= 0;
																$compos = $composition[$copmposition_id] ;
																$yarn_data_array['comp'][$s]=$compos;
																$count_comp_type = $count_id . "__" . $copmposition_id . "__" . $percent_one. "__" . $type_id;
												              	$yarn_data_array['count_comp_type'][$s]=$count_comp_type;

												              	$yarn_data_array['required'][$s]+=0;
												              	
																$s++;
															}
														}
													}
												}
												
												
												
													
												
													  
													 
									                ?>
									                <tr  bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>"  >
									                	
									                	<?php if ($job_rowspan==0): ?>
									                		<td  align="left" rowspan="<? echo $job_rowspan_arr[$job_no];?>"><? echo $i; ?></td>
									                		<td  align="left" rowspan="<? echo $job_rowspan_arr[$job_no];?>"><p><? echo $buyer_arr[$val['buyer_name']];?></p></td>
									                		<td  align="left" rowspan="<? echo $job_rowspan_arr[$job_no];?>"><p><? echo $job_no;?></p></td>
									                	<?php endif ?>

									                	
									                		
									                    	<td  align="left" ><p><? echo $val['po_number'];?></p> </td>
									                	

									                   	<?php if ($job_rowspan==0): ?>
									                		 <td  align="left" rowspan="<? echo $job_rowspan_arr[$job_no];?>"><p><? echo $val['style_ref_no'];$job_rowspan++;?></p></td>

									                	<?php endif ?>
									                   
									                	
									                		
									                    	<td  align="left" ><p><? echo $val['grouping'];?></p></td>
										                    <td  align="left" ><p><? echo $val['file_no'];?></p></td>
										                    <td  align="left" ><p><? echo number_format($val['order_quantity'],2);?></p></td>
										                    <td  align="left" ><p><? echo change_date_format($val['pub_shipment_date']);?> </p></td>
									                	
									                   




									                   <td  >
									                   	<p>

									                   	<?php
									                   		
															$hr=0;
															foreach ($yarn_data_array['count'] as $count_value) {
																if($hr>0)
																{
																	echo "&nbsp;<hr>";
																}
																 echo $count_value;
																 $hr++;
															}


									                   	  ?>
									                   	</p>
									                   	  	
									                   	  </td>

									                   <td  > 
									                   	<p>
									                   		<?php
									                   		

															$hr=0;
															foreach ($yarn_data_array['comp'] as $comp_value) {
																if($hr>0)
																{
																	echo "&nbsp;<hr>";
																}
																 echo $comp_value;
																 $hr++;
															}


									                   	  ?>
									                   	</p>
									               		</td>
									                   <td  >
									                   	<p>

									                   		<?php
									                   		

															$hr=0;
															foreach ($yarn_data_array['type'] as $type_value) {
																if($hr>0)
																{
																	echo "&nbsp;<hr>";
																}
																 echo $type_value;
																 $hr++;
															}


									                   	  ?>
									                   	</p>
									                   </td>
								                       <td  >
									                   	<p>

									                   		<?php
									                   		

															$hr=0;
															
															foreach ($yarn_data_array['required'] as $required) {
																if($hr>0)
																{
																	echo "&nbsp;<hr>";
																}
																
																echo number_format($required,2) ;
																$hr++;
															}


									                   	  ?>
									                   	</p>
									                   </td>
									                   <td  >
									                   	<p>

									                   		<?php
									                   		

									                   		$hr=0;
									                   		foreach ($yarn_data_array['issue_qnty'] as $issue_qnty) 
															{
																
																	if($hr>0)
																	{
																		echo "&nbsp;<hr>";
																	}
																	echo number_format($issue_qnty,2) ;
																	$hr++;
																
																
															}


									                   	  ?>
									                   	</p>
									                   </td>
								                       <td >
								                       	<p>
		                       		                   		<?php
			                       		                   		$hr=0;
			                       		                   		foreach ($yarn_data_array['supplier_id'] as $supplier_id) 
			                       								{
			                       									
			                       									
			                       									if($hr>0)
			                       									{
			                       										echo "&nbsp;<hr>";
			                       									}
			                       		                   			 echo implode(", ", array_unique(explode("***", chop($supplier_id,"***"))));
			                       		                   			 $hr++;

			                       		                   		}

			                       		                   		


		                       		                   	  ?>
		                       		                   	</p>
								                        </td>
								                        <?php 


								                        	 
								                        	
								                        	 $grey_fab_qnty=$booking_data_po_wise_array[$po_id]['grey_fab_qnty'];
															

								                         ?>
								                      
								                       <td >
									                       	<P>
									                       		<?
									                       			$hr=0;
										                       		foreach ($booking_data_array[$po_id] as $construction => $cons_data) {
										                       			if($hr>0)
				                       									{
				                       										echo "&nbsp;<hr>";
				                       									}
				                       		                   			 echo $construction;
				                       		                   			 $hr++;
										                       		}
										                       ?>
									                       	
									                       </P>
									                   </td>
								                        
								                       <td >
									                       	<P>
									                       		<?
									                       			$hr=0;
										                       		foreach ($booking_data_array[$po_id] as $construction => $cons_data) {
										                       			if($hr>0)
				                       									{
				                       										echo "&nbsp;<hr>";
				                       									}
				                       		                   			  echo implode(", ", array_unique(array_filter(explode("***", chop($cons_data['copmposition'],"***")))));
				                       		                   			 $hr++;
										                       		}
										                       ?>
									                       	
									                       </P>
									                   </td>
									                   <td >
									                       	<P>
									                       		<?
									                       			$hr=0;
									                       			
										                       		foreach ($booking_data_array[$po_id] as $construction => $cons_data) {
										                       			if($hr>0)
				                       									{
				                       										echo "&nbsp;<hr>";
				                       									}
				                       		                   			 echo implode(", ", array_unique(array_filter(explode("***", chop($cons_data['color_type'],"***")))));
				                       		                   			 $hr++;
										                       		}
										                       ?>
									                       	
									                       </P>
									                   </td>
								                       <td ><P><?=number_format($grey_fab_qnty,2);?></P></td>
								                       <td ><P><?=number_format($knit_data_array[$po_id]['knitQty'],2);?></P></td>
								                       <td ><P><?=number_format($grey_fab_qnty-$knit_data_array[$po_id]['knitQty'],2);?></P></td>
								                       
								                      
								                       
								                     	<?php 
								                     		$color_wise_all_data=array();
								                     		$cl=1;
								                     		$all_color=array();
								                     		foreach ($dyeing_qnty_arr[$po_id] as $color_id => $color_data) 
			                       							{
			                       								$color_wise_all_data[$color_id]['fin_fab_qnty']+=$color_wise_booking_data_array[$po_id][$color_id]['fin_fab_qnty'];
			                       								$color_wise_all_data[$color_id]['dyeing_qty']+=$color_data['dyeing_qty'];
			                       								$booking_req_qnt=$color_wise_booking_data_array[$po_id][$color_id]['fin_fab_qnty'];
			                       								
			                       								$color_wise_all_data[$color_id]['booking_req_qnt_minus_dyeing_qty']+=($booking_req_qnt-$color_data['dyeing_qty']);
			                       								array_push($all_color, $color_id);
			                       							}
			                       							foreach ($finish_qnty_arr[$po_id] as $color_id => $color_data) 
			                       							{
			                       								if(!in_array($color_id, $all_color))
			                       								{
			                       									array_push($all_color, $color_id);
			                       								}
			                       								else
			                       								{
			                       									$color_wise_all_data[$color_id]['fin_fab_qnty']+=$color_wise_booking_data_array[$po_id][$color_id]['fin_fab_qnty'];
			                       								}
			                       								
			                       								$color_wise_all_data[$color_id]['production_qty']+=$color_data['production_qty'];
			                       								$booking_req_qnt=$color_wise_booking_data_array[$po_id][$color_id]['fin_fab_qnty'];
       		                       								$color_wise_all_data[$color_id]['booking_req_qnt_minus_production_qty']+=($booking_req_qnt-$color_data['production_qty']);
			                       		                 
			                       							}
			                       							foreach ($finish_qc_qnty_arr[$po_id] as $color_id => $color_data) 
			                       							{
			                       								$color_wise_all_data[$color_id]['qc_pass_qnty']+=$color_data['qc_pass_qnty'];
			                       								$booking_req_qnt=$color_wise_booking_data_array[$po_id][$color_id]['fin_fab_qnty'];
			                       								if(!in_array($color_id, $all_color))
			                       								{
			                       									array_push($all_color, $color_id);
			                       								}
			                       								else
			                       								{
			                       									$color_wise_all_data[$color_id]['fin_fab_qnty']+=$color_wise_booking_data_array[$po_id][$color_id]['fin_fab_qnty'];
			                       								}
			                       								$color_wise_all_data[$color_id]['booking_req_qnt_minus_qc_pass_qnty']+=($booking_req_qnt-$color_data['qc_pass_qnty']);
       		                       									
			                       							}

			                       							foreach ($cut_lay_first_arr[$po_id] as $color_id => $color_data) 
       		                       							{	
       		                       									
       		                       								if(!in_array($color_id, $all_color))
			                       								{
			                       									array_push($all_color, $color_id);
			                       								}
			                       								$color_wise_all_data[$color_id]['size_qty']+=$color_data['size_qty'];

       		                       		                   	}

       		                       		                   	foreach ($garment_prod_data_arr[$po_id] as $color_id => $color_data) 
       		                       							{
       		                       								if(!in_array($color_id, $all_color))
			                       								{
			                       									array_push($all_color, $color_id);
			                       								}
			                       								
			                       								$color_wise_all_data[$color_id]['sew_recv_qnty_in']+=$color_data['sew_recv_qnty_in'];
			                       								$color_wise_all_data[$color_id]['finish_qnty_in']+=$color_data['finish_qnty_in'];
       		                       							}
       		                       							foreach ($exfactory_data_array[$po_id] as $color_id => $color_data) 
       		                       							{
       		                       								if(!in_array($color_id, $all_color))
			                       								{
			                       									array_push($all_color, $color_id);
			                       								}
			                       								$color_wise_all_data[$color_id]['ex_factory_qnty']+=$color_data['ex_factory_qnty'];
       		                       		                   	}
								                     	 ?>

								                       <td  >
								                       	<p>
								                       		<?php 
								                       			$hr=0;
								                       			
								                       			foreach ($color_wise_all_data as $color_id => $color_data) 
			                       								{
			                       									
			                       									if($hr>0)
			                       									{
			                       										echo "&nbsp;<hr>";
			                       									}
			                       		                   			 echo $color_library[$color_id] ;
			                       		                   			 $hr++;

			                       		                   		}

								                       		 ?>
								                       		</p>
								                       </td>
								                        <td  > 
								                        	<p>
								                        	<?php 

								                       			
								                        		$hr=0;
								                       			foreach ($color_wise_all_data as $color_id => $color_data) 
			                       								{
			                       									
			                       									
			                       									if($hr>0)
			                       									{
			                       										echo "&nbsp;<hr>";
			                       									}
			                       									
			                       		                   			 echo number_format($color_wise_booking_data_array[$po_id][$color_id]['fin_fab_qnty'],2) ;
			                       		                   			 $hr++;
			                       		                   		}

								                       		 ?>
								                       		</p>

								                        </td>
								                        <td  >
								                        	<p>
								                       		<?php 

								                       			$hr=0;
								                       			foreach ($color_wise_all_data as $color_id => $color_data) 
			                       								{
			                       									
			                       									if($hr>0)
			                       									{
			                       										echo "&nbsp;<hr>";
			                       									}
			                       		                   			 echo $color_data['dyeing_qty'] ;
			                       		                   			 $hr++;

			                       		                   		}

								                       		 ?>
								                       		</p>
								                       </td>
								                       
								                      
								                       <td  >
								                       	<p>
       							                       		<?php 
       							                       			$hr=0;

       							                       			foreach ($color_wise_all_data as $color_id => $color_data) 
       		                       								{
       		                       									
       		                       									
       		                       									if($hr>0)
       		                       									{
       		                       										echo "&nbsp;<hr>";
       		                       									}
			                       		                   			// echo number_format($color_data['booking_req_qnt_minus_dyeing_qty'],2)." ,".;
			                       		                   			 echo number_format($color_wise_booking_data_array[$po_id][$color_id]['fin_fab_qnty']-$color_data['dyeing_qty'],2) ;
       		                       		                   			 //echo $color_data['dyeing_qty'] ."&nbsp;<hr>";
			                       		                   			 $hr++;
       		                       		                   		}

       							                       		 ?>
       							                       		</p>
								                        </td>

								                      <td  >
								                      	<p>
								                       		<?php 


								                       			$hr=0;

								                       			foreach ($color_wise_all_data as $color_id => $color_data) 
			                       								{
			                       									
			                       									if($hr>0)
			                       									{
			                       										echo "&nbsp;<hr>";
			                       									}
			                       		                   			 echo $color_library[$color_id] ;
			                       		                   			 $hr++;

			                       		                   		}

								                       		 ?>
								                       		</p>
								                       </td>
								                        <td  > 
								                        	<p>
								                        	<?php 

								                       			
								                        		$hr=0;
								                       			foreach ($color_wise_all_data as $color_id => $color_data) 
			                       								{
			                       									
			                       									
			                       									if($hr>0)
			                       									{
			                       										echo "&nbsp;<hr>";
			                       									}
			                       									
			                       		                   			// echo number_format($color_data['fin_fab_qnty'],2) ;
			                       		                   			echo number_format($color_wise_booking_data_array[$po_id][$color_id]['fin_fab_qnty'],2) ;
			                       		                   			 $hr++;
			                       		                   		}

								                       		 ?>
								                       		</p>
								                        </td>
								                        <td  >
								                        	<p>
								                       		<?php 

								                       			$hr=0;
								                       			foreach ($color_wise_all_data as $color_id => $color_data) 
			                       								{
			                       									
			                       									if($hr>0)
			                       									{
			                       										echo "&nbsp;<hr>";
			                       									}
			                       		                   			 echo $color_data['production_qty'] ;
			                       		                   			 $hr++;

			                       		                   		}

								                       		 ?>
								                       		</p>
								                       </td>
								                       
								                      
								                       <td  >
								                       	<p>
       							                       		<?php 
       							                       			$hr=0;

       							                       			foreach ($color_wise_all_data as $color_id => $color_data) 
       		                       								{
       		                       									
       		                       									
       		                       									if($hr>0)
       		                       									{
       		                       										echo "&nbsp;<hr>";
       		                       									}
			                       		                   			// echo number_format($color_data['booking_req_qnt_minus_production_qty'],2) ;
			                       		                   			 echo number_format($color_wise_booking_data_array[$po_id][$color_id]['fin_fab_qnty']-$color_data['production_qty'],2) ;
       		                       		                   			 //echo $color_data['dyeing_qty'] ."&nbsp;<hr>";
			                       		                   			 $hr++;
       		                       		                   		}

       							                       		 ?>
       							                       		</p>
								                        </td>


								                     <td  >
								                     	<p>
								                       		<?php 


								                       			$hr=0;

								                       			foreach ($color_wise_all_data as $color_id => $color_data) 
			                       								{
			                       									
			                       									if($hr>0)
			                       									{
			                       										echo "&nbsp;<hr>";
			                       									}
			                       		                   			 echo $color_library[$color_id] ;
			                       		                   			 $hr++;

			                       		                   		}

								                       		 ?>
								                       		</p>
								                       </td>
								                        <td  >
								                        <p> 
								                        	<?php 

								                       			
								                        		$hr=0;
								                       			foreach ($color_wise_all_data as $color_id => $color_data) 
			                       								{
			                       									
			                       									
			                       									if($hr>0)
			                       									{
			                       										echo "&nbsp;<hr>";
			                       									}
			                       									
			                       		                   			// echo number_format($color_data['fin_fab_qnty'],2) ;
			                       		                   			  echo number_format($color_wise_booking_data_array[$po_id][$color_id]['fin_fab_qnty'],2) ;
			                       		                   			 $hr++;
			                       		                   		}

								                       		 ?>
								                       		</p>
								                        </td>
								                        <td  >
								                        	<p>
								                       		<?php 

								                       			$hr=0;
								                       			foreach ($color_wise_all_data as $color_id => $color_data) 
			                       								{
			                       									
			                       									if($hr>0)
			                       									{
			                       										echo "&nbsp;<hr>";
			                       									}
			                       		                   			 echo $color_data['qc_pass_qnty'] ;
			                       		                   			 $hr++;

			                       		                   		}

								                       		 ?>
								                       		</p>
								                       </td>
								                       
								                      
								                       <td  >
								                       	<p>
       							                       		<?php 
       							                       			$hr=0;

       							                       			foreach ($color_wise_all_data as $color_id => $color_data) 
       		                       								{
       		                       									
       		                       									
       		                       									if($hr>0)
       		                       									{
       		                       										echo "&nbsp;<hr>";
       		                       									}
			                       		                   			// echo number_format($color_data['booking_req_qnt_minus_qc_pass_qnty'],2) ;
			                       		                   			 echo number_format($color_wise_booking_data_array[$po_id][$color_id]['fin_fab_qnty']-$color_data['qc_pass_qnty'],2) ;
       		                       		                   			 //echo $color_data['dyeing_qty'] ."&nbsp;<hr>";
			                       		                   			 $hr++;
       		                       		                   		}

       							                       		 ?>
       							                       		</p>
       							                       		 
								                        </td>

								                       <td  >
								                       	<p>
       							                       		<?php 
       							                       			$hr=0;

       							                       			foreach ($color_wise_all_data as $color_id => $color_data) 
       		                       								{
       		                       									
       		                       									
       		                       									if($hr>0)
       		                       									{
       		                       										echo "&nbsp;<hr>";
       		                       									}
			                       		                   			 echo $color_library[$color_id] ; ;
       		                       		                   			 //echo $color_data['dyeing_qty'] ."&nbsp;<hr>";
			                       		                   			 $hr++;
       		                       		                   		}

       							                       		 ?>
       							                       		</p>
       							                       		
								                        </td>
								                      <td  >
								                      	<p>
       							                       		<?php 
       							                       			$hr=0;

       							                       			foreach ($color_wise_all_data as $color_id => $color_data) 
       		                       								{
       		                       									
       		                       									
       		                       									if($hr>0)
       		                       									{
       		                       										echo "&nbsp;<hr>";
       		                       									}
			                       		                   			echo number_format($color_data['size_qty'],2) ;
       		                       		                   			 //echo $color_data['dyeing_qty'] ."&nbsp;<hr>";
			                       		                   			$hr++;
       		                       		                   		}

       							                       		 ?>
       							                       		</p>
       							                       		
								                        </td>
								                     

								                        <td  >
								                        	<p>
       							                       		<?php 
       							                       			$hr=0;

       							                       			foreach ($color_wise_all_data as $color_id => $color_data) 
       		                       								{
       		                       									
       		                       									
       		                       									if($hr>0)
       		                       									{
       		                       										echo "&nbsp;<hr>";
       		                       									}
			                       		                   			echo number_format($color_data['sew_recv_qnty_in'],2) ;
       		                       		                   			 //echo $color_data['dyeing_qty'] ."&nbsp;<hr>";
			                       		                   			$hr++;
       		                       		                   		}

       							                       		 ?>
       							                       		</p>
       							                       		
								                        </td>
								                       <td  >
								                       	<p>
       							                       		<?php 
       							                       			$hr=0;

       							                       			foreach ($color_wise_all_data as $color_id => $color_data) 
       		                       								{
       		                       									
       		                       									
       		                       									if($hr>0)
       		                       									{
       		                       										echo "&nbsp;<hr>";
       		                       									}

			                       		                   			echo number_format($color_data['finish_qnty_in'],2) ;
       		                       		                   			 //echo $color_data['dyeing_qty'] ."&nbsp;<hr>";
			                       		                   			$hr++;
       		                       		                   		}

       							                       		 ?>
       							                       		</p>
       							                       		
								                        </td>

								                       <td  >
								                       	<p>
       							                       		<?php 
       							                       			$hr=0;

       							                       			foreach ($color_wise_all_data as $color_id => $color_data) 
       		                       								{
       		                       									
       		                       									
       		                       									if($hr>0)
       		                       									{
       		                       										echo "&nbsp;<hr>";
       		                       									}
			                       		                   			echo number_format($color_data['ex_factory_qnty'],2) ;
       		                       		                   			 //echo $color_data['dyeing_qty'] ."&nbsp;<hr>";
			                       		                   			$hr++;
       		                       		                   		}

       							                       		 ?>
       							                       		</p>
       							                       		
								                        </td>
									                   
									        	  	</tr>
													<?	
												     

												      	$knitting_issue_total+=$knitting_issue;
												   		$knitting_receive_total+=$knitting_receive;
												   		$knitting_receive_weight_total+=$knitting_receive_weight;
												   		$balance_total+=$balance;
												

											
											   		
										}
									
									
								 }

							
								?>

								

	                    </tbody>
	                   
	                     
	                         
									    
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




