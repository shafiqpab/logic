<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "" );	
	exit();	 
}

if ($action=="save_update_delete")
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
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		
		$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GDSR', date("Y",time()), 5, "select sys_number_prefix, sys_number_prefix_num from pro_grey_prod_delivery_mst where company_id=$cbo_company_id and entry_form=56 and $year_cond=".date('Y',time())." order by id desc ", "sys_number_prefix","sys_number_prefix_num"));
		
		$id=return_next_id( "id", "pro_grey_prod_delivery_mst", 1 ) ;
				 
		$field_array="id,sys_number_prefix,sys_number_prefix_num,sys_number,delevery_date,company_id,location_id,knitting_source,knitting_company,entry_form,inserted_by,insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',".$txt_delivery_date.",".$cbo_company_id.",".$cbo_location_id.",".$cbo_knitting_source.",".$knit_company_id.",56,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$field_array_dtls="id, mst_id, entry_form, grey_sys_id, sys_dtls_id, product_id, order_id, determination_id, roll_id, barcode_num, current_delivery, inserted_by, insert_date";
		$dtls_id = return_next_id( "id", "pro_grey_prod_delivery_dtls", 1 );
		
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, booking_no, booking_without_order, inserted_by, insert_date";
		$id_roll = return_next_id( "id", "pro_roll_details", 1 ); 
		
		$barcodeNos=''; $used_roll_ids='';
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$productionId="productionId_".$j;
			$productionDtlsId="productionDtlsId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$deterId="deterId_".$j;
			$rollId="rollId_".$j;
			$barcodeNo="barcodeNo_".$j;
			$currentDelivery="currentDelivery_".$j;
			$rollNo="rollNo_".$j;
			$bookingWithoutOrder="bookingWithoutOrder_".$j;
			$smnBookingNo="smnBookingNo_".$j;
			
			if($$bookingWithoutOrder==1) $booking_no=$$smnBookingNo; else $booking_no='';

			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",56,".$$productionId.",'".$$productionDtlsId."','".$$productId."','".$$orderId."','".$$deterId."','".$$rollId."','".$$barcodeNo."','".$$currentDelivery."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$id.",".$dtls_id.",'".$$orderId."',56,'".$$currentDelivery."','".$$rollNo."','".$$rollId."','".$booking_no."','".$$bookingWithoutOrder."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_roll = $id_roll+1;
			
			$used_roll_ids.=$$rollId.",";
			$barcodeNos.=$$barcodeNo."__".$dtls_id."__".number_format($$currentDelivery,2).",";
			$dtls_id = $dtls_id+1;
		}

		//echo "10**insert into pro_grey_prod_delivery_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID=sql_insert("pro_grey_prod_delivery_mst",$field_array,$data_array,0);
		$rID2=sql_insert("pro_grey_prod_delivery_dtls",$field_array_dtls,$data_array_dtls,1);
		$rID3=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		$used_roll_ids=chop($used_roll_ids,",");
		$statusUsed=sql_multirow_update("pro_roll_details","roll_used",1,"id",$used_roll_ids,0);
		
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$statusUsed;die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $statusUsed)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_mrr_number[0]."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $statusUsed)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0]."**".substr($barcodeNos,0,-1);
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
		
		$field_array="delevery_date*company_id*location_id*knitting_source*knitting_company*updated_by*update_date";
		$data_array=$txt_delivery_date."*".$cbo_company_id."*".$cbo_location_id."*".$cbo_knitting_source."*".$knit_company_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array_dtls="id, mst_id, entry_form, grey_sys_id, sys_dtls_id, product_id, order_id, determination_id, roll_id, barcode_num, current_delivery, inserted_by, insert_date";
		$dtls_id = return_next_id( "id", "pro_grey_prod_delivery_dtls", 1 );
		$field_array_update="current_delivery*updated_by*update_date*status_active*is_deleted";
		
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, booking_no, booking_without_order, inserted_by, insert_date";
		$id_roll = return_next_id( "id", "pro_roll_details", 1 ); 
		
		$barcodeNos=''; $used_roll_ids='';
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$productionId="productionId_".$j;
			$productionDtlsId="productionDtlsId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$deterId="deterId_".$j;
			$rollId="rollId_".$j;
			$barcodeNo="barcodeNo_".$j;
			$currentDelivery="currentDelivery_".$j;
			$dtlsId="dtlsId_".$j;
			$rollNo="rollNo_".$j;
			$bookingWithoutOrder="bookingWithoutOrder_".$j;
			$smnBookingNo="smnBookingNo_".$j;
			
			if($$dtlsId>0)
			{
				$dtlsId_arr[]=$$dtlsId;
				$data_array_update[$$dtlsId]=explode("*",("'".$$currentDelivery."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0"));
				
				$barcode_dtls_arr[$$barcodeNo]['dtls_id']=$$dtlsId;
				$barcode_dtls_arr[$$barcodeNo]['qty']=$$currentDelivery;
				
				$barcodeNos.=$$barcodeNo."__".$$dtlsId."__".number_format($$currentDelivery,2).",";
				$dtls_id_for_roll=$$dtlsId;
			}
			else
			{
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",56,".$$productionId.",'".$$productionDtlsId."','".$$productId."','".$$orderId."','".$$deterId."','".$$rollId."','".$$barcodeNo."','".$$currentDelivery."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$barcodeNos.=$$barcodeNo."__".$dtls_id."__".number_format($$currentDelivery,2).",";
				$dtls_id_for_roll=$dtls_id;
				$dtls_id = $dtls_id+1;
			}
			
			$used_roll_ids.=$$rollId.",";
			
			if($$bookingWithoutOrder==1) $booking_no=$$smnBookingNo; else $booking_no='';
			
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$update_id.",".$dtls_id_for_roll.",'".$$orderId."',56,'".$$currentDelivery."','".$$rollNo."','".$$rollId."','".$booking_no."','".$$bookingWithoutOrder."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_roll = $id_roll+1;
		}
		
		//echo "insert into com_export_proceed_rlzn_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID=sql_update("pro_grey_prod_delivery_mst",$field_array,$data_array,"id",$update_id,0);
		
		$rID2=true; $rID3=true; $statusChange=true; $statusNotUsed=true;
		if(count($data_array_update)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement( "pro_grey_prod_delivery_dtls", "id", $field_array_update, $data_array_update, $dtlsId_arr ));
			//echo bulk_update_sql_statement( "pro_grey_prod_delivery_dtls", "id", $field_array_update, $data_array_update, $dtlsId_arr );
		}
		
		if($data_array_dtls!="")
		{
			$rID3=sql_insert("pro_grey_prod_delivery_dtls",$field_array_dtls,$data_array_dtls,1);
		}

		/*$field_array_status="updated_by*update_date*status_active*is_deleted";
		$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$statusChange=sql_multirow_update("pro_grey_prod_delivery_dtls",$field_array_status,$data_array_status,"mst_id",$update_id,0);*/
		
		$txt_deleted_id=str_replace("'","",$txt_deleted_id);
		if($txt_deleted_id!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChange=sql_multirow_update("pro_grey_prod_delivery_dtls",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
		}
		
		$delete_roll=execute_query( "delete from pro_roll_details where mst_id=$update_id and entry_form=56",0);
		$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		$used_roll_ids=chop($used_roll_ids,",");
		$statusUsed=sql_multirow_update("pro_roll_details","roll_used",1,"id",$used_roll_ids,0);
		
		$txt_deleted_roll_id=str_replace("'","",$txt_deleted_roll_id);
		if($txt_deleted_roll_id!="")
		{
			$statusNotUsed=sql_multirow_update("pro_roll_details","roll_used",0,"id",$txt_deleted_roll_id,0);
		}
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$delete_roll."&&".$statusChange."&&".$statusUsed."&&".$statusNotUsed;die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $delete_roll && $statusChange && $statusNotUsed)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_challan_no)."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $delete_roll && $statusChange && $statusNotUsed)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_challan_no)."**".substr($barcodeNos,0,-1);
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

if($action=="challan_popup")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(data,barcode_nos)
		{
			$('#hidden_data').val(data);
			$('#hidden_barcode_nos').val(barcode_nos);
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
                    <th>Company</th>
                    <th>Delivery Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Challan No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="hidden_data" id="hidden_data">  
                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"",0); ?>        
                    </td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
					</td>
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Challan No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value, 'create_challan_search_list_view', 'search_div', 'grey_feb_delivery_roll_wise_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	
	$search_string="%".trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];

	if($company_id==0) { echo "Please Select Company First."; die; }
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and delevery_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and delevery_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and sys_number like '$search_string'";
	}
	
	if($db_type==0) 
	{
		$year_field="YEAR(insert_date) as year,";
		$barcode_arr=return_library_array( "select mst_id, group_concat(barcode_num order by id desc) as barcode_num from  pro_grey_prod_delivery_dtls where entry_form=56 and status_active=1 and is_deleted=0 group by mst_id",'mst_id','barcode_num');
	}
	else if($db_type==2) 
	{
		$year_field="to_char(insert_date,'YYYY') as year,";
		$barcode_arr=return_library_array( "select mst_id, LISTAGG(barcode_num, ',') WITHIN GROUP (ORDER BY id desc) as barcode_num from  pro_grey_prod_delivery_dtls where entry_form=56 and status_active=1 and is_deleted=0 group by mst_id",'mst_id','barcode_num');
	}
	else $year_field="";//defined Later
	
	$sql = "select id, $year_field sys_number_prefix_num, sys_number, company_id, knitting_source, knitting_company, location_id, delevery_date from pro_grey_prod_delivery_mst where entry_form=56 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond order by id"; 
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="130">Company</th>
            <th width="120">Location</th>
            <th width="70">Challan No</th>
            <th width="60">Year</th>
            <th width="90">Knitting Source</th>
            <th width="130">Knitting Company</th>
            <th>Delivery date</th>
        </thead>
	</table>
	<div style="width:750px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				 
				$knit_comp="&nbsp;";
                if($row[csf('knitting_source')]==1)
					$knit_comp=$company_arr[$row[csf('knitting_company')]]; 
				else
					$knit_comp=$supllier_arr[$row[csf('knitting_company')]];
				
				$data=$row[csf('id')]."**".$row[csf('sys_number')]."**".change_date_format($row[csf('delevery_date')]);
				$barcode_nos=$barcode_arr[$row[csf('id')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>','<? echo $barcode_nos; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="130"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                    <td width="120"><p><? echo $location_arr[$row[csf('location_id')]]; ?>&nbsp;</p></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="90"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?>&nbsp;</p></td>
                    <td width="130"><p><? echo $knit_comp; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
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

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	if($company_id>0) $disable=1; else $disable=0;  
	?> 
	<script>
		var selected_id = new Array();
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_barcode_nos').val( id );
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_barcode_nos').val( '' );
			selected_id = new Array();
		}
	
    </script>
</head>
<body>
<div align="center" style="width:1075px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:1070px; margin-left:5px">
		<!--<legend>Enter search words</legend>-->           
            <table cellpadding="0" cellspacing="0" width="720" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Location</th>
                    <th>Order No</th>
                    <th>File No</th>
                    <th>Ref. No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? 
						 echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',$company_id,"load_drop_down( 'grey_feb_delivery_roll_wise_entry_controller', this.value, 'load_drop_down_location', 'location_td' );",$disable); ?>        
                    </td>
                    <td align="center" id="location_td">	
                    	<? 
							if($company_id>0)
							{
								echo create_drop_down( "cbo_location_id", 130, "select id,location_name from lib_location where company_id='$company_id' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $location_id, "",1 );	
							}
							else
							{
								echo create_drop_down( "cbo_location_id", 130, $blank_array,"", 1, "-- Select --", $selected, "",1,"" ); 
							}
						?>
                    </td>     
                    <td align="center">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />	
                    </td> 
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_file_no" id="txt_file_no" />	
                    </td> 
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_ref_no" id="txt_ref_no" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_id').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value, 'create_barcode_search_list_view', 'search_div', 'grey_feb_delivery_roll_wise_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:70px;" />
                     </td>
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

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);
	$company_id		= $data[0];
	$location_id	= $data[1];
	$order_no 		= trim($data[2]);
	$file_no 		= trim($data[3]);
	$ref_no 		= trim($data[4]);

	if($company_id==0) { echo "Please Select Company First."; die; }
	$location_id	== 0	? $location_id	= "" : $location_id	= "and a.location_id='$location_id'";
	$order_no		== ""	? $order_no		= "" : $order_no	= "and d.po_number like '".$order_no."%'";
	$file_no		== ""	? $file_no		= "" : $file_no		= "and d.file_no='$file_no'";
	$ref_no			== ""	? $ref_no		= "" : $ref_no		= "and d.grouping like '".$ref_no."%'";

	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_num from pro_grey_prod_delivery_dtls where entry_form=56 and status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_num')]]=$row[csf('barcode_num')];
	}
	
	//$sql="SELECT a.recv_number, a.location_id, a.knitting_source, a.knitting_company, a.receive_date, b.shift_name, b.machine_no_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and a.company_id=$company_id $location_id $order_no $file_no $ref_no";
	
	if($order_no!="" || $file_no!="" || $ref_no!="")
	{
		$sql="select a.recv_number_prefix_num, a.location_id, a.knitting_source, a.knitting_company, a.receive_date, b.shift_name, b.machine_no_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, e.job_no_prefix_num 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.booking_without_order!=1 and a.company_id=$company_id $location_id $order_no $file_no $ref_no";	
	}
	else
	{
		$sql="select a.recv_number_prefix_num, a.location_id, a.knitting_source, a.knitting_company, a.receive_date, b.shift_name, b.machine_no_id, c.barcode_no, c.roll_no, c.qnty, c.booking_without_order, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, e.job_no_prefix_num 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.booking_without_order!=1 and a.company_id=$company_id $location_id $order_no $file_no $ref_no
		union all
			select a.recv_number_prefix_num, a.location_id, a.knitting_source, a.knitting_company, a.receive_date, b.shift_name, b.machine_no_id, c.barcode_no, c.roll_no, c.qnty, c.booking_without_order, null as po_number, null as pub_shipment_date, null as job_no_mst, null as file_no, null as grouping, null as job_no_prefix_num 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.booking_without_order=1 and a.company_id=$company_id $location_id
	";		
	}
	//echo $sql;//die;
	$result = sql_select($sql);
	
	$company_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name");
	$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$supplier_arr=return_library_array( "select id, short_name from lib_supplier", "id", "short_name");
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1055" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="50">System Id</th>
            <th width="70">Production Date</th>
            <th width="50">Job No</th>
            <th width="100">Order No</th>
            <th width="50">file No</th>
            <th width="50">Ref. No</th>
            <th width="70">Shipment Date</th>
            <th width="65">Knitting Source</th>
            <th width="60">Knitting Company</th>
            <th width="120">Location</th>
            <th width="40">Shift</th>
            <th width="60">Machine No</th>
            <th width="80">Barcode No</th>
            <th width="50">Roll No</th>
            <th>Roll Qty.</th>
        </thead>
	</table>
	<div style="width:1055px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1035" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="40">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="50"><p><? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                        <td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
						<td width="50"><p><? echo $row[csf('job_no_prefix_num')]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $row[csf('po_number')]; ?>&nbsp;</p></td>
                        <td width="50"><p><? echo $row[csf('file_no')]; ?>&nbsp;</p></td>
                        <td width="50"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
                        <td width="70" align="center"><? if($row[csf('booking_without_order')]==1) echo '&nbsp;'; else echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        <td width="65"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                        <td width="60">
                        	<p>
							<?
								if($row[csf('knitting_source')]==1)
								{
									echo $company_arr[$row[csf('knitting_company')]]; 
								}
								else 
								{
									echo $supplier_arr[$row[csf('knitting_company')]];
								}
							?>
                            </p>
                        </td>
                        <td width="120"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                        <td width="40"><p><? echo $shift_name[$row[csf('shift_name')]]; ?></p></td>
                        <td width="60"><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
						<td width="80"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="50"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
					<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="1055">
        <tr>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?	
    exit();
}

if($action=="grey_delivery_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];
	$kniting_source=$data[4];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	
	$mstData=sql_select("select company_id, delevery_date, knitting_source, knitting_company from pro_grey_prod_delivery_mst where id=$update_id");
	
	$job_array=array();
	$job_sql="select a.job_no_prefix_num, a.job_no, a.style_ref_no, b.id, b.po_number, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
		$job_array[$row[csf('id')]]['ref_no']=$row[csf('ref_no')];
	}
	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
	
	
	?>
    <div style="width:1620px;">
    	<table width="1290" cellspacing="0" align="center" border="0">
			<tr>
				<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px"><strong><u>Delivery Challan</u></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px"><strong><u>Knitting Section</u></strong></td>
			</tr>
        </table> 
        <br>
		<table width="1290" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="80">Challan No</td>
                <td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
                <td style="font-size:16px; font-weight:bold;" width="60">Location</td>
                <td width="170" id="location_td"></td>
                <td width="810" id="barcode_img_id" align="right"></td>
			</tr>
            <tr>
				<td style="font-size:16px; font-weight:bold;">Delivery Date</td>
                <td colspan="4">:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
			</tr>
		</table>
        <br>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1620" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="90">Order No</th>
                    <th width="100">Style No</th>
                    <th width="60">Buyer <br> Job</th>
                    <th width="60">File No <br> Ref No</th>
                    <th width="50">System ID</th>
                    <th width="65">Prog./ Book. No</th>
                    <th width="80">Production Basis</th>
                    <th width="70">Production Date</th><!--new-->
                    <th width="40">Shift</th><!--new-->
                    <th width="70">Knitting Company</th>
                    <th width="50">Yarn Count</th>
                    <th width="70">Yarn Brand</th>
                    <th width="60">Lot No</th>
                    <th width="70">Fab Color</th>
                    <th width="70">Color Range</th>
                    <th width="150">Fabric Type</th>
                    <th width="50">Stich</th>
                    <th width="50">Fin GSM</th>
                    <th width="40">Fab. Dia</th>
                    <th width="40" >Machine No</th>
                    <th width="80">Barcode No</th>
                    <th width="40">Roll No</th>
                    <th>QC Pass Qty</th>
                </tr>
            </thead>
            <?
				$i=0; $tot_qty=0; $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
				
				if($kniting_source==1)//in-house
				{
					$sql="SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_without_order, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.barcode_no, c.roll_no, c.po_breakdown_id, d.current_delivery, c.booking_no as bwo, c.booking_without_order FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 order by e.seq_no";
				}
				else 
				{			
					$sql="SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_without_order, a.booking_no, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.barcode_no, c.roll_no, c.po_breakdown_id, d.current_delivery, c.booking_no as bwo, c.booking_without_order FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 order by c.roll_no";
				}
				
				$result=sql_select($sql);
				$loc_arr=array();
				$loc_nm=": ";
				foreach($result as $row)
				{
					if($loc_arr[$row[csf('location_id')]]=="")
					{
						$loc_arr[$row[csf('location_id')]]=$row[csf('location_id')];
						$loc_nm.=$location_arr[$row[csf('location_id')]].', ';
					}
					 
					$knit_company="&nbsp;";
					if($row[csf("knitting_source")]==1)
					{
						$knit_company=$company_array[$row[csf("knitting_company")]]['shortname'];
					}
					else if($row[csf("knitting_source")]==3)
					{
						$knit_company=$supplier_arr[$row[csf("knitting_company")]];
					}
					
					$count='';
					$yarn_count=explode(",",$row[csf('yarn_count')]);
					foreach($yarn_count as $count_id)
					{
						if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
					}
					
					if($row[csf('receive_basis')]==1)
					{
						$booking_no=explode("-",$row[csf('booking_no')]);	
						$prog_book_no=(int)$booking_no[3];
					}
					else $prog_book_no=$row[csf('booking_no')];
					$i++;
					?>
                	<tr>
                        <td width="30"><? echo $i; ?></td>
                        <?
						if($row[csf('booking_without_order')]==1)
						{
						?>
							<td width="100" style="word-break:break-all;"><? echo $row[csf('bwo')]; ?></td>
                            <td width="100" style="word-break:break-all;">&nbsp;</td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]]; ?></td>
							<td width="60" style="word-break:break-all;"><? echo "F:<br>R:"; ?></td>
						<?	
						}
						else
						{
						?>
							<td width="100" style="word-break:break-all;"><? echo $job_array[$row[csf('po_breakdown_id')]]['po']; ?></td>
                            <td width="100" style="word-break:break-all;"><? echo $job_array[$row[csf('po_breakdown_id')]]['style_ref_no']; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]]."<br>".$job_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
							<td width="60" style="word-break:break-all;"><? echo "F:".$job_array[$row[csf('po_breakdown_id')]]['file_no']."<br>R:".$job_array[$row[csf('po_breakdown_id')]]['ref_no']; ?></td>
						<?
						}
						?>
                        <td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                        <td width="65" style="word-break:break-all;"><? echo $prog_book_no; ?></td>
                        <td width="80" style="word-break:break-all;"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo change_date_format($row[csf("receive_date")]); ?></td>
                        <td width="40" style="word-break:break-all;"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
                        <td width="50" style="word-break:break-all;"><? echo $count; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
                        <td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
                        <td width="70" style="word-break:break-all;">
						<? 
						//echo $color_arr[$row[csf("color_id")]]; 
						$color_id_arr=array_unique(explode(",",$row[csf("color_id")]));
						$all_color_name="";
						foreach($color_id_arr as $c_id)
						{
							$all_color_name.=$color_arr[$c_id].",";
						}
						$all_color_name=chop($all_color_name,",");
						echo $all_color_name;
						?>
                        </td>
                        <td width="70" style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
                        <td width="150" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
                        <td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
                        <td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
                        <td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
                        <td width="40" style="word-break:break-all;" align="center"><? echo $machine_details[$row[csf('machine_no_id')]]; ?></td>
                        <td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
                        <td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('current_delivery')],2); ?></td>
                    </tr>
                <?
					$tot_qty+=$row[csf('current_delivery')];
				}

				$loc_nm=rtrim($loc_nm,', ');
			?>
            <tr> 
                <td align="right" colspan="22"><strong>Total</strong></td>
                <td align="right"><? echo $i; ?></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
            <tr>
                <td colspan="2" align="left"><b>Remarks:</b></td>
                <td colspan="22">&nbsp;</td>
            </tr>
		</table>
	</div>
    <? echo signature_table(44, $company, "1600px"); ?>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
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
			  barHeight: 40,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_challan_no; ?>');
		document.getElementById('location_td').innerHTML='<? echo $loc_nm; ?>';
	</script>
	<?
    exit();
}
?>