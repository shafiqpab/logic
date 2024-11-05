<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
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




	
    $prod_complete_sql=sql_select("SELECT sample_dtls_part_tbl_id,sample_name,gmts_item_id,max(delivery_date) as delv_date from  sample_ex_factory_dtls where entry_form_id=132 and status_active=1 and is_deleted=0 group by sample_dtls_part_tbl_id,sample_name,gmts_item_id");
    foreach($prod_complete_sql as $results)
    {
        $prod_complete_arr[$results[csf('sample_dtls_part_tbl_id')]][$results[csf('sample_name')]][$results[csf('gmts_item_id')]]=change_date_format($results[csf('delv_date')]);
    }

	$delv_qty_arr=array(); $delivery_date_arr=array();
	$delv_qty_sql=sql_select("SELECT a.company_id, a.ex_factory_date, a.delivery_to, b.sample_development_id, b.sample_dtls_part_tbl_id, b.sample_name, b.gmts_item_id, sum(b.ex_factory_qty) as qc_pass_qty from sample_ex_factory_mst a, sample_ex_factory_dtls b
	where a.id=b.sample_ex_factory_mst_id and  a.entry_form_id=132 and b.entry_form_id=132 and a.status_active=1 and b.status_active=1 group by  a.company_id, a.ex_factory_date, a.delivery_to, b.sample_development_id, b.sample_dtls_part_tbl_id, b.sample_name, b.gmts_item_id ");
	foreach ($delv_qty_sql as  $result)
	{
	   $delv_qty_arr[$result[csf('company_id')]][$result[csf('sample_dtls_part_tbl_id')]][$result[csf('sample_name')]][$result[csf('gmts_item_id')]]+=$result[csf('qc_pass_qty')];

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
 




    $all_data_arr=array(); 
    $prod_sql=sql_select("SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name, b.sewing_date, b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty as reject from sample_sewing_output_mst a, sample_sewing_output_dtls b where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in (127,128,130,131,337,338) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");


    $string="";
    foreach($prod_sql as $val)
    {

        $string=$val[csf('smp_dev_id')].'*'.$val[csf('sample_name')].'*'.$val[csf('item_number_id')];

        if($val[csf("entry_form_id")]==127)
		{
		
            $all_data_arr[$string]['cutting_qty']+=$val[csf('qc_pass_qty')];
            $all_data_arr[$string]['other_reject']+=$val[csf('reject')];

		}
		
		if($val[csf("entry_form_id")]==130)
		{
			
            $all_data_arr[$string]['sewing_out']+=$val[csf('qc_pass_qty')];
            $all_data_arr[$string]['sewing_reject']+=$val[csf('reject')];
        
		}
        if($val[csf("entry_form_id")]==337)
		{
			
            $all_data_arr[$string]['sewing_in']+=$val[csf('qc_pass_qty')];
          
		}

		// if($val[csf("entry_form_id")]==131)
		// {
			
		// 	if($val[csf("embel_name")]==5) $dyeing_arr[$val[csf('smp_dev_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
		// 	if($val[csf("embel_name")]==3) $wash_arr[$val[csf('smp_dev_id')]][$val[csf('sample_name')]][$val[csf('item_number_id')]]+=$val[csf('qc_pass_qty')];
        //     $all_data_arr[$string]['other_reject']+=$val[csf('reject')];
		// }
		
		if($val[csf("entry_form_id")]==338)
		{
            if($val[csf("embel_name")]==1){
			   
                $all_data_arr[$string]['print_issue']+=$val[csf('qc_pass_qty')];
            }else if($val[csf("embel_name")]==2){
			  
                $all_data_arr[$string]['embr_issue']+=$val[csf('qc_pass_qty')];
            }else if($val[csf("embel_name")]==3){
			    
                $all_data_arr[$string]['wash_issue']+=$val[csf('qc_pass_qty')];
            }else if($val[csf("embel_name")]==4){
			  
                $all_data_arr[$string]['spwork_issue']+=$val[csf('qc_pass_qty')];
            }
		}

        if($val[csf("entry_form_id")]==128)
		{
           
            $all_data_arr[$string]['other_reject']+=$val[csf('reject')];
            if($val[csf("embel_name")]==1){
			   
                $all_data_arr[$string]['print_rcv']+=$val[csf('qc_pass_qty')];
            }else if($val[csf("embel_name")]==2){
			   
                $all_data_arr[$string]['embr_rcv']+=$val[csf('qc_pass_qty')];
            }else if($val[csf("embel_name")]==3){
			  
                $all_data_arr[$string]['wash_rcv']+=$val[csf('qc_pass_qty')];
            }else if($val[csf("embel_name")]==4){
			    
                $all_data_arr[$string]['spwork_rcv']+=$val[csf('qc_pass_qty')];
            }
		}
    }
    // echo "<pre>";print_r($embellish_issue_arr);die;


    $prod_sql2=sql_select("SELECT   a.sample_development_id as smp_dev_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty, a.shiping_status
    from sample_ex_factory_mst c, sample_ex_factory_dtls a, sample_ex_factory_colorsize b where c.id=a.sample_ex_factory_mst_id and a.id=b.sample_ex_factory_dtls_id and c.delivery_basis=1  and c.status_active=1
     and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=132  and b.entry_form_id=132 and c.entry_form_id=132 and b.color_id IS NOT NULL");

    $string="";
    foreach($prod_sql2 as $val)
    {
        $string=$val[csf('smp_dev_id')].'*'.$val[csf('sample_name')].'*'.$val[csf('gmts_item_id')];
        $all_data_arr[$string]['ex_factory_qty']+=$val[csf('ex_factory_qty')];
        $all_data_arr[$string]['shiping_status']=$val[csf('shiping_status')];
        
    }

    $prod_sql3=sql_select("SELECT a.id,a.order_type,a.sample_development_id as smp_dev_id,a.sample_name,a.gmts_item_id, a.ex_factory_qty,a.delivery_date, a.carton_qty, a.carton_per_qty, a.remarks, a.shiping_status,b.color_id as sample_color,b.size_id,b.size_pass_qty as size_qty from sample_ex_factory_dtls a, sample_ex_factory_colorsize b where a.id=b.sample_ex_factory_dtls_id    and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=396 and b.entry_form_id=396 ");


    $string="";
    foreach($prod_sql3 as $val)
    {
        $string=$val[csf('smp_dev_id')].'*'.$val[csf('sample_name')].'*'.$val[csf('gmts_item_id')];
        $all_data_arr[$string]['delivery_qty']+=$val[csf('ex_factory_qty')];
       
        
    }



            if($db_type==0) $yearCond="YEAR(a.insert_date)"; else if($db_type==2) $yearCond="to_char(a.insert_date,'YYYY')";
            $query="SELECT a.id, a.requisition_date, a.quotation_id, a.company_id, a.is_approved, a.is_acknowledge, a.dealing_marchant, a.requisition_number_prefix_num, to_char(a.insert_date,'YYYY') as year,
            a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.id as dtls_id, b.sample_name, b.gmts_item_id, b.sample_color, b.smv, b.sample_charge, b.sample_curency, 
            b.sample_prod_qty, b.delv_start_date, b.delv_end_date, b.is_complete_prod, a.refusing_cause 
            from sample_development_mst a,sample_development_dtls b where a.id=b.sample_mst_id   and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.entry_form_id=203 
            and b.entry_form_id=203    $txt_date $company_name $buyer_name $sample_stages $dealing_merchant $req_no $job_no $internal_ref $style_ref $file_no $order_no $year_cond order by b.id DESC";
         
        //  echo  $query;
                
            $sql=sql_select($query);
            foreach ($sql as $key => $value)
            {

                $req_id_arr[$value[csf('id')]]=$value[csf('requisition_number_prefix_num')];
             
                $req_wise_data_arr[$value[csf('requisition_number_prefix_num')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]['color'].=$color_arr[$value[csf('sample_color')]].",";
                $req_wise_data_arr[$value[csf('requisition_number_prefix_num')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]['season']=$value[csf('season')];
                $req_wise_data_arr[$value[csf('requisition_number_prefix_num')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]['year']=$value[csf('year')];
                $req_wise_data_arr[$value[csf('requisition_number_prefix_num')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]['sample_stage_id']=$value[csf('sample_stage_id')];
                $req_wise_data_arr[$value[csf('requisition_number_prefix_num')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]['quotation_id']=$value[csf('quotation_id')];
                $req_wise_data_arr[$value[csf('requisition_number_prefix_num')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]['requisition_date']=$value[csf('requisition_date')];
                $req_wise_data_arr[$value[csf('requisition_number_prefix_num')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]['buyer_name']=$value[csf('buyer_name')];
                $req_wise_data_arr[$value[csf('requisition_number_prefix_num')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]['dtls_id']=$value[csf('dtls_id')];
                $req_wise_data_arr[$value[csf('requisition_number_prefix_num')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]['sample_prod_qty']+=$value[csf('sample_prod_qty')];
                $req_wise_data_arr[$value[csf('requisition_number_prefix_num')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]['style_ref_no']=$value[csf('style_ref_no')];
                $req_wise_data_arr[$value[csf('requisition_number_prefix_num')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]['is_complete_prod']=$value[csf('is_complete_prod')];
                $req_wise_data_arr[$value[csf('requisition_number_prefix_num')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]['company_id']=$value[csf('company_id')];
                $req_wise_data_arr[$value[csf('requisition_number_prefix_num')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]]['id']=$value[csf('id')];
            }

            // echo "<pre>";
            // print_r($req_wise_data_arr);
            $sql_fabric="SELECT id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,sample_color,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,color_data, determination_id,fabric_source,delivery_date,process_loss_percent,grey_fab_qnty,remarks_ra, collar_cuff_breakdown,yarn_dtls from sample_development_fabric_acc where  form_type=1 and  is_deleted=0  and status_active=1 order by id ASC";
            $sql_resultf =sql_select($sql_fabric); 
            foreach($sql_resultf as $row)
			{

                $req_id=$req_id_arr[$row[csf('sample_mst_id')]];
                $sample_req_data_arr[$req_id][$row[csf('sample_name')]]['fabric_description'] .=$row[csf('fabric_description')].",";
                $sample_req_data_arr[$req_id][$row[csf('sample_name')]]['fin_fab_req_qty']+=$row[csf('required_qty')];


            }

            $req_row_arr=array();
            foreach ($req_wise_data_arr as $req_key => $sample_data) {
                foreach ($sample_data as $sample_key => $gmts_data){
                    $req_rowspan=0;
                  foreach ($gmts_data as $gmts_key => $value){
                    $req_rowspan++;
                  }
                  $req_row_arr[$req_key][$sample_key]+=$req_rowspan;
                }
            }

            // echo "<pre>";
            // print_r($req_row_arr);

            ?>
                    <div>
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="3265" rules="all" id="table_header" >
            <thead>
                <tr>
                    <th width="30">SL </th>
                    <th width="95">Requisition No</th>       
                    <th width="110">Sample Name</th>    
                    <th width="100">Buyer</th>       
                    <th width="45">Year</th>                   
                    <th width="110">Booking No</th>
                    <th width="70">Season</th>
                    <th width="90">Style Ref.</th>
                    <th width="60">Color</th>
                    <th width="100">Finish Fab. Rcv Qty (Kg)</th>
                    <th width="100">Finish Fabric Issue Qty (Kg)</th>
                    <th width="150">Fabric Description</th>
                    <th width="95">Delivery  Date</th>
                    <th width="80">MnM Ex-Factory Date</th>
                    <th width="110">Garments Item</th>
                    <th width="105">Sample Req. Qty</th>
                    <th width="105">Approved Cut %</th>
                    <th width="90">Plan Cut Qty</th>
                    <th width="80">Cutting Qty</th>
                    <th width="90">Cutting Qty Balance</th>
                    <th width="90">Sent to Print</th>
                    <th width="90">Rcv Print</th>
                    <th width="70">Sent to Emb</th>
                    <th width="70">Rcv Emb</th>
                    <th width="90">Sent to Sp. Works</th>
                    <th width="90">Rcv Sp. Works</th>
                   
                    <th width="80">Sewing Input Qty</th>
                    <th width="80">Sewing Output Qty</th>                 
                  
                    <th width="80">Sent to Wash</th>
                    <th width="70">Rcv Wash</th>
                    <th width="80">Reject Qty Others</th>
                    <th width="90">Sample Sewing Reject Qty</th>
                    <th width="105">Delivery to MnM</th>
                    <th width="95">Ex-Factory MnM</th>
                    <th width="80">Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:320px; overflow-y:scroll; width:3285px;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3265" rules="all" id="table_body">
            <tbody>

            <?






            $i=1;$string2="";
            foreach ($req_wise_data_arr as $req_key => $sample_data)
            {
                foreach ($sample_data as $sample_key => $gmts_data)
                {
                    $rs=1;

                  foreach ($gmts_data as $gmts_key => $value)
                  {

                        $string2=$value['id'].'*'.$sample_key.'*'.$gmts_key;





                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                       
                        <?php
                        if($rs==1){
                            $rowspan=$req_row_arr[$req_key][$sample_key];
                            ?>
                         <td width="30" align="center" rowspan="<?=$rowspan;?>"><? echo $i; ?></td>
                        <td width="95" align="center" rowspan="<?=$rowspan;?>" title="<?=$value['id'];?>"><? echo  $req_key; ?></td>
                        <td width="110" style="word-break:break-all" rowspan="<?=$rowspan;?>"><? echo $sample_name_arr[$sample_key]; ?></td>
                        <td width="100" style="word-break:break-all" rowspan="<?=$rowspan;?>"><? echo $buyer_arr[$value['buyer_name']]; ?></td>
                        <td width="45" align="center" rowspan="<?=$rowspan;?>"><? echo  $value['year'] ; ?></td>       
                        <td width="110" align="center" style="word-break:break-all" rowspan="<?=$rowspan;?>">
                            <?  
                                if($value['sample_stage_id']==1) echo $booking_arr[$value["quotation_id"]];
                                else echo $booking_without_order_arr[$value["id"]];
                             ?>
                        </td>
                        <td width="70" align="center" rowspan="<?=$rowspan;?>"><? echo $season_arr[$value['season']] ; ?></td>
                        <td width="90" style="word-break:break-all" rowspan="<?=$rowspan;?>"><? echo  $value['style_ref_no'] ; ?></td>
                        <td width="60" style="word-break:break-all" rowspan="<?=$rowspan;?>"><? echo $value['color']; ?></td>
                        <td width="100" style="word-break:break-all" rowspan="<?=$rowspan;?>"><? echo $sample_req_data_arr[$req_key][$sample_key]['fin_fab_req_qty']; ?></td>
                        <td width="100" style="word-break:break-all" rowspan="<?=$rowspan;?>"><? echo $color_arr[$value['color']]; ?></td>
                        <td width="150" style="word-break:break-all" rowspan="<?=$rowspan;?>"><div style="word-wrap:break-word; width:100px"><? echo $sample_req_data_arr[$req_key][$sample_key]['fabric_description']; ?></div></td>
                        <td width="95" align="center" rowspan="<?=$rowspan;?>"> <? echo change_date_format($sewing_in_date_arr[$value['id']]) ; ?></td>                    
                        <td width="80" style="word-break:break-all" rowspan="<?=$rowspan;?>"><a href="##"  onclick="openmypage_ex_factory(<? echo $value['id'];?>,<? echo $sample_key;?>,'ex_factory_date_view');" > View </a></td>
                        <? $i++;}?>
                      
                        <td width="110" style="word-break:break-all"><? echo $garments_item[$gmts_key]; ?></td>
                        <td width="105" align="right"><? echo $value['sample_prod_qty'] ; ?></td>
                        <td width="105" align="right"></td>
                        <td width="90" align="right"></td>
                        <td width="80" align="right"><?=$all_data_arr[$string2]['cutting_qty']  ; ?></td>
                        <td width="90" align="right" ><?=$all_data_arr[$string2]['cutting_qty']  ; ?> </td>
                        <td width="90" align="right"><?=$all_data_arr[$string2]['print_issue']; ?></td>
                        <td width="90" align="right"><?=$all_data_arr[$string2]['print_rcv']; ;?></td>
                        <td width="70" align="right"><?=$all_data_arr[$string2]['embr_issue']; ?></td>
                        <td width="70" align="right"><?=$all_data_arr[$string2]['embr_rcv']; ?></td>
                        <td width="90" align="right"><?=$all_data_arr[$string2]['spwork_issue']; ?> </td>
                        <td width="90" align="right"><?=$all_data_arr[$string2]['spwork_rcv']; ?>  </td>
                        <td width="80" align="right"><?=$all_data_arr[$string2]['sewing_in']; ?></td>
                        <td width="80" align="right"><?=$all_data_arr[$string2]['sewing_out']; ?></td>
                        <td width="80" align="right"><?=$all_data_arr[$string2]['wash_issue'];  ?></td>
                        <td width="70" align="right"><?=$all_data_arr[$string2]['wash_rcv']; ?></td>
                        <td width="80" align="center"><a href="##"  onclick="openmypage_reject_qty(<? echo $value['id'];?>,'sample_other_reject_view');" ><?=$all_data_arr[$string2]['other_reject'];?></a></td>
                        <td width="90" align="center"><a href="##"  onclick="openmypage_reject_qty(<? echo $value['id'];?>,<? echo $sample_key;?>,'sample_sewing_reject_view');" > 
                        <?=$all_data_arr[$string2]['sewing_reject'];?> </a></td>
                        <td width="105"><?=$all_data_arr[$string2]['delivery_qty']; ?></td>
                        <td width="95" align="right"><?=$all_data_arr[$string2]['ex_factory_qty'] ; ?></td>
                        <td width="80"><?=$shipment_status[$all_data_arr[$string2]['shiping_status']]; ?></td>
                        <td align="center"><a href="##"  onclick="openmypage_remark(<? echo $value['id'];?>,'remarks_view',<? echo $value['id'];?>);" > View </a></td>
                    </tr>
                <?
                $rs++;

                $tot_sample_prod_qty+=$value['sample_prod_qty'];
                $tot_cutting_qty+=$all_data_arr[$string2]['cutting_qty'];
                $tot_cutting_bal+=$all_data_arr[$string2]['cutting_qty'];
                $tot_print_issue+=$all_data_arr[$string2]['print_issue'];
                $tot_print_rcv+=$all_data_arr[$string2]['print_rcv'];
                $tot_embr_issue+=$all_data_arr[$string2]['embr_issue'];
                $tot_embr_rcv+=$all_data_arr[$string2]['embr_rcv'];
                $tot_spwork_issue+=$all_data_arr[$string2]['spwork_issue'];
                $tot_spwork_rcv+=$all_data_arr[$string2]['spwork_rcv'];
                $tot_sewing_in+=$all_data_arr[$string2]['sewing_in'];
                $tot_sewing_out+=$all_data_arr[$string2]['sewing_out'];
                $tot_wash_issue+=$all_data_arr[$string2]['wash_issue'];
                $tot_wash_rcv+=$all_data_arr[$string2]['wash_rcv'];
                $tot_other_reject+=$all_data_arr[$string2]['other_reject'];
                $tot_sewing_reject+=$all_data_arr[$string2]['sewing_reject'];
                $tot_delivery_qty+=$all_data_arr[$string2]['delivery_qty'];
                $tot_ex_factory_qty+=$all_data_arr[$string2]['ex_factory_qty'];


                }
            }
        }
                ?>
            </tbody>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
        <tr>
                    <td width="30" align="center">&nbsp;</td>                  
                    <td width="95" align="center">&nbsp;</td>
                    <td width="110" align="center">&nbsp;</td>
                    <td width="100" align="center">&nbsp;</td>
                    <td width="45" align="center">&nbsp;</td>
                    <td width="110" align="center" >&nbsp;</td>
                    <td width="70"align="center">&nbsp;</td>
                    <td width="90" align="center">&nbsp;</td>
                    <td width="60" align="center">&nbsp;</td>
                    <td width="100" align="center">&nbsp;</td>
                    <td width="100" align="center">&nbsp;</td>
                    <td width="150" align="center">&nbsp;</td>
                    <td width="95" align="center">&nbsp;</td>
                    <td width="80" align="center"></td>
                    <td width="110" align="right">&nbsp;<b>Total</b> </td>
                    <td width="105" align="right" id="sample_req_qty"><b><?=$tot_sample_prod_qty;?></b></td>
                    <td width="105" align="center">&nbsp;</td>
                    <td width="90" align="center">&nbsp;</td>
                    <td width="80" align="right" id="cuttting_qty"><b><?=$tot_cutting_qty;?></b></td>
                    <td width="90" align="right" id="cuttting_bal">&nbsp;  <b><?=$tot_cutting_bal;?></b> </td>
                    <td width="90" align="right" id="print_issue">&nbsp;  <b><?=$tot_print_issue;?> </b>       </td>
                    <td width="90" align="right" id="print_rcv">&nbsp;   <b><?=$tot_print_rcv;?> </b>      </td>
                    <td width="70" align="right" id="embro_issue"><b><?=$tot_embr_issue;?></b></td>
                    <td width="70" align="right" id="embro_rcv"><b><?=$tot_embr_rcv;?></b></td>
                    <td width="90" align="right" id="spworks_issue">&nbsp;   <b> <?=$tot_spwork_issue;?>  </b>    </td>
                    <td width="90" align="right" id="spworks_rcv">&nbsp;   <b><?=$tot_spwork_rcv;?>   </b>    </td>
                    <td width="80" align="right" id="sewing_input_qty"><b><?=$tot_sewing_in;?></b></td>
                    <td width="80" align="right" id="sewing_output_qty"><b><?=$tot_sewing_out;?></b></td>
                    <td width="80" align="right" id="wash_issue"><b><?=$tot_wash_issue;?></b></td>
                    <td width="70" align="right" id="wash_rcv"><b><?=$tot_wash_rcv;?></b></td>
                    <td width="80" align="right" id="others_reject"><b><?=$tot_other_reject;?></b></td>
                    <td width="90" align="right" id="sewing_reject">&nbsp;<b><?=$tot_sewing_reject;?></b></td>
                    <td width="105" align="right" id="delivery_qty">&nbsp;<b><?=$tot_delivery_qty;?></b></td>
                    <td width="95" align="right" id="ex_factory_qty">&nbsp;<b><?=$tot_ex_factory_qty;?></b></td>
                    <td width="80" align="center">&nbsp;</td>
                    <td align="center">&nbsp;</td>
                </tr>
			</table>
        </div>
        
    </div>
	<?
	exit();
}





if($action=='comments_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend>Cutting</legend>
        <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=127 and is_deleted=0 and status_active=1";
              // echo $sql;die;
             echo  create_list_view ( "list_view_1", "Date,Cutting Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');

      
	    ?>
    </fieldset>

    <fieldset>
    <legend>Sewing Output</legend>
        <?
             $sql= "select b.id, b.sample_sewing_output_mst_id, b.sample_name,b.sample_dtls_row_id,b.item_number_id, b.sewing_date, b.qc_pass_qty,b.reject_qty, b.remarks from sample_sewing_output_dtls b,sample_sewing_output_mst a where a.id=b.sample_sewing_output_mst_id and b.sample_dtls_row_id='$sample_dtls_id' and a.sample_development_id=$smp_dev_mst_id and b.entry_form_id=130 and b.is_deleted=0 and b.status_active=1";
              // echo $sql;die; 
             echo  create_list_view ( "list_view_1", "Date,Sewing Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');

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
                echo  create_list_view ( "list_view_1", "Date,Delivery Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "delivery_date,ex_factory_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
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
             echo  create_list_view ( "list_view_1", "Date,Print Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
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
             echo  create_list_view ( "list_view_1", "Date,Embroidery Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
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
             echo  create_list_view ( "list_view_1", "Date,Wash Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
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
             echo  create_list_view ( "list_view_1", "Date,Special Works Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
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
             echo  create_list_view ( "list_view_1", "Date,Dyeing Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
            }

        ?>
                </fieldset>




 <?
}
if($action=='sample_other_reject_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend>Cutting</legend>
        <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=127 and is_deleted=0 and status_active=1";
              // echo $sql;die;
             echo  create_list_view ( "list_view_1", "Date,Cutting Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');

      
	    ?>
    </fieldset>

    <fieldset>
    <legend>Sewing Output</legend>
        <?
             $sql= "select b.id, b.sample_sewing_output_mst_id, b.sample_name,b.sample_dtls_row_id,b.item_number_id, b.sewing_date, b.qc_pass_qty,b.reject_qty, b.remarks from sample_sewing_output_dtls b,sample_sewing_output_mst a where a.id=b.sample_sewing_output_mst_id and b.sample_dtls_row_id='$sample_dtls_id' and a.sample_development_id=$smp_dev_mst_id and b.entry_form_id=130 and b.is_deleted=0 and b.status_active=1";
              // echo $sql;die; 
             echo  create_list_view ( "list_view_1", "Date,Sewing Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');

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
                echo  create_list_view ( "list_view_1", "Date,Delivery Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "delivery_date,ex_factory_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
             }


        ?>
             </fieldset>



        <?
     
             $sql=  "select a.id,  a.sample_name, b.color_id as sample_color,a.sample_dtls_row_id,a.item_number_id,a.qc_pass_qty,a.reject_qty from sample_sewing_output_dtls a, sample_sewing_output_colorsize b  where a.id=b.sample_sewing_output_dtls_id and a.sample_dtls_row_id='$sample_dtls_id' and a.entry_form_id=128 and a.is_deleted=0 and a.status_active=1 and a.embel_name=1";
             
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Print</legend>

                <?
             echo  create_list_view ( "list_view_1", "Sample Name,Color Name,Total Qty", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sample_name,sample_color,reject_qty", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
            }

        ?>
                </fieldset>


                   <?
           
           $sql=  "select a.id,  a.sample_name, b.color_id as sample_color,a.sample_dtls_row_id,a.item_number_id,a.qc_pass_qty,a.reject_qty from sample_sewing_output_dtls a, sample_sewing_output_colorsize b  where a.id=b.sample_sewing_output_dtls_id and a.sample_dtls_row_id='$sample_dtls_id' and a.entry_form_id=128 and a.is_deleted=0 and a.status_active=1 and a.embel_name=2";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Embroidery</legend>

                <?
           echo  create_list_view ( "list_view_1", "Sample Name,Color Name,Total Qty", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sample_name,sample_color,reject_qty", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
            }

        ?>
                </fieldset>


                 <?
           
             $sql=  "select a.id,  a.sample_name, b.color_id as sample_color,a.sample_dtls_row_id,a.item_number_id,a.qc_pass_qty,a.reject_qty from sample_sewing_output_dtls a, sample_sewing_output_colorsize b  where a.id=b.sample_sewing_output_dtls_id and a.sample_dtls_row_id='$sample_dtls_id' and a.entry_form_id=128 and a.is_deleted=0 and a.status_active=1 and a.embel_name=3";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Wash</legend>

                <?
                  echo  create_list_view ( "list_view_1", "Sample Name,Color Name,Total Qty", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sample_name,sample_color,reject_qty", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
            }

        ?>
                </fieldset>



                 <?
          
          $sql=  "select a.id,  a.sample_name, b.color_id as sample_color,a.sample_dtls_row_id,a.item_number_id,a.qc_pass_qty,a.reject_qty from sample_sewing_output_dtls a, sample_sewing_output_colorsize b  where a.id=b.sample_sewing_output_dtls_id and a.sample_dtls_row_id='$sample_dtls_id' and a.entry_form_id=128 and a.is_deleted=0 and a.status_active=1 and a.embel_name=4";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Special Works</legend>

                <?
             echo  create_list_view ( "list_view_1", "Date,Special Works Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
            }

        ?>
                </fieldset>



 <?
}
if($action=='remarks_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend>Cutting</legend>
        <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=127 and is_deleted=0 and status_active=1";
              // echo $sql;die;
             echo  create_list_view ( "list_view_1", "Date,Cutting Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');

      
	    ?>
    </fieldset>

    <fieldset>
    <legend>Sewing Output</legend>
        <?
             $sql= "select b.id, b.sample_sewing_output_mst_id, b.sample_name,b.sample_dtls_row_id,b.item_number_id, b.sewing_date, b.qc_pass_qty,b.reject_qty, b.remarks from sample_sewing_output_dtls b,sample_sewing_output_mst a where a.id=b.sample_sewing_output_mst_id and b.sample_dtls_row_id='$sample_dtls_id' and a.sample_development_id=$smp_dev_mst_id and b.entry_form_id=130 and b.is_deleted=0 and b.status_active=1";
              // echo $sql;die; 
             echo  create_list_view ( "list_view_1", "Date,Sewing Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');

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
                echo  create_list_view ( "list_view_1", "Date,Delivery Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "delivery_date,ex_factory_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
             }


        ?>
             </fieldset>



        <?
     
             $sql=  "select a.id,  a.sample_name, b.color_id as sample_color,a.sample_dtls_row_id,a.item_number_id,a.qc_pass_qty,a.reject_qty from sample_sewing_output_dtls a, sample_sewing_output_colorsize b  where a.id=b.sample_sewing_output_dtls_id and a.sample_dtls_row_id='$sample_dtls_id' and a.entry_form_id=128 and a.is_deleted=0 and a.status_active=1 and a.embel_name=1";
             
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Print</legend>

                <?
             echo  create_list_view ( "list_view_1", "Sample Name,Color Name,Total Qty", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sample_name,sample_color,reject_qty", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
            }

        ?>
                </fieldset>


                   <?
           
           $sql=  "select a.id,  a.sample_name, b.color_id as sample_color,a.sample_dtls_row_id,a.item_number_id,a.qc_pass_qty,a.reject_qty from sample_sewing_output_dtls a, sample_sewing_output_colorsize b  where a.id=b.sample_sewing_output_dtls_id and a.sample_dtls_row_id='$sample_dtls_id' and a.entry_form_id=128 and a.is_deleted=0 and a.status_active=1 and a.embel_name=2";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Embroidery</legend>

                <?
           echo  create_list_view ( "list_view_1", "Sample Name,Color Name,Total Qty", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sample_name,sample_color,reject_qty", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
            }

        ?>
                </fieldset>


                 <?
           
             $sql=  "select a.id,  a.sample_name, b.color_id as sample_color,a.sample_dtls_row_id,a.item_number_id,a.qc_pass_qty,a.reject_qty from sample_sewing_output_dtls a, sample_sewing_output_colorsize b  where a.id=b.sample_sewing_output_dtls_id and a.sample_dtls_row_id='$sample_dtls_id' and a.entry_form_id=128 and a.is_deleted=0 and a.status_active=1 and a.embel_name=3";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Wash</legend>

                <?
                  echo  create_list_view ( "list_view_1", "Sample Name,Color Name,Total Qty", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sample_name,sample_color,reject_qty", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
            }

        ?>
                </fieldset>



                 <?
          
          $sql=  "select a.id,  a.sample_name, b.color_id as sample_color,a.sample_dtls_row_id,a.item_number_id,a.qc_pass_qty,a.reject_qty from sample_sewing_output_dtls a, sample_sewing_output_colorsize b  where a.id=b.sample_sewing_output_dtls_id and a.sample_dtls_row_id='$sample_dtls_id' and a.entry_form_id=128 and a.is_deleted=0 and a.status_active=1 and a.embel_name=4";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Special Works</legend>

                <?
             echo  create_list_view ( "list_view_1", "Date,Special Works Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');
            }

        ?>
                </fieldset>



 <?
}
if($action=='sample_sewing_reject_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');


    
    $sql= "select b.id, b.sample_sewing_output_mst_id, b.sample_name,b.sample_dtls_row_id,b.item_number_id, b.sewing_date, b.qc_pass_qty,b.reject_qty, b.remarks from sample_sewing_output_dtls b,sample_sewing_output_mst a where a.id=b.sample_sewing_output_mst_id and b.sample_dtls_row_id='$sample_dtls_id' and a.sample_development_id=$smp_dev_mst_id and b.entry_form_id=130 and b.is_deleted=0 and b.status_active=1";
     // echo $sql;die;
     if(count(sql_select($sql))>0)
     {
 ?>
    <fieldset>
        <legend>Sewing Output</legend>
        <?
             $sql= "select b.id, b.sample_sewing_output_mst_id, b.sample_name,b.sample_dtls_row_id,b.item_number_id, b.sewing_date, b.qc_pass_qty,b.reject_qty, b.remarks from sample_sewing_output_dtls b,sample_sewing_output_mst a where a.id=b.sample_sewing_output_mst_id and b.sample_dtls_row_id='$sample_dtls_id' and a.sample_development_id=$smp_dev_mst_id and b.entry_form_id=130 and b.is_deleted=0 and b.status_active=1";
              // echo $sql;die; 
             echo  create_list_view ( "list_view_1", "Date,Sewing Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../../requires/sample_rmg_production_report_v2_controller", '','3,1,0');

        ?>
    </fieldset>




 <? 
     }
}
if($action=='ex_factory_date_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("MnM Ex-Factory Date Details", "../../../", 1, 1,$unicode,'','');
	?>
	<fieldset>
        <legend> MnM Ex-Factory Date Details</legend>
        <div style="width:410px; margin-top:10px">
            <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
                <thead>
                    <th width="40">SL</th>
                    <th width="100">Delivery Date</th>
                    <th width="100">Delivery Challan No.</th>
                    <th width="100">Delivery Qty</th>
                   
                </thead>
                <?
                $sql="SELECT c.sys_number,  a.ex_factory_qty, a.delivery_date	from sample_ex_factory_mst c, sample_ex_factory_dtls a, sample_ex_factory_colorsize b where c.id=a.sample_ex_factory_mst_id and a.id=b.sample_ex_factory_dtls_id and a.sample_development_id = $sample_req_mst_id and a.sample_name=$sample_name  and c.status_active=1 and c.is_deleted=0 and a.status_active=1  and a.is_deleted=0 and a.entry_form_id=132 and b.entry_form_id=132 and c.entry_form_id=132 and b.color_id IS NOT NULL";

               
                $sql_sel=sql_select($sql);
           
                $i=1;$total=0;
                foreach($sql_sel as  $value)
                {
					
						?>
						<tr>
							
                            <td width="40"  align="center"><? echo $i; ?></td>
                            <td width="100" align="center"><?=$value[csf('delivery_date')];?></td>
                            <td width="100" align="center"><?=$value[csf('sys_number')]; ?></td>
                            <td width="100" align="center"><? echo $value[csf('ex_factory_qty')]; ?></td>
						</tr>
						<?
						$i++;
						$total+=$value[csf('ex_factory_qty')];
						
                }
                ?>
                <tr>
                    <td colspan="3" align="right"><b> Grand Total</b></td>
                    <td align="center"><b><? echo $total; ?></b></td>
                </tr>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}
if($action=='reject_qty_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("Remarks", "../../.../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend>Cuttings Reject</legend>
        <?
             $sql= "select sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sum(qc_pass_qty) as qc,sum(reject_qty) as reject from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=127 and is_deleted=0 and status_active=1 group by   sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id";
             //echo $sql;die;
             $arr =array(0=>$sample_name_arr,1=>$garments_item);
              echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../requires/sample_rmg_production_report_v2_controller", '','0,0,0');

        ?>
    </fieldset>

     <fieldset>
    <legend>Embellishment Reject</legend>
        <?
             $sql= "select sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id,   sum(qc_pass_qty) as qc,sum(reject_qty) as reject  from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=128 and is_deleted=0 and status_active=1 group by  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id  ";
              // echo $sql;die;
              $arr =array(0=>$sample_name_arr,1=>$garments_item);
             echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');

        ?>
    </fieldset>


    <fieldset>
    <legend>Sewing Reject</legend>
        <?
             $sql= "select sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id,   sum(qc_pass_qty) as qc,sum(reject_qty) as reject  from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=130 and is_deleted=0 and status_active=1 group by  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id  ";
              // echo $sql;die;
              $arr =array(0=>$sample_name_arr,1=>$garments_item);
             echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');

        ?>
    </fieldset>

    <fieldset>
    <legend>Dyeing and Wash Reject</legend>
        <?
             $sql= "select  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id,   sum(qc_pass_qty) as qc,sum(reject_qty) as reject  from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=131 and is_deleted=0 and status_active=1 group by  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id  ";
             $arr =array(0=>$sample_name_arr,1=>$garments_item);
           //  echo $sql;die;
             echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0,0');

        ?>
    </fieldset>


 <?
}




?>