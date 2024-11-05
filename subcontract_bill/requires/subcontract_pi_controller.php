<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id = $_SESSION['logic_erp']["user_id"];

if($action=="load_drop_down_buyer")
{
	$data = explode("_",$data);
	$company_id=$data[0];
	
	if($data[1]==0)
	{
		echo create_drop_down( "cbo_buyer_name", 151, $blank_array,"",1, "-- Select Buyer --", 0, "" );
	}
	else if($data[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 151, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Buyer --", "0", "",0 );
	}
	else if($data[1]==2)
	{
		echo create_drop_down( "cbo_buyer_name", 151, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );  
	}
	
	exit();
}

if ($action=="save_update_delete")
{ 

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	 
	if ($operation==0)  // Insert Here
	{ 
		if (is_duplicate_field( "pi_number", "com_export_pi_mst", "pi_number=$pi_number and exporter_id=$cbo_exporter_id and within_group=$cbo_within_group and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			 
			$id=return_next_id( "id", "com_export_pi_mst", 1 ); 
			$new_rcv_number		=explode("*",return_mrr_number( str_replace("'","",$cbo_exporter_id), '', '', '', 5, "select rcv_number_prefix,rcv_number_prefix_num from com_export_pi_mst where exporter_id=$cbo_exporter_id  order by id desc ", "rcv_number_prefix", "rcv_number_prefix_num" ));
			
			if(str_replace("'",'',$pi_number)=="")
			{
				$pi_number=$new_rcv_number[0];
			}
			else
			{
				$pi_number=str_replace("'",'',$pi_number);
			}
			
			$field_array="id,entry_form,rcv_number_prefix,rcv_number_prefix_num, rcv_number,item_category_id,exporter_id,within_group,buyer_id,pi_number,pi_date,last_shipment_date,pi_validity_date,currency_id,hs_code,internal_file_no,remarks,advising_bank,inserted_by,insert_date";
			
			$data_array="(".$id.",174,'".$new_rcv_number[1]."','".$new_rcv_number[2]."','".$new_rcv_number[0]."',".$cbo_item_category_id.",".$cbo_exporter_id.",".$cbo_within_group.",".$cbo_buyer_name.",'".$pi_number."',".$pi_date.",".$last_shipment_date.",".$pi_validity_date.",".$cbo_currency_id.",".$hs_code.",".$txt_internal_file_no.",".$txt_remarks.",".$cbo_advising_bank.",".$user_id.",'".$pc_date_time."')";
			
			//echo "5**insert into com_export_pi_mst (".$field_array.") values ".$data_array;die;	 
			$rID=sql_insert("com_export_pi_mst",$field_array,$data_array,1);
			//mysql_query("ROLLBACK"); 
			//echo "5**".$rID;die;
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");  
					echo "0**".$id."**".$new_rcv_number[0]."**".$pi_number;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "5**0";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID )
				{
					oci_commit($con);  
					echo "0**".$id."**".$new_rcv_number[0]."**".$pi_number;
				}
				else
				{
					oci_rollback($con); 
					echo "5**0";
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==1)   // Update Here
	{
		if (is_duplicate_field( "pi_number", "com_export_pi_mst", "pi_number=$pi_number and exporter_id=$cbo_exporter_id and within_group=$cbo_within_group and status_active=1 and is_deleted=0 and id!=$update_id" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			
			if(str_replace("'",'',$pi_number)=="")
			{
				$pi_number=(str_replace("'",'',$sys_pi_id));
			}
			else
			{
				$pi_number=str_replace("'",'',$pi_number);
			}
			$field_array="item_category_id*exporter_id*within_group*buyer_id*pi_number*pi_date*last_shipment_date*pi_validity_date*currency_id*hs_code*internal_file_no*remarks*advising_bank*updated_by*update_date";
			
			$data_array="".$cbo_item_category_id."*".$cbo_exporter_id."*".$cbo_within_group."*".$cbo_buyer_name."*".$pi_number."*".$pi_date."*".$last_shipment_date."*".$pi_validity_date."*".$cbo_currency_id."*".$hs_code."*".$txt_internal_file_no."*".$txt_remarks."*".$cbo_advising_bank."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rID=sql_update("com_export_pi_mst",$field_array,$data_array,"id",$update_id,1); 
			if($db_type==0)
			{
				if($rID)
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $sys_pi_id)."**".str_replace("'", '', $pi_number);
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "6**".str_replace("'", '', $update_id);
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID)
				{
					oci_commit($con);  
					echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $sys_pi_id)."**".str_replace("'", '', $pi_number);
				}
				else
				{
					oci_rollback($con); 
					echo "6**".str_replace("'", '', $update_id);
				}
			}
			
			disconnect($con);
			die;
		}
	}
}

///Save Item Details Table

if ($action=="save_update_delete_dtls")
{ 
//echo $action;
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	 
		$id=return_next_id( "id","com_export_pi_dtls", 1 ) ;
	
		$field_array="id,pi_id,work_order_no,work_order_id,work_order_dtls_id,determination_id,construction,composition,color_id,gsm,dia_width,uom,quantity,rate,amount,net_pi_rate,net_pi_amount,inserted_by,insert_date"; 
		
		$field_array_update="total_amount*upcharge*discount*net_total_amount";
		if($cbo_currency_id==1)
		{
			$txt_total_amount=number_format($txt_total_amount,$dec_place[4],'.','');
			$txt_total_amount_net=number_format($txt_total_amount_net,$dec_place[4],'.','');
		}
		else
		{
			$txt_total_amount=number_format($txt_total_amount,$dec_place[5],'.','');
			$txt_total_amount_net=number_format($txt_total_amount_net,$dec_place[5],'.','');
		}
		$data_array_update=$txt_total_amount."*'".$txt_upcharge."'*'".$txt_discount."'*".$txt_total_amount_net;
		
		for($i=1;$i<=$total_row;$i++)
		{
			$workOrderNo="workOrderNo_".$i;
			$workOrderId="hideWoId_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$determinationId="hideDeterminationId_".$i; 
			$construction="construction_".$i; 
			$composition="composition_".$i;
			$colorId="colorId_".$i;
			$gsm="gsm_".$i;
			$diawidth="diawidth_".$i;
			$uom="uom_".$i;
			$quantity="quantity_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			
			$perc=(str_replace("'","",$$amount)/$txt_total_amount)*100;
			$net_pi_amount=($perc*$txt_total_amount_net)/100;
			$net_pi_rate=$net_pi_amount/str_replace("'","",$$quantity);
			
			if($cbo_currency_id==1)
				$net_pi_amount=number_format($net_pi_amount,$dec_place[4],'.','');
			else
				$net_pi_amount=number_format($net_pi_amount,$dec_place[5],'.','');
					
			$net_pi_rate=number_format($net_pi_rate,$dec_place[3],'.','');
			
			if($data_array!="") $data_array.=",";
			$data_array .="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$determinationId)."','".str_replace("'","",$$construction)."','".str_replace("'","",$$composition)."','".str_replace("'","",$$colorId)."','".str_replace("'","",$$gsm)."','".str_replace("'","",$$diawidth)."','".str_replace("'","",$$uom)."',".$$quantity.",".$$rate.",".$$amount.",".$net_pi_rate.",".$net_pi_amount.",".$user_id.",'".$pc_date_time."')"; 
			
			$id=$id+1;
			
		}
		//echo "5**insert into com_export_pi_dtls (".$field_array.") Values ".$data_array."";die;
		$rID=sql_insert("com_export_pi_dtls",$field_array,$data_array,0);
		$rID2=sql_update("com_export_pi_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		echo "5**".$rID."**".$rID2;die;
	
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'", '', $update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "0**".str_replace("'", '', $update_id);
			}
			else
			{
				oci_rollback($con); 
				echo "5**0";
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
		
		$field_array_update="work_order_no*work_order_id*work_order_dtls_id*determination_id*construction*composition*color_id*gsm*dia_width*uom*quantity*rate*amount*net_pi_rate*net_pi_amount*updated_by*update_date";
			
		$field_array="id, pi_id, work_order_no, work_order_id, work_order_dtls_id, determination_id, construction, composition, color_id, gsm, dia_width, uom, quantity, rate, amount, net_pi_rate, net_pi_amount, inserted_by, insert_date"; 
		
		$field_array_update2="total_amount*upcharge*discount*net_total_amount";
		if($cbo_currency_id==1)
		{
			$txt_total_amount=number_format($txt_total_amount,$dec_place[4],'.','');
			$txt_total_amount_net=number_format($txt_total_amount_net,$dec_place[4],'.','');
		}
		else
		{
			$txt_total_amount=number_format($txt_total_amount,$dec_place[5],'.','');
			$txt_total_amount_net=number_format($txt_total_amount_net,$dec_place[5],'.','');
		}
		$data_array_update2=$txt_total_amount."*'".$txt_upcharge."'*'".$txt_discount."'*".$txt_total_amount_net;
		
		$id = return_next_id( "id","com_export_pi_dtls", 1 );
		$data_array==""; $data_array_update=array();
		
		for($i=1;$i<=$total_row;$i++)
		{
			$updateIdDtls="updateIdDtls_".$i;
			$workOrderNo="workOrderNo_".$i;
			$workOrderId="hideWoId_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$determinationId="hideDeterminationId_".$i; 
			$construction="construction_".$i; 
			$composition="composition_".$i;
			$colorId="colorId_".$i;
			$gsm="gsm_".$i;
			$diawidth="diawidth_".$i;
			$uom="uom_".$i;
			$quantity="quantity_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
						
			$perc=(str_replace("'","",$$amount)/$txt_total_amount)*100;
			$net_pi_amount=($perc*$txt_total_amount_net)/100;
			$net_pi_rate=$net_pi_amount/str_replace("'","",$$quantity);
			
			if($cbo_currency_id==1)
				$net_pi_amount=number_format($net_pi_amount,$dec_place[4],'.','');
			else
				$net_pi_amount=number_format($net_pi_amount,$dec_place[5],'.','');
					
			$net_pi_rate=number_format($net_pi_rate,$dec_place[3],'.','');

			if(str_replace("'","",$$updateIdDtls)!="")
			{
				$id_arr[]=str_replace("'",'',$$updateIdDtls);
				$data_array_update[str_replace("'",'',$$updateIdDtls)] = explode("*",("'".str_replace("'","",$$workOrderNo)."'*'".str_replace("'","",$$workOrderId)."'*'".str_replace("'","",$$workOrderDtlsId)."'*'".str_replace("'","",$$determinationId)."'*'".str_replace("'","",$$construction)."'*'".str_replace("'","",$$composition)."'*'".str_replace("'","",$$colorId)."'*'".str_replace("'","",$$gsm)."'*'".str_replace("'","",$$diawidth)."'*'".str_replace("'","",$$uom)."'*".$$quantity."*".$$rate."*".$$amount."*".$net_pi_rate."*".$net_pi_amount."*".$user_id."*'".$pc_date_time."'"));
			}
			else
			{
				if($data_array!="") $data_array.=",";
				$data_array .="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$determinationId)."','".str_replace("'","",$$construction)."','".str_replace("'","",$$composition)."','".str_replace("'","",$$colorId)."','".str_replace("'","",$$gsm)."','".str_replace("'","",$$diawidth)."','".str_replace("'","",$$uom)."',".$$quantity.",".$$rate.",".$$amount.",".$net_pi_rate.",".$net_pi_amount.",".$user_id.",'".$pc_date_time."')"; 
				$id=$id+1;
			}
		}

		$rID=true; $rID2=true;
		if(count($data_array_update)>0)
		{
			$rID=execute_query(bulk_update_sql_statement( "com_export_pi_dtls", "id", $field_array_update, $data_array_update, $id_arr ));
		}
		
		if($data_array!="")
		{
			$rID2=sql_insert("com_export_pi_dtls",$field_array,$data_array,0);
		}

		$rID3=sql_update("com_export_pi_mst",$field_array_update2,$data_array_update2,"id",$update_id,1);
		//echo "5**".$rID."**".$rID2."**".$rID3;die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id);
			}
			else
			{
				oci_rollback($con);
				echo "6**0";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="pi_popup")
{
	echo load_html_head_contents("PI Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		
		function js_set_value( pi_id )
		{
			document.getElementById('txt_selected_pi_id').value=pi_id;
			parent.emailwindow.hide();
		}
		
    </script>

</head>

<body>
<div align="center" style="width:900px;">
	<form name="searchpifrm"  id="searchpifrm">
		<fieldset style="width:100%;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="800" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>PI Number</th>
                    <th>Date Range</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="txt_selected_pi_id" id="txt_selected_pi_id" class="text_boxes" style="width:70px" value="">   
                    </th> 
                </thead>
                <tr class="general">
                    <td>
						 <? echo create_drop_down( "cbo_exporter_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',$exporter_id,"",1); ?>       
                    </td>
                    <td> 
                        <input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:120px">
                    </td>						
                    <td align="center">
                      <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_exporter_id').value, 'create_pi_search_list_view', 'search_div', 'subcontract_pi_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px" id="search_div" align="left"></div> 
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_pi_search_list_view")
{
	$data=explode('_',$data);
	 
	if ($data[0]!="") $pi_number=" and pi_number like '%".trim($data[0])."%'"; else { $pi_number = ''; }
	if ($data[1]!="" &&  $data[2]!="")
	{
		if($db_type==0)
		{
			$pi_date = "and pi_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$pi_date = "and pi_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'";
		}
	}
	else
	{
		$pi_date ="";
	}

	$exporter_id =$data[3];
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$export_item_category,3=>$comp,4=>$yes_no);
	 
	
	$sql= "select id,pi_number,pi_date,item_category_id,exporter_id,within_group,last_shipment_date,hs_code from com_export_pi_mst where exporter_id = $exporter_id $pi_number $pi_date and entry_form=174 and status_active=1 and is_deleted=0 order by pi_number";  
	
	echo create_list_view("list_view", "PI No,PI Date,Item Category,Exporter,Within Group,Last Shipment Date,HS Code", "150,80,80,150,80,120","880","270",0, $sql , "js_set_value", "id", "", 1, "0,0,item_category_id,exporter_id,within_group,0,0", $arr , "pi_number,pi_date,item_category_id,exporter_id,within_group,last_shipment_date,hs_code", "",'','0,3,0,0,0,3,0');
	 
exit();	
} 

if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("select id,item_category_id, exporter_id,within_group,buyer_id, pi_number,pi_date,last_shipment_date, pi_validity_date,currency_id, hs_code, internal_file_no, remarks, total_amount, upcharge, discount, net_total_amount from com_export_pi_mst where id='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_item_category_id').value = '".$row[csf("item_category_id")]."';\n";  
		echo "document.getElementById('cbo_exporter_id').value = '".$row[csf("exporter_id")]."';\n";  
		echo "document.getElementById('cbo_within_group').value = '".$row[csf("within_group")]."';\n";  
		
		echo "load_drop_down('requires/subcontract_pi_controller',".$row[csf("exporter_id")]."+'_'+".$row[csf("within_group")].", 'load_drop_down_buyer', 'buyer_td' );\n";
		
		if($row[csf("last_shipment_date")]=="0000-00-00" || $row[csf("last_shipment_date")]=="") $last_shipment_date=""; else $last_shipment_date=change_date_format($row[csf("last_shipment_date")]);
		if($row[csf("pi_validity_date")]=="0000-00-00" || $row[csf("pi_validity_date")]=="") $pi_validity_date=""; else $pi_validity_date=change_date_format($row[csf("pi_validity_date")]);
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('pi_number').value = '".$row[csf("pi_number")]."';\n";  
		echo "document.getElementById('pi_date').value = '".change_date_format($row[csf("pi_date")])."';\n";  
		echo "document.getElementById('last_shipment_date').value = '".$last_shipment_date."';\n";  
		echo "document.getElementById('pi_validity_date').value = '".$pi_validity_date."';\n";  
		echo "document.getElementById('cbo_currency_id').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('hs_code').value = '".$row[csf("hs_code")]."';\n";  
		
		echo "document.getElementById('txt_total_amount').value = '".$row[csf("total_amount")]."';\n";  
		echo "document.getElementById('txt_upcharge').value = '".($row[csf("upcharge")])."';\n";
		echo "document.getElementById('txt_discount').value = '".$row[csf("discount")]."';\n";
		echo "document.getElementById('txt_total_amount_net').value = '".$row[csf("net_total_amount")]."';\n";
		
		echo "document.getElementById('txt_internal_file_no').value = '".$row[csf("internal_file_no")]."';\n";  
		echo "document.getElementById('txt_remarks').value = '".($row[csf("remarks")])."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_system_id').value = '".$row[csf("id")]."';\n";
		
		echo "$('#cbo_exporter_id').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_pi_mst',1);\n";
	}
	exit();
}

//---------------------------------------------- Start Pi Details -----------------------------------------------------------------------//

if( $action == 'pi_details' ) 
{	
	extract($_REQUEST);
	
	//echo $data; die; 
	$data=explode('_',$data);
	$job_pi_id=$data[0];
	$order_id=$data[1];
	$extra_parameter=$data[2];
	$is_save=$data[3];
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	$tblRow=0;
	
	//echo "select a.id, a.mst_id, a.order_id, a.item_id, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.excess_cut, a.plan_cut, a.process_loss,a.gsm,a.grey_dia,a.finish_dia,a.dia_width_type,a.embellishment_type,a.description, b.order_no , b.order_quantity , b.main_process_id, b.process_id  from subcon_ord_breakdown a ,subcon_ord_dtls b where  a.order_id=b.id and a.order_id='$order_id' and a.mst_id='$job_id'"; die; 
	if($is_save==1){
		$order_result=sql_select( "select a.id, a.mst_id, a.order_id, a.item_id, a.color_id, a.size_id, a.qnty, a.rate, a.amount, a.excess_cut, a.plan_cut, a.process_loss,a.gsm,a.grey_dia,a.finish_dia,a.dia_width_type,a.embellishment_type,a.description, b.order_no , b.order_quantity , b.main_process_id, b.process_id ,b.cust_buyer, b.cust_style_ref from subcon_ord_breakdown a ,subcon_ord_dtls b where  a.order_id=b.id and a.order_id='$order_id' and a.mst_id='$job_pi_id'" );
		foreach($order_result as $row)
		{
			$tblRow++;		
			if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
	        <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
	            <td>
	                <input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
	            </td>
	            <td><input name="txtOrderNo_<? echo $tblRow; ?>" id="txtOrderNo_<? echo $tblRow; ?>" type="text" class="text_boxes" style="width:75px" placeholder="Double Click" onDblClick="openmypage_job()" value="<? echo $row[csf('order_no')]; ?>" readonly />
	            <input name="txtOrderId_<? echo $tblRow; ?>" id="txtOrderId_<? echo $tblRow; ?>" type="hidden" class="text_boxes" style="width:75px"  value="<? echo $row[csf('order_id')]; ?>"  readonly/>
	            <input name="txtOrderRowId_<? echo $tblRow; ?>" id="txtOrderRowId_<? echo $tblRow; ?>" type="hidden" class="text_boxes" style="width:75px"  value="<? echo $row[csf('id')]; ?>" readonly />
	            </td>
	            <td><? echo create_drop_down( "cboProcessName_".$tblRow, 80, $production_process,"", 1, "--Select Process--",$row[csf('main_process_id')],"", 1,"","" ); ?></td>
	            
	            <td class="emb_type_show">
	            <?
	                $type_array=array(0=>$blank_array,8=>$emblishment_print_type,9=>$emblishment_embroy_type,7=>$emblishment_wash_type,12=>$emblishment_gmts_type);

	                if($type_array[$row[csf('main_process_id')]]=="")
	                {
	                    $dropdown_type_array=$blank_array;
	                }
	                else
	                {
	                    $dropdown_type_array=$type_array[$row[csf('main_process_id')]];
	                }
	                echo create_drop_down( "cboembtype_".$tblRow, 170, $dropdown_type_array,"", 1, "-- Select --", $row[csf('embellishment_type')], "",1,"" );
	            ?>
	            </td>

	            <td class="descriptions">
	                <input type="text" id="txtdescription_<? echo $tblRow; ?>" name="txtdescription_<? echo $tblRow; ?>" class="text_boxes descriptions" style="width:140px" value="<? echo $row[csf('description')]; ?>" readonly />
	            </td>

	            <td>                
	                <input type="text" id="txtitem_<? echo $tblRow; ?>" name="txtitem_<? echo $tblRow; ?>" class="text_boxes itemdescription" style="width:140px"  value="<? echo $garments_item[$row[csf('item_id')]] ; ?>" readonly /> 
	            </td>                                    
	            <td>
	                <input type="text" name="colorName_<? echo $tblRow; ?>" id="colorName_<? echo $tblRow; ?>" class="text_boxes" style="width:80px" value="<? echo $color_library[$row[csf('color_id')]]; ?>" readonly />
	                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>" readonly />
	            </td>                                    
	            <td>
	            <?
				if($row[csf("main_process_id")]==7){ $new_subprocess_array= $emblishment_wash_type;}
				if($row[csf("main_process_id")]==12){ $new_subprocess_array= $emblishment_gmts_type;}
				if($row[csf("main_process_id")]==8){ $new_subprocess_array= $emblishment_print_type;}
				if($row[csf("main_process_id")]==9){ $new_subprocess_array= $emblishment_embroy_type;}
				if($row[csf("main_process_id")]!=7 && $row[csf("main_process_id")] !=12 && $row[csf("main_process_id")] !=8 && $row[csf("main_process_id")] !=9)
				{
					$process_name='';
					$process_id_array=explode(",",$row[csf("process_id")]);
					foreach($process_id_array as $val)
					{
						if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
					}
				}
				else
				{
					$process_name='';
					$process_id_array=explode(",",$row[csf("process_id")]);
					foreach($process_id_array as $val)
					{
						if($process_name=="") $process_name=$new_subprocess_array[$val]; else $process_name.=",".$new_subprocess_array[$val];
					}	
				}
				?>
	            <input type="text" name="txSubProcessName_<? echo $tblRow; ?>" id="txSubProcessName_<? echo $tblRow; ?>" class="text_boxes" style="width:140px;"  value="<? echo $process_name; ?>"  readonly/>
	    		<input type="hidden" name="txtSubProcessId_<? echo $tblRow; ?>" id="txtSubProcessId_<? echo $tblRow; ?>" value="<? echo $row[csf("process_id")]; ?>"/></td>
	            <td>
	                <input type="text" name="txtCustBuyStle_<? echo $tblRow; ?>" id="txtCustBuyStle_<? echo $tblRow; ?>" class="text_boxes" style="width:140px;" readonly value="<? echo $row[csf("cust_buyer")]." , ".$row[csf("cust_style_ref")]; ?>" readonly />					 
	            </td>
	            <td>
	                <input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" value="<? echo $row[csf('qnty')]; ?>" />
	            </td>
	            <td>
	                <input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric"style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" value="<? echo $row[csf('rate')]; ?>" />
	            </td>
	            <td>
	                <input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:75px;" readonly value="<? echo $row[csf('amount')]; ?>" readonly />
	                <input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
	            </td>
	        </tr>
		<?
		}
	}
	else
	{
		$pi_sql= " select id,pi_id,work_order_no,work_order_id,work_order_dtls_id,determination_id,construction,composition,color_id,gsm,dia_width,uom,quantity,rate,amount,net_pi_rate,net_pi_amount from com_export_pi_dtls where pi_id='$job_pi_id'";
		$pi_result=sql_select($pi_sql);

		//echo count($pi_result);
		if(count($pi_result)>0){
			foreach($pi_result as $row)
			{
				$tblRow++;		
				if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>

				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_id')]; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_dtls_id')]; ?>" readonly />
					</td>
		            <td> 
						<input type="text" name="construction_<? echo $tblRow; ?>" id="construction_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $construction_arr[$row[csf('determination_id')]]; ?>" style="width:110px" disabled="disabled"/>
						<input type="hidden" name="hideDeterminationId_<? echo $tblRow; ?>" id="hideDeterminationId_<? echo $tblRow; ?>" value="<? echo $row[csf('determination_id')]; ?>" readonly />
					</td>
					<td>
						<input type="text" name="composition_<? echo $tblRow; ?>" id="composition_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $composition_arr[$row[csf('determination_id')]]; ?>" style="width:110px" disabled="disabled"/>
					</td> 
		            <td>
		                <input type="text" name="colorName_<? echo $tblRow; ?>" id="colorName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:80px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="gsm_<? echo $tblRow; ?>" id="gsm_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('gsm')]; ?>" style="width:60px" disabled="disabled"/>
		            </td>
		            <td>
		                <input type="text" name="diawidth_<? echo $tblRow; ?>" id="diawidth_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('dia_width')]; ?>" style="width:70px" disabled="disabled"/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('uom')],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
					</td>
				</tr>
			<?
			}
		}
		else
		{
			$bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_id')]; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_dtls_id')]; ?>" readonly />
					</td>
		            <td> 
						<input type="text" name="construction_<? echo $tblRow; ?>" id="construction_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $construction_arr[$row[csf('determination_id')]]; ?>" style="width:110px" disabled="disabled"/>
						<input type="hidden" name="hideDeterminationId_<? echo $tblRow; ?>" id="hideDeterminationId_<? echo $tblRow; ?>" value="<? echo $row[csf('determination_id')]; ?>" readonly />
					</td>
					<td>
						<input type="text" name="composition_<? echo $tblRow; ?>" id="composition_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $composition_arr[$row[csf('determination_id')]]; ?>" style="width:110px" disabled="disabled"/>
					</td> 
		            <td>
		                <input type="text" name="colorName_<? echo $tblRow; ?>" id="colorName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:80px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="gsm_<? echo $tblRow; ?>" id="gsm_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('gsm')]; ?>" style="width:60px" disabled="disabled"/>
		            </td>
		            <td>
		                <input type="text" name="diawidth_<? echo $tblRow; ?>" id="diawidth_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('dia_width')]; ?>" style="width:70px" disabled="disabled"/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('uom')],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
					</td>
				</tr>
			<?
		}
	}
	
	exit();
}

//---------------------------------------------End Pi Details------------------------------------------------------------------------//

if ($action=="wo_popup")
{
	echo load_html_head_contents("Sales/Booking No. Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		var selected_id = new Array;
		
	 	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		
		function js_set_value( str ) {
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_wo_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_wo_id' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_wo_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#txt_selected_wo_id').val( id );
		}
	
		function reset_hide_field()
		{
			$('#txt_selected_wo_id').val( '' );
			selected_id = new Array();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:900px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:100%; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="800" border="1" rules="all" class="rpt_table">
                <thead>
                	<th>Within Group</th>
                    <th>Buyer</th>
                    <th>Sales/Booking No.</th>
                    <th>Booking Date Range</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" onClick="reset_hide_field();" />
                    	<input type="hidden" name="txt_selected_wo_id" id="txt_selected_wo_id" class="text_boxes" value=""> 
                    </th> 
                </thead>
                <tr class="general">
                	<td>
						<?php echo create_drop_down( "cbo_within_group", 151, $yes_no,"", 1, "-- Select --", 0, "load_drop_down( 'subcontract_pi_controller',$exporter_id+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
                    </td>
                    <td id="buyer_td"> 
						<?php echo create_drop_down( "cbo_buyer_name", 151, $blank_array,"", 1, "-- Select Buyer --", 0, "",0 ); ?>
                    </td>
                    <td> 
                        <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:120px">
                    </td>						
                    <td align="center">
                      <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_wo_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+<? echo $exporter_id; ?>, 'create_wo_search_list_view', 'search_div', 'subcontract_pi_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:5px" id="search_div" align="left"></div> 
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_wo_search_list_view")
{
	$data = explode("_",$data);
	
	$within_group =$data[3];
	$buyer_id =$data[4];
	$company_id =$data[5];
	
	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and a.within_group=$within_group";
	if($buyer_id==0) $buyer_id_cond=""; else $buyer_id_cond=" and a.buyer_id=$buyer_id";
	
	if (trim($data[0])!="") $wo_number=" and a.sales_booking_no like '%".trim($data[0])."'"; else { $wo_number = ''; }
	if ($data[1]!="" &&  $data[2]!="")
	{
		if($db_type==0)
		{
			$wo_date_cond = "and a.booking_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; 
		}
		else
		{
			$wo_date_cond = "and a.booking_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; 
		}
	}
	else
	{
		$wo_date_cond ="";
	}
	
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	
	$sql = "select a.id as mst_id, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.order_uom, b.id as dtls_id, b.fabric_desc, b.gsm_weight, b.dia, b.color_id from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $within_group_cond $buyer_id_cond order by a.id";
	
	?>
	 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="110">Job No</th>
            <th width="105">Sales/Booking No</th>
			<th width="75">WO Date</th>
			<th width="70">Buyer</th>
			<th width="80">Color</th>
			<th width="200">Fabric Description</th>
			<th width="60">GSM</th>
			<th width="60">Dia/ Width</th>
			<th>UOM</th>
		</thead>
	 </table>
	 <div style="width:890px; max-height:250px; overflow-y:scroll">
		 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" id="tbl_list_search">
		 <? 
		 $i=1;
		 $nameArray=sql_select( $sql );
		 foreach ($nameArray as $row)
		 {
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			if($row[csf('within_group')]==2) $buyer=$buyer_arr[$row[csf('buyer_id')]];
			else $buyer=$company_arr[$row[csf('buyer_id')]];
		 ?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)"> 
                <td width="40" align="center"><?php echo $i; ?>
                    <input type="hidden" name="txt_wo_id" id="txt_wo_id<?php echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>	
                </td>	
				<td width="110"><p><? echo $row[csf('job_no')];?></p></td>
                <td width="105"><p><? echo $row[csf('sales_booking_no')];?></p></td>
				<td width="75" align="center"><p><? if($row[csf('booking_date')]!="" && $row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]);?></p></td>
				<td width="70"><p><? echo $buyer; ?>&nbsp;</p></td> 
				<td width="80"><p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p></td>
				<td width="200"><p><? echo $row[csf('fabric_desc')]; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
				<td width="60"><p><? echo $row[csf('dia')]; ?>&nbsp;</p></td>
				<td align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>&nbsp;</p></td>
			</tr>
		 <?
		 $i++;
		 }
		 ?>
		</table>
	</div>
	<table width="890" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%"> 
					<div style="width:50%; float:left" align="left">
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

if( $action == 'populate_data_wo_form' ) 
{
	$data=explode('**',$data);
	$wo_dtls_id=$data[0];
	$tblRow=$data[1];
	
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" );
	$composition_arr=array(); $construction_arr=array();
	$sql_deter="select a.id,a.construction,a.color_range_id,b.copmposition_id,b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			
			$construction_arr[$row[csf('id')]]=$row[csf('construction')];
		}
	}
	
	
	$sql = "select a.id, a.job_no, a.order_uom, b.id as dtls_id, b.determination_id, b.gsm_weight, b.dia, b.color_id, b.finish_qty as qty, b.avg_rate, b.amount from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and b.id in($wo_dtls_id)";
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$tblRow++;
		
		if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
            <td>
				<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
			</td>
			<td>
				<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('job_no')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
				<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>" readonly />
				<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('dtls_id')]; ?>" readonly />
			</td>
            <td> 
				<input type="text" name="construction_<? echo $tblRow; ?>" id="construction_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $construction_arr[$row[csf('determination_id')]]; ?>" style="width:110px" disabled="disabled"/>
				<input type="hidden" name="hideDeterminationId_<? echo $tblRow; ?>" id="hideDeterminationId_<? echo $tblRow; ?>" value="<? echo $row[csf('determination_id')]; ?>" readonly />
			</td>
			<td>
				<input type="text" name="composition_<? echo $tblRow; ?>" id="composition_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $composition_arr[$row[csf('determination_id')]]; ?>" style="width:110px" disabled="disabled"/>
			</td> 
            <td>
                <input type="text" name="colorName_<? echo $tblRow; ?>" id="colorName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:80px" disabled="disabled"/>
                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
            </td>
            <td>
                <input type="text" name="gsm_<? echo $tblRow; ?>" id="gsm_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('gsm_weight')]; ?>" style="width:60px" disabled="disabled"/>
            </td>
            <td>
                <input type="text" name="diawidth_<? echo $tblRow; ?>" id="diawidth_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('dia')]; ?>" style="width:70px" disabled="disabled"/>
            </td>
             <td>
                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('order_uom')],'',1,''); ?>						 
            </td>
            <td>
				<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('qty')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
			</td>
			<td>
				<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('avg_rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
			</td>
			<td>
				<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
				<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
			</td>
		</tr>
	<?
	}
	exit();
}

if($action=="print") 
{
	$data = explode('*',$data);
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $company_data[csf('country_id')].','.' ';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
?>
	<div style="width:1000px">
		<?
			$sql_mst = sql_select("select id,pi_number,pi_date,item_category_id,buyer_id,currency_id,pi_validity_date,exporter_id,within_group,last_shipment_date,hs_code from com_export_pi_mst where id= $data[1]"); 
		?>
        <table width="100%">
            <tr>
            	<td style="font-size:20px;" align="center" colspan="6">
                	<strong>
						<? 
							if($sql_mst[0][csf('within_group')]==1)
							{
                            	$buyer=$company_arr[$sql_mst[0][csf('buyer_id')]];
							}
							else
							{
								$buyer=return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0][csf('buyer_id')]."'");
							}
							echo $buyer;
                        ?>
                    </strong>
                </td>
            </tr>
            <tr>
            	<td width="100" align="right">From</td>
            </tr>
            <tr>
            	<td></td>
                <td width="200"><? echo $company_arr[$sql_mst[0][csf('exporter_id')]]; ?></td>
                <td width="100">PI No:</td>
                <td width="250"><? echo $sql_mst[0][csf('pi_number')];?></td>
                <td width="150">Within Group:</td>
                <td><? echo $yes_no[$sql_mst[0][csf('within_group')]];?></td>
            </tr>
            <tr>
                <td></td>
                <td rowspan="3"><? echo $company_address ;?></td>
                <td>PI Date:</td>
                <td><? echo change_date_format($sql_mst[0][csf('pi_date')]);?></td>
                <td>Last Shipment Date:</td>
                <td><? echo change_date_format($sql_mst[0][csf('last_shipment_date')]);?></td>
            </tr> 
            <tr>
            	<td></td>
                <td>Currency:</td>
                <td><? echo $currency[$sql_mst[0][csf('currency_id')]];?></td>
                <td>Validity:</td>
                <td><? echo change_date_format($sql_mst[0][csf('pi_validity_date')]);?></td>
            </tr> 
        </table>
        <br>
		<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
            <thead>
                <th>Job No</th>
                <th>Construction</th>
                <th>Composition</th>
                <th>Color</th>					
                <th>GSM</th>
                <th>Dia/Width</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Amount</th>
            </thead>
            <tbody>
			<?
				$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name" ); $total_ammount = 0; $total_quantity=0;
				$sql = "select id, work_order_no, color_id, construction, composition, gsm, dia_width, uom, quantity, rate, amount from com_export_pi_dtls where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0";
				$data_array=sql_select($sql);
				foreach($data_array as $row)
				{
				?>
                    <tr>
                        <td><?php echo $row[csf('work_order_no')]; ?></td>
                        <td><?php echo $row[csf('construction')]; ?></td>
                        <td><?php echo $row[csf('composition')]; ?></td>
                        <td><?php echo $color_library[$row[csf('color_id')]]; ?></td>
                        <td><?php echo $row[csf('gsm')]; ?></td>
                        <td><?php echo $row[csf('dia_width')]; ?></td>
                        <td><?php echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="right"><?php echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                        <td align="right"><?php echo number_format($row[csf('rate')],4); ?></td>
                        <td align="right"><?php echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
                    </tr>
				<? 
                } 
                ?>
                <tr>
                    <td align="right" colspan="7">Total</td> 
                    <td><? //echo number_format($total_quantity,2);?></td>
                    <td></td>
                    <td align="right"><? echo number_format($total_ammount,4); ?></td>
                </tr>
            </tbody> 
        </table>
        <table>
            <tr height="20"></tr>
            <tr>
                <td valign="top"><strong>In-Words: </strong></td>
                <td><? echo number_to_words(number_format($total_ammount,4, '.', ''),'USD','Cent');?></td>
            </tr>
            <tr> 
            <tr height="50"></tr>
        </table>
        <table>
            <tr height="20"></tr>
            <tr>
            	<td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
            </tr>
            <tr height="50"></tr>
        </table>
	</div>
<?
	exit();	 
 }


if ($action=="job_popup")
{
	echo load_html_head_contents("Job Popup Info","../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(ids)
		{ 
		//alert (id);
			$("#hidden_mst_id").val(ids);
			document.getElementById('selected_job').value=ids;
			parent.emailwindow.hide();
		}
		
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
                <table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead> 
                        <tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Job No</th>
                            <th width="100">Order No</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>           
                    </thead>
                    <tbody>
                        <tr>
                        <td> <input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
                            <? 
                               echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "load_drop_down( 'sub_contract_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                        </td>
                        <td id="buyer_td">
                            <? echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );   	 
                            ?>
                        </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                        </td>
                        <td align="center">
                    		<input type="text" name="txt_search_job" id="txt_search_job" class="text_boxes" style="width:100px" placeholder="Search Job" />
                        </td> 
                        <td align="center">
                    		<input type="text" name="txt_search_order" id="txt_search_order" class="text_boxes" style="width:100px" placeholder="Search Order" />
                        </td> 
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_order').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_job_search_list_view', 'search_div', 'subcontract_pi_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" valign="top" id=""><div id="search_div"></div></td>
                        </tr>
                    </tbody>
                </table>    
            </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_job=str_replace("'","",$data[4]);
	$search_order=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	
	if($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if($search_type==1)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num='$search_job'"; else $search_job_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no='$search_order'"; else $search_order_cond="";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '%$search_job%'"; else $search_job_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no like '%$search_order%'"; else $search_order_cond="";
	}
	else if($search_type==2)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '$search_job%'"; else $search_job_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no like '$search_order%'"; else $search_order_cond="";
	}
	else if($search_type==3)
	{
		if($search_job!='') $search_job_cond=" and a.job_no_prefix_num like '%$search_job'"; else $search_job_cond="";
		if($search_order!='') $search_order_cond=" and b.order_no like '%$search_order'"; else $search_order_cond="";
	}	
	
	if($party_id!=0) $party_id_cond=" and party_id='$party_id'"; else $party_id_cond="";
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and b.order_rcv_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and b.order_rcv_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}
	
	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (3=>$production_process,4=>$party_arr,5=>$service_type);

	if($db_type==0)
	{
		$sql= "select a.id as mst_id, a.subcon_job, a.job_no_prefix_num, YEAR(a.insert_date) as year, a.company_id, a.location_id, a.party_id, a.status_active, b.id, b.job_no_mst, b.order_no, b.order_rcv_date, b.delivery_date, b.main_process_id, b.status_active from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active in (1,2) $order_rcv_date $company $search_job_cond $search_order_cond $party_id_cond order by a.id DESC";
	}
	else if($db_type==2)
	{
		$sql= "select a.id as mst_id, a.subcon_job, a.job_no_prefix_num, TO_CHAR(a.insert_date,'YYYY') as year, a.company_id, a.location_id, a.party_id, a.status_active, b.id, b.job_no_mst, b.order_no, b.order_rcv_date, b.delivery_date, b.main_process_id, b.status_active from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active in (1,2) $order_rcv_date $company $search_job_cond $search_order_cond $party_id_cond order by a.id DESC";
	}
		 //echo $sql;die;
	 echo  create_list_view("list_view", "Job No,Year,Order No,Process,Party Name,Order Date,Delivery Date","70,80,100,100,100,70,70","740","250",0,$sql, "js_set_value","mst_id,id","",1,"0,0,0,main_process_id,party_id,0,0",$arr,"job_no_prefix_num,year,order_no,main_process_id,party_id,order_rcv_date,delivery_date", "",'','0,0,0,0,0,3,3') ;
	exit();		 
} 


?>


 