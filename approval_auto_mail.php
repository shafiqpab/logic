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


//Fabric booking Auto mail..............................................................................
$company_library = array(3=>$company_library[3]);
foreach($company_library as $compid=>$compname)
{
    $sequence_wise_user_id_arr=array();
	$sql = "select user_id,buyer_id,bypass,sequence_no FROM electronic_approval_setup where company_id = $compid and entry_form=7 and is_deleted=0 order by sequence_no,user_id asc";
    $elecData_array=sql_select($sql);
	foreach($elecData_array as $row){
         $use_seque[$row[csf(user_id)]] = $row[csf(sequence_no)];
         $startSequence = min($use_seque);
         $endSequence = max($use_seque);
	}


	if($db_type==0){
		$app_date_cond	=" and b.approved_date between '".$previous_date."' and '".$current_date."'";
		$un_date_cond	=" and b.un_approved_date between '".$previous_date."' and '".$current_date."'";
	}
	else
	{
		$app_date_cond	=" and b.approved_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$un_date_cond	=" and b.un_approved_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	}
	
    
	$bookingCon="and a.ready_to_approved=1 and a.booking_no='D n C-Fb-17-00165'";
	
/*	$appSql="select a.booking_type,a.booking_no,a.job_no,b.current_approval_status,b.approved_by,b.un_approved_by,b.sequence_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$compid and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 $app_date_cond";*/
	
$appSql="select a.booking_type,a.booking_no,a.job_no,b.current_approval_status,b.approved_by,b.un_approved_by,b.sequence_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$compid and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 $bookingCon";

        $html='
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="8" align="center">
                    <strong style="font-size:23px;">'.$company_library[$compid].'</strong>
                </td>
            </tr>
            <tr>
                <td colspan="8" align="center">
                    <b style="font-size:14px;">Date : From '. $previous_date.' To '. $current_date.'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="8" align="center"><b>Fabric Booking approval remainder.</b></td>
            </tr>
        </table>
        <br>
        
		<b>Dear Concern,</b><br>
		Approved remainder of following booking -
		<table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Booking No</th>
                    <th width="80">Job No</th>
                    <th width="150">Booking Type</th>
                    <th width="80">App. Type</th>
                    <th width="100">App. By</th>
                </tr>
            </thead>
            <tbody>'; 


	$fab_booking_sql = sql_select($appSql);
	$sl=1;
	foreach($fab_booking_sql as $row){
     	if($endSequence!=$row[csf(sequence_no)]){$appType="Partial App.";}else{$appType="Full App.";}
		
		$html.='
                <tr align="center">
                    <th width="35">'.$sl.'</th>
                    <th width="100"><p>'.$row[csf(booking_no)].'</p></th>
                    <th width="100"><p>'.$row[csf(job_no)].'</p></th>
                    <th width="150">'.$report_name[$row[csf(booking_type)]].'</th>
                    <th width="80">'.$appType.'</th>
                    <th width="100">'.$user_id_arr[$row[csf(approved_by)]].'</th>
                </tr>
            '; 
	$sl++;
	}
     	$html.='</table>'; 


	$unAppSql="select a.booking_type,a.booking_no,a.job_no,b.current_approval_status,b.approved_by,b.un_approved_by,b.sequence_no,b.comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$compid and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=0 $un_date_cond group by a.booking_type,a.booking_no,a.job_no,b.current_approval_status,b.approved_by,b.un_approved_by,b.sequence_no,b.comments";
	$fab_booking_sql = sql_select($unAppSql);


        $html.='<br>
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="8" align="center"><b>Fabric booking Un-Approval Notification.</b></td>
            </tr>
        </table>
       
        
		<b>Dear Concern,</b><br>
		Following booking has been Un-Approved -
		<table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Booking No</th>
                    <th width="80">Job No</th>
                    <th width="150">Booking Type</th>
                    <th width="120">Un-Approve Reason</th>
                    <th width="100">Un App. By</th>
                </tr>
            </thead>
            <tbody>'; 



	$sl=1;
	foreach($fab_booking_sql as $row){
     	if($endSequence!=$row[csf(sequence_no)]){$appType="Partial App.";}else{$appType="Full App.";}
		
		$html.='
                <tr align="center">
                    <th width="35">'.$sl.'</th>
                    <th width="100"><p>'.$row[csf(booking_no)].'</p></th>
                    <th width="100"><p>'.$row[csf(job_no)].'</p></th>
                    <th width="150">'.$report_name[$row[csf(booking_type)]].'</th>
                    <th width="80">'.$row[csf(comments)].'</th>
                    <th width="100">'.$user_id_arr[$row[csf(un_approved_by)]].'</th>
                </tr>
            '; 
	$sl++;
	}
     	$html.='</table>'; 





		$to="";
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=2 and b.mail_user_setup_id=c.id and a.company_id=$compid";
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
 		$subject = "Approved & Un-approved remainder";
    	$message=$html;
		$header=mail_header();
		//if($to!=""){echo send_mail_mailer( $to, $subject, $message, $from_mail );}

echo $html;	

die;
}




$page_id = 869;
$company_library = array(3=>$company_library[3]);
foreach($company_library as $compid=>$compname)
{	
    $sequence_wise_user_id_arr=array();
	$sql = "select user_id,buyer_id,bypass,sequence_no FROM electronic_approval_setup where company_id = $compid and entry_form=7 and is_deleted=0 order by sequence_no,user_id asc";
    $elecData_array=sql_select($sql);
	$i=0;
	foreach($elecData_array as $erow){
		 $sequence_wise_user_id_arr[$erow[csf(sequence_no)]] = $erow[csf(user_id)];
		 $sequence_wise_buyer_id_arr[$erow[csf(sequence_no)]] = $erow[csf(buyer_id)];
         $use_seque[$erow[csf(user_id)]] = $erow[csf(sequence_no)];
         $startSequence = min($use_seque);
         $endSequence = max($use_seque);
		 $i++;
	}
// =========================ready to approve mail start==========================================;
    
    foreach($sequence_wise_user_id_arr as $userSequence=>$userid)
    {  
        $approval_type = 2;	
        $user_sequence_no = $userSequence;
        $min_sequence_no = $startSequence;

        $buyer_ids_array = array();
        $buyerData = sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$compid and entry_form=7 and is_deleted=0 and bypass=2");
        foreach($buyerData as $row)
        {
            $buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
            $buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
        }
        

        if($approval_type==2)
        {
            if($db_type==0)
            {
                $sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$compid and entry_form=7 and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
            }
            else
            {
                $sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$compid and entry_form=7 and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
            }
                            
            if($user_sequence_no==$min_sequence_no)
            {
                $buyer_ids = $buyer_ids_array[$userid]['u'];
                if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
                
               // $sql="select b.id,a.gmts_item_id,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,'0' as approval_id, b.partial_approved,b.inserted_by from wo_pre_cost_mst b,  wo_po_details_master a where a.job_no=b.job_no and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.partial_approved=2 $buyer_id_cond $buyer_id_cond2 $date_cond";
                $sql="select a.booking_no,a.booking_type,a.job_no, a.buyer_id, a.po_break_down_id,a.inserted_by from wo_booking_mst a where a.company_id=$compid and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1  and a.is_approved=0 $buyer_id_cond $buyer_id_cond2 $date_cond";
			}
            else if($sequence_no == "")
            {				
                $buyer_ids=$buyer_ids_array[$userid]['u'];
                if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
                if( $buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and a.buyer_id in($buyer_ids)";
                if($db_type==0)
                {
                    $seqSql="select group_concat(sequence_no) as sequence_no_by,
     group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$compid and entry_form=7 and sequence_no<$user_sequence_no and is_deleted=0";
                    $seqData=sql_select($seqSql);
                    
                    $sequence_no_by=$seqData[0][csf('sequence_no_by')];
                    $buyerIds=$seqData[0][csf('buyer_ids')];
                    
                    if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.buyer_name not in($buyerIds)";
                    
                    //$pre_cost_id=return_field_value("group_concat(distinct(mst_id)) as pre_cost_id","wo_pre_cost_mst a, approval_history b,wo_po_details_master c","a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$compid and b.sequence_no in ($sequence_no_by) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond3","pre_cost_id");
                    //$pre_cost_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as pre_cost_id","wo_pre_cost_mst a, approval_history b,wo_po_details_master c","a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$compid and b.sequence_no=$user_sequence_no and b.entry_form=7 and a.ready_to_approved=1 and b.current_approval_status=1","pre_cost_id");
                    
					
					$pre_cost_id=return_field_value("group_concat(distinct(b.mst_id)) as pre_cost_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$compid and b.sequence_no in ($sequence_no_by) and b.entry_form=7 and b.current_approval_status=1 and a.is_approved=0 $buyer_id_cond3","pre_cost_id");
					
					//echo $pre_cost_id;die;	
                    $pre_cost_id_app_byuser=return_field_value("group_concat(distinct(b.mst_id)) as pre_cost_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$compid and b.sequence_no=$user_sequence_no and b.entry_form=7 and a.ready_to_approved=1 and a.is_approved=0 and b.current_approval_status=1","pre_cost_id");
					
                }
                else if($db_type==2)
                {
                    $seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$compid and entry_form=7 and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
                    $seqData=sql_select($seqSql);                       
                    $buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
                    $query_string ='';
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
                                    $query_string.=" (b.sequence_no=".$sRow[csf('sequence_no')]." and c.buyer_name in(".implode(",",$result).")) or ";
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
                        $buyerIds_cond=" and a.buyer_name not in($buyerIds)";
                        $seqCond=" and (".chop($query_string,'or ').")";
                    }
                    //echo $query_string;
                    $sequence_no_by_no=chop($sequence_no_by_no,',');
                    $sequence_no_by_yes=chop($sequence_no_by_yes,',');
                    
                    if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
                    if($sequence_no_by_no=="") $sequence_no_by_no=0;
                    
                    
                    $pre_cost_id='';
                    //echo $userid."==".$buyer_id_cond3."=".$seqCond; //die;
                    //echo $userid."==".$seqCond; //die;
                   /* $pre_cost_id_sql="select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$compid and b.sequence_no in ($sequence_no_by_no) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond3 $seqCond
                    union
                    select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$compid and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond3";*/
					
					
                    $pre_cost_id_sql="select distinct (mst_id) as pre_cost_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$compid and b.sequence_no in ($sequence_no_by_no) and b.entry_form=7 and b.current_approval_status=1 and a.is_approved=0 $buyer_id_cond3 $seqCond
                    union
                    select distinct (mst_id) as pre_cost_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$compid and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=7 and b.current_approval_status=1 and a.is_approved=0 $buyer_id_cond3";
					
					$bResult=sql_select($pre_cost_id_sql);
                    foreach($bResult as $bRow)
                    {
                        $pre_cost_id.=$bRow[csf('pre_cost_id')].",";
                    }
                    
                    $pre_cost_id=chop($pre_cost_id,',');
               
                    $pre_cost_id_app_sql=sql_select("select b.mst_id as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c 
                    where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$compid and b.sequence_no=$user_sequence_no and b.entry_form=7 and a.ready_to_approved=1 and b.current_approval_status=1");
                    $pre_cost_id_app_byuser ="";
                    foreach($pre_cost_id_app_sql as $inf)
                    {                   
                        if($pre_cost_id_app_byuser!="") $pre_cost_id_app_byuser.=",".$inf[csf('pre_cost_id')];
                        else $pre_cost_id_app_byuser.=$inf[csf('pre_cost_id')];
                        //echo "<pre>";
                        //print_r($inf);
                    }
                   
                    $pre_cost_id_app_byuser=implode(",",array_unique(explode(",",$pre_cost_id_app_byuser)));
                }
                //echo $userid."==".$pre_cost_id."===".$pre_cost_id_app_byuser."<br>"; 
                    
                $result=array_diff(explode(',',$pre_cost_id),explode(',',$pre_cost_id_app_byuser));
                
                //var_dump($result);
                
                $pre_cost_id=implode(",",$result);
               
                $pre_cost_id_cond="";
                
                if($pre_cost_id_app_byuser!="")
                {
                    $pre_cost_id_app_byuser_arr=explode(",",$pre_cost_id_app_byuser);
                    if(count($pre_cost_id_app_byuser_arr)>995)
                    {
                        $pre_cost_id_app_byuser_chunk_arr=array_chunk(explode(",",$pre_cost_id_app_byuser),995) ;
                        foreach($pre_cost_id_app_byuser_chunk_arr as $chunk_arr)
                        {
                            $chunk_arr_value=implode(",",$chunk_arr);	
                            $pre_cost_id_cond.=" and b.id not in($chunk_arr_value)";	
                        }
                    }
                    else
                    {
                        $pre_cost_id_cond=" and b.id not in($pre_cost_id_app_byuser)";	 
                    }
                    
                }
                else $pre_cost_id_cond="";
                                   
                $sql="select b.id,a.gmts_item_id,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,0 as approval_id,
                b.approved,b.inserted_by 
                from wo_booking_mst a
                where a.company_id=$compid and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.is_approved =0 $buyer_id_cond $pre_cost_id_cond $buyerIds_cond $buyer_id_cond2";
                if($pre_cost_id!="")
                {
                    $sql.=" union all
                    select b.id,a.gmts_item_id,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,0 as approval_id,
                    b.approved,b.inserted_by 
                    from wo_booking_mst a
                    where  a.company_id=$compid  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.is_approved=0 and (a.id in($pre_cost_id)) $buyer_id_cond $buyer_id_cond2";

                }  
               
            }
            else
            {
                $buyer_ids=$buyer_ids_array[$userid]['u'];
                if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
                
                $user_sequence_no=$user_sequence_no-1;
                if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
                else
                {
                    if($db_type==0)
                    {
                        $sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$compid and entry_form=7 and
                         sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
                    }
                    else
                    {
                        $sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no)
                         as sequence_no","electronic_approval_setup","company_id=$compid and entry_form=7 and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and 	
                    }
                }
                
                if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
                else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
                
				$sql="select a.booking_no,a.booking_type,a.job_no, a.buyer_id,a.inserted_by,
                  b.id as approval_id, b.sequence_no, b.approved_by
                  from wo_booking_mst a,approval_history b
                  where a.id=b.mst_id and b.entry_form=7 and a.company_id=$compid  and a.is_deleted=0 and
                  a.status_active=1 and b.current_approval_status=1 and a.is_approved=0 and a.ready_to_approved=1 $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond";
            
			}
            
        }
        
		  //echo $sql;die;
		
		
        if($userSequence == $endSequence) {
            $subject = "Following Booking Sheet Is Ready For Final Approval";
        } else {
            $subject = "Following Booking Sheet Is Ready For Approval";
        }
        
        $htmlheader='';
        $html_arr = array();
        $htmlheader='
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="8" align="center">
                    <strong style="font-size:23px;">'.$company_library[$compid].'</strong><br>
                    <strong style="font-size:14px;">'.ucfirst($user_arr[$userid]).'</strong>
                </td>
            </tr>
            <tr>
                <td colspan="8" align="center">
                    <b style="font-size:14px;">Upto Date : '. date("d-m-Y h:i:s a",time()).'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="8" align="center"><b>'.$subject.'</b></td>
            </tr>
          
        </table>
        <br>
        
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="150">Booking</th>
                    <th width="150">Booking Type</th>
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
                        $garments_items = "";
                        $gmts_item_id_arr = explode(',',$row[csf(gmts_item_id)]);
                        foreach($gmts_item_id_arr as $gmts_item=>$val_id){
                            if($garments_items==""){$garments_items = $garments_item[$val_id];}else {$garments_items .=",".$garments_item[$val_id]; }
                        }  
                        $htmlBody.='
                        <tr bgcolor="'.$bgcolor.'">
                            <td align="center">'.$i.'</td>
                            <td>'.$buyer_library[$row[csf(buyer_id)]].'</td>
                            <td>'.$row[csf(job_no)].'</td>
                            <td>'.$row[csf(booking_no)].'</td>
                            <td>'.$report_name[$row[csf(booking_type)]].'</td>
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
                <th align="right"></th>
            </tfoot>
        </table>'; 
        
        echo  $html=$htmlheader.$htmlBody.$htmlFooter;

        $to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$userid";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

    	$message = $html;
		$header = mail_header();
		
		//if($to!="" && $htmlBody!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
 
    } // ready to approve user sequence loop end  
    



    echo "//=========================  <b>ready to approve mail end</b> ==========================================//";
    
    echo "<br>";
    echo "//========================<b> Approved job mail data Start </b> ========================================//<br>";
    
    // apply for approval job list 
    $applyForApprovalJobsSql = sql_select("select job_no,mst_id,approved_by,avg(current_approval_status) as current_approval_status from CO_COM_PRE_COSTING_APPROVAL WHERE approved_by NOT IN ($sequence_wise_user_id_arr[$endSequence])  group by job_no,mst_id,approved_by order by job_no");  
    $applyForApprovalJobs = array();  
    foreach ($applyForApprovalJobsSql as $datrow) {
        if($datrow[csf(current_approval_status)]==1) { 
		   	if($tmp[$datrow[csf('job_no')]]=='') $i=0;
			$applyForApprovalJobs[$datrow[csf('job_no')]][$i] = $datrow[csf('approved_by')];
			$tmp[$datrow[csf('job_no')]]=$datrow[csf('job_no')];     
			$i++;      
        }        
    }  

    // Higher Authority Approved job list
    $higherAuthoritySql = sql_select("select job_no,mst_id,approved_by,avg(current_approval_status) as current_approval_status from co_com_pre_costing_approval WHERE approved_by IN ($sequence_wise_user_id_arr[$endSequence])  group by job_no,mst_id,approved_by");
    $finalApproveJobs = array();     
    foreach ($higherAuthoritySql as $row) {          
        if ($row[csf('current_approval_status')]==1) {
            $finalApproveJobs[$row[csf('approved_by')]][$row[csf('job_no')]] = $row[csf('job_no')];
			$final_app_jobs_arr[]= $row[csf('job_no')];
        }
    }

    
    /* echo "<pre>";
    print_r($finalApproveJobs);  */    
    
    $userWiseApprovedJobMailData = array();
	foreach( $finalApproveJobs as $appr=>$jobs)
	{          
		foreach($jobs as $job)
		{
			foreach( $applyForApprovalJobs[$job] as $key=>$apprver)
			{              
                $userWiseApprovedJobMailData[$apprver][$job] = $job;
			}
            
		}
	}
     
    /* echo "<pre>";
    print_r($userWiseApprovedJobMailData);     */
     
    $finalAuthorityJobs = "";
    foreach ($final_app_jobs_arr as $key=>$value) {
        if($finalAuthorityJobs == "") {$finalAuthorityJobs = "'".$value."'";} else {$finalAuthorityJobs .=","."'".$value."'"; }
    }
    //echo $finalAuthorityJobs ."<br>";
    
    $finalAuthorityAppSql = "select a.gmts_item_id,a.job_no,a.buyer_name, a.style_ref_no,b.costing_date,c.approved_by from wo_pre_cost_mst b,wo_po_details_master a,co_com_pre_costing_approval c where a.job_no=b.job_no and b.id= c.mst_id and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and a.job_no IN($finalAuthorityJobs) order by a.job_no";
    $result = sql_select( $finalAuthorityAppSql );
    
    $cost_com_app_data = array();
    foreach ($result as $row) {
        $cost_com_app_data[$row[csf(approved_by)]][$row[csf(job_no)]][buyer_name]   = $row[csf(buyer_name)];
        $cost_com_app_data[$row[csf(approved_by)]][$row[csf(job_no)]][job_no]       = $row[csf(job_no)];     
        $cost_com_app_data[$row[csf(approved_by)]][$row[csf(job_no)]][style_ref_no] = $row[csf(style_ref_no)];     
        $cost_com_app_data[$row[csf(approved_by)]][$row[csf(job_no)]][gmts_item_id] = $row[csf(gmts_item_id)];     
        $cost_com_app_data[$row[csf(approved_by)]][$row[csf(job_no)]][costing_date] = $row[csf(costing_date)];           
    }
 
    /* echo "<pre>";
    print_r($cost_com_app_data);*/
    
    foreach ($userWiseApprovedJobMailData as $approval_user=>$approvedJobArr) {
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
                <td colspan="8" align="center">                    
                    <b style="font-size:14px;">Upto Date : '. date("d-m-Y h:i:s a",time()).'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="8" align="center"><b>Following pre-cost sheet has been approved</b></td>
            </tr>
          
        </table>
        <br>
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="150">Style Ref.</th>
                    <th width="150">Gmts Item</th>
                    <th width="70">Costing Date</th>
                    <th width="100">Un-App. Cost Component</th>
                </tr>
            </thead>
            <tbody>';
    
            $i=1;
            $htmlBody_approved ="";
            foreach ($approvedJobArr as $appJob) {          
                //$user_sequence[$user];
				if($cost_com_app_data[$approval_user][$appJob][job_no] !='') {
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                $garments_items = "";
                $gmts_item_id_arr = explode(',',$cost_com_app_data[$approval_user][$appJob][gmts_item_id]);
                foreach($gmts_item_id_arr as $gmts_item=>$val_id){
                    if($garments_items==""){$garments_items = $garments_item[$val_id];}else {$garments_items .=",".$garments_item[$val_id]; }
                } 
                $htmlBody_approved.='
                <tr bgcolor="'.$bgcolor.'">
                    <td align="center">'.$i.'</td>                  
                    <td>'.$buyer_library[$cost_com_app_data[$approval_user][$appJob][buyer_name]].'</td>
                    <td>'.$cost_com_app_data[$approval_user][$appJob][job_no].'</td>
                    <td>'.$cost_com_app_data[$approval_user][$appJob][style_ref_no].'</td>
                    <td>'.$garments_items.'</td>
                    <td>'.change_date_format($cost_com_app_data[$approval_user][$appJob][costing_date]).'</td>
                    <td align="center"></td>
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
            <th align="right"></th>
            <th>&nbsp;</th>
            <th align="right"></th>
        </tfoot>
        </table>';
        //if($htmlBody_approved!=''){$approve_html_arr[$approval_user]=$htmlheader.$htmlBody_approved.$htmlFooter;}
        
       /* foreach($approve_html_arr as $html){
            echo $html;
        }*/
        
		echo $html = $htmlheader.$htmlBody_approved.$htmlFooter;
        
		$to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$approval_user";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

 		$subject = "Following pre-cost sheet has been approved";
    	$message = $html;
		$header = mail_header();
		
		//if($to!="" && $htmlBody_approved!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
		
    }
    echo "//========================<b> Approved job mail data End </b> ========================================//";
    
    echo "<br>";
    echo "//========================<b> Unapproved job mail data Start </b>====================================//<br>";
    //var_dump($final_un_app_jobs_arr);
    
    
    // apply for approval job list 
    $apply_job_sql = sql_select("select a.mst_id,b.job_no,a.approved_by from approval_history a,co_com_pre_costing_approval b WHERE a.mst_id = b.mst_id and a.approved_by NOT IN ($sequence_wise_user_id_arr[$endSequence])  group by a.mst_id,b.job_no,a.approved_by order by job_no");  
    

    $applyForUNapprovalJobs = array();  
    foreach ($apply_job_sql as $datrow) {      
        if($tmp[$datrow[csf('job_no')]]=='') $i=0;
        $applyForUNapprovalJobs[$datrow[csf('job_no')]][$i] = $datrow[csf('approved_by')];
        $tmp[$datrow[csf('job_no')]]=$datrow[csf('job_no')];     
        $i++;                  
    } 
    
    // Higher Authority Unapproved job list
    $higherAuthorityUnAppSql = sql_select("select a.mst_id,b.job_no,a.approved_by from approval_history a,co_com_pre_costing_approval b WHERE a.mst_id = b.mst_id and a.approved_by IN ($sequence_wise_user_id_arr[$endSequence]) and b.current_approval_status in(0)  group by a.mst_id,b.job_no,a.approved_by order by job_no");  
    $finalUnApproveJobs = array();     
    foreach ($higherAuthorityUnAppSql as $row) { 
        $finalUnApproveJobs[$row[csf('approved_by')]][$row[csf('job_no')]] = $row[csf('job_no')];
        $final_un_app_jobs_arr[]= $row[csf('job_no')];       
    }
    
   
    $userWiseUnApprovedJobMailData = array();
    
	foreach( $finalUnApproveJobs as $unAppr=>$unapp_jobs)
	{          
		foreach($unapp_jobs as $unapp_job) {
            
            foreach( $applyForUNapprovalJobs[$unapp_job] as $key=>$applier) {
               
                $userWiseUnApprovedJobMailData[$applier][$unapp_job] = $unapp_job;
                
            }
        
        }       
	}
    
    
    
    $finalAuthorityUnAppJobs = "";
    foreach ($final_un_app_jobs_arr as $key=>$value) {
        if($finalAuthorityUnAppJobs == "") {$finalAuthorityUnAppJobs = "'".$value."'";} else {$finalAuthorityUnAppJobs .=","."'".$value."'"; }
    }
    //echo "143==".$finalAuthorityUnAppJobs."<br>";
    
    $finalAuthorityUnAppSql = "select a.gmts_item_id,a.job_no,a.buyer_name, a.style_ref_no,b.costing_date,c.id,c.approved_by,c.cost_component_id,c.current_approval_status from wo_pre_cost_mst b,wo_po_details_master a,co_com_pre_costing_approval c where a.job_no=b.job_no and b.id= c.mst_id and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $buyer_id_cond2 and a.job_no IN($finalAuthorityUnAppJobs) and c.current_approval_status in(0) order by a.job_no";
    $un_app_result = sql_select( $finalAuthorityUnAppSql );

    $cost_com_un_app_data = array();
    $com_component_status = array();
    foreach ($un_app_result as $un_app_row) {
        //if($un_app_row[csf(current_approval_status)]==0) {
        $cost_com_un_app_data[$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][buyer_name]   = $un_app_row[csf(buyer_name)];
        $cost_com_un_app_data[$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][job_no]       = $un_app_row[csf(job_no)];     
        $cost_com_un_app_data[$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][style_ref_no] = $un_app_row[csf(style_ref_no)];     
        $cost_com_un_app_data[$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][gmts_item_id] = $un_app_row[csf(gmts_item_id)];     
        $cost_com_un_app_data[$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][costing_date] = $un_app_row[csf(costing_date)];           
        $com_component_status[$un_app_row[csf(id)]][$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][$un_app_row[csf(cost_component_id)]] = $un_app_row[csf(current_approval_status)];           
       // }
    }
    
    $userwise_job_compStatus = array();
    foreach($com_component_status as $idArr=>$userArr) {
        foreach ($userArr as $comp_user_id=>$jobArr) {
            foreach($jobArr as $comp_job=>$costComponentIdArr) {
                $comp_jobs[$comp_job]=$comp_job;
                foreach ($costComponentIdArr as $costComponentId=>$costComponentStatus) {
                    if($com_component_status[$comp_user_id][$comp_user_id][$comp_job][$costComponentId]==0) {
                        $userwise_job_compStatus[$comp_user_id][$comp_job][$costComponentId] = $costComponentId."__".$costComponentStatus;
                    }
                }
            }        
        }   
    }
    
    //var_dump($comp_jobs);
    //var_dump($userwise_job_compStatus);

    foreach ($userWiseUnApprovedJobMailData as $applier_user=>$unApprovedJobArr) {

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
                <td colspan="8" align="center"><b>Following Pre-Cost Sheet Has Been Un-approved</b></td>
            </tr>
          
        </table>
        
        <br>
        
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="150">Style Ref.</th>
                    <th width="150">Gmts Item</th>
                    <th width="70">Costing Date</th>
                    <th>Un-App. Cost Component</th>
                </tr>
            </thead>
            <tbody>';
    
            $i=1;
            $htmlBody_un_approved ="";
            foreach ($final_un_app_jobs_arr as $unAppJob) {
                if($cost_com_un_app_data[$applier_user][$unAppJob][job_no]!='') {               
                    //$user_sequence[$user];
                    
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                    $garments_items = "";
                    $gmts_item_id_arr = explode(',',$cost_com_un_app_data[$applier_user][$unAppJob][gmts_item_id]);
                    foreach($gmts_item_id_arr as $gmts_item=>$val_id){
                        if($garments_items==""){$garments_items = $garments_item[$val_id];}else {$garments_items .=",".$garments_item[$val_id]; }
                    } 
                    
                    $compStatus = "";
                    foreach ($userwise_job_compStatus[$applier_user][$unAppJob] as $cosid=>$cost_comp_status) {
                        if($compStatus =="") {$compStatus = $cost_components[$cosid];} else {$compStatus .= ", ".$cost_components[$cosid];}               
                    }
                    
                    $htmlBody_un_approved.='
                    <tr bgcolor="'.$bgcolor.'">
                        <td align="center">'.$i.'</td>                  
                        <td>'.$buyer_library[$cost_com_un_app_data[$applier_user][$unAppJob][buyer_name]].'</td>
                        <td>'.$cost_com_un_app_data[$applier_user][$unAppJob][job_no].'</td>
                        <td>'.$cost_com_un_app_data[$applier_user][$unAppJob][style_ref_no].'</td>
                        <td>'.$garments_items.'</td>
                        <td>'.change_date_format($cost_com_un_app_data[$applier_user][$unAppJob][costing_date]).'</td>
                        <td align="center">'.$compStatus.'</td>
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
            <th align="right"></th>
            <th>&nbsp;</th>
            <th align="right"></th>
        </tfoot>
        </table>';

		echo $html = $htmlheader.$htmlBody_un_approved.$htmlFooter;
        
		$to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$applier_user";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

 		$subject = "Following Pre-cost Sheet Has Been Un-approved";
    	$message = $html;
		$header = mail_header();
		
		//if($to!="" && $htmlBody_un_approved!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
        
    }
    echo "//========================<b> Unapproved job mail data End </b>====================================//";
    
    echo "<br>// ================== <b>Merchandiser approved mail data Start</b> ========================//<br>";

    
    // apply for approval job list 
    $applyJobsByMarchantiger = sql_select("select b.job_no,b.inserted_by as marchantiger from wo_pre_cost_mst b,wo_po_details_master a where a.job_no=b.job_no and a.company_name=$compid and b.ready_to_approved=1 and b.approved = 1 and b.partial_approved = 1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0");

    $applyByMarchantiger = array();  
    foreach ($applyJobsByMarchantiger as $datrow) {       
        if($tmp[$datrow[csf('job_no')]]=='') $i=0;
        $applyByMarchantiger[$datrow[csf('job_no')]][$i] = $datrow[csf('marchantiger')];
        $tmp[$datrow[csf('job_no')]]=$datrow[csf('job_no')];     
        $i++;                   
    }  
    
   //var_dump($applyByMarchantiger);
    
    // Higher Authority Approved job list
    $higherAuthoritySql = sql_select("select job_no,approved_by,avg(current_approval_status) as current_approval_status from co_com_pre_costing_approval WHERE approved_by IN ($sequence_wise_user_id_arr[$endSequence])  group by job_no,approved_by");
    $higherAuthorityApproveJobs = array();     
    foreach ($higherAuthoritySql as $row) {       
        if ($row[csf('current_approval_status')]==1) {
            $higherAuthorityApproveJobs[$row[csf('approved_by')]][$row[csf('job_no')]] = $row[csf('job_no')];
			$higherAuthority_app_jobs_arr[]= $row[csf('job_no')];
        }
    }	   
    
    $marchantigerWiseApprovedJobMailData = array();
	foreach( $higherAuthorityApproveJobs as $appr=>$jobs)
	{          
		foreach($jobs as $job)
		{
			foreach( $applyByMarchantiger[$job] as $key=>$marchantiger)
			{                
                $marchantigerWiseApprovedJobMailData[$marchantiger][$job] = $job;
			}           
		}
	}
    
    
    $finalAuthorityJobs = "";  
    foreach ($final_app_jobs_arr as $key=>$value) {
        if($finalAuthorityJobs == "") {$finalAuthorityJobs = "'".$value."'";} else {$finalAuthorityJobs .=","."'".$value."'"; }
    }
    // echo "143==".$finalAuthorityJobs."<br>";
    
    $finalAuthorityAppSql = "select a.gmts_item_id,a.job_no,a.buyer_name, a.style_ref_no,b.costing_date,b.inserted_by from wo_pre_cost_mst b,wo_po_details_master a,co_com_pre_costing_approval c where a.job_no=b.job_no and b.id= c.mst_id and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 $buyer_id_cond2 and a.job_no IN($finalAuthorityJobs) order by a.job_no";
    $result = sql_select( $finalAuthorityAppSql );
    
    $marchant_approve_data = array();
    foreach ($result as $row) {
        $marchant_approve_data[$row[csf(inserted_by)]][$row[csf(job_no)]][buyer_name]   = $row[csf(buyer_name)];
        $marchant_approve_data[$row[csf(inserted_by)]][$row[csf(job_no)]][job_no]       = $row[csf(job_no)];     
        $marchant_approve_data[$row[csf(inserted_by)]][$row[csf(job_no)]][style_ref_no] = $row[csf(style_ref_no)];     
        $marchant_approve_data[$row[csf(inserted_by)]][$row[csf(job_no)]][gmts_item_id] = $row[csf(gmts_item_id)];     
        $marchant_approve_data[$row[csf(inserted_by)]][$row[csf(job_no)]][costing_date] = $row[csf(costing_date)];           
    }
 
    foreach ($marchantigerWiseApprovedJobMailData as $marchantiger=>$approvedJobArr) {
    
        $approve_html_arr = array();
        $htmlheader='
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="8" align="center">
                    <strong style="font-size:23px;">'.$company_library[$compid].'</strong> <br>
                    <strong style="font-size:14px;">'.ucfirst($user_arr[$marchantiger]).'</strong>
                </td>
            </tr>
            <tr>
                <td colspan="8" align="center">                    
                    <b style="font-size:14px;">Upto Date : '. date("d-m-Y h:i:s a",time()).'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="8" align="center"><b>Following Pre-Cost Sheet Has Been Approved</b></td>
            </tr>
          
        </table>
       
        <br>
       
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="150">Style Ref.</th>
                    <th width="150">Gmts Item</th>
                    <th width="70">Costing Date</th>
                    <th width="100">Un-App. Cost Component</th>
                </tr>
            </thead>
            <tbody>';
    
            $i=1;
            $htmlBody_approved ="";
            foreach ($approvedJobArr as $appJob) {          
                //$user_sequence[$user];
				if($marchant_approve_data[$marchantiger][$appJob][job_no] !='') {
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                $garments_items = "";
                $gmts_item_id_arr = explode(',',$marchant_approve_data[$marchantiger][$appJob][gmts_item_id]);
                foreach($gmts_item_id_arr as $gmts_item=>$val_id){
                    if($garments_items==""){$garments_items = $garments_item[$val_id];}else {$garments_items .=",".$garments_item[$val_id]; }
                } 
                $htmlBody_approved.='
                <tr bgcolor="'.$bgcolor.'">
                    <td align="center">'.$i.'</td>                  
                    <td>'.$buyer_library[$marchant_approve_data[$marchantiger][$appJob][buyer_name]].'</td>
                    <td>'.$marchant_approve_data[$marchantiger][$appJob][job_no].'</td>
                    <td>'.$marchant_approve_data[$marchantiger][$appJob][style_ref_no].'</td>
                    <td>'.$garments_items.'</td>
                    <td>'.change_date_format($marchant_approve_data[$marchantiger][$appJob][costing_date]).'</td>
                    <td align="center"></td>
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
            <th align="right"></th>
            <th>&nbsp;</th>
            <th align="right"></th>
        </tfoot>
        </table>';
        
        
		echo $html = $htmlheader.$htmlBody_approved.$htmlFooter;
        
		$to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$marchantiger";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

 		$subject = "Following Pre-Cost Sheet Has Been Approved";
    	$message = $html;
		$header = mail_header();
		
		//if($to!="" && $htmlBody_approved!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
        
    }
    
    echo "// ========================== <b>Merchandiser approved mail data End</b> =====================//<br>";
    echo "//=================<b>Merchandiser Unapproved mail data Start</b> // ===================================//<br>";
    
    // apply for approval job list 
    $apply_job_by_marchantiger = sql_select("select a.job_no,a.inserted_by as marchantiger from wo_pre_cost_mst a, co_com_pre_costing_approval b, approval_history c WHERE a.id = b.mst_id and b.mst_id=c.mst_id and c.approved_by NOT IN ($sequence_wise_user_id_arr[$endSequence])  group by a.job_no,a.inserted_by order by job_no");  
    

    $unApproveJobsOfMarchantiger = array();  
    foreach ($apply_job_by_marchantiger as $datrow) {      
        if($tmp[$datrow[csf('job_no')]]=='') $i=0;
        $unApproveJobsOfMarchantiger[$datrow[csf('job_no')]][$i] = $datrow[csf('marchantiger')];
        $tmp[$datrow[csf('job_no')]]=$datrow[csf('job_no')];     
        $i++;                  
    } 
    
    
    // Higher Authority Unapproved job list
    $higherAuthorityUnAppSql = sql_select("select a.mst_id,b.job_no,a.approved_by from approval_history a,co_com_pre_costing_approval b WHERE a.mst_id = b.mst_id and a.approved_by IN ($sequence_wise_user_id_arr[$endSequence]) and b.current_approval_status in(0)  group by a.mst_id,b.job_no,a.approved_by order by job_no");  
    $higherAuthorityUnApproveJobs = array();     
    foreach ($higherAuthorityUnAppSql as $row) { 
        $higherAuthorityUnApproveJobs[$row[csf('approved_by')]][$row[csf('job_no')]] = $row[csf('job_no')];
        $higherAuthority_un_app_jobs_arr[]= $row[csf('job_no')];       
    }
    
   
    $marchantigerWiseUnApprovedJobMailData = array();
    
	foreach( $higherAuthorityUnApproveJobs as $unAppr=>$unapp_jobs)
	{          
		foreach($unapp_jobs as $unapp_job) {
            
            foreach( $unApproveJobsOfMarchantiger[$unapp_job] as $key=>$marchantiger) {              
                $marchantigerWiseUnApprovedJobMailData[$marchantiger][$unapp_job] = $unapp_job;               
            }
        
        }       
	}
    
    
    
    $finalAuthorityUnAppJobs = "";
    foreach ($final_un_app_jobs_arr as $key=>$value) {
        if($finalAuthorityUnAppJobs == "") {$finalAuthorityUnAppJobs = "'".$value."'";} else {$finalAuthorityUnAppJobs .=","."'".$value."'"; }
    }
    
    $finalAuthorityUnAppSql = "select a.gmts_item_id,a.job_no,a.buyer_name, a.style_ref_no,b.costing_date,b.inserted_by,c.id,c.approved_by,c.cost_component_id,c.current_approval_status from wo_pre_cost_mst b,wo_po_details_master a,co_com_pre_costing_approval c where a.job_no=b.job_no and b.id= c.mst_id and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $buyer_id_cond2 and a.job_no IN($finalAuthorityUnAppJobs) and c.current_approval_status in(0) order by a.job_no";
    $un_app_result = sql_select( $finalAuthorityUnAppSql );

    $marchant_un_app_data = array();
    $com_component_status = array();
    $appoved_aby = array();
    foreach ($un_app_result as $un_app_row) {
        //if($un_app_row[csf(current_approval_status)]==0) {
        $marchant_un_app_data[$un_app_row[csf(inserted_by)]][$un_app_row[csf(job_no)]][buyer_name]   = $un_app_row[csf(buyer_name)];
        $marchant_un_app_data[$un_app_row[csf(inserted_by)]][$un_app_row[csf(job_no)]][job_no]       = $un_app_row[csf(job_no)];     
        $marchant_un_app_data[$un_app_row[csf(inserted_by)]][$un_app_row[csf(job_no)]][style_ref_no] = $un_app_row[csf(style_ref_no)];     
        $marchant_un_app_data[$un_app_row[csf(inserted_by)]][$un_app_row[csf(job_no)]][gmts_item_id] = $un_app_row[csf(gmts_item_id)];     
        $marchant_un_app_data[$un_app_row[csf(inserted_by)]][$un_app_row[csf(job_no)]][costing_date] = $un_app_row[csf(costing_date)];           
        $com_component_status[$un_app_row[csf(id)]][$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][$un_app_row[csf(cost_component_id)]] = $un_app_row[csf(current_approval_status)];           
        $appoved_byArr[$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]] = $un_app_row[csf(job_no)];
       // }
    }
    
    //var_dump($marchant_un_app_data);
    
    $userwise_job_compStatus = array();
    foreach($com_component_status as $idArr=>$userArr) {
        foreach ($userArr as $comp_user_id=>$jobArr) {
            foreach($jobArr as $comp_job=>$costComponentIdArr) {
                $comp_jobs[$comp_job]=$comp_job;
                foreach ($costComponentIdArr as $costComponentId=>$costComponentStatus) {
                    if($com_component_status[$comp_user_id][$comp_user_id][$comp_job][$costComponentId]==0) {
                        $userwise_job_compStatus[$comp_user_id][$comp_job][$costComponentId] = $costComponentId."__".$costComponentStatus;
                    }
                }
            }        
        }   
    }
    

    foreach ($marchantigerWiseUnApprovedJobMailData as $marchantiger=>$unApprovedJobArr) {

        $unApprove_html_arr = array();
        $htmlheader='
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="8" align="center">
                    <strong style="font-size:23px;">'.$company_library[$compid].'</strong> <br>
                    <strong style="font-size:14px;">'.ucfirst($user_arr[$marchantiger]).'</strong>
                </td>
            </tr>
            <tr>
                <td colspan="8" align="center">                    
                    <b style="font-size:14px;">Upto Date : '. date("d-m-Y h:i:s a",time()).'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="8" align="center"><b>Following Pre-Cost Sheet Has Been Un-approved</b></td>
            </tr>
          
        </table>
        
        <br>
        
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="150">Style Ref.</th>
                    <th width="150">Gmts Item</th>
                    <th width="70">Costing Date</th>
                    <th>Un-App. Cost Component</th>
                </tr>
            </thead>
            <tbody>';
    
            $i=1;
            $htmlBody_un_approved ="";
            foreach ($higherAuthority_un_app_jobs_arr as $unAppJob) {
                if($marchant_un_app_data[$marchantiger][$unAppJob][job_no]!='') {               
                    //$user_sequence[$user];
                    
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                    $garments_items = "";
                    $gmts_item_id_arr = explode(',',$marchant_un_app_data[$marchantiger][$unAppJob][gmts_item_id]);
                    foreach($gmts_item_id_arr as $gmts_item=>$val_id){
                        if($garments_items==""){$garments_items = $garments_item[$val_id];}else {$garments_items .=",".$garments_item[$val_id]; }
                    } 

                    
                    foreach ($appoved_byArr as $approvedBy=>$jobArr) {
                        foreach($jobArr as $app_job) {
                            $compStatus = "";
                            foreach ($userwise_job_compStatus[$approvedBy][$unAppJob] as $cosid=>$cost_comp_status) {                              
                                if($compStatus =="") {$compStatus = $cost_components[$cosid];} else {$compStatus .= ", ".$cost_components[$cosid];}                                              
                            }               
                        }
                    }
                    
                    
                    $htmlBody_un_approved.='
                    <tr bgcolor="'.$bgcolor.'">
                        <td align="center">'.$i.'</td>                  
                        <td>'.$buyer_library[$marchant_un_app_data[$marchantiger][$unAppJob][buyer_name]].'</td>
                        <td>'.$marchant_un_app_data[$marchantiger][$unAppJob][job_no].'</td>
                        <td>'.$marchant_un_app_data[$marchantiger][$unAppJob][style_ref_no].'</td>
                        <td>'.$garments_items.'</td>
                        <td>'.change_date_format($marchant_un_app_data[$marchantiger][$unAppJob][costing_date]).'</td>
                        <td align="center">'.$compStatus.'</td>
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
            <th align="right"></th>
            <th>&nbsp;</th>
            <th align="right"></th>
        </tfoot>
        </table>';

		echo $html = $htmlheader.$htmlBody_un_approved.$htmlFooter;
		die; 
		$to="";
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$marchantiger";
		$mail_sql = sql_select($sql);
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}
 		$subject = "Following Pre-Cost Sheet Has Been Un-approved";
    	$message = $html;
		$header = mail_header();
		//if($to!="" && $htmlBody_un_approved!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
    }
       
    echo "//====================<b>Merchandiser Unapproved mail data End</b> // ==============================//<br>";
   
} // company loop end 








//Pre-cost Auto mail..............................................................................

//$company_library = array(3=>$company_library[3]);
//echo count($company_library);
$page_id = 869;

foreach($company_library as $compid=>$compname)
{	
    $sequence_wise_user_id_arr=array();
	$sql = "select user_id,buyer_id,bypass,sequence_no FROM electronic_approval_setup where company_id = $compid and page_id=$page_id and is_deleted=0 order by sequence_no,user_id asc";
    $elecData_array=sql_select($sql);
	$i=0;
	foreach($elecData_array as $erow){
		 $sequence_wise_user_id_arr[$erow[csf(sequence_no)]] = $erow[csf(user_id)];
		 $sequence_wise_buyer_id_arr[$erow[csf(sequence_no)]] = $erow[csf(buyer_id)];
         
         $use_seque[$erow[csf(user_id)]] = $erow[csf(sequence_no)];
         
         $startSequence = min($use_seque);
         $endSequence = max($use_seque);
		 $i++;
	}
    //echo "// =========================  <b>ready to approve mail start<b> ==========================================//<br> ";
    
    //$sequence_wise_user_id_arr= array(3=>143);
    // $compid=3;
    //print_r($sequence_wise_user_id_arr); die;
    foreach($sequence_wise_user_id_arr as $userSequence=>$userid)
    {  
        $approval_type = 2;	
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

        if($approval_type==2)
        {
            if($db_type==0)
            {
                $sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$compid and page_id=$page_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
            }
            else
            {
                $sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$compid and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
            }
                            
            if($user_sequence_no==$min_sequence_no)
            {
                $buyer_ids = $buyer_ids_array[$userid]['u'];
                if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
                
                $sql="select b.id,a.gmts_item_id,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,'0' as approval_id, b.partial_approved,b.inserted_by from wo_pre_cost_mst b,  wo_po_details_master a where a.job_no=b.job_no and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.partial_approved=2 $buyer_id_cond $buyer_id_cond2 $date_cond";
            }
            else if($sequence_no == "")
            {				
                $buyer_ids=$buyer_ids_array[$userid]['u'];
                
                if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
                
                if( $buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_name in($buyer_ids)";
                //echo $userid."==".$buyer_id_cond3; //die;     
                if($db_type==0)
                {
                    $seqSql="select group_concat(sequence_no) as sequence_no_by,
     group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$compid and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
                    $seqData=sql_select($seqSql);
                    
                    $sequence_no_by=$seqData[0][csf('sequence_no_by')];
                    $buyerIds=$seqData[0][csf('buyer_ids')];
                    
                    if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.buyer_name not in($buyerIds)";
                    
                    $pre_cost_id=return_field_value("group_concat(distinct(mst_id)) as pre_cost_id","wo_pre_cost_mst a, approval_history b,wo_po_details_master c","a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$compid and b.sequence_no in ($sequence_no_by) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3","pre_cost_id");
                    //echo $pre_cost_id;die;	
                    $pre_cost_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as pre_cost_id","wo_pre_cost_mst a, approval_history b,wo_po_details_master c","a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$compid and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1","pre_cost_id");
                }
                else if($db_type==2)
                {
                    $seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$compid and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
                    $seqData=sql_select($seqSql);                       
                    $buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
                    $query_string ='';
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
                                    $query_string.=" (b.sequence_no=".$sRow[csf('sequence_no')]." and c.buyer_name in(".implode(",",$result).")) or ";
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
                        $buyerIds_cond=" and a.buyer_name not in($buyerIds)";
                        $seqCond=" and (".chop($query_string,'or ').")";
                    }
                    //echo $query_string;
                    $sequence_no_by_no=chop($sequence_no_by_no,',');
                    $sequence_no_by_yes=chop($sequence_no_by_yes,',');
                    
                    if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
                    if($sequence_no_by_no=="") $sequence_no_by_no=0;
                    
                    
                    $pre_cost_id='';
                    //echo $userid."==".$buyer_id_cond3."=".$seqCond; //die;
                    //echo $userid."==".$seqCond; //die;
                    $pre_cost_id_sql="select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$compid and b.sequence_no in ($sequence_no_by_no) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3 $seqCond
                    union
                    select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$compid and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3";
                    $bResult=sql_select($pre_cost_id_sql);
                    foreach($bResult as $bRow)
                    {
                        $pre_cost_id.=$bRow[csf('pre_cost_id')].",";
                    }
                    
                    $pre_cost_id=chop($pre_cost_id,',');
               
                    $pre_cost_id_app_sql=sql_select("select b.mst_id as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c 
                    where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$compid and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1");
                    $pre_cost_id_app_byuser ="";
                    foreach($pre_cost_id_app_sql as $inf)
                    {                   
                        if($pre_cost_id_app_byuser!="") $pre_cost_id_app_byuser.=",".$inf[csf('pre_cost_id')];
                        else $pre_cost_id_app_byuser.=$inf[csf('pre_cost_id')];
                        //echo "<pre>";
                        //print_r($inf);
                    }
                   
                    $pre_cost_id_app_byuser=implode(",",array_unique(explode(",",$pre_cost_id_app_byuser)));
                }
                //echo $userid."==".$pre_cost_id."===".$pre_cost_id_app_byuser."<br>"; 
                    
                $result=array_diff(explode(',',$pre_cost_id),explode(',',$pre_cost_id_app_byuser));
                
                //var_dump($result);
                
                $pre_cost_id=implode(",",$result);
               
                $pre_cost_id_cond="";
                
                if($pre_cost_id_app_byuser!="")
                {
                    $pre_cost_id_app_byuser_arr=explode(",",$pre_cost_id_app_byuser);
                    if(count($pre_cost_id_app_byuser_arr)>995)
                    {
                        $pre_cost_id_app_byuser_chunk_arr=array_chunk(explode(",",$pre_cost_id_app_byuser),995) ;
                        foreach($pre_cost_id_app_byuser_chunk_arr as $chunk_arr)
                        {
                            $chunk_arr_value=implode(",",$chunk_arr);	
                            $pre_cost_id_cond.=" and b.id not in($chunk_arr_value)";	
                        }
                    }
                    else
                    {
                        $pre_cost_id_cond=" and b.id not in($pre_cost_id_app_byuser)";	 
                    }
                    
                }
                else $pre_cost_id_cond="";
                                   
                $sql="select b.id,a.gmts_item_id,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,0 as approval_id,
                b.approved,b.inserted_by 
                from wo_pre_cost_mst b,wo_po_details_master a
                where a.job_no=b.job_no and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.status_active=1
                and b.is_deleted=0 and b.ready_to_approved=1 and b.partial_approved in (0,2) $buyer_id_cond $pre_cost_id_cond $buyerIds_cond $buyer_id_cond2";
                if($pre_cost_id!="")
                {
                    //echo $userId."=="."I am here";
                    $sql.=" union all
                    select b.id,a.gmts_item_id,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,0 as approval_id,
                    b.approved,b.inserted_by 
                    from wo_pre_cost_mst b,wo_po_details_master a
                    where  a.job_no=b.job_no and a.company_name=$compid  and a.is_deleted=0 and a.status_active=1 and b.status_active=1
                    and b.is_deleted=0 and b.ready_to_approved=1 and b.partial_approved=1 and (b.id in($pre_cost_id)) $buyer_id_cond $buyer_id_cond2";

                }  
               
            }
            else
            {
                $buyer_ids=$buyer_ids_array[$userid]['u'];
                if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
                
                $user_sequence_no=$user_sequence_no-1;
                if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
                else
                {
                    if($db_type==0)
                    {
                        $sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$compid and page_id=$page_id and
                         sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
                    }
                    else
                    {
                        $sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no)
                         as sequence_no","electronic_approval_setup","company_id=$compid and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and 	
                    }
                }
                
                if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
                else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
                $sql="select b.id,a.gmts_item_id,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date, b.partial_approved,b.inserted_by,
                  c.id as approval_id, c.sequence_no, c.approved_by
                  from wo_pre_cost_mst b, wo_po_details_master a,approval_history c
                  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$compid  and a.is_deleted=0 and
                  a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and
                  b.is_deleted=0 and b.partial_approved=1 $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond";
            }
            
        }
        
        if($userSequence == $endSequence) {
            $subject = "Following Pre-Cost Sheet Is Ready For Final Approval";
        } else {
            $subject = "Following Pre-Cost Sheet Is Ready For Approval";
        }
        
        $htmlheader='';
        $html_arr = array();
        $htmlheader='
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="8" align="center">
                    <strong style="font-size:23px;">'.$company_library[$compid].'</strong><br>
                    <strong style="font-size:14px;">'.ucfirst($user_arr[$userid]).'</strong>
                </td>
            </tr>
            <tr>
                <td colspan="8" align="center">
                    <b style="font-size:14px;">Upto Date : '. date("d-m-Y h:i:s a",time()).'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="8" align="center"><b>'.$subject.'</b></td>
            </tr>
          
        </table>
        <br>
        
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="150">Style Ref.</th>
                    <th width="150">Gmts Item</th>
                    <th width="70">Costing Date</th>
                    <th width="100">Un-App. Cost Component</th>
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
                        $garments_items = "";
                        $gmts_item_id_arr = explode(',',$row[csf(gmts_item_id)]);
                        foreach($gmts_item_id_arr as $gmts_item=>$val_id){
                            if($garments_items==""){$garments_items = $garments_item[$val_id];}else {$garments_items .=",".$garments_item[$val_id]; }
                        }  
                        $htmlBody.='
                        <tr bgcolor="'.$bgcolor.'">
                            <td align="center">'.$i.'</td>
                            <td>'.$buyer_library[$row[csf(buyer_name)]].'</td>
                            <td>'.$row[csf(job_no)].'</td>
                            <td>'.$row[csf(style_ref_no)].'</td>
                            <td>'.$garments_items.'</td>
                            <td align="center">'.change_date_format($row[csf(costing_date)]).'</td>
                            <td align="center"></td>
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
                <th align="right"></th>
                <th>&nbsp;</th>
                <th align="right"></th>
            </tfoot>
        </table>'; 
        
        echo  $html=$htmlheader.$htmlBody.$htmlFooter;

        $to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$userid";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

    	$message = $html;
		$header = mail_header();
		
		if($to!="" && $htmlBody!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
 
    } // ready to approve user sequence loop end  
    


/*}

die;
if($test==1)
{*/
    echo "//=========================  <b>ready to approve mail end</b> ==========================================//";
    
    echo "<br>";
    echo "//========================<b> Approved job mail data Start </b> ========================================//<br>";
    
    // apply for approval job list 
    $applyForApprovalJobsSql = sql_select("select job_no,mst_id,approved_by,avg(current_approval_status) as current_approval_status from CO_COM_PRE_COSTING_APPROVAL WHERE approved_by NOT IN ($sequence_wise_user_id_arr[$endSequence])  group by job_no,mst_id,approved_by order by job_no");  
    $applyForApprovalJobs = array();  
    foreach ($applyForApprovalJobsSql as $datrow) {
        if($datrow[csf(current_approval_status)]==1) { 
		   	if($tmp[$datrow[csf('job_no')]]=='') $i=0;
			$applyForApprovalJobs[$datrow[csf('job_no')]][$i] = $datrow[csf('approved_by')];
			$tmp[$datrow[csf('job_no')]]=$datrow[csf('job_no')];     
			$i++;      
        }        
    }  
    /* 
    echo "<pre>";
    print_r($applyForApprovalJobs); 
    */
    
    // Higher Authority Approved job list
    $higherAuthoritySql = sql_select("select job_no,mst_id,approved_by,avg(current_approval_status) as current_approval_status from co_com_pre_costing_approval WHERE approved_by IN ($sequence_wise_user_id_arr[$endSequence])  group by job_no,mst_id,approved_by");
    $finalApproveJobs = array();     
    foreach ($higherAuthoritySql as $row) {          
        if ($row[csf('current_approval_status')]==1) {
            $finalApproveJobs[$row[csf('approved_by')]][$row[csf('job_no')]] = $row[csf('job_no')];
			$final_app_jobs_arr[]= $row[csf('job_no')];
        }
    }

    
    /* echo "<pre>";
    print_r($finalApproveJobs);  */    
    
    $userWiseApprovedJobMailData = array();
	foreach( $finalApproveJobs as $appr=>$jobs)
	{          
		foreach($jobs as $job)
		{
			foreach( $applyForApprovalJobs[$job] as $key=>$apprver)
			{              
                $userWiseApprovedJobMailData[$apprver][$job] = $job;
			}
            
		}
	}
     
    /* echo "<pre>";
    print_r($userWiseApprovedJobMailData);     */
     
    $finalAuthorityJobs = "";
    foreach ($final_app_jobs_arr as $key=>$value) {
        if($finalAuthorityJobs == "") {$finalAuthorityJobs = "'".$value."'";} else {$finalAuthorityJobs .=","."'".$value."'"; }
    }
    //echo $finalAuthorityJobs ."<br>";
    
    $finalAuthorityAppSql = "select a.gmts_item_id,a.job_no,a.buyer_name, a.style_ref_no,b.costing_date,c.approved_by from wo_pre_cost_mst b,wo_po_details_master a,co_com_pre_costing_approval c where a.job_no=b.job_no and b.id= c.mst_id and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and a.job_no IN($finalAuthorityJobs) order by a.job_no";
    $result = sql_select( $finalAuthorityAppSql );
    
    $cost_com_app_data = array();
    foreach ($result as $row) {
        $cost_com_app_data[$row[csf(approved_by)]][$row[csf(job_no)]][buyer_name]   = $row[csf(buyer_name)];
        $cost_com_app_data[$row[csf(approved_by)]][$row[csf(job_no)]][job_no]       = $row[csf(job_no)];     
        $cost_com_app_data[$row[csf(approved_by)]][$row[csf(job_no)]][style_ref_no] = $row[csf(style_ref_no)];     
        $cost_com_app_data[$row[csf(approved_by)]][$row[csf(job_no)]][gmts_item_id] = $row[csf(gmts_item_id)];     
        $cost_com_app_data[$row[csf(approved_by)]][$row[csf(job_no)]][costing_date] = $row[csf(costing_date)];           
    }
 
    /* echo "<pre>";
    print_r($cost_com_app_data);*/
    
    foreach ($userWiseApprovedJobMailData as $approval_user=>$approvedJobArr) {
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
                <td colspan="8" align="center">                    
                    <b style="font-size:14px;">Upto Date : '. date("d-m-Y h:i:s a",time()).'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="8" align="center"><b>Following pre-cost sheet has been approved</b></td>
            </tr>
          
        </table>
        <br>
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="150">Style Ref.</th>
                    <th width="150">Gmts Item</th>
                    <th width="70">Costing Date</th>
                    <th width="100">Un-App. Cost Component</th>
                </tr>
            </thead>
            <tbody>';
    
            $i=1;
            $htmlBody_approved ="";
            foreach ($approvedJobArr as $appJob) {          
                //$user_sequence[$user];
				if($cost_com_app_data[$approval_user][$appJob][job_no] !='') {
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                $garments_items = "";
                $gmts_item_id_arr = explode(',',$cost_com_app_data[$approval_user][$appJob][gmts_item_id]);
                foreach($gmts_item_id_arr as $gmts_item=>$val_id){
                    if($garments_items==""){$garments_items = $garments_item[$val_id];}else {$garments_items .=",".$garments_item[$val_id]; }
                } 
                $htmlBody_approved.='
                <tr bgcolor="'.$bgcolor.'">
                    <td align="center">'.$i.'</td>                  
                    <td>'.$buyer_library[$cost_com_app_data[$approval_user][$appJob][buyer_name]].'</td>
                    <td>'.$cost_com_app_data[$approval_user][$appJob][job_no].'</td>
                    <td>'.$cost_com_app_data[$approval_user][$appJob][style_ref_no].'</td>
                    <td>'.$garments_items.'</td>
                    <td>'.change_date_format($cost_com_app_data[$approval_user][$appJob][costing_date]).'</td>
                    <td align="center"></td>
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
            <th align="right"></th>
            <th>&nbsp;</th>
            <th align="right"></th>
        </tfoot>
        </table>';
        //if($htmlBody_approved!=''){$approve_html_arr[$approval_user]=$htmlheader.$htmlBody_approved.$htmlFooter;}
        
       /* foreach($approve_html_arr as $html){
            echo $html;
        }*/
        
		echo $html = $htmlheader.$htmlBody_approved.$htmlFooter;
        
		$to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$approval_user";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

 		$subject = "Following pre-cost sheet has been approved";
    	$message = $html;
		$header = mail_header();
		
		if($to!="" && $htmlBody_approved!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
		
    }
    echo "//========================<b> Approved job mail data End </b> ========================================//";
    
    echo "<br>";
    echo "//========================<b> Unapproved job mail data Start </b>====================================//<br>";
    //var_dump($final_un_app_jobs_arr);
    
    
    // apply for approval job list 
    $apply_job_sql = sql_select("select a.mst_id,b.job_no,a.approved_by from approval_history a,co_com_pre_costing_approval b WHERE a.mst_id = b.mst_id and a.approved_by NOT IN ($sequence_wise_user_id_arr[$endSequence])  group by a.mst_id,b.job_no,a.approved_by order by job_no");  
    

    $applyForUNapprovalJobs = array();  
    foreach ($apply_job_sql as $datrow) {      
        if($tmp[$datrow[csf('job_no')]]=='') $i=0;
        $applyForUNapprovalJobs[$datrow[csf('job_no')]][$i] = $datrow[csf('approved_by')];
        $tmp[$datrow[csf('job_no')]]=$datrow[csf('job_no')];     
        $i++;                  
    } 
    
    // Higher Authority Unapproved job list
    $higherAuthorityUnAppSql = sql_select("select a.mst_id,b.job_no,a.approved_by from approval_history a,co_com_pre_costing_approval b WHERE a.mst_id = b.mst_id and a.approved_by IN ($sequence_wise_user_id_arr[$endSequence]) and b.current_approval_status in(0)  group by a.mst_id,b.job_no,a.approved_by order by job_no");  
    $finalUnApproveJobs = array();     
    foreach ($higherAuthorityUnAppSql as $row) { 
        $finalUnApproveJobs[$row[csf('approved_by')]][$row[csf('job_no')]] = $row[csf('job_no')];
        $final_un_app_jobs_arr[]= $row[csf('job_no')];       
    }
    
   
    $userWiseUnApprovedJobMailData = array();
    
	foreach( $finalUnApproveJobs as $unAppr=>$unapp_jobs)
	{          
		foreach($unapp_jobs as $unapp_job) {
            
            foreach( $applyForUNapprovalJobs[$unapp_job] as $key=>$applier) {
               
                $userWiseUnApprovedJobMailData[$applier][$unapp_job] = $unapp_job;
                
            }
        
        }       
	}
    
    
    
    $finalAuthorityUnAppJobs = "";
    foreach ($final_un_app_jobs_arr as $key=>$value) {
        if($finalAuthorityUnAppJobs == "") {$finalAuthorityUnAppJobs = "'".$value."'";} else {$finalAuthorityUnAppJobs .=","."'".$value."'"; }
    }
    //echo "143==".$finalAuthorityUnAppJobs."<br>";
    
    $finalAuthorityUnAppSql = "select a.gmts_item_id,a.job_no,a.buyer_name, a.style_ref_no,b.costing_date,c.id,c.approved_by,c.cost_component_id,c.current_approval_status from wo_pre_cost_mst b,wo_po_details_master a,co_com_pre_costing_approval c where a.job_no=b.job_no and b.id= c.mst_id and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $buyer_id_cond2 and a.job_no IN($finalAuthorityUnAppJobs) and c.current_approval_status in(0) order by a.job_no";
    $un_app_result = sql_select( $finalAuthorityUnAppSql );

    $cost_com_un_app_data = array();
    $com_component_status = array();
    foreach ($un_app_result as $un_app_row) {
        //if($un_app_row[csf(current_approval_status)]==0) {
        $cost_com_un_app_data[$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][buyer_name]   = $un_app_row[csf(buyer_name)];
        $cost_com_un_app_data[$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][job_no]       = $un_app_row[csf(job_no)];     
        $cost_com_un_app_data[$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][style_ref_no] = $un_app_row[csf(style_ref_no)];     
        $cost_com_un_app_data[$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][gmts_item_id] = $un_app_row[csf(gmts_item_id)];     
        $cost_com_un_app_data[$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][costing_date] = $un_app_row[csf(costing_date)];           
        $com_component_status[$un_app_row[csf(id)]][$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][$un_app_row[csf(cost_component_id)]] = $un_app_row[csf(current_approval_status)];           
       // }
    }
    
    $userwise_job_compStatus = array();
    foreach($com_component_status as $idArr=>$userArr) {
        foreach ($userArr as $comp_user_id=>$jobArr) {
            foreach($jobArr as $comp_job=>$costComponentIdArr) {
                $comp_jobs[$comp_job]=$comp_job;
                foreach ($costComponentIdArr as $costComponentId=>$costComponentStatus) {
                    if($com_component_status[$comp_user_id][$comp_user_id][$comp_job][$costComponentId]==0) {
                        $userwise_job_compStatus[$comp_user_id][$comp_job][$costComponentId] = $costComponentId."__".$costComponentStatus;
                    }
                }
            }        
        }   
    }
    
    //var_dump($comp_jobs);
    //var_dump($userwise_job_compStatus);

    foreach ($userWiseUnApprovedJobMailData as $applier_user=>$unApprovedJobArr) {

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
                <td colspan="8" align="center"><b>Following Pre-Cost Sheet Has Been Un-approved</b></td>
            </tr>
          
        </table>
        
        <br>
        
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="150">Style Ref.</th>
                    <th width="150">Gmts Item</th>
                    <th width="70">Costing Date</th>
                    <th>Un-App. Cost Component</th>
                </tr>
            </thead>
            <tbody>';
    
            $i=1;
            $htmlBody_un_approved ="";
            foreach ($final_un_app_jobs_arr as $unAppJob) {
                if($cost_com_un_app_data[$applier_user][$unAppJob][job_no]!='') {               
                    //$user_sequence[$user];
                    
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                    $garments_items = "";
                    $gmts_item_id_arr = explode(',',$cost_com_un_app_data[$applier_user][$unAppJob][gmts_item_id]);
                    foreach($gmts_item_id_arr as $gmts_item=>$val_id){
                        if($garments_items==""){$garments_items = $garments_item[$val_id];}else {$garments_items .=",".$garments_item[$val_id]; }
                    } 
                    
                    $compStatus = "";
                    foreach ($userwise_job_compStatus[$applier_user][$unAppJob] as $cosid=>$cost_comp_status) {
                        if($compStatus =="") {$compStatus = $cost_components[$cosid];} else {$compStatus .= ", ".$cost_components[$cosid];}               
                    }
                    
                    $htmlBody_un_approved.='
                    <tr bgcolor="'.$bgcolor.'">
                        <td align="center">'.$i.'</td>                  
                        <td>'.$buyer_library[$cost_com_un_app_data[$applier_user][$unAppJob][buyer_name]].'</td>
                        <td>'.$cost_com_un_app_data[$applier_user][$unAppJob][job_no].'</td>
                        <td>'.$cost_com_un_app_data[$applier_user][$unAppJob][style_ref_no].'</td>
                        <td>'.$garments_items.'</td>
                        <td>'.change_date_format($cost_com_un_app_data[$applier_user][$unAppJob][costing_date]).'</td>
                        <td align="center">'.$compStatus.'</td>
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
            <th align="right"></th>
            <th>&nbsp;</th>
            <th align="right"></th>
        </tfoot>
        </table>';

		echo $html = $htmlheader.$htmlBody_un_approved.$htmlFooter;
        
		$to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$applier_user";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

 		$subject = "Following Pre-cost Sheet Has Been Un-approved";
    	$message = $html;
		$header = mail_header();
		
		if($to!="" && $htmlBody_un_approved!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
        
    }
    echo "//========================<b> Unapproved job mail data End </b>====================================//";
    
    echo "<br>// ======================================= <b>Merchandiser approved mail data Start</b> ============================//<br>";
    // ======================================= approve job mail to Merchandiser =============================================
    
    // apply for approval job list 
    $applyJobsByMarchantiger = sql_select("select b.job_no,b.inserted_by as marchantiger from wo_pre_cost_mst b,wo_po_details_master a where a.job_no=b.job_no and a.company_name=$compid and b.ready_to_approved=1 and b.approved = 1 and b.partial_approved = 1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0");

    $applyByMarchantiger = array();  
    foreach ($applyJobsByMarchantiger as $datrow) {       
        if($tmp[$datrow[csf('job_no')]]=='') $i=0;
        $applyByMarchantiger[$datrow[csf('job_no')]][$i] = $datrow[csf('marchantiger')];
        $tmp[$datrow[csf('job_no')]]=$datrow[csf('job_no')];     
        $i++;                   
    }  
    
   //var_dump($applyByMarchantiger);
    
    // Higher Authority Approved job list
    $higherAuthoritySql = sql_select("select job_no,approved_by,avg(current_approval_status) as current_approval_status from co_com_pre_costing_approval WHERE approved_by IN ($sequence_wise_user_id_arr[$endSequence])  group by job_no,approved_by");
    $higherAuthorityApproveJobs = array();     
    foreach ($higherAuthoritySql as $row) {       
        if ($row[csf('current_approval_status')]==1) {
            $higherAuthorityApproveJobs[$row[csf('approved_by')]][$row[csf('job_no')]] = $row[csf('job_no')];
			$higherAuthority_app_jobs_arr[]= $row[csf('job_no')];
        }
    }	   
    
    $marchantigerWiseApprovedJobMailData = array();
	foreach( $higherAuthorityApproveJobs as $appr=>$jobs)
	{          
		foreach($jobs as $job)
		{
			foreach( $applyByMarchantiger[$job] as $key=>$marchantiger)
			{                
                $marchantigerWiseApprovedJobMailData[$marchantiger][$job] = $job;
			}           
		}
	}
    
    
    $finalAuthorityJobs = "";  
    foreach ($final_app_jobs_arr as $key=>$value) {
        if($finalAuthorityJobs == "") {$finalAuthorityJobs = "'".$value."'";} else {$finalAuthorityJobs .=","."'".$value."'"; }
    }
    // echo "143==".$finalAuthorityJobs."<br>";
    
    $finalAuthorityAppSql = "select a.gmts_item_id,a.job_no,a.buyer_name, a.style_ref_no,b.costing_date,b.inserted_by from wo_pre_cost_mst b,wo_po_details_master a,co_com_pre_costing_approval c where a.job_no=b.job_no and b.id= c.mst_id and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 $buyer_id_cond2 and a.job_no IN($finalAuthorityJobs) order by a.job_no";
    $result = sql_select( $finalAuthorityAppSql );
    
    $marchant_approve_data = array();
    foreach ($result as $row) {
        $marchant_approve_data[$row[csf(inserted_by)]][$row[csf(job_no)]][buyer_name]   = $row[csf(buyer_name)];
        $marchant_approve_data[$row[csf(inserted_by)]][$row[csf(job_no)]][job_no]       = $row[csf(job_no)];     
        $marchant_approve_data[$row[csf(inserted_by)]][$row[csf(job_no)]][style_ref_no] = $row[csf(style_ref_no)];     
        $marchant_approve_data[$row[csf(inserted_by)]][$row[csf(job_no)]][gmts_item_id] = $row[csf(gmts_item_id)];     
        $marchant_approve_data[$row[csf(inserted_by)]][$row[csf(job_no)]][costing_date] = $row[csf(costing_date)];           
    }
 
    foreach ($marchantigerWiseApprovedJobMailData as $marchantiger=>$approvedJobArr) {
    
        $approve_html_arr = array();
        $htmlheader='
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="8" align="center">
                    <strong style="font-size:23px;">'.$company_library[$compid].'</strong> <br>
                    <strong style="font-size:14px;">'.ucfirst($user_arr[$marchantiger]).'</strong>
                </td>
            </tr>
            <tr>
                <td colspan="8" align="center">                    
                    <b style="font-size:14px;">Upto Date : '. date("d-m-Y h:i:s a",time()).'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="8" align="center"><b>Following Pre-Cost Sheet Has Been Approved</b></td>
            </tr>
          
        </table>
       
        <br>
       
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="150">Style Ref.</th>
                    <th width="150">Gmts Item</th>
                    <th width="70">Costing Date</th>
                    <th width="100">Un-App. Cost Component</th>
                </tr>
            </thead>
            <tbody>';
    
            $i=1;
            $htmlBody_approved ="";
            foreach ($approvedJobArr as $appJob) {          
                //$user_sequence[$user];
				if($marchant_approve_data[$marchantiger][$appJob][job_no] !='') {
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                $garments_items = "";
                $gmts_item_id_arr = explode(',',$marchant_approve_data[$marchantiger][$appJob][gmts_item_id]);
                foreach($gmts_item_id_arr as $gmts_item=>$val_id){
                    if($garments_items==""){$garments_items = $garments_item[$val_id];}else {$garments_items .=",".$garments_item[$val_id]; }
                } 
                $htmlBody_approved.='
                <tr bgcolor="'.$bgcolor.'">
                    <td align="center">'.$i.'</td>                  
                    <td>'.$buyer_library[$marchant_approve_data[$marchantiger][$appJob][buyer_name]].'</td>
                    <td>'.$marchant_approve_data[$marchantiger][$appJob][job_no].'</td>
                    <td>'.$marchant_approve_data[$marchantiger][$appJob][style_ref_no].'</td>
                    <td>'.$garments_items.'</td>
                    <td>'.change_date_format($marchant_approve_data[$marchantiger][$appJob][costing_date]).'</td>
                    <td align="center"></td>
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
            <th align="right"></th>
            <th>&nbsp;</th>
            <th align="right"></th>
        </tfoot>
        </table>';
        
        
		echo $html = $htmlheader.$htmlBody_approved.$htmlFooter;
        
		$to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$marchantiger";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

 		$subject = "Following Pre-Cost Sheet Has Been Approved";
    	$message = $html;
		$header = mail_header();
		
		if($to!="" && $htmlBody_approved!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
        
    }
    
    echo "// ======================================= <b>Merchandiser approved mail data End</b> =====================//<br>";
    
    
    echo "//====================<b>Merchandiser Unapproved mail data Start</b> // ===================================//<br>";
    // ======================================= unapproved job mail to Merchandiser =============================================
    
    // apply for approval job list 
    $apply_job_by_marchantiger = sql_select("select a.job_no,a.inserted_by as marchantiger from wo_pre_cost_mst a, co_com_pre_costing_approval b, approval_history c WHERE a.id = b.mst_id and b.mst_id=c.mst_id and c.approved_by NOT IN ($sequence_wise_user_id_arr[$endSequence])  group by a.job_no,a.inserted_by order by job_no");  
    

    $unApproveJobsOfMarchantiger = array();  
    foreach ($apply_job_by_marchantiger as $datrow) {      
        if($tmp[$datrow[csf('job_no')]]=='') $i=0;
        $unApproveJobsOfMarchantiger[$datrow[csf('job_no')]][$i] = $datrow[csf('marchantiger')];
        $tmp[$datrow[csf('job_no')]]=$datrow[csf('job_no')];     
        $i++;                  
    } 
    
    //var_dump($unApproveJobsOfMarchantiger);
    
    // Higher Authority Unapproved job list
    $higherAuthorityUnAppSql = sql_select("select a.mst_id,b.job_no,a.approved_by from approval_history a,co_com_pre_costing_approval b WHERE a.mst_id = b.mst_id and a.approved_by IN ($sequence_wise_user_id_arr[$endSequence]) and b.current_approval_status in(0)  group by a.mst_id,b.job_no,a.approved_by order by job_no");  
    $higherAuthorityUnApproveJobs = array();     
    foreach ($higherAuthorityUnAppSql as $row) { 
        $higherAuthorityUnApproveJobs[$row[csf('approved_by')]][$row[csf('job_no')]] = $row[csf('job_no')];
        $higherAuthority_un_app_jobs_arr[]= $row[csf('job_no')];       
    }
    
   
    $marchantigerWiseUnApprovedJobMailData = array();
    
	foreach( $higherAuthorityUnApproveJobs as $unAppr=>$unapp_jobs)
	{          
		foreach($unapp_jobs as $unapp_job) {
            
            foreach( $unApproveJobsOfMarchantiger[$unapp_job] as $key=>$marchantiger) {              
                $marchantigerWiseUnApprovedJobMailData[$marchantiger][$unapp_job] = $unapp_job;               
            }
        
        }       
	}
    
    // var_dump($marchantigerWiseUnApprovedJobMailData);
    
    
    $finalAuthorityUnAppJobs = "";
    foreach ($final_un_app_jobs_arr as $key=>$value) {
        if($finalAuthorityUnAppJobs == "") {$finalAuthorityUnAppJobs = "'".$value."'";} else {$finalAuthorityUnAppJobs .=","."'".$value."'"; }
    }
    //echo "143==".$finalAuthorityUnAppJobs."<br>";
    
    $finalAuthorityUnAppSql = "select a.gmts_item_id,a.job_no,a.buyer_name, a.style_ref_no,b.costing_date,b.inserted_by,c.id,c.approved_by,c.cost_component_id,c.current_approval_status from wo_pre_cost_mst b,wo_po_details_master a,co_com_pre_costing_approval c where a.job_no=b.job_no and b.id= c.mst_id and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  $buyer_id_cond2 and a.job_no IN($finalAuthorityUnAppJobs) and c.current_approval_status in(0) order by a.job_no";
    $un_app_result = sql_select( $finalAuthorityUnAppSql );

    $marchant_un_app_data = array();
    $com_component_status = array();
    $appoved_aby = array();
    foreach ($un_app_result as $un_app_row) {
        //if($un_app_row[csf(current_approval_status)]==0) {
        $marchant_un_app_data[$un_app_row[csf(inserted_by)]][$un_app_row[csf(job_no)]][buyer_name]   = $un_app_row[csf(buyer_name)];
        $marchant_un_app_data[$un_app_row[csf(inserted_by)]][$un_app_row[csf(job_no)]][job_no]       = $un_app_row[csf(job_no)];     
        $marchant_un_app_data[$un_app_row[csf(inserted_by)]][$un_app_row[csf(job_no)]][style_ref_no] = $un_app_row[csf(style_ref_no)];     
        $marchant_un_app_data[$un_app_row[csf(inserted_by)]][$un_app_row[csf(job_no)]][gmts_item_id] = $un_app_row[csf(gmts_item_id)];     
        $marchant_un_app_data[$un_app_row[csf(inserted_by)]][$un_app_row[csf(job_no)]][costing_date] = $un_app_row[csf(costing_date)];           
        $com_component_status[$un_app_row[csf(id)]][$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]][$un_app_row[csf(cost_component_id)]] = $un_app_row[csf(current_approval_status)];           
        $appoved_byArr[$un_app_row[csf(approved_by)]][$un_app_row[csf(job_no)]] = $un_app_row[csf(job_no)];
       // }
    }
    
    //var_dump($marchant_un_app_data);
    
    $userwise_job_compStatus = array();
    foreach($com_component_status as $idArr=>$userArr) {
        foreach ($userArr as $comp_user_id=>$jobArr) {
            foreach($jobArr as $comp_job=>$costComponentIdArr) {
                $comp_jobs[$comp_job]=$comp_job;
                foreach ($costComponentIdArr as $costComponentId=>$costComponentStatus) {
                    if($com_component_status[$comp_user_id][$comp_user_id][$comp_job][$costComponentId]==0) {
                        $userwise_job_compStatus[$comp_user_id][$comp_job][$costComponentId] = $costComponentId."__".$costComponentStatus;
                    }
                }
            }        
        }   
    }
    
    //var_dump($comp_jobs);
    //var_dump($userwise_job_compStatus);

    foreach ($marchantigerWiseUnApprovedJobMailData as $marchantiger=>$unApprovedJobArr) {

        $unApprove_html_arr = array();
        $htmlheader='
        <table cellspacing="0" border="0" width="670">
            <tr>
                <td colspan="8" align="center">
                    <strong style="font-size:23px;">'.$company_library[$compid].'</strong> <br>
                    <strong style="font-size:14px;">'.ucfirst($user_arr[$marchantiger]).'</strong>
                </td>
            </tr>
            <tr>
                <td colspan="8" align="center">                    
                    <b style="font-size:14px;">Upto Date : '. date("d-m-Y h:i:s a",time()).'</b>
                </td>
            </tr>
            
            <tr>
                <td colspan="8" align="center"><b>Following Pre-Cost Sheet Has Been Un-approved</b></td>
            </tr>
          
        </table>
        
        <br>
        
        <table border="1" rules="all" class="rpt_table" id="table_body3" style="font-size:13px;">
            <thead style="background-color:#CCC">
                <tr align="center">
                    <th width="35">SL</th>
                    <th width="80">Buyer</th>
                    <th width="80">Job No</th>
                    <th width="150">Style Ref.</th>
                    <th width="150">Gmts Item</th>
                    <th width="70">Costing Date</th>
                    <th>Un-App. Cost Component</th>
                </tr>
            </thead>
            <tbody>';
    
            $i=1;
            $htmlBody_un_approved ="";
            foreach ($higherAuthority_un_app_jobs_arr as $unAppJob) {
                if($marchant_un_app_data[$marchantiger][$unAppJob][job_no]!='') {               
                    //$user_sequence[$user];
                    
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                    $garments_items = "";
                    $gmts_item_id_arr = explode(',',$marchant_un_app_data[$marchantiger][$unAppJob][gmts_item_id]);
                    foreach($gmts_item_id_arr as $gmts_item=>$val_id){
                        if($garments_items==""){$garments_items = $garments_item[$val_id];}else {$garments_items .=",".$garments_item[$val_id]; }
                    } 

                    
                    foreach ($appoved_byArr as $approvedBy=>$jobArr) {
                        foreach($jobArr as $app_job) {
                            $compStatus = "";
                            foreach ($userwise_job_compStatus[$approvedBy][$unAppJob] as $cosid=>$cost_comp_status) {                              
                                if($compStatus =="") {$compStatus = $cost_components[$cosid];} else {$compStatus .= ", ".$cost_components[$cosid];}                                              
                            }               
                        }
                    }
                    
                    
                    $htmlBody_un_approved.='
                    <tr bgcolor="'.$bgcolor.'">
                        <td align="center">'.$i.'</td>                  
                        <td>'.$buyer_library[$marchant_un_app_data[$marchantiger][$unAppJob][buyer_name]].'</td>
                        <td>'.$marchant_un_app_data[$marchantiger][$unAppJob][job_no].'</td>
                        <td>'.$marchant_un_app_data[$marchantiger][$unAppJob][style_ref_no].'</td>
                        <td>'.$garments_items.'</td>
                        <td>'.change_date_format($marchant_un_app_data[$marchantiger][$unAppJob][costing_date]).'</td>
                        <td align="center">'.$compStatus.'</td>
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
            <th align="right"></th>
            <th>&nbsp;</th>
            <th align="right"></th>
        </tfoot>
        </table>';

		echo $html = $htmlheader.$htmlBody_un_approved.$htmlFooter;
        
		$to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$marchantiger";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

 		$subject = "Following Pre-Cost Sheet Has Been Un-approved";
    	$message = $html;
		$header = mail_header();
		
		if($to!="" && $htmlBody_un_approved!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
		
		//if($to!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
        
    }   // end marchantiger unapproved mail data 
       
    echo "//====================<b>Merchandiser Unapproved mail data End</b> // ==============================//<br>";
    
} // company loop end 













?> 