<?php
date_default_timezone_set("Asia/Dhaka");

require_once('includes/common.php');
require_once('mailer/class.phpmailer.php');

$company_library = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );
$buyer_library = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
$user_arr = return_library_array("select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$user_id_arr = return_library_array("select id,user_name from user_passwd where valid=1","id","user_name");
$user_mail_arr = return_library_array("select user_id,email_address from user_mail_address where is_deleted=0","user_id","email_address");

if($db_type==0)
{
    $current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
    $previous_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
}
else
{
    $current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
    $previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 
}

//Fabric approval Auto mail..............................................................................

$company_library = array(3=>$company_library[3]);
//echo count($company_library);
$page_id = 410;

foreach($company_library as $compid=>$compname)
{	
    $sequence_wise_user_id_arr=array();
	$sql = "select user_id,buyer_id,bypass,sequence_no FROM electronic_approval_setup where company_id = $compid and page_id=$page_id and is_deleted=0 order by sequence_no,user_id asc";
    $elecData_array=sql_select($sql);
	$i=0;
	foreach($elecData_array as $erow)
    {
		 $sequence_wise_user_id_arr[$erow[csf(sequence_no)]] = $erow[csf(user_id)];
		 $sequence_wise_buyer_id_arr[$erow[csf(sequence_no)]] = $erow[csf(buyer_id)];
         
         $use_seque[$erow[csf(user_id)]] = $erow[csf(sequence_no)];
         
         $startSequence = min($use_seque);
         $endSequence = max($use_seque);
		 $i++;
	}
    //echo "// =========================  <b>ready to approve mail start<b> ==========================================//<br> ";
    
    /* $sequence_wise_user_id_arr= array(1=>1);
    $compid=3; */
    //print_r($sequence_wise_user_id_arr); die;
    foreach($sequence_wise_user_id_arr as $userSequence=>$userid)
    {  
        $approval_type = 0;	
        $user_sequence_no = $userSequence;//return_field_value("sequence_no","electronic_approval_setup","company_id = $compid and page_id=$page_id and user_id=$userid and is_deleted=0");
     
        $min_sequence_no = $startSequence;// return_field_value("min(sequence_no)","electronic_approval_setup","company_id = $compid and page_id=$page_id and is_deleted=0");

        $buyer_ids_array = array();
        $buyerData = sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$compid and page_id=$page_id and is_deleted=0 and bypass=2");
        foreach($buyerData as $row)
        {
            $buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
            $buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
        }
        
        //var_dump($buyer_ids_array);

        if($approval_type==0)
        { 
            if($db_type==0)
            {
                $sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$compid and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
            }
            else
            {
                $sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$compid and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
            }
            
            if($user_sequence_no==$min_sequence_no)
            {	
                $buyer_ids=$buyer_ids_array[$userid]['u'];
                if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
                //$sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $date_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num order by $orderBy_cond(a.update_date, a.insert_date) desc";
                //wo_po_break_down,
                //c.grouping, c.file_no,
                //job_no_mst
               $sql="select a.id,a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,c.grouping, c.file_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$compid and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $date_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,c.grouping, c.file_no";
                //echo $sql;

            }
            else if($sequence_no=="")
            {  
                $buyer_ids=$buyer_ids_array[$userid]['u'];
                if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
                
                if($db_type==0)
                {
                    //$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$page_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");
                    $seqSql="select group_concat(sequence_no) as sequence_no_by,
     group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$compid and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
                    $seqData=sql_select($seqSql);
                    
                    $sequence_no_by=$seqData[0][csf('sequence_no_by')];
                    $buyerIds=$seqData[0][csf('buyer_ids')];
                    
                    if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.buyer_id not in($buyerIds)";
                    
                    $booking_id=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$compid and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond","booking_id");
                    
                    $booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$compid and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=7 and b.current_approval_status=1","booking_id");
                }
                else
                {
                    //$sequence_no_by=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0","sequence_no");
                    //$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by, LISTAGG(case when bypass=2 and buyer_id is not null then buyer_id end, ',') WITHIN GROUP (ORDER BY null) as buyer_ids from electronic_approval_setup where company_id=$cbo_company_name and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
                    $seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$compid and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
                    $seqData=sql_select($seqSql);
                    
                    //$sequence_no_by=$seqData[0][csf('sequence_no_by')];
                    //$buyerIds=$seqData[0][csf('buyer_ids')];
                    
                    $buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
                    foreach($seqData as $sRow)
                    {
                        if($sRow[csf('bypass')]==2)
                        {
                            $sequence_no_by_no.=$sRow[csf('sequence_no')].",";
                            if($sRow[csf('buyer_id')]!="") 
                            {
                                $buyerIds.=$sRow[csf('buyer_id')].",";
                                
                                $buyer_id_arr=explode(",",$sRow[csf('buyer_id')]);
                                $result=array_diff($buyer_id_arr,$check_buyerIds_arr);
                                if(count($result)>0)
                                {
                                    $query_string.=" (b.sequence_no=".$sRow[csf('sequence_no')]." and a.buyer_id in(".implode(",",$result).")) or ";
                                }
                                $check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
                            }
                        }
                        else
                        {
                            $sequence_no_by_yes.=$sRow[csf('sequence_no')].",";
                        }
                    }
                    //var_dump($check_buyerIds_arr);die;
                    $buyerIds=chop($buyerIds,',');
                    if($buyerIds=="") 
                    {
                        $buyerIds_cond=""; 
                        $seqCond="";
                    }
                    else 
                    {
                        $buyerIds_cond=" and a.buyer_id not in($buyerIds)";
                        $seqCond=" and (".chop($query_string,'or ').")";
                    }
                    //echo $seqCond;die;
                    $sequence_no_by_no=chop($sequence_no_by_no,',');
                    $sequence_no_by_yes=chop($sequence_no_by_yes,',');
                    
                    if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
                    if($sequence_no_by_no=="") $sequence_no_by_no=0;
                    
                    $booking_id='';
                    $booking_id_sql="select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$compid and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond $seqCond
                    union
                    select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$compid and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond";
                    $bResult=sql_select($booking_id_sql);
                    foreach($bResult as $bRow)
                    {
                        $booking_id.=$bRow[csf('booking_id')].",";
                    }
                    
                    $booking_id=chop($booking_id,',');
                    
                    $booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$compid and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=7 and b.current_approval_status=1","booking_id");
                    $booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
                }
                //echo $booking_id;die;
                $result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
                $booking_id=implode(",",$result);
                
                $booking_id_cond="";
                if($booking_id!="")
                {
                    if($db_type==2 && count($result)>999)
                    {
                        $booking_id_chunk_arr=array_chunk($result,999) ;
                        foreach($booking_id_chunk_arr as $chunk_arr)
                        {
                            $chunk_arr_value=implode(",",$chunk_arr);	
                            $bokIds_cond.=" a.id in($chunk_arr_value) or ";	
                        }
                        
                        $booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
                        //echo $booking_id_cond;die;
                    }
                    else
                    {
                        $booking_id_cond=" and a.id in($booking_id)";	 
                    }
                }
                else $booking_id_cond="";
                
                /*$booking_id_cond="";
                if($booking_id_app_byuser!="") $booking_id_cond=" and a.id not in($booking_id_app_byuser)";
                if($booking_id!="") $booking_id_cond.=" or (a.id in($booking_id))";
                
                $sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $booking_id_cond $buyer_id_cond $date_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num order by $orderBy_cond(a.update_date, a.insert_date) desc";*/
                
                if($db_type==0)
                {
                    if($booking_id!="")
                    {
                        $sql="select a.update_date as ob_update, a.insert_date as ob_insertdate, a.id,  a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,c.grouping, c.file_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$compid and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond  $date_cond $buyerIds_cond $buyer_id_cond2 group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,c.grouping, c.file_no
                            union all
                            select a.update_date as ob_update, a.insert_date as ob_insertdate, a.id, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,c.grouping, c.file_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$compid and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=1 and b.fin_fab_qnty>0 and a.id in($booking_id) $buyer_id_cond $buyer_id_cond2  $date_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,c.grouping, c.file_no ";
                    }
                    else
                    {
                        $sql="select a.id,a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,c.grouping, c.file_no from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c  where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$compid and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $date_cond $buyerIds_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,c.grouping, c.file_no ";
                    }
                    //echo $sql;
                }
                else
                {
                    if($booking_id!="")
                    {   // and a.id in($booking_id)
                        $sql="select * from(select a.update_date, a.insert_date, a.id, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,c.grouping, c.file_no from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$compid and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $date_cond $buyerIds_cond $buyer_id_cond2 group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,c.grouping, c.file_no
                            union all
                            select a.update_date, a.insert_date, a.id, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,c.grouping, c.file_no from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c  where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$compid and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=1 and b.fin_fab_qnty>0 $booking_id_cond $buyer_id_cond $buyer_id_cond2 $date_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,c.grouping, c.file_no)";  
                    }
                    else
                    {
                        $sql="select a.id,a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,c.grouping, c.file_no from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$compid and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $date_cond $buyerIds_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,c.grouping, c.file_no";
                        // echo $sql;
                    }
                    
                    /*$booking_id_cond="";
                    if($booking_id_app_byuser!="") $booking_id_cond=" and a.id not in($booking_id_app_byuser)";
                    if($booking_id!="") $booking_id_cond.=" or (a.id in($booking_id))";
                    
                    $sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $booking_id_cond $buyer_id_cond $date_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num order by $orderBy_cond(a.update_date, a.insert_date) desc";*/
                    //echo $sql;
                }
            }
            else
            {
                $buyer_ids=$buyer_ids_array[$userid]['u'];
                if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
                
                $user_sequence_no = $user_sequence_no-1;
                //echo $sequence_no;
                if($sequence_no==$user_sequence_no) 
                {
                    $sequence_no_by_pass='';
                }
                else
                {
                    if($db_type==0)
                    {
                        $sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$compid and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
                    }
                    else
                    {
                        $sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$compid and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");	
                    }
                    
                    if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
                    else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
                    
                    $sql="select a.id,a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,c.grouping, c.file_no from wo_booking_mst a, approval_history b,wo_po_break_down c where a.id=b.mst_id and a.job_no=c.job_no_mst and b.entry_form=7 and a.company_id=$compid and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved=1 $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond ";
                }
            } 
        }
        
        if($userSequence == $endSequence) {
            $subject = "Following Fabric booking sheet is Ready For Final Approval";
        } else {
            $subject = "Following Fabric booking sheet is Ready For Approval";
        }
        
        $htmlheader='';
        $html_arr = array();
        $htmlheader='
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="6" align="center">
                    <strong style="font-size:23px;">'.$company_library[$compid].'</strong><br>
                    <strong style="font-size:14px;">'.ucfirst($user_arr[$userid]).'</strong>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center">
                    <b style="font-size:14px;">Upto Date : '. date("d-m-Y h:i:s a",time()).'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="6" align="center"><b>'.$subject.'</b></td>
            </tr>
          
        </table>
        <br>
        
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="70">Booking Date</th>
                </tr>
            </thead>
            <tbody>';           
                $i = 1;
                $nameArray=sql_select( $sql );
                
                $htmlBody='';
                foreach ($nameArray as $row)
                {               
                    //$user_sequence[$user];
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                    $value=$row[csf('id')];
                
                    if($row[csf('approval_id')]==0)
                    {
                        $print_cond = 1;
                    }
                    else
                    {
                        if($duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('approved_by')]]=="")
                        {
                            $duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('approved_by')]]=$row[csf('id')];
                            $print_cond=1;
                        }
                        else
                        {
                            if($all_approval_id == "") $all_approval_id = $row[csf('approval_id')]; else $all_approval_id.=",".$row[csf('approval_id')];
                            $print_cond=0;
                        }
                    }
                    if($print_cond==1)
                    {	                      
                        $htmlBody.='
                        <tr bgcolor="'.$bgcolor.'">
                            <td align="center">'.$i.'</td>
                            <td>'.$buyer_library[$row[csf(buyer_id)]].'</td>
                            <td>'.$row[csf(job_no)].'</td>
                            <td align="center">'.change_date_format($row[csf(booking_date)]).'</td>
                        </tr>
                        ';
                        $i++; 
                    }   
                }
				$htmlFooter='';
                $htmlFooter=' 
            </tbody>         
            <tfoot style="background-color:#CCC">
                <th align="right"></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right"></th>
            </tfoot>
        </table>'; 
        
        echo  $html=$htmlheader.$htmlBody.$htmlFooter;

       /* 
        $to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$userid";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

    	$message = $html;
		$header = mail_header();
		
		if($to!="" && $htmlBody!="") echo send_mail_mailer( $to, $subject, $message, $from_mail ); */
 
    } // ready to approve user sequence loop end  
    
    
    
    echo "//=========================  <b>ready to approve mail end</b> ==========================================//";
    
    echo "<br>";
    echo "//========================<b> Approved job mail data Start </b> ========================================//<br>";
    
    // apply for approval job list 
    $applyForApprovalBookingSqlresult = sql_select("select mst_id,approved_by from approval_history WHERE approved_by NOT IN ($sequence_wise_user_id_arr[$endSequence]) and entry_form = 7 group by mst_id,approved_by order by mst_id");  
    
    $applyFabricBookingForApproval = array();  
    foreach ($applyForApprovalBookingSqlresult as $datrow) {        
        if($tmp[$datrow[csf('mst_id')]]=='') $i=0;
        $applyFabricBookingForApproval[$datrow[csf('mst_id')]][$i] = $datrow[csf('approved_by')];
        $tmp[$datrow[csf('mst_id')]]=$datrow[csf('mst_id')];     
        $i++;      
    }  
    
   /*  echo "<pre>";
    print_r($applyFabricBookingForApproval); 
    die; */
    
    // Higher Authority Approved job list
    $higherAuthoritySql = sql_select("select mst_id,approved_by from approval_history WHERE approved_by IN ($sequence_wise_user_id_arr[$endSequence]) and entry_form = 7  and current_approval_status=1 group by mst_id,approved_by,current_approval_status order by mst_id");  
    $finalApproveBooking = array();     
    foreach ($higherAuthoritySql as $row) {          
        $finalApproveBooking[$row[csf('approved_by')]][$row[csf('mst_id')]] = $row[csf('mst_id')];
        $final_app_booking_id_arr[]= $row[csf('mst_id')];
    }

    
    /* echo "<pre>";
    print_r($finalApproveJobs);     die; */
    
    $userWiseApprovedBookingMailData = array();
	foreach( $finalApproveBooking as $appr=>$mstids)
	{          
		foreach($mstids as $mstid)
		{
			foreach( $applyFabricBookingForApproval[$mstid] as $key=>$apprver)
			{              
                $userWiseApprovedBookingMailData[$apprver][$mstid] = $mstid;
			}
            
		}
	}
     
    /* echo "<pre>";
    print_r($userWiseApprovedBookingMailData);    */
     
    $finalAuthorityBookings = "";
    foreach ($final_app_booking_id_arr as $key=>$value) {
        if($finalAuthorityBookings == "") {$finalAuthorityBookings = "'".$value."'";} else {$finalAuthorityBookings .=","."'".$value."'"; }
    }
    //echo $finalAuthorityBookings ."<br>";
    
    $finalAuthorityAppSql ="select a.id,a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update from wo_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form=7 and a.company_id=$compid and b.mst_id IN($finalAuthorityBookings) order by a.buyer_id";
    $result = sql_select( $finalAuthorityAppSql );
    
    //var_dump($result );
   
    $fabric_booking_approved_data = array();
    foreach ($result as $row) {
        $fabric_booking_approved_data[$row[csf(approved_by)]][$row[csf(id)]][buyer_id]   = $row[csf(buyer_id)];
        $fabric_booking_approved_data[$row[csf(approved_by)]][$row[csf(id)]][job_no]       = $row[csf(job_no)];         
        $fabric_booking_approved_data[$row[csf(approved_by)]][$row[csf(id)]][booking_date] = $row[csf(booking_date)];           
    } 
 
    /* echo "<pre>";
    print_r($fabric_booking_data); */
    
    
    foreach ($userWiseApprovedBookingMailData as $approval_user=>$approvedMstIdArr) {
        $approve_html_arr = array();
		$htmlheader='';
        $htmlheader='
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="8" align="center">
                    <strong style="font-size:23px;">'.$company_library[$compid].'</strong> <br>
                    <strong style="font-size:14px;">'.ucfirst($user_arr[$approval_user]).'</strong>
                </td>
            </tr>
            <tr>
                <td colspan="4" align="center">                    
                    <b style="font-size:14px;">Upto Date : '. date("d-m-Y h:i:s a",time()).'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="4" align="center"><b>Following Fabric booking sheet has been approved</b></td>
            </tr>
          
        </table>
        <br>
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="70">Booking Date</th>
                </tr>
            </thead>
            <tbody>';
    
            $i=1;
            $htmlBody_approved ="";
            foreach ($approvedMstIdArr as $mstid) 
            {          
                //$user_sequence[$user];
				if($fabric_booking_approved_data[$approval_user][$mstid][job_no] !='') 
                {
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                  
                    $htmlBody_approved.='
                    <tr bgcolor="'.$bgcolor.'">
                        <td align="center">'.$i.'</td>                  
                        <td>'.$buyer_library[$fabric_booking_approved_data[$approval_user][$mstid][buyer_id]].'</td>
                        <td>'.$fabric_booking_approved_data[$approval_user][$mstid][job_no].'</td>
                        <td>'.change_date_format($fabric_booking_approved_data[$approval_user][$mstid][booking_date]).'</td>
                    </tr>
                    ';
                    $i++;
                }
                
            }
		$htmlFooter='';	
        $htmlFooter=' 
        </tbody>         
        <tfoot style="background-color:#CCC">
            <th align="right"></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th align="right"></th>
        </tfoot>
        </table>';
      
        
		echo $html = $htmlheader.$htmlBody_approved.$htmlFooter;
        
		/* $to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$approval_user";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}
        
        // management mail recipient group start //
        $sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=11 and b.mail_user_setup_id=c.id and a.company_id=$compid";
		
		$mailsen_sql=sql_select($sql);
		foreach($mailsen_sql as $mail_row)
		{
			if ($to=="")  $to=$mail_row[csf('email_address')]; else $to=$to.", ".$mail_row[csf('email_address')]; 
		}
        
        // management mail recipient group end //

 		$subject = "Following pre-cost sheet has been approved";
    	$message = $html;
		$header = mail_header();
		
		if($to!="" && $htmlBody_approved!="") 
        {
            $con = connect();
            echo $mailSend = send_mail_mailer( $to, $subject, $message, $from_mail );
            $query = "UPDATE wo_pre_cost_mst SET is_mail_sent=1 WHERE id in(".implode(",",$mail_send_precost_id).")"; 
            $feedback_id = execute_query($query,1);
            //echo $feedback_id."hhhh";    
        }; */
		
    }
    echo "//========================<b> Approved job mail data End </b> ========================================//";
    
    
    echo "<br>";
    echo "//========================<b> Unapproved job mail data Start </b>====================================//<br>";
    //var_dump($final_un_app_jobs_arr);
    
    
    // Higher Authority Unapproved Booking list
    $higherAuthorityUnappSql = sql_select("select mst_id,approved_by from approval_history WHERE approved_by IN ($sequence_wise_user_id_arr[$endSequence]) and entry_form = 7  and current_approval_status=0 group by mst_id,approved_by,current_approval_status order by mst_id");  
    $finalUnApproveBooking = array();     
    foreach ($higherAuthorityUnappSql as $row) {          
        $finalUnApproveBooking[$row[csf('approved_by')]][$row[csf('mst_id')]] = $row[csf('mst_id')];
        $final_un_app_booking_id_arr[]= $row[csf('mst_id')];
    }

    
    /* echo "<pre>";
    print_r($finalUnApproveBooking);     die; */
    
    $userWiseUnApprovedBookingMailData = array();
	foreach( $finalUnApproveBooking as $appr=>$mstids)
	{          
		foreach($mstids as $mstid)
		{
			foreach( $applyFabricBookingForApproval[$mstid] as $key=>$apprver)
			{              
                $userWiseUnApprovedBookingMailData[$apprver][$mstid] = $mstid;
			}
            
		}
	}
     
    /* echo "<pre>";
    print_r($userWiseUnApprovedBookingMailData);   */ 
     
    $finalAuthorityUnappBookings = "";
    foreach ($final_un_app_booking_id_arr as $key=>$value) {
        if($finalAuthorityUnappBookings == "") {$finalAuthorityUnappBookings = "'".$value."'";} else {$finalAuthorityUnappBookings .=","."'".$value."'"; }
    }
    //echo $finalAuthorityUnappBookings ."<br>";
    
    $finalAuthorityUnAppSql ="select a.id,a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update from wo_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form=7 and a.company_id=$compid and b.mst_id IN($finalAuthorityUnappBookings) order by a.buyer_id";
    $result = sql_select( $finalAuthorityUnAppSql );
    
    //var_dump($result );
   
    $fabric_booking_un_approved_data = array();
    foreach ($result as $row) {
        $fabric_booking_un_approved_data[$row[csf(approved_by)]][$row[csf(id)]][buyer_id]   = $row[csf(buyer_id)];
        $fabric_booking_un_approved_data[$row[csf(approved_by)]][$row[csf(id)]][job_no]       = $row[csf(job_no)];         
        $fabric_booking_un_approved_data[$row[csf(approved_by)]][$row[csf(id)]][booking_date] = $row[csf(booking_date)];           
    } 
 
   /*  echo "<pre>";
    print_r($fabric_booking_un_approved_data);  */

    foreach ($userWiseUnApprovedBookingMailData as $applier_user=>$unApprovedMstIdArr) {

        $unApprove_html_arr = array();
		$htmlheader='';
        $htmlheader='
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="8" align="center">
                    <strong style="font-size:23px;">'.$company_library[$compid].'</strong> <br>
                    <strong style="font-size:14px;">'.ucfirst($user_arr[$applier_user]).'</strong>
                </td>
            </tr>
            <tr>
                <td colspan="8" align="center">                    
                    <b style="font-size:14px;">Upto Date : '. date("d-m-Y h:i:s a",time()).'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="8" align="center"><b>Following Fabric booking Sheet Has Been Un-approved</b></td>
            </tr>
          
        </table>
        
        <br>
        
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="70">Booking Date</th>
                </tr>
            </thead>
            <tbody>';
    
            $i=1;
            $htmlBody_un_approved ="";
            foreach ($unApprovedMstIdArr as $mstid) 
            {
                if($fabric_booking_un_approved_data[$applier_user][$mstid][job_no]!='')
                {  
                  
                   //$user_sequence[$user];
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                  
                    $htmlBody_un_approved.='
                    <tr bgcolor="'.$bgcolor.'">
                        <td align="center">'.$i.'</td>                  
                        <td>'.$buyer_library[$fabric_booking_un_approved_data[$applier_user][$mstid][buyer_id]].'</td>
                        <td>'.$fabric_booking_un_approved_data[$applier_user][$mstid][job_no].'</td>
                        <td>'.change_date_format($fabric_booking_un_approved_data[$applier_user][$mstid][booking_date]).'</td>
                    </tr>
                    ';
                    $i++;            
                }
            }
		$htmlFooter='';	
        $htmlFooter=' 
        </tbody>         
        <tfoot style="background-color:#CCC">
            <th align="right"></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th align="right"></th>
        </tfoot>
        </table>';

		echo $html = $htmlheader.$htmlBody_un_approved.$htmlFooter;
        
		/* $to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$applier_user";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}
    
        // management mail recipient group start //
        $sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=11 and b.mail_user_setup_id=c.id and a.company_id=$compid";
		
		$mailsen_sql=sql_select($sql);
		foreach($mailsen_sql as $mail_row)
		{
			if ($to=="")  $to=$mail_row[csf('email_address')]; else $to=$to.", ".$mail_row[csf('email_address')]; 
		}
        
        // management mail recipient group end //
    
 		$subject = "Following Pre-cost Sheet Has Been Un-approved";
    	$message = $html;
		$header = mail_header();

		if($to!="" && $htmlBody_un_approved!="") {
           
            $con = connect();
            echo $mailSend = send_mail_mailer( $to, $subject, $message, $from_mail );
            echo $query = "UPDATE wo_pre_cost_mst SET is_mail_sent=1 WHERE id in(".implode(",",$mail_send_un_app_precost_id).")"; 
            $feedback_id = execute_query($query,1);
            //echo $feedback_id."hhhh";    
        }; */
        
    }
    echo "//========================<b> Unapproved job mail data End </b>====================================//";
   

    echo "<br>// ======================================= <b>Merchandiser approved mail data Start</b> ============================//<br>";
    // ======================================= approve job mail to Merchandiser =============================================
    
    // marchantiger apply for approval booking list 
    $applyfabricBookingByMarchantiger=sql_select("select id,inserted_by as marchantiger from wo_booking_mst where company_id=$compid and is_short in(2,3) and booking_type=1 and item_category in(2,3,13) and is_deleted=0 and status_active=1 and ready_to_approved=1 order by id");

    // apply for approval booking list 
    $applyByMarchantiger = array();  
    foreach ($applyfabricBookingByMarchantiger as $datrow) {       
        if($tmp[$datrow[csf('id')]]=='') $i=0;
        $applyByMarchantiger[$datrow[csf('id')]][$i] = $datrow[csf('marchantiger')];
        $tmp[$datrow[csf('id')]]=$datrow[csf('id')];     
        $i++;                   
    }  
    
   //var_dump($applyByMarchantiger); die;
    
     /* echo "<pre>";
    print_r($finalApproveJobs);     die; */
  
    $marchantigerWiseApprovedBookingMailData = array();
	foreach( $finalApproveBooking as $appr=>$mstids)
	{          
		foreach($mstids as $mstid)
		{
			foreach( $applyByMarchantiger[$mstid] as $key=>$marchantiger)
			{                
                $marchantigerWiseApprovedBookingMailData[$marchantiger][$mstid] = $mstid;
			}           
		}
	} 
    
    //var_dump($marchantigerWiseApprovedBookingMailData); die;
    
    $finalAuthorityBookings = "";  
    foreach ($final_app_booking_id_arr as $key=>$value) {
        if($finalAuthorityBookings == "") {$finalAuthorityBookings = "'".$value."'";} else {$finalAuthorityBookings .=","."'".$value."'"; }
    }
    // echo "143==".$finalAuthorityBookings."<br>";
    
    $finalAuthorityAppSql ="select a.id,a.inserted_by,a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update from wo_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form=7 and a.company_id=$compid and b.mst_id IN($finalAuthorityBookings) order by a.buyer_id";
    $result = sql_select( $finalAuthorityAppSql );
    
    $marchant_approve_data = array();

    foreach ($result as $row) {
        $marchant_approve_data[$row[csf(inserted_by)]][$row[csf(id)]][buyer_id]   = $row[csf(buyer_id)];
        $marchant_approve_data[$row[csf(inserted_by)]][$row[csf(id)]][job_no]       = $row[csf(job_no)];     
        $marchant_approve_data[$row[csf(inserted_by)]][$row[csf(id)]][booking_date] = $row[csf(booking_date)];          
    }
    
    /* echo "<pre>";
    print_r($marchant_approve_data); die; */
 
    foreach ($marchantigerWiseApprovedBookingMailData as $marchantiger=>$approvedMstIdArr) {
    
        $approve_html_arr = array();
        $htmlheader='
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="4" align="center">
                    <strong style="font-size:23px;">'.$company_library[$compid].'</strong> <br>
                    <strong style="font-size:14px;">'.ucfirst($user_arr[$marchantiger]).'</strong>
                </td>
            </tr>
            <tr>
                <td colspan="4" align="center">                    
                    <b style="font-size:14px;">Upto Date : '. date("d-m-Y h:i:s a",time()).'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="4" align="center"><b>Following Fabric booking Sheet Has Been Approved</b></td>
            </tr>
          
        </table>
       
        <br>
       
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="70">Booking Date</th>
                </tr>
            </thead>
            <tbody>';
    
            $i=1;
            $htmlBody_approved ="";
            foreach ($approvedMstIdArr as $mstid) {          
                //$user_sequence[$user];
				if($marchant_approve_data[$marchantiger][$mstid][job_no] !='') {
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                        
                    $htmlBody_approved.='
                    <tr bgcolor="'.$bgcolor.'">
                        <td align="center">'.$i.'</td>                  
                        <td>'.$buyer_library[$marchant_approve_data[$marchantiger][$mstid][buyer_id]].'</td>
                        <td>'.$marchant_approve_data[$marchantiger][$mstid][job_no].'</td>
                        <td>'.change_date_format($marchant_approve_data[$marchantiger][$mstid][booking_date]).'</td>
                    </tr>
                    ';
                    $i++;
                    
				}   
            }
        $htmlFooter=' 
        </tbody>         
        <tfoot style="background-color:#CCC">
            <th align="right"></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th align="right"></th>
        </tfoot>
        </table>';
        
        
		echo $html = $htmlheader.$htmlBody_approved.$htmlFooter;
        
		/* $to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$marchantiger";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

 		$subject = "Following Pre-Cost Sheet Has Been Approved";
    	$message = $html;
		$header = mail_header();
		
		if($to!="" && $htmlBody_approved!="") {
            $con = connect();
            echo $mailSend = send_mail_mailer( $to, $subject, $message, $from_mail );
            echo $query = "UPDATE wo_pre_cost_mst SET is_mail_sent=1 WHERE id in(".implode(",",$marchant_approve_precost_id).")"; 
            $feedback_id = execute_query($query,1);
            //echo $feedback_id."hhhh";    
        }; */
        
    }
    
    echo "// ======================================= <b>Merchandiser approved mail data End</b> =====================//<br>";

    
    echo "<br>// ======================================= <b>Merchandiser unapproved mail data Start</b> ============================//<br>";
    // ======================================= unapproved job mail to Merchandiser =============================================
    
    //var_dump($applyByMarchantigerUnapp);
    
    // Higher Authority Unapproved Booking list
    $higherAuthorityUnappSql = sql_select("select mst_id,approved_by from approval_history WHERE approved_by IN ($sequence_wise_user_id_arr[$endSequence]) and entry_form = 7  and current_approval_status=0 group by mst_id,approved_by,current_approval_status group by mst_id,approved_by order by mst_id");  
    $finalUnApproveBooking = array();     
    foreach ($higherAuthorityUnappSql as $row) 
    {          
        $final_un_app_booking_id_arr[]= $row[csf('mst_id')];
    }
    
    // marchantiger apply for approval booking list 
    $applyfabricBookingByMarchantiger=sql_select("select id,inserted_by as marchantiger,buyer_id,job_no,booking_date from wo_booking_mst where company_id=$compid  and id in (".implode(",",$final_un_app_booking_id_arr).") and is_short in(2,3) and booking_type=1 and item_category in(2,3,13) and is_deleted=0 and status_active=1 order by id");

    $marchantigerWiseUnappBooking = array();
    $marchant_un_approve_data = array();
    foreach($applyfabricBookingByMarchantiger as $row)
    {
        $marchantigerWiseUnappBooking[$row[csf('marchantiger')]][$row[csf('id')]] = $row[csf('id')];
        
        $marchant_un_approve_data[$row[csf(marchantiger)]][$row[csf(id)]][buyer_id]     = $row[csf(buyer_id)];
        $marchant_un_approve_data[$row[csf(marchantiger)]][$row[csf(id)]][job_no]       = $row[csf(job_no)];     
        $marchant_un_approve_data[$row[csf(marchantiger)]][$row[csf(id)]][booking_date] = $row[csf(booking_date)];          
        
    }
    
    foreach ($marchantigerWiseUnappBooking as $marchantiger=>$unApprovedMstIdArr) {
        
        $approve_html_arr = array();
        $htmlheader='
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="4" align="center">
                    <strong style="font-size:23px;">'.$company_library[$compid].'</strong> <br>
                    <strong style="font-size:14px;">'.ucfirst($user_arr[$marchantiger]).'</strong>
                </td>
            </tr>
            <tr>
                <td colspan="4" align="center">                    
                    <b style="font-size:14px;">Upto Date : '. date("d-m-Y h:i:s a",time()).'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="4" align="center"><b>Following Fabric booking Sheet Has Been Approved</b></td>
            </tr>
          
        </table>
       
        <br>
       
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="70">Booking Date</th>
                </tr>
            </thead>
            <tbody>';
    
            $i=1;
            $htmlBody_un_approved ="";
            foreach ($unApprovedMstIdArr as $mstid) {          
                //$user_sequence[$user];
               
				if($marchant_un_approve_data[$marchantiger][$mstid][job_no] !='') {
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                   
                    $htmlBody_un_approved.='
                    <tr bgcolor="'.$bgcolor.'">
                        <td align="center">'.$i.'</td>                  
                        <td>'.$buyer_library[$marchant_un_approve_data[$marchantiger][$mstid][buyer_id]].'</td>
                        <td>'.$marchant_un_approve_data[$marchantiger][$mstid][job_no].'</td>
                        <td>'.change_date_format($marchant_un_approve_data[$marchantiger][$mstid][booking_date]).'</td>
                    </tr>
                    ';
                    $i++;
                }    
				 
            }
        $htmlFooter=' 
        </tbody>         
        <tfoot style="background-color:#CCC">
            <th align="right"></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th align="right"></th>
        </tfoot>
        </table>';
        
        
		echo $html = $htmlheader.$htmlBody_un_approved.$htmlFooter;
        
		/* $to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$marchantiger";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

 		$subject = "Following Pre-Cost Sheet Has Been Approved";
    	$message = $html;
		$header = mail_header();
		
		if($to!="" && $htmlBody_approved!="") {
            $con = connect();
            echo $mailSend = send_mail_mailer( $to, $subject, $message, $from_mail );
            echo $query = "UPDATE wo_pre_cost_mst SET is_mail_sent=1 WHERE id in(".implode(",",$marchant_approve_precost_id).")"; 
            $feedback_id = execute_query($query,1);
            //echo $feedback_id."hhhh";    
        }; */
       
        
    
    }
    
     echo "<br>// ======================================= <b>Merchandiser unapproved mail data Start</b> ============================//<br>";
    // ======================================= unapproved job mail to Merchandiser =============================================
 
} // company loop end 

?> 