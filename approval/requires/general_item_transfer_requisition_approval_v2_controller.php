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

if ($action=="load_drop_down_store")
{
	$permitted_store_id=return_field_value("STORE_LOCATION_ID","user_passwd","id='".$user_id."'");
	if($permitted_store_id){$storCon=" and id in($permitted_store_id)";}
	echo create_drop_down( "cbo_store_id", 130, "select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and company_id=$data $storCon order by store_name","id,store_name", 1, "-- All --","","",0,"","","","");
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

      $sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation,b.sequence_no from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and valid=1   and b.is_deleted=0  and  b.entry_form=52  order by b.sequence_no";
				//echo $sql;
	$arr=array (2=>$custom_designation,3=>$Department);
		echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department,Seq", "100,120,150,150,30,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id,0", $arr , "user_name,user_full_name,designation,department_id,sequence_no", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
			?>
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>
	<?
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

	$field_array_up = "quantity*updated_by*update_date";
	for ($i=1; $i<=$tot_row; $i++)
    {
		$txtqty = "txtqty_".$i;
		$req_dtls_id = "req_dtls_id_".$i;

		$updateID_array[] = str_replace("'",'',$$req_dtls_id);
		$data_array_up[str_replace("'",'',$$req_dtls_id)] = explode("*",("".$$txtqty."*".$user_id."*'".$pc_date_time."'"));	
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

if($action=="reqdetails_popup")
{
	echo load_html_head_contents("Requ. Details","../../", 1, 1, $unicode,1);
	extract($_REQUEST);
	$ex_data=explode("**",$data);

	$sql="SELECT a.id, b.id as dtls_id, b.quantity, b.product_id, c.item_category_id, c.item_account, c.item_description, c.item_size, c.item_group_id, c.sub_group_name, c.order_uom as unit_of_measure, d.item_name
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c, lib_item_group d
	where a.id=b.mst_id and b.product_id=c.id and c.item_group_id=d.id and a.id=$ex_data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	//echo $sql;
	$sql_res=sql_select($sql);
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
			http.open("POST","purchase_requisition_approval_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fn_requisition_details_qtyupdate_reponse;					
		}

		function fn_requisition_details_qtyupdate_reponse()
		{
			if(http.readyState == 4)
			{				
				var reponse=trim(http.responseText).split('**');
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
        	<table class="rpt_table" width="850" cellspacing="0" cellpadding="0" align="center" id="tbl_details">
                <thead>
                    <tr>
                    	<th width="100">Item Account</th>
	                    <th width="100">Item Category</th>
	                    <th width="100">Item Group</th>
	                    <th width="100">Item Sub. Group</th>
	                    <th width="150">Item Description</th>
	                    <th width="100">Item Size</th>
	                    <th width="60">Order UOM</th>
	                    <th class="must_entry_caption" title="Must Entry Field." width="80"> <font color="blue">Quantity</font></th>
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
	                        <td width="100"><?= $row[csf('item_account')]; ?></td>
	                        <td width="100"><?= $item_category[$row[csf('item_category_id')]]; ?></td>
	                        <td width="100"><?= $row[csf('item_name')]; ?></td>
	                        <td width="100"><?= $row[csf('sub_group_name')]; ?></td>
	                        <td width="150"><?= $row[csf('item_description')]; ?></td>
	                        <td width="100"><?= $row[csf('item_size')]; ?></td>
	                        <td width="60"><?= $unit_of_measurement[$row[csf('unit_of_measure')]]; ?></td>
	                        <td width="80" align="right"><input type="text" name="txtqty[]" id="txtqty_<?= $i; ?>" style="width:80px" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" /></td>
	                    </tr>
	                    <?
	                    $i++;
	                }
	                ?>    
                </tbody>
            </table>

                <table width="100%">
                	<tr>
                        <td colspan="20" height="20" valign="middle" align="center" class="button_container"> 
                            <input type="button" class="formbutton" id="updateqtyid" name="updateqtyid" value="Update" onClick="fn_requisition_details_qtyupdate(1)" style="width:80px" />                           
                        </td>    
                    </tr>
                </table>
            </div>
        </form>
	    </fieldset>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?	
}

function getSequence($parameterArr=array()){
	//$lib_location_arr=implode(',',(array_keys($parameterArr['lib_location_arr'])));
	$lib_location_arr=implode(',',(array_keys($parameterArr['lib_location_arr']))); 
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,USER_ID,ENTRY_FORM,BYPASS,SEQUENCE_NO,BUYER_ID,BRAND_ID,SUPPLIER_ID,DEPARTMENT,LOCATION as LOCATION_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0 order by SEQUENCE_NO";
	 //echo $sql;die;
	$sql_result=sql_select($sql);
	$dataArr=array();
	foreach($sql_result as $rows){		
		if($rows['LOCATION_ID']==''){$rows['LOCATION_ID']=$lib_location_arr;}
		$dataArr['sequ_by'][$rows['SEQUENCE_NO']]=$rows;
		$dataArr['user_by'][$rows['USER_ID']]=$rows;
		$dataArr['sequ_arr'][$rows['SEQUENCE_NO']]=$rows['SEQUENCE_NO'];
	}
 
	return $dataArr;
}

function getFinalUser($parameterArr=array()){
	$lib_location_arr=implode(',',(array_keys($parameterArr['lib_location_arr'])));

	
	//Electronic app setup data.....................
	$sql="SELECT COMPANY_ID,PAGE_ID,ENTRY_FORM,USER_ID,BYPASS,SEQUENCE_NO,BUYER_ID,SUPPLIER_ID,BRAND_ID,DEPARTMENT,LOCATION as LOCATION_ID FROM ELECTRONIC_APPROVAL_SETUP WHERE COMPANY_ID = {$parameterArr['company_id']} AND ENTRY_FORM = {$parameterArr['ENTRY_FORM']} AND IS_DELETED = 0  order by SEQUENCE_NO";
 //echo $sql;die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $rows){
		if($rows['LOCATION_ID']==''){$rows['LOCATION_ID']=$lib_location_arr;}
		$usersDataArr[$rows['USER_ID']]['LOCATION_ID']=explode(',',$rows['LOCATION_ID']);
		$userSeqDataArr[$rows['USER_ID']]=$rows['SEQUENCE_NO'];
	
	}

	//print_r($userSeqDataArr);


	$finalSeq=array();
	foreach($parameterArr['match_data'] as $sys_id=>$bbtsRows){
		foreach($userSeqDataArr as $user_id=>$seq){
			if( in_array($bbtsRows['location_id'],$usersDataArr[$user_id]['LOCATION_ID']) &&  $bbtsRows['location_id']>0 ){
				$finalSeq[$sys_id][$user_id]=$seq;
			}
		}
	}

	 // print_r($parameterArr['match_data']);die;

	return array('final_seq'=>$finalSeq,'user_seq'=>$userSeqDataArr);
}

$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$item_cat_arr=return_library_array( "select id, SHORT_NAME from LIB_ITEM_CATEGORY_LIST", "id", "SHORT_NAME"  );

$location_arr = return_library_array("select id,location_name from lib_location", 'id', 'location_name');

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_req_year=str_replace("'","",$cbo_req_year);
	$txt_req_no=str_replace("'","",$txt_req_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_store_id=str_replace("'","",$cbo_store_id);
    $approval_type=str_replace("'","",$cbo_approval_type);
	$cbo_transfer_criteria=str_replace("'","",$cbo_transfer_criteria);
    $txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
    $user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;
    

    $electronicDataArr=getSequence(array('company_id'=>$company_name,'ENTRY_FORM'=>52,'user_id'=>$user_id_approval,'lib_location_arr'=>$location_arr,'lib_brand_arr'=>0,'product_dept_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0));
 // print_r($location_arr);

	$req_date_cond=$req_no_conds='';
	if ($txt_req_no != ''){ $req_no_conds=" and a.TRANSFER_PREFIX_NUMBER=$txt_req_no";$req_no_conds2=" and a.TRANSFER_PREFIX_NUMBER=$txt_req_no";}
	if ($cbo_transfer_criteria >0){ $transfer_criteria_conds=" and a.transfer_criteria=$cbo_transfer_criteria"; $transfer_criteria_conds2=" and a.transfer_criteria=$cbo_transfer_criteria";}
	if ($txt_date_from != '' && $txt_date_to != '')
	{
		if($db_type==0)
		{
			$txt_date_from = date("Y-m-d", strtotime($txt_date_from));
			$txt_date_to = date("Y-m-d", strtotime($txt_date_to));
			$req_date_cond = " and transfer_date between '$txt_date_from' and '$txt_date_to'";
			$req_date_cond2 = " and a.transfer_date between '$txt_date_from' and '$txt_date_to'";
		}
		else
		{
			$txt_date_from = date("d-M-Y", strtotime($txt_date_from));
			$txt_date_to = date("d-M-Y", strtotime($txt_date_to));
			$req_date_cond = " and a.transfer_date between '$txt_date_from' and '$txt_date_to'";
			$req_date_cond2 = " and a.transfer_date between '$txt_date_from' and '$txt_date_to'";
		}	
	}
	
	?>

	<script type="text/javascript">
		function openmypage_app_cause(wo_id,app_type,i)
		{
			var txt_appv_cause = $("#txt_appv_cause_"+i).val();
			var approval_id = $("#approval_id_"+i).val();
			var data=wo_id+"_"+app_type+"_"+txt_appv_cause+"_"+approval_id;
			var title = 'Approval Cause Info';
			var page_link = 'requires/purchase_requisition_approval_controller.php?data='+data+'&action=appcause_popup';
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
			var title = 'Requisition Details Info';
			var page_link = 'requires/purchase_requisition_approval_controller.php?data='+data+'&action=reqdetails_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=250px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				/*var appv_cause=this.contentDoc.getElementById("hidden_appv_cause");
				$('#txt_appv_cause_'+i).val(appv_cause.value);*/
			}
		}
	</script>
	<?

	$req_year_cond='';
	if($db_type==0)
	{
		if ($cbo_req_year != 0) $req_year_cond= " and year(insert_date)=$cbo_req_year";
		$year_cond_prefix= "year(a.insert_date)";
	}
	else if($db_type==2)
	{
		if ($cbo_req_year != 0) $req_year_cond= " and TO_CHAR(a.insert_date,'YYYY')=$cbo_req_year";
		$year_cond_prefix= "TO_CHAR(insert_date,'YYYY')";
	}

	if($approval_type==0) // Un-Approve
	{  
		if($electronicDataArr['user_by'][$user_id_approval]['LOCATION_ID']){
			$where_con .= " and a.LOCATION_ID in(".$electronicDataArr['user_by'][$user_id_approval]['LOCATION_ID'].",0)";
			$electronicDataArr['sequ_by'][0]['LOCATION_ID']=$electronicDataArr['user_by'][$user_id_approval]['LOCATION_ID'];
		 }
        // $data_mast_sql = "SELECT a.ID,b.ITEM_CATEGORY_ID from inv_item_transfer_requ_mst a,wo_non_order_info_dtls b  where a.id=b.mst_id and a.company_name=$company_name  and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and a.IS_APPROVED<>1 and a.READY_TO_APPROVED=1  and a.entry_form=147 $where_con group by a.ID,b.ITEM_CATEGORY_ID ";


        $data_mast_sql ="SELECT a.ID, a.company_id as COMPANY_ID, a.to_company as TO_COMPANY, a.location_id as LOCATION_ID, a.ready_to_approve as READY_TO_APPROVE, a.is_approved as IS_APPROVED from inv_item_transfer_requ_mst a where a.is_deleted=0 and  a.status_active=1 and a.is_approved<>1 and a.READY_TO_APPROVE=1  and a.company_id=$company_name $where_con ";
		 //echo $data_mast_sql;die;



		 $tmp_sys_id_arr=array();
		 $data_mast_sql_res=sql_select( $data_mast_sql );
		 
		 foreach ($data_mast_sql_res as $row)
		 { 
			 for($seq=($electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']-1);$seq>=0; $seq-- ){
				 
				 if($electronicDataArr['sequ_by'][$seq]['LOCATION_ID']==''){$electronicDataArr['sequ_by'][$seq]['LOCATION_ID']=0;}
				 
				 if(in_array($row['LOCATION_ID'],explode(',',$electronicDataArr['sequ_by'][$seq]['LOCATION_ID'])) && $row['LOCATION_ID'] > 0)
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
		
	
		// 	  echo "<pre>";
		// 	  print_r($tmp_sys_id_arr);die;
		//    echo "</pre>";die();
		
		$sql='';
		for($seq=0;$seq<=count($electronicDataArr['sequ_arr']); $seq++ ){
			$sys_con = where_con_using_array($tmp_sys_id_arr[$seq],0,'a.ID');

			if($tmp_sys_id_arr[$seq]){
				if($sql!=''){$sql .=" UNION ALL ";}
				//$approved_user_cond=" and c.approved_by='$user_id'";
				$sql.="SELECT a.ID,transfer_system_id as TRANSFER_SYSTEM_ID,a.TRANSFER_PREFIX_NUMBER,TO_CHAR(a.insert_date,'YYYY') as year, a.challan_no as CHALLAN_NO,a.transfer_criteria as TRANSFER_CRITERIA, a.company_id as COMPANY_ID,a.transfer_date as TRANSFER_DATE, a.to_company as TO_COMPANY, a.location_id as LOCATION_ID, a.to_location_id as TO_LOCATION_ID, a.from_store_id as FROM_STORE_ID,a.to_store_id as TO_STORE_ID, a.remarks as REMARKS, a.ready_to_approve as READY_TO_APPROVE, a.is_approved as IS_APPROVED from inv_item_transfer_requ_mst a where a.is_deleted=0 and  a.status_active=1 and a.is_approved<>1 and a.entry_form=494 and a.APPROVED_SEQU_BY=$seq $sys_con  and a.company_id=$company_name and a.ready_to_approve=1  $req_year_cond $req_no_conds $transfer_criteria_conds $req_date_cond ";


			}
		}
	}
	else
	{   
        $sql="SELECT a.ID,a.transfer_system_id as TRANSFER_SYSTEM_ID,a.TRANSFER_PREFIX_NUMBER,TO_CHAR(a.insert_date,'YYYY') as year, a.challan_no as CHALLAN_NO,a.transfer_criteria as TRANSFER_CRITERIA, a.company_id as COMPANY_ID,a.transfer_date as TRANSFER_DATE, a.to_company as TO_COMPANY, a.location_id as LOCATION_ID, a.to_location_id as TO_LOCATION_ID, a.from_store_id as FROM_STORE_ID,a.to_store_id as TO_STORE_ID, a.remarks as REMARKS, a.ready_to_approve as READY_TO_APPROVE, a.is_approved as IS_APPROVED from inv_item_transfer_requ_mst a,APPROVAL_MST b where a.is_deleted=0 and b.SEQUENCE_NO={$electronicDataArr['user_by'][$user_id_approval]['SEQUENCE_NO']} and a.APPROVED_SEQU_BY=b.SEQUENCE_NO and b.entry_form=52 and b.mst_id=a.id and a.status_active=1 and a.is_approved<>0 and a.entry_form=494  and company_id=$company_name and a.ready_to_approve=1  $req_date_cond2 $req_no_conds2 $transfer_criteria_conds2  ";


    }
  
	

 //echo $sql;die;	
  
  
	$department_arr=return_library_array( "select id, department_name from lib_department", 'id', 'department_name' );

	$company_arr=return_library_array( "select id, company_name from lib_company", 'id', 'company_name' );
	$store_arr=return_library_array( "select id, store_name from lib_store_location", 'id', 'store_name' );
	$user_arr=return_library_array( "select id, user_name from user_passwd", 'id', 'user_name' );	
	$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_name." and module_id=6 and report_id =39 and is_deleted=0 and status_active=1");
    $format_ids=explode(",",$print_report_format);
    $cause_with=0;
  
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:900px; margin-top:10px">
        <legend>Purchase Requisition Approval</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="50">SL</th>
                    <th width="120">Requisition No</th>

                    <th width="140">To Company</th>

                    <th width="60">Year</th>
                  	
                  	<th width="140">To Store</th>
               
                    <th width="100">Manual Challan No.</th>                    
                    <th width="70">Requisition Date</th>
                   
                    <th>Transfer Criteria</th>
										
                </thead>
            </table>
            <div style="width:900px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" style="margin-left: 15px;"class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <? 
                            $i=1; $j=0;
							//echo $sql;
                            $nameArray=sql_select( $sql );
                            foreach ($nameArray as $row)
                            {
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$value='';
								$unapprove_value_id=$row[csf('id')];
								/*if($approval_type==0)
								{
									$value=$row[csf('id')];
									//$TO_STORE_ID=$row[csf('store_name')];
								}
								else
								{
									//$TO_STORE_ID=$row[csf('to_store_id')];
									$app_id = sql_select("select id from approval_history where mst_id='".$row[csf('id')]."' and entry_form='1'  order by id desc");
									
									$value=$row[csf('id')]."**".$app_id[0][csf('id')];
								}*/

								$variable='';
							
                                   $variable="<a href='#'    onclick=\"print_report('".$row[csf('company_id')]."','".$row[csf('id')]."','General Transfer Requisition','".$row[csf('is_approved')]."','".$row[csf('remarks,')]."','4')\"> ".$row[csf('TRANSFER_PREFIX_NUMBER')]." <a/>";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="req_id_<? echo $i;?>" name="req_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /> 
                                        <input id="requisition_id_<? echo $i;?>" name="requisition_id[]" type="hidden" value="<? echo $row[csf('id')]; ?>" /><!--this is uesd for delete row-->
                                        <input id="approval_id_<?=$i;?>" name="approval_id[]" type="hidden" value="<?=$row[csf('approval_id')]; ?>" />
                                    </td>   
									<td width="50" align="center"><? echo $i; ?></td>
									<td width="120" align="center"><p><? echo $variable; ?></p></td>
                                    <td width="140"><p><? echo $company_arr[$row[csf('to_company')]]; ?></p></td>

                                    <td width="60"><p><? echo $row[csf('year')]; ?></p></td>

                                    <td width="140"><p><? echo $store_arr[$row[csf('to_store_id')]]; ?></p></td>
                                   									
                                    <td width="100" align="right"><p><? echo $row[csf('challan_no')]; ?></p></td>
									<td width="70" align="center"><? if($row[csf('transfer_date')]!="0000-00-00") echo change_date_format($row[csf('transfer_date')]); ?>&nbsp;</td>
							
									<td  align="center"><p><? echo $item_transfer_criteria[$row[csf('transfer_criteria')]]; ?></p></td>
									
								</tr>
								<?
								$i++;
							
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
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
}

if ($action=="approve")
{ 
	$process = array( &$_POST );

	extract(check_magic_quote_gpc( $process )); 
	$con = connect();

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_store_id=str_replace("'","",$cbo_store_id);
	$txt_alter_user_id = str_replace("'","",$txt_alter_user_id);
    $user_id_approval=($txt_alter_user_id!='')?$txt_alter_user_id:$user_id;

		//............................................................................
		$sql = "SELECT ID, company_id as COMPANY_ID, location_id as LOCATION_ID,IS_APPROVED from inv_item_transfer_requ_mst where is_deleted=0 and  status_active=1   and company_id=$cbo_company_name and id in($req_nos)";
		 //echo $sql;die();
		$sqlResult=sql_select( $sql );
		foreach ($sqlResult as $row)
		{
			
			$matchDataArr[$row['ID']]=array('location_id'=>$row['LOCATION_ID'],'brand_id'=>0,'store'=>0,'is_approved'=>$row['IS_APPROVED']);
		}
		
		$finalDataArr=getFinalUser(array('company_id'=>$cbo_company_name,'ENTRY_FORM'=>52,'lib_location_arr'=>$location_arr,'lib_brand_arr'=>0,'lib_item_cat_arr'=>0,'lib_store_arr'=>0,'product_dept_arr'=>0,'match_data'=>$matchDataArr));
		
	
		$sequ_no_arr_by_sys_id =$finalDataArr['final_seq'];
		$user_sequence_no = $finalDataArr['user_seq'][$user_id_approval];


		if($approval_type==0)
		{ 
			
			$id=return_next_id( "id","approval_mst", 1 ) ;
			$ahid=return_next_id( "id","approval_history", 1 ) ;	
			
			$target_app_id_arr = explode(',',$req_nos);	
			//print_r($target_app_id_arr);
			foreach($target_app_id_arr as $mst_id)
			{
				//echo max($finalDataArr['final_seq'][$mst_id]);die;

				if($data_array!=''){$data_array.=",";}
				$data_array.="(".$id.",52,'".$mst_id."','".$user_sequence_no."',".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$user_ip."')"; 
				$id=$id+1;
				
				if($matchDataArr[$mst_id]['is_approved'] == 0 || $matchDataArr[$mst_id]['is_approved'] == 2){
					$approved_no=$max_approved_no_arr[$mst_id][$user_id_approval]+1;
				}
				else{
					$approved_no=$max_approved_no_arr[$mst_id][$user_id_approval]*1;
				}
				
				if($history_data_array!="") $history_data_array.=",";
				$history_data_array.="(".$ahid.",52,".$mst_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				$ahid++;
				
				//mst data.......................
				$approved=(max($finalDataArr['final_seq'][$mst_id])==$user_sequence_no)?1:3;
				
                $data_array_up[$mst_id] = explode(",",("".$approved.",".$user_sequence_no."")); 
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
				$field_array_up="IS_APPROVED*APPROVED_SEQU_BY"; 
				$rID2=execute_query(bulk_update_sql_statement( "inv_item_transfer_requ_mst", "id", $field_array_up, $data_array_up, $target_app_id_arr ));
				if($rID2) $flag=1; else $flag=0; 
			}

			if($flag==1)
			{
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=52 and mst_id in ($req_nos)";
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
		else
		{            

			$next_user_app = sql_select("select id from approval_history where mst_id in($req_nos) and entry_form=52 and sequence_no >$user_sequence_no and current_approval_status=1 group by id");
					
			if(count($next_user_app)>0)
			{
				echo "25**unapproved"; 
				disconnect($con);
				die;
			}

			$rID1=sql_multirow_update("inv_item_transfer_requ_mst","IS_APPROVED*READY_TO_APPROVE*APPROVED_SEQU_BY",'0*0*0',"id",$req_nos,0);
			if($rID1) $flag=1; else $flag=0;


			if($flag==1)
			{
				$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=52 and mst_id in ($req_nos)";
				$rID2=execute_query($query,1);
				if($rID2) $flag=1; else $flag=0;
			}

			
			if($flag==1) 
			{
				$query="delete from approval_mst  WHERE entry_form=52 and mst_id in ($req_nos)";
				$rID3=execute_query($query,1); 
				if($rID3) $flag=1; else $flag=0; 
			}
			

			
			if($flag==1)
			{
				$query="UPDATE approval_history SET current_approval_status=0, un_approved_by=".$user_id_approval.", un_approved_date='".$pc_date_time."', updated_by='".$user_id."', update_date='".$pc_date_time."' WHERE entry_form=52 and current_approval_status=1 and mst_id in ($req_nos)";

				
				$rID4=execute_query($query,1);
				//echo $rID4;
				if($rID4) $flag=1; else $flag=0;
			}
			
			//echo "5**".$rID1.",".$rID2.",".$rID3.",".$rID4.$flag;oci_rollback($con);die;
			
			$response=$req_nos;
			if($flag==1) $msg='20'; else $msg='22';
			
		}
		

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
	 

	//end company if;	

	disconnect($con);
	die;

}


?>