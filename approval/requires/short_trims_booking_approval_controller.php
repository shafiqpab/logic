<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];

include('../../includes/common.php');
include('../../includes/class4/class.conditions.php');
include('../../includes/class4/class.reports.php');
include('../../includes/class4/class.fabrics.php');
include('../../includes/class4/class.yarns.php');
include('../../includes/class4/class.conversions.php');
include('../../includes/class4/class.trims.php');
include('../../includes/class4/class.emblishments.php');
include('../../includes/class4/class.washes.php');
include('../../includes/class4/class.commercials.php');
include('../../includes/class4/class.commisions.php');
include('../../includes/class4/class.others.php');
//echo $action;die;

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );  
	exit();
	
}

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
$po_number_arr=return_library_array("select id,po_number from wo_po_break_down", "id", "po_number");
$trim_group= return_library_array("select id, item_name from lib_item_group",'id','item_name');

function getSequence($parameterArr=array()){
	$lib_buyer_arr=implode(',',(array_keys($parameterArr['lib_buyer_arr'])));
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,ENTRY_FORM,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,SUPPLIER_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0 order by SEQUENCE_NO";
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

	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,SUPPLIER_ID,BRAND_ID,DEPARTMENT FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0  order by SEQUENCE_NO";
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
			//if( in_array($bbtsRows['buyer_id'],$usersDataArr[$user_id]['BUYER_ID']) &&  $bbtsRows['buyer_id']>0 ){
				$finalSeq[$sys_id][$user_id]=$seq;
			//}
		}
	}

 
	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}

if($action=="report_generate")//for Urmi
{ 
	$process = array( &$_POST );
	//print_r($process);
	extract(check_magic_quote_gpc( $process ));  
	$sequence_no='';
	$cbo_company_name=str_replace("'","",$cbo_company_name); 
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$booking_year=str_replace("'","",$cbo_booking_year);
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
    $user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	// if($cbo_buyer_id==0){$cbo_buyer_id="'%%'";}

	$approval_type = str_replace("'","",$cbo_approval_type);

	$electronicDataArr=getSequence(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>85,'user_id'=>$user_id_approval,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));
   //print_r($buyer_arr);

	//if ($booking_year=="" || $booking_year==0) $booking_year_cond=""; else $booking_year_cond=" and to_char(a.insert_date,'YYYY')='".trim($booking_year)."' ";
	if($db_type==0)
	{
		if(str_replace("'","",$booking_year)!=0) $booking_year_cond=" and year(a.insert_date)=".str_replace("'","",$booking_year).""; else $booking_year_cond="";
	}
	else
	{
		if(str_replace("'","",$booking_year)!=0) $booking_year_cond=" and to_char(a.insert_date,'YYYY')=".str_replace("'","",$booking_year).""; else $booking_year_cond="";
	}

	
	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.booking_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.booking_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.booking_date=$txt_date";
		else $date_cond='';
	}

	
	$booking_cond="";
	if($txt_booking_no!="") $booking_cond=" and a.booking_no like '%$txt_booking_no%'";

	$buyer_id_cond = '';
	if($cbo_buyer_name != 0){
		$buyer_id_cond .= " and a.buyer_id =$cbo_buyer_name";
	}

	// $buyer_id_cond = '';
	// if($cbo_buyer_name != 0){
	// 	$buyer_id_cond .= " and a.buyer_name =$cbo_buyer_name";
	// }

	
	

	
	if($approval_type==0) // Un-Approve
	{  
		// if($electronicDataArr['user_by'][$user_id_approval]['BUYER_ID']){
		// 	$where_con .= " and a.BUYER_NAME in(".$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'].",0)";
		// 	$electronicDataArr['sequ_by'][0]['BUYER_ID']=$electronicDataArr['user_by'][$user_id_approval]['BUYER_ID'];
		//  }
        $data_mast_sql ="select a.ID,a.entry_form, a.booking_no_prefix_num as prefix_num,a.booking_no from wo_booking_mst a where a.company_id=$cbo_company_name and a.IS_APPROVED<>1 and a.entry_form=262 and a.is_deleted=0  and a.ready_to_approved=1";
       
       // echo $data_mast_sql;die;


          $tmp_sys_id_arr=array();
		 $data_mast_sql_res=sql_select( $data_mast_sql );
		 
		 foreach ($data_mast_sql_res as $row)
		 { 
			 for($seq=($electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
				 
				if($electronicDataArr['sequ_by'][$seq]['BYPASS']==1){
					 $tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
				 }
				 else{
					 $tmp_sys_id_arr[$seq][$row['ID']]=$row['ID'];
					 break;
				 }

			 }
		 }
	 
	
		// 	  echo "<pre>";
		// 	  print_r($tmp_sys_id_arr);die;
		//    echo "</pre>";die();
		
		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');

			if($tmp_sys_id_arr[$seq]){
				if($sql!=''){$sql .=" UNION ALL ";}
				//$approved_user_cond=" and c.approved_by='$user_id'";
				$sql.="select a.id,a.entry_form, a.booking_no_prefix_num as prefix_num,a.booking_no,a.item_category, a.fabric_source, a.company_id, a.entry_form,a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$cbo_company_name and a.item_category in(4) and a.is_deleted=0 and a.entry_form=262 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.IS_APPROVED<>1  and a.APPROVED_SEQU_BY=$seq $sys_con and a.ready_to_approved=1  $buyer_id_cond  $date_cond $booking_cond $booking_year_cond  group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type,a.entry_form, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.job_no, a.is_approved, a.po_break_down_id";
				



			}
		}
	}
	else
	{   $sql="select a.id,a.entry_form, a.booking_no_prefix_num as prefix_num,a.booking_no,a.item_category, a.fabric_source, a.company_id, a.entry_form,a.booking_type, a.is_short, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date, '0' as approval_id, a.job_no, a.is_approved, a.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_po_details_master c,APPROVAL_MST d where a.booking_no=b.booking_no and b.job_no=c.job_no and a.company_id=$cbo_company_name and a.item_category in(4) and a.is_deleted=0 and a.entry_form=262 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.IS_APPROVED<>0 and a.ready_to_approved=1 and d.SEQUENCE_NO={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']} and a.APPROVED_SEQU_BY=d.SEQUENCE_NO and d.entry_form=85 and d.mst_id=a.id  $buyer_id_cond  $date_cond $booking_cond $booking_year_cond  group by a.id,a.booking_no_prefix_num,a.booking_no,a.entry_form, a.item_category, a.fabric_source, a.company_id, a.booking_type,a.entry_form, a.is_short, a.buyer_id, a.pay_mode, a.supplier_id, a.delivery_date, a.booking_date,a.job_no, a.is_approved, a.po_break_down_id ";


    }
   //.echo $sql;die();
     

 		$print_report_format_ids=return_field_value("format_id","lib_report_template","template_name=".$company_name."  and module_id=2 and report_id in(26) and is_deleted=0 and status_active=1");
		$format_ids=explode(",",$print_report_format_ids);
		$format_ids=$format_ids[0];
		
		$print_report_format_ids2=return_field_value("format_id","lib_report_template","template_name=".$company_name."  and module_id=2 and report_id in(5) and is_deleted=0 and status_active=1");
		$format_ids2=explode(",",$print_report_format_ids2);
		$format_ids2=$format_ids2[0];
		
		$print_report_format_ids3=return_field_value("format_id","lib_report_template","template_name=".$company_name."  and module_id=2 and report_id in(6,15) and is_deleted=0 and status_active=1");
		$format_ids3=explode(",",$print_report_format_ids3);
		$format_ids3=$format_ids3[0];

		$print_report_format_ids4=return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name."  and module_id=2 and report_id in(57) and is_deleted=0 and status_active=1");
		$format_ids4=explode(",",$print_report_format_ids4);
		$format_ids4=$format_ids4[0];
        //print_r($format_ids4);
		$print_report_format_ids5=return_field_value("format_id","lib_report_template","template_name=".$company_name."  and module_id=2 and report_id=219 and is_deleted=0 and status_active=1");
		$format_ids5=explode(",",$print_report_format_ids5);
		$format_ids5=$format_ids5[0];

		$print_report_format_ids='';
		$format_ids='';	
		$print_report_format_ids2='';
		$format_ids2='';
		$print_report_format_ids3='';
		$format_ids3='';	
	

	$nameArray_buyer=sql_select( "select  a.buyer_id as buyer_name,a.booking_no  from wo_non_ord_samp_booking_mst a   where a.status_active=1 and a.is_deleted=0 $booking_id_cond $buyer_id_condnon2 $buyer_id_condnon $date_cond $booking_cond $booking_year_cond"); 
	$non_booking_buyer=array();
	foreach ($nameArray_buyer as  $value) {
		$non_booking_buyer[$value[csf('booking_no')]]=$value[csf('buyer_name')];
	}
	unset($nameArray_buyer);

	$nameArray_buyer=sql_select( "select distinct c.buyer_name,b.booking_no,a.ID from wo_po_details_master c, wo_booking_dtls b,wo_booking_mst a where c.job_no=b.job_no and a.booking_no=b.booking_no and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond $buyer_id_cond2  $date_cond $booking_cond $booking_year_cond "); 
	 
	//echo "select distinct c.buyer_name,b.booking_no  from wo_po_details_master c, wo_booking_dtls b,wo_booking_mst a where c.job_no=b.job_no and a.booking_no=b.booking_no and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond $buyer_id_cond2   $date_cond $booking_cond $booking_year_cond ";

	$booking_buyer=array();
	foreach ($nameArray_buyer as  $value) {
		$booking_buyer[$value[csf('booking_no')]][$value[csf('buyer_name')]]=$value[csf('buyer_name')];
	}
	unset($nameArray_buyer);

 
	$nameArray=sql_select($sql);
	$booking_id_arr=array();
	foreach ($nameArray as  $rows) {
		$booking_id_arr[$rows[csf('id')]]=$rows[csf('id')];
	}

 

	$sql_cause="select booking_id,MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=85 and user_id={$_SESSION['logic_erp']['user_id']} ".where_con_using_array($booking_id_arr,0,'booking_id')." and approval_type=$cbo_approval_type and status_active=1 and is_deleted=0 group by booking_id";				
	$nameArray_cause=sql_select($sql_cause);
	foreach($nameArray_cause as $row)
	{
		$app_cause_arr[$row[csf('booking_id')]]=return_field_value("approval_cause", "fabric_booking_approval_cause", "id='".$row[csf("id")]."' and status_active=1 and is_deleted=0");
	}

 
	
	 $sql_req="select booking_id,approval_cause from fabric_booking_approval_cause where entry_form=85 ".where_con_using_array($booking_id_arr,0,'booking_id')."  and approval_type=2 and status_active=1 and is_deleted=0";				
	$nameArray_req=sql_select($sql_req);
	foreach($nameArray_req as $row)
	{
		$unappv_req_arr[$row[csf('booking_id')]]=$row[csf('approval_cause')];
	}
 
  //print_r($unappv_req_arr);
 
	?>
    <script>
	function openmypage_app_instrac(wo_id,app_type,i)
	{
		var txt_appv_instra = $("#txt_appv_instra_"+i).val();	
		var approval_id = $("#approval_id_"+i).val();
		
		var data=wo_id+"_"+app_type+"_"+txt_appv_instra+"_"+approval_id;
		
		var title = 'Approval Instruction';	
		var page_link = 'requires/short_trims_booking_approval_controller.php?data='+data+'&action=appinstra_popup';
		  
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
		var page_link = 'requires/short_trims_booking_approval_controller.php?data='+data+'&action=appcause_popup';
		  
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
		var page_link = 'requires/short_trims_booking_approval_controller.php?data='+data+'&action=unappcause_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=180px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var unappv_request=this.contentDoc.getElementById("hidden_unappv_request");
			
			$('#txt_unappv_req_'+i).val(unappv_request.value);
		}
	}
	</script>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1075px; margin-top:10px">
        <legend>Trims Booking Approval</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1075" class="rpt_table" >
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
                    <?
					if($approval_type==0) echo "<th width='80'>Appv Instra</th>";
					if($approval_type==1) echo "<th width='80'>Un-appv request</th>"; 
					?>
                    <th><? if($approval_type==0) echo "Not Appv. Cause" ; else echo "Not Un-Appv. Cause"; ?></th>
                    
                </thead>
            </table>
            <div style="width:1077px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1059" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1;
                            // echo $sql;
                           
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$value='';
								if($approval_type==0) $value=$row[csf('id')]; else $value=$row[csf('id')]."**".$row[csf('approval_id')]; 
								
								$value2=$row[csf('id')];
								
								//echo $row[csf('booking_type')]."<br/>";//die;
								//if($row[csf('booking_type')]==4) $booking_type="Sample";
									
									
									$buyer_string="";
									foreach ($booking_buyer[$row[csf('booking_no')]] as $result_buy)
									{
										$buyer_string.=$buyer_arr[$result_buy].",";
									} 
							
								// 	if($row[csf('booking_type')]==5) $booking_type="None Order"; else $booking_type="Order"; 
								// 	$buyer_string="";
		 
								// 	foreach ($non_booking_buyer[$row[csf('booking_no')]] as $result_buy)
								// 	{
								// 		$buyer_string.=$buyer_arr[$result_buy].",";
								// 	}	
								// }
								 
								//entry_form=857
								 $trim_button='';
								 //$booking_type=$row[csf('booking_type')];
								$report_type=2;
							 //echo $format_ids.'='.$row[csf('entry_form')].'= '.$booking_type;//die;
								
								
								 if($row[csf('entry_form')]==262) 
								//Multi Job Wise Short Trims Booking Urmi
								 //Multi Job Wise Short Trims Booking -woven
								{
									
									if($format_ids4==67) //print_booking1
									{ 
										$trim_button="<a href='#' onClick=\"generate_trim_booking_report('".$row[csf('booking_no')]."','".$report_type."','".$row[csf('company_id')]."','".$row[csf('is_short')]."','".$row[csf('is_approved')]."','".$row[csf('entry_form')]."','show_trim_booking_report2','".$i."')\" title='formate_id=".$format_ids.";\n entry_form=".$row[csf('entry_form')]."'>".$row[csf('booking_no')]."<a/>";
										//$row[csf('prefix_num')]
									}
									
									if($format_ids4==19) //print_booking2 
									{ 
										$trim_button="<a href='#' onClick=\"generate_trim_booking_report('".$row[csf('booking_no')]."','".$report_type."','".$row[csf('company_id')]."','".$row[csf('is_short')]."','".$row[csf('is_approved')]."','".$row[csf('entry_form')]."','show_trim_booking_report3','".$i."')\" title='formate_id=".$format_ids.";\n entry_form=".$row[csf('entry_form')]."'>".$row[csf('booking_no')]." <a/>";
										//$row[csf('prefix_num')]
									}
									if($format_ids4==16) //print_booking4 
									{ 
										$trim_button="<a href='#' onClick=\"generate_trim_booking_report('".$row[csf('booking_no')]."','".$report_type."','".$row[csf('company_id')]."','".$row[csf('is_short')]."','".$row[csf('is_approved')]."','".$row[csf('entry_form')]."','show_trim_booking_report4','".$i."')\" title='formate_id=".$format_ids.";\n entry_form=".$row[csf('entry_form')]."'>".$row[csf('booking_no')]." <a/>";
										//$row[csf('prefix_num')]
									}	
								}
								
								
								
								if($trim_button=="") $trim_button=$row[csf('booking_no')];
								 
								
								
								 
								//show_fabric_booking_report
								// echo $row[csf('entry_form')];
								$supplierName="";
								if($row[csf('pay_mode')]==3 ||  $row[csf('pay_mode')]==5) $supplierName=$company_arr[$row[csf('supplier_id')]]; else $supplierName=$supplier_arr[$row[csf('supplier_id')]];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" title="<?=$row[csf('entry_form')]?>"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<?= $row['ID']; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no[]" type="hidden" value="<? echo $row[csf('booking_no')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $row[csf('approval_id')]; ?>" />
                                        <input id="<? echo strtoupper($row[csf('booking_no')]); ?>" name="no_booook[]" type="hidden" value="<? echo $i;?>" />
                                    
                                    </td>   
									<td width="40" align="center"><? echo $i; ?></td>
                                    
                                    <td width="80">
                                    	<p>
                                        
                                        <a href='##' style='color:#000' onClick="generate_comment_popup('<? echo $row[csf('booking_no')]; ?>',<? echo $row[csf('company_id')]; ?>,'show_trim_comment_report')">
									<? //echo $row[csf('booking_no')]; ?>View</a>
                                   
									</p>
                                    </td>
									<td width="130">
                                    	<p><? //echo $row[csf('booking_no')]; ?><? echo $trim_button;?></p>
                                    </td>
                                    <td width="80" align="center"><p>Short</p></td>
									<td width="100" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>

                                    <td width="125"><p><? echo rtrim($buyer_string,","); //$buyer_arr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
									<td width="160" style="word-break:break-all" title="Paymode_id=<? echo $row[csf('pay_mode')];?>"><? echo $supplierName; ?>&nbsp;</td>
									<td align="center"><? if($row[csf('delivery_date')]!="0000-00-00") echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
                                    
                                      <?
										if($approval_type==0)echo "<td align='center' width='80'>
                                        		<Input name='txt_appv_instra[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_appv_instra_".$i."' style='width:70px' maxlength='50' onClick='openmypage_app_instrac(".$value2.",".$approval_type.",".$i.")'></td>";
											if($approval_type==1)echo "<td align='center' width='80'>
                                        		<Input name='txt_unappv_req[]' class='text_boxes' readonly placeholder='Please Browse' ID='txt_unappv_req_".$i."' value='".$unappv_req_arr[$row[csf('id')]]."' style='width:70px' maxlength='50' onClick='openmypage_unapp_request(".$value2.",".$approval_type.",".$i.")'></td>"; 
                                        ?>
                                        <td align="center">
                                        	<Input name="txt_appv_cause[]" class="text_boxes" readonly placeholder="Please Browse" ID="txt_appv_cause_<? echo $i;?>" style="width:97px" value="<?=$app_cause_arr[$rows[csf('id')]];?>" maxlength="50" title="Maximum 50 Character" onClick="openmypage_app_cause(<? echo $value2; ?>,<? echo $approval_type; ?>,<? echo $i;?>)">&nbsp;</td>
                                            
								</tr>
								<?
								 
								$i++;
							}
							$isApp="";
							if($approval_type==1) $isApp=" display:none"; else $isApp="";
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1075" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" style=" <?=$isApp; ?>"><input type="checkbox" id="all_check" onClick="check_all('all_check')" /></td>
                    <td colspan="2" align="left">
						<?
						if($approval_type==1){
							$denyBtn=" display:none";
							$denyBtnMsg="";	
						}
						else{
							$denyBtnMsg="Deny";
						}
						?>
						<input type="button" value="<? if($approval_type==1) echo "Un-Approve"; else echo "Approve"; ?>" class="formbutton" style="width:100px" onClick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/>

						<input type="button" value="<?=$denyBtnMsg; ?>" class="formbutton" style="width:100px;<?=$denyBtn; ?>" onClick="submit_approved(<?=$i; ?>,5);"/>
						<input type="hidden" id="txt_selected_id" value="">
					</td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
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
			$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and a.id!=$user_id  and b.company_id=$cbo_company_name  and valid=1  and b.is_deleted=0 and b.entry_form=85 order by b.SEQUENCE_NO";
			//echo $sql;die;
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

function auto_approved($dataArr=array()){
		global $pc_date_time;
		global $user_id;
		$sys_id_arr=explode(',',$dataArr[sys_id]);
		
		$queryText = "select a.id,a.SETUP_DATE,b.APPROVAL_NEED,b.ALLOW_PARTIAL,b.PAGE_ID from APPROVAL_SETUP_MST a,APPROVAL_SETUP_DTLS b where a.id=b.MST_ID and a.COMPANY_ID=$dataArr[company_id] and b.PAGE_ID=$dataArr[app_necessity_page_id] and a.STATUS_ACTIVE =1 and a.IS_DELETED=0  and b.STATUS_ACTIVE =1 and b.IS_DELETED=0 order by a.SETUP_DATE desc";
		$queryTextRes = sql_select($queryText);
		
		if($queryTextRes[0][ALLOW_PARTIAL]==1){
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

	if ($action=="approve")
	{ 
		$process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
		$con = connect();
		
		
		$msg=''; $flag=''; $response='';
		
		$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
		$user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
	
		
	
		//............................................................................
		
		$sql = "select a.ID,a.BUYER_ID,a.READY_TO_APPROVED  from wo_booking_mst a where a.COMPANY_ID=$cbo_company_name and a.STATUS_ACTIVE=1 and a.IS_DELETED=0  and a.id in($booking_ids)";
		//echo $sql;die();
		$sqlResult=sql_select( $sql );
		foreach ($sqlResult as $row)
		{
			if($row['READY_TO_APPROVED'] != 1){echo '21**Ready to approve Yes is mandatory';exit();}
			$matchDataArr[$row['ID']]=array('buyer_id'=>$row['BUYER_NAME'],'brand_id'=>0,'store'=>0);
		}
		
		$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>85,'lib_buyer_arr'=>$buyer_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
		
	 
		$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
		$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];
	  //print_r($user_sequence_no) ;die;
	
		
		 if($approval_type==0)
		{ 
			 
			$id=return_next_id( "id","approval_mst", 1 ) ;
			$ahid=return_next_id( "id","approval_history", 1 ) ;	
			
			$target_app_id_arr = explode(',',$booking_ids);	
			//print_r($target_app_id_arr);
			foreach($target_app_id_arr as $mst_id)
			{
				if($data_array!=''){$data_array.=",";}
				$data_array.="(".$id.",85,'".$mst_id."','".$user_sequence_no."',".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
				$id=$id+1;
				
				$approved_no=$max_approved_no_arr[$mst_id][$user_id_approval]+1;
				if($history_data_array!="") $history_data_array.=",";
				$history_data_array.="(".$ahid.",85,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				$ahid++;
				
				//mst data.......................
				$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
				$data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no.",'".$pc_date_time."',".$user_id_approval."")); 
			}
		 
	 
	
			$flag=1;
			if($flag==1) 
			{  
				$field_array="id, entry_form, mst_id,  sequence_no,approved_by, approved_date,INSERTED_BY,INSERT_DATE,user_ip";
				//echo "10**insert into approval_mst($field_array) values $data_array";die;
				$rID1=sql_insert("approval_mst",$field_array,$data_array,0);
				if($rID1) $flag=1; else $flag=0; 
			}
			   
			if($flag==1) 
			{
				$field_array_up="IS_APPROVED*APPROVED_SEQU_BY*APPROVED_DATE*APPROVED_BY"; 
				$rID2=execute_query(bulk_update_sql_statement( "wo_booking_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
				if($rID2) $flag=1; else $flag=0; 
			}
	
			if($flag==1)
			{
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=85 and mst_id in ($booking_ids)";
				$rID3=execute_query($query,1);
				if($rID3) $flag=1; else $flag=0;
			}
			 
			if($flag==1)
			{
				$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING";
				$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
				if($rID4) $flag=1; else $flag=0;
			}

			
			
			//echo "24444**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
			
			if($flag==1) $msg='19'; else $msg='21';
	
			
		}
	
		else if($approval_type==5)
		{
			$max_approved_no_arr=return_library_array( "select mst_id, max(approved_no) as approved_no from approval_history where entry_form=85 and mst_id in($booking_ids)  and APPROVED=2 and APPROVED_BY=$user_id_approval group by mst_id", "mst_id", "approved_no"  );
			$ahid=return_next_id( "id","approval_history", 1 ) ;	
			
			$rID1=sql_multirow_update("wo_booking_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY",'2*0*0',"id",$booking_ids,0); ; 
			if($rID1) $flag=1; else $flag=0;
	
			if($flag==1)
			{
				$query="UPDATE approval_history SET current_approval_status=0, un_approved_by='".$user_id_approval."', un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=85 and current_approval_status=1 and mst_id in ($booking_ids)";
				$rID3=execute_query($query,1);
				if($rID3) $flag=1; else $flag=0;
	
	
				
				
				$target_app_id_arr = explode(',',$booking_ids);	
				foreach($target_app_id_arr as $mst_id)
				{		
					$approved_no=$max_approved_no_arr[$mst_id]+1;
					if($history_data_array!="") $history_data_array.=",";
					$history_data_array.="(".$ahid.",85,".$mst_id.",".$approved_no.",'".$user_sequence_no."',0,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,2)";
					$ahid++;
				}		
				
				$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date,IS_SIGNING,APPROVED";
				$rID4=sql_insert("approval_history",$field_array,$history_data_array,0);
				if($rID4) $flag=1; else $flag=0;
			}
	
			// if($flag==1)
			// {
			// 	$query="UPDATE refusing_cause_history SET CURR_APP_STATUS=0  WHERE entry_form=66 and CURR_APP_STATUS=1 and mst_id in ($booking_ids)";
			// 	$rID4=execute_query($query,1);
			// 	if($rID4) $flag=1; else $flag=0;
			// }
			
	
			// echo "191**".$rID1.",".$rID2.",".$rID3.",".$rID4;oci_rollback($con);die;
			
			$response=$booking_ids;
			if($flag==1) $msg='50'; else $msg='51';
	
		} 
		else
		{            
			
			
			
			$next_user_app = sql_select("select id from approval_history where mst_id in($booking_ids) and entry_form=85 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");

		
					
			if(count($next_user_app)>0)
			{
				echo "25**unapproved"; 
				disconnect($con);
				die;
			}
	
			$rID1=sql_multirow_update("wo_booking_mst","IS_APPROVED*READY_TO_APPROVED*APPROVED_SEQU_BY",'0*0*0',"id",$booking_ids,0);
			if($rID1) $flag=1; else $flag=0;
	
	
			if($flag==1)
			{
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=85 and mst_id in ($booking_ids)";
				$rID2=execute_query($query,1);
				if($rID2) $flag=1; else $flag=0;
			}
	
			
			if($flag==1) 
			{
				$query="delete from approval_mst  WHERE entry_form=85 and mst_id in ($booking_ids)";
				$rID3=execute_query($query,1); 
				if($rID3) $flag=1; else $flag=0; 
			}
			
	
			
			if($flag==1)
			{
				$query="UPDATE approval_history SET current_approval_status=0, un_approved_by=".$user_id_approval.", un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=85 and current_approval_status=1 and mst_id in ($booking_ids)";
	
				
				$rID4=execute_query($query,1);
				//echo $rID4;
				if($rID4) $flag=1; else $flag=0;
			}
			 
			// echo "5**".$rID1.",".$rID2.",".$rID3.",".$rID4.$flag;oci_rollback($con);die;
			
			$response=$booking_ids;
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

	/* Formatted on 2/28/2023 10:55:24 AM (QP5 v5.360) */

	   
	  // echo $sql;die();
       
	
	
	// $sql="select job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group from wo_booking_dtls where booking_no='$booking_no' and status_active=1 and is_deleted=0 group by job_no,po_break_down_id,pre_cost_fabric_cost_dtls_id,trim_group";
    $sql="select a.exchange_rate, b.job_no, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.trim_group from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.exchange_rate,b.job_no,b.po_break_down_id,b.pre_cost_fabric_cost_dtls_id,b.trim_group order by b.po_break_down_id";

	//echo $sql;die;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row){
		$pre_cost_fabic_dtls_id.=$row[csf('pre_cost_fabric_cost_dtls_id')].',';
		$exchange_rate= $row[csf('exchange_rate')];
		$job_no= $row[csf('job_no')];
		$po_id_arr[$row[csf('po_break_down_id')]]= $row[csf('po_break_down_id')];
	}
	$po_ids=implode(",",$po_id_arr);
	$pre_cost_fabic_dtls_ids=implode(',',array_unique(explode(',',rtrim($pre_cost_fabic_dtls_id,','))));
	//$exchange_rate=return_field_value("exchange_rate", " wo_booking_mst", "booking_no='".$booking_no."'");
		
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

// 	$fab_sql=sql_select("select  a.po_break_down_id  as po_id, a.trim_group, a.pre_cost_fabric_cost_dtls_id,  
// 	sum(case a.is_short when 2 then b.amount else 0 end) as main_amount,
// 	sum(case a.is_short when 1 then b.amount else 0 end) as short_amount
// 	from  wo_booking_dtls a, wo_trim_book_con_dtls b   where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no 
//    and a.booking_type=2
//    and a.booking_no='$booking_no' and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id,a.trim_group,a.pre_cost_fabric_cost_dtls_id ");

	$fab_sql=sql_select("select  a.po_break_down_id  as po_id, a.trim_group, a.pre_cost_fabric_cost_dtls_id,  
	sum(case a.is_short when 2 then b.amount else 0 end) as main_amount,
	sum(case a.is_short when 1 then b.amount else 0 end) as short_amount
	from  wo_booking_dtls a, wo_trim_book_con_dtls b   where a.id= b.wo_trim_booking_dtls_id and a.booking_no=b.booking_no 
   and a.booking_type=2
   and a.job_no='$job_no' and a.pre_cost_fabric_cost_dtls_id in ($pre_cost_fabic_dtls_ids) and a.status_active=1 and a.is_deleted=0 group by a.po_break_down_id,a.trim_group,a.pre_cost_fabric_cost_dtls_id  ");



   
	foreach($fab_sql as $row_data)
	{
		$trim_qty_data_arr[$row_data[csf('pre_cost_fabric_cost_dtls_id')]][$row_data[csf('po_id')]][$row_data[csf('trim_group')]]['main_amount']=$row_data[csf('main_amount')];
		$trim_qty_data_arr[$row_data[csf('pre_cost_fabric_cost_dtls_id')]][$row_data[csf('po_id')]][$row_data[csf('trim_group')]]['short_amount']=$row_data[csf('short_amount')];
	}  //var_dump($trim_qty_data_arr);
	// echo "<pre>";
	// print_r($trim_qty_data_arr); 
	//   echo "</pre>";die();
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
		//echo $po_ids;die();
		//$condition= new condition();

	//    if(str_replace("'",'',$po_ids) !=""){
	// 	$condition->po_id("in($po_ids)");
	//     }
	// 	$condition->init();
	// 	$trim= new trims($condition);
	// 	$trim_amount_arr=$trim->getAmountArray_precostdtlsid();
		
			// echo $trim->getQuery();die;
		// print_r($trim_amount_arr);
		$sql_cons_data=sql_select("select a.id as pre_cost_fabric_cost_dtls_id,b.po_break_down_id as po_id,a.rate,b.cons from wo_pre_cost_trim_cost_dtls a , wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id   and a.is_deleted=0  and a.status_active=1 and a.id in($pre_cost_fabic_dtls_ids)");
						 
		foreach($sql_cons_data as $row)
		{
			$pre_cost_data_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("po_id")]]['cons']=$row[csf("cons")];
			$pre_cost_data_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("po_id")]]['rate']=$row[csf("rate")];
		}
         //this is for budget wise..
		$trim_sql=sql_select("SELECT a.job_no, a.total_set_qnty, b.id AS po_id, c.item_number_id, SUM(c.order_quantity), SUM(c.plan_cut_qnty), d.id AS pre_cost_dtls_id, d.trim_group, d.cons_uom, AVG(d.cons_dzn_gmts), AVG(d.rate), d.amount, SUM(d.cons_dzn_gmts * c.order_quantity * d.rate) AS trims_amount, e.cons, e.tot_cons, e.rate
		FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e
		WHERE 1 = 1 AND b.id IN ($po_ids) AND a.id = b.job_id AND b.id = c.po_break_down_id AND a.id = d.job_id AND d.id = e.wo_pre_cost_trim_cost_dtls_id AND b.id = e.po_break_down_id AND c.item_number_id = e.item_number_id AND c.color_number_id = e.color_number_id AND c.size_number_id = e.size_number_id AND e.cons > 0 AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0 AND b.status_active IN (1) AND c.is_deleted = 0 AND c.status_active IN (1) AND d.is_deleted = 0 AND d.status_active = 1 AND e.is_deleted = 0 AND e.status_active = 1
		GROUP BY a.job_no, a.total_set_qnty, b.id, c.item_number_id, d.id, d.trim_group, d.cons_uom, d.amount, e.cons, e.tot_cons, e.rate"); 

//  echo "SELECT a.job_no, a.total_set_qnty, b.id AS po_id, c.item_number_id, SUM(c.order_quantity), SUM(c.plan_cut_qnty), d.id AS pre_cost_dtls_id, d.trim_group, d.cons_uom, AVG(d.cons_dzn_gmts), AVG(d.rate), d.amount, SUM(d.cons_dzn_gmts * c.order_quantity * d.rate) AS trims_amount, e.cons, e.tot_cons, e.rate
// FROM wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, wo_pre_cost_trim_cost_dtls d, wo_pre_cost_trim_co_cons_dtls e
// WHERE 1 = 1 AND b.id IN ($po_ids) AND a.id = b.job_id AND b.id = c.po_break_down_id AND a.id = d.job_id AND d.id = e.wo_pre_cost_trim_cost_dtls_id AND b.id = e.po_break_down_id AND c.item_number_id = e.item_number_id AND c.color_number_id = e.color_number_id AND c.size_number_id = e.size_number_id AND e.cons > 0 AND a.is_deleted = 0 AND a.status_active = 1 AND b.is_deleted = 0 AND b.status_active IN (1) AND c.is_deleted = 0 AND c.status_active IN (1) AND d.is_deleted = 0 AND d.status_active = 1 AND e.is_deleted = 0 AND e.status_active = 1
// GROUP BY a.job_no, a.total_set_qnty, b.id, c.item_number_id, d.id, d.trim_group, d.cons_uom, d.amount, e.cons, e.tot_cons, e.rate";die;
	   
	   
	   foreach($trim_sql as $row)
		{
			$pre_cu_data_arr[$row[csf("pre_cost_dtls_id")]][$row[csf("po_id")]]['trims_amount']=$row[csf("trims_amount")];
			
		}	
	   
		//echo count($pre_cost_data_arr);die;

		$sql_cu_woq=sql_select("select sum(amount) as amount,po_break_down_id ,pre_cost_fabric_cost_dtls_id  from wo_booking_dtls where  booking_no='$booking_no' and booking_type=2 and status_active=1 and is_deleted=0 group by po_break_down_id,pre_cost_fabric_cost_dtls_id");

		// echo "select sum(amount) as amount,po_break_down_id as po_id,pre_cost_fabric_cost_dtls_id  from wo_booking_dtls where  booking_no='$booking_no' and booking_type=2 and status_active=1 and is_deleted=0 group by po_break_down_id,pre_cost_fabric_cost_dtls_id";die;
			
		foreach($sql_cu_woq as $row)
		{
			$pre_cut_data_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]][$row[csf("po_break_down_id")]]['amount']=$row[csf("amount")];
			
		}	
	// 	echo "<pre>";
    //  print_r($pre_cut_data_arr); 
    //   echo "</pre>";die();
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
			$main_fab_cost=$trim_qty_data_arr[$selectResult[csf("pre_cost_fabric_cost_dtls_id")]][$selectResult[csf('po_break_down_id')]][$selectResult[csf('trim_group')]]['main_amount'];
			$short_fab_cost=$trim_qty_data_arr[$selectResult[csf("pre_cost_fabric_cost_dtls_id")]][$selectResult[csf('po_break_down_id')]][$selectResult[csf('trim_group')]]['short_amount'];
			$sam_trim_with=$trim_sam_qty_data_arr[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['sam_with'];
			$sam_trim_without=$trim_sam_qty_data_arr[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['sam_without'];
			$po_qty=$po_qty_arr[$selectResult[csf('po_break_down_id')]]['order_quantity'];
			$po_ship_date=$po_qty_arr[$selectResult[csf('po_break_down_id')]]['pub_shipment_date'];
			$pre_rate=$pre_cost_data_arr[$selectResult[csf("pre_cost_fabric_cost_dtls_id")]][$selectResult[csf("po_break_down_id")]]['rate'];
			$pre_cons=$pre_cost_data_arr[$selectResult[csf("pre_cost_fabric_cost_dtls_id")]][$selectResult[csf("po_break_down_id")]]['cons'];
			$pre_req_qnty=def_number_format(($pre_cons*($po_qty/$costing_per_qty))/$sql_lib_item_group_array[$selectResult[csf('trim_group')]][conversion_factor],5,"");
			$pre_amount=$pre_req_qnty*$pre_rate;
			 $tot_grey_req_as_price_cost=($tot_mkt_cost/$costing_per_qty)*$po_qty;

				//  $trims_value=$trim_amount_arr[$selectResult[csf("pre_cost_fabric_cost_dtls_id")]];
			// $trims_value=$pre_cu_data_arr[$selectResult[csf('pre_cost_fabric_cost_dtls_id')]][$selectResult[csf('po_break_down_id')]]['trims_amount'];
			$trims_value=$pre_req_qnty*$pre_rate;

			
			 
	$k++;
	
	?>
	<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $k; ?>">
		<td width="30"> <? echo $k; ?> </td> 
		<td width="100" title="<?=$selectResult[csf("pre_cost_fabric_cost_dtls_id")]."==>".$selectResult[csf('trim_group')];?>"><p><? echo 	$trim_group[$selectResult[csf('trim_group')]];?></p>  </td>
		<td width="120"><p><? echo $po_number_arr[$selectResult[csf('po_break_down_id')]];?></p>  </td>
		<td width="70" align="right"><? echo change_date_format($po_ship_date,"dd-mm-yyyy",'-'); ?> </td>
		<td width="80" align="right"><? $total_price_mkt_cost+=$tot_grey_req_as_price_cost; echo number_format($tot_grey_req_as_price_cost,2);?> </td>
		<td width="70" align="right"><?  echo number_format( $trims_value,2); $pre_cost+=$trims_value;?></td>
		<td width="70" align="right"><? echo number_format($main_fab_cost,2); $total_booking_qnty_main+=$main_fab_cost;?> </td>
		<td width="70" align="right"> <? echo number_format($short_fab_cost,2); $total_booking_qnty_short+=$short_fab_cost;?></td>
		<td width="70" align="right"><? echo number_format($sam_trim_with,2); $total_booking_qnty_sample+=$sam_trim_with;?></td>
		<td width="70" align="right">	<? $tot_bok_qty=$main_fab_qty+$short_fab_qty+$total_booking_qnty_sample; echo number_format($tot_bok_qty,2); $total_tot_bok_qty+=$tot_bok_qty;?> </td>
		<td width="70" align="right"> <? $balance_mkt= def_number_format($tot_grey_req_as_price_cost-$total_booking_qnty_main,2,""); echo number_format($balance_mkt,2); $tot_mkt_balance+= $balance_mkt; ?></td>
		<td width="70" align="right"> <? $total_pre_cost=$trims_value-$total_booking_qnty_main;$tot_pre_cost+=$total_pre_cost; echo number_format($total_pre_cost,2);?></td>
		<td width="">
		<? 
		if( $total_pre_cost>0)
			{
			echo "Less Booking";
			}
		// else if ($total_pre_cost<0) 
		// 	{
		// 	echo "Over Booking";
		// 	} 
		else if ($total_pre_cost==0.00) 
			{
				echo "As Per";
			} 
		else
			{
			echo "As Per";
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
		$sql_cause="select MAX(id) as id from fabric_booking_approval_cause where page_id='$menu_id' and entry_form=85 and user_id='$user_id' and booking_id='$wo_id' and approval_type='$app_type' and status_active=1 and is_deleted=0";				
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
				http.open("POST","short_trims_booking_approval_controller.php",true);
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
				fnc_close();
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
			http.open("POST","trims_booking_approval_controller.php",true);
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

if ($action=="unappcause_popup")
{
	echo load_html_head_contents("Un Approval Request", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$wo_id=$data;
	$sql_req="select approval_cause from fabric_booking_approval_cause where entry_form=85 and booking_id='$wo_id' and approval_type=2 and status_active=1 and is_deleted=0";				
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

		$operation=0;
		if ($operation==0)  // Insert Here
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$approved_no_history=return_field_value("approved_no","approval_history","entry_form=85 and mst_id=$wo_id and approved_by=$user_id");
			$approved_no_cause=return_field_value("approval_no","fabric_booking_approval_cause","entry_form=85 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
			
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
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=85 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
				
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
				$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=85 and mst_id=$wo_id and approved_by=$user_id");
				$max_appv_no_cause=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=85 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
				
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
					
					$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=85 and booking_id=$wo_id and user_id=$user_id and approval_type=$appv_type");
					
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
		
			$unapproved_cause_id=return_field_value("id","fabric_booking_approval_cause","entry_form=85 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
			
			$max_appv_no_his=return_field_value("max(approved_no)","approval_history","entry_form=85 and mst_id=$wo_id and approved_by=$user_id");
			
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
				
				$id_cause=return_field_value("max(id)","fabric_booking_approval_cause","entry_form=85 and user_id=$user_id and booking_id=$wo_id  and approval_type=$appv_type and approval_history_id=$approval_id");
				
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

if ( $action=="deny_mail" )
{

	require_once('../../mailer/class.phpmailer.php');
	require_once('../../auto_mail/setting/mail_setting.php');
	
	list($sys_id,$mail,$mail_body)=explode('__',$data);
	// ob_start();

	// $message=ob_get_contents();
	// ob_clean();
	
	$bookingSql = "select ID,BOOKING_NO,COMPANY_ID,INSERTED_BY from WO_BOOKING_MST where id in($sys_id)";
	$bookingSqlRes=sql_select($bookingSql);
	$booking_data_arr=array();
	foreach($bookingSqlRes as $row)
	{
		$booking_data_arr['INSERTED_BY'][$row['INSERTED_BY']]=$row['INSERTED_BY'];

		$electronicSql = "select a.USER_EMAIL from USER_PASSWD a,ELECTRONIC_APPROVAL_SETUP b where a.id=b.USER_ID and b.ENTRY_FORM = 8 AND b.IS_DELETED = 0 AND a.IS_DELETED = 0 AND b.COMPANY_ID = {$row['COMPANY_ID']} and a.USER_EMAIL is not null
		UNION ALL
		select a.USER_EMAIL from USER_PASSWD a where a.IS_DELETED = 0 and a.USER_EMAIL is not null and a.id={$row['INSERTED_BY']} 
		";
		$electronicSqlRes=sql_select($electronicSql);
		$mail_arr=array();
		foreach($electronicSqlRes as $row)
		{
			$mail_arr[$row['USER_EMAIL']]=$row['USER_EMAIL'];
		}

		$message="Booking no: ".$row['BOOKING_NO']." is deny.".$mail_body;
		$to = implode(',',$mail_arr);
		$subject = "Trims booking deny notification.";
		$header=mailHeader();
		if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);

	}
	exit();
		
}

function send_final_app_notification($sys_id){

	require_once('../../mailer/class.phpmailer.php');
	require_once('../../auto_mail/setting/mail_setting.php');
	
	$bookingSql = "select ID,BOOKING_NO,COMPANY_ID,INSERTED_BY from WO_BOOKING_MST where id in($sys_id)";
	$bookingSqlRes=sql_select($bookingSql);
	$booking_data_arr=array();
	foreach($bookingSqlRes as $row)
	{
		$booking_data_arr['INSERTED_BY'][$row['INSERTED_BY']]=$row['INSERTED_BY'];

		$electronicSql = "select a.USER_EMAIL from USER_PASSWD a,ELECTRONIC_APPROVAL_SETUP b where a.id=b.USER_ID and b.ENTRY_FORM = 8 AND b.IS_DELETED = 0 AND a.IS_DELETED = 0 AND b.COMPANY_ID = {$row['COMPANY_ID']} and a.USER_EMAIL is not null
		UNION ALL
		select a.USER_EMAIL from USER_PASSWD a where a.IS_DELETED = 0 and a.USER_EMAIL is not null and a.id={$row['INSERTED_BY']} 
		";
		$electronicSqlRes=sql_select($electronicSql);
		$mail_arr=array();
		foreach($electronicSqlRes as $row)
		{
			$mail_arr[$row['USER_EMAIL']]=$row['USER_EMAIL'];
		}

		$message="Booking no: ".$row['BOOKING_NO']." is approverd.".$mail_body;
		$to = implode(',',$mail_arr);
		$subject = "Trims booking full approved notification.";
		$header=mailHeader();
		if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);

	}
}




?>