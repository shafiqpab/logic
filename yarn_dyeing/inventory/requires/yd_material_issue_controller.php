<?
include('../../../includes/common.php'); 
session_start();

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$trans_Type="2";

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );	
	exit();	 
}

if ($action=="load_drop_down_buyer")
{
	//echo $data; die;
	$data=explode('_',$data);
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --",$data[2], "" );
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
	$trans_Type="2";
	//echo $total_row;die;
	//echo $process;die;
	// Insert Start Here ----------------------------------------------------------
	if ($operation==0)   
	{

		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
			
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
			
		$new_issue_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name),'', 'YDMI' , date("Y",time()), 5, "select id,trans_no_prefix,trans_no_prefix_num from yd_material_mst where company_id=$cbo_company_name and trans_Type='$trans_Type' and entry_form=388 $insert_date_con order by id desc", 'trans_no_prefix', 'trans_no_prefix_num' ));

		if(is_duplicate_field( "a.chalan_no", "yd_material_mst a, yd_material_dtls b", "a.yd_job_id='$new_issue_no[0]' and a.trans_Type='$trans_Type' and a.entry_form=388 and a.chalan_no=$txt_issue_challan and b.order_id='$order_no_id' and b.material_description='$txt_material_description' and b.color_id='$color_id'" )==1)
		{
			//check_table_status( $_SESSION['menu_id'],0);
			echo "11**0";
			die;
		}			
		
		$id=return_next_id("id","yd_material_mst",1) ;
		$field_array="id,entry_form, booking_without_order, booking_type, embl_job_no, trans_no_prefix, trans_no_prefix_num, yd_trans_no, trans_type, company_id, location_id, party_id, chalan_no, issue_to, receive_date, receive_quantity, within_group, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.",'388',".$hdn_booking_without_order.",".$hdn_booking_type_id.",".$txt_job_no.",'".$new_issue_no[1]."','".$new_issue_no[2]."','".$new_issue_no[0]."','".$trans_Type."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_party_name.",".$txt_issue_challan.",1,".$txt_issue_date.",".$txt_receive_quantity.",".$cbo_within_group.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
		$txt_issue_no=$new_issue_no[0];//change_date_format($data[2], "dd-mm-yyyy", "-",1)
		
		$id1=return_next_id("id","yd_material_dtls",1) ;
		$field_array2="id, mst_id,entry_form, order_id, sales_order_id,sales_order_no,product_id, receive_qty, uom, job_dtls_id, buyer_po_id,color_id,rec_cone, inserted_by, insert_date, status_active, is_deleted";
		$data_array2="";  $add_commaa=0;
		
		for($i=1; $i<=$total_row; $i++)				
		{
			$ordernoid			= "ordernoid_".$i;
			$orderId			= "hdnOrderId_".$i;
			$txtbuyerPoId		= "txtbuyerPoId_".$i;
			$coloridd	        = "colorid_".$i;
			$cbouom				= "cbouom_".$i;
			$txtissueqty		= "txtissuequantity_".$i;
			
			$colorid		    = "txtitemcolor_".$i;
			$noofcone		    = "txtnoofcone_".$i;
			$updatedtlsid		= "updatedtlsid_".$i;
			$salesorderid		= "hidsalesorderid_".$i;
			$productid		    = "hidproductid_".$i;
			$salesno		    = "txtsalesorderno_".$i;
			
			
			
			
			if ($add_commaa!=0) $data_array2 .=",";

			if ($cbo_within_group==1) {
				$data_array2.="(".$id1.",'".$id."','388',".$$orderId.",".$$salesorderid.",".$$salesno.",".$$productid.",".$$txtissueqty.",".$$cbouom.",".$$ordernoid.",".$$txtbuyerPoId.",".$$coloridd.",".$$noofcone.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			}else{
				$data_array2.="(".$id1.",'".$id."','388','".$$orderId."',".$$salesorderid.",".$$salesno.",".$$productid.",".$$txtissueqty.",".$$cbouom.",".$$ordernoid.",".$$txtbuyerPoId.",".$$coloridd.",".$$noofcone.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			}
			 
			$id1=$id1+1; $add_commaa++;
			
		}
		$flag=1;
		/*echo "INSERT INTO yd_material_mst (".$field_array.") VALUES ".$data_array; die;*/
		$rID=sql_insert("yd_material_mst",$field_array,$data_array,0);
		if($flag==1 && $rID==1) $flag=1; else $flag=0;
		//echo $id1;
		//echo "10**INSERT INTO yd_material_dtls (".$field_array2.") VALUES ".$data_array2; die;
		$rID2=sql_insert("yd_material_dtls",$field_array2,$data_array2,1);	
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
				echo "102**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$id)."**".str_replace("'",'',$txt_job_no);
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
		$flag = 1;
		$con = connect();

		// echo '10**emnei echo';die;

		if($db_type==0) mysql_query("BEGIN");

		$field_array_mst="location_id*chalan_no*receive_date*receive_quantity*updated_by*update_date";
		$data_array_mst="".$cbo_location_name."*".$txt_issue_challan."*".$txt_issue_date."*".$txt_receive_quantity."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls = "color_id*uom*receive_qty*rec_cone*updated_by*update_date";

		for($i = 1; $i <= $total_row; $i++) {
			$ordernoid			= "ordernoid_".$i; 
			$txtbuyerPoId		= "txtbuyerPoId_".$i;
			$coloridd	        = "colorid_".$i;
			$cbouom				= "cbouom_".$i;
			$txtissueqty		= "txtissuequantity_".$i;
			
			$itemcolor		    = "txtitemcolor_".$i;
			$noofcone		    = "txtnoofcone_".$i;
			$updatedtlsid		= "updatedtlsid_".$i;
			$salesorderid		= "hidsalesorderid_".$i;
			$productid		    = "hidproductid_".$i;
			$salesno		    = "txtsalesorderno_".$i;

			$data_array_dtls[str_replace("'","",$$updatedtlsid)]=explode("*",("".$$coloridd."*".$$cbouom."*".$$txtissueqty."*".$$noofcone."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			$id_arr[]=str_replace("'", "", $$updatedtlsid);
		}

		// sql_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
		$rID = sql_update("yd_material_mst", $field_array_mst, $data_array_mst, "id", $update_id, 0);
		$flag = ($flag && $rID);	// return true if $flag is true and mst table update is successful

		/*echo "10**"."yd_material_mst", $field_array_mst, $data_array_mst, "id", $update_id ;die;*/
		/*echo "10**" . bulk_update_sql_statement("yd_material_dtls", "id", $field_array_dtls, $data_array_dtls, $id_arr);die;*/
		/*var_dump($flag);die;*/

		$rID2 = execute_query(bulk_update_sql_statement("yd_material_dtls", "id", $field_array_dtls, $data_array_dtls, $id_arr), 1);

		$flag = ($flag && $rID2);	// return true if $flag is true and dtls table update is successful

		$flag = ($flag && $rID2);	// return true if $flag is true and dtls table insert is successful

		if($db_type==0) {
			if($flag) {
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
				// echo "0**".str_replace("'", '', $txt_receive_no)."**".$id_mst."**".str_replace("'",'',$txt_job_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}
		else if($db_type==2) {
			if($flag) {
				oci_commit($con);
				echo "1**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			} else {
				oci_rollback($con);
				echo "10**".str_replace("'",'',$txt_issue_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_job_no);
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==2)   // delete
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		 //echo $zero_val;
		 
		
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
		$data_array_dtls="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			
		$flag=1;
		$rID=sql_update("yd_material_mst",$field_array,$data_array,"id",$update_id,0);  
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo "INSERT INTO sub_material_dtls (".$field_array.") VALUES ".$data_array_dtls; die;

		$rID1=sql_update("yd_material_dtls",$field_array,$data_array_dtls,"mst_id",$update_id,1); 
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
		disconnect($con); 
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
			load_drop_down( 'yd_material_issue_controller', company+'_'+within_group+'_'+party_name, 'load_drop_down_buyer_pop', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('YD Job No');
			else if(val==2) $('#search_by_td').html('Order No');
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
                            <th width="70">Issue ID</th>
                            <th width="80">Challan No</th>
                            <th width="100">Search By</th>
                    		<th width="100" id="search_by_td">YD Job No</th>
                            <th width="100" colspan="2">Date Range</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td> <input type="hidden" id="selected_job">  <!--  echo $data;-->
							<? 
								echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'yd_material_issue_controller', this.value+'_'+".$within_group.", 'load_drop_down_buyer_pop', 'buyer_td' );"); ?>
                            </td>
                            <td>
							<?
								echo create_drop_down( "cbo_within_group", 50, $yes_no,"", 0, "--  --",$within_group, "load_drop_down( 'yd_material_issue_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer_pop', 'buyer_td' );" ); ?>
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
                                    $search_by_arr=array(1=>"YD Job No",2=>"Order No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style");
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
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_search_challan').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value, 'create_issue_search_list_view', 'search_div', 'yd_material_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="10" align="center" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="text_boxes" style="width:70px">
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
	
	$search_type =$data[6];
	$within_group =$data[7];
	$search_by=str_replace("'","",$data[8]);
	$search_str=trim(str_replace("'","",$data[9]));
	/*echo $search_str;die;*/

	if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.party_id='$data[1]'"; else $buyer_cond="";
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $issue_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $issue_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $issue_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $issue_date ="";
	}
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.trans_no_prefix_num='$search_str'";
			
			else if ($search_by==3) $search_com_cond=" and a.trans_no_prefix_num = '$search_str' ";
			
			
		}
		
		
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.trans_no_prefix_num like '$search_str%'";  
			 
			else if ($search_by==3) $search_com_cond=" and a.trans_no_prefix_num like '$search_str%'";  
			
		}
		
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.trans_no_prefix_num like '$search_str%'";  
			
			
			else if ($search_by==3) $search_com_cond=" and a.trans_no_prefix_num like '$search_str%'";  
			
		}
		
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.trans_no_prefix_num like '%$search_str'";  
			
			
			else if ($search_by==3) $search_com_cond=" and a.trans_no_prefix_num like '%$search_str'";  
			
		}
		
	}	
	
	
			$order_buyer_po_array=array();
			$buyer_po_arr=array();
			$order_buyer_po='';
			/*$order_sql ="select b.id,b.buyer_po_no,b.buyer_style_ref from yd_ord_mst a, yd_ord_dtls b where a.id=b.mst_id and a.entry_form='374' $search_com_cond"; */
			
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array("select id, company_name from lib_company",'id','company_name');
	$po_arr=return_library_array( "select id,order_no from yd_ord_dtls",'id','order_no');
	
	/*$po_ids=''; //$buyer_po_arr=array();
	if($within_group==1)
	{
		if($db_type==0) $id_cond="group_concat(b.id)";
		else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
		//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
		if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
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
		unset($po_sql_res);
	}*/
	//$spo_ids='';
	
	if($db_type==0)
	{
		$id_cond="group_concat(b.id)";
		$insert_date_cond="year(a.insert_date)";
						
		$buyer_po_id_cond="group_concat(distinct(b.order_id))";
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
	
	if ( $spo_ids!="") $spo_idsCond=" and b.job_dtls_id in ($spo_ids)"; else $spo_idsCond="";
	*/
	
	$sql= "select a.id, a.yd_trans_no, a.trans_no_prefix, a.trans_no_prefix_num, a.chalan_no, $insert_date_cond as year, a.location_id, a.party_id, a.receive_date, a.within_group
	from yd_material_mst a, yd_material_dtls b where a.entry_form=388 and a.id=b.mst_id and a.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $issue_date $company $buyer_cond $withinGroup $issue_id_cond
	group by a.id, a.yd_trans_no, a.trans_no_prefix_num,a.trans_no_prefix,a.chalan_no, a.insert_date, a.location_id, a.party_id, a.receive_date, a.within_group order by a.id DESC";
	/*echo $sql;die;*/ 
	$result = sql_select($sql);
	?>
    <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
            <thead>
                <th width="40" >SL</th>
                <th width="70" >Issue No</th>
                <th width="70" >Year</th>
                <th width="120" >Party Name</th>
                <th width="100" >Challan No</th>
                <th width="80" >Issue Date</th>
                <th width="120">Order No</th>
                
               
            </thead>
     	</table>
     <div style="width:820px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
                
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")];?>');" > 
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="70" align="center"><? echo $row[csf("yd_trans_no")]; ?></td>
                        <td width="70" align="center"><? echo $row[csf("year")]; ?></td>
                        <td width="120"><?php echo $party_arr[$row[csf('party_id')]]; ?></td>
						<td width="100" align="center"><? echo $row[csf("chalan_no")]; ?></td>
						<td width="80"><? echo change_date_format($row[csf("receive_date")]);  ?></td>	
						<td width="120" style="word-break:break-all"><p><? echo $row[csf("yd_trans_no")]; ?></p></td>	
                        
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
	
	$nameArray=sql_select( "select id, yd_trans_no,embl_job_no,company_id,receive_quantity, location_id, party_id, receive_date,receive_quantity, chalan_no,within_group from yd_material_mst where id='$data' and is_deleted=0 and  status_active=1");
	/*echo "select id, yd_trans_no,yd_job,company_id,receive_quantity, location_id, party_id, receive_date,receive_quantity, chalan_no,within_group from yd_material_mst where id='$data'"; die;*/
	/*print_r($nameArray);die;*/

	//echo "select id, yd_trans_no,embl_job_no,company_id,receive_quantity, location_id, party_id, receive_date,receive_quantity, chalan_no,within_group from yd_material_mst where id='$data'";
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_issue_no').value 		= '".$row[csf("yd_trans_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 	= '".$row[csf("company_id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n"; 
		
		echo "document.getElementById('cbo_within_group').value		= '".$row[csf("within_group")]."';\n";

		echo "load_drop_down( 'requires/yd_material_issue_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";
		
		echo "load_drop_down( 'requires/yd_material_issue_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );\n"; 		
		
		echo "document.getElementById('cbo_location_name').value	= '".$row[csf("location_id")]."';\n";  
		echo "document.getElementById('cbo_party_name').value		= '".$row[csf("party_id")]."';\n"; 
		echo "document.getElementById('txt_issue_challan').value	= '".$row[csf("chalan_no")]."';\n"; 
		echo "document.getElementById('txt_issue_date').value 	= '".change_date_format($row[csf("receive_date")])."';\n";  
		
	    echo "document.getElementById('update_id').value            = '".$row[csf("id")]."';\n";
	    echo "document.getElementById('txt_receive_quantity').value 		= '".number_format($row[csf("receive_quantity")],2,".","")."';\n";
	    echo "document.getElementById('txt_job_no').value 		= '".$row[csf("embl_job_no")]."';\n";
	    
		//echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_material_receive',1);\n";
		
		echo "$('#txt_job_no').attr('disabled','true')".";\n"; 
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
			$("#hidden_mst_id").val(id);
			document.getElementById('selected_order').value=id;
			parent.emailwindow.hide();
		}
		
		function fnc_load_party_order_popup(company,within_group,party)
		{
			load_drop_down( 'yd_material_issue_controller', company+'_'+within_group+'_'+party, 'load_drop_down_buyer', 'buyer_td' );
			$('#cbo_party_name').attr('disabled','disabled');
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('YD Job No');
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
                            <th width="150">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Search By</th>
                            <th width="100" id="search_by_td">Buyer PO</th>
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
									$search_by_arr=array(1=>"YD Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer PO",5=>"Buyer Style");
									echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",4,'search_by(this.value)',"","" );
								?>
                            </td>
                            <td align="center">
                                <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+<? echo $data[3];?>, 'create_job_search_list_view', 'search_div', 'yd_material_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" />
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
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";*/
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $search_com_cond=" and b.sales_order_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.style_ref = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{/*
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'"; */
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'"; 
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'"; 
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '%$search_str%'";
			else if ($search_by==4) $search_com_cond=" and b.sales_order_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.style_ref like '%$search_str%'";  
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			/*if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  */
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.sales_order_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.style_ref like '$search_str%'"; 
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			/*if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  */ 
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			else if ($search_by==3) $search_com_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.sales_order_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.style_ref like '%$search_str'"; 
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
	
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	
	echo $po_ids; die;
	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";*/
	
	$buyer_po_arr=array();
	
	$po_sql ="select b.style_ref, b.id, b.order_no from yd_ord_mst a, yd_ord_dtls b where a.yd_job=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("order_no")];
	}
	unset($po_sql_res);
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	/*if($db_type==0)
	{
		$ins_year_cond="year(a.insert_date)"; $color_id_str="group_concat(b.yd_color_id)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')"; $color_id_str="listagg(b.gmts_color_id,',') within group (order by b.gmts_color_id)";
	}*/
	$ins_year_cond="EXTRACT(YEAR from a.insert_date)"; $color_id_str='group_concat(b.yd_color_id)';
	
	/* $sql= "select a.id, a.yd_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.sales_order_no,b.style_ref,b.yd_color_id, b.order_id 
	 from yd_ord_mst a, yd_ord_dtls b 
	 where  a.yd_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 
	 order by a.id DESC";*/

	 $sql= "select a.id, a.yd_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.delivery_date,b.sales_order_no,b.style_ref, b.yd_color_id as color_id, b.order_id  
	 from yd_ord_mst a, yd_ord_dtls b, yd_material_dtls c
	 where a.yd_job=b.job_no_mst and a.id=b.mst_id and b.id=c.job_dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $order_rcv_date $company $buyer $withinGroup $search_com_cond
	 group by a.id, a.yd_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date,a.delivery_date,b.sales_order_no,b.yd_color_id, a.order_no,b.order_id,b.style_ref
	 order by a.id DESC";
 	//echo $sql; 

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885" >
        <thead>
            <th width="30">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            
            <th width="100">Order No</th>
            <th width="100">Style Ref</th>
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
				$excolor_id=array_unique(explode(",",$row[csf('yd_color_id')]));
				/*print_r($excolor_id);*/
				/*$color_name="";*/	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('yd_job')]; ?>")' style="cursor:pointer" >
             

                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf('yd_job')]; ?></td>
                    <td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
                    
                    <td width="100"><? if ($within_group==1) echo $buyer_po_arr[$row[csf('order_no')]]['po']; else echo $row[csf('order_no')];//echo $buyer_po;  echo $buyer_po_arr[$row[csf('buyer_po_id')]]['po']; ?></td>
                    <td width="100"><? if ($within_group==1)echo $buyer_po_arr[$row[csf('order_id')]]['style']; echo $row[csf('style_ref')];//echo $buyer_style;  echo $buyer_po_arr[$row[csf('buyer_po_id')]]['style']; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>	
                    <td><? echo  $color_arr[$row[csf('color_id')]]; //$color_name; ?></td>

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
	//echo $data;die;
	
	 
	
	$sql = "select a.id, a.company_id, a.order_no, a.location_id, a.within_group,a.booking_without_order,a.booking_type, a.party_id, a.receive_date, a.yd_job, b.id as po_id, b.style_ref, b.lot, b.count_id, b.yarn_type_id,b.order_id, b.yarn_composition_id,b.sales_order_id,b.product_id, b.total_order_quantity, b.yd_color_id, b.no_bag, b.cone_per_bag, b.no_cone, b.avg_wgt, b.order_quantity, b.sales_order_no,b.uom,sum(c.receive_qty) as receive_qty
	from yd_ord_mst a, yd_ord_dtls b, yd_material_dtls c
	where a.yd_job='$data'  and a.id = b.mst_id and b.id=c.job_dtls_id  and c.entry_form = 387 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.id, a.company_id, a.order_no, a.location_id, a.within_group,a.booking_without_order,a.booking_type, a.party_id, a.receive_date, a.yd_job, b.id, b.style_ref, b.lot, b.count_id, b.yarn_type_id,b.order_id, b.yarn_composition_id,b.sales_order_id,b.product_id, b.total_order_quantity, b.yd_color_id, b.no_bag, b.cone_per_bag, b.no_cone, b.avg_wgt, b.order_quantity, b.sales_order_no,b.uom";
	//echo $sql;die;
	$data_array = sql_select($sql);
	//print_r($data_array);die;
	$count_arr = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1", 'id', 'yarn_count');
	//$count_arr = return_library_array("select id,construction from lib_yarn_count_determina_mst where is_deleted=0 and status_active=1", 'id', 'construction');
    $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
    $color_arr = return_library_array("select id,color_name from lib_color where is_deleted=0 and status_active=1", 'id', 'color_name');
    $yarn_arr = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1", 'id', 'yarn_count');
	
	
	 

       $sql_iss="select a.id, a.mst_id, a.receive_qty, a.uom, a.job_dtls_id  from yd_material_dtls a, yd_material_mst b where b.id=a.mst_id and b.embl_job_no='$data' and b.entry_form=388  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_iss_res =sql_select($sql_iss);
	
	foreach ($sql_iss_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_data_arr[$row[csf("job_dtls_id")]]['dtlsid']=$row[csf("id")];
			$updtls_data_arr[$row[csf("job_dtls_id")]]['qty']=$row[csf("receive_qty")];
		}
		else
		{
			$pre_qty_arr[$row[csf("job_dtls_id")]]['qty']+=$row[csf("receive_qty")];
		}
	}
	
	$rec_data_arr=array();
	  $sql_rec="select a.id, a.mst_id, a.receive_qty, a.uom, a.job_dtls_id  from yd_material_dtls a, yd_material_mst b where b.id=a.mst_id  and b.entry_form=387 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rec_res =sql_select($sql_rec);
	foreach ($sql_rec_res as $row)
	{
		
		$rec_data_arr[$row[csf("job_dtls_id")]]['qty']+=$row[csf("receive_qty")];
	}
	unset($sql_rec_res);
	
   
    $counter = 1;
    foreach ($data_array as $row) 
	{
		$uomId=$row[csf("uom")];
		
		$qty=0;  $balance_qty=0; $rec_qty=0; $pre_issue_qty=0;
		$rec_qty=$rec_data_arr[$row[csf("po_id")]]['qty'];
		$pre_issue_qty=$pre_qty_arr[$row[csf("po_id")]]['qty'];
		
		//echo $rec_qty."dfdf".$pre_issue_qty; 
		
		$balance_qty=number_format($rec_qty-$pre_issue_qty,4,'.','');
		/*if($update_id!=0)
		{
			$qty=$updtls_data_arr[$row[csf("po_id")]]['qty'];
		}
		else $qty=$balance_qty;*/
		
		
    	?> <tr> 

                <td><input type="hidden" name="ordernoid_<? echo $counter; ?>" id="ordernoid_<? echo $counter; ?>" value="<? echo $row[csf("po_id")]; ?>">
                    <input type="hidden" name="jobno_<? echo $counter; ?>" id="jobno_<? echo $counter; ?>" value="<? echo $row[csf("yd_job")]; ?>">
                    <input type="hidden" name="colorid_<? echo $counter; ?>" id="colorid_<? echo $counter; ?>" value="<? echo $row[csf("yd_color_id")]; ?>">
                    <input type="hidden" name="updatedtlsid_<? echo $counter; ?>" id="updatedtlsid_<? echo $counter; ?>" value="<? echo $row[csf("po_id")]; ?>">
                    <input type="hidden" name="hidsalesorderid_<? echo $counter; ?>" id="hidsalesorderid_<? echo $counter; ?>" value="<? echo $row[csf("sales_order_id")]; ?>">
                    <input type="hidden" name="hidproductid_<? echo $counter; ?>" id="hidproductid_<? echo $counter; ?>" value="<? echo $row[csf("product_id")]; ?>">
                    
                    <input name="hdnOrderId_<? echo $counter; ?>" id="hdnOrderId_<? echo $counter; ?>" type="hidden" value="<? echo $row[csf("order_id")]; ?>" />

                    <input name="txtbuyerPoId_<? echo $counter; ?>" id="txtbuyerPoId_<? echo $counter; ?>" type="hidden" class="text_boxes" style="width:70px" value="-1" /> 
                    
                    <input name="txtstyle_<?php echo $counter; ?>" id="txtstyle_<?php echo $counter; ?>" class="text_boxes" type="text"  style="width:70px" value="<?php echo $row[csf("style_ref")]; ?>" readonly/>
                     
                     
                </td> 
                <td>
                     <input type="text" id="txtsalesorderno_<?php echo $counter; ?>" name="txtsalesorderno_" class="text_boxes" style="width:120px" value="<?php echo $row[csf("sales_order_no")]; ?>" readonly>
                </td>
                <td>
                     <input type="text" id="txtlot_<?php echo $counter; ?>" name="txtlot_<?php echo $counter; ?>" class="text_boxes" style="width:40px" value="<?php echo $row[csf("lot")]; ?>" readonly>
                </td>

                <td>
                     <input type="text" id="txtcount_<?php echo $counter; ?>" name="txtcount_<?php echo $counter; ?>" class="text_boxes" style="width:50px" value="<?php echo $count_arr[$row[csf("count_id")]]; ?>" readonly>
                </td>
                <td>
                     <input type="text" id="txtyarntype_<?php echo $counter; ?>" name="txtyarntype_<?php echo $counter; ?>" class="text_boxes" style="width:50px" value="<?php echo $yarn_type[$row[csf("yarn_type_id")]]; ?>" readonly>
                </td>
                <td>
                    <input name="txtyarncomposition_<?php echo $counter; ?>" id="txtyarncomposition_<?php echo $counter; ?>" class="text_boxes" type="text"  style="width:60px" value="<?php echo $comp_arr[$row[csf("yarn_composition_id")]]; ?>" readonly/>
                </td>
               <td>
                    <input name="txtitemcolor_<?php echo $counter; ?>" id="txtitemcolor_<?php echo $counter; ?>" class="text_boxes" type="text"  style="width:50px" value="<?php echo $color_arr[$row[csf("yd_color_id")]]; ?>" readonly/>
                </td>
                <td>
                    <input name="txtnoofbag_<?php echo $counter; ?>" id="txtnoofbag_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:40px" value="<?php echo $row[csf("no_bag")]; ?>" />
                </td>
                 <td>
                    <input name="txtconeperbag_<?php echo $counter; ?>" id="txtconeperbag_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:40px"  value="<?php echo $row[csf("cone_per_bag")]; ?>"  />
                </td>
                 <td>
                    <input name="txtnoofcone_<?php echo $counter; ?>" id="txtnoofcone_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:50px" value="<?php echo $row[csf("no_cone")]; ?>" />
                </td>
                <td>
                    <input name="txtavgwtpercone_<?php echo $counter; ?>" id="txtavgwtpercone_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:70px" value="<?php echo $row[csf("avg_wgt")]; ?>" readonly/>
                </td>
                <td>
				<?php echo create_drop_down( "cbouom_".$counter, 50, $unit_of_measurement,"", 1, "-- Select --",$uomId,"", 1,'','','','','','',"cboUom[]"); ?>
                 </td>
                <td>
                    <input name="txtissuequantity_<?php echo $counter; ?>" id="txtissuequantity_<?php echo $counter; ?>" style="width:70px"  class="text_boxes_numeric" type="text" onKeyUp="check_iss_qty_ability(this.value,<? echo $counter; ?>); fnc_total_calculate();"   value="<?php //echo number_format($balance_qty,2,".","") ; ?>"  placeholder="<? echo number_format($balance_qty,2,".","")  ;?>"   pre_issue_qty="<? echo number_format($pre_issue_qty,2,".","")  ; ?>" rec_qty="<? echo number_format($rec_qty,2,".",""); ?>" />
                </td>
                 </tr>
    	
			<?php
				$counter++;
	    }
    ?>
    
	    </tr>

    <?php
  exit();
}

if($action == 'load_php_mst_data_to_form') {
	
	

	
		$sql = "select a.id, a.company_id, a.order_no, a.location_id, a.within_group, a.party_id, a.receive_date, a.yd_job, a.booking_type, a.booking_without_order, b.receive_quantity
		from yd_ord_mst a, yd_material_mst b
		where a.yd_job='$data' and b.yd_job_id=a.id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	
	/*echo $sql;die;*/
	$data_array = sql_select($sql);
    unset($sql);

    // echo 'location id: '.$data_array[0][csf('location_id')];die;

    /*echo "document.getElementById('cbo_company_name').value = '".$data_array[0][csf('company_id')]."';\n";
    echo "document.getElementById('cbo_within_group').value = '".$data_array[0][csf('within_group')]."';\n";
    echo "load_drop_down('requires/yd_material_receive_controller', document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );";
    echo "document.getElementById('cbo_party_name').value = '".$data_array[0][csf('party_id')]."';\n";
    echo "document.getElementById('cbo_location_name').value = '".$data_array[0][csf('location_id')]."';\n";
    echo "document.getElementById('txt_receive_challan').value = '".$data_array[0][csf('chalan_no')]."';\n";
    echo "document.getElementById('txt_receive_no').value = '".$data_array[0][csf('yd_trans_no')]."';\n";
    echo "document.getElementById('txt_receive_date').value = '".change_date_format($data_array[0][csf('receive_date')], "dd-mm-yyyy", "-")."';\n";
    echo "document.getElementById('txt_job_no').value = '".$data_array[0][csf('yd_job')]."';\n";
    echo "document.getElementById('hdn_job_no_id').value = '".$data_array[0][csf('id')]."';\n";*/
    echo "document.getElementById('txt_receive_quantity').value = '".number_format($data_array[0][csf('receive_quantity')],2,".","")."';\n";
    echo "document.getElementById('hdn_booking_type_id').value = '".$data_array[0][csf('booking_type')]."';\n";
    echo "document.getElementById('hdn_booking_without_order').value = '".$data_array[0][csf('booking_without_order')]."';\n";

    exit();
}


if($action=="load_php_dtls_form_aftersave")
{  $exdata=explode("**",$data);
	$mst_id=''; $update_id=0;
	$update_id=$exdata[0];
	$mst_id=$exdata[0];

	//echo $data;die;
	/*$sql = "select a.company_id,a.location_id, a.within_group, a.party_id, a.receive_date, a.yd_trans_no,b.id as po_id, b.id,b.quantity, uom, b.job_dtls_id,b.buyer_po_id,b.color_id,b.rec_cone
	from yd_material_mst a, yd_material_dtls b
	where a.yd_trans_no='$jobno' and a.id = b.mst_id";*/

	$sql = "select a.company_id, a.order_no, a.location_id, a.within_group, a.party_id, a.receive_date, a.yd_job, b.id, b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.no_cone, b.avg_wgt, b.order_quantity, c.receive_qty, c.id as update_dtls_id, b.sales_order_no,b.uom
	from yd_ord_mst a, yd_ord_dtls b, yd_material_dtls c
	where c.mst_id='$mst_id' and a.id=b.mst_id and b.id = c.job_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	
	//echo $sql;die;
	$data_array = sql_select($sql);
	
	
	
	
	
	  $sql_iss="select a.id, a.mst_id, a.receive_qty, a.uom, a.job_dtls_id  from yd_material_dtls a, yd_material_mst b where b.id=a.mst_id  and b.entry_form=388  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_iss_res =sql_select($sql_iss);
	
	foreach ($sql_iss_res as $row)
	{
		if($row[csf("mst_id")]==$update_id)
		{
			$updtls_data_arr[$row[csf("job_dtls_id")]]['dtlsid']=$row[csf("id")];
			$updtls_data_arr[$row[csf("job_dtls_id")]]['qty']=$row[csf("receive_qty")];
		}
		else
		{
			$pre_qty_arr[$row[csf("job_dtls_id")]]['qty']+=$row[csf("receive_qty")];
		}
	}
	
	$rec_data_arr=array();
	  $sql_rec="select a.id, a.mst_id, a.receive_qty, a.uom, a.job_dtls_id  from yd_material_dtls a, yd_material_mst b where b.id=a.mst_id  and b.entry_form=387 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_rec_res =sql_select($sql_rec);
	foreach ($sql_rec_res as $row)
	{
		
		$rec_data_arr[$row[csf("job_dtls_id")]]['qty']+=$row[csf("receive_qty")];
	}
	unset($sql_rec_res);
	
	
	//print_r($data_array);die;
	$count_arr = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1", 'id', 'yarn_count');
	//$count_arr = return_library_array("select id,construction from lib_yarn_count_determina_mst where is_deleted=0 and status_active=1", 'id', 'construction');
    $comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
    $color_arr = return_library_array("select id,color_name from lib_color where is_deleted=0 and status_active=1", 'id', 'color_name');
    $yarn_arr = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1", 'id', 'yarn_count');
   
    $counter = 1;

    foreach ($data_array as $row)
	 {
		 
		 $uomId=$row[csf("uom")];
		 
		 $qty=0;  $balance_qty=0; $rec_qty=0; $pre_issue_qty=0;
		$rec_qty=$rec_data_arr[$row[csf("id")]]['qty'];
		$pre_issue_qty=$pre_qty_arr[$row[csf("id")]]['qty'];
		
		//echo $rec_qty."dfdf".$pre_issue_qty; 
		
		$balance_qty=number_format($rec_qty-$pre_issue_qty,4,'.','');
		if($update_id!=0)
		{
			$qty=$updtls_data_arr[$row[csf("id")]]['qty'];
		}
		else $qty=$balance_qty;
    	?> <tr> 

                     <td><input type="hidden" name="ordernoid_<? echo $counter; ?>" id="ordernoid_<? echo $counter; ?>" value="<? echo $row[csf("update_dtls_id")]; ?>">
                         <input type="hidden" name="jobno_<? echo $counter; ?>" id="jobno_<? echo $counter; ?>" value="<? echo $row[csf("yd_job")]; ?>">
                         <input type="hidden" name="colorid_<? echo $counter; ?>" id="colorid_<? echo $counter; ?>" value="<? echo $row[csf("yd_color_id")]; ?>">
                         
                          
                          <input name="txtbuyerPoId_<? echo $counter; ?>" id="txtbuyerPoId_<? echo $counter; ?>" type="hidden" class="text_boxes" style="width:70px" value="<? echo $row[csf("ord_id")]; ?>" /> 
                          <input type="hidden" name="updatedtlsid_<? echo $counter; ?>" id="updatedtlsid_<? echo $counter; ?>" value="<? echo $row[csf("update_dtls_id")]; ?>">
                        
                         <input name="txtstyle_<?php echo $counter; ?>" id="txtstyle_<?php echo $counter; ?>" class="text_boxes" type="text"  style="width:70px" value="<?php echo $row[csf("style_ref")]; ?>" readonly/>
                         
                     </td>
                     
                    <td>
                         <input type="text" id="txtsalesorderno_<?php echo $counter; ?>" name="txtsalesorderno_" class="text_boxes" style="width:120px" value="<?php echo $row[csf("sales_order_no")]; ?>" readonly>
                    </td>
                    <td>
                         <input type="text" id="txtlot_<?php echo $counter; ?>" name="txtlot_<?php echo $counter; ?>" class="text_boxes" style="width:40px" value="<?php echo $row[csf("lot")]; ?>" readonly>
                    </td>

                    <td>
                         <input type="text" id="txtcount_<?php echo $counter; ?>" name="txtcount_<?php echo $counter; ?>" class="text_boxes" style="width:50px" value="<?php echo $count_arr[$row[csf("count_id")]]; ?>" readonly>
                    </td>
                    <td>
                         <input type="text" id="txtyarntype_<?php echo $counter; ?>" name="txtyarntype_<?php echo $counter; ?>" class="text_boxes" style="width:50px" value="<?php echo $yarn_type[$row[csf("yarn_type_id")]]; ?>" readonly>
                    </td>
                    <td>
                        <input name="txtyarncomposition_<?php echo $counter; ?>" id="txtyarncomposition_<?php echo $counter; ?>" class="text_boxes" type="text"  style="width:60px" value="<?php echo $comp_arr[$row[csf("yarn_composition_id")]]; ?>" readonly/>
                    </td>
                   <td>
                        <input name="txtitemcolor_<?php echo $counter; ?>" id="txtitemcolor_<?php echo $counter; ?>" class="text_boxes" type="text"  style="width:50px" value="<?php echo $color_arr[$row[csf("yd_color_id")]]; ?>" readonly/>
                    </td>
                    <td>
                        <input name="txtnoofbag_<?php echo $counter; ?>" id="txtnoofbag_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:40px" value="<?php echo $row[csf("no_bag")]; ?>" />
                    </td>
                     <td>
                        <input name="txtconeperbag_<?php echo $counter; ?>" id="txtconeperbag_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:40px"  value="<?php echo $row[csf("cone_per_bag")]; ?>" />
                    </td>
                     <td>
                        <input name="txtnoofcone_<?php echo $counter; ?>" id="txtnoofcone_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:50px" value="<?php echo $row[csf("no_cone")]; ?>" />
                    </td>
                    <td>
                        <input name="txtavgwtpercone_<?php echo $counter; ?>" id="txtavgwtpercone_<?php echo $counter; ?>" class="text_boxes_numeric" type="text"  style="width:70px" value="<?php echo $row[csf("avg_wgt")]; ?>" readonly/>
                    </td>
                    <td>     <?php echo create_drop_down( "cbouom_".$counter, 50, $unit_of_measurement,"", 1, "-- Select --",$uomId,"", 1,'','','','','','',"cboUom[]"); ?>
                       
                    </td>
                    <td>
                        <input name="txtissuequantity_<?php echo $counter; ?>" id="txtissuequantity_<?php echo $counter; ?>" class="text_boxes_numeric" type="text" onKeyUp="check_iss_qty_ability(this.value,<? echo $counter; ?>); fnc_total_calculate();"   value="<?php echo number_format($qty,2,".",""); ?>"  placeholder="<? echo number_format($balance_qty,2,".","");?>"   pre_issue_qty="<? echo number_format($pre_issue_qty,2,".",""); ?>" rec_qty="<? echo  number_format($rec_qty,2,".",""); ?>"    style="width:70px" />
                    </td> 
                </tr>
    	
			<?php
				$counter++;
	    }
    ?>
    
	    </tr>

    <?php
  exit();
}

if($action=="yd_material_issue_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$comp_arr = return_library_array("select id,composition_name from lib_composition_array where is_deleted=0 and status_active=1", 'id', 'composition_name');
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$count_arr = return_library_array("select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1", 'id', 'yarn_count');
	$dataArray=sql_select( "SELECT id, yd_trans_no, embl_job_no, company_id, receive_quantity, location_id, party_id, receive_date,receive_quantity, chalan_no, within_group from yd_material_mst where id=$data[1] and entry_form=388 and is_deleted=0 and  status_active=1");

	$sql_dtls = "SELECT a.company_id, a.order_no, a.location_id, a.within_group, a.party_id, a.receive_date, a.yd_job, b.id, b.style_ref, b.lot, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.yd_color_id, b.no_bag, b.cone_per_bag, b.no_cone, b.avg_wgt, b.order_quantity, c.receive_qty, c.id as update_dtls_id, b.sales_order_no, b.uom, b.item_color_id,b.buyer_buyer
	from yd_ord_mst a, yd_ord_dtls b, yd_material_dtls c
	where c.mst_id=$data[1] and a.id=b.mst_id and b.id = c.job_dtls_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
	$sql_arr=sql_select($sql_dtls);

	$style=$sql_arr[0]["STYLE_REF"];
	$sales_order_no=$sql_arr[0]["SALES_ORDER_NO"];
	$buyer_buyer=$sql_arr[0]["BUYER_BUYER"];
	$order_no=$sql_arr[0]["ORDER_NO"];

	$company = $data[0];
	$issue_id = $data[1];
	$system_no = $data[3];

	if($data[3]==1){
		$party=$company_library[$dataArray[0][csf('party_id')]];
	}else{
		$party=$party_arr[$dataArray[0][csf('party_id')]];
	}

	
	?> 
    
    <div style="width:1020px; font-size:6px">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right" style="display: none;"> 
                    <img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><strong> <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:14px"><strong><u><? echo $data[2]; ?></u></strong></td>
                        </tr>
						<tr>
                            <td align="center" style="font-size:15px"><strong><u>Issue Id: <? echo $dataArray[0]["YD_TRANS_NO"]; ?></u></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>   
          <br>
        <table width="100%" cellpadding="0" cellspacing="0" >  
            <tr>
            	<td width="130"><strong>Challan No:</strong></td>
                <td width="175"><? echo $dataArray[0][csf('chalan_no')]; ?></td>
				<td width="150"><strong>Cust. Buyer:</strong></td>
                <td width="175"><? echo $buyer_buyer; ?></td>
                <td width="130"><strong>WO No:</strong></td>
                <td><? echo $order_no; ?></td>
            </tr>
            <tr>
            	<td><strong>Party: </strong></td>
                <td ><? echo $party; ?></td>
                <td ><strong>Issue Date: </strong></td>
                <td ><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
            	<td><strong>Job: </strong></td>
                <td><? echo $dataArray[0][csf('embl_job_no')]; ?></td>
            </tr>
            <tr>
            	<td width="130"><strong>Style:</strong></td>
                <td width="175"><? echo $style; ?></td>
            	<td><strong>Job/Sales order no: </strong></td>
                <td><? echo $sales_order_no; ?></td>              
            </tr>
        </table>
        <br>
        
            <table cellspacing="0" width="1200" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center">
                    <th width="30">SL</th>
                    <th width="90">Order</th>
                    <th width="100">Cust Buyer</th>
                    <th width="90">Job/Sales Order</th>
                    <th width="100">Yarn Composition</th>
                    <th width="90">Yarn Type</th>
                    <th width="120">Item Color</th>
                    <th width="70">Color Range</th>
                    <th width="80">Count</th>
                    <th width="80">Raw Yarn Lot</th>
                    <th width="60">No of Bag</th>
                    <th width="60">Cone Per Bag</th>
                    <th width="60">No of Cone</th>
                    <th width="60">UOM</th>
                    <th width="60">AVG. Wt. Per Cone</th>
                    <th width="60">Issue Qty.</th>
                    <th>Remarks</th>
                </thead>
				<?
				$color_wish_array=array();
				foreach($sql_arr as $row){
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["ORDER_NO"]=$row["ORDER_NO"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["SALES_ORDER_NO"]=$row["SALES_ORDER_NO"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["YARN_COMPOSITION_ID"]=$row["YARN_COMPOSITION_ID"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["YARN_TYPE_ID"]=$row["YARN_TYPE_ID"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["ORDER_QUANTITY"]+=$row["ORDER_QUANTITY"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["RECEIVE_QTY"]+=$row["RECEIVE_QTY"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["AVG_WGT"]=$row["AVG_WGT"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["NO_CONE"]=$row["NO_CONE"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["NO_BAG"]=$row["NO_BAG"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["YD_COLOR_ID"]=$row["YD_COLOR_ID"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["UOM"]=$row["UOM"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["LOT"]=$row["LOT"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["CONE_PER_BAG"]=$row["CONE_PER_BAG"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["COUNT_ID"]=$row["COUNT_ID"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["ITEM_COLOR_ID"]=$row["ITEM_COLOR_ID"];
					$color_wish_array[$row["YD_COLOR_ID"]][$row["LOT"]][$row["COUNT_ID"]]["BUYER_BUYER"]=$row["BUYER_BUYER"];
				}
				unset($sql_arr); 

 				$i=1;$total_qty=0;
				foreach ($color_wish_array as $yd_color_id=> $lot_arr) 
				{			
					foreach($lot_arr as $lot_data=> $count_arrs)
					{	
						foreach($count_arrs as $key=> $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td><? echo $i; ?></td>
								<td style="word-break:break-all"><? echo $row["ORDER_NO"]; ?></td>
								<td style="word-break:break-all"><? echo $row["BUYER_BUYER"] ?></td>
								<td style="word-break:break-all"><? echo $row["SALES_ORDER_NO"]; ?></td>
								<td style="word-break:break-all"><? echo $comp_arr[$row["YARN_COMPOSITION_ID"]]; ?></td>
								<td style="word-break:break-all"><? echo $yarn_type[$row['YARN_TYPE_ID']]; ?></td>
								<td style="word-break:break-all"><? echo $color_arr[$row['YD_COLOR_ID']]; ?>&nbsp;</td>
								<td style="word-break:break-all"><? echo $color_range[$row['ITEM_COLOR_ID']]; ?>&nbsp;</td>
								<td style="word-break:break-all"><? echo $count_arr[$row['COUNT_ID']]; ?>&nbsp;</td>
								<td style="word-break:break-all" align="center"><? echo $row['LOT']; ?>&nbsp;</td>
								<td align="right"><? echo $row['NO_BAG']; ?>&nbsp;</td>
								<td align="right"><? echo $row['CONE_PER_BAG']; ?>&nbsp;</td>
								<td align="right"><? echo $row['NO_CONE']; ?>&nbsp;</td>
								<td align="right"><? echo $unit_of_measurement[$row['UOM']]; ?>&nbsp;</td>
								<td align="right"><? echo $row['AVG_WGT']; ?>&nbsp;</td>
								<td align="right"><? echo number_format($row['RECEIVE_QTY'],2,".",""); ?>&nbsp;</td>
								<td style="word-break:break-all"><? echo $row['remarks']; ?>&nbsp;</td>
							</tr>
							<? $i++;	
							$total_qty+=$row['RECEIVE_QTY'];
						}
				    }		
			   }		
				?>
				<tr>
					<td colspan="15" align="right"><b> Total</b></td>
					<td align="right" ><?=number_format($total_qty,2,".","")?></td>
					<td></td>
				</tr>
            </table>
            <br>
			<?// echo signature_table(154, $com_id, "1200px"); ?>
         </div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
			//for gate pass barcode
			function generateBarcodeGatePass(valuess)
			{
				//var zs = '<?php // echo $x; ?>';
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer = 'bmp';// $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#gate_pass_barcode_img_id_1").html('11');
				value = {code: value, rect: false};
				$("#gate_pass_barcode_img_id_1").show().barcode(value, btype, settings);
			}
			var value = '<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>';
			
			if( value != '')
			{
				generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
			}
		</script>
        <div style="page-break-after:always;"></div>
        <?php
	exit();
}
?>