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

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

if ($action=="load_drop_down_buyer")
{
    if($data != 0)
    {
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
	exit();  
    }  
    else{
        echo create_drop_down( "cbo_buyer_name", 152, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,30,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); 
        exit(); 
    }	 
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

if($action=="report_generate")
{
	// var_dump($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
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
					if($r_log[csf('buyer_id')]!="") $buyer_id_cond=" and a.buyer_id in (".$r_log[csf('buyer_id')].")"; else $buyer_id_cond="";
				}
				else $buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
		}
		$user_id=$txt_alter_user_id;
	}
	else
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
		}
	}
	//echo $buyer_id_cond."**";die;
	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.quot_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.quot_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.quot_date=$txt_date";
		else $date_cond='';
	}

	$team_member_cond = "";
	if(str_replace("'","",$txt_team_member)!="")
	{
		$team_member_cond ="and a.team_member LIKE '%$txt_team_member%'";
	}

	$price_quotation_cond = "";
	if(str_replace("'","",$txt_price_quotation_id)!="")
	{
		$price_quotation_cond = " and a.sys_prefix_num=$txt_price_quotation_id";
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}
	
	//echo $menu_id;die;
	//$user_id=3;
	//$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	//$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");  
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	$buyer_ids_array = array();
	$buyerData = sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2");//echo "select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2";
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Price Quotation.</font>";
		die;
	}
	if($previous_approved==1 && $approval_type==1) // For approve list with prevous user aprroval
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
		
		$sequence_no_cond=" and b.sequence_no<'$user_sequence_no'";
		$sql="SELECT a.id,a.sys_prefix_num,  a.company_id, a.buyer_id, a.style_ref, a.quot_date,a.team_member, b.id as approval_id
		from wo_price_quotation_v3_mst a, approval_history b where a.id=b.mst_id and b.entry_form=34 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and b.current_approval_status=1 and a.approved in(1,3) and b.approved_by!=$user_id $buyer_id_cond2 $sequence_no_cond $date_cond $team_member_cond $price_quotation_cond order by a.id ASC";
		//$buyer_id_cond
		
	}
	else if($approval_type==0) // For unapprove list
	{		
		if($db_type==0)
		{
			$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass = 2 and buyer_id='' and is_deleted=0","seq");
		}
		else
		{
			$sequence_no = return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and buyer_id is null and is_deleted=0","seq");
		}
		// echo "Sequence=".$sequence_no;
		if($user_sequence_no==$min_sequence_no) // FIRST USER (DEPEND ON ELECTRONIC APPROVAL SETUP)
		{
			
			$buyer_ids = $buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
			
			$sql="SELECT a.id, a.sys_prefix_num, a.company_id,  a.buyer_id, a.style_ref, a.team_member, a.quot_date,'0' as approval_id
			from wo_price_quotation_v3_mst a
			where a.company_id=$company_name and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.is_approved=$approval_type $buyer_id_cond $buyer_id_cond2 $date_cond $team_member_cond $price_quotation_cond 
			group by a.id, a.sys_prefix_num, a.company_id,  a.buyer_id, a.style_ref, a.team_member, a.quot_date 
			order by a.id ASC";
			//echo $sql;//die;	
		}
		else if($sequence_no=="")
		{
			
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
	
			$seqSql="SELECT sequence_no, bypass, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0 order by sequence_no desc";
			//echo $seqSql;die;
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
			
			$quotation_id='';
			$quotation_id_sql="SELECT distinct (mst_id) as quotation_id from wo_price_quotation_v3_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_name  and b.sequence_no in ($sequence_no_by_no) and b.entry_form=34 and b.current_approval_status=1 $buyer_id_cond $date_cond $seqCond
			union
			select distinct (mst_id) as quotation_id from wo_price_quotation_v3_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_name and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=34 and b.current_approval_status=1 $buyer_id_cond $date_cond";
			$bResult=sql_select($quotation_id_sql);
			foreach($bResult as $bRow)
			{
				$quotation_id.=$bRow[csf('quotation_id')].",";
			}
			
			$quotation_id=chop($quotation_id,',');
		
			
			$quotation_id_app_sql=sql_select(" SELECT mst_id as quotation_id from wo_price_quotation_v3_mst a, approval_history b where a.id=b.mst_id and a.company_id=$company_name  and b.sequence_no=$user_sequence_no and b.entry_form=34 and b.current_approval_status=1");
			
			foreach($quotation_id_app_sql as $inf)
			{
				if($quotation_id_app_byuser!="") $quotation_id_app_byuser.=",".$inf[csf('pre_cost_id')];
				else $quotation_id_app_byuser.=$inf[csf('quotation_id')];
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
		
			if($quotation_id!="")
			{
				$sql="SELECT x.* from (SELECT a.id,a.sys_prefix_num,  a.company_id,  a.buyer_id, a.style_ref,a.team_member,a.quot_date,'0' as approval_id,  a.is_approved from wo_price_quotation_v3_mst a where  a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.is_approved=$approval_type  $quotation_id_cond $buyerIds_cond $buyer_id_cond $buyer_id_cond2  $date_cond  group by a.id,a.sys_prefix_num, a.company_id, a.buyer_id, a.team_member, a.style_ref, a.is_approved
					union all
					select a.id,a.sys_prefix_num,  a.company_id,  a.buyer_id, a.style_ref, a.team_member, a.quot_date,'0' as approval_id,  a.is_approved  from wo_price_quotation_v3_mst a where a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.is_approved in (1,3) and (a.id in($quotation_id))  $buyer_id_cond $buyer_id_cond2   $date_cond group by a.id,a.sys_prefix_num, a.company_id, a.buyer_id,a.team_member, a.style_ref, a.quot_date, a.is_approved) x order by x.id ";
					
			}
			else
			{
				$sql="SELECT a.id,a.sys_prefix_num, a.company_id,  a.buyer_id, a.style_ref, a.team_member, a.quot_date,'0' as approval_id,  a.is_approved 
				 from wo_price_quotation_v3_mst a where a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1 and a.ready_to_approved=1 and a.is_approved=$approval_type  $quotation_id_cond $buyerIds_cond $buyer_id_cond $buyer_id_cond2  $date_cond  group by a.id,a.sys_prefix_num, a.company_id, a.buyer_id, a.team_member, a.style_ref, a.quot_date,a.is_approved order by a.id ASC";
			}
			//echo $sql;
		}
		else
		{
			
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
			$user_sequence_no=$user_sequence_no-1;
			//$sequence_no_min=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0");
			if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
			else
			{
				if($db_type==0)
				{
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");	
				}
			}
			
			if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
			
			$sql="SELECT a.id,  a.company_id, a.sys_prefix_num,a.team_member, a.buyer_id,  a.style_ref, a.quot_date, a.is_approved,b.approved_date, b.id as approval_id  from wo_price_quotation_v3_mst a, approval_history b where a.id=b.mst_id and b.entry_form=34 and a.company_id=$company_name  and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and  a.is_approved in(1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond group by a.id,  a.company_id,a.sys_prefix_num,a.team_member,  a.buyer_id,  a.style_ref, a.quot_date, a.is_approved,b.approved_date, b.id order by a.id ASC";
		}
		//echo $sql;
	}
	else
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
		
		$sequence_no_cond=" and b.sequence_no='$user_sequence_no'";
		$sql="SELECT a.id,a.sys_prefix_num,a.team_member,  a.company_id, a.buyer_id, a.style_ref, a.quot_date, a.is_approved,b.approved_date, b.id as approval_id from wo_price_quotation_v3_mst a, approval_history b where a.id=b.mst_id and b.entry_form=34 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and b.current_approval_status=1 and a.is_approved in(1,3) $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $date_cond order by a.id ASC";
	}
	 //echo $sql;
	
	$tbl_width = 870;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $tbl_width+20;?>px; margin-top:10px">
        <legend>Price Quotation Approval V3</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width;?>" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="130">Quatation No</th>                  
                    <th width="125">Buyer</th>
                    <th width="60">Style Ref.</th>
                    <th width="100">Quatation Date</th>
                    <th width="100">Team Member</th>
                    <th width="100">Approved Date</th>
                </thead>
            </table>
            <div style="width:<? echo $tbl_width;?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width-18;?>" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
							//echo $sql; die;
                            $i=1;
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$value=$row[csf('id')];
								// $gmt_nature=$row[csf('garments_nature')];
								$date = change_date_format($row[csf("approved_date")]);

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                        <input id="<? echo strtoupper($row[csf('id')]); ?>" name="no_quot[]" type="hidden" value="<? echo $i;?>" />
                                    </td>   
									<td width="40" align="center"><? echo $i; ?></td>
									<?
										$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$row[csf('company_id')]."' and module_id=2 and report_id=32 and is_deleted=0 and status_active=1");
										$report_id = explode(',', $print_report_format);
										//$report_name=$report_format[$report_id['0']];
										if($report_id['0'] == 90){
											$quotation_rep = 'preCostRpt';
										}
										elseif ($report_id['0'] == 91) {
											$quotation_rep = 'preCostRpt2';
										}
										elseif ($report_id['0'] == 92) {
											$quotation_rep = 'preCostRpt3';
										}
										elseif ($report_id['0'] == 194) {
											$quotation_rep = 'preCostRpt4';
										}
										else{
											$quotation_rep = 'preCostRpt2';
										}
										//$gmt_nature = $_SESSION['fabric_nature'];
									?>

									<td width="130">
                                    	<p><a href='##' style='color:#000' onclick="generate_worder_report('<? echo $quotation_rep; ?>',<? echo $row[csf('id')]; ?>,<? echo $row[csf('company_id')]; ?>,<? echo $row[csf('buyer_id')]; ?>,'<? echo $row[csf('style_ref')];?>','<? echo $row[csf('quot_date')]; ?>',2)"><? echo $row[csf('sys_prefix_num')]; ?></a></p>
                                    </td>
                                    
									
                                    <td width="125" align="left"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
									<td width="60" align="left"><p><? echo $row[csf('style_ref')]; ?>&nbsp;</p></td>                                
                                  
                                   
                                    <td width="100" align="center"><? if($row[csf('quot_date')]!="0000-00-00") echo change_date_format($row[csf('quot_date')]); ?>&nbsp;</td>
									<td align="left" width="100"><? echo $row[csf('team_member')]; ?></td>
                                    <td align="center" width="100"><? if($row[csf('approved_date')]!="0000-00-00") echo change_date_format($row[csf('approved_date')]); ?>&nbsp;</td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<? echo $tbl_width;?>" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1 || $previous_approved_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
	<?
	exit();	
}

// ********************************** Approve and Unapprove process start ********************************************
if ($action=="approve")
{
	// var_dump($_REQUEST);die();
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$user_id_approval=0;
	$msg=''; $flag=''; $response='';
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") 	$user_id_approval=$txt_alter_user_id;
	else						$user_id_approval=$user_id;
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

	if($approval_type==0) // approve process start
	{
		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
		
		// if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
		$partial_approval = "";
		if($is_not_last_user != "")
		{			
			// getting login in user's buyer id
			$loginUserBuyersArr = [];
			$loginUserBuyersSQL=sql_select("SELECT (b.buyer_id || ',' ||  a.buyer_id) as buyer_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.user_id=$user_id_approval and b.is_deleted=0 group by b.buyer_id, a.buyer_id");
			foreach ($loginUserBuyersSQL as $key => $buyerID) {
				$loginUserBuyersArr[] = $buyerID[csf('buyer_id')];
			}		

			$loginUserBuyersArr = implode(',',$loginUserBuyersArr);
			$loginUserBuyersArr = explode(',',$loginUserBuyersArr);
			$loginUserBuyersArr = array_filter($loginUserBuyersArr);
			$loginUserBuyersArr = array_unique($loginUserBuyersArr);
			// print_r($loginUserBuyersArr);die();
			
			// getting next level all user's buyer id
			$credentialUserBuyersArr = [];
			$sql = "SELECT (b.buyer_id || ',' ||  a.buyer_id) as buyer_id from user_passwd a, electronic_approval_setup b where a.id = b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and b.sequence_no>$user_sequence_no and b.is_deleted=0 group by b.buyer_id, a.buyer_id";
			$sql_res = sql_select($sql);
			foreach ($sql_res as $key => $buyerID) {
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
		//echo $partial_approval;die;
		$response=$booking_ids;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date,inserted_by,insert_date"; 
		$id=return_next_id( "id","approval_history", 1 ) ;
		$max_approved_no_arr = return_library_array("SELECT mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=34 group by mst_id","mst_id","approved_no");
		
		$approved_status_arr = return_library_array("SELECT id, is_approved from wo_price_quotation_v3_mst where id in($booking_ids)","id","is_approved");
		// print_r($approved_status_arr);die;
		$approved_no_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		$book_nos='';
		
		for($i=0;$i<count($booking_ids_all);$i++)
		{
			$booking_id=$booking_ids_all[$i];

			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];
			
			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$booking_id]=$approved_no;
				if($book_nos=="") $book_nos=$booking_id; else $book_nos.=",".$booking_id;
			}
			
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",34,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')"; 
				
			$id=$id+1;
			
		}
		//print_r($booking_ids_all);
		//echo "insert into approval_history (".$field_array.") Values ".$data_array."**".$book_nos."**".$booking_nos;die;
		
		/*if(count($approved_no_array)>0)
		{
			$approved_string=""; 
			
			foreach($approved_no_array as $key=>$value)
			{
				$approved_string.=" WHEN $key THEN $value";
			}
			
			$approved_string_mst="CASE id ".$approved_string." END";
			$approved_string_dtls="CASE quotation_id ".$approved_string." END";
			
			$sql_insert="INSERT into wo_price_quotation_his( id, approved_no, quotation_id, company_id, buyer_id, style_ref, revised_no, pord_dept, product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, fabric_source, costing_per, quot_date, est_ship_date, factory, remarks, garments_nature, order_uom, gmts_item_id, set_break_down, total_set_qnty, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, season, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, op_date, inquery_id, m_list_no, bh_marchant, ready_to_approved) 
				select	
				'', $approved_string_mst, id, company_id, buyer_id, style_ref, revised_no, pord_dept, product_code, style_desc, currency, agent, offer_qnty, region, color_range, incoterm, incoterm_place, machine_line, prod_line_hr, fabric_source, costing_per, quot_date, est_ship_date, factory, remarks, garments_nature, order_uom, gmts_item_id, set_break_down, total_set_qnty, cm_cost_predefined_method_id, exchange_rate, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, efficiency_wastage_percent, season, approved, approved_by, approved_date, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, op_date, inquery_id, m_list_no, bh_marchant, ready_to_approved
			from wo_price_quotation_v3_mst where id in ($book_nos)";
			//echo $sql_insert;die;	
		
			$sql_insert2="INSERT into wo_price_quot_costing_mst_his(id, quot_mst_id, quotation_id, approved_no, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, terget_qty, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price)
				select	
				'', id, quotation_id, $approved_string_dtls, costing_per_id, order_uom_id, fabric_cost, fabric_cost_percent, trims_cost, trims_cost_percent, embel_cost, embel_cost_percent, wash_cost, wash_cost_percent, comm_cost, comm_cost_percent, lab_test, lab_test_percent, inspection, inspection_percent, cm_cost, cm_cost_percent, freight, freight_percent, common_oh, common_oh_percent, total_cost, total_cost_percent, commission, commission_percent, final_cost_dzn, final_cost_dzn_percent, final_cost_pcs, final_cost_set_pcs_rate, a1st_quoted_price, a1st_quoted_price_percent, a1st_quoted_price_date, revised_price, revised_price_date, confirm_price, confirm_price_set_pcs_rate, confirm_price_dzn, confirm_price_dzn_percent, margin_dzn, margin_dzn_percent, price_with_commn_dzn, price_with_commn_percent_dzn, price_with_commn_pcs, price_with_commn_percent_pcs, confirm_date, asking_quoted_price, asking_quoted_price_percent, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, currier_pre_cost, currier_percent, certificate_pre_cost, certificate_percent, terget_qty, depr_amor_pre_cost, depr_amor_po_price, interest_pre_cost, interest_po_price, income_tax_pre_cost, income_tax_po_price from wo_price_quotation_costing_mst where quotation_id in ($book_nos)";
			//echo $sql_insert2;die;
			
			$sql_insert3="insert into wo_price_quot_set_details_his(id, approved_no, quot_set_dlts_id, quotation_id, gmts_item_id, set_item_ratio)
				select	
				'', $approved_string_dtls, id, quotation_id, gmts_item_id, set_item_ratio from wo_price_quotation_set_details where quotation_id in ($book_nos)";
			//echo $sql_insert3;die;
		
			$sql_insert4="insert into wo_pri_quo_comm_cost_dtls_his(id, approved_no, quo_comm_dtls_id, quotation_id, item_id, base_id,  rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select	
				'', $approved_string_dtls, id, quotation_id, item_id, base_id, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_comarcial_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert4;die;
		
			$sql_insert5="insert into wo_pri_quo_commiss_dtls_his(id, approved_no, quo_commiss_dtls_id, quotation_id, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by, update_date,  status_active, is_deleted)
				select	
				'', $approved_string_dtls, id, quotation_id, particulars_id, commission_base_id, commision_rate, commission_amount, inserted_by, insert_date, updated_by,  update_date, status_active, is_deleted from wo_pri_quo_commiss_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert5;die;
		
			$sql_insert6="insert into wo_pri_quo_embe_cost_dtls_his(id, approved_no, quo_emb_dtls_id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select	
				'', $approved_string_dtls, id, quotation_id, emb_name, emb_type, cons_dzn_gmts, rate, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_embe_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert6;die;
			
			$sql_insert7="insert into wo_pri_quo_fab_cost_dtls_his(id, approved_no, quo_fab_dtls_id, quotation_id, item_number_id, body_part_id,  fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, yarn_breack_down, width_dia_type, cons_breack_down, msmnt_break_down, marker_break_down)
				select	
				'', $approved_string_dtls, id, quotation_id, item_number_id, body_part_id, fab_nature_id, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, avg_cons, fabric_source, rate, amount, avg_finish_cons, avg_process_loss, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, company_id, costing_per, fab_cons_in_quotat_varia, process_loss_method, yarn_breack_down, width_dia_type, cons_breack_down, msmnt_break_down, marker_break_down from wo_pri_quo_fabric_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert7;die;
		
			$sql_insert8="insert into wo_pri_quo_fab_conv_dtls_his (id, approved_no, quo_fab_conv_dtls_id, quotation_id, cost_head, cons_type, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, process_loss)
				select	
				'', $approved_string_dtls, id, quotation_id, cost_head, cons_type, req_qnty, charge_unit, amount, charge_lib_id, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, process_loss from wo_pri_quo_fab_conv_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert8;die;
		
			$sql_insert9="insert into wo_pri_quo_fab_co_avg_con_his (id, approved_no, quo_fab_avg_co_dtls_id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons)
				select	
				'', $approved_string_dtls, id, wo_pri_quo_fab_co_dtls_id, quotation_id, gmts_sizes, dia_width, cons, process_loss_percent, requirment, pcs, body_length, body_sewing_margin, body_hem_margin, sleeve_length, sleeve_sewing_margin, sleeve_hem_margin, half_chest_length, half_chest_sewing_margin, front_rise_length, front_rise_sewing_margin, west_band_length, west_band_sewing_margin, in_seam_length, in_seam_sewing_margin, in_seam_hem_margin, half_thai_length, half_thai_sewing_margin, total, marker_dia, marker_yds, marker_inch, gmts_pcs, marker_length, net_fab_cons from wo_pri_quo_fab_co_avg_con_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert9;die;
		
			$sql_insert10="insert into wo_pri_quo_fab_yarn_dtls_his(id, approved_no, quo_yarn_dtls_id, quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, supplier_id)
				select	
				'', $approved_string_dtls, id, quotation_id, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, cons_ratio, cons_qnty, rate, amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, supplier_id from wo_pri_quo_fab_yarn_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert10;die;
			
			$sql_insert11="insert into wo_pri_quo_sum_dtls_his( id, approved_no, quo_sum_dtls_id, quotation_id, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, wash_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select	
				'', $approved_string_dtls, id, quotation_id, fab_yarn_req_kg, fab_woven_req_yds, fab_knit_req_kg, fab_amount, yarn_cons_qnty, yarn_amount, conv_req_qnty, conv_charge_unit, conv_amount, trim_cons, trim_rate, trim_amount, emb_amount, wash_amount, comar_rate, comar_amount, commis_rate, commis_amount, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_sum_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert11;die;

			$sql_insert12="insert into wo_pri_quo_trim_cost_dtls_his( id, approved_no, quo_trim_dtls_id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted)
				select	
				'', $approved_string_dtls, id, quotation_id, trim_group, cons_uom, cons_dzn_gmts, rate, amount, apvl_req, nominated_supp, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_pri_quo_trim_cost_dtls where quotation_id in ($book_nos)";
			//echo $sql_insert12;die;
		}*/
		// print_r($approved_no_array);
		if($partial_approval == 1)
		{
			$updateData=$user_id_approval."*'".$pc_date_time."'";

			
			sql_multirow_update("wo_price_quotation_v3_mst","approved_by*approved_date",$updateData,"id",$booking_ids,1);
		}
		
		$rID=sql_multirow_update("wo_price_quotation_v3_mst","is_approved",$partial_approval,"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;
		
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=34 and mst_id in ($booking_ids)";
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
		
		/*if(count($approved_no_array)>0)
		{
			$rID3=execute_query($sql_insert,1);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
			
			$rID4=execute_query($sql_insert2,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			} 
			
			$rID5=execute_query($sql_insert3,1);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
			
			$rID6=execute_query($sql_insert4,1);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			}
			
			$rID7=execute_query($sql_insert5,1);
			if($flag==1) 
			{
				if($rID7) $flag=1; else $flag=0; 
			}  
			
			$rID8=execute_query($sql_insert6,1);
			if($flag==1) 
			{
				if($rID8) $flag=1; else $flag=0; 
			} 
			
			$rID9=execute_query($sql_insert7,1);
			if($flag==1) 
			{
				if($rID9) $flag=1; else $flag=0; 
			} 
			
			$rID10=execute_query($sql_insert8,1);
			if($flag==1) 
			{
				if($rID10) $flag=1; else $flag=0; 
			}
			
			$rID11=execute_query($sql_insert9,1);
			if($flag==1) 
			{
				if($rID11) $flag=1; else $flag=0; 
			} 
			
			$rID12=execute_query($sql_insert10,1);
			if($flag==1) 
			{
				if($rID12) $flag=1; else $flag=0; 
			}
			
			$rID13=execute_query($sql_insert11,1);
			if($flag==1) 
			{
				if($rID13) $flag=1; else $flag=0; 
			} 
			
			$rID14=execute_query($sql_insert12,1);
			if($flag==1) 
			{
				if($rID14) $flag=1; else $flag=0; 
			}   
		}*/
		//echo "21**".$flag;die;
		if($flag==1) $msg='19'; else $msg='21';
	}
	else // unapprove process start
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
		$rID=sql_multirow_update("wo_price_quotation_v3_mst","is_approved*ready_to_approved",'0*0',"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;
		$unapprove_reasons_arr = explode(',', $unapprove_reasons);
		$approval_ids_arr = explode(',', $approval_ids);
		$id_val = array_combine($approval_ids_arr, $unapprove_reasons_arr);
		// $id_val = array_combine($booking_ids_all, $unapprove_reasons_arr);
		// echo "<pre>";
		// print_r($id_val);
		// $size = count($booking_ids_all);
		foreach ($id_val as $key => $value) {
			$rID2 = execute_query("UPDATE approval_history SET current_approval_status=0, un_approved_reason=$value WHERE entry_form=34 and id=$key",1);
		}

		// die();
		// $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=34 and mst_id in ($booking_ids)";
		$rID2 = true;
		// $rID2=execute_query($query,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		//echo $flag;die;
		$response=$booking_ids;
		
		if($flag==1) $msg='20'; else $msg='22';
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
				$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.sequence_no from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id and b.is_deleted=0  and b.page_id=$menu_id order by sequence_no ASC";
				//echo $sql;
			 $arr=array (2=>$custom_designation,3=>$Department);
			 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
			?>
	        
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>


	<?
}// action SystemIdPopup end;

if($action == "top_botton_report") // print report
{
	// var_dump($_REQUEST);die();

	extract($_REQUEST);

	$quotation_id 	= str_replace("'", "", $txt_quotation_id);
	$company_name 	= str_replace("'", "", $cbo_company_name);
	$buyer_name 	= str_replace("'", "", $cbo_buyer_name);
	$style_ref 		= str_replace("'", "", $txt_style_ref);
	$quotation_date = str_replace("'", "", $txt_quotation_date);

	echo load_html_head_contents("Price Quotation V3", "../", 1, 1, '', '', '');
	$data = explode('*', $data);
    $buyer_name_arr = return_library_array("SELECT a.id, a.buyer_name from lib_buyer a, lib_buyer_party_type b, lib_buyer_tag_company c  where a.id=b.buyer_id and a.id=c.buyer_id and b.party_type=1 and c.tag_company=$company_name and a.status_active=1 and a.is_deleted =0 order by a.buyer_name", "id", "buyer_name");
	
    $agent_name_arr = return_library_array("SELECT a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) group by a.id,a.buyer_name order by buyer_name", "id", "buyer_name");
	
	$company_library = return_library_array("select id,company_name  from lib_company where status_active =1 and is_deleted=0", "id", "company_name");
	
	
	$sql = "SELECT a.id, a.sys_no_prefix, a.sys_prefix_num, a.system_id, a.company_id, a.team_member, a.buyer_id, a.quot_date, a.agent, a.style_ref, a.gmts_item, a.fabrication, a.color, a.yarn_count, a.cons_size, a.order_qty, a.measurment_basis, a.yarn_cons, a.yarn_unit_price, a.yarn_total, a.knit_fab_purc_cons, a.knit_fab_purc_price, a.knit_fab_purc_total, a.woven_fab_purc_cons, a.woven_fab_purc_price, a.woven_fab_purc_total, a.yarn_dye_crg_cons, a.yarn_dye_crg_price, a.yarn_dye_crg_total, a.knit_crg_cons, a.knit_crg_unit_price, a.knit_crg_total, a.dye_crg_cons, a.dye_crg_unit_price, a.dye_crg_total, a.spandex_amt, a.spandex_cons, a.spandex_unit_price, a.spandex_total, a.aop_cons, a.aop_price, a.aop_total, a.collar_cuff_cons, a.collar_cuff_unit_price, a.collar_cuff_total, a.print_cons, a.print_unit_price, a.print_total, a.gmts_wash_dye_cons, a.gmts_wash_dye_price, a.gmts_wash_dye_total, a.access_price_cons, a.access_price_unit_price, a.access_price_total, a.zipper_cons, a.zipper_unit_price, a.zipper_total, a.button_cons, a.button_unit_price, a.button_total, a.test_cons, a.test_unit_price, a.test_total, a.cm_cons, a.cm_unit_price, a.cm_total, a.inspec_cost_cons, a.inspec_cost_unit_price, a.inspec_cost_total, a.freight_cons, a.freight_unit_price, a.freight_total, a.carrier_cost_cons, a.carrier_cost_unit_price, a.carrier_cost_total, a.others_column_caption, a.others_cost_cons, a.others_cost_unit_price, a.others_cost_total, a.comm_cost_cons, a.comm_cost_price, a.comm_cost_total, a.remarks, a.fact_u_price, a.agnt_comm, a.agnt_comm_tot, a.local_comm, a.local_comm_tot, a.final_offer_price, a.order_conf_price, a.order_conf_date, a.embro_cons, a.embro_unit_price, a.embro_total, a.uom_yarn, a.uom_knit_fab_purc, a.uom_woven_fab_purc, a.uom_yarn_dye_crg, a.uom_knit_crg, a.uom_dye_crg, a.uom_spandex, a.uom_aop, a.uom_collar_cuff, a.uom_print, a.uom_embro, a.uom_wash_gmts_dye, a.uom_access_price, a.uom_zipper, a.uom_button, a.uom_test, a.uom_cm, a.uom_inspec_cost, a.uom_freight, a.uom_carrier_cost, a.uom_others, a.uom_others2, a.uom_others3, a.size_range, a.others_column_caption2, a.others_cost_cons2, a.others_cost_unit_price2, a.others_cost_total2, a.others_column_caption3, a.others_cost_cons3, a.others_cost_unit_price3, a.others_cost_total3,a.is_approved, 
	b.id as dtls_id, b.garments_type, b.fabric_source, b.fabric_natu, b.body_length, b.sleeve_length, b.inseam_length, b.front_back_rise, b.sleev_rise_allow, b.chest, b.thigh, b.chest_thigh_allow, b.gsm, b.body_fabric, b.wastage, b.net_body_fabric, b.rib, b.ttl_top_bottom_cons
	FROM wo_price_quotation_v3_mst a, wo_price_quotation_v3_dtls b where a.id=b.mst_id and a.id=$quotation_id and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0";
	
	 
	
	
	//echo $sql;die;
	$result = sql_select($sql);
	$dtlsDataArray =array();
	foreach($result as $row)
	{
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['fabric_source']=$row[csf('fabric_source')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['fabric_natu']=$row[csf('fabric_natu')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['body_length']=$row[csf('body_length')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['sleeve_length']=$row[csf('sleeve_length')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['front_back_rise']=$row[csf('front_back_rise')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['sleev_rise_allow']=$row[csf('sleev_rise_allow')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['chest']=$row[csf('chest')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['thigh']=$row[csf('thigh')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['chest_thigh_allow']=$row[csf('chest_thigh_allow')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['gsm']=$row[csf('gsm')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['body_fabric']=$row[csf('body_fabric')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['wastage']=$row[csf('wastage')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['net_body_fabric']=$row[csf('net_body_fabric')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['rib']=$row[csf('rib')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['ttl_top_bottom_cons']=$row[csf('ttl_top_bottom_cons')];
		$dtlsDataArray[$row[csf('garments_type')]][$row[csf('dtls_id')]]['inseam_length']=$row[csf('inseam_length')];
	}
	
	
	
	
	

	$sub_total = $result[0][csf('yarn_total')]+$result[0][csf('knit_fab_purc_total')]+$result[0][csf('woven_fab_purc_total')]+$result[0][csf('yarn_dye_crg_total')]+$result[0][csf('knit_crg_total')]+$result[0][csf('dye_crg_total')]+$result[0][csf('spandex_total')]+$result[0][csf('aop_total')]+$result[0][csf('collar_cuff_total')]+$result[0][csf('print_total')]+$result[0][csf('embro_total')]+$result[0][csf('gmts_wash_dye_total')]+$result[0][csf('access_price_total')]+$result[0][csf('zipper_total')]+$result[0][csf('button_total')]+$result[0][csf('test_total')]+$result[0][csf('cm_total')]+$result[0][csf('inspec_cost_total')]+$result[0][csf('freight_total')]+$result[0][csf('carrier_cost_total')]+$result[0][csf('others_cost_total')]+$result[0][csf('others_cost_total2')]+$result[0][csf('others_cost_total3')];
	
	$tot_factory_cost =$sub_total+$result[0][csf('comm_cost_total')];
		
	$measurement_basis_arr = array(1=>"Cad Bassis", 2=>"Measurement Basis");	
		
        ?> 
	<div style="margin: 0 auto;">
        <div style="width:210mm;">
            <table cellspacing="0" border="0" style="width:210mm; margin-right:-10px;">
                <tr class="form_caption">
                    <?
                    $data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$company_name' and form_name='company_details' and is_deleted=0 and file_type=1");
                    ?>
                    <td rowspan="2" align="left" width="50">
					<?
                    foreach ($data_array as $img_row) 
                    {
                        ?>
                        <img src='../<? echo $img_row[csf('image_location')]; ?>' height='50' width='50' align="middle"/>
                        <?
                    }
					
                    ?>
                    </td>
                    <td colspan="8" align="center" style="font-size:25px;">
                        <strong> <? echo $company_library[$company_name]; ?></strong>
                        
                        
                    </td>
                    <td rowspan="2" align="left" width="50">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="8" align="center" style="font-size:18px">
                   		 <? echo show_company($company_name, '', array('city')); ?>
                         <br>
                    	<strong><u> Price Quotation V3</u></strong>
                    </td>
                </tr>
                <?
                $msg = "";
                if($result[0][csf('is_approved')] != 0)
                {
                	$msg = ($result[0][csf('is_approved')] == 1) ? "This Quotation is approved!" : "This Quotation is partial approved!";
	                ?>
	                <tr>
	                	<td colspan="10" align="center" style="font-size:16px;color: red;"> <? echo $msg;?> </td>
	                </tr>
	                <?
            	}
                ?>
            </table>
        </div>
        <br>
		<div style="width:210mm;">
            <table style="width:210mm; text-align:center; font-size:13px;" cellspacing="0" border="1" rules="all" class="rpt_table">
            	<tr>
                	<td colspan="14" bgcolor="#dddddd" align="left"><b>System No: <? echo $result[0][csf('system_id')]; ?></b></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">Buyer</td>
                    <td align="left"><? echo  $buyer_name_arr[$result[0][csf('buyer_id')]]; ?></td>
                    <td colspan="2" align="left">Consumption Basis</td>
                    <td colspan="4" align="left"><? echo $measurement_basis_arr[$result[0][csf('measurment_basis')]]; ?></td>
                    <td colspan="2" align="left">Date</td>
                    <td colspan="3" align="left"><? echo change_date_format($result[0][csf('quot_date')],'dd-mm-yyyy'); ?></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">Agent</td>
                    <td align="left"><? echo $agent_name_arr[$result[0][csf('agent')]]; ?></td>
                    <td colspan="2" align="left">Size Range</td>
                    <td colspan="4" align="left"><? echo $result[0][csf('size_range')]; ?></td>
                    <td colspan="2" align="left">Style Ref</td>
                    <td colspan="3" align="left"><? echo $result[0][csf('style_ref')]; ?></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">Description</td>
                    <td align="left"><? echo $result[0][csf('gmts_item')]; ?></td>
                    <td colspan="2" align="left">Team Member</td>
                    <td colspan="4" align="left"><? echo $result[0][csf('team_member')]; ?></td>
                    <td colspan="2" align="left">Fabrication</td>
                    <td colspan="3" align="left"><? echo $result[0][csf('fabrication')]; ?></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">GSM</td>
                    <td align="left"><? echo $result[0][csf('gsm')]; ?></td>
                    <td colspan="2" align="left">Consumption Size</td>
                    <td colspan="4" align="left"><? echo $result[0][csf('cons_size')]; ?></td>
                    <td colspan="2" align="left">Color</td>
                    <td colspan="3" align="left"><? echo $result[0][csf('color')]; ?></td>
                </tr>
                <tr>
                    <td colspan="2" align="left">Require Yarn Count</td>
                    <td colspan="7" align="left"><? echo $result[0][csf('yarn_count')]; ?></td>
                    
                    <td colspan="2" align="left">Order Qty</td>
                    <td colspan="3" align="left"><? echo $result[0][csf('order_qty')]; ?></td>
                </tr>
                <tr>
                    <td colspan="14" bgcolor="#dddddd" align="left"><b>Fabric Consumption / Dz</b></td>
                </tr>
                
                <?
				$subTotalCons = 0;
				$subTotalBottom = 0;
				$grandTota =0;
				
				foreach($dtlsDataArray as $gmtsType => $gmtsData)
				{
					// Top == 1
					// Bottom = 20
					if($gmtsType==1)
					{
						?>
						<tr style="font-weight:bold;">
                        	<td>Garment<br>Type</td>
							<td>Source</td>
							<td>Fabric Nature</td>
							<td>Body Length</td>
							<td>Sleeve Length</td>
							<td>Allow</td>
							<td>1/2 Chest</td>
							<td>Allow</td>
							<td>GSM</td>
							<td>Body Fabric</td>
							<td>Wastage<br>%</td>
							<td>Net Body<br>Fabric</td>
							<td>Rib<br>%</td>
                            <td>TTL Top<br>Cons</td>
							
						</tr>
                        <? foreach($gmtsData as $row)
						{ 
							$subTotalCons += $row['ttl_top_bottom_cons'];
							$grandTota += $row['ttl_top_bottom_cons'];
				
						?>
						<tr  valign="middle">
                        	<td><? echo $body_part_type[$gmtsType]; ?></td>
							<td><? echo $fabric_source[$row['fabric_source']]; ?></td>
							<td><? echo $item_category[$row['fabric_natu']]; ?></td>
							<td><? echo $row['body_length']; ?></td>
							<td><? echo $row['sleeve_length']; ?></td>
							<td><? echo $row['sleev_rise_allow']; ?></td>
							<td><? echo $row['chest']; ?></td>
							<td><? echo $row['chest_thigh_allow']; ?></td>
							<td><? echo $row['gsm']; ?></td>
							<td><? echo $row['body_fabric']; ?></td>
							<td><? echo $row['wastage']; ?></td>
							<td><? echo number_format($row['net_body_fabric'],4);  ?></td>
							<td><? echo $row['rib']; ?></td>
							<td align="right"><? echo $row['ttl_top_bottom_cons']; ?></td>
						</tr>
                         <? 
						 }
						 ?>
                         <tr valign="middle">
                        	<td colspan="13" align="right"><strong>Subtotal Top Consumption </strong></td>
							<td align="right"><strong><?  echo number_format($subTotalCons,4);  ?></strong></td>
						</tr>
                         <?
						 
					}
					else
					{
						
				
						?>
						<tr style="font-weight:bold;">
                        	<td>Garment<br>Type</td>
							<td>Source</td>
							<td>Fabric Nature</td>
							<td>TTL Side/<br>Inseam Length</td>
							<td>Front/ <br>Back Rise</td>
							<td>Allow</td>
							<td>1/2 Thigh</td>
							<td>Allow</td>
							<td>GSM</td>
							<td>Body Fabric<br>Cons</td>
							<td>Wastage<br>%</td>
							<td>Net Body<br>Fabric</td>
							<td>Rib<br>%</td>
                            <td>TTL Top<br>Cons</td>
							
						</tr>
                        <? foreach($gmtsData as $row)
						{ 
						$subTotalBottom += $row['ttl_top_bottom_cons'];
						$grandTota += $row['ttl_top_bottom_cons'];
						?>
						<tr valign="middle">
                        	<td><? echo $body_part_type[$gmtsType]; ?></td>
							<td><? echo $fabric_source[$row['fabric_source']]; ?></td>
							<td><? echo $item_category[$row['fabric_natu']]; ?></td>
							
							<td><? echo $row['inseam_length']; ?></td>
							<td><? echo $row['front_back_rise']; ?></td>
							<td><? echo $row['sleev_rise_allow']; ?></td>
							<td><? echo $row['thigh']; ?></td>
							<td><? echo $row['chest_thigh_allow']; ?></td>
							<td><? echo $row['gsm']; ?></td>
							<td><? echo $row['body_fabric']; ?></td>
							<td><? echo $row['wastage']; ?></td>
							<td><?  echo number_format($row['net_body_fabric'],4);  ?></td>
							<td><? echo $row['rib']; ?></td>
							<td align="right"><? echo $row['ttl_top_bottom_cons']; ?></td>
						</tr>
						<?
						}
						?>
                         <tr valign="middle">
                        	<td colspan="13" align="right"><strong>Subtotal Bottom Consumption </strong></td>
							<td align="right"><strong><? echo number_format($subTotalBottom,4); ?></strong></td>
						</tr>
                        <tr valign="middle">
                        	<td colspan="13" align="right"><strong>Grand Total Consumption </strong></td>
							<td align="right"><strong><? echo number_format($grandTota,4); ?></strong></td>
						</tr>
                         <?
					}
				}
				
				?>
            </table>
		</div>
         <br/>
         <div style="width:210mm;">
			<div style='width:65%;float:left;padding-right:10px;'>
			<table cellspacing='0' border='1' class='rpt_table' rules='all' align='left' style=' text-align:center; font-size:11px; font-family: Arial Narrow,Arial,sans-serif;' >
                <thead>
                    <th width="">Costing Head</th>
                    <th width="">UOM</th>
                    <th width="100">Consumption</th>
                    <th width="100">Unit Price</th>
                    <th width="100">Total Price</th>
                </thead>
                <tbody class="" id="costing_dtls">
                	<? 
					 if($result[0][csf('yarn_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Yarn Price</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_yarn')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('yarn_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('yarn_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('yarn_total')],4); 
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('knit_fab_purc_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Knit Fabric Purchase</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_knit_fab_purc')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('knit_fab_purc_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('knit_fab_purc_price')],2); ?></td>
                        <td align="right"><? 
						 echo number_format($result[0][csf('knit_fab_purc_total')],4); 
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('woven_fab_purc_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Woven Fabric Purchase</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_woven_fab_purc')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('woven_fab_purc_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('woven_fab_purc_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('woven_fab_purc_total')],4); 
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('yarn_dye_crg_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Yarn Dyeing Charge</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_yarn_dye_crg')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('yarn_dye_crg_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('yarn_dye_crg_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('yarn_dye_crg_total')],4);  
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('knit_crg_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Knitting Charge</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_knit_crg')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('knit_crg_cons')],4);  ?></td>
                        <td align="right"><? echo number_format($result[0][csf('knit_crg_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('knit_crg_total')],4);
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('dye_crg_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Dyeing Charge</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_dye_crg')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('dye_crg_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('dye_crg_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('dye_crg_total')],4);
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('spandex_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Spandex</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_spandex')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('spandex_amt')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('spandex_unit_price')],2); ?></td>
                        <td align="right"><? 
						 echo number_format($result[0][csf('spandex_total')],4);
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('aop_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">AOP</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_aop')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('aop_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('aop_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('aop_total')],4);
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('collar_cuff_unit_price')]*1 > 0){ 
					 ?> 
                    <tr>
                        <td align="left">Flat Knit Collar & Cuff</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_collar_cuff')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('collar_cuff_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('collar_cuff_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('collar_cuff_total')],4);
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('print_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Print</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_print')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('print_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('print_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('print_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('embro_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Embroidery</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_embro')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('embro_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('embro_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('embro_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('gmts_wash_dye_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Wash/Gmts Dyeing</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_wash_gmts_dye')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('gmts_wash_dye_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('gmts_wash_dye_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('gmts_wash_dye_total')],4);  
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('access_price_unit_price')]*1 > 0){ 
					 ?> 
                    <tr>
                        <td align="left">Accessories Price</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_access_price')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('access_price_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('access_price_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('access_price_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('zipper_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Zipper</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_zipper')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('zipper_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('zipper_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('zipper_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('button_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Button</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_button')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('button_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('button_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('button_total')],4);  
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('test_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Test</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_test')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('test_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('test_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('test_total')],4);
						?></td>
                    </tr> 
                    <? 
					 }
					 if($result[0][csf('cm_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">CM</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_cm')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('cm_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('cm_unit_price')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('cm_total')],4);  
						?></td>
                    </tr>
                    <? 
					 }
					 if($result[0][csf('inspec_cost_unit_price')]*1 > 0){ 
					 ?>
                    <tr>
                        <td align="left">Inspection Cost</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_inspec_cost')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('inspec_cost_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('inspec_cost_unit_price')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('inspec_cost_total')],4); ?></td>
                    </tr>
                     <? 
					 }
					 if($result[0][csf('freight_unit_price')]*1 > 0){ 
					 ?>
                   
                    <tr>
                        <td align="left">Freight</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_freight')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('freight_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('freight_unit_price')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('freight_total')],4); ?></td>
                    </tr>
                     <? 
					 }
					 if($result[0][csf('carrier_cost_unit_price')]*1 > 0){ 
					 ?>
                    
                    <tr>
                        <td align="left">Currier Cost</td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_carrier_cost')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('carrier_cost_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('carrier_cost_unit_price')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('carrier_cost_total')],4); ?></td>
                    </tr> 
					<? 
					 }
					if($result[0][csf('others_cost_unit_price')]*1 > 0){ 
					?>
                    <tr>
                        <td align="left"><? echo $result[0][csf('others_column_caption')]; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_others')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_cons')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_unit_price')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_total')],4);  ?></td>
                    </tr>
                     <? }if($result[0][csf('others_cost_unit_price2')]*1 > 0){ 
					?>
                    <tr>
                        <td align="left"><? echo $result[0][csf('others_column_caption2')]; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_others2')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_cons2')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_unit_price2')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_total2')],4);  ?></td>
                    </tr>
                     <? }if($result[0][csf('others_cost_unit_price3')]*1 > 0){ 
					?>
                    <tr>
                        <td align="left"><? echo $result[0][csf('others_column_caption3')]; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$result[0][csf('uom_others3')]]; ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_cons3')],4); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_unit_price3')],2); ?></td>
                        <td align="right"><? echo number_format($result[0][csf('others_cost_total3')],4);  ?></td>
                    </tr>
                     <? }?>
                    
                    <tr>
                        <td align="left" colspan="4"> <strong>Sub Total</strong> </td>
                        <td align="right"><strong><? echo number_format($sub_total,4); ?></strong></td>
                    </tr> 
                    <tr>
                        <td align="left">Commercial Cost</td>
                        <td colspan="3"><? echo number_format($result[0][csf('comm_cost_cons')],2); ?></td>
                        <td align="right"><? 
						echo number_format($result[0][csf('comm_cost_total')],4);  
						?></td>
                    </tr> 
                    <tr>
                        <td align="left" colspan="4"><strong>Total Factory Cost/ Dz </strong></td>
                        <td align="right"><strong><? echo number_format($tot_factory_cost,4); ?></strong></td>
                    </tr> 
                </tbody>
            </table>
            </div>
            <div style="width:30%;float:right;">
			<table cellspacing='0' border='1' class='rpt_table' id='' rules='all' align='left' style='width:64mm; text-align:center;font-size:11px; font-family: Arial Narrow,Arial,sans-serif;' >
                <thead>
                    <th colspan="2">Offer Price/ Unit (FOB)</th>
                </thead>
                <tbody class="" id="costing_dtls">
                    <tr>
                        <td align="left"width="150">Factory Unit Price</td>
                        <td align="right"><? 
						//$final_offer_price = $tot_factory_cost/12;
						echo number_format($result[0][csf('fact_u_price')],4); 
						?></td>
                    </tr> 
                    <tr>
                        <td align="left"width="150">Agent Commission</td>
                        <td align="right"><? 
						//$final_offer_price = $tot_factory_cost/12;
						echo number_format($result[0][csf('agnt_comm_tot')],4); 
						?></td>
                    </tr> 
                    <tr>
                        <td align="left"width="150">Local Commission</td>
                        <td align="right"><? 
						//$final_offer_price = $tot_factory_cost/12;
						echo number_format($result[0][csf('local_comm_tot')],4); 
						?></td>
                    </tr> 
                    
                    <tr>
                        <td align="left" >Final Offer Price</td>
                        <td align="right"><strong><? echo number_format($result[0][csf('final_offer_price')],4); ?></strong></td>
                    </tr> 
                    <tr>
                        <td colspan="2"><strong>Order Confirmed Price</strong></td>
                    </tr> 
                    <tr>
                        <td colspan="2" align="right"><strong><? echo number_format($result[0][csf('order_conf_price')],4); ?></strong></td>
                    </tr> 
                    <tr>
                        <td colspan="2"><strong>Order Confirmed Date</strong></td>
                    </tr> 
                    <tr>
                        <td colspan="2"><strong><? echo change_date_format($result[0][csf('order_conf_date')],'dd-mm-yyyy'); ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:left; height:60px;  text-align: justify; text-justify: inter-word; " valign="top"><strong>Remarks : </strong><? echo $result[0][csf('remarks')]; ?></td>
                    </tr>
                </tbody>
            </table>
            </div>
		</div>
        <br>
        <div style="padding-top:500px; width:210mm;">
            <table cellspacing="0" style="width:210mm;" border="0">
                <tr align="center">
                    <td colspan="4" align="left" style="padding-left:40px;">Prepared By</td>
                    <td colspan="2" align="right" style="padding-right:40px;">Approved By</td>
                </tr>
            </table>
		</div>
     </div>   
	<?
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
                    if($db_type==0)
	                 {
	                    $sql_img = "select id,master_tble_id,image_location
	                    from common_photo_library  
	                    where master_tble_id='$id' and form_name='quotation_entry' limit 1";
	                 }
	              	if($db_type==2)
	                 {
	                $sql_img = "select id,master_tble_id,image_location from common_photo_library  
	                    where master_tble_id='$id' and form_name='quotation_entry'  ";
	                 }
	                 //echo $sql_img; die;
	    
	                $data_array_img=sql_select($sql_img);
	                if(count($data_array_img) > 0){
	                    foreach($data_array_img as $inf_img)
	                    {
							$i++;
	                    ?>
	                    	<td align="center"><img  src='../../<? echo $inf_img[csf("image_location")]; ?>' height='300' width='200'/></td>
	                    <?
							if($i%2==0) echo "</tr><tr>";
	                    }
                	}
                	else{ ?>
                		<td align="center"><img  src='../../images/no-image.jpg' height='300' width='200'/></td>
                	<? }
                    ?>
                </tr>
            </table>
        </div>	
	</fieldset>     
	<?
	exit();
}

?>