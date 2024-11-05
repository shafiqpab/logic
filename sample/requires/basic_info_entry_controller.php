<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
$permission=$_SESSION['page_permission'];
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
 $color_arr=return_library_array( "select id, color_name from lib_color where status_active=1", "id", "color_name");
 
if ($action=="load_drop_down_basic_color_type")
{
	$data=explode("_",$data);
	$deterId=$data[0];
	$booking_no=$data[1];
	 $res_book = sql_select("SELECT  c.color_type_id,c.dtls_id,c.gsm_weight FROM sample_development_mst b, wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls c WHERE c.booking_no='$booking_no' and a.entry_form_id = 140 AND a.status_active = 1 AND a.booking_no=c.booking_no and b.id=c.style_id and c.lib_yarn_count_deter_id=$deterId AND a.is_deleted = 0 group by c.color_type_id,c.dtls_id,c.gsm_weight");

	foreach ($res_book as $row)
	{
	 $colorTypeArr[$color_type[$row[csf('color_type_id')]]]=$color_type[$row[csf('color_type_id')]];
	  $dtls_idArr[$row[csf('dtls_id')]]=$row[csf('dtls_id')];
	   $gsm_weight_idArr[$row[csf('gsm_weight')]]=$row[csf('gsm_weight')];
	}
	//echo "select width_dia_id from sample_development_fabric_acc where   status_active=1 and is_deleted=0 and width_dia_id>0  and id in(".implode(",",$dtls_idArr).")";
	$sql_fab=sql_select("select width_dia_id,dia from sample_development_fabric_acc where   status_active=1 and is_deleted=0 and width_dia_id>0  and id in(".implode(",",$dtls_idArr).")");
	foreach($sql_fab as $row)
	{
		$dia_typeArr[]=$fabric_typee[$row[csf('width_dia_id')]];
		$dia_Arr[]=$row[csf('dia')];
	}
	$fab_color_type_drop=create_drop_down( "txt_color_type", 150, $colorTypeArr,"", 1, "-- select --","","" );
	echo "document.getElementById('color_type_td').innerHTML = '".$fab_color_type_drop."';\n";

	 $gsm_drop=create_drop_down( "finished_gsm", 150, $gsm_weight_idArr,"", 1, "-- select --","","" );
	 echo "document.getElementById('finished_gsm_td').innerHTML = '".$gsm_drop."';\n";
		
	
		
 //echo "$('#txt_color_type').val('".implode(",",array_unique($colorTypeArr))."');\n";
 echo "$('#finish_dia_type').val('".implode(",",array_unique($dia_typeArr))."');\n";
 echo "$('#finish_dia').val('".implode(",",array_unique($dia_Arr))."');\n";
 //echo "$('#finished_gsm').val('".implode(",",array_unique($gsm_weight_idArr))."');\n";
exit();	 
}
if ($action=="load_drop_down_basic_color_type")
{

	$dataArr=explode("_",$data);
	$color_id=$dataArr[0];
	$booking_no=$dataArr[1]; 

	$sql_all_batch=sql_select("select id,color_id from pro_batch_create_mst where booking_no='$booking_no' and color_id=$color_id and entry_form=0 and status_active = 1 and is_deleted = 0");
	foreach ($sql_all_batch as $row)
	{
		$basic_color_no_arr[$row[csf('id')]]= $color_arr[$row[csf('color_id')]];
	}
	
	$basic_color_no=create_drop_down( "txt_color_type", 270, $basic_color_no_arr,"", 1, "-- select --","");
	echo "document.getElementById('color_type_td').innerHTML = '".$basic_color_no."';\n";
	exit();
}
if ($action=="listview_basic_info")
{
	$booking_id=$data;
	$sql_result =sql_select("SELECT a.id,a.req_id,a.company_id,a.booking_no,a.booking_id, a.booking_sl_no,a.fabric_color_id,a.color_type_id,a.fabric_desc,a.fin_dia_type,a.dia,a.fin_gsm,a.deter_id from sample_archive_basic_info a where a.booking_id=$booking_id and a.is_deleted=0  and a.status_active=1  order by a.id asc");

				
	?>
    <table width="710" cellspacing="0" border="1" rules="all" class="rpt_table" >
        <thead>
			
            <th width="100">Fab. Color/Code</th>
            <th width="100">Finished <br>Dia Type</th>
            <th width="200">Fabrication</th>
            <th width="100">Color Type</th>
            <th width="70">Finished Dia</th>
            <th width="70">Booking<br> SL No.</th>
            <th width="">Finished GSM</th>
          
        </thead>
    </table>
    <div style="width:730px; overflow-y:scroll; max-height:180px;">
        <table width="710" cellspacing="0" border="1" rules="all" class="rpt_table" id="tbl_details">
			<?
				$i=1;
               	foreach ($sql_result as $row)
               	{
				   if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
            		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]; ?>', 'populate_details_form_data', 'requires/basic_info_entry_controller');"> 
						
                		<td width="100"><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></td>
                        <td width="100"><? echo $row[csf('fin_dia_type')]; ?></td>
                        <td width="200" style="word-break:break-all"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
                        <td width="100"><? echo $row[csf('color_type_id')]; ?>&nbsp;</td>
                        <td align="center" width="70"><? echo $row[csf('dia')]; ?></td>
                        <td align="center" width="70"><? echo $row[csf('booking_sl_no')]; ?></td>
                        <td align="center" width=""><? echo $row[csf('fin_gsm')]; ?></td>
               		</tr>
				<? 
               		$i++; 
			   	}
            ?>
        </table>
    </div>
    <?
	exit();
}

 
if($action=="populate_details_form_data")
{  
 	
	$sql_result =sql_select("SELECT a.id,a.req_id,a.company_id,a.booking_no,a.booking_id, a.booking_sl_no,a.fabric_color_id,a.color_type_id,a.fabric_desc,a.fin_dia_type,a.dia,a.fin_gsm,a.deter_id from sample_archive_basic_info a where a.id=$data and a.is_deleted=0  and a.status_active=1  order by a.id asc");
	
	foreach($sql_result as $row)
	{ 
 		//echo "load_drop_down( 'requires/sample_checklist_controller', '".$result[csf('id')]."', 'load_drop_down_gmts', 'gmts_td' );\n";
 		echo "$('#update_id').val('".$row[csf('id')]."');\n";
		echo "$('#cbo_fab_color_code').val('".$row[csf('fabric_color_id')]."');\n";
		echo "$('#cbo_fabrication').val('".$row[csf('deter_id')]."');\n";
		echo "$('#txt_color_type').val('".$row[csf('color_type_id')]."');\n";
		echo "$('#txt_booking_sl_no').val('".$row[csf('booking_sl_no')]."');\n";
		echo "$('#finish_dia_type').val('".$row[csf('fin_dia_type')]."');\n";
		echo "$('#finished_gsm').val('".$row[csf('fin_gsm')]."');\n";
		echo "$('#finish_dia').val('".$row[csf('dia')]."');\n";
		
   	}
	   echo "$('#cbo_fab_color_code').val('".$row[csf('fabric_color_id')]."');\n";
	   echo "$('#cbo_fabrication').val('".$row[csf('deter_id')]."');\n";
	   echo "$('#txt_color_type').val('".$row[csf('color_type_id')]."');\n";
	   echo "$('#txt_booking_sl_no').val('".$row[csf('booking_sl_no')]."');\n";
	   echo "$('#finish_dia_type').val('".$row[csf('fin_dia_type')]."');\n";
	   echo "$('#finished_gsm').val('".$row[csf('fin_gsm')]."');\n";
	   echo "$('#finish_dia').val('".$row[csf('dia')]."');\n";
	 	 
		if(count($sql_result)>0)
		{
			
			   echo "$('#update1').removeAttr('onclick').attr('onclick','show_button_disable_msg(1);')\n";
			   echo "$('#save1').removeClass('formbutton').addClass('formbutton_disabled');\n"; //formbutton 
			   echo "$('#update1').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
  			   echo "$('#update1').removeAttr('onclick').attr('onclick','fnc_basic_entry(1);')\n";

			   echo "$('#Delete1').removeClass('formbutton_disabled').addClass('formbutton');\n"; //formbutton 
			   echo "$('#Delete1').removeAttr('onclick').attr('onclick','fnc_basic_entry(2);')\n"; 
			   
			
		}
		else
		{
			echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_basic_entry',1);\n"; 
		}
		
   	unlink($sql_result);
 	exit();	
}	


if ($action=="save_update_delete_basic")
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
 		$id_mst=return_next_id( "id", "sample_archive_basic_info", 1 ) ;
 		//$cbo_company_name=return_field_value("company_id","sample_development_mst","entry_form_id in (117,203,449) and id=$requisition_hidden_id");
 

 		$field_array="id, req_id,company_id,booking_no,booking_id, booking_sl_no,fabric_color_id,color_type_id,fabric_desc,fin_dia_type,dia,fin_gsm,deter_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id_mst.",".$req_id.",".$company_id.",".$txt_booking_no.",".$txt_booking_id.",".$txt_booking_sl_no.",".$cbo_fab_color_code.",".$txt_color_type.",'".$fabric_desc."',".$finish_dia_type.",".$finish_dia.",".$finished_gsm.",".$cbo_fabrication.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$rID=sql_insert("sample_archive_basic_info",$field_array,$data_array,1);
		//echo "10**insert into sample_archive_basic_info ($field_array) values $data_array";die;
		 //echo $rID." data array ".$data_array; die;
		 if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con); 
				echo "0"."**".$id_mst."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con);
				echo "10".$id_mst;
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
  		$field_array="booking_sl_no*fabric_color_id*color_type_id*fabric_desc*deter_id*fin_dia_type*dia*fin_gsm*updated_by*update_date";
		$data_array="".$txt_booking_sl_no."*".$cbo_fab_color_code."*".$txt_color_type."*'".$fabric_desc."'*".$cbo_fabrication."*".$finish_dia_type."*".$finish_dia."*".$finished_gsm."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
 		$rID=sql_update("sample_archive_basic_info",$field_array,$data_array,"id","".$update_id."",1);
		//echo "10**=".$data_array.'=';die;
 		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
 				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID1=sql_delete("sample_archive_basic_info",$field_array,$data_array,"id","".$update_id."",0);
		 
		if($db_type==2 || $db_type==1 )
		{
			if($rID1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$req_id)."**".str_replace("'","",$txt_booking_id);
				//echo "2**".str_replace("'","",$update_id)."Working";
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con); die;
	}
}

if ($action=="save_update_delete_dtls")
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
 		$id_dtls=return_next_id( "id", "sample_checklist_dtls", 1 ) ;
 		$a=explode(',', $checkArrayNo);
 	   //  echo "1505***".$checkArrayNo;die;
  		$field_array="id,checklist_mst_id,checklist_id,requisition_id,inserted_by,insert_date,status_active,is_deleted";
  		//$checklist_ids="";
		for ($i=0;$i<count($a);$i++)
		{
			$checklist_ids=$a[$i];
			//$updateIdDtls="updateidRequiredEmbellishdtl_".$i;
			if ($i!=0) $data_array .=",";
			$data_array .="(".$id_dtls.",".$update_id.",".$checklist_ids.",".$requisition_hidden_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			$id_dtls=$id_dtls+1;
		 }
		 //  echo "7200 ".$checklist_ids;die;
  		//echo "5**INSERT INTO sample_checklist_dtls(".$field_array.") VALUES ".$data_array; die;
 		$rID=sql_insert("sample_checklist_dtls",$field_array,$data_array,1);
 		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$update_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con); 
				echo "0**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$update_id;
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
  		$field_array_st="is_deleted";
		$data_array_st="1";
		//  echo "3090ass ".trim($updateDtls,',');die;
		
		if(trim($updateDtls,',')!="") 
		{
 			$rID=sql_multirow_update("sample_checklist_dtls",$field_array_st,"1","checklist_mst_id",$update_id,0); 
			$rID2=sql_multirow_update("sample_checklist_dtls",$field_array_st,"0","id",trim($updateDtls,','),0);
		}

		if($updateDtls=="") 
		{
 			$rID=sql_multirow_update("sample_checklist_dtls",$field_array_st,"1","checklist_mst_id",$update_id,0); 
 		}
 
		$id_dtls=return_next_id( "id", "sample_checklist_dtls", 1 ) ;
 		$a=explode(',', $forNewSave);
   		$field_array_new="id,checklist_mst_id,checklist_id,requisition_id,inserted_by,insert_date,status_active,is_deleted";
		for ($i=0;$i<count($a);$i++)
    	{
			$checklist_ids=$a[$i];
				if ($i!=0) $data_array_new .=",";
		    $data_array_new .="(".$id_dtls.",".$update_id.",".$checklist_ids.",".$requisition_hidden_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			    $id_dtls=$id_dtls+1;
		
   		 }
   		//echo "5**INSERT INTO sample_checklist_dtls(".$field_array.") VALUES ".$data_array; die;
 		$rID3=sql_insert("sample_checklist_dtls",$field_array_new,$data_array_new,1);
  		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
 				echo "1**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID1=sql_delete("sample_checklist_mst",$field_array,$data_array,"id","".$update_id."",0);
		 
		if($db_type==0)
		{
			if($rID1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$update_id);
			}
		}
		disconnect($con); die;
	}
}

if($action=="load_php_dtls_form")
{
	$sql_sam="select id,checklist_mst_id,checklist_id,requisition_id from sample_checklist_dtls where checklist_mst_id='$data' and is_deleted=0  and status_active=1 order by id ASC"; 
	$existArr=return_library_array( "select checklist_id,id  from sample_checklist_dtls where checklist_mst_id='$data' and is_deleted=0  and status_active=1 ", "checklist_id", "id" );
	$sql_result =sql_select($sql_sam); 
	$i=1;
	//echo count($sql_result);die;
	if(count($sql_result)>0)
	{	
		foreach($sample_checklist_set as $id=>$name)
		{
			?>
			<tr id="tr_<? echo $i; ?>" style="height:10px;" >
                <th align="left"> 
					<?
                    if($existArr[$id]!='')
                    {
                        ?>
                        <input type="checkbox" checked name="txtCheckBoxId_<? echo $i ?>" id="txtCheckBoxId_<? echo $i ?>" />
                        <?
                    }
                    else
                    {
                        ?>
                        <input type="checkbox" name="txtCheckBoxId_<? echo $i ?>" id="txtCheckBoxId_<? echo $i ?>" />
                        <?
                    }
                    ?>
                    &nbsp; <? echo $name; ?> 
                    <input type="hidden" name="txtDocumentSetArrayid_<?php echo $i ?>" id="txtDocumentSetArrayid_<?php echo $i ?>" value="<? echo $id; ?>"/>
                    <input type="hidden" name="updateDtlsId_<?php echo $i ?>" id="updateDtlsId_<?php echo $i ?>" value="<? if($existArr[$id]!='') echo $existArr[$id]; else echo '';?>" />
                </th>
			</tr>
			<?
			$i++;
		}
	}
	exit();
}

if($action=="booking_data")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=2 and report_id=90 and is_deleted=0 and status_active=1");
	
	$sql= "SELECT a.company_id, a.is_approved, b.booking_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where  a.booking_no=b.booking_no and b.style_id='$data' and a.status_active=1 and a.is_deleted=0 and a.entry_form_id=140 and b.status_active=1 and b.is_deleted=0 and b.entry_form_id=140 group by a.company_id,  a.is_approved, b.booking_no";
	$sql_res=sql_select($sql);
	
	$company_id=$sql_res[0][csf('company_id')];
	$booking_no=$sql_res[0][csf('booking_no')];
	$is_approved=$sql_res[0][csf('is_approved')];
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_id."' and module_id=2 and report_id=90 and is_deleted=0 and status_active=1");
	
	$print_report_id=explode(",",$print_report_format);
	foreach($print_report_id as $button_id)
	{
		if($button_id==10) $booking_btn='1';
		if($button_id==17) $booking_btn='2';
	}
	
	echo $booking_no.'__'.$booking_btn.'__'.$company_id.'__'.$is_approved.'__2';
	exit();
}

if($action=="receive_popup")
{
	extract($_REQUEST); 
    echo load_html_head_contents("Finish Fabric Receive Details", "../../", 1, 1,$unicode,'','');
	$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$product_details=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
		<div style="width:985px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:980px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="10"><b>Fabric Receive Info</b></th>
					</thead>
					<thead>
	                	<th width="30">SL</th>
	                    <th width="110">Receive ID</th>
	                    <th width="130">Insart Date and Time</th>
	                    <th width="90">Batch No.</th>
	                    <th width="170">Fabric Description</th>
	                    <th width="40">Uom</th>
	                    <th width="100">Fabric Color</th>
                        <th width="40">F. Shade</th>
	                    <th width="70">Receive Qty</th>
	                    <th>Remarks</th>
					</thead>
	             </table>
	             <div style="width:987px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
	                    <?
	                    $i=1;
	                    $total_fabric_recv_qnty=0; $dye_company='';
	                    $sql="(select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, a.insert_date, c.batch_no, b.prod_id, b.uom, b.color_id, b.fabric_shade, b.remarks, sum(b.receive_qnty) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id  and a.receive_basis=5 and a.entry_form in (7,37) and c.booking_without_order=1 and c.booking_no='$booking_no' and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, a.insert_date, c.batch_no, b.prod_id, b.uom, b.color_id, b.fabric_shade, b.remarks)
						union all
						( select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, a.insert_date, c.batch_no, b.prod_id, b.uom, b.color_id, b.fabric_shade, b.remarks, sum(b.receive_qnty) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, inv_receive_master d where a.id=b.mst_id and b.batch_id=c.id and a.booking_no=d.recv_number and a.receive_basis=9 and d.receive_basis=5 and a.entry_form =37 and d.entry_form =7 and c.booking_without_order=1 and c.booking_no='$booking_no' and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, a.insert_date, c.batch_no, b.prod_id, b.uom, b.color_id, b.fabric_shade, b.remarks)";
						
						//echo $sql;
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
	                    
	                        $total_fabric_recv_qnty+=$row[csf('quantity')];
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="30"><? echo $i; ?></td>
	                            <td width="110" style="word-break:break-all"><? echo $row[csf('recv_number')]; ?></td>
	                            <td width="130" align="center"><? echo $row[csf('insert_date')]; ?></td>
                                <td width="90" style="word-break:break-all"><? echo $row[csf('batch_no')]; ?></p></td>
                                <td width="170" style="word-break:break-all"><? echo $product_details[$row[csf('prod_id')]]; ?></td>
	                            <td width="40"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                                <td width="100" style="word-break:break-all"><? echo $color_array[$row[csf('color_id')]]; ?></td>
	                            <td width="40"><? echo $fabric_shade[$row[csf('fabric_shade')]]; ?></td>
                                <td width="70" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
	                            <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
	                        </tr>
	                    <?
	                    $i++;
	                    }
	                    ?>
	                    <tfoot>
	                        <th colspan="8" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
	                        <th>&nbsp;</th>
	                    </tfoot>
	                </table>
	            </div>	
	        </div>
		</fieldset>   
	<?
	exit();
}
if($action=="Image_check"){

	$nameArray=sql_select( "select id,image_location,master_tble_id,details_tble_id,form_name,file_type,real_file_name,INSERT_DATE from common_photo_library where master_tble_id='$data' and form_name='sample_checklist_pattern_img' and file_type=1 " );

		$flag=0;
			if (count($nameArray)>0) 
			{
				echo 1;die;
				/* foreach ($nameArray as $inf)
				{
					$inf[csf("INSERT_DATE")]=date('d-m-Y h:i:s a',strtotime($inf[csf("INSERT_DATE")]));
					$ext =strtolower( get_file_ext($inf[csf("image_location")]));
					//echo $ext;die;
					if($ext=="jpg" || $ext=="jpeg" || $ext=="png" || $ext=="bmp")
					{
						
						
					}
				} */
			

			}else{
				echo 0;die;
			}
			//echo $flag;
			exit();
			
}

?>