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
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/wash_material_issue_return_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor', 'floor_td');" );	
	exit();	 
}


if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);

	//echo $data[0]."__".$data[1]; die;

	echo create_drop_down( "cbo_floor_name", 150, "select a.id,a.floor_name from lib_prod_floor a, lib_location b where a.location_id=b.id and a.company_id='$data[1]' and a.location_id='$data[0]' and a.production_process in(7,21) and a.is_deleted=0  and a.status_active=1  order by a.floor_name",'id,floor_name', 1, '--- Select Floor ---', 0, ""  );
	exit();
}

if ($action=="load_drop_down_buyer")
{
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

if($action=="load_drop_down_company_supplier")
{
	$data = explode("**",$data);
	if($data[0]==3)
	{
		//echo create_drop_down( "cbo_company_supplier", 140, "select id, supplier_name from lib_supplier where find_in_set(2,party_type) and find_in_set($data[1],tag_company) and status_active=1 and is_deleted=0","id,supplier_name", 1, "--Select Supplier--", 1, "" );
		echo create_drop_down( "cbo_company_supplier", 150, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=2 and  a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "--Select Supplier--", $selected, "" );
	}
	else if($data[0]==1)
	{
		if($data[1]!="")
		{
			 echo create_drop_down( "cbo_company_supplier", 150,"select id,company_name from lib_company where is_deleted=0 and status_active=1 order by company_name","id,company_name", 1, "--Select Supplier--", $data[1], "",1 );	
		}
		else
		{
			 echo create_drop_down( "cbo_company_supplier", 150, $blank_array,"", 1, "--Select Company--", $selected, "",0 );
		}
	}
	else
	{
		echo create_drop_down( "cbo_company_supplier", 150, $blank_array,"", 1, "--Select Supplier--", $selected, "",0 );
	}
	exit();	
}

if ($action=="load_drop_down_buyer_pop")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
	}
	exit();
} 

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$trans_type="4";
	//echo $total_row;die;
	//---------------Check Receive date with Last Transaction date-------------//
	/*$is_update_cond = ($operation == 1 ) ? " and id <> $update_id" : "" ;
    $max_recv_date = return_field_value("max(subcon_date) as max_date", "sub_material_mst", " embl_job_no=$txt_job_no $is_update_cond and status_active = 1", "max_date");    
    if($max_recv_date != "")
    {
    	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_return_date)));
		if ($issue_date < $max_recv_date) 
	    {
            echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
            disconnect($con); die;
		}
    } */ 
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
				
		$new_issue_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'WMIR' , date("Y",time()), 5, "select id,prefix_no,prefix_no_num from sub_material_mst where company_id=$cbo_company_name and trans_type='$trans_type' and entry_form=436 $insert_date_con order by id desc ", "prefix_no", "prefix_no_num" ));

		if(is_duplicate_field( "a.chalan_no", "sub_material_mst a, sub_material_dtls b", "a.sys_no='$new_issue_no[0]' and a.chalan_no=$txt_issue_return_challan and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" )==1)
		{
			echo "11**0";
			disconnect($con);
			die;	
		}			
		
		$id=return_next_id("id","sub_material_mst",1);
		$field_array="id, entry_form, prefix_no, prefix_no_num, sys_no, trans_type, company_id, location_id, floor_id, party_id, chalan_no, subcon_date, within_group, embl_job_no, receive_no, receive_id, remarks, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",'436','".$new_issue_no[1]."','".$new_issue_no[2]."','".$new_issue_no[0]."','".$trans_type."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_floor_name.",".$cbo_party_name.",".$txt_issue_return_challan.",".$txt_issue_return_date.",".$cbo_within_group.",".$txt_job_no.",".$txt_issue_no.",".$issue_id.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//echo "10**INSERT INTO sub_material_mst (".$field_array.") VALUES ".$data_array; disconnect($con); die;		
		
		$txt_issue_return_no=$new_issue_no[0];
		
		$id1=return_next_id("id","sub_material_dtls",1) ;
		$field_array2="id, mst_id, quantity, uom, job_dtls_id, buyer_po_id, receive_dtls_id, receive_id, receive_qty, job_id,rec_challan, inserted_by, insert_date, status_active, is_deleted";
		
		$data_array2="";  $add_commaa=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$ordernoid			= "ordernoid_".$i; 
			$txtbuyerPoId		= "txtbuyerPoId_".$i;
			$cbouom				= "cbouom_".$i;
			$txtissueqty		= "txtissueqty_".$i;
			$updatedtlsid		= "updatedtlsid_".$i;
			$txtissuereturnqty  = "txtissuereturnqty_".$i;
			$issueid     = "issueid_".$i;
			$receiveNo     = "receiveNo_".$i;
			$issuedtlsid = "issuedtlsid_".$i;
			$jobid       = "jobid_".$i;
			
			if ($add_commaa!=0) $data_array2 .=",";
			 
			$data_array2.="(".$id1.",'".$id."',".$$txtissuereturnqty.",".$$cbouom.",".$$ordernoid.",".$$txtbuyerPoId.",".$$issuedtlsid.",".$$issueid.",".$$txtissueqty.",".$$jobid.",".$$receiveNo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			 
			$id1=$id1+1; $add_commaa++;
		}
		//echo "10**INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; disconnect($con); die;
		$flag=1;
		//echo "10**INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; disconnect($con); die;
		$rID=sql_insert("sub_material_mst",$field_array,$data_array,0);
		if($flag==1 && $rID==1) $flag=1; else $flag=0;
		$rID2=sql_insert("sub_material_dtls",$field_array2,$data_array2,1);	
		if($flag==1 && $rID2==1) $flag=1; else $flag=0;
		//echo "10**".$rID."**".$rID2."**".str_replace("'",'',$txtJob_no); disconnect($con); die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_issue_return_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$issue_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_issue_return_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$issue_id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_issue_return_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$issue_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_issue_return_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$issue_id);
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
		
		
			$chk_next_transaction=return_field_value("id","sub_material_mst","trans_Type in (2,3)and embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and id >$update_id ","id");
			if($chk_next_transaction !="")
			{ 
				echo "17**Update not allowed.This item is used in another transaction"; disconnect($con); die;
			}
		
		
		//echo "10**".$update_id; disconnect($con); die; 
		/*$issue_id=return_field_value( "receive_id", "sub_material_dtls","status_active=1 and mst_id=".str_replace("'",'',$update_id)." and  is_deleted=0");
		$issue_return_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and entry_form=297 and id=".$issue_id." and is_deleted=0 and trans_type=2");
		//echo "10**".$issue_return_number; disconnect($con); die; 
		if($issue_return_number){
			echo "washreturn**".str_replace("'","",$txt_job_no)."**".$issue_return_number;
			disconnect($con); die;
		}*/
		
		$sql_issue_dtls="select b.ID from sub_material_dtls b, sub_material_mst a where a.id=b.mst_id and a.id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=4";//
		$all_dtls_id_arr=array();
		//echo "10**".$rec_sql_dtls; disconnect($con); die;
		$nameArray=sql_select( $sql_issue_dtls ); 
		foreach($nameArray as $row)
		{
			$all_dtls_id_arr[]=$row['ID'];
		}
		unset($nameArray);
		
		$field_array="location_id*floor_id*party_id*chalan_no*subcon_date*embl_job_no*remarks*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$cbo_floor_name."*".$cbo_party_name."*".$txt_issue_return_challan."*".$txt_issue_return_date."*".$txt_job_no."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$field_array2="id, mst_id, quantity, uom, job_dtls_id, buyer_po_id,inserted_by, insert_date, status_active, is_deleted";
		$field_array2="id, mst_id, quantity, uom, job_dtls_id, buyer_po_id, receive_dtls_id, receive_id, receive_qty, job_id,rec_challan, inserted_by, insert_date, status_active, is_deleted";
		$field_arr_up="quantity*uom*job_dtls_id*buyer_po_id*rec_challan*updated_by*update_date";
		$id1=return_next_id("id","sub_material_dtls",1);
		$data_array2="";  $add_commaa=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$ordernoid			= "ordernoid_".$i; 
			$txtbuyerPoId		= "txtbuyerPoId_".$i;
			$cbouom				= "cbouom_".$i;
			$txtissueqty		= "txtissueqty_".$i;
			$updatedtlsid		= "updatedtlsid_".$i;
			$txtissuereturnqty  = "txtissuereturnqty_".$i;
			$issueid = "issueid_".$i;
			$receiveNo     = "receiveNo_".$i;
			$issuedtlsid = "issuedtlsid_".$i;
			$jobid = "jobid_".$i;
			
			if(str_replace("'","",$$updatedtlsid)=="")
			{
				if ($add_commaa!=0) $data_array2 .=",";
				$data_array2.="(".$id1.",".$update_id.",".$$txtissuereturnqty.",".$$cbouom.",".$$ordernoid.",".$$txtbuyerPoId.",".$$issuedtlsid.",".$$issueid.",".$$txtissueqty.",".$$jobid.",".$$receiveNo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$id_arr_rec[]=$id1;
				$id1=$id1+1; $add_commaa++;
			}
			else if(str_replace("'","",$$updatedtlsid)!="")
			{
				$data_arr_up[str_replace("'","",$$updatedtlsid)]=explode("*",("".$$txtissuereturnqty."*".$$cbouom."*".$$ordernoid."*".$$txtbuyerPoId."*".$$receiveNo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$id_arr_rec[]=str_replace("'","",$$updatedtlsid);
				$hdn_break_id_arr[]=str_replace("'","",$$updatedtlsid);
			}
		}
		
		
		$flag=1;
		$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0); 
		if($rID==1 && $flag==1) $flag=1; else $flag=0;	
		
		if($data_array2!="")
		{
			//echo "10**INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; disconnect($con); die;
			$rID2=sql_insert("sub_material_dtls",$field_array2,$data_array2,1);
			if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		}
			
		if($data_arr_up!="")
		{
			//echo "10**".bulk_update_sql_statement( "sub_material_dtls", "id", $field_arr_up,$data_arr_up,$hdn_break_id_arr);
			$rID3=execute_query(bulk_update_sql_statement( "sub_material_dtls", "id", $field_arr_up,$data_arr_up,$hdn_break_id_arr),1);
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
			$rID4=execute_query( "update sub_material_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in ($distance_delete_id)",1);
			if($rID4==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".$rID."**".$rID2."**".$rID3."**".$rID4."**".str_replace("'",'',$txt_job_no)."**".implode(',',$all_dtls_id_arr); disconnect($con); die;
		 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_issue_return_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$issue_id);	
			}

			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_issue_return_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$issue_id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_issue_return_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$issue_id);	
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_issue_return_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$issue_id);
			}
		}
		disconnect($con);
	}
	else if ($operation==2)   // delete
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		 //echo $zero_val;
		 
		$prod_number=return_field_value( "sys_no", "subcon_embel_production_dtls"," job_no=$txt_job_no and status_active=1 and is_deleted=0");
		if($prod_number){
			echo "washProduction**".str_replace("'","",$txt_job_no)."**".$prod_number;
			disconnect($con); die;
		}
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$data_array_dtls="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			
		$flag=1;
		$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0);  
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo "INSERT INTO sub_material_dtls (".$field_array.") VALUES ".$data_array_dtls; disconnect($con); die;

		$rID1=sql_update("sub_material_dtls",$field_array,$data_array_dtls,"mst_id",$update_id,1); 
		if($rID1==1 && $flag==1) $flag=1; else $flag=0; 
				
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_issue_return_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2)."**".str_replace("'",'',$issue_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_issue_return_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2)."**".str_replace("'",'',$issue_id);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_issue_return_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2)."**".str_replace("'",'',$issue_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_issue_return_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2)."**".str_replace("'",'',$issue_id);
			}
		}
		disconnect($con); 
	}
}

if ($action=="issue_reurn_popup")
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
			load_drop_down( 'wash_material_issue_return_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer PO');
			else if(val==5) $('#search_by_td').html('Buyer Style');
			else if(val==6) $('#search_by_td').html('IR/IB');
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
                            <th width="70">Return ID</th>
                            <th width="80">Challan No</th>
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
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'wash_material_issue_return_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 1, "-- Select --",$within_group, "load_drop_down( 'wash_material_issue_return_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Return ID" />
                            </td>
                            <td>
                                <input type="text" name="txt_search_challan" id="txt_search_challan" class="text_boxes" style="width:70px" placeholder="Challan" />
                            </td>
                            <td>
								<?
                                    $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style",6=>"IR/IB");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_return_search_list_view', 'search_div', 'wash_material_issue_return_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
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
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_return_search_list_view")
{
	$data=explode('_',$data);
	$company_id = $data[0];
	$party_id   = $data[1];
	$date_from = $data[2];
	$date_to   = $data[3];
	$search_issue   = $data[4];
	$search_challan = $data[5];
	$search_type  = $data[6];
	$within_group = $data[7];
	$search_by = str_replace("'","",$data[8]);
	$search_str= trim(str_replace("'","",$data[9]));
	//echo $company.'system';die; 

	if ($company_id != 0) $company_cond =" and a.company_id=$company_id";
	else { echo "Please Select Company First."; die; }

	$buyer_cond=$within_group_cond='';
	if ($party_id != 0) $buyer_cond=" and a.party_id=$party_id";
	if ($within_group !=0 ) $within_group_cond=" and a.within_group=$within_group";

	$issue_date_cond='';
	if ($date_from != '' &&  $date_to != '')
	{
		if ($db_type == 0) { 
			$issue_date_cond = " and a.subcon_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		} else {
			$issue_date_cond = " and a.subcon_date between '".change_date_format($date_from, '', '', 1)."' and '".change_date_format($date_to, '', '', 1)."'";
		}
	}	
	
	$job_cond=$style_cond=$po_cond='';
	$search_com_cond=$issue_id_cond=$challan_no_cond='';
	if($search_type==1)
	{
		if ($search_str != '')
		{
			if($search_by==1) $search_com_cond=" and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond=" and a.order_no='$search_str'";
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num='$search_str' ";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no='$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref='$search_str' ";
			else if ($search_by==6) $inter_ref=" and b.grouping = '$search_str' ";
		}
		if ($search_issue !='') $issue_id_cond=" and a.prefix_no_num=$search_issue";
		if ($search_challan !='') $challan_no_cond=" and a.chalan_no='$search_challan'";
	}
	else if($search_type==2)
	{
		if($search_str != '')
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";			
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";
			else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str%'";     
		}
		if ($search_issue !='') $issue_id_cond=" and a.prefix_no_num like '$search_issue%'";
		if ($search_challan !='') $challan_no_cond=" and a.chalan_no like '$$search_challan%'";
	}
	else if($search_type==3)
	{
		if($search_str != '')
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";			
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'"; 
			else if ($search_by==6) $inter_ref=" and b.grouping like '$search_str%'";
		}
		if ($search_issue !='') $issue_id_cond=" and a.prefix_no_num like '%$search_issue'";
		if ($search_challan !='') $challan_no_cond=" and a.chalan_no like '%$search_challan'";
	}
	else
	{
		if($search_str != '')
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'"; 
			else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str'";  
		}
		if ($search_issue !='') $issue_id_cond=" and a.prefix_no_num like '%$search_issue%'";
		if ($search_challan !='') $challan_no_cond=" and a.chalan_no like '%$search_challan%'";
	}		
	
	
	$po_ids=''; 
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id) as id";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) || ($inter_ref!="" && $search_by==6))
		{
			$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $inter_ref  and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
		}
		//echo $po_ids;
		if($db_type==2 && $po_ids!="") $po_ids = $po_ids->load();
		if ($po_ids!="")
		{
			$po_ids=explode(",",$po_ids);
			$po_idsCond=""; $poIdsCond="";
			//echo count($po_ids); die;
			if($db_type==2 && count($po_ids)>=999)
			{
				$chunk_arr=array_chunk($po_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($po_idsCond=="")
					{
						$po_idsCond.=" and ( b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" and ( b.id in ( $ids) ";
					}
					else
					{
						$po_idsCond.=" or  b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" or  b.id in ( $ids) ";
					}
				}
				$po_idsCond.=")";
				$poIdsCond.=")";
			}
			else
			{
				$ids=implode(",",$po_ids);
				$po_idsCond.=" and b.buyer_po_id in ($ids) ";
				$poIdsCond.=" and b.id in ($ids) ";
			}
		}
		else if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5)|| ($inter_ref!="" && $search_by==6))
		{
			echo "Not Found"; die;
			//$po_idsCond.=" and b.buyer_po_id in ($ids) ";
		}
		
		 
	}
	
	$buyer_po_arr=array();
	$order_buyer_po_array=array();
	
	$order_buyer_po='';
	$sql_order="SELECT b.ID, b.BUYER_PO_NO, b.BUYER_STYLE_REF, b.PARTY_BUYER_NAME from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form=295 and a.status_active=1 and b.status_active=1 $search_com_cond";
	$sql_order_res=sql_select($sql_order);
	foreach ($sql_order_res as $row)
	{
		$order_buyer_po_array[]=$row['ID'];
		$buyer_po_arr[$row['ID']]['STYLE']=$row['BUYER_STYLE_REF'];
		$buyer_po_arr[$row['ID']]['PO']=$row['BUYER_PO_NO'];
		$buyer_po_arr[$row['ID']]['PARTY_BUYER_NAME']=$row['PARTY_BUYER_NAME'];
	}
	$order_buyer_po=implode(",",$order_buyer_po_array);
	//echo $order_buyer_po; 
	$order_order_buyer_poCond='';
	//if ($order_buyer_po != '') $order_order_buyer_poCond=" and b.job_dtls_id in ($order_buyer_po)";
	if ($order_buyer_po!="") $order_order_buyer_poCond=where_con_using_array($order_buyer_po_array,0,"b.job_dtls_id"); else $order_order_buyer_poCond="";
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array("select id, company_name from lib_company",'id','company_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	
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
	
	
	
	if($search_str == '' && $search_issue =='' && $search_challan =='')
	{
		
		if ($date_from == '' &&  $date_to == '')
		{
			echo "Please Select Date Range."; die;
			
		}else{
			
			$sql= "SELECT a.ID, a.SYS_NO, a.PREFIX_NO_NUM, $insert_date_cond as YEAR, a.LOCATION_ID, a.PARTY_ID, a.subcon_date as ISSUE_RETURN_DATE, a.WITHIN_GROUP, a.CHALAN_NO, a.REMARKS, a.EMBL_JOB_NO, a.RECEIVE_ID, a.RECEIVE_NO, $wo_cond as ORDER_ID, $buyer_po_id_cond as BUYER_PO_ID 
	from sub_material_mst a, sub_material_dtls b 
	where a.id=b.mst_id and a.trans_type=4 and a.entry_form=436 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $issue_date_cond $company_cond $buyer_cond $within_group_cond $issue_id_cond $challan_no_cond $order_order_buyer_poCond  $po_idsCond
	group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.party_id, a.subcon_date, a.within_group, a.chalan_no, a.remarks, a.receive_id, a.receive_no, a.embl_job_no
	order by a.id DESC ";
			
			
			}
	}
	else
	{
		
		$sql= "SELECT a.ID, a.SYS_NO, a.PREFIX_NO_NUM, $insert_date_cond as YEAR, a.LOCATION_ID, a.PARTY_ID, a.subcon_date as ISSUE_RETURN_DATE, a.WITHIN_GROUP, a.CHALAN_NO, a.REMARKS, a.EMBL_JOB_NO, a.RECEIVE_ID, a.RECEIVE_NO, $wo_cond as ORDER_ID, $buyer_po_id_cond as BUYER_PO_ID 
	from sub_material_mst a, sub_material_dtls b 
	where a.id=b.mst_id and a.trans_type=4 and a.entry_form=436 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_cond $buyer_cond $within_group_cond $issue_id_cond $challan_no_cond $order_order_buyer_poCond  $po_idsCond
	group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.party_id, a.subcon_date, a.within_group, a.chalan_no, a.remarks, a.receive_id, a.receive_no, a.embl_job_no
	order by a.id DESC ";
		
	}
	
	
	
	
	 //echo $sql; die;
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="70">Issue Return No</th>
                <th width="70">Year</th>
                <th width="130">Party Name</th>
                <th width="100">Challan No</th>
                <th width="80">Issue Return Date</th>
                <th width="120">Order No</th>
                <th width="100">Buyer PO</th>
                <th>Buyer Style</th>
            </thead>
     	</table>
    	<div style="width:830px; max-height:270px;overflow-y:scroll;" >	 
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="810" class="rpt_table" id="tbl_po_list">
				<?
				$i=1;
	            foreach( $result as $row )
	            {
	                if ($i%2==0) $bgcolor="#E9F3FF";
	                else $bgcolor="#FFFFFF";
	                $order_no='';					
					$order_id=array_unique(explode(',',$row['ORDER_ID']));
					foreach($order_id as $val)
					{
						if($order_no == '') $order_no=$po_arr[$val]; 
						else $order_no.=','.$po_arr[$val];
					}
					$order_no=implode(',',array_unique(explode(',',$order_no)));

				$buyer_po=""; $buyer_style="";$party_buyer="";
				$buyer_po_id=explode(",",$row['ORDER_ID']);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['PO']; else $buyer_po.=','.$buyer_po_arr[$po_id]['PO'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['STYLE']; else $buyer_style.=','.$buyer_po_arr[$po_id]['STYLE'];
					if($party_buyer=="") $party_buyer=$buyer_po_arr[$po_id]['PARTY_BUYER_NAME']; else $party_buyer.=','.$buyer_po_arr[$po_id]['PARTY_BUYER_NAME'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				$party_buyer_name =implode(",",array_unique(explode(",",$party_buyer)));



					//$buyer_po    = $buyer_po_arr[$row['JOB_DTLS_ID']]['PO']; 
					//$buyer_style = $buyer_po_arr[$row['JOB_DTLS_ID']]['STYLE'];
					//$party_buyer_name = $buyer_po_arr[$row[csf('JOB_DTLS_ID')]]['PARTY_BUYER_NAME'];
					
					if($row['WITHIN_GROUP']==1) $party_name=$company_arr[$row['PARTY_ID']]; 
					else $party_name=$party_arr[$row['PARTY_ID']];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row['RECEIVE_ID']."_".$row['EMBL_JOB_NO']."_".$row['ID']."_".$row['RECEIVE_NO']."_".$row['SYS_NO']."_".$party_buyer_name; ?>');">
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row['PREFIX_NO_NUM']; ?></td>
                        <td width="70" align="center"><? echo $row['YEAR']; ?></td>
                        <td width="130"><p><? echo $party_name; ?></p></td>
						<td width="100" align="center"><p><? echo $row['CHALAN_NO']; ?></p></td>
						<td width="80"><p><? echo change_date_format($row['ISSUE_RETURN_DATE']);  ?></p></td>
						<td width="120" style="word-break:break-all"><p><? echo $order_no; ?></p></td>
                        <td width="100" style="word-break:break-all"><p><? echo $buyer_po; ?></p></td>
                        <td style="word-break:break-all"><p><? echo $buyer_style; ?></p></td>
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

if ($action=="issue_popup")
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
			//alert(id);return;
			//$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,party_name,within_group)
		{   //alert(company+'_'+party_name+'_'+within_group);	
			load_drop_down( 'wash_material_issue_return_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer PO');
			else if(val==5) $('#search_by_td').html('Buyer Style');
			else if(val==6) $('#search_by_td').html('IR/IB');
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
                            <th width="70">Issue ID</th>
                            <th width="80">Challan No</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Wash Job No</th>
                            <th width="100" colspan="2" class="must_entry_caption">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'wash_material_issue_return_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 1, "-- Select --",$within_group, "load_drop_down( 'wash_material_issue_return_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Issue ID" />
                            </td>
                            <td>
                                <input type="text" name="txt_search_challan" id="txt_search_challan" class="text_boxes" style="width:70px" placeholder="Challan" />
                            </td>
                            <td>
								<?
                                    $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style",6=>"IR/IB");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From">
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To">
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_issue_search_list_view', 'search_div', 'wash_material_issue_return_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                               <!--  <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px"> -->
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <div id="search_div"></div>   
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_issue_search_list_view")
{
	$data=explode('_',$data);
	$company_id = $data[0];
	$party_id   = $data[1];
	$date_from = $data[2];
	$date_to   = $data[3];
	$search_issue   = $data[4];
	$search_challan = $data[5];
	$search_type  = $data[6];
	$within_group = $data[7];
	$search_by = str_replace("'","",$data[8]);
	$search_str= trim(str_replace("'","",$data[9]));
	//echo $company.'system';die; 

	if ($company_id != 0) $company_cond =" and a.company_id=$company_id";
	else { echo "Please Select Company First."; die; }
	
	
	if ($company_id != 0) $company_cond =" and a.company_id=$company_id";
	else { echo "Please Select Company First."; die; }

	$buyer_cond=$within_group_cond='';
	if ($party_id != 0) $buyer_cond=" and a.party_id=$party_id";
	if ($within_group !=0 ) $within_group_cond=" and a.within_group=$within_group";
	
	
	
	$issue_date_cond='';
	if ($date_from != '' &&  $date_to != '')
	{
		if ($db_type == 0) { 
			$issue_date_cond = " and a.subcon_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		} else {
			$issue_date_cond = " and a.subcon_date between '".change_date_format($date_from, '', '', 1)."' and '".change_date_format($date_to, '', '', 1)."'";
		}
	}	
	
	$job_cond=$style_cond=$po_cond='';
	$search_com_cond=$issue_id_cond=$challan_no_cond='';
	if($search_type==1)
	{
		if ($search_str != '')
		{
			if($search_by==1) $search_com_cond=" and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond=" and a.order_no='$search_str'";
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num='$search_str' ";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no='$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref='$search_str' ";
			else if ($search_by==6) $inter_ref=" and b.grouping = '$search_str' ";
		}
		if ($search_issue !='') $issue_id_cond=" and a.prefix_no_num=$search_issue";
		if ($search_challan !='') $challan_no_cond=" and a.chalan_no='$search_challan'";
	}
	else if($search_type==2)
	{
		if($search_str != '')
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";			
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";
			else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str%'";   
		}
		if ($search_issue !='') $issue_id_cond=" and a.prefix_no_num like '$search_issue%'";
		if ($search_challan !='') $challan_no_cond=" and a.chalan_no like '$$search_challan%'";
	}
	else if($search_type==3)
	{
		if($search_str != '')
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";			
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'"; 
			else if ($search_by==6) $inter_ref=" and b.grouping like '$search_str%'"; 
		}
		if ($search_issue !='') $issue_id_cond=" and a.prefix_no_num like '%$search_issue'";
		if ($search_challan !='') $challan_no_cond=" and a.chalan_no like '%$search_challan'";
	}
	else
	{
		if($search_str != '')
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'"; 
			else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str'";   
		}
		if ($search_issue !='') $issue_id_cond=" and a.prefix_no_num like '%$search_issue%'";
		if ($search_challan !='') $challan_no_cond=" and a.chalan_no like '%$search_challan%'";
	}	
	
	
	$po_ids=''; 
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id) as id";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) || ($inter_ref!="" && $search_by==6))
		{
			$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $inter_ref  and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
		}
		//echo $po_ids;
		if($db_type==2 && $po_ids!="") $po_ids = $po_ids->load();
		if ($po_ids!="")
		{
			$po_ids=explode(",",$po_ids);
			$po_idsCond=""; $poIdsCond="";
			//echo count($po_ids); die;
			if($db_type==2 && count($po_ids)>=999)
			{
				$chunk_arr=array_chunk($po_ids,999);
				foreach($chunk_arr as $val)
				{
					$ids=implode(",",$val);
					if($po_idsCond=="")
					{
						$po_idsCond.=" and ( b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" and ( b.id in ( $ids) ";
					}
					else
					{
						$po_idsCond.=" or  b.buyer_po_id in ( $ids) ";
						$poIdsCond.=" or  b.id in ( $ids) ";
					}
				}
				$po_idsCond.=")";
				$poIdsCond.=")";
			}
			else
			{
				$ids=implode(",",$po_ids);
				$po_idsCond.=" and b.buyer_po_id in ($ids) ";
				$poIdsCond.=" and b.id in ($ids) ";
			}
		}
		else if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5)|| ($inter_ref!="" && $search_by==6))
		{
			echo "Not Found"; die;
			//$po_idsCond.=" and b.buyer_po_id in ($ids) ";
		}
		
		 
	}
	
	$buyer_po_arr=array();
	$order_buyer_po_array=array();
	
	$order_buyer_po='';
	$sql_order="SELECT b.ID, b.BUYER_PO_NO, b.BUYER_STYLE_REF, b.PARTY_BUYER_NAME from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form=295 and a.status_active=1 and b.status_active=1 $search_com_cond";
	$sql_order_res=sql_select($sql_order);
	foreach ($sql_order_res as $row)
	{
		$order_buyer_po_array[]=$row['ID'];
		$buyer_po_arr[$row['ID']]['STYLE']=$row['BUYER_STYLE_REF'];
		$buyer_po_arr[$row['ID']]['PO']=$row['BUYER_PO_NO'];
		$buyer_po_arr[$row['ID']]['PARTY_BUYER_NAME']=$row['PARTY_BUYER_NAME'];
	}
	$order_buyer_po=implode(",",$order_buyer_po_array);
	//echo $order_buyer_po; 
	//$order_order_buyer_poCond='';
	//if ($order_buyer_po != '') $order_order_buyer_poCond=" and b.job_dtls_id in ($order_buyer_po)";
	if ($order_buyer_po!="")
	{
		$order_buyer_po=explode(",",$order_buyer_po);
		$order_order_buyer_poCond=""; 
		//echo count($order_buyer_po); die;
		if($db_type==2 && count($order_buyer_po)>=999)
		{
			$chunk_arr=array_chunk($order_buyer_po,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",",$val);
				if($order_order_buyer_poCond=="")
				{
					$order_order_buyer_poCond.=" and ( b.job_dtls_id in ( $ids) ";
				}
				else
				{
					$order_order_buyer_poCond.=" or  b.job_dtls_id in ( $ids) ";
				}
			}
			$order_order_buyer_poCond.=")";
		}
		else
		{
			$ids=implode(",",$order_buyer_po);
			$order_order_buyer_poCond.=" and b.job_dtls_id in ($ids) ";
		}
		//echo $order_buyer_po."==";
	}
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array("select id, company_name from lib_company",'id','company_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	
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
	
	
	
	//echo $search_str."sd".$search_issue."sdsd".$search_challan; die;
	if($search_str == '' && $search_issue =='' && $search_challan =='')
	{
		
		if ($date_from == '' &&  $date_to == '')
		{
			echo "Please Select Date Range."; die;
			
		}
		else
		{
			
			$sql= "SELECT a.ID, a.SYS_NO, a.PREFIX_NO_NUM, $insert_date_cond as YEAR, a.LOCATION_ID, a.PARTY_ID, a.subcon_date as ISSUE_DATE, a.WITHIN_GROUP, a.CHALAN_NO, a.REMARKS, a.EMBL_JOB_NO, $wo_cond as ORDER_ID, $buyer_po_id_cond as BUYER_PO_ID
	from sub_material_mst a, sub_material_dtls b 
	where a.id=b.mst_id and a.trans_type=2 and a.entry_form=297 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $issue_date_cond $company_cond $buyer_cond $within_group_cond $issue_id_cond $challan_no_cond $order_order_buyer_poCond $po_idsCond 
	group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.party_id, a.subcon_date, a.within_group, a.chalan_no, a.remarks, a.embl_job_no 
	order by a.id DESC ";
			
			
			}
	}
	else
	{
		
		$sql= "SELECT a.ID, a.SYS_NO, a.PREFIX_NO_NUM, $insert_date_cond as YEAR, a.LOCATION_ID, a.PARTY_ID, a.subcon_date as ISSUE_DATE, a.WITHIN_GROUP, a.CHALAN_NO, a.REMARKS, a.EMBL_JOB_NO, $wo_cond as ORDER_ID, $buyer_po_id_cond as BUYER_PO_ID
	from sub_material_mst a, sub_material_dtls b 
	where a.id=b.mst_id and a.trans_type=2 and a.entry_form=297 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_cond $buyer_cond $within_group_cond $issue_id_cond $challan_no_cond $order_order_buyer_poCond  $po_idsCond
	group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.party_id, a.subcon_date, a.within_group, a.chalan_no, a.remarks, a.embl_job_no 
	order by a.id DESC ";
		
	}
	
	
	
	
	
	//echo $sql; die;
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="70">Issue No</th>
                <th width="70">Year</th>
                <th width="130">Party Name</th>
                <th width="100">Challan No</th>
                <th width="80">Issue Date</th>
                <th width="120">Order No</th>
                <th width="100">Buyer PO</th>
                <th>Buyer Style</th>
            </thead>
     	</table>
    	<div style="width:830px; max-height:270px;overflow-y:scroll;" >	 
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="810" class="rpt_table" id="tbl_po_list">
				<?
				$i=1;
	            foreach( $result as $row )
	            {
	                if ($i%2==0) $bgcolor="#E9F3FF";
	                else $bgcolor="#FFFFFF";
	                $order_no='';					
					$order_id=array_unique(explode(',',$row['ORDER_ID']));
					foreach($order_id as $val)
					{
						if($order_no == '') $order_no=$po_arr[$val]; 
						else $order_no.=','.$po_arr[$val];
					}
					$order_no=implode(',',array_unique(explode(',',$order_no)));
					
					

					//$buyer_po    = $buyer_po_arr[$row['JOB_DTLS_ID']]['PO']; 
					//$buyer_style = $buyer_po_arr[$row['JOB_DTLS_ID']]['STYLE'];
					//$party_buyer_name = $buyer_po_arr[$row[csf('JOB_DTLS_ID')]]['PARTY_BUYER_NAME'];
					
					
				$buyer_po=""; $buyer_style="";$party_buyer="";
				$buyer_po_id=explode(",",$row['ORDER_ID']);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['PO']; else $buyer_po.=','.$buyer_po_arr[$po_id]['PO'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['STYLE']; else $buyer_style.=','.$buyer_po_arr[$po_id]['STYLE'];
					if($party_buyer=="") $party_buyer=$buyer_po_arr[$po_id]['PARTY_BUYER_NAME']; else $party_buyer.=','.$buyer_po_arr[$po_id]['PARTY_BUYER_NAME'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				$party_buyer_name =implode(",",array_unique(explode(",",$party_buyer)));
					
					
					
					if($row['WITHIN_GROUP']==1) $party_name=$company_arr[$row['PARTY_ID']]; 
					else $party_name=$party_arr[$row['PARTY_ID']];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row['ID']."_".$row['EMBL_JOB_NO']."_".$row['SYS_NO']."_".$party_buyer_name; ?>');">
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row['PREFIX_NO_NUM']; ?></td>
                        <td width="70" align="center"><? echo $row['YEAR']; ?></td>
                        <td width="130"><p><? echo $party_name; ?></p></td>
						<td width="100" align="center"><p><? echo $row['CHALAN_NO']; ?></p></td>
						<td width="80"><p><? echo change_date_format($row['ISSUE_DATE']);  ?></p></td>
						<td width="120" style="word-break:break-all"><p><? echo $order_no; ?></p></td>
                        <td width="100" style="word-break:break-all"><p><? echo $buyer_po; ?></p></td>
                        <td style="word-break:break-all"><p><? echo $buyer_style; ?></p></td>
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
	$nameArray=sql_select( "select ID, SYS_NO, COMPANY_ID, LOCATION_ID, FLOOR_ID, PARTY_ID, SUBCON_DATE, CHALAN_NO, WITHIN_GROUP, EMBL_JOB_NO, REMARKS from sub_material_mst where id='$data'" );
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_issue_return_no').value 		= '".$row['SYS_NO']."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row['COMPANY_ID']."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n"; 
		
		echo "document.getElementById('cbo_within_group').value		= '".$row['WITHIN_GROUP']."';\n"; 
		echo "load_drop_down( 'requires/wash_material_issue_return_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";
		
		echo "load_drop_down( 'requires/wash_material_issue_return_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );\n"; 		
		
		echo "document.getElementById('cbo_location_name').value	= '".$row['LOCATION_ID']."';\n"; 

		echo "load_drop_down( 'requires/wash_material_receive_controller', document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor', 'floor_td');\n"; 
		echo "document.getElementById('cbo_floor_name').value	= '".$row['FLOOR_ID']."';\n";  
		echo "document.getElementById('cbo_party_name').value		= '".$row['PARTY_ID']."';\n"; 
		echo "document.getElementById('txt_issue_return_challan').value	= '".$row['CHALAN_NO']."';\n"; 
		echo "document.getElementById('txt_issue_return_date').value 	= '".change_date_format($row['SUBCON_DATE'])."';\n";  
		echo "document.getElementById('txt_job_no').value			= '".$row['EMBL_JOB_NO']."';\n"; 
	    echo "document.getElementById('update_id').value            = '".$row['ID']."';\n";
	    echo "document.getElementById('txt_remarks').value          = '".$row['REMARKS']."';\n";
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_material_receive',1);\n";
		
		echo "$('#cbo_within_group').attr('disabled','true')".";\n"; 
		echo "$('#cbo_party_name').attr('disabled','true')".";\n"; 
	}
	exit();
}

if($action=="load_php_dtls_form")
{	
	$ex_data=explode("**",$data);
	$jobno=$issue_id='';
	$update_id='';
	$update_id = $ex_data[3];
	$issue_id = $ex_data[0];
	$jobno = $ex_data[1];
	$color_arr = return_library_array( "select id,color_name from lib_color",'id','color_name');
	$size_arr  = return_library_array( "select id,size_name from  lib_size",'id','size_name');	
	
	
	$sql_rec="SELECT a.ID, a.MST_ID, a.QUANTITY, a.UOM, a.JOB_DTLS_ID, a.BUYER_PO_ID, a.REMARKS, b.TRANS_TYPE,a.REC_CHALLAN,A.RECEIVE_DTLS_ID AS ISSUE_DTLS_ID, A.RECEIVE_ID AS ISSUE_ID from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type in (4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $sql_rec; 
	$sql_rec_res =sql_select($sql_rec);
	$updtls_data_arr=array(); 
	$pre_qty_arr=array();
	foreach ($sql_rec_res as $row)
	{
		if($row['MST_ID']==$update_id)
		{
			$updtls_data_arr[$row['JOB_DTLS_ID']][$row['TRANS_TYPE']]['DTLSID']=$row['ID'];
			//$updtls_data_arr[$row['JOB_DTLS_ID']][$row['TRANS_TYPE']]['QTY']=$row['QUANTITY'];
			//$updtls_data_arr[$row['JOB_DTLS_ID']][$row['REC_CHALLAN']][$row['TRANS_TYPE']]['QTY']=$row['QUANTITY'];
			$updtls_data_arr[$row['JOB_DTLS_ID']][$row['ISSUE_ID']][$row['TRANS_TYPE']]['QTY']=$row['QUANTITY'];
		}
		else
		{
			//$pre_qty_arr[$row['JOB_DTLS_ID']][$row['TRANS_TYPE']]['QTY']+=$row['QUANTITY'];
			//$pre_qty_arr[$row['JOB_DTLS_ID']][$row['REC_CHALLAN']][$row['TRANS_TYPE']]['QTY']+=$row['QUANTITY'];
			$pre_qty_arr[$row['JOB_DTLS_ID']][$row['ISSUE_ID']][$row['TRANS_TYPE']]['QTY']+=$row['QUANTITY'];
		}
	}	
	//echo '<pre>';print_r($pre_qty_arr);
	
	$sql_job="SELECT a.ID, a.SUBCON_JOB, a.JOB_NO_PREFIX_NUM, a.ORDER_ID, a.ORDER_NO, a.DELIVERY_DATE, b.id as PO_ID, b.BUYER_PO_ID, b.GMTS_ITEM_ID, b.ORDER_UOM, b.gmts_color_id as COLOR_ID, b.GMTS_SIZE_ID, c.quantity as QNTY, b.BUYER_PO_NO, b.BUYER_STYLE_REF, c.mst_id as ISSUE_ID, c.id as ISSUE_DTLS_ID,c.REC_CHALLAN 
	from subcon_ord_mst a, subcon_ord_dtls b, sub_material_dtls c, sub_material_mst d 
	where a.subcon_job=b.job_no_mst and a.id=b.mst_id and b.id=c.job_dtls_id and c.mst_id=d.id and a.entry_form=295 and d.trans_type=2 and d.entry_form=297 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.subcon_job='$jobno' and d.id='$issue_id'
	order by b.id ASC";	
	 //echo $sql_job; die;
	$sql_job_res =sql_select($sql_job);
	$k=0;$pre_issue_return_qty=0;
	foreach ($sql_job_res as $row)
	{
		$k++;		
		$quantity=$quantity_balance=0; 
		$dtlsup_id='';
		//$pre_issue_return_qty=$pre_qty_arr[$row['PO_ID']][4]['QTY'];
		//$pre_issue_return_qty=$pre_qty_arr[$row['PO_ID']][$row['REC_CHALLAN']][4]['QTY'];
		$pre_issue_return_qty=$pre_qty_arr[$row['PO_ID']][$row['ISSUE_ID']][4]['QTY'];
		//$total_issue_return_qty=$pre_qty_arr[$row['PO_ID']][4]['QTY']+$updtls_data_arr[$row['PO_ID']][4]['QTY'];
		$total_issue_return_qty=$pre_qty_arr[$row['PO_ID']][$row['ISSUE_ID']][4]['QTY']+$updtls_data_arr[$row['PO_ID']][$row['ISSUE_ID']][4]['QTY'];
		$issue_qty=$row['QNTY'];
		$quantity=$quantity_balance=$issue_qty-$pre_issue_return_qty;

		if($update_id != '') 
		{
			//$quantity=$updtls_data_arr[$row['PO_ID']][4]['QTY'];
			$quantity=$updtls_data_arr[$row['PO_ID']][$row['ISSUE_ID']][4]['QTY'];
			$quantity_balance=$issue_qty-$pre_issue_return_qty;
		}	

		if($quantity==0) $quantity='';
		$dtlsup_id=$updtls_data_arr[$row['PO_ID']][4]['DTLSID'];
		?>
		<tr>
            <td>
            	<input type="hidden" name="issueid_<? echo $k; ?>" id="issueid_<? echo $k; ?>" value="<? echo $row['ISSUE_ID']; ?>">
                <input type="hidden" name="receiveNo_<? echo $k; ?>" id="receiveNo_<? echo $k; ?>" value="<? echo $row['REC_CHALLAN']; ?>">
                <input type="hidden" name="issuedtlsid_<? echo $k; ?>" id="issuedtlsid_<? echo $k; ?>" value="<? echo $row['ISSUE_DTLS_ID']; ?>">
                <input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row['PO_ID']; ?>">
                <input type="hidden" name="jobno_<? echo $k; ?>" id="jobno_<? echo $k; ?>" value="<? echo $row['SUBCON_JOB']; ?>">
                <input type="hidden" name="jobid_<? echo $k; ?>" id="jobid_<? echo $k; ?>" value="<? echo $row['ID']; ?>">           
                <input type="hidden" name="updatedtlsid_<? echo $k; ?>" id="updatedtlsid_<? echo $k; ?>" value="<? echo $dtlsup_id; ?>">
                <input type="text" name="txtorderno_<? echo $k; ?>" id="txtorderno_<? echo $k; ?>" class="text_boxes" style="width:120px" value="<? echo $row['ORDER_NO']; ?>" readonly />
            </td>
            <td>
            	<input name="txtbuyerPo_<? echo $k; ?>" id="txtbuyerPo_<? echo $k; ?>" type="text" class="text_boxes" style="width:120px" value="<? echo $row['BUYER_PO_NO']; ?>" readonly />
                <input name="txtbuyerPoId_<? echo $k; ?>" id="txtbuyerPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:120px" value="<? echo $row['BUYER_PO_ID']; ?>"/>
            </td>
            <td><input name="txtstyleRef_<? echo $k; ?>" id="txtstyleRef_<? echo $k; ?>" type="text" class="text_boxes" style="width:120px" value="<? echo $row['BUYER_STYLE_REF']; ?>" readonly /></td>
            <td><? echo create_drop_down( "cboGmtsItem_".$k, 90, $garments_item,"", 1, "-- Select --",$row['GMTS_ITEM_ID'], "",1,"" ); ?></td>
            <td><input type="text" id="txtcolor_<? echo $k; ?>" name="txtcolor_<? echo $k; ?>" class="text_boxes" value="<? echo $color_arr[$row['COLOR_ID']]; ?>" style="width:90px" readonly/></td>
            <td><input type="text" id="txtsize_<? echo $k; ?>" name="txtsize_<? echo $k; ?>" class="text_boxes" value="<? echo $size_arr[$row['GMTS_SIZE_ID']]; ?>" style="width:90px" readonly/></td> 
            <td><? echo create_drop_down( "cbouom_".$k, 60, $unit_of_measurement,"", 1, "-Select-",1,"", 1,"" );?></td>           
            <td>
            	<input name="txtissueqty_<? echo $k; ?>" id="txtissueqty_<? echo $k; ?>" value="<? echo $issue_qty; ?>" class="text_boxes_numeric" type="text" style="width:80px" disabled/>
            </td>
            <td><input name="txtissuereturnqty_<? echo $k; ?>" id="txtissuereturnqty_<? echo $k; ?>" class="text_boxes_numeric" type="text" onKeyUp="check_issue_qty_ability(this.value,<? echo $k; ?>);fnc_total_calculate();" value="<? echo $quantity; ?>" placeholder="<? echo $quantity_balance; ?>"  style="width:80px" /></td>
            <td><input type="text" name="txtpreissuereturnqty_<? echo $k; ?>" id="txtpreissuereturnqty_<? echo $k; ?>" value="<? echo $pre_issue_return_qty; ?>" class="text_boxes" placeholder="" style="width:80px" readonly disabled/>
            <input type="hidden" name="hiddentxtpreissuereturnqty_<? echo $k; ?>" id="hiddentxtpreissuereturnqty_<? echo $k; ?>" value="<? echo $total_issue_return_qty; ?>"/></td>
        </tr>
	<?	
	}
	exit();
}

if($action=="receive_return_in_wash_print")
{
	extract($_REQUEST);
	list($company_id, $txt_receive_return_no, $update_id, $report_title, $receive_id, $job_no, $cbo_location_name, $cbo_party_name, $txt_return_date, $cbo_within_group, $txt_receive_no, $txt_remarks, $txt_return_challan, $txtBuyerName) = explode("*", $data);

	$company_library= return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library  = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$buyer_address  = return_library_array("select id, address_1 from lib_buyer", "id", "address_1");
	$location_arr   = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$color_arr      = return_library_array("select id, color_name from lib_color", 'id', 'color_name');	
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');



	$updtls_data_arr=array(); 
	$pre_qty_arr=array();
	$sql_rec="select a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id, a.remarks,b.trans_type,a.receive_id,a.rec_challan,b.sys_no from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type in (3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; 
	$sql_rec_res =sql_select($sql_rec);
	foreach ($sql_rec_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("trans_type")]]['dtlsid']=$row[csf("id")];
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("trans_type")]]['qty']=$row[csf("quantity")];
			
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("rec_challan")]][$row[csf("trans_type")]]['qty']+=$row[csf("quantity")];
		}
		else
		{
			$pre_qty_arr[$row[csf("job_dtls_id")]][$row[csf("trans_type")]]['qty']+=$row[csf("quantity")];
			$pre_qty_arr[$row[csf("job_dtls_id")]][$row[csf("rec_challan")]][$row[csf("trans_type")]]['qty']+=$row[csf("quantity")];
		}
	}
	
	
	
$sql_main="select a.id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id, c.quantity as qnty, b.buyer_po_no, b.buyer_style_ref,c.mst_id as receive_id ,c.id as receive_dtls_id,d.sys_no from subcon_ord_mst a, subcon_ord_dtls b,sub_material_dtls c,sub_material_mst d where a.entry_form='295' and d.trans_type=4  and d.entry_form='436' and a.subcon_job=b.job_no_mst and a.id=b.mst_id and b.id=c.job_dtls_id and c.mst_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.subcon_job='$job_no' and d.id='$update_id'  order by b.id ASC";
	$dataArray = sql_select($sql_main);
	
	
	$size_arrey=return_library_array( "select id,size_name from  lib_size",'id','size_name');
	
	
	foreach ($dataArray as $val)
	{
		$order_id    .= $val[csf('order_id')].',';
		$buyer_po_id .= $val[csf('buyer_po_id')].',';
	}
	$order_ids    = chop($order_id,',');
	$buyer_po_ids = chop($buyer_po_id,',');

	
	if ($buyer_po_ids != 0)
	{		
		$po_sql ="SELECT a.style_ref_no, b.id, b.po_number, a.currency_id from wo_po_details_master a, wo_po_break_down b where b.id in('$buyer_po_ids') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$po_sql_res=sql_select($po_sql);
		$buyer_po_arr = array();
		$currency_id = '';
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style_ref_no'] 	= $row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po_number']    	= $row[csf("po_number")];
			$currency_id    = $row[csf("currency_id")];
		}
	}	


	if($db_type==0) $process_type_cond="group_concat(c.process,'*',c.embellishment_type)";
	else if ($db_type==2) $process_type_cond="listagg(c.process||'*'||c.embellishment_type,',') within group (order by c.process||'*'||c.embellishment_type)";

	if ($order_ids != "")
	{
		$sql_process = "select c.mst_id as order_id, $process_type_cond as process_type from subcon_ord_breakdown c where c.mst_id in($order_ids) group by c.mst_id";
		$sql_process_res = sql_select($sql_process);
		$process_wash_type_arr = array();
		foreach ($sql_process_res as $val)
		{
			$process_wash_type_arr[$val[csf('order_id')]]['process_type'] = $val[csf('process_type')];
		}
	}


	if ($cbo_within_group == 1)  // within group yes
	{
		$party_name      = $company_library[$cbo_party_name];
		$party_address   = $location_arr[$dataArray[0][csf('party_location')]];
		$order_no        = $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['po_number'];
		$buyer_style_ref = $buyer_po_arr[$dataArray[0][csf('buyer_po_id')]]['style_ref_no'];
	} 
	else 
	{
		$party_name      = $buyer_library[$cbo_party_name];
		$party_address   = $buyer_address[$dataArray[0][csf('party_id')]];
		$order_no        = $dataArray[0][csf('order_no')];
		$buyer_style_ref = $dataArray[0][csf('buyer_style_ref')];
	}
	?>
	<style type="text/css">
		table,tr,td,th{font-size: 18px;}		
	</style>
	<?php
	?>
	    <div style="width:1200px; font-size:20px">
	        <table width="100%" cellpadding="1" cellspacing="1">
	            <tr>
	                <td width="70" align="right"> 
	                    <img  src='../../<? echo $imge_arr[$company_id]; ?>' height='100%' width='100%'/>
	                </td>
	                <td>
	                    <table width="1000" cellspacing="0" align="center">
	                        <tr>
	                            <td align="center" style="font-size:x-large;"><strong><? echo $company_library[$company_id]; ?></strong></td>
	                        </tr>
	                        
	                        <tr class="form_caption">
	                            <td  align="center"><strong><?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result)
						{
						?>
							 <? echo $result[csf('plot_no')]; ?>
							 <? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
							 <? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
							 <? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
							 <? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
							 <? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
							 <? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
							 <? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
							 Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
							 Website:<? if($result[csf('website')]!="") echo $result[csf('website')];


						}
	                ?> </strong>
	                            </td>  
	                        </tr>
	                        <tr>
	                            <td align="center" style="font-size:x-large;"><strong><? echo $report_title; ?></strong></td>
	                        </tr>
	                    </table>
	                </td>
	            </tr>
	        </table>
	        <table width="100%" cellpadding="1" cellspacing="1">  
	            <tr>
	            	<td width="100"><strong> Return ID</strong></td>
	            	<td width="20"><strong>:</strong></td>
	                <td width="220"><? echo $txt_receive_return_no; ?></td>
	                <td width="120"><strong>Return Date</strong></td>
	                <td width="20"><strong>:</strong></td>
	                <td width="220"><? echo change_date_format($txt_return_date); ?></td> 
	                <td width="140"><strong>Return Challan</strong></td>
	                <td width="20"><strong>:</strong></td>
	                <td width="220"><? echo $txt_return_challan; ?></td>              
	            </tr>
	            <tr>
                
	            	<td width="120"><strong>Within Group</strong></td>
	            	<td width="20"><strong>:</strong></td>
	                <td width="220"><? echo $yes_no[$cbo_within_group]; ?></td>
	            	<td width="80"><strong>Issue ID</strong></td>
	            	<td width="20"><strong>:</strong></td>
	                <td width="220"><? echo $txt_receive_no; ?></td>  
	            	<td width="80"><strong>Party</strong></td>
	            	<td width="20"><strong>:</strong></td>
	            	<td width="220"><? echo $party_name; ?></td>
	                 
	            </tr>
	            <tr>
	            	<td width="100"><strong>Remarks</strong></td>
	            	<td width="20"><strong>:</strong></td>
	            	<td width="220"><? echo $txt_remarks; ?></td>
	                <td width="80"></td>
	            	<td width="20"></td>
	                <td width="220"></td>
	                <td width="120"></td>
	            	<td width="20"></td>
	                <td width="220"></td>
	            </tr>
	            
	        </table>
	        <br>
	        <div style="width:100%;">
	            <table align="right" cellspacing="1" cellpadding="1" width="1200" border="1" rules="all" class="rpt_table">
	                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
	                    <th width="50">SL</th>
	                    <th width="150">Style Ref</th>
	                    <th width="150">Job No</th>
	                    <th width="120">Buyer</th>
	                    <th width="100">Order No</th>
	                    <th width="100">Buyer PO</th>
	                    <th width="120">Garments Item</th>
                        <th width="100">Color</th>
                        <th width="100">Size</th>
                        <th width="60">UOM</th>
                        <th width="120">Return Qty</th>
	                </thead>
					<?
	 				$i=1; $tot_delivery_qty=0;
					foreach ($dataArray as $row) 
					{

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>">
	                        <td width="50"><p><? echo $i; ?></p></td>
                             <td width="150"><p><? echo $row[csf('buyer_style_ref')]; ?></p></td>
	                        <td width="150"><p><? echo $job_no; ?></p></td>
	                        <td width="120"><p><? echo $txtBuyerName;//$garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
	                        <td width="100"><p><? echo  $row[csf('order_no')]; ?></p></td>
	                        <td width="100" align="right"><p><? echo $row[csf('buyer_po_no')]; ?></p></td>
	                        <td width="120" align="right"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
	                        <td width="100"><p><? echo  $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100"><p><? echo $size_arrey[$row[csf('gmts_size_id')]]; ?></p></td>
                            <td width="60"><p><? echo "Pcs";//$unit_of_measurement[$row[csf('order_uom')]];?></p></td>
                            <td width="120" align="right"><p><? echo $row[csf('qnty')]; ?></p></td>
	                    </tr>
						<?
						$i++;
						$tot_delivery_qty += $row[csf('qnty')];
					}
					?>
					<tr bgcolor="#ddd">
						<th colspan="10" align="right"><strong>Total:</strong></th>
						<th align="right"><? echo $tot_delivery_qty; ?></th>
					</tr>
					<tr>
						<td colspan="2" align="right"><strong>Total in word: </strong></td>
						<td colspan="9" align="left"><strong><? echo number_to_words($tot_delivery_qty); ?> Pcs</td>
					</tr>
	            </table>	         
	            <br>
				
				<div>
					<? echo signature_table(217, $company_id, "1200px"); ?>
				</div>
	        </div>
	    </div>
	    <p style="page-break-after:always;"></p>
	<?
		
	exit();
}
?>