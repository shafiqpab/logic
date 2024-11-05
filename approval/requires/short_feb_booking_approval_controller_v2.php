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
if($action=="report_formate_setting")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=12 and report_id=177 and is_deleted=0 and status_active=1");
	echo "print_report_button_setting('$print_report_format');\n";
	exit();
}
if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}

if($action=="load_drop_down_buyer_new_user")
{
	$data=explode("_",$data); 
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

	function auto_approved($dataArr=array()){
		global $pc_date_time;
		global $user_id;
		$sys_id_arr=explode(',',$dataArr['sys_id']);
		
		$queryText = "select a.id,a.SETUP_DATE,b.APPROVAL_NEED,b.ALLOW_PARTIAL,b.PAGE_ID from APPROVAL_SETUP_MST a,APPROVAL_SETUP_DTLS b where a.id=b.MST_ID and a.COMPANY_ID=$dataArr[company_id] and b.PAGE_ID=$dataArr[app_necessity_page_id] and a.STATUS_ACTIVE =1 and a.IS_DELETED=0  and b.STATUS_ACTIVE =1 and b.IS_DELETED=0 order by a.SETUP_DATE desc";
		$queryTextRes = sql_select($queryText);
		
		if($queryTextRes[0]['ALLOW_PARTIAL']==1){
			$con = connect();
			$query="UPDATE $dataArr[mst_table] SET IS_APPROVED=1,approved_by={$dataArr['approval_by']},approved_date='$pc_date_time' WHERE id in (".$dataArr['sys_id'].")";
			$rID1=execute_query($query,1);
			//echo $query;die;
			
			if($rID1==1){ oci_commit($con);}
			else{oci_rollback($con);}
			disconnect($con);
			//return $query;
		}
		//return $ALLOW_PARTIAL;
	}

	
	function getSequence($parameterArr=array()){
		$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
		//$lib_brand_arr=implode(',',(array_keys($parameterArr['lib_brand_arr'])));

		//Electronic app setup data.....................
		$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0 order by SEQUENCE_NO";
		//echo $sql;die;
		$sql_result=sql_select($sql);
		$dataArr=array();
		foreach($sql_result as $rows){		
			
			if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_arr;}

			$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
			$dataArr['user_by'][$rows['USER_ID']]=$rows;
			$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
		}
	
		return $dataArr;
	}




	function getFinalUser($parameterArr=array()){
		$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));

		//echo $lib_buyer_arr;die;

		
		//Electronic app setup data.....................
		$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND PAGE_ID = {$parameterArr['page_id']} AND IS_DELETED = 0  order by SEQUENCE_NO";
		 //echo $sql;die;
		$sql_result=sql_select($sql);
		foreach($sql_result as $rows){

			if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_arr;}

			$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$rows['BUYER_ID']);
			$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
		
		}


		$finalSeq=array();
		foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
			
			foreach($userSeqDataArr as $user_id=>$seq){ 
				if( in_array($bbtsRows['buyer'],$usersDataArr[$user_id]['BUYER_ID']) &&  $bbtsRows['buyer']>0 ){
					$finalSeq[$sys_id][$user_id]=$seq;
				}

			}
		}

			//print_r($parameterArr['match_data']);die;
	
		return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
	}



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$companyFullName_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name"  );


	$company_name=str_replace("'","",$cbo_company_name);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$booking_no=str_replace("'","",$txt_booking_no);
	$approval_type=str_replace("'","",$cbo_approval_type);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);

	$approved_by=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;


	$sequence_no='';
	if($txt_alter_user_id!="")
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			$log_sql = sql_select("SELECT user_level,buyer_id,unit_id,is_data_level_secured FROM user_passwd WHERE id = '$txt_alter_user_id' AND valid = 1"); 
			foreach($log_sql as $r_log)
			{
				if($r_log[csf('is_data_level_secured')]==1)
				{
					if($r_log[csf('buyer_id')]!="") $where_con .=" and a.buyer_id in (".$r_log[csf('buyer_id')].")";
				}

			}
		}
		else
		{
			$where_con .=" and a.buyer_id=$cbo_buyer_name";
		}
		$user_id=$txt_alter_user_id;
	}
	else
	{
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $where_con .=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; 
			}
		}
		else
		{
			$where_con .=" and a.buyer_id=$cbo_buyer_name";
		}
	}
 
	
	
	//if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and c.file_no='".trim($file_no)."' "; 
	//if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and c.grouping like '%".trim($internal_ref)."%' "; 
	if ($booking_no != ""){$where_con .= " and a.booking_no_prefix_num='".trim($booking_no)."' ";}

	//echo change_date_format(str_replace("'","",$txt_date));die;
	if(str_replace("'","",$txt_date)!="")
	{
		if($db_type==2)
		{
			$txt_date1=str_replace("'","",$txt_date);
			$txt_date="'".date("d-M-Y",strtotime($txt_date1))."'";
		}
		
		if(str_replace("'","",$cbo_get_upto)==1) $where_con .= " and a.booking_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $where_con .= " and a.booking_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $where_con .= " and a.booking_date=$txt_date";

	}

	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}
	//$user_id=181;

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");

	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Short Fabric Booking.</font>";
		die;
	}

	
	
	$electronicDataArr=getSequence(array('company_id'=>$company_name,'page_id'=>$menu_id,'user_id'=>$approved_by,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'lib_department_id_arr'=>0));
	
 	
	if($previous_approved==1 && $approval_type==1)
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
		
		$sequence_no_cond=" and b.sequence_no<'$user_sequence_no'";

		/*$sql="SELECT a.id,  a.company_id, a.buyer_id, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, a.approved, a.inserted_by, b.id as approval_id, a.garments_nature from wo_price_quotation a, approval_history b where a.id=b.mst_id and b.entry_form=10 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and b.current_approval_status=1 and a.approved in(1,3) and b.approved_by!=$user_id $buyer_id_cond2 $sequence_no_cond $date_cond order by a.id ASC";*/

		$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,c.grouping, c.file_no, a.pay_mode
		from wo_booking_mst a, approval_history b, wo_po_break_down c
		where a.id=b.mst_id and a.job_no=c.job_no_mst and b.entry_form=12 and a.is_short=1 and a.booking_type=1 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved in(1,3) and b.approved_by!=$user_id $buyer_id_cond2 $buyer_id_cond $sequence_no_cond $date_cond $booking_no_cond $internal_ref_cond $file_no_cond 
		order by a.insert_date desc";
		/*$nameArray=sql_select( $sql );
        echo "<pre>";
        print_r($nameArray);die;*/
		// echo $sql;die;
	}
	else if($approval_type==0)
	{

		//Match data..................................
		if($electronicDataArr['user_by'][$approved_by]['BUYER_ID']){
			$where_con .= where_con_using_array(explode(',',$electronicDataArr['user_by'][$approved_by]['BUYER_ID']),0,'a.BUYER_ID');
			$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$approved_by]['BUYER_ID'];
		}
	
		 
		$data_mas_sql = "SELECT A.ID, A.BUYER_ID from wo_booking_mst a where  a.is_short=1 and a.booking_type=1 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and  a.ready_to_approved=1 and a.is_approved<>1  $where_con $buyer_id_cond";
		 //echo $data_mas_sql; die;
		$tmp_sys_id_arr=array();
		$data_mas_sql_res=sql_select( $data_mas_sql );
		foreach ($data_mas_sql_res as $row)
		{ 
			for($seq=($electronicDataArr['user_by'][$approved_by]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
				if((in_array($row['BUYER_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BUYER_ID'])) || $row['BUYER_ID']==0) )
				{
					if($electronicDataArr['sequ_by'][$seq]['BYPASS']==1){
						$tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
					}
					else{
						$tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
						break;
					}
				}
			}
		}
		//..........................................Match data;

		//print_r($tmp_sys_id_arr);die;
		
		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
 			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');
			
			if($tmp_sys_id_arr[$seq]){
				if($sql!=''){$sql .=" UNION ALL ";}

				$sql .= "SELECT A.ID, A.BOOKING_NO_PREFIX_NUM AS PREFIX_NUM,A.BOOKING_NO, A.ITEM_CATEGORY, A.FABRIC_SOURCE, A.COMPANY_ID, A.BOOKING_TYPE, A.IS_SHORT, A.BUYER_ID, A.SUPPLIER_ID, A.DELIVERY_DATE, A.BOOKING_DATE, A.JOB_NO, A.PO_BREAK_DOWN_ID, A.IS_APPROVED, a.APPROVED_BY, A.IS_APPLY_LAST_UPDATE, A.PAY_MODE from wo_booking_mst a
					where  a.is_short=1 and a.booking_type=1 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and  a.ready_to_approved=1 and a.is_approved<>1 and a.APPROVED_SEQU_BY=$seq   $sys_con ";	
			}
		
		}
		

	}
	else
	{
		
 		$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved,  a.approved_by, a.is_apply_last_update, a.pay_mode
		from wo_booking_mst a, APPROVAL_MST d
		where d.mst_id=a.id and  a.is_short=1 and a.booking_type=1 and a.APPROVED_SEQU_BY=d.SEQUENCE_NO and a.company_id=$company_name and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and  a.ready_to_approved=1 $buyer_id_cond  and d.SEQUENCE_NO={$electronicDataArr[user_by][$approved_by][SEQUENCE_NO]}  $whereCon 
		order by a.insert_date desc";
	}
	 //echo $sql;
	?>
    
    <script>
	
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();	
			var approval_id = $("#approval_id_"+i).val();
			
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			
			var title = 'Approval Cause Info';	
			var page_link = 'requires/short_feb_booking_approval_controller_v2.php?data='+data+'&action=appcause_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}
		
		function openmypage_app_instrac(wo_id,app_type,i)
		{
			var txt_appv_instra = $("#txt_appv_instra_"+i).val();	
			var approval_id = $("#approval_id_"+i).val();
			
			var data=wo_id+"_"+app_type+"_"+txt_appv_instra+"_"+approval_id;
			
			var title = 'Approval Instruction';	
			var page_link = 'requires/fabric_booking_approval_controller.php?data='+data+'&action=appinstra_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				
				$('#txt_appv_instra_'+i).val(appv_cause.value);
			}
		}
		
		function openmypage_unapp_request(wo_id,app_type,i)
		{
			
			var data=wo_id;
			
			var title = 'Un Approval Request';	
			var page_link = 'requires/short_feb_booking_approval_controller_v2.php?data='+data+'&action=unappcause_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
				
				$('#txt_unappv_req_'+i).val(unappv_request.value);
			}
		}

		function openmypage_remarks(booking_id,app_user_id){
			var title = 'Remarks';	
			var page_link = 'requires/short_feb_booking_approval_controller_v2.php?booking_id='+booking_id+'&app_user_id='+app_user_id+'&action=remarks_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=340px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var remarks=this.contentDoc.getElementById("txt_remarks");
				$('#txt_remarks_'+booking_id).val(remarks.value);
			}	
		}


	</script>
    <?
		 $fset=1570;
		 $table1=1550; 
		 $table2=1532; 
	?>
    
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<?=$fset; ?>px; margin-top:10px">
        <legend>Short Fabric Booking Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table1; ?>" class="rpt_table" >
                <thead>
                	<th width="25">&nbsp;</th>
                    <th width="25">SL</th>
                    <th width="130">Booking No</th>
                    <th width="80">Type</th>
                    <th width="100">Booking Date</th>
                    <th width="125">Buyer</th>
                    <th width="160">Supplier</th>
                    <th width="100">Job No</th>
                    <th width="70">Internal Ref</th>
                    <th width="70">File</th>
                    <th width="110">Dealing Merchant</th>
                    <th width="50">Image</th>
                    <th width="50">File</th>
                    <th width="90">Delivery Date</th>
                    <? 
						if($approval_type==0) echo "<th width='80'>Appv Instra</th>";
						if($approval_type==1)echo "<th width='80'>Un-appv request</th>"; 
					?>
                    <th width="100"><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
					<th>Remarks</th>
                </thead>
            </table>            
            <div style="width:<? echo $table1; ?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table2; ?>" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
                        $i=1; $all_approval_id='';
                        $nameArray=sql_select( $sql ); 
                        foreach ($nameArray as $row)
                        {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$value=$row[csf('id')];
								
								if($row[csf('booking_type')]==4) 
								{
									$booking_type="Sample";
									$type=3;
								}
								else
								{
									if($row[csf('is_short')]==1) $booking_type="Short"; else $booking_type="Main"; 
									$type=$row[csf('is_short')];
								}
								
								$dealing_merchant=$dealing_merchant_array[$job_dealing_merchant_array[$row[csf('job_no')]]];
								
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
								$report_action='';
						if($type==1){
							$print_report_format1=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
							
							$report_id=explode(",",$print_report_format1);
						
							if($report_id[0]==8){
								$report_action='show_fabric_booking_report';
							}if($report_id[0]==9){
								$report_action='show_fabric_booking_report3';
							}elseif($report_id[0]==136){
								$report_action='print_booking_3';
							}elseif($report_id[0]==10){
								$report_action='show_fabric_booking_report4';
							}elseif($report_id[0]==46){
								$report_action='show_fabric_booking_report_urmi';
							}elseif($report_id[0]==244){
								$report_action='show_fabric_booking_report_ntg';
							}elseif($report_id[0]==7){
								$report_action='show_fabric_booking_report5';
							}elseif($report_id[0]==28){
								$report_action='show_fabric_booking_report_akh';
							}elseif($report_id[0]==45){
								$report_action='show_fabric_booking_report_urmi';
							}elseif($report_id[0]==53){
								$report_action='show_fabric_booking_report_jk';
							}elseif($report_id[0]==73){
								$report_action='show_fabric_booking_report_b6';
							}
						}elseif($type==2){
							$print_report_format2=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
					
							$report_id=explode(",",$print_report_format2);
							if($report_id[0]==1){
								$report_action='show_fabric_booking_report_gr';
							}if($report_id[0]==2){
								$report_action='show_fabric_booking_report';
							}elseif($report_id[0]==3){
								$report_action='show_fabric_booking_report3';
							}elseif($report_id[0]==4){
								$report_action='show_fabric_booking_report1';
							}elseif($report_id[0]==16){
								$report_action='show_fabric_booking_report16';
							}elseif($report_id[0]==6){
								$report_action='show_fabric_booking_report4';
							}elseif($report_id[0]==7){
								$report_action='show_fabric_booking_report5';
							}elseif($report_id[0]==28){
								$report_action='show_fabric_booking_report_akh';
							}elseif($report_id[0]==45){
								$report_action='show_fabric_booking_report_urmi';
							}elseif($report_id[0]==53){
								$report_action='show_fabric_booking_report_jk';
							}elseif($report_id[0]==73){
								$report_action='show_fabric_booking_report_b6';
							}
						}
						$report_type=2;
						if($print_cond==1)
						{	
							?>
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>"> 
								<td width="25" align="center" valign="middle">
									<input type="checkbox" id="tbl_<?=$i; ?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
									<input id="booking_id_<?=$i; ?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
									<input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('booking_no')]; ?>" />
									<input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
									<input id="last_update_<? echo $i;?>" name="last_update[]" type="hidden" value="<? echo $row[csf('is_apply_last_update')]; ?>" />
									<input id="<? echo strtoupper($row[csf('booking_no')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
								</td>   
								<td width="25" id="td_<? echo $i; ?>" style="cursor:pointer" align="center" onClick="generate_worder_report2(<? echo $type; ?>,'<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('company_id')]; ?>,'<? echo $row[csf('po_break_down_id')]; ?>',<? echo $row[csf('item_category')].','.$row[csf('fabric_source')]; ?>,'<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('is_approved')]; ?>','show_fabric_booking_report3')"><? echo $i; ?></td>
								<td width="130">
									<p><a href='##' style='color:#000' onClick="generate_worder_report(<? echo $type; ?>,<? echo $report_type; ?>,'<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('company_id')]; ?>,'<? echo $row[csf('po_break_down_id')]; ?>',<? echo $row[csf('item_category')].','.$row[csf('fabric_source')]; ?>,'<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('is_approved')]; ?>','<?=$report_action;?>',' <? echo $i; ?>')"><? echo $row[csf('prefix_num')]; ?></a></p>
								</td>
								<td width="80" align="center"><p><? echo $booking_type; ?></p></td>
								<td width="100" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
								<td width="125"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
								<td width="160"><p><? echo ($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) ? $companyFullName_arr[$row[csf('supplier_id')]] : $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
								
								<td width="70"><? echo $row[csf('grouping')]; ?></td>
								<td width="70"><? echo $row[csf('file_no')]; ?></td>
								<td width="110" id="dealing_merchant_<? echo $i;?>"><p><? echo $dealing_merchant; ?>&nbsp;</p></td>
								<td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','img');">View</a></td>
								<td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','file');">View</a></td>
								<td align="center" width="90"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>                                
								<?
								if($approval_type==0){
									echo "<td align='center' width='80'>
										<Input name='txt_appv_instra[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_appv_instra_".$i."' style='width:70px' maxlength='50' onClick='openmypage_app_instrac(".$value.",".$approval_type.",".$i.")'></td>";
								}
								else if($approval_type==1){
									echo "<td align='center' width='80'>
									<Input name='txt_unappv_req[]' class='text_boxes' readonly placeholder='Please Brows' ID='txt_unappv_req_".$i."' style='width:65px' maxlength='50' onClick='openmypage_unapp_request(".$value.",".$approval_type.",".$i.")'></td>"; 
								}
									
								?>
								
								<td align="center" width="100">
									<Input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Brows" ID="txt_appv_cause_<? echo $i;?>" style="width:80px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<? echo $value; ?>,<? echo $approval_type; ?>,<? echo $i;?>)">&nbsp;</td>
							
									<td><input type="text" id="txt_remarks_<? echo $value; ?>" onClick="openmypage_remarks(<?= $value; ?>,<?= $approved_by; ?>)" class="text_boxes" value=""></td>
								</tr>
							<?
							$i++;
						}
								
							/*	if($all_approval_id!="")
								{
									$con = connect();
									$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
									disconnect($con);
								}*/
							}
							$isApp="";
							if($approval_type==1) $isApp=" display:none"; else $isApp="";
                        ?>
						
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<? echo $table1; ?>" class="rpt_table">
				<tfoot>
                    <td width="25" align="center" style=" <?=$isApp; ?>"><input type="checkbox" id="all_check" onClick="check_all('all_check')" />
					<span style="display:none"><? echo $all_approval_id; ?></span>
				</td>
                    <td colspan="2" align="left">
						<input type="button" value="<? if($approval_type==1 || $previous_approved_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>
						<input type="button" value="Deny" class="formbutton" style="width:100px;<?= ($approval_type==1)?' display:none':''; ?>" onClick="submit_approved(<?=$i; ?>,5);"/>
				</td>

					

				</tfoot>
			</table>
        </fieldset>
    </form>         
	<?
	exit();	
}

if($action=="report_generate_2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$companyFullName_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$brand=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	
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
	// echo $buyer_id_cond;
	
	$file_no=str_replace("'","",$txt_file_no);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$booking_no=str_replace("'","",$txt_booking_no);
	
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and c.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and c.grouping like '%".trim($internal_ref)."%' "; 
	if ($booking_no=="") $booking_no_cond=""; else $booking_no_cond=" and a.booking_no_prefix_num='".trim($booking_no)."' ";
	$date_cond='';
	//echo change_date_format(str_replace("'","",$txt_date));die;
	if(str_replace("'","",$txt_date)!="")
	{
		if($db_type==2)
		{
			$txt_date1=str_replace("'","",$txt_date);
			$txt_date="'".date("d-M-Y",strtotime($txt_date1))."'";
		}
		
		//echo $txt_date;die;
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.booking_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.booking_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.booking_date=$txt_date";
		else $date_cond='';
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}
	//$user_id=181;
	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from wo_po_details_master","job_no","dealing_marchant");

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
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Short Fabric Booking.</font>";
		die;
	}

	if($previous_approved==1 && $approval_type==1)
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
		
		$sequence_no_cond=" and b.sequence_no<'$user_sequence_no'";

		/*$sql="SELECT a.id,  a.company_id, a.buyer_id, a.style_ref, a.style_desc, a.quot_date,a.est_ship_date, a.approved, a.inserted_by, b.id as approval_id, a.garments_nature from wo_price_quotation a, approval_history b where a.id=b.mst_id and b.entry_form=10 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and a.ready_to_approved=1 and b.current_approval_status=1 and a.approved in(1,3) and b.approved_by!=$user_id $buyer_id_cond2 $sequence_no_cond $date_cond order by a.id ASC";*/

		$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,c.grouping, c.file_no, a.pay_mode,a.inserted_by,max(b.approved_no) as revised_no,a.insert_date,b.approved_date,a.remarks
		from wo_booking_mst a, approval_history b, wo_po_break_down c
		where a.id=b.mst_id and a.job_no=c.job_no_mst and b.entry_form=12 and a.is_short=1 and a.booking_type=1 and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved in(1,3) and b.approved_by!=$user_id $buyer_id_cond2 $sequence_no_cond $date_cond $booking_no_cond $internal_ref_cond $file_no_cond 
		group by a.id, a.booking_no_prefix_num ,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, b.id , b.sequence_no, b.approved_by, a.is_apply_last_update,c.grouping, c.file_no, a.pay_mode,a.inserted_by,a.insert_date,b.approved_date,a.remarks
		order by a.insert_date desc";
		/*$nameArray=sql_select( $sql );
        echo "<pre>";
        print_r($nameArray);die;*/
		// echo $sql;die;
	}
	
	else if($approval_type==0)
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
		
		//echo $user_sequence_no.'_'.$sequence_no;die;
		
		if($user_sequence_no==$min_sequence_no)
		{
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
			
			$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,c.grouping, c.file_no, a.pay_mode,a.inserted_by, (select max(d.approved_no) as revised_no from approval_history d where d.mst_id =a.id and d.entry_form=12) as revised_no ,a.insert_date,a.remarks
			from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c  
			where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$company_name and a.is_short=1 and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=$approval_type and b.fin_fab_qnty>0 $buyer_id_cond $buyer_id_cond2 $internal_ref_cond $file_no_cond $booking_no_cond $date_cond 
			group by a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no, a.pay_mode ,a.inserted_by ,a.insert_date,a.remarks
			order by a.insert_date desc";
		}
		
		else if($sequence_no=="")
		{
			
			$buyer_ids=$buyer_ids_array[$user_id]['u'];
			if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
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
				$booking_id_sql="select distinct (a.id) as booking_id from wo_booking_mst a, approval_history b where a.id=b.mst_id  and a.company_id=$cbo_company_name and b.sequence_no in ($sequence_no_by_no) and b.entry_form=12 and b.current_approval_status=1 $buyer_id_cond2 $buyer_id_cond      $seqCond $booking_no_cond $date_cond
				union
				select distinct (a.id) as booking_id from wo_booking_mst a, approval_history b  where a.id=b.mst_id  and a.company_id=$company_name and b.sequence_no in ($sequence_no_by_yes) and b.entry_form=12 and b.current_approval_status=1 $buyer_id_cond2 $buyer_id_cond $booking_no_cond $date_cond";
				//echo $booking_id_sql;die;
				$bResult=sql_select($booking_id_sql);
				foreach($bResult as $bRow)
				{
					$booking_id.=$bRow[csf('booking_id')].",";
				}
				
				$booking_id=chop($booking_id,',');

				$booking_id_app_sql=sql_select("select b.mst_id as booking_id from wo_booking_mst a, approval_history b 
				where a.id=b.mst_id and  a.company_id=$company_name and b.sequence_no=$user_sequence_no and b.entry_form=12 and a.ready_to_approved=1 and b.current_approval_status=1 $buyer_id_cond2 $buyer_id_cond $booking_no_cond $date_cond");
				
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
			
			
			$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no, a.pay_mode,a.inserted_by,  (select max(d.approved_no) as revised_no from approval_history d where d.mst_id =a.id and d.entry_form=12) as revised_no  ,a.remarks
			from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c 
			where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$company_name and a.is_short=1 and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved in(0,3) and b.fin_fab_qnty>0 $buyerIds_cond $buyer_id_cond2 $buyer_id_cond $booking_id_cond $booking_no_cond $internal_ref_cond $file_no_cond $date_cond  
			group by a.id, a.booking_no,a.booking_no_prefix_num, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no, a.pay_mode,a.inserted_by,a.remarks ";
			//echo $sql;die;
			if($booking_id!="")
			{
				$sql.=" union all
				select a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no, a.pay_mode,a.inserted_by,  (select max(d.approved_no) as revised_no from approval_history d where d.mst_id =a.id and d.entry_form=12) as revised_no, a.remarks  from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and a.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.company_id=$company_name and a.is_short=1 and a.booking_type=1 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved=3 and b.fin_fab_qnty>0 and a.id in($booking_id) $buyer_id_cond $buyer_id_cond2 $booking_no_cond $internal_ref_cond $file_no_cond $date_cond group by a.id, a.booking_no,a.booking_no_prefix_num, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no, a.pay_mode,a.inserted_by, a.remarks ";
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
					$sequence_no_by_pass=return_field_value("group_concat(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0");// and bypass=1
				}
				else
				{
					$sequence_no_by_pass=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and is_deleted=0","sequence_no");// and bypass=1
				}
			}

			if($sequence_no_by_pass=="") $sequence_no_cond=" and b.sequence_no='$sequence_no'";
			else $sequence_no_cond=" and b.sequence_no in ($sequence_no_by_pass)";
			
			$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,c.grouping, c.file_no, a.pay_mode,a.inserted_by ,max(b.approved_no) as revised_no,a.insert_date,b.approved_date,a.remarks
			from wo_booking_mst a, approval_history b, wo_po_break_down c  
			where a.id=b.mst_id and a.job_no=c.job_no_mst and b.entry_form=12 and a.is_short=1 and a.booking_type=1 and a.company_id=$company_name and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved=3 $buyer_id_cond $buyer_id_cond2 $sequence_no_cond $booking_no_cond $internal_ref_cond $file_no_cond $date_cond 
			group by   a.id, a.booking_no_prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, b.id , b.sequence_no, b.approved_by, a.is_apply_last_update,c.grouping, c.file_no, a.pay_mode,a.inserted_by,a.insert_date,b.approved_date,a.remarks
			order by a.insert_date desc";
	
		}
	}
	else
	{
		$buyer_ids=$buyer_ids_array[$user_id]['u'];
		if($buyer_ids=="") $buyer_id_cond2=""; else $buyer_id_cond2=" and a.buyer_id in($buyer_ids)";
			
		$sequence_no_cond=" and b.sequence_no='$user_sequence_no'";
		$sql="SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, b.id as approval_id, b.sequence_no, b.approved_by, a.is_apply_last_update,c.grouping, c.file_no, a.pay_mode,a.inserted_by,max(b.approved_no) as revised_no,a.insert_date,b.approved_date,a.remarks
		from wo_booking_mst a, approval_history b, wo_po_break_down c 
		where a.id=b.mst_id and a.job_no=c.job_no_mst and b.entry_form=12 and a.is_short=1 and a.booking_type=1 and a.company_id=$company_name and a.item_category in(2,3,13) and a.status_active=1 and a.is_deleted=0 and b.current_approval_status=1 and a.ready_to_approved=1 and a.is_approved in(1,3) $buyer_id_cond2 $buyer_id_cond $sequence_no_cond $date_cond $booking_no_cond $internal_ref_cond $file_no_cond 
		group by  a.id, a.booking_no_prefix_num ,a.booking_no, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.po_break_down_id, a.is_approved, b.id, b.sequence_no, b.approved_by, a.is_apply_last_update,c.grouping, c.file_no, a.pay_mode,a.inserted_by,a.insert_date,b.approved_date,a.remarks
		order by a.insert_date desc";
	}
	// echo $sql;
	?>
    
    <script>
		function openmypage_app_cause_show_2(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();	
			var approval_id = $("#approval_id_"+i).val();
			
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			
			var title = 'Approval Cause Info';	
			var page_link = 'requires/short_feb_booking_approval_controller_v2.php?data='+data+'&action=appcause_popup_show_2';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}
		
		function openmypage_app_instrac(wo_id,app_type,i)
		{
			var txt_appv_instra = $("#txt_appv_instra_"+i).val();	
			var approval_id = $("#approval_id_"+i).val();
			
			var data=wo_id+"_"+app_type+"_"+txt_appv_instra+"_"+approval_id;
			
			var title = 'Approval Instruction';	
			var page_link = 'requires/fabric_booking_approval_controller.php?data='+data+'&action=appinstra_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				
				$('#txt_appv_instra_'+i).val(appv_cause.value);
			}
		}
		
		function openmypage_unapp_request(wo_id,app_type,i)
		{
			
			var data=wo_id;
			
			var title = 'Un Approval Request';	
			var page_link = 'requires/short_feb_booking_approval_controller_v2.php?data='+data+'&action=unappcause_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
				
				$('#txt_unappv_req_'+i).val(unappv_request.value);
			}
		}
	</script>
    
    <?
    	 $new_column=970;
		 if($approval_type==0)
		 {
			$fset=1470+$new_column;//70-50=20
			$table1=1450+$new_column; //50-32=18
			$table2=1432+$new_column; //0
		 }
		 else if($approval_type==1)
		 {
			 $fset=1470+$new_column;//70-50=20
			 $table1=1450+$new_column; //50-32=18
			 $table2=1432+$new_column; //0
		 }

		$lib_user = return_library_array("select id,user_name from user_passwd","id","user_name");
		//Pre cost button---------------------------------
		$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id in (43) and is_deleted=0 and status_active=1");
		$format_ids=explode(",",$print_report_format_ids);
		$row_id=$format_ids[0];
		$print_report_format_ids_wvn = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id in (122) and is_deleted=0 and status_active=1");
		$format_ids_wvn=explode(",",$print_report_format_ids_wvn);
		$row_id_wvn=$format_ids_wvn[0];

		$print_report_format_ids_partial = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=35 and is_deleted=0 and status_active=1");
		$format_ids_partial=explode(",",$print_report_format_ids_partial);

		$print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
		$format_ids_2=explode(",",$print_report_format_ids2);
	?>
    
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<? echo $fset; ?>px; margin-top:10px">
        <legend>Short Fabric Booking Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $table1; ?>" class="rpt_table" >
                <thead>
                	<th style="word-break: break-all;" width="50"></th>
                    <th style="word-break: break-all;" width="40">SL</th>
                    <th style="word-break: break-all;" width="70">Insert By/ Merchandiser</th>
                    <th style="word-break: break-all;" width="50">Image</th>
                    <th style="word-break: break-all;" width="100">Short Fabric<br>Booking Date</th>
                    <th style="word-break: break-all;" width="70">EFR/Short Fabric<br>Booking Date</th>
                    <th style="word-break: break-all;" width="60">Mkt Cost</th>
                    <th style="word-break: break-all;" width="130">EFR/Short Fabric Booking No</th>
                    <th style="word-break: break-all;" width="70">Last Version</th>
                    <th style="word-break: break-all;" width="50">Year</th>
                    <th style="word-break: break-all;" width="100">Job No</th>
                    <th style="word-break: break-all;" width="100">Main Fabric<br>Booking No</th>
                    <th style="word-break: break-all;" width="100">Style Ref.</th>
                    <th style="word-break: break-all;" width="125">Buyer</th>
                    <th style="word-break: break-all;" width="80">Fabric Source</th>
                    <th style="word-break: break-all;" width="80">Type</th>
                    <th style="word-break: break-all;" width="160">Supplier</th>
                    <th style="word-break: break-all;" width="100">Product Dept</th>
                    <th style="word-break: break-all;" width="100">Sub Dept</th>
                    <th style="word-break: break-all;" width="80">Brand</th>
                    <th style="word-break: break-all;" width="70">Ship Start</th>
                    <th style="word-break: break-all;" width="70">Ship End</th>
                    <th style="word-break: break-all;" width="90">Delivery Date</th>
                    <? 
					if($approval_type==0) echo "<th style='word-break: break-all;' width='80'>Un-appv<br>request</th>";
					if($approval_type==1) echo "<th style='word-break: break-all;' width='80'>Un-appv<br>request</th>"; 
					?>
                    <th style="word-break: break-all;" width="100"><? if($approval_type==0) echo "Not Appv.<br>Cause" ; else echo "Refusing Cause"; ?></th>
                    <th style="word-break: break-all;" >Remarks</th>
                </thead>
            </table>            
            <div style="width:<? echo $table1; ?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$table2; ?>" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
						 	
                            $i=1; $all_approval_id='';
                           // echo $sql;
                            $nameArray=sql_select( $sql ); 
                            $job_no_arr=array();
                            $po_arr=array();
                            $booking_id_arr=array();
                            foreach ($nameArray as $row) 
                            {
                            	array_push($job_no_arr, $row[csf('job_no')]);
                            	array_push($po_arr, $row[csf('po_break_down_id')]);
                            	array_push($booking_id_arr, $row[csf('id')]);
                            }
                           // $sql_job="SELECT a.dep"
                            $po_cond=where_con_using_array($po_arr,0,"a.id");
                            $job_cond=where_con_using_array($job_no_arr,1,"b.job_no");
                            $job_cond_2=where_con_using_array($job_no_arr,1,"master_tble_id");
                            $imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where file_type=1 $job_cond_2",'master_tble_id','image_location');
						  $booking_id_cond=where_con_using_array($booking_id_arr,1,"booking_id");
						  $sql_req="select approval_cause,booking_id from fabric_booking_approval_cause where entry_form=12  and approval_type=2 and status_active=1 and is_deleted=0  $booking_id_cond";       
						  $nameArray_req=sql_select($sql_req);
						   $unappv_req_arr=array();
						  foreach($nameArray_req as $row)
						  {
						    $unappv_req_arr[$row[csf('booking_id')]].=$row[csf('approval_cause')]."***";
						  }

						  $sql_cause="select approval_cause,booking_id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=12   and approval_type='$approval_type' and status_active=1 and is_deleted=0 $booking_id_cond";	
						    //echo 	$sql_cause;		
							$nameArray_cause=sql_select($sql_cause);
							$app_cause_arr=array();
							foreach($nameArray_cause as $row)
							{
								$app_cause_arr[$row[csf("booking_id")]].=$row[csf("approval_cause")]."***";
							}

							$po_res =sql_select("SELECT b.booking_no, max(a.shipment_date) as shipment_end,min(a.shipment_date) as shipment_start from   wo_po_break_down a, wo_booking_dtls b where a.id=b.po_break_down_id and a.is_deleted=0 and b.is_deleted=0  $po_cond group by b.booking_no ");

							$sub_dep_arr=return_library_array( "select id, sub_department_name from lib_pro_sub_deparatment", "id", "sub_department_name");


							$shipment_date_arr=array();
							foreach ($po_res as $row) {
								$shipment_date_arr[$row[csf('booking_no')]]['shipment_end']=$row[csf('shipment_end')];
								$shipment_date_arr[$row[csf('booking_no')]]['shipment_start']=$row[csf('shipment_start')];
							}

							$main_fab_res =sql_select("SELECT b.booking_no,a.job_no_mst as job_no,c.entry_form,b.id,c.fabric_source,c.item_category,c.is_approved,c.company_id,c.booking_no_prefix_num  from   wo_po_break_down a, wo_booking_dtls b,wo_booking_mst c where a.id=b.po_break_down_id and b.booking_no=c.booking_no  and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and b.booking_type=1 and b.is_short=2 and c.entry_form in (118,108,86)  $po_cond group by b.booking_no,a.job_no_mst,c.entry_form,b.id,c.fabric_source,c.item_category,c.is_approved,c.company_id,c.booking_no_prefix_num ");


							$job_wise_main_fabric_data=array();

							foreach ($main_fab_res as $row) {
								$job_wise_main_fabric_data[$row[csf('job_no')]][$row[csf('booking_no')]]['entry_form']=$row[csf('entry_form')];
								$job_wise_main_fabric_data[$row[csf('job_no')]][$row[csf('booking_no')]]['item_category']=$row[csf('item_category')];
								$job_wise_main_fabric_data[$row[csf('job_no')]][$row[csf('booking_no')]]['fabric_source']=$row[csf('fabric_source')];
								$job_wise_main_fabric_data[$row[csf('job_no')]][$row[csf('booking_no')]]['is_approved']=$row[csf('is_approved')];
								$job_wise_main_fabric_data[$row[csf('job_no')]][$row[csf('booking_no')]]['company_id']=$row[csf('company_id')];
								$job_wise_main_fabric_data[$row[csf('job_no')]][$row[csf('booking_no')]]['booking_no_prefix_num']=$row[csf('booking_no_prefix_num')];
								$job_wise_main_fabric_data[$row[csf('job_no')]][$row[csf('booking_no')]]['po_id'].=$row[csf('id')].",";
							}
							
							//print_r($job_wise_main_fabric_data);

							$year='';
							if($db_type==0)
							{

								$year="YEAR(a.insert_date) as year";
							}
							else if($db_type==2 || $db_type==0)
							{

								$year="to_char(a.insert_date,'YYYY') as year";
							}

							$precost=sql_select("SELECT b.costing_date,b.entry_from,a.quotation_id,a.job_no,a.brand_id,a.pro_sub_dep,a.product_dept,a.garments_nature,a.style_ref_no,a.buyer_name,$year from wo_po_details_master a, wo_pre_cost_mst b where a.job_no=b.job_no and  a.status_active in (1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_cond");
							
							$job_wise_data=array();
							foreach ($precost as $row) {
								$job_wise_data[$row[csf('job_no')]]['costing_date']=$row[csf('costing_date')];
								$job_wise_data[$row[csf('job_no')]]['entry_from']=$row[csf('entry_from')];
								$job_wise_data[$row[csf('job_no')]]['quotation_id']=$row[csf('quotation_id')];
								$job_wise_data[$row[csf('job_no')]]['brand_id']=$brand[$row[csf('brand_id')]];
								$job_wise_data[$row[csf('job_no')]]['pro_sub_dep']=$sub_dep_arr[$row[csf('pro_sub_dep')]];
								$job_wise_data[$row[csf('job_no')]]['product_dept']=$product_dept[$row[csf('product_dept')]];
								$job_wise_data[$row[csf('job_no')]]['garments_nature']=$row[csf('garments_nature')];
								$job_wise_data[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
								$job_wise_data[$row[csf('job_no')]]['buyer_name']=$row[csf('buyer_name')];
								$job_wise_data[$row[csf('job_no')]]['year']=$row[csf('year')];
							}
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$value=$row[csf('id')];
								
								if($row[csf('booking_type')]==4) 
								{
									$booking_type="Sample";
									$type=3;
								}
								else
								{
									if($row[csf('is_short')]==1) $booking_type="Short"; else $booking_type="Main"; 
									$type=$row[csf('is_short')];
								}
								
								$dealing_merchant=$dealing_merchant_array[$job_dealing_merchant_array[$row[csf('job_no')]]];
								
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

					    // short fabric booking hyperlink//
								$report_action='';
						if($type==1){
							$print_report_format1=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=2 and report_id=2 and is_deleted=0 and status_active=1");
						
							$report_id=explode(",",$print_report_format1);
						
							if($report_id[0]==8){
								$report_action='show_fabric_booking_report';
							}if($report_id[0]==9){
								$report_action='show_fabric_booking_report3';
							}elseif($report_id[0]==136){
								$report_action='print_booking_3';
							}elseif($report_id[0]==10){
								$report_action='show_fabric_booking_report4';
							}elseif($report_id[0]==46){
								$report_action='show_fabric_booking_report_urmi';
							}elseif($report_id[0]==244){
								$report_action='show_fabric_booking_report_ntg';
							}elseif($report_id[0]==7){
								$report_action='show_fabric_booking_report5';
							}elseif($report_id[0]==28){
								$report_action='show_fabric_booking_report_akh';
							}elseif($report_id[0]==45){
								$report_action='show_fabric_booking_report_urmi';
							}elseif($report_id[0]==53){
								$report_action='show_fabric_booking_report_jk';
							}elseif($report_id[0]==73){
								$report_action='show_fabric_booking_report_b6';
							}

						}elseif($type==2){
							$print_report_format2=return_field_value("format_id","lib_report_template","template_name ='".$company_name."' and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
					
							$report_id=explode(",",$print_report_format2);
							
							if($report_id[0]==1){
								$report_action='show_fabric_booking_report_gr';
							}if($report_id[0]==2){
								$report_action='show_fabric_booking_report';
							}elseif($report_id[0]==3){
								$report_action='show_fabric_booking_report3';
							}elseif($report_id[0]==4){
								$report_action='show_fabric_booking_report1';
							}elseif($report_id[0]==16){
								$report_action='show_fabric_booking_report16';
							}elseif($report_id[0]==6){
								$report_action='show_fabric_booking_report4';
							}elseif($report_id[0]==7){
								$report_action='show_fabric_booking_report5';
							}elseif($report_id[0]==28){
								$report_action='show_fabric_booking_report_akh';
							}elseif($report_id[0]==45){
								$report_action='show_fabric_booking_report_urmi';
							}elseif($report_id[0]==53){
								$report_action='show_fabric_booking_report_jk';
							}elseif($report_id[0]==73){
								$report_action='show_fabric_booking_report_b6';
							}
						}

							$job_no=$row[csf('job_no')];
							$po_id=$row[csf('po_break_down_id')];

							// job hyperling

									if($row_id_wvn==311 && $job_wise_data[$row[csf('job_no')]]['garments_nature']==3){$action='bom_epm_woven';}
									else if($row_id_wvn==313 && $job_wise_data[$row[csf('job_no')]]['garments_nature']==3){$action='mkt_source_cost';}
									else if($row_id==50){$action='preCostRpt'; } //report_btn_1;
									else if($row_id==51){$action='preCostRpt2';} //report_btn_2;
									else if($row_id==52){$action='bomRpt';} //report_btn_3;
									else if($row_id==63){$action='bomRpt2';} //report_btn_4;
									else if($row_id==142){$action='preCostRptBpkW';}
									else if($row_id==156){$action='accessories_details';} //report_btn_5;
									else if($row_id==157){$action='accessories_details2';} //report_btn_6;
									else if($row_id==158){$action='preCostRptWoven';} //report_btn_7;
									else if($row_id==159){$action='bomRptWoven';} //report_btn_8;
									else if($row_id==170){$action='preCostRpt3';} //report_btn_9;
									else if($row_id==171){$action='preCostRpt4';} //report_btn_10;
									else if($row_id==173){$action='preCostRpt5';} //report_btn_10;
									else if($row_id==192){$action='checkListRpt';}
									else if($row_id==197){$action='bomRpt3';}
									else if($row_id==211){$action='mo_sheet';}
									else if($row_id==215){$action='budget3_details';}
									else if($row_id==221){$action='fabric_cost_detail';}											
									else if($row_id==238){$action='summary';}
									else if($row_id==270){$action='preCostRpt6';}
									else if($row_id==581){$action='costsheet';}
									else if($row_id==730){$action='budgetsheet';}
								

								$function="generate_worder_report_pre_cost('".$action."','".$row[csf('job_no')]."',".$cbo_company_name.",'".$job_wise_data[$row[csf('job_no')]]['buyer_name']."','".$job_wise_data[$row[csf('job_no')]]['style_ref_no']."','".$job_wise_data[$row[csf('job_no')]]['costing_date']."','".$job_wise_data[$row[csf('job_no')]]['entry_from']."','".$job_wise_data[$row[csf('job_no')]]['quotation_id']."',".$job_wise_data[$row[csf('job_no')]]['garments_nature'].");"; 


								$main_fabric_booking="";
								// main fabric booking hyperlink 
								//print_r($job_wise_main_fabric_data[$row[csf('job_no')]]);
								foreach($job_wise_main_fabric_data[$row[csf('job_no')]] as $main_booking_no => $booking_data) 
								{
									$action='';
									if( $booking_data['entry_form']==118) $row_id=$format_ids_2[0];
									else if($booking_data['entry_form']==108) $row_id=$format_ids_partial[0];
									else if($booking_data['entry_form']==86) $row_id=$format_ids_2[0];

									if($booking_data['entry_form']==86) //Budget wise fab booking
									{
										if($row_id==1)$action='show_fabric_booking_report_gr';
										else if($row_id==3)$action='show_fabric_booking_report3';
										else if($row_id==5)$action='show_fabric_booking_report2';
										else if($row_id==6)$action='show_fabric_booking_report4';
										else if($row_id==7)$action='show_fabric_booking_report5';
										else if($row_id==73)$action='show_fabric_booking_report_b6';
										else if($row_id==45)$action='show_fabric_booking_report_urmi';
										else if($row_id==53)$action='show_fabric_booking_report_jk';
										
									}
									else if ($booking_data['entry_form']==108)
									{
										
										if($row_id==143)$action='show_fabric_booking_report_urmi';
										else if($row_id==84)$action='show_fabric_booking_report_urmi_per_job';
										else if($row_id==85)$action='print_booking_3';
										else if($row_id==151)$action='show_fabric_booking_report_advance_attire_ltd';
										else if($row_id==160)$action='print_booking_5';
										else if($row_id==175)$action='print_booking_6';
										else if($row_id==218)$action='print_booking_7';
										else if($row_id==220)$action='print_booking_northern_new';
										else if($row_id==235)$action='print_booking_northern_9';
										else if($row_id==274)$action='print_booking_10';
									   //	"action="+type+get_submitted_data_string('txt_booking_no*cbo_company_name*txt_order_no_id*cbo_fabric_natu*cbo_fabric_source*id_approved_id',"../../")+'&report_title='+$report_title+'&show_yarn_rate='+show_yarn_rate+'&path=../../'

									}
									else if($booking_data['entry_form']==118)
									{
										
										if($row_id==1) $action='show_fabric_booking_report_gr';
										else if($row_id==2) $action='show_fabric_booking_report';
										else if($row_id==3) $action='show_fabric_booking_report3';
										else if($row_id==4) $action='show_fabric_booking_report1';
										else if($row_id==5) $action='show_fabric_booking_report2';
										else if($row_id==6) $action='show_fabric_booking_report4';
										else if($row_id==7) $action='show_fabric_booking_report5';
										else if($row_id==28) $action='show_fabric_booking_report_akh';
										else if($row_id==39) $action='print_booking_39';
										else if($row_id==45) $action='show_fabric_booking_report_urmi';
										else  if($row_id==53) $action='show_fabric_booking_report_jk';
										else if($row_id==73) $action='show_fabric_booking_report_mf';
										else if($row_id==84) $action='show_fabric_booking_report_islam';
										else if($row_id==85) $action='print_booking_3';
										else if($row_id==93) $action='show_fabric_booking_report_print5';
										else if($row_id==129) $action='show_fabric_booking_report_libas';
										else if($row_id==143) $action='show_fabric_booking_report_urmi';
										else if($row_id==193) $action='show_fabric_booking_report_print4';
										else if($row_id==220) $action='print_booking_northern_new';
										else if($row_id==160) $action='print_booking_5';
										else if($row_id==269) $action='show_fabric_booking_report_knit';
										else if($row_id==280) $action='show_fabric_booking_report_print14';
										else if($row_id==274) $action='print_booking_10';
										else if($row_id==304) $action='show_fabric_booking_report10';
										else  if($row_id==719) $action='show_fabric_booking_report16';
										else  if($row_id==723) $action='show_fabric_booking_report17';
										
									}
									$all_po_id=implode(",", array_unique(explode(",", chop($booking_data['po_id'],","))));
									$variable='';
									$variable="<a href='#' onClick=\"generate_worder_report_main('".$main_booking_no."','".$booking_data['company_id']."','".$all_po_id."','".$booking_data['item_category']."','".$booking_data['fabric_source']."','".$row[csf('job_no')]."','".$booking_data['is_approved']."','".$row_id."','".$booking_data['entry_form']."','".$action."','".$i."',".$booking_data['item_category'].")\"> ".$booking_data['booking_no_prefix_num']."<a/>";
									if(!empty($main_fabric_booking))
									{
										$main_fabric_booking.="<br>".$variable;
									}
									else{
										$main_fabric_booking=$variable;
									}
									//echo $variable."loop";
								}
					
								$report_type=2; $variable1="";
								if($row[csf('revised_no')]>0)
								{
									for($q=1; $q<=$row[csf('revised_no')]; $q++)
									{
										if($variable1=="")
											$variable1="<a href='#' onClick=\"generate_worder_report_history('".$type."','".$report_type."','".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report3','".$i."','".$q."'".")\"> ".$q."<a/>";
										else
											$variable1.=", "."<a href='#' onClick=\"generate_worder_report_history('".$type."','".$report_type."','".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report3','".$i."','".$q."'".")\"> ".$q."<a/>";
									}
								}
								if($print_cond==1)
								{	
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
										<td width="50" align="center" valign="middle" style="word-break: break-all;">
											<input type="checkbox" id="tbl_<? echo $i;?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
											<input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
											<input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('booking_no')]; ?>" />
											<input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                            <input id="last_update_<? echo $i;?>" name="last_update[]" type="hidden" value="<? echo $row[csf('is_apply_last_update')]; ?>" />
                                            <input id="<? echo strtoupper($row[csf('booking_no')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
										</td>   
										<td style="word-break: break-all;cursor:pointer" width="40" id="td_<? echo $i; ?>" align="center" onClick="generate_worder_report2(<? echo $type; ?>,'<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('company_id')]; ?>,'<? echo $row[csf('po_break_down_id')]; ?>',<? echo $row[csf('item_category')].','.$row[csf('fabric_source')]; ?>,'<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('is_approved')]; ?>','show_fabric_booking_report3')"><? echo $i; ?></td>
										<td style="word-break: break-all;" width="70"><?=$lib_user[$row[csf('inserted_by')]];?></td>
                                        <td width="50" onClick="openmypage_image('requires/short_feb_booking_approval_controller_v2.php?action=show_image&job_no=<?=$row[csf('job_no')]; ?>','Image View')"><img src='../<?=$imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
										
										<td style="word-break: break-all;" width="100" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
										<td style="word-break: break-all;" width="70" align="center"><? if($row[csf('approved_date')]!="0000-00-00") echo change_date_format($row[csf('approved_date')]); ?>&nbsp;</td>
										<td style="word-break: break-all;" width="60" align="center"><a href='##' style='color:#000' onClick="generate_mkt_report('<?=$job_no; ?>','<?=$row[csf('booking_no')]; ?>','<?=$po_id; ?>','<?=$row[csf('item_category')]; ?>','<?=$row[csf('fabric_source')]; ?>','show_fabric_comment_report')">View</a></td>
										<td style="word-break: break-all;" width="130" align="center">
											<a href='##'  onClick="generate_worder_report(<? echo $type; ?>,<? echo $report_type; ?>,'<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('company_id')]; ?>,'<? echo $row[csf('po_break_down_id')]; ?>',<? echo $row[csf('item_category')].','.$row[csf('fabric_source')]; ?>,'<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('is_approved')]; ?>','<?=$report_action;?>',' <? echo $i; ?>')"><? echo $row[csf('prefix_num')]; ?></a>
										</td>
										<td style="word-break: break-all;word-break:break-all" width="70" align="center" >&nbsp;&nbsp;<?=$variable1; ?></td>
										<td style="word-break: break-all;" width="50" align="center"><?=$job_wise_data[$row[csf('job_no')]]['year']; ?></td>
										<td style="word-break: break-all;" width="100" align="center"><p><a href='##'  onclick="<? echo $function; ?>"><? echo $row[csf('job_no')]; ?></a></p></td>
										<td style="word-break: break-all;" width="100" align="center"><p><? echo $main_fabric_booking; ?></p></td>
										<td style="word-break: break-all;" width="100" align="center"><p><? echo $job_wise_data[$row[csf('job_no')]]['style_ref_no']; ?></p></td>
										<td style="word-break: break-all;" width="125"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
										<td style="word-break: break-all;" width="80" align="center"><p><? echo $fabric_source[$row[csf('fabric_source')]]; ?></p></td>
										<td style="word-break: break-all;" width="80" align="center"><p><? echo $booking_type; ?></p></td>
										
										<td style="word-break: break-all;" width="160"><p><? echo ($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) ? $companyFullName_arr[$row[csf('supplier_id')]] : $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>


										<td style="word-break: break-all;" width="100"><?=$job_wise_data[$row[csf('job_no')]]['product_dept']; ?>&nbsp;</td>
										<td style="word-break: break-all;" width="100"><?=$job_wise_data[$row[csf('job_no')]]['pro_sub_dep']; ?>&nbsp;</td>
										<td style="word-break: break-all;word-break:break-all" width="80" align="center" >
											<?=$job_wise_data[$row[csf('job_no')]]['brand_id']; ?>&nbsp;
										</td>
										<td style="word-break: break-all;word-break:break-all" width="70" align="center" >
											<? if($shipment_date_arr[$row[csf('booking_no')]]['shipment_start']!="0000-00-00") echo change_date_format($shipment_date_arr[$row[csf('booking_no')]]['shipment_start']); ?>&nbsp;
										</td>
										<td style="word-break: break-all;word-break:break-all" width="70" align="center" >
											<? if($shipment_date_arr[$row[csf('booking_no')]]['shipment_end']!="0000-00-00") echo change_date_format($shipment_date_arr[$row[csf('booking_no')]]['shipment_end']); ?>&nbsp;
										</td>
										<td style="word-break: break-all;" align="center" width="90"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>     
										<td align='center' width='80'>                           
                                        <?
                                        $app_cause=implode(",", array_unique(array_filter(explode("***", $app_cause_arr[$row[csf("id")]])))); ;
										if($approval_type==0)echo "<Input name='txt_appv_instra[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_appv_instra_".$i."' style='width:70px' maxlength='50' onClick='openmypage_app_instrac(".$value.",".$approval_type.",".$i.")' >";
                                        $unapproved_req=implode(",", array_unique(array_filter(explode("***", $unappv_req_arr[$row[csf('id')]]))));
										if($approval_type==1)echo "<Input name='txt_unappv_req[]' class='text_boxes' readonly placeholder='Please Brows' ID='txt_unappv_req_".$i."' style='width:65px' maxlength='50' onClick='openmypage_unapp_request(".$value.",".$approval_type.",".$i.")' value='".$unapproved_req."'>"; 
                                        ?>
                                        </td>
                                        <td style="word-break: break-all;" align="center" width="100" title="<?=$app_cause?>">
                                        	<Input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Brows" ID="txt_appv_cause_<? echo $i;?>" style="width:80px" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause_show_2(<? echo $value; ?>,<? echo $approval_type; ?>,<? echo $i;?>)" value="<?=$app_cause?>"></td>
                                        <td style="word-break: break-all;"  align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
									</tr>
									<?
									$i++;
								}
								
							/*	if($all_approval_id!="")
								{
									$con = connect();
									$rID=sql_multirow_update("approval_history","current_approval_status",0,"id",$all_approval_id,1);
									disconnect($con);
								}*/
							}
							$denyBtn="";
							if($approval_type==0) $denyBtn=""; else $denyBtn=" display:none";
							//echo $approval_type.'='.$denyBtn;
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<?=$table1; ?>" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onClick="check_all('all_check')" /><font style="display:none"><? echo $all_approval_id; ?></font></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1 || $previous_approved_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?=$i; ?>,<?=$approval_type; ?>)"/> &nbsp;&nbsp;&nbsp;<input type="button" value="<? if($approval_type==0) echo "Deny"; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
	<?
	exit();	
}

if($action=="show_image")
{
	echo load_html_head_contents("Image PopUp","../../", 1, 1, $unicode);
    extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$jobNos="'".implode(",",explode(',',$job_no))."'";
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id in ($jobNos) and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
			?>
			<td><img src='../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
			<?
        }
        ?>
        </tr>
    </table>
    <?
	exit();
}



 
if ($action=="check_unapprove_req"){
		$sqlidPre="select BOOKING_NO FROM WO_BOOKING_MST WHERE ID NOT IN(select BOOKING_ID from fabric_booking_approval_cause where booking_id in ($data) and entry_form=12 and approval_type=2) AND ID IN($data)";
		$idPreRes=sql_select($sqlidPre);
		//echo $sqlidPre;die;
		$bookingIdArr=array();
		foreach($idPreRes as $idrow)
		{
			$bookingIdArr[$idrow['BOOKING_NO']]=$idrow['BOOKING_NO'];
		}
		echo implode(',',$bookingIdArr);

}
 


if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//$user_id=181;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	

	
	$user_id_approval=0;
	$msg=''; $flag=''; $response='';
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="") 	$user_id_approval=$txt_alter_user_id;
	else $user_id_approval=$user_id;
	
	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id_approval and is_deleted=0");
	//$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and is_deleted=0");
	
	$buyer_arr=return_library_array( "select id, BUYER_NAME  from LIB_BUYER where  IS_DELETED=0 and STATUS_ACTIVE=1", "id", "BUYER_NAME"  );

	
	if($approval_type==0)
	{
		
		//------------------
		$sql="select A.ID,a.BUYER_ID from WO_BOOKING_MST a where a.id in($booking_ids) and a.IS_DELETED=0 and a.STATUS_ACTIVE=1";
		$sqlResult=sql_select( $sql );
		foreach ($sqlResult as $row)
		{
			$matchDataArr[$row[ID]]=array('buyer'=>$row[BUYER_ID],'brand'=>0,'item'=>0,'store'=>0,'department'=>0);
		}
 		
		//$matchDataArr[333]=array('buyer'=>0,'brand'=>0,'item'=>15,'store'=>358);
		$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'page_id'=>$menu_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'match_data'=>$matchDataArr));
		
		 $sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
		 $user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
	 //---------------------
		
		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date"; 
		$id=return_next_id( "id","approval_history", 1 ) ;
		
		$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=12 group by mst_id","mst_id","approved_no");
		

		$app_mst_field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
		$app_mst_id=return_next_id( "id","approval_mst", 1 ) ;
	
	
		
		$approved_no_array=array();
		$booking_nos_all=explode(",",$booking_nos);
		$app_instru_all=explode(",",$appv_instras);
		$book_nos='';
		
		
		$booking_ids_all=explode(",",$booking_ids);
		$i=0;
		foreach($booking_ids_all as $mst_id)
		{
			if(($user_sequence_no*1)==0) { echo "seq**".$user_sequence_no; disconnect($con);die; }
			$booking_id_arr[]=$mst_id;
			$approved_no=$max_approved_no_arr[$mst_id]+1;
			
			$booking_no=$booking_nos_all[$i];
			$app_instru=$app_instru_all[$i];
			$approved_no_array[$booking_no]=$approved_no;
			
			//History................
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",12,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."',".$app_instru.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			$id=$id+1;
			//App Mst........................
			if($app_data_array!=''){$app_data_array.=",";}
			$app_data_array.="(".$app_mst_id.",12,".$mst_id.",".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$app_mst_id=$app_mst_id+1;
			
			// echo "21**";print_r(max($finalDataArr['final_seq'][$mst_id]));die;
			
			//mst data.......................
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			$data_array_up[$mst_id] =explode(",",("".$approved.",".$user_sequence_no."")); 
			
			
			$i++;	
		}
		
	
		
		
		
		
		$book_nos=implode(",",$booking_nos_all);
		
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
			
			
			$sql_insert="insert into wo_booking_mst_hstry(id, approved_no, booking_id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,ready_to_approved, is_apply_last_update, rmg_process_breakdown,revised_date) 
				select	
				'', $approved_string_mst, id, booking_type, is_short, booking_no_prefix, booking_no_prefix_num, booking_no, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, currency_id, exchange_rate, pay_mode, source, booking_date, delivery_date, booking_month, booking_year, supplier_id, attention, booking_percent, colar_excess_percent, cuff_excess_percent, is_approved, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,ready_to_approved, is_apply_last_update, rmg_process_breakdown,'".date('d-M-Y',time())."' from wo_booking_mst where booking_no in ($book_nos)";
		 
			
			$sql_insert_dtls="insert into wo_booking_dtls_hstry(id, approved_no, booking_dtls_id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_qty, gmt_item, responsible_dept, responsible_person, reason, gmts_size, gmts_color_id,revised_date) 
				select	
				'', $approved_string_dtls, id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_qty, gmt_item, responsible_dept, responsible_person, reason, gmts_size, gmts_color_id,'".date('d-M-Y',time())."' from wo_booking_dtls where booking_no in ($book_nos)";
				
		}
		
		$flag=1;
		if($flag==1) 
		{
			$field_array_booking_update = "is_approved*APPROVED_SEQU_BY";
			$rID=execute_query(bulk_update_sql_statement( "wo_booking_mst", "id", $field_array_booking_update, $data_array_up, $booking_id_arr));
			if($rID) $flag=1; else $flag=0;
		}
		
		if($flag==1) 
		{
			$rID0=sql_insert("approval_mst",$app_mst_field_array,$app_data_array,0);
			if($rID0) $flag=1; else $flag=0; 
		}
		
		if($flag==1) 
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=12 and mst_id in ($booking_ids)";
			$rID1=execute_query($query,1);;
			if($rID1) $flag=1; else $flag=0; 
		} 
		
		if($flag==1) 
		{
			$rID2=sql_insert("approval_history",$field_array,$data_array,0);
			if($rID2) $flag=1; else $flag=0; 
		}
		
		  
		   //echo "21**". $app_data_array;oci_rollback($con); die;
		
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
			} 
		}
		$response=$booking_ids;
		
		
		// echo "21**".$rID."**".$rID0."**".$rID1."**".$rID2."**".$rID3."**".$sql_insert_dtls;oci_rollback($con);die;
		
		if($flag==1) $msg='19'; else $msg='21';
		
		//-------------auto approve if partial allow is yes----------------
			//auto_approved(array(company_id=>$cbo_company_name,app_necessity_page_id=>6,mst_table=>'wo_booking_mst',sys_id=>$booking_ids,approval_by=>$user_id_approval));

		//----------------------------------
		
		
		
	}
	else if($approval_type==1)
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
		
		$sqlidPre="select booking_id, max(id) as id from fabric_booking_approval_cause where booking_id in ($booking_ids) and entry_form=12 and approval_type=2 group by booking_id";
		$idPreRes=sql_select($sqlidPre); $idpre="";
		foreach($idPreRes as $idrow)
		{
			if($idpre=="") $idpre=$idrow[csf('id')]; else $idpre.=','.$idrow[csf('id')];
		}
		unset($idPreRes);
		
		
		$flag=1;
		$query="delete from approval_mst  WHERE entry_form=12 and mst_id in ($booking_ids)";
		$rID=execute_query($query,1); 
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		}
		
		
		
		if($idpre!="")
		{
			$sqlHis="insert into approval_cause_refusing_his( id, cause_id, page_id, entry_form, user_id, booking_id, approval_type, approval_no, approval_history_id, approval_cause, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, not_approval_cause)
					select '', id, page_id, entry_form, user_id, booking_id, approval_type, approval_no, approval_history_id, approval_cause, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, not_approval_cause from fabric_booking_approval_cause where booking_id in ($booking_ids) and approval_type=2 and entry_form=12 and id in ($idpre)";
			// echo "22**".$sqlHis;oci_rollback($con); die;
			
			if(count($sqlHis)>0)
			{
				
				if($flag==1)
				{
					$rID1=execute_query($sqlHis,0);
					if($rID1==1) $flag=1; else $flag=0;
				}
			}
		}
		
		if($flag==1){
			$rID2=sql_multirow_update("wo_booking_mst","is_approved*ready_to_approved*APPROVED_SEQU_BY","0*0*0","id",$booking_ids,0);
			if($rID2) $flag=1; else $flag=0;
		}

		//$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,0);
		if($flag==1){
			$query="UPDATE approval_history SET current_approval_status=0,un_approved_by=$user_id_approval,un_approved_date='".$pc_date_time."',updated_by=$user_id,update_date='".$pc_date_time."' WHERE entry_form=12 and mst_id in ($booking_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;
		}

		
		if($flag==1){
			//$rID4=sql_multirow_update("fabric_sales_order_mst","is_apply_last_update","2","booking_id",$booking_ids,1);
			//if($rID4) $flag=1; else $flag=0;
		}
		$response=$booking_ids;
		
		// echo "22**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$rID4."**".$flag;oci_rollback($con);die;
		
		
		if($flag==1) $msg='20'; else $msg='22';
	}
	else if($approval_type==5)//Deny
	{
	 
		$booking_ids_all=explode(",",$booking_ids);
		
		$sqlBookinghistory="select id, mst_id from approval_history where current_approval_status=1 and entry_form=12 and mst_id in ($booking_ids) ";
		//echo "10**".$sqlBookinghistory;
		$nameArray=sql_select($sqlBookinghistory); $bookidstr=""; $approval_id="";
		foreach ($nameArray as $row)
		{
			if($bookidstr=="") $bookidstr=$row[csf('mst_id')]; else $bookidstr.=','.$row[csf('mst_id')];
			if($approval_id=="") $approval_id=$row[csf('id')]; else $approval_id.=','.$row[csf('id')];
		}
		
		$appBookNoId=implode(",",array_filter(array_unique(explode(",",$bookidstr))));
		$approval_ids=implode(",",array_filter(array_unique(explode(",",$approval_id))));
		
		$flag=1;
		if($flag==1)
		{
			$rID=sql_multirow_update("wo_booking_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY","0*0*0","id",$booking_ids,0);
			if($rID) $flag=1; else $flag=0;
		}
		
		if($approval_ids!="")
		{
			if($flag==1)
			{
				$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=12 and current_approval_status=1 and id in ($approval_ids)";
				$rID2=execute_query($query,1);
				if($rID2) $flag=1; else $flag=0;
			}
		}
		
		
		if($flag==1) 
		{
			$rID3=sql_multirow_update("fabric_sales_order_mst","is_apply_last_update","2","booking_id",$booking_ids,1);
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=12 and mst_id in ($booking_ids)";
			$rID4=execute_query($query,1); 
			if($rID4) $flag=1; else $flag=0; 
		}
		
		
		$response=$booking_ids;
		
		if($flag==1) $msg='50'; else $msg='51';
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
                    $sql="select image_location from common_photo_library where master_tble_id='$job_no' and form_name='knit_order_entry' and file_type=1";
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
                    $sql="select image_location from common_photo_library where master_tble_id='$job_no' and form_name='knit_order_entry' and file_type=2";
                    $result=sql_select($sql);
                    foreach($result as $row)
                    {
						$i++;
                    ?>
                    	<td width="100" align="center"><a href="../../<? echo $row[csf('image_location')]; ?>"><img width="89" height="97" src="../../file_upload/blank_file.png"><br>File-<? echo $i; ?></a></td>
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
		$sql_cause="select approval_cause from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=12 and user_id='$user_id' and booking_id='$wo_id' and approval_type='$app_type' and status_active=1 and is_deleted=0";				
		$nameArray_cause=sql_select($sql_cause);
		$app_cause='';
		foreach($nameArray_cause as $row)
		{
			$app_cause.=$row[csf("approval_cause")].",";
		}
		$app_cause=chop($app_cause,",");
	}
	
	//echo $app_cause;
	
	?>
    <script>
	
		// $( document ).ready(function() {
		// 	document.getElementById("appv_cause").value='<? echo $app_cause; ?>';
		// });
		
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
				http.open("POST","short_feb_booking_approval_controller_v2.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}
		
		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				// alert(http.responseText);//return;
				var reponse=trim(http.responseText).split('**');	
				show_msg(reponse[0]);
				
				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();
				fnc_close();
				
				//generate_worder_mail(reponse[2],reponse[3],reponse[4],reponse[5]);
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
			http.open("POST","short_feb_booking_approval_controller_v2.php",true);
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

if ($action=="appcause_popup_show_2")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$permission="1_1_1_1";//$_SESSION['page_permission'];
	
	$data_all=explode('_',$data);
	$wo_id=$data_all[0];
	$app_type=$data_all[1];
	$app_cause=$data_all[2];
	$approval_id=$data_all[3];
		
	$sql_cause="select approval_cause from fabric_booking_approval_cause where entry_form=12 and booking_id='$wo_id' and approval_type='$app_type' and status_active=1 and is_deleted=0";	
	//echo $sql_cause;		
	$nameArray_cause=sql_select($sql_cause);
	$app_cause='';
	foreach($nameArray_cause as $row)
	{
		$app_cause.=$row[csf("approval_cause")].",";
	}
	$app_cause=chop($app_cause,",");
	
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
				
				var data="action=save_update_delete_appv_cause_show_2&operation="+operation+get_submitted_data_string('appv_cause*wo_id*appv_type*page_id*user_id*approval_id',"../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","short_feb_booking_approval_controller_v2.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}
		
		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				// alert(http.responseText);//return;
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
			http.open("POST","short_feb_booking_approval_controller_v2.php",true);
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
        <?=load_freeze_divs ("../../",$permission,1); ?>
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
                            if(!empty($app_cause))
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }
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
            <?
				$sqlHis="select approval_cause from approval_cause_refusing_his where page_id='$menu_id' and entry_form=12 and booking_id='$wo_id' and approval_type='$app_type' and status_active=1 and is_deleted=0 order by id Desc";
				$sqlHisRes=sql_select($sqlHis);
			?>
            <table align="center" cellspacing="0" width="420" class="rpt_table" border="1" rules="all">
            	<thead>
                    <th width="30">SL</th>
                    <th>Refusing History</th>
                </thead>
            </table>
            <div style="width:420px; overflow-y:scroll; max-height:260px;" align="center">
            	<table align="center" cellspacing="0" width="403" class="rpt_table" border="1" rules="all">
                <?
				$i=1;
				foreach($sqlHisRes as $hrow)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');">
                        <td width="30"><?=$i; ?></td>
                        <td style="word-break:break-all"><?=$hrow[csf('approval_cause')]; ?></td>
                    </tr>
					<?
                    $i++;
				}
                ?>
                </table>
            </div>
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
	
	$sql_req="select approval_cause from fabric_booking_approval_cause where entry_form=12 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0";				
	$nameArray_req=sql_select($sql_req);
	$unappv_req='';
	foreach($nameArray_req as $row)
	{
		
		if(empty($unappv_req))
		{
			$unappv_req=$row[csf('approval_cause')];
		}
		else{
			$unappv_req.=",".$row[csf('approval_cause')];
		}
	}
	
	?>
    <script>
	
		var permission='<? echo $permission; ?>';
		
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
                    	<textarea name="unappv_req" id="unappv_req" readonly class="text_area" style="width:430px; height:100px;">
                    		
                    	</textarea>
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
        <?
			$sqlHis="select approval_cause from approval_cause_refusing_his where entry_form=12 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0 order by id Desc";
			$sqlHisRes=sql_select($sqlHis);
		?>
		<table align="center" cellspacing="0" width="420" class="rpt_table" border="1" rules="all">
			<thead>
				<th width="30">SL</th>
				<th>Unapproved Request History</th>
			</thead>
		</table>
		<div style="width:420px; overflow-y:scroll; max-height:260px;" align="center">
			<table align="center" cellspacing="0" width="403" class="rpt_table" border="1" rules="all">
			<?
			$i=1;
			foreach($sqlHisRes as $hrow)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');">
					<td width="30"><?=$i; ?></td>
					<td style="word-break:break-all"><?=$hrow[csf('approval_cause')]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
			</table>
		</div> 
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
    <script>
		var permission='<? echo $permission; ?>';
		
		 function fnc_remarks_entry(operation){
		
			var data="action=save_update_delete_remarks&operation="+operation+"&booking_id=<?= $booking_id;?>&app_user_id=<?= $app_user_id;?>"+get_submitted_data_string('txt_remarks*remarks_mst_id',"../");
	

			http.open("POST","short_feb_booking_approval_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = ()=>{
				if(http.readyState == 4) 
				{
					set_button_status(0, permission, 'fnc_remarks_entry',1);
					parent.emailwindow.hide();
					$('#remarks_mst_id').val('');
					$('#txt_remarks').val('');
				}
			};

		 }

		 function getFormData(remarks_id,remarks){
			$('#remarks_mst_id').val(remarks_id);
			$('#txt_remarks').val(remarks);
			set_button_status(1, permission, 'fnc_remarks_entry',1);
		 }
		
 
			
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1,false,0); ?>
        <form name="remarks_1" id="remarks_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="0" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center">
						<input type="hidden" id="remarks_mst_id" value="">
                    	<textarea name="txt_remarks" id="txt_remarks" class="text_area" style="width:430px; height:50px;"></textarea>
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="0" rules="all" id="" >
                <tr>
                    <td align="center">
						<?
                            echo load_submit_buttons($permission, "fnc_remarks_entry", 0,0,"reset_form('remarks_1','','','','','');",1);
						?>
                    
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>	
					
                </tr>
            </table>
            </fieldset>
            </form>

        </div>
        <?
			$sqlHis="select ID,CAUSE from BOOKING_CAUSE where entry_form=12 and CAUSE_ID='$booking_id' and INSERTED_BY='$app_user_id' and status_active=1 and is_deleted=0 order by id Desc";
			$sqlHisRes=sql_select($sqlHis);
		?>
		<table align="center" cellspacing="0" width="490" class="rpt_table" border="1" rules="all">
			<thead>
				<th width="30">SL</th>
				<th>Remarks List</th>
			</thead>
		</table>
		<div style="overflow-y:scroll; max-height:160px;" align="center">
			<table align="center" cellspacing="0" width="475" class="rpt_table" border="1" rules="all">
			<?
			$i=1;
			foreach($sqlHisRes as $hrow)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<?=$bgcolor; ?>" onClick="getFormData('<?= $hrow['ID']; ?>','<?=$hrow['CAUSE']; ?>');">
					<td width="30"><?=$i; ?></td>
					<td style="word-break:break-all"><?=$hrow['CAUSE']; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
			</table>
		</div> 
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


if ($action=="save_update_delete_remarks")
{

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$txt_remarks = str_replace("'","",$txt_remarks);
	$remarks_mst_id = str_replace("'","",$remarks_mst_id);

 

	if ($operation==0)  // Insert Here
	{
			$con = connect();

			$id=return_next_id( "id", "BOOKING_CAUSE", 1 ) ;
		
			$field_array="ID,CAUSE_ID,ENTRY_FORM,CAUSE,INSERTED_BY,INSERT_DATE,STATUS_ACTIVE,IS_DELETED";
			$data_array="(".$id.",".$booking_id.",12,'".$txt_remarks."',".$app_user_id.",'".$pc_date_time."',1,0)";
			//echo "insert into BOOKING_CAUSE ($field_array) values $data_array";die;
			$rID=sql_insert("BOOKING_CAUSE",$field_array,$data_array,0);

			if($rID )
			{
				oci_commit($con); 
				echo "0**".$id;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$id;
			}
		 
			disconnect($con);
			die;	
	}
	else if($operation==1){
		$con = connect();

		$field_array="CAUSE*UPDATED_BY*UPDATE_DATE";
		$data_array="'".$txt_remarks."'*".$app_user_id."*'".$pc_date_time."'";
				
		$rID=sql_update("BOOKING_CAUSE",$field_array,$data_array,"id","".$remarks_mst_id."",0);

		//echo $rID;die;

		if($rID )
		{
			oci_commit($con); 
			echo "0**".$remarks_mst_id;
		}
		else
		{
			oci_rollback($con); 
			echo "10**".$remarks_mst_id;
		}
		
		disconnect($con);
		die;	

	}

	


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
			
			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=12 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=12 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
			
			//echo "shajjad_".$approved_no_history.'_'.$approved_no_cause; die;
			
			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;
				
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",12,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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
				if($db_type==2 || $db_type==1 )
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
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=12 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
				
				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*12*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
				
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
				if($db_type==2 || $db_type==1 )
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
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=12 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=12 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
				
				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",12,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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
					if($db_type==2 || $db_type==1 )
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
					
					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=12 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
					
					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*12*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
					
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
					if($db_type==2 || $db_type==1 )
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
		
			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=12 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
			
			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=12 and mst_id=$wo_id and approved_by=$user_id");
			
			if($unapproved_cause_id=="")
			{
			
				//echo "shajjad_".$unapproved_cause_id; die;
		
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_history_id,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",12,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
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
			
				if($db_type==2 || $db_type==1 )
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
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=12 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
				
				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_history_id*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*12*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
				
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
				if($db_type==2 || $db_type==1 )
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

if ($action=="save_update_delete_appv_cause_show_2")
{
	//$approval_id
	
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$appv_cause=str_replace(array("&", "*", "(", ")", "=","'","_","\r", "\n",'"','#'), "", $appv_cause);
	$approval_type=str_replace("'","",$appv_type);
	$operation=str_replace("'","",$operation);
	
	//echo "10**select approval_cause from approval_cause_refusing_his where approval_cause='".str_replace("'", "", $appv_cause)."' and entry_form=12 and booking_id='".str_replace("'", "", $wo_id)."' and approval_type=1 and status_active=1 and is_deleted=0"; die;
	$flag=1;
	if(is_duplicate_field( "approval_cause", "approval_cause_refusing_his", "approval_cause='".str_replace("'", "", $appv_cause)."' and entry_form=12 and booking_id='".str_replace("'", "", $wo_id)."' and approval_type=1 and status_active=1 and is_deleted=0" )==1)
	{
		//
	}
	else
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$id_his=return_next_id( "id", "approval_cause_refusing_his", 1);
		$idpre=return_field_value("max(id) as id", "fabric_booking_approval_cause", "booking_id=".$wo_id." and entry_form=12 and approval_type=1 group by booking_id","id");
		$sqlHis="insert into approval_cause_refusing_his( id, cause_id, page_id, entry_form, user_id, booking_id, approval_type, approval_no, approval_history_id, approval_cause, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, not_approval_cause)
			select '', id, page_id, entry_form, user_id, booking_id, approval_type, approval_no, approval_history_id, approval_cause, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, not_approval_cause from fabric_booking_approval_cause where booking_id=".$wo_id." and approval_type=1 and entry_form=12 and status_active=1 and is_deleted=0 and id=$idpre";
		
		if(count($sqlHis)>0)
		{
			$rID3=execute_query($sqlHis,0);
			if($flag==1)
			{
				if($rID3==1) $flag=1; else $flag=0;
			}
		}
	}
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
		$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=12 and mst_id=$wo_id");
		
		$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
		$data_array="(".$id_mst.",".$page_id.",12,".$user_id.",".$wo_id." ,".$appv_type.",'".$max_appv_no_his."','".$appv_cause."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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
		if($db_type==2 || $db_type==1 )
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
	else if ($operation==1)  // Update Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=12 and mst_id=$wo_id ");

		$idpre=return_field_value("max(id) as id", "fabric_booking_approval_cause", "booking_id=".$wo_id." and entry_form=12 and approval_type=1 group by booking_id","id");
		//$sql= "Select id from fabric_booking_approval_cause where user_id=".$user_id." and page_id=".$page_id." and booking_id=".$wo_id." and approval_type=".$appv_type."  and entry_form=12 and approval_no='$max_appv_no_his'";
		
		$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*inserted_by*insert_date*status_active*is_deleted";

		$data_array=$page_id."*12*".$user_id."*".$wo_id."*".$appv_type."*'".$max_appv_no_his."'*'".$appv_cause."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
		
		//echo "10**".$data_array.'='.$id;	 die;	
		
		$rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id",$idpre,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo $rID; die;
		
		if($db_type==0)
		{
			if($flag==1)
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
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$user_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$rID."**".$sql;
			}
		}
		disconnect($con);
		die;	
	}
}

if ($action=="app_cause_mail")
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
		
		$header=mail_header();
		
		if (mail($to,$subject,$message,$header))
			echo "****Mail Sent.---".date("Y-m-d");
		else
			echo "****Mail Not Sent.---".date("Y-m-d");
		
		//echo "222**".$woid;
		exit();
		
}

if($action=="check_booking_last_update")
{
	$last_update=return_field_value("is_apply_last_update","wo_booking_mst","booking_no='".trim($data)."'");
	echo $last_update;
	exit();	
}
if($action=="check_sales_order_approved")
{
	$last_update=return_field_value("is_approved","fabric_sales_order_mst","sales_booking_no='".trim($data)."'");
	echo $last_update;
	exit();	
}

if($action=="show_fabric_booking_report3")
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$revised_no=str_replace("'","",$revised_no);
	$report_type=str_replace("'","",$report_type);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id=$cbo_company_name",'master_tble_id','image_location');
	
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	//$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name");

	$po_number_arr=return_library_array( "select id,po_number from   wo_po_break_down",'id','po_number');
	$po_ship_date_arr=return_library_array( "select id,pub_shipment_date from   wo_po_break_down ",'id','pub_shipment_date');
	?>
	<div style="width:1330px" align="center">
		    <?php
		/*$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=12");
		list($nameArray_approved_row) = $nameArray_approved;*/
		$nameArray_approved_row[csf('approved_no')]=$revised_no;
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=12 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_date_row) = $nameArray_approved_date;
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=12 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_comments_row) = $nameArray_approved_comments;
		$path = str_replace("'", "", $path);
		if ($path == "") {
			$path = '../../';
		}
		?>										<!--    Header Company Information         -->
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black" >
           <tr>
               <td width="100">
               <?
               if($report_type==1)
			   {
			   ?>
               <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			   else if($report_type==1)
			   {
			   ?>
               <img  src='../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			   else
			   {
			   ?>
               <img  src='<? echo $path.$imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               <?
			   }
			   $path = str_replace("'", "", $path);
				if ($path != "") {
					$path = $path;
				} else {
					$path = "../../";
				}
			  
			   ?>
               </td>
               <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php
								echo $company_library[$cbo_company_name];
								?>
                            </td>
                            <td rowspan="3"  width="270">
                               <span style="font-size:18px"><b> Job No:&nbsp;&nbsp;<? echo trim($txt_job_no,"'"); ?></b></span>

                               <br>
                               <?
								 if($nameArray_approved_row[csf('approved_no')]>1)
								 {
								 ?>
								 <b> Revised No: <?=$nameArray_approved_row[csf('approved_no')]-1; ?></b>
                                  <br/>
								 <b> Approved Date: <?=$nameArray_approved_date_row[csf('approved_date')]; ?>
								  <?
								 }
							  	?>
                            	</b>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px; word-break:break-all">
                            <?
                            $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
                            foreach ($nameArray as $result)
                            {
                            ?>
                                Plot No: <? echo $result[csf('plot_no')]; ?>
                                Level No: <? echo $result[csf('level_no')]?>
                                Road No: <? echo $result[csf('road_no')]; ?>
                                Block No: <? echo $result[csf('block_no')];?>
                                City No: <? echo $result[csf('city')];?>
                                Zip Code: <? echo $result[csf('zip_code')]; ?>
                                Province No: <?php echo $result[csf('province')]; ?>
                                Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
                                Email Address: <? echo $result[csf('email')];?>
                                Website No: <? echo $result[csf('website')];
                            }
                            ?>
                               </td>
                            </tr>
                            <tr>
                            <td align="center" style="font-size:20px">
                                <strong><? if($report_type==1) echo str_replace("'","",$report_title);else echo 'Short Fabric Booking';?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><? if(str_replace("'","",$id_approved_id) ==1){?><font style="color:green"><? echo "(Approved)";?></font><?}else{?><font style="color:#F00"><? echo "(Not Approved)";?></font><?}; ?> </strong>
                             </td>
                              <td>

                             </td>
                            </tr>
                      </table>
                </td>
            </tr>
       </table>
                <?

				$job_no='';
				$total_set_qnty=0;
				$colar_excess_percent=0;
				$cuff_excess_percent=0;
                $nameArray=sql_select( "SELECT a.booking_no,a.booking_date,a.supplier_id,a.pay_mode,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.delivery_date,a.is_apply_last_update,a.fabric_source,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant, b.factory_marchant from wo_booking_mst_hstry a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no and a.approved_no=$revised_no");


				foreach ($nameArray as $result)
				{
					$total_set_qnty=$result[csf('total_set_qnty')];
					$colar_excess_percent=$result[csf('colar_excess_percent')];
				    $cuff_excess_percent=$result[csf('cuff_excess_percent')];
					$po_no="";
					$shipment_date="";$internal_ref="";	$file_no="";
					$sql_po= "select po_number,grouping,file_no,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,grouping,file_no";
					$data_array_po=sql_select($sql_po);
					foreach ($data_array_po as $row_po)
					{
						$po_no.=$row_po[csf('po_number')].", ";
						$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
						$internal_ref.=$row_po[csf('grouping')].", ";
						$file_no.=$row_po[csf('file_no')].", ";
					}

					$lead_time="";
					if($db_type==0)
					{
					$sql_lead_time= "select DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					}

					if($db_type==2)
					{
					$sql_lead_time= "select (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					}
					$data_array_lead_time=sql_select($sql_lead_time);
					foreach ($data_array_lead_time as $row_lead_time)
					{
						$lead_time.=$row_lead_time[csf('date_diff')].",";
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}

					$po_received_date="";
					$sql_po_received_date= "select MIN(po_received_date) as po_received_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
					$data_array_po_received_date=sql_select($sql_po_received_date);
					foreach ($data_array_po_received_date as $row_po_received_date)
					{
						$po_received_date=change_date_format($row_po_received_date[csf('po_received_date')],'dd-mm-yyyy','-');
						//$shipment_date.=change_date_format($row_po['pub_shipment_date'],'dd-mm-yyyy','-').",";
					}
					$file=rtrim($file_no,", ");//rtrim($po_no,", ")
					$file_all=array_unique(explode(",",$file));

					$file='';
					foreach($file_all as $file_id)
					{
						if($file=="") $file_cond=$file_id; else $file_cond.=", ".$file_id;
					}

				?>
       <table width="100%" style="border:1px solid black" >
            <tr>
                <td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
            </tr>
            <tr>
                <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
                <td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                <td width="110" style="font-size:18px"><b>:&nbsp;
				<?  echo $po_qnty_tot1." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?></b>
                </td>
            </tr>
            <tr>

                <td width="100" style="font-size:12px"><b>Garments Item</b></td>
                <td width="110">:&nbsp;
				<?
				$gmts_item_name="";
				$gmts_item=explode(',',$result[csf('gmts_item_id')]);
				for($g=0;$g<=count($gmts_item); $g++)
				{
					$gmts_item_name.= $garments_item[$gmts_item[$g]].",";
				}
				echo rtrim($gmts_item_name,',');
				?>
                </td>
                <td width="100" style="font-size:12px"><b>Booking Date</b></td>
                <td width="110">:&nbsp;<? echo change_date_format($result[csf('booking_date')],'dd-mm-yyyy','-');?>&nbsp;&nbsp;&nbsp;</td>
                <td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $style_sting=$result[csf('style_ref_no')];?> </b>   </td>

            </tr>
             <tr>
                <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                <td width="110">:&nbsp;<?=implode(",",array_filter(array_unique(explode(",",$lead_time))));?> </td>
                <td width="100" style="font-size:12px"><b>Dealing Merchandiser</b></td>
                <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                <td width="110">:&nbsp;<?
				if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
				//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                <td width="100" style="font-size:12px"><b>Delivery Date</b></td>
               	<td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                <td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo "(".$fabric_source[$result[csf('fabric_source')]].")"?></td>
            </tr>
            <tr>
                <td width="100" style="font-size:12px"><b>Season</b></td>
                <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Attention</b></td>
                <td  width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                <td  width="100" style="font-size:12px"><b>Po Received Date</b></td>
                <td  width="110" >:&nbsp;<? echo $po_received_date; ?></td>
            </tr>
           <tr>
               <td width="100" style="font-size:18px"><b>Order No</b></td>
                <td width="110" style="font-size:18px">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>

                <td width="100" style="font-size:17px"><b>Factory Marchant</b></td>
                <td width="100" style="font-size:17px"><? echo $marchentrArr[$result[csf('factory_marchant')]] ?></td>
                <td width="100" style="font-size:17px"><b>Internal Ref</b></td>
                <td width="100" style="font-size:16px">:&nbsp;<b><?=implode(",",array_filter(array_unique(explode(",",$internal_ref)))); ?></b></td>

            </tr>
            <tr>
               <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
               <td width="110" style="word-break:break-all"> :&nbsp;<?=implode(",",array_filter(array_unique(explode(",",$shipment_date)))); ?></td>
               <td width="100" style="font-size:12px"><b>Factory Merchandiser</b></td>
               <td width="110">:&nbsp;<?=$marchentrArr[$result[csf('factory_marchant')]]; ?></td>
               <td width="100" style="font-size:17px"><b>File No</b></td>
               <td width="100" style="font-size:16px"> :&nbsp;<b><? echo $file_cond; ?></b></td>
            </tr>
        </table>
           <?
			}
		   ?>
          <br/>     									 <!--  Here will be the main portion  -->

     <?
	 $costing_per="";
	 $costing_per_qnty=0;
	 $costing_per_id=return_field_value( "costing_per", "wo_pre_cost_mst","job_no ='$job_no'");
	 if($costing_per_id==1)
			{
				$costing_per="1 Dzn";
				$costing_per_qnty=12;

			}
			if($costing_per_id==2)
			{
				$costing_per="1 Pcs";
				$costing_per_qnty=1;

			}
			if($costing_per_id==3)
			{
				$costing_per="2 Dzn";
				$costing_per_qnty=24;

			}
			if($costing_per_id==4)
			{
				$costing_per="3 Dzn";
				$costing_per_qnty=36;

			}
			if($costing_per_id==5)
			{
				$costing_per="4 Dzn";
				$costing_per_qnty=48;

			}
			$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");;

	 ?>

     <?
	 if(str_replace("'","",$cbo_fabric_source)==1)
	  {
	$nameArray_fabric_description= sql_select("SELECT  a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width, avg(b.process_loss_percent) as process_loss_percent,avg(rmg_qty) as rmg_qty FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls_hstry b where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and b.is_deleted=0 and b.approved_no=$revised_no group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,dia_width");



	 ?>

     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <th colspan="5" align="left">Body Part</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
			else         		               echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
		}
		?>
        <td  rowspan="9" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Total Grey Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Process Loss % </p></td>
       </tr>
     <tr align="center"><th colspan="5" align="left">Color Type</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
			else         		               echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
		}
		?>
        <!--<td  rowspan="8" width="50"><p>Total  Finish Fabric (KG)</p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric (KG)</p></td>-->
             <!--<td  rowspan="7" width="50"><p>Process Loss % </p></td>-->
       </tr>
        <tr align="center"><th colspan="5" align="left">Fabric Construction</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";
			else         		               echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";
		}
		?>


       </tr>
        <tr align="center"><th   colspan="5" align="left">Fabric Composition</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
			else         		               echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th  colspan="5" align="left">GSM</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		       echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="5" align="left">Dia/Width (Inch)</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>".$result_fabric_description[csf('dia_width')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="5" align="left">RMG Qty</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('rmg_qty')] == "")   echo "<td colspan='2'>&nbsp</td>";
			else         		              echo "<td colspan='2' align='center'>". $result_fabric_description[csf('rmg_qty')]."</td>";
		}
		?>

       </tr>
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*2+5; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>

       <tr>
            <th  width="120" align="left">PO Number</th>
            <th  width="120" align="left">Ship Date</th>
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left">Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Finish</th><th width='50' >Gray</th>";
		}
		?>

       </tr>
       <?
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select gmts_color_id,fabric_color_id
		  FROM
		  wo_booking_dtls
		  WHERE
		  booking_no =$txt_booking_no");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("fabric_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id ,po_break_down_id,gmts_color_id
										  FROM
										  wo_booking_dtls_hstry
										  WHERE
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
                                          and approved_no=$revised_no
										  group by po_break_down_id,gmts_color_id,fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?>
			<tr>
          <td  width="120" align="left">
			<?
			echo $po_number_arr[$color_wise_wo_result[csf('po_break_down_id')]];
			?></td>
             <td  width="120" align="left">
			<?

			echo change_date_format($po_ship_date_arr[$color_wise_wo_result[csf('po_break_down_id')]],"dd-mm-yyyy","-");

			?></td>
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];


			?>
            </td>
            <td>
            <?
			echo $color_library[$color_wise_wo_result[csf("gmts_color_id")]];//rtrim($gmt_color_library[$color_wise_wo_result['fabric_color_id']],",");
			?>
            </td>
            <td  width="120" align="left">
			<?
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			//echo "lapdip_no from wo_po_lapdip_approval_info where job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result['fabric_color_id']."";
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;

			foreach($nameArray_fabric_description as $result_fabric_description)
		    {

				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls_hstry b
												  WHERE
												  b.booking_no =$txt_booking_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												  a.construction='".$result_fabric_description[csf('construction')]."' and
												  a.composition='".$result_fabric_description[csf('composition')]."' and
												  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												  b.gmts_color_id=".$color_wise_wo_result[csf('gmts_color_id')]." and
												  b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
												  b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]." and
												  b.approved_no=".$revised_no." and
												  b.status_active=1 and
												  b.is_deleted=0");
				}
				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls_hstry b
												  WHERE
												  b.booking_no =$txt_booking_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
												  nvl( a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
												  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
												  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
												  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
												  nvl( b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
												  nvl(b.gmts_color_id,0)=nvl(".$color_wise_wo_result[csf('gmts_color_id')].",0) and
												  nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
												  b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]." and
												  b.approved_no=".$revised_no." and
												  b.status_active=1 and
												  b.is_deleted=0");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'>
			<?
			if($color_wise_wo_result_qnty[csf('fin_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' >
			<?
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);
			$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>

            <td align="right">
            <?
			if($process_loss_method==1)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_fin_fab_qnty)*100;
			}

			if($process_loss_method==2)
			{
				$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
			}
			echo number_format($process_percent,2);

			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {

				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls_hstry b
												  WHERE
												  b.booking_no =$txt_booking_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												  a.construction='".$result_fabric_description[csf('construction')]."' and
												  a.composition='".$result_fabric_description[csf('composition')]."' and
												  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												  b.status_active=1 and
												  b.approved_no=".$revised_no." and
												  b.is_deleted=0");
				}

				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls_hstry b
												  WHERE
												  b.booking_no =$txt_booking_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  nvl( a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
												  nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
												  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
												  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
												  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
												  nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
												  b.status_active=1 and
												  b.approved_no=".$revised_no." and
												  b.is_deleted=0");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td><td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
            <td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
            <td align="right">
            <?
            if($process_loss_method==1)// markup
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_fin_fab_qnty)*100;
			}

			if($process_loss_method==2) //margin
			{
				$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_grey_fab_qnty)*100;
			}
			echo number_format($totalprocess_percent,2);
			?>
            </td>
            </tr>

    </table>

        <br/>
        <?
	  }
		?>



        <?
	 if(str_replace("'","",$cbo_fabric_source)==2)
	  {
	$nameArray_fabric_description= sql_select("SELECT  a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width, avg(b.process_loss_percent) as process_loss_percent,avg(rmg_qty) as rmg_qty FROM wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls_hstry b where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and
  b.is_deleted=0 and b.approved_no=$revised_no group by a.body_part_id,a.color_type_id,a.construction,a.composition,a.gsm_weight,b.dia_width order by a.body_part_id,dia_width");


	 ?>

     <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
     <tr align="center">
     <th colspan="5" align="left">Body Part</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
		}
		?>
        <td  rowspan="9" width="50"><p>Total   Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Rate <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
        <td  rowspan="9" width="50"><p>Amount</p></td>
       </tr>
     <tr align="center"><th colspan="5" align="left">Color Type</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
		}
		?>
        <!--<td  rowspan="8" width="50"><p>Total  Finish Fabric (KG)</p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric (KG)</p></td>-->
             <!--<td  rowspan="7" width="50"><p>Process Loss % </p></td>-->
       </tr>
        <tr align="center"><th colspan="5" align="left">Fabric Construction</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
			else         		               echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
		}
		?>


       </tr>
        <tr align="center"><th   colspan="5" align="left">Fabric Composition</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
			else         		               echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th  colspan="5" align="left">GSM</th>
        <?
		foreach($nameArray_fabric_description  as $result_fabric_description)
		{
			if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		       echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="5" align="left">Dia/Width (Inch)</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
			else         		              echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')]."</td>";
		}
		?>

       </tr>
       <tr align="center"><th   colspan="5" align="left">RMG Qty</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			if( $result_fabric_description[csf('rmg_qty')] == "")   echo "<td colspan='3'>&nbsp</td>";

			else         		              echo "<td colspan='3' align='center'>". $result_fabric_description[csf('rmg_qty')]."</td>";
		}
		?>

       </tr>
       <tr>
       <th  colspan="<? echo  count($nameArray_fabric_description)*3+5; ?>" align="left" style="height:30px">&nbsp;</th>
       </tr>

       <tr>
            <th  width="120" align="left">PO Number</th>
            <th  width="120" align="left">Ship Date</th>
            <th  width="120" align="left">Fabric Color</th>
            <th  width="120" align="left">Body Color</th>
            <th  width="120" align="left">Lab Dip No</th>
        <?
		foreach($nameArray_fabric_description as $result_fabric_description)
		{
			  echo "<th width='50'>Fab Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
		}
		?>

       </tr>
       <?
		  $gmt_color_library=array();
		  $gmt_color_data=sql_select("select gmts_color_id,fabric_color_id
		  FROM
		  wo_booking_dtls
		  WHERE
		  booking_no =$txt_booking_no");
		  foreach( $gmt_color_data as $gmt_color_row)
		  {
			$gmt_color_library[$gmt_color_row[csf("fabric_color_id")]].=$color_library[$gmt_color_row[csf("gmts_color_id")]]."," ;
		  }

	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			$color_wise_wo_sql=sql_select("select fabric_color_id ,po_break_down_id
										  FROM
										  wo_booking_dtls_hstry
										  WHERE
										  booking_no =$txt_booking_no and
										  status_active=1 and
                                          is_deleted=0
                                          and approved_no=$revised_no
										  group by po_break_down_id,fabric_color_id");
		foreach($color_wise_wo_sql as $color_wise_wo_result)
	    {
		?>
			<tr>
          <td  width="120" align="left">
			<?
			echo $po_number_arr[$color_wise_wo_result[csf('po_break_down_id')]];
			?></td>
             <td  width="120" align="left">
			<?

			echo change_date_format($po_ship_date_arr[$color_wise_wo_result[csf('po_break_down_id')]],"dd-mm-yyyy","-");

			?></td>
            <td  width="120" align="left">
			<?
			echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];


			?>
            </td>
            <td>
            <?
			echo rtrim($gmt_color_library[$color_wise_wo_result['fabric_color_id']],",");
			?>
            </td>
            <td  width="120" align="left">
			<?
			$lapdip_no="";
			$lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
			//echo "lapdip_no from wo_po_lapdip_approval_info where job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result['fabric_color_id']."";
			if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
			?>
            </td>
            <?
			$total_fin_fab_qnty=0;
			$total_grey_fab_qnty=0;
			$total_amount=0;

			foreach($nameArray_fabric_description as $result_fabric_description)
		    {
				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty,avg(b.rate) as rate
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls_hstry b
												  WHERE
												  b.booking_no =$txt_booking_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												  a.construction='".$result_fabric_description[csf('construction')]."' and
												  a.composition='".$result_fabric_description[csf('composition')]."' and
												  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												  b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
												  b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]." and
												  b.approved_no=".$revised_no." and
												  b.status_active=1 and
												  b.is_deleted=0");
				}
				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty,avg(b.rate) as rate
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls_hstry b
												  WHERE
												  b.booking_no =$txt_booking_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
												  nvl( a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
												  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
												  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
												  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
												  nvl( b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
												  nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
												  b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]." and
												  b.approved_no=".$revised_no." and
												  b.status_active=1 and
												  b.is_deleted=0");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'>
			<?
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2) ;
			$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' >
			<?
			if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
			{
			echo number_format($color_wise_wo_result_qnty[csf('rate')],2);
			//$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			}
			?>
            </td>
            <td width='50' align='right' >
            <?
		    $amount=$color_wise_wo_result_qnty[csf('grey_fab_qnty')]*$color_wise_wo_result_qnty[csf('rate')];
			if($amount!="")
			{
			echo number_format($amount,2);
			$total_amount+=$amount;
			}
			?>
            </td>
            <?
			}
			?>
            <td align="right"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
            <td align="right"><? echo number_format($total_amount/$total_fin_fab_qnty,2); $grand_total_amount+=$total_amount;?></td>

            <td align="right">
            <?
			echo number_format($total_amount,2);
			?>
            </td>
            </tr>
         <?
		}
		?>
        <tr style=" font-weight:bold">
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left">&nbsp;</td>
        <th  width="120" align="left">&nbsp;</th>
        <td  width="120" align="left">&nbsp;</td>
        <td  width="120" align="left"><strong>Total</strong></td>
        <?
			foreach($nameArray_fabric_description as $result_fabric_description)
		    {

				if($db_type==0)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty,avg(b.rate) as rate
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls_hstry b
												  WHERE
												  b.booking_no =$txt_booking_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												  a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
												  a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
												  a.construction='".$result_fabric_description[csf('construction')]."' and
												  a.composition='".$result_fabric_description[csf('composition')]."' and
												  a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
												  b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
												  b.status_active=1 and
												  b.approved_no=$revised_no and 
												  b.is_deleted=0");
				}

				if($db_type==2)
				{
				$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty,avg(b.rate) as rate
												  FROM
												  wo_pre_cost_fabric_cost_dtls a,
												  wo_booking_dtls_hstry b
												  WHERE
												  b.booking_no =$txt_booking_no  and
												  a.id=b.pre_cost_fabric_cost_dtls_id and
												 nvl( a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
												  nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
												  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
												  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
												  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
												  nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
												  b.status_active=1 and
												   b.approved_no=$revised_no and 
												  b.is_deleted=0");
				}
				list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
			?>
			<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('fin_fab_qnty')],2) ;?></td>
            <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
            <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
            <?
			}
			?>
            <td align="right"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
            <td align="right"><? echo number_format($grand_total_amount/$grand_total_fin_fab_qnty,2);?></td>
            <td align="right">
            <?
			echo number_format($grand_total_amount,2);
			?>
            </td>
            </tr>

    </table>

        <br/>
        <?
	  }
		?>




        <?
		if(str_replace("'","",$cbo_fabric_source)==1)
	    {
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		//$yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		/*$condition= new condition();
		if(str_replace("'","",$job_no) !=''){
			$condition->job_no("='$job_no'");
		}
		$condition->init();
		$yarn= new yarn($condition);
		$yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();*/

		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		
		if($db_type==2 || $db_type==1)
		{
			
			  $yarn_sql="SELECT MIN (a.id) AS id,
		         a.count_id,
		         a.copm_one_id,
		         a.percent_one,
		         a.copm_two_id,
		        a.percent_two,
		        a.type_id,
		         SUM (a.cons_qnty) AS yarn_required,
		         AVG (a.rate) AS rate,
		         listagg (CAST (a.fabric_cost_dtls_id AS VARCHAR2 (4000)), ',')
		            WITHIN GROUP (ORDER BY   a.fabric_cost_dtls_id)
		            AS cost_dtls,
		         AVG (a.cons_ratio) AS cons_ratio,
				 sum (b.grey_fab_qnty*a.cons_ratio/100) AS grey_req
		         
				FROM wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls_hstry b
				WHERE a.job_no = '$job_no' AND a.status_active = 1
				    and a.is_deleted = 0
				   and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id 
				   and a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0
				   and b.booking_no=$txt_booking_no
				   and b.approved_no=$revised_no
				   --and a.count_id=b.yarn_count
				GROUP BY a.count_id,
				         a.copm_one_id,
				        a.percent_one,
				         a.copm_two_id,
				         a.percent_two,
				         a.type_id";
  
		}
		else
		{
		
			$yarn_sql="SELECT MIN (a.id) AS id,
			         a.count_id,
			         a.copm_one_id,
			         a.percent_one,
			         a.copm_two_id,
			        a.percent_two,
			        a.type_id,
			         SUM (a.cons_qnty) AS yarn_required,
			         AVG (a.rate) AS rate,
			         group_concat(fabric_cost_dtls_id) as cost_dtls,
			         sum (b.grey_fab_qnty*a.cons_ratio/100) AS grey_req
			         
					FROM wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls_hstry b
					WHERE a.job_no = '$job_no' AND a.status_active = 1
					    and a.is_deleted = 0
					   and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id 
					   and a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0
					   and b.booking_no=$txt_booking_no
					   and b.approved_no=$revised_no
					   --and a.count_id=b.yarn_count
					GROUP BY a.count_id,
					         a.copm_one_id,
					        a.percent_one,
					         a.copm_two_id,
					         a.percent_two,
					         a.type_id";
			
		}
		//echo $yarn_sql;
		$yarn_sql_array=sql_select($yarn_sql);


		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    	<td colspan="5"><b>Yarn Required Summary (Pre Cost) </b></td>
                    </tr>
                    <tr align="center">
                        <td>Sl</td>
                        <td>Yarn Description</td>
                        <?
                        if($show_yarn_rate==1)
                        {
                        ?>
                        <td>Rate</td>
                        <?
                        }
                        ?>
                        <td>Cons for <? echo $costing_per; ?> Gmts</td>
                        <td>Total (KG)</td>
                    </tr>
                    <?
					$i=0;
					$total_yarn=0;//$fab_chk_arr=array();
					foreach($yarn_sql_array  as $row)
                    {
						$i++;
						$fabric_cost_id=$row[csf('cost_dtls')];
						$yarn_id=$row[csf('id')];
						 
						//echo $grey_fab_qnty.'='.$fabric_cost_ratio.'D';
						//$tot_booking_grey=sql_select("select sum(grey_fab_qnty) as grey_qty  from wo_booking_dtls where status_active=1 and is_deleted=0 and booking_type=1 and is_short=1 and job_no='$job_no' and booking_no=$txt_booking_no and pre_cost_fabric_cost_dtls_id in($fabric_cost_id)");

						//$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
						?>
						<tr align="center">
                            <td><? echo $i; ?></td>
                            <td>
                            <?
                            $yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
                            if($row['copm_two_id'] !=0)
                            {
                                $yarn_des.=$composition[$row[csf('copm_two_id')]]." ".$row[csf('percent_two')]."%";
                            }
                            $yarn_des.=$color_library[$row[csf('color')]]." ";
                            $yarn_des.=$yarn_type[$row[csf('type_id')]];
                            //echo $yarn_count_arr[$row['count_id']]." ".$composition[$row['copm_one_id']]." ".$row['percent_one']."%  ".$composition[$row['copm_two_id']]." ".$row['percent_two']."%  ".$yarn_type[$row['type_id']];
                            echo $yarn_des;
                            ?>
                            </td>
                            <?
                            if($show_yarn_rate==1)
                            {
                            ?>
                             <td><? echo number_format($row[csf('rate')],4); ?></td>
                             <?
                            }
                             ?>
                            <td><? echo number_format($row[csf('yarn_required')],4); ?></td>

                            <!--<td><? //echo number_format(($row['yarn_required']/$po_qnty_tot)*$costing_per_qnty,2); ?></td>-->
                          <!--   <td align="right"><? //echo number_format($rowcons_qnty,2); $total_yarn+=$rowcons_qnty; ?></td> -->
                          <td align="right" title="Grey Fab Qty(<? //echo $tot_booking_grey[0][csf("grey_qty")];?>)*Avg Pre Yarn Ratio(<? //echo $row[csf("cons_ratio")];?>)/100"><? $total_kg=$row[csf('grey_req')];//$tot_booking_grey[0][csf("grey_qty")] * ($row[csf("cons_ratio")]/100); 
						  echo number_format($total_kg,2); ?> </td>
						</tr>
						<?
						$total_yarn += $total_kg;
						$tot_qty+=$tot_booking_grey[0][csf("grey_qty")];
					}
					?>
                    <tr align="center">
                        <td></td>
                        <td></td>
                        <?
                        if($show_yarn_rate==1)
                        {
                        ?>
                        <td></td>
                        <?
                        }
                        ?>
                        <td align="right">Total : </td>
                        <td align="right" title="<? //echo $tot_qty;?>"><? echo number_format($total_yarn,2); ?></td>
                    </tr>
                    </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <?
				$yarn_sql_array=sql_select("SELECT min(a.id) as id, a.item_id, sum(a.qnty) as qnty ,min(b.supplier_id) as supplier_id,min(b.lot) as lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0 group by a.item_id order by a.id");
				if(count($yarn_sql_array)>0)
				{
				?>
                   <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Allocated Yarn</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>


                    <td>Allocated Qty (Kg)</td>
                    </tr>
                    <?
					$total_allo=0;
					$item=return_library_array( "select id, product_name_details from   product_details_master",'id','product_name_details');
					$supplier=return_library_array( "select id, short_name from   lib_supplier",'id','short_name');
					//$yarn_sql_array=sql_select("SELECT a.item_id, a.qnty,b.supplier_id,b.lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0");
					$i=0;
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?

					echo $item[$row[csf('item_id')]];
					?>
                    </td>
                    <td>
                    <?

					echo $supplier[$row[csf('supplier_id')]];
					?>
                    </td>
                    <td>
					<?

					echo $row[csf('lot')];
					?>
                    </td>
                    <td align="right"><? echo number_format($row[csf('qnty')],4); $total_allo+= $row[csf('qnty')];?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>


                    <td></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_allo,4); ?></td>
                    </tr>
                    </table>
                    <?
				}
				else
				{
					$is_yarn_allocated=return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_name and variable_list=18 and item_category_id=1");
					if($is_yarn_allocated==1)
					{
					?>
					<font style=" font-size:30px"><b> Draft</b></font>
                    <?
					}
					else
					{
						echo "";
					}
				}
					?>
                </td>
            </tr>
        </table>
        <br/>
        <?
		}
		?>
        <?
		$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                <?
				if(count($sql_embelishment)>0)
				{
				?>
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Embelishment (Pre Cost)</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Embelishment Name</td>
                    <td>Embelishment Type</td>
                    <td>Cons <? echo $costing_per; ?> Gmts</td>
                    <td>Rate</td>
                    <td>Amount</td>

                    </tr>
                    <?
					$sql_embelishment=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls where job_no='$job_no' and status_active=1 and 	is_deleted=0");
					$i=0;
					//$total_yarn=0;
					foreach($sql_embelishment  as $row_embelishment)
                    {

						$i++;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					echo $emblishment_name_array[$row_embelishment[csf('emb_name')]];
					?>
                    </td>
                    <td>
                    <?
					if($row_embelishment[csf('emb_name')]==1)
					{
					echo $emblishment_print_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==2)
					{
					echo $emblishment_embroy_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==3)
					{
					echo $emblishment_wash_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==4)
					{
					echo $emblishment_spwork_type[$row_embelishment[csf('emb_type')]];
					}
					if($row_embelishment[csf('emb_name')]==5)
					{
					echo $row_embelishment[csf('emb_type')];
					}
					?>

                    </td>
                    <td>
                    <?
					echo $row_embelishment[csf('cons_dzn_gmts')];
					?>
                    </td>
                    <td>
					<?
					echo $row_embelishment[csf('rate')];
					?>
                    </td>

                    <td>
					<?
					echo $row_embelishment[csf('amount')];
					?>
                    </td>


                    </tr>
                    <?
					}
					?>
                    </table>
                    <?
				}
					?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top" align="center">
                <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td><b>Approved Instructions</b></td>

                    </tr>
                    <tr>
                    <td>
                <?  echo $nameArray_approved_comments_row[csf('comments')];  ?>
                </td>
                </tr>
                </table>

                </td>
            </tr>
        </table>
        <br/>

         <br>
        <?
     $desg_name=return_library_array( "select id, custom_designation from lib_designation", "id", "custom_designation"  );
	 $data_array=sql_select("select b.approved_by,b.approved_no, b.approved_date, c.user_full_name,c.designation from  wo_booking_mst a , approval_history b, user_passwd c where a.id=b.mst_id and b.approved_by=c.id and a.booking_no=$txt_booking_no and b.entry_form=12 order by b.id asc");
	?>
       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="3" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th><th width="50%" style="border:1px solid black;">Name/Designation</th><th width="27%" style="border:1px solid black;">Approval Date</th><th width="20%" style="border:1px solid black;">Approval No</th>
                </tr>
            </thead>
            <tbody>
            <?
			$s=1;
			foreach($data_array as $row){
			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $s;?></td><td width="50%" style="border:1px solid black;"><? echo $row[csf('user_full_name')].'/'.$desg_name[$row[csf('designation')]];?></td><td width="27%" style="border:1px solid black;"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); //echo change_date_format($row[csf('approved_date')],"dd-mm-yyyy","-");?></td><td width="20%" style="border:1px solid black;"><? echo $row[csf('approved_no')];?></td>
                </tr>
                <?
				$s++;
			}
				?>
            </tbody>
        </table>
        </br>


        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <?php echo get_spacial_instruction($txt_booking_no); ?>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top">
                <?
				if(str_replace("'","",$cbo_fabric_source)==1 || str_replace("'","",$cbo_fabric_source)==2)
				{
				?>
                   <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                   <tr align="center">
                    <td colspan="10"><b>Comments</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Po NO</td>
                    <td>Ship Date</td>
                    <td>Pre-Cost Qty</td>
                    <td>Mn.Book Qty</td>
                    <td>Sht.Book Qty</td>
                    <td>Smp.Book Qty</td>
                    <td>Tot.Book Qty</td>
                    <td>Balance</td>
                    <td>Comments</td>
                    </tr>
                    <?
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	if ($cbo_fabric_natu!=0) $cbo_fabric_natu="and a.fab_nature_id='$cbo_fabric_natu'";
	if ($cbo_fabric_source!=0) $cbo_fabric_source_cond="and a.fabric_source='$cbo_fabric_source'";
	$paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_mst_id,size_mst_id,item_mst_id", "id", "plan_cut_qnty");

	$item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no =$txt_job_no", "gmts_item_id", "set_item_ratio");
	$nameArray=sql_select("
	select
	a.id,
	a.item_number_id,
	a.costing_per,
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
		b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0
		order by a.id");
	$count=0;
	$tot_grey_req_as_pre_cost_arr=array();
	foreach ($nameArray as $result)
	{
		if (count($nameArray)>0 )
		{
            if($result[csf("costing_per")]==1)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(12*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==2)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(1*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==3)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(24*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==4)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(36*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			if($result[csf("costing_per")]==5)
			{
				$tot_grey_req_as_pre_cost=def_number_format((($paln_cut_qnty_array[$result[csf("color_size_table_id")]]/(48*$item_ratio_array[$result[csf("item_number_id")]]))*$result[csf("requirment")]),5,"");
			}
			$tot_grey_req_as_pre_cost_arr[$result[csf("po_number")]]+=$tot_grey_req_as_pre_cost;
        }
    }
	                $total_pre_cost=0;
					$total_booking_qnty_main=0;
					$total_booking_qnty_short=0;
					$total_booking_qnty_sample=0;
					$total_tot_bok_qty=0;
					$tot_balance=0;
					$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

					$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

					$sql_data=sql_select( "select max(a.id) as id,a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by id");
					foreach($sql_data  as $row)
                    {
					$col++;
					?>
                    <tr align="center">
                    <td><? echo $col; ?></td>
                    <td><? echo $row[csf("po_number")]; ?></td>
                     <td><? echo change_date_format($row[csf("pub_shipment_date")],"dd-mm-yyyy",'-'); ?></td>
                    <td align="right"><? echo number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]],2); $total_pre_cost+=$tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]; ?></td>
                    <td align="right"><? echo number_format($booking_qnty_main[$row[csf("id")]],2); $total_booking_qnty_main+=$booking_qnty_main[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_short[$row[csf("id")]],2); $total_booking_qnty_short+=$booking_qnty_short[$row[csf("id")]];?></td>
                    <td align="right"><? echo number_format($booking_qnty_sample[$row[csf("id")]],2); $total_booking_qnty_sample+=$booking_qnty_sample[$row[csf("id")]];?></td>
                    <td align="right"><? $tot_bok_qty=$booking_qnty_main[$row[csf("id")]]+$booking_qnty_short[$row[csf("id")]]+$booking_qnty_sample[$row[csf("id")]]; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?></td>
                    <td align="right">
					<? $balance= def_number_format($tot_grey_req_as_pre_cost_arr[$row[csf("po_number")]]-$tot_bok_qty,2,""); echo number_format($balance,2); $tot_balance+= $balance?>
                    </td>
                    <td>
					<?
					if( $balance>0)
					{
						echo "Less Booking";
					}
					else if ($balance<0)
					{
						echo "Over Booking";
					}
					else
					{
						echo "";
					}
					?>
                    </td>
                    </tr>
                    <?
					}
					?>
                    <tfoot>

                    <tr>
                    <td colspan="3">Total:</td>

                    <td align="right"><? echo number_format($total_pre_cost,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_main,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_short,2); ?></td>
                    <td align="right"><? echo number_format($total_booking_qnty_sample,2); ?></td>
                     <td align="right"><? echo number_format($total_tot_bok_qty,2); ?></td>
                    <td align="right"><? echo number_format($tot_balance,2); ?></td>
                    <td></td>
                    </tr>
                    </tfoot>
                    </table>
                    <?
				}
				?>
                </td>

            </tr>
        </table>


        <br/>
        <?
		$department_name_library=return_library_array( "select id,department_name from  lib_department where status_active=1 and is_deleted=0 order by  department_name", "id", "department_name"  );

		$sql_responsible= sql_select("select responsible_dept,	responsible_person,	reason from wo_booking_dtls where booking_no =$txt_booking_no");
		if(count($sql_responsible)>0)
		{
		?>
         <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
         <tr>
         <td>
          #
         </td>
          <td>
         Responsible Dept.
         </td>
         <td>
         Responsible person
         </td>
         <td>
         Reason
         </td>
         </tr>
         <?
		 $ir=1;
		foreach($sql_responsible as $sql_responsible_row)
		{
			?>
             <tr>
             <td>
             <?  echo $ir; ?>
             </td>
              <td>
             <?
			 $responsible_dept_st="";
			 $responsible_dept_arr=explode( ",",$sql_responsible_row[csf('responsible_dept')]);
			 foreach($responsible_dept_arr as $key => $value)
			 {
				 $responsible_dept_st.= $department_name_library[$value].", ";
			 }
			 echo rtrim($responsible_dept_st,", ");
			 ?>
             </td>
             <td>
            <?=$sql_responsible_row[csf('responsible_person')]; ?>
             </td>
             <td>
              <?=$sql_responsible_row[csf('reason')]; ?>
             </td>
             </tr>
            <?
			$ir++;

		}
		 ?>
         </table>
         <?
		}
		$job_imge_arr = sql_select("select master_tble_id,image_location from   common_photo_library where form_name='knit_order_entry'  and file_type=1 and master_tble_id =$txt_job_no");
		foreach ($job_imge_arr as $row) {
			$image_location = $row[csf('image_location')];
		}
		 ?>
         <table width="100%"   cellpadding="2" cellspacing="0" rules="all" style="border-left: none;border-right: none;border-top: none;border-bottom: none; margin-top: 10px">
			<tr>
			 	<td align="left" style="border-left: none;border-right: none;border-top: none;border-bottom: none;">
			 		<img  src='<? echo $path.$image_location; ?>' height='180' width='250' />
			 	</td>
			</tr>
		</table>

         <?
		 	echo signature_table(4, $cbo_company_name, "1330px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
		 ?>

       </div>
       <?
	   exit();
}




if($action=='app_mail_notification'){
	extract($_REQUEST);
	list($booking_id_str,$email,$alter_user_id,$company_name,$type)=explode('__',$data);


	$alter_user_id=str_replace("'","",$alter_user_id);
	$alter_user_id = ($alter_user_id=="")?$user_id:$alter_user_id;


	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$company_library=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$teamArr=array();$marchentrArr=array();
	$teamSql = sql_select("select a.id, b.id as bid, a.team_name, a.team_leader_name, a.team_leader_email, a.user_tag_id, b.user_tag_id as user_id, b.team_member_name, b.team_member_email from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and a.status_active=1 and a.is_deleted=0 ");
	foreach($teamSql as $trow)
	{
		$teamArr[$trow[csf('id')]]['team_leader_name']=$trow[csf('team_leader_name')];
		$marchentrArr[$trow[csf('bid')]]['team_member_name']=$trow[csf('team_member_name')];
	}


	$sql= "select a.IS_APPROVED,a.COMPANY_ID,a.JOB_NO,a.BUYER_ID,a.BOOKING_DATE, a.BOOKING_NO,a.SHORT_BOOKING_TYPE, b.TEAM_LEADER,b.DEALING_MARCHANT,b.FACTORY_MARCHANT,a.INSERTED_BY ,sum(c.amount) as AMOUNT from wo_booking_mst a, wo_po_details_master b,WO_BOOKING_DTLS c where a.job_no=b.job_no and a.BOOKING_NO=c.BOOKING_NO and a.entry_form=88 and a.booking_type=1 and a.is_short=1 and a.id in($booking_id_str) and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 group by a.COMPANY_ID,a.JOB_NO,a.BUYER_ID,a.BOOKING_DATE, a.BOOKING_NO,a.SHORT_BOOKING_TYPE, b.TEAM_LEADER,b.DEALING_MARCHANT,b.FACTORY_MARCHANT,a.INSERTED_BY,a.IS_APPROVED order by a.id DESC";
	 //echo $sql; 
	$sql_res=sql_select($sql);
	foreach($sql_res as $row){

		ob_start();
		?>
		<table border="1" rules="all">
			<tr><td>Company Name</td><td><?=$company_library[$row['COMPANY_ID']];?></td></tr>
			<tr><td>Buyer Name</td><td><?=$buyer_name_arr[$row['BUYER_ID']];?></td></tr>
			<tr><td>Booking Date</td><td><?=change_date_format($row['BOOKING_DATE']);?></td></tr>
			<tr><td>Booking No.</td><td><?=$row['BOOKING_NO'];?></td></tr>
			<tr><td>Short Booking Type</td><td><?=$short_booking_type[$row['SHORT_BOOKING_TYPE']];?></td></tr>
			<tr><td>Team Leader</td><td><?=$teamArr[$row['TEAM_LEADER']]['team_leader_name'];?></td></tr>
			<tr><td>Dealing Merchant</td><td><?=$marchentrArr[$row['DEALING_MARCHANT']]['team_member_name'];?></td></tr>
			<tr><td>Factory Merchant</td><td><?=$marchentrArr[$row['FACTORY_MARCHANT']]['team_member_name'];?></td></tr>
			<tr><td>Job No.</td><td><?=$row['JOB_NO'];?></td></tr>
			<tr><td>Additional fabric booking value</td><td><?= $row['AMOUNT'];?></td></tr>
		</table>

		<?

		$mailBody=ob_get_contents();
		ob_clean();

 

		//Mail send------------------------------------------
			
			//require_once('../../../mailer/class.phpmailer.php');
			require_once('../../auto_mail/setting/mail_setting.php');
			
			$mailToArr=array();
			if($msil_address){$mailToArr[]=$msil_address;}


			$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and ENTRY_FORM=12 and user_id=$alter_user_id and is_deleted=0");


			if($type == 0){
				$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1  AND a.ENTRY_FORM=12 and a.company_id=$company_name and SEQUENCE_NO >$user_sequence_no order by a.SEQUENCE_NO";
				 //echo $elcetronicSql;
				$elcetronicSqlRes=sql_select($elcetronicSql);
				foreach($elcetronicSqlRes as $rows){
					if($rows['USER_EMAIL']){$mailToArr[]=$rows['USER_EMAIL'];}
					if($rows['BYPASS']==2){break;}
				}

				 
				if($row['IS_APPROVED'] == 1){
					$elcetronicSql = "SELECT b.USER_EMAIL  from  user_passwd b where b.id=".$row['INSERTED_BY'];
				 //echo $elcetronicSql;
					$elcetronicSqlRes=sql_select($elcetronicSql);
					foreach($elcetronicSqlRes as $rows){
						if($rows['USER_EMAIL']){$mailToArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];}
					}	
				}

			}
			else{//$alter_user_id
				$elcetronicSql = "SELECT b.USER_EMAIL  from  user_passwd b where b.id=".$row['INSERTED_BY'];
				//echo $elcetronicSql;
				$elcetronicSqlRes=sql_select($elcetronicSql);
				foreach($elcetronicSqlRes as $rows){
					if($rows['USER_EMAIL']){$mailToArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];}
				}	
			}

			$to=implode(',',array_unique($mailToArr));

			if($row['IS_APPROVED'] == 1){
				$subject = "Additional Fabric Booking Request Full Approved";
				$msg = "Your request has been full approved.";
			}
			else if($type == 0){
				$subject = "Additional Fabric Booking Approval Request";
				$msg = "<b>Dear Sir,</b><br>Please log in to ERP and  check below Additional Fabric Booking request for your electronic approval.";
			}
			else if($type == 1){
				$subject = "Additional Fabric Booking Request Un-approved";
				$msg = "<b>Dear Sir,</b><br>Your request has been unapproved.";
			}
			else if($type == 5){
				$subject = "Additional Fabric Booking Approval Request Rejected";
				$msg = "<b>Dear Sir,</b><br>Your request has been rejected";
			}
			$mailBody = $msg."<br>".$mailBody;
 
			
			  //echo $to;die;

			$header=mailHeader();
			echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );


		//------------------------------------End;
	}

exit();
}


?>