<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$approval_type=str_replace("'","",$cbo_approval_type);
	//$user_id=151;
	//echo "select sequence_no from electronic_approval_setup where page_id=$menu_id and user_id=$user_id and  is_deleted=0 ";
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	
	$buyer_ids_array=array();
	$buyerData=sql_select("select user_id, sequence_no, buyer_id from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2");
	foreach($buyerData as $row)
	{
		$buyer_ids_array[$row[csf('user_id')]]['u']=$row[csf('buyer_id')];
		$buyer_ids_array[$row[csf('sequence_no')]]['s']=$row[csf('buyer_id')];
	}

	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Sample Booking (Without order).</font>";
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
	
	if($approval_type==0)
	{
		//$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0");
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
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
			$sql="select a.id,a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no,a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a where company_id=$company_name and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and $orderBy_cond(a.is_approved,0)=$approval_type and a.ready_to_approved=1 $buyer_id_cond and (a.entry_form_id=140 or a.entry_form_id is null or a.entry_form_id=0) order by $orderBy_cond(a.update_date, a.insert_date) desc";
			//echo $sql;
		}
		else if($sequence_no=="")
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
			
			if($db_type==0)
			{
				//$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");
				
				$seqSql="select group_concat(sequence_no) as sequence_no_by,
 group_concat(case when bypass=2 and buyer_id<>'' then buyer_id end) as buyer_ids from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
				$seqData=sql_select($seqSql);
				
				$sequence_no_by=$seqData[0][csf('sequence_no_by')];
				$buyerIds=$seqData[0][csf('buyer_ids')];
				
				if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.buyer_id not in($buyerIds)";
			
				$booking_id=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by) and b.entry_form=9 and b.current_approval_status=1","booking_id");
				
				$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=9 and b.current_approval_status=1","booking_id");
			}
			else
			{
				//$sequence_no_by=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0","sequence_no");

				$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by, LISTAGG(case when bypass=2 and buyer_id is not null then buyer_id end, ',') WITHIN GROUP (ORDER BY null) as buyer_ids from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
				$seqData=sql_select($seqSql);
				
				$sequence_no_by=$seqData[0][csf('sequence_no_by')];
				$buyerIds=$seqData[0][csf('buyer_ids')];
				
				if($buyerIds=="") $buyerIds_cond=""; else $buyerIds_cond=" and a.buyer_id not in($buyerIds)";
	
				$booking_id=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(2,3,13) and b.sequence_no in ($sequence_no_by) and b.entry_form=9 and b.current_approval_status=1","booking_id");
				$booking_id=implode(",",array_unique(explode(",",$booking_id)));
				
				$booking_id_app_byuser=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(2,3,13) and b.sequence_no=$user_sequence_no and b.entry_form=9 and b.current_approval_status=1","booking_id");
				$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
			}

			$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
			$booking_id=implode(",",$result);
			
			/*$booking_id_cond="";
			if($booking_id_app_byuser!="") $booking_id_cond=" and a.id not in($booking_id_app_byuser)";
			if($booking_id!="") $booking_id_cond.=" or (a.id in($booking_id))";*/
			if($booking_id!="")
			{
				$sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id,a.insert_date from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and $orderBy_cond(a.is_approved,0)=$approval_type and a.ready_to_approved=1 $buyer_id_cond $buyer_id_cond2 $buyerIds_cond group by a.id,a.booking_no_prefix_num, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id,a.insert_date
					union all
					select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id,a.insert_date from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and $orderBy_cond(a.is_approved,0)=1 and a.ready_to_approved=1 and a.id in($booking_id) $buyer_id_cond $buyer_id_cond2 group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id,a.insert_date order by insert_date desc";
			}
			else
			{
				$sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and $orderBy_cond(a.is_approved,0)=$approval_type and a.ready_to_approved=1 $buyer_id_cond $buyer_id_cond2 $buyerIds_cond group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id,a.insert_date order by $orderBy_cond(a.update_date, a.insert_date) desc";
			}
			//echo $sql;
		}
		
		else
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id in($buyer_ids)";
			//$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and  is_deleted=0");
			$user_sequence_no=$user_sequence_no-1;
			
			if($sequence_no==$user_sequence_no) $sequence_no_by_pass='';
			else
			{
				if($db_type==0)
				{
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");// and bypass=1
				}
			}
			
			if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
			
			$sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, b.id as approval_id ,a.is_approved, b.sequence_no, b.approved_by from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form=9 and a.company_id=$company_name and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved=1 $buyer_id_cond $buyer_id_cond2 $sequence_no_cond order by $orderBy_cond(a.update_date, a.insert_date) desc";
		}
	}
	else
	{
		$sequence_no_cond=" and b.sequence_no='$user_sequence_no'";
		 $sql="select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, b.id as approval_id ,a.is_approved, b.sequence_no, b.approved_by from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form=9 and a.company_id=$company_name and a.item_category in(2,3,13) and a.status_active=1 and a.ready_to_approved=1 and a.is_deleted=0 and b.current_approval_status=1 and a.is_approved=1 $buyer_id_cond2 $sequence_no_cond order by $orderBy_cond(a.update_date, a.insert_date) desc";
	}

	   //echo $sql;die;
	
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:840px; margin-top:10px">
        <legend>Sample Booking (Without Order) Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="130">Booking No</th>
                    <th width="80">Type</th>
                    <th width="100">Booking Date</th>
                    <th width="125">Buyer</th>
                    <th width="160">Supplier</th>
                    <th>Delivery Date</th>
                </thead>
            </table>
            <div style="width:820px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="802" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1; $all_approval_id='';
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            { 
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$value=$row[csf('id')];
								/*if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
									$app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='9' order by id desc limit 0,1");
									$value=$row[csf('id')]."**".$app_id;
								}*/
								
								if($row[csf('booking_type')]==4) $booking_type="Sample";
								
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
                                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                        <td width="50" align="center" valign="middle">
                                            <input type="checkbox" id="tbl_<? echo $i;?>" />
                                            <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                            <input id="booking_no_<? echo $i;?>" name="booking_no]" type="hidden" value="<? echo $row[csf('booking_no')]; ?>" />
                                            <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                            <input id="<? echo strtoupper($row[csf('booking_no')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
                                        </td>   
                                        <td width="40" align="center"><? echo $i; ?></td>
                                        <td width="130">
                                            <p><a href='##' style='color:#000' onclick="generate_worder_report('<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('company_id')]; ?>,<? echo $row[csf('is_approved')]; ?>)"><? echo $row[csf('prefix_num')]; ?></a></p>
                                        </td>
                                        <td width="80" align="center"><p><? echo $booking_type; ?></p></td>
                                        <td width="100" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
                                        <td width="125"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                                        <td width="160"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                                        <td align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
                                    </tr>
                                    <?
                                    $i++;
								}
								
								if($all_approval_id!="")
								{
									$con = connect();
									$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
									disconnect($con);
								}
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="820" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
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
	//$user_id=151;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and  is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and  is_deleted=0");
	
	if($approval_type==0)
	{
		$response=$booking_ids;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip"; 
		$id=return_next_id( "id","approval_history", 1 ) ;
		
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=9 group by mst_id","mst_id","approved_no");
		
		$approved_status_arr = return_library_array("select id, is_approved from wo_non_ord_samp_booking_mst where id in($booking_ids)","id","is_approved");

		/*$rID=sql_multirow_update("wo_non_ord_samp_booking_mst","is_approved",1,"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;
		
		if($approval_ids!="")
		{
			$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
			if($flag==1) 
			{
				if($rIDapp) $flag=1; else $flag=0; 
			} 
		}*/
		
		/*if($user_sequence_no==$min_sequence_no)
		{
			$rID=sql_multirow_update("wo_non_ord_samp_booking_mst","is_approved",1,"id",$booking_ids,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
			if($rID) $flag=1; else $flag=0;
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
			
			$data_array.="(".$id.",9,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id.",'".$pc_date_time."','".$user_ip."')"; 
				
			$id=$id+1;
			
			/*$approved_no=return_field_value("max(approved_no)","approval_history","mst_id=$booking_id and entry_form=9");
			if($user_sequence_no==$min_sequence_no) $approved_no=$approved_no+1;*/
		
			/*if($i!=0) $data_array.=",";
			
			$data_array.="(".$id.",9,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id.",'".$pc_date_time."')"; 
			
			$approved_no_array[$val]=$approved_no;
				
			$id=$id+1;*/
		}
		
		//echo "insert into approval_history (".$field_array.") Values ".$data_array."";die;
		/*$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} */
		
		
		if(count($approved_no_array)>0)
		{
			$approved_string="";
			
			if($db_type==0)
			{
				foreach($approved_no_array as $key=>$value)
				{
					$approved_string.=" WHEN $key THEN $value";
				}
			}
			else
			{
				foreach($approved_no_array as $key=>$value)
				{
					$approved_string.=" WHEN TO_NCHAR($key) THEN '".$value."'";
				}
			}
			
			$approved_string_mst="CASE booking_no ".$approved_string." END";
			$approved_string_dtls="CASE booking_no ".$approved_string." END";
			
			 $sql_insert="insert into wo_nonord_samboo_msthtry(id, approved_no, booking_id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
				select	
				'', $approved_string_mst, id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_non_ord_samp_booking_mst where booking_no in ($booking_nos) and (entry_form_id=140 or entry_form_id is null or entry_form_id=0)";
					
			/*$rID3=execute_query($sql_insert,0);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} */
			
			
			
			 $sql_insert_dtls="insert into wo_nonor_sambo_dtl_hstry(id, approved_no, booking_dtls_id, booking_no, style_id, sample_type, body_part, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, fabric_color, item_size, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, yarn_breack_down, process_loss_method, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
				select	
				'', $approved_string_dtls, id, booking_no, style_id, sample_type, body_part, color_type_id, lib_yarn_count_deter_id, construction, composition, fabric_description, gsm_weight, fabric_color, item_size, dia_width, finish_fabric, process_loss, grey_fabric, rate, amount, yarn_breack_down, process_loss_method, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_non_ord_samp_booking_dtls where booking_no in ($booking_nos)";
					
			/*$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}*/
			
			$sql_insert_yarn_cons="insert into wo_nonord_samyar_dtlhstry(id, approved_no, wo_nonord_sam_yarndtl_id,wo_non_ord_samp_book_dtls_id,booking_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted) 
				select	
				'', $approved_string_dtls, id,wo_non_ord_samp_book_dtls_id,booking_no,count_id,copm_one_id,percent_one,type_id,cons_ratio,cons_qnty,rate,amount,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from wo_non_ord_samp_yarn_dtls where booking_no in ($booking_nos)";
					
			/*$rID5=execute_query($sql_insert_yarn_cons,1);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} */
			
			//echo '21**'.$sql_insert;die;
		}
		//echo '21**'.$sql_insert;die;
		$rID=sql_multirow_update("wo_non_ord_samp_booking_mst","is_approved",1,"id",$booking_ids,1); 
		if($rID) $flag=1; else $flag=0;
		
		if($approval_ids!="")
		{
			$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,1);
			if($flag==1) 
			{
				if($rIDapp) $flag=1; else $flag=0; 
			} 
		}
		
		$rID2=sql_insert("approval_history",$field_array,$data_array,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		
		if(count($approved_no_array)>0)
		{
			$rID3=execute_query($sql_insert,1);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
			
			$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}

			$rID5=execute_query($sql_insert_yarn_cons,1);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}
		
		if($flag==1) $msg='19'; else $msg='21';
		
		//echo $rID.','.$rID2.','.$rID3.','.$rID4.','.$rID5;
	}
	else
	{
		/*$booking_ids_all=explode(",",$booking_ids);
		
		$booking_ids=''; $app_ids='';
		
		foreach($booking_ids_all as $value)
		{
			$data = explode('**',$value);
			$booking_id=$data[0];
			$app_id=$data[1];
			
			if($booking_ids=='') $booking_ids=$booking_id; else $booking_ids.=",".$booking_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}*/
		
		$rID=sql_multirow_update("wo_non_ord_samp_booking_mst","is_approved*ready_to_approved","0*0","id",$booking_ids,1);
		if($rID) $flag=1; else $flag=0;
		
		//$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,1);
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=9 and mst_id in ($booking_ids)";
		$rID2=execute_query($query,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
			
		$data=$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$approval_ids,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
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
	
	if($db_type==2 || $db_type==1 )
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

?>