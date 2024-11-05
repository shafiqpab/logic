<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_name=$_SESSION['logic_erp']['user_id'];



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
    $booking_no=str_replace("'", "", $txt_booking_no);
     
    $group=str_replace("'", "", $txt_internal_ref);
    $file=str_replace("'", "", $txt_file_no);
    $order=str_replace("'", "", $txt_order_no);
    $txt_job=str_replace("'", "", $txt_job_no);
    $sample_year=str_replace("'", "", $cbo_year);
    $report_category=str_replace("'", "", $cbo_report_category);
    $year_cond=""; $year_cond2="";
    if($db_type==2)
    {
        $year_cond=($sample_year)? " and  to_char(a.insert_date,'YYYY')=$sample_year" : " ";
		 $year_cond2=($sample_year)? " and  to_char(c.insert_date,'YYYY')=$sample_year" : " ";
    }
    else
    {
        $year_cond=($sample_year)? " and year(a.insert_date)=$sample_year" : " ";
    }

 	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_id=$cbo_company_name";
	if(str_replace("'","",$cbo_company_name)==0) $company_name_cond=""; else $company_name_cond=" and c.company_id=$cbo_company_name";
	if(str_replace("'","",$cbo_sample_stage)==0) $sample_stages=""; else $sample_stages=" and a.sample_stage_id=$cbo_sample_stage";
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
 	if(str_replace("'","",trim($cbo_dealing_merchant))==0) $dealing_merchant=""; else $dealing_merchant=" and a.dealing_marchant=$cbo_dealing_merchant";

	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))==""){ $txt_date="";
    }else{ 
        $txt_date2=""; $txt_date=""; $txt_date3=""; $txt_date4="";$booking_date_cond="";$exfact_date="";
        if($report_category==2){
            $txt_date2=" and b.sewing_date between $txt_date_from and $txt_date_to";
			$booking_date_cond=" and b.sewing_date between $txt_date_from and $txt_date_to";
			$booking_date_cond2=" and b.sewing_date between $txt_date_from and $txt_date_to";
			$booking_date_cond3=" and d.sewing_date between $txt_date_from and $txt_date_to";
		    $txt_date3=" and e.requisition_date between $txt_date_from and $txt_date_to";
		  // $txt_date3=" and e.requisition_date between $txt_date_from and $txt_date_to";
            $txt_date4=" and a.delivery_date  between $txt_date_from and $txt_date_to";//
			$exfact_date=" and a.ex_factory_date  between $txt_date_from and $txt_date_to";//
        }else{
            $booking_date_cond=" and c.requisition_date between $txt_date_from and $txt_date_to";
			$booking_date_cond2="";
			$booking_date_cond3="and a.requisition_date between $txt_date_from and $txt_date_to";
			 
			// $txt_date3=" and e.requisition_date between $txt_date_from and $txt_date_to";
        }
    }

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
    $sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
    $season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0","id","season_name");
	if($req_no=="") $req_no_cond=""; else $req_no_cond=" and c.requisition_number_prefix_num like '%$req_no%' ";
	if($req_no=="") $req_no=""; else $req_no=" and a.requisition_number_prefix_num like '%$req_no%' ";
    if($booking_no=="") $booking_no=""; else $booking_no_cond=" and a.booking_no_prefix_num like '%$booking_no%' ";
	if(str_replace("'","",$txt_internal_ref)=="") $internal_ref_cond="";else $internal_ref_cond=" and c.internal_ref=$txt_internal_ref";
    $fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
    $toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
 if($report_category==1)//Booking Date
 { 
    //  $wo_without_order_sql=sql_select("SELECT b.style_id,a.booking_no from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b,sample_development_mst c where a.booking_no=b.booking_no and c.id=b.style_id and a.entry_form_id = 140 and c.entry_form_id=203  and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.status_active=1 $company_name $booking_date_cond $booking_no_cond  $req_no_cond $year_cond $internal_ref_cond  ");
		 $booking_reqId_cond=""; $booking_reqId_cond2="";
		 
		if($booking_no!=="") 
	   {
		 $booking_with_sql=sql_select("SELECT b.style_id,a.booking_no from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no $booking_no_cond $year_cond and a.status_active=1 and b.status_active=1 $reqId_cond $company_name  group by  b.style_id,a.booking_no");
		foreach($booking_with_sql as $vals)
		{
		   $booking_with_arr[$vals[csf("style_id")]]=$vals[csf("style_id")];
		}
		$booking_reqId_cond=where_con_using_array($booking_with_arr,0,'c.id');
	//	$booking_reqId_cond2=where_con_using_array($booking_with_arr,0,'b.sample_development_id');
	   }
   
	   $wo_without_order_sql=sql_select("SELECT c.id as style_id from sample_development_mst c where   c.entry_form_id=203   and c.status_active=1 $company_name_cond $booking_date_cond    $req_no_cond $year_cond2 $internal_ref_cond $booking_reqId_cond ");
	  // echo "SELECT c.id as style_id from sample_development_mst c where   c.entry_form_id=203   and c.status_active=1 $company_name_cond $booking_date_cond    $req_no_cond $year_cond2 $internal_ref_cond $booking_reqId_cond ";
	 
    $reqIdArr=array();
    foreach($wo_without_order_sql as $vals)
    {
       // $booking_without_order_arr[$vals[csf("style_id")]]=$vals[csf("booking_no")];
        $reqIdArr[$vals[csf("style_id")]]=$vals[csf("style_id")];
		// $reqIdArr[$vals[csf("style_id")]]=$vals[csf("style_id")];
    }
   
 }
 else if($report_category==2){ //Transaction Date
 $booking_reqId_cond=""; $booking_reqId_cond2="";
   if($booking_no!=="") 
   {
	 $booking_with_sql=sql_select("SELECT b.style_id,a.booking_no from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no $booking_no_cond $year_cond and a.status_active=1 and b.status_active=1 $reqId_cond $company_name  group by  b.style_id,a.booking_no");
    foreach($booking_with_sql as $vals)
    {
       $booking_with_arr[$vals[csf("style_id")]]=$vals[csf("style_id")];
    }
	$booking_reqId_cond=where_con_using_array($booking_with_arr,0,'a.sample_development_id');
	$booking_reqId_cond2=where_con_using_array($booking_with_arr,0,'b.sample_development_id');
   }
 	 $wo_without_order_sql=sql_select("SELECT a.sample_development_id as style_id from sample_sewing_output_mst a,sample_sewing_output_dtls b,sample_development_mst c where a.id=b.sample_sewing_output_mst_id and a.sample_development_id=c.id  and a.status_active=1 and c.status_active=1 $company_name  $booking_reqId_cond $booking_date_cond $req_no_cond $year_cond $internal_ref_cond  ");
	  // echo "SELECT a.sample_development_id as style_id from sample_sewing_output_mst a,sample_sewing_output_dtls b,sample_development_mst c where a.id=b.sample_sewing_output_mst_id and a.sample_development_id=c.id  and a.status_active=1 and c.status_active=1 $company_name  $booking_reqId_cond $booking_date_cond $req_no_cond $year_cond $internal_ref_cond  ";
    $reqIdArr=array();
    foreach($wo_without_order_sql as $vals)
    {
        $booking_without_order_arr[$vals[csf("style_id")]]=$vals[csf("booking_no")];
        $reqIdArr[$vals[csf("style_id")]]=$vals[csf("style_id")];
    }
	 $wo_ex_fact_sql=sql_select("SELECT b.sample_development_id as style_id from sample_ex_factory_mst a,sample_ex_factory_dtls b,sample_development_mst c where a.id=b.sample_ex_factory_mst_id and b.sample_development_id=c.id  and a.status_active=1 and c.status_active=1 $company_name  $booking_reqId_cond2 $exfact_date $req_no_cond $year_cond $internal_ref_cond  ");// Sample Delivery Entry//Sample Delivery To MKT
	//echo "SELECT b.sample_development_id as style_id from sample_ex_factory_mst a,sample_ex_factory_dtls b,sample_development_mst c where a.id=b.sample_ex_factory_mst_id and b.sample_development_id=c.id  and a.status_active=1 and c.status_active=1 $company_name  $booking_reqId_cond2 $exfact_date $req_no_cond $year_cond $internal_ref_cond  ";
  if( count($wo_ex_fact_sql>0))
  {  
    foreach($wo_ex_fact_sql as $vals)
    {
      //  $booking_without_order_arr[$vals[csf("style_id")]]=$vals[csf("booking_no")];
        $reqIdArr[$vals[csf("style_id")]]=$vals[csf("style_id")];
    }
  }
	 
 }
$reqId_cond=where_con_using_array($reqIdArr,0,'b.style_id');
$reqId_cond2=where_con_using_array($reqIdArr,0,'a.id');
$reqId_cond3=where_con_using_array($reqIdArr,0,'a.sample_development_id');

	 $booking_without_order_sql=sql_select("SELECT b.style_id,a.booking_no from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no $booking_no_cond $year_cond and a.status_active=1 and b.status_active=1 $reqId_cond  group by  b.style_id,a.booking_no");
	//echo "SELECT b.style_id,a.booking_no from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no $booking_no_cond $year_cond and a.status_active=1 and b.status_active=1  group by  b.style_id,a.booking_no";
    foreach($booking_without_order_sql as $vals)
    {
       $booking_without_order_arr[$vals[csf("style_id")]]=$vals[csf("booking_no")];
    }

    $all_data_arr=array(); 
          $prod_sql="SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name, b.sewing_date, b.entry_form_id, sum(c.size_pass_qty) as qc_pass_qty, b.reject_qty as reject,c.color_id 
            from sample_sewing_output_mst a left join sample_development_mst e on a.sample_development_id=e.id,
                sample_sewing_output_dtls b,
                sample_sewing_output_colorsize c  
            where a.id=b.sample_sewing_output_mst_id and 
                b.id=c.sample_sewing_output_dtls_id and 
                b.entry_form_id in (127,128,130,131,337,338) and 
                a.status_active=1 and 
                a.is_deleted=0 and 
                b.status_active=1 and 
                b.is_deleted=0 and 
                c.status_active=1  and 
                c.is_deleted=0 $reqId_cond3  $booking_date_cond2    
            group by  a.sample_development_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name, b.sewing_date, b.entry_form_id, b.reject_qty,c.color_id";
        
    $prod_sql_data=sql_select($prod_sql);
    $string="";$reqIdArr2=array();
    foreach($prod_sql_data as $val)
    {
        $string=$val[csf('smp_dev_id')].'*'.$val[csf('sample_name')].'*'.$val[csf('item_number_id')].'*'.$val[csf('color_id')];
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
			else if($val[csf("embel_name")]==5){
                $all_data_arr[$string]['embl_issue']+=$val[csf('qc_pass_qty')];
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
			else if($val[csf("embel_name")]==5){
                $all_data_arr[$string]['embl_recv']+=$val[csf('qc_pass_qty')];
            }
		}
    }
            if($db_type==0) $yearCond="YEAR(a.insert_date)"; else if($db_type==2) $yearCond="to_char(a.insert_date,'YYYY')";
			
			if($report_category==1)//Booking Date
 			{ 
             if($booking_no!=="") {

                $query="SELECT a.id, a.requisition_date, a.quotation_id, a.company_id, a.is_approved, a.is_acknowledge, a.dealing_marchant, a.requisition_number_prefix_num, to_char(a.insert_date,'YYYY') as year,a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.id as dtls_id, b.sample_name, b.gmts_item_id, b.sample_color, b.smv, b.sample_charge, b.sample_curency,
                b.sample_prod_qty, b.delv_start_date, b.delv_end_date, b.is_complete_prod, a.refusing_cause 
                from sample_development_mst a,
                    sample_development_dtls b 
                where a.id=b.sample_mst_id and 
                    a.status_active=1 and 
                    b.is_deleted=0 and 
                    b.status_active=1 and 
                    a.entry_form_id=203 
                    and b.entry_form_id=203    
                $reqId_cond2 $company_name $buyer_name $sample_stages $dealing_merchant $req_no $internal_ref  $year_cond order by b.id DESC";

            }else{
                  $query="SELECT a.id, a.requisition_date, a.quotation_id, a.company_id, a.is_approved, a.is_acknowledge, a.dealing_marchant, a.requisition_number_prefix_num, to_char(a.insert_date,'YYYY') as year, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.id as dtls_id, b.sample_name, b.gmts_item_id, b.sample_color, b.smv, b.sample_charge, b.sample_curency, b.sample_prod_qty, b.delv_start_date, b.delv_end_date, b.is_complete_prod, a.refusing_cause 
                from sample_development_mst a,sample_development_dtls b 
                where a.id=b.sample_mst_id and 
                    a.status_active=1 and 
                    b.is_deleted=0 and 
                    b.status_active=1 and 
                    a.entry_form_id=203 and 
                    b.entry_form_id=203     
                  $reqId_cond2 $company_name $buyer_name $sample_stages $dealing_merchant $req_no  $internal_ref  $year_cond   order by b.id DESC";
           		 }
           // echo  $query;
			}
			else
			{
				 // $query="SELECT a.sample_development_id as id from sample_sewing_output_mst a,sample_sewing_output_dtls b,sample_development_mst c where a.id=b.sample_sewing_output_mst_id and a.sample_development_id=c.id  and a.status_active=1 and c.status_active=1 $company_name  $booking_reqId_cond $booking_date_cond $req_no_cond $year_cond $internal_ref_cond  ";
				/*  $query="SELECT a.id, a.requisition_date, a.quotation_id, a.company_id, a.is_approved, a.is_acknowledge, a.dealing_marchant, a.requisition_number_prefix_num, to_char(a.insert_date,'YYYY') as year, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.id as dtls_id, b.sample_name, b.gmts_item_id, b.sample_color, b.smv, b.sample_charge, b.sample_curency, b.sample_prod_qty, b.delv_start_date, b.delv_end_date, b.is_complete_prod, a.refusing_cause 
                from sample_development_mst a,sample_development_dtls b,sample_sewing_output_mst c,sample_sewing_output_dtls d 
                where a.id=b.sample_mst_id and c.sample_development_id=a.id and c.id=d.sample_sewing_output_mst_id and d.sample_dtls_row_id=b.id and
                    a.status_active=1 and 
                    b.is_deleted=0 and 
                    b.status_active=1 and 
					c.status_active=1 and  d.status_active=1 and 
                    a.entry_form_id=203 and 
                    b.entry_form_id=203  $booking_date_cond3  
                  $reqId_cond2 $company_name $buyer_name $sample_stages $dealing_merchant $req_no  $internal_ref  $year_cond   order by b.id DESC";*/
				  
				     $query="SELECT a.id, a.requisition_date, a.quotation_id, a.company_id, a.is_approved, a.is_acknowledge, a.dealing_marchant, a.requisition_number_prefix_num, to_char(a.insert_date,'YYYY') as year, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.id as dtls_id, b.sample_name, b.gmts_item_id, b.sample_color, b.smv, b.sample_charge, b.sample_curency, b.sample_prod_qty, b.delv_start_date, b.delv_end_date, b.is_complete_prod, a.refusing_cause 
                from sample_development_mst a,sample_development_dtls b
                where a.id=b.sample_mst_id  and
                    a.status_active=1 and 
                    b.is_deleted=0 and 
                    b.status_active=1 and 
					
                    a.entry_form_id=203 and 
                    b.entry_form_id=203  
                  $reqId_cond2 $company_name $buyer_name $sample_stages $dealing_merchant $req_no  $internal_ref  $year_cond   order by b.id DESC";
			}
            $sql=sql_select($query);$sampleQtyChkArr=array();
            foreach ($sql as $key => $value)
            {

                $req_id_arr[$value[csf('id')]]=$value[csf('requisition_number_prefix_num')];
                $reqArr[$value[csf('id')]]=$value[csf('id')];
                $req_wise_data_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('sample_color')]]['color']=$value[csf('sample_color')];
                $req_wise_data_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('sample_color')]]['season']=$value[csf('season')];
                $req_wise_data_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('sample_color')]]['year']=$value[csf('year')];
                $req_wise_data_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('sample_color')]]['sample_stage_id']=$value[csf('sample_stage_id')];
                $req_wise_data_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('sample_color')]]['quotation_id']=$value[csf('quotation_id')];
                $req_wise_data_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('sample_color')]]['requisition_date']=$value[csf('requisition_date')];
                $req_wise_data_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('sample_color')]]['buyer_name']=$value[csf('buyer_name')];
                $req_wise_data_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('sample_color')]]['dtls_id']=$value[csf('dtls_id')];
				if( $sampleQtyChkArr[$value[csf('dtls_id')]]=="")
				{
                $req_wise_data_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('sample_color')]]['sample_prod_qty']+=$value[csf('sample_prod_qty')];
				 $sampleQtyChkArr[$value[csf('dtls_id')]]=$value[csf('dtls_id')];
				}
                $req_wise_data_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('sample_color')]]['style_ref_no']=$value[csf('style_ref_no')];
                $req_wise_data_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('sample_color')]]['is_complete_prod']=$value[csf('is_complete_prod')];
                $req_wise_data_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('sample_color')]]['company_id']=$value[csf('company_id')];
                $req_wise_data_arr[$value[csf('id')]][$value[csf('sample_name')]][$value[csf('gmts_item_id')]][$value[csf('sample_color')]]['id']=$value[csf('id')];
            }

            // echo "<pre>";
            // print_r($req_id_arr);
            $sql_fabric="SELECT id,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,sample_color,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,color_data, determination_id,fabric_source,delivery_date,process_loss_percent,grey_fab_qnty,remarks_ra, collar_cuff_breakdown,yarn_dtls 
            from sample_development_fabric_acc 
            where  form_type=1 and  
                is_deleted=0  and 
                status_active=1 ".where_con_using_array($reqArr,0,'sample_mst_id')." order by id ASC";
            // echo $sql_fabric;
            $sql_resultf =sql_select($sql_fabric); 
            foreach($sql_resultf as $row)
			{
                $sample_req_data_arr[$row[csf('sample_mst_id')]][$row[csf('sample_name')]]['fabric_description'] .=$row[csf('fabric_description')].",";
                $sample_req_data_arr[$row[csf('sample_mst_id')]][$row[csf('sample_name')]]['fin_fab_req_qty']+=$row[csf('required_qty')];
            }
			unset($sql_resultf);
//from_po_id,to_po_id
            $sql_finGmts_transfer="SELECT a.id,1 as sample_name,b.production_type,b.from_po_id,b.to_po_id,b.item_number_id,d.color_id,d.production_qnty 
            from pro_gmts_delivery_mst a,pro_gmts_delivery_dtls b ,pro_garments_production_mst c,pro_garments_production_dtls d
            where  a.id=b.mst_id and c.dtls_id=b.id and c.id=d.mst_id and c.po_break_down_id=b.to_po_id and c.production_type=10 and a.transfer_criteria=2 and b.ENTRY_BREAK_DOWN_TYPE=3   and  
                b.is_deleted=0  and  b.status_active=1 and  a.is_deleted=0  and  a.status_active=1 and  c.is_deleted=0  and  c.status_active=1  and  d.is_deleted=0  and  d.status_active=1  ".where_con_using_array($reqArr,0,'b.to_po_id')."";
             // echo $sql_finGmts_transfer;
            $sql_resultfin_trans =sql_select($sql_finGmts_transfer); 
            foreach($sql_resultfin_trans as $row)
			{
                $transfer_data_arr[$row[csf('to_po_id')]][$row[csf('sample_name')]][$row[csf('item_number_id')]][$row[csf('color_id')]][$row[csf('production_type')]]['prod_qty']+=$row[csf('production_qnty')];
            }
			unset($sql_resultfin_trans);
            // echo "<pre>";
            // print_r($transfer_data_arr);

            $prod_sql2="SELECT   a.sample_development_id as smp_dev_id, a.sample_name, a.gmts_item_id, b.size_pass_qty as ex_factory_qty, a.shiping_status ,b.color_id as sample_color
             from sample_ex_factory_mst c, 
                  sample_ex_factory_dtls a, 
                  sample_ex_factory_colorsize b 
             where c.id=a.sample_ex_factory_mst_id and 
                a.id=b.sample_ex_factory_dtls_id and 
                c.delivery_basis=1  and 
                c.status_active=1  and 
                c.is_deleted=0 and 
                a.status_active=1 and 
                a.is_deleted=0 and 
                a.entry_form_id=132  
                ".where_con_using_array($reqArr,0,'a.sample_development_id')." and 
                b.entry_form_id=132 and 
                c.entry_form_id=132 and 
                b.color_id IS NOT NULL   $txt_date4
            ";
			// group by a.sample_development_id, a.sample_name, a.gmts_item_id, a.ex_factory_qty, a.shiping_status,b.color_id
             $prod_sql2_data=sql_select($prod_sql2);
            $string3="";
            foreach($prod_sql2_data as $val)
            {
                $string3=$val[csf('smp_dev_id')].'*'.$val[csf('sample_name')].'*'.$val[csf('gmts_item_id')].'*'.$val[csf('sample_color')];
                $all_data_arr[$string3]['ex_factory_qty']+=$val[csf('ex_factory_qty')];
                $all_data_arr[$string3]['shiping_status']=$val[csf('shiping_status')];
            }
        unset($prod_sql2_data);
              $prod_sql3="SELECT a.id,a.sample_development_id as smp_dev_id,a.sample_name,a.gmts_item_id, a.ex_factory_qty,b.color_id as sample_color,c.ex_factory_date 
            from sample_ex_factory_dtls a, 
                 sample_ex_factory_colorsize b ,
                 sample_ex_factory_mst c 
            where a.id=b.sample_ex_factory_dtls_id and 
                  a.sample_ex_factory_mst_id=c.id and 
                  a.status_active=1 and 
                  a.is_deleted=0 and 
                  a.entry_form_id=396 and 
                  b.entry_form_id=396   $txt_date4
                ".where_con_using_array($reqArr,0,'a.sample_development_id')."   
            group by a.id,a.sample_development_id ,a.sample_name,a.gmts_item_id, a.ex_factory_qty,b.color_id,c.ex_factory_date";
            $prod_sql3_data=sql_select($prod_sql3);
            $string4="";
            foreach($prod_sql3_data as $val)
            {
                $string4=$val[csf('smp_dev_id')].'*'.$val[csf('sample_name')].'*'.$val[csf('gmts_item_id')].'*'.$val[csf('sample_color')];
                $all_data_arr[$string4]['delivery_qty']+=$val[csf('ex_factory_qty')];
            }
			 unset($prod_sql3_data);
            $req_row_arr=array();
            foreach ($req_wise_data_arr as $req_key => $sample_data) {
                foreach ($sample_data as $sample_key => $gmts_data){
                    $req_rowspan=0;
                  foreach ($gmts_data as $gmts_key => $color_data){
                    foreach ($color_data as $color_key => $value){
                    $req_rowspan++;
                     }
                   }
                  $req_row_arr[$req_key][$sample_key]+=$req_rowspan;
                }
            }
            $checklist_reqId_cond=where_con_using_array($reqArr,0,'a.REQUISITION_ID');
            $sql_chklist=sql_select("select a.REQUISITION_ID,a.GMTS_ITEM_ID,b.SUBMIT_DATE,b.REMARKS,b.CHECKLIST_ID from sample_checklist_dtls b,sample_checklist_mst a where a.id=b.CHECKLIST_MST_ID and b.status_active=1 and a.status_active=1 $checklist_reqId_cond");
             
            
            foreach($sql_chklist as $vals)
            {
              if(strtotime($vals["SUBMIT_DATE"])>0)//
              {
                $chkListArr[$vals["GMTS_ITEM_ID"]][$vals["REQUISITION_ID"]][$vals["CHECKLIST_ID"]]['chk_date']=$vals["SUBMIT_DATE"];
              }
              if($vals["REMARKS"])
              {
                $chkListArr[$vals["GMTS_ITEM_ID"]][$vals["REQUISITION_ID"]][$vals["CHECKLIST_ID"]]['remark']=$vals["REMARKS"];
              }
            }
            // echo "<pre>";
            // print_r($req_row_arr);
            ?>
       <div>
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3720" rules="all" id="table_header" >
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
                  
                    <th width="100">Finish Fabric<br> required qty</th>
                    <th width="150">Fabric <br>Description</th>
                    <th width="95">Delivery  Date</th>
                    <th width="80">MnM Ex.<br>Factory Date</th>
                    <th width="110">Garments Item</th>
                    <th width="120">Gmts Color</th>
                    <th width="105">Sample Req. Qty</th>
                    <th width="70">Pattern</th>
                    <th width="70">Fabric Receive</th>
                    <th width="70">Program Receive</th>
                    <th width="105">Approved Cut %</th>
                    <th width="90">Plan Cut Qty</th>
                    <th width="80">Cutting Qty</th>
                    <th width="90">Cutting Qty<br> Balance</th>
                    <th width="90">Sent to Print</th>
                    <th width="90">Rcv Print</th>
                    <th width="70">Sent to Emb</th>
                    <th width="70">Rcv Emb</th>
                    <th width="90">Sent to<br> Sp. Works</th>
                    <th width="90">Rcv Sp. Works</th>
                    <th width="80">Sewing <br>Input Qty</th>
                    <th width="80">Sewing <br>Output Qty</th>                 
                    <th width="80">Sent to Wash</th>
                    <th width="70">Rcv Wash</th>
                    <th width="70">Gmt Dyeing<br> Sent </th>
                    <th width="70">Gmt. Dyeing<br> Rcv </th>
                    <th width="80">Reject <br>Qty Others</th>
                    <th width="90">Sample Sewing<br>Reject Qty</th>
                    <th width="105">Delivery<br> to MnM</th>
                    <th width="70">Transfer In</th>
                    <th width="70">Transfer Out</th>
                    <th width="70">Available Qty</th>
                    <th width="95">Ex-Factory<br> MnM</th>
                    <th width="100">Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:400px; overflow-y:scroll; width:3740px;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"   width="3720" rules="all" id="table_body">
            
            <?
            $i=1;$string2="";$tot_fin_fab_issue_qty=0;
            foreach ($req_wise_data_arr as $req_key => $sample_data)
            {
                foreach ($sample_data as $sample_key => $gmts_data)
                {
                    $rs=1;
                    foreach ($gmts_data as $gmts_key => $color_data){
                        foreach ($color_data as $color_key => $value){
                        $string2=$value['id'].'*'.$sample_key.'*'.$gmts_key.'*'.$color_key;
                        $string3=$sample_name_arr[$sample_key].'*'.$color_arr[$color_key].'*'.$value['sample_prod_qty'];
					//	echo $string2.'='.$all_data_arr[$string2]['cutting_qty'].'<br>';
                    $transfer_in=$transfer_data_arr[$value['id']][$sample_key][$gmts_key][$color_key][5]['prod_qty'];
                    $transfer_out=$transfer_data_arr[$value['id']][$sample_key][$gmts_key][$color_key][6]['prod_qty'];
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                        <?php
                        if($rs==1){
                            $rowspan=$req_row_arr[$req_key][$sample_key];
							 $tot_fin_fab_issue_qty+=$sample_req_data_arr[$req_key][$sample_key]['fin_fab_req_qty'];
                            ?>
                        <td width="30" align="center" rowspan="<?=$rowspan;?>"><? echo $i; ?></td>
                        <td width="95" align="center" style="word-wrap:break-word; width:94px"  rowspan="<?=$rowspan;?>" title="<?=$value['id'];?>"><? echo  $req_id_arr[$req_key]; ?></td>
                        <td width="110" style="word-wrap:break-word; width:108px" rowspan="<?=$rowspan;?>" title="<?='Sample Stage id='.$value['sample_stage_id'];?>"><? echo $sample_name_arr[$sample_key]; ?></td>
                        <td width="100" style="word-wrap:break-word; width:98px" rowspan="<?=$rowspan;?>"><p><? echo $buyer_arr[$value['buyer_name']]; ?></p></td>
                        <td width="45" align="center" rowspan="<?=$rowspan;?>"><? echo  $value['year'] ; ?></td>       
                        <td width="110" align="center" style="word-wrap:break-word; width:108px" rowspan="<?=$rowspan;?>">
						<?  
                            echo $booking_without_order_arr[$value["id"]];
                         ?>
                        </td>
                        <td width="70" align="center" rowspan="<?=$rowspan;?>"><p><? echo $season_arr[$value['season']] ; ?></p></td>
                        <td width="90" style="word-wrap:break-word; width:88px" rowspan="<?=$rowspan;?>"><p><? echo  $value['style_ref_no'] ; ?></p></td>
                     
                        <td width="100" style="word-wrap:break-word; width:99px" rowspan="<?=$rowspan;?>" align="right"><?  echo $sample_req_data_arr[$req_key][$sample_key]['fin_fab_req_qty']; ?></td>
                        <td width="150" style="word-wrap:break-word; width:148px" rowspan="<?=$rowspan;?>"><div style="word-wrap:break-word; width:148px"><? echo $sample_req_data_arr[$req_key][$sample_key]['fabric_description']; ?></div></td>
                        <td width="95" align="center" rowspan="<?=$rowspan;?>"> <? echo change_date_format($sewing_in_date_arr[$value['id']]) ; ?></td>                    
                        <td width="80" style="word-wrap:break-word; width:79px" rowspan="<?=$rowspan;?>"><a href="##"  onclick="openmypage_ex_factory(<? echo $value['id'];?>,<? echo $sample_key;?>,'ex_factory_date_view');" > View </a></td>
                        <? $i++;}?>
                        <td width="110" style="word-wrap:break-word; width:108px"><p><? echo $garments_item[$gmts_key]; ?></p></td>
                        <td width="120" style="word-break:break-all" title="<?=$color_key;?>"><p><? echo $color_arr[$color_key]; ?></p></td>
                        <td width="105" align="right"><? ; ?>
                        <a href="##"  onclick="openmypage_sample_req('<?=$string3;?>','sample_req_qty_view');" ><?=$value['sample_prod_qty']  ; ?></a> </td>
                        <td width="70" align="center" title="<?=$chkListArr[$gmts_key][$req_key][440]['remark'];?>"><?=$chkListArr[$gmts_key][$req_key][440]['chk_date'];?></td>
                        <td width="70" align="center" title="<?=$chkListArr[$gmts_key][$req_key][442]['remark'];?>"><?=$chkListArr[$gmts_key][$req_key][442]['chk_date'];?></td>
                        <td width="70" align="center" title="<?=$chkListArr[$gmts_key][$req_key][443]['remark'];?>"><?=$chkListArr[$gmts_key][$req_key][443]['chk_date'];?></td>
                        <td width="105" align="right"></td>

                        

                        <td width="90" align="right"></td>
                        <td width="80" align="right" title="<?=$string2;?>"><a href="##"  onclick="openmypage_details('<?=$string2;?>',127,'',<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Cutting');" ><?=$all_data_arr[$string2]['cutting_qty']  ; ?></a></td>
                        <td width="90" align="right" title="Sample ReqQty-Cutting Qty" ><?=$value['sample_prod_qty']-$all_data_arr[$string2]['cutting_qty']; ?> </td>
                        <td width="90" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',338,1,<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Send Print');" ><?=$all_data_arr[$string2]['print_issue']; ?></a></td>
                        <td width="90" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',128,1,<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Rcv Print');" ><?=$all_data_arr[$string2]['print_rcv']; ;?></a></td>
                        <td width="70" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',338,2,<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Sent to Emb');" ><?=$all_data_arr[$string2]['embr_issue']; ?></a></td>
                        <td width="70" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',128,2,<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Rcv Emb');" ><?=$all_data_arr[$string2]['embr_rcv']; ?></a></td>
                        <td width="90" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',338,4,<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Sent to Sp. Works');" ><?=$all_data_arr[$string2]['spwork_issue']; ?> </a></td>
                        <td width="90" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',128,4,<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Rcv Sp. Works');" ><?=$all_data_arr[$string2]['spwork_rcv']; ?> </a> </td>
                        <td width="80" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',337,'',<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Sewing Input');" ><?=$all_data_arr[$string2]['sewing_in']; ?></a></td>
                        <td width="80" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',130,'',<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Sewing Output');" ><?=$all_data_arr[$string2]['sewing_out']; ?></a></td>
                        <td width="80" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',338,3,<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Sent to Wash');" ><?=$all_data_arr[$string2]['wash_issue'];  ?></a></td>
                        <td width="70" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',128,3,<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Rcv Wash');" ><?=$all_data_arr[$string2]['wash_rcv']; ?></a></td>
                         <td width="70" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',338,5,<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Embl Issue');" ><?=$all_data_arr[$string2]['embl_issue']; ?></a></td>
                          <td width="70" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',128,5,<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Embl Recv');" ><?=$all_data_arr[$string2]['embl_recv']; ?></a></td>
                        <td width="80" align="right"><a href="##"  onclick="openmypage_reject_qty(<? echo $value['id'];?>,<?=$color_key;?>,'sample_other_reject_view');" ><?=$all_data_arr[$string2]['other_reject'];?></a></td>
                        <td width="90" align="right"><a href="##"  onclick="openmypage_reject_qty(<? echo $value['id'];?>,<?=$color_key;?>,'sample_sewing_reject_view');" > 
                        <?=$all_data_arr[$string2]['sewing_reject'];?> </a></td>
                        <td width="105" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',396,'',<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'Delivery to MnM');" ><?=$all_data_arr[$string2]['delivery_qty']; ?></a></td>

                        <td width="70" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',396,'',<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'fin_gmt_trans_in_popup');" ><?=$transfer_in; ?></a></td>
                        <td width="70" align="right"><a href="##"  onclick="openmypage_details('<?=$string2;?>',396,'',<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'fin_gmt_trans_out_popup');" ><?=$transfer_out; ?></a></td>
                        <td width="70" align="right" title="Delivery To MM+TransferIn-Transfer Out"> <?  $aviable_qty=($all_data_arr[$string2]['delivery_qty']+$transfer_in)-$transfer_out;echo $aviable_qty; ?></td>

                        <td width="95" align="right"><?=$all_data_arr[$string2]['ex_factory_qty'] ; ?></td>
                        <td width="100" align="center"><?=$shipment_status[$all_data_arr[$string2]['shiping_status']]; ?></td>
                        <td align="center"><a href="##"  onclick="openmypage_remark(<?=$value['id'];?>,<?=$value['id'];?>,<?=$color_key;?>,<?=$report_category;?>,<?=$txt_date_from;?>,<?=$txt_date_to;?>,'remarks_view');" > View </a></td>
                    </tr>
                <?
                $rs++;

               
				  $tot_sample_prod_qty+=$value['sample_prod_qty'];
                $tot_cutting_qty+=$all_data_arr[$string2]['cutting_qty'];
                $tot_cutting_bal+=$value['sample_prod_qty']-$all_data_arr[$string2]['cutting_qty'];
                $tot_print_issue+=$all_data_arr[$string2]['print_issue'];
                $tot_print_rcv+=$all_data_arr[$string2]['print_rcv'];
                $tot_embr_issue+=$all_data_arr[$string2]['embr_issue']; 
				 $tot_gmts_sent+=$all_data_arr[$string2]['embl_issue'];
				 $tot_gmts_rcv+=$all_data_arr[$string2]['embl_recv'];
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
                $tot_transfer_in+=$transfer_in;
                $tot_transfer_out+=$transfer_out;
                $tot_aviable_qty+=$aviable_qty;
                }
              }
            }
        }
                ?>
             
        </table>
        </div>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="3720" class="rpt_table" id="report_table_footer">
		 <tfoot>
         <tr>
                    <th width="30" align="center">&nbsp;</th>                  
                    <th width="95" align="center">&nbsp;</th>
                    <th width="110" align="center">&nbsp;</th>
                    <th width="100" align="center">&nbsp;</th>
                    <th width="45" align="center">&nbsp;</th>
                    <th width="110" align="center" >&nbsp;</th>
                    <th width="70"align="center">&nbsp;</th>
                    <th width="90" align="center"><b>Total</b> </th>
                    <th width="100" align="center"><?=$tot_fin_fab_issue_qty;?></b></th> 
                    <th width="150" align="center">&nbsp;</th>
                    <th width="95" align="center">&nbsp;</th>
                    <th width="80" align="center"></th>
                    <th width="110" align="right">&nbsp;</th>
                    <th width="120" align="center">&nbsp; </th>
                    <th width="105" align="right" id="sample_req_qty"><b><?=$tot_sample_prod_qty;?></b></th>
                    <th width="70" align="center">&nbsp;</th>
                    <th width="70" align="center">&nbsp;</th>
                    <th width="70" align="center">&nbsp;</th>
                    <th width="105" align="center">&nbsp;</th>

                    

                    <th width="90" align="center">&nbsp;</th>
                    <th width="80" align="right" id="cuttting_qty"><b><?=$tot_cutting_qty;?></b></th>
                    <th width="90" align="right" id="cuttting_bal">&nbsp;  <b><?=$tot_cutting_bal;?></b> </th>
                    <th width="90" align="right" id="print_issue">&nbsp;  <b><?=$tot_print_issue;?> </b>       </th>
                    <th width="90" align="right" id="print_rcv">&nbsp;   <b><?=$tot_print_rcv;?> </b>      </th>
                    <th width="70" align="right" id="embro_issue"><b><?=$tot_embr_issue;?></b></th>
                    <th width="70" align="right" id="embro_rcv"><b><?=$tot_embr_rcv;?></b></th>
                    <th width="90" align="right" id="spworks_issue">&nbsp;   <b> <?=$tot_spwork_issue;?>  </b>    </th>
                    <th width="90" align="right" id="spworks_rcv">&nbsp;   <b><?=$tot_spwork_rcv;?>   </b>    </th>
                    <th width="80" align="right" id="sewing_input_qty"><b><?=$tot_sewing_in;?></b></th>
                    <th width="80" align="right" id="sewing_output_qty"><b><?=$tot_sewing_out;?></b></th>
                    <th width="80" align="right" id="wash_issue"><b><?=$tot_wash_issue;?></b></th>
                    <th width="70" align="right" id="wash_rcv"><b><?=$tot_wash_rcv;?></b></th>
                     <th width="70" align="right" id="wash_rcv"><b><?=$tot_gmts_sent;?></b></th>
                      <th width="70" align="right" id="wash_rcv"><b><?=$tot_gmts_rcv;?></b></th>
                    <th width="80" align="right" id="others_reject"><b><?=$tot_other_reject;?></b></th>
                    <th width="90" align="right" id="sewing_reject">&nbsp;<b><?=$tot_sewing_reject;?></b></th>
                    <th width="105" align="right" id="delivery_qty">&nbsp;<b><?=$tot_delivery_qty;?></b></th>

                    <th width="70" align="right" id="td_fin_gmt_in_qty">&nbsp;<b><? echo  $tot_transfer_in;?></b></th>
                    <th width="70" align="right" id="td_fin_gmt_out_qty">&nbsp;<b><?  echo $tot_transfer_out;?></b></th>
                    <th width="70" align="right" id="td_fin_gmt_aviable_qty">&nbsp;<b><? echo $tot_aviable_qty;?></b></th>

                    <th width="95" align="right" id="ex_factory_qty">&nbsp;<b><?=$tot_ex_factory_qty;?></b></th>
                    <th width="100" align="center">&nbsp;</th>
                    <th align="center">&nbsp;</th>
                    </tr>
                </tfoot>
			</table>
         
    </div>
	<?
	exit();
}


if($action=="remarks_view")
{
    extract($_REQUEST);
    echo load_html_head_contents("Other Reject View", "../../../", 1, 1,$unicode,'','');
    $color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
    $sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );

    $arr=array(1=>$sample_name_arr,2=>$color_arr);
	  $sewing_date_cond="";
	  if($category==2)
	  {
		 $sewing_date_cond=" and b.sewing_date between '$date_form' and '$date_to'";
	  }
  ?>
  <fieldset>
        <legend>Sewing In</legend>
        <?
      
       

       
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id,b.sewing_date, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (337) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sewing_date_cond";
              //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample date,Sample Name,Color Name,Qty", "100,80,100,100","400","220",1, $sql, "", "","", 1, '0,sample_name,color_id,0', $arr, "sewing_date,sample_name,color_id,qc_pass_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');
        ?>
    </fieldset>
    <fieldset>
        <legend>Sewing Output</legend>
        <?
      
       

       
             $sql= "SELECT a.sample_development_id as smp_dev_id,b.sewing_date,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (130) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sewing_date_cond";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample date,Sample Name,Color Name,Qty", "100,80,100,100","400","220",1, $sql, "", "","", 1, '0,sample_name,color_id,0', $arr, "sewing_date,sample_name,color_id,qc_pass_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');
        ?>
    </fieldset>
    <fieldset>
    <legend>Cutting</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id,b.sewing_date, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty , b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (127) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sewing_date_cond";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample date,Sample Name,Color Name, Qty", "100,80,100,100","400","220",1, $sql, "", "","", 1, '0,sample_name,color_id,0', $arr, "sewing_date,sample_name,color_id,qc_pass_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0,0');

      
	    ?>
    </fieldset>

   

    <fieldset>
    <legend>Print Issue</legend>
        <?

             $sql= "SELECT a.sample_development_id as smp_dev_id,b.sewing_date,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (338) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=1 $sewing_date_cond";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample date,Sample Name,Color Name,Qty", "100,80,100,100","400","220",1, $sql, "", "","", 1, '0,sample_name,color_id,0', $arr, "sewing_date,sample_name,color_id,qc_pass_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0,0');

      
	    ?>
    </fieldset>

    <fieldset>
    <legend>Print Rcv</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name,b.sewing_date, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=1 $sewing_date_cond";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample date,Sample Name,Color Name,Qty", "100,80,100,150","400","220",1, $sql, "", "","", 1, '0,sample_name,color_id,0', $arr, "sewing_date,sample_name,color_id,qc_pass_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');

      
	    ?>
    </fieldset>
    <fieldset>
    <legend>Embroidery Issue</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name,b.sewing_date, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty , b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (338) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=2 $sewing_date_cond";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample date,Sample Name,Color Name,Qty", "100,80,100,150","400","220",1, $sql, "", "","", 1, '0,sample_name,color_id,0', $arr, "sewing_date,sample_name,color_id,qc_pass_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');

      
	    ?>
    </fieldset>
    <fieldset>
    <legend>Embroidery</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name,b.sewing_date, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty , b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=2 $sewing_date_cond";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample date,Sample Name,Color Name,Qty", "100,80,100,150","400","220",1, $sql, "", "","", 1, '0,sample_name,color_id,0', $arr, "sewing_date,sample_name,color_id,qc_pass_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');

      
	    ?>
    </fieldset>
    <fieldset>
    <legend>Wash Issue</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name,b.sewing_date, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (338) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=3 $sewing_date_cond";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample date,Sample Name,Color Name,Qty", "100,80,100,150","400","220",1, $sql, "", "","", 1, '0,sample_name,color_id,0', $arr, "sewing_date,sample_name,color_id,qc_pass_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');
      
	    ?>
    </fieldset>
    <fieldset>
    <legend>Wash Rcv</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name,b.sewing_date, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=3 $sewing_date_cond";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample date,Sample Name,Color Name,Qty", "100,80,100,150","400","220",1, $sql, "", "","", 1, '0,sample_name,color_id,0', $arr, "sewing_date,sample_name,color_id,qc_pass_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');
	    ?>
    </fieldset>

    <fieldset>
    <legend>Sp. Works Issue</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id,b.sewing_date, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty , b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (338) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=4 $sewing_date_cond";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample date,Sample Name,Color Name,Qty", "100,80,100,150","400","220",1, $sql, "", "","", 1, '0,sample_name,color_id,0', $arr, "sewing_date,sample_name,color_id,qc_pass_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');

      
	    ?>
    </fieldset>

    <fieldset>
    <legend>Sp. Works Rcv</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name,b.sewing_date, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty , b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=4 $sewing_date_cond";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample date,Sample Name,Color Name,Qty", "100,80,100,150","400","220",1, $sql, "", "","", 1, '0,sample_name,color_id,0', $arr, "sewing_date,sample_name,color_id,qc_pass_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');

      
	    ?>
    </fieldset>




 <?
}
if($action=="details_view")
{
    extract($_REQUEST);
    echo load_html_head_contents("Other Reject View", "../../../", 1, 1,$unicode,'','');
    $color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
    $sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );

    $arr=array(1=>$sample_name_arr,2=>$color_arr);
    list($mst_id,$sample_id,$item_id,$color_id)=explode("*",$data);
    $embel_name_cond="";
    if($embl_id>0){
        $embel_name_cond="and b.embel_name=$embl_id";
    }
   ?>
  
   
  <fieldset>
    <legend><?=$sub_action;?></legend>
    <?
	// echo $entry_form.'='.$date_form.'='.$date_to;
	 $category=str_replace("'","",$category);
	if(str_replace("'","",trim($date_form))=="" || str_replace("'","",trim($date_to))==""){ $txt_date="";
    }else{ 
        $txt_date2=""; $txt_date=""; $txt_date3=""; $txt_date4="";$booking_date_cond="";
        if($category==2){
            if($date_form!="" && $date_to!="") $txt_date2=" and b.sewing_date between '$date_form' and '$date_to'";
			//$booking_date_cond=" and b.sewing_date between $date_form and $date_to";
          //  $txt_date3=" and e.requisition_date between $date_form and $date_to";
            if($date_form!="" && $date_to!="")  $txt_date4=" and a.delivery_date  between '$date_form' and '$date_to'";
        }else{
            $booking_date_cond=" and a.booking_date between '$date_form' and '$date_to'";
        }
    }
	
  
    if($category==2){
       // $txt_date=" and b.sewing_date between $txt_date_from and $txt_date_to";
        //$txt_date1=" and c.ex_factory_date  between $txt_date_from and $txt_date_to";
    }else{
       // $txt_date="";
      //  $txt_date1="";
    }


    if($entry_form==396){

        $sql="SELECT a.id,a.sample_development_id as smp_dev_id,a.sample_name,a.gmts_item_id, a.ex_factory_qty as qc_pass_qty,b.color_id,a.delivery_date as sewing_date 
        from sample_ex_factory_mst c, sample_ex_factory_dtls a,
             sample_ex_factory_colorsize b 
        where c.id=a.sample_ex_factory_mst_id and a.id=b.sample_ex_factory_dtls_id and 
            a.status_active=1 and 
            a.is_deleted=0 and 
            a.entry_form_id=396 and 
            b.entry_form_id=396 and 
            a.sample_development_id=$mst_id and 
            a.sample_name=$sample_id and 
            a.gmts_item_id=$item_id and 
            b.color_id=$color_id   $txt_date4
        group by a.id,a.sample_development_id ,a.sample_name,a.gmts_item_id, a.ex_factory_qty,b.color_id,a.delivery_date";

    }else{
        $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id,b.sewing_date, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, sum(c.size_pass_qty) as qc_pass_qty, b.reject_qty 
        from sample_sewing_output_mst a, 
            sample_sewing_output_dtls b, 
            sample_sewing_output_colorsize c 
        where a.id=b.sample_sewing_output_mst_id and 
            b.id=c.sample_sewing_output_dtls_id and 
            b.entry_form_id=$entry_form and 
            a.sample_development_id=$mst_id and 
            b.sample_name=$sample_id and 
            b.item_number_id=$item_id and 
            c.color_id=$color_id  $embel_name_cond and 
            a.status_active=1 and 
            a.is_deleted=0 and 
            b.status_active=1 and 
            b.is_deleted=0   $txt_date2
        group by a.sample_development_id ,a.company_id,b.sewing_date, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id ,b.entry_form_id, b.reject_qty";
    }
	//echo  $sql;
       echo  create_list_view ( "list_view", "Sample date,Sample Name,Color Name,Qty", "100,80,100,100","450","220",1, $sql, "", "","", 1, '0,sample_name,color_id,0', $arr, "sewing_date,sample_name,color_id,qc_pass_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');
    ?>
  </fieldset>


 <?
}
if($action=="sample_other_reject_view")
{
    extract($_REQUEST);
    echo load_html_head_contents("Other Reject View", "../../../", 1, 1,$unicode,'','');
    $color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
    $sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );

    $arr=array(0=>$sample_name_arr,1=>$color_arr);
 ?>
    <fieldset>
    <legend>Cutting</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (127) and c.color_id=$color_id  and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Reject Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,reject_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');

      
	    ?>
    </fieldset>

   

    <fieldset>
    <legend>Print</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=1";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Reject Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,reject_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');

      
	    ?>
    </fieldset>
    <fieldset>
    <legend>Embroidery</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=2";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Reject Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,reject_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');

      
	    ?>
    </fieldset>

    <fieldset>
    <legend>Wash</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=3";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Reject Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,reject_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');
      
	    ?>
    </fieldset>

    <fieldset>
    <legend>Sp. Works</legend>
        <?
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (128) and c.color_id=$color_id and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.embel_name=4";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Reject Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,reject_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');

      
	    ?>
    </fieldset>




 <?
}
if($action=='sample_sewing_reject_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');


    ?>
    <fieldset>
        <legend>Sewing Output</legend>
        <?
      
      $color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
      $sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );

        $arr=array(0=>$sample_name_arr,1=>$color_arr);
             $sql= "SELECT a.sample_development_id as smp_dev_id,a.company_id, b.sample_dtls_row_id, b.sample_name, b.item_number_id, b.embel_name,c.color_id , b.entry_form_id, b.qc_pass_qty as qc_pass_qty, b.reject_qty from sample_sewing_output_mst a, sample_sewing_output_dtls b, sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id in (130) and c.color_id=$color_id  and a.sample_development_id=$sample_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
             //  echo $sql;die;
             echo  create_list_view ( "list_view", "Sample Name,Color Name,Reject Qty", "80,100,150","400","220",1, $sql, "", "","", 1, 'sample_name,color_id,0', $arr, "sample_name,color_id,reject_qty", "../../requires/sample_rmg_production_report_v2_controller", '','0,0,0');
        ?>
    </fieldset>

  <? 
     
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

if($action=='sample_req_qty_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("Sample Req. Qty  View", "../../../", 1, 1,$unicode,'','');
    list($sample_name,$gmts_color,$qnty)=explode("*",$data);
	?>
	<fieldset>
        <legend>Sample Req. Qty  View</legend>
        <div style="width:410px; margin-top:10px">
            <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
                <thead>
                  
                    <th width="100">Sample Name</th>
                    <th width="100">Gmts Color</th>
                    <th width="100">Sample Req. Qty</th>
                   
                </thead>
              
					<tr>
							
                           
                            <td width="100" align="center"><?=$sample_name;?></td>
                            <td width="100" align="center"><?=$gmts_color; ?></td>
                            <td width="100" align="center"><?=$qnty; ?></td>
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
    $sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );

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