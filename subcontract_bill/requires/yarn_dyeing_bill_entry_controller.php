<?
header('Content-type:text/html; charset=utf-8');    
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$bill_process_id="30";
	
	
	if ($operation==0)   // Insert Here========================================================================================delivery_id
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";	
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		}
        $cbo_bill_on=str_replace("'",'',$cbo_bill_on);
        $new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'YDB', date("Y",time()), 5, "select prefix_no, prefix_no_num from  subcon_outbound_bill_mst where company_id=$cbo_company_id and process_id=30 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));
		
		//	and process_id=$bill_process_id
		//	echo "10**";print_r($new_bill_no);die;
		//	print_r($new_bill_no);die;
		
		if(str_replace("'",'',$update_id)=="")
		{
			$id=return_next_id( "id", "subcon_outbound_bill_mst", 1 ) ; 	
			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, supplier_id, bill_for,bill_on,manual_challan, party_bill_no, process_id, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location_name.",".$txt_bill_date.",".$cbo_supplier_company.",".$cbo_bill_for.",".$cbo_bill_on.",".$txt_manual_challan.",".$txt_party_bill.",30,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//echo "10**INSERT INTO subcon_outbound_bill_mst (".$field_array.") VALUES ".$data_array;die;
			$rID=sql_insert("subcon_outbound_bill_mst",$field_array,$data_array,1);
			$return_no=$new_bill_no[0]; 
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="location_id*bill_date*supplier_id*bill_for*bill_on*manual_challan*party_bill_no*updated_by*update_date";
			$data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_supplier_company."*".$cbo_bill_for."*".$cbo_bill_on."*".$txt_manual_challan."*".$txt_party_bill."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("subcon_outbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
			$return_no=str_replace("'",'',$txt_bill_no);
		}
		
		
			
		$id1=return_next_id( "id","subcon_outbound_bill_dtls",1);
		//$field_array1 ="id, mst_id, receive_id, receive_date ,challan_no, order_id, item_id, febric_description_id, batch_id, dia_width_type, color_id, body_part_id, roll_no, wo_num_id, receive_qty, uom, rate, amount, remarks, process_id, inserted_by, insert_date, currency_id,sub_process_id,source";
		$field_array1 ="id, mst_id, receive_id, receive_date, mrr_no, challan_no, job_no, item_id, color_id, wo_num_id, uom, receive_qty, rate, amount, remarks, process_id, currency_id, no_of_bags, cone_per_bag, domestic_currency, inserted_by, insert_date";
		  
		$add_comma=0;
		$rID2=1;
		for($i=1; $i<=$tot_row; $i++)
		{
			$receive_date="txtReceiveDate_".$i;
			$mrr_no="txtMrrNo_".$i;
			$challen_no="txtChallenNo_".$i;
			//$orderid="orderNoId_".$i;
			$order_no="txtOrderNo_".$i;
			
			
			//$style_name="txtStyleName_".$i;
			//$party_name="txtPartyName_".$i;
			$num_of_bag="txtNumberBag_".$i;
			$num_of_cone="txtNumberCone_".$i;
			$item_id="itemid_".$i;
			$colorid="colorId_".$i;
			$wo_number="textWoNumId_".$i;
			$cbo_uom="cboUom_".$i;
			$yarn_qnty="txtYarnQty_".$i;
			$rate="txtRate_".$i;
			$amount="txtAmount_".$i;
			$remarks="txtRemarks_".$i;
			$recive_id="reciveId_".$i;
			$updateid_dtls="updateIdDtls_".$i;
			$curanci="curanci_".$i;
			$domesticAmount_="txtDomesticAmount_".$i;
			
			if(str_replace("'",'',$$updateid_dtls)=="0")  
			{
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$recive_id.",".$$receive_date.",".$$mrr_no.",".$$challen_no.",".$$order_no.",".$$item_id.",".$$colorid.",".$$wo_number.",".$$cbo_uom.",".$$yarn_qnty.",".$$rate.",".$$amount.",".$$remarks.",'30',".$$curanci.",".$$num_of_bag.",".$$num_of_cone.",".$$domesticAmount_.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//."',".$$curanci.",".$$subprocessId.",".$$serviceSource
				$id1=$id1+1;
				$add_comma++;
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$cbo_uom."*".$$yarn_qnty."*".$$rate."*".$$amount."*".$$curanci."*".$$remarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				$rID2=execute_query(bulk_update_sql_statement("subcon_outbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
			}
		}
		
		//echo "10**$data_array1"; die;
		
		if($data_array1!="")
		{
			//echo "10**insert into subcon_outbound_bill_dtls (".$field_array1.") values ".$data_array1;die;
			$rID1=sql_insert("subcon_outbound_bill_dtls",$field_array1,$data_array1,1);
		}
		//echo "10**".$rID."**".$rID1."**".$rID2;die;
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}	
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		$id=str_replace("'",'',$update_id);
        $cbo_bill_on=str_replace("'",'',$cbo_bill_on);
		$field_array="location_id*bill_date*supplier_id*bill_for*bill_on*manual_challan*party_bill_no*updated_by*update_date";
		$data_array="".$cbo_location_name."*".$txt_bill_date."*".$cbo_supplier_company."*".$cbo_bill_for."*".$cbo_bill_on."*".$txt_manual_challan."*".$txt_party_bill."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("subcon_outbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
		$return_no=str_replace("'",'',$txt_bill_no);
		
		$dtls_update_id_array=array();
		$sql_dtls="Select id from subcon_outbound_bill_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
		}
		
		$id1=return_next_id( "id","subcon_outbound_bill_dtls",1);
		$field_array1 ="id, mst_id, receive_id, receive_date, mrr_no, challan_no, job_no, item_id, febric_description_id, color_id, wo_num_id, uom, receive_qty, rate, amount, remarks, process_id, currency_id, no_of_bags, cone_per_bag, inserted_by, insert_date";
		$field_array_up ="uom*receive_qty*rate*amount*currency_id*remarks*updated_by*update_date";
		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
	  	{
			$receive_date="txtReceiveDate_".$i;
			$mrr_no="txtMrrNo_".$i;
			$challen_no="txtChallenNo_".$i;
			//$orderid="orderNoId_".$i;
			$order_no="txtOrderNo_".$i;
			
			
			//$style_name="txtStyleName_".$i;
			//$party_name="txtPartyName_".$i;
			$num_of_bag="txtNumberBag_".$i;
			$num_of_cone="txtNumberCone_".$i;
			$item_id="itemid_".$i;
			$colorid="colorId_".$i;
			$wo_number="textWoNumId_".$i;
			$cbo_uom="cboUom_".$i;
			$yarn_qnty="txtYarnQty_".$i;
			$rate="txtRate_".$i;
			$amount="txtAmount_".$i;
			$remarks="txtRemarks_".$i;
			$recive_id="reciveId_".$i;
			$updateid_dtls="updateIdDtls_".$i;
			$curanci="curanci_".$i;
			
			if(str_replace("'",'',$$updateid_dtls)=="0")  
			{ 
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$id1.",".$id.",".$$recive_id.",".$$receive_date.",".$$mrr_no.",".$$challen_no.",".$$order_no.",".$$item_id.",0,".$$colorid.",".$$wo_number.",".$$cbo_uom.",".$$yarn_qnty.",".$$rate.",".$$amount.",".$$remarks.",'30',".$$curanci.",".$$num_of_bag.",".$$num_of_cone.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1=$id1+1;
				$add_comma++;
				
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$updateid_dtls);
				$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$cbo_uom."*".$$yarn_qnty."*".$$rate."*".$$amount."*".$$curanci."*".$$remarks."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			}
		}
			  
		$rID1=execute_query(bulk_update_sql_statement("subcon_outbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		if($data_array1!="")
		{
			//echo "10**insert into subcon_outbound_bill_dtls (".$field_array1.") values ".$data_array1;
			$rID1=sql_insert("subcon_outbound_bill_dtls",$field_array1,$data_array1,1);
		}
		if(!empty($id_arr))
		{
			$delete_arr=array_diff($dtls_update_id_array, $id_arr);
			$delete_id=implode(",", $delete_arr);
			if($delete_id)
			{
				$rID3=execute_query( "update subcon_outbound_bill_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in ($delete_id)",1);
			}
		}
		
		//echo "10**<pre>";
		//print_r($dtls_update_id_array);
		//print_r($id_arr);die;
		
		//echo "10**".$rID."**".$rID1."**".$rID3;die;
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($rID && $rID1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}		
		disconnect($con);
		die;
	}
}

else if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );
	exit();	 
}

else if ($action=="load_drop_down_supplier_name")
{
	echo create_drop_down( "cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data'  and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type in (2,9,21)) order by supplier_name", "id,supplier_name", 1, "-- Select supplier --", $selected, "","","","","","",5 );
	//$supplier_cond
	exit();
}
else if ($action=="load_drop_down_supplier_name_new")
{
	 $sql = "SELECT DISTINCT sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data'  and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type in (2,9,21))  UNION ALL SELECT DISTINCT sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active IN(1,3) and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data'  and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type in (2,9,21))  order by supplier_name";
	echo create_drop_down( "cbo_supplier_company", 150, "$sql", "id,supplier_name", 1, "-- Select supplier --", $selected, "","","","","","",5 );
	//$supplier_cond
	exit();
}

else if ($action=="outside_bill_popup")
{
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode,'','');
	$ex_data=explode('_',$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('outside_bill_id').value=id;
			parent.emailwindow.hide();
		}
    </script>
  </head>
  <body>
  <div align="center" style="width:100%;" >
  <form name="finishingbill_1"  id="finishingbill_1" autocomplete="off">
	  <table width="730" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
          <thead>                	 
              <th width="150">Company Name</th>
              <th width="150">Supplier Name</th>
              <th width="80">Bill ID</th>
              <th width="100">Receive MRR</th>
              <th width="170">Date Range</th>
              <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>           
          </thead>
          <tbody>
                <tr>
                <td> <input type="hidden" id="outside_bill_id">  
					<?   
						echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"load_drop_down( 'yarn_dyeing_bill_entry_controller', this.value, 'load_drop_down_supplier_name', 'supplier_td' );",1 );
						//load_drop_down_supplier_name_pop
                    ?>
                </td>
                <td width="140" id="supplier_td">
					<?
						echo create_drop_down( "cbo_supplier_company", 150, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$ex_data[0]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type=21) order by supplier_name", "id,supplier_name", 1, "-- Select supplier --", $ex_data[1], "","","","","","",5 );
                    ?> 
                </td>
                <td>
                    <input type="text" name="txt_bill_no" id="txt_bill_no" class="text_boxes_numeric" style="width:75px" />
                </td>
                <td>
                    <input type="text" name="txt_recv_mrr" id="txt_recv_mrr" class="text_boxes" style="width:100px" />
                </td>
                <td align="center">
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                </td> 
                <td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_company').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_bill_no').value+'_'+document.getElementById('txt_recv_mrr').value, 'outside_yarn_dyeing_bill_list_view', 'search_div', 'yarn_dyeing_bill_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" height="40" valign="middle">
					<? echo load_month_buttons(1);  ?>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="center" valign="top" id=""><div id="search_div"></div></td>
            </tr>
	  </table>    
	  </form>
    </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


else if ($action=="outside_yarn_dyeing_bill_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_cond=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $supplier_cond=" and a.supplier_id='$data[1]'"; 
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $trans_date_cond = "and a.bill_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $return_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $trans_date_cond = "and a.bill_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $return_date ="";
	}
	if ($data[4]!='') $bill_id_cond=" and a.prefix_no_num='$data[4]'"; else $bill_id_cond="";
	if ($data[5]!='') $recv_mrr_cond=" and b.mrr_no='$data[5]'"; else $recv_mrr_cond="";
	
	$location=return_library_array( "select id,location_name from lib_location",'id','location_name');
	$supplier_library_arr=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$bill_for_yarn=array(2=>'Yarn Dyeing with Order',3=>'Yarn Dyeing without Order');
	$arr=array (2=>$location,4=>$supplier_library_arr,5=>$bill_for_yarn,6=>$production_process);
	
	if($db_type==0)
	{
		$year_cond= "year(a.INSERT_DATE)as year";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(a.INSERT_DATE,'YYYY') as year";
	}

	$sql= "SELECT A.ID, A.BILL_NO, A.PREFIX_NO_NUM, $year_cond, A.PARTY_BILL_NO, A.BILL_DATE, A.SUPPLIER_ID, A.BILL_FOR, B.RECEIVE_ID, B.MRR_NO 
	FROM SUBCON_OUTBOUND_BILL_MST A, SUBCON_OUTBOUND_BILL_DTLS B 
	where a.id=b.mst_id and a.process_id=30 and a.status_active=1 and b.status_active=1 $company_cond $supplier_cond $trans_date_cond $bill_id_cond $recv_mrr_cond";
	// echo $sql;
	$sqlresult = sql_select($sql);
	foreach ($sqlresult as $key => $row) 
	{
		$data_arr[$row["ID"]]['prefix_no_num']=$row["PREFIX_NO_NUM"];
		$data_arr[$row["ID"]]['party_bill_no']=$row["PARTY_BILL_NO"];
		$data_arr[$row["ID"]]['bill_date']=$row["BILL_DATE"];
		$data_arr[$row["ID"]]['supplier_id']=$row["SUPPLIER_ID"];
		$data_arr[$row["ID"]]['bill_for']=$row["BILL_FOR"];
		$data_arr[$row["ID"]]['mrr_no'].=$row["MRR_NO"].',';
		$data_arr[$row["ID"]]['year']=$row["YEAR"];
	}
	?>

	<style>
		.wrd_brk{word-break: break-all;word-wrap: break-word;}          
	</style>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width='700'>
		<thead>
			<tr>
				<th width="30" class="wrd_brk">SL</th>
				<th width="70" class="wrd_brk">Bill No</th>
				<th width="70" class="wrd_brk">Year</th>
				<th width="100" class="wrd_brk">Party Bill No</th>
				<th width="100" class="wrd_brk">Bill Date</th>
				<th width="100" class="wrd_brk">Receive No</th>
				<th width="120" class="wrd_brk">Supplier</th>
				<th width="" class="wrd_brk">Bill For</th>
			</tr>
		</thead>
	</table>
	<div style="width:700px; max-height:220px; overflow-y:scroll" id="scroll_body">
		<table width="700px" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_search">
			<tbody >
				<? 
				$i = 1;
				foreach ($data_arr as $keyid => $row) 
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$yRecvNo=implode(",", array_unique(explode(",", chop($row["mrr_no"],","))));
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"
						style="cursor:pointer" onClick="js_set_value('<? echo $keyid; ?>');">
						<td width="30" class="wrd_brk" ><? echo $i; ?></td>
						<td width="70" class="wrd_brk" ><? echo $row["prefix_no_num"]; ?></td>
						<td width="70" class="wrd_brk" ><? echo $row["year"]; ?></td>
						<td width="100" class="wrd_brk" ><? echo $row["party_bill_no"]; ?></td>
						<td width="100" class="wrd_brk" ><? echo change_date_format($row["bill_date"]); ?></td>
						<td width="100" class="wrd_brk" ><? echo chop($yRecvNo,","); ?></td>
						<td width="120" class="wrd_brk" ><? echo $supplier_library_arr[$row["supplier_id"]]; ?></td>
						<td width="" class="wrd_brk" ><? echo $bill_for_yarn[$row["bill_for"]]; ?></td>
					</tr>
					<? $i++;
				} 
				?>
			</tbody>
		</table>
	</div>	
	<?
	exit();	
}

else if ($action=="outside_yarn_dyeing_info_list_view")
{
	echo load_html_head_contents("Popup Info","../", 1, 1, $unicode,1,'');
	$from=1;
	$exdata=explode('***',$data);
	$cbo_company_id=$exdata[0];
	$cbo_supplier_company=$exdata[1];
	$ex_bill_for=$exdata[2];
	$date_from=$exdata[3];
	$date_to=$exdata[4];
	$manualChallan=$exdata[5];
	$variable_check=$exdata[6];
	$update_id=$exdata[7];
	$str_data=$exdata[8];
	$ex_str_data=explode("!!!!",$str_data);
	$str_arr=array();
	foreach($ex_str_data as $str)
	{
		$str_arr[]=$str;
	}
	
	if($db_type==0)
	{ 
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
	}
	else if ($db_type==2)
	{
		if ($date_from!="" &&  $date_to!="") $date_cond= "and a.receive_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
	}
//	die();
	?>
	</head>
	<body>
    <div id="body-close-after-populate">
        <div align="center" style="width:100%;" >
            <div style="width:100%;">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1040px" class="rpt_table">
                    <thead>
                    	<th width="30"><input type="checkbox" name="checkall" id="checkall" onClick="fnc_check('all'); check_all_data();" value="2" ></th>
                        <th width="25">SL</th>
                        <th width="50">Sys. Challan</th>
                        <th width="60">Challan No</th>
                        <th width="65">Recive Date</th>
                        <th width="80">Color</th>
                        <th width="160">Yarn Description</th>
                        <th width="60">Receive Qty</th>
                        <th width="60">Dye Charge</th>
                        <th width="120">Job No</th>
                        <th width="120">Style Ref.</th>
                        <th>Buyer</th>
                    </thead>
                </table>
            </div>
        <div style="width:1040px;max-height:180px; overflow-y:scroll" id="kintt_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020px" class="rpt_table" id="tbl_list_search">
                <tbody>
                <?
                $color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
                $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
                //$recive_basis_arr=return_library_array( "select id, receive_basis from inv_receive_master",'id','receive_basis');
                $booking_no_arr=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst where entry_form not in (42,114)",'id','ydw_no');
                $product_dtls_arr=array();

                $sql_prod= sql_select("select id, product_name_details, lot, color from product_details_master where company_id=$data[0] and item_category_id=1");
                foreach($sql_prod as $row)
                {
                    $product_dtls_arr[$row[csf('id')]]['prod_name']=$row[csf('product_name_details')];
                    $product_dtls_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
                    $product_dtls_arr[$row[csf('id')]]['color']=$row[csf('color')];
                }
                unset($sql_prod);

                $bill_qty_array=array();
                $sql_bill="select a.receive_id, sum(a.receive_qty) as bill_qty, b.bill_on from subcon_outbound_bill_dtls a, subcon_outbound_bill_mst b where b.id = a.mst_id and a.status_active=1 and a.is_deleted=0 and a.process_id=30  group by a.receive_id, b.bill_on";
                $sql_bill_result =sql_select($sql_bill);
                foreach($sql_bill_result as $row)
                {
                    $bill_qty_array[$row[csf('receive_id')]]['qty']=$row[csf('bill_qty')];
                    $bill_qty_array[$row[csf('receive_id')]]['bill_on']=$row[csf('bill_on')];
                }
                unset($sql_bill_result);

                //$grey_used_arr=return_library_array( "select dtls_id, used_qty from  pro_material_used_dtls",'dtls_id','used_qty');

                $i=1;

                if($db_type==0)
                {
                    $year_cond="year(a.insert_date)";
                    $booking_without_order="IFNULL(d.booking_without_order,0)";
                }
                else if($db_type==2)
                {
                    $year_cond="TO_CHAR(a.insert_date,'YYYY')";
                    $booking_without_order="nvl(d.booking_without_order,0)";
                }

                $job_arr=array();
                if($ex_bill_for!=3) //Yarn Dyeing with Order
                {
                    //$sql_order="Select a.id, a.po_number, b.style_ref_no, b.buyer_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
                    $sql_job="Select id, job_no, style_ref_no, buyer_name from wo_po_details_master where is_deleted=0 and status_active=1";
                    $sql_order_result=sql_select($sql_job);
                    foreach ($sql_order_result as $row)
                    {
                        $job_arr[$row[csf("job_no")]]['style']=$row[csf("style_ref_no")];
                        $job_arr[$row[csf("job_no")]]['buyer']=$row[csf("buyer_name")];
                    }
                    unset($sql_order_result);
                      $sql="select a.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.receive_purpose, a.booking_id, a.receive_date, a.challan_no, a.store_id, a.currency_id, a.exchange_rate, a.remarks,  b.id as trans_id, b.no_of_bags, b.cone_per_bag, b.receive_basis, b.job_no, b.prod_id, b.brand_id, b.cons_uom, b.cons_quantity, b.cons_avg_rate, b.dye_charge, b.cons_amount, b.buyer_id, b.yarn_count, nvl(b.grey_quantity, 0) as grey_quantity
                    from inv_receive_master a, inv_transaction b, wo_yarn_dyeing_mst c 
                    where a.id=b.mst_id and a.booking_id=c.id and a.entry_form=1 and a.item_category=1 and a.item_category=b.item_category and b.transaction_type=1 and a.receive_purpose=2 and a.receive_basis in(2) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_id=$cbo_company_id and a.supplier_id=$cbo_supplier_company and c.entry_form IN (41,125,135) $date_cond  order by a.id DESC";
					//  and job_no is not null
                }
                else /*Yarn Dyeing without Order*/
                {
					$sql_job="Select id, job_no, style_ref_no, buyer_name from wo_po_details_master where is_deleted=0 and status_active=1";
                    $sql_order_result=sql_select($sql_job);
                    foreach ($sql_order_result as $row)
                    {
                        $job_arr[$row[csf("job_no")]]['style']=$row[csf("style_ref_no")];
                        $job_arr[$row[csf("job_no")]]['buyer']=$row[csf("buyer_name")];
                    }
                    unset($sql_order_result);

                    $sql="select a.id, a.recv_number_prefix_num, a.recv_number, a.receive_basis, a.receive_purpose, a.booking_id, a.receive_date, a.challan_no, a.store_id, a.currency_id, a.exchange_rate, a.remarks,  b.id as trans_id, b.no_of_bags, b.cone_per_bag, b.receive_basis, b.job_no, b.prod_id, b.brand_id, b.cons_uom, b.cons_quantity, b.cons_avg_rate, b.dye_charge, b.cons_amount, b.buyer_id, b.yarn_count, nvl(b.grey_quantity, 0) as grey_quantity
                    from inv_receive_master a, inv_transaction b, wo_yarn_dyeing_mst c 
                    where a.id=b.mst_id and a.booking_id=c.id and a.entry_form=1 and a.item_category=1 and a.item_category=b.item_category and b.transaction_type=1 and a.receive_purpose=2 and a.receive_basis in(2) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_id=$cbo_company_id and a.supplier_id=$cbo_supplier_company  and c.entry_form IN (42,114) $date_cond  order by a.id DESC";
					// and job_no is null
                }
               // echo $sql;
                $sql_result=sql_select($sql);
                foreach($sql_result as $row) // for update row
                {
                    $conv_rate=sql_select("SELECT conversion_rate from currency_conversion_rate WHERE con_date = (SELECT MAX(con_date) from currency_conversion_rate WHERE is_deleted=0 and status_active=1 and currency=".$row[csf('currency_id')].")");

                    $prod_id = $row[csf("prod_id")];
                    $all_value=$row[csf('trans_id')];

                    if(in_array($all_value,$str_arr))
                    {
                        $bookingNos=$booking_no_arr[$row[csf('booking_id')]];
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; //rec_qty grey_qty

                        $avilable_qty=0; $rec_percent=0; $bill_qty=0;
                        $bill_qty=$bill_qty_array[$row[csf('trans_id')]]['qty'];
                        $avilable_qty=$row[csf('cons_quantity')]-$bill_qty;
                        if($bill_qty_array[$row[csf('trans_id')]]['bill_on'] == 1){
                            $avilable_qty=$row[csf('cons_quantity')]-$bill_qty;
                        }elseif($bill_qty_array[$row[csf('trans_id')]]['bill_on'] == 2){
                            $avilable_qty=$row[csf('grey_quantity')]-$bill_qty;
                        }
                        $on_bill_qty=$row[csf('cons_quantity')];
                        $amount=$avilable_qty*$row[csf('dye_charge')];
                        $dom_currency = $amount * $convRate=  ($conv_rate[0][csf("conversion_rate")])?$conv_rate[0][csf("conversion_rate")]:1;


                        $str_val=$row[csf('trans_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number')].'_'.$row[csf('challan_no')].'_'.$row[csf("job_no")].'_'.$job_arr[$row[csf("job_no")]]['style'].'_'.$buyer_arr[$job_arr[$row[csf("job_no")]]['buyer']].'_'.$row[csf('no_of_bags')].'_'.$row[csf('cone_per_bag')].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]]['prod_name'].'_'.$product_dtls_arr[$row[csf('prod_id')]]['color'].'_'.$color_arr[$product_dtls_arr[$row[csf('prod_id')]]['color']].'_'.$product_dtls_arr[$row[csf('prod_id')]]['lot'].'_'.$row[csf('booking_id')].'_'.$booking_no_arr[$row[csf('booking_id')]].'_'.$row[csf('cons_uom')].'_'.$avilable_qty.'_'.$row[csf('dye_charge')].'_'.$amount.'_'.$dom_currency.'_1_'.$row[csf('remarks')].'_0_'.$row[csf('grey_quantity')];

                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr id="tr_<? echo $all_value; ?>" bgcolor="yellow" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
							<td width="30" align="center" bgcolor="#CCFFCC"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="1" checked ></td>
                            <td width="25"><? echo $i; ?></td>
                            <td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                            <td width="60"><? echo $row[csf('challan_no')]; ?></td>
                            <td width="65"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>

                            <td width="80" style="word-break: break-all;"><? echo $color_arr[$product_dtls_arr[$row[csf('prod_id')]]['color']]; ?></td>

                            <td width="160" style="word-break: break-all;"><? echo $product_dtls_arr[$row[csf('prod_id')]]['prod_name']; ?></td>
                            <td width="60" align="right"><? echo number_format($avilable_qty,2); ?></td>
                            <td width="60" align="right"><? echo number_format($row[csf('dye_charge')],2); ?></td>
                            <td width="120" style="word-break: break-all;"><? echo $row[csf("job_no")]; ?></td>
                            <td width="120" style="word-break: break-all;"><? echo $job_arr[$row[csf("job_no")]]['style']; ?></td>
                            <td style="word-break: break-all;"><? echo $buyer_arr[$job_arr[$row[csf("job_no")]]['buyer']]; ?>
                            <input type="hidden" class="strcls" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                            <input type="hidden" id="currid<? echo $i; ?>" value="<? echo $all_value; ?>"></td>
                        </tr>
                        <?php
                        $i++;
                    }
                }

                foreach($sql_result as $row) // for new row
                {
                    $conv_rate=sql_select("SELECT conversion_rate from currency_conversion_rate WHERE con_date = (SELECT MAX(con_date) from currency_conversion_rate WHERE is_deleted=0 and status_active=1 and currency=".$row[csf('currency_id')].")");

                    $prod_id = $row[csf("prod_id")];
                    $all_value=$row[csf('trans_id')];

                    $bookingNos=$booking_no_arr[$row[csf('booking_id')]];
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; //rec_qty grey_qty

                    $avilable_qty=0; $rec_percent=0; $bill_qty=0;
                    $bill_qty=$bill_qty_array[$row[csf('trans_id')]]['qty'];
                    $avilable_qty=$row[csf('cons_quantity')]-$bill_qty;
                    if($bill_qty_array[$row[csf('trans_id')]]['bill_on'] == 1){
                        $avilable_qty=$row[csf('cons_quantity')]-$bill_qty;
                    }elseif($bill_qty_array[$row[csf('trans_id')]]['bill_on'] == 2){
                        $avilable_qty=$row[csf('grey_quantity')]-$bill_qty;
                    }
                    $on_bill_qty=$row[csf('cons_quantity')];
                    $amount=$avilable_qty*$row[csf('dye_charge')];
                    $dom_currency = $amount * $convRate=  ($conv_rate[0][csf("conversion_rate")])?$conv_rate[0][csf("conversion_rate")]:1;

                    $str_val=$row[csf('trans_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number')].'_'.$row[csf('challan_no')].'_'.$row[csf("job_no")].'_'.$job_arr[$row[csf("job_no")]]['style'].'_'.$buyer_arr[$job_arr[$row[csf("job_no")]]['buyer']].'_'.$row[csf('no_of_bags')].'_'.$row[csf('cone_per_bag')].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]]['prod_name'].'_'.$product_dtls_arr[$row[csf('prod_id')]]['color'].'_'.$color_arr[$product_dtls_arr[$row[csf('prod_id')]]['color']].'_'.$product_dtls_arr[$row[csf('prod_id')]]['lot'].'_'.$row[csf('booking_id')].'_'.$booking_no_arr[$row[csf('booking_id')]].'_'.$row[csf('cons_uom')].'_'.$avilable_qty.'_'.$row[csf('dye_charge')].'_'.$amount.'_'.$dom_currency.'_1_'.$row[csf('remarks')].'_0_'.$row[csf('grey_quantity')];
                    if($avilable_qty>0)
                    {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr id="tr_<? echo $all_value; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $all_value; ?>');" >
                                <td width="30" align="center"><input type="checkbox" name="checkid<? echo $i; ?>" id="checkid<? echo $i; ?>" onClick="fnc_check(<? echo $i; ?>)" value="2" ></td>
                                <td width="25"><? echo $i; ?></td>
                                <td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                                <td width="60"><? echo $row[csf('challan_no')]; ?></td>
                                <td width="65"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>

                                <td width="80" style="word-break: break-all;"><? echo $color_arr[$product_dtls_arr[$row[csf('prod_id')]]['color']]; ?></td>

                                <td width="160" style="word-break: break-all;"><? echo $product_dtls_arr[$row[csf('prod_id')]]['prod_name']; ?></td>
                                <td width="60" align="right"><? echo number_format($avilable_qty,2); ?></td>
                                <td width="60" align="right"><? echo number_format($row[csf('dye_charge')],2); ?></td>
                                <td width="120" style="word-break: break-all;"><? echo $row[csf("job_no")]; ?></td>
                                <td width="120" style="word-break: break-all;"><? echo $job_arr[$row[csf("job_no")]]['style']; ?></td>
                                <td style="word-break: break-all;"><? echo $buyer_arr[$job_arr[$row[csf("job_no")]]['buyer']]; ?>
                                <input type="hidden" class="strcls" id="strid<? echo $i; ?>" value="<? echo $str_val; ?>">
                                <input type="hidden" id="currid<? echo $i; ?>" value="<? echo $all_value; ?>"></td>
                            </tr>
                            <?php
                            $i++;

                    }
                }
                ?>
                </tbody>
            </table>
            </div>
            </div>
             <div>
                <table width="940px" >
                    <tr>
                        <td colspan="10" align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:100px" value="Close" onClick="window_close(0)" />
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        </div>
	</body>           
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

else if ($action=="load_php_data_to_form_outside_bill")
{
	$sql="SELECT min(receive_date) as min_date, max(receive_date) as max_date FROM subcon_outbound_bill_dtls WHERE mst_id='$data' and status_active=1 and is_deleted=0 group by mst_id";
	
	$sql_result_arr =sql_select($sql); 
	$mindate='';  $maxdate='';
	$mindate=$sql_result_arr[0][csf('min_date')];
	$maxdate=$sql_result_arr[0][csf('max_date')];
	
	$nameArray= sql_select("select id, bill_no, company_id, location_id, bill_date, supplier_id, bill_for, bill_on, party_bill_no, manual_challan, is_posted_account from subcon_outbound_bill_mst where id='$data'");
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_bill_no').value 					= '".$row[csf("bill_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/yarn_dyeing_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_location', 'location_td' );\n";
		echo "document.getElementById('cbo_location_name').value			= '".$row[csf("location_id")]."';\n"; 
		echo "document.getElementById('txt_bill_date').value 				= '".change_date_format($row[csf("bill_date")])."';\n";   
		echo "load_drop_down( 'requires/yarn_dyeing_bill_entry_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_supplier_name_new', 'supplier_td' );\n";
		echo "document.getElementById('cbo_supplier_company').value			= '".$row[csf("supplier_id")]."';\n"; 
		echo "document.getElementById('cbo_bill_for').value					= '".$row[csf("bill_for")]."';\n"; 
		echo "document.getElementById('cbo_bill_on').value					= '".($row[csf("bill_on")] == "" ? 0 : $row[csf("bill_on")])."';\n";
		echo "document.getElementById('txt_party_bill').value				= '".$row[csf("party_bill_no")]."';\n";
		echo "document.getElementById('txt_bill_from_date').value 			= '".change_date_format($mindate)."';\n";  
		echo "document.getElementById('txt_bill_to_date').value 			= '".change_date_format($maxdate)."';\n"; 
		echo "document.getElementById('txt_manual_challan').value			= '".$row[csf("manual_challan")]."';\n";
		echo "document.getElementById('hidden_acc_integ').value				= '".$row[csf("is_posted_account")]."';\n";
		
		
		if($row[csf("is_posted_account")]==1)
		{
			echo "$('#accounting_integration_div').text('Already Posted In Accounts.');\n"; 
		}
		else 
		{
			echo "$('#accounting_integration_div').text('');\n"; 
		}
		
		echo "disable_enable_fields('cbo_company_id*cbo_location_name*cbo_supplier_company*cbo_bill_for*cbo_bill_on',1);\n";
	    echo "document.getElementById('update_id').value            		= '".$row[csf("id")]."';\n";
	}
	exit();
}

else if ($action=="load_dtls_data") 
{
	
	//print_r($data[2]); die;
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$roll_no_arr=return_library_array( "select id, no_of_roll from  pro_grey_prod_entry_dtls",'id','no_of_roll');
    $product_dtls_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$booking_no_arr=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst where entry_form not in (42,114)",'id','ydw_no');
	
	
	$sql_job="Select id, job_no, style_ref_no, buyer_name from wo_po_details_master where is_deleted=0 and status_active=1";
	$sql_order_result=sql_select($sql_job);
	foreach ($sql_order_result as $row)
	{
		$job_arr[$row[csf("job_no")]]['style']=$row[csf("style_ref_no")];
		$job_arr[$row[csf("job_no")]]['buyer']=$row[csf("buyer_name")];
	}
	unset($sql_order_result);
	
	
	$product_dtls_arr=array();
	//echo "select id, product_name_details, lot, color from product_details_master where company_id=$data[0] and item_category_id=1"; die;
	$sql_prod= sql_select("select id, product_name_details, lot, color from product_details_master where item_category_id=1");// company_id=$data[0] and
	foreach($sql_prod as $row)
	{
		$product_dtls_arr[$row[csf('id')]]['prod_name']=$row[csf('product_name_details')];
		$product_dtls_arr[$row[csf('id')]]['lot']=$row[csf('lot')];
		$product_dtls_arr[$row[csf('id')]]['color']=$row[csf('color')];
	}
	unset($sql_prod);
	
	$bill_qty_array=array();
	$sql_bill="select receive_id, sum(receive_qty) as bill_qty from subcon_outbound_bill_dtls where status_active=1 and is_deleted=0 and process_id=30 ";
	$sql_bill_result =sql_select($sql_bill);
	foreach($sql_bill_result as $row)
	{
		$bill_qty_array[$row[csf('receive_id')]]['qty']=$row[csf('bill_qty')];
	}
	unset($sql_bill_result);
	
	 $sql="select id, mst_id, receive_id, receive_date, mrr_no, challan_no, job_no, item_id, color_id, wo_num_id, uom, receive_qty, rate, amount, remarks, process_id, currency_id, no_of_bags, cone_per_bag, domestic_currency  from subcon_outbound_bill_dtls where mst_id='$data' and status_active=1 and  is_deleted=0 and process_id=30 order by id ASC";
	$sql_result_arr =sql_select($sql); $str_val="";
	foreach ($sql_result_arr as $row)
	{
		$conv_rate=sql_select("SELECT conversion_rate from currency_conversion_rate WHERE con_date = (SELECT MAX(con_date) from currency_conversion_rate WHERE is_deleted=0 and status_active=1 and currency=".$row[csf('currency_id')].")");

		$bill_qty=$bill_qty_array[$row[csf('receive_id')]]['qty'];
		$avilable_qty=$row[csf('receive_qty')];//-$bill_qty;
		$on_bill_qty=$row[csf('receive_qty')];
		$amount=$row[csf('amount')];
		$dom_currency = $amount * $convRate=  ($conv_rate[0][csf("conversion_rate")])?$conv_rate[0][csf("conversion_rate")]:1;
		
		
		if($str_val=="") 
		{
			$amount=$row[csf('amount')];
			$dom_currency= $row[csf('domestic_currency')];
			$str_val=$row[csf('receive_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('mrr_no')].'_'.$row[csf('challan_no')].'_'.$row[csf("job_no")].'_'.$job_arr[$row[csf("job_no")]]['style'].'_'.$buyer_arr[$job_arr[$row[csf("job_no")]]['buyer']].'_'.$row[csf('no_of_bags')].'_'.$row[csf('cone_per_bag')].'_'.$row[csf('item_id')].'_'.$product_dtls_arr[$row[csf('item_id')]]['prod_name'].'_'.$product_dtls_arr[$row[csf('item_id')]]['color'].'_'.$color_arr[$product_dtls_arr[$row[csf('item_id')]]['color']].'_'.$product_dtls_arr[$row[csf('item_id')]]['lot'].'_'.$row[csf('wo_num_id')].'_'.$booking_no_arr[$row[csf('wo_num_id')]].'_'.$row[csf('uom')].'_'.$avilable_qty.'_'.$row[csf('rate')].'_'.$amount.'_'.$dom_currency.'_'.$row[csf('currency_id')].'_'.$row[csf('remarks')].'_'.$row[csf('id')].'_0';
			
		}
		else 
		{
			//$str_val.="###".$row[csf('trans_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('recv_number')].'_'.$row[csf('challan_no')].'_'.$row[csf("job_no")].'_'.$job_arr[$row[csf("job_no")]]['style'].'_'.$buyer_arr[$job_arr[$row[csf("job_no")]]['buyer']].'_'.$row[csf('no_of_bags')].'_'.$row[csf('cone_per_bag')].'_'.$row[csf('prod_id')].'_'.$product_dtls_arr[$row[csf('prod_id')]]['prod_name'].'_'.$product_dtls_arr[$row[csf('prod_id')]]['color'].'_'.$color_arr[$product_dtls_arr[$row[csf('prod_id')]]['color']].'_'.$product_dtls_arr[$row[csf('prod_id')]]['lot'].'_'.$row[csf('booking_id')].'_'.$booking_no_arr[$row[csf('booking_id')]].'_'.$row[csf('cons_uom')].'_'.$avilable_qty.'_'.$row[csf('dye_charge')].'_'.$amount.'_1__0'.'_'.$row[csf('id')];
			$str_val.="###".$row[csf('receive_id')].'_'.change_date_format($row[csf('receive_date')]).'_'.$row[csf('mrr_no')].'_'.$row[csf('challan_no')].'_'.$row[csf("job_no")].'_'.$job_arr[$row[csf("job_no")]]['style'].'_'.$buyer_arr[$job_arr[$row[csf("job_no")]]['buyer']].'_'.$row[csf('no_of_bags')].'_'.$row[csf('cone_per_bag')].'_'.$row[csf('item_id')].'_'.$product_dtls_arr[$row[csf('item_id')]]['prod_name'].'_'.$product_dtls_arr[$row[csf('item_id')]]['color'].'_'.$color_arr[$product_dtls_arr[$row[csf('item_id')]]['color']].'_'.$product_dtls_arr[$row[csf('item_id')]]['lot'].'_'.$row[csf('wo_num_id')].'_'.$booking_no_arr[$row[csf('wo_num_id')]].'_'.$row[csf('uom')].'_'.$avilable_qty.'_'.$row[csf('rate')].'_'.$amount.'_'.$dom_currency.'_'.$row[csf('currency_id')].'_'.$row[csf('remarks')].'_'.$row[csf('id')].'_0';
		}
	}
	
	echo $str_val;
	exit();
}

else if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../", 1, 1, $unicode);
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
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

else if ($action=="load_domestic_amount")
{
	$data = explode("_",$data);
	if($data[1]>1){
	$conv_rate=sql_select("SELECT conversion_rate from currency_conversion_rate WHERE con_date = (SELECT MAX(con_date) from currency_conversion_rate WHERE is_deleted=0 and status_active=1 and currency=$data[1])");
	echo "document.getElementById('txtDomesticAmount_".$data[0]."').value			= '".($conv_rate[0][csf('conversion_rate')]*$data[2])."';\n"; 
	}
	exit();
}
if($action=="outbound_yarn_dyeing_bill_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id, supplier_name from lib_supplier", "id","supplier_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id","buyer_name");
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$yearn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	
	$sql_mst="Select id, bill_no, bill_date, supplier_id, location_id, bill_for, party_bill_no from subcon_outbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	
	$dataArray=sql_select($sql_mst);
	
	$mst_id=$dataArray[0][csf('id')];
	$billFor_id=$dataArray[0][csf('bill_for')];
	$partyBillNo=$dataArray[0][csf('party_bill_no')];
	$sql_result =sql_select("select id, receive_id as delivery_id, receive_date as delivery_date, challan_no, job_no,order_id, item_id, uom, roll_no as packing_qnty, receive_qty as delivery_qty, rec_qty_pcs as delivery_qtypcs, rate, amount, remarks, currency_id, process_id,color_id,no_of_bags,wo_num_id, body_part_id, febric_description_id from subcon_outbound_bill_dtls  where mst_id='$mst_id' and process_id='30' and status_active=1 and is_deleted=0 order by id ASC"); 
	//echo "select id, receive_id as delivery_id, receive_date as delivery_date, challan_no, order_id, item_id, uom, roll_no as packing_qnty, receive_qty as delivery_qty, rec_qty_pcs as delivery_qtypcs, rate, amount, remarks, currency_id, process_id, body_part_id, febric_description_id from subcon_outbound_bill_dtls  where mst_id='$mst_id' and process_id='30' and status_active=1 and is_deleted=0 order by id ASC";
	//echo "select id, receive_id as delivery_id, receive_date as delivery_date, challan_no, order_id, item_id, uom, roll_no as packing_qnty, receive_qty as delivery_qty, rec_qty_pcs as delivery_qtypcs, rate, amount, remarks, currency_id, process_id, body_part_id, febric_description_id from subcon_outbound_bill_dtls  where mst_id='$mst_id' and process_id='2' and status_active=1 and is_deleted=0 order by id ASC";
	$po_id_arr=array(); $reciveid=""; 
	foreach($sql_result as $row)
	{
		$po_id_arr[$row[csf('order_id')]]=$row[csf('order_id')];
		$receive_id_arr[$row[csf('delivery_id')]]=$row[csf('delivery_id')];
		$reciveid.=$row[csf('delivery_id')].',';
		//$job_no=$row[csf('job_no')];
		$job_array[$row[csf('job_no')]]=$row[csf('job_no')];
	}
	?>
    <div style="width:1150px; margin-left:20px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="70" align="right"> 
            	<img  src='../../<? echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='70' width='200' />
            </td>
            <td>
                <table width="800" cellspacing="0" align="center">
                    <tr>
                    	<td align="center" style="font-size:20px"><strong ><? echo $company_library[$data[0]]; ?></strong></td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size:16px"><strong>Unit : <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                    </tr>
                    	<td align="center" class="form_caption"><? echo show_company($data[0],'',''); ?></td>  
                    </tr>
                    <tr>
                    	<td align="center" style="font-size:18px"><strong><? echo $data[3]; ?></strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
     <table width="1150" cellspacing="0" align="" border="0">
            <tr>
                <td width="150" valign="top"><strong>Bill No :</strong></td> <td width="200"><? echo $dataArray[0][csf('bill_no')]; ?></td>
                <td width="150"><strong>Bill Date: </strong></td><td width="200px"><? echo change_date_format($dataArray[0][csf('bill_date')]); ?></td>
                <td width="150"><strong>Source :</strong></td><td>Out-bound Subcontract</td>
            </tr>
            <tr>
				<?
                    $party_add=$dataArray[0][csf('supplier_id')];
					$nameArray=sql_select( "select address_1, web_site, email, country_id from lib_supplier where id=$party_add"); 
					foreach ($nameArray as $result)
					{ 
						$address="";
						if($result!="") $address=$result[csf('address_1')];
					}
					$party_name=$party_library[$dataArray[0][csf('supplier_id')]];
					$party_location=$address;
                ?>
                <td><strong>Party Name: </strong></td><td style="word-break:break-all"><? echo $party_name; ?></td>
				<td><strong>Party Location: </strong></td><td style="word-break:break-all"><? echo $party_location; ?></td>
                <td><strong>Bill For : </strong></td><td style="word-break:break-all"><? echo $bill_for[$dataArray[0][csf('bill_for')]]; ?></td>
            </tr>
            <tr>
            	<td><strong>Party Bill No : </strong></td><td style="word-break:break-all"><?php echo $partyBillNo; ?></td>
            </tr>
        </table>
         <br>
	<div style="width:100%;" >
		<table cellspacing="0" width="1330"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:16px">
                <th width="30">SL</th>
                <th width="55">Ch. Date</th>
                <th width="40">Sys. Challan</th>
                <th width="40">Rec. Challan</th>
                <th width="60">Buyer</th>
                <th width="40">Job</th>

                <th width="60">Order</th> 
               
               
                <th width="60">Style</th>
                <th width="50">Internal Ref.</th>
                <th width="50">File No</th>
                <th width="35">N.O Bag</th>
                <th width="150">Yarn Desc.</th>
                <th width="100">Color</th>
                <th width="40">Lot</th>
                
                <th width="100">Wo Num</th>
                
               
                <th width="55">Grey Qty</th>
                <th width="55">Recv. Qty</th>
                <th width="40">Rate</th>
                <th width="70">Amount</th>
                <th width="70">Currency</th>
                <th>Remarks</th>
            </thead>
		 <? 
		 if($db_type==0) $job_year="YEAR(a.insert_date) as year"; else $job_year="to_char(a.insert_date,'YYYY') as year";
		 	
		$order_array=array(); $internal_ref_arr=array();
		if($billFor_id!=3)
		{
			  $job_sql="select a.job_no, $job_year, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, b.id, b.po_number,b.grouping as ref_no,b.file_no, (b.po_quantity) as po_quantity, b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active!=0 and a.is_deleted=0 and  b.status_active!=0 and b.is_deleted=0 and a.job_no in('".implode("','",$job_array)."')";//and a.company_name=$data[0]
			$job_sql_result =sql_select($job_sql);
			foreach($job_sql_result as $row)
			{
				$order_array[$row[csf('job_no')]]['buyer_name']=$buyerArr[$row[csf('buyer_name')]];
				$order_array[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
				$order_array[$row[csf('job_no')]]['po_number'].=$row[csf('po_number')].',';
				$order_array[$row[csf('job_no')]]['file_no'].=$row[csf('file_no')].',';
				$order_array[$row[csf('job_no')]]['ref_no'].=$row[csf('ref_no')].',';
				$order_array[$row[csf('job_no')]]['job']=$row[csf('job_no')];
				$order_array[$row[csf('job_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$order_array[$row[csf('job_no')]]['po_quantity']=$row[csf('po_quantity')];
				$order_array[$row[csf('job_no')]]['plan_cut']=$row[csf('plan_cut')];
				$order_array[$row[csf('job_no')]]['ratio']=$row[csf('ratio')];
				$order_array[$row[csf('job_no')]]['year']=$row[csf('year')];
				$job_array[$row[csf('job_no')]]=$row[csf('job_no')];
				
			}
			unset($job_sql_result);
			
			$internal_ref_sql="select job_no, internal_ref from wo_order_entry_internal_ref where job_no in('".implode("','",$job_array)."')";
			$internal_ref_sql_result=sql_select($internal_ref_sql);
			foreach($internal_ref_sql_result as $row)
			{
				$internal_ref_arr[$row[csf('job_no')]][$row[csf('internal_ref')]]=$row[csf('internal_ref')];
			}
		}
		//$const_comp_arr=return_library_array( "select id,product_name_details from product_details_master",'id','product_name_details');
		
		$sql_prod=sql_select("select id,product_name_details,lot from product_details_master");
		foreach($sql_prod as $row)
		{
			$const_comp_arr[$row[csf('id')]]=$row[csf('product_name_details')];
			$const_lot_arr[$row[csf('id')]]=$row[csf('lot')];
		}
		
		
		
		$production_arr=array();
		$production_sql=sql_select("select id, booking_id, booking_no from inv_receive_master where company_id='".$dataArray[0][csf('supplier_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form in (1) and receive_basis=2 and status_active=1 and is_deleted=0"); 
		foreach($production_sql as $row)
		{
			$production_arr[$row[csf('id')]]=$feeder[$knit_plan_arr[$row[csf('booking_id')]]];
		}
		unset($production_sql);
		
		$rec_data_arr=array(); $recChallan_arr=array(); $bookingNoArr=array();
		//and a.location_id='".$dataArray[0][csf('location_id')]."'
		if($dataArray[0][csf('location_id')]>0)
		{
			$loc_cond="and  a.location_id=".$dataArray[0][csf('location_id')]." ";
		} else $loc_cond="";
		
		//echo $res_sql="select c.barcode_no,a.id, a.recv_number_prefix_num, a.receive_date, a.entry_form, a.challan_no, a.receive_basis, a.booking_id, a.booking_no, b.prod_id, b.body_part_id, b.febric_description_id,c.lot from inv_receive_master a, inv_transaction b, product_details_master c where a.company_id='$data[0]' and a.knitting_company='".$dataArray[0][csf('supplier_id')]."' and a.location_id='".$dataArray[0][csf('location_id')]."' and a.id=b.mst_id and b.prod_id=c.id  and a.entry_form in (1)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in(".implode(',',$receive_id_arr).")";	
		 $res_sql="select   a.id,a.recv_number_prefix_num, a.receive_date, a.entry_form, a.challan_no, a.receive_basis, a.booking_id, a.booking_no,b.id as trans_id,b.no_of_bags, b.cone_per_bag, b.receive_basis, b.job_no, b.prod_id, b.brand_id, b.cons_uom, b.cons_quantity,b.grey_quantity, b.cons_avg_rate, b.dye_charge, b.cons_amount, b.buyer_id, b.yarn_count
                    from inv_receive_master a, inv_transaction b, wo_yarn_dyeing_mst c 
                    where a.id=b.mst_id and a.booking_id=c.id and a.entry_form=1 and a.item_category=1 and a.item_category=b.item_category and b.transaction_type=1 and a.receive_purpose=2 and a.receive_basis in(2) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.entry_form IN (41,125,135) and a.company_id='$data[0]' and a.supplier_id='".$dataArray[0][csf('supplier_id')]."' and b.id in(".implode(',',$receive_id_arr).") $loc_cond  order by a.id DESC";
		$res_sql_res=sql_select($res_sql); 
		foreach($res_sql_res as $row)
		{
			$barCodeArr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
			$bookingNoArr[$row[csf('id')]]['bookno']=$row[csf('booking_id')];
		}
	
		foreach($res_sql_res as $row)
		{
			$sys_challan=$row[csf('trans_id')];
			$recChallan_arr[$sys_challan][change_date_format($row[csf('receive_date')])]=$row[csf('challan_no')];
			
			$rec_data_arr[$sys_challan][$row[csf('prod_id')]]['grey_quantity']=$row[csf('grey_quantity')];
			//$rec_data_arr[$sys_challan][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('trans_id')]]['color_id'].=$barCodeDataArr[$row[csf('barcode_no')]]['color_id'].',';
			
		}
		//print_r($rec_data_arr);
		unset($res_sql_res);
		if($dataArray[0][csf('bill_for')]==3)
		{
			$buyer_id_arr=array();
			$sql_non_booking=sql_select("select recv_number_prefix_num, receive_date, buyer_id from inv_receive_master where company_id='".$dataArray[0][csf('party_id')]."' and knitting_company='$data[0]' and location_id='".$dataArray[0][csf('location_id')]."' and entry_form=1  and status_active=1 and is_deleted=0 group by recv_number_prefix_num, receive_date, buyer_id");
			foreach($sql_non_booking as $row)
			{
				$buyer_id_arr[$row[csf('recv_number_prefix_num')]][change_date_format($row[csf('receive_date')])]=$party_library[$row[csf('buyer_id')]];
			}
			
			$reciveIds=implode(",",array_filter(array_unique(explode(",",$reciveid)))); $reciveIdsCond="";
			$receive_ids=count(explode(",",$reciveIds));
			if($db_type==2 && $receive_ids>1000)
			{
				$reciveIdsCond=" and (";
				$reciveIdsArr=array_chunk(explode(",",$reciveIds),999);
				foreach($reciveIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$reciveIdsCond.=" a.id in($ids) or"; 
				}
				$reciveIdsCond=chop($reciveIdsCond,'or ');
				$reciveIdsCond.=")";
			}
			else $reciveIdsCond=" and a.id in($reciveIds)";
			
			
			
			  $styleBuyer_arr=array();
			if($is_booking==1)
			{
				$bookingNos=implode(",",array_unique(explode(",",chop($bookingNo,',')))); $bookingNosCond="";
				$booking_nos=count(array_unique(explode(",",$bookingNos)));
				if($db_type==2 && $booking_nos>1000)
				{
					$bookingNosCond=" and (";
					$bookingNosArr=array_chunk(explode(",",$bookingNos),999);
					foreach($bookingNosArr as $ids)
					{
						$ids=implode(",",$ids);
						$bookingNosCond.=" a.booking_no in($ids) or"; 
					}
					$bookingNosCond=chop($bookingNosCond,'or ');
					$bookingNosCond.=")";
				}
				else $bookingNosCond=" and a.booking_no in($bookingNos)";
				
				$sampleSql="select a.booking_no, c.grouping, b.style_ref_no, b.buyer_name, b.internal_ref from wo_non_ord_samp_booking_dtls a, sample_development_mst b, wo_non_ord_samp_booking_mst c where a.style_id=b.id and a.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $bookingNosCond";
				$sampleSqlRes=sql_select($sampleSql);
				foreach($sampleSqlRes as $row)
				{
					if($row[csf('internal_ref')]=="") $row[csf('internal_ref')]=$row[csf('grouping')];
					$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['style']=$row[csf('style_ref_no')];
					$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['buyer']=$buyerArr[$row[csf('buyer_name')]];
					$styleBuyer_arr[$bookingWiseStyleBuyerArr[$row[csf('booking_no')]]]['internal_ref']=$row[csf('internal_ref')];
				}
				unset($sampleSqlRes);
				//print_r($styleBuyer_arr);
			}
			
			
		}
			
		
				
			$po_id="";$i=1;
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($po_id=="") $po_id=$row[csf('order_id')]; else $po_id.=','.$row[csf('order_id')];
				$buyer_id_name="";
				if($dataArray[0][csf('bill_for')]==3)
				{
					$buyer_id_name=$buyer_id_arr[$row[csf('challan_no')]][change_date_format($row[csf('delivery_date')])];
				}
				else
				{
					$buyer_id_name=$order_array[$row[csf('job_no')]]['buyer_name'];//row[csf('job_no')]
				}
				
			//	$fab_color=""; $feeder_str=""; $yarn_count=""; $mc_dia=''; $mc_gg="";
				 
				
				//$feeder_str=implode(",",array_filter(array_unique(explode(",",$rec_data_arr[$row[csf('delivery_id')]][$row[csf('item_id')]][$row[csf('body_part_id')]][$row[csf('febric_description_id')]]['feeder']))));
				
				$buyerNameStr=""; $styleRef=""; $intRef="";
				if($dataArray[0][csf('bill_for')]==3)
				{
					$buyerNameStr=$styleBuyer_arr[$row[csf('delivery_id')]]['buyer'];
					$styleRef=$styleBuyer_arr[$row[csf('delivery_id')]]['style'];
					$intRef=$styleBuyer_arr[$row[csf('delivery_id')]]['internal_ref'];
				}
				else
				{
					$buyerNameStr=$buyer_id_name;
					$styleRef=$order_array[$row[csf('job_no')]]['style_ref_no'];
					$intRef=implode(',',$internal_ref_arr[$order_array[$row[csf('job_no')]]['job']]);
				}
				$ydw_no = return_field_value("ydw_no", "wo_yarn_dyeing_mst", " status_active=1 and id=".$row[csf('wo_num_id')]."", "ydw_no");
				$lot_no=$const_lot_arr[$row[csf('item_id')]];
				$grey_quantity=$rec_data_arr[$row[csf('delivery_id')]][$row[csf('prod_id')]]['grey_quantity'];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:16px"> 
                    <td><? echo $i; ?></td>
                    <td style="word-break:break-all"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                    <td style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
                    <td style="word-break:break-all"><? echo $recChallan_arr[$row[csf('delivery_id')]][change_date_format($row[csf('delivery_date')])]; ?></td>
                   <td style="word-break:break-all"><? echo $buyerNameStr; ?></td>
                    <td align="center" style="word-break:break-all"><? echo $order_array[$row[csf('job_no')]]['job_no_prefix_num']; ?></td>
                    <td style="word-break:break-all"><? echo $order_array[$row[csf('job_no')]]['po_number']; ?></td>
                   
                   
                    <td style="word-break:break-all"><? echo $styleRef; ?></td>
                   
                    <td align="center" style="word-break:break-all"><? echo $intRef; ?></td>
                     <td style="word-break:break-all"><? $file_no=rtrim($order_array[$row[csf('job_no')]]['file'],','); echo implode(',',array_unique(explode(',',$file_no))); ?></td>
                    <td align="center" style="word-break:break-all"><? echo  $row[csf('no_of_bags')]; ?></td>
                    <td style="word-break:break-all"><? echo $const_comp_arr[$row[csf('item_id')]]; ?></td>
                    <td align="center" style="word-break:break-all"><? echo $color_arr[$row[csf('color_id')]];; ?></td>
                    <td><div style="word-wrap:break-word; width:60px"><? echo $lot_no; ?></div></td>
                    <td align="center"><div style="word-wrap:break-word; width:100px"><? echo $ydw_no; ?></div></td>
                   
                    <td align="right"><p><b><? echo number_format($grey_quantity,2,'.',''); $tot_delivery_qty+=$grey_quantity; ?>&nbsp;</b></p></td>
                    <td align="right"><p><b><? echo number_format($row[csf('delivery_qty')],2,'.',''); $tot_delivery_qtypcs+=$row[csf('delivery_qty')]; ?>&nbsp;</b></p></td>
                   
                    <td align="right"><p><? echo number_format($row[csf('rate')],4,'.',''); ?>&nbsp;</p></td>
                    <td align="right"><p><b><? echo number_format($row[csf('amount')],2,'.','');  $total_amount += $row[csf('amount')]; ?>&nbsp;</b></p></td>

                    <td align="center"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                    <td align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
                  
                    <? 
					$carrency_id=$row[csf('currency_id')];
					if($carrency_id==1){$paysa_sent="Paisa";} else if($carrency_id==2){$paysa_sent="CENTS";}
				   ?>
                </tr>
                <?
                $i++;
			}
			?>
        	<tr> 
                <td align="right" colspan="14"><strong>Total</strong></td>
                <td align="right"><? echo $tot_packing_qty; ?>&nbsp;</td>
                <td align="right"><b><? echo number_format($tot_delivery_qty,2,'.',''); ?></b></td>
                <td align="right"><b><? echo number_format($tot_delivery_qtypcs,2,'.',''); ?></b></td>
                <td>&nbsp;</td>
                 <td align="right"><b><? echo $format_total_amount=number_format($total_amount,0,'.',''); ?></b></td>
                <td>&nbsp;</td>
               
                <td>&nbsp;</td>
			</tr>
           <tr>
               <td colspan="21" align="left"><b>In Word: <? echo number_to_words($format_total_amount,$currency[$carrency_id],$paysa_sent); ?></b></td>
           </tr>
        </table>
        <?
			$bill_no=$dataArray[0][csf('bill_no')];
			$sql_terms="Select id,terms from subcon_terms_condition where entry_form=1 and bill_no='$bill_no' ";
			$result_sql_terms =sql_select($sql_terms);

			$i=1;
			if(count($result_sql_terms)>0)
			{
				?>
                <table width="930" align="left" > 
                    <tr><td colspan="2">&nbsp;</td> </tr>
                    <tr><td colspan="2" align="center"><b>TERMS & CONDITION</b></td></tr>
					<?
                    foreach($result_sql_terms as $rows)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>"> 
                            <td width="30"><? echo $i; ?></td>
                            <td><p><? echo $rows[csf('terms')]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
					?>
        		</table><?
			}
			?>
        
        <br>
		 <? echo signature_table(175, $data[0], "980px"); ?>
   </div>
   </div>
	<?
    exit();
}

if($action=="file_upload")
{
	header("Content-Type: application/json");
	$filename = time().$_FILES['file']['name']; 
	$location = "../../file_upload/".$filename; 
    //echo "0**".$filename; die;
	$uploadOk = 1;
	if(empty($mst_id))
	{
		$mst_id=$_GET['mst_id'];
	} 
    
	if(move_uploaded_file($_FILES['file']['tmp_name'], $location))
	{ 
		$uploadOk = 1;
	}
	else
	{ 
		$uploadOk=0; 
	} 
    // echo "0**".$uploadOk; die;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$id=return_next_id( "id","COMMON_PHOTO_LIBRARY", 1 ) ;
	$data_array .="(".$id.",".$mst_id.",'yarn_dying_bill_entry','file_upload/".$filename."','2','".$filename."','".$pc_date_time."')";
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name,insert_date";
	$rID=sql_insert("COMMON_PHOTO_LIBRARY",$field_array,$data_array,1);

	if($db_type==0)
	{
		if($rID==1 && $uploadOk==1)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$mst_id;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID==1 && $uploadOk==1)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$rID."**".$uploadOk."**INSERT INTO COMMON_PHOTO_LIBRARY(".$field_array.") VALUES ".$data_array;
		}
	}
	disconnect($con);
	die;
}

?>