<?
include('../../../includes/common.php');
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$trans_Type="1";

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$color_arr=return_library_array( "SELECT id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
//$size_arr=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "SELECT id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();	 
}

if ($action=="load_drop_down_buyer")
{
	//echo $data; die;
	$data=explode('_',$data);//hello
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
		exit();
	}
} 

if ($action=="load_drop_down_buyer_pop")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 120, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
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
				
		if($db_type==0)
		{
			$new_receive_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'AOPMR' , date("Y",time()), 5, "select id,prefix_no,prefix_no_num from sub_material_mst where company_id=$cbo_company_name and trans_Type='$trans_Type' and entry_form=279 and YEAR(insert_date)=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		}
		else if($db_type==2)
		{
			$new_receive_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'AOPMR' , date("Y",time()), 5, "select id,prefix_no,prefix_no_num from sub_material_mst where company_id=$cbo_company_name and trans_Type='$trans_Type' and entry_form=279 and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		}

		if(is_duplicate_field( "a.chalan_no", "sub_material_mst a, sub_material_dtls b", "a.sys_no='$new_receive_no[0]' and a.chalan_no=$txt_receive_challan and b.order_id=$order_no_id and b.material_description=$txt_material_description and b.color_id=$color_id" )==1)
		{
			//check_table_status( $_SESSION['menu_id'],0);
			echo "11**0"; 
			disconnect($con); die;			
		}			
		
		$id=return_next_id("id","sub_material_mst",1) ;
		$field_array="id, entry_form, prefix_no, prefix_no_num, sys_no, trans_type, company_id, location_id, party_id, chalan_no, subcon_date, within_group, embl_job_no, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",'279','".$new_receive_no[1]."','".$new_receive_no[2]."','".$new_receive_no[0]."','".$trans_Type."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_party_name.",".$txt_receive_challan.",".$txt_receive_date.",".$cbo_within_group.",".$txt_job_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";  
		//echo "INSERT INTO sub_material_mst (".$field_array.") VALUES ".$data_array; disconnect($con); die;
		
		$txt_receive_no=$new_receive_no[0];//change_date_format($data[2], "dd-mm-yyyy", "-",1)
		
		$id1=return_next_id("id","sub_material_dtls",1) ;
		$field_array2="id, mst_id, quantity, job_dtls_id, buyer_po_id, remarks,fabric_details_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array2="";  $add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{
			$ordernoid			= "ordernoid_".$i; 
			//$internalRef		= "internalRef_".$i;
			$txtbuyerPoId		= "txtbuyerPoId_".$i;
			$txtreceiveqty		= "txtreceiveqty_".$i;
			$txtremarks			= "txtremarks_".$i;
			$updatedtlsid		= "updatedtlsid_".$i;
			$fabricdtlsid		= "fabricdtlsid_".$i;
			
			if ($add_commaa!=0) $data_array2 .=",";
			 
			$data_array2.="(".$id1.",'".$id."',".$$txtreceiveqty.",".$$ordernoid.",".$$txtbuyerPoId.",".$$txtremarks.",".$$fabricdtlsid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			//.",".$$internalRef
			 
			$id1=$id1+1; $add_commaa++;
		}
		//echo "10**INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; disconnect($con); die;
		$flag=1;
		//echo "10**INSERT INTO sub_material_dtls (".$field_array2.") VALUES ".$data_array2; disconnect($con); die;
		$rID=sql_insert("sub_material_mst",$field_array,$data_array,0);
		if($flag==1 && $rID==1) $flag=1; else $flag=0;
		$rID2=sql_insert("sub_material_dtls",$field_array2,$data_array2,1);	
		if($flag==1 && $rID2==1) $flag=1; else $flag=0;
			//echo "10**".$rID."**".$rID2	; disconnect($con); die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$cbo_within_group);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$cbo_within_group);
			}
		}
		else if($db_type==2)
		{
			if($flag==1) 
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$cbo_within_group);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$cbo_within_group);
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
		
		/*$iss_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=2");
		if($iss_number){
			echo "emblIssue**".str_replace("'","",$txt_job_no)."**".$iss_number;
			disconnect($con); die;
		}*/
		
		$rec_sql_dtls="SELECT b.id from sub_material_dtls b, sub_material_mst a where a.id=b.mst_id and a.id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trans_type=1";//
		$all_dtls_id_arr=array();
		//echo "10**".$rec_sql_dtls; disconnect($con); die;
		$nameArray=sql_select( $rec_sql_dtls ); 
		foreach($nameArray as $row)
		{
			$all_dtls_id_arr[]=$row[csf('id')];
		}
		unset($nameArray);

		$field_array="location_id*party_id*chalan_no*subcon_date*embl_job_no*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$cbo_party_name."*".$txt_receive_challan."*".$txt_receive_date."*".$txt_job_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		
		$field_array2="id, mst_id, quantity, job_dtls_id, buyer_po_id, remarks,fabric_details_id, inserted_by, insert_date, status_active, is_deleted";
		
		$field_arr_up="quantity*job_dtls_id*buyer_po_id*remarks*fabric_details_id*updated_by*update_date";
		
		$id1=return_next_id("id","sub_material_dtls",1);
		$data_array2="";  $add_commaa=0;
		for($i=1; $i<=$total_row; $i++)
		{
			$ordernoid			= "ordernoid_".$i; 
			//$internalRef		= "internalRef_".$i; 
			$txtbuyerPoId		= "txtbuyerPoId_".$i;
			$txtreceiveqty		= "txtreceiveqty_".$i;
			$txtremarks			= "txtremarks_".$i;
			$updatedtlsid		= "updatedtlsid_".$i;
			$fabricdtlsid		= "fabricdtlsid_".$i;
			
			if(str_replace("'","",$$updatedtlsid)=="")
			{
				if ($add_commaa!=0) $data_array2 .=",";
			 
				$data_array2.="(".$id1.",".$update_id.",".$$txtreceiveqty.",".$$ordernoid.",".$$txtbuyerPoId.",".$$txtremarks.",".$$fabricdtlsid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//.",".$$internalRef
				$id_arr_rec[]=$id1;
				$id1=$id1+1; $add_commaa++;
			}
			else if(str_replace("'","",$$updatedtlsid)!="")
			{
				$data_arr_up[str_replace("'","",$$updatedtlsid)]=explode("*",("".$$txtreceiveqty."*".$$ordernoid."*".$$txtbuyerPoId."*".$$txtremarks."*".$$fabricdtlsid."*".$user_id."*'".$pc_date_time."'"));
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
			///echo "10**".bulk_update_sql_statement( "sub_material_dtls", "id", $field_arr_up,$data_arr_up,$hdn_break_id_arr);
			$rID3=execute_query(bulk_update_sql_statement( "sub_material_dtls", "id", $field_arr_up,$data_arr_up,$hdn_break_id_arr),1);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		/*$field_array_del="status_active*is_deleted*updated_by*update_date";
		$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//print_r ($distance_delete_id);
		
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
			$ex_delete_id=explode(",",$distance_delete_id);
			$rID4=execute_query(bulk_update_sql_statement( "sub_material_dtls", "id", $field_array_del,$data_array_del,$ex_delete_id),1);
			if($rID4==1 && $flag==1) $flag=1; else $flag=0;
		}*/
		//echo "10**".$rID."**".$rID2."**".$rID3."**".$rID4."**".implode(',',$all_dtls_id_arr); disconnect($con); die;
		 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$cbo_within_group);	
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$cbo_within_group);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$cbo_within_group);	
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_receive_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no)."**".str_replace("'",'',$cbo_within_group);
			}
		}
		disconnect($con);
	}
	else if ($operation==2)   // delete
	{
		$con = connect();
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 //echo $zero_val;
		$iss_number=return_field_value( "sys_no", "sub_material_mst"," embl_job_no=$txt_job_no and status_active=1 and is_deleted=0 and trans_type=2");
		if($iss_number){
			echo "emblIssue**".str_replace("'","",$txt_job_no)."**".$iss_number;
			disconnect($con); die;
		}
		/*if ( $zero_val==1 )
		{*/
			$field_array="status_active*is_deleted*updated_by*update_date";
			$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			$data_array_dtls="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			
			/*if (str_replace("'",'',$cbo_status)==1)
			{
				$rID=sql_update("sub_material_dtls",$field_array,$data_array_dtls,"id",$update_id2,1); //disconnect($con); die;
			}
			else
			{*/
			$flag=1;
				$rID=sql_update("sub_material_mst",$field_array,$data_array,"id",$update_id,0); 
				if($rID==1 && $flag==1) $flag=1; else $flag=0; 
				//echo "INSERT INTO sub_material_dtls (".$field_array.") VALUES ".$data_array_dtls; disconnect($con); die;

				$rID1=sql_update("sub_material_dtls",$field_array,$data_array_dtls,"mst_id",$update_id,1);
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;  
			//}
		/*}
		else
		{
			$rID=0;
		}*/
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
		disconnect($con); 
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
			load_drop_down( 'aop_material_receive_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('AOP Job No');
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
                            <th colspan="11"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>                	 
                            <th width="140">Company Name</th>
                            <th width="50">Within Group</th>
                            <th width="120">Party Name</th>
                            <th width="70">Receive ID</th>
                            <th width="80">Challan No</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">AOP Job No</th>
                            <th width="100">AOP Ref.</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'aop_material_receive_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",$within_group, "load_drop_down( 'aop_material_receive_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
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
                                    $search_by_arr=array(1=>"AOP Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                                    echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
                                ?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="text" name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From" readonly/>
                            </td>
                            <td>
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To" readonly/>
                            </td> 
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('txt_aop_ref').value, 'create_receive_search_list_view', 'search_div', 'aop_material_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="11" align="center" valign="middle">
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

if($action=="create_receive_search_list_view")
{
	$data=explode('_',$data);
	$search_type =$data[6];
	$within_group =$data[7];
	$search_by=str_replace("'","",$data[8]);
	$search_str=trim(str_replace("'","",$data[9]));
	$aop_ref=trim(str_replace("'","",$data[10]));

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
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";  $aop_cond="";
	if($search_type==1)
	{
		if ($aop_ref!="") $aop_cond=" and c.aop_reference ='$aop_ref'";
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num='$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no='$data[5]'"; else $challan_no_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($aop_ref!="") $aop_cond=" and c.aop_reference like '%$aop_ref%'";
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";   
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '%$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]%'"; else $challan_no_cond="";
	}
	else if($search_type==2)
	{
		if ($aop_ref!="") $aop_cond=" and c.aop_reference like '$aop_ref%'";
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '$data[4]%'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '$data[5]%'"; else $challan_no_cond="";
		if ($data[9]!='') $order_no_cond=" and order_no like '$data[9]%'"; else $order_no_cond="";
	}
	else if($search_type==3)
	{
		if ($aop_ref!="") $aop_cond=" and c.aop_reference like '%$aop_ref'";
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
		}
		if ($data[4]!='') $rec_id_cond=" and a.prefix_no_num like '%$data[4]'"; else $rec_id_cond="";
		if ($data[5]!='') $challan_no_cond=" and a.chalan_no like '%$data[5]'"; else $challan_no_cond="";
	}	
	
	$party_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	$comp=return_library_array("SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	$po_arr=return_library_array( "SELECT id,order_no from subcon_ord_dtls where status_active =1 and is_deleted=0",'id','order_no');
	
	/*//if ($aop_re!="") $aop_cond=" and a.aop_reference like '$aop_re%'";
	if ($aop_ref!="") 
	{
		$subcon_job=return_field_value("subcon_job","subcon_ord_mst"," entry_form=278 and aop_reference='".$aop_ref."'");
		//echo  $collor_name; die;
		//$lc_number=" and e.com_export_lc_id like '%".$brand."%'";
		$subconjob=" and a.embl_job_no='".$subcon_job."'";
	}*/
	//echo $subcon_job;
	
	$po_ids=''; $buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id) as id";
		else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
		//echo "select $id_cond from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
		{
			$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
		}
		//echo $po_ids."==".$po_cond."==";
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
			//echo $po_ids."==";
		}
		else if($po_ids=="" && (($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5)))
		{
			echo "Not Found"; die;
		}
		
		$po_sql ="SELECT a.style_ref_no,a.job_no_prefix_num, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $poIdsCond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
		}
		unset($po_sql_res);
	}
	else{
		$order_sql ="SELECT b.id,b.order_no,b.buyer_style_ref,b.buyer_po_no,a.within_group from subcon_ord_dtls b , subcon_ord_mst a where a.subcon_job=b.job_no_mst and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$order_sql_res=sql_select($order_sql);
		foreach ($order_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['buyer_style_ref']=$row[csf("buyer_style_ref")];
			$buyer_po_arr[$row[csf("id")]]['buyer_po_no']=$row[csf("buyer_po_no")];
			$buyer_po_arr[$row[csf("id")]]['order_no']=$row[csf("order_no")];
		}
		unset($order_sql_res);
		/*$po_sql ="SELECT a.style_ref_no,a.job_no_prefix_num, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $poIdsCond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
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
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond", "id");
	}
	$spo_idsCond="";
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		if ( $spo_ids!="") 
			{
				$spo_idsCond=" and b.job_dtls_id in ($spo_ids)";
			} 
			else
			{
			 	echo "Not Found"; die;
			}
	}
	
	
	if($aop_cond!='')
	{
		$ref_tab= ',subcon_ord_mst c';
		$ref_tab_rel= ' and c.subcon_job=a.embl_job_no and c.entry_form=278 and c.status_active=1';
		$aop_reference= ',c.aop_reference';
	}
	else
	{
		$aop_reference_arr=return_library_array( "SELECT subcon_job,aop_reference from subcon_ord_mst where entry_form=278 and status_active=1 and is_deleted=0",'subcon_job','aop_reference');
	}
	
	$sql= "SELECT a.id, a.sys_no, a.prefix_no_num, $insert_date_cond as year, a.location_id, a.within_group, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.embl_job_no, $wo_cond as order_id, $buyer_po_id_cond as buyer_po_id,a.embl_job_no $aop_reference from sub_material_mst a, sub_material_dtls b $ref_tab where a.id=b.mst_id and a.trans_type=1 and a.entry_form='279' $ref_tab_rel and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $aop_cond  $recieve_date $company $buyer_cond $withinGroup $rec_id_cond $challan_no_cond  $subconjob  $spo_idsCond $po_idsCond group by a.id, a.sys_no, a.prefix_no_num, a.insert_date, a.location_id, a.within_group, a.party_id, a.subcon_date, a.chalan_no, a.remarks, a.embl_job_no,a.embl_job_no $aop_reference order by a.id DESC ";
	//echo $sql; 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table">
            <thead>
                <th width="40" >SL</th>
                <th width="40" >Recv. No</th>
                <th width="40" >Year</th>
                <th width="120" >Party Name</th>
                <th width="100" >Challan No</th>
                <th width="80" >Receive Date</th>
                <th width="120">Order No</th>
                <th width="100">Buyer Job</th>
                <th width="100">Buyer Po</th>
                <th width="100">Buyer Style</th>
                 <th>AOP Reference</th>
            </thead>
     	</table>
     <div style="width:990px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach($result as $row)
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_no='';
				$order_id=array_unique(explode(",",$row[csf("order_id")]));
				/*foreach($order_id as $val)
				{
					if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
				}*/
				
				$buyer_po=""; $buyer_style=""; $buyer_job="";
				if($within_group==1){
					$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
					foreach($buyer_po_id as $po_id)
					{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
						if($buyer_job=="") $buyer_job=$buyer_po_arr[$po_id]['job']; else $buyer_job.=','.$buyer_po_arr[$po_id]['job'];
					}
					foreach($order_id as $val)
					{
						if($order_no=="") $order_no=$po_arr[$val]; else $order_no.=",".$po_arr[$val];
					}
				}
				else{
					foreach($order_id as $val)
					{
						if($order_no=="") $order_no=$buyer_po_arr[$val]['order_no']; else $order_no.=','.$buyer_po_arr[$val]['order_no'];
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$val]['buyer_po_no']; else $buyer_po.=','.$buyer_po_arr[$val]['buyer_po_no'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$val]['buyer_style_ref']; else $buyer_style.=','.$buyer_po_arr[$val]['buyer_style_ref'];
					}
				}
				
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				$buyer_job=implode(",",array_unique(explode(",",$buyer_job)));
				$order_no=implode(",",array_unique(explode(",",$order_no)));
				$party_name="";
				if($row[csf("within_group")]==1) $party_name=$comp[$row[csf("party_id")]]; else $party_name=$party_arr[$row[csf("party_id")]];
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")]."_".$row[csf("embl_job_no")]."_".$row[csf("within_group")];?>');" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="40" align="center"><? echo $row[csf("prefix_no_num")]; ?></td>
                        <td width="40" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="120"><? echo $party_name; ?></td>		
						<td width="100"><? echo $row[csf("chalan_no")]; ?></td>
						<td width="80"><? echo change_date_format($row[csf("subcon_date")]);  ?></td>
                        <td width="120" style="word-break:break-all"><p><? echo $order_no; ?></p></td>	
                        <td width="100" style="word-break:break-all"><? echo $buyer_job; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                       	<td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                        <td style="word-break:break-all"><?
                        if($aop_cond!='')$aop_reff=$row[csf('aop_reference')]; else $aop_reff=$aop_reference_arr[$row[csf('embl_job_no')]];
                        echo $aop_reff; ?></td>
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
	$nameArray=sql_select( "SELECT id, sys_no, company_id, location_id, party_id, subcon_date, chalan_no,within_group, embl_job_no from sub_material_mst where id='$data' and status_active=1 and is_deleted=0" ); 
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_receive_no').value 		= '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n"; 
		
		echo "document.getElementById('cbo_within_group').value		= '".$row[csf("within_group")]."';\n"; 
		echo "load_drop_down( 'requires/aop_material_receive_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";
		
		echo "load_drop_down( 'requires/aop_material_receive_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );\n"; 		
		
		echo "document.getElementById('cbo_location_name').value	= '".$row[csf("location_id")]."';\n";  
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


if ($action=="fabric_finish_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	$data=explode("_",$data);
	?>
	<script>
	
	var selected_dtls_id = new Array;
	var selected_id = new Array;
	var selected_job = new Array;
	 
	function toggle( x, origColor ){
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

		function js_set_value(str,id,fab_process_dtlsid)
		{ 
		
			for( var i = 0; i < selected_dtls_id.length; i++ ) 
			{
				if( selected_job[i] != $('#txt_job_no' + str).val() )
				{
					alert("Job No Not Mixed"); 
					return;
				}
			}
		
		document.getElementById('selected_order').value=id;
		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

		if( jQuery.inArray( $('#txt_dtls_id' + str).val(), selected_dtls_id ) == -1 ) 
		{
			
			
			selected_dtls_id.push( $('#txt_dtls_id' + str).val() );
			selected_id.push( $('#txt_mst_id' + str).val() );
			selected_job.push( $('#txt_job_no' + str).val() );
		}
		else 
		{
			for( var i = 0; i < selected_dtls_id.length; i++ ) 
			{
				if( selected_dtls_id[i] == $('#txt_dtls_id' + str).val() ) break;
			}
			selected_dtls_id.splice( i, 1 );
			selected_id.splice( i, 1 );
			selected_job.splice( i, 1 );
		}
		var mst_id =''; var dtls_id =''; var req_no ='';
		for( var i = 0; i < selected_dtls_id.length; i++ ) 
		{
			dtls_id += selected_dtls_id[i] + ',';
			mst_id += selected_id[i] + ',';
			req_no += selected_job[i] + ',';
		}
		dtls_id	= dtls_id.substr( 0, dtls_id.length - 1 );
		mst_id 	= mst_id.substr( 0, mst_id.length - 1 );
		req_no 	= req_no.substr( 0, req_no.length - 1 );
		$('#txt_dtls_id').val( dtls_id );
		$('#txt_mst_id').val( mst_id );
		$('#txt_job_no').val( req_no );
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			//alert();
			load_drop_down( 'aop_material_receive_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			
			$('#cbo_party_name').attr('disabled','disabled');
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('AOP Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
			else if(val==6) $('#search_by_td').html('Internal Ref.');
		}
		
	</script>
	</head>
	<body onLoad="fnc_load_party_order_popup(<? echo $data[0];?>,<? echo $data[3];?>,<? echo $data[2];?>)">
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">AOP Job No</th>
                            <th width="100">AOP Ref.</th>
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
									$search_by_arr=array(1=>"AOP Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style",6=>"Internal Ref.");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',"","" );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="text" name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[3];?>+'_'+document.getElementById('txt_aop_ref').value, 'create_fabric_finish_search_list_view', 'search_div', 'aop_material_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
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
                 <table width="850" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:50%; float:left" align="left">
                            <input type="hidden" id="txt_dtls_id" /> 
                            <input type="hidden" id="txt_mst_id" /> 
                            <input type="hidden" id="txt_job_no" />
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table> 
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_fabric_finish_search_list_view")
{	
	$data=explode('_',$data);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	$aop_ref =trim(str_replace("'","",$data[8]));
	
	//print_r($aop_re);
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $buyer=" and a.party_id='$data[1]'"; else { $buyer=""; echo "PLease Select Party name"; die;}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	 
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";$internal_ref="";
	if($search_type==1)
	{
		if ($aop_ref!="") $aop_cond=" and a.aop_reference='$aop_ref'"; 
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
			else if ($search_by==6) $internal_ref=" and b.grouping = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($aop_ref!="") $aop_cond=" and a.aop_reference like '%$aop_ref%'"; 
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'";  
			else if ($search_by==6) $internal_ref=" and b.grouping like '%$search_str%' ";  
		}
	}
	else if($search_type==2)
	{
		if ($aop_ref!="") $aop_cond=" and a.aop_reference like '$aop_ref%'"; 
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";  
			else if ($search_by==6) $internal_ref=" and b.grouping like '$search_str%' ";  
		}
	}
	else if($search_type==3)
	{
		if ($aop_ref!="") $aop_cond=" and a.aop_reference like '%$aop_ref'"; 
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";  
			else if ($search_by==6) $internal_ref=" and b.grouping like '%$search_str' "; 
		}
	}
	//echo $aop_ref."==".$aop_cond;die;	
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id) as id";
	//else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
	if(($job_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==5)|| ($po_cond!="" && $search_by==4)|| ($internal_ref!="" && $search_by==6))
	{
		//echo "select $id_cond from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond"; die;
		$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst 
			$job_cond $style_cond $po_cond $internal_ref", "id");
	}
	//echo $search_type; die;
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
	else if($po_ids=="" && ($job_cond!="" && $search_by==2) || ($po_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4)|| ($internal_ref!="" && $search_by==6))
	{
		die;
		//$po_idsCond.=" and b.buyer_po_id in ($ids) ";
	}
	// $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	//echo $poIdsCond; die;
	$buyer_po_arr=array();
	$po_sql ="SELECT a.style_ref_no,a.job_no_prefix_num, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $poIdsCond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
	}
	unset($po_sql_res);
	
	$comp=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	$challan_arr=return_library_array( "SELECT id, recv_number from inv_receive_mas_batchroll where status_active =1 and is_deleted=0",'id','recv_number');
	//$color_lib_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$arr=array (3=>$comp,6=>$yes_no,7=>$color_lib_arr);
	 
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(b.aop_color_id)";
		$buyer_po_id_cond="year(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(b.aop_color_id,',') within group (order by b.aop_color_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
	}



		/*$fabric_data_arr=array();
		$fabric_sql= "SELECT c.id,c.mst_id, a.subcon_job,c.booking_dtls_id,c.batch_issue_qty
		from subcon_ord_mst a, subcon_ord_dtls b ,pro_grey_batch_dtls c
		where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id  and a.order_no=c.booking_no and b.booking_dtls_id=c.booking_dtls_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		$fabric_sql_result =sql_select($fabric_sql);
		foreach ($fabric_sql_result as $row)
		{
			$fabric_data_arr[$row[csf("id")]]['batch_issue_qty']+=$row[csf("batch_issue_qty")];
		}
		*/
		
		$pre_qty_arr=array();
		$sql_rec="SELECT a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id, a.remarks,a.fabric_details_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rec_res =sql_select($sql_rec);
		foreach ($sql_rec_res as $row)
		{
			$pre_qty_arr[$row[csf("fabric_details_id")]]['qty']+=$row[csf("quantity")];
		}
		
		
		$fabric_data_arr=array();
		/*$fabric_sql= "SELECT c.id,c.mst_id, a.subcon_job,c.booking_dtls_id,c.batch_issue_qty
		from subcon_ord_mst a, subcon_ord_dtls b ,pro_grey_batch_dtls c
		where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id  and a.order_no=c.booking_no and b.booking_dtls_id=c.booking_dtls_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";*/
		
		$fabric_sql= "SELECT c.id,c.mst_id, a.subcon_job,c.booking_dtls_id,c.batch_issue_qty
		from subcon_ord_mst a, subcon_ord_dtls b ,pro_grey_batch_dtls c,inv_receive_mas_batchroll d
		where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id  and a.order_no=c.booking_no and b.booking_dtls_id=c.booking_dtls_id  and c.mst_id=d.id and d.entry_form=91  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
		$fabric_sql_result =sql_select($fabric_sql);
		foreach ($fabric_sql_result as $row)
		{
			$fabric_data_arr[$row[csf("id")]]['batch_issue_qty']+=$row[csf("batch_issue_qty")];
		}
	
	
	//print_r($fabric_data_arr);
	
	

	
	/*$sql= "SELECT a.id,c.mst_id,c.id as fab_process_dtlsid, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,c.batch_issue_qty, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id,a.aop_reference 
	from subcon_ord_mst a, subcon_ord_dtls b ,pro_grey_batch_dtls c
	where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id  and a.order_no=c.booking_no and b.booking_dtls_id=c.booking_dtls_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $aop_cond group by a.id,c.mst_id,c.id,a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,a.aop_reference,c.batch_issue_qty
	order by a.id DESC";*/
	
	$sql= "SELECT a.id,c.mst_id,c.id as fab_process_dtlsid, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,c.batch_issue_qty, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id,a.aop_reference 
	from subcon_ord_mst a, subcon_ord_dtls b ,pro_grey_batch_dtls c,inv_receive_mas_batchroll d 
	where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id  and a.order_no=c.booking_no and b.booking_dtls_id=c.booking_dtls_id  and c.mst_id=d.id and d.entry_form=91   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $aop_cond 
	group by a.id,c.mst_id,c.id,a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,a.aop_reference,c.batch_issue_qty
	order by a.id DESC";

	//echo $sql;die;

	$data_array=sql_select($sql);
	
	
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="985" >
        <thead>
            <th width="30">SL</th>
            <th width="50">Job No</th>
            <th width="40">Year</th>
            <th width="100">W/O No</th>
            <th width="100">Challan No</th>
            <th width="100">Buyer Job</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th width="80">Color</th>
            <th>AOP Ref.</th>
        </thead>
        </table>
        <div style="width:985px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="965" class="rpt_table" id="tbl_po_list">
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
				$buyer_po=""; $buyer_style=""; $buyer_job="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
					if($buyer_job=="") $buyer_job=$buyer_po_arr[$po_id]['job']; else $buyer_job.=','.$buyer_po_arr[$po_id]['job'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				$buyer_job=implode(",",array_unique(explode(",",$buyer_job)));
				
				
				$total_batch_issue_qty=$fabric_data_arr[$row[csf("fab_process_dtlsid")]]['batch_issue_qty'];
				$pre_receive_qty=$pre_qty_arr[$row[csf("fab_process_dtlsid")]]['qty'];
				$balance_batch_issue_qty=$total_batch_issue_qty-$pre_receive_qty;
				//$balance_batch_issue_qty;
				if($balance_batch_issue_qty>0)
				{
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>" style="text-decoration:none;cursor:pointer;" onClick="js_set_value(<? echo $i;?>,'<? echo $row[csf('subcon_job')];?>','<? echo $row[csf('fab_process_dtlsid')];?>')">
                    <td width="30"><? echo $i; ?>
							<input type="hidden" name="txt_mst_id[]" id="txt_mst_id<? echo $i ?>" value="<? echo $row[csf('mst_id')]; ?>"/>
							<input type="hidden" name="txt_dtls_id[]" id="txt_dtls_id<? echo $i ?>" value="<? echo $row[csf('fab_process_dtlsid')]; ?>"/>
							<input type="hidden" name="txt_job_no[]" id="txt_job_no<? echo $i ?>" value="<? echo $row[csf('subcon_job')]; ?>"/>
                    </td>
                    <td width="50"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="40" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $challan_arr[$row[csf('mst_id')]]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_job; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td width="80" style="word-break:break-all"><? echo $color_name; ?></td>
                    <td style="word-break:break-all"><? echo $row[csf('aop_reference')]; ?></td>	
                </tr>
				<? 
				}
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?    
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
			load_drop_down( 'aop_material_receive_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			
			$('#cbo_party_name').attr('disabled','disabled');
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('AOP Job No');
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
                <table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="7"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Buyer Po</th>
                            <th width="100">AOP Ref.</th>
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
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<?
									$search_by_arr=array(1=>"AOP Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",4,'search_by(this.value)',"","" );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="text" name="txt_aop_ref" id="txt_aop_ref" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[3];?>+'_'+document.getElementById('txt_aop_ref').value, 'create_job_search_list_view', 'search_div', 'aop_material_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
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
	$aop_ref =trim(str_replace("'","",$data[8]));
	
	//print_r($aop_re);
	
	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { $company=""; echo "PLease Select Company name"; die;}
	if ($data[1]!=0) $buyer=" and a.party_id='$data[1]'"; else { $buyer=""; echo "PLease Select Party name"; die;}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	 
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if ($aop_ref!="") $aop_cond=" and a.aop_reference='$aop_ref'"; 
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if ($aop_ref!="") $aop_cond=" and a.aop_reference like '%$aop_ref%'"; 
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'";  
		}
	}
	else if($search_type==2)
	{
		if ($aop_ref!="") $aop_cond=" and a.aop_reference like '$aop_ref%'"; 
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if ($aop_ref!="") $aop_cond=" and a.aop_reference like '%$aop_ref'"; 
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";  
		}
	}
	//echo $aop_ref."==".$aop_cond;die;	
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id) as id";
	//else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";
	if(($job_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==5)|| ($po_cond!="" && $search_by==4))
	{
		//echo "select $id_cond from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond"; die;
		$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst 
			$job_cond $style_cond $po_cond", "id");
	}
	//echo $search_type; die;
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
	else if($po_ids=="" && ($job_cond!="" && $search_by==2) || ($po_cond!="" && $search_by==3) || ($style_cond!="" && $search_by==4))
	{
		die;
		//$po_idsCond.=" and b.buyer_po_id in ($ids) ";
	}
	// $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	//echo $poIdsCond; die;
	$buyer_po_arr=array();
	$po_sql ="SELECT a.style_ref_no,a.job_no_prefix_num, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $poIdsCond and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no_prefix_num")];
	}
	unset($po_sql_res);
	
	$comp=return_library_array( "SELECT id, company_name from lib_company where status_active =1 and is_deleted=0",'id','company_name');
	//$color_lib_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$arr=array (3=>$comp,6=>$yes_no,7=>$color_lib_arr);
	
	if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str="group_concat(b.aop_color_id)";
		$buyer_po_id_cond="year(b.buyer_po_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str="listagg(b.aop_color_id,',') within group (order by b.aop_color_id)";
		$buyer_po_id_cond="listagg(b.buyer_po_id,',') within group (order by b.buyer_po_id)";
		$buyer_po_cond="listagg(b.buyer_po_no,',') within group (order by b.id)";
		$buyer_style_cond="listagg(b.buyer_style_ref,',') within group (order by b.id)";
	}
	
	$sql= "SELECT a.id, a.subcon_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date, $color_id_str as color_id, $buyer_po_id_cond as buyer_po_id, $buyer_po_cond as buyer_po_no , $buyer_style_cond as buyer_style_ref ,a.aop_reference 
	from subcon_ord_mst a, subcon_ord_dtls b 
	where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $aop_cond 
	group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,a.aop_reference
	order by a.id DESC";

	//echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="50">Job No</th>
            <th width="40">Year</th>
            <th width="100">W/O No</th>
            <th width="100">Buyer Job</th>
            <th width="100">Buyer Po</th>
            <th width="100">Buyer Style</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
            <th width="80">Color</th>
            <th>AOP Ref.</th>
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
				$buyer_po=""; $buyer_style=""; $buyer_job="";
				if($within_group==1)
				{
					$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
					foreach($buyer_po_id as $po_id)
					{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
						if($buyer_job=="") $buyer_job=$buyer_po_arr[$po_id]['job']; else $buyer_job.=','.$buyer_po_arr[$po_id]['job'];
					}
				}
				else
				{
					$buyer_po_no=explode(",",$row[csf('buyer_po_no')]);
					$buyer_style_ref=explode(",",$row[csf('buyer_style_ref')]);
					foreach($buyer_po_no as $po_no)
					{
						if($buyer_po=="") $buyer_po=$po_no; else $buyer_po.=','.$po_no;
					}

					foreach($buyer_style_ref as $style_no)
					{
						if($buyer_style=="") $buyer_style=$style_no; else $buyer_style.=','.$style_no;
					}
				}
				
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				$buyer_job=implode(",",array_unique(explode(",",$buyer_job)));
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('subcon_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="50"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="40" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_job; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_po; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_style; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td width="80" style="word-break:break-all"><? echo $color_name; ?></td>
                    <td style="word-break:break-all"><? echo $row[csf('aop_reference')]; ?></td>	
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
	$jobno=''; $update_id=0;
	$update_id=$exdata[0];
	$jobno=$exdata[1];
	$within_group=$exdata[3];
	$fabric_dtls_id=$exdata[4];
	$color_arrey=return_library_array( "SELECT id,color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$size_arrey=return_library_array( "SELECT id,size_name from  lib_size where status_active =1 and is_deleted=0",'id','size_name');
	$buyer_po_arr=array();
	
	$po_sql ="SELECT a.style_ref_no, b.id, b.grouping, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['int_ref']=$row[csf("grouping")];
	}
	unset($po_sql_res);
	$updtls_data_arr=array();$fab_updtls_data_arr=array(); $pre_qty_arr=array();
	
	if($within_group==1)
	{
		
		$sql_rec="SELECT a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id, a.remarks,a.fabric_details_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$sql_rec_res =sql_select($sql_rec);
		foreach ($sql_rec_res as $row)
		{
			if($row[csf("mst_id")]==$update_id)
			{
				//$updtls_data_arr[$row[csf("job_dtls_id")]]['dtlsid']=$row[csf("id")];
				$updtls_data_arr[$row[csf("fabric_details_id")]]['dtlsid']=$row[csf("id")];
				//$updtls_data_arr[$row[csf("job_dtls_id")]]['qty']=$row[csf("quantity")];
				$updtls_data_arr[$row[csf("fabric_details_id")]]['qty']=$row[csf("quantity")];
				$updtls_data_arr[$row[csf("job_dtls_id")]]['remarks']=$row[csf("remarks")];
				
			}
			else
			{
				$pre_qty_arr[$row[csf("fabric_details_id")]]['qty']+=$row[csf("quantity")];
			}
		}
	}
	else if($within_group==2)
	{
		/*$sql_rec="SELECT a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.buyer_po_id, a.remarks,b.fabric_details_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";*/
		
		$sql_rec="SELECT a.id, a.mst_id, a.quantity, a.uom, a.job_dtls_id, a.grouping, a.buyer_po_id, a.remarks,a.fabric_details_id from sub_material_dtls a, sub_material_mst b where b.id=a.mst_id and b.embl_job_no='$jobno' and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rec_res =sql_select($sql_rec);
		
		foreach ($sql_rec_res as $row)
		{
			if($row[csf("mst_id")]==$update_id)
			{
				$updtls_data_arr[$row[csf("job_dtls_id")]]['dtlsid']=$row[csf("id")];
				$updtls_data_arr[$row[csf("job_dtls_id")]]['qty']=$row[csf("quantity")];
				$updtls_data_arr[$row[csf("job_dtls_id")]]['remarks']=$row[csf("remarks")];
				$updtls_data_arr[$row[csf("job_dtls_id")]]['int_ref']=$row[csf("grouping")];
			}
			else
			{
				$pre_qty_arr[$row[csf("job_dtls_id")]]['qty']+=$row[csf("quantity")];
			}
		}
	}
	
	
	if($within_group==1)
	{
	 		if($update_id!=0)
			{
			 /* $sql_job="SELECT a.id, c.id as fabric_dtlsid,a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.body_part, c.batch_issue_qty as order_quantity, b.order_uom , b.body_part, b.construction, b.composition, b.gsm, b.grey_dia, b.gmts_color_id, b.item_color_id, b.fin_dia, b.aop_color_id, b.lib_yarn_deter,b.booking_dtls_id,b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer
				from subcon_ord_mst a, subcon_ord_dtls b,pro_grey_batch_dtls c
				where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id  and a.order_no=c.booking_no and b.booking_dtls_id=c.booking_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.subcon_job='$jobno' order by b.id ASC";*/
				
				 $sql_job="SELECT a.id, c.id as fabric_dtlsid,a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.body_part, c.batch_issue_qty as order_quantity, b.order_uom , b.body_part, b.construction, b.composition, b.gsm, b.grey_dia, b.gmts_color_id, b.item_color_id, b.fin_dia, b.aop_color_id, b.lib_yarn_deter,b.booking_dtls_id
				from subcon_ord_mst a, subcon_ord_dtls b,pro_grey_batch_dtls c,inv_receive_mas_batchroll d
				where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id  and a.order_no=c.booking_no and b.booking_dtls_id=c.booking_dtls_id and c.mst_id=d.id and d.entry_form=91 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.subcon_job='$jobno' order by b.id ASC";
				
				
			}
			else
			{
				/* $sql_job="SELECT a.id, c.id as fabric_dtlsid,a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.body_part, c.batch_issue_qty as order_quantity, b.order_uom , b.body_part, b.construction, b.composition, b.gsm, b.grey_dia, b.gmts_color_id, b.item_color_id, b.fin_dia, b.aop_color_id, b.lib_yarn_deter,b.booking_dtls_id,b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer
		from subcon_ord_mst a, subcon_ord_dtls b,pro_grey_batch_dtls c
		where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id  and a.order_no=c.booking_no and b.booking_dtls_id=c.booking_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.subcon_job='$jobno' and c.id in ($fabric_dtls_id) order by b.id ASC";*/
		
		
		$sql_job="SELECT a.id, c.id as fabric_dtlsid,a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.body_part, c.batch_issue_qty as order_quantity, b.order_uom , b.body_part, b.construction, b.composition, b.gsm, b.grey_dia, b.gmts_color_id, b.item_color_id, b.fin_dia, b.aop_color_id, b.lib_yarn_deter,b.booking_dtls_id
		from subcon_ord_mst a, subcon_ord_dtls b,pro_grey_batch_dtls c,inv_receive_mas_batchroll d
		where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id  and a.order_no=c.booking_no and b.booking_dtls_id=c.booking_dtls_id   and c.mst_id=d.id and d.entry_form=91 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.subcon_job='$jobno' and c.id in ($fabric_dtls_id) order by b.id ASC";
			}
	}
	else if($within_group==2)
	{
	 $sql_job="SELECT a.id, a.subcon_job, a.job_no_prefix_num, a.order_id, a.order_no, a.delivery_date, b.id as po_id, b.buyer_po_id, b.gmts_item_id, b.body_part, b.order_quantity, b.order_uom , b.body_part, b.construction, b.composition, b.gsm, b.grey_dia, b.gmts_color_id, b.item_color_id, b.fin_dia, b.aop_color_id, b.lib_yarn_deter,b.booking_dtls_id,b.buyer_po_no, b.buyer_style_ref, b.buyer_buyer
		from subcon_ord_mst a, subcon_ord_dtls b 
		where a.entry_form=278 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.subcon_job='$jobno' order by b.id ASC";
		
	}
	//echo $sql_job;
	
	$sql_result =sql_select($sql_job);
	$k=0;
	$num_rowss=count($sql_result);
	foreach ($sql_result as $row)
	{
		$k++;
		
		if($within_group==1)
		{
			$quantity=0; $dtlsup_id=""; $balanceQty=0; $prerec_qty=0; $orderQty=0; $remarks='';
			$prerec_qty=$pre_qty_arr[$row[csf("fabric_dtlsid")]]['qty'];
			$orderQty=number_format($row[csf("order_quantity")],4,'.','');
			//$orderQty=number_format($fabric_data_arr[$row[csf("fabric_dtlsid")]]['batch_issue_qty'],4,'.','');
			//$balanceQty=number_format($orderQty,4,'.','');
			$balanceQty=number_format($orderQty-$prerec_qty,4,'.','');
			if($update_id!=0)
			{
				$quantity=$updtls_data_arr[$row[csf("fabric_dtlsid")]]['qty'];
				$remarks=$updtls_data_arr[$row[csf("po_id")]]['remarks'];
			}
			else $quantity='';
			if($quantity==0) $quantity='';
			if($balanceQty==0) $balanceQty=0;
			
			$dtlsup_id=$updtls_data_arr[$row[csf("fabric_dtlsid")]]['dtlsid'];
			$buyer_po=$buyer_po_arr[$row[csf("buyer_po_id")]]['po'];
			$buyer_style=$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
			$int_ref= $buyer_po_arr[$row[csf("buyer_po_id")]]['int_ref'];
			//echo shakil."_".$int_ref; die;
		
		}
		if($within_group==2)
		{
			$quantity=0; $dtlsup_id=""; $balanceQty=0; $prerec_qty=0; $orderQty=0; $remarks='';
			$prerec_qty=$pre_qty_arr[$row[csf("po_id")]]['qty'];
			$orderQty=number_format($row[csf("order_quantity")],4,'.','');
			$balanceQty=number_format($orderQty-$prerec_qty,4,'.','');
			
			if($update_id!=0)
			{
				$quantity=$updtls_data_arr[$row[csf("po_id")]]['qty'];
				$remarks=$updtls_data_arr[$row[csf("po_id")]]['remarks'];
			}
			else $quantity='';
			if($quantity==0) $quantity='';
			if($balanceQty==0) $balanceQty=0;
			
			$dtlsup_id=$updtls_data_arr[$row[csf("po_id")]]['dtlsid'];
			$buyer_po=$row[csf("buyer_po_no")];
			$buyer_style=$row[csf("buyer_style_ref")];
			$int_ref= $updtls_data_arr[$row[csf("po_id")]]['int_ref'];
		
		}
		?>
		 <tr>
            <td>
            	<input type="hidden" name="ordernoid_<? echo $k; ?>" id="ordernoid_<? echo $k; ?>" value="<? echo $row[csf("po_id")]; ?>"/>
                <input type="hidden" name="jobno_<? echo $k; ?>" id="jobno_<? echo $k; ?>" value="<? echo $row[csf("subcon_job")]; ?>"/>
                <input type="hidden" name="updatedtlsid_<? echo $k; ?>" id="updatedtlsid_<? echo $k; ?>" value="<? echo $dtlsup_id; ?>"/>
                <input type="hidden" name="fabricdtlsid_<? echo $k; ?>" id="fabricdtlsid_<? echo $k; ?>" value="<? echo $row[csf("fabric_dtlsid")]; ?>"/>
                <input type="text" name="txtorderno_<? echo $k; ?>" id="txtorderno_<? echo $k; ?>" class="text_boxes" style="width:90px" value="<? echo $row[csf("order_no")]; ?>" readonly />
                
            </td>

            <td><input type="text" name="internalRef_<? echo $k; ?>" id="internalRef_<? echo $k; ?>" type="text" class="text_boxes" style="width:90px" value="<? echo $int_ref ; ?>" readonly /></td>

            <td><input type="text" name="txtbuyerPo_<? echo $k; ?>" id="txtbuyerPo_<? echo $k; ?>"  class="text_boxes" style="width:90px" value="<? echo $buyer_po; ?>" readonly />
                <input type="hidden" name="txtbuyerPoId_<? echo $k; ?>" id="txtbuyerPoId_<? echo $k; ?>" class="text_boxes" style="width:70px" value="<? echo $row[csf("buyer_po_id")]; ?>" />
            </td>
            <td><input type="text" name="txtstyleRef_<? echo $k; ?>" id="txtstyleRef_<? echo $k; ?>"  class="text_boxes" style="width:90px" value="<? echo $buyer_style; ?>" readonly /></td>
            <td><? echo create_drop_down( "cboBodyPart_".$k, 90, $body_part,"", 1, "--Select--",$row[csf('body_part')],"", 1 ); ?></td>
            <td><input type="text" id="txtConstruction_<? echo $k; ?>" name="txtConstruction_<? echo $k; ?>" class="text_boxes" style="width:77px" value="<? echo $row[csf("construction")]; ?>" readonly /></td>
            <td><input type="text" id="txtComposition_<? echo $k; ?>" name="txtComposition_<? echo $k; ?>" class="text_boxes" style="width:77px"  value="<? echo $row[csf("composition")]; ?>" readonly/></td>
            <td><input type="text" id="txtGsm_<? echo $k; ?>" name="txtGsm_<? echo $k; ?>" class="text_boxes" style="width:47px" value="<? echo $row[csf("gsm")]; ?>" readonly /></td>
            <td><input type="text" id="txtDia_<? echo $k; ?>" name="txtDia_<? echo $k; ?>" class="text_boxes" style="width:47px"  value="<? echo $row[csf("grey_dia")]; ?>" readonly /></td>
            <td><input type="text" id="txtGmtsColor_<? echo $k; ?>" name="txtGmtsColor_<? echo $k; ?>" class="text_boxes" style="width:67px" value="<? echo $color_arrey[$row[csf("gmts_color_id")]]; ?>" readonly /></td>
            <td><input type="text" id="txtItemColor_<? echo $k; ?>" name="txtItemColor_<? echo $k; ?>" class="text_boxes" style="width:67px"  value="<? echo $color_arrey[$row[csf("item_color_id")]]; ?>" readonly/></td>
            <td><input type="text" id="txtFinDia_<? echo $k; ?>" name="txtFinDia_<? echo $k; ?>" class="text_boxes" style="width:47px" value="<? echo $row[csf("fin_dia")]; ?>" readonly /></td>
            <td><input type="text" id="txtAopColor_<? echo $k; ?>" name="txtAopColor_<? echo $k; ?>" class="text_boxes" style="width:67px" value="<? echo $color_arrey[$row[csf("aop_color_id")]]; ?>" readonly /></td>
            <td><input type="text" id="txtreceiveqty_<? echo $k; ?>" name="txtreceiveqty_<? echo $k; ?>" class="text_boxes_numeric" style="width:57px" onKeyUp="check_receive_qty_ability(this.value,<? echo $k; ?>); fnc_total_calculate();" value="<? echo $quantity; ?>" placeholder="<? echo $balanceQty; ?>" pre_rec_qty="<? echo $prerec_qty; ?>" order_qty="<? echo $orderQty; ?>"  /></td>
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