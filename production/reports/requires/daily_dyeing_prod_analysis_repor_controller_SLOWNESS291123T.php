<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_unit")
{
    echo create_drop_down( "cbo_unit_id", 120, "Select id, floor_name from  lib_prod_floor where company_id in($data) and production_process=3 and status_active=1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 );          
}
if ($action == "booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_booking_id").val(splitData[0]); 
			$("#hide_booking_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:840px;">
					<table width="840" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th width="130">Please Enter Job No</th>
							<th width="130">Please Enter Booking No</th>
							<th>Booking Date</th>

							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
							<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($companyID) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--","","",0 );
									?>
								</td>
								<td align="center">				
									<input type="text" style="width:100px" class="text_boxes" name="txt_job_no" id="txt_job_no" />	
								</td>
								<td align="center">				
									<input type="text" style="width:100px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />	
								</td> 
								<td align="center">	
									<input type="text" style="width:70px" class="datepicker" name="txt_date_from" id="txt_date_from" readonly/> To
									<input type="text" style="width:70px" class="datepicker" name="txt_date_to" id="txt_date_to" readonly/>
								</td>     

								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('txt_job_no').value, 'create_booking_no_search_list_view', 'search_div', 'daily_dyeing_prod_analysis_repor_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="4" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
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
}//bookingnumbershow;

if($action == "create_booking_no_search_list_view")
{
	$data=explode('**',$data);

	if ($data[0]!=0) $company="  a.company_id in($data[0])"; 
//	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if ($data[2]!=0) $booking_no=" and a.booking_no_prefix_num='$data[2]'"; else $booking_no='';	
	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and s.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	if ($data[5]!="") $job_no_cond=" and a.job_no='$data[5]'"; else $job_no_cond='';

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);
	//pro_batch_create_mst//wo_non_ord_samp_booking_mst
 	$sql= "(select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,pro_batch_create_mst b where $company $buyer $booking_no $booking_date $job_no_cond and a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 
	union all
	 select a.id,a.booking_no_prefix_num as no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no,a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_non_ord_samp_booking_mst a ,pro_batch_create_mst b where $company $buyer $booking_no $booking_date $job_no_cond and a.booking_no=b.booking_no and a.booking_type=4 and b.status_active=1 and b.is_deleted=0) order by id Desc
	";
	// echo $sql;
	//echo "select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,wo_non_ord_samp_booking_mst b where $company $buyer $booking_no $booking_date and a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 "; 
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "110,80,80,80,90,120,80,80,60,50","910","320",0, $sql , "js_set_value", "id,no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','setFilterGrid(\'list_view\',-1);','0,3,0,0,0,0,0,0,0,0','','');
	exit(); 
}
if($action=="batchnumber_show")
{
	echo load_html_head_contents("Batch Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_year_id.'DD-';
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

	?>
	<script type="text/javascript">
	function js_set_value(id)
	{ 
	//alert(id);
	document.getElementById('selected_id').value=id;
	parent.emailwindow.hide();
	}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	if($db_type==2)
	{
	$year_cond="to_char(insert_date,'YYYY')=$cbo_year_id";
	}
	else
	{
		$year_cond="YEAR(insert_date)=$cbo_year_id";
	}
	if($db_type==0) $year_field_grpby="GROUP BY batch_no"; 
	else if($db_type==2) $year_field_grpby=" GROUP BY batch_no,id,batch_no,batch_for,booking_no,color_id,batch_weight order by batch_no desc";
	$sql="select id,batch_no,batch_for,booking_no,color_id,batch_weight from pro_batch_create_mst where company_id in($company_name) and is_deleted = 0 and batch_no is not null and $year_cond $year_field_grpby ";	
	$arr=array(1=>$color_library,3=>$batch_for);
	echo create_list_view("list_view", "Batch no,Color,Booking no, Batch for,Batch weight ", "100,100,100,100,70","520","350",0, $sql, "js_set_value", "id,batch_no", "", 1, "0,color_id,0,batch_for,0", $arr , "batch_no,color_id,booking_no,batch_for,batch_weight", "employee_info_controller",'setFilterGrid("list_view",-1);','0') ;
	exit();
}//batchnumbershow;

if($action=="report_generate") // show
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    
    //echo $datediff;
    $cbo_company=str_replace("'","",$cbo_company_id);
    $working_company_id=str_replace("'","",$cbo_working_company_id);
    $cbo_unit=str_replace("'","",$cbo_unit_id);
	$txt_batch_no=str_replace("'","",$txt_batch_no);
	$batch_id=str_replace("'","",$batch_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_hide_booking_id=str_replace("'","",$txt_hide_booking_id);
	$type_id=str_replace("'","",$type_id);
	
	//batch_id*txt_batch_no*txt_booking_no*txt_hide_booking_id
    $date_from=str_replace("'","",$txt_date_from);
    $date_to=str_replace("'","",$txt_date_to);
    $type=str_replace("'","",$cbo_type);
    //$floor_id =str_replace("'","",$floor_id );
    if($working_company_id==0) $working_company_cond="";else $working_company_cond="and a.service_company in($working_company_id)";
    if($cbo_company==0 || $cbo_company=='') $lc_company_cond="";else $lc_company_cond="and a.company_id in($cbo_company)";
    
    if($working_company_id==0) $working_company_cond_issue="";else $working_company_cond_issue="and c.knit_dye_company in($working_company_id)";
    if($cbo_company==0 || $cbo_company=='') $lc_company_cond_issue="";else $lc_company_cond_issue="and c.company_id in($cbo_company)";
	 if($cbo_company==0 || $cbo_company=='') $lc_company_cond_issue2="";else $lc_company_cond_issue2="and c.lc_company in($cbo_company)";
   
    if($txt_batch_no!="" && $batch_id=="") $batch_cond="and b.batch_no='$txt_batch_no'";
	else  if($txt_batch_no!="" && $batch_id!="") $batch_cond="and b.id in($batch_id)";
	else $batch_cond="";
	 if($txt_booking_no!="" && $txt_hide_booking_id=="") $book_cond="and b.booking_no like '%$txt_booking_no%'";
	else  if($txt_booking_no!="" && $txt_hide_booking_id!="") $book_cond="and b.booking_no_id in($txt_hide_booking_id)";
	else $book_cond="";
	if($date_from!="" && $date_to!="" )
	{
	$date_cond="and a.process_end_date between '$date_from' and '$date_to'";
	}
	else $date_cond="";
   
    if($cbo_unit!=0) $floor_cond="and a.floor_id=$cbo_unit";else $floor_cond="";
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
 

    
    $machine_cap_array=array();
    $mach_cap_sql=sql_select("select id, machine_no, sum(prod_capacity) as prod_capacity from lib_machine_name where status_active=1 and is_deleted=0 group by id, machine_no");
    foreach($mach_cap_sql as $row)
    {
        $machine_cap_array[$row[csf('id')]]["machine_no"]=$row[csf('machine_no')];
        $machine_cap_array[$row[csf('id')]]["prod_capacity"]=$row[csf('prod_capacity')];
    }
    
	function convertMinutes2Hours($Minutes)
	{
	    if ($Minutes < 0)
	    {
	        $Min = Abs($Minutes);
	    }
	    else
	    {
	        $Min = $Minutes;
	    }
	    $iHours = Floor($Min / 60);
	    $Minutes = ($Min - ($iHours * 60)) / 100;
	    $Minutes = number_format($Minutes,2,'.','');
	    $tHours = $iHours + $Minutes;
	    $tHours = number_format($tHours,2,'.','');
	    if ($Minutes < 0)
	    {
	        $tHours = $tHours * (-1);
	    }
	    $aHours = explode(".", $tHours);
	    $iHours = $aHours[0];
	    if (empty($aHours[1]))
	    {
	        $aHours[1] = "00";
	    }
	    $Minutes = $aHours[1];
	    if (strlen($Minutes) < 2)
	    {
	        $Minutes = $Minutes ."0";
	    }
	    $tHours = $iHours .":". $Minutes;
	    return $tHours;
	}
	    
	ob_start(); 
	//$table_width=90+($datediff*160);
	    
	?>
    <div>
        <table width="2250px" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="25" class="form_caption" ><strong style="font-size:18px"><? echo $company_library[$cbo_company];?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="25" class="form_caption" ><strong style="font-size:14px">Daily Dyeing Produciton Analysis Report</strong></td>
            </tr>
            <tr>  
               <td align="center" width="100%" colspan="25" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>  
        </table>
        <?
        $color_library=return_library_array( "select id,color_name from lib_color where status_active=1", "id", "color_name");
        if($type==0 || $type==1 || $type==3 || $type==4)
        {
            $buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
            $gsm_library=return_library_array( "select id,gsm from  product_details_master", "id", "gsm");
             $con = connect();
		  execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=139");
			// oci_commit($con);
			// disconnect($con);
		 
            $order_arr=array();
            $sql_order="Select b.id as ID, a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.style_ref_no as STYLE_REF_NO, b.po_number as PO_NUMBER, b.po_quantity as PO_QUANTITY from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
            $result_sql_order=sql_select($sql_order);
            foreach($result_sql_order as $row)
            {
                $order_arr[$row['ID']]['job_no']=$row['JOB_NO'];
                $order_arr[$row['ID']]['buyer_name']=$row['BUYER_NAME'];
                $order_arr[$row['ID']]['style_ref_no']=$row['STYLE_REF_NO'];
                $order_arr[$row['ID']]['po_number']=$row['PO_NUMBER'];
                $order_arr[$row['ID']]['po_quantity']=$row['PO_QUANTITY'];
            }
            unset($result_sql_order);

            $non_order_arr=array();
            $sql_non_order="SELECT a.company_id, a.buyer_id as BUYER_NAME, b.booking_no as BOOKING_NO, b.bh_qty as BH_QTY
            from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
            where a.booking_no=b.booking_no and a.booking_type=4 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
            $result_sql_non_order=sql_select($sql_non_order);
            foreach($result_sql_non_order as $row)
            {
                $non_order_arr[$row[csf('BOOKING_NO')]]['buyer_name']=$row[csf('BUYER_NAME')];
                $non_order_arr[$row[csf('BOOKING_NO')]]['bh_qty']=$row[csf('BH_QTY')];
            }
            unset($result_sql_non_order);

            $load_data=array();
                $load_time_data=sql_select("select a.id,a.batch_id,a.batch_no,a.load_unload_id,a.process_end_date,a.end_hours,a.end_minutes from pro_fab_subprocess a where a.load_unload_id=1 and a.entry_form=35 and  a.status_active=1  and a.is_deleted=0 $working_company_cond $lc_company_cond");
            foreach($load_time_data as $row_time)// for Loading time
            {
                $load_data[$row_time[csf('batch_id')]]['process_end_date']=$row_time[csf('process_end_date')];
                $load_data[$row_time[csf('batch_id')]]['end_hours']=$row_time[csf('end_hours')];
                $load_data[$row_time[csf('batch_id')]]['end_minutes']=$row_time[csf('end_minutes')];
            }
        }

        if($type==0 || $type==1) // Self
        {
            ?>
            <style>
			span::before {content: '\A'; white-space: pre;}
			</style>
            <div align="left" style="background-color:#E1E1E1; color:#000; width:250px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Dyeing Production</i></u></strong></div>
            <table width="2770" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
                <thead>
                    <th width="40">SL</th>
                    <th width="110">WO/Booking No</th>
                    <th width="90">Job No</th>
                    <th width="120">Buyer Name</th>
                    <th width="130">Style Ref.</th>
                    <th width="140">Order No</th>
                    <th width="100">Order Qty.</th>
                    <th width="60">GSM</th>
                    <th width="100">Fabric Color</th>
                    <th width="100">Batch No/ Lot No.</th>
                    <th width="50">Ext. No</th>
                    <th width="100">MC No.</th>
                    <th width="90">MC Capacity</th>
                    <th width="70">Dyeing Date</th>
                    <th width="100">Dyeing Qty</th>
                    <th width="100">Trims weight</th>
                    <th width="60">UL %</th>
                    <th width="60">Hour Req.</th>
                    <th width="80">Loading Time</th>
                    <th width="80">Unloading Time</th>
                    <th width="100">Liquor Ratio</th>
                    <th width="70">Hour Used</th>
                    <th width="60">Hour Devi.</th>                
                    <th width="90">Process/ Color Range</th>
                    <th width="110">Total Dye Cost</th>
                    <th width="90">Total Dye Cost/kg</th>
                    <th width="110">Total Chem Cost</th>
                    <th width="90">Total Chem Cost/Kg</th>
                    <th width="130">Dyeing Company</th>
                    <th>Remarks</th>
                </thead>
            </table>
            <div style="width:2790px; overflow-y:scroll; max-height:275px;" id="scroll_body" >
                <table width="2770" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body">
                <?  
                
                
                if($db_type==0)
                {
                    $sql_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min,b.total_trims_weight, min(c.prod_id) as prod_id, group_concat(c.po_id) as po_id, sum(c.batch_qnty) as qnty
                    from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
                    where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.batch_against in(1,2) and b.is_sales!=1   $batch_cond $book_cond $floor_cond $working_company_cond $lc_company_cond   $date_cond
                    group by b.id,b.extention_no,a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min,b.total_trims_weight order by a.process_end_date";
                }
                else if($db_type==2)
                {
                    $sql_dtls="SELECT b.id,b.extention_no,a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min,b.total_trims_weight, min(c.prod_id) as prod_id, LISTAGG(cast(c.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.po_id) as po_id, sum(c.batch_qnty) as qnty
                    from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c 
                    where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id   and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against  in(1,2) and b.is_sales!=1 $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond 
                    group by b.id,b.extention_no, a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min,b.total_trims_weight order by a.process_end_date";
                }
              	// echo $sql_dtls;die;

                $sql_result=sql_select($sql_dtls);
                if(!empty($sql_result))
                {
                    // ================== getting batch id =========================
                    $batch_id_array = array();
					$all_prod_id="";
                    foreach ( $sql_result as $row )
                    {
                        $batch_id_array[$row[csf('batch_id')]] = $row[csf('batch_id')];
                        $booking_no_arr=explode("-",$row[csf('booking_no')]);
                        $fb_booking_no=$booking_no_arr[1];
                        if($fb_booking_no=='FB' || $fb_booking_no=='Fb') //echo $fb_booking_no.'A';else echo $fb_booking_no.'B';
                        {
	                        $self_batch_wise_array[$row[csf('batch_id')]]['id']= $row[csf('id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['batch_id']= $row[csf('batch_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['extention_no']= $row[csf('extention_no')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['dyeing_company']= $row[csf('dyeing_company')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['service_company']= $row[csf('service_company')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['production_date']= $row[csf('production_date')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['process_end_date']= $row[csf('process_end_date')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['machine_id']= $row[csf('machine_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['end_hours']= $row[csf('end_hours')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['end_minutes']= $row[csf('end_minutes')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['remarks']= $row[csf('remarks')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['batch_no']= $row[csf('batch_no')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['color_id']= $row[csf('color_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['booking_no']= $row[csf('booking_no')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['color_range_id']= $row[csf('color_range_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['dur_req_hr']= $row[csf('dur_req_hr')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['dur_req_min']= $row[csf('dur_req_min')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['trims_qty']+= $row[csf('total_trims_weight')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['prod_id']= $row[csf('prod_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['po_id']= $row[csf('po_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['qnty']= $row[csf('qnty')];
							if($all_prod_id=="") $all_prod_id=$row[csf('prod_id')];else $all_prod_id.=",".$row[csf('prod_id')];
                        }
                    }
					$batch_quantity_arr=return_library_array( "select a.id,sum(b.batch_qnty) as  batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($batch_id_array,0,'a.id')." group by a.id", "id", "batch_qnty");
					$sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
					//die;
					$result_data_recipe=sql_select($sql_recipe);
					foreach ($result_data_recipe as $row)
					{
						$batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
						if($tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
						{
						$batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
						}
						$batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
					}
				

                  	/*  $con = connect();
                    $r_id2=execute_query("delete from tmp_batch_or_iss where userid=$user_id and block=1");
                    if($r_id2)
                    {
                        oci_commit($con);
                    }

                    if(!empty($batch_id_array))
                    {
                        //here temporary table type is to define batch/issue no like (1=batch, 2=issue) and block means report block here its 1.
                        foreach ($batch_id_array as $batch_id_key => $batch_id_val) 
                        {
                            $r_id=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_no, type, block) values ($user_id,'".$batch_id_key."',1,1".")");
                            if($r_id) 
                            {
                                $r_id=1;
                            } 
                            else 
                            {
                                echo "insert into tmp_batch_or_iss (userid, batch_issue_no, type, block) values ($user_id,'".$batch_id_key."',1,1".")";
                                oci_rollback($con);
                                die;
                            }
                        }

                        if($r_id)
                        {
                            oci_commit($con);
                        }
                        else
                        {
                            oci_rollback($con);
                            disconnect($con);
                        }
                    }*/

               	 	/* $poIds=chop($all_prod_id,','); //$po_cond_for_in=""; $order_cond1=""; $order_cond2=""; $precost_po_cond="";
					$po_ids=count(array_unique(explode(",",$all_prod_id)));
						if($db_type==2 && $po_ids>1000)
						{
							$prod_cond_for_in=" and (";
							//$po_cond_for_in2=" and (";
						
							$poIdsArr=array_chunk(explode(",",$poIds),999);
							foreach($poIdsArr as $ids)
							{
								$ids=implode(",",$ids);
								//$poIds_cond.=" po_break_down_id in($ids) or ";
								$prod_cond_for_in.=" b.prod_id in($ids) or"; 
							}
							$prod_cond_for_in=chop($prod_cond_for_in,'or ');
							$prod_cond_for_in.=")";
							
						}
						else
						{
							$prod_cond_for_in=" and b.prod_id in($poIds)";
							//$po_cond_for_in2=" and d.po_break_down_id  in($poIds)";
							
							
						}*/
                    //============================= getting issue amount ================================
                    $chem_dye_cost_array=array(); //and b.batch_id in($batchIds) 
                    $chem_dye_cost_kg_array=array();

                 	//$chem_dye_cost_sql="SELECT a.mst_id, b.batch_id, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c, tmp_batch_or_iss d where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 and b.batch_id= d.batch_issue_no and d.userid=$user_id and d.block=1 and d.type=1 $working_company_cond_issue $lc_company_cond_issue2 group by a.mst_id, b.batch_id ";
					 $batch_id=str_replace("'","",$batch_id);
                    if($batch_id) $batch_idCond1=" and b.batch_id like '%$batch_id%' ";else $batch_idCond1="";
				 	 $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 $lc_company_cond_issue2 $working_company_cond_issue $batch_idCond1 group by a.mst_id, b.batch_id,a.item_category ";

                    //echo $chem_dye_cost_sql;die;

                    $chem_dye_cost_result=sql_select($chem_dye_cost_sql);
                    //var_dump($chem_dye_cost_result);die;
                    
                    foreach($chem_dye_cost_result as $row)
                    {
                        $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
                        $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
                        $total_batch_qnty=0;
                        foreach($batch_id_arr as $batch_ids)
                        {
                            $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
                            //echo $total_batch_qnty;die;
                            //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
                            
                        }
                        
                        foreach($batch_id_arr as $batch_id)
                        {
                           if($batch_quantity_arr[$batch_id]>0)
						   {
						    $chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
                            $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
						   }
                            
                        }
                        
                    }
                    //var_dump($chem_dye_cost_kg_array);//die;

                    // ============================ getting issue return amount =============================
                    if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company in($working_company_id)";
                    if(count($all_issue_id)>0)
                    {
                        fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 139, 1, $all_issue_id, $empty_arr);//PO ID Ref from=1
                       
                        
                        //and a.issue_id in($allIssueIds)
                        //$sql_return_cost="SELECT p.batch_no as BATCH_ID, sum(a.cons_amount) as CONS_AMOUNT  from inv_issue_master p, inv_transaction a, inv_receive_master b, tmp_batch_or_iss c where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1 and a.issue_id=c.batch_issue_id and c.userid=$user_id and c.block=1 and c.type=2 group by p.batch_no"; 
                    
                  //  if($batch_id) $batch_idCond=" and p.batch_no like '%$batch_id%' ";else $batch_idCond="";
                            $sql_return_cost="SELECT a.mst_id as MST_ID,p.batch_no as BATCH_ID,a.ITEM_CATEGORY, sum(a.cons_amount) as CONS_AMOUNT  from inv_issue_master p, inv_transaction a, inv_receive_master b, GBL_TEMP_ENGINE g where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond  and b.is_deleted=0 and b.status_active=1   and a.issue_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=139  group by p.batch_no,a.mst_id,a.item_category";

                        // echo $sql_return_cost;die();
                        $cost_return_data_arr=array();
                        $sql_return_res=sql_select($sql_return_cost);
                        foreach ($sql_return_res as $row)
                        {
                            $all_issue_id[$row[('MST_ID')]]=$row[('MST_ID')];

                            $batch_id_arr=array_unique(explode(",",$row[('BATCH_ID')]));
                            $total_batch_qnty=0;
                            foreach($batch_id_arr as $batch_ids)
                            {
                                $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
                                //echo $total_batch_qnty;die;
                                //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
                                
                            }
                            
                            foreach($batch_id_arr as $batch_id)
                            {
                            if($batch_quantity_arr[$batch_id]>0)
                            {
                                $cost_return_data_arr[$batch_id][$row[('ITEM_CATEGORY')]]+=(($row[('CONS_AMOUNT')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
                              //  $chem_dye_cost_kg_array[$batch_id][$row[('ITEM_CATEGORY')]]+=($row[('CONS_AMOUNT')]/$total_batch_qnty);
                            }
                                
                            }
                            
                            
                            //$cost_return_data_arr[$row['BATCH_ID']][$row['ITEM_CATEGORY']]+=$row['CONS_AMOUNT'];
                            // $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
                        }
                       // echo "<pre>";
                       //  print_r($cost_return_data_arr);die;

                        // foreach ($all_issue_id as $issue_id_key => $issue_id_val) 
                        // {
                        //     $r_id3=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,1".")");
                        //     if($r_id3) 
                        //     {
                        //         $r_id3=1;
                        //     } 
                        //     /*else 
                        //     {
                        //         echo "insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,1".")";
                        //         oci_rollback($con);
                        //         die;
                        //     }*/
                        // }

                        // if($r_id3)
                        // {
                        //     oci_commit($con);
                        // }
                        // else
                        // {
                        //     oci_rollback($con);
                        //     disconnect($con);
                        // }
                    }
                }
                
                execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."   and ENTRY_FORM=139");
                oci_commit($con);
                disconnect($con);
                // print_r($cost_return_data_arr);
                $tot_rows=count($sql_result);
                unset($sql_result);
                $i=1;  $ul_percent=0; $tot_dye_qnty=0;$total_dye_chem_cost=0;$total_chem_cost_kg=0;
                foreach ( $self_batch_wise_array as $batch_id=>$row )
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $po_id_arr=array_unique(explode(",",$row[('po_id')])); 
                    // echo $cost_return_data_arr[$batch_id][5].'='. $cost_return_data_arr[$batch_id][6].'<br>';
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[('booking_no')]; ?></p></td>
                        <td width="90" align="center"><p>
                        <?
                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;
                        foreach($po_id_arr as $po_id)
                        {
                            if(!in_array($order_arr[$po_id]['job_no'],$job_chech))
                            {
                                $job_chech[]=$order_arr[$po_id]['job_no'];
                                if($job_all=="") $job_all=$order_arr[$po_id]['job_no']; else $job_all .=", ".$order_arr[$po_id]['job_no'];
                                if($buyer_all=="") $buyer_all=$buyer_library[$order_arr[$po_id]['buyer_name']]; else $buyer_all .=", ".$buyer_library[$order_arr[$po_id]['buyer_name']];
                                if($style_all=="") $style_all=$order_arr[$po_id]['style_ref_no']; else $style_all .=", ".$order_arr[$po_id]['style_ref_no'];
                            }
                            if($po_all=="") $po_all=$order_arr[$po_id]['po_number']; else $po_all .=", ".$order_arr[$po_id]['po_number'];
                            $po_qnty +=$order_arr[$po_id]['po_quantity'];
                        }
                        echo $job_all; 
						$sub_process_id=rtrim($batch_recipe_arr[$batch_id]['sub_process_id'],',');
						$sub_process_ids=array_unique(explode(",",$sub_process_id));
						//$seq_no=$batch_recipe_subpro_arr2[$batch_id][$row[csf("sub_process_id")]]['seq_no'];
						$sid_ratio_all='';$sub_liquor_ratio_all='';
						
						foreach($sub_process_ids as $sid)
						{
							$liquor_ratio=$batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
							if($liquor_ratio>0)
							{
							$seq_no=$batch_recipe_subpro_arr[$batch_id][$sid]['seq_no'];
							$sid_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.'&#013;';
							
							$sub_liquor_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.',';
							}
							
						}
						$sub_liquor_ratio_all=rtrim($sub_liquor_ratio_all,',');
						$sub_liquor_ratio_allArr=array_unique(explode(",",$sub_liquor_ratio_all));
						$sub_liquor_ratio=$sub_liquor_ratio_allArr[0];
							
                        ?></p></td>
                        <td width="120"><p><? echo $buyer_all; echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']]; ?></p></td>
                        <td width="130"><p><? echo $style_all;  ?></p></td>
                        <td width="140"><p><? echo $po_all;?></p></td>
                        <td width="100" align="right"><p><? echo number_format($po_qnty,0);  ?></p></td>
                        <td width="60" align="center"><p><? echo $gsm_library[$row[('prod_id')]]; ?></p></td>
                        <td width="100"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
                        <td width="100"><p><? echo $row[('batch_no')]; ?></p></td>
                        <td width="50"><p><? echo $row[('extention_no')]; ?></p></td>
                        <td width="100" align="center"><p><? echo $machine_cap_array[$row[('machine_id')]]["machine_no"]; ?></p></td>
                        <td width="90" align="right"><p><? $machine_capacity=$machine_cap_array[$row[('machine_id')]]["prod_capacity"]; echo number_format($machine_capacity,2,'.',''); ?></p></td>
                        <td width="70" align="right"><p><? echo change_date_format($row[('production_date')]); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[('qnty')],2,'.',''); $total_dyeing_qnty+=$row[('qnty')]; ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[('trims_qty')],2,'.',''); $total_trims_qnty+=$row[('trims_qty')]; ?></p></td>
                        <td width="60" align="right"><p><? $ul_percent=$row[('qnty')]/$machine_capacity*100; echo number_format($ul_percent,2,'.',''); ?></p></td>
                        <td width="60" align="center"><p><? $req_hour=""; $req_hour=$row['dur_req_hr'].":".$row['dur_req_min']; echo $req_hour; ?></p></td>
                        <td width="80" align="center"><p><? $start_time=''; $start_time=$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']; echo  $start_time; ?></p></td>
                        
                        <td width="80" align="center"><p><? $end_time=''; $end_time=$row[('end_hours')].':'.$row[('end_minutes')]; echo $end_time; ?></p></td>
                         <td width="100" align="center" id="title_td" title="<? echo $sid_ratio_all;?>"><p><?  echo $sub_liquor_ratio; ?></p></td>
                        <td width="70" align="right"><p><? $start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
                        
                        if($timeDiffin>0)
                        {
                             $time_used=convertMinutes2Hours($timeDiffin); 
                             echo $time_used;
                        }
                        else
                        {
                            echo "Invalid";
                        }
                         ?></p></td>
                        <?
                        
                        $req_time=strtotime($row[('dur_req_hr')].":".$row[('dur_req_min')].":" . 00);
                        $hour_dev_cal=0;$tot_deviation="";
                        $time_used = number_format($time_used,2);
                        // echo $req_time."--".$time_used."<br>";
                        if($req_time!="")
                        {
                            $used_time=strtotime($time_used.':'. 00);
                            $hour_dev_cal=($used_time-$req_time)/60;
                            $tot_deviation=convertMinutes2Hours($hour_dev_cal); 
                        }
                        if($hour_dev_cal>0)
                        {
                            ?>
                            <td width="60" align="center" bgcolor="#FF0000"><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        else
                        {
                            ?>
                            <td width="60" align="center" ><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        ?>
                        <td width="90"><p><? echo $color_range[$row[('color_range_id')]]; ?></p></td>

                        <td width="110" align="right" title="Batch ID=<? echo $cost_return_data_arr[$batch_id][5].'='. $cost_return_data_arr[$batch_id][6].',BId='.$row[('batch_id')].',Tot dyes Cost='.$chem_dye_cost_array[$row[('batch_id')]][6];?>"><p><? $dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$batch_id][6]; echo number_format($dye_cost,2,'.','');?></p></td>
                        <td width="90" align="right" title="Total Dye Cost/Dyeing Qty"><p><? $tot_dye_cost_kg=$dye_cost/$row[('qnty')];echo number_format($tot_dye_cost_kg,2,'.','');
                        $total_dye_cost+=$dye_cost;$total_dye_cost_kg+=$tot_dye_cost_kg;?></p></td>

                        <td width="110" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $chemi_cost =$chem_dye_cost_array[$row[('batch_id')]][5] - $cost_return_data_arr[$batch_id][5]; echo number_format($chemi_cost,2,'.','');?></p></td>                        
                        <td width="90" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $tot_chemi_cost_kg=$chemi_cost/$row[('qnty')];echo number_format($tot_chemi_cost_kg,2,'.','');
                        $total_chem_cost+=$chemi_cost;$total_chem_cost_kg+=$tot_chemi_cost_kg;?> </p></td>

                        <td width="130" align="center"><p><? echo $company_library[$row[('service_company')]]; ?></p></td>
                        <td><p><? echo $row[('remarks')]; ?></p></td>
                    </tr>
                    <?
                    $i++;
                    //$total_chem_dye_cost+=$chem_dye_cost;
                }
                ?>
                </table>
                <table width="2770px" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
                    <tfoot>
                        <th width="40">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="70"><strong>Total</strong></th>
                        <th width="100" id="value_total_dyeing_qnty" align="right"><? echo number_format($total_dyeing_qnty,2,'.',''); ?></th>
                         <th width="100" id="value_total_trims_qnty" align="right"><? echo number_format($total_trims_qnty,2,'.',''); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="110" id="value_total_dye_cost"><? echo number_format($total_dye_cost,2,'.',''); ?></th>
                        <th width="90" title="Tot Dye Cost/Tot dying Qty" id=""><? echo number_format(($total_dye_cost/$total_dyeing_qnty),2,'.',''); ?></th>
                        <th width="110"  id="value_total_chem_cost" align="right"><? echo number_format($total_chem_cost,2,'.',''); ?></th>
                        <th width="90"  align="right"><? echo number_format(($total_chem_cost/$total_dyeing_qnty),2,'.',''); ?></th>
                        <th width="130">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div> 
            <?
        }
        if($type==0 || $type==2) // Subcon
        {
            ?>
            <br />
            <div>
            <div align="left" style="background-color:#E1E1E1; color:#000; width:320px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>In-bound Subcontract Dyeing Production</i></u></strong></div>
            <table width="2570" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
                <thead>
                <th width="40">SL</th>
                <th width="90">Job No</th>
                <th width="120">Party Name</th>
                <th width="130">Style Ref.</th>
                <th width="140">Order No</th>
                <th width="100">Order Qty.</th>
                <th width="100">Batch No</th>
                <th width="50">Ext. No</th>
                <th width="100">Batch Color</th>
                <th width="100">MC No.</th>
                <th width="90">MC Capacity</th>
                <th width="70">Dyeing Date</th>
                <th width="100">Dyeing Qty</th>
                <th width="100">Trims weight</th>
                <th width="60">UL %</th>
                <th width="60">Hour Req.</th>
                <th width="80">Loading Time</th>
                <th width="80">Unloading Time</th>
                <th width="100">Liquor Ratio</th>
                <th width="70">Hour Used</th>
                <th width="60">Hour Devi.</th>                
                <th width="90">Process/ Color Range</th>
                <th width="110">Total Dye Cost</th>
                <th width="90">Total Dye Cost/Kg</th>
                <th width="110">Total Chem Cost</th>
                <th width="90">Total Chem Cost/Kg</th>
                <th width="130">Dyeing Company</th>
                <th>Remarks</th>
                </thead>
            </table>
            <div style="width:2590px; overflow-y:scroll; max-height:275px;" id="scroll_body" >
                <table width="2570" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body1">
	                <?  
	                $buyer_library_subcon=return_library_array( "select a.id,a.short_name from lib_buyer a, lib_buyer_party_type b where a.id=b.buyer_id and b.party_type in(2,3)", "id", "short_name");
	                //$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	                $machine_library=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no");
	                
	                $job_arr=array();
	                $sql_job="Select b.id, a.subcon_job, a.party_id, b.cust_style_ref, b.order_no, b.order_quantity from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	                $result_sql_job=sql_select($sql_job);
	                foreach($result_sql_job as $row)
	                {
	                    $job_arr[$row[csf('id')]]['job_no']=$row[csf('subcon_job')];
	                    $job_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
	                    $job_arr[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
	                    $job_arr[$row[csf('id')]]['order_no']=$row[csf('order_no')];
	                    $job_arr[$row[csf('id')]]['order_quantity']=$row[csf('order_quantity')];
	                }
	                
	                $load_data_sub=array();
	                $load_time_data_sub=sql_select("select a.id, a.batch_id, a.batch_no, a.process_end_date, a.load_unload_id, a.end_hours, a.end_minutes from pro_fab_subprocess  a where a.load_unload_id=1 and a.entry_form=38 and status_active=1  and is_deleted=0 $working_company_cond $lc_company_cond");
	                
	                foreach($load_time_data_sub as $row_time)// for Loading time
	                {
	                    $load_data_sub[$row_time[csf('batch_id')]]['process_end_date']=$row_time[csf('process_end_date')];
	                    $load_data_sub[$row_time[csf('batch_id')]]['end_hours']=$row_time[csf('end_hours')];
	                    $load_data_sub[$row_time[csf('batch_id')]]['end_minutes']=$row_time[csf('end_minutes')];
	                }
	                unset($load_time_data_sub);
	                
	                
	               	/* $machine_cap_array=array();
	                $mach_cap_sql=sql_select("select id, sum(prod_capacity) as prod_capacity from lib_machine_name where status_active=1 and is_deleted=0 group by id");
	                foreach($mach_cap_sql as $row)
	                {
	                    $machine_cap_array[$row[csf('id')]]=$row[csf('prod_capacity')];
	                }*/
	                //echo $db_type."jahid";die;
	            
	                if($db_type==0)
	                {
	                    $sql_sub_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company, a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min, b.total_trims_weight, min(c.prod_id), group_concat(c.po_id) as po_id, sum(c.batch_qnty) as qnty 
	                    FROM pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
	                    WHERE a.batch_id=b.id and a.entry_form=38 and a.load_unload_id=2 and b.id=c.mst_id   and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $floor_cond  $batch_cond $book_cond   $working_company_cond $lc_company_cond 
	                    GROUP BY b.id, b.extention_no,a.batch_id, a.company_id,a.service_company, a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min, b.total_trims_weight order by a.process_end_date";
	                }
	                else if($db_type==2)
	                {
	                    $sql_sub_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company, a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min, b.total_trims_weight, min(c.prod_id), LISTAGG(cast(c.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.po_id) as po_id, sum(c.batch_qnty) as qnty 
	                    FROM pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
	                    WHERE a.batch_id=b.id and a.entry_form=38 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $batch_cond $book_cond $date_cond $floor_cond  $lc_company_cond $working_company_cond GROUP BY b.id, b.extention_no,a.batch_id, a.company_id,a.service_company, a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min, b.total_trims_weight order by a.process_end_date";
	                }
	                //echo $sql_sub_dtls;die;
	                $sql_sub_result=sql_select($sql_sub_dtls);
					 $tot_rows=count($sql_sub_result);
					$subBatch_id_arr=array();
					foreach( $sql_sub_result as $row)
					{
						  $subBatch_id_arr[$row[csf('id')]]=$row[csf('id')];
					}
					
					$batch_quantity_arr=return_library_array( "select a.id,sum(b.batch_qnty) as  batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($subBatch_id_arr,0,'a.id')." group by a.id", "id", "batch_qnty");
					 
	                $chem_dye_cost_array=array();
	                $chem_dye_cost_kg_array=array();

	                $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1   $working_company_cond_issue  $lc_company_cond_issue2 group by a.mst_id, b.batch_id,a.item_category ";  

	                $chem_dye_cost_result=sql_select($chem_dye_cost_sql);
	                foreach($chem_dye_cost_result as $row)
	                {
	                    $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
	                    $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
	                    $total_batch_qnty=0;
	                    foreach($batch_id_arr as $batch_ids)
	                    {
	                        $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
	                        //echo $total_batch_qnty;die;
	                        //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
	                        
	                    }
	                    
	                    foreach($batch_id_arr as $batch_id)
	                    {
	                         if($batch_quantity_arr[$batch_id]>0)
						  	 {
							$chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
	                        $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
						  	 }
	                        
	                    }
	                }
						
	                if($type==2)
	                {
	                   /* $batchIds = "'" . implode ( "', '", $sub_batch_id_array ) . "'";
	                    $baIds=chop($batchIds,','); $batch_cond_in="";
	                    $ba_ids=count(array_unique(explode(",",$batchIds)));
	                    if($db_type==2 && $ba_ids>1000)
	                    {
	                    $batch_cond_in=" and (";
	                    $baIdsArr=array_chunk(explode(",",$baIds),999);
	                    foreach($baIdsArr as $ids)
	                    {
	                    $ids=implode(",",$ids);
	                    $batch_cond_in.=" b.batch_id in($ids) or"; 
	                    }
	                    $batch_cond_in=chop($batch_cond_in,'or ');
	                    $batch_cond_in.=")";
	                    }
	                    else
	                    {
	                    $batch_cond_in=" and b.batch_id in($baIds)";
	                    }*/

	                    //============================= getting issue amount ================================
	                   

	                    // ============================ getting issue return amount =============================
	                    /*
	                        ############################  

	                        As this code has no impact on this block so it is commented 

	                        ###################

	                        if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company=$working_company_id";
	                        if(count($all_issue_id)>0)
	                        {
	                            $allIssueIds = implode(",", $all_issue_id);

	                            $issueIds=chop($allIssueIds,','); $issue_cond_in="";
	                            $iss_ids=count(array_unique(explode(",",$allIssueIds)));
	                            if($db_type==2 && $iss_ids>1000)
	                            {
	                            $issue_cond_in=" and (";
	                            $issueIdsArr=array_chunk(explode(",",$issueIds),999);
	                            foreach($issueIdsArr as $ids)
	                            {
	                            $ids=implode(",",$ids);
	                            $issue_cond_in.=" a.issue_id in($ids) or"; 
	                            }
	                            $issue_cond_in=chop($issue_cond_in,'or ');
	                            $issue_cond_in.=")";
	                            }
	                            else
	                            {
	                            $issue_cond_in=" and a.issue_id in($issueIds)";
	                            }
	                            //and a.issue_id in($allIssueIds)
	                            $sql_return_cost="SELECT p.batch_no as batch_id, sum(a.cons_amount) as cons_amount  from inv_issue_master p, inv_transaction a, inv_receive_master b where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1  $issue_cond_in  group by p.batch_no"; 

	                            $cost_return_data_arr=array();
	                            $sql_return_res=sql_select($sql_return_cost);
	                            foreach ($sql_return_res as $row)
	                            {
	                                $cost_return_data_arr[$row[csf('batch_id')]]=$row[csf('cons_amount')];
	                                // $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
	                            }
	                        }
	                    */
	                }
	                // Issue end
					$sub_sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.entry_form=38  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
					//die;
					$sub_result_data_recipe=sql_select($sub_sql_recipe);
					foreach ($sub_result_data_recipe as $row)
					{
						$sub_batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
						if($sub_tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
						{
						$sub_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
						}
						$sub_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
					}
					
	            
	                $k=1;  $ul_percent=0; $tot_dye_qnty=0;$total_dye_chem_cost=0;
	                foreach ( $sql_sub_result as $row )
	                {
	                    if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	                    $po_id_arr=array_unique(explode(",",$row[csf('po_id')])); 
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color_sub('row_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="row_<? echo $k; ?>">
	                        <td width="40"><? echo $k; ?></td>
	                        <td width="90" align="center"><p>
	                        <?
	                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;
	                        foreach($po_id_arr as $po_id)
	                        {
	                            if(!in_array($job_arr[$po_id]['job_no'],$job_chech))
	                            {
	                                $job_chech[]=$job_arr[$po_id]['job_no'];
	                                if($job_all=="") $job_all=$job_arr[$po_id]['job_no']; else $job_all .=", ".$job_arr[$po_id]['job_no'];
	                                if($buyer_all=="") $buyer_all=$job_arr[$po_id]['party_id']; else $buyer_all .=", ".$job_arr[$po_id]['party_id'];
	                                if($style_all=="") $style_all=$job_arr[$po_id]['style_ref_no']; else $style_all .=", ".$job_arr[$po_id]['style_ref_no'];
	                            }
	                            if($po_all=="") $po_all=$job_arr[$po_id]['order_no']; else $po_all .=", ".$job_arr[$po_id]['order_no'];
	                            $po_qnty +=$job_arr[$po_id]['order_quantity'];
	                        }
	                        echo $job_all;
							
							$batch_id=$row[csf('batch_id')];
							
							$sub_process_id=rtrim($sub_batch_recipe_arr[$batch_id]['sub_process_id'],',');
							$sub_process_ids=array_unique(explode(",",$sub_process_id));
							$sub_sid_ratio_all='';$sub_liquor_ratio_all='';
							foreach($sub_process_ids as $sid)
							{
								$sub_liquor_ratio=$sub_batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
								if($sub_liquor_ratio>0)
								{
								$seq_no=$sub_batch_recipe_subpro_arr[$batch_id][$sid]['seq_no'];
								$sub_sid_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$sub_liquor_ratio.'&#013;';
								
								$sub_liquor_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$sub_liquor_ratio.',';
								}
								
							}
							$sub_liquor_ratio_all=rtrim($sub_liquor_ratio_all,',');
							$sub_liquor_ratio_allArr=array_unique(explode(",",$sub_liquor_ratio_all));
							$sub_liquor_ratio=$sub_liquor_ratio_allArr[0]; 
	                        ?></p></td>
	                        <td width="120"><p><? echo $buyer_library_subcon[$buyer_all]; ?></p></td>
	                        <td width="130"><p><? echo $style_all;  ?></p></td>
	                        <td width="140"><p><? echo $po_all;?></p></td>
	                        <td width="100" align="right"><p><? echo number_format($po_qnty,0);  ?></p></td>
	                        <td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
	                        <td width="50"><p><? echo $row[csf('extention_no')]; ?></p></td>
	                        <td width="100"><p><? echo $color_library[$row[csf('color_id')]]; ?></p></td>
	                        <td width="100" align="center"><p><? echo $machine_cap_array[$row[csf('machine_id')]]["machine_no"]; ?></p></td>
	                        <td width="90" align="right"><p><? $machine_capacity=$machine_cap_array[$row[csf('machine_id')]]["prod_capacity"]; echo number_format($machine_capacity,2,'.',''); ?></p></td>
	                        <td width="70" align="right"><p><? echo change_date_format($row[csf('production_date')]); ?></p></td>
	                        <td width="100" align="right"><p><? echo number_format($row[csf('qnty')],2,'.',''); $total_sub_dyeing_qnty+=$row[csf('qnty')]; ?></p></td>
	                        <td width="100" align="right"><p><? echo number_format($row[csf('total_trims_weight')],2,'.',''); $total_trims_qnty+=$row[csf('total_trims_weight')]; ?></p></td>
	                        <td width="60" align="right"><p><? $ul_percent=$row[csf('qnty')]/$machine_capacity*100; echo number_format($ul_percent,2,'.',''); ?></p></td>
	                        <td width="60" align="center"><p><? $req_hour=""; $req_hour=$row[csf('dur_req_hr')].":".$row[csf('dur_req_min')]; echo $req_hour; ?></p></td>
	                        <td width="80" align="center"><p><? $start_time=''; $start_time=$load_data_sub[$row[csf('id')]]['end_hours'].':'.$load_data_sub[$row[csf('id')]]['end_minutes']; echo  $start_time; ?></p></td>
	                        <td width="80" align="center"><p><? $end_time=''; $end_time=$row[csf('end_hours')].':'.$row[csf('end_minutes')]; echo $end_time; ?></p></td>
	                         <td width="100" align="center" title="<? echo $sub_sid_ratio_all;?>"><p><?  echo $sub_liquor_ratio; ?></p></td>
	                         
	                        <td width="70" align="right"><p><? $start_time=strtotime($load_data_sub[$row[csf('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[csf('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
	                         if($timeDiffin>0)
	                         {  
	                             $time_used=convertMinutes2Hours($timeDiffin); 
	                             echo $time_used ;
	                         }
	                         else
	                         {
	                             echo "Invalid";
	                         }
	                         ?></p></td>
	                        <?
	                        
	                        $req_time=strtotime($row[csf('dur_req_hr')].":".$row[csf('dur_req_min')].":" . 00);
	                        $hour_dev_cal=0;$tot_deviation="";
	                        if($req_time!="")
	                        {
	                            $used_time=strtotime($time_used.':'. 00);
	                            $hour_dev_cal=($used_time-$req_time)/60;
	                            $tot_deviation=convertMinutes2Hours($hour_dev_cal); 
	                        }
	                        if($hour_dev_cal>0)
	                        {
	                            ?>
	                            <td width="60" align="center" bgcolor="#FF0000"><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
	                            <?
	                        }
	                        else
	                        {
	                            ?>
	                            <td width="60" align="center" ><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
	                            <?
	                        }
	                        ?>
	                        <td width="90"><p><? echo $color_range[$row[csf('color_range_id')]]; ?></p></td>

	                        <td width="110" align="right"><p><? $subcon_dye_cost =$chem_dye_cost_array[$row[csf('batch_id')]][6]; echo number_format($subcon_dye_cost,2,'.',''); ?></p></td>
	                        <td width="90" align="right" title="Total Dye Cost/Dyeing Qty"><p><?  $subcon_dye_cost_kg=$subcon_dye_cost/$row[csf('qnty')];
	                        echo number_format($subcon_dye_cost_kg,2,'.','');
	                        $total_sub_dye_cost+=$subcon_dye_cost;$total_sub_dye_cost_kg+=$subcon_dye_cost_kg; ?></p></td>

	                        <td width="110" align="right"><p><? $subcon_chem_cost =$chem_dye_cost_array[$row[csf('batch_id')]][5]; echo number_format($subcon_chem_cost,2,'.',''); ?></p></td>
	                        <td width="90" align="right" title="Total Chemical Cost/Dyeing Qty"><p><?  $subcon_chemi_cost_kg=$subcon_chem_cost/$row[csf('qnty')];
	                        echo number_format($subcon_chemi_cost_kg,2,'.','');
	                        $total_sub_chem_cost+=$subcon_chem_cost;$total_sub_chem_cost_kg+=$subcon_chemi_cost_kg; ?></p></td>

	                        <td width="130" align="center"><p><? echo $company_library[$row[csf('service_company')]]; ?></p></td>
	                        <td><p><? echo $row[csf('remarks')]; ?></p></td>
	                    </tr>
	                    <?
	                    $k++;
	                } 
	                ?>
                </table>
                <table width="2570px" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
                    <tfoot>
                        <th width="40">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="70"><strong>Total</strong></th>
                        <th width="100" id="value_total_sub_dyeing_qnty" align="right"><? echo number_format($total_sub_dyeing_qnty,2,'.',''); ?></th>
                        <th width="100" id="value_total_sub_trims_qnty" align="right">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                         <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="110" id="" ><? echo number_format($total_sub_dye_cost,2,'.',''); ?></th>
                        <th width="90" id="" align="right"><? echo number_format($total_sub_dye_cost/$total_sub_dyeing_qnty,2,'.',''); ?></th>
                        <th width="110" id="" ><? echo number_format($total_sub_chem_cost,2,'.',''); ?></th>
                        <th width="90" id="" align="right"><? echo number_format($total_sub_chem_cost/$total_sub_dyeing_qnty,2,'.',''); ?></th>
                        <th width="130">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>
            </div>     
            <?
        }
        if($type==0 || $type==3) // Sample
        {
            
            ?>
            <br/>
            <div align="left" style="background-color:#E1E1E1; color:#000; width:250px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Sample Dyeing Production</i></u></strong></div>
            <table width="2670" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
                <thead>
                    <th width="40">SL</th>
                    <th width="110">WO/Booking No</th>
                    <th width="90">Job No</th>
                    <th width="120">Buyer Name</th>
                    <th width="130">Style Ref.</th>
                    <th width="140">Order No</th>
                    <th width="100">Order Qty.</th>
                    <th width="60">GSM</th>
                    <th width="100">Fabric Color</th>
                    <th width="100">Batch No/ Lot No.</th>
                    <th width="50">Ext. No</th>
                    <th width="100">MC No.</th>
                    <th width="90">MC Capacity</th>
                    <th width="70">Dyeing Date</th>
                    <th width="100">Dyeing Qty</th>
                    <th width="60">UL %</th>
                    <th width="60">Hour Req.</th>
                    <th width="80">Loading Time</th>
                    <th width="80">Unloading Time</th>
                    <th width="100">Liquor Ratio</th>
                    <th width="70">Hour Used</th>
                    <th width="60">Hour Devi.</th>                
                    <th width="90">Process/ Color Range</th>
                    <th width="110">Total Dye Cost</th>
                    <th width="90">Dye Cost/Kg</th>
                    <th width="110">Total Chem Cost</th>
                    <th width="90">Chem Cost/Kg</th>
                    <th width="130">Dyeing Company</th>
                    <th>Remarks</th>
                </thead>
            </table>
            <div style="width:2690px; overflow-y:scroll; max-height:275px;" id="scroll_body" >
                <table width="2670" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body3">
                <?
                
                if($db_type==0)
                {
                    $sql_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, group_concat(c.po_id) as po_id, sum(c.batch_qnty) as qnty
	                from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
	                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.batch_against n(3,2) and b.is_sales!=1 $floor_cond $working_company_cond $lc_company_cond  $date_cond  $batch_cond $book_cond 
	                group by b.id,b.extention_no,a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                else if($db_type==2)
                {
                    $sql_dtls="SELECT b.id,b.extention_no,a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_without_order,b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, LISTAGG(cast(c.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.po_id) as po_id, sum(c.batch_qnty) as qnty
	                from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c 
	                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against in(3,2) and b.is_sales!=1 $floor_cond $working_company_cond $lc_company_cond  $date_cond $batch_cond $book_cond 
	                group by b.id,b.extention_no, a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id,b.booking_without_order, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by  a.process_end_date";
                }
                //echo $sql_dtls;
                $sql_result=sql_select($sql_dtls);
                foreach ( $sql_result as $row )
                {
                    $sam_batch_id_array[$row[csf('batch_id')]] = $row[csf('batch_id')];
                    $samp_booking_no_arr=explode("-",$row[csf('booking_no')]);
                    $sm_booking_no=$samp_booking_no_arr[1];
                    if($sm_booking_no=='SM' || $sm_booking_no=='SMN') 
                    {
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['id']=$row[csf('id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['extention_no']=$row[csf('extention_no')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['dyeing_company']=$row[csf('dyeing_company')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['service_company']=$row[csf('service_company')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['production_date']=$row[csf('production_date')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['process_end_date']=$row[csf('process_end_date')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['end_hours']=$row[csf('end_hours')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['end_minutes']=$row[csf('end_minutes')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['booking_without_order']=$row[csf('booking_without_order')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['dur_req_hr']=$row[csf('dur_req_hr')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['dur_req_min']=$row[csf('dur_req_min')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['prod_id']=$row[csf('prod_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['po_id']=$row[csf('po_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['qnty']=$row[csf('qnty')];
                    }
                }
				$samp_sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.entry_form=35  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
				//die;
				$samp_result_data_recipe=sql_select($samp_sql_recipe);
				foreach ($samp_result_data_recipe as $row)
				{
					$samp_batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
					if($samp_tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
					{
					$samp_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
					}
					$samp_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
				}
				$batch_quantity_arr=return_library_array( "select a.id,sum(b.batch_qnty) as  batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($sam_batch_id_array,0,'a.id')." group by a.id", "id", "batch_qnty");
               	// if($type==3 || $type==0)
                //{
                    $batchIds = "'" . implode ( "', '", $sam_batch_id_array ) . "'";
                    $baIds=chop($batchIds,','); $other_batch_cond_in="";
                    $ba_ids=count(array_unique(explode(",",$batchIds)));
                    if($db_type==2 && $ba_ids>1000)
                    {
                    $other_batch_cond_in=" and (";
                    $baIdsArr=array_chunk(explode(",",$baIds),999);
                    foreach($baIdsArr as $ids)
                    {
                    $ids=implode(",",$ids);
                    $other_batch_cond_in.=" b.batch_id in($ids) or"; 
                    }
                    $other_batch_cond_in=chop($other_batch_cond_in,'or ');
                    $other_batch_cond_in.=")";
                    }
                    else
                    {
                    $other_batch_cond_in=" and b.batch_id in($baIds)";
                    }

                    //============================= getting issue amount ================================
                    $chem_dye_cost_array=array(); //and b.batch_id in($batchIds) 
                    $chem_dye_cost_kg_array=array();
                //  $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b where a.id=b.trans_id AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 $batch_cond_in group by a.mst_id, b.batch_id "; 

                $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 $working_company_cond_issue $lc_company_cond_issue2 group by a.mst_id, b.batch_id,a.item_category ";

                //echo $chem_dye_cost_sql;//die;
                $chem_dye_cost_result=sql_select($chem_dye_cost_sql);
                //var_dump($chem_dye_cost_result);die;
                
                foreach($chem_dye_cost_result as $row)
                {
                    $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
                    $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
                    $total_batch_qnty=0;
                    foreach($batch_id_arr as $batch_ids)
                    {
                        $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
                        //echo $total_batch_qnty;die;
                        //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
                        
                    }
                    
                    foreach($batch_id_arr as $batch_id)
                    {
						if($batch_quantity_arr[$batch_id]>0)
					  	{							
                        	$chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
                        	$chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
						}
                        
                    }
                    
                   	// }
                    
                    /*
                        ################################

                            These code has no effect on this block so it is commented

                        ################################

                        if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company=$working_company_id";
                        // if($cbo_company==0 || $cbo_company=='') $lc_company_cond="";else $lc_company_cond="and a.company_id in($cbo_company)";

                        if(count($all_issue_id)>0)
                        {
                            $allIssueIds = implode(",", $all_issue_id);

                            $issueIds=chop($allIssueIds,','); $issue_cond_in="";
                            $iss_ids=count(array_unique(explode(",",$allIssueIds)));
                            if($db_type==2 && $iss_ids>1000)
                            {
                            $issue_cond_in=" and (";
                            $issueIdsArr=array_chunk(explode(",",$issueIds),999);
                            foreach($issueIdsArr as $ids)
                            {
                            $ids=implode(",",$ids);
                            $issue_cond_in.=" a.issue_id in($ids) or"; 
                            }
                            $issue_cond_in=chop($issue_cond_in,'or ');
                            $issue_cond_in.=")";
                            }
                            else
                            {
                            $issue_cond_in=" and a.issue_id in($issueIds)";
                            }
                            //and a.issue_id in($allIssueIds)
                            $sql_return_cost="SELECT p.batch_no as batch_id, sum(a.cons_amount) as cons_amount  from inv_issue_master p, inv_transaction a, inv_receive_master b where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1  $issue_cond_in  group by p.batch_no"; 
                            // echo $sql_return_cost;
                            $cost_return_data_arr=array();
                            $sql_return_res=sql_select($sql_return_cost);
                            foreach ($sql_return_res as $row)
                            {
                                $cost_return_data_arr[$row[csf('batch_id')]]=$row[csf('cons_amount')];
                                // $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
                            }
                        }
                    */
            	}
                
                $tot_rows=count($sql_result);
                $i=1;  $ul_percent=0; $tot_dye_qnty=0;$total_dye_chem_cost=$total_samp_dyeing_qnty=0;
                foreach ( $sample_batch_wise_arr as $batch_id=>$row )
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $po_id_arr=array_unique(explode(",",$row['po_id'])); 
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[('booking_no')]; ?></p></td>
                        <td width="90" align="center"><p>
                        <?
                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;
                        foreach($po_id_arr as $po_id)
                        {
                            if(!in_array($order_arr[$po_id]['job_no'],$job_chech))
                            {
                                $job_chech[]=$order_arr[$po_id]['job_no'];
                                if($job_all=="") $job_all=$order_arr[$po_id]['job_no']; else $job_all .=", ".$order_arr[$po_id]['job_no'];
                                if($buyer_all=="") $buyer_all=$buyer_library[$order_arr[$po_id]['buyer_name']]; else $buyer_all .=", ".$buyer_library[$order_arr[$po_id]['buyer_name']];
                                if($style_all=="") $style_all=$order_arr[$po_id]['style_ref_no']; else $style_all .=", ".$order_arr[$po_id]['style_ref_no'];
                            }
                            if($po_all=="") $po_all=$order_arr[$po_id]['po_number']; else $po_all .=", ".$order_arr[$po_id]['po_number'];
                            $po_qnty +=$order_arr[$po_id]['po_quantity'];
                        }
                        echo $job_all; 
						
						$samp_sid_ratio_all='';$samp_liquor_ratio_all='';

						$samp_process_id=rtrim($samp_batch_recipe_arr[$batch_id]['sub_process_id'],',');
						$samp_process_ids=array_unique(explode(",",$samp_process_id));
						
						foreach($samp_process_ids as $sid)
						{
							$liquor_ratio=$samp_batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
							if($liquor_ratio>0)
							{
							$seq_no=$samp_batch_recipe_subpro_arr[$batch_id][$sid]['seq_no'];
							$samp_sid_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.'&#013;';
							
							$samp_liquor_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.',';
							}
							
						}
						$samp_liquor_ratio_all=rtrim($samp_liquor_ratio_all,',');
						$samp_liquor_ratio_allArr=array_unique(explode(",",$samp_liquor_ratio_all));
						$samp_liquor_ratio=$samp_liquor_ratio_allArr[0];
						
                        ?></p></td>
                        <td width="120"><p><? echo $buyer_all; echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']]; ?></p></td>
                        <td width="130"><p><? echo $style_all;  ?></p></td>
                        <td width="140"><p><? echo $po_all;?></p></td>
                        <td width="100" align="right"><p><? echo number_format($po_qnty,0);  ?></p></td>
                        <td width="60" align="center"><p><? echo $gsm_library[$row[('prod_id')]]; ?></p></td>
                        <td width="100"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
                        <td width="100"><p><? echo $row[('batch_no')]; ?></p></td>
                        <td width="50"><p><? echo $row[('extention_no')]; ?></p></td>
                        <td width="100" align="center"><p><? echo $machine_cap_array[$row[('machine_id')]]["machine_no"]; ?></p></td>
                        <td width="90" align="right"><p><? $machine_capacity=$machine_cap_array[$row[('machine_id')]]["prod_capacity"]; echo number_format($machine_capacity,2,'.',''); ?></p></td>
                        <td width="70" align="right"><p><? echo change_date_format($row[('production_date')]); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[('qnty')],2,'.',''); $total_samp_dyeing_qnty+=$row[('qnty')]; ?></p></td>
                        <td width="60" align="right"><p><? $ul_percent=$row[('qnty')]/$machine_capacity*100; echo number_format($ul_percent,2,'.',''); ?></p></td>
                        <td width="60" align="center"><p><? $req_hour=""; $req_hour=$row[('dur_req_hr')].":".$row[('dur_req_min')]; echo $req_hour; ?></p></td>
                        <td width="80" align="center"><p><? $start_time=''; $start_time=$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']; echo  $start_time; ?></p></td>
                        <td width="80" align="center"><p><? $end_time=''; $end_time=$row[('end_hours')].':'.$row[('end_minutes')]; echo $end_time; ?></p></td>
                        <td width="100" align="center" title="<? echo $samp_sid_ratio_all;?>"><p><?  echo $samp_liquor_ratio; ?></p></td>
                        <td width="70" align="right"><p><? $start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
                        
                        if($timeDiffin>0)
                        {
                             $time_used=convertMinutes2Hours($timeDiffin); 
                             echo $time_used; // 
                        }
                        else
                        {
                            echo "Invalid";
                        }
                         ?></p></td>
                        <?
                        
                        $req_time=strtotime($row[('dur_req_hr')].":".$row[('dur_req_min')].":" . 00);
                        $hour_dev_cal=0;$tot_deviation="";
                        if($req_time!="")
                        {
                            $used_time=strtotime($time_used.':'. 00);
                            $hour_dev_cal=($used_time-$req_time)/60;
                            $tot_deviation=convertMinutes2Hours($hour_dev_cal); 
                        }
                        if($hour_dev_cal>0)
                        {
                            ?>
                            <td width="60" align="center" bgcolor="#FF0000"><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        else
                        {
                            ?>
                            <td width="60" align="center" ><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        ?>
                        <td width="90"><p><? echo $color_range[$row[('color_range_id')]]; ?></p></td>

                        <td width="110" align="right" title="Batch ID=<? echo $row[('batch_id')];?>"><p><? $samp_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6];  if($samp_dye_cost) echo number_format($samp_dye_cost,2,'.','');else echo "";// echo number_format($samp_dye_cost,4,'.','');?></p></td>
                        <td width="90" align="right" title="Tot Dye Cost/Dyeing Qty"><p><? 
						 if($samp_dye_cost) $tot_samp_dye_cost_kg=$samp_dye_cost/$row[('qnty')];
						 else $tot_samp_dye_cost_kg=0;
                        echo number_format($tot_samp_dye_cost_kg,2,'.','');
                        $total_samp_dye_cost+=$samp_dye_cost;$total_samp_dye_cost_kg+=$tot_samp_dye_cost_kg;?> </p></td>

                        <td width="110" align="right" title="Batch ID=<? echo $row[('batch_id')];?>"><p><? $samp_chem_cost =$chem_dye_cost_array[$row[('batch_id')]][5]; echo number_format($samp_chem_cost,2,'.','');?></p></td>
                        <td width="90" align="right" title="Tot Chemical Cost/Dyeing Qty"><p><? $tot_samp_chemi_cost_kg=$samp_chem_cost/$row[('qnty')];
                        echo number_format($tot_samp_chemi_cost_kg,2,'.','');
                        $total_samp_chem_cost+=$samp_chem_cost;$total_samp_chem_cost_kg+=$tot_samp_chemi_cost_kg;?> </p></td>

                        <td width="130" align="center"><p><? echo $company_library[$row[('service_company')]]; ?></p></td>
                        <td><p><? echo $row[('remarks')]; ?></p></td>
                    </tr>
                    <?
                    $i++;
                } 
                ?>
                </table>
                <table width="2670px" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
                    <tfoot>
                        <th width="40">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="70"><strong>Total</strong></th>
                        <th width="100" id="value_dyeing_qnty" align="right"><? echo number_format($total_samp_dyeing_qnty,2,'.',''); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="110" id="value_dye_cost"><? echo number_format($total_samp_dye_cost,2,'.',''); ?></th>
                        <th width="90"  align="right"><? echo number_format(($total_samp_dye_cost/$total_samp_dyeing_qnty),2,'.',''); ?></th>
                        <th width="110" id="value_chem_cost"><? echo number_format($total_samp_chem_cost,2,'.',''); ?></th>
                        <th width="90"  align="right"><? echo number_format(($total_samp_chem_cost/$total_samp_dyeing_qnty),2,'.',''); ?></th>
                        <th width="130">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div> 
            <?
        }
        if($type==0 || $type==4) // Others, Gmts Dyeing and Without Booking
        {
            
            ?>
            <div align="left" style="background-color:#E1E1E1; color:#000; width:250px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Others Dyeing Production</i></u></strong></div>
            <table width="2670" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
                <thead>
                    <th width="40">SL</th>
                    <th width="110">WO/Booking No</th>
                    <th width="90">Job No</th>
                    <th width="120">Buyer Name</th>
                    <th width="130">Style Ref.</th>
                    <th width="140">Order No</th>
                    <th width="100">Order Qty.</th>
                    <th width="60">GSM</th>
                    <th width="100">Fabric Color</th>
                    <th width="100">Batch No/ Lot No.</th>
                    <th width="50">Ext. No</th>
                    <th width="100">MC No.</th>
                    <th width="90">MC Capacity</th>
                    <th width="70">Dyeing Date</th>
                    <th width="100">Dyeing Qty</th>
                    <th width="60">UL %</th>
                    <th width="60">Hour Req.</th>
                    <th width="80">Loading Time</th>
                    <th width="80">Unloading Time</th>
                    <th width="100">Liquor Ratio</th>
                    <th width="70">Hour Used</th>
                    <th width="60">Hour Devi.</th>                
                    <th width="90">Process/ Color Range</th>
                    <th width="110">Total Dye Cost</th>
                    <th width="90">Dye Cost/Kg</th>
                    <th width="110">Total Chem Cost</th>
                    <th width="90">Chem Cost/Kg</th>
                    <th width="130">Dyeing Company</th>
                    <th>Remarks</th>
                </thead>
            </table>
            <div style="width:2690px; overflow-y:scroll; max-height:275px;" id="scroll_body" >
                <table width="2670" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body4">
                <?  
                /*
                    $buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
                    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
                    $gsm_library=return_library_array( "select id,gsm from  product_details_master", "id", "gsm");
                    
                    $order_arr=array();
                    $sql_order="Select b.id, a.job_no, a.buyer_name, a.style_ref_no, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
                    $result_sql_order=sql_select($sql_order);
                    foreach($result_sql_order as $row)
                    {
                        $order_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
                        $order_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
                        $order_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
                        $order_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
                        $order_arr[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
                    }

                    $non_order_arr=array();
                    $sql_non_order="SELECT a.company_id, a.buyer_id as buyer_name, b.booking_no, b.bh_qty 
                    from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
                    where a.booking_no=b.booking_no and a.booking_type=4 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
                    $result_sql_order=sql_select($sql_non_order);
                    foreach($result_sql_order as $row)
                    {
                        
                        $non_order_arr[$row[csf('booking_no')]]['buyer_name']=$row[csf('buyer_name')];
                        $non_order_arr[$row[csf('booking_no')]]['bh_qty']=$row[csf('bh_qty')];
                    }
                
                    // echo "<pre>";
                    // print_r($non_order_arr);
                    $load_data=array();
                    $load_time_data=sql_select("select a.id,a.batch_id,a.batch_no,a.load_unload_id,a.process_end_date,a.end_hours,a.end_minutes from pro_fab_subprocess a where a.load_unload_id=1 and a.entry_form=35 and  a.status_active=1  and a.is_deleted=0 $working_company_cond $lc_company_cond");
                    
                    
                    foreach($load_time_data as $row_time)// for Loading time
                    {
                        $load_data[$row_time[csf('batch_id')]]['process_end_date']=$row_time[csf('process_end_date')];
                        $load_data[$row_time[csf('batch_id')]]['end_hours']=$row_time[csf('end_hours')];
                        $load_data[$row_time[csf('batch_id')]]['end_minutes']=$row_time[csf('end_minutes')];
                    }
                */
                
                if($db_type==0)
                {
                    $sql_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, group_concat(c.po_id) as po_id, sum(c.batch_qnty) as qnty
	                from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
	                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.batch_against in(5,7) and b.is_sales!=1  $floor_cond $date_cond $working_company_cond $lc_company_cond    $batch_cond $book_cond 
	                group by b.id,b.extention_no,a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                else if($db_type==2)
                {
                    $sql_dtls="SELECT b.id,b.extention_no,a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, LISTAGG(cast(c.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.po_id) as po_id, sum(c.batch_qnty) as qnty
	                from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c 
	                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against  in(5,7) and b.is_sales!=1  $floor_cond $working_company_cond $lc_company_cond $date_cond $batch_cond $book_cond 
	                group by b.id,b.extention_no, a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                //echo $sql_dtls; die;
                $sql_result=sql_select($sql_dtls);

                if(!empty($sql_result))
                {
                    // ================== getting batch id =========================
                    $batch_id_array = array();
                    foreach ( $sql_result as $row )
                    {
                        $batch_id_array[$row[csf('batch_id')]] = $row[csf('batch_id')];
                        $booking_no_arr=explode("-",$row[csf('booking_no')]);
                        $fb_booking_no=$booking_no_arr[1];
                        //if($fb_booking_no=='Fb') //echo $fb_booking_no.'A';else echo $fb_booking_no.'B';
                        //{
                        $other_batch_wise_array[$row[csf('batch_id')]]['id']= $row[csf('id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['batch_id']= $row[csf('batch_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['extention_no']= $row[csf('extention_no')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['dyeing_company']= $row[csf('dyeing_company')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['service_company']= $row[csf('service_company')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['production_date']= $row[csf('production_date')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['process_end_date']= $row[csf('process_end_date')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['machine_id']= $row[csf('machine_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['end_hours']= $row[csf('end_hours')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['end_minutes']= $row[csf('end_minutes')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['remarks']= $row[csf('remarks')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['batch_no']= $row[csf('batch_no')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['color_id']= $row[csf('color_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['booking_no']= $row[csf('booking_no')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['color_range_id']= $row[csf('color_range_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['dur_req_hr']= $row[csf('dur_req_hr')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['dur_req_min']= $row[csf('dur_req_min')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['prod_id']= $row[csf('prod_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['po_id']= $row[csf('po_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['qnty']= $row[csf('qnty')];
                        //}
                    }

                    $con = connect();
                    $r_id2=execute_query("delete from tmp_batch_or_iss where userid=$user_id and block=4");
                    if($r_id2)
                    {
                        oci_commit($con);
                    }

                    if(!empty($batch_id_array))
                    {
                        foreach ($batch_id_array as $batch_id_key => $batch_id_val) 
                        {
                            $r_id_1_4=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_no, type, block) values ($user_id,'".$batch_id_key."',1,4".")");
                            if($r_id_1_4) 
                            {
                                $r_id_1_4=1;
                            } 
                            else 
                            {
                                echo "insert into tmp_batch_or_iss (userid, batch_issue_no, type, block) values ($user_id,'".$batch_id_key."',1,4".")";
                                oci_rollback($con);
                                die;
                            }
                        }

                        if($r_id_1_4)
                        {
                            oci_commit($con);
                        }
                        else
                        {
                            oci_rollback($con);
                            disconnect($con);
                        }
                    }

                    //============================= getting issue amount ================================
                    $chem_dye_cost_array=array(); //and b.batch_id in($batchIds) 
                    $chem_dye_cost_kg_array=array();

                    $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c, tmp_batch_or_iss d where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 and b.batch_id=d.batch_issue_no and d.userid=$user_id and d.type=1 and d.block=4  $working_company_cond_issue $lc_company_cond_issue2 group by a.mst_id, b.batch_id,a.item_category ";
                    $chem_dye_cost_result=sql_select($chem_dye_cost_sql);

                    $all_issue_id =array();
                    foreach($chem_dye_cost_result as $row)
                    {
                        $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
                        $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
                        $total_batch_qnty=0;
                        foreach($batch_id_arr as $batch_ids)
                        {
                            $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
                            //echo $total_batch_qnty;die;
                            //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
                            
                        }
                        
                        foreach($batch_id_arr as $batch_id)
                        {
                            $chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
                            $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
                            
                        }
                    }

                    // ============================ getting issue return amount =============================
                    if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company in($working_company_id)";

                    if(count($all_issue_id)>0)
                    {
                        foreach ($all_issue_id as $issue_id_key => $issue_id_val) 
                        {
                            $r_id4=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,4".")");
                            if($r_id4) 
                            {
                                $r_id4=1;
                            } 
                            else 
                            {
                                echo "insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,4".")";
                                oci_rollback($con);
                                die;
                            }
                        }

                        if($r_id4)
                        {
                            oci_commit($con);
                        }
                        else
                        {
                            oci_rollback($con);
                            disconnect($con);
                        }

                        $sql_return_cost="SELECT p.batch_no as batch_id,a.item_category, sum(a.cons_amount) as cons_amount  from inv_issue_master p, inv_transaction a, inv_receive_master b, tmp_batch_or_iss c where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1 and a.issue_id=c.batch_issue_id and c.block=4 and c.type=2 and userid= $user_id  group by p.batch_no,a.item_category"; 

                        // echo $sql_return_cost;
                        $cost_return_data_arr=array();
                        $sql_return_res=sql_select($sql_return_cost);
                        foreach ($sql_return_res as $row)
                        {
                            $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
                            // $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
                        }
                    }
                }
                
                
                // print_r($cost_return_data_arr);
				$other_sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.entry_form=35  and b.batch_against  in(5,7) and b.is_sales!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
				//die;
				$other_result_data_recipe=sql_select($other_sql_recipe);
				foreach ($other_result_data_recipe as $row)
				{
					$other_batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
					if($other_tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
					{
					$other_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
					}
					$other_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
				}
				
                $tot_rows=count($sql_result);
                $m=1;  $ul_percent=0; $tot_dye_qnty=0;$total_other_dye_chem_cost=0;
                foreach ( $other_batch_wise_array as $batch_id=>$row )
                {
                    if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $po_id_arr=array_unique(explode(",",$row[('po_id')])); 
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trother_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="trother_<? echo $m; ?>">
                        <td width="40"><? echo $m; ?></td>
                        <td width="110"><p><? echo $row[('booking_no')]; ?></p></td>
                        <td width="90" align="center"><p>
                        <?
                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;
                        foreach($po_id_arr as $po_id)
                        {
                            if(!in_array($order_arr[$po_id]['job_no'],$job_chech))
                            {
                                $job_chech[]=$order_arr[$po_id]['job_no'];
                                if($job_all=="") $job_all=$order_arr[$po_id]['job_no']; else $job_all .=", ".$order_arr[$po_id]['job_no'];
                                if($buyer_all=="") $buyer_all=$buyer_library[$order_arr[$po_id]['buyer_name']]; else $buyer_all .=", ".$buyer_library[$order_arr[$po_id]['buyer_name']];
                                if($style_all=="") $style_all=$order_arr[$po_id]['style_ref_no']; else $style_all .=", ".$order_arr[$po_id]['style_ref_no'];
                            }
                            if($po_all=="") $po_all=$order_arr[$po_id]['po_number']; else $po_all .=", ".$order_arr[$po_id]['po_number'];
                            $po_qnty +=$order_arr[$po_id]['po_quantity'];
                        }
                        echo $job_all; 
						
						$other_sid_ratio_all='';$other_liquor_ratio_all='';

						$sub_process_id=rtrim($other_batch_recipe_arr[$batch_id]['sub_process_id'],',');
						$sub_process_ids=array_unique(explode(",",$sub_process_id));
						
						foreach($sub_process_ids as $sid)
						{
							$liquor_ratio=$other_batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
							if($liquor_ratio>0)
							{
							$seq_no=$other_batch_recipe_subpro_arr[$batch_id][$sid]['seq_no'];
							$other_sid_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.'&#013;';
							
							$other_liquor_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.',';
							}
							
						}
						$other_liquor_ratio_all=rtrim($other_liquor_ratio_all,',');
						$other_liquor_ratio_allArr=array_unique(explode(",",$other_liquor_ratio_all));
						$other_liquor_ratio=$other_liquor_ratio_allArr[0];
						
                        ?></p></td>
                        <td width="120"><p><? echo $buyer_all; echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']]; ?></p></td>
                        <td width="130"><p><? echo $style_all;  ?></p></td>
                        <td width="140"><p><? echo $po_all;?></p></td>
                        <td width="100" align="right"><p><? echo number_format($po_qnty,0);  ?></p></td>
                        <td width="60" align="center"><p><? echo $gsm_library[$row[('prod_id')]]; ?></p></td>
                        <td width="100"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
                        <td width="100"><p><? echo $row[('batch_no')]; ?></p></td>
                        <td width="50"><p><? echo $row[('extention_no')]; ?></p></td>
                        <td width="100" align="center"><p><? echo $machine_cap_array[$row[('machine_id')]]["machine_no"]; ?></p></td>
                        <td width="90" align="right"><p><? $machine_capacity=$machine_cap_array[$row[('machine_id')]]["prod_capacity"]; echo number_format($machine_capacity,2,'.',''); ?></p></td>
                        <td width="70" align="right"><p><? echo change_date_format($row[('production_date')]); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[('qnty')],2,'.',''); $total_other_dyeing_qnty+=$row[('qnty')]; ?></p></td>
                        <td width="60" align="right"><p><? $ul_percent=$row[('qnty')]/$machine_capacity*100; echo number_format($ul_percent,2,'.',''); ?></p></td>
                        <td width="60" align="center"><p><? $req_hour=""; $req_hour=$row[('dur_req_hr')].":".$row[('dur_req_min')]; echo $req_hour; ?></p></td>
                        <td width="80" align="center"><p><? $start_time=''; $start_time=$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']; echo  $start_time; ?></p></td>
                        <td width="80" align="center"><p><? $end_time=''; $end_time=$row[('end_hours')].':'.$row[('end_minutes')]; echo $end_time; ?></p></td>
                        <td width="100" title="<? echo $other_sid_ratio_all;?>" align="center"><p><? echo $other_liquor_ratio; ?></p></td>
                        <td width="70" align="right"><p><? $start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
                        
                        if($timeDiffin>0)
                        {
                             $time_used=convertMinutes2Hours($timeDiffin); 
                             echo $time_used; // 
                        }
                        else
                        {
                            echo "Invalid";
                        }
                         ?></p></td>
                        <?
                        
                        $req_time=strtotime($row[('dur_req_hr')].":".$row[('dur_req_min')].":" . 00);
                        $hour_dev_cal=0;$tot_deviation="";
                        if($req_time!="")
                        {
                            $used_time=strtotime($time_used.':'. 00);
                            $hour_dev_cal=($used_time-$req_time)/60;
                            $tot_deviation=convertMinutes2Hours($hour_dev_cal); 
                        }
                        if($hour_dev_cal>0)
                        {
                            ?>
                            <td width="60" align="center" bgcolor="#FF0000"><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        else
                        {
                            ?>
                            <td width="60" align="center" ><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        ?>
                        <td width="90"><p><? echo $color_range[$row[('color_range_id')]]; ?></p></td>

                        <td width="110" align="right" title="Batch ID=<? echo $row[('batch_id')];?>"><p><? $other_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$row[('batch_id')]][6]; echo number_format($other_dye_cost,2,'.','');?></p></td>
                        <td width="90" align="right" title="Tot Dye Cost/Dyeing Qty"><p><? 
                        $tot_dye_cost_kg=$other_dye_cost/$row[('qnty')]; echo number_format($tot_dye_cost_kg,2,'.','');
                        //echo number_format(($chem_dye_cost_kg_array[$row[('batch_id')]]-$cost_return_data_arr[$row[('batch_id')]]),2,'.','');
                         $total_other_dye_cost+=$other_dye_cost;$total_other_dye_cost_kg+=$tot_dye_cost_kg;?> </p></td>

                        <td width="110" align="right" title="Batch ID=<? echo $row[('batch_id')];?>"><p><? $other_chem_cost =$chem_dye_cost_array[$row[('batch_id')]][5] - $cost_return_data_arr[$row[('batch_id')]][5]; echo number_format($other_chem_cost,2,'.','');?></p></td>
                        <td width="90" align="right" title="Tot Chemical Cost/Dyeing Qty"><p><? 
                        $tot_chemi_cost_kg=$other_chem_cost/$row[('qnty')]; echo number_format($tot_chemi_cost_kg,2,'.','');
                        //echo number_format(($chem_dye_cost_kg_array[$row[('batch_id')]]-$cost_return_data_arr[$row[('batch_id')]]),2,'.','');
                         $total_other_chem_cost+=$other_chem_cost;$total_other_chem_cost_kg+=$tot_chemi_cost_kg;?> </p></td>

                        <td width="130" align="center"><p><? echo $company_library[$row[('service_company')]]; ?></p></td>
                        <td><p><? echo $row[('remarks')]; ?></p></td>
                    </tr>
                    <?
                    $m++;
                } 
                ?>
                </table>
                <table width="2670px" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
                    <tfoot>
                        <th width="40">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="70"><strong>Total</strong></th>
                        <th width="100" id="value_total_dyeing_qnty" align="right"><? echo number_format($total_other_dyeing_qnty,2,'.',''); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="110" id="value_total_dye_cost"><? echo number_format($total_other_dye_cost,2,'.',''); ?></th>
                        <th width="90" align="right"><? echo number_format($total_other_dye_cost/$total_other_dyeing_qnty,2,'.',''); ?></th>
                        <th width="110" id="value_total_chem_cost"><? echo number_format($total_other_chem_cost,2,'.',''); ?></th>
                        <th width="90" align="right"><? echo number_format($total_other_chem_cost/$total_other_dyeing_qnty,2,'.',''); ?></th>
                        <th width="130">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div> 
            <?
        }
        ?>    
    </div>
  
    <?  
    //echo "$total_data****requires/$filename****$tot_rows****$type_id";
    foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$type_id";
    exit();
}

if($action=="report_generate2") // show 2
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    
    //echo $datediff;
    $cbo_company=str_replace("'","",$cbo_company_id);
    $working_company_id=str_replace("'","",$cbo_working_company_id);
    $cbo_unit=str_replace("'","",$cbo_unit_id);
	$txt_batch_no=str_replace("'","",$txt_batch_no);
	$batch_id=str_replace("'","",$batch_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_hide_booking_id=str_replace("'","",$txt_hide_booking_id);
	$type_id=str_replace("'","",$type_id);
	
	//batch_id*txt_batch_no*txt_booking_no*txt_hide_booking_id
    $date_from=str_replace("'","",$txt_date_from);
    $date_to=str_replace("'","",$txt_date_to);
    $type=str_replace("'","",$cbo_type);
    //$floor_id =str_replace("'","",$floor_id );
    if($working_company_id==0) $working_company_cond="";else $working_company_cond="and a.service_company in($working_company_id)";
    if($cbo_company==0 || $cbo_company=='') $lc_company_cond="";else $lc_company_cond="and a.company_id in($cbo_company)";
    
    if($working_company_id==0) $working_company_cond_issue="";else $working_company_cond_issue="and c.knit_dye_company in($working_company_id)";
    if($cbo_company==0 || $cbo_company=='') $lc_company_cond_issue="";else $lc_company_cond_issue="and c.company_id in($cbo_company)";
	 if($cbo_company==0 || $cbo_company=='') $lc_company_cond_issue2="";else $lc_company_cond_issue2="and c.lc_company in($cbo_company)";
   
    if($txt_batch_no!="" && $batch_id=="") $batch_cond="and b.batch_no='$txt_batch_no'";
	else  if($txt_batch_no!="" && $batch_id!="") $batch_cond="and b.id in($batch_id)";
	else $batch_cond="";
	 if($txt_booking_no!="" && $txt_hide_booking_id=="") $book_cond="and b.booking_no like '%$txt_booking_no%'";
	else  if($txt_booking_no!="" && $txt_hide_booking_id!="") $book_cond="and b.booking_no_id in($txt_hide_booking_id)";
	else $book_cond="";
	if($date_from!="" && $date_to!="" )
	{
	$date_cond="and a.process_end_date between '$date_from' and '$date_to'";
	}
	else $date_cond="";
   
    if($cbo_unit!=0) $floor_cond="and a.floor_id=$cbo_unit";else $floor_cond="";
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
 

    
    $machine_cap_array=array();
    $mach_cap_sql=sql_select("select id, machine_no, sum(prod_capacity) as prod_capacity from lib_machine_name where status_active=1 and is_deleted=0 group by id, machine_no");
    foreach($mach_cap_sql as $row)
    {
        $machine_cap_array[$row[csf('id')]]["machine_no"]=$row[csf('machine_no')];
        $machine_cap_array[$row[csf('id')]]["prod_capacity"]=$row[csf('prod_capacity')];
    }
    
	function convertMinutes2Hours($Minutes)
	{
	    if ($Minutes < 0)
	    {
	        $Min = Abs($Minutes);
	    }
	    else
	    {
	        $Min = $Minutes;
	    }
	    $iHours = Floor($Min / 60);
	    $Minutes = ($Min - ($iHours * 60)) / 100;
	    $Minutes = number_format($Minutes,2,'.','');
	    $tHours = $iHours + $Minutes;
	    $tHours = number_format($tHours,2,'.','');
	    if ($Minutes < 0)
	    {
	        $tHours = $tHours * (-1);
	    }
	    $aHours = explode(".", $tHours);
	    $iHours = $aHours[0];
	    if (empty($aHours[1]))
	    {
	        $aHours[1] = "00";
	    }
	    $Minutes = $aHours[1];
	    if (strlen($Minutes) < 2)
	    {
	        $Minutes = $Minutes ."0";
	    }
	    $tHours = $iHours .":". $Minutes;
	    return $tHours;
	}
	    
	ob_start(); 
	    //$table_width=90+($datediff*160);
	     $width=1530;
	?>
	    <div>
	        <table width="<? echo $width;?>px" cellpadding="0" cellspacing="0" id="caption" align="center">
	            <tr>
	               <td align="center" width="100%" colspan="25" class="form_caption" ><strong style="font-size:18px"><? echo $company_library[$cbo_company];?></strong></td>
	            </tr> 
	            <tr>  
	               <td align="center" width="100%" colspan="25" class="form_caption" ><strong style="font-size:14px">Daily Dyeing Produciton Analysis Report</strong></td>
	            </tr>
	            <tr>  
	               <td align="center" width="100%" colspan="25" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
	            </tr>  
	        </table>
	        <?
	        $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	        if($type==0 || $type==1 || $type==3 || $type==4)
	        {
	            $buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
	            $gsm_library=return_library_array( "select id,gsm from  product_details_master", "id", "gsm");
	             $non_order_arr=array();
				$sql_non_order="SELECT a.company_id, a.buyer_id as BUYER_NAME, b.booking_no as BOOKING_NO, b.bh_qty as BH_QTY
				from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
				where a.booking_no=b.booking_no and a.booking_type=4 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
				$result_sql_non_order=sql_select($sql_non_order);
				foreach($result_sql_non_order as $row)
				{
					$non_order_arr[$row[csf('BOOKING_NO')]]['buyer_name']=$row[csf('BUYER_NAME')];
					$non_order_arr[$row[csf('BOOKING_NO')]]['bh_qty']=$row[csf('BH_QTY')];
				}
				unset($result_sql_non_order);
	            $load_data=array();
	                $load_time_data=sql_select("select a.id,a.batch_id,a.batch_no,a.load_unload_id,a.process_end_date,a.end_hours,a.end_minutes from pro_fab_subprocess a where a.load_unload_id=1 and a.entry_form=35 and  a.status_active=1  and a.is_deleted=0 $working_company_cond $lc_company_cond");
	            foreach($load_time_data as $row_time)// for Loading time
	            {
	                $load_data[$row_time[csf('batch_id')]]['process_end_date']=$row_time[csf('process_end_date')];
	                $load_data[$row_time[csf('batch_id')]]['end_hours']=$row_time[csf('end_hours')];
	                $load_data[$row_time[csf('batch_id')]]['end_minutes']=$row_time[csf('end_minutes')];
	            }
	        }


	        if($type==0 || $type==1)
	        {
	            
	          	$width=1600;
			    ?>
	            <style>
				span::before {content: '\A'; white-space: pre;}
				</style>
	            <div align="left" style="background-color:#E1E1E1; color:#000; width:250px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Dyeing Production</i></u></strong></div>
	                <table width="<? echo $width;?>" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >	                   
	                    <thead>
		                    <tr>
		                    	<th  colspan="15">&nbsp;</th>
		                        <th colspan="4">First Dying And Finishing Cost</th>
		                        <th colspan="4">Subsequent Dyeing and Finishing Cost</th>
		                        <th colspan="2">&nbsp;</th>
		                    </tr>
		                    <tr>
		                        <th width="30">SL</th>
		                        <th width="40">M/C No</th>
		                        <th width="50">Batch No</th>
		                        <th width="70">Ref. No</th>
		                        <th width="70">Buyer</th>
		                        <th width="70">Booking No</th>
		                        <th width="70">Job no</th>
		                        <th width="70">Style</th>
		                        <th width="70">Color Name</th>
		                        <th width="70">Fabric Type</th>
		                        <th width="70">Dyeing Qty (Kg)</th>
		                        <th width="70">Trims weight</th>
		                        <th width="50">Loading Time</th>
		                        <th width="50">Unloading Time</th>
		                        <th width="50">Hour Used</th>
		                        
		                        <th width="70">Ttl Chem Cost (Tk)</th>
		                        <th width="70">Ttl Dyes Cost (Tk)</th>
		                        <th width="70">Ttl Chem + Dyes Cost (Tk)</th>
		                        <th width="70">Cost Per Kg (Tk)</th>
		                        
		                        <th width="70">Ttl Chem Cost (Tk)</th>
		                        <th width="70">Ttl Dyes Cost (Tk)</th>
		                        <th width="70">Ttl Chem + Dyes Cost (Tk)</th>
		                        <th width="70">Re-Dye Cost per kg (Tk)</th>
		                        <th width="70">Total Cost (Tk)</th>
		                        <th>Total Per Kg Cost (Tk)</th>
		                        </tr>
	                    </thead>
	                </table>
	            <div style="width:<? echo $width+20;?>px; overflow-y:scroll; max-height:275px;" id="scroll_body" >
	                <table width="<? echo $width;?>" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body">
	                <?  
	                
	                
	                if($db_type==0)
	                {
	                    $sql_dtls="SELECT b.id,b.extention_no, a.batch_id,a.fabric_type, b.batch_against,a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks,b.booking_without_order,b.booking_no_id, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min,b.total_trims_weight, (c.prod_id) as prod_id, c.po_id as po_id, (c.batch_qnty) as qnty
	                    from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
	                    where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.batch_against in(1,2) and b.is_sales!=1   $batch_cond $book_cond $floor_cond $working_company_cond $lc_company_cond   $date_cond
	                     order by a.process_end_date";
	                }
	                else if($db_type==2)
	                {
	                    $sql_dtls="SELECT b.id,b.extention_no,a.batch_id,a.fabric_type,b.batch_against, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no,b.booking_no_id, b.color_id, b.booking_without_order,b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min,b.total_trims_weight, (c.prod_id) as prod_id,  c.po_id as po_id, (c.batch_qnty) as qnty
	                    from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c 
	                    where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id   and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against  in(1,2) and b.is_sales!=1 $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond 
	                     order by a.process_end_date";
	                }
	              	//echo $sql_dtls;die;

	                $sql_result=sql_select($sql_dtls);
	                if(!empty($sql_result))
	                {
	                    // ================== getting batch id =========================
	                    $batch_id_array = array();
						$all_prod_id="";
	                    foreach ( $sql_result as $row )
	                    {
	                        $batch_id_array[$row[csf('batch_id')]] = $row[csf('batch_id')];
	                        $booking_no_arr=explode("-",$row[csf('booking_no')]);
	                        $fb_booking_no=$booking_no_arr[1];
							$booking_without_order=$row[csf('booking_without_order')];
							$booking_no_id=$row[csf('booking_no_id')];
	                        if($fb_booking_no=='FB' || $fb_booking_no=='Fb') //echo $fb_booking_no.'A';else echo $fb_booking_no.'B';
	                        {
	                        $self_batch_wise_array[$row[csf('batch_id')]]['id']= $row[csf('id')];
							$self_batch_wise_array[$row[csf('batch_id')]]['batch_against']= $row[csf('batch_against')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['batch_id']= $row[csf('batch_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['extention_no']= $row[csf('extention_no')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['dyeing_company']= $row[csf('dyeing_company')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['service_company']= $row[csf('service_company')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['production_date']= $row[csf('production_date')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['process_end_date']= $row[csf('process_end_date')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['fabric_type']= $fabric_type_for_dyeing[$row[csf('fabric_type')]]; 
							$self_batch_wise_array[$row[csf('batch_id')]]['machine_id']= $row[csf('machine_id')];
							$self_batch_wise_array[$row[csf('batch_id')]]['booking_no_id']= $row[csf('booking_no_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['end_hours']= $row[csf('end_hours')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['end_minutes']= $row[csf('end_minutes')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['remarks']= $row[csf('remarks')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['batch_no']= $row[csf('batch_no')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['color_id']= $row[csf('color_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['booking_no']= $row[csf('booking_no')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['color_range_id']= $row[csf('color_range_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['dur_req_hr']= $row[csf('dur_req_hr')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['dur_req_min']= $row[csf('dur_req_min')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['trims_qty']+= $row[csf('total_trims_weight')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['prod_id']= $row[csf('prod_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['po_id'].= $row[csf('po_id')].',';
	                        $self_batch_wise_array[$row[csf('batch_id')]]['qnty']+= $row[csf('qnty')];
							if($all_prod_id=="") $all_prod_id=$row[csf('prod_id')];else $all_prod_id.=",".$row[csf('prod_id')];
							     $po_id_array[$row[csf('po_id')]]= $row[csf('po_id')];
	                        }
							if($booking_without_order==1)
							{
								$non_booking_id_array[$booking_no_id]= $booking_no_id;
							}
	                    }
						$po_id_cond=where_con_using_array($po_id_array,0,'b.id');
						//$non_booking_id_cond=where_con_using_array($non_booking_id_array,0,'a.id');
						$order_arr=array();
						$sql_order="Select b.id as ID, a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.style_ref_no as STYLE_REF_NO,b.grouping as GROUPING, b.po_number as PO_NUMBER, b.po_quantity as PO_QUANTITY from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_id_cond";
						$result_sql_order=sql_select($sql_order);
						foreach($result_sql_order as $row)
						{
							$order_arr[$row['ID']]['job_no']=$row['JOB_NO'];
							$order_arr[$row['ID']]['buyer_name']=$row['BUYER_NAME'];
							$order_arr[$row['ID']]['style_ref_no']=$row['STYLE_REF_NO'];
							$order_arr[$row['ID']]['po_number']=$row['PO_NUMBER'];
							$order_arr[$row['ID']]['po_quantity']=$row['PO_QUANTITY'];
							$order_arr[$row['ID']]['grouping']=$row['GROUPING'];
						}
						unset($result_sql_order);
						
						
						
						
						   $batch_quantity_arr=return_library_array( "select a.id,sum(b.batch_qnty) as  batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($batch_id_array,0,'a.id')." group by a.id", "id", "batch_qnty");
					
	                    //============================= getting issue amount ================================
	                    $chem_dye_cost_array=array(); //and b.batch_id in($batchIds) 
	                    $chem_dye_cost_kg_array=array();

	                 //$chem_dye_cost_sql="SELECT a.mst_id, b.batch_id, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c, tmp_batch_or_iss d where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 and b.batch_id= d.batch_issue_no and d.userid=$user_id and d.block=1 and d.type=1 $working_company_cond_issue $lc_company_cond_issue2 group by a.mst_id, b.batch_id ";
						
					 $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1   $lc_company_cond_issue2 $working_company_cond_issue  group by a.mst_id, b.batch_id,a.item_category "; 

	                    //echo $chem_dye_cost_sql;die;

	                    $chem_dye_cost_result=sql_select($chem_dye_cost_sql);
	                    //var_dump($chem_dye_cost_result);die;
	                    
	                    foreach($chem_dye_cost_result as $row)
	                    {
	                        $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
	                        $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
	                        $total_batch_qnty=0;
	                        foreach($batch_id_arr as $batch_ids)
	                        {
	                            $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
	                            //echo $total_batch_qnty;die;
	                            //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
	                            
	                        }
	                        
	                        foreach($batch_id_arr as $batch_id)
	                        {
	                            $chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
	                            $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
	                            
	                        }
	                        
	                    }
	                    //var_dump($chem_dye_cost_kg_array);//die;

	                    // ============================ getting issue return amount =============================
	                    if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company in($working_company_id)";
	                    if(count($all_issue_id)>0)
	                    {
	                        
	                        foreach ($all_issue_id as $issue_id_key => $issue_id_val) 
	                        {
	                            $r_id3=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,1".")");
	                            if($r_id3) 
	                            {
	                                $r_id3=1;
	                            } 
	                            /*else 
	                            {
	                                echo "insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,1".")";
	                                oci_rollback($con);
	                                die;
	                            }*/
	                        }

	                        if($r_id3)
	                        {
	                            oci_commit($con);
	                        }
	                        else
	                        {
	                            oci_rollback($con);
	                            disconnect($con);
	                        }
	                        
	                        //and a.issue_id in($allIssueIds)
	                        //$sql_return_cost="SELECT p.batch_no as BATCH_ID, sum(a.cons_amount) as CONS_AMOUNT  from inv_issue_master p, inv_transaction a, inv_receive_master b, tmp_batch_or_iss c where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1 and a.issue_id=c.batch_issue_id and c.userid=$user_id and c.block=1 and c.type=2 group by p.batch_no"; 

	                        $sql_return_cost="SELECT p.batch_no as BATCH_ID,a.ITEM_CATEGORY, sum(a.cons_amount) as CONS_AMOUNT  from inv_issue_master p, inv_transaction a, inv_receive_master b, tmp_batch_or_iss c where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond  and b.is_deleted=0 and b.status_active=1 and a.issue_id=c.batch_issue_id and c.userid=$user_id and c.block=1 and c.type=2 group by p.batch_no,a.item_category"; 

	                        // echo $sql_return_cost;die();
	                        $cost_return_data_arr=array();
	                        $sql_return_res=sql_select($sql_return_cost);
	                        foreach ($sql_return_res as $row)
	                        {
	                            $cost_return_data_arr[$row['BATCH_ID']][$row['ITEM_CATEGORY']]+=$row['CONS_AMOUNT'];
	                            // $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
	                        }
	                    }
	                }
	                

	                // print_r($cost_return_data_arr);
	                $tot_rows=count($sql_result);
	                unset($sql_result);
	                $i=1;  $ul_percent=0; $tot_dye_qnty=0;$total_first_dye_cost=$total_first_chemical_cost=$total_redying_first_chemical_cost=0;
					$total_first_dye_cost=0;$total_redye_first_dye_cost=$total_tot_dye_chemical_cost=$total_redye_first_dye_cost=$total_tot_redying_first_chemical_cost=0;
					$total_first_dye_chemical_cost=0;//$total_first_dye_chemical_cost=0;$total_first_dye_chemical_cost=0;
	                foreach ( $self_batch_wise_array as $batch_id=>$row )
	                {
	                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$batch_against_id=$row[('batch_against')];
						$po_id=rtrim($row[('po_id')],',');
	                    $po_id_arr=array_unique(explode(",",$po_id)); 
						
						 
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="30"><? echo $i; ?></td>
	                        <td width="40"><p><? echo $machine_cap_array[$row[('machine_id')]]["machine_no"];//$row[('booking_no')]; ?></p></td>
	                        <td width="50" align="center"><p>
	                        <?
							echo $row[('batch_no')];
	                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;;$ref_no='';
	                        foreach($po_id_arr as $po_id)
	                        {
	                            if(!in_array($order_arr[$po_id]['job_no'],$job_chech))
	                            {
	                                $job_chech[]=$order_arr[$po_id]['job_no'];
	                                if($job_all=="") $job_all=$order_arr[$po_id]['job_no']; else $job_all .=", ".$order_arr[$po_id]['job_no'];
	                                if($buyer_all=="") $buyer_all=$buyer_library[$order_arr[$po_id]['buyer_name']]; else $buyer_all .=", ".$buyer_library[$order_arr[$po_id]['buyer_name']];
	                                if($style_all=="") $style_all=$order_arr[$po_id]['style_ref_no']; else $style_all .=", ".$order_arr[$po_id]['style_ref_no'];
	                            }
	                            if($po_all=="") $po_all=$order_arr[$po_id]['po_number']; else $po_all .=", ".$order_arr[$po_id]['po_number'];
								 if($ref_no=="") $ref_no=$order_arr[$po_id]['grouping']; else $ref_no .=", ".$order_arr[$po_id]['grouping'];
	                            $po_qnty +=$order_arr[$po_id]['po_quantity'];
	                        }
	                       // echo $job_all; 
							$sub_process_id=rtrim($batch_recipe_arr[$batch_id]['sub_process_id'],',');
							$sub_process_ids=array_unique(explode(",",$sub_process_id));
							//$seq_no=$batch_recipe_subpro_arr2[$batch_id][$row[csf("sub_process_id")]]['seq_no'];
							$sid_ratio_all='';$sub_liquor_ratio_all='';
							
							foreach($sub_process_ids as $sid)
							{
								$liquor_ratio=$batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
								if($liquor_ratio>0)
								{
								$seq_no=$batch_recipe_subpro_arr[$batch_id][$sid]['seq_no'];
								$sid_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.'&#013;';
								
								$sub_liquor_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.',';
								}
								
							}
							$sub_liquor_ratio_all=rtrim($sub_liquor_ratio_all,',');
							$sub_liquor_ratio_allArr=array_unique(explode(",",$sub_liquor_ratio_all));
							$sub_liquor_ratio=$sub_liquor_ratio_allArr[0];
								
	                        ?></p></td>
	                        <td width="70"><p><? echo $ref_no;//$buyer_all; echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']]; ?></p></td>
	                        <td width="70"><p><? if($buyer_all) echo $buyer_all; else echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']];//$style_all;  ?></p></td>
	                        <td width="70"><p><? echo $row[('booking_no')];//$po_all;?></p></td>
	                        <td width="70" align="right"><p><? echo $job_all;//number_format($po_qnty,0);  ?></p></td>
	                        <td width="70" align="center"><p><? echo $style_all; ?></p></td>
	                        <td width="70"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
	                        
	                        <td width="70"><p><? echo $row[('fabric_type')]; ?></p></td>
	                        <td width="70" align="right"><p><? echo number_format($row[('qnty')],2,'.',''); $total_dyeing_qnty+=$row[('qnty')]; ?></p></td>
	                        <td width="70" align="right"><p><? echo number_format($row[('trims_qty')],2,'.',''); $total_trims_qnty+=$row[('trims_qty')]; ?></p></td>
	                        <td width="50" align="center"><p><? $start_time=''; $start_time=$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']; echo  $start_time; ?></p></td>
	                         <td width="50" align="center"><p><? $end_time=''; $end_time=$row[('end_hours')].':'.$row[('end_minutes')]; echo $end_time; ?></p></td>
	                          <td width="50" align="right"><p><? $start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
	                        
	                        if($timeDiffin>0)
	                        {
	                             $time_used=convertMinutes2Hours($timeDiffin); 
	                             echo $time_used;
	                        }
	                        else
	                        {
	                            echo "Invalid";
	                        }
							
	                         ?></p></td>
	                         
	                        <td width="70" align="right" title="Batch ID=<? echo $cost_return_data_arr[$row[('batch_id')]][6].',BId='.$row[('batch_id')].',Tot dyes Cost='.$chem_dye_cost_array[$row[('batch_id')]][6];?>"><p><? 
							if($batch_against_id==1)
							{
							$first_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$row[('batch_id')]][6];
							$first_chemical_cost =($chem_dye_cost_array[$row[('batch_id')]][5]+$chem_dye_cost_array[$row[('batch_id')]][7]) - ($cost_return_data_arr[$row[('batch_id')]][6]+ $cost_return_data_arr[$row[('batch_id')]][7]);
							}
							else
							{
								$redying_first_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$row[('batch_id')]][6];
								$redying_first_chemical_cost =($chem_dye_cost_array[$row[('batch_id')]][5]+$chem_dye_cost_array[$row[('batch_id')]][7]) - ($cost_return_data_arr[$row[('batch_id')]][6]+ $cost_return_data_arr[$row[('batch_id')]][7]);
							}
							  echo number_format($first_chemical_cost,2,'.','');?></p></td>
	                            <td width="70" align="right" title="Total Dye Cost"><p><?  echo number_format($first_dye_cost,2,'.','');
								 $tot_first_dye_chemical_cost=$first_dye_cost+$first_chemical_cost;
								 $total_first_dye_chemical_cost+=$tot_first_dye_chemical_cost; 
								  $total_first_chemical_cost+=$first_chemical_cost; 
								   $total_first_dye_cost+=$first_dye_cost; 
								?></p></td>  
	                            
	                        <td width="70" align="right" title="Total Chemical Dye Cost"><p><? echo number_format($tot_first_dye_chemical_cost,2,'.','');
	                       ?></p></td>

	                                            
	                        <td width="70" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $tot_first_dye_chemi_cost_kg=$tot_first_dye_chemical_cost/$row[('qnty')];echo number_format($tot_first_dye_chemi_cost_kg,2,'.','');
	                         ?> </p></td>
	                        
	                           <td width="70" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><?  echo number_format($redying_first_chemical_cost,2,'.','');?></p></td> 
	                         <td width="70" align="right" title="Batch ID"><p><?  echo number_format($redying_first_dye_cost,2,'.','');?></p></td>
	                        <td width="70" align="right" title="Total chemical Dye Cost"><p><? 
							 $tot_redying_first_chemical_cost=$redying_first_chemical_cost+$redying_first_dye_cost;
								 $total_redye_first_dye_chemical_cost+=$tot_redying_first_chemical_cost; 
								  $total_redying_first_chemical_cost+=$redying_first_chemical_cost; 
								   $total_redye_first_dye_cost+=$redying_first_dye_cost; 
								   
							 echo number_format($tot_redying_first_chemical_cost,2,'.','');
	                        ?></p></td>

	                                             
	                        <td width="70" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $tot_redye_chemi_cost_kg=$tot_redying_first_chemical_cost/$row[('qnty')];echo number_format($tot_redye_chemi_cost_kg,2,'.','');
	                        $total_tot_redying_first_chemical_cost+=$tot_redying_first_chemical_cost;?> </p></td>

	                        <td width="70" align="center"><p><? 
							 $tot_dye_chemical_cost=$tot_redying_first_chemical_cost+$tot_first_dye_chemical_cost;
							 $total_tot_dye_chemical_cost+=$tot_dye_chemical_cost;
							echo number_format($tot_dye_chemical_cost,2,'.',''); ?></p></td>
	                        <td width="" align="center"><p><? echo number_format($tot_dye_chemical_cost/$row[('qnty')],2,'.',''); ?></p></td>
	                    </tr>
	                    <?
	                    $i++;
	                    //$total_chem_dye_cost+=$chem_dye_cost;
	                } 
	                ?>
	                </table>
	                <table width="<? echo $width;?>px" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
	                    <tfoot>
	                        <th width="30">&nbsp;</th>
	                        <th width="40">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        
	                        <th width="70"><strong>Total</strong></th>
	                        <th width="70"><? echo number_format($total_dyeing_qnty,2,'.',''); ?></th>
	                        <th width="70"><? echo number_format($total_trims_qnty,2,'.',''); ?></th>
	                        <th width="50">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        
	                        <th width="70" id="value_total_chemical_qnty"><? echo number_format($total_first_chemical_cost,2,'.',''); ?></th>
	                        <th width="70"><? echo number_format($total_first_dye_cost,2,'.',''); ?></th>
	                        
	                        <th width="70"><strong><? echo number_format($total_first_dye_chemical_cost,2,'.',''); ?></strong></th>
	                        <th width="70" id="" align="right"><? echo number_format($total_first_dye_chemical_cost/$total_dyeing_qnty,2,'.',''); ?></th>
	                        <th width="70"><? echo number_format($total_redying_first_chemical_cost,2,'.',''); ?></th>
	                        
	                        <th width="70" id="value_total_dye_cost"><? echo number_format($total_redye_first_dye_cost,2,'.',''); ?></th>
	                        <th width="70" id="value_total_chemi_cost"><? echo number_format($total_tot_redying_first_chemical_cost,2,'.',''); ?></th>
	                        <th width="70" title="Tot Dye Cost/Tot dying Qty" id=""><? echo number_format(($total_tot_redying_first_chemical_cost/$total_dyeing_qnty),2,'.',''); ?></th>
	                        <th width="70"  id="value_total_chem_cost" align="right"><? echo number_format($total_tot_dye_chemical_cost,2,'.',''); ?></th>
	                        <th width=""><? echo number_format(($total_tot_dye_chemical_cost/$total_dyeing_qnty),2,'.',''); ?></th>
	                    </tfoot>
	                </table>
	            </div> 
	            <?
	        }
			 
	        if($type==0 || $type==2)
	        {
	            $sub_width=1630;
				?>
	            <br />
	            <div>
	            <div align="left" style="background-color:#E1E1E1; color:#000; width:320px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>In-bound Subcontract Dyeing Production</i></u></strong></div>
	                <table width="<? echo $sub_width;?>" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
	                    <thead>
		                    <tr>
		                    	<th  colspan="15">&nbsp;</th>
		                        <th colspan="4">First Dying And Finishing Cost</th>
		                        <th colspan="4">Subsequent Dyeing and Finishing Cost</th>
		                        <th colspan="2">&nbsp;</th>
		                    </tr>
	                    	<tr>
		                        <th width="30">SL</th>
		                        <th width="40">M/C No</th>
		                        <th width="50">Batch No</th>
		                        <th width="70">Ref. No</th>
		                        <th width="70">Buyer</th>
		                        <th width="70">Booking No</th>
		                        <th width="70">Job no</th>
		                        <th width="70">Style</th>
		                        <th width="70">Color Name</th>
		                        <th width="70">Fabric Type</th>
		                        <th width="70">Dyeing Qty (Kg)</th>
		                        <th width="70">Trims weight</th>
		                        <th width="50">Loading Time</th>
		                        <th width="50">Unloading Time</th>
		                        <th width="50">Hour Used</th>
		                        
		                        <th width="70">Ttl Chem Cost (Tk)</th>
		                        <th width="70">Ttl Dyes Cost (Tk)</th>
		                        <th width="70">Ttl Chem + Dyes Cost (Tk)</th>
		                        <th width="70">Cost Per Kg (Tk)</th>
		                        
		                        <th width="70">Ttl Chem Cost (Tk)</th>
		                        <th width="70">Ttl Dyes Cost (Tk)</th>
		                        <th width="70">Ttl Chem + Dyes Cost (Tk)</th>
		                        <th width="70">Re-Dye Cost per kg (Tk)</th>
		                        <th width="70">Total Cost (Tk)</th>
		                        <th>Total Per Kg Cost (Tk)</th>
	                        </tr>
	                    </thead>
	                </table>
	            <div style="width:<? echo $sub_width+20;?>px; overflow-y:scroll; max-height:275px;" id="scroll_body1" >
	                <table width="<? echo $sub_width;?>" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body1">
	                <?  
	                $buyer_library_subcon=return_library_array( "select a.id,a.short_name from lib_buyer a, lib_buyer_party_type b where a.id=b.buyer_id and b.party_type in(2,3)", "id", "short_name");
	                //$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	                $machine_library=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no");
	                
	                
	                
	                $load_data_sub=array();
	                $load_time_data_sub=sql_select("select a.id, a.batch_id, a.batch_no, a.process_end_date, a.load_unload_id, a.end_hours, a.end_minutes from pro_fab_subprocess  a where a.load_unload_id=1 and a.entry_form=38 and status_active=1  and is_deleted=0 $working_company_cond $lc_company_cond");
	                
	                foreach($load_time_data_sub as $row_time)// for Loading time
	                {
	                    $load_data_sub[$row_time[csf('batch_id')]]['process_end_date']=$row_time[csf('process_end_date')];
	                    $load_data_sub[$row_time[csf('batch_id')]]['end_hours']=$row_time[csf('end_hours')];
	                    $load_data_sub[$row_time[csf('batch_id')]]['end_minutes']=$row_time[csf('end_minutes')];
	                }
	                unset($load_time_data_sub);
	                
	                
	               /* $machine_cap_array=array();
	                $mach_cap_sql=sql_select("select id, sum(prod_capacity) as prod_capacity from lib_machine_name where status_active=1 and is_deleted=0 group by id");
	                foreach($mach_cap_sql as $row)
	                {
	                    $machine_cap_array[$row[csf('id')]]=$row[csf('prod_capacity')];
	                }*/
	                //echo $db_type."jahid";die;
	            
	                if($db_type==0)
	                {
	                    $sql_sub_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company,a.fabric_type, a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_against,b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min,b.total_trims_weight, (c.prod_id), c.po_id as po_id, (c.batch_qnty) as qnty 
	                    FROM pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
	                    WHERE a.batch_id=b.id and a.entry_form=38 and a.load_unload_id=2 and b.id=c.mst_id   and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $floor_cond  $batch_cond $book_cond   $working_company_cond $lc_company_cond order by a.process_end_date";
	                }
	                else if($db_type==2)
	                {
	                    $sql_sub_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company, a.fabric_type,a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks,b.batch_against, b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min,b.total_trims_weight, (c.prod_id), c.po_id as po_id, (c.batch_qnty) as qnty 
	                    FROM pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
	                    WHERE a.batch_id=b.id  and b.id=c.mst_id and a.entry_form=38 and a.load_unload_id=2  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $batch_cond $book_cond $date_cond $floor_cond  $lc_company_cond $working_company_cond  order by a.process_end_date";
	                }
	                //echo $sql_sub_dtls;die;
	                $sql_sub_result=sql_select($sql_sub_dtls);
					 $tot_rows=count($sql_sub_result);
					$subBatch_id_arr=array();$sub_po_id_array=array();
					foreach( $sql_sub_result as $row)
					{
						$subBatch_id_arr[$row[csf('id')]]=$row[csf('id')];

						$batch_id_array[$row[csf('batch_id')]] = $row[csf('batch_id')];
						$booking_no_arr=explode("-",$row[csf('booking_no')]);
						$fb_booking_no=$booking_no_arr[1];

						$sub_batch_wise_array[$row[csf('batch_id')]]['id']= $row[csf('id')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['batch_against']= $row[csf('batch_against')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['batch_id']= $row[csf('batch_id')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['extention_no']= $row[csf('extention_no')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['dyeing_company']= $row[csf('dyeing_company')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['service_company']= $row[csf('service_company')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['production_date']= $row[csf('production_date')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['process_end_date']= $row[csf('process_end_date')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['fabric_type']= $fabric_type_for_dyeing[$row[csf('fabric_type')]]; 
						$sub_batch_wise_array[$row[csf('batch_id')]]['machine_id']= $row[csf('machine_id')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['end_hours']= $row[csf('end_hours')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['end_minutes']= $row[csf('end_minutes')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['remarks']= $row[csf('remarks')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['batch_no']= $row[csf('batch_no')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['color_id']= $row[csf('color_id')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['booking_no']= $row[csf('booking_no')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['color_range_id']= $row[csf('color_range_id')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['dur_req_hr']= $row[csf('dur_req_hr')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['dur_req_min']= $row[csf('dur_req_min')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['prod_id'].= $row[csf('prod_id')].',';
						$sub_batch_wise_array[$row[csf('batch_id')]]['po_id'].= $row[csf('po_id')].',';
						$sub_batch_wise_array[$row[csf('batch_id')]]['qnty']+= $row[csf('qnty')];
						$sub_batch_wise_array[$row[csf('batch_id')]]['trims_qty']+= $row[csf('total_trims_weight')];
						if($all_prod_id=="") $all_prod_id=$row[csf('prod_id')];else $all_prod_id.=",".$row[csf('prod_id')];
						$sub_po_id_array[$row[csf('po_id')]]= $row[csf('po_id')];
					}
					$subcon_po_cond=where_con_using_array($sub_po_id_array,0,'b.id');
					$job_arr=array();
	                $sql_job="Select b.id, a.subcon_job, a.party_id, b.cust_style_ref, b.order_no, b.order_quantity from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $subcon_po_cond";
	                $result_sql_job=sql_select($sql_job);
	                foreach($result_sql_job as $row)
	                {
	                    $job_arr[$row[csf('id')]]['job_no']=$row[csf('subcon_job')];
	                    $job_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
	                    $job_arr[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
	                    $job_arr[$row[csf('id')]]['order_no']=$row[csf('order_no')];
	                    $job_arr[$row[csf('id')]]['order_quantity']=$row[csf('order_quantity')];
	                }
					
					$batch_quantity_arr=return_library_array( "select a.id,sum(b.batch_qnty) as  batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($subBatch_id_arr,0,'a.id')." group by a.id", "id", "batch_qnty");
					 
					$chem_dye_cost_array=array();
					$chem_dye_cost_kg_array=array();

					$chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1   $working_company_cond_issue  $lc_company_cond_issue2 group by a.mst_id, b.batch_id,a.item_category ";  

                    $chem_dye_cost_result=sql_select($chem_dye_cost_sql);
                    foreach($chem_dye_cost_result as $row)
                    {
                        $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
                        $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
                        $total_batch_qnty=0;
                        foreach($batch_id_arr as $batch_ids)
                        {
                            $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
                            //echo $total_batch_qnty;die;
                            //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
                            
                        }
                        
                        foreach($batch_id_arr as $batch_id)
                        {
                            $chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
                            $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
                            
                        }
                    }
						
	                if($type==2)
	                {
	                   /* $batchIds = "'" . implode ( "', '", $sub_batch_id_array ) . "'";
	                    $baIds=chop($batchIds,','); $batch_cond_in="";
	                    $ba_ids=count(array_unique(explode(",",$batchIds)));
	                    if($db_type==2 && $ba_ids>1000)
	                    {
	                    $batch_cond_in=" and (";
	                    $baIdsArr=array_chunk(explode(",",$baIds),999);
	                    foreach($baIdsArr as $ids)
	                    {
	                    $ids=implode(",",$ids);
	                    $batch_cond_in.=" b.batch_id in($ids) or"; 
	                    }
	                    $batch_cond_in=chop($batch_cond_in,'or ');
	                    $batch_cond_in.=")";
	                    }
	                    else
	                    {
	                    $batch_cond_in=" and b.batch_id in($baIds)";
	                    }*/

	                    //============================= getting issue amount ================================
	                   

	                    // ============================ getting issue return amount =============================
	                    /*
	                        ############################  

	                        As this code has no impact on this block so it is commented 

	                        ###################

	                        if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company=$working_company_id";
	                        if(count($all_issue_id)>0)
	                        {
	                            $allIssueIds = implode(",", $all_issue_id);

	                            $issueIds=chop($allIssueIds,','); $issue_cond_in="";
	                            $iss_ids=count(array_unique(explode(",",$allIssueIds)));
	                            if($db_type==2 && $iss_ids>1000)
	                            {
	                            $issue_cond_in=" and (";
	                            $issueIdsArr=array_chunk(explode(",",$issueIds),999);
	                            foreach($issueIdsArr as $ids)
	                            {
	                            $ids=implode(",",$ids);
	                            $issue_cond_in.=" a.issue_id in($ids) or"; 
	                            }
	                            $issue_cond_in=chop($issue_cond_in,'or ');
	                            $issue_cond_in.=")";
	                            }
	                            else
	                            {
	                            $issue_cond_in=" and a.issue_id in($issueIds)";
	                            }
	                            //and a.issue_id in($allIssueIds)
	                            $sql_return_cost="SELECT p.batch_no as batch_id, sum(a.cons_amount) as cons_amount  from inv_issue_master p, inv_transaction a, inv_receive_master b where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1  $issue_cond_in  group by p.batch_no"; 

	                            $cost_return_data_arr=array();
	                            $sql_return_res=sql_select($sql_return_cost);
	                            foreach ($sql_return_res as $row)
	                            {
	                                $cost_return_data_arr[$row[csf('batch_id')]]=$row[csf('cons_amount')];
	                                // $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
	                            }
	                        }
	                    */
	                }
	                // Issue end
					$sub_sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.entry_form=38  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
					//die;
					$sub_result_data_recipe=sql_select($sub_sql_recipe);
					foreach ($sub_result_data_recipe as $row)
					{
						$sub_batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
						if($sub_tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
						{
						$sub_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
						}
						$sub_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
					}
					
	            
	                $k=1;  $ul_percent=0; $tot_dye_qnty=0;$total_dye_chem_cost=0;$total_dyeing_qnty=0;
					$total_first_dye_cost=$total_first_chemical_cost=$total_redying_first_chemical_cost=0;
					$total_first_dye_cost=0;$total_redye_first_dye_cost=$total_tot_dye_chemical_cost=$total_redye_first_dye_cost=$total_tot_redying_first_chemical_cost=0;
					
	                foreach ( $sub_batch_wise_array as $batch_id=>$row )
	                {
	                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$batch_against_id=$row[('batch_against')];$po_id=rtrim($row[('po_id')],',');
	                    $po_id_arr=array_unique(explode(",",$po_id)); 
						
						 
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trsub_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="trsub_<? echo $k; ?>">
	                        <td width="30"><? echo $k; ?></td>
	                        <td width="40"><p><? echo $machine_library[$row[('machine_id')]];//$row[('booking_no')]; ?></p></td>
	                        <td width="50" align="center"><p>
	                        <?
							 
						
							echo $row[('batch_no')];
	                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;;$ref_no='';
	                        foreach($po_id_arr as $po_id)
	                        {
	                            if(!in_array($job_arr[$po_id]['job_no'],$job_chech))
	                            {
	                                $job_chech[]=$job_arr[$po_id]['job_no'];
	                                if($job_all=="") $job_all=$job_arr[$po_id]['job_no']; else $job_all .=", ".$job_arr[$po_id]['job_no'];
	                                if($buyer_all=="") $buyer_all=$buyer_library[$job_arr[$po_id]['party_id']]; else $buyer_all .=", ".$buyer_library[$job_arr[$po_id]['party_id']];
	                                if($style_all=="") $style_all=$job_arr[$po_id]['style_ref_no']; else $style_all .=", ".$job_arr[$po_id]['style_ref_no'];
	                            }
	                            if($po_all=="") $po_all=$job_arr[$po_id]['order_no']; else $po_all .=", ".$job_arr[$po_id]['order_no'];
								// if($ref_no=="") $ref_no=$job_arr[$po_id]['grouping']; else $ref_no .=", ".$job_arr[$po_id]['grouping'];
	                            $po_qnty +=$job_arr[$po_id]['order_quantity'];
	                        }
	                       	// echo $job_all; 
							
								
	                        ?></p></td>
	                        <td width="70"><p><? echo $ref_no;//$buyer_all; echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']]; ?></p></td>
	                        <td width="70"><p><? if($buyer_all) echo $buyer_all; else echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']];//$style_all;  ?></p></td>
	                        <td width="70"><p><? echo $row[('booking_no')];//$po_all;?></p></td>
	                        <td width="70" align="right"><p><? echo $job_all;//number_format($po_qnty,0);  ?></p></td>
	                        <td width="70" align="center"><p><? echo $style_all; ?></p></td>
	                        <td width="70"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
	                        
	                        <td width="70"><p><? echo $row[('fabric_type')]; ?></p></td>
	                        <td width="70" align="right"><p><? echo number_format($row[('qnty')],2,'.',''); $total_dyeing_qnty+=$row[('qnty')]; ?></p></td>
	                        <td width="70" align="right"><p><? echo number_format($row[('trims_qty')],2,'.',''); $total_trims_qnty+=$row[('trims_qty')]; ?></p></td>
	                        <td width="50" align="center"><p><? $start_time=''; $start_time=$load_data_sub[$row[('id')]]['end_hours'].':'.$load_data_sub[$row[('id')]]['end_minutes']; echo  $start_time; ?></p></td>
	                        <td width="50" align="center"><p><? $end_time=''; $end_time=$row[('end_hours')].':'.$row[('end_minutes')]; echo $end_time; ?></p></td>
	                        <td width="50" align="right"><p><? $start_time=strtotime($load_data_sub[$row[('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
	                        
	                        if($timeDiffin>0)
	                        {
	                            $time_used=convertMinutes2Hours($timeDiffin); 
	                            echo $time_used;
	                        }
	                        else
	                        {
	                            echo "Invalid";
	                        }
							
	                        ?></p></td>
	                         
	                        <td width="70" align="right" title="Batch ID=<? echo $cost_return_data_arr[$row[('batch_id')]][6].',BId='.$row[('batch_id')].',Tot dyes Cost='.$chem_dye_cost_array[$row[('batch_id')]][6];?>"><p><? 
							if($batch_against_id==1)
							{
							$first_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$row[('batch_id')]][6];
							$first_chemical_cost =($chem_dye_cost_array[$row[('batch_id')]][5]+$chem_dye_cost_array[$row[('batch_id')]][7]) - ($cost_return_data_arr[$row[('batch_id')]][6]+ $cost_return_data_arr[$row[('batch_id')]][7]);
							}
							else
							{
								$redying_first_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$row[('batch_id')]][6];
								$redying_first_chemical_cost =($chem_dye_cost_array[$row[('batch_id')]][5]+$chem_dye_cost_array[$row[('batch_id')]][7]) - ($cost_return_data_arr[$row[('batch_id')]][6]+ $cost_return_data_arr[$row[('batch_id')]][7]);
							}
							echo number_format($first_chemical_cost,2,'.','');?></p></td>
							<td width="70" align="right" title="Total Dye Cost"><p><?  echo number_format($first_dye_cost,2,'.','');
							$tot_first_dye_chemical_cost=$first_dye_cost+$first_chemical_cost;
							$total_first_dye_chemical_cost+=$tot_first_dye_chemical_cost; 
							$total_first_chemical_cost+=$first_chemical_cost; 
							$total_first_dye_cost+=$first_dye_cost; 
							?></p></td>  
	                            
	                        <td width="70" align="right" title="Total Chemical Dye Cost"><p><? echo number_format($tot_first_dye_chemical_cost,2,'.','');
	                       	?></p></td>

	                                            
							<td width="70" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $tot_first_dye_chemi_cost_kg=$tot_first_dye_chemical_cost/$row[('qnty')];echo number_format($tot_first_dye_chemi_cost_kg,2,'.','');
							?> </p></td>

							<td width="70" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><?  echo number_format($redying_first_chemical_cost,2,'.','');?></p></td> 
							<td width="70" align="right" title="Batch ID"><p><?  echo number_format($redying_first_dye_cost,2,'.','');?></p></td>
							<td width="70" align="right" title="Total chemical Dye Cost"><p><? 
							$tot_redying_first_chemical_cost=$redying_first_chemical_cost+$redying_first_dye_cost;
							$total_redye_first_dye_chemical_cost+=$tot_redying_first_chemical_cost; 
							$total_redying_first_chemical_cost+=$redying_first_chemical_cost; 
							$total_redye_first_dye_cost+=$redying_first_dye_cost; 

							echo number_format($tot_redying_first_chemical_cost,2,'.','');
							?></p></td>
							<td width="70" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $tot_redye_chemi_cost_kg=$tot_redying_first_chemical_cost/$row[('qnty')];echo number_format($tot_redye_chemi_cost_kg,2,'.','');
							$total_tot_redying_first_chemical_cost+=$tot_redying_first_chemical_cost;?> </p></td>

							<td width="70" align="center"><p><? 
							$tot_dye_chemical_cost=$tot_redying_first_chemical_cost+$tot_first_dye_chemical_cost;
							$total_tot_dye_chemical_cost+=$tot_dye_chemical_cost;
							echo number_format($tot_dye_chemical_cost,2,'.',''); ?></p></td>
							<td width="" align="center"><p><? echo number_format($tot_dye_chemical_cost/$row[('qnty')],2,'.',''); ?></p></td>
	                    </tr>
	                    <?
	                    $k++;
	                } 
	                ?>
	                </table>
	                <table width="<? echo $sub_width;?>px" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
	                    <tfoot>
	                        <th width="30">&nbsp;</th>
	                        <th width="40">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        
	                        <th width="70"><strong>Total</strong></th>
	                        <th width="70"><? echo number_format($total_dyeing_qnty,2,'.',''); ?></th>
	                        <th width="70"><? echo number_format($total_trims_qnty,2,'.',''); ?></th>
	                        <th width="50">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        
	                        
	                        <th width="70" id="value_total_chemical_qnty"><? echo number_format($total_first_chemical_cost,2,'.',''); ?></th>
	                        <th width="70"><? echo number_format($total_first_dye_cost,2,'.',''); ?></th>
	                        
	                        <th width="70"><strong><? echo number_format($total_first_dye_chemical_cost,2,'.',''); ?></strong></th>
	                        <th width="70" id="" align="right"><? echo number_format($total_first_dye_chemical_cost/$total_dyeing_qnty,2,'.',''); ?></th>
	                        <th width="70"><? echo number_format($total_redying_first_chemical_cost,2,'.',''); ?></th>
	                        
	                        <th width="70" id="value_total_dye_cost"><? echo number_format($total_redye_first_dye_cost,2,'.',''); ?></th>
	                        <th width="70" id="value_total_chemi_cost"><? echo number_format($total_tot_redying_first_chemical_cost,2,'.',''); ?></th>
	                        <th width="70" title="Tot Dye Cost/Tot dying Qty" id=""><? echo number_format(($total_tot_redying_first_chemical_cost/$total_dyeing_qnty),2,'.',''); ?></th>
	                        <th width="70"  id="value_total_chem_cost" align="right"><? echo number_format($total_tot_dye_chemical_cost,2,'.',''); ?></th>
	                        <th width=""><? echo number_format(($total_tot_dye_chemical_cost/$total_dyeing_qnty),2,'.',''); ?></th>
	                    </tfoot>
	                </table>
	            </div>
	            </div>     
	            <?
	        }
	        if($type==0 || $type==3)
	        {
	            $sam_width=1530;
	            ?>
	            <br/>
	            <div align="left" style="background-color:#E1E1E1; color:#000; width:250px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Sample Dyeing Production</i></u></strong></div>
	                <table width="<? echo $sam_width;?>" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
	                    <thead>
	                       <tr>
	                    	<th  colspan="14">&nbsp;</th>
	                        <th colspan="4">First Dying And Finishing Cost</th>
	                        <th colspan="4">Subsequent Dyeing and Finishing Cost</th>
	                        <th colspan="2">&nbsp;</th>
	                    </tr>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="40">M/C No</th>
	                        <th width="50">Batch No</th>
	                        <th width="70">Ref. No</th>
	                        <th width="70">Buyer</th>
	                        <th width="70">Booking No</th>
	                        <th width="70">Job no</th>
	                        <th width="70">Style</th>
	                        <th width="70">Color Name</th>
	                        <th width="70">Fabric Type</th>
	                        <th width="70">Dyeing Qty (Kg)</th>
	                        <th width="50">Loading Time</th>
	                        <th width="50">Unloading Time</th>
	                        <th width="50">Hour Used</th>
	                        
	                        <th width="70">Ttl Chem Cost (Tk)</th>
	                        <th width="70">Ttl Dyes Cost (Tk)</th>
	                        <th width="70">Ttl Chem + Dyes Cost (Tk)</th>
	                        <th width="70">Cost Per Kg (Tk)</th>
	                        
	                        <th width="70">Ttl Chem Cost (Tk)</th>
	                        <th width="70">Ttl Dyes Cost (Tk)</th>
	                        <th width="70">Ttl Chem + Dyes Cost (Tk)</th>
	                        <th width="70">Re-Dye Cost per kg (Tk)</th>
	                        <th width="70">Total Cost (Tk)</th>
	                        <th>Total Per Kg Cost (Tk)</th>
	                        </tr>
	                    </thead>
	                </table>
	            <div style="width:<? echo $sam_width+20;?>px; overflow-y:scroll; max-height:275px;" id="scroll_body2" >
	                <table width="<? echo $sam_width;?>" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body3">
	                <?
	                
	                if($db_type==0)
	                {
	                    $sql_dtls="SELECT b.id,b.extention_no, a.batch_id,a.fabric_type, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_against,b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, (c.prod_id) as prod_id, c.po_id as po_id, (c.batch_qnty) as qnty
	                from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
	                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.batch_against n(3,2) and b.is_sales!=1 $floor_cond $working_company_cond $lc_company_cond  $date_cond  $batch_cond $book_cond 
	               order by a.process_end_date";
	                }
	                else if($db_type==2)
	                {
	                    $sql_dtls="SELECT b.id,b.extention_no,a.batch_id, a.fabric_type,a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.batch_against,b.color_id, b.booking_without_order,b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, (c.prod_id) as prod_id, c.po_id as po_id, (c.batch_qnty) as qnty
	                from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c 
	                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against in(3,2) and b.is_sales!=1 $floor_cond $working_company_cond $lc_company_cond  $date_cond $batch_cond $book_cond order by  a.process_end_date";
	                }
	                //echo $sql_dtls;
	                $sql_result=sql_select($sql_dtls);
	                foreach ( $sql_result as $row )
	                {
	                    $sam_batch_id_array[$row[csf('batch_id')]] = $row[csf('batch_id')];
	                    $samp_booking_no_arr=explode("-",$row[csf('booking_no')]);
	                    $sm_booking_no=$samp_booking_no_arr[1];
	                    if($sm_booking_no=='SM' || $sm_booking_no=='SMN') 
	                    {
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['id']=$row[csf('id')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['extention_no']=$row[csf('extention_no')];
							$sample_batch_wise_arr[$row[csf('batch_id')]]['batch_against']=$row[csf('batch_against')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['dyeing_company']=$row[csf('dyeing_company')];
							$sample_batch_wise_arr[$row[csf('batch_id')]]['fabric_type']= $fabric_type_for_dyeing[$row[csf('fabric_type')]]; 
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['service_company']=$row[csf('service_company')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['production_date']=$row[csf('production_date')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['process_end_date']=$row[csf('process_end_date')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['end_hours']=$row[csf('end_hours')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['end_minutes']=$row[csf('end_minutes')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['booking_without_order']=$row[csf('booking_without_order')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['dur_req_hr']=$row[csf('dur_req_hr')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['dur_req_min']=$row[csf('dur_req_min')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['prod_id']=$row[csf('prod_id')];
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['po_id'].=$row[csf('po_id')].',';
	                        $sample_batch_wise_arr[$row[csf('batch_id')]]['qnty']+=$row[csf('qnty')];
							$sam_po_id_array[$row[csf('po_id')]]=$row[csf('po_id')];
	                    }
	                }
					$sam_po_id_cond=where_con_using_array($sam_po_id_array,0,'b.id');
						$order_arr=array();
						$sql_order="Select b.id as ID, a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.style_ref_no as STYLE_REF_NO,b.grouping as GROUPING, b.po_number as PO_NUMBER, b.po_quantity as PO_QUANTITY from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $sam_po_id_cond";
						$result_sql_order=sql_select($sql_order);
						foreach($result_sql_order as $row)
						{
							$order_arr[$row['ID']]['job_no']=$row['JOB_NO'];
							$order_arr[$row['ID']]['buyer_name']=$row['BUYER_NAME'];
							$order_arr[$row['ID']]['style_ref_no']=$row['STYLE_REF_NO'];
							$order_arr[$row['ID']]['po_number']=$row['PO_NUMBER'];
							$order_arr[$row['ID']]['po_quantity']=$row['PO_QUANTITY'];
							$order_arr[$row['ID']]['grouping']=$row['GROUPING'];
						}
						unset($result_sql_order);
						
					/*$samp_sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.entry_form=35  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
					//die;
					$samp_result_data_recipe=sql_select($samp_sql_recipe);
					foreach ($samp_result_data_recipe as $row)
					{
						$samp_batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
						if($samp_tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
						{
						$samp_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
						}
						$samp_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
					}*/
					$batch_quantity_arr=return_library_array( "select a.id,sum(b.batch_qnty) as  batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($sam_batch_id_array,0,'a.id')." group by a.id", "id", "batch_qnty");
	                if($type==3)
	                {
	                    $batchIds = "'" . implode ( "', '", $sam_batch_id_array ) . "'";
	                    $baIds=chop($batchIds,','); $other_batch_cond_in="";
	                    $ba_ids=count(array_unique(explode(",",$batchIds)));
	                    if($db_type==2 && $ba_ids>1000)
	                    {
	                    $other_batch_cond_in=" and (";
	                    $baIdsArr=array_chunk(explode(",",$baIds),999);
	                    foreach($baIdsArr as $ids)
	                    {
	                    $ids=implode(",",$ids);
	                    $other_batch_cond_in.=" b.batch_id in($ids) or"; 
	                    }
	                    $other_batch_cond_in=chop($other_batch_cond_in,'or ');
	                    $other_batch_cond_in.=")";
	                    }
	                    else
	                    {
	                    $other_batch_cond_in=" and b.batch_id in($baIds)";
	                    }

	                    //============================= getting issue amount ================================
	                    $chem_dye_cost_array=array(); //and b.batch_id in($batchIds) 
	                    $chem_dye_cost_kg_array=array();
	                  //  $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b where a.id=b.trans_id AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 $batch_cond_in group by a.mst_id, b.batch_id "; 

	                    $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 $working_company_cond_issue $lc_company_cond_issue2 group by a.mst_id, b.batch_id,a.item_category ";

	                    //echo $chem_dye_cost_sql;//die;
	                    $chem_dye_cost_result=sql_select($chem_dye_cost_sql);
	                    //var_dump($chem_dye_cost_result);die;
	                    
	                    foreach($chem_dye_cost_result as $row)
	                    {
	                        $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
	                        $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
	                        $total_batch_qnty=0;
	                        foreach($batch_id_arr as $batch_ids)
	                        {
	                            $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
	                            //echo $total_batch_qnty;die;
	                            //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
	                            
	                        }
	                        
	                        foreach($batch_id_arr as $batch_id)
	                        {
	                            $chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
	                            $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
	                            
	                        }
	                        
	                    }
	                    
	                    /*
	                        ################################

	                            These code has no effect on this block so it is commented

	                        ################################

	                        if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company=$working_company_id";
	                        // if($cbo_company==0 || $cbo_company=='') $lc_company_cond="";else $lc_company_cond="and a.company_id in($cbo_company)";

	                        if(count($all_issue_id)>0)
	                        {
	                            $allIssueIds = implode(",", $all_issue_id);

	                            $issueIds=chop($allIssueIds,','); $issue_cond_in="";
	                            $iss_ids=count(array_unique(explode(",",$allIssueIds)));
	                            if($db_type==2 && $iss_ids>1000)
	                            {
	                            $issue_cond_in=" and (";
	                            $issueIdsArr=array_chunk(explode(",",$issueIds),999);
	                            foreach($issueIdsArr as $ids)
	                            {
	                            $ids=implode(",",$ids);
	                            $issue_cond_in.=" a.issue_id in($ids) or"; 
	                            }
	                            $issue_cond_in=chop($issue_cond_in,'or ');
	                            $issue_cond_in.=")";
	                            }
	                            else
	                            {
	                            $issue_cond_in=" and a.issue_id in($issueIds)";
	                            }
	                            //and a.issue_id in($allIssueIds)
	                            $sql_return_cost="SELECT p.batch_no as batch_id, sum(a.cons_amount) as cons_amount  from inv_issue_master p, inv_transaction a, inv_receive_master b where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1  $issue_cond_in  group by p.batch_no"; 
	                            // echo $sql_return_cost;
	                            $cost_return_data_arr=array();
	                            $sql_return_res=sql_select($sql_return_cost);
	                            foreach ($sql_return_res as $row)
	                            {
	                                $cost_return_data_arr[$row[csf('batch_id')]]=$row[csf('cons_amount')];
	                                // $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
	                            }
	                        }
	                    */
	                }
	                
	                $tot_rows=count($sql_result);
	                $i=1;  $ul_percent=0; $tot_dye_qnty=0;$total_dye_chem_cost=$total_samp_dyeing_qnty=0;$total_dyeing_qnty=0;
					$total_first_dye_cost=$total_first_chemical_cost=$total_redying_first_chemical_cost=0;$total_first_dye_chemical_cost=0;
					$total_first_dye_cost=0;$total_redye_first_dye_cost=$total_tot_dye_chemical_cost=$total_redye_first_dye_cost=$total_tot_redying_first_chemical_cost=0;
	                foreach ( $sample_batch_wise_arr as $batch_id=>$row )
	                {
	                     if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$batch_against_id=$row[('batch_against')];$po_id=rtrim($row[('po_id')],',');
	                    $po_id_arr=array_unique(explode(",",$po_id)); 
						
						 
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trsam_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trsam_<? echo $i; ?>">
	                        <td width="30"><? echo $i; ?></td>
	                        <td width="40"><p><? echo $machine_cap_array[$row[('machine_id')]]["machine_no"];//$row[('booking_no')]; ?></p></td>
	                        <td width="50" align="center"><p>
	                        <?
							echo $row[('batch_no')];
	                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;;$ref_no='';
	                        foreach($po_id_arr as $po_id)
	                        {
	                            if(!in_array($order_arr[$po_id]['job_no'],$job_chech))
	                            {
	                                $job_chech[]=$order_arr[$po_id]['job_no'];
	                                if($job_all=="") $job_all=$order_arr[$po_id]['job_no']; else $job_all .=", ".$order_arr[$po_id]['job_no'];
	                                if($buyer_all=="") $buyer_all=$buyer_library[$order_arr[$po_id]['buyer_name']]; else $buyer_all .=", ".$buyer_library[$order_arr[$po_id]['buyer_name']];
	                                if($style_all=="") $style_all=$order_arr[$po_id]['style_ref_no']; else $style_all .=", ".$order_arr[$po_id]['style_ref_no'];
	                            }
	                            if($po_all=="") $po_all=$order_arr[$po_id]['po_number']; else $po_all .=", ".$order_arr[$po_id]['po_number'];
								 if($ref_no=="") $ref_no=$order_arr[$po_id]['grouping']; else $ref_no .=", ".$order_arr[$po_id]['grouping'];
	                            $po_qnty +=$order_arr[$po_id]['po_quantity'];
	                        }
	                       
								
	                        ?></p></td>
	                        <td width="70"><p><? echo $ref_no;//$buyer_all; echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']]; ?></p></td>
	                        <td width="70"><p><? if($buyer_all) echo $buyer_all; else echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']];//$style_all;  ?></p></td>
	                        <td width="70"><p><? echo $row[('booking_no')];//$po_all;?></p></td>
	                        <td width="70" align="right"><p><? echo $job_all;//number_format($po_qnty,0);  ?></p></td>
	                        <td width="70" align="center"><p><? echo $style_all; ?></p></td>
	                        <td width="70"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
	                        
	                        <td width="70"><p><? echo $row[('fabric_type')]; ?></p></td>
	                        <td width="70" align="right"><p><? echo number_format($row[('qnty')],2,'.',''); $total_dyeing_qnty+=$row[('qnty')]; ?></p></td>
	                        <td width="50" align="center"><p><? $start_time=''; $start_time=$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']; echo  $start_time; ?></p></td>
	                         <td width="50" align="center"><p><? $end_time=''; $end_time=$row[('end_hours')].':'.$row[('end_minutes')]; echo $end_time; ?></p></td>
	                          <td width="50" align="right"><p><? $start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
	                        
	                        if($timeDiffin>0)
	                        {
	                             $time_used=convertMinutes2Hours($timeDiffin); 
	                             echo $time_used;
	                        }
	                        else
	                        {
	                            echo "Invalid";
	                        }
							
	                         ?></p></td>
	                         
	                        <td width="70" align="right" title="Batch ID=<? echo $cost_return_data_arr[$row[('batch_id')]][6].',BId='.$row[('batch_id')].',Tot dyes Cost='.$chem_dye_cost_array[$row[('batch_id')]][6];?>"><p><? 
							if($batch_against_id==1)
							{
							$first_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$row[('batch_id')]][6];
							$first_chemical_cost =($chem_dye_cost_array[$row[('batch_id')]][5]+$chem_dye_cost_array[$row[('batch_id')]][7]) - ($cost_return_data_arr[$row[('batch_id')]][6]+ $cost_return_data_arr[$row[('batch_id')]][7]);
							}
							else
							{
								$redying_first_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$row[('batch_id')]][6];
								$redying_first_chemical_cost =($chem_dye_cost_array[$row[('batch_id')]][5]+$chem_dye_cost_array[$row[('batch_id')]][7]) - ($cost_return_data_arr[$row[('batch_id')]][6]+ $cost_return_data_arr[$row[('batch_id')]][7]);
							}
							  echo number_format($first_chemical_cost,2,'.','');?></p></td>
	                            <td width="70" align="right" title="Total Dye Cost"><p><?  echo number_format($first_dye_cost,2,'.','');
								 $tot_first_dye_chemical_cost=$first_dye_cost+$first_chemical_cost;
								 $total_first_dye_chemical_cost+=$tot_first_dye_chemical_cost; 
								  $total_first_chemical_cost+=$first_chemical_cost; 
								   $total_first_dye_cost+=$first_dye_cost; 
								?></p></td>  
	                            
	                        <td width="70" align="right" title="Total Chemical Dye Cost"><p><? echo number_format($tot_first_dye_chemical_cost,2,'.','');
	                       ?></p></td>

	                                            
	                        <td width="70" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $tot_first_dye_chemi_cost_kg=$tot_first_dye_chemical_cost/$row[('qnty')];echo number_format($tot_first_dye_chemi_cost_kg,2,'.','');
	                         ?> </p></td>
	                        
	                           <td width="70" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><?  echo number_format($redying_first_chemical_cost,2,'.','');?></p></td> 
	                         <td width="70" align="right" title="Batch ID"><p><?  echo number_format($redying_first_dye_cost,2,'.','');?></p></td>
	                        <td width="70" align="right" title="Total chemical Dye Cost"><p><? 
							 $tot_redying_first_chemical_cost=$redying_first_chemical_cost+$redying_first_dye_cost;
								 $total_redye_first_dye_chemical_cost+=$tot_redying_first_chemical_cost; 
								  $total_redying_first_chemical_cost+=$redying_first_chemical_cost; 
								   $total_redye_first_dye_cost+=$redying_first_dye_cost; 
								   
							 echo number_format($tot_redying_first_chemical_cost,2,'.','');
	                        ?></p></td>

	                                             
	                        <td width="70" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $tot_redye_chemi_cost_kg=$tot_redying_first_chemical_cost/$row[('qnty')];echo number_format($tot_redye_chemi_cost_kg,2,'.','');
	                        $total_tot_redying_first_chemical_cost+=$tot_redying_first_chemical_cost;?> </p></td>

	                        <td width="70" align="center"><p><? 
							 $tot_dye_chemical_cost=$tot_redying_first_chemical_cost+$tot_first_dye_chemical_cost;
							 $total_tot_dye_chemical_cost+=$tot_dye_chemical_cost;
							echo number_format($tot_dye_chemical_cost,2,'.',''); ?></p></td>
	                        <td width="" align="center"><p><? echo number_format($tot_dye_chemical_cost/$row[('qnty')],2,'.',''); ?></p></td>
	                    </tr>
	                    <?
	                    $i++;
	                } 
	                ?>
	                </table>
	                <table width="<? echo $sam_width+20;?>px" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
	                    <tfoot>
	                         <th width="30">&nbsp;</th>
	                        <th width="40">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70"><strong>Total</strong></th>
	                        <th width="70"><? echo number_format($total_dyeing_qnty,2,'.',''); ?></th>
	                        <th width="50">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        <th width="70" id="value_total_chemical_qnty"><? echo number_format($total_first_chemical_cost,2,'.',''); ?></th>
	                        <th width="70"><? echo number_format($total_first_dye_cost,2,'.',''); ?></th>
	                        <th width="70"><strong><? echo number_format($total_first_dye_chemical_cost,2,'.',''); ?></strong></th>
	                        <th width="70" id="" align="right"><? echo number_format($total_first_dye_chemical_cost/$total_dyeing_qnty,2,'.',''); ?></th>
	                        <th width="70"><? echo number_format($total_redying_first_chemical_cost,2,'.',''); ?></th>
	                        <th width="70" id="value_total_dye_cost"><? echo number_format($total_redye_first_dye_cost,2,'.',''); ?></th>
	                        <th width="70" id="value_total_chemi_cost"><? echo number_format($total_tot_redying_first_chemical_cost,2,'.',''); ?></th>
	                        <th width="70" title="Tot Dye Cost/Tot dying Qty" id=""><? echo number_format(($total_tot_redying_first_chemical_cost/$total_dyeing_qnty),2,'.',''); ?></th>
	                        <th width="70"  id="value_total_chem_cost" align="right"><? echo number_format($total_tot_dye_chemical_cost,2,'.',''); ?></th>
	                        <th width=""><? echo number_format(($total_tot_dye_chemical_cost/$total_dyeing_qnty),2,'.',''); ?></th>
	                     
	                        
	                    </tfoot>
	                </table>
	            </div> 
	            <?
	        }
	        if($type==0 || $type==4) //Gmts Dyeing and Without Booking
	        {
	            
	            $without_width=1530;
				?>
	            <div align="left" style="background-color:#E1E1E1; color:#000; width:250px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Others Dyeing Production</i></u></strong></div>
	                <table width="<? echo $without_width?>" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
	                     <thead>
	                    <tr>
	                    	<th  colspan="14">&nbsp;</th>
	                        <th colspan="4">First Dying And Finishing Cost</th>
	                        <th colspan="4">Subsequent Dyeing and Finishing Cost</th>
	                        <th colspan="2">&nbsp;</th>
	                    </tr>
	                    <tr>
	                        <th width="30">SL</th>
	                        <th width="40">M/C No</th>
	                        <th width="50">Batch No</th>
	                        <th width="70">Ref. No</th>
	                        <th width="70">Buyer</th>
	                        <th width="70">Booking No</th>
	                        <th width="70">Job no</th>
	                        <th width="70">Style</th>
	                        <th width="70">Color Name</th>
	                        <th width="70">Fabric Type</th>
	                        <th width="70">Dyeing Qty (Kg)</th>
	                        <th width="50">Loading Time</th>
	                        <th width="50">Unloading Time</th>
	                        <th width="50">Hour Used</th>
	                        
	                        <th width="70">Ttl Chem Cost (Tk)</th>
	                        <th width="70">Ttl Dyes Cost (Tk)</th>
	                        <th width="70">Ttl Chem + Dyes Cost (Tk)</th>
	                        <th width="70">Cost Per Kg (Tk)</th>
	                        
	                        <th width="70">Ttl Chem Cost (Tk)</th>
	                        <th width="70">Ttl Dyes Cost (Tk)</th>
	                        <th width="70">Ttl Chem + Dyes Cost (Tk)</th>
	                        <th width="70">Re-Dye Cost per kg (Tk)</th>
	                        <th width="70">Total Cost (Tk)</th>
	                        <th>Total Per Kg Cost (Tk)</th>
	                        </tr>
	                    </thead>
	                </table>
	            <div style="width:<? echo $without_width+20;?>px; overflow-y:scroll; max-height:275px;" id="scroll_body3" >
	                <table width="<? echo $without_width;?>" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body4">
	                <?  
	                
	                
	                if($db_type==0)
	                {
	                    $sql_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes,a.fabric_type, a.remarks,b.batch_against, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, (c.prod_id) as prod_id, (c.po_id) as po_id, (c.batch_qnty) as qnty
	                from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
	                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.batch_against in(5,7) and b.is_sales!=1  $floor_cond $date_cond $working_company_cond $lc_company_cond    $batch_cond $book_cond 
	                 order by a.process_end_date";
	                }
	                else if($db_type==2)
	                {
	                    $sql_dtls="SELECT b.id,b.extention_no,a.batch_id,a.fabric_type, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks,b.batch_against, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, (c.prod_id) as prod_id, c.po_id as po_id, (c.batch_qnty) as qnty
	                from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c 
	                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against  in(5,7) and b.is_sales!=1  $floor_cond $working_company_cond $lc_company_cond $date_cond $batch_cond $book_cond 
	                order by a.process_end_date";
	                }
	                //echo $sql_dtls; die;
	                $sql_result=sql_select($sql_dtls);

	                if(!empty($sql_result))
	                {
	                    // ================== getting batch id =========================
	                    $batch_id_array = array();
	                    foreach ( $sql_result as $row )
	                    {
	                        $batch_id_array[$row[csf('batch_id')]] = $row[csf('batch_id')];
	                        $booking_no_arr=explode("-",$row[csf('booking_no')]);
	                        $fb_booking_no=$booking_no_arr[1];
	                        //if($fb_booking_no=='Fb') //echo $fb_booking_no.'A';else echo $fb_booking_no.'B';
	                        //{
	                        $other_batch_wise_array[$row[csf('batch_id')]]['id']= $row[csf('id')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['batch_id']= $row[csf('batch_id')];
							$other_batch_wise_array[$row[csf('batch_id')]]['batch_against']= $row[csf('batch_against')];
							 $other_batch_wise_array[$row[csf('batch_id')]]['fabric_type']= $fabric_type_for_dyeing[$row[csf('fabric_type')]]; 
	                        $other_batch_wise_array[$row[csf('batch_id')]]['extention_no']= $row[csf('extention_no')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['dyeing_company']= $row[csf('dyeing_company')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['service_company']= $row[csf('service_company')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['production_date']= $row[csf('production_date')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['process_end_date']= $row[csf('process_end_date')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['machine_id']= $row[csf('machine_id')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['end_hours']= $row[csf('end_hours')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['end_minutes']= $row[csf('end_minutes')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['remarks']= $row[csf('remarks')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['batch_no']= $row[csf('batch_no')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['color_id']= $row[csf('color_id')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['booking_no']= $row[csf('booking_no')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['color_range_id']= $row[csf('color_range_id')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['dur_req_hr']= $row[csf('dur_req_hr')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['dur_req_min']= $row[csf('dur_req_min')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['prod_id']= $row[csf('prod_id')];
	                        $other_batch_wise_array[$row[csf('batch_id')]]['po_id'].= $row[csf('po_id')].',';
	                        $other_batch_wise_array[$row[csf('batch_id')]]['qnty']+= $row[csf('qnty')];
							$without_po_id_array[$row[csf('po_id')]]=$row[csf('po_id')];
	                        //}
	                    }
						$w_po_id_cond=where_con_using_array($without_po_id_array,0,'b.id');
						$order_arr=array();
						$sql_order="Select b.id as ID, a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.style_ref_no as STYLE_REF_NO,b.grouping as GROUPING, b.po_number as PO_NUMBER, b.po_quantity as PO_QUANTITY from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $w_po_id_cond";
						$result_sql_order=sql_select($sql_order);
						foreach($result_sql_order as $row)
						{
							$order_arr[$row['ID']]['job_no']=$row['JOB_NO'];
							$order_arr[$row['ID']]['buyer_name']=$row['BUYER_NAME'];
							$order_arr[$row['ID']]['style_ref_no']=$row['STYLE_REF_NO'];
							$order_arr[$row['ID']]['po_number']=$row['PO_NUMBER'];
							$order_arr[$row['ID']]['po_quantity']=$row['PO_QUANTITY'];
							$order_arr[$row['ID']]['grouping']=$row['GROUPING'];
						}
						unset($result_sql_order);

	                    $con = connect();
	                    $r_id2=execute_query("delete from tmp_batch_or_iss where userid=$user_id and block=4");
	                    if($r_id2)
	                    {
	                        oci_commit($con);
	                    }

	                    if(!empty($batch_id_array))
	                    {
	                        foreach ($batch_id_array as $batch_id_key => $batch_id_val) 
	                        {
	                            $r_id_1_4=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_no, type, block) values ($user_id,'".$batch_id_key."',1,4".")");
	                            if($r_id_1_4) 
	                            {
	                                $r_id_1_4=1;
	                            } 
	                            else 
	                            {
	                                echo "insert into tmp_batch_or_iss (userid, batch_issue_no, type, block) values ($user_id,'".$batch_id_key."',1,4".")";
	                                oci_rollback($con);
	                                die;
	                            }
	                        }

	                        if($r_id_1_4)
	                        {
	                            oci_commit($con);
	                        }
	                        else
	                        {
	                            oci_rollback($con);
	                            disconnect($con);
	                        }
	                    }

	                    //============================= getting issue amount ================================
	                    $chem_dye_cost_array=array(); //and b.batch_id in($batchIds) 
	                    $chem_dye_cost_kg_array=array();

	                    $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c, tmp_batch_or_iss d where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 and b.batch_id=d.batch_issue_no and d.userid=$user_id and d.type=1 and d.block=4  $working_company_cond_issue $lc_company_cond_issue2 group by a.mst_id, b.batch_id,a.item_category ";
	                    $chem_dye_cost_result=sql_select($chem_dye_cost_sql);

	                    $all_issue_id =array();
	                    foreach($chem_dye_cost_result as $row)
	                    {
	                        $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
	                        $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
	                        $total_batch_qnty=0;
	                        foreach($batch_id_arr as $batch_ids)
	                        {
	                            $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
	                            //echo $total_batch_qnty;die;
	                            //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
	                            
	                        }
	                        
	                        foreach($batch_id_arr as $batch_id)
	                        {
	                            $chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
	                            $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
	                            
	                        }
	                    }

	                    // ============================ getting issue return amount =============================
	                    if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company in($working_company_id)";

	                    if(count($all_issue_id)>0)
	                    {
	                        foreach ($all_issue_id as $issue_id_key => $issue_id_val) 
	                        {
	                            $r_id4=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,4".")");
	                            if($r_id4) 
	                            {
	                                $r_id4=1;
	                            } 
	                            else 
	                            {
	                                echo "insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,4".")";
	                                oci_rollback($con);
	                                die;
	                            }
	                        }

	                        if($r_id4)
	                        {
	                            oci_commit($con);
	                        }
	                        else
	                        {
	                            oci_rollback($con);
	                            disconnect($con);
	                        }

	                        $sql_return_cost="SELECT p.batch_no as batch_id,a.item_category, sum(a.cons_amount) as cons_amount  from inv_issue_master p, inv_transaction a, inv_receive_master b, tmp_batch_or_iss c where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1 and a.issue_id=c.batch_issue_id and c.block=4 and c.type=2 and userid= $user_id  group by p.batch_no,a.item_category"; 

	                        // echo $sql_return_cost;
	                        $cost_return_data_arr=array();
	                        $sql_return_res=sql_select($sql_return_cost);
	                        foreach ($sql_return_res as $row)
	                        {
	                            $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
	                            // $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
	                        }
	                    }
	                }
	                
	                
	                // print_r($cost_return_data_arr);
					/*$other_sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.entry_form=35  and b.batch_against  in(5,7) and b.is_sales!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
					//die;
					$other_result_data_recipe=sql_select($other_sql_recipe);
					foreach ($other_result_data_recipe as $row)
					{
						$other_batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
						if($other_tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
						{
						$other_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
						}
						$other_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
					}*/
					
	                $tot_rows=count($sql_result);
	                $m=1;  $ul_percent=0; $tot_dye_qnty=0;$total_dyeing_qnty=0;
					$total_first_dye_cost=$total_first_chemical_cost=$total_redying_first_chemical_cost=0;$total_first_dye_chemical_cost=0;
					$total_first_dye_cost=0;$total_redye_first_dye_cost=$total_tot_dye_chemical_cost=$total_redye_first_dye_cost=$total_tot_redying_first_chemical_cost=0;
	                foreach ( $other_batch_wise_array as $batch_id=>$row )
	                {
	                    if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$batch_against_id=$row[('batch_against')];
						$po_id=rtrim($row[('po_id')],',');
	                    $po_id_arr=array_unique(explode(",",$po_id)); 
						
						 
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trw_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="trw_<? echo $m; ?>">
	                        <td width="30"><? echo $m; ?></td>
	                        <td width="40"><p><? echo $machine_cap_array[$row[('machine_id')]]["machine_no"];//$row[('booking_no')]; ?></p></td>
	                        <td width="50" align="center"><p>
	                        <?
							echo $row[('batch_no')];
	                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;;$ref_no='';
	                        foreach($po_id_arr as $po_id)
	                        {
	                            if(!in_array($order_arr[$po_id]['job_no'],$job_chech))
	                            {
	                                $job_chech[]=$order_arr[$po_id]['job_no'];
	                                if($job_all=="") $job_all=$order_arr[$po_id]['job_no']; else $job_all .=", ".$order_arr[$po_id]['job_no'];
	                                if($buyer_all=="") $buyer_all=$buyer_library[$order_arr[$po_id]['buyer_name']]; else $buyer_all .=", ".$buyer_library[$order_arr[$po_id]['buyer_name']];
	                                if($style_all=="") $style_all=$order_arr[$po_id]['style_ref_no']; else $style_all .=", ".$order_arr[$po_id]['style_ref_no'];
	                            }
	                            if($po_all=="") $po_all=$order_arr[$po_id]['po_number']; else $po_all .=", ".$order_arr[$po_id]['po_number'];
								 if($ref_no=="") $ref_no=$order_arr[$po_id]['grouping']; else $ref_no .=", ".$order_arr[$po_id]['grouping'];
	                            $po_qnty +=$order_arr[$po_id]['po_quantity'];
	                        }
	                       // echo $job_all; 
							 
							 
								
	                        ?></p></td>
	                        <td width="70"><p><? echo $ref_no;//$buyer_all; echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']]; ?></p></td>
	                        <td width="70"><p><? if($buyer_all) echo $buyer_all; else echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']];//$style_all;  ?></p></td>
	                        <td width="70"><p><? echo $row[('booking_no')];//$po_all;?></p></td>
	                        <td width="70" align="right"><p><? echo $job_all;//number_format($po_qnty,0);  ?></p></td>
	                        <td width="70" align="center"><p><? echo $style_all; ?></p></td>
	                        <td width="70"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
	                        
	                        <td width="70"><p><? echo $row[('fabric_type')]; ?></p></td>
	                        <td width="70" align="right"><p><? echo number_format($row[('qnty')],2,'.',''); $total_dyeing_qnty+=$row[('qnty')]; ?></p></td>
	                        <td width="50" align="center"><p><? $start_time=''; $start_time=$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']; echo  $start_time; ?></p></td>
	                         <td width="50" align="center"><p><? $end_time=''; $end_time=$row[('end_hours')].':'.$row[('end_minutes')]; echo $end_time; ?></p></td>
	                          <td width="50" align="right"><p><? $start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
	                        
	                        if($timeDiffin>0)
	                        {
	                             $time_used=convertMinutes2Hours($timeDiffin); 
	                             echo $time_used;
	                        }
	                        else
	                        {
	                            echo "Invalid";
	                        }
							
	                         ?></p></td>
	                         
	                        <td width="70" align="right" title="Batch ID=<? echo $cost_return_data_arr[$row[('batch_id')]][6].',BId='.$row[('batch_id')].',Tot dyes Cost='.$chem_dye_cost_array[$row[('batch_id')]][6];?>"><p><? 
							if($batch_against_id==1)
							{
							$first_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$row[('batch_id')]][6];
							$first_chemical_cost =($chem_dye_cost_array[$row[('batch_id')]][5]+$chem_dye_cost_array[$row[('batch_id')]][7]) - ($cost_return_data_arr[$row[('batch_id')]][6]+ $cost_return_data_arr[$row[('batch_id')]][7]);
							}
							else
							{
								$redying_first_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$row[('batch_id')]][6];
								$redying_first_chemical_cost =($chem_dye_cost_array[$row[('batch_id')]][5]+$chem_dye_cost_array[$row[('batch_id')]][7]) - ($cost_return_data_arr[$row[('batch_id')]][6]+ $cost_return_data_arr[$row[('batch_id')]][7]);
							}
							  echo number_format($first_chemical_cost,2,'.','');?></p></td>
	                            <td width="70" align="right" title="Total Dye Cost"><p><?  echo number_format($first_dye_cost,2,'.','');
								 $tot_first_dye_chemical_cost=$first_dye_cost+$first_chemical_cost;
								 $total_first_dye_chemical_cost+=$tot_first_dye_chemical_cost; 
								  $total_first_chemical_cost+=$first_chemical_cost; 
								   $total_first_dye_cost+=$first_dye_cost; 
								?></p></td>  
	                            
	                        <td width="70" align="right" title="Total Chemical Dye Cost"><p><? echo number_format($tot_first_dye_chemical_cost,2,'.','');
	                       ?></p></td>

	                                            
	                        <td width="70" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $tot_first_dye_chemi_cost_kg=$tot_first_dye_chemical_cost/$row[('qnty')];echo number_format($tot_first_dye_chemi_cost_kg,2,'.','');
	                         ?> </p></td>
	                        
	                           <td width="70" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><?  echo number_format($redying_first_chemical_cost,2,'.','');?></p></td> 
	                         <td width="70" align="right" title="Batch ID"><p><?  echo number_format($redying_first_dye_cost,2,'.','');?></p></td>
	                        <td width="70" align="right" title="Total chemical Dye Cost"><p><? 
							 $tot_redying_first_chemical_cost=$redying_first_chemical_cost+$redying_first_dye_cost;
								 $total_redye_first_dye_chemical_cost+=$tot_redying_first_chemical_cost; 
								  $total_redying_first_chemical_cost+=$redying_first_chemical_cost; 
								   $total_redye_first_dye_cost+=$redying_first_dye_cost; 
								   
							 echo number_format($tot_redying_first_chemical_cost,2,'.','');
	                        ?></p></td>

	                                             
	                        <td width="70" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $tot_redye_chemi_cost_kg=$tot_redying_first_chemical_cost/$row[('qnty')];echo number_format($tot_redye_chemi_cost_kg,2,'.','');
	                        $total_tot_redying_first_chemical_cost+=$tot_redying_first_chemical_cost;?> </p></td>

	                        <td width="70" align="center"><p><? 
							 $tot_dye_chemical_cost=$tot_redying_first_chemical_cost+$tot_first_dye_chemical_cost;
							 $total_tot_dye_chemical_cost+=$tot_dye_chemical_cost;
							echo number_format($tot_dye_chemical_cost,2,'.',''); ?></p></td>
	                        <td width="" align="center"><p><? echo number_format($tot_dye_chemical_cost/$row[('qnty')],2,'.',''); ?></p></td>
	                    </tr>
	                    <?
	                    $m++;
	                } 
	                ?>
	                </table>
	                <table width="<? echo $without_width;?>px" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
	                    <tfoot>
	                        <th width="30">&nbsp;</th>
	                        <th width="40">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        <th width="70">&nbsp;</th>
	                        
	                        <th width="70"><strong>Total</strong></th>
	                        <th width="70"><? echo number_format($total_dyeing_qnty,2,'.',''); ?></th>
	                        <th width="50">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        <th width="50">&nbsp;</th>
	                        
	                        
	                        <th width="70" id="value_total_chemical_qnty"><? echo number_format($total_first_chemical_cost,2,'.',''); ?></th>
	                        <th width="70"><? echo number_format($total_first_dye_cost,2,'.',''); ?></th>
	                        
	                        <th width="70"><strong><? echo number_format($total_first_dye_chemical_cost,2,'.',''); ?></strong></th>
	                        <th width="70" id="" align="right"><? echo number_format($total_first_dye_chemical_cost/$total_dyeing_qnty,2,'.',''); ?></th>
	                        <th width="70"><? echo number_format($total_redying_first_chemical_cost,2,'.',''); ?></th>
	                        
	                        <th width="70" id="value_total_dye_cost"><? echo number_format($total_redye_first_dye_cost,2,'.',''); ?></th>
	                        <th width="70" id="value_total_chemi_cost"><? echo number_format($total_tot_redying_first_chemical_cost,2,'.',''); ?></th>
	                        <th width="70" title="Tot Dye Cost/Tot dying Qty" id=""><? echo number_format(($total_tot_redying_first_chemical_cost/$total_dyeing_qnty),2,'.',''); ?></th>
	                        <th width="70"  id="value_total_chem_cost" align="right"><? echo number_format($total_tot_dye_chemical_cost,2,'.',''); ?></th>
	                        <th width=""><? echo number_format(($total_tot_dye_chemical_cost/$total_dyeing_qnty),2,'.',''); ?></th>
	                     
	                    </tfoot>
	                </table>
	            </div> 
	            <?
	        }
	        ?>    
	    </div>
	  
	    <?
	    // echo "$total_data****requires/$filename****$tot_rows***$type_id";
	    foreach (glob("$user_id*.xls") as $filename) 
		{
			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename,'w');
		$is_created = fwrite($create_new_doc,ob_get_contents());
		echo "$total_data****$filename****$tot_rows****$type_id";;
		exit();      
}

if($action=="report_generate3") // FOS wise
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    
    //echo $datediff;
    $cbo_company=str_replace("'","",$cbo_company_id);
    $working_company_id=str_replace("'","",$cbo_working_company_id);
    $cbo_unit=str_replace("'","",$cbo_unit_id);
	$txt_batch_no=str_replace("'","",$txt_batch_no);
	$batch_id=str_replace("'","",$batch_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_hide_booking_id=str_replace("'","",$txt_hide_booking_id);
	$type_id=str_replace("'","",$type_id);
	
	//batch_id*txt_batch_no*txt_booking_no*txt_hide_booking_id
    $date_from=str_replace("'","",$txt_date_from);
    $date_to=str_replace("'","",$txt_date_to);
    $type=str_replace("'","",$cbo_type);
    //$floor_id =str_replace("'","",$floor_id );
    if($working_company_id==0) $working_company_cond="";else $working_company_cond="and a.service_company in($working_company_id)";
    if($cbo_company==0 || $cbo_company=='') $lc_company_cond="";else $lc_company_cond="and a.company_id in($cbo_company)";
    
    if($working_company_id==0) $working_company_cond_issue="";else $working_company_cond_issue="and c.knit_dye_company in($working_company_id)";
    if($cbo_company==0 || $cbo_company=='') $lc_company_cond_issue="";else $lc_company_cond_issue="and c.company_id in($cbo_company)";
	 if($cbo_company==0 || $cbo_company=='') $lc_company_cond_issue2="";else $lc_company_cond_issue2="and c.lc_company in($cbo_company)";
   
    if($txt_batch_no!="" && $batch_id=="") $batch_cond="and b.batch_no='$txt_batch_no'";
	else  if($txt_batch_no!="" && $batch_id!="") $batch_cond="and b.id in($batch_id)";
	else $batch_cond="";
	 if($txt_booking_no!="" && $txt_hide_booking_id=="") $book_cond="and b.booking_no like '%$txt_booking_no%'";
	else  if($txt_booking_no!="" && $txt_hide_booking_id!="") $book_cond="and b.booking_no_id in($txt_hide_booking_id)";
	else $book_cond="";
	if($date_from!="" && $date_to!="" )
	{
	$date_cond="and a.process_end_date between '$date_from' and '$date_to'";
	}
	else $date_cond="";
   
    if($cbo_unit!=0) $floor_cond="and a.floor_id=$cbo_unit";else $floor_cond="";
    $company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
 

    
    $machine_cap_array=array();
    $mach_cap_sql=sql_select("select id, machine_no, sum(prod_capacity) as prod_capacity from lib_machine_name where status_active=1 and is_deleted=0 group by id, machine_no");
    foreach($mach_cap_sql as $row)
    {
        $machine_cap_array[$row[csf('id')]]["machine_no"]=$row[csf('machine_no')];
        $machine_cap_array[$row[csf('id')]]["prod_capacity"]=$row[csf('prod_capacity')];
    }
    
	function convertMinutes2Hours($Minutes)
	{
	    if ($Minutes < 0)
	    {
	        $Min = Abs($Minutes);
	    }
	    else
	    {
	        $Min = $Minutes;
	    }
	    $iHours = Floor($Min / 60);
	    $Minutes = ($Min - ($iHours * 60)) / 100;
	    $Minutes = number_format($Minutes,2,'.','');
	    $tHours = $iHours + $Minutes;
	    $tHours = number_format($tHours,2,'.','');
	    if ($Minutes < 0)
	    {
	        $tHours = $tHours * (-1);
	    }
	    $aHours = explode(".", $tHours);
	    $iHours = $aHours[0];
	    if (empty($aHours[1]))
	    {
	        $aHours[1] = "00";
	    }
	    $Minutes = $aHours[1];
	    if (strlen($Minutes) < 2)
	    {
	        $Minutes = $Minutes ."0";
	    }
	    $tHours = $iHours .":". $Minutes;
	    return $tHours;
	}
	    
	ob_start(); 
	//$table_width=90+($datediff*160);
	    
	?>
    <div>
        <table width="2250px" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="25" class="form_caption" ><strong style="font-size:18px"><? echo $company_library[$cbo_company];?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="25" class="form_caption" ><strong style="font-size:14px">Daily Dyeing Produciton Analysis Report</strong></td>
            </tr>
            <tr>  
               <td align="center" width="100%" colspan="25" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>  
        </table>
        <?
        $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
        if($type==0 || $type==1 || $type==3 || $type==4)
        {
            $buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
            $gsm_library=return_library_array( "select id,gsm from  product_details_master", "id", "gsm");

            $load_data=array();
                $load_time_data=sql_select("SELECT a.id,a.batch_id,a.batch_no,a.load_unload_id,a.process_end_date,a.end_hours,a.end_minutes from pro_fab_subprocess a where a.load_unload_id=1 and a.entry_form=35 and  a.status_active=1  and a.is_deleted=0 $working_company_cond $lc_company_cond");
            foreach($load_time_data as $row_time)// for Loading time
            {
                $load_data[$row_time[csf('batch_id')]]['process_end_date']=$row_time[csf('process_end_date')];
                $load_data[$row_time[csf('batch_id')]]['end_hours']=$row_time[csf('end_hours')];
                $load_data[$row_time[csf('batch_id')]]['end_minutes']=$row_time[csf('end_minutes')];
            }
        }

        if($type==0 || $type==1) // Self
        {          
            ?>
            <style>
			span::before {content: '\A'; white-space: pre;}
			</style>
            <div align="left" style="background-color:#E1E1E1; color:#000; width:250px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Dyeing Production</i></u></strong></div>
            <table width="2670" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
                <thead>
                    <th width="40">SL</th>
                    <th width="110">WO/Booking No</th>
                    <th width="90">Job No</th>
                    <th width="120">Buyer Name</th>
                    <th width="130">Style Ref.</th>
                    <th width="140">Order No</th>
                    <th width="100">Order Qty.</th>
                    <th width="60">GSM</th>
                    <th width="100">Fabric Color</th>
                    <th width="100">Batch No/ Lot No.</th>
                    <th width="50">Ext. No</th>
                    <th width="100">MC No.</th>
                    <th width="90">MC Capacity</th>
                    <th width="70">Dyeing Date</th>
                    <th width="100">Dyeing Qty</th>
                    <th width="60">UL %</th>
                    <th width="60">Hour Req.</th>
                    <th width="80">Loading Time</th>
                    <th width="80">Unloading Time</th>
                    <th width="100">Liquor Ratio</th>
                    <th width="70">Hour Used</th>
                    <th width="60">Hour Devi.</th>                
                    <th width="90">Process/ Color Range</th>
                    <th width="110">Total Dye Cost</th>
                    <th width="90">Total Dye Cost/kg</th>
                    <th width="110">Total Chem Cost</th>
                    <th width="90">Total Chem Cost/Kg</th>
                    <th width="130">Dyeing Company</th>
                    <th>Remarks</th>
                </thead>
            </table>
            <div style="width:2690px; overflow-y:scroll; max-height:275px;" id="scroll_body" >
                <table width="2670" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body">
                <?
                $sql_dtls="SELECT b.id,b.extention_no,a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, c.po_id, sum(c.batch_qnty) as qnty, d.style_ref_no, d.job_no as fso_order_no, d.within_group, d.po_buyer, d.po_job_no, d.buyer_id
                from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c, fabric_sales_order_mst d
                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id   and c.po_id=d.id and b.sales_order_id=d.id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against  in(1,2) and b.is_sales=1 $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond 
                group by b.id,b.extention_no, a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, c.po_id, d.style_ref_no, d.job_no, d.within_group, d.po_buyer, d.po_job_no, d.buyer_id order by a.process_end_date";                
              	// echo $sql_dtls;die;

                $sql_result=sql_select($sql_dtls);
                if(!empty($sql_result))
                {
                    // ================== getting batch id =========================
                    $batch_id_array = array();
					$all_prod_id="";
                    foreach ( $sql_result as $row )
                    {
                        $batch_id_array[$row[csf('batch_id')]] = $row[csf('batch_id')];
                        $booking_no_arr=explode("-",$row[csf('booking_no')]);
                        $fb_booking_no=$booking_no_arr[1];
                        if($fb_booking_no=='FB' || $fb_booking_no=='Fb') //echo $fb_booking_no.'A';else echo $fb_booking_no.'B';
                        {
	                        $self_batch_wise_array[$row[csf('batch_id')]]['id']= $row[csf('id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['batch_id']= $row[csf('batch_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['extention_no']= $row[csf('extention_no')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['dyeing_company']= $row[csf('dyeing_company')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['service_company']= $row[csf('service_company')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['production_date']= $row[csf('production_date')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['process_end_date']= $row[csf('process_end_date')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['machine_id']= $row[csf('machine_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['end_hours']= $row[csf('end_hours')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['end_minutes']= $row[csf('end_minutes')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['remarks']= $row[csf('remarks')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['batch_no']= $row[csf('batch_no')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['color_id']= $row[csf('color_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['booking_no']= $row[csf('booking_no')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['color_range_id']= $row[csf('color_range_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['dur_req_hr']= $row[csf('dur_req_hr')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['dur_req_min']= $row[csf('dur_req_min')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['prod_id']= $row[csf('prod_id')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['po_id']= $row[csf('po_id')];
	                        
	                        $self_batch_wise_array[$row[csf('batch_id')]]['style_ref_no']= $row[csf('style_ref_no')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['fso_order_no']= $row[csf('fso_order_no')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['within_group']= $row[csf('within_group')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['po_buyer']= $row[csf('po_buyer')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['po_job_no']= $row[csf('po_job_no')];
	                        $self_batch_wise_array[$row[csf('batch_id')]]['buyer_id']= $row[csf('buyer_id')];

	                        $self_batch_wise_array[$row[csf('batch_id')]]['qnty']= $row[csf('qnty')];
							if($all_prod_id=="") $all_prod_id=$row[csf('prod_id')];else $all_prod_id.=",".$row[csf('prod_id')];
							$po_id_array[$row[csf('po_id')]]=$row[csf('po_id')];
                        }
                    }
                    
					$po_id_cond=where_con_using_array($po_id_array,0,'a.id');
					$fso_order_arr=array();
					$sql_order="SELECT a.ID, a.job_no as PO_NUMBER, A.WITHIN_GROUP, A.PO_BUYER, A.PO_JOB_NO, A.BUYER_ID, A.STYLE_REF_NO, b.grey_qty as PO_QUANTITY
					from fabric_sales_order_mst a, fabric_sales_order_dtls b 
					where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  $po_id_cond";
		            $result_sql_order=sql_select($sql_order);
		            foreach($result_sql_order as $row)
		            {
		                $fso_order_arr[$row['ID']]['po_quantity']+=$row['PO_QUANTITY'];
		            }
		            unset($result_sql_order);

					$batch_quantity_arr=return_library_array( "SELECT a.id,sum(b.batch_qnty) as  batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($batch_id_array,0,'a.id')." group by a.id", "id", "batch_qnty");
					   
					$sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
					//die;
					$result_data_recipe=sql_select($sql_recipe);
					foreach ($result_data_recipe as $row)
					{
						$batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
						if($tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
						{
						$batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
						}
						$batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
					}
				
                    //============================= getting issue amount ================================
                    $chem_dye_cost_array=array(); //and b.batch_id in($batchIds) 
                    $chem_dye_cost_kg_array=array();
					
				 	$chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1   $lc_company_cond_issue2 $working_company_cond_issue  group by a.mst_id, b.batch_id,a.item_category "; 

                    //echo $chem_dye_cost_sql;die;

                    $chem_dye_cost_result=sql_select($chem_dye_cost_sql);
                    //var_dump($chem_dye_cost_result);die;
                    
                    foreach($chem_dye_cost_result as $row)
                    {
                        $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
                        $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
                        $total_batch_qnty=0;
                        foreach($batch_id_arr as $batch_ids)
                        {
                            $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
                            //echo $total_batch_qnty;die;
                            //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
                        }
                        
                        foreach($batch_id_arr as $batch_id)
                        {
                           if($batch_quantity_arr[$batch_id]>0)
						   {
						    $chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
                            $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
						   }                            
                        }                        
                    }
                    //var_dump($chem_dye_cost_kg_array);//die;

                    // ============================ getting issue return amount =============================
                    if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company in($working_company_id)";
                    if(count($all_issue_id)>0)
                    {                        
                        foreach ($all_issue_id as $issue_id_key => $issue_id_val) 
                        {
                            $r_id3=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,1".")");
                            if($r_id3) 
                            {
                                $r_id3=1;
                            } 
                            /*else 
                            {
                                echo "insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,1".")";
                                oci_rollback($con);
                                die;
                            }*/
                        }

                        if($r_id3)
                        {
                            oci_commit($con);
                        }
                        else
                        {
                            oci_rollback($con);
                            disconnect($con);
                        }
                        
                        //and a.issue_id in($allIssueIds)
                        //$sql_return_cost="SELECT p.batch_no as BATCH_ID, sum(a.cons_amount) as CONS_AMOUNT  from inv_issue_master p, inv_transaction a, inv_receive_master b, tmp_batch_or_iss c where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1 and a.issue_id=c.batch_issue_id and c.userid=$user_id and c.block=1 and c.type=2 group by p.batch_no"; 

                        $sql_return_cost="SELECT p.batch_no as BATCH_ID,a.ITEM_CATEGORY, sum(a.cons_amount) as CONS_AMOUNT  from inv_issue_master p, inv_transaction a, inv_receive_master b, tmp_batch_or_iss c where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond  and b.is_deleted=0 and b.status_active=1 and a.issue_id=c.batch_issue_id and c.userid=$user_id and c.block=1 and c.type=2 group by p.batch_no,a.item_category"; 

                        // echo $sql_return_cost;die();
                        $cost_return_data_arr=array();
                        $sql_return_res=sql_select($sql_return_cost);
                        foreach ($sql_return_res as $row)
                        {
                            $cost_return_data_arr[$row['BATCH_ID']][$row['ITEM_CATEGORY']]+=$row['CONS_AMOUNT'];
                            // $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
                        }
                    }
                }
                

                // print_r($cost_return_data_arr);
                $tot_rows=count($sql_result);
                unset($sql_result);
                $i=1;  $ul_percent=0; $tot_dye_qnty=0;$total_dye_chem_cost=0;$total_chem_cost_kg=0;
                foreach ( $self_batch_wise_array as $batch_id=>$row )
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $po_id_arr=array_unique(explode(",",$row[('po_id')])); 
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[('booking_no')]; ?></p></td>
                        <td width="90" align="center"><p><? echo $row[('po_job_no')];

						$sub_process_id=rtrim($batch_recipe_arr[$batch_id]['sub_process_id'],',');
						$sub_process_ids=array_unique(explode(",",$sub_process_id));
						//$seq_no=$batch_recipe_subpro_arr2[$batch_id][$row[csf("sub_process_id")]]['seq_no'];
						$sid_ratio_all='';$sub_liquor_ratio_all='';
						
						foreach($sub_process_ids as $sid)
						{
							$liquor_ratio=$batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
							if($liquor_ratio>0)
							{
							$seq_no=$batch_recipe_subpro_arr[$batch_id][$sid]['seq_no'];
							$sid_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.'&#013;';
							
							$sub_liquor_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.',';
							}
						}
						$sub_liquor_ratio_all=rtrim($sub_liquor_ratio_all,',');
						$sub_liquor_ratio_allArr=array_unique(explode(",",$sub_liquor_ratio_all));
						$sub_liquor_ratio=$sub_liquor_ratio_allArr[0];
							
                        ?></p></td>
                        <td width="120"><p><? 
                        if($row[('within_group')]==1)
                        {
                        	echo $buyer_all=$buyer_library[$row[('po_buyer')]];
                        }
                        else
                        {
                        	echo $buyer_all=$buyer_library[$row[('buyer_id')]];
                        }
                        //echo $buyer_all; echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']]; ?></p></td>
                        <td width="130"><p><? echo $row[('style_ref_no')];//$style_all;  ?></p></td>
                        <td width="140"><p><? echo $row[('fso_order_no')];//$po_all;?></p></td>
                        <td width="100" align="right"><p><? echo number_format($fso_order_arr[$row[('po_id')]]['po_quantity'],4);//number_format($po_qnty,0);  ?></p></td>
                        <td width="60" align="center"><p><? echo $gsm_library[$row[('prod_id')]]; ?></p></td>
                        <td width="100"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
                        <td width="100"><p><? echo $row[('batch_no')]; ?></p></td>
                        <td width="50"><p><? echo $row[('extention_no')]; ?></p></td>
                        <td width="100" align="center"><p><? echo $machine_cap_array[$row[('machine_id')]]["machine_no"]; ?></p></td>
                        <td width="90" align="right"><p><? $machine_capacity=$machine_cap_array[$row[('machine_id')]]["prod_capacity"]; echo number_format($machine_capacity,2,'.',''); ?></p></td>
                        <td width="70" align="right"><p><? echo change_date_format($row[('production_date')]); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[('qnty')],2,'.',''); $total_dyeing_qnty+=$row[('qnty')]; ?></p></td>
                        <td width="60" align="right"><p><? $ul_percent=$row[('qnty')]/$machine_capacity*100; echo number_format($ul_percent,2,'.',''); ?></p></td>
                        <td width="60" align="center"><p><? $req_hour=""; $req_hour=$row['dur_req_hr'].":".$row['dur_req_min']; echo $req_hour; ?></p></td>
                        <td width="80" align="center"><p><? $start_time=''; $start_time=$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']; echo  $start_time; ?></p></td>
                        
                        <td width="80" align="center"><p><? $end_time=''; $end_time=$row[('end_hours')].':'.$row[('end_minutes')]; echo $end_time; ?></p></td>
                         <td width="100" align="center" id="title_td" title="<? echo $sid_ratio_all;?>"><p><?  echo $sub_liquor_ratio; ?></p></td>
                        <td width="70" align="right"><p><? $start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
                        
                        if($timeDiffin>0)
                        {
                             $time_used=convertMinutes2Hours($timeDiffin); 
                             echo $time_used;
                        }
                        else
                        {
                            echo "Invalid";
                        }
                         ?></p></td>
                        <?
                        
                        $req_time=strtotime($row[('dur_req_hr')].":".$row[('dur_req_min')].":" . 00);
                        $hour_dev_cal=0;$tot_deviation="";
                        $time_used = number_format($time_used,2);
                        // echo $req_time."--".$time_used."<br>";
                        if($req_time!="")
                        {
                            $used_time=strtotime($time_used.':'. 00);
                            $hour_dev_cal=($used_time-$req_time)/60;
                            $tot_deviation=convertMinutes2Hours($hour_dev_cal); 
                        }
                        if($hour_dev_cal>0)
                        {
                            ?>
                            <td width="60" align="center" bgcolor="#FF0000"><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        else
                        {
                            ?>
                            <td width="60" align="center" ><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        ?>
                        <td width="90"><p><? echo $color_range[$row[('color_range_id')]]; ?></p></td>

                        <td width="110" align="right" title="Batch ID=<? echo $cost_return_data_arr[$row[('batch_id')]][6].',BId='.$row[('batch_id')].',Tot dyes Cost='.$chem_dye_cost_array[$row[('batch_id')]][6];?>"><p><? $dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$row[('batch_id')]][6]; echo number_format($dye_cost,4,'.','');?></p></td>
                        <td width="90" align="right" title="Total Dye Cost/Dyeing Qty"><p><? $tot_dye_cost_kg=$dye_cost/$row[('qnty')];echo number_format($tot_dye_cost_kg,4,'.','');
                        $total_dye_cost+=$dye_cost;$total_dye_cost_kg+=$tot_dye_cost_kg;?></p></td>

                        <td width="110" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $chemi_cost =$chem_dye_cost_array[$row[('batch_id')]][5] - $cost_return_data_arr[$row[('batch_id')]][5]; echo number_format($chemi_cost,4,'.','');?></p></td>                        
                        <td width="90" align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $tot_chemi_cost_kg=$chemi_cost/$row[('qnty')];echo number_format($tot_chemi_cost_kg,4,'.','');
                        $total_chem_cost+=$chemi_cost;$total_chem_cost_kg+=$tot_chemi_cost_kg;?> </p></td>

                        <td width="130" align="center"><p><? echo $company_library[$row[('service_company')]]; ?></p></td>
                        <td><p><? echo $row[('remarks')]; ?></p></td>
                    </tr>
                    <?
                    $i++;
                    //$total_chem_dye_cost+=$chem_dye_cost;
                } 
                ?>
                </table>
                <table width="2670px" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
                    <tfoot>
                        <th width="40">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="70"><strong>Total</strong></th>
                        <th width="100" id="value_total_dyeing_qnty" align="right"><? echo number_format($total_dyeing_qnty,2,'.',''); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="110" id="value_total_dye_cost"><? echo number_format($total_dye_cost,2,'.',''); ?></th>
                        <th width="90" title="Tot Dye Cost/Tot dying Qty" id=""><? echo number_format(($total_dye_cost/$total_dyeing_qnty),2,'.',''); ?></th>
                        <th width="110"  id="value_total_chem_cost" align="right"><? echo number_format($total_chem_cost,2,'.',''); ?></th>
                        <th width="90"  align="right"><? echo number_format(($total_chem_cost/$total_dyeing_qnty),2,'.',''); ?></th>
                        <th width="130">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div> 
            <?
        }
        if($type==0 || $type==2) // In-bound / Subcon
        {
            ?>
            <br />
            <div>
            <div align="left" style="background-color:#E1E1E1; color:#000; width:320px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>In-bound Subcontract Dyeing Production</i></u></strong></div>
            <table width="2470" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
                <thead>
                <th width="40">SL</th>
                <th width="90">Job No</th>
                <th width="120">Party Name</th>
                <th width="130">Style Ref.</th>
                <th width="140">Order No</th>
                <th width="100">Order Qty.</th>
                <th width="100">Batch No</th>
                <th width="50">Ext. No</th>
                <th width="100">Batch Color</th>
                <th width="100">MC No.</th>
                <th width="90">MC Capacity</th>
                <th width="70">Dyeing Date</th>
                <th width="100">Dyeing Qty</th>
                <th width="60">UL %</th>
                <th width="60">Hour Req.</th>
                <th width="80">Loading Time</th>
                <th width="80">Unloading Time</th>
                <th width="100">Liquor Ratio</th>
                <th width="70">Hour Used</th>
                <th width="60">Hour Devi.</th>                
                <th width="90">Process/ Color Range</th>
                <th width="110">Total Dye Cost</th>
                <th width="90">Total Dye Cost/Kg</th>
                <th width="110">Total Chem Cost</th>
                <th width="90">Total Chem Cost/Kg</th>
                <th width="130">Dyeing Company</th>
                <th>Remarks</th>
                </thead>
            </table>
            <div style="width:2490px; overflow-y:scroll; max-height:275px;" id="scroll_body" >
                <table width="2470" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body1">
                <?  
                $buyer_library_subcon=return_library_array( "select a.id,a.short_name from lib_buyer a, lib_buyer_party_type b where a.id=b.buyer_id and b.party_type in(2,3)", "id", "short_name");
                //$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
                $machine_library=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no");
                
                $job_arr=array();
                $sql_job="SELECT b.id, a.subcon_job, a.party_id, b.cust_style_ref, b.order_no, b.order_quantity from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
                $result_sql_job=sql_select($sql_job);
                foreach($result_sql_job as $row)
                {
                    $job_arr[$row[csf('id')]]['job_no']=$row[csf('subcon_job')];
                    $job_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
                    $job_arr[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
                    $job_arr[$row[csf('id')]]['order_no']=$row[csf('order_no')];
                    $job_arr[$row[csf('id')]]['order_quantity']=$row[csf('order_quantity')];
                }
                
                $load_data_sub=array();
                $load_time_data_sub=sql_select("SELECT a.id, a.batch_id, a.batch_no, a.process_end_date, a.load_unload_id, a.end_hours, a.end_minutes from pro_fab_subprocess  a where a.load_unload_id=1 and a.entry_form=38 and status_active=1  and is_deleted=0 $working_company_cond $lc_company_cond");
                
                foreach($load_time_data_sub as $row_time)// for Loading time
                {
                    $load_data_sub[$row_time[csf('batch_id')]]['process_end_date']=$row_time[csf('process_end_date')];
                    $load_data_sub[$row_time[csf('batch_id')]]['end_hours']=$row_time[csf('end_hours')];
                    $load_data_sub[$row_time[csf('batch_id')]]['end_minutes']=$row_time[csf('end_minutes')];
                }
                unset($load_time_data_sub);
                
                
               /* $machine_cap_array=array();
                $mach_cap_sql=sql_select("select id, sum(prod_capacity) as prod_capacity from lib_machine_name where status_active=1 and is_deleted=0 group by id");
                foreach($mach_cap_sql as $row)
                {
                    $machine_cap_array[$row[csf('id')]]=$row[csf('prod_capacity')];
                }*/
                //echo $db_type."jahid";die;
            
                if($db_type==0)
                {
                    $sql_sub_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company, a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id), group_concat(c.po_id) as po_id, sum(c.batch_qnty) as qnty 
                    FROM pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
                    WHERE a.batch_id=b.id and a.entry_form=38 and a.load_unload_id=2 and b.id=c.mst_id   and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $floor_cond  $batch_cond $book_cond   $working_company_cond $lc_company_cond 
                    GROUP BY b.id, b.extention_no,a.batch_id, a.company_id,a.service_company, a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                else if($db_type==2)
                {
                    $sql_sub_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company, a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id), LISTAGG(cast(c.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.po_id) as po_id, sum(c.batch_qnty) as qnty 
                    FROM pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
                    WHERE a.batch_id=b.id and a.entry_form=38 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $batch_cond $book_cond $date_cond $floor_cond  $lc_company_cond $working_company_cond 
                    GROUP BY b.id, b.extention_no,a.batch_id, a.company_id,a.service_company, a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                //echo $sql_sub_dtls;die;
                $sql_sub_result=sql_select($sql_sub_dtls);
				 $tot_rows=count($sql_sub_result);
				$subBatch_id_arr=array();
				foreach( $sql_sub_result as $row)
				{
					$subBatch_id_arr[$row[csf('id')]]=$row[csf('id')];
				}
				
				$batch_quantity_arr=return_library_array( "SELECT a.id,sum(b.batch_qnty) as  batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($subBatch_id_arr,0,'a.id')." group by a.id", "id", "batch_qnty");
				 
                $chem_dye_cost_array=array();
                $chem_dye_cost_kg_array=array();

                $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1   $working_company_cond_issue  $lc_company_cond_issue2 group by a.mst_id, b.batch_id,a.item_category ";  

                $chem_dye_cost_result=sql_select($chem_dye_cost_sql);
                foreach($chem_dye_cost_result as $row)
                {
                    $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
                    $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
                    $total_batch_qnty=0;
                    foreach($batch_id_arr as $batch_ids)
                    {
                        $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
                        //echo $total_batch_qnty;die;
                        //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
                        
                    }
                    
                    foreach($batch_id_arr as $batch_id)
                    {
                         if($batch_quantity_arr[$batch_id]>0)
					  	 {
						$chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
                        $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
					  	 }
                        
                    }
                }
					
                if($type==2)
                {
                   /* $batchIds = "'" . implode ( "', '", $sub_batch_id_array ) . "'";
                    $baIds=chop($batchIds,','); $batch_cond_in="";
                    $ba_ids=count(array_unique(explode(",",$batchIds)));
                    if($db_type==2 && $ba_ids>1000)
                    {
                    $batch_cond_in=" and (";
                    $baIdsArr=array_chunk(explode(",",$baIds),999);
                    foreach($baIdsArr as $ids)
                    {
                    $ids=implode(",",$ids);
                    $batch_cond_in.=" b.batch_id in($ids) or"; 
                    }
                    $batch_cond_in=chop($batch_cond_in,'or ');
                    $batch_cond_in.=")";
                    }
                    else
                    {
                    $batch_cond_in=" and b.batch_id in($baIds)";
                    }*/

                    //============================= getting issue amount ================================
                   

                    // ============================ getting issue return amount =============================
                    /*
                        ############################  

                        As this code has no impact on this block so it is commented 

                        ###################

                        if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company=$working_company_id";
                        if(count($all_issue_id)>0)
                        {
                            $allIssueIds = implode(",", $all_issue_id);

                            $issueIds=chop($allIssueIds,','); $issue_cond_in="";
                            $iss_ids=count(array_unique(explode(",",$allIssueIds)));
                            if($db_type==2 && $iss_ids>1000)
                            {
                            $issue_cond_in=" and (";
                            $issueIdsArr=array_chunk(explode(",",$issueIds),999);
                            foreach($issueIdsArr as $ids)
                            {
                            $ids=implode(",",$ids);
                            $issue_cond_in.=" a.issue_id in($ids) or"; 
                            }
                            $issue_cond_in=chop($issue_cond_in,'or ');
                            $issue_cond_in.=")";
                            }
                            else
                            {
                            $issue_cond_in=" and a.issue_id in($issueIds)";
                            }
                            //and a.issue_id in($allIssueIds)
                            $sql_return_cost="SELECT p.batch_no as batch_id, sum(a.cons_amount) as cons_amount  from inv_issue_master p, inv_transaction a, inv_receive_master b where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1  $issue_cond_in  group by p.batch_no"; 

                            $cost_return_data_arr=array();
                            $sql_return_res=sql_select($sql_return_cost);
                            foreach ($sql_return_res as $row)
                            {
                                $cost_return_data_arr[$row[csf('batch_id')]]=$row[csf('cons_amount')];
                                // $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
                            }
                        }
                    */
                }
                // Issue end
				$sub_sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.entry_form=38  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
				//die;
				$sub_result_data_recipe=sql_select($sub_sql_recipe);
				foreach ($sub_result_data_recipe as $row)
				{
					$sub_batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
					if($sub_tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
					{
					$sub_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
					}
					$sub_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
				}
				
            
                $k=1;  $ul_percent=0; $tot_dye_qnty=0;$total_dye_chem_cost=0;
                foreach ( $sql_sub_result as $row )
                {
                    if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $po_id_arr=array_unique(explode(",",$row[csf('po_id')])); 
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color_sub('row_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="row_<? echo $k; ?>">
                        <td width="40"><? echo $k; ?></td>
                        <td width="90" align="center"><p>
                        <?
                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;
                        foreach($po_id_arr as $po_id)
                        {
                            if(!in_array($job_arr[$po_id]['job_no'],$job_chech))
                            {
                                $job_chech[]=$job_arr[$po_id]['job_no'];
                                if($job_all=="") $job_all=$job_arr[$po_id]['job_no']; else $job_all .=", ".$job_arr[$po_id]['job_no'];
                                if($buyer_all=="") $buyer_all=$job_arr[$po_id]['party_id']; else $buyer_all .=", ".$job_arr[$po_id]['party_id'];
                                if($style_all=="") $style_all=$job_arr[$po_id]['style_ref_no']; else $style_all .=", ".$job_arr[$po_id]['style_ref_no'];
                            }
                            if($po_all=="") $po_all=$job_arr[$po_id]['order_no']; else $po_all .=", ".$job_arr[$po_id]['order_no'];
                            $po_qnty +=$job_arr[$po_id]['order_quantity'];
                        }
                        echo $job_all;
						
						$batch_id=$row[csf('batch_id')];
						
						$sub_process_id=rtrim($sub_batch_recipe_arr[$batch_id]['sub_process_id'],',');
						$sub_process_ids=array_unique(explode(",",$sub_process_id));
						$sub_sid_ratio_all='';$sub_liquor_ratio_all='';
						foreach($sub_process_ids as $sid)
						{
							$sub_liquor_ratio=$sub_batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
							if($sub_liquor_ratio>0)
							{
							$seq_no=$sub_batch_recipe_subpro_arr[$batch_id][$sid]['seq_no'];
							$sub_sid_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$sub_liquor_ratio.'&#013;';
							
							$sub_liquor_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$sub_liquor_ratio.',';
							}
							
						}
						$sub_liquor_ratio_all=rtrim($sub_liquor_ratio_all,',');
						$sub_liquor_ratio_allArr=array_unique(explode(",",$sub_liquor_ratio_all));
						$sub_liquor_ratio=$sub_liquor_ratio_allArr[0]; 
                        ?></p></td>
                        <td width="120"><p><? echo $buyer_library_subcon[$buyer_all]; ?></p></td>
                        <td width="130"><p><? echo $style_all;  ?></p></td>
                        <td width="140"><p><? echo $po_all;?></p></td>
                        <td width="100" align="right"><p><? echo number_format($po_qnty,0);  ?></p></td>
                        <td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
                        <td width="50"><p><? echo $row[csf('extention_no')]; ?></p></td>
                        <td width="100"><p><? echo $color_library[$row[csf('color_id')]]; ?></p></td>
                        <td width="100" align="center"><p><? echo $machine_cap_array[$row[csf('machine_id')]]["machine_no"]; ?></p></td>
                        <td width="90" align="right"><p><? $machine_capacity=$machine_cap_array[$row[csf('machine_id')]]["prod_capacity"]; echo number_format($machine_capacity,2,'.',''); ?></p></td>
                        <td width="70" align="right"><p><? echo change_date_format($row[csf('production_date')]); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[csf('qnty')],2,'.',''); $total_sub_dyeing_qnty+=$row[csf('qnty')]; ?></p></td>
                        <td width="60" align="right"><p><? $ul_percent=$row[csf('qnty')]/$machine_capacity*100; echo number_format($ul_percent,2,'.',''); ?></p></td>
                        <td width="60" align="center"><p><? $req_hour=""; $req_hour=$row[csf('dur_req_hr')].":".$row[csf('dur_req_min')]; echo $req_hour; ?></p></td>
                        <td width="80" align="center"><p><? $start_time=''; $start_time=$load_data_sub[$row[csf('id')]]['end_hours'].':'.$load_data_sub[$row[csf('id')]]['end_minutes']; echo  $start_time; ?></p></td>
                        <td width="80" align="center"><p><? $end_time=''; $end_time=$row[csf('end_hours')].':'.$row[csf('end_minutes')]; echo $end_time; ?></p></td>
                         <td width="100" align="center" title="<? echo $sub_sid_ratio_all;?>"><p><?  echo $sub_liquor_ratio; ?></p></td>
                         
                        <td width="70" align="right"><p><? $start_time=strtotime($load_data_sub[$row[csf('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[csf('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
                         if($timeDiffin>0)
                         {  
                             $time_used=convertMinutes2Hours($timeDiffin); 
                             echo $time_used ;
                         }
                         else
                         {
                             echo "Invalid";
                         }
                         ?></p></td>
                        <?
                        
                        $req_time=strtotime($row[csf('dur_req_hr')].":".$row[csf('dur_req_min')].":" . 00);
                        $hour_dev_cal=0;$tot_deviation="";
                        if($req_time!="")
                        {
                            $used_time=strtotime($time_used.':'. 00);
                            $hour_dev_cal=($used_time-$req_time)/60;
                            $tot_deviation=convertMinutes2Hours($hour_dev_cal); 
                        }
                        if($hour_dev_cal>0)
                        {
                            ?>
                            <td width="60" align="center" bgcolor="#FF0000"><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        else
                        {
                            ?>
                            <td width="60" align="center" ><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        ?>
                        <td width="90"><p><? echo $color_range[$row[csf('color_range_id')]]; ?></p></td>

                        <td width="110" align="right"><p><? $subcon_dye_cost =$chem_dye_cost_array[$row[csf('batch_id')]][6]; echo number_format($subcon_dye_cost,4,'.',''); ?></p></td>
                        <td width="90" align="right" title="Total Dye Cost/Dyeing Qty"><p><?  $subcon_dye_cost_kg=$subcon_dye_cost/$row[csf('qnty')];
                        echo number_format($subcon_dye_cost_kg,4,'.','');
                        $total_sub_dye_cost+=$subcon_dye_cost;$total_sub_dye_cost_kg+=$subcon_dye_cost_kg; ?></p></td>

                        <td width="110" align="right"><p><? $subcon_chem_cost =$chem_dye_cost_array[$row[csf('batch_id')]][5]; echo number_format($subcon_chem_cost,4,'.',''); ?></p></td>
                        <td width="90" align="right" title="Total Chemical Cost/Dyeing Qty"><p><?  $subcon_chemi_cost_kg=$subcon_chem_cost/$row[csf('qnty')];
                        echo number_format($subcon_chemi_cost_kg,4,'.','');
                        $total_sub_chem_cost+=$subcon_chem_cost;$total_sub_chem_cost_kg+=$subcon_chemi_cost_kg; ?></p></td>

                        <td width="130" align="center"><p><? echo $company_library[$row[csf('service_company')]]; ?></p></td>
                        <td><p><? echo $row[csf('remarks')]; ?></p></td>
                    </tr>
                    <?
                    $k++;
                } 
                ?>
                </table>
                <table width="2470px" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
                    <tfoot>
                        <th width="40">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="70"><strong>Total</strong></th>
                        <th width="100" id="value_total_sub_dyeing_qnty" align="right"><? echo number_format($total_sub_dyeing_qnty,2,'.',''); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                         <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="110" id="" ><? echo number_format($total_sub_dye_cost,4,'.',''); ?></th>
                        <th width="90" id="" align="right"><? echo number_format($total_sub_dye_cost/$total_sub_dyeing_qnty,4,'.',''); ?></th>
                        <th width="110" id="" ><? echo number_format($total_sub_chem_cost,4,'.',''); ?></th>
                        <th width="90" id="" align="right"><? echo number_format($total_sub_chem_cost/$total_sub_dyeing_qnty,4,'.',''); ?></th>
                        <th width="130">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>
            </div>     
            <?
        }
        if($type==0 || $type==3) // Sample
        {
            
            ?>
            <br/>
            <div align="left" style="background-color:#E1E1E1; color:#000; width:250px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Sample Dyeing Production</i></u></strong></div>
            <table width="2670" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
                <thead>
                    <th width="40">SL</th>
                    <th width="110">WO/Booking No</th>
                    <th width="90">Job No</th>
                    <th width="120">Buyer Name</th>
                    <th width="130">Style Ref.</th>
                    <th width="140">Order No</th>
                    <th width="100">Order Qty.</th>
                    <th width="60">GSM</th>
                    <th width="100">Fabric Color</th>
                    <th width="100">Batch No/ Lot No.</th>
                    <th width="50">Ext. No</th>
                    <th width="100">MC No.</th>
                    <th width="90">MC Capacity</th>
                    <th width="70">Dyeing Date</th>
                    <th width="100">Dyeing Qty</th>
                    <th width="60">UL %</th>
                    <th width="60">Hour Req.</th>
                    <th width="80">Loading Time</th>
                    <th width="80">Unloading Time</th>
                    <th width="100">Liquor Ratio</th>
                    <th width="70">Hour Used</th>
                    <th width="60">Hour Devi.</th>                
                    <th width="90">Process/ Color Range</th>
                    <th width="110">Total Dye Cost</th>
                    <th width="90">Dye Cost/Kg</th>
                    <th width="110">Total Chem Cost</th>
                    <th width="90">Chem Cost/Kg</th>
                    <th width="130">Dyeing Company</th>
                    <th>Remarks</th>
                </thead>
            </table>
            <div style="width:2690px; overflow-y:scroll; max-height:275px;" id="scroll_body" >
                <table width="2670" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body3">
                <?                
                if($db_type==0)
                {
                    $sql_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, group_concat(c.po_id) as po_id, sum(c.batch_qnty) as qnty
	                from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
	                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.batch_against n(3,2) and b.is_sales!=1 $floor_cond $working_company_cond $lc_company_cond  $date_cond  $batch_cond $book_cond 
	                group by b.id,b.extention_no,a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                else if($db_type==2)
                {
                    $sql_dtls="SELECT b.id,b.extention_no,a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_without_order,b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, LISTAGG(cast(c.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.po_id) as po_id, sum(c.batch_qnty) as qnty
	                from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c 
	                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against in(3,2) and b.is_sales!=1 $floor_cond $working_company_cond $lc_company_cond  $date_cond $batch_cond $book_cond 
	                group by b.id,b.extention_no, a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id,b.booking_without_order, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by  a.process_end_date";
                }
                //echo $sql_dtls;
                $sql_result=sql_select($sql_dtls);
                foreach ( $sql_result as $row )
                {
                    $sam_batch_id_array[$row[csf('batch_id')]] = $row[csf('batch_id')];
                    $samp_booking_no_arr=explode("-",$row[csf('booking_no')]);
                    $sm_booking_no=$samp_booking_no_arr[1];
                    if($sm_booking_no=='SM' || $sm_booking_no=='SMN') 
                    {
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['id']=$row[csf('id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['extention_no']=$row[csf('extention_no')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['dyeing_company']=$row[csf('dyeing_company')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['service_company']=$row[csf('service_company')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['production_date']=$row[csf('production_date')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['process_end_date']=$row[csf('process_end_date')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['end_hours']=$row[csf('end_hours')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['end_minutes']=$row[csf('end_minutes')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['booking_without_order']=$row[csf('booking_without_order')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['dur_req_hr']=$row[csf('dur_req_hr')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['dur_req_min']=$row[csf('dur_req_min')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['prod_id']=$row[csf('prod_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['po_id']=$row[csf('po_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['qnty']=$row[csf('qnty')];
                    }
                }
				$samp_sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.entry_form=35  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
				//die;
				$samp_result_data_recipe=sql_select($samp_sql_recipe);
				foreach ($samp_result_data_recipe as $row)
				{
					$samp_batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
					if($samp_tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
					{
					$samp_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
					}
					$samp_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
				}
				$batch_quantity_arr=return_library_array( "select a.id,sum(b.batch_qnty) as  batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ".where_con_using_array($sam_batch_id_array,0,'a.id')." group by a.id", "id", "batch_qnty");
               	// if($type==3 || $type==0)
                //{
                    $batchIds = "'" . implode ( "', '", $sam_batch_id_array ) . "'";
                    $baIds=chop($batchIds,','); $other_batch_cond_in="";
                    $ba_ids=count(array_unique(explode(",",$batchIds)));
                    if($db_type==2 && $ba_ids>1000)
                    {
                    $other_batch_cond_in=" and (";
                    $baIdsArr=array_chunk(explode(",",$baIds),999);
                    foreach($baIdsArr as $ids)
                    {
                    $ids=implode(",",$ids);
                    $other_batch_cond_in.=" b.batch_id in($ids) or"; 
                    }
                    $other_batch_cond_in=chop($other_batch_cond_in,'or ');
                    $other_batch_cond_in.=")";
                    }
                    else
                    {
                    $other_batch_cond_in=" and b.batch_id in($baIds)";
                    }

                    //============================= getting issue amount ================================
                    $chem_dye_cost_array=array(); //and b.batch_id in($batchIds) 
                    $chem_dye_cost_kg_array=array();
                //  $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b where a.id=b.trans_id AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 $batch_cond_in group by a.mst_id, b.batch_id "; 

                $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 $working_company_cond_issue $lc_company_cond_issue2 group by a.mst_id, b.batch_id,a.item_category ";

                //echo $chem_dye_cost_sql;//die;
                $chem_dye_cost_result=sql_select($chem_dye_cost_sql);
                //var_dump($chem_dye_cost_result);die;
                
                foreach($chem_dye_cost_result as $row)
                {
                    $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
                    $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
                    $total_batch_qnty=0;
                    foreach($batch_id_arr as $batch_ids)
                    {
                        $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
                        //echo $total_batch_qnty;die;
                        //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
                        
                    }
                    
                    foreach($batch_id_arr as $batch_id)
                    {
						if($batch_quantity_arr[$batch_id]>0)
					  	{							
                        	$chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
                        	$chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
						}
                        
                    }
                    
                   	// }
                    
                    /*
                        ################################

                            These code has no effect on this block so it is commented

                        ################################

                        if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company=$working_company_id";
                        // if($cbo_company==0 || $cbo_company=='') $lc_company_cond="";else $lc_company_cond="and a.company_id in($cbo_company)";

                        if(count($all_issue_id)>0)
                        {
                            $allIssueIds = implode(",", $all_issue_id);

                            $issueIds=chop($allIssueIds,','); $issue_cond_in="";
                            $iss_ids=count(array_unique(explode(",",$allIssueIds)));
                            if($db_type==2 && $iss_ids>1000)
                            {
                            $issue_cond_in=" and (";
                            $issueIdsArr=array_chunk(explode(",",$issueIds),999);
                            foreach($issueIdsArr as $ids)
                            {
                            $ids=implode(",",$ids);
                            $issue_cond_in.=" a.issue_id in($ids) or"; 
                            }
                            $issue_cond_in=chop($issue_cond_in,'or ');
                            $issue_cond_in.=")";
                            }
                            else
                            {
                            $issue_cond_in=" and a.issue_id in($issueIds)";
                            }
                            //and a.issue_id in($allIssueIds)
                            $sql_return_cost="SELECT p.batch_no as batch_id, sum(a.cons_amount) as cons_amount  from inv_issue_master p, inv_transaction a, inv_receive_master b where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1  $issue_cond_in  group by p.batch_no"; 
                            // echo $sql_return_cost;
                            $cost_return_data_arr=array();
                            $sql_return_res=sql_select($sql_return_cost);
                            foreach ($sql_return_res as $row)
                            {
                                $cost_return_data_arr[$row[csf('batch_id')]]=$row[csf('cons_amount')];
                                // $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
                            }
                        }
                    */
            	}
                
                $tot_rows=count($sql_result);
                $i=1;  $ul_percent=0; $tot_dye_qnty=0;$total_dye_chem_cost=$total_samp_dyeing_qnty=0;
                foreach ( $sample_batch_wise_arr as $batch_id=>$row )
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $po_id_arr=array_unique(explode(",",$row['po_id'])); 
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="110"><p><? echo $row[('booking_no')]; ?></p></td>
                        <td width="90" align="center"><p>
                        <?
                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;
                        foreach($po_id_arr as $po_id)
                        {
                            if(!in_array($order_arr[$po_id]['job_no'],$job_chech))
                            {
                                $job_chech[]=$order_arr[$po_id]['job_no'];
                                if($job_all=="") $job_all=$order_arr[$po_id]['job_no']; else $job_all .=", ".$order_arr[$po_id]['job_no'];
                                if($buyer_all=="") $buyer_all=$buyer_library[$order_arr[$po_id]['buyer_name']]; else $buyer_all .=", ".$buyer_library[$order_arr[$po_id]['buyer_name']];
                                if($style_all=="") $style_all=$order_arr[$po_id]['style_ref_no']; else $style_all .=", ".$order_arr[$po_id]['style_ref_no'];
                            }
                            if($po_all=="") $po_all=$order_arr[$po_id]['po_number']; else $po_all .=", ".$order_arr[$po_id]['po_number'];
                            $po_qnty +=$order_arr[$po_id]['po_quantity'];
                        }
                        echo $job_all; 
						
						$samp_sid_ratio_all='';$samp_liquor_ratio_all='';

						$samp_process_id=rtrim($samp_batch_recipe_arr[$batch_id]['sub_process_id'],',');
						$samp_process_ids=array_unique(explode(",",$samp_process_id));
						
						foreach($samp_process_ids as $sid)
						{
							$liquor_ratio=$samp_batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
							if($liquor_ratio>0)
							{
							$seq_no=$samp_batch_recipe_subpro_arr[$batch_id][$sid]['seq_no'];
							$samp_sid_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.'&#013;';
							
							$samp_liquor_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.',';
							}
							
						}
						$samp_liquor_ratio_all=rtrim($samp_liquor_ratio_all,',');
						$samp_liquor_ratio_allArr=array_unique(explode(",",$samp_liquor_ratio_all));
						$samp_liquor_ratio=$samp_liquor_ratio_allArr[0];
						
                        ?></p></td>
                        <td width="120"><p><? echo $buyer_all; echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']]; ?></p></td>
                        <td width="130"><p><? echo $style_all;  ?></p></td>
                        <td width="140"><p><? echo $po_all;?></p></td>
                        <td width="100" align="right"><p><? echo number_format($po_qnty,0);  ?></p></td>
                        <td width="60" align="center"><p><? echo $gsm_library[$row[('prod_id')]]; ?></p></td>
                        <td width="100"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
                        <td width="100"><p><? echo $row[('batch_no')]; ?></p></td>
                        <td width="50"><p><? echo $row[('extention_no')]; ?></p></td>
                        <td width="100" align="center"><p><? echo $machine_cap_array[$row[('machine_id')]]["machine_no"]; ?></p></td>
                        <td width="90" align="right"><p><? $machine_capacity=$machine_cap_array[$row[('machine_id')]]["prod_capacity"]; echo number_format($machine_capacity,2,'.',''); ?></p></td>
                        <td width="70" align="right"><p><? echo change_date_format($row[('production_date')]); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[('qnty')],2,'.',''); $total_samp_dyeing_qnty+=$row[('qnty')]; ?></p></td>
                        <td width="60" align="right"><p><? $ul_percent=$row[('qnty')]/$machine_capacity*100; echo number_format($ul_percent,2,'.',''); ?></p></td>
                        <td width="60" align="center"><p><? $req_hour=""; $req_hour=$row[('dur_req_hr')].":".$row[('dur_req_min')]; echo $req_hour; ?></p></td>
                        <td width="80" align="center"><p><? $start_time=''; $start_time=$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']; echo  $start_time; ?></p></td>
                        <td width="80" align="center"><p><? $end_time=''; $end_time=$row[('end_hours')].':'.$row[('end_minutes')]; echo $end_time; ?></p></td>
                        <td width="100" align="center" title="<? echo $samp_sid_ratio_all;?>"><p><?  echo $samp_liquor_ratio; ?></p></td>
                        <td width="70" align="right"><p><? $start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
                        
                        if($timeDiffin>0)
                        {
                             $time_used=convertMinutes2Hours($timeDiffin); 
                             echo $time_used; // 
                        }
                        else
                        {
                            echo "Invalid";
                        }
                         ?></p></td>
                        <?
                        
                        $req_time=strtotime($row[('dur_req_hr')].":".$row[('dur_req_min')].":" . 00);
                        $hour_dev_cal=0;$tot_deviation="";
                        if($req_time!="")
                        {
                            $used_time=strtotime($time_used.':'. 00);
                            $hour_dev_cal=($used_time-$req_time)/60;
                            $tot_deviation=convertMinutes2Hours($hour_dev_cal); 
                        }
                        if($hour_dev_cal>0)
                        {
                            ?>
                            <td width="60" align="center" bgcolor="#FF0000"><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        else
                        {
                            ?>
                            <td width="60" align="center" ><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        ?>
                        <td width="90"><p><? echo $color_range[$row[('color_range_id')]]; ?></p></td>

                        <td width="110" align="right" title="Batch ID=<? echo $row[('batch_id')];?>"><p><? $samp_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6];  if($samp_dye_cost) echo number_format($samp_dye_cost,4,'.','');else echo "";// echo number_format($samp_dye_cost,4,'.','');?></p></td>
                        <td width="90" align="right" title="Tot Dye Cost/Dyeing Qty"><p><? 
						 if($samp_dye_cost) $tot_samp_dye_cost_kg=$samp_dye_cost/$row[('qnty')];
						 else $tot_samp_dye_cost_kg=0;
                        echo number_format($tot_samp_dye_cost_kg,4,'.','');
                        $total_samp_dye_cost+=$samp_dye_cost;$total_samp_dye_cost_kg+=$tot_samp_dye_cost_kg;?> </p></td>

                        <td width="110" align="right" title="Batch ID=<? echo $row[('batch_id')];?>"><p><? $samp_chem_cost =$chem_dye_cost_array[$row[('batch_id')]][5]; echo number_format($samp_chem_cost,4,'.','');?></p></td>
                        <td width="90" align="right" title="Tot Chemical Cost/Dyeing Qty"><p><? $tot_samp_chemi_cost_kg=$samp_chem_cost/$row[('qnty')];
                        echo number_format($tot_samp_chemi_cost_kg,4,'.','');
                        $total_samp_chem_cost+=$samp_chem_cost;$total_samp_chem_cost_kg+=$tot_samp_chemi_cost_kg;?> </p></td>

                        <td width="130" align="center"><p><? echo $company_library[$row[('service_company')]]; ?></p></td>
                        <td><p><? echo $row[('remarks')]; ?></p></td>
                    </tr>
                    <?
                    $i++;
                } 
                ?>
                </table>
                <table width="2670px" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
                    <tfoot>
                        <th width="40">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="70"><strong>Total</strong></th>
                        <th width="100" id="value_dyeing_qnty" align="right"><? echo number_format($total_samp_dyeing_qnty,2,'.',''); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="110" id="value_dye_cost"><? echo number_format($total_samp_dye_cost,4,'.',''); ?></th>
                        <th width="90"  align="right"><? echo number_format(($total_samp_dye_cost/$total_samp_dyeing_qnty),4,'.',''); ?></th>
                        <th width="110" id="value_chem_cost"><? echo number_format($total_samp_chem_cost,4,'.',''); ?></th>
                        <th width="90"  align="right"><? echo number_format(($total_samp_chem_cost/$total_samp_dyeing_qnty),4,'.',''); ?></th>
                        <th width="130">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div> 
            <?
        }
        if($type==0 || $type==4) // Others, Gmts Dyeing and Without Booking
        {
            
            ?>
            <div align="left" style="background-color:#E1E1E1; color:#000; width:250px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Others Dyeing Production</i></u></strong></div>
            <table width="2670" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
                <thead>
                    <th width="40">SL</th>
                    <th width="110">WO/Booking No</th>
                    <th width="90">Job No</th>
                    <th width="120">Buyer Name</th>
                    <th width="130">Style Ref.</th>
                    <th width="140">Order No</th>
                    <th width="100">Order Qty.</th>
                    <th width="60">GSM</th>
                    <th width="100">Fabric Color</th>
                    <th width="100">Batch No/ Lot No.</th>
                    <th width="50">Ext. No</th>
                    <th width="100">MC No.</th>
                    <th width="90">MC Capacity</th>
                    <th width="70">Dyeing Date</th>
                    <th width="100">Dyeing Qty</th>
                    <th width="60">UL %</th>
                    <th width="60">Hour Req.</th>
                    <th width="80">Loading Time</th>
                    <th width="80">Unloading Time</th>
                    <th width="100">Liquor Ratio</th>
                    <th width="70">Hour Used</th>
                    <th width="60">Hour Devi.</th>                
                    <th width="90">Process/ Color Range</th>
                    <th width="110">Total Dye Cost</th>
                    <th width="90">Dye Cost/Kg</th>
                    <th width="110">Total Chem Cost</th>
                    <th width="90">Chem Cost/Kg</th>
                    <th width="130">Dyeing Company</th>
                    <th>Remarks</th>
                </thead>
            </table>
            <div style="width:2690px; overflow-y:scroll; max-height:275px;" id="scroll_body" >
                <table width="2670" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="table_body4">
                <?  
                /*
                    $buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
                    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
                    $gsm_library=return_library_array( "select id,gsm from  product_details_master", "id", "gsm");
                    
                    $order_arr=array();
                    $sql_order="Select b.id, a.job_no, a.buyer_name, a.style_ref_no, b.po_number, b.po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
                    $result_sql_order=sql_select($sql_order);
                    foreach($result_sql_order as $row)
                    {
                        $order_arr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
                        $order_arr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
                        $order_arr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
                        $order_arr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
                        $order_arr[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
                    }

                    $non_order_arr=array();
                    $sql_non_order="SELECT a.company_id, a.buyer_id as buyer_name, b.booking_no, b.bh_qty 
                    from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
                    where a.booking_no=b.booking_no and a.booking_type=4 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
                    $result_sql_order=sql_select($sql_non_order);
                    foreach($result_sql_order as $row)
                    {
                        
                        $non_order_arr[$row[csf('booking_no')]]['buyer_name']=$row[csf('buyer_name')];
                        $non_order_arr[$row[csf('booking_no')]]['bh_qty']=$row[csf('bh_qty')];
                    }
                
                    // echo "<pre>";
                    // print_r($non_order_arr);
                    $load_data=array();
                    $load_time_data=sql_select("select a.id,a.batch_id,a.batch_no,a.load_unload_id,a.process_end_date,a.end_hours,a.end_minutes from pro_fab_subprocess a where a.load_unload_id=1 and a.entry_form=35 and  a.status_active=1  and a.is_deleted=0 $working_company_cond $lc_company_cond");
                    
                    
                    foreach($load_time_data as $row_time)// for Loading time
                    {
                        $load_data[$row_time[csf('batch_id')]]['process_end_date']=$row_time[csf('process_end_date')];
                        $load_data[$row_time[csf('batch_id')]]['end_hours']=$row_time[csf('end_hours')];
                        $load_data[$row_time[csf('batch_id')]]['end_minutes']=$row_time[csf('end_minutes')];
                    }
                */
                
                if($db_type==0)
                {
                    $sql_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, group_concat(c.po_id) as po_id, sum(c.batch_qnty) as qnty
	                from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
	                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.batch_against in(5,7) and b.is_sales!=1  $floor_cond $date_cond $working_company_cond $lc_company_cond    $batch_cond $book_cond 
	                group by b.id,b.extention_no,a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                else if($db_type==2)
                {
                    $sql_dtls="SELECT b.id,b.extention_no,a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, LISTAGG(cast(c.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.po_id) as po_id, sum(c.batch_qnty) as qnty
	                from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c 
	                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against  in(5,7) and b.is_sales!=1  $floor_cond $working_company_cond $lc_company_cond $date_cond $batch_cond $book_cond 
	                group by b.id,b.extention_no, a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                //echo $sql_dtls; die;
                $sql_result=sql_select($sql_dtls);

                if(!empty($sql_result))
                {
                    // ================== getting batch id =========================
                    $batch_id_array = array();
                    foreach ( $sql_result as $row )
                    {
                        $batch_id_array[$row[csf('batch_id')]] = $row[csf('batch_id')];
                        $booking_no_arr=explode("-",$row[csf('booking_no')]);
                        $fb_booking_no=$booking_no_arr[1];
                        //if($fb_booking_no=='Fb') //echo $fb_booking_no.'A';else echo $fb_booking_no.'B';
                        //{
                        $other_batch_wise_array[$row[csf('batch_id')]]['id']= $row[csf('id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['batch_id']= $row[csf('batch_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['extention_no']= $row[csf('extention_no')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['dyeing_company']= $row[csf('dyeing_company')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['service_company']= $row[csf('service_company')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['production_date']= $row[csf('production_date')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['process_end_date']= $row[csf('process_end_date')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['machine_id']= $row[csf('machine_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['end_hours']= $row[csf('end_hours')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['end_minutes']= $row[csf('end_minutes')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['remarks']= $row[csf('remarks')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['batch_no']= $row[csf('batch_no')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['color_id']= $row[csf('color_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['booking_no']= $row[csf('booking_no')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['color_range_id']= $row[csf('color_range_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['dur_req_hr']= $row[csf('dur_req_hr')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['dur_req_min']= $row[csf('dur_req_min')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['prod_id']= $row[csf('prod_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['po_id']= $row[csf('po_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['qnty']= $row[csf('qnty')];
                        //}
                    }

                    $con = connect();
                    $r_id2=execute_query("delete from tmp_batch_or_iss where userid=$user_id and block=4");
                    if($r_id2)
                    {
                        oci_commit($con);
                    }

                    if(!empty($batch_id_array))
                    {
                        foreach ($batch_id_array as $batch_id_key => $batch_id_val) 
                        {
                            $r_id_1_4=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_no, type, block) values ($user_id,'".$batch_id_key."',1,4".")");
                            if($r_id_1_4) 
                            {
                                $r_id_1_4=1;
                            } 
                            else 
                            {
                                echo "insert into tmp_batch_or_iss (userid, batch_issue_no, type, block) values ($user_id,'".$batch_id_key."',1,4".")";
                                oci_rollback($con);
                                die;
                            }
                        }

                        if($r_id_1_4)
                        {
                            oci_commit($con);
                        }
                        else
                        {
                            oci_rollback($con);
                            disconnect($con);
                        }
                    }

                    //============================= getting issue amount ================================
                    $chem_dye_cost_array=array(); //and b.batch_id in($batchIds) 
                    $chem_dye_cost_kg_array=array();

                    $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c, tmp_batch_or_iss d where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 and b.batch_id=d.batch_issue_no and d.userid=$user_id and d.type=1 and d.block=4  $working_company_cond_issue $lc_company_cond_issue2 group by a.mst_id, b.batch_id,a.item_category ";
                    $chem_dye_cost_result=sql_select($chem_dye_cost_sql);

                    $all_issue_id =array();
                    foreach($chem_dye_cost_result as $row)
                    {
                        $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
                        $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
                        $total_batch_qnty=0;
                        foreach($batch_id_arr as $batch_ids)
                        {
                            $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
                            //echo $total_batch_qnty;die;
                            //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
                            
                        }
                        
                        foreach($batch_id_arr as $batch_id)
                        {
                            $chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
                            $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
                            
                        }
                    }

                    // ============================ getting issue return amount =============================
                    if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company in($working_company_id)";

                    if(count($all_issue_id)>0)
                    {
                        foreach ($all_issue_id as $issue_id_key => $issue_id_val) 
                        {
                            $r_id4=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,4".")");
                            if($r_id4) 
                            {
                                $r_id4=1;
                            } 
                            else 
                            {
                                echo "insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,4".")";
                                oci_rollback($con);
                                die;
                            }
                        }

                        if($r_id4)
                        {
                            oci_commit($con);
                        }
                        else
                        {
                            oci_rollback($con);
                            disconnect($con);
                        }

                        $sql_return_cost="SELECT p.batch_no as batch_id,a.item_category, sum(a.cons_amount) as cons_amount  from inv_issue_master p, inv_transaction a, inv_receive_master b, tmp_batch_or_iss c where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1 and a.issue_id=c.batch_issue_id and c.block=4 and c.type=2 and userid= $user_id  group by p.batch_no,a.item_category"; 

                        // echo $sql_return_cost;
                        $cost_return_data_arr=array();
                        $sql_return_res=sql_select($sql_return_cost);
                        foreach ($sql_return_res as $row)
                        {
                            $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
                            // $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
                        }
                    }
                }
                
                
                // print_r($cost_return_data_arr);
				$other_sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.entry_form=35  and b.batch_against  in(5,7) and b.is_sales!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
				//die;
				$other_result_data_recipe=sql_select($other_sql_recipe);
				foreach ($other_result_data_recipe as $row)
				{
					$other_batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
					if($other_tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
					{
					$other_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
					}
					$other_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
				}
				
                $tot_rows=count($sql_result);
                $m=1;  $ul_percent=0; $tot_dye_qnty=0;$total_other_dye_chem_cost=0;
                foreach ( $other_batch_wise_array as $batch_id=>$row )
                {
                    if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $po_id_arr=array_unique(explode(",",$row[('po_id')])); 
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trother_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="trother_<? echo $m; ?>">
                        <td width="40"><? echo $m; ?></td>
                        <td width="110"><p><? echo $row[('booking_no')]; ?></p></td>
                        <td width="90" align="center"><p>
                        <?
                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;
                        foreach($po_id_arr as $po_id)
                        {
                            if(!in_array($order_arr[$po_id]['job_no'],$job_chech))
                            {
                                $job_chech[]=$order_arr[$po_id]['job_no'];
                                if($job_all=="") $job_all=$order_arr[$po_id]['job_no']; else $job_all .=", ".$order_arr[$po_id]['job_no'];
                                if($buyer_all=="") $buyer_all=$buyer_library[$order_arr[$po_id]['buyer_name']]; else $buyer_all .=", ".$buyer_library[$order_arr[$po_id]['buyer_name']];
                                if($style_all=="") $style_all=$order_arr[$po_id]['style_ref_no']; else $style_all .=", ".$order_arr[$po_id]['style_ref_no'];
                            }
                            if($po_all=="") $po_all=$order_arr[$po_id]['po_number']; else $po_all .=", ".$order_arr[$po_id]['po_number'];
                            $po_qnty +=$order_arr[$po_id]['po_quantity'];
                        }
                        echo $job_all; 
						
						$other_sid_ratio_all='';$other_liquor_ratio_all='';

						$sub_process_id=rtrim($other_batch_recipe_arr[$batch_id]['sub_process_id'],',');
						$sub_process_ids=array_unique(explode(",",$sub_process_id));
						
						foreach($sub_process_ids as $sid)
						{
							$liquor_ratio=$other_batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
							if($liquor_ratio>0)
							{
							$seq_no=$other_batch_recipe_subpro_arr[$batch_id][$sid]['seq_no'];
							$other_sid_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.'&#013;';
							
							$other_liquor_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.',';
							}
							
						}
						$other_liquor_ratio_all=rtrim($other_liquor_ratio_all,',');
						$other_liquor_ratio_allArr=array_unique(explode(",",$other_liquor_ratio_all));
						$other_liquor_ratio=$other_liquor_ratio_allArr[0];
						
                        ?></p></td>
                        <td width="120"><p><? echo $buyer_all; echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']]; ?></p></td>
                        <td width="130"><p><? echo $style_all;  ?></p></td>
                        <td width="140"><p><? echo $po_all;?></p></td>
                        <td width="100" align="right"><p><? echo number_format($po_qnty,0);  ?></p></td>
                        <td width="60" align="center"><p><? echo $gsm_library[$row[('prod_id')]]; ?></p></td>
                        <td width="100"><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
                        <td width="100"><p><? echo $row[('batch_no')]; ?></p></td>
                        <td width="50"><p><? echo $row[('extention_no')]; ?></p></td>
                        <td width="100" align="center"><p><? echo $machine_cap_array[$row[('machine_id')]]["machine_no"]; ?></p></td>
                        <td width="90" align="right"><p><? $machine_capacity=$machine_cap_array[$row[('machine_id')]]["prod_capacity"]; echo number_format($machine_capacity,2,'.',''); ?></p></td>
                        <td width="70" align="right"><p><? echo change_date_format($row[('production_date')]); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[('qnty')],2,'.',''); $total_other_dyeing_qnty+=$row[('qnty')]; ?></p></td>
                        <td width="60" align="right"><p><? $ul_percent=$row[('qnty')]/$machine_capacity*100; echo number_format($ul_percent,2,'.',''); ?></p></td>
                        <td width="60" align="center"><p><? $req_hour=""; $req_hour=$row[('dur_req_hr')].":".$row[('dur_req_min')]; echo $req_hour; ?></p></td>
                        <td width="80" align="center"><p><? $start_time=''; $start_time=$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']; echo  $start_time; ?></p></td>
                        <td width="80" align="center"><p><? $end_time=''; $end_time=$row[('end_hours')].':'.$row[('end_minutes')]; echo $end_time; ?></p></td>
                        <td width="100" title="<? echo $other_sid_ratio_all;?>" align="center"><p><? echo $other_liquor_ratio; ?></p></td>
                        <td width="70" align="right"><p><? $start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
                        
                        if($timeDiffin>0)
                        {
                             $time_used=convertMinutes2Hours($timeDiffin); 
                             echo $time_used; // 
                        }
                        else
                        {
                            echo "Invalid";
                        }
                         ?></p></td>
                        <?
                        
                        $req_time=strtotime($row[('dur_req_hr')].":".$row[('dur_req_min')].":" . 00);
                        $hour_dev_cal=0;$tot_deviation="";
                        if($req_time!="")
                        {
                            $used_time=strtotime($time_used.':'. 00);
                            $hour_dev_cal=($used_time-$req_time)/60;
                            $tot_deviation=convertMinutes2Hours($hour_dev_cal); 
                        }
                        if($hour_dev_cal>0)
                        {
                            ?>
                            <td width="60" align="center" bgcolor="#FF0000"><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        else
                        {
                            ?>
                            <td width="60" align="center" ><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        ?>
                        <td width="90"><p><? echo $color_range[$row[('color_range_id')]]; ?></p></td>

                        <td width="110" align="right" title="Batch ID=<? echo $row[('batch_id')];?>"><p><? $other_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$row[('batch_id')]][6]; echo number_format($other_dye_cost,4,'.','');?></p></td>
                        <td width="90" align="right" title="Tot Dye Cost/Dyeing Qty"><p><? 
                        $tot_dye_cost_kg=$other_dye_cost/$row[('qnty')]; echo number_format($tot_dye_cost_kg,4,'.','');
                        //echo number_format(($chem_dye_cost_kg_array[$row[('batch_id')]]-$cost_return_data_arr[$row[('batch_id')]]),2,'.','');
                         $total_other_dye_cost+=$other_dye_cost;$total_other_dye_cost_kg+=$tot_dye_cost_kg;?> </p></td>

                        <td width="110" align="right" title="Batch ID=<? echo $row[('batch_id')];?>"><p><? $other_chem_cost =$chem_dye_cost_array[$row[('batch_id')]][5] - $cost_return_data_arr[$row[('batch_id')]][5]; echo number_format($other_chem_cost,4,'.','');?></p></td>
                        <td width="90" align="right" title="Tot Chemical Cost/Dyeing Qty"><p><? 
                        $tot_chemi_cost_kg=$other_chem_cost/$row[('qnty')]; echo number_format($tot_chemi_cost_kg,4,'.','');
                        //echo number_format(($chem_dye_cost_kg_array[$row[('batch_id')]]-$cost_return_data_arr[$row[('batch_id')]]),2,'.','');
                         $total_other_chem_cost+=$other_chem_cost;$total_other_chem_cost_kg+=$tot_chemi_cost_kg;?> </p></td>

                        <td width="130" align="center"><p><? echo $company_library[$row[('service_company')]]; ?></p></td>
                        <td><p><? echo $row[('remarks')]; ?></p></td>
                    </tr>
                    <?
                    $m++;
                } 
                ?>
                </table>
                <table width="2670px" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0">
                    <tfoot>
                        <th width="40">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="50">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="70"><strong>Total</strong></th>
                        <th width="100" id="value_total_dyeing_qnty" align="right"><? echo number_format($total_other_dyeing_qnty,2,'.',''); ?></th>
                        <th width="60">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="110" id="value_total_dye_cost"><? echo number_format($total_other_dye_cost,2,'.',''); ?></th>
                        <th width="90" align="right"><? echo number_format($total_other_dye_cost/$total_other_dyeing_qnty,2,'.',''); ?></th>
                        <th width="110" id="value_total_chem_cost"><? echo number_format($total_other_chem_cost,2,'.',''); ?></th>
                        <th width="90" align="right"><? echo number_format($total_other_chem_cost/$total_other_dyeing_qnty,2,'.',''); ?></th>
                        <th width="130">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div> 
            <?
        }
        ?>    
    </div>
  
    <?  
    echo "$total_data****requires/$filename****$tot_rows****$type_id";
    exit();
}
?>
