<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$colorname_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

if ($action=="print_report_button_setting")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=59 and is_deleted=0 and status_active=1");
	echo $print_report_format; 	
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
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref",3=> "Lot Ratio No");
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'knitting_production_summary_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
			$search_field = " and c.cut_num_prefix_no like ".$search_string;
	}
	

	$start_date = $data[3];
	$end_date = $data[4];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = " and b.insert_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and b.insert_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_arr, 1 => $buyer_short_library);
	if ($db_type == 0)
	{
		$year_field = "YEAR(b.insert_date)";
    //$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(b.insert_date,'YYYY')";
    //$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
	}
	else
	{$year_field = "";
   // $year_cond = "";
    } //defined Later
    
  
  	// if($search_by == 3)
  	// {
  	// 	$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,b.insert_date
	// 	    from wo_po_break_down a, wo_po_details_master b,ppl_cut_lay_mst c where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	//         b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,b.insert_date order by job_no";
  	// }
  	// else
  	// {
  	// 	$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,b.insert_date
	// 	    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	//         b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,b.insert_date order by job_no";

  	// }
	if ($_SESSION['logic_erp']["data_level_secured"]==1)
	{
		if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
	}
	else $buyer_id_cond="";
	
  	if($search_by == 3)
  	{
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,$year_field as year
		    from wo_po_break_down a, wo_po_details_master b,ppl_cut_lay_mst c where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond $buyer_id_cond  $search_field  group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,b.insert_date order by job_no";
  	}
  	else
  	{
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,$year_field as year
		    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond  $buyer_id_cond $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,b.insert_date order by job_no";
  	}

	

	// echo $sql;

	$conclick="id,job_no";
	 $style=$data[5];
	if($style==1)
	{
		$conclick="id,style_ref_no";
	}

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "150,130,140,100", "760", "320", 0, $sql, "js_set_value_job", $conclick, "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "", '', '0,0,0,0,0,0,3', '',1);
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

	if(str_replace("'","",$cbo_buyer_name)==0)  $buyer_name=""; else $buyer_name="and b.buyer_name=".str_replace("'","",$cbo_buyer_name)."";

	if(str_replace("'","",$hidden_job_id)!="")  $job_cond_id=where_con_using_array(explode(",", str_replace("'","",$hidden_job_id)),0,"b.id");

	else  if (str_replace("'","",$txt_job_no)=="") $job_cond_id=""; else $job_cond_id="and b.job_no like '%".str_replace("'","",$txt_job_no)."%'";

	if(str_replace("'","",$hidden_style_id)!="")  $style_cond= where_con_using_array(explode(",", str_replace("'","",$hidden_style_id)),0,"b.id");

	else  if (str_replace("'","",$txt_style_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no like '%".str_replace("'","",$txt_style_no)."%' ";
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

	

	$shipping_status_cond="";
	if(str_replace("'","",$cbo_status)==3) $shipping_status_cond=" and d.shiping_status=3";
	else if(str_replace("'","",$cbo_status)==2) $shipping_status_cond=" and d.shiping_status!=3";
	else $shipping_status_cond="";
	
	
  	if($type==1)
  	{
		$po_number_data=array();
		 $production_data_arr=array();
		 $po_number_id=array();

	 	 
			  if(str_replace("'","",trim($txt_date_from))=="" ) $production_date="";
			  else $production_date=" and e.production_date  =$txt_date_from ";

			 if ($db_type == 0)
			{
				$year_field = ",YEAR(a.insert_date) as year";
		    	//$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
			}
			else if ($db_type == 2)
			{
				$year_field = ",to_char(a.insert_date,'YYYY') as year";
		    	//$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
			}
			else
			{	
				$year_field = "";
		   		
		    }
			// ================================ knitting issue/receive data ===========================
			if(str_replace("'","",$hidden_job_id)!="")  $job_id_cond=where_con_using_array(explode(",", str_replace("'","",$hidden_job_id)),0,"a.job_id");
			$order_id_conds = str_replace("b.order_id", "b.po_break_down_id", $order_id_cond);
			$company_conds = str_replace("d.company_id", "b.company_id", $company_cond);
			$prod_date = str_replace("e.production_date", "b.production_date", $production_date);

			$prod_sql="SELECT a.job_id,a.job_no_mst as job_no,a.po_break_down_id as order_id, a.color_number_id as color_id,a.item_number_id,a.size_number_id as size_id, 
			sum (case when c.production_type = 50 then  c.production_qnty else 0 end) as knitting_issue ,				
			sum (case when c.production_type = 51 then  c.bundle_qty else 0 end) as knitting_receive_weight ,
			sum (case when c.production_type = 51 then c.production_qnty else 0 end) as knitting_receive
			from wo_po_color_size_breakdown a, pro_garments_production_mst b, pro_garments_production_dtls c 
			where a.id=c.color_size_break_down_id and a.po_break_down_id=b.po_break_down_id and b.id=c.mst_id and c.production_type in(50,51) $company_conds $working_company_cond $prod_date  $job_id_cond and a.status_active=1 and b.status_active=1 and c.status_active=1
			group by a.job_id,a.job_no_mst,a.color_number_id,a.item_number_id,a.size_number_id,a.po_break_down_id";
			// echo $prod_sql;die();
			$prod_res = sql_select($prod_sql);
			if(count($prod_res)==0)
			{
				?>
				<div class="alert alert-danger">Data not found! Please try again.</div>
				<?
				die();
			}
			$order_id_arr = array();
			$job_id_arr = array();

			foreach ($prod_res as $row)
			{			  		
				$kniting_issue_rcv[$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_issue']+=$row[csf('knitting_issue')];
				$kniting_issue_rcv[$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_receive_weight']+=$row[csf('knitting_receive_weight')];
				$kniting_issue_rcv[$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_receive']+=$row[csf('knitting_receive')];

				$order_id_arr[$row['ORDER_ID']] = $row['ORDER_ID'];
				$job_id_arr[$row['JOB_ID']] = $row['JOB_ID'];
				
			}
			$po_id_cond = where_con_using_array($order_id_arr,0,"a.id");
			$job_id_cond = where_con_using_array($job_id_arr,0,"b.id");

		   /*  if(str_replace("'","",$txt_job_no)=="" && str_replace("'","",$txt_style_no)=="" && str_replace("'","",$txt_date_from)!="")
			{
				$lay_date = str_replace("e.production_date", "d.entry_date", $production_date);
				$order_id_arr = array();
				$sql="SELECT b.order_id
					from ppl_cut_lay_bundle b, ppl_cut_lay_mst d 
					where d.id=b.mst_id and b.status_active = 1 and b.is_deleted = 0 and d.status_active=1 $company_cond $working_company_cond $lay_date";
				// echo $sql;	die();
				$res = sql_select($sql);	
				foreach ($res as $val) 
				{
					$order_id_arr[$val['ORDER_ID']] = $val['ORDER_ID'];
				}

				$po_id_cond = where_con_using_array($order_id_arr,0,"a.id");
			} */


	 

			$po_sql="SELECT  a.id,a.job_no_mst,a.po_number,d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,b.buyer_name,
			  b.style_ref_no as style,d.country_ship_date,d.color_number_id,d.item_number_id,d.size_number_id,b.company_name,a.excess_cut,b.gauge $year_field
			  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
			  where a.job_id=b.id and a.id=d.po_break_down_id and  a.job_id=d.job_id and  a.is_deleted=0 and a.status_active in(1,3) and 
			  b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and
			  
			  b.status_active=1 $company_name $buyer_name $style_cond  $job_cond_id  $shipping_status_cond $job_id_cond order by a.job_no_mst,a.po_number,d.country_ship_date";
			 //echo $po_sql;
		    $pro_date_sql=sql_select ($po_sql);

		     

			  


			 
			  $po_id_marge=array();
			  $po_country_arr=array();
			  $po_plan_cutqty_arr=array();
			  $order_qty_arr=array();
			  $job_no_arr=array();
			  $job_no_list=array();
			  foreach($pro_date_sql as $row)
			  {
				  $job_no_arr[$row[csf('job_no_mst')]]=$row[csf('job_no_mst')];

				 // $job_number_data[$row[csf('job_no_mst')]]['id']=$row[csf('id')];
				  $job_number_data[$row[csf('job_no_mst')]]['job_no']=$row[csf('job_no_mst')];
				  $job_number_data[$row[csf('job_no_mst')]]['po_number']=$row[csf('po_number')];
				  $job_number_data[$row[csf('job_no_mst')]]['po_quantity']+=$row[csf('order_qty')];
				  $job_number_data[$row[csf('job_no_mst')]]['plan_qty']+=$row[csf('plan_qty')];
				  $job_number_data[$row[csf('job_no_mst')]]['buyer_name']=$row[csf('buyer_name')];
				  $job_number_data[$row[csf('job_no_mst')]]['company_name']=$row[csf('company_name')];
				  $job_number_data[$row[csf('job_no_mst')]]['style']=$row[csf('style')];
				  $job_number_data[$row[csf('job_no_mst')]]['country_ship_date']=$row[csf('country_ship_date')];
				  $job_number_data[$row[csf('job_no_mst')]]['item_number_id'][]=$row[csf('item_number_id')];
				  $job_number_data[$row[csf('job_no_mst')]]['color_id']=$row[csf('color_number_id')];
				  $job_number_data[$row[csf('job_no_mst')]]['excess_cut']=$row[csf('excess_cut')];
				  $job_number_data[$row[csf('job_no_mst')]]['gauge']=$row[csf('gauge')];
				  $job_number_data[$row[csf('job_no_mst')]]['year']=$row[csf('year')];
				  $job_plan_cutqty_arr[$row[csf('id')]]['plan_qty']+=$row[csf('plan_qty')];

				  $order_qty_arr[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] += $row[csf('order_qty')];

				  array_push( $po_number_id, $row[csf('id')]);
				  array_push( $job_no_list, $row[csf('job_no_mst')]);
			  }

			  // echo "<pre>";
			  // print_r( $po_number_data);
			  // echo "</pre>";

			$po_number_id=array_unique($po_number_id);
			$job_no_list=array_unique($job_no_list);
			$order_id_cond="";
			if(count($po_number_id))
			{
				$order_id_cond=where_con_using_array($po_number_id,0,"b.order_id");
			}

			$all_job_nos = "'".implode("','", $job_no_arr)."'";

			// ======================= getting Size Set Weight ==============================
			$sql = "SELECT a.JOB_NO,b.COLOR_ID,b.GMT_SIZE_ID,b.PRODUCTION_WEIGHT from ppl_size_set_mst a,ppl_size_set_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.job_no in($all_job_nos)";
			// echo $sql;die();
			$res = sql_select($sql);
			$size_set_weight_array = array();
			foreach ($res as $val) 
			{
				$size_set_weight_array[$val['JOB_NO']][$val['COLOR_ID']][$val['GMT_SIZE_ID']] += $val['PRODUCTION_WEIGHT'];
			}
			   
			
			/*$prod_sql="SELECT d.company_id, d.source, d.working_company_id, b.size_id, a.gmt_item_id, a.color_id, e.production_date, d.cutting_no, d.job_no, e.delivery_mst_id, b.order_id, sum(a.marker_qty) as lot_ratio_qnty, a.roll_data, sum (case when c.production_type = 50 then  b.size_qty else 0 end) as knitting_issue 
			from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c, pro_garments_production_mst e, ppl_cut_lay_mst d 
			where c.bundle_no = b.bundle_no and c.barcode_no = b.barcode_no and d.id=a.mst_id and a.id = b.dtls_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and d.status_active=1 and e.status_active=1 and e.id=c.mst_id and c.production_type =50 $company_cond $working_company_cond $production_date $order_id_cond 
			group by d.company_id, d.source, d.working_company_id, b.size_id, a.gmt_item_id, a.color_id, e.production_date, d.cutting_no, d.job_no, e.delivery_mst_id, b.order_id, a.roll_data 
			order by d.cutting_no, e.production_date ";*/

			$lay_date = str_replace("e.production_date", "d.entry_date", $production_date);

			$prod_sql="SELECT d.company_id, d.source, d.working_company_id, b.size_id, a.gmt_item_id, a.color_id, d.cutting_no, d.job_no, b.order_id, sum(a.marker_qty) as lot_ratio_qnty, a.roll_data,b.id
			from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, ppl_cut_lay_mst d 
			where d.id=a.mst_id and a.id = b.dtls_id and d.id=b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and d.status_active=1 $company_cond $working_company_cond $lay_date $order_id_cond 
			group by d.company_id, d.source, d.working_company_id, b.size_id, a.gmt_item_id, a.color_id, d.cutting_no, d.job_no,b.order_id, a.roll_data,b.id 
			order by d.cutting_no";

				// echo $prod_sql;
			  	$production_mst_sql=sql_select($prod_sql);

			  	$issue_wise=array();
			  	$delevery_mst_ids=array();
			  	$order_id_arrs=array();
			  	foreach ($production_mst_sql as $row)
			  	{
			  		array_push($order_id_arrs, $row[csf('order_id')]);
			  	}

			  	$order_id_arrs=array_unique($order_id_arrs);

			  	$order_id_cond_bundle="";
				if(count($order_id_arrs))
				{
					$order_id_cond_bundle=where_con_using_array($order_id_arrs,0,"b.order_id");
				}

				

			  	// echo "<pre>";print_r($kniting_issue_rcv);die();

                $pop_sql="
                 			SELECT
                 			       	a.gmt_item_id,
							        a.color_id,
							        b.size_id,
							        sum(b.size_qty) as size_qty,
							        b.order_id,
							        d.job_no
                 			  from ppl_cut_lay_dtls a,
                 			       ppl_cut_lay_bundle b,
                 			       ppl_cut_lay_mst d
                 			       
                 			 where     
                 			      d.id=a.mst_id
                 			       and a.id = b.dtls_id
                 			       and d.id=b.mst_id
                 			       and a.status_active = 1
                 			       and d.status_active = 1
                 			       and a.is_deleted = 0
                 			       and b.status_active = 1
                 			       and b.is_deleted = 0
                 			      
                 			       $order_id_cond_bundle
                 			group by 
                 					a.gmt_item_id,
							        a.color_id,
							        b.size_id,
							        b.order_id,
							        d.job_no
                 		  	order by 
                 		  			
                 		  			b.size_id
                 		  ";
                // echo $pop_sql;
                 $pop_result=sql_select($pop_sql);

                 $pop_up_data=array();

                 foreach ($pop_result as $row) 
                 {
                 	$pop_up_data[$row[csf('job_no')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]+=$row[csf('size_qty')];
                 }

                 $order_id_cond_plan_cut=str_replace("b.order_id", "po_break_down_id", $order_id_cond_bundle);

                 $plan_cust_res=sql_select("SELECT 
											 
											item_number_id, 
											size_number_id, 
											plan_cut_qnty, 
											color_number_id ,
											job_no_mst
										from 
											wo_po_color_size_breakdown 
										where 
											 
											status_active=1 and 
											is_deleted=0  $order_id_cond_plan_cut ");

                 $plan_cut_arr=array();
                 $plan_cut_mst=array();

                 foreach ($plan_cust_res as $row) {
                 	$plan_cut_arr[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];                                                                                                                                      
                 	$plan_cut_mst[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];                                                                                                                                      
                 }
		
	               

			  	foreach ($production_mst_sql as $row)
			  	{
			  		$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['delivery_mst_id']=$row[csf('delivery_mst_id')];
			  		$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['company_id']=$row[csf('company_id')];
			  		$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['source']=$row[csf('source')];
			  		$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['working_company_id']=$row[csf('working_company_id')];
			  		$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['size_id']=$row[csf('size_id')];
			  		$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['gmt_item_id']=$row[csf('gmt_item_id')];
			  		$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['color_id']=$row[csf('color_id')];
			  		$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['production_date']=$row[csf('production_date')];
			  		$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['cutting_no']=$row[csf('cutting_no')];
			  		$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['job_no']=$row[csf('job_no')];
			  		// $issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_issue']+=$kniting_issue_rcv[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_issue'];
			  		$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['lot_ratio_qnty']+=$row[csf('lot_ratio_qnty')];
			  		$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['roll_data'].=$row[csf('roll_data')]."**";
			  		$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['total_bundle']++;
			  		
			  	}

			  	$mst_id_cond="";
			  	if(count($delevery_mst_ids))
			  	{

			  		$delevery_mst_ids=array_unique($delevery_mst_ids);
			  		$mst_id_cond=where_con_using_array($delevery_mst_ids,0,"d.issue_challan_id");



			  	}
			
			  	$data_arr=array();

			  	
				foreach ($issue_wise as $gmt_item_id => $item_data) 
				{
					foreach ($item_data as $color_id => $color_data) 
					{

						foreach ($color_data as $size_id => $row) 
						{


							

						
							
							$job_no=$row['job_no'];

							$working_company_id=$row['working_company_id'];
							$$company_id=$row['company_id'];
							$knitting_issue=$row['knitting_issue'];

							// $knitting_receive_weight=$kniting_issue_rcv[$gmt_item_id][$color_id][$size_id]['knitting_receive_weight'];
							// $knitting_receive=$kniting_issue_rcv[$gmt_item_id][$color_id][$size_id]['knitting_receive'];
							$production_source=$row['source'];

							
							
							
						
						
					   	
							
							
						  	
							  
							  $buyer_name=$job_number_data[$job_no]['buyer_name'];
							  $style=$job_number_data[$job_no]['style'];
							  $year=$job_number_data[$job_no]['year'];

							  $order_qty=$job_number_data[$job_no]['po_quantity'];

							  
							  $country_ship_date=$job_number_data[$job_no]['country_ship_date'];
							$working_company='';

							if($production_source==1)
						  	{
						  		$working_company= $company_arr[$working_company_id];
						  	}
						  	else 
						  	{
						  		$working_company= $supplier_arr[$working_company_id];  
						  	}

							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['job_no'].=$job_no." , ";
							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['buyer_name'].=$buyer_arr[$buyer_name]." , ";
							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['style'].=$style." , ";
							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['year'].=$year." , ";
							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['country_ship_date'].=$country_ship_date." , ";
							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['cutting_no'].=$row['cutting_no']." , ";
							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['working_company'].=$working_company." , ";
							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['roll_data'].=$row['roll_data']."**";

							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['knitting_issue']+=$knitting_issue;
							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['knitting_receive']+=$knitting_receive;
							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['knitting_receive_weight']+=$knitting_receive_weight;
							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['order_qty']+= $order_qty;
							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['total_bundle']+= $row['total_bundle'];
							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['lot_ratio_qnty']+= $row['lot_ratio_qnty'];
							  $data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['size_qty']+= $row['size_qty'];
						}

					}
				
				}					

				// echo "<pre>";	print_r($data_arr);die();
				
				$order_qty_total=0;
				$lot_ratio_qty_total=0;
				$lot_ratio_weight_total=0;
				$bundle_qnty_total=0;
		   		$knitting_issue_total=0;
		   		$knitting_receive_total=0;
		   		$knitting_receive_weight_total=0;
		   		$balance_total=0;
		  ob_start();
	  

		 ?>
  		<fieldset style="width:1630px;">
        	   <table  cellspacing="0" style="justify-content: center;text-align: center;width: 1580px;" >
                    <tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
                           <td colspan="17" align="center" style="border:none;font-size:14px; font-weight:bold" > Knitting Production Summary Report</td>
                    </tr>
                    <tr style="border:none;justify-content: center;text-align: center;">
                           <td colspan="17" align="center" style="border:none; font-size:16px; font-weight:bold">
                            Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
                           </td>
                     </tr>
                     <tr style="border:none;justify-content: center;text-align: center;">
                           <td colspan="17" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? echo "Production date ".change_date_format(str_replace("'","",$txt_date_from)) ;?>
                           </td>
                     </tr>
              </table>
             <br />	


            

             <br>

            
           
            
             <div style="width:1690px; margin-left:18px" >
             		<table cellspacing="0" border="1" class="rpt_table" width="1660" rules="all">
             			<thead>
             				<tr >
		                       <th width="40" >SL</th>
		                       <th width="130" >Buyer</th>
		                       <th width="110" >Style</th>
		                       <th width="70"> Job Year</th>
		                       <th width="120" >Job</th>
		                       <th width="130" >GMT Item</th>
		                       <th width="80" >C. Ship Date</th>
		                       <th width="160" >Gmts. Color</th>                        
		                       <th width="80" >Size</th>
		                       <th width="80" >Order  Qty (Pcs)</th>
		                       <th width="80">Lot Ratio Qty. (Pcs)</th>
		                       <th width="80">Bundle Qty.</th>
		                       <th width="80">Lot Ratio <br>Weight (Lbs)</th>
		                       <th width="80" >Knitting <br>Issue<br> ( Pcs)</th>
		                       <th width="80" >Knitting <br>Issue<br> Balance</th>
		                       <th width="80" >Knitting <br>Receive<br> (Pcs)</th>
		                       <th width="80">Knitting <br>Receive  <br>Weight<br> (Lbs)</th>
		                       <th width="80">Knitting  <br>Receive <br>Balance</th>
		                      
		                    </tr>
             			</thead>
             		</table>
                    <table cellspacing="0" border="1" class="rpt_table"  width="1660" rules="all"   style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body">
                    	
		                <tbody >
		                	
		                	
	                  			<?
	               
	                     		 $i=1;
				                 $knit=0;
								
								foreach($data_arr as $job_no_mst => $delivery_data)	
								{
									foreach ($delivery_data as $gmt_item_id => $item_data) 
									{
										foreach ($item_data as $color_id => $color_data) 
										{

											foreach ($color_data as $size_id => $row) 
											{
												
												$knitting_issue = $kniting_issue_rcv[$job_no_mst][$gmt_item_id][$color_id][$size_id]['knitting_issue'];

												$knitting_receive_weight=$kniting_issue_rcv[$job_no_mst][$gmt_item_id][$color_id][$size_id]['knitting_receive_weight'];
												$knitting_receive=$kniting_issue_rcv[$job_no_mst][$gmt_item_id][$color_id][$size_id]['knitting_receive'];
												// $order_qty=$row['order_qty'];
												$order_qty=$order_qty_arr[$job_no_mst][$gmt_item_id][$color_id][$size_id];

												$working_company=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['working_company'] , " , "))) , 'strlen'));
												$buyer_name=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['buyer_name'] , " , "))), 'strlen'));
												$style=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['style'] , " , "))), 'strlen'));
												$year=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['year'] , " , "))), 'strlen'));
												$job_no=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['job_no'] , " , "))), 'strlen'));
												$country_ship_date=implode(",", array_filter(array_unique(explode(" , ", chop($row['country_ship_date'] , " , "))), 'strlen'));											

												$cutting_no=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['cutting_no'] , " , "))), 'strlen'));

												/*$roll_data=$row['roll_data'];

												$yarn_ratio_form_msert=explode("**",$roll_data);
												$total_lot_qty=0;
												foreach($yarn_ratio_form_msert as $single_yarn_data)
												{
													
													$single_yarn_data_arr= array_unique(explode("=",$single_yarn_data));
													//echo $single_yarn_data;
													$total_lot_qty+=$single_yarn_data_arr[5];
												}

												$plan_cut_qty=$plan_cut_arr[$job_no_mst][$gmt_item_id][$color_id][$size_id];
												$total_plan_cut_qty=$plan_cut_mst[$job_no_mst][$gmt_item_id][$color_id];

												$plan_qty=($plan_cut_qty/$total_plan_cut_qty)*$total_lot_qty;*/

												$size_set_weight = $size_set_weight_array[$job_no_mst][$color_id][$size_id];
												$lot_ratio_qnty = $pop_up_data[$job_no_mst][$gmt_item_id][$color_id][$size_id];
												$lot_ratio_weight = ($size_set_weight*$lot_ratio_qnty*0.00220462);

												$knitting_issue_bal=$lot_ratio_qnty-$knitting_issue;
												$balance=$knitting_issue-$knitting_receive;
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
												  // $country_ship_date=$row['country_ship_date'];
												  
								                ?>
								                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
								                    <td width="40" align="left"><? echo $i; ?></td>
								                   
								                   
								                    <td width="130" align="left"><p><? echo $buyer_name; ?></p></td>
								                    <td width="110" align="left"><p><? echo $style;?></p></td>
								                    <td width="70" align="left"><p><? echo $year;?></p></td>
								                    <td width="120" align="left"><p><? echo  $job_no; ?>  </p></td>
								                    <td  width="130" align="left"><p><? echo  $garments_item[$gmt_item_id]; ?> </p></td>
								                    <td width="80" align="left"><p>
								                    	<? 
								                    	$cdate_ex = explode(",", $country_ship_date);
								                    	$country_ship_date = "";
								                    	foreach ($cdate_ex as $key => $value) 
								                    	{
								                    		$country_ship_date .= ($country_ship_date=="") ? change_date_format($value) : ",".change_date_format($value) ;
								                    	}
								                    	echo $country_ship_date;
								                    	?>
								                    
								                    	</p></td>
								                    <td width="160" align="left"> <p><?php echo $color_library[$color_id]; ?> </p></td>
								                    <td width="80" align="left"> <p><?php echo $size_library[$size_id]; ?> </p></td>
								                   
								                     <td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($order_qty,0) ?></td>

								                    <td width="80" style="justify-content: right;text-align: right;"><a href='##' onClick="generate_report2(<? echo "'".$job_no_mst . "'," . $gmt_item_id.",".$color_id.",".$size_id; ?>)"><?php echo $pop_up_data[$job_no_mst][$gmt_item_id][$color_id][$size_id];?> </a> </td>
								                    <td width="80"  style="justify-content: right;text-align: right;"> <p><?php echo number_format($row['total_bundle']); ?></p></td>

								                    
								                    <td width="80" style="justify-content: right;text-align: right;"><p><?php echo number_format($lot_ratio_weight,2); ?></p> </td>
								                    <td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_issue,2) ?></td>
								                    <td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_issue_bal,2) ?></td>

								                    <td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_receive,2) ?></td>
								                    <td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_receive_weight,2) ?></td>
								                    <td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($balance,0) ?></td>
								                   
								        	  	</tr>
												<?	
											     
													$order_qty_total+=$order_qty;
													$lot_ratio_qty_total+=$pop_up_data[$job_no_mst][$gmt_item_id][$color_id][$size_id];
													$bundle_qnty_total+=$row['total_bundle'];
													$lot_ratio_weight_total+=$plan_qty;
											      	$knitting_issue_total+=$knitting_issue;
											      	$knitting_issue_bal_total=$knitting_issue_bal;
											   		$knitting_receive_total+=$knitting_receive;
											   		$knitting_receive_weight_total+=$knitting_receive_weight;
											   		$balance_total+=$balance;
											   		
												 $i++;	

											}

										}
														

									}
										
								}
							
								?>

								

	                    </tbody>
	                   
	                     
	                         
									    
	                </table> 
	                <table cellspacing="0" border="1" class="rpt_table" width="1660" rules="all">
	                	
	                	 <tfoot>
	                    	 <tr>
		                      		
	                      		<th width="920" colspan="9" style="justify-content: right;text-align: right;">Total</th>
	                      		
	                      		
	                      		<th width="80" style="justify-content: right;text-align: right;" id="order_qty_total"> <?php echo number_format($order_qty_total,0) ?></th>
	                      		<th width="80" style="justify-content: right;text-align: right;" id="lot_ratio_qty_total"> <?php echo number_format($lot_ratio_qty_total,2) ?></th>
	                      		<th width="80" style="justify-content: right;text-align: right;" id="bundle_qnty_total"> <?php echo number_format($bundle_qnty_total,2) ?></th>
	                      		<th width="80" style="justify-content: right;text-align: right;" id="lot_ratio_weight_total"> <?php echo number_format($lot_ratio_weight_total,2) ?></th>


	                      		<th width="80" style="justify-content: right;text-align: right;" id="knitting_issue"> <?php echo number_format($knitting_issue_total,2) ?></th>
	                      		<th width="80" style="justify-content: right;text-align: right;" id="knitting_issue_bal"> <?php echo number_format($knitting_issue_bal_total,2) ?></th>

	                      		<th width="80" style="justify-content: right;text-align: right;" id="knitting_receive"><?php echo number_format($knitting_receive_total,2) ?></th>
	                      		<th width="80" style="justify-content: right;text-align: right;" id="knitting_receive_weight"><?php echo number_format($knitting_receive_weight_total,2) ?></th>
	                      		<th width="80" style="justify-content: right;text-align: right;" id="balance"><?php echo number_format($balance_total,0) ?></th>
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
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename####$type";
		exit(); 
	}
	else if($type==2)
	{
	  $po_number_data=array();
	   $production_data_arr=array();
	   $po_number_id=array();

		
			if(str_replace("'","",trim($txt_date_from))=="" ) $production_date="";
			else $production_date=" and e.production_date  =$txt_date_from ";

		   if ($db_type == 0)
		  {
			  $year_field = ",YEAR(a.insert_date) as year";
			  //$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
		  }
		  else if ($db_type == 2)
		  {
			  $year_field = ",to_char(a.insert_date,'YYYY') as year";
			  //$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
		  }
		  else
		  {	
			  $year_field = "";
				 
		  }
		  // ================================ knitting issue/receive data ===========================
		  if(str_replace("'","",$hidden_job_id)!="")  $job_id_cond=where_con_using_array(explode(",", str_replace("'","",$hidden_job_id)),0,"a.job_id");
		  $order_id_conds = str_replace("b.order_id", "b.po_break_down_id", $order_id_cond);
		  $company_conds = str_replace("d.company_id", "b.company_id", $company_cond);
		  $prod_date = str_replace("e.production_date", "b.production_date", $production_date);

		  $prod_sql="SELECT a.job_id,a.job_no_mst as job_no,a.po_break_down_id as order_id, a.color_number_id as color_id,a.item_number_id,a.size_number_id as size_id,c.bodypart_type_id, 
		  sum (case when c.production_type = 50 then  c.production_qnty else 0 end) as knitting_issue ,				
		  sum (case when c.production_type = 51 then  c.bundle_qty else 0 end) as knitting_receive_weight ,
		  sum (case when c.production_type = 51 then c.production_qnty else 0 end) as knitting_receive
		  from wo_po_color_size_breakdown a, pro_garments_production_mst b, pro_garments_production_dtls c 
		  where a.id=c.color_size_break_down_id and a.po_break_down_id=b.po_break_down_id and b.id=c.mst_id and c.production_type in(50,51) $company_conds $working_company_cond $prod_date  $job_id_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.bodypart_type_id =2
		  group by a.job_id,a.job_no_mst,a.color_number_id,a.item_number_id,a.size_number_id,a.po_break_down_id,c.bodypart_type_id";
		  //echo $prod_sql;//die();
		  $prod_res = sql_select($prod_sql);
		  if(count($prod_res)==0)
		  {
			  ?>
			  <div class="alert alert-danger">Data not found! Please try again.</div>
			  <?
			  die();
		  }
		  $order_id_arr = array();
		  $job_id_arr = array();

		  foreach ($prod_res as $row)
		  {			  		
			  $kniting_issue_rcv[$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_issue']+=$row[csf('knitting_issue')];
			  $kniting_issue_rcv[$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_receive_weight']+=$row[csf('knitting_receive_weight')];
			  $kniting_issue_rcv[$row[csf('job_no')]][$row[csf('item_number_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_receive']+=$row[csf('knitting_receive')];

			  $order_id_arr[$row['ORDER_ID']] = $row['ORDER_ID'];
			  $job_id_arr[$row['JOB_ID']] = $row['JOB_ID'];
			  
		  }
		  $po_id_cond = where_con_using_array($order_id_arr,0,"a.id");
		  $job_id_cond = where_con_using_array($job_id_arr,0,"b.id");

		 /*  if(str_replace("'","",$txt_job_no)=="" && str_replace("'","",$txt_style_no)=="" && str_replace("'","",$txt_date_from)!="")
		  {
			  $lay_date = str_replace("e.production_date", "d.entry_date", $production_date);
			  $order_id_arr = array();
			  $sql="SELECT b.order_id
				  from ppl_cut_lay_bundle b, ppl_cut_lay_mst d 
				  where d.id=b.mst_id and b.status_active = 1 and b.is_deleted = 0 and d.status_active=1 $company_cond $working_company_cond $lay_date";
			  // echo $sql;	die();
			  $res = sql_select($sql);	
			  foreach ($res as $val) 
			  {
				  $order_id_arr[$val['ORDER_ID']] = $val['ORDER_ID'];
			  }

			  $po_id_cond = where_con_using_array($order_id_arr,0,"a.id");
		  } */


   

		  $po_sql="SELECT  a.id,a.job_no_mst,a.po_number,d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,b.buyer_name,
			b.style_ref_no as style,d.country_ship_date,d.color_number_id,d.item_number_id,d.size_number_id,b.company_name,a.excess_cut,b.gauge $year_field
			from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
			where a.job_id=b.id and a.id=d.po_break_down_id and  a.job_id=d.job_id and  a.is_deleted=0 and a.status_active=1 and 
			b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and
			
			b.status_active=1 $company_name $buyer_name $style_cond  $job_cond_id  $shipping_status_cond $job_id_cond order by a.job_no_mst,a.po_number,d.country_ship_date";
		   //echo $po_sql;die;
		    $pro_date_sql=sql_select ($po_sql);
		   
			$po_id_marge=array();
			$po_country_arr=array();
			$po_plan_cutqty_arr=array();
			$order_qty_arr=array();
			$job_no_arr=array();
			$job_no_list=array();
			foreach($pro_date_sql as $row)
			{
				$job_no_arr[$row[csf('job_no_mst')]]=$row[csf('job_no_mst')];

			   // $job_number_data[$row[csf('job_no_mst')]]['id']=$row[csf('id')];
				$job_number_data[$row[csf('job_no_mst')]]['job_no']=$row[csf('job_no_mst')];
				$job_number_data[$row[csf('job_no_mst')]]['po_number']=$row[csf('po_number')];
				$job_number_data[$row[csf('job_no_mst')]]['po_quantity']+=$row[csf('order_qty')];
				$job_number_data[$row[csf('job_no_mst')]]['plan_qty']+=$row[csf('plan_qty')];
				$job_number_data[$row[csf('job_no_mst')]]['buyer_name']=$row[csf('buyer_name')];
				$job_number_data[$row[csf('job_no_mst')]]['company_name']=$row[csf('company_name')];
				$job_number_data[$row[csf('job_no_mst')]]['style']=$row[csf('style')];
				$job_number_data[$row[csf('job_no_mst')]]['country_ship_date']=$row[csf('country_ship_date')];
				$job_number_data[$row[csf('job_no_mst')]]['item_number_id'][]=$row[csf('item_number_id')];
				$job_number_data[$row[csf('job_no_mst')]]['color_id']=$row[csf('color_number_id')];
				$job_number_data[$row[csf('job_no_mst')]]['excess_cut']=$row[csf('excess_cut')];
				$job_number_data[$row[csf('job_no_mst')]]['gauge']=$row[csf('gauge')];
				$job_number_data[$row[csf('job_no_mst')]]['year']=$row[csf('year')];
				$job_plan_cutqty_arr[$row[csf('id')]]['plan_qty']+=$row[csf('plan_qty')];

				$order_qty_arr[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]] += $row[csf('order_qty')];

				array_push( $po_number_id, $row[csf('id')]);
				array_push( $job_no_list, $row[csf('job_no_mst')]);
			}

			// echo "<pre>";
			// print_r( $po_number_data);
			// echo "</pre>";

		  $po_number_id=array_unique($po_number_id);
		  $job_no_list=array_unique($job_no_list);
		  $order_id_cond="";
		  if(count($po_number_id))
		  {
			  $order_id_cond=where_con_using_array($po_number_id,0,"b.order_id");
		  }

		  $all_job_nos = "'".implode("','", $job_no_arr)."'";

		  // ======================= getting Size Set Weight ==============================
		  $sql = "SELECT a.JOB_NO,b.COLOR_ID,b.GMT_SIZE_ID,b.PRODUCTION_WEIGHT from ppl_size_set_mst a,ppl_size_set_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.job_no in($all_job_nos)";
		   //echo $sql;die();
		  $res = sql_select($sql);
		  $size_set_weight_array = array();
		  foreach ($res as $val) 
		  {
			  $size_set_weight_array[$val['JOB_NO']][$val['COLOR_ID']][$val['GMT_SIZE_ID']] += $val['PRODUCTION_WEIGHT'];
		  }
			 
		  
		  /*$prod_sql="SELECT d.company_id, d.source, d.working_company_id, b.size_id, a.gmt_item_id, a.color_id, e.production_date, d.cutting_no, d.job_no, e.delivery_mst_id, b.order_id, sum(a.marker_qty) as lot_ratio_qnty, a.roll_data, sum (case when c.production_type = 50 then  b.size_qty else 0 end) as knitting_issue 
		  from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, pro_garments_production_dtls c, pro_garments_production_mst e, ppl_cut_lay_mst d 
		  where c.bundle_no = b.bundle_no and c.barcode_no = b.barcode_no and d.id=a.mst_id and a.id = b.dtls_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and d.status_active=1 and e.status_active=1 and e.id=c.mst_id and c.production_type =50 $company_cond $working_company_cond $production_date $order_id_cond 
		  group by d.company_id, d.source, d.working_company_id, b.size_id, a.gmt_item_id, a.color_id, e.production_date, d.cutting_no, d.job_no, e.delivery_mst_id, b.order_id, a.roll_data 
		  order by d.cutting_no, e.production_date ";*/

		  $lay_date = str_replace("e.production_date", "d.entry_date", $production_date);

		  $prod_sql="SELECT d.company_id, d.source, d.working_company_id, b.size_id, a.gmt_item_id, a.color_id, d.cutting_no, d.job_no, b.order_id, sum(a.marker_qty) as lot_ratio_qnty, a.roll_data,b.id
		  from ppl_cut_lay_dtls a, ppl_cut_lay_bundle b, ppl_cut_lay_mst d 
		  where d.id=a.mst_id and a.id = b.dtls_id and d.id=b.mst_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and d.status_active=1 $company_cond $working_company_cond $lay_date $order_id_cond 
		  group by d.company_id, d.source, d.working_company_id, b.size_id, a.gmt_item_id, a.color_id, d.cutting_no, d.job_no,b.order_id, a.roll_data,b.id 
		  order by d.cutting_no"; //  

			  //echo $prod_sql;die;
				$production_mst_sql=sql_select($prod_sql);

				$issue_wise=array();
				$delevery_mst_ids=array();
				$order_id_arrs=array();
				foreach ($production_mst_sql as $row)
				{
					array_push($order_id_arrs, $row[csf('order_id')]);
				}

				$order_id_arrs=array_unique($order_id_arrs);

				$order_id_cond_bundle="";
			  if(count($order_id_arrs))
			  {
				  $order_id_cond_bundle=where_con_using_array($order_id_arrs,0,"b.order_id");
			  }

			  

				// echo "<pre>";print_r($kniting_issue_rcv);die();

			  $pop_sql="
						SELECT
								a.gmt_item_id,
								a.color_id,
								b.size_id,
								sum(b.size_qty) as size_qty,
								b.order_id,
								d.job_no
							from ppl_cut_lay_dtls a,
								ppl_cut_lay_bundle b,
								ppl_cut_lay_mst d
								  
							where     
								d.id=a.mst_id
								and a.id = b.dtls_id
								and d.id=b.mst_id
								and a.status_active = 1
								and d.status_active = 1
								and a.is_deleted = 0
								and b.status_active = 1
								and b.is_deleted = 0
								
								$order_id_cond_bundle
						   group by 
								a.gmt_item_id,
								a.color_id,
								b.size_id,
								b.order_id,
								d.job_no
							order by 
								b.size_id
						 ";
			  // echo $pop_sql;
			   $pop_result=sql_select($pop_sql);

			   $pop_up_data=array();

			   foreach ($pop_result as $row) 
			   {
				   $pop_up_data[$row[csf('job_no')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]+=$row[csf('size_qty')];
			   }

			   $order_id_cond_plan_cut=str_replace("b.order_id", "po_break_down_id", $order_id_cond_bundle);

			   $plan_cust_res=sql_select("SELECT 
										   
										item_number_id, 
										size_number_id, 
										plan_cut_qnty, 
										color_number_id ,
										job_no_mst
									from 
										wo_po_color_size_breakdown 
									where 
										
										status_active=1 and 
										is_deleted=0  $order_id_cond_plan_cut ");

			   $plan_cut_arr=array();
			   $plan_cut_mst=array();

			   foreach ($plan_cust_res as $row) {
				   $plan_cut_arr[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];                                                                                                                                      
				   $plan_cut_mst[$row[csf('job_no_mst')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]+=$row[csf('plan_cut_qnty')];                                                                                                                                      
			   }
	  
				 

				foreach ($production_mst_sql as $row)
				{
					$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['delivery_mst_id']=$row[csf('delivery_mst_id')];
					$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['company_id']=$row[csf('company_id')];
					$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['source']=$row[csf('source')];
					$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['working_company_id']=$row[csf('working_company_id')];
					$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['size_id']=$row[csf('size_id')];
					$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['gmt_item_id']=$row[csf('gmt_item_id')];
					$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['color_id']=$row[csf('color_id')];
					$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['production_date']=$row[csf('production_date')];
					$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['cutting_no']=$row[csf('cutting_no')];
					$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['job_no']=$row[csf('job_no')];
					// $issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_issue']+=$kniting_issue_rcv[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_issue'];
					$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['lot_ratio_qnty']+=$row[csf('lot_ratio_qnty')];
					$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['roll_data'].=$row[csf('roll_data')]."**";
					$issue_wise[$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['total_bundle']++;
					
				}

				$mst_id_cond="";
				if(count($delevery_mst_ids))
				{

					$delevery_mst_ids=array_unique($delevery_mst_ids);
					$mst_id_cond=where_con_using_array($delevery_mst_ids,0,"d.issue_challan_id");



				}
		  
				$data_arr=array();

				
			foreach ($issue_wise as $gmt_item_id => $item_data) 
			{
				foreach ($item_data as $color_id => $color_data) 
				{

					foreach ($color_data as $size_id => $row) 
					{
					$job_no=$row['job_no'];

					$working_company_id=$row['working_company_id'];
					$$company_id=$row['company_id'];
					$knitting_issue=$row['knitting_issue'];

					// $knitting_receive_weight=$kniting_issue_rcv[$gmt_item_id][$color_id][$size_id]['knitting_receive_weight'];
					// $knitting_receive=$kniting_issue_rcv[$gmt_item_id][$color_id][$size_id]['knitting_receive'];
					$production_source=$row['source'];

					$buyer_name=$job_number_data[$job_no]['buyer_name'];
					$style=$job_number_data[$job_no]['style'];
					$year=$job_number_data[$job_no]['year'];

					$order_qty=$job_number_data[$job_no]['po_quantity'];

					
					$country_ship_date=$job_number_data[$job_no]['country_ship_date'];
					$working_company='';

					if($production_source==1)
					{
						$working_company= $company_arr[$working_company_id];
					}
					else 
					{
						$working_company= $supplier_arr[$working_company_id];  
					}

					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['job_no'].=$job_no." , ";
					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['buyer_name'].=$buyer_arr[$buyer_name]." , ";
					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['style'].=$style." , ";
					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['year'].=$year." , ";
					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['country_ship_date'].=$country_ship_date." , ";
					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['cutting_no'].=$row['cutting_no']." , ";
					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['working_company'].=$working_company." , ";
					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['roll_data'].=$row['roll_data']."**";

					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['knitting_issue']+=$knitting_issue;
					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['knitting_receive']+=$knitting_receive;
					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['knitting_receive_weight']+=$knitting_receive_weight;
					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['order_qty']+= $order_qty;
					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['total_bundle']+= $row['total_bundle'];
					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['lot_ratio_qnty']+= $row['lot_ratio_qnty'];
					$data_arr[$row['job_no']][$gmt_item_id][$color_id][$size_id]['size_qty']+= $row['size_qty'];
				}

				}
			
			}					

			  // echo "<pre>";	print_r($data_arr);die();
			  
			  $order_qty_total=0;
			  $lot_ratio_qty_total=0;
			  $lot_ratio_weight_total=0;
			  $bundle_qnty_total=0;
				 $knitting_issue_total=0;
				 $knitting_receive_total=0;
				 $knitting_receive_weight_total=0;
				 $balance_total=0;
		ob_start();
	

	   ?>
		<fieldset style="width:1550px;">
			 <table  cellspacing="0" style="justify-content: center;text-align: center;width: 1580px;" >
				  <tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
						 <td colspan="16" align="center" style="border:none;font-size:14px; font-weight:bold" > Knitting Production [Accessories] Summary Report</td>
				  </tr>
				  <tr style="border:none;justify-content: center;text-align: center;">
						 <td colspan="16" align="center" style="border:none; font-size:16px; font-weight:bold">
						  Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>                                
						 </td>
				   </tr>
				   <tr style="border:none;justify-content: center;text-align: center;">
						 <td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
						  <? echo "Production date ".change_date_format(str_replace("'","",$txt_date_from)) ;?>
						 </td>
				   </tr>
			</table>
		   <br />	
		   <br>
		   <div style="width:1610px; margin-left:18px" >
				<table cellspacing="0" border="1" class="rpt_table" width="1580" rules="all">
					<thead>
						<tr >
							<th width="40" >SL</th>
							<th width="130" >Buyer</th>
							<th width="110" >Style</th>
							<th width="70"> Job Year</th>
							<th width="120" >Job</th>
							<th width="130" >GMT Item</th>
							<th width="80" >C. Ship Date</th>
							<th width="160" >Gmts. Color</th>                        
							<th width="80" >Size</th>
							<th width="80" >Order  Qty (Pcs)</th>
							<th width="80">Lot Ratio Qty. (Pcs)</th>
							<th width="80">Bundle Qty.</th>
							<th width="80">Lot Ratio <br>Weight (Lbs)</th>
							<th width="80" >Knitting <br>Issue<br> ( Pcs)</th>
							<th width="80" >Knitting <br>Issue<br> Balance</th>
							<th width="80" >Knitting <br>Receive<br> (Pcs)</th>
							<th width="80">Knitting  <br>Receive <br>Balance</th>
						
						</tr>
					</thead>
				</table>
				<table cellspacing="0" border="1" class="rpt_table"  width="1580" rules="all"   style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body">
					
					<tbody >
						
						<?
			
						$i=1;
						$knit=0;
						
						foreach($data_arr as $job_no_mst => $delivery_data)	
						{
							foreach ($delivery_data as $gmt_item_id => $item_data) 
							{
								foreach ($item_data as $color_id => $color_data) 
								{

									foreach ($color_data as $size_id => $row) 
									{
										
										$knitting_issue = $kniting_issue_rcv[$job_no_mst][$gmt_item_id][$color_id][$size_id]['knitting_issue'];

										$knitting_receive_weight=$kniting_issue_rcv[$job_no_mst][$gmt_item_id][$color_id][$size_id]['knitting_receive_weight'];
										$knitting_receive=$kniting_issue_rcv[$job_no_mst][$gmt_item_id][$color_id][$size_id]['knitting_receive'];
										// $order_qty=$row['order_qty'];
										$order_qty=$order_qty_arr[$job_no_mst][$gmt_item_id][$color_id][$size_id];

										$working_company=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['working_company'] , " , "))) , 'strlen'));
										$buyer_name=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['buyer_name'] , " , "))), 'strlen'));
										$style=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['style'] , " , "))), 'strlen'));
										$year=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['year'] , " , "))), 'strlen'));
										$job_no=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['job_no'] , " , "))), 'strlen'));
										$country_ship_date=implode(",", array_filter(array_unique(explode(" , ", chop($row['country_ship_date'] , " , "))), 'strlen'));											

										$cutting_no=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['cutting_no'] , " , "))), 'strlen'));

										/*$roll_data=$row['roll_data'];

										$yarn_ratio_form_msert=explode("**",$roll_data);
										$total_lot_qty=0;
										foreach($yarn_ratio_form_msert as $single_yarn_data)
										{
											
											$single_yarn_data_arr= array_unique(explode("=",$single_yarn_data));
											//echo $single_yarn_data;
											$total_lot_qty+=$single_yarn_data_arr[5];
										}

										$plan_cut_qty=$plan_cut_arr[$job_no_mst][$gmt_item_id][$color_id][$size_id];
										$total_plan_cut_qty=$plan_cut_mst[$job_no_mst][$gmt_item_id][$color_id];

										$plan_qty=($plan_cut_qty/$total_plan_cut_qty)*$total_lot_qty;*/

										$size_set_weight = $size_set_weight_array[$job_no_mst][$color_id][$size_id];
										$lot_ratio_qnty = $pop_up_data[$job_no_mst][$gmt_item_id][$color_id][$size_id];
										$lot_ratio_weight = ($size_set_weight*$lot_ratio_qnty*0.00220462);

										$knitting_issue_bal=$lot_ratio_qnty-$knitting_issue;
										$balance=$knitting_issue-$knitting_receive;
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
										// $country_ship_date=$row['country_ship_date'];
										
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
											<td width="40" align="left"><? echo $i; ?></td>
											<td width="130" align="left"><p><? echo $buyer_name; ?></p></td>
											<td width="110" align="left"><p><? echo $style;?></p></td>
											<td width="70" align="left"><p><? echo $year;?></p></td>
											<td width="120" align="left"><p><? echo  $job_no; ?>  </p></td>
											<td  width="130" align="left"><p><? echo  $garments_item[$gmt_item_id]; ?> </p></td>
											<td width="80" align="left"><p>
												<? 
												$cdate_ex = explode(",", $country_ship_date);
												$country_ship_date = "";
												foreach ($cdate_ex as $key => $value) 
												{
													$country_ship_date .= ($country_ship_date=="") ? change_date_format($value) : ",".change_date_format($value) ;
												}
												echo $country_ship_date;
												?>
											
												</p></td>
											<td width="160" align="left"> <p><?php echo $color_library[$color_id]; ?> </p></td>
											<td width="80" align="left"> <p><?php echo $size_library[$size_id]; ?> </p></td>
											<td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($order_qty,0) ?></td>
											<td width="80" style="justify-content: right;text-align: right;"><a href='##' onClick="generate_report2(<? echo "'".$job_no_mst . "'," . $gmt_item_id.",".$color_id.",".$size_id; ?>)"><?php echo $pop_up_data[$job_no_mst][$gmt_item_id][$color_id][$size_id];?> </a> </td>
											<td width="80"  style="justify-content: right;text-align: right;"> <p><?php echo number_format($row['total_bundle']); ?></p></td>
											<td width="80" style="justify-content: right;text-align: right;"><p><?php echo number_format($lot_ratio_weight,2); ?></p> </td>
											<td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_issue,2) ?></td>
											<td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_issue_bal,2) ?></td>
											<td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_receive,2) ?></td>
											<td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($balance,0) ?></td>
											
										</tr>
										<?	
										
											$order_qty_total+=$order_qty;
											$lot_ratio_qty_total+=$pop_up_data[$job_no_mst][$gmt_item_id][$color_id][$size_id];
											$bundle_qnty_total+=$row['total_bundle'];
											$lot_ratio_weight_total+=$plan_qty;
											$knitting_issue_total+=$knitting_issue;
											$knitting_issue_bal_total=$knitting_issue_bal;
											$knitting_receive_total+=$knitting_receive;
												
										$i++;	
									}
								}
							}	
						}
					
						?>
					</tbody>			
				</table> 
				<table cellspacing="0" border="1" class="rpt_table" width="1580" rules="all">
					
					<tfoot>
						<tr>
								
							<th width="920" colspan="9" style="justify-content: right;text-align: right;">Total</th>
							<th width="80" style="justify-content: right;text-align: right;" id="order_qty_total"> <?php echo number_format($order_qty_total,0) ?></th>
							<th width="80" style="justify-content: right;text-align: right;" id="lot_ratio_qty_total"> <?php echo number_format($lot_ratio_qty_total,2) ?></th>
							<th width="80" style="justify-content: right;text-align: right;" id="bundle_qnty_total"> <?php echo number_format($bundle_qnty_total,2) ?></th>
							<th width="80" style="justify-content: right;text-align: right;" id="lot_ratio_weight_total"> <?php echo number_format($lot_ratio_weight_total,2) ?></th>
							<th width="80" style="justify-content: right;text-align: right;" id="knitting_issue"> <?php echo number_format($knitting_issue_total,2) ?></th>
							<th width="80" style="justify-content: right;text-align: right;" id="knitting_issue_bal"> <?php echo number_format($knitting_issue_bal_total,2) ?></th>
							<th width="80" style="justify-content: right;text-align: right;" id="knitting_receive"><?php echo number_format($knitting_receive_total,2) ?></th>
							<th width="80" style="justify-content: right;text-align: right;" id="balance"><?php echo number_format($balance_total,0) ?></th>
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
	  $is_created = fwrite($create_new_doc,ob_get_contents());
	  //$filename=$user_id."_".$name.".xls";
	  echo "$total_data####$filename####$type";
	  exit(); 
 	}
	
}

if ($action == "requisition_print_two") 
{
	extract($_REQUEST);
	//echo $data;die;
	$data = explode('**', $data);
	$job_no=str_replace("'", "", $data[0]);
	$item_id=$data[1];
	$color_id=$data[2];
	$size_id=$data[3];
	echo load_html_head_contents("Program Qnty Info", "../../../../", 1, 1, '', '', '');

	
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );

	$po_sql="SELECT  a.id,a.job_no_mst,b.job_no_prefix_num,
              b.style_ref_no as style,d.color_number_id,d.item_number_id,d.size_number_id
              from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
              where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and  a.job_no_mst=d.job_no_mst and  a.is_deleted=0 and a.status_active=1 and 
              b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and
              
              b.status_active=1

              and a.job_no_mst='$job_no'
              and d.item_number_id=$item_id
              and d.color_number_id=$color_id
              and d.size_number_id=$size_id
                 order by a.job_no_mst,a.po_number,d.country_ship_date";
			 //echo $po_sql;
		      $pro_date_sql=sql_select ($po_sql);

		     

			  


			 
			  $job_number_data=array();
			  $po_number_id=array();
			  
			  foreach($pro_date_sql as $row)
			  {
				  

				 // $job_number_data[$row[csf('job_no_mst')]]['id']=$row[csf('id')];
				  $job_number_data[$row[csf('job_no_mst')]]['job_no']=$row[csf('job_no_mst')];
				  $job_number_data[$row[csf('job_no_mst')]]['job_prefix']=$row[csf('job_no_prefix_num')];
				  $job_number_data[$row[csf('job_no_mst')]]['style']=$row[csf('style')];
				  
				  
				  array_push( $po_number_id, $row[csf('id')]);
				 
			  }
			  $po_number_id=array_unique($po_number_id);
			
			$order_id_cond="";
			if(count($po_number_id))
			{
				$order_id_cond=where_con_using_array($po_number_id,0,"b.order_id");
			}

			  $prod_sql="
						SELECT
						       
						        b.size_id,
						       	b.size_qty,
						        d.cutting_no,
						        d.job_no,
						        b.order_id
						  from ppl_cut_lay_dtls a,
						       ppl_cut_lay_bundle b,
						       
						       ppl_cut_lay_mst d
						 where     
						      
						        d.id=a.mst_id
						       and a.id = b.dtls_id
						       and d.id=b.mst_id
						       and a.status_active = 1
						       and a.is_deleted = 0
						       and b.status_active = 1
						       and b.is_deleted = 0
						       and d.status_active=1
				               and a.gmt_item_id=$item_id
				               and a.color_id=$color_id
				               and b.size_id=$size_id
						       $order_id_cond
					  order by 
					  			d.cutting_no,
					  			b.size_id
					  ";
				// echo $prod_sql;

				$result=sql_select($prod_sql);


	$total=0;
	?>

	<table cellspacing="0" border="1" class="rpt_table"  rules="all">
		<thead>
			<tr>
				<th width="40">Sl</th>
				<th width="120">Job No.</th>
				<th width="120">Style No.</th>
				<th width="120">Lot Ratio </th>
				<th width="70">Size</th>
				<th width="80">Qty [Pcs]</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				$i=1;
				foreach ($result as $row) 
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	

					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
							<td  align="left"><? echo $i; ?></td>
							<td  align="left"><? echo $job_number_data[$row[csf('job_no')]]['job_prefix']; ?></td>
							<td  align="left"><? echo $job_number_data[$row[csf('job_no')]]['style']; ?></td>
							<td  align="left"><? echo $row[csf('cutting_no')]; ?></td>
							<td  align="left"><? echo $size_library[$row[csf('size_id')]]; ?></td>
							<td  align="right"><? echo number_format($row[csf('size_qty')],2); ?></td>

						</tr>

					<?

					$total+=$row[csf('size_qty')];
					$i++;
					
				}


			 ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="5">Total</th>
				<th align="right"><? echo number_format($total,2); ?></th>
			</tr>
		</tfoot>
	</table>

	<?
	

    
    exit();
}




