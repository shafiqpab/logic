<?
session_start();
if($_SESSION['logic_erp']['user_notification']!=''){ echo $_SESSION['logic_erp']['user_notification'];exit();}

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;


if($action=='app_notification'){
	
	$user_id= $_SESSION['logic_erp']['user_id'];
	$company_arr=return_library_array( "select comp.id, comp.COMPANY_NAME from LIB_COMPANY comp where STATUS_ACTIVE=1 $company_cond", "id", "COMPANY_NAME"  );
	
		
	//user_sequence_no.........................
	$user_sequence_no_sql="select SEQUENCE_NO,PAGE_ID,COMPANY_ID from electronic_approval_setup where user_id=$user_id and is_deleted=0";
	$user_sequence_no_sql_data_arr = sql_select($user_sequence_no_sql);
	$user_sequence_no_array=array();
	$user_selected_page_array=array();
	foreach($user_sequence_no_sql_data_arr as $row)
	{
		$user_sequence_no_array[$row[PAGE_ID]][$row[COMPANY_ID]]=$row[SEQUENCE_NO];
		$user_selected_page_array[$row[PAGE_ID]]=$row[PAGE_ID];
	}

	//min_sequence_no.........................
	$min_sequence_no_sql="select min(sequence_no) as SEQUENCE_NO,PAGE_ID,COMPANY_ID from electronic_approval_setup where is_deleted=0 group by PAGE_ID,COMPANY_ID";
	$min_sequence_no_sql_data_arr = sql_select($min_sequence_no_sql);
	$min_sequence_no_array=array();
	foreach($min_sequence_no_sql_data_arr as $row)
	{
		$min_sequence_no_array[$row[PAGE_ID]][$row[COMPANY_ID]]=$row[SEQUENCE_NO];
	}

	//min_sequence_no.........................
	$max_sequence_no_sql="select max(sequence_no) as SEQUENCE_NO,PAGE_ID,COMPANY_ID from electronic_approval_setup where is_deleted=0 group by PAGE_ID,COMPANY_ID";
	$max_sequence_no_sql_data_arr = sql_select($max_sequence_no_sql);
	$max_sequence_no_array=array();
	foreach($max_sequence_no_sql_data_arr as $row)
	{
		$max_sequence_no_array[$row[PAGE_ID]][$row[COMPANY_ID]]=$row[SEQUENCE_NO];
	}

	//user and seq wise buyer id.........................
	$user_buyer_ids_array = array();
	$buyerData = sql_select("select USER_ID, SEQUENCE_NO, BUYER_ID,PAGE_ID,COMPANY_ID from electronic_approval_setup where is_deleted=0 and bypass=2");
	foreach($buyerData as $row)
	{
		$user_buyer_ids_array[$row[PAGE_ID]][$row[COMPANY_ID]][$row[USER_ID]]['u']=$row[BUYER_ID];
		$user_buyer_ids_array[$row[PAGE_ID]][$row[COMPANY_ID]][$row[SEQUENCE_NO]]['s']=$row[BUYER_ID];
	}
			
			
	$retunr_val_arr=array();		
	$page_id=428;
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
		
			$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
			$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
			$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
			$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
					
		 if($user_sequence_no !="" )
		  {
				
			if($db_type==0){$year_cond="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";}
			else{$year_cond="to_char(a.insert_date,'YYYY') as year";}
		
		
	   
		   if($select_no){$job_no_cond=" and a.job_no='$select_no'";}
		   
		   $type=0;
		   if($type==0)
		   {
				if($db_type==0)
				{
					$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
				}
				else
				{
					$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
				}
				
				
		
				if($user_sequence_no==$min_sequence_no)
				{
					
					
					$buyer_ids = $buyer_ids_array[$user_id]['u'];
					if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
		
		
					$sql="select b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,'0' as approval_id, b.approved,b.inserted_by,b.entry_from from wo_pre_cost_mst b,  wo_po_details_master a ,wo_po_break_down d  where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved=2 $buyer_id_cond $buyer_id_cond2 $date_cond $job_no_cond $job_year_cond $internal_ref_cond $file_no_cond group by b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,'0', b.approved,b.inserted_by,b.entry_from";
					 //echo $sql;die;
				}
				else if($sequence_no == "")
				{
					$buyer_ids=$buyer_ids_array[$user_id]['u'];
					if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
		
					if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_name in($buyer_ids)";
		
						$seqSql="select SEQUENCE_NO, BYPASS, BUYER_ID from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
						$seqData=sql_select($seqSql);
		
		
		
						$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
						foreach($seqData as $sRow)
						{
							if($sRow[BYPASS]==2)
							{
								$sequence_no_by_no.=$sRow[SEQUENCE_NO].",";
								if($sRow[BUYER_ID]!="")
								{
									$buyerIds.=$sRow[BUYER_ID].",";
									$buyer_id_arr=explode(",",$sRow[BUYER_ID]);
									$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
									if(count($result)>0)
									{
										$query_string.=" (b.sequence_no=".$sRow[SEQUENCE_NO]." and c.buyer_name in(".implode(",",$result).")) or ";
									}
									$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
								}
							}
							else
							{
								$sequence_no_by_yes.=$sRow[SEQUENCE_NO].",";
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
							$buyerIds_cond=" and a.buyer_name not in($buyerIds)";
							$seqCond=" and (".chop($query_string,'or ').")";
						}
						$sequence_no_by_no=chop($sequence_no_by_no,',');
						$sequence_no_by_yes=chop($sequence_no_by_yes,',');
		
						if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
						if($sequence_no_by_no=="") $sequence_no_by_no=0;
		
						$pre_cost_id='';
						$pre_cost_id_sql="select distinct (mst_id) as PRE_COST_ID from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_id and b.sequence_no in ($sequence_no_by_no) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3 $seqCond
						union
						select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_id and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3";
						$bResult=sql_select($pre_cost_id_sql);
						foreach($bResult as $bRow)
						{
							$pre_cost_id.=$bRow[PRE_COST_ID].",";
						}
		
						$pre_cost_id=chop($pre_cost_id,',');
		
						$pre_cost_id_app_sql=sql_select("select b.mst_id as PRE_COST_ID from wo_pre_cost_mst a, approval_history b, wo_po_details_master c
						where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_id and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1");
		
						foreach($pre_cost_id_app_sql as $inf)
						{
							if($pre_cost_id_app_byuser!="") $pre_cost_id_app_byuser.=",".$inf[PRE_COST_ID];
							else $pre_cost_id_app_byuser.=$inf[PRE_COST_ID];
						}
		
						$pre_cost_id_app_byuser=implode(",",array_unique(explode(",",$pre_cost_id_app_byuser)));
					
		
					$pre_cost_id_app_byuser=chop($pre_cost_id_app_byuser,',');
					$result=array_diff(explode(',',$pre_cost_id),explode(',',$pre_cost_id_app_byuser));
					$pre_cost_id=implode(",",$result);
					//echo $pre_cost_id;die;
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
		
					$sql="select b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,0 as approval_id,
					b.approved,b.inserted_by ,b.entry_from
					from wo_pre_cost_mst b,wo_po_details_master a,wo_po_break_down d
					where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1
					and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in (0,2) $buyer_id_cond $date_cond $pre_cost_id_cond $buyerIds_cond $buyer_id_cond2 $job_no_cond $file_no_cond $internal_ref_cond group by b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,
					b.approved,b.inserted_by,b.entry_from ";
					//echo $sql;die;
					if($pre_cost_id!="")
					{
						$pre_cost_id_cond2="and ";
						$pre_cost_id_arr=explode(",",$pre_cost_id);
						if(count($pre_cost_id_arr)>995)
						{
							$pre_cost_id_cond2.=" ( ";
							$pre_cost_id_arr_chunk_arr=array_chunk(explode(",",$pre_cost_id),995) ;
							$slcunk=0;
							foreach($pre_cost_id_arr_chunk_arr as $chunk_arr)
							{
								if($slcunk>0) $pre_cost_id_cond2.=" or";
								$chunk_arr_value=implode(",",$chunk_arr);	
								$pre_cost_id_cond2.="  b.id  in($chunk_arr_value)";
								$slcunk++;	
							}
							$pre_cost_id_cond2.=" )";
						}
						else
						{
							$pre_cost_id_cond2.="  b.id  in($pre_cost_id)";	 
						}
						
						$sql.=" union all
						select b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,0 as approval_id,
						b.approved,b.inserted_by,b.entry_from
						from wo_pre_cost_mst b,wo_po_details_master a,wo_po_break_down d
						where  a.job_no=b.job_no and a.job_no=d.job_no_mst and a.company_name=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1
						and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in(1,3) $pre_cost_id_cond2 $buyer_id_cond $buyer_id_cond2  $date_cond $job_no_cond $file_no_cond $internal_ref_cond group by b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,
						b.approved,b.inserted_by,b.entry_from";
		
					}
				}
				else
				{
					$buyer_ids=$buyer_ids_array[$user_id]['u'];
					if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
		
					$user_sequence_no=$user_sequence_no-1;
					if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
					else
					{
						if($db_type==0)
						{
							$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and
							 sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
						}
						else
						{
							$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no)
							 as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and
						}
					}
		
					if($sequence_no_by_pass==""){$sequence_no_cond=" and c.sequence_no='$sequence_no'";}
					else{$sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";}
		
					  $sql="select b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date, b.approved,b.inserted_by,
						  c.id as approval_id, c.sequence_no, c.approved_by,c.id as approval_id ,b.entry_from
						  from wo_pre_cost_mst b,  wo_po_details_master a,approval_history c,wo_po_break_down d
						  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_id and a.job_no=d.job_no_mst  and a.is_deleted=0 and
						  a.status_active=1 and b.status_active=1 and c.current_approval_status=1  and
						  b.is_deleted=0 and b.approved in(1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $job_no_cond $file_no_cond $internal_ref_cond group by  b.id,a.quotation_id,a.job_no_prefix_num,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date, b.approved,b.inserted_by,c.id, c.sequence_no, c.approved_by,b.entry_from "; //and b.ready_to_approved=1
				}
				//return $sql;
			
			}
		
			$nameArray=sql_select( $sql );
			$totalRows=count($nameArray);
			
			if($totalRows){
				$pageTotalRows+=$totalRows;
			}
			
		  }//user sequence empty check;
				
		}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	
		
	$page_id=427; //Price Quotation Approval
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){$year_cond="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";}
					else{$year_cond="to_char(a.insert_date,'YYYY') as year";}
	
				
					if($select_no){$quotation_cond=" and a.id=$select_no";}
		
					$approval_type=0;
					if($approval_type==0)
					{
				
						if($db_type==0)
						{
							$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
						}
						else
						{
							$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
						}
				
						if($user_sequence_no==$min_sequence_no)
						{
				
							$buyer_ids = $buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.BUYER_ID in($buyer_ids)";
				 
							$sql="SELECT a.ID,  a.company_id,  a.BUYER_ID, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, '0' as approval_id,  a.approved, a.inserted_by, a.garments_nature, a.mkt_no  from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type $buyer_id_cond $buyer_id_cond2 $date_cond $quotation_cond $mkt_no_cond group by a.ID,  a.company_id,  a.BUYER_ID , a.garments_nature, a.style_ref, a.STYLE_REF, a.QUOT_DATE, a.est_ship_date, a.approved, a.inserted_by, a.mkt_no order by a.id ASC";
						}
						else if($sequence_no=="")
						{
				
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.BUYER_ID in($buyer_ids)";
				
							$seqSql="select SEQUENCE_NO, BYPASS, BUYER_ID from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
							$seqData=sql_select($seqSql);
				
							$buyerIds=''; $sequence_no_by_yes=''; $sequence_no_by_no=''; $approved_string=''; $check_buyerIds_arr=array();
							foreach($seqData as $sRow)
							{
								if($sRow->BYPASS==2)
								{
									$sequence_no_by_no.=$sRow[SEQUENCE_NO].",";
									if($sRow[BUYER_ID]!="")
									{
										$buyerIds.=$sRow[BUYER_ID].",";
				
										$buyer_id_arr=explode(",",$sRow[BUYER_ID]);
										$result=array_diff($buyer_id_arr,$check_buyerIds_arr);
										if(count($result)>0)
										{
											$query_string.=" (b.sequence_no=".$sRow[SEQUENCE_NO]." and a.BUYER_ID in(".implode(",",$result).")) or ";
										}
										$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
									}
								}
								else
								{
									$sequence_no_by_yes.=$sRow[SEQUENCE_NO].",";
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
								$buyerIds_cond=" and a.BUYER_ID not in($buyerIds)";
								$seqCond=" and (".chop($query_string,'or ').")";
							}
							//echo $seqCond;die;
							$sequence_no_by_no=chop($sequence_no_by_no,',');
							$sequence_no_by_yes=chop($sequence_no_by_yes,',');
				
							if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
							if($sequence_no_by_no=="") $sequence_no_by_no=0;
							if($select_no){$quotation_cond=" and mst_id=$select_no";}
							 
							$quotation_id_sql="select distinct (mst_id) as QUOTATION_ID from wo_price_quotation a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and b.sequence_no in ($sequence_no_by_no) and b.entry_form=10 and b.current_approval_status=1 $quotation_cond $buyer_id_cond $date_cond $seqCond
							union
							select distinct (mst_id) as quotation_id from wo_price_quotation a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=10 and b.current_approval_status=1 $quotation_cond $buyer_id_cond $date_cond";
							
							
							
							$bResult=sql_select($quotation_id_sql);
							foreach($bResult as $bRow)
							{
								$quotation_id.=$bRow[QUOTATION_ID].",";
							}
				
							$quotation_id=chop($quotation_id,',');
				
				
							$quotation_id_app_sql=sql_select(" select mst_id as QUOTATION_ID from wo_price_quotation a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and b.sequence_no=$user_sequence_no and b.entry_form=10 and b.current_approval_status=1 $quotation_cond ");
				
							foreach($quotation_id_app_sql as $inf)
							{
								if($quotation_id_app_byuser!="") $quotation_id_app_byuser.=",".$inf[QUOTATION_ID];
								else $quotation_id_app_byuser.=$inf[QUOTATION_ID];
							}
				
							$quotation_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
							$quotation_id_app_byuser=chop($quotation_id_app_byuser,',');
							$result=array_diff(explode(',',$quotation_id),explode(',',$quotation_id_app_byuser));
							$quotation_id=implode(",",$result);
				
							$quotation_id_cond="";
							if($quotation_id_app_byuser!="")
							{
								$quotation_id_app_byuser_arr=explode(",",$quotation_id_app_byuser);
								if( count($quotation_id_app_byuser_arr)>999)
								{
									$quotation_id_chunk_arr=array_chunk($quotation_id_app_byuser_arr,999) ;
									foreach($quotation_id_chunk_arr as $chunk_arr)
									{
										$chunk_arr_value=implode(",",$chunk_arr);
										$quotation_id_cond.=" and a.id not in($chunk_arr_value)";
									}
				
								}
								else
								{
									$quotation_id_cond=" and a.id not in($quotation_id_app_byuser)";
								}
							}
							else $quotation_id_cond="";
							
							if($select_no){$quotation_cond=" and a.id=$select_no";}
				
							if($quotation_id!="")
							{
								$sql="select a.ID,  a.company_id,  a.BUYER_ID, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, '0' as approval_id, a.approved, a.inserted_by, a.garments_nature, a.mkt_no from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type $quotation_id_cond $buyerIds_cond $buyer_id_cond $buyer_id_cond2 $date_cond $quotation_cond $mkt_no_cond group by a.ID, a.company_id, a.BUYER_ID, a.garments_nature, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, a.approved, a.inserted_by, a.mkt_no
								UNION ALL
								SELECT a.ID, a.company_id, a.BUYER_ID, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, '0' as approval_id, a.approved, a.inserted_by, a.garments_nature, a.mkt_no from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved in (1,3) and (a.id in($quotation_id))  $buyer_id_cond $buyer_id_cond2 $date_cond $quotation_cond $mkt_no_cond group by a.ID, a.company_id, a.BUYER_ID,a.garments_nature, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, a.approved , a.inserted_by, a.mkt_no ";
								
							}
							else
							{
								$sql="SELECT a.ID, a.company_id, a.BUYER_ID, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, '0' as approval_id, a.approved, a.inserted_by, a.garments_nature, a.mkt_no from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type  $quotation_id_cond $buyerIds_cond $buyer_id_cond $buyer_id_cond2  $date_cond $quotation_cond $mkt_no_cond group by a.ID, a.company_id, a.BUYER_ID, a.garments_nature, a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, a.approved , a.inserted_by, a.mkt_no order by a.id ASC";
							}
							//return $sql;
						}
						else
						{
				
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.BUYER_ID in($buyer_ids)";
							$user_sequence_no=$user_sequence_no-1;
				
							if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
							else
							{
								if($db_type==0)
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
								}
								else
								{
									$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");
								}
							}
				
							if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
							else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
				
							$sql="SELECT a.ID,  a.company_id,  a.BUYER_ID,  a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, a.approved,a.inserted_by,b.approved_date, b.id as approval_id, a.garments_nature, a.mkt_no from wo_price_quotation a, approval_history b where a.id=b.mst_id and b.entry_form=10 and a.company_id=$company_id  and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and  a.approved in(1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $quotation_cond $mkt_no_cond group by a.ID,  a.company_id,  a.BUYER_ID,  a.style_ref, a.STYLE_REF, a.QUOT_DATE,a.est_ship_date, a.approved,a.inserted_by,b.approved_date, b.ID, a.garments_nature, a.mkt_no order by a.id ASC";
						}
						
					
					}
	
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						//echo $page_id.'*'.$totalRows; exit();
						$pageTotalRows+=$totalRows;
					}		
					
					
	
					
			
				  }//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
			
	$page_id=410; //Fabric Booking Approval New
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){$year_field="YEAR(a.insert_date) as year";$orderBy_cond="IFNULL";}
					else{$year_field="to_char(a.insert_date,'YYYY') as year";$orderBy_cond="NVL";}
	
				
		
					$approval_type=0;
					if($approval_type==0)
					{
						if($db_type==0)
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
						}
						else
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
						}
				
						if($_SESSION[logic_erp][buyer_id]!='' && $_SESSION['logic_erp']["data_level_secured"]==1){
							$buyer_id_cond2=" and a.buyer_id in (".$_SESSION[logic_erp][buyer_id].")"; 
						}
						
						
						if($user_sequence_no==$min_sequence_no)
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
							$approved_user_cond=" and c.approved_by='$user_id'";
							$sql="select a.id,a.entry_form, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no  and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $booking_no_cond $date_cond $booking_year_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form order by $orderBy_cond(a.update_date, a.insert_date) desc";
							//echo $sql;
						}
						else if($sequence_no=="")
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
				
							if($db_type==0)
							{
								$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=7 and b.current_approval_status=1","booking_id");
							}
							else
							{
								$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=7 and b.current_approval_status=1","booking_id");
							}
							$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
							$seqData=sql_select($seqSql);
				
							//$sequence_no_by=$seqData[0][csf('sequence_no_by')];
							//$buyerIds=$seqData[0][csf('buyer_ids')];//die("with seq");
				
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
								else $sequence_no_by_yes.=$sRow[csf('sequence_no')].",";
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
							$booking_id_sql="select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond $seqCond
							union
							select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=1 and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=7 and b.current_approval_status=1 $buyer_id_cond $date_cond";
							$bResult=sql_select($booking_id_sql);
							foreach($bResult as $bRow)
							{
								$booking_id.=$bRow[csf('booking_id')].",";
							}
				
							$booking_id=chop($booking_id,',');
							$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
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
								else $booking_id_cond=" and a.id in($booking_id)";
							}
							else $booking_id_cond="";
				
							if($db_type==0)
							{
								if($booking_id!="")
								{
									$approved_user_cond=" and c.approved_by='$user_id'";
									$sql="select a.id, a.entry_form,a.update_date as ob_update, a.insert_date as ob_insertdate, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id,  a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond  $date_cond $buyerIds_cond $buyer_id_cond2 $booking_no_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form
										union all
										select a.id, a.entry_form,a.update_date as ob_update, a.insert_date as ob_insertdate, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved in(3) and b.fin_fab_qnty>0 and a.id in($booking_id) $buyer_id_cond $buyer_id_cond2 $date_cond $booking_no_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form order by $orderBy_cond(ob_update, ob_insertdate) desc";
								}
								else
								{
									$approved_user_cond=" and c.approved_by='$user_id'";
									$sql="select a.id, a.entry_form,$year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $date_cond $buyerIds_cond $booking_no_cond $booking_year_cond group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num,a.entry_form order by $orderBy_cond(a.update_date, a.insert_date) desc";
								}
								//echo $sql;
							}
							else
							{
								
								
								if($booking_id!="")
								{   // and a.id in($booking_id)
									$approved_user_cond=" and c.approved_by='$user_id'";
									$sql="select * from(select a.id, a.entry_form,a.update_date, a.insert_date, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond  $date_cond $buyerIds_cond $buyer_id_cond2 $booking_no_cond group by a.id, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num
										union all
										select a.id, a.entry_form,a.update_date, a.insert_date, $year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved in (3) and b.fin_fab_qnty>0 $booking_id_cond $buyer_id_cond $buyer_id_cond2 $date_cond $booking_no_cond group by a.id, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num) order by $orderBy_cond(update_date, insert_date) desc";
								}
								else
								{
									$approved_user_cond=" and c.approved_by='$user_id'";
									$sql="select a.id, a.entry_form,$year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2   $date_cond $buyerIds_cond $booking_no_cond $booking_year_cond group by a.id, a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.is_approved, a.is_apply_last_update, a.insert_date, a.update_date, a.booking_no_prefix_num order by $orderBy_cond(a.update_date, a.insert_date) desc";
									// echo $sql;die;
								}
				
				
							}
						}
						else
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
				
							$user_sequence_no = $user_sequence_no-1;
							
								if($db_type==0)
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
								}
								else
								{
									$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");
								}
				
								if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
								else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
								$approved_user_cond=" and c.approved_by='$user_id'";
				
								$sql="select a.id, a.entry_form,$year_field, a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,(select max(c.approved_no) as revised_no from approval_history c where a.id=c.mst_id $approved_user_cond) as revised_no from wo_booking_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=7 and a.company_id=$company_id and a.is_short in(2,3) and a.booking_type=1 and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved in (1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond   $date_cond $booking_no_cond $booking_year_cond order by $orderBy_cond(a.update_date, a.insert_date) desc";
							
						}
					
					}
					
					  //echo  $sql;die;
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	//$company_arr=array(3=>3);		
				
	$page_id=820; //PI Approval
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){$year_field="YEAR(a.insert_date) as year";$orderBy_cond="IFNULL";}
					else{$year_field="to_char(a.insert_date,'YYYY') as year";$orderBy_cond="NVL";}
	
				
		
					$approval_type=0;
					if($approval_type==0)
					{
					
						if($db_type==0)
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
						}
						else
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
						}
						
						
	
						if($user_sequence_no==$min_sequence_no)
						{	
							
							$sql="select a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date,a.is_apply_last_update
							from com_pi_master_details a, com_pi_item_details b 
							where a.id=b.pi_id and a.importer_id=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type and b.quantity>0 $buyer_id_cond $buyer_id_cond2 $date_cond $system_id_cond $pi_number_cond group by  a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date ,a.is_apply_last_update order by $orderBy_cond(a.update_date, a.insert_date) desc";
						}
						else if($sequence_no=="")
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
							
							if($db_type==0)
							{
								$seqSql="select group_concat(sequence_no) as sequence_no_by,
				 group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
								$seqData=sql_select($seqSql);
								$sequence_no_by=$seqData[0][csf('sequence_no_by')];
								
								$booking_id=return_field_value("group_concat(distinct(mst_id)) as booking_id","com_pi_master_details a, approval_history b","a.id=b.mst_id and a.importer_id=$company_id and b.sequence_no in ($sequence_no_by) and b.entry_form=21 and b.current_approval_status=1  $date_cond","booking_id");
								
								$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","com_pi_master_details a, approval_history b","a.id=b.mst_id and a.importer_id=$company_id and b.sequence_no=$user_sequence_no and b.entry_form=21 and b.current_approval_status=1","booking_id");
							}
							else
							{
								
								$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by, LISTAGG(case when bypass=2 and buyer_id is not null then buyer_id end, ',') WITHIN GROUP (ORDER BY null) as buyer_ids from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
								$seqData=sql_select($seqSql);
								
								$sequence_no_by=$seqData[0][csf('sequence_no_by')];
								
								$booking_id=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","com_pi_master_details a, approval_history b","a.id=b.mst_id and a.importer_id=$company_id and b.sequence_no in ($sequence_no_by) and b.entry_form=21 and b.current_approval_status=1 $buyer_id_cond $date_cond","booking_id");
								$booking_id=implode(",",array_unique(explode(",",$booking_id)));
								
								$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","com_pi_master_details a, approval_history b","a.id=b.mst_id and a.importer_id=$company_id and b.sequence_no=$user_sequence_no and b.entry_form=21 and b.current_approval_status=1","booking_id");
								$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
							}
							 //echo $booking_id;die;
							$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
							$booking_id=implode(",",$result);
							
							if($db_type==0)
							{
								if($booking_id!="")
								{
									$sql="a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date ,a.is_apply_last_update
									from com_pi_master_details a, com_pi_item_details b
									where a.id=b.pi_id  and a.importer_id=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type and b.quantity>0
									group by  a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date,a.is_apply_last_update
										union all
										a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date ,a.is_apply_last_update
										from com_pi_master_details a, com_pi_item_details b
										where a.id=b.pi_id and a.importer_id=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=1 and b.quantity>0 and a.id in($booking_id)  $date_cond  $system_id_cond $pi_number_cond
										group by  a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date,a.is_apply_last_update
										order by $orderBy_cond(ob_update, ob_insertdate) desc";
								}
								else
								{
									$sql="select a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date ,a.is_apply_last_update
									from com_pi_master_details a, com_pi_item_details b
									where a.id=b.pi_id  and a.importer_id=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.quantity>0   $date_cond $system_id_cond $pi_number_cond 
									group by  a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date ,a.is_apply_last_update
									order by $orderBy_cond(a.update_date, a.insert_date) desc";
								}
								//echo $sql;
							}
							else
							{
								if($booking_id!="")
								{
									$sql="select * from(select a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date ,a.is_apply_last_update
									from com_pi_master_details a, com_pi_item_details b
									where a.id=b.pi_id and a.importer_id=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type and b.quantity>0  $date_cond   $system_id_cond $pi_number_cond
									group by a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date,a.is_apply_last_update
										union all
										select a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date ,a.is_apply_last_update
										from com_pi_master_details a, com_pi_item_details b
										where a.id=b.pi_id and a.importer_id=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=1 and b.quantity>0 and a.id in($booking_id)   $date_cond $system_id_cond $pi_number_cond
										group by a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date,a.is_apply_last_update) order by $orderBy_cond(update_date, insert_date) desc";
								}
								else
								{
									$sql="select a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date ,a.is_apply_last_update
									from com_pi_master_details a, com_pi_item_details b 
									where a.id=b.pi_id and a.importer_id=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.approved=$approval_type and b.quantity>0   $date_cond  $system_id_cond $pi_number_cond
									group by a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, a.update_date, a.insert_date,a.is_apply_last_update order by $orderBy_cond(a.update_date, a.insert_date) desc";
								}
								
							}
							
						}
						else
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
							
							$user_sequence_no=$user_sequence_no-1;
							if($sequence_no==$user_sequence_no) 
							{
								$sequence_no_by_pass='';
							}
							else
							{
								if($db_type==0)
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
								}
								else
								{
									$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");	
								}
								
								if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
								else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
								$sql="select a.id,a.item_category_id,a.importer_id,a.source,a.supplier_id,a.pi_number,a.pi_date,a.last_shipment_date,a.internal_file_no,a.approved, sum(b.net_pi_amount) as net_pi_amount,a.update_date, a.insert_date,a.is_apply_last_update from com_pi_master_details a, approval_history b where a.id=b.mst_id and b.entry_form=21 and a.importer_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.approved=1   $sequence_no_cond $date_cond $system_id_cond $pi_number_cond order by $orderBy_cond(a.update_date, a.insert_date) desc";
							}
							
						}
			
					}
					
					 //echo  $sql;die;
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	
	$page_id=670; //Gate Pass Activation Approval
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){$year_field="YEAR(a.insert_date) as year";$orderBy_cond="IFNULL";}
					else{$year_field="to_char(a.insert_date,'YYYY') as year";$orderBy_cond="NVL";}
	
				
		
					$approval_type=0;
					if($approval_type==0)
					{
						
						$sequence_no=return_field_value("max(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0","sequence_no");
						if($user_sequence_no==$min_sequence_no)
						{
							$sql="select a.id,$year_cond,a.time_hour,a.time_minute,  a.within_group,a.sys_number_prefix_num,a.challan_no, a.sent_to,a.challan_no, a.company_id,a.out_date,'0' as approval_id from  inv_gate_pass_mst a, inv_gate_pass_dtls b where a.id=b.mst_id  and a.company_id=$company_id and a.is_approved=$approval_type  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and  a.sys_number not in(select gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0) $basis_cond $gate_pass_cond $date_cond $gatepass_year_cond  group by a.id,a.insert_date,a.time_hour,a.time_minute, a.within_group,a.challan_no, a.sent_to,a.sys_number_prefix_num,a.challan_no, a.company_id,a.out_date order by a.id desc";
						}
						else if($sequence_no=="")
						{
							
							if($db_type==0)
							{
								$group_concat="group_concat(sequence_no) ";
								$group_concat2="group_concat(mst_id) ";
							}
							else
							{
								$group_concat="LISTAGG(CAST( sequence_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no ";
								$group_concat2="LISTAGG(CAST( b.mst_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.mst_id) as quotation_id";
							}
							$quotation_id_app_byuser=return_field_value("$group_concat2","inv_gate_pass_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id  and b.sequence_no=$user_sequence_no and b.entry_form=19 and b.current_approval_status=1","quotation_id");
							
							if($quotation_id_app_byuser!="") $quotation_id_cond=" and a.id not in($quotation_id_app_byuser)";
							else if($quotation_id!="") $quotation_id_cond.=" or (a.id in($quotation_id))";
							else $quotation_id_cond="";
							
							$sequence_no_by=return_field_value("$group_concat ","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0","sequence_no");
								if($sequence_no_by)
								{
									$quotation_id=return_field_value("$group_concat2","inv_gate_pass_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id  and b.sequence_no in ($sequence_no_by) and b.entry_form=19 and b.current_approval_status=1","quotation_id");
								
								}
							
								$quotation_id_app_byuser=implode(",",array_unique(explode(",",$quotation_id_app_byuser)));
								
								   $sql="select a.id,$year_cond,a.time_hour,a.time_minute,  a.sys_number_prefix_num,a.challan_no, a.sent_to,a.challan_no, a.company_id,a.out_date,'0' as approval_id from  inv_gate_pass_mst a, inv_gate_pass_dtls b where a.id=b.mst_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.is_approved=$approval_type   and  a.sys_number not in(select gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0) $basis_cond $gate_pass_cond $date_cond  $quotation_id_cond $gatepass_year_cond group by a.id,a.insert_date,a.time_hour,a.time_minute, a.within_group,a.sys_number_prefix_num,a.challan_no, a.sent_to,a.challan_no, a.company_id,a.out_date order by a.id desc";
							
						}
						else
						{
							$user_sequence_no=$user_sequence_no-1;
							
							if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
							else
							{
								if($db_type==0)
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0");
								}
								else
								{
									$sequence_no_by_pass=return_field_value("LISTAGG(CAST( sequence_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0","sequence_no");	
								}
							}
							
							if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
							else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
						
								 $sql="select a.id,$year_cond,a.time_hour,a.time_minute, a.sys_number_prefix_num, a.challan_no, a.sent_to,a.challan_no, a.company_id,a.out_date, a.is_approved   from  inv_gate_pass_mst a,  approval_history c, inv_gate_pass_dtls b where a.id=b.mst_id and a.id=c.mst_id  and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.entry_form=19 and a.is_approved=1  and  a.sys_number not in(select gate_pass_id from inv_gate_out_scan where  gate_pass_id is not null and status_active=1 and is_deleted=0) $basis_cond $gate_pass_cond $date_cond $gatepass_year_cond group by a.id,a.insert_date,a.time_hour,a.time_minute,  a.challan_no, a.sent_to,a.sys_number_prefix_num,a.challan_no, a.company_id,a.out_date, a.is_approved order by a.id desc";
						}
						
					}
					
					 // echo  $sql;die;
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	
	$page_id=674; //Yarn Requisition Approval
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){$year_field="YEAR(a.insert_date) as year";$orderBy_cond="IFNULL";}
					else{$year_field="to_char(a.insert_date,'YYYY') as year";$orderBy_cond="NVL";}
		
					$approval_type=0;
					if($approval_type==0)
					{
	
						if($user_sequence_no==$min_sequence_no)
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and b.buyer_id in($buyer_ids)";
				
							$sql="SELECT a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id,  a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id
							from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
							where a.id=b.mst_id and a.company_id=$company_id and a.item_category_id=1  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approve=1 and (a.is_approved in (0) or a.is_approved is null) $buyer_id_cond2 group by a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.supplier_id, a.is_approved
							  order by a.requisition_date desc";
				
						}
						else if($sequence_no=="") 
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_condnon2=""; else $buyer_id_condnon2=" and b.buyer_id in($buyer_ids)";
							if($db_type==2)
							{
								$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
												$query_string.=" (c.sequence_no=".$sRow[csf('sequence_no')]." and b.buyer_id in(".implode(",",$result).")) or ";
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
									$buyerIds_cond=" and b.buyer_id not in($buyerIds)";
									$seqCond=" and (".chop($query_string,'or ').")";
								}
								//echo $seqCond;die;
								$sequence_no_by_no=chop($sequence_no_by_no,',');
								$sequence_no_by_yes=chop($sequence_no_by_yes,',');
								
								if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
								if($sequence_no_by_no=="") $sequence_no_by_no=0;
								
								
								
								$booking_id='';
								
								
								$booking_id_sql="select distinct (c.mst_id) as booking_id from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, approval_history c  where a.id=b.mst_id and a.id=c.mst_id and b.mst_id=c.mst_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.sequence_no in ($sequence_no_by_no) and c.entry_form=20 and c.current_approval_status=1 $buyer_id_condnon2  $seqCond
								union
								select distinct (c.mst_id) as booking_id from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, approval_history c  where a.id=b.mst_id and a.id=c.mst_id and b.mst_id=c.mst_id and a.company_id=$company_id and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.sequence_no in ($sequence_no_by_yes) and c.entry_form=20 and c.current_approval_status=1 $buyer_id_condnon2  ";
								//echo $booking_id_sql;die;
								$bResult=sql_select($booking_id_sql);
								foreach($bResult as $bRow)
								{
									$booking_id.=$bRow[csf('booking_id')].",";
								}
								
								$booking_id=chop($booking_id,',');
								
								$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","inv_purchase_requisition_mst a, approval_history b","a.id=b.mst_id  and a.is_deleted=0 and a.status_active=1 and a.company_id=$company_id and b.sequence_no=$user_sequence_no and b.entry_form=20 and b.current_approval_status=1","booking_id");
								$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
								
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
								
								if($booking_id!=="")
								{
									
									$sql="SELECT a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id, a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b		where a.id=b.mst_id and a.company_id=$company_id and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and a.ready_to_approve=1 and a.is_approved in (3,0) $booking_id_cond $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond group by a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.supplier_id, a.is_approved 		
									union all 
									SELECT a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id, a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id		from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
									where a.id=b.mst_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and a.ready_to_approve=1 and a.is_approved in (0) $buyer_id_condnon2 $buyer_id_condnon $buyerIds_cond group by a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.supplier_id, a.is_approved order by requisition_date desc";
									
								}
								else 
								{
									 
									  $sql="SELECT a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id, a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
									  where a.id=b.mst_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and a.ready_to_approve=1 and a.is_approved=$approval_type $buyer_id_condnon2 $buyerIds_cond group by a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.supplier_id, a.is_approved order by requisition_date desc";
								}	
								// echo $sql;	
							}
							
				
							
						}
						else // bypass No
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and b.buyer_id in($buyer_ids)";
				
							if($db_type==0)
							{
								$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and
								sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0");
							}
							else
							{
								$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no",
								"electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and
								is_deleted=0","sequence_no");
							}
							if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
							else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
				
							$sql="SELECT a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.supplier_id, a.requisition_date, a.delivery_date, a.is_approved, listagg(b.buyer_id, ',') within group (order by b.buyer_id) buyer_id
							from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, approval_history c
							where a.id=b.mst_id and a.id=c.mst_id and  a.company_id=$company_id and a.item_category_id=1  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approve=1 and a.is_approved in (0,3) and c.current_approval_status=1 $sequence_no_cond  $buyer_id_cond2 group by a.id, a.company_id, a.requ_prefix_num, a.requ_no, a.requisition_date, a.delivery_date, a.is_approved, a.supplier_id  order by requisition_date desc"; 
						}
				
	
					}
					
					 //echo  $sql;die;
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	//$company_arr=array(9=>9);
	$page_id=479; //Yarn Delivery Approval
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
			
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				 
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){$year_field="YEAR(a.insert_date) as year";$orderBy_cond="IFNULL";}
					else{$year_field="to_char(a.insert_date,'YYYY') as year";$orderBy_cond="NVL";}
		
					$approval_type=0;
					if($approval_type==0)
					{
						
						$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0 and company_id=$company_id");
						
						if($user_sequence_no==$min_sequence_no)
						{
							if($db_type==0)
							{
								$sql="SELECT a.id,  a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis,a.knit_dye_source, a.issue_date, a.knit_dye_company, '0' as approval_id,  group_concat(distinct(case when a.issue_basis=3 then  b.requisition_no end )) as requisition_no, group_concat(distinct(case when a.issue_basis=1 then  a.booking_no end )) as booking_no, a.is_approved  from inv_issue_master a, inv_transaction b where a.id=b.mst_id  and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 and a.is_approved=$approval_type and a.ready_to_approve=1  $txt_req_no $date_cond $issue_cond  group by a.id,  a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id,a.insert_date, a.issue_purpose, a.issue_basis,a.knit_dye_source, a.issue_date, a.knit_dye_company, a.is_approved";
							}
							else if($db_type==2)
							{
								$sql="SELECT a.id,  a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis,a.knit_dye_source, a.issue_date, a.knit_dye_company, '0' as approval_id, LISTAGG(CAST( (case when a.issue_basis=3 then  b.requisition_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.requisition_no) as requisition_no, LISTAGG(CAST( (case when a.issue_basis=1 then  a.booking_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, a.is_approved  from  inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 and a.is_approved=$approval_type and a.ready_to_approve=1  $txt_req_no $date_cond   $issue_cond
								group by a.id,  a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id,a.insert_date, a.issue_purpose, a.issue_basis,a.knit_dye_source, a.issue_date, a.knit_dye_company, a.is_approved";
							}
							// echo $sql;
						}
						else if($sequence_no=="") //last approval authority having bypass=no previlages // Next User bypass Yes
						{
							if($quotation_id_app_byuser!="") $quotation_id_cond=" and a.id not in($quotation_id_app_byuser)";
							else if($quotation_id!="") $quotation_id_cond.=" or (a.id in($quotation_id))";
							else $quotation_id_cond="";
							
							if($db_type==0)
							{
								$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$page_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");
								$quotation_id=return_field_value("group_concat(distinct(mst_id)) as quotation_id","inv_issue_master a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.item_category=1  and b.sequence_no in ($sequence_no_by) and b.entry_form=14 and b.current_approval_status=1","quotation_id");
								
								
								$quotation_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as quotation_id","inv_issue_master a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.item_category=1  and b.sequence_no=$user_sequence_no and b.entry_form=14 and b.current_approval_status=1","quotation_id");
								
								$sql="SELECT a.id,  a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis, a.issue_date, a.knit_dye_company,a.knit_dye_source, '0' as approval_id,  group_concat(distinct(case when a.issue_basis=3 then  b.requisition_no end )) as requisition_no, group_concat(distinct(case when a.issue_basis=1 then  a.booking_no end )) as booking_no, a.is_approved  from  inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 and a.is_approved=$approval_type and a.ready_to_approve=1  $txt_req_no $date_cond $issue_cond $quotation_id_cond  group by a.id,a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id,a.insert_date,a.issue_purpose, a.issue_basis, a.issue_date, a.knit_dye_company,a.knit_dye_source, a.is_approved";
								// echo $sql;
							}
							else if($db_type==2)
							{
								$sequence_no_by=return_field_value("LISTAGG(CAST( sequence_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no ","electronic_approval_setup","page_id=$page_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0","sequence_no");
								
								$quotation_id=return_field_value("LISTAGG(CAST( mst_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY mst_id) as quotation_id","inv_issue_master a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.item_category=1  and b.sequence_no in ($sequence_no_by) and b.entry_form=14 and b.current_approval_status=1","quotation_id");
								
								$quotation_id_app_byuser=return_field_value("LISTAGG(CAST( mst_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY mst_id) as quotation_id","inv_issue_master a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.item_category=1  and b.sequence_no=$user_sequence_no and b.entry_form=14 and b.current_approval_status=1","quotation_id");
								$quotation_id_app_byuser=implode(",",array_unique(explode(",",$quotation_id_app_byuser)));
								
								$sql="SELECT a.id,  a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis, a.issue_date, a.knit_dye_company,a.knit_dye_source, '0' as approval_id, LISTAGG(CAST( (case when a.issue_basis=3 then  b.requisition_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.requisition_no) as requisition_no, LISTAGG(CAST( (case when a.issue_basis=1 then  a.booking_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, a.is_approved  from  inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 and a.is_approved=$approval_type and a.ready_to_approve=1  $txt_req_no $date_cond $issue_cond $quotation_id_cond  group by a.id,a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id,a.insert_date,a.issue_purpose, a.issue_basis, a.issue_date, a.knit_dye_company,a.knit_dye_source, a.is_approved";
								
							}
							
						}
						else  // if previous User bypass No
						{
							$user_sequence_no=$user_sequence_no-1;
							
							if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
							else
							{
								if($db_type==0)
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0");
								}
								else
								{
									$sequence_no_by_pass=return_field_value("LISTAGG(CAST( sequence_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0","sequence_no");	
								}
							}
							
							if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
							else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
							
							if($db_type==0)
							{
								$sql="SELECT a.id,  a.issue_number_prefix_num, a.issue_number, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis, a.issue_date,a.challan_no,a.knit_dye_company,a.knit_dye_source, group_concat(distinct c.id) as approval_id,  group_concat(distinct(case when a.issue_basis=3 then  b.requisition_no end ) ) as requisition_no, group_concat(distinct(case when a.issue_basis=1 then  a.booking_no end )) as booking_no, a.is_approved   from  inv_issue_master a,  approval_history c, inv_transaction b where a.id=b.mst_id and a.id=c.mst_id  and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.entry_form=14 and a.is_approved in(3) and a.ready_to_approve=1 $txt_req_no $date_cond $issue_cond $sequence_no_cond 
								group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.insert_date, a.issue_purpose, a.issue_basis, a.issue_date, a.challan_no,a.knit_dye_company,a.knit_dye_source, a.is_approved order by a.insert_date desc";
							}
							else if($db_type==2)
							{
								$sql="SELECT a.id,  a.issue_number_prefix_num, a.issue_number, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis, a.issue_date,a.challan_no,a.knit_dye_company,a.knit_dye_source,   LISTAGG(CAST( c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as approval_id, LISTAGG(CAST( (case when a.issue_basis=3 then  b.requisition_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.requisition_no) as requisition_no, LISTAGG(CAST( (case when a.issue_basis=1 then  a.booking_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, a.is_approved   from  inv_issue_master a,  approval_history c, inv_transaction b where a.id=b.mst_id and a.id=c.mst_id  and a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.entry_form=14 and a.is_approved in(3) and a.ready_to_approve=1 $txt_req_no $date_cond $issue_cond $sequence_no_cond 
								group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.insert_date, a.issue_purpose, a.issue_basis, a.issue_date ,a.challan_no,a.knit_dye_company,a.knit_dye_source, a.is_approved
								order by a.insert_date desc";//and b.id=d.trans_id
							}
							// echo $sql;	
						}
					
					}
					
					  // echo  $sql.'=========';
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			
		}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
				
	
	$page_id=902; //Fabric Sales Order Approval
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){$year_field="YEAR(a.insert_date) as year";$orderBy_cond="IFNULL";}
					else{$year_field="to_char(a.insert_date,'YYYY') as year";$orderBy_cond="NVL";}
		
					$approval_type=0;
					if($approval_type==0)
					{
						
						if($db_type==0)
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
						}
						else
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
						}
				
						if($user_sequence_no==$min_sequence_no)
						{	
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
							if($buyer_ids=="") $po_buyer_id_cond2=""; else $po_buyer_id_cond2=" and a.po_buyer in($buyer_ids)";
				
							$sql=" select  x.* from 
													(select 
														a.id, $year_field,a.job_no,a.job_no_prefix_num,
														a.company_id,a.within_group,
														a.po_buyer,a.sales_booking_no,a.booking_id,a.booking_date,a.delivery_date,a.buyer_id,a.style_ref_no,a.location_id,a.ship_mode,a.team_leader,a.dealing_marchant,a.currency_id,a.order_uom 
													from
														fabric_sales_order_mst a
													WHERE 
														a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 and
														a.ready_to_approved=1 and a.within_group=1 and a.is_approved=0
														$po_buyer_id_cond2 $pobuyer_id_cond $date_cond
												union all
													select 
														a.id, $year_field, a.job_no,
														a.job_no_prefix_num, a.company_id, a.within_group,
														a.po_buyer,
														a.sales_booking_no, a.booking_id, a.booking_date, a.delivery_date, a.buyer_id, a.style_ref_no, a.location_id, a.ship_mode, a.team_leader, a.dealing_marchant, a.currency_id, a.order_uom 
													from
														fabric_sales_order_mst a
													WHERE 
														a.company_id=$company_id and  a.is_deleted=0 and 
														a.status_active=1 and a.ready_to_approved=1 and
														a.within_group=2 and  a.is_approved=0 $buyer_id_cond2 $buyer_id_cond
														$date_cond )  x
												order by x.id";
						}
						else if($sequence_no == "")
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
				
							if($buyer_ids=="") $buyer_id_cond2   =""; else $buyer_id_cond2   =" and a.buyer_id in($buyer_ids)";
							if($buyer_ids=="") $po_buyer_id_cond2=""; else $po_buyer_id_cond2=" and a.po_buyer in($buyer_ids)";			
							$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
											$po_query_string.=" (b.sequence_no=".$sRow[csf('sequence_no')]." and a.po_buyer in(".implode(",",$result).")) or ";
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
								$po_buyerIds_cond="";
								$po_seqCond="";
								$buyerIds_cond="";
								$seqCond="";
							}
							else
							{
								$buyerIds_cond=" and a.buyer_id not in($buyerIds)";
								$seqCond=" and (".chop($query_string,'or ').")";
				
								$po_buyerIds_cond=" and a.po_buyer not in($buyerIds)";
								$po_seqCond=" and (".chop($po_query_string,'or ').")";
							}
							$sequence_no_by_no=chop($sequence_no_by_no,',');
							$sequence_no_by_yes=chop($sequence_no_by_yes,',');
				
							if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
							if($sequence_no_by_no=="") $sequence_no_by_no=0;
				
							$sales_order_id='';
							$sales_order_id_sql="select 
													distinct (a.id) as sales_order_id
												from 
													fabric_sales_order_mst a, 
													approval_history b 
												where 
													a.id=b.mst_id  and 
													a.company_id=$company_id and 
													b.sequence_no in ($sequence_no_by_no) and 
													b.entry_form=24 and 
													b.current_approval_status=1 and 
													a.within_group=1
													$po_buyer_id_cond2 
													$po_seqCond
											union 
												select 
													distinct (a.id) as sales_order_id 
												from 
													fabric_sales_order_mst a, 
													approval_history b 
												where 
													a.id=b.mst_id  and 
													a.company_id=$company_id and 
													b.sequence_no in ($sequence_no_by_no) and 
													b.entry_form=24 and 
													b.current_approval_status=1 and 
													a.within_group=2
													$buyer_id_cond2 
													$seqCond
											union  
												select 
													distinct (a.id) as sales_order_id 
												from 
													fabric_sales_order_mst a, 
													approval_history b 
												where 
													a.id=b.mst_id  and 
													a.company_id=$company_id and 
													b.sequence_no in ($sequence_no_by_yes) and 
													b.entry_form=24 and 
													b.current_approval_status=1 and 
													a.within_group=2
													$buyer_id_cond2
											union  
												select 
													distinct (a.id) as sales_order_id 
												from 
													fabric_sales_order_mst a, 
													approval_history b 
												where 
													a.id=b.mst_id  and 
													a.company_id=$company_id and 
													b.sequence_no in ($sequence_no_by_yes) and 
													b.entry_form=24 and 
													b.current_approval_status=1 and 
													a.within_group=1
													$po_buyer_id_cond2";
													//echo $sales_order_id_sql;die;
							$bResult=sql_select($sales_order_id_sql);
							foreach($bResult as $bRow)
							{
								$sales_order_id.=$bRow[csf('sales_order_id')].",";
							}
				
							$sales_order_id=chop($sales_order_id,',');
				
							$sales_order_id_sql_app_sql=sql_select("select 
																		a.id as sales_order_id 
																	from 
																		fabric_sales_order_mst a, 
																		approval_history b
																	where 
																		a.id=b.mst_id and 
																		a.job_no=c.job_no and 
																		c.company_name=$company_id and 
																		b.sequence_no=$user_sequence_no and 
																		b.entry_form=24 and 
																		a.ready_to_approved=1 and 
																		b.current_approval_status=1");
				
							foreach($sales_order_id_sql_app_sql as $inf)
							{
								if($sales_order_id_app_byuser!="") $sales_order_id_app_byuser.=",".$inf[csf('sales_order_id')];
								else $sales_order_id_app_byuser.=$inf[csf('sales_order_id')];
							}
				
							$sales_order_id_app_byuser=implode(",",array_unique(explode(",",$sales_order_id_app_byuser)));
					
				
							$sales_order_id_app_byuser=chop($sales_order_id_app_byuser,',');
							$result=array_diff(explode(',',$sales_order_id),explode(',',$sales_order_id_app_byuser));
							$sales_order_id=implode(",",$result);
							//echo $pre_cost_id;die;
							$sales_order_id_cond="";
				
							if($sales_order_id_app_byuser!="")
							{
								$sales_order_id_app_byuser_arr=explode(",",$sales_order_id_app_byuser);
								if(count($sales_order_id_app_byuser_arr)>995)
								{
									$sales_order_id_app_byuser_chunk_arr=array_chunk(explode(",",$sales_order_id_app_byuser_arr),995) ;
									foreach($sales_order_id_app_byuser_chunk_arr as $chunk_arr)
									{
										$chunk_arr_value=implode(",",$chunk_arr);
										$sales_order_id_cond.=" and a.id not in($chunk_arr_value)";
									}
								}
								else
								{
									$sales_order_id_cond=" and a.id not in($pre_cost_id_app_byuser)";
								}
							}
							else{$sales_order_id_cond="";}
				
							$sql="select 
										a.id,$year_field,a.job_no,
										a.job_no_prefix_num,a.company_id,a.within_group,
										a.po_buyer,
										a.sales_booking_no,a.booking_id,a.booking_date,a.delivery_date,a.buyer_id,a.style_ref_no,a.location_id,a.ship_mode,a.team_leader,a.dealing_marchant,a.currency_id,
										a.order_uom ,
										0 as approval_id,
										a.is_approved,
										a.inserted_by 
									from 
										fabric_sales_order_mst a
									where
										a.company_id=$company_id and 
										a.is_deleted=0 and 
										a.status_active=1 and 
										a.ready_to_approved=1 and 
										a.is_approved in (0) and 
										a.within_group=1
										$pobuyer_id_cond 
										$date_cond 
										$sales_order_id_cond 
										$po_buyerIds_cond 
										$po_buyer_id_cond2 
									group by 
										a.id,a.insert_date,
										a.job_no,
										a.job_no_prefix_num,a.company_id,a.within_group,
										a.po_buyer,
										a.sales_booking_no,a.booking_id,a.booking_date,a.delivery_date,a.buyer_id,a.style_ref_no,a.location_id,a.ship_mode,a.team_leader,a.dealing_marchant,a.currency_id,
										a.order_uom ,
										a.is_approved,
										a.insert_date,
										a.inserted_by 
								union 
									select 
										a.id,$year_field,  a.job_no,a.job_no_prefix_num,
										a.company_id,a.within_group,
										a.po_buyer,
										a.sales_booking_no,a.booking_id,a.booking_date,a.delivery_date,a.buyer_id,a.style_ref_no,a.location_id,a.ship_mode,a.team_leader,a.dealing_marchant,a.currency_id,
										a.order_uom ,
										0 as approval_id,
										a.is_approved,
										a.inserted_by 
									from 
										fabric_sales_order_mst a
									where
										a.company_id=$company_id and 
										a.is_deleted=0 and 
										a.status_active=1 and 
										a.ready_to_approved=1 and 
										a.is_approved in (0) and 
										a.within_group=2
										$buyer_id_cond 
										$date_cond 
										$sales_order_id_cond 
										$buyerIds_cond 
										$buyer_id_cond2 
									group by 
										a.id,a.insert_date, a.job_no,a.job_no_prefix_num,
										a.company_id,a.within_group,
										a.po_buyer,
										a.sales_booking_no,a.booking_id,a.booking_date,a.delivery_date,a.buyer_id,a.style_ref_no,a.location_id,a.ship_mode,a.team_leader,a.dealing_marchant,a.currency_id,
										a.order_uom ,
										a.is_approved,
										a.insert_date,
										a.inserted_by ";
							
							if($sales_order_id!="")
							{
								$sql.=" union all
											select 
												a.id,$year_field,a.job_no,a.job_no_prefix_num,
												a.company_id,a.within_group,
												a.po_buyer,
												a.sales_booking_no,a.booking_id,a.booking_date,a.delivery_date,a.buyer_id,a.style_ref_no,a.location_id,a.ship_mode,a.team_leader,a.dealing_marchant,a.currency_id,
												a.order_uom ,
												0 as approval_id,
												a.is_approved,
												a.inserted_by 
											from 
												fabric_sales_order_mst a
											where
												a.company_id=$company_id and 
												a.is_deleted=0 and 
												a.status_active=1 and 
												a.ready_to_approved=1 and 
												a.is_approved in (1,3) and 
												(a.id in($sales_order_id)) and 
												a.within_group=1
												$pobuyer_id_cond 
												$date_cond 
												$sales_order_id_cond 
												$po_buyer_id_cond2 
											group by 
												a.id,a.insert_date, a.job_no,a.job_no_prefix_num,
												a.company_id,a.within_group,
												a.po_buyer,
												a.sales_booking_no,a.booking_id,a.booking_date,a.delivery_date,a.buyer_id,a.style_ref_no,a.location_id,a.ship_mode,a.team_leader,a.dealing_marchant,a.currency_id,
												a.order_uom ,
												a.is_approved,
												a.insert_date,
												a.inserted_by 
										union
											select 
												a.id, $year_field, a.job_no,a.job_no_prefix_num, a.company_id, a.within_group,a.po_buyer,a.sales_booking_no, a.booking_id, a.booking_date, a.delivery_date, a.buyer_id, a.style_ref_no, a.location_id, a.ship_mode, a.team_leader, a.dealing_marchant, a.currency_id,a.order_uom ,0 as approval_id,a.is_approved,
												a.inserted_by 
											from 
												fabric_sales_order_mst a
											where
												a.company_id=$company_id and 
												a.is_deleted=0 and 
												a.status_active=1 and 
												a.ready_to_approved=1 and 
												a.is_approved in (1,3) and 
												(a.id in($sales_order_id)) and 
												a.within_group=2
												$buyer_id_cond 
												$date_cond 
												$sales_order_id_cond 
												$buyer_id_cond2 
											group by 
												a.id,a.insert_date, a.job_no,a.job_no_prefix_num,
												a.company_id,a.within_group,
												a.po_buyer,
												a.sales_booking_no,a.booking_id,a.booking_date,a.delivery_date,a.buyer_id,a.style_ref_no,a.location_id,a.ship_mode,a.team_leader,a.dealing_marchant,a.currency_id,
												a.order_uom ,
												a.is_approved,
												a.insert_date,
												a.inserted_by ";
												
											}
				
				
					}
					}
					
					  //echo  $sql;die;
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	
	$page_id=627; //Stationary Work Order Approval
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){$year_field="YEAR(a.insert_date) as year";$orderBy_cond="IFNULL";}
					else{$year_field="to_char(a.insert_date,'YYYY') as year";$orderBy_cond="NVL";}
		
					$approval_type=0;
					if($approval_type==0)
					{
									
						if($user_sequence_no == $min_sequence_no) // First user
						{     
							if($db_type==0)
							{
								$select_item_cat = "group_concat(b.item_category_id) as item_category_id ";
							}else{
								$select_item_cat = "listagg(b.item_category_id, ',') within group (order by b.item_category_id) as item_category_id ";
							}
				
							$sql ="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
							FROM wo_non_order_info_mst a, wo_non_order_info_dtls b 
							WHERE a.id = b.mst_id and a.company_name=$company_id and a.entry_form = 146 and a.is_approved=$approval_type and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.ready_to_approved =1 and a.status_active=1 and a.is_deleted=0  $date_cond
							order by a.id";
							// echo $sql;
						}
				
						else // Next user
						{
							$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
							if($sequence_no=="") // bypass if previous user Yes
							{
								$sql="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
								from wo_non_order_info_mst a, wo_non_order_info_dtls b 
								where a.id =b.mst_id and a.company_name=$company_id and a.entry_form = 146 $user_crediatial_item_cat_cond2 and b.item_category_id not in(1,2,3,12,13,14) and a.is_approved in(0,3) and a.ready_to_approved=1 and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond
								order by a.id";
								// echo $sql;
							}
				
							else // bypass No
							{
								$user_sequence_no=$user_sequence_no-1;
								// echo $sequence_no.'Tipu';
								if($sequence_no==$user_sequence_no) 
								{
									// echo $sequence_no.'=='.$user_sequence_no.'if';
									$sequence_no_by_pass=$sequence_no;
									$sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
								}
								else
								{
									// echo $sequence_no.'=='.$user_sequence_no.'else';
									if($db_type==0) 
									{
										$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
				
									}
									else if($db_type==2) 
									{
										$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
									}
									
									if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
									else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";              
								}
								$sql="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
								from wo_non_order_info_mst a, approval_history b, wo_non_order_info_dtls c 
								where a.id=b.mst_id and a.id = c.mst_id and a.entry_form = 146 and a.ready_to_approved =1 and b.entry_form=5 and a.company_name=$company_id $user_crediatial_item_cat_cond and c.item_category_id not in(1,2,3,12,13,14) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.current_approval_status=1 and a.wo_number_prefix_num LIKE '%$txt_wo_no%' and a.is_approved in (1,3) $sequence_no_cond $date_cond
								order by a.id";
								// echo $sql;
							}
						}
					
					
					}
					
					 //echo  $sql;die;
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	
	$page_id=628; //Other Purchase WO Approval
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){$year_field="YEAR(a.insert_date) as year";$orderBy_cond="IFNULL";}
					else{$year_field="to_char(a.insert_date,'YYYY') as year";$orderBy_cond="NVL";}
		
					$approval_type=0;
					if($approval_type==0)
					{
						if($user_sequence_no == $min_sequence_no) // First user
						{     
							if($db_type==0)
							{
								$select_item_cat = "group_concat(b.item_category_id) as item_category_id ";
							}else{
								$select_item_cat = "listagg(b.item_category_id, ',') within group (order by b.item_category_id) as item_category_id ";
							}
				
							$sql ="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
							FROM wo_non_order_info_mst a, wo_non_order_info_dtls b 
							WHERE a.id = b.mst_id and a.company_name=$company_id $user_crediatial_item_cat_cond2 and a.is_approved=$approval_type and a.ready_to_approved =1 and a.status_active=1 and a.is_deleted=0 and b.item_category_id not in(1,4,5,6,7,11,23) and a.wo_number_prefix_num LIKE '%$txt_wo_no%' $date_cond
							order by a.id";
							// echo $sql; 
						}
						else // Next user
						{
							$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
							if($sequence_no=="") // bypass if previous user Yes
							{
								$sql="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
								from wo_non_order_info_mst a, wo_non_order_info_dtls b 
								where a.id =b.mst_id and a.company_name=$company_id $user_crediatial_item_cat_cond2 and b.item_category_id not in(1,4,5,6,7,11,23) and a.is_approved in(0,3) and a.ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.wo_number_prefix_num LIKE '%$txt_wo_no%' $date_cond
								order by a.id";
								//echo $sql;
							}
				
							else // bypass No
							{
								$user_sequence_no=$user_sequence_no-1;
								// echo $sequence_no.'Tipu';
								if($sequence_no==$user_sequence_no) 
								{
									// echo $sequence_no.'=='.$user_sequence_no.'if';
									$sequence_no_by_pass=$sequence_no;
									$sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
								}
								else
								{
									// echo $sequence_no.'=='.$user_sequence_no.'else';
									if($db_type==0) 
									{
										$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
				
									}
									else if($db_type==2) 
									{
										$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
									}
									
									if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
									else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";              
								}
								$sql="SELECT DISTINCT (a.id), a.company_name, a.wo_number_prefix_num, a.supplier_id, a.wo_date, a.delivery_date, a.wo_number, a.item_category, a.currency_id, a.wo_basis_id, a.pay_mode, a.source, a.attention, a.requisition_no,  a.delivery_place, a.location_id
								from wo_non_order_info_mst a, approval_history b, wo_non_order_info_dtls c 
								where a.id=b.mst_id and a.id = c.mst_id and a.ready_to_approved =1 and b.entry_form=17 and a.company_name=$company_id $user_crediatial_item_cat_cond and c.item_category_id not in(1,4,5,6,7,11,23) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.current_approval_status=1 and a.is_approved in (1,3) and a.wo_number_prefix_num LIKE '%$txt_wo_no%' $date_cond $sequence_no_cond
								order by a.id";
								// echo $sql;
							}
						}
					}
					
					 //echo  $sql;die;
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	
	$page_id=616; //Dyeing Batch Approval
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){$year_field="YEAR(a.insert_date) as year";$orderBy_cond="IFNULL";}
					else{$year_field="to_char(a.insert_date,'YYYY') as year";$orderBy_cond="NVL";}
		
					$batch_cond=" and a.entry_form in(0,36)"; $approve_form=" and b.entry_form in (0,36) ";
					
					$approval_type=0;
					if($approval_type==0)
					{
						
						$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 
						and is_deleted=0");
					
						if($user_sequence_no==$min_sequence_no) // check sequence
						{
							$sql="SELECT a.id,  a.booking_no_id,a.batch_no,a.booking_no,a.extention_no, a.color_id, a.company_id, a.batch_weight, a.batch_date,
							a.batch_against, a.batch_for, a.entry_form, a.batch_no, '0' as approval_id, a.is_approved ,to_char(a.insert_date,'YY') as year
							from pro_batch_create_mst a
							where  a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and 
							a.is_approved=$approval_type  $date_cond $batch_cond";
						}
						else if($sequence_no=="")
						{
							if($db_type==0)
							{
								$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$page_id
								and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");
								$booking_id=return_field_value("group_concat(distinct(mst_id)) as batch_id","pro_batch_create_mst a, approval_history b","a.id=b.mst_id
								and a.company_id=$company_id and b.sequence_no in ($sequence_no_by) and b.entry_form=16 and b.current_approval_status=1","batch_id");
								$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as batch_id","pro_batch_create_mst a, approval_history b",
								"a.id=b.mst_id and a.company_id=$company_id  and b.sequence_no=$user_sequence_no and b.entry_form=16 and 
								b.current_approval_status=1","batch_id");
							}
							else
							{
								$sequence_no_by=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no",
								"electronic_approval_setup","page_id=$page_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0","sequence_no");
								
								$booking_id=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as batch_id","pro_batch_create_mst a,
								approval_history b","a.id=b.mst_id and a.company_id=$company_id  and b.sequence_no in ($sequence_no_by) and b.entry_form=16 
								and b.current_approval_status=1","batch_id");
								$booking_id=implode(",",array_unique(explode(",",$booking_id)));
								
								$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as batch_id","pro_batch_create_mst a,
								approval_history b","a.id=b.mst_id and a.company_id=$company_id and b.sequence_no=$user_sequence_no and b.entry_form=16 and
								b.current_approval_status=1","batch_id");
								$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
							}
							
							$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
							$booking_id=implode(",",$result);
							
							$booking_id_cond="";
							if($booking_id_app_byuser!="") $booking_id_cond=" and a.id not in($booking_id_app_byuser)";
							
							$sql="SELECT a.id,a.booking_no_id,a.batch_no, a.booking_no, a.extention_no, a.color_id, a.company_id, a.batch_weight, a.batch_date,
							a.batch_against, a.batch_for, a.entry_form, a.batch_no, '0' as approval_id, a.is_approved,to_char(a.insert_date,'YY') as year from pro_batch_create_mst a 
							where  a.company_id=$company_id  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 
							and a.is_approved=$approval_type  $date_cond $batch_cond $booking_id_cond";
							if($booking_id!="")
							{
								$sql.="UNION ALL
								SELECT a.id,a.booking_no_id,a.batch_no, a.booking_no, a.extention_no, a.color_id, a.company_id, a.batch_weight, a.batch_date, 
								a.batch_against,a.batch_for, a.entry_form, a.batch_no, '0' as approval_id, a.is_approved,to_char(a.insert_date,'YY') as year from pro_batch_create_mst a 
								where  a.company_id=$company_id and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and 
								a.is_approved=1 and a.id in($booking_id) $date_cond $batch_cond";
							}
						}
						else
						{
								
							$user_sequence_no=$user_sequence_no-1;
							
							if($sequence_no==$user_sequence_no) $sequence_no_by_pass=''; 
							else
							{
								if($db_type==0)
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$page_id and
									sequence_no between $sequence_no and $user_sequence_no and bypass=1 and is_deleted=0");
								}
								else
								{
									$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no",
									"electronic_approval_setup","page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and 
									is_deleted=0","sequence_no");	
								}
							}
							if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
							else $sequence_no_cond=" and (b.sequence_no='$sequence_no' or b.sequence_no in ($sequence_no_by_pass))";
							
							$sql="SELECT a.id,  a.booking_no_id,a.batch_no, a.booking_no, a.extention_no, a.color_id, a.company_id, a.batch_weight, a.batch_date,
							a.batch_against,a.batch_for, a.entry_form, a.batch_no, b.id as approval_id, a.is_approved,b.sequence_no,to_char(a.insert_date,'YY') as year from pro_batch_create_mst a,
							approval_history b 
							where a.id=b.mst_id and  a.company_id=$company_id and b.entry_form=16 and a.is_deleted=0 and a.status_active=1 and
							a.ready_to_approved=1 and a.is_approved=1 and b.current_approval_status=1  $sequence_no_cond $date_cond $batch_cond";
						}
					
					}
					
					  //echo  $sql.'=============';
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	//die;
	$page_id=813; //Purchase Requisition Approval
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){ $year_cond_prefix= "year(a.insert_date)"; }
					else if($db_type==2){$year_cond_prefix= "TO_CHAR(a.insert_date,'YYYY')";}
								
					
					$approval_type=0;
					if($approval_type==0)
					{
					
						$permitted_item_category=return_field_value("item_cate_id","user_passwd","id=".$user_id."");
						if($permitted_item_category)
						{
							$item_category_id=$permitted_item_category;
						}
						else
						{
							$item_category_id=implode(",", array_flip(array_diff($item_category, explode(",", "1,2,3,12,13,14"))));
						}
										
						
						
						if($user_sequence_no==$min_sequence_no)
						{
							if($db_type==0)
							{
								$select_item_cat = "group_concat(b.item_category) as item_category_id ";
							}else{
								$select_item_cat = "listagg(b.item_category, ',') within group (order by b.item_category) as item_category_id ";
							}
				
								$sql ="SELECT a.id,a.remarks, a.company_id, a.requ_no, a.requ_prefix_num , $year_cond_prefix as year, $select_item_cat, a.requisition_date, a.delivery_date, 0 as approval_id, a.is_approved, a.department_id, sum(b.amount) as req_value
								from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
								where a.id = b.mst_id and a.company_id=$company_id and b.item_category in ($item_category_id) and b.item_category not in(1,2,3,12,13,14) and a.is_approved=$approval_type and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 $req_year_cond $req_date_cond $req_no_conds
								group by a.id,a.remarks, a.company_id, a.requ_no, a.requ_prefix_num , $year_cond_prefix , a.requisition_date, a.delivery_date, a.is_approved, a.department_id
								order by a.id";
						}
						else // Next user
						{
							$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
							if($sequence_no=="") // bypass if previous user Yes
							{
								if($db_type==0)
								{
									
									$seqSql="select group_concat(sequence_no) as sequence_no_by  from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
									$seqData=sql_select($seqSql);
									$sequence_no_by=$seqData[0][csf('sequence_no_by')];
									
									$requsition_id=return_field_value("group_concat(distinct(b.mst_id)) as requsition_id","inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c","a.id=b.mst_id and a.id = c.mst_id and a.company_id=$company_id and c.item_category in ($item_category_id) and c.item_category not in(1,2,3,12,13,14) and b.sequence_no in ($sequence_no_by) and a.ready_to_approve=1 and b.entry_form=1 and b.current_approval_status=1 $date_cond","requsition_id");
									$requsition_id=implode(",",array_unique(explode(",",$requsition_id)));
									
									$requsition_id_app_byuser=return_field_value("group_concat(distinct(b.mst_id)) as requsition_id","inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c","a.id=b.mst_id and a.id = c.mst_id and a.company_id=$company_id and a.ready_to_approve=1 and  c.item_category in ($item_category_id) and c.item_category not in(1,2,3,12,13,14) and b.sequence_no=$user_sequence_no and a.ready_to_approve=1 and b.entry_form=1 and b.current_approval_status=1","requsition_id");
								}
								else
								{
									$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
									$booking_id_sql="select distinct (b.mst_id) as booking_id from inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c where a.id=b.mst_id and a.id=c.mst_id and a.company_id=$company_id and c.item_category in ($item_category_id) and c.item_category not in(1,2,3,12,13,14) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=1 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond";
									//echo $booking_id_sql;die;
									$bResult=sql_select($booking_id_sql);
									foreach($bResult as $bRow)
									{
										$booking_id.=$bRow[csf('booking_id')].",";
									}
									$booking_id=chop($booking_id,',');
									
									$booking_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as booking_id","inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c","a.id=b.mst_id and a.id=c.mst_id and a.company_id=$company_id and c.item_category in ($item_category_id) and c.item_category not in(1,2,3,12,13,14) and b.sequence_no=$user_sequence_no and b.entry_form=1 and b.current_approval_status=1","booking_id");
									$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
									
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
								}
								$result=array_diff(explode(',',$requsition_id),explode(',',$requsition_id_app_byuser));
								$requsition_id=implode(",",$result);
								// print_r($requsition_id);
								if($db_type==0)
								{
									$select_item_cat = "group_concat(b.item_category) as item_category_id ";
								}else{
									
									$select_item_cat = "listagg(b.item_category, ',') within group (order by b.item_category) as item_category_id ";
								}
								
								
								$sql=" SELECT x.* from  (SELECT DISTINCT (a.id),a.remarks, a.company_id, a.requ_no, a.requ_prefix_num , $year_cond_prefix as year, $select_item_cat, a.requisition_date, a.delivery_date, a.is_approved, a.department_id, sum(b.amount) as req_value
								from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id =b.mst_id and a.company_id=$company_id and b.item_category in ($item_category_id) and b.item_category not in(1,2,3,12,13,14) and a.is_approved in(0,2) and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $req_year_cond $req_date_cond $req_no_conds
								GROUP by a.id, a.company_id,a.remarks, a.requ_no, a.requ_prefix_num , $year_cond_prefix, a.requisition_date, a.delivery_date, a.is_approved, a.department_id"; 
								if($booking_id!="")
								{
									$sql.=" UNION ALL
				
									SELECT DISTINCT (a.id),a.remarks, a.company_id, a.requ_no, a.requ_prefix_num , $year_cond_prefix as year, $select_item_cat, a.requisition_date, a.delivery_date, a.is_approved, a.department_id, sum(b.amount) as req_value
									from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id =b.mst_id and a.company_id=$company_id and b.item_category in ($item_category_id) and b.item_category not in(1,2,3,12,13,14) and a.is_approved in(3) and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $req_year_cond $req_date_cond $booking_id_cond
									GROUP by a.id,a.remarks, a.company_id,a.remarks, a.requ_no, a.requ_prefix_num , $year_cond_prefix, a.is_approved, a.requisition_date, a.delivery_date, a.department_id) x  order by x.id";
									//echo $sql;
								}
								else
								{ 
									$sql="SELECT DISTINCT (a.id),a.remarks, a.company_id, a.requ_no, a.requ_prefix_num , $year_cond_prefix as year, $select_item_cat, a.requisition_date, a.delivery_date, a.is_approved, a.department_id, sum(b.amount) as req_value
									from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id =b.mst_id and a.company_id=$company_id and b.item_category in ($item_category_id) and b.item_category not in(1,2,3,12,13,14) and a.is_approved in (0,2,3) and a.ready_to_approve=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $req_year_cond $req_date_cond $req_no_conds
									group by a.id, a.remarks, a.company_id, a.requ_no, a.requ_prefix_num , $year_cond_prefix, a.requisition_date, a.delivery_date, a.is_approved, a.department_id order by a.id";
								}
							}
							else // bypass No
							{
								$user_sequence_no=$user_sequence_no-1;
								//echo $sequence_no;
								if($sequence_no==$user_sequence_no) 
								{
									$sequence_no_by_pass=$sequence_no;
									$sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
									if($db_type==0) 
									{
										$select_item_cat = "group_concat(c.item_category) as item_category_id ";
									}
									else if($db_type==2) 
									{
										$select_item_cat = "listagg(c.item_category, ',') within group (order by c.item_category) as item_category_id ";
									}
								}
								else
								{
									if($db_type==0) 
									{
										$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
				
										$select_item_cat = "group_concat(c.item_category) as item_category_id ";
				
									}
									else if($db_type==2) 
									{
										$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
				
										$select_item_cat = "listagg(c.item_category, ',') within group (order by c.item_category) as item_category_id ";
									}
									
									if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
									else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
								
								}
									$sql="SELECT a.id,a.remarks, a.company_id, a.requ_no, a.requ_prefix_num, $year_cond_prefix as year, $select_item_cat, a.requisition_date, a.delivery_date, b.id as approval_id, a.is_approved, a.department_id, sum(c.amount) as req_value, b.approved_date, b.approved_by  
									from inv_purchase_requisition_mst a, approval_history b, inv_purchase_requisition_dtls c 
									where a.id=b.mst_id and a.id = c.mst_id and a.ready_to_approve=1 and b.entry_form=1 and a.company_id=$company_id and c.item_category in ($item_category_id) and c.item_category not in(1,2,3,12,13,14) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.current_approval_status=1 and a.is_approved in (1,3) $sequence_no_cond $req_year_cond $req_date_cond $req_no_conds
									group by a.id,a.remarks, a.company_id, a.requ_no, a.requ_prefix_num, $year_cond_prefix, a.requisition_date, a.delivery_date, b.id, a.is_approved, a.department_id, b.approved_date, b.approved_by order by a.id";
									//echo $sql;
							}
						}
					
					
						
					}
					
					   //echo  $sql;die;
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	
	$page_id=626; //Dyes N Chemical WO Approval
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){ $year_cond_prefix= "year(a.insert_date)"; }
					else if($db_type==2){$year_cond_prefix= "TO_CHAR(a.insert_date,'YYYY')";}
								
					
					$approval_type=0;
					if($approval_type==0)
					{
									
						if($db_type==0)
						{
							$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
						}
						else
						{
							$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
						}
						if($user_sequence_no==$min_sequence_no)
						{
				
							$sql= "select a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode,   a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, 0 as approval_id, 0 as sequence_no, 0 as approved_by
					from wo_non_order_info_mst a, wo_non_order_info_dtls b
					where a.id = b.mst_id and a.ready_to_approved=1 and a.company_name=$company_id and a.is_approved in (0,2) and a.entry_form=145 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $itemCategoryIdCond $woNoCond $woYearCond $woDateCond
					group by a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by
					order by a.id Desc"; 
							 //echo $sql;die;
						}
						else if($sequence_no == "")
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
				
							if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_name in($buyer_ids)";
				
							$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
							//echo $seqSql; die;
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
											$query_string.=" (b.sequence_no=".$sRow[csf('sequence_no')]." and c.buyer_name in(".implode(",",$result).")) or ";
										}
										$check_buyerIds_arr=array_unique(array_merge($check_buyerIds_arr,$buyer_id_arr));
									}
								}
								else $sequence_no_by_yes.=$sRow[csf('sequence_no')].",";
							}
				
							$buyerIds=chop($buyerIds,',');
							if($buyerIds=="")
							{
								$buyerIds_cond=""; $seqCond="";
							}
							else
							{
								$buyerIds_cond=" and a.buyer_name not in($buyerIds)"; $seqCond=" and (".chop($query_string,'or ').")";
							}
							$sequence_no_by_no=chop($sequence_no_by_no,',');
							$sequence_no_by_yes=chop($sequence_no_by_yes,',');
				
							if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
							if($sequence_no_by_no=="") $sequence_no_by_no=0;
							
							$pre_cost_id='';
							$pre_cost_id_sql="select distinct (c.mst_id) as pre_cost_id from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c where a.id = b.mst_id and a.id=c.mst_id and c.entry_form=3 and c.current_approval_status=1 and a.ready_to_approved=1 and a.entry_form = 145 and a.company_name=$company_id and a.is_approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.sequence_no in ($sequence_no_by_no) $itemCategoryIdCond $woNoCond $woYearCond $woDateCond
							union
							select distinct (c.mst_id) as pre_cost_id from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c where a.id = b.mst_id and a.id=c.mst_id and c.entry_form=3 and c.current_approval_status=1 and a.ready_to_approved=1 and a.entry_form = 145 and a.company_name=$company_id and a.is_approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.sequence_no in ($sequence_no_by_yes) $itemCategoryIdCond $woNoCond $woYearCond $woDateCond";
							$bResult=sql_select($pre_cost_id_sql);
							foreach($bResult as $bRow)
							{
								$pre_cost_id.=$bRow[csf('pre_cost_id')].",";
							}
							$pre_cost_id=chop($pre_cost_id,',');
							
							$pre_cost_id_app_sql=sql_select("select c.mst_id as pre_cost_id from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c
							where a.id = b.mst_id and a.id=c.mst_id and c.entry_form=3 and c.current_approval_status=1 and a.ready_to_approved=1 and a.entry_form = 145 and a.company_name=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.sequence_no=$user_sequence_no");
				
							foreach($pre_cost_id_app_sql as $inf)
							{
								if($pre_cost_id_app_byuser!="") $pre_cost_id_app_byuser.=",".$inf[csf('pre_cost_id')];
								else $pre_cost_id_app_byuser.=$inf[csf('pre_cost_id')];
							}
				
							$pre_cost_id_app_byuser=implode(",",array_unique(explode(",",$pre_cost_id_app_byuser)));
				
							$pre_cost_id_app_byuser=chop($pre_cost_id_app_byuser,',');
							$result=array_diff(explode(',',$pre_cost_id),explode(',',$pre_cost_id_app_byuser));
							$pre_cost_id=implode(",",$result);
							//echo $pre_cost_id;die;
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
										$pre_cost_id_cond.=" and a.id not in($chunk_arr_value)";
									}
								}
								else $pre_cost_id_cond=" and a.id not in($pre_cost_id_app_byuser)";
							}
							else $pre_cost_id_cond="";
							
							$sql= "select a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode,  a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, 0 as approval_id, 0 as sequence_no, 0 as approved_by
								from wo_non_order_info_mst a, wo_non_order_info_dtls b
								where a.id = b.mst_id and a.ready_to_approved=1 and a.company_name=$company_id and a.is_approved in (0,2) and a.entry_form=145 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $itemCategoryIdCond $woNoCond $woYearCond $woDateCond $pre_cost_id_cond
								group by a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by";
							//echo $sql;die;
							if($pre_cost_id!="")
							{
								$pre_cost_id_cond2="and ";
								$pre_cost_id_arr=explode(",",$pre_cost_id);
								if(count($pre_cost_id_arr)>995)
								{
									$pre_cost_id_cond2.=" ( ";
									$pre_cost_id_arr_chunk_arr=array_chunk(explode(",",$pre_cost_id),995) ;
									$slcunk=0;
									foreach($pre_cost_id_arr_chunk_arr as $chunk_arr)
									{
										if($slcunk>0) $pre_cost_id_cond2.=" or";
										$chunk_arr_value=implode(",",$chunk_arr);	
										$pre_cost_id_cond2.=" a.id in($chunk_arr_value)";
										$slcunk++;	
									}
									$pre_cost_id_cond2.=" )";
								}
								else $pre_cost_id_cond2.=" a.id in($pre_cost_id)";	 
								
								$sql.=" union all
										select a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode,  a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by
									from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c
									where a.id = b.mst_id and a.id=c.mst_id and c.entry_form=3 and c.current_approval_status=1 and a.ready_to_approved=1 and a.entry_form = 145 and a.company_name=$company_id and a.is_approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $itemCategoryIdCond $woNoCond $woYearCond $woDateCond $pre_cost_id_cond2
									group by a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, c.id, c.sequence_no, c.approved_by";
							}
							$sql.="order by a.id Desc";
						}
						else
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
				
							$user_sequence_no=$user_sequence_no-1;
							if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
							else
							{
								if($db_type==0)
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
								}
								else
								{
									$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and
								}
							}
				
							if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
							else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
				
							$sql="select a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode,   a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, c.id as approval_id, c.sequence_no, c.approved_by
									from wo_non_order_info_mst a, wo_non_order_info_dtls b, approval_history c
									where a.id = b.mst_id and a.id=c.mst_id and c.entry_form=3 and c.current_approval_status=1 and a.ready_to_approved=1 and a.entry_form = 145 and a.company_name=$company_id and a.is_approved in (1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $itemCategoryIdCond $woNoCond $woYearCond $woDateCond $sequence_no_cond
									group by a.id, a.company_name, a.wo_number_prefix_num, a.wo_number, a.pay_mode, a.supplier_id, a.wo_date, a.delivery_date, a.inserted_by, c.id, c.sequence_no, c.approved_by order by a.id Desc";
							//echo $sql; die;
						}
					
					}
					
					   //echo  $sql;die;
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	
	$page_id=414; //Sample Fabric Booking Aproval-With order
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){ $year_cond_prefix= "year(a.insert_date)"; }
					else if($db_type==2){$year_cond_prefix= "TO_CHAR(a.insert_date,'YYYY')";}
								
					
					$approval_type=0;
					if($approval_type==0)
					{
									
						
						if($db_type==0)
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
						}
						else
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
						}
						if($user_sequence_no==$min_sequence_no)
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
							
							$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,c.grouping, c.file_no,a.pay_mode from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$company_id and a.is_short=2 and a.booking_type=4 and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0  $buyer_id_cond2 $internal_ref_cond $file_no_cond $date_cond $booking_no_cond group by a.id,a.booking_no_prefix_num, a.booking_no, a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no,a.pay_mode order by a.insert_date desc";
						}
						else if($sequence_no=="")
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
							
							if($db_type==0)
							{
								
								$seqSql="select group_concat(sequence_no) as sequence_no_by,
											group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
								$seqData=sql_select($seqSql);
								
								$sequence_no_by=$seqData[0][csf('sequence_no_by')];
								$buyerIds=$seqData[0][csf('buyer_ids')];
								
								if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.buyer_id not in($buyerIds)";
								
								$booking_id=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=4 and a.item_category in(2,13) and b.sequence_no in ($sequence_no_by) and b.entry_form=13 and b.current_approval_status=1 $buyer_id_cond $date_cond","booking_id");
								
								$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=4 and a.item_category in(2,13) and b.sequence_no=$user_sequence_no and b.entry_form=13 and b.current_approval_status=1","booking_id");
							}
							else
							{
								
								$seqSql="SELECT LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by, LISTAGG(case when bypass=2 and buyer_id is not null then buyer_id end, ',') WITHIN GROUP (ORDER BY null) as buyer_ids from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
								$seqData=sql_select($seqSql);
								
								$sequence_no_by=$seqData[0][csf('sequence_no_by')];
								$buyerIds=$seqData[0][csf('buyer_ids')];
								
								if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.buyer_id not in($buyerIds)";
								
								$booking_id=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=4 and a.item_category in(2,13) and b.sequence_no in ($sequence_no_by) and b.entry_form=13 and b.current_approval_status=1 $buyer_id_cond $date_cond","booking_id");
								$booking_id=implode(",",array_unique(explode(",",$booking_id)));
								
								$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.is_short=2 and a.booking_type=4 and a.item_category in(2,13) and b.sequence_no=$user_sequence_no and b.entry_form=13 and b.current_approval_status=1","booking_id");
								$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
							}
							
							$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
							$booking_id=implode(",",$result);
							
							if($booking_id!="")
							{
								$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no,a.pay_mode from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$company_id and a.is_short=2 and a.booking_type=4 and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $internal_ref_cond $file_no_cond $date_cond $buyerIds_cond $booking_no_cond group by a.id, a.booking_no, a.entry_form, a.booking_no_prefix_num, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no,a.pay_mode
									union all
									SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.entry_form,  a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no,a.pay_mode from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$company_id and a.is_short=2 and a.booking_type=4 and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved in(1,3) and b.fin_fab_qnty>0 and a.id in($booking_id) $buyer_id_cond $buyer_id_cond2 $internal_ref_cond $file_no_cond $date_cond $booking_no_cond group by a.id, a.booking_no, a.entry_form, a.booking_no_prefix_num, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no,a.pay_mode order by insert_date desc";
							}
							else
							{
								$sql="SELECT a.id,a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.entry_form, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,c.grouping, c.file_no,a.pay_mode from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$company_id and a.is_short=2 and a.booking_type=4 and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0  $buyer_id_cond2 $internal_ref_cond $file_no_cond $date_cond  $booking_no_cond group by a.id, a.booking_no,a.booking_no_prefix_num, a.item_category, a.entry_form, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no,a.pay_mode order by a.insert_date desc";
							}
							// echo $sql;
						}
						else
						{ 
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
							
							$user_sequence_no=$user_sequence_no-1;
							
							if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
							else
							{
								if($db_type==0)
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
								}
								else
								{
									$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");// and bypass=1
								}
							}
							
							if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
							else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
							
							$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.entry_form, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,c.grouping, c.file_no,a.pay_mode from wo_booking_mst a, approval_history b, wo_po_break_down c where a.id=b.mst_id and a.job_no=c.job_no_mst and b.entry_form=13 and a.company_id=$company_id and a.is_short=2 and a.booking_type=4 and a.item_category in(2,13) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved=1 $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $internal_ref_cond $file_no_cond $date_cond $booking_no_cond order by a.insert_date desc";
						}
					
					
					}
						//echo  $sql;die;
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	
	$page_id=411; //Sample Booking [Without Order] Approval New
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){ $year_cond_prefix= "year(a.insert_date)"; }
					else if($db_type==2){$year_cond_prefix= "TO_CHAR(a.insert_date,'YYYY')";}
								
					
					$approval_type=0;
					if($approval_type==0)
					{
									
						if($db_type==0)
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
						}
						else
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
						}
						
						if($user_sequence_no==$min_sequence_no)
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
							$sql="SELECT a.id,a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.entry_form_id, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id,a.pay_mode, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no,a.is_approved, a.po_break_down_id FROM wo_non_ord_samp_booking_mst a where a.company_id=$company_id and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and $orderBy_cond(a.is_approved,0)=$approval_type and a.ready_to_approved=1 $booking_con $buyer_id_cond $buyer_id_cond2 and (a.entry_form_id=140 or a.entry_form_id is null or a.entry_form_id=0) order by  a.insert_date desc";
						}
						else if($sequence_no=="")
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
							
								$seqSql="SELECT sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
								$sequence_no_by_no=chop($sequence_no_by_no,',');
								$sequence_no_by_yes=chop($sequence_no_by_yes,',');
								
								if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
								if($sequence_no_by_no=="") $sequence_no_by_no=0;
								
								
								
								
								$booking_id='';
								$booking_id_sql="SELECT distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=9 and b.current_approval_status=1 $booking_con $buyer_id_cond  $seqCond
								union
								select distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=9 and b.current_approval_status=1 $booking_con $buyer_id_cond ";
								$bResult=sql_select($booking_id_sql);
								foreach($bResult as $bRow)
								{
									$booking_id.=$bRow[csf('booking_id')].",";
								}
								
								$booking_id=chop($booking_id,',');
							if($db_type==0)
							{
								$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=9 and b.current_approval_status=1 $booking_con ","booking_id");
							}
							else
							{
								$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=9 and b.current_approval_status=1 $booking_con ","booking_id");
							
							}
							
							$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
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
					
							if($booking_id!="")
							{
								$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.entry_form_id, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id,a.pay_mode, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id,a.insert_date from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and $orderBy_cond(a.is_approved,0)=$approval_type and a.ready_to_approved=1 $booking_con $buyer_id_cond $buyer_id_cond2 $buyerIds_cond group by a.id,a.booking_no_prefix_num, a.booking_no, a.item_category, a.entry_form_id, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.pay_mode, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id,a.insert_date
									union all
									SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.entry_form_id, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id,a.pay_mode, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id,a.insert_date from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and $orderBy_cond(a.is_approved,0)=1 and a.ready_to_approved=1 $booking_con $booking_id_cond $buyer_id_cond $buyer_id_cond2 group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.entry_form_id, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.pay_mode, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id,a.insert_date order by insert_date desc";
							}
							else
							{
								$buyerData=sql_select("SELECT user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$company_id and page_id=$page_id and is_deleted=0 and user_id=$user_id");
								foreach($buyerData as $row)
								{
									$buyer_ids=$row[csf('buyer_id')];
								}
				
								if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and a.buyer_id in($buyer_ids)";
								$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.entry_form_id, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id,a.pay_mode,  a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and $orderBy_cond(a.is_approved,0)=$approval_type and a.ready_to_approved=1 $booking_con $buyer_id_cond $buyer_id_cond2 $buyerIds_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.entry_form_id, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.pay_mode,  a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id,a.insert_date order by  a.insert_date desc";
							}
						}
						else
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
							$user_sequence_no=$user_sequence_no-1;
							
							if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
							else
							{
								if($db_type==0)
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
								}
								else
								{
									$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");// and bypass=1
								}
							}
							
							if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
							else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
							
							$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.entry_form_id, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id,a.pay_mode, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, b.id as approval_id ,a.is_approved, b.sequence_no, b.approved_by from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form=9 and a.company_id=$company_id and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved=1 $booking_con $buyer_id_cond $buyer_id_cond2 $sequence_no_cond order by a.insert_date desc";
						}
						
					
					}
						//echo  $sql;die;
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	
	$page_id=336; //Trims Booking Approval
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){ $year_cond_prefix= "year(a.insert_date)"; }
					else if($db_type==2){$year_cond_prefix= "TO_CHAR(a.insert_date,'YYYY')";}
								
					
					$approval_type=0;
					if($approval_type==0)
					{
						$cbo_booking_type=1;
						
						if($db_type==0)
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
						}
						else
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and buyer_id is null and bypass=2 and is_deleted=0","seq");
						}
					  
						if($user_sequence_no==$min_sequence_no) // first user
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") { $buyer_id_cond2=""; $buyer_id_condnon2="";}
							else {$buyer_id_cond2=" and c.buyer_name in($buyer_ids)"; $buyer_id_condnon2=" and a.buyer_id in($buyer_ids)";}
							if($cbo_booking_type==1) //With Order
							{
								if($db_type==0)
								{
									$sql="select a.id,a.entry_form, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source,a.entry_form, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where  a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond $buyer_id_cond3 $date_cond $booking_cond $booking_year_cond group by a.id";
								}
								if($db_type==2) 
								{
								  $sql="select a.id,a.entry_form, a.booking_no_prefix_num as prefix_num,a.booking_no,a.item_category, a.fabric_source, a.company_id, a.entry_form,a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond $buyer_id_cond3 $date_cond $booking_cond $booking_year_cond group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type,a.entry_form, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.job_no, a.is_approved, a.po_break_down_id order by a.booking_no_prefix_num";
								}
								//echo $sql;die;//die("with hot pic");
							}
							else  //WithOut Order
							{
								if($db_type==0)
								{
									$sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond $booking_year_cond group by a.id ";
								}
								if($db_type==2)
								{
									$sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond  $booking_year_cond group by a.id,a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.po_break_down_id order by a.booking_no_prefix_num";
								}
								//echo $sql;die(" with cool ppic");
							}
						}
						else if($sequence_no=="") // Next user // bypass if previous user Yes 
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") {$buyer_id_cond2=""; $buyer_id_condnon2="";} 
							else {$buyer_id_cond2=" and c.buyer_name in($buyer_ids)"; $buyer_id_condnon2=" and a.buyer_id in($buyer_ids)";}
							
							if($db_type==0)
							{
								$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
	
								$sequence_no_by_no=chop($sequence_no_by_no,',');
								$sequence_no_by_yes=chop($sequence_no_by_yes,',');
								
								if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
								if($sequence_no_by_no=="") $sequence_no_by_no=0;
								
								
								if($cbo_booking_type==1) //With Order
								{
									$booking_id='';
									 $booking_id_sql="select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and a.item_category in(4) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond $booking_cond
									union
									select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(4) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2 $booking_cond";
									//echo $booking_id_sql;die;
									$bResult=sql_select($booking_id_sql);
									foreach($bResult as $bRow)
									{
										$booking_id.=$bRow[csf('booking_id')].",";
									}
									
									$booking_id=chop($booking_id,',');
									
									$booking_id_app_byuser=return_field_value("GROUP_CONCAT(mst_id, ',') as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.booking_type=2 and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1 $booking_cond $booking_year_cond","booking_id");
									$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
									
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
									if($booking_id!="")
									{
									$sql="SELECT a.id, a.entry_form,a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved in(0) and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond $buyerIds_cond $date_cond $booking_cond $booking_year_cond group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.job_no, a.is_approved, a.po_break_down_id 
										union all 
										SELECT a.id, a.entry_form,a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.is_approved in(0,3) and a.id in($booking_id) $buyer_id_cond2 $buyer_id_cond $date_cond $booking_cond $booking_year_cond  group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.job_no, a.is_approved, a.po_break_down_id order by prefix_num";
									}
									else 
									{
										$sql="SELECT a.id,a.entry_form,a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved in(0,3) and a.ready_to_approved=1 $buyer_id_cond2 $buyer_id_cond  $buyerIds_cond $date_cond $booking_cond $booking_year_cond group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.job_no, a.is_approved, a.po_break_down_id order by a.booking_no_prefix_num";
									}
									//echo $sql;	die;
								}
								else //Without Order
								{				
									$booking_id='';
									$booking_id_sql="select distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and a.item_category in(4) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond $booking_year_cond $booking_cond
									union
									select distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(4) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2 $booking_year_cond $booking_cond ";
									//echo $booking_id_sql;die;
									$bResult=sql_select($booking_id_sql);
									foreach($bResult as $bRow)
									{
										$booking_id.=$bRow[csf('booking_id')].",";
									}
									
									$booking_id=chop($booking_id,',');
									
									$booking_id_app_byuser=return_field_value("GROUP_CONCAT(mst_id, ',') as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.booking_type=5 and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1 $booking_year_cond $booking_cond","booking_id");
									$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
									
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
									
									
									
									
									if($booking_id!="")
									{
										
										$sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond $booking_year_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.po_break_down_id 
										union all 
										select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.is_approved in(1,0) $booking_id_cond $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond $booking_year_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.po_break_down_id order by prefix_num";
										
									}
									else 
									{
										
										  $sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond $booking_year_cond group by a.id,a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.po_break_down_id order by a.booking_no_prefix_num";
									}	
							
								}
								 //echo $sql;	
							}
							
							
							if($db_type==2)
							{
								$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
								//echo $seqSql;die;
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
								
								
								if($cbo_booking_type==1) //With Order
								{
									$booking_id='';
									 $booking_id_sql="select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and a.item_category in(4) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond $booking_year_cond $booking_cond
									union
									select distinct (mst_id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(4) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2 $booking_year_cond $booking_cond ";
									//echo $booking_id_sql;die;
									$bResult=sql_select($booking_id_sql);
									foreach($bResult as $bRow)
									{
										$booking_id.=$bRow[csf('booking_id')].",";
									}
									
									$booking_id=chop($booking_id,',');
									
									$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.booking_type=2 and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1 $booking_year_cond $booking_cond","booking_id");
									$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
									
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
				
									
									$sequence_no_cond=" and d.sequence_no in ($user_sequence_no)";
									
									$sql="SELECT a.id, a.entry_form,a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved in(0,2) and a.ready_to_approved=1 $booking_year_cond $buyer_id_cond2 $buyer_id_cond $buyerIds_cond $date_cond $booking_cond   group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.job_no, a.is_approved, a.po_break_down_id";
									if($booking_id!="")
									{
										$sql.=" union all 
										select a.id, a.entry_form,a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and  b.job_no=c.job_no and a.company_id=$company_id and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.is_approved  in(3) $date_cond $booking_cond $booking_year_cond $booking_id_cond $buyer_id_cond2 $buyer_id_cond  group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.job_no, a.is_approved, a.po_break_down_id  order by prefix_num";
									}
									
								}
								else //Without Order
								{
									
									
									$booking_id='';
									$booking_id_sql="select distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id  and a.item_category in(4) and b.sequence_no in ($sequence_no_by_no) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2  $seqCond $booking_year_cond $booking_cond 
									union
									select distinct (mst_id) as booking_id from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_id and  a.item_category in(4) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=8 and b.current_approval_status=1 $buyer_id_condnon2 $booking_year_cond $booking_cond";
									//echo $booking_id_sql;die;
									$bResult=sql_select($booking_id_sql);
									foreach($bResult as $bRow)
									{
										$booking_id.=$bRow[csf('booking_id')].",";
									}
									
									$booking_id=chop($booking_id,',');
									
									$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_id and a.booking_type=5 and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1 $booking_year_cond $booking_cond","booking_id");
									$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
									
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
									
									
									
									
									if($booking_id!="")
									{
										
										$sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond $booking_year_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.po_break_down_id
										union all 
										select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0  and a.ready_to_approved=1 and a.is_approved in(1,0) $booking_id_cond $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond $booking_year_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.po_break_down_id  order by prefix_num";
										
									}
									else 
									{
										
										  $sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type and a.ready_to_approved=1 $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond $booking_year_cond group by a.id,a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.po_break_down_id  order by a.booking_no_prefix_num";
									}	
								}
								// echo $sql;	
							}
						
						}
						else // bypass No // bypass if previous user No
						{
							
							
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and c.buyer_name in($buyer_ids)";
							if($buyer_ids=="") $buyer_id_condnon2=""; else  $buyer_id_condnon2=" and a.buyer_id in($buyer_ids)";
							$user_sequence_no=$user_sequence_no-1;
							//echo $approval_type.'='.$sequence_no.'='.$user_sequence_no;die;
							if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
							else
							{
								if($db_type==0)
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_id","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and  is_deleted=0","sequence_id");
								}
								if($db_type==2)
								{
									$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_id","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and  is_deleted=0","sequence_id");
							   }
							}
							
							if($sequence_no_by_pass=="") $sequence_no_cond=" and d.sequence_no in($sequence_no)";
							else $sequence_no_cond=" and (d.sequence_no='$sequence_no' or d.sequence_no in ($sequence_no_by_pass))";
							if($cbo_booking_type==1) //With Order
							{
								
								$sql="SELECT a.id,a.entry_form,a.booking_no_prefix_num as prefix_num,a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, d.id as approval_id,a.is_approved 
								from wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c,approval_history d 
								where a.id=d.mst_id and a.booking_no=b.booking_no and b.job_no=c.job_no and d.entry_form=8 and a.company_id=$company_id and a.item_category in(4) and a.status_active=1 and a.is_deleted=0 and current_approval_status=1 and a.ready_to_approved=1 and a.is_approved in(3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond $booking_cond $booking_year_cond group by a.id,a.booking_no_prefix_num,a.entry_form,a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, d.id,a.is_approved  order by a.booking_no_prefix_num";
							}
							else
							{
								$sql="SELECT a.id,a.booking_no_prefix_num as prefix_num, a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.po_break_down_id, d.id as approval_id,a.is_approved 
								from wo_non_ord_samp_booking_mst  a, approval_history d 
								where a.id=d.mst_id and d.entry_form=8 and a.company_id=$company_id and a.item_category in(4) and a.booking_type=5 and a.status_active=1 and a.is_deleted=0 and current_approval_status=1 and a.is_approved=1 and a.ready_to_approved=1 $buyer_id_condnon $buyer_id_condnon2 $sequence_no_cond $date_cond $booking_cond $booking_year_cond";
								
							}
						}
					
					
					}
						
					
					//echo  $sql;die;
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	
	
	$page_id=413; //Short Fabric Booking Approval New
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){ $year_cond_prefix= "year(a.insert_date)"; }
					else if($db_type==2){$year_cond_prefix= "TO_CHAR(a.insert_date,'YYYY')";}
								
					
					$approval_type=0;
					if($approval_type==0)
					{
									
						
						if($db_type==0)
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
						}
						else
						{
							$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
						}
						
						//echo $user_sequence_no.'_'.$sequence_no;die;
						
						if($user_sequence_no==$min_sequence_no)
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
							
							$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,c.grouping, c.file_no, a.pay_mode 
							from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c  
							where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$company_id and a.is_short=1 and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $internal_ref_cond $file_no_cond $booking_no_cond $date_cond 
							group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no, a.pay_mode  
							order by a.insert_date desc";
						}
						
						else if($sequence_no=="")
						{
							
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
							$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
								$sequence_no_by_no=chop($sequence_no_by_no,',');
								$sequence_no_by_yes=chop($sequence_no_by_yes,',');
								
								if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
								if($sequence_no_by_no=="") $sequence_no_by_no=0;
							
								$booking_id='';
								$booking_id_sql="select distinct (a.id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id  and a.company_id=$company_id and b.sequence_no in ($sequence_no_by_no) and b.entry_form=12 and b.current_approval_status=1 $buyer_id_cond2 $buyer_id_cond      $seqCond $booking_no_cond $date_cond
								union
								select distinct (a.id) as booking_id from wo_booking_mst a, approval_history b  where a.id=b.mst_id  and a.company_id=$company_id and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=12 and b.current_approval_status=1 $buyer_id_cond2 $buyer_id_cond $booking_no_cond $date_cond";
								//echo $booking_id_sql;die;
								$bResult=sql_select($booking_id_sql);
								foreach($bResult as $bRow)
								{
									$booking_id.=$bRow[csf('booking_id')].",";
								}
								
								$booking_id=chop($booking_id,',');
				
								$booking_id_app_sql=sql_select("select b.mst_id as booking_id from wo_booking_mst a, approval_history b 
								where a.id=b.mst_id and  a.company_id=$company_id and b.sequence_no=$user_sequence_no and b.entry_form=12 and a.ready_to_approved=1 and b.current_approval_status=1 $buyer_id_cond2 $buyer_id_cond $booking_no_cond $date_cond");
								
								foreach($booking_id_app_sql as $inf)
								{
									if($booking_id_app_byuser!="") $booking_id_app_byuser.=",".$inf[csf('booking_id')];
									else $booking_id_app_byuser.=$inf[csf('booking_id')];
								}
							
								$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
								
								$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
								$booking_id=implode(",",$result);
								$booking_id_cond="";
								
								if($booking_id_app_byuser!="")
								{
									$booking_id_app_byuser_arr=explode(",",$booking_id_app_byuser);
									if(count($booking_id_app_byuser_arr)>995)
									{
										$booking_id_app_byuser_chunk_arr=array_chunk(explode(",",$booking_id_app_byuser_arr),995) ;
										foreach($booking_id_app_byuser_chunk_arr as $chunk_arr)
										{
											$chunk_arr_value=implode(",",$chunk_arr);	
											$booking_id_cond.=" and a.id not in($chunk_arr_value)";	
										}
									}
									else
									{
										$booking_id_cond=" and a.id not in($booking_id_app_byuser)";	 
									}
								}
								else $booking_id_cond="";
								
								//echo $booking_id_cond;die;
							
							
							$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no, a.pay_mode 
							from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c 
							where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$company_id and a.is_short=1 and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved in(0,3) and b.fin_fab_qnty>0 $buyerIds_cond $buyer_id_cond2 $buyer_id_cond $booking_id_cond $booking_no_cond $internal_ref_cond $file_no_cond $date_cond  
							group by a.id, a.booking_no,a.booking_no_prefix_num, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no, a.pay_mode ";
							//echo $sql;die;
							if($booking_id!="")
							{
								$sql.=" union all
								select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no, a.pay_mode from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$company_id and a.is_short=1 and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=3 and b.fin_fab_qnty>0 and a.id in($booking_id) $buyer_id_cond $buyer_id_cond2 $booking_no_cond $internal_ref_cond $file_no_cond $date_cond group by a.id, a.booking_no,a.booking_no_prefix_num, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no, a.pay_mode ";
					
							}
					
						}
						else
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
							
							$user_sequence_no=$user_sequence_no-1;
							
							if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
							else
							{
								if($db_type==0)
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
								}
								else
								{
									$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");// and bypass=1
								}
								
							}
				
							if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
							else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
							
							
							$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,c.grouping, c.file_no, a.pay_mode 
							from wo_booking_mst a, approval_history b, wo_po_break_down c  
							where a.id=b.mst_id and a.job_no=c.job_no_mst and b.entry_form=12 and a.is_short=1 and a.booking_type=1 and a.company_id=$company_id and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved=3 $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $booking_no_cond $internal_ref_cond $file_no_cond $date_cond 
							order by a.insert_date desc";
					
						}
						
					}
						
					
					//echo  $sql;die;
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}
	
	$page_id=869; //New Pre Costing Approval
	if($user_selected_page_array[$page_id]>0){
		$pageTotalRows=0;
		foreach($company_arr as $company_id=>$company_name){
				
				$user_sequence_no=$user_sequence_no_array[$page_id][$company_id];
				$min_sequence_no=$min_sequence_no_array[$page_id][$company_id];
				$maxUserNo=$max_sequence_no_array[$page_id][$company_id];
				$buyer_ids_array=$user_buyer_ids_array[$page_id][$company_id];
				
				  if($user_sequence_no !="")
				  {
				
					if($db_type==0){ $year_cond_prefix= "year(a.insert_date)"; }
					else if($db_type==2){$year_cond_prefix= "TO_CHAR(a.insert_date,'YYYY')";}
								
					
					$approval_type=0;
					if($approval_type==0)
					{
						
									
						if($db_type==0)
						{
							$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
						}
						else
						{
							$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
						}
						
						if($user_sequence_no==$min_sequence_no)
						{
							$buyer_ids = $buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
							
							$sql="select b.id,a.job_no_prefix_num,a.quotation_id,$year_cond,b.entry_from,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,'0' as approval_id, b.partial_approved,b.inserted_by from wo_pre_cost_mst b,  wo_po_details_master a where a.job_no=b.job_no and a.company_name=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.higher_othorized_approved not in (1,2,3) and b.ready_to_approved=1 and b.partial_approved=2 $job_no_cond $buyer_id_cond $buyer_id_cond2 $date_cond";
						}
						else if($sequence_no == "")
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
							
							if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_name in($buyer_ids)";
							if($db_type==0)
							{
										
								$seqSql="select group_concat(sequence_no) as sequence_no_by,
								group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0";
								$seqData=sql_select($seqSql);
								
								$sequence_no_by=$seqData[0][csf('sequence_no_by')];
								$buyerIds=$seqData[0][csf('buyer_ids')];
								
								if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.buyer_name not in($buyerIds)";
								
								$pre_cost_id=return_field_value("group_concat(distinct(mst_id)) as pre_cost_id","wo_pre_cost_mst a, approval_history b,wo_po_details_master c","a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_id and b.sequence_no in ($sequence_no_by) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3","pre_cost_id");
								//echo $pre_cost_id;die;	
								$pre_cost_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as pre_cost_id","wo_pre_cost_mst a, approval_history b,wo_po_details_master c","a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_id and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1","pre_cost_id");
							}
							else if($db_type==2) 
							{
								
								$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$company_id and page_id=$page_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
								$sequence_no_by_no=chop($sequence_no_by_no,',');
								$sequence_no_by_yes=chop($sequence_no_by_yes,',');
								
								if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
								if($sequence_no_by_no=="") $sequence_no_by_no=0;
								
								$pre_cost_id='';
								
								$pre_cost_id_sql="select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_id and b.sequence_no in ($sequence_no_by_no) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3 $seqCond
								union
								select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_id and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3";
								$bResult=sql_select($pre_cost_id_sql);
								foreach($bResult as $bRow)
								{
									$pre_cost_id.=$bRow[csf('pre_cost_id')].",";
								}
								
								$pre_cost_id=chop($pre_cost_id,',');
								
								$pre_cost_id_app_sql=sql_select("select b.mst_id as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c 
								where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_id and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1");
								foreach($pre_cost_id_app_sql as $inf)
								{                   
									if($pre_cost_id_app_byuser!="") $pre_cost_id_app_byuser.=",".$inf[csf('pre_cost_id')];
									else $pre_cost_id_app_byuser.=$inf[csf('pre_cost_id')];  
								}
								
								$pre_cost_id_app_byuser=implode(",",array_unique(explode(",",$pre_cost_id_app_byuser)));
							}
							
							$result=array_diff(explode(',',$pre_cost_id),explode(',',$pre_cost_id_app_byuser));
							
							
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
							
							
							$sql="select b.id,a.job_no_prefix_num,a.quotation_id,$year_cond,b.entry_from,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,0 as approval_id,
							b.approved,b.inserted_by 
							from wo_pre_cost_mst b,wo_po_details_master a
							where a.job_no=b.job_no and a.company_name=$company_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1
							and b.is_deleted=0 and b.ready_to_approved=1  and b.higher_othorized_approved not in (1,2,3) and b.partial_approved in (0,2) $job_no_cond $buyer_id_cond $date_cond $pre_cost_id_cond $buyerIds_cond $buyer_id_cond2";
							if($pre_cost_id!="")
							{
								$sql.=" union all
								select b.id,a.job_no_prefix_num,a.quotation_id,$year_cond,b.entry_from,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,0 as approval_id,
								b.approved,b.inserted_by 
								from wo_pre_cost_mst b,wo_po_details_master a
								where  a.job_no=b.job_no and a.company_name=$company_id  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 
								and b.higher_othorized_approved not in (1,2,3) and b.is_deleted=0 and b.ready_to_approved=1 and b.partial_approved=1 and 
								(b.id in($pre_cost_id)) $job_no_cond $buyer_id_cond $buyer_id_cond2 $date_cond";
								
								
							}
							
							//echo $sql;
						   
						}
						else
						{
							$buyer_ids=$buyer_ids_array[$user_id]['u'];
							if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
							
							$user_sequence_no=$user_sequence_no-1;
							if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
							else
							{
								if($db_type==0)
								{
									$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$company_id and page_id=$page_id and
									 sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
								}
								else
								{
									$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no)
									 as sequence_no","electronic_approval_setup","company_id=$company_id and page_id=$page_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and 	
								}
							}
							
							if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
							else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
							$sql="select b.id,a.job_no_prefix_num,a.quotation_id,$year_cond,b.entry_from,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date, b.partial_approved,b.inserted_by,
							  c.id as approval_id, c.sequence_no, c.approved_by
							  from wo_pre_cost_mst b, wo_po_details_master a,approval_history c
							  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_id  and a.is_deleted=0 and
							  a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.higher_othorized_approved not 
							  in (1,2,3) and  b.is_deleted=0  $job_no_cond $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond";
						}
						
						$sql1 = "SELECT id,job_no, cost_component_id,approved_by,current_approval_status from co_com_pre_costing_approval";   
						$costCompArray = sql_select( $sql1 );         
						
						$costCompStatus = array();
						$dbjobNo = array();
						$dbjobStatus = array();
						foreach ($costCompArray as $costComponetntRow) {
							$costCompStatus[$costComponetntRow[csf('job_no')]][$costComponetntRow[csf('cost_component_id')]][$costComponetntRow[csf('approved_by')]] = $costComponetntRow[csf('current_approval_status')];                  
						}
				
					
					}
						
					
					//echo  $sql.'=========';
				
					$nameArray=sql_select( $sql );
					$totalRows=count($nameArray);								
					if($totalRows){
						$pageTotalRows+=$totalRows;
					}		
					
			
				}//end user seq empty check;
			
			}//end company loof;
		$retunr_val_arr[$page_id] = $page_id.'*'.$pageTotalRows;
	}		
	
	echo $_SESSION['logic_erp']['user_notification']=implode('__',$retunr_val_arr);
	
	exit();	
}





?>