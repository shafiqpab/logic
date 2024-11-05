<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

$permissionSql = "SELECT approve_priv FROM user_priv_mst where user_id = $user_id AND main_menu_id = $menu_id";
$permissionCheck = sql_select( $permissionSql ); 
$approvePermission = $permissionCheck[0][csf('approve_priv')];


if($db_type==0) $year_cond="SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
else if($db_type==2) $year_cond="to_char(a.insert_date,'YYYY') as year";

if($action=="check_cost_wise_job_booking")
{
	$job_cost_row_id = $job_cost_row_id;
	$cost_component_key = $cost_component_key;
	$job_no = $job_no;
	//$tableName = "wo_booking_mst";
	
	//1=FB,2=TB,3=ServiceB,4=Sample B,5=Trim Sample,6=embellishment Booking,7=Dia Wise Fabric Booking
	
	
	if ($cost_component_key==1) // Febric Cost
	{
		$sql = "SELECT distinct(booking_no) FROM wo_booking_dtls WHERE job_no ='".$job_no."'". " AND booking_type = 1 AND is_deleted=0 AND status_active=1";
	}
	elseif ($cost_component_key==2) //  Trims Cost 
	{
		$sql = "SELECT distinct(booking_no) FROM wo_booking_dtls WHERE job_no ='".$job_no."'". " AND booking_type = 2 AND is_deleted=0 AND status_active=1";	
	}
	elseif ($cost_component_key==3) // Service booking / Embell.Cost 
	{
		//wo_booking_dtls
		$sql = "SELECT distinct(b.booking_no) FROM wo_booking_mst a, wo_booking_dtls b WHERE a.job_no ='".$job_no."'". " AND a.booking_type = 6 AND  a.booking_no = b.booking_no AND a.job_no = b.job_no AND b.booking_type = 6 AND emblishment_name in (1,2,4,5) AND a.is_deleted=0 AND a.status_active=1 AND b.is_deleted=0 AND b.status_active=1";	
	
	}
	elseif ($cost_component_key==4) //  Service booking / Gmts.Wash 
	{
		$sql = "SELECT distinct(b.booking_no) FROM wo_booking_mst a, wo_booking_dtls b WHERE a.job_no ='".$job_no."'". " AND a.booking_type = 6 AND  a.booking_no = b.booking_no AND a.job_no = b.job_no AND b.booking_type = 6 AND emblishment_name=3 AND a.is_deleted=0 AND a.status_active=1 AND b.is_deleted=0 AND b.status_active=1";	
	}
    elseif ($cost_component_key==7) //  Service booking / Gmts.Wash 
	{
        $sql = "SELECT distinct(a.labtest_no) as booking_no FROM wo_labtest_mst a, wo_labtest_dtls b WHERE a.id= b.mst_id AND b.job_no ='".$job_no."'". " AND a.is_deleted=0 AND a.status_active=1 AND b.is_deleted=0 AND b.status_active=1 ";	
       
    }
	

	$result = sql_select($sql);
	$bookingNumbers = "";
	foreach ($result as $row)
	{
		if($bookingNumbers=='')
		{
			$bookingNumbers = $row[csf('booking_no')];
		} 
		else 
		{
			$bookingNumbers .=",".$row[csf('booking_no')];
		}
	}
	
	echo "$job_cost_row_id**$bookingNumbers";	
}


if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();	
}

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );


if($action == "report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
    
	$company_name = str_replace("'","",$cbo_company_name);
	$job_no=str_replace("'","",$txt_job_no);
	$bom_percent_arr=return_library_array( "select job_id, margin_dzn_percent from wo_pre_cost_dtls",'job_id','margin_dzn_percent');
	if(str_replace("'","",$cbo_buyer_name) == 0)
	{ 
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}
	
	$txt_date = str_replace("'","",$txt_date);		
    
	$date_cond = '';
	if($txt_date!="")
	{
		if($db_type==0)  $txt_date=change_date_format($txt_date,"yyyy-mm-dd");
		else   			 $txt_date=change_date_format($txt_date,"yyyy-mm-dd","-",1);

		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and b.costing_date>'".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and b.costing_date<='".$txt_date."'";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and b.costing_date='".$txt_date."'";
		else $date_cond = '';
	}
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num='".trim($job_no)."' ";
	$approval_type = str_replace("'","",$cbo_approval_type);				
	
	$user_sequence_no = return_field_value("sequence_no","electronic_approval_setup","company_id = $cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no = return_field_value("min(sequence_no)","electronic_approval_setup","company_id = $cbo_company_name and page_id=$menu_id and is_deleted=0");
	
	$maxUserNo = return_field_value("max(sequence_no)","electronic_approval_setup","company_id = $cbo_company_name and page_id=$menu_id and is_deleted=0");
    

    $buyer_ids_array = array();
	$buyerData = sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2");
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}
    
    //var_dump($buyer_ids_array);
    
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Pre-Costing.</font>";
		die;
	}
       
    if($approval_type==2)
	{
		if($db_type==0)
		{
			$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
		}
		else
		{
            $sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
		}
		
		//$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0");
		
		if($user_sequence_no==$min_sequence_no)
		{
			$buyer_ids = $buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
			
            $sql="select b.id,a.job_no_prefix_num,a.quotation_id,$year_cond,b.entry_from,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,'0' as approval_id, b.partial_approved,b.inserted_by,a.id as job_id from wo_pre_cost_mst b,  wo_po_details_master a where a.job_no=b.job_no and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.higher_othorized_approved not in (1,2,3) and b.ready_to_approved=1 and b.partial_approved=2 $job_no_cond $buyer_id_cond $buyer_id_cond2 $date_cond";
        }
		else if($sequence_no == "")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
			
			if($buyer_ids=="") $buyer_id_cond3=""; else $buyer_id_cond3=" and c.buyer_name in($buyer_ids)";
			//echo $buyer_id_cond3; die; 	
			if($db_type==0)
			{
				//$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");			
				$seqSql="select group_concat(sequence_no) as sequence_no_by,
 				group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
				$seqData=sql_select($seqSql);
				
				$sequence_no_by=$seqData[0][csf('sequence_no_by')];
				$buyerIds=$seqData[0][csf('buyer_ids')];
				
				if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.buyer_name not in($buyerIds)";
				
				$pre_cost_id=return_field_value("group_concat(distinct(mst_id)) as pre_cost_id","wo_pre_cost_mst a, approval_history b,wo_po_details_master c","a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3","pre_cost_id");
				//echo $pre_cost_id;die;	
				$pre_cost_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as pre_cost_id","wo_pre_cost_mst a, approval_history b,wo_po_details_master c","a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1","pre_cost_id");
			}
			else if($db_type==2) 
			{
				//$sequence_no_by=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0","sequence_no");
				/*$pre_cost_id=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as pre_cost_id","wo_pre_cost_mst a, approval_history b,wo_po_details_master c","a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by) and b.entry_form=15    and  a.ready_to_approved=1 and b.current_approval_status=1","pre_cost_id");*/
				//if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.buyer_name not in($buyerIds)";
				
				$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
                //echo $buyer_id_cond3."=".$seqCond; die;
                //echo  $seqCond; die;
				$pre_cost_id_sql="select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_no) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3 $seqCond
				union
				select distinct (mst_id) as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=15 and b.current_approval_status=1 $buyer_id_cond3";
				$bResult=sql_select($pre_cost_id_sql);
				foreach($bResult as $bRow)
				{
					$pre_cost_id.=$bRow[csf('pre_cost_id')].",";
                    //var_dump($bRow);
				}
				
				$pre_cost_id=chop($pre_cost_id,',');
				
				/*$pre_cost_id_sql=sql_select("select b.mst_id as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c
				where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no in ($sequence_no_by) and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1 $buyer_id_cond3");
				foreach($pre_cost_id_sql as $val)
				{
					if($pre_cost_id!="") $pre_cost_id.=",".$val[csf('pre_cost_id')];
					else $pre_cost_id.=$val[csf('pre_cost_id')];
				}*/
									
				$pre_cost_id_app_sql=sql_select("select b.mst_id as pre_cost_id from wo_pre_cost_mst a, approval_history b, wo_po_details_master c 
				where a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1");
				foreach($pre_cost_id_app_sql as $inf)
				{                   
					if($pre_cost_id_app_byuser!="") $pre_cost_id_app_byuser.=",".$inf[csf('pre_cost_id')];
					else $pre_cost_id_app_byuser.=$inf[csf('pre_cost_id')];  
				}
				/*$pre_cost_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as pre_cost_id","wo_pre_cost_mst a, approval_history b,wo_po_details_master c","a.id=b.mst_id and a.job_no=c.job_no and c.company_name=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=15 and a.ready_to_approved=1 and b.current_approval_status=1","pre_cost_id");*/
				$pre_cost_id_app_byuser=implode(",",array_unique(explode(",",$pre_cost_id_app_byuser)));
			}
            
			//echo $pre_cost_id."===".$pre_cost_id_app_byuser; die;
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
			
			
			$sql="select b.id,a.job_no_prefix_num,a.quotation_id,$year_cond,b.entry_from,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,0 as approval_id,
			b.approved,b.inserted_by ,a.id as job_id
			from wo_pre_cost_mst b,wo_po_details_master a
			where a.job_no=b.job_no and a.company_name=$company_name and a.is_deleted=0 and a.status_active=1 and b.status_active=1
			and b.is_deleted=0 and b.ready_to_approved=1  and b.higher_othorized_approved not in (1,2,3) and b.partial_approved in (0,2) $job_no_cond $buyer_id_cond $date_cond $pre_cost_id_cond $buyerIds_cond $buyer_id_cond2";
			if($pre_cost_id!="")
			{
                $sql.=" union all
				select b.id,a.job_no_prefix_num,a.quotation_id,$year_cond,b.entry_from,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date,0 as approval_id,
				b.approved,b.inserted_by ,a.id as job_id
				from wo_pre_cost_mst b,wo_po_details_master a
				where  a.job_no=b.job_no and a.company_name=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 
				and b.higher_othorized_approved not in (1,2,3) and b.is_deleted=0 and b.ready_to_approved=1 and b.partial_approved=1 and 
				(b.id in($pre_cost_id)) $job_no_cond $buyer_id_cond $buyer_id_cond2 $date_cond";
				
				/* $sql="select b.id,a.job_no_prefix_num,$year_cond,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date, b.approved,b.inserted_by,
				c.id as approval_id, c.sequence_no, c.approved_by
				from wo_pre_cost_mst b,  wo_po_details_master a,approval_history c
				where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name  and a.is_deleted=0 and
				a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and
				b.is_deleted=0 and b.approved=1 $buyer_id_cond $sequence_no_cond $date_cond";
				*/
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
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and
					 sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no)
					 as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");//bypass=1 and 	
				}
			}
			
			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
			//else $sequence_no_cond=" and (c.sequence_no='$sequence_no' or c.sequence_no in ($sequence_no_by_pass))";
			$sql="select b.id,a.job_no_prefix_num,a.quotation_id,$year_cond,b.entry_from,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date, b.partial_approved,b.inserted_by,
			  c.id as approval_id, c.sequence_no, c.approved_by,a.id as job_id
			  from wo_pre_cost_mst b, wo_po_details_master a,approval_history c
			  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name  and a.is_deleted=0 and
			  a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.higher_othorized_approved not 
			  in (1,2,3) and  b.is_deleted=0  $job_no_cond $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond";
			  //removes (and b.partial_approved=1) 
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
	else
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_name in($buyer_ids)";
			
		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
		 $sql="select b.id,a.job_no_prefix_num,a.quotation_id,$year_cond,b.entry_from,a.job_no, a.buyer_name, a.style_ref_no,b.costing_date, b.partial_approved,b.inserted_by,b.insert_date,b.updated_by,b.update_date,
			      c.id as approval_id, c.sequence_no, c.approved_by ,a.id as job_id
			      from wo_pre_cost_mst b,  wo_po_details_master a,approval_history c
				  where b.id=c.mst_id and c.entry_form=15 and a.job_no=b.job_no and a.company_name=$company_name  and a.is_deleted=0 and
				  a.status_active=1 and b.status_active=1 and c.current_approval_status=1 and b.ready_to_approved=1 and b.higher_othorized_approved not 
				  in (1,2,3) and  b.is_deleted=0 and b.partial_approved=1 $job_no_cond $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond";
        
        // approved job checked
        $sqlApprovedJob = "select job_no,mst_id,approved_by,avg(current_approval_status) as current_approval_status from CO_COM_PRE_COSTING_APPROVAL  group by job_no,mst_id,approved_by";
        $result = sql_select($sqlApprovedJob);
        
        $appJobNolist = array(); 
        
        foreach ($result as $row) {          
            if ($row[csf('current_approval_status')]==1) {
                $appJobNolist[$row[csf('approved_by')]][$row[csf('job_no')]] = $row[csf('current_approval_status')];
            }
        }
        
        // approved cost component id checked
        $sql1 = "SELECT id,job_no, cost_component_id,approved_by,current_approval_status from co_com_pre_costing_approval";   
        $costCompArray = sql_select( $sql1 );         
        
        $costCompStatus = array();
        $dbjobNo = array();
        $dbjobStatus = array();
        foreach ($costCompArray as $costComponetntRow) {
            $costCompStatus[$costComponetntRow[csf('job_no')]][$costComponetntRow[csf('cost_component_id')]][$costComponetntRow[csf('approved_by')]] = $costComponetntRow[csf('current_approval_status')];                  
        }
	}
	  //echo $sql;die;
	//echo $approval_type;die;
	
	$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=43 and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format_ids);
	$row_id=$format_ids[0];

	$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=11 and report_id=18 and is_deleted=0 and status_active=1");
	$format_ids2=explode(",",$print_report_format_ids2);
	$row_id2=$format_ids2[0];
	//echo $row_id2;die;
	$sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1");
	$unapproved_request_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
	}

	?>
	<form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1315px; margin-top:10px">
            <legend>Pre-Costing Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1280" class="rpt_table" >
                <thead>
                    <th width="50">SL</th>
                    <th width="80">Job No</th>
                    <th width="170">Cost Components</th>
                    <th width="50">Year</th>
                    <th width="100">Buyer</th>
                    <th width="100">Style Ref.</th>
                    <th width="100">Costing Date</th>
                    <th width="100">Est. Ship Date</th>
                    <th width="50">Image</th>
					<th width="50">File</th>
                    <th width="100">Insert By</th>
                    <th width="80">Insert Date</th>
                    <th width="80">Last Update By</th>
                    <th width="80">Last Update Date</th>
                    <th width="">Un-Approved Request</th>                                    
                </thead>
            </table>
            <div style="width:1280px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1261" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                        $i = 1;
                        $nameArray=sql_select( $sql );
                        
                        foreach ($nameArray as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            
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
							$entry_from=$row[csf('entry_from')];
							if($entry_from==111) // Old Pre Cost
							{
								$pre_cost_version=1;
								$pre_cost='V1';
							}
							else
							{
								$pre_cost_version=2;
								$pre_cost='V2';
							}

							if($row_id2==23){$type=1;/*Summary;*/}
							else if($row_id2==24){$type=2;}
							else if($row_id2==25){$type=3;/*Budget Report2;*/}
							else if($row_id2==26){$type=4;/*Quote Vs Budget;*/}
							else if($row_id2==27){$type=5;/*Budget On Shipout;*/}
							else if($row_id2==29){$type=6;/*C.Date Budget On Shipout;*/}
							else if($row_id2==182){$type=7;/*Budget Report 3;*/}
							else if($row_id2==285){$type=8;/*Spot Cost VS Budget;*/}

							$function2="generat_print_report($type,$cbo_company_name,0,'','',{$row[csf('job_no_prefix_num')]},'','','',".$row[csf('year')].",0,1,'','','','')";

							if($print_cond==1)
							{	                           
								if($row_id==50){$action='preCostRpt'; } //report_btn_1;
								else if($row_id==51){$action='preCostRpt2';} //report_btn_2;
								else if($row_id==52){$action='bomRpt';} //report_btn_3;
								else if($row_id==63){$action='bomRpt2';} //report_btn_4;
								else if($row_id==156){$action='accessories_details';} //report_btn_5;
								else if($row_id==157){$action='accessories_details2';} //report_btn_6;
								else if($row_id==158){$action='preCostRptWoven';} //report_btn_7;
								else if($row_id==159){$action='bomRptWoven';} //report_btn_8;
								else if($row_id==170){$action='preCostRpt3';} //report_btn_9;
								else if($row_id==171){$action='preCostRpt4';} //report_btn_10;
								else if($row_id==173){$action='preCostRpt5';} //report_btn_10;
								else if($row_id==211){$action='mo_sheet';}
								else if($row_id==142){$action='preCostRptBpkW';}
								else if($row_id==197){$action='bomRpt3';}
								else if($row_id==192){$action='checkListRpt';}
								else if($row_id==221){$action='fabric_cost_detail';}
								else if($row_id==238){$action='summary';} 
								
								if($row_id==50){$function="report_part('".$action."','".$row[csf('job_no')]."',".$cbo_company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$pre_cost_version.");";
								}
								else{
								$function="generate_worder_report('".$action."','".$row[csf('job_no')]."',".$cbo_company_name.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."');";
								}
								
								
								
								/*report_part('preCostRpt','<? echo $row[csf('job_no')]; ?>',<? echo $cbo_company_name; ?>,<? echo $row[csf('buyer_name')]; ?>,'<? echo $row[csf('style_ref_no')];?>','<? echo $row[csf('costing_date')]; ?>','<? //echo $row[csf('po_break_down_id')]; ?>')*/
								
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" align="center"> 
									<td width="50" align="center">
                                        <? echo  $i; ?>
                                    </td>
									<td width="80" id ="job_<? echo  $i.$row[csf('job_no_prefix_num')]; ?>">
                                    	<input type="checkbox" id="check<? echo $i ?>" onclick="check_all_cost_component(<? echo $i; ?>,'<? echo $row[csf('job_no')]; ?>')" value="<?php echo $i; ?>" <? if($appJobNolist[$user_id][$row[csf('job_no')]]==1){?> checked <? } ?>/>
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('job_no')]; ?>" />
                                        <a href='##' title="<? echo $pre_cost;?>"  onclick="<? echo $function;?>"><? echo $row[csf('job_no_prefix_num')]; ?></a> 
                                        
                                         <br />
                                      
                                        <a href='##' onclick="generate_report('bomRpt3','<? echo $row[csf('job_no')]; ?>',<? echo $cbo_company_name; ?>,<? echo $row[csf('buyer_name')]; ?>,'<? echo $row[csf('style_ref_no')];?>','<? echo $row[csf('costing_date')]; ?>','','')">BOM 3</a>
										<br>
										   <a href='##' onclick="generate_report('fabric_cost_detail','<? echo $row[csf('job_no')]; ?>',<? echo $cbo_company_name; ?>,<? echo $row[csf('buyer_name')]; ?>,'<? echo $row[csf('style_ref_no')];?>','<? echo $row[csf('costing_date')]; ?>','','')">Fab Pre Cost</a>
   
                                        
                                                                   
                                    </td>
                                    <td width="170" id="cost_component_<?php echo $row[csf('job_no')];?>">
                                        <table cellspacing="0" cellpadding="0" border="0" rules="all" id="cost_component_tbl_<?php echo $i;?>">                                                                                    
                                            <?php                                            
                                            $k = 0;                
                                            foreach ($cost_components as $key=>$val) { 
                                                $k++;                                                 
                                                ?>
                                                <tr>      
                                                    <td width="170">                                               
                                                        <input type="checkbox" id="cost_com_<?php echo $i.'_'.$k;?>" value="<?php echo $i.'_'.$k;?>" class="custom" onclick="specificJobCheck('<? echo $i; ?>');<?php if($user_sequence_no==$maxUserNo) {?> specificJobCostUnCheck(<? echo $approval_type; ?>,'<?php echo $i.'_'.$k;?>',<?php echo $key; ?>,'<?php echo $row[csf('job_no')];?>');<?php } ?>"  <? if($costCompStatus[$row[csf('job_no')]][$key][$user_id]==1){?> checked <? } ?>/>
                                                        <?php echo $val;?>
                                                        <input type="hidden" id="cost_com_hidden_<?php echo $i.'_'.$k;?>" value="<?php  echo $row[csf('job_no')].'*'.$key.'*'.$row[csf('id')];?>">   
                                                    </td>
                                                </tr>
                                                <?                                                 
                                            }
                                            ?>                                            
                                        </table>
                                    </td>
                                    <td width="50"><p><? echo $row[csf('year')]; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
									<td width="100" align="center"><p><a href='##'  onclick="<? echo $function2; ?>"><? echo $row[csf('style_ref_no')]; ?></a><br>BOM%= <? echo $bom_percent_arr[$row[csf('job_id')]] ?></p></td>
                                    <td width="100" align="center"><? if($row[csf('costing_date')]!="0000-00-00") echo change_date_format($row[csf('costing_date')]); ?>&nbsp;</td>
									<td width="100" align="center" ><? if($row[csf('est_ship_date')]!="0000-00-00") echo change_date_format($row[csf('est_ship_date')]); ?>&nbsp;</td>
                                    <td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','img');">View</a></td>
									<td width="50" align="center"><a href="javascript:void()" onClick="openPopup('<? echo $row[csf('job_id')];?>','Job File Pop up','job_file_popup')">File</a></td>
                                    <td width="100"><p><? echo ucfirst ($user_arr[$row[csf('inserted_by')]]); ?>&nbsp;</p></td>
                                    <td width="80"><? $insertDate =  explode(" ",$row[csf('insert_date')]); echo change_date_format($insertDate[0]); ?></td>
                                    <td width="80"><p><? echo ucfirst ($user_arr[$row[csf('updated_by')]]); ?>&nbsp;</p></td>
                                    <td width="80"><? $upDate =  explode(" ",$row[csf('update_date')]); echo change_date_format($upDate[0]); ?></td>
                                    <td width="">
                                    	<?
										if($approval_type==1)
										{
											echo $unapproved_request_arr[$value];
										}
									?>
                                    </td>
                                </tr>
								<?
								$i++;
							}
							
							if($all_approval_id!="")
							{																
                                $con = connect();
								$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
								if($db_type==0)
								{ 
									if($rID==1)
									{
										mysql_query("COMMIT");  
										echo $msg."**".$response;
									}
									else
									{
										mysql_query("ROLLBACK"); 
										echo $msg."**".$response;
									}
								}
								if($db_type==2 || $db_type==1 )
								{
									if($rID==1)
									{
										oci_commit($con); 
										echo $msg."**".$response;
									}
									else
									{
										oci_rollback($con); 
										echo $msg."**".$response;
									}
								}
								disconnect($con); 
							}
                           
						}
                       
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1280" class="rpt_table">
				<tfoot>
                    <td width="50">&nbsp;</td>
                    <td colspan="" width="88" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>,<? echo $approvePermission;?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
	<?
	exit();	
}

if($action=="job_file_popup")
{
	echo load_html_head_contents("Job Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$job_file=sql_select("SELECT id, master_tble_id, image_location, real_file_name from common_photo_library where is_deleted=0 and form_name = 'pre_cost_v2'	and file_type = 2 and master_tble_id='$data'");
	?>
	<fieldset style="width:670px; margin-left:3px">
		<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
			<thead>
				<tr>
					<th>SL</th>
					<th>File Name</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?
				$i=1;
				foreach($job_file as $row){
					$filename_arr=explode(".", $row[csf('real_file_name')]);
				?>
					<tr>
						<td><?= $i ?></td>
						<td><?= $filename_arr[0]; ?></td>
						<td><a href="../../<?= $row[csf('image_location')];  ?>" download>download</a></td>
					</tr>
				<?
				$i++;
				}
			?>
			</tbody>
		</table>
	</fieldset>
	<?
}



if($action=="report_part_select_view")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", '', '', $unicode);
	?>
    <script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() 
		{
			//var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			var tbl_row_count = $('#list_view tbody tr').length;
			var onclickString=paramArr=functionParam="";
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				onclickString = $('#tr_' + i).attr('onclick');
				paramArr = onclickString.split("'");
				functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( strCon ) 
		{
			//alert(strCon);
				var splitSTR = strCon.split("_");
				var str_or = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
				
				if( jQuery.inArray( str_or, selected_no ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );
					selected_no.push( str_or );				
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_no.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = ''; var num='';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ',';
					num += selected_no[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				num 	= num.substr( 0, num.length - 1 );
				//alert(num);
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				$('#txt_selected_no').val( num );
		}
		
		function frm_close()
		{
			parent.emailwindow.hide();
		}
    </script>
    <?
	$select_rpt_option_arr=array(
		1=>"Main Cost Sheet ",
		2=>"Fabric Cost Details",
		3=>"Trims Cost Details",
		4=>"Embellishment Cost Details",			
		5=>"Commercial Cost Details",
		6=>"Commission Cost Details",
		7=>"Other Cost Details",
		8=>"Summary"
	);
	
	//echo $sql;die;
	?>
    
    <div>
    <table width="100%" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="list_view" align="left">
    	<thead>
        	<tr>
                <th width="50">Sl</th>
                <th>Report Option</th>
            </tr>
        </thead>
        <tbody>
			<?
            $i=1;
            foreach($select_rpt_option_arr as $id=>$val)
            {
                if ($i%2==0)
                $bgcolor="#E9F3FF";
                else
                $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value('<? echo $i."_".$id."_".$val; ?>')" style="cursor:pointer">
                    <td width="50"  align="center"><? echo $i; ?></td>
                    <td><? echo $val; ?></td>
                </tr>
                <?
                $i++;
            }
            ?>
            <tr>
                <td  style="vertical-align:middle; padding-left:20px;" colspan="2"><input type="checkbox" id="all_check" onClick="check_all_data('all_check')" />&nbsp;Check All / Un-Check All
                <input type='hidden' id='txt_selected_id' />
                <input type='hidden' id='txt_selected' />
                <input type='hidden' id='txt_selected_no' />
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <div style="width:100%"><p align="center"><input type="button" id="btn_close" class="formbutton" style="width:100px;" value="Close" onClick="frm_close();" ></p></div>
    </div>
	
    <script language="javascript" type="text/javascript">
	var category_no='<? echo $print_option_no;?>';
	var category_id='<? echo $print_option_id;?>';
	var category_des='<? echo $print_option;?>';
	var cate_ref="";
	if(category_no!="")
	{
		category_no_arr=category_no.split(",");
		category_id_arr=category_id.split(",");
		category_des_arr=category_des.split(",");
		var str_ref="";
		for(var k=0;k<category_no_arr.length; k++)
		{
			cate_ref=category_no_arr[k]+'_'+category_id_arr[k]+'_'+category_des_arr[k];
			js_set_value(cate_ref);
		}
	}
	</script>
    <?
	exit();
}

if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';
	 
    $user_sequence_no = return_field_value("sequence_no","electronic_approval_setup","company_id = $cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no = return_field_value("min(sequence_no)","electronic_approval_setup","company_id = $cbo_company_name and page_id=$menu_id and is_deleted=0");

	if($approval_type==2)
	{	
        $costComponentsArr = explode(",",$costComponents); 

        foreach ($costComponentsArr as $costComponent){
			list($job,$cost_id,$mst_id,$val)=explode("*",$costComponent);
			$deletedJob[$job]=$job;	
			//$booking_id_arr[$mst_id]=$mst_id;		
        } 
       
        $buyer_arr=return_library_array( "select b.id, a.buyer_name   from wo_pre_cost_mst b,  wo_po_details_master a   where  a.job_no=b.job_no and a.is_deleted=0 and  a.status_active=1 and b.status_active=1 and  b.is_deleted=0 and b.id in (".$booking_ids.")", "id", "buyer_name"  ); 
       // print_r($buyer_arr);die;
      	if($db_type==2) {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
		else 			{$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}

		$is_not_last_user=return_field_value("a.sequence_no","electronic_approval_setup a"," a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");

		if($is_not_last_user == "")
		{
			//$credentialUserBuyersArr = [];
			$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id");
			foreach ($sql as $key => $buyerID) {
				$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
			}

			$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
			$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
			$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
		}
		else
		{

			$check_user_buyer = sql_select("select b.user_id as user_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id");
			//echo "21**".count($check_user_buyer);die;
			if(count($check_user_buyer)==0)
			{

				$sql = sql_select("select b.buyer_id as buyer_id from user_passwd b, electronic_approval_setup a where b.id = a.user_id and a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.is_deleted=0 and a.bypass=2 $buyer_id_cond $buyer_id_cond2 group by b.buyer_id");
				foreach ($sql as $key => $buyerID) {
					$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
				}

				$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond2  group by b.buyer_id");
				foreach ($sql as $key => $buyerID) {
					$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
				}

				$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
			}
		}



		if(count($deletedJob)>0){
            $deleteSuccess = execute_query("DELETE FROM co_com_pre_costing_approval WHERE job_no in('".implode("','",$deletedJob)."') and approved_by = $user_id",1);
		}

		$field_array_cost="id, entry_form, mst_id, job_no,cost_component_id, current_approval_status, approved_by, approved_date"; 
        
        $id = return_next_id( "id","co_com_pre_costing_approval", 1 ); 
        
        $approvedJobNolist = array(); 
        $data_array_cost="";
        
        foreach ($costComponentsArr as $costComponent) {
            if($data_array_cost!="") $data_array_cost.=","; 
			list($job,$cost_id,$mst_id,$val)=explode("*",$costComponent);
            $selectedjob[$job] = $job; 

			$data_array_cost.="(".$id.",15,".$mst_id.",'".$job."',".$cost_id.",".$val.",".$user_id.",'".$pc_date_time."')"; 
			$id++;             
        } 
        
		$rID = sql_insert("co_com_pre_costing_approval",$field_array_cost,$data_array_cost,0);       
        
        $costComsql = sql_select("SELECT job_no, mst_id, current_approval_status, approved_by FROM co_com_pre_costing_approval where job_no in('".implode("','",$selectedjob)."') and approved_by = $user_id");
        
        $job_app_data = array();
        $approvedJobNolist = array();
        foreach($costComsql as $cosComDataRow)
        {         
            $job_app_data[$cosComDataRow[csf(mst_id)]][$cosComDataRow[csf(job_no)]]+= $cosComDataRow[csf(current_approval_status)]*1;
			
            if($job_app_data[$cosComDataRow[csf(mst_id)]][$cosComDataRow[csf(job_no)]]==count($cost_components))
            {
                $approvedJobNolist[$cosComDataRow[csf(job_no)]] = $cosComDataRow[csf(mst_id)];
            }
        }
        
        $bokingIds = explode(",",$booking_ids);
        foreach ($bokingIds as $bokingId) {
           
            if($approvedJobNolist[$bokingId]!='')
            {               
                $mstId = $bokingId;					
                if($mstIds=="") $mstIds= $mstId; else $mstIds .=','.$mstId;                					
            }
        }

        $max_approved_sql = sql_select("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in(".implode(",",$approvedJobNolist).") and entry_form=15 group by mst_id");
        $approvedNoArr= array();
        foreach ($max_approved_sql as $approvedRow) {          
            $approvedNoArr[$approvedRow[csf('mst_id')]] = $approvedRow[csf('approved_no')];           
        }
        
        if (count($approvedJobNolist)>0) {
        
            foreach ($approvedJobNolist as $jobNo=>$mst_id) {
                $approvedMstId[$mst_id]=$mst_id;
            }
            
			$bokingIds = explode(",",$booking_ids);
             if (count($bokingIds)>0) {
                $approve_field_array = "id, entry_form, mst_id,approved_no,sequence_no,current_approval_status,full_approved,approved_by, approved_date"; 
                $id = return_next_id( "id","approval_history", 1 ) ;
                foreach ($bokingIds as $bokingId) {
                    
                    if($approvedMstId[$bokingId]!='')
                    {
                        $mstId = $bokingId;
                        $buyer_id=$buyer_arr[$bokingId];
                        $full_approval = "";
			            if($is_not_last_user == "")
						{
							if(in_array($buyer_id,$credentialUserBuyersArr))
							{
								$full_approval=3;
							}
							else
							{
								$full_approval=1;
							}
						}
						else
						{
							if(count($credentialUserBuyersArr)>0)
							{
								if(in_array($buyer_id,$credentialUserBuyersArr))
								{
									$full_approval=3;
								}
								else
								{
									$full_approval=1;
								}
							}
							else
							{
								$full_approval=3;
							}
						}

						$approved_no = $approvedNoArr[$mst_id]; 
		                if($approved_no =='') $approved_no = 0;
		               
		                if($full_approval==1) $approved_no=$approved_no+1;

                        if($mstIds=="") $mstIds= $mstId; else $mstIds .=','.$mstId;    
                        if($data_array_approved!="") $data_array_approved.=",";
                        $data_array_approved.="(".$id.",15,".$mstId.",".$approved_no.",'".$user_sequence_no."',1,".$full_approval.",".$user_id.",'".$pc_date_time."')"; 
                        $id=$id+1;
                    }
                    $approvMstId[$mstId]=$mstId;
                }
			}
			
 
            $query = "UPDATE approval_history SET current_approval_status=0 WHERE entry_form=15 and mst_id in($mstIds)"; 
            $rID2 = execute_query($query,1); 
            
            $rID1 = sql_multirow_update("wo_pre_cost_mst","partial_approved",1,"id",implode(",",$approvMstId),0);
            
            $max_seq_no = return_field_value("max(sequence_no) as max_seq_no","electronic_approval_setup", "company_id=$cbo_company_name and page_id=$menu_id and entry_form = 15 and is_deleted = 0","max_seq_no");
            
            if ($user_sequence_no == $max_seq_no) 
            {
              $rID4=sql_multirow_update("wo_pre_cost_mst","approved",1,"id",implode(",",$approvMstId),0); 
            }
			else
			{
				$rID4=sql_multirow_update("wo_pre_cost_mst","approved",3,"id",implode(",",$approvMstId),0);
			}
            
            $rID3 = sql_insert("approval_history",$approve_field_array,$data_array_approved,0); 
            
            if ($rID1 && $rID2 && $rID3 && $rID4){$flag=1;$msg='19';} else {$flag=0;$msg='21';}
        }
		
		$sqlCheckBack=sql_select("select id, job_no from wo_pre_cost_mst where status_active=1 and is_deleted=0 and id in (".implode(',',$bokingIds).") and ready_to_approved!=1");
		$readyToApproveNoJobLIst="";
		if(count($sqlCheckBack)>0)
		{
			foreach($sqlCheckBack as $jmrow)
			{
				if($readyToApproveNoJobLIst=="") $readyToApproveNoJobLIst=$jmrow[csf('job_no')]; else $readyToApproveNoJobLIst.=",".$jmrow[csf('job_no')];
			}
			if($readyToApproveNoJobLIst!="")
			{
				echo "40**".$readyToApproveNoJobLIst; disconnect($con); die;
			}
		}
		//echo "10**"."select id, job_no from wo_pre_cost_mst where status_active=1 and is_deleted=0 and id in (".implode(',',$bokingIds).") and ready_to_approved!=1"; die;
        
        if ($rID){$flag=1; $msg='100';} else {$flag=0;$msg='101';}
		//print_r($approved_no_array); die;
	}
	else
	{				
        $costComponentsArr = explode(",",$costComponents); 
		//echo "10**<pre>";
		//print_r($costComponentsArr); die;

        $updateCostId = array(); $unApprovedJobNolist=array();
        foreach ($costComponentsArr as $costComponent) {
            if($data!="") $data.=","; 
			list($job,$cost_id,$mst_id,$val)=explode("*",$costComponent);           
            if($updateCostId[$job][$cost_id] !=1) {
                $deletedJob[$job] = $job; 
            }
			if($val>0)
            {
                $unApprovedJobNolist[$job] = $mst_id; 
            }			
        }
		$hisidArr=array();
		$hisidsql = sql_select("select mst_id, max(id) as hisid from approval_history where mst_id in(".implode(",",$unApprovedJobNolist).") and entry_form=15 group by mst_id");	
		foreach($hisidsql as $hrow)
		{
			$hisidArr[$hrow[csf('mst_id')]]=$hrow[csf('hisid')];
		}
		unset($hisidsql);
		//echo "10**<pre>";
		//print_r($unApprovedJobNolist); die;
         
		if(count($deletedJob)>0){
            $deleteSuccess = execute_query("DELETE FROM co_com_pre_costing_approval WHERE job_no in('".implode("','",$deletedJob)."') and approved_by = $user_id",1);
		}
        
        $field_array_cost="id, entry_form, mst_id, job_no,cost_component_id, current_approval_status, approved_by, approved_date"; 
        
        $id = return_next_id( "id","co_com_pre_costing_approval", 1 );     

        $data_array_cost="";
        
       // 
	    
        foreach ($costComponentsArr as $costComponent) {     
            if($data_array_cost!="") $data_array_cost.=","; 
			list($job,$cost_id,$mst_id,$val)= explode("*",$costComponent);  
			//echo $val.'--'.$job.'--'.$mst_id;
                       
            $data_array_cost.="(".$id.",15,".$mst_id.",'".$job."',".$cost_id.",".$val.",".$user_id.",'".$pc_date_time."')"; 
            $id++;            
        }       
        //print_r($unApprovedJobNolist);
        $rID = sql_insert("co_com_pre_costing_approval",$field_array_cost,$data_array_cost,0);  
        
        if($rID)
        {
            // detect unapproved cost component data which have to updated
            foreach ($costComponentsArr as $costComponent) {               
                list($job,$cost_id,$mst_id,$val)= explode("*",$costComponent);
                if($val==0)
                {
                    execute_query("update co_com_pre_costing_approval set current_approval_status=0 where cost_component_id=$cost_id and job_no='".$job."'",1);
                }
            }
        }
        
        $sqljobSt = "select job_no,mst_id,avg(current_approval_status) as current_approval_status from CO_COM_PRE_COSTING_APPROVAL  group by job_no,mst_id";
        
        $result = sql_select($sqljobSt);
             
        $field_array_appr ="id, entry_form, mst_id, approved_no, current_approval_status, approved_by, approved_date"; 
  		$unApprovedMstId_arr=array(); $unApprovedHisId_arr=array();
        if (count($unApprovedJobNolist)>0) 
        {        
            foreach ($unApprovedJobNolist as $jobNo=>$mst_id) {               
                $unApprovedMstId[$mst_id]=$mst_id;	
				$unApprovedHisId_arr[$mst_id]=$hisidArr[$mst_id];                  
            } 		
            
            $rID1 = sql_multirow_update("wo_pre_cost_mst","approved*partial_approved*ready_to_approved",'2*2*0',"id",implode(",",$unApprovedMstId),1);  
            
            $query = "UPDATE approval_history SET current_approval_status=0 WHERE entry_form=15 and mst_id in(".implode(",",$unApprovedMstId).")"; 
            $rID2 = execute_query($query,1); 
         
            $data = $user_id."*'".$pc_date_time."'";
            $rID3 = sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",implode(",",$unApprovedHisId_arr),1);        
        }
		//echo "10**"; 
      // echo $rID.'='.$rID1.'='.$rID2.'='.$rID3; print_r($unApprovedHisId_arr); die;
        if ($rID && $rID1 && $rID2 && $rID3){$flag=1;$msg='20';} else {$flag=0;$msg='21';}
		
		if($flag==1)
		{
			$unapproved_no_array=array(); $unapproved_job_array=array();
			if (count($unApprovedJobNolist)>0) 
			{

				// for checking last user ******************************************************************  
				$buyer_arr=return_library_array( "select b.id, a.buyer_name   from wo_pre_cost_mst b,  wo_po_details_master a   where  a.job_no=b.job_no and a.is_deleted=0 and  a.status_active=1 and b.status_active=1 and  b.is_deleted=0 and b.id in (".$booking_ids.")", "id", "buyer_name"  ); 
		       // print_r($buyer_arr);die;
		      	if($db_type==2) {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
				else 			{$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}
				$is_not_last_user=return_field_value("a.sequence_no","electronic_approval_setup a"," a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");

				if($is_not_last_user == "")
				{
					//$credentialUserBuyersArr = [];
					$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id");
					foreach ($sql as $key => $buyerID) {
						$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
					}

					$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
					$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
					$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
				}
				else
				{

					$check_user_buyer = sql_select("select b.user_id as user_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id");
					//echo "21**".count($check_user_buyer);die;
					if(count($check_user_buyer)==0)
					{

						$sql = sql_select("select b.buyer_id as buyer_id from user_passwd b, electronic_approval_setup a where b.id = a.user_id and a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.is_deleted=0 and a.bypass=2 $buyer_id_cond $buyer_id_cond2 group by b.buyer_id");
						foreach ($sql as $key => $buyerID) {
							$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
						}

						$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond2  group by b.buyer_id");
						foreach ($sql as $key => $buyerID) {
							$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
						}

						$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
						$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
						$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
					}
				}

				// for checking last user finish  ******************************************************************      
				foreach ($unApprovedJobNolist as $jobNo=>$mst_id) { 

					// for checking last user ******************************************************************

					$buyer_id=$buyer_arr[$mst_id];
                    $full_approval = "";
		            if($is_not_last_user == "")
					{
						if(in_array($buyer_id,$credentialUserBuyersArr))
						{
							$full_approval=3;
						}
						else
						{
							$full_approval=1;
						}
					}
					else
					{
						if(count($credentialUserBuyersArr)>0)
						{
							if(in_array($buyer_id,$credentialUserBuyersArr))
							{
								$full_approval=3;
							}
							else
							{
								$full_approval=1;
							}
						}
						else
						{
							$full_approval=3;
						}
					}


					// for checking last user finish ******************************************************************
					if($full_approval==1)
					{
						$unapproved_no_array[$mst_id]=$mst_id;
						$unapproved_job_array[$mst_id]=$jobNo;
					}
							                  
				} 
				
/*				$max_approved_sql = sql_select("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in(".implode(",",$unapproved_no_array).") and entry_form=15 group by mst_id");
				$approvedNoArr= array();
				foreach ($max_approved_sql as $approvedRow) {          
					$approvedNoArr[$approvedRow[csf('mst_id')]] = $approvedRow[csf('approved_no')];           
				}
*/				
				$max_approved_sql = sql_select("select PRE_COST_MST_ID as mst_id, max(APPROVED_NO) as approved_no from wo_pre_cost_mst_histry where PRE_COST_MST_ID in(".implode(",",$unapproved_no_array).")   group by PRE_COST_MST_ID");
				$approvedNoArr= array();
				foreach ($max_approved_sql as $approvedRow) {          
					$approvedNoArr[$approvedRow[csf('mst_id')]] = $approvedRow[csf('approved_no')];           
				}
				
				
				
				foreach($unapproved_job_array as $mst_id=>$jobNo)
				{
					$job="'".$jobNo."'";
					//echo $job; die;
					$app_no=$approvedNoArr[$mst_id]+1;
					//============================wo_pre_cost_mst_histry============
					$sql_insert="insert into wo_pre_cost_mst_histry(id, approved_no, pre_cost_mst_id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,history_insert_date) 
							select	
							'', $app_no, id, garments_nature, job_no, costing_date, incoterm, incoterm_place, machine_line, prod_line_hr, costing_per, remarks, copy_quatation, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,'$pc_date_time'
					from wo_pre_cost_mst where job_no in ($job)";
					
					//============================wo_pre_cost_dtls_histry============
					$sql_precost_dtls="insert into wo_pre_cost_dtls_histry (id, approved_no, pre_cost_dtls_id, job_no, costing_per_id, order_uom_id, fabric_cost,  fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent,	lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom, margin_bom_per, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select	
				'', $app_no, id, job_no, costing_per_id, order_uom_id, fabric_cost,  fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, commission, commission_percent,	lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, price_dzn, price_dzn_percent, margin_dzn, margin_dzn_percent, cost_pcs_set, cost_pcs_set_percent, price_pcs_or_set, price_pcs_or_set_percent, margin_pcs_set, margin_pcs_set_percent, cm_for_sipment_sche, margin_pcs_bom, margin_bom_per, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pre_cost_dtls  where job_no in ($job)";
					
					
						
					//--------------------------------------wo_pre_cost_fabric_cost_dtls_h---------------------------------------------------------------------------
					$sql_precost_fabric_cost_dtls="insert into wo_pre_cost_fabric_cost_dtls_h( id, approved_no, pre_cost_fabric_cost_dtls_id, job_no, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, color_size_sensitive, color, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, consumption_basis, process_loss_method, cons_breack_down, msmnt_break_down, color_break_down, yarn_breack_down, marker_break_down, width_dia_type)
					select	
					'', $app_no, id,job_no, item_number_id, body_part_id, fab_nature_id, color_type_id,lib_yarn_count_deter_id,construction,composition, fabric_description, gsm_weight,color_size_sensitive,	color, avg_cons, fabric_source, rate,amount,avg_finish_cons,avg_process_loss, inserted_by, insert_date,updated_by,update_date, status_active, is_deleted, company_id, costing_per,consumption_basis,process_loss_method,cons_breack_down,msmnt_break_down,color_break_down,yarn_breack_down,marker_break_down,width_dia_type from wo_pre_cost_fabric_cost_dtls where  job_no in ($job)";
					//echo $sql_precost_fabric_cost_dtls;die;
					
					//--------------------------------------wo_pre_cost_fab_yarn_cst_dtl_h---------------------------------------------------------------------------
					$sql_precost_fab_yarn_cst="insert into wo_pre_cost_fab_yarn_cst_dtl_h(id,approved_no,pre_cost_fab_yarn_cost_dtls_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select	
					'', $app_no, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,
					inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarn_cost_dtls  where  job_no in ($job)";
					//echo $sql_precost_fab_yarn_cst;die;
					
					//--------------------------------------wo_pre_cost_comarc_cost_dtls_h---------------------------------------------------------------------------
					$sql_precost_fcomarc_cost_dtls="insert into wo_pre_cost_comarc_cost_dtls_h(id,approved_no,pre_cost_comarci_cost_dtls_id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select	
					'', $app_no,id,job_no,item_id,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,
					is_deleted from wo_pre_cost_comarci_cost_dtls where  job_no in ($job)";
					//echo $sql_precost_fcomarc_cost_dtls;die;
					
					
					//--------------------------------------  wo_pre_cost_commis_cost_dtls_h---------------------------------------------------------------------------
					$sql_precost_commis_cost_dtls="insert into wo_pre_cost_commis_cost_dtls_h(id,approved_no,pre_cost_commiss_cost_dtls_id,job_no,particulars_id,
					commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select	
					'', $app_no, id,job_no,particulars_id,commission_base_id,commision_rate,commission_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_commiss_cost_dtls where  job_no in ($job)";
					//	echo $sql_precost_commis_cost_dtls;die;
					
					//--------------------------------------   wo_pre_cost_embe_cost_dtls_his---------------------------------------------------------------------------
					$sql_precost_embe_cost_dtls="insert into  wo_pre_cost_embe_cost_dtls_his(id,approved_no,pre_cost_embe_cost_dtls_id,job_no,emb_name,
					emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select	
					'', $app_no, id,job_no,emb_name,
					emb_type,cons_dzn_gmts,rate,amount,charge_lib_id,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_embe_cost_dtls  where  job_no in ($job)";
					//echo $sql_precost_commis_cost_dtls;die;
					
					//----------------------------------------------------wo_pre_cost_fab_yarnbkdown_his------------------------------------------------------------------------
					
					$sql_precost_fab_yarnbkdown_his="insert into  wo_pre_cost_fab_yarnbkdown_his(id,approved_no,pre_cost_fab_yarnbreakdown_id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select	
					'', $app_no, id,fabric_cost_dtls_id,job_no,count_id,copm_one_id,percent_one,copm_two_id,percent_two,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_fab_yarnbreakdown  where  job_no in ($job)";
					//echo $sql_precost_fab_yarnbkdown_his;die;
					
					//----------------------------------------------------wo_pre_cost_sum_dtls_histroy------------------------------------------------------------------------
					
					$sql_precost_fab_sum_dtls="insert into  wo_pre_cost_sum_dtls_histroy(id,approved_no,pre_cost_sum_dtls_id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
					comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted)
					select	
					'', $app_no, id,job_no,fab_yarn_req_kg,fab_woven_req_yds,fab_knit_req_kg,fab_woven_fin_req_yds,fab_knit_fin_req_kg,fab_amount,avg,yarn_cons_qnty,yarn_amount,conv_req_qnty,conv_charge_unit,conv_amount,trim_cons,trim_rate,trim_amount,emb_amount,comar_rate,
					comar_amount,commis_rate,commis_amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_pre_cost_sum_dtls  where  job_no in ($job)";
					//echo $sql_precost_fab_sum_dtls;die;
					//----------------------------------------------------wo_pre_cost_trim_cost_dtls_his------------------------------------------------------------------------
					
					$sql_precost_trim_cost_dtls="insert into  wo_pre_cost_trim_cost_dtls_his(id,approved_no,pre_cost_trim_cost_dtls_id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted)
					select	
					'', $app_no, id,job_no, trim_group,description,brand_sup_ref, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp,cons_breack_down,inserted_by, insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_trim_cost_dtls  where  job_no in ($job)";
					//echo $sql_precost_trim_cost_dtls;die;
					
					
					//----------------------------------------------------wo_pre_cost_trim_co_cons_dtl_h------------------------------------------------------------------------
					
					$sql_precost_trim_co_cons_dtl="insert into   wo_pre_cost_trim_co_cons_dtl_h(id,approved_no,pre_cost_trim_co_cons_dtls_id,wo_pre_cost_trim_cost_dtls_id,job_no, po_break_down_id,item_size, cons, place, pcs,country_id)
					select	
					'', $app_no, id,wo_pre_cost_trim_cost_dtls_id,job_no,po_break_down_id,item_size, cons,place, pcs,country_id from wo_pre_cost_trim_co_cons_dtls  where  job_no in ($job)";
					//----------------------------------------------------wo_pre_cost_fab_con_cst_dtls_h------------------------------------------------------------------------
					
					$sql_precost_fab_con_cst_dtls="insert into   wo_pre_cost_fab_con_cst_dtls_h(id,approved_no,pre_cost_fab_conv_cst_dtls_id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted)
					select	
					'', $app_no, id,job_no, fabric_description,cons_process,req_qnty,charge_unit,amount,color_break_down,charge_lib_id,inserted_by,insert_date,updated_by,update_date, status_active,is_deleted from wo_pre_cost_fab_conv_cost_dtls  where  job_no in ($job)";
					if(count($sql_precost_trim_cost_dtls)>0)
					{
						$rID12=execute_query($sql_precost_trim_cost_dtls,1);
						if($flag==1) 
						{
							if($rID12) $flag=1; else $flag=0; 
						} 
					}
					
					if(count($sql_precost_trim_cost_dtls)>0)
					{
						$rID13=execute_query($sql_precost_trim_co_cons_dtl,1);
						if($flag==1) 
						{
							if($rID13) $flag=1; else $flag=0; 
						}
					}
					
					//echo $sql_precost_fab_con_cst_dtls;die;
					$rID13=execute_query($sql_precost_fab_con_cst_dtls,1);
					if($flag==1) 
					{
						if($rID13) $flag=1; else $flag=0; 
					}
					
					if(count($sql_insert)>0)
					{
						$rID3=execute_query($sql_insert,0);
						if($flag==1) 
						{
							if($rID3) $flag=1; else $flag=0; 
						} 
					}
					if(count($sql_precost_dtls)>0)
					{
						$rID4=execute_query($sql_precost_dtls,1);
						if($flag==1) 
						{
							if($rID4) $flag=1; else $flag=0; 
						}
					}
					if(count($sql_precost_fabric_cost_dtls)>0)
					{
						$rID5=execute_query($sql_precost_fabric_cost_dtls,1);
						if($flag==1) 
						{
							if($rID5) $flag=1; else $flag=0; 
						} 
					}
					if(count($sql_precost_fab_yarn_cst)>0)
					{
						$rID6=execute_query($sql_precost_fab_yarn_cst,1);
						if($flag==1) 
						{
							if($rID6) $flag=1; else $flag=0; 
						} 
					}
					if(count($sql_precost_fcomarc_cost_dtls)>0)
					{	
						$rID7=execute_query($sql_precost_fcomarc_cost_dtls,1);
						if($flag==1) 
						{
							if($rID7) $flag=1; else $flag=0; 
						} 			
					}
					if(count($sql_precost_commis_cost_dtls)>0)
					{
						$rID8=execute_query($sql_precost_commis_cost_dtls,1);
						if($flag==1) 
						{
							if($rID8) $flag=1; else $flag=0; 
						}
					}
					if(count($sql_precost_embe_cost_dtls)>0)
					{
						$rID9=execute_query($sql_precost_embe_cost_dtls,1);
						if($flag==1) 
						{
							if($rID9) $flag=1; else $flag=0; 
						} 
					}
					if(count($sql_precost_fab_yarnbkdown_his)>0)
					{
						$rID10=execute_query($sql_precost_fab_yarnbkdown_his,1);
						if($flag==1) 
						{
							if($rID10) $flag=1; else $flag=0; 
						} 
					}
					if(count($sql_precost_fab_sum_dtls)>0)
					{
						$rID11=execute_query($sql_precost_fab_sum_dtls,1);
						if($flag==1) 
						{
							if($rID11) $flag=1; else $flag=0; 
						} 
					}
				}
			}
		}
	}
    
	if($db_type==0)
    { 
        if($flag==1)
        {
            mysql_query("COMMIT");  
            echo $msg."**".$response;
        }
        else
        {
            mysql_query("ROLLBACK"); 
            echo $msg."**".$response;
        }
    }
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con); 
			echo $msg."**".$response;
		}
		else
		{
			oci_rollback($con); 
			echo $msg."**".$response;
		}
	}
	disconnect($con);
	die;
}

if($action=="img")
{	
	echo load_html_head_contents("Image View", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id='$id' and form_name='knit_order_entry' and file_type=1";
					
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    <!--<td align="center"><? echo $row[csf('image_location')];?></td>-->
                    	<td align="center"><img width="300px" height="180px" src="../../<? echo $row[csf('image_location')];?>" /></td>
                    <?
						if($i%2==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
	<?
	exit();
}
?>