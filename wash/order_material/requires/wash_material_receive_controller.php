<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$trans_Type="1";

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/wash_material_receive_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor', 'floor_td');" );	
	exit();	 
}

if ($action=="load_variable_settings")
{
 	//$nameArray= sql_select("select id, company_name, variable_list, variable_dtls from variable_setting_wash where company_name='$company_id' and variable_list=4 order by id");
	
	echo "$('#work_order_material_auto_receive').val(0);\n";
  	$workorder_material_autoreceive=return_field_value("variable_dtls","variable_setting_wash ","company_name=$data and variable_list=4 and status_active=1 and is_deleted=0");
  	echo "$('#work_order_material_auto_receive').val(".$workorder_material_autoreceive.");\n";
	
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
				
		$new_receive_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'WMR' , date("Y",time()), 5, "select id,prefix_no,prefix_no_num from sub_material_mst where company_id=$cbo_company_name and trans_Type='$trans_Type' and entry_form=296 $insert_date_con order by id desc ", "prefix_no", "prefix_no_num" ));

		if(is_duplicate_field( "a.chalan_no", "sub_material_mst a, sub_material_dtls b", "a.sys_no='$new_receive_no[0]' and a.chalan_no=$txt_receive_challan and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0" )==1) //  and b.order_id=$order_no_id and b.color_id=$color_id
		{
			//check_table_status( $_SESSION['menu_id'],0);
			echo "11**0"; 
			disconnect($con); die;			
		}			
		
		
		
		
		
		$max_transaction_date = return_field_value("max(subcon_date) as max_date", "sub_material_mst", "embl_job_no=$txt_job_no and status_active = 1", "max_date");      
		if($max_transaction_date != "")
		{
			$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
			if ($receive_date < $max_transaction_date) 
			{
				echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Job";
				//check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
				die;
			}
		}
		
		
		$id=return_next_id("id","sub_material_mst",1) ;
		$field_array="id, entry_form, prefix_no, prefix_no_num, sys_no, trans_type, company_id, location_id, floor_id, party_id, chalan_no, subcon_date, within_group, embl_job_no,issue_chalan_no, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",'296','".$new_receive_no[1]."','".$new_receive_no[2]."','".$new_receive_no[0]."','".$trans_Type."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_floor_name.",".$cbo_party_name.",".$txt_receive_challan.",".$txt_receive_date.",".$cbo_within_group.",".$txt_job_no.",".$txt_receive_challan.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";  
		//echo "INSERT INTO sub_material_mst (".$field_array.") VALUES ".$data_array; disconnect($con); die;
		
		
		$txt_receive_no=$new_receive_no[0];//change_date_format($data[2], "dd-mm-yyyy", "-",1)
		
		$id1=return_next_id("id","sub_material_dtls",1) ;
		$field_array2="id, mst_id, quantity, uom, job_dtls_id, buyer_po_id, remarks,challan_id,order_id,inserted_by, insert_date, status_active, is_deleted";
		$data_array2="";  $add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{
			$challan_id			= "challan_id_".$i; 
			$jobdtlsid			= "jobdtlsid_".$i; 
			$txtbuyerPoId		= "txtbuyerPoId_".$i;
			$cbouom				= "cbouom_".$i;
			$txtreceiveqty		= "txtreceiveqty_".$i;
			$txtremarks			= "txtremarks_".$i;
			$updatedtlsid		= "updatedtlsid_".$i;
			$txtorderid			= "txtorderid_".$i;
			
			if ($add_commaa!=0) $data_array2 .=",";
			 
			$data_array2.="(".$id1.",'".$id."',".$$txtreceiveqty.",".$$cbouom.",".$$jobdtlsid.",".$$txtbuyerPoId.",".$$txtremarks.",".$$challan_id.",".$$txtorderid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			 
			$id1=$id1+1; $add_commaa++;
		}
		//echo $data_array2; die;
		$flag=1;
		//echo "10**INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; die;
		$rID=sql_insert("sub_material_mst",$field_array,$data_array,0);
		if($flag==1 && $rID==1) $flag=1; else $flag=0;
		$rID2=sql_insert("sub_material_dtls",$field_array2,$data_array2,1);	
		if($flag==1 && $rID2==1) $flag=1; else $flag=0;
			//echo "10**".$rID."**".$rID2."**".$flag	; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
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
		
		
	
		$chk_next_transaction=return_field_value("id","sub_material_mst","trans_type in(2,3,6) and embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and entry_form in (297,372) and id >$update_id ","id");
		if($chk_next_transaction !="")
		{ 
			echo "18**Update not allowed.This item is used in another transaction";disconnect($con); die;
		}
		
	
		$max_transaction_date = return_field_value("max(subcon_date) as max_date", "sub_material_mst", "embl_job_no=$txt_job_no and id <> $update_id and status_active = 1", "max_date");      
		if($max_transaction_date != "")
		{
			$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
			if ($receive_date < $max_transaction_date) 
			{
				echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Job";
				//check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
				die;
			}
		}
		
		/*$iss_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and entry_form=297 and is_deleted=0 and trans_type=2");
		if($iss_number){
			echo "washIssue**".str_replace("'","",$txt_job_no)."**".$iss_number;
			disconnect($con); die;
		}
		
		$receive_return_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and entry_form=372 and is_deleted=0 and trans_type=3");
		if($receive_return_number){
			echo "washreturn**".str_replace("'","",$txt_job_no)."**".$receive_return_number;
			disconnect($con); die;
		}*/
		
		$rec_sql_dtls="Select b.id from sub_material_dtls b, sub_material_mst a where a.id=b.mst_id and a.id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=1";//
		$all_dtls_id_arr=array();
		//echo "10**".$rec_sql_dtls; die;
		$nameArray=sql_select( $rec_sql_dtls ); 
		foreach($nameArray as $row)
		{
			$all_dtls_id_arr[]=$row[csf('id')];
		}
		unset($nameArray);

		$field_array="location_id*floor_id*party_id*chalan_no*subcon_date*embl_job_no*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$cbo_floor_name."*".$cbo_party_name."*".$txt_receive_challan."*".$txt_receive_date."*".$txt_job_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
		$field_array2="id, mst_id, quantity, uom, job_dtls_id, buyer_po_id, remarks, inserted_by, insert_date, status_active, is_deleted";
		
		$field_arr_up="quantity*uom*job_dtls_id*buyer_po_id*remarks*updated_by*update_date";
		
		$id1=return_next_id("id","sub_material_dtls",1);
		$data_array2="";  $add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{
			$jobdtlsid			= "jobdtlsid_".$i; 
			$txtbuyerPoId		= "txtbuyerPoId_".$i;
			$cbouom				= "cbouom_".$i;
			$txtreceiveqty		= "txtreceiveqty_".$i;
			$txtremarks			= "txtremarks_".$i;
			$updatedtlsid		= "updatedtlsid_".$i;
			
			if(str_replace("'","",$$updatedtlsid)=="")
			{
				if ($add_commaa!=0) $data_array2 .=",";
			 
				$data_array2.="(".$id1.",".$update_id.",".$$txtreceiveqty.",".$$cbouom.",".$$jobdtlsid.",".$$txtbuyerPoId.",".$$txtremarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_arr_rec[]=$id1;
				$id1=$id1+1; $add_commaa++;
			}
			else if(str_replace("'","",$$updatedtlsid)!="")
			{
				$data_arr_up[str_replace("'","",$$updatedtlsid)]=explode("*",("".$$txtreceiveqty."*".$$cbouom."*".$$jobdtlsid."*".$$txtbuyerPoId."*".$$txtremarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$id_arr_rec[]=str_replace("'","",$$updatedtlsid);
				$hdn_break_id_arr[]=str_replace("'","",$$updatedtlsid);
			}
		}
		$flag=1;
		$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0); 
		if($rID==1 && $flag==1) $flag=1; else $flag=0;	
		if($data_array2!="")
		{
			//echo "10**INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; die;
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
		//echo "10**".$rID."**".$rID2."**".$rID3."**".$rID4."**".implode(',',$all_dtls_id_arr); die;
		 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);	
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);	
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
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
		/*$iss_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and entry_form=297 and is_deleted=0 and trans_type=2");
		if($iss_number){
			echo "washIssue**".str_replace("'","",$txt_job_no)."**".$iss_number;
			disconnect($con); die;
		}*/
		
			$chk_next_transaction=return_field_value("id","sub_material_mst","trans_type in(2,3,6) and embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and entry_form in (297,372) and id >$update_id ","id");
			if($chk_next_transaction !="")
			{ 
				echo "18**Deleted not allowed.This item is used in another transaction";disconnect($con); die;
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
				echo "2**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
		}
		disconnect($con); die; 
	}
}

if ($action=="receive_popup")
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
			load_drop_down( 'wash_material_receive_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
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
                            <th width="70">Receive ID</th>
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
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'wash_material_receive_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 1, "-- Select --",$within_group, "load_drop_down( 'wash_material_receive_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
							</td>
                            <td id="buyer_td">
								<? 
								echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                            </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:60px" placeholder="Receive ID" />
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

    		show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_receive_search_list_view', 'search_div', 'wash_material_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)');
    		isValidated = false;
    	}
    </script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_receive_search_list_view")
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
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and a.subcon_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $recieve_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $recieve_date = "and a.subcon_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $recieve_date ="";
	}
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
			else if ($search_by==6) $inter_ref=" and b.grouping = '$search_str' ";
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
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'"; 
			else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str%'";   
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
			
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";  
			else if ($search_by==6) $inter_ref=" and b.grouping like '$search_str%'";   
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
			
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'"; 
			else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str'";   
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '%$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]'"; else $challan_no_cond="";
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
	
	
	$order_buyer_po_array=array();
	$buyer_po_arr=array();
	$order_buyer_po='';
	$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form='295' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_com_cond"; 

	$order_sql_res=sql_select($order_sql);
	foreach ($order_sql_res as $row)
	{
		$order_buyer_po_array[]=$row[csf("id")];
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
	}
	//unset($order_sql_res);
	//$order_buyer_po=implode(",",$order_buyer_po_array);
	//echo $order_buyer_po; 
	//if ($order_buyer_po!="") $order_order_buyer_poCond=" and b.job_dtls_id in ($order_buyer_po)"; else $order_order_buyer_poCond="";

	$order_buyer_po = array_unique($order_buyer_po_array);
	$order_order_buyer_poCond=where_con_using_array($order_buyer_po,0,"b.job_dtls_id");
	
	
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
	
	/*$sql= "select a.id, a.sys_no, a.prefix_no_num, $insert_date_cond as year, a.location_id, a.within_group, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.embl_job_no, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.entry_form='296' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond $spo_idsCond $po_idsCond $order_idsCond group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.within_group, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.embl_job_no order by a.id DESC ";*/
	$sql= "select a.id, a.sys_no, a.prefix_no_num, $insert_date_cond as year, a.location_id, a.within_group, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.embl_job_no, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id,sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=1 and a.entry_form='296' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond  $order_order_buyer_poCond  $po_idsCond  group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.within_group, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.embl_job_no order by a.id DESC ";
	//echo $sql; 
	$result = sql_select($sql);
	?>
    <div style="width:970;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="100">Job NO</th>
                 <th width="70">Receive No</th>
                <th width="70">Year</th>
                <th width="100">Party Name</th>
                <th width="100">Challan No</th>
                <th width="80">Receive Date</th>
                <th width="100">Order No</th>
                <th width="100">Buyer PO</th> 
                <th width="100">Buyer Style</th>
                <th>Received Qty</th>
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
						<td width="100"><? echo $row[csf("chalan_no")]; ?></td>
						<td width="80"><? echo change_date_format($row[csf("subcon_date")]);  ?></td>
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
	$nameArray=sql_select( "select id, sys_no, company_id, location_id, floor_id, party_id, subcon_date, chalan_no,within_group, embl_job_no,challan_id, issue_chalan_no from sub_material_mst where id='$data'" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_receive_no').value 		= '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n"; 
		
		echo "document.getElementById('txt_receive_challan').value		= '".$row[csf("issue_chalan_no")]."';\n"; 
		echo "document.getElementById('hid_challan_id').value		= '".$row[csf("challan_id")]."';\n"; 
		echo "$('#txt_receive_challan').attr('disabled','true')".";\n";
		
		echo "document.getElementById('cbo_within_group').value		= '".$row[csf("within_group")]."';\n"; 
		echo "load_drop_down( 'requires/wash_material_receive_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";
		
		echo "load_drop_down( 'requires/wash_material_receive_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );\n"; 		
		
		echo "document.getElementById('cbo_location_name').value	= '".$row[csf("location_id")]."';\n"; 

		echo "load_drop_down( 'requires/wash_material_receive_controller', document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor', 'floor_td');\n";

		echo "document.getElementById('cbo_floor_name').value	= '".$row[csf("floor_id")]."';\n"; 

		echo "document.getElementById('cbo_party_name').value		= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('txt_receive_challan').value	= '".$row[csf("chalan_no")]."';\n"; 
		echo "document.getElementById('txt_receive_date').value 	= '".change_date_format($row[csf("subcon_date")])."';\n";  
		echo "document.getElementById('txt_job_no').value			= '".$row[csf("embl_job_no")]."';\n"; 
	    echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_material_receive',1);\n";
		
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
			else if(val==6) $('#search_by_td').html('IR/IB');
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
									$search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style",6=>"IR/IB");
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

    		show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[3];?>, 'create_job_search_list_view', 'search_div', 'wash_material_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)');
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
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
			else if ($search_by==6) $inter_ref=" and b.grouping = '$search_str' ";
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
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";
			else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str%'";
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
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'"; 
			else if ($search_by==6) $inter_ref=" and b.grouping like '$search_str%'"; 
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
			
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";
			else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str'";
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
	
	
	$po_ids='';  
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id) as id";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) ||($inter_ref!="" && $search_by==6))
		{
			$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond $inter_ref and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
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
		else if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			echo "Not Found"; die;
			//$po_idsCond.=" and b.buyer_po_id in ($ids) ";
		}
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
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	 where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond   and b.id=c.mst_id  
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
            <th width="80">Ord Receive Date</th>
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
                    <td width="100" style="word-break:break-all"><? echo $row[csf('buyer_style_ref')]; //if ($within_group==1)echo $buyer_style; echo $row[csf('buyer_style_ref')];//echo $buyer_style; ?></td>
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

if ($action=="wash_material_receive_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$jobno=''; $update_id=0;
	$update_id=$data[1];
	$jobno=$data[7];
	$sql= "SELECT id, sys_no, company_id, location_id, party_id, subcon_date, chalan_no,within_group, embl_job_no,from_company_name,from_location_name,remarks, inserted_by from sub_material_mst where id='$data[1]' and status_active =1 and is_deleted=0";
	$dataArray=sql_select($sql);
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$inserted_by=$user_library[$dataArray[0][csf("inserted_by")]];

	if ($data[6]==1) {
		$buyer_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	}else{
		$buyer_arr=return_library_array( "select id,buyer_name from  lib_buyer", "id","buyer_name"  );
	}
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$floor_arr = return_library_array("select id, floor_name from  lib_prod_floor","id","floor_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	?>
	<div style="width:1080px;">
    <table width="1060" cellspacing="0" border="0">
        <tr>
            <td colspan="2" rowspan="3">
			<img src="../../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
			</td>
			<td colspan="3" align="center" style="font-size:22px">
            <strong><? echo $company_library[$data[0]]; ?></strong>
            </td>
        </tr>
        <tr class="form_caption">
        	<td colspan="3" align="center" style="font-size:14px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <? echo $result[csf('province')];?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="3" align="center" style="font-size:18px"><strong><u><? echo $data[8]; ?></u></strong></td>
            
        </tr>
        <tr>
        	<td width="130"><strong>Receive ID</strong></td>
            <td width="125px"><strong>: </strong><? echo $dataArray[0][csf('sys_no')]; ?></td>
            <td width="100"><strong></strong></td>
            <td width="110px"><strong></strong></td>
            <td width="130"><strong>Company</strong></td>
            <td><strong>: </strong><? echo $company_library[$dataArray[0][csf('company_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Receive Challan</strong></td>
            <td><strong>: </strong><? echo $dataArray[0][csf('chalan_no')]; ?></td>
            <td><strong></strong></td>
            <td><strong></strong></td>
            <td><strong>Job No</strong></td>
			<td><strong>: </strong><? echo $dataArray[0][csf('embl_job_no')]; ?> </td>
        </tr>
		<tr>
		<td width="100"><strong>Receive Date</strong></td>
            <td width="175px"><strong>: </strong><? echo change_date_format($dataArray[0][csf('subcon_date')]); ?></td>
            <td><strong></strong></td>
            <td><strong></strong></td>
			<td><strong>Within Group</strong></td>
            <td><strong>: </strong><? echo $yes_no[$dataArray[0][csf('within_group')]]; ?></td>
        </tr>
         <tr>
			
           	<td><strong>Buyer</strong></td>
           	<td colspan="3"><strong>: </strong><? echo $buyer_arr[$dataArray[0][csf('party_id')]]; ?></td>
			<td><strong>Remark</strong></td>
           	<td><strong>: </strong><? echo $buyer_arr[$dataArray[0][csf('remarks')]]; ?></td>
       </tr>
    </table>
	<div style="width:100%;">
    <table cellspacing="0" width="1260"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" style="font-size:13px">
            <th width="30">SL</th>
            <th width="100">Order No</th>
            <th width="100">Buyer PO</th>
            <th width="100">Style Ref.</th>
            <th width="120">Garments Item</th>
            <th width="80">Color</th>
            <th width="80">GMTS Size</th>
            <th width="80">Order Qty</th>
            <th width="80">Prev. Rec. Qty</th>
            <th width="80">Receive Qty</th>
            <th width="60">UOM</th>
        </thead>
        <tbody style="font-size:11px">
	<?

	$color_arrey=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$size_arrey=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');
	$buyer_po_arr=array();
	$updtls_issue_data_arr=array(); $pre_issue_qty_arr=array(); $pre_qty_arr=array();
	
	// $sql_iss="SELECT a.id, a.mst_id, a.quantity, a.emb_name_id, a.material_description, a.uom, a.job_dtls_id, a.job_break_id, a.buyer_po_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.entry_form=296 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	//  $sql_iss="select a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id, a.remarks from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";


	 $sql_rec="select a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id, a.remarks from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rec_res =sql_select($sql_rec);
	
	foreach ($sql_rec_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_data_arr[$row[csf("job_dtls_id")]]['qty']=$row[csf("quantity")];
		    $updtls_data_arr[$row[csf("job_dtls_id")]]['uom']=$row[csf("uom")];		

		}
		else
		{
			$pre_qty_arr[$row[csf("job_dtls_id")]]['qty']+=$row[csf("quantity")];
		}
	}


	
	$sql_job="SELECT a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id as size_id, b.order_quantity as qnty, b.buyer_po_no, b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.entry_form=295 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.subcon_job='$jobno'";
		// echo $sql_job;
	$sql_result =sql_select($sql_job);
	$i=1; $pre_recei_qty=0; $receive_qty=0;
    $orderQty=0;$order_Qty=0;$pre_rec_qty_tot=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		//$k++;
		if($row[csf("main_process_id")]==1) $emb_type=$emblishment_print_type;
		else if($row[csf("main_process_id")]==2) $emb_type=$emblishment_embroy_type;
		else if($row[csf("main_process_id")]==3) $emb_type=$emblishment_wash_type;
		else if($row[csf("main_process_id")]==4) $emb_type=$emblishment_spwork_type;
		else if($row[csf("main_process_id")]==5) $emb_type=$emblishment_gmts_type;
		else $emb_type="";
		
		$quantity=0; $dtlsup_id=""; $balanceQty=0; $prerec_qty=0; $preissue_qty=0; $orderQty=0; $remarks='';
		//$orderQty=number_format($row[csf("qnty")]*12,4,'.','');
		$pre_rec_qty=$pre_qty_arr[$row[csf("po_id")]]['qty'];
		
		if($row[csf("order_uom")]==1) { $orderQty=number_format($row[csf("qnty")],4,'.','');}
		if($row[csf("order_uom")]==2) { $orderQty=number_format($row[csf("qnty")]*12,4,'.','');}
		if($quantity==0) $quantity='';
		//if($balanceQty==0) $balanceQty=0;
		$uom=$updtls_data_arr[$row[csf("po_id")]]['uom'];

		if($update_id!=0)
		{
			$quantity=$updtls_data_arr[$row[csf("po_id")]]['qty'];

		}
		else $quantity='';
		if($quantity==0) $quantity='';
		
		$dtlsup_id=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['dtlsid'];
		
		
		if(number_format($quantity,2)>0)
		{
	   ?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td align="center"><? echo $i; ?></td>
			<td align="center"><? echo $row[csf("order_no")]; ?></td>
			<td align="center"><? echo $row[csf("buyer_po_no")]; ?></td>
			<td align="center"><? echo $row[csf("buyer_style_ref")]; ?></td>
			<td align="center"><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
            <td align="center"><? echo $color_arrey[$row[csf("color_id")]]; ?></td>
			<td align="center"><? echo $size_arrey[$row[csf("size_id")]]; ?></td>
			<td align="right"><? echo number_format($orderQty,2); ?></td>
			<td align="right"><? echo number_format($pre_rec_qty,2); ?></td>
			<td align="right"><? echo number_format($quantity,2); ?></td>
			<td align="center"><? echo $unit_of_measurement[1]; ?></td>
		</tr>
		<?
		$order_Qty+=$orderQty; 
		$pre_recei_qty+=$quantity; 
		$pre_rec_qty_tot+=$pre_rec_qty;
		$i++; 
		}
		
	} 
	?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" align="right">Grand Total :</td>
            <td align="right"><? echo number_format($order_Qty,2); ?></td>
            <td align="right"><? echo number_format($pre_rec_qty_tot,2); ?></td>
            <td align="right"><? echo number_format($pre_recei_qty,2); ?></td>
            <td>&nbsp;</td>
        </tr>
    </tfoot>
    </table>
        <br>
		 <?
            echo signature_table(326, $data[0], "1160px","",40,$inserted_by);
         ?>
	</div>
	</div>
     <script type="text/javascript" src="../../../js/jquery.js"></script>
      
	<?
	exit();
}

if ($action=="issue_challan_popup")
{
	 echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	?>
	<script>
	
	 var selected_id = new Array(); var jobNoArr = new Array;
	function toggle( x, origColor ) 
	{
		var newColor = 'yellow';
		if ( x.style ) {
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	/*function check_all_data() 
	{
		$("#tbl_po_list tr").each(function() 
		{
			var valTP=$(this).attr("id");
			//if(valTP==1) return;
			//alert(valTP);
			$("#"+valTP).click();
		
		
		});
	}*/


	
		function js_set_value(str)//id,challan_id,challan_no
		{ 
			
				splitArr = str.split('***'); // ["something", "something_else"]
				var job_no=splitArr[1];
				var job_details_id=splitArr[4];// job_details_id
				
				var challan_id=splitArr[2];
					
					
					//alert(job_details_id);
  			 
				 
					if(jobNoArr.length==0)
					{
						jobNoArr.push( challan_id );
					}
					else if( jQuery.inArray( challan_id, jobNoArr )==-1 &&  jobNoArr.length>0)
					{
						alert("Issue No Mixed is Not Allow");
						return true;
					} 
					
					
					toggle( document.getElementById( 'search' + job_details_id ), '#FFFFCC' );
					if( jQuery.inArray( $('#txt_individual_id' + job_details_id).val(), selected_id ) == -1 ) 
					{
						selected_id.push( $('#txt_individual_id' + job_details_id).val() );
					}
					else 
					{
						for( var i = 0; i < selected_id.length; i++ ) 
						{
							if( selected_id[i] == $('#txt_individual_id' + job_details_id).val() ) break;
						}
						selected_id.splice( i, 1 );
						jobNoArr.splice( i, 1 );
					}
					
					
					var po_id = ''; var chalan_id = ''; var selected_ids = '';
					for( var i = 0; i < selected_id.length; i++ ) 
					{
						
						//alert(selected_id[i]);
 						selected_ids = selected_id[i].split('_');
 						po_id += selected_ids[0] + ',';
						chalan_id += selected_ids[1] + ',';
						
					}
					po_id = po_id.substr( 0, po_id.length - 1 );
					chalan_id = chalan_id.substr( 0, chalan_id.length - 1 );
					$('#txt_selected_id').val( po_id );
					$('#txt_selected_chalan_id').val( chalan_id );
 					//alert(po_id);
					document.getElementById('selected_order').value=str;
				
			
			 
		}
	
	
	
		/*function js_set_value(id,challan_id,challan_no)
		{ 
			document.getElementById('selected_order').value=id;
			document.getElementById('hidden_challan_id').value=challan_id;
			document.getElementById('hidden_challan_no').value=challan_no;
			parent.emailwindow.hide();
		}*/
		
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
			else if(val==6) $('#search_by_td').html('IR/IB');
			else if(val==7) $('#search_by_td').html('Challan No');
		}
		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $data[0];?>,<? echo $data[3];?>,<? echo $data[2];?>)">
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="1050" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th> 
                            <th width="100">Sending Location</th>
                            <th width="170" class="must_entry_caption">Issue.Date Range</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Wash Job No</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td><input type="hidden" id="selected_order"> 
                                 <input type="hidden" id="txt_selected_id" name="txt_selected_id" value="" /> 
                                 <input type="hidden" id="txt_selected_chalan_id" name="txt_selected_chalan_id" value="" /> 
								<?   
									echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0],"",1 );
                                ?>
                            </td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                            </td>
                            <td>
									<? 
										$sending_sql="select b.id||'*'||a.id as id, b.location_name||' : '||a.company_name as location_name  from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
										echo create_drop_down( "cbo_sending_location", 100, $sending_sql,"id,location_name", 1, "-- Select Sending Location --", $selected, "" );  
									?>
								</td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" readonly/>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" readonly/>
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style",6=>"IR/IB",7=>"Challan No");
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

    		show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[3];?>+'_'+document.getElementById('cbo_sending_location').value, 'create_wash_to_issue_challan_search_list_view', 'search_div', 'wash_material_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)');
    		isValidated = false;
    	}
    </script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_wash_to_issue_challan_search_list_view")
{	
	$data=explode('_',$data);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	$sending_location =explode('*',$data[8]);;
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $buyer=" and a.party_id='$data[1]'"; else { $buyer=""; echo "PLease Select Party name"; die;}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	
	if ($sending_location[0]!=0) $sending_location=" and d.sending_location='$sending_location[0]'"; else $sending_location="";
	
	//echo $sending_location; die;
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			
			
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
			else if ($search_by==6) $inter_ref=" and b.grouping = '$search_str' ";
			else if ($search_by==7) $challan_cond=" and e.challan_no = '$search_str' ";
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
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";
			else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str%'";
			else if ($search_by==7) $challan_cond=" and e.challan_no like '%$search_str%'";
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
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'"; 
			else if ($search_by==6) $inter_ref=" and b.grouping like '$search_str%'"; 
			else if ($search_by==7) $challan_cond=" and e.challan_no like '$search_str%'";
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
			
			else if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";
			else if ($search_by==6) $inter_ref=" and b.grouping like '%$search_str'";
			else if ($search_by==7) $challan_cond=" and e.challan_no like '%$search_str'";
		}
	}	
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and d.delivery_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and d.delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	
	
	$po_ids='';  
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id) as id";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) ||($inter_ref!="" && $search_by==6))
		{
			$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond $inter_ref and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0", "id");
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
		else if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			echo "Not Found"; die;
			//$po_idsCond.=" and b.buyer_po_id in ($ids) ";
		}
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
	
	 
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
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
	
	
	//echo "select variable_dtls,id from variable_setting_wash where company_name = $data[0] and variable_list =4 and is_deleted = 0 and status_active = 1"; die;
	 $sql =  sql_select("select variable_dtls,id from variable_setting_wash where company_name = $data[0] and variable_list =4 and is_deleted = 0 and status_active = 1");
  	 $variable_Printing_Material_Auto_Receive=$sql[0][csf('variable_dtls')];
	//echo $variable_Printing_Material_Auto_Receive; die;
	if($variable_Printing_Material_Auto_Receive==1) 
	{
	      $sql= "select b.id as job_details_id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id,d.id as challan_id ,e.challan_no,d.sending_location ,d.sending_company,d.sys_number ,d.delivery_date  ,sum(e.production_quantity) as production_quantity
	 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c , pro_gmts_delivery_mst d,pro_garments_production_mst e 
	 where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id  and b.id=c.mst_id  and e.delivery_mst_id=d.id  and  b.buyer_po_id=e.po_break_down_id and e.entry_form=645  and d.entry_form=645  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1  $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond  $challan_cond  $sending_location
	 group by b.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref,d.id,e.challan_no,d.sending_location,d.sending_company,d.sys_number,d.delivery_date
	 order by b.id DESC";
	 
	}
	 /*
	  $sql= "SELECT a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.within_group, b.buyer_po_no, b.buyer_style_ref, a.order_no,f.id as challan_id,f.sys_number as challan_no, a.delivery_date $color_id_str  $buyer_po_id_cond 
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, wo_booking_mst d,pro_garments_production_mst e,pro_gmts_delivery_mst f
		where a.entry_form=204 and a.embellishment_job=b.job_no_mst and c.order_id=d.id   and c.order_id=e.wo_order_id  and  d.id=e.wo_order_id  and  b.buyer_po_id=e.po_break_down_id  and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and b.id=c.mst_id  and e.delivery_mst_id=f.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and f.print_receive_status!=1 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $year_cond and b.id=c.mst_id  
		group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.within_group, b.buyer_po_no, b.buyer_style_ref, a.order_no, a.delivery_date,f.id,f.sys_number
		order by a.id DESC";*/

	 //echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1085" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Issue ID</th>
            <th width="80">Issue Date</th> 
            <th width="60">Issue Qty</th>
            <th width="100">Job No</th> 
            <th width="100">W/O No</th>
            <th width="100">Buyer PO</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th width="80">Color</th> 
            <th>Sending Location</th>
        </thead>
        </table>
        <div style="width:1085px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1065" class="rpt_table" id="tbl_po_list">
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
                
                  <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('job_details_id')].'***'.$row[csf('subcon_job')].'***'.$row[csf('challan_id')].'***'.$row[csf('sys_number')].'***'.$row[csf('job_details_id')]."_".$row[csf('challan_id')].'***'.$location_arr[$row[csf('sending_location')]].$comp[$row[csf('sending_company')]]; ?>')" style="cursor:pointer" id="search<? echo $row[csf('job_details_id')]."_".$row[csf('challan_id')];?>" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><? echo $row[csf('sys_number')]; ?></td> 
                    <td width="80"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                     <td width="60"><? echo $row[csf('production_quantity')]; ?></td>
                    <td width="100"><? echo $row[csf('subcon_job')]; ?></td> 
                    <td width="100" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? if ($within_group==1)echo $buyer_po; else echo $row[csf('buyer_po_no')];//echo $buyer_po;  ?></td>
                    <td width="100" style="word-break:break-all"><? echo $row[csf('buyer_style_ref')]; //if ($within_group==1)echo $buyer_style; echo $row[csf('buyer_style_ref')];//echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?>
                      <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $row[csf('job_details_id')]."_".$row[csf('challan_id')]; ?>" value="<? echo $row[csf('job_details_id')]."_".$row[csf('challan_id')]; ?>"/>
                    
                    </td>	
                    <td style="word-break:break-all" width="80" ><? echo $color_name; ?></td>
                    <td style="word-break:break-all"><? echo $location_arr[$row[csf('sending_location')]].$comp[$row[csf('sending_company')]]; ?></td>
                    
                  
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
    	</div>
    
     <table width="450" cellspacing="0" cellpadding="0" style="border:none" align="center">
    <tr>
        <td align="center" height="30" valign="bottom">
        <div style="width:100%">
        <div style="width:50%; float:left; display:none" align="left">
        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
        </div>
        <div style="width:50%; float:left" align="left">
        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
        </div>
        </div>
        </td>
    </tr>
  </table>
 	<?    
	exit();
}
 


if($action=="load_php_dtls_form")
{
	//echo $data;
	$exdata=explode("**",$data);
	$jobno=''; $update_id=0;
	$update_id=$exdata[0];
	$jobno=$exdata[1];
	$popUpClose=$exdata[2]; //popUpClose=1, FormSubmit=2
	
	$within_group=$exdata[3];
	$company=$exdata[4];
	$challan_id=$exdata[5];
	$multi_job_dtls_id=$exdata[6];
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
	
	$sql_rec_return="SELECT a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id, a.remarks from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rec_return_arr =sql_select($sql_rec_return);
	
	foreach ($sql_rec_return_arr as $row)
	{
		$pre_rec_return_qty_arr[$row[csf("job_dtls_id")]]['qty']+=$row[csf("quantity")];
	}
	
	
	
	$updtls_data_arr=array(); 
	$pre_qty_arr=array();
	
	$sql_rec="SELECT a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id, a.remarks from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $sql_rec; exit();
	$sql_rec_res =sql_select($sql_rec);
	
	foreach ($sql_rec_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_data_arr[$row[csf("job_dtls_id")]]['dtlsid']=$row[csf("id")];
			$updtls_data_arr[$row[csf("job_dtls_id")]]['qty']=$row[csf("quantity")];
			$updtls_data_arr[$row[csf("job_dtls_id")]]['remarks']=$row[csf("remarks")];
			$pre_qty_arr[$row[csf("job_dtls_id")]]['up_qty']=$row[csf("quantity")];
		}
		else
		{
			$pre_qty_arr[$row[csf("job_dtls_id")]]['qty']+=$row[csf("quantity")];
		}
	}
	
	 $sql =  sql_select("select variable_dtls,id from variable_setting_wash where company_name = $company and variable_list =4 and is_deleted = 0 and status_active = 1");
  	 $variable_Printing_Material_Auto_Receive=$sql[0][csf('variable_dtls')];
	 
	 //echo $variable_Printing_Material_Auto_Receive; die; //and b.id in($multi_job_dtls_id)
				
	if($update_id>0)
	{
 		if($challan_id!="")
		{
			
			
			  $sql_job="select a.id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id, b.order_quantity as qnty, b.buyer_po_no, b.buyer_style_ref, sum(e.production_qnty) as production_qnty,d.delivery_mst_id  from subcon_ord_mst a, subcon_ord_dtls b,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f 
	where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id  and  d.entry_form=645 and  e.entry_form=645
 	and d.delivery_mst_id in($challan_id)
	and  b.buyer_po_id=d.po_break_down_id 
	and d.id=e.mst_id 
	and  e.color_size_break_down_id=f.id 
	and  b.buyer_po_id=f.po_break_down_id 
	and  d.po_break_down_id=f.po_break_down_id  
	and  b.gmts_item_id=d.item_number_id  
 	and  b.gmts_color_id=f.color_number_id 
	and  b.gmts_color_id=f.color_number_id   
	and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.subcon_job='$jobno' 
	group by a.id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.gmts_size_id, b.order_quantity, b.buyer_po_no, b.buyer_style_ref ,d.delivery_mst_id
	 order by b.id ASC";
			
			
			
 			/*$sql_job="SELECT a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, sum(e.production_qnty) as qnty,b.buyer_po_no, b.buyer_style_ref,e.color_size_break_down_id
				from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
				where a.entry_form=204 
				and a.embellishment_job=b.job_no_mst 
				and b.job_no_mst=c.job_no_mst 
				and b.id=c.mst_id  
				and a.id=b.mst_id  and  b.order_id=d.wo_order_id  
				and  c.order_id=d.wo_order_id 
				and d.delivery_mst_id=e.delivery_mst_id  
				and  b.buyer_po_id=d.po_break_down_id 
				and  b.gmts_item_id=d.item_number_id 
				and  b.embl_type=d.embel_type
				and  e.color_size_break_down_id=f.id 
				and  b.buyer_po_id=f.po_break_down_id 
				and  d.po_break_down_id=f.po_break_down_id  
				and  c.color_id=f.color_number_id     
				and  c.size_id=f.size_number_id
				and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and d.delivery_mst_id=$challan_id and a.embellishment_job='$jobno' and c.qnty>0 group by a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id, c.description, c.color_id, c.size_id,b.buyer_po_no, b.buyer_style_ref,e.color_size_break_down_id order by c.id ASC";*/
			
			}
		else
		{
	
		 $sql_job="select a.id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id, b.order_quantity as qnty, b.buyer_po_no, b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b 
	where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.subcon_job='$jobno' order by b.id ASC";
		}
	}
	else
	{
 		    if($variable_Printing_Material_Auto_Receive==1)
			{
				
				 
						
				  $sql_job="select a.id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id, b.order_quantity as qnty, b.buyer_po_no, b.buyer_style_ref, sum(e.production_qnty) as production_qnty,d.delivery_mst_id  from subcon_ord_mst a, subcon_ord_dtls b,pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f 
			where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id  and  d.entry_form=645 and  e.entry_form=645
			and b.id in($multi_job_dtls_id)
			and d.delivery_mst_id in($challan_id)
			and  b.buyer_po_id=d.po_break_down_id 
			and d.id=e.mst_id 
			and  e.color_size_break_down_id=f.id 
			and  b.buyer_po_id=f.po_break_down_id 
			and  d.po_break_down_id=f.po_break_down_id  
			and  b.gmts_item_id=d.item_number_id  
			and  b.gmts_color_id=f.color_number_id  
			and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.subcon_job='$jobno'
			group by a.id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id, b.gmts_size_id, b.order_quantity, b.buyer_po_no, b.buyer_style_ref,d.delivery_mst_id
			 order by b.id ASC";
						
	 
			}
			else
			{
				
				$sql_job="select a.id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.order_uom, b.gmts_color_id as color_id, b.gmts_size_id, b.order_quantity as qnty, b.buyer_po_no, b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b 
		where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.subcon_job='$jobno' order by b.id ASC";
				
			}
		
	}		 
	
	
	
	
	
	//echo $sql_job;
	
	$sql_result =sql_select($sql_job); 
	$k=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		$k++;
		
		$currentBalance=0;$quantity=0; $dtlsup_id=""; $balanceQty=0; $prerec_qty=0; $orderQty=0; $remarks='';$pre_rec_return_qty=0;$prerec_balance_qty=0;
		$prerec_qty=$pre_qty_arr[$row[csf("po_id")]]['qty'];
 		$pre_rec_return_qty=$pre_rec_return_qty_arr[$row[csf("po_id")]]['qty'];
		$prerec_balance_qty=$prerec_qty-$pre_rec_return_qty;
		$orderQty=($row[csf("order_uom")]==1) ? number_format($row[csf("qnty")],0,'.','') : number_format($row[csf("qnty")]*12,0,'.','');
		$balanceQty=number_format($orderQty-$prerec_balance_qty,4,'.','');
		
		
			 if($variable_Printing_Material_Auto_Receive==1)
			{
				
					if($update_id!=0)
					{
						if($popUpClose==1){//popupclose
							$quantity = $updtls_data_arr[$row[csf("po_id")]]['qty'];
							//$check_quantity = $row[csf("production_qnty")] - ($updtls_data_arr[$row[csf("po_id")]]['qty'] + $prerec_qty);
							$check_quantity =  $row[csf("production_qnty")] - $prerec_qty;
						}else if($popUpClose==2){ //form submit
							$quantity = $updtls_data_arr[$row[csf("po_id")]]['qty']; //saved qty
							$check_quantity = $row[csf("production_qnty")] -  $prerec_qty;
						}else{
							echo "PopUPClose: $popUpClose"; exit();
						}
						$remarks=$updtls_data_arr[$row[csf("po_id")]]['remarks'];
					}else{
						$quantity=$row[csf("production_qnty")] - $prerec_qty;
						$check_quantity = $row[csf("production_qnty")] - ($updtls_data_arr[$row[csf("po_id")]]['qty'] + $prerec_qty);
					}
					$currentBalance = $row[csf("production_qnty")] - ($updtls_data_arr[$row[csf("po_id")]]['qty'] + $prerec_qty);
					if($quantity==0) $quantity='';
						
			}
			else
			{
				
					if($update_id!=0)
					{
						$quantity=$updtls_data_arr[$row[csf("po_id")]]['qty'];
						$remarks=$updtls_data_arr[$row[csf("po_id")]]['remarks'];
					}
					else $quantity='';
					$check_quantity = $quantity;
					if($quantity==0) $quantity='';
					
			}
		//$balanceQty=number_format($orderQty-$prerec_qty,4,'.','');
		//echo "VAriable: $variable_Printing_Material_Auto_Receive , upID: $update_id"; exit();
		if($balanceQty==0) $balanceQty=0;
		
		$dtlsup_id=$updtls_data_arr[$row[csf("po_id")]]['dtlsid'];
		
		
		
		/* if($variable_Printing_Material_Auto_Receive==1)
		 {
			 $disabled='';
		 }
		 else
		 {
			 $disabled='disabled';
		 }*/
 		?>
		 <tr>
            <td><input type="hidden" name="jobdtlsid_<? echo $k; ?>" id="jobdtlsid_<? echo $k; ?>" value="<? echo $row[csf("po_id")]; ?>">
                <input type="hidden" name="challan_id_<? echo $k; ?>" id="challan_id_<? echo $k; ?>" value="<? echo $row[csf("delivery_mst_id")]; ?>">
                <input type="hidden" name="jobno_<? echo $k; ?>" id="jobno_<? echo $k; ?>" value="<? echo $row[csf("subcon_job")]; ?>">
                <input type="hidden" name="updatedtlsid_<? echo $k; ?>" id="updatedtlsid_<? echo $k; ?>" value="<? echo $dtlsup_id; ?>">
                <input type="hidden" name="breakdownid_<? echo $k; ?>" id="breakdownid_<? echo $k; ?>" value="<? echo $row[csf("breakdown_id")]; ?>"> 
                <input type="hidden" name="txtorderid_<? echo $k; ?>" id="txtorderid_<? echo $k; ?>" value="<? echo $row[csf("order_id")]; ?>">
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
            <td><input name="txtprereturnqty_<? echo $k; ?>" id="txtprereturnqty_<? echo $k; ?>" value="<? echo $pre_rec_return_qty; ?>" class="text_boxes_numeric" type="text" style="width:50px" disabled/></td>
            <td><input name="txtpreqty_<? echo $k; ?>" id="txtpreqty_<? echo $k; ?>" value="<? echo $prerec_qty; ?>" class="text_boxes_numeric" type="text" style="width:50px" disabled/></td>
            <td><input name="txtreceiveqty[]" id="txtreceiveqty_<? echo $k; ?>" class="text_boxes_numeric" type="text" onKeyUp="check_receive_qty_ability(this.value,<? echo $k; ?>); fnc_total_calculate();" value="<? echo $quantity; ?>" placeholder="<? // echo $balanceQty; ?>" pre_rec_qty="<? echo $prerec_qty; ?>" pre_rec_return_qty="<? echo $pre_rec_return_qty; ?>" check_qty="<? echo $check_quantity; ?>"  check_variable_auto="<? echo $variable_Printing_Material_Auto_Receive; ?>"  order_qty="<? echo $orderQty; ?>"  <? echo $disabled ;?> style="width:50px" /></td>
            <td><input name="txtbalance_<? echo $k; ?>" id="txtbalance_<? echo $k; ?>" value="<? echo $currentBalance; ?>" class="text_boxes_numeric" type="text" style="width:50px" disabled/></td>
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
?>