<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
require_once('../../includes/class4/class.conditions.php');
require_once('../../includes/class4/class.reports.php');
require_once('../../includes/class4/class.fabrics.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];




if($action=='user_popup'){
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	?>	
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>

	<form>
		<input type="hidden" id="selected_id" name="selected_id" /> 
		<?php
			$custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
			$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;

			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.entry_form=13 and valid=1 and a.id!=$user_id  and b.is_deleted=0 order by SEQUENCE_NO";
				//echo $sql;
			$arr=array (2=>$custom_designation,3=>$Department);
			echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","330",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
			?>
			
	</form>
	<script language="javascript" type="text/javascript">
		setFilterGrid("tbl_style_ref");
	</script>
	<?
	exit();
}




if($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}

if($action=="load_drop_down_buyer_new_user"){
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

 
function getSequence($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
 	$brandSql = "select ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0";
	$brandSqlRes=sql_select($brandSql);
	foreach($brandSqlRes as $row){
		$buyer_wise_brand_id_arr[$row['BUYER_ID']][$row['ID']] = $row['ID'];
	}

	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['entry_form']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 // echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){		

		if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_arr;}

		if($rows['BRAND_ID']=='' || $rows['BRAND_ID']==0){
			$tempBrandArr = array();
			foreach(explode(',',$rows['BUYER_ID']) as $buyer_id){
				if(count($buyer_wise_brand_id_arr[$buyer_id])){$tempBrandArr[]= implode(',',$buyer_wise_brand_id_arr[$buyer_id]);}
			}
			$rows['BRAND_ID']=implode(',',$tempBrandArr);
		}


		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
 
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));

	$brandSql = "select ID, BRAND_NAME,BUYER_ID from lib_buyer_brand where STATUS_ACTIVE=1 and IS_DELETED=0";
	$brandSqlRes=sql_select($brandSql);
	foreach($brandSqlRes as $row){
		$buyer_wise_brand_id_arr[$row['BUYER_ID']][$row['ID']] = $row['ID'];
	}
	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND entry_form = {$parameterArr['entry_form']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	   //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
	
		if($rows['BUYER_ID']==''){$rows['BUYER_ID']=$lib_buyer_arr;}

		if($rows['BRAND_ID']=='' || $rows['BRAND_ID'] == 0){
			$tempBrandArr = array();
			foreach(explode(',',$rows['BUYER_ID']) as $buyer_id){
				if(count($buyer_wise_brand_id_arr[$buyer_id])){$tempBrandArr[]= implode(',',$buyer_wise_brand_id_arr[$buyer_id]);}
			}
			$rows['BRAND_ID']=implode(',',$tempBrandArr);
		}

		$usersDataArr[$rows['USER_ID']]['BUYER_ID']=explode(',',$rows['BUYER_ID']);
		$usersDataArr[$rows['USER_ID']]['BRAND_ID']=explode(',',$rows['BRAND_ID']);

		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}

	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			if(
				(in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID']) &&  $bbtsRows['buyer_id']>0)
				&& (in_array($bbtsRows['brand_id'],$usersDataArr[$user_id]['BRAND_ID']) ||  $bbtsRows['brand_id'] == 0)
			){
				$finalSeq[$sys_id][$user_id]=$seq;
			}


		}
	}

	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}

function auto_approved($dataArr=array()){
	global $pc_date_time;
	global $user_id;
	$sys_id_arr=explode(',',$dataArr['sys_id']);
	
	$queryText = "select a.id,a.SETUP_DATE,b.APPROVAL_NEED,b.ALLOW_PARTIAL,b.PAGE_ID from APPROVAL_SETUP_MST a,APPROVAL_SETUP_DTLS b where a.id=b.MST_ID and a.COMPANY_ID=$dataArr[company_id] and b.PAGE_ID=$dataArr[app_necessity_page_id] and a.STATUS_ACTIVE =1 and a.IS_DELETED=0  and b.STATUS_ACTIVE =1 and b.IS_DELETED=0 order by a.SETUP_DATE desc";
	$queryTextRes = sql_select($queryText);
	
	if($queryTextRes[0]['ALLOW_PARTIAL']==1){
		$con = connect();
	
		$query="UPDATE $dataArr[mst_table] SET IS_APPROVED=1,approved_by=$dataArr[approval_by],approved_date='$pc_date_time' WHERE id in ($dataArr[sys_id])";
		$rID1=execute_query($query,1);
		//echo $query;die;
		
		if($rID1==1){ oci_commit($con);}
		else{oci_rollback($con);}
		
		
		disconnect($con);
		//return $query;
	}
	//return $ALLOW_PARTIAL;
}


$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="report_generate")
{
 	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$txt_booking_no	= trim(str_replace("'","",$txt_booking_no));
    $txt_file_no = trim(str_replace("'","",$txt_file_no));
	$txt_internal_ref = trim(str_replace("'","",$txt_internal_ref));	
	$approval_type = str_replace("'","",$cbo_approval_type);
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
	$cbo_get_upto = str_replace("'","",$cbo_get_upto);
	$txt_date = str_replace("'","",$txt_date);

	$app_user_id = ($txt_alter_user_id != "")?$txt_alter_user_id:$user_id;


	if($cbo_company_name != 0){$where_con = " and a.company_id=$cbo_company_name";}
	if($cbo_buyer_name != 0){$where_con .= " and a.buyer_id=$cbo_buyer_name";}
	if($txt_file_no != "") {$where_con .= " and c.file_no='".$txt_file_no."'";} 
	if($txt_internal_ref != ""){$where_con .=" and c.grouping like '%".$txt_internal_ref."%' ";  }  
    if($txt_booking_no != ""){$where_con .=" and a.booking_no like('%".$txt_booking_no."') ";}
	
	if($txt_date != "")
	{
		if($cbo_get_upto == 1){ $where_con .= " and a.booking_date > '$txt_date'";}
		else if($cbo_get_upto == 2){ $where_con .= " and a.booking_date <= '$txt_date'";}
		else if($cbo_get_upto == 3){ $where_con .= " and a.booking_date = '$txt_date'";}
	}


	$electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'entry_form'=>13,'user_id'=>$app_user_id,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0));

	//echo $electronicDataArr['sequ_by'][2]['BUYER_ID'];die;


	if($approval_type == 0){
			//Match data..................................
			if($electronicDataArr['user_by'][$app_user_id]['BUYER_ID']){
				$where_con .= " and a.BUYER_ID in(".$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'].",0)";
				$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$app_user_id]['BUYER_ID'];
			}

			if($electronicDataArr['user_by'][$app_user_id]['BRAND_ID']){
				$where_con .= " and a.BRAND_ID in(".$electronicDataArr['user_by'][$app_user_id]['BRAND_ID'].",0)";
				$electronicDataArr['sequ_by'][0]['BRAND_ID']=$electronicDataArr['user_by'][$app_user_id]['BRAND_ID'];
			}
		
			$data_mas_sql = "select a.ID, a.BUYER_ID,a.BRAND_ID from wo_booking_mst a where  a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.is_approved<>1 and a.ready_to_approved=1 and a.is_short=2 and a.booking_type=4 and a.item_category in(2,3,13)  $where_con ";
			  //echo $data_mas_sql;die;

			$tmp_sys_id_arr=array();
			$data_mas_sql_res=sql_select( $data_mas_sql );
			foreach ($data_mas_sql_res as $row)
			{ 
				for($seq=($electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
					
					if($electronicDataArr['sequ_by'][$seq]['BUYER_ID']==''){$electronicDataArr['sequ_by'][$seq]['BUYER_ID']=0;}
					if($electronicDataArr['sequ_by'][$seq]['BRAND_ID']==''){$electronicDataArr['sequ_by'][$seq]['BRAND_ID']=0;}

					if( (in_array($row['BUYER_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BUYER_ID']))) 
						&& (in_array($row['BRAND_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['BRAND_ID'])) || $row['BRAND_ID']==0)
					)	
					{
						$tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
						break;
					}
				}
			}
			//..........................................Match data;		

			//  print_r($tmp_sys_id_arr[0]);die;
			  //echo $electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO'];die;

			$sql='';
			for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
				$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');

				if($tmp_sys_id_arr[$seq]){
					if($sql!=''){$sql .=" UNION ALL ";}
					
					$sql .= "SELECT a.id, a.booking_no_prefix_num as prefix_num,a.booking_no, a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,c.grouping, c.file_no,c.JOB_ID from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.is_short=2 and a.booking_type=4 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1   and b.fin_fab_qnty>0 and a.is_approved<>1  and a.APPROVED_SEQU_BY=$seq  $sys_con group by a.id,a.booking_no_prefix_num, a.booking_no, a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no,c.job_id order by a.insert_date desc";
				}

			}

		}
		else
		{

			$sql="SELECT a.ID,a.booking_no_prefix_num as prefix_num, a.booking_no, a.item_category, a.entry_form, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,c.grouping, c.file_no,c.JOB_ID from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,APPROVAL_MST d  where a.booking_no=b.booking_no and b.job_no=c.job_no_mst and b.po_break_down_id =c.id and a.ready_to_approved=1 and d.SEQUENCE_NO={$electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']} and a.APPROVED_SEQU_BY=d.SEQUENCE_NO and d.ENTRY_FORM=13 $where_con and a.is_short=2 and a.booking_type=4 and a.item_category in(2,3,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1 and a.is_approved in(1,3) and b.fin_fab_qnty>0 group by a.id, a.booking_no,a.booking_no_prefix_num, a.item_category, a.entry_form, a.fabric_source, a.company_id, a.booking_type, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, a.job_no, a.is_approved, a.po_break_down_id, a.is_apply_last_update,a.insert_date,c.grouping, c.file_no,c.job_id order by a.insert_date desc";
		}
	  
		 //echo $sql;

	 $nameArray=sql_select( $sql );
	 foreach ( $nameArray as $row) {
		$job_id_arr[$row['JOB_ID']] = $row['JOB_ID'];
		$booking_id_arr[$row['ID']] = $row['ID'];
	 }


	$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");
	$job_dealing_merchant_array = return_library_array("select job_no, dealing_marchant from  wo_po_details_master where is_deleted=0 and  status_active=1 ".where_con_using_array($job_id_arr,0,'id')."","job_no","dealing_marchant");

	// print_r($job_dealing_merchant_array);


	$sql_cause_sql="select BOOKING_ID,APPROVAL_CAUSE from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=13 and user_id=$app_user_id  ".where_con_using_array($booking_id_arr,0,'booking_id')." and approval_type=$approval_type and status_active=1 and is_deleted=0";
	$cause_arr = return_library_array($sql_cause_sql,"BOOKING_ID","APPROVAL_CAUSE");

	//print_r($sql_cause_sql);

	?>

    
    <script>
	
		function openmypage_app_cause(wo_id,app_type,i,app_user_id)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();	
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id+"_"+app_user_id;
			var title = 'Approval Cause Info';	
			var page_link = 'requires/sample_fb_booking_wo_with_order_approval_controller.php?data='+data+'&action=appcause_popup';
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
			var page_link = 'requires/sample_fb_booking_wo_with_order_approval_controller.php?data='+data+'&action=unappcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
				$('#txt_unappv_req_'+i).val(unappv_request.value);
			}
		}
		
	</script>
    
    <?

		$table=1500; 
	
		$print_report_format_ids_sample = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id=3 and is_deleted=0 and status_active=1");
		$format_ids_sample_feb_booking=explode(",",$print_report_format_ids_sample);  //Sample fabric Booking
        //print_r($format_ids_sample_feb_booking);
		$print_report_formatreq_ids_sample = return_field_value("format_id","lib_report_template","template_name=".$company_name." and module_id=2 and report_id=48 and is_deleted=0 and status_active=1");
		$format_ids_sample_requ_feb_booking=explode(",",$print_report_formatreq_ids_sample);  //Sample fabric Booking
		
	?>
    
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<?=$table+20; ?>px; margin-top:10px">
        <legend>Sample Booking Approval (With Order)</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$table; ?>" class="rpt_table" align="left" >
                <thead>
                	<th width="35"></th>
                    <th width="40">SL</th>
                    <th width="100">Booking No</th>
					<th width="100">Last Version</th>
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
                    <th width="80" >Delivery Date</th>
                    <? 
						if($approval_type==0) echo "<th width='100'>Appv Instra</th>";
						if($approval_type==1)echo "<th width='100'>Un-appv request</th>"; 
					?>
                    <th><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
                </thead>
            </table>            
            <div style="width:<?=$table+20; ?>px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="left">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$table; ?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?
						 //echo $sql; die;
                            $i=1; $all_approval_id='';
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

								$row_id=0;
								if ($row[csf('entry_form')]==89) 
								{
									$row_id=$format_ids_sample_feb_booking[0];
									if($row_id==""){ $row_id=0; }
								}
								if ($row[csf('entry_form')]==139) 
								{
									$row_id=$format_ids_sample_requ_feb_booking[0];
									if($row_id==""){ $row_id=0; }
									else{ $type=139; }
								}

								if($row_id==38)
								{	
									$variable="<a href='##' style='color:#000' onClick=\"generate_worder_report12('".$type."','".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report','".$i."')\">".$row[csf('prefix_num')]."</a>";
								}

								
								else if($row_id==17)
								{
									$variable="<a href='##' style='color:#000' onClick=\"generate_worder_report12('".$type."','".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report2','".$i."')\">".$row[csf('prefix_num')]."</a>";
								}
								else if($row_id==39)
								{
									$variable="<a href='##' style='color:#000' onClick=\"generate_worder_report12('".$type."','".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report2','".$i."')\">".$row[csf('prefix_num')]."</a>";
								}
								else if($row_id==64)
								{
									$variable="<a href='##' style='color:#000' onClick=\"generate_worder_report12('".$type."','".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report3','".$i."')\">".$row[csf('prefix_num')]."</a>";
								}
								else if($row_id==155)
								{
									$variable="<a href='##' style='color:#000' onClick=\"generate_worder_report12('".$type."','".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report','".$i."')\">".$row[csf('prefix_num')]."</a>";
								}
								else if($row_id==17)
								{
									$variable="<a href='##' style='color:#000' onClick=\"generate_worder_report12('".$type."','".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report2','".$i."')\">".$row[csf('prefix_num')]."</a>";
								}
								else if($row_id==0)
								{
									$variable="<a href='##' style='color:#000' onClick=\"generate_worder_report12('".$type."','".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','show_fabric_booking_report','".$i."')\">".$row[csf('prefix_num')]."</a>";
								}
								$supplier_name=""; 
								if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) $supplier_name=$company_arr[$row[csf('supplier_id')]]; else $supplier_name=$supplier_arr[$row[csf('supplier_id')]];



								$revised_no="";
								if($row[csf('revised_no')]>0)
								{
									for($q=1; $q<=$row[csf('revised_no')]; $q++)
									{
										if($revised_no=="") $revised_no="<a href='##' style='color:#000' onClick=\"generate_worder_report('38','".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','last_version_check_print_booking','".$q."')\">".$q."</a>";
										else $revised_no.=", "."<a href='##' style='color:#000' onClick=\"generate_worder_report('38','".$row[csf('booking_no')]."','".$row[csf('company_id')]."','".$row[csf('po_break_down_id')]."','".$row[csf('item_category')]."','".$row[csf('fabric_source')]."','".$row[csf('job_no')]."','".$row[csf('is_approved')]."','last_version_check_print_booking','".$q."')\">".$q."</a>";

									}
								}
							

								if($print_cond==1)
								{	
									?>
									<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?= $i; ?>"> 
										<td width="35" align="center" valign="middle">
											<input type="checkbox" id="tbl_<?= $i;?>" name="tbl[]" onClick="check_last_update(<? echo $i;?>);" />
											<input id="booking_id_<?= $i;?>" name="booking_id[]" type="hidden" value="<?= $value; ?>" />
											<input id="booking_no_<?= $i;?>" name="booking_no[]" type="hidden" value="<?= $row[csf('booking_no')]; ?>" />
											<input id="approval_id_<?= $i;?>" name="approval_id[]" type="hidden" value="<?= $row[csf('approval_id')]; ?>" />
                                            <input id="last_update_<?= $i;?>" name="last_update[]" type="hidden" value="<?= $row[csf('is_apply_last_update')]; ?>" />
                                            <input id="<?= strtoupper($row[csf('booking_no')]); ?>" name="no_booook[]" type="hidden" value="<?= $i;?>" />
										</td>   
										<td width="40" id="td_<?= $i; ?>" style="cursor:pointer" align="center" onClick="generate_worder_report2(<?= $type; ?>,'<?= $row[csf('booking_no')]; ?>',<?= $row[csf('company_id')]; ?>,'<?= $row[csf('po_break_down_id')]; ?>',<?= $row[csf('item_category')].','.$row[csf('fabric_source')]; ?>,'<?= $row[csf('job_no')]; ?>','<?= $row[csf('is_approved')]; ?>','show_fabric_booking_report3')"><?= $i; ?>
										</td>
										<td width="100"><p><?= $variable; ?></p></td>
										<td width="100"><p><?= $revised_no;?></p></td>
										<td width="80" align="center"><p><?= $booking_type; ?></p></td>
										<td width="100" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?></td>
										<td width="125"><p><?= $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
										<td width="160"><p><? 
										if($row[csf('pay_mood')]==1 || $row[csf('pay_mood')]==2)
										{
											echo $supplier_arr[$row[csf('supplier_id')]]; 
										}
										else
										{
											echo $company_arr[$row[csf('supplier_id')]]; 
										}
										?></p></td>
										<td width="100" align="center"><p><?= $row[csf('job_no')]; ?></p></td>
                                        <td width="70"><?= $row[csf('grouping')]; ?></td>
                    					<td width="70"><?= $row[csf('file_no')]; ?></td>
										<td width="110" id="dealing_merchant_<?= $i;?>"><p><?= $dealing_merchant; ?></p></td>
										<td width="50" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('job_no')];?>','img');">View</a></td>
										<td width="50" align="center"><a href="##" onClick="openImgFile('<?= $row[csf('job_no')];?>','file');">View</a></td>
										<td align="center" width="80"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?></td>                                
                                        <?
										if($approval_type==0)echo "<td align='center' width='100'>
                                        		<Input name='txt_appv_instra[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_appv_instra_".$i."' style='width:70px' maxlength='50' onClick='openmypage_app_instrac(".$value.",".$approval_type.",".$i.")'></td>";
										if($approval_type==1)echo "<td align='center' width='100'>
                                        	<Input name='txt_unappv_req[]' class='text_boxes' readonly placeholder='Please Brows' ID='txt_unappv_req_".$i."' style='width:65px' maxlength='50' onClick='openmypage_unapp_request(".$value.",".$approval_type.",".$i.")'></td>"; 
                                            
                                        ?>
                                        <td align="center">
                                        	<Input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Brows" ID="txt_appv_cause_<? echo $i;?>" style="width:97px" maxlength="50" title="Maximum 50 Character" value="<?=$cause_arr[$row['ID']];?>" onClick="openmypage_app_cause(<?= $value; ?>,<?= $approval_type; ?>,<?= $i;?>,<?= $app_user_id;?>)"></td>
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
							$isApp="";
							if($approval_type==1) $isApp=" display:none"; else $isApp="";
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="<?=$table; ?>" class="rpt_table" align="left">
				<tfoot>
                    <td width="35" align="center" style=" <?=$isApp; ?>"><input type="checkbox" id="all_check" onClick="check_all('all_check')" /><font style="display:none"><? echo $all_approval_id; ?></font></td>
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
	$user_id_approval = ($_REQUEST['txt_alter_user_id'] !="" ) ? $_REQUEST['txt_alter_user_id'] : $user_id;

	$sql="select A.ID,a.BUYER_ID,a.BRAND_ID,a.IS_APPROVED from wo_booking_mst a where a.READY_TO_APPROVED=1 and a.id in($booking_ids)";
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
		$matchDataArr[$row['ID']]=array('buyer_id'=>$row['BUYER_ID'],'brand_id'=>$row['BRAND_ID'],'item'=>0,'store'=>0);
		$approved_status_arr[$row['ID']] = $row['IS_APPROVED'];
	}

	 
	$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'entry_form'=>13,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
	
	$sequ_no_arr_by_sys_id = $finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];

	$max_approved_no_arr = return_library_array("select mst_id, max(approved_no) as approved_no from approval_history where mst_id in($booking_ids) and entry_form=13 group by mst_id","mst_id","approved_no");

	//print_r($sequ_no_arr_by_sys_id);die;
	$rIDArr=array();
	if($approval_type==0)
	{

		$response=$booking_ids;
		$id=return_next_id( "id","approval_history", 1 ) ;
		$appid=return_next_id( "id","approval_mst", 1 ) ;
		

		$approved_no_array=array();
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

			$approved=(max($finalDataArr['final_seq'][$booking_id])==$user_sequence_no)?1:3;

			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$val]=$approved_no;
				if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
			}
			
			if(($user_sequence_no*1)==0) { echo "seq**".$user_sequence_no; disconnect($con);die; }
			if($his_data_array!="") $his_data_array.=",";
			$his_data_array.="(".$id.",13,".$booking_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."','".$user_ip."',".$app_instru.",".$user_id.",'".$pc_date_time."',".$approved.")"; 
			$id=$id+1;

			//app mst data.......................
			if($app_data_array!=''){$app_data_array.=",";}
			$app_data_array.="(".$appid.",13,".$booking_id.",".$user_sequence_no.",".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."',".$approved.")"; 
			$appid=$appid+1;

			//mst data.......................
			
			$data_array_up[$booking_id] = explode(",",("".$approved.",".$user_sequence_no."")); 
			
		}
		
		$flag=1;
		
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
				
					
			
			$sql_insert_dtls="insert into wo_booking_dtls_hstry(id, approved_no, booking_dtls_id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted,rmg_qty, gmt_item, responsible_dept, responsible_person, reason, gmts_size, gmts_color_id,revised_date) 
				select	
				'', $approved_string_dtls, id, job_no, po_break_down_id, pre_cost_fabric_cost_dtls_id, color_size_table_id, booking_no, booking_type, is_short, fabric_color_id, item_size, fin_fab_qnty, grey_fab_qnty, rate, amount, color_type, construction, copmposition, gsm_weight, dia_width, process_loss_percent, trim_group, description, brand_supplier, uom, process, sensitivity, wo_qnty, delivery_date, cons_break_down, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, rmg_qty, gmt_item, responsible_dept, responsible_person, reason, gmts_size, gmts_color_id,'".date('d-M-Y',time())."' from wo_booking_dtls where booking_no in ($book_nos)";
				
				
				if($flag==1) 
				{
					$rIDArr[1]=execute_query($sql_insert,0);
					if($rIDArr[1]) $flag=1; else $flag=0; 
				}
				
				if($flag==1) 
				{
					$rIDArr[2]=execute_query($sql_insert_dtls,1);
					if($rIDArr[2]) $flag=1; else $flag=0; 
				}		
		
		}
		
		//first
			
		if($flag==1) 
		{

			 $field_array_up="IS_APPROVED*APPROVED_SEQU_BY"; 
			 $rIDArr[3]=execute_query(bulk_update_sql_statement( "WO_BOOKING_MST", "id", $field_array_up, $data_array_up, $booking_ids_all ));
			 if($rIDArr[3]) $flag=1; else $flag=0; 


		}

		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip,approved";
			$rIDArr[4]=sql_insert("approval_mst",$field_array,$app_data_array,0);
			if($rIDArr[4]) $flag=1; else $flag=0; 
		}
		
		if($flag==1) 
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=13 and mst_id in ($booking_ids)";
			$rIDArr[5]=execute_query($query,1);
			if($rIDArr[5]) $flag=1; else $flag=0; 
		} 
		
		
		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date,APPROVED"; 
			$rIDArr[6]=sql_insert("approval_history",$field_array,$his_data_array,0);
			if($rIDArr[6]) $flag=1; else $flag=0; 
		} 


		if($flag==1) $msg=19; else $msg=21;
		
		if($flag==1)
		{
			auto_approved(array('company_id'=>$cbo_company_name,'app_necessity_page_id'=>7,'mst_table'=>'wo_booking_mst','sys_id'=>$booking_ids,'approval_by'=>$user_id_approval));//,user_sequence=>$user_sequence_no,entry_form=>15,page_id=>$menu_id
		}
		
		
	}
	else
	{
		
		
		$response=$booking_ids;
		$id=return_next_id( "id","approval_history", 1 ) ;
		
		$approved_no_array=array();
		$booking_ids_all=explode(",",$booking_ids);
		$booking_nos_all=explode(",",$booking_nos);
		$app_instru_all=explode(",",$appv_instras);

		
		for($i=0;$i<count($booking_nos_all);$i++)
		{
			$booking_id=$booking_ids_all[$i];
			$app_instru=$app_instru_all[$i];
			$approved_no=$max_approved_no_arr[$booking_id]*1;
			if(($user_sequence_no*1)==0) { echo "seq**".$user_sequence_no; disconnect($con);die; }
			if($his_data_array!="") $his_data_array.=",";
			$his_data_array.="(".$id.",13,".$booking_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$pc_date_time."','".$user_ip."',".$app_instru.",".$user_id.",'".$pc_date_time."',0)"; 
			$id=$id+1;
		}
		

		
		$rIDArr[1]=sql_multirow_update("wo_booking_mst","is_approved*ready_to_approved*APPROVED_SEQU_BY","0*0*0","id",$booking_ids,0);
		if($rIDArr[1]) $flag=1; else $flag=0;


		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=13 and mst_id in ($booking_ids)";
			$rIDArr[2]=execute_query($query,1); 
			if($rIDArr[2]) $flag=1; else $flag=0; 
		}

		
		if($flag==1) 
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=13 and mst_id in ($booking_ids)";
			$rIDArr[3]=execute_query($query,1);
			if($rIDArr[3]) $flag=1; else $flag=0; 
		} 
			

		
		if($flag==1) 
		{
			$rIDArr[4]=sql_multirow_update("fabric_sales_order_mst","is_apply_last_update","2","booking_id",$booking_ids,1);
			if($rIDArr[4]) $flag=1; else $flag=0; 
		} 

		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=13 and mst_id in ($booking_ids)";
			$rIDArr[5]=execute_query($query,1); 
			if($rIDArr[5]) $flag=1; else $flag=0; 
		}

		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, user_ip,comments,inserted_by,insert_date,APPROVED"; 
			$rIDArr[6]=sql_insert("approval_history",$field_array,$his_data_array,0);
			if($rIDArr[6]) $flag=1; else $flag=0; 
		} 
		
		$response=$booking_ids;
		
		if($flag==1) $msg=20; else $msg=22;
	}
	
	
	if($flag==1)
	{
		oci_commit($con); 
		echo $msg."**".$response;
	}
	else
	{
		oci_rollback($con); 
		echo $msg."**".$response.'** Response Status:'.implode(", ",$rIDArr);
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
	$app_user_id=$data_all[4];
	
	if($app_cause=="")
	{	
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=13 and user_id='$app_user_id' and booking_id='$wo_id' and approval_type='$app_type' and status_active=1 and is_deleted=0";				
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
				
				var data="action=save_update_delete_appv_cause&operation="+operation+get_submitted_data_string('appv_cause*wo_id*appv_type*page_id*app_user_id*approval_id',"../../");
				//alert (data);return;
				freeze_window(operation);
				http.open("POST","sample_fb_booking_wo_with_order_approval_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange=fnc_appv_entry_Reply_info;
			}
		}
		
		function fnc_appv_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{

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
			http.open("POST","sample_fb_booking_wo_with_order_approval_controller.php",true);
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
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<?= $wo_id; ?>" style="width:30px" />
                        <Input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<?= $app_type; ?>" style="width:30px" />
                        <Input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<?= $menu_id; ?>" style="width:30px" />
                        <Input type="hidden" name="app_user_id" class="text_boxes" ID="app_user_id" value="<?= $app_user_id; ?>" style="width:30px" />
                        <Input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<?= $approval_id; ?>" style="width:30px" />
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                
                <tr>
                    <td align="center" class="button_container">
                        <? 
						//print_r ($id_up_all);
                            if($app_cause!='')
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
	
	$sql_req="select approval_cause from fabric_booking_approval_cause where entry_form=13 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0";				
	$nameArray_req=sql_select($sql_req);
	foreach($nameArray_req as $row)
	{
		$unappv_req=$row[approval_cause];
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


if ($action=="save_update_delete_appv_cause")
{
	//$approval_id
	$approval_type=str_replace("'","",$appv_type);
	
	if($approval_type==0)
	{
			
   		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 

		if ($operation==0 || $operation==1)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=13 and mst_id=$wo_id and approved_by=$app_user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=13 and booking_id=$wo_id and user_id=$app_user_id and approval_type=$appv_type");
			
			//echo "10**". $approved_no_history .'==='.$approved_no_cause;die;
			
			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;
				
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
				//echo "10**=====". $rID; die;
				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",13,".$app_user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,1);
				
				
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
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
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
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
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=13 and booking_id=$wo_id and user_id=$app_user_id and approval_type=$appv_type");
				
				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*13*".$app_user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
				
				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",1);
				
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
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=13 and mst_id=$wo_id and approved_by=$app_user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=13 and booking_id=$wo_id and user_id=$app_user_id and approval_type=$appv_type");
				
				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",13,".$app_user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,1);
					//echo $rID; die;
					
					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
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
							echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
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
					
					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=13 and booking_id=$wo_id and user_id=$app_user_id and approval_type=$appv_type");
					
					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*13*".$app_user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
					
					 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",1);
					
					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT"); 
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id); 
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
							echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id); 
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
		
			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=13 and user_id=$app_user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
			
			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=13 and mst_id=$wo_id and approved_by=$app_user_id");
			
			if($unapproved_cause_id=="")
			{
			
				//echo "shajjad_".$unapproved_cause_id; die;
		
				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;
			
				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_history_id,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",13,".$app_user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$approval_id.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$rID=sql_insert("fabric_booking_approval_cause",$field_array,$data_array,1);
				//echo $rID; die;
		
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT");
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
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
						echo "0**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id);
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
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=13 and user_id=$app_user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
				
				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_history_id*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*13*".$app_user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$approval_id."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
				
				 $rID=sql_update("fabric_booking_approval_cause",$field_array,$data_array,"id","".$id_cause."",1);
				
				if($db_type==0)
				{
					if($rID )
					{
						mysql_query("COMMIT"); 
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id); 
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
						echo "1**".$rID."**".str_replace("'","",$wo_id)."**".str_replace("'","",$appv_cause)."**".str_replace("'","",$appv_type)."**".str_replace("'","",$app_user_id); 
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
		
		//$to='shajjad@logicsoftbd.com';
		
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


if($action=="last_version_check_p20")//Print B20
{
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_fabric_natu=str_replace("'","",$cbo_fabric_natu);
	$cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$show_yarn_rate=str_replace("'","",$show_yarn_rate);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='$cbo_company_name'",'master_tble_id','image_location');
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_name_arr=return_library_array( "select id, brand_name from lib_buyer_brand ",'id','brand_name');
	$user_name_arr=return_library_array( "select id, user_full_name from user_passwd ",'id','user_full_name');

	//$location_name_arr=return_library_array( "select id,location_name from lib_location",'id','location_name');
	
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//$po_qnty_tot1=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	//wo_pre_cost_fabric_cost_dtls
	$pro_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	?>
	<style type="text/css">
		@media print {
		    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
		}
	</style>
	<div style="width:1330px" align="center">
    <?php
    	$lip_yarn_count=return_library_array( "select id,fabric_composition_id from lib_yarn_count_determina_mst where  status_active=1", "id", "fabric_composition_id");
		$fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where  status_active=1", "id", "fabric_composition_name");

		
		$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		list($nameArray_approved_row) = $nameArray_approved;
		$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_date_row) = $nameArray_approved_date;
		$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
		list($nameArray_approved_comments_row) = $nameArray_approved_comments;

		$max_approve_date_data = sql_select("select min(b.approved_date) as approved_date,max(b.approved_date) as last_approve_date,max(b.un_approved_date) as un_approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=7");
		$first_approve_date='';
		$last_approve_date='';
		$un_approved_date='';
		if(count($max_approve_date_data))
		{
			$last_approve_date=$max_approve_date_data[0][csf('last_approve_date')];
			$first_approve_date=$max_approve_date_data[0][csf('approved_date')];
			$un_approved_date=$max_approve_date_data[0][csf('un_approved_date')];
		}
		
		  if($txt_job_no!="") $location=return_field_value( "location_name", "wo_po_details_master","job_no='$txt_job_no'");
             else $location="";
		$sql_loc=sql_select("select id,location_name,address from lib_location where company_id=$cbo_company_name");
		foreach($sql_loc as $row)
		{
			$location_name_arr[$row[csf('id')]]= $row[csf('location_name')];
			$location_address_arr[$row[csf('id')]]= $row[csf('address')];
		}		
		$yes_no_sql=sql_select("select job_no,cons_process from  wo_pre_cost_fab_conv_cost_dtls where job_no='$txt_job_no'  and status_active=1 and is_deleted=0  order by id");
		
		$peach='';
		$brush='';
		$fab_wash='';
		// foreach ($yes_no_sql as $row) {
			
		// 	if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'peach') !== false)
        // 	{
		// 	    if(!empty($peach))
		// 	    {
		// 	    	$peach.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
		// 	    }
		// 	    else{
		// 	    	$peach.=$conversion_cost_head_array[$row[csf('cons_process')]];
		// 	    }
			   
		// 	}
		// 	if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'brush') !== false)
        // 	{
		// 	    if(!empty($brush))
		// 	    {
		// 	    	$brush.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
		// 	    }
		// 	    else{
		// 	    	$brush.=$conversion_cost_head_array[$row[csf('cons_process')]];
		// 	    }

		// 	}
		// 	if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'wash') !== false)
        // 	{
		// 	    if(!empty($fab_wash))
		// 	    {
		// 	    	$fab_wash.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
		// 	    }
		// 	    else{
		// 	    	$fab_wash.=$conversion_cost_head_array[$row[csf('cons_process')]];
		// 	    }

		// 	}			

		// }
		// $emb_print=sql_select("select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where  job_no='$txt_job_no' and status_active=1 and is_deleted=0 order by id");

		// $emb_print_data=array();
		// $type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);
		// foreach ($emb_print as $row) 
		// {
		// 	$emb_print_data[$row[csf('job_no')]][$row[csf('emb_name')]].=$type_array[$row[csf("emb_name")]][$row[csf('emb_type')]].",";
		// }



		$emb_print=sql_select("select a.id, a.job_no, a.emb_name, a.emb_type from wo_pre_cost_embe_cost_dtls a left join wo_pre_cost_embe_cost_dtls_his b on a.id=b.PRE_COST_EMBE_COST_DTLS_ID where  b.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and a.cons_dzn_gmts>0  and b.approved_no=$revised_no and a.emb_name in (1,2,3) order by a.id");
		
		//echo "select id, job_no, emb_name, emb_type from wo_pre_cost_embe_cost_dtls where emb_name!=3 and job_no='$txt_job_no' and status_active=1 and is_deleted=0 order by id";


		$emb_print_data=array();
		$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type,99=>$blank_array);
		
		foreach ($emb_print as $row) 
		{
			$emb_print_data[$row[csf('job_no')]][$row[csf('emb_name')]].=$type_array[$row[csf("emb_name")]][$row[csf('emb_type')]].",";
		}
		

		// echo "<pre>";
		// print_r();

		$nameArray=sql_select( "select a.booking_no, a.booking_date, a.supplier_id, a.currency_id, a.exchange_rate, a.attention, a.delivery_date, a.po_break_down_id, a.colar_excess_percent, a.cuff_excess_percent, a.delivery_date, a.is_apply_last_update, a.fabric_source, a.rmg_process_breakdown, a.insert_date, a.update_date, a.tagged_booking_no, a.uom, a.pay_mode, a.booking_percent, b.job_no, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.total_set_qnty, b.style_description, b.season_buyer_wise as season, b.product_dept, b.product_code, b.pro_sub_dep, b.dealing_marchant,b.factory_marchant, b.order_repeat_no, b.repeat_job_no, a.fabric_composition, a.remarks,a.sustainability_standard,b.brand_id,a.quality_level,a.fab_material,a.requisition_no,b.qlty_label,b.packing,b.job_no from wo_booking_mst a, wo_po_details_master b where a.job_no=b.job_no and a.booking_no=$txt_booking_no");
		
		$po_id_all=$nameArray[0][csf('po_break_down_id')];
		$job_no_str=$nameArray[0][csf('job_no')];
		$booking_uom=$nameArray[0][csf('uom')];
		$bookingup_date=$nameArray[0][csf('update_date')];
		$bookingins_date=$nameArray[0][csf('insert_date')];
		$delivery_date=$nameArray[0][csf('delivery_date')];
		$requisition_no=$nameArray[0][csf('requisition_no')];
		
		$job_yes_no=sql_select("select id, job_id,job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, ws_id, aop, aopseq,bush,bushseq,peach,peachseq,yd,ydseq from wo_po_details_mas_set_details where job_no='$job_no_str'");

		$po_shipment_date=sql_select("select  MIN(pub_shipment_date) as min_shipment_date,max(pub_shipment_date) as max_shipment_date from wo_po_break_down where id in(".$po_id_all.") order by shipment_date asc ");
         $min_shipment_date='';
         $max_shipment_date='';
         foreach ($po_shipment_date as $row) {
         	 $min_shipment_date=$row[csf('min_shipment_date')];
         	 $max_shipment_date=$row[csf('max_shipment_date')];
         	 break;
         }

        $po_running_cancel= sql_select("select case when status_active=1 then  PO_NUMBER end as running_po, case when status_active>1 then po_number end as cancel_po,po_quantity from wo_po_break_down  where id in(".$po_id_all.") order by shipment_date asc ");
        $running_po='';
        $cancel_po='';
        $running_po_qnty=0;
        foreach ($po_running_cancel as $row) {
        	if(!empty($row[csf('running_po')]))
        	{
        		if(!empty($running_po))
        		{
        			$running_po.=",".$row[csf('running_po')];
        		}
        		else{
        			$running_po.=$row[csf('running_po')];
        		}
        		 $running_po_qnty+=$row[csf('po_quantity')];
        	}
        	if(!empty($row[csf('cancel_po')]))
        	{
        		if(!empty($cancel_po))
        		{
        			$cancel_po.=",".$row[csf('cancel_po')];
        		}
        		else{
        			$cancel_po.=$row[csf('cancel_po')];
        		}
        	}
        }
        $stype_color_res=sql_select("select  stripe_type from wo_pre_stripe_color where job_no='$txt_job_no' and status_active=1 and is_deleted=0 group by stripe_type");
        $stype_color='';
        foreach ($stype_color_res as $val) {
        	if(!empty($stype_color))
        	{
        		$stype_color.=", ".$stripe_type_arr[$val[csf('stripe_type')]];
        	}
        	else
        	{
        		$stype_color=$stripe_type_arr[$val[csf('stripe_type')]];
        	}
        	
        }
        $yd_aop_sql=sql_select("select a.id, b.job_no,  b.color_type_id from wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls b on a.id=b.PRE_COST_FABRIC_COST_DTLS_ID
		where a.job_no='$txt_job_no'  and b.approved_no=$revised_no and a.status_active=1 and a.is_deleted=0 order by a.id asc");

        $yd='';
        $aop='';
		foreach ($yes_no_sql as $row) {
			
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'peach') !== false)
        	{
			    if(!empty($peach))
			    {
			    	$peach.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$peach.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			   
			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'brush') !== false || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('Brushing at Main Fabric Booking') || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('Brushing at Main Fabric Booking') || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('Brush [With Finish]') || strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'brushing') !== false)
        	{
			    if(!empty($brush))
			    {
			    	$brush.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$brush.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }

			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'wash') !== false || strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'washing') !== false)
        	{
			    if(!empty($fab_wash))
			    {
			    	$fab_wash.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$fab_wash.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }

			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'y/d') !== false || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('YD at Main Fabric Booking') || strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'yarn dyeing') !== false)
        	{
			    if(!empty($yd))
			    {
			    	$yd.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$yd.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }

			}
			if (strpos(strtolower($conversion_cost_head_array[$row[csf('cons_process')]]), 'aop') !== false || strtolower($conversion_cost_head_array[$row[csf('cons_process')]])==strtolower('All Over Printing'))
        	{
			    if(!empty($aop))
			    {
			    	$aop.=",".$conversion_cost_head_array[$row[csf('cons_process')]];
			    }
			    else{
			    	$aop.=$conversion_cost_head_array[$row[csf('cons_process')]];
			    }

			}

			

		}
       

		?>	
											<!--    Header Company Information         -->
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black; font-family:Arial Narrow;" >
            <tr>
                <td width="200" style="font-size:28px"><img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' /></td>
                <td width="1250">
                    <table width="100%" cellpadding="0" cellspacing="0"  >
                        <tr>
                            <td align="center" style="font-size:28px;"> <?php echo $company_library[$cbo_company_name]; ?></td>
                            
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px">
                           <?
                                                      
                            echo $location_address_arr[$location];
                           ?> 
                            </td>
                            
                        </tr>
                        <tr>
                            <td align="center" style="font-size:24px">
                            <span style="float:center;"><b><strong> <font style="color:black">Main Fabric Booking </font></strong></b></span> 
                               
                            </td>
                            
                        </tr>
						
                        <tr>
                            <td align="center" style="font-size:20px">
							<?
							if(str_replace("'","",$id_approved_id) ==1){ ?>
                            <span style="font-size:20px; float:center;"><strong> <font style="color:green"> <? echo "[Approved]"; ?> </font></strong></span> 
                               <?}else{?>

								<span style="font-size:20px; float:center;"><strong> <font style="color:red"><? echo "[Not Approved]"; ?> </font></strong></span> 

							   <?}?>
                            </td>
                            
                        </tr>
                    </table>
                </td>
                <td width="200">
                	<table style="border:1px solid black; font-family:Arial Narrow;" width="100%">
                		<tr>
                			<td><b>Min. Ship Date:</b></td>
                			<td><b><?php echo  date('d-m-Y',strtotime($min_shipment_date));?></b></td>
                		</tr>
                		<tr>
                			<td><b>Max. Ship Date:</b></td>
                			<td><b><?php echo date('d-m-Y',strtotime($max_shipment_date));?></b></td>
                		</tr>
                	</table>
                	<br>
                	<table style="border:1px solid black; font-family:Arial Narrow;font-size: 10px;" width="100%">
                		<tr>
                			<td>Printing Date :</td>
                			<td><?php echo  date('d-m-Y');?></td>
                		</tr>
                		<tr>
                			<td>Printing Time:</td>
                			<td><?php echo  date('h:i:sa');?></td>
                		</tr>
                		<tr>
                			<td>User Name:</td>
                			<td><?php echo $user_name_arr[$user_id];?></td>
                		</tr>
                		<tr>
                			<?php 

                				function get_client_ip() {
								    $ipaddress = '';
								    if (getenv('HTTP_CLIENT_IP'))
								        $ipaddress = getenv('HTTP_CLIENT_IP');
								    else if(getenv('HTTP_X_FORWARDED_FOR'))
								        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
								    else if(getenv('HTTP_X_FORWARDED'))
								        $ipaddress = getenv('HTTP_X_FORWARDED');
								    else if(getenv('HTTP_FORWARDED_FOR'))
								        $ipaddress = getenv('HTTP_FORWARDED_FOR');
								    else if(getenv('HTTP_FORWARDED'))
								       $ipaddress = getenv('HTTP_FORWARDED');
								    else if(getenv('REMOTE_ADDR'))
								        $ipaddress = getenv('REMOTE_ADDR');
								    else
								        $ipaddress = 'UNKNOWN';
								    return $ipaddress;
								}

                			 ?>
                			<td>IP Address:</td>
                			<td><?php if(empty($user_ip)){echo get_client_ip();} echo $user_ip;?></td>
                		</tr>
                	</table>
                </td>
            </tr>
        </table>
		<?
		
       
		
        $job_no=trim($txt_job_no,"'"); $total_set_qnty=0; $colar_excess_percent=0; $cuff_excess_percent=0; $rmg_process_breakdown=0; $booking_percent=0; $booking_po_id='';
		
        
		
		if($db_type==0)
        {
            $date_dif_cond="DATEDIFF(pub_shipment_date,po_received_date)";
            $group_concat_all="group_concat(grouping) as grouping, group_concat(file_no) as file_no";
        }
        else
        {
            $date_dif_cond="(pub_shipment_date-po_received_date)";
            $group_concat_all=" listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping,
                                listagg(cast(file_no as varchar2(4000)),',') within group (order by file_no) as file_no  ";
        }
        $po_number_arr=array(); $po_ship_date_arr=array(); $shipment_date=""; $po_no=""; $po_received_date=""; $shiping_status="";
        $po_sql=sql_select("select id, po_number, pub_shipment_date, MIN(pub_shipment_date) as mpub_shipment_date, MIN(po_received_date) as po_received_date, MIN(insert_date) as insert_date, plan_cut, po_quantity, shiping_status, $date_dif_cond as date_diff,min(factory_received_date) as factory_received_date, $group_concat_all from wo_po_break_down where id in(".$po_id_all.") group by id, po_number, pub_shipment_date, plan_cut, po_quantity, shiping_status, po_received_date");
      
        $to_ship=0;
        $fp_ship=0;
        $f_ship=0;

        foreach($po_sql as $row)
        {
            $po_qnty_tot+=$row[csf('plan_cut')];
            $po_qnty_tot1+=$row[csf('po_quantity')];
            $po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_ship_date_arr[$row[csf('id')]]=$row[csf('pub_shipment_date')];
            $po_num_arr[$row[csf('id')]]=$row[csf('po_number')];
            $po_no.=$row[csf('po_number')].", ";
            $shipment_date.=change_date_format($row[csf('mpub_shipment_date')],'dd-mm-yyyy','-').", ";
            $lead_time.=$row[csf('date_diff')].",";
            $po_received_date=change_date_format($row[csf('po_received_date')],'dd-mm-yyyy','-');
            $factory_received_date=change_date_format($row[csf('factory_received_date')],'dd-mm-yyyy','-');
            $grouping.=$row[csf('grouping')].",";
            $file_no.=$row[csf('file_no')].",";
			
			$daysInHand.=(datediff('d',date('d-m-Y',time()),$row[csf('mpub_shipment_date')])-1).",";
			
			if($bookingup_date=="" || $bookingup_date=="0000-00-00 00:00:00")
			{
				$booking_date=$bookingins_date;
			}
			$WOPreparedAfter.=(datediff('d',$row[csf('insert_date')],$booking_date)-1).",";

			if($row[csf('shiping_status')]==1) {
				$shiping_status.= "FP".",";
				$to_ship++;
				$fp_ship++;
			}
			else if($row[csf('shiping_status')]==2){
				$shiping_status.= "PD".",";
				$to_ship++;
			} 
			else if($row[csf('shiping_status')]==3){
				$shiping_status.= "FS".",";
				$to_ship++;
				$f_ship++;
			} 

			
        }

        if($to_ship==$f_ship)
        {
        	$shiping_status= "Full shipped";
        }
        else if($to_ship==$fp_ship)
        {
        	$shiping_status= "Full Pending";
        }
        else{
        	$shiping_status= "Partial Delivery";
        }
       
		
		$po_no=implode(",",array_filter(array_unique(explode(",",$po_no))));
		$shipment_date=implode(",",array_filter(array_unique(explode(",",$shipment_date))));
		$lead_time=implode(",",array_filter(array_unique(explode(",",$lead_time))));
		$po_received_date=implode(",",array_filter(array_unique(explode(",",$po_received_date))));
		$factory_received_date=implode(",",array_filter(array_unique(explode(",",$factory_received_date))));
		$grouping=implode(",",array_filter(array_unique(explode(",",$grouping))));
		$file_no=implode(",",array_filter(array_unique(explode(",",$file_no))));
		
		$daysInHand=implode(",",array_filter(array_unique(explode(",",$daysInHand))));
		$WOPreparedAfter=implode(",",array_filter(array_unique(explode(",",$WOPreparedAfter))));
		$shiping_status=implode(",",array_filter(array_unique(explode(",",$shiping_status))));
		
        foreach ($nameArray as $result)
        {
            $total_set_qnty=$result[csf('total_set_qnty')];
            $colar_excess_percent=$result[csf('colar_excess_percent')];
            $cuff_excess_percent=$result[csf('cuff_excess_percent')];
            $rmg_process_breakdown=$result[csf('rmg_process_breakdown')];
            
            $booking_percent=$result[csf('booking_percent')];
			$booking_po_id=$result[csf('po_break_down_id')];

			?>
			<table width="100%" class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all"  style="font-size:18px; font-family:Arial Narrow;" >
				<tr>
					<td colspan="2" rowspan="6" width="210">
						<? $nameArray_imge =sql_select("SELECT image_location FROM common_photo_library where master_tble_id='$job_no' and file_type=1"); ?>
                        <div id="div_size_color_matrix" style="float:left;">
                            <fieldset id="" width="210">
                                <legend>Image </legend>
                                <table width="208">
                                    <tr>
										<?
                                        $img_counter = 0;
                                        foreach($nameArray_imge as $result_imge)
                                        {
											if($path=="") $path='../../';
											?>
											<td><img src="<? echo $path.$result_imge[csf('image_location')]; ?>" width="200" height="200" border="2" /></td>
											<?
											$img_counter++;
                                        }
                                        ?>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
					</td>
					<td width="100"><b>Job No</b></td>
					<?php 
						
					 	$revised_no=$nameArray_approved_row[csf('approved_no')]-1;
						if($revised_no<0)
						{
							$revised_no=0;
						}

					 ?>
					<td width="140"> <span style="font-size:18px"><b style="float:left;font-size:18px"><? echo trim($txt_job_no,"'");if(!empty($revised_no)){ ?>&nbsp;<span style="color: red;">/&nbsp;<? echo $revised_no; }?></span></b> </span> </td>
					<td colspan="7" width="760">
						<b><?php if(!empty($result[csf('remarks')])){ ?><span style="font-size: 25px;">&#8592;</span><? echo $result[csf('remarks')]; } ?></b>
					</td>
					
				</tr>
				<?php

					$order_yes_no=sql_select("Select embelishment ,embro ,wash  ,spworks ,gmtsdying , ws_id , aop , aopseq , bush ,  peach, yd    from wo_po_details_mas_set_details where job_no='$txt_job_no' order by id");			

				?>
				<tr>
					<td width="100" style="font-size:16px;"><b>Style</b></td>
					<td width="110"style="font-size:16px;" >&nbsp;<? echo $result[csf('style_ref_no')]; ?></td>
					
					<td width="100" style="font-size:16px;"><b>Dept. (Prod Code)</b></td>
					<td width="140"style="font-size:16px;" >&nbsp;<? echo $product_dept[$result[csf('product_dept')]]; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} ?></td>
					
					
					<td width="100"><b>YD</b></td>
					<td width="110"><?php echo (!empty($yd) || $order_yes_no[0][csf('yd')]==1)  ? 'Yes' : 'No' ;?></td>
					<td width="100"><?php 	echo $yd; ?></td>
					<td width="110"><b>Fac. Order Received Date</b></td>
					<td width="100"><?php echo $factory_received_date; ?></td>
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></span></td>
					
					<td width="100"><b>Sub Dep</b></td>
					<td width="140"><? if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
					<td width="100"><b>AOP</b></td>
					
					<td width="110"><?php echo   ($order_yes_no[0][csf('aop')]==1 || (!empty($aop))) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?=$aop;?></td>
					<td width="110"><b>Booking Start<br>Appoval Date</b></td>
					<td width="100">
						<?php if(!empty($first_approve_date)){ echo date('d-m-Y',strtotime($first_approve_date)); } ?><br>
						<?php if(!empty($last_approve_date)){ echo date('d-m-Y',strtotime($last_approve_date)); } ?>
							
						</td>
					
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Garments Item</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"> <?
                        $gmts_item_name="";
                        $gmts_item=explode(',',$result[csf('gmts_item_id')]);
                        for($g=0;$g<=count($gmts_item); $g++)
                        {
                            $gmts_item_name.= $garments_item[$gmts_item[$g]].",";
                        }
                        echo rtrim($gmts_item_name,',');
                        ?></span></td>
					
					<td width="100"><b>Season</b></td>
					<td width="140"><? echo $season_name_arr[$result[csf('season')]]; ?></td>
					<td width="100"><b>Peach</b></td>
					
				<td width="110"><?php echo  ($job_yes_no[0][csf('peach')]==1 || (!empty($peach))) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?=$peach;?></td>
					<td width="110"><b>Approved Status</b></td>
					<td width="100">
					<? if(str_replace("'","",$id_approved_id) ==1){ ?>
					<b style="color:green"><?	echo "Yes";?></b>
					<?}else{?><b style="color:red"><?	echo "No";?></b><?}; ?></td>
					
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Sample Req. No</b></span></td>
					<td width="110">&nbsp;<span style="font-size:18px"><?php echo $requisition_no; ?></span></td>
					
					<td width="100"><b>Brand</b></td>
					<td width="140"><?php echo $brand_name_arr[$result[csf('brand_id')]]; ?></td>
					<td width="100"><b>Brushing</b></td>
					
					<td width="110"><?php echo  ($job_yes_no[0][csf('bush')]==1 ||  (!empty($brush))) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?=$brush;?></td>
					<td width="110"><b>Booking Date</b></td>
					<td width="100"> <?
                        if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
                        {
                        }
                        $booking_date=$result[csf('insert_date')];
                        echo change_date_format($booking_date,'dd-mm-yyyy','-','');
                        ?>
                        	
                    </td>
					
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Booking No</b></span></td>
					<td width="110"><span style="font-size:18px"><b><? echo $result[csf('booking_no')];?></b><? echo "<br>(".$fabric_source[$result[csf('fabric_source')]].")"?></span></td>
					
					<td width="100"><b>Order Repeat No</b></td>
					<td width="140"><? echo  $result[csf('order_repeat_no')];?></td>

					<td width="100"><b>Print / Type</b></td>
					<td width="110"><?php echo   ($order_yes_no[0][csf('embelishment')]==1 || (!empty($emb_print_data[$txt_job_no][1]))) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?php echo  !empty($emb_print_data[$txt_job_no][1]) ? chop($emb_print_data[$txt_job_no][1],",") : '' ;?></td>
					
					<td width="110"><b>Delivery Date</b></td>
					<td width="100"> 
                        <? echo change_date_format($delivery_date); ?>
                        	
                    </td>
					
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Running PO No</b></span></td>
					<td width="350" colspan="3" style="word-break: break-all;">
						<p style="font-size:12px;width: 450px;word-break: break-all;" ><?
					 echo $running_po; ?></p>
					</td>
					
					<td width="100"><b>Order Repeat Job No</b></td>
					<td width="110"><? echo $result[csf('repeat_job_no')];?></td>

					<td width="100"><b>EMB / Type</b></td>
					
					<td width="110"><?php echo  ($order_yes_no[0][csf('embro')]==1 || (!empty($emb_print_data[$txt_job_no][2]))) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?php echo  !empty($emb_print_data[$txt_job_no][2]) ? chop($emb_print_data[$txt_job_no][2],",") : '' ;?></td>

					<td width="110"><b>Amendment Date</b></td>
					<td width="100"> 
                        <? if(!empty($un_approved_date)){echo change_date_format($un_approved_date);} ?>
                        	
                    </td>
					
					
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Cancelled PO</b></span></td>
					<td width="350" colspan="3" ><p style="font-size:12px;width: 450px;word-break: break-all;"><?
					 echo $cancel_po; ?></p></td>		

					<?php $fab_material=array(1=>"Organic",2=>"BCI"); ?>
					<td width="100"><b>Fab. Material</b> </td>
					<td width="110"><?php echo $fab_material[$result[csf('fab_material')]]; ?></td>


					<td width="100"><b>GMT Wash</b></td>
					
					<td width="110"><?php echo  ($order_yes_no[0][csf('wash')]==1 || (!empty($emb_print_data[$txt_job_no][3]))) ? 'Yes' : 'No' ;?></td>
					<td width="100"><?php echo  !empty($emb_print_data[$txt_job_no][3]) ? chop($emb_print_data[$txt_job_no][3],",") : '' ;?></td>

					<td width="110"><b>Dealing Merchandiser</b></td>
					<td width="100"> 
                        <? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?>
                        	
                    </td>
					
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>GMT/ Style Description</b></span></td>
					<td width="350" colspan="3"><span style="font-size:18px"><? echo $result[csf('style_description')]; ?></span></td>
					
					
					 
					<td width="100"><b>Sustainability Standard</b></td>
					<td width="110"><?php echo $sustainability_standard[$result[csf('sustainability_standard')]]; ?></td>
					

					<td width="100"><b>Fab Wash</b></td>
					<td width="110"><?php echo !empty($fab_wash)? 'Yes' : 'No'; ?></td>


					<td width="100"><?=$fab_wash?></td>
					
					<td width="110"><b>Factory Merchandiser</b></td>
					<td width="100"> 
                        
                         <? echo $marchentrArr[$result[csf('factory_marchant')]]; ?>
                        	
                    </td>
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Fabric Description</b></span></td>
					<td width="350" colspan="3"><span style="font-size:18px">

						<? 

							$sql_fab="SELECT e.lib_yarn_count_deter_id AS determin_id, e.construction 
							FROM wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h e on a.id=e.PRE_COST_FABRIC_COST_DTLS_ID, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d 
							WHERE a.job_id = b.job_id AND a.id = b.pre_cost_fabric_cost_dtls_id AND a.id = d.pre_cost_fabric_cost_dtls_id AND b.po_break_down_id = d.po_break_down_id AND 
							b.color_size_table_id = d.color_size_table_id AND b.pre_cost_fabric_cost_dtls_id = d.pre_cost_fabric_cost_dtls_id 
							 AND d.booking_no = $txt_booking_no  and e.approved_no=$revised_no AND a.status_active = 1 AND d.status_active = 1 AND d.is_deleted = 0 and a.body_part_id in (1,20) group by e.lib_yarn_count_deter_id , e.construction 
							";
							
							//echo $sql_fab;
							$res_fab=sql_select($sql_fab);
							$des='';
							foreach ($res_fab as $row) 
							{

								if(!empty($des))
								{
									$des."***";
								}
								$des.=$row[csf('construction')] . " ". $fabric_composition[$lip_yarn_count[$row[csf('determin_id')]]];
							}

							echo implode(",", array_unique(explode("***", $des)));

						?>
							
						</span></td>
					
					
					<td width="100"><b>Order Nature</b></td>
					<td width="110"><?php echo $fbooking_order_nature[$result[csf('quality_level')]] ?></td>
					<td width="100"><b>Running Order Qty</b></td>
					<?php $order_uom_res=sql_select( "select a.order_uom from wo_po_details_master a where a.status_active=1 and a.is_deleted=0  and a.job_no='$txt_job_no' ");
						$order_uom='';
						if(count($order_uom_res))
						{
							$order_uom=$unit_of_measurement[$order_uom_res[0][csf('order_uom')]];
						}
					 ?>
					<td width="210" colspan="2" align="center"><b> <?php echo number_format($running_po_qnty,0); ?>&nbsp;(<?=$order_uom;?>)</b></td>
					
					
					<td width="110"><b>Shipment Status</b></td>
					<td width="100"> 
                        <? echo rtrim($shiping_status,','); ?>
                        	
                    </td>
				</tr>
				<tr>
					<td width="100"><span style="font-size:18px"><b>Attention</b></span></td>
					<td  width="350" colspan="3"><span style="font-size:18px"><? echo $result[csf('attention')]?></span></td>
					
					<td width="100"><b>Quality Label</b></td>
					<td width="110"><?php echo $quality_label[$result[csf('qlty_label')]] ?></td>
					<td width="100"><b>Packing</b></td>
					
					<td width="420" colspan="4" align="center"><b> <?php echo $packing[$result[csf('packing')]]; ?></b></td>					
				</tr>				
			</table>
			<br>
			<?
		}		
		if($cbo_fabric_source==1)
		{
			$nameArray_size=sql_select( "select size_number_id,min(id) as id, min(size_order) as size_order from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by size_number_id order by size_order");
			?>
			<table width="100%" style="font-family:Arial Narrow;font-size:18px" >
                <tr>
                    <td width="900">
                        <div id="div_size_color_matrix" style="float:left; max-width:1000;">
                            <fieldset id="div_size_color_matrix" style="max-width:1000;">
                                <legend>Size and Color Breakdown</legend>
                                <table  class="rpt_table"  border="1" align="left" cellpadding="0" width="1000" cellspacing="0" rules="all" >
                                    <tr>
                                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                                        <?
                                        foreach($nameArray_size  as $result_size)
                                        {
											?>
                                        	<td align="center" style="border:1px solid black"><strong><?=$size_library[$result_size[csf('size_number_id')]];?></strong></td>
                                        <? } ?>
                                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Order Qty(Pcs)</strong></td>
                                        <td style="border:1px solid black; width:80px" align="center"><strong> Excess %</strong></td>
                                        <td style="border:1px solid black; width:130px" align="center"><strong> Total Plan Cut Qty(Pcs)</strong></td>
                                    </tr>
                                    <?
                                    $color_size_order_qnty_array=array(); $color_size_qnty_array=array(); $size_tatal=array(); $size_tatal_order=array();
                                    for($c=0;$c<count($gmts_item); $c++)
                                    {
										$item_size_tatal=array(); $item_size_tatal_order=array(); $item_grand_total=0; $item_grand_total_order=0;
										$nameArray_color=sql_select( "select  color_number_id,min(id) as id,min(color_order) as color_order from wo_po_color_size_breakdown where  item_number_id=$gmts_item[$c] and po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id  order by color_order");
										?>
										<tr>
											<td style="border:1px solid black; text-align:center;" colspan="<? echo count($nameArray_size)+3;?>"><strong><? echo $garments_item[$gmts_item[$c]];?></strong></td>
										</tr>
										<?
										foreach($nameArray_color as $result_color)
										{
											?>
											<tr>
                                                <td align="center" style="border:1px solid black"><?=$color_library[$result_color[csf('color_number_id')]]; ?></td>
                                                <?
                                                $color_total=0; $color_total_order=0;
                                                foreach($nameArray_size  as $result_size)
                                                {
													$nameArray_color_size_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as  order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$result_color[csf('color_number_id')]."  and item_number_id=$gmts_item[$c] and  status_active=1 and is_deleted =0");
													foreach($nameArray_color_size_qnty as $result_color_size_qnty)
													{
														?>
														<td style="border:1px solid black; text-align:center; font-size:18px;">
														<?
														if($result_color_size_qnty[csf('plan_cut_qnty')]!= "")
														{
															echo number_format($result_color_size_qnty[csf('order_quantity')],0);
															$color_total += $result_color_size_qnty[csf('plan_cut_qnty')] ;
															$color_total_order += $result_color_size_qnty[csf('order_quantity')] ;
															$item_grand_total+=$result_color_size_qnty[csf('plan_cut_qnty')];
															$item_grand_total_order+=$result_color_size_qnty[csf('order_quantity')];
															$grand_total +=$result_color_size_qnty[csf('plan_cut_qnty')];
															$grand_total_order +=$result_color_size_qnty[csf('order_quantity')];
															
															$color_size_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
															$color_size_order_qnty_array[$result_size[csf('size_number_id')]][$result_color[csf('color_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
															if (array_key_exists($result_size[csf('size_number_id')], $size_tatal))
															{
																$size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
																$size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
															}
															else
															{
																$size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
																$size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
															}
															if (array_key_exists($result_size[csf('size_number_id')], $item_size_tatal))
															{
																$item_size_tatal[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('plan_cut_qnty')];
																$item_size_tatal_order[$result_size[csf('size_number_id')]]+=$result_color_size_qnty[csf('order_quantity')];
															}
															else
															{
																$item_size_tatal[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('plan_cut_qnty')];
																$item_size_tatal_order[$result_size[csf('size_number_id')]]=$result_color_size_qnty[csf('order_quantity')];
															}
														}
														else echo "0";
														?>
														</td>
														<?
													}
                                                }
                                                ?>
                                                <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($color_total_order),0); ?></td>
                                                <td style="border:1px solid black; text-align:center; font-size:18px;"><? $excexss_per=($color_total-$color_total_order)/$color_total_order*100; echo number_format($excexss_per,2)." %"; ?></td>
                                                <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($color_total),0); ?></td>
											</tr>
											<?
										}
										?>
										
										<td align="center" style="border:1px solid black"><strong>Sub Total</strong></td>
										<?
										foreach($nameArray_size  as $result_size)
										{
											?>
											<td style="border:1px solid black;  text-align:center; font-size:18px;"><? echo $item_size_tatal_order[$result_size[csf('size_number_id')]];  ?></td>
											<?
										}
										?>
										<td style="border:1px solid black;  text-align:center; font-size:18px;"><?  echo number_format(round($item_grand_total_order),0); ?></td>
										<td style="border:1px solid black;  text-align:center; font-size:18px;"><? $excess_item_gra_tot=($item_grand_total-$item_grand_total_order)/$item_grand_total_order*100; echo number_format($excess_item_gra_tot,2)." %"; ?></td>
										<td style="border:1px solid black;  text-align:center; font-size:18px;"><?  echo number_format(round($item_grand_total),0); ?></td>
										</tr>
										<?
                                    }
                                    ?>
                                    <tr>
                                    	<td style="border:1px solid black; font-size:18px;" align="center" colspan="<? echo count($nameArray_size)+3; ?>"><strong>&nbsp;</strong></td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="border:1px solid black"><strong>Grand Total</strong></td>
                                        <?
                                        foreach($nameArray_size  as $result_size)
                                        {
											?>
											<td style="border:1px solid black; text-align:center; font-size:18px;"><? echo $size_tatal_order[$result_size[csf('size_number_id')]]; ?></td>
											<?
                                        }
                                        ?>
                                        <td style="border:1px solid black; text-align:center; font-size:18px;"><?=number_format(round($grand_total_order),0); ?></td>
                                        <td style="border:1px solid black; text-align:center; font-size:18px;"><? $excess_gra_tot=($grand_total-$grand_total_order)/$grand_total_order*100; echo number_format($excess_gra_tot,2)." %"; ?></td>
                                        <td style="border:1px solid black; text-align:center; font-size:18px;"><?  echo number_format(round($grand_total),0); ?></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
                    </td>
                    <td width="130" valign="top" align="left">
                        <div id="div_size_color_matrix" style="float:left;">
							
                        </div>
                    </td>
                    <td width="200" valign="top" align="right">
						
                        <div id="div_size_color_matrix" style="float:right;font-size:18px;font-family:Arial Narrow;">
                           <? $rmg_process_breakdown_arr=explode('_',$rmg_process_breakdown); ?>
                            <fieldset id="" >
                                <legend>RMG Process Loss % </legend>
                                <table width="180" class="rpt_table" border="1" rules="all">
									<?
                                    if(number_format($rmg_process_breakdown_arr[8],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Cut Panel rejection <!-- Extra Cutting % breack Down 8--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[8],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[2],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Chest Printing <!-- Printing % breack Down 2--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[2],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[10],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Neck/Sleeve Printing <!-- New breack Down 10--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[10],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[1],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Embroidery   <!-- Embroidery  % breack Down 1--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[1],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[4],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Sewing /Input<!-- Sewing % breack Down 4--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[4],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[3],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Garments Wash <!-- Washing %breack Down 3--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[3],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[15],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Gmts Finishing <!-- Washing %breack Down 3--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[15],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[11],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Others <!-- New breack Down 11--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[11],2); ?></td>
										</tr>
										<?
                                    }
                                    $gmts_pro_sub_tot=$rmg_process_breakdown_arr[8]+$rmg_process_breakdown_arr[2]+$rmg_process_breakdown_arr[10]+$rmg_process_breakdown_arr[1]+$rmg_process_breakdown_arr[4]+$rmg_process_breakdown_arr[3]+$rmg_process_breakdown_arr[11]+$rmg_process_breakdown_arr[15];
                                    if($gmts_pro_sub_tot>0)
                                    {
										?>
										<tr>
                                            <td width="130">Sub Total <!-- New breack Down 11--></td>
                                            <td align="right"><? echo number_format($gmts_pro_sub_tot,2); ?></td>
										</tr>
										<?
                                    }
                                    ?>
                                </table>
                            </fieldset>
                            <fieldset id="" >
                                <legend>Fabric Process Loss % </legend>
                                <table width="180" class="rpt_table" border="1" rules="all" style="font-family:Arial Narrow;">
                                    <?
                                    if(number_format($rmg_process_breakdown_arr[6],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Knitting  <!--  Knitting % breack Down 6--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[6],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[12],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Yarn Dyeing  <!--  New breack Down 12--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[12],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[5],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Dyeing & Finishing  <!-- Finishing % breack Down 5--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[5],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[13],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130"> All Over Print <!-- new  breack Down 13--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[13],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[14],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Lay Wash (Fabric) <!-- new  breack Down 14--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[14],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[7],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Dying   <!-- breack Down 7--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[7],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[0],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Cutting (Fabric) <!-- Cutting % breack Down 0--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[0],2); ?></td>
										</tr>
										<?
                                    }
                                    if(number_format($rmg_process_breakdown_arr[9],2)>0)
                                    {
										?>
										<tr>
                                            <td width="130">Others  <!-- Others% breack Down 9--></td>
                                            <td align="right"><? echo number_format($rmg_process_breakdown_arr[9],2); ?></td>
										</tr>
										<?
                                    }

                                    $fab_proce_sub_tot=$rmg_process_breakdown_arr[6]+$rmg_process_breakdown_arr[12]+$rmg_process_breakdown_arr[5]+$rmg_process_breakdown_arr[13]+$rmg_process_breakdown_arr[14]+$rmg_process_breakdown_arr[7]+$rmg_process_breakdown_arr[0]+$rmg_process_breakdown_arr[9];
                                    if(fab_proce_sub_tot>0)
                                    {
										?>
										<tr>
                                            <td width="130">Sub Total  <!-- Others% breack Down 9--></td>
                                            <td align="right"><? echo number_format($fab_proce_sub_tot,2); ?></td>
										</tr>
										<?
                                    }
                                    if($gmts_pro_sub_tot+$fab_proce_sub_tot>0)
                                    {
										?>
										<tr>
                                            <td width="130">Grand Total  <!-- Others% breack Down 9--></td>
                                            <td align="right"><? echo number_format($gmts_pro_sub_tot+$fab_proce_sub_tot,2); ?></td>
										</tr>
										<?
                                    }
                                    ?>
                                </table>
                            </fieldset>
                        </div>
                    </td>
                </tr>
			</table>
			<?
		}
		// if($cbo_fabric_source==1) end
		
	  	?>
		<br/>
		<br>
      	<!--  Here will be the main portion  -->
		<?
        $costing_per=""; $costing_per_qnty=0;
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

        $process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");
		//$s_length=return_field_value( "stitch_length", "fabric_mapping","mst_id=$determin_id");;
		$s_lengthArr=return_library_array( "select mst_id, stitch_length from fabric_mapping",'mst_id','stitch_length');		
		if($cbo_fabric_source==1)
		{
			$fb_desc_sq="SELECT min(a.id) as fabric_cost_dtls_id, e.lib_yarn_count_deter_id as determin_id, e.item_number_id, e.body_part_id, e.color_type_id, e.construction, e.composition, e.gsm_weight,	min(e.width_dia_type) as width_dia_type,  b.dia_width, avg(b.cons) as cons, avg(b.process_loss_percent) as process_loss_percent, avg(b.requirment) as requirment 
			FROM wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h e on a.id=e.PRE_COST_FABRIC_COST_DTLS_ID, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d WHERE a.job_id=b.job_id and a.id=b.pre_cost_fabric_cost_dtls_id  and a.id=d.pre_cost_fabric_cost_dtls_id  and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id 
			and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and a.status_active=1 and d.status_active=1 and d.is_deleted=0 and d.booking_no=$txt_booking_no  and e.approved_no=$revised_no group by e.body_part_id,e.lib_yarn_count_deter_id, e.color_type_id, e.item_number_id, e.construction, e.composition, e.gsm_weight,  b.dia_width order by fabric_cost_dtls_id, e.body_part_id, b.dia_width";

 
			$nameArray_fabric_description= sql_select($fb_desc_sq);
			?>
			<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" style="font-family:Arial Narrow;font-size:18px;" >
                <tr align="center">
                    <th colspan="3" align="left">Body Part</th>
                    <?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('body_part_id')] == "") echo "<td  colspan='2'>&nbsp</td>";
						else echo "<td colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
                    }
                    ?>
                    <td rowspan="10" width="50"><p>Total  Finish Fabric (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
                    <td rowspan="10" width="50"><p>Total Grey Fabric (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
                    <td rowspan="10" width="50"><p>Process Loss % </p></td>
                </tr>
                <tr align="center">
                    <th colspan="3" align="left">Color Type</th>
                    <?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
						else echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center">
                    <th colspan="3" align="left">Fabric Construction</th>
                    <?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if($result_fabric_description[csf('construction')]== "") echo "<td  colspan='2'>&nbsp</td>";
						else echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";
                    }
                    ?>
                </tr>
                 <tr align="center">
                    <th colspan="3" align="left">Fabric Composition</th>
                    <?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if($result_fabric_description[csf('determin_id')]== "") echo "<td  colspan='2'>&nbsp</td>";
						else echo "<td  colspan='2'>". $fabric_composition[$lip_yarn_count[$result_fabric_description[csf('determin_id')]]]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center">
                    <th colspan="3" align="left">Yarn Composition</th>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('composition')] == "") echo "<td colspan='2' >&nbsp</td>";
						else echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center">
                	<th colspan="3" align="left">GSM</th>
					<?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('gsm_weight')] == "") echo "<td colspan='2'>&nbsp</td>";
						else echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
                    }
                    ?>
                </tr>
               
                <tr align="center">
                    <th colspan="3" align="left">Dia/Width (Inch)</th>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
						else echo "<td colspan='2' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center">
                    <th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('requirment')] == "")   echo "<td colspan='2'>&nbsp</td>";
						else echo "<td colspan='2' align='center'>A Fin: ".number_format($result_fabric_description[csf('cons')],4).", Grey: ".number_format($result_fabric_description[csf('requirment')],4)."</td>";
                    }
                    ?>
                </tr>
                <tr>
                	<th colspan="<? echo  count($nameArray_fabric_description)*2+3; ?>" align="left" style="height:30px">&nbsp;</th>
                </tr>
                <tr>
                    <th width="120" align="left">Fabric Color</th>
                    <th width="120" align="left">GMT Color</th>
                    <th width="120" align="left">Lab Dip No</th>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
                   		echo "<th width='50'>Finish</th><th width='50' >Grey</th>";
                    }
                    ?>
                </tr>
                <?
                $color_wise_wo_sql = "SELECT e.item_number_id, e.body_part_id, e.color_type_id, e.construction, e.composition, e.gsm_weight, e.lib_yarn_count_deter_id, b.dia_width, b.remarks, d.fabric_color_id, d.fin_fab_qnty as fin_fab_qnty, d.grey_fab_qnty as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h e on a.id=e.PRE_COST_FABRIC_COST_DTLS_ID join wo_pre_cos_fab_co_avg_con_dtls b on  a.id=b.pre_cost_fabric_cost_dtls_id join  wo_po_color_size_breakdown c on c.id=b.color_size_table_id join wo_booking_dtls d on 	b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id  WHERE  d.booking_no =$txt_booking_no and e.APPROVED_NO=$revised_no  a.uom=12 and d.status_active=1 and  d.is_deleted=0 ";
		
                
                $color_wise_wo_sql_res=sql_select($color_wise_wo_sql); $fin_grey_qty_arr=array(); $fin_grey_color_qty_arr=array();
                foreach($color_wise_wo_sql_res as $row)
                {
					$fin_grey_key = $row[csf('item_number_id')].'**'.$row[csf('body_part_id')].'**'.$row[csf('color_type_id')].'**'.$row[csf('construction')].'**'.$row[csf('composition')].'**'.$row[csf('gsm_weight')].'**'.$row[csf('lib_yarn_count_deter_id')].'**'.$row[csf('dia_width')];
					$fin_grey_color_key = $row[csf('item_number_id')].'**'.$row[csf('body_part_id')].'**'.$row[csf('color_type_id')].'**'.$row[csf('construction')].'**'.$row[csf('composition')].'**'.$row[csf('gsm_weight')].'**'.$row[csf('lib_yarn_count_deter_id')].'**'.$row[csf('dia_width')].'**'.$row[csf('fabric_color_id')];
					$fin_grey_qty_arr[$fin_grey_key]['fin'] += $row[csf('fin_fab_qnty')];
					$fin_grey_qty_arr[$fin_grey_key]['grey'] += $row[csf('grey_fab_qnty')];
					$fin_grey_color_qty_arr[$fin_grey_color_key]['fin'] +=$row[csf('fin_fab_qnty')];
					$fin_grey_color_qty_arr[$fin_grey_color_key]['grey'] +=$row[csf('grey_fab_qnty')];
                }
                unset($color_wise_wo_sql_res);
                
                $gmt_color_library=array();
                $gmt_color_data=sql_select(" e.id,b.gmts_color_id, b.contrast_color_id FROM wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h e on a.id=e.PRE_COST_FABRIC_COST_DTLS_ID,wo_pre_cos_fab_co_color_dtls b WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.fabric_source =$cbo_fabric_source and a.job_no ='$job_no' and e.APPROVED_NO=$revised_no and a.status_active=1 and b.status_active=1 order by a.id");
                foreach( $gmt_color_data as $gmt_color_row)
                {
                	$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
                }
                
                $lab_dip_no_arr=array();
                $lab_dip_no_sql=sql_select("select lapdip_no, color_name_id from wo_po_lapdip_approval_info where job_no_mst='$job_no' and status_active=1 and is_deleted=0 and approval_status=3");
                foreach($lab_dip_no_sql as $row)
                {
                	$lab_dip_no_arr[$row[csf('color_name_id')]]=$row[csf('lapdip_no')];
                }
                unset($lab_dip_no_sql);
                
                $grand_total_fin_fab_qnty=0; $grand_total_grey_fab_qnty=0; $grand_totalcons_per_finish=0; $grand_totalcons_per_grey=0;
                $color_wise_wo_sql=sql_select("select fabric_color_id FROM wo_booking_dtls WHERE booking_no =$txt_booking_no and status_active=1 and is_deleted=0 group by fabric_color_id");
                foreach($color_wise_wo_sql as $color_wise_wo_result)
                {
					?>
					<tr>
                        <td width="120" align="left"><? echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]]; ?></td>
                        <td><? echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]); ?></td>
                        <td width="120" align="left">
							<?
                            $lapdip_no=""; $lapdip_no=$lab_dip_no_arr[$color_wise_wo_result[csf('fabric_color_id')]];
                            if($lapdip_no=="") echo "&nbsp;"; else echo $lapdip_no;
                            ?>
                        </td>
                        <?
                        $total_fin_fab_qnty=0; $total_grey_fab_qnty=0;
                        foreach($nameArray_fabric_description as $result_fabric_description)
                        {
							$color_wo_fin_qnty=0; $color_wo_grey_qnty=0;
							$fin_gray_color = $result_fabric_description[csf('item_number_id')].'**'.$result_fabric_description[csf('body_part_id')].'**'.$result_fabric_description[csf('color_type_id')].'**'.$result_fabric_description[csf('construction')].'**'.$result_fabric_description[csf('composition')].'**'.$result_fabric_description[csf('gsm_weight')].'**'.$result_fabric_description[csf('determin_id')].'**'.$result_fabric_description[csf('dia_width')].'**'.$color_wise_wo_result[csf('fabric_color_id')];
							$color_wo_fin_qnty=$fin_grey_color_qty_arr[$fin_gray_color]['fin'];
							
							$color_wo_grey_qnty=$fin_grey_color_qty_arr[$fin_gray_color]['grey'];
							?>
							<td width='50' align='center' style="font-size:18px;">
								<?
                                if($color_wo_fin_qnty!="")
                                {
                                    echo number_format($color_wo_fin_qnty,2) ;
                                    $total_fin_fab_qnty+=$color_wo_fin_qnty;
                                }
                                ?>
							</td>
							<td width='50' align='center' style="font-size:18px;">
								<?
                                if($color_wo_grey_qnty!="")
                                {
									echo number_format($color_wo_grey_qnty,2);
									$total_grey_fab_qnty+=$color_wo_grey_qnty;
                                }
                                ?>
							</td>
							<?
                        }
                        ?>
                        <td align="center" style="font-size:18px;"><? echo number_format($total_fin_fab_qnty,2); $grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
                        <td align="center" style="font-size:18px;"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
                        <td align="center" style="font-size:18px;">
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
                    <th width="120" align="left">&nbsp;</th>
                    <td width="120" align="left">&nbsp;</td>
                    <td width="120" align="left"><strong>Total</strong></td>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						$wo_fin_qnty=0; $wo_grey_qnty=0;
						$fin_key = $result_fabric_description[csf('item_number_id')].'**'.$result_fabric_description[csf('body_part_id')].'**'.$result_fabric_description[csf('color_type_id')].'**'.$result_fabric_description[csf('construction')].'**'.$result_fabric_description[csf('composition')].'**'.$result_fabric_description[csf('gsm_weight')].'**'.$result_fabric_description[csf('determin_id')].'**'.$result_fabric_description[csf('dia_width')];
						
						$wo_fin_qnty=$fin_grey_qty_arr[$fin_key]['fin'];
						$wo_grey_qnty=$fin_grey_qty_arr[$fin_key]['grey'];
						?>
						<td width='50' align='center' style="font-size:18px;"><?  echo number_format($wo_fin_qnty,2) ;?></td><td width='50' align='center' style="font-size:18px;" > <? echo number_format($wo_grey_qnty,2);?></td>
						<?
                    }
                    ?>
                    <td align="center" style="font-size:18px;"><? echo number_format($grand_total_fin_fab_qnty,2);?></td>
                    <td align="center" style="font-size:18px;"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
                    <td align="center" style="font-size:18px;">
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
                <tr style="font-weight:bold">
                    <th width="120" align="left">&nbsp;</th>
                    <td width="120" align="left">&nbsp;</td>
                    <td width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
						<?
                        foreach($nameArray_fabric_description as $result_fabric_description)
                        {
							?>
							<td width='50' align='center' style="font-size:18px;"></td>
                            <td width='50' align='right' > </td>
							<?
                        }
                        ?>
                    <td align="center" style="font-size:18px;">
						<?
                        //echo $grand_total_fin_fab_qnty;
                        echo number_format(($grand_total_fin_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4);
                        $grand_total_fin_fab_qnty_dzn=number_format(($grand_total_fin_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4);
                        ?>
                    </td>
                    <td align="center" style="font-size:18px;"><? echo number_format(($grand_total_grey_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4);$grand_total_grey_fab_qnty_dzn=number_format(($grand_total_grey_fab_qnty/$grand_total)*($total_set_qnty*$costing_per_qnty),4)?></td>
                    <td align="center" style="font-size:18px;">
						<?
                        if($process_loss_method==1)
                        {
                        	$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_fin_fab_qnty_dzn)*100;
                        }
                        
                        if($process_loss_method==2)
                        {
                        	$totalprocess_percent_dzn=(($grand_total_grey_fab_qnty_dzn-$grand_total_fin_fab_qnty_dzn)/$grand_total_grey_fab_qnty_dzn)*100;
                        }
                        echo number_format($totalprocess_percent_dzn,2);
                        ?>
                    </td>
                </tr>
			</table>
			<?
		}
		//echo "kausar"; die;
		if($cbo_fabric_source==2)
		{
			$nameArray_fabric_description= sql_select("select min(e.id) as fabric_cost_dtls_id, e.lib_yarn_count_deter_id as determin_id, e.body_part_id, e.color_type_id, e.construction, e.composition,e.gsm_weight,min(a.width_dia_type) as width_dia_type, b.dia_width, avg(e.avg_finish_cons) as cons, avg(b.process_loss_percent) as process_loss_percent, avg(e.avg_cons) as requirment 
			FROM wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h e on a.id=e.PRE_COST_FABRIC_COST_DTLS_ID, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d WHERE a.job_id=b.job_id and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_id=a.job_id and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id	 and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and e.APPROVED_NO=$revised_no and d.status_active=1 and d.is_deleted=0 
			group by e.body_part_id, e.lib_yarn_count_deter_id, e.color_type_id, e.construction, e.composition, e.gsm_weight, b.dia_width order by fabric_cost_dtls_id, e.body_part_id, b.dia_width");


			?>
			<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" style="font-family:Arial Narrow;font-size:18px;" >
                <tr align="center">
                    <th colspan="3" align="left">Body Part</th>
                    <?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
						else echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
                    }
                    ?>
                    <td rowspan="10" width="50"><p>Total Fabric (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
                    <td rowspan="10" width="50"><p>Avg Rate (<? echo $unit_of_measurement[$booking_uom];?>)</p></td>
                    <td rowspan="10" width="50"><p>Amount </p></td>
                </tr>
                <tr align="center"><th colspan="3" align="left">Color Type</th>
					<?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
                        if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
                        else echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center"><th colspan="3" align="left">Fabric Construction</th>
					<?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
						else  echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center"><th colspan="3" align="left">Fabric Composition</th>
					<?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('determin_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
						else  echo "<td  colspan='3'>". $fabric_composition[$lip_yarn_count[$result_fabric_description[csf('determin_id')]]]."</td>";

						
                    }
                    ?>
                </tr>
                <tr align="center"><th colspan="3" align="left">Yarn Composition</th>
					<?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
						else echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center"><th  colspan="3" align="left">GSM</th>
					<?
                    foreach($nameArray_fabric_description  as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
						else  echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
                    }
                    ?>
                </tr>
                
                <tr align="center"><th colspan="3" align="left">Dia/Width (Inch)</th>
					<?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
						else echo "<td colspan='3' align='center'>".$result_fabric_description[csf('dia_width')].",".$fabric_typee[$result_fabric_description[csf('width_dia_type')]]."</td>";
                    }
                    ?>
                </tr>
                <tr align="center"><th   colspan="3" align="left">Consumption For <? echo $costing_per; ?></th>
					<?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
						if( $result_fabric_description[csf('requirment')] == "")   echo "<td colspan='3'>&nbsp</td>";
						else echo "<td colspan='3' align='center'>Fin: ".number_format($result_fabric_description[csf('cons')],2).", Grey: ".number_format($result_fabric_description[csf('requirment')],2)."</td>";
                    }
                    ?>
                </tr>
                <tr>
                	<th colspan="<? echo  count($nameArray_fabric_description)*3+3; ?>" align="left" style="height:30px">&nbsp;</th>
                </tr>
                <tr>
                    <th width="120" align="left">Fabric Color</th>
                    <th width="120" align="left">GMT Color</th>
                    <th width="120" align="left">Lab Dip No</th>
                    <?
                    foreach($nameArray_fabric_description as $result_fabric_description)
                    {
                    	echo "<th width='50'>Fab. Qty</th><th width='50' >Rate</th><th width='50' >Amount</th>";
                    }
                    ?>
                </tr>
                <?
                $gmt_color_library=array();
                $gmt_color_data=sql_select("select e.id as fab_id,b.gmts_color_id, b.contrast_color_id FROM wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h e on a.id=e.PRE_COST_FABRIC_COST_DTLS_ID,wo_pre_cos_fab_co_color_dtls b  WHERE a.id=b.pre_cost_fabric_cost_dtls_id and a.fab_nature_id=$cbo_fabric_natu and a.fabric_source =$cbo_fabric_source and a.job_no ='$job_no' and e.APPROVED_NO=$revised_no and a.status_active=1 and b.status_active=1 order by e.id"); 

                foreach( $gmt_color_data as $gmt_color_row)
                {
                	$gmt_color_library[$gmt_color_row[csf("contrast_color_id")]][$gmt_color_row[csf("gmts_color_id")]]=$color_library[$gmt_color_row[csf("gmts_color_id")]];
                }
                
                $grand_total_fin_fab_qnty=0; $grand_total_amount=0;
                
                $color_wise_wo_sql=sql_select("select fabric_color_id FROM wo_booking_dtls WHERE booking_no =$txt_booking_no and status_active=1 and is_deleted=0 group by fabric_color_id");
                foreach($color_wise_wo_sql as $color_wise_wo_result)
                {
                ?>
                <tr>
                
                <td  width="120" align="left">
                <?
                echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
                
                
                ?>
                </td>
                <td>
                <?
                echo implode(",",$gmt_color_library[$color_wise_wo_result[csf('fabric_color_id')]]);
                ?>
                </td>
                <td  width="120" align="left">
                <?
                $lapdip_no="";
                $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$job_no."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."");
                if($lapdip_no=="") echo "&nbsp;"; echo $lapdip_no;
                ?>
                </td>
                <?
                $total_fin_fab_qnty=0;
                $total_amount=0;
                
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                if($db_type==0)
                {
                $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty, avg(d.rate) as rate  from wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h e on a.id=e.pre_cost_fabric_cost_dtls_id, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
                WHERE a.job_id=b.job_id and
                a.id=b.pre_cost_fabric_cost_dtls_id and
                c.job_id=a.job_id and
                c.id=b.color_size_table_id and
                b.po_break_down_id=d.po_break_down_id and
                b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
                d.booking_no =$txt_booking_no and 
				e.APPROVED_NO=$revised_no and
                a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
                a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
                a.construction='".$result_fabric_description[csf('construction')]."' and
                a.composition='".$result_fabric_description[csf('composition')]."' and
                a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
                a.lib_yarn_count_deter_id='".$result_fabric_description[csf('determin_id')]."' and
                b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
                d.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
                d.status_active=1 and
                d.is_deleted=0
                ");
                }
                if($db_type==2)
                {
                
                $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty,avg(d.rate) as rate from wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h e on a.id=e.pre_cost_fabric_cost_dtls_id, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
                where a.job_id=b.job_id and
                a.id=b.pre_cost_fabric_cost_dtls_id and
                c.job_id=a.job_id and
                c.id=b.color_size_table_id and
                b.po_break_down_id=d.po_break_down_id and
                b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
                d.booking_no =$txt_booking_no and
				e.APPROVED_NO=$revised_no and
                a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
                a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
                a.construction='".$result_fabric_description[csf('construction')]."' and
                a.composition='".$result_fabric_description[csf('composition')]."' and
                a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
                a.lib_yarn_count_deter_id='".$result_fabric_description[csf('determin_id')]."' and
                b.dia_width='".$result_fabric_description[csf('dia_width')]."' and

                nvl(d.fabric_color_id,0)=nvl('".$color_wise_wo_result[csf('fabric_color_id')]."',0) and
                d.status_active=1 and
                d.is_deleted=0
                ");
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
                if($color_wise_wo_result_qnty[csf('rate')]!="")
                {
                echo number_format($color_wise_wo_result_qnty[csf('rate')],2);
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
                <!--<td  width="120" align="left">&nbsp;</td>-->
                <th  width="120" align="left">&nbsp;</th>
                <td  width="120" align="left">&nbsp;</td>
                <td  width="120" align="left"><strong>Total</strong></td>
                <?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty  from wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h e on a.id=e.pre_cost_fabric_cost_dtls_id, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
                WHERE a.job_id=b.job_id and
                a.id=b.pre_cost_fabric_cost_dtls_id and
                c.job_id=a.job_id and
                c.id=b.color_size_table_id and
                b.po_break_down_id=d.po_break_down_id and
                b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
                d.booking_no =$txt_booking_no and
				e.APPROVED_NO=$revised_no and
                a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
                a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
                a.construction='".$result_fabric_description[csf('construction')]."' and
                a.composition='".$result_fabric_description[csf('composition')]."' and
                a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
                a.lib_yarn_count_deter_id='".$result_fabric_description[csf('determin_id')]."' and
                b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
                d.status_active=1 and
                d.is_deleted=0
                ");
                list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
                ?>
                <td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2) ;?></td>
                <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
                <td width='50' align='right' > <? //echo number_format($color_wise_wo_result_qnty['grey_fab_qnty'],2);?></td>
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
                <tr style="font-weight:bold">
                <!--<td  width="120" align="left">&nbsp;</td>-->
                <th  width="120" align="left">&nbsp;</th>
                <td  width="120" align="left">&nbsp;</td>
                <td  width="120" align="left"><strong>Consumption For <? echo $costing_per; ?></strong></td>
                <?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                $color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty from wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h e on a.id=e.pre_cost_fabric_cost_dtls_id, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
                WHERE a.job_id=b.job_id and
                a.id=b.pre_cost_fabric_cost_dtls_id and
                c.job_id=a.job_id and
                c.id=b.color_size_table_id and
                b.po_break_down_id=d.po_break_down_id and
                b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
                d.booking_no =$txt_booking_no and
				e.APPROVED_NO=$revised_no and
                a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
                a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
                a.construction='".$result_fabric_description[csf('construction')]."' and
                a.composition='".$result_fabric_description[csf('composition')]."' and
                a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
                a.lib_yarn_count_deter_id='".$result_fabric_description[csf('determin_id')]."' and
                b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
                d.status_active=1 and
                d.is_deleted=0
                ");
                list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
                
                ?>
                <td width='50' align='right'><?  //echo number_format(($color_wise_wo_result_qnty['fin_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4) ;?></td>
                <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
                <td width='50' align='right' > <? //echo number_format(($color_wise_wo_result_qnty['grey_fab_qnty']/$grand_total)*($total_set_qnty*$costing_per_qnty),4);?></td>
                <?
                }
                ?>
                <td align="right">
                <?
                $consumption_per_unit_fab=($grand_total_fin_fab_qnty/$po_qnty_tot)*($costing_per_qnty);
                echo number_format($consumption_per_unit_fab,4);
                ?>
                </td>
                <td align="right">
                <?
                $consumption_per_unit_amuont=($grand_total_amount/$po_qnty_tot)*($costing_per_qnty);
                echo number_format(($consumption_per_unit_amuont/$consumption_per_unit_fab),2);
                ?>
                </td>
                <td align="right">
                <?
                echo number_format($consumption_per_unit_amuont,2);
                ?>
                </td>
                </tr>
			</table>
			<?
		}
		?>
        <br/>
        <?
		if($cbo_fabric_source==1)
		{
			$lab_dip_color_arr=array();
			$lab_dip_color_sql=sql_select("select pre_cost_fabric_cost_dtls_id, gmts_color_id, contrast_color_id from wo_pre_cos_fab_co_color_dtls where job_no='$job_no'");
			foreach($lab_dip_color_sql as $row)
			{
				$lab_dip_color_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]][$row[csf('gmts_color_id')]]=$row[csf('contrast_color_id')];
			}
			
			

			$collar_cuff_percent_arr=array(); $collar_cuff_body_arr=array(); $collar_cuff_color_arr=array(); $collar_cuff_size_arr=array(); $collar_cuff_item_size_arr=array(); $color_size_sensitive_arr=array();

			$collar_cuff_sql="select a.id, f.item_number_id, f.color_size_sensitive, f.color_break_down, b.color_number_id, b.gmts_sizes, b.item_size, c.size_number_id, d.colar_cuff_per, e.body_part_full_name, e.body_part_type	from wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h f on a.id=f.pre_cost_fabric_cost_dtls_id, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c, wo_booking_dtls d, lib_body_part e	WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and
			 f.APPROVED_NO=$revised_no and and a.body_part_id=e.id and e.body_part_type in (40,50) and c.id=d.color_size_table_id and d.color_size_table_id=b.color_size_table_id  and d.po_break_down_id=c.po_break_down_id and
			 a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 order by  c.size_order";
			//echo $collar_cuff_sql;
			$collar_cuff_sql_res=sql_select($collar_cuff_sql);
			
			$itemIdArr=array();

			foreach($collar_cuff_sql_res as $collar_cuff_row)
			{
				$collar_cuff_percent_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('gmts_sizes')]]=$collar_cuff_row[csf('colar_cuff_per')];
				$collar_cuff_body_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]]=$collar_cuff_row[csf('body_part_full_name')];
				$collar_cuff_size_arr[$collar_cuff_row[csf('body_part_type')]][$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]]=$collar_cuff_row[csf('size_number_id')];
				if(!empty($collar_cuff_row[csf('item_size')]))
				{
					$collar_cuff_item_size_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('size_number_id')]][$collar_cuff_row[csf('item_size')]]=$collar_cuff_row[csf('item_size')];
				}
				
				$color_size_sensitive_arr[$collar_cuff_row[csf('body_part_full_name')]][$collar_cuff_row[csf('id')]][$collar_cuff_row[csf('color_number_id')]][$collar_cuff_row[csf('color_size_sensitive')]]=$collar_cuff_row[csf('color_break_down')];
				
				$itemIdArr[$collar_cuff_row[csf('body_part_type')]].=$collar_cuff_row[csf('item_number_id')].',';
			}
			//print_r($collar_cuff_percent_arr[40]) ;
			unset($collar_cuff_sql_res);
			//$count_collar_cuff=count($collar_cuff_size_arr);
			
			$order_plan_qty_arr=array();
			$color_wise_wo_sql_qnty=sql_select( "select item_number_id, color_number_id, size_number_id, sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in ($booking_po_id) and status_active=1 and is_deleted =0  group by item_number_id, color_number_id, size_number_id");//and item_number_id in (".implode(",",$itemIdArr).")
			foreach($color_wise_wo_sql_qnty as $row)
			{
				$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['plan']=$row[csf('plan_cut_qnty')];
				$order_plan_qty_arr[$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['order']=$row[csf('order_quantity')];
			}
			unset($color_wise_wo_sql_qnty);

			
			foreach($collar_cuff_body_arr as $body_type=>$body_name)
			{
				$gmtsItemId=array_filter(array_unique(explode(",",$itemIdArr[$body_type])));
				foreach($body_name as $body_val)
				{
					$count_collar_cuff=count($collar_cuff_size_arr[$body_type][$body_val]);
					$pre_grand_tot_collar=0; $pre_grand_tot_collar_order_qty=0;

					?>
                    <div style="max-height:1330px; overflow:auto; float:left; padding-top:5px; margin-left:5px; margin-bottom:5px; position:relative;font-size:18px;">
					<table width="625" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr>
                        	<td colspan="<? echo $count_collar_cuff+3; ?>" align="center"><b><? echo $body_val; ?> - Color Size Brakedown in Pcs.</b></td>
                        </tr>
                        <tr>
                            <td width="100">Size</td>
								<?
                                foreach($collar_cuff_size_arr[$body_type][$body_val]  as $size_number_id)
                                {
									?>
									<td align="center" style="border:1px solid black"><strong><? echo $size_library[$size_number_id];?></strong></td>
									<?
                                }
                                ?>
                            <td width="60" rowspan="2" align="center"><strong>Total</strong></td>
                            <td rowspan="2" align="center"><strong>Extra %</strong></td>
                        </tr>
                        <tr>
                            <td style="font-size:12px"><? echo $body_val; ?> Size</td>
                            <?
                            foreach($collar_cuff_item_size_arr[$body_val]  as $size_number_id=>$size_number)
                            {
								if(count($size_number)>0)
								{
									 foreach($size_number  as $item_size=>$val)
									 {
										?>
										<td align="center" style="border:1px solid black"><strong><? echo $item_size;?></strong></td>
										<?
									 }
								}
								else
								{
									?>
									<td align="center" style="border:1px solid black"><strong>&nbsp;</strong></td>
									<?
								}
                            }
                            ?>
                        </tr>
                            <?

                            $pre_size_total_arr=array();
                            foreach($color_size_sensitive_arr[$body_val] as $pre_cost_id=>$pre_cost_data)
                            {
								foreach($pre_cost_data as $color_number_id=>$color_number_data)
								{
									foreach($color_number_data as $color_size_sensitive=>$color_break_down)
									{
										$pre_color_total_collar=0;
										$pre_color_total_collar_order_qnty=0;
										$process_loss_method=$process_loss_method;
										$constrast_color_arr=array();
										if($color_size_sensitive==3)
										{
											$constrast_color=explode('__',$color_break_down);
											for($i=0;$i<count($constrast_color);$i++)
											{
												$constrast_color2=explode('_',$constrast_color[$i]);
												$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
											}
										}
										?>
										<tr>
											<td>
												<?
                                                if( $color_size_sensitive==3)
                                                {
                                                    echo strtoupper ($constrast_color_arr[$color_number_id]) ;
                                                    $lab_dip_color_id=$lab_dip_color_arr[$pre_cost_id][$color_number_id];
                                                }
                                                else
                                                {
                                                    echo $color_library[$color_number_id];
                                                    $lab_dip_color_id=$color_number_id;
                                                }
                                                ?>
											</td>
											<?
											foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
											{
												?>
												<td align="center" style="border:1px solid black">
													<? $plan_cut=0; $collerqty=0; $collar_ex_per=0;
													$plan_cut=0;
													foreach($gmtsItemId as $giid)
													{
														if($body_type==50) $plan_cut+=($order_plan_qty_arr[$giid][$color_number_id][$size_number_id]['plan'])*2;
														else $plan_cut+=$order_plan_qty_arr[$giid][$color_number_id][$size_number_id]['plan'];
													}
													
                                                    //$ord_qty=$order_plan_qty_arr[$color_number_id][$size_number_id]['order'];

                                                    $collar_ex_per=$collar_cuff_percent_arr[$body_type][$body_val][$color_number_id][$size_number_id];
                                                    // echo $collar_ex_per.'=';

												    if($body_type==50) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$cuff_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                                    else if($body_type==40) { if($collar_ex_per==0 || $collar_ex_per=="") $collar_ex_per=$colar_excess_percent; else $collar_ex_per=$collar_ex_per; }
                                                    $colar_excess_per=number_format(($plan_cut*$collar_ex_per)/100,6,".",",");
                                                    $collerqty=($plan_cut+$colar_excess_per);

                                                    //$collerqty=number_format(($requirment/$costing_per_qnty)*$plan_cut,2,'.','');

                                                    echo number_format($collerqty);
                                                    $pre_size_total_arr[$size_number_id]+=$collerqty;
                                                    $pre_color_total_collar+=$collerqty;
                                                    $pre_color_total_collar_order_qnty+=$plan_cut;

                                                    //$pre_grand_tot_collar_order_qty+=$plan_cut;
                                                    ?>
												</td>
												<?
											}
											?>

											<td align="center"><? echo number_format($pre_color_total_collar); ?></td>
											<td align="center"><? echo number_format((($pre_color_total_collar-$pre_color_total_collar_order_qnty)/$pre_color_total_collar_order_qnty)*100,2); ?></td>
										</tr>
										<?
										$pre_grand_collar_ex_per+=$collar_ex_per;
										$pre_grand_tot_collar+=$pre_color_total_collar;
										$pre_grand_tot_collar_order_qty+=$pre_color_total_collar_order_qnty;
									}
								}
							}
							?>
                        
                        <tr>
                            <td>Size Total</td>
								<?
                               // foreach($pre_size_total_arr  as $size_qty)
                               // {
                                	foreach($collar_cuff_size_arr[$body_type][$body_val] as $size_number_id)
									{
										$size_qty=$pre_size_total_arr[$size_number_id];
										?>
										<td style="border:1px solid black;  text-align:center"><? echo number_format($size_qty); ?></td>
										<?
									}

                               // }
                                ?>
                            <td style="border:1px solid black; text-align:center"><? echo number_format($pre_grand_tot_collar); ?></td>
                            <td align="center" style="border:1px solid black"><? echo number_format((($pre_grand_tot_collar-$pre_grand_tot_collar_order_qty)/$pre_grand_tot_collar_order_qty)*100,2); ?></td>
                        </tr>
					</table>
                </div>
                <br/>
                <?
            }
        }

        ?>

       		 <table width="98%">
       		 	<tr>
       		 		<td width="45%" style="float: left;">
       		 			<?php 

       		 				$sql_purchase="SELECT a.booking_no, a.uom, sum(b.fin_fab_qnty) as qnty , b.construction, b.copmposition, b.gsm_weight as dia, b.dia_width as gsm from wo_booking_mst a, wo_booking_dtls b where     a.booking_no = b.booking_no and a.fabric_source = 2 and b.job_no = '$txt_job_no'and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and  a.booking_type=1 group by a.booking_no, a.uom, b.construction, b.copmposition, b.dia, b.gsm_weight, b.dia_width"; 
       		 				//echo $sql_purchase;
							$purchase_res=sql_select($sql_purchase);
							?>
							<table  width="98%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

 			                   <thead>
				                   <tr align="center">
				                    	<th colspan="5"><b>Purchased Booking Info</b></th>
				                    </tr>
				                    <tr align="center">
					                    <th>Sl</th>
					                    <th>Booking No</th>
					                    <th>Fabric Data</th>
					                    <th>UOM</th>
					                    <th>Qnty</th>
					                   
				                    </tr>
 			                   </thead>
 			                   <tbody>
 			                   		<?

 			                   			foreach ($purchase_res as  $row) 
 			                   			{
 			                   				?>
 			                   				<tr>
 			                   					<td><?=$p++;?></td>
 			                   					<td><p><?=$row[csf('booking_no')];?></p></td>
 			                   					<td><p><?=$row[csf('construction')] .", ".$row[csf('copmposition')].", ".$row[csf('gsm')].", ".$row[csf('dia')];?></p></td>
 			                   					<td><p><?=$unit_of_measurement[$row[csf('uom')]];?></p></td>
 			                   					<td><p><?=number_format($row[csf('qnty')],2);?></p></td>
 			                   				</tr>
 			                   				<?
 			                   			}


 			                   		?>
 			                   </tbody>
 			                   
 			            </table>

       		 			 
       		 		</td>
       		 		<td width="45%" style="float: right;">
 			       		 <table  width="98%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">

 			                   <tr align="center">
 			                    	<td colspan="10"><b>Dyes To Match</b></td>
 			                    </tr>
 			                    <tr align="center">
 				                    <td>Sl</td>
 				                    <td>Item</td>
 				                    <td>Item Description</td>
 				                    <td>Body Color</td>
 				                    <td>Item Color</td>
 				                    <td>Finish Qty.</td>
 				                    <td>UOM</td>
 			                    </tr>
 			                    <?
 								$lib_item_group_arr=return_library_array( "select item_name, id from lib_item_group where item_category=4 and is_deleted=0  and  status_active=1 order by item_name", "id", "item_name");
 								$sql=sql_select("select id from wo_booking_mst where booking_no=$txt_booking_no");
 								$bookingId=0;
 								foreach($sql as $row){
 									$bookingId= $row[csf('id')];
 								}
 								$co=0;
 								$sql_data=sql_select("select a.fabric_color,a.item_color,a.precost_trim_cost_id,c.trim_group,c.cons_uom,sum(qty) as qty,c.description   
								 from wo_dye_to_match a, wo_pre_cost_trim_cost_dtls b left join wo_pre_cost_trim_cost_dtls_his c on b.id=c.PRE_COST_TRIM_COST_DTLS_ID
								  where a.precost_trim_cost_id=b.id and a.booking_id=$bookingId  and c.APPROVED_NO=$revised_no and a.qty>0 and a.status_active=1 and b.status_active=1  
								 group by a.fabric_color,a.item_color,a.precost_trim_cost_id,c.trim_group,c.cons_uom, c.description order by a.fabric_color");

 								foreach($sql_data  as $row)
 			                    {
 									$co++;
 									?>
 				                    <tr>
 				                    <td><? echo $co; ?></td>
 				                    <td> <? echo $lib_item_group_arr[$row[csf('trim_group')]];?></td>
 				                    <td><p> <? echo $row[csf('description')];?></p></td>
 				                    <td><? echo $color_library[$row[csf('fabric_color')]];?></td>
 				                    <td><? echo $color_library[$row[csf('item_color')]];?></td>
 				                    <td align="right"><? echo $row[csf('qty')];?></td>
 				                    <td><? echo $unit_of_measurement[$row[csf('cons_uom')]];?></td>
 				                    </tr>
 				                    <?
 								}
 								?>
 			            </table>
       		 		</td>
       		 	</tr>
       		 </table>
            <br>

        <?

		 $condition= new condition();
		if(str_replace("'","",$txt_order_no_id) !=''){
			$condition->po_id("in(".str_replace("'","",$txt_order_no_id).")");
		}

		$condition->init();
		$cos_per_arr=$condition->getCostingPerArr();
		$yarn= new yarn($condition);
		$yarn_data_array=$yarn->getCountCompositionPercentTypeColorAndRateWiseYarnQtyAndAmountArray();
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');

		$yarn_sql_array=sql_select("SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, a.rate  from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no  and  a.status_active=1 and a.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one,a.color,a.type_id,a.rate order by id");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="font-family:Arial Narrow;font-size:18px;" >
            <tr>
                <td width="49%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tr align="center">
                    <td colspan="7"><b>Yarn Required Summary (Pre Cost)</b></td>

                    </tr>
                    <tr align="center">
                    <td>Sl</td>
                    <td>Yarn Description</td>
                    <td>Brand</td>
                    <td>Lot</td>
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
					$total_yarn=0;
					foreach($yarn_sql_array  as $row)
                    {

						$i++;
						$rowcons_qnty = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['qty'];
						$rowcons_Amt = $yarn_data_array[$row[csf("count_id")]][$row[csf("copm_one_id")]][$row[csf("percent_one")]][$row[csf("type_id")]][$row[csf("color")]][$row[csf("rate")]]['amount'];


						$rate=$rowcons_Amt/$rowcons_qnty;
						$rowcons_qnty =($rowcons_qnty/100)*$booking_percent;
					?>
                    <tr align="center">
                    <td><? echo $i; ?></td>
                    <td>
					<?
					$yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
					$yarn_des.=$color_library[$row[csf('color')]]." ";
					$yarn_des.=$yarn_type[$row[csf('type_id')]];
					echo $yarn_des;
					?>
                    </td>
                    <td></td>
                    <td></td>
                    <?
					if($show_yarn_rate==1)
					{
					?>
                     <td><? echo number_format($row[csf('rate')],4); ?></td>
                     <?
					}
					 ?>
                    <td><?  echo number_format(($rowcons_qnty/$po_qnty_tot)*$cos_per_arr[$job_no],4);//echo number_format($row[csf('yarn_required')],4); ?></td>

                    <td align="right">
					<? echo number_format($rowcons_qnty,2); $total_yarn+=$rowcons_qnty; ?></td>
                    </tr>
                    <?
					}
					?>
                    <tr align="center">
                    <td>Total</td>
                    <td></td>
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
                    <td></td>
                    <td align="right"><? echo number_format($total_yarn,2); ?></td>
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
                   <table  width="100%"  style="font-size:18px;" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
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
					<font style=" font-size:30px"><b> </b></font>
                    <?
					}
					else
					{
						echo "";
					}
				}
					?>
                </td>
                 <td width="49%" valign="top" align="center">
                	
               	
                </td>
            </tr>
        </table>
        <br/>
        <?
		$sql_embelishment=sql_select("select b.emb_name,b.emb_type,b.cons_dzn_gmts,b.rate,b.amount from wo_pre_cost_embe_cost_dtls a left join wo_pre_cost_embe_cost_dtls_his b on a.id=b.PRE_COST_EMBE_COST_DTLS_ID where a.job_no='$job_no'  and b.APPROVED_NO=$revised_no and a.status_active=1 and 	a.is_deleted=0");
		?>
        <table  width="100%"  border="0" cellpadding="0" cellspacing="0" style="font-family:Arial Narrow;font-size:18px;">
            <tr>
                <td width="49%" valign="top">
                <?
				if(count($sql_embelishment)>0)
				{
				?>
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;font-size:18px;">
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
					$sql_embelishment=sql_select("select b.emb_name,b.emb_type,b.cons_dzn_gmts,b.rate,b.amount from wo_pre_cost_embe_cost_dtls a left join wo_pre_cost_embe_cost_dtls_his b on a.id=b.PRE_COST_EMBE_COST_DTLS_ID where b.job_no='$job_no' and b.APPROVED_NO=$revised_no and b.status_active=1 and b.is_deleted=0");
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
					echo $emblishment_gmts_type[$row_embelishment[csf('emb_type')]];
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
                				<?
                				$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
									 $user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
									 $user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

								$mst_id=return_field_value("id as mst_id","wo_booking_mst","booking_no=$txt_booking_no","mst_id");
                				 $unapprove_data_array=sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=7  order by b.approved_date,b.approved_by");


                				if(count($unapprove_data_array)>0)
                				{
                				$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=7 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
                					$unapproved_request_arr=array();
                					foreach($sql_unapproved as $rowu)
                					{
                						$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
                					}
                		 		?>
                		       <table  width="100%" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
                		            <thead>
                		            <tr style="border:1px solid black;">
                		                <th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
                		                </tr>
                		                <tr style="border:1px solid black;">
                		                <th width="3%" style="border:1px solid black;">Sl</th>
                						<th width="30%" style="border:1px solid black;">Name</th>
                						<th width="20%" style="border:1px solid black;">Designation</th>
                						<th width="5%" style="border:1px solid black;">Approval Status</th>
                						<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
                						<th width="22%" style="border:1px solid black;"> Date</th>

                		                </tr>
                		            </thead>
                		            <tbody>
                		            <?
                					$i=1;
                					foreach($unapprove_data_array as $row){

                					?>
                		            <tr style="border:1px solid black;">
                		                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
                						<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
                						<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                						<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
                						<td width="20%" style="border:1px solid black;"><? echo '';?></td>
                						<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
                		            </tr>
                		                <?
                						$i++;
                						$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
                						$un_approved_date=$un_approved_date[0];
                						if($db_type==0) //Mysql
                						{
                							if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
                						}
                						else
                						{
                							if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
                						}

                						if($un_approved_date!="")
                						{
                						?>
                					<tr style="border:1px solid black;">
                		                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
                						<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
                						<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
                						<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
                						<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id];?></td>
                						<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
                		              </tr>

                		                <?
                						$i++;
                						}

                					}
                						?>
                		            </tbody>
                		        </table>
                				<?
                				}
                				?>

                </td>
            </tr>
        </table>
        <br/>
        <table  width="100%" style="margin: 0px;padding: 0px;">
        <?php $stripe_color_wise=array(); ?>
       
        <tr>
        	<td width="70%">
        		<table  class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%"   style="font-family:Arial Narrow;font-size:18px;margin: 0px;padding: 0px;" >
        		       
        		        <tr>
        		            <td align="center" colspan="9">  Stripe Details</td>
        		            
    		            </tr>
        		        <?
        				$color_name_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
        				$sql_stripe=("select c.id,c.composition,c.construction,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,sum(b.grey_fab_qnty) as fab_qty,b.dia_width,d.color_number_id as color_number_id,d.id as did,d.stripe_color,d.fabreqtotkg as fabreqtotkg ,d.measurement as measurement ,d.yarn_dyed,d.uom  from wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c,wo_pre_stripe_color d where c.id=b.pre_cost_fabric_cost_dtls_id and c.job_no=b.job_no and d.pre_cost_fabric_cost_dtls_id=c.id and d.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and b.job_no=d.job_no and b.job_no='$job_no'  and d.job_no='$job_no' and b.booking_no=$txt_booking_no  and c.color_type_id in (2,6,33,34) and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and d.is_deleted=0 and d.status_active=1  and 	b.is_deleted=0  group by c.id,c.body_part_id,c.fabric_description,c.gsm_weight,c.color_type_id,d.color_number_id,d.id,d.stripe_color,d.yarn_dyed,d.fabreqtotkg ,d.measurement,d.uom,c.composition,c.construction,b.dia_width order by d.id ");        				
        				$result_data=sql_select($sql_stripe);
        				foreach($result_data as $row)
        				{
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['stripe_color'][$row[csf('did')]]=$row[csf('stripe_color')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['measurement'][$row[csf('did')]]=$row[csf('measurement')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['uom'][$row[csf('did')]]=$row[csf('uom')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['fabreqtotkg'][$row[csf('did')]]=$row[csf('fabreqtotkg')];
        					$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['yarn_dyed'][$row[csf('did')]]=$row[csf('yarn_dyed')];

        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['composition']=$row[csf('composition')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['construction']=$row[csf('construction')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['gsm_weight']=$row[csf('gsm_weight')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['color_type_id']=$row[csf('color_type_id')];
        					$stripe_arr2[$row[csf('body_part_id')]][$row[csf('color_number_id')]]['dia_width']=$row[csf('dia_width')];
        				}
        				?>
        		            <tr>
	        		            <td align="center" width="30"> SL</td>
	        		            <td align="center" width="100"> Body Part</td>
	        		            <td align="center" width="80"> Fabric Color</td>
	        		            <td align="center" width="70"> Fabric Qty(KG)</td>
	        		            <td align="center" width="70"> Stripe Color</td>
	        		            <td align="center" width="70"> Stripe Measurement</td>
	        		            <td align="center" width="70"> Stripe Uom</td>
	        		            <td  align="center" width="70"> Qty.(KG)</td>
	        		            <td  align="center" width="70"> Y/D Req.</td>
        		            </tr>
        		            <?
        					$i=1;$total_fab_qty=0;$total_fabreqtotkg=0;$fab_data_array=array();
        		            foreach($stripe_arr as $body_id=>$body_data)
        		            {
        						foreach($body_data as $color_id=>$color_val)
        						{
        							$rowspan=count($color_val['stripe_color']);
        							$composition=$stripe_arr2[$body_id][$color_id]['composition'];
        							$construction=$stripe_arr2[$body_id][$color_id]['construction'];
        							$gsm_weight=$stripe_arr2[$body_id][$color_id]['gsm_weight'];
        							$color_type_id=$stripe_arr2[$body_id][$color_id]['color_type_id'];
        							$dia_width=$stripe_arr2[$body_id][$color_id]['dia_width'];

        							if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
        							else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";

        							$color_wise_wo_sql_qnty=sql_select("select  sum(d.fin_fab_qnty) as fin_fab_qnty,sum(d.grey_fab_qnty) as grey_fab_qnty FROM wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d
        								WHERE a.job_id=b.job_id and
        								a.id=b.pre_cost_fabric_cost_dtls_id and
        								c.job_no_mst=a.job_no and
        								c.id=b.color_size_table_id and
        								b.po_break_down_id=d.po_break_down_id and
        								b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and
        								d.booking_no =$txt_booking_no and
        								a.body_part_id='".$body_id."' and
        								a.color_type_id='".$color_type_id."' and
        								a.construction='".$construction."' and
        								a.composition='".$composition."' and
        								a.gsm_weight='".$gsm_weight."' and
        								$color_cond and
        								d.status_active=1 and
        								d.is_deleted=0
        								");
        						
        								list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty;
        							$sk=0;
    								foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
        							{
        								
        								?>
	        							<tr>
		        							<?
		        							if($sk==0)
		        							{


			        							$color_qty=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			        							?>
			        							<td rowspan="<? echo $rowspan;?>"> <? echo $i; ?></td>
			        							<td rowspan="<? echo $rowspan;?>"> <? echo $body_part[$body_id]; ?></td>
			        							<td rowspan="<? echo $rowspan;?>"> <? echo $color_name_arr[$color_id]; ?></td>
			        							<td rowspan="<? echo $rowspan;?>" align="right"> <? echo number_format($color_qty,2); ?>&nbsp; </td>
			        							<?
			        							$total_fab_qty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
			        							$i++;
			        						}
		        							$sk=0;
		        							

		        								$measurement=$color_val['measurement'][$strip_color_id];
		        								$uom=$color_val['uom'][$strip_color_id];
		        								$fabreqtotkg=$color_val['fabreqtotkg'][$strip_color_id];
		        								$yarn_dyed=$color_val['yarn_dyed'][$strip_color_id];
		        								
		        								?>
		        							
			        								<td><?  echo  $color_name_arr[$s_color_val]; ?></td>
			        								<td align="right"> <? echo  number_format($measurement,2); ?> &nbsp; </td>
			        		                        <td> <? echo  $unit_of_measurement[$uom]; ?></td>
			        								<td align="right"> <? echo  number_format($fabreqtotkg,2); ?> &nbsp;</td>
			        								<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
		        								
		        								<?
		        								
		        								$sk++;
		        								$total_fabreqtotkg+=$fabreqtotkg;
		        								$stripe_color_wise[$color_name_arr[$s_color_val]]+=$fabreqtotkg;
		        							
		        							
		        							?>
	        							</tr>
	        							<?
	        						}
        						}
        					}
        					?>
	        		            <tfoot>
		        		            <tr>
			        		            <td colspan="3">Total </td>
			        		            <td align="right">  <? echo  number_format($total_fab_qty,2); ?> &nbsp;</td>
			        		            <td></td>
			        		            <td></td>
			        		            <td>   </td>
			        		            <td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> &nbsp;</td>
			        		        </tr>
	        		            </tfoot>
        		            </table>
        	</td>
        	
        	<td width="20%" >
        		        <table  class="rpt_table" border="1" cellpadding="1" cellspacing="1" rules="all" width="100%"   style="font-family:Arial Narrow;font-size:18px;margin: 0px;padding: 0px;"  >
        		       

        		        <tr>
        		            <td align="left" colspan="3"> Stripe  Color wise Summary</td>
        		            
        		           
        		           
    		            </tr>
        		        <?
        				
        				?>
        		            <tr>
	        		            <td width="30"> SL</td>
	        		            
	        		            <td width="70"> Stripe Color</td>
	        		           
	        		            <td  width="70"> Qty.(KG)</td>
	        		           
        		            </tr>
        		            <?

        					$i=1;$total_stripe_qnt=0;        		            
        						foreach($stripe_color_wise as $color=>$val)
        						{
        							
        							
        							?>
        							<tr>
	        							<td> <? echo $i; ?></td>
	        							
	        							<td > <? echo $color; ?></td>
	        							<td align="right"> <?php echo number_format($val,2); ?></td>
	        						</tr>
        							
        							<?
        							$total_stripe_qnt+=$val;
        							
        							$i++;
        						}
        					
        					?>
        		            <tfoot>
        		            <tr>
        		            
        		            <td></td>
        		            <td></td>
        		            
        		            <td align="right"><? echo  number_format($total_stripe_qnt,2); ?> </td>
        		            </tr>
        		            </tfoot>
        		            </table>
        	</td>
        </tr>
         </table >
      
        <br/>
        <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;font-size:18px;">
        <tr align="center">
        <td colspan="10"><b> Comments(Production) </b></td>
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
        $paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");
        $item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$txt_job_no'", "gmts_item_id", "set_item_ratio");

        $nameArray=sql_select("SELECT a.id, a.item_number_id, a.costing_per, b.po_break_down_id, b.color_size_table_id, b.requirment, c.po_number FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_break_down c WHERE a.job_id=b.job_id and a.job_no=c.job_no_mst and a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=c.id and b.po_break_down_id in (".str_replace("'","",$txt_order_no_id).")  $cbo_fabric_natu $cbo_fabric_source_cond and a.status_active=1 and a.is_deleted=0 order by id");
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
        $sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by pub_shipment_date asc");
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
        echo "Extra Booking";
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
         if(count($purchase_res))
        {
        	?>
         <br/>
        <table width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" style="font-family:Arial Narrow;font-size:18px;">
        <tr align="center">
        <td colspan="10"><b> Comments(Purchase)</b></td>
        </tr>
        <tr align="center">
        <td>Sl</td>
        <td>Po NO</td>
        <td>Ship Date</td>
        <td>Pre-Cost Qty</td>
        <td>Purchase<br>Booking Qty</td>
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
        $paln_cut_qnty_array=return_library_array( "select min(id) as id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown  where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by color_number_id,size_number_id,item_number_id,po_break_down_id", "id", "plan_cut_qnty");
        $item_ratio_array=return_library_array( "select gmts_item_id,set_item_ratio from wo_po_details_mas_set_details  where job_no ='$txt_job_no'", "gmts_item_id", "set_item_ratio");

        $nameArray=sql_select("select a.id, a.item_number_id, a.costing_per, b.po_break_down_id, b.color_size_table_id, b.requirment, c.po_number FROM wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b, wo_po_break_down c WHERE a.job_no=b.job_no and a.job_no=c.job_no_mst and a.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=c.id and c.job_no_mst = '$txt_job_no'  and a.fab_nature_id=3 and   a.fabric_source = '2'  and a.status_active=1 and a.is_deleted=0 order by id");

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
        $booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and   a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.job_no = '$txt_job_no' and a.booking_type =1  and c.fabric_source = 2  and a.is_short=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");


        $booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.job_no = '$txt_job_no' and a.booking_type =1 and c.fabric_source=2 and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
        $booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.job_no = '$txt_job_no' and a.booking_type =4 and c.fabric_source=2 and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
        $sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.job_no_mst='$txt_job_no' group by a.po_number order by pub_shipment_date asc");
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
        echo "Extra Booking";
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
       <br>

		<fieldset id="div_size_color_matrix" style="max-width:1000;">
		<?
		//------------------------------ Query for TNA start-----------------------------------
				$po_id_all=str_replace("'","",$txt_order_no_id);
				$po_num_arr=return_library_array("select id,po_number from wo_po_break_down where id in($po_id_all)",'id','po_number');
				$tna_start_sql=sql_select( "SELECT id,po_number_id, (case when task_number=31 then task_start_date else null end) as fab_booking_start_date, (case when task_number=31 then task_finish_date else null end) as fab_booking_end_date, (case when task_number=60 then task_start_date else null end) as knitting_start_date, (case when task_number=60 then task_finish_date else null end) as knitting_end_date, (case when task_number=61 then task_start_date else null end) as dying_start_date, (case when task_number=61 then task_finish_date else null end) as dying_end_date, (case when task_number=64 then task_start_date else null end) as finishing_start_date, (case when task_number=64 then task_finish_date else null end) as finishing_end_date, (case when task_number=84 then task_start_date else null end) as cutting_start_date, (case when task_number=84 then task_finish_date else null end) as cutting_end_date, (case when task_number=86 then task_start_date else null end) as sewing_start_date, (case when task_number=86 then task_finish_date else null end) as sewing_end_date, (case when task_number=110 then task_start_date else null end) as exfact_start_date, (case when task_number=110 then task_finish_date else null end) as exfact_end_date, (case when task_number=47 then task_start_date else null end) as yarn_rec_start_date, (case when task_number=47 then task_finish_date else null end) as yarn_rec_end_date from tna_process_mst where status_active=1 and po_number_id in($po_id_all)"); 
				$tna_fab_start=$tna_knit_start=$tna_dyeing_start=$tna_fin_start=$tna_cut_start=$tna_sewin_start=$tna_exfact_start="";
				$tna_date_task_arr=array();
				foreach($tna_start_sql as $row)
				{
					if($row[csf("fab_booking_start_date")]!="" && $row[csf("fab_booking_start_date")]!="0000-00-00")
					{
						if($tna_fab_start=="")
						{
							$tna_fab_start=$row[csf("fab_booking_start_date")];
						}
					}


					if($row[csf("knitting_start_date")]!="" && $row[csf("knitting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_start_date']=$row[csf("knitting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['knitting_end_date']=$row[csf("knitting_end_date")];
					}
					if($row[csf("dying_start_date")]!="" && $row[csf("dying_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['dying_start_date']=$row[csf("dying_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['dying_end_date']=$row[csf("dying_end_date")];
					}
					if($row[csf("finishing_start_date")]!="" && $row[csf("finishing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_start_date']=$row[csf("finishing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['finishing_end_date']=$row[csf("finishing_end_date")];
					}
					if($row[csf("cutting_start_date")]!="" && $row[csf("cutting_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_start_date']=$row[csf("cutting_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['cutting_end_date']=$row[csf("cutting_end_date")];
					}

					if($row[csf("sewing_start_date")]!="" && $row[csf("sewing_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_start_date']=$row[csf("sewing_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['sewing_end_date']=$row[csf("sewing_end_date")];
					}
					if($row[csf("exfact_start_date")]!="" && $row[csf("exfact_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_start_date']=$row[csf("exfact_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['exfact_end_date']=$row[csf("exfact_end_date")];
					}
					if($row[csf("yarn_rec_start_date")]!="" && $row[csf("yarn_rec_start_date")]!="0000-00-00")
					{
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_start_date']=$row[csf("yarn_rec_start_date")];
						$tna_date_task_arr[$row[csf("po_number_id")]]['yarn_rec_end_date']=$row[csf("yarn_rec_end_date")];
					}
				}

			//------------------------------ Query for TNA end-----------------------------------
		?>
        <legend>TNA Information</legend>
        <!--<span style="font-size:180; font-weight:bold;"></span>-->
        <table width="100%" style="border:1px solid black;font-size:17px; font-family:Arial Narrow;" border="1" cellpadding="2" cellspacing="0" rules="all">
            <tr>
            	<td rowspan="2" align="center" valign="top">SL</td>
            	<td width="180" rowspan="2"  align="center" valign="top"><b>Order No</b></td>
                <td colspan="2" align="center" valign="top"><b>Yarn Receive</b></td>
                <td colspan="2" align="center" valign="top"><b>Knitting</b></td>
                <td colspan="2" align="center" valign="top"><b>Dyeing</b></td>
                <td colspan="2" align="center" valign="top"><b>Finish Fabric Prod.</b></td>
                <td colspan="2" align="center" valign="top"><b>Cutting </b></td>
                <td colspan="2" align="center" valign="top"><b>Sewing </b></td>
                <td colspan="2"  align="center" valign="top"><b>Ex-factory </b></td>
            </tr>
            <tr>
            	<td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>
                <td width="85" align="center" valign="top"><b>Start Date</b></td>
                <td width="85" align="center" valign="top"><b>End Date</b></td>

            </tr>
            <?
			$i=1;
			foreach($tna_date_task_arr as $order_id=>$row)
			{
				 //$tna_date_task_arr//knitting_start_date dying_start_date finishing_start_date cutting_start_date sewing_start_date exfact_start_date
				?>
                <tr>
                	<td><? echo $i; ?></td>
                    <td><? echo $po_num_arr[$order_id]; ?></td>
                    <td align="center"><? echo change_date_format($row['yarn_rec_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['yarn_rec_end_date']); ?></td>
                	<td align="center"><? echo change_date_format($row['knitting_start_date']); ?></td>
                    <td  align="center"><? echo change_date_format($row['knitting_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['dying_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['dying_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['finishing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['finishing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['cutting_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['sewing_end_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_start_date']); ?></td>
                    <td align="center"><? echo change_date_format($row['exfact_end_date']); ?></td>
                </tr>
                <?
				$i++;
			}
			?>

        </table>
        </fieldset>
        <?
		}// fabric Source End
		if($isYarnPurchseValidate==2)
		{
			?>
            <br>
			<table align="left" width="350px" style="border:1px solid black;font-size:12px; font-family:Arial Narrow;" border="1" cellspacing="0" rules="all">
				<tr>
					<th colspan="3">Yarn Purchase Requisition Info</th>
				</tr>
				<tr>
					<th width="120">Job No</th>
					<th width="130">Purchase Req. No</th>
					<th>Qty.</th>
				</tr>
                
                <?
				$sqlYarnReq="select a.requ_no, b.job_no, sum(b.quantity) as qty from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and b.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.requ_no, b.job_no";
				$sqlYarnReqRes=sql_select($sqlYarnReq);
				foreach($sqlYarnReqRes as $row)
				{
				?>
                <tr>
					<td width="120"><?=$row[csf("job_no")]; ?></td>
					<td width="130"><?=$row[csf("requ_no")]; ?></td>
					<td align="right"><?=number_format($row[csf("qty")],2); ?></td>
				</tr>
                
                <?
				}
				?>
        	</table>
            <br><br><br>
		<? } echo get_spacial_instruction($txt_booking_no,"97%",118); ?>
        
        
        <br>
         <div style="font-family:Arial Narrow;">
         <?
		 	echo signature_table(1, $cbo_company_name, "1400px");
		 ?>
         </div>
        <br>

        <?
        	$grand_order_total=0;
        	$grand_plan_total=0;
        	$size_wise_total=array();
			$nameArray_size=sql_select( "select size_number_id from wo_po_color_size_breakdown where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by size_number_id ");
			?>
			
                <div id="div_size_color_matrix" class="pagebreak">
                    <fieldset id="div_size_color_matrix" >
                        <legend>Size and Color Breakdown</legend>
                        <table  class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all" >
                            <tr>
                            	<td>PO Number</td>
                            	<td>PO Received Date</td>
                            	<td>Ship Date</td>
                            	<td>Lead Time</td>
                            	<td>Ship.days in Hand</td>
                            	<td>Gmts Item</td>
                                <td style="border:1px solid black"><strong>Color/Size</strong></td>
                                <?
                                foreach($nameArray_size  as $result_size)
                                {
									?>
                                	<td align="center" style="border:1px solid black"><?=$size_library[$result_size[csf('size_number_id')]];?></td>
                                <? } ?>
                                <td  align="center"> Total Order Qty(Pcs)</td>
                                <td  align="center"> Excess %</td>
                                <td  align="center"> Total Plan Cut Qty(Pcs)</td>
                            </tr>
                            <?
                            $color_size_order_qnty_array=array(); $color_size_qnty_array=array(); $size_tatal=array(); $size_tatal_order=array();
                           	$item_size_tatal=array(); $item_size_tatal_order=array(); $item_grand_total=0; $item_grand_total_order=0;
                           	$result_cs=sql_select( "select b.item_number_id,b.color_number_id,sum(b.order_quantity) as order_quantity,b.size_number_id,b.po_break_down_id from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.job_no_mst ='$job_no' and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 group by b.item_number_id,b.color_number_id,b.size_number_id,b.po_break_down_id");
                           	$color_size_data=array();
                           	foreach ($result_cs as $row) {
                           		$color_size_data[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('order_quantity')];
                           	}

                           	$sql_color="select a.po_number,a.id,a.po_received_date,a.shipment_date,b.item_number_id,b.color_number_id,sum(b.plan_cut_qnty) as plan_cut_qnty,a.shiping_status from wo_po_break_down a, wo_po_color_size_breakdown b where a.id=b.po_break_down_id and a.job_no_mst ='$job_no' and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 group by a.po_number,a.id,a.po_received_date,a.shipment_date,b.item_number_id,b.color_number_id,a.shiping_status order by a.shipment_date asc ";
                           	//echo $sql_color;
							$result_color_size=sql_select( $sql_color);

							foreach ($result_color_size as $row) 
							{
								
								?>

								<tr>
									<td><?php echo $row[csf('po_number')] ?></td>
									<td><?php echo change_date_format($row[csf('po_received_date')]); ?></td>
									<td><?php echo change_date_format($row[csf('shipment_date')]); ?></td>
									<?php 

										$date1=date_create($row[csf('po_received_date')]);
										$date2=date_create($row[csf('shipment_date')]);
										$diff=date_diff($date1,$date2);
										$current_date=date_create(strval(date('Y-m-d')));
										$diff1=date_diff($current_date,$date2);
										
										$day_in_hand=$diff1->format("%R%a days");
										if($row[csf('shiping_status')]==3)
										{
											$day_in_hand='0 days';
										}
									 ?>
									<td><?php echo $diff->format("%a days"); ?></td>
									<td><?php echo str_replace("+", "", $day_in_hand); ?></td>
									<td><?php echo $garments_item[$row[csf('item_number_id')]]; ?></td>
									<td><?php echo $color_library[$row[csf('color_number_id')]]; ?></td>
									<?
									$total=0;

                                    foreach($nameArray_size  as $key)
                                    {
										
										?>
                                    	<td align="center" style="border:1px solid black">
                                    		<?
                                    				$qnty=0;
                                    				$qnty=$color_size_data[$row[csf('id')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$key[csf('size_number_id')]];

                                    				echo number_format($qnty);
                                    				$size_wise_total[$key[csf('size_number_id')]]+=$qnty;
                                    				$total+=$qnty;
                                    				
                                    			?>
                                    		
                                    	
                                    	</td>
                                   	 	<? 
                                	}

                                	$grand_order_total+=$total;
        							$grand_plan_total+=$row[csf('plan_cut_qnty')];

        							$plan_cut_dif=$row[csf('plan_cut_qnty')]-$total;
        							$ex_cut_perc=($plan_cut_dif/$total)*100;

                                	?>
                                	<td align="center"><?php echo number_format($total); ?></td>
                                	<td align="center"><?php echo number_format($ex_cut_perc,2); ?>%</td>
                                	<td align="center"><?php echo number_format($row[csf('plan_cut_qnty')]); ?></td>

								</tr>
								<?
							}

								
                            ?>
                            <tr>
                            	<td align="right" colspan="7">Total</td>
                            	
                            	<?
                                    foreach($nameArray_size  as $key)
                                    {
										
										?>
                                    	<td align="center" style="border:1px solid black">
                                    		<strong><?
                                    				
                                    				
                                    				echo number_format($size_wise_total[$key[csf('size_number_id')]])
                                    			?>
                                    		
                                    	</strong>

                                    	</td>
                                   	 	<? 
                                	}

                                	?>
                                <td align="center"><strong><?=number_format($grand_order_total)?></strong></td>
                                <td></td>
                                <td align="center"><strong><?=number_format($grand_plan_total)?></strong></td>
                            </tr>
                           
                           
                        </table>
                    </fieldset>
                </div>
          
			<?

			$actule_po_size=sql_select("select gmts_size_id from wo_po_acc_po_info where po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and is_deleted=0 and status_active=1 group by gmts_size_id ");
			$actule_po_data=sql_select( "SELECT a.id as po_id,a.po_number, b.acc_po_no, a.po_received_date, b.acc_ship_date, b.gmts_color_id, b.gmts_size_id, b.acc_po_qty, b.id as actule_po_id , b.gmts_item from wo_po_break_down a join wo_po_acc_po_info b on a.id=b.PO_BREAK_DOWN_ID where b.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 and a.status_active=1");
			$actule_po_arr=array();
			$attribute=array('po_number','acc_po_no','po_received_date','acc_ship_date','gmts_color_id','gmts_size_id','acc_po_qty','gmts_item');
			foreach ($actule_po_data as $row) {
				foreach ($attribute as $attr) {
					$actule_po_arr[$row[csf('po_id')]][$row[csf('actule_po_id')]][$attr]=$row[csf($attr)];
				}
				$actual_color_size[$row[csf('po_id')]][$row[csf('actule_po_id')]][$row[csf('gmts_item')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size_id')]] =$row[csf('acc_po_qty')];				
			}

			$booking_dtls_data=sql_select(" SELECT a.po_number, a.pub_shipment_date,b.fabric_color_id,b.gmts_color_id, b.fin_fab_qnty, b.grey_fab_qnty, c.size_number_id,c.excess_cut_perc from wo_po_break_down a join wo_booking_dtls b on a.id=b.po_break_down_id join WO_PO_COLOR_SIZE_BREAKDOWN c on c.id=b.color_size_table_id where b.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
			$size_lib_arr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		
		?>
		<div id="div_size_color_matrix" class="pagebreak">
            <fieldset id="div_size_color_matrix" >
                <legend>Actual PO Info</legend>
                <table  class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all" >
                    <tr>
                    	<td>PO Number</td>
                    	<td>Actual PO</td>
                    	<td>PO Received Date</td>
                    	<td>Ship Date</td>
                    	<td>Gmts Item</td>
                        <td style="border:1px solid black"><strong>Color/Size</strong></td>
                        <?
                        foreach($actule_po_size  as $result_size)
                        {
							?>
                        	<td align="center" style="border:1px solid black"><?=$size_library[$result_size[csf('gmts_size_id')]];?></td>
                        <? } ?>
                        <td  align="center"> Total Order Qty(Pcs)</td>
                    </tr>
                    <?

					foreach ($actule_po_arr as $po_id=>$po_data) 
					{	$k=1;
						foreach ($po_data as $actule_po_id=>$data) {
							$total_size_qty=0;
							if($k==1){
						 	?>
							<tr>
								<td rowspan="<? echo count($po_data)?>"><?php echo $data['po_number'] ?></td>
								<td><?php echo $data['acc_po_no'] ?></td>								
								<td><?php echo change_date_format($data['po_received_date']) ?></td>		
								<td><?php echo change_date_format($data['acc_ship_date']) ?></td>			
								<td><?php echo $garments_item[$data['gmts_item']] ?></td>
								<td><?php echo $color_library[$data['gmts_color_id']] ?></td>
								<?php
								foreach($actule_po_size  as $result_size)
		                        {
		                        	$size_qty=$actual_color_size[$po_id][$actule_po_id][$data['gmts_item']][$data['gmts_color_id']][$result_size[csf('gmts_size_id')]];
		                        	$total_size_qty+=$size_qty;
									?>
		                        	<td align="center" width="30"><? echo $size_qty; ?></td>
		                        <? } ?>
		                        <td><? echo $total_size_qty ?></td>				
							</tr>
							<? 
							}
							else{ ?>
								<tr>
									<td><?php echo $data['acc_po_no'] ?></td>								
									<td><?php echo change_date_format($data['po_received_date']) ?></td>		
									<td><?php echo change_date_format($data['acc_ship_date']) ?></td>			
									<td><?php echo $garments_item[$data['gmts_item']] ?></td>
									<td><?php echo $color_library[$data['gmts_color_id']] ?></td>
									<?php
									foreach($actule_po_size  as $result_size)
			                        {
										$size_qty=$actual_color_size[$po_id][$actule_po_id][$data['gmts_item']][$data['gmts_color_id']][$result_size[csf('gmts_size_id')]];
										$total_size_qty+=$size_qty;
										?>
		                        	<td align="center" width="30"><? echo $size_qty; ?></td>
			                        <? } ?>
			                        <td><? echo $total_size_qty ?></td>					
								</tr>
							<? }
							$k++;
						}
					}						
                    ?>       
                   
                </table>
            </fieldset>
        </div>
        <div id="div_size_color_matrix" class="pagebreak">
            <fieldset id="div_size_color_matrix" >
                <legend>PO, Color and Size wise fabric Required Quantity</legend>
                <table  class="rpt_table"  border="1" align="left" cellpadding="0"  cellspacing="0" rules="all" >
                	<tr>
                    	<th>PO Number</th>
                    	<th>Ship Date</th>
                    	<th>Fabric Color</th>
                    	<th>Body Color</th>
                    	<th>Size</th>
                    	<th>Total Finish Fabric (Kg)</th>
                    	<th>Total Greige Fabric (Kg)</th>
                    	<th>Process Loss %</th>
                    </tr>
                    <? foreach ($booking_dtls_data as $row) { ?>
                    	<tr>
	                    	<td><? echo $row[csf('po_number')] ?></td>
	                    	<td><? echo change_date_format($row[csf('pub_shipment_date')]) ?></td>
	                    	<td><? echo $color_library[$row[csf('fabric_color_id')]] ?></td>
	                    	<td><? echo $color_library[$row[csf('gmts_color_id')]] ?></td>
	                    	<td align="center"><? echo $size_lib_arr[$row[csf('size_number_id')]] ?></td>
	                    	<td align="center"><? echo number_format($row[csf('fin_fab_qnty')],2) ?></td>
	                    	<td align="center"><? echo number_format($row[csf('grey_fab_qnty')],2) ?></td>
	                    	<td align="center"><? echo $row[csf('excess_cut_perc')] ?></td>
                    	</tr>
                    <? 
                    	$atotal_fin_fab_qnty+=$row[csf('fin_fab_qnty')];
                    	$atotal_grey_fab_qnty+=$row[csf('grey_fab_qnty')];
                    	} 
                    ?>
                    <tr>
                    	<th colspan="5" align="right">Total</th>
                    	<th><? echo number_format($atotal_fin_fab_qnty,2) ?></th>
                    	<th><? echo number_format($atotal_grey_fab_qnty,2) ?></th>
                    </tr>
                </table>
            </fieldset>
        </div>        
       </div>
       <?
	   exit();
}

if($action=="last_version_check_print_booking")
{
	extract($_REQUEST);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$revised_no=str_replace("'","",$revised_no);
	$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
    $marchentrArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info ","id","team_member_name");
	$country_arr=return_library_array( "select id,country_name from   lib_country",'id','country_name');
	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_address_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	//$job_no_arr=return_library_array( "SELECT id,job_no from wo_po_details_master",'id','job_no');
	$po_qnty_tot=return_field_value( "sum(po_quantity)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	$sample_name_name_arr=return_library_array( "select id,sample_name from    lib_sample",'id','sample_name');
	?>
	<div style="width:1330px" align="center">
    										<!--    Header Company Information         -->
    <?php
	$nameArray_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13");

	list($nameArray_approved_row) = $nameArray_approved;
	$nameArray_approved_date = sql_select("select b.approved_date as approved_date from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
	list($nameArray_approved_date_row) = $nameArray_approved_date;
	$nameArray_approved_comments = sql_select("select b.comments as comments from wo_booking_mst a, approval_history b where a.id=b.mst_id and booking_no=$txt_booking_no and b.entry_form=13 and b.approved_no='" . $nameArray_approved_row[csf('approved_no')] . "'");
	list($nameArray_approved_comments_row) = $nameArray_approved_comments;
	?>
       <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid black">
           <tr>
               <td width="100">
              	 <img  src='../../<? echo $imge_arr[$cbo_company_name]; ?>' height='100%' width='100%' />
               </td>
               <td width="1000">
                    <table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;"><? echo $company_library[$cbo_company_name]; ?></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px">
                            <?
								$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$cbo_company_name");
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
                            	<strong><? echo $report_title;?> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:#F00"><? if(str_replace("'","",$id_approved_id) ==1){ echo "(Approved)";}else{echo "";}; ?> </font></strong>
                            </td>

                              <td style="font-size:15px"> <strong>Revised No: <? echo $revised_no-1; ?></strong></td>
                        </tr>
                    </table>
                </td>
                 <td width="250" id="barcode_img_id">

               </td>
            </tr>
       </table>
		<?
        $job_no='';
        $total_set_qnty=0;
        $colar_excess_percent=0;
        $cuff_excess_percent=0;
        $nameArray=sql_select( "SELECT a.id,a.booking_no,a.booking_date,a.supplier_id,a.currency_id,a.exchange_rate,a.attention,a.delivery_date,a.po_break_down_id,a.colar_excess_percent,a.cuff_excess_percent,a.fabric_composition,a.delivery_date,a.is_apply_last_update,a.pay_mode,a.fabric_source,b.job_no,b.buyer_name, b.style_ref_no ,b.gmts_item_id,b.order_uom,b.total_set_qnty,b.style_description,b.season,b.product_dept,b.product_code,b.pro_sub_dep,b.dealing_marchant from wo_booking_mst a, wo_po_details_master b where  a.job_no=b.job_no and a.booking_no=$txt_booking_no and a.company_id='$cbo_company_name' and b.company_name='$cbo_company_name'"); //2 compnay check for sample job is FAL-15-00586 in development
        foreach ($nameArray as $result)
        {
			$total_set_qnty=$result[csf('total_set_qnty')];
			$colar_excess_percent=$result[csf('colar_excess_percent')];
			$cuff_excess_percent=$result[csf('cuff_excess_percent')];
			$po_no="";$file_no="";$ref_no="";
			$shipment_date="";
			 $sql_po= "SELECT po_number,grouping,file_no,MIN(pub_shipment_date) pub_shipment_date from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,grouping,file_no";
			$data_array_po=sql_select($sql_po);
			foreach ($data_array_po as $row_po)
			{
				$po_no.=$row_po[csf('po_number')].", ";
				$shipment_date.=change_date_format($row_po[csf('pub_shipment_date')],'dd-mm-yyyy','-').", ";
				$ref_no=$row_po[csf('grouping')].",";
				$file_no=$row_po[csf('file_no')].",";
			}
      		 //$file_no= rtrim($file_no,','); $ref_no= rtrim($ref_no,',');
			$lead_time="";
			if($db_type==0)
			{
				$sql_lead_time= "SELECT DATEDIFF(pub_shipment_date,po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
			}

			if($db_type==2)
			{
				$sql_lead_time= "SELECT (pub_shipment_date-po_received_date) date_diff from  wo_po_break_down  where id in(".$result[csf('po_break_down_id')].")";
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

			$sql_po= "select po_number,MIN(pub_shipment_date) pub_shipment_date, MIN(insert_date) as insert_date,shiping_status from wo_po_break_down  where id in(".$result[csf('po_break_down_id')].") group by po_number,shiping_status";
			$data_array_po=sql_select($sql_po);
			foreach ($data_array_po as $rows)
			{
				$daysInHand.=(datediff('d',$result[csf('delivery_date')],$rows[csf('pub_shipment_date')])-1).",";
				$booking_date=$result[csf('update_date')];
				if($booking_date=="" || $booking_date=="0000-00-00 00:00:00")
				{
					$booking_date=$result[csf('insert_date')];
				}
				$WOPreparedAfter.=(datediff('d',$rows[csf('insert_date')],$booking_date)-1).",";

				if($rows[csf('shiping_status')]==1)
				{
					$shiping_status.= "FP".",";
				}
				else if($rows[csf('shiping_status')]==2)
				{
					$shiping_status.= "PS".",";
				}
				else if($rows[csf('shiping_status')]==3)
				{
					$shiping_status.= "FS".",";
				}
			}

			$varcode_booking_no=$result[csf('booking_no')];
			if($result[csf('style_ref_no')])$style_sting.=$result[csf('style_ref_no')].'_';
        ?>
            <table width="100%" style="border:1px solid black" >
                <tr>
                	<td colspan="6" valign="top" style="font-size:18px; color:#F00"><? if($result[csf('is_apply_last_update')]==2){echo "Booking Info not synchronized with order entry and pre-costing. order entry or pre-costing has updated after booking entry.  Contact to ".$marchentrArr[$result[csf('dealing_marchant')]]; } else{ echo "";} ?></td>
                </tr>
                <tr>
                    <td width="100"><span style="font-size:18px"><b>Buyer/Agent Name</b></span></td>
                    <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $buyer_name_arr[$result[csf('buyer_name')]]; ?></b></span></td>
                    <td width="100"><span style="font-size:18px"><b>Job No</b></span></td>
                    <td width="110">:&nbsp;<span style="font-size:18px"><b><? echo $result[csf('job_no')]; ?></b></span></td>
                    <td width="100"><span style="font-size:12px"><b>Dept.</b></span></td>
                    <td width="110">:&nbsp;<? echo $product_dept[$result[csf('product_dept')]] ; if($result[csf('product_code')] !=""){ echo " (".$result[csf('product_code')].")";} if($result[csf('pro_sub_dep')] !=0){ echo " (".$pro_sub_dept_array[$result[csf('pro_sub_dep')]].")";}?></td>
                    
                </tr>
                <tr>
                	<td width="100"><span style="font-size:12px"><b>Order Qnty</b></span></td>
                    <td width="110">:&nbsp;<? echo $po_qnty_tot." ".$unit_of_measurement[$result[csf('order_uom')]] ; ?></td>
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
                    
                </tr>
                <tr>
                	<td width="100" style="font-size:18px"><b>Style Ref.</b>   </td>
                    <td width="110" style="font-size:18px">:&nbsp;<b><? echo $result[csf('style_ref_no')];?></b></td>
                    <td  width="100" style="font-size:12px"><b>Style Des.</b></td>
                    <td  width="110" >:&nbsp;<? echo $result[csf('style_description')]; $job_no= $result[csf('job_no')];?></td>
                    <td width="100" style="font-size:12px"><b>Lead Time </b>   </td>
                    <td width="110">:&nbsp;<?  echo rtrim($lead_time,",");;?> </td>
                    
                </tr>
                <tr>
                	<td width="100" style="font-size:12px"><b>Dealing Merchant</b></td>
                    <td width="110">:&nbsp;<? echo $marchentrArr[$result[csf('dealing_marchant')]]; ?></td>
                    <td width="100" style="font-size:12px"><b>Supplier Name</b>   </td>
                    <td width="110">:&nbsp;<?
					if($result[csf('pay_mode')]==5 || $result[csf('pay_mode')]==3){
					echo $company_library[$result[csf('supplier_id')]];
					}
					else{
					echo $supplier_name_arr[$result[csf('supplier_id')]];
					}
					//echo $supplier_name_arr[$result[csf('supplier_id')]];?>    </td>
                    <td width="100" style="font-size:12px"><b>Fab. Delivery Date</b></td>
                    <td width="110">:&nbsp;<? echo change_date_format( $result[csf('delivery_date')],'dd-mm-yyyy','-');?></td>
                    
                </tr>
                <tr>
                	<td width="100" style="font-size:18px"><b>Booking No </b>   </td>
                    <td width="150" style="font-size:18px">:&nbsp;<b><? echo $result[csf('booking_no')];?></b><? echo " (".$fabric_source[$result[csf('fabric_source')]].")"?></td>
                    <td width="100" style="font-size:12px"><b>Season</b></td>
                    <td width="110">:&nbsp;<? echo $result[csf('season')]; ?></td>
                    <td width="100" style="font-size:12px"><b>Attention</b></td>
                    <td width="110" >:&nbsp;<? echo $result[csf('attention')]; ?></td>
                    
                </tr>
                <tr>
                	<td width="100" style="font-size:12px"><b>Po Received Date</b></td>
                    <td width="110" >:&nbsp;<? echo $po_received_date; ?></td>
                    <td width="100" style="font-size:18px"><b>Order No</b></td>
                    <td width="110" style="font-size:18px" colspan="5">:&nbsp;<b><? echo rtrim($po_no,", "); ?></b></td>
                </tr>
                <tr>
                    <td width="100" style="font-size:12px"><b>Shipment Date</b></td>
                    <td width="110" colspan="5"> :&nbsp;<? echo rtrim($shipment_date,", "); ?></td>
                </tr>
                <tr>
                    <td width="110" style="font-size:12px"><b>WO Prepared After</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($WOPreparedAfter,',').' Days' ?></td>
                    <td width="100" style="font-size:12px"><b>Ship.days in Hand</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($daysInHand,',').' Days'?></td>
                    <td width="100" style="font-size:12px"><b>Ex-factory status</b></td>
                    <td> :&nbsp;<? echo rtrim($shiping_status,','); ?></td>
                </tr>
                 <tr>
                    <td width="110" style="font-size:12px"><b>File No</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($file_no,','); ?></td>
                    <td width="100" style="font-size:12px"><b>Internal Ref.</b></td>
                    <td width="300"> :&nbsp;<? echo rtrim($ref_no,',');?></td>
                    <td width="100" style="font-size:12px"></td>
                    <td>&nbsp;</td>
                </tr>

                <tr>
                    <td width="110" style="font-size:12px"><b>Fabric Composition</b></td>
                    <td  colspan="5">: &nbsp;<? echo $result[csf('fabric_composition')]; ?></td>

                </tr>
            </table>
        <?
		}
		?>
      <br/>   									 <!--  Here will be the main portion  -->
     <style>
		 .main_table tr th{
			 border:1px solid black;
			 font-size:13px;
			 outline: 0;
		 }
		  .main_table tr td{
			 border:1px solid black;
			 font-size:13px;
			 outline: 0;
		 }
	</style>
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
	//$process_loss_method=return_field_value( "process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job_no'");

	$process_loss_method=return_field_value("process_loss_method", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=18 and item_category_id=$cbo_fabric_natu and status_active=1 and is_deleted=0");

	if(str_replace("'","",$cbo_fabric_source)==1)
	{
		$nameArray_fabric_description= sql_select("select c.body_part_id,c.color_type_id,c.construction,c.composition,c.gsm_weight,d.dia_width,d.process_loss_percent ,d.gmts_color_id,a.id as pre_cost_fab_cost_dtls ,d.approved_no from wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h c on a.id=c.pre_cost_fabric_cost_dtls_id,wo_booking_dtls b left join wo_booking_dtls_hstry d  on b.id=d.booking_dtls_id where b.booking_no =$txt_booking_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.approved_no=$revised_no and b.status_active=1 and b.is_deleted=0 group by c.body_part_id,c.color_type_id,c.construction,c.composition,c.gsm_weight,d.dia_width,d.process_loss_percent,d.gmts_color_id,a.id,d.approved_no order by c.body_part_id ");

	

			$gmts_color_id_arr=array();	$pre_cost_fab_cost_dtls_arr=array();
		foreach($nameArray_fabric_description as $rows){
			$gmts_color_id_arr[$rows[csf('gmts_color_id')]]=$rows[csf('gmts_color_id')];
			$pre_cost_fab_cost_dtls_arr[$rows[csf('pre_cost_fab_cost_dtls')]]=$rows[csf('pre_cost_fab_cost_dtls')];

		}

		// echo "<pre>";
		// print_r($pre_cost_fab_cost_dtls_arr);

	    ?>
        <table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
            <tr align="center"><th colspan="3" align="left">Body Part</th>
				<?
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
                    if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
                    else echo "<td  colspan='2'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
                }
                ?>
            	<td rowspan="8" width="50"><p>Total  Finish Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td> <td  rowspan="8" width="50"><p>Total Grey Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?>)</p></td>
            	<td rowspan="8" width="50"><p>Process Loss %</p></td>
            </tr>
            <tr align="center"><th colspan="3" align="left">Color Type</th>
                <?
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='2'>&nbsp</td>";
					else echo "<td  colspan='2'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th colspan="3" align="left">Construction</th>
				<?
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='2'>&nbsp</td>";
					else echo "<td  colspan='2'>". $result_fabric_description[csf('construction')]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="3" align="left">Composition</th>
				<?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='2' >&nbsp</td>";
					else echo "<td colspan='2' >".$result_fabric_description[csf('composition')]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">GSM</th>
				<?
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='2'>&nbsp</td>";
					else echo "<td colspan='2' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="3" align="left">Dia/Width</th>
				<?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                    if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='2'>&nbsp</td>";
                    else echo "<td colspan='2' align='center'>". $result_fabric_description[csf('dia_width')]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">Process Loss%</th>
				<?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('process_loss_percent')] == "")   echo "<td colspan='2'>&nbsp</td>";
					else echo "<td align='center' colspan='2'>". $result_fabric_description[csf('process_loss_percent')]."</td>";
                }
                ?>
            </tr>
            <tr>
                <th  width="100" align="left">Sample Name</th>
                <th  width="100" align="left">Fabric Color</th>
                <th  width="100" align="left">Lapdip No</th>
				<?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
                    echo "<th width='50'>Finish</th><th width='50' >Gray</th>";
                }
                ?>
            </tr>
       		<?
	        $grand_total_fin_fab_qnty=0;
			$grand_total_grey_fab_qnty=0;
			$grand_totalcons_per_finish=0;
			$grand_totalcons_per_grey=0;
			if($db_type==0) $sample_type_id="group_concat(sample_type)";
			else if($db_type==2) $sample_type_id="listagg((cast(sample_type as varchar2(4000))),',') within group (order by sample_type)";

			$color_wise_wo_sql=sql_select("select b.job_no, b.fabric_color_id, b.po_break_down_id, listagg((cast(sample_type as varchar2(4000))),',') within group (order by sample_type) as sample_type 
			FROM wo_booking_dtls a left join wo_booking_dtls_hstry b  on a.id=b.booking_dtls_id WHERE a.booking_no =$txt_booking_no and b.approved_no=$revised_no and a.status_active=1 and a.is_deleted=0 group by b.job_no, b.fabric_color_id, b.po_break_down_id");
										 
			foreach($color_wise_wo_sql as $color_wise_wo_result)
			{
				?>
				<tr>
                    <td  width="100" align="left">
						<?
                        $sample_type_val="";
                        $ex_sample_type=array_unique(explode(",",$color_wise_wo_result[csf('sample_type')]));
                        foreach($ex_sample_type as $sm_val)
                        {
                        	if($sample_type_val=="") $sample_type_val=$sample_name_name_arr[$sm_val]; else $sample_type_val.=','.$sample_name_name_arr[$sm_val];
                        }
                   		echo $sample_type_val;
                    ?></td>
                    <td  width="100" align="left">
						<?
                        echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
                        ?>
                    </td>
                    <td  width="100" align="left">
						<?
                        $lapdip_no="";
                        $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$color_wise_wo_result[csf('job_no')]."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]." and po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]."","lapdip_no");
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
							$color_wise_wo_sql_qnty=sql_select("select sum(c.fin_fab_qnty) as fin_fab_qnty,sum(c.grey_fab_qnty) as grey_fab_qnty
															FROM
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b left join wo_booking_dtls_hstry c  on b.id=c.booking_dtls_id
															WHERE
															b.booking_no =$txt_booking_no  and 
															c.approved_no=$revised_no and															  
															a.id=b.pre_cost_fabric_cost_dtls_id and
															a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
															a.construction='".$result_fabric_description[csf('construction')]."' and
															a.composition='".$result_fabric_description[csf('composition')]."' and
															a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
															b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
															b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and
															b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
															b.status_active=1 and
															b.is_deleted=0");

						}
						if($db_type==2)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(c.fin_fab_qnty) as fin_fab_qnty,sum(c.grey_fab_qnty) as grey_fab_qnty
															  FROM
															  wo_pre_cost_fabric_cost_dtls a ,
															  wo_booking_dtls b left join wo_booking_dtls_hstry c  on b.id=c.booking_dtls_id
															  WHERE
															  b.booking_no =$txt_booking_no  and
															  c.approved_no=$revised_no and		
															  a.id=b.pre_cost_fabric_cost_dtls_id and
															  nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															  nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
															  nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
															  nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
															  nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
															  nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
															  nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and
															  nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
															  b.status_active=1 and
															  b.is_deleted=0 and b.po_break_down_id=".$color_wise_wo_result[csf('po_break_down_id')]."");

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
						//$devided_val = 1-(($total_grey_fab_qnty-$total_fin_fab_qnty)/100);
						//$process_percent=$total_grey_fab_qnty/$devided_val;
						$process_percent=(($total_grey_fab_qnty-$total_fin_fab_qnty)/$total_grey_fab_qnty)*100;
                    }
                    echo number_format($process_percent,2);
                    ?>
                    </td>
				</tr>
				<?
			}
			?>
			<tr>
				<td  width="120" align="left">&nbsp;</td>
				<td  width="120" align="left">&nbsp;</td>
				<td  width="120" align="left"><strong>Total</strong></td>
				<?
				foreach($nameArray_fabric_description as $result_fabric_description)
				{
					if($db_type==0)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(c.fin_fab_qnty) as fin_fab_qnty,sum(c.grey_fab_qnty) as grey_fab_qnty
															FROM
															wo_pre_cost_fabric_cost_dtls a ,
															wo_booking_dtls b left join wo_booking_dtls_hstry c  on b.id=c.booking_dtls_id
															WHERE
															b.booking_no =$txt_booking_no  and
															c.approved_no=$revised_no and		
															a.id=b.pre_cost_fabric_cost_dtls_id and
															a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
															a.construction='".$result_fabric_description[csf('construction')]."' and
															a.composition='".$result_fabric_description[csf('composition')]."' and
															a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
															b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
															b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and
															b.status_active=1 and
															b.is_deleted=0");
					}
					if($db_type==2)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(c.fin_fab_qnty) as fin_fab_qnty,sum(c.grey_fab_qnty) as grey_fab_qnty
															FROM
															wo_pre_cost_fabric_cost_dtls a,
															wo_booking_dtls b left join wo_booking_dtls_hstry c  on b.id=c.booking_dtls_id
															WHERE
															b.booking_no =$txt_booking_no  and
															c.approved_no=$revised_no and		
															a.id=b.pre_cost_fabric_cost_dtls_id and
															nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
															nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
															nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
															nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
															nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
															nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and
															b.status_active=1 and
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
					if($process_loss_method==1)
					{
						$totalprocess_percent=(($grand_total_grey_fab_qnty-$grand_total_fin_fab_qnty)/$grand_total_fin_fab_qnty)*100;
					}
					if($process_loss_method==2)
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
	 //echo  $cbo_fabric_source;
	if(str_replace("'","",$cbo_fabric_source)==2)
	{
		$nameArray_fabric_description= sql_select("SELECT c.body_part_id,c.color_type_id,c.construction,c.composition,c.gsm_weight,b.dia_width,b.process_loss_percent 
		FROM wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h c on a.id=c.pre_cost_fabric_cost_dtls_id,wo_booking_dtls b where b.booking_no =$txt_booking_no  and c.approved_no=$revised_no and a.id=b.pre_cost_fabric_cost_dtls_id  and b.status_active=1 and	b.is_deleted=0  group by c.body_part_id,c.color_type_id,c.construction,c.composition,c.gsm_weight,b.dia_width,b.process_loss_percent order by c.body_part_id");
		?>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
            <tr align="center"><th colspan="3" align="left">Body Part</th>
				<?
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('body_part_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
					else echo "<td  colspan='3'>". $body_part[$result_fabric_description[csf('body_part_id')]]."</td>";
                }
                ?>
                <td rowspan="8" width="50"><p>Total  Fabric <? if(trim($cbo_fabric_natu ,"'")==2){echo "(KG)";}else{echo "(Yds)";} ?></p></td>
                <td rowspan="8" width="50"><p>Avg. Rate</p></td>
                <td rowspan="8" width="50"><p>Amount</p></td>
            </tr>
            <tr align="center"><th colspan="3" align="left">Color Type</th>
				<?
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('color_type_id')] == "")  echo "<td  colspan='3'>&nbsp</td>";
					else echo "<td  colspan='3'>". $color_type[$result_fabric_description[csf('color_type_id')]]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th colspan="3" align="left">Construction</th>
				<?
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('construction')] == "")  echo "<td  colspan='3'>&nbsp</td>";
					else echo "<td  colspan='3'>". $result_fabric_description[csf('construction')]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="3" align="left">Composition</th>
				<?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('composition')] == "")   echo "<td colspan='3' >&nbsp</td>";
					else echo "<td colspan='3' >".$result_fabric_description[csf('composition')]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">GSM</th>
				<?
                foreach($nameArray_fabric_description  as $result_fabric_description)
                {
					if( $result_fabric_description[csf('gsm_weight')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('gsm_weight')]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th   colspan="3" align="left">Dia/Width</th>
				<?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('dia_width')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td colspan='3' align='center'>". $result_fabric_description[csf('dia_width')]."</td>";
                }
                ?>
            </tr>
            <tr align="center"><th  colspan="3" align="left">Process Loss%</th>
				<?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if( $result_fabric_description[csf('process_loss_percent')] == "")   echo "<td colspan='3'>&nbsp</td>";
					else echo "<td align='center' colspan='3'>". $result_fabric_description[csf('process_loss_percent')]."</td>";
                }
                ?>
            </tr>
            <tr>
                <th  width="100" align="left">Sample Name</th>
                <th  width="100" align="left">Fabric Color</th>
                <th  width="100" align="left">Lapdip No</th>
                <?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
               		echo "<th width='50'>Fab Qty</th><th width='50'>Rate</th><th width='50'>Amount</th>";
                }
                ?>
            </tr>
            <?
            $grand_total_fin_fab_qnty=0;
            $grand_total_grey_fab_qnty=0;
            $grand_totalcons_per_finish=0;
            $grand_totalcons_per_grey=0;
            if($db_type==0) $sample_type_id="group_concat(sample_type)";
            else if($db_type==2) $sample_type_id="listagg((cast(sample_type as varchar2(4000))),',') within group (order by sample_type)";

            $color_wise_wo_sql=sql_select("select job_no, fabric_color_id, $sample_type_id as sample_type
											FROM
											wo_booking_dtls
											WHERE
											booking_no =$txt_booking_no and
											status_active=1 and
											is_deleted=0
											group by job_no, fabric_color_id");
            foreach($color_wise_wo_sql as $color_wise_wo_result)
			{
				?>
				<tr>
                    <td  width="100" align="left">
						<?
                        $sample_type_val="";
                        $ex_sample_type=array_unique(explode(",",$color_wise_wo_result[csf('sample_type')]));
                        foreach($ex_sample_type as $sm_val)
                        {
                        	if($sample_type_val=="") $sample_type_val=$sample_name_name_arr[$sm_val]; else $sample_type_val.=','.$sample_name_name_arr[$sm_val];
                        }
                        echo $sample_type_val;
                    ?></td>
                    <td width="100" align="left">
						<?
                        echo $color_library[$color_wise_wo_result[csf('fabric_color_id')]];
                        ?>
                    </td>
                    <td width="100" align="left">
						<?
                        $lapdip_no="";
                        $lapdip_no=return_field_value("lapdip_no","wo_po_lapdip_approval_info","job_no_mst='".$color_wise_wo_result[csf('job_no')]."' and approval_status=3 and color_name_id=".$color_wise_wo_result[csf('fabric_color_id')]."","lapdip_no");
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
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
																FROM
																wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h c on a.id=c.pre_cost_fabric_cost_dtls_id,
																wo_booking_dtls b
																WHERE
																b.booking_no =$txt_booking_no  and
																c.approved_no=$revised_no  and
																a.id=b.pre_cost_fabric_cost_dtls_id and
																a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
																a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
																a.construction='".$result_fabric_description[csf('construction')]."' and
																a.composition='".$result_fabric_description[csf('composition')]."' and
																a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
																b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
																b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and
																b.fabric_color_id=".$color_wise_wo_result[csf('fabric_color_id')]." and
																b.status_active=1 and
																b.is_deleted=0");
						}
						if($db_type==2)
						{
							$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
																FROM
																wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h c on a.id=c.pre_cost_fabric_cost_dtls_id,
																wo_booking_dtls b
																WHERE
																b.booking_no =$txt_booking_no  and
																c.approved_no=$revised_no  and
																a.id=b.pre_cost_fabric_cost_dtls_id and
																nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
																nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
																nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
																nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
																nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
																nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
																nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and
																nvl(b.fabric_color_id,0)=nvl(".$color_wise_wo_result[csf('fabric_color_id')].",0) and
																b.status_active=1 and
																b.is_deleted=0");
						}
						list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
						?>
						<td width='50' align='right' >
							<?
                            if($color_wise_wo_result_qnty[csf('grey_fab_qnty')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);
								$total_grey_fab_qnty+=$color_wise_wo_result_qnty[csf('grey_fab_qnty')];
                            }
                            ?>
						</td>
						<td width='50' align='right'>
							<?
                            if($color_wise_wo_result_qnty[csf('rate')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('rate')],2) ;
								//$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
                            }
                            ?>
						</td>
						<td width='50' align='right'>
							<?
                            if($color_wise_wo_result_qnty[csf('amount')]!="")
                            {
								echo number_format($color_wise_wo_result_qnty[csf('amount')],2) ;
								//$total_fin_fab_qnty+=$color_wise_wo_result_qnty[csf('fin_fab_qnty')];
                            }
                            ?>
						</td>
						<?
						$total_amount+=$color_wise_wo_result_qnty[csf('amount')];
                    }
                    ?>
                    <td align="right"><? echo number_format($total_grey_fab_qnty,2); $grand_total_grey_fab_qnty+=$total_grey_fab_qnty;?></td>
                    <td align="right"><? echo number_format($total_amount/$total_grey_fab_qnty,2); //$grand_total_fin_fab_qnty+=$total_fin_fab_qnty;?></td>
                    <td align="right"><? echo number_format($total_amount,2); $grand_total_amount+=$total_amount;?></td>
				</tr>
				<?
			}
            ?>
            <tr>
                <td width="100" align="left">&nbsp;</td>
                <td width="100" align="left">&nbsp;</td>
                <td width="100" align="left"><strong>Total</strong></td>
                <?
                foreach($nameArray_fabric_description as $result_fabric_description)
                {
					if($db_type==0)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
															FROM
															wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h c on a.id=c.pre_cost_fabric_cost_dtls_id,
															wo_booking_dtls b
															WHERE
															b.booking_no =$txt_booking_no  and
															c.approved_no=$revised_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															a.body_part_id='".$result_fabric_description[csf('body_part_id')]."' and
															a.color_type_id='".$result_fabric_description[csf('color_type_id')]."' and
															a.construction='".$result_fabric_description[csf('construction')]."' and
															a.composition='".$result_fabric_description[csf('composition')]."' and
															a.gsm_weight='".$result_fabric_description[csf('gsm_weight')]."' and
															b.dia_width='".$result_fabric_description[csf('dia_width')]."' and
															b.process_loss_percent='".$result_fabric_description[csf('process_loss_percent')]."' and
															b.status_active=1 and
															b.is_deleted=0");
					}
					if($db_type==2)
					{
						$color_wise_wo_sql_qnty=sql_select("select sum(b.fin_fab_qnty) as fin_fab_qnty,sum(b.grey_fab_qnty) as grey_fab_qnty, avg(b.rate) as rate, sum(b.amount) as amount
															FROM
															wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h c on a.id=c.pre_cost_fabric_cost_dtls_id,
															wo_booking_dtls b
															WHERE
															b.booking_no =$txt_booking_no  and
															c.approved_no=$revised_no  and
															a.id=b.pre_cost_fabric_cost_dtls_id and
															nvl(a.body_part_id,0)=nvl('".$result_fabric_description[csf('body_part_id')]."',0) and
															nvl(a.color_type_id,0)=nvl('".$result_fabric_description[csf('color_type_id')]."',0) and
															nvl(a.construction,0)=nvl('".$result_fabric_description[csf('construction')]."',0) and
															nvl(a.composition,0)=nvl('".$result_fabric_description[csf('composition')]."',0) and
															nvl(a.gsm_weight,0)=nvl('".$result_fabric_description[csf('gsm_weight')]."',0) and
															nvl(b.dia_width,0)=nvl('".$result_fabric_description[csf('dia_width')]."',0) and
															nvl(b.process_loss_percent,0)=nvl('".$result_fabric_description[csf('process_loss_percent')]."',0) and
															b.status_active=1 and
															b.is_deleted=0");
					}
					list($color_wise_wo_result_qnty)=$color_wise_wo_sql_qnty
					?>
					<td width='50' align='right' > <? echo number_format($color_wise_wo_result_qnty[csf('grey_fab_qnty')],2);?></td>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('rate')],2) ;?></td>
					<td width='50' align='right'><?  echo number_format($color_wise_wo_result_qnty[csf('amount')],2) ;?></td>
					<?
                }
                ?>
                <td align="right"><? echo number_format($grand_total_grey_fab_qnty,2);?></td>
                <td align="right"><? echo number_format($grand_total_amount/$grand_total_grey_fab_qnty,2);?></td>
                <td align="right"><? echo number_format($grand_total_amount,2);?></td>
            </tr>
		</table>
		<br/>
	<?
	}?>
	<!-- start  -->
	<div style="width:1330px; float:left">
	
	<?
	// Body Part type used only Cuff and Flat Knit
	$colar_percent_size_wise_array=return_library_array( "select a.colar_cuff_per,b.gmts_sizes from wo_booking_dtls a, wo_pre_cos_fab_co_avg_con_dtls b,wo_pre_cost_fabric_cost_dtls c  where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.pre_cost_fabric_cost_dtls_id=c.id and a.po_break_down_id=b.po_break_down_id and a.color_size_table_id=b.color_size_table_id and a.booking_no=$txt_booking_no and a.booking_type=1 and c.body_part_type in(40,50) and a.status_active=1 and a.is_deleted=0", "gmts_sizes", "colar_cuff_per");
	
	//	echo  "select a.body_part_type,a.body_part_id,sum(d.rmg_qty) as rmg_qty,d.gmts_size,d.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d WHERE a.job_no=d.job_no and d.booking_no =$txt_booking_no and  a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and d.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.body_part_type in(40,50) group by a.body_part_type,a.body_part_id,d.gmts_size,d.gmts_color_id order by a.body_part_id";
	
	$nameArray_body_part=sql_select( "select a.body_part_type,a.body_part_id,sum(d.bh_qty) as rmg_qty,d.gmts_size,d.gmts_color_id FROM wo_pre_cost_fabric_cost_dtls a, wo_booking_dtls d WHERE a.job_no=d.job_no and d.booking_no =$txt_booking_no and  a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and d.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.body_part_type in(40,50) group by a.body_part_type,a.body_part_id,d.gmts_size,d.gmts_color_id order by a.body_part_id");
	
	
	
	$row_count=count($nameArray_body_part);
	//if($row_count==0) echo " <p style='color:#f00; text-align:center; font-size:15px;'> Body part type is  used only Flat Knit and Cuff.</p> ";
	foreach($nameArray_body_part as $row)
	{
		$body_part_arr[$row[csf('body_part_id')]]['bpart_type']=$row[csf('body_part_type')];
		$body_part_rmg_qty_arr[$row[csf('body_part_id')]][$row[csf('gmts_size')]][$row[csf('gmts_color_id')]]['rmg_qty']+=$row[csf('rmg_qty')];
	}
	// print_r($body_part_arr);
	$tbl_row_count=count($body_part_arr);
	//echo $tbl_row_count.'Dx';
	?>
	
	
	<?
	
	$k=1;
	foreach($body_part_arr as $body_id=>$val)
	{
		$k++;
	
		$bpart_type_id=$val['bpart_type'];
		$nameArray_item_size=sql_select( "select min(c.id) as id,b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=$body_id  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id  and d.status_active=1 and d.is_deleted=0 and a.body_part_type in(40,50) group by b.item_size,c.size_number_id order by id");
		
	
	
	
	?>
	
	<div style="max-height:1330px; width:660px; overflow:auto; float:left; padding-top:20px; margin-left:5px; margin-bottom:5px; position: relative;">
	<table  width="100%" align="left"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<tr>
	<td colspan="<? echo count($nameArray_size)+4;?>" align="center"><b><? echo $body_part[$body_id];?> -  Colour Size Breakedown in Pcs</b></td>
	</tr>
	<tr>
	<td width="70">Size</td>
	
	<?
	
	/*$nameArray_item_size=sql_select( "select b.item_size,c.size_number_id FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no  and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 group by c.size_number_id,b.item_size order by c.size_number_id");*/
	foreach($nameArray_item_size  as $result_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $size_library[$result_size[csf('size_number_id')]];?></strong></td>
	<?
	}
	?>
	<td rowspan="2" align="center"><strong>Total</strong></td>
	
	</tr>
	<tr>
	<td>Collar Size</td>
	
	<?
	foreach($nameArray_item_size  as $result_item_size)
	{
	?>
	<td align="center" style="border:1px solid black"><strong><? echo $result_item_size[csf('item_size')];?></strong></td>
	<?
	}
	?>
	 <?
		$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name");
		$color_wise_wo_sql=sql_select("select a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method,c.color_number_id,sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=$body_id  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.color_number_id=d.gmts_color_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and a.body_part_type in(40,50) group by c.color_number_id,a.id,a.job_no, a.color_size_sensitive,a.color_break_down,a.process_loss_method order by a.id
	");
	foreach($color_wise_wo_sql as $color_wise_wo_result)
	{
		$color_total_collar=0;
		$color_total_collar_order_qnty=0;
		$process_loss_method=$color_wise_wo_result[csf("process_loss_method")];
		$constrast_color_arr=array();
		if($color_wise_wo_result[csf("color_size_sensitive")]==3)
		{
			$constrast_color=explode('__',$color_wise_wo_result[csf("color_break_down")]);
			for($i=0;$i<count($constrast_color);$i++)
			{
				$constrast_color2=explode('_',$constrast_color[$i]);
				$constrast_color_arr[$constrast_color2[0]]=$constrast_color2[2];
			}
		}
	?>
		<tr>
		<td>
		<?
		if($color_wise_wo_result[csf("color_size_sensitive")]==3)
		{
			echo strtoupper ($constrast_color_arr[$color_wise_wo_result[csf('color_number_id')]]) ;
			$lab_dip_color_id=return_field_value("contrast_color_id","wo_pre_cos_fab_co_color_dtls","job_no='".$color_wise_wo_result[csf('job_no')]."' and pre_cost_fabric_cost_dtls_id=".$color_wise_wo_result[csf('id')]." and gmts_color_id=".$color_wise_wo_result[csf('color_number_id')]."");
		}
		else
		{
			echo $color_library[$color_wise_wo_result[csf('color_number_id')]];
			$lab_dip_color_id=$color_wise_wo_result[csf('color_number_id')];
		}
		?>
	
		</td>
		<?
		foreach($nameArray_item_size  as $result_size)
		{
			?>
			<td align="center" style="border:1px solid black">
	
			<?
			$rmg_qty=$body_part_rmg_qty_arr[$body_id][$result_size[csf('size_number_id')]][$color_wise_wo_result[csf('color_number_id')]]['rmg_qty'];
			//echo $bpart_type_id.'=';
			if($bpart_type_id==50)//Cuff
			{
				$fab_rmg_qty=$rmg_qty*2;
			}
			else //Flat Knit
			{
				$fab_rmg_qty=$rmg_qty;
			}
			//$color_wise_wo_sql_qnty=sql_select("select c.color_number_id,sum(c.order_quantity) as order_quantity, sum(c.plan_cut_qnty) as plan_cut_qnty FROM wo_pre_cost_fabric_cost_dtls a,wo_pre_cos_fab_co_avg_con_dtls b, wo_po_color_size_breakdown c , wo_booking_dtls d WHERE a.job_no=b.job_no and a.id=b.pre_cost_fabric_cost_dtls_id and d.booking_no =$txt_booking_no and a.body_part_id=2  and c.job_no_mst=a.job_no and c.id=b.color_size_table_id and d.po_break_down_id=c.po_break_down_id and c.id=d.color_size_table_id and a.id=d.pre_cost_fabric_cost_dtls_id and d.status_active=1 and d.is_deleted=0 and c.color_number_id='".$color_wise_wo_result['color_number_id']."' and c.size_number_id='".$result_size['size_number_id']."'");
			/*$color_wise_wo_sql_qnty=sql_select( "select sum(plan_cut_qnty) as plan_cut_qnty, sum(order_quantity) as order_quantity  from wo_po_color_size_breakdown where  po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and  size_number_id =".$result_size[csf('size_number_id')]." and color_number_id =".$color_wise_wo_result[csf('color_number_id')]."   and  status_active=1 and is_deleted =0");
	
			list($plan_cut_qnty)=$color_wise_wo_sql_qnty;*/
			//$plan_cut=$plan_cut_qnty[csf('plan_cut_qnty')];
			//$colar_excess_per=($plan_cut*$colar_excess_percent)/100;
			echo number_format($fab_rmg_qty,0);
			$color_total_collar+=$fab_rmg_qty;
			$color_total_collar_order_qnty+=$fab_rmg_qty;
			$grand_total_collar+=$fab_rmg_qty;
			$grand_total_collar_order_qnty+=$fab_rmg_qty;
	
			$size_tatal[$result_size[csf('size_number_id')]]+=$fab_rmg_qty;
			?>
			</td>
			<?
		}
		?>
	
		<td align="center"><? echo number_format($color_total_collar,0); ?></td>
	
		</tr>
		<?
		}
		?>
		<tr>
			<td>Size Total</td>
	
			<?
			foreach($nameArray_item_size  as $result_size)
			{
				//$colar_excess_pers=($size_tatal[$result_size[csf('size_number_id')]]*$colar_excess_percent)/100;
				$tot_size_tatal=$size_tatal[$result_size[csf('size_number_id')]];
				//$size_tatal[$result_size[csf('size_number_id')]]=0;
			?>
			<td style="border:1px solid black;  text-align:center"><?  echo number_format($size_tatal[$result_size[csf('size_number_id')]],0);$size_tatal[$result_size[csf('size_number_id')]]=0; ?></td>
			<?
			}
			?>
			<td  style="border:1px solid black;  text-align:center"><?  echo number_format($grand_total_collar,0);$grand_total_collar=0; ?></td>
	
		</tr>
	</table>
	  <br/>
	</div>
	
		
	  <!--End here-->	
	<!-- end -->
	<?
	}
	
	
	




	if(str_replace("'","",$cbo_fabric_source)==1)
	{
		$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
		//$yarn_sql_array=sql_select("SELECT min(id) as id ,count_id, copm_one_id, percent_one,copm_two_id, percent_two, type_id, sum(cons_qnty) as yarn_required, AVG(rate) as rate from wo_pre_cost_fab_yarn_cost_dtls where job_no='$job_no' and  status_active=1 and is_deleted=0 group by count_id,copm_one_id,percent_one, copm_two_id,percent_two,type_id order by id");
		//echo "SELECT min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one,a.copm_two_id, a.percent_two, a.color, a.type_id, sum(a.cons_qnty) as yarn_required, AVG(a.rate) as rate,b.po_break_down_id from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0 group by a.count_id,a.copm_one_id,a.percent_one, a.copm_two_id,a.percent_two,a.color,a.type_id,b.po_break_down_id order by po_break_down_id";

		$po_number_arr=return_library_array( "select b.id,b.po_number from wo_po_break_down b,wo_booking_dtls a where a.po_break_down_id=b.id and a.booking_no=$txt_booking_no ", "id", "po_number"  );
		$yarn_sql_array=sql_select("select a.fabric_cost_dtls_id,min(a.id) as id ,a.count_id, a.copm_one_id, a.percent_one,a.copm_two_id, a.percent_two, a.color, a.type_id, sum(a.cons_qnty) as yarn_required,	avg(a.rate) as rate,c.po_break_down_id,sum(c.grey_fab_qnty) as grey_fab_qnty, a.cons_ratio from wo_pre_cost_fab_yarn_cost_dtls a, wo_booking_dtls b left join wo_booking_dtls_hstry c  on b.id=c.booking_dtls_id where a.job_no=b.job_no and a.fabric_cost_dtls_id=b.pre_cost_fabric_cost_dtls_id and a.job_no='$job_no' and b.booking_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.approved_no=$revised_no group by a.count_id,a.copm_one_id,a.percent_one, a.copm_two_id,a.percent_two,a.color,a.type_id,c.po_break_down_id,a.fabric_cost_dtls_id, a.cons_ratio order by c.po_break_down_id ");
		?>
		<table  width="100%"  border="0" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="49%" valign="top">
                    <table  width="100%"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                        <tr align="center">
                        	<td colspan="7"><b>Yarn Required Summary</b></td>
                        </tr>
                        <tr align="center">
                            <td>Sl</td>
                            <td>PO</td>
                            <td>Yarn Description</td>
                            <td>Brand</td>
                            <td>Lot</td>
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
                        $total_yarn=0;
                        foreach($yarn_sql_array  as $row)
                        {
							$i++;
							?>
							<tr align="center">
                                <td><? echo $i; ?></td>
                                 <td><? echo $po_number_arr[$row[csf('po_break_down_id')]]; ?></td>
                                <td>
									<?
                                    $yarn_des=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]."%  ";
                                    if($row['copm_two_id'] !=0)
                                    {
                                    	$yarn_des.=$composition[$row[csf('copm_two_id')]]." ".$row[csf('percent_two')]."%";
                                    }
                                    $yarn_des.=$yarn_type[$row[csf('type_id')]];
                                    //echo $yarn_count_arr[$row['count_id']]." ".$composition[$row['copm_one_id']]." ".$row['percent_one']."%  ".$composition[$row['copm_two_id']]." ".$row['percent_two']."%  ".$yarn_type[$row['type_id']];
                                    echo $yarn_des;
                                    ?>
                                </td>
                                <td></td>
                                <td></td>
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
                                <td align="right"><? $yarn=($row[csf('grey_fab_qnty')]*$row[csf('cons_ratio')])/100; echo number_format($yarn,2); $total_yarn+=$yarn; ?></td>
							</tr>
							<?
                        }
                        ?>
                        <tr align="center">
                            <td>Total</td>
                            <td></td>
                            <td></td>
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
                            <td></td>
                            <td align="right"><? echo number_format($total_yarn,2); ?></td>
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
                        $supplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                        //$yarn_sql_array=sql_select("SELECT a.item_id, a.qnty,b.supplier_id,b.lot from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.booking_no=$txt_booking_no and  a.status_active=1 and a.is_deleted=0");
                        $i=0;
                        $total_yarn=0;
                        foreach($yarn_sql_array  as $row)
                        {
							$i++;
							?>
							<tr align="center">
                                <td><? echo $i; ?></td>
                                <td><? echo $item[$row[csf('item_id')]]; ?></td>
                                <td><? echo $supplier[$row[csf('supplier_id')]]; ?></td>
                                <td><? echo $row[csf('lot')]; ?></td>
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
					$is_yarn_allocated=return_field_value("allocation","variable_settings_inventory","company_name=$cbo_company_name and variable_list=18 and item_category_id=1");
					if($is_yarn_allocated==1)
					{
						?>
						<font style=" font-size:30px"><b>Draft</b></font>
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
		<?
	}

	?>
 	  <br/>
	<?
	$txt_req_no=$dataArray[0][csf("requisition_number")];
	$color_name_arr=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');

	$costing_per_arr=return_library_array( "select job_no,costing_per from wo_pre_cost_mst where  job_no='$job_no'", "job_no", "costing_per");

	$condition= new condition();
	if(str_replace("'","",$job_no) !=''){
		$condition->job_no("='$job_no'");
	}
	$condition->init();
	$GmtsitemRatioArr=$condition->getGmtsitemRatioArr();
	$cost_per_qty_arr=$condition->getCostingPerArr();
	//print_r($cost_per_qty_arr);
	$fabric= new fabric($condition);
	$fabric_costing_arr=$fabric->getQtyArray_by_FabriccostidAndGmtscolor_knitAndwoven_greyAndfinish();
	$TotalGreyreq=array_sum($fabric_costing_arr['knit']['grey'][$fabric_cost_id][$cbo_color_name]);
	$fabric_color=array(); $color_type_id=0; $fab_des=''; $plan_cut_qnty=0;
	
	

	$sql_data=sql_select("select a.job_no, b.id ,c.item_number_id ,c.country_id ,c.color_number_id ,c.size_number_id ,c.order_quantity ,c.plan_cut_qnty ,d.id as pre_cost_dtls_id , g.item_number_id as cbogmtsitem,g.body_part_id ,g.fab_nature_id ,g.fabric_source ,g.color_type_id, g.fabric_description,g.color_size_sensitive,d.rate, d.uom,e.cons ,e.requirment,f.contrast_color_id 
	from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d left join wo_pre_cost_fabric_cost_dtls_h g on d.id=g.pre_cost_fabric_cost_dtls_id,wo_pre_cos_fab_co_avg_con_dtls e left join wo_pre_cos_fab_co_color_dtls f on e.pre_cost_fabric_cost_dtls_id=f.pre_cost_fabric_cost_dtls_id and e.color_number_id=f.gmts_color_id where 1=1 ".where_con_using_array($pre_cost_fab_cost_dtls_arr,1,'d.id')." ".where_con_using_array($gmts_color_id_arr,1,'c.color_number_id')."  and a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and  c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and a.job_no='$job_no' and g.approved_no=$revised_no and c.size_number_id=e.gmts_sizes and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 order by b.id,d.id");//


	foreach($sql_data as $row){
		$plan_cut_qnty+=$row[csf('plan_cut_qnty')];
		$fab_des=$body_part[$row[csf("body_part_id")]].', '.$color_type[$row[csf("color_type_id")]].', '.$row[csf("fabric_description")];
		$color_type_id=$row[csf("color_type_id")];
		$fabric_uom = $row[csf("uom")];
		if($row[csf('color_size_sensitive')]==1){
			$fabric_color[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		}else{
			$fabric_color[$row[csf('color_number_id')]]=$row[csf('contrast_color_id')];
		}
		$cbogmtsitem=$row[csf('cbogmtsitem')];

		$body_part_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_dtls_id')]]['body_part_id']=$row[csf('body_part_id')];
		$body_part_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_dtls_id')]]['fab_color']=$row[csf('color_number_id')];
		$body_part_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_dtls_id')]]['gmts_ratio']=$GmtsitemRatioArr[$job_no][$cbogmtsitem];
		// $body_part_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_dtls_id')]]['fab_kg_qty']=($TotalGreyreq/$plan_cut_qnty)*$cost_per_qty_arr[$job_no]*$GmtsitemRatioArr[$job_no][$cbogmtsitem];
		// $body_part_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_dtls_id')]]['gmts_ratio']=$GmtsitemRatioArr[$job_no][$cbogmtsitem];

	}

	$GmtsitemRatio=$GmtsitemRatioArr[$txt_job_no][$cbogmtsitem];
	$cons_txt="";
	$cons_txt=$costing_per[$costing_per_arr[$txt_job_no]];
	
	$sql_data=sql_select("select a.stripe_color,a.measurement, a.uom, a.totfidder, a.fabreq, a.fabreqtotkg, a.yarn_dyed, a.stripe_type,a.pre_cost_fabric_cost_dtls_id as pre_cost_id,c.body_part_id, a.color_number_id,c.item_number_id as cbogmtsitem 	from wo_pre_stripe_color a , wo_pre_cost_fabric_cost_dtls b left join wo_pre_cost_fabric_cost_dtls_h c on b.id=c.pre_cost_fabric_cost_dtls_id 
	where a.status_active=1 ".where_con_using_array($pre_cost_fab_cost_dtls_arr,1,'a.pre_cost_fabric_cost_dtls_id')." ".where_con_using_array($gmts_color_id_arr,1,'a.color_number_id')." and a.is_deleted=0");

	

		foreach($sql_data as $row){

			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_id')]][$row[csf('stripe_color')]]['measurement']=$row[csf('measurement')];
			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_id')]][$row[csf('stripe_color')]]['uom']=$row[csf('uom')];
			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_id')]][$row[csf('stripe_color')]]['fabreq']=$row[csf('fabreq')];
			$stripe_arr[$row[csf('body_part_id')]][$row[csf('color_number_id')]][$row[csf('pre_cost_id')]][$row[csf('stripe_color')]]['yarn_dyed']=$row[csf('yarn_dyed')];
		}


	if(count($sql_data)>0) $stripeType=$sql_data[0][csf('stripe_type')]; else $stripeType=0;
	//echo $tot_stripe_measurement;


	if(count($sql_data)>0)
	{
		?>
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
			<tr>
				<td width="60%">
				<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
					<tr>
						<td colspan="9" align="center"><b>Stripe Details</b></td>
					</tr>

					<tr align="center">
						<th width="30"> SL</th>
					
						<th width="100"> Body Part</th>
						<th width="80"> Fabric Color</th>
						<th width="70"> Fabric Qty(KG)</th>
						<th width="70"> Stripe Color</th>
						<th width="70"> Stripe Measurement</th>
						<th width="70"> Stripe Uom</th>
						<th  width="70"> Qty.(KG)</th>
						<th  width="70"> Y/D Req.</th>
					</tr>

					<?
					$i=1;$total_fab_qty=0;
					$total_fabreqtotkg=0;
					$fab_data_array=array();
					$stripe_wise_fabkg_arr=array();
					$color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=23  and status_active=1 and is_deleted=0");
					//	if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
					//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";
					$stripe_wise_fabkg_sql=sql_select("select b.color_id as color_id,a.sample_prod_qty,c.body_part_id,c.color_type_id from sample_development_dtls a,sample_development_rf_color b,sample_development_fabric_acc c where a.sample_mst_id=b.mst_id and   b.dtls_id=c.id  and a.sample_mst_id=c.sample_mst_id and a.sample_color=b.color_id and   a.sample_mst_id in($mst_style_id) and a.sample_prod_qty>0	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

					// echo "<pre>";
					// print_r($stripe_arr);
					foreach($stripe_wise_fabkg_sql as $vals)
					{
						$stripe_wise_fabkg_arr[$vals[csf("body_part_id")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]] +=$vals[csf("sample_prod_qty")];
					}
					foreach($body_part_arr as $body_id=> $color_data)
					{
						foreach($color_data as $color_id=>$pre_cost_data)
						{
						foreach($pre_cost_data as $pre_cost_id=>$color_val){

							$s=1;
								$count=count($stripe_arr[$body_id][$color_id][$pre_cost_id]);
							foreach($stripe_arr[$body_id][$color_id][$pre_cost_id] as $stripe_color_id=>$stripe_data)
							{
							?>
							<tr>
								<?
							
								
								if($s==1){
								?>
								<td rowspan="<?=$count;?>"> <? echo $i; ?></td>						
								<td rowspan="<?=$count;?>"> <? echo $body_part[$body_id]; ?></td>
								<td rowspan="<?=$count;?>"> <? echo $color_name_arr[$color_id]; ?></td>
								<td align="right" rowspan="<?=$count;?>"> <? echo number_format($color_qty,2); ?></td>
								<?
								$s++;
								}
								$tot_stripe_measurement=$tot_stripe_measurement_arr[$color_id];
								$total_fab_qty+=$color_qty;
								//foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
								//{
									$measurement=$stripe_data['measurement'];
									$uom=$stripe_data['uom'];
									$fabreqtotkg=($measurement/$tot_stripe_measurement)*$color_qty;//$color_val['fabreqtotkg'][$strip_color_id];
									$yarn_dyed=$stripe_data['yarn_dyed'];
									?>
									<td><?  echo  $color_name_arr[$stripe_color_id]; ?></td>
									<td align="right"> <? echo  number_format($measurement,2); ?></td>
									<td> <? echo  $unit_of_measurement[$uom]; ?></td>
									<td align="right"> <? echo  number_format($stripe_data['fabreq'],4); ?></td>
									<td> <? echo  $yes_no[$yarn_dyed]; ?></td>
							</tr>
									<?
									$total_fabreqtotkg+=$fabreqtotkg;
									$i++;

							}}
								
						}}
					?>
					<tfoot>
						<tr>
							<td colspan="3">Total </td>
							<td align="right">  <? echo  number_format($total_fab_qty,2); ?> </td>
							<td></td>
							<td></td>
							<td>   </td>
							<td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
						</tr>
					</tfoot>
				</table>
			 </td>
		 <td width="40%">
		<table class="rpt_table" width="100%"  border="1" cellpadding="0" cellspacing="0" rules="all" >
			<tr>
	             <td colspan="3" align="center"><b>Stripe Summery</b></td>
	        </tr>

	        <tr align="center">
	        	<th width="30"> SL</th>	        
	            <th width="70"> Stripe Color</th>	          
	            <th  width="70"> Qty.(KG)</th>
	           
	        </tr>

	        <?
			$i=1;$total_fab_qty=0;
			$total_fabreqtotkg=0;
			$fab_data_array=array();
			$stripe_wise_fabkg_arr=array();
			$color_from_library=return_field_value("color_from_library", "variable_order_tracking", "company_name=$cbo_company_name  and variable_list=23  and status_active=1 and is_deleted=0");
			//	if($db_type==0) $color_cond="d.fabric_color_id='".$color_id."'";
			//else if($db_type==2) $color_cond="nvl(d.fabric_color_id,0)=nvl('".$color_id."',0)";
			$stripe_wise_fabkg_sql=sql_select("select b.color_id as color_id,a.sample_prod_qty,c.body_part_id,c.color_type_id from sample_development_dtls a,sample_development_rf_color b,sample_development_fabric_acc c where a.sample_mst_id=b.mst_id and   b.dtls_id=c.id  and a.sample_mst_id=c.sample_mst_id and a.sample_color=b.color_id and   a.sample_mst_id in($mst_style_id) and a.sample_prod_qty>0	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

			// echo "<pre>";
			// print_r($stripe_arr);
			$si=1;
			foreach($stripe_wise_fabkg_sql as $vals)
			{
				$stripe_wise_fabkg_arr[$vals[csf("body_part_id")]][$vals[csf("color_type_id")]][$vals[csf("color_id")]] +=$vals[csf("sample_prod_qty")];
			}
	        foreach($body_part_arr as $body_id=> $color_data)
	        {
				foreach($color_data as $color_id=>$pre_cost_data)
				{
				foreach($pre_cost_data as $pre_cost_id=>$color_val){
					foreach($stripe_arr[$body_id][$color_id][$pre_cost_id] as $stripe_color_id=>$stripe_data)
					{
					?>
					<tr>
						<?
					
						$tot_stripe_measurement=$tot_stripe_measurement_arr[$color_id];
						$total_fab_qty+=$color_qty;
						//foreach($color_val['stripe_color'] as $strip_color_id=>$s_color_val)
						//{
							$measurement=$stripe_data['measurement'];
							$uom=$stripe_data['uom'];
							$fabreqtotkg=($measurement/$tot_stripe_measurement)*$color_qty;//$color_val['fabreqtotkg'][$strip_color_id];
							$yarn_dyed=$stripe_data['yarn_dyed'];
							?>
							<td ><?=$si;?> </td>
							<td><?  echo  $color_name_arr[$stripe_color_id]; ?></td>
						
							<td align="right"> <? echo  number_format($stripe_data['fabreq'],4); ?></td>
							
					</tr>
							<?
							$total_fabreqtotkg+=$fabreqtotkg;
							$si++;

					}}
						
				}}
			?>
	        <tfoot>
	        	<tr>
	        		<td >Total </td>
	        		<td align="right">  <? echo  number_format($total_fab_qty,2); ?> </td>        	
	        		
	        		<td align="right"><? echo  number_format($total_fabreqtotkg,2); ?> </td>
	        	</tr>
	        </tfoot>
		</table>
		</td>
		</tr>
		</table>
		<?
	}
	//$bookingId=$nameArray[0][csf('id')]
	?>

        <br/>


         <?
	$lib_designation_arr=return_library_array(" select id,custom_designation from lib_designation","id","custom_designation");
	$user_lib_designation_arr=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
	$user_lib_name_arr=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$mst_id=return_field_value("id as mst_id","wo_booking_mst","booking_no=$txt_booking_no","mst_id");
	//echo $mst_id.'ssD';
	//and b.un_approved_date is null
	 $approve_data_array=sql_select("select b.approved_by,min(b.approved_date) as approved_date from   approval_history b where b.mst_id=$mst_id and b.entry_form=13  group by  b.approved_by order by b.approved_by asc");

	 $unapprove_data_array=sql_select("select b.approved_by,b.approved_date,b.un_approved_reason,b.un_approved_date,b.approved_no from   approval_history b where b.mst_id=$mst_id and b.entry_form=13  order by b.approved_date,b.approved_by");

	?>
    <td width="49%" valign="top">
	<?
		
          if(count($approve_data_array)>0)
			{
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="5" style="border:1px solid black;">Approval Status</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="40%" style="border:1px solid black;">Name</th>
				<th width="30%" style="border:1px solid black;">Designation</th>
				<th width="27%" style="border:1px solid black;">Approval Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($approve_data_array as $row){


			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="40%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="27%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')]));?></td>

                </tr>
                <?
				$i++;
			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
		<br>
		<?
		if(count($unapprove_data_array)>0)
		{
			//and approval_type=2
			$sql_unapproved=sql_select("select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=13 and is_deleted=0 and status_active=1 and booking_id=$mst_id");
			//echo "select booking_id,approval_cause from fabric_booking_approval_cause where  entry_form=13 and approval_type=2 and is_deleted=0 and status_active=1 and booking_id=$mst_id";
			$unapproved_request_arr=array();
			foreach($sql_unapproved as $rowu)
			{
			$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
			}
 		?>
       <table  width="850" class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
            <thead>
            <tr style="border:1px solid black;">
                <th colspan="6" style="border:1px solid black;">Approval/Un Approval History</th>
                </tr>
                <tr style="border:1px solid black;">
                <th width="3%" style="border:1px solid black;">Sl</th>
				<th width="30%" style="border:1px solid black;">Name</th>
				<th width="20%" style="border:1px solid black;">Designation</th>
				<th width="5%" style="border:1px solid black;">Approval Status</th>
				<th width="20%" style="border:1px solid black;">Reason For Un Approval</th>
				<th width="22%" style="border:1px solid black;"> Date</th>

                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($unapprove_data_array as $row){

			?>
            <tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black; text-align:center"><? echo 'Yes';?></td>
				<td width="20%" style="border:1px solid black;"><? echo '';?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('approved_date')])); else echo "";?></td>
             </tr>
				<?
				$i++;
				$un_approved_date= explode(" ",$row[csf('un_approved_date')]);
				$un_approved_date=$un_approved_date[0];
				if($db_type==0) //Mysql
				{
					if($un_approved_date=="" || $un_approved_date=="0000-00-00") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}
				else
				{
					if($un_approved_date=="") $un_approved_date="";else $un_approved_date=$un_approved_date;
				}

				if($un_approved_date!="")
				{
				?>
			<tr style="border:1px solid black;">
                <td width="3%" style="border:1px solid black;"><? echo $i;?></td>
				<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_lib_name_arr[$row[csf('approved_by')]];?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $lib_designation_arr[$user_lib_designation_arr[$row[csf('approved_by')]]];?></td>
				<td width="5%" style="border:1px solid black;text-align:center;"><? echo 'No';?></td>
				<td width="20%" style="border:1px solid black;text-align:center"><? echo $unapproved_request_arr[$mst_id];?></td>
				<td width="22%" style="border:1px solid black;text-align:center"><? if($row[csf('un_approved_date')]!="") echo date ("d-m-Y h:i:s",strtotime($row[csf('un_approved_date')])); else echo "";?></td>
              </tr>

                <?
				$i++;
				}

			}
				?>
            </tbody>
        </table>
		<?
		}
		?>
        <br/>



        <?
		$sql_embelishment=sql_select("select b.emb_name,b.emb_type,b.cons_dzn_gmts,b.rate,b.amount from wo_pre_cost_embe_cost_dtls a left join wo_pre_cost_embe_cost_dtls_his b on a.id=b.pre_cost_embe_cost_dtls_id where a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.approved_no=$revised_no");

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
					$sql_embelishment=sql_select("select b.emb_name,b.emb_type,b.cons_dzn_gmts,b.rate,b.amount from wo_pre_cost_embe_cost_dtls a left join wo_pre_cost_embe_cost_dtls_his b on a.id=b.pre_cost_embe_cost_dtls_id where a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.approved_no=$revised_no");
					$i=0;
					//$total_yarn=0;
					foreach($sql_embelishment  as $row_embelishment)
                    {
						$i++;
						?>
						<tr align="center">
                            <td><? echo $i; ?></td>
                            <td><? echo $emblishment_name_array[$row_embelishment[csf('emb_name')]]; ?></td>
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
                            <td><? echo $row_embelishment[csf('cons_dzn_gmts')]; ?></td>
                            <td><? echo $row_embelishment[csf('rate')]; ?></td>
                            <td><? echo $row_embelishment[csf('amount')]; ?></td>
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









 		<table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <table align="left" cellspacing="0" width="<? echo $width?>"  border="1" rules="all" class="rpt_table" >

            <?
				   $sql_req=("select gmts_color_id as gmts_color,gmts_size,sum(bh_qty) as bh_qty,sum(rf_qty) as rf_qty  FROM wo_booking_dtls  WHERE booking_no=$txt_booking_no  and status_active=1 and is_deleted=0 and gmts_size!=0 and bh_qty>0 group by gmts_color_id,gmts_size  order by gmts_size");
				$sql_data =sql_select($sql_req);
				$size_array=array();$qnty_array_bh=array();$qnty_array_rf=array();
				foreach($sql_data as $row)
				{
					$size_array[$row[csf('gmts_size')]]=$row[csf('gmts_size')];
					$qnty_array_bh[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')];
					$qnty_array_rf[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('rf_qty')];
					$qnty_array[$row[csf('gmts_color')]][$row[csf('gmts_size')]]=$row[csf('bh_qty')]+$row[csf('rf_qty')];
				}
				 $sql_color=("select gmts_color_id as gmts_color,sum(bh_qty) as bh_qty  FROM wo_booking_dtls  WHERE booking_no=$txt_booking_no  and status_active=1 and is_deleted=0 and gmts_size!=0 and bh_qty>0 group by gmts_color_id  order by gmts_color");
				$sql_data_color =sql_select($sql_color);
				$color_array=array();
				foreach($sql_data_color as $row)
				{
					$color_array[$row[csf('gmts_color')]]=$row[csf('gmts_color')];
				}
				 $sizearr=return_library_array("select id,size_name from lib_size where status_active =1 and is_deleted=0","id","size_name");
				 $colorarr=return_library_array("select id,color_name from  lib_color where status_active =1 and is_deleted=0","id","color_name");
				 $width=400+(count($size_array)*150);
				 //count($size_array);
				 ?>


		        <thead align="center">
		         <tr>
		           		 <th align="left" colspan="<? echo count($size_array)+5;?>" width="30"><strong>Sample Requirement</strong></th>
		           </tr>
		            <tr>
		            <th width="30" rowspan="2">SL</th>
		            <th width="80" rowspan="2" align="center">Color/Size</th>
		            <?
		            foreach ($size_array as $sizid)
		            {
		            //$size_count=count($sizid);
		            ?>
		            <th width="" colspan="2"><strong><? echo  $sizearr[$sizid];  ?></strong>
		            </th>

		            <?
		            } ?>
		           <th width="80" rowspan="2" align="center">Total Qnty.</th>
		            </tr>
		            <tr>
		             <?
		            foreach ($size_array as $sizid)
		            {
		            //$size_count=count($sizid);
		            ?>
		            <th width="75"> BH &nbsp;</th> <th width="75"> Rf.&nbsp;</th>
		            <?
		            } ?>
		            </tr>
		        </thead>
		        <tbody>
					<?
		            //$mrr_no=$dataArray[0][csf('issue_number')];
		            $i=1;
		            $tot_qnty=array();
		                foreach($color_array as $cid)
		                {
		                    if ($i%2==0)
		                        $bgcolor="#E9F3FF";
		                    else
		                        $bgcolor="#FFFFFF";
							$color_count=count($cid);
		                    ?>
		                    <tr>
		                        <td><? echo $i;  ?></td>
		                        <td><? echo $colorarr[$cid]; ?></td>

		                         <?
								foreach ($size_array as $sizval)
								{
								//$size_count=count($sizid);
								$tot_qnty[$cid]+=$qnty_array[$cid][$sizval];
								$tot_qnty_size_bh[$sizval]+=$qnty_array_bh[$cid][$sizval];
								$tot_qnty_size_rf[$sizval]+=$qnty_array_rf[$cid][$sizval];
								?>
								<td width="75" align="right"> <? echo $qnty_array_bh[$cid][$sizval]; ?></td> <td width="75" align="right"> <? echo $qnty_array_rf[$cid][$sizval]; ?></td>
								<?

								} ?>

		                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
		                    </tr>
		                    <?
							$production_quantity+=$tot_qnty[$cid];
							$i++;
		                }
		            ?>
		        </tbody>
		        <tr>
		            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
		            <?
						foreach ($size_array as $sizval)
						{
							?>
		                    <td align="right"><?php echo $tot_qnty_size_bh[$sizval]; ?></td>
		                    <td align="right"><?php echo $tot_qnty_size_rf[$sizval]; ?></td>
		                    <?
						}
					?>
		            <td align="right"><?php echo $production_quantity; ?></td>
		        </tr>
		    </table>
       		<br>
        </table>



        <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="49%" style="border:solid; border-color:#000; border-width:thin" valign="top">
                    <table  width="100%"  border="0" cellpadding="0" cellspacing="0">
                	<thead>
                    	<tr>
                        	<th width="3%"></th><th width="97%" align="left"><u>Special Instruction</u></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" valign="top">
                                    <td style="vertical-align:top"><? echo $i;?></td>
                                    <td><strong style="font-size:20px"> <? echo $row[csf('terms')]; ?></strong></td>
                                </tr>
                            <?
						}
					}
					/*else
					{
				    $i=0;
					$data_array=sql_select("select id, terms from  lib_terms_condition");// quotation_id='$data'
					foreach( $data_array as $row )
						{
							$i++;
					?>
                    <tr id="settr_1" align="">
                                    <td valign="top">
                                    <? echo $i;?>
                                    </td>
                                    <td>
                                    <? echo $row['terms']; ?>
                                    </td>

                                </tr>
                    <?
						}
					} */
					?>
                </tbody>
                </table>
                </td>
                <td width="2%">
                </td>
                <td width="49%" valign="top">
                <?
                if(str_replace("'","",$cbo_fabric_source)==1)
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
	d.item_number_id,
	d.costing_per,
	b.po_break_down_id,
	b.color_size_table_id,
	b.requirment,
	c.po_number
	FROM
		wo_pre_cost_fabric_cost_dtls a left join wo_pre_cost_fabric_cost_dtls_h d on a.id=d.pre_cost_fabric_cost_dtls_id,
		wo_pre_cos_fab_co_avg_con_dtls b,
		wo_po_break_down c
	WHERE
	a.job_no=b.job_no and
	a.job_no=c.job_no_mst and
	d.approved_no=$revised_no and 
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
					/*$booking_qnty=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and booking_type in(1,4)  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by a.po_break_down_id", "po_break_down_id", "grey_fab_qnty");*/
					$booking_qnty_main=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b, wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no  and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and a.is_short=2 and c.item_category=2 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");

					$booking_qnty_short=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =1 and c.fabric_source=$cbo_fabric_source and c.item_category=2 and a.is_short=1 and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					$booking_qnty_sample=return_library_array( "select max(a.po_break_down_id) as po_break_down_id ,sum(a.grey_fab_qnty) as grey_fab_qnty  from wo_booking_dtls a, wo_po_break_down b , wo_booking_mst c  where a.job_no =b.job_no_mst and  a.job_no =c.job_no and  a.booking_no =c.booking_no and a.po_break_down_id =b.id and a.po_break_down_id in(".str_replace("'","",$txt_order_no_id).") and a.booking_type =4 and c.fabric_source=$cbo_fabric_source and c.item_category=2  and a.status_active=1 and a.is_deleted=0 group by b.po_number order by po_break_down_id", "po_break_down_id", "grey_fab_qnty");
					//echo "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by id";
					$sql_data=sql_select( "select max(a.id) as id,  a.po_number,max(a.pub_shipment_date) as pub_shipment_date,sum(a.plan_cut) as plan_cut  from wo_po_break_down a,wo_pre_cost_sum_dtls b,wo_pre_cost_mst c where a.job_no_mst=b.job_no and a.job_no_mst=c.job_no and a.id in(".str_replace("'","",$txt_order_no_id).") group by a.po_number order by id");
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

          <?
		 	echo signature_table(5, $cbo_company_name, "1330px");
			echo "****".custom_file_name($varcode_booking_no,$style_sting,$txt_job_no);
		 ?>
       </div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
       <?

}
?>