<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$trans_Type="2";

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0 ",'id','color_name');
$size_arr=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');

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
	$trans_Type="2";
	// Insert Start Here ----------------------------------------------------------
	if ($operation==0)   
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
				
		if($db_type==0)
		{
			$new_issue_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'EMI' , date("Y",time()), 5, "select id,prefix_no,prefix_no_num from sub_material_mst where company_id=$cbo_company_name and trans_Type='$trans_Type' and entry_form=313 and YEAR(insert_date)=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		}
		else if($db_type==2)
		{
			$new_issue_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'EMI' , date("Y",time()), 5, "select id,prefix_no,prefix_no_num from sub_material_mst where company_id=$cbo_company_name and trans_Type='$trans_Type' and entry_form=313 and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		}

		if(is_duplicate_field( "a.chalan_no", "sub_material_mst a, sub_material_dtls b", "a.sys_no='$new_issue_no[0]' and a.trans_Type='$trans_Type' and a.entry_form=313 and a.chalan_no=$txt_issue_challan" )==1)
		{
			//check_table_status( $_SESSION['menu_id'],0);
			echo "11**0"; 
			disconnect($con); die;			
		}			
		
		$id=return_next_id("id","sub_material_mst",1) ;
		$field_array="id, entry_form, prefix_no, prefix_no_num, sys_no, trans_type, company_id, location_id, party_id, chalan_no, prod_source, issue_to, subcon_date, within_group, embl_job_no, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",'313','".$new_issue_no[1]."','".$new_issue_no[2]."','".$new_issue_no[0]."','".$trans_Type."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_party_name.",".$txt_issue_challan.",1,".$cbo_company_name.",".$txt_issue_date.",".$cbo_within_group.",".$txt_job_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";  
		
		$txt_issue_no=$new_issue_no[0];//change_date_format($data[2], "dd-mm-yyyy", "-",1)
		
		$id1=return_next_id("id","sub_material_dtls",1) ;
		$field_array2="id, mst_id, emb_name_id, quantity, uom, job_dtls_id, job_break_id, buyer_po_id, rec_challan, inserted_by, insert_date, status_active, is_deleted";
		$data_array2="";  $add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{
			$ordernoid			= "ordernoid_".$i; 
			$breakdownid		= "breakdownid_".$i;
			$txtbuyerPoId		= "txtbuyerPoId_".$i;
			$hidrecsyschallan	= "hidrecsyschallan_".$i;
			$cboProcessName		= "cboProcessName_".$i;
			$cbouom				= "cbouom_".$i;
			$txtissueqty		= "txtissueqty_".$i;
			$updatedtlsid		= "updatedtlsid_".$i;
			
			if ($add_commaa!=0) $data_array2 .=",";
			 
			$data_array2.="(".$id1.",'".$id."',".$$cboProcessName.",".$$txtissueqty.",".$$cbouom.",".$$ordernoid.",".$$breakdownid.",".$$txtbuyerPoId.",".$$hidrecsyschallan.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			 
			$id1=$id1+1; $add_commaa++;
		}
		$flag=1;
		//echo "INSERT INTO sub_material_mst (".$field_array.") VALUES ".$data_array; disconnect($con); die;
		$rID=sql_insert("sub_material_mst",$field_array,$data_array,0);
		if($flag==1 && $rID==1) $flag=1; else $flag=0;
		//echo "10**INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; disconnect($con); die;
		$rID2=sql_insert("sub_material_dtls",$field_array2,$data_array2,1);	
		if($flag==1 && $rID2==1) $flag=1; else $flag=0;
		//echo "10**".$rID."**".$rID2	; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
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
		
		/*$prod_number=return_field_value( "sys_no", "subcon_embel_production_dtls"," job_no=$txt_job_no and status_active=1 and is_deleted=0");
		if($prod_number){
			echo "emblProduction**".str_replace("'","",$txt_job_no)."**".$prod_number;
			disconnect($con); die;
		}*/
		
		$iss_sql_dtls="Select b.id from sub_material_dtls b, sub_material_mst a where a.id=b.mst_id and a.id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=2";//
		$all_dtls_id_arr=array();
		//echo "10**".$colSize_sql_dtls; disconnect($con); die;
		$nameArray=sql_select( $iss_sql_dtls ); 
		foreach($nameArray as $row)
		{
			$all_dtls_id_arr[]=$row[csf('id')];
		}
		unset($nameArray);

		$field_array="location_id*party_id*chalan_no*subcon_date*embl_job_no*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$cbo_party_name."*".$txt_issue_challan."*".$txt_issue_date."*".$txt_job_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
		$field_array2="id, mst_id, emb_name_id, quantity, uom, job_dtls_id, job_break_id, buyer_po_id, rec_challan, inserted_by, insert_date, status_active, is_deleted";
		
		$field_arr_up="emb_name_id*quantity*uom*job_dtls_id*job_break_id*buyer_po_id*rec_challan*updated_by*update_date";
		
		$id1=return_next_id("id","sub_material_dtls",1);
		$data_array2="";  $add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{
			$ordernoid			= "ordernoid_".$i; 
			$breakdownid		= "breakdownid_".$i;
			$txtbuyerPoId		= "txtbuyerPoId_".$i;
			$hidrecsyschallan	= "hidrecsyschallan_".$i;
			$cboProcessName		= "cboProcessName_".$i;
			$cbouom				= "cbouom_".$i;
			$txtissueqty		= "txtissueqty_".$i;
			$updatedtlsid		= "updatedtlsid_".$i;
			
			if(str_replace("'","",$$updatedtlsid)=="")
			{
				if ($add_commaa!=0) $data_array2 .=",";
			 
				$data_array2.="(".$id1.",".$update_id.",".$$cboProcessName.",".$$txtissueqty.",".$$cbouom.",".$$ordernoid.",".$$breakdownid.",".$$txtbuyerPoId.",".$$hidrecsyschallan.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_arr_iss[]=$id1;
				$id1=$id1+1; $add_commaa++;
			}
			else if(str_replace("'","",$$updatedtlsid)!="")
			{
				$data_arr_up[str_replace("'","",$$updatedtlsid)]=explode("*",("".$$cboProcessName."*".$$txtissueqty."*".$$cbouom."*".$$ordernoid."*".$$breakdownid."*".$$txtbuyerPoId."*".$$hidrecsyschallan."*".$user_id."*'".$pc_date_time."'"));
				$id_arr_iss[]=str_replace("'","",$$updatedtlsid);
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
		
		$field_array_del="status_active*is_deleted*updated_by*update_date";
		$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//print_r ($distance_delete_id);
		
		$distance_delete_id="";
	
		if(implode(',',$id_arr_iss)!="")
		{
			$distance_delete_id=implode(',',array_diff($all_dtls_id_arr,$id_arr_iss));
		}
		else
		{
			$distance_delete_id=implode(',',$all_dtls_id_arr);
		}
		
		if(str_replace("'",'',$distance_delete_id)!="")
		{
			$ex_delete_id=explode(",",$distance_delete_id);
			$rID4=execute_query(bulk_update_sql_statement( "sub_material_dtls", "id", $field_array_del,$data_array_del,$ex_delete_id),1);
			if($rID4==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".$rID."**".$rID2."**".$rID3."**".$flag; die;
		 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);	
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);	
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
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
		 
		$prod_sql = "select sys_no from subcon_embel_production_mst where job_no=$txt_job_no and status_active=1 and is_deleted=0 and entry_form=315"; 
		$prod_sql_res=sql_select($prod_sql);
		if(count($prod_sql_res)>0){
			foreach ($prod_sql_res as $row){
				$prod_nos .=$row[csf("sys_no")].', ';
			}
			echo "emblProduction**".str_replace("'","",$txt_job_no)."**".chop($prod_nos,', ');
			disconnect($con); die;
		}
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$data_array_dtls="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
		$flag=1;
		$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0);  
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo "INSERT INTO sub_material_dtls (".$field_array.") VALUES ".$data_array_dtls; die;

		$rID1=sql_update("sub_material_dtls",$field_array,$data_array_dtls,"mst_id",$update_id,1); 
		if($rID1==1 && $flag==1) $flag=1; else $flag=0; 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$update_id2);
			}
		}
		disconnect($con); die; 
	}
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
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_job').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,party_name,within_group)
		{   //alert(company+'_'+party_name+'_'+within_group);	
			load_drop_down( 'emb_material_issue_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Embl. Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
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
                            <th width="70">Issue ID</th>
                            <th width="80">Challan No</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">Embl. Job No</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'emb_material_issue_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",$within_group, "load_drop_down( 'emb_material_issue_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
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
                                    $search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style", 6=> "IR/IB");
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_year_selection').value, 'create_issue_search_list_view', 'search_div', 'emb_material_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="top" id=""><div id="search_div"></div></td>
                        </tr>
                    </tbody>
                </table>    
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
	//$search_issue_id =$data[4];
	//echo $search_issue_id ; die;
	$search_type =$data[6];
	$within_group =$data[7];
	$search_by=str_replace("'","",$data[8]);
	$search_str=trim(str_replace("'","",$data[9]));
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[10]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[10]";}

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $issue_date = "and a.subcon_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $issue_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $issue_date = "and a.subcon_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $issue_date ="";
	}
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	
	if($within_group==1)
	{
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
				else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
				else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
				else if ($search_by==6) $group_cond=" and c.grouping = '$search_str' ";
			}
			if ($data[4]!='') $issue_id_cond=" and a.prefix_no_num='$data[4]'"; else $issue_id_cond="";
			if ($data[5]!='') $challan_no_cond=" and a.chalan_no='$data[5]'"; else $challan_no_cond="";
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
				
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
				else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
				else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";   
				else if ($search_by==6) $group_cond=" and c.grouping = '$search_str' ";
			}
			if ($data[4]!='') $issue_id_cond=" and a.prefix_no_num like '%$data[4]%'"; else $issue_id_cond="";
			if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]%'"; else $challan_no_cond="";
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
				
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
				else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
				else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
				else if ($search_by==6) $group_cond=" and c.grouping = '$search_str' ";
			}
			if ($data[4]!='') $issue_id_cond=" and a.prefix_no_num like '$data[4]%'"; else $issue_id_cond="";
			if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '$data[5]%'"; else $challan_no_cond="";
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
				
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
				else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
				else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
				else if ($search_by==6) $group_cond=" and c.grouping = '$search_str' ";
			}
			if ($data[4]!='') $issue_id_cond=" and a.prefix_no_num like '%$data[4]'"; else $issue_id_cond="";
			if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]'"; else $challan_no_cond="";
		}
	}
	
	if($within_group==2)
	{
	
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no = '$search_str' ";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref = '$search_str' ";
			}
			if ($data[4]!='') $issue_id_cond=" and a.prefix_no_num='$data[4]'"; else $issue_id_cond="";
			if ($data[5]!='') $challan_no_cond=" and a.chalan_no='$data[5]'"; else $challan_no_cond="";
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
				
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no like '%$search_str%'";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref like '$search_str%'";   
			}
			if ($data[4]!='') $issue_id_cond=" and a.prefix_no_num like '%$data[4]%'"; else $issue_id_cond="";
			if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]%'"; else $challan_no_cond="";
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
				
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no like '$search_str%'";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref like '$search_str%'";   
			}
			if ($data[4]!='') $issue_id_cond=" and a.prefix_no_num like '$data[4]%'"; else $issue_id_cond="";
			if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '$data[5]%'"; else $challan_no_cond="";
			//if ($data[9]!='') $order_no_cond=" and order_no like '$data[9]%'"; else $order_no_cond="";
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
				
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
				else if ($search_by==4) $within_no_cond=" and b.buyer_po_no like '%$search_str'";
				else if ($search_by==5) $within_no_cond=" and b.buyer_style_ref like '%$search_str'";   
			}
			if ($data[4]!='') $issue_id_cond=" and a.prefix_no_num like '%$data[4]'"; else $issue_id_cond="";
			if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]'"; else $challan_no_cond="";
		}	
		
	}
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array("select id, company_name from lib_company",'id','company_name');
	$po_arr=return_library_array( "select id,order_no from subcon_ord_dtls",'id','order_no');
	
	$po_ids=''; $buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id)";
		else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
			
			if ($po_ids=="")
			{
				$po_idsCond="";
				echo "Not Found."; die;
			}
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
		unset($po_sql_res);
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
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.embellishment_job=b.job_no_mst $search_com_cond", "id");
	}
	
	if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";
	
	if ( $spo_ids!="") $orderidsCond=" and b.id in ($spo_ids)"; else $orderidsCond="";
	
			$order_buyer_po_array=array();
			$orderbuyerpo_arr=array(); 
			$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.entry_form='311' $orderidsCond $within_no_cond"; 
			$order_sql_res=sql_select($order_sql);
			foreach ($order_sql_res as $row)
			{
				$order_buyer_po_array[]=$row[csf("id")];
				$orderbuyerpo_arr[$row[csf("id")]]['style']=$row[csf("buyer_style_ref")];
				$orderbuyerpo_arr[$row[csf("id")]]['po']=$row[csf("buyer_po_no")];
			}
	if($within_group==2)
	{
		$order_buyer_po=implode(",",$order_buyer_po_array);
		if ($order_buyer_po=="")
		{
			$order_order_buyer_poCond="";
			echo "Not Found."; die;
		}
		if ($data[4]!='' || $data[5]!='')
		{
			 $order_order_buyer_poCond="";
		}
		else
		{
 			if ($order_buyer_po!="") $order_order_buyer_poCond=" and b.job_dtls_id in ($order_buyer_po)"; else $order_order_buyer_poCond="";
		}
	}
	
	if($within_group==1)
	{
		
		$sql= "select a.id, a.sys_no, a.prefix_no_num, $insert_date_cond as year, a.location_id, a.party_id, a.subcon_date, a.within_group, a.chalan_no, a.remarks, a.embl_job_no, c.grouping, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id from sub_material_mst a, sub_material_dtls b, wo_po_break_down c where a.id=b.mst_id and c.id=b.buyer_po_id and a.trans_type=2 and a.entry_form=313 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $issue_date $company $buyer_cond $withinGroup $issue_id_cond $challan_no_cond $spo_idsCond $po_idsCond $year_cond $order_order_buyer_poCond $group_cond group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.party_id, a.subcon_date, a.within_group, a.chalan_no, a.remarks, a.embl_job_no, c.grouping order by a.id DESC ";
	}else{
		$sql= "select a.id, a.sys_no, a.prefix_no_num, $insert_date_cond as year, a.location_id, a.party_id, a.subcon_date, a.within_group, a.chalan_no, a.remarks, a.embl_job_no, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.entry_form=313 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $issue_date $company $buyer_cond $withinGroup $issue_id_cond $challan_no_cond $spo_idsCond $po_idsCond $year_cond $order_order_buyer_poCond group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.party_id, a.subcon_date, a.within_group, a.chalan_no, a.remarks, a.embl_job_no order by a.id DESC ";
	}
	
//echo $sql; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table">
            <thead>
                <th width="40" >SL</th>
                <th width="70" >Issue No</th>
                <th width="70" >Year</th>
                <th width="120" >Party Name</th>
                <th width="100" >Challan No</th>
                <th width="80" >Issue Date</th>
                <th width="120">Order No</th>
                <th width="100">IR/IB</th>
                <th width="100">Buyer Po</th>
                <th>Buyer Style</th>
            </thead>
     	</table>
     <div style="width:920px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_no='';$odbuyer_po=""; $odbuyer_style="";
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
					if($odbuyer_po=="") $odbuyer_po=$orderbuyerpo_arr[$val]['po']; else $odbuyer_po.=','.$orderbuyerpo_arr[$val]['po'];
					if($odbuyer_style=="") $odbuyer_style=$orderbuyerpo_arr[$val]['style']; else $odbuyer_style.=','.$orderbuyerpo_arr[$val]['style'];
				}
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				
				$buyer_po=""; $buyer_style="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				//$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				//$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				
				if($row[csf("within_group")]==1)
				{
					$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				}
				else
				{
					$buyer_po=implode(",",array_unique(explode(",",$odbuyer_po)));
					$buyer_style=implode(",",array_unique(explode(",",$odbuyer_style)));
				}
				
				if($row[csf("within_group")]==1) $party_name=$comp[$row[csf("party_id")]]; else $party_name=$party_arr[$row[csf("party_id")]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")]."_".$row[csf("embl_job_no")];?>');" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf("prefix_no_num")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="120"><? echo $party_name; ?></td>		
						<td width="100" align="center"><? echo $row[csf("chalan_no")]; ?></td>
						<td width="80"><? echo change_date_format($row[csf("subcon_date")]);  ?></td>	
						<td width="120" style="word-break:break-all"><p><? echo $order_no; ?></p></td>	
                        <td width="100" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                        <td style="word-break:break-all"><? echo $buyer_style; ?></td>
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
	$nameArray=sql_select( "select id, sys_no, company_id, location_id, party_id, subcon_date, chalan_no,within_group, embl_job_no from sub_material_mst where id='$data'" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_issue_no').value 		= '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n"; 
		
		echo "document.getElementById('cbo_within_group').value		= '".$row[csf("within_group")]."';\n"; 
		echo "load_drop_down( 'requires/emb_material_issue_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";
		
		echo "load_drop_down( 'requires/emb_material_issue_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );\n"; 		
		
		echo "document.getElementById('cbo_location_name').value	= '".$row[csf("location_id")]."';\n";  
		echo "document.getElementById('cbo_party_name').value		= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('txt_issue_challan').value	= '".$row[csf("chalan_no")]."';\n"; 
		echo "document.getElementById('txt_issue_date').value 	= '".change_date_format($row[csf("subcon_date")])."';\n";  
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
			load_drop_down( 'emb_material_issue_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			
			$('#cbo_party_name').attr('disabled','disabled');
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Embl. Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
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
                            <th width="150">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Buyer Po</th>
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
                            <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"Embl. Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style", 6=>"IR/IB");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",4,'search_by(this.value)',"","" );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $data[3];?>, 'create_job_search_list_view', 'search_div', 'emb_material_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
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
                <div id="search_div"></div>   
            </form>
        </div>
	</body>           
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
	//$within_group =$data[7];
	
	$within_group =$data[8];
	
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[7]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";}
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $buyer=" and a.party_id='$data[1]'"; else { $buyer=""; echo "PLease Select Party name"; die;}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($within_group==1)
	{
		if($search_type==1)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
				else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
				else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
				else if ($search_by==6) $$group_cond=" and d.grouping = '$search_str' ";
			}
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
				
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
				else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'";  
				else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'"; 
				else if ($search_by==6) $$group_cond=" and d.grouping = '$search_str' ";
			}
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
				
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
				else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";  
				else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'"; 
				else if ($search_by==6) $$group_cond=" and d.grouping = '$search_str' "; 
			}
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
				
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
				else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'"; 
				else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";   
				else if ($search_by==6) $$group_cond=" and d.grouping = '$search_str' ";
			}
		}	
	}
	
	if($within_group==2)
	{
		if($search_type==1)
		{
			if($search_str!="") 
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
				else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
				else if ($search_by==4) $buyer_po_no_cond=" and b.buyer_po_no = '$search_str' ";
				else if ($search_by==5) $buyer_style_ref_cond=" and b.buyer_style_ref = '$search_str' ";
			}
		}
		else if($search_type==4 || $search_type==0)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
				else if ($search_by==4) $buyer_po_no_cond=" and b.buyer_po_no like '%$search_str%'"; 
				else if ($search_by==5) $buyer_style_ref_cond=" and b.buyer_style_ref like '%$search_str%'";   
			}
		}
		else if($search_type==2)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
				else if ($search_by==4) $buyer_po_no_cond=" and b.buyer_po_no like '$search_str%'";
				else if ($search_by==5) $buyer_style_ref_cond=" and b.buyer_style_ref like '$search_str%'";  
			}
		}
		else if($search_type==3)
		{
			if($search_str!="")
			{
				if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
				else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
				if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
				else if ($search_by==4) $buyer_po_no_cond=" and b.buyer_po_no like '%$search_str'";
				else if ($search_by==5) $buyer_style_ref_cond=" and b.buyer_style_ref like '%$search_str'";  
			}
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
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
		if ($po_ids=="")
		{
			$po_idsCond="";
			echo "Not Found."; die;
		}
	}
	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
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
		$color_id_str="group_concat(c.color_id)";
		$buyer_po_id_cond="year(b.buyer_po_id)"; 
		
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(c.color_id,',') within group (order by c.color_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}

	if($within_group==1)
	{
		$sql= "select a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, d.grouping, a.delivery_date, $color_id_str as color_id,$buyer_po_id_cond as buyer_po_id ,b.buyer_po_no,b.buyer_style_ref  
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, wo_po_break_down d
		where a.entry_form=311 and a.embellishment_job=b.job_no_mst and a.id=b.mst_id and b.id=c.mst_id and d.id=b.buyer_po_id and b.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $group_cond  $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond  and b.id=c.mst_id  $year_cond $buyer_po_no_cond  $buyer_style_ref_cond
		group by a.id, d.grouping, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref
		order by a.id DESC";
	}else{
		$sql= "select a.id, a.embellishment_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id,$buyer_po_id_cond as buyer_po_id ,b.buyer_po_no,b.buyer_style_ref  
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=311 and a.embellishment_job=b.job_no_mst and a.id=b.mst_id and b.id=c.mst_id  and b.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0    $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond  and b.id=c.mst_id  $year_cond $buyer_po_no_cond  $buyer_style_ref_cond
		group by a.id, a.embellishment_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.buyer_po_no,b.buyer_style_ref
		order by a.id DESC";
	}

	//echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1025" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">IR/IB</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th>Color</th>
        </thead>
        </table>
        <div style="width:1035px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1025" class="rpt_table" id="tbl_po_list">
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
				$buyer_po_id=array_unique(explode(",",$row[csf('buyer_po_id')]));
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				//echo $buyer_style;
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				
				
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('embellishment_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="60"><? echo $row[csf('grouping')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="120"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100"><?  if ($within_group==1)echo $buyer_po; else echo $row[csf('buyer_po_no')];
					//echo $buyer_po_arr[$row[csf('buyer_po_id')]]['po']; ?></td>
                    <td width="100"><? if ($within_group==1)echo $buyer_style; else echo $row[csf('buyer_style_ref')];//echo $buyer_po_arr[$row[csf('buyer_po_id')]]['style']; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td><? echo $color_name; ?></td>
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

if ($action=="material_issue_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	// print_r ($data); die;
	$jobno=$data[7];


	

	$sql= "SELECT id, entry_form, prefix_no, prefix_no_num, sys_no, trans_type, company_id, location_id, party_id, chalan_no, prod_source, issue_to, subcon_date, within_group, embl_job_no, inserted_by, insert_date from sub_material_mst where id='$data[1]' and status_active =1 and is_deleted=0";

	//echo $sql;// die;
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
        	<td width="130"><strong>Issue ID</strong></td>
            <td width="125px"><strong>: </strong><? echo $dataArray[0][csf('sys_no')]; ?></td>
            <td width="100"><strong></strong></td>
            <td width="110px"><strong></strong></td>
            <td width="130"><strong>Company</strong></td>
            <td><strong>: </strong><? echo $company_library[$dataArray[0][csf('company_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Issue Challan</strong></td>
            <td><strong>: </strong><? echo $dataArray[0][csf('chalan_no')]; ?></td>
            <td><strong></strong></td>
            <td><strong></strong></td>
            <td><strong>Buyer</strong></td>
           	<td ><strong>: </strong><? echo $buyer_arr[$dataArray[0][csf('party_id')]]; ?></td>
        </tr>
		<tr>
		<td width="100"><strong>Issue Date</strong></td>
            <td width="175px"><strong>: </strong><? echo change_date_format($dataArray[0][csf('subcon_date')]); ?></td>
            <td><strong></strong></td>
            <td><strong></strong></td>
			<td><strong>Within Group</strong></td>
            <td><strong>: </strong><? echo $yes_no[$dataArray[0][csf('within_group')]]; ?></td>
        </tr>
         <tr>
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
            <th width="120">Embl. Name</th>
            <th width="120">Embl. Type</th>
            <th width="120">Garments Item</th>
            <th width="120">Material Description</th>
            <th width="120">Body Part</th>
            <th width="80">Color</th>
            <th width="80">GMTS Size</th>
            <th width="80">Order Qty</th>
            <th width="80">Prev. Issu. Qty</th>
            <th width="80">Issue Qty</th>
            <th width="60">UOM</th>
        </thead>
        <tbody style="font-size:11px">
	<?


	$color_arrey=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$size_arrey=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');
	$buyer_po_arr=array();
	
	$po_sql ="SELECT a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	

	$updtls_issue_data_arr=array(); $pre_issue_qty_arr=array();
	
	 $sql_iss="SELECT a.id, a.mst_id, a.quantity, a.emb_name_id, a.material_description, a.uom, a.job_dtls_id, a.job_break_id, a.buyer_po_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=2 and b.entry_form=313 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_iss_res =sql_select($sql_iss);
	
	foreach ($sql_iss_res as $row)
	{
		
			$pre_issue_qty_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']+=$row[csf("quantity")];
			$pre_issue_qty_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['emb_name_id']=$row[csf("emb_name_id")];
			$pre_issue_qty_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['material_description']=$row[csf("material_description")];
			$pre_issue_qty_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['uom']=$row[csf("uom")];

		
	}
	 //print_r($updtls_data_arr);
	// print_r($pre_qty_arr);
	unset($sql_iss_res);
	
	$sql_job="SELECT a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty,b.buyer_po_no, b.buyer_style_ref
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=311 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.embellishment_job='$jobno' and c.qnty>0 order by c.id ASC";

		// echo $sql_job;
	$sql_result =sql_select($sql_job);



	//$k=0; 
	$i=1; $pre_recei_qty=0; $receive_qty=0; $order_Qty=0;
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
		
		$quantity=0; $dtlsup_id=""; $balanceQty=0; $prerec_qty=0; $preissue_qty=0; $orderQty=0; $remarks=''; $qty=0;$uom=0;
		$prerec_qty=$pre_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		$preissue_qty=$pre_issue_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		$emb_name=$pre_issue_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['emb_name_id'];
		// $material_description=$pre_issue_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['material_description'];
		$qty=$pre_issue_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		$uom=$pre_issue_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['uom'];
		
		if($row[csf("order_uom")]==1) { $orderQty=number_format($row[csf("qnty")],4,'.','');}
		if($row[csf("order_uom")]==2) { $orderQty=number_format($row[csf("qnty")]*12,4,'.','');}
	?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td align="center"><? echo $i; ?></td>
			<td align="center"><? echo $row[csf("order_no")]; ?></td>
			<td align="center"><? echo $row[csf("buyer_po_no")]; ?></td>
			<td align="center"><? echo $row[csf("buyer_style_ref")]; ?></td>
			<td align="center"><? echo $emblishment_name_array[$emb_name]; ?></td>
			<td align="center"><? echo $emblishment_embroy_type_arr[$row[csf("embl_type")]]; ?></td>
			<td align="center"><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
            <td align="center"><? echo $row[csf("description")]; ?></td>
            <td align="center"><? echo $body_part[$row[csf("body_part")]]; ?></td>
            <td align="center"><? echo $color_arrey[$row[csf("color_id")]]; ?></td>
			<td align="center"><? echo $size_arrey[$row[csf("size_id")]]; ?></td>
			<td align="right"><? echo number_format($orderQty,2); ?></td>
			<td align="right"><? echo number_format($prerec_qty,2); ?></td>
			<td align="right"><? echo number_format($qty,2); ?></td>
			<td align="center"><? echo $unit_of_measurement[$uom]; ?></td>
		</tr>
		<? 

		$order_Qty+=$orderQty; 
		$pre_recei_qty+=$prerec_qty; 
		$receive_qty+=$qty;

		$i++; 
	} 

	?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="11" align="right">Grand Total :</td>
            <td align="right"><? echo $order_Qty; ?></td>
            <td align="right"><? echo $pre_recei_qty; ?></td>
            <td align="right"><? echo $receive_qty; ?></td>
            <td>&nbsp;</td>
        </tr>
    </tfoot>
    </table>
        <br>
		 <?
            echo signature_table(293, $data[0], "1160px","",40,$inserted_by);
         ?>
	</div>
	</div>
     <script type="text/javascript" src="../../../js/jquery.js"></script>
      
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
	$with_in_group=$exdata[3];
	$color_arrey=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arrey=return_library_array( "select id,size_name from  lib_size where status_active=1 and is_deleted=0",'id','size_name');
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
	
	 $sql_iss="select a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.job_break_id, a.buyer_po_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=2 and b.entry_form=313 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_iss_res =sql_select($sql_iss);
	
	foreach ($sql_iss_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['dtlsid']=$row[csf("id")];
			$updtls_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']=$row[csf("quantity")];
		}
		else
		{
			$pre_qty_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']+=$row[csf("quantity")];
		}
	}
	 //print_r($updtls_data_arr);
	// print_r($pre_qty_arr);
	unset($sql_iss_res);
	
	$rec_data_arr=array();
	$sql_rec="select a.sys_no, b.quantity, b.job_dtls_id, b.job_break_id, b.buyer_po_id from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.embl_job_no='$jobno' and a.trans_type=1 and a.entry_form=312 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rec_res =sql_select($sql_rec);
	foreach ($sql_rec_res as $row)
	{
		$rec_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['sys_challan']=$row[csf("sys_no")];
		$rec_data_arr[$row[csf("job_dtls_id")]][$row[csf("job_break_id")]]['qty']+=$row[csf("quantity")];
	}
	unset($sql_rec_res);
	
	$sql_job="select a.id, a.embellishment_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.embl_type, b.body_part, b.main_process_id, b.order_uom, c.id as breakdown_id, c.description, c.color_id, c.size_id, c.qnty,b.buyer_po_no, b.buyer_style_ref
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
		where a.entry_form=311 and a.embellishment_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.mst_id and c.qnty>0 and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id=c.mst_id and a.embellishment_job='$jobno' order by c.id ASC";
	//echo $sql_job;
	
	$sql_result =sql_select($sql_job);
	$k=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		$k++;
		
		if($row[csf("main_process_id")]==1) $emb_type=$emblishment_print_type;
		else if($row[csf("main_process_id")]==2) $emb_type=$emblishment_embroy_type;
		else if($row[csf("main_process_id")]==3) $emb_type=$emblishment_wash_type;
		else if($row[csf("main_process_id")]==4) $emb_type=$emblishment_spwork_type;
		else if($row[csf("main_process_id")]==5) $emb_type=$emblishment_gmts_type;
		else $emb_type="";
		
		$qty=0; $dtlsup_id=""; $sys_challan=""; $balance_qty=0; $rec_qty=0; $pre_issue_qty=0;
		$rec_qty=$rec_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		$pre_issue_qty=$pre_qty_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		$balance_qty=number_format($rec_qty-$pre_issue_qty,4,'.','');
		if($update_id!=0)
		{
			$qty=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['qty'];
		}
		else $qty=$balance_qty;
		
		if($qty==0) $qty='';
		if($balance_qty==0) $balance_qty=0;
		
		$sys_challan=$rec_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['sys_challan'];
		
		$dtlsup_id=$updtls_data_arr[$row[csf("po_id")]][$row[csf("breakdown_id")]]['dtlsid'];
		
		$order_qnty_pcs=($row[csf("order_uom")]==1) ? number_format($row[csf("qnty")],0,'.','') : number_format($row[csf("qnty")]*12,4,'.','');
		//$order_qnty_pcs=number_format($row[csf("qnty")]*12,4,'.','');
		
		
		if($with_in_group==1)
		{
			$buyer_po=$buyer_po_arr[$row[csf("buyer_po_id")]]['po'];
			$buyer_style=$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
		}
		else
		{
			$buyer_po=$row[csf("buyer_po_no")];
			$buyer_style=$row[csf("buyer_style_ref")];
		}
		
		?>
		 <tr>
            <td><input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("po_id")]; ?>">
                <input type="hidden" name="jobno_<? echo $k; ?>" id="jobno_<? echo $k; ?>" value="<? echo $row[csf("embellishment_job")]; ?>">
                <input type="hidden" name="hidrecsyschallan_<? echo $k; ?>" id="hidrecsyschallan_<? echo $k; ?>" value="<? echo $sys_challan; ?>">
                <input type="hidden" name="updatedtlsid_<? echo $k; ?>" id="updatedtlsid_<? echo $k; ?>" value="<? echo $dtlsup_id; ?>">
                <input type="hidden" name="breakdownid_<? echo $k; ?>" id="breakdownid_<? echo $k; ?>" value="<? echo $row[csf("breakdown_id")]; ?>">
                <input type="text" name="txtorderno_<? echo $k; ?>" id="txtorderno_<? echo $k; ?>" class="text_boxes" style="width:90px" value="<? echo $row[csf("order_no")]; ?>" readonly /> <!--onDblClick="job_search_popup('requires/emb_material_issue_controller.php?action=job_popup','Order Selection Form')" -->
            </td>
            <td><input name="txtbuyerPo_<? echo $k; ?>" id="txtbuyerPo_<? echo $k; ?>" type="text" class="text_boxes" style="width:90px" value="<? echo $buyer_po;//$buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?>" readonly />
                <input name="txtbuyerPoId_<? echo $k; ?>" id="txtbuyerPoId_<? echo $k; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("buyer_po_id")]; ?>" />
            </td>
            <td><input name="txtstyleRef_<? echo $k; ?>" id="txtstyleRef_<? echo $k; ?>" type="text" class="text_boxes" style="width:90px" value="<? echo $buyer_style// $buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>" readonly /></td>
            <td><? echo create_drop_down( "cboProcessName_".$k, 80, $emblishment_name_array,"", 1, "--Select--",$row[csf("main_process_id")],"", 1,"" ); ?></td>
            <td id="reType_<? echo $k; ?>"><? echo create_drop_down( "cboReType_".$k, 80, $emb_type,"", 1, "Select Item", $row[csf("embl_type")], "",1); ?></td>
            <td><? echo create_drop_down( "cboGmtsItem_".$k, 90, $garments_item,"", 1, "-- Select --",$row[csf("gmts_item_id")], "",1,"" ); ?></td>
            <td><? echo create_drop_down( "cboBodyPart_".$k, 90, $body_part,"", 1, "-- Select --",$row[csf("body_part")], "",1,"" ); ?></td>
            <td>
                <input type="text" id="txtmaterialdescription_<? echo $k; ?>" name="txtmaterialdescription_<? echo $k; ?>" class="text_boxes" style="width:115px" value="<? echo $row[csf("description")]; ?>" readonly title="Maximum 200 Character" >
            </td>
            <td><input type="text" id="txtcolor_<? echo $k; ?>" name="txtcolor_<? echo $k; ?>" class="text_boxes" value="<? echo $color_arrey[$row[csf("color_id")]]; ?>" style="width:55px" readonly/></td>
            <td><input type="text" id="txtsize_<? echo $k; ?>" name="txtsize_<? echo $k; ?>" class="text_boxes" style="width:55px" value="<? echo $size_arrey[$row[csf("size_id")]]; ?>" readonly/></td>
            <td><input type="text" id="txt_order_qty_<? echo $k; ?>" name="txt_order_qty_<? echo $k; ?>" class="text_boxes" style="width:55px" value="<? echo $order_qnty_pcs; ?>" readonly/></td>
            <td><input type="text" id="txt_prev_issue_qty_<? echo $k; ?>" name="txt_prev_issue_qty_<? echo $k; ?>" class="text_boxes" style="width:55px" value="<?   echo $pre_issue_qty; ?>" readonly/></td>
            <td><input name="txtissueqty_<? echo $k; ?>" id="txtissueqty_<? echo $k; ?>" class="text_boxes_numeric" type="text" onKeyUp="check_iss_qty_ability(this.value,<? echo $k; ?>); fnc_total_calculate();" value="<? echo $qty; ?>" placeholder="<? echo $balance_qty; ?>" pre_issue_qty="<? echo $pre_issue_qty; ?>" rec_qty="<? echo $rec_qty; ?>" style="width:60px" /></td>
            <td><? echo create_drop_down( "cbouom_".$k,50, $unit_of_measurement,"", 1, "-Select-",1,"", 1,"" );?></td>
        </tr>
	<?	
	}
	exit();
}
?>