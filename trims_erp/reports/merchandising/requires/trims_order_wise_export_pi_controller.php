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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company;?>+'_'+<? echo $party_name;?>+'_'+<? echo $customer_source;?>, 'create_booking_search_list_view', 'search_div', 'trims_order_wise_export_pi_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$report_type=str_replace("'","", $report_type);
	$date_range_type=str_replace("'","", $date_range_all);
	$report_type_all=str_replace("'","", $report_type_all);
	
	if($cbo_section_id){$where_con.=" and b.section='$cbo_section_id'";} 
	if($cbo_customer_source){$where_con.=" and a.within_group='$cbo_customer_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";} 
	if($cbo_location_name){$where_con.=" and a.location_id = $cbo_location_name";}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_name_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$machine_noArr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0","id","machine_no");
	$location_arr = return_library_array("select id, location_name from lib_location where status_active=1 and is_deleted=0","id","location_name");
	$lib_item_group_arr = return_library_array("select id, item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$round_type=array(1=>"Round Up",2=>"Round Down");

	if($date_range_type==1){
	   if($txt_date_from!="" and $txt_date_to!="")
	   {	
		 $date_con.=" and a.receive_date between '$txt_date_from' and '$txt_date_to'";
	   }
    }elseif($date_range_type==2){
		$date_con.=" and f.pi_date between '$txt_date_from' and '$txt_date_to'";
	}
	elseif($date_range_type==3){
		$date_con.=" and d.lc_date between '$txt_date_from' and '$txt_date_to'";
	}
	
	if(trim($txt_order_no)!="")
	{
			$sql_order_no="and a.order_no like '%$txt_order_no%'";
	}
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1  and id in(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)" , "conversion_rate" ); 

	$width=2700;
	ob_start();
	if ($report_type==1) {

		if($report_type_all==1){
			$order_sql="SELECT a.id,b.id as dtls_id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom, a.trims_ref,b.item_group,b.order_quantity,b.rate,b.amount,d.export_lc_no,d.lc_date,d.lc_value,c.attached_qnty,c.attached_value, sum(e.amount) as    pi_amount, f.pi_number,f.pi_date,a.payterm_id
			from  subcon_ord_mst   a 
			left join com_export_pi_dtls e on e.WORK_ORDER_ID=a.id AND e.is_deleted=0 and e.status_active=1 
			left join com_export_pi_mst f on f.id=e.PI_ID AND f.is_deleted=0 and f.status_active=1 ,
			subcon_ord_dtls  b  
			LEFT JOIN com_export_lc_order_info c ON b.id = c.wo_po_break_down_id AND c.is_deleted=0 and c.status_active=1 
			LEFT JOIN com_export_lc d ON d.id = c.com_export_lc_id AND d.is_deleted=0 and d.status_active=1 
			where a.subcon_job=b.job_no_mst and a.entry_form=255 and (e.WORK_ORDER_ID <> '' OR e.WORK_ORDER_ID IS NULL) and (c.wo_po_break_down_id <> '' OR c.wo_po_break_down_id IS NULL) and a.company_id =$cbo_company_id  AND a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $sql_order_no $date_con $where_con
			group by a.id,b.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom, a.trims_ref,b.item_group,b.order_quantity,b.rate,b.amount,d.export_lc_no,d.lc_date,d.lc_value,c.attached_qnty,c.attached_value, f.pi_number, f.pi_date,a.payterm_id";
		 }
		 elseif($report_type_all==2){
		   $order_sql="SELECT a.id,b.id as dtls_id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom, a.trims_ref,b.item_group,b.order_quantity,b.rate,b.amount,d.export_lc_no,d.lc_date,d.lc_value,c.attached_qnty,c.attached_value, sum(e.amount) as    pi_amount, f.pi_number,f.pi_date,a.payterm_id
		   from  subcon_ord_mst   a 
		   left join com_export_pi_dtls e on e.WORK_ORDER_ID=a.id  AND e.is_deleted=0 and e.status_active=1 
		   left join com_export_pi_mst f on f.id=e.PI_ID AND f.is_deleted=0 and f.status_active=1 , 
           subcon_ord_dtls  b  
		   LEFT JOIN com_export_lc_order_info c ON b.id = c.wo_po_break_down_id AND c.is_deleted=0 and c.status_active=1 
           LEFT JOIN com_export_lc d ON d.id = c.com_export_lc_id and d.is_deleted=0 and d.status_active=1 
		   where a.subcon_job=b.job_no_mst and a.entry_form=255 and (e.WORK_ORDER_ID <> '' OR e.WORK_ORDER_ID IS NULL) and a.company_id =$cbo_company_id  AND a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  $sql_order_no $date_con $where_con
		   group by a.id,b.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom, a.trims_ref,b.item_group,b.order_quantity,b.rate,b.amount,d.export_lc_no,d.lc_date,d.lc_value,c.attached_qnty,c.attached_value, f.pi_number, f.pi_date,a.payterm_id";
		}
		elseif($report_type_all==3){

			$order_sql="SELECT a.id,b.id as dtls_id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom, a.trims_ref,b.item_group,b.order_quantity,b.rate,b.amount,d.export_lc_no,d.lc_date,d.lc_value,c.attached_qnty,c.attached_value, sum(e.amount) as    pi_amount, f.pi_number,f.pi_date,a.payterm_id
			from  subcon_ord_mst   a 
			left join com_export_pi_dtls e on e.WORK_ORDER_ID=a.id  AND e.is_deleted=0 and e.status_active=1 
			left join com_export_pi_mst f on f.id=e.PI_ID and f.is_deleted=0 and f.status_active=1,
			subcon_ord_dtls  b 
		    LEFT JOIN com_export_lc_order_info c ON b.id = c.wo_po_break_down_id AND c.is_deleted=0 and c.status_active=1 
			LEFT JOIN com_export_lc d ON d.id = c.com_export_lc_id AND d.is_deleted=0 and d.status_active=1 
			where a.subcon_job=b.job_no_mst and a.entry_form=255 and (c.wo_po_break_down_id <> '' OR c.wo_po_break_down_id IS NULL) and a.company_id =$cbo_company_id AND a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  $sql_order_no $date_con $where_con
			group by a.id,b.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom, a.trims_ref,b.item_group,b.order_quantity,b.rate,b.amount,d.export_lc_no,d.lc_date,d.lc_value,c.attached_qnty,c.attached_value, f.pi_number, f.pi_date,a.payterm_id";		
		}
		elseif($report_type_all==4){
		   $order_sql="SELECT a.id,b.id as dtls_id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom, a.trims_ref,b.item_group,b.order_quantity,b.rate,b.amount,d.export_lc_no,d.lc_date,d.lc_value,c.attached_qnty,c.attached_value, sum(e.amount) as    pi_amount, f.pi_number,f.pi_date,a.payterm_id
		   from  subcon_ord_mst   a 
		   left join com_export_pi_dtls e on e.WORK_ORDER_ID=a.id 
		   left join com_export_pi_mst f on f.id=e.PI_ID,
           subcon_ord_dtls  b  
		   LEFT JOIN com_export_lc_order_info c ON b.id = c.wo_po_break_down_id
           LEFT JOIN com_export_lc d ON d.id = c.com_export_lc_id
		   where a.subcon_job=b.job_no_mst and a.entry_form=255 and a.company_id =$cbo_company_id AND c.wo_po_break_down_id IS NOT NULL
           AND e.WORK_ORDER_ID IS NOT NULL AND a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 AND c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 AND e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $sql_order_no $date_con $where_con
		   group by a.id,b.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom, a.trims_ref,b.item_group,b.order_quantity,b.rate,b.amount,d.export_lc_no,d.lc_date,d.lc_value,c.attached_qnty,c.attached_value, f.pi_number, f.pi_date,a.payterm_id";
		}else{
			$order_sql="SELECT a.id,b.id as dtls_id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom, a.trims_ref,b.item_group,b.order_quantity,b.rate,b.amount,d.export_lc_no,d.lc_date,d.lc_value,c.attached_qnty,c.attached_value, sum(e.amount) as    pi_amount, f.pi_number,f.pi_date,a.payterm_id
		   from  subcon_ord_mst   a 
		   left join com_export_pi_dtls e on e.WORK_ORDER_ID=a.id AND e.is_deleted=0 and e.status_active=1 
		   left join com_export_pi_mst f on f.id=e.PI_ID and f.is_deleted=0 and f.status_active=1,
           subcon_ord_dtls  b  
		   LEFT JOIN com_export_lc_order_info c ON b.id = c.wo_po_break_down_id AND c.is_deleted=0 and c.status_active=1 
           LEFT JOIN com_export_lc d ON d.id = c.com_export_lc_id and d.is_deleted=0 and d.status_active=1
		   where a.subcon_job=b.job_no_mst and a.entry_form=255  and a.company_id =$cbo_company_id AND a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  $sql_order_no $date_con $where_con
		   group by a.id,b.id,a.subcon_job,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.party_id,a.within_group,a.subcon_job,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom, a.trims_ref,b.item_group,b.order_quantity,b.rate,b.amount,d.export_lc_no,d.lc_date,d.lc_value,c.attached_qnty,c.attached_value, f.pi_number, f.pi_date,a.payterm_id";  //and f.item_category_id=45
		}
		 // echo $order_sql;

		$order_sql_result = sql_select($order_sql);
		$order_id_array=array();
		$order_dtls_id_array=array();

		foreach($order_sql_result as $row)
		{
			$order_id_array[] = $row[csf('id')];
			$order_dtls_id_array[] = $row[csf('dtls_id')];
		}

		?>	
		<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
	    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
					<thead class="form_caption" >
						<tr>
							<td colspan="20" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
						</tr>
						<tr>
							<td colspan="20" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
						</tr>
						<tr>
							<td colspan="20" align="center" style="font-size:14px; font-weight:bold">
								<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
							</td>
						</tr>
					</thead>
				</table>
	            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<div style="text-align:center;" class="search_type"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --",4,"","","2,3,4" ); ?></div>
					<thead>
	                    <th width="35">SL</th>
	                    <th width="100">Job No</th>
	                    <th width="100">Order No</th>
	                    <th width="100">Party</th>
	                    <th width="100">Ord. Rcv Date</th>
	                    <th width="100">Buyers Buyer</th>
	                    <th width="100">Section</th>
	                    <th width="100">Item Group</th>
	                    <th width="100">Order Qty</th>
	                	<th width="100">Rate</th>
	                    <th width="100">Order Value</th>
	                    <th width="100">Currency</th>
	                    <th width="100">Pay Term</th>
	                    <th width="100">Export PI No</th>
	                    <th width="150">PI Date</th>
	                    <th width="100">PI Value</th>
	                    <th width="100">LC No</th>
	                    <th width="60">LC Date</th>
	                    <th width="60">LC Value</th>
	                    <th width="100">Attached Qty</th>
	                    <th width="100">Attached Value</th>
					</thead>
				</table>
	        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
	        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
	            <? 
					$i=1;
					$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;

						           foreach($order_sql_result as $row)
					             {
										?>
						                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						                	<td width="35"  align="center"><? echo $i;?></td>
						                    <td width="100" style="word-break: break-all;" align="center"><? echo $row[csf('subcon_job')];?></td>
						                    <td width="100" style="word-break: break-all;" align="center"><? echo $row[csf('order_no')];?></td>
						                    <td width="100" style="word-break: break-all;" align="center"><? echo $party=($row[csf('within_group')]==1)?$companyArr[$row[csf('party_id')]]:$buyerArr[$row[csf('party_id')]];?></td>										
						                    <td width="100" style="word-break: break-all;" align="center"><? echo $row[csf('receive_date')];?></td>
						                    <td width="100" style="word-break: break-all;" align="center"><? echo  $row[csf('buyer_buyer')];?></td>
						                    <td width="100" style="word-break: break-all;" align="center"><? echo $trims_section[$row[csf('section')]];?></td>
						                    <td width="100" style="word-break: break-all;" align="center"><? echo $lib_item_group_arr[$row[csf('item_group')]] ;?></td>
						                    <td width="100" style="word-break: break-all;" align="center"><?php echo $row[csf('order_quantity')] ?></td>
						                    <td width="100" align="center" style="word-break: break-all;" align="left"><?php echo $row[csf('rate')] ?></td>
						                    <td width="100" align="center" style="word-break: break-all;" align="left"><? echo $row[csf('amount')];?></td>

						                    <td width="100" align="center" style="word-break: break-all;" align="left"><? echo $currency[$row[csf('currency_id')]]?></td>
						                    <td width="100" align="center" style="word-break: break-all;" align="left"><? echo $pay_term[$row[csf('payterm_id')]]?></td>
						                    <td width="100" align="center" style="word-break: break-all;" align="left" style="word-break:break-all;"><? echo $row[csf('pi_number')] ?></td>
						                    <td width="150" align="center" style="word-break: break-all;" align="left"><? echo $row[csf('pi_date')]?></td>
						                    <td width="100" align="center" style="word-break: break-all;" align="right"><? echo $row[csf('pi_amount')] ?></td>
						                    <td width="100" style="word-break: break-all;" align="center">&nbsp;<? echo $row[csf('export_lc_no')] ?></td>  
						                    <td width="60" style="word-break: break-all;" align="right"><? echo $row[csf('lc_date')] ?></td>
						                    <td width="60" style="word-break: break-all;" align="right"><? echo $row[csf('lc_value')]?></td>
						                    <td width="100" style="word-break: break-all;" align="right"><? echo $row[csf('attached_qnty')] ?></td>
						                    <td width="100" style="word-break: break-all;" align="right"><?  echo $row[csf('attached_value')] ?></td>
						                </tr>
						                <? 
										$i++;
					         }
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
	                <th width="100"></th>
	                <th width="150"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="60"></th>
	                <th width="60"> </th>
	                <th width="100"></th>
	                <th width="100"></th>
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