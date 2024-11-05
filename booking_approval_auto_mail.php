<?php
date_default_timezone_set("Asia/Dhaka");

require_once('includes/common.php');
require_once('mailer/class.phpmailer.php');

$company_library = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );
$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$user_arr = return_library_array("select id,user_name from user_passwd where valid=1","id","user_name");
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


$company_library = array(1=>$company_library[1]);

foreach($company_library as $comp_id=>$compname)
{
	$menu_id = 410;
	$approval_type=0;
	
	
	$sequence_wise_user_id_arr=array();
	$sql = "select user_id,buyer_id,bypass,sequence_no FROM electronic_approval_setup where company_id = $comp_id and page_id=$menu_id and is_deleted=0 order by sequence_no,user_id asc";
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
	
	

	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$comp_id and page_id=$menu_id and is_deleted=0","seq");
	
	$buyer_ids_array=array();
	$buyerData=sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$comp_id and page_id=$menu_id and is_deleted=0 and bypass=2");
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}
	
	$sequence_no='';
	$booking_year=date("Y",time());
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')='".trim($booking_year)."' ";
	
/*	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.booking_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.booking_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.booking_date=$txt_date";
		else $date_cond='';
	}*/



    foreach($sequence_wise_user_id_arr as $userSequence=>$user_id)
    {
	//echo $user_arr[$user_id];			
				
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$comp_id and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Fabric Booking.</font>";
		die;
	}
	
	
	if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date) as year"; 
		$orderBy_cond="IFNULL";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		$orderBy_cond="NVL";
	}
	else 
	{
		$year_field="";//defined Later
		$orderBy_cond="ISNULL";
	}

	if($approval_type==0)//0=unapproved
	{
		if($db_type==0)
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$comp_id and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
		}
		else
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$comp_id and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
		}
		
		if($user_sequence_no==$min_sequence_no)
		{	
			$buyer_ids = $buyer_ids_array[$userid]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
			
			$sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no  and a.company_id=$comp_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $booking_no_cond $date_cond $booking_year_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num order by $orderBy_cond(a.update_date, a.insert_date) desc";
             //echo $sql;
		}
		else if($sequence_no=="")
		{  
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
			
			if($db_type==0)
			{
				
				$seqSql="select group_concat(sequence_no) as sequence_no_by,
 group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$comp_id and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
				$seqData=sql_select($seqSql);
				
				$sequence_no_by=$seqData[0][csf('sequence_no_by')];
				$buyerIds=$seqData[0][csf('buyer_ids')];
				
				if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.buyer_id not in($buyerIds)";
				
				$booking_id=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$comp_id and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond","booking_id");
				
				$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$comp_id and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=7 and b.current_approval_status=1","booking_id");
			}
			else
			{
				
				$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$comp_id and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
				$seqData=sql_select($seqSql);
				
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
				$booking_id_sql="select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$comp_id and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond $seqCond
				union
				select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$comp_id and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond";
				$bResult=sql_select($booking_id_sql);
				foreach($bResult as $bRow)
				{
					$booking_id.=$bRow[csf('booking_id')].",";
				}
				
				$booking_id=chop($booking_id,',');
				
				$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$comp_id and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=7 and b.current_approval_status=1","booking_id");
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
				}
				else
				{
					$booking_id_cond=" and a.id in($booking_id)";	 
				}
			}
			else $booking_id_cond="";
			
			if($db_type==0)
			{
				if($booking_id!="")
				{
					$sql="select a.update_date as ob_update, a.insert_date as ob_insertdate, a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id,  a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$comp_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond  $date_cond $buyerIds_cond $buyer_id_cond2 $booking_no_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num
						union all
						select a.update_date as ob_update, a.insert_date as ob_insertdate, a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$comp_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=1 and b.fin_fab_qnty>0 and a.id in($booking_id) $buyer_id_cond $buyer_id_cond2 $date_cond $booking_no_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num order by $orderBy_cond(ob_update, ob_insertdate) desc";
				}
				else
				{
					$sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$comp_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $date_cond $buyerIds_cond $booking_no_cond $booking_year_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num order by $orderBy_cond(a.update_date, a.insert_date) desc";
				}
				 //echo $sql;
			}
			else
			{
				if($booking_id!="")
				{   // and a.id in($booking_id)
					$sql="select * from(select a.update_date, a.insert_date, a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$comp_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond  $date_cond $buyerIds_cond $buyer_id_cond2 $booking_no_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num
						union all
						select a.update_date, a.insert_date, a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.company_id=$comp_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=1 and b.fin_fab_qnty>0 $booking_id_cond $buyer_id_cond $buyer_id_cond2 $date_cond $booking_no_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num) order by $orderBy_cond(update_date, insert_date) desc";  
                }
				else
				{
					$sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$comp_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2   $date_cond $buyerIds_cond $booking_no_cond $booking_year_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num order by $orderBy_cond(a.update_date, a.insert_date) desc";
                     // echo $sql;
                }
				
				
			}
		}
		else
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
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
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$comp_id and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$comp_id and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");	
				}
				
				if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
				else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
				
				$sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update from wo_booking_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=7 and a.company_id=$comp_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved=1 $buyer_id_cond $buyer_id_cond2 $sequence_no_cond   $date_cond $booking_no_cond $booking_year_cond order by $orderBy_cond(a.update_date, a.insert_date) desc";
			}
		}
	}
	else
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
		//$sequence_no_cond=" and b.sequence_no='$user_sequence_no'";
		$sequence_no_cond=" and b.approved_by='$user_id'";
		$sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update from wo_booking_mst a, approval_history b, wo_po_break_down c where a.id=b.mst_id and a.job_no=c.job_no_mst and b.entry_form=7 and a.company_id=$comp_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved=1 $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $internal_ref_cond $file_no_cond $date_cond $booking_no_cond $booking_year_cond order by $orderBy_cond(a.update_date, a.insert_date) desc";
	
    }
	

	
	$sql_job=sql_select("select a.pay_mode,a.booking_no,b.po_break_down_id ,b.job_no,c.grouping, c.file_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$comp_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and b.fin_fab_qnty>0  $booking_no_cond $date_cond $booking_year_cond group by a.pay_mode,a.booking_no,b.po_break_down_id ,b.job_no,c.grouping, c.file_no");
	
	$job_information_arr=array();
	foreach( $sql_job as $jval)
	{
		$job_information_arr[$jval[csf('booking_no')]]['jobno'][]=$jval[csf('job_no')];
		$job_information_arr[$jval[csf('booking_no')]]['po_break_down_id'][]=$jval[csf('po_break_down_id')];
		$job_information_arr[$jval[csf('booking_no')]]['grouping'][]=$jval[csf('grouping')];
		$job_information_arr[$jval[csf('booking_no')]]['file_no'][]=$jval[csf('file_no')];
		$job_information_arr[$jval[csf('booking_no')]]['file_no'][]=$jval[csf('file_no')];
		$job_paymode_arr[$jval[csf('booking_no')]]=$jval[csf('pay_mode')];
	}
	
	ob_start();
	//print_r($format_ids);
	?>
        <legend>
            <b>Subject :</b> Fabric booking Un-Approval Notification.<br />
           <strong> Dear Concern,</strong><br />
            Following booking has been Un-Approved<br />
        </legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" >
                <thead style="background:#CCC">
                    <th width="40">SL</th>
                    <th width="130">Booking No</th>
                    <th width="100">Fab. Source</th>
                    <th width="60">Year</th>
                    <th width="80">Type</th>
                    <th width="100">Booking Date</th>
                    <th width="125">Buyer</th>
                    <th width="160">Supplier</th>
                    <th width="120">Job No</th>
                    <th>Delivery Date</th>
                </thead>
                <tbody>
                    <?
                        $i=1; $all_approval_id='';
                        $nameArray=sql_select( $sql );
                        foreach ($nameArray as $row)
                        {
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
                            
                            $value=$row[csf('id')];
                            
                            if($row[csf('booking_type')]==4) 
                            {
                                $booking_type="Sample";
                                $type=3;
                            }
                            else
                            {
                                if($row[csf('is_short')]==1) {
                                    $booking_type="Short";
                                } 
                                elseif($row[csf('is_short')]==3) {
                                    $booking_type="Dia Wise";
                                } 
                                else {
                                    $booking_type="Main";
                                } 
                                $type=$row[csf('is_short')];
                            }
                            
//======================================= for job file internal reff=================================
                            $dealing_merchant='';
                            $dealing_merchant_arr=array();
                            $job_no_arr=array();
                            $all_job_no='';
                            foreach( $job_information_arr[$row[csf('booking_no')]]['jobno'] as $key=>$job_data )
                            {
                                $job_no_arr[]=$job_data;
                                $dealing_merchant_arr[]=$dealing_merchant_array[$job_dealing_merchant_array[$job_data]];
                            }
                            
                            $job_no_arr=array_unique($job_no_arr);
                            $all_job_no=implode(",",$job_no_arr);
                            $dealing_merchant_arr=array_unique($dealing_merchant_arr);
                            $dealing_merchant=implode(",",$dealing_merchant_arr);
                            
// file no information......................................................................
                            /*$file_no_arr=array();
                            $all_file_no='';
                            foreach( $job_information_arr[$row[csf('booking_no')]]['file_no'] as $key=>$file_data )
                            {
                                $file_no_arr[]=$file_data;
                            }
                            $file_no_arr=array_unique($file_no_arr);
                            $all_file_no=implode(",",$file_no_arr);*/
// internal reference information.........................................................................
                            $all_internal_ref='';
                            $internal_ref_arr=array();
                            foreach( $job_information_arr[$row[csf('booking_no')]]['grouping'] as $key=>$internalref_data )
                            {
                                $internal_ref_arr[]=$internalref_data;
                            }
                            $internal_ref_arr=array_unique($internal_ref_arr);
                            $all_internal_ref=implode(",",$internal_ref_arr);
                            
// order no information......................................................................................
                            $all_po_id='';
                            $po_id_arr=array();
                            foreach( $job_information_arr[$row[csf('booking_no')]]['po_break_down_id'] as $key=>$po_data )
                            {
                                $po_id_arr[]=$po_data;
                            }
                            $po_id_arr=array_unique($po_id_arr);
                            $all_po_id=implode(",",$po_id_arr);
                            
                            if($row[csf('approval_id')]==0)
                            {
                                $print_cond=1;
                            }
                            else
                            {
                                if($duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=="")
                                {
                                    $duplicate_array[$row[csf('entry_form')]][$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=$row[csf('id')];
                                    $print_cond=1;
                                }
                                else
                                {
                                    if($all_approval_id=="") $all_approval_id=$row[csf('approval_id')]; else $all_approval_id.=",".$row[csf('approval_id')];
                                    $print_cond=0;
                                }
                            }
                            if($print_cond==1)
                            {	
                                
                            
                            
                            ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>"> 
                                    <td><? echo $i; ?></td>
                                    <td><p><? echo $row[csf('booking_no')]?></p></td>
                                    <td><p><? echo $fabric_source[$row[csf('fabric_source')]];?></p></td>
                                    <td align="center"><? echo $row[csf('year')]; ?></td>
                                    <td align="center"><p><? echo $booking_type; ?></p></td>
                                    <td align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?></td>
                                    <td><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
                                    <td><p>
                                    <? 
                                        if($job_paymode_arr[$row[csf('booking_no')]]==3 || $job_paymode_arr[$row[csf('booking_no')]]==5)
                                        {
                                            echo $company_library[$row[csf('supplier_id')]];
                                        }
                                        else
                                        {
                                            echo $supplier_arr[$row[csf('supplier_id')]]; 
                                        }
                                    ?>

                                    </p></td>
                                    <td align="center"><p><? echo $all_job_no; ?></p></td>
                                    <td align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?></td>                                
                                </tr>
                                <?
                                $i++;
                            }
                            
                        }
                    ?>
                </tbody>
           </table>

<?

 		$subject = "Fabric booking Un-Approval Notification.";
    	$message=ob_get_contents();
		$header = mail_header();
		ob_clean();
		$to=$user_mail_arr[$user_id];
		if($to!="" && $print_cond==1) {
           $con = connect();
            //echo $mailSend = send_mail_mailer( $to, $subject, $message, $from_mail );
        };
        
	
	echo $message;
	


}//user loop	
} // company loop end 
?> 