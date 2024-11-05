<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];
$user_id=$_SESSION['logic_erp']['user_id'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
$dealing_arr=return_library_array( "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0",'id','team_member_name');
$season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "load_drop_down( 'requires/sample_requisition_approval_controller', this.value, 'load_drop_down_season_buyer', 'season_td');" );  
	exit();
}

if($action=="load_drop_down_buyer_new_user")
{
	$data=explode("_",$data);
//	echo "SELECT password,user_level,id,access_ip,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE user_name = '$data[1]' AND valid = 1";die;
	$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$data[1]' AND valid = 1"); 
	//print_r($log_sql);die;
	foreach($log_sql as $r_log)
	{
		if($r_log[csf('IS_DATA_LEVEL_SECURED')]==1)
		{
			if($r_log[csf('BUYER_ID')]!="") $buyer_cond=" and buy.id in (".$r_log[csf('BUYER_ID')].")"; else $buyer_cond="";
		}
		else $buyer_cond="";
	}
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();	
}


if ($action=="load_drop_down_season_buyer")
{
 	echo create_drop_down( "cbo_season_name", 70, "select a.id,a.season_name from lib_buyer_season a where a.status_active =1 and a.is_deleted=0 and a.buyer_id='$data'","id,season_name", 1, "-- Select Season --", $selected, "" );
}

if($action=='user_popup'){
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
			// echo $sql;
		 $arr=array (2=>$custom_designation,3=>$Department);
		 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
        
</form>
<script language="javascript" type="text/javascript">
  setFilterGrid("tbl_style_ref");
</script>


<?
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_season_name=str_replace("'","",$cbo_season_name);
 	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_st_date=str_replace("'","",$txt_st_date);
	$txt_end_date=str_replace("'","",$txt_end_date);
 	$approval_type=str_replace("'","",$cbo_approval_type);
 	$txt_req_no=str_replace("'","",$txt_req_no);
	$cbo_year=str_replace("'","",$cbo_year);

	
	//echo "000dsdreud   ".$company_name."  ".$txt_season."   ".$txt_style_ref."   ".$txt_st_date."   ".$txt_end_date."   ".$approval_type;die;
	
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	
	if($txt_alter_user_id!="")
	{	
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$txt_alter_user_id' AND valid = 1"); 
			foreach($log_sql as $r_log)
			{
				if($r_log[csf('is_data_level_secured')]==1)
				{
					if($r_log[csf('buyer_id')]!="") $buyer_id_cond2=" and a.buyer_name in (".$r_log[csf('buyer_id')].")"; else $buyer_id_cond2="";
				}
				else $buyer_id_cond2="";
			}
		}
		else
		{
			//echo $buyer_id_cond2."**".$cbo_buyer_name;die;
			$buyer_id_cond2=" and a.buyer_name=$cbo_buyer_name";
		}
		
		$user_id=$txt_alter_user_id;
	}
	else
	{
		if(str_replace("'","",$cbo_buyer_name)==0) 
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") 
				{
					$buyer_id_cond2=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				}
				else 
				{
					$buyer_id_cond2="";
				}
			}
			else
			{
				$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond2=" and a.buyer_name=$cbo_buyer_name"; 
		}
	}
	
	$season_cond="";
	if($cbo_season_name>0)
	{
		$season_cond=" and a.season='$cbo_season_name' ";
	}
	
	if (trim($txt_style_ref)!="") $style_id_cond=" and a.style_ref_no like '%$txt_style_ref%' "; else $style_id_cond="";
	if (trim($txt_req_no)!="") $req_id_cond=" and a.requisition_number_prefix_num like '%$txt_req_no%' "; else $req_id_cond="";
	$date_cond="";
	if($db_type==2)
	{
		if (trim($txt_st_date)!="" && trim($txt_end_date!="")) $date_cond=" and a.requisition_date between '$txt_st_date' and '$txt_end_date' "; else $date_cond="";
	}
	if($cbo_year>0)
	{
		if($db_type==0)
		{
			$cbo_year_cond =" and year(a.insert_date)='$cbo_year'";
		}
		else
		{
			$cbo_year_cond =" and to_char(a.insert_date,'YYYY')='$cbo_year'";
		}	
	}

	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}	 


	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq");
	
	
	$buyer_ids_array=array();
	$buyerData=sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2 and sequence_no<=$user_sequence_no");
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Fabric Booking.</font>";
		die;
	}
	
	
	if($previous_approved==1 && $approval_type==1)
	{
	
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_name in($buyer_ids)";
		$sequence_no_cond=" and b.sequence_no<'$user_sequence_no'";
		 
		$sql_req="select a.id,a.company_id,a.requisition_date,a.requisition_number_prefix_num,a.style_ref_no,a.buyer_name,a.season,a.product_dept,a.dealing_marchant,a.agent_name,a.buyer_ref,a.bh_merchant,estimated_shipdate,a.remarks,a.status_active,a.is_deleted from sample_development_mst a,approval_history b where a.id=b.mst_id and a.entry_form_id in(449,117,203) and b.entry_form=25 and a.company_id=$company_name $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $season_cond $style_id_cond $req_id_cond $date_cond $cbo_year_cond and a.is_approved  in (1,3) and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 order by a.id";

		
	}
	else if($approval_type==0)
	{
		if($db_type==0)
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id='' and is_deleted=0","seq");
		}
		else
		{
			$sequence_no=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
		}
		
		if($user_sequence_no==$min_sequence_no)
		{	
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_name in($buyer_ids)";
			
			$sql_req="select a.id,a.company_id,a.requisition_date,a.requisition_number_prefix_num,a.style_ref_no,a.buyer_name,a.season,a.product_dept,a.dealing_marchant,a.agent_name,a.buyer_ref,a.bh_merchant,estimated_shipdate,a.remarks,a.status_active,a.is_deleted from sample_development_mst a where a.entry_form_id in(449,117,203) and a.company_id=$company_name  $buyer_id_cond $buyer_id_cond2 $season_cond $style_id_cond $req_id_cond $date_cond $cbo_year_cond and  a.is_approved=$approval_type and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 order by a.id";
		}
		else if($sequence_no=="")
		{  
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_name in($buyer_ids)";
			
			if($db_type==0)
			{

				$seqSql="select group_concat(sequence_no) as sequence_no_by,
 		group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
		
				$seqData=sql_select($seqSql);
				
				$sequence_no_by=$seqData[0][csf('sequence_no_by')];
				$buyerIds=$seqData[0][csf('buyer_ids')];
				
				if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.buyer_name not in($buyerIds)";
				
				$sample_id=return_field_value("group_concat(distinct(mst_id)) as sample_id","sample_development_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.entry_form_id in(449,117,203) and b.entry_form=25 and b.sequence_no in ($sequence_no_by) and b.current_approval_status=1 $buyer_id_cond $date_cond","sample_id");
				
				$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as sample_id","sample_development_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.entry_form_id in(449,117,203) and b.sequence_no=$user_sequence_no and b.entry_form=25 and b.current_approval_status=1","sample_id");
			}
			else
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
								$query_string.=" (b.sequence_no=".$sRow[csf('sequence_no')]." and a.buyer_name in(".implode(",",$result).")) or ";
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
				
				$booking_id='';
				$booking_id_sql="select distinct (mst_id) as booking_id from sample_development_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_name and a.entry_form_id in(449,117,203)  and b.sequence_no in ($sequence_no_by_no) and b.entry_form=25 and b.current_approval_status=1 $buyer_id_cond $date_cond $seqCond
				union
				select distinct (mst_id) as booking_id from sample_development_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_name and a.entry_form_id in(449,117,203) and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=25 and b.current_approval_status=1 $buyer_id_cond $date_cond";
				$bResult=sql_select($booking_id_sql);
				foreach($bResult as $bRow)
				{
					$booking_id.=$bRow[csf('booking_id')].",";
				}
				
				$booking_id=chop($booking_id,',');
				
				$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","sample_development_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.entry_form_id in(449,117,203) and b.sequence_no=$user_sequence_no and b.entry_form=25 and b.current_approval_status=1","booking_id");
				$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
			}
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
				
					$sql_req="select * from ( select a.id,a.company_id,a.requisition_date,a.requisition_number_prefix_num,a.style_ref_no,a.buyer_name, a.season,a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted , '0' as approval_id,  a.is_approved from sample_development_mst a where  a.entry_form_id in(449,117,203)  and a.company_id=$company_name  $buyer_id_cond $buyer_id_cond2  $buyerIds_cond $season_cond $style_id_cond $req_id_cond $date_cond  $cbo_year_cond and  a.is_approved=$approval_type and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 
					union all
					select a.id,a.company_id,a.requisition_date,a.requisition_number_prefix_num, a.style_ref_no,a.buyer_name,a.season, a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted , '0' as approval_id,  a.is_approved from sample_development_mst a where  a.entry_form_id in(449,117,203)  and a.company_id=$company_name  and a.is_approved in(1,3) and a.id in($booking_id) $buyer_id_cond $buyer_id_cond2 $date_cond $season_cond $style_id_cond $req_id_cond $date_cond $cbo_year_cond and a.is_approved in(1,3) and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 ) sql order by sql.id
					";	
						
				}
				else
				{
					$sql_req="select a.id,a.company_id,a.requisition_date,a.requisition_number_prefix_num,a.style_ref_no,a.buyer_name, a.season,a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted , '0' as approval_id,  a.is_approved from sample_development_mst a where  a.entry_form_id in(449,117,203)  and a.company_id=$company_name  $buyer_id_cond $buyer_id_cond2  $buyerIds_cond $season_cond $style_id_cond $req_id_cond $date_cond $cbo_year_cond and  a.is_approved=$approval_type and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 order by a.id";
				}
			
			}
			else
			{
				if($booking_id!="")
				{   
					
					$sql_req=" select * from (select a.id,a.company_id,a.requisition_date,a.requisition_number_prefix_num,a.style_ref_no,a.buyer_name, a.season,a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted , '0' as approval_id,  a.is_approved from sample_development_mst a where  a.entry_form_id in(449,117,203)  and a.company_id=$company_name  $buyer_id_cond $buyer_id_cond2  $buyerIds_cond $season_cond $style_id_cond $req_id_cond $date_cond $cbo_year_cond and  a.is_approved=$approval_type and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 
					union all
					select a.id,a.company_id,a.requisition_date,a.requisition_number_prefix_num, a.style_ref_no,a.buyer_name,a.season, a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant,a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted , '0' as approval_id,  a.is_approved from sample_development_mst a where  a.entry_form_id in(449,117,203)  and a.company_id=$company_name  and a.is_approved in(1,3) $booking_id_cond $buyer_id_cond $buyer_id_cond2 $date_cond $season_cond $style_id_cond $req_id_cond $date_cond $cbo_year_cond and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 ) sql order by sql.id";	 
                }
				else
				{
				
					$sql_req="select a.id,a.company_id,a.requisition_date,a.requisition_number_prefix_num,a.style_ref_no,a.buyer_name, a.season,a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted , '0' as approval_id,  a.is_approved from sample_development_mst a where  a.entry_form_id in(449,117,203)  and a.company_id=$company_name  $buyer_id_cond $buyer_id_cond2  $buyerIds_cond $season_cond $style_id_cond $req_id_cond $date_cond $cbo_year_cond and  a.is_approved=$approval_type and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 order by a.id";
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
				$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
			}
			else
			{
				$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");	
			}
			
			if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
			$sql_req="select a.id,a.company_id,a.requisition_date,a.requisition_number_prefix_num,a.style_ref_no,a.buyer_name, a.season,a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate,a.remarks,a.status_active,a.is_deleted,  a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by from sample_development_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=25 and a.entry_form_id in(449,117,203)  and a.company_id=$company_name  $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $season_cond $style_id_cond $req_id_cond $date_cond $cbo_year_cond and a.is_approved in (1,3) and b.current_approval_status=1 and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0 order by a.id";
			//echo $sql_req;
		}
	}
	else
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_name in($buyer_ids)";
		//$sequence_no_cond=" and b.sequence_no='$user_sequence_no'";
		$sequence_no_cond=" and b.approved_by='$user_id'";
		
		 $sql_req="select a.id,a.company_id,a.requisition_date,a.requisition_number_prefix_num,a.style_ref_no,a.buyer_name, a.season,a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate ,a.remarks, a.status_active, a.is_deleted, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by from sample_development_mst a, approval_history b where a.id=b.mst_id  and b.entry_form=25 and a.entry_form_id in(449,117,203) and a.company_id=$company_name  $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $season_cond $style_id_cond $req_id_cond $date_cond $cbo_year_cond and  a.is_approved in (1,3) and a.req_ready_to_approved=1 and b.current_approval_status=1 and a.status_active=1 and a.is_deleted=0 group  by a.id,a.company_id,a.requisition_date,a.requisition_number_prefix_num,a.style_ref_no,a.buyer_name, a.season,a.product_dept, a.dealing_marchant, a.agent_name,a.buyer_ref, a.bh_merchant, a.estimated_shipdate, a.remarks,a.status_active, a.is_deleted, a.is_approved, b.id, b.sequence_no, b.approved_by order by a.id";
		
    }
	 //echo $sql_req;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1000px; margin-top:10px">
        <legend>Sample Requisition Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="50">SL</th>
                    <th width="80">Requisition No</th>
                    <th width="70">Date</th>
                    <th width="120">Company</th>
                    <th width="60">Dealing Mer.</th>
                    <th width="90">Buyer</th>
                    <th width="120">Season</th>
                    <th width="70">Style Ref</th>
                    <th width="70">Sample Qty</th>
                    <th width="50">Fabric Qty</th>
                    <th>Embellishment</th>
                </thead>
            </table>
            <div style="width:960px; overflow-y:scroll; max-height:380px;" id="buyer_list_view">
                <table align="left" cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1;
							$nameArray=sql_select($sql_req);
							$sampQty=sql_select("select sample_mst_id,sum(sample_prod_qty) as sd from sample_development_dtls where  status_active=1 and is_deleted=0 and entry_form_id in(449,117,203) group by sample_mst_id");
						
							{
								$samplQtyArr[$val[csf('sample_mst_id')]]=$val[csf('sd')];
							}
							$reqQty=sql_select("select sample_mst_id,sum(required_qty) as rq from sample_development_fabric_acc where status_active=1 and is_deleted=0 and form_type=1 group by sample_mst_id");
							foreach ($reqQty as $Reqval)
							{
								$reqQtyArr[$Reqval[csf('sample_mst_id')]]=$Reqval[csf('rq')];
							}

							$emb_sel=sql_select("select sample_mst_id,count(id) as ide from sample_development_fabric_acc where form_type=3 and status_active=1 and is_deleted=0 group by sample_mst_id");
							foreach($emb_sel as $embVal)
							{
								$embArr[$embVal[csf('sample_mst_id')]]=$embVal[csf('ide')];
							}

							foreach ($nameArray as $row)
							{ 
								$id=$row[csf('id')] ;


								if ($i%2==0)  
								$bgcolor="#E9F3FF";
								else
								$bgcolor="#FFFFFF";
								$value='';
								$value=$row[csf('id')]."**".$row[csf('approval_id')];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
									<td width="50" align="center" valign="middle">
									<input type="checkbox" id="tbl_<? echo $i;?>" />
									<input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<? echo $value; ?>" />
									<input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
									<input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
									</td>   
									<td width="50" align="center"><? echo $i; ?></td>
									<td width="80" align="center"> <a href='##' style='color:#000' onClick="print_report(<? echo $row[csf('company_id')]; ?>+'*'+<? echo $row[csf('id')]; ?>,'sample_requisition_print', '../order/woven_order/requires/sample_requisition_controller')">
									<? echo $row[csf('requisition_number_prefix_num')]; ?></a>
									</td>
									<td width="70"><p><? if($row[csf('requisition_date')]!="0000-00-00") echo change_date_format($row[csf('requisition_date')]); ?></p></td>
									<td width="120" align="left"> <? echo $company_arr[$row[csf('company_id')]]; ?></td>
									<td align="left" width="60"> <? echo $dealing_arr[$row[csf('dealing_marchant')]]; ?></td>

									<td width="90"> <? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
									<td width="120"><p><? echo $season_arr[$row[csf('season')]]; ?></p></td>
									<td width="70"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
									<td width="70" align="right">
									<p>
									<?
										echo $samplQtyArr[$row[csf('id')]];
									?>
									</p></td>
									<td width="50" align="right"><p><? echo  $reqQtyArr[$row[csf('id')]];?></p></td>
									<td>
									<? 
									if($embArr[$row[csf('id')]]>0)
									{
										echo "&nbsp; YES";
									}
									else
									{
										echo "&nbsp; NO";
									}

									?>  
									</td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="960" class="rpt_table">
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

if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response=''; $requisition='';
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") 	$user_id_approval=$txt_alter_user_id;
	else						$user_id_approval=$user_id;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

	if($approval_type==0)
	{
		$response=trim($req_nos);
		$reqs_ids=explode(",",trim($req_nos));
		$buyer_arr=return_library_array( "select id, buyer_name  from sample_development_mst where id in ($reqs_ids)", "id", "buyer_id"  );
		
		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
		
		if($db_type==2) {$buyer_id_cond=" and a.buyer_id is null "; $buyer_id_cond2=" and b.buyer_id is not null ";}
		else 			{$buyer_id_cond=" and a.buyer_id='' "; $buyer_id_cond2=" and b.buyer_id!='' ";}
		
		$is_not_last_user=return_field_value("a.sequence_no as sequence_no","electronic_approval_setup a"," a.company_id=$cbo_company_name and a.page_id=$menu_id and a.sequence_no>$user_sequence_no and a.bypass=2 and a.is_deleted=0 $buyer_id_cond","sequence_no");
		
		$partial_approval = "";
		if($is_not_last_user == "")
		{
			$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2  group by b.buyer_id");
			if(count($sql)>0)
			{
				foreach ($sql as $key => $buyerID) {
					$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
				}
				
				$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
			}
		}
		else
		{
			$check_user_buyer = sql_select("select b.user_id as user_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond  group by b.user_id");

			if(count($check_user_buyer)==0)
			{
				
				$sql = sql_select("select b.buyer_id as buyer_id from user_passwd b, electronic_approval_setup a where b.id = a.user_id and a.company_id=$cbo_company_name and a.page_id=$menu_id and b.sequence_no>$user_sequence_no and a.is_deleted=0 and a.bypass=2 $buyer_id_cond $buyer_id_cond2 group by b.buyer_id");
				if(count($sql)>0)
				{
					foreach ($sql as $key => $buyerID) {
						$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
					}
				}
				
				$sql = sql_select("select (b.buyer_id) as buyer_id from electronic_approval_setup b where b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 and b.bypass=2 $buyer_id_cond2  group by b.buyer_id");
				if(count($sql)>0)
				{
					foreach ($sql as $key => $buyerID) {
						$credentialUserBuyersArr[] = $buyerID[csf('buyer_id')];
					}
				}
				$credentialUserBuyersArr = implode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = explode(',',$credentialUserBuyersArr);
				$credentialUserBuyersArr = array_unique($credentialUserBuyersArr);
			}
			
		}
		$field_array="id, entry_form, mst_id, approved_no,sequence_no, current_approval_status, approved_by, approved_date,inserted_by,insert_date";
		
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($reqs_ids) and entry_form=25 group by mst_id","mst_id","approved_no");
		
		$approved_status_arr = return_library_array("select id, is_approved from sample_development_mst where id in($reqs_ids)","id","is_approved");
		 
		$i=0;
		$id=return_next_id( "id","approval_history", 1 ) ;
		$approved_no_array=array();
		$booking_ids_all=explode(",",$reqs_ids);
		$book_nos='';
		$booking_ids='';
		foreach($reqs_ids as $val)
		{	$value_arr=explode("**",$val);
			$booking_id=$value_arr[0];
			if($booking_ids=='') $booking_ids=$booking_id; else $booking_ids.=",".$booking_id;
			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];
			$buyer_id=$buyer_arr[$booking_id];
			
			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$booking_id]=$approved_no;
			}
			
			
			if($is_not_last_user == "")
			{
				if(in_array($buyer_id,$credentialUserBuyersArr))
				{
					$partial_approval=3;
				}			
				else
				{
					$partial_approval=1;
				}
			}
			else
			{
				if(count($credentialUserBuyersArr)>0)
				{
					if(in_array($buyer_id,$credentialUserBuyersArr))
					{
						$partial_approval=3;
					}			
					else
					{
						$partial_approval=1;
					}
				}
				else
				{
					$partial_approval=1;
				}
			}
			
			$booking_id_arr[]=$booking_id;
			$data_array_booking_update[$booking_id]=explode("*",($partial_approval));
			
 			if($data_array!="") $data_array.=",";

			$data_array.="(".$id.",25,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')";
			
			$id=$id+1;
			$i++;
		}
 		


	if(count($approved_no_array)>0)
	{
		$approved_string="";
		
		foreach($approved_no_array as $key=>$value)
		{
			$approved_string.=" WHEN $key THEN $value";
		}


		$approved_string_mst="CASE id ".$approved_string." END"; 
		//CASE id  WHEN 538 THEN 2 END
		$approved_string_dtls="CASE sample_mst_id ".$approved_string." END";
		$approved_string_dtls_fab="CASE sample_mst_id ".$approved_string." END";
		
		$sql_insert="insert into sample_development_mst_history(id,hist_mst_id,approved_no,requisition_number_prefix,requisition_number_prefix_num,requisition_number,sample_stage_id,requisition_date,quotation_id,style_ref_no,company_id,location_id,buyer_name,season,product_dept,dealing_marchant,agent_name,buyer_ref,bh_merchant,estimated_shipdate,remarks,inserted_by,insert_date,status_active,is_deleted,entry_form_id,updated_by,update_date) 
			select	
			'',id,$approved_string_mst,requisition_number_prefix,requisition_number_prefix_num,requisition_number,sample_stage_id,requisition_date,quotation_id,style_ref_no,company_id,location_id,buyer_name,season,product_dept,dealing_marchant,agent_name,buyer_ref,bh_merchant,estimated_shipdate,remarks,inserted_by,insert_date,status_active,is_deleted,entry_form_id,updated_by,update_date from  sample_development_mst where id in ($booking_ids)";
				
		

		 $sql_insert_dtls="insert into sample_development_dtls_hist(id,approved_no,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id,updated_by,update_date) 
			select	
			'', $approved_string_dtls,sample_mst_id,sample_name,gmts_item_id,smv,article_no,sample_color,sample_prod_qty,submission_qty,delv_start_date,delv_end_date,sample_charge,sample_curency,inserted_by,insert_date,status_active,is_deleted,entry_form_id,size_data,fabric_status,acc_status,embellishment_status,fab_status_id,acc_status_id,embellishment_status_id,updated_by,update_date from  sample_development_dtls where sample_mst_id in ($booking_ids) and entry_form_id in(449,117,203)";

			$sql_insert_dtls_fab="insert into sample_development_fabric_hist(id,approved_no,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,updated_by,update_date,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re) 
			select	
			'',$approved_string_dtls_fab,sample_mst_id,sample_name,gmts_item_id,body_part_id,fabric_nature_id,fabric_description,gsm,dia,color_data,color_type_id,width_dia_id,uom_id,required_dzn,required_qty,inserted_by,insert_date,status_active,is_deleted,updated_by,update_date,sample_name_ra,gmts_item_id_ra,trims_group_ra,description_ra,brand_ref_ra,uom_id_ra,req_dzn_ra,req_qty_ra,sample_name_re,gmts_item_id_re,name_re,type_re,remarks_re from sample_development_fabric_acc where sample_mst_id in ($booking_ids)";
		}
		$flag=1;
		
		$field_array_booking_update = "is_approved";
		//echo bulk_update_sql_statement( "sample_development_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr);die;
		$rID=execute_query(bulk_update_sql_statement( "sample_development_mst", "id", $field_array_booking_update, $data_array_booking_update, $booking_id_arr));
		if($rID) $flag=1; else $flag=0;
		
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=25 and mst_id in ($booking_ids)";
		$rIDapp=execute_query($query,1);
		if($flag==1) 
		{
			if($rIDapp) $flag=1; else $flag=0; 
		}
		//echo "21**".$sql_insert;die;
		$rID2=sql_insert("approval_history",$field_array,$data_array,0);

		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0;
		} 
		
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
		
 		$rID5=execute_query($sql_insert_dtls_fab,1);
  		if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0; 
		} 
		//echo "21**".$flag.'='.$rID5.'='.$rID4.'='.$rID3.'='.$rID2.'='.$rIDapp.'='.$rID; die;
		if($flag==1) $msg='19'; else $msg='21';
	}
	else
	{
  		$arrs=explode(',', $req_nos);
  		$val="";
  		$requisition_number="";
  		foreach ($arrs as $value)
 		 {
			$arrs2=explode('**', $value);//concate(buyer_name,'_',contact_person
			$is_exits_ack=return_field_value("is_acknowledge","sample_development_mst","id=$arrs2[0]");
			
   			if($is_exits_ack==1)
 			{
 				if($val=="") $val.=$is_exits_ack."t";else $val .=','.$is_exits_ack.'t';
 				$req_no=return_field_value("requisition_number","sample_development_mst","id=$arrs2[0]");
 				if($requisition_number=="") $requisition_number.=$req_no;else $requisition_number .=','.$req_no;
 			}	
 		 }
 		 $requisition=$requisition_number;
 		 if(strpos($val, 't')==true)
 		 {
  		 	  $msg='308';
   		 }
  		 else 
 		 {
 		$req_nos = explode(',',$req_nos); 
 		$reqs_ids=''; $app_ids='';
		
		foreach($req_nos as $value)
		{
			$data = explode('**',$value);
			$reqs_id=$data[0];
			$app_id=$data[1];
			
			if($reqs_ids=='') $reqs_ids=$reqs_id; else $reqs_ids.=",".$reqs_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}
		
		$rID=sql_multirow_update("sample_development_mst","is_approved*req_ready_to_approved","0*0","id",$reqs_ids,0);
		if($rID) $flag=1; else $flag=0;

		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=25 and mst_id in ($reqs_ids)";
		$rID2=execute_query($query,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=20; 
		}
		
 		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,0);

 		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=10; 
		} 
		
		$response=$reqs_ids;
		if($flag==1) $msg='20'; else $msg='22';
 		 }
 		
	}
	
	if($db_type==0)
	{ 
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo $msg."**".$response."**".$requisition;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo $msg."**".$response."**".$requisition;
		}
	}
	
	if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con);
			echo $msg."**".$response."**".$requisition;
		}
		else
		{
			oci_rollback($con);
			echo $msg."**".$response."**".$requisition;
		}
	}
	disconnect($con);
	die;
	
}
?>