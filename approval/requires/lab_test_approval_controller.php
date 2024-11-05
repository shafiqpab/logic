<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
//echo $action;die;

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//$menu_id=$_SESSION['menu_id'];

if($action=="approval_setupCheck")
{
	$ex_data=explode("__",$data);
	$approval_setup=is_duplicate_field( "page_id", "electronic_approval_setup", "company_id='$ex_data[0]' and page_id='$ex_data[1]' and is_deleted=0" );
	echo $approval_setup;
	exit();
}

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));  
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name); 
	$cbo_booking_type = str_replace("'","",$cbo_booking_type);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$booking_year=str_replace("'","",$cbo_booking_year);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$menu_id=str_replace("'","",$active_menu_id);
	//echo $menu_id; die;
	if($txt_alter_user_id!="")
	{
		$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$txt_alter_user_id' AND valid = 1"); 
		foreach($log_sql as $r_log)
		{
			if($r_log[csf('is_data_level_secured')]==1)
			{
				if($r_log[csf('buyer_id')]!="") $buyer_id_cond3=" and c.buyer_name in (".$r_log[csf('buyer_id')].")"; else $buyer_id_cond3="";
			}
			else $buyer_id_cond3="";
		}
		
		$user_id=$txt_alter_user_id;
	}
	else
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond3=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else  $buyer_id_cond3="";
		}
		else $buyer_id_cond3="";
	}
	
	$booking_year_cond="";
	if ($booking_year!=0)  
	{
		if($db_type==2) $booking_year_cond=" and to_char(a.insert_date,'YYYY')='".trim($booking_year)."' ";
		else if($db_type==0) $booking_year_cond=" and YEAR(a.insert_date)='".trim($booking_year)."' ";
	}
	
	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.wo_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.wo_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.wo_date=$txt_date";
		else $date_cond='';
	}
	
	//add booking no search 
	$booking_cond="";
	if($txt_booking_no!="") $booking_cond=" and a.labtest_prefix_num='$txt_booking_no'";


	
	
	$approval_type=str_replace("'","",$cbo_approval_type);
		
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and  is_deleted=0");
	
	$buyer_ids_array=array();
	$buyerData=sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2 order by sequence_no"); $seqn=1;
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
		
		$buyer_ids=explode(",",$user_buyer[$row[csf('user_id')]]);
		$user_bu_seq[$row[csf('user_id')]]=$row[csf('sequence_no')];
		if($row[csf('buyer_id')])
		{
			$buyer_ids=explode(",",$row[csf('buyer_id')]);
			foreach($buyer_ids as $buyer_id)
			{
				$user_buyer_app_data[$buyer_id]['user_id']=$row[csf('user_id')];
				$user_buyer_app_data[$buyer_id]['sequence_no']=$row[csf('sequence_no')];
				$seq=1;
				if($user_buyer_check[$row[csf('user_id')]][$buyer_id]=="")
				{
					$user_buyer_check[$row[csf('user_id')]][$buyer_id]=$buyer_id;
					$user_wise_buyer[$row[csf('user_id')]]["seq"]=$seq;
				}
				else
				{
					$seq++;
					$user_wise_buyer[$row[csf('user_id')]]["seq"]=$row[csf('sequence_no')];	
				}
			}
		}
		else
		{
			$user_wise_buyer[$row[csf('user_id')]]["seq"]=$row[csf('sequence_no')];	
			$all_prev_seq[$row[csf('user_id')]]=$row[csf('sequence_no')];
		}
	}
	//echo $user_sequence_no."= $min_sequence_no".test;//die;
	//echo 'DDDD';
	//print_r($buyer_ids_array);die;
	if($user_sequence_no=="")
	{
		echo "<strong style='color:#F00; font-size:16px; font-weight:bold'>You Have No Authority To Sign Lab Test WO.</strong>";
		die;
	}
	
	if($db_type==0) $group_concat="group_concat(c.buyer_name) buyer_id";
	else $group_concat="listagg(c.buyer_name,',') within group (order by c.buyer_name) as buyer_id";
	
	echo $buyer_ids=$buyer_ids_array[$user_id]['u'];
	if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and c.buyer_name in($buyer_ids)";
	
	$sql_buyer= "SELECT a.id, a.labtest_no, c.buyer_name from wo_labtest_mst a, wo_labtest_dtls b, wo_po_details_master c where a.id=b.mst_id and b.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name $buyer_id_cond2 $buyer_id_cond3 $booking_year_cond";
	$sql_job=sql_select($sql_buyer); $woBuyerArr=array(); $woids='';
	foreach($sql_job as $jrow)
	{
		$woBuyerArr[$jrow[csf("id")]].=$jrow[csf("buyer_name")].',';
		$woids.=$jrow[csf("id")].',';
	}
	unset($sql_job);
	$wo_id_cond = ''; $tot_rows=0;
	if ($woids!='')
    {
        $woIds = array_flip(array_flip(explode(',', rtrim($woids,','))));
		$tot_rows=count($woIds);
        if($db_type==2 && $tot_rows>1000)
        {
            $wo_id_cond = ' and (';
            $woNoArr = array_chunk($woIds,999);
            foreach($woNoArr as $ids)
            {
                $ids = implode(',',$ids);
                $wo_id_cond .= " a.id in($ids) or ";
            }
            $wo_id_cond = rtrim($wo_id_cond,'or ');
            $wo_id_cond .= ')';
        }
        else
        {
            $woIds = implode(',', $woIds);
            $wo_id_cond=" and a.id in ($woIds)";
        }
    }
	
	if($previous_approved==1 && $approval_type==1)	//approval process with prevous approve start
	{
		$sequence_no_cond=" and b.sequence_no<'$user_sequence_no'";
		if($cbo_booking_type==1) //With Order
		{
		   $sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, b.id as approval_id,a.currency
		   from wo_labtest_mst a, approval_history b where a.id=b.mst_id and a.entry_form=79 and a.company_id=$company_name and b.current_approval_status=1 and a.is_approved in(1,3) and a.ready_to_approved=1 $wo_id_cond $sequence_no_cond $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date,a.currency order by a.id desc";
		}
		else
		{
			 $sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, b.id as approval_id,a.currency
		   from wo_labtest_mst a, approval_history b where a.id=b.mst_id and a.entry_form=179 and a.company_id=$company_name and b.current_approval_status=1 and a.is_approved in(1,3) and a.ready_to_approved=1 $sequence_no_cond $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date,a.currency order by a.id desc";	
		}
	}
	else if($approval_type==0) //Un-approved
	{
	  	if($db_type==0)
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
		}
		else
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and buyer_id is null and bypass=2 and is_deleted=0","seq");
		}
	  
		//echo $approval_type.'='.$user_sequence_no.'='.$min_sequence_no.'='.$sequence_no;//die;
		if($user_sequence_no==$min_sequence_no) // first user
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") { $buyer_id_cond2=""; $buyer_id_condnon2="";}
			else {$buyer_id_cond2=" and c.buyer_name in($buyer_ids)"; $buyer_id_condnon2=" and a.buyer_id in($buyer_ids)";}
			
			if($cbo_booking_type==1) //With Order
			{
				$sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 1 as type,a.currency
					from wo_labtest_mst a where  a.company_id=$company_name and a.is_approved in(0,2) and a.ready_to_approved=1 $wo_id_cond  $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date,a.currency order by a.id desc";
			}
			else
			{
				$sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 1 as type,a.currency
					from wo_labtest_mst a where a.entry_form=179 and a.company_id=$company_name and a.is_approved in(0,2) and a.ready_to_approved=1 $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date,a.currency order by a.id desc";
			}
		}
		else if($sequence_no=="") // Next user // bypass if previous user Yes 
		{
			// echo "bypass yes";die;
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") {$buyer_id_cond2=""; $buyer_id_condnon2="";} 
			else {$buyer_id_cond2=" and c.buyer_name in($buyer_ids)"; $buyer_id_condnon2=" and a.buyer_id in($buyer_ids)";}
			
			if($db_type==0)
			{
				$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
				
				$sequence_no_by_no=chop($sequence_no_by_no,',');
				$sequence_no_by_yes=chop($sequence_no_by_yes,',');
				
				if($sequence_no_by_yes=="") $sequence_no_by_yes=0;
				if($sequence_no_by_no=="") $sequence_no_by_no=0;
				
				$booking_id='';
				$booking_id_sql="select distinct (b.mst_id) as booking_id from wo_labtest_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_name and b.sequence_no in ($sequence_no_by_no) and b.entry_form=42 and b.current_approval_status=1";
				//echo $booking_id_sql;die;
				$bResult=sql_select($booking_id_sql);
				foreach($bResult as $bRow)
				{
					$booking_id.=$bRow[csf('booking_id')].",";
				}
				
				$booking_id=chop($booking_id,',');
				
				$booking_id_app_byuser=return_field_value("GROUP_CONCAT(mst_id, ',') as booking_id","wo_labtest_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=42 and b.current_approval_status=1","booking_id");
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
					if($cbo_booking_type==1) //With Order
					{
						$sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 1 as type,a.currency
							from wo_labtest_mst a where a.entry_form=79 and a.company_id=$company_name and a.is_approved in(0,2) and a.ready_to_approved=1 $wo_id_cond $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date ,a.currency
							union all
							select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 1 as type,a.currency
							from wo_labtest_mst a where a.entry_form=79 and a.company_id=$company_name and a.is_approved in(0,2,3) and a.ready_to_approved=1 and a.id in($booking_id) $wo_id_cond $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date ,a.currency
							order by id desc";
					}
					else
					{
						$sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 1 as type,a.currency
							from wo_labtest_mst a where a.entry_form=179 and a.company_id=$company_name and a.is_approved in(0,2) and a.ready_to_approved=1 $wo_id_cond $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date ,a.currency
							union all
							select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 1 as type,a.currency
							from wo_labtest_mst a where a.entry_form=179 and a.company_id=$company_name and a.is_approved in(0,2,3) and a.ready_to_approved=1 and a.id in($booking_id) $wo_id_cond $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date ,a.currency
							order by id desc";
					}
				}
				else 
				{
					if($cbo_booking_type==1) //With Order
					{
						$sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 1 as type,a.currency
							from wo_labtest_mst a where a.entry_form=79 and a.company_id=$company_name and a.is_approved in(0,2,3) and a.ready_to_approved=1 $wo_id_cond $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date,a.currency order by a.id desc";
					}
					else
					{
						$sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 1 as type,a.currency
							from wo_labtest_mst a where a.entry_form=179 and a.company_id=$company_name and a.is_approved in(0,2) and a.ready_to_approved=1 $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date,a.currency order by a.id desc";
					}
				}//echo $sql;	
			}
			else if($db_type==2)
			{
				$seqSql="select sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
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
				
				$booking_id='';
				$booking_id_sql="select distinct (mst_id) as booking_id from wo_labtest_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_name and b.sequence_no in ($sequence_no_by_no) and b.entry_form=42 and b.current_approval_status=1 $seqCond
				union
				select distinct (mst_id) as booking_id from wo_labtest_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_name and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=42 and b.current_approval_status=1";
				//echo $booking_id_sql;die;
				$bResult=sql_select($booking_id_sql);
				foreach($bResult as $bRow)
				{
					$booking_id.=$bRow[csf('booking_id')].",";
				}
				
				$booking_id=chop($booking_id,',');
				
				$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_labtest_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=42 and b.current_approval_status=1","booking_id");
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

				$sequence_no_cond=" and b.sequence_no in ($user_sequence_no)";
				
				if($cbo_booking_type==1) //With Order
				{
					$sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 1 as type,a.currency
						from wo_labtest_mst a where a.entry_form=79 and a.company_id=$company_name and a.is_approved in(0,2) and a.ready_to_approved=1 $wo_id_cond $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date,a.currency";
					if($booking_id!="")
					{
						$sql.=" union all
						select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 1 as type,a.currency
						from wo_labtest_mst a where a.entry_form=79 and a.company_id=$company_name and a.is_approved in(3) and a.ready_to_approved=1 $booking_id_cond $wo_id_cond $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date,a.currency 
						order by id desc";
					}
				}
				else
				{
					$sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 2 as type,a.currency
						from wo_labtest_mst a where a.entry_form=179 and a.company_id=$company_name and a.is_approved in(0,2) and a.ready_to_approved=1 $wo_id_cond $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date,a.currency";
					if($booking_id!="")
					{
						$sql.="union all
						select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, 2 as type,a.currency
						from wo_labtest_mst a where a.entry_form=179 and a.company_id=$company_name and a.is_approved in(3) and a.ready_to_approved=1 $booking_id_cond $wo_id_cond $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date,a.currency 
						order by id desc";
					}
				}
		}
		}
		else // bypass No // bypass if previous user No
		{
			if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
			else
			{
				if($db_type==0)
				{
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_id","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and  is_deleted=0","sequence_id");
			   	}
				if($db_type==2)
				{
					$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_id","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and  is_deleted=0","sequence_id");
			   }
			}
			
			if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no in($sequence_no)";
			//if($sequence_no_by_pass=="") $sequence_no_cond=" and d.sequence_no in ($min_sequence_no)";
			else $sequence_no_cond=" and (b.sequence_no='$sequence_no' or b.sequence_no in ($sequence_no_by_pass))";
			if($cbo_booking_type==1) //With Order
			{
				$sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, b.id as approval_id
					from wo_labtest_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=42 and a.company_id=$company_name and a.is_approved in(3) and a.ready_to_approved=1 and current_approval_status=1 $sequence_no_cond $wo_id_cond $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, b.id order by a.id desc";
			}
			else
			{
				$sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, b.id as approval_id,a.currency
					from wo_labtest_mst a, approval_history b where a.id=b.mst_id and a.entry_form=179  and b.entry_form=42 and a.company_id=$company_name and a.is_approved in(3) and a.ready_to_approved=1 and current_approval_status=1 $sequence_no_cond $wo_id_cond $date_cond $booking_cond $booking_year_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, b.id,a.currency order by a.id desc";
			}
		}
	}
	else // approval process start
	{
		$sequence_no_cond=" and b.sequence_no in ($user_sequence_no)";
		
		if($cbo_booking_type==1) //With Order
		{
			$sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, b.id as approval_id,a.currency
					from wo_labtest_mst a, approval_history b where a.id=b.mst_id and b.entry_form=42 and a.company_id=$company_name and a.is_approved in(1,3) and a.ready_to_approved=1 and current_approval_status=1 $wo_id_cond $date_cond $booking_cond $booking_year_cond $sequence_no_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, b.id,a.currency order by a.id desc";
		}
		else
		{
			 $sql="select a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, b.id as approval_id,a.currency
					from wo_labtest_mst a, approval_history b where a.id=b.mst_id and a.entry_form=179 and b.entry_form=42 and a.company_id=$company_name and a.is_approved in(1,3) and a.ready_to_approved=1 and current_approval_status=1 $wo_id_cond $date_cond $booking_cond $booking_year_cond $sequence_no_cond group by a.id, a.labtest_prefix_num, a.labtest_no, a.supplier_id, a.wo_date, b.id,a.currency order by a.id desc";
		}
	}
	//echo  $sql;
	?>
    <script>
	function openmypage_app_instrac(wo_id,app_type,i)
	{
		var txt_appv_instra = $("#txt_appv_instra_"+i).val();	
		var approval_id = $("#approval_id_"+i).val();
		
		var data=wo_id+"_"+app_type+"_"+txt_appv_instra+"_"+approval_id;
		
		var title = 'Approval Instruction';	
		var page_link = 'requires/lab_test_approval_controller.php?data='+data+'&action=appinstra_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
			
			$('#txt_appv_instra_'+i).val(appv_cause.value);
		}
	}
	
	function openmypage_app_cause(wo_id,app_type,i)
	{
		var txt_appv_cause = $("#txt_appv_cause_"+i).val();	
		var approval_id = $("#approval_id_"+i).val();
		
		var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
		
		var title = 'Approval Cause Info';	
		var page_link = 'requires/lab_test_approval_controller.php?data='+data+'&action=appcause_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
			$('#txt_appv_cause_'+i).val(appv_cause.value);
		}
	}
	
	function openmypage_unapp_request(wo_id,app_type,i)
	{
		var data=wo_id;
		
		var title = 'Un Approval Request';	
		var page_link = 'requires/lab_test_approval_controller.php?data='+data+'&action=unappcause_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
			
			$('#txt_unappv_req_'+i).val(unappv_request.value);
		}
	}
	function print_lab_test(company_name,labtest_system_id,currency,labtest_work_order_date)
	{
		print_report( company_name+'*'+labtest_system_id+'*'+currency+'*'+labtest_work_order_date+'*'+'Lab Test Work Order', "show_trim_booking_report_new",
		"../order/woven_order/requires/labtest_work_order_controller" ) ;
		return;
	}
	</script>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:900px; margin-top:10px">
        <legend>Lab Test WO Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" >
                <thead>
                	<th width="50">&nbsp;</th>
                    <th width="40">SL</th>
                    <th width="130">WO No</th>
                    <th width="80">Type</th>
                    <th width="100">WO Date</th>
                    <th width="125">Buyer</th>
                    <th width="160">Supplier</th>
                    <?
					if($approval_type==0) echo "<th width='80'>Appv Instra</th>";
					if($approval_type==1) echo "<th width='80'>Un-appv request</th>"; 
					?>
                    <th><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
                    
                </thead>
            </table>
            <div style="width:900px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1;// echo $sql;
                            $nameArray=sql_select($sql);
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$value='';
								if($approval_type==0) $value=$row[csf('id')]; else $value=$row[csf('id')]."**".$row[csf('approval_id')]; 
								
								$value2=$row[csf('id')];
								
								//echo $row[csf('booking_type')]."<br/>";//die;
								//if($row[csf('booking_type')]==4) $booking_type="Sample";
								if($cbo_booking_type==1) //With Order
								{
									$booking_type="Order"; 
									
									$buyer_string="";
									$exbuyerid=explode(",",$woBuyerArr[$row[csf('id')]]);
									foreach($exbuyerid as $buyerId)
									{
										$buyer_string.=$buyer_arr[$buyerId].",";
										//echo $buyerId;
									} 
								}
								else
								{
									$booking_type="None Order";
									$buyer_string="";
								}
								
								$buyer_string=implode(",",array_filter(array_unique(explode(",",$buyer_string))));
								
								$supplierName="";
								$supplierName=$supplier_arr[$row[csf('supplier_id')]];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('labtest_no')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                        <input id="<? echo strtoupper($row[csf('labtest_no')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
                                    
                                    </td>   
									<td width="40" align="center"><? echo $i; ?></td>
                                    
                                    
									<td width="130">
										 
										<a href="#" onclick="print_lab_test(<?=$cbo_company_name?>,<?=$row[csf('id')]?>,<?=$row[csf('currency')]?>,<?=change_date_format($row[csf('wo_date')])?>)">
											<?=$row[csf('labtest_no')];?>
												
										</a>
									</td>
                                    <td width="80" align="center"><p><? echo $booking_type; ?></p></td>
									<td width="100" align="center"><? if($row[csf('wo_date')]!="0000-00-00") echo change_date_format($row[csf('wo_date')]); ?>&nbsp;</td>

                                    <td width="125" style="word-break:break-all"><?=$buyer_string; ?>&nbsp;</td>
									<td width="160" style="word-break:break-all"><?=$supplierName; ?>&nbsp;</td>
                                      <?
										if($approval_type==0)echo "<td align='center' width='80'>
                                        		<Input name='txt_appv_instra[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_appv_instra_".$i."' style='width:70px' maxlength='50' onClick='openmypage_app_instrac(".$value2.",".$approval_type.",".$i.")'></td>";
											if($approval_type==1)echo "<td align='center' width='80'>
                                        		<Input name='txt_unappv_req[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_unappv_req_".$i."' style='width:70px' maxlength='50' onClick='openmypage_unapp_request(".$value2.",".$approval_type.",".$i.")'></td>"; 
                                        ?>
                                        <td align="center">
                                        	<Input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<? echo $i;?>" style="width:97px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<? echo $value2; ?>,<? echo $approval_type; ?>,<? echo $i;?>)">&nbsp;</td>
                                            
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="900" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
	<?
	exit();	
}

if($action=='user_popup')
{
echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
?>	

<script>

// flowing script for multy select data------------------------------------------------------------------------------start;
  function js_set_value(id)
  { 
 // alert(id)
	document.getElementById('selected_id').value=id;
	  parent.emailwindow.hide();
  }

// avobe script for multy select data------------------------------------------------------------------------------end;

</script>

<form>
        <input type="hidden" id="selected_id" name="selected_id" /> 
       <?php
        $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
		 $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
		 	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id";
			//echo $sql;
		 $arr=array (2=>$custom_designation,3=>$Department);
		 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
        
</form>
<script language="javascript" type="text/javascript">
  setFilterGrid("tbl_style_ref");
</script>
<?
exit();
}


if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//$user_id=4;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';
	$menu_id=str_replace("'","",$active_menu_id);

	// echo 'Alter user_id = '.$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);die();
	if($_REQUEST['txt_alter_user_id']!="") $user_id_approval=$_REQUEST['txt_alter_user_id']; else $user_id_approval=$user_id;
	
	$buyer_arr=return_library_array( "select id, buyer_id from wo_booking_mst where is_deleted=0 and status_active=1 and id in ($booking_ids)", "id", "buyer_id");
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and  is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and  is_deleted=0");
	
	$booking_type=str_replace("'","",$cbo_booking_type);
	//echo $booking_type;die;
	if($booking_type==1) //With Order
	{
		if($approval_type==0)
		{
			if($db_type==2) {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null";}
			else 			{$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}
			//$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
			$is_not_last_user=return_field_value("a.sequence_no","electronic_approval_setup a"," a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");
			//if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
			//echo "21**select a.sequence_no from electronic_approval_setup a where a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond".trim($is_not_last_user);die;
			$partial_approval = ""; 
			//echo "21**".$is_not_last_user; die;
			if(trim($is_not_last_user)=="")
			{
				//$credentialUserBuyersArr = [];
				$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 group by b.buyer_id");
				//echo "21**select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 group by b.buyer_id".count($sql);die;
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
				//echo "21**select b.user_id as user_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id".count($check_user_buyer);die;
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
				//print_r($credentialUserBuyersArr);die;
			}

			$response=$booking_ids;
			//$trims_type= max($trims_types);
			//echo $trims_type;die;
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status,comments, approved_by, approved_date, inserted_by, insert_date"; 
			$id=return_next_id( "id","approval_history", 1 ) ;
			      
			$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=42 group by mst_id","mst_id","approved_no");
			$approved_status_arr = return_library_array("select id, is_approved from wo_booking_mst where id in($booking_ids)","id","is_approved");
		
			$approved_no_array=array();
			$booking_ids_all=explode(",",$booking_ids);
			$booking_nos_all=explode(",",$booking_nos);
			$app_instru_all=explode(",",$appv_instras);
			$book_nos='';// echo "10**";
			for($i=0;$i<count($booking_nos_all);$i++)
			{
				$val=$booking_nos_all[$i];
				$booking_id=$booking_ids_all[$i];
				$app_instru=$app_instru_all[$i];
				
				$approved_no=$max_approved_no_arr[$booking_id];
				if($approved_no=="") $approved_no=1;
				$approved_status=$approved_status_arr[$booking_id];
				$buyer_id=$buyer_arr[$booking_id];
				
				if($approved_status==0)
				{
					$approved_no=$approved_no+1;
					$approved_no_array[$val]=$approved_no;
					if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
				}
				
				if($is_not_last_user=="")
				{
					if(in_array($buyer_id,$credentialUserBuyersArr))
					{
						$partial_approval=3;
					}
					else $partial_approval=1;
				}
				else
				{
					if(count($credentialUserBuyersArr)>0)
					{
						if(in_array($buyer_id,$credentialUserBuyersArr))
						{
							$partial_approval=3;
						}
						else $partial_approval=1;
					}
					else $partial_approval=3;
				}
				//echo $partial_approval; die;
				$booking_id_arr[]=$booking_id;
				$data_array_booking_update[$booking_id]=explode("*",($partial_approval));
	
				/*if($partial_approval==1)
				{
					$full_approve_booking_id_arr[]=$booking_id;
					$data_array_full_approve_booking_update[$booking_id]=explode("*",($user_id_approval."*'".$pc_date_time."'"));
				}*/
				
				if($data_array!="") $data_array.=",";
				$data_array.="(".$id.",42,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$app_instru.",".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')"; 
				$id=$id+1;
			}
			/*echo $data_array;
			die;*/
			$flag=1;
			if(count($approved_no_array)>0)
			{
				/*$approved_string="";
				if($db_type==0)
				{
					foreach($approved_no_array as $key=>$value)
					{
						$approved_string.=" WHEN $key THEN $value";
						$approved_string1.=" WHEN $key THEN $value";
					}
				}
				else
				{
					foreach($approved_no_array as $key=>$value)
					{
						$approved_string.=" WHEN TO_NCHAR($key) THEN '".$value."'";
						$approved_string1.=" WHEN TO_CHAR($key) THEN '".$value."'";
					}
				}
				
				$approved_string_mst="CASE booking_no ".$approved_string." END";
				$approved_string_dtls="CASE booking_no ".$approved_string." END";
				
				$sql_insert="insert into wo_labtest_mst_hst(id, approved_no, wo_id, labtest_prefix, labtest_prefix_num, labtest_no, company_id, supplier_id, wo_date, delivery_date, currency, ecchange_rate, pay_mode, address, attention, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, entry_form, ready_to_approved, vat_percent, is_approved, approved_by, approved_date ) 
					select	
					'', $approved_string_mst, id, labtest_prefix, labtest_prefix_num, labtest_no, company_id, supplier_id, wo_date, delivery_date, currency, ecchange_rate, pay_mode, address, attention, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, entry_form, ready_to_approved, vat_percent, is_approved, approved_by, approved_date from wo_booking_mst where wo_labtest_mst id in ($booking_nos)";
						
				
				$sql_insert_dtls="insert into wo_booking_dtls_hstry(id, approved_no, booking_dtls_id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
					select	
					'', $approved_string_dtls, id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_booking_dtls where booking_no in ($booking_nos)";
						
				 
					
				$rID3=execute_query($sql_insert,0);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
				} 
					
				$rID4=execute_query($sql_insert_dtls,1);
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=0; 
				} 
				
				$rID5=execute_query($sql_insert_cons_dtls,1);
				if($flag==1) 
				{
					if($rID5) $flag=1; else $flag=0; 
				} */
			}
			
			//$rID=sql_multirow_update("wo_booking_mst","is_approved",$partial_approval,"id",$booking_ids,0);
			//if($rID) $flag=1; else $flag=0;
			
			/*$rID9=1;
			if(count($full_approve_booking_id_arr)>0)
			{
	
				$field_array_full_approved_booking_update = "approved_by*approved_date";
				$rID9=execute_query(bulk_update_sql_statement( "wo_booking_mst", "id", $field_array_full_approved_booking_update, $data_array_full_approve_booking_update, $full_approve_booking_id_arr));
				if($flag==1)
				{
					if($rID9) $flag=1; else $flag=0;
				}
				//sql_multirow_update("wo_pre_cost_mst","approved_by*approved_date",$updateData,"id",$booking_ids,1);
			}*/
	
			$field_array_booking_update = "is_approved";
			//$rID=sql_multirow_update("wo_pre_cost_mst","approved",$partial_approval,"id",$booking_ids,0);
	
			$rID=execute_query(bulk_update_sql_statement( "wo_labtest_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));
	
			if($flag==1)
			{
				if($rID) $flag=1; else $flag=0;
			}
			
			
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=42 and mst_id in ($booking_ids)";
			$rIDapp=execute_query($query,1);
			if($flag==1) 
			{
				if($rIDapp) $flag=1; else $flag=0; 
			}
			
				
			$rID2=sql_insert("approval_history",$field_array,$data_array,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			}
			
			/*if($approval_ids!="")
			{
				$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
				if($flag==1) 
				{
					if($rIDapp) $flag=1; else $flag=0; 
				} 
			}*/
			
			//echo "10**".$rID.'='.$rIDapp.'='.$rID2.'='.$flag;die;
			if($flag==1) $msg='19'; else $msg='21';
		}
		else
		{
			$booking_ids_all=explode(",",$booking_ids);
			
			$booking_ids=''; $app_ids='';
			
			foreach($booking_ids_all as $value)
			{
				$data = explode('**',$value);
				$booking_id=$data[0];
				$app_id=$data[1];
				
				if($booking_ids=='') $booking_ids=$booking_id; else $booking_ids.=",".$booking_id;
				if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
			}
			/*$rID=sql_multirow_update("wo_booking_mst","is_approved",0,"id",$booking_ids,0);
			if($rID) $flag=1; else $flag=0;*/

			$rID=sql_multirow_update("wo_labtest_mst","is_approved*ready_to_approved",'2*2',"id",$booking_ids,0);
			if($rID) $flag=1; else $flag=0;
			
			$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
				
			// $data_arr=$user_id."*'".$pc_date_time."'";
			// $rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data_arr,"id",$approval_ids,1);
			$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
			$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
			
			$response=$booking_ids;
			//echo "22**".$rID.'**'.$rID2.'**'.$rID3;die;
			if($flag==1) $msg='20'; else $msg='22';
		}
	} //With Order End
	else //WithOut Order
	{
		if($approval_type==0)
		{
			$response=$booking_ids;
			
			$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
			
			// if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
			$partial_approval = "";
			if($is_not_last_user != "")
			{
				// getting login in user's buyer id
				$loginUserBuyersArr = array();
				$loginUserBuyersSQL=sql_select("select (b.buyer_id || ',' ||  a.buyer_id) as buyer_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.user_id=$user_id_approval and b.is_deleted=0 group by b.buyer_id, a.buyer_id");
				foreach ($loginUserBuyersSQL as $key => $buyerID) {
					$loginUserBuyersArr[] = $buyerID[csf('buyer_id')];
				}		

				$loginUserBuyersArr = implode(',',$loginUserBuyersArr);
				$loginUserBuyersArr = explode(',',$loginUserBuyersArr);
				$loginUserBuyersArr = array_filter($loginUserBuyersArr);
				$loginUserBuyersArr = array_unique($loginUserBuyersArr);
				// print_r($loginUserBuyersArr);die();
				
				// getting next level all user's buyer id
				$credentialUserBuyersArr = array();
				$sql = sql_select("select (b.buyer_id || ',' ||  a.buyer_id) as buyer_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 group by b.buyer_id, a.buyer_id");
				foreach ($sql as $key => $buyerID) {
					$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
				}
				$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = array_filter($credentialUserBuyersArr);
				$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
				// print_r($credentialUserBuyersArr);die();
				
				$isBuyerExist = array_intersect($loginUserBuyersArr,$credentialUserBuyersArr);
				//print_r($isBuyerExist);
				if(count($isBuyerExist) > 0)
				{
					$partial_approval=3;
				}			
				else
				{
					$partial_approval=1;
				}
				// echo $partial_approval;
				// die();
			}
			else
			{
				$partial_approval=1;
			}


			//echo $booking_ids.'azzzz';die;
			// $field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date"; 
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date"; 
			$id=return_next_id( "id","approval_history", 1 ) ;
			      
			$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=42 group by mst_id","mst_id","approved_no");
				$approved_status_arr = return_library_array("select id, is_approved from wo_non_ord_samp_booking_mst where id in($booking_ids)","id","is_approved");
			//print_r($max_approved_no_arr);
				
			/*	$rID=sql_multirow_update("wo_booking_mst","is_approved",1,"id",$booking_ids,0);
				if($rID) $flag=1; else $flag=0;
				
				if($approval_ids!="")
				{
					$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
					if($flag==1) 
					{
						if($rIDapp) $flag=1; else $flag=10; 
					} 
				}*/
			
			
			
			
			$approved_no_array=array();
			$booking_ids_all=explode(",",$booking_ids);
			$booking_nos_all=explode(",",$booking_nos);
			$book_nos='';

			for($i=0;$i<count($booking_nos_all);$i++)
			{
				$val=$booking_nos_all[$i];
				$booking_id=$booking_ids_all[$i];
				
				$approved_no=$max_approved_no_arr[$booking_id];
				$approved_status=$approved_status_arr[$booking_id];
				
				if($approved_status==0)
				{
					$approved_no=$approved_no+1;
					$approved_no_array[$val]=$approved_no;
					if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
				}
				
				if($data_array!="") $data_array.=",";
				// $data_array.="(".$id.",8,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id.",'".$pc_date_time."')"; 
				$data_array.="(".$id.",42,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
				$id=$id+1;
			}
			//echo "insert into approval_history (".$field_array.") values ".$data_array;die;
			/*$rID2=sql_insert("approval_history",$field_array,$data_array,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=20; 
			} 
			*/
			if(count($approved_no_array)>0)
			{
				$approved_string="";
				if($db_type==0)
				{
					foreach($approved_no_array as $key=>$value)
					{
						$approved_string.=" WHEN $key THEN $value";
						$approved_string1.=" WHEN $key THEN $value";
					}
				}
				else
				{
					foreach($approved_no_array as $key=>$value)
					{
						$approved_string.=" WHEN TO_NCHAR($key) THEN '".$value."'";
						$approved_string1.=" WHEN TO_CHAR($key) THEN '".$value."'";
					}
				}
				
				$approved_string_mst="CASE booking_no ".$approved_string." END";
				$approved_string_dtls="CASE booking_no ".$approved_string." END";
				
				$sql_insert="insert into wo_non_ord_samp_bk_mst_his( id, approved_no, non_ord_samp_mst_id,booking_type, is_short, booking_no_prefix,booking_no_prefix_num, booking_no, company_id,buyer_id, job_no, po_break_down_id,item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention,is_deleted, status_active, inserted_by,insert_date, updated_by, update_date,is_approved, ready_to_approved, team_leader,dealing_marchant) 
					select	
					'', $approved_string_mst,   id, booking_type, is_short,booking_no_prefix, booking_no_prefix_num, booking_no, 
	   		company_id, buyer_id, job_no,po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode,source, booking_date, delivery_date, 
	   		booking_month, booking_year, supplier_id,attention, is_deleted, status_active,inserted_by, insert_date, updated_by,update_date, is_approved, ready_to_approved,team_leader, dealing_marchant from wo_non_ord_samp_booking_mst where booking_no in ($booking_nos)";
					//echo $sql_insert;	
				/*$rID3=execute_query($sql_insert,0);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=30; 
				} 
				*/
				//echo "insert into wo_booking_mst_hstry (".$field_array.") values ".$data_array;die;
				$sql_insert_dtls="insert into wo_non_ord_samp_bk_dtls_his( id, approved_no, non_ord_samp_dtls_id,booking_no, body_part, color_type_id, 
	   		lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, fabric_color,item_size, dia_width, finish_fabric, 
	   		process_loss, grey_fabric, rate,amount, yarn_breack_down, process_loss_method,inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,style_id, style_des, sample_type, gmts_color, gmts_size, trim_group, uom, barnd_sup_ref, trim_qty,article_no, remarks, yarn_details,   body_type_id, item_qty, knitting_charge, rf_qty, bh_qty) 
					select	
					'', $approved_string_dtls, id, booking_no, body_part, color_type_id, lib_yarn_count_deter_id, construction,composition, fabric_description, gsm_weight,fabric_color, item_size, dia_width,finish_fabric, process_loss, grey_fabric,  rate, amount, yarn_breack_down,process_loss_method, inserted_by, insert_date,updated_by, update_date, status_active,is_deleted, style_id, style_des, sample_type, gmts_color, gmts_size, trim_group, uom, barnd_sup_ref, trim_qty, article_no, remarks,yarn_details, body_type_id, item_qty, knitting_charge, rf_qty, bh_qty from wo_non_ord_samp_booking_dtls where booking_no in ($booking_nos)";
						
			/*	$rID4=execute_query($sql_insert_dtls,1);
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=40; 
				} 
				*/
				//echo $sql_insert_dtls;
				/*$sql_insert_cons_dtls="insert into wo_trim_book_con_dtls_hstry(id, approved_no,wo_trim_book_con_dtl_id, wo_trim_booking_dtls_id,booking_no, job_no, po_break_down_id, color_number_id, gmts_sizes, item_color, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id) 
					select	
					'', $approved_string_dtls1, id,wo_trim_booking_dtls_id,booking_no, job_no, po_break_down_id, color_number_id, gmts_sizes, item_color, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id from wo_trim_book_con_dtls where booking_no in ($booking_nos)";*/
				
				 
					
				$rID3=execute_query($sql_insert,0);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
				} 
					
				$rID4=execute_query($sql_insert_dtls,1);
				if($flag==1) 
				{
					if($rID4) $flag=1; else $flag=0; 
				} 
				
				/*$rID5=execute_query($sql_insert_cons_dtls,1);
				if($flag==1) 
				{
					if($rID5) $flag=1; else $flag=50; 
				} */
			}
			
			$rID=sql_multirow_update("wo_labtest_mst","is_approved",1,"id",$booking_ids,0);
			if($rID) $flag=1; else $flag=0;
			
			/*if($approval_ids!="")
			{
				$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
				if($flag==1) 
				{
					if($rIDapp) $flag=1; else $flag=0; 
				} 
			}*/

			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=42 and mst_id in ($booking_ids)";
			$rIDapp=execute_query($query,1);
			if($flag==1) 
			{
				if($rIDapp) $flag=1; else $flag=0; 
			}
				
			$rID2=sql_insert("approval_history",$field_array,$data_array,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			}
			
			if($flag==1) $msg='19'; else $msg='21';
		}
		else
		{
			$booking_ids_all=explode(",",$booking_ids);
			
			$booking_ids=''; $app_ids='';
			
			foreach($booking_ids_all as $value)
			{
				$data = explode('**',$value);
				$booking_id=$data[0];
				$app_id=$data[1];
				
				if($booking_ids=='') $booking_ids=$booking_id; else $booking_ids.=",".$booking_id;
				if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
			}
			// echo $data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."*'".$app_ids.'*'.$approval_ids.'*'.$booking_ids;die;
			$rID=sql_multirow_update("wo_labtest_mst","is_approved*ready_to_approved",'2*2',"id",$booking_ids,0);
			if($rID) $flag=1; else $flag=0; 
			
			$rID2=sql_multirow_update("approval_history","current_approval_status",0,"mst_id",$booking_ids,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
				
			// $data=$user_id."*'".$pc_date_time."'";
			// $rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$app_ids,1);
			$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
			$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
			
			$response=$booking_ids;
			
			if($flag==1) $msg='20'; else $msg='22';
		}
	} //Without Order End
	
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

if($action=="show_trim_comment_report")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	//$last_update=return_field_value("is_apply_last_update","wo_booking_mst","booking_no='".trim($data)."'");
	//echo $last_update;
	
	$sql_lib_item_group_array=array();
	$sql_lib_item_group=sql_select("select id, item_name,conversion_factor,order_uom as cons_uom from lib_item_group");
	foreach($sql_lib_item_group as $row_sql_lib_item_group)
	{
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][item_name]=$row_sql_lib_item_group[csf('item_name')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][conversion_factor]=$row_sql_lib_item_group[csf('conversion_factor')];
		$sql_lib_item_group_array[$row_sql_lib_item_group[csf('id')]][cons_uom]=$row_sql_lib_item_group[csf('cons_uom')];
	}
	
	
	$sql="select job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls where booking_no='$booking_no' and status_active=1 and is_deleted=0 group by job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group";
	$exchange_rate=return_field_value("exchange_rate", " wo_booking_mst", "booking_no='".$booking_no."'");
	
?>
<body>
<div>
<table width="990"   cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
 <thead>
   <tr align="center">
    <th colspan="13"><b>Trim Comments</b></th>
    </tr>
    
    <tr>
    <th width="30" rowspan="2">Sl</th>
     <th width="100" rowspan="2">Item Name</th>
    <th width="120" rowspan="2">Po NO</th>
    <th width="70" rowspan="2">Ship Date</th>  
    <th width="80" rowspan="2">As Merketing</th>
    <th width="70" rowspan="2">As Budget</th>
    <th width="70" rowspan="2">Mn.Book Val</th>
    <th width="70" rowspan="2">Sht.Book Val</th>
    <th width="70" rowspan="2">Smp.Book Val</th>
    <th  width="70" rowspan="2">Tot.Book Val</th>
    <th colspan="2">Balance</th>
    <th width="" rowspan="2">Comments On Budget</th>
    </tr>
    <tr>
    <th width="70">As Mkt.</th>
    <th width="70">As Budget</th>
    </tr>
     </thead>
</table>
<?

	 $po_qty_arr=array(); $pre_cost_data_arr=array();$pre_cu_data_arr=array();$trim_qty_data_arr=array();$trim_sam_qty_data_arr=array();$trim_price_cost_arr=array();	
	 $fab_sql=sql_select("select  a.po_break_down_id  as po_id,a.trim_group,
	sum(case a.is_short when 2 then a.amount else 0 end) as main_amount,
	sum(case a.is_short when 1 then a.amount else 0 end) as short_amount
	from    wo_booking_dtls a, wo_trim_book_con_dtls b   where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no 
   and a.booking_type=2
   and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id,a.trim_group  ");
		foreach($fab_sql as $row_data)
		{
		$trim_qty_data_arr[$row_data[csf('po_id')]][$row_data[csf('trim_group')]]['main_amount']=$row_data[csf('main_amount')];
		$trim_qty_data_arr[$row_data[csf('po_id')]][$row_data[csf('trim_group')]]['short_amount']=$row_data[csf('short_amount')];
		}  //var_dump($trim_qty_data_arr);
	 $sam_sql=sql_select("select d.po_break_down_id  as po_id,d.trim_group,
	sum(case c.is_short when 2 then d.amount else 0 end) as sam_with_amount,
	sum(case c.is_short when 1 then d.amount else 0 end) as sam_without_amount
	from   wo_booking_mst c,wo_booking_dtls d where c.booking_no=d.booking_no and c.booking_type=5  and c.booking_no='$booking_no' and   c.company_id='$company'  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by d.po_break_down_id ,d.trim_group ");
		foreach($sam_sql as $row_data)
		{
		$trim_sam_qty_data_arr[$row_data[csf('po_id')]][$row_data[csf('trim_group')]]['sam_with']=$row_data[csf('sam_with_amount')];
		$trim_sam_qty_data_arr[$row_data[csf('po_id')]][$row_data[csf('trim_group')]]['sam_without']=$row_data[csf('sam_without_amount')];
		} 
	 
	 $sql_po_qty=sql_select("select b.id as po_id,b.pub_shipment_date,sum(c.order_quantity) as order_quantity,(sum(c.order_quantity)/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id   and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.id,a.total_set_qnty,b.pub_shipment_date");
		foreach( $sql_po_qty as $row)
		{
			$po_qty_arr[$row[csf("po_id")]]['order_quantity']=$row[csf("order_quantity_set")];
			$po_qty_arr[$row[csf("po_id")]]['pub_shipment_date']=$row[csf("pub_shipment_date")];
		}
		
		$sql_cons_data=sql_select("select a.id as pre_cost_fabric_cost_dtls_id,b.po_break_down_id as po_id,a.rate,b.cons from wo_pre_cost_trim_cost_dtls a , wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id   and a.is_deleted=0  and a.status_active=1");
						 
		foreach($sql_cons_data as $row)
		{
			$pre_cost_data_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("po_id")]]['cons']=$row[csf("cons")];
			$pre_cost_data_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("po_id")]]['rate']=$row[csf("rate")];
		}
			
			$sql_cu_woq=sql_select("select sum(amount) as amount,po_break_down_id as po_id,pre_cost_fabric_cost_dtls_id  from wo_booking_dtls where  booking_type=2 and status_active=1 and is_deleted=0");
			
		foreach($sql_cu_woq as $row)
		{
			$pre_cu_data_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("po_id")]]['amount']=$row[csf("amount")];
			
		}	
		
		$sql_price_trim=sql_select("select quotation_id,trim_group,sum(amount) as amount  from wo_pri_quo_trim_cost_dtls where   status_active=1 and is_deleted=0 group by quotation_id,trim_group");
		
		foreach($sql_price_trim as $row)
		{
			$trim_price_cost_arr[$row[csf("quotation_id")]][$row[csf("trim_group")]]['amount']=$row[csf("amount")];
			
		}				
	//$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'", "gmts_item_id", "set_item_ratio");
	$total_pre_cost=0;
	$total_booking_qnty_main=0;
	$total_booking_qnty_short=0;
	$total_booking_qnty_sample=0;
	$total_tot_bok_qty=0;
	$tot_balance=0;
					

?>
<div style="width:1010px; max-height:400px; overflow-y:scroll" id="scroll_body">
<table width="990"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
<?
$k=0;
$total_amount=0;$total_booking_qnty_main=0;$total_booking_qnty_short=0;$pre_cost=0;$total_booking_qnty_sample=0;$total_booking_qnty_sample=0;$total_tot_bok_qty=0
;$tot_mkt_balance=0;$tot_pre_cost=0;
$nameArray=sql_select( $sql );
foreach ($nameArray as $selectResult)
	{
		 if ($k%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			
		 $quotation_id = return_field_value(" quotation_id as quotation_id"," wo_po_details_master ","job_no='".$selectResult[csf('job_no')]."'","quotation_id");  
		$tot_mkt_cost  = $trim_price_cost_arr[$quotation_id][$selectResult[csf("trim_group")]]['amount'];
		//return_field_value(" sum(b.fabric_cost) as mkt_cost","wo_price_quotation a,wo_price_quotation_costing_mst b"," a.id=b.quotation_id and a.id='".$quotation_id."'","mkt_cost");
	// $tot_mkt_cost;
			 $costing_per=return_field_value("costing_per", "wo_pre_cost_mst", "job_no='".$selectResult[csf('job_no')]."'");
			if($costing_per==1)
			{
				$costing_per_qty=12;
			}
			else if($costing_per==2)
			{
				$costing_per_qty=1;
			}
			else if($costing_per==3)
			{
				$costing_per_qty=24;
			}
			else if($costing_per==4)
			{
				$costing_per_qty=36;
			}
			else if($costing_per==5)
			{
				$costing_per_qty=48;
			} 
			//$selectResult[csf('trim_group')]
			$main_fab_cost=$trim_qty_data_arr[$selectResult[csf('po_break_down_id')]][$selectResult[csf('trim_group')]]['main_amount'];
			$short_fab_cost=$trim_qty_data_arr[$selectResult[csf('po_break_down_id')]][$selectResult[csf('trim_group')]]['short_amount'];
			$sam_trim_with=$trim_sam_qty_data_arr[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['sam_with'];
			$sam_trim_without=$trim_sam_qty_data_arr[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['sam_without'];
			$po_qty=$po_qty_arr[$selectResult[csf('po_break_down_id')]]['order_quantity'];
			$po_ship_date=$po_qty_arr[$selectResult[csf('po_break_down_id')]]['pub_shipment_date'];
			$pre_rate=$pre_cost_data_arr[$selectResult[csf("pre_cost_fabric_cost_dtls_id")]][$selectResult[csf("po_break_down_id")]]['rate'];
			$pre_cons=$pre_cost_data_arr[$selectResult[csf("pre_cost_fabric_cost_dtls_id")]][$selectResult[csf("po_break_down_id")]]['cons'];
			$pre_req_qnty=def_number_format(($pre_cons*($po_qty/$costing_per_qty))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
			$pre_amount=$pre_req_qnty*$pre_rate;
			 $tot_grey_req_as_price_cost=($tot_mkt_cost/$costing_per_qty)*$po_qty;
			 
	$k++;
	
	?>
<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $k; ?>">
    <td width="30"> <? echo $k; ?> </td> 
    <td width="100"><p><? echo 	$trim_group[$selectResult[csf('trim_group')]];?></p>  </td>
    <td width="120"><p><? echo $po_number_arr[$selectResult[csf('po_break_down_id')]];?></p>  </td>
    <td width="70" align="right"><? echo change_date_format($po_ship_date,"dd-mm-yyyy",'-'); ?> </td>
    <td width="80" align="right"><? $total_price_mkt_cost+=$tot_grey_req_as_price_cost; echo number_format($tot_grey_req_as_price_cost,2);?> </td>
    <td width="70" align="right"><?  echo number_format($pre_amount,2); $pre_cost+=$pre_amount;?> </td>
    <td width="70" align="right"><? echo number_format($main_fab_cost,2); $total_booking_qnty_main+=$main_fab_cost;?> </td>
    <td width="70" align="right"> <? echo number_format($short_fab_cost,2); $total_booking_qnty_short+=$short_fab_cost;?></td>
    <td width="70" align="right"><? echo number_format($sam_trim_with,2); $total_booking_qnty_sample+=$sam_trim_with;?></td>
    <td width="70" align="right">	<? $tot_bok_qty=$main_fab_qty+$short_fab_qty+$total_booking_qnty_sample; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?> </td>
    <td width="70" align="right"> <? $balance_mkt= def_number_format($tot_grey_req_as_price_cost-$tot_bok_qty,2,""); echo number_format($balance_mkt,2); $tot_mkt_balance+= $balance_mkt; ?></td>
    <td width="70" align="right"> <? $total_pre_cost=$pre_amount-$tot_bok_qty;$tot_pre_cost+=$total_pre_cost; echo number_format($total_pre_cost,2);?></td>
    <td width="">
     <? 
	if( $total_pre_cost>0)
		{
		echo "Less Booking";
		}
	else if ($total_pre_cost<0) 
		{
		echo "Over Booking";
		} 
	else if ($pre_amount==$tot_bok_qty) 
		{
			echo "As Per";
		} 
	else
		{
		echo "";
		}
	?></td>
</tr>
<?
	}
?>
<tfoot>
    <tr>
        <td colspan="4">Total:</td>
        <td align="right"><? echo number_format($total_price_mkt_cost,2); ?></td>
        <td align="right"><? echo number_format($pre_cost,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
        <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
        <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
        <td align="right"><? echo number_format($tot_mkt_balance,2); ?></td>
        <td align="right"><? echo number_format($tot_pre_cost,2); ?></td>
    </tr>
    </tfoot>
</table>
</div>
</div>
 <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</body>
<?	
	
	exit();	

	
}
if ($action=="appcause_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$data_all=explode('_',$data);
	$wo_id=$data_all[0];
	$app_type=$data_all[1];
	$app_cause=$data_all[2];
	$approval_id=$data_all[3];
	
	if($app_cause=="")
	{	
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=8 and user_id='$user_id' and booking_id='$wo_id' and approval_type='$app_type' and status_active=1 and is_deleted=0";				
		$nameArray_cause=sql_select($sql_cause);
		foreach($nameArray_cause as $row)
		{
			$app_cause=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
		}
	}
	
	?>
    <script>
	
		$( document ).ready(function() {
			document.getElementById("appv_cause").value='<? echo $app_cause; ?>';
		});
		
		var permission='<? echo $permission; ?>';
		
		function fnc_appv_entry(operation)
		{
			var appv_cause = $('#appv_cause').val();
			
			if (form_validation('appv_cause','Approval Cause')==false)
			{
				if (appv_cause=='')
				{
					alert("Please write cause.");
				}
				return;
			}
			else
			{
				
				var data="action=save_update_delete_appv_cause&operation="+operation+get_submitted_data_string('appv_cause*wo_id*appv_type*page_id*user_id*approval_id',"../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","lab_test_approval_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}
		
		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				//release_freezing();	
				//alert(http.responseText);return;
			
				var reponse=trim(http.responseText).split('**');	
				show_msg(reponse[0]);
				
				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();
				
				generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
			}
		}
		
		function fnc_close()
		{	
			appv_cause= $("#appv_cause").val();
			document.getElementById('hidden_appv_cause').value=appv_cause;
			parent.emailwindow.hide();
		}
		
		function generate_worder_mail(woid,mail,appvtype,user)
		{
			var data="action=app_cause_mail&woid="+woid+'&mail='+mail+'&appvtype='+appvtype+'&user='+user;
			//alert (data);return;
			freeze_window(6);
			http.open("POST","lab_test_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_appv_mail_Reply_info;
		}
		
		function fnc_appv_mail_Reply_info()
		{
			if(http.readyState == 4) 
			{
				var response=trim(http.responseText).split('**');
				release_freezing();
			}
		}
			
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <Input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <Input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                
                <tr>
                    <td align="center" class="button_container">
                        <? 
						//print_r ($id_up_all);
                            if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">
                        
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>	  
                </tr>
            </table>
            </fieldset>
            </form>
        </div> 
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}
if ($action=="unappcause_popup")
{
	echo load_html_head_contents("Un Approval Request", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$wo_id=$data;
	$sql_req="select approval_cause from fabric_booking_approval_cause where entry_form=8 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0";				
	$nameArray_req=sql_select($sql_req);
	foreach($nameArray_req as $row)
	{
		$unappv_req=$row[csf('approval_cause')];
	}
	?>
    <script>
	
		//var permission='<?// echo $permission; ?>';
		
		$( document ).ready(function() {
			document.getElementById("unappv_req").value='<? echo $unappv_req; ?>';
		});
		
		
		function fnc_close()
		{	
			unappv_request= $("#unappv_req").val();
			document.getElementById('hidden_unappv_request').value=unappv_request;
			parent.emailwindow.hide();
		}
			
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="unappv_req" id="unappv_req" readonly class="text_area" style="width:430px; height:100px;"></textarea>
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                <tr>
                    <td align="center">
                    	<input type="hidden" name="hidden_unappv_request" id="hidden_unappv_request" class="text_boxes /">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>	  
                </tr>
            </table>
            </fieldset>
            </form>
        </div> 
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
<?
	exit();
}
if ($action=="appinstra_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$data_all=explode('_',$data);
	$wo_id=$data_all[0];
	$app_type=$data_all[1];
	$app_cause=$data_all[2];
	$approval_id=$data_all[3];
	?>
    <script>
	
		$( document ).ready(function() {
			document.getElementById("appv_cause").value='<? echo $app_cause; ?>';
		});
		
		var permission='<? echo $permission; ?>';
		
		function fnc_close()
		{	
			appv_cause= $("#appv_cause").val();
			
			document.getElementById('hidden_appv_cause').value=appv_cause;
			
			parent.emailwindow.hide();
		}
		
		
			
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <Input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <Input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                
                <tr>
                    <td align="center" class="button_container">
                        <? 
						//print_r ($id_up_all);
                            /*if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_instru_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_instru_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }*/
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes"/>
                        
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>	  
                </tr>
            </table>
            </fieldset>
            </form>
        </div> 
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="save_update_delete_appv_cause")
{
	
	//$approval_id
	$approval_type=str_replace("'","",$appv_type);
	
	
	if($approval_type==0)
	{
			
   		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 

		if ($operation==0)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=8 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=8 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
			
			//echo "shajjad_".$approved_no_history.'_'.$approved_no_cause; die;
			
			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;
				
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",8,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				//echo $rID; die;
				
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con); 
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;	
			}
			else if($approved_no_history=="" && $approved_no_cause!="")
			{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=8 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
				
				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*8*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
				
				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
				
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT"); 
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con);  
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
					}
					else
					{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
			else if($approved_no_history!="" && $approved_no_cause!="")
			{
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=8 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=8 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
				
				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",8,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
					//echo $rID; die;
					
					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							mysql_query("ROLLBACK"); 
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con); 
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
						}
						else
						{
							oci_rollback($con); 
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
				else if($max_appv_no_his==$max_appv_no_cause)
				{	
					$con = connect();
					if($db_type==0)
					{
						mysql_query("BEGIN");
					}
					
					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=8 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
					
					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*8*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
					
					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
					
					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT"); 
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
						}
						else
						{
							mysql_query("ROLLBACK"); 
							echo "10**".$rID;
						}
					}
					else if($db_type==2 || $db_type==1 )
					{
						if($rID )
						{
							oci_commit($con); 
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
						}
						else
						{
							oci_rollback($con); 
							echo "10**".$rID;
						}
					}
					disconnect($con);
					die;
				}
			}
			
		}
	
		if ($operation==1)  // Update Here
		{	
			
		}
	
	}//type=0
	if($approval_type==1)
	{
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 

		if ($operation==0)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
		
			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=8 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
			
			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=8 and mst_id=$wo_id and approved_by=$user_id");
			
			if($unapproved_cause_id=="")
			{
			
				//echo "10**"."=shajjad_".$unapproved_cause_id; die;
		
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_history_id,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",8,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,0);
				//echo $rID; die;
		
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con); 
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
					}
					else
					{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
			
				disconnect($con);
				die;
			}
			else
			{
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=8 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
				
				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_history_id*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*8*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
				
				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",0);
				
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT"); 
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID )
					{
						oci_commit($con); 
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id); 
					}
					else
					{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
		}
		
	}//type=1

	
}
if ( $action=="app_cause_mail" )
{
	//echo $woid.'_'.$mail.'_'.$appvtype; die;
	ob_start();
	?>
    
        <table width="800" cellpadding="0" cellspacing="0" border="1">
            <tr>
                <td valign="top" align="center"><strong><font size="+2">Subject : Fabric Booking &nbsp;<?  if($appvtype==0) echo "Approval Request"; else echo "Un-Approval Request"; ?>&nbsp;Refused</font></strong></td>
            </tr>
            <tr>
                <td valign="top">
                    Dear Mr. <?   
								$to="";
								
								$sql ="SELECT c.team_member_name FROM wo_booking_mst a,wo_po_details_master b,lib_mkt_team_member_info c where b.job_no=a.job_no and b.dealing_marchant=c.id and a.id=$woid";
								$result=sql_select($sql);
								foreach($result as $row)
								{
									if ($to=="")  $to=$row[csf('team_member_name')]; else $to=$to.", ".$row[csf('team_member_name')]; 
								}
								echo $to;  
							?>
                            <br> Your Fabric Booking No. &nbsp;
							<?
								$sql1 ="SELECT booking_no,buyer_id FROM wo_booking_mst where id=$woid";
								$result1=sql_select($sql1);
								foreach($result1 as $row1)
								{
									$wo_no=$row1[csf('booking_no')]; 
									$buyer=$row1[csf('buyer_id')]; 
								}
								
								
							?>&nbsp;<?  echo $wo_no;  ?>,&nbsp; <? echo $buyer_arr[$buyer]; ?>&nbsp;of buyer has been refused due to following reason. 
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <?  echo $mail; ?>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    Thanks,<br>
					<?
						$user_name=return_field_value("user_name","user_passwd","id=$user_id"); 
						echo $user_name;  
					?>
                </td>
            </tr>
        </table>
    <?
	
	$to="";
	
	$sql2 ="SELECT c.team_member_email FROM wo_booking_mst a,wo_po_details_master b,lib_mkt_team_member_info c where b.job_no=a.job_no and b.dealing_marchant=c.id and a.id=$woid";
		
		$result2=sql_select($sql2);
		foreach($result2 as $row2)
		{
			if ($to=="")  $to=$row2[csf('team_member_email')]; else $to=$to.", ".$row2[csf('team_member_email')]; 
		}
		
 		$subject="Approval Status";
    	$message="";
    	$message=ob_get_contents();
    	ob_clean();
		
		//echo $message;
		 //$to='akter.babu@gmail.com,saeed@fakirapparels.com,akter.hossain@fakirapparels.com,bdsaeedkhan@gmail.com,shajjadhossain81@gmail.com';
		//$to='shajjad@logicsoftbd.com';
		//$to='shajjadhossain81@gmail.com';
		$header=mail_header();
		
		echo send_mail_mailer( $to, $subject, $message, $header );
		
		/*if (mail($to,$subject,$message,$header))
			echo "****Mail Sent.---".date("Y-m-d");
		else
			echo "****Mail Not Sent.---".date("Y-m-d");*/
		
		//echo "222**".$woid;
		exit();
		
}
?>