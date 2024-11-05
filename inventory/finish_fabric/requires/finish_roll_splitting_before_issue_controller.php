<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$hidden_dtls_id=str_replace("'","",$hidden_dtls_id);
	
	$dtls_sql=sql_select("select a.id, a.mst_id, a.trans_id, a.prod_id, a.body_part_id, a.febric_description_id, gsm, a.width, a.no_of_roll, a.order_id, a.grey_receive_qnty, a.reject_fabric_receive, a.rate, a.amount, a.uom, a.yarn_lot, a.yarn_count, a.brand_id, a.shift_name, a.floor_id, a.machine_no_id, a.room, a.rack, a.self, a.bin_box, a.color_id, a.color_range_id, a.stitch_length,a.kniting_charge, a.yarn_rate, a.inserted_by, a.insert_date,b.is_transfer ,b.transfer_criteria ,b.from_roll_id  from pro_grey_prod_entry_dtls a, pro_roll_details b where b.dtls_id=a.id and b.entry_form in(22,58,2) and b.barcode_no=$hidden_barcode");

	foreach($dtls_sql as $inf)
	{
		$trans_id=$inf[csf('trans_id')];	
		$prod_id=$inf[csf('prod_id')];
		$body_part_id=$inf[csf('body_part_id')];
		$febric_description_id=$inf[csf('febric_description_id')];
		$gsm=$inf[csf('gsm')];
		$width=$inf[csf('width')];
		$order_id=$inf[csf('order_id')];
		$rate=$inf[csf('rate')];
		$amount=$inf[csf('amount')];
		$uom=$inf[csf('uom')];
		$yarn_lot=$inf[csf('yarn_lot')];
		$yarn_count=$inf[csf('yarn_count')];
		$brand_id=$inf[csf('brand_id')];
		$shift_id=$inf[csf('shift_name')];	
		$floor_id=$inf[csf('floor_id')];
		$machine_no_id=$inf[csf('machine_no_id')];
		$room=$inf[csf('room')];
		$rack=$inf[csf('rack')];
		$self=$inf[csf('self')];
		$bin_box=$inf[csf('bin_box')];
		$color_id=$inf[csf('color_id')];
		$color_range_id=$inf[csf('color_range_id')];
		$stitch_length=$inf[csf('stitch_length')];
		$kniting_charge=$inf[csf('kniting_charge')];
		$yarn_rate=$inf[csf('yarn_rate')];
		
		$transfer_criteria=$inf[csf('transfer_criteria')];
		$is_transfer=$inf[csf('is_transfer')];
		$from_roll_id=$inf[csf('from_roll_id')];
	}
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		
		//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$hidden_company_id), '', 'FRSBI', date("Y",time()), 5, "select system_number_prefix, system_number_prefix_num from pro_roll_split where company_id=$hidden_company_id and entry_form=141 and $year_cond=".date('Y',time())." order by id desc","system_number_prefix", "system_number_prefix_num" ));
		
		//$id=return_next_id( "id", "pro_roll_split", 1 ) ;
		
		$id = return_next_id_by_sequence("PRO_ROLL_SPLIT_PK_SEQ", "pro_roll_split", $con);
		$new_mrr_number = explode("*", return_next_id_by_sequence("PRO_ROLL_SPLIT_PK_SEQ", "pro_roll_split",$con,1,$hidden_company_id,'RRS',141,date("Y",time()),2 ));
				 
		$field_array="id,system_number_prefix,system_number_prefix_num,system_number,company_id,roll_id,roll_no,order_id,roll_wgt,barcode_no,entry_form,split_from_id,inserted_by,insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',".$hidden_company_id.",".$hidden_rollId.",".$hidden_roll_name.",".$hidden_po_breakdown_id.",".$hidden_roll_wgt.",".$hidden_barcode.",141,".$hidden_table_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
	
		
		
		$maxRollNo=return_field_value("max(roll_no) as roll_no","pro_roll_details","entry_form in(2,22,62,58,83) and po_breakdown_id=$hidden_po_breakdown_id and booking_without_order=$booking_without_order",'roll_no'); 
		//echo "10**".$maxRollNo;die;	
	    //$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, qc_pass_qnty, reject_qnty, roll_id, roll_no, roll_split_from, booking_without_order ,booking_no, inserted_by, insert_date ,from_roll_id";
		
		$barcode_year=date("y");
		$hidden_transfer_mother_roll =str_replace("'","",$hidden_transfer_mother_roll);
		$hidden_entry_form =str_replace("'","",$hidden_entry_form);
		/*if(!empty($hidden_transfer_mother_roll)) $grey_entry_form=$hidden_transfer_mother_roll;
		elseif($hidden_entry_form==58) 	$grey_entry_form=2;
		else 						$grey_entry_form=$hidden_entry_form;*/
		
		$grey_entry_form=$hidden_entry_form;
		
		//$barcode_suffix_no=return_field_value("max(barcode_suffix_no) as suffix_no","pro_roll_details","barcode_year=$barcode_year","suffix_no");
		if($grey_entry_form<10) $grey_entry_form=str_pad($grey_entry_form,2,"0",STR_PAD_LEFT);
		//else $hidden_entry_form=str_replace("'","",$hidden_entry_form);

		$barcodeNos=''; $prod_id_array=array(); $prod_data_array=array(); $prod_new_array=array(); $company_id=str_replace("'","",$cbo_company_id); $z=1; 
		$total_split_qty=0;
		//$field_array_dtls="id, mst_id, trans_id, prod_id, body_part_id, febric_description_id, gsm, width, no_of_roll, order_id, grey_receive_qnty, reject_fabric_receive, rate, amount, uom, yarn_lot, yarn_count, brand_id, shift_name, floor_id, machine_no_id, room, rack, self, bin_box, color_id, color_range_id, stitch_length,kniting_charge, yarn_rate, inserted_by, insert_date";
		//if(str_replace("'","",$booking_without_order)==0) $booking_number=
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$rollNo=$maxRollNo+1;
			$maxRollNo+=1;
			$update_roll_id="update_roll_id_".$j;
			$rollWgt="rollWgt_".$j;
			$roll_reject_qty=0;
			//$barcode_suffix_no=$barcode_suffix_no+1;
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$barcode_suffix_no = explode("*", return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "pro_roll_details",$con,1,0,'',2,date("Y",time()),2 ));
			$barcode_no=$barcode_year."".$grey_entry_form."".str_pad($barcode_suffix_no[2],7,"0",STR_PAD_LEFT);
			
			if($data_array_dtls!="") $data_array_dtls.=",";

			$data_array_roll.="(".$id_roll.",".$barcode_year.",".$barcode_suffix_no[2].",".$barcode_no.",".$hidden_mst_id.",".$hidden_dtls_id.",".$hidden_po_breakdown_id.",".$hidden_entry_form.",'".$$rollWgt."','".$$rollWgt."','".$roll_reject_qty."',".$hidden_rollId.",'".$rollNo."',".$hidden_table_id.",".$booking_without_order.",'".$booking_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_table_id.")";
			$total_split_qty+=str_replace("'","",$$rollWgt);
			$barcodeNos.=$barcode_no."__".$id_dtls."__".$id_roll.",";
			//$id_roll = $id_roll+1;
		}
		
		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		//$grey_receive_qnty=return_field_value("grey_receive_qnty","pro_grey_prod_entry_dtls","id=".$hidden_dtls_id."",'grey_receive_qnty'); 
		$update_roll_wgt=str_replace("'","",$txt_original_wgt)-$total_split_qty;
		//$grey_receive_qnty=$grey_receive_qnty-$total_split_qty;
		$flag=1;
		$rID=sql_insert("pro_roll_split",$field_array,$data_array,0);
		if($rID) $flag=1; else $flag=0; 
		
		/*	if($hidden_entry_form==58)
		{
			//$rID2=sql_insert("pro_grey_prod_entry_dtls",$field_array_dtls,$data_array_dtls,0);
			//if($flag==1) 
			//{
			//	if($rID2) $flag=1; else $flag=0; 
			//}
			//$field_array_dtls_update="grey_receive_qnty*updated_by*update_date";
			//$data_array_dtls_update="".$update_roll_wgt."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			////if($flag==1) 
			//{
			//	$rID4=sql_update("pro_grey_prod_entry_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$hidden_dtls_id,1);
				//if($rID4) $flag=1; else $flag=0; 
			//}
		}*/
		
		
		if($flag==1) 
		{
			$rID3=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($rID3) $flag=1; else $flag=0; 
		}
		
		
		$field_array_roll_update="qnty*qc_pass_qnty*updated_by*update_date";
		$data_array_roll_update="".$update_roll_wgt."*".$update_roll_wgt."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		if($flag==1) 
		{
			$rID5=sql_update("pro_roll_details",$field_array_roll_update,$data_array_roll_update,"id",$hidden_table_id,1);
			if($rID5) $flag=1; else $flag=0; 
		}
		
		//echo "10**$rID=$rID3=$rID5";die;
	 
		if($db_type==0)
		{
			if($flag==1) 
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_mrr_number[0]."**".substr($barcodeNos,0,-1)."**".str_replace("'","",$hidden_table_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1) 
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0]."**".substr($barcodeNos,0,-1)."**".str_replace("'","",$hidden_table_id);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
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
		
		
			
	   // $id_roll = return_next_id( "id", "pro_roll_details", 1 );
		//$field_array_roll="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, reject_qnty, roll_id,roll_no,roll_split_from,booking_without_order ,booking_no, inserted_by, insert_date ,is_transfer ,transfer_criteria ,from_roll_id";
		
		$field_array_roll="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, qc_pass_qnty, reject_qnty, roll_id, roll_no, roll_split_from, booking_without_order ,booking_no, inserted_by, insert_date ,from_roll_id";
				
		$hidden_entry_form=str_replace("'","",$hidden_entry_form);
		/*if($hidden_entry_form==58) 	$grey_entry_form=2;
		else 						$grey_entry_form=$hidden_entry_form;*/
		
		$grey_entry_form=$hidden_entry_form;
		
		$barcode_year=date("y");  
		//$barcode_suffix_no=return_field_value("max(barcode_suffix_no) as suffix_no","pro_roll_details","barcode_year=$barcode_year","suffix_no");
		if(str_replace("'","",$grey_entry_form)<10) $grey_entry_form=str_pad($grey_entry_form,2,"0",STR_PAD_LEFT);
		$maxRollNo=return_field_value("max(roll_no) as roll_no","pro_roll_details","entry_form in(2,22,58,83) and po_breakdown_id=$hidden_po_breakdown_id and booking_without_order=$booking_without_order",'roll_no'); 
		$barcodeNos='';
		$total_split_qty=0;
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			//$rollNo="roll_no_".$j;
			$update_roll_id="update_roll_id_".$j;
			$rollWgt="rollWgt_".$j;
			$barcodeNo="barcodeNo_".$j;
			$update_dtls_id="update_dtls_id_".$j;
			$roll_reject_qty=0;
		
			if(str_replace("'","",$$update_roll_id)!="")
			{
				$total_split_qty+=str_replace("'","",$$rollWgt);
				$update_roll_arr[]=$$update_roll_id;
				$data_array_roll_update[$$update_roll_id]=explode("*",($$rollWgt."*".$$rollWgt."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$barcodeNos.=$$barcodeNo."__".$$update_dtls_id."__".$$update_roll_id.",";
			}
			else
			{
				$rollNo=$maxRollNo+1;
				$maxRollNo+=1;
				
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				
				//$barcode_suffix_no=$barcode_suffix_no+1;
				$barcode_suffix_no = explode("*", return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "pro_roll_details",$con,1,0,'',2,date("Y",time()),2 ));
				$barcode_no=$barcode_year."".$grey_entry_form."".str_pad($barcode_suffix_no[2],7,"0",STR_PAD_LEFT);
		
				
				/*$data_array_roll.="(".$id_roll.",".$barcode_year.",".$barcode_suffix_no.",".$barcode_no.",".$hidden_mst_id.",".$hidden_dtls_id.",".$hidden_po_breakdown_id.",".$hidden_entry_form.",'".$$rollWgt."','".$$rollWgt."','".$roll_reject_qty."',".$hidden_rollId.",'".$rollNo."',".$hidden_table_id.",".$booking_without_order.",'".$booking_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$is_transfer."','".$transfer_criteria."','".$from_roll_id."')";*/
				
				$data_array_roll.="(".$id_roll.",".$barcode_year.",".$barcode_suffix_no[2].",".$barcode_no.",".$hidden_mst_id.",".$hidden_dtls_id.",".$hidden_po_breakdown_id.",".$hidden_entry_form.",'".$$rollWgt."','".$$rollWgt."','".$roll_reject_qty."',".$hidden_rollId.",'".$rollNo."',".$hidden_table_id.",".$booking_without_order.",'".$booking_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_table_id.")";
				
				$total_split_qty+=str_replace("'","",$$rollWgt);
				$barcodeNos.=$barcode_no."__".$id_dtls."__".$id_roll.",";
				//$id_roll = $id_roll+1;
			}
		}
		
		
		$field_array_roll_deleted="updated_by*update_date*status_active*is_deleted";
		$deleted_all_id=str_replace("'","",$deleted_all_id);
		
		if($deleted_ids!="")
		{
			$deleted_ids=explode(",",$deleted_all_id);
			foreach($deleted_ids as $ids)
			{
				$id_detals=explode("**",$ids);
				$deleted_roll_id[]=$id_detals[0];
				//$deleted_detls_id[]=$id_detals[1];
				$data_array_roll_deleted[$id_detals[0]]=explode("*",($_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
			}
		}
		
		$field_array_roll_update="qnty*qc_pass_qnty*updated_by*update_date";
		$update_roll_arr[]=str_replace("'","",$hidden_table_id);
		$update_roll_wgt=str_replace("'","",$txt_original_wgt)-$total_split_qty;
		$data_array_roll_update[str_replace("'","",$hidden_table_id)]=explode("*",($update_roll_wgt."*".$update_roll_wgt."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		
		$flag=1;
		
		if(count($data_array_roll_deleted)>0)
		{
			$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_deleted, $data_array_roll_deleted, $deleted_roll_id ));
			if($flag==1) 
			{
				if($rollUpdate) $flag=1; else $flag=0; 
			} 
		}
		
		
		
		if(count($data_array_roll_update)>0)
		{
			$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $update_roll_arr ));
			if($flag==1) 
			{
				if($rollUpdate) $flag=1; else $flag=0; 
			} 
		}
		
		if($data_array_roll!="")
		{
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}
		}
		/*echo "10**";
		echo $flag;die;*/
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no)."**".substr($barcodeNos,0,-1)."**".str_replace("'","",$hidden_table_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no)."**".substr($barcodeNos,0,-1)."**".str_replace("'","",$hidden_table_id);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="mrr_popup")
{
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(id)
		{
			
			$('#hidden_system_id').val(id);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:760px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Company Name</th>
                     <th>System No</th>
                     <th>Barcode No</th>
                    <th id="search_by_td_up" width="180">Insert Date</th>
                    <th>
                
                    	<input type="hidden" name="hidden_system_id" id="hidden_system_id">  
                    </th> 
                </thead>
                <tr class="general">
                	<td>
					<? 
                        echo create_drop_down( "cbo_company_id", 151, "select comp.id, comp.company_name from lib_company comp 
                        where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, 
                        "--Select Company--", 0, "" );
                    ?>
                    </td>
                   
                    <td align="center" >				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_system_no" id="txt_system_no" />	
                    </td>
                     <td align="center" >				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_barcode_no" id="txt_barcode_no" />	
                    </td>
                     <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
					</td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_barcode_no').value, 'create_challan_search_list_view', 'search_div', 'finish_roll_splitting_before_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	$company_id =$data[0];
	
	$system_id=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$barcode_no=$data[4];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and TIME_FORMAT(a.insert_date, '%Y-%m-%d' ) between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and TO_CHAR(a.insert_date,'YYYY-MM-DD') between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	
	if(trim($system_id)!="") $search_field_cond=" and a.system_number_prefix_num=$system_id ";
	if(trim($barcode_no)!="") $search_field_cond.=" and a.barcode_no='$barcode_no'";
	if(trim($company_id)==0) { echo "Please insert Company First";die;}

	
	$sql = "select a.id, system_number,a.roll_no,a.split_from_id,a.insert_date,a.company_id,a.order_id,a.barcode_no,a.roll_wgt, b.booking_without_order, b.booking_no  from pro_roll_split a,pro_roll_details b 
	where a.split_from_id=b.id and b.entry_form in(7,37,68) and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond order by id"; 
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="130">System No</th>
            <th width="120">Company Name</th>
            <th width="120">Order No</th>
            <th width="120">Booking No</th>
            <th width="90">Barcode No</th>
            <th width="50">Roll No</th>
            <th>Insert date</th>
        </thead>
	</table>
	<div style="width:760px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_no="";
				$booking_number="";	
				if($row[csf('booking_without_order')]==1)
				{
					$booking_number=$row[csf('booking_no')];
				}
				else
				{
					$order_no=$order_arr[$row[csf('order_id')]];
				}
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('system_number')]."_".$row[csf('id')]."_".$row[csf('barcode_no')]."_".$row[csf('split_from_id')]."_".$row[csf('roll_wgt')]; ?>');"> 
                    <td width="30"><? echo $i; ?></td>
                    <td width="130"><p>&nbsp;<? echo $row[csf('system_number')]; ?></p></td>
                    <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $order_no; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $booking_number; ?>&nbsp;</p></td>
                    <td width="90"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                    <td width="50"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('insert_date')]); ?></td>
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

if($action=="roll_details_update")
{
	//$data=explode("_",$data);
	
	$sql=sql_select("select a.id,a.barcode_no,a.roll_no,a.qc_pass_qnty,a.dtls_id as dtls_id from  pro_roll_details a where  a.roll_split_from=$data and a.status_active=1 and a.is_deleted=0 order by a.id" );
	$i=1;
	foreach($sql as $row)
	{
	?>
    	<tr id="tr_<? echo $i;  ?>" align="center" valign="middle">
            <td width="40" id="txtSl_<? echo $i;  ?>"><? echo $i;  ?></td>
            <td width="100" >
            <input type="text" name="roll_no[]" id="rollno_<? echo $i;  ?>" style="width:80px" class="text_boxes_numeric" onBlur="check_roll_no(<? echo $i;  ?>)" value="<? echo $row[csf('roll_no')] ;  ?>" disabled/>
            </td>
            <td width="60" >
            <input type="text" name="rollWgt[]" id="rollWgt_<? echo $i;  ?>" style="width:50px" class="text_boxes_numeric"   onBlur="check_qty(<? echo $i;  ?>)" value="<? echo $row[csf('qc_pass_qnty')] ;  ?>"/>
            </td>
            <td width="180" >
            <input type="text" name="barcodeNo[]" id="barcodeNo_<? echo $i;  ?>" style="width:150px" class="text_boxes" value="<? echo $row[csf('barcode_no')] ;  ?>"  placeholder="Display" readonly/>
            </td>
            <td id="button_1" align="center">
             <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i;  ?>)" />
             <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i;  ?>);" />
             <input type="hidden" name="updateRollId[]" id="updateRollId_<? echo $i;  ?>" value="<? echo $row[csf('id')] ;  ?>"/>
             <input type="hidden" name="updateDtlsId[]" id="updateDtlsId_<? echo $i;  ?>" value="<? echo $row[csf('dtls_id')] ;  ?>"/>
               
            </td>
            <td> <input id="chkBundle_<? echo $i; ?>" type="checkbox" name="chkBundle"  >
            	
            
            </td>
        </tr>
	<?
	$i++;	
	}
}

if($action=="load_barcode_mst_form")
{ 
	$product_name_dtls=sql_select( "select a.id, a.product_name_details, c.booking_without_order from product_details_master a,  pro_finish_fabric_rcv_dtls b, pro_roll_details c  where a.id=b.prod_id and b.id=c.dtls_id and c.entry_form in(37,7,68) and c.barcode_no=$data and a.item_category_id=2");
	$prod_name=$product_name_dtls[0][csf("product_name_details")];
	$booking_without_order=$product_name_dtls[0][csf("booking_without_order")];
	if($booking_without_order==1)
	{
		$sql="SELECT a.company_id, a.recv_number, b.prod_id, c.barcode_no, c.roll_no, c.qc_pass_qnty as qnty, c.id as roll_tbl_id, c.roll_id, c.entry_form, c.po_breakdown_id, c.mst_id, c.dtls_id, c.booking_without_order, c.booking_no as po_number,   null as pub_shipment_date, null as job_no_mst 
		FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.roll_split_from=0 and c.is_transfer<>6 and c.booking_without_order=1 and c.barcode_no=$data";
	}
	else
	{
		$sql="SELECT a.company_id, a.recv_number, b.prod_id, c.barcode_no, c.roll_no, c.qc_pass_qnty as qnty, c.id as roll_tbl_id, c.roll_id, c.entry_form, c.po_breakdown_id, c.mst_id, c.dtls_id, c.booking_without_order, d.po_number, d.pub_shipment_date, d.job_no_mst 
		FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.roll_split_from=0 and c.is_transfer<>6 and c.booking_without_order=0 and c.barcode_no=$data";
	}
	 
	//echo $sql;die;

	$data_array=sql_select($sql);


	foreach($data_array as $val)
	{
		echo "$('#txt_bar_code_num').val('".$val[csf("barcode_no")]."');\n";
		echo "$('#txt_fabric_description').val('".$prod_name."');\n"; 
		echo "$('#txt_job_no').val('".$val[csf("job_no_mst")]."');\n";
		echo "$('#txt_order_no').val('".$val[csf("po_number")]."');\n";
		echo "$('#txt_original_roll').val('".$val[csf("roll_no")]."');\n";
		echo "$('#txt_original_wgt').val('".$val[csf("qnty")]."');\n";
		echo "$('#hidden_company_id').val('".$val[csf("company_id")]."');\n";
		echo "$('#hidden_roll_name').val('".$val[csf("roll_no")]."');\n";
		echo "$('#hidden_rollId').val('".$val[csf("roll_id")]."');\n";
		echo "$('#hidden_table_id').val('".$val[csf("roll_tbl_id")]."');\n";
		echo "$('#hidden_barcode').val('".$val[csf("barcode_no")]."');\n";
		echo "$('#hidden_mst_id').val('".$val[csf("mst_id")]."');\n";
		echo "$('#hidden_dtls_id').val('".$val[csf("dtls_id")]."');\n";
		echo "$('#hidden_po_breakdown_id').val('".$val[csf("po_breakdown_id")]."');\n";
		echo "$('#hidden_entry_form').val('".$val[csf("entry_form")]."');\n";
		echo "$('#hidden_roll_wgt').val('".$val[csf("qnty")]."');\n";
		echo "$('#booking_without_order').val('".$val[csf("booking_without_order")]."');\n";
		exit();
		
	}
}


if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
		function js_set_value(id)
		{
			$('#hidden_barcode_nos').val(id);
			parent.emailwindow.hide();
		}

		function fnc_close()
		{
			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
<div align="center" style="width:760px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Order No</th>
                    <th>Barcode No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>'Job No');
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '') ";							
							echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
						?>                   
                    </td> 
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onChange="check_batch(this.value)"/>	
                        
                    </td> 			
                    <td><input type="text" name="barcode_no" id="barcode_no" style="width:120px" class="text_boxes" /></td>    			
            		<td align="center">
                            <input type="hidden" class="text_boxes" id="hidden_batch_id" name="hidden_batch_id">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('barcode_no').value, 'create_barcode_search_list_view', 'search_div', 'finish_roll_splitting_before_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1);'),'js_set_value'" style="width:100px;" />
                     </td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);
	$search_string=trim($data[0]);
	$search_by=trim($data[1]);
	$barcode_no =trim($data[2]);
	
	
	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_no from pro_roll_details where entry_form=71 and status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2",'id','product_name_details');
	
	
	
	if($search_string=="" && $barcode_no=="")
	{
		echo "Please Insert Order Or Barcode No.";die;
	}
	
	
	$search_order_cond="";
	if($search_string!="")
	{
		if($search_by==1) $search_order_cond=" and d.po_number like '%$search_string%'";
		else $search_order_cond=" and d.job_no_mst like '%$search_string%'";
		
		if($barcode_no!="") $barcode_cond=" and c.barcode_no='$barcode_no'";	
		
		$sql="SELECT a.recv_number, b.prod_id, c.barcode_no, c.roll_no, c.qc_pass_qnty as qnty, d.po_number, d.pub_shipment_date, d.job_no_mst 
		FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_po_break_down d 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.roll_split_from=0 and c.is_transfer<>6 and c.booking_without_order=0 $search_order_cond $barcode_cond"; 
		
	}
	else
	{
		if($barcode_no!="") $barcode_cond=" and c.barcode_no='$barcode_no'";
		
		$booking_without_order=return_field_value("c.booking_without_order as booking_without_order","pro_finish_fabric_rcv_dtls b, pro_roll_details c","b.id=c.dtls_id and b.trans_id<>0 and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.roll_split_from=0 and c.is_transfer<>6 and c.booking_without_order=0 $barcode_cond","booking_without_order");
			
		if($booking_without_order==1)
		{
			$sql="SELECT a.recv_number, b.prod_id, c.barcode_no, c.roll_no, c.qc_pass_qnty as qnty, c.booking_no as po_number, null as pub_shipment_date, null as job_no_mst 
			FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.roll_split_from=0 and c.is_transfer<>6 and c.booking_without_order=1 $barcode_cond";
		}
		else
		{
			$sql="SELECT a.recv_number, b.prod_id, c.barcode_no, c.roll_no, c.qc_pass_qnty as qnty, d.po_number, d.pub_shipment_date, d.job_no_mst 
			FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_po_break_down d 
			WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.roll_split_from=0 and c.is_transfer<>6 and c.booking_without_order=0 $barcode_cond";
		}
		 
	}
	
	
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="220">Fabric Description</th>
            <th width="100">Job No</th>
            <th width="110">Order/Booking No</th>
            <th width="80">Shipment Date</th>
            <th width="80">Barcode No</th>
            <th width="50">Roll No</th>
            <th>Roll Qty.</th>
        </thead>
	</table>
	<div style="width:750px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//echo $row[csf('barcode_no')]."_".$product_arr[$row[csf('prod_id')]]."_".$row[csf('job_no_mst')]."_".$row[csf('po_number')]."_".$row[csf('roll_no')]."_".$row[csf('qnty')];	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('barcode_no')]; ?>');"> 
						<td width="30" align="center">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="220"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						<td width="100"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="80" align="center"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="50" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
<?	
exit();
}

if($action=="report_barcode_generation")
{
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$roll_id=sql_select("select roll_id,po_breakdown_id  from pro_roll_details where id in($data)");
	$roll_id_arr=array();
	foreach($roll_id as $val)
	{
		$roll_id_arr[]=	$val[csf('roll_id')];
		$order_id_arr[]=$val[csf('po_breakdown_id')];
	}
	$sql="select a.company_id,a.receive_basis,a.booking_id,a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.color_id, b.febric_description_id,b.insert_date from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.id in (".implode(",", array_unique($roll_id_arr)).")";
	$result=sql_select($sql);
	$party_name=''; $prod_date=''; $order_id=''; $buyer_name=''; $grey_dia=''; $tube_type=''; $program_no=''; $yarn_lot=''; $yarn_count=''; $brand=''; $gsm=''; $finish_dia='';
	foreach($result as $row)
	{
		if($row[csf('knitting_source')]==1)
		{
			$party_name=return_field_value("company_short_name","lib_company", "id=".$row[csf('knitting_company')]);
		}
		else if($row[csf('knitting_source')]==3)
		{
			$party_name=return_field_value("short_name","lib_supplier", "id=".$row[csf('knitting_company')]);
		}
		
		$prod_date=date("d-m-Y",strtotime($row[csf('insert_date')]));
		$prod_time=date("H:i",strtotime($row[csf('insert_date')]));
		
		$order_id=$row[csf('order_id')];
		$gsm=$row[csf('gsm')];
		$finish_dia=$row[csf('width')];
		$color=$color_arr[$row[csf('color_id')]];
		$stitch_length=$row[csf('stitch_length')];
		$yarn_lot=$row[csf('yarn_lot')];
		$brand=$brand_arr[$row[csf('brand_id')]];
		$yarn_count='';
		$count_id=explode(",",$row[csf('yarn_count')]);
		foreach($count_id as $val)
		{
			if($val>0)
			{
				if($yarn_count=="") $yarn_count=$count_arr[$val]; else $yarn_count.=",".$count_arr[$val];
			}
		}

		$machine_data=sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='".$row[csf('machine_no_id')]."'");
		$machine_name=$machine_data[0][csf('machine_no')];
		$machine_dia_width=$machine_data[0][csf('dia_width')];
		$machine_gauge=$machine_data[0][csf('gauge')];
		
		//$buyer_name=return_field_value("short_name","lib_buyer", "id=".$row[csf('buyer_id')]);
		
		$comp='';
		if($row[csf('febric_description_id')]==0 || $row[csf('febric_description_id')]=="")
		{
			$comp=return_field_value("item_description","product_details_master","id=".$row[csf('prod_id')]);
		}
		else
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row[csf('febric_description_id')]);
					
			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}
			
			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}
		
		if($row[csf('receive_basis')]==2)
		{
			$program_data=sql_select("select width_dia_type, machine_dia from ppl_planning_info_entry_dtls where id='".$row[csf('booking_id')]."'");
			$program_no=$row[csf('booking_id')];
			$grey_dia=$program_data[0][csf('machine_dia')]; 
			$tube_type=$fabric_typee[$program_data[0][csf('width_dia_type')]]; 
		}
	}
	//echo "select a.job_no,a.job_no_prefix_num,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)";
	$po_array=array();
	$all_po_id=implode(",",array_unique($order_id_arr));
	$po_sql=sql_select("select a.job_no,a.job_no_prefix_num,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_po_id)");
	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no_prefix_num')]; 
		$po_array[$row[csf('id')]]['grouping']=$row[csf('grouping')]; 
		$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
		$po_array[$row[csf('id')]]['buyer_id']=$buyer_name_array[$row[csf('buyer_name')]]; 
	}
	
	$i=1; $barcode_array=array();
	$query="select id, roll_no, po_breakdown_id, barcode_no, qc_pass_qnty as qnty,booking_without_order from pro_roll_details where id in($data)";
	$res=sql_select($query);
	echo '<table width="800" border="0"><tr>';
	foreach($res as $row)
   	{
		$file_no='';
		$po_number='';
		$job_no='';
		$buyer_name='';
		$reff_no="";
		if($row[csf('booking_without_order')]==0)
		{
			$file_no=$po_array[$row[csf('po_breakdown_id')]]['file_no'];
			$po_number=$po_array[$row[csf('po_breakdown_id')]]['po_no'];
			$job_no=$po_array[$row[csf('po_breakdown_id')]]['job_no'];
			$buyer_name=$po_array[$row[csf('po_breakdown_id')]]['buyer_id'];
			$reff_no=$po_array[$row[csf('po_breakdown_id')]]['grouping'];
		}
		
	
		$barcode_array[$i]=$row[csf('barcode_no')];
		$txt="&nbsp;&nbsp;".$row[csf('barcode_no')]."; ".$party_name." Job No.".$job_no.";<br>";
		$txt .="&nbsp;&nbsp;M/C: ".$machine_name."; M/C Dia X Gauge-".$machine_dia_width."X".$machine_gauge.";<br>";
		$txt .="&nbsp;&nbsp;Date: ".$prod_date.";<br>";
		$txt .="&nbsp;&nbsp;Buyer: ".$buyer_name.", Order No: ". $po_number.";<br>";
		$txt .="&nbsp;&nbsp;".$comp."<br>";
		$txt .="&nbsp;&nbsp;G/Dia: ".$grey_dia."; SL: ".trim($stitch_length)."; ".trim($tube_type)."; F/Dia: ".trim($finish_dia).";<br>";
		$txt .="&nbsp;&nbsp;GSM: ".$gsm."; ";
		$txt .=$yarn_count."; Lot: ".$yarn_lot.";<br>";
		$txt .="&nbsp;&nbsp;Prg: ".$program_no."; Roll Wt: ".number_format($row[csf('qnty')],2,'.','')." Kg;<br>";
		$txt .="&nbsp;&nbsp;Custom Roll No: ". $row[csf('roll_no')].";";
		if(trim($color)!="") $txt .=" Color: ".trim($color).";";
		
		echo '<td style="padding-left:7px;padding-top:10px;padding-bottom:5px"><div id="div_'.$i.'"></div>'.$txt.'</td>';//border:dotted;
		if($i%3==0) echo '</tr><tr>';
    	$i++;
    }
	echo '</tr></table>';
	?>
    
    <script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		var barcode_array =<? echo json_encode($barcode_array); ?>;
		function generateBarcode( td_no, valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#div_"+td_no).show().barcode(value, btype, settings);
		}

		for (var i in barcode_array) 
		{
			generateBarcode(i,barcode_array[i]);
		}
	</script>
    <?
	exit();
}



if($action=="report_barcode_text_file")
{
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$roll_id=sql_select("select roll_id,po_breakdown_id  from pro_roll_details where id in($data)");
	$roll_id_arr=array();
	foreach($roll_id as $val)
	{
		$roll_id_arr[]=	$val[csf('roll_id')];
		$order_id_arr[]=$val[csf('po_breakdown_id')];
	}
	$sql="select a.company_id,a.receive_basis,a.booking_id,a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.color_id, b.febric_description_id,b.insert_date from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.id in (".implode(",", array_unique($roll_id_arr)).")";
	//echo $sql;die;
	$result=sql_select($sql);
	$party_name=''; $prod_date=''; $order_id=''; $buyer_name=''; $grey_dia=''; $tube_type=''; $program_no=''; $yarn_lot=''; $yarn_count=''; $brand=''; $gsm=''; $finish_dia='';
	foreach($result as $row)
	{
		if($row[csf('knitting_source')]==1)
		{
			$party_name=return_field_value("company_short_name","lib_company", "id=".$row[csf('knitting_company')]);
		}
		else if($row[csf('knitting_source')]==3)
		{
			$party_name=return_field_value("short_name","lib_supplier", "id=".$row[csf('knitting_company')]);
		}
		
		$prod_date=date("d-m-Y",strtotime($row[csf('insert_date')]));
		$prod_time=date("H:i",strtotime($row[csf('insert_date')]));
		
		$order_id=$row[csf('order_id')];
		$gsm=$row[csf('gsm')];
		$finish_dia=$row[csf('width')];
		$color=$color_arr[$row[csf('color_id')]];
		$stitch_length=$row[csf('stitch_length')];
		$yarn_lot=$row[csf('yarn_lot')];
		$brand=$brand_arr[$row[csf('brand_id')]];
		$yarn_count='';
		$count_id=explode(",",$row[csf('yarn_count')]);
		foreach($count_id as $val)
		{
			if($val>0)
			{
				if($yarn_count=="") $yarn_count=$count_arr[$val]; else $yarn_count.=",".$count_arr[$val];
			}
		}

		$machine_data=sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='".$row[csf('machine_no_id')]."'");
		$machine_name=$machine_data[0][csf('machine_no')];
		$machine_dia_width=$machine_data[0][csf('dia_width')];
		$machine_gauge=$machine_data[0][csf('gauge')];
		
		//$buyer_name=return_field_value("short_name","lib_buyer", "id=".$row[csf('buyer_id')]);
		
		$comp='';
		if($row[csf('febric_description_id')]==0 || $row[csf('febric_description_id')]=="")
		{
			$comp=return_field_value("item_description","product_details_master","id=".$row[csf('prod_id')]);
		}
		else
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row[csf('febric_description_id')]);
					
			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}
			
			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}
		
		if($row[csf('receive_basis')]==2)
		{
			$program_data=sql_select("select width_dia_type, machine_dia from ppl_planning_info_entry_dtls where id='".$row[csf('booking_id')]."'");
			$program_no=$row[csf('booking_id')];
			$grey_dia=$program_data[0][csf('machine_dia')]; 
			$tube_type=$fabric_typee[$program_data[0][csf('width_dia_type')]]; 
		}
	}
	//echo "select a.job_no,a.job_no_prefix_num,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)";
	$po_array=array();
	$all_po_id=implode(",",array_unique($order_id_arr));
	$po_sql=sql_select("select a.job_no,a.job_no_prefix_num,b.id,b.po_number, b.grouping, b.file_no,a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($all_po_id)");
	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no_prefix_num')]; 
		$po_array[$row[csf('id')]]['grouping']=$row[csf('grouping')]; 
		$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
		$po_array[$row[csf('id')]]['buyer_id']=$buyer_name_array[$row[csf('buyer_name')]]; 
	}
	foreach (glob(""."*.zip") as $filename)
	{			
		@unlink($filename);
	}
	
	$i=1;
	
	
	$i=1; $year=date("y");
	$query="select id, roll_no, po_breakdown_id, barcode_no, qc_pass_qnty as qnty,booking_without_order from pro_roll_details where id in($data)";
	$res=sql_select($query);
	foreach($res as $row)
   	{
		$file_no='';
		$po_number='';
		$job_no='';
		$buyer_name='';
		$reff_no="";
		if($row[csf('booking_without_order')]==0)
		{
			$file_no=$po_array[$row[csf('po_breakdown_id')]]['file_no'];
			$po_number=$po_array[$row[csf('po_breakdown_id')]]['po_no'];
			$job_no=$po_array[$row[csf('po_breakdown_id')]]['job_no'];
			$buyer_name=$po_array[$row[csf('po_breakdown_id')]]['buyer_id'];
			$reff_no=$po_array[$row[csf('po_breakdown_id')]]['grouping'];
		}
	
		//echo $i."--";
		$file_name="NORSEL-IMPORT_".$i;
		$myfile = fopen($file_name.".txt", "w") or die("Unable to open file!");
		$txt ="Norsel_imp\r\n1\r\n";
		$txt .=$party_name." Job No.".$job_no." M/C:".$machine_name."-".$machine_dia_width."X".$machine_gauge."\r\n";
		$txt .= $row[csf('barcode_no')]."\r\n";
		$txt .="ID: ".$row[csf('barcode_no')]." D:".$prod_date." T:".$prod_time."\r\n";
		$txt .=$buyer_name.", Order No:". $po_number."\r\n";
		$txt .=$comp."\r\n";
		$txt .="G/F-Dia:".trim($grey_dia)."/".trim($finish_dia)." ".trim($stitch_length)." ".trim($tube_type)."\r\n";
		$txt .="File No:".$file_no.",Ref.No:".$reff_no."\r\n";
		$txt .="GSM:".$gsm." ";
		$txt .= $yarn_count." ".$brand." Lot:".$yarn_lot."\r\n";
		$txt .="Prg: ".$program_no."/Roll Wt:".number_format($row[csf('qnty')],2,'.','')." Kg "."\r\n";
		$txt .="Roll Sl. ". $row[csf('roll_no')].", ".trim($color)."\r\n";
		
		//Wt:".number_format($row[csf('qnty')],2,'.','')." Kg "."\r\n";
		//$txt .= "Prod Date: ".$prod_date;
		
		fwrite($myfile, $txt);
		fclose($myfile);
		
		$i++;
	}
	//echo "===".$filename; die;
	//$filename="norsel";
	$zip = new ZipArchive();			// Load zip library	
	$filename = str_replace(".sql",".zip",'norsel_bundle.sql');			// Zip name
	if($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE)
	{		// Opening zip file to load files
		$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error;
	}
	
	foreach (glob(""."*.txt") as $filenames)
	{			
	   $zip->addFile($file_folder.$filenames);		
	}
	$zip->close();
	     
	foreach (glob(""."*.txt") as $filename) 
	{			
		@unlink($filename);
	}
	echo "norsel_bundle";
	exit();
}

if($action=="check_barcode_no")
{
	
	$sql="select barcode_no from pro_roll_split where entry_form=113 and status_active=1 and is_deleted=0 and barcode_no=".$data."";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo 1;die;
	}
	else
	{
		$barcodeData=sql_select( "select barcode_no from pro_roll_details where  entry_form=71 and is_returned<>1 and  barcode_no=".$data." and  status_active=1 and is_deleted=0");
		if(count($barcodeData)>0)
		{
			echo 0;die;
		}
		else
		{
			echo 2;die;
		}
	}
	exit();	
}


?>
