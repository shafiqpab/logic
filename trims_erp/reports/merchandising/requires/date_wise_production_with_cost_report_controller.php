<?
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Trim;header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_buyer")
{ 
	list($company,$type)=explode("_",$data);
	if($type==1)
	{
		echo create_drop_down( "cbo_customer_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company, "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_customer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", "", "" );
	}	
	exit();	 
}

if ($action=="load_drop_down_location") {	
	echo create_drop_down( 'cbo_location_name', 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if($action=="load_drop_down_subsection")
{
	$data=explode('_',$data);
	if($data[0]==1) $subID='1,2,3,23';
	else if($data[0]==3) $subID='4,5,18';
	else if($data[0]==5) $subID='6,7,8,9,10,11,12,13,16,17,17,24';
	else if($data[0]==10) $subID='14,15';
	else if($data[0]==7) $subID='19,20,21,25,26';
	else if($data[0]==9) $subID='22';
	else $subID='0';
	//echo $data[0]."**".$subID;
	//echo create_drop_down( "cboembtype_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	echo create_drop_down( "cbo_sub_section_id", 100, $trims_sub_section,"",1, "-- Select Sub-Section --","","",0,$subID,'','','','','',"");
	exit();
}



if ($action=="order_popup")
{	
	echo load_html_head_contents("Order Search","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id)
	{
		//alert(booking_no); 
		document.getElementById('hidd_booking_data').value=id;
		parent.emailwindow.hide();
	}
	
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<tr>                	 
							<th width="120">W/O No</th>
							<th colspan="2" width="160">W/O Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" id="hidd_booking_data">
							</th>
						</tr>                                 
					</thead>
					<tr class="general">
						<td><input name="txt_order_number" id="txt_order_number" class="text_boxes" style="width:90px"></td>
						<td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From"></td>
						<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To"></td> 
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company;?>+'_'+<? echo $party_name;?>+'_'+<? echo $customer_source;?>, 'create_booking_search_list_view', 'search_div', 'date_wise_production_with_cost_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div id="search_div"></div>   
			</form>
		</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_booking_search_list_view")
{	
	$data=explode('_',$data);
	//print_r($data);
	
	if ($data[4]!=0) $company=" and a.company_id='$data[4]'";
	if ($data[4]!=0) $sample_company=" and c.company_id='$data[4]'";
	
	if($db_type==0)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date = "and a.booking_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $booking_date ="";

		if ($data[1]!="" &&  $data[2]!="") $sample_booking_date = "and c.booking_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $sample_booking_date ="";
		
	}
	else if($db_type==2)
	{
		if ($data[1]!="" &&  $data[2]!="") $booking_date = "and a.booking_date between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		

		if ($data[1]!="" &&  $data[2]!="") $sample_booking_date = "and c.booking_date between '".change_date_format($data[1], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'"; else $sample_booking_date ="";
		
	} 
	
	if ($data[0]!="") $woorder_cond=" and a.booking_no like '%$data[0]%' "; 
	if ($data[0]!="") $sample_woorder_cond=" and c.booking_no like '%$data[0]%' ";
	
	$sql= "SELECT a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id,1 as type from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type in(2,5) and a.status_active=1 and a.lock_another_process=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company $booking_date $woorder_cond group by a.id, a.booking_type, a.booking_no, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.job_no, a.booking_date, a.currency_id 
	UNION
	SELECT c.id, c.booking_type, c.booking_no, c.booking_no_prefix_num, c.company_id, c.buyer_id, c.job_no, c.booking_date, c.currency_id,2 as type from  wo_non_ord_samp_booking_mst c, wo_non_ord_samp_booking_dtls d where c.booking_no=d.booking_no and c.booking_type in(5) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sample_company $sample_booking_date $sample_woorder_cond group by c.id, c.booking_type, c.booking_no, c.booking_no_prefix_num, c.company_id, c.buyer_id, c.job_no, c.booking_date, c.currency_id";

	$data_array=sql_select($sql);
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="440" >
        <thead>
            <th width="30">SL</th>
            <th width="60">W/O No</th>
            <th width="70">W/O Date</th>
        </thead>
        </table>
        <div style="width:440px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="420" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('booking_no')].'_'.$row[csf('currency_id')].'_'.$row[csf('type')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60" align="center"><? echo $row[csf('booking_no')]; ?></td>
                    <td width="70"><? echo change_date_format($row[csf('booking_date')]); ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$hid_order_id=str_replace("'","", $hid_order_id);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_sub_section_id=str_replace("'","", $cbo_sub_section_id);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$report_type=str_replace("'","", $report_type);
	
	if($db_type==0)
	{
		$from_date=change_date_format($txt_date_from,'yyyy-mm-dd');
		$to_date=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$from_date=change_date_format($txt_date_from,'','',1);
		$to_date=change_date_format($txt_date_to,'','',1);
	}
	else
	{
		$from_date=""; $to_date="";
	}
	if($cbo_section_id){$where_con.=" and b.section ='$cbo_section_id'";} 
	if($cbo_sub_section_id){$where_con.=" and b.sub_section='$cbo_sub_section_id'";} 
	if($cbo_customer_source){$where_con.=" and d.within_group='$cbo_customer_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";} 
	if($cbo_location_name){$where_con.=" and a.location_id = $cbo_location_name";}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_name_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	 
	$machine_noArr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0","id","machine_no");
	$location_arr = return_library_array("select id, location_name from lib_location where status_active=1 and is_deleted=0","id","location_name");
	$orderRateArr = return_library_array("select id, rate from subcon_ord_breakdown where status_active=1 and is_deleted=0", 'id', 'rate');

	
	if($txt_date_from!="" and $txt_date_to!="")
	{	
		$where_con_prod_date.=" and a.production_date between '$txt_date_from' and '$txt_date_to'";
	}
	if($cbo_section_id){$where_con_prod.=" and d.section_id ='$cbo_section_id'";} 
	if($cbo_sub_section_id){$where_con_prod.=" and c.sub_section='$cbo_sub_section_id'";} 
	if($cbo_customer_source){$where_con_prod.=" and d.within_group='$cbo_customer_source'";} 
	if($cbo_customer_name){$where_con_prod.=" and a.party_id='$cbo_customer_name'";} 
	if($cbo_location_name){$where_con_prod.=" and a.location_id = $cbo_location_name";}
	
	if(trim($txt_order_no)!="")
	{
		//$sql_cond= " and d.order_no=$hid_order_id";
		//CustomerName LIKE 'a%';	 
		$where_con_prod="and d.order_no like '%$txt_order_no%'";
		$sql_order_no="and a.order_no like '%$txt_order_no%'";
	}
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1  and id in(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)" , "conversion_rate" ); 
	//echo $report_type;
	$width=1700;
	$width_second=2380;
	
	if ($report_type==1)
	{
		$trims_production_sql=" select a.job_id,a.production_date,a.received_id,d.section_id,b.qc_qty,c.conv_factor
		from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d
		where  a.id=b.mst_id and c.id=b.job_dtls_id and c.mst_id=d.id and a.entry_form=269 and d.entry_form=257 and a.company_id =$cbo_company_id and  a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $where_con_prod $where_con_prod_date";
		//echo $trims_order_sql; die;
		$result = sql_select($trims_production_sql);
	    $date_wise_production_arr=array(); $production_arr=array();
	    foreach($result as $row)
	    {
	    	$all_received_ids .=$row[csf('received_id')].',';
		}
		

		 $all_trims_production_sql=" SELECT a.job_id,a.production_date,a.received_id,d.section_id,b.qc_qty, b.uom, c.conv_factor,c.break_id,c.item_group_id
		from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d
		where  a.id=b.mst_id and c.id=b.job_dtls_id and c.mst_id=d.id and a.entry_form=269 and d.entry_form=257 and a.company_id =$cbo_company_id and  a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $where_con_prod ";
		//echo $trims_order_sql; die;
		$result_all_production = sql_select($all_trims_production_sql);
	    $date_wise_production_arr=array(); $production_arr=array();
	    foreach($result_all_production as $row)
	    {
	    	$qc_qty=$row[csf('qc_qty')]/$row[csf('conv_factor')];
			$order_uom=$row[csf('uom')];
			$breakIdArr=explode(',',$row[csf('break_id')]);
			
			if($txt_order_no!="" && $from_date!="" && $to_date!="")
			{
				
				if( (strtotime($row[csf('production_date')])>=strtotime($from_date)) && (strtotime($row[csf('production_date')])<=strtotime($to_date)) )
				{
					//$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$order_uom]['today_qc_qty'] +=$qc_qty;
					//$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$order_uom]['today_qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
					
					$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$order_uom]['today_qc_qty'] +=$qc_qty;
					$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$order_uom]['today_qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
					
				}
				
			}
			else if($txt_order_no!="")
			{
  				//$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$order_uom]['today_qc_qty'] +=$qc_qty;
				// $date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$order_uom]['today_qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
				 
				 $date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$order_uom]['today_qc_qty'] +=$qc_qty;
				 $date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$order_uom]['today_qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
				
				
			}
			else
			{
				
				if( (strtotime($row[csf('production_date')])>=strtotime($from_date)) && (strtotime($row[csf('production_date')])<=strtotime($to_date)) )
				{
					//$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$order_uom]['today_qc_qty'] +=$qc_qty;
					//$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$order_uom]['today_qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
					
					$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$order_uom]['today_qc_qty'] +=$qc_qty;
					$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$order_uom]['today_qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
					
				}
				
			}
			
			//$production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$order_uom]['qc_qty'] +=$qc_qty;
	   	 	//$production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$order_uom]['qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
			
			$production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$order_uom]['qc_qty'] +=$qc_qty;
	   	 	$production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$order_uom]['qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
	   	 	
		}

		$all_received_ids=array_unique(explode(",",chop($all_received_ids,',')));
		if(count($all_received_ids)>0){
			foreach($all_received_ids as $val)
			{
				$r_id2=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$val,555)");
			}
			//print_r($issue_item_arr);
			//$wo_ids=implode(",",array_unique(explode(",",(chop($wo_id,',')))));
			if($db_type==0)
			{
				if($r_id2)
				{
					mysql_query("COMMIT");  
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				//echo $r_id2; die;
				if($r_id2)
				{
					oci_commit($con);  
				}
			} 
			//$wo_cond=" and a.id in ($all_received_ids)";
		}

		if(count($result)>0)
		{
			/*  $order_sql="SELECT a.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,c.order_id,d.id as jobcard_id ,c.qnty,c.rate,c.amount,c.booked_qty,c.description,c.id as breakdown_id, a.trims_ref,b.item_group
			from  subcon_ord_dtls b, subcon_ord_breakdown c,subcon_ord_mst a 
			LEFT JOIN trims_job_card_mst d on a.id=d.received_id and d.status_active=1 and d.is_deleted=0
			where a.id=b.mst_id  and a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  and a.is_deleted=0 and d.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and c.status_active=1 and a.id in (select poid from tmp_poid where userid=$user_id and type=555) $sql_order_no $where_con $wo_cond"; 
			
			*/
			
			
			$order_sql="SELECT a.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,c.order_id,c.qnty,c.rate,c.amount,c.booked_qty,c.description,c.id as breakdown_id, a.trims_ref,b.item_group
			from  subcon_ord_dtls b, subcon_ord_breakdown c,subcon_ord_mst a  
			where a.id=b.mst_id  and a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  and a.is_deleted=0 and a.status_active=1   and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.id in (select poid from tmp_poid where userid=$user_id and type=555) $sql_order_no $where_con $wo_cond";   

			$order_sql_result = sql_select($order_sql);
			$order_array=array(); $all_job_ids='';
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('id')]][$row[csf('subcon_job')]][$row[csf('section')]][$row[csf('order_uom')]]['buyer_buyer'] .=$row[csf('buyer_buyer')].','; 
				$order_array[$row[csf('id')]][$row[csf('subcon_job')]][$row[csf('section')]][$row[csf('order_uom')]]['currency_id'] +=$row[csf('currency_id')];
				$order_array[$row[csf('id')]][$row[csf('subcon_job')]][$row[csf('section')]][$row[csf('order_uom')]]['qnty'] 		+=$row[csf('qnty')];
				$order_array[$row[csf('id')]][$row[csf('subcon_job')]][$row[csf('section')]][$row[csf('order_uom')]]['amount'] 		+=$row[csf('amount')];
				$order_array[$row[csf('id')]][$row[csf('subcon_job')]][$row[csf('section')]][$row[csf('order_uom')]]['exchange_rate'] =$row[csf('exchange_rate')];
				$order_array[$row[csf('id')]][$row[csf('subcon_job')]][$row[csf('section')]][$row[csf('order_uom')]]['cust_order_no'] =$row[csf('cust_order_no')];
				$order_array[$row[csf('id')]][$row[csf('subcon_job')]][$row[csf('section')]][$row[csf('order_uom')]]['jobcard_id'] =$row[csf('id')];
				$order_array[$row[csf('id')]][$row[csf('subcon_job')]][$row[csf('section')]][$row[csf('order_uom')]]['booked_uom'] =$row[csf('booked_uom')];
				$order_array[$row[csf('id')]][$row[csf('subcon_job')]][$row[csf('section')]][$row[csf('order_uom')]]['order_uom'] =$row[csf('order_uom')];
				$order_array[$row[csf('id')]][$row[csf('subcon_job')]][$row[csf('section')]][$row[csf('order_uom')]]['item_group'] =$row[csf('item_group')];
				$all_job_ids .=$row[csf('jobcard_id')].',';
			}
			$all_job_ids=implode(",",array_unique(explode(",",chop($all_job_ids,','))));
		}
		
		if($all_job_ids!=''){
			$job_card_cond=" and g.id in ($all_job_ids)";
			$job_card_condd=" and c.id in ($all_job_ids)";
		}


		  $issue_sql = "SELECT distinct g.received_id,e.issue_date,f.cons_quantity as issue_qty, f.cons_rate,f.prod_id,e.id as issue_id ,h.uom
		from trims_raw_mat_requisition_mst a,trims_raw_mat_requisition_dtls b,inv_issue_master e,inv_transaction f,trims_job_card_mst g,trims_job_card_dtls h
		where a.entry_form in (427,501) and a.job_id=g.id and a.id = b.mst_id and a.job_no = g.trims_job and a.id = e.req_id and e.id=f.mst_id and b.product_id = f.prod_id and b.mst_id = e.req_id and b.job_no = g.trims_job and e.req_no=a.requisition_no  and g.id=h.mst_id   and f.transaction_type = 2 and e.issue_basis = 7 and e.entry_form = 265 and a.status_active = 1 and b.status_active = 1 and e.status_active = 1 and f.status_active = 1 and g.status_active = 1  and g.received_id in (select poid from tmp_poid where userid=$user_id and type=555)
		union all
		SELECT distinct c.received_id,a.issue_date, b.cons_quantity as issue_qty, b.cons_rate,b.prod_id,a.id as issue_id,d.uom
		from inv_issue_master a, inv_transaction b, trims_job_card_mst c , trims_job_card_dtls d 
		where a.id=b.mst_id and a.req_id=c.id  and c.id=d.mst_id and b.transaction_type=2 and a.issue_basis=15 and a.entry_form=265  and c.received_id in (select poid from tmp_poid where userid=$user_id and type=555)";   

		$iss_data_array=sql_select($issue_sql);
		$issue_qty_arr=array(); $date_wise_issue_qty_arr=array();
		foreach($iss_data_array as $row)
		{
			$issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['qty']+=$row[csf("issue_qty")];
			$issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['amount']+=$row[csf("issue_qty")]*$row[csf("cons_rate")];
			$issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['issue_id'].=$row[csf("issue_id")].',';
			$issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['prod_id'].=$row[csf("prod_id")].',';
			if($from_date!="" and $to_date!="")
			{
				
				if( (strtotime($row[csf('issue_date')])>=strtotime($from_date)) && (strtotime($row[csf('issue_date')])<=strtotime($to_date)) )
				{
			    	$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['today_qty']+=$row[csf("issue_qty")];
					$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['today_amount']+=$row[csf("issue_qty")]*$row[csf("cons_rate")];
					$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['issue_id'].=$row[csf("issue_id")].',';
					$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['prod_id'].=$row[csf("prod_id")].',';
					//echo "dsfds";
			    }
			}
			else
			{
		    	$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['today_qty']+=$row[csf("issue_qty")];
				$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['today_amount']+=$row[csf("issue_qty")]*$row[csf("cons_rate")];
				$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['issue_id'].=$row[csf("issue_id")].',';
				$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['prod_id'].=$row[csf("prod_id")].',';
		    }
		    //echo "bbbb";
		}
		/*echo '<pre>';
		print_r($date_wise_issue_qty_arr);
		die;*/
		/*echo $issue_return_sql = "SELECT c.received_id,a.issue_date, b.cons_quantity as issue_qty, b.cons_rate,b.prod_id from inv_issue_master a, inv_transaction b, trims_job_card_mst c, inv_receive_master d where a.req_id=c.id and b.transaction_type=2 and a.issue_basis=15 and a.entry_form=265 and d.id=b.mst_id and a.id=b.issue_id $job_card_condd and c.received_id in (select poid from tmp_poid where userid=$user_id and type=555)";*/

		$issue_return_sql = "SELECT a.id as issue_id,a.issue_date, b.cons_quantity as rtn_qty, b.cons_rate,b.prod_id,d.receive_date from inv_issue_master a, inv_transaction b, inv_receive_master d where b.transaction_type=4 and a.entry_form=265 and d.id=b.mst_id and a.id=b.issue_id and d.entry_form=266  and a.status_active = 1 and b.status_active = 1 and d.status_active = 1";
		$iss_rtn_data_array=sql_select($issue_return_sql);
		$issue_rtn_qty_arr=array();
		foreach($iss_rtn_data_array as $row){
			$issue_rtn_qty_arr[$row[csf("issue_id")]]['qty']+=$row[csf("rtn_qty")];
			$issue_rtn_qty_arr[$row[csf("issue_id")]]['amount']+=$row[csf("rtn_qty")]*$row[csf("cons_rate")];
			if($from_date!="" and $to_date!="")
			{

				if( (strtotime($row[csf('receive_date')])>=strtotime($from_date)) && (strtotime($row[csf('receive_date')])<=strtotime($to_date)) )
				//if(($row[csf('receive_date')]>$txt_date_from) && ($txt_date_to < $row[csf('receive_date')]))
				{
					//echo 'vdghdo';
			    	$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]]['today_qty']+=$row[csf("rtn_qty")];
					$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]]['today_amount']+=$row[csf("rtn_qty")]*$row[csf("cons_rate")];
				}
		    }else{
		    	$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]]['today_qty']+=$row[csf("rtn_qty")];
				$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]]['today_amount']+=$row[csf("rtn_qty")]*$row[csf("cons_rate")];
		    }
		}
		$r_id3=execute_query("delete from tmp_poid where userid=$user_id and type=555");
		if($db_type==0)
		{
			if($r_id3)
			{
				mysql_query("COMMIT");  
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($r_id3)
			{
				oci_commit($con);  
			}
		}
		disconnect($con);	
		/*echo "<pre>";
		print_r($date_wise_issue_rnn_qty_arr);*/
		ob_start();
		?>	
		<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
	    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
					<thead class="form_caption" >
						<tr>
							<td colspan="17" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
						</tr>
						<tr>
							<td colspan="17" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
						</tr>
						<? if($txt_date_from!='' && $txt_date_to!='' ){
							?>
							<tr>
								<td colspan="17" align="center" style="font-size:14px; font-weight:bold">
								<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?></td>
							</tr>
							<?
						} ?>
						
					</thead>
				</table>
	            <table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead>	
                    	<? $content.=ob_get_flush(); ?>		
						<tr>
							<th colspan="17" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
						</tr>
                        <? ob_start();?>
						<tr>
							<th width="35">SL</th>
							<th width="100">Section</th>
							<th width="100">Work Order No</th>
							<th width="120">Buyer Name</th>
							<th width="100">WO Qty.</th>
							<th width="60">WO UOM</th>
							<th width="80">Avg. Unit Price ($)</th>
							<th width="100">WO Value ($)</th>
							<th width="100">Today Prod in WO UOM</th>
							<th width="100">Today Prod. Value [$]</th>
							<th width="100">Today Material Issued Cost [$]</th>
							<th width="80">Today Margin %</th>
							<th width="100">Cumm Prod in WO UOM</th>
							<th width="100">Cumm. Prod. Value [$]</th>
							<th width="100">Total Material Cost ($)</th>
							<th width="80">Gross. Margin %	</th>
							<th >Balance Qty. in WO UOM</th>
						</tr>
					</thead>
				</table>
	        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
	        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<? echo $width;?>" rules="all" align="left">
	            <? 
					$i=1;
					$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
					//$order_array[$row[csf('id')]][$row[csf('subcon_job')]][$row[csf('section')]][$row[csf('order_uom')]]
					foreach($order_array as $wo_id=>$wo_id_arr)
					{
						foreach($wo_id_arr as $subcon_job=>$subcon_job_data)
						{
							foreach($subcon_job_data as $section_id=>$section_id_data)
							{
								foreach($section_id_data as $order_uom=>$row)
								{
									if ($i % 2 == 0){ $bgcolor = "#E9F3FF";}else{ $bgcolor = "#FFFFFF";}
									
									
									//echo $order_uom."jk";
									if($order_uom*1==$row['booked_uom']*1)
									{
									  $order_uom=$order_uom;
									}
									else
									{
										$order_uom=$row['booked_uom']*1;
									}
									
									
									$buyer_buyer=implode(",",array_unique(explode(",",chop($row['buyer_buyer'],','))));
									$qnty=$row['qnty'];
									$amount=$row['amount'];
									$rate=$amount/$qnty;
									//$cum_qc_qty=$production_arr[$wo_id][$section_id][$row['item_group']][$order_uom]['qc_qty'];
									$cum_qc_qty=$production_arr[$wo_id][$section_id][$order_uom]['qc_qty'];
									// $cum_qc_val=$cum_qc_qty*$rate;
									//$cum_qc_val=$production_arr[$wo_id][$section_id][$row['item_group']][$order_uom]['qc_value'];
									$cum_qc_val=$production_arr[$wo_id][$section_id][$order_uom]['qc_value'];
									$today=date("d-m-Y");
									$jobcard_id=$row['jobcard_id'];
									$exchange_rate=$row['exchange_rate'];
									//echo $today;
									//echo $today.'=='.$wo_id.'=='.$section_id;
									//$today_qc_qty=$date_wise_production_arr[$wo_id][$section_id][$row['item_group']][$order_uom]['today_qc_qty'];
									$today_qc_qty=$date_wise_production_arr[$wo_id][$section_id][$order_uom]['today_qc_qty'];
									//$today_qc_val=($today_qc_qty*$rate)/$exchange_rate;
									// $today_qc_val=($today_qc_qty*$rate);
									//$today_qc_val=$date_wise_production_arr[$wo_id][$section_id][$row['item_group']][$order_uom]['today_qc_value'];
									$today_qc_val=$date_wise_production_arr[$wo_id][$section_id][$order_uom]['today_qc_value'];
									
									$issue_id=$issue_qty_arr[$wo_id][$row['order_uom']]['issue_id'];
									$issue_ids=array_unique(explode(",",chop($issue_id,',')));
									$popup_issue_id=implode(",",$issue_ids);

									$rtn_qty=$rtn_val=$today_rtn_qty=$today_rtn_val='';
									foreach ($issue_ids as $key => $value)
									 {
										$rtn_qty+=$issue_rtn_qty_arr[$value]['qty'];
										$rtn_val+=$issue_rtn_qty_arr[$value]['amount'];
									}

									$cum_issue_qty=$issue_qty_arr[$wo_id][$row['order_uom']]['qty']-$rtn_qty;
									$cum_issue_val=($issue_qty_arr[$wo_id][$row['order_uom']]['amount']-$rtn_val)/$exchange_rate;
									//$cum_issue_val=($cum_issue_qty*$rate)/$exchange_rate;
									//$cum_issue_val=($cum_issue_qty*$rate);
									$today_issue_id=$date_wise_issue_qty_arr[$wo_id][$row['order_uom']]['issue_id'];
									$today_issue_ids=array_unique(explode(",",chop($today_issue_id,',')));
									foreach ($today_issue_ids as $key => $value)
									{
										$today_rtn_qty+=$date_wise_issue_rnn_qty_arr[$value]['today_qty'];
										$today_rtn_val+=$date_wise_issue_rnn_qty_arr[$value]['today_amount'];
									}
									//echo $today_rtn_qty;
									$today_issue_qty=$date_wise_issue_qty_arr[$wo_id][$row['order_uom']]['today_qty']-$today_rtn_qty;
									$today_issue_cost=($date_wise_issue_qty_arr[$wo_id][$row['order_uom']]['today_amount']-$today_rtn_val)/$exchange_rate;
									//$today_issue_cost=($today_issue_qty*$rate)/$exchange_rate;
									//$today_issue_cost=($today_issue_qty*$rate);
									$today_margin_percent =(($today_qc_val-$today_issue_cost)*100)/$today_qc_val;
									$gross_margin_percent =(($cum_qc_val-$cum_issue_val)*100)/$cum_qc_val;
									$balance_qty=$qnty-$cum_qc_qty;
									$balance_val=$amount-$cum_qc_val;
									$pop_order_uom=$row['order_uom'];
									$item_group=$row['item_group'];
									
									 
								if(number_format($today_qc_qty,2)>0)
								{
								?>
				                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				                	<td width="35"  align="center"><? echo $i;?></td> 
				                    <td width="100" style="word-break: break-all;" align="left"><? echo $trims_section[$section_id];?></td>
				                    <td width="100" style="word-break: break-all;" align="left"><a href="##" onClick="fnc_amount_details(<?php echo "$wo_id, $section_id, $jobcard_id, $order_uom, $rate, $exchange_rate, '$txt_date_from', '$txt_date_to','$pop_order_uom','$item_group'"; ?>, 'wo_qty_popup', 'WO Quantity')"><? echo $row['cust_order_no'];?></a></td>
				                    <td width="120" style="word-break: break-all;" align="left"><? echo $buyer_buyer;?></td>
				                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($qnty,2);?></td>
				                    <td width="60" style="word-break: break-all;" align="center"><? echo $unit_of_measurement[$row['order_uom']]; ?></td>
				                    <td width="80" style="word-break: break-all;" align="right"><? echo number_format($rate,4);?></td>
				                    <td width="100" style="word-break: break-all;" align="right"><?php echo number_format($amount,4); ?></td>

				                    <td width="100" style="word-break: break-all;" align="right"><a href="##" onClick="fnc_amount_details(<?php echo "$wo_id, $section_id, $jobcard_id, $order_uom, $rate, $exchange_rate, '$txt_date_from', '$txt_date_to','$pop_order_uom','$item_group'"; ?>, 'production_qty_popup', 'Production Quantity')"><?php echo number_format($today_qc_qty,2); ?></a></td>
				                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($today_qc_val,4);?></td>
				                    <td width="100" style="word-break: break-all;" align="right"><a href="##" onClick="fnc_amount_details(<?php echo "$wo_id, $section_id, $jobcard_id, $order_uom, $rate, $exchange_rate, '$txt_date_from', '$txt_date_to','$pop_order_uom','$item_group'"; ?>, 'today_issue_qty_popup', 'Issue Quantity','<?php   echo trim($today_issue_id, ",") ?>')"><?php echo number_format($today_issue_cost,4); ?></a></td>
				                    <td width="80" style="word-break: break-all;" align="right" style="word-break:break-all;"><? echo number_format($today_margin_percent,2); ?></td>

				                    <td width="100" style="word-break: break-all;" align="right"><a href="##" onClick="fnc_amount_details(<?php echo "$wo_id, $section_id, $jobcard_id, $order_uom, $rate, $exchange_rate, '$txt_date_from', '$txt_date_to','$pop_order_uom','$item_group'"; ?>, 'cum_production_qty_popup', 'Production Quantity')"><?php echo number_format($cum_qc_qty,2); ?></a></td>
				                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($cum_qc_val,4);?></td>
				                    <td width="100" style="word-break: break-all;" align="right"><a href="##" onClick="fnc_amount_details(<?php echo "$wo_id, $section_id, $jobcard_id, $order_uom, $rate, $exchange_rate, '$txt_date_from', '$txt_date_to','$pop_order_uom','$item_group'"; ?>, 'issue_qty_popup', 'Material Issued Cost','<?php echo trim($popup_issue_id, ","); ?>')"><? echo number_format($cum_issue_val,4) ;?></a></td>  
				                    <td width="80" style="word-break: break-all;" align="right"><? echo number_format($gross_margin_percent,2);?></td>
				                    <td style="word-break: break-all;" align="right"><? echo number_format($balance_qty,2);?></td>
				                </tr>
				                <? 
									 
									$tot_qnty+=$qnty;
									$tot_amount+=$amount;
									$tot_today_qc_qty+=$today_qc_qty;
									$tot_today_qc_val+=$today_qc_val;
									$tot_today_issue_cost+=$today_issue_cost;
									$tot_cum_qc_qty+=$cum_qc_qty;
									$tot_cum_qc_val+=$cum_qc_val;
									$tot_cum_issue_val+=$cum_issue_val;
									$tot_balance_qty+=$balance_qty;
									$i++;
									}
								}
							}
						}
					}
					//echo "<pre>"; 
					//print_r($prod_sammary_array);
					?>
	       		</table>
	        </div>
	        <table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
					<th width="35"  align="center">&nbsp;</th>
                    <th width="100" style="word-break: break-all;" align="left">&nbsp;</th>
                    <th width="100" style="word-break: break-all;" align="left">&nbsp;</th>
                    <th width="120" style="word-break: break-all;" align="left">&nbsp;</th>
                    <th width="100" style="word-break: break-all;" align="right">&nbsp;</th>
                    <th width="60" style="word-break: break-all;" align="left">&nbsp;</th>
                    <th width="80" style="word-break: break-all;" align="right"><strong>Total:</strong></th>
                    <th width="100" style="word-break: break-all;" align="right" id="tot_amount"><?php echo number_format($tot_amount,4); ?></th>

                    <th width="100" style="word-break: break-all;" align="right"><?php //echo number_format($tot_today_qc_qty,2); ?></th>
                    <th width="100" style="word-break: break-all;" align="right" id="tot_today_qc_val"><? echo number_format($tot_today_qc_val,4);?></th>
                    <th width="100" style="word-break: break-all;" align="right" id="tot_today_issue_cost"><? echo number_format($tot_today_issue_cost,4);?></th>
                    <th width="80" style="word-break: break-all;" align="right" style="word-break:break-all;">&nbsp;</th>

                    <th width="100" style="word-break: break-all;" align="right"><? //echo number_format($tot_cum_qc_qty,2);?></th>
                    <th width="100" style="word-break: break-all;" align="right" id="tot_cum_qc_val"><? echo number_format($tot_cum_qc_val,4);?></th>
                    <th width="100" style="word-break: break-all;" align="right" id="tot_cum_issue_val"><? echo number_format($tot_cum_issue_val,4) ;//$unit_of_measurement[$row[uom]];?></th>  
                    <th width="80" style="word-break: break-all;" align="right">&nbsp;</th>
                    <th style="word-break: break-all;" align="right"><? //echo number_format($tot_balance_qty,2);?></th>
				</tfoot>
			</table>
	    </div>
		<?
		//die;
	}
	else
	{
		
		$r_id3=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_from=555");
		if($db_type==0)
		{
			if($r_id3)
			{
				mysql_query("COMMIT");  
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($r_id3)
			{
				oci_commit($con);  
			}
		}
		
		$trims_production_sql=" SELECT a.job_id,a.production_date,a.received_id,d.section_id,b.qc_qty,c.conv_factor
		from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d
		where  a.id=b.mst_id and c.id=b.job_dtls_id and c.mst_id=d.id and a.entry_form=269 and d.entry_form=257 and a.company_id =$cbo_company_id and  a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $where_con_prod $where_con_prod_date";
		//echo $trims_order_sql; die;
		$result = sql_select($trims_production_sql);
	    $date_wise_production_arr=array(); $production_arr=array();
	    foreach($result as $row)
	    {
	    	$all_received_ids .=$row[csf('received_id')].',';
		}
 		$all_trims_production_sql=" SELECT a.job_id,a.production_date,a.received_id,d.section_id,b.qc_qty,b.uom,c.conv_factor,c.break_id,c.item_group_id
		from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d
		where  a.id=b.mst_id and c.id=b.job_dtls_id and c.mst_id=d.id and a.entry_form=269 and d.entry_form=257 and a.company_id=$cbo_company_id and  a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $where_con_prod ";
		//echo $trims_order_sql; die;
		$result_all_production = sql_select($all_trims_production_sql);
	    $date_wise_production_arr=array(); $production_arr=array();
	    foreach($result_all_production as $row)
	    {
	    	$qc_qty=$row[csf('qc_qty')]/$row[csf('conv_factor')];
			$breakIdArr=explode(',',$row[csf('break_id')]);
 			if($txt_order_no!="" && $from_date!="" && $to_date!="")
			{
				
				if( (strtotime($row[csf('production_date')])>=strtotime($from_date)) && (strtotime($row[csf('production_date')])<=strtotime($to_date)) )
				{
				//$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$row[csf('uom')]]['today_qc_qty'] +=$qc_qty;
				//$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$row[csf('uom')]]['today_qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
				
				
				$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('uom')]]['today_qc_qty'] +=$qc_qty;
				$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('uom')]]['today_qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
				}
				
			}
			else if($txt_order_no!="")
			{
  				 
	    		//$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$row[csf('uom')]]['today_qc_qty'] +=$qc_qty;
				//$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$row[csf('uom')]]['today_qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
				
				
				$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('uom')]]['today_qc_qty'] +=$qc_qty;
				$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('uom')]]['today_qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
 	    	 
				
			}
			else
			{
 					if( (strtotime($row[csf('production_date')])>=strtotime($from_date)) && (strtotime($row[csf('production_date')])<=strtotime($to_date)) )
					{
						//$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$row[csf('uom')]]['today_qc_qty'] +=$qc_qty;
						//$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$row[csf('uom')]]['today_qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
						
						
						$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('uom')]]['today_qc_qty'] +=$qc_qty;
						$date_wise_production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('uom')]]['today_qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
					}
				
				}
 			    //$production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$row[csf('uom')]]['qc_qty'] +=$qc_qty;
			   // $production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('item_group_id')]][$row[csf('uom')]]['qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
				 $production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('uom')]]['qc_qty'] +=$qc_qty;
			    $production_arr[$row[csf('received_id')]][$row[csf('section_id')]][$row[csf('uom')]]['qc_value'] +=$qc_qty*$orderRateArr[$breakIdArr[0]];
	   	 	
		}

		
		$all_received_ids=array_unique(explode(",",chop($all_received_ids,',')));
		if(count($all_received_ids)>0)
		{
            $id = return_field_value("nvl(max(id), 0) as id", "GBL_TEMP_ENGINE", "id is not null", "id");

			foreach($all_received_ids as $val)
			{
                $id = $id+1;
				$r_id2=execute_query("insert into GBL_TEMP_ENGINE (id,user_id,entry_form,ref_val,ref_from) values ($id,$user_id,555,$val,1)");

			}
            if($db_type==0)
			{
				if($r_id2)
				{
					mysql_query("COMMIT");
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($r_id2)
				{
					oci_commit($con);
				}
			}
		}

		if(count($result)>0)
		{
           /* $order_sql="SELECT a.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,c.order_id,d.id as jobcard_id ,c.qnty,c.rate,c.amount,c.booked_qty,c.description,c.id as breakdown_id, a.trims_ref,b.item_group
			from  subcon_ord_dtls b, subcon_ord_breakdown c,gbl_temp_engine g,subcon_ord_mst a 
			LEFT JOIN trims_job_card_mst d on a.id=d.received_id and d.status_active=1 
			where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and a.id=g.ref_val and g.user_id=$user_id and g.entry_form=555 $sql_order_no $where_con $wo_cond"; 
			*/
			
			  $order_sql="SELECT a.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,c.order_id,0 as jobcard_id ,c.qnty,c.rate,c.amount,c.booked_qty,c.description,c.id as breakdown_id, a.trims_ref,b.item_group
			from  subcon_ord_dtls b, subcon_ord_breakdown c,gbl_temp_engine g,subcon_ord_mst a
			where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and a.id=g.ref_val and g.user_id=$user_id and g.entry_form=555 $sql_order_no $where_con $wo_cond";  
			 
			
			/*$order_sql="SELECT a.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,c.order_id,c.qnty,c.rate,c.amount,c.booked_qty,c.description,c.id as breakdown_id, a.trims_ref,b.item_group
			from  subcon_ord_dtls b, subcon_ord_breakdown c,subcon_ord_mst a  
			where a.id=b.mst_id  and a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  and a.is_deleted=0 and a.status_active=1   and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.id in (select poid from tmp_poid where userid=$user_id and type=555) $sql_order_no $where_con $wo_cond";  */ 

			$order_sql_result = sql_select($order_sql);
			$order_array=array(); $all_job_ids='';
			foreach($order_sql_result as $row)
			{
				$order_array[$row[csf('section')]][$row[csf('order_uom')]][$row[csf('id')]][$row[csf('subcon_job')]]['buyer_buyer'] .=$row[csf('buyer_buyer')].','; 
				$order_array[$row[csf('section')]][$row[csf('order_uom')]][$row[csf('id')]][$row[csf('subcon_job')]]['currency_id'] =$row[csf('currency_id')];
				$order_array[$row[csf('section')]][$row[csf('order_uom')]][$row[csf('id')]][$row[csf('subcon_job')]]['qnty'] 		+=$row[csf('qnty')];
				$order_array[$row[csf('section')]][$row[csf('order_uom')]][$row[csf('id')]][$row[csf('subcon_job')]]['amount'] 		+=$row[csf('amount')];
				$order_array[$row[csf('section')]][$row[csf('order_uom')]][$row[csf('id')]][$row[csf('subcon_job')]]['exchange_rate'] =$row[csf('exchange_rate')];
				$order_array[$row[csf('section')]][$row[csf('order_uom')]][$row[csf('id')]][$row[csf('subcon_job')]]['cust_order_no'] =$row[csf('cust_order_no')];
				$order_array[$row[csf('section')]][$row[csf('order_uom')]][$row[csf('id')]][$row[csf('subcon_job')]]['jobcard_id'] =$row[csf('jobcard_id')];
				$order_array[$row[csf('section')]][$row[csf('order_uom')]][$row[csf('id')]][$row[csf('subcon_job')]]['order_uom'] =$row[csf('order_uom')];
				$order_array[$row[csf('section')]][$row[csf('order_uom')]][$row[csf('id')]][$row[csf('subcon_job')]]['booked_uom'] =$row[csf('booked_uom')];
				$order_array[$row[csf('section')]][$row[csf('order_uom')]][$row[csf('id')]][$row[csf('subcon_job')]]['item_group'] =$row[csf('item_group')];
				 
				$all_job_ids .=$row[csf('jobcard_id')].',';
			}
			$all_job_ids=implode(",",array_unique(explode(",",chop($all_job_ids,','))));
		}

		if($all_job_ids!=''){
			$job_card_cond=" and g.id in ($all_job_ids)";
			$job_card_condd=" and c.id in ($all_job_ids)";
		}

          $issue_sql = "SELECT distinct g.received_id,e.issue_date,f.cons_quantity as issue_qty, f.cons_rate,f.prod_id,e.id as issue_id,i.uom 
		from trims_raw_mat_requisition_mst a,trims_raw_mat_requisition_dtls b,inv_issue_master e,inv_transaction f,trims_job_card_mst g,gbl_temp_engine h ,trims_job_card_dtls i
		where a.entry_form in (427,501) and a.id = b.mst_id and a.job_id=g.id and a.id = e.req_id and e.id=f.mst_id and b.product_id = f.prod_id and b.mst_id = e.req_id and b.job_no = g.trims_job and e.req_no=a.requisition_no  and g.id=i.mst_id  and f.transaction_type = 2 and e.issue_basis = 7 and e.entry_form = 265 and a.status_active = 1 and b.status_active = 1 and e.status_active = 1 and f.status_active = 1 and g.status_active=1 and g.received_id=h.ref_val and h.user_id=$user_id and h.entry_form=555
		union all
		SELECT distinct c.received_id,a.issue_date, b.cons_quantity as issue_qty, b.cons_rate,b.prod_id,a.id as issue_id,i.uom
		from inv_issue_master a, inv_transaction b, trims_job_card_mst c,gbl_temp_engine h ,trims_job_card_dtls i 
		where a.issue_basis=15 and a.entry_form=265  and  b.transaction_type=2 and  a.id=b.mst_id and a.req_id=c.id and  a.status_active=1 and b.status_active=1 and c.status_active=1 and c.id=i.mst_id and c.received_id=h.ref_val and h.user_id=$user_id and h.entry_form=555";   
		
		
		 /* $issue_sql = "SELECT distinct g.received_id,e.issue_date,f.cons_quantity as issue_qty, f.cons_rate,f.prod_id,e.id as issue_id ,h.uom
		from trims_raw_mat_requisition_mst a,trims_raw_mat_requisition_dtls b,inv_issue_master e,inv_transaction f,trims_job_card_mst g,trims_job_card_dtls h
		where a.entry_form in (427,501) and a.job_id=g.id and a.id = b.mst_id and a.job_no = g.trims_job and a.id = e.req_id and e.id=f.mst_id and b.product_id = f.prod_id and b.mst_id = e.req_id and b.job_no = g.trims_job and e.req_no=a.requisition_no  and g.id=h.mst_id   and f.transaction_type = 2 and e.issue_basis = 7 and e.entry_form = 265 and a.status_active = 1 and b.status_active = 1 and e.status_active = 1 and f.status_active = 1 and g.status_active = 1  and g.received_id in (select poid from tmp_poid where userid=$user_id and type=555)
		union all
		SELECT distinct c.received_id,a.issue_date, b.cons_quantity as issue_qty, b.cons_rate,b.prod_id,a.id as issue_id,d.uom
		from inv_issue_master a, inv_transaction b, trims_job_card_mst c , trims_job_card_dtls d 
		where a.id=b.mst_id and a.req_id=c.id  and c.id=d.mst_id and b.transaction_type=2 and a.issue_basis=15 and a.entry_form=265  and c.received_id in (select poid from tmp_poid where userid=$user_id and type=555)";   
*/
		$iss_data_array=sql_select($issue_sql);
		$issue_qty_arr=array(); $date_wise_issue_qty_arr=array();
		foreach($iss_data_array as $row)
		{
			$issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['qty']+=$row[csf("issue_qty")];
			$issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['amount']+=$row[csf("issue_qty")]*$row[csf("cons_rate")];
			$issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['issue_id'].=$row[csf("issue_id")].',';
			$issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['prod_id'].=$row[csf("prod_id")].',';
			if($from_date!="" and $to_date!="")
			{
				
				if( (strtotime($row[csf('issue_date')])>=strtotime($from_date)) && (strtotime($row[csf('issue_date')])<=strtotime($to_date)) )
				{
			    	$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['today_qty']+=$row[csf("issue_qty")];
					$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['today_amount']+=$row[csf("issue_qty")]*$row[csf("cons_rate")];
					$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['issue_id'].=$row[csf("issue_id")].',';
					$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['prod_id'].=$row[csf("prod_id")].',';
					//echo "dsfds";
			    }
			}
			else
			{
		    	$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['today_qty']+=$row[csf("issue_qty")];
				$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['today_amount']+=$row[csf("issue_qty")]*$row[csf("cons_rate")];
				$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['issue_id'].=$row[csf("issue_id")].',';
				$date_wise_issue_qty_arr[$row[csf("received_id")]][$row[csf("uom")]]['prod_id'].=$row[csf("prod_id")].',';
		    }
			$all_issue_ids[$row[csf("issue_id")]] =$row[csf("issue_id")];
		    //echo "bbbb";
		}
 		$all_issue_ids_id=implode(",",$all_issue_ids);
		/*$issue_return_sql = "SELECT a.id as issue_id,a.issue_date, b.cons_quantity as rtn_qty, b.cons_rate,b.prod_id,d.receive_date from inv_issue_master a, inv_transaction b, inv_receive_master d where b.transaction_type=4 and a.entry_form=265 and d.id=b.mst_id and a.id=b.issue_id and d.entry_form=266 and a.status_active=1 and b.status_active=1 and d.status_active=1";*/
		
		 $issue_return_sql = "SELECT a.id as issue_id,a.issue_date, b.cons_quantity as rtn_qty, b.cons_rate,b.prod_id,d.receive_date from inv_issue_master a, inv_transaction b, inv_receive_master d where b.transaction_type=4 and a.entry_form=265 and d.id=b.mst_id and a.id=b.issue_id and b.issue_id in ($all_issue_ids_id) and d.entry_form=266 and a.status_active=1 and b.status_active=1 and d.status_active=1";
		$iss_rtn_data_array=sql_select($issue_return_sql);
		$issue_rtn_qty_arr=array();
		foreach($iss_rtn_data_array as $row){
			$issue_rtn_qty_arr[$row[csf("issue_id")]]['qty']+=$row[csf("rtn_qty")];
			$issue_rtn_qty_arr[$row[csf("issue_id")]]['amount']+=$row[csf("rtn_qty")]*$row[csf("cons_rate")];
			if($from_date!="" and $to_date!="")
			{

				if( (strtotime($row[csf('receive_date')])>=strtotime($from_date)) && (strtotime($row[csf('receive_date')])<=strtotime($to_date)) )
				//if(($row[csf('receive_date')]>$txt_date_from) && ($txt_date_to < $row[csf('receive_date')]))
				{
					//echo 'vdghdo';
			    	$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]]['today_qty']+=$row[csf("rtn_qty")];
					$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]]['today_amount']+=$row[csf("rtn_qty")]*$row[csf("cons_rate")];
				}
		    }else{
		    	$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]]['today_qty']+=$row[csf("rtn_qty")];
				$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]]['today_amount']+=$row[csf("rtn_qty")]*$row[csf("cons_rate")];
		    }
		}
		/*echo "<pre>";
		print_r($date_wise_issue_rnn_qty_arr);*/
		$r_id3=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form=555");
		if($db_type==0)
		{
			if($r_id3)
			{
				mysql_query("COMMIT");  
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($r_id3)
			{
				oci_commit($con);  
			}
		}
		disconnect($con);
		ob_start();
		?>	
		<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
	    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
					<thead class="form_caption" >
						<tr>
							<td colspan="17" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
						</tr>
						<tr>
							<td colspan="17" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
						</tr>
						<? if($txt_date_from!='' && $txt_date_to!='' ){
							?>
							<tr>
								<td colspan="17" align="center" style="font-size:14px; font-weight:bold">
								<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?></td>
							</tr>
							<?
						} ?>
						
					</thead>
				</table>
	            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead>	
                    	<? $content.=ob_get_flush(); ?>		
						<tr>
							<th colspan="18" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4 ); ?></th>
						</tr>
                        <? ob_start();?>
						<tr>
							<th width="35">SL</th>
							<th width="100">Section</th>
							<th width="100">Work Order No</th>
							<th width="120">Buyer Name</th>
							<th width="100">WO Qty.</th>
							<th width="60">WO UOM</th>
							<th width="80">Avg. Unit Price ($)</th>
							<th width="100">WO Value ($)</th>
							<th width="100">Today Prod in WO UOM</th>
							<th width="100">Today Prod. Value [$]</th>
							<th width="100" title="Total Material Cost/WO Qty.*Today Prod in WO UOM">Today Prod. Material Cost</th>
							<th width="100">Today Material Issued Cost [$]</th>
							<th width="80">Today Margin %</th>
							<th width="100">Cumm Prod in WO UOM</th>
							<th width="100">Cumm. Prod. Value [$]</th>
							<th width="100">Total Material Cost ($)</th>
							<th width="80">Gross. Margin %	</th>
							<th >Balance Qty. in WO UOM</th>
						</tr>
					</thead>
				</table>
	        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
	        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<? echo $width;?>" rules="all" align="left">
	            <? 
					$i=1;
                   	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and company_id = $cbo_company_id and  con_date=(select max(con_date) as con_date from currency_conversion_rate where currency=2 and status_active = 1 and company_id = $cbo_company_id)" , "conversion_rate" );

					$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
					foreach($order_array as $section_id=>$section_id_data)
					{
						$tot_section_amount=$tot_section_today_qc_val=$tot_section_today_material_cost=$tot_section_today_issue_cost=$tot_section_cum_qc_val=$tot_section_cum_issue_val=0;
						foreach($section_id_data as $order_uom=>$order_uom_data)
						{
							$tot_uom_qnty=$tot_uom_amount=$tot_uom_today_qc_val=$tot_uom_today_material_cost=$tot_uom_today_issue_cost=$tot_uom_cum_qc_val=$tot_uom_cum_issue_val=$tot_uom_today_qc_qty=$tot_uom_cum_qc_qty=$tot_uom_balance_qty=0;
							foreach($order_uom_data as $wo_id=>$wo_id_arr)
							{
								foreach($wo_id_arr as $subcon_job=>$row)
								{
									if ($i % 2 == 0){ $bgcolor = "#E9F3FF";}else{ $bgcolor = "#FFFFFF";}
						
									if($order_uom*1==$row['booked_uom']*1)
									{
									  $order_uom=$order_uom;
									}
									else
									{
										$order_uom=$row['booked_uom'];
									}
									
						
									$buyer_buyer=implode(",",array_unique(explode(",",chop($row['buyer_buyer'],','))));

									//$cum_qc_qty=$production_arr[$wo_id][$section_id][$row['item_group']][$order_uom]['qc_qty'];
									$cum_qc_qty=$production_arr[$wo_id][$section_id][$order_uom]['qc_qty'];
									
									
									// $cum_qc_val=$cum_qc_qty*$rate;
									//$cum_qc_val=$production_arr[$wo_id][$section_id][$row['item_group']][$order_uom]['qc_value'];
									$cum_qc_val=$production_arr[$wo_id][$section_id][$order_uom]['qc_value'];
									$today=date("d-m-Y");
									$jobcard_id=$row['jobcard_id'];
									$exchange_rate=$row['exchange_rate'];
                                    $currency_id_ac = $row['currency_id'];
                                    $qnty=$row['qnty'];
 									if($currency_id_ac == 1)
									{
									    $amount=$row['amount']/$currency_rate;			
                                    }
									else
									{
										$amount=$row['amount'];			
									}
									$rate=$amount/$qnty;

									//$today_qc_qty=$date_wise_production_arr[$wo_id][$section_id][$row['item_group']][$order_uom]['today_qc_qty'];
									//$today_qc_val=$date_wise_production_arr[$wo_id][$section_id][$row['item_group']][$order_uom]['today_qc_value'];
									$today_qc_qty=$date_wise_production_arr[$wo_id][$section_id][$order_uom]['today_qc_qty'];
									 
									if($currency_id_ac == 1)
									{
									    $today_qc_val=$date_wise_production_arr[$wo_id][$section_id][$order_uom]['today_qc_value']/$currency_rate; 					
                                    }
									else
									{
										$today_qc_val=$date_wise_production_arr[$wo_id][$section_id][$order_uom]['today_qc_value']; 									
									}
                                    
									
									

									$issue_id=$issue_qty_arr[$wo_id][$row['order_uom']]['issue_id'];
									
									
									
									$issue_ids=array_unique(explode(",",chop($issue_id,',')));
									$popup_issue_id=implode(",",$issue_ids);
								 
									$rtn_qty=$rtn_val=$today_rtn_qty=$today_rtn_val='';
									foreach ($issue_ids as $key => $value) {
										$rtn_qty+=$issue_rtn_qty_arr[$value]['qty'];
										$rtn_val+=$issue_rtn_qty_arr[$value]['amount'];
									}

									$cum_issue_qty=$issue_qty_arr[$wo_id][$row['order_uom']]['qty']-$rtn_qty;
									$cum_issue_val=($issue_qty_arr[$wo_id][$row['order_uom']]['amount']-$rtn_val)/$exchange_rate;
									//$cum_issue_val=($cum_issue_qty*$rate)/$exchange_rate;
									//$cum_issue_val=($cum_issue_qty*$rate);
									if($currency_id_ac == 1){
                                        $cum_issue_val=($issue_qty_arr[$wo_id][$row['order_uom']]['amount']-$rtn_val)/$currency_rate;
                                    }
									$today_issue_id=$date_wise_issue_qty_arr[$wo_id][$row['order_uom']]['issue_id'];
									$today_issue_ids=array_unique(explode(",",chop($today_issue_id,',')));
									foreach ($today_issue_ids as $key => $value) {
										$today_rtn_qty+=$date_wise_issue_rnn_qty_arr[$value]['today_qty'];
										$today_rtn_val+=$date_wise_issue_rnn_qty_arr[$value]['today_amount'];
									}									//echo $today_rtn_qty;
									$today_issue_qty=$date_wise_issue_qty_arr[$wo_id][$row['order_uom']]['today_qty']-$today_rtn_qty;
									$today_issue_cost=($date_wise_issue_qty_arr[$wo_id][$row['order_uom']]['today_amount']-$today_rtn_val)/$exchange_rate;
									if($currency_id_ac == 1){
                                        $today_issue_cost=($date_wise_issue_qty_arr[$wo_id][$row['order_uom']]['today_amount']-$today_rtn_val)/$currency_rate;
                                    }
                                    $today_margin_percent =(($today_qc_val-$today_issue_cost)*100)/$today_qc_val;
									$gross_margin_percent =(($cum_qc_val-$cum_issue_val)*100)/$cum_qc_val;
									$balance_qty=$qnty-$cum_qc_qty;
									$balance_val=$amount-$cum_qc_val;
									$pop_order_uom=$row['order_uom'];
									$item_group=$row['item_group'];
									
								if(number_format($today_qc_qty,2)>0)
								{
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="35"  align="center"><? echo $i;?></td> 
										<td width="100" style="word-break: break-all;" align="left"><? echo $trims_section[$section_id];?></td>
										<td width="100" style="word-break: break-all;" align="left"><a href="##" onClick="fnc_amount_details(<?php echo "$wo_id, $section_id, $jobcard_id, $order_uom, $rate, $exchange_rate, '$txt_date_from', '$txt_date_to','$pop_order_uom','$item_group'"; ?>, 'wo_qty_popup', 'WO Quantity')"><? echo $row['cust_order_no'];?></a></td>
										<td width="120" style="word-break: break-all;" align="left"><? echo $buyer_buyer;?></td>
										<td width="100" style="word-break: break-all;" align="right"><? echo number_format($qnty,2);?></td>
										<td width="60" style="word-break: break-all;" align="center"><? echo $unit_of_measurement[$row['order_uom']]; ?></td>
										<td width="80" style="word-break: break-all;" align="right"><? echo number_format($rate,4);?></td>
										<td width="100" style="word-break: break-all;" align="right"><?php echo number_format($amount,4); ?></td>

										<td width="100" style="word-break: break-all;" align="right"><a href="##" onClick="fnc_amount_details(<?php echo "$wo_id, $section_id, $jobcard_id, $order_uom, $rate, $currency_rate, '$txt_date_from', '$txt_date_to','$pop_order_uom','$item_group'"; ?>, 'production_qty_popup', 'Production Quantity','<?php echo trim($popup_issue_id, ","); ?>', '<?=$currency_id_ac?>','<?=$cbo_company_id?>')"><?php echo number_format($today_qc_qty,2); ?></a></td>
										<td width="100" style="word-break: break-all;" align="right"><? echo number_format($today_qc_val,4);?></td>
										<td width="100" style="word-break: break-all;" align="right" title="<?=$cum_issue_val." / ".$qnty." * ".$today_qc_qty;?>"><? echo fn_number_format($cum_issue_val/$qnty*$today_qc_qty,4);?></td>
										<td width="100" style="word-break: break-all;" align="right"><a href="##" onClick="fnc_amount_details(<?php echo "$wo_id, $section_id, $jobcard_id, $order_uom, $rate, $exchange_rate, '$txt_date_from', '$txt_date_to','$pop_order_uom','$item_group'"; ?>, 'today_issue_qty_popup', 'Issue Quantity','<?php echo trim($popup_issue_id, ","); ?>', '<?=$currency_id_ac?>','<?=$cbo_company_id?>')"><?php echo number_format($today_issue_cost,4); ?></a></td>
										<td width="80" style="word-break: break-all;" align="right"><? echo number_format($today_margin_percent,2); ?></td>

										<td width="100" style="word-break: break-all;" align="right"><a href="##" onClick="fnc_amount_details(<?php echo "$wo_id, $section_id, $jobcard_id, $order_uom, $rate, $exchange_rate, '$txt_date_from', '$txt_date_to','$pop_order_uom','$item_group'"; ?>, 'cum_production_qty_popup', 'Production Quantity')"><?php echo number_format($cum_qc_qty,2); ?></a></td>
										<td width="100" style="word-break: break-all;" align="right"><? echo number_format($cum_qc_val,4);?></td>
										<td width="100" style="word-break: break-all;" align="right"><a href="##" onClick="fnc_amount_details(<?php echo "$wo_id, $section_id, $jobcard_id, $order_uom, $rate, $exchange_rate, '$txt_date_from', '$txt_date_to','$pop_order_uom','$item_group'"; ?>, 'issue_qty_popup', 'Material Issued Cost','<?php echo trim($popup_issue_id, ","); ?>', '<?=$currency_id_ac?>','<?=$cbo_company_id?>')"><? echo number_format($cum_issue_val,4) ;?></a></td>
										<td width="80" style="word-break: break-all;" align="right"><? echo number_format($gross_margin_percent,2);?></td>
										<td style="word-break: break-all;" align="right"><? echo number_format($balance_qty,2);?></td>
									</tr>
									<? 
									$tot_section_amount+=$amount;
									$tot_section_today_qc_val+=$today_qc_val;
									$tot_section_today_material_cost+=$cum_issue_val/$qnty*$today_qc_qty;
									$tot_section_today_issue_cost+=$today_issue_cost;
									$tot_section_cum_qc_val+=$cum_qc_val;
									$tot_section_cum_issue_val+=$cum_issue_val;

									$tot_uom_qnty+=$qnty;
									$tot_uom_amount+=$amount;
									$tot_uom_today_qc_val+=$today_qc_val;
									$tot_uom_today_material_cost+=$cum_issue_val/$qnty*$today_qc_qty;
									$tot_uom_today_issue_cost+=$today_issue_cost;
									$tot_uom_cum_qc_val+=$cum_qc_val;
									$tot_uom_cum_issue_val+=$cum_issue_val;
									$tot_uom_today_qc_qty+=$today_qc_qty;
									$tot_uom_cum_qc_qty+=$cum_qc_qty;
									$tot_uom_balance_qty+=$balance_qty;

									$tot_qnty+=$qnty;
									$tot_amount+=$amount;
									$tot_today_qc_qty+=$today_qc_qty;
									$tot_today_qc_val+=$today_qc_val;
									$tot_today_material_cost+=$cum_issue_val/$qnty*$today_qc_qty;
									$tot_today_issue_cost+=$today_issue_cost;
									$tot_cum_qc_qty+=$cum_qc_qty;
									$tot_cum_qc_val+=$cum_qc_val;
									$tot_cum_issue_val+=$cum_issue_val;
									$tot_balance_qty+=$balance_qty;
									$i++;
									}
								}
							}
							
							if(number_format($tot_uom_today_qc_qty,2)>0)
							{
							?>
							<tr bgcolor="#AEA9A9">
								<td >&nbsp;</td>
								<td style="word-break: break-all;" align="right" colspan="3"><strong>Sub-Total <? echo $trims_section[$section_id]." ".$unit_of_measurement[$row['order_uom']];?></strong></td>
								<td style="word-break: break-all;" align="right"><?php echo number_format($tot_uom_qnty,2); ?></td>
								<td style="word-break: break-all;" align="right">&nbsp;</td>
								<td style="word-break: break-all;" align="right">&nbsp;</td>
								<td style="word-break: break-all;" align="right"><?php echo number_format($tot_uom_amount,4); ?></td>
								<td style="word-break: break-all;" align="right"><?php echo number_format($tot_uom_today_qc_qty,2); ?></td>
								<td style="word-break: break-all;" align="right"><? echo number_format($tot_uom_today_qc_val,4);?></td>
								<td style="word-break: break-all;" align="right"><? echo fn_number_format($tot_uom_today_material_cost,4);?></td>
								<td style="word-break: break-all;" align="right"><? echo number_format($tot_uom_today_issue_cost,4);?></td>
								<td style="word-break: break-all;" align="right">&nbsp;</td>
								<td style="word-break: break-all;" align="right"><? echo number_format($tot_uom_cum_qc_qty,2);?></td>
								<td style="word-break: break-all;" align="right" ><? echo number_format($tot_uom_cum_qc_val,4);?></td>
								<td style="word-break: break-all;" align="right" ><? echo number_format($tot_uom_cum_issue_val,4) ;?></td>  
								<td style="word-break: break-all;" align="right">&nbsp;</td>
								<td style="word-break: break-all;" align="right"><? echo number_format($tot_uom_balance_qty,2);?></td>
							</tr>
							<?
							}
						}
						?>
						<tr bgcolor="#848080">
							<td >&nbsp;</td>
							<td style="word-break: break-all;" align="right" colspan="6"><strong>Grand Total <? echo $trims_section[$section_id];?></strong></td>
							<td width="100" style="word-break: break-all;" align="right" ><?php echo number_format($tot_section_amount,4); ?></td>
							<td width="100" style="word-break: break-all;" align="right">&nbsp;</td>
							<td width="100" style="word-break: break-all;" align="right" ><? echo number_format($tot_section_today_qc_val,4);?></td>
							<td width="100" style="word-break: break-all;" align="right" ><? echo fn_number_format($tot_section_today_material_cost,4);?></td>
							<td width="100" style="word-break: break-all;" align="right" ><? echo number_format($tot_section_today_issue_cost,4);?></td>
							<td width="80" style="word-break: break-all;" align="right" >&nbsp;</td>
							<td width="100" style="word-break: break-all;" align="right">&nbsp;</td>
							<td width="100" style="word-break: break-all;" align="right" ><? echo number_format($tot_section_cum_qc_val,4);?></td>
							<td width="100" style="word-break: break-all;" align="right" ><? echo number_format($tot_section_cum_issue_val,4) ;?></td>  
							<td width="80" style="word-break: break-all;" align="right">&nbsp;</td>
							<td style="word-break: break-all;" align="right">&nbsp;</td>
						</tr>
						<?
					}
					?>
	       		</table>
	        </div>
	        <table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
					<th width="35"  >&nbsp;</th>
                    <th width="100" >&nbsp;</th>
                    <th width="100" >&nbsp;</th>
                    <th width="120" >&nbsp;</th>
                    <th width="100" >&nbsp;</th>
                    <th width="60" >&nbsp;</th>
                    <th width="80" style="word-break: break-all;" align="right"><strong>Total:</strong></th>
                    <th width="100" style="word-break: break-all;" align="right"><?php echo number_format($tot_amount,4); ?></th>
                    <th width="100" style="word-break: break-all;" align="right"><?php //echo number_format($tot_today_qc_qty,2); ?></th>
                    <th width="100" style="word-break: break-all;" align="right"><? echo fn_number_format($tot_today_qc_val,4);?></th>
                    <th width="100" style="word-break: break-all;" align="right"><? echo number_format($tot_today_material_cost,4);?></th>
                    <th width="100" style="word-break: break-all;" align="right" ><? echo number_format($tot_today_issue_cost,4);?></th>
                    <th width="80" style="word-break: break-all;" align="right" >&nbsp;</th>
                    <th width="100" style="word-break: break-all;" align="right"><? //echo number_format($tot_cum_qc_qty,2);?></th>
                    <th width="100" style="word-break: break-all;" align="right" ><? echo number_format($tot_cum_qc_val,4);?></th>
                    <th width="100" style="word-break: break-all;" align="right"><? echo number_format($tot_cum_issue_val,4) ;?></th>  
                    <th width="80" style="word-break: break-all;" align="right">&nbsp;</th>
                    <th style="word-break: break-all;" align="right"><? //echo number_format($tot_balance_qty,2);?></th>
				</tfoot>
			</table>
	    </div>
		<?
		
	}
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$content.=ob_get_flush();
	$is_created = fwrite($create_new_doc,$content);
	echo "$total_data**$filename**$report_type";
	exit();
	
}

if($action=='wo_qty_popup') {
	echo load_html_head_contents('Popup Info', '../../../../', 1, 1, $unicode);
	extract($_REQUEST);
	// $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name');
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$sizeArr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", 'id', 'size_name');
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="1195" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">S.L</th>
                <th width="100">System ID</th>
                <th width="100">Delivery Date</th>
                <th width="80">Customer Name</th>
                <th width="120">Job Card No.</th>
                <th width="100">Section</th>
                <th width="130">Item Description</th>
                <th width="130">Item Color</th>
                <th width="50">Item Size</th>
                <th width="50">Order UOM</th>
                <th width="50">Currency</th>
                <th width="50">Order Qty</th>
                <th width="50">Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
		<?php
		/*$details_sql="SELECT a.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,c.order_id,c.qnty,c.rate,c.amount,c.booked_qty,c.description,c.id as breakdown_id,c.color_id,c.size_id, a.trims_ref ,d.id as jobcard_id ,d.trims_job
		from  subcon_ord_dtls b, subcon_ord_breakdown c,subcon_ord_mst a 
		LEFT JOIN trims_job_card_mst d on a.id=d.received_id and d.is_deleted=0 and d.status_active=1 
		where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.id=$wo_id and b.section=$section and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 $sql_order_no ";*/
		
 			
			
			
			  $jobcard_sql="SELECT  a.id as jobcard_id , a.trims_job,b.uom,a.received_id
			from  trims_job_card_mst a,trims_job_card_dtls b  where a.id=b.mst_id and a.received_id=$wo_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";    
 			$jobcard_sql_result = sql_select($jobcard_sql);
			$jobcard_array=array(); 
			foreach($jobcard_sql_result as $row)
			{
				$jobcard_array[$row[csf('received_id')]][$row[csf('uom')]]['trims_job']=$row[csf('trims_job')]; 
				 
			}
			 
		
		
		
		//echo $pop_order_uom;
		$details_sql="SELECT a.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,c.order_id,c.qnty,c.rate,c.amount,c.booked_qty,c.description,c.id as breakdown_id,c.color_id,c.size_id, a.trims_ref  
		from  subcon_ord_dtls b, subcon_ord_breakdown c,subcon_ord_mst a  
		where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.id=$wo_id  and b.order_uom=$pop_order_uom  and b.section=$section and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 $sql_order_no ";
		//echo $details_sql;
		$sql_result=sql_select($details_sql); $t=1;
		$total_qc_qty=0;
		$total_qc_amt=0;
		foreach($sql_result as $row)
		{
			$qc_qty = $row[csf('qc_qty')];
			$amount = $qc_qty*$orderRate*$exchangeRate;
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$delevery_amt=$row[csf("delevery_qty")]*$rate; ?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onClick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t; ?></p></td>
                <td><p><?php echo $row[csf('subcon_job')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                <td><p><?php echo $buyerArr[$row[csf('party_id')]]; ?></p></td>
                <td><p><?php echo $jobcard_array[$row[csf('id')]][$row[csf('order_uom')]]['trims_job']; ?></p></td>
                <td><p><?php echo $trims_section[$section]; ?></p></td>
                <td><p><?php echo $row[csf('description')]; ?></p></td>
                <td><p><?php echo $colorNameArr[$row[csf('color_id')]]; ?></p></td>
                <td><p><?php echo $sizeArr[$row[csf('size_id')]]; ?></p></td>
                <td><p><?php echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
                <td><p><?php echo $currency[$row[csf('currency_id')]]; ?></p></td>
                <td style="text-align: right;"><p><?php echo $row[csf('qnty')]; ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($row[csf('rate')], 2); ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($row[csf('amount')], 2); ?></p></td>
            </tr>
            <?php
            $total_qty += $row[csf('qnty')];
            $total_amt += $row[csf('amount')];
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
               	<th>&nbsp;</th>
               	<th>&nbsp;</th>
                <th>Total</th>
               	<th align="right"><?php echo $total_qty; ?></th>
               	<th>&nbsp;</th>
              	<th align="right"><?php echo number_format($total_amt, 2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

if($action=='production_qty_popup') 
{
	echo load_html_head_contents('Popup Info', '../../../../', 1, 1, $unicode);
	extract($_REQUEST);
	// $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name');
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$sizeArr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", 'id', 'size_name');
	$orderRateArr = return_library_array("select id, rate from subcon_ord_breakdown where status_active=1 and is_deleted=0", 'id', 'rate');
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="1195" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">S.L</th>
                <th width="100">System ID</th>
                <th width="100">Prod. Date</th>
                <th width="150">Customer Name</th>
                <th width="120">Job Card No.</th>
                <th width="100">Section</th>
                <th width="130">Item Description</th>
                <th width="130">Item Color</th>
                <th width="60">Item Size</th>
                <th width="50">Order UOM</th>
                <th width="100">Prod. Qty</th>
                <th>Production Amount [$]</th>
            </tr>
        </thead>
        <tbody>
		<?php

		if(str_replace("'","", $form_date)!="" and str_replace("'","", $to_date)!="")
		{	
			$where_con.=" and a.production_date between '$form_date' and '$to_date'";
		}
		//echo $jobcard_id;
		$today= date("d-M-Y"); 
		/*if($today!="")
		{	
			$where_con.=" and a.production_date= '$today'";
		}*/
		// and d.id=$jobcard_id
		// and c.uom=$order_uom ,'$item_group'
		 /* $details_sql=" SELECT a.trims_production,a.job_id,a.production_date,a.received_id,d.section_id,b.qc_qty,b.job_dtls_id,b.uom as product_uom,c.conv_factor,c.job_quantity,c.uom,c.job_no_mst,c.break_id,c.item_description,c.color_id,c.size_id,d.order_no,d.party_id,d.within_group,d.trims_job,c.order_uom
		from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d, subcon_ord_dtls e
		where  a.id=b.mst_id and c.id=b.job_dtls_id and c.mst_id=d.id and e.id=c.receive_dtls_id and e.id=b.receive_dtls_id and a.entry_form=269 and d.entry_form=257 and d.received_id =$wo_id and d.section_id=$section and e.order_uom=$order_uom  and a.status_active=1 and b.status_active=1 and c.status_active=1 $where_con $sql_cond
		group by a.trims_production,a.job_id,a.production_date,a.received_id,d.section_id,b.qc_qty,b.job_dtls_id,b.uom,c.conv_factor,c.job_quantity,c.uom,c.job_no_mst,c.break_id,c.item_description,c.color_id,c.size_id,d.order_no,d.party_id,d.within_group,d.trims_job,c.order_uom";*/
		
		//if($order_uom==12){$order_uom=2;}else{$order_uom=$order_uom;}
		
		 $details_sql=" SELECT a.trims_production,a.job_id,a.production_date,a.received_id,d.section_id,b.qc_qty,b.job_dtls_id,b.uom as product_uom,c.conv_factor,c.job_quantity,c.uom,c.job_no_mst,c.break_id,c.item_description,c.color_id,c.size_id,d.order_no,d.party_id,d.within_group,d.trims_job,c.order_uom
		from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d 
		where  a.id=b.mst_id and c.id=b.job_dtls_id and c.mst_id=d.id and a.entry_form=269 and d.entry_form=257 and d.received_id =$wo_id and d.section_id=$section and c.uom=$order_uom   and a.status_active=1 and b.status_active=1 and c.status_active=1 $where_con $sql_cond
		group by a.trims_production,a.job_id,a.production_date,a.received_id,d.section_id,b.qc_qty,b.job_dtls_id,b.uom,c.conv_factor,c.job_quantity,c.uom,c.job_no_mst,c.break_id,c.item_description,c.color_id,c.size_id,d.order_no,d.party_id,d.within_group,d.trims_job,c.order_uom";
	//echo $details_sql;
		$sql_result=sql_select($details_sql); $t=1;
		$total_qc_qty=0;
		$total_qc_amt=0;
		foreach($sql_result as $row)
		{
			$qc_qty = $row[csf('qc_qty')]/$row[csf('conv_factor')];
			
			
			//$amount = ($qc_qty*$rate)/$exchange_rate;
			// $amount = ($qc_qty*$rate);
			$breakIdArr=explode(",",$row[csf('break_id')]);
 			if($currency_id == 1)
			{
			 
				$rate=$orderRateArr[$breakIdArr[0]]/$exchange_rate;	
				 	
			}
			else
			{
				$rate=$orderRateArr[$breakIdArr[0]];		
			}
			$amount = ($qc_qty*$rate);
			
			//echo  $currency_id; die;
			//echo $currency_id; die;
			
			 

			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$delevery_amt=$row[csf("delevery_qty")]*$rate; ?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onClick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t; ?></p></td>
                <td><p><?php echo $row[csf('trims_production')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('production_date')]); ?></p></td>
                <td><p><?php echo $buyerArr[$row[csf('party_id')]]; ?></p></td>
                <td><p><?php echo $row[csf('trims_job')]; ?></p></td>
                <td><p><?php echo $trims_section[$section]; ?></p></td>
                <td><p><?php echo $row[csf('item_description')]; ?></p></td>
                <td><p><?php echo $colorNameArr[$row[csf('color_id')]]; ?></p></td>
                <td><p><?php echo $sizeArr[$row[csf('size_id')]]; ?></p></td>
                <td><p><?php echo $unit_of_measurement[$pop_order_uom]; ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($qc_qty, 2); ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($amount, 4); ?></p></td>
            </tr>
            <?php
            $total_qty += $qc_qty;
            $total_amt += $amount;
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
               	<th>&nbsp;</th>
               	<th>&nbsp;</th>
               	<th>&nbsp;</th>
                <th>Total</th>
               	<th align="right"><?php echo number_format($total_qty, 2); ?></th>
              	<th align="right"><?php echo number_format($total_amt, 4); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

if($action=='cum_production_qty_popup') 
{
	echo load_html_head_contents('Popup Info', '../../../../', 1, 1, $unicode);
	extract($_REQUEST);
	// $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name');
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$sizeArr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", 'id', 'size_name');
	$orderRateArr = return_library_array("select id, rate from subcon_ord_breakdown where status_active=1 and is_deleted=0", 'id', 'rate');
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="1195" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">S.L</th>
                <th width="100">System ID</th>
                <th width="100">Prod. Date</th>
                <th width="150">Customer Name</th>
                <th width="120">Job Card No.</th>
                <th width="100">Section</th>
                <th width="130">Item Description</th>
                <th width="130">Item Color</th>
                <th width="60">Item Size</th>
                <th width="50">Order UOM</th>
                <th width="100">Prod. Qty</th>
                <th>Production Amount [$]</th>
            </tr>
        </thead>
        <tbody>
		<?php
		$today= date("d-M-Y");
		if(str_replace("'","", $form_date)!="" and str_replace("'","", $to_date)!="")
		{	
			$where_con.=" and a.production_date between '$form_date' and '$to_date'";
		}
		// and d.id=$jobcard_id
		// and c.uom=$order_uom
		$details_sql=" SELECT a.trims_production,a.job_id,a.production_date,a.received_id,d.section_id,b.qc_qty,b.job_dtls_id,b.uom as product_uom,c.conv_factor,c.job_quantity,c.uom,c.job_no_mst,c.break_id,c.item_description,c.color_id,c.size_id,d.order_no,d.party_id,d.within_group,d.trims_job,c.order_uom
		from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d
		where  a.id=b.mst_id and c.id=b.job_dtls_id and c.mst_id=d.id and a.entry_form=269 and d.entry_form=257 and d.received_id =$wo_id and d.section_id=$section and c.uom=$order_uom  and a.status_active=1 and b.status_active=1 and c.status_active=1 $where_con_prod $sql_cond  
		group by a.trims_production,a.job_id,a.production_date,a.received_id,d.section_id,b.qc_qty,b.job_dtls_id,b.uom,c.conv_factor,c.job_quantity,c.uom,c.job_no_mst,c.break_id,c.item_description,c.color_id,c.size_id,d.order_no,d.party_id,d.within_group,d.trims_job,c.order_uom";
		//echo $details_sql;
		$sql_result=sql_select($details_sql); $t=1;
		$total_qc_qty=0;
		$total_qc_amt=0;
		foreach($sql_result as $row)
		{
			$qc_qty = $row[csf('qc_qty')]/$row[csf('conv_factor')];
			//$amount = ($qc_qty*$rate)/$exchange_rate;
			// $amount = ($qc_qty*$rate);
			$breakIdArr=explode(",",$row[csf('break_id')]);
			$rate=$orderRateArr[$breakIdArr[0]];
			$amount = ($qc_qty*$rate);

			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$delevery_amt=$row[csf("delevery_qty")]*$rate; ?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onClick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t; ?></p></td>
                <td><p><?php echo $row[csf('trims_production')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('production_date')]); ?></p></td>
                <td><p><?php echo $buyerArr[$row[csf('party_id')]]; ?></p></td>
                <td><p><?php echo $row[csf('trims_job')]; ?></p></td>
                <td><p><?php echo $trims_section[$section]; ?></p></td>
                <td><p><?php echo $row[csf('item_description')]; ?></p></td>
                <td><p><?php echo $colorNameArr[$row[csf('color_id')]]; ?></p></td>
                <td><p><?php echo $sizeArr[$row[csf('size_id')]]; ?></p></td>
                <td><p><?php echo $unit_of_measurement[$pop_order_uom]; ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($qc_qty, 2); ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($amount, 4); ?></p></td>
            </tr>
            <?php
            $total_qty += $qc_qty;
            $total_amt += $amount;
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
               	<th>&nbsp;</th>
               	<th>&nbsp;</th>
               	<th>&nbsp;</th>
                <th>Total</th>
               	<th align="right"><?php echo number_format($total_qty, 2); ?></th>
              	<th align="right"><?php echo number_format($total_amt, 4); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

if($action=='today_issue_qty_popup') 
{
	echo load_html_head_contents('Popup Info', '../../../../', 1, 1, $unicode);
	extract($_REQUEST);
	// $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	//$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name');
	//$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	//$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	//$sizeArr = return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", 'id', 'size_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$store_arr = return_library_array("select id, store_name from lib_store_location ", 'id', 'store_name');
	//txt_date_from  txt_date_to

	if($db_type==0)
	{
		$from_date=change_date_format($form_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$from_date=change_date_format($form_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	if(str_replace("'","", $from_date)!="" and str_replace("'","", $to_date)!="")
	{	
		$where_con=" and a.issue_date between '$from_date' and '$to_date'";
		$where_con_req=" and e.issue_date between '$from_date' and '$to_date'";
		$where_con_rtn.=" and d.receive_date between '$from_date' and '$to_date'";
	}
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="1195" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">S.L</th>
                <th width="100">System ID</th>
                <th width="80">Issue Date</th>
                <th width="100">Req. No</th>
                <th width="120">Item Group</th>
                <th width="180">Item Description</th>
                <th width="150">Store</th>
                <th width="80">Issue Qty</th>
                <th width="80">Issue Return</th>
                <th width="80">Net Issue</th>
                <th width="50">UOM</th>
                <th width="50">Rate</th>
                <th>Amount [$]</th>
            </tr>
        </thead>
        <tbody>
		<?php
		$details_sql = "(SELECT distinct g.received_id,e.id as issue_id, e.issue_date,e.issue_number,e.req_no,f.cons_quantity as issue_qty, f.cons_rate,f.store_id ,f.prod_id,f.cons_uom from trims_raw_mat_requisition_mst a,trims_raw_mat_requisition_dtls b,inv_issue_master e,inv_transaction f,trims_job_card_mst g,trims_job_card_dtls h
		where a.entry_form in (427,501) and a.job_id=g.id and a.id = b.mst_id and a.job_no = g.trims_job and a.id = e.req_id and e.id=f.mst_id and b.product_id = f.prod_id and b.mst_id = e.req_id and b.job_no = g.trims_job and e.req_no=a.requisition_no and g.id=h.mst_id and f.transaction_type = 2 and e.issue_basis = 7 and e.entry_form = 265 and a.status_active = 1 and b.status_active = 1 and e.status_active = 1 and f.status_active = 1 $job_card_cond and g.received_id in ($wo_id) and h.uom=$pop_order_uom $where_con_req
		union all
		SELECT distinct c.received_id ,a.id as issue_id,a.issue_date,a.issue_number,a.req_no,b.cons_quantity as issue_qty, b.cons_rate,b.store_id,b.prod_id,b.cons_uom from inv_issue_master a, inv_transaction b, trims_job_card_mst c , trims_job_card_dtls d  where a.id=b.mst_id and a.req_id=c.id  and c.id=d.mst_id and b.transaction_type=2 and a.issue_basis=15 and a.entry_form=265 $job_card_condd and c.received_id in ($wo_id) and d.uom=$pop_order_uom $where_con 
		) order by issue_number";



  /*$issue_sql = "SELECT distinct g.received_id,e.issue_date,f.cons_quantity as issue_qty, f.cons_rate,f.prod_id,e.id as issue_id ,h.uom
		from trims_raw_mat_requisition_mst a,trims_raw_mat_requisition_dtls b,inv_issue_master e,inv_transaction f,trims_job_card_mst g,trims_job_card_dtls h
		where a.entry_form in (427,501) and a.job_id=g.id and a.id = b.mst_id and a.job_no = g.trims_job and a.id = e.req_id and e.id=f.mst_id and b.product_id = f.prod_id and b.mst_id = e.req_id and b.job_no = g.trims_job and e.req_no=a.requisition_no  and g.id=h.mst_id   and f.transaction_type = 2 and e.issue_basis = 7 and e.entry_form = 265 and a.status_active = 1 and b.status_active = 1 and e.status_active = 1 and f.status_active = 1 and g.status_active = 1  and g.received_id in (select poid from tmp_poid where userid=$user_id and type=555)
		union all
		SELECT distinct c.received_id,a.issue_date, b.cons_quantity as issue_qty, b.cons_rate,b.prod_id,a.id as issue_id,d.uom
		from inv_issue_master a, inv_transaction b, trims_job_card_mst c , trims_job_card_dtls d 
		where a.id=b.mst_id and a.req_id=c.id  and c.id=d.mst_id and b.transaction_type=2 and a.issue_basis=15 and a.entry_form=265  and c.received_id in (select poid from tmp_poid where userid=$user_id and type=555)";   */

		$sql_result=sql_select($details_sql); $t=1; $all_prod_id='';
		foreach($sql_result as $row)
		{
			$all_prod_id .=$row[csf('prod_id')].',';
		}
		$all_prod_ids=implode(",",array_unique(explode(",",(chop($all_prod_id,',')))));
		$prodData=sql_select("SELECT a.id,a.item_group_id, a.item_description from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id in ($all_prod_ids)");
		foreach($prodData as $row)
		{
			$prodData_arr[$row[csf('id')]]['item_group_id']=$item_group_arr[$row[csf('item_group_id')]];
			$prodData_arr[$row[csf('id')]]['item_description']=$row[csf('item_description')];
		}

		$issue_return_sql = "SELECT a.id as issue_id,a.issue_date, b.cons_quantity as rtn_qty, b.cons_rate,b.prod_id,d.receive_date from inv_issue_master a, inv_transaction b, inv_receive_master d where b.transaction_type=4 and a.entry_form=265 and d.id=b.mst_id and a.id=b.issue_id and d.entry_form=266 and a.id in ($issue_ids) $where_con_rtn";
		$iss_rtn_data_array=sql_select($issue_return_sql);
		$issue_rtn_qty_arr=array();
		foreach($iss_rtn_data_array as $row){
			$issue_rtn_qty_arr[$row[csf("issue_id")]]['qty']+=$row[csf("rtn_qty")];
			$issue_rtn_qty_arr[$row[csf("issue_id")]]['amount']+=$row[csf("rtn_qty")]*$row[csf("cons_rate")];
			if($txt_date_from!="" and $txt_date_to!="")
			{
				if(($row[csf('receive_date')]>$txt_date_from) && ($txt_date_to < $row[csf('receive_date')])){
			    	$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_qty']+=$row[csf("rtn_qty")];
					$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_amount']+=$row[csf("rtn_qty")]*$row[csf("cons_rate")];
				}
		    }else{
		    	$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_qty']+=$row[csf("rtn_qty")];
				$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_amount']+=$row[csf("rtn_qty")]*$row[csf("cons_rate")];
		    }
		}
		/*echo '<pre>';
		print_r($date_wise_issue_rnn_qty_arr);*/
		$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and company_id = $company and  con_date=(select max(con_date) as con_date from currency_conversion_rate where currency=2 and status_active = 1 and company_id = $company)" , "conversion_rate" );

		$total_qc_qty=0;
		$total_qc_amt=0;
		foreach($sql_result as $row)
		{
			//echo $row[csf("issue_id")].'=='.$row[csf("prod_id")].'=='.$row[csf('issue_qty')].'=='.$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_qty'].'++';
			$issue_rtn_qty = $date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_qty'];
			$net_issue_qty = $row[csf('issue_qty')]-$issue_rtn_qty;
            $amount = (($row[csf('issue_qty')]*$row[csf('cons_rate')])-$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_amount'])/$exchange_rate;
            if($currency_id == 1){
               $amount = (($row[csf('issue_qty')]*$row[csf('cons_rate')])-$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_amount'])/$currency_rate;
            }
			$rate = $amount / $net_issue_qty;
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$delevery_amt=$row[csf("delevery_qty")]*$rate; ?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onClick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t; ?></p></td>
                <td><p><?php echo $row[csf('issue_number')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('issue_date')]); ?></p></td>
                <td><p><?php echo $row[csf('req_no')]; ?></p></td>
                <td><p><?php echo $prodData_arr[$row[csf('prod_id')]]['item_group_id']; ?></p></td>
                <td><p><?php echo $prodData_arr[$row[csf('prod_id')]]['item_description']; ?></p></td>
                <td><p><?php echo $store_arr[$row[csf('store_id')]]; ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($row[csf('issue_qty')], 2); ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($issue_rtn_qty, 2); ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($net_issue_qty, 2); ?></p></td>
                <td><p><?php echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></p></td>
                <td style="text-align: right;" title="<?=$rate?>"><p><?php echo number_format($rate, 5); ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($amount, 4); ?></p></td>
            </tr>
            <?php
            $total_issue_qty += $row[csf('issue_qty')];
            $total_issue_rtn_qty += $issue_rtn_qty;
            $total_net_issue_qty += $net_issue_qty;
            $total_amt += $amount;
			$t++;
		}

		?>
        </tbody>
        <tfoot>
        	<tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
               	<th>&nbsp;</th>
               	<th>&nbsp;</th>
               	<th>&nbsp;</th>
                <th>Total</th>
               	<th align="right"><?php echo number_format($total_issue_qty, 2); ?></th>
               	<th align="right"><?php echo number_format($total_issue_rtn_qty, 2); ?></th>
               	<th align="right"><?php echo number_format($total_net_issue_qty, 2); ?></th>
               	<th>&nbsp;</th>
               	<th>&nbsp;</th>
              	<th align="right"><?php echo number_format($total_amt, 4); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
}

if($action=='issue_qty_popup') {
	echo load_html_head_contents('Popup Info', '../../../../', 1, 1, $unicode);
	extract($_REQUEST);
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$store_arr = return_library_array("select id, store_name from lib_store_location ", 'id', 'store_name');
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
	<style>
		tbody tr td {
			text-align: center;
		}
	</style>
    <table width="1195" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">S.L</th>
                <th width="100">System ID</th>
                <th width="80">Issue Date</th>
                <th width="100">Req. No</th>
                <th width="120">Item Group</th>
                <th width="180">Item Description</th>
                <th width="150">Store</th>
                <th width="80">Issue Qty</th>
                <th width="80">Issue Return</th>
                <th width="80">Net Issue</th>
				<th width="50">UOM</th>
                <th width="50">Rate</th>
                <th>Amount [$]</th>
            </tr>
        </thead>
        <tbody>
		<?php
		
		//echo $company; die;
		
		$details_sql = "(SELECT distinct g.received_id,e.id as issue_id, e.issue_date,e.issue_number,e.req_no,f.cons_quantity as issue_qty, f.cons_rate,f.store_id ,f.prod_id,f.cons_uom from trims_raw_mat_requisition_mst a,trims_raw_mat_requisition_dtls b,inv_issue_master e,inv_transaction f,trims_job_card_mst g,trims_job_card_dtls h
		where a.entry_form in (427,501) and a.job_id=g.id and a.id = b.mst_id and a.job_no = g.trims_job and a.id = e.req_id and e.id=f.mst_id and b.product_id = f.prod_id and b.mst_id = e.req_id and b.job_no = g.trims_job and e.req_no=a.requisition_no and g.id=h.mst_id and f.transaction_type = 2 and e.issue_basis = 7 and e.entry_form = 265 and a.status_active = 1 and b.status_active = 1 and e.status_active = 1 and f.status_active = 1 $job_card_cond and g.received_id in ($wo_id) and h.uom=$pop_order_uom  
		union all
		SELECT distinct c.received_id ,a.id as issue_id,a.issue_date,a.issue_number,a.req_no,b.cons_quantity as issue_qty, b.cons_rate,b.store_id,b.prod_id,b.cons_uom from inv_issue_master a, inv_transaction b, trims_job_card_mst c , trims_job_card_dtls d  where a.id=b.mst_id and a.req_id=c.id  and c.id=d.mst_id and b.transaction_type=2 and a.issue_basis=15 and a.entry_form=265 $job_card_condd and c.received_id in ($wo_id) and d.uom=$pop_order_uom  
		) order by issue_number";

		/*$details_sql = "(SELECT g.received_id,e.id as issue_id,e.issue_date,e.issue_number,e.req_no,f.cons_quantity as issue_qty, f.cons_rate,f.store_id ,f.prod_id,f.cons_uom from trims_raw_mat_requisition_mst a,trims_raw_mat_requisition_dtls b,inv_issue_master e,inv_transaction f,trims_job_card_mst g
		where a.entry_form in (427,501)  and a.job_id=g.id and a.id = b.mst_id and a.job_no = g.trims_job and a.id = e.req_id and e.id=f.mst_id and b.product_id = f.prod_id and b.mst_id = e.req_id and b.job_no = g.trims_job and e.req_no=a.requisition_no and f.transaction_type = 2 and e.issue_basis = 7 and e.entry_form = 265 and a.status_active = 1 and b.status_active = 1 and e.status_active = 1 and f.status_active = 1 $job_card_cond and g.received_id in ($wo_id)
		union all
		SELECT c.received_id,a.id as issue_id,a.issue_date,a.issue_number,a.req_no,b.cons_quantity as issue_qty, b.cons_rate,b.store_id,b.prod_id,b.cons_uom from inv_issue_master a, inv_transaction b, trims_job_card_mst c where a.id=b.mst_id and a.req_id=c.id and b.transaction_type=2 and a.issue_basis=15 and a.entry_form=265 $job_card_condd and c.received_id in ($wo_id)) order by issue_number";*/

        $currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and company_id = $company and  con_date=(select max(con_date) as con_date from currency_conversion_rate where currency=2 and status_active = 1 and company_id = $company)" , "conversion_rate" );
		
		//echo  "select max(con_date) as con_date from currency_conversion_rate where currency=2 and status_active = 1 and company_id = $company"; die;

		$sql_result=sql_select($details_sql); $t=1; $all_prod_id='';
		foreach($sql_result as $row)
		{
			$all_prod_id .=$row[csf('prod_id')].',';
		}
		$all_prod_ids=implode(",",array_unique(explode(",",(chop($all_prod_id,',')))));
		$prodData=sql_select("SELECT a.id,a.item_group_id, a.item_description from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.id in ($all_prod_ids)");
		foreach($prodData as $row)
		{
			$prodData_arr[$row[csf('id')]]['item_group_id']=$item_group_arr[$row[csf('item_group_id')]];
			$prodData_arr[$row[csf('id')]]['item_description']=$row[csf('item_description')];
		}

		$issue_return_sql = "SELECT a.id as issue_id,a.issue_date, b.cons_quantity as rtn_qty, b.cons_rate,b.prod_id,d.receive_date from inv_issue_master a, inv_transaction b, inv_receive_master d where b.transaction_type=4 and a.entry_form=265 and d.id=b.mst_id and a.id=b.issue_id and d.entry_form=266 and a.id in ($issue_ids)";
		$iss_rtn_data_array=sql_select($issue_return_sql);
		$issue_rtn_qty_arr=array();
		foreach($iss_rtn_data_array as $row){
			$issue_rtn_qty_arr[$row[csf("issue_id")]]['qty']+=$row[csf("rtn_qty")];
			$issue_rtn_qty_arr[$row[csf("issue_id")]]['amount']+=$row[csf("rtn_qty")]*$row[csf("cons_rate")];
			if($txt_date_from!="" and $txt_date_to!="")
			{
				if(($row[csf('receive_date')]>$txt_date_from) && ($txt_date_to < $row[csf('receive_date')])){
			    	$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_qty']+=$row[csf("rtn_qty")];
					$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_amount']+=$row[csf("rtn_qty")]*$row[csf("cons_rate")];
				}
		    }else{
		    	$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_qty']+=$row[csf("rtn_qty")];
				$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_amount']+=$row[csf("rtn_qty")]*$row[csf("cons_rate")];
		    }
		}
		$total_qc_qty=0;
		$total_qc_amt=0;
		foreach($sql_result as $row)
		{
			$issue_rtn_qty = $date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_qty'];
			$net_issue_qty = $row[csf('issue_qty')]-$issue_rtn_qty;
   			$amount = (($row[csf('issue_qty')]*$row[csf('cons_rate')])-$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_amount'])/$exchange_rate;
            $rate = $row[csf('cons_rate')]/$exchange_rate;
            if($currency_id == 1)
			{
                $amount = (($row[csf('issue_qty')]*$row[csf('cons_rate')])-$date_wise_issue_rnn_qty_arr[$row[csf("issue_id")]][$row[csf("prod_id")]]['today_amount'])/$currency_rate;
                $rate = $row[csf('cons_rate')]/$currency_rate;
            }
			
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$delevery_amt=$row[csf("delevery_qty")]*$rate; ?>
        	<tr bgcolor="<?php echo $bgcolor; ?>" onClick="change_color('tr_<?php echo $t; ?>','<?php echo $bgcolor; ?>')" id="tr_<?php echo $t; ?>">
        		<td><p><?php echo $t; ?></p></td>
                <td><p><?php echo $row[csf('issue_number')]; ?></p></td>
                <td><p><?php echo change_date_format($row[csf('issue_date')]); ?></p></td>
                <td><p><?php echo $row[csf('req_no')]; ?></p></td>
                <td><p><?php echo $prodData_arr[$row[csf('prod_id')]]['item_group_id']; ?></p></td>
                <td><p><?php echo $prodData_arr[$row[csf('prod_id')]]['item_description']; ?></p></td>
                <td><p><?php echo $store_arr[$row[csf('store_id')]]; ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($row[csf('issue_qty')], 2); ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($issue_rtn_qty, 2); ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($net_issue_qty, 2); ?></p></td>
                <td><p><?php echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></p></td>
                <td style="text-align: right;" title="<?=$rate?>"><p><?php echo number_format($rate, 4); ?></p></td>
                <td style="text-align: right;"><p><?php echo number_format($amount, 4); ?></p></td>
            </tr>
            <?php
            $total_issue_qty += $row[csf('issue_qty')];
            $total_issue_rtn_qty += $issue_rtn_qty;
            $total_net_issue_qty += $net_issue_qty;
            $total_amt += $amount;
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
               	<th>&nbsp;</th>
               	<th>&nbsp;</th>
               	<th>&nbsp;</th>
                <th>Total</th>
				<th align="right"><?php echo number_format($total_issue_qty, 2); ?></th>
               	<th align="right"><?php echo number_format($total_issue_rtn_qty, 2); ?></th>
               	<th align="right"><?php echo number_format($total_net_issue_qty, 2); ?></th>
               	<th>&nbsp;</th>
               	<th>&nbsp;</th>
              	<th align="right"><?php echo number_format($total_amt, 4); ?></th>
            </tr>
        </tfoot>
    </table>
    <?php
    exit;
}


?>