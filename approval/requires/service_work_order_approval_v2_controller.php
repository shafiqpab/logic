<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_ip=$_SESSION['logic_erp']['user_ip'];

extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
//require_once('../../mailer/class.phpmailer.php');
//$from_mail="PLATFORM-ERP@fakir.app";
	
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];


 
function getSequence($parameterArr=array()){
	$lib_dept_arr=implode(',',(array_keys($parameterArr['lib_dept_arr'])));
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,ENTRY_FORM,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT as DEPARTMENT_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){		
		if($rows['DEPARTMENT_ID']==''){$rows['DEPARTMENT_ID']=$lib_dept_arr;}
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
 
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_dept_arr=implode(',',(array_keys($parameterArr['lib_dept_arr'])));

	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,DEPARTMENT  as DEPARTMENT_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0  order by SEQUENCE_NO";
	//echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		if($rows['DEPARTMENT_ID']==''){$rows['DEPARTMENT_ID']=$lib_dept_arr;}
		$usersDataArr[$rows['USER_ID']]['DEPARTMENT_ID']=explode(',',$rows['DEPARTMENT_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}

	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			//if( in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID']) &&  $bbtsRows['buyer_id']>0 ){
				$finalSeq[$sys_id][$user_id]=$seq;
			//}
		}
	}

 
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}



if ($action=="load_drop_down_store")
{
	$permitted_store_id=return_field_value("STORE_LOCATION_ID","user_passwd","id='".$user_id."'");
	if($permitted_store_id){$storCon=" and id in($permitted_store_id)";}
	echo create_drop_down( "cbo_store_id", 130, "select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and company_id=$data $storCon order by store_name","id,store_name", 1, "-- All --","","load_drop_down( 'requires/service_work_order_approval_v2_controller', this.value, 'load_drop_down_item', 'category_id' );",0,"","","","");
	exit();
}

// if ($action=="load_drop_down_item")
// {
// 	echo create_drop_down( "cbo_item_category_id", '100', "select id, brand_name from lib_buyer_brand brand where buyer_id='$data' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
// 	exit();
// }

if ($action=="load_drop_down_item")
{
	$permitted_buyer_id=return_field_value("ITEM_CATEGORY_ID","lib_store_location","id='".$data."'");
	if($permitted_buyer_id){$buyerCon=" and id in($permitted_buyer_id)";}
	echo create_drop_down( "cbo_item_category_id", 130, "select buy.category_id, buy.short_name from lib_item_category_list buy where buy.status_active =1 and buy.is_deleted=0 $buyerCon","category_id,short_name", 1, "-- All --","", "");

	// echo "select buy.category_id, buy.short_name from lib_item_category_list buy where buy.status_active =1 and buy.is_deleted=0 $buyerCon";
	exit();
}




if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
}

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name"  );
$item_cat_arr=return_library_array( "select id, SHORT_NAME from LIB_ITEM_CATEGORY_LIST", "id", "SHORT_NAME"  );
$lib_store_arr=return_library_array( "select id, STORE_NAME from LIB_STORE_LOCATION", "id", "STORE_NAME"  );
$department_arr=return_library_array( "SELECT ID,DEPARTMENT_NAME FROM LIB_DEPARTMENT WHERE STATUS_ACTIVE=1 AND IS_DELETED=0",'ID','DEPARTMENT_NAME');
$location_arr=return_library_array( "SELECT ID,LOCATION_NAME FROM LIB_LOCATION WHERE COMPANY_ID=$cbo_company_name and STATUS_ACTIVE=1 AND IS_DELETED=0",'ID','LOCATION_NAME');







if($action=="report_generate")
{
	
	?>

	<script>
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			var title = 'Approval Cause Info';
			var page_link = 'requires/service_work_order_approval_v2_controller.php?data='+data+'&action=appcause_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);
			}
		}

		function openmypage_reqdetails(requ_id,requ_no)
		{
			var data=requ_id+"**"+requ_no;

			//alert(data);
			var title = 'WO Details Info';
			var page_link = 'requires/service_work_order_approval_v2_controller.php?data='+data+'&action=reqdetails_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				/*var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);*/
			}
		}
	</script>
	<?
	
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company_name = str_replace("'","",$cbo_company_name);
   
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	
	$cbo_item_category_id = str_replace("'","",$cbo_item_category_id);
	$cbo_store_id = str_replace("'","",$cbo_store_id);
	$cbo_wo_year = str_replace("'","",$cbo_wo_year);
	$txt_wo_no = str_replace("'","",$txt_wo_no);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	
	$approval_type = str_replace("'","",$cbo_approval_type);
	$alter_user_id = str_replace("'","",$txt_alter_user_id);
	$app_user_id=($alter_user_id!='') ? $alter_user_id:$user_id;
	
	if($cbo_item_category_id){$searchCon .=" and b.ITEM_CATEGORY=$cbo_item_category_id";}
	if($cbo_store_id){$searchCon .=" and a.STORE_NAME=$cbo_store_id";}
	
	if ($txt_wo_no != ''){$woCon .=" and a.WO_NUMBER_PREFIX_NUM=$txt_wo_no";}
	if ($txt_date_from != '' && $txt_date_to != '')
	{
		if($db_type==0)
		{
			$txt_date_from = date("Y-m-d", strtotime($txt_date_from));
			$txt_date_to = date("Y-m-d", strtotime($txt_date_to));
			$searchCon .= " and a.requisition_date between '$txt_date_from' and '$txt_date_to'";
		}
		else
		{
			$txt_date_from = date("d-M-Y", strtotime($txt_date_from));
			$txt_date_to = date("d-M-Y", strtotime($txt_date_to));
			$searchCon .= " and a.requisition_date between '$txt_date_from' and '$txt_date_to'";

		}	
	}
	
		// if($db_type==0)
		// {
		// 	if ($cbo_wo_year != 0) $searchCon.= " and year(a.insert_date)=$cbo_wo_year";
		// }
		// else
		// {
		// 	if ($cbo_wo_year != 0) $searchCon.= " and TO_CHAR(a.insert_date,'YYYY')=$cbo_wo_year";
		// }	

	$category_mixing_variable = return_field_value("allocation","variable_settings_inventory","company_name=$company_name and variable_list=44 and status_active=1 and is_deleted=0 order by id desc ","allocation");

 	
	//813=>Purchase Requisition Approval
	$electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>60,'user_id'=>$app_user_id,'lib_dept_arr'=>$department_arr,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));
  
  //print_r($cbo_company_name);

 
	if($approval_type==0) // Un-Approve
	{
		
		// //Match data..................................
		// // if($electronicDataArr['user_by'][$app_user_id][ITEM_ID] && $category_mixing_variable == 2){
		// // 	$where_con .= " and b.ITEM_CATEGORY in(".$electronicDataArr['user_by'][$app_user_id]['ITEM_ID'].",0)";
		// // 	$electronicDataArr[sequ_by][0][ITEM_ID]=$electronicDataArr['user_by'][$app_user_id]['ITEM_ID'];
		// //   }
		//   if($electronicDataArr['user_by'][$app_user_id]['DEPARTMENT_ID']){
		// 	$where_con .= " and a.DEPARTMENT_ID in(".$electronicDataArr['user_by'][$app_user_id]['DEPARTMENT_ID'].",0)";
		// 	$electronicDataArr[sequ_by][0]['DEPARTMENT_ID']=$electronicDataArr['user_by'][$app_user_id]['DEPARTMENT_ID'];
		//   }
		//   if($electronicDataArr['user_by'][$app_user_id]['STORE_ID']){
		// 	$where_con .= " and a.STORE_NAME in(".$electronicDataArr['user_by'][$app_user_id]['STORE_ID'].",0)";
		// 	$electronicDataArr[sequ_by][0]['STORE_ID']=$electronicDataArr['user_by'][$app_user_id][STORE_ID];
		//   }

		//   if($electronicDataArr['user_by'][$app_user_id]['LOCATION_ID']){
		// 	$where_con .= " and a.LOCATION_ID in(".$electronicDataArr['user_by'][$app_user_id]['STORE_ID'].",0)";
		// 	$electronicDataArr[sequ_by][0]['LOCATION_ID']=$electronicDataArr['user_by'][$app_user_id]['LOCATION_ID'];
		//   }

		 
			// $data_mas_sql = " select a.ID,a.STORE_NAME,a.DEPARTMENT_ID,a.LOCATION_ID, b.ITEM_CATEGORY from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.ENTRY_FORM=69 and a.is_approved<>1 and a.READY_TO_APPROVE=1  and a.COMPANY_ID=$company_name $where_con $searchCon";//and a.is_mixed_category=2
			//   echo $data_mas_sql; die;


              $data_mas_sql = "SELECT a.ID, a.WO_NUMBER_PREFIX_NUM,a.DEPARTMENT_ID, a.WO_NUMBER, a.COMPANY_NAME, a.WO_DATE , a.IS_APPROVED 
              from wo_non_order_info_mst a
              where a.COMPANY_NAME=$cbo_company_name and a.ENTRY_FORM=484 and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.READY_TO_APPROVED=1 and a.IS_APPROVED<>1 $where_con
              group by a.ID, a.WO_NUMBER_PREFIX_NUM, a.WO_NUMBER, a.COMPANY_NAME,a.DEPARTMENT_ID, a.INSERT_DATE, a.WO_DATE, a.IS_APPROVED 
              order by a.ID";//and a.is_mixed_category=2
			 //echo $data_mas_sql; die; 

              
             $tmp_sys_id_arr=array();
             $data_mast_sql_res=sql_select( $data_mas_sql );
            // print_r($data_mast_sql_res);
             foreach ($data_mast_sql_res as $row)
             { 
                 for($seq=($electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
                     if($electronicDataArr['sequ_by'][$seq]['BYPASS']==1){
                         $tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
                     }
                     else{
                         $tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
                         break;
                     }
                 }
             }
		//..........................................Match data;
		
		
// 		echo "<pre>";
// print_r($tmp_sys_id_arr); 
// echo "</pre>";die();
		
		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
 			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');
			
			if($tmp_sys_id_arr[$seq]){
				if($sql!=''){$sql .=" UNION ALL ";}
				$sql .= " SELECT a.ID, a.WO_NUMBER_PREFIX_NUM,a.DEPARTMENT_ID, a.WO_NUMBER, a.COMPANY_NAME, a.WO_DATE ,TO_CHAR(a.INSERT_DATE,'YYYY') as YEAR, a.IS_APPROVED,B.AMOUNT AS WO_VALUE, c.item_category_id AS ITEM_CATTEGORY, c.item_description  AS ITEM_DESCRIPTION, b.SERVICE_FOR AS SERVICE_FOR
				from wo_non_order_info_mst a,wo_non_order_info_dtls b
                left join product_details_master c on b.item_id=c.id
				where a.id=b.mst_id and a.COMPANY_NAME=$cbo_company_name $woCon and a.ENTRY_FORM=484 and a.APPROVED_SEQU_BY=$seq   $sys_con and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.READY_TO_APPROVED=1 and a.IS_APPROVED<>1 $where_con
				group by a.ID, a.WO_NUMBER_PREFIX_NUM,B.AMOUNT, a.WO_NUMBER, a.COMPANY_NAME,a.DEPARTMENT_ID, a.INSERT_DATE, a.WO_DATE, a.IS_APPROVED, c.item_description, b.SERVICE_FOR,c.item_category_id  ";

				//and a.is_mixed_category=2
			
			}
		
		}
		
	}
	else
	{
		
		$sql = "SELECT a.ID, a.WO_NUMBER_PREFIX_NUM,a.DEPARTMENT_ID, a.WO_NUMBER, a.COMPANY_NAME, a.WO_DATE ,TO_CHAR(a.INSERT_DATE,'YYYY') as YEAR, a.IS_APPROVED,B.AMOUNT AS WO_VALUE, c.item_category_id AS ITEM_CATTEGORY, c.item_description  AS ITEM_DESCRIPTION, b.SERVICE_FOR AS SERVICE_FOR
		from APPROVAL_MST d,wo_non_order_info_mst a,wo_non_order_info_dtls b
		left join product_details_master c on b.item_id=c.id
		where a.id=b.mst_id and a.COMPANY_NAME=$cbo_company_name $woCon and a.ENTRY_FORM=484 and d.SEQUENCE_NO={$electronicDataArr['user_by'][$app_user_id]['SEQUENCE_NO']}  and a.APPROVED_SEQU_BY=d.SEQUENCE_NO and d.entry_form=60 and d.mst_id=a.id  and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and a.READY_TO_APPROVED=1 and a.IS_APPROVED<>0 $where_con
		group by a.ID, a.WO_NUMBER_PREFIX_NUM,B.AMOUNT, a.WO_NUMBER, a.COMPANY_NAME,a.DEPARTMENT_ID, a.INSERT_DATE, a.WO_DATE, a.IS_APPROVED,B.AMOUNT,c.item_description, b.SERVICE_FOR,c.item_category_id";
		//and a.is_mixed_category=2		
    }
	 // echo $sql;die;
 
		
	$mst_id_arr=array();$item_arr=array();$item_des_arr=array();$service_arr=array();$amount_arr=array();$data_arr=array();
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row)
	{ 
		$mst_id_arr[$row[ID]]=$row[ID];
		$data_arr[$row[csf('COMPANY_NAME')]][$row[csf('WO_NUMBER')]]['ID']=$row[csf("ID")];
		$data_arr[$row[csf('COMPANY_NAME')]][$row[csf('WO_NUMBER')]]['WO_NUMBER']=$row[csf("WO_NUMBER")];
		$data_arr[$row[csf('COMPANY_NAME')]][$row[csf('WO_NUMBER')]]['COMPANY_NAME']=$row[csf("COMPANY_NAME")];

		$data_arr[$row[csf('COMPANY_NAME')]][$row[csf('WO_NUMBER')]]['ITEM_CATTEGORY'].=$item_category[$row[csf("ITEM_CATTEGORY")]].",";

		$data_arr[$row[csf('COMPANY_NAME')]][$row[csf('WO_NUMBER')]]['ITEM_DESCRIPTION'].=$row[csf("ITEM_DESCRIPTION")].",";

		$data_arr[$row[csf('COMPANY_NAME')]][$row[csf('WO_NUMBER')]]['SERVICE_FOR'].=$service_for_arr[$row[csf("SERVICE_FOR")]].",";

		$data_arr[$row[csf('COMPANY_NAME')]][$row[csf('WO_NUMBER')]]['WO_VALUE']+=$row[csf("WO_VALUE")];
		$data_arr[$row[csf('COMPANY_NAME')]][$row[csf('WO_NUMBER')]]['YEAR']=$row[csf("YEAR")];
		$data_arr[$row[csf('COMPANY_NAME')]][$row[csf('WO_NUMBER')]]['WO_DATE']=$row[csf("WO_DATE")];
		$data_arr[$row[csf('COMPANY_NAME')]][$row[csf('WO_NUMBER')]]['DEPARTMENT_ID']=$row[csf("DEPARTMENT_ID")];
		$data_arr[$row[csf('COMPANY_NAME')]][$row[csf('WO_NUMBER')]]['IS_APPROVED']=$row[csf("IS_APPROVED")];
	}

//  echo "<pre>";
// print_r($mst_id_arr); 
//   echo "</pre>";die();
  
	$hostory_sql=sql_select( "select MST_ID,APPROVED_BY, APPROVED_DATE from approval_history where current_approval_status=1  and mst_id in (".implode(',',$mst_id_arr).")");

	
	
	foreach ($hostory_sql as $row)
	{ 
		$history_data[LAST_APP_DATE][$row[MST_ID]]=$row[APPROVED_DATE];
		$history_data[LAST_APP_BY][$row[MST_ID]]=$row[APPROVED_BY];
	}
   
  

	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=19 and report_id =206 and is_deleted=0 and status_active=1");
    $format_ids=explode(",",$print_report_format);
    //echo "<pre>";print_r($format_ids);
	
	$user_arr=return_library_array( "select id, user_name from user_passwd", 'id', 'user_name' );	
	
	
	$sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=60  and is_deleted=0 and status_active=1 and BOOKING_ID in(".implode(',',$mst_id_arr).")");
	$unapproved_request_arr=array();
	$approval_case_arr=array();
	foreach($sql_unapproved as $rowu)
	{
		if($rowu[csf('approval_type')]==2)
		{
			$unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
		}
		$approval_case_arr[$rowu[csf('booking_id')]][$rowu[csf('approval_type')]]=$rowu[csf('approval_cause')];
	}
	

	//echo "select * from fabric_booking_approval_cause where  entry_form=60  and is_deleted=0 and status_active=1 and PAGE_ID in(".implode(',',$mst_id_arr).")";
	
	
	$width=1300;
    
    ?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:<?= $width+20; ?>px; margin-top:10px">
        <legend>PI Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" align="left" >
                <thead>
                    <th width="20"></th>
                    <th width="35">SL</th>                   
                    <th width="120">Work Order No</th>
                    <th width="50">Year</th>
                    <th width="100">Service For</th>
                    <th width="100">Item Category</th>
                    <th width="100">Item Description</th>
                    <th width="100">Depatment</th>                    
                    <th width="60">Work order Value</th>                                       
                    <th width="80">Work Order Date</th> 
                    <th  width="130">Last Approval Date and Person</th>
					<th width="100">Un-approve request</th>
					<? if($approval_type==0){?><th width="100">Not Appv. Cause</th><? }?>
					<th>&nbsp;</th>
                                                         
                </thead>
            </table>            
            <div style="width:<?= $width+20; ?>px; overflow-y:scroll; max-height:330px; float:left;" id="buyer_list_view">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" id="tbl_list_search" align="left">
                    <tbody>
                        <?						 
                        $i=1; $all_approval_id=''; $j=0;
						
                        foreach ($data_arr as $company=>$com_data)
                        {  
							foreach ($com_data as $wo_no=>$row)
                        {                                             
                             $unapprove_value_id=$row[ID];
							 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
									$app_id = sql_select("select id from approval_history where mst_id='".$row[ID]."' and entry_form='1'  order by id desc");									
									$value=$row[csf('id')]."**".$app_id[0][csf('id')];
								}

								$variable='';
								if ($format_ids[$j]==118) $type=1;// Print Report With Group
                                elseif($format_ids[$j]==119)  $type=2; // Print Report Without Group
								else if($format_ids[$j]==120) $type=3; // Print Report 
                                elseif($format_ids[$j]==121) $type=4; //Print Report 2
                                elseif($format_ids[$j]==122) $type=5; // Print Report 3
                                elseif($format_ids[$j]==123) $type=6; // Print Report 4
                                elseif($format_ids[$j]==129)  $type=7; // Print 5
                                elseif($format_ids[$j]==169) $type=8; // Print Report 6
                                elseif($format_ids[$j]==165)  $type=9; // Print Report 7
                                elseif($format_ids[$j]==227) $type=10; // Print Report 8                            
                                elseif($format_ids[$j]==241) $type=11; // Print Report 11 
                                elseif($format_ids[$j]==580)  $type=12; // Print Report 5
                                elseif($format_ids[$j]==28)  $type=13; // Print Report 13 
                                elseif($format_ids[$j]==280)  $type=14; // Print 14
                                elseif($format_ids[$j]==688) $type=15; // Re-Order Level
                                elseif($format_ids[$j]==243)  $type=16; // Item wise
                                elseif($format_ids[$j]==310) $type=17; // Category Wise
                                elseif($format_ids[$j]==304)  $type=18; // Print 15
                                elseif($format_ids[$j]==719) $type=19; // Print 16
                                elseif($format_ids[$j]==723) $type=20; // Print 17
                                elseif($format_ids[$j]==339)  $type=21; // Print 18
                                elseif($format_ids[$j]==370) $type=22; // Print 19
                                elseif($format_ids[$j]==382)  $type=23; //Print Out5
                                elseif($format_ids[$j]==235) $type=24; // Print 9                             
                                elseif($format_ids[$j]==768) $type=25; // Print 20  
                                elseif($format_ids[$j]==419) $type=26; // Print 22 
								elseif($format_ids[$j]==732) $type=27; // Po Print  
                                elseif($format_ids[$j]==86) $type=28; // print   
                                elseif($format_ids[$j]==84) $type=29; // print 2   
                                elseif($format_ids[$j]==85) $type=30; // print   
                                else  $type=0;

                                $variable="<a href='#'  title='".$format_ids[$j]."'  onclick=\"print_report('".$row[COMPANY_NAME]."','".$row[ID]."','Service Work Order','".$row[IS_APPROVED]."','".$type."')\"> ".$row[WO_NUMBER]." <a/>";

								$SERVICE_FOR=implode(",",array_filter(array_unique(explode(",",$row['SERVICE_FOR'])))); 

								$ITEM_DESCRIPTION=implode(",",array_filter(array_unique(explode(",",$row['ITEM_DESCRIPTION'])))); 

								$ITEM_CATTEGORY=implode(",",array_filter(array_unique(explode(",",$row['ITEM_CATTEGORY']))));
						   
						   		?>
                                <tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')" id="tr_<?= $i; ?>"> 
                                    <td width="20" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<?= $i;?>" name="tbl[]"  />
                                        <input type="hidden" id="target_id_<?= $i;?>" name="target_id_[]"  value="<?=$row[ID]; ?>" />                                                  
                                    </td> 
                                    <td width="35" align="center"><p><?= $i;?></p></td>
                                    <td width="120" align="center"><?=$variable;?></td>
                                    <td width="50" align="center"><p><?= $row[YEAR]; ?></p></td>
									<td width="100" align="center"><p><?=$SERVICE_FOR;?></td>								
                                    <td width="100"><?=$ITEM_CATTEGORY;?></td>
					
									<td width="100" align="center"><p><?=$ITEM_DESCRIPTION?></p></td>	                                  
                                    <td width="100" align="center"><p><?= $department_arr[$row[DEPARTMENT_ID]]; ?></p></td>      
                                    <td width="60" align="right"><?=$row[csf("WO_VALUE")];?></td>      
                                    <td width="80" align="center"><?= $row[WO_DATE]; ?></td>      
									<td width="130" align="center"><p><? echo $history_data[LAST_APP_DATE][$row[ID]] .'<br>'.$user_arr[$history_data[LAST_APP_BY][$row[ID]]]; ?></p></td>
                                    
                                    
									<td width="100" align="center"><p>
									<?
										if($approval_type==1)
										{
											$unapproved_request=$unapproved_request_arr[$row[ID]]; 
											if($unapproved_request!='')
											{
												$view_request='View';
											}
										}
										else
										{
											$unapproved_request=''; 
											$view_request='';
										}
										
									?>
									<a href="#report_details" onClick="openmypage('<? echo $unapproved_request; ?>','unapprove_request_action','Unapprove Request Details')"><? echo $view_request; ?></a>
									  </p>&nbsp; 
									</td>
									  <? 
									if($approval_type==0)
									{
										$casues=$approval_case_arr[$unapprove_value_id][$approval_type]
										?>
										 <td width="100" align="center" style="word-break:break-all">
	                                        	<input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<?=$i;?>" style="width:70px" value="<? echo $casues;?>" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<?=$unapprove_value_id; ?>,<?=$approval_type; ?>,<?=$i;?>)">&nbsp;
	                                    </td>
										<? 
									}

									?>
									<td align="center"><input type="button" class="formbutton" id="reqdtls_<? echo $i;?>" style="width:100px" value="Req. Details" onClick="openmypage_reqdetails(<? echo $row[ID]; ?>, '<? echo $row[WO_NUMBER]; ?>')"/></td>
                                    
                                       
                                </tr>
                                <?
                                $i++;
                        } }

                        ?>
                    </tbody>
                </table>
            </div>
			
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $width; ?>" class="rpt_table" align="left" >
				<tfoot>
                     <td width="20" align="center" valign="middle">
                    	<input type="checkbox" id="all_check" onClick="check_all('all_check')" />
                        <input type="hidden" name="hide_approval_type" id="hide_approval_type" value="<?= $approval_type; ?>">
                    </td>
                    <td align="left">
                        <input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<?= $i; ?>,<?= $approval_type; ?>,<?= $user_id; ?>)"/>&nbsp;&nbsp;&nbsp;
						<input type="button" value="Deny" class="formbutton" style="width:100px; display:<?=($approval_type==1)?'none':'';?> " onClick="submit_approved(<?=$i; ?>,5,<?= $user_id; ?>);"/>
                    </td>
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

	$cbo_company_name = str_replace("'","",$cbo_company_name);
	//$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$approval_type = str_replace("'","",$approval_type);
	$target_ids = str_replace("'","",$target_ids);
	$target_app_id_arr = explode(',',$target_ids);	
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$app_user_id=($txt_alter_user_id)?$txt_alter_user_id:$user_id;	





	//echo "10**".'zdhgdsfgsgf';die;

	//............................................................................
	
	$sql = "select a.ID  from wo_non_order_info_mst a where a.COMPANY_NAME=$cbo_company_name and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.id in($target_ids)";
    //echo $sql;die();
	$sqlResult=sql_select( $sql );
	foreach ($sqlResult as $row)
	{
        //if($row['READY_TO_APPROVE'] != 1){echo '21**Ready to approve yes is mandatory';exit();}
		$matchDataArr[$row['ID']]=array('department_id'=>0,'brand_id'=>0,'supplier_id'=>0,'store'=>0);
	}
    $finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>60,'lib_dept_arr'=>$department_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
	
 
	$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
	$user_sequence_no = $finalDataArr['user_seq'][$app_user_id];
  //print_r($user_sequence_no) ;

	if($approval_type==5)
	{

		$rID1=sql_multirow_update("wo_non_order_info_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY",'0*0*0',"id",$target_ids,0);
		if($rID1) $flag=1; else $flag=0;

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$app_user_id."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=60 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

				
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=60 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		
		 // echo "191**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		$response=$target_ids;
		if($flag==1) $msg='50'; else $msg='51';

	} 
	else if($approval_type==0)
	{      
 		
		$id=return_next_id( "id","approval_mst", 1 ) ;
		$ahid=return_next_id( "id","approval_history", 1 ) ;	
		
		$target_app_id_arr = explode(',',$target_ids);	
        foreach($target_app_id_arr as $mst_id)
        {		
			if($data_array!=''){$data_array.=",";}
			$data_array.="(".$id.",60,".$mst_id.",".$user_sequence_no.",".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
			$id=$id+1;
			
			$approved_no=$max_approved_no_arr[$mst_id][$app_user_id]+1;
			if($history_data_array!="") $history_data_array.=",";
			$history_data_array.="(".$ahid.",60,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$app_user_id.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			$ahid++;
			
			//mst data.......................
			$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
			$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no."")); 
        }
	 
 

        $flag=1;
		if($flag==1) 
		{
			$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
			//echo "10**insert into approval_mst ($field_array) values" . $data_array;die;
			$rID1=sql_insert("approval_mst",$field_array,$data_array,0);
			



			if($rID1) $flag=1; else $flag=0; 
		}
		
		if($flag==1) 
		{
			$field_array_up="IS_APPROVED*APPROVED_SEQU_BY"; 
			$rID2=execute_query(bulk_update_sql_statement( "wo_non_order_info_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=60 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1);
			if($rID3) $flag=1; else $flag=0;
		}
		 
		if($flag==1)
		{
			$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING";
			$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
			if($rID4) $flag=1; else $flag=0;
		}
		
		//echo "21**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
		
		if($flag==1) $msg='19'; else $msg='21';

        
	}
	else
	{              
		
		$next_user_app = sql_select("select id from approval_history where mst_id in($target_ids) and entry_form=60 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
				
		if(count($next_user_app)>0)
		{
			echo "25**unapproved"; 
			disconnect($con);
			die;
		}

		$rID1=sql_multirow_update("wo_non_order_info_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY",'0*0*0',"id",$target_ids,0);
		if($rID1) $flag=1; else $flag=0;


		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=60 and mst_id in ($target_ids)";
			$rID2=execute_query($query,1);
			if($rID2) $flag=1; else $flag=0;
		}

		
		if($flag==1) 
		{
			$query="delete from approval_mst  WHERE entry_form=60 and mst_id in ($target_ids)";
			$rID3=execute_query($query,1); 
			if($rID3) $flag=1; else $flag=0; 
		}
		

		
		if($flag==1)
		{
			$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$app_user_id."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=60 and current_approval_status=1 and mst_id in ($target_ids)";
			$rID4=execute_query($query,1);
			if($rID4) $flag=1; else $flag=0;
		}
 		
		//echo "5**".$rID1.",".$rID2.",".$rID3.",".$rID4.$flag;oci_rollback($con);die;
		
		$response=$target_ids;
		if($flag==1) $msg='20'; else $msg='22';
		
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




if ($action=="save_update_delete_appv_cause")
{
	//$approval_id
	$approval_type=str_replace("'","",$appv_type);


	if($approval_type==0)
	{

   		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process ));

		if ($operation==0|| $operation==1)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=60 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=60 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

			//echo "shajjad_".$approved_no_history.'_'.$approved_no_cause; die;

			if($approved_no_history=="" && $approved_no_cause=="")
			{
				//echo "insert"; die;

				$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

				$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
				$data_array="(".$id_mst.",".$page_id.",60,".$user_id.",".$wo_id." ,".$appv_type.",0,".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=60 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
				$data_array="".$page_id."*60*".$user_id."*".$wo_id."*".$appv_type."*0*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=60 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=60 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",60,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=60 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*1*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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
			else if($approved_no_history!="" && $approved_no_cause=="")
			{
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=60 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=60 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

				if($max_appv_no_his!=$max_appv_no_cause)
				{
					$id_mst=return_next_id( "id", "fabric_booking_approval_cause", 1 ) ;

					$field_array="id,page_id,entry_form,user_id,booking_id,approval_type,approval_no,approval_cause,inserted_by,insert_date,status_active,is_deleted";
					$data_array="(".$id_mst.",".$page_id.",60,".$user_id.",".$wo_id." ,".$appv_type.",".$max_appv_no_his.",".$appv_cause.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
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

					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=60 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");

					$field_array="page_id*entry_form*user_id*booking_id*approval_type*approval_no*approval_cause*updated_by*update_date*status_active*is_deleted";
					$data_array="".$page_id."*60*".$user_id."*".$wo_id."*".$appv_type."*".$max_appv_no_his."*".$appv_cause."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";

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

	}//type=0	
}


if($action=="reqdetails_popup")
{ 
	echo load_html_head_contents("Requ. Details","../../", 1, 1, $unicode,1);
	extract($_REQUEST);
	$ex_data=explode("**",$data);
	//print_r($ex_data);

	$sql="SELECT a.ID ,b.ID as DTLS_ID, SUM(B.AMOUNT) AS AMOUNT,b.SERVICE_FOR, b.SUPPLIER_ORDER_QUANTITY as QNTY, b.RATE, b.REMARKS, b.UOM as  MEASUREMENT,c.ITEM_GROUP_ID,LISTAGG(DISTINCT c.item_category_id, ', ') WITHIN GROUP (ORDER BY a.ID) AS ITEM_CATTEGORY,LISTAGG(DISTINCT c.item_description, ', ') WITHIN GROUP (ORDER BY a.ID) AS ITEM_DESCRIPTION,LISTAGG(DISTINCT b.SERVICE_DETAILS, ', ') WITHIN GROUP (ORDER BY a.ID) AS SERVICE_DETAILS
	from wo_non_order_info_mst a,wo_non_order_info_dtls b
	left join product_details_master c on b.item_id=c.id
	where a.id=b.mst_id   and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.id=$ex_data[0] and  b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 
	group by a.ID,b.ID, a.WO_NUMBER_PREFIX_NUM,B.AMOUNT, a.WO_NUMBER,b.SERVICE_FOR, b.SUPPLIER_ORDER_QUANTITY, b.RATE, b.REMARKS, b.UOM,c.ITEM_GROUP_ID ";


	//echo $sql;die();
	$sql_res=sql_select($sql);

	$item_name_arr=return_library_array("SELECT id,item_name from lib_item_group", "id","item_name");
		 
	?>
	<script>
		var permission='<? echo $permission; ?>';
		function js_set_value()
		{
			parent.emailwindow.hide(); 
		}

		function fn_requisition_details_qtyupdate(operation)
		{
			var tot_row=$('#tbl_details tbody tr').length;
			var data_all="";
			for(var i=1; i<=tot_row; i++)
			{
				if (form_validation('txtqty_'+i,'Quantity')==false)
				{
					return;
				}
				data_all = data_all+get_submitted_data_string('txtqty_'+i+'*req_dtls_id_'+i,"../");				
			}

			var data="action=save_update_delete_requ_qty&tot_row="+tot_row+data_all;
			//alert (data);//return;
			freeze_window(operation);
			http.open("POST","service_work_order_approval_v2_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fn_requisition_details_qtyupdate_reponse;					
		}

		function fn_requisition_details_qtyupdate_reponse()
		{
			if(http.readyState == 4)
			{				
				var reponse=http.responseText.split('**');
				if (reponse[0]==1) alert("Data is updated Successfully");
				else alert("Data is not updated Successfully");
				release_freezing();
			}
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <? echo load_freeze_divs ("../../",$permission,1); ?>
    	<fieldset style="width:900px; margin-top:10px;">
        <legend>Purchase Requisition Details</legend>
        <form name="purchaserequisition_2" id="purchaserequisition_2" autocomplete="off">
        	<table class="rpt_table" width="950" cellspacing="0" cellpadding="0" align="center" id="tbl_details">
                <thead>
                    <tr>
                    	<th width="100">Service For</th>
						<th width="100">Service Details</th>
	                    <th width="100">Item Category</th>
	                    <th width="100">Item Group</th>
	                    <th width="150">Item Description.</th>
	                    
	                    <th width="60"> UOM</th>
	                    <th class="must_entry_caption" title="Must Entry Field." width="80"> <font color="blue">Quantity</font></th>
	                    <th width="50">Rate</th>
	                    <th width="100">Remarks</th>
	                    
                	</tr>
            	</thead>               
                <tbody>
                	<?
                	$i=1;
                	$bgcolor="#E9F3FF";
                	foreach ($sql_res as $row) 
                	{    
	                	?>
	                    <tr bgcolor="<?= $bgcolor; ?>">
	                    	 <input type="hidden" name="req_dtls_id[]" id="req_dtls_id_<?= $i; ?>" value="<?= $row[csf('dtls_id')]; ?>">
	                        <td><?= $service_for_arr[$row[csf('SERVICE_FOR')]]; ?></td>
	                        <td><?= $row[SERVICE_DETAILS];?></td>
	                        <td><?= $item_category[$row[csf('ITEM_CATTEGORY')]]; ?></td>  
	                        <td><?= $item_name_arr[$row[csf('ITEM_GROUP_ID')]]; ?></td>
	                        <td><?= $row[ITEM_DESCRIPTION];?></td>
	                        <td><?= $service_uom_arr[$row[csf('MEASUREMENT')]]; ?></td>
	                        <td align="right"><input type="text" name="txtqty[]" id="txtqty_<?= $i; ?>" style="width:80px" class="text_boxes_numeric" value="<? echo $row[csf('QNTY')]; ?>" /></td>
							<td align="right"><?= number_format($row[csf('RATE')],2); ?></td>
							<td align="right"><?= $row[REMARKS];?></td>
							
	                    </tr>
	                    <?
	                    $i++;
	                }
	                ?>    
                </tbody>
            </table>

                <table width="100%">
                	<tr>
                        <td colspan="22" height="20" valign="middle" align="center" class="button_container"> 
                            <input type="button" class="formbutton" id="updateqtyid" name="updateqtyid" value="Update" onClick="fn_requisition_details_qtyupdate(1)" style="width:80px" />                           
                        </td>    
                    </tr>
                </table>
        </form>
	    </fieldset>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?	
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
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=60 and user_id='$user_id' and booking_id='$wo_id' and approval_type=$app_type and status_active=1 and is_deleted=0";
		//echo $sql_cause; //die;
		$nameArray_cause=sql_select($sql_cause);
		if(count($nameArray_cause)>0){
			foreach($nameArray_cause as $row)
			{
				$app_cause1=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
				$app_cause = str_replace(array("\r", "\n"), ' ', $app_cause1);
			}
		}
		else
		{
			$app_cause = '';
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
				http.open("POST","service_work_order_approval_v2_controller.php",true);
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

				var reponse=http.responseText.split('**');
				show_msg(reponse[0]);

				set_button_status(1, permission, 'fnc_appv_entry',1);
				release_freezing();
				appv_cause= $("#appv_cause").val();

			document.getElementById('hidden_appv_cause').value=appv_cause;

			parent.emailwindow.hide();

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
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"><?php echo $app_cause;?></textarea>
                        <input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                        <input type="hidden" name="appv_type" class="text_boxes" ID="appv_type" value="<? echo $app_type; ?>" style="width:30px" />
                        <input type="hidden" name="page_id" class="text_boxes" ID="page_id" value="<? echo $menu_id; ?>" style="width:30px" />
                        <input type="hidden" name="user_id" class="text_boxes" ID="user_id" value="<? echo $user_id; ?>" style="width:30px" />
                        <input type="hidden" name="approval_id" class="text_boxes" ID="approval_id" value="<? echo $approval_id; ?>" style="width:30px" />
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

if($action=='user_popup')
{
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
		//$Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
		$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name  and a.id!=$user_id  and b.is_deleted=0 and  b.page_id not in ($menu_id) and b.entry_form=60 order by b.sequence_no";

		
			 //echo $sql;die;
		$arr=array (2=>$custom_designation,3=>$department_arr);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
		?>
	</form>
	<?
	exit();
}


if ($action=="save_update_delete_requ_qty")
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	for ($i=1; $i<=$tot_row; $i++)
    {
		$req_dtls_id = "req_dtls_id_".$i;
		if(str_replace("'","",$$req_dtls_id)) $all_req_dtls_ids[str_replace("'","",$$req_dtls_id)]=str_replace("'","",$$req_dtls_id);
	}
	
	if(count($all_req_dtls_ids)>0)
	{
		$requisition_arr=return_library_array( "select id, rate from inv_purchase_requisition_dtls where id in(".implode(",",$all_req_dtls_ids).") ", 'id', 'rate' );
	}
	//echo "10**<pre>";print_r($requisition_arr);oci_rollback($con);disconnect($con);die;
	$field_array_up = "quantity*amount*updated_by*update_date";
	for ($i=1; $i<=$tot_row; $i++)
    {
		$txtqty = "txtqty_".$i;
		$req_dtls_id = "req_dtls_id_".$i;
		$amount=str_replace("'",'',$$txtqty)*$requisition_arr[str_replace("'",'',$$req_dtls_id)];
		$updateID_array[] = str_replace("'",'',$$req_dtls_id);
		$data_array_up[str_replace("'",'',$$req_dtls_id)] = explode("*",("".$$txtqty."*'".$amount."'*".$user_id."*'".$pc_date_time."'"));	
	}

	//echo bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$field_array_up,$data_array_up,$updateID_array);
	$dtlsrID=execute_query(bulk_update_sql_statement("inv_purchase_requisition_dtls","id",$field_array_up,$data_array_up,$updateID_array),1);

	if($db_type==0)
	{
		if($dtlsrID)
		{
			mysql_query("COMMIT");
			echo "1**";
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**";
		}
	}
	if($db_type==2 || $db_type==1)
	{
	    if($dtlsrID)
		{
			oci_commit($con);
			echo "1**";
		}
		else
		{
			oci_rollback($con);
			echo "10**";
		}
	}
	disconnect($con);
	die;
}

if ($action=="unapprove_request_action")
{
	echo load_html_head_contents("Un Approval Request","../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data_all=explode('_',$data);
	$requ_unapprove=$data_all[1];
	//$unapp_request=$data_all[1];
?>
<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="unappv_request" id="unappv_request" readonly class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"><? echo $requ_unapprove;?> </textarea>
                        <Input type="hidden" name="wo_id" class="text_boxes" ID="wo_id" value="<? echo $wo_id; ?>" style="width:30px" />
                       
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                
               
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>	  
                </tr>
            </table>
            </fieldset>
            </form>
			  <script src="../includes/functions_bottom.js" type="text/javascript"></script>
        </div>

<?

exit();
}


if ($action=="send_requisition_app_mail")
{


	$user_maill_arr=return_library_array("select id,USER_EMAIL from USER_PASSWD","id","USER_EMAIL");
	//$department_arr=return_library_array( "select id, department_name from lib_department", 'id', 'department_name' );
	$user_arr=return_library_array( "select id, user_name from user_passwd", 'id', 'user_name' );	

	list($sysId,$app_user_id,$company_id,$type)=explode('__',$data);
	$sysId=str_replace('*',',',$sysId);
	if($mailId)$mailToArr[]=str_replace('*',',',$mailId);
	//  echo $data;


		
		$sql = " select A.COMPANY_ID,A.ID, A.REQU_NO, A.WO_NUMBER_PREFIX_NUM ,A.REMARKS, A.COMPANY_ID, TO_CHAR(a.insert_date,'YYYY') AS YEAR, listagg(b.item_category, ',') within group (order by b.item_category) as ITEM_CATEGORY_ID , A.REQUISITION_DATE, A.DELIVERY_DATE, 0 AS APPROVAL_ID, A.IS_APPROVED, A.DEPARTMENT_ID, SUM(B.AMOUNT) AS REQ_VALUE,a.INSERTED_BY from inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b where a.id=b.mst_id and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.is_approved<>1 and a.READY_TO_APPROVE=1 and a.ENTRY_FORM=69 and a.id in($sysId) group by A.COMPANY_ID,a.id,a.remarks, a.company_id, a.requ_no, a.WO_NUMBER_PREFIX_NUM ,TO_CHAR(a.insert_date,'YYYY'), a.requisition_date, a.delivery_date, a.is_approved, a.department_id,a.INSERTED_BY";
		//echo $sql;
		$sql_dtls=sql_select($sql);
		
		$mst_id_arr=array();
		foreach ($sql_dtls as $row)
		{ 
			$mst_id_arr[$row[ID]]=$row[ID];
		}
	  
		$hostory_sql=sql_select( "select MST_ID,APPROVED_BY, APPROVED_DATE from approval_history where current_approval_status=1 and entry_form=60 and mst_id in (".implode(',',$mst_id_arr).")");
		foreach ($hostory_sql as $row)
		{ 
			$history_data[LAST_APP_DATE][$row[MST_ID]]=$row[APPROVED_DATE];
			$history_data[LAST_APP_BY][$row[MST_ID]]=$row[APPROVED_BY];
		}
   
		
		$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","entry_form=60 and user_id=$app_user_id and company_id ={$sql_dtls[0][COMPANY_ID]} and is_deleted = 0");

		$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1  AND a.entry_form=60 and a.SEQUENCE_NO>$user_sequence_no and a.company_id={$sql_dtls[0][COMPANY_ID]} order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
		 //echo $elcetronicSql;die;
		
		$elcetronicSqlRes=sql_select($elcetronicSql);
		foreach($elcetronicSqlRes as $rows){
			
			if($rows[BUYER_ID]!=''){
				foreach(explode(',',$rows[BUYER_ID]) as $bi){
					if($rows[USER_EMAIL]!='' && $bi==$buyer_name_id){$mailToArr[]=$rows[USER_EMAIL];}
					if($rows[BYPASS]==2){break;}
				}
			}
			else{
				if($rows[USER_EMAIL]){$mailToArr[]=$rows[USER_EMAIL];}
				if($rows[BYPASS]==2){break;}
			}

		}

		if($type == 5){$subject = "Purchase Requisition is deny";}
		else{$subject = "Purchase Requisition App Notification";}

		
		ob_start();	
		?>
		<p><b>Dear Concern,</b>	<br />			
		Below  MPR is ready to approve for you.</p>
		<table rules="all" border="1">
			<tr bgcolor="#CCCCCC">
				<td>SL</td>
				<td>Requisition No</td>
				<td>Year</td>
				<td>Item Category</td>
				<td>Depatment</td>
				<td>Requisition Value</td>
				<td>Requisition Date</td>
				<td>Last Approval Date and Person</td>
			</tr>
			
			<?php 
			$i=1;
			foreach($sql_dtls as $row){ 
				if($user_maill_arr[$row[INSERTED_BY]]){$mailToArr[$row[INSERTED_BY]]=$user_maill_arr[$row[INSERTED_BY]];}
				if($user_maill_arr[$app_user_id]){$mailToArr[$app_user_id]=$user_maill_arr[$app_user_id];}
			?>
			<tr>
				<td><?=$i;?></td>
				<td><?=$row[REQU_NO];?></td>
				<td><?=$row[YEAR];?></td>
				<td>
				<?
					$item_name_arr=array();
					foreach(explode(',',$row[ITEM_CATEGORY_ID]) as $item_id){
						$item_name_arr[$item_id]=$item_category[$item_id];
					}
					echo implode(', ',$item_name_arr);
				?>                
                </td>
				<td><?= $department_arr[$row[DEPARTMENT_ID]]; ?></td>
				<td><?= $row[REQ_VALUE]; ?></td>
				<td><?= change_date_format($row[REQUISITION_DATE]); ?></td>
				<td><? echo $history_data[LAST_APP_DATE][$row[ID]] .'<br>'.$user_arr[$history_data[LAST_APP_BY][$row[ID]]]; ?></td>
			</tr>
			<?php $i++;} ?>
		</table>
		<?	
			
			$message=ob_get_contents();
			ob_clean();
			
			
			require_once('../../mailer/class.phpmailer.php');
			require_once('../../auto_mail/setting/mail_setting.php');
			
			
			$header=mailHeader();
			$to=implode(',',$mailToArr);
			
			if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
			echo $to. $message;
			exit();	

}
?>