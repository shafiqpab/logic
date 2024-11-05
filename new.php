<?php
date_default_timezone_set("Asia/Dhaka");

require_once('includes/common.php');
require_once('mailer/class.phpmailer.php');

$company_library = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );
$buyer_library = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
$user_arr = return_library_array("select id,user_full_name from user_passwd where valid=1","id","user_full_name");
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


$company_library = array(3=>$company_library[3]);
//echo count($company_library);

foreach($company_library as $compid=>$compname)
{
	
	$electronic_data=array();
	$electronic_user_data=array();
	$sql = "select user_id,buyer_id,bypass,sequence_no FROM electronic_approval_setup where company_id = $compid and page_id=869 and is_deleted=0 order by sequence_no,user_id asc";
    $elecData_array=sql_select($sql);
	$i=0;
	foreach($elecData_array as $erow){
		 $sequence_wise_user_id_arr[$erow[csf(sequence_no)]] = $erow[csf(user_id)];
		 $sequence_wise_buyer_id_arr[$erow[csf(sequence_no)]] = $erow[csf(buyer_id)];
         $user_wise_byuer_id_arr[$erow[csf(user_id)]] = $erow[csf(buyer_id)];
         
         $use_seque[$erow[csf(user_id)]] = $erow[csf(sequence_no)];
         
         $startSequence = min($use_seque);
         $endSequence = max($use_seque);
		 $i++;
	}

    echo "// =========================  <b>ready to approve mail start<b> ==========================================//<br> ";
  //print_r($sequence_wise_user_id_arr); die;
   
    foreach($sequence_wise_user_id_arr as $userSequence=>$userid)
    {  
        $approval_type = 2;				
        
        $user_sequence_no = $userSequence;
        $min_sequence_no = $startSequence;

        if($approval_type==2)
        {           
            $sql ="select b.id,a.gmts_item_id,a.job_no,a.buyer_name, a.style_ref_no,b.costing_date from wo_pre_cost_mst b,  wo_po_details_master a where a.job_no=b.job_no and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.partial_approved=2";
            $mainData = sql_select($sql);
           
            /* foreach($mainData as $row)
            {
                $ready_to_approved_data[$row[csf(buyer_name)]][]=array(
                    buyer_name=>$row[csf(buyer_name)],
                    job_no=>$row[csf(job_no)],
                    gmts_item_id=>$row[csf(gmts_item_id)],
                    style_ref_no=>$row[csf(style_ref_no)],
                    costing_date=>$row[csf(costing_date)],
                );
            } */

        }
        
        //var_dump($ready_to_approved_data);
        
        
        die;
      
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
                <td colspan="8" align="center"><b>Following pre-cost sheet is ready to approved</b></td>
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
        if($htmlBody!=''){ $html_arr[$userid] = $htmlheader.$htmlBody.$htmlFooter; }
        
        foreach($html_arr as $html){
            echo $html;
        }
        
        
        $to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$userid";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

 		$subject = "FOLLOWING PRE-COST SHEET IS READY TO APPROVE";
    	$message = $html;
		$header = mail_header();
		
		//if($to!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );

       
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
        if($htmlBody_approved!=''){$approve_html_arr[$approval_user]=$htmlheader.$htmlBody_approved.$htmlFooter;}
        
        foreach($approve_html_arr as $html){
            echo $html;
        }
        
        
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
		
		//if($to!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
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
                <td colspan="8" align="center"><b>Following pre-cost sheet has been unapproved</b></td>
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
        if($htmlBody_un_approved!=''){$unApprove_html_arr[$applier_user]=$htmlheader.$htmlBody_un_approved.$htmlFooter;}
        
        foreach($unApprove_html_arr as $html){
            echo $html;
        }
        
        $to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$applier_user";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

 		$subject = "Following pre-cost sheet has been unapproved";
    	$message = $html;
		$header = mail_header();
		
		//if($to!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
        
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
        if($htmlBody_approved!=''){$approve_html_arr[$marchantiger]=$htmlheader.$htmlBody_approved.$htmlFooter;}
        
        foreach($approve_html_arr as $html){
            echo $html;
        }
        
        
        $to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$marchantiger";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

 		$subject = "Following pre-cost sheet has been approved";
    	$message = $html;
		$header = mail_header();
		
		//if($to!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
        
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
                <td colspan="8" align="center"><b>Following pre-cost sheet has been unapproved</b></td>
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
        if($htmlBody_un_approved!=''){$unApprove_html_arr[$marchantiger]=$htmlheader.$htmlBody_un_approved.$htmlFooter;}
        
        foreach($unApprove_html_arr as $html){
            echo $html;
        }
        
        $to="";
		
		$sql = "SELECT id,user_email FROM user_passwd WHERE id=$marchantiger";
		
		$mail_sql = sql_select($sql);
        
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('user_email')]; else $to=$to.", ".$row[csf('user_email')]; 
		}

 		$subject = "Following pre-cost sheet has been unapproved";
    	$message = $html;
		$header = mail_header();
		
		//if($to!="") echo send_mail_mailer( $to, $subject, $message, $from_mail );
        
    }   // end marchantiger unapproved mail data 
       
    echo "//====================<b>Merchandiser Unapproved mail data End</b> // ==============================//<br>";
    
} // company loop end 
?> 