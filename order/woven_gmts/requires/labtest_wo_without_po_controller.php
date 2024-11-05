<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];
//---------------------------------------------------- Start---------------------------------------------------------------------------
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if($action=="test_item_popup")
{
	echo load_html_head_contents("Lab Test Work Order", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function fn_close()
		{
			var tot_row=$('#tbl_list_search tr').length;
			var all_party_id=""; var all_party_rate=""; var txt_wo_qty=""; var txt_wo_amt=0;
			for(var i=1; i<=tot_row; i++)
			{
				var wo_qnty=$('#woQty_'+i).val()*1;
				var wo_party_id=$('#txt_individual_id'+i).val()*1;
				var wo_party_rate=$('#txt_net_rate_update'+i).val()*1;
				var wo_amount=$('#woAmount_'+i).val()*1;
				if(wo_qnty>0)
				{
					if(all_party_id=="") all_party_id=wo_party_id; else all_party_id+=","+wo_party_id;
					if(all_party_rate=="") all_party_rate=wo_party_rate; else all_party_rate+=","+wo_party_rate;
					if(txt_wo_qty=="") txt_wo_qty=wo_party_id+"_"+wo_qnty+"_"+wo_amount; else txt_wo_qty+=","+wo_party_id+"_"+wo_qnty+"_"+wo_amount;
					txt_wo_amt+=wo_amount;
				}
			}
			
			$('#all_party_id').val(all_party_id);
			$('#all_party_rate').val(all_party_rate);
			$('#txt_wo_qty').val(txt_wo_qty);
			$('#txt_wo_amt').val(txt_wo_amt);
			parent.emailwindow.hide();
		}
		
		function cal_amount(strCon)
		{
			var rate=  $('#txt_net_rate_update'+strCon).val()*1;
			var Qnty=  $("#woQty_"+strCon).val()*1;
			var wo_amt=rate*Qnty;
			$('#woAmount_'+strCon).val(number_format(wo_amt,2,'.' , ""));
		}
		
    </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:1040px;">
            <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <input type='hidden' id='all_party_id' value="<? echo $txt_party_type_id; ?>" />
            <input type='hidden' id='all_party_rate' value="<? echo $txt_party_type_name; ?>" />
            <input type='hidden' id='txt_wo_qty' value="<? echo $save_qty_break_data; ?>" />
            <input type='hidden' id='txt_wo_amt' value="<? echo $txt_amount; ?>" />
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table" >
                    <thead>
                        <th width="30">SL</th>
                        <th width="120">Test Category</th>
                        <th width="80">Test For</th>
                        <th width="100">Test Item</th>
                        <th width="70">Rate</th>
                        <th width="60">Upcharge %</th>
                        <th width="70">Upcharge Amount</th>
                        <th width="80">Net Rate</th>
                        <th width="70">Currency</th>
                        <th width="80">Net Rate <? echo $currency[$cbo_currency] ;?></th>
                        <th width="80">WO Rate</th>
                        <th width="80">WO Qty</th>
                        <th>WO Amount</th>
                    </thead>
                </table>
                <div style="width:1030px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table" id="tbl_list_search" >
                    <?
                        $prev_txt_party_type_id=explode(",",$txt_party_type_id);
                        $prev_party_id_arr=array();
                        foreach($prev_txt_party_type_id as $p_id)
                        {
                            $prev_party_id_arr[$p_id]=$p_id;
                        }
						
                        $variable_setting=return_field_value("lab_test_rate_update","variable_order_tracking","company_name=$cbo_company_name and variable_list=39");
                        if($variable_setting==1) {$dissable_cond="";} else  { $dissable_cond="disabled";}
                    
                        if($db_type==2) { $txt_workorder_date=change_date_format($txt_workorder_date,'yyyy-mm-dd',"-",1);}
                        else { $txt_workorder_date=change_date_format($txt_workorder_date,'yyyy-mm-dd');}
                        $current_currency=set_conversion_rate($cbo_currency,$txt_workorder_date);
                        $party_row_id=''; 
                        
                        $hidden_party_id=explode(",",$txt_party_type_id);
                        $hidden_party_value=explode(",",$txt_party_type_name);
                        $currency_id="";
                        $qt_break_arr=explode(",",$save_qty_break_data);
                        $prev_qty_breakdown=array();
                        foreach ($qt_break_arr as $value) 
                        {
                            $value_ref=explode("_",$value);
                            $prev_qty_breakdown[$value_ref[0]]=$value_ref[1];
                        }
                        $sql=sql_select("SELECT id, test_category, test_for, test_item, rate, upcharge_parcengate, upcharge_amount, net_rate, currency_id, testing_company FROM lib_lab_test_rate_chart WHERE status_active =1 AND is_deleted =0 and testing_company=$cbo_supplier and test_for=$cbo_test_for");
						$i=1; 
                        foreach($sql as $name)
                        {
                            $currency_id=$name[csf('currency_id')];
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            
                            $converted_currency=set_conversion_rate($name[csf('currency_id')],$txt_workorder_date);
                            $actual_currency=$converted_currency/$current_currency;
                            $actual_net_rate=$actual_currency*$name[csf('net_rate')];
                            
                            $key='';
                            if(in_array($name[csf('id')],$hidden_party_id)) 
                            { 
                                if($party_row_id=="") $party_row_id=$i; else $party_row_id.=",".$i;
                                $key = array_search($name[csf('id')], $hidden_party_id);
                                if(trim($hidden_party_value[$key])!=="") $update_net_rate=$hidden_party_value[$key]; else $update_net_rate=$actual_net_rate;	
                            }
                            else $update_net_rate=$actual_net_rate;	
                            
                            if($prev_qty_breakdown[$name[csf('id')]]>0) $wo_qty=$prev_qty_breakdown[$name[csf('id')]];
                            else
                            {
                                if($prev_party_id_arr[$name[csf('id')]]!="") $wo_qty='1'; else $wo_qty='';
                            }
                            
                            $TotalAmount=$wo_qty*$update_net_rate;
    
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" > 
                                <td width="30" align="center" title="<? echo $name[csf('id')];?>"><?php echo "$i"; ?>
                                    <input type="hidden" name="txt_individual_id<?php echo $i ?>" id="txt_individual_id<?php echo $i ?>" value="<? echo $name[csf('id')]; ?>"/>	
                                    <input type="hidden" name="txt_individual<?php echo $i ?>" id="txt_individual<?php echo $i ?>" value="<? echo $actual_net_rate; ?>"/>
                                </td>	
                                <td width="120"><p><? echo $testing_category[$name[csf('test_category')]]; ?></p></td>
                                <td width="80"><p><? echo $test_for[$name[csf('test_for')]]; ?></p></td>
                                <td width="100"><p><? echo $name[csf('test_item')]; ?></p></td>
                                <td width="70" align="right"><? echo number_format($name[csf('rate')],2); ?></td>
                                
                                <td width="60" align="right"><? echo number_format($name[csf('upcharge_parcengate')],2); ?></td>
                                <td width="70" align="right"><? echo number_format($name[csf('upcharge_amount')],2); ?></td>
                                <td width="80" align="right"><? echo number_format($name[csf('net_rate')],2); ?></td>
                                <td width="70"><? echo $currency[$name[csf('currency_id')]]; ?></td>
                                <td width="80" align="right"><? echo number_format($actual_net_rate,2); ?></td>
                                <td width="80" id="last_<?php echo $i ?>" align="center"><input type="text" id="txt_net_rate_update<?php echo $i ?>" name="txt_net_rate_update<?php echo $i ?>" value="<? echo number_format($update_net_rate,2); ?>" style="width:63px" class="text_boxes_numeric"  onkeyup="update_value_calculation(<?php echo $i ?>,this.value,<?php echo $name[csf('id')] ?>)" <? echo $dissable_cond; ?>/>
                                </td>
                                <td width="80"><input type="text" class="text_boxes_numeric" style="width:60px" value="<? echo $wo_qty; ?>" onKeyUp="cal_amount(<? echo $i;?>)"; id="woQty_<? echo $i;?>" ></td>
    
                                <td><input type="text" class="text_boxes_numeric" style="width:60px" name="woAmount" id="woAmount_<? echo $i;?>" value="<? $wo_amt=$wo_qty*$update_net_rate; echo number_format($wo_amt,2,".",""); ?>" readonly></td>
                            </tr>
                            <?
                            $i++;
                        }
                    ?>
                    </table>
                </div>
                 <table width="1020" cellspacing="0" cellpadding="0" style="border:none" align="center">
                    <tr>
                        <td align="center" valign="bottom">
                                <div style="width:100%; float:left" align="center">
                                  <input type="button" name="close" onClick="fn_close();" class="formbutton" value="Close" style="width:100px" />
                                </div>
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

if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0) 
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}	
		$id=return_next_id("id", "wo_labtest_mst", 1);			
		$new_sys_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'LWO', date("Y"), 5, "select labtest_prefix, labtest_prefix_num from wo_labtest_mst where company_id=$cbo_company_name and entry_form=575 and TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id DESC ", "labtest_prefix", "labtest_prefix_num",""));
		
		
		$field_array="id, labtest_prefix, labtest_prefix_num, labtest_no, entry_form, company_id, supplier_id, wo_date,delivery_date, currency, ecchange_rate, pay_mode, attention, address, ready_to_approved, vat_percent, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id.", '".$new_sys_number[1]."', '".$new_sys_number[2]."', '".$new_sys_number[0]."', 575, ".$cbo_company_name.", ".$cbo_supplier.", ".$txt_workorder_date.", ".$txt_delivery_date.", ".$cbo_currency.", ".$txt_exchange_rate.", ".$cbo_pay_mode.", ".$txt_attention.", ".$txt_address.", ".$cbo_ready_to_approved.", ".$txt_vat_per.", '".$user_id."', '".$pc_date_time."',1,0)";
		
		$return_no=str_replace("'",'',$new_sys_number[0]);
		$rID=sql_insert("wo_labtest_mst",$field_array,$data_array,0); 
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
		}
		else if($db_type==1 || $db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
		}
		check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		if( str_replace("'","",$update_id) == "")
		{
			echo "15"; disconnect($con);exit(); 
		}
		if ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}	
		
		$txt_booking_no=$txt_workorder_no;
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		$sales_order=0;
		/*$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}*/
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a, com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				 disconnect($con);die;
			}
		}
		
		$field_array="supplier_id*wo_date*delivery_date*currency*ecchange_rate*pay_mode*attention*address*ready_to_approved*vat_percent*updated_by*update_date";
		$data_array="".$cbo_supplier."*".$txt_workorder_date."*".$txt_delivery_date."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$txt_attention."*".$txt_address."*".$cbo_ready_to_approved."*".$txt_vat_per."*'".$user_id."'*'".$pc_date_time."'";
		$rID=sql_update("wo_labtest_mst",$field_array,$data_array,"id",$update_id,1);	
		$return_no=str_replace("'",'',$txt_workorder_no);
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2)
		{
			if($rID)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$update_id);
			}
		}
		check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); } 
		
		$update_id=str_replace("'","",$update_id);
		// master table delete here---------------------------------------
		if($update_id=="" || $update_id==0){ echo "15**0";  disconnect($con);die;}
		
		$txt_booking_no=$txt_workorder_no;
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		/*$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}*/
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a, com_pi_item_details b"," a.id=b.pi_id and b.work_order_no=$txt_booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				 disconnect($con);die;
			}
		}
		
		$dtlsrID = sql_update("wo_labtest_mst",'status_active*is_deleted','0*1',"id",$update_id,1);
		$return_no=str_replace("'",'',$txt_workorder_no);
		if($db_type==0 )
		{
			if($dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "2**".$return_no."**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($dtlsrID)
			{
				oci_commit($con);   
				echo "2**".$return_no."**".$update_id;
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	/*$poCond="";
	if(str_replace("'","",$txt_order_id )){
		$poCond="and a.id=$txt_order_id";
	}

	$sql_order=sql_select("select a.job_no_mst, a.id, a.po_quantity,b.job_quantity  from wo_po_break_down a,wo_po_details_master b 
	where job_no_mst=$txt_job_no $poCond  and a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by a.id,a.job_no_mst,a.po_number,a.po_quantity,b.job_quantity");*/
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}		
		$id_dtls=return_next_id( "id", "wo_labtest_dtls", 1 ) ;
		$id_order_dtls=return_next_id( "id", "wo_labtest_order_dtls", 1 ) ;
		
		if(str_replace("'","",$txt_color)!="")
		{ 
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				//$color_id = return_id( str_replace("'","",$txt_color), $color_library, "lib_color", "id,color_name");  
				if(str_replace("'","",$txt_color)!="")
				{ 
					if (!in_array(str_replace("'","",$txt_color),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$txt_color), $color_library, "lib_color", "id,color_name","179");  
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$txt_color);
					}
					else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color); 
				}
				else $color_id=0;
				$new_array_color[$color_id]=str_replace("'","",$txt_color);
			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color); 
		}
		else
		{
			$color_id=0;
		}
		
		$field_array="id, mst_id, entry_form, test_for, test_item_id, test_item_value, color, amount, discount, labtest_charge, wo_value, vat_amount, wo_with_vat_value, remarks, inserted_by, insert_date, status_active, is_deleted, qty_breakdown";
		//$field_array1="id, mst_id, dtls_id, wo_value, order_qty, inserted_by, insert_date, status_active, is_deleted";
		$data_array="(".$id_dtls.",".$update_id.",575,".$cbo_test_for.",".$txt_party_type_id.",".$txt_party_type_name.",'".$color_id."',".$txt_amount.",".$txt_discount.",".$txt_delivery_charge.",".$txt_wo_value.",".$txt_vat_amount.",".$txt_wo_value_with_vat.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,".$save_qty_break_data.")";
		/*foreach($sql_order as $name)
		{
			$order_percentage=($name[csf('po_quantity')]/$name[csf('job_quantity')])*100;
			$txt_wo_value=str_replace("'","",$txt_wo_value);
			//$workorder_value=($txt_wo_value*$order_percentage)/100;
			$workorder_value=number_format(($txt_wo_value*$order_percentage)/100,4,".","");
			$order_id=$name[csf('id')];
			$order_qty=$name[csf('po_quantity')];
			
			if ($data_array1!=1) $data_array1 .=",";
			$data_array1 .="(".$id_order_dtls.",".$update_id.",".$id_dtls.",".$order_id.",".$workorder_value.",".$order_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id_order_dtls=$id_order_dtls+1;
		}*/
		//$rID=sql_insert("wo_labtest_order_dtls",$field_array1,$data_array1,0); 
		$rID1=sql_insert("wo_labtest_dtls",$field_array,$data_array,0);
		
		
		//echo "10**".$rID.'**'.$rID1.'**';
		 //echo "insert into wo_labtest_dtls (".$field_array.") values".$data_array;
		//die;
		if($db_type==0)
		{
			if($rID1 )
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$id_dtls)."**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$id_dtls)."**".str_replace("'","",$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1){
				oci_commit($con);  
				echo "0**".str_replace("'","",$id_dtls)."**".str_replace("'","",$update_id);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'","",$id_dtls)."**".str_replace("'","",$update_id);
			}
		}
		check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	    if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; disconnect($con); die;}
		
		$txt_booking_no=$txt_workorder_no;
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		/*$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				die;
			}
		}*/
		
	  	if(str_replace("'","",$txt_color)!="")
		{ 
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				//$color_id = return_id( str_replace("'","",$txt_color), $color_library, "lib_color", "id,color_name");  
				//echo $$txtColorName.'='.$color_id.'<br>';
				if(str_replace("'","",$txt_color)!="")
				{ 
					if (!in_array(str_replace("'","",$txt_color),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$txt_color), $color_library, "lib_color", "id,color_name","179");  
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$txt_color);
					}
					else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color); 
				}
				else $color_id=0;
				$new_array_color[$color_id]=str_replace("'","",$txt_color);
			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color); 
		}
		else
		{
			$color_id=0;
		}
			
		$field_array="test_for*test_item_id*test_item_value*color*amount*discount*labtest_charge*wo_value*vat_amount*wo_with_vat_value*remarks*updated_by*update_date*qty_breakdown";
		$data_array="".$cbo_test_for."*".$txt_party_type_id."*".$txt_party_type_name."*'".$color_id."'*".$txt_amount."*".$txt_discount."*".$txt_delivery_charge."*".$txt_wo_value."*".$txt_vat_amount."*".str_replace(",","",$txt_wo_value_with_vat)."*".$txt_remarks."*'".$user_id."'*'".$pc_date_time."'*".$save_qty_break_data."";//,".$txt_vat_amount.",".$txt_wo_value_with_vat."
		//print_r($data_array);
		/*$field_array1="id,mst_id,job_no,dtls_id,order_id,wo_value,order_qty,inserted_by,insert_date,status_active,is_deleted,qty_breakdown";
		$add_comma=0;
		$id_order_dtls=return_next_id( "id", "wo_labtest_order_dtls", 1 ) ;
		$sql_query=execute_query("delete from wo_labtest_order_dtls where mst_id=$update_id and  dtls_id=$update_dtls_id");
		foreach($sql_order as $name)
		{
			$order_percentage=($name[csf('po_quantity')]/$name[csf('job_quantity')])*100;
			$txt_wo_value=str_replace("'","",$txt_wo_value);
			//$workorder_value=($txt_wo_value*$order_percentage)/100;
			$workorder_value=number_format(($txt_wo_value*$order_percentage)/100,4,".","");
			$order_id=$name[csf('id')];
			$order_qty=$name[csf('po_quantity')];
		
			if ($data_array1) $data_array1 .=",";
			$data_array1 .="(".$id_order_dtls.",".$update_id.",".$txt_job_no.",".$update_dtls_id.",".$order_id.",".$workorder_value.",".$order_qty.", ".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,".$save_qty_break_data.")";
			$id_order_dtls=$id_order_dtls+1;
			$i++;
		}
		$rID1=true;*/
		
		$update_dtls_id=str_replace("'", "", $update_dtls_id);
		$update_dtls_id="'".trim($update_dtls_id)."'";

	    $rID=sql_update("wo_labtest_dtls",$field_array,$data_array,"id",$update_dtls_id,1);
		/*if($data_array1 !="")
		{
			$rID1=sql_insert("wo_labtest_order_dtls",$field_array1,$data_array1,0);
		}*/
		//	 echo "insert into wo_labtest_order_dtls (".$field_array1.") Values ".$data_array1."";die;
		//echo "10**".$rID.'='.$rID1;die;
       
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$update_id);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$update_id);;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$update_id);;
			}
			else
			{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$update_dtls_id)."**".str_replace("'","",$update_id);;
			}
		}
		check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$txt_booking_no=$txt_workorder_no;
		$is_approved=0;
		$sql=sql_select("select is_approved from wo_booking_mst where booking_no=$txt_booking_no");
		foreach($sql as $row){
			$is_approved=$row[csf('is_approved')];
		}
		if($is_approved==1){
			echo "approved**".str_replace("'","",$txt_booking_no);
			 disconnect($con);die;
		}
		/*$sales_order=0;
		$sqls=sql_select("select job_no from fabric_sales_order_mst where sales_booking_no=$txt_booking_no");
		foreach($sqls as $rows){
			$sales_order=$rows[csf('job_no')];
		}
		if($sales_order){
			echo "sal1**".str_replace("'","",$txt_booking_no)."**".$sales_order;
			die;
		}
		if(str_replace("'","",$cbo_pay_mode)==2){
			$pi_number=return_field_value( "pi_number", "com_pi_master_details a,com_pi_item_details b"," a.id=b.pi_id  and b.work_order_no=$txt_booking_no  and a.status_active=1 and  a.is_deleted=0  and b.status_active=1 and  b.is_deleted=0");
			if($pi_number){
				echo "pi1**".str_replace("'","",$txt_booking_no)."**".$pi_number;
				die;
			}
		}*/
		
		$rID=execute_query( "update wo_booking_dtls set status_active=0,is_deleted =1 where  id =$update_id_details",0);	
			
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);  
				echo "2**".str_replace("'","",$txt_booking_no);
			}
			else{
				oci_rollback($con);  
				echo "10**".str_replace("'","",$txt_booking_no);
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="load_dtls_data_view")
{
	$arr=array (0=>$test_for,2=>$color_library);

	$sql= "select id,mst_id,entry_form,test_for,test_item_id,color,amount,discount,labtest_charge,wo_with_vat_value,remarks from wo_labtest_dtls 
where mst_id=$data ";

	echo  create_list_view("list_view", "Test For,Remarks,Color,Amount,Quick Delv Charge,Discount,WO Value", "110,150,100,80,80,80,80","720","320",0, $sql , "get_php_form_data", "id", "'load_php_dtls_data_to_form'", 1, "test_for,0,color,0,0,0,0", $arr , "test_for,remarks,color,amount,labtest_charge,discount,wo_with_vat_value", "requires/labtest_wo_without_po_controller",'','0,0,0,5,5,5,5','','');
	exit();
}

if ($action=="workorder_popup")
{
  	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('selected_booking').value=id;
			parent.emailwindow.hide();
		}
	</script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                   <th colspan="6" align="center"><? echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?></th>
                </tr>
                <tr>                	 
                    <th width="150">Company Name</th>
                    <th width="150">Test Company</th>
                    <th width="100">WO No</th>
                    <th width="200">WO Date Range</th>
                    <th>&nbsp;</th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_booking">
                    	<? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name, ""); ?>
                    </td>
                    <td><? echo create_drop_down( "cbo_supplier_name", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=26 order by a.supplier_name","id,supplier_name", 1, "-- Select Test Company--", 0, "","" ); ?></td>
                    <td><input name="txt_wo_prifix" id="txt_wo_prifix" class="text_boxes" style="width:100px" ></td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date">
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date">
                    </td> 
                    <td align="center">
                    	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_wo_prifix').value+'_'+document.getElementById('cbo_search_category').value, 'create_wo_search_list_view', 'search_div', 'labtest_wo_without_po_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                </tr>
                <tr class="general">
                    <td colspan="6"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
    	<div align="center" id="search_div"></div>   
    </form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if ($action=="create_wo_search_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company="and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer="and supplier_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($db_type==0)
	{
		$booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		$year_id=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	else if($db_type==2)
	{
		$booking_year_cond=" and to_char(insert_date,'YYYY')=$data[4]";	
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
		$year_id=" to_char(insert_date,'YYYY') as year";
	}
	
	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and labtest_prefix_num like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
	}
    else if($data[6]==1)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and labtest_prefix_num ='$data[5]' "; else $booking_cond="";
	}
   	else if($data[6]==2)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and labtest_prefix_num like '$data[5]%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($data[6]==3)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and labtest_prefix_num like '%$data[5]'  $booking_year_cond  "; else $booking_cond="";
	}
	
	$approved=array(0=>"No",1=>"Yes");
	$suplier=return_library_array( "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=26 order by a.supplier_name",'id','supplier_name');
	
	$arr=array(2=>$comp,2=>$suplier,3=>$currency,7=>$pay_mode,9=>$approved);
	$sql= "select id, labtest_prefix, labtest_prefix_num, labtest_no, entry_form, company_id, supplier_id, wo_date, delivery_date, currency, ecchange_rate, pay_mode, attention,address, ready_to_approved, inserted_by, insert_date, $year_id from wo_labtest_mst where status_active=1 and is_deleted=0 and entry_form=179 $company $buyer $booking_date $booking_cond order by id";
	
	echo  create_list_view("list_view", "WO No,Year,Test Company,Currency,Exchange Rate,Wo Date,Delivery Date,Pay Mode,Attention,Ready To Approved", "60,60,120,70,60,70,70,80,150,70","860","250",0, $sql , "js_set_value", "id,labtest_no", "", 1, "0,0,supplier_id,currency,0,0,0,pay_mode,0,ready_to_approved", $arr , "labtest_prefix_num,year,supplier_id,currency,ecchange_rate,wo_date,delivery_date,pay_mode,attention,ready_to_approved", '','','0,0,0,0,1,3,3,0,0,0','','');
	exit();
}

if ($action=="load_php_mst_data")
{
	$data=explode("_",$data);
	$sql= "select id, labtest_prefix, labtest_prefix_num, labtest_no, entry_form, company_id, supplier_id, wo_date, delivery_date, currency, ecchange_rate, pay_mode, attention, address, ready_to_approved, vat_percent, inserted_by, insert_date from wo_labtest_mst where labtest_no='$data[1]'"; 
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('txt_workorder_date').value = '".change_date_format($row[csf("wo_date")])."';\n";
		echo "document.getElementById('cbo_supplier').value = '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('cbo_currency').value = '".$row[csf("currency")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value = '".$row[csf("ecchange_rate")]."';\n";
		echo "document.getElementById('cbo_pay_mode').value = '".$row[csf("pay_mode")]."';\n";
		echo "document.getElementById('txt_delivery_date').value = '".change_date_format($row[csf("delivery_date")])."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('txt_attention').value = '".$row[csf("attention")]."';\n";
		echo "document.getElementById('txt_vat_per').value = '".$row[csf("vat_percent")]."';\n";
		echo "document.getElementById('txt_address').value = '".$row[csf("address")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "$('#cbo_company_name').attr('disabled',true);";
		echo "$('#cbo_supplier').attr('disabled',true);";
	}
	exit();
}

if($action=="load_php_dtls_data_to_form")
{
	$sql= "select id, mst_id, test_for, test_item_id, test_item_value, color, amount, discount, labtest_charge, wo_value, vat_amount, wo_with_vat_value, remarks, qty_breakdown from wo_labtest_dtls where id=$data and entry_form=179";
	//echo $sql;die; 
	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_test_for').value = '".$row[csf("test_for")]."';\n";
		echo "document.getElementById('txt_party_type_id').value = '".$row[csf("test_item_id")]."';\n";
		echo "document.getElementById('txt_party_type_name').value = '".$row[csf("test_item_value")]."';\n";
		echo "document.getElementById('txt_color').value = '".$color_library[$row[csf("color")]]."';\n";
		//echo "document.getElementById('txt_amount').value = '".$row[csf("amount")]."';\n";
		echo "document.getElementById('txt_amount').value = '".$row[csf("amount")]."';\n";
		echo "document.getElementById('txt_discount').value = '".$row[csf("discount")]."';\n";
		echo "document.getElementById('txt_delivery_charge').value = '".$row[csf("labtest_charge")]."';\n";
		echo "document.getElementById('txt_wo_value').value = '".$row[csf("wo_value")]."';\n"; 
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_vat_amount').value = number_format('".$row[csf("vat_amount")]."',4);\n"; 
		echo "document.getElementById('txt_wo_value_with_vat').value = number_format('".$row[csf("wo_with_vat_value")]."',4);\n";
		echo "document.getElementById('save_qty_break_data').value = '".$row[csf("qty_breakdown")]."';\n";
		
		echo "document.getElementById('update_dtls_id').value = '".$row[csf("id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_labtest_wo_dtls',2);\n";
	}
	exit();
}

if($action=="show_trim_booking_report")
{
    extract($_REQUEST);

	echo load_html_head_contents("Lab Test Work Order", "../../", 1, 1,'','','');
	$data=explode('*',str_replace("'","",$data));
	
	$sql="select a.id, a.labtest_no, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.address, a.attention,a.ready_to_approved,a.vat_percent from wo_labtest_mst a where a.id='$data[1]' and a.company_id='$data[0]'"; // new
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	
	$lab_test_rate_library=return_library_array( "select id, test_item from lib_lab_test_rate_chart", "id", "test_item"  );
	
	$supplier_library=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id 
	and b.party_type=26 order by a.supplier_name","id","supplier_name"  );
	$total_charge=0;
	$sql_dtls= "select id,mst_id,po_id,job_no,entry_form,test_for,test_item_id,test_item_value,color,amount,discount,labtest_charge,wo_value,vat_amount,remarks,test_item_id,qty_breakdown
	from wo_labtest_dtls
	where mst_id=$data[1]"; 
	$sql_result= sql_select($sql_dtls);
	$amount_arr=array();
	$job_nos='';
	$poArr=array();
	foreach($sql_result as $inf)
	{
		$amount_arr[$inf[csf('job_no')]]['wo_value']=$inf[csf('wo_value')];	
		$total_charge+=$inf[csf('labtest_charge')];
		if($job_nos=='') $job_nos=$inf[csf('job_no')];else $job_nos.=",".$inf[csf('job_no')];
		if($inf[csf('po_id')]){
			$poArr[$inf[csf('po_id')]]=$inf[csf('po_id')];
		}
	}
	$jobid=array_unique(explode(",",$job_nos));
	$jobs='';
	foreach($jobid as $jid)
	{
		if($jobs=='') $jobs="'$jid'";else $jobs.=","."'$jid'";
	}
	$poCond="";
	if(count($poArr)>0){
		$poCond="and b.id in (".implode(",",$poArr).")";
	}
	//echo $jobs;
	//echo $job_nos;
	$po_numberArr=array();
	$po_shipdateArr=array();
	$pos_sql="select b.id, b.po_number,b.shipment_date,a.buyer_name,a.style_ref_no as style_ref, a.job_no from wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst and a.job_no in($jobs) $poCond ";
	$sql_results=sql_select($pos_sql);
	foreach($sql_results as $row)
	{
		$buyer_library[$row[csf('job_no')]]=$row[csf('buyer_name')];
		$style_ref_no_library[$row[csf('job_no')]]=$row[csf('style_ref')];
		$po_no_arr[$row[csf('job_no')]].=$row[csf('po_number')].',';
		$po_numberArr[$row[csf('id')]]=$row[csf('po_number')];
		$po_shipdateArr[$row[csf('id')]]=$row[csf('shipment_date')];
	}

	$buyer_name='';
	foreach($jobid as $job)
	{
			if($buyer_name=='') $buyer_name=$buyer_name_arr[$buyer_library[$job]];else $buyer_name.=",".$buyer_name_arr[$buyer_library[$job]];
	}


$varcode_booking_no=$dataArray[0][csf('labtest_no')];


?>
<div style="width:900px;" align="center">
	
	<table width="900" style="table-layout: fixed;">
		<tr>
			<td width="150" align="center" style="font-size: 14px"><strong><? 
				$company_logo=sql_select( "select b.image_location from common_photo_library b where b.master_tble_id='$data[0]' and b.form_name='company_details'");
				?>
	            <img src="../../<?  echo $company_logo[0][csf('image_location')]; ?>"  width="100px"  height="70" ></strong>
        	</td>
			<td align="center" style="font-size: 14px; text-align:center;" valign="top">
            	<strong style="font-size: 30px"><? echo $company_library[$data[0]]; ?></strong><br>
            	<strong><?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						 <? echo $result[csf('plot_no')]; ?> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('road_no')]; ?> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('block_no')];?> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('city')];?> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('zip_code')]; ?> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('province')];?> 
						 <? if($result[csf('level_no')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('email')];?> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('website')];
						
						 
					}
                ?> </strong>
            </td>
			<td width="300" id="barcode_img_id" align="right" style="font-size: 14px"></td>
		</tr>
		<tr>
			<td></td><td align="center"><u style="font-size: 18px;font-weight: bold;">Lab Test Work Order</u></td><td></td>
		</tr>
	</table>
<div style="margin-top: 20px"></div>
	<table align="center">
		<tr>
			<td width="80"><strong>To</strong></td>
			<td width="150"><b> : </b><? echo $supplier_library[$dataArray[0][csf('supplier_id')]];?></td>
			<td width="110"><strong>Buyer</strong></td>
			<td width="150"><b> : </b><? echo implode(",",array_unique(explode(",",$buyer_name))); ?></td>
			<td width="90"><strong>Wo No.</strong></td>
			<td width="150"><b> : </b><? echo $dataArray[0][csf('labtest_no')];?></td>

		</tr>
		<tr>
			<td><strong>Address</strong></td>
			<td><b> : </b><? echo $dataArray[0][csf('address')]; ?></td>
			
			<td><strong>Pay Mode</strong></td>
			<td><b> : </b><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
			<td><strong>Wo Date.</strong></td>
			<td><b> : </b><? echo change_date_format($dataArray[0][csf('wo_date')]); ?></td>

		</tr>
		<tr>
			<td><strong>Attention</strong></td>
			<td><b> : </b><? echo $dataArray[0][csf('attention')];?></td>
			<td><strong>Delivery Date</strong></td>
			<td><b> : </b><? echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
			<td><strong>Rate For.</strong></td>
			<td><b> : </b><?
			 if($total_charge>0) {$rate_for='Express';}  else  { $rate_for='Regular';}
			 echo $rate_for; ?></td>
		</tr>
		<tr>
			<td><strong>Currency</strong></td>
			<td ><b> : </b><? echo $currency[$dataArray[0][csf('currency')]]; ?></td>
			<td><strong>Exchange Rate</strong></td>
			<td ><b> : </b><? echo $dataArray[0][csf('ecchange_rate')]; ?></td>
			<td><strong> Vat %</strong></td>
			<td><b> : </b><? echo $dataArray[0][csf('vat_percent')]; ?></td>
		</tr>
		
	</table>

 
        <br/> <br/> <br/>
	
         <table  cellspacing="0" width="900"  border="1" rules="all" class=""  align="left" style="table-layout: fixed;">
         <thead bgcolor="#dddddd" >
             <tr>
             		<th width="30">SL</th>
                	<th width="100">Style/Job No</th> 
                    <th width="100">Po No</th>  
                    <th width="80">Ship Date</th>  
                    <th width="70">Test For</th>   
                    <th width="100">Remarks</th> 
                    <th width="80">Color</th>   
                    <th width="200">Test Item</th>   
                    <th width="50">Qty</th>
                    <th width="50">Rate</th>
                    <th width="80">Amount</th>
                </tr>
        </thead>
        <tbody> 
   
<?
	
	
	$i=1; $j=1;
	$all_job_no='';
	$sl_arr=array();
	$cbo_currency=$data[2];
	$txt_workorder_date=$data[3];
	if($db_type==2) { $txt_workorder_date=change_date_format($txt_workorder_date,'yyyy-mm-dd',"-",1);}
	else { $txt_workorder_date=change_date_format($txt_workorder_date,'yyyy-mm-dd');}
	$current_currency=set_conversion_rate($cbo_currency,$txt_workorder_date);
	$grand_wo_value=0;
	foreach($sql_result as $row)
	{
		
		if ($j%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$test_item=explode(",",$row[csf('test_item_id')]);
			$test_item_value=explode(",",$row[csf('test_item_value')]);
			$test_item_qty_break=explode(",",$row[csf('qty_breakdown')]);
			
			$itemDataArr=array();
			foreach($test_item_qty_break as $item_data_string){
				list($item_id,$qty,$amount)=explode('_',$item_data_string);
				$itemDataArr[$item_id]=array(
					'qty'=>$qty,
					'amu'=>$amount
				);
			}
			
			//echo $row[csf('test_item_value')];
			$colum_span=count(explode(",",$row[csf('test_item_id')]));
			$colum_span=$colum_span+4;
			if(trim($all_job_no)!='') $all_job_no.=",'".$row[csf("job_no")]."'";
			else $all_job_no="'".$row[csf("job_no")]."'";
			$total_net_reate=0;
			
			//print_r($test_item_value);
			$index=0;
			foreach($test_item as $name)
			{
				
				if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$converted_currency=set_conversion_rate($name[csf('currency_id')],$txt_workorder_date);
				$actual_currency=$converted_currency/$current_currency;
				//$actual_net_rate=$actual_currency*$name[csf('net_rate')];
				
				if($row[csf("po_id")]==""){
					$po_no=rtrim($po_no_arr[$row[csf("job_no")]],',');
				}else{
					$po_no=$po_numberArr[$row[csf('po_id')]];
		            $shipment_date=$po_shipdateArr[$row[csf('po_id')]];
				}
				//$po_ids=explode(",",$po_no);
				//if(count($po_ids)>3)
				if(!in_array($i,$main_row))
				{
				$main_row[]=$i; //style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"
		?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center" style="font-size:15px" rowspan="<? echo $colum_span?>"><? echo $i; ?></td>
                    <td align="center" style="font-size:15px" rowspan="<? echo $colum_span?>"><? echo $style_ref_no_library[$row[csf("job_no")]].'<br/>'.$row[csf("job_no")]; ?></td>
                      <td  align="center" style="word-break:break-all;font-size:15px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" rowspan="<? echo $colum_span?>"><p><? echo $po_no; ?></p></td>
                      <td  align="center" style="word-break:break-all;font-size:15px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" rowspan="<? echo $colum_span?>"><p><? echo date("d-m-Y",strtotime($shipment_date)); ?></p></td>
                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $test_for[$row[csf("test_for")]]; ?></td>
                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $row[csf("remarks")]; ?></td>
                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $color_library[$row[csf("color")]]; ?></td>
                    <td align="left" style="word-break:break-all;font-size:15px"> <?  echo $lab_test_rate_library[$name];  ?> </td>
                   
                   	<td align="right"><? echo $itemDataArr[$name]['qty'];?></td>
                    <td align="right"><? echo number_format($test_item_value[$index],2);?></td>
                    <td align="right" style="font-size:15px">
					<? 
					  	$total_net_reate+=$itemDataArr[$name]['amu'];
						echo number_format($itemDataArr[$name]['amu'],4);
					?>
                    </td>
                </tr>
		   <? 
				}
				else
				{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
                    
                    <td align="left" style="font-size:15px"><? echo $lab_test_rate_library[$name]; ?></td>
                    
                   	<td align="right"><? echo $itemDataArr[$name]['qty'];?></td>
                    <td align="right"><? echo number_format($test_item_value[$index],2);?></td>
                    <td align="right" style="font-size:15px">
					<? 
					  	$total_net_reate+=$itemDataArr[$name]['amu'];
						echo number_format($itemDataArr[$name]['amu'],4);
					?>
                    </td>
                </tr>
                
				<?
				}
				$index++;
			}
			?>
             <tr bgcolor="#E3E3E3">
                <td align="right" style="font-size:15px" colspan="3"><b>Gross Amount</b></td>
                <td align="right" style="font-size:15px"><b><? echo number_format($total_net_reate,4); ?></b></td>
			</tr>
            <tr bgcolor="<? //echo $bgcolor; ?>">
                <td align="right" style="font-size:15px" colspan="3" >Add Quick Delv Charge (USD)</td>
                <td align="right" style="font-size:15px"><? echo number_format($row[csf("labtest_charge")],4) ; ?></td>
			</tr>
              <tr bgcolor="<? //echo $bgcolor; ?>">
                <td align="right" style="font-size:15px" colspan="3" >Less Discount</td>
                <td align="right" style="font-size:15px"><? echo number_format($row[csf("discount")],4) ; ?></td>
			</tr>
            </tr>
              <tr bgcolor="#E3E3E3">
               <td align="right" style="font-size:15px" colspan="3" ><b>Total Value</b></td>
                <td align="right" style="font-size:15px"><b>
				<?
				 $toatal_wo_value=$row[csf("labtest_charge")]+$total_net_reate-$row[csf("discount")];
				 echo number_format(($toatal_wo_value),4) ;
				  ?></b></td>
			</tr>
            <!-- new develop -->
            <tr bgcolor="#E3E3E3">
               <td colspan="10" align="right" style="font-size:15px" ><b>Vat Amount</b></td>
                <td align="right" style="font-size:15px"><b>
					<?
						$toatal_vat_percent= $row[csf("vat_amount")];
						echo number_format(($toatal_vat_percent),4) ;
                    ?>
                 </b>
                </td>
			</tr>
            <tr bgcolor="#E3E3E3">
               <td colspan="10" align="right" style="font-size:15px"><b>Wo Value</b></td>
                <td align="right" style="font-size:15px"><b>
				<?
				 $wo_totall= $toatal_vat_percent+$toatal_wo_value;
				 $grand_wo_value+=$wo_totall;
				 echo number_format(($wo_totall),4) ;
				  ?></b></td>
			</tr>
            <!--  -->
            
            <?
		
        $i++;
        }

         
	   $mcurrency="";
	   $dcurrency="";
	   if($cbo_currency==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
	   }
	   if($cbo_currency==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($cbo_currency==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
	   
       
        ?>
        	<tr bgcolor="#B7B7B7">
                <td align="right" style="font-size:15px" colspan="10"><b>Grand Total</b></td>
                <td align="right" style="font-size:15px"><b><? echo number_format(($grand_wo_value),4) ; ?></b></td>

			</tr>
			<tr>
				
				<td colspan="3"><strong>In Words:</strong></td>
				<td align="left" colspan="7" style="font-size:12px;"><b><? echo number_to_words(def_number_format($grand_wo_value,2,""),$mcurrency, $dcurrency);?></b></td>
			</tr>
			
			</tbody>
     
      </table>   
	
    <table  cellspacing="0" width="800"  border="1" rules="all" class=""  align="left" style=" display:none">
        <thead bgcolor="#dddddd" >
        	<tr>
                 <th colspan="9" align="left">Comments</th>   
           </tr>
           <tr>
                <th width="60">SL</th>
                <th width="120">Job No</th>   
                <th width="130">Pre-Cost Value</th> 
                <th width="140">WO Value</th>
                <th width="140">Balance</th>    
                <th >Comments</th>   
                       
             </tr>
        </thead>
        <tbody> 
   
<?
	$all_job_no=implode(",",array_unique(explode(",",$all_job_no)));
	
	$sql_pre_cost="select a.costing_per,b.lab_test,a.job_no from wo_pre_cost_mst a, wo_pre_cost_dtls b where a.job_no=b.job_no and a.job_no in ($all_job_no)";
	$result_precost= sql_select($sql_pre_cost);
	$job_arr_labtest=array();
	foreach($result_precost as $inf)
	{
		$costing_per=$inf[csf('costing_per')];
		if($costing_per==1)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/12;
		}
		else if($costing_per==2)
		{
			$costing_per_pcs=$inf[csf('lab_test')];
		}
		else if($costing_per==3)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/24;
		}
		else if($costing_per==4)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/36;
		}
		else if($costing_per==5)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/48;
		}
		$job_arr_labtest[$inf[csf('job_no')]]=$costing_per_pcs;
	}
	
	$poIdCond="";
	$poIdCond1="";
	if(count($poArr)>0){
		$poIdCond="and id in (".implode(",",$poArr).")";
		$poIdCond1="and order_id in (".implode(",",$poArr).")";
	}
	$sql_order_qty="select sum(po_quantity) as po_quantity,job_no_mst from  wo_po_break_down  where job_no_mst in ($all_job_no) and status_active=1  and is_deleted=0 $poIdCond group by job_no_mst ";
	
	$result_order= sql_select($sql_order_qty);
	$job_order_arr=array();
	foreach($result_order as $value)
	{
		$job_order_arr[$value[csf('job_no_mst')]]=$value[csf('po_quantity')];
	}
	$sql_order= "select job_no,sum(wo_value) as total_wo_value from wo_labtest_order_dtls where job_no in ($all_job_no) and status_active=1 
	and is_deleted=0 $poIdCond1  group by  job_no  "; 
	//echo $sql_order;
	$result= sql_select($sql_order);
	$i=1;
	$commants='';
	foreach($result as $val)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$total_wo_value=$amount_arr[$val[csf('job_no')]]['wo_value'];
			$total_budget=$job_order_arr[$val[csf('job_no')]]*$job_arr_labtest[$val[csf('job_no')]];
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center" style="font-size:15px"><? echo $i; ?></td>
                <td align="center" style="font-size:15px"><? echo $val[csf("job_no")]; ?></td>
                <td align="right" style="font-size:15px"><? echo number_format($total_budget,2); ?></td>
                <td align="right" style="font-size:15px"><? echo number_format($total_wo_value,2); ?></td>
                <td align="right" style="font-size:15px"><?
				$wo_balance=$total_budget-$total_wo_value;  echo number_format($wo_balance,2);
				 ?></td>
                <td align="center" style="font-size:15px">
				<?
					if($wo_balance<0)  $commants="Over"  ;
					else if($wo_balance==0)  $commants="At Per";
					else if($wo_balance>0)  $commants="Less";
					echo $commants;
				?>
                </td>
			</tr>
		<? 
        $i++;
        }
        ?>
        </tbody>
     
      </table>
		 <?
            echo signature_table(80, $data[0], "900px");
         ?>
   </div> 
   	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
     <?
	 exit(); 
}
if($action=="show_trim_booking_report2")
{
    extract($_REQUEST);

	echo load_html_head_contents("Lab Test Work Order", "../../", 1, 1,'','','');
	$data=explode('*',str_replace("'","",$data));
	
	$sql="select a.id, a.labtest_no, a.supplier_id, a.wo_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode, a.address, a.attention,a.ready_to_approved,a.vat_percent from wo_labtest_mst a where a.id='$data[1]' and a.company_id='$data[0]'"; // new
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	
	$lab_test_rate_library=return_library_array( "select id, test_item from lib_lab_test_rate_chart", "id", "test_item"  );
	
	$supplier_library=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id 
	and b.party_type=26 order by a.supplier_name","id","supplier_name"  );
	$total_charge=0;
	$sql_dtls= "select id,mst_id,po_id,job_no,entry_form,test_for,test_item_id,test_item_value,color,amount,discount,labtest_charge,wo_value,vat_amount,remarks,test_item_id,qty_breakdown
	from wo_labtest_dtls
	where mst_id=$data[1]"; 
	$sql_result= sql_select($sql_dtls);
	$amount_arr=array();
	$job_nos='';
	$poArr=array();
	foreach($sql_result as $inf)
	{
		$amount_arr[$inf[csf('job_no')]]['wo_value']=$inf[csf('wo_value')];	
		$total_charge+=$inf[csf('labtest_charge')];
		if($job_nos=='') $job_nos=$inf[csf('job_no')];else $job_nos.=",".$inf[csf('job_no')];
		if($inf[csf('po_id')]){
			$poArr[$inf[csf('po_id')]]=$inf[csf('po_id')];
		}
	}
	$jobid=array_unique(explode(",",$job_nos));
	$jobs='';
	foreach($jobid as $jid)
	{
		if($jobs=='') $jobs="'$jid'";else $jobs.=","."'$jid'";
	}
	$poCond="";
	if(count($poArr)>0){
		$poCond="and b.id in (".implode(",",$poArr).")";
	}
	//echo $jobs;
	//echo $job_nos;
	$po_numberArr=array();
	$po_shipdateArr=array();
	$pos_sql="select b.id, b.po_number,b.shipment_date,a.buyer_name,a.style_ref_no as style_ref, a.job_no from wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst and a.job_no in($jobs) $poCond ";
	$sql_results=sql_select($pos_sql);
	foreach($sql_results as $row)
	{
		$buyer_library[$row[csf('job_no')]]=$row[csf('buyer_name')];
		$style_ref_no_library[$row[csf('job_no')]]=$row[csf('style_ref')];
		$po_no_arr[$row[csf('job_no')]].=$row[csf('po_number')].',';
		$po_numberArr[$row[csf('id')]]=$row[csf('po_number')];
		$po_shipdateArr[$row[csf('id')]]=$row[csf('shipment_date')];
	}

	$buyer_name='';
	foreach($jobid as $job)
	{
			if($buyer_name=='') $buyer_name=$buyer_name_arr[$buyer_library[$job]];else $buyer_name.=",".$buyer_name_arr[$buyer_library[$job]];
	}


$varcode_booking_no=$dataArray[0][csf('labtest_no')];


?>
<div style="width:900px;" align="center">
	
	<table width="900" style="table-layout: fixed;">
		<tr>
			<td width="150" align="center" style="font-size: 14px"><strong><? 
				$company_logo=sql_select( "select b.image_location from common_photo_library b where b.master_tble_id='$data[0]' and b.form_name='company_details'");
				?>
	            <img src="../../<?  echo $company_logo[0][csf('image_location')]; ?>"  width="100px"  height="70" ></strong>
        	</td>
			<td align="center" style="font-size: 14px; text-align:center;" valign="top">
            	<strong style="font-size: 30px"><? echo $company_library[$data[0]]; ?></strong><br>
            	<strong><?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						 <? echo $result[csf('plot_no')]; ?> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('road_no')]; ?> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('block_no')];?> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('city')];?> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('zip_code')]; ?> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('province')];?> 
						 <? if($result[csf('level_no')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('email')];?> 
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('website')];
						
						 
					}
                ?> </strong>
            </td>
			<td width="300" id="barcode_img_id" align="right" style="font-size: 14px"></td>
		</tr>
		<tr>
			<td></td><td align="center"><u style="font-size: 18px;font-weight: bold;">Lab Test Work Without Order</u></td><td></td>
		</tr>
	</table>
<div style="margin-top: 20px"></div>
	<table align="center">
		<tr>
			<td width="80"><strong>To</strong></td>
			<td width="150"><b> : </b><? echo $supplier_library[$dataArray[0][csf('supplier_id')]];?></td>
			<td width="110"><strong>Buyer</strong></td>
			<td width="150"><b> : </b><? echo implode(",",array_unique(explode(",",$buyer_name))); ?></td>
            <td><strong>Rate For.</strong></td>
			<td><b> : </b><?
			 if($total_charge>0) {$rate_for='Express';}  else  { $rate_for='Regular';}
			 echo $rate_for; ?></td>
			<?php /*?><td width="90"><strong>Wo No.</strong></td>
			<td width="150"><b> : </b><? echo $dataArray[0][csf('labtest_no')];?></td><?php */?>

		</tr>
		<tr>
			<td><strong>Address</strong></td>
			<td><b> : </b><? echo $dataArray[0][csf('address')]; ?></td>
			
			<td><strong>Pay Mode</strong></td>
			<td><b> : </b><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
            <td><strong> Vat %</strong></td>
			<td><b> : </b><? echo $dataArray[0][csf('vat_percent')]; ?></td>
			<?php /*?><td><strong>Wo Date.</strong></td>
			<td><b> : </b><? echo change_date_format($dataArray[0][csf('wo_date')]); ?></td><?php */?>

		</tr>
		<tr>
			<td><strong>Attention</strong></td>
			<td><b> : </b><? echo $dataArray[0][csf('attention')];?></td>
			<td><strong>Delivery Date</strong></td>
			<td><b> : </b><? echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
			<?php /*?><td><strong>Rate For.</strong></td>
			<td><b> : </b><?
			 if($total_charge>0) {$rate_for='Express';}  else  { $rate_for='Regular';}
			 echo $rate_for; ?></td><?php */?>
		</tr>
		<tr>
			<td><strong>Currency</strong></td>
			<td ><b> : </b><? echo $currency[$dataArray[0][csf('currency')]]; ?></td>
			<td><strong>Exchange Rate</strong></td>
			<td ><b> : </b><? echo $dataArray[0][csf('ecchange_rate')]; ?></td>
			<?php /*?><td><strong> Vat %</strong></td>
			<td><b> : </b><? echo $dataArray[0][csf('vat_percent')]; ?></td><?php */?>
		</tr>
		
	</table>

 
        <br/> <br/> <br/>
	
         <table  cellspacing="0" width="1000"  border="1" rules="all" class=""  align="left" style="table-layout: fixed;">
         <thead bgcolor="#dddddd" >
             <tr>
             		<th width="30">SL</th>
                	<!--<th width="100">Style/Job No</th> 
                    <th width="100">Po No</th>  
                    <th width="80">Ship Date</th>  -->
                    <th width="70">Test For</th>   
                    <th width="380">Remarks</th> 
                    <th width="80">Color</th>   
                    <th width="200">Test Item</th>   
                    <th width="50">Qty</th>
                    <th width="50">Rate</th>
                    <th width="80">Amount</th>
                </tr>
        </thead>
        <tbody> 
   
<?
	
	
	$i=1; $j=1;
	$all_job_no='';
	$sl_arr=array();
	$cbo_currency=$data[2];
	$txt_workorder_date=$data[3];
	if($db_type==2) { $txt_workorder_date=change_date_format($txt_workorder_date,'yyyy-mm-dd',"-",1);}
	else { $txt_workorder_date=change_date_format($txt_workorder_date,'yyyy-mm-dd');}
	$current_currency=set_conversion_rate($cbo_currency,$txt_workorder_date);
	$grand_wo_value=0;
	foreach($sql_result as $row)
	{
		
		if ($j%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$test_item=explode(",",$row[csf('test_item_id')]);
			$test_item_value=explode(",",$row[csf('test_item_value')]);
			$test_item_qty_break=explode(",",$row[csf('qty_breakdown')]);
			
			$itemDataArr=array();
			foreach($test_item_qty_break as $item_data_string){

				list($item_id,$qty,$amount)=explode('_',$item_data_string);
				$itemDataArr[$item_id]=array(
					'qty'=>$qty,
					'amu'=>$amount
				);
			}
			
			//echo $row[csf('test_item_value')];
			$colum_span=count(explode(",",$row[csf('test_item_id')]));
			$colum_span=$colum_span+4;
			if(trim($all_job_no)!='') $all_job_no.=",'".$row[csf("job_no")]."'";
			else $all_job_no="'".$row[csf("job_no")]."'";
			$total_net_reate=0;
			
			//print_r($test_item_value);
			$index=0;
			foreach($test_item as $name)
			{
				
				if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$converted_currency=set_conversion_rate($name[csf('currency_id')],$txt_workorder_date);
				$actual_currency=$converted_currency/$current_currency;
				//$actual_net_rate=$actual_currency*$name[csf('net_rate')];
				
				if($row[csf("po_id")]==""){
					$po_no=rtrim($po_no_arr[$row[csf("job_no")]],',');
				}else{
					$po_no=$po_numberArr[$row[csf('po_id')]];
		            $shipment_date=$po_shipdateArr[$row[csf('po_id')]];
				}
				//$po_ids=explode(",",$po_no);
				//if(count($po_ids)>3)
				if(!in_array($i,$main_row))
				{
				$main_row[]=$i; //style="overflow:hidden;text-overflow: ellipsis;white-space: nowrap;"
		?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td align="center" style="font-size:15px" rowspan="<? echo $colum_span?>"><? echo $i; ?></td>
                    <?php /*?><td align="center" style="font-size:15px" rowspan="<? echo $colum_span?>"><? echo $style_ref_no_library[$row[csf("job_no")]].'<br/>'.$row[csf("job_no")]; ?></td>
                      <td  align="center" style="word-break:break-all;font-size:15px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" rowspan="<? echo $colum_span?>"><p><? echo $po_no; ?></p></td>
                      <td  align="center" style="word-break:break-all;font-size:15px;overflow:hidden;text-overflow: ellipsis;white-space: nowrap;" rowspan="<? echo $colum_span?>"><p><? echo date("d-m-Y",strtotime($shipment_date)); ?></p></td><?php */?>
                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $test_for[$row[csf("test_for")]]; ?></td>
                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $row[csf("remarks")]; ?></td>
                    <td align="center" style="word-break:break-all;font-size:15px" rowspan="<? echo $colum_span?>"><? echo $color_library[$row[csf("color")]]; ?></td>
                    <td align="left" style="word-break:break-all;font-size:15px"> <?  echo $lab_test_rate_library[$name];  ?> </td>
                   
                   	<td align="right"><? echo $itemDataArr[$name]['qty'];?></td>
                    <td align="right"><? echo number_format($test_item_value[$index],2);?></td>
                    <td align="right" style="font-size:15px">
					<? 
					  	$total_net_reate+=$itemDataArr[$name]['amu'];
						echo number_format($itemDataArr[$name]['amu'],4);
					?>
                    </td>
                </tr>
		   <? 
				}
				else
				{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
                    
                    <td align="left" style="font-size:15px"><? echo $lab_test_rate_library[$name]; ?></td>
                    
                   	<td align="right"><? echo $itemDataArr[$name]['qty'];?></td>
                    <td align="right"><? echo number_format($test_item_value[$index],2);?></td>
                    <td align="right" style="font-size:15px">
					<? 
					  	$total_net_reate+=$itemDataArr[$name]['amu'];
						echo number_format($itemDataArr[$name]['amu'],4);
					?>
                    </td>
                </tr>
                
				<?
				}
				$index++;
			}
			?>
             <tr bgcolor="#E3E3E3">
                <td align="right" style="font-size:15px" colspan="3"><b>Gross Amount</b></td>
                <td align="right" style="font-size:15px"><b><? echo number_format($total_net_reate,4); ?></b></td>
			</tr>
            <tr bgcolor="<? //echo $bgcolor; ?>">
                <td align="right" style="font-size:15px" colspan="3" >Add Quick Delv Charge (USD)</td>
                <td align="right" style="font-size:15px"><? echo number_format($row[csf("labtest_charge")],4) ; ?></td>
			</tr>
              <tr bgcolor="<? //echo $bgcolor; ?>">
                <td align="right" style="font-size:15px" colspan="3" >Less Discount</td>
                <td align="right" style="font-size:15px"><? echo number_format($row[csf("discount")],4) ; ?></td>
			</tr>
            </tr>
              <tr bgcolor="#E3E3E3">
               <td align="right" style="font-size:15px" colspan="3" ><b>Total Value</b></td>
                <td align="right" style="font-size:15px"><b>
				<?
				 $toatal_wo_value=$row[csf("labtest_charge")]+$total_net_reate-$row[csf("discount")];
				 echo number_format(($toatal_wo_value),4) ;
				  ?></b></td>
			</tr>
            <!-- new develop -->
            <tr bgcolor="#E3E3E3">
               <td colspan="7" align="right" style="font-size:15px" ><b>Vat Amount</b></td>
                <td align="right" style="font-size:15px"><b>
					<?
						$toatal_vat_percent= $row[csf("vat_amount")];
						echo number_format(($toatal_vat_percent),4) ;
                    ?>
                 </b>
                </td>
			</tr>
            <tr bgcolor="#E3E3E3">
               <td colspan="7" align="right" style="font-size:15px"><b>Wo Value</b></td>
                <td align="right" style="font-size:15px"><b>
				<?
				 $wo_totall= $toatal_vat_percent+$toatal_wo_value;
				 $grand_wo_value+=$wo_totall;
				 echo number_format(($wo_totall),4) ;
				  ?></b></td>
			</tr>
            <!--  -->
            
            <?
		
        $i++;
        }

         
	   $mcurrency="";
	   $dcurrency="";
	   if($cbo_currency==1)
	   {
		$mcurrency='Taka';
		$dcurrency='Paisa'; 
	   }
	   if($cbo_currency==2)
	   {
		$mcurrency='USD';
		$dcurrency='CENTS'; 
	   }
	   if($cbo_currency==3)
	   {
		$mcurrency='EURO';
		$dcurrency='CENTS'; 
	   }
	   
       
        ?>
        	<tr bgcolor="#B7B7B7">
                <td align="right" style="font-size:15px" colspan="7"><b>Grand Total</b></td>
                <td align="right" style="font-size:15px"><b><? echo number_format(($grand_wo_value),4) ; ?></b></td>

			</tr>
			<tr>
				
				<td colspan="3"><strong>In Words:</strong></td>
				<td align="left" colspan="7" style="font-size:12px;"><b><? echo number_to_words(def_number_format($grand_wo_value,2,""),$mcurrency, $dcurrency);?></b></td>
			</tr>
			
			</tbody>
     
      </table>   
	
    <table  cellspacing="0" width="800"  border="1" rules="all" class=""  align="left" style=" display:none">
        <thead bgcolor="#dddddd" >
        	<tr>
                 <th colspan="9" align="left">Comments</th>   
           </tr>
           <tr>
                <th width="60">SL</th>
                <th width="120">Job No</th>   
                <th width="130">Pre-Cost Value</th> 
                <th width="140">WO Value</th>
                <th width="140">Balance</th>    
                <th >Comments</th>   
                       
             </tr>
        </thead>
        <tbody> 
   
<?
	$all_job_no=implode(",",array_unique(explode(",",$all_job_no)));
	
	$sql_pre_cost="select a.costing_per,b.lab_test,a.job_no from wo_pre_cost_mst a, wo_pre_cost_dtls b where a.job_no=b.job_no and a.job_no in ($all_job_no)";
	$result_precost= sql_select($sql_pre_cost);
	$job_arr_labtest=array();
	foreach($result_precost as $inf)
	{
		$costing_per=$inf[csf('costing_per')];
		if($costing_per==1)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/12;
		}
		else if($costing_per==2)
		{
			$costing_per_pcs=$inf[csf('lab_test')];
		}
		else if($costing_per==3)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/24;
		}
		else if($costing_per==4)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/36;
		}
		else if($costing_per==5)
		{
			$costing_per_pcs=$inf[csf('lab_test')]/48;
		}
		$job_arr_labtest[$inf[csf('job_no')]]=$costing_per_pcs;
	}
	
	$poIdCond="";
	$poIdCond1="";
	if(count($poArr)>0){
		$poIdCond="and id in (".implode(",",$poArr).")";
		$poIdCond1="and order_id in (".implode(",",$poArr).")";
	}
	$sql_order_qty="select sum(po_quantity) as po_quantity,job_no_mst from  wo_po_break_down  where job_no_mst in ($all_job_no) and status_active=1  and is_deleted=0 $poIdCond group by job_no_mst ";
	
	$result_order= sql_select($sql_order_qty);
	$job_order_arr=array();
	foreach($result_order as $value)
	{
		$job_order_arr[$value[csf('job_no_mst')]]=$value[csf('po_quantity')];
	}
	$sql_order= "select job_no,sum(wo_value) as total_wo_value from wo_labtest_order_dtls where job_no in ($all_job_no) and status_active=1 
	and is_deleted=0 $poIdCond1  group by  job_no  "; 
	//echo $sql_order;
	$result= sql_select($sql_order);
	$i=1;
	$commants='';
	foreach($result as $val)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			$total_wo_value=$amount_arr[$val[csf('job_no')]]['wo_value'];
			$total_budget=$job_order_arr[$val[csf('job_no')]]*$job_arr_labtest[$val[csf('job_no')]];
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center" style="font-size:15px"><? echo $i; ?></td>
                <td align="center" style="font-size:15px"><? echo $val[csf("job_no")]; ?></td>
                <td align="right" style="font-size:15px"><? echo number_format($total_budget,2); ?></td>
                <td align="right" style="font-size:15px"><? echo number_format($total_wo_value,2); ?></td>
                <td align="right" style="font-size:15px"><?
				$wo_balance=$total_budget-$total_wo_value;  echo number_format($wo_balance,2);
				 ?></td>
                <td align="center" style="font-size:15px">
				<?
					if($wo_balance<0)  $commants="Over"  ;
					else if($wo_balance==0)  $commants="At Per";
					else if($wo_balance>0)  $commants="Less";
					echo $commants;
				?>
                </td>
			</tr>
		<? 
        $i++;
        }
        ?>
        </tbody>
     
      </table>
		 <?
            echo signature_table(80, $data[0], "900px");
         ?>
   </div> 
   	<script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    fnc_generate_Barcode('<? echo $varcode_booking_no; ?>','barcode_img_id');
    </script>
     <?
	 exit(); 
}
?>