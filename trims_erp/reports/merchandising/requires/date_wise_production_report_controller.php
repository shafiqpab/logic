<?
header('Content-type:text/html; charset=utf-8');
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company;?>+'_'+<? echo $party_name;?>+'_'+<? echo $customer_source;?>, 'create_booking_search_list_view', 'search_div', 'date_wise_production_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
	////////////////////////////////////////////////
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
	
	if($cbo_section_id){$where_con.=" and a.section_id='$cbo_section_id'";} 
	if($cbo_sub_section_id){$where_con.=" and c.sub_section='$cbo_sub_section_id'";} 
	if($cbo_customer_source){$where_con.=" and d.within_group='$cbo_customer_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";} 
	if($cbo_location_name){$where_con.=" and a.location_id = $cbo_location_name";}
	
	//////////////////////////////////////////////////////////////////
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");


	$size_name_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	 
	$machine_noArr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0","id","machine_no");
	$location_arr = return_library_array("select id, location_name from lib_location where status_active=1 and is_deleted=0","id","location_name");

	//////////////////////////////////////////////////////////////////
	
	if($txt_date_from!="" and $txt_date_to!="")
	{	
		$where_con.=" and a.production_date between '$txt_date_from' and '$txt_date_to'";
	}
	
	if(trim($txt_order_no)!="")
	{
			//$sql_cond= " and d.order_no=$hid_order_id";
			//CustomerName LIKE 'a%';	 
			$sql_cond="and d.order_no like '%$txt_order_no%'";
			$sql_order_no="and a.order_no like '%$txt_order_no%'";
	}
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1  and id in(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)" , "conversion_rate" ); 

	$width=3000;
	$width_second=2380;
	ob_start();
	if ($report_type==1) {
		//echo $currency_rate; die;
		$order_sql="select a.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,c.qnty,c.order_id ,c.qnty,c.rate,c.amount,c.booked_qty,c.description,c.id as breakdown_id, a.trims_ref
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
		where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $sql_order_no ";
		$order_sql_result = sql_select($order_sql);
		$order_array=array();
		$booked_array=array();
		$booked_sammary_array=array();
		foreach($order_sql_result as $row)
		{
			$order_array[$row[csf('id')]]['receive_date']=$row[csf('receive_date')]; 
			$order_array[$row[csf('id')]]['delivery_date']=$row[csf('delivery_date')];
			$order_array[$row[csf('id')]]['cust_order_no']=$row[csf('cust_order_no')];
			$order_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
			$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
			$order_array[$row[csf('id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			$order_array[$row[csf('id')]]['within_group']=$row[csf('within_group')];
			$order_array[$row[csf('id')]]['exchange_rate']=$row[csf('exchange_rate')];
			$order_array[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
			$order_array[$row[csf('id')]]['trims_ref']=$row[csf('trims_ref')];
			$booked_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]][$row[csf('id')]]['order_quantity']+=$row[csf('qnty')];
			$booked_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]][$row[csf('id')]]['amount']+=$row[csf('amount')];
			
			$booked_sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['order_quantity']+=$row[csf('qnty')];
			$booked_sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['amount']+=$row[csf('amount')];
		}
		//echo "<pre>";
		//print_r($booked_array)."mahbub";
		$trims_order_sql=" select a.trims_production,a.job_id,a.location_id,a.party_id,a.production_date,a.received_id,d.section_id,b.qc_qty,b.job_dtls_id,b.uom as product_uom,c.sub_section,b.machine_id,c.conv_factor,c.job_quantity,c.uom ,c.buyer_po_no,c.item_description,c.job_no_mst,c.break_id,d.order_no,c.book_con_dtls_id
		from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d
		where  a.id=b.mst_id and c.id=b.job_dtls_id and c.mst_id=d.id and  a.entry_form=269 and  d.entry_form=257  and   a.company_id =$cbo_company_id  and  a.status_active=1 and b.status_active=1 and c.status_active=1 $where_con $sql_cond
		group by a.trims_production,a.job_id,a.location_id,a.party_id,a.production_date,a.received_id,d.section_id,b.job_dtls_id,c.sub_section,c.conv_factor,c.job_quantity,c.uom,c.buyer_po_no,c.item_description,c.job_no_mst,b.qc_qty,c.break_id,b.uom,b.machine_id,d.order_no,c.book_con_dtls_id"; 
		//echo $trims_order_sql; die;
		$result = sql_select($trims_order_sql);
	    $date_array=array();
	    
		$wo_book_con_dtls_id_arr=array();
	    $job_dtls_arr=array();
	    $buyer_po_arr=array();
	    foreach($result as $row)
	    {
	    	$buyer_po_arr[] = $row[csf('break_id')];
	    	$job_dtls_arr[] = $row[csf('job_dtls_id')];
	    	$job_id_arr[] = $row[csf('job_id')];
	   	 	$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['section_id']=$row[csf('section_id')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['sub_section']=$row[csf('sub_section')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['production_date']=$row[csf('production_date')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['job_dtls_id']=$row[csf('job_dtls_id')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['location_id']=$row[csf('location_id')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['received_id']=$row[csf('received_id')];
	   		$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['job_quantity']=$row[csf('job_quantity')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['uom']=$row[csf('uom')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['conv_factor']=$row[csf('conv_factor')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['buyer_po_no']=$row[csf('buyer_po_no')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['item_description']=$row[csf('item_description')];			            
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['job_no_mst']=$row[csf('job_no_mst')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['qc_qty']+=$row[csf('qc_qty')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['break_id']=$row[csf('break_id')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['product_uom']=$row[csf('product_uom')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['machine_id']=$row[csf('machine_id')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['order_no']=$row[csf('order_no')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['trims_ref']=$row[csf('trims_ref')];
			$date_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['book_con_dtls_id'].=$row[csf('book_con_dtls_id')].',';

			if($row[csf('book_con_dtls_id')]){$wo_book_con_dtls_id_arr[$row[csf('book_con_dtls_id')]]=$row[csf('book_con_dtls_id')];}
		}
		$wo_book_con_dtls_id=where_con_using_array($wo_book_con_dtls_id_arr,0,'id');
		$main_job_arr = return_library_array("select id, job_no from wo_trim_book_con_dtls where status_active=1 and is_deleted=0 $wo_book_con_dtls_id","id","job_no");
		$po_str = implode(',', $buyer_po_arr);
		$po_sql ="select id, grouping from wo_po_break_down where is_deleted=0 and status_active=1 and id in($po_str) and grouping is not null";
		$po_sql_res=sql_select($po_sql);
		$grouping_arr = array();
		foreach ($po_sql_res as $row)
		{
			$grouping_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
		}
		
		unset($po_sql_res);




		$job_dtls_ids = implode(',', $job_dtls_arr);
		$job_ids = implode(',', $job_id_arr);

		/*$trims_order_sql2=" select a.job_id, b.qc_qty,b.job_dtls_id
		from trims_production_mst a,trims_production_dtls b
		where  a.id=b.mst_id and  a.entry_form=269 and b.job_dtls_id in($job_dtls_ids) and   a.company_id =$cbo_company_id  and  a.status_active=1 and b.status_active=1
		group by b.qc_qty,b.job_dtls_id"; */
		$trims_order_sql2=" select a.job_id,a.location_id,a.party_id,a.production_date,a.received_id,d.section_id,b.qc_qty,b.job_dtls_id,b.uom as product_uom,c.sub_section,b.machine_id,c.conv_factor,c.job_quantity,c.uom ,c.buyer_po_no,c.item_description,c.job_no_mst,c.break_id,d.order_no
		from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d
		where  a.id=b.mst_id and c.id=b.job_dtls_id and c.mst_id=d.id and  a.entry_form=269 and  d.entry_form=257  and   a.company_id =$cbo_company_id  and  a.status_active=1 and b.status_active=1 and c.status_active=1 and a.job_id in($job_ids) $sql_cond
		group by a.job_id,a.location_id,a.party_id,a.production_date,a.received_id,d.section_id,b.job_dtls_id,c.sub_section,c.conv_factor,c.job_quantity,c.uom,c.buyer_po_no,c.item_description,c.job_no_mst,b.qc_qty,c.break_id,b.uom,b.machine_id,d.order_no";
		//echo $trims_order_sql2; die;
		$result2 = sql_select($trims_order_sql2);
	    $date_array2=array();
	    foreach($result2 as $row)
	    {
			//$date_array2[$row[csf('job_dtls_id')]]['total_qc_qty']+=$row[csf('qc_qty')];

			$date_array2[$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('job_no_mst')]]['total_qc_qty']+=$row[csf('qc_qty')];

		}
		//echo "<pre>";
		//print_r($date_array);
	








		?>	
		<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
	    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
					<thead class="form_caption" >
						<tr>
							<td colspan="35" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
						</tr>
						<tr>
							<td colspan="35" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
						</tr>
						<tr>
							<td colspan="35" align="center" style="font-size:14px; font-weight:bold">
								<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
							</td>
						</tr>
					</thead>
				</table>
	            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead>
	                    <th width="35">SL</th>
	                    <th width="100">Section</th>
	                    <th width="100">Sub-Section</th>
	                    <th width="100">Prod. date</th>
	                    <th width="100">Order Rcv date</th>
	                    <th width="100">Tgt.Trims Delv.date</th>
	                    <th width="100">Main Job No</th>
	                    <th width="100">Work Order No</th>
	                    <th width="100">Internal Ref</th>
	                	<th width="100">Trims Ref</th>
	                    <th width="100">Party Name</th>
	                    <th width="100">Buyer Name</th>
	                    <th width="100">Order Number</th>
	                    <th width="150">Item Description</th>
	                    <th width="100">WO Qty</th>
	                    <th width="60">WO UOM</th>
	                    <th width="60">U/Price (TK)</th>
	                    <th width="60">U/Price ($)</th>
	                    <th width="100">WO Value (TK)</th>
	                    <th width="100">WO Value ($)</th>
	                    <th width="100">Job No</th>
	                    <th width="80">Conv. Factor</th>
	                    <th width="80">Job/Book Qty.</th>
	                    <th width="80">BK/Prod UOM</th>
	                    <th width="100">Prod. Qty</th>
	                    <th width="100">Eqv. Prod to WO UOM</th>
	                    <th width="150">MC/No</th>
	                    <th width="100">Prod Value (TK)</th>
	                    <th width="100">Prod Value ($)</th>
	                    <th width="100">Material Issue ($)</th>
	                    <th>Remarks</th>
					</thead>
				</table>
	        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
	        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
	            <? 
					$i=1;
					$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
					foreach($date_array as $prod_date=>$prod_date_arr)
					{
						foreach($prod_date_arr as $section_key_id=>$section_data)
						{
							foreach($section_data as $sub_section_key_id=>$sub_section_key_data)
							{
								foreach($sub_section_key_data as $item_description_id=>$item_description_data)
								{
									foreach($item_description_data as $order_no_id=>$order_no_data)
									{
										foreach($order_no_data as $job_no_mst_id=>$row)
										{
											$orderquantity=$booked_array[$row[section_id]][$row[sub_section]][$row[item_description]][$row[order_no]][$row[received_id]]['order_quantity'];
											$orderamount=$booked_array[$row[section_id]][$row[sub_section]][$row[item_description]][$row[order_no]][$row[received_id]]['amount'];
											$rate=number_format($orderamount/$orderquantity,4);
											$currency_id=$order_array[$row[received_id]]['currency_id'];
											if($currency_id==1)
											{
												 $takarate=$rate;
												 $orderamounttaka=$orderamount;
												 $usdrate=number_format($rate/$currency_rate,4);
												 $orderamountusd=$orderamount/$currency_rate;
												 
												 $product_valu_taka=$row[qc_qty]/$row[conv_factor]*$takarate;
												 
												 $product_valu_usd=$row[qc_qty]/$row[conv_factor]*$usdrate;
												 
											}
											elseif($currency_id==2)
											{
												$takarate=number_format($rate*$currency_rate,4);
												$orderamounttaka=$orderamount*$currency_rate;
												$usdrate=$rate;
												$orderamountusd=$orderamount;
												
												$product_valu_taka=$row[qc_qty]/$row[conv_factor]*$takarate;
												$product_valu_usd=$row[qc_qty]/$row[conv_factor]*$usdrate;
											}

											$po_id_arr = explode(',', $row[break_id]);
											foreach ($po_id_arr as $value) {
												$grouping = $buyer_po_arr[$value]['grouping'];
											}
											$book_con_dtls_id_arr = explode(',', rtrim($row[book_con_dtls_id],','));
											$main_job ='';
											foreach ($book_con_dtls_id_arr as $value) {
												$main_job = $main_job_arr[$value].',';
											}
										?>
						                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						                	<td width="35"  align="center"><? echo $i;?></td>
						                    <td width="100" style="word-break: break-all;" align="left"><? echo $trims_section[$row[section_id]];?></td>
						                    <td width="100" style="word-break: break-all;" align="left"><? echo $trims_sub_section[$row[sub_section]];?></td>
						                    <td width="100" style="word-break: break-all;" align="center"><? echo $row[production_date];?></td>
						                    <td width="100" style="word-break: break-all;" align="center"><? echo $order_array[$row[received_id]]['receive_date'] ;?></td>
						                    <td width="100" style="word-break: break-all;" align="center"><? echo $order_array[$row[received_id]]['delivery_date']; ?></td>
						                    <td width="100" style="word-break: break-all;" align="left"><? echo implode(", ",array_unique(explode(",",chop($main_job,', '))));?></td>
						                    <td width="100" style="word-break: break-all;" align="left"><? echo $order_array[$row[received_id]]['cust_order_no'] ;?></td>
						                    <td width="100" style="word-break: break-all;" align="left"><?php echo $grouping; ?></td>
						                    <td width="100" style="word-break: break-all;" align="left"><?php echo $order_array[$row[received_id]]['trims_ref']; ?></td>
						                    <td width="100" style="word-break: break-all;" align="left"><? echo $party=($order_array[$row[received_id]]['within_group']==1)?$companyArr[$order_array[$row[received_id]]['party_id']]:$buyerArr[$order_array[$row[received_id]]['party_id']];?></td>
						                    <td width="100" style="word-break: break-all;" align="left"><? echo ($order_array[$row[received_id]]['within_group']==1)? $buyerArr[$order_array[$row[received_id]]['buyer_buyer']]:$order_array[$row[received_id]]['buyer_buyer'];?></td>
						                    <td width="100" style="word-break: break-all;" align="left" style="word-break:break-all;"><? echo $row[buyer_po_no]; ?></td>
						                    <td width="150" style="word-break: break-all;" align="left"><? echo $row[item_description];?></td>
						                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($orderquantity,2);?></td>
						                    <td width="60" style="word-break: break-all;" align="center"><? echo $unit_of_measurement[$order_array[$row[received_id]]['order_uom']]//$unit_of_measurement[$row[uom]];?></td>  
						                    <td width="60" style="word-break: break-all;" align="right"><? echo number_format($takarate,4);?></td>
						                    <td width="60" style="word-break: break-all;" align="right"><? echo number_format($usdrate,4);?></td>
						                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($orderamounttaka); $total_orderamount_taka+=$orderamounttaka;//echo //$orderquantity*$takarate;?></td>
						                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($orderamountusd); $total_orderamount_usd+=$orderamountusd; ?></td>
						                    <td width="100" style="word-break: break-all;" align="left"><? echo $row[job_no_mst]; ?></td>
						                    <td width="80" style="word-break: break-all;" align="right"><? echo  $conv_factor=number_format($row[conv_factor],4);?></td>
						                    <td width="80" style="word-break: break-all;" align="right"><? if ($row[conv_factor])echo number_format($orderquantity*$row[conv_factor],2); else echo "0"; //$booked_array[$row[break_id]]['booked_qty'];?></td>
						                    <td width="80" style="word-break: break-all;" align="center"><? echo $unit_of_measurement[$row[product_uom]];?></td>
						                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($row[qc_qty],2);$total_product_qnty+=$row[qc_qty];?></td>
						                    <td width="100" style="word-break: break-all;" align="right"><? if ($row[conv_factor])echo  number_format($row[qc_qty]/$row[conv_factor],2);else echo "0";?></td>
						                    <td width="150" style="word-break: break-all;" align="center"><? echo $machine_noArr[$row[machine_id]];?></td>
						                    <td width="100" style="word-break: break-all;" align="right"><? if ($row[conv_factor]) echo number_format($product_valu_taka);else echo $product_valu_taka=0; $total_product_valu_taka+=$product_valu_taka;?></td>
						                    <td width="100" style="word-break: break-all;" align="right"><? if ($row[conv_factor])echo number_format($product_valu_usd);else echo $product_valu_usd=0; $total_product_valu_usd+=$product_valu_usd;?></td>
						                    <td width="100" style="word-break: break-all;" align="center"></td>
						                    <td align="center" style="word-break: break-all;"></td>
						                </tr>
						                <? 
											$prod_sammary_array[$row[production_date]][$row[section_id]][$row[sub_section]]['qc_qty']+=$product_valu_usd;
											$i++;
										}
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
	                <th width="35"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="150"></th>
	                <th width="100"></th>
	                <th width="60"></th>
	                <th width="60"></th>
	                <th width="60"> Total:</th>
	                <th width="100"><? echo number_format($total_orderamount_taka);?></th>
	                <th width="100"><? echo number_format($total_orderamount_usd);?></th>
	                <th width="100"></th>
	                <th width="80"></th>
	                <th width="80"></th>
	                <th width="80"></th>
	                <th width="100"><? echo number_format($total_product_qnty);?></th>
	                <th width="100"></th>
	                <th width="150"></th>
	                <th width="100"><? echo number_format($total_product_valu_taka);?></th>
	                <th width="100"><? echo number_format($total_product_valu_usd);?></th>
	                <th width="100"></th>
	                <th></th>
				</tfoot>
			</table>
	    </div>
	    <div align="left" style="height:auto; width:500px; margin:0 auto; padding:0;">
	    	<table width="500" cellpadding="0" cellspacing="0" id="caption" align="left">
				<thead class="form_caption" >
					<tr>
						<td colspan="35" align="center" style="font-size:14px; font-weight:bold" ><? echo "Summary"; ?></td>
					</tr>
				</thead>
			</table>
	        <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="500" rules="all" id="rpt_table_header" align="left">
				<thead>
	                <th width="35">SL</th>
	                <th width="100">Section</th>
	                <th width="100">Sub-Section</th>
	                <th>Prod Value ($)</th>
				</thead>
			</table>
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="500" rules="all" align="left">
			<? 

			$sammary_sql=" select a.party_id,a.production_date,a.received_id,a.section_id,b.qc_qty,b.job_dtls_id,b.uom as product_uom,c.sub_section,b.machine_id,c.conv_factor,c.job_quantity,c.uom ,c.buyer_po_no,c.item_description,c.job_no_mst,c.break_id,d.order_no from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d  where  a.id=b.mst_id and c.id=b.job_dtls_id  and   c.mst_id=d.id and  a.entry_form=269 and  d.entry_form=257  and   a.company_id =$cbo_company_id  and  a.status_active=1 and b.status_active=1 and c.status_active=1 $where_con $sql_cond  group by a.party_id,a.production_date,a.received_id,a.section_id,b.job_dtls_id,c.sub_section,c.conv_factor,c.job_quantity,c.uom,c.buyer_po_no,c.item_description,c.job_no_mst,b.qc_qty,c.break_id,b.uom,b.machine_id,d.order_no"; 
			$sammary_result = sql_select($sammary_sql);
	        $sammary_array=array();
	        foreach($sammary_result as $row)
	        {
	       	 	$sammary_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]]['section_id']=$row[csf('section_id')];
				$sammary_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]]['sub_section']=$row[csf('sub_section')];
				$sammary_array[$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]]['production_date']=$row[csf('production_date')];
	 		}
			$t=1;
			foreach($sammary_array as $prod_date=>$prod_date_arr)
			{
				foreach($prod_date_arr as $section_key_id=>$section_data)
				{
					$section_total=0;
					foreach($section_data as $sub_section_key_id=>$row)
					{
						?>
	                       <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
	                        <td width="35"  align="center"><? echo $t;?></td>
	                        <td width="100" align="center"><? echo $trims_section[$row[section_id]];?></td>
	                        <td width="100" align="center"><? echo $trims_sub_section[$row[sub_section]];?></td>
	                        <td align="right"><? echo $product_valu_usd=number_format($prod_sammary_array[$row[production_date]][$row[section_id]][$row[sub_section]]['qc_qty']);$section_total+=$prod_sammary_array[$row[production_date]][$row[section_id]][$row[sub_section]]['qc_qty'];?></td>
	                       </tr>
						<? 
					$t++;
					}
					?>
	                <tr style="background-color:#CCC">
	               	 	<td colspan="3" align="right"><b><? echo $trims_section[$row[section_id]];?> Total</b></td>
	                	<td align="right"><b><? echo number_format($section_total);?></b></td>
	                </tr>
	             	<?
	            }
			} 
			?>
			</table>
	    </div>
	<?
	}
	else
	{

		//echo $currency_rate; die;
		$order_sql="select a.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.buyer_style_ref,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,c.qnty,c.order_id ,c.qnty,c.rate,c.amount,c.booked_qty,c.description,c.style,c.color_id, c.size_id,c.id as breakdown_id, a.trims_ref
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
		where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $sql_order_no ";
		$order_sql_result = sql_select($order_sql);
		$order_array=array();
		$booked_array=array();
		$booked_sammary_array=array();
		foreach($order_sql_result as $row)
		{
			$order_array[$row[csf('id')]]['receive_date']=$row[csf('receive_date')]; 
			$order_array[$row[csf('id')]]['delivery_date']=$row[csf('delivery_date')];
			$order_array[$row[csf('id')]]['cust_order_no']=$row[csf('cust_order_no')];
			$order_array[$row[csf('id')]]['party_id']=$row[csf('party_id')];
			$order_array[$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
			$order_array[$row[csf('id')]]['buyer_buyer']=$row[csf('buyer_buyer')];
			$order_array[$row[csf('id')]]['within_group']=$row[csf('within_group')];
			$order_array[$row[csf('id')]]['exchange_rate']=$row[csf('exchange_rate')];
			$order_array[$row[csf('id')]]['currency_id']=$row[csf('currency_id')];
			$order_array[$row[csf('id')]]['trims_ref']=$row[csf('trims_ref')];
			$order_array[$row[csf('id')]]['buyer_style_ref']=$row[csf('buyer_style_ref')];
			//$order_array[$row[csf('id')]]['style']=$row[csf('style')];

			//[$row[csf('style')]][$row[csf('color_id')]][$row[csf('size_id')]]

			$booked_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]][$row[csf('id')]][$row[csf('color_id')]][$row[csf('size_id')]]['order_quantity']+=$row[csf('qnty')];
			$booked_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]][$row[csf('id')]][$row[csf('color_id')]][$row[csf('size_id')]]['style'].=$row[csf('style')].',';
			$booked_array[$row[csf('section')]][$row[csf('sub_section')]][$row[csf('description')]][$row[csf('order_no')]][$row[csf('id')]][$row[csf('color_id')]][$row[csf('size_id')]]['amount']+=$row[csf('amount')];
			
			//$booked_sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['order_quantity']+=$row[csf('qnty')];
			//$booked_sammary_array[$row[csf('section')]][$row[csf('sub_section')]]['amount']+=$row[csf('amount')];
		}
		//echo "<pre>";
		//print_r($booked_array)."mahbub";
		$trims_order_sql=" select a.company_id,a.trims_production,a.job_id,a.location_id,a.party_id,a.production_date,a.received_id,d.section_id,b.qc_qty,b.color_id, b.size_id,b.job_dtls_id,b.uom as product_uom,c.sub_section,b.machine_id,c.conv_factor,c.job_quantity,c.uom ,c.buyer_po_no,c.item_description,c.job_no_mst,c.break_id,d.order_no
		from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d
		where  a.id=b.mst_id and c.id=b.job_dtls_id and c.mst_id=d.id and  a.entry_form=269 and  d.entry_form=257  and   a.company_id =$cbo_company_id  and  a.status_active=1 and b.status_active=1 and c.status_active=1 $where_con $sql_cond
		group by a.company_id,a.trims_production,a.job_id,a.location_id,a.party_id,a.production_date,a.received_id,d.section_id,b.job_dtls_id,c.sub_section,c.conv_factor,c.job_quantity,c.uom,c.buyer_po_no,c.item_description,c.job_no_mst,b.qc_qty,b.color_id, b.size_id,c.break_id,b.uom,b.machine_id,d.order_no"; 
		//echo $trims_order_sql; die;

		$result = sql_select($trims_order_sql);
	    $date_array=array();
	    
	    //$job_dtls_arr=array();
	    $buyer_po_arr=array();
	    foreach($result as $row)
	    {
	    	$buyer_po_arr[] = $row[csf('break_id')];
	    	//$job_dtls_arr[] = $row[csf('job_dtls_id')];
	    	//$job_id_arr[] = $row[csf('job_id')];
	    	$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['company_id']=$row[csf('company_id')];
	    	$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['trims_production']=$row[csf('trims_production')];
	    	$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['color_id']=$row[csf('color_id')];
	    	$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['size_id']=$row[csf('size_id')];
	   	 	$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['section_id']=$row[csf('section_id')];
			$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['sub_section']=$row[csf('sub_section')];

			$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['production_date']=$row[csf('production_date')];

			//$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['job_dtls_id']=$row[csf('job_dtls_id')];
			$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['location_id']=$row[csf('location_id')];

			$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['received_id']=$row[csf('received_id')];

	   		$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['job_quantity']=$row[csf('job_quantity')];

			$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['uom']=$row[csf('uom')];

			$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['conv_factor']=$row[csf('conv_factor')];

			//$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['buyer_po_no']=$row[csf('buyer_po_no')];
			$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['item_description']=$row[csf('item_description')];			            
			//$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['job_no_mst']=$row[csf('job_no_mst')];
			$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['qc_qty']+=$row[csf('qc_qty')];

			$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['break_id']=$row[csf('break_id')];
			$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['product_uom']=$row[csf('product_uom')];
			$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['machine_id']=$row[csf('machine_id')];
			$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['order_no']=$row[csf('order_no')];
			$date_array[$row[csf('trims_production')]][$row[csf('production_date')]][$row[csf('section_id')]][$row[csf('sub_section')]][$row[csf('item_description')]][$row[csf('order_no')]][$row[csf('color_id')]][$row[csf('size_id')]]['trims_ref']=$row[csf('trims_ref')];

			
		}

		$po_str = implode(',', $buyer_po_arr);
		$po_sql ="select c.id, a.grouping from wo_po_break_down a, subcon_ord_dtls b, subcon_ord_breakdown c where b.id=c.mst_id and a.id=b.buyer_po_id and a.is_deleted=0 and a.status_active=1 and c.id in($po_str) and a.grouping is not null";
		//echo $po_sql; 
		$po_sql_res=sql_select($po_sql);
		$grouping_arr = array();
		foreach ($po_sql_res as $row)
		{
			$grouping_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
		}
		
		unset($po_sql_res);

		//$internal_ref = return_library_array("select id, grouping from wo_po_break_down where status_active=1 and is_deleted=0", 'id','grouping');


		?>	
		<div align="center" style="height:auto; width:<? echo $width_second+20;?>px; margin:0 auto; padding:0;">
	    	<table width="<? echo $width_second;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
					<thead class="form_caption" >
						<tr>
							<td colspan="24" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
						</tr>
						<tr>
							<td colspan="24" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
						</tr>
						<tr>
							<td colspan="24" align="center" style="font-size:14px; font-weight:bold">
								<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
							</td>
						</tr>
					</thead>
				</table>
	            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width_second;?>" rules="all" id="rpt_table_header" align="left">
					<thead>
	                    <th width="35">SL</th>
	                    <th width="120">Company</th>
	                    <th width="100">Location</th>
	                    <th width="120">Production Id</th>
	                    <th width="100">Machine No</th>
	                    <th width="100">Shift Name</th>
	                    <th width="100">Section</th>
	                    <th width="100">Sub-Section</th>
	                    <th width="100">Prod. date</th>
	                    <th width="100">Work Order No</th>
	                    <th width="100">Internal Ref</th>
	                	<th width="100">Trims Ref</th>
	                    <th width="100">Party Name</th>
	                    <th width="100">Buyer Name</th>
	                    <th width="100">Style</th>
	                    <th width="150">Item Description</th>
	                    <th width="80">Color</th>
	                    <th width="80">Size</th>
	                    <th width="60">WO UOM</th>
	                   	<th width="100">WO Qty</th>
	                    <th width="80">Conv. Factor</th>
	                    <th width="80">BK/Prod UOM </th>
	                    <th width="80">Job/Book Qty.</th>
	                    <th width="100">Prod. Qty</th>
	                    <th width="">Prod. Value($)</th>
					</thead>
				</table>
	        <div style="width:<? echo $width_second+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
	        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<? echo $width_second;?>" rules="all" align="left">
	            <? 
				$i=1;
				$total_order_qty=0;$total_booked_qty=0;$total_production_qty=0;$total_production_value=0;
				foreach ($date_array as $prodiction_id => $prodiction_arr) 
				{
					foreach($prodiction_arr as $prod_date=>$prod_date_arr)
					{
						foreach($prod_date_arr as $section_key_id=>$section_data)
						{
							foreach($section_data as $sub_section_key_id=>$sub_section_key_data)
							{
								foreach($sub_section_key_data as $item_description_id=>$item_description_data)
								{
									foreach($item_description_data as $order_no_id=>$order_no_data)
									{
										foreach($order_no_data as $color_id=>$color_arr)
										{
											foreach($color_arr as $size_id=>$row)
											{

												$style_arr=$booked_array[$row[section_id]][$row[sub_section]][$row[item_description]][$row[order_no]][$row[received_id]][$row[color_id]][$row[size_id]]['style'];
												
												$within_group=$order_array[$row[received_id]]['within_group'];
												if ($within_group==1) {
													$style=$order_array[$row[received_id]]['buyer_style_ref'];
												}else{
													$style=chop($style_arr,",");
												}

												$orderquantity=$booked_array[$row[section_id]][$row[sub_section]][$row[item_description]][$row[order_no]][$row[received_id]][$row[color_id]][$row[size_id]]['order_quantity'];
												$orderamount=$booked_array[$row[section_id]][$row[sub_section]][$row[item_description]][$row[order_no]][$row[received_id]][$row[color_id]][$row[size_id]]['amount'];
												

												//$orderamount=$booked_array[$row[section_id]][$row[sub_section]][$row[item_description]][$row[order_no]][$row[received_id]]['amount'];
												$rate=number_format($orderamount/$orderquantity,4);
												$currency_id=$order_array[$row[received_id]]['currency_id'];
												if($currency_id==1)
												{
													 //$takarate=$rate;
													 //$orderamounttaka=$orderamount;
													 //$usdrate=number_format($rate/$currency_rate,4);
													 //$orderamountusd=$orderamount/$currency_rate;
													 
													 //$product_valu_taka=$row[qc_qty]/$row[conv_factor]*$takarate;
													 
													 //$product_valu_usd=$row[qc_qty]/$row[conv_factor]*$usdrate;
													 $prod_usd=$orderamount/$currency_rate;
													 
												}
												else if($currency_id==2)
												{
													//$takarate=number_format($rate*$currency_rate,4);
													//$orderamounttaka=$orderamount*$currency_rate;
													$usdrate=$rate;
													//$orderamountusd=$orderamount;
													
													//$product_valu_taka=$row[qc_qty]/$row[conv_factor]*$takarate;
													$product_valu_usd=$row[qc_qty]/$row[conv_factor]*$usdrate;
													//$prod_usd=$orderamount;
													$prod_usd=$product_valu_usd;
												}



												$grouping = $grouping_arr[$row[break_id]]['grouping'];
												//$grouping = $internal_ref[$row[break_id]];

												/*$po_id_arr = explode(',', $row[break_id]);
												foreach ($po_id_arr as $value) {
													$grouping = $buyer_po_arr[$value]['grouping'];
												}*/


												

											?>
							                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							                	<td width="35"  align="center"><? echo $i;?></td>
							                	<td width="120" style="word-break: break-all;" align="center"><? echo $companyArr[$row[company_id]];?></td>
							                	<td width="100" style="word-break: break-all;" align="center"><? echo $location_arr[$row[location_id]];?></td>
							                	<td width="120" style="word-break: break-all;" align="center"><? echo $prodiction_id;?></td>
							                	<td width="100" style="word-break: break-all;" align="center"><? echo $machine_noArr[$row[machine_id]];?></td>
							                	<td width="100" style="word-break: break-all;" align="center"><? //echo $row[shift_name];?></td>
							                    <td width="100" style="word-break: break-all;" align="left"><? echo $trims_section[$row[section_id]];?></td>
							                    <td width="100" style="word-break: break-all;" align="left"><? echo $trims_sub_section[$row[sub_section]];?></td>
							                    <td width="100" style="word-break: break-all;" align="center"><? echo $row[production_date];?></td>
							                    <td width="100" style="word-break: break-all;" align="left"><? echo $order_array[$row[received_id]]['cust_order_no'] ;?></td>
							                    <td width="100" style="word-break: break-all;" align="left"><?php echo $grouping; ?></td>
							                    <td width="100" style="word-break: break-all;" align="left"><?php echo $order_array[$row[received_id]]['trims_ref']; ?></td>
							                    <td width="100" style="word-break: break-all;" align="left"><? echo $party=($order_array[$row[received_id]]['within_group']==1)?$companyArr[$order_array[$row[received_id]]['party_id']]:$buyerArr[$order_array[$row[received_id]]['party_id']];?></td>
							                    <td width="100" style="word-break: break-all;" align="left"><? echo ($order_array[$row[received_id]]['within_group']==1)? $buyerArr[$order_array[$row[received_id]]['buyer_buyer']]:$order_array[$row[received_id]]['buyer_buyer'];?></td>
							                    <td width="100" style="word-break: break-all;" align="center"><? echo $style;?></td>
							                    <td width="150" style="word-break: break-all;" align="left"><? echo $row[item_description];?></td>
							                    <td width="80" style="word-break: break-all;" align="center"><? echo $color_name_arr[$color_id];?></td> 
							                    <td width="80" style="word-break: break-all;" align="center"><? echo $size_name_arr[$size_id];?></td> 
							                    <td width="60" style="word-break: break-all;" align="center"><? echo $unit_of_measurement[$order_array[$row[received_id]]['order_uom']]; //$unit_of_measurement[$row[uom]];?></td>  
							                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($orderquantity,2);?></td>
							                    <td width="80" style="word-break: break-all;" align="right"><? echo  $conv_factor=number_format($row[conv_factor],4);?></td>
							                    <td width="80" style="word-break: break-all;" align="center"><? echo $unit_of_measurement[$row[product_uom]];?></td>
							                    <td width="80" style="word-break: break-all;" align="right"><? if ($row[conv_factor])echo number_format($orderquantity*$row[conv_factor],2); else echo "0"; //$booked_array[$row[break_id]]['booked_qty'];?></td>
							                    <td width="100" style="word-break: break-all;" align="right"><? echo number_format($row[qc_qty],2);?></td>
							                    <td width="" style="word-break: break-all;" align="right"><? echo number_format($prod_usd,2);?></td>
							                </tr>
							                <? 
												$total_order_qty+=$orderquantity;
												$total_booked_qty+=$orderquantity*$row[conv_factor];
												$total_production_qty+=$row[qc_qty];
												$total_production_value+=$prod_usd;
												

												$i++;
											}
										}
									}
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
	        <table width="<? echo $width_second;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
	                <th width="35"></th>
	                <th width="120"></th>
	                <th width="100"></th>
	                <th width="120"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="150"></th>
	                <th width="80"></th>
	                <th width="80"></th>
	                <th width="60">Total:</th>
	                <th width="100" id="total_order_qty"><? echo number_format($total_order_qty,2);?></th>
	                <th width="80"></th>
	                <th width="80"></th>
	                <th width="80" id="total_booked_qty"><? echo number_format($total_booked_qty,2);?></th>
	                <th width="100" id="total_production_qty"><? echo number_format($total_production_qty,2);?></th>
	                <th width="" ><? echo number_format($total_production_value,2);?></th>
	                
	                
				</tfoot>
			</table>
	    </div>
		
	<?
	}
	
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
	
}

?>