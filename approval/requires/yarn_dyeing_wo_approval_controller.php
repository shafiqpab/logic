<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
$from_mail="PLATFORM-ERP@fakir.app";

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];



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

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$company_fullname_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") $user_id=$txt_alter_user_id;
	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$booking_no=str_replace("'","",$txt_booking_no);
	$booking_year=str_replace("'","",$cbo_booking_year);
	if ($booking_no=="") $booking_no_cond=""; else $booking_no_cond=" and a.yarn_dyeing_prefix_num='".trim($booking_no)."' ";
	//if ($booking_year=="" || $booking_year==0) $booking_year_cond=""; else $booking_year_cond=" and to_char(a.insert_date,'YYYY')='".trim($booking_year)."' ";

	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.booking_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.booking_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.booking_date=$txt_date";
		else $date_cond='';
	}

	$approval_type = str_replace("'","",$cbo_approval_type);


	//$user_id=137;
	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");

	//echo "select sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0";//die;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");

	$min_sequence_no=return_field_value("min(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0","seq");

	$user_seq_bypass_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id = $cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2
	and is_deleted=0");


	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Yarn Dyeing Work Order Booking.</font>";
		die;
	}

	if($db_type==0)
	{
		$year_field="YEAR(a.insert_date) as year";
		$orderBy_cond="IFNULL";

		$last_appro_authority=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0","seq");
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		$orderBy_cond="NVL";

		$last_appro_authority=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0","seq");


	}
	else
	{
		$year_field="";//defined Later
		$orderBy_cond="ISNULL";

		$last_appro_authority=return_field_value("max(sequence_no) as seq","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0","seq");
	}



	if($booking_year!=0)
	{
		if($db_type==0)
		{
			$year_field_cond=" and YEAR(a.insert_date)=$booking_year";
		}
		else if($db_type==2)
		{
			$year_field_cond=" and to_char(a.insert_date,'YYYY')=$booking_year";
		}

	}


	if($db_type==2) $job_field_string="listagg(cast(b.job_no as varchar2(4000)), ',') within group (order by b.id) as job_nos";
	else			$job_field_string="group_concat(b.job_no) as job_nos";


	if($approval_type==0)// start of showing un-approved list
	{
		//echo $user_seq_bypass_no; //die;
		if($user_sequence_no==$min_sequence_no) //First approval authority
		{
			$sql="select a.id,a.ydw_no, a.yarn_dyeing_prefix_num, a.company_id , a.supplier_id, a.booking_date ,a.delivery_date,a.item_category_id,a.currency ,a.ecchange_rate, a.pay_mode, a.entry_form, a.budget_version, $year_field ,$job_field_string  from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b
			where a.id=b.mst_id and a.entry_form in (41,42,114) and a.ready_to_approved=1 and a.is_approved in(0) and a.company_id=$company_name $year_field_cond $date_cond $booking_no_cond  group by a.id,a.ydw_no, a.yarn_dyeing_prefix_num, a.company_id , a.supplier_id  , a.booking_date, a.delivery_date, a.item_category_id, a.currency, a.ecchange_rate ,a.pay_mode,a.insert_date,a.entry_form,a.budget_version";
			//echo $sql; //die;
		}
		else if($user_seq_bypass_no=="")// other approval authority or last approval authority
		{
			//echo "select listagg(sequence_no,',') within group (order by sequence_no) as sequence_no from electronic_approval_setup	where page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0"; die("with saal");
			if($db_type==0)
			{
				$sequence_no_by=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0");

				$booking_id=return_field_value("group_concat(distinct(mst_id)) as booking_id","wo_yarn_dyeing_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and b.sequence_no in ($sequence_no_by) and b.entry_form=30 and b.current_approval_status=1","booking_id");
				//echo $pre_cost_id;die;
				$booking_id_app_sql=sql_select("select b.mst_id as booking_id from wo_yarn_dyeing_mst a, approval_history b
				where a.id=b.mst_id and a.company_id=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=30 and a.ready_to_approved=1 and b.current_approval_status=1");
				foreach($booking_id_app_sql as $inf)
				{
					if($booking_id_app_byuser!="") $booking_id_app_byuser.=",".$inf[csf('booking_id')];
					else $booking_id_app_byuser.=$inf[csf('booking_id')];
				}

				$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));

			}
			else
			{

				$sequence_no_by=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","page_id=$menu_id and sequence_no<$user_sequence_no and bypass=1 and is_deleted=0","sequence_no");


				$booking_id_sql=sql_select("select b.mst_id as booking_id from wo_yarn_dyeing_mst a, approval_history b
				where a.id=b.mst_id and a.company_id=$company_name and b.sequence_no in ($sequence_no_by) and b.entry_form=30 and  a.ready_to_approved=1 and b.current_approval_status=1");
				foreach($booking_id_sql as $val)
				{
				 	if($booking_id!="") $booking_id.=",".$val[csf('booking_id')];
				 	else $booking_id.=$val[csf('booking_id')];
				}
				$booking_id=implode(",",array_unique(explode(",",$booking_id)));
				//echo "select b.mst_id as booking_id from wo_yarn_dyeing_mst a, approval_history b	where a.id=b.mst_id and a.company_id=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=30 and a.ready_to_approved=1 and b.current_approval_status=1";
				$booking_id_app_sql=sql_select("select b.mst_id as booking_id from wo_yarn_dyeing_mst a, approval_history b
				where a.id=b.mst_id and a.company_id=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=30 and a.ready_to_approved=1 and b.current_approval_status=1");

				foreach($booking_id_app_sql as $inf)
				{
					if($booking_id_app_byuser!="") $booking_id_app_byuser.=",".$inf[csf('booking_id')];
					else $booking_id_app_byuser.=$inf[csf('booking_id')];
				}

				$booking_id_app_byuser=implode(",",array_unique(explode(",",$booking_id_app_byuser)));
			}
			$result=array_diff(explode(',',$booking_id),explode(',',$booking_id_app_byuser));
			$booking_id=implode(",",$result);

			$booking_id_cond="";
			if($booking_id_app_byuser!="") $booking_id_cond=" and a.id not in($booking_id_app_byuser)";
			else $booking_id_cond="";

			if($booking_id!="")
			{   // and a.id in($booking_id)
				$sql="select a.id,a.ydw_no, a.yarn_dyeing_prefix_num, a.company_id , a.supplier_id  , a.booking_date ,a.delivery_date,a.item_category_id,a.currency ,a.ecchange_rate ,a.pay_mode, a.entry_form, $year_field ,$job_field_string  from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b
				where a.id=b.mst_id and a.entry_form in (41,42,114) and a.ready_to_approved=1 and a.is_approved in($approval_type) and a.company_id=$company_name $year_field_cond $date_cond $booking_no_cond $booking_id_cond  group by a.id,a.ydw_no, a.yarn_dyeing_prefix_num, a.company_id , a.supplier_id  , a.booking_date, a.delivery_date, a.item_category_id,a.currency ,a.ecchange_rate ,a.pay_mode,a.insert_date,a.entry_form
					union all
					select a.id,a.ydw_no, a.yarn_dyeing_prefix_num, a.company_id , a.supplier_id  , a.booking_date ,a.delivery_date,a.item_category_id,a.currency, a.ecchange_rate ,a.pay_mode, a.entry_form, $year_field ,$job_field_string  from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b
					where a.id=b.mst_id and a.entry_form in (41,42,114) and a.ready_to_approved=1 and a.is_approved in (0,1,3) and a.company_id=$company_name and (a.id in($booking_id)) $year_field_cond $date_cond $booking_no_cond  group by a.id,a.ydw_no, a.yarn_dyeing_prefix_num, a.company_id , a.supplier_id  , a.booking_date ,a.delivery_date,a.item_category_id,a.currency ,a.ecchange_rate ,a.pay_mode,a.insert_date,a.entry_form";
					 //echo $sql;//die;
			}
			else
			{
				$sql="select a.id,a.ydw_no, a.yarn_dyeing_prefix_num, a.company_id , a.supplier_id  , a.booking_date ,a.delivery_date,a.item_category_id,a.currency ,a.ecchange_rate ,a.pay_mode ,$year_field ,$job_field_string  from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, approval_history c
				where a.id=b.mst_id and b.mst_id = c.mst_id and a.entry_form in (41,42,114) and a.ready_to_approved=1 and a.is_approved in(0,3) and a.company_id=$company_name $year_field_cond $date_cond $booking_no_cond group by a.id,a.ydw_no, a.yarn_dyeing_prefix_num, a.company_id , a.supplier_id  , a.booking_date, a.delivery_date, a.item_category_id, a.currency ,a.ecchange_rate ,a.pay_mode,a.insert_date";
				//echo $sql;//die;
			}

		}
		else
		{
			$user_sequence_no=$user_sequence_no-1;
			$user_bypass_status = return_field_value("bypass","electronic_approval_setup","page_id=$menu_id and
			 sequence_no = $user_sequence_no and is_deleted=0");


			if($user_seq_bypass_no==$user_sequence_no && $user_bypass_status ==1) {
				$sequence_no_by_pass='';
			}
			else
			{
				if($db_type==0)
				{
					if($bypass_status == 2){
						$bypass_status_cond = " and bypass=2";
					}else{
						$bypass_status_cond = " and bypass=1";
					}
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","page_id=$menu_id and
					 sequence_no between $user_seq_bypass_no and $user_sequence_no $bypass_status_cond and is_deleted=0");
				}
				else
				{
					if($bypass_status == 2){
						$bypass_status_cond = " and bypass=2";
					}else{
						$bypass_status_cond = " and bypass=1";
					}
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no)
					 as sequence_no","electronic_approval_setup","page_id=$menu_id and sequence_no between $user_seq_bypass_no and $user_sequence_no
					 $bypass_status_cond and is_deleted=0","sequence_no");
					// echo $sequence_no_by_pass;die;
				}
			}
			//echo $user_sequence_no."__".$sequence_no_by_pass;die("with sequence no");
			if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$user_sequence_no' and c.current_approval_status =1";
			else $sequence_no_cond=" and ((c.sequence_no='$user_seq_bypass_no' or c.sequence_no in ($sequence_no_by_pass)) and c.current_approval_status =1)";

			$sql="select a.id,a.ydw_no, a.yarn_dyeing_prefix_num, a.company_id , a.supplier_id  , a.booking_date ,a.delivery_date,a.item_category_id,a.currency ,a.ecchange_rate ,a.pay_mode, a.entry_form, $year_field ,$job_field_string  from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b,approval_history c
			 where c.mst_id=a.id and a.id=b.mst_id and a.entry_form in (41,42,114) and c.entry_form=30 and a.ready_to_approved=1 and a.is_approved in (0,3) and a.company_id=$company_name $year_field_cond $date_cond $booking_no_cond $sequence_no_cond group by a.id,a.ydw_no, a.yarn_dyeing_prefix_num, a.company_id , a.supplier_id, a.booking_date ,a.delivery_date,a.item_category_id,a.currency,a.ecchange_rate,a.pay_mode,a.insert_date, a.entry_form";
			//echo $sql; //die("with ostad");
		}
	}
	else //start of showing approved list
	{

		if($last_appro_authority ==""){
			$sequence_no_cond = "  and c.id in ( select id from approval_history where approved_by in( $user_id))";
		}else{
			$sequence_no_cond=" and c.approved_by='$user_id'";
		}


		 $sql="select a.id,a.ydw_no, a.yarn_dyeing_prefix_num, a.company_id , a.supplier_id  , a.booking_date ,a.delivery_date,a.item_category_id,a.currency ,a.ecchange_rate ,a.pay_mode, a.entry_form, a.budget_version, $year_field ,$job_field_string
		 from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b,approval_history c
		 where a.id=b.mst_id and b.mst_id=c.mst_id and a.entry_form in (41,42,114) and c.entry_form=30  and a.ready_to_approved = 1 and a.is_approved in (1,3) and c.current_approval_status =1 and   a.company_id=$company_name $year_field_cond $date_cond $booking_no_cond $sequence_no_cond
		 group by a.id,a.ydw_no, a.yarn_dyeing_prefix_num, a.company_id , a.supplier_id  , a.booking_date ,a.delivery_date,a.item_category_id,a.currency ,a.ecchange_rate ,a.pay_mode,a.insert_date,a.entry_form,a.budget_version";
		// echo $sql;//die;

    }



	//echo $sql;//die;
	?>

    <script>



	</script>
    <?

	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1270px; margin-top:10px">
        <legend>Yarn Dyeing Work Order Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1220" class="rpt_table" >
                <thead>
                	<th width="40"></th>
                    <th width="40">SL</th>
                    <th width="70">Booking No</th>
                    <th width="60">Year</th>
                    <th width="100">Booking Date</th>
                    <th width="160">Supplier</th>
                    <th width="200">Job No</th>
                    <th width="150">Item Category</th>
                    <th width="70">Currency</th>
                    <th width="70">Exchange Rate</th>
                    <th width="70">Pay Mode</th>
                    <th width="70">Page Menu</th>
                    <th width="">Delivery Date</th>
                </thead>
            </table>
            <div style="width:1220px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
						 //echo $sql;

                        $i=1;
                        $nameArray=sql_select( $sql );

                        foreach ($nameArray as $row)
                        {
							count($row);
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";

								//echo $row[csf('id')];
							if($row[csf('entry_form')] == 41){
								$report_id_con = " and report_id = 7";
							}else if($row[csf('entry_form')] == 42){
								$report_id_con = " and report_id = 8";
							}else if($row[csf('entry_form')] == 114){
								$report_id_con = " and report_id = 29";
							}else if($row[csf('entry_form')] == 125){
								$report_id_con = " and report_id = 28";
							}


							$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=2 $report_id_con and is_deleted=0 and status_active=1");
							//echo "format_id from lib_report_template where  template_name =".$cbo_company_name." and module_id=2 $report_id_con and is_deleted=0 and status_active=1";die;

							$format_ids2 = explode(",",$print_report_format);
							//print_r($format_ids2);die;

							foreach ($format_ids2 as $value) {
								//echo $value;die;
								if($value==77)
								{
									$variable="<a href='#'  title='".$value."__".$row[csf('entry_form')]."' onClick=\"generate_worder_report('".$row[csf('ydw_no')]."','".$row[csf('company_id')]."','".$row[csf('id')]."','".$row[csf('budget_version')]."','show_with_multiple_job_without_rate','".$row[csf('entry_form')]."')\"> ".$row[csf('yarn_dyeing_prefix_num')]." <a/>";
									break;
								}
								else if($value==76)
								{

									$variable="<a href='#'  title='".$value."__".$row[csf('entry_form')]."' onClick=\"generate_worder_report('".$row[csf('ydw_no')]."','".$row[csf('company_id')]."','".$row[csf('id')]."','".$row[csf('budget_version')]."','show_with_multiple_job','".$row[csf('entry_form')]."')\"> ".$row[csf('yarn_dyeing_prefix_num')]." <a/>";
									break;
								}
							 	else if($value==75)
								{

									 $variable="<a href='#'  title='".$value."__".$row[csf('entry_form')]."' onClick=\"generate_worder_report('".$row[csf('ydw_no')]."','".$row[csf('company_id')]."','".$row[csf('id')]."','".$row[csf('budget_version')]."','show_without_rate_booking_report','".$row[csf('entry_form')]."')\"> ".$row[csf('yarn_dyeing_prefix_num')]." <a/>";
									 break;
								}
								else if($value==74)
								{
									//echo $value; die("km nazim");
									 $variable="<a href='#'  title='".$value."__".$row[csf('entry_form')]."'  onClick=\"generate_worder_report('".$row[csf('ydw_no')]."','".$row[csf('company_id')]."','".$row[csf('id')]."','".$row[csf('budget_version')]."','show_trim_booking_report','".$row[csf('entry_form')]."')\"> ".$row[csf('yarn_dyeing_prefix_num')]." <a/>";
									 break;
								}
								else if($value==79)
								{
									 $variable="<a href='#'  title='".$value."__".$row[csf('entry_form')]."'  onClick=\"generate_worder_report('".$row[csf('ydw_no')]."','".$row[csf('company_id')]."','".$row[csf('id')]."','".$row[csf('budget_version')]."','show_trim_booking_report','".$row[csf('entry_form')]."')\"> ".$row[csf('yarn_dyeing_prefix_num')]." <a/>";
									 break;
								}
								else if($value==80)
								{
									 $variable="<a href='#'  title='".$value."__".$row[csf('entry_form')]."'  onClick=\"generate_worder_report('".$row[csf('ydw_no')]."','".$row[csf('company_id')]."','".$row[csf('id')]."','".$row[csf('budget_version')]."','show_trim_booking_report','".$row[csf('entry_form')]."')\"> ".$row[csf('yarn_dyeing_prefix_num')]." <a/>";
									 break;
								}
							}


							if($row[csf('entry_form')] == 41){
								$page_menu = "Main";
							}elseif($row[csf('entry_form')] == 42 || $row[csf('entry_form')] == 125){
								$page_menu = "Without Work Order";
							}

								//if($variable=="") $variable="".$row[csf('yarn_dyeing_prefix_num')]."";

							//echo $variable;
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td width="40" align="center" valign="middle">
											<input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" />
											<input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
											<input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('ydw_no')]; ?>" />
											<input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                            <input id="<? echo strtoupper($row[csf('ydw_no')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
										</td>
										<td width="40" id="td_<? echo $i; ?>" ><? echo $i; ?></td>

                                        <td width="70" align="center"><p><? echo $variable ; //echo $row[csf('yarn_dyeing_prefix_num')]; ?></p></td>
                                        <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
										<td width="100" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
										<td width="160"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                                        <td width="200" align="center"><p><? echo $row[csf('job_nos')]; ?>&nbsp;</p></td>
                                        <td width="150" align="center"><p><? echo $item_category[$row[csf('item_category_id')]]; ?>&nbsp;</p></td>

										<td width="70" align="center"><p><? echo $currency[$row[csf('currency')]]; ?>&nbsp;</p></td>


                                        <td width="70"><p><? echo $row[csf('ecchange_rate')]; ?></p></td>
                    					<td width="70"><? echo $pay_mode[$row[csf('pay_mode')]]; ?></td>
                    					<td width="70"><? echo $page_menu; ?></td>

										<td align="center" width=""><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>

									</tr>
									<?
									$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1200" class="rpt_table">
				<tfoot>
                    <td width="40" align="center" >
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<? echo $approval_type; ?>">
                        <font style="display:none"><? echo $all_approval_id; ?></font>
                    </td>
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
	//$user_id=137;

	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$msg=''; $flag=''; $response='';
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") 	$user_id_approval=$txt_alter_user_id;
	else						$user_id_approval=$user_id;

	$approval_ids = return_field_value("id","approval_history", "mst_id in($booking_ids) and entry_form=30 and current_approval_status=1");

	//echo "0**";
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");

	$last_app_authority=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2");
	//echo "select max(sequence_no) from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0 and bypass=2";die;

	if($approval_type==0) //Approve Button
	{
		$response=$booking_ids;

		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");

		//echo "select sequence_no from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted=0";die;

		if($is_not_last_user!=""){
			$partial_approval=3;
		}else{
			// if($user_sequence_no == $min_sequence_no){
			//
			// 	$partial_approval=3;
			// }else{
				$partial_approval=1;
			//}
		}
		//echo $partial_approval;die;
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,inserted_by,insert_date";

		$id=return_next_id( "id","approval_history", 1 ) ;

		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=30 group by mst_id","mst_id","approved_no");

		$approved_status_arr = return_library_array("select id, is_approved from wo_yarn_dyeing_mst where id in($booking_ids)","id","is_approved");

		$approved_no_array=array();
		$approved_no_dtls_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		$booking_nos_all=explode(",",$booking_nos);
		$app_instru_all=explode(",",$appv_instras);
		$book_nos='';

		for($i=0;$i<count($booking_nos_all);$i++)
		{
			$val=$booking_nos_all[$i];

			$booking_id=$booking_ids_all[$i];
			$app_instru=$app_instru_all[$i];

			$approved_no=$max_approved_no_arr[$booking_id];
			$approved_status=$approved_status_arr[$booking_id];

			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$val]=$approved_no;
				$approved_no_dtls_array[$booking_id]=$approved_no;
				if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
			}

			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",30,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."',".$user_id.",'".$pc_date_time."')";
			$id=$id+1;

		}

		//echo "10**".$data_array;die;
		//echo $approved_no_array."ss";die;
		if(count($approved_no_array)>0)
		{
			$approved_string="";
			$approved_string_dtls='';

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

			foreach($approved_no_dtls_array as $key=>$value)
			{
				$approved_string_dtls.=" WHEN $key THEN $value";
			}

			$approved_string_mst="CASE ydw_no ".$approved_string." END";
			$approved_string_dtls="CASE mst_id ".$approved_string_dtls." END";

			$sql_insert="insert into wo_yarn_dyeing_mst_hstry(id, approved_no, booking_id, yarn_dyeing_prefix, yarn_dyeing_prefix_num, ydw_no, company_id, supplier_id, booking_date, delivery_date, delivery_date_end, dy_delivery_date_start,dy_delivery_date_end, currency, ecchange_rate, pay_mode, source, attention,  inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,entry_form, item_category_id, service_type, is_short, is_sales, budget_version, ready_to_approved, is_approved)
				select
				'', $approved_string_mst,  id, yarn_dyeing_prefix, yarn_dyeing_prefix_num,  ydw_no, company_id, supplier_id, booking_date, delivery_date, delivery_date_end, dy_delivery_date_start, dy_delivery_date_end, currency,  ecchange_rate, pay_mode, source,  attention, inserted_by, insert_date, updated_by, update_date, status_active,   is_deleted, entry_form, item_category_id,  service_type, is_short, is_sales, budget_version, ready_to_approved, is_approved from wo_yarn_dyeing_mst where ydw_no in ($book_nos)";

			$sql_insert_dtls="insert into wo_yarn_dyeing_dtls_hstry( id,approved_no,booking_dtls_id, mst_id,  job_no, product_id, job_no_id, count, yarn_description,  yarn_color, yarn_wo_qty, dyeing_charge,   amount, no_of_bag, no_of_cone, referance_no, min_require_cone, remarks,  status_active, is_deleted, color_range,  uom, entry_form, sample_name,  file_no, internal_ref_no, yarn_comp_type1st, yarn_comp_percent1st, yarn_type, booking_no,  app_batch_no)
				select
				'', $approved_string_dtls,  id, mst_id, job_no, product_id, job_no_id, count, yarn_description,  yarn_color, yarn_wo_qty, dyeing_charge,   amount, no_of_bag, no_of_cone, referance_no, min_require_cone, remarks,  status_active, is_deleted, color_range,  uom, entry_form, sample_name,  file_no, internal_ref_no, yarn_comp_type1st, yarn_comp_percent1st, yarn_type, booking_no,  app_batch_no from wo_yarn_dyeing_dtls where mst_id in ($booking_ids)";

		}

		/*if($is_not_last_user!=""){

			$rID=sql_multirow_update("wo_yarn_dyeing_mst","ready_to_approved*is_approved","1*$partial_approval","id",$booking_ids,0);

		}else{

			if($user_sequence_no == $min_sequence_no){
				$rID=sql_multirow_update("wo_yarn_dyeing_mst","is_approved","$partial_approval","id",$booking_ids,0);
			}else{
				$rID=sql_multirow_update("wo_yarn_dyeing_mst","is_approved","$partial_approval","id",$booking_ids,0);
			}
		}*/

		$rID=sql_multirow_update("wo_yarn_dyeing_mst","is_approved","$partial_approval","id",$booking_ids,0);
		if($rID) $flag=1; else $flag=0;

		$query_curr_stat="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=30 and mst_id in ($booking_ids)";
		$rID_curr_stat=execute_query($query_curr_stat,1);

		if($flag==1)
		{
			if($rID_curr_stat) $flag=1; else $flag=0;
		}

		//echo "18**insert into approval_history (".$field_array.") Values ".$data_array;die;
		$rID2=sql_insert("approval_history",$field_array,$data_array,0);
		//echo "18**".$rID2;die;
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}
		//echo count($approved_no_array); die;
		if(count($approved_no_array)>0)
		{

			$rID3=execute_query($sql_insert,0);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}

			$rID4=execute_query($sql_insert_dtls,1);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			} //echo $rID3.'df'.$rID4;die;
		}
		//oci_rollback($con);
			//echo "21**".$flag;die;
		if($flag==1) $msg='19'; else $msg='21';
		//echo "21**".$msg;die;
	}
	else
	{ //Un Approve Button


		$rID=sql_multirow_update("wo_yarn_dyeing_mst","is_approved*ready_to_approved","0*0","id",$booking_ids,0);

		if($rID) $flag=1; else $flag=0;

		//$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$booking_ids,0);
		//$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=30 and mst_id in ($booking_ids)";

		//$rID2=execute_query($query,1);

		// if($flag==1)
		// {
		// 	if($rID2) $flag=1; else $flag=0;
		// }

		$data=$user_id_approval."*'".$pc_date_time."'*0*".$user_id."*'".$pc_date_time."'";

		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*current_approval_status*updated_by*update_date",$data,"id",$approval_ids,0);
		//echo "22**".$rID3;die;

		//echo $rID3;die("in kochukhet");
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		/*$rID4=sql_multirow_update("fabric_sales_order_mst","is_apply_last_update","2","booking_id",$booking_ids,1);
		if($flag==1)
		{
			if($rID4) $flag=1; else $flag=0;
		} */
		//echo "22**".$flag;die;
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
                    $sql="select image_location from common_photo_library where master_tble_id in('$job_no') and form_name='knit_order_entry' and file_type=1";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
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

if($action=="file")
{
	echo load_html_head_contents("File View", "../../", 1, 1,'','','');
	extract($_REQUEST);

?>
	<fieldset style="width:600px; margin-left:5px">
		<div style="width:100%; word-wrap:break-word" id="scroll_body">
             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
             	<tr>
					<?
					$i=0;
                    $sql="select image_location from common_photo_library where master_tble_id in('$job_no') and form_name='knit_order_entry' and file_type=2";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td width="100" align="center"><a target="_blank" href="../../<? echo $row[csf('image_location')]; ?>"><img width="89" height="97" src="../../file_upload/blank_file.png"><br>File-<? echo $i; ?></a></td>
                    <?
						if($i%6==0) echo "</tr><tr>";
                    }
                    ?>
                </tr>
            </table>
        </div>
	</fieldset>
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
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=7 and user_id='$user_id' and booking_id='$wo_id' and approval_type='$app_type' and status_active=1 and is_deleted=0";
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
				http.open("POST","fabric_booking_approval_controller.php",true);
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
			http.open("POST","fabric_booking_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_appv_mail_Reply_info;

		}

		function fnc_appv_mail_Reply_info()
		{
			if(http.readyState == 4)
			{
				var response=trim(http.responseText).split('**');
				/*if(response[0]==222)
				{
					show_msg(reponse[0]);
				}*/
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

if ($action=="unappcause_popup")
{
	echo load_html_head_contents("Un Approval Request", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$wo_id=$data;
	$sql_req="select approval_cause,approval_no from fabric_booking_approval_cause where entry_form=7 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0 order by approval_no ";
	//echo $sql_req;
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
                    	<textarea name="unappv_req" id="unappv_req" class="text_area" style="width:430px; height:100px;"></textarea>
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
			//echo $sql;
		 $arr=array (2=>$custom_designation,3=>$Department);
		 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>

</form>
<script language="javascript" type="text/javascript">
  setFilterGrid("tbl_style_ref");
</script>


<?
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

			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=7 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

			//echo "shajjad_".$approved_no_history.'_'.$approved_no_cause; die;

			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*7*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=7 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=7 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*7*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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

			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=7 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");

			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=7 and mst_id=$wo_id and approved_by=$user_id");

			if($unapproved_cause_id=="")
			{

				//echo "shajjad_".$unapproved_cause_id; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_history_id,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",7,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

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

				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=7 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_history_id*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*7*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
						$user_name=return_field_value("user_name","user_passwd","id=$user");
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

if($action=="check_booking_last_update")
{
	$last_update=return_field_value("is_apply_last_update","wo_booking_mst","booking_no='".trim($data)."'");
	echo $last_update;
	exit();
}

if($action=="pre_cost_data")
{

	$jobData=sql_select("select a.job_no, a.company_name, a.buyer_name, a.style_ref_no,b.costing_date,b.costing_per from wo_po_details_master a,wo_pre_cost_mst b where a.job_no=b.job_no and b.job_no='".trim($data)."'");
	foreach($jobData as $row)
	{
		$last_job_data=implode('***',$row);;
	}
	echo $last_job_data;
	exit();
}


// For Comments
if($action=="show_fabric_comment_report")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	//$last_update=return_field_value("is_apply_last_update","wo_booking_mst","booking_no='".trim($data)."'");
	//echo $last_update;
?>
<body>
<div>
<table width="870"   cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
 <thead>
   <tr align="center">
    <th colspan="12"><b>Comments</b></th>
    </tr>

    <tr>
    <th width="30" rowspan="2">Sl</th>
    <th width="120" rowspan="2">Po NO</th>
    <th width="70" rowspan="2">Ship Date</th>
    <th width="80" rowspan="2">As Merketing</th>
    <th width="70" rowspan="2">As Budget</th>
    <th width="70" rowspan="2">Mn.Book Qty</th>
    <th width="70" rowspan="2">Sht.Book Qty</th>
    <th width="70" rowspan="2">Smp.Book Qty</th>
    <th  width="70" rowspan="2">Tot.Book Qty</th>
    <th colspan="2">Balance</th>
    <th width="" rowspan="2">Comments ON Budget</th>
    </tr>
    <tr>
    <th width="70">As Mkt.</th>
    <th width="70">As Budget</th>
    </tr>
     </thead>
</table>
<?

	$cbo_fabric_natu=str_replace("'","",$fab_nature);
	$cbo_fabric_source=str_replace("'","",$fab_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'";
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
	 $paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$order_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");
	//print_r( $paln_cut_qnty_array);
	//echo $job_no;

	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no in('".str_replace(',',"','",$job_no)."')", "gmts_item_id", "set_item_ratio");
	//$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$job_no'", "gmts_item_id", "set_item_ratio");
//	echo "select quotation_id from wo_po_details_master where job_no='".$job_no."' ";
	$quotation_id = return_field_value(" quotation_id as quotation_id"," wo_po_details_master ","job_no in('".str_replace(',',"','",$job_no)."')","quotation_id");
	$tot_mkt_cost  = return_field_value(" sum(b.fab_knit_req_kg) as mkt_cost","wo_price_quotation a,wo_pri_quo_sum_dtls b"," a.id=b.quotation_id and a.id='".$quotation_id."'","mkt_cost");
//	print_r( $item_ratio_array);
	$nameArray=sql_select("
	select
	a.id,
	a.item_number_id,
	a.costing_per,
	a.job_no,
	b.po_break_down_id,
	b.color_size_table_id,
	b.requirment,
	c.po_number
FROM
	wo_pre_cost_fabric_cost_dtls a,
	wo_pre_cos_fab_co_avg_con_dtls b,
	wo_po_break_down c
WHERE
	a.job_no=b.job_no and
	a.job_no=c.job_no_mst and
    a.id=b.pre_cost_fabric_cost_dtls_id and
	b.po_break_down_id=c.id and
	b.po_break_down_id in (".str_replace("'","",$order_id).")   and a.status_active=1 and a.is_deleted=0
	order by id");
	$count=0;
	//$cbo_fabric_natu $cbo_fabric_source_cond
	$tot_grey_req_as_pre_cost_arr=array();$tot_grey_req_as_price_cost_arr=array();$tot_grey_req_as_price_cost=0;
	foreach ($nameArray as $result)
	{
		//echo "select quotation_id as quotation_id from wo_po_details_master where job_no='".$result[csf('job_no')]."'";
		// $quotation_id = return_field_value(" quotation_id as quotation_id"," wo_po_details_master ","job_no='".$result[csf('job_no')]."'","quotation_id");
		if (count($nameArray)>0 )
		{
            if($result[csf("costing_per")]==1)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=12;
				//$tot_mkt_price=$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==2)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=1;
			}
			if($result[csf("costing_per")]==3)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=12*2;
			}
			if($result[csf("costing_per")]==4)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=12*3;
			}
			if($result[csf("costing_per")]==5)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
				$dzn_qnty_p=12*4;
			}
			$dzn_qnty_p=$dzn_qnty_p*$item_ratio_array[$result[csf("item_number_id")]];

			  $tot_grey_req_as_price_cost+=($tot_mkt_cost/$dzn_qnty_p)*$paln_cut_qnty_array[$result[csf("color_size_table_id")]];
			//echo $paln_cut_qnty_array[$result[csf("color_size_table_id")]].'='.$tot_mkt_cost.'/'.$dzn_qnty_p.'<br>';
			//$tot_grey_req_as_price_cost_arr[$quotation_id]+=$tot_grey_req_as_price_cost;

			$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
    }
	       // $tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]];
		  // echo $tot_grey_req_as_pre_cost;die;
		   //Price Quotation


		            $total_pre_cost=0;
					$total_booking_qnty_main=0;
					$total_booking_qnty_short=0;
					$total_booking_qnty_sample=0;
					$total_tot_bok_qty=0;
					$tot_balance=0;

					$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and    a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty"); //a.job_no =c.job_no and

					$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");


					$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no=c.job_no and  a.booking_no=c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$order_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$order_id).") group by a.po_number order by id");



?>
<div style="width:890px; max-height:400px; overflow-y:scroll" id="scroll_body">
<table width="870"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
<?
$k=0;$total_price_mkt_cost=0;
foreach($sql_data  as $row)
	{
		 if ($i%2==0)
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
	$k++;
	//tot_grey_req_as_price_cost_arr
	$quotation_id = return_field_value(" a.quotation_id as quotation_id"," wo_po_details_master a,wo_po_break_down b ","a.job_no=b.job_no_mst and b.po_number='".$row[csf('po_number')]."'","quotation_id");
	?>
<tr bgcolor="<? echo $bgcolor; ?>">
    <td width="30"> <? echo $k; ?> </td>
    <td width="120"><p><? echo $row[csf("po_number")]; ?></p> </td>
    <td width="70" align="right"><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?> </td>
    <td width="80" align="right"><? $total_price_mkt_cost+=$tot_grey_req_as_price_cost;echo number_format($tot_grey_req_as_price_cost,2);?> </td>
    <td width="70" align="right"><?  echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]];?> </td>
    <td width="70" align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?> </td>
    <td width="70" align="right"> <? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
    <td width="70" align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
    <td width="70" align="right">	<? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?> </td>
    <td width="70" align="right"> <? $balance= def_number_format($total_price_mkt_cost-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?></td>
    <td width="70" align="right"> <?  $total_pre_cost_bal=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty;$tot_pre_cost_bal+=$total_pre_cost_bal;echo number_format($total_pre_cost_bal,2); ?></td>
    <td width="">
    <p>
     <?
	$pre_cost= $tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]];

	if( $total_pre_cost_bal>0)
	{
		echo "Less Booking";
	}
	else if ($total_pre_cost_bal<0)
	{
		echo "Over Booking";
	}
	else if ($pre_cost==$tot_bok_qty)
	{
		echo "As Per";
	}
	else
	{
		echo "";
	}
	?>
    </p>
	</td>
</tr>
<?
	}
?>
<tfoot>
    <tr>
    <td colspan="3">Total:</td>
    <td align="right"><? echo number_format($total_price_mkt_cost,2); ?></td>
     <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
    <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
    <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
    <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
     <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
    <td align="right"><? echo number_format($tot_balance,2); ?></td>
    <td align="right"><? echo number_format($tot_pre_cost_bal,2); ?></td>
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
