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
	
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
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
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'daily_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
		$year_field = "YEAR(a.insert_date) as year";
    //$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(a.insert_date,'YYYY') as year";
    //$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
	}
	else
	{$year_field = "";
   // $year_cond = "";
    } //defined Later
    
  

	$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name
		    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name order by job_no";

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

    //print_r($process);die;
    extract(check_magic_quote_gpc( $process ));
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	$supplier_arr=return_library_array( "select id,supplier_name from lib_supplier ",'id','supplier_name');

	$job_cond_id=""; 
	$style_cond="";
	$order_cond="";
	$type=str_replace("'","",$type);
   	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=".str_replace("'","",$cbo_company_name)."";

	if(str_replace("'","",$cbo_buyer_name)==0)  $buyer_name=""; else $buyer_name="and b.buyer_name=".str_replace("'","",$cbo_buyer_name)."";

	if(str_replace("'","",$hidden_job_id)!="")  $job_cond_id="and b.id in(".str_replace("'","",$hidden_job_id).")";

	else  if (str_replace("'","",$txt_job_no)=="") $job_cond_id=""; else $job_cond_id="and b.job_no='".str_replace("'","",$txt_job_no)."'";

	if(str_replace("'","",$hidden_style_id)!="")  $style_cond="and b.id in(".str_replace("'","",$hidden_style_id).")";

	else  if (str_replace("'","",$txt_style_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no like '%".str_replace("'","",$txt_style_no)."%' ";


	 

	$shipping_status_cond="";
	if(str_replace("'","",$cbo_status)==3) $shipping_status_cond=" and d.shiping_status=3";
	else if(str_replace("'","",$cbo_status)==2) $shipping_status_cond=" and d.shiping_status!=3";
	else $shipping_status_cond="";
	
	
  	if($type==1)
  	{


		 $po_number_data=array();
		 $production_data_arr=array();
		 $po_number_id=array();

	 	 
			  if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $country_ship_date="";
			  else $production_date=" and a.production_date between $txt_date_from and $txt_date_to";

			  $po_sql="SELECT  a.id,a.job_no_mst,a.po_number,d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,b.buyer_name,
			  b.style_ref_no as style,d.country_ship_date,d.color_number_id,d.item_number_id,b.company_name,a.excess_cut,b.gauge
			  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
			  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and  a.job_no_mst=d.job_no_mst and  a.is_deleted=0 and a.status_active=1 and 
			  b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and
			  
			  b.status_active=1 $company_name $buyer_name $style_cond  $job_cond_id  $shipping_status_cond order by a.job_no_mst,a.po_number,d.country_ship_date";
			 // echo $po_sql;
		      $pro_date_sql=sql_select ($po_sql);

			  


			 
			  $po_id_marge=array();
			  $po_country_arr=array();
			  $po_plan_cutqty_arr=array();
			  $job_no_arr=array();
			  $job_no_list=array();
			  foreach($pro_date_sql as $row)
			  {
				  $job_no_arr[$row[csf('id')]]=$row[csf('job_no_mst')];

				 // $job_number_data[$row[csf('job_no_mst')]]['id']=$row[csf('id')];
				  $job_number_data[$row[csf('job_no_mst')]]['job_no']=$row[csf('job_no_mst')];
				  $job_number_data[$row[csf('job_no_mst')]]['po_number']=$row[csf('po_number')];
				  $job_number_data[$row[csf('job_no_mst')]]['po_quantity']+=$row[csf('order_qty')];
				  $job_number_data[$row[csf('job_no_mst')]]['plan_qty']+=$row[csf('plan_qty')];
				  $job_number_data[$row[csf('job_no_mst')]]['buyer_name']=$row[csf('buyer_name')];
				  $job_number_data[$row[csf('job_no_mst')]]['company_name']=$row[csf('company_name')];
				  $job_number_data[$row[csf('job_no_mst')]]['style']=$row[csf('style')];
				  $job_number_data[$row[csf('job_no_mst')]]['item_number_id'][]=$row[csf('item_number_id')];
				  $job_number_data[$row[csf('job_no_mst')]]['color_id']=$row[csf('color_number_id')];
				  $job_number_data[$row[csf('job_no_mst')]]['excess_cut']=$row[csf('excess_cut')];
				  $job_number_data[$row[csf('job_no_mst')]]['gauge']=$row[csf('gauge')];
				  $job_plan_cutqty_arr[$row[csf('id')]]['plan_qty']+=$row[csf('plan_qty')];
				  array_push( $po_number_id, $row[csf('id')]);
				  array_push( $job_no_list, $row[csf('job_no_mst')]);
			  }

			  // echo "<pre>";
			  // print_r( $po_number_data);
			  // echo "</pre>";

			$po_number_id=array_unique($po_number_id);
			$job_no_list=array_unique($job_no_list);
			   
			// $production_sql="select a.po_break_down_id,c.country_ship_date,a.production_date,a.serving_company,a.production_source,
			  	
			//   	sum(CASE WHEN b.production_type =1 $production_date THEN b.production_qnty ELSE 0 END) AS knitting_complete_qnty, 
			// 	sum(CASE WHEN b.production_type =3  $production_date THEN b.production_qnty ELSE 0 END) AS wash_complete_qnty,
				
			// 	sum(CASE WHEN b.production_type =222 $production_date THEN b.production_qnty ELSE 0 END) AS inline_inspection_qnty,
			// 	sum(CASE WHEN b.production_type =5 $production_date THEN b.production_qnty ELSE 0 END) AS sewing_complete_qnty,
			// 	sum(CASE WHEN b.production_type =67 $production_date THEN b.production_qnty ELSE 0 END) AS iron_complete_qnty,
			// 	sum(CASE WHEN b.production_type =67 $production_date THEN b.re_production_qty ELSE 0 END) AS re_iron_qnty,
			// 	sum(CASE WHEN b.production_type =4 $production_date THEN b.production_qnty ELSE 0 END) AS linking_complete_qnty, 
			// 	sum(CASE WHEN b.production_type =11 $production_date THEN b.production_qnty ELSE 0 END) AS attachment_complete_qnty,
			// 	sum(CASE WHEN b.production_type =8  $production_date THEN b.production_qnty ELSE 0 END) AS packing_complete_qnty
			  
			//   from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c where a.id=b.mst_id 
			//   and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0
			//   and b.color_size_break_down_id=c.id  and a.po_break_down_id=c.po_break_down_id and a.company_id=$cbo_company_name  $production_date  group by a.po_break_down_id,c.country_ship_date,a.production_date,a.serving_company,a.production_source order by a.production_date";
			//echo $production_sql;
			$prod_sql="select a.po_break_down_id,a.production_date,a.serving_company,a.production_source, 
					sum(CASE WHEN a.production_type =1  THEN a.production_quantity ELSE 0 END) AS knitting_complete_qnty, 
					sum(CASE WHEN a.production_type =3  THEN a.production_quantity ELSE 0 END) AS wash_complete_qnty, 
					0 AS inline_inspection_qnty, 
					sum(CASE WHEN a.production_type =5  THEN a.production_quantity ELSE 0 END) AS sewing_complete_qnty, 
					sum(CASE WHEN a.production_type =67  THEN a.production_quantity ELSE 0 END) AS iron_complete_qnty, 
					sum(CASE WHEN a.production_type =67  THEN a.re_production_qty ELSE 0 END) AS re_iron_qnty, 
					sum(CASE WHEN a.production_type =4  THEN a.production_quantity ELSE 0 END) AS linking_complete_qnty, 
					sum(CASE WHEN a.production_type =11  THEN a.production_quantity ELSE 0 END) AS attachment_complete_qnty, 
					sum(CASE WHEN a.production_type =111  THEN a.production_quantity ELSE 0 END) AS trimming_complete_qnty, 
					sum(CASE WHEN a.production_type =112  THEN a.production_quantity ELSE 0 END) AS mending_complete_qnty, 
					sum(CASE WHEN a.production_type =114  THEN a.production_quantity ELSE 0 END) AS pqc_complete_qnty, 
					sum(CASE WHEN a.production_type =8  THEN a.production_quantity ELSE 0 END) AS packing_complete_qnty 
					from pro_garments_production_mst a
					where  a.status_active=1 and a.is_deleted=0
					 ".where_con_using_array($po_number_id,0,"a.po_break_down_id")."  
					$production_date 
					group by a.po_break_down_id,a.production_date,a.serving_company,a.production_source 
					

					union all 

					select a.po_break_down_id,a.inspection_date as production_date ,a.working_company as serving_company ,a.source as production_source, 0 as knitting_complete_qnty, 0 as wash_complete_qnty, sum(a.inspection_qnty) as inline_inspection_qnty,0 as sewing_complete_qnty,0 as iron_complete_qnty ,0 as re_iron_qnty,0 as linking_complete_qnty,0 as attachment_complete_qnty,0 as trimming_complete_qnty,0 as mending_complete_qnty,0 as pqc_complete_qnty, 0 as packing_complete_qnty  
					from  pro_buyer_inspection a
					 where a.status_active=1  
					 ".where_con_using_array($po_number_id,0,"a.po_break_down_id")." 
					   and a.inspection_date   between $txt_date_from and $txt_date_to 
					 group by a.po_break_down_id,a.inspection_date  ,a.working_company  ,a.source order by production_date";

			//echo $prod_sql;



			  $production_mst_sql=sql_select($prod_sql);


			
		       foreach($production_mst_sql as $val)
			   {
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['knitting_complete_qnty']+=$val[csf('knitting_complete_qnty')];
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['wash_complete_qnty']+=$val[csf('wash_complete_qnty')];
					
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['inline_inspection_qnty']+=$val[csf('inline_inspection_qnty')];
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['sewing_complete_qnty']+=$val[csf('sewing_complete_qnty')];
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['iron_complete_qnty']+=$val[csf('iron_complete_qnty')];
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['re_iron_qnty']+=$val[csf('re_iron_qnty')];
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['linking_complete_qnty']+=$val[csf('linking_complete_qnty')];
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['attachment_complete_qnty']+=$val[csf('attachment_complete_qnty')];
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['packing_complete_qnty']+=$val[csf('packing_complete_qnty')];
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['trimming_complete_qnty']+=$val[csf('trimming_complete_qnty')];
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['mending_complete_qnty']+=$val[csf('mending_complete_qnty')];
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['pqc_complete_qnty']+=$val[csf('pqc_complete_qnty')];
					
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['serving_company']=$val[csf('serving_company')];
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['production_date']=$val[csf('production_date')];
					$production_data_arr[$job_no_arr[$val[csf('po_break_down_id')]]][$val[csf('production_date')]]['production_source']=$val[csf('production_source')];
					
					$po_number_gmt[]=$val[csf('po_break_down_id')];		
			   }

			   // echo "<pre>";
			   // print_r($production_data_arr);
			   // echo "</pre>";
			  
				//print_r($production_data_arr);die;

			   		$buyer_wise_data=array();

			   		$knitting_complete_qnty_total=0;
			   		$wash_complete_qnty_total=0;
			   		$inline_inspection_qnty_total=0;
			   		$sewing_complete_qnty_total=0;
			   		$iron_complete_qnty_total=0;
			   		$re_iron_qnty_total=0;
			   		$linking_complete_qnty_total=0;
			   		$attachment_complete_qnty_total=0;
			   		$trimming_complete_qnty_total=0;
			   		$mending_complete_qnty_total=0;
			   		$pqc_complete_qnty_total=0;
			   		$packing_complete_qnty_total=0;

					foreach($production_data_arr as $job_no=>$job_arr)	
					{
						
						foreach ($job_arr as $production_date => $pro_data)
						{
							
						
				 				
							

								$serving_company=$pro_data['serving_company'];
								$knitting_complete_qnty=$pro_data['knitting_complete_qnty'];
								$wash_complete_qnty=$pro_data['wash_complete_qnty'];
								
								$inline_inspection_qnty=$pro_data['inline_inspection_qnty'];
								$sewing_complete_qnty=$pro_data['sewing_complete_qnty'];
								$iron_complete_qnty=$pro_data['iron_complete_qnty'];
								$re_iron_qnty=$pro_data['re_iron_qnty'];
								$linking_complete_qnty=$pro_data['linking_complete_qnty'];
								$attachment_complete_qnty=$pro_data['attachment_complete_qnty'];
								$production_date=$pro_data['production_date'];
								$production_source=$pro_data['production_source'];
								$packing_complete_qnty=$pro_data['packing_complete_qnty'];
								$trimming_complete_qnty=$pro_data['trimming_complete_qnty'];
								$mending_complete_qnty=$pro_data['mending_complete_qnty'];
								$pqc_complete_qnty=$pro_data['pqc_complete_qnty'];
							
								
						   	
								
								
							  	
								  $job_no=$job_number_data[$job_no]['job_no'];
								  $buyer_name=$job_number_data[$job_no]['buyer_name'];
								  $company_id=$job_number_data[$job_no]['company_name'];
								  $style=$job_number_data[$job_no]['style'];

								  $order_qty=$job_number_data[$job_no]['po_quantity'];
								  $plan_qty=$job_number_data[$job_no]['plan_qty'];
								  $excess_cut=$job_number_data[$job_no]['excess_cut'];
								  $gauge=$job_number_data[$job_no]['gauge'];


								  $buyer_wise_data[$buyer_name]['knitting_complete_qnty']+=$knitting_complete_qnty;
								  $buyer_wise_data[$buyer_name]['wash_complete_qnty']+=$wash_complete_qnty;
								  $buyer_wise_data[$buyer_name]['inline_inspection_qnty']+=$inline_inspection_qnty;
								  $buyer_wise_data[$buyer_name]['sewing_complete_qnty']+=$sewing_complete_qnty;
								  $buyer_wise_data[$buyer_name]['iron_complete_qnty']+=$iron_complete_qnty;
								  $buyer_wise_data[$buyer_name]['re_iron_qnty']+=$re_iron_qnty;
								  $buyer_wise_data[$buyer_name]['linking_complete_qnty']+=$linking_complete_qnty;
								  $buyer_wise_data[$buyer_name]['attachment_complete_qnty']+=$attachment_complete_qnty;
								  $buyer_wise_data[$buyer_name]['packing_complete_qnty']+=$packing_complete_qnty;
								  $buyer_wise_data[$buyer_name]['trimming_complete_qnty']+=$trimming_complete_qnty;
								  $buyer_wise_data[$buyer_name]['mending_complete_qnty']+=$mending_complete_qnty;
								  $buyer_wise_data[$buyer_name]['pqc_complete_qnty']+=$pqc_complete_qnty;
								  
								
				                
							 
						}		
						
					}
			
		  ob_start();
	      //and po_number_id in (".str_replace("'","",$po_number_id).")


		 ?>
  		<fieldset style="width:1730px;">
        	   <table width="1000"  cellspacing="0"  align="center" >
                    <tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
                           <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" > Daily Production Progress Report</td>
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


             <table cellspacing="0" border="1" class="rpt_table"   rules="all"     align="center">
             	<thead>
             		<tr>
             			<th>Sl.</th>
             			<th>Buyer Name</th>
             			<th width="80">Knitting Complete</th>
             			<th width="80">Inline Inspection</th>
             			<th width="80">Linking Complete</th>
             			<th width="80">Trimming Complete</th>
             			<th width="80">Mending Complete</th>
             			<th width="80">Wash Complete</th>
             			<th width="80">Attachment Complete</th>
             			<th width="80">Sewing Complete</th>
             			<th width="80">PQC Complete</th>
             			<th width="80">Iron Complete</th>
             			<th width="80">Re Iron</th>
             			<th width="80">Packing Complete</th>
             		</tr>
             	</thead>
             	<tbody>

             		
             		<?php 
             		$i=0;

             		$knitting_complete_qnty_buyer=0;
			   		$wash_complete_qnty_buyer=0;
			   		$inline_inspection_qnty_buyer=0;
			   		$sewing_complete_qnty_buyer=0;
			   		$iron_complete_qnty_buyer=0;
			   		$re_iron_qnty_buyer=0;
			   		$linking_complete_qnty_buyer=0;
			   		$attachment_complete_qnty_buyer=0;
			   		$packing_complete_qnty_buyer=0;
			   		$trimming_complete_qnty_buyer=0;
			   		$mending_complete_qnty_buyer=0;
			   		$pqc_complete_qnty_buyer=0;
             		foreach ($buyer_wise_data as $buyer=>$buyer_data)
             		{
             			$i++;
             			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
             			?>
	             		<tr  bgcolor="<? echo $bgcolor; ?>">
	             			<td><?php echo $i ; ?></td>
	             			<td><?php echo $buyer_arr[$buyer]; ?></td>
	             			<td><?php echo $buyer_data['knitting_complete_qnty']; ?></td>
	             			<td><?php echo $buyer_data['inline_inspection_qnty']; ?></td>
	             			<td><?php echo $buyer_data['linking_complete_qnty']; ?></td>
	             			<td><?php echo $buyer_data['trimming_complete_qnty']; ?></td>
	             			<td><?php echo $buyer_data['mending_complete_qnty']; ?></td>
	             			<td><?php echo $buyer_data['wash_complete_qnty']; ?></td>
	             			<td><?php echo $buyer_data['attachment_complete_qnty']; ?></td>
	             			<td><?php echo $buyer_data['sewing_complete_qnty']; ?></td>
	             			<td><?php echo $buyer_data['pqc_complete_qnty']; ?></td>
	             			<td><?php echo $buyer_data['iron_complete_qnty']; ?></td>
	             			<td><?php echo $buyer_data['re_iron_qnty']; ?></td>
	             			<td><?php echo $buyer_data['packing_complete_qnty']; ?></td>
	             			
	             		</tr>
	             		<?php 

	             		$knitting_complete_qnty_buyer+=$buyer_data['knitting_complete_qnty'];
				   		$wash_complete_qnty_buyer+=$buyer_data['wash_complete_qnty'];
				   		$inline_inspection_qnty_buyer+=$buyer_data['inline_inspection_qnty'];
				   		$sewing_complete_qnty_buyer+=$buyer_data['sewing_complete_qnty'];
				   		$iron_complete_qnty_buyer+=$buyer_data['iron_complete_qnty'];
				   		$re_iron_qnty_buyer+=$buyer_data['re_iron_qnty'];
				   		$linking_complete_qnty_buyer+=$buyer_data['linking_complete_qnty'];
				   		$attachment_complete_qnty_buyer+=$buyer_data['attachment_complete_qnty'];
				   		$packing_complete_qnty_buyer+=$buyer_data['packing_complete_qnty'];
				   		$trimming_complete_qnty_buyer+=$buyer_data['trimming_complete_qnty'];
				   		$mending_complete_qnty_buyer+=$buyer_data['mending_complete_qnty'];
				   		$pqc_complete_qnty_buyer+=$buyer_data['pqc_complete_qnty'];

	             	}

	             		?>
             	</tbody>
             	<tbody>
	              	<tr>
	              		
	              		<td colspan="2" style="justify-content: right;text-align: right;">Total</td>
	              		<td><?php echo $knitting_complete_qnty_buyer; ?></td>
	              		<td><?php echo $inline_inspection_qnty_buyer; ?></td>
	              		<td><?php echo $linking_complete_qnty_buyer; ?></td>
	              		<td><?php echo $trimming_complete_qnty_buyer; ?></td>
	              		<td><?php echo $mending_complete_qnty_buyer; ?></td>
	              		<td><?php echo $wash_complete_qnty_buyer; ?></td>
	              		<td><?php echo $attachment_complete_qnty_buyer; ?></td>
	              		<td><?php echo $sewing_complete_qnty_buyer; ?></td>
	              		<td><?php echo $pqc_complete_qnty_buyer; ?></td>
	              		<td><?php echo $iron_complete_qnty_buyer; ?></td>
	              		<td><?php echo $re_iron_qnty_buyer; ?></td>
	              		<td><?php echo $packing_complete_qnty_buyer; ?></td>
	              	</tr>
	              </tbody>    
             </table>

             <br>

            
           
            
             <div style="width:1720px; margin-left:18px" >
                    <table cellspacing="0" border="1" class="rpt_table"  width="1700" rules="all" id="table_body"  style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body">
                    	<thead >
                    		<tr>
                    			<td colspan="23" style="justify-content: left;text-align: left;">In House Production</td>
                    		</tr>
		                	<tr >
		                       <th width="40" >SL</th>
		                       <th width="120" >Company</th>
		                       <th width="120" >Working Company</th>
		                       <th width="100" >Buyer Name</th>
		                       <th width="100" >Job No</th>
		                       <th width="100" >Style Name</th>
		                       <th width="80" >Guage</th>
		                       <th width="80" >Production Date</th>
		                       <th width="80" >Order Qty</th>
		                       <th width="80" >Plan Knit Qty</th>                        
		                       <th width="80" >Ex. Percentage</th>
		                       <th width="80">Knitting Complete</th>
		                       <th width="80" >Inline Inspection</th>
		                       <th width="80" >Linking Complete</th>
		                       <th width="80">Trimming Complete</th>
             				   <th width="80">Mending Complete</th>
		                       <th width="80" >Wash Complete</th>
		                       <th width="80" >Attachment Complete</th>
		                       <th width="80" >Sewing Complete</th>
		                       <th width="80">PQC Complete</th>
		                       <th width="80" >Iron Complete</th>
		                       <th width="80" >Re Iron</th>
		                       <th >Packing Complete</th>
		                      
		                    </tr>
		                    
		                </thead>
		                <tbody >
		                	
	                  			<?
	               
	                     		 $i=1;
				                 $knit=0;
								  
			                      
							  	foreach($production_data_arr as $job_no=>$job_arr)	
								{
									
									foreach ($job_arr as $production_date => $pro_data)
									{
							 				
										

										$production_source=$pro_data['production_source'];
										if($production_source==1)
										{



											$serving_company=$pro_data['serving_company'];
											$knitting_complete_qnty=$pro_data['knitting_complete_qnty'];
											$wash_complete_qnty=$pro_data['wash_complete_qnty'];
											
											$inline_inspection_qnty=$pro_data['inline_inspection_qnty'];
											$sewing_complete_qnty=$pro_data['sewing_complete_qnty'];
											$iron_complete_qnty=$pro_data['iron_complete_qnty'];
											$re_iron_qnty=$pro_data['re_iron_qnty'];
											$linking_complete_qnty=$pro_data['linking_complete_qnty'];
											$attachment_complete_qnty=$pro_data['attachment_complete_qnty'];
											$packing_complete_qnty=$pro_data['packing_complete_qnty'];
											$trimming_complete_qnty=$pro_data['trimming_complete_qnty'];
											$mending_complete_qnty=$pro_data['mending_complete_qnty'];
											$pqc_complete_qnty=$pro_data['pqc_complete_qnty'];
										
										
									   	
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
											
										  	
											  $job_no=$job_number_data[$job_no]['job_no'];
											  $buyer_name=$job_number_data[$job_no]['buyer_name'];
											  $company_id=$job_number_data[$job_no]['company_name'];
											  $style=$job_number_data[$job_no]['style'];

											  $order_qty=$job_number_data[$job_no]['po_quantity'];
											  $plan_qty=$job_number_data[$job_no]['plan_qty'];
											  $excess_cut=$job_number_data[$job_no]['excess_cut'];
											  $gauge=$job_number_data[$job_no]['gauge'];
											  
							                ?>
							                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
							                    <td ><? echo $i; ?></td>
							                    <td ><?php echo $company_arr[$company_id]; ?></td>
							                    <td ><?php  if($production_source==1){echo $company_arr[$serving_company];}else {echo $supplier_arr[$serving_company];  } ?></td>
							                   
							                    <td ><p><? echo $buyer_arr[$buyer_name]; ?></p></td>
							                    <td  align="center"><p><? echo  $job_no; ?></p></td>
							                    <td  align="center"><p><? echo $style;?></p></td>
							                    <td  align="center"><p><? echo $gauge_arr[$gauge];?></p></td>
							                    <td  align="center"><p><? echo change_date_format($production_date);?></p></td>


							                    
				                      
				                      
				                      
				                      
				                       
				                      
				                      
							                    
							                    <td > <?php echo number_format($order_qty,0) ?></td>
							                    <td > <?php echo number_format($plan_qty,0) ?></td>
							                    <td > <?php echo $excess_cut; ?></td>
							                    <td > <?php echo number_format($knitting_complete_qnty,0);$knit+=$knitting_complete_qnty; ?></td>
							                    <td > <?php echo number_format($inline_inspection_qnty,0) ?></td>
							                    <td > <?php echo number_format($linking_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($trimming_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($mending_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($wash_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($attachment_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($sewing_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($pqc_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($iron_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($re_iron_qnty,0) ?></td>
							                    <td > <?php echo number_format($packing_complete_qnty,0) ?></td>
							                   
							        	  	</tr>
											<?	
										     

										      	$knitting_complete_qnty_total=+$knitting_complete_qnty;
										   		$wash_complete_qnty_total+=$wash_complete_qnty;
										   		$inline_inspection_qnty_total+=$inline_inspection_qnty;
										   		$sewing_complete_qnty_total+=$sewing_complete_qnty;
										   		$iron_complete_qnty_total+=$iron_complete_qnty;
										   		$re_iron_qnty_total+=$re_iron_qnty;
										   		$linking_complete_qnty_total+=$linking_complete_qnty;
										   		$attachment_complete_qnty_total+=$attachment_complete_qnty;
										   		$packing_complete_qnty_total+=$packing_complete_qnty;
										   		$trimming_complete_qnty_total+=$trimming_complete_qnty;
										   		$mending_complete_qnty_total+=$mending_complete_qnty;
										   		$pqc_complete_qnty_total+=$pqc_complete_qnty;
											 $i++;	
										}	
										
									}		
									
								}
							
								?>

	                    </tbody>    
	                      <tbody>
		                      	<tr>
		                      		
		                      		<td colspan="11" style="justify-content: right;text-align: right;">Total</td>
		                      		
		                      		<td><?php echo $knit; ?></td>
		                      		<td><?php echo $inline_inspection_qnty_total; ?></td>
		                      		<td><?php echo $linking_complete_qnty_total; ?></td>
		                      		<td><?php echo $trimming_complete_qnty_total; ?></td>
		                      		<td><?php echo $mending_complete_qnty_total; ?></td>
		                      		<td><?php echo $wash_complete_qnty_total; ?></td>
		                      		<td><?php echo $attachment_complete_qnty_total; ?></td>
		                      		<td><?php echo $sewing_complete_qnty_total; ?></td>
		                      		<td><?php echo $pqc_complete_qnty_total; ?></td>
		                      		<td><?php echo $iron_complete_qnty_total; ?></td>
		                      		<td><?php echo $re_iron_qnty_total; ?></td>
		                      		<td><?php echo $packing_complete_qnty_total; ?></td>
		                      	</tr>
	                      </tbody>      
									    
	                </table> 

	                <br>

	              		<?php   

	              			$knitting_complete_qnty_total=0;
					   		$wash_complete_qnty_total=0;
					   		$inline_inspection_qnty_total=0;
					   		$sewing_complete_qnty_total=0;
					   		$iron_complete_qnty_total=0;
					   		$re_iron_qnty_total=0;
					   		$linking_complete_qnty_total=0;
					   		$attachment_complete_qnty_total=0;
					   		$trimming_complete_qnty_total=0;
					   		$mending_complete_qnty_total=0;
					   		$pqc_complete_qnty_total=0;
					   		$packing_complete_qnty_total=0;

					   	 ?>

	                 <table cellspacing="0" border="1" class="rpt_table"  width="1700" rules="all" id="table_body"  style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body">
                    	<thead >
                    		<tr>
                    			<td colspan="23" style="justify-content: left;text-align: left;">Sub-Contract Production</td>
                    		</tr>
		                	<tr >
		                       <th width="40" >SL</th>
		                       <th width="120" >Company</th>
		                       <th width="120" >Working Company</th>
		                       <th width="100" >Buyer Name</th>
		                       <th width="100" >Job No</th>
		                       <th width="100" >Style Name</th>
		                       <th width="80" >Guage</th>
		                       <th width="80" >Production Date</th>
		                       <th width="80" >Order Qty</th>
		                       <th width="80" >Plan Knit Qty</th>                        
		                       <th width="80" >Ex. Percentage</th>
		                       <th width="80">Knitting Complete</th>
		                       <th width="80" >Inline Inspection</th>
		                       <th width="80" >Linking Complete</th>
		                       <th width="80">Trimming Complete</th>
             				   <th width="80">Mending Complete</th>
		                       <th width="80" >Wash Complete</th>
		                       <th width="80" >Attachment Complete</th>
		                       <th width="80" >Sewing Complete</th>
		                       <th width="80">PQC Complete</th>
		                       <th width="80" >Iron Complete</th>
		                       <th width="80" >Re Iron</th>
		                       <th >Packing Complete</th>
		                      
		                    </tr>
		                    
		                </thead>
		                <tbody >
		                	
	                  			<?

	                  			$sl=$i;
	               
	                     		 $i=1;
				                 $knit=0;
								  
			                      
							  	foreach($production_data_arr as $job_no=>$job_arr)	
								{
									
									foreach ($job_arr as $production_date => $pro_data)
									{
							 				
										
										$production_source=$pro_data['production_source'];
										if($production_source!=1)
										{

											$serving_company=$pro_data['serving_company'];
											$knitting_complete_qnty=$pro_data['knitting_complete_qnty'];
											$wash_complete_qnty=$pro_data['wash_complete_qnty'];
											
											$inline_inspection_qnty=$pro_data['inline_inspection_qnty'];
											$sewing_complete_qnty=$pro_data['sewing_complete_qnty'];
											$iron_complete_qnty=$pro_data['iron_complete_qnty'];
											$re_iron_qnty=$pro_data['re_iron_qnty'];
											$linking_complete_qnty=$pro_data['linking_complete_qnty'];
											$attachment_complete_qnty=$pro_data['attachment_complete_qnty'];
											
											$packing_complete_qnty=$pro_data['packing_complete_qnty'];
											$trimming_complete_qnty=$pro_data['trimming_complete_qnty'];
											$mending_complete_qnty=$pro_data['mending_complete_qnty'];
											$pqc_complete_qnty=$pro_data['pqc_complete_qnty'];
										
										
									   	
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
											
										  	
											  $job_no=$job_number_data[$job_no]['job_no'];
											  $buyer_name=$job_number_data[$job_no]['buyer_name'];
											  $company_id=$job_number_data[$job_no]['company_name'];
											  $style=$job_number_data[$job_no]['style'];

											  $order_qty=$job_number_data[$job_no]['po_quantity'];
											  $plan_qty=$job_number_data[$job_no]['plan_qty'];
											  $excess_cut=$job_number_data[$job_no]['excess_cut'];
											  $gauge=$job_number_data[$job_no]['gauge'];
											  
							                ?>
							                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i+$sl; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i+$sl; ?>">
							                    <td ><? echo $i; ?></td>
							                    <td ><?php echo $company_arr[$company_id]; ?></td>
							                    <td ><?php  if($production_source==1){echo $company_arr[$serving_company];}else {echo $supplier_arr[$serving_company];  } ?></td>
							                   
							                    <td ><p><? echo $buyer_arr[$buyer_name]; ?></p></td>
							                    <td  align="center"><p><? echo  $job_no; ?></p></td>
							                    <td  align="center"><p><? echo $style;?></p></td>
							                    <td  align="center"><p><? echo $gauge_arr[$gauge];?></p></td>
							                    <td  align="center"><p><? echo change_date_format($production_date);?></p></td>


							                    
				                      
				                      
				                      
				                      
				                       
				                      
				                      
							                    
							                    <td > <?php echo number_format($order_qty,0) ?></td>
							                    <td > <?php echo number_format($plan_qty,0) ?></td>
							                    <td > <?php echo $excess_cut; ?></td>
							                    <td > <?php echo number_format($knitting_complete_qnty,0);$knit+=$knitting_complete_qnty; ?></td>
							                    <td > <?php echo number_format($inline_inspection_qnty,0) ?></td>
							                    <td > <?php echo number_format($linking_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($trimming_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($mending_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($wash_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($attachment_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($sewing_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($pqc_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($iron_complete_qnty,0) ?></td>
							                    <td > <?php echo number_format($re_iron_qnty,0) ?></td>
							                    <td > <?php echo number_format($packing_complete_qnty,0) ?></td>
							                   
							        	  	</tr>
											<?	
										     

										      	$knitting_complete_qnty_total=+$knitting_complete_qnty;
										   		$wash_complete_qnty_total+=$wash_complete_qnty;
										   		$inline_inspection_qnty_total+=$inline_inspection_qnty;
										   		$sewing_complete_qnty_total+=$sewing_complete_qnty;
										   		$iron_complete_qnty_total+=$iron_complete_qnty;
										   		$re_iron_qnty_total+=$re_iron_qnty;
										   		$linking_complete_qnty_total+=$linking_complete_qnty;
										   		$attachment_complete_qnty_total+=$attachment_complete_qnty;
										   		$packing_complete_qnty_total+=$packing_complete_qnty;
										   		$trimming_complete_qnty_total+=$trimming_complete_qnty;
										   		$mending_complete_qnty_total+=$mending_complete_qnty;
										   		$pqc_complete_qnty_total+=$pqc_complete_qnty;
											 $i++;		
										}
										
									}		
									
								}
							
								?>

	                    </tbody>    
	                      <tbody>
		                      	<tr>
		                      		
		                      		<td colspan="11" style="justify-content: right;text-align: right;">Total</td>
		                      		
		                      		<td><?php echo $knit; ?></td>
		                      		<td><?php echo $inline_inspection_qnty_total; ?></td>
		                      		<td><?php echo $linking_complete_qnty_total; ?></td>
		                      		<td><?php echo $trimming_complete_qnty_total; ?></td>
		                      		<td><?php echo $mending_complete_qnty_total; ?></td>
		                      		<td><?php echo $wash_complete_qnty_total; ?></td>
		                      		<td><?php echo $attachment_complete_qnty_total; ?></td>
		                      		<td><?php echo $sewing_complete_qnty_total; ?></td>
		                      		<td><?php echo $pqc_complete_qnty_total; ?></td>
		                      		<td><?php echo $iron_complete_qnty_total; ?></td>
		                      		<td><?php echo $re_iron_qnty_total; ?></td>
		                      		<td><?php echo $packing_complete_qnty_total; ?></td>
		                      	</tr>
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
		$is_created = fwrite($create_new_doc,ob_get_contents());
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
		exit(); 
	}
	
}




