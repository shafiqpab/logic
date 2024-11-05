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
				$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by sequence_no";
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



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_req_year=str_replace("'","",$cbo_req_year);
	$txt_req_no=str_replace("'","",$txt_req_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_store_id=str_replace("'","",$cbo_store_id);
	$cbo_transfer_criteria=str_replace("'","",$cbo_transfer_criteria);

	$req_date_cond=$req_no_conds='';
	if ($txt_req_no != ''){ $req_no_conds=" and TRANSFER_PREFIX_NUMBER=$txt_req_no";$req_no_conds2=" and a.TRANSFER_PREFIX_NUMBER=$txt_req_no";}
	if ($cbo_transfer_criteria >0){ $transfer_criteria_conds=" and transfer_criteria=$cbo_transfer_criteria"; $transfer_criteria_conds2=" and a.transfer_criteria=$cbo_transfer_criteria";}
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
			$req_date_cond = " and transfer_date between '$txt_date_from' and '$txt_date_to'";
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
		if ($cbo_req_year != 0) $req_year_cond= " and TO_CHAR(insert_date,'YYYY')=$cbo_req_year";
		$year_cond_prefix= "TO_CHAR(insert_date,'YYYY')";
	}
	//echo $req_date_cond.'system'.$req_year_cond;die;
	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="")
	{		
		$user_id=$txt_alter_user_id;	
	}

	

	

	//echo $item_category_id.'system';
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and user_id=$user_id and is_deleted = 0");
	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and is_deleted = 0");
 	
	//store and item lavel start---------------------------------
	
	$app_setup_sql= "select USER_ID, SEQUENCE_NO,BYPASS from electronic_approval_setup where company_id=$company_name and page_id=$menu_id and is_deleted = 0";	
	
	$app_setup_sql_res=sql_select( $app_setup_sql );
	foreach ($app_setup_sql_res as $row)
	{
		$appDataArr['BYPASS'][$row[USER_ID]]=$row[BYPASS];
		$appDataArr['USER_WISE_SEQ'][$row[USER_ID]]=$row[SEQUENCE_NO];
		$appDataArr['SEQUENCE_WISE_USER'][$row[SEQUENCE_NO]]=$row[USER_ID];
		$appDataArr['USER_ID'][$row[USER_ID]]=$row[USER_ID];
	}
	
	
	
	if($cbo_store_id!=0){
		$userDataArr[$user_id]['STORE_ID']=$cbo_store_id;
	}
	
	
	
	
	
	//echo $userDataArr[$user_id]['STORE_ID'];die;
	
	//--------------------store and item lavel end
	
	
	
	//echo $user_sequence_no."nazim"; die;
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority To Sign Pre-Costing.</font>";
		die;
	}	
	
	
	if($previous_approved==1 && $approval_type==1)	//approval process with prevous approve start
	{
		

		// echo $sql;
		$sql="SELECT a.transfer_system_id as TRANSFER_SYSTEM_ID,a.TRANSFER_PREFIX_NUMBER,TO_CHAR(a.insert_date,'YYYY') as year, a.challan_no as CHALLAN_NO,a.transfer_criteria as TRANSFER_CRITERIA, a.company_id as COMPANY_ID,a.transfer_date as TRANSFER_DATE, a.to_company as TO_COMPANY, a.location_id as LOCATION_ID, a.to_location_id as TO_LOCATION_ID, a.from_store_id as FROM_STORE_ID, a.to_store_id as TO_STORE_ID, a.remarks as REMARKS, a.ready_to_approve as READY_TO_APPROVE, a.is_approved as IS_APPROVED ,a.entry_form from inv_item_transfer_requ_mst a, approval_history b where a.id=b.mst_id and b.entry_form=52 and  a.company_id=$company_name $req_no_conds2 $transfer_criteria_conds2 $req_date_cond2 and a.is_approved in (1,3) and b.current_approval_status=1 ";

		  
	}
	else if($approval_type==0)	// unapproval process start
	{
		 
		
		//----------------------------------------------------------
		
		if($user_sequence_no==$min_sequence_no)//"1,2,3,12,13,14 // First user
		{

			$sql ="SELECT ID,transfer_system_id as TRANSFER_SYSTEM_ID,TRANSFER_PREFIX_NUMBER,TO_CHAR(insert_date,'YYYY') as year, challan_no as CHALLAN_NO,transfer_criteria as TRANSFER_CRITERIA, company_id as COMPANY_ID,transfer_date as TRANSFER_DATE, to_company as TO_COMPANY, location_id as LOCATION_ID, to_location_id as TO_LOCATION_ID, from_store_id as FROM_STORE_ID,to_store_id as TO_STORE_ID, remarks as REMARKS, ready_to_approve as READY_TO_APPROVE, is_approved as IS_APPROVED from inv_item_transfer_requ_mst where is_deleted=0 and  status_active=1 and is_approved=0 and entry_form=494  and company_id=$company_name and ready_to_approve=1  $req_year_cond $req_no_conds $transfer_criteria_conds $req_date_cond";
		
		}else{
			
			$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$company_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
		
		

			if($sequence_no=="") // bypass if previous user Yes
			{
				$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup where company_id=$company_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
				// echo $seqSql;
				$seqData=sql_select($seqSql);
				
				$sequence_no_by=$seqData[0][csf('sequence_no_by')];

				$requsition_id=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as requsition_id","inv_item_transfer_requ_mst a, approval_history b","a.id=b.mst_id  and a.company_id=$company_name and b.sequence_no in ($sequence_no_by) and a.ready_to_approve=1 and b.entry_form=52 and b.current_approval_status=1 and a.is_approved in (1,3)","requsition_id");

				$requsition_id=implode(",",array_unique(explode(",",$requsition_id)));
				// echo $requsition_id;
				
				$requsition_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as requsition_id","inv_item_transfer_requ_mst a, approval_history b","a.id=b.mst_id and a.company_id=$company_name and a.ready_to_approve=1  and b.sequence_no=$user_sequence_no  and b.entry_form=52 and b.current_approval_status=1 and a.is_approved in (1,3)","requsition_id");
				$requsition_id_app_byuser=implode(",",array_unique(explode(",",$requsition_id_app_byuser)));

				// echo $requsition_id_app_byuser;

				$result=array_diff(explode(',',$requsition_id),explode(',',$requsition_id_app_byuser));
				// print_r($result);
				$requsition_id=implode(",",$result);

				if($requsition_id!="")
				{
					
					//echo $sql;

					$sql ="SELECT ID,transfer_system_id as TRANSFER_SYSTEM_ID,TRANSFER_PREFIX_NUMBER,TO_CHAR(insert_date,'YYYY') as year, challan_no as CHALLAN_NO,transfer_criteria as TRANSFER_CRITERIA, company_id as COMPANY_ID,transfer_date as TRANSFER_DATE, to_company as TO_COMPANY, location_id as LOCATION_ID, to_location_id as TO_LOCATION_ID, from_store_id as FROM_STORE_ID,to_store_id as TO_STORE_ID, remarks as REMARKS, ready_to_approve as READY_TO_APPROVE, is_approved as IS_APPROVED from inv_item_transfer_requ_mst where is_deleted=0 and  status_active=1 and entry_form=494  and company_id=$company_name and ready_to_approve=1  and id in ($requsition_id) $req_year_cond $req_no_conds $transfer_criteria_conds $req_date_cond";
				
				}
				else
				{ 
					$sql ="SELECT ID,transfer_system_id as TRANSFER_SYSTEM_ID,TRANSFER_PREFIX_NUMBER,TO_CHAR(insert_date,'YYYY') as year, challan_no as CHALLAN_NO,transfer_criteria as TRANSFER_CRITERIA, company_id as COMPANY_ID,transfer_date as TRANSFER_DATE, to_company as TO_COMPANY, location_id as LOCATION_ID, to_location_id as TO_LOCATION_ID, from_store_id as FROM_STORE_ID,to_store_id as TO_STORE_ID, remarks as REMARKS, ready_to_approve as READY_TO_APPROVE, is_approved as IS_APPROVED from inv_item_transfer_requ_mst where is_deleted=0 and  status_active=1 and entry_form=494 and is_approved=0 and company_id=$company_name and ready_to_approve=1  $req_year_cond $req_no_conds $transfer_criteria_conds $req_date_cond";
				}

				
			}else{
				$sql ="SELECT ID,transfer_system_id as TRANSFER_SYSTEM_ID,TRANSFER_PREFIX_NUMBER,TO_CHAR(insert_date,'YYYY') as year, challan_no as CHALLAN_NO,transfer_criteria as TRANSFER_CRITERIA, company_id as COMPANY_ID,transfer_date as TRANSFER_DATE, to_company as TO_COMPANY, location_id as LOCATION_ID, to_location_id as TO_LOCATION_ID, from_store_id as FROM_STORE_ID,to_store_id as TO_STORE_ID, remarks as REMARKS, ready_to_approve as READY_TO_APPROVE, is_approved as IS_APPROVED from inv_item_transfer_requ_mst where is_deleted=0 and  status_active=1 and entry_form=494 and is_approved=0 and company_id=$company_name and ready_to_approve=1  $req_year_cond $req_no_conds $transfer_criteria_conds $req_date_cond";
			}


		}
		
		//-----------------------------------------
		
	}
	else // approval process start
	{
		
		$sequence_no_cond=" and b.sequence_no='$user_sequence_no'";
		$sql="SELECT a.id,a.transfer_system_id as TRANSFER_SYSTEM_ID,a.TRANSFER_PREFIX_NUMBER,TO_CHAR(a.insert_date,'YYYY') as year, a.challan_no as CHALLAN_NO,a.transfer_criteria as TRANSFER_CRITERIA, a.company_id as COMPANY_ID,a.transfer_date as TRANSFER_DATE, a.to_company as TO_COMPANY, a.location_id as LOCATION_ID, a.to_location_id as TO_LOCATION_ID, a.from_store_id as FROM_STORE_ID, a.to_store_id as TO_STORE_ID, a.remarks as REMARKS, a.ready_to_approve as READY_TO_APPROVE, a.is_approved as IS_APPROVED ,a.entry_form,b.id as approval_id from inv_item_transfer_requ_mst a, approval_history b where a.id=b.mst_id and b.entry_form=52 and  a.company_id=$company_name  $sequence_no_cond $req_no_conds2 $transfer_criteria_conds2 $req_date_cond2 and a.is_approved in (1,3) and b.current_approval_status=1 ";

	}
	// echo $sql;die;
 
	
  
  
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
	//$user_id=7;
	
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';
	
	if($_REQUEST['txt_alter_user_id']!=""){$user_id_approval=$_REQUEST['txt_alter_user_id'];}
	else{$user_id_approval=$user_id;}

	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$menu_id and user_id=$user_id_approval and company_id = $cbo_company_name and is_deleted = 0");
   
    $min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$menu_id and is_deleted = 0");

	if($approval_type==0)
	{
		$response=$req_nos;

		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0 order by sequence_no desc");

		if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
        
        
		
		//Item store lave start...............................................
		$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
		$cbo_store_id=str_replace("'","",$cbo_store_id);
		
		$app_setup_sql= "select USER_ID, SEQUENCE_NO,BYPASS from electronic_approval_setup where company_id=$cbo_company_name and page_id=$menu_id and is_deleted = 0";	
		$app_setup_sql_res=sql_select( $app_setup_sql );
		foreach ($app_setup_sql_res as $row)
		{
			$appDataArr['BYPASS'][$row[USER_ID]]=$row[BYPASS];
			$appDataArr['USER_WISE_SEQ'][$row[USER_ID]]=$row[SEQUENCE_NO];
			$appDataArr['SEQUENCE_WISE_USER'][$row[SEQUENCE_NO]]=$row[USER_ID];
			$appDataArr['USER_ID'][$row[USER_ID]]=$row[USER_ID];
		}
		
		
		$user_sql="select ID, STORE_LOCATION_ID,ITEM_CATE_ID from user_passwd where id in(".implode(',',$appDataArr['USER_ID']).")";	
		$user_sql_res=sql_select( $user_sql );
		foreach ($user_sql_res as $row)
		{
			$userDataArr[$row[ID]]['STORE_ID']=$row[STORE_LOCATION_ID];
			
		}
		
		
		$nextAllUserStorIdArr=array();
		$nextAllUserItemIdArr=array();
		foreach($appDataArr['SEQUENCE_WISE_USER'] as $seqId=>$userId){
			if($seqId>$appDataArr['USER_WISE_SEQ'][$user_id_approval]){
				$nextAllUserStorIdArr[$userId]=$userDataArr[$userId]['STORE_ID'];
		
			}
		}
	
	
	

		if(count($nextAllUserStorIdArr)>0){$nextAllUserStorIdArr=array_unique(explode(',',implode(',',$nextAllUserStorIdArr)));}

	
		
		$sql="select ID, COMPANY_ID,to_store_id as STORE_NAME, READY_TO_APPROVE, IS_APPROVED from inv_item_transfer_requ_mst where id in ($req_nos)";
		$sqlRes=sql_select( $sql );
		foreach ($sqlRes as $row)
		{
			$sotreIdArr[$row[ID]]=$row[STORE_NAME];
			
		}
 
		
		foreach($sotreIdArr as $sysId=>$storeId){
			if(in_array($storeId,$nextAllUserStorIdArr) && count($nextAllUserStorIdArr)>0){
				$approvalStatusBySysIdArr[$sysId]=3;
			} else {
				$approvalStatusBySysIdArr[$sysId]=1;
			}
		}

		
		//print_r($storeId);print_r($nextAllUserStorIdArr);die
		
		
		foreach($approvalStatusBySysIdArr as $sysId=>$statusVal){
			$data_array_up[$sysId] =array($approvalStatusBySysIdArr[$sysId]); 
			$sys_id_up_array[]=$sysId;
			
		}
		
	
		$field_array_up="is_approved";
		$rID=execute_query(bulk_update_sql_statement( "inv_item_transfer_requ_mst", "id", $field_array_up, $data_array_up, $sys_id_up_array ));
        if($rID) $flag=1; else $flag=0;
		 
		// echo "10**".$rID_up; die;
		//...............................................Item store lave end;
		
		$reqs_ids=explode(",",$req_nos);

		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date";  
		
		$i=0;
        $id=return_next_id( "id","approval_history", 1 ) ;
        
        $approved_no_array=array();
		foreach($reqs_ids as $val)
        {
            $approved_no=return_field_value("max(approved_no) as approved_no","approval_history","mst_id='$val' and entry_form=52","approved_no");
            $approved_no=$approved_no+1;
        
            if($i!=0) $data_array.=",";
             
            $data_array.="(".$id.",52,".$val.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id_approval.",'".$pc_date_time."')";
            
            $approved_no_array[$val]=$approved_no;
                
            $id=$id+1;
            $i++;
        }
		
		//echo "10**";
		/*$approved_string="";
    
        foreach($approved_no_array as $key=>$value)
        {
            $approved_string.=" WHEN $key THEN $value";
        }
        
        $approved_string_mst="CASE id ".$approved_string." END";
        $approved_string_dtls="CASE mst_id ".$approved_string." END";
		
		$sql_insert="INSERT into inv_pur_requisition_mst_hist(id, hist_mst_id, approved_no, entry_form, requ_no, requ_no_prefix, requ_prefix_num, company_id, item_category_id, supplier_id, location_id, division_id, department_id, section_id, requisition_date, store_name, pay_mode, source, cbo_currency, delivery_date, do_no, attention, remarks, terms_and_condition, manual_req, ready_to_approve, is_approved, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date) 
		select	
		'', id, $approved_string_mst, entry_form, requ_no, requ_no_prefix, requ_prefix_num, company_id, item_category_id, supplier_id, location_id, division_id, department_id, section_id, requisition_date, store_name, pay_mode, source, cbo_currency, delivery_date, do_no, attention, remarks, terms_and_condition, manual_req, ready_to_approve, is_approved, status_active, is_deleted, inserted_by,insert_date,updated_by,update_date from  inv_purchase_requisition_mst where id in ($req_nos)";
		
		//echo $sql_insert;
		$sql_insert_dtls="INSERT into  inv_pur_requisition_dtls_hist(id, approved_no, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, style_ref_no, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted) 
		select	
		'', $approved_string_dtls, mst_id, product_id, user_code_maintain, required_for, job_id, job_no, buyer_id, style_ref_no, color_id, count_id, composition_id, com_percent, yarn_type_id, yarn_inhouse_date, cons_uom, quantity, rate, amount, stock, remarks, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted from  inv_purchase_requisition_dtls where mst_id in ($req_nos)";

        //$rID=sql_multirow_update("inv_purchase_requisition_mst","is_approved",$partial_approval,"id",$req_nos,0);    
        //if($rID) $flag=1; else $flag=0;

        $query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=52 and mst_id in ($approval_ids)"; //die;
        $rIDapp=execute_query($query,1);
        if($flag==1) 
        {
            if($rIDapp) $flag=1; else $flag=0; 
        }*/
		//echo "insert into approval_history $field_array values($data_array)";die;
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
        //echo "10**".$rID.'='.$rIDapp.'='.$rID2.'='.$rID3.'='.$rID4.'='.$flag; die;
        if($flag==1) $msg='19'; else $msg='21'; 
	}
	else
	{
		
		/*$req_nos = explode(',',$req_nos); 
		
		$reqs_ids=''; $app_ids='';
		
		foreach($req_nos as $value)
		{
			$data = explode('**',$value);
			$reqs_id=$data[0];
			$app_id=$data[1];
			
			if($reqs_ids=='') $reqs_ids=$reqs_id; else $reqs_ids.=",".$reqs_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}*/
		
		$rID=sql_multirow_update("inv_item_transfer_requ_mst","is_approved*ready_to_approve",'0*2',"id",$req_nos,0);
		if($rID) $flag=1; else $flag=0;
		
		$query="UPDATE approval_history SET current_approval_status=0 WHERE entry_form=52 and mst_id in ($req_nos)";
		$rID2=execute_query($query,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
			
		// $data=$user_id."*'".$pc_date_time."'";		
		// $rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date",$data,"id",$app_ids,1);
		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id_approval."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$approval_ids,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		$response=$reqs_ids;
		
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