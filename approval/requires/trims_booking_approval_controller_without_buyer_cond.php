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
$menu_id=$_SESSION['menu_id'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
	
}

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
$po_number_arr=return_library_array("select id,po_number from wo_po_break_down", "id", "po_number");
$trim_group= return_library_array("select id, item_name from lib_item_group",'id','item_name');

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));  
	
	$sequence_no='';
	 $company_name=str_replace("'","",$cbo_company_name); 
	 $cbo_booking_type=str_replace("'","",$cbo_booking_type);

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
		$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$approval_type=str_replace("'","",$cbo_approval_type);
		
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and  is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and  is_deleted=0");
	//$buyer_arr_booking=return_library_array( "select  a.booking_no from wo_booking_mst a ,wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 ", "id", "buyer_name"  );
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Trims Booking.</font>";
		die;
	}

	
	
	if($approval_type==0)
	{

	    $sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0");

		if($user_sequence_no==$min_sequence_no)
		{
			if($cbo_booking_type==1) //With Order
			{
				if($db_type==0)
					{
					$sql="select a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type group by a.id ";
					}
				if($db_type==2)
					{
					  $sql="select a.id, a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type    group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.job_no, a.is_approved, a.po_break_down_id";
					}
			}
			else  //WithOut Order
			{
				if($db_type==0)
					{
						$sql="select a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type group by a.id ";
					}
				if($db_type==2)
					{
					   $sql="select a.id, a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.is_approved, a.po_break_down_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4) and a.booking_type=5  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type    group by a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.is_approved, a.po_break_down_id";
					}
			}

		}
		
		else if($sequence_no=="")
		{
			if($db_type==0)
			{
				$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");
				
				if($cbo_booking_type==1) //With Order
				{
				$booking_id=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(4) and b.sequence_no in ($sequence_no_by) and b.entry_form=8 and b.current_approval_status=1","booking_id");
				
				$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1","booking_id");
				}
				else //Without Order
				{
				$booking_id=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(4) and  a.booking_type=5  and b.sequence_no in ($sequence_no_by) and b.entry_form=8 and b.current_approval_status=1","booking_id");
				
				$booking_id_app_byuser=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(4) and  a.booking_type=5 and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1","booking_id");	
				}
			}
			if($db_type==2)
			{
				$sequence_no_by=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");
				
				if($cbo_booking_type==1) //With Order
				{
				$booking_id=return_field_value("listagg(mst_id,',') within group (order by mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(4) and b.sequence_no in ($sequence_no_by) and b.entry_form=8 and b.current_approval_status=1","booking_id");
				$booking_id=implode(",",array_unique(explode(",",$booking_id)));
				
				$booking_id_app_byuser=return_field_value("listagg(mst_id,',') within group (order by mst_id) as booking_id","wo_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(4) and b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1","booking_id");            
				$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
				}
				else //Without Order
				{
				$booking_id=return_field_value("listagg(mst_id,',') within group (order by mst_id) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(4) and a.booking_type=5 and b.sequence_no in ($sequence_no_by) and b.entry_form=8 and b.current_approval_status=1","booking_id");
				$booking_id=implode(",",array_unique(explode(",",$booking_id)));
				
				$booking_id_app_byuser=return_field_value("listagg(mst_id,',') within group (order by mst_id) as booking_id","wo_non_ord_samp_booking_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.item_category in(4) and  a.booking_type=5 b.sequence_no=$user_sequence_no and b.entry_form=8 and b.current_approval_status=1","booking_id");            
				$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
				}
			}
			if($booking_id_app_byuser!="") $booking_id_cond=" and a.id not in($booking_id_app_byuser)";
			else if($booking_id!="") $booking_id_cond.=" or (a.id in($booking_id))";
			else $booking_id_cond="";
			if($cbo_booking_type==1) //With Order
				{
					if($db_type==0)
					{
						$sql="select a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type   group by a.id";
					}
					else if($db_type==2)
					{
						$sql="select a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type    group by a.id,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.job_no, a.is_approved, a.po_break_down_id";
					}
				}
				else //Without Order
				{
					if($db_type==0)
					{
						$sql="select a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type   group by a.id";
					}
					else if($db_type==2)
					{
						$sql="select a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_name and a.item_category in(4) and a.booking_type=5 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved=$approval_type    group by a.id,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date,a.job_no, a.is_approved, a.po_break_down_id";
					}	
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
				$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and  is_deleted=0");
			   }
			 if($db_type==2)
				{
				$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_id","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1 and  is_deleted=0");
			   }
			}
			
			if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and (b.sequence_no='$sequence_no' or b.sequence_no in ($sequence_no_by_pass))";
			if($cbo_booking_type==1) //With Order
				{
			 $sql="select a.id, a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, b.id as approval_id,a.is_approved from wo_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form=8 and a.company_id=$company_name and a.item_category in(4) and a.status_active=1 and a.is_deleted=0 and current_approval_status=1 and a.is_approved=1  $sequence_no_cond";
				}
				else
				{
			$sql="select a.id, a.booking_no,a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.po_break_down_id, b.id as approval_id,a.is_approved from wo_non_ord_samp_booking_mst  a, approval_history b where a.id=b.mst_id and b.entry_form=8 and a.company_id=$company_name and a.item_category in(4) and a.booking_type=5 and a.status_active=1 and a.is_deleted=0 and current_approval_status=1 and a.is_approved=1  $sequence_no_cond";
				}
		}
	}
	else
	{
		 $sequence_no_cond=" and b.sequence_no='$user_sequence_no'";
		 
		 if($cbo_booking_type==1) //With Order
			{
		   $sql="select a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, b.id as approval_id,a.is_approved from wo_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form=8 and a.company_id=$company_name and a.item_category in(4) and a.status_active=1 and a.is_deleted=0 and current_approval_status=1 and a.is_approved=1  $sequence_no_cond";
			}
			else
			{
			$sql="select a.id, a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.po_break_down_id, b.id as approval_id,a.is_approved from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form=8 and a.company_id=$company_name and a.item_category in(4) and a.booking_type=5 and a.status_active=1 and a.is_deleted=0 and current_approval_status=1 and a.is_approved=1  $sequence_no_cond";	
			}
	}

	//echo $sql;die;
	if($cbo_booking_type==1) //With Order
		{
 $print_report_format_ids=return_field_value("format_id","lib_report_template","template_name=".$company_name."  and module_id=2 and report_id=5 and is_deleted=0 and status_active=1");
$format_ids=explode(",",$print_report_format_ids);
		}
		else
		{
			$print_report_format_ids='';
			$format_ids='';	
		}
	//print_r($format_ids);die;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:920px; margin-top:10px">
        <legend>Trims Booking Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="80">MKT Cost</th>
                    <th width="130">Booking No</th>
                    <th width="80">Type</th>
                    <th width="100">Booking Date</th>
                    <th width="125">Buyer</th>
                    <th width="160">Supplier</th>
                    <th>Delivery Date</th>
                </thead>
            </table>
            <div style="width:900px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="882" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1;
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$value='';
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
								 if($db_type==0)
								  {
									$app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='8' order by id desc limit 0,1");
								  }
								  if($db_type==2)
								  {
									$app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' and entry_form='8'  and ROWNUM=1 order by id desc");
								  }
									$value=$row[csf('id')]."**".$app_id;
								}
								
								//echo $row[csf('trims_type')];
								//if($row[csf('booking_type')]==4) $booking_type="Sample";
								if($cbo_booking_type==1) //With Order
									{
										if($row[csf('is_short')]==1) $booking_type="Short"; else $booking_type="Main";
										
										$buyer_string="";
		
										$nameArray_buyer=sql_select( "select distinct a.buyer_name  from wo_po_details_master a, wo_booking_dtls b where a.job_no=b.job_no and b.booking_no='".$row[csf('booking_no')]."'"); 
										foreach ($nameArray_buyer as $result_buy)
										{
											$buyer_string.=$buyer_arr[$result_buy[csf('buyer_name')]].",";
										} 
									}
									else
									{
										if($row[csf('booking_type')]==5) $booking_type="None Order"; else $booking_type="Order"; 
									$buyer_string="";
		
									$nameArray_buyer=sql_select( "select distinct b.buyer_id as buyer_name  from wo_non_ord_samp_booking_mst  b where b.booking_no='".$row[csf('booking_no')]."'"); 
									foreach ($nameArray_buyer as $result_buy)
									{
										$buyer_string.=$buyer_arr[$result_buy[csf('buyer_name')]].",";
									}	
									}
									
									
									
								
								foreach($format_ids as $row_id)
								{
									//echo  $row_id;
									if($row_id==13)
									{ 
								
									 $trim_button="<input type='button' value='PB' onClick=\"generate_trim_booking_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('is_short')]."','".$row[csf('approval_id')]."','show_trim_booking_report','".$i."')\" style='width:50px;' class='formbutton' name='print_booking1' id='print_booking1' />";
									
									}
									if($row_id==14)
									{ 
									
									$trim_button="<input type='button' value='PB1' onClick=\"generate_trim_booking_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('is_short')]."','".$row[csf('approval_id')]."','show_trim_booking_report1','".$i."')\" style='width:50px;' class='formbutton' name='print_booking2' id='print_booking2' />";
									 }
								   if($row_id==15)
									{ 
									 $trim_button="<input type='button' value='PB2' onClick=\"generate_trim_booking_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('is_short')]."','".$row[csf('approval_id')]."','show_trim_booking_report2','".$i."')\" style='width:50px;' name='print_booking3' id='print_booking3' class='formbutton' />";
									 }
									if($row_id==16)
									{ 
									 $trim_button="<input type='button' value='PB3' onClick=\"generate_trim_booking_report('".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('is_short')]."','".$row[csf('approval_id')]."','show_trim_booking_report3','".$i."')\" style='width:50px;' name='print_booking4' id='print_booking4' class='formbutton' />";
									 }
								   
								}
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('booking_no')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                        <input id="<? echo strtoupper($row[csf('booking_no')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
                                    
                                    </td>   
									<td width="40" align="center"><? echo $i; ?></td>
                                    
                                    <td width="80">
                                    	<p>
                                        <? 
										if($cbo_booking_type==1)
										{
										?>
                                        <a href='##' style='color:#000' onClick="generate_comment_popup('<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('company_id')]; ?>,'show_trim_comment_report')">
									<? //echo $row[csf('booking_no')]; ?>View</a>
                                    <?
										}
										else
										{
											?>
                                             View
                                            <?
										}
									?>
                                    </p>
                                    </td>
									<td width="130">
                                    	<p><? echo $row[csf('booking_no')]; ?> <a href='##' style='color:#000'><? echo $trim_button;?></a></p>
                                    </td>
                                    <td width="80" align="center"><p><? echo $booking_type; ?></p></td>
									<td width="100" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
                                    <td width="125"><p><? echo rtrim($buyer_string,","); //$buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
									<td width="160"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
									<td align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
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
	
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and  is_deleted=0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and  is_deleted=0");
	
	$booking_type=str_replace("'","",$cbo_booking_type);
	//echo $booking_type;die;
	if($booking_type==1) //With Order
	{
	if($approval_type==0)
	{
		$response=$booking_ids;
		
		//$trims_type= max($trims_types);
		//echo $trims_type;die;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date"; 
		$id=return_next_id( "id","approval_history", 1 ) ;
		      
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=8 group by mst_id","mst_id","approved_no");
		$approved_status_arr = return_library_array("select id, is_approved from wo_booking_mst where id in($booking_ids)","id","is_approved");
	
		
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
			$data_array.="(".$id.",8,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id.",'".$pc_date_time."')"; 
			$id=$id+1;
		}
		
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
			$approved_string_dtls1="CASE booking_no ".$approved_string1." END";
			
			$sql_insert="insert into wo_booking_mst_hstry(id, approved_no, booking_id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
				select	
				'', $approved_string_mst, id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_booking_mst where booking_no in ($booking_nos)";
					
			/*$rID3=execute_query($sql_insert,0);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=30; 
			} 
			*/
			$sql_insert_dtls="insert into wo_booking_dtls_hstry(id, approved_no, booking_dtls_id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
				select	
				'', $approved_string_dtls, id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_booking_dtls where booking_no in ($booking_nos)";
					
		/*	$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=40; 
			} 
			*/
			
			$sql_insert_cons_dtls="insert into wo_trim_book_con_dtls_hstry(id, approved_no,wo_trim_book_con_dtl_id, wo_trim_booking_dtls_id,booking_no, job_no, po_break_down_id, color_number_id, gmts_sizes, item_color, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id) 
				select	
				'', $approved_string_dtls1, id,wo_trim_booking_dtls_id,booking_no, job_no, po_break_down_id, color_number_id, gmts_sizes, item_color, item_size, cons, process_loss_percent, requirment, pcs, color_size_table_id from wo_trim_book_con_dtls where booking_no in ($booking_nos)";
			
			$rID=sql_multirow_update("wo_booking_mst","is_approved",1,"id",$booking_ids,0);
			if($rID) $flag=1; else $flag=0;
			
			if($approval_ids!="")
			{
				$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
				if($flag==1) 
				{
					if($rIDapp) $flag=1; else $flag=10; 
				} 
			}
				
			$rID2=sql_insert("approval_history",$field_array,$data_array,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=20; 
			} 
				
			$rID3=execute_query($sql_insert,0);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=30; 
			} 
				
			$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=40; 
			} 
			
			$rID5=execute_query($sql_insert_cons_dtls,1);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=50; 
			} 
			
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
		$rID=sql_multirow_update("wo_booking_mst","is_approved",0,"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;
		
		$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
			
		$data=$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$app_ids,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		$response=$booking_ids;
		
		if($flag==1) $msg='20'; else $msg='22';
	}
	}
	else //WithOut Order
	{
		
	if($approval_type==0)
	{
		$response=$booking_ids;
		
		
		//echo $booking_ids.'azzzz';die;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date"; 
		$id=return_next_id( "id","approval_history", 1 ) ;
		      
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=8 group by mst_id","mst_id","approved_no");
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
			$data_array.="(".$id.",8,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id.",'".$pc_date_time."')"; 
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
			$approved_string_dtls1="CASE booking_no ".$approved_string1." END";
			
			$sql_insert="insert into wo_booking_mst_hstry(id, approved_no, booking_id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
				select	
				'', $approved_string_mst, id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_non_ord_samp_booking_mst where booking_no in ($booking_nos)";
				//echo $sql_insert;	
			/*$rID3=execute_query($sql_insert,0);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=30; 
			} 
			*/
			//echo "insert into wo_booking_mst_hstry (".$field_array.") values ".$data_array;die;
			$sql_insert_dtls="insert into wo_booking_dtls_hstry(id, approved_no, booking_dtls_id, booking_no, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width,trim_group, description, brand_supplier, uom,wo_qnty,cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
				select	
				'', $approved_string_dtls, id, booking_no, fabric_color, item_size, finish_fabric, grey_fabric, rate, amount,color_type_id, construction, composition, gsm_weight, dia_width, trim_group, fabric_description, barnd_sup_ref, uom, trim_qty, yarn_breack_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from wo_non_ord_samp_booking_dtls where booking_no in ($booking_nos)";
					
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
			
			$rID=sql_multirow_update("wo_non_ord_samp_booking_mst","is_approved",1,"id",$booking_ids,0);
			if($rID) $flag=1; else $flag=0;
			
			if($approval_ids!="")
			{
				$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
				if($flag==1) 
				{
					if($rIDapp) $flag=1; else $flag=10; 
				} 
			}
				
			$rID2=sql_insert("approval_history",$field_array,$data_array,0);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=20; 
			} 
				
			$rID3=execute_query($sql_insert,0);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=30; 
			} 
				
			$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=40; 
			} 
			
			/*$rID5=execute_query($sql_insert_cons_dtls,1);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=50; 
			} */
			
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
		$rID=sql_multirow_update("wo_non_ord_samp_booking_mst","is_approved",0,"id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;
		
		$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
			
		$data=$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$app_ids,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		$response=$booking_ids;
		
		if($flag==1) $msg='20'; else $msg='22';
	}
		
	} //Without End
	
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
?>