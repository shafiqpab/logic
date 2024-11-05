<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

//---------------------------------------------------- Start
if ($action=="load_drop_down_buyer")
{
	if($data != 0) $comCond="and b.tag_company=$data"; else  $comCond="";
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $comCond $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
	exit();
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	// echo "10**=A=".$operation;die;
	if($operation==0)  // Insert Here
	{
		$con = connect();
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
	
		$field_array="id, job_no_mst, booking_no, booking_id, app_type, color_name_id, lapdip_target_approval_date, send_to_factory_date, plandeliverydate, recv_from_factory_date, submitted_to_buyer, approval_status, approval_status_date, lapdip_no, applabdipno, shade_per, lapdip_comments, is_master_color, is_deleted, status_active, entry_form, inserted_by, insert_date, garments_nature"; 
		
		for($i=1; $i<=$tot_row; $i++)
		{
			$cbofabriccolor="cbofabriccolor_".$i;
			$txtgcolorid="txtgcolorid_".$i;
			$txtjobno="txtjobno_".$i;
			$txttargetappdate="txttargetappdate_".$i;
			$txtsendtofactorydate="txtsendtofactorydate_".$i;
			
			$txtplandeliverydate="txtplandeliverydate_".$i;
			$txtrecvfromfactorydate="txtrecvfromfactorydate_".$i;
			$txtsubmittedtobuyer="txtsubmittedtobuyer_".$i;
			$cboaction="cboaction_".$i;
			$txtactiondate="txtactiondate_".$i;
			$txtlapdipno="txtlapdipno_".$i;
			$txtapplabdipno="txtapplabdipno_".$i;
			$txtapplabdipno="txtapplabdipno_".$i;
			$txtshadeper="txtshadeper_".$i;
			$txtcomments="txtcomments_".$i;
			$cbostatus="cbostatus_".$i;
			$updateid="updateid_".$i;
			
			if(str_replace("'",'',$$txttargetappdate)!="") $txttargetappdate=date("j-M-Y",strtotime(str_replace("'",'',$$txttargetappdate))); else $txttargetappdate="";
			if(str_replace("'",'',$$txtsendtofactorydate)!="") $txtsendtofactorydate=date("j-M-Y",strtotime(str_replace("'",'',$$txtsendtofactorydate))); else $txtsendtofactorydate="";
			if(str_replace("'",'',$$txtplandeliverydate)!="") $txtplandeliverydate=date("j-M-Y",strtotime(str_replace("'",'',$$txtplandeliverydate))); else $txtplandeliverydate="";
			if(str_replace("'",'',$$txtrecvfromfactorydate)!="") $txtrecvfromfactorydate=date("j-M-Y",strtotime(str_replace("'",'',$$txtrecvfromfactorydate))); else $txtrecvfromfactorydate="";
			if(str_replace("'",'',$$txtsubmittedtobuyer)!="") $txtsubmittedtobuyer=date("j-M-Y",strtotime(str_replace("'",'',$$txtsubmittedtobuyer))); else $txtsubmittedtobuyer="";
			if(str_replace("'",'',$$txtactiondate)!="") $txtactiondate=date("j-M-Y",strtotime(str_replace("'",'',$$txtactiondate))); else $txtactiondate="";
			
			/*//$color_id_ec_chk="color_id_ec_".$color_id."_".$i;
			$color_id_ec_chk="color_id_ec_".$i;
			
			if($color_id=='ec') 
			{
				
				 	// Issue Id 24209 for Charka
					if(str_replace("'","",$$color_id_ec_chk)!="")
					{
						if (!in_array(str_replace("'","",$$color_id_ec_chk),$new_array_color))
						{
							$color_new_id = return_id( str_replace("'","",$$color_id_ec_chk), $color_arr, "lib_color", "id,color_name","79");
							$new_array_color[$color_new_id]=str_replace("'","",$$color_id_ec_chk);
						}
						else $color_new_id =  array_search(str_replace("'","",$$color_id_ec_chk), $new_array_color);
					}
					else $color_new_id =0;
				
				$color_lib_id=$color_new_id;
				$is_master_color=0;
			}
			else
			{
				$color_lib_id=$$color_name_id;
				$is_master_color=1;
			}	*/

			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$$txtjobno.",".$txt_booking_no.",".$txt_booking_id.",".$txt_app_type.",".$$cbofabriccolor.",'".$txttargetappdate."','".$txtsendtofactorydate."','".$txtplandeliverydate."','".$txtrecvfromfactorydate."','".$txtsubmittedtobuyer."',".$$cboaction.",'".$txtactiondate."',".$$txtlapdipno.",".$$txtapplabdipno.",".$$txtshadeper.",".$$txtcomments.",1,0,".$$cbostatus.",584,".$user_id.",'".$pc_date_time."',".$garments_nature.")";
			
			$id=$id+1;
		}
		// echo "10**insert into wo_po_lapdip_approval_info (".$field_array.") values ".$data_array;die;
		$rID=sql_insert("wo_po_lapdip_approval_info",$field_array,$data_array,1);
		 
		if($db_type==2 || $db_type==1 )
		{
			if($rID==1)
			{
				oci_commit($con);   
				echo "0**".$txt_booking_no."**".$txt_booking_id."**".$txt_req_id."**".$txt_app_type;
			}
			else{
				oci_rollback($con);
				echo "5**".$txt_booking_no."**".$txt_booking_id."**".$txt_req_id."**".$txt_app_type;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "wo_po_lapdip_approval_info", 1);
		$data_array="";
		
		$field_array="id, job_no_mst, booking_no, booking_id, requisition_id, app_type, color_name_id, lapdip_target_approval_date, send_to_factory_date, plandeliverydate, recv_from_factory_date, submitted_to_buyer, approval_status, approval_status_date, lapdip_no, applabdipno, shade_per, lapdip_comments, is_master_color, is_deleted, status_active, entry_form, inserted_by, insert_date, garments_nature";
		$field_array_update="job_no_mst*booking_no*booking_id*requisition_id*app_type*color_name_id*lapdip_target_approval_date*send_to_factory_date*plandeliverydate*recv_from_factory_date*submitted_to_buyer*approval_status*approval_status_date*lapdip_no*applabdipno*shade_per*lapdip_comments*is_deleted*status_active*updated_by*update_date*garments_nature";

		for($i=1; $i<=$tot_row; $i++)
		{
			$cbofabriccolor="cbofabriccolor_".$i;
			$txtgcolorid="txtgcolorid_".$i;
			$txtjobno="txtjobno_".$i;
			$txttargetappdate="txttargetappdate_".$i;
			$txtsendtofactorydate="txtsendtofactorydate_".$i;
			$txtplandeliverydate="txtplandeliverydate_".$i;
			$txtrecvfromfactorydate="txtrecvfromfactorydate_".$i;
			$txtsubmittedtobuyer="txtsubmittedtobuyer_".$i;
			$cboaction="cboaction_".$i;
			$txtactiondate="txtactiondate_".$i;
			$txtlapdipno="txtlapdipno_".$i;
			$txtapplabdipno="txtapplabdipno_".$i;
			$txtshadeper="txtshadeper_".$i;
			$txtcomments="txtcomments_".$i;
			$cbostatus="cbostatus_".$i;
			$updateid="updateid_".$i;
			
			if(str_replace("'",'',$$txttargetappdate)!="") $txttargetappdate=date("j-M-Y",strtotime(str_replace("'",'',$$txttargetappdate))); else $txttargetappdate="";
			if(str_replace("'",'',$$txtsendtofactorydate)!="") $txtsendtofactorydate=date("j-M-Y",strtotime(str_replace("'",'',$$txtsendtofactorydate))); else $txtsendtofactorydate="";
			if(str_replace("'",'',$$txtplandeliverydate)!="") $txtplandeliverydate=date("j-M-Y",strtotime(str_replace("'",'',$$txtplandeliverydate))); else $txtplandeliverydate="";
			if(str_replace("'",'',$$txtrecvfromfactorydate)!="") $txtrecvfromfactorydate=date("j-M-Y",strtotime(str_replace("'",'',$$txtrecvfromfactorydate))); else $txtrecvfromfactorydate="";
			if(str_replace("'",'',$$txtsubmittedtobuyer)!="") $txtsubmittedtobuyer=date("j-M-Y",strtotime(str_replace("'",'',$$txtsubmittedtobuyer))); else $txtsubmittedtobuyer="";
			if(str_replace("'",'',$$txtactiondate)!="") $txtactiondate=date("j-M-Y",strtotime(str_replace("'",'',$$txtactiondate))); else $txtactiondate="";
			//$txt_reporting_hour="to_date('".$txttargetappdate."','DD MONTH YYYY')";

			if(str_replace("'",'',$$updateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_update[str_replace("'",'',$$updateid)] = explode(",",("".$$txtjobno.",".$txt_booking_no.",".$txt_booking_id.",".$txt_req_id.",".$txt_app_type.",".$$cbofabriccolor.",'".$txttargetappdate."','".$txtsendtofactorydate."','".$txtplandeliverydate."','".$txtrecvfromfactorydate."','".$txtsubmittedtobuyer."',".$$cboaction.",'".$txtactiondate."',".$$txtlapdipno.",".$$txtapplabdipno.",".$$txtshadeper.",".$$txtcomments.",0,".$$cbostatus.",".$user_id.",'".$pc_date_time."',".$garments_nature.""));
			}
			else
			{
				if ($i!=1) $data_array .=",";
				$data_array .="(".$id.",".$$txtjobno.",".$txt_booking_no.",".$txt_booking_id.",".$txt_req_id.",".$txt_app_type.",".$$cbofabriccolor.",'".$txttargetappdate."','".$txtsendtofactorydate."','".$txtplandeliverydate."','".$txtrecvfromfactorydate."','".$txtsubmittedtobuyer."',".$$cboaction.",'".$txtactiondate."',".$$txtlapdipno.",".$$txtapplabdipno.",".$$txtshadeper.",".$$txtcomments.",1,0,".$$cbostatus.",584,".$user_id.",'".$pc_date_time."',".$garments_nature.")";
			
				$id=$id+1;
			 }
		}
		
		$flag=$rID=$rID2=1;
		if($data_array_update!="")
		{
			//echo "10**".bulk_update_sql_statement( "wo_po_lapdip_approval_info", "id", $field_array_update, $data_array_update, $id_arr ); die;
			$rID=execute_query(bulk_update_sql_statement( "wo_po_lapdip_approval_info", "id", $field_array_update, $data_array_update, $id_arr ),1);
			if($rID) $flag=1; else $flag=0;
		}
		if($data_array!="")
		{
			$rID2=sql_insert("wo_po_lapdip_approval_info",$field_array,$data_array,1);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}
          
		/*if($current_status!="")
		{
			$field_array_status="updated_by*update_date*current_status";
			$data_array_status=$user_id."*'".$pc_date_time."'*0";
	
			$rID3=sql_multirow_update("wo_po_lapdip_approval_info",$field_array_status,$data_array_status,"id",$current_status,1);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
		}*/
		//echo "10**".$rID.'='.$rID2.'='.$flag; die;
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".$txt_booking_no."**".$txt_booking_id."**".$txt_req_id."**".$txt_app_type;
			}
			else
			{
				oci_rollback($con);
				echo "6**".$txt_booking_no."**".$txt_booking_id."**".$txt_req_id."**".$txt_app_type;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)//Delete here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array=$user_id."*'".$pc_date_time."'*0*1";
 
		$rID=sql_delete("wo_po_lapdip_approval_info",$field_array,$data_array,"booking_no*color_name_id","".$txt_booking_no.""."*".$color_id,1);
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".$txt_job_no."**".$txt_booking_id."**".$txt_req_id."**".$txt_app_type;
			}
			else
			{
				oci_rollback($con);
				echo "7**".$txt_job_no."**".$txt_booking_id."**".$txt_req_id."**".$txt_app_type;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==5)  // For Deny Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		 $id=return_next_id( "id", "wo_po_lapdip_approval_info", 1);
		$data_array="";
		
		$field_array="id, job_no_mst, booking_no, booking_id, requisition_id, app_type, color_name_id, lapdip_target_approval_date, send_to_factory_date, plandeliverydate, recv_from_factory_date, submitted_to_buyer, approval_status, approval_status_date, lapdip_no, applabdipno, shade_per, lapdip_comments,deny_comments, is_master_color, is_deleted, status_active, entry_form, inserted_by, insert_date, garments_nature";
		$field_array_update="deny_comments*is_deleted*status_active*updated_by*update_date";

		for($i=1; $i<=$tot_row; $i++)
		{
		 
			$txtdenycomments="txtdenycomments_".$i;
			$cbofabriccolor="cbofabriccolor_".$i;
			$txtgcolorid="txtgcolorid_".$i;
			$txtjobno="txtjobno_".$i;
			$txttargetappdate="txttargetappdate_".$i;
			$txtsendtofactorydate="txtsendtofactorydate_".$i;
			$txtplandeliverydate="txtplandeliverydate_".$i;
			$txtrecvfromfactorydate="txtrecvfromfactorydate_".$i;
			$txtsubmittedtobuyer="txtsubmittedtobuyer_".$i;
			$cboaction="cboaction_".$i;
			$txtactiondate="txtactiondate_".$i;
			$txtlapdipno="txtlapdipno_".$i;
			$txtapplabdipno="txtapplabdipno_".$i;
			$txtshadeper="txtshadeper_".$i;
			$txtcomments="txtcomments_".$i;
			$cbostatus="cbostatus_".$i;
			$updateid="updateid_".$i;
			
			if(str_replace("'",'',$$txttargetappdate)!="") $txttargetappdate=date("j-M-Y",strtotime(str_replace("'",'',$$txttargetappdate))); else $txttargetappdate="";
			if(str_replace("'",'',$$txtsendtofactorydate)!="") $txtsendtofactorydate=date("j-M-Y",strtotime(str_replace("'",'',$$txtsendtofactorydate))); else $txtsendtofactorydate="";
			if(str_replace("'",'',$$txtplandeliverydate)!="") $txtplandeliverydate=date("j-M-Y",strtotime(str_replace("'",'',$$txtplandeliverydate))); else $txtplandeliverydate="";
			if(str_replace("'",'',$$txtrecvfromfactorydate)!="") $txtrecvfromfactorydate=date("j-M-Y",strtotime(str_replace("'",'',$$txtrecvfromfactorydate))); else $txtrecvfromfactorydate="";
			if(str_replace("'",'',$$txtsubmittedtobuyer)!="") $txtsubmittedtobuyer=date("j-M-Y",strtotime(str_replace("'",'',$$txtsubmittedtobuyer))); else $txtsubmittedtobuyer="";
			if(str_replace("'",'',$$txtactiondate)!="") $txtactiondate=date("j-M-Y",strtotime(str_replace("'",'',$$txtactiondate))); else $txtactiondate="";


			if(str_replace("'",'',$$updateid)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_update[str_replace("'",'',$$updateid)] = explode(",",("".$$txtdenycomments.",0,".$$cbostatus.",".$user_id.",'".$pc_date_time."'"));
			}
			else
			{
				if ($i!=1) $data_array .=",";
				$data_array .="(".$id.",".$$txtjobno.",".$txt_booking_no.",".$txt_booking_id.",".$txt_req_id.",".$txt_app_type.",".$$cbofabriccolor.",'".$txttargetappdate."','".$txtsendtofactorydate."','".$txtplandeliverydate."','".$txtrecvfromfactorydate."','".$txtsubmittedtobuyer."',".$$cboaction.",'".$txtactiondate."',".$$txtlapdipno.",".$$txtapplabdipno.",".$$txtshadeper.",".$$txtcomments.",".$$txtdenycomments.",1,0,".$$cbostatus.",584,".$user_id.",'".$pc_date_time."',".$garments_nature.")";
			
				$id=$id+1;
			 }
			 
		}
		//echo "10**=";
		//print_r($data_array_update);die;
		$flag=$rID=$rID2=$rID3=1;
		if($data_array_update!="")
		{
			// echo "10**".bulk_update_sql_statement( "wo_po_lapdip_approval_info", "id", $field_array_update, $data_array_update, $id_arr ); die;
			$rID=execute_query(bulk_update_sql_statement( "wo_po_lapdip_approval_info", "id", $field_array_update, $data_array_update, $id_arr ),1);
			if($rID) $flag=1; else $flag=0;
				 
		}
		if($data_array!="")
		{
			$rID2=sql_insert("wo_po_lapdip_approval_info",$field_array,$data_array,1);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
		}

		$req_id=str_replace("'",'',$txt_req_id);
		if($flag==1)
		{
			$rID3=execute_query("update sample_development_mst set req_ready_to_approved=2 where id ='".$req_id."'",1);
			if($rID3) $flag=1; else $flag=0;
		}
		 //txt_req_id
		  //echo "10**".$rID.'='.$rID2.'='.$rID3.'='.$flag; die;
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".$txt_booking_no."**".$txt_booking_id."**".$txt_req_id."**".$txt_app_type;
			}
			else
			{
				oci_rollback($con);
				echo "6**".$txt_booking_no."**".$txt_booking_id."**".$txt_req_id."**".$txt_app_type;
			}
		}
		disconnect($con);
		die;
	}

}
 
if($action=="order_popup")
{
  	echo load_html_head_contents("Lapdip Approval Info","../../../", 1, 1, '','','');
	$garments_nature=$_REQUEST['garments_nature'];
?>
     
	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
	function js_set_value( str )
	{
		var data = str.split("_");
		if($('#cbo_type').val()==1)
		{
			document.getElementById('selected_booking_id').value=data[2];
			document.getElementById('selected_job_no').value=data[1];
			parent.emailwindow.hide();
		}
		else if($('#cbo_type').val()==2)
		{
			document.getElementById('selected_req_id').value=data[2];
			parent.emailwindow.hide();
		}
	}
	
	function fnc_search_by(val)
	{
		if(val==1)
		{
			$('#txt_req_no').val('');
			$('#txt_req_no').attr('disabled', true);
			$('#txt_job_prifix').attr('disabled', false);
			$('#txt_order_search').attr('disabled', false);
			$('#txt_booking_no').attr('disabled', false);
		}
		else if (val==2)
		{
			$('#txt_job_prifix').val('');
			$('#txt_order_search').val('');
			$('#txt_booking_no').val('');
			$('#txt_job_prifix').attr('disabled', true);
			$('#txt_order_search').attr('disabled', true);
			$('#txt_req_no').attr('disabled', false);
			$('#txt_booking_no').attr('disabled', true);
		}
		else
		{
			$('#txt_job_prifix').attr('disabled', false);
			$('#txt_order_search').attr('disabled', false);
			$('#txt_req_no').attr('disabled', false);
			$('#txt_booking_no').attr('disabled', false);
		}
	}
	
	function fnc_data_generate()
	{
		$('#cbo_type').attr('disabled', true);
		show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_req_no').value+'_'+document.getElementById('cbo_type').value, 'create_po_search_list_view', 'search_div', 'lapdip_approval_entry_v2_controller', 'setFilterGrid(\'list_view\',-1)');
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="1060" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr align="center">
                    <th colspan="12"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr> 
                <tr>                     	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="172" class="must_entry_caption">Buyer Name</th>
                    <th width="70" class="must_entry_caption">Search Type</th>
                    <th width="70">Job No</th>
                    <th width="80">Style Ref </th>
                    <th width="80">Order No</th>
					<th width="70">Booking No</th>
                    <th width="70">Req. No</th>
                    <th width="70">Internal Ref.</th>
                    <th width="120" colspan="2">Date Range</th>
                    <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>  
                </tr>         
            </thead>
            <tbody>
				<tr class="general">
					<td>
                    	<input type="hidden" id="selected_booking_id">
                        <input type="hidden" id="selected_job_no">
                        <input type="hidden" id="selected_req_id">
                        <? echo create_drop_down( "cbo_company_name", 140, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'lapdip_approval_entry_v2_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
                    </td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --",$selected ); ?></td>
                    <td>
						<?
                        $search_by_arr = array(1 => "Booking", 2 => "Requisition");
                        echo create_drop_down("cbo_type", 70, $search_by_arr, "", 1, "-All-", "", 'fnc_search_by(this.value)', 0);
                        ?>
                    </td>
                    <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes_numeric" style="width:60px"></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:70px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:70px"></td>
					<td><input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:60px"></td>
                    <td><input name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:60px"></td>
                    <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:60px"></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date"></td> 
                    <td align="center">
                         <input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_data_generate();" style="width:80px;" />
                    </td>
                </tr>
                <tr align="center">
                    <td colspan="12"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>          
        </table>
        <div id="search_div" style="margin-top:5px"></div>
    </form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_po_search_list_view")
{
	$data=explode('_',$data);
	
	if($data[14]==1 || $data[14]==0)// Fabric and all
	{
		if($data[0]==0 && $data[1]==0)
		{
			echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select Company or Buyer first.";
			die;
		}
		if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else $company="";
		
		if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $$buyer="";//{ echo "Please Select Buyer First."; die; }
		if($db_type==0) { $year_cond=" and SUBSTRING_INDEX(a.`insert_date`, '-', 1)=$data[7]"; $insert_year="SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year"; }
		if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";   $insert_year="to_char(a.insert_date,'YYYY') as year";}
		//if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond "; else  $job_cond=""; 
		//if (str_replace("'","",$data[8])!="") $order_cond=" and b.po_number like '%$data[8]%' $year_cond "; else  $order_cond=""; 
		$job_cond=""; $order_cond=""; $style_cond=""; $internalRefCond="";
		if($data[10]==1)
		{
			if (str_replace("'","",$data[6])!="") $job_cond=" and b.job_no_prefix_num='$data[6]' $year_cond"; //else  $job_cond=""; 
			if (str_replace("'","",$data[8])!="") $order_cond=" and c.po_number = '$data[8]'  "; //else  $order_cond=""; 
			if (trim($data[9])!="") $style_cond=" and b.style_ref_no ='$data[9]'"; //else  $style_cond=""; 
			if (trim($data[11])!="") $internalRefCond=" and c.grouping ='$data[11]'"; //else  $style_cond=""; 
		}
		else if($data[10]==2)
		{
			if (str_replace("'","",$data[6])!="") $job_cond=" and b.job_no_prefix_num like '$data[6]%' $year_cond"; //else  $job_cond=""; 
			if (str_replace("'","",$data[8])!="") $order_cond=" and c.po_number like '$data[8]%'  "; //else  $order_cond=""; 
			if (trim($data[9])!="") $style_cond=" and b.style_ref_no like '$data[9]%'  "; //else  $style_cond=""; 
			if (trim($data[11])!="") $internalRefCond=" and c.grouping like '$data[11]%'  "; //else  $style_cond=""; 
		}
		else if($data[10]==3)
		{
			if (str_replace("'","",$data[6])!="") $job_cond=" and b.job_no_prefix_num like '%$data[6]' $year_cond"; //else  $job_cond=""; 
			if (str_replace("'","",$data[8])!="") $order_cond=" and c.po_number like '%$data[8]'  "; //else  $order_cond=""; 
			if (trim($data[9])!="") $style_cond=" and b.style_ref_no like '%$data[9]'"; //else  $style_cond="";
			if (trim($data[11])!="") $internalRefCond=" and c.grouping like '%$data[11]'"; //else  $style_cond=""; 
		}
		else if($data[10]==4 || $data[10]==0)
		{
			if (str_replace("'","",$data[6])!="") $job_cond=" and b.job_no_prefix_num like '%$data[6]%' $year_cond"; //else  $job_cond=""; 
			if (str_replace("'","",$data[8])!="") $order_cond=" and c.po_number like '%$data[8]%'  "; //else  $order_cond=""; 
			if (trim($data[9])!="") $style_cond=" and b.style_ref_no like '%$data[9]%'"; //else  $style_cond="";
			if (trim($data[11])!="") $internalRefCond=" and c.grouping like '%$data[11]%'"; //else  $style_cond=""; 
		}
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and a.insert_date between '".date("j-M-Y",strtotime($data[3]))."' and '".date("j-M-Y",strtotime($data[4]))."'"; else $shipment_date ="";
		
		$job_cond2="";
		if (str_replace("'","",$data[6])!="") $job_cond2=" and job_no>0"; //else  $job_cond=""; 
		if (str_replace("'","",$data[8])!="") $job_cond2=" and job_no>0"; //else  $order_cond=""
		 
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
		$booking_no=trim(str_replace("'","",$data[12]));
		if (str_replace("'","",$data[12])!=""){ $booking_cond="and a.booking_no_prefix_num ='$booking_no'"; $booking_cond2="and a.booking_no_prefix_num='$booking_no'"; }else{ $booking_cond=""; $booking_cond2=""; }; //else  $job_cond="";
		if (str_replace("'","",$data[13])!=""){ $booking_cond2="and c.requisition_number_prefix_num=$data[13]"; }else{ $booking_cond2=""; };
		//echo $booking_cond.'='.$data[2].'='.$data[14];
	
		$arr=array (2=>$comp,3=>$buyer_arr);
		if($data[2]==0)
		{
			if(str_replace("'","",$data[14])==1)
			{
				$sql= "SELECT a.booking_no_prefix_num, a.booking_no, $insert_year, a.company_id, a.buyer_id, a.id, b.style_ref_no, b.job_no_prefix_num, b.job_quantity, c.po_number, c.po_quantity, a.job_no, c.grouping, c.pub_shipment_date as shipment_date, null as style_id
					FROM wo_booking_mst a, wo_po_details_master b, wo_po_break_down c 
					WHERE a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=118   
					$shipment_date $company $buyer $job_cond $style_cond $order_cond $internalRefCond $booking_cond $year_cond
					GROUP BY a.booking_no_prefix_num, a.booking_no, a.insert_date, a.company_id, a.buyer_id, a.id, b.style_ref_no, b.job_no_prefix_num, b.job_quantity, c.po_number, c.po_quantity, a.job_no, c.grouping, c.pub_shipment_date 
				 order by id desc";
			}
			else
			{
				$sql= "SELECT a.booking_no_prefix_num, a.booking_no, $insert_year, a.company_id, a.buyer_id, a.id, b.style_ref_no, b.job_no_prefix_num, b.job_quantity, c.po_number, c.po_quantity, a.job_no, c.grouping, c.pub_shipment_date as shipment_date, null as style_id
					FROM wo_booking_mst a, wo_po_details_master b, wo_po_break_down c 
					WHERE a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=118   
					$shipment_date $company $buyer $job_cond $style_cond $order_cond $internalRefCond $booking_cond $year_cond
					GROUP BY a.booking_no_prefix_num, a.booking_no, a.insert_date, a.company_id, a.buyer_id, a.id, b.style_ref_no, b.job_no_prefix_num, b.job_quantity, c.po_number, c.po_quantity, a.job_no, c.grouping, c.pub_shipment_date 
				UNION ALL
					SELECT a.booking_no_prefix_num, a.booking_no, $insert_year, a.company_id, a.buyer_id, a.id, c.style_ref_no, null as job_no_prefix_num, null as job_quantity, null as po_number, null as po_quantity, null as job_no, c.internal_ref as grouping, null as shipment_date, b.style_id
					FROM wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c
					WHERE a.booking_no=b.booking_no and b.style_id=c.id	and a.entry_form_id = '140' and a.status_active = 1 and a.is_deleted = 0 
					$company $booking_cond2 $buyer $year_cond $booking_cond
					GROUP BY a.booking_no_prefix_num, a.booking_no, a.insert_date, a.company_id, a.buyer_id, a.id, c.style_ref_no, c.internal_ref, b.style_id order by id desc";
			}
			//echo $sql;
			echo create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO No,Booking No,Internal Ref,PO Qty.,Shipment Date", "50,50,120,110,100,70,100,100,80,70,70","1050","240",0, $sql , "js_set_value", "job_no,style_id,id", "", 1, "0,0,company_id,buyer_id,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_id,buyer_id,style_ref_no,job_quantity,po_number,booking_no,grouping,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,0,1,1,3');
		}
		else
		{
			$sql= "select a.job_no_prefix_num, $insert_year, a.job_no, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.garments_nature=$data[5] and a.is_deleted=0 $company $buyer $job_cond order by a.id DESC";
			
			echo create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,", "90,90,150,150,100","880","240",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "",'','0,0,0,0,0,1,0,2,3') ;
		}
	}
	else
	{
		if($data[0]==0 && $data[1]==0)
		{
			echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select Company or Buyer first.";
			die;
		}
		if ($data[0]!=0) $company=" and a.company_id='$data[0]'"; else $company="";
		
		if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $$buyer="";//{ echo "Please Select Buyer First."; die; }
		if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]"; $insert_year="to_char(a.insert_date,'YYYY') as year";}
		$job_cond=""; $order_cond=""; $style_cond=""; $internalRefCond="";
		if($data[10]==1)
		{
			if (str_replace("'","",$data[13])!="") $job_cond=" and a.requisition_number_prefix_num='$data[13]' $year_cond"; //else  $job_cond=""; 
			if (trim($data[9])!="") $style_cond=" and a.style_ref_no ='$data[9]'"; //else  $style_cond=""; 
		}
		else if($data[10]==2)
		{
			if (str_replace("'","",$data[13])!="") $job_cond=" and a.requisition_number_prefix_num like '$data[13]%' $year_cond"; //else  $job_cond=""; 
			if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '$data[9]%'  "; //else  $style_cond=""; 
		}
		else if($data[10]==3)
		{
			if (str_replace("'","",$data[13])!="") $job_cond=" and a.requisition_number_prefix_num like '%$data[13]' $year_cond"; //else  $job_cond=""; 
			if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]'"; //else  $style_cond="";
		}
		else if($data[10]==4 || $data[10]==0)
		{
			if (str_replace("'","",$data[13])!="") $job_cond=" and a.requisition_number_prefix_num like '%$data[13]%' $year_cond"; //else  $job_cond=""; 
			if (trim($data[9])!="") $style_cond=" and a.style_ref_no like '%$data[9]%'"; //else  $style_cond="";
		}
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and a.requisition_date between '".date("j-M-Y",strtotime($data[3]))."' and '".date("j-M-Y",strtotime($data[4]))."'"; else $shipment_date ="";
		
		$job_cond2="";
		if (str_replace("'","",$data[6])!="") $job_cond2=" and job_no>0"; //else  $job_cond=""; 
		if (str_replace("'","",$data[8])!="") $job_cond2=" and job_no>0"; //else  $order_cond=""
		 
		$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
		$arr=array (2=>$comp,3=>$buyer_arr,5=>$sample_stage,6=>$sample_req_for_arr,9=>$product_dept);
		if($data[2]==0)
		{
			/*$sql= "SELECT a.requisition_number_prefix_num, a.requisition_number, $insert_year, a.company_id, a.buyer_id, a.id, b.style_ref_no, b.job_no_prefix_num, b.job_quantity, c.po_number, c.po_quantity, a.job_no, c.grouping, c.pub_shipment_date as shipment_date, null as style_id
			
			
			
				FROM wo_booking_mst a, wo_po_details_master b, wo_po_break_down c 
				WHERE a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=118   
				$shipment_date $company $buyer $job_cond $style_cond $year_cond
				GROUP BY a.booking_no_prefix_num, a.booking_no, a.insert_date, a.company_id, a.buyer_id, a.id, b.style_ref_no, b.job_no_prefix_num, b.job_quantity, c.po_number, c.po_quantity, a.job_no, c.grouping, c.pub_shipment_date 
			 order by id desc";*/
		 	 $sql= "SELECT a.requisition_number_prefix_num, a.requisition_number, $insert_year, a.company_id, a.location_id, a.buyer_name, a.id, a.style_ref_no, a.quotation_id, a.sample_stage_id, a.req_for, a.product_dept, a.team_leader, a.dealing_marchant, a.requisition_date
			 
			 FROM sample_development_mst a WHERE a.entry_form_id=117 and a.req_ready_to_approved=1 and a.status_active=1 and a.is_deleted=0
				$shipment_date $company $buyer $job_cond $style_cond $year_cond
				GROUP BY a.requisition_number_prefix_num, a.requisition_number, a.insert_date, a.company_id, a.location_id, a.buyer_name, a.id, a.style_ref_no, a.quotation_id, a.sample_stage_id, a.req_for, a.product_dept, a.team_leader, a.dealing_marchant, a.requisition_date
			 order by a.id desc";
			
			//echo $sql;
			echo create_list_view("list_view", "Req. No,Year,Company,Buyer Name,Style Ref. No,Sample Stage,Req. For,Req. Date,Style ID,Prod. Dept.", "50,50,130,130,130,100,100,70,60,90","1050","240",0, $sql , "js_set_value", "requisition_number,company_id,id", "", 1, "0,0,company_id,buyer_name,0,sample_stage_id,req_for,0,0,product_dept", $arr , "requisition_number_prefix_num,year,company_id,buyer_name,style_ref_no,sample_stage_id,req_for,requisition_date,quotation_id,product_dept", "",'','0,0,0,0,0,0,0,3,0,0');
		}
		else
		{
			$sql= "select a.requisition_number_prefix_num, $insert_year, a.job_no, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.garments_nature=$data[5] and a.is_deleted=0 $company $buyer $job_cond order by a.id DESC";
			
			echo create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,", "90,90,150,150,100","880","240",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_id,buyer_name,0,0,0,0", $arr , "requisition_number_prefix_num,year,company_id,buyer_name,style_ref_no", "",'','0,0,0,0,0,1,0,2,3') ;
		}
	}
	exit();
} 

if ($action=="populate_data_from_search_popup")
{
	list($id,$req_id,$app_type)=explode("**",$data);

	if(str_replace("'",'',$app_type)==2){
		/*$data_array=sql_select("SELECT a.booking_no_prefix_num, a.booking_no, a.insert_date, a.company_id as company_name, a.buyer_id, a.id, a.job_no, b.style_id, c.style_desc as style_description, a.currency_id, c.dealing_marchant, c.style_ref_no, c.product_dept, c.team_leader, c.agent_name, c.location_id as location_name
   		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, sample_development_mst c
 		where a.booking_no = b.booking_no and a.entry_form_id ='140' and a.status_active = 1 and a.is_deleted = 0 and a.id=$id and b.style_id=$req_id and b.style_id=c.id
		group by a.booking_no_prefix_num, a.booking_no, a.insert_date, a.company_id, a.buyer_id, a.id, a.job_no, b.style_id, c.style_desc, a.currency_id, c.dealing_marchant, c.style_ref_no, c.product_dept, c.team_leader, c.agent_name, c.location_id");*/
		
		$sql= "SELECT a.id, a.company_id as company_name, a.buyer_name as buyer_id, a.location_id as location_name, a.style_ref_no, null as style_description, a.product_dept, 0 as currency_id, a.agent_name, a.requisition_number as booking_no, 0 as region, a.team_leader, a.dealing_marchant 
		
			FROM sample_development_mst a WHERE a.entry_form_id=117 and a.id=$req_id and a.status_active=1 and a.is_deleted=0";
			//echo $sql;
		
	}else if(str_replace("'",'',$app_type)==1){
		$sql= "select a.id, b.company_name, a.buyer_id, a.job_no, b.location_name, b.style_ref_no, b.style_description, b.product_dept, b.currency_id, b.agent_name, a.booking_no, b.region, b.team_leader, b.dealing_marchant 
		from wo_booking_mst a, wo_po_details_master b	
		where a.job_no=b.job_no and a.booking_type=1 and a.id=$id and a.is_short=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=118 
		group by a.id, b.company_name, a.buyer_id, a.job_no, b.location_name, b.style_ref_no, b.style_description, b.product_dept, b.currency_id, b.agent_name, a.booking_no, b.region, b.team_leader, b.dealing_marchant order by a.id DESC";
		
	}
	//echo $sql;
	$data_array=sql_select($sql);

	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_booking_no').value = '".$row[csf("booking_no")]."';\n";  
		if($app_type==1)
		{
		echo "document.getElementById('txt_booking_id').value = '".$row[csf("id")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";  
		}else if($app_type==2){
		echo "document.getElementById('txt_req_id').value = '".$req_id."';\n"; 
		echo "document.getElementById('txt_job_no').value = '';\n";   
		}
		
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";  
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";  
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";  
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n";  
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";  
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";  
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n"; 
		
		//echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_lapdip_approval',1);\n";
		//echo "load_drop_down('requires/lapdip_approval_entry_v2_controller','".$row[csf("job_no")]."', 'load_drop_down_color_name', 'load_color');\n";
	}
	exit();
}
if($action=="load_drop_down_color_name")
{
	echo create_drop_down( "cbo_color_name", 172, "select a.id, a.color_name from lib_color a, wo_po_color_size_breakdown b where a.id=b.color_number_id and b.job_no_mst='$data' and  b.is_deleted=0 and b.status_active=1 and a.id not in(select color_name_id from wo_po_lapdip_approval_info where job_no_mst='$data' and is_deleted=0 and status_active=1) group by a.id,a.color_name order by a.color_name","id,color_name", 1, "-- Select Color --", '', "show_list_view(document.getElementById('txt_job_no').value+'**'+1+'**'+this.value+'**'+document.getElementById('txt_booking_id').value+'**'+document.getElementById('txt_req_id').value, 'lapdip_approval_list_view_edit','lapdip_approval_list_view','requires/lapdip_approval_entry_v2_controller','');");  
	exit();
}

if($action=="lapdip_approval_list_view_edit")
{
	$data=explode("**",$data);
	$colorlib_arr=array(); $colorid_arr=array();
	$updatedateCheckid="";
	if(str_replace("'",'',$data[5])==1)//Booking
	{
		$colorid_sql=sql_select("select A.BOOKING_NO, A.BOOKING_DATE, B.JOB_NO, B.FABRIC_COLOR_ID, B.GMTS_COLOR_ID from  wo_booking_mst a, wo_booking_dtls b where a.id=b.booking_mst_id and a.id=$data[3] group by A.BOOKING_NO, A.BOOKING_DATE, B.JOB_NO, b.fabric_color_id, b.gmts_color_id");
	
		foreach($colorid_sql as $cid){
			$colorid_arr[$cid['JOB_NO']][$cid['BOOKING_DATE']][$cid['FABRIC_COLOR_ID']]['gcolor'].=$cid['GMTS_COLOR_ID'].',';
			//$colorid_arr[$cid['FABRIC_COLOR_ID']]['fcolor'].=$cid['GMTS_COLOR_ID'].',';
			$colorlib_arr[$cid['FABRIC_COLOR_ID']]=$cid['FABRIC_COLOR_ID'];
			$colorlib_arr[$cid['GMTS_COLOR_ID']]=$cid['GMTS_COLOR_ID'];
		}
		unset($colorid_sql);
		$updatedateCheckid="and BOOKING_ID=$data[3]";
	}
	else if(str_replace("'",'',$data[5])==2)//Req
	{
		$colorid_sql=sql_select("select A.REQUISITION_NUMBER as JOB_NO, A.REQUISITION_DATE, B.FABRIC_COLOR, B.COLOR_ID from SAMPLE_DEVELOPMENT_MST a, SAMPLE_DEVELOPMENT_RF_COLOR b where a.id=b.MST_ID and a.id=$data[4] group by A.REQUISITION_NUMBER, A.REQUISITION_DATE, B.FABRIC_COLOR, B.COLOR_ID");
	
		foreach($colorid_sql as $cid){
			$colorid_arr[$cid['JOB_NO']][$cid['REQUISITION_DATE']][$cid['FABRIC_COLOR']]['gcolor'].=$cid['COLOR_ID'].',';
			//$colorid_arr[$cid['FABRIC_COLOR_ID']]['fcolor'].=$cid['GMTS_COLOR_ID'].',';
			$colorlib_arr[$cid['FABRIC_COLOR']]=$cid['FABRIC_COLOR'];
			$colorlib_arr[$cid['COLOR_ID']]=$cid['COLOR_ID'];
		}
		unset($colorid_sql);
		$updatedateCheckid="and REQUISITION_ID=$data[4]";
	}
	
	$uid=$_SESSION['logic_erp']['user_id'];
	$sqlUserDept=sql_select("select b.department_name, a.single_user_id from user_passwd a, lib_department b where a.department_id=b.id and a.id='$uid' and a.valid = 1");
	$userDepartment=$sqlUserDept[0][csf('department_name')];
	//$userDepartment="Merchandising";
	$single_user_id=$sqlUserDept[0][csf('single_user_id')];
	$user_level=$_SESSION['logic_erp']["user_level"];
	$labdepatDisable=""; $mktdepatDisable=""; $filedColormm=""; $filedColorLab="";
	//echo $single_user_id.'='.strtolower(trim($userDepartment));
	if ($single_user_id==2 && strtolower(trim($userDepartment))==strtolower("Merchandising")) {
		$mktdepatDisable="disabled"; $filedColormm="background-color:#CC9966";
	}
	if ($single_user_id==2 && strtolower(trim($userDepartment))==strtolower("Lab Department")) {
		$labdepatDisable="disabled"; $filedColorLab="background-color:#DDA0DD";
	}
	/*else if ($single_user_id==2 && strtolower(trim($userDepartment))==strtolower("Merchandising")) {//Defined Later
		$mktdepatEnable="disabled='disabled'";
	}*/
	
	
	$sqlOld="SELECT ID, JOB_NO_MST, BOOKING_NO, BOOKING_ID, REQUISITION_NO, REQUISITION_ID, COLOR_NAME_ID, LAPDIP_TARGET_APPROVAL_DATE, SEND_TO_FACTORY_DATE, PLANDELIVERYDATE, RECV_FROM_FACTORY_DATE, SUBMITTED_TO_BUYER, APPROVAL_STATUS, APPROVAL_STATUS_DATE, LAPDIP_NO, APPLABDIPNO, SHADE_PER, LAPDIP_COMMENTS,DENY_COMMENTS, STATUS_ACTIVE from wo_po_lapdip_approval_info where status_active=1 and is_deleted=0 $updatedateCheckid";
	 //echo $sqlOld;
	$sqlOldarr=sql_select($sqlOld); $prevDataArr=array();
	foreach($sqlOldarr as $row)
	{
		if($row['BOOKING_ID']=="") { $row['BOOKING_ID']=$row['REQUISITION_ID']; }
		$prevDataArr[$row['JOB_NO_MST']][$row['COLOR_NAME_ID']]['str']=$row['ID'].'*'.$row['LAPDIP_TARGET_APPROVAL_DATE'].'*'.$row['SEND_TO_FACTORY_DATE'].'*'.$row['RECV_FROM_FACTORY_DATE'].'*'.$row['SUBMITTED_TO_BUYER'].'*'.$row['APPROVAL_STATUS'].'*'.$row['APPROVAL_STATUS_DATE'].'*'.$row['LAPDIP_NO'].'*'.$row['SHADE_PER'].'*'.$row['LAPDIP_COMMENTS'].'*'.$row['STATUS_ACTIVE'].'*'.$row['PLANDELIVERYDATE'].'*'.$row['APPLABDIPNO'].'*'.$row['DENY_COMMENTS'];
		$colorlib_arr[$row['COLOR_NAME_ID']]=$row['COLOR_NAME_ID'];
	}
	unset($sqlOldarr);
	
	$color_lib=return_library_array( "select id, color_name from lib_color where id in (".implode(",",$colorlib_arr).")", "id", "color_name");
	?>
    <table class="rpt_table" border="1" width="1340" cellpadding="0" cellspacing="0" rules="all" id="color_table">
        <thead>
            <th width="100" class="must_entry_caption">Fabric Color</th>
            <th width="110" class="must_entry_caption">Gmts Color</th>
            <th width="70">Target Approval Date</th>
            <th width="70">Sent To Lab Section</th>
            <th width="70">Plan Del. Date</th>
            <th width="70">Recv. From Lab Section</th>
            <th width="70">Submitted To Buyer</th>
            <th width="70">Action</th>
            <th width="70" class="must_entry_caption">Action Date</th>
            <th width="100">App. Labdip No</th>
            <th width="100">Submit Lab No</th>
            <th width="70">Shade %</th>
            <th width="100">Comments</th>
			<th width="100">Deny Comments</th>
            <th width="70">Status</th>
            <th>&nbsp;</th>
        </thead>
        <tbody>
		<? $i=1;
		foreach($colorid_arr as $jobno=>$jobdata)
		{
			foreach($jobdata as $bookingdate=>$bookingdatedata)
			{
				foreach($bookingdatedata as $fcolor=>$fcolordata)
				{
					$gmtscolorname="";
					$exgcolorid=array_filter(array_unique(explode(",",$fcolordata['gcolor'])));
					foreach($exgcolorid as $xgcolorid)
					{
						if($gmtscolorname=="") $gmtscolorname=$color_lib[$xgcolorid]; else $gmtscolorname.=','.$color_lib[$xgcolorid];
					}
					$prevData="";
					$prevData=explode("*",$prevDataArr[$jobno][$fcolor]['str']);
					
					$upid=$labdip_target_approval_date=$send_to_factory_date=$recv_from_factory_date=$submitted_to_buyer=$labapproval_status=$approval_status_date=$labdip_no=$shade_per=$shade_per=$lapdip_comments=$status_active=$plan_delivery_date=$applabdipno='';
					$upid=$prevData[0];
					$labdip_target_approval_date=$prevData[1];
					$send_to_factory_date=$prevData[2];
					$recv_from_factory_date=$prevData[3];
					$submitted_to_buyer=$prevData[4];
					$labapproval_status=$prevData[5];
					$approval_status_date=$prevData[6];
					$labdip_no=$shade_per=$prevData[7];
					$lapdip_comments=$prevData[8];
					$shade_per=$prevData[9];
					$status_active=$prevData[10];
					$plan_delivery_date=$prevData[11];
					//echo $plan_delivery_date;
					$applabdipno=$prevData[12];
					$deny_comments=$prevData[13];
					
					if($send_to_factory_date=="") $send_to_factory_date=$bookingdate;
					
					$disable=""; $disable_status=0;
					
					if($labapproval_status==2 || $labapproval_status==3)
					{
						$disable="disabled='disabled'";
						$disable_status=1;
					}
					
					?>
					<tr align="center" title="<?=$color_lib[$fcolor].'==='.$jobno;?>">
						<td title="<?=$jobno; ?>"><?=create_drop_down("cbofabriccolor_".$i, 100, $color_lib,"", 0,'', $fcolor,"1","",$fcolor); ?></td>
						<td id="gcolortd_<?=$i; ?>" title="<?=$gmtscolorname; ?>">
							<input type="text" name="txtgcolorname_<?=$i; ?>" id="txtgcolorname_<?=$i; ?>" value="<?=$gmtscolorname; ?>" style="width:100px;" class="text_boxes" disabled="disabled">
							<input type="hidden" name="txtgcolorid_<?=$i; ?>" id="txtgcolorid_<?=$i; ?>" value="<?=implode(",",$exgcolorid); ?>" style="width:60px;" >
							<input type="hidden" name="txtjobno_<?=$i; ?>" id="txtjobno_<?=$i; ?>" value="<?=$jobno; ?>" style="width:60px;" >
						</td>
						<td><input type="text" name="txttargetappdate_<?=$i; ?>" id="txttargetappdate_<?=$i; ?>" style="width:60px;" class="datepicker" onChange="copy_value(this.value,'txttargetappdate_',<?=$i; ?>);" value="<? if($labdip_target_approval_date!="0000-00-00") echo change_date_format($labdip_target_approval_date); ?>" <?=$disable; ?>></td>
						<td><input type="text" name="txtsendtofactorydate_<?=$i; ?>" id="txtsendtofactorydate_<?=$i; ?>" style="width:60px;" class="datepicker" onChange="copy_value(this.value,'txtsendtofactorydate_',<?=$i; ?>);" value="<? if($send_to_factory_date!="0000-00-00") echo change_date_format($send_to_factory_date); ?>"  disabled ></td>
                        <td><input type="text" name="txtplandeliverydate_<?=$i; ?>" id="txtplandeliverydate_<?=$i; ?>" style="width:60px;<?=$filedColormm; ?>" class="datepicker" onChange="copy_value(this.value,'txtplandeliverydate_',<?=$i; ?>);" value="<? if($plan_delivery_date!="0000-00-00") echo change_date_format($plan_delivery_date); ?>" <?=$disable; ?><?=$mktdepatDisable; ?> ></td>
                        
						<td><input type="text" name="txtrecvfromfactorydate_<?=$i; ?>" id="txtrecvfromfactorydate_<?=$i; ?>" style="width:60px;<?=$filedColormm; ?>" class="datepicker" onChange="copy_value(this.value,'txtrecvfromfactorydate_',<?=$i; ?>);" value="<? if($recv_from_factory_date!="0000-00-00") echo change_date_format($recv_from_factory_date); ?>" <?=$disable; ?><?=$mktdepatDisable; ?> ></td>                        
						<td><input type="text" name="txtsubmittedtobuyer_<?=$i; ?>" id="txtsubmittedtobuyer_<?=$i; ?>" style="width:60px;<?=$filedColorLab; ?>" class="datepicker" onChange="copy_value(this.value,'txtsubmittedtobuyer_',<?=$i; ?>);" value="<? if($submitted_to_buyer!="0000-00-00") echo change_date_format($submitted_to_buyer); ?>" <?=$disable; ?> <?=$labdepatDisable; ?> ></td>
						<td><?=create_drop_down("cboaction_".$i, 70, $approval_status,"", 1, "--   --",$labapproval_status,"copy_value(this.value,'cboaction_',".$i.")",$disable_status); ?></td>
						<td><input type="text" name="txtactiondate_<?=$i; ?>" id="txtactiondate_<?=$i; ?>" style="width:60px;<?=$filedColorLab; ?>" class="datepicker" onChange="copy_value(this.value,'txtactiondate_',<?=$i; ?>);" value="<? if($approval_status_date!="" && $approval_status_date!="0000-00-00") echo change_date_format($approval_status_date); ?>" <?=$disable; ?> <?=$labdepatDisable; ?>></td>
						<td><input type="text" name="txtlapdipno_<?=$i; ?>" id="txtlapdipno_<?=$i; ?>" style="width:90px;<?=$filedColorLab; ?>" class="text_boxes" onChange="copy_value(this.value,'txtlapdipno_',<?=$i; ?>);" value="<?=$labdip_no; ?>" <?=$disable; ?> <?=$labdepatDisable; ?>></td>
                        <td><input type="text" name="txtapplabdipno_<?=$i; ?>" id="txtapplabdipno_<?=$i; ?>" style="width:90px;<?=$filedColormm; ?>" class="text_boxes" onChange="copy_value(this.value,'txtapplabdipno_',<?=$i; ?>);" value="<?=$applabdipno; ?>" <?=$disable; ?> <?=$mktdepatDisable; ?>></td>
						<td><input type="text" name="txtshadeper_<?=$i; ?>" id="txtshadeper_<?=$i; ?>" style="width:60px;<?=$filedColormm; ?>" class="text_boxes" onChange="copy_value(this.value,'txtshadeper_',<?=$i; ?>);" value="<?=$shade_per; ?>"  <?=$disable; ?> <?=$mktdepatDisable; ?>></td>
						<td><input type="text" name="txtcomments_<?=$i; ?>" id="txtcomments_<?=$i; ?>" style="width:90px;<?=$filedColorLab; ?>" class="text_boxes" onChange="copy_value(this.value,'txtcomments_',<?=$i; ?>);" value="<?=$lapdip_comments; ?>" <?=$disable; ?> placeholder="Single Click" onClick="fnc_comments(this.id, this.value);" readonly <?=$labdepatDisable; ?>></td>
						<td><input type="text" name="txtdenycomments_<?=$i; ?>" id="txtdenycomments_<?=$i; ?>" style="width:90px;<?=$filedColorLab; ?>" class="text_boxes" onChange="copy_value(this.value,'txtcomments_',<?=$i; ?>);" value="<?=$deny_comments; ?>" <?=$disable; ?>      <? //$labdepatDisable; ?>></td>

						<td><?=create_drop_down("cbostatus_".$i, 80, $row_status,"", 0,"",$status_active,"copy_value(this.value,'cbostatus_',".$i.")",$disable_status); ?>
							<input type="hidden" name="updateid_<?=$i; ?>" id="updateid_<?=$i; ?>" value="<?=$upid; ?>">
						</td>
						<td>
						<?
						if($labapproval_status==2)
						{
							?><input type="button" id="addrow_<?=$i; ?>"  name="addrow_<?=$i; ?>" style="width:75px" class="formbutton" value="Re-Submit" onClick="resubmit(<?=$i; ?>);" /><?
						}
						?>
						</td>
					</tr>
					<?
					$i++;
				}
			}
		}
		?>
        </tbody>
    </table>
    <?
	exit();
}

if($action=="lapdip_approval_list_view_edit-06092023")//kausar
{
	$data=explode("**",$data);
	$color_lib=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$color_array=array(); $po_id=''; $color_arr=array();$poid_arr=array();
	$poIdsArr=array(); $allPoIdArr=array();
	$poid_arr=sql_select("select  b.po_break_down_id,b.fabric_color_id,b.gmts_color_id from  wo_booking_mst a ,wo_booking_dtls b where a.id=b.booking_mst_id and a.id=$data[3] group by b.po_break_down_id,b.fabric_color_id,b.gmts_color_id");

	 	foreach($poid_arr as $pid){
			$poIds[$pid['PO_BREAK_DOWN_ID']]=$pid['PO_BREAK_DOWN_ID'];
		}
 		$poId=implode(",",$poIds);
		
	$job_no=$data[0]; $type=$data[1]; 
	$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down where job_no_mst='$job_no' and id in($poId)",'id','po_number');

	 
	if($data[3]>0){
		$booking_cond="and booking_id=$data[3]";
	}else{
		$booking_cond="";
	}
 //echo $data[3].'='.$data[4].'=';
	if($data[3] >0 && $data[4]>0){
		
		 $sql="SELECT id, po_break_down_id, color_name_id, lapdip_target_approval_date, send_to_factory_date, recv_from_factory_date, submitted_to_buyer, approval_status, approval_status_date,lapdip_no,shade_per,lapdip_comments,status_active from wo_po_lapdip_approval_info where status_active=1  $booking_cond and is_deleted=0   order by color_name_id,po_break_down_id,id";

		$colorDataEc=sql_select("select b.id, b.color_name from wo_po_lapdip_approval_info a, lib_color b where a.color_name_id=b.id and booking_id=$data[3] and a.color_name_id not in(".implode(",",array_keys($color_arr)).") group by b.id, b.color_name");
		foreach($colorDataEc as $row)
		{
			$color_arr[$row[csf('id')]]=$row[csf('color_name')];
		}
		//echo "A";

	}else{
		$colorData=sql_select("select a.po_break_down_id, b.id, b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.job_no_mst='$job_no' and a.po_break_down_id in ($poId) group by a.po_break_down_id, b.id, b.color_name");
		//  echo "select a.po_break_down_id, b.id, b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.job_no_mst='$job_no' and a.po_break_down_id in ($poId) group by a.po_break_down_id, b.id, b.color_name";
		// echo "select a.po_break_down_id, b.id, b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.job_no_mst='$job_no' and a.po_break_down_id in ($poId) group by a.po_break_down_id, b.id, b.color_name";
		 
		 
		foreach($colorData as $row)
		{
				$color_arr[$row[csf('id')]]=$row[csf('color_name')];
				$poIdsArr[$row[csf('id')]].=$row[csf('po_break_down_id')].",";
				$allPoIdArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
	
		}

		$sql="SELECT id, po_break_down_id, color_name_id, lapdip_target_approval_date, send_to_factory_date, recv_from_factory_date, submitted_to_buyer, approval_status, approval_status_date,lapdip_no,shade_per,lapdip_comments,status_active 
			from wo_po_lapdip_approval_info 
		 	where job_no_mst='$job_no' and po_break_down_id in ($poId) and is_deleted=0 and status_active=1  
		 	group by id,po_break_down_id,color_name_id,lapdip_target_approval_date,send_to_factory_date,recv_from_factory_date,submitted_to_buyer,approval_status,approval_status_date,lapdip_no,shade_per,lapdip_comments,status_active,booking_id 
			 order by color_name_id,po_break_down_id,id";

		$colorDataEc=sql_select("select b.id, b.color_name from wo_po_lapdip_approval_info a, lib_color b where a.color_name_id=b.id and a.job_no_mst='$job_no' and a.color_name_id not in(".implode(",",array_keys($color_arr)).") group by b.id, b.color_name");
		// echo "select b.id, b.color_name from wo_po_lapdip_approval_info a, lib_color b where a.color_name_id=b.id and a.job_no_mst='$job_no' and a.color_name_id not in(".implode(",",array_keys($color_arr)).") group by b.id, b.color_name";
		foreach($colorDataEc as $row)
		{
			$color_arr[$row[csf('id')]]=$row[csf('color_name')];
			
		}
	}
	
	// echo $sql;
		
	$dataArray=sql_select($sql);
	$partial_approved=0; $full_approved=0; $unapproved=0;
	foreach ($dataArray as $row) {
		
		$color_approval_status_arr[$row[csf('color_name_id')]][$row[csf('po_break_down_id')]]=$row[csf('approval_status')];
			
	}
	$partial_approved = 0;
	$color_approved_status=array();
	foreach ($color_approval_status_arr as $color_id => $color_app_arr) {
	    $status = 'Fully Approved';
	    $partial_approved = 0;
	    if(count($color_app_arr)>1)
	    {
			foreach ($color_app_arr as $key1 => $value1) {
				$unapproved = 0;
				foreach ($color_app_arr as $key2 => $value2) {
					if($key1 != $key2 && $value1 == 3 && $value2 != 3){
						$partial_approved = 1 ; 
						break;			
					}
					if($key1 != $key2 && $value1 != 3 && $value2 != 3){
						$unapproved++;				
					}
				};
				if($partial_approved > 0){
					$status = 'Partial Approved';
					break;
				}
				elseif($unapproved == count($color_app_arr) - 1){
					$status = 'Full Pending';
					break;
				}
			}
	    }
	    else{
	    	foreach ($color_app_arr as $key => $value) {
	    		if($value == 3){
	    			$status = 'Fully Approved';
	    		}
	    		else{
	    			$status = 'Full Pending';
	    		}
	    	}
	    }

		$color_approved_status[$color_id] = $status;
	}
	$z=1; $i=1;
	foreach($dataArray as $row)
	{
		if($row[csf("approval_status")]==2 || $row[csf("approval_status")]==3)
		{
			$disable="disabled='disabled'";
			$disable_status=1;
		}
		else
		{
			$disable="";
			$disable_status=0;
		}
		
		$color_id=$row[csf("color_name_id")];
		if(in_array($color_id,$color_array))
		{
			$print_cond_header=0;
			$print_cond_footer=0;
        }
		else
		{
			$print_cond_header=1;
			if($z==1) 
			{
				$print_cond_footer=0;
			}
			else 
			{
				$print_cond_footer=1;
			}
			$color_array[]=$color_id;
		}
		
		if($print_cond_footer==1)
		{
					$po_id_arr=array_unique(explode(",",substr($po_id,0,-1)));
					$colorPoIds=array_unique(explode(",",substr($poIdsArr[$prev_color_id],0,-1)));
					$result=implode(",",array_diff($colorPoIds,$po_id_arr));
					//print_r($result);
					foreach($result as $poId)
					{
					?>
						<tr align="center">
							<td>
								  <?
									echo create_drop_down("po_no_".$prev_color_id."_".$i, 100, $po_number_arr,"", 1,'', $poId,"",1);
								?>
								<input type="hidden" name="po_id_<? echo $prev_color_id.'_'.$i; ?>" id="po_id_<? echo $prev_color_id.'_'.$i; ?>" value="<? echo $poId; ?>" style="width:100px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("color_".$prev_color_id."_".$i, 90, $color_arr,"", 1,'', $prev_color_id,"",1);
								?>
								<input type="hidden" name="color_id_<? echo $prev_color_id.'_'.$i; ?>" id="color_id_<? echo $prev_color_id.'_'.$i; ?>" value="<? echo $prev_color_id; ?>" style="width:90px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<input type="text" name="target_app_date_<? echo $prev_color_id.'_'.$i; ?>" id="target_app_date_<? echo $prev_color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'target_app_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="send_to_factory_date_<? echo $prev_color_id.'_'.$i; ?>" id="send_to_factory_date_<? echo $prev_color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'send_to_factory_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="recv_from_factory_date_<? echo $prev_color_id.'_'.$i; ?>" id="recv_from_factory_date_<? echo $prev_color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'recv_from_factory_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="submitted_to_buyer_<? echo $prev_color_id.'_'.$i; ?>" id="submitted_to_buyer_<? echo $prev_color_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'submitted_to_buyer_',<? echo $i; ?>)">
							</td>
							<td>
								<?
									echo create_drop_down("action_".$prev_color_id."_".$i, 90, $approval_status,"", 1, "--   --","","copy_value(this.value,'action_',".$i.")");
								?>
							</td>
							<td>
								<input type="text" name="action_date_<? echo $prev_color_id.'_'.$i; ?>" id="action_date_<? echo $prev_color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'action_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_lapdip_no_<? echo $prev_color_id.'_'.$i; ?>" id="txt_lapdip_no_<? echo $prev_color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_lapdip_no_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_shade_per_no_<? echo $prev_color_id.'_'.$i; ?>" id="txt_shade_per_no_<? echo $prev_color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_shade_per_no_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_comments_<? echo $prev_color_id.'_'.$i; ?>" id="txt_comments_<? echo $prev_color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_comments_',<? echo $i; ?>)" placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly>
							</td>
							<td>
								<?
									echo create_drop_down("cbo_status_".$prev_color_id."_".$i, 80, $row_status,"", 0,"","","copy_value(this.value,'cbo_status_',".$i.")",0);
								?>
								<input type="hidden" name="updateid_<? echo $prev_color_id.'_'.$i; ?>" id="updateid_<? echo $prev_color_id.'_'.$i; ?>" value="">
							</td>
							<td></td>
						</tr>
					<?	
					$i++;
					}
					$po_id='';
					$i=1;
					?>
        			</tbody>
                </table>
            </div>
		<?
		}
		
		if($print_cond_header==1)
		{
			
		  ?>
            <h3 align="left" id="accordion_h<? echo $color_id; ?>" style="width:1175px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel_<? echo $color_id; ?>', 'fnc_color_id(<? echo $color_id; ?>,1,0)',1)" > +<? echo $color_lib[$color_id]; ?> <span style="color: red">[<? echo $color_approved_status[$color_id] ?>]</span></h3>
            <div id="content_search_panel_<? echo $color_id; ?>" style="display:none" class="accord_close">
                <table class="rpt_table" border="1" width="1175" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $color_id; ?>">
                    <thead>
                        <th>Po Number</th>
                        <th class="must_entry_caption">Color Name</th>
                        <th>Target Approval Date</th>
                        <th>Sent To Lab Section</th>
                        <th>Recv. From Lab Section</th>
                        <th>Submitted To Buyer</th>
                        <th>Action</th>
                        <th class="must_entry_caption">Action Date</th>
                        <th>Labdip No</th>
						<th>Shade %</th>
                        <th>Comments</th>
                        <th>Status</th>
                        <th><input type="hidden" name="current_status_<? echo $color_id; ?>" id="current_status_<? echo $color_id; ?>" value="" style="width:75px;" class="text_boxes" readonly></th>
                    </thead>
                    <tbody>
		  <?
		  }
		
		   $po_id.=$row[csf("po_break_down_id")].",";
		   if($row[csf("approval_status")]!=3)
		   {
			$disable="";
			$disable_status=0;
		   }
		    // echo $row[csf("approval_status")].'='.$color_id.'='.$disable.'<br>';
          ?>
                <tr align="center" title="<?=$po_number_arr[$row[csf("po_break_down_id")]];?>">
                    <td>
                           <?
                            echo create_drop_down("po_no_".$color_id."_".$i, 100, $po_number_arr,"", 1,'', $row[csf("po_break_down_id")],$disable_status);
                        ?>
                        <input type="hidden" name="po_id_<? echo $color_id.'_'.$i; ?>" id="po_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $row[csf("po_break_down_id")]; ?>" style="width:100px;" class="text_boxes" disabled="disabled">
                    </td>
                    <td>
                        <?
                            echo create_drop_down("color_".$color_id."_".$i, 90, $color_lib,"", 1,'', $color_id,"","");
                        ?>
                        <input type="hidden" name="color_id_<? echo $color_id.'_'.$i; ?>" id="color_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $color_id; ?>" style="width:90px;" class="text_boxes" disabled="disabled">
                    </td>
                    <td>
                        <input type="text" name="target_app_date_<? echo $color_id.'_'.$i; ?>" id="target_app_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'target_app_date_',<? echo $i;?>)" value="<? if($row[csf("lapdip_target_approval_date")]!="0000-00-00") echo change_date_format($row[csf("lapdip_target_approval_date")]);?>" <? echo $disable; ?>>
                    </td>
                    <td>
                        <input type="text" name="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" id="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'send_to_factory_date_',<? echo $i; ?>)" value="<? if($row[csf("send_to_factory_date")]!="0000-00-00") echo change_date_format($row[csf("send_to_factory_date")]); ?>" <? echo $disable; ?> >
                    </td>
                    <td>
                        <input type="text" name="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" id="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'recv_from_factory_date_',<? echo $i; ?>)" value="<? if($row[csf("recv_from_factory_date")]!="0000-00-00") echo change_date_format($row[csf("recv_from_factory_date")]); ?>" <? echo $disable; ?> >
                    </td>                        
                    <td>
                        <input type="text" name="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" id="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'submitted_to_buyer_',<? echo $i; ?>)" value="<? if($row[csf("submitted_to_buyer")]!="0000-00-00") echo change_date_format($row[csf("submitted_to_buyer")]); ?>" <? echo $disable; ?>>
                    </td>
                    <td>
                        <?
                            echo create_drop_down("action_".$color_id."_".$i, 90, $approval_status,"", 1, "--   --",$row[csf("approval_status")],"copy_value(this.value,'action_',".$i.")",$disable_status);
                        ?>
                    </td>
                    <td>
                        <input type="text" name="action_date_<? echo $color_id.'_'.$i; ?>" id="action_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'action_date_',<? echo $i; ?>)" value="<? if($row[csf("approval_status_date")]!="" && $row[csf("approval_status_date")]!="0000-00-00") echo change_date_format($row[csf("approval_status_date")]); ?>"  <? echo $disable; ?>>
                    </td>
                     <td>
                        <input type="text" name="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" id="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_lapdip_no_',<? echo $i; ?>)" value="<? echo $row[csf("lapdip_no")]; ?>"  <? echo $disable; ?>>
                    </td>
					<td>
                        <input type="text" name="txt_shade_per_no_<? echo $color_id.'_'.$i; ?>" id="txt_shade_per_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_shade_per_no_',<? echo $i; ?>)" value="<? echo $row[csf("shade_per")]; ?>"  <? echo $disable; ?>>
                    </td>
                    <td>
                        <input type="text" name="txt_comments_<? echo $color_id.'_'.$i; ?>" id="txt_comments_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_comments_',<? echo $i; ?>)" value="<? echo $row[csf("lapdip_comments")]; ?>" <? echo $disable; ?> placeholder="Single Click" onClick="fnc_comments(this.id, this.value)" readonly >
                    </td>
                    <td>
                        <?
                            echo create_drop_down("cbo_status_".$color_id."_".$i, 80, $row_status,"", 0,"",$row[csf("status_active")],"copy_value(this.value,'cbo_status_',".$i.")",$disable_status);
                        ?>
                        <input type="hidden" name="updateid_<? echo $color_id.'_'.$i; ?>" id="updateid_<? echo $color_id.'_'.$i; ?>" value="<? echo $row[csf("id")]; ?>">
                    </td>
                    <td>
                        <?
                        if($row[csf("approval_status")]==2)
                        {
                        ?>
                            <input type="button" id="addrow_<? echo $i; ?>"  name="addrow_<? echo $i; ?>" style="width:75px" class="formbutton" value="Re-Submit" onClick="resubmit(<? echo $color_id; ?>,<? echo $i; ?>)" />
                        <?
                        }
                        ?>
                    </td>
                </tr>
                
		<?
		$i++;
		$z++;
		$prev_color_id=$color_id;
	}
		if($z>1){
					$po_id_arr=array_unique(explode(",",substr($po_id,0,-1)));
					$colorPoIds=array_unique(explode(",",substr($poIdsArr[$color_id],0,-1)));
					$result=implode(",",array_diff($colorPoIds,$po_id_arr));
					//print_r($result);
					foreach($result as $poId)
					{
					?>
						<tr align="center">
							  <td>
								<?
									echo create_drop_down("po_no_".$color_id."_".$i, 100, $po_number_arr,"", 1,'', $poId,"",1);
								?>
								<input type="hidden" name="po_id_<? echo $color_id.'_'.$i; ?>" id="po_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $poId; ?>" style="width:100px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("color_".$color_id."_".$i, 90, $color_arr,"", 1,'', $color_id,"",1);
								?>
								<input type="hidden" name="color_id_<? echo $color_id.'_'.$i; ?>" id="color_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $color_id; ?>" style="width:90px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<input type="text" name="target_app_date_<? echo $color_id.'_'.$i; ?>" id="target_app_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'target_app_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" id="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'send_to_factory_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" id="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'recv_from_factory_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" id="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'submitted_to_buyer_',<? echo $i; ?>)">
							</td>
							<td>
								<?
									echo create_drop_down("action_".$color_id."_".$i, 90, $approval_status,"", 1, "--   --","","copy_value(this.value,'action_',".$i.")");
								?>
							</td>
							<td>
								<input type="text" name="action_date_<? echo $color_id.'_'.$i; ?>" id="action_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'action_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" id="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_lapdip_no_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_shade_per_no_<? echo $color_id.'_'.$i; ?>" id="txt_shade_per_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_shade_per_no_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_comments_<? echo $color_id.'_'.$i; ?>" id="txt_comments_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_comments_',<? echo $i; ?>)" placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly>
							</td>
							<td>
								<?
									echo create_drop_down("cbo_status_".$color_id."_".$i, 80, $row_status,"", 0,"","","copy_value(this.value,'cbo_status_',".$i.")",0);
								?>
								<input type="hidden" name="updateid_<? echo $color_id.'_'.$i; ?>" id="updateid_<? echo $color_id.'_'.$i; ?>" value="">
							</td>
							<td></td>
						</tr>
					<?	
					$i++;
					}
					?>
    			</tbody>
			</table>
		</div>
	<?
	}

	if($type==1)
	{
		
		$color_id=trim($data[2]);
		if($color_id=="ec")//ec=Extra Color
		{
			
		 ?>
			<h3 align="left" id="accordion_h<? echo $color_id; ?>" style="width:1175px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel_<? echo $color_id; ?>', 'fnc_color_id(\'<? echo $color_id; ?>\',0,1)',1)"> +<? echo "Extra Color"; ?> <span style="color: red">[<? echo $color_approved_status[$color_id] ?>]</span></h3>
			<div id="content_search_panel_<? echo $color_id; ?>" style="display:none" class="accord_close">
				<table class="rpt_table" border="1" width="1175" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $color_id; ?>">
					<thead>
						<th>Po Number</th>
						<th>Color Name</th>
						<th>Target Approval Date</th>
						<th>Sent To Lab Section</th>
                        <th>Recv. From Lab Section</th>
						<th>Submitted To Buyer</th>
						<th>Action</th>
						<th>Action Date</th>
						<th>Labdip No</th>
						<th>Shade %</th>
						<th>Comments</th>
						<th>Status</th>
						<th><input type="hidden" name="current_status_<? echo $color_id; ?>" id="current_status_<? echo $color_id; ?>" value="" style="width:75px;" class="text_boxes" readonly></th>
					</thead>
					<tbody>
					<?
                    $i=1;
					// print_r($allPoIdArr);
				if(count($allPoIdArr)>0){ 

					foreach($allPoIdArr as $poId)
					{
						if($i==1)
						{
							$disable="";
							$disable_status=0;
						}
						else 
						{
							$disable="disabled='disabled'";
							$disable_status=1;
						}
						 
						$disable_status="";
						$disable="";
							
							//echo $row[csf("approval_status")].'=K'.$color_id.'='.$disable.'<br>';
						 
						
					 ?>
						<tr align="center" title="<?=$po_number_arr[$poId];?>">
							<td>
								  <?
									echo create_drop_down("po_no_".$color_id."_".$i, 100, $po_number_arr,"", 1,'', $poId,"",1);
								?>
								<input type="hidden" name="po_id_<? echo $color_id.'_'.$i; ?>" id="po_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $poId; ?>" style="width:100px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<input type="text" name="color_id_<? echo $color_id.'_'.$i; ?>" id="color_id_<? echo $color_id.'_'.$i; ?>" value="" style="width:90px;" class="text_boxes" onBlur="check_color_name(this.value,'color_id_',<? echo $i; ?>)" <? //echo $disable; ?>>
                                <input type="hidden" name="color_<? echo $color_id.'_'.$i; ?>" id="color_id_<? echo $color_id.'_'.$i; ?>" value="" style="width:90px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<input type="text" name="target_app_date_<? echo $color_id.'_'.$i; ?>" id="target_app_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'target_app_date_',<? echo $i; ?>)" <? echo $disable; ?>>
							</td>
							<td>
								<input type="text" name="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" id="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'send_to_factory_date_',<? echo $i; ?>)" <? echo $disable; ?>>
							</td>
							<td>
								<input type="text" name="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" id="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'recv_from_factory_date_',<? echo $i; ?>)" <? echo $disable; ?>>
							</td>
							<td>
								<input type="text" name="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" id="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'submitted_to_buyer_',<? echo $i; ?>)" <? echo $disable; ?>>
							</td>
							<td>
								<?
									echo create_drop_down("action_".$color_id."_".$i, 90, $approval_status,"", 1, "--   --","","copy_value(this.value,'action_',".$i.")",$disable_status);
								?>
							</td>
							<td>
								<input type="text" name="action_date_<? echo $color_id.'_'.$i; ?>" id="action_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'action_date_',<? echo $i; ?>)" <? echo $disable; ?>>
							</td>
							<td>
								<input type="text" name="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" id="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_lapdip_no_',<? echo $i; ?>)" <? echo $disable; ?>>
							</td>
							<td>
								<input type="text" name="txt_shade_per_no_<? echo $color_id.'_'.$i; ?>" id="txt_shade_per_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_shade_per_no_',<? echo $i; ?>)" <? echo $disable; ?>>
							</td>
							<td>
								<input type="text" name="txt_comments_<? echo $color_id.'_'.$i; ?>" id="txt_comments_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_comments_',<? echo $i; ?>)" <? echo $disable; ?> placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly>
							</td>
							<td>
								<?
									echo create_drop_down("cbo_status_".$color_id."_".$i, 80, $row_status,"", 0,"","","copy_value(this.value,'cbo_status_',".$i.")",$disable_status);
								?>
								<input type="hidden" name="updateid_<? echo $color_id.'_'.$i; ?>" id="updateid_<? echo $color_id.'_'.$i; ?>" value="">
							</td>
							<td></td>
						</tr>
					 <?	
					 $i++;
					}
				}else{?>
					<tr align="center" title="<?=$po_number_arr[$poId];?>">
							<td>
								  <?
									echo create_drop_down("po_no_".$color_id."_".$i, 100, $po_number_arr,"", 1,'', $poId,"",1);
								?>
								<input type="hidden" name="po_id_<? echo $color_id.'_'.$i; ?>" id="po_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $poId; ?>" style="width:100px;" class="text_boxes">
							</td>
							<td>
								<input type="text" name="color_id_<? echo $color_id.'_'.$i; ?>" id="color_id_<? echo $color_id.'_'.$i; ?>" value="" style="width:90px;" class="text_boxes" onBlur="check_color_name(this.value,'color_id_',<? echo $i; ?>)" <? //echo $disable; ?>>
                                <input type="hidden" name="color_<? echo $color_id.'_'.$i; ?>" id="color_id_<? echo $color_id.'_'.$i; ?>" value="" style="width:90px;" class="text_boxes">
							</td>
							<td>
								<input type="text" name="target_app_date_<? echo $color_id.'_'.$i; ?>" id="target_app_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'target_app_date_',<? echo $i; ?>)" >
							</td>
							<td>
								<input type="text" name="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" id="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'send_to_factory_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" id="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'recv_from_factory_date_',<? echo $i; ?>)" >
							</td>
							<td>
								<input type="text" name="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" id="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'submitted_to_buyer_',<? echo $i; ?>)" >
							</td>
							<td>
								<?
									echo create_drop_down("action_".$color_id."_".$i, 90, $approval_status,"", 1, "--   --","","copy_value(this.value,'action_',".$i.")",'');
								?>
							</td>
							<td>
								<input type="text" name="action_date_<? echo $color_id.'_'.$i; ?>" id="action_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'action_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" id="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_lapdip_no_',<? echo $i; ?>)" >
							</td>
							<td>
								<input type="text" name="txt_shade_per_no_<? echo $color_id.'_'.$i; ?>" id="txt_shade_per_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_shade_per_no_',<? echo $i; ?>)" >
							</td>
							<td>
								<input type="text" name="txt_comments_<? echo $color_id.'_'.$i; ?>" id="txt_comments_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_comments_',<? echo $i; ?>)" <? echo $disable; ?> placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly>
							</td>
							<td>
								<?
									echo create_drop_down("cbo_status_".$color_id."_".$i, 80, $row_status,"", 0,"","","copy_value(this.value,'cbo_status_',".$i.")",'');
								?>
								<input type="hidden" name="updateid_<? echo $color_id.'_'.$i; ?>" id="updateid_<? echo $color_id.'_'.$i; ?>" value="">
							</td>
							<td></td>
						</tr>
						<?

				}
					?>
					</tbody>
				</table>
			</div>
		<?
		}
		else if($color_id!=0)
		{
		?>
			<h3 align="left" id="accordion_h<? echo $color_id; ?>" style="width:1175px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel_<? echo $color_id; ?>', 'fnc_color_id(<? echo $color_id; ?>,0,0)',1)"> +<? echo $color_arr[$color_id]; ?> <span style="color: red">[<? echo $color_approved_status[$color_id] ?>]</span></h3>
			<div id="content_search_panel_<? echo $color_id; ?>" style="display:none" class="accord_close">
				<table class="rpt_table" border="1" width="1175" cellpadding="0" cellspacing="0" rules="all" id="table_<? echo $color_id; ?>">
					<thead>
						<th>Po Number</th>
						<th>Color Name</th>
						<th>Target Approval Date</th>
						<th>Sent To Lab Section</th>
                        <th>Recv. From Lab Section</th>
						<th>Submitted To Buyer</th>
						<th>Action</th>
						<th>Action Date</th>
						<th>Labdip No</th>
						<th>Shade %</th>
						<th>Comments</th>
						<th>Status</th>
						<th><input type="hidden" name="current_status_<? echo $color_id; ?>" id="current_status_<? echo $color_id; ?>" value="" style="width:75px;" class="text_boxes" readonly></th>
					</thead>
					<tbody>
					<?
					$i=1;
					/*$sql="select po_break_down_id from wo_po_color_size_breakdown where job_no_mst='$job_no' and color_number_id=$color_id and status_active=1 and is_deleted=0 group by po_break_down_id";
					$dataArray=sql_select($sql);
					foreach($dataArray as $row)*/
					$colorPoIds=array_unique(explode(",",substr($poIdsArr[$color_id],0,-1)));
					foreach($colorPoIds as $poId)
					{
					?>
						<tr align="center" title="<?=$po_number_arr[$poId];?>">
							<td>
								<?
									echo create_drop_down("po_no_".$color_id."_".$i, 100, $po_number_arr,"", 1,'', $poId,"",1);
								?>
								<input type="hidden" name="po_id_<? echo $color_id.'_'.$i; ?>" id="po_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $poId; ?>" style="width:100px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<?
									echo create_drop_down("color_".$color_id."_".$i, 90, $color_arr,"", 1,'', $color_id,"",1);
								?>
								<input type="hidden" name="color_id_<? echo $color_id.'_'.$i; ?>" id="color_id_<? echo $color_id.'_'.$i; ?>" value="<? echo $color_id; ?>" style="width:90px;" class="text_boxes" disabled="disabled">
							</td>
							<td>
								<input type="text" name="target_app_date_<? echo $color_id.'_'.$i; ?>" id="target_app_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'target_app_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" id="send_to_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'send_to_factory_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" id="recv_from_factory_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'recv_from_factory_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" id="submitted_to_buyer_<? echo $color_id.'_'.$i; ?>" style="width:80px;" class="datepicker" onChange="copy_value(this.value,'submitted_to_buyer_',<? echo $i; ?>)">
							</td>
							<td>
								<?
									echo create_drop_down("action_".$color_id."_".$i, 90, $approval_status,"", 1, "--   --","","copy_value(this.value,'action_',".$i.")");
								?>
							</td>
							<td>
								<input type="text" name="action_date_<? echo $color_id.'_'.$i; ?>" id="action_date_<? echo $color_id.'_'.$i; ?>" style="width:70px;" class="datepicker" onChange="copy_value(this.value,'action_date_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" id="txt_lapdip_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_lapdip_no_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_shade_per_no_<? echo $color_id.'_'.$i; ?>" id="txt_shade_per_no_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_shade_per_no_',<? echo $i; ?>)">
							</td>
							<td>
								<input type="text" name="txt_comments_<? echo $color_id.'_'.$i; ?>" id="txt_comments_<? echo $color_id.'_'.$i; ?>" style="width:100px;" class="text_boxes" onChange="copy_value(this.value,'txt_comments_',<? echo $i; ?>)" placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly>
							</td>
							<td>
								<?
									echo create_drop_down("cbo_status_".$color_id."_".$i, 80, $row_status,"", 0,"","","copy_value(this.value,'cbo_status_',".$i.")",0);
								?>
								<input type="hidden" name="updateid_<? echo $color_id.'_'.$i; ?>" id="updateid_<? echo $color_id.'_'.$i; ?>" value="">
							</td>
							<td></td>
						</tr>
					<?	
					$i++;
					}
					?>
					</tbody>
				</table>
			</div>
		<?
		}
	}
	exit();
}


if($action=="check_color_name")
{
	$data=explode("**",$data);
	
	if($data[2]==1){
		$response=is_duplicate_field( "b.color_number_id", "lib_color a, wo_po_color_size_breakdown b", "a.id=b.color_number_id and b.job_no_mst='".trim($data[1])."' and a.color_name='".trim($data[0])."' and b.is_deleted=0 and b.status_active=1");
	}else{
		$response=is_duplicate_field( "a.sample_color", "lib_color a, sample_development_dtls b", "a.id=b.sample_color and b.sample_mst_id='".trim($data[3])."' and a.color_name='".trim($data[0])."' ");
	
	}
	
	echo $response;
	exit();
}

if($action=="comments_popup")
{
	echo load_html_head_contents("Comments Info", "../../../", 1, 1,'','','');
	extract($_REQUEST); 
?>
    
</head>

<body>
<div style="width:430px;" align="center">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:400px; margin-top:10px;">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table" >
                <tr>
               		<td><textarea name="txt_comments" id="txt_comments" class="text_area" style="width:385px; height:120px;"><? echo $comments_data; ?></textarea></td>
                </tr>
            </table>
            <table width="400" id="tbl_close">
                 <tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
?>