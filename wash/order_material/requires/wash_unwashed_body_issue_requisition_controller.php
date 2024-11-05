<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();	 
}

if ($action=="load_drop_down_buyer")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
} 

if ($action=="load_drop_down_buyer_pop")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
} 

if($action=="load_drop_down_emb_type")
{
	$data=explode('_',$data);
	
	if($data[0]==1) $emb_type=$emblishment_print_type;
	else if($data[0]==2) $emb_type=$emblishment_embroy_type;
	else if($data[0]==3) $emb_type=$emblishment_wash_type;
	else if($data[0]==4) $emb_type=$emblishment_spwork_type;
	else if($data[0]==5) $emb_type=$emblishment_gmts_type;
	
	echo create_drop_down( "cboReType_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$trans_Type="1";
	// Insert Start Here ----------------------------------------------------------
	if ($operation==0)   
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		
	

				
		$new_requisition_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'UBIR' , date("Y",time()), 5, "select id,prefix_no,prefix_no_num from sub_issue_requisition_mst where company_id=$cbo_company_name and requisition_type='$trans_Type' and entry_form=473 $insert_date_con order by id desc ", "prefix_no", "prefix_no_num" ));

		/*if(is_duplicate_field( "a.chalan_no", "sub_issue_requisition_mst a, sub_issue_requisition_dtls b", "a.sys_no='$new_requisition_no[0]' and a.chalan_no=$txt_requisition_remarks and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" )==1) //  and b.order_id=$order_no_id and b.color_id=$color_id
		{
			//check_table_status( $_SESSION['menu_id'],0);
			echo "11**0"; 
			disconnect($con); die;			
		}	*/		
		
		/*$max_transaction_date = return_field_value("max(subcon_date) as max_date", "sub_issue_requisition_mst", "embl_job_no=$txt_job_no and status_active = 1", "max_date");      
		if($max_transaction_date != "")
		{
			$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_requesition_date)));
			if ($receive_date < $max_transaction_date) 
			{
				echo "20**Requisition Date Can not Be Less Than Last Transaction Date Of This Job";
				//check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
				die;
			}
		}
		*/
		
 	 
		$id=return_next_id("id","sub_issue_requisition_mst",1) ;
		$field_array="id, entry_form, prefix_no, prefix_no_num, sys_no, requisition_type, company_id, location_id, party_id, remarks, requisition_date, within_group, embl_job_no, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",'473','".$new_requisition_no[1]."','".$new_requisition_no[2]."','".$new_requisition_no[0]."','".$trans_Type."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_party_name.",".$txt_requisition_remarks.",".$txt_requesition_date.",".$cbo_within_group.",".$txt_job_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";  
		//echo "INSERT INTO sub_issue_requisition_mst (".$field_array.") VALUES ".$data_array; disconnect($con); die;
		$txt_requesition_no=$new_requisition_no[0];//change_date_format($data[2], "dd-mm-yyyy", "-",1)
		
		$id1=return_next_id("id","sub_issue_requisition_dtls",1) ;
		$field_array2="id, mst_id, quantity,receive_qty, uom, job_dtls_id, buyer_po_id, remarks,receive_dtls_id,receive_id,job_id,entry_form, inserted_by, insert_date, status_active, is_deleted";
		$data_array2="";  $add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{
			$ordernoid			= "ordernoid_".$i; 
			$txtbuyerPoId		= "txtbuyerPoId_".$i;
			$cbouom				= "cbouom_".$i;
			$txtreceiveqty		= "txtreceiveqty_".$i;
			$txtrequisitionqty	= "txtrequisitionqty_".$i;
			$jobid	= "jobid_".$i;
			$receiveid	= "receiveid_".$i;
			$receivedtlsid		= "receivedtlsid_".$i;
			$txtremarks			= "txtremarks_".$i;
			$updatedtlsid		= "updatedtlsid_".$i;
			
			if ($add_commaa!=0) $data_array2 .=",";
			 
			$data_array2.="(".$id1.",'".$id."',".$$txtrequisitionqty.",".$$txtreceiveqty.",".$$cbouom.",".$$ordernoid.",".$$txtbuyerPoId.",".$$txtremarks.",".$$receivedtlsid.",".$$receiveid.",".$$jobid.",'473',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			 
			$id1=$id1+1; $add_commaa++;
		}
		//echo "10**INSERT INTO sub_issue_requisition_dtls (".$field_array2.") VALUES ".$data_array2; die;
		$flag=1;
		//echo "10**INSERT INTO sub_issue_requisition_dtls (".$field_array2.") VALUES ".$data_array2; die;
		$rID=sql_insert("sub_issue_requisition_mst",$field_array,$data_array,0);
		if($flag==1 && $rID==1) $flag=1; else $flag=0;
		$rID2=sql_insert("sub_issue_requisition_dtls",$field_array2,$data_array2,1);	
		if($flag==1 && $rID2==1) $flag=1; else $flag=0;
			//echo "10**".$rID."**".$rID2	; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_requesition_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_requesition_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_requesition_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_requesition_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		disconnect($con);
		die;		
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		
	/*
		$chk_next_transaction=return_field_value("id","sub_issue_requisition_mst","trans_type in(2,3,6) and embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and entry_form in (297,372) and id >$update_id ","id");
		if($chk_next_transaction !="")
		{ 
			echo "18**Update not allowed.This item is used in another transaction";disconnect($con); die;
		}
		
	
		$max_transaction_date = return_field_value("max(subcon_date) as max_date", "sub_issue_requisition_mst", "embl_job_no=$txt_job_no and id <> $update_id and status_active = 1", "max_date");      
		if($max_transaction_date != "")
		{
			$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_requesition_date)));
			if ($receive_date < $max_transaction_date) 
			{
				echo "20**Requisition Date Can not Be Less Than Last Transaction Date Of This Job";
				//check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
				die;
			}
		}*/
		
		/*$iss_number=return_field_value( "sys_no", "sub_issue_requisition_mst"," embl_job_no=$txt_job_no and status_active=1 and entry_form=297 and is_deleted=0 and trans_type=2");
		if($iss_number){
			echo "washIssue**".str_replace("'","",$txt_job_no)."**".$iss_number;
			disconnect($con); die;
		}
		
		$receive_return_number=return_field_value( "sys_no", "sub_issue_requisition_mst"," embl_job_no=$txt_job_no and status_active=1 and entry_form=372 and is_deleted=0 and trans_type=3");
		if($receive_return_number){
			echo "washreturn**".str_replace("'","",$txt_job_no)."**".$receive_return_number;
			disconnect($con); die;
		}*/
		
	
		$rec_sql_dtls="Select b.id from sub_issue_requisition_dtls b, sub_issue_requisition_mst a where a.id=b.mst_id and a.id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.requisition_type=1";//
		$all_dtls_id_arr=array();
		//echo "10**".$rec_sql_dtls; die;
		$nameArray=sql_select( $rec_sql_dtls ); 
		foreach($nameArray as $row)
		{
			$all_dtls_id_arr[]=$row[csf('id')];
		}
		unset($nameArray);

		$field_array="location_id*party_id*remarks*requisition_date*embl_job_no*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$cbo_party_name."*".$txt_requisition_remarks."*".$txt_requesition_date."*".$txt_job_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
		$field_array2="id, mst_id, quantity,receive_qty, uom, job_dtls_id, buyer_po_id, remarks,receive_dtls_id,receive_id,job_id,entry_form, inserted_by, insert_date, status_active, is_deleted";
		$field_arr_up="quantity*receive_qty*uom*job_dtls_id*receive_dtls_id*receive_id*job_id*buyer_po_id*remarks*updated_by*update_date";
		
		$id1=return_next_id("id","sub_issue_requisition_dtls",1);
		$data_array2="";  $add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{
			$ordernoid			= "ordernoid_".$i; 
			$txtbuyerPoId		= "txtbuyerPoId_".$i;
			$cbouom				= "cbouom_".$i;
			$txtreceiveqty		= "txtreceiveqty_".$i;
			$jobid	= "jobid_".$i;
			$receiveid	= "receiveid_".$i;
			$receivedtlsid		= "receivedtlsid_".$i;
			$txtrequisitionqty	= "txtrequisitionqty_".$i;
			$txtremarks			= "txtremarks_".$i;
			$updatedtlsid		= "updatedtlsid_".$i;
			
			if(str_replace("'","",$$updatedtlsid)=="")
			{
				if ($add_commaa!=0) $data_array2 .=",";
				$data_array2.="(".$id1.",'".$update_id."',".$$txtrequisitionqty.",".$$txtreceiveqty.",".$$cbouom.",".$$ordernoid.",".$$txtbuyerPoId.",".$$txtremarks.",".$$receivedtlsid.",".$$receiveid.",".$$jobid.",'473',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_arr_rec[]=$id1;
				$id1=$id1+1; $add_commaa++;
			}
			else if(str_replace("'","",$$updatedtlsid)!="")
			{
				$data_arr_up[str_replace("'","",$$updatedtlsid)]=explode("*",("".$$txtrequisitionqty."*".$$txtreceiveqty."*".$$cbouom."*".$$ordernoid."*".$$receivedtlsid."*".$$receiveid."*".$$jobid."*".$$txtbuyerPoId."*".$$txtremarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$id_arr_rec[]=str_replace("'","",$$updatedtlsid);
				$hdn_break_id_arr[]=str_replace("'","",$$updatedtlsid);
			}
		}
		$flag=1;
		$rID=sql_update("sub_issue_requisition_mst",$field_array,$data_array,"id",$update_id,0); 
		if($rID==1 && $flag==1) $flag=1; else $flag=0;	
		if($data_array2!="")
		{
			//echo "10**INSERT INTO sub_issue_requisition_dtls (".$field_array2.") VALUES ".$data_array2; die;
			$rID2=sql_insert("sub_issue_requisition_dtls",$field_array2,$data_array2,1);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
			
		if($data_arr_up!="")
		{
			//echo "10**".bulk_update_sql_statement( "sub_issue_requisition_dtls", "id", $field_arr_up,$data_arr_up,$hdn_break_id_arr);
			$rID3=execute_query(bulk_update_sql_statement( "sub_issue_requisition_dtls", "id", $field_arr_up,$data_arr_up,$hdn_break_id_arr),1);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$distance_delete_id="";
		if(implode(',',$id_arr_rec)!="")
		{
			$distance_delete_id=implode(',',array_diff($all_dtls_id_arr,$id_arr_rec));
		}
		else
		{
			$distance_delete_id=implode(',',$all_dtls_id_arr);
		}
		if(str_replace("'",'',$distance_delete_id)!="")
		{
			$delete_id=explode(",",$distance_delete_id);
			$rID4=execute_query( "update sub_issue_requisition_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in ($distance_delete_id)",1);
			if($rID4==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID."**".$rID2."**".$rID3."**".$rID4."**".implode(',',$all_dtls_id_arr); die;
		 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_requesition_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);	
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_requesition_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_requesition_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);	
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_requesition_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		disconnect($con); die;
	}
	else if ($operation==2)   // delete
	{
		$con = connect();
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 //echo $zero_val;
		/*$iss_number=return_field_value( "sys_no", "sub_issue_requisition_mst"," embl_job_no=$txt_job_no and status_active=1 and entry_form=297 and is_deleted=0 and trans_type=2");
		if($iss_number){
			echo "washIssue**".str_replace("'","",$txt_job_no)."**".$iss_number;
			disconnect($con); die;
		}*/
		
			$chk_next_transaction=return_field_value("id","sub_issue_requisition_mst","requisition_type in(1) and embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and entry_form in (473) and id >$update_id ","id");
			if($chk_next_transaction !="")
			{ 
				echo "18**Deleted not allowed.This item is used in another transaction";disconnect($con); die;
			}
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$data_array_dtls="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
		$flag=1;
		$rID=sql_update("sub_issue_requisition_mst",$field_array,$data_array,"id",$update_id,0); 
		if($rID==1 && $flag==1) $flag=1; else $flag=0; 
		//echo "INSERT INTO sub_issue_requisition_dtls (".$field_array.") VALUES ".$data_array_dtls; disconnect($con); die;

		$rID1=sql_update("sub_issue_requisition_dtls",$field_array,$data_array_dtls,"mst_id",$update_id,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;  
			
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_requesition_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_requesition_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_requesition_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_requesition_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
		}
		disconnect($con); die; 
	}
}

if ($action=="requisition_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	$company=$data[0];
	$location=$data[1];
	$party_name=$data[2];
	$within_group=$data[3];
	?>
	<script>
		function js_set_value(id)
		{ 
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,party_name,within_group)
		{   //alert(company+'_'+party_name+'_'+within_group);	
			load_drop_down( 'wash_unwashed_body_issue_requisition_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer PO');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $company;?>,<? echo $party_name;?>,<? echo $within_group;?>)">
        <div align="center" style="width:100%;" >
            <form name="searchreceivefrm_1"  id="searchreceivefrm_1" autocomplete="off">
                <table width="870" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="10"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>                	 
                            <th width="140">Company Name</th>
                            <th width="50">Within Group</th>
                            <th width="120">Party Name</th>
                            <th width="70">Requisition ID</th>
                            <th width="80" style="display:none">Challan No</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Wash Job No</th>
                            <th width="100" colspan="2" class="must_entry_caption">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'wash_unwashed_body_issue_requisition_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 1, "-- Select --",$within_group, "load_drop_down( 'wash_unwashed_body_issue_requisition_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Requisition ID" />
                            </td>
                            <td style="display:none">
                                <input type="text" name="txt_search_challan" id="txt_search_challan" class="text_boxes" style="width:70px" placeholder="Challan" />
                            </td>
                            <td>
								<?
                                    $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From" readonly>
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To" readonly>
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="checkFields();showList();" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <div id="search_div"></div>   
            </form>
        </div>
	</body>
	<script>
    	var isValidated = false;
    	function checkFields() {
    		var rcvId = document.getElementById('txt_search_common').value;
    		var challan = document.getElementById('txt_search_challan').value;
    		var searchString = document.getElementById('txt_search_string').value;

    		if(searchString == '' && rcvId == '' && challan == '' ) {
    			if( !form_validation('txt_date_from*txt_date_to','Date From*Date To') ) {
					return;
				}
    		}

    		isValidated = true;
    	}

    	function showList() {
    		if(!isValidated) {
    			return;
    		}

    		show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_requisition_search_list_view', 'search_div', 'wash_unwashed_body_issue_requisition_controller', 'setFilterGrid(\'tbl_po_list\',-1)');
    		isValidated = false;
    	}
    </script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_requisition_search_list_view")
{
	$data=explode('_',$data);
	$search_type =$data[6];
	$within_group =$data[7];
	$search_by=str_replace("'","",$data[8]);
	$search_str=trim(str_replace("'","",$data[9]));

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and a.requisition_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $recieve_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and a.requisition_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $recieve_date ="";
	}
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
		
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num='$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no='$data[5]'"; else $challan_no_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{  //echo $search_by; die;
		
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";  
			/*$order_buyer_po_array=array();
			$order_buyer_po='';
			$order_sql ="select b.id from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form='295' $search_com_cond"; 
			$order_sql_res=sql_select($order_sql);
			foreach ($order_sql_res as $row)
			{
				$order_buyer_po_array[]=$row[csf("id")];
			}
			//unset($order_sql_res);
			$order_buyer_po=implode(",",$order_buyer_po_array);
			//echo $order_buyer_po; 
			
			if ($order_buyer_po!="") $order_order_buyer_poCond=" and b.job_dtls_id in ($order_buyer_po)"; else $order_order_buyer_poCond="";*/ 
			
			//echo $order_idsCond;
		}
		
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '%$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]%'"; else $challan_no_cond="";
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";   
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '$data[5]%'"; else $challan_no_cond="";
		if ($data[9]!='') $order_no_cond=" and order_no like '$data[9]%'"; else $order_no_cond="";
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '%$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]'"; else $challan_no_cond="";
	}	
	
	
	
	$order_buyer_po_array=array();
	$buyer_po_arr=array();
	$order_buyer_po='';
	$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form='295' $search_com_cond"; 

	$order_sql_res=sql_select($order_sql);
	foreach ($order_sql_res as $row)
	{
		$order_buyer_po_array[]=$row[csf("id")];
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
	}
	//unset($order_sql_res);
	$order_buyer_po=implode(",",$order_buyer_po_array);
	//echo $order_buyer_po; 
	if ($order_buyer_po!="") $order_order_buyer_poCond=" and b.job_dtls_id in ($order_buyer_po)"; else $order_order_buyer_poCond="";
	
	
	//die;
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array("select id, company_name from lib_company",'id','company_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	
	$po_ids='';// $buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id)";
		else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		/*if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
		}
		//echo $po_ids;
		if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
		
		$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);*/
	}
	$spo_ids='';
	
	if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
		$insert_date_cond="year(a.insert_date)";
		$wo_cond="group_concat(distinct(b.job_dtls_id))";
		$buyer_po_id_cond="group_concat(distinct(b.buyer_po_id))";
	}
	else if($db_type==2)
	{
		$id_cond="listagg(b.id,',') within group (order by b.id)";
		$insert_date_cond="TO_CHAR(a.insert_date,'YYYY')";
		$wo_cond="listagg(b.job_dtls_id,',') within group (order by b.job_dtls_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}
	/*if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond", "id");
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";*/
	
	/*$sql= "select a.id, a.sys_no, a.prefix_no_num, $insert_date_cond as year, a.location_id, a.within_group, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.embl_job_no, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id from sub_issue_requisition_mst a, sub_issue_requisition_dtls b where a.id=b.mst_id and a.trans_type=1 and a.entry_form='296' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $order_idsCond group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.within_group, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.embl_job_no order by a.id DESC ";*/
	
	/*select 
   rowid, id, prefix_no, prefix_no_num, 
   sys_no, requisition_type, company_id, 
   location_id, requisition_source, party_id, 
   requisition_date, chalan_no, remarks, 
   inserted_by, insert_date, updated_by, 
   update_date, status_active, is_deleted, 
   within_group, entry_form, embl_job_no, 
   issue_to, receive_no, receive_id, 
   multi_receive_id, emb_job_id
from logic3rdversion.sub_issue_requisition_mst*/
	
	  $sql= "select a.id, a.sys_no, a.prefix_no_num, $insert_date_cond as year, a.location_id, a.within_group, a.party_id, a.requisition_date, a.chalan_no, a.remarks, a.embl_job_no, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id,sum(b.quantity) as quantity from sub_issue_requisition_mst a, sub_issue_requisition_dtls b where a.id=b.mst_id and a.requisition_type=1 and a.entry_form='473' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond  $order_order_buyer_poCond  group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.within_group, a.party_id, a.requisition_date, a.chalan_no, a.remarks, a.embl_job_no order by a.id DESC ";
	//echo $sql; 
	$result = sql_select($sql);
	?>
    <div style="width:970;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="100">Job NO</th>
                 <th width="70">Requisition No</th>
                <th width="70">Year</th>
                <th width="100">Party Name</th>
                <th width="80">Requisition Date</th>
                <th width="100">Order No</th>
                <th width="100">Buyer PO</th> 
                <th width="100">Buyer Style</th>
                <th>Requisition Qty</th>
            </thead>
     	</table>
     <div style="width:970; max-height:270px;overflow-y:scroll;">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_no='';
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
				}
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				
				/*$buyer_po=""; $buyer_style="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));*/
				
				
				$buyer_po=""; $buyer_style="";
				$buyer_po_id=explode(",",$row[csf('order_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				
				
				//print_r($buyer_po_arr);
				
				// $buyer_po=$buyer_po_arr[$row[csf('job_dtls_id')]]['po']; 
				// $buyer_style=$buyer_po_arr[$row[csf('job_dtls_id')]]['style'];
				
				$party_name="";
				if($row[csf("within_group")]==1) $party_name=$comp[$row[csf("party_id")]]; else $party_name=$party_arr[$row[csf("party_id")]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")]."_".$row[csf("embl_job_no")];?>');" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="100" align="center"><? echo $row[csf("embl_job_no")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("prefix_no_num")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="100"><? echo $party_name; ?></td>		
						<td width="80"><? echo change_date_format($row[csf("requisition_date")]);  ?></td>
                        <td width="100" style="word-break:break-all"><p><? echo $order_no; ?></p></td>	
                        <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                         <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                        <td style="word-break:break-all"><? echo $row[csf("quantity")]; ?></td>
						
					</tr>
				<? 
				$i++;
            }
   		?>
			</table>
		</div>
     </div>
     <?	
	exit();
}

if ($action=="load_php_data_to_form")
{
	
	
	
	$nameArray=sql_select( "select id, sys_no, company_id, location_id, party_id, requisition_date, chalan_no,within_group, embl_job_no,remarks from sub_issue_requisition_mst where id='$data'" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_requesition_no').value 		= '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n"; 
		
		echo "document.getElementById('cbo_within_group').value		= '".$row[csf("within_group")]."';\n"; 
		echo "load_drop_down( 'requires/wash_unwashed_body_issue_requisition_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";
		
		echo "load_drop_down( 'requires/wash_unwashed_body_issue_requisition_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );\n"; 		
		
		echo "document.getElementById('cbo_location_name').value	= '".$row[csf("location_id")]."';\n";  
		echo "document.getElementById('cbo_party_name').value		= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('txt_requisition_remarks').value	= '".$row[csf("chalan_no")]."';\n"; 
		echo "document.getElementById('txt_requesition_date').value 	= '".change_date_format($row[csf("requisition_date")])."';\n";  
		echo "document.getElementById('txt_job_no').value			= '".$row[csf("embl_job_no")]."';\n"; 
	    echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";  
		echo "document.getElementById('txt_requisition_remarks').value            = '".$row[csf("remarks")]."';\n";
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_unwashed_body_issue_requisition',1);\n";
		
		echo "$('#cbo_within_group').attr('disabled','true')".";\n"; 
		echo "$('#cbo_party_name').attr('disabled','true')".";\n"; 
	}
	exit();
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_order').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			//alert();
			load_drop_down( 'wash_order_entry_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			
			$('#cbo_party_name').attr('disabled','disabled');
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer PO');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}
		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $data[0];?>,<? echo $data[3];?>,<? echo $data[2];?>)">
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170" class="must_entry_caption">Date Range</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Wash Job No</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><input type="hidden" id="selected_order">  
								<?   
									echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );
                                ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" readonly/>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" readonly/>
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',"","" );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="checkFields();showList();" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <br>
                <div id="search_div"></div>   
            </form>
        </div>
	</body>
	<script>
    	var isValidated = false;
    	function checkFields() {
    		var searchString = document.getElementById('txt_search_string').value;

    		if(searchString == '') {
    			if( !form_validation('txt_date_from*txt_date_to','Date From*Date To') ) {
					return;
				}
    		}

    		isValidated = true;
    	}

    	function showList() {
    		if(!isValidated) {
    			return;
    		}

    		show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[3];?>, 'create_job_search_list_view', 'search_div', 'wash_unwashed_body_issue_requisition_controller', 'setFilterGrid(\'tbl_po_list\',-1)');
    		isValidated = false;
    	}
    </script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $buyer=" and a.party_id='$data[1]'"; else { $buyer=""; echo "PLease Select Party name"; die;}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			/*if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $style_cond=" and a.style_ref_no = '$search_str' ";
			else if ($search_by==5) $po_cond=" and b.po_number = '$search_str' ";*/
			
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			/*if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $style_cond=" and a.style_ref_no like '%$search_str%'";  
			else if ($search_by==5) $po_cond=" and b.po_number like '%$search_str%'";  */
			
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'"; 
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'"; 
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '%$search_str%'";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";   
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			/*if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $style_cond=" and a.style_ref_no like '$search_str%'";  
			else if ($search_by==5) $po_cond=" and b.po_number like '$search_str%'";  */
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'"; 
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			/*if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $style_cond=" and a.style_ref_no like '%$search_str'";  
			else if ($search_by==5) $po_cond=" and b.po_number like '%$search_str'";  */
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'"; 
		}
	}	
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	
	/*$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4)|| ($po_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	*/
	
	$buyer_po_arr=array();
	
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//$color_lib_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$arr=array (3=>$comp,6=>$yes_no,7=>$color_lib_arr);
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(b.gmts_color_id)";
		$buyer_po_id_cond="year(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(b.gmts_color_id,',') within group (order by b.gmts_color_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}
	   $sql= "select a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id  
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,sub_material_mst d, sub_material_dtls e  
	 where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond   and b.id=c.mst_id and d.id=e.mst_id  and  b.id=e.job_dtls_id  and d.trans_type=1 and d.entry_form='296' and   d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
	 group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref
	 order by a.id DESC";
	 
	 
	//echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer PO</th>
            <th width="100">Buyer Style</th>
            <th width="80"> Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
        </table>
        <div style="width:885px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="865" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				$buyer_po=""; $buyer_style="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('subcon_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? if ($within_group==1)echo $buyer_po; else echo $row[csf('buyer_po_no')];//echo $buyer_po;  ?></td>
                    <td width="100" style="word-break:break-all"><? if ($within_group==1)echo $buyer_style; echo $row[csf('buyer_style_ref')];//echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td style="word-break:break-all"><? echo $color_name; ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?    
	exit();
}

if($action=="load_php_dtls_form")
{
	//echo $data;
	$exdata=explode("**",$data);
	$jobno=''; 
	$update_id=$exdata[0];
	$jobno=$exdata[1];
	$color_arrey=return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arrey=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	$buyer_po_arr=array();
	
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	$updtls_data_arr=array(); $pre_qty_arr=array();
	
	 $sql_rec="select a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id, a.remarks,a.receive_dtls_id from sub_issue_requisition_dtls a, sub_issue_requisition_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.requisition_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rec_res =sql_select($sql_rec);
	
	foreach ($sql_rec_res as $row)
	{
		$pre_qty_arr[$row[csf("job_dtls_id")]][$row[csf("receive_dtls_id")]]['qty']+=$row[csf("quantity")];
	}
	    if($update_id*1>0)
		{
	 		  $sql_job="select a.id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id, b.order_quantity as qnty, b.buyer_po_no, b.buyer_style_ref,e.receive_qty as receive_qty,e.receive_dtls_id as receive_dtls_id,e.receive_id as receive_id,e.id as update_dtls_id,e.quantity as requisition_qty,e.remarks  from subcon_ord_mst a, subcon_ord_dtls b ,sub_issue_requisition_mst d, sub_issue_requisition_dtls e  
		where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.subcon_job='$jobno'  and d.id=e.mst_id  and  b.id=e.job_dtls_id  and d.requisition_type=1 and d.entry_form='473' and d.id=$update_id and   d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 order by b.id ASC";  
		}
		else
		{
			
			 $sql_job="select a.id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id, b.order_quantity as qnty, b.buyer_po_no, b.buyer_style_ref,e.quantity as receive_qty,e.id as receive_dtls_id,d.id as receive_id  from subcon_ord_mst a, subcon_ord_dtls b ,sub_material_mst d, sub_material_dtls e  
		where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.subcon_job='$jobno'  and d.id=e.mst_id  and  b.id=e.job_dtls_id  and d.trans_type=1 and d.entry_form='296' and   d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 order by b.id ASC";
			
		}
	//echo $sql_job;
	
	$sql_result =sql_select($sql_job);
	$k=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		$k++;
		
		$quantity=0; $dtlsup_id=""; $balanceQty=0; $prerec_qty=0; $orderQty=0; $remarks='';
		if($row[csf("requisition_qty")]!=""){ $current_requisition_qty=$row[csf("requisition_qty")] ;} else{ $current_requisition_qty=0;}
		$prerec_qty=$pre_qty_arr[$row[csf("po_id")]][$row[csf("receive_dtls_id")]]['qty']-$current_requisition_qty;
		$orderQty=($row[csf("order_uom")]==1) ? number_format($row[csf("qnty")],0,'.','') : number_format($row[csf("qnty")]*12,0,'.','');
		$receiveQty=  number_format($row[csf("receive_qty")],0,'.','');
		$balanceQty=number_format($receiveQty-$prerec_qty,4,'.','');
		
		if($update_id!=0)
		{
			$quantity= $row[csf("requisition_qty")];
			$remarks= $row[csf("remarks")];
		}
		else $quantity='';
		if($quantity==0) $quantity='';
		if($balanceQty==0) $balanceQty=0;
		
		//$dtlsup_id=$updtls_data_arr[$row[csf("po_id")]]['dtlsid'];
		?>
		 <tr>
            <td><input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("po_id")]; ?>">
                <input type="hidden" name="jobno_<? echo $k; ?>" id="jobno_<? echo $k; ?>" value="<? echo $row[csf("subcon_job")]; ?>">
                 <input type="hidden" name="jobid_<? echo $k; ?>" id="jobid_<? echo $k; ?>" value="<? echo $row[csf("id")]; ?>">
                <input type="hidden" name="updatedtlsid_<? echo $k; ?>" id="updatedtlsid_<? echo $k; ?>" value="<? echo $row[csf("update_dtls_id")];; ?>">
                <input type="hidden" name="receivedtlsid_<? echo $k; ?>" id="receivedtlsid_<? echo $k; ?>" value="<? echo $row[csf("receive_dtls_id")]; ?>"> 
                <input type="hidden" name="receiveid_<? echo $k; ?>" id="receiveid_<? echo $k; ?>" value="<? echo $row[csf("receive_id")]; ?>">
                <input type="text" name="txtorderno_<? echo $k; ?>" id="txtorderno_<? echo $k; ?>" class="text_boxes" style="width:90px" value="<? echo $row[csf("order_no")]; ?>" readonly />
            </td>
            <td><input name="txtbuyerPo_<? echo $k; ?>" id="txtbuyerPo_<? echo $k; ?>" type="text" class="text_boxes" style="width:90px" value="<? echo $row[csf("buyer_po_no")]; ?>" readonly />
                <input name="txtbuyerPoId_<? echo $k; ?>" id="txtbuyerPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("buyer_po_id")]; ?>" />
            </td>
            <td><input name="txtstyleRef_<? echo $k; ?>" id="txtstyleRef_<? echo $k; ?>" type="text" class="text_boxes" style="width:90px" value="<? echo $row[csf("buyer_style_ref")]; ?>" readonly /></td>
            <td><? echo create_drop_down( "cboGmtsItem_".$k, 90, $garments_item,"", 1, "-- Select --",$row[csf("gmts_item_id")], "",1,"" ); ?></td>
            <td><input type="text" id="txtcolor_<? echo $k; ?>" name="txtcolor_<? echo $k; ?>" class="text_boxes" value="<? echo $color_arrey[$row[csf("color_id")]]; ?>" style="width:80px" readonly/></td>
            <td><input type="text" id="txtsize_<? echo $k; ?>" name="txtsize_<? echo $k; ?>" class="text_boxes" value="<? echo $size_arrey[$row[csf("gmts_size_id")]]; ?>" style="width:70px" readonly/></td>
            <td>
            	<input name="txtordqty_<? echo $k; ?>" id="txtordqty_<? echo $k; ?>" value="<? echo $orderQty; ?>" class="text_boxes_numeric" type="text" style="width:50px" disabled/>
            </td>
          
            <td><input name="txtreceiveqty_<? echo $k; ?>" id="txtreceiveqty_<? echo $k; ?>" class="text_boxes_numeric" type="text"   value="<? echo  $row[csf("receive_qty")]; ; ?>"  style="width:50px"  disabled/></td>
              <td><input name="txtpreqty_<? echo $k; ?>" id="txtpreqty_<? echo $k; ?>" value="<? echo $prerec_qty; ?>" class="text_boxes_numeric" type="text" style="width:50px" disabled/></td>
             <td><input name="txtrequisitionqty_<? echo $k; ?>" id="txtrequisitionqty_<? echo $k; ?>" class="text_boxes_numeric" type="text" onKeyUp="check_receive_qty_ability(this.value,<? echo $k; ?>); fnc_total_calculate();" value="<? echo $quantity; ?>" placeholder="<? echo $balanceQty; ?>" pre_rec_qty="<? echo $prerec_qty; ?>" order_qty="<? echo $receiveQty; ?>" style="width:50px" /></td>
            <td><? echo create_drop_down( "cbouom_".$k,50, $unit_of_measurement,"", 1, "-Select-",1,"", 1,"" );?></td>
            <td><input type="text" name="txtremarks_<? echo $k; ?>" id="txtremarks_<? echo $k; ?>" style="width:40px" value="<? echo $remarks; ?>" class="text_boxes" placeholder="Remark" onClick="openmypage_remarks(<? echo $k; ?>);" /></td>
        </tr>
	<?	
	}
	exit();
}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(val)
	{
		document.getElementById('text_new_remarks').value=val;
		parent.emailwindow.hide();
	}
	</script>
    </head>
<body>
<div align="center">
	<fieldset style="width:400px;margin-left:4px;">
        <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="370" >
                <tr>
                    <td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<? echo $data; ?>" />
                      <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:330px; height:270px" placeholder="Remarks Here. Maximum 1000 Character." ><? echo $data; ?></textarea>
                    </td>
                </tr>
                <tr>
                	<td align="center">
                 <input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
                 	</td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action=="unwashed_issue_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data); die;

	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arrs=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	if($data[3]==2)
	{
		$buyer_arr=$buyer_arrs;
	}
	else
	{
		$buyer_arr=$company_library;
	}

		
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	  $sql="select id, sys_no, company_id, location_id, party_id, requisition_date, chalan_no,within_group, embl_job_no,remarks  from sub_issue_requisition_mst where sys_no='$data[1]' and status_active=1 and is_deleted=0";
	$dataArray=sql_select($sql);


	?>
	<div style="width:1160px;">
    <table width="1160" cellspacing="0" align="right" border="0">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="6" align="center">
				<?
					$nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website, contact_no from lib_company where id=$data[0] and status_active=1 and is_deleted=0");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <?php echo $result[csf('province')];?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];?> <br>
                        <b> Mobile No : <? echo $result[csf('contact_no')]; ?></b> <?
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> </u></strong></center></td>
        </tr>
        <tr>
         	<td width="180"><strong> Company Name:</strong></td><td width="195px"><? echo $company_library[$dataArray[0][csf('company_id')]]; ?></td>
            <td width="180"><strong>Location:</strong></td> <td width="195px"><? echo $location_library[$dataArray[0][csf('location_id')]]; ?></td>
            <td width="180"><strong>Within Group:</strong></td><td width="195px"><? echo $yes_no[$dataArray[0][csf('within_group')]]; ?></td>
           
        </tr>
        <tr>
            <td width="180"><strong>Requisition  Date:</strong></td> <td width="195px"><? echo change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
            <td width="180"><strong>Job No:</strong></td><td width="195px"><? echo $dataArray[0][csf('embl_job_no')]; ?></td>
            <td width="180"><strong>Party:</strong></td><td width="195px"><? echo $buyer_arr[$dataArray[0][csf('party_id')]]; ?></td>
        </tr>
        <tr>
            <td width="180"><strong>Requisition No:</strong></td> <td width="195px"><? echo $dataArray[0][csf('sys_no')]; ?></td>
            <td width="180"><strong>Remarks</strong></td> <td width="195px"><? echo $dataArray[0][csf('remarks')]; ?></td>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr style=" height:20px">
				<td colspan="6">&nbsp;</td>
	    </tr>
    </table>
         <br>
	<div style="width:100%;">
		<table align="right" cellspacing="0" width="1160"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="120" align="center">Order No</th>
                <th width="100" align="center">Buyer PO</th>
                <th width="100" align="center">Style Ref.</th>
                <th width="100" align="center">Garments Item</th>
                <th width="100" align="center">Color</th>
                <th width="100" align="center">Size</th>
                <th width="100" align="center">Order Qty (Pcs)</th>
                <th width="100" align="center">Received Qty (Pcs)</th>
                <th width="100" align="center">Prev. Requ. Qty</th>
                <th width="100" align="center">Requ. Qty (Pcs)</th>
                <th width="100" align="center">Balance Qty (Pcs)</th>
                <th  align="center">UOM</th>
            </thead>
   <?
	 
    
	$jobno=$dataArray[0][csf('embl_job_no')];
	$mst_id=$dataArray[0][csf('id')];
	$update_id=$dataArray[0][csf('id')];

	$color_arrey=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$size_arrey=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');
	
	$updtls_data_arr=array(); $pre_qty_arr=array();
	
	 $sql_rec="select a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id, a.remarks,a.receive_dtls_id from sub_issue_requisition_dtls a, sub_issue_requisition_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.requisition_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rec_res =sql_select($sql_rec);
	
	foreach ($sql_rec_res as $row)
	{
			$pre_qty_arr[$row[csf("job_dtls_id")]][$row[csf("receive_dtls_id")]]['qty']+=$row[csf("quantity")];
		
	}
	
	 	
		
		$sql_job="select a.id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id, b.order_quantity as qnty, b.buyer_po_no, b.buyer_style_ref,e.receive_qty as receive_qty,e.receive_dtls_id as receive_dtls_id,e.receive_id as receive_id,e.id as update_dtls_id,e.quantity as requisition_qty,e.remarks  from subcon_ord_mst a, subcon_ord_dtls b ,sub_issue_requisition_mst d, sub_issue_requisition_dtls e  
		where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.subcon_job='$jobno'  and d.id=e.mst_id  and  b.id=e.job_dtls_id  and d.requisition_type=1 and d.entry_form='473' and d.id=$data[4] and   d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 order by b.id ASC";  
	
	
 	$k=0;
	$sql_result =sql_select($sql_job);
	foreach($sql_result as $row)
	{
		$k++;
		if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$quantity=0; $dtlsup_id=""; $balanceQty=0; $prerec_qty=0; $orderQty=0; $remarks='';  
		$prerec_qty=$pre_qty_arr[$row[csf("po_id")]][$row[csf("receive_dtls_id")]]['qty'];
		$orderQty=($row[csf("order_uom")]==1) ? number_format($row[csf("qnty")],0,'.','') : number_format($row[csf("qnty")]*12,0,'.','');
		$receiveQty=  number_format($row[csf("receive_qty")],0,'.','');
		
		
		
		if($update_id!=0)
		{
			$quantity= $row[csf("requisition_qty")];
			$remarks= $row[csf("remarks")];
		}
		else $quantity='';
		if($quantity==0) $quantity='';
		if($balanceQty==0) $balanceQty=0;
		$totalquantity=$quantity+$prerec_qty;
		$balanceQty=number_format(($receiveQty-$totalquantity),4,'.','');
		
		$total_qty+=$quantity;
 		?>
		<tr bgcolor="<? echo $bgcolor; ?>">
            <td width="30"><? echo $k; ?></td>
            <td width="120"><p><? echo $row[csf("order_no")]; ?></p></td>
            <td width="100"><p><? echo $row[csf("buyer_po_no")];; ?></p></td>
            <td width="100"><p><? echo $row[csf("buyer_style_ref")]; ?></p></td>
            <td width="100"><p><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></p></td>
            <td width="100"><p><? echo $color_arrey[$row[csf("color_id")]];; ?></p></td>
            <td width="100"><p><? echo $size_arrey[$row[csf("gmts_size_id")]]; ?></p></td>
            <td width="100" align="right"><p><? echo $orderQty; ?></p></td>
            <td width="100" align="right"><p><? echo $row[csf("receive_qty")]; ?></p></td>
            <td width="100" align="right"><p><? echo  $prerec_qty;  ?></p></td> 
            <td width="100" align="right"><p><? echo  $quantity;  ?></p></td>
            <td width="80" align="right"><p><? echo $balanceQty;?></p></td>
            <td width="" align="right"><p><? echo  $unit_of_measurement[1]; ?></p></td>
		</tr>
		<?php
			
	 
	}
	?>
    	<tr>
            <td align="right" colspan="10" >Total</td>
            <td align="right"><? echo number_format($total_qty,2,'.',''); ?></td>
            <td align="right">&nbsp;</td>
            <td align="right">&nbsp;</td>
           
		</tr>
	</table>
    <br>
	 <?
        echo signature_table(233, $data[0], "1140px");
     ?>
	</div>
	</div>
 	 <script type="text/javascript" src="../../js/jquery.js"></script>
     <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	 
<?
exit();
}


?>