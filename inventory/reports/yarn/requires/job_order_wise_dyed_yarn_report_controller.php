<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_name", 130, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name","id,supplier_name", 1, "--Select Supplier--", $selected, "" );
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
			//alert (str);
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
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
	                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
	                    <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year; ?>', 'create_job_no_search_list_view', 'search_div', 'job_order_wise_dyed_yarn_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
	                    </td>
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];

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

	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($db_type==0) $insert_year="year(insert_date)";
	if($db_type==2) $insert_year="to_char(insert_date,'yyyy')";


	if($db_type==0)
	{
		if($data[4]!=0) $year_cond=" and YEAR(insert_date)=$data[4]"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($data[4]!=0) $year_cond=" $year_field_con=$data[4]"; else $year_cond="";
	}

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $insert_year as year from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond order by job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0') ;

   exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "SELECT id, company_name from lib_company where status_active=1 and is_deleted=0 ",'id','company_name');//and id =$cbo_company_name 

	$lot_prod_arr=return_library_array( "select id, lot from product_details_master where status_active=1 and is_deleted=0",'id','lot');

	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	$supplier_arr=return_library_array( "SELECT id, short_name from lib_supplier where status_active=1 and is_deleted=0",'id','short_name');
	$wo_type_arr = array('All','Short','Main');

	$txt_process_loss=str_replace("'","",$txt_process_loss);

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
	$job_no=str_replace("'","",$txt_job_no);
	if($job_no=="")
	{
		$job_no_cond="";
	}
	else
	{
		$job_no_cond="and a.job_no_prefix_num ='$job_no'";
	}

	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if(trim($cbo_year)!=0) $year_cond=" $year_field_con=$cbo_year"; else $year_cond="";
	}

	$color_array=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);

	if($db_type==0)
	{
		$date_from=change_date_format($from_date,'yyyy-mm-dd');
		$date_to=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$date_from=change_date_format($from_date,'','',1);
		$date_to=change_date_format($to_date,'','',1);
	}
	else
	{
		$date_from="";
		$date_to="";
	}
	$date_trans_cond=""; $date_cond="";
	if(str_replace("'","",$cbo_date_type)==1)
	{
		if($date_from!="" && $date_to!="") $date_cond=" and c.booking_date between '".$date_from."' and '".$date_to."'"; else $date_cond="";
	}
	else if(str_replace("'","",$cbo_date_type)==2)
	{
		if($date_from!="" && $date_to!="") $date_trans_cond=" and a.transaction_date between '".$date_from."' and '".$date_to."'"; else $date_trans_cond="";
	}

	$supplier_id=str_replace("'","",$cbo_supplier_name);


	if($supplier_id==0) $supplier_id_cond=""; else  $supplier_id_cond="and c.supplier_id='$supplier_id'";

	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	
	$search_cond='';
	if ($txt_search_comm=="") 
	{
		$search_cond.="";
	}
	else
	{
		if($cbo_search_by==1) {

			$search_cond.=" and c.ydw_no LIKE '%$txt_search_comm%'";
		}
		else if($cbo_search_by==2) 
		{
			$booking_search_sql = "SELECT job_no,booking_no from wo_booking_mst where status_active=1 and is_deleted=0 and booking_no LIKE  '%$txt_search_comm%' group by job_no,booking_no";

			$booking_search_data = sql_select($booking_search_sql);

			foreach ($booking_search_data as $row) {
				
				$booking_job_no_arr[] = "'".$row[csf('job_no')]."'";
			}

			$booking_search_job_no_string = implode(',',array_unique($booking_job_no_arr));

			$search_cond.=" and a.job_no in ($booking_search_job_no_string)";
		}
	}

	$cbo_wo_type=str_replace("'","",$cbo_wo_type);
	
	if($cbo_wo_type==1)// Main
	{
		$wo_type_cond = "and c.is_short=2";
	}else if($cbo_wo_type==2) {// main 
		$wo_type_cond = "and c.is_short=1";
	}else{
		$wo_type_cond = "";
	}

	if($txt_internal_ref!="")
	{
		$jobno =  return_field_value("job_no_mst","wo_po_break_down" ,"status_active=1 and is_deleted=0 and grouping=$txt_internal_ref group by job_no_mst","job_no_mst");
		if($jobno!="")
		{
			$internal_ref_job_cond = "and a.job_no = '$jobno'";
		}
	}

	if($type==1)
	{	
		if($db_type==0) 
		{

		   $sql_main="SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no,group_concat(distinct(b.product_id)) as product_id,sum(b.yarn_wo_qty) as qnty,c.id, c.ydw_no,c.supplier_id,c.is_short,c.pay_mode,group_concat(distinct(b.yarn_color)) as yarn_color from wo_po_details_master a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where a.id=b.job_no_id and b.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form not in (94,42,114) and c.booking_without_order!=1 and a.company_name=$cbo_company_name $buyer_id_cond $job_no_cond $year_cond $date_cond $supplier_id_cond $search_cond $wo_type_cond $internal_ref_job_cond group by a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, c.id, c.ydw_no, c.supplier_id,c.is_short,c.pay_mode order by a.job_no,c.id";
		}
		else if($db_type==2)
		{
			$sql_main="SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no,listagg(b.product_id,',') within group (order by b.product_id) as product_id,sum(b.yarn_wo_qty) as qnty, c.id, c.ydw_no, c.supplier_id,c.is_short,c.pay_mode,listagg(b.yarn_color,',') within group (order by b.yarn_color) as yarn_color from wo_po_details_master a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where a.id=b.job_no_id and b.mst_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form not in (94,42,114) and c.booking_without_order!=1 and a.company_name=$cbo_company_name $buyer_id_cond $job_no_cond $year_cond $date_cond $supplier_id_cond $search_cond $wo_type_cond $internal_ref_job_cond group by a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no,c.id,c.ydw_no,c.supplier_id,c.is_short,c.pay_mode order by a.job_no,c.id";
		}
		//echo $sql_main;
		// die();
		$main_query_result = sql_select($sql_main);

		if(count($main_query_result)>0)
		{
			foreach($main_query_result as $row)
			{
				$job_no_arr[] = "'".$row[csf('job_no')]."'";
				$work_order_id_arr[] = $row[csf('id')];
				
				if($row[csf('product_id')]!="")
				{
					$grey_product_id_arr[] = $row[csf('product_id')];
				}
				
				$workorder_job[$row[csf('id')]]= $row[csf('job_no')];
			}

			$job_no_string = implode(',',array_unique($job_no_arr));
			$work_order_ids_string = implode(',',array_unique($work_order_id_arr));
			$grey_product_id_string = implode(',',array_unique($grey_product_id_arr));
		}
		else
		{
			echo "<br><center><span style='color:red; font-size:20px; font-weight:bolder;'>Data Not Fond.</span></center>";
			die();
		}

		if($job_no_string!="")
		{
			if($db_type==0)
		    {
				$order_sql = "SELECT job_no_mst, group_concat(distinct(po_number)) as order_no,group_concat(distinct(grouping)) as internal_ref from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst in($job_no_string) group by job_no_mst";

				$order_resutl = sql_select($order_sql);
				foreach ($order_resutl as $row) {
					$order_arr[$row[csf('job_no_mst')]]['order_no'] = $row[csf('order_no')];
					$order_arr[$row[csf('job_no_mst')]]['internal_ref'] = $row[csf('internal_ref')];
				}

				$booking_arr = return_library_array( "SELECT job_no,group_concat(distinct(booking_no)) as booking_no from wo_booking_mst where status_active=1 and is_deleted=0 and job_no in($job_no_string) group by job_no", "job_no", "booking_no" );
			}
		 	else if($db_type==2)
		    {
				$order_sql = "SELECT job_no_mst,listagg(po_number,',') within group (order by po_number) as order_no,listagg(grouping,',') within group (order by po_number) as internal_ref from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst in($job_no_string) group by job_no_mst";

				$order_resutl = sql_select($order_sql);
				foreach ($order_resutl as $row) {
					$order_arr[$row[csf('job_no_mst')]]['order_no'] = $row[csf('order_no')];
					$order_arr[$row[csf('job_no_mst')]]['internal_ref'] = $row[csf('internal_ref')];
				}	

				$booking_arr = return_library_array( "SELECT job_no,listagg(booking_no,', ') within group (order by booking_no) as booking_no from wo_booking_mst where status_active=1 and is_deleted=0 and job_no in($job_no_string) group by job_no", "job_no", "booking_no" );		
			}	
		}
		
		if($job_no_string!="" && $work_order_ids_string!="")
		{	
			//if($grey_product_id_string!="")
			//{
				$bookingSql = "SELECT mst_id, sum(yarn_wo_qty) as qnty, yarn_color, job_no from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0 and mst_id in($work_order_ids_string) and job_no in($job_no_string)  group by job_no, mst_id, yarn_color ";
				$bookingData = sql_select($bookingSql);

				$BookingColorArray=array(); $color_req_array=array();
				foreach($bookingData as $row)
				{
					$BookingColorArray[$row[csf('mst_id')]].=$row[csf('yarn_color')].",";
					$color_req_array[$row[csf('job_no')]][$row[csf('mst_id')]][$row[csf('yarn_color')]]=$row[csf('qnty')];
					$job_wise_req_array[$row[csf('job_no')]][$row[csf('mst_id')]]+=$row[csf('qnty')];
				}
			//}		
			
			$job_no_string_cond = "and a.job_no in($job_no_string)";
			$work_order_ids_string_cond = "and b.booking_id in($work_order_ids_string)";
			
			if($grey_product_id_string!="")
			{
				$grey_product_id_string_cond = "and a.prod_id in($grey_product_id_string)";
			}
			
			//echo $grey_product_id_string_cond;

			$grey_issue_sql = "SELECT b.id, a.job_no, a.cons_quantity as issue_qnty, b.booking_id from inv_transaction a, inv_issue_master b where a.mst_id=b.id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=2 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and a.transaction_type=2 $work_order_ids_string_cond $job_no_string_cond $grey_product_id_string_cond $date_trans_cond";
			//echo $grey_issue_sql;
			$issueDataArr = sql_select($grey_issue_sql);

			$issue_arr=array();
			foreach($issueDataArr as $row)
			{
				$issue_arr[$row[csf('job_no')]][$row[csf('booking_id')]] += $row[csf('issue_qnty')];
				$issue_id_arr[] = $row[csf('id')];
			} 
			
		}

		$issue_id_string = implode(',',array_unique($issue_id_arr));
		
		if($issue_id_string!="")
		{
			//if($grey_product_id_string_cond!="")
			//{
		        $issueRet_arr_sql= "SELECT c.id as trans_id, c.quantity as issue_ret_qnty, b.booking_id, d.job_no_mst from inv_transaction a, inv_receive_master b, order_wise_pro_details c, wo_po_break_down d where a.mst_id = b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and b.entry_form = 9 and a.item_category=1 and a.transaction_type=4 and b.receive_basis=1 and a.company_id = $cbo_company_name and a.issue_id in($issue_id_string) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and c.entry_form = 9 and c.trans_type = 4 $work_order_ids_string_cond $grey_product_id_string_cond $date_trans_cond";

				$issue_return_Data_Arr=sql_select($issueRet_arr_sql);

				$issueRet_arr=array(); $trans_check=array();
				foreach($issue_return_Data_Arr as $val)
				{
					if($trans_check[$val[csf("trans_id")]]=="")
					{
						$issueRet_arr[$val[csf('job_no_mst')]][$val[csf('booking_id')]]+=$val[csf('issue_ret_qnty')];
						$trans_check[$val[csf("trans_id")]]= $val[csf("trans_id")];
					}
				}
			//}
	        
		}  

		if($job_no_string!="" && $work_order_ids_string!="")
		{
			if($db_type==0)
			{
				$mrrRcvSql="select a.job_no, sum(a.cons_quantity) as recv_qnty,b.id as mrr_rcv_id, b.booking_id,group_concat(distinct(c.id)) as product_id, c.color from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=1 and a.transaction_type=1 and a.job_no in($job_no_string) and b.booking_id in($work_order_ids_string) and c.dyed_type=1 $date_trans_cond group by a.job_no,b.id,b.booking_id,c.color"; //and a.prod_id=19184 
			}
			else if($db_type=2)
			{
				$mrrRcvSql="select a.job_no, sum(a.cons_quantity) as recv_qnty,b.id as mrr_rcv_id, b.booking_id, listagg(c.id,',') within group (order by c.id) as product_id, c.color from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=1 and a.transaction_type=1 and a.job_no in($job_no_string) and b.booking_id in($work_order_ids_string) and c.dyed_type=1 $date_trans_cond group by a.job_no,b.id,b.booking_id,c.color"; //and a.prod_id=19184 
			}
			
			$mrrRcvDataArr=sql_select($mrrRcvSql);

			$recv_arr=array();
			foreach($mrrRcvDataArr as $row)
			{
				$recv_arr[$row[csf('job_no')]][$row[csf('booking_id')]][$row[csf('color')]] += $row[csf('recv_qnty')];				
				$job_total_recv_arr[$row[csf('job_no')]][$row[csf('booking_id')]] += $row[csf('recv_qnty')];

				$receive_wo_job_arr[$row[csf("mrr_rcv_id")]]['job_no'] = $row[csf('job_no')];
				$receive_wo_job_arr[$row[csf('mrr_rcv_id')]]['work_order_id'] = $row[csf('booking_id')];

				$mrr_arr[$row[csf("mrr_rcv_id")]] = $row[csf("mrr_rcv_id")];
				$dyed_product_id_arr[] = $row[csf('product_id')];
			}
		}

		$mrr_ids = implode(",",array_filter($mrr_arr));
		$dyed_product_id_string = implode(',',array_unique($dyed_product_id_arr));

		if($mrr_ids!="")
        {    
        	if($dyed_product_id_string!="")
        	{
	        	$sql_rcvrtn = "SELECT b.received_id,c.color, sum(a.cons_quantity) as rec_ret_qnty from inv_transaction a, inv_issue_master b, product_details_master c where b.id = a.mst_id and a.prod_id = c.id and b.entry_form = 8 and a.item_category = 1 and a.transaction_type = 3 and a.company_id = $cbo_company_name and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and b.received_id in ($mrr_ids) and a.prod_id in ($dyed_product_id_string) $date_trans_cond group by b.received_id,c.color";

	        	$recRetDataArr=sql_select($sql_rcvrtn);

	        	foreach($recRetDataArr as $row)
				{	
					$job_no = $receive_wo_job_arr[$row[csf("received_id")]]['job_no'];
					$work_order_id = $receive_wo_job_arr[$row[csf('received_id')]]['work_order_id'];
					
					$dyedYarnRcvReturn[$job_no][$work_order_id][$row[csf('color')]]+=$row[csf('rec_ret_qnty')];
					$jobwise_dyed_recRet_arr[$job_no]+=$row[csf('rec_ret_qnty')];
				}
        	}    	
        	
        }
		//echo "<pre>";
		//print_r($dyedRecRetArr);
		//Issue Nos From Receive Mrr
		if($dyed_product_id_string!="")
		{
			$issue_sql = sql_select("select a.id,b.cons_quantity, d.id as receive_id 
				from  inv_issue_master a, inv_transaction b, inv_mrr_wise_issue_details c, inv_receive_master d, inv_transaction e where a.id = b.mst_id and b.id = c.issue_trans_id and c.recv_trans_id = e.id and d.id = e.mst_id and d.id in ($mrr_ids) and b.prod_id in ($dyed_product_id_string) and a.entry_form = 3 and c.entry_form = 3 and d.item_category=1 and b.status_active = 1 and b.is_deleted = 0");

			foreach ($issue_sql as $val)
			{
				$issue_id_arr[$val[csf("id")]] = $val[csf("id")];
				$receive_id_by_issu_id[$val[csf("id")]] = $val[csf("receive_id")];
			}	
		}		

		$issue_ids = implode(",", array_filter($issue_id_arr));

		if($issue_ids)
		{
			// $Issue Return From Issue
			if($dyed_product_id_string!="")
			{
				$issue_ret_sql = sql_select("select a.id,a.issue_id,b.cons_quantity from  inv_receive_master a, inv_transaction b
				where a.id = b.mst_id and a.entry_form =9 and b.item_category = 1
				and b.status_active = 1 and b.is_deleted = 0 and a.issue_id in ($issue_ids) and b.prod_id in ($dyed_product_id_string)");

				foreach ($issue_ret_sql as $val)
				{
					$issue_id_by_issue_rtn[$val[csf("id")]] = $val[csf("issue_id")];
					$issue_ret_ids[$val[csf("id")]] = $val[csf("id")];
				}
				unset($issue_ret_sql);
			}
			
			
			$issue_ret_ids = implode(",", array_filter($issue_ret_ids));				
			
			if($issue_ret_ids)
			{
				if($dyed_product_id_string!="")
				{
					$rcvReturnFromIssueRetArr = sql_select("SELECT b.received_id,c.color, sum(a.cons_quantity) as rec_ret_qnty from inv_transaction a, inv_issue_master b, product_details_master c where b.id = a.mst_id and a.prod_id = c.id and b.entry_form = 8 and a.item_category = 1 and a.transaction_type = 3 and a.company_id = $cbo_company_name and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and b.received_id in ($issue_ret_ids) and a.prod_id in ($dyed_product_id_string) $date_trans_cond group by b.received_id,c.color");

					foreach ($rcvReturnFromIssueRetArr as $row)
					{
						$issueId = $issue_id_by_issue_rtn[$row[csf("received_id")]];
						$receive_id = $receive_id_by_issu_id[$issueId];

						$job_no = $receive_wo_job_arr[$receive_id]['job_no'];
						$work_order_id = $receive_wo_job_arr[$receive_id]['work_order_id'];
					
						$dyedYarnRcvReturn[$job_no][$work_order_id][$row[csf('color')]]+=$row[csf('rec_ret_qnty')];
						
						$jobwise_dyed_recRet_arr[$job_no]+=$row[csf('rec_ret_qnty')];
					}
				}				
			}
		}
        //echo "<pre>";
        //print_r($dyedYarnRcvReturnByRcvMrr);

		ob_start();
		?>
		<fieldset style="width:1200px; margin-left:10px">
			<table cellpadding="0" cellspacing="0" width="1200">
				<tr>
				   <td align="center" width="100%" colspan="17" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr>
				   <td align="center" width="100%" colspan="17" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr>
			</table>
			<table width="1182" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="35" rowspan="2">SL</th>
						<th width="100" rowspan="2">WO No</th>
						<th width="50" rowspan="2">WO Type</th>
						<th width="100" rowspan="2">Booking No</th>
						<th width="240" colspan="3">Grey Yarn</th>
						<th width="530" colspan="7">Dyed Yarn</th>
						<th rowspan="2">Party Name</th>
					</tr>
					<tr>
						<th width="80">Total Required</th>
						<th width="80">Delivery</th>
						<th width="80">Balance</th>
						<th width="90">Order Qty. (Less <? echo $txt_process_loss; ?>% process loss)</th>
                        <th width="80">Req. Qty.</th>
                        <th width="70">Color</th>
						<th width="80">Received Qty.</th>
                        <th width="80">Color Wise Balance</th>
						<th width="80">Balance</th>
						<th width="70">Act. Pro. Loss(%)</th>
					</tr>
				</thead>
			</table>
			<div style="width:1200px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="1182" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?
						$i=1; $z=0; $job_array=array();
						$tot_recv_bl_qnty=0; $total_req_qnty=0; $tot_delivery_qnty=0; $total_grey_bl_qnty=0; $total_process_loss_qnty=0; $total_recv_qnty=0; $total_dyed_bl_qnty=0;
						$job_total_recv_qty = 0;
						$job_req_qty = 0;
						$dyed_balance = 0;

						foreach($main_query_result as $row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$colorArray=explode(",",substr($BookingColorArray[$row[csf('id')]],0,-1));
							$s=0; $row_span=count($colorArray);

							$delivery_qnty=$issue_arr[$row[csf('job_no')]][$row[csf('id')]] - $issueRet_arr[$row[csf('job_no')]][$row[csf('id')]];
							$grey_balance=$row[csf('qnty')]-$delivery_qnty;

							$process_loss_qnty=$delivery_qnty-($delivery_qnty*$txt_process_loss/100);
							
							$job_req_qty  = $job_req_array[$row[csf('job_no')]][$row[csf('id')]];
							$job_wise_req_qty = $job_wise_req_array[$row[csf('job_no')]][$row[csf('id')]];
							$job_proce_req_qnty=$job_wise_req_qty-($job_wise_req_qty*$txt_process_loss/100);

							$actual_rcv_qty = array();
							foreach($colorArray as $color_id)
							{
								$actual_rcv_qty[$row[csf('id')]] += ($recv_arr[$row[csf('job_no')]][$row[csf('id')]][$color_id]-$dyedYarnRcvReturn[$row[csf('job_no')]][$row[csf('id')]][$color_id]);								
							}
							
							$dyed_balance = ($job_proce_req_qnty-$actual_rcv_qty[$row[csf('id')]]);
							$actual_process_loss=($delivery_qnty-$actual_rcv_qty[$row[csf('id')]])/$delivery_qnty*100;

							$order_no  = implode(",",array_unique(explode(",", $order_arr[$row[csf('job_no')]]['order_no'])));
							$internal_ref  = implode(",",array_unique(explode(",", trim($order_arr[$row[csf('job_no')]]['internal_ref']))));
	
							if($row[csf('is_short')]==1)
							{
								$workOrderType = "Short";
							}else{
								$workOrderType = "Main";
							}
							
							if(!in_array($row[csf('job_no')],$job_array))
							{
								if($i!=1)
								{
								?>
									<tr bgcolor="#CCCCCC">
										<td colspan="4" align="right"><b>Job Total</b></td>
                                        <td align="right"><?php echo number_format($job_req_qnty,2,'.',''); ?></td>
                                        <td align="right"><?php echo number_format($job_delivery_qnty,2,'.',''); ?></td>
                                        <td align="right"><?php echo number_format($job_grey_bl_qnty,2,'.',''); ?></td>
                                        <td align="right"><?php echo number_format($job_process_loss_qnty,2,'.',''); ?></td>
                                        <td align="right"><?php echo number_format($job_color_req_qnty,2,'.',''); ?></td>
                                        <td>&nbsp;</th>
                                        <td align="right"><?php echo number_format($job_recv_qnty,2,'.',''); ?></td>
                                        <td align="right"><?php echo number_format($job_recv_bl_qnty,2,'.',''); ?></td>
                                        <td align="right"><?php echo number_format($job_dyed_bl_qnty,2,'.',''); ?></td>
                                        <td colspan="2">&nbsp;</td>
									</tr>
								<?
									$job_req_qnty=0;
									$job_delivery_qnty=0;
									$job_grey_bl_qnty=0;
									$job_process_loss_qnty=0;
									$job_color_req_qnty=0;
									$job_recv_qnty=0;
									$job_recv_bl_qnty=0;
									$job_dyed_bl_qnty=0;
								}
								?>
								<tr>
									<td colspan="15" bgcolor="#EEEEEE"><b><?php echo "Job No:- ".$row[csf('job_no')]."; Buyer:- ".$buyer_arr[$row[csf('buyer_name')]]."; Style Ref:- ".$row[csf('style_ref_no')]."; Order No:- ".$order_no."; Internal. Ref.:- ".$internal_ref;?></b></td>
								</tr>
								<?
								$job_array[$i]=$row[csf('job_no')];
							}

							foreach($colorArray as $color_id)
							{	
								$recv_qnty = $recv_arr[$row[csf('job_no')]][$row[csf('id')]][$color_id] - $dyedYarnRcvReturn[$row[csf('job_no')]][$row[csf('id')]][$color_id];

								$req_qnty=$color_req_array[$row[csf('job_no')]][$row[csf('id')]][$color_id];

								$req_qnty=$req_qnty-($req_qnty*$txt_process_loss/100);

								$recv_bl_qnty=$req_qnty-$recv_qnty;
								?>
                                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $z; ?>','<? echo $bgcolor; ?>')" id="tr<? echo $z;?>">
                                	<?
									if($s==0)
									{
									?>
                                        <td width="35" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
                                        <td width="100" rowspan="<? echo $row_span; ?>"><p><a href="javascript:generate_trim_report('show_trim_booking_report','<? echo $row[csf('ydw_no')]; ?>',<? echo $cbo_company_name; ?>,<? echo $row[csf('id')]; ?>)"><? echo $row[csf('ydw_no')]; ?></a></p></td>
                                        <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $workOrderType; ?></p></td>
                                        <td width="100" rowspan="<? echo $row_span; ?>"><p><? echo $booking_arr[$row[csf('job_no')]]; ?></p></td>
                                        <td width="80" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('qnty')],2,'.',''); ?></td>
                                        <td width="80" align="right" rowspan="<? echo $row_span; ?>"><a href='#report_details' onClick="openmypage('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $color_id?>',0,'issue_popup');"><? echo number_format($delivery_qnty,2,'.',''); ?></a></td>
                                        <td width="80" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($grey_balance,2,'.',''); ?></td>
                                        <td width="90" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($process_loss_qnty,2,'.','');  ?></td>
                                    	<?
										$total_req_qnty+=$row[csf('qnty')];
										$tot_delivery_qnty+=$delivery_qnty;
										$total_grey_bl_qnty+=$grey_balance;
										$total_process_loss_qnty+=$process_loss_qnty;

										$job_req_qnty+=$row[csf('qnty')];
										$job_delivery_qnty+=$delivery_qnty;
										$job_grey_bl_qnty+=$grey_balance;
										$job_process_loss_qnty+=$process_loss_qnty;
									}
									?>
                                    <td width="80" align="right"><? echo number_format($req_qnty,2,'.','');  ?></td>
                                    <td width="70"><p><? echo $color_array[$color_id]; ?>&nbsp;</p></td>
                                    <td width="80" align="right">
                                    	<?
                                    	if($recv_qnty>0)
                                    	{
                                    		?>
                                    			<a href='#report_details' onClick="openmypage('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $color_id; ?>','<? echo '0'; ?>','receive_popup','1,4');"><? echo number_format($recv_qnty,2,'.','');  ?></a>
                                    		<?
                                    	}else{                                   		
                                    		echo number_format($recv_qnty,2,'.','');
                                    	}
                                    	?> 
                                    </td>
                                    <td width="80" align="right"><? echo number_format($recv_bl_qnty,2,'.','');  ?></td>
                                    <?
									if($s==0)
									{
										?>
                                        <td width="80" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($dyed_balance,2,'.','');  ?></td>
                                        <td width="70" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($actual_process_loss,2,'.',''); ?></td>
                                        <td rowspan="<? echo $row_span; ?>"><p>
                                        <?
                                        if($row[csf('pay_mode')]==4 || $row[csf('pay_mode')]==5)
                                        {
                                         	echo $company_arr[$row[csf('supplier_id')]]; 
                                        }
                                        else
                                        {
                                        	echo $supplier_arr[$row[csf('supplier_id')]]; 
                                        }
                                        ?>
                                        </p>
                                        </td>
                                    	<?
										$total_dyed_bl_qnty+=$dyed_balance;
										$job_dyed_bl_qnty+=$dyed_balance;
									}
									?>
                                </tr>
								<?
								$total_color_req_qnty+=$req_qnty;
								$job_color_req_qnty+=$req_qnty;
								$total_recv_qnty+=$recv_qnty;
								$job_recv_qnty+=$recv_qnty;
								$job_recv_bl_qnty+=$recv_bl_qnty;
								$tot_recv_bl_qnty+=$recv_bl_qnty;

								$z++;
								$s++;
							}
							$i++;
						}

						if(count($main_query_result)>0)
						{
						?>
                            <tr bgcolor="#CCCCCC">
                                <td colspan="4" align="right"><b>Job Total</b></td>
                                <td align="right"><?php echo number_format($job_req_qnty,2,'.',''); ?></td>
                                <td align="right"><?php echo number_format($job_delivery_qnty,2,'.',''); ?></td>
                                <td align="right"><?php echo number_format($job_grey_bl_qnty,2,'.',''); ?></td>
                                <td align="right"><?php echo number_format($job_process_loss_qnty,2,'.',''); ?></td>
                                <td align="right"><?php echo number_format($job_color_req_qnty,2,'.',''); ?></td>
                                <td>&nbsp;</th>
                                <td align="right"><?php echo number_format($job_recv_qnty,2,'.',''); ?></td>
                                <td align="right"><?php echo number_format($job_recv_bl_qnty,2,'.',''); ?></td>
                                <td align="right"><?php echo number_format($job_dyed_bl_qnty,2,'.',''); ?></td>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                        <?
						}
					?>
					<tfoot>
						<th colspan="4" align="right">Total</th>
						<th align="right"><?php echo number_format($total_req_qnty,2,'.',''); ?>&nbsp;</th>
						<th align="right"><?php echo number_format($tot_delivery_qnty,2,'.',''); ?>&nbsp;</th>
						<th align="right"><?php echo number_format($total_grey_bl_qnty,2,'.',''); ?>&nbsp;</th>
						<th align="right"><?php echo number_format($total_process_loss_qnty,2,'.',''); ?>&nbsp;</th>
                        <th align="right"><?php echo number_format($total_color_req_qnty,2,'.',''); ?>&nbsp;</th>
                        <th>&nbsp;</th>
						<th align="right"><?php echo number_format($total_recv_qnty,2,'.',''); ?>&nbsp;</th>
                        <th align="right"><?php echo number_format($tot_recv_bl_qnty,2,'.',''); ?>&nbsp;</th>
						<th align="right"><?php echo number_format($total_dyed_bl_qnty,2,'.',''); ?>&nbsp;</th>
						<th colspan="2">&nbsp;</th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	else if($type==2)
	{	
		//$db_type=0;
		if($db_type==0)
		{
			$sql_main="SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, c.id, c.ydw_no, c.supplier_id,c.is_short,group_concat(distinct(b.product_id)) as product_id,sum(b.yarn_wo_qty) as qnty,c.pay_mode from wo_po_details_master a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where a.id=b.job_no_id and b.mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form not in (94,42,114) and a.company_name=$cbo_company_name $buyer_id_cond $job_no_cond $year_cond $date_cond  $supplier_id_cond $search_cond $search_cond $wo_type_cond $internal_ref_job_cond group by a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, c.id, c.ydw_no, c.supplier_id,c.is_short,c.pay_mode order by a.job_no,c.id";
		}
		else if($db_type==2)
		{
			$sql_main="SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, c.id, c.ydw_no, c.supplier_id,c.is_short,listagg(b.product_id,',') within group (order by b.product_id) as product_id, sum(b.yarn_wo_qty) as qnty,c.pay_mode from wo_po_details_master a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where a.id=b.job_no_id and b.mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.entry_form not in (94,42,114) and c.status_active=1 and c.is_deleted=0 and a.company_name=$cbo_company_name $buyer_id_cond $job_no_cond $year_cond $date_cond $supplier_id_cond $search_cond $search_cond $wo_type_cond $internal_ref_job_cond group by a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, c.id, c.ydw_no, c.supplier_id,c.is_short,c.pay_mode order by a.job_no,c.id";
		}

		$main_query_result = sql_select($sql_main);

		if(count($main_query_result)>0)
		{
			foreach($main_query_result as $row)
			{
				$job_no_arr[] = "'".$row[csf('job_no')]."'";
				$work_order_id_arr[] = $row[csf('id')];

				if($row[csf('product_id')]!="")
				{
					$grey_product_id_arr[] = $row[csf('product_id')];
				}
				
				//$BookingColorArray[$row[csf('id')]].=$row[csf('yarn_color')].",";
				//$color_req_array[$row[csf('job_no')]][$row[csf('id')]][$row[csf('yarn_color')]]=$row[csf('qnty')];	
			}

			$job_no_string = implode(',',array_unique($job_no_arr));
			$work_order_ids_string = implode(',',array_unique($work_order_id_arr));
			$grey_product_id_string = implode(',',array_unique($grey_product_id_arr));
		}
		else
		{
			echo "<br><center><span style='color:red; font-size:20px; font-weight:bolder;'>Data Not Fond.</span></center>";
			die();
		}


		if($job_no_string!="")
		{
			if($db_type==0)
		    {
				$order_sql = "SELECT job_no_mst, group_concat(distinct(po_number)) as order_no,group_concat(distinct(grouping)) as internal_ref from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst in($job_no_string) group by job_no_mst";

				$order_resutl = sql_select($order_sql);
				foreach ($order_resutl as $row) {
					$order_arr[$row[csf('job_no_mst')]]['order_no'] = $row[csf('order_no')];
					$order_arr[$row[csf('job_no_mst')]]['internal_ref'] = $row[csf('internal_ref')];
				}

				$booking_arr = return_library_array( "select job_no,group_concat(distinct(booking_no)) as booking_no from wo_booking_mst where status_active=1 and is_deleted=0 and job_no in($job_no_string) group by job_no", "job_no", "booking_no" );
			}
		 	else if($db_type==2)
		    {
				$order_sql = "SELECT job_no_mst,listagg(po_number,',') within group (order by po_number) as order_no,listagg(grouping,',') within group (order by po_number) as internal_ref from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst in($job_no_string) group by job_no_mst";

				$order_resutl = sql_select($order_sql);
				foreach ($order_resutl as $row) {
					$order_arr[$row[csf('job_no_mst')]]['order_no'] = $row[csf('order_no')];
					$order_arr[$row[csf('job_no_mst')]]['internal_ref'] = $row[csf('internal_ref')];
				}

				$booking_arr = return_library_array( "select job_no,listagg(booking_no,',') within group (order by booking_no) as booking_no from wo_booking_mst where status_active=1 and is_deleted=0 and job_no in($job_no_string) group by job_no", "job_no", "booking_no" );				
			}
		}

		if($job_no_string!="" && $work_order_ids_string!="")
		{	

			$job_arr =return_library_array( "select id, job_no_mst from wo_po_break_down where status_active=1 and job_no_mst in($job_no_string) ",'id','job_no_mst');

			if($db_type==0)
		   	{
		   		if($grey_product_id_string!="")
		   		{
		   			$prodidCond = " and product_id in($grey_product_id_string) ";
		   		}

		   		$BookingColorArray=return_library_array("select mst_id, group_concat(distinct(yarn_color)) as color from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0 and mst_id in($work_order_ids_string) and job_no in($job_no_string) $prodidCond group by mst_id",'mst_id','color');
				
		   	}
	  		else if($db_type==2)
		   	{
		   		if($grey_product_id_string!="")
		   		{
		   			$prodidCond = " and product_id in($grey_product_id_string) ";					
				}

				$BookingColorArray=return_library_array("select mst_id, listagg(yarn_color,',') within group (order by yarn_color) as color from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0  and mst_id in($work_order_ids_string) and job_no in($job_no_string) $prodidCond group by mst_id",'mst_id','color');
		   	}

			$job_no_string_cond = "and a.job_no in($job_no_string)";
			$work_order_ids_string_cond = "and b.booking_id in($work_order_ids_string)";
			
			if($grey_product_id_string_cond!="")
			{
				$grey_product_id_string_cond = "and a.prod_id in($grey_product_id_string)";
			}			

			$grey_issue_sql = "select b.id, a.job_no, a.dyeing_color_id, a.cons_quantity as issue_qnty, b.booking_id from inv_transaction a, inv_issue_master b where a.mst_id=b.id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=2 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and a.transaction_type=2 $work_order_ids_string_cond $job_no_string_cond $grey_product_id_string_cond $date_trans_cond ";

			$issueDataArr = sql_select($grey_issue_sql);

			$issue_arr=array();
			foreach($issueDataArr as $row)
			{
				$issue_arr[$row[csf('job_no')]][$row[csf('booking_id')]] += $row[csf('issue_qnty')];
				$color_wise_qnty_array[$row[csf('job_no')]][$row[csf('booking_id')]][$row[csf('dyeing_color_id')]]+=$row[csf('issue_qnty')];
				$issue_id_arr[] = $row[csf('id')];
			} 
		}

		$issue_id_string = implode(',',array_unique($issue_id_arr));
		
		if($issue_id_string!="")
		{
			//if($grey_product_id_string_cond!="")
			//{
		       $issueRet_arr_sql= "SELECT c.id as trans_id, c.quantity as issue_ret_qnty, b.booking_id, d.job_no_mst from inv_transaction a, inv_receive_master b, order_wise_pro_details c, wo_po_break_down d where a.mst_id = b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and b.entry_form = 9 and a.item_category=1 and a.transaction_type=4 and b.receive_basis=1 and a.company_id = $cbo_company_name and a.issue_id in($issue_id_string) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and c.entry_form = 9 and c.trans_type = 4 $work_order_ids_string_cond $grey_product_id_string_cond $date_trans_cond";

				$issue_return_Data_Arr=sql_select($issueRet_arr_sql);

				$issue_ret_array=array(); $trans_check=array();
				foreach($issue_return_Data_Arr as $val)
				{
					if($trans_check[$val[csf("trans_id")]]=="")
					{
						$trans_check[$val[csf("trans_id")]]=$val[csf("trans_id")];
						$issue_ret_array[$val[csf('job_no_mst')]][$val[csf('booking_id')]]+=$val[csf('issue_ret_qnty')];
					}
				}
			//}	       
		}  

		if($job_no_string!="" && $work_order_ids_string!="")
		{			
			if($db_type==0)
			{
				$mrrRcvSql="SELECT a.job_no, sum(a.cons_quantity) as recv_qnty,b.id as mrr_rcv_id, b.booking_id, group_concat(distinct(c.id)) as product_id, c.color from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=1 and a.transaction_type=1 and a.job_no in($job_no_string) and b.booking_id in($work_order_ids_string) and c.dyed_type=1 $date_trans_cond group by a.job_no,b.id,b.booking_id, c.id,c.color"; //and a.prod_id=19184 
			}
			else if ($db_type==2) 
			{
				$mrrRcvSql="SELECT a.job_no, sum(a.cons_quantity) as recv_qnty,b.id as mrr_rcv_id, b.booking_id, listagg(c.id,',') within group (order by c.id) as product_id, c.color from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=1 and a.transaction_type=1 and a.job_no in($job_no_string) and b.booking_id in($work_order_ids_string) and c.dyed_type=1 $date_trans_cond group by a.job_no,b.id,b.booking_id, c.id,c.color"; //and a.prod_id=19184 
			}
			
			$mrrRcvDataArr=sql_select($mrrRcvSql);

			$mrrRcvJobBooking = array();
			$recv_arr=array();
			foreach($mrrRcvDataArr as $row)
			{
				$recv_arr[$row[csf('job_no')]][$row[csf('booking_id')]][$row[csf('color')]] += $row[csf('recv_qnty')];
				
				$receive_wo_job_arr[$row[csf("mrr_rcv_id")]]['job_no'] = $row[csf('job_no')];
				$receive_wo_job_arr[$row[csf('mrr_rcv_id')]]['work_order_id'] = $row[csf('booking_id')];

				$mrr_arr[$row[csf("mrr_rcv_id")]] = $row[csf("mrr_rcv_id")];
				$dyed_product_id_arr[] = $row[csf('product_id')];
			}
		}

		$mrr_ids = implode(",",array_filter($mrr_arr));
		$dyed_product_id_string = implode(',',array_unique($dyed_product_id_arr));

		if($mrr_ids!="")
        {        	
        	$sql_rcvrtn = "SELECT b.received_id,c.color, sum(a.cons_quantity) as rec_ret_qnty from inv_transaction a, inv_issue_master b, product_details_master c where b.id = a.mst_id and a.prod_id = c.id and b.entry_form = 8 and a.item_category = 1 and a.transaction_type = 3 and a.company_id = $cbo_company_name and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and b.received_id in ($mrr_ids) $date_trans_cond group by b.received_id,c.color";

        	$recRetDataArr=sql_select($sql_rcvrtn);

        	foreach($recRetDataArr as $row)
			{	
				$job_no = $receive_wo_job_arr[$row[csf("received_id")]]['job_no'];
				$work_order_id = $receive_wo_job_arr[$row[csf('received_id')]]['work_order_id'];
				
				$dyedYarnRcvReturn[$job_no][$work_order_id][$row[csf('color')]]+=$row[csf('rec_ret_qnty')];
				$jobwise_dyed_recRet_arr[$job_no]+=$row[csf('rec_ret_qnty')];
			}
        }
		//echo "<pre>";
		//print_r($dyedRecRetArr);
		//Issue Nos From Receive Mrr
		if($dyed_product_id_string!="")
		{
			$issue_sql = sql_select("select a.id,b.cons_quantity, d.id as receive_id 
				from  inv_issue_master a, inv_transaction b, inv_mrr_wise_issue_details c, inv_receive_master d, inv_transaction e where a.id = b.mst_id and b.id = c.issue_trans_id and c.recv_trans_id = e.id and d.id = e.mst_id and d.id in ($mrr_ids) and b.prod_id in ($dyed_product_id_string) and a.entry_form = 3 and c.entry_form = 3 and d.item_category=1 and b.status_active = 1 and b.is_deleted = 0");

			foreach ($issue_sql as $val)
			{
				$issue_id_arr[$val[csf("id")]] = $val[csf("id")];
				$receive_id_by_issu_id[$val[csf("id")]] = $val[csf("receive_id")];
			}

			$issue_ids = implode(",", array_filter($issue_id_arr));

			if($issue_ids)
			{
				// $Issue Return From Issue
				if($dyed_product_id_string!="")
				{
					$issue_ret_sql = sql_select("select a.id,a.issue_id,b.cons_quantity from  inv_receive_master a, inv_transaction b
						where a.id = b.mst_id and a.entry_form =9 and b.item_category = 1
						and b.status_active = 1 and b.is_deleted = 0 and a.issue_id in ($issue_ids) and b.prod_id in ($dyed_product_id_string)");

					foreach ($issue_ret_sql as $val)
					{
						$issue_id_by_issue_rtn[$val[csf("id")]] = $val[csf("issue_id")];
						$issue_ret_ids[$val[csf("id")]] = $val[csf("id")];
					}
					unset($issue_ret_sql);
				}
				
				
				$issue_ret_ids = implode(",", array_filter($issue_ret_ids));				
				
				if($issue_ret_ids)
				{
					if($dyed_product_id_string!="")
					{
						$rcvReturnFromIssueRetArr = sql_select("SELECT b.received_id,c.color, sum(a.cons_quantity) as rec_ret_qnty from inv_transaction a, inv_issue_master b, product_details_master c where b.id = a.mst_id and a.prod_id = c.id and b.entry_form = 8 and a.item_category = 1 and a.transaction_type = 3 and a.company_id = $cbo_company_name and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and b.received_id in ($issue_ret_ids) and a.prod_id in ($dyed_product_id_string) $date_trans_cond group by b.received_id,c.color");

						foreach ($rcvReturnFromIssueRetArr as $row)
						{
							$issueId = $issue_id_by_issue_rtn[$row[csf("received_id")]];
							$receive_id = $receive_id_by_issu_id[$issueId];

							$job_no = $receive_wo_job_arr[$receive_id]['job_no'];
							$work_order_id = $receive_wo_job_arr[$receive_id]['work_order_id'];
						
							$dyedYarnRcvReturn[$job_no][$work_order_id][$row[csf('color')]]+=$row[csf('rec_ret_qnty')];
							
							$jobwise_dyed_recRet_arr[$job_no]+=$row[csf('rec_ret_qnty')];
						}
					}
					
				}
			}
		}
		

		
		
		ob_start();
		?>
		<fieldset style="width:1970px;">
			<table cellpadding="0" cellspacing="0" width="1970">
				<tr>
				   <td align="center" width="100%" colspan="17" style="font-size:16px"><strong><? echo "Color Wise Dyed Yarn Report"; ?></strong></td>
				</tr>
				<tr>
				   <td align="center" width="100%" colspan="17" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr>
			</table>
			<table width="1952" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="40" rowspan="2">SL</th>
						<th width="100" rowspan="2">Job No</th>
						<th width="80" rowspan="2">Buyer Name</th>
						<th width="170" rowspan="2">Order No</th>
						<th width="170" rowspan="2">Internal Ref.</th>
						<th width="110" rowspan="2">Style No</th>
						<th width="120" rowspan="2">WO No</th>
						<th width="50" rowspan="2">WO Type</th>
						<th width="100" rowspan="2">Booking No</th>
						<th width="400" colspan="4">Grey Yarn</th>
                        <th width="100" rowspan="2">Color</th>
						<th width="400" colspan="4">Dyed Yarn</th>
						<th rowspan="2">Party Name</th>
					</tr>
					<tr>
						<th width="100">Total Required</th>
						<th width="100">Delivery</th>
						<th width="100">Balance</th>
                        <th width="100">Grey (Color-wise) Issue Qty.</th>
						<th width="100">Order Qty. (Less <? echo $txt_process_loss; ?>% process loss)</th>
						<th width="100">Received Qty.</th>
						<th width="100">Balance</th>
						<th width="100">Actual Process Loss(%)</th>
					</tr>
				</thead>
			</table>
			<div style="width:1970px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="1952" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?					
						$i=1; $z=1; $total_req_qnty=0; $tot_delivery_qnty=0; 
						$total_grey_bl_qnty=0; $total_process_loss_qnty=0; 
						$total_recv_qnty=0; $total_dyed_bl_qnty=0;
						foreach($main_query_result as $row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$delivery_qnty=$issue_arr[$row[csf('job_no')]][$row[csf('id')]] - $issue_ret_array[$row[csf('job_no')]][$row[csf('id')]]; 

							$grey_balance=$row[csf('qnty')]-$delivery_qnty;

							$colorArray=array_unique(explode(",",  chop($BookingColorArray[$row[csf('id')]],",") ));
							
							if($color_wise_qnty_array[$row[csf('job_no')]][$row[csf('id')]][0]>0)
							{
								array_unshift($colorArray, "0");
							}

							if($row[csf('is_short')]==1)
							{
								$workOrderType = "Short";
							}else{
								$workOrderType = "Main";
							}

							$order_no  = implode(",",array_unique(explode(",", $order_arr[$row[csf('job_no')]]['order_no'])));
							$internal_ref  = implode(",",array_unique(explode(",", trim($order_arr[$row[csf('job_no')]]['internal_ref']))));

							$s=0; 
							$row_span=count($colorArray);
							foreach($colorArray as $color_id)
							{
								$grey_color_qnty=$color_wise_qnty_array[$row[csf('job_no')]][$row[csf('id')]][$color_id]-$issue_ret_array[$row[csf('job_no')]][$row[csf('id')]];
								
								$recv_qnty=$recv_arr[$row[csf('job_no')]][$row[csf('id')]][$color_id] - $dyedYarnRcvReturn[$row[csf('job_no')]][$row[csf('id')]][$color_id]; 

								$process_loss_qnty=$grey_color_qnty-($grey_color_qnty*$txt_process_loss/100);
								$dyed_balance=$process_loss_qnty-$recv_qnty;
								$actual_process_loss=($grey_color_qnty-$recv_qnty)/$grey_color_qnty*100;

								?>
								<tr valign="middle" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $z; ?>','<? echo $bgcolor; ?>')" id="tr<? echo $z;?>">
                                	<?
									if($s==0)
									{
									?>
                                        <td width="40" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
                                        <td width="100" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
                                        <td width="80" rowspan="<? echo $row_span; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                                        <td width="170" rowspan="<? echo $row_span; ?>"><p><? echo $order_no; ?>&nbsp;</p></td>
                                        <td width="170" rowspan="<? echo $row_span; ?>"><p><? echo $internal_ref; ?>&nbsp;</p></td>
                                        <td width="110" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
                                        <td width="120" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('ydw_no')]; ?></p></td>
                                         <td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $workOrderType; ?></p></td>
                                        <td width="100" rowspan="<? echo $row_span; ?>"><p><? echo $booking_arr[$row[csf('job_no')]]; ?></p></td>
                                        <td width="100" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('qnty')],2,'.',''); ?></td>
                                        <td width="100" align="right" rowspan="<? echo $row_span; ?>"><a href='#report_details' onClick="openmypage('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo '0'; ?>','<? echo '0'; ?>','issue_popup');"><? echo number_format($delivery_qnty,2,'.',''); ?></a></td>
                                        <td width="100" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($grey_balance,2,'.',''); ?></td>
                                    <?
										$total_req_qnty+=$row[csf('qnty')];
										$tot_delivery_qnty+=$delivery_qnty;
										$total_grey_bl_qnty+=$grey_balance;
									}
									?>
									<td width="100" align="right"><? echo number_format($grey_color_qnty,2,'.',''); ?></td>
									<td width="100" align="center"><? if($color_id==0) echo "Select"; else echo $color_array[$color_id]; ?>&nbsp;</td>
									<td width="100" align="right"><? echo number_format($process_loss_qnty,2,'.','');  ?></td>
									<td width="100" align="right">	

										<?
										if($recv_qnty>0)
										{
											?>
											<a href='#report_details' onClick="openmypage('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $color_id; ?>','<? echo '0' ; ?>','receive_popup','1,4');">
											<? echo number_format($recv_qnty,2,'.','');  ?>
											</a> 
											<?
										}else{
											echo number_format($recv_qnty,2,'.',''); 
										}
										?>																		
									</td>
									<td width="100" align="right"><? echo number_format($dyed_balance,2,'.','');  ?></td>
									<td width="100" align="right"><? echo number_format($actual_process_loss,2,'.',''); ?></td>
                                    <?
									if($s==0)
									{
									?>
										<td rowspan="<? echo $row_span; ?>"><p>
										<?
                                        if($row[csf('pay_mode')]==4 || $row[csf('pay_mode')]==5)
                                        {
                                         	echo $company_arr[$row[csf('supplier_id')]]; 
                                        }
                                        else
                                        {
                                        	echo $supplier_arr[$row[csf('supplier_id')]]; 
                                        }
                                        ?>
                                        </p>
										</td>
                                    <?
									}
									?>
								</tr>
								<?
								$total_color_wise_qnty+=$grey_color_qnty;
								$total_process_loss_qnty+=$process_loss_qnty;
								$total_recv_qnty+=$recv_qnty;
								$total_dyed_bl_qnty+=$dyed_balance;

								$s++;
								$z++;
							}
							$i++;
						}
					?>
					<tfoot>
						<th colspan="9" align="right">Total</th>
						<th align="right"><?php echo number_format($total_req_qnty,2); ?>&nbsp;</th>
						<th align="right"><?php echo number_format($tot_delivery_qnty,2); ?>&nbsp;</th>
						<th align="right"><?php echo number_format($total_grey_bl_qnty,2); ?>&nbsp;</th>
                        <th align="right"><?php echo number_format($total_color_wise_qnty,2); ?>&nbsp;</th>
                        <th>&nbsp;</th>
						<th align="right"><?php echo number_format($total_process_loss_qnty,2); ?>&nbsp;</th>
						<th align="right"><?php echo number_format($total_recv_qnty,2); ?>&nbsp;</th>
						<th align="right"><?php echo number_format($total_dyed_bl_qnty,2); ?>&nbsp;</th>
						<th colspan="2">&nbsp;</th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	else if($type==3)
	{
		$color_prod_arr=return_library_array( "select id, color from product_details_master",'id','color');
		$job_arr=return_library_array( "select id, job_no_mst from wo_po_break_down",'id','job_no_mst');
		$brand_name_arr=return_library_array( "select id, brand_name from  lib_brand",'id','brand_name');
		$company_short_arr=return_library_array( "select id, company_short_name from  lib_company",'id','company_short_name');

		if($db_type==0)
		{
			$sql_main="SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, c.id as ID , c.ydw_no, c.supplier_id,c.is_short,c.booking_date,e.pi_number, e.pi_date, g.lc_number, g.lc_date, group_concat(distinct(b.product_id)) as product_id, sum(b.yarn_wo_qty) as qnty 
			from wo_po_details_master a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c
			where  a.id=b.job_no_id and b.mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form not in (94,42,114)  and a.company_name=$cbo_company_name $buyer_id_cond $job_no_cond $year_cond $date_cond $supplier_id_cond $search_cond $search_cond $wo_type_cond $internal_ref_job_cond 
			group by a.id, c.id  as prod_id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, c.ydw_no, c.supplier_id,c.is_short,c.booking_date
			order by a.job_no,c.id";
		}
		else if($db_type==2)
		{
			$sql_main="SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, c.id as ID, c.ydw_no, c.supplier_id,c.is_short,c.booking_date, listagg(b.product_id,',') within group (order by b.product_id) as product_id, sum(b.yarn_wo_qty) as qnty 
			from wo_po_details_master a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c
			where a.id=b.job_no_id and b.mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.entry_form not in (94,42,114) and a.company_name=$cbo_company_name $buyer_id_cond $job_no_cond $year_cond $date_cond $supplier_id_cond $search_cond $search_cond $wo_type_cond $internal_ref_job_cond 
			group by a.id, c.id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, c.ydw_no, c.supplier_id,c.is_short,c.booking_date
			order by a.job_no,c.id";
		}
         // echo $sql_main;

		 $main_query_result = sql_select($sql_main);

		if(count($main_query_result)>0)
		{
			foreach($main_query_result as $row)
			{  
		      $all_id[$row['ID']]=$row['ID'];

				$job_no_arr[] = "'".$row[csf('job_no')]."'";
				$work_order_id_arr[] = $row[csf('id')];

				if($row[csf('product_id')]!="")
				{
					$grey_product_id_arr[] = $row[csf('product_id')];	
				}
			}

			$job_no_string = implode(',',array_unique($job_no_arr));
			$work_order_ids_string = implode(',',array_unique($work_order_id_arr));
			$grey_product_id_string = implode(',',array_unique($grey_product_id_arr));
		}
		else
		{
			echo "<br><center><span style='color:red; font-size:20px; font-weight:bolder;'>Data Not Fond.</span></center>";
			die();
		}
		$all_prod_in=where_con_using_array($all_id,0,'d.work_order_id ');
		$sql_data="SELECT d.work_order_id , e.pi_number, e.pi_date, g.lc_number, g.lc_date 
		from com_pi_item_details d, com_pi_master_details e, com_btb_lc_pi f, com_btb_lc_master_details g where d.pi_id=e.id and e.id=f.pi_id and g.id=f.com_btb_lc_master_details_id and d.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 and d.is_deleted=0 and e.is_deleted=0 and f.is_deleted=0 and g.is_deleted=0 and e.item_category_id=24 $all_prod_in";
		 
		$tem_data=array();
		$data_sql=sql_select($sql_data);

		foreach($data_sql as $row){
			$tem_data[$row[csf("work_order_id")]]["pi_number"] .= $row[csf("pi_number")].",";
			$tem_data[$row[csf("work_order_id")]]["pi_date"] = $row[csf("pi_date")];
			$tem_data[$row[csf("work_order_id")]]["lc_date"] = $row[csf("lc_date")];
			$tem_data[$row[csf("work_order_id")]]["lc_number"] .= $row[csf("lc_number")].",";
		}

		if($job_no_string!="")
		{
			if($db_type==0)
		    {
				$order_sql = "SELECT job_no_mst, group_concat(distinct(po_number)) as order_no,group_concat(distinct(grouping)) as internal_ref from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst in($job_no_string) group by job_no_mst";

				$order_resutl = sql_select($order_sql);
				foreach ($order_resutl as $row) {
					$order_arr[$row[csf('job_no_mst')]]['order_no'] = $row[csf('order_no')];
					$order_arr[$row[csf('job_no_mst')]]['internal_ref'] = $row[csf('internal_ref')];
				}

				$booking_arr = return_library_array( "select job_no,group_concat(distinct(booking_no)) as booking_no from wo_booking_mst where status_active=1 and is_deleted=0 and job_no in($job_no_string) group by job_no", "job_no", "booking_no" );
			}
		 	else if($db_type==2)
		    {
				$order_sql = "SELECT job_no_mst,listagg(po_number,',') within group (order by po_number) as order_no,listagg(grouping,',') within group (order by po_number) as internal_ref from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst in($job_no_string) group by job_no_mst";

				$order_resutl = sql_select($order_sql);
				foreach ($order_resutl as $row) {
					$order_arr[$row[csf('job_no_mst')]]['order_no'] = $row[csf('order_no')];
					$order_arr[$row[csf('job_no_mst')]]['internal_ref'] = $row[csf('internal_ref')];
				}

				$booking_arr = return_library_array( "select job_no,listagg(booking_no,',') within group (order by booking_no) as booking_no from wo_booking_mst where status_active=1 and is_deleted=0 and job_no in($job_no_string) group by job_no", "job_no", "booking_no" );				
			}
		}

		if($job_no_string!="" && $work_order_ids_string!="")
		{	
			if($db_type==0)
		   	{
		   		if($grey_product_id_string!="")
		   		{
		   			$prodidCond = " and product_id in($grey_product_id_string)";
		   		}

		   		$BookingColorArray=return_library_array("select mst_id, group_concat(distinct(yarn_color)) as color from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0 and mst_id in($work_order_ids_string) and job_no in($job_no_string) $prodidCond group by mst_id",'mst_id','color');
				
		   	}
	  		else if($db_type==2)
		   	{
		   		if($grey_product_id_string!="")
		   		{
		   			$prodidCond = " and product_id in($grey_product_id_string)";
		   			$grey_product_id_string_cond = "and a.prod_id in($grey_product_id_string)";

				}

				$BookingColorArray=return_library_array("select mst_id, listagg(yarn_color,',') within group (order by yarn_color) as color from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0 and mst_id in($work_order_ids_string) and job_no in($job_no_string) $prodidCond group by mst_id",'mst_id','color');
		   	}

			$job_no_string_cond = "and a.job_no in($job_no_string)";
			$work_order_ids_string_cond = "and b.booking_id in($work_order_ids_string)";
			
			//if($grey_product_id_string_cond!="")
		   //{
				$grey_issue_sql = "select b.id, a.job_no,a.dyeing_color_id,a.cons_quantity as issue_qnty, b.booking_id from inv_transaction a, inv_issue_master b where a.mst_id=b.id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=2 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.item_category=1 and a.transaction_type=2 $work_order_ids_string_cond $job_no_string_cond $grey_product_id_string_cond $date_trans_cond ";

				$issueDataArr = sql_select($grey_issue_sql);

				$issue_arr=array();
				$color_wise_qnty=array(); 
				foreach($issueDataArr as $row)
				{
					$issue_arr[$row[csf('job_no')]][$row[csf('booking_id')]] += $row[csf('issue_qnty')];
					$color_wise_qnty[$row[csf('job_no')]][$row[csf('booking_id')]][$row[csf('dyeing_color_id')]]+=$row[csf('issue_qnty')];
					$issue_id_arr[] = $row[csf('id')];
				} 
				unset($issueDataArr);

			//}


			$issue_id_string = implode(',',array_unique($issue_id_arr));
		
			if($issue_id_string!="") 
			{
				//if($grey_product_id_string_cond!="")
		   		//{
			     	$issueRet_arr_sql= "select c.id as trans_id,c.quantity as issue_ret_qnty, b.booking_id, d.job_no_mst,e.color,e.lot from inv_transaction a, inv_receive_master b, order_wise_pro_details c, wo_po_break_down d,product_details_master e where a.mst_id = b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and a.prod_id=e.id and b.entry_form = 9 and a.item_category=1 and a.transaction_type=4 and b.receive_basis=1 and a.company_id = $cbo_company_name and a.issue_id in($issue_id_string) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and c.entry_form = 9 and c.trans_type = 4 $work_order_ids_string_cond $grey_product_id_string_cond $date_trans_cond";		     	  

					$issue_return_Data_Arr=sql_select($issueRet_arr_sql);

					$issue_ret_delivery=array(); 
					$issue_ret_array=array(); 
					$trans_check=array();
					foreach($issue_return_Data_Arr as $val)
					{
						if($trans_check[$val[csf("trans_id")]]=="")
						{
							$trans_check[$val[csf("trans_id")]]=$val[csf("trans_id")];
							$issue_ret_delivery[$val[csf('job_no_mst')]][$val[csf('booking_id')]]+=$val[csf('issue_ret_qnty')];
							$issue_ret_array[$val[csf('job_no_mst')]][$val[csf('color')]][$val[csf('lot')]] += $val[csf('issue_ret_qnty')];

						}
					}
					unset($issue_return_Data_Arr);
				//}
			}  

			// ==  new recieve dyed yarn 
			$mrrRcvSql="select a.job_no,a.brand_id,sum(a.cons_quantity) as recv_qnty,b.id as mrr_rcv_id, b.booking_id,c.id as product_id, c.color,c.lot from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=1 and a.transaction_type=1 and a.job_no in($job_no_string) and b.booking_id in($work_order_ids_string) and c.dyed_type=1 $date_trans_cond group by a.job_no,a.brand_id,b.id,b.booking_id,c.id,c.color,c.lot"; //and a.prod_id=19184 
			
			$mrrRcvDataArr=sql_select($mrrRcvSql);

			$mrrRcvJobBooking = array();
			$recv_arr=array(); 
			$color_recv_arr=array();
			foreach($mrrRcvDataArr as $row)
			{
				$recv_arr[$row[csf('job_no')]][$row[csf('booking_id')]][$row[csf('color')]][$row[csf('lot')]] += $row[csf('recv_qnty')];

				$color_recv_arr[$row[csf('job_no')]][$row[csf('booking_id')]][$row[csf('color')]]+=$row[csf('recv_qnty')];

				$mrrRcvJobBooking[$row[csf('mrr_rcv_id')]]['work_order_id']=$row[csf('booking_id')];
				$mrrRcvJobBooking[$row[csf('mrr_rcv_id')]]['job_no']=$row[csf('job_no')];

			
				$lot_arr[$row[csf('job_no')]][$row[csf('booking_id')]][$row[csf('color')]].=$row[csf('lot')].",";
				
				
				$brand_arr[$row[csf('job_no')]][$row[csf('booking_id')]][$row[csf('color')]]['brand_id']=$row[csf('brand_id')];

				$dyed_product_id_arr[] = $row[csf('product_id')];
				$total_rcv_id_arr[]= $row[csf('mrr_rcv_id')];
			}
			unset($mrrRcvDataArr);
			//==
		}
		
		$dyed_product_id_string = implode(',',array_unique($dyed_product_id_arr));

		if($dyed_product_id_string!="")
		{			
			$sql_issue_return_rcv ="select b.id as mrr_rcv_id,d.job_no_mst from inv_transaction a, inv_receive_master b,order_wise_pro_details c,wo_po_break_down d where a.mst_id=b.id and b.entry_form=9 and b.receive_basis=3 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.id=c.trans_id and c.po_breakdown_id = d.id and a.item_category=1 and a.transaction_type=4 and a.prod_id in($dyed_product_id_string) $date_trans_cond group by b.id,d.job_no_mst";  

			$issue_return_rcv=sql_select($sql_issue_return_rcv);

			foreach($issue_return_rcv as $row)
			{			
				$mrrRcvJobBooking[$row[csf('mrr_rcv_id')]]['work_order_id']=$row[csf('mrr_rcv_id')];
				$mrrRcvJobBooking[$row[csf('mrr_rcv_id')]]['job_no']=$row[csf('job_no_mst')];

				$total_rcv_id_arr[]= $row[csf('mrr_rcv_id')];	
			}

			unset($issue_return_rcv);
		}
		
        $total_received_ids = implode(',',array_unique($total_rcv_id_arr));	

        if($total_received_ids!="")
        {
        	if($dyed_product_id_string!="")
			{
	        	$sql_rcvrtn = "select b.received_id, sum(a.cons_quantity) as rec_ret_qnty,b.pi_id,c.color,c.lot from inv_transaction a, inv_issue_master b, product_details_master c where b.id = a.mst_id and a.prod_id = c.id and b.entry_form = 8 and a.item_category = 1 and a.transaction_type = 3 and a.company_id = $cbo_company_name and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and b.received_id in ($total_received_ids) and a.prod_id in($dyed_product_id_string) $date_trans_cond group by b.received_id,b.pi_id,c.color,c.lot";

	        	$recRetDataArr=sql_select($sql_rcvrtn);
	        	$colorRecRet_arr=array(); 
				$lotRecRet_arr=array();
				$recRet_arr=array(); 

	        	foreach($recRetDataArr as $rcv_return_row)
				{	
					$job_no = $mrrRcvJobBooking[$rcv_return_row[csf('received_id')]]['job_no'];

					$lotRecRet_arr[$job_no][$rcv_return_row[csf('color')]][$rcv_return_row[csf('lot')]]+=$rcv_return_row[csf('rec_ret_qnty')];
					$colorRecRet_arr[$job_no][$rcv_return_row[csf('color')]]+=$rcv_return_row[csf('rec_ret_qnty')];
					//$recRet_arr[$job_no][$work_order_id][$rcv_return_row[csf('color')]]+=$rcv_return_row[csf('rec_ret_qnty')];
				}
				unset($recRetDataArr);
			}
        }					
			
		if($dyed_product_id_string!="") // knitting issue 
		{
			$knitting_issue_sql= "select a.dyeing_color_id, b.prod_id, b.po_breakdown_id, sum(b.quantity) as issue_qnty, c.id as issue_id, c.knit_dye_source, c.knit_dye_company, c.booking_id,c.issue_purpose from inv_transaction a, order_wise_pro_details b, inv_issue_master c where a.mst_id=c.id and a.id=b.trans_id and b.entry_form=3 and a.company_id=$cbo_company_name and c.issue_purpose in(1,7,12,15,38,46,50,51) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=1 and a.transaction_type=2 and a.prod_id in($dyed_product_id_string) $date_trans_cond group by c.id,a.dyeing_color_id, b.prod_id, b.po_breakdown_id, c.knit_dye_source, c.knit_dye_company, c.booking_id,c.issue_purpose";
			//echo $knitting_issue_sql;

			$issueDatalotArr = sql_select($knitting_issue_sql);

			$lot_wise_qnty_array=array();
			foreach($issueDatalotArr as $row)
			{	
				if($row[csf('issue_purpose')]==1)
				{
					$lot_wise_qnty_array[$job_arr[$row[csf('po_breakdown_id')]]][$color_prod_arr[$row[csf('prod_id')]]][$lot_prod_arr[$row[csf('prod_id')]]]['lot']+=$row[csf('issue_qnty')];
					$dyed_yarn_issue_id[] = $row[csf('issue_id')];	
				}
				else
				{
					$lot_wise_qnty_array[$job_arr[$row[csf('po_breakdown_id')]]][$color_prod_arr[$row[csf('prod_id')]]][$lot_prod_arr[$row[csf('prod_id')]]]['services']+=$row[csf('issue_qnty')];
					$services_issue_ids[$job_arr[$row[csf('po_breakdown_id')]]][$color_prod_arr[$row[csf('prod_id')]]][$lot_prod_arr[$row[csf('prod_id')]]][] = $row[csf('issue_id')];

					$dyed_yarn_services_issue_id[] = $row[csf('issue_id')];	
				}

				$lot_wise_qnty_array[$job_arr[$row[csf('po_breakdown_id')]]][$color_prod_arr[$row[csf('prod_id')]]][$lot_prod_arr[$row[csf('prod_id')]]]['source']=$row[csf('knit_dye_source')];
				$lot_wise_qnty_array[$job_arr[$row[csf('po_breakdown_id')]]][$color_prod_arr[$row[csf('prod_id')]]][$lot_prod_arr[$row[csf('prod_id')]]]['party']=$row[csf('knit_dye_company')];
				
				if(str_replace("'","",$cbo_date_type)==2)
				{
					if($row[csf('booking_id')]!='' && $row[csf('booking_id')]!=0)
					{
						$trns_booking_arr[]=$row[csf('booking_id')];
					}
				}

				
			}

			unset($issueDatalotArr);

			$kning_issue_id_string = implode(',',array_unique($dyed_yarn_issue_id));
			$services_issue_id_string = implode(',',array_unique($dyed_yarn_services_issue_id));

			if($kning_issue_id_string!="")
			{
				$issueRetKnitingDataArr=sql_select("select a.prod_id,sum(c.quantity) as issue_return,b.booking_no,e.job_no,f.color,f.lot from inv_transaction a, inv_receive_master b, order_wise_pro_details c,wo_po_break_down d, wo_po_details_master e,product_details_master f where a.mst_id =b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and d.job_no_mst = e.job_no and f.id =a.prod_id and b.entry_form = 9 and a.item_category = 1 and a.transaction_type= 4 and a.prod_id in($dyed_product_id_string) and b.issue_id in($kning_issue_id_string) group by a.prod_id,b.booking_no,e.job_no,f.color,f.lot");

				$knitting_issure_return_arr = array();
				foreach($issueRetKnitingDataArr as $row)
				{
					$knitting_issure_return_arr[$row[csf('job_no')]][$row[csf('color')]][$row[csf('lot')]] += $row[csf('issue_return')];
				}
				unset($issueRetKnitingDataArr);

				if ($db_type == 0) $grp_conct="group_concat(distinct(b.id)) as issue_rtn_id";
				else $grp_conct="LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as issue_rtn_id";

				$servicesIsueRtnArr=sql_select("select a.prod_id,sum(c.quantity) as issue_return,$grp_conct,b.booking_no, e.job_no,f.color,f.lot from inv_transaction a, inv_receive_master b, order_wise_pro_details c,wo_po_break_down d, wo_po_details_master e,product_details_master f where a.mst_id =b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and d.job_no_mst = e.job_no and f.id =a.prod_id and b.entry_form = 9 and a.item_category = 1 and a.transaction_type= 4 and a.prod_id in($dyed_product_id_string) and b.issue_id in($services_issue_id_string) group by a.prod_id,b.booking_no,e.job_no,f.color,f.lot");

				$services_issure_return_arr = array();
				foreach($servicesIsueRtnArr as $row)
				{
					$services_issure_return_arr[$row[csf('job_no')]][$row[csf('color')]][$row[csf('lot')]]['rtn_qty'] += $row[csf('issue_return')];
					$services_issure_return_ids[$row[csf('job_no')]][$row[csf('color')]][$row[csf('lot')]][] = $row[csf('issue_rtn_id')];
				}
			}
		}		
		ob_start();
		?>
		<fieldset style="width:2580px;">
			<table cellpadding="0" cellspacing="0" width="1090">
				<tr>
				   <td align="center" width="100%" colspan="22" style="font-size:16px"><strong><? echo "Lot Wise Dyed Yarn Report"; ?></strong></td>
				</tr>
				<tr>
				   <td align="center" width="100%" colspan="22" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr>
			</table>
			<table width="2552" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="40" rowspan="2">SL</th>
						<th width="100" rowspan="2">Job No</th>
						<th width="80" rowspan="2">Buyer Name</th>
						<th width="110" rowspan="2">Style No</th>
						<th width="170" rowspan="2">Order No</th>
						<th width="170" rowspan="2">Internal Ref.</th>
                        <th width="120" rowspan="2">WO No</th>
                        <th width="70" rowspan="2">WO DATE</th>
                        <th width="70" rowspan="2">PI No</th>
                        <th width="70" rowspan="2">PI DATE</th>
                        <th width="70" rowspan="2">BTB No</th>
                        <th width="70" rowspan="2">BTB DATE</th>
                        <th width="50" rowspan="2">WO Type</th>
						<th width="400" colspan="4">Grey Yarn</th>
                        <th width="100" rowspan="2">Color</th>
						<th width="500" colspan="5">Dyed Yarn</th>
                        <th width="400" colspan="4">YD Delivery for Knitting & Services</th>
					</tr>
					<tr>
						<th width="100">Total Required</th>
						<th width="100">Delivery</th>
						<th width="100">Balance</th>
                        <th width="100">Grey (Color-wise) Issue Qty.</th>
						<th width="100">WO Qty.</th>
                        <th width="100">Brand</th>
                        <th width="100">Lot/Batch</th>
						<th width="100">Received Qty.</th>
						<th width="100">Color Total Rec. Balance</th>
						<th width="100">Issue to Service</th>
						<th width="100">Issue To Knitting</th>
                        <th width="100">Issue Balance</th>
                        <th>Knitting Party</th>
					</tr>
				</thead>
			</table>
			<div style="width:2570px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="2552" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?						
					$sqlCol=sql_select("select a.job_no, c.ydw_no, b.yarn_color, c.booking_date, sum(b.yarn_wo_qty) as qnty from wo_po_details_master a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where a.id=b.job_no_id and b.mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_name=$cbo_company_name $buyer_id_cond $job_no_cond group by a.job_no, c.ydw_no, b.yarn_color, c.booking_date");
                    // echo "select a.job_no, c.ydw_no, b.yarn_color, c.booking_date, sum(b.yarn_wo_qty) as qnty from wo_po_details_master a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where a.id=b.job_no_id and b.mst_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_name=$cbo_company_name $buyer_id_cond $job_no_cond group by a.job_no, c.ydw_no, b.yarn_color, c.booking_date";
					$wo_qty_arr=array();
				//	echo $sqlCol;
					foreach($sqlCol as $row)
					{
						$wo_qty_arr[$row[csf('job_no')]][$row[csf('ydw_no')]][$row[csf('yarn_color')]] = $row[csf('qnty')];
					}

					$i=1; $z=1;  $total_req_qnty=0; $tot_delivery_qnty=0; $total_grey_bl_qnty=0; $total_process_loss_qnty=0; $total_recv_qnty=0; $total_dyed_bl_qnty=0;

					$rowspan_array=array();

					foreach($main_query_result as $row)
					{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						if( $row[csf('is_short')]==1 )
						{
							$workOrderType = "Short";
						}else{
							$workOrderType = "Main";
						}

						$order_no  = implode(",",array_unique(explode(",", $order_arr[$row[csf('job_no')]]['order_no'])));
						$internal_ref  = implode(",",array_unique(explode(",", trim($order_arr[$row[csf('job_no')]]['internal_ref']))));

						$delivery_qnty=$issue_arr[$row[csf('job_no')]][$row[csf('id')]] - $issue_ret_delivery[$row[csf('job_no')]][$row[csf('id')]];
						$grey_balance=$row[csf('qnty')]-$delivery_qnty;
						$col_rec_qty=array();
						$colorArray=array_unique(explode(",",$BookingColorArray[$row[csf('id')]]));
						if($color_wise_qnty_array[$row[csf('job_no')]][$row[csf('id')]][0]>0)
						{
							array_unshift($colorArray, "0"); 
						}
						 
						$rowspan_color_array=array();
						$s=0; $k=0; $row_span=count($colorArray); $count_lot=0;
						$x=0;
						foreach($colorArray as $color_id)
						{
							$lotspan=array_unique(explode(",", chop($lot_arr[$row[csf('job_no')]][$row[csf('id')]][$color_id],",") ));
							$count_lot=count($lotspan);
							foreach($lotspan as $lot_span)
							{ 
								$rowspan_array[$lot_span]+=1;
								$rowspan_color_array[$color_id][$x]+=1;
								$k++;
							}
							$x++;
						}
						$x=0;
						foreach($colorArray as $color_id)
						{
							$lot=array_unique(explode(",",chop($lot_arr[$row[csf('job_no')]][$row[csf('id')]][$color_id],"," ) ) );
							$lot_span=count($lot);
							//echo $lot_span;
							$brand_val=$brand_arr[$row[csf('job_no')]][$row[csf('id')]][$color_id]['brand_id'];
							$color_rec_qty=$color_recv_arr[$row[csf('job_no')]][$row[csf('id')]][$color_id] - $colorRecRet_arr[$row[csf('job_no')]][$color_id];
							$grey_color_qnty=$color_wise_qnty[$row[csf('job_no')]][$row[csf('id')]][$color_id]+$colorRecRet_arr[$row[csf('job_no')]][$color_id];
							$m=0; 

							$total_color_wise_qnty+=$grey_color_qnty;

							foreach($lot as $lot_val)
							{
								$issue_qnty_lot=$lot_wise_qnty_array[$row[csf('job_no')]][$color_id][$lot_val]['lot'] - $knitting_issure_return_arr[$row[csf('job_no')]][$color_id][$lot_val];

								$services_issue_qnty=$lot_wise_qnty_array[$row[csf('job_no')]][$color_id][$lot_val]['services'] - $services_issure_return_arr[$row[csf('job_no')]][$color_id][$lot_val]['rtn_qty'];	
								//print_r($issure_return_id_arr);
								$source=$lot_wise_qnty_array[$row[csf('job_no')]][$color_id][$lot_val]['source'];
								$party=$lot_wise_qnty_array[$row[csf('job_no')]][$color_id][$lot_val]['party'];
								$wo_qty_color=$wo_qty_arr[$row[csf('job_no')]][$row[csf('ydw_no')]][$color_id];
								//$process_loss_qnty=$grey_color_qnty-($grey_color_qnty*$txt_process_loss/100);
								$recv_qnty=$recv_arr[$row[csf('job_no')]][$row[csf('id')]][$color_id][$lot_val] - $lotRecRet_arr[$row[csf('job_no')]][$color_id][$lot_val];//+$issue_ret_array[$row[csf('job_no')]][$row[csf('id')]][$color_id][$lot_val];
								$col_rec_qty[$color_id]+=$recv_qnty;
								
								$rcv_qnty=$recv_arr[$row[csf('job_no')]][$row[csf('id')]][$color_id][$lot_val] - $lotRecRet_arr[$row[csf('job_no')]][$color_id][$lot_val];
								$iss_rtn_qnty=$issue_ret_array[$row[csf('job_no')]][$color_id][$lot_val];
								
								$dyed_balance=$wo_qty_color-$color_rec_qty;
								$actual_process_loss=($grey_color_qnty-$recv_qnty)/$grey_color_qnty*100;
								$knittinig_party="";
								if ($source==1)
								{
									$knittinig_party=$company_short_arr[$party];
								}
								else if($source==3)
								{
									$knittinig_party=$supplier_arr[$party];
								}
							
								?>
								<tr valign="middle" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $z; ?>','<? echo $bgcolor; ?>')" id="tr<? echo $z;?>">
                            	<?
								if($s==0)
								{
									?>
                                    <td width="40" rowspan="<? echo $k; ?>"><? echo $i; ?></td>
                                    <td width="100" rowspan="<? echo $k; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
                                    <td width="80" rowspan="<? echo $k; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                                    <td width="110" rowspan="<? echo $k; ?>"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
                                    <td width="170" rowspan="<? echo $k; ?>"><p><? echo $order_no; ?>&nbsp;</p></td>
                                    <td width="170" rowspan="<? echo $k; ?>"><p><? echo $internal_ref; ?>&nbsp;</p></td>
                                    <td width="120" rowspan="<? echo $k; ?>"><p><? echo $row[csf('ydw_no')]; ?></p></td>
                                    <td width="70" rowspan="<? echo $k; ?>"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>
                                    <td width="70" rowspan="<? echo $k; ?>"><p><? echo implode(', ',array_unique(explode(',', rtrim($tem_data[$row[csf("id")]]["pi_number"],','))));
									
									?></p></td>
                                    <td width="70" rowspan="<? echo $k; ?>"><p><? echo change_date_format($tem_data[$row[csf("id")]]["pi_date"]); ?></p></td>
                                    <td width="70" rowspan="<? echo $k; ?>"><p><? echo implode(', ',array_unique(explode(',', rtrim($tem_data[$row[csf("id")]]["lc_number"],',')))); ?></p></td>
                                    <td width="70" rowspan="<? echo $k; ?>"><p><? echo change_date_format($tem_data[$row[csf("id")]]["lc_date"]); ?></p></td>
                                    <td width="50" rowspan="<? echo $k; ?>"><p><? echo $wo_type_arr[$row[csf('is_short')]]; ?></p></td>
                                    <td width="100" align="right" rowspan="<? echo $k; ?>"><? echo number_format($row[csf('qnty')],2,'.',''); ?></td>
                                    <td width="100" align="right" rowspan="<? echo $k; ?>"><a href='#report_details' onClick="openmypage('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo '0'; ?>','<? echo '0'; ?>','issue_popup');"><? echo number_format($delivery_qnty,2,'.',''); ?></a></td>
                                    <td width="100" align="right" rowspan="<? echo $k; ?>"><? echo number_format($grey_balance,2,'.',''); ?></td>
                                <?
									$total_req_qnty+=$row[csf('qnty')];
									$tot_delivery_qnty+=$delivery_qnty;
									$total_grey_bl_qnty+=$grey_balance;
								}
								if($m==0)
								{
									?>
									<td width="100" align="right" rowspan="<? echo $rowspan_color_array[$color_id][$x]; ?>"><? echo number_format($grey_color_qnty,2,'.',''); ?>&nbsp;</td>
									<td width="100" align="center" rowspan="<? echo $rowspan_color_array[$color_id][$x]; ?>"><? if($color_id==0) echo "Select"; else echo $color_array[$color_id]; ?>&nbsp;</td>
									<td width="100" align="right" rowspan="<? echo $rowspan_color_array[$color_id][$x]; ?>"><? echo number_format($wo_qty_color,2,'.','');  ?></td>
                                    <td width="100" rowspan="<? echo $rowspan_color_array[$color_id][$x]; ?>"><? echo $brand_name_arr[$brand_val];  ?>&nbsp;</td>
									<?
									$total_wo_qty_color+=$wo_qty_color;
								}
								?>
                                <td width="100"><? echo $lot_val; ?>&nbsp;</td>

								<td width="100" align="right">
									<? 
									if($rcv_qnty>0)
									{
										?>
										<a href='#report_details' onClick="openmypage('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $color_id; ?>','<? echo $lot_val; ?>','receive_popup','1,0');"><? echo number_format($rcv_qnty,2,'.',''); ?></a> 

										<?
									}
									else
									{
										echo number_format($rcv_qnty,2,'.','');	
									}
									?>
								</td>

                                <?
                                if($m==0)
								{ 
								?>
                                   <td width="100" align="right" rowspan="<? echo $rowspan_color_array[$color_id][$x]; ?>"><? echo number_format($dyed_balance,2,'.','');  ?></td> 
                                <?
								}
								?>
								<td width="100">
									<? 
									if($services_issue_qnty>0)
									{
										$issue_ids =  implode(",", $services_issue_ids[$row[csf('job_no')]][$color_id][$lot_val]);
										$issue_rtn_ids = implode(",", $services_issure_return_ids[$row[csf('job_no')]][$color_id][$lot_val]);

										?>
										<a href='#report_details' onClick="openmypage('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $color_id; ?>','<? echo $lot_val; ?>','yarn_services_issue_popup','','<? echo $issue_ids; ?>','<? echo $issue_rtn_ids; ?>');"><? echo number_format($services_issue_qnty,2,'.',''); ?></a>
										<?
									}
									else
									{
										echo number_format($services_issue_qnty,2,'.','');
									}
									?>	
								</td>
								<td width="100" align="right">
									<? 
									if($issue_qnty_lot>0)
									{
										?>
										<a href='#report_details' onClick="openmypage('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $color_id; ?>','<? echo $lot_val; ?>','yd_issue_popup');"><? echo number_format($issue_qnty_lot,2,'.',''); ?></a>
										<?
									}
									else
									{
										echo number_format($issue_qnty_lot,2,'.','');
									}
									?>									
								</td>
                                
                                <td width="100" align="right"><? $issue_balance=$recv_qnty-($services_issue_qnty+$issue_qnty_lot); echo number_format($issue_balance,2,'.',''); ?></td>

                                <td><p><? echo $knittinig_party; ?></td>
								</tr>   
							<?  
							
							$total_recv_qnty+=$recv_qnty;
							$total_rcv_qnty+=$rcv_qnty;
							$total_iss_rtn_qnty+=$iss_rtn_qnty;
							$total_services_issue_qnty += $services_issue_qnty;
							$total_issue+=$issue_qnty_lot;
							$total_issue_balance+=$issue_balance;
							
							$m++;
							$s++;
							$z++;
						}
						$x++;
						$total_dyed_bl_qnty+=$dyed_balance;
					}
					$i++;
				}
				?>
				<tfoot>
					<th colspan="13" align="right">Total</th>
					<th align="right"><?php echo number_format($total_req_qnty,2); ?>&nbsp;</th>
					<th align="right"><?php echo number_format($tot_delivery_qnty,2); ?>&nbsp;</th>
					<th align="right"><?php echo number_format($total_grey_bl_qnty,2); ?>&nbsp;</th>
                    <th align="right"><?php echo number_format($total_color_wise_qnty,2); ?>&nbsp;</th>
                    <th>&nbsp;</th>
					<th align="right"><?php echo number_format($total_wo_qty_color,2); ?>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
					<th align="right"><?php echo number_format($total_recv_qnty,2); ?>&nbsp;</th>
					<th align="right"><?php echo number_format($total_dyed_bl_qnty,2); ?>&nbsp;</th>
					<th align="right"><?php echo number_format($total_services_issue_qnty
,2); ?></th>
					<th align="right"><?php echo number_format($total_issue,2); ?></th>
                    <th align="right"><?php echo number_format($total_issue_balance,2); ?></th>
                    <th>&nbsp;</th>
				</tfoot>
				</table>					
			</div>
		</fieldset>
		<?
	}

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
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');

	$issueDataArr=sql_select("select b.issue_number, b.issue_date, a.job_no, c.lot, c.yarn_count_id, sum(a.cons_quantity) as issue_qnty, b.booking_no from inv_transaction a, inv_issue_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and c.item_category_id=1 and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=2 and a.company_id=$companyID and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=2 and a.job_no='$job_no' and b.booking_id='$booking_id' group by b.issue_number, b.issue_date, a.job_no, b.booking_no, c.lot, c.yarn_count_id");
	?>
    <fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Issue Details</th>
                    </tr>
                	<tr>
                        <th width="20">Sl</th>
                        <th width="100">Issue Number</th>
                        <th width="90">Job No</th>
                        <th width="60">Issue Date</th>
                        <th width="100">Booking No</th>
                        <th width="60">Count</th>
                        <th width="60">Lot</th>
                        <th>Issue Qty</th>
                    </tr>
				</thead>
                <tbody>
                <? $i=1;
				foreach($issueDataArr as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="20"><p><? echo $i; ?></p></td>
                        <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $row[csf('job_no')]; ?></p></td>
                        <td width="60"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                        <td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td width="60"><p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf('issue_qnty')],2); ?></p></td>
                    </tr>
                    <?
					$tot_issue_qty+=$row[csf('issue_qnty')];
					$i++;
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total Issue</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issue_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
	<!--		<?
			$booking_array=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst",'id','ydw_no');
            $recRetDataArr=sql_select("select b.issue_number, b.issue_date, sum(a.cons_quantity) as recRet_qnty, c.booking_id, d.job_no from inv_transaction a, inv_issue_master b, inv_receive_master c, inv_transaction d where a.mst_id=b.id and b.entry_form=8 and a.company_id=$companyID and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=3 and b.received_id=c.id and c.id=d.mst_id and c.entry_form=1 and c.receive_basis=2 and c.receive_purpose=2 and d.item_category=1 and d.transaction_type=1 and d.job_no='$job_no' and c.booking_id='$booking_id' group by b.issue_number, b.issue_date, c.booking_id, d.job_no");
			?>
            <table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Receive Return Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="130">Rec. Ret. Number</th>
                        <th width="110">Job No</th>
                        <th width="75">Ret. Date</th>
                        <th width="130">Booking No</th>
                        <th>Ret. Qty</th>
                    </tr>
				</thead>
                <tbody>
                <? $k=1;
				foreach($recRetDataArr as $row)
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
                        <td width="30"><p><? echo $k; ?></p></td>
                        <td width="130"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
                        <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                        <td width="130" ><p><? echo $booking_array[$row[csf('booking_id')]]; ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf('recRet_qnty')],2); ?></p></td>
                    </tr>
                    <?
					$tot_recRet_qnty+=$row[csf('recRet_qnty')];
					$k++;
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right">Total Receive Return</td>
                        <td align="right">&nbsp;<? echo number_format($tot_recRet_qnty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table> -->
         <?
                    $sqlcond = "";
                    if ($companyID){
                    $sqlcond .= "  and a.company_id = $companyID ";
                    }
                    if ($booking_id) {
                    $sqlcond .= "  and b.booking_id = '$booking_id' ";
                    }
                    if ($job_no) {
                    $sqlcond .= "  and d.job_no_mst = '$job_no' ";
                    }

					//group by b.booking_id,d.job_no_mst, a.transaction_date, b.recv_number

                    $issueRetDataArr = sql_select("select c.id as trans_id, c.quantity as issue_ret_qnty, b.booking_id, d.job_no_mst, b.receive_date, b.recv_number, e.lot, e.yarn_count_id
                    from inv_transaction a, inv_receive_master b, order_wise_pro_details c, wo_po_break_down d, product_details_master e
                    where a.mst_id = b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and a.prod_id=e.id and e.item_category_id=1 and b.entry_form = 9 and a.item_category=1
                    and a.transaction_type=4 and b.receive_basis=1 and a.status_active=1 and a.is_deleted=0 $sqlcond
                    and b.status_active=1 and b.is_deleted=0 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and c.entry_form = 9
                    and c.trans_type = 4 ");


					$issue_return_data=array();
                    $trans_check=array();
					foreach($issueRetDataArr as $row)
					{
						$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["booking_id"]=$row[csf("booking_id")];
						$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["recv_number"]=$row[csf("recv_number")];
						$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["job_no_mst"]=$row[csf("job_no_mst")];
						$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["receive_date"]=$row[csf("receive_date")];
						$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["count"]=$row[csf("yarn_count_id")];
						$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["lot"]=$row[csf("lot")];
						if($trans_check[$row[csf("trans_id")]]=="")
						{
							$trans_check[$row[csf("trans_id")]]=$row[csf("trans_id")];
							$issue_return_data[$row[csf("recv_number")]][$row[csf("job_no_mst")]]["issueRet_qnty"]+=$row[csf("issue_ret_qnty")];
						}
					}


					?>
                    <table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Issue Return Details</th>
                    </tr>
                	<tr>
                        <th width="20">Sl</th>
                        <th width="100">Issue. Ret. Number</th>
                        <th width="90">Job No</th>
                        <th width="60">Ret. Date</th>
                        <th width="100">Booking No</th>
                        <th width="60">Count</th>
                        <th width="60">Lot</th>
                        <th>Ret. Qty</th>
                    </tr>
				</thead>
                <tbody>
                <? $k=1;
				foreach($issue_return_data as $rcv_num=>$rcv_data)
				{
					foreach($rcv_data as $job_no=>$row)
					{
						if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
							<td width="20"><p><? echo $k; ?></p></td>
							<td width="100"><p><? echo $row[('recv_number')]; ?></p></td>
							<td width="90"><p><? echo $row[('job_no_mst')]; ?></p></td>
							<td width="60"><p><? echo change_date_format($row[('receive_date')]); ?></p></td>
							<td width="100" ><p><? echo $booking_array[$row[('booking_id')]]; ?></p></td>
                            <td width="60"><p><? echo $count_arr[$row[('count')]]; ?></p></td>
                            <td width="60"><p><? echo $row[('lot')]; ?></p></td>
							<td align="right"><p><? echo number_format($row[('issueRet_qnty')],2); ?></p></td>
						</tr>
						<?
						$tot_issueRet_qnty+=$row[('issueRet_qnty')];
						$k++;
					}

				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total Issue Return</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issueRet_qnty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
          </div>
      </fieldset>
	<?
    exit();
}

if($action=="receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$trans_type=explode(",",$trans_type);
	$booking_array=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst",'id','ydw_no');

	if($lot) $lot_cond=" and c.lot='$lot'"; else $lot_cond=''; 

	//echo $lot_cond; die();
	?>
    <fieldset style="width:570px; margin-left:3px">
    <div id="scroll_body" align="center">
	    <?
		if($trans_type[0]==1)
		{
			$recv_sql="select b.recv_number, b.receive_date, a.job_no, sum(a.cons_quantity) as recv_qnty, b.booking_id, c.color,a.grey_quantity from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.company_id=$companyID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.item_category=1 and a.transaction_type=1 and a.job_no='$job_no' and b.booking_id='$booking_id'  and c.dyed_type=1 and c.color='$color' $lot_cond group by b.recv_number, b.receive_date, a.job_no, b.booking_id, c.color,a.grey_quantity";
			//and a.prod_id in( $product_ids )
			//echo $recv_sql; 
			$recvDataArr=sql_select($recv_sql);
			?>
	        <table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
	            <thead>
	                <tr>
	                    <th colspan="8">Receive Details</th>
	                </tr>
	                <tr>
	                    <th width="30">Sl</th>
	                    <th width="100">Receive Number</th>
	                    <th width="100">Job No</th>
	                    <th width="75">Receive Date</th>
	                    <th width="100">Booking No</th>
	                    <th width="80">Receive Qty</th>
	                    <th>Grey Yarn Qty</th>
	                </tr>
	            </thead>
	            <tbody>
	            <? $i=1;
	            foreach($recvDataArr as $row)
	            {
	                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                ?>
	                <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    <td width="30"><p><? echo $i; ?></p></td>
	                    <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                    <td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
	                    <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
	                    <td width="100" ><p><? echo $booking_array[$row[csf('booking_id')]]; ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($row[csf('recv_qnty')],2); ?></p></td>
	                    <td align="right"><p><? echo number_format($row[csf('grey_quantity')],2); ?></p></td>
	                </tr>
	                <?
	                $tot_recv_qnty+=$row[csf('recv_qnty')];
	                $tot_grey_qnty+=$row[csf('grey_quantity')];
	                $i++;
	            }
	            ?>
	            </tbody>
	            <tfoot>
	                <tr class="tbl_bottom">
	                    <td colspan="5" align="right">Total Receive</td>
	                    <td align="right">&nbsp;<? echo number_format($tot_recv_qnty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;<? echo number_format($tot_grey_qnty,2); ?>&nbsp;</td>
	                </tr>
	            </tfoot>
	        </table>
	        <?
		}

	    if($lot==0) $lot_cond=''; else $lot_cond=" and c.lot='$lot'";
	    $booking_array=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst where status_active=1 and is_deleted=0",'id','ydw_no');

	    if($booking_id!="")
	    {
		    $sql_issue_transec = sql_select("select b.mst_id,b.id,c.issue_trans_id,c.entry_form,d.id
			from  inv_receive_master a , inv_transaction b, inv_mrr_wise_issue_details c
			left join inv_transaction d on c.issue_trans_id=d.id and d.status_active=1 and d.transaction_type=2
			where a.id=b.mst_id and  b.id=c.recv_trans_id
			and a.booking_id = '$booking_id' and b.transaction_type=1 and a.entry_form=1 and c.entry_form=3
			and a.status_active=1 and b.status_active=1 and c.status_active=1
			and a.item_category=1 and b.transaction_type=1");

			foreach($sql_issue_transec as $trn_issue_row)
	        {
				$issue_trans_id .= $trn_issue_row[csf('issue_trans_id')].",";
			}

			$issue_trans_id = chop($issue_trans_id, " , ");
	    }

		if($issue_trans_id!="")
		{
			$issue_id_from_issue_rtn = sql_select("select a.id from inv_issue_master a,inv_transaction b where a.id=b.mst_id and b.id in($issue_trans_id) and a.entry_form=3 and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

			foreach($issue_id_from_issue_rtn as $issue_row)
	        {
				$issue_id .= $issue_row[csf('id')].",";
			}

			$issue_ids = chop($issue_id, " , ");
		}

		if($issue_ids!="")
		{
			$sql_issue_rtn_rcv = sql_select("select a.id as received_id  from inv_receive_master a,inv_transaction b where a.id=b.mst_id and a.issue_id in($issue_ids) and a.entry_form=9 and b.item_category=1 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

			foreach($sql_issue_rtn_rcv as $iss_rcv_row)
	        {
	        	$total_rcv_id[] = $iss_rcv_row[csf('received_id')];
	        }
		}

    	$mrr_rcv_id_sql = sql_select("select d.id as received_id from inv_receive_master d, inv_transaction e where d.id = e.mst_id and e.transaction_type = 1 and e.item_category = 1 and e.receive_basis = 2 and d.receive_purpose = 2 and d.booking_id = '$booking_id' and e.job_no = '$job_no' and d.entry_form = 1 and d.is_deleted = 0 and d.status_active = 1 and d.company_id = $companyID group by d.id");

    	foreach($mrr_rcv_id_sql as $rcv_row)
        {
        	$total_rcv_id[]= $rcv_row[csf('received_id')];
        }


        $total_received_ids = implode(",", $total_rcv_id);

        if($total_received_ids!="")
        {
        	$sql = "select b.issue_number, b.issue_date,b.received_id, sum(a.cons_quantity) as rec_ret_qnty,  c.color
        	from inv_transaction a, inv_issue_master b, product_details_master c
        	where b.id = a.mst_id and a.prod_id = c.id and b.entry_form = 8 and a.item_category = 1 and a.transaction_type = 3
        	and a.company_id = $companyID and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.color = '$color' $lot_cond
        	and b.received_id in ($total_received_ids)
        	group by b.issue_number, b.issue_date,b.received_id,c.color";

        	$recRetDataArr=sql_select($sql);
        }

        ?>
        <table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <tr>
                        <th colspan="9">Receive Return Details</th>
                    </tr>
                    <tr>
                        <th width="30">Sl</th>
                        <th width="130">Rec. Ret. Number</th>
                        <th width="110">Job No</th>
                        <th width="75">Ret. Date</th>
                        <th width="130">Booking No</th>
                        <th>Ret. Qty</th>
                    </tr>
                </thead>
            <tbody>
            <? $k=1;
                foreach($recRetDataArr as $row)
                {
                        if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
                <td width="30"><p><? echo $k; ?></p></td>
                <td width="130"><p><? echo $row[csf('issue_number')]; ?></p></td>
                <td width="110"><p><? echo $job_no; ?></p></td>
                <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                <td width="130" ><p><? echo $booking_array[$booking_id]; ?></p></td>
                <td align="right"><p><? echo number_format($row[csf('rec_ret_qnty')],2); ?></p></td>
                </tr>
                <?
                        $tot_recRet_qnty+=$row[csf('rec_ret_qnty')];
                        $k++;
                }
                ?>
            </tbody>
            <tfoot>
            	<tr class="tbl_bottom">
                	<td colspan="5" align="right">Total Receive Return</td>
                    <td align="right">&nbsp;<? echo number_format($tot_recRet_qnty,2); ?>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
	</div>
	</fieldset>
	<?
    exit();
}

if($action=="yd_issue_popup")//Issue/Return
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$booking_array=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst",'id','ydw_no');
	$po_id_arr=return_library_array( "select job_no_mst, id from wo_po_break_down",'job_no_mst','id');
	$po_id=$po_id_arr[$job_no];

	if($lot==0) $lot_cond=''; else $lot_cond=" and d.lot='$lot'";
	if($lot==0) $lotconds=''; else $lotconds=" and c.lot='$lot'";
	
    $issueDatalotSql = "select a.id as trans_id, e.job_no, c.quantity as cons_quantity, b.issue_number, b.issue_date, b.booking_no, a.requisition_no from inv_transaction a, inv_issue_master b, order_wise_pro_details c,  wo_po_break_down d, wo_po_details_master e, product_details_master f where a.mst_id =b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and d.job_id = e.id and b.entry_form = 3 and a.item_category = 1 and a.prod_id = f.id and a.transaction_type= 2 and f.item_category_id = 1 and b.issue_purpose = 1 and b.issue_basis in (1,3) and e.job_no = '$job_no' and f.color = '$color' and f.lot = '$lot' order by a.id";

     $issueDatalotArr= sql_select($issueDatalotSql);

	?>
    <fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Issue Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="130">Issue Number</th>
                        <th width="110">Job No</th>
                        <th width="75">Issue Date</th>
                        <th width="130">Booking / Requisition</th>
                        <th>Issue Qty</th>
                    </tr>
				</thead>
                <tbody>
                <? $i=1;$tot_issue_qnty=0;
				foreach($issueDatalotArr as $row)
				{
                    $booking_requisition = "";
                    if($row[csf('booking_no')]){
                        $booking_requisition = $row[csf('booking_no')];
                    }else if($row[csf('requisition_no')]){
                        $booking_requisition = $row[csf('requisition_no')];
                    }
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$rec_ret_qty=$lotRecRet_arr[$row[csf('job_no')]][$row[csf('color')]][$row[csf('lot')]];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="130"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
                        <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                        <td width="130" align="center"><p><? echo $booking_requisition; ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf('cons_quantity')],2); ?></p></td>
                    </tr>
                    <?
					$tot_issue_qnty+=$row[csf('cons_quantity')];
					$i++;
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right">Total Issue</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issue_qnty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br/>
			<?
            $lotconds = "";
            if($lot != ""){ $lotconds .= " and f.lot = '$lot'";}

			$booking_job_arr=return_library_array( "select mst_id, job_no from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0",'mst_id','job_no');

            $issueRetDataArr=sql_select("select a.id as trans_id, e.job_no, b.recv_number, b.receive_date, a.cons_quantity,b.booking_no from inv_transaction a, inv_receive_master b, order_wise_pro_details c,  wo_po_break_down d, wo_po_details_master e, product_details_master f where a.mst_id =b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and d.job_no_mst = e.job_no and b.entry_form = 9 and a.item_category = 1 and a.prod_id = f.id and a.transaction_type= 4 and f.item_category_id = 1 and f.color = '$color' $lotconds and e.job_no = '$job_no' order by a.id");
	 		?>

            <table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Issue Return Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="130">Issue. Ret. Number</th>
                        <th width="110">Job No</th>
                        <th width="75">Ret. Date</th>
                        <th width="130">Booking/Requisition</th>
                        <th>Ret. Qty</th>
                    </tr>
				</thead>
                <tbody>
                <? $k=1;
				foreach($issueRetDataArr as $row)
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
                        <td width="30"><p><? echo $k; ?></p></td>
                        <td width="130"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
                        <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                        <td width="130" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf('cons_quantity')],2); ?></p></td>
                    </tr>
                    <?
					$tot_issRet_qnty+=$row[csf('cons_quantity')];
					$k++;
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right">Total Issue Return</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issRet_qnty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
          </div>
      </fieldset>
	<?
    exit();
}


if($action=="yarn_services_issue_popup")//Issue/Return of services
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$booking_array=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst",'id','ydw_no');
	$po_id_arr=return_library_array( "select job_no_mst, id from wo_po_break_down",'job_no_mst','id');
	$po_id=$po_id_arr[$job_no];

	if($lot==0) $lot_cond=''; else $lot_cond=" and d.lot='$lot'";
	if($lot==0) $lotconds=''; else $lotconds=" and c.lot='$lot'";
	
    $issueDatalotSql = "select a.id as trans_id, e.job_no, a.cons_quantity, b.issue_number, b.issue_date, b.booking_no, a.requisition_no from inv_transaction a, inv_issue_master b, order_wise_pro_details c,  wo_po_break_down d, wo_po_details_master e, product_details_master f where a.mst_id =b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and d.job_no_mst = e.job_no and b.entry_form = 3 and a.item_category = 1 and a.prod_id = f.id and a.transaction_type= 2 and f.item_category_id = 1 and b.issue_purpose in (7,12,15,38,46,50,51) and b.issue_basis=1 and e.job_no = '$job_no' and f.color = '$color' and f.lot = '$lot' and b.id in ($issue_ids) order by a.id";

     $issueDatalotArr= sql_select($issueDatalotSql);

	?>
    <fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Issue Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="130">Issue Number</th>
                        <th width="110">Job No</th>
                        <th width="75">Issue Date</th>
                        <th width="130">Booking / Requisition</th>
                        <th>Issue Qty</th>
                    </tr>
				</thead>
                <tbody>
                <? $i=1;$tot_issue_qnty=0;
				foreach($issueDatalotArr as $row)
				{
                    $booking_requisition = "";
                    if($row[csf('booking_no')]){
                        $booking_requisition = $row[csf('booking_no')];
                    }else if($row[csf('requisition_no')]){
                        $booking_requisition = $row[csf('requisition_no')];
                    }
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//$rec_ret_qty=$lotRecRet_arr[$row[csf('job_no')]][$row[csf('color')]][$row[csf('lot')]];
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                        <td width="30"><p><? echo $i; ?></p></td>
                        <td width="130"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
                        <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                        <td width="130" align="center"><p><? echo $booking_requisition; ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf('cons_quantity')],2); ?></p></td>
                    </tr>
                    <?
					$tot_issue_qnty+=$row[csf('cons_quantity')];
					$i++;
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right">Total Issue</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issue_qnty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br/>
			<?
            $lotconds = "";
            if($lot != ""){ $lotconds .= " and f.lot = '$lot'";}

			$booking_job_arr=return_library_array( "select mst_id, job_no from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0",'mst_id','job_no');

            $issueRetDataArr=sql_select("select a.id as trans_id, e.job_no, b.recv_number, b.receive_date, a.cons_quantity,b.booking_no from inv_transaction a, inv_receive_master b, order_wise_pro_details c,  wo_po_break_down d, wo_po_details_master e, product_details_master f where a.mst_id =b.id and a.id = c.trans_id and c.po_breakdown_id = d.id and d.job_no_mst = e.job_no and b.entry_form = 9 and a.item_category = 1 and a.prod_id = f.id and a.transaction_type= 4 and f.item_category_id = 1 and f.color = '$color' $lotconds and e.job_no = '$job_no' and b.id in ($issue_rtn_ids) order by a.id");
	 		?>

            <table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Issue Return Details</th>
                    </tr>
                	<tr>
                        <th width="30">Sl</th>
                        <th width="130">Issue. Ret. Number</th>
                        <th width="110">Job No</th>
                        <th width="75">Ret. Date</th>
                        <th width="130">Booking/Requisition</th>
                        <th>Ret. Qty</th>
                    </tr>
				</thead>
                <tbody>
                <? $k=1;
				foreach($issueRetDataArr as $row)
				{
					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
                        <td width="30"><p><? echo $k; ?></p></td>
                        <td width="130"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
                        <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                        <td width="130" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf('cons_quantity')],2); ?></p></td>
                    </tr>
                    <?
					$tot_issRet_qnty+=$row[csf('cons_quantity')];
					$k++;
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="5" align="right">Total Issue Return</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issRet_qnty,2); ?>&nbsp;</td>
                    </tr>
                    <tr class="tbl_bottom">
                    	<td colspan="5" align="right">Grand Total Issue</td>
                        <td align="right">&nbsp;<? echo number_format($tot_issue_qnty-$tot_issRet_qnty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
          </div>
      </fieldset>
	<?
    exit();
}
?>
