<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_name=$_SESSION['logic_erp']['user_id'];
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
$dealing_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0",'id','team_member_name');
$sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0","id","season_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
	exit();
}

if ($action=="print_button_variable_setting")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=14 and report_id=295 and is_deleted=0 and status_active=1");
    // print_r($print_report_format);die;
    echo "print_report_button_setting('$print_report_format');\n";
}


if($action=="report_generate")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
 	$req_no=str_replace("'", "", $txt_req_no);
    $group=str_replace("'", "", $txt_internal_ref);
    $file=str_replace("'", "", $txt_file_no);
    $order=str_replace("'", "", $txt_order_no);
    $txt_job=str_replace("'", "", $txt_job_no);
    $sample_year=str_replace("'", "", $cbo_year);
    $year_cond="";
    if($db_type==2)
    {
        $year_cond=($sample_year)? " and  to_char(a.insert_date,'YYYY')=$sample_year" : " ";
    }
    else
    {
        $year_cond=($sample_year)? " and year(a.insert_date)=$sample_year" : " ";
    }

 	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_id=$cbo_company_name";
	if(str_replace("'","",$cbo_sample_stage)==0) $sample_stages=""; else $sample_stages=" and a.sample_stage_id=$cbo_sample_stage";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
 	if(str_replace("'","",trim($cbo_dealing_merchant))==0) $dealing_merchant=""; else $dealing_merchant=" and a.dealing_marchant=$cbo_dealing_merchant";
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
	else $txt_date=" and a.requisition_date between $txt_date_from and $txt_date_to";
	if(str_replace("'","",$txt_req_no)=="") $req_no=""; else $req_no=" and a.requisition_number_prefix_num like '%$req_no%' ";
	if(str_replace("'","",$txt_job)=="") $job_no=""; else $job_no=" and a.quotation_id in(select id from wo_po_details_master where job_no like '%$txt_job%' and company_name=$cbo_company_name)";

    if(str_replace("'","",$txt_internal_ref)=="") $internal_ref=""; else $internal_ref=" and a.quotation_id in(select a.id from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.grouping like '%$group%'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name)";

    if(str_replace("'","",$txt_file_no)=="") $file_no=""; else $file_no=" and a.quotation_id in(select a.id from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.file_no like '%$file%'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name)";

    if(str_replace("'","",$txt_order_no)=="") $order_no=""; else $order_no=" and a.quotation_id in(select a.id from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.po_number like '%$order%'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name)";

    $fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
    $toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
    $style=str_replace("'", "", $txt_style_ref);
	if($style=='') $style_ref="";else $style_ref=" and a.style_ref_no like '%$style%'";

    $file_sql = sql_select("select master_tble_id, form_name, image_location from common_photo_library where file_type=2 and is_deleted=0 and form_name='sample_requisition_1'");

    $file_arr= array();

    foreach($file_sql as $file)
    {
        if($file_arr[$file[csf('master_tble_id')]][$file[csf('form_name')]]=='')
        {
            $file_arr[$file[csf('master_tble_id')]][$file[csf('form_name')]] = $file[csf('image_location')];
        }
        else
        {
            $file_arr[$file[csf('master_tble_id')]][$file[csf('form_name')]] .= "**".$file[csf('image_location')];
        }
    }

	$cutting_reject_arr=array(); $emb_reject_arr=array(); $sew_reject_arr=array(); $wash_dyeing_reject_arr=array();
	$cutting_qty_arr=array(); $embellish_issue_arr=array(); $embellish_arr=array(); $sewingIn_arr=array(); $sewing_arr=array(); $dyeing_arr=array(); $wash_arr=array();
	$cutting_date_arr=array(); $sewing_in_date_arr=array(); $sewing_out_date_arr=array();
    $prod_sql=sql_select("SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name, b.sewing_date, b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty as reject from sample_sewing_output_mst a, sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in (127,128,130,131,337,338) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	//echo "SELECT a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name, b.sewing_date, b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty as reject from sample_sewing_output_mst a, sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in (127,128,130,131,337,338) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    foreach($prod_sql as $val)
    {
        if($val[csf("entry_form_id")]==127)
		{
			$cutting_reject_arr[$val[csf("smp_dev_id")]][$val[csf("sample_dtls_row_id")]]+=$val[csf("reject")];
			$cutting_qty_arr[$val[csf('company_id')]][$val[csf("smp_dev_id")]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
			$cutting_date_arr[$val[csf("smp_dev_id")]][$val[csf("sample_dtls_row_id")]]=$val[csf("sewing_date")];
		}
		if($val[csf("entry_form_id")]==128)
		{
			$emb_reject_arr[$val[csf("sample_dtls_row_id")]]+=$val[csf("reject")];
			$embellish_arr[$val[csf('company_id')]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
		}
		if($val[csf("entry_form_id")]==130)
		{
			$sew_reject_arr[$val[csf("sample_dtls_row_id")]]+=$val[csf("reject")];
			$sewing_arr[$val[csf('company_id')]][$val[csf('smp_dev_id')]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
            $sewing_out_date_arr[$val[csf("sample_dtls_row_id")]]=$val[csf("sewing_date")];
		}
		if($val[csf("entry_form_id")]==131)
		{
			$wash_dyeing_reject_arr[$val[csf("sample_dtls_row_id")]]+=$val[csf("reject")];
			if($val[csf("embel_name")]==5) $dyeing_arr[$val[csf('company_id')]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
			if($val[csf("embel_name")]==3) $wash_arr[$val[csf('company_id')]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
		}
		if($val[csf("entry_form_id")]==337)
		{
			$sewingIn_arr[$val[csf('company_id')]][$val[csf('smp_dev_id')]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
            $sewing_in_date_arr[$val[csf("sample_dtls_row_id")]]=$val[csf("sewing_date")];
		}
		if($val[csf("entry_form_id")]==338)
		{
			$embellish_issue_arr[$val[csf('company_id')]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
		}
    }
    // echo "<pre>";print_r($wash_dyeing_reject_arr);die;
    $prod_complete_sql=sql_select("SELECT sample_dtls_part_tbl_id,sample_name,gmts_item_id,max(delivery_date) as delv_date from  sample_ex_factory_dtls where entry_form_id=132 and status_active=1 and is_deleted=0 group by sample_dtls_part_tbl_id,sample_name,gmts_item_id");
    foreach($prod_complete_sql as $results)
    {
        $prod_complete_arr[$results[csf('sample_dtls_part_tbl_id')]][$results[csf('sample_name')]][$results[csf('gmts_item_id')]]=change_date_format($results[csf('delv_date')]);
    }

	$delv_qty_arr=array(); $delivery_date_arr=array();
	$delv_qty_sql=sql_select("SELECT a.company_id, a.ex_factory_date, a.delivery_to, b.sample_development_id, b.sample_dtls_part_tbl_id, b.sample_name, b.gmts_item_id, (b.ex_factory_qty) as qc_pass_qty from sample_ex_factory_mst a, sample_ex_factory_dtls b
	where a.id=b.sample_ex_factory_mst_id and  a.entry_form_id=132 and b.entry_form_id=132 and a.status_active=1 and b.status_active=1 ");
	foreach ($delv_qty_sql as  $result)
	{
	   $delv_qty_arr[$result[csf('company_id')]][$result[csf('sample_development_id')]][$result[csf('sample_name')]][$result[csf('gmts_item_id')]]=$result[csf('qc_pass_qty')];

	   $delivery_date_arr[$result[csf("sample_development_id")]]["date"]=$result[csf("ex_factory_date")];
       $delivery_date_arr[$result[csf("sample_development_id")]]["delivery"]=$result[csf("delivery_to")];
	}

	if($db_type==0)
	{
		$po_sql=sql_select("SELECT a.id, group_concat(b.po_number) as po_number from wo_po_details_master a ,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id");
	}
	else
	{
		$po_sql=sql_select("SELECT a.id, listagg(CAST(b.po_number as VARCHAR(4000)),',') within group (order by b.po_number) as po_number from wo_po_details_master a ,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id");
    }

    foreach($po_sql as $po_value)
    {
        $po_array[$po_value[csf("id")]]=$po_value[csf("po_number")];
    }
    $booking_sql=sql_select("SELECT a.id,b.booking_no from wo_po_details_master a,wo_booking_mst b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
    foreach($booking_sql as $booking_value)
    {
        $booking_arr[$booking_value[csf("id")]]=$booking_value[csf("booking_no")];
    }

    $booking_without_order_sql=sql_select("SELECT b.style_id,a.booking_no from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1  group by  b.style_id,a.booking_no");
    foreach($booking_without_order_sql as $vals)
    {
        $booking_without_order_arr[$vals[csf("style_id")]]=$vals[csf("booking_no")];
    }

	$checklist_status_sql=sql_select("SELECT requisition_id ,completion_status from sample_checklist_mst where status_active=1 and is_deleted=0");
	foreach($checklist_status_sql as $checklist_status_value)
	{
		$checklist_status_arr[$checklist_status_value[csf("requisition_id")]]=$checklist_status_value[csf("completion_status")];
	}

	$before_order_app_arr=array(); $sub_buyer_date_arr=array();
	$before_order_app=sql_select("SELECT sample_dtls_id, approval_status, max(submitted_to_buyer) as dt from wo_po_sample_approval_info where entry_form_id=137 and status_active=1 and is_deleted=0 group by sample_dtls_id, approval_status order by id asc");
	foreach($before_order_app as $vals)
	{
		$before_order_app_arr[$vals[csf("sample_dtls_id")]]=$vals[csf("approval_status")];
		$sub_buyer_date_arr[$vals[csf("sample_dtls_id")]]=$vals[csf("dt")];
	}
    ob_start();
    ?>

    <div>
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="4595" rules="all" id="table_header" >
            <thead>
                <tr>
                    <th style="word-break:break-all" width="30">SL </th>
                    <th style="word-break:break-all" width="95">Requisition No</th>
                    <th style="word-break:break-all" width="95">Req. Date</th>
                    <th style="word-break:break-all" width="45">Year</th>
                    <th style="word-break:break-all" width="110">Dealing Merchant</th>
                    <th style="word-break:break-all" width="100">BH Merchant</th>
                    <th style="word-break:break-all" width="100">Buyer</th>
                    <th style="word-break:break-all" width="110">Sample Stage</th>
                    <th style="word-break:break-all" width="90">Style Ref.</th>
                    <th style="word-break:break-all" width="110">Sample Name</th>
                    <th style="word-break:break-all" width="110">Garments Item</th>
                    <th style="word-break:break-all" width="60">Color</th>
                    <th style="word-break:break-all" width="40">SMV</th>
                    <th style="word-break:break-all" width="70">Season</th>
                    <th style="word-break:break-all" width="100">File</th>
                    <th style="word-break:break-all" width="90">Booking No</th>
                    <th style="word-break:break-all" width="105">Req. Approval</th>
                    <th style="word-break:break-all" width="115">Req. Acknowledge</th>
                    <th style="word-break:break-all" width="90">Req. Acknowledge Date</th>
                    <th style="word-break:break-all" width="100">Order No</th>
                    <th style="word-break:break-all" width="95">Ready To Prod.</th>
                    <th style="word-break:break-all" width="105">Sample Req. Qty</th>
                    <th style="word-break:break-all" width="105">Tgt. Delv. Date</th>
                    <th style="word-break:break-all" width="90">Confirm Delv End Date</th>
                    <th style="word-break:break-all" width="90">Pattern Date</th>
                    <th style="word-break:break-all" width="90">Cutting Date</th>
                    <th style="word-break:break-all" width="90">Sewing Date</th>
                    <th style="word-break:break-all" width="90">Wash Send Date</th>
                    <th style="word-break:break-all" width="90">Wash Rec. Date</th>
                    <th style="word-break:break-all" width="90">Finish Date</th>
                    <th style="word-break:break-all" width="80">Cutting Qty</th>
                    <th style="word-break:break-all" width="70">Embl. Issue Qty</th>
                    <th style="word-break:break-all" width="70">Embl. Receive Qty</th>
                    <th style="word-break:break-all" width="80">Input Date(Last)</th>
                    <th style="word-break:break-all" width="80">Sewing Input Qty</th>
                    <th style="word-break:break-all" width="80">Output Date(Last)</th>
                    <th style="word-break:break-all" width="80">Sewing Output Qty</th>
                    <th style="word-break:break-all" width="80">Dyeing Qty</th>
                    <th style="word-break:break-all" width="70">Wash Qty</th>
                    <th style="word-break:break-all" width="80">Reject Qty</th>
                    <th style="word-break:break-all" width="70">Delv. Qty</th>
                    <th style="word-break:break-all" width="85">Delv. Balance</th>
                    <th style="word-break:break-all" width="80">Cutting Date</th>
                    <th style="word-break:break-all" width="125">Allowed Lead Time</th>
                    <th style="word-break:break-all" width="105">Actual Lead Time</th>
                    <th style="word-break:break-all" width="90">Sub To Buyer</th>
                    <th style="word-break:break-all" width="105">Approval Status</th>
                    <th style="word-break:break-all" width="95">Charge/Unit</th>
                    <th style="word-break:break-all" width="95">Amount</th>
                    <th style="word-break:break-all" width="80">Currency</th>
                    <th style="word-break:break-all">Comments</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:320px; width:4595px;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="4595" rules="all" id="table_body">
            <tbody>
            <?
             $sample_req_ackg=sql_select("SELECT distinct(a.id), a.entry_form_id,
             a.buyer_name, a.season, a.product_dept, b.insert_date as req_acknowledge_date, b.confirm_del_end_date
             from sample_development_mst a,sample_requisition_acknowledge b
             where a.entry_form_id in (117,203,449) $company_name $txt_date $buyer_name $sample_stages $req_no and a.status_active=1 and a.is_deleted=0 and a.id=b.sample_mst_id order by a.id desc ");
            //  echo "<pre>"; print_r($sample_req_ackg); die;
             foreach($sample_req_ackg as $val){
                 $req_ackg_arr[$val[csf('id')]]['req_acknowledge_date']=$val[csf('req_acknowledge_date')];
                 $req_ackg_arr[$val[csf('id')]]['confirm_del_end_date']=$val[csf('confirm_del_end_date')];
             }
            if($db_type==0) $yearCond="YEAR(a.insert_date)"; else if($db_type==2) $yearCond="to_char(a.insert_date,'YYYY')";

            $query="SELECT a.id, a.requisition_date, a.quotation_id, a.company_id, a.is_approved, a.is_acknowledge, a.dealing_marchant, a.requisition_number_prefix_num, $yearCond as year, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.id as dtls_id, b.sample_name, b.gmts_item_id, b.sample_color, b.smv, b.sample_charge, b.sample_curency, b.sample_prod_qty, b.delv_start_date, b.delv_end_date, b.is_complete_prod, b.entry_form_id, a.refusing_cause,c.confirm_del_end_date, c.insert_date as req_acknowledge_date, a.season_buyer_wise, c.sample_plan
            from sample_development_mst a,sample_development_dtls b, sample_requisition_acknowledge c
            where a.id=b.sample_mst_id and a.id = c.sample_mst_id and a.entry_form_id in(117,203,449) and b.entry_form_id in(117,203,449) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $txt_date $company_name $buyer_name $sample_stages $dealing_merchant $req_no $job_no $internal_ref $style_ref $file_no $order_no $year_cond and c.acknowledge_status=1 order by a.requisition_number_prefix_num";

            $sql=sql_select($query);
            $i=1;
            // echo "<pre>"; print_r($sql); die;
            foreach ($sql as $key => $value)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                $seasonName="";
                                
                if($value[csf('season')]!="") $seasonName=$season_arr[$value[csf('season')]];
                else $seasonName=$season_arr[$value[csf('season_buyer_wise')]];

                $entry_form_id=$value[csf('entry_form_id')] ;
                $link_format=""; $buttonAction="";
                $page_path='';

                $image_path = $file_arr[$value[csf('id')]]['sample_requisition_1'];

                $image_path = explode("**",$image_path);
                $sample_date=explode('----',$value[csf("sample_plan")]);
                $req_array=array();
                foreach($sample_date as $sample){
                    list($sapmple_plan,$pat_date,$cut_date,$swin_date,$wash_send_date,$wash_rec_date,$finish_date)=explode("**",$sample);
                    $req_array[$sapmple_plan]['pat_date']=$pat_date;
                    $req_array[$sapmple_plan]['cut_date']=$cut_date;
                    $req_array[$sapmple_plan]['swin_date']=$swin_date;
                    $req_array[$sapmple_plan]['wash_send_date']=$wash_send_date;
                    $req_array[$sapmple_plan]['wash_rec_date']=$wash_rec_date;
                    $req_array[$sapmple_plan]['finish_date']=$finish_date;
                }
                // echo "<pre>"; print_r($req_array); die;
                 //$all_date=$value[csf("sample_plan")];
                  //echo $all_date;
                

                //  echo $pat_date."##".$cut_date."##".$swin_date."##".$wash_send_date."##".$wash_rec_date."##".$finish_date;


                $image_location = '';
                foreach($image_path as $image){
                    if($image!='')
                    {
                        $image_location .="<a href='../".$image."'>File</a>&nbsp;";
                    }
                }

                if($image_location!='')
                {
                    $update_id = $value[csf('id')];
                    $image_location = "<a href='##' onclick='view_file(".$update_id.")'>VIEW</a>";
                }
                if($value[csf('sample_stage_id')]==1) $booking_no= $booking_arr[$value[csf("quotation_id")]];
                            else $booking_no= $booking_without_order_arr[$value[csf("id")]];

                if($entry_form_id==117) 
                {
                    $link_format="'../order/woven_order/requires/sample_requisition_controller'";
                    $buttonAction="sample_requisition_print";
                    $page_path=0;
                }
                else if($entry_form_id==203) 
                {
                    $link_format="'../order/woven_order/requires/sample_requisition_with_booking_controller'";
                    $buttonAction="sample_requisition_print";
                    $page_path=0;
                }
                else if($entry_form_id==449) 
                {
                    $link_format="'../order/woven_gmts/requires/sample_requisition_with_booking_controller'";
                    $buttonAction="sample_requisition_print1";
                    $page_path="'".$booking_no."'+'*'+1+'*'+0";
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="95" style="word-break:break-all" align="center"><a href='##' onClick="print_report(<? echo $value[csf('company_id')]; ?>+'*'+<? echo $value[csf('id')]; ?>+'*'+<? echo $page_path; ?>,'<?=$buttonAction;?>', <?=$link_format;?>)"><? echo $value[csf('requisition_number_prefix_num')]; ?></a></td>
                    <td width="95" style="word-break:break-all" align="center"><? echo  change_date_format($value[csf('requisition_date')]); ?></td>
                    <td width="45" style="word-break:break-all" align="center"><? echo  $value[csf('year')] ; ?></td>
                    <td width="110" style="word-break:break-all"><? echo $dealing_arr[$value[csf('dealing_marchant')]]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo  $value[csf('bh_merchant')] ; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_arr[$value[csf('buyer_name')]]; ?></td>
                    <td width="110" align="center" style="word-break:break-all"><? echo $sample_stage[$value[csf('sample_stage_id')]]; ?></td>
                    <td width="90" style="word-break:break-all"><? echo  $value[csf('style_ref_no')] ; ?></td>
                    <td width="110" style="word-break:break-all"><? echo $sample_name_arr[$value[csf('sample_name')]]; ?></td>
                    <td width="110" style="word-break:break-all"><? echo $garments_item[$value[csf('gmts_item_id')]]; ?></td>
                    <td width="60" style="word-break:break-all"><? echo $color_arr[$value[csf('sample_color')]]; ?></td>
                    <td width="40" align="center"><? echo fn_number_format($value[csf('smv')],2); ?></td>
                    <td width="70" style="word-break:break-all"><? echo $seasonName; ?></td>
                    <td width="100" align="center" style="word-break:break-all"><? echo $image_location; ?></td>
                    <td width="90" style="word-break:break-all">
						<?
                            if($value[csf('sample_stage_id')]==1) echo $booking_arr[$value[csf("quotation_id")]];
                            else echo $booking_without_order_arr[$value[csf("id")]];
                        ?>
                    </td>
                    <td width="105" style="word-break:break-all"><? if($value[csf('is_approved')]==1){echo "YES";} else{echo "NO";} ?></td>
                    <td width="115" style="word-break:break-all"><? $cause=$value[csf("refusing_cause")]; if($value[csf('is_acknowledge')]==1){echo "YES";} else if($cause!=""){$req_id=$value[csf("id")]; echo "<p><a style='color:crimson;font-size:14px;' href='##'  onclick=\"openmypage_refusing_cause( '$req_id' ,'refusing_popup');\" >Refused </a></p>";} else { echo "NO";} ?></td>
                    <td width="90" style="word-break:break-all" title="Date Time=<?=$req_ackg_arr[$value[csf('id')]]['req_acknowledge_date'];?>" align="center"><?=change_date_format($req_ackg_arr[$value[csf('id')]]['req_acknowledge_date']);?></td>
                    <td width="100" style="word-break:break-all"><a href="##"  onclick="openmypage_order_qty(<? echo $value[csf("quotation_id")];?>,'order_qty_popup');" ><? if($value[csf("sample_stage_id")]==1){ echo  $po_array[$value[csf('quotation_id')]] ; } else {echo "";}?></a></td>
                    <td width="95" style="word-break:break-all"><? $req_id=$value[csf("id")]; if($checklist_status_arr[$value[csf('id')]]==1){echo "<p><a href='##'  onclick=\"openmypage_checklist( '$req_id' ,'checklist_popup','YES');\" >YES </a></p>";}
                    else{ echo "<p><a href='##'  onclick=\"openmypage_checklist( '$req_id' ,'checklist_popup','NO');\" >NO </a></p>";} ?></td>
                    <td width="105" style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_prod_qty(<? echo $value[csf("dtls_id")];?>,'prod_qty_popup');" ><? echo $value[csf('sample_prod_qty')] ;$tot_sample_qnty+=$value[csf('sample_prod_qty')]; ?></a></td>
                    <td width="105" style="word-break:break-all" align="center"><? echo change_date_format($value[csf('delv_end_date')]);  ?></td>
                    <td width="90" style="word-break:break-all" align="center"><?=change_date_format($req_ackg_arr[$value[csf('id')]]['confirm_del_end_date']);?></td>                   
                    <td width="90" style="word-break:break-all" align="center"><?=change_date_format($req_array[$value[csf('sample_name')]]['pat_date']);?></td>
                    <td width="90" style="word-break:break-all" align="center"><?=change_date_format($req_array[$value[csf('sample_name')]]['cut_date']);?></td>
                    <td width="90" style="word-break:break-all" align="center"><?=change_date_format($req_array[$value[csf('sample_name')]]['swin_date']);?></td>
                    <td width="90" style="word-break:break-all" align="center"><?=change_date_format($req_array[$value[csf('sample_name')]]['wash_send_date']);?></td>
                    <td width="90" style="word-break:break-all" align="center"><?=change_date_format($req_array[$value[csf('sample_name')]]['wash_rec_date']);?></td>
                    <td width="90" style="word-break:break-all" align="center"><?=change_date_format($req_array[$value[csf('sample_name')]]['finish_date']);?></td>
                    <td width="80"style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_cutting_qty(<? echo $value[csf("dtls_id")];?>,'cutting_qty_popup');" ><? echo $cutting_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]  ;$tot_cutting_qnty+=$cutting_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?> </a></td>

                    <td width="70" style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_embellishment_qty(<? echo $value[csf("dtls_id")]; ?>,'338','embellishment_qty_popup');" ><? echo $embellish_issue_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];$tot_emb_qnty+=$embellish_issue_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?></a></td>
                    <td width="70" style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_embellishment_qty(<? echo $value[csf("dtls_id")];?>,'128','embellishment_qty_popup');" ><? echo $embellish_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];$tot_embl_qnty+=$embellish_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?></a></td>

                    <td width="80" style="word-break:break-all" align="center"><? echo change_date_format($sewing_in_date_arr[$value[csf('dtls_id')]]) ; ?></td>

                    <td width="80" style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_sewing_qty(<? echo $value[csf("dtls_id")];?>,'337','sewing_qty_popup',<? echo $value[csf("id")];?>);" ><? echo $sewingIn_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];$tot_sewing_input+=$sewingIn_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?></a></td>

                    <td width="80" style="word-break:break-all" align="center"><? echo change_date_format($sewing_out_date_arr[$value[csf('dtls_id')]]) ; ?></td>

                    <td width="80" style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_sewing_qty(<? echo $value[csf("dtls_id")];?>,'130','sewing_qty_popup',<? echo $value[csf("id")];?>);" ><? echo $sewing_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];$tot_sewing_output+=$sewing_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?> </a></td>
                    <td width="80" style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_dyeing_qty(<? echo $value[csf("dtls_id")];?>,'dyeing_qty_popup');" ><? echo $dyeing_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];$tot_dying_qnty+=$dyeing_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?></a></td>
                    <td width="70" style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_wash_qty(<? echo $value[csf("dtls_id")];?>,'wash_qty_popup');" ><? echo $wash_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];$tot_wash_qnty+=$wash_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?></a></td>
                    <td width="80" style="word-break:break-all" align="center"><a href="##"  onclick="openmypage_reject_qty(<? echo $value[csf("dtls_id")];?>,'reject_qty_view');" ><? echo $cutting_reject_arr[$value[csf('id')]][$value[csf("dtls_id")]] + $emb_reject_arr[$value[csf("dtls_id")]]+$sew_reject_arr[$value[csf("dtls_id")]] + $wash_dyeing_reject_arr[$value[csf("dtls_id")]];$tot_reject_qnty+=$cutting_reject_arr[$value[csf('id')]][$value[csf("dtls_id")]] + $emb_reject_arr[$value[csf("dtls_id")]]+$sew_reject_arr[$value[csf("dtls_id")]] + $wash_dyeing_reject_arr[$value[csf("dtls_id")]];
                    ?></a></td>
                    <td width="70" style="word-break:break-all" align="right"><? echo $delv_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]  ; ?>
                    </td>
                    <td width="85" style="word-break:break-all" align="right"><? echo $value[csf('sample_prod_qty')] -  $delv_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]] ;$tot_delv_balance+=$value[csf('sample_prod_qty')] -  $delv_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]] ?></td>
                    <td width="80" style="word-break:break-all" align="center"><? echo  change_date_format($cutting_date_arr[$value[csf('id')]][$value[csf('dtls_id')]]) ; ?></td>
                    <td width="125" style="word-break:break-all" align="right"><?
						if($value[csf("is_complete_prod")]!="" || $value[csf("is_complete_prod")]!=0 )
						{
							$dates1=$value[csf('requisition_date')]; $dates2=$value[csf('delv_end_date')]; $time=trim(datediff('n',$dates1,$dates2));

							echo $days_allowed= floor(($time/60)/24);
						}
                    	?>
                    </td>
                    <td width="105" style="word-break:break-all" align="right">
						<?
                        if($value[csf("is_complete_prod")]!="" || $value[csf("is_complete_prod")]!=0 )
                        {
                            $date1=$value[csf('requisition_date')];
                            $date2=$prod_complete_arr[$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];
                            $time2=trim(datediff('n',$date1,$date2));
                            $days_actual=floor(($time2/60)/24);
                            $diff=date_diff($date2,$date1);
                            if($days_allowed < $days_actual) echo "<p style='color:crimson;'>$days_actual days</p> "; else echo $days_actual." days";
                        }
                        ?>
                    </td>
                    <td width="90" style="word-break:break-all" align="center"><? echo change_date_format($sub_buyer_date_arr[$value[csf("dtls_id")]]); ?></td>
                    <td width="105" style="word-break:break-all"><a href="##"  onclick="openmypage_buyer_approval(<? echo $value[csf("dtls_id")];?>,'buyer_approval_popup');" ><? echo $approval_status[$before_order_app_arr[$value[csf("dtls_id")]]]; ?></a></td>
                    <td width="95" style="word-break:break-all" align="right"><? echo  $value[csf('sample_charge')] ; ?></td>
                    <td width="95" style="word-break:break-all" align="right"><? $amount=$sewing_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]*$value[csf('sample_charge')];echo $amount;$total_amount+=$amount; ?></td>
                    <td width="80" style="word-break:break-all"><? echo  $currency[$value[csf('sample_curency')]] ; ?></td>
                    <td  style="word-break:break-all" align="center"><a href="##"  onclick="openmypage_remark(<? echo $value[csf("dtls_id")];?>,'comments_view',<? echo $value[csf("id")];?>);" > View </a></td>
                </tr>
                <?
                $i++;
            }
            ?>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="4595px" class="rpt_table">
        <tr>
                    <td width="30" align="center">&nbsp;</td>
                    <td width="95" align="center">&nbsp;</td>
                    <td width="95" align="center">&nbsp;</td>
                    <td width="45" align="center">&nbsp;</td>
                    <td width="110" style="word-break:break-all">&nbsp;</td>
                    <td width="100" style="word-break:break-all">&nbsp;</td>
                    <td width="100" style="word-break:break-all">&nbsp;</td>
                    <td width="110" align="center" style="word-break:break-all">&nbsp;</td>
                    <td width="90" style="word-break:break-all">&nbsp;</td>
                    <td width="110" style="word-break:break-all">&nbsp;</td>
                    <td width="110" style="word-break:break-all">&nbsp;</td>
                    <td width="60" style="word-break:break-all">&nbsp;</td>
                    <td width="40" align="center">&nbsp;</td>
                    <td width="70" style="word-break:break-all">&nbsp;</td>
                    <td width="100" style="word-break:break-all">&nbsp;</td>
                    <td width="90" style="word-break:break-all">&nbsp;</td>
                    <td width="105">&nbsp;</td>
                    <td width="115">&nbsp;</td>
                    <td width="90">&nbsp;</td>
                    <td width="100" style="word-break:break-all">&nbsp; </td>
                    <td width="95"><b>Total</b></td>
                    <td width="105" align="right"> <? echo  $tot_sample_qnty?></td>
                    <td width="105">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="80" align="right"><? echo $tot_cutting_qnty?></td>

                    <td width="70" align="right"><? echo $tot_emb_qnty?></td>
                    <td width="70" align="right"><? echo $tot_embl_qnty?></td>

                    <td width="80" align="center"></td>

                    <td width="80" align="right"><? echo  $tot_sewing_input ?></td>

                    <td width="80" align="center"></td>

                    <td width="80" align="right"><? echo  $tot_sewing_output ?></td>
                    <td width="80" align="right"><? echo  $tot_dying_qnty ?></td>
                    <td width="70" align="right"><? echo  $tot_wash_qnty ?></td>
                    <td width="80" align="center"><? echo  $tot_reject_qnty ?></td>
                    <td width="70" align="right"><? echo  $tot_delv_qnty ?></td>
                    <td width="85" align="right"><? echo  $tot_delv_balance ?></td>
                    <td width="80">&nbsp;</td>
                    <td width="125" align="right">&nbsp;</td>
                    <td width="105" align="right">&nbsp;</td>
                    <td width="90">&nbsp;</td>
                    <td width="105">&nbsp;</td>
                    <td width="95" align="right">&nbsp;</td>
                    <td width="95" align="right"><? echo $total_amount ?>&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td  align="center">&nbsp;</td>
                </tr>
			</table>
        </div>
        
    </div>
	<?
	exit();
}
if($action=="report_generate3")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
 	$req_no=str_replace("'", "", $txt_req_no);
    $group=str_replace("'", "", $txt_internal_ref);
    $file=str_replace("'", "", $txt_file_no);
    $order=str_replace("'", "", $txt_order_no);
    $txt_job=str_replace("'", "", $txt_job_no);
    $sample_year=str_replace("'", "", $cbo_year);
    $year_cond="";
    if($db_type==2)
    {
        $year_cond=($sample_year)? " and  to_char(a.insert_date,'YYYY')=$sample_year" : " ";
    }
    else
    {
        $year_cond=($sample_year)? " and year(a.insert_date)=$sample_year" : " ";
    }

 	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_id=$cbo_company_name";
	if(str_replace("'","",$cbo_sample_stage)==0) $sample_stages=""; else $sample_stages=" and a.sample_stage_id=$cbo_sample_stage";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
 	if(str_replace("'","",trim($cbo_dealing_merchant))==0) $dealing_merchant=""; else $dealing_merchant=" and a.dealing_marchant=$cbo_dealing_merchant";
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
	else $txt_date=" and a.requisition_date between $txt_date_from and $txt_date_to";
	if(str_replace("'","",$txt_req_no)=="") $req_no=""; else $req_no=" and a.requisition_number_prefix_num like '%$req_no%' ";
	if(str_replace("'","",$txt_job)=="") $job_no=""; else $job_no=" and a.quotation_id in(select id from wo_po_details_master where job_no like '%$txt_job%' and company_name=$cbo_company_name)";

    if(str_replace("'","",$txt_internal_ref)=="") $internal_ref=""; else $internal_ref=" and a.quotation_id in(select a.id from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.grouping like '%$group%'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name)";

    if(str_replace("'","",$txt_file_no)=="") $file_no=""; else $file_no=" and a.quotation_id in(select a.id from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.file_no like '%$file%'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name)";

    if(str_replace("'","",$txt_order_no)=="") $order_no=""; else $order_no=" and a.quotation_id in(select a.id from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and  b.po_number like '%$order%'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$cbo_company_name)";

    $fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
    $toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
    $style=str_replace("'", "", $txt_style_ref);
	if($style=='') $style_ref="";else $style_ref=" and a.style_ref_no like '%$style%'";

    $file_sql = sql_select("select master_tble_id, form_name, image_location from common_photo_library where file_type=2 and is_deleted=0 and form_name='sample_requisition_1'");

    $file_arr= array();

    foreach($file_sql as $file)
    {
        if($file_arr[$file[csf('master_tble_id')]][$file[csf('form_name')]]=='')
        {
            $file_arr[$file[csf('master_tble_id')]][$file[csf('form_name')]] = $file[csf('image_location')];
        }
        else
        {
            $file_arr[$file[csf('master_tble_id')]][$file[csf('form_name')]] .= "**".$file[csf('image_location')];
        }
    }

	$cutting_reject_arr=array(); $emb_reject_arr=array(); $sew_reject_arr=array(); $wash_dyeing_reject_arr=array();
	$cutting_qty_arr=array(); $embellish_issue_arr=array(); $embellish_arr=array(); $sewingIn_arr=array(); $sewing_arr=array(); $dyeing_arr=array(); $wash_arr=array();
	$cutting_date_arr=array(); $sewing_in_date_arr=array(); $sewing_out_date_arr=array();
    $prod_sql=sql_select("SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name, b.sewing_date, b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty as reject from sample_sewing_output_mst a, sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in (127,128,130,131,337,338) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	//echo "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name, b.sewing_date, b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty as reject from sample_sewing_output_mst a, sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in (127,128,130,131,337,338) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
    foreach($prod_sql as $val)
    {
        if($val[csf("entry_form_id")]==127)//Sample Requisition Cutting
		{
			$cutting_reject_arr[$val[csf("smp_dev_id")]][$val[csf("sample_dtls_row_id")]]+=$val[csf("reject")];
			$cutting_qty_arr[$val[csf('company_id')]][$val[csf("smp_dev_id")]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
			$cutting_date_arr[$val[csf("smp_dev_id")]][$val[csf("sample_dtls_row_id")]]=$val[csf("sewing_date")];
		}
		if($val[csf("entry_form_id")]==128)//Sample Embellishment Entry
		{
			$emb_reject_arr[$val[csf("sample_dtls_row_id")]]+=$val[csf("reject")];
			$embellish_arr[$val[csf('company_id')]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
		}
		if($val[csf("entry_form_id")]==130)//Sample Requisition Sewing Output
		{
			$sew_reject_arr[$val[csf("sample_dtls_row_id")]]+=$val[csf("reject")];
			$sewing_arr[$val[csf('company_id')]][$val[csf('smp_dev_id')]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
            $sewing_out_date_arr[$val[csf("sample_dtls_row_id")]]=$val[csf("sewing_date")];
		}
		if($val[csf("entry_form_id")]==131)//Sample Wash Or Dyeing
		{
			$wash_dyeing_reject_arr[$val[csf("sample_dtls_row_id")]]+=$val[csf("reject")];
			if($val[csf("embel_name")]==5) $dyeing_arr[$val[csf('company_id')]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
			if($val[csf("embel_name")]==3) $wash_arr[$val[csf('company_id')]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
		}
		if($val[csf("entry_form_id")]==337)//Sample Sewing Input
		{
			$sewingIn_arr[$val[csf('company_id')]][$val[csf('smp_dev_id')]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
            $sewing_in_date_arr[$val[csf("sample_dtls_row_id")]]=$val[csf("sewing_date")];
		}
		if($val[csf("entry_form_id")]==338)//Sample Embellishment Issue
		{
			$embellish_issue_arr[$val[csf('company_id')]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
		}
        if($val[csf("entry_form_id")]==396)//Sample Delivery To MKT
		{
			$delivery_mkt_qty_arr[$val[csf('company_id')]][$val[csf('sample_dtls_row_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
		}
    }
     //echo "<pre>";print_r($cutting_reject_arr);die;//132-Sample Delivery Entry
    $prod_complete_sql=sql_select("SELECT sample_dtls_part_tbl_id,sample_name,gmts_item_id,max(delivery_date) as delv_date from  sample_ex_factory_dtls where entry_form_id=132 and status_active=1 and is_deleted=0 group by sample_dtls_part_tbl_id,sample_name,gmts_item_id");
    foreach($prod_complete_sql as $results)
    {
        $prod_complete_arr[$results[csf('sample_dtls_part_tbl_id')]][$results[csf('sample_name')]][$results[csf('gmts_item_id')]]=change_date_format($results[csf('delv_date')]);
    }
    //132-Sample Delivery Entry, 136-Sample Delivery To MKT
	$delv_qty_arr=array(); $delivery_date_arr=array(); $delv_mkt_qty_arr=array();
	$delv_qty_sql=sql_select("SELECT a.company_id, a.ex_factory_date, a.delivery_to, b.sample_development_id, b.sample_dtls_part_tbl_id, b.sample_name, b.gmts_item_id, sum(b.ex_factory_qty) as qc_pass_qty from sample_ex_factory_mst a, sample_ex_factory_dtls b
	where a.id=b.sample_ex_factory_mst_id and  a.entry_form_id=132 and b.entry_form_id =132 and a.status_active=1 and b.status_active=1 group by  a.company_id, a.ex_factory_date, a.delivery_to, b.sample_development_id, b.sample_dtls_part_tbl_id, b.sample_name, b.gmts_item_id ");
	foreach ($delv_qty_sql as  $result)
	{
	   $delv_qty_arr[$result[csf('company_id')]][$result[csf('sample_development_id')]][$result[csf('sample_name')]][$result[csf('gmts_item_id')]]+=$result[csf('qc_pass_qty')];
	   $delivery_date_arr[$result[csf("sample_development_id")]]["date"]=$result[csf("ex_factory_date")];
       $delivery_date_arr[$result[csf("sample_development_id")]]["delivery"]=$result[csf("delivery_to")];
	}
    $delv_mkt_qty_sql=sql_select("SELECT a.company_id, a.ex_factory_date, a.delivery_to, b.sample_development_id, b.sample_dtls_part_tbl_id, b.sample_name, b.gmts_item_id, sum(b.ex_factory_qty) as del_mkt_qty from sample_ex_factory_mst a, sample_ex_factory_dtls b
	where a.id=b.sample_ex_factory_mst_id and  a.entry_form_id=396 and b.entry_form_id=396 and a.status_active=1 and b.status_active=1 group by  a.company_id, a.ex_factory_date, a.delivery_to, b.sample_development_id, b.sample_dtls_part_tbl_id, b.sample_name, b.gmts_item_id ");
	foreach ($delv_mkt_qty_sql as  $result)
	{
       $delv_mkt_qty_arr[$result[csf('company_id')]][$result[csf('sample_development_id')]][$result[csf('sample_name')]][$result[csf('gmts_item_id')]]+=$result[csf('del_mkt_qty')];
	 
	}

	if($db_type==0)
	{
		$po_sql=sql_select("SELECT a.id, group_concat(b.po_number) as po_number from wo_po_details_master a ,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id");
	}
	else
	{
		$po_sql=sql_select("SELECT a.id, listagg(CAST(b.po_number as VARCHAR(4000)),',') within group (order by b.po_number) as po_number from wo_po_details_master a ,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id");
    }

    foreach($po_sql as $po_value)
    {
        $po_array[$po_value[csf("id")]]=$po_value[csf("po_number")];
    }
    $booking_sql=sql_select("SELECT a.id,b.booking_no from wo_po_details_master a,wo_booking_mst b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
    foreach($booking_sql as $booking_value)
    {
        $booking_arr[$booking_value[csf("id")]]=$booking_value[csf("booking_no")];
    }

    $booking_without_order_sql=sql_select("SELECT b.style_id,a.booking_no from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and b.status_active=1  group by  b.style_id,a.booking_no");
    foreach($booking_without_order_sql as $vals)
    {
        $booking_without_order_arr[$vals[csf("style_id")]]=$vals[csf("booking_no")];
    }

	$checklist_status_sql=sql_select("SELECT requisition_id ,completion_status from sample_checklist_mst where status_active=1 and is_deleted=0");
	foreach($checklist_status_sql as $checklist_status_value)
	{
		$checklist_status_arr[$checklist_status_value[csf("requisition_id")]]=$checklist_status_value[csf("completion_status")];
	}
    //137-Sample Approval-Before Order Place
	$before_order_app_arr=array(); $sub_buyer_date_arr=array();
	$before_order_app=sql_select("SELECT sample_dtls_id, approval_status, max(submitted_to_buyer) as dt from wo_po_sample_approval_info where entry_form_id=137 and status_active=1 and is_deleted=0 group by sample_dtls_id, approval_status order by id asc");
	foreach($before_order_app as $vals)
	{
		$before_order_app_arr[$vals[csf("sample_dtls_id")]]=$vals[csf("approval_status")];
		$sub_buyer_date_arr[$vals[csf("sample_dtls_id")]]=$vals[csf("dt")];
	}
    ob_start();
    ?>

    <div>
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="5625" rules="all" id="table_header" >
            <thead>
                <tr>
                    <th style="word-break:break-all" width="30">SL </th>
                    <th style="word-break:break-all" width="95">Requisition No</th>
                    <th style="word-break:break-all" width="95">Req. Date</th>
                    <th style="word-break:break-all" width="45">Year</th>
                    <th style="word-break:break-all" width="110">Dealing Merchant</th>
                    <th style="word-break:break-all" width="100">BH Merchant</th>
                    <th style="word-break:break-all" width="100">Buyer</th>
                    <th style="word-break:break-all" width="110">Sample Stage</th>
                    <th style="word-break:break-all" width="90">Style Ref.</th>
                    <th style="word-break:break-all" width="110">Sample Name</th>
                    <th style="word-break:break-all" width="110">Garments Item</th>
                    <th style="word-break:break-all" width="60">Color</th>
                    <th style="word-break:break-all" width="40">SMV</th>
                    <th style="word-break:break-all" width="70">Season</th>
                    <th style="word-break:break-all" width="100">File</th>
                    <th style="word-break:break-all" width="90">Booking No</th>
                    <th style="word-break:break-all" width="105">Req. Approval</th>
                    <th style="word-break:break-all" width="115">Req. Acknowledge</th>
                    <th style="word-break:break-all" width="90">Req. Acknowledge Date</th>
                    <th style="word-break:break-all" width="60">Order No</th>
                    <th style="word-break:break-all" width="95">Ready To Prod.</th>
                    <th style="word-break:break-all" width="105">Sample Req. Qty</th>
                    <th style="word-break:break-all" width="105">Tgt. Delv. Date</th>
                    <th style="word-break:break-all" width="90">Confirm Delv End Date</th>
                    <th style="word-break:break-all" width="90">Pattern Date</th>
                    <th style="word-break:break-all" width="90">Cutting Date</th>
                    <th style="word-break:break-all" width="90">Sewing Date</th>
                    <th style="word-break:break-all" width="90">Wash Send Date</th>
                    <th style="word-break:break-all" width="90">Wash Rec. Date</th>
                    <th style="word-break:break-all" width="90">Finish Date</th>
                    <th style="word-break:break-all" width="80">Cutting Qty</th>
                    <th style="word-break:break-all" width="80">Cutting Reject Qty</th>
                    <th style="word-break:break-all" width="80">Cutting Bal From Req</th>
                    <th style="word-break:break-all" width="70">Embl. Issue Qty</th>
                    <th style="word-break:break-all" width="70">Embl. Receive Qty</th>
                    <th style="word-break:break-all" width="70">Embl. Bla</th>
                    <th style="word-break:break-all" width="80">Input Date(Last)</th>
                    <th style="word-break:break-all" width="80">Sewing Input Qty</th>
                    <th style="word-break:break-all" width="80">Input Bal From Req</th>
                    <th style="word-break:break-all" width="80">Input Bal From Cutting (Cutting Inhand)</th>
                    <th style="word-break:break-all" width="80">Output Date(Last)</th>
                    <th style="word-break:break-all" width="80">Sewing Output Qty</th>
                    <th style="word-break:break-all" width="80">Sewing Reject</th>
                    <th style="word-break:break-all" width="80">Sewing Output Bal From Req</th>
                    <th style="word-break:break-all" width="80">Sewing Output Bal From Input</th>
                    <th style="word-break:break-all" width="80">Dyeing Qty</th>
                    <th style="word-break:break-all" width="70">Wash Qty</th>
                    <th style="word-break:break-all" width="80">Reject Qty</th>
                    <th style="word-break:break-all" width="80">MKT Delv. Qty</th>
                    <th style="word-break:break-all" width="80">MKT Delv. Bal From Req</th>
                    <th style="word-break:break-all" width="80">MKT Delv. Bal From Sew. Output (Sample Inhand)</th>
                    <th style="word-break:break-all" width="70">Delv. Qty</th>
                    <th style="word-break:break-all" width="85">Delv. Balance</th>
                    <th style="word-break:break-all" width="80">Delv. Bal From MKT Delv. (MKT Inhand)</th>
                    <th style="word-break:break-all" width="80">Cutting Date</th>
                    <th style="word-break:break-all" width="125">Allowed Lead Time</th>
                    <th style="word-break:break-all" width="105">Actual Lead Time</th>
                    <th style="word-break:break-all" width="90">Sub To Buyer</th>
                    <th style="word-break:break-all" width="105">Approval Status</th>
                    <th style="word-break:break-all" width="95">Charge/Unit</th>
                    <th style="word-break:break-all" width="95">Amount</th>
                    <th style="word-break:break-all" width="80">Currency</th>
                    <th style="word-break:break-all">Comments</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:320px; width:5625px;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="5625" rules="all" id="table_body">
            <tbody>
            <?
             $sample_req_ackg=sql_select("SELECT distinct(a.id), a.entry_form_id,
             a.buyer_name, a.season, a.product_dept, b.insert_date as req_acknowledge_date, b.confirm_del_end_date
             from sample_development_mst a,sample_requisition_acknowledge b
             where a.entry_form_id in (117,203,449) $company_name $txt_date $buyer_name $sample_stages $req_no and a.status_active=1 and a.is_deleted=0 and a.id=b.sample_mst_id order by a.id desc ");
            //  echo "<pre>"; print_r($sample_req_ackg); die;
             foreach($sample_req_ackg as $val){
                 $req_ackg_arr[$val[csf('id')]]['req_acknowledge_date']=$val[csf('req_acknowledge_date')];
                 $req_ackg_arr[$val[csf('id')]]['confirm_del_end_date']=$val[csf('confirm_del_end_date')];
             }
            if($db_type==0) $yearCond="YEAR(a.insert_date)"; else if($db_type==2) $yearCond="to_char(a.insert_date,'YYYY')";

            $query="SELECT a.id, a.requisition_date, a.quotation_id, a.company_id, a.is_approved, a.is_acknowledge, a.dealing_marchant, a.requisition_number_prefix_num, $yearCond as year, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.id as dtls_id, b.sample_name, b.gmts_item_id, b.sample_color, b.smv, b.sample_charge, b.sample_curency, b.sample_prod_qty, b.delv_start_date, b.delv_end_date, b.is_complete_prod, b.entry_form_id, a.refusing_cause,c.confirm_del_end_date, c.insert_date as req_acknowledge_date, a.season_buyer_wise, c.sample_plan
            from sample_development_mst a,sample_development_dtls b, sample_requisition_acknowledge c
            where a.id=b.sample_mst_id and a.id = c.sample_mst_id and a.entry_form_id in(117,203,449) and b.entry_form_id in(117,203,449) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $txt_date $company_name $buyer_name $sample_stages $dealing_merchant $req_no $job_no $internal_ref $style_ref $file_no $order_no $year_cond and c.acknowledge_status=1 order by a.requisition_number_prefix_num";

            $sql=sql_select($query);
            $i=1;
            // echo "<pre>"; print_r($sql); die;
            foreach ($sql as $key => $value)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                $seasonName="";
                                
                if($value[csf('season')]!="") $seasonName=$season_arr[$value[csf('season')]];
                else $seasonName=$season_arr[$value[csf('season_buyer_wise')]];

                $entry_form_id=$value[csf('entry_form_id')] ;
                $link_format=""; $buttonAction="";
                $page_path='';

                $image_path = $file_arr[$value[csf('id')]]['sample_requisition_1'];

                $image_path = explode("**",$image_path);
                $sample_date=explode('----',$value[csf("sample_plan")]);
                $req_array=array();
                foreach($sample_date as $sample){
                    list($sapmple_plan,$pat_date,$cut_date,$swin_date,$wash_send_date,$wash_rec_date,$finish_date)=explode("**",$sample);
                    $req_array[$sapmple_plan]['pat_date']=$pat_date;
                    $req_array[$sapmple_plan]['cut_date']=$cut_date;
                    $req_array[$sapmple_plan]['swin_date']=$swin_date;
                    $req_array[$sapmple_plan]['wash_send_date']=$wash_send_date;
                    $req_array[$sapmple_plan]['wash_rec_date']=$wash_rec_date;
                    $req_array[$sapmple_plan]['finish_date']=$finish_date;
                }
                // echo "<pre>"; print_r($req_array); die;
                 //$all_date=$value[csf("sample_plan")];
                  //echo $all_date;
                

                //  echo $pat_date."##".$cut_date."##".$swin_date."##".$wash_send_date."##".$wash_rec_date."##".$finish_date;


                $image_location = '';
                foreach($image_path as $image){
                    if($image!='')
                    {
                        $image_location .="<a href='../".$image."'>File</a>&nbsp;";
                    }
                }

                if($image_location!='')
                {
                    $update_id = $value[csf('id')];
                    $image_location = "<a href='##' onclick='view_file(".$update_id.")'>VIEW</a>";
                }
                if($value[csf('sample_stage_id')]==1) $booking_no= $booking_arr[$value[csf("quotation_id")]];
                            else $booking_no= $booking_without_order_arr[$value[csf("id")]];

                if($entry_form_id==117) 
                {
                    $link_format="'../order/woven_order/requires/sample_requisition_controller'";
                    $buttonAction="sample_requisition_print";
                    $page_path=0;
                }
                else if($entry_form_id==203) 
                {
                    $link_format="'../order/woven_order/requires/sample_requisition_with_booking_controller'";
                    $buttonAction="sample_requisition_print";
                    $page_path=0;
                }
                else if($entry_form_id==449) 
                {
                    $link_format="'../order/woven_gmts/requires/sample_requisition_with_booking_controller'";
                    $buttonAction="sample_requisition_print1";
                    $page_path="'".$booking_no."'+'*'+1+'*'+0";
                }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="95" style="word-break:break-all" align="center"><a href='##' onClick="print_report(<? echo $value[csf('company_id')]; ?>+'*'+<? echo $value[csf('id')]; ?>+'*'+<? echo $page_path; ?>,'<?=$buttonAction;?>', <?=$link_format;?>)"><? echo $value[csf('requisition_number_prefix_num')]; ?></a></td>
                    <td width="95" style="word-break:break-all" align="center"><? echo  change_date_format($value[csf('requisition_date')]); ?></td>
                    <td width="45" style="word-break:break-all" align="center"><? echo  $value[csf('year')] ; ?></td>
                    <td width="110" style="word-break:break-all"><? echo $dealing_arr[$value[csf('dealing_marchant')]]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo  $value[csf('bh_merchant')] ; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_arr[$value[csf('buyer_name')]]; ?></td>
                    <td width="110" align="center" style="word-break:break-all"><? echo $sample_stage[$value[csf('sample_stage_id')]]; ?></td>
                    <td width="90" style="word-break:break-all"><? echo  $value[csf('style_ref_no')] ; ?></td>
                    <td width="110" style="word-break:break-all"><? echo $sample_name_arr[$value[csf('sample_name')]]; ?></td>
                    <td width="110" style="word-break:break-all"><? echo $garments_item[$value[csf('gmts_item_id')]]; ?></td>
                    <td width="60" style="word-break:break-all"><? echo $color_arr[$value[csf('sample_color')]]; ?></td>
                    <td width="40" align="center"><? echo fn_number_format($value[csf('smv')],2); ?></td>
                    <td width="70" style="word-break:break-all"><? echo $seasonName; ?></td>
                    <td width="100" align="center" style="word-break:break-all"><? echo $image_location; ?></td>
                    <td width="90" style="word-break:break-all">
						<?
                            if($value[csf('sample_stage_id')]==1) echo $booking_arr[$value[csf("quotation_id")]];
                            else echo $booking_without_order_arr[$value[csf("id")]];
                        ?>
                    </td>
                    <td width="105" style="word-break:break-all"><? if($value[csf('is_approved')]==1){echo "YES";} else{echo "NO";} ?></td>
                    <td width="115" style="word-break:break-all"><? $cause=$value[csf("refusing_cause")]; if($value[csf('is_acknowledge')]==1){echo "YES";} else if($cause!=""){$req_id=$value[csf("id")]; echo "<p><a style='color:crimson;font-size:14px;' href='##'  onclick=\"openmypage_refusing_cause( '$req_id' ,'refusing_popup');\" >Refused </a></p>";} else { echo "NO";} ?></td>
                    <td width="90" style="word-break:break-all" title="Date Time=<?=$req_ackg_arr[$value[csf('id')]]['req_acknowledge_date'];?>"><?=change_date_format($req_ackg_arr[$value[csf('id')]]['req_acknowledge_date']);?></td>
                    <td width="60" style="word-break:break-all"><a href="##"  onclick="openmypage_order_qty(<? echo $value[csf("quotation_id")];?>,'order_qty_popup');" ><? if($value[csf("sample_stage_id")]==1){ echo  $po_array[$value[csf('quotation_id')]] ; } else {echo "";}?></a></td>
                    <td width="95" style="word-break:break-all"><? $req_id=$value[csf("id")]; if($checklist_status_arr[$value[csf('id')]]==1){echo "<p><a href='##'  onclick=\"openmypage_checklist( '$req_id' ,'checklist_popup','YES');\" >YES </a></p>";}
                    else{ echo "<p><a href='##'  onclick=\"openmypage_checklist( '$req_id' ,'checklist_popup','NO');\" >NO </a></p>";} ?></td>
                    <td width="105" style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_prod_qty(<? echo $value[csf("dtls_id")];?>,'prod_qty_popup');" ><? echo $value[csf('sample_prod_qty')] ;$tot_sample_qnty+=$value[csf('sample_prod_qty')]; ?></a></td>
                    <td width="105" style="word-break:break-all"><? echo change_date_format($value[csf('delv_end_date')]);  ?></td>
                    <td width="90" style="word-break:break-all" align="right"><?=change_date_format($req_ackg_arr[$value[csf('id')]]['confirm_del_end_date']);?></td>                   
                    <td width="90" style="word-break:break-all" align="right"><?=change_date_format($req_array[$value[csf('sample_name')]]['pat_date']);?></td>
                    <td width="90" style="word-break:break-all" align="right"><?=change_date_format($req_array[$value[csf('sample_name')]]['cut_date']);?></td>
                    <td width="90" style="word-break:break-all" align="right"><?=change_date_format($req_array[$value[csf('sample_name')]]['swin_date']);?></td>
                    <td width="90" style="word-break:break-all" align="right"><?=change_date_format($req_array[$value[csf('sample_name')]]['wash_send_date']);?></td>
                    <td width="90" style="word-break:break-all" align="right"><?=change_date_format($req_array[$value[csf('sample_name')]]['wash_rec_date']);?></td>
                    <td width="90" style="word-break:break-all" align="right"><?=change_date_format($req_array[$value[csf('sample_name')]]['finish_date']);?></td>
                    <td width="80"style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_cutting_qty(<? echo $value[csf("dtls_id")];?>,'cutting_qty_popup');" ><? echo $cutting_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]  ;$tot_cutting_qnty+=$cutting_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?> </a></td> 

                    <td width="80"style="word-break:break-all" align="right"><?=$cutting_reject_arr[$value[csf("id")]][$value[csf("dtls_id")]];?></td>
                    <td width="80"style="word-break:break-all" title="Sample Req.Qty-Cutting Qty" align="right"><? $cutting_bal=$value[csf('sample_prod_qty')]-$cutting_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; echo $cutting_bal;?></td>

                    <td width="70" style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_embellishment_qty(<? echo $value[csf("dtls_id")]; ?>,'338','embellishment_qty_popup');" ><? echo $embellish_issue_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];$tot_emb_qnty+=$embellish_issue_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?></a></td>

                    <td width="70" style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_embellishment_qty(<? echo $value[csf("dtls_id")];?>,'128','embellishment_qty_popup');" ><? echo $embellish_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];$tot_embl_qnty+=$embellish_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?></a></td>
                    <td width="70" style="word-break:break-all" title="Embl.Issue Qty-Embl.Receive Qty" align="right"><? $embl_balance=$embellish_issue_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]-$embellish_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; echo $embl_balance; ?></td>

                    <td width="80" style="word-break:break-all" align="center"><? echo change_date_format($sewing_in_date_arr[$value[csf('dtls_id')]]) ; ?></td>

                    <td width="80" style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_sewing_qty(<? echo $value[csf("dtls_id")];?>,'337','sewing_qty_popup',<? echo $value[csf("id")];?>);" ><? echo $sewingIn_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];$tot_sewing_input+=$sewingIn_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?></a></td>

                    <td width="80" style="word-break:break-all" title="Sample Req.Qty-Sewing Input Qty" align="right"><? $input_bal_from_req=$value[csf('sample_prod_qty')]-$sewingIn_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; echo $input_bal_from_req;?></td>

                    <td width="80" style="word-break:break-all" title="Cutting Qty-Sewing Input Qty" align="right"><? $input_bal_from_cutting=$cutting_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]-$sewingIn_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; echo $input_bal_from_cutting;?></td> 
                    <td width="80" style="word-break:break-all" align="center"><? echo change_date_format($sewing_out_date_arr[$value[csf('dtls_id')]]) ; ?></td>
                    <td width="80" style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_sewing_qty(<? echo $value[csf("dtls_id")];?>,'130','sewing_qty_popup',<? echo $value[csf("id")];?>);" ><? echo $sewing_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];$tot_sewing_output+=$sewing_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?> </a></td>
                    <td width="80" style="word-break:break-all" align="center"><? echo $sew_reject_arr[$value[csf("dtls_id")]] ; ?></td> 
                    <td width="80" style="word-break:break-all" title="Sample Req.Qty-Sewing Output Qty" align="right"><?$sewing_outputbal_from_req=$value[csf('sample_prod_qty')]-$sewing_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]] ;echo $sewing_outputbal_from_req; ?></td>
                    <td width="80" style="word-break:break-all" title="Sewing Input Qty-(Sewing Output Qty+Sewing Reject)" align="right"><?$sewing_outputbal_from_input=$sewingIn_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]-($sewing_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]+$sew_reject_arr[$value[csf("dtls_id")]]); echo $sewing_outputbal_from_input; ?></td>

                    <td width="80" style="word-break:break-all"  align="right"><a href="##"  onclick="openmypage_dyeing_qty(<? echo $value[csf("dtls_id")];?>,'dyeing_qty_popup');" ><? echo $dyeing_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];$tot_dying_qnty+=$dyeing_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?></a></td>
                    <td width="70" style="word-break:break-all" align="right"><a href="##"  onclick="openmypage_wash_qty(<? echo $value[csf("dtls_id")];?>,'wash_qty_popup');" ><? echo $wash_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];$tot_wash_qnty+=$wash_arr[$value[csf('company_id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?></a></td>
                    <td width="80" style="word-break:break-all" align="center"><a href="##"  onclick="openmypage_reject_qty(<? echo $value[csf("dtls_id")];?>,'reject_qty_view');" ><? echo $cutting_reject_arr[$value[csf('id')]][$value[csf("dtls_id")]] + $emb_reject_arr[$value[csf("dtls_id")]]+$sew_reject_arr[$value[csf("dtls_id")]] + $wash_dyeing_reject_arr[$value[csf("dtls_id")]];$tot_reject_qnty+=$cutting_reject_arr[$value[csf('id')]][$value[csf("dtls_id")]] + $emb_reject_arr[$value[csf("dtls_id")]]+$sew_reject_arr[$value[csf("dtls_id")]] + $wash_dyeing_reject_arr[$value[csf("dtls_id")]];
                    ?></a></td>


                    <td width="80" style="word-break:break-all" align="right"><? echo  $delv_mkt_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; ?></td>
                    <td width="80" style="word-break:break-all" title="Sample Req.Qty-MKT Delv.Qty" align="right"><? $mkt_delvary_bal_from_req= $value[csf('sample_prod_qty')]-$delv_mkt_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]; echo  $mkt_delvary_bal_from_req; ?></td>
                    <td width="80" style="word-break:break-all" title="Sewing Output Qty-(Sewing Reject+MKT Delv.Qty)" align="right"><? $delv_bal_from_sew_output=$sewing_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]-($sew_reject_arr[$value[csf("dtls_id")]]+$delv_mkt_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]); echo  $delv_bal_from_sew_output; ?></td>


                    <td width="70" style="word-break:break-all" align="right"><? echo $delv_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]  ; ?>
                    </td>
                    <td width="85" style="word-break:break-all" title="Sample Req.Qty-Delv.Qty" align="right"><? echo $value[csf('sample_prod_qty')] -  $delv_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]] ;$tot_delv_balance+=$value[csf('sample_prod_qty')] -  $delv_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]] ?></td>

                    <td width="80" style="word-break:break-all" title="MKT Delv.Qty-Delv.Qty" align="right"><? $delv_bal_from_mkt_delivary_inhand=$delv_mkt_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]-$delv_qty_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];  echo  $delv_bal_from_mkt_delivary_inhand; ?></td>

                    <td width="80" style="word-break:break-all"><? echo  change_date_format($cutting_date_arr[$value[csf('id')]][$value[csf('dtls_id')]]) ; ?></td>
                    <td width="125" style="word-break:break-all" align="right"><?
						if($value[csf("is_complete_prod")]!="" || $value[csf("is_complete_prod")]!=0 )
						{
							$dates1=$value[csf('requisition_date')]; $dates2=$value[csf('delv_end_date')]; $time=trim(datediff('n',$dates1,$dates2));

							echo $days_allowed= floor(($time/60)/24);
						}
                    	?>
                    </td>
                    <td width="105" style="word-break:break-all" align="right">
						<?
                        if($value[csf("is_complete_prod")]!="" || $value[csf("is_complete_prod")]!=0 )
                        {
                            $date1=$value[csf('requisition_date')];
                            $date2=$prod_complete_arr[$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]];
                            $time2=trim(datediff('n',$date1,$date2));
                            $days_actual=floor(($time2/60)/24);
                            $diff=date_diff($date2,$date1);
                            if($days_allowed < $days_actual) echo "<p style='color:crimson;'>$days_actual days</p> "; else echo $days_actual." days";
                        }
                        ?>
                    </td>
                    <td width="90" style="word-break:break-all"><? echo change_date_format($sub_buyer_date_arr[$value[csf("dtls_id")]]); ?></td>
                    <td width="105" style="word-break:break-all"><a href="##"  onclick="openmypage_buyer_approval(<? echo $value[csf("dtls_id")];?>,'buyer_approval_popup');" ><? echo $approval_status[$before_order_app_arr[$value[csf("dtls_id")]]]; ?></a></td>
                    <td width="95" style="word-break:break-all" align="right"><? echo  $value[csf('sample_charge')] ; ?></td>
                    <td width="95" style="word-break:break-all" align="right"><? $amount=$sewing_arr[$value[csf('company_id')]][$value[csf('id')]][$value[csf('dtls_id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]*$value[csf('sample_charge')];echo $amount;$total_amount+=$amount; ?></td>
                    <td width="80" style="word-break:break-all"><? echo  $currency[$value[csf('sample_curency')]] ; ?></td>
                    <td  style="word-break:break-all" align="center"><a href="##"  onclick="openmypage_remark(<? echo $value[csf("dtls_id")];?>,'comments_view',<? echo $value[csf("id")];?>);" > View </a></td>
                </tr>
                <?
                $i++;
            }
            ?>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="5625" class="rpt_table">
        <tr>
                    <td width="30" align="center">&nbsp;</td>
                    <td width="95" align="center">&nbsp;</td>
                    <td width="95" align="center">&nbsp;</td>
                    <td width="45" align="center">&nbsp;</td>
                    <td width="110" style="word-break:break-all">&nbsp;</td>
                    <td width="100" style="word-break:break-all">&nbsp;</td>
                    <td width="100" style="word-break:break-all">&nbsp;</td>
                    <td width="110" align="center" style="word-break:break-all">&nbsp;</td>
                    <td width="90" style="word-break:break-all">&nbsp;</td>
                    <td width="110" style="word-break:break-all">&nbsp;</td>
                    <td width="110" style="word-break:break-all">&nbsp;</td>
                    <td width="60" style="word-break:break-all">&nbsp;</td>
                    <td width="40" align="center">&nbsp;</td>
                    <td width="70" style="word-break:break-all">&nbsp;</td>
                    <td width="100" style="word-break:break-all">&nbsp;</td>
                    <td width="90" style="word-break:break-all">&nbsp;</td>
                    <td width="105">&nbsp;</td>
                    <td width="115">&nbsp;</td>
                    <td width="90">&nbsp;</td>
                    <td width="60" style="word-break:break-all">&nbsp; </td>
                    <td width="95"><b>Total</b></td>
                    <td width="105" align="right"> <? echo  $tot_sample_qnty?></td>
                    <td width="105">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="90" align="right">&nbsp;</td>
                    <td width="80" align="right"><? echo $tot_cutting_qnty?></td>
                    <td width="80" align="right">&nbsp;</td>
                    <td width="80" align="right">&nbsp;</td>

                    <td width="70" align="right"><? echo $tot_emb_qnty?></td>
                    <td width="70" align="right"><? echo $tot_embl_qnty?></td>
                    <td width="70" align="right">&nbsp;</td>
                    <td width="80" align="center"></td>

                    <td width="80" align="right"><? echo  $tot_sewing_input ?></td>
                    <td width="80" align="center"></td>
                    <td width="80" align="center"></td>
                    <td width="80" align="center"></td>
                    <td width="80" align="right"><? echo  $tot_sewing_output ?></td>
                    <td width="80" align="center"></td>
                    <td width="80" align="center"></td>
                    <td width="80" align="center"></td>
                    <td width="80" align="right"><? echo  $tot_dying_qnty ?></td>
                    <td width="70" align="right"><? echo  $tot_wash_qnty ?></td>
                    <td width="80" align="center"><? echo  $tot_reject_qnty ?></td>
                    <td width="80" align="center"></td>
                    <td width="80" align="center"></td>
                    <td width="80" align="center"></td>
                    <td width="70" align="right"><? echo  $tot_delv_qnty ?></td>
                    <td width="85" align="right"><? echo  $tot_delv_balance ?></td>
                    <td width="80" align="right">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="125" align="right">&nbsp;</td>
                    <td width="105" align="right">&nbsp;</td>
                    <td width="90">&nbsp;</td>
                    <td width="105">&nbsp;</td>
                    <td width="95" align="right">&nbsp;</td>
                    <td width="95" align="right"><? echo  $total_amount; ?>&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td  align="center">&nbsp;</td>
                </tr>
			</table>
        </div>
        
    </div>
	<?
	exit();
}
if($action=='prod_qty_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Sample Prod. Qty", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend> Sample Production Qty.</legend>
     <div style="width:610px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="90" >Size</th>
                <th width="80" >BH Qty</th>
                <th width="80" >Plan Qty</th>
                <th width="80" >Dyeing Qty</th>
                <th width="80" >Test Qty</th>
                <th width="80" >Self Qty</th>
                <th width="">Total Qty</th>
            </thead>

        <?
            $sql= "select b.size_id,b.bh_qty,b.plan_qty ,b.dyeing_qty, b.test_qty ,b.self_qty ,b.total_qty from sample_development_dtls a,sample_development_size b where a.id=b.dtls_id and a.entry_form_id in(117,203,449) and a.status_active=1 and a.is_deleted=0 and  a.status_active=1 and a.is_deleted=0 and a.id='$sample_dtls_id' order by b.id";
              //  echo $sql;die;
           //  $arr=array(0=>$size_arr);
          //   echo  create_list_view ( "list_view_1", "Size,BH Qty,Plan Qty,Dyeing Qty,Test Qty,Self,Total", "90,80,80,80,80,80,80","610","220",1, $sql, "", "","", 1, 'size_id,0,0,0,0,0,0', $arr, "size_id,bh_qty,plan_qty ,dyeing_qty, test_qty ,self_qty ,total_qty", "../requires/sample_progress_report_controller", '','');
             $sql_sel=sql_select($sql);
             $i=1;
             foreach($sql_sel as $row)
             {
                $total_bh+=$row[csf("bh_qty")];
                $total_pl+=$row[csf("plan_qty")];
                $total_dy+=$row[csf("dyeing_qty")];
                $total_test+=$row[csf("test_qty")];
                $total_self+=$row[csf("self_qty")];
                $total+=$row[csf("total_qty")];
                ?>


            <tr>
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="90" align="center"><? echo  $size_arr[$row[csf("size_id")]]; ?></td>
                <td width="80" align="center"><? echo  $row[csf("bh_qty")]; ?></td>
                <td width="80" align="center"><? echo  $row[csf("plan_qty")]; ?></td>
                <td width="80" align="center"><? echo  $row[csf("dyeing_qty")]; ?></td>
                <td width="80" align="center"><? echo  $row[csf("test_qty")]; ?></td>
                <td width="80" align="center"><? echo  $row[csf("self_qty")]; ?></td>
                <td width="" align="center">   <? echo  $row[csf("total_qty")]; ?></td>
            </tr>

             <?
             $i++;
             }
         ?>
             <tr>
                <td colspan="2" >  </td>
                 <td align="center"> <? echo $total_bh; ?></td>
                <td align="center"> <? echo $total_pl; ?></td>
                <td align="center"> <? echo $total_dy; ?></td>
                <td align="center"> <? echo $total_test; ?></td>
                <td align="center"> <? echo $total_self; ?></td>
                <td align="center">  <? echo $total; ?></td>

            </tr>

        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='cutting_qty_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Cutting  Qty", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend> Cutting Qty.</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="40" >SL</th>
                <th width="100" >Color Name</th>
                <th width="110" >Size</th>
                <th width="110" >Output Qty</th>

            </thead>

        <?
            $sql="select b.size_id,sum(b.size_pass_qty) as qty,b.color_id from sample_sewing_output_dtls a,sample_sewing_output_colorsize b where a.id=b.sample_sewing_output_dtls_id and a.entry_form_id=127 and b.entry_form_id=127 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.sample_dtls_row_id='$sample_dtls_id' group by b.size_id,b.color_id";
           
            $sql_sel=sql_select($sql);
            $i=1;
            foreach($sql_sel as $row)
            {
                $total+=$row[csf("qty")];
                ?>


            <tr>
                <td width="40" align="center"><? echo $i; ?></td>
                <td width="100" align="center"><? echo  $color_arr[$row[csf("color_id")]]; ?></td>
                <td width="110" align="center"><? echo  $size_arr[$row[csf("size_id")]]; ?></td>
                <td width="110" align="center"><? echo  $row[csf("qty")]; ?></td>
            </tr>

             <?
             $i++;
             }
         ?>
             <tr>
                 <td colspan="2">Total</td>
                 <td align="center">  <? echo $total; ?></td>

            </tr>

        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='sewing_qty_popup')
{
	extract($_REQUEST);
	echo load_html_head_contents("Sewing Qty", "../../", 1, 1,$unicode,'','');
	if($entry_form==130) $caption_name="Output Qty"; else if($entry_form==337) $caption_name="Input Qty";
	?>
	<fieldset>
        <legend>Sewing Info</legend>
        <div style="width:370px; margin-top:10px">
            <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Color Name</th>
                    <th width="110">Size</th>
                    <th width="110"><? echo $caption_name; ?></th>
                </thead>
                <?
				//echo $smp_dev_mst_id.'DSDS';
                  $sql="select b.size_id, sum(b.size_pass_qty) as qty,b.color_id from sample_sewing_output_mst c,sample_sewing_output_dtls a, sample_sewing_output_colorsize b where a.id=b.sample_sewing_output_dtls_id and c.id=a.sample_sewing_output_mst_id and c.sample_development_id=$smp_dev_mst_id and a.entry_form_id='$entry_form' and b.entry_form_id='$entry_form'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sample_dtls_row_id='$sample_dtls_id' group by b.size_id,b.color_id";
                $sql_sel=sql_select($sql);
                $i=1;
                foreach($sql_sel as $row)
                {
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$total+=$row[csf("qty")];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                        <td width="40" align="center"><? echo $i; ?></td>
                        <td width="100" align="center"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
                        <td width="110" align="center"><? echo $size_arr[$row[csf("size_id")]]; ?></td>
                        <td width="110" align="center"><? echo $row[csf("qty")]; ?></td>
					</tr>
					<?
					$i++;
                }
                ?>
                <tr>
                    <td colspan="2">Total:</td>
                    <td align="center"><? echo $total; ?></td>
                </tr>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}

if($action=='dyeing_qty_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Dyeing Qty", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend> Dyeing Quantity</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="40" >SL</th>
                <th width="110" >Size</th>
                <th width="110" >Output Qty</th>

            </thead>

        <?
            $sql="select b.size_id,sum(b.size_pass_qty) as qty from sample_sewing_output_dtls a,sample_sewing_output_colorsize b where a.id=b.sample_sewing_output_dtls_id and a.entry_form_id=131 and b.entry_form_id=131 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.sample_dtls_row_id='$sample_dtls_id' and a.embel_name=5 group by b.size_id";
            $sql_sel=sql_select($sql);
            $i=1;
            foreach($sql_sel as $row)
            {
                $total+=$row[csf("qty")];
                ?>


            <tr>
                <td width="40" align="center"><? echo $i; ?></td>
                <td width="110" align="center"><? echo  $size_arr[$row[csf("size_id")]]; ?></td>
                <td width="110" align="center"><? echo  $row[csf("qty")]; ?></td>
            </tr>

             <?
             $i++;
             }
         ?>
             <tr>
                 <td colspan="2"></td>
                 <td align="center">  <? echo $total; ?></td>

            </tr>

        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='wash_qty_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Wash Qty", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend> Wash Quantity</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="40" >SL</th>
                <th width="110" >Size</th>
                <th width="110" >Output Qty</th>

            </thead>

        <?
            $sql="select b.size_id,sum(b.size_pass_qty) as qty from sample_sewing_output_dtls a,sample_sewing_output_colorsize b where a.id=b.sample_sewing_output_dtls_id and a.entry_form_id=131 and b.entry_form_id=131 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.sample_dtls_row_id='$sample_dtls_id' and a.embel_name=3 group by b.size_id";
            $sql_sel=sql_select($sql);
            $i=1;
            foreach($sql_sel as $row)
            {
                $total+=$row[csf("qty")];
                ?>


            <tr>
                <td width="40" align="center"><? echo $i; ?></td>
                <td width="110" align="center"><? echo  $size_arr[$row[csf("size_id")]]; ?></td>
                <td width="110" align="center"><? echo  $row[csf("qty")]; ?></td>
            </tr>

             <?
             $i++;
            }
         ?>
             <tr>
                 <td colspan="2"></td>
                 <td align="center">  <? echo $total; ?></td>

            </tr>

        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='order_qty_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("PO Info", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend> Order Info</legend>
     <div style="width:470px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="40" >SL</th>
                <th width="80" >Job No</th>
                <th width="80" >Order No.</th>
                <th width="90" >Color</th>
                <th width="90" >Qty</th>
                <th>Ship Date</th>
            </thead>

        <?
            $sql="select  a.job_no,b.po_number,b.po_quantity ,b.shipment_date ,c.color_number_id from  wo_po_details_master  a,wo_po_break_down  b,wo_po_color_size_breakdown c where a.job_no =b.job_no_mst and b.job_no_mst =c.job_no_mst and b.id=c.po_break_down_id and a.id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0    group by a.job_no,b.po_number,b.po_quantity ,b.shipment_date ,c.color_number_id";
            $sql_sel=sql_select($sql);
             $i=1;
            foreach($sql_sel as $row)
            {
                 ?>
             <tr>
                <td width="40" align="center"><? echo $i; ?></td>
                <td width="80" align="center"><? echo $row[csf("job_no")] ; ?></td>
                <td width="80" align="center"><? echo  $row[csf("po_number")]; ?></td>
                 <td align="center"><? echo  $color_arr[$row[csf("color_number_id")]]; ?></td>
                <td width="90" align="center"><? echo  $row[csf("po_quantity")]; ?></td>
                <td width="90" align="center"><? echo  $row[csf("shipment_date")]; ?></td>

            </tr>

             <?
             $i++;
            }
         ?>
             <tr>
                 <td colspan="2"></td>
                 <td align="center">  <? echo $total; ?></td>

            </tr>

        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='buyer_approval_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Approval Status", "../../", 1, 1,$unicode,'','');
    $sql=sql_select("select a.buyer_name,a.requisition_number,a.requisition_number_prefix_num,a.company_id,a.style_ref_no,b.id as dtls_id  from sample_development_mst a , sample_development_dtls b where a.id=b.sample_mst_id and b.entry_form_id in(117,203) and b.status_active=1 and b.is_deleted=0 and b.id='$sample_dtls_id'");
    foreach($sql as $value)
    {
       $requisition_info_arr[$value[csf("dtls_id")]]["comp"]=$value[csf("company_id")];
       $requisition_info_arr[$value[csf("dtls_id")]]["buyer"]=$value[csf("buyer_name")];
       $requisition_info_arr[$value[csf("dtls_id")]]["req"]=$value[csf("requisition_number")];
       $requisition_info_arr[$value[csf("dtls_id")]]["req_no"]=$value[csf("requisition_number_prefix_num")];
       $requisition_info_arr[$value[csf("dtls_id")]]["style"]=$value[csf("style_ref_no")];
    }

 ?>
    <fieldset>
    <legend> Approval Status</legend>
     <div style="width:660px; margin-top:10px">
        <table width="100%">
            <tr style="font-size: 25px;">
                <th colspan="6" align="center">Sample Approval Details</th>

            </tr>
            <tr>
                <td colspan="6" height="15"></td>
            </tr>

            <tr style="font-size: 22px;">
                 <td align="right"><b>Requisition Number</b> </td>
                 <td> &nbsp; : <? echo  $requisition_info_arr[$value[csf("dtls_id")]]["req"];?></td>
                 <td>&nbsp; </td>
                 <td>&nbsp;</td>
                 <td align="right"><b>Buyer Name </b></td>
                 <td align="left">&nbsp; : <? echo  $buyer_arr[$requisition_info_arr[$value[csf("dtls_id")]]["buyer"]];?></td>
            </tr>

            <tr style="font-size: 22px;">
                 <td align="right"><b>Company Name</b> </td>
                 <td> &nbsp; : <? echo  $company_arr[$requisition_info_arr[$value[csf("dtls_id")]]["comp"]];?></td>
                 <td>&nbsp; </td>
                 <td>&nbsp;</td>
                 <td align="right"><b>Style Ref No </b></td>
                 <td align="left">&nbsp; :<? echo  $requisition_info_arr[$value[csf("dtls_id")]]["style"];?></td>
            </tr>
       </table>
       </div>
       <div style="width:660px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>

                <th width="40" >SL</th>
                <th width="80" >Req. No</th>
                <th width="100" >To Buyer</th>
                <th width="120" >Status</th>
                <th width="120" >Approval Date</th>
                 <th>Comments</th>

            </thead>


        <?
            $sql="select submitted_to_buyer ,approval_status,approval_status_date ,sample_comments  from wo_po_sample_approval_info where entry_form_id=137 and status_active=1 and is_deleted=0 and sample_dtls_id ='$sample_dtls_id' order by id ";
            $sql_sel=sql_select($sql);
            $i=1;
            foreach($sql_sel as $row)
            {
             ?>
             <tr>
                <td width="40" align="center"><? echo $i; ?></td>
                <td width="80" align="center"><? echo $requisition_info_arr[$sample_dtls_id]["req_no"];?></td>
                <td width="100" align="center"><?  echo  change_date_format($row[csf("submitted_to_buyer")]); ?></td>
                <td width="120" align="center"><?  echo  $approval_status[$row[csf("approval_status")]]; ?></td>
                <td width="120" align="center"><? echo  change_date_format($row[csf("approval_status_date")]); ?></td>
                 <td align="center"><?  echo  $row[csf("sample_comments")]; ?></td>
             </tr>

             <?
             $i++;
            }
         ?>
             <tr>
                 <td colspan="2"></td>
                 <td align="center">  <? echo $total; ?></td>

            </tr>

        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='embellishment_qty_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Embellishment Qty", "../../", 1, 1,$unicode,'','');
	if($entry_form==128) $caption_name="Receive Qty"; else if($entry_form==338) $caption_name="Issue Qty";
	?>
	<fieldset>
        <legend> Embellishment Qty.</legend>
        <div style="width:410px; margin-top:10px">
            <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
                <thead>
                    <th width="40">SL</th>
                    <th width="40">Embl. Name</th>
                    <th width="100">Color Name</th>
                    <th width="110">Size</th>
                    <th width="110"><? echo $caption_name; ?></th>
                </thead>
                <?
                $sql="select a.embel_name, b.size_id, b.size_pass_qty as qty,b.color_id from sample_sewing_output_dtls a, sample_sewing_output_colorsize b where a.id=b.sample_sewing_output_dtls_id and a.entry_form_id='$entry_form' and b.entry_form_id='$entry_form' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sample_dtls_row_id='$sample_dtls_id' order by a.embel_name";

               
                $sql_sel=sql_select($sql);
                $color_name_arr=array();
                foreach($sql_sel as $values)
                {
                	$embel_arrs[$values[csf("embel_name")]][$values[csf("size_id")]]['qty']+=$values[csf("qty")];
        
                    $color_name_arr[$values[csf("embel_name")]]['color']=$values[csf("color_id")];

                }

                $row_span_arr=array();
                foreach($embel_arrs as $emb_name=>$emb_data)
                {
					$row_span=0;
					foreach($emb_data as $size_id=>$row)
					{
						$row_span++;
					}
					$row_span_arr[$emb_name]=$row_span;
                }

                // print_r($row_span_arr);
                $i=1;
                foreach($embel_arrs as $emb_name=>$emb_data)
                {
					$j=0;
					foreach($emb_data as $size_id=>$row)
					{
						?>
						<tr>
							<?
                            if($j==0)
                            {
                                ?>
                                <td width="40" rowspan="<? echo $row_span_arr[$emb_name]?>" align="center"><? echo $i; ?></td>
                                <td width="40" rowspan="<? echo $row_span_arr[$emb_name]?>" align="center"><? echo $emblishment_name_array[$emb_name]; ?></td>
                                <?
                                $i++;
                            }
                            
                            ?>
                            <td width="100" align="center"><?$color_id=$color_name_arr[$emb_name]['color']; echo $color_arr[$color_id];?></td>
                            <td width="110" align="center"><? echo $size_arr[$size_id]; ?></td>
                            <td width="110" align="center"><? echo $row[("qty")]; ?></td>
						</tr>
						<?
						$j++;
						$total+=$row[("qty")];
						$gr_total+=$row[("qty")];
					}
					?>
					<tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td align="center"><b><? echo $total; $total=0; ?></b></td>
					</tr>
					<?
                }
                ?>
                <tr>
                    <td colspan="3" align="right"><b> Grand Total</b></td>
                    <td align="center"><b><? echo $gr_total; ?></b></td>
                </tr>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}

if($action=='comments_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("Remarks", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend>Cutting</legend>
        <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=127 and is_deleted=0 and status_active=1";
              // echo $sql;die;
             echo  create_list_view ( "list_view_1", "Date,Cutting Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');

      
	    ?>
    </fieldset>

    <fieldset>
    <legend>Sewing Output</legend>
        <?
             $sql= "select b.id, b.sample_sewing_output_mst_id, b.sample_name,b.sample_dtls_row_id,b.item_number_id, b.sewing_date, b.qc_pass_qty,b.reject_qty, b.remarks from sample_sewing_output_dtls b,sample_sewing_output_mst a where a.id=b.sample_sewing_output_mst_id and b.sample_dtls_row_id='$sample_dtls_id' and a.sample_development_id=$smp_dev_mst_id and b.entry_form_id=130 and b.is_deleted=0 and b.status_active=1";
              // echo $sql;die; 
             echo  create_list_view ( "list_view_1", "Date,Sewing Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');

        ?>
    </fieldset>


        <?
             $sql= "select  b.delivery_date,b.sample_ex_factory_mst_id,b. sample_name,b.sample_dtls_part_tbl_id,b.gmts_item_id, b.ex_factory_qty , b.remarks from sample_ex_factory_mst a ,sample_ex_factory_dtls b where a.id=b.sample_ex_factory_mst_id and  b.sample_dtls_part_tbl_id='$sample_dtls_id' and a.entry_form_id=132 and a.is_deleted=0 and a.status_active=1 and b.entry_form_id=132 and b.is_deleted=0 and b.status_active=1";
             // echo $sql;die;
             if(count(sql_select($sql))>0)
             {
                ?>
             <fieldset>
             <legend>Delivery</legend>
                <?
                echo  create_list_view ( "list_view_1", "Date,Delivery Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "delivery_date,ex_factory_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');
             }


        ?>
             </fieldset>



        <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=128 and is_deleted=0 and status_active=1 and embel_name=1";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Print</legend>

                <?
             echo  create_list_view ( "list_view_1", "Date,Print Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');
            }

        ?>
                </fieldset>


                   <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=128 and is_deleted=0 and status_active=1 and embel_name=2";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Embroidery</legend>

                <?
             echo  create_list_view ( "list_view_1", "Date,Embroidery Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');
            }

        ?>
                </fieldset>


                 <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=131 and is_deleted=0 and status_active=1 and embel_name=3";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Wash</legend>

                <?
             echo  create_list_view ( "list_view_1", "Date,Wash Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');
            }

        ?>
                </fieldset>



                 <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=128 and is_deleted=0 and status_active=1 and embel_name=4";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Special Works</legend>

                <?
             echo  create_list_view ( "list_view_1", "Date,Special Works Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');
            }

        ?>
                </fieldset>



                 <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=131 and is_deleted=0 and status_active=1 and embel_name=5";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Gmts Dyeing</legend>

                <?
             echo  create_list_view ( "list_view_1", "Date,Dyeing Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');
            }

        ?>
                </fieldset>




<?
}

if($action=='reject_qty_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("Remarks", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend>Cuttings Reject</legend>
        <?
             $sql= "select sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sum(qc_pass_qty) as qc,sum(reject_qty) as reject from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=127 and is_deleted=0 and status_active=1 group by   sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id";
             //echo $sql;die;
             $arr =array(0=>$sample_name_arr,1=>$garments_item);
              echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../requires/sample_progress_report_controller", '','0,0,0');

        ?>
    </fieldset>

     <fieldset>
    <legend>Embellishment Reject</legend>
        <?
             $sql= "select sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id,   sum(qc_pass_qty) as qc,sum(reject_qty) as reject  from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=128 and is_deleted=0 and status_active=1 group by  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id  ";
              // echo $sql;die;
              $arr =array(0=>$sample_name_arr,1=>$garments_item);
             echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../requires/sample_progress_report_controller", '','0,0,0');

        ?>
    </fieldset>


    <fieldset>
    <legend>Sewing Reject</legend>
        <?
             $sql= "select sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id,   sum(qc_pass_qty) as qc,sum(reject_qty) as reject  from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=130 and is_deleted=0 and status_active=1 group by  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id  ";
              // echo $sql;die;
              $arr =array(0=>$sample_name_arr,1=>$garments_item);
             echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../requires/sample_progress_report_controller", '','0,0,0');

        ?>
    </fieldset>

    <fieldset>
    <legend>Dyeing and Wash Reject</legend>
        <?
             $sql= "select  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id,   sum(qc_pass_qty) as qc,sum(reject_qty) as reject  from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=131 and is_deleted=0 and status_active=1 group by  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id  ";
             $arr =array(0=>$sample_name_arr,1=>$garments_item);
           //  echo $sql;die;
             echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../requires/sample_progress_report_controller", '','0,0,0,0');

        ?>
    </fieldset>


<?
}

//cutting_popup
if($action=='cutting_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");

	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise cutting production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?


}

if($action=='printing_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond  and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?


}

if($action=='checklist_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Not Checklist", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend> <? if($type=="NO"){echo "Not";} ?> Checklist Info</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="50" >SL</th>
                <th width="" >Name</th>
             </thead>

        <?
             $checklist_arr=$sample_checklist_set;
             $sql= sql_select("select checklist_id,requisition_id from sample_checklist_dtls where status_active=1 and is_deleted=0 and requisition_id='$req_id'");
             if($type=="NO")
             {
                 foreach($sql as $val)
                 {
                    unset($checklist_arr[$val[csf("checklist_id")]]);
                 }
             }

             if($type=="YES")
             {
                foreach($sql as $val)
                 {
                   $checklist_arrs[$val[csf("checklist_id")]]= $checklist_arr[$val[csf("checklist_id")]];
                 }
             }

              $i=1;
              if($type=="YES"){$checklist_arr=$checklist_arrs;}
             foreach($checklist_arr as $name)
             {

                ?>
                <tr>
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="" align="center"><strong><? echo  $name; ?> </strong></td>

                </tr>
                 <?
                 $i++;
             }
         ?>
        </table>
     </div>
    </fieldset>
	<?
    exit();
}

if($action=='refusing_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Not Checklist", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend>  Refusing Cause Info</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
        <?
              $sql= "select id,refusing_cause from sample_development_mst where status_active=1 and is_deleted=0 and id='$req_id' and entry_form_id in(117,203)";
              $sql_sel=sql_select($sql);
              $i=1;

                 foreach($sql_sel as $val)
                  {
                   if($val[csf("refusing_cause")]!="")
                   {
                    ?>
                    <thead>
                <th width="50" >SL</th>
                <th width="" >Cause</th>
             </thead>
                <tr>
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="" align="center"><strong style="font-size: 16px;"><? echo  $val[csf("refusing_cause")]; ?> </strong></td>

                </tr>
                 <?
                 $i++;
                   }
                 }
         ?>
        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='view_file')
{
    extract($_REQUEST);
    echo load_html_head_contents("View File", "../../", 1, 1,$unicode,'','');
    ?>
    <?php
    $update_id = $update_id;

    $file_sql = sql_select("select master_tble_id, form_name, image_location from common_photo_library where file_type=2 and is_deleted=0 and form_name='sample_requisition_1' and master_tble_id='$update_id'");
    ?>

    <div style="width:670px">
        <table width="100%" cellspacing="0" cellpadding="0" border="0">
            <tbody>
                <tr>
                    <td width="100%" align="center"> 
                        <div id="files" style="width:100%; height:100%; background-color:" align="center">
                            <li style="list-style:none; width:100%;">
                                <?php
                                    foreach($file_sql as $file)
                                        {
                                            $file_name = explode("/",$file[csf('image_location')]);
                                            $file_name = $file_name[1];
                                            ?>
                                            <div style="width: 200px;display:inline-block; float:left;">
                                            <a  href="../../includes/common_functions_for_js.php?filename=../<?php echo $file[csf('image_location')];?>&action=download_file"> <img src="../../file_upload/blank_file.png" height="97px" width="89px" /></a><br /><p><?php echo $file_name;?></p>
                                            </div>
                                            <?php
                                        }
                                ?>
                            </li>
                        </div> 
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}

?>