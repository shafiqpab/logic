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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'date_wise_knitting_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
    
  
  	if($search_by == 3)
  	{
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name
		    from wo_po_break_down a, wo_po_details_master b,ppl_cut_lay_mst c where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name order by job_no";
  	}
  	else
  	{
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name
		    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name order by job_no";

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

	else  if (str_replace("'","",$txt_job_no)=="") $job_cond_id=""; else $job_cond_id="and b.job_no='".str_replace("'","",$txt_job_no)."'";

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

	 	 
			  if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $production_date="";
			  else $production_date=" and e.production_date between $txt_date_from and $txt_date_to";

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

			  $po_sql="SELECT  a.id,a.job_no_mst,a.po_number,d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,b.buyer_name,
			  b.style_ref_no as style,d.country_ship_date,d.color_number_id,d.item_number_id,b.company_name,a.excess_cut,b.gauge $year_field
			  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
			  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and  a.job_no_mst=d.job_no_mst and  a.is_deleted=0 and a.status_active=1 and 
			  b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and
			  
			  b.status_active=1 $company_name $buyer_name $style_cond  $job_cond_id  $shipping_status_cond order by a.job_no_mst,a.po_number,d.country_ship_date";
			 //echo $po_sql;
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
				  $job_number_data[$row[csf('job_no_mst')]]['country_ship_date']=$row[csf('country_ship_date')];
				  $job_number_data[$row[csf('job_no_mst')]]['item_number_id'][]=$row[csf('item_number_id')];
				  $job_number_data[$row[csf('job_no_mst')]]['color_id']=$row[csf('color_number_id')];
				  $job_number_data[$row[csf('job_no_mst')]]['excess_cut']=$row[csf('excess_cut')];
				  $job_number_data[$row[csf('job_no_mst')]]['gauge']=$row[csf('gauge')];
				  $job_number_data[$row[csf('job_no_mst')]]['year']=$row[csf('year')];
				  $job_plan_cutqty_arr[$row[csf('id')]]['plan_qty']+=$row[csf('plan_qty')];
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
			   
			
			$prod_sql="
						SELECT
						        d.company_id,
						        d.source,
						        d.working_company_id,
						        b.size_id,
						        a.gmt_item_id,
						        a.color_id,
						        e.production_date,
						        d.cutting_no,
						        d.job_no,
						        e.delivery_mst_id,

         						
						        sum (case when c.production_type = 50 then  b.size_qty else 0 end) as knitting_issue
						        
						       
						  from ppl_cut_lay_dtls a,
						       ppl_cut_lay_bundle b,
						       pro_garments_production_dtls c,
						       pro_garments_production_mst e,
						       ppl_cut_lay_mst d
						 where     
						       c.bundle_no = b.bundle_no
						       and c.barcode_no = b.barcode_no
						       and d.id=a.mst_id
						       and a.id = b.dtls_id
						       and a.status_active = 1
						       and a.is_deleted = 0
						       and b.status_active = 1
						       and b.is_deleted = 0
						       and d.status_active=1
						       and e.status_active=1
						       and e.id=c.mst_id
						       and c.production_type =50
						       $company_cond 
						       $working_company_cond 
						       $production_date
						       $order_id_cond
						       
						group by 
						       	d.company_id,
						        d.source,
						        d.working_company_id,
						        b.size_id,
						        a.gmt_item_id,
						        a.color_id,
						        e.production_date,
						        d.cutting_no,
						        d.job_no,
						        e.delivery_mst_id
	     						
                    
					  order by 
					  			d.cutting_no,
					  			e.production_date
					  ";

				//echo $prod_sql;
			  	$production_mst_sql=sql_select($prod_sql);

			  	$issue_wise=array();
			  	$delevery_mst_ids=array();
			  	foreach ($production_mst_sql as $row)
			  	{
			  		array_push($delevery_mst_ids, $row[csf('delivery_mst_id')]);

			  		$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['delivery_mst_id']=$row[csf('delivery_mst_id')];
			  		$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['company_id']=$row[csf('company_id')];
			  		$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['source']=$row[csf('source')];
			  		$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['working_company_id']=$row[csf('working_company_id')];
			  		$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['size_id']=$row[csf('size_id')];
			  		$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['gmt_item_id']=$row[csf('gmt_item_id')];
			  		$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['color_id']=$row[csf('color_id')];
			  		$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['production_date']=$row[csf('production_date')];
			  		$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['cutting_no']=$row[csf('cutting_no')];
			  		$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['job_no']=$row[csf('job_no')];
			  		$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_issue']+=$row[csf('knitting_issue')];
			  	}

			  	$mst_id_cond="";
			  	if(count($delevery_mst_ids))
			  	{

			  		$delevery_mst_ids=array_unique($delevery_mst_ids);
			  		$mst_id_cond=where_con_using_array($delevery_mst_ids,0,"d.issue_challan_id");



			  	}

			  




			$recieve_sql="
						SELECT
						        
						        b.size_id,
						        a.gmt_item_id,
						        a.color_id,
						        d.issue_challan_id,
						        sum (case when c.production_type = 51 then  c.bundle_qty else 0 end) as knitting_receive_weight ,
						        sum (case when c.production_type = 51 then c.production_qnty else 0 end) as knitting_receive
						       
						  from ppl_cut_lay_dtls a,
						       ppl_cut_lay_bundle b,
						       pro_garments_production_dtls c,
						       pro_garments_production_mst e,
						       pro_gmts_delivery_mst d
						 where     
						       c.bundle_no = b.bundle_no
						       and c.barcode_no = b.barcode_no
						       and d.id=c.delivery_mst_id
						       and a.id = b.dtls_id
						       and a.status_active = 1
						       and a.is_deleted = 0
						       and b.status_active = 1
						       and b.is_deleted = 0
						       and d.status_active=1
						       and e.status_active=1
						       and e.id=c.mst_id
						       and c.production_type in (51)
						       $mst_id_cond
						       
						       
						group by 
						       	b.size_id,
						        a.gmt_item_id,
						        a.color_id,
						        d.issue_challan_id
	     						
					  ";
				$recieve_res=sql_select($recieve_sql);

				$receive_data=array();
				//echo $recieve_sql;

				foreach ($recieve_res as $row)
			  	{
			  		
			  		$receive_data[$row[csf('issue_challan_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_receive_weight']+=$row[csf('knitting_receive_weight')];
			  		$receive_data[$row[csf('issue_challan_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_receive']+=$row[csf('knitting_receive')];
			  	}
			  	$data_arr=array();

			  	foreach($issue_wise as $delivery_mst_id => $delivery_data)	
				{
					foreach ($delivery_data as $gmt_item_id => $item_data) 
					{
						foreach ($item_data as $color_id => $color_data) 
						{

							foreach ($color_data as $size_id => $row) 
							{


								

							
								
								$job_no=$row['job_no'];

								$working_company_id=$row['working_company_id'];
								$$company_id=$row['company_id'];
								$knitting_issue=$row['knitting_issue'];

								$knitting_receive_weight=$receive_data[$delivery_mst_id][$gmt_item_id][$color_id][$size_id]['knitting_receive_weight'];
								$knitting_receive=$receive_data[$delivery_mst_id][$gmt_item_id][$color_id][$size_id]['knitting_receive'];
								$production_source=$row['source'];

								
								
								
							
							
						   	
								
								
							  	
								  
								  $buyer_name=$job_number_data[$job_no]['buyer_name'];
								  $style=$job_number_data[$job_no]['style'];
								  $year=$job_number_data[$job_no]['year'];

								  
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

								  $data_arr[$row['production_date']][$gmt_item_id][$color_id][$size_id]['job_no'].=$job_no." , ";
								  $data_arr[$row['production_date']][$gmt_item_id][$color_id][$size_id]['buyer_name'].=$buyer_arr[$buyer_name]." , ";
								  $data_arr[$row['production_date']][$gmt_item_id][$color_id][$size_id]['style'].=$style." , ";
								  $data_arr[$row['production_date']][$gmt_item_id][$color_id][$size_id]['year'].=$year." , ";
								  $data_arr[$row['production_date']][$gmt_item_id][$color_id][$size_id]['country_ship_date'].=$country_ship_date." , ";
								  $data_arr[$row['production_date']][$gmt_item_id][$color_id][$size_id]['cutting_no'].=$row['cutting_no']." , ";
								  $data_arr[$row['production_date']][$gmt_item_id][$color_id][$size_id]['working_company'].=$working_company." , ";

								  $data_arr[$row['production_date']][$gmt_item_id][$color_id][$size_id]['knitting_issue']+=$knitting_issue;
								  $data_arr[$row['production_date']][$gmt_item_id][$color_id][$size_id]['knitting_receive']+=$knitting_receive;
								  $data_arr[$row['production_date']][$gmt_item_id][$color_id][$size_id]['knitting_receive_weight']+=$knitting_receive_weight;
								  




								  
				               
							}

						}
										

					}
						
				}

		   		$knitting_issue_total=0;
		   		$knitting_receive_total=0;
		   		$knitting_receive_weight_total=0;
		   		$balance_total=0;
		  ob_start();
	  

		 ?>
  		<fieldset style="width:1730px;">
        	   <table  cellspacing="0" style="justify-content: center;text-align: center;width: 1720px;" >
                    <tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
                           <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" > Date Wise Knitting Production Report</td>
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

            
           
            
             <div style="width:1720px; margin-left:18px" >
             		<table cellspacing="0" border="1" class="rpt_table" width="1700" rules="all">
             			<thead>
             				<tr >
		                       <th width="40" >SL</th>
		                       <th width="80" >Production Date</th>
		                       <th width="130" >Company</th>
		                       <th width="130" >Working Company</th>
		                       <th width="130" >Buyer</th>
		                       <th width="110" >Style</th>
		                       <th width="70"> Job Year</th>
		                       <th width="120" >Job</th>
		                       <th width="130" >GMT Item</th>
		                       <th width="80" >C. Ship Date</th>
		                       <th width="160" >Gmts. Color</th>                        
		                       <th width="80" >Size</th>
		                       <th width="115">Lot Ratio No</th>
		                       <th width="80" >Knitting Issue<br> ( Pcs)</th>
		                       <th width="80" >Knitting Receive<br> (Pcs)</th>
		                       <th width="80">Knitting Receive  Weight<br> (Lbs)</th>
		                       <th width="68">Knitting  Receive Balance</th>
		                      
		                    </tr>
             			</thead>
             		</table>
                    <table cellspacing="0" border="1" class="rpt_table"  width="1700" rules="all"   style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body">
                    	
		                <tbody >
		                	
		                	
	                  			<?
	               
	                     		 $i=1;
				                 $knit=0;
								
								foreach($data_arr as $production_date => $delivery_data)	
								{
									foreach ($delivery_data as $gmt_item_id => $item_data) 
									{
										foreach ($item_data as $color_id => $color_data) 
										{

											foreach ($color_data as $size_id => $row) 
											{
												
												$knitting_issue=$row['knitting_issue'];

												$knitting_receive_weight=$row['knitting_receive_weight'];
												$knitting_receive=$row['knitting_receive'];

												$working_company=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['working_company'] , " , "))) , 'strlen'));
												$buyer_name=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['buyer_name'] , " , "))), 'strlen'));
												$style=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['style'] , " , "))), 'strlen'));
												$year=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['year'] , " , "))), 'strlen'));
												$job_no=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['job_no'] , " , "))), 'strlen'));
												$country_ship_date=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['country_ship_date'] , " , "))), 'strlen'));
												$cutting_no=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['cutting_no'] , " , "))), 'strlen'));

												$balance=$knitting_issue-$knitting_receive;
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
												  //$country_ship_date=$row['country_ship_date'];
												  
								                ?>
								                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
								                    <td width="40" align="left"><? echo $i; ?></td>
								                    <td width="80" align="left"><p><? echo change_date_format($production_date);?></p></td>
								                    <td width="130" align="left"><?php echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
								                    <td width="130" align="left"><?php  echo $working_company; ?></td>
								                   
								                    <td width="130" align="left"><p><? echo $buyer_name; ?></p></td>
								                    <td width="110" align="left"><p><? echo $style;?></p></td>
								                    <td width="70" align="left"><p><? echo $year;?></p></td>
								                    <td width="120" align="left"><p><? echo  $job_no; ?>  </p></td>
								                    <td  width="130" align="left"><p><? echo  $garments_item[$gmt_item_id]; ?> </p></td>
								                    <td width="80" align="left"><p><? echo change_date_format($country_ship_date);?></p></td>
								                    <td width="160" align="left"> <p><?php echo $color_library[$color_id]; ?> </p></td>
								                    <td width="80" align="left"> <p><?php echo $size_library[$size_id]; ?> </p></td>
								                    <td width="115"  align="left"> <p><?php echo $cutting_no; ?></p></td>
								                    <td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_issue,2) ?></td>
								                    <td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_receive,2) ?></td>
								                    <td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_receive_weight,2) ?></td>
								                    <td width="68" style="justify-content: right;text-align: right;"> <?php echo number_format($balance,2) ?></td>
								                   
								        	  	</tr>
												<?	
											     

											      	$knitting_issue_total+=$knitting_issue;
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
	                <table cellspacing="0" border="1" class="rpt_table" width="1700" rules="all">
	                	
	                	 <tfoot>
	                    	 <tr>
		                      		
	                      		<td width="1375" colspan="13" style="justify-content: right;text-align: right;">Total</td>
	                      		
	                      		
	                      		<td width="80" style="justify-content: right;text-align: right;" id="knitting_issue"> <?php echo number_format($knitting_issue_total,2) ?></td>
	                      		<td width="80" style="justify-content: right;text-align: right;" id="knitting_receive"><?php echo number_format($knitting_receive_total,2) ?></td>
	                      		<td width="80" style="justify-content: right;text-align: right;" id="knitting_receive_weight"><?php echo number_format($knitting_receive_weight_total,2) ?></td>
	                      		<td width="68" style="justify-content: right;text-align: right;" id="balance"><?php echo number_format($balance_total,2) ?></td>
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
		echo "$total_data####$filename";
		exit(); 
	}
	else if($type==2)
	{
	   $po_number_data=array();
	   $production_data_arr=array();
	   $po_number_id=array();
		
		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $production_date="";
		else $production_date=" and e.production_date between $txt_date_from and $txt_date_to";

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

		$po_sql="SELECT  a.id,a.job_no_mst,a.po_number,d.order_quantity as order_qty,d.plan_cut_qnty as plan_qty,b.buyer_name,
		b.style_ref_no as style,d.country_ship_date,d.color_number_id,d.item_number_id,b.company_name,a.excess_cut,b.gauge $year_field
		from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
		where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and  a.job_no_mst=d.job_no_mst and  a.is_deleted=0 and a.status_active=1 and 
		b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and
		
		b.status_active=1 $company_name $buyer_name $style_cond  $job_cond_id  $shipping_status_cond order by a.job_no_mst,d.country_ship_date desc";
		//echo $po_sql;
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
			$job_number_data[$row[csf('job_no_mst')]]['country_ship_date']=$row[csf('country_ship_date')];
			$job_number_data[$row[csf('job_no_mst')]]['item_number_id'][]=$row[csf('item_number_id')];
			$job_number_data[$row[csf('job_no_mst')]]['color_id']=$row[csf('color_number_id')];
			$job_number_data[$row[csf('job_no_mst')]]['excess_cut']=$row[csf('excess_cut')];
			$job_number_data[$row[csf('job_no_mst')]]['gauge']=$row[csf('gauge')];
			$job_number_data[$row[csf('job_no_mst')]]['year']=$row[csf('year')];
			$job_plan_cutqty_arr[$row[csf('id')]]['plan_qty']+=$row[csf('plan_qty')];
			array_push( $po_number_id, $row[csf('id')]);
			array_push( $job_no_list, $row[csf('job_no_mst')]);
		}

		// echo "<pre>";
		// print_r( $job_number_data);
		// echo "</pre>";

		$po_number_id=array_unique($po_number_id);
		$job_no_list=array_unique($job_no_list);
		$order_id_cond="";
		if(count($po_number_id))
		{
			$order_id_cond=where_con_using_array($po_number_id,0,"b.order_id");
		}
			 
		
		$prod_sql="
					SELECT
						d.company_id,
						d.source,
						d.working_company_id,
						b.size_id,
						a.gmt_item_id,
						a.color_id,
						e.production_date,
						d.cutting_no,
						d.job_no,
						e.delivery_mst_id,
						c.bodypart_type_id, 
						c.body_part_ids,
						sum (case when c.production_type = 50 then  b.size_qty else 0 end) as knitting_issue
							
							
					from ppl_cut_lay_dtls a,
						ppl_cut_lay_bundle b,
						pro_garments_production_dtls c,
						pro_garments_production_mst e,
						ppl_cut_lay_mst d
					where     
						c.bundle_no = b.bundle_no
						and c.barcode_no = b.barcode_no
						and d.id=a.mst_id
						and a.id = b.dtls_id
						and a.status_active = 1
						and a.is_deleted = 0
						and b.status_active = 1
						and b.is_deleted = 0
						and d.status_active=1
						and e.status_active=1
						and e.id=c.mst_id
						and c.production_type =50
						$company_cond 
						$working_company_cond 
						$production_date
						$order_id_cond
							
					group by 
						d.company_id,
						d.source,
						d.working_company_id,
						b.size_id,
						a.gmt_item_id,
						a.color_id,
						e.production_date,
						d.cutting_no,
						d.job_no,
						e.delivery_mst_id,
						c.bodypart_type_id, 
						c.body_part_ids
							
				
				order by 
					d.cutting_no,
					e.production_date
				";

			//echo $prod_sql;
			$production_mst_sql=sql_select($prod_sql);

			$issue_wise=array();
			$delevery_mst_ids=array();
			foreach ($production_mst_sql as $row)
			{
				array_push($delevery_mst_ids, $row[csf('delivery_mst_id')]);

				$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['delivery_mst_id']=$row[csf('delivery_mst_id')];
				$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['company_id']=$row[csf('company_id')];
				$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['source']=$row[csf('source')];
				$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['working_company_id']=$row[csf('working_company_id')];
				$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['size_id']=$row[csf('size_id')];
				$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['gmt_item_id']=$row[csf('gmt_item_id')];
				$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['color_id']=$row[csf('color_id')];
				$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['production_date']=$row[csf('production_date')];
				$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['cutting_no']=$row[csf('cutting_no')];
				$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['job_no']=$row[csf('job_no')];
				$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_issue']+=$row[csf('knitting_issue')];
				$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['bodypart_type_id']=$row[csf('bodypart_type_id')];
				$issue_wise[$row[csf('delivery_mst_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['body_part_ids']=$row[csf('body_part_ids')];
			}
			/* echo "<pre>";
			print_r($issue_wise);
			echo "</pre>"; */

			$mst_id_cond="";
			$mst_id_cond2="";
			if(count($delevery_mst_ids))
			{
				$delevery_mst_ids=array_unique($delevery_mst_ids);
				$mst_id_cond=where_con_using_array($delevery_mst_ids,0,"d.issue_challan_id");
				$mst_id_cond2=where_con_using_array($delevery_mst_ids,0,"delivery_mst_id");
			}
		$recieve_sql="
			SELECT
					
					b.size_id,
					a.gmt_item_id,
					a.color_id,
					d.issue_challan_id,
					sum (case when c.production_type = 51 then  c.bundle_qty else 0 end) as knitting_receive_weight ,
					sum (case when c.production_type = 51 then c.production_qnty else 0 end) as knitting_receive
					
			from ppl_cut_lay_dtls a,
					ppl_cut_lay_bundle b,
					pro_garments_production_dtls c,
					pro_garments_production_mst e,
					pro_gmts_delivery_mst d
			where     
					c.bundle_no = b.bundle_no
					and c.barcode_no = b.barcode_no
					and d.id=c.delivery_mst_id
					and a.id = b.dtls_id
					and a.status_active = 1
					and a.is_deleted = 0
					and b.status_active = 1
					and b.is_deleted = 0
					and d.status_active=1
					and e.status_active=1
					and e.id=c.mst_id
					and c.production_type in (51)
					$mst_id_cond
			group by 
					b.size_id,
					a.gmt_item_id,
					a.color_id,
					d.issue_challan_id
							   
					";
			//echo $recieve_sql;
			$recieve_res=sql_select($recieve_sql);

			$receive_data=array();
			//echo $recieve_sql;

			foreach ($recieve_res as $row)
			{
				
				$receive_data[$row[csf('issue_challan_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_receive_weight']+=$row[csf('knitting_receive_weight')];
				$receive_data[$row[csf('issue_challan_id')]][$row[csf('gmt_item_id')]][$row[csf('color_id')]][$row[csf('size_id')]]['knitting_receive']+=$row[csf('knitting_receive')];
			}
			
			$data_array_strip=sql_select("SELECT delivery_mst_id, issue_qty from pro_gmts_knitting_issue_dtls where  production_type=50 $mst_id_cond2 and status_active=1 and is_deleted=0 order by id ");
			$issue_qty_lbs_arr = array();
			foreach ($data_array_strip as $row)
			{
				$issue_qty_lbs_arr[$row[csf('delivery_mst_id')]]['issue_qty'] += $row[csf('issue_qty')];
			}


			$data_arr_main=array();
			$data_arr_accessories=array();

			foreach($issue_wise as $delivery_mst_id => $delivery_data)	
			{
				foreach ($delivery_data as $gmt_item_id => $item_data) 
				{
					foreach ($item_data as $color_id => $color_data) 
					{

						foreach ($color_data as $size_id => $row) 
						{
							
							$job_no=$row['job_no'];
							$bodypart_type_id=$row['bodypart_type_id'];
							$body_part_ids=$row['body_part_ids'];

							$working_company_id=$row['working_company_id'];
							$$company_id=$row['company_id'];
							$knitting_issue=$row['knitting_issue'];

							$knitting_receive_weight=$receive_data[$delivery_mst_id][$gmt_item_id][$color_id][$size_id]['knitting_receive_weight'];
							$knitting_receive=$receive_data[$delivery_mst_id][$gmt_item_id][$color_id][$size_id]['knitting_receive'];
							$issue_qty_lbs = $issue_qty_lbs_arr[$delivery_mst_id]['issue_qty'];
							$production_source=$row['source'];
							
								
							$buyer_name=$job_number_data[$job_no]['buyer_name'];
							$style=$job_number_data[$job_no]['style'];
							$year=$job_number_data[$job_no]['year'];

								
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

							if($row['bodypart_type_id']==1)
							{
								$data_arr_main[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['job_no'].=$job_no." , ";
								$data_arr_main[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['buyer_name'].=$buyer_arr[$buyer_name]." , ";
								$data_arr_main[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['style'].=$style." , ";
								$data_arr_main[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['year'].=$year." , ";
								$data_arr_main[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['country_ship_date'].=$country_ship_date." , ";
								$data_arr_main[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['cutting_no'].=$row['cutting_no']." , ";
								$data_arr_main[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['working_company'].=$working_company." , ";
	
								$data_arr_main[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['knitting_issue']+=$knitting_issue;
								$data_arr_main[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['knitting_receive']+=$knitting_receive;
								$data_arr_main[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['knitting_receive_weight']+=$knitting_receive_weight;
								$data_arr_main[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['body_part_ids']=$body_part_ids;
								$data_arr_main[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['issue_qty_lbs']+=$issue_qty_lbs;
							}
							else if($row['bodypart_type_id']==2)
							{
								$data_arr_accessories[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['job_no'].=$job_no." , ";
								$data_arr_accessories[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['buyer_name'].=$buyer_arr[$buyer_name]." , ";
								$data_arr_accessories[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['style'].=$style." , ";
								$data_arr_accessories[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['year'].=$year." , ";
								$data_arr_accessories[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['country_ship_date'].=$country_ship_date." , ";
								$data_arr_accessories[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['cutting_no'].=$row['cutting_no']." , ";
								$data_arr_accessories[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['working_company'].=$working_company." , ";
								$data_arr_accessories[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['knitting_issue']+=$knitting_issue;
								$data_arr_accessories[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['knitting_receive']+=$knitting_receive;
								$data_arr_accessories[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['knitting_receive_weight']+=$knitting_receive_weight;
								$data_arr_accessories[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['body_part_ids']=$body_part_ids;
								$data_arr_accessories[$row['bodypart_type_id']][$row['production_date']][$gmt_item_id][$color_id][$size_id]['issue_qty_lbs']+=$issue_qty_lbs;
							}	
						}
					}
				}	
			}
			// echo "<pre>";
			// print_r($data_arr);
			// echo "</pre>";
			
		ob_start();
	

	   ?>
		<fieldset style="width:1960px;">
			 <table  cellspacing="0" style="justify-content: center;text-align: center;width: 1870px;" >
				  <tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
						 <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" > Date Wise Knitting Production Report</td>
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
		    <div style="width:1950px; margin-left:18px" >
				<table cellspacing="0" border="1" class="rpt_table" width="1930" rules="all">
					<thead>
						<tr>
							<td colspan="18" style="font-size: 16px; font-weight:bold">Knit Production [Body]	</td>
						</tr>
						<tr >
							<th width="40" >SL</th>
							<th width="80" >Production Date</th>
							<th width="130" >Company</th>
							<th width="130" >Working Company</th>
							<th width="130" >Buyer</th>
							<th width="110" >Style</th>
							<th width="70"> Job Year</th>
							<th width="120" >Job</th>
							<th width="130" >GMT Item</th>
							<th width="150" >Baody Part</th>
							<th width="80" >C. Ship Date</th>
							<th width="160" >Gmts. Color</th>                        
							<th width="80" >Size</th>
							<th width="115">Lot Ratio No</th>
							<th width="80" >Knitting Issue<br> ( Pcs)</th>
							<th width="80" >Knitting Receive<br> (Pcs)</th>
							<th width="68">Knitting  Receive Balance</th>
							<th width="80">Knitting Issue  Weight<br> (Lbs)</th>
							<th width="80">Knitting Receive  Weight<br> (Lbs)</th>
						
						</tr>
					</thead>
				</table>
				<table cellspacing="0" border="1" class="rpt_table"  width="1930" rules="all"   style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body">
					  
					<tbody >
						<?
			
						$i=1;
						$knit=0;
						$knitting_issue_total=0;
						$knitting_receive_total=0;
						$knitting_receive_weight_total=0;
						$issue_qty_lbs_total=0;
						$balance_total=0;
						
						foreach ($data_arr_main as $bodypart_type_id => $bodypart_type_id_data) 
						{
							foreach($bodypart_type_id_data as $production_date => $delivery_data)	
							{
								foreach ($delivery_data as $gmt_item_id => $item_data) 
								{
									foreach ($item_data as $color_id => $color_data) 
									{

										foreach ($color_data as $size_id => $row) 
										{
											
											$knitting_issue=$row['knitting_issue'];

											$knitting_receive_weight=$row['knitting_receive_weight'];
											$knitting_receive=$row['knitting_receive'];
											$issue_qty_lbs=$row['issue_qty_lbs'];

											$working_company=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['working_company'] , " , "))) , 'strlen'));
											$buyer_name=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['buyer_name'] , " , "))), 'strlen'));
											$style=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['style'] , " , "))), 'strlen'));
											$year=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['year'] , " , "))), 'strlen'));
											$job_no=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['job_no'] , " , "))), 'strlen'));
											$country_ship_date=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['country_ship_date'] , " , "))), 'strlen'));
											$cutting_no=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['cutting_no'] , " , "))), 'strlen'));
											$body_part_ids_arr=array_filter(array_unique(explode(",", chop($row['body_part_ids'] , ", "))));
											$body_part_name = '';
											foreach ($body_part_ids_arr as $row) 
											{
												$body_part_name .= $time_weight_panel[$row].', ';
											}
										

											$balance=$knitting_issue-$knitting_receive;
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
											
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
												<td width="40" align="left"><? echo $i; ?></td>
												<td width="80" align="left"><p><? echo change_date_format($production_date);?></p></td>
												<td width="130" align="left"><?php echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
												<td width="130" align="left"><?php  echo $working_company; ?></td>
												<td width="130" align="left"><p><? echo $buyer_name; ?></p></td>
												<td width="110" align="left"><p><? echo $style;?></p></td>
												<td width="70" align="left"><p><? echo $year;?></p></td>
												<td width="120" align="left"><p><? echo  $job_no; ?>  </p></td>
												<td  width="130" align="left"><p><? echo  $garments_item[$gmt_item_id]; ?> </p></td>
												<td  width="150" align="left"><p><? echo  rtrim($body_part_name,', '); ?> </p></td>
												<td width="80" align="left"><p><? echo change_date_format($country_ship_date);?></p></td>
												<td width="160" align="left"> <p><?php echo $color_library[$color_id]; ?> </p></td>
												<td width="80" align="left"> <p><?php echo $size_library[$size_id]; ?> </p></td>
												<td width="115"  align="left"> <p><?php echo $cutting_no; ?></p></td>
												<td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_issue,2) ?></td>
												<td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_receive,2) ?></td>
												<td width="68" style="justify-content: right;text-align: right;"> <?php echo number_format($balance,2) ?></td>
												<td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($issue_qty_lbs,2) ?></td>
												<td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_receive_weight,2) ?></td>
												
												
											</tr>
											<?	

												$knitting_issue_total+=$knitting_issue;
												$knitting_receive_total+=$knitting_receive;
												$knitting_receive_weight_total+=$knitting_receive_weight;
												$issue_qty_lbs_total+=$issue_qty_lbs;
												$balance_total+=$balance;
													
											$i++;	

										}
									}
								}	
							}
						}
						
					
						?>
					</tbody>			  
					</table> 
					<table cellspacing="0" border="1" class="rpt_table" width="1930" rules="all">
						
						<tfoot>
							<tr>	
								<td width="1375" colspan="14" style="justify-content: right;text-align: right;">Total</td>
								<td width="80" style="justify-content: right;text-align: right;" id="knitting_issue"> <?php echo number_format($knitting_issue_total,2) ?></td>
								<td width="80" style="justify-content: right;text-align: right;" id="knitting_receive"><?php echo number_format($knitting_receive_total,2) ?></td>
								<td width="68" style="justify-content: right;text-align: right;" id="balance"><?php echo number_format($balance_total,2) ?></td>
								<td width="80" style="justify-content: right;text-align: right;" id="knitting_issue_weight"><?php echo number_format($issue_qty_lbs_total,2) ?></td>
								<td width="80" style="justify-content: right;text-align: right;" id="knitting_receive_weight"><?php echo number_format($knitting_receive_weight_total,2) ?></td>
							</tr>  
						</tfoot>
					</table>
			</div> 
			
		   <br>
		    <div style="width:1950px; margin-left:18px" >
				<table cellspacing="0" border="1" class="rpt_table" width="1930" rules="all">
					<thead>
						<tr>
							<td colspan="18" style="font-size: 16px; font-weight:bold">Knit Production [Accessories]	</td>
						</tr>
						<tr >
							<th width="40" >SL</th>
							<th width="80" >Production Date</th>
							<th width="130" >Company</th>
							<th width="130" >Working Company</th>
							<th width="130" >Buyer</th>
							<th width="110" >Style</th>
							<th width="70"> Job Year</th>
							<th width="120" >Job</th>
							<th width="130" >GMT Item</th>
							<th width="150" >Baody Part</th>
							<th width="80" >C. Ship Date</th>
							<th width="160" >Gmts. Color</th>                        
							<th width="80" >Size</th>
							<th width="115">Lot Ratio No</th>
							<th width="80" >Knitting Issue<br> ( Pcs)</th>
							<th width="80" >Knitting Receive<br> (Pcs)</th>
							<th width="68">Knitting  Receive Balance</th>
							<th width="80">Knitting Issue  Weight<br> (Lbs)</th>
							<th width="80">Knitting Receive  Weight<br> (Lbs)</th>
						
						</tr>
					</thead>
				</table>
				<table cellspacing="0" border="1" class="rpt_table"  width="1930" rules="all"   style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body1">
					  
					<tbody >
						<?
			
						$i=1;
						$knit=0;
						$knitting_issue_total_acc=0;
						$knitting_receive_total_acc=0;
						$knitting_receive_weight_total_acc=0;
						$issue_qty_lbs_total_acc=0;
						$balance_total_acc=0;
						foreach ($data_arr_accessories as $bodypart_type_id => $bodypart_type_id_data) 
						{
							foreach($bodypart_type_id_data as $production_date => $delivery_data)	
							{
								foreach ($delivery_data as $gmt_item_id => $item_data) 
								{
									foreach ($item_data as $color_id => $color_data) 
									{

										foreach ($color_data as $size_id => $row) 
										{
											
											$knitting_issue=$row['knitting_issue'];

											$knitting_receive_weight=$row['knitting_receive_weight'];
											$knitting_receive=$row['knitting_receive'];
											$issue_qty_lbs=$row['issue_qty_lbs'];

											$working_company=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['working_company'] , " , "))) , 'strlen'));
											$buyer_name=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['buyer_name'] , " , "))), 'strlen'));
											$style=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['style'] , " , "))), 'strlen'));
											$year=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['year'] , " , "))), 'strlen'));
											$job_no=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['job_no'] , " , "))), 'strlen'));
											$country_ship_date=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['country_ship_date'] , " , "))), 'strlen'));
											$cutting_no=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['cutting_no'] , " , "))), 'strlen'));
											$bodypart_type_id=implode(" , ", array_filter(array_unique(explode(" , ", chop($row['bodypart_type_id'] , " , "))), 'strlen'));
											$body_part_ids_arr=array_filter(array_unique(explode(",", chop($row['body_part_ids'] , ", "))));
											$body_part_name = '';
											foreach ($body_part_ids_arr as $row) 
											{
												$body_part_name .= $time_weight_panel[$row].', ';
											}
										

											$balance=$knitting_issue-$knitting_receive;
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
											
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
												<td width="40" align="left"><? echo $i; ?></td>
												<td width="80" align="left"><p><? echo change_date_format($production_date);?></p></td>
												<td width="130" align="left"><?php echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
												<td width="130" align="left"><?php  echo $working_company; ?></td>
												<td width="130" align="left"><p><? echo $buyer_name; ?></p></td>
												<td width="110" align="left"><p><? echo $style;?></p></td>
												<td width="70" align="left"><p><? echo $year;?></p></td>
												<td width="120" align="left"><p><? echo  $job_no; ?>  </p></td>
												<td  width="130" align="left"><p><? echo  $garments_item[$gmt_item_id]; ?> </p></td>
												<td  width="150" align="left"><p><? echo  rtrim($body_part_name,', '); ?> </p></td>
												<td width="80" align="left"><p><? echo change_date_format($country_ship_date);?></p></td>
												<td width="160" align="left"> <p><?php echo $color_library[$color_id]; ?> </p></td>
												<td width="80" align="left"> <p><?php echo $size_library[$size_id]; ?> </p></td>
												<td width="115"  align="left"> <p><?php echo $cutting_no; ?></p></td>
												<td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_issue,2) ?></td>
												<td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_receive,2) ?></td>
												<td width="68" style="justify-content: right;text-align: right;"> <?php echo number_format($balance,2) ?></td>
												<td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($issue_qty_lbs,2) ?></td>
												<td width="80" style="justify-content: right;text-align: right;"> <?php echo number_format($knitting_receive_weight,2) ?></td>
												
											</tr>
											<?	

												$knitting_issue_total_acc+=$knitting_issue;
												$knitting_receive_total_acc+=$knitting_receive;
												$knitting_receive_weight_total_acc+=$knitting_receive_weight;
												$issue_qty_lbs_total_acc+=$issue_qty_lbs;
												$balance_total_acc+=$balance;
													
											$i++;	

										}
									}
								}	
							}
						}
						
					
						?>
					</tbody>			  
					</table> 
					<table cellspacing="0" border="1" class="rpt_table" width="1930" rules="all">
						
						<tfoot>
							<tr>	
								<td width="1375" colspan="14" style="justify-content: right;text-align: right;">Total</td>
								<td width="80" style="justify-content: right;text-align: right;" id="knitting_issue_acc"> <?php echo number_format($knitting_issue_total_acc,2) ?></td>
								<td width="80" style="justify-content: right;text-align: right;" id="knitting_receive_acc"><?php echo number_format($knitting_receive_total_acc,2) ?></td>
								<td width="68" style="justify-content: right;text-align: right;" id="balance_acc"><?php echo number_format($balance_total_acc,2) ?></td>
								<td width="80" style="justify-content: right;text-align: right;" id="knitting_issue_weight_acc"><?php echo number_format($issue_qty_lbs_total_acc,2) ?></td>
								<td width="80" style="justify-content: right;text-align: right;" id="knitting_receive_weight_acc"><?php echo number_format($knitting_receive_weight_total_acc,2) ?></td>
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
	  echo "$total_data####$filename";
	  exit(); 
 	} 
	
}




