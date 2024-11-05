<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id = $_SESSION['logic_erp']["user_id"];

//------------------------------------------Load Drop Down on Change---------------------------------------------//
if ($action=="load_supplier_dropdown")
{
	$data = explode('_',$data);
	
	if($data[1]==0) 
	{
		echo create_drop_down( "cbo_supplier_id", 151, $blank_array,'', 1, '-- Select Supplier --',0,'',0);
	}
	else if($data[1]==4)
	{
		echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
		
	}
	else
	{
		echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 7 and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);

	} 
	exit();
}

if ($action=="save_update_delete")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	 
	if ($operation==0)  // Insert Here
	{ 
		$pi_year =  date("Y",strtotime(str_replace("'","", $pi_date)));
		if($db_type==0) $year_cond=" and year(pi_date)=".trim($pi_year); else $year_cond=" and to_char(pi_date,'YYYY')=".trim($pi_year);
		if (is_duplicate_field( "pi_number", "com_pi_master_details", "pi_number=$pi_number and importer_id=$cbo_importer_id and supplier_id=$cbo_supplier_id $year_cond and status_active=1 and is_deleted=0" ) == 1)
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
			 
			$id=return_next_id( "id", "com_pi_master_details", 1 ); 
			
			if(str_replace("'", '', $export_pi_id)>0) 
			{
				$field_array_dtls="id, pi_id, work_order_no, work_order_id, work_order_dtls_id, determination_id, fabric_construction, fabric_composition, color_id, gsm, dia_width, uom, quantity, rate, amount, net_pi_rate, net_pi_amount, inserted_by, insert_date";
				$idDtls=return_next_id( "id","com_pi_item_details", 1 ) ;
				
				$is_import_pi=1;$woDtlsTrsansId='';
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
					$wotransId=str_replace("'","",$$workOrderDtlsId);
					if($woDtlsTrsansId=='') $woDtlsTrsansId=$wotransId;else $woDtlsTrsansId.=','.$wotransId;
					
					$perc=(str_replace("'","",$$amount)/str_replace("'","",$txt_total_amount))*100;
					$net_pi_amount=($perc*str_replace("'","",$txt_total_amount_net))/100;
					$net_pi_rate=$net_pi_amount/str_replace("'","",$$quantity);
					
					if(str_replace("'","",$cbo_currency_id)==1) 
						$net_pi_amount=number_format($net_pi_amount,$dec_place[4],'.','');
					else
						$net_pi_amount=number_format($net_pi_amount,$dec_place[5],'.','');
							
					$net_pi_rate=number_format($net_pi_rate,$dec_place[3],'.','');
					
					//if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls[$idDtls]="(".$idDtls.",".$id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$determinationId)."','".str_replace("'","",$$construction)."','".str_replace("'","",$$composition)."','".str_replace("'","",$$colorId)."','".str_replace("'","",$$gsm)."','".str_replace("'","",$$diawidth)."','".str_replace("'","",$$uom)."',".$$quantity.",".$$rate.",".$$amount.",".$net_pi_rate.",".$net_pi_amount.",".$user_id.",'".$pc_date_time."')"; 
					
					$idDtls=$idDtls+1;
					
				}
			}
			else 
			{
				$is_import_pi=0; 
			}
			//cbo_item_category_id
			/*if(str_replace("'", '',$cbo_pi_basis_id)==1 && str_replace("'", '',$cbo_goods_rcv_status)==1 && str_replace("'", '',$cbo_item_category_id)==1)
			{
				$sql_trns="Select id, order_qnty from inv_transaction where id in ($woDtlsTrsansId)";echo 10;die;
			}*/
			
			$field_array="id,item_category_id,importer_id,supplier_id,pi_number,pi_date,last_shipment_date,pi_validity_date,currency_id,source,hs_code,internal_file_no,intendor_name,pi_basis_id,remarks,goods_rcv_status,export_pi_id,within_group,total_amount,upcharge,discount,net_total_amount,import_pi,ready_to_approved,approval_user,inserted_by,insert_date,lc_group_no,entry_form,version";
			
			$data_array="(".$id.",".$cbo_item_category_id.",".$cbo_importer_id.",".$cbo_supplier_id.",".$pi_number.",".$pi_date.",".$last_shipment_date.",".$pi_validity_date.",".$cbo_currency_id.",".$cbo_source_id.",".$hs_code.",".$txt_internal_file_no.",".$intendor_name.",".$cbo_pi_basis_id.",".$txt_remarks.",".$cbo_goods_rcv_status.",".$export_pi_id.",".$within_group.",'".str_replace("'", '', $txt_total_amount)."','".str_replace("'", '', $txt_upcharge)."','".str_replace("'", '', $txt_discount)."','".str_replace("'", '', $txt_total_amount_net)."',".$is_import_pi.",".$cbo_ready_to_approved.",".$hiddn_user_id.",".$user_id.",'".$pc_date_time."',".$txt_lc_group_no.",167,2)";
			
			//echo "5**insert into com_pi_master_details (".$field_array.") values ".$data_array;die;	
			$rID=sql_insert("com_pi_master_details",$field_array,$data_array,1);
			$rID2=true;
			if($data_array_dtls!="")
			{
				$rID2=sql_insert("com_pi_item_details",$field_array_dtls,$data_array_dtls,0);
			}
			//oci_rollback($con); 
		    //echo "10**".$rID.$rID2;
		    //echo "5**insert into com_pi_item_details (".$field_array.") values ".$data_array;die;	
			if($db_type==0)
			{
				if($rID && $rID2)
				{
					mysql_query("COMMIT");  
					echo "0**".$id;
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
					echo "0**".$id;
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
		$sql_app=sql_select("select approved from com_pi_master_details where id=$update_id and approved=1");
		if(count($sql_app)>0)
		{
			echo "16**1**1"; 
			die;	
		}
		
		//###### Descuss with siddiq sir update facility consider ######///////
		/*$sql_attach=sql_select("select a.lc_number from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and b.pi_id=$update_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.lc_number");
		if(count($sql_attach)>0)
		{
			$lc_number=$sql_attach[0][csf('lc_number')];
			echo "14**This PI is Attached With BTB LC No- '".$lc_number."'. So You can not change it**1"; 
			die;	
		}
		
		$sql_attach_mrr=sql_select("select a.recv_number from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.pi_wo_batch_no=$update_id and b.receive_basis=1 and b.status_active=1 and b.is_deleted=0 group by a.id, a.recv_number");
		if(count($sql_attach_mrr)>0)
		{
			$recv_number='';
			foreach($sql_attach_mrr as $row)
			{
				$recv_number.=$row[csf('recv_number')].",";
			}
			
			echo "14**This PI is Attached With MRR No- '".chop($recv_number,',')."'. So You can not change it**1"; 
			die;	
		}*/
		
		$pi_year =  date("Y",strtotime(str_replace("'","", $pi_date)));
		if($db_type==0) $year_cond=" and year(pi_date)=".trim($pi_year); else $year_cond=" and to_char(pi_date,'YYYY')=".trim($pi_year);
		if(is_duplicate_field("pi_number", "com_pi_master_details","pi_number=$pi_number and importer_id=$cbo_importer_id and supplier_id=$cbo_supplier_id $year_cond and id!=$update_id and status_active=1 and is_deleted=0")==1)
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
			
			$field_array="item_category_id*importer_id*supplier_id*pi_number*pi_date*last_shipment_date*pi_validity_date*currency_id*source*hs_code*internal_file_no*intendor_name*pi_basis_id*remarks*goods_rcv_status*total_amount*upcharge*discount*net_total_amount*ready_to_approved*approval_user*updated_by*update_date*lc_group_no";
			
			$data_array=$cbo_item_category_id."*".$cbo_importer_id."*".$cbo_supplier_id."*".$pi_number."*".$pi_date."*".$last_shipment_date."*".$pi_validity_date."*".$cbo_currency_id."*".$cbo_source_id."*".$hs_code."*".$txt_internal_file_no."*".$intendor_name."*".$cbo_pi_basis_id."*".$txt_remarks."*".$cbo_goods_rcv_status."*".$txt_total_amount."*".$txt_upcharge."*".$txt_discount."*".$txt_total_amount_net."*".$cbo_ready_to_approved."*".$hiddn_user_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_lc_group_no."";
			
			//echo "5**".$data_array; die;
			
			if(str_replace("'", '', $export_pi_id)>0) 
			{
				$field_array_update="work_order_no*work_order_id*work_order_dtls_id*determination_id*fabric_construction*fabric_composition*color_id*gsm*dia_width*uom*quantity*rate*amount*net_pi_rate*net_pi_amount*updated_by*update_date";
				$field_array_dtls="id,pi_id,work_order_no,work_order_id,work_order_dtls_id,determination_id,fabric_construction,fabric_composition,color_id,gsm,dia_width,uom,quantity,rate,amount,net_pi_rate,net_pi_amount,inserted_by,insert_date";
				$idDtls=return_next_id( "id","com_pi_item_details", 1 ) ;
				
				$data_array_dtls='';
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
					
					$perc=(str_replace("'","",$$amount)/str_replace("'","",$txt_total_amount))*100;
					$net_pi_amount=($perc*str_replace("'","",$txt_total_amount_net))/100;
					$net_pi_rate=$net_pi_amount/str_replace("'","",$$quantity);
					
					if(str_replace("'","",$cbo_currency_id)==1)
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
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$idDtls.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$determinationId)."','".str_replace("'","",$$construction)."','".str_replace("'","",$$composition)."','".str_replace("'","",$$colorId)."','".str_replace("'","",$$gsm)."','".str_replace("'","",$$diawidth)."','".str_replace("'","",$$uom)."',".$$quantity.",".$$rate.",".$$amount.",".$net_pi_rate.",".$net_pi_amount.",".$user_id.",'".$pc_date_time."')"; 
						$idDtls=$idDtls+1;
					}
				}
			}
			
			$rID=sql_update("com_pi_master_details",$field_array,$data_array,"id",$update_id,1);
			$rID2=true; $rID3=true;
			if(count($data_array_update)>0)
			{
				$rID2=execute_query(bulk_update_sql_statement( "com_pi_item_details", "id", $field_array_update, $data_array_update, $id_arr ));
			}
			
			if($data_array_dtls!="")
			{
				$rID3=sql_insert("com_pi_item_details",$field_array_dtls,$data_array_dtls,0);
			}
			//echo "10**".$rID ."&&". $rID2 ."&&". $rID3;die;
				
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
					echo "6**".str_replace("'", '', $update_id);
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
					echo "6**".str_replace("'", '', $update_id);
				}
			}
			
			disconnect($con);
			die;
		}
	}
	else if ($operation==2)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$sql_app=sql_select("select approved from com_pi_master_details where id=$update_id and approved=1");
		if(count($sql_app)>0)
		{
			echo "16**1**1"; 
			die;	
		}
		
		$sql_attach=sql_select("select a.lc_number from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and b.pi_id=$update_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.lc_number");
		if(count($sql_attach)>0)
		{
			$lc_number=$sql_attach[0][csf('lc_number')];
			echo "14**This PI is Attached With BTB LC No- '".$lc_number."'. So You can not delete it**1"; 
			die;	
		}
		if(str_replace("'","",$cbo_goods_rcv_status)!=1)
		{
			$sql_attach_mrr=sql_select("select a.recv_number from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.pi_wo_batch_no=$update_id and b.receive_basis=1 and b.status_active=1 and b.is_deleted=0 group by a.id, a.recv_number");
			if(count($sql_attach_mrr)>0)
			{
				$recv_number='';
				foreach($sql_attach_mrr as $row)
				{
					$recv_number.=$row[csf('recv_number')].",";
				}
				
				echo "14**This PI is Attached With MRR No- '".chop($recv_number,',')."'. So You can not delete it**1"; 
				die;	
			}
		}
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_update("com_pi_master_details",$field_array,$data_array,"id",$update_id,0);
		
		$field_array_dtls="status_active*is_deleted*updated_by*update_date";
		$data_array_dtls="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID2=sql_update("com_pi_item_details",$field_array_dtls,$data_array_dtls,"pi_id",$update_id,1);
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "2**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**".str_replace("'", '', $update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);   
				echo "2**0";
			}
			else
			{
				oci_rollback($con);  
				echo "7**".str_replace("'", '', $update_id);
			}
		}
		
		disconnect($con);
		die;
	}
}

if($action=="load_drop_down_importer")
{
	if($data==1)
	{
		echo create_drop_down( "cbo_importer", 151, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Importer --", "0", "",0 );
	}
	else if($data==2)
	{
		echo create_drop_down( "cbo_importer", 151, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Importer --", $selected, "",0 );  
	}
	else 
	{
		echo create_drop_down( "cbo_importer", 151, $blank_array,"",1, "-- Select Importer --", 0, "" );
	}
	exit();
}

if ($action=="load_drop_down_exporter")
{
	echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =2 and c.status_active=1 and c.is_deleted=0",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	exit();
}

if ($action=="export_pi_popup")
{
	echo load_html_head_contents("PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		
		function js_set_value(pi_id )
		{
			document.getElementById('txt_selected_data').value=pi_id;
			parent.emailwindow.hide();
		}
		
    </script>

</head>

<body>
<div align="center" style="width:900px;">
	<form name="searchpifrm"  id="searchpifrm">
		<fieldset style="width:100%; margin-left:4px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="800" class="rpt_table">
                <thead>
                    <th>Within Group</th>
                    <th>Importer</th>
                    <th>Export PI Number</th>
                    <th>Date Range</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="txt_selected_data" id="txt_selected_data" class="text_boxes" style="width:70px" value="">   
                    </th> 
                </thead>
                <tr class="general">
                    <td>
						<?php echo create_drop_down( "cbo_within_group", 151, $yes_no,"", 1, "-- Select --", 0, "load_drop_down( 'pi_accessories_controller',this.value, 'load_drop_down_importer', 'importer_td' );" ); ?>
                    </td>
                    <td id="importer_td"> 
						<?php echo create_drop_down( "cbo_importer", 151, $blank_array,"", 1, "-- Select Importer --", 0, "",0 ); ?>
                    </td>
                    <td> 
                        <input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:120px">
                    </td>						
                    <td align="center">
                      <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_importer').value, 'create_export_pi_search_list_view', 'search_div', 'pi_accessories_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_export_pi_search_list_view")
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
	
	$cbo_within_group=$data[3];
	$cbo_importer=$data[4];
	if($cbo_within_group!=0) $within_group=" and within_group='".$cbo_within_group."'"; else { $within_group = ''; }
	if($cbo_importer!=0) $importer_cond=" and buyer_id='".$cbo_importer."'"; else { $importer_cond = ''; }

	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$arr=array (2=>$export_item_category,3=>$comp,4=>$yes_no);
	 
	//echo create_list_view("list_view", "PI No,PI Date,Item Category,Exporter,Within Group,Last Shipment Date,HS Code", "150,80,80,150,80,120","880","270",0, $sql , "js_set_value", "id", "", 1, "0,0,item_category_id,exporter_id,within_group,0,0", $arr , "pi_number,pi_date,item_category_id,exporter_id,within_group,last_shipment_date,hs_code", "",'','0,3,0,0,0,3,0');
	
	$importPiArr=array();
	$importData=sql_select("select id,source,intendor_name,remarks,goods_rcv_status,export_pi_id,total_amount,upcharge,discount,net_total_amount,lc_group_no from com_pi_master_details where import_pi=1");
	foreach($importData as $rowI)
	{
		$importPiArr[$rowI[csf('export_pi_id')]]['id']=$rowI[csf('id')];
		$importPiArr[$rowI[csf('export_pi_id')]]['source']=$rowI[csf('source')];
		$importPiArr[$rowI[csf('export_pi_id')]]['intendor_name']=$rowI[csf('intendor_name')];
		$importPiArr[$rowI[csf('export_pi_id')]]['goods_rcv_status']=$rowI[csf('goods_rcv_status')];
		$importPiArr[$rowI[csf('export_pi_id')]]['remarks']=$rowI[csf('remarks')];
		$importPiArr[$rowI[csf('export_pi_id')]]['lc_group_no']=$rowI[csf('lc_group_no')];
		//$importPiArr[$rowI[csf('export_pi_id')]]['total_amount']=$rowI[csf('total_amount')];
		//$importPiArr[$rowI[csf('export_pi_id')]]['upcharge']=$rowI[csf('upcharge')];
		//$importPiArr[$rowI[csf('export_pi_id')]]['discount']=$rowI[csf('discount')];
		//$importPiArr[$rowI[csf('export_pi_id')]]['net_total_amount']=$rowI[csf('net_total_amount')];
	}

	$sql= "select id, pi_number, pi_date, item_category_id, exporter_id, buyer_id, within_group, last_shipment_date, hs_code, pi_validity_date, currency_id, upcharge, discount, internal_file_no, remarks, total_amount, net_total_amount from com_export_pi_mst where status_active=1 and is_deleted=0 $within_group $importer_cond $pi_number $pi_date order by pi_number";  
	?>
	<table width="895" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead> 
            <th width="40">SL</th>
            <th width="90">PI No</th>
            <th width="80">PI Date</th>
            <th width="80">Item Category</th>
            <th width="75">Within Group</th>
            <th width="125">Importer</th>
            <th width="125">Exporter</th>
            <th width="105">Last Shipment Date</th>
            <th width="70">HS Code</th>
            <th>Import PI Id</th>
        </thead>
     </table>
     <div style="width:895px; overflow-y:scroll; max-height:280px">  
     	<table width="875" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view"> 
		<?
			$data_array=sql_select($sql); $i = 1; 
            foreach($data_array as $row)
            { 
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
				$importer='';
				if($row[csf('within_group')]==2)
				{
					$importer=$buyer_arr[$row[csf('buyer_id')]];
				}
				else
				{
					$importer=$comp[$row[csf('buyer_id')]];
				}
				
				$importPi_id=$importPiArr[$row[csf('id')]]['id'];
				if($importPi_id>0) $remarks=$importPiArr[$row[csf('id')]]['remarks']; else $remarks=$row[csf('remarks')];
				
				$data=$row[csf('id')]."_".$row[csf('within_group')]."_".$row[csf('buyer_id')]."_".$row[csf('exporter_id')]."_".$row[csf('pi_number')]."_".change_date_format($row[csf('pi_date')])."_".change_date_format($row[csf('last_shipment_date')])."_".change_date_format($row[csf('pi_validity_date')])."_".$row[csf('currency_id')]."_".$row[csf('hs_code')]."_".$row[csf('internal_file_no')]."_".$remarks."_".$importer."_".$comp[$row[csf('exporter_id')]]."_".$row[csf('upcharge')]."_".$row[csf('discount')]."_".$row[csf('total_amount')]."_".$row[csf('net_total_amount')]."_".$importPi_id."_".$importPiArr[$row[csf('id')]]['source']."_".$importPiArr[$row[csf('id')]]['intendor_name']."_".$importPiArr[$row[csf('goods_rcv_status')]]['id']."_".$row[csf('item_category_id')]."_".$export_item_category[$row[csf('item_category_id')]]."_".$importPiArr[$row[csf('id')]]['lc_group_no'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $data; ?>');">       	
                	<td width="40"><? echo $i; ?></td>
					<td width="90"><? echo $row[csf('pi_number')]; ?></td>
					<td width="80"><? echo change_date_format($row[csf('pi_date')]); ?></td>
                    <td width="80"><p><? echo $export_item_category[$row[csf('item_category_id')]]; ?></p></td>
					<td width="75" align="center"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
                    <td width="125"><p><? echo $importer; ?></p></td>
                    <td width="125"><p><? echo $comp[$row[csf('exporter_id')]]; ?></p></td>
                    <td width="105" align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
					<td width="70"><? echo $row[csf('hs_code')]; ?>&nbsp;</td>
                    <td><? echo $importPi_id; ?>&nbsp;</td>
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

$color_library=return_library_array( "select id,color_name from lib_color where status_active=1", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size where status_active=1", "id", "size_name"  );

if( $action == 'export_pi_details' ) 
{	
	$data=explode("**",$data);
	$export_id=$data[0];
	$upcharge=$data[1];
	$discount=$data[2];
	$total_amount=$data[3];
	$net_total_amount=$data[4];

?>
	<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
        <thead>
            <th>Job No</th>
            <th class="must_entry_caption">Construction</th>
            <th>Composition</th>
            <th class="must_entry_caption">Color</th>					
            <th>GSM</th>
            <th class="must_entry_caption">Dia/Width</th>
            <th>UOM</th>
            <th class="must_entry_caption">Quantity</th>
            <th class="must_entry_caption">Rate</th>
            <th>Amount</th>
        </thead>    
        <tbody id="pi_details_container">
		<?
            $tblRow=0;
            $sql = "select id, work_order_no, work_order_id, work_order_dtls_id, determination_id, color_id, construction, composition, gsm, dia_width, uom, quantity, rate, amount from com_export_pi_dtls where pi_id='$export_id' and quantity>0 and status_active=1 and is_deleted=0";
            $data_array=sql_select($sql);
            foreach($data_array as $row)
            {
                $tblRow++;
                
                if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
                    <td>
                        <input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:110px;" disabled="disabled" />			
                        <input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_id')]; ?>" readonly />
                        <input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_dtls_id')]; ?>" readonly />
                    </td>
                    <td> 
                        <input type="text" name="construction_<? echo $tblRow; ?>" id="construction_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('construction')]; ?>" style="width:110px" disabled="disabled"/>
                        <input type="hidden" name="hideDeterminationId_<? echo $tblRow; ?>" id="hideDeterminationId_<? echo $tblRow; ?>" value="<? echo $row[csf('determination_id')]; ?>" readonly />
                    </td>
                    <td>
                        <input type="text" name="composition_<? echo $tblRow; ?>" id="composition_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('composition')]; ?>" style="width:110px" disabled="disabled"/>
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
                        <input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" disabled/>
                    </td>
                    <td>
                        <input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" disabled/>
                    </td>
                    <td>
                        <input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" disabled/>
                        <input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
                    </td>
                </tr>
			<?
            }
		?>
   		</tbody> 
        <tfoot class="tbl_bottom">
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Total</td>
                <td style="text-align:center">
                    <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $total_amount; ?>" style="width:75px;" readonly/>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Upcharge</td>
                <td style="text-align:center">
                    <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:75px;" readonly/>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Discount</td>
                <td style="text-align:center">
                    <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:75px;" readonly/>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Net Total</td>
                <td style="text-align:center">
                    <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $net_total_amount; ?>" style="width:75px;" readonly/>
                </td>
            </tr>
        </tfoot>
    </table>    
	<?         
	exit();
}

if( $action == 'export_pi_details_update' ) 
{	
	$data=explode("**",$data);
	$import_id=$data[0];
	$upcharge=$data[1];
	$discount=$data[2];
	$total_amount=$data[3];
	$net_total_amount=$data[4];

?>
	<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
        <thead>
            <th>Job No</th>
            <th class="must_entry_caption">Construction</th>
            <th>Composition</th>
            <th class="must_entry_caption">Color</th>					
            <th>GSM</th>
            <th class="must_entry_caption">Dia/Width</th>
            <th>UOM</th>
            <th class="must_entry_caption">Quantity</th>
            <th class="must_entry_caption">Rate</th>
            <th>Amount</th>
        </thead>    
        <tbody id="pi_details_container">
		<?
            $tblRow=0;
            $sql = "select id, work_order_no, work_order_id, work_order_dtls_id, determination_id, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, quantity, rate, amount from com_pi_item_details where pi_id='$import_id' and status_active=1 and is_deleted=0";
            $data_array=sql_select($sql);
            foreach($data_array as $row)
            {
                $tblRow++;
                
                if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
                    <td>
                        <input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:110px;" disabled="disabled" />			
                        <input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_id')]; ?>" readonly />
                        <input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_dtls_id')]; ?>" readonly />
                    </td>
                    <td> 
                        <input type="text" name="construction_<? echo $tblRow; ?>" id="construction_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('construction')]; ?>" style="width:110px" disabled="disabled"/>
                        <input type="hidden" name="hideDeterminationId_<? echo $tblRow; ?>" id="hideDeterminationId_<? echo $tblRow; ?>" value="<? echo $row[csf('determination_id')]; ?>" readonly />
                    </td>
                    <td>
                        <input type="text" name="composition_<? echo $tblRow; ?>" id="composition_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('composition')]; ?>" style="width:110px" disabled="disabled"/>
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
                        <input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" disabled/>
                    </td>
                    <td>
                        <input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" disabled/>
                    </td>
                    <td>
                        <input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" disabled/>
                        <input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
                    </td>
                </tr>
			<?
            }
		?>
   		</tbody> 
        <tfoot class="tbl_bottom">
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Total</td>
                <td style="text-align:center">
                    <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $total_amount; ?>" style="width:75px;" readonly/>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Upcharge</td>
                <td style="text-align:center">
                    <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:75px;" readonly/>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Discount</td>
                <td style="text-align:center">
                    <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:75px;" readonly/>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Net Total</td>
                <td style="text-align:center">
                    <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $net_total_amount; ?>" style="width:75px;" readonly/>
                </td>
            </tr>
        </tfoot>
    </table>    
	<?         
	exit();
}

///Save Item Details Table

if ($action=="save_update_delete_dtls")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$conver_factor=return_library_array("select a.id, b.conversion_factor from product_details_master a, lib_item_group b where a.item_group_id=b.id","id","conversion_factor");
	
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	 
		$id=return_next_id( "id","com_pi_item_details", 1 ) ;
	
		$field_array="id,pi_id,work_order_no,work_order_id,work_order_dtls_id,booking_without_order,determination_id,item_prod_id,item_group,item_description,color_id,size_id,item_size,count_name,yarn_composition_item1,yarn_composition_percentage1,yarn_composition_item2,yarn_composition_percentage2,fabric_composition,fabric_construction,yarn_type,gsm,dia_width,weight,item_color,uom,quantity,rate,amount,net_pi_rate,net_pi_amount,service_type,brand_supplier,gmts_item_id,embell_name,embell_type,lot_no,yarn_color,color_range,test_for,test_item_id,remarks,item_category_id,entry_form,inserted_by,insert_date"; 
		
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
		
		
		$all_dtls_id="";
		for($i=1;$i<=$total_row;$i++)
		{
			$workOrderId="hideWoId_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$hideTransData="hideTransData_".$i;
			$all_WoId.=str_replace("'","",$$workOrderId).",";
			if(str_replace("'", '',$cbo_pi_basis_id)==1 && str_replace("'", '',$cbo_goods_rcv_status)==1 && (str_replace("'", '',$cbo_item_category_id)!=12 && str_replace("'", '',$cbo_item_category_id)!=25 && str_replace("'", '',$cbo_item_category_id)!=31))
			{
				$hideTransData_arr=explode("__",str_replace("'","",$$hideTransData));
				foreach($hideTransData_arr as $hide_trans_val)
				{
					$hide_trans_val_arr=explode("_",$hide_trans_val);
					$all_dtls_id.=$hide_trans_val_arr[0].",";
				}
			}
			else
			{
				$all_dtls_id.=str_replace("'","",$$workOrderDtlsId).",";
			}
		}
		$all_dtls_id=chop($all_dtls_id,",");
		$all_WoId=implode(",",array_unique(explode(",",chop($all_WoId,","))));
		
		if(str_replace("'", '',$cbo_goods_rcv_status)==1)
		{
			if($all_dtls_id=="") $all_dtls_id=0;

			$wo_qnty_arr=return_library_array("select id, cons_quantity as qnty from inv_transaction where status_active=1 and id in($all_dtls_id)","id","qnty");
			
			$previous_pi_qty=return_library_array("select b.work_order_dtls_id as id, sum(b.quantity) as qnty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=".str_replace("'", '',$cbo_item_category_id)." and a.goods_rcv_status=1 and b.status_active=1 and b.work_order_dtls_id in($all_dtls_id) group by b.work_order_dtls_id","id","qnty");
			
			if(str_replace("'","",$cbo_item_category_id)==4)
			{
				$prev_retun_sql="select booking_id, prod_id, issue_qnty from inv_trims_issue_dtls where booking_id>0 and  booking_id in($all_WoId) order by booking_id,prod_id";
				
				$prev_retun_sql_result=sql_select($prev_retun_sql);
				foreach($prev_retun_sql_result as $row)
				{
					$prev_retun_qnty[$row[csf("booking_id")]][$row[csf("prod_id")]]+=$row[csf("issue_qnty")];
				}
				
				//echo "10**";print_r($prev_retun_qnty);die;
			}
			
		}
		else
		{
			if($all_dtls_id=="") $all_dtls_id=0;
			$booking_id_cond=""; $bokIds_cond=""; $all_dtls_arr=explode(",",$all_dtls_id);
			if($db_type==2 && count($all_dtls_arr)>999)
			{
				$booking_id_chunk_arr=array_chunk($all_dtls_arr,999) ;
				foreach($booking_id_chunk_arr as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);	
					$bokIds_cond.=" b.work_order_dtls_id in($chunk_arr_value) or ";	
				}
				
				$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
				//echo $booking_id_cond;die;
			}
			else
			{
				$booking_id_cond=" and b.work_order_dtls_id in($all_dtls_id)";	 
			}
			
			$previous_pi_qty=return_library_array("select b.work_order_dtls_id as id, sum(b.quantity) as qnty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=".str_replace("'", '',$cbo_item_category_id)." and a.goods_rcv_status<>1 and b.status_active=1 $booking_id_cond group by b.work_order_dtls_id","id","qnty");
			if(str_replace("'", '',$txt_order_type)==1)
			{
				if($all_dtls_id=="") $all_dtls_id=0;
				$booking_id_cond=""; $bokIds_cond=""; $all_dtls_arr=explode(",",$all_dtls_id);
				if($db_type==2 && count($all_dtls_arr)>999)
				{
					$booking_id_chunk_arr=array_chunk($all_dtls_arr,999) ;
					foreach($booking_id_chunk_arr as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);	
						$bokIds_cond.="  id in($chunk_arr_value) or ";	
					}
					
					$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
					//echo $booking_id_cond;die;
				}
				else
				{
					$booking_id_cond=" and id in($all_dtls_id)";	 
				}
				
				$wo_qnty_arr=return_library_array("select id as wo_trim_booking_dtls_id, trim_qty as qnty from wo_non_ord_samp_booking_dtls where status_active=1 $booking_id_cond","wo_trim_booking_dtls_id","qnty");
			}
			else
			{
				if($all_dtls_id=="") $all_dtls_id=0;
				$booking_id_cond=""; $bokIds_cond=""; $all_dtls_arr=explode(",",$all_dtls_id);
				if($db_type==2 && count($all_dtls_arr)>999)
				{
					$booking_id_chunk_arr=array_chunk($all_dtls_arr,999) ;
					foreach($booking_id_chunk_arr as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);	
						$bokIds_cond.="  wo_trim_booking_dtls_id in($chunk_arr_value) or ";	
					}
					
					$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
					//echo $booking_id_cond;die;
				}
				else
				{
					$booking_id_cond=" and wo_trim_booking_dtls_id in($all_dtls_id)";	 
				}
				
				
				$wo_qnty_arr=return_library_array("select wo_trim_booking_dtls_id, sum(cons) as qnty from wo_trim_book_con_dtls where status_active=1 $booking_id_cond and cons>0 group by wo_trim_booking_dtls_id","wo_trim_booking_dtls_id","qnty");
			}
		}
		
		//echo "10**".var_dump($prev_pi_qty_arr);die;

		//echo "10**$total_row jahid";die; txt_order_type
		$woDtlsTrsansId='';$trans_qty_check_arr=array();
		for($i=1;$i<=$total_row;$i++)
		{
			$colorName="colorName_".$i;  
			$sizeName="sizeName_".$i;
			$countName="countName_".$i;
			$yarnCompositionItem1="yarnCompositionItem1_".$i;
			$yarnCompositionPercentage1="yarnCompositionPercentage1_".$i;
			$yarnCompositionItem2="yarnCompositionItem2_".$i;
			$yarnCompositionPercentage2="yarnCompositionPercentage2_".$i;			 
			$yarnType="yarnType_".$i;
			$uom="uom_".$i;
			$quantity="quantity_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			$workOrderNo="workOrderNo_".$i;
			$workOrderId="hideWoId_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$itemProdId="itemProdId_".$i;
			$determinationId="hideDeterminationId_".$i; 
			$construction="construction_".$i; 
			$composition="composition_".$i;
			$gsm="gsm_".$i;
			$diawidth="diawidth_".$i;
			$weight="weight_".$i;
			$itemgroupid="itemgroupid_".$i;
			$itemdescription="itemdescription_".$i;
			$servicetype="servicetype_".$i;
			$item_color="itemColor_".$i;
			$itemSize="itemSize_".$i;
			$brandSupRef="brandSupRef_".$i;
			$lot="lot_".$i;
			$yarnColor="yarnColor_".$i;
			$colorRange="colorRange_".$i;
			$gmtsitem="gmtsitem_".$i;
			$embellname="embellname_".$i;
			$embelltype="embelltype_".$i;
			$bookingWithoutOrder="bookingWithoutOrder_".$i;
			$cboTestFor="cboTestFor_".$i;
			$testItemId="testItemId_".$i;
			$remarks="remarks_".$i;
			$hideTransData="hideTransData_".$i;
			if(str_replace("'","",$$testItemId)=="" || str_replace("'","",$$testItemId)=="undefined")
			{
				$$testItemId=0;
			}
			if(str_replace("'","",$$itemProdId)=='undefined')
			{
				$$itemProdId=0;
			}
			
			if(str_replace("'", '',$cbo_pi_basis_id)==1 && str_replace("'", '',$cbo_goods_rcv_status)==1 && (str_replace("'", '',$cbo_item_category_id)!=12 && str_replace("'", '',$cbo_item_category_id)!=25 && str_replace("'", '',$cbo_item_category_id)!=31))
			{
				//echo "10** jahid".$$hideTransData;die;
				$hideTransData_arr=explode("__",str_replace("'","",$$hideTransData));
				foreach($hideTransData_arr as $hide_trans_val)
				{
					$hide_trans_val_arr=explode("_",$hide_trans_val);
					$trans_id=$hide_trans_val_arr[0];
					$trans_qnty=$hide_trans_val_arr[1];
					$trans_amt=$hide_trans_val_arr[2];
					$prod_id=$hide_trans_val_arr[3];
					
					//echo "10**".$trans_id;die;
					
					$allWoId.=str_replace("'","",$$workOrderId).",";
					$wotransId=str_replace("'","",$trans_id);
					$trans_qty_check_arr[$trans_id]=str_replace("'","",$trans_qnty);
					if($woDtlsTrsansId=='') $woDtlsTrsansId=$trans_id; else $woDtlsTrsansId.=','.$trans_id;
					
					$perc=(str_replace("'","",$trans_amt)/$txt_total_amount)*100;
					$net_pi_amount=($perc*$txt_total_amount_net)/100;
					
					if($net_pi_amount>0 && str_replace("'","",$trans_qnty)>0)
					{
						$net_pi_rate=$net_pi_amount/str_replace("'","",$trans_qnty);
						$net_pi_rate=number_format($net_pi_rate,$dec_place[3],'.','');
					}
					else
					{
						$net_pi_rate=0;
					}
						
					
					if($cbo_currency_id==1)
						$net_pi_amount=number_format($net_pi_amount,$dec_place[4],'.','');
					else
						$net_pi_amount=number_format($net_pi_amount,$dec_place[5],'.','');
							
					if(str_replace("'","",$$colorName)!="")
					{ 
						if (!in_array(str_replace("'","",$$colorName),$new_array_color))
						{
							$color_id = return_id( str_replace("'","",$$colorName), $color_library, "lib_color", "id,color_name","167");  
							$new_array_color[$color_id]=str_replace("'","",$$colorName);
						}
						else $color_id =  array_search(str_replace("'","",$$colorName), $new_array_color); 
					}
					else
					{
						$color_id=0;
					}
					
					if($cbo_item_category_id==4 || $cbo_item_category_id==12)
					{
						if(str_replace("'","",$$sizeName)!="")
						{
							if (!in_array(str_replace("'","",$$sizeName),$new_array_size))
							{
							  $size_id = return_id( str_replace("'","",$$sizeName), $size_library, "lib_size", "id,size_name","167");  
							  $new_array_size[$size_id]=str_replace("'","",$$sizeName);
							}
							else $size_id =  array_search(str_replace("'","",$$sizeName), $new_array_size); 
						}
						else
						{
							$size_id=0;
						}
						
						if(str_replace("'","",$$item_color)!="")
						{
							if (!in_array(str_replace("'","",$$item_color),$new_array_color))
							{
								$item_color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","167");  
								$new_array_color[$item_color_id]=str_replace("'","",$$item_color);
							}
							else $item_color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
						}
						else
						{
							$item_color_id=0;
						}
					}
					
					
					//echo "11**".$wo_qnty_arr[$trans_id]."==".$previous_pi_qty[$trans_id]."==".$prev_retun_qnty[$trans_id];die;
					if(str_replace("'", '',$cbo_pi_basis_id)==1)
					{
						if(str_replace("'", '',$cbo_item_category_id)==4)
						{
							if($wo_qnty_arr[$trans_id]>$prev_retun_qnty[str_replace("'","",$$workOrderId)][$prod_id])
							{
								$bal_wo_qnty=($wo_qnty_arr[$trans_id]-($previous_pi_qty[$trans_id]+$prev_retun_qnty[str_replace("'","",$$workOrderId)][$prod_id]));
								$prev_retun_qnty[str_replace("'","",$$workOrderId)][$prod_id]=0;
							}
							else
							{
								$bal_wo_qnty=($wo_qnty_arr[$trans_id]-($previous_pi_qty[$trans_id]+$wo_qnty_arr[$trans_id]));
								$prev_retun_qnty[str_replace("'","",$$workOrderId)][$prod_id]=$prev_retun_qnty[str_replace("'","",$$workOrderId)][$prod_id]-$wo_qnty_arr[$trans_id];
								$previous_pi_qty[$trans_id]+$prev_retun_qnty[str_replace("'","",$$workOrderId)][$prod_id]=$previous_pi_qty[$trans_id]+$prev_retun_qnty[str_replace("'","",$$workOrderId)][$prod_id]-$wo_qnty_arr[$trans_id];
							}
						}
						else
						{
							$bal_wo_qnty=($wo_qnty_arr[$trans_id]-($previous_pi_qty[$trans_id]+$prev_retun_qnty[$trans_id]));
						}
						
						$bal_wo_qnty=number_format($bal_wo_qnty,2,'.','');
						
						if($trans_qnty>$bal_wo_qnty)
						{
							//echo "11**$trans_qnty==$bal_wo_qnty==$wo_qnty_arr[$trans_id]==$previous_pi_qty[$trans_id]==".$prev_retun_qnty[str_replace("'","",$$workOrderId)][$prod_id];die;
							echo "11**PI Quantity Not Allow Over Balance Quantity";die;
						}
					}
					
					
					
					//if ($data_array!="") $data_array.=",";
					
					$data_array[$id]="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$trans_id)."','".str_replace("'","",$$bookingWithoutOrder)."','".str_replace("'","",$$determinationId)."','".str_replace("'","",$$itemProdId)."','".str_replace("'","",$$itemgroupid)."','".str_replace("'","",$$itemdescription)."','".str_replace("'","",$color_id)."','".str_replace("'","",$size_id)."','".str_replace("'","",$$itemSize)."','".str_replace("'","",$$countName)."','".str_replace("'","",$$yarnCompositionItem1)."','".str_replace("'","",$$yarnCompositionPercentage1)."','".str_replace("'","",$$yarnCompositionItem2)."','".str_replace("'","",$$yarnCompositionPercentage2)."','".str_replace("'","",$$composition)."','".str_replace("'","",$$construction)."','".str_replace("'","",$$yarnType)."','".str_replace("'","",$$gsm)."','".str_replace("'","",$$diawidth)."','".str_replace("'","",$$weight)."','".str_replace("'","",$item_color_id)."','".str_replace("'","",$$uom)."','".str_replace("'","",$trans_qnty)."','".str_replace("'","",$$rate)."','".$trans_amt."','".$net_pi_rate."','".$net_pi_amount."','".str_replace("'","",$$servicetype)."','".str_replace("'","",$$brandSupRef)."','".str_replace("'","",$$gmtsitem)."','".str_replace("'","",$$embellname)."','".str_replace("'","",$$embelltype)."','".str_replace("'","",$$lot)."','".str_replace("'","",$$yarnColor)."','".str_replace("'","",$$colorRange)."','".str_replace("'","",$$cboTestFor)."','".str_replace("'","",$$testItemId)."','".str_replace("'","",$$remarks)."',4,167,".$user_id.",'".$pc_date_time."')"; 
					
					$id=$id+1;
					 
				}
			}
			else
			{
				$allWoId.=str_replace("'","",$$workOrderId).",";
				$wotransId=str_replace("'","",$$workOrderDtlsId);
				$trans_qty_check_arr[$wotransId]=str_replace("'","",$$quantity);
				if($woDtlsTrsansId=='') $woDtlsTrsansId=$wotransId; else $woDtlsTrsansId.=','.$wotransId;
				
				$perc=(str_replace("'","",$$amount)/$txt_total_amount)*100;
				$net_pi_amount=($perc*$txt_total_amount_net)/100;
				$net_pi_rate=$net_pi_amount/str_replace("'","",$$quantity);
				$net_pi_rate=number_format($net_pi_rate,$dec_place[3],'.','');
				
				
				if($cbo_currency_id==1)
					$net_pi_amount=number_format($net_pi_amount,$dec_place[4],'.','');
				else
					$net_pi_amount=number_format($net_pi_amount,$dec_place[5],'.','');
						
				if(str_replace("'","",$$colorName)!="")
				{ 
					if (!in_array(str_replace("'","",$$colorName),$new_array_color))
					{
						$color_id = return_id( str_replace("'","",$$colorName), $color_library, "lib_color", "id,color_name","167");  
						$new_array_color[$color_id]=str_replace("'","",$$colorName);
					}
					else $color_id =  array_search(str_replace("'","",$$colorName), $new_array_color); 
				}
				else
				{
					$color_id=0;
				}
				
				if($cbo_item_category_id==4 || $cbo_item_category_id==12)
				{
					if(str_replace("'","",$$sizeName)!="")
					{
						if (!in_array(str_replace("'","",$$sizeName),$new_array_size))
						{
						  $size_id = return_id( str_replace("'","",$$sizeName), $size_library, "lib_size", "id,size_name","167");  
						  $new_array_size[$size_id]=str_replace("'","",$$sizeName);
						}
						else $size_id =  array_search(str_replace("'","",$$sizeName), $new_array_size); 
					}
					else
					{
						$size_id=0;
					}
					
					if(str_replace("'","",$$item_color)!="")
					{
						if (!in_array(str_replace("'","",$$item_color),$new_array_color))
						{
							$item_color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","167");  
							$new_array_color[$item_color_id]=str_replace("'","",$$item_color);
						}
						else $item_color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
					}
					else
					{
						$item_color_id=0;
					}
				}
				else
				{
					$size_id=0;
					$item_color_id=0;
				}
				
				if(str_replace("'", '',$cbo_pi_basis_id)==1)
				{
					
					if(str_replace("'", '',$cbo_goods_rcv_status)==1)
					{
						$bal_wo_qnty=($wo_qnty_arr[str_replace("'","",$$workOrderDtlsId)]-($previous_pi_qty[str_replace("'","",$$workOrderDtlsId)]+$prev_retun_qnty[str_replace("'","",$$workOrderDtlsId)]));
						
					}
					else
					{
						$bal_wo_qnty=($wo_qnty_arr[str_replace("'","",$$workOrderDtlsId)]-$previous_pi_qty[str_replace("'","",$$workOrderDtlsId)]);
						$wo_prev_qnty=$wo_qnty_arr[str_replace("'","",$$workOrderDtlsId)];
						$pi_prev_qnty=$previous_pi_qty[str_replace("'","",$$workOrderDtlsId)];
						
					}
					
					$trans_qnty=str_replace("'","",$$quantity);
					$bal_wo_qnty=number_format($bal_wo_qnty,2,'.','');
					$trans_qnty=number_format($trans_qnty,2,'.','');

					//echo "11** $trans_qnty=$bal_wo_qnty=$wo_prev_qnty=$pi_prev_qnty";die;
					
					if($trans_qnty>$bal_wo_qnty)
					{
						echo "11** PI Quantity Not Allow Over Balance Quantity";die;
					}
					
					
				}
				
				
				$data_array[$id]="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$bookingWithoutOrder)."','".str_replace("'","",$$determinationId)."','".str_replace("'","",$$itemProdId)."','".str_replace("'","",$$itemgroupid)."','".str_replace("'","",$$itemdescription)."','".str_replace("'","",$color_id)."','".str_replace("'","",$size_id)."','".str_replace("'","",$$itemSize)."','".str_replace("'","",$$countName)."','".str_replace("'","",$$yarnCompositionItem1)."','".str_replace("'","",$$yarnCompositionPercentage1)."','".str_replace("'","",$$yarnCompositionItem2)."','".str_replace("'","",$$yarnCompositionPercentage2)."','".str_replace("'","",$$composition)."','".str_replace("'","",$$construction)."','".str_replace("'","",$$yarnType)."','".str_replace("'","",$$gsm)."','".str_replace("'","",$$diawidth)."','".str_replace("'","",$$weight)."','".str_replace("'","",$item_color_id)."','".str_replace("'","",$$uom)."','".str_replace("'","",$$quantity)."','".str_replace("'","",$$rate)."',".$$amount.",'".$net_pi_rate."','".$net_pi_amount."','".str_replace("'","",$$servicetype)."','".str_replace("'","",$$brandSupRef)."','".str_replace("'","",$$gmtsitem)."','".str_replace("'","",$$embellname)."','".str_replace("'","",$$embelltype)."','".str_replace("'","",$$lot)."','".str_replace("'","",$$yarnColor)."','".str_replace("'","",$$colorRange)."','".str_replace("'","",$$cboTestFor)."','".str_replace("'","",$$testItemId)."','".str_replace("'","",$$remarks)."',4,167,".$user_id.",'".$pc_date_time."')"; 
				$id=$id+1;
			}
		}
		
		//echo "10**$woDtlsTrsansId";die;
		
		if(str_replace("'", '',$cbo_pi_basis_id)==1 && str_replace("'", '',$cbo_goods_rcv_status)==1 && str_replace("'", '',$cbo_item_category_id)!=12 && str_replace("'", '',$cbo_item_category_id)!=25 && str_replace("'", '',$cbo_item_category_id)!=31)
		{
			$allWoId=chop($allWoId,","); 
			if($allWoId=="") $allWoId=0;
			$prev_pi_qnty=return_library_array("Select b.work_order_dtls_id, sum(b.quantity) as pi_qnty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.goods_rcv_status=1 and a.item_category_id=$cbo_item_category_id and b.work_order_dtls_id in ($woDtlsTrsansId) and b.status_active=1 group by b.work_order_dtls_id","work_order_dtls_id","pi_qnty");
			
			$prev_retun_qnty=return_library_array("select b.recv_trans_id, sum(b.issue_qnty) as qnty 
			from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.issue_trans_id and a.transaction_type=3 and b.recv_trans_id in($all_dtls_id) and b.status_active=1 group by recv_trans_id","recv_trans_id","qnty");
			
			$field_array_trans_update="pi_is_lock*updated_by*update_date";
			$sql_trns="Select id, pi_wo_batch_no, prod_id, order_qnty as order_qnty from inv_transaction where id in ($woDtlsTrsansId) and status_active=1 order by id";
			
			//echo "10**"; print_r($prev_retun_qnty); die;
			//echo "10**"; echo $prev_retun_qnty[8278][9228]; die;
			
			$sql_trns_res = sql_select($sql_trns);
			foreach($sql_trns_res as $row)
			{
				$pi_trans_qty=number_format(($trans_qty_check_arr[$row[csf('id')]]),2,'.','');
				
				if($row[csf('order_qnty')]>$prev_retun_qnty[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]])
				{
					$order_trans_qty=number_format(($row[csf('order_qnty')]-($prev_retun_qnty[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]]+$prev_pi_qnty[$row[csf('id')]])),2,'.','');
					$prev_rtn+=$prev_retun_qnty[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]];
					$prev_retun_qnty[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]]=0;
				}
				else
				{
					$order_trans_qty=number_format(($row[csf('order_qnty')]-($row[csf('order_qnty')]+$prev_pi_qnty[$row[csf('id')]])),2,'.','');
					$prev_retun_qnty[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]]=$prev_retun_qnty[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]]-$row[csf('order_qnty')];
					$prev_rtn+=$prev_retun_qnty[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]];
				}
				
				if($pi_trans_qty>=$order_trans_qty)
				{
					$is_lock=1;
					//echo $is_lock.'A';
				}
				else
				{
				   $is_lock=0;	
				}
				$test_lock3.=$pi_trans_qty."=".$order_trans_qty."=".$is_lock."=".$prev_rtn.",";
				$test_lock2.=$row[csf('id')]."=".$trans_qty_check_arr[$row[csf('id')]]."=".$row[csf('order_qnty')]."=".$prev_retun_qnty[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]]."=".$prev_pi_qnty[$row[csf('id')]]."=".$is_lock.",";
				$test_lock.=$row[csf('id')]."= $order_trans_qty = $pi_trans_qty = ".$is_lock.",";
				$trans_id_arr[]=$row[csf('id')];
				$data_array_trans_update[$row[csf('id')]]=explode("*",($is_lock."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}
		
		
		//echo "10**".$test_lock3;die;
		//echo "5**insert into com_pi_item_details (".$field_array.") Values ".$data_array."";die;
		//echo "10**";print_r($data_array);die;
		$data_set=array_chunk($data_array,200);
		foreach( $data_set as $setRows)
		{
			//echo "5** insert into com_pi_item_details ($field_array) values ".implode(",",$setRows);die;
			$rID=sql_insert("com_pi_item_details",$field_array,implode(",",$setRows),0);
			
			//echo "5**".$rID;die;
			//$rID=sql_insert("com_pi_item_details",$field_array,$data_array,0);
			if($rID==1) $flag=1; //else $flag=0;
			else if($rID==0) 
			{
				$flag=0;
				oci_rollback($con); 
				echo "10**"; die;
			}
		}
		
		//if($rID) $flag=1; else $flag=0; execute_query
		
		$rID2=sql_update("com_pi_master_details",$field_array_update,$data_array_update,"id",$update_id,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}  
		if(count($data_array_trans_update)>0)
		{
			$rID3=execute_query(bulk_update_sql_statement("inv_transaction", "id",$field_array_trans_update,$data_array_trans_update,$trans_id_arr ));
			//echo "10**".$rID3;die;
			if($rID3) $flag=1; else $flag=20;
		}
		
		//echo "10**jahid**".$rID."**".$rID2."**".$rID3;die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'", '', $update_id)."**1";
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
				echo "0**".str_replace("'", '', $update_id)."**1";
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
		
		$cbo_item_category_id=str_replace("'", '',$cbo_item_category_id);
		$cbo_goods_rcv_status=str_replace("'", '',$cbo_goods_rcv_status);
		
		$sql_attach=sql_select("select a.lc_number, a.lc_value from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and b.pi_id='$update_id' and b.status_active=1 and b.is_deleted=0 group by a.id,a.lc_number, a.lc_value");
		if(count($sql_attach)>0)
		{
			$txt_total_amount_net=number_format(str_replace("'","",$txt_total_amount_net),2,".","");
			$lc_number=$sql_attach[0][csf('lc_number')];
			$lc_value=number_format($sql_attach[0][csf('lc_value')],2,".","");
			if($txt_total_amount_net > $lc_value)
			{
				echo "14** PI Value Not Allow Over LC Value, LC Number : ".$lc_number."**1"; 
				die;
			}
				
		}
		
		
		
		$sql_app=sql_select("select approved from com_pi_master_details where id='$update_id' and approved=1");
		if(count($sql_app)>0)
		{
			echo "16**1**1"; 
			die;	
		}
		
		$field_array_update="pi_id*work_order_no*work_order_id*work_order_dtls_id*booking_without_order*determination_id*item_prod_id*item_group*item_description*color_id*size_id*item_size*count_name*yarn_composition_item1*yarn_composition_percentage1*yarn_composition_item2*yarn_composition_percentage2*fabric_composition*fabric_construction*yarn_type*gsm*dia_width*weight*item_color*uom*quantity*rate*amount*net_pi_rate*net_pi_amount*service_type*brand_supplier*gmts_item_id*embell_name*embell_type*lot_no*yarn_color*color_range*test_for*test_item_id*remarks*item_category_id*entry_form*updated_by*update_date";
			
		$field_array="id,pi_id,work_order_no,work_order_id,work_order_dtls_id,booking_without_order,determination_id,item_prod_id,item_group,item_description,color_id,size_id,item_size,count_name,yarn_composition_item1,yarn_composition_percentage1,yarn_composition_item2,yarn_composition_percentage2,fabric_composition,fabric_construction,yarn_type,gsm,dia_width,weight,item_color,uom,quantity,rate,amount,net_pi_rate,net_pi_amount,service_type,brand_supplier,gmts_item_id,embell_name,embell_type,lot_no,yarn_color,color_range,test_for,test_item_id,remarks,item_category_id,entry_form,inserted_by,insert_date"; 
		
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
		
		$all_dtls_id="";
		for($i=1;$i<=$total_row;$i++)
		{
			$workOrderId="hideWoId_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$hideTransData="hideTransData_".$i;
			$all_WoId.=str_replace("'","",$$workOrderId).",";
			if(str_replace("'", '',$cbo_pi_basis_id)==1 && str_replace("'", '',$cbo_goods_rcv_status)==1 && (str_replace("'", '',$cbo_item_category_id)!=12 && str_replace("'", '',$cbo_item_category_id)!=25 && str_replace("'", '',$cbo_item_category_id)!=31))
			{
				$hideTransData_arr=explode("__",str_replace("'","",$$hideTransData));
				foreach($hideTransData_arr as $hide_trans_val)
				{
					$hide_trans_val_arr=explode("_",$hide_trans_val);
					$all_dtls_id.=$hide_trans_val_arr[0].",";
				}
			}
			else
			{
				$all_dtls_id.=str_replace("'","",$$workOrderDtlsId).",";
			}
		}
		$all_dtls_id=chop($all_dtls_id,",");
		$all_WoId=implode(",",array_unique(explode(",",chop($all_WoId,","))));
		
		if(str_replace("'", '',$cbo_goods_rcv_status)==2)
		{
			if($all_dtls_id=="") $all_dtls_id=0;
			$booking_id_cond=""; $bokIds_cond=""; $all_dtls_arr=explode(",",$all_dtls_id);
			if($db_type==2 && count($all_dtls_arr)>999)
			{
				$booking_id_chunk_arr=array_chunk($all_dtls_arr,999) ;
				foreach($booking_id_chunk_arr as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);	
					$bokIds_cond.=" b.work_order_dtls_id in($chunk_arr_value) or ";	
				}
				
				$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
				//echo $booking_id_cond;die;
			}
			else
			{
				$booking_id_cond=" and b.work_order_dtls_id in($all_dtls_id)";	 
			}
			$previous_pi_qty=return_library_array("select b.work_order_dtls_id as id, sum(b.quantity) as qnty 
			from com_pi_master_details a, com_pi_item_details b 
			where a.id=b.pi_id and a.item_category_id=".str_replace("'", '',$cbo_item_category_id)." and a.goods_rcv_status<>1 and b.status_active=1 $booking_id_cond and a.id not in($update_id) group by b.work_order_dtls_id","id","qnty");
			
			if(str_replace("'", '',$txt_order_type)==1)
			{
				$wo_qnty_arr=return_library_array("select id as wo_trim_booking_dtls_id, trim_qty as qnty from wo_non_ord_samp_booking_dtls where status_active=1 and id in($all_dtls_id)","wo_trim_booking_dtls_id","qnty");
			}
			else
			{
				$wo_qnty_arr=return_library_array("select wo_trim_booking_dtls_id, sum(cons) as qnty from wo_trim_book_con_dtls where status_active=1 and wo_trim_booking_dtls_id in($all_dtls_id) and cons>0 group by wo_trim_booking_dtls_id","wo_trim_booking_dtls_id","qnty");
			}
		}
		
		$id = return_next_id( "id","com_pi_item_details", 1 );
		$data_array==""; $data_array_update=array();
		$woDtlsTrsansId='';$trans_qty_check_arr=array();
		for($i=1;$i<=$total_row;$i++)
		{
			$updateIdDtls="updateIdDtls_".$i;
			$colorName="colorName_".$i; 
			$sizeName="sizeName_".$i;
			$countName="countName_".$i;
			$yarnCompositionItem1="yarnCompositionItem1_".$i;
			$yarnCompositionPercentage1="yarnCompositionPercentage1_".$i;
			$yarnCompositionItem2="yarnCompositionItem2_".$i;
			$yarnCompositionPercentage2="yarnCompositionPercentage2_".$i;			 
			$yarnType="yarnType_".$i;
			$uom="uom_".$i;
			$quantity="quantity_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			$workOrderNo="workOrderNo_".$i;
			$workOrderId="hideWoId_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$itemProdId="itemProdId_".$i;
			$determinationId="hideDeterminationId_".$i; 
			$construction="construction_".$i; 
			$composition="composition_".$i;
			$gsm="gsm_".$i;
			$diawidth="diawidth_".$i;
			$weight="weight_".$i;
			$itemgroupid="itemgroupid_".$i;
			$itemdescription="itemdescription_".$i;
			$servicetype="servicetype_".$i;
			$item_color="itemColor_".$i;
			$itemSize="itemSize_".$i;
			$brandSupRef="brandSupRef_".$i;
			$lot="lot_".$i;
			$yarnColor="yarnColor_".$i;
			$colorRange="colorRange_".$i;
			$gmtsitem="gmtsitem_".$i;
			$embellname="embellname_".$i;
			$embelltype="embelltype_".$i;
			$bookingWithoutOrder="bookingWithoutOrder_".$i;
			$cboTestFor="cboTestFor_".$i;
			$testItemId="testItemId_".$i;
			$remarks="remarks_".$i;
			
			if(str_replace("'","",$$testItemId)=='undefined')
			{
				$$testItemId=0;
			}
			if(str_replace("'","",$$itemProdId)=='undefined')
			{
				$$itemProdId=0;
			}
			
			$wotransId=str_replace("'","",$$workOrderDtlsId);
			$allWoId.=str_replace("'","",$$workOrderId).",";
			$trans_qty_check_arr[$wotransId]=str_replace("'","",$$quantity);
			if($woDtlsTrsansId=='') $woDtlsTrsansId=$wotransId;else $woDtlsTrsansId.=','.$wotransId;
						
			$perc=(str_replace("'","",$$amount)/$txt_total_amount)*100;
			$net_pi_amount=($perc*$txt_total_amount_net)/100;
			
			
			$net_pi_rate=$net_pi_amount/str_replace("'","",$$quantity);
			$net_pi_rate=number_format($net_pi_rate,$dec_place[3],'.','');
			
			
			if($cbo_currency_id==1)
				$net_pi_amount=number_format($net_pi_amount,$dec_place[4],'.','');
			else
				$net_pi_amount=number_format($net_pi_amount,$dec_place[5],'.','');
					
			if(str_replace("'","",$$colorName)!="")
			{ 
				if (!in_array(str_replace("'","",$$colorName),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$colorName), $color_library, "lib_color", "id,color_name","167");  
					$new_array_color[$color_id]=str_replace("'","",$$colorName);
				}
				else $color_id =  array_search(str_replace("'","",$$colorName), $new_array_color); 
			}
			else
			{
				$color_id=0;
			}
			
			if($cbo_item_category_id==4 || $cbo_item_category_id==12)
			{
				if(str_replace("'","",$$sizeName)!="")
				{
					if (!in_array(str_replace("'","",$$sizeName),$new_array_size))
					{
					  $size_id = return_id( str_replace("'","",$$sizeName), $size_library, "lib_size", "id,size_name","167");  
					  $new_array_size[$size_id]=str_replace("'","",$$sizeName);
					}
					else $size_id =  array_search(str_replace("'","",$$sizeName), $new_array_size); 
				}
				else
				{
					$size_id=0;
				}
				
				if(str_replace("'","",$$item_color)!="")
				{
					if (!in_array(str_replace("'","",$$item_color),$new_array_color))
					{
						$item_color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","167");  
						$new_array_color[$item_color_id]=str_replace("'","",$$item_color);
					}
					else $item_color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
				}
				else
				{
					$item_color_id=0;
				}
			}
			
			
			if(str_replace("'", '',$cbo_goods_rcv_status)==2 && str_replace("'", '',$cbo_pi_basis_id)==1)
			{
				
				$bal_wo_qnty=($wo_qnty_arr[str_replace("'","",$$workOrderDtlsId)]-$previous_pi_qty[str_replace("'","",$$workOrderDtlsId)]);
					
				$trans_qnty=str_replace("'","",$$quantity);
				$bal_wo_qnty=number_format($bal_wo_qnty,2,'.','');
				$trans_qnty=number_format($trans_qnty,2,'.','');
				//echo "11** $trans_qnty=$bal_wo_qnty=$wo_prev_qnty=$pi_prev_qnty";die;
				
				if($trans_qnty>$bal_wo_qnty)
				{
					echo "11**PI Quantity Not Allow Over Balance Quantity";die;
				}
			}
			
			
			
			if(str_replace("'","",$$updateIdDtls)!="")
			{
				$all_updateDtls_id.=str_replace("'",'',$$updateIdDtls).",";
				$id_arr[]=str_replace("'",'',$$updateIdDtls);
				$data_array_update[str_replace("'",'',$$updateIdDtls)] = explode("*",($update_id."*'".str_replace("'","",$$workOrderNo)."'*'".str_replace("'","",$$workOrderId)."'*'".str_replace("'","",$$workOrderDtlsId)."'*'".str_replace("'","",$$bookingWithoutOrder)."'*'".str_replace("'","",$$determinationId)."'*'".str_replace("'","",$$itemProdId)."'*'".str_replace("'","",$$itemgroupid)."'*'".str_replace("'","",$$itemdescription)."'*'".str_replace("'","",$color_id)."'*'".str_replace("'","",$size_id)."'*'".str_replace("'","",$$itemSize)."'*'".str_replace("'","",$$countName)."'*'".str_replace("'","",$$yarnCompositionItem1)."'*'".str_replace("'","",$$yarnCompositionPercentage1)."'*'".str_replace("'","",$$yarnCompositionItem2)."'*'".str_replace("'","",$$yarnCompositionPercentage2)."'*'".str_replace("'","",$$composition)."'*'".str_replace("'","",$$construction)."'*'".str_replace("'","",$$yarnType)."'*'".str_replace("'","",$$gsm)."'*'".str_replace("'","",$$diawidth)."'*'".str_replace("'","",$$weight)."'*'".str_replace("'","",$item_color_id)."'*'".str_replace("'","",$$uom)."'*'".str_replace("'","",$$quantity)."'*'".str_replace("'","",$$rate)."'*".$$amount."*'".$net_pi_rate."'*'".$net_pi_amount."'*'".str_replace("'","",$$servicetype)."'*'".str_replace("'","",$$brandSupRef)."'*'".str_replace("'","",$$gmtsitem)."'*'".str_replace("'","",$$embellname)."'*'".str_replace("'","",$$embelltype)."'*'".str_replace("'","",$$lot)."'*'".str_replace("'","",$$yarnColor)."'*'".str_replace("'","",$$colorRange)."'*'".str_replace("'","",$$cboTestFor)."'*'".str_replace("'","",$$testItemId)."'*'".str_replace("'","",$$remarks)."'*4*167*".$user_id."*'".$pc_date_time."'"));
			}
			else
			{
				if($data_array!="") $data_array.=","; 		
						
				$data_array .="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$bookingWithoutOrder)."','".str_replace("'","",$$determinationId)."','".str_replace("'","",$$itemProdId)."','".str_replace("'","",$$itemgroupid)."','".str_replace("'","",$$itemdescription)."','".str_replace("'","",$color_id)."','".str_replace("'","",$size_id)."','".str_replace("'","",$$itemSize)."','".str_replace("'","",$$countName)."','".str_replace("'","",$$yarnCompositionItem1)."','".str_replace("'","",$$yarnCompositionPercentage1)."','".str_replace("'","",$$yarnCompositionItem2)."','".str_replace("'","",$$yarnCompositionPercentage2)."','".str_replace("'","",$$composition)."','".str_replace("'","",$$construction)."','".str_replace("'","",$$yarnType)."','".str_replace("'","",$$gsm)."','".str_replace("'","",$$diawidth)."','".str_replace("'","",$$weight)."','".str_replace("'","",$item_color_id)."','".str_replace("'","",$$uom)."','".str_replace("'","",$$quantity)."','".str_replace("'","",$$rate)."',".$$amount.",'".$net_pi_rate."','".$net_pi_amount."','".str_replace("'","",$$servicetype)."','".str_replace("'","",$$brandSupRef)."','".str_replace("'","",$$gmtsitem)."','".str_replace("'","",$$embellname)."','".str_replace("'","",$$embelltype)."','".str_replace("'","",$$lot)."','".str_replace("'","",$$yarnColor)."','".str_replace("'","",$$colorRange)."','".str_replace("'","",$$cboTestFor)."','".str_replace("'","",$$testItemId)."','".str_replace("'","",$$remarks)."',4,167,". $user_id.",'".$pc_date_time."')"; 
				$id=$id+1;
			}
		}
		
		//echo "10**".print_r($data_array_update)."**1";die;
		
		$allWoId=chop($allWoId,",");
		$all_updateDtls_id=chop($all_updateDtls_id,",");
		
		if(str_replace("'", '',$cbo_pi_basis_id)==1 && str_replace("'", '',$cbo_goods_rcv_status)==1 && str_replace("'", '',$cbo_item_category_id)!=31)
		{
			$prev_pi_qnty=return_library_array("Select b.work_order_dtls_id, sum(b.quantity) as pi_qnty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.goods_rcv_status=1 and a.item_category_id=$cbo_item_category_id and b.work_order_dtls_id in ($woDtlsTrsansId) and id not in($all_updateDtls_id) and b.status_active=1 group by b.work_order_dtls_id","work_order_dtls_id","pi_qnty");
			
			$prev_retun_sql="select booking_id, prod_id, trans_id, issue_qnty from inv_trims_issue_dtls where booking_id>0 and booking_id in($allWoId)";
			$prev_retun_sql_result=sql_select($prev_retun_sql);
			foreach($prev_retun_sql_result as $row)
			{
				$prev_retun_qnty[$row[csf("booking_id")]][$row[csf("prod_id")]]+=$row[csf("issue_qnty")]/$conver_factor[$row[csf("prod_id")]];
			}
			
			$field_array_trans_update="pi_is_lock*updated_by*update_date";
			$sql_trns="Select id, pi_wo_batch_no, order_qnty as order_qnty from inv_transaction where id in ($woDtlsTrsansId) and item_category=$cbo_item_category_id order by id";
						
			$sql_trns_res = sql_select($sql_trns);
			foreach($sql_trns_res as $row)
			{
				
				$pi_trans_qty=number_format(($trans_qty_check_arr[$row[csf('id')]]),2,'.','');
				if(str_replace("'","",$cbo_item_category_id)==4)
				{
					if($row[csf('order_qnty')]>$prev_retun_qnty[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]])
					{
						$order_trans_qty=number_format(($row[csf('order_qnty')]-($prev_retun_qnty[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]]+$prev_pi_qnty[$row[csf('id')]])),2,'.','');
						$prev_retun_qnty[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]]=0;
					}
					else
					{
						$order_trans_qty=number_format(($row[csf('order_qnty')]-($row[csf('order_qnty')]+$prev_pi_qnty[$row[csf('id')]])),2,'.','');
						$prev_retun_qnty[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]]=$prev_retun_qnty[$row[csf('pi_wo_batch_no')]][$row[csf('prod_id')]]-$row[csf('order_qnty')];
					}
				}
				
				
				if($order_trans_qty>=$pi_trans_qty)
				{
					$is_lock=1;
					//echo $is_lock.'A';
				}
				else
				{
				   $is_lock=0;	
				}
				
				$trans_id_arr[]=$row[csf('id')];
				$data_array_trans_update[$row[csf('id')]]=explode("*",($is_lock."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}

	 	$flag=1;
		$rID=$rID2=$rID3=$rID5=$rID4=true;
		if(str_replace("'", '',$cbo_goods_rcv_status)==2)
		{
			if(count($data_array_update)>0)
			{
				$rID=execute_query(bulk_update_sql_statement( "com_pi_item_details", "id", $field_array_update, $data_array_update, $id_arr ));
				//$rID=bulk_update_sql_statement( "com_pi_item_details", "id", $field_array_update, $data_array_update, $id_arr ); execute_query 
				//echo "6**$rID**1";die;
				if($rID) $flag=1; else $flag=0;
			}
			
			if($data_array!="")
			{
				$rID2=sql_insert("com_pi_item_details",$field_array,$data_array,0);
				if($flag==1) 
				{
					if($rID2) $flag=1; else $flag=0; 
				} 
			}
			
			if($txt_deleted_id!="")
			{
				$field_array_status="updated_by*update_date*status_active*is_deleted";
				$data_array_status=$user_id."*'".$pc_date_time."'*0*1";
		
				$rID3=sql_multirow_update("com_pi_item_details",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
				if($flag==1) 
				{
					if($rID3) $flag=1; else $flag=0; 
				} 
			}
			if(count($data_array_trans_update)>0)
			{
				$rID5=execute_query(bulk_update_sql_statement("inv_transaction", "id",$field_array_trans_update,$data_array_trans_update,$trans_id_arr ));
				if($rID5) $flag=1; else $flag=0;
			}
		}

		$rID4=sql_update("com_pi_master_details",$field_array_update2,$data_array_update2,"id",$update_id,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		//echo "10**==$operation==jahid**1";die;
		//echo "6**=$rID=$rID2=$rID3=$rID5=$rID4=$flag**1";die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$sql_app=sql_select("select approved from com_pi_master_details where id='$update_id' and approved=1");
		if(count($sql_app)>0)
		{
			echo "16**1**1";
			disconnect($con); 
			die;	
		}
		
		$sql_attach=sql_select("select a.lc_number from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and b.pi_id='$update_id' and b.status_active=1 and b.is_deleted=0 group by a.id, a.lc_number");
		if(count($sql_attach)>0)
		{
			$lc_number=$sql_attach[0][csf('lc_number')];
			echo "14**This PI is Attached With BTB LC No- '".$lc_number."'. So You can not delete it**1";
			disconnect($con);
			die;	
		}
		
		if(str_replace("'","",$cbo_goods_rcv_status)!=1)
		{
			$sql_attach_mrr=sql_select("select a.recv_number from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.pi_wo_batch_no=$update_id and b.receive_basis=1 and b.status_active=1 and b.is_deleted=0 group by a.id, a.recv_number");
			if(count($sql_attach_mrr)>0)
			{
				$recv_number='';
				foreach($sql_attach_mrr as $row)
				{
					$recv_number.=$row[csf('recv_number')].",";
				}
				
				echo "14**This PI is Attached With MRR No- '".chop($recv_number,',')."'. So You can not delete it**1";
				disconnect($con); 
				die;	
			}
		}
		
		/*$cbo_item_category_id=str_replace("'", '', $cbo_item_category_id);
		$recv_arr=array();
		if($cbo_item_category_id==4)
		{
			$sql_recv="select b.item_group_id, b.item_description, b.brand_supplier, b.order_uom, b.gmts_color_id, b.item_color, b.gmts_size_id, b.item_size, b.rate, b.receive_qnty from inv_receive_master a, inv_trims_entry_dtls b where a.id=b.mst_id and a.booking_id=$update_id and a.receive_basis=1 and a.entry_form=24 and b.status_active=1 and b.is_deleted=0 and b.receive_qnty>0";
			$prevData=sql_select($sql_prev);
			foreach($prevData as $rowP)
			{
				$recv_arr[$rowP[csf('item_group_id')]][$rowP[csf('item_description')]][$rowP[csf('brand_supplier')]][$rowP[csf('order_uom')]][$rowP[csf('gmts_color_id')]][$rowP[csf('item_color')]][$rowP[csf('gmts_size_id')]][$rowP[csf('item_size')]][$rowP[csf('rate')]]=$rowP[csf('receive_qnty')];
			}
			
			$pi_qty_arr=array();
			$sql_pi="select b.item_group, b.item_description, b.brand_supplier, b.uom, b.color_id, b.item_color, b.size_id, b.item_size, b.rate, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 and b.id in($txt_deleted_id) group by b.color_id, b.item_group, b.item_description, b.size_id, b.item_color, b.item_size, b.uom, b.rate, b.brand_supplier";
			$prevData=sql_select($sql_pi);
			foreach($prevData as $rowP)
			{
				$pi_qty_arr[$rowP[csf('item_group')]][$rowP[csf('item_description')]][$rowP[csf('brand_supplier')]][$rowP[csf('uom')]][$rowP[csf('color_id')]][$rowP[csf('item_color')]][$rowP[csf('size_id')]][$rowP[csf('item_size')]][$rowP[csf('rate')]]=$rowP[csf('qty')];
			}
		}*/
		
		$field_array_update_mst="total_amount*upcharge*discount*net_total_amount";
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
		
		$data_array_update_mst=$txt_total_amount."*'".$txt_upcharge."'*'".$txt_discount."'*".$txt_total_amount_net;
		
		$field_array_update="quantity*rate*amount*net_pi_rate*net_pi_amount*updated_by*update_date";
		$woDtlsTrsansId='';$trans_qty_check_arr=array();
		for($i=1;$i<=$total_row;$i++)
		{
			$updateIdDtls="updateIdDtls_".$i;
			$quantity="quantity_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$wotransId=str_replace("'","",$$workOrderDtlsId);
			$trans_qty_check_arr[$wotransId]['qty']=str_replace("'","",$$quantity);
			if($wotransId!="")
			{
				$woDtlsTrsansId.=$wotransId.",";
			}
			//if($woDtlsTrsansId=='') $woDtlsTrsansId=$wotransId; else $woDtlsTrsansId.=','.$wotransId;
			
			
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
				$data_array_update[str_replace("'",'',$$updateIdDtls)] = explode(",",($$quantity.",".$$rate.",".$$amount.",".$net_pi_rate.",".$net_pi_amount.",". $user_id.",'".$pc_date_time."'"));
			}
			
		}
		
		$woDtlsTrsansId= chop($woDtlsTrsansId,",");
		//echo '10**'.$woDtlsTrsansId."=".str_replace("'", '',$cbo_pi_basis_id);die;
		if(str_replace("'", '',$cbo_pi_basis_id)==1)
		{
			$field_array_trans_update="pi_is_lock*updated_by*update_date";
			$sql_trns="Select id, sum(order_qnty) as order_qnty from inv_transaction where id in ($woDtlsTrsansId)group by id order by id";
			//echo "10**".$sql_trns; die;
			$sql_trns_res = sql_select($sql_trns);
			foreach($sql_trns_res as $row)
			{
				$pi_trans_qty=$trans_qty_check_arr[$row[csf('id')]]['qty'];
				//echo $trans_qty_check_arr[$row[csf('id')]]['qty'];
				$order_trans_qty=$row[csf('order_qnty')];
				$is_lock=0;
				/*if($order_trans_qty>=$pi_trans_qty)
				{
					//$is_lock=1;
					$is_lock=0;
					//echo $is_lock.'A';
				}
				else
				{
				   //$is_lock=0;	
				   $is_lock=1;	
				}*/
				$trans_id_arr[]=$row[csf('id')];
				$data_array_trans_update[$row[csf('id')]]=explode("*",($is_lock."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				//echo "10**".$trans_id_arr[1].print_r($data_array_trans_update[$row[csf('id')]]); die;
			}
		}
		
		//
		//yes
		$flag=1;
		if($data_array_update!="")
		{
			$rID2=execute_query(bulk_update_sql_statement( "com_pi_item_details", "id", $field_array_update, $data_array_update, $id_arr ));
			if($rID2) $flag=1; else $flag=0;
			//echo '10**'.$field_array_update.'='.print_r($data_array_update).'__';			
			//$rID=bulk_update_sql_statement( "com_pi_item_details", "id", $field_array_update, $data_array_update, $id_arr );
		}
		
		if($txt_deleted_id!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$user_id."*'".$pc_date_time."'*0*1";
	
			$delete=sql_multirow_update("com_pi_item_details",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
			if($flag==1) 
			{
				if($delete) $flag=1; else $flag=0; 
			} 
		}
		
		$rID=sql_update("com_pi_master_details",$field_array_update_mst,$data_array_update_mst,"id",$update_id,1);
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		} 

		if($txt_total_amount>0) $button_status=1; else $button_status=0;
		
		if(str_replace("'", '',$cbo_item_category_id)!=31)
		{
	  		if(count($data_array_trans_update)>0)
	  		{
	  			$rID3=execute_query(bulk_update_sql_statement("inv_transaction", "id",$field_array_trans_update,$data_array_trans_update,$trans_id_arr ));
	  			if($rID3) $flag=1; else $flag=20;
	  		}
		}
		else
		{
			$flag=1;
		}
		 
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'", '', $update_id)."**$button_status";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);    
				echo "2**".str_replace("'", '', $update_id)."**$button_status";
			}
			else
			{
				oci_rollback($con); 
				echo "7**".str_replace("'", '', $update_id)."**1";
			}
		}
		
		disconnect($con);
		die;
	}
}

//---------------------------------------------- Start Pi Details -----------------------------------------------------------------------//

if( $action == 'pi_details' ) 
{	
	$data = explode( '_', $data );
	$pi_basis_id=$data[0];
	$item_category_id=$data[1];
	$type=$data[2];
	$pi_id=$data[3];
	$goods_rcv_status=$data[4];
	
	if($item_category_id!=0)
	{
	?>
    <table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
		<?
       	if($item_category_id==4)
		{
		 	?>
        	<thead>
				<? 
                if($pi_basis_id == 1) 
                { 
                	?>
                    <th>&nbsp;</th>
                    <th>WO</th>
                	<? 
                } 
                ?>
                <th class="must_entry_caption">Item Group</th>
                <th class="must_entry_caption">Item Description</th>
                <th>Brand/ Supp. Ref</th>
                <th>Gmts Color</th>
                <th>Gmts Size</th>
                <th class="must_entry_caption">Item Color</th>
                <th>Item Size</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
                <? 
                if( $pi_basis_id == 2 ) 
                { 
                	?>
                    <th></th>
                	<? 
                } 
                ?>
            </thead>
            <tbody>
       		<?
            if($pi_basis_id==2)
            {
                $disable="";
                $disable_status=0;
            }
            else
            {
				$disable="disabled='disabled'";
                $disable_status=1;
				if($goods_rcv_status==1) $disable_field="disabled='disabled'"; else $disable_field="";
                
            }
			
			if($goods_rcv_status==2)
            {
                
				$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, color_id, item_group, item_description, size_id, item_color, item_size, uom, quantity, rate, amount, brand_supplier, booking_without_order, item_prod_id from com_pi_item_details where pi_id='$pi_id' and status_active=1 and is_deleted=0" );
            }
            else
            {
				
				if($db_type==0)
				{
					$nameArray=sql_select( "select group_concat(id) as id, work_order_no, work_order_id, group_concat(work_order_dtls_id) as work_order_dtls_id, color_id, item_group, item_description, size_id, item_color, item_size, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, brand_supplier, booking_without_order, item_prod_id 
					from com_pi_item_details where pi_id='$pi_id' and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, color_id, item_group, item_description, size_id, item_color, item_size, uom, brand_supplier, booking_without_order, item_prod_id" );
				
				}
				else
				{
					
					$nameArray=sql_select( "select listagg(cast(id as varchar(4000)), ',') within group(order by id) as id, work_order_no, work_order_id, listagg(cast(work_order_dtls_id as varchar(4000)), ',') within group(order by work_order_dtls_id) as work_order_dtls_id, color_id, item_group, item_description, size_id, item_color, item_size, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, brand_supplier, booking_without_order, item_prod_id 
					from com_pi_item_details where pi_id='$pi_id' and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, color_id, item_group, item_description, size_id, item_color, item_size, uom, brand_supplier, booking_without_order, item_prod_id" );
				}
				
                
            }

			
			//echo "select id, work_order_no, work_order_id, work_order_dtls_id, color_id, item_group, item_description, size_id, item_color, item_size, uom, quantity, rate, amount, brand_supplier, booking_without_order from com_pi_item_details where pi_id='$pi_id' and status_active=1 and is_deleted=0";

			if($type==1 || count($nameArray)<1)
            {
				$tot_amnt=''; $upcharge=''; $discount=''; $tot_amnt_net=''; $txt_tot_row=0;
            	?>
                <tr class="general" id="row_1">
                    <? 
                    if( $pi_basis_id == 1 ) 
                    { 
                    ?>
                        <td>
                            <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                        </td>
                        <td>
                            <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                            <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                            <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                            <input type="hidden" name="hideTransData_1" id="hideTransData_1" value="" readonly />
                            <input type="hidden" name="hideProdId_1" id="hideProdId_1" value="" readonly />
                        </td>
                        <td> 
							 <?
                                //echo create_drop_down( "itemgroupid_1", 110, "SELECT id,item_name FROM lib_item_group WHERE item_category =$item_category_id AND status_active = 1 AND is_deleted = 0 ORDER BY item_name ASC",'id,item_name', 1, '-Select-',0,"get_php_form_data( this.value+'**'+'uom_1', 'get_uom', 'requires/pi_accessories_controller' );",$disable_status); 
                             ?>
                              <input type="text" name="itemgroupid_1" id="itemgroupid_1" class="text_boxes" value="" style="width:100px;" placeholder="" readonly <? echo $disable_status; ?> />  
                        </td>
                    <? 
                    }
					else
					{
						?>
                        <td> 
							 <?
                                echo create_drop_down( "itemgroupid_1", 110, "SELECT id,item_name FROM lib_item_group WHERE item_category =$item_category_id AND status_active = 1 AND is_deleted = 0 ORDER BY item_name ASC",'id,item_name', 1, '-Select-',0,"get_php_form_data( this.value+'**'+'uom_1', 'get_uom', 'requires/pi_accessories_controller' );",$disable_status); 
                             ?>  
                        </td>
                        <?
					}
                    ?>
                    
                    <td>
                        <input type="text" name="itemdescription_1" id="itemdescription_1" class="text_boxes" value="" style="width:130px" maxlength="200" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="brandSupRef_1" id="brandSupRef_1" class="text_boxes" value="" maxlength="150" style="width:80px" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" onFocus="add_auto_complete( 1 )" value="" style="width:70px" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="sizeName_1" id="sizeName_1" class="text_boxes" value="" onFocus="add_auto_complete( 1 )" style="width:60px;" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="itemColor_1" id="itemColor_1" class="text_boxes" value="" onFocus="add_auto_complete( 1 )" style="width:70px" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="itemSize_1" id="itemSize_1" class="text_boxes" value="" style="width:60px;" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <?
							if($pi_basis_id == 1) 
                			{
								?>
                                <input type="text" name="uom_1" id="uom_1" class="text_boxes" value="" style="width:50px;" placeholder="" readonly />
                                <?
							}
							else
							{
								echo create_drop_down( "uom_1", 60, $unit_of_measurement,'', 0, '',0,'',1);
							}
                                        
                        ?>		
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:45px;" onKeyUp="calculate_amount(1)" <? echo $disable; ?> />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" <? echo $disable; ?> readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                        <input type="hidden" name="bookingWithoutOrder_1" id="bookingWithoutOrder_1" readonly/>
                    </td>	
                    <? 
                    if( $pi_basis_id == 2 ) 
                    { 
                    ?>
                    <td width="65">
                        <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( 1 )" />
                        <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                    </td> 
                    <?
                    }
                    ?> 
                </tr>
            	<?
            }
			else
			{
				$item_group_arr=return_library_array( "SELECT id,item_name FROM lib_item_group WHERE item_category =$item_category_id AND status_active = 1 AND is_deleted = 0 ORDER BY item_name ASC",'id','item_name');
				
				$data_array=sql_select("select item_category_id,goods_rcv_status,total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_id'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')]; 
				$item_category_id=$data_array[0][csf('item_category_id')];  
				$goods_rcv_status=$data_array[0][csf('goods_rcv_status')]; 
				
				if( $pi_basis_id == 1 )
				{
					$wo_dtls_id=''; $wo_dtls_id2=''; $wo_dtls_id_all='';
					foreach ($nameArray as $row)
					{
						if($row[csf('booking_without_order')]==1)
						{
							$wo_dtls_id2.=$row[csf('work_order_dtls_id')].',';
						}
						else
						{
							$wo_dtls_id.=$row[csf('work_order_dtls_id')].',';
						}
						
						$wo_dtls_id_all.=$row[csf('work_order_dtls_id')].',';
					}
					$wo_dtls_id=chop($wo_dtls_id,',');
					$wo_dtls_id2=chop($wo_dtls_id2,',');
					$wo_dtls_id_all=chop($wo_dtls_id_all,',');
					
					if($goods_rcv_status==2)
					{
						if($wo_dtls_id!="")
						{
							$wo_qty_arr=array();
							if($db_type==0) $null_val="c.color_number_id,c.item_color,c.gmts_sizes,";
							else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,";

							$sql_prev="select a.booking_no, b.id, b.trim_group, b.uom, c.description, c.rate, $null_val c.item_size, c.brand_supplier, sum(c.cons) as qnty from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and b.id in($wo_dtls_id) group by a.booking_no, b.id, b.trim_group, b.uom, c.description, c.rate, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier";
							//$sql_prev="select a.booking_no, b.id, b.trim_group, b.uom, c.description, c.rate, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier, sum(c.cons) as qnty from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and b.id in($wo_dtls_id) group by a.booking_no, b.id, b.trim_group, b.uom, c.description, c.rate, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier";
							$prevData=sql_select($sql_prev);
							foreach($prevData as $rowP)
							{
								$wo_qty_arr[$rowP[csf('booking_no')]][$rowP[csf('id')]][$rowP[csf('color_number_id')]][$rowP[csf('trim_group')]][$rowP[csf('description')]][$rowP[csf('gmts_sizes')]][$rowP[csf('item_color')]][$rowP[csf('item_size')]][$rowP[csf('brand_supplier')]][$rowP[csf('uom')]][number_format($rowP[csf('rate')],2,'.','')]=$rowP[csf('qnty')];
							}
							//echo "<pre>";
							//print_r($wo_qty_arr);die;
							$prev_pi_qty_arr=array();
							$sql_prev="select b.work_order_no, b.work_order_dtls_id, b.color_id, b.item_group, b.item_description, b.size_id, b.item_color, b.item_size, b.uom, b.rate, b.brand_supplier, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id_all) group by b.work_order_no, b.work_order_dtls_id, b.color_id, b.item_group, b.item_description, b.size_id, b.item_color, b.item_size, b.uom, b.rate, b.brand_supplier";
							$prevData=sql_select($sql_prev);
							foreach($prevData as $rowP)
							{
								$prev_pi_qty_arr[$rowP[csf('work_order_no')]][$rowP[csf('work_order_dtls_id')]][$rowP[csf('color_id')]][$rowP[csf('item_group')]][$rowP[csf('item_description')]][$rowP[csf('size_id')]][$rowP[csf('item_color')]][$rowP[csf('item_size')]][$rowP[csf('brand_supplier')]][$rowP[csf('uom')]][$rowP[csf('rate')]]=$rowP[csf('qty')];
							}
							/*echo "<pre>";
							print_r($prev_pi_qty_arr);die;*/
							
							//$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id_all) group by b.work_order_dtls_id", "work_order_dtls_id", "qty");
							
							//$wo_qty_arr=return_library_array("select wo_trim_booking_dtls_id as id, sum(cons) as qty from wo_trim_book_con_dtls where wo_trim_booking_dtls_id in($wo_dtls_id) group by wo_trim_booking_dtls_id", "id", "qty");
						}
						
						if($wo_dtls_id2!="")
						{
							$wo_qty_arr2=return_library_array("select id, trim_qty as qty from wo_non_ord_samp_booking_dtls where id in($wo_dtls_id2)", "id", "qty");
							$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id2) group by b.work_order_dtls_id", "work_order_dtls_id", "qty");
						}
						
						
						
					}
					else
					{
						$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id_all) group by b.work_order_dtls_id", "work_order_dtls_id", "qty");
					
						$wo_qty_arr=return_library_array("select id, order_qnty as qty from inv_transaction where id in($wo_dtls_id)", "id", "qty");
					} 
				}
				
				$i=1;
				foreach ($nameArray as $row)
				{
					$bl_qty='';
				?>
                    <tr class="general" id="row_<? echo $i; ?>">
                        <? 
                        if( $pi_basis_id == 1 ) 
                        { 
                        	?>
                            <td>
                                <input type="checkbox" name="workOrderChkbox_<? echo $i; ?>" id="workOrderChkbox_<? echo $i; ?>" value="" />
                            </td>
                            <td>
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $i; ?>);" <? echo $disable_field; ?>  readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                                <input type="hidden" name="hideTransData_<? echo $i; ?>" id="hideTransData_<? echo $i; ?>" value="" readonly />
                            </td>
                        	<? 
							if($goods_rcv_status==2 && $row[csf('booking_without_order')]==1)
							{ 
								$bl_qty=$wo_qty_arr2[$row[csf('work_order_dtls_id')]]-$prev_pi_qty_arr[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
							}
							else if($goods_rcv_status==2 && $row[csf('booking_without_order')]!=1)
							{ 
								$bl_qty=$wo_qty_arr[$row[csf('work_order_no')]][$row[csf('work_order_dtls_id')]][$row[csf('color_id')]][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('size_id')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('brand_supplier')]][$row[csf('uom')]][number_format($row[csf('rate')],2,'.','')]-$prev_pi_qty_arr[$row[csf('work_order_no')]][$row[csf('work_order_dtls_id')]][$row[csf('color_id')]][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('size_id')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('brand_supplier')]][$row[csf('uom')]][$row[csf('rate')]]+$row[csf('quantity')];
							}
							else
							{
								$bl_qty=$wo_qty_arr[$row[csf('work_order_dtls_id')]]-$prev_pi_qty_arr[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
							}
							
							//echo $row[csf('work_order_no')]."#".$row[csf('work_order_dtls_id')]."#".$row[csf('color_id')]."#".$row[csf('item_group')]."#".$row[csf('item_description')]."#".$row[csf('size_id')]."#".$row[csf('item_color')]."#".$row[csf('item_size')]."#".$row[csf('brand_supplier')]."#".$row[csf('uom')]."#".$row[csf('rate')];die;
							?>
							<td>
                            <input type="text" name="itemgroupid_<? echo $i; ?>" id="itemgroupid_<? echo $i; ?>" class="text_boxes" value="<? echo $item_group_arr[$row[csf('item_group')]]; ?>" style="width:100px;" placeholder="<? echo $row[csf('item_group')]; ?>" <? echo $disable_field; ?>  readonly /> 
							</td>
                            <?
                        }
						else
						{
							?>
							<td> 
							 <?
								echo create_drop_down( "itemgroupid_".$i, 110, $item_group_arr,'', 1, '-Select-',$row[csf('item_group')],"get_php_form_data( this.value+'**'+'uom_$i', 'get_uom', 'requires/pi_accessories_controller' );",$disable_status); 
							 ?>  
							</td>
                            <?
						}
                        ?>
                        <td>
                            <input type="text" name="itemdescription_<? echo $i; ?>" id="itemdescription_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('item_description')]; ?>" style="width:130px" maxlength="200" <? echo $disable; ?>/>
                        </td>
                        <td>
                        	<input type="text" name="brandSupRef_<? echo $i; ?>" id="brandSupRef_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('brand_supplier')]; ?>" style="width:80px" maxlength="150" <? echo $disable; ?>/>
                        </td>
                        <td>
 							<input type="text" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )"  style="width:70px" maxlength="50" <? echo $disable; ?> value="<? echo $color_library[$row[csf('color_id')]]; ?>"/>                        
                        </td>
                        <td>
                            <input type="text" name="sizeName_<? echo $i; ?>" id="sizeName_<? echo $i; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('size_id')]]; ?>" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:60px;" maxlength="50" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <input type="text" name="itemColor_<? echo $i; ?>" id="itemColor_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="<? echo $color_library[$row[csf('item_color')]]; ?>" style="width:70px" maxlength="50" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <input type="text" name="itemSize_<? echo $i; ?>" id="itemSize_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('item_size')]; ?>" style="width:60px;" maxlength="50" <? echo $disable; ?>/>
                        </td>
                        <?
                        if( $pi_basis_id == 1 ) 
                        {
							?>
                            <td>
                            <input type="text" name="uom_<? echo $i; ?>" id="uom_<? echo $i; ?>" class="text_boxes" value="<? echo $unit_of_measurement[$row[csf('uom')]]; ?>" style="width:50px;" placeholder="<? echo $row[csf('uom')]; ?>" readonly <? echo $disable; ?>/>
                            </td>
                            <?
							
						}
						else
						{
							?>
                            <td>
								<?
                                    echo create_drop_down( "uom_".$i, 60, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1); 
                                ?>
                            </td>
                            <?
						}
                        ?>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $i; ?>)" placeholder="<? echo $bl_qty; ?>" <? echo $disable_field; ?> />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:45px;" onKeyUp="calculate_amount(<? echo $i; ?>)" <? echo $disable; ?> />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" <? echo $disable; ?> readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
                            <input type="hidden" name="bookingWithoutOrder_<? echo $i; ?>" id="bookingWithoutOrder_<? echo $i; ?>" value="<? echo $row[csf('booking_without_order')]; ?>"/>
                        </td>
                        <? 
                        if( $pi_basis_id == 2 ) 
                        { 
                        ?>
                        	<td width="65">
                                <input type="button" id="increase_<? echo $i; ?>" name="increase_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
                                <input type="button" id="decrease_<? echo $i; ?>" name="decrease_<? echo $i; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                        	</td> 
                        <?
                        }
                        ?> 
                    </tr>
            	<?		
				$i++;
				}
				$txt_tot_row=$i-1;
			}
				?>
	            </tbody>	
	            <tfoot class="tbl_bottom">
		            <tr>
		                <? 
		                if( $pi_basis_id == 1 ) 
		                { 
		                ?>
		                    <td>&nbsp;</td>
		                    <td>&nbsp;</td>
		                <? 
		                } 
		                ?>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>Total</td>
		                <td>
		                    <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $tot_amnt; ?>" style="width:75px;" readonly/>
		                </td>
		                <? 
		                if( $pi_basis_id == 2 ) 
		                { 
		                ?>
		                    <td width="65">&nbsp;</td> 
		                <?
		                }
		                ?>
		            </tr>
		            <tr>
		                <? 
		                if( $pi_basis_id == 1 ) 
		                { 
		                ?>
		                    <td>&nbsp;</td>
		                    <td>&nbsp;</td>
		                <? 
		                } 
		                ?>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>Upcharge</td>
		                <td>
		                    <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
		                </td>
		                <? 
		                if( $pi_basis_id == 2 ) 
		                { 
		                ?>
		                    <td width="65">&nbsp;</td> 
		                <?
		                }
		                ?>
		            </tr>
		            <tr>
		                <? 
		                if( $pi_basis_id == 1 ) 
		                { 
		                ?>
		                    <td>&nbsp;</td>
		                    <td>&nbsp;</td>
		                <? 
		                } 
		                ?>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>Discount</td>
		                <td>
		                    <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
		                </td>
		                <? 
		                if( $pi_basis_id == 2 ) 
		                { 
		                ?>
		                    <td width="65">&nbsp;</td> 
		                <?
		                }
		                ?>
		            </tr>
		            <tr>
		                <? 
		                if( $pi_basis_id == 1 ) 
		                { 
		                ?>
		                    <td>&nbsp;</td>
		                    <td>&nbsp;</td>
		                <? 
		                } 
		                ?>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>&nbsp;</td>
		                <td>Net Total</td>
		                <td>
		                    <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $tot_amnt_net; ?>" style="width:75px;" readonly/>
		                </td>
		                <? 
		                if($pi_basis_id == 2 ) 
		                { 
		                ?>
		                    <td width="65">&nbsp;</td> 
		                <?
		                }
		                ?>
		            </tr>
	        	</tfoot>
        <?     
		}
		
        ?>
	    </table>
	    <table width="100%">
	        <tr>
	            <td class="button_container" colspan="2"></td>
	        </tr>
	        <tr>
	            <td width="15%">
	                <?
	                if($pi_basis_id == 1) 
	                {
	                ?>
	                    <input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/> Check / Uncheck All
	                <?
	                }
	                ?>
	                &nbsp;&nbsp;&nbsp;&nbsp;
	            </td>
	            <td width="80%" align="center"> 
	                <input type="hidden" name="txt_deleted_id" id="txt_deleted_id" readonly/> 
	                <input type="hidden" name="txt_tot_row" id="txt_tot_row" value="<? echo $txt_tot_row; ?>" readonly/>                      
	               <? echo load_submit_buttons( $_SESSION['page_permission'], "fnc_pi_item_details", 0,0 ,"reset_form('pimasterform_2','','','txt_tot_row,0','$(\'#tbl_pi_item tbody tr:not(:first)\').remove();')",2) ; ?>
	            </td>    
	        </tr>				
	    </table>
    <?
	}
	exit();
}

//---------------------------------------------End Pi Details------------------------------------------------------------------------//
if ($action=="get_uom")
{
	$data=explode("**",$data);
	$database_id=$data[0];
	$field_id=$data[1];
	$nameArray=sql_select( "select order_uom from lib_item_group where id='$database_id'" );
	foreach ($nameArray as $row)
	{
	 	echo "document.getElementById('$field_id').value = ".trim($row[csf("order_uom")]).";\n";  
		die;
	}
	exit();
}

if($action=="load_drop_down_embelltype")
{
	$data=explode("**",$data);
	$embell_name=$data[0];
	$disable_status=$data[1];
	$field_id=$data[2];
    
	if($embell_name==1)
		echo create_drop_down( "$field_id", 130, $emblishment_print_type,'', 1, '-Select-', 0,"",$disable_status); 
	else if($embell_name==2)
		echo create_drop_down( "$field_id", 130, $emblishment_embroy_type,'', 1, '-Select-', 0,"",$disable_status); 
	else if($embell_name==3)
		echo create_drop_down( "$field_id", 130, $emblishment_wash_type,'', 1, '-Select-', 0,"",$disable_status); 	
	else if($embell_name==4)
		echo create_drop_down( "$field_id", 130, $emblishment_spwork_type,'', 1, '-Select-', 0,"",$disable_status); 
	else
		echo create_drop_down( "$field_id", 130, $blank_array,'', 1, '-Select-', 0,"",$disable_status); 
		
	exit();		
}

//--------------------
if ($action=="fabricDescription_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
		
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
		var selected_id = new Array; var selected_name = new Array();
		
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
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
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_data' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name ='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}
		
    </script>

</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:720px;margin-left:10px">
			<? 
				/*if($prev_attached_id=="")
					$id_cond="";
				else
					$id_cond="and id not in($prev_attached_id)";*/
				
				$composition_arr=array();
				$compositionData=sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls where status_active=1 and is_deleted=0");
				foreach( $compositionData as $row )
				{
					$composition_arr[$row[csf('mst_id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
				}
				
				$data_array=sql_select("select id, construction, fab_nature_id, gsm_weight from lib_yarn_count_determina_mst where fab_nature_id=$fabricNature and status_active=1 and is_deleted=0 $id_cond"); 
			?>
            <input type="hidden" name="txt_selected_id" id="txt_selected_id" class="text_boxes" value="">
            <input type="hidden" name="txt_selected" id="txt_selected" class="text_boxes" value="">
            <div style="margin-left:10px; margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680">
                    <thead>
                        <th width="50">SL</th>
                        <th width="100">Fabric Nature</th>
                        <th width="150">Construction</th>
                        <th width="100">GSM/Weight</th>
                        <th>Composition</th>
                    </thead>
                </table>
                <div style="width:700px; max-height:280px; overflow-y:scroll" id="list_container" align="left"> 
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="680" id="tbl_list_search">  
                        <? 
                        $i=1; 
                        foreach($data_array as $row)
                        {  
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
                            $construction=$row[csf('construction')];
							$comp=$composition_arr[$row[csf('id')]];
							
                            /*$determ_sql=sql_select("select copmposition_id, percent from lib_yarn_count_determina_dtls where mst_id=".$row[csf('id')]." and status_active=1 and is_deleted=0");
                            foreach( $determ_sql as $d_row )
                            {
                                $comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
                            }*/
                            
                         ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
                                <td width="50"><? echo $i; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
                                    <input type="hidden" name="txt_data" id="txt_data<? echo $i ?>" value="<? echo $construction."**".$comp."**". $row[csf('gsm_weight')]; ?>"/>	
                                </td>
                                <td width="100"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
                                <td width="150"><p><? echo $row[csf('construction')]; ?></p></td>
                                <td width="100"><? echo $row[csf('gsm_weight')]; ?></td>
                                <td><p><? echo $comp; ?></p></td>
                            </tr>
                        <? 
                        $i++; 
                        } 
                        ?>
                    </table>
                </div> 
            </div>
            <table width="700" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%"> 
                            <div style="width:50%; float:left" align="left">
                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                            </div>
                            <div style="width:50%; float:left" align="left">
                            	<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbuttonplasminus" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

//--------------------
if ($action=="testItem_popup")
{
	echo load_html_head_contents("Test Item Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
		
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
		var selected_id = new Array; var selected_name = new Array();
		
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function set_all()
		{
			var old=document.getElementById('txt_item_row_id').value;
			if(old!="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{  
					js_set_value( old[i] ) 
				}
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
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name ='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}
		
    </script>

</head>

<body onLoad="set_all();">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:510px;margin-left:10px">
            <input type="hidden" name="txt_selected_id" id="txt_selected_id" class="text_boxes" value="">
            <input type="hidden" name="txt_selected" id="txt_selected" class="text_boxes" value="">
            <div style="margin-left:10px; margin-top:10px">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="480">
                    <thead>
                        <th width="50">SL</th>
                        <th width="150">Test Category</th>
                        <th width="100">Test For</th>
                        <th>Test Item</th>
                    </thead>
                </table>
                <div style="width:500px; max-height:280px; overflow-y:scroll" id="list_container" align="left"> 
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="480" id="tbl_list_search">  
                        <? 
                        $i=1; $item_row_id=''; $prev_attached_id=explode(",",$prev_attached_id); 
						$data_array=sql_select("select id, test_category,test_for,test_item from lib_lab_test_rate_chart where test_for=$cboTestFor and status_active=1 and is_deleted=0");
                        foreach($data_array as $row)
                        {  
                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							if(in_array($row[csf('id')],$prev_attached_id)) 
							{
								if($item_row_id=="") $item_row_id=$i; else $item_row_id.=",".$i;
							}
                         ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>">
                                <td width="50"><? echo $i; ?>
                                    <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $row[csf('id')]; ?>"/>
                                    <input type="hidden" name="txt_individual" id="txt_individual<? echo $i ?>" value="<? echo $row[csf('test_item')]; ?>"/>	
                                </td>
                                <td width="150"><? echo $testing_category[$row[csf('test_category')]]; ?></td>
                                <td width="100"><p><? echo $test_for[$row[csf('test_for')]]; ?></p></td>
                                <td><p><? echo $row[csf('test_item')]; ?></p></td>
                            </tr>
                        <? 
                        $i++; 
                        } 
                        ?>
                        <input type="hidden" name="txt_item_row_id" id="txt_item_row_id" value="<?php echo $item_row_id; ?>"/>
                    </table>
                </div> 
            </div>
            <table width="500" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                        <div style="width:100%"> 
                            <div style="width:50%; float:left" align="left">
                                <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                            </div>
                            <div style="width:50%; float:left" align="left">
                            	<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbuttonplasminus" value="Close" style="width:100px" />
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}


if ($action=="pi_popup")
{
	echo load_html_head_contents("PI Info", "../../../", 1, 1,'','','');
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
            <table cellpadding="0" cellspacing="0" width="870" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Supplier</th>
                    <th>PI Number</th>
                    <th>System ID</th>
                    <th>Date Range</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="txt_item_category" id="txt_item_category" class="text_boxes" style="width:70px" value="<? echo $item_category; ?>">
                    	<input type="hidden" name="txt_selected_pi_id" id="txt_selected_pi_id" class="text_boxes" style="width:70px" value="">   
                    </th> 
                </thead>
                <tr class="general">
                    <td class="must_entry_caption">
						 <? echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--Select--',$importer_id,"load_drop_down( 'pi_accessories_controller',this.value+'_'+$item_category, 'load_supplier_dropdown', 'supplier_td' );",0); ?>       
                    </td>
                    <td id="supplier_td">	
                        <?
							//echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
                        echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$importer_id' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
						?> 
                    </td>                 
                    <td> 
                        <input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:100px">
                    </td>	
                    <td> 
                        <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes_numeric" style="width:100px">
                    </td>						
                    <td align="center">
                      <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_item_category').value+'_'+document.getElementById('cbo_importer_id').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_system_id').value, 'create_pi_search_list_view', 'search_div', 'pi_accessories_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="6" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_supplier_id').val( <? echo $supplier_id; ?> );
</script>
</html>
<?
exit();
}

if($action=="create_pi_search_list_view")
{
	$data=explode('_',$data);
	 
	if (trim($data[0])!="") $pi_number=" and a.pi_number like '%".trim($data[0])."%'"; else { $pi_number = ''; }
	if ($data[1]!="" &&  $data[2]!="")
	{
		if($db_type==0)
		{
			$pi_date = "and a.pi_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$pi_date = "and a.pi_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'";
		}
	}
	else
	{
		$pi_date ="";
	}
	
	$item_category_id =$data[3];
	$importer_id =$data[4];
	if($data[5]==0) $supplier_id="%%"; else $supplier_id=$data[5];
	
	if (trim($data[6])=="") $system_id_cond=''; else $system_id_cond=" and a.id=".trim($data[6]);
	  
	if($importer_id==0) { echo "Please Select Company First."; die; }
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$arr=array(2=>$item_category,3=>$comp,4=>$supplier,7=>$pi_basis);
	 
	//$sql= "select distinct(a.id),a.pi_number,a.pi_date,b.item_category_id,a.importer_id,a.supplier_id,a.last_shipment_date,a.hs_code,a.pi_basis_id from com_pi_master_details a, com_pi_item_details b where a.id = b.pi_id and a.supplier_id like '$supplier_id' and a.importer_id=$importer_id and b.item_category_id=$item_category_id and a.entry_form=167 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $pi_number $pi_date $system_id_cond order by a.pi_number";

	$sql= "select distinct(a.id),a.pi_number,a.pi_date,a.importer_id,a.supplier_id,a.last_shipment_date,a.hs_code,a.pi_basis_id 
		from com_pi_master_details a left join  com_pi_item_details b on a.id = b.pi_id and b.item_category_id = $item_category_id and b.status_active=1 and b.is_deleted=0
		where a.supplier_id like '$supplier_id' and a.importer_id=$importer_id $pi_number $pi_date $system_id_cond and a.entry_form=167 and a.version = 2 and a.status_active = 1 and a.is_deleted = 0
		order by a.id desc";

	
	echo create_list_view("list_view", "PI No,PI Date,Item Category,Importer,Supplier,Last Shipment Date,HS Code,PI Basis, System ID", "90,75,90,120,100,90,80,100","890","270",0, $sql , "js_set_value", "id", "", 1, "0,0,item_category_id,importer_id,supplier_id,0,0,pi_basis_id,0", $arr , "pi_number,pi_date,item_category_id,importer_id,supplier_id,last_shipment_date,hs_code,pi_basis_id,id", "",'','0,3,0,0,0,3,0,0,0');
	 
	exit();	
} 

if ($action=="populate_data_from_search_popup")
{
	$userName_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$data_array=sql_select("select id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, approved, ready_to_approved, lc_group_no, approval_user, goods_rcv_status from com_pi_master_details where id='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_item_category_id').value = '".$row[csf("item_category_id")]."';\n";  
		echo "document.getElementById('cbo_importer_id').value = '".$row[csf("importer_id")]."';\n";  
		
		echo "load_drop_down('requires/pi_accessories_controller',".$row[csf("importer_id")]."+'_'+".$row[csf("item_category_id")].", 'load_supplier_dropdown', 'supplier_td' );\n";
		
		if($row[csf("last_shipment_date")]=="0000-00-00" || $row[csf("last_shipment_date")]=="") $last_shipment_date=""; else $last_shipment_date=change_date_format($row[csf("last_shipment_date")]);
		if($row[csf("pi_validity_date")]=="0000-00-00" || $row[csf("pi_validity_date")]=="") $pi_validity_date=""; else $pi_validity_date=change_date_format($row[csf("pi_validity_date")]);
		echo "document.getElementById('cbo_supplier_id').value = '".$row[csf("supplier_id")]."';\n";  
		echo "document.getElementById('pi_number').value = '".$row[csf("pi_number")]."';\n";  
		echo "document.getElementById('pi_date').value = '".change_date_format($row[csf("pi_date")])."';\n";  
		echo "document.getElementById('last_shipment_date').value = '".$last_shipment_date."';\n";  
		echo "document.getElementById('pi_validity_date').value = '".$pi_validity_date."';\n";  
		echo "document.getElementById('cbo_currency_id').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('cbo_source_id').value = '".$row[csf("source")]."';\n";  
		echo "document.getElementById('hs_code').value = '".$row[csf("hs_code")]."';\n";  
		echo "document.getElementById('txt_internal_file_no').value = '".$row[csf("internal_file_no")]."';\n";  
		echo "document.getElementById('intendor_name').value = '".$row[csf("intendor_name")]."';\n";  
		echo "document.getElementById('cbo_pi_basis_id').value = '".$row[csf("pi_basis_id")]."';\n";  
		echo "document.getElementById('txt_remarks').value = '".($row[csf("remarks")])."';\n";
		echo "document.getElementById('hide_approved_status').value = '".$row[csf("approved")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_user_name').value = '".$userName_arr[$row[csf("approval_user")]]."';\n";
		echo "document.getElementById('hiddn_user_id').value = '".$row[csf("approval_user")]."';\n";
		echo "document.getElementById('txt_system_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_goods_rcv_status').value = '".$row[csf("goods_rcv_status")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("ready_to_approved")]."';\n";
		echo "document.getElementById('txt_lc_group_no').value = '".$row[csf("lc_group_no")]."';\n";
		echo "$('#cbo_item_category_id').attr('disabled','true')".";\n";
		echo "$('#cbo_pi_basis_id').attr('disabled','true')".";\n";
		echo "$('#cbo_goods_rcv_status').attr('disabled','true')".";\n";
		echo "$('#cbo_importer_id').attr('disabled','true')".";\n";
		echo "$('#cbo_supplier_id').attr('disabled','true')".";\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_pi_mst',1);\n";
		
		echo "document.getElementById('is_approved').value = '".$row[csf("approved")]."';\n";
		
		if($row[csf("approved")]==1)
	  	{
			echo "$('#approved').text('Approved');\n"; 
	  	}
	  	else
	  	{
		 	echo "$('#approved').text('');\n";
	  	}	 
	}
	
	exit();
}
 
if ($action=="wo_popup")
{
	echo load_html_head_contents("WO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $prev_wo_ids;die;
	$previous_wo_ids=$prev_wo_nums=$prev_deters=$prev_colors=$prev_constructs=$prev_compositions=$prev_gsms=$prev_widths=$prev_uoms="";
	if($item_category_id==2 || $item_category_id==3)
	{
		$prev_wo_id_arr=$prev_wo_num_arr=$prev_deter_arr=$prev_color_arr=$prev_construct_arr=$prev_composition_arr=$prev_gsm_arr=$prev_width_arr=$prev_uom_arr=array();
		$prev_wo_feb_datas_ref=explode("***",$prev_wo_feb_datas);
		foreach($prev_wo_feb_datas_ref as $prev_data)
		{
			$prev_data_ref=explode("**",$prev_data);
			$prev_wo_id_arr[$prev_data_ref[0]]=$prev_data_ref[0];
			$prev_wo_num_arr[$prev_data_ref[1]]=$prev_data_ref[1];
			$prev_deter_arr[$prev_data_ref[2]]=$prev_data_ref[2];
			$prev_color_arr[$prev_data_ref[3]]=$prev_data_ref[3];
			$prev_construct_arr[$prev_data_ref[4]]=$prev_data_ref[4];
			$prev_composition_arr[$prev_data_ref[5]]=$prev_data_ref[5];
			$prev_gsm_arr[$prev_data_ref[6]]=$prev_data_ref[6];
			$prev_width_arr[$prev_data_ref[7]]=$prev_data_ref[7];
			$prev_uom_arr[$prev_data_ref[8]]=$prev_data_ref[8];
		}
		
		$color_name_array=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'color_name','id');
		$previous_wo_ids=implode(",",$prev_wo_id_arr);
		$prev_wo_nums=implode(",",$prev_wo_num_arr);
		$prev_deters=implode(",",$prev_deter_arr);
		foreach($prev_color_arr as $color_names)
		{
			$prev_colors.=$color_name_array[$color_names].",";
		}
		$prev_colors=chop($prev_colors,",");
		$prev_constructs=implode(",",$prev_construct_arr);
		$prev_compositions=implode(",",$prev_composition_arr);
		$prev_gsms=implode(",",$prev_gsm_arr);
		$prev_widths=implode(",",$prev_width_arr);
		$prev_uoms=implode(",",$prev_uom_arr);
	}
	//echo $previous_wo_ids;die;
	
?> 

	<script>
	
		var item_category_id=<? echo $item_category_id; ?>;
		
		/*$(document).ready(function(e) {
            load_drop_down( 'pi_accessories_controller',<?echo $importer_id; ?>+'_'+item_category, 'load_supplier_dropdown', 'supplier_td' );
			$('#cbo_supplier_id').val( <?echo $supplier_id; ?> );
        });*/
		
		var selected_id = new Array; var selected_id_check = new Array; selected_name = new Array();var order_type_arr = new Array; order_type = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) 
		{
			//var selected_id = new Array;
			//alert(str);
			if (str!="") str=str.split("_");
			
			//alert(item_category);
			if(item_category_id==4)
			{
				var id_sensitivity=str[1];
				var id_sensitivity_type=+str[3];
			}
			else 
			{
				var id_sensitivity_ref=str[1].split("*");
				var id_sensitivity=id_sensitivity_ref[0];
				var id_sensitivity_type=id_sensitivity_ref[1];
			}
			
			/*if(item_category==4)
				var id_sensitivity=str[1]+"_"+str[2]+"_"+str[3];
				
			else 
				
				var id_sensitivity_ref=str[1].split("*");
				var id_sensitivity=id_sensitivity_ref[0];
				var id_sensitivity_type=id_sensitivity_ref[1];*/
			
			
			if(order_type_arr.length==0)
			{
				order_type_arr.push( id_sensitivity_type );
			}
			else if( jQuery.inArray( id_sensitivity_type, order_type_arr )==-1 &&  order_type_arr.length>0)
			{
				alert("Order and Non Order Mixed is Not Allow");
				return;
			}
			
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			
			var tot_wo_amt=$('#totWoAmt').val()*1;
			var wo_amt=$('#woAmt_'+str[0]).val()*1;
			
			if( jQuery.inArray( id_sensitivity, selected_id ) == -1 ) {
				selected_id.push( id_sensitivity );
				$('#totWoAmt').val(number_format_common((tot_wo_amt+wo_amt),2));
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) 
				{
					if( selected_id[i] == id_sensitivity ) break;
				}
				selected_id.splice( i, 1 );
				$('#totWoAmt').val(number_format_common((tot_wo_amt-wo_amt),2));
			}
			
			
			
			var id = ''; var ord_type= '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ord_type = order_type_arr[0];
			
			$('#txt_selected_wo_id').val( id );
			$('#order_non_order_type').val( ord_type );
		}
	
		function reset_hide_field()
		{
			$('#txt_selected_wo_id').val( '' );
			selected_id = new Array(); 
			selected_name = new Array();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:900px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:100%;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="850" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Supplier</th>
                    <th>WO Number</th>
                    <th>WO Date Range</th>
                    <th>Based on</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" onClick="reset_hide_field()" />
                        <input type="hidden" name="txt_item_category" id="txt_item_category" class="text_boxes" style="width:70px" value="<? echo $item_category_id; ?>">
                        <input type="hidden" name="txt_goods_rcv_status" id="txt_goods_rcv_status" class="text_boxes" style="width:70px" value="<? echo $goods_rcv_status; ?>">
                    	<input type="hidden" name="txt_selected_wo_id" id="txt_selected_wo_id" class="text_boxes" value=""> 
                        <input type="hidden" name="order_non_order_type" id="order_non_order_type" class="text_boxes" value="">  
                    </th> 
                </thead>
                <tr class="general">
                    <td>
					<?
						if($importer_id>0) $dis_ana=1; else $dis_ana=0;
                    	echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',$importer_id,"load_drop_down( 'pi_accessories_controller',this.value+'_'+$item_category_id, 'load_supplier_dropdown', 'supplier_td' );",$dis_ana); 
                    ?>       
                    </td>
                    <td id="supplier_td">	
					<?
					if($importer_id>0 && $supplier_id>0)
					{
						echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a, lib_supplier c where c.id=a.supplier_id and a.tag_company=$importer_id and c.id=$supplier_id and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier_id,'',1); 
					}
					else if($importer_id>0 && $supplier_id<1)
					{
						echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a, lib_supplier c where c.id=a.supplier_id and a.tag_company=$importer_id and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
					}
					else
					{
						echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
					}
                        
                    ?> 
                    </td>                 
                    <td> 
                        <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:120px">
                    </td>						
                    <td align="center">
                      <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
					  <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					</td>
                    <td> 
                    <?
						$wo_based_on=array(1=>"Work Orde Wise",2=>"Item Wise");
						/*if($item_category==2 || $item_category==4) 
						{
							echo create_drop_down( "cbo_based_on", 100, $wo_based_on,"", 1, "Select", 1, "",0 );
						}
						else
						{
							echo create_drop_down( "cbo_based_on", 100, $wo_based_on,"", 1, "Select", 0, "",1 );
						}*/
						echo create_drop_down( "cbo_based_on", 100, $wo_based_on,"", 0, "", 1, "",0 );
					?>
                    </td> 
                    
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_wo_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_item_category').value+'_'+document.getElementById('cbo_importer_id').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_based_on').value+'_'+document.getElementById('txt_goods_rcv_status').value+'_'+'<? echo $prev_wo_ids; ?>'+'_'+'<? echo $previous_wo_ids; ?>'+'_'+'<? echo $prev_deters; ?>'+'_'+'<? echo $prev_colors; ?>'+'_'+'<? echo $prev_constructs; ?>'+'_'+'<? echo $prev_compositions; ?>'+'_'+'<? echo $prev_gsms; ?>'+'_'+'<? echo $prev_widths; ?>'+'_'+'<? echo $prev_uoms; ?>', 'create_wo_search_list_view', 'search_div', 'pi_accessories_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="6" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <table width="100%" style="margin-top:5px">
                <tr>
                    <td colspan="5">
                        <div style="width:100%; margin-top:10px; margin-left:10px" id="search_div" align="left"></div>
                    </td>
                </tr>
            </table> 
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_supplier_id').val( <? echo $supplier_id; ?> );
</script>
</html>
<?
exit();
}

if($action=="create_wo_search_list_view")
{
	$data = explode("_",$data);
	
	$item_category_id =$data[3];
	$company_id =$data[4];
	$selected_based_on=$data[7];
	$goods_rcv_status=$data[8];
	$prev_wo_ids=$data[9];
	//echo $data[10];die;
	if($item_category_id==2 || $item_category_id==3)
	{
		$prev_wo_mst_ids=$data[10];
		$prev_dtls_data_arr=array();
		if($selected_based_on==2 && $prev_wo_mst_ids!="")
		{
			$prev_deters=$data[11];
			$prev_colors=$data[12];
			$prev_constructs_data=$data[13];
			$prev_compositions_data=$data[14];
			$prev_gsms=$data[15];
			$prev_widths_data=$data[16];
			$prev_uoms=$data[17];
			
			$prev_construct_arr=explode(",",$prev_constructs_data);
			foreach($prev_construct_arr as $prev_construct)
			{
				$prev_constructs.="'".$prev_construct."',";
			}
			$prev_constructs=chop($prev_constructs,",");
			
			$prev_composition_arr=explode(",",$prev_compositions_data);
			foreach($prev_composition_arr as $prev_composition)
			{
				$prev_compositions.="'".$prev_composition."',";
			}
			$prev_compositions=chop($prev_compositions,",");
			$prev_width_arr=explode(",",$prev_widths_data);
			foreach($prev_width_arr as $prev_width)
			{
				$prev_widths.="'".$prev_width."',";
			}
			$prev_widths=chop($prev_widths,",");
			
			$sql_prev_cond="";
			if($prev_deters!="") $sql_prev_cond.=" and c.lib_yarn_count_deter_id in($prev_deters)";
			if($prev_colors!="") $sql_prev_cond.=" and b.fabric_color_id in($prev_colors)";
			if($prev_constructs!="") $sql_prev_cond.=" and c.construction in($prev_constructs)";
			if($prev_compositions!="") $sql_prev_cond.=" and c.composition in($prev_compositions)";
			if($prev_gsms!="") $sql_prev_cond.=" and c.gsm_weight in($prev_gsms)";
			if($prev_widths!="") $sql_prev_cond.=" and b.dia_width in($prev_widths)";
			if($prev_uoms!="") $sql_prev_cond.=" and c.uom in($prev_uoms)";
			
			
			$sql_prev_cond2="";
			if($prev_colors!="") $sql_prev_cond2.=" and b.fabric_color in($prev_colors)";
			if($prev_constructs!="") $sql_prev_cond2.=" and b.construction in($prev_constructs)";
			if($prev_compositions!="") $sql_prev_cond2.=" and b.composition in($prev_compositions)";
			if($prev_gsms!="") $sql_prev_cond2.=" and  b.gsm_weight in($prev_gsms)";
			if($prev_widths!="") $sql_prev_cond2.=" and b.dia_width in($prev_widths)";
			if($prev_uoms!="") $sql_prev_cond2.=" and b.uom in($prev_uoms)";
			
			
			if($db_type==0)
			{
				$sql_prev = "select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, group_concat(b.id) as dtls_id, b.fabric_color_id as  fabric_color_id, sum(b.fin_fab_qnty) as wo_qnty, c.lib_yarn_count_deter_id, c.construction as construction, c.composition as copmposition, c.gsm_weight as gsm_weight, b.dia_width, c.uom, 1 as type 
				from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c 
				where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.id in($prev_wo_mst_ids) $sql_prev_cond
				group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, b.fabric_color_id, c.lib_yarn_count_deter_id, c.construction, c.composition, c.gsm_weight, b.dia_width, c.uom 
				union all
				select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, group_concat(b.id) as dtls_id, b.fabric_color as  fabric_color_id, sum(b.grey_fabric) as wo_qnty, 0 as lib_yarn_count_deter_id, b.construction, b.composition as copmposition, b.gsm_weight, b.dia_width, b.uom, 2 as type  
				from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.id in($prev_wo_mst_ids) $sql_prev_cond2
				group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, b.fabric_color, b.construction, b.composition, b.gsm_weight, b.dia_width, b.uom";
				
			}
			else
			{
				$sql_prev = "select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, listagg(cast(b.id as varchar(4000)), ',') within group (order by b.id) as dtls_id, b.fabric_color_id as  fabric_color_id, sum(b.fin_fab_qnty) as wo_qnty, c.lib_yarn_count_deter_id, c.construction as construction, c.composition as copmposition, c.gsm_weight as gsm_weight, b.dia_width, c.uom, 1 as type 
				from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c 
				where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.id in($prev_wo_mst_ids) $sql_prev_cond
				group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, b.fabric_color_id, c.lib_yarn_count_deter_id, c.construction, c.composition, c.gsm_weight, b.dia_width, c.uom 
				union all
				select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, listagg(cast(b.id as varchar(4000)), ',') within group (order by b.id) as dtls_id, b.fabric_color as  fabric_color_id, sum(b.grey_fabric) as wo_qnty, 0 as lib_yarn_count_deter_id, b.construction, b.composition as copmposition, b.gsm_weight, b.dia_width, b.uom, 2 as type  
				from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.id in($prev_wo_mst_ids) $sql_prev_cond2
				group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, b.fabric_color, b.construction, b.composition, b.gsm_weight, b.dia_width, b.uom";
			}
			
			//echo $sql_prev;die; 
			$prev_wo_id_arr=$prev_wo_num_arr=$prev_deter_arr=$prev_color_arr=$prev_construct_arr=$prev_composition_arr=$prev_gsm_arr=$prev_width_arr=$prev_uom_arr=array();
			$sql_prev_result=sql_select($sql_prev);
			foreach($sql_prev_result as $row)
			{
				$prev_dtls_data_arr[$row[csf("booking_no")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("fabric_color_id")]][$row[csf("construction")]][$row[csf("copmposition")]][$row[csf("gsm_weight")]][$row[csf("dia_width")]][$row[csf("uom")]]=$row[csf("booking_no")];
			}
		}
	}
	//print_r($prev_dtls_data_arr);die;
	
	/*
	$prev_wo_ids=$prev_wo_nums=$prev_deters=$prev_colors=$prev_constructs=$prev_compositions=$prev_gsms=$prev_widths=$prev_uoms="";
	$color_name_array=return_library_array( "select id, color_name from lib_color",'color_name','id');
		$prev_wo_ids=implode(",",$prev_wo_id_arr);
		$prev_wo_nums=implode(",",$prev_wo_num_arr);
		$prev_deters=implode(",",$prev_deter_arr);
		
		foreach($prev_color_arr as $color_names)
		{
			$prev_colors.=$color_name_array[$color_names].",";
		}
		$prev_colors=chop($prev_colors,",");
		
		foreach($prev_construct_arr as $prev_construct)
		{
			$prev_constructs.="'".$prev_construct."',";
		}
		$prev_constructs=chop($prev_constructs,",");
		
		foreach($prev_composition_arr as $prev_composition)
		{
			$prev_compositions.="'".$prev_composition."',";
		}
		$prev_compositions=chop($prev_compositions,",");
		
		$prev_gsms=implode(",",$prev_gsm_arr);
		
		foreach($prev_width_arr as $prev_width)
		{
			$prev_widths.="'".$prev_width."',";
		}
		$prev_widths=chop($prev_widths,",");
		
		$prev_uoms=implode(",",$prev_uom_arr);
	
	*/
	
	
	
	//echo "$prev_wo_ids=$prev_deters=$prev_colors=$prev_constructs=$prev_compositions=$prev_gsms=$prev_widths=$prev_uoms";die;
	
	$prev_wo_ids_cond=""; $prev_wo_ids_cond2=""; $prev_wo_ids_cond3=""; $prev_wo_ids_cond4="";
	if($prev_wo_ids!="")
	{
		$prev_wo_ids_cond=" and b.id not in($prev_wo_ids)";
		$prev_wo_ids_cond2=" and c.id not in($prev_wo_ids)";
		$prev_wo_ids_cond3=" and d.id not in($prev_wo_ids)";
		$prev_wo_ids_cond4=" and e.id not in($prev_wo_ids)";
	}
	
	if($company_id==0) { echo "Please Select Company First."; die; }
	if($data[5]==0) $supplier_id="%%"; else $supplier_id =$data[5];
	
	if($item_category_id==2 || $item_category_id==3 || $item_category_id==4 || $item_category_id==12 || $item_category_id==24 || $item_category_id==25)
	{
		if ($data[1]!="" &&  $data[2]!="") 
		{
			if($db_type==0)
			{
				$wo_date_cond = "and a.booking_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'";
				$sample_wo_date_cond = "and s.booking_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$wo_date_cond = "and a.booking_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'";
				$sample_wo_date_cond = "and s.booking_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'";
			}
		}
		else 
		{
			$wo_date_cond ="";
			$sample_wo_date_cond ="";
		}
	}
	else
	{
		if ($data[1]!="" &&  $data[2]!="")
		{
			if($db_type==0)
			{
				$wo_date_cond = "and a.wo_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; 
			}
			else
			{
				$wo_date_cond = "and a.wo_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; 
			}
		}
		else
		{
			$wo_date_cond ="";
		}
	}

	if($item_category_id==1)
	{
		if($data[0]!="") $wo_number=" and a.wo_number like '%".trim($data[0])."'"; else { $wo_number = ''; }
		
		if($selected_based_on==1)
		{
			if($goods_rcv_status==2)
			{
				if($db_type==0)
				{
					$sql = "select a.id as mst_id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.supplier_id, group_concat(b.id) as id, sum(b.supplier_order_quantity) as wo_qnty 
					from wo_non_order_info_mst a, wo_non_order_info_dtls b 
					where a.id=b.mst_id and a.company_name=$company_id and b.item_category_id=$item_category_id and a.pay_mode=2 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond
					group by  a.id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.supplier_id
					order by a.id";
				}
				else
				{
					$sql = "select a.id as mst_id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.supplier_id, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as  id, sum(b.supplier_order_quantity) as wo_qnty 
					from wo_non_order_info_mst a, wo_non_order_info_dtls b 
					where a.id=b.mst_id and a.company_name=$company_id and b.item_category_id=$item_category_id and a.pay_mode=2 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond 
					group by  a.id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.supplier_id
					order by a.id";
				}
				 
			}
			else
			{
				if($db_type==0)
				{
					$sql = "select  a.id as mst_id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.supplier_id, group_concat(c.id) as id
					from wo_non_order_info_mst a, inv_receive_master b, inv_transaction c, product_details_master d 
					where a.id=b.booking_id and b.id=c.mst_id and b.entry_form=1 and b.receive_purpose!=2 and c.item_category=1 and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id=1 and a.company_name=$company_id and c.pi_is_lock!=1 and a.pay_mode=1 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond2
					group by a.id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.supplier_id
					order by a.id";
				}
				else
				{
					$sql = "select a.id as mst_id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.supplier_id, listagg(cast(c.id as varchar(4000)),',') within group(order by c.id) as  id 
					from wo_non_order_info_mst a, inv_receive_master b, inv_transaction c, product_details_master d 
					where a.id=b.booking_id and b.id=c.mst_id and b.entry_form=1 and b.receive_purpose!=2 and c.item_category=1 and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id=1 and a.company_name=$company_id and c.pi_is_lock!=1 and a.pay_mode=1 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond2
					group by a.id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.supplier_id 
					order by a.id";
				}
				
			}
			
			$prev_pi_qnty_arr=return_library_array("select b.work_order_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=1 and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_id",'work_order_id','quantity');
		}
		else
		{
			if($goods_rcv_status==2)
			{
				$sql = "select a.wo_number_prefix_num, a.wo_number, a.wo_date, a.supplier_id, b.id, b.color_name, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.uom, b.supplier_order_quantity as wo_qnty 
				from wo_non_order_info_mst a, wo_non_order_info_dtls b 
				where a.id=b.mst_id and a.company_name=$company_id and b.item_category_id=$item_category_id and a.pay_mode=2 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond order by a.id"; 
			}
			else
			{
				$sql = "select a.wo_number_prefix_num, a.wo_number, a.wo_date, a.supplier_id, c.id, d.color as color_name, d.yarn_count_id as yarn_count, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type, d.unit_of_measure as uom 
				from wo_non_order_info_mst a, inv_receive_master b, inv_transaction c, product_details_master d where a.id=b.booking_id and b.id=c.mst_id and b.entry_form=1 and b.receive_purpose!=2 and c.item_category=1 and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id=1 and a.company_name=$company_id and c.pi_is_lock!=1 and a.pay_mode=1 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond2 order by a.id";
			}
			
			$prev_pi_qnty_arr=return_library_array("select b.work_order_dtls_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=1 and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_dtls_id",'work_order_dtls_id','quantity');
			
		}
		
		
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
		
		/*$arr=array (2=>$supplier_arr,3=>$color_library,4=>$yarn_count,5=>$composition,7=>$composition,9=>$yarn_type,10=>$unit_of_measurement);
		echo create_list_view("tbl_list_search", "WO Number, WO Date, Supplier, Color, Count, Item 1st, Perc. 1st, Item 2nd, Perc. 2nd, Yarn Type, UOM", "70,80,130,80,60,70,60,70,60,80","880","250",0, $sql, "js_set_value", "id", "", 1, "0,0,supplier_id,color_name,yarn_count,yarn_comp_type1st,0,yarn_comp_type2nd,0,yarn_type,uom", $arr , "wo_number_prefix_num,wo_date,supplier_id,color_name,yarn_count,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,uom", "",'','0,3,0,0,0,0,2,0,2,0,0','',1);*/
		
		
		//echo $sql;die;
		$result = sql_select($sql);
	
		
		if($goods_rcv_status==1)
		{
			?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
				<thead>
					<th width="40">SL</th>
					<th width="70">WO No</th>
					<th width="80">WO Date</th>
					<th width="130">Supplier</th>
					<th width="80">Color</th>
					<th width="60">Count</th>
					<th width="70">Item 1st</th>
					<th width="60">Perc. 1st</th>
					<th width="70">Item 2nd</th>
					<th width="60">Perc. 2nd</th>
					<th width="80">Yarn Type</th>
					<th>UOM</th>
				</thead>
			 </table>
			 <div style="width:880px; max-height:250px; overflow-y:scroll">
				 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table" id="tbl_list_search">
				 <? 
				 $i=1;
				 foreach ($result as $row)
				 {
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$row[csf('id')];?>')"> 				
						<td width="40"><? echo "$i"; ?></td>
						<td width="70"><p><? echo $row[csf('wo_number_prefix_num')];?></p></td>
						<td width="80" align="center"><p><? if($row[csf('wo_date')]!="" && $row[csf('wo_date')]!="0000-00-00") echo change_date_format($row[csf('wo_date')]);?></p></td>
						<td width="130"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td> 
						<td width="80"><p><? echo $color_library[$row[csf('color_name')]]; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $yarn_count[$row[csf('yarn_count')]]; ?>&nbsp;</p></td>
						<td width="70"><p><? echo $composition[$row[csf('yarn_comp_type1st')]]; ?></p></td>
						<td width="60"><p><? echo $row[csf('yarn_comp_percent1st')]; ?></p></td>
						<td width="70"><p><? echo $composition[$row[csf('yarn_comp_type2nd')]]; ?>&nbsp;</p></td>
						<td align="center" width="60"><p><? echo $row[csf('yarn_comp_percent2nd')]; ?>&nbsp;</p></td>
						<td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?>&nbsp;</p></td>
						<td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
					</tr>
					 <?
					 $i++;
				 }
				 ?>
				</table>
			</div>
			<?
		}
		else
		{
			?>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
				<thead>
					<th width="40">SL</th>
					<th width="70">WO No</th>
					<th width="80">WO Date</th>
					<th width="130">Supplier</th>
					<th width="80">Color</th>
					<th width="60">Count</th>
					<th width="70">Item 1st</th>
					<th width="60">Perc. 1st</th>
					<th width="70">Item 2nd</th>
					<th width="60">Perc. 2nd</th>
					<th width="80">Yarn Type</th>
					<th>UOM</th>
				</thead>
			 </table>
			 <div style="width:880px; max-height:250px; overflow-y:scroll">
				 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table" id="tbl_list_search">
				 <? 
				 $i=1;
				 foreach ($result as $row)
				 {
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; //mst_id
					if($selected_based_on==1)
					{
						$balance_qnty=$row[csf('wo_qnty')]-$prev_pi_qnty_arr[$row[csf('mst_id')]];
					}
					else
					{
						$balance_qnty=$row[csf('wo_qnty')]-$prev_pi_qnty_arr[$row[csf('id')]];
					}
					if($balance_qnty>0)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$row[csf('id')];?>')"> 				
							<td width="40"><? echo "$i"; ?></td>
							<td width="70"><p><? echo $row[csf('wo_number_prefix_num')];?></p></td>
							<td width="80" align="center"><p><? if($row[csf('wo_date')]!="" && $row[csf('wo_date')]!="0000-00-00") echo change_date_format($row[csf('wo_date')]);?></p></td>
							<td width="130"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td> 
							<td width="80"><p><? echo $color_library[$row[csf('color_name')]]; ?>&nbsp;</p></td>
							<td width="60"><p><? echo $yarn_count[$row[csf('yarn_count')]]; ?>&nbsp;</p></td>

							<td width="70"><p><? echo $composition[$row[csf('yarn_comp_type1st')]]; ?></p></td>
							<td width="60"><p><? echo $row[csf('yarn_comp_percent1st')]; ?></p></td>
							<td width="70"><p><? echo $composition[$row[csf('yarn_comp_type2nd')]]; ?>&nbsp;</p></td>
							<td align="center" width="60"><p><? echo $row[csf('yarn_comp_percent2nd')]; ?>&nbsp;</p></td>
							<td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?>&nbsp;</p></td>
							<td><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
						</tr>
						<?
						$i++;
					}
				 }
				 ?>
				</table>
			</div>
			<?
		}
		
		?>
        
        
        <table width="880" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	}
	
	else if($item_category_id==2 || $item_category_id==3)
	{
		if ($data[0]!="")
		{
			$wo_number=" and a.booking_no like '%".trim($data[0])."'";
			$pi_wo_number=" and b.work_order_no like '%".trim($data[0])."'";
		}
		else 
		{ 
			$wo_number = '';
			$pi_wo_number=""; 
		}
		if($selected_based_on==1)
		{
			$prev_wo_mst_cond="";
			if($prev_wo_mst_ids!="") $prev_wo_mst_cond=" and a.id not in($prev_wo_mst_ids)";
			if($goods_rcv_status==2)
			{
				
				if($db_type==0)
				{
					$sql = "select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, group_concat(b.id) as dtls_id, sum(b.fin_fab_qnty) as wo_qnty, 1 as type 
					from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c 
					where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_mst_cond
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id 
					union all
					select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, group_concat(b.id) as dtls_id, sum(b.grey_fabric) as wo_qnty, 2 as type  
					from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_mst_cond
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id
					order by mst_id";
				}
				else
				{
					$sql = "select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id,  rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as dtls_id, sum(b.fin_fab_qnty) as wo_qnty, 1 as type 
					from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c 
					where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_mst_cond
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id 
					union all
					select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as dtls_id, sum(b.grey_fabric) as wo_qnty, 2 as type  
					from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_mst_cond
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id
					order by mst_id";
				}
				
				//echo $sql;//die;
				
			}
			else
			{
				if($db_type==0)
				{
					$sql = "select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, group_concat(c.id) as dtls_id, 1 as type 
					from wo_booking_mst a, inv_receive_master b, inv_transaction c, product_details_master d
					where a.id=b.booking_id and b.id=c.mst_id and b.entry_form in(17,37) and c.item_category in($item_category_id) and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id in($item_category_id) and a.company_id=$company_id and a.item_category in($item_category_id) and a.supplier_id like '$supplier_id' and a.pay_mode=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.pi_is_lock!=1 $wo_number $wo_date_cond $prev_wo_mst_cond
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id 
					union all
					select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, group_concat(c.id) as dtls_id, 2 as type 
					from wo_non_ord_samp_booking_mst a, inv_receive_master b, inv_transaction c, product_details_master d
					where a.id=b.booking_id and b.id=c.mst_id and b.entry_form in(17,37) and c.item_category in($item_category_id) and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id in($item_category_id) and a.company_id=$company_id and a.item_category in($item_category_id) and a.supplier_id like '$supplier_id' and a.pay_mode=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.pi_is_lock!=1 $wo_number $wo_date_cond $prev_wo_mst_cond 
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id
					order by mst_id";
				}
				else
				{
					$sql = "select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, listagg(cast(c.id as varchar(4000)),',') within group(order by c.id) as dtls_id, 1 as type 
					from wo_booking_mst a, inv_receive_master b, inv_transaction c, product_details_master d
					where a.id=b.booking_id and b.id=c.mst_id and b.entry_form in(17,37) and c.item_category in($item_category_id) and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id in($item_category_id) and a.company_id=$company_id and a.item_category in($item_category_id) and a.supplier_id like '$supplier_id' and a.pay_mode=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.pi_is_lock!=1 $wo_number $wo_date_cond $prev_wo_mst_cond
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id 
					union all
					select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id,listagg(cast(c.id as varchar(4000)),',') within group(order by c.id) as dtls_id, 2 as type 
					from wo_non_ord_samp_booking_mst a, inv_receive_master b, inv_transaction c, product_details_master d
					where a.id=b.booking_id and b.id=c.mst_id and b.entry_form in(17,37) and c.item_category in($item_category_id) and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id in($item_category_id) and a.company_id=$company_id and a.item_category in($item_category_id) and a.supplier_id like '$supplier_id' and a.pay_mode=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.pi_is_lock!=1 $wo_number $wo_date_cond $prev_wo_mst_cond 
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id
					order by mst_id";
				}
				// and c.pi_is_lock!=1 and c.pi_is_lock!=1
				//echo $sql;die;
				$construction_arr=array(); $composition_arr=array();
				$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
				$data_array=sql_select($sql_deter);
				if(count($data_array)>0)
				{
					foreach( $data_array as $row )
					{
						$construction_arr[$row[csf('id')]]=$row[csf('construction')];
						
						if(array_key_exists($row[csf('id')],$composition_arr))
						{
							$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
						}
						else
						{
							$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
						}
					}
				}
			}
			
				
			$prev_pi_qnty_arr=return_library_array("select b.work_order_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.goods_rcv_status<>1 and a.pi_basis_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $pi_wo_number group by b.work_order_id",'work_order_id','quantity');
			
		}
		else
		{
			if($goods_rcv_status==2)
			{
				if($db_type==0)
				{
					
					$sql = "select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, group_concat(b.id) as dtls_id, b.fabric_color_id as  fabric_color_id, sum(b.fin_fab_qnty) as wo_qnty, c.lib_yarn_count_deter_id, c.construction as construction, c.composition as copmposition, c.gsm_weight as gsm_weight, b.dia_width, c.uom, 1 as type 
					from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c 
					where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, b.fabric_color_id, c.lib_yarn_count_deter_id, c.construction, c.composition, c.gsm_weight, b.dia_width, c.uom 
					union all
					select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, group_concat(b.id) as dtls_id, b.fabric_color as  fabric_color_id, sum(b.grey_fabric) as wo_qnty, 0 as lib_yarn_count_deter_id, b.construction, b.composition as copmposition, b.gsm_weight, b.dia_width, b.uom, 2 as type  
					from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, b.fabric_color, b.construction, b.composition, b.gsm_weight, b.dia_width, b.uom
					order by mst_id";
					
				}
				else
				{
					$sql = "select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, listagg(cast(b.id as varchar(4000)), ',') within group (order by b.id) as dtls_id, b.fabric_color_id as  fabric_color_id, sum(b.fin_fab_qnty) as wo_qnty, c.lib_yarn_count_deter_id, c.construction as construction, c.composition as copmposition, c.gsm_weight as gsm_weight, b.dia_width, c.uom, 1 as type 
					from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c 
					where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond 
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, b.fabric_color_id, c.lib_yarn_count_deter_id, c.construction, c.composition, c.gsm_weight, b.dia_width, c.uom 
					union all
					select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, listagg(cast(b.id as varchar(4000)), ',') within group (order by b.id) as dtls_id, b.fabric_color as  fabric_color_id, sum(b.grey_fabric) as wo_qnty, 0 as lib_yarn_count_deter_id, b.construction, b.composition as copmposition, b.gsm_weight, b.dia_width, b.uom, 2 as type  
					from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, b.fabric_color, b.construction, b.composition, b.gsm_weight, b.dia_width, b.uom
					order by mst_id";
				}
				
				$prev_pi_qty_arr=array();
				$sql_prev="select b.work_order_no, b.determination_id, b.color_id, b.fabric_construction, b.fabric_composition, b.gsm, b.dia_width, b.uom, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 $pi_wo_number
				group by b.work_order_no, b.determination_id, b.color_id, b.fabric_construction, b.fabric_composition, b.gsm, b.dia_width, b.uom";// and b.work_order_dtls_id in($wo_dtls_id)
				$prevData=sql_select($sql_prev);
				foreach($prevData as $rowP)
				{
					$prev_pi_qty_arr[$rowP[csf('work_order_no')]][$rowP[csf('determination_id')]][$rowP[csf('color_id')]][$rowP[csf('fabric_construction')]][$rowP[csf('fabric_composition')]][$rowP[csf('gsm')]][$rowP[csf('dia_width')]][$rowP[csf('uom')]]=$rowP[csf('qty')];
				}
				
			}
			else
			{
				if($db_type==0)
				{
					$sql = "select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, group_concat(c.id) as dtls_id, d.color as  fabric_color_id, d.detarmination_id, d.gsm as gsm_weight, d.dia_width, c.order_uom as uom, 1 as type 
					from wo_booking_mst a, inv_receive_master b, inv_transaction c, product_details_master d
					where a.id=b.booking_id and b.id=c.mst_id and b.entry_form in(17,37) and c.item_category in($item_category_id) and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id in($item_category_id) and a.company_id=$company_id and a.item_category in($item_category_id) and a.supplier_id like '$supplier_id' and a.pay_mode=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.pi_is_lock!=1 $wo_number $wo_date_cond
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, d.color, d.detarmination_id, d.gsm, d.dia_width, c.order_uom
					union all
					select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, group_concat(c.id) as dtls_id, d.color as  fabric_color_id, d.detarmination_id, d.gsm as gsm_weight, d.dia_width, c.order_uom as uom, 2 as type 
					from wo_non_ord_samp_booking_mst a, inv_receive_master b, inv_transaction c, product_details_master d
					where a.id=b.booking_id and b.id=c.mst_id and b.entry_form in(17,37) and c.item_category in($item_category_id) and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id in($item_category_id) and a.company_id=$company_id and a.item_category in($item_category_id) and a.supplier_id like '$supplier_id' and a.pay_mode=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.pi_is_lock!=1 $wo_number $wo_date_cond 
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, d.color, d.detarmination_id, d.gsm, d.dia_width, c.order_uom
					order by mst_id";
				}
				else
				{
					$sql = "select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, listagg(cast(c.id as varchar(4000)), ',') within group(order by c.id) as dtls_id, d.color as  fabric_color_id, d.detarmination_id, d.gsm as gsm_weight, d.dia_width, c.order_uom as uom, 1 as type 
					from wo_booking_mst a, inv_receive_master b, inv_transaction c, product_details_master d
					where a.id=b.booking_id and b.id=c.mst_id and b.entry_form in(17,37) and c.item_category in($item_category_id) and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id in($item_category_id) and a.company_id=$company_id and a.item_category in($item_category_id) and a.supplier_id like '$supplier_id' and a.pay_mode=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.pi_is_lock!=1 $wo_number $wo_date_cond
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, d.color, d.detarmination_id, d.gsm, d.dia_width, c.order_uom 
					union all
					select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, listagg(cast(c.id as varchar(4000)), ',') within group(order by c.id) as dtls_id, d.color as  fabric_color_id, d.detarmination_id, d.gsm as gsm_weight, d.dia_width, c.order_uom as uom, 2 as type 
					from wo_non_ord_samp_booking_mst a, inv_receive_master b, inv_transaction c, product_details_master d
					where a.id=b.booking_id and b.id=c.mst_id and b.entry_form in(17,37) and c.item_category in($item_category_id) and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id in($item_category_id) and a.company_id=$company_id and a.item_category in($item_category_id) and a.supplier_id like '$supplier_id' and a.pay_mode=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.pi_is_lock!=1 $wo_number $wo_date_cond
					group by a.id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, d.color, d.detarmination_id, d.gsm, d.dia_width, c.order_uom
					order by mst_id";
				}
				// and c.pi_is_lock!=1 and c.pi_is_lock!=1
				
				$construction_arr=array(); $composition_arr=array();
				$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
				$data_array=sql_select($sql_deter);
				if(count($data_array)>0)
				{
					foreach( $data_array as $row )
					{
						$construction_arr[$row[csf('id')]]=$row[csf('construction')];
						
						if(array_key_exists($row[csf('id')],$composition_arr))
						{
							$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
						}
						else
						{
							$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
						}
					}
				}
			}
			
		}
		//echo $sql;
		//print_r($prev_pi_qnty_arr);die;
			
		//var_dump($prev_pi_qty_arr);die;
		//echo $sql; //die;
		//$result = sql_select($sql);
	
		$supplier_arr=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
		$arr=array (2=>$supplier_arr,3=>$color_library,8=>$unit_of_measurement);
		
		//echo  create_list_view("tbl_list_search", "WO Number, WO Date, Supplier, Color, Construction, Copmposition, GSM, Dia/Width, UOM", "105,80,100,80,120,120,60,70,60","880","250",0, $sql, "js_set_value", "id", "", 1, "0,0,supplier_id,fabric_color_id,0,0,0,0,uom", $arr , "booking_no_prefix_num,booking_date,supplier_id,fabric_color_id,construction,copmposition,gsm_weight,dia_width,uom", "",'','0,3,0,0,0,0,0,0,0','',1);
		
		
		
		if($goods_rcv_status==1)
		{
			?>
			 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
				<thead>
					<th width="40">SL</th>
					<th width="50">WO No</th>
					<th width="75">WO Date</th>
					<th width="80">Supplier</th>
					<th width="80">Color</th>
					<th width="100">Construction</th>
					<th width="130">Copmposition</th>
					<th width="60">GSM</th>
					<th width="60">Dia/ Width</th>
					<th width="60">UOM</th>
					<th>Booking Type</th>
				</thead>
			 </table>
			 <div style="width:880px; max-height:250px; overflow-y:scroll">
				 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table" id="tbl_list_search">
				 <? 
				 $i=1;
				 $nameArray=sql_select( $sql );
				 foreach ($nameArray as $row)
				 {
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$construction=''; $copmposition='';
					$construction=$construction_arr[$row[csf('detarmination_id')]]; 
					$copmposition=$composition_arr[$row[csf('detarmination_id')]];
					
					$prev_datas="";
					if($selected_based_on!=1)
					{
						$prev_datas=$prev_dtls_data_arr[$row[csf('booking_no')]][$row[csf('detarmination_id')]][$row[csf('fabric_color_id')]][$construction][$copmposition][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]];
					}
					
					if($prev_datas=="")
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$row[csf('dtls_id')]."*".$row[csf('type')];?>')"> 				
							<td width="40"><? echo "$i"; ?></td>	
							<td width="50"><p><? echo $row[csf('booking_no_prefix_num')];?></p></td>
							<td width="75" align="center"><p><? if($row[csf('booking_date')]!="" && $row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]);?></p></td>
							<td width="80"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td> 
							<td width="80"><p><? echo $color_library[$row[csf('fabric_color_id')]]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $construction; ?>&nbsp;</p></td>
							<td width="130"><p><? echo $copmposition; ?></p></td>
							<td width="60"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
							<td width="60"><p><? echo $row[csf('dia_width')]; ?>&nbsp;</p></td>
							<td align="center" width="60"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
							<td><p>
							<?
							$booking_type=""; 
							if($row[csf('type')]==1)
							{
								if($row[csf('booking_type')]==1 && $row[csf('is_short')]==1) $booking_type="Short Fab.";
								if($row[csf('booking_type')]==1 && $row[csf('is_short')]==2) $booking_type="Main Fab.";
								if($row[csf('booking_type')]==4 && $row[csf('is_short')]==2) $booking_type="Sample With Order";
							}
							else
							{
								$booking_type="Sample Without Order";
							}
							echo $booking_type; 
							?>&nbsp;</p></td>
						</tr>
						 <?
						 $i++;
					}
				 }
				 
				 ?>
				</table>
			</div>
			<?
		}
		else
		{
			?>
			 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
				<thead>
					<th width="40">SL</th>
					<th width="50">WO No</th>
					<th width="75">WO Date</th>
					<th width="80">Supplier</th>
					<th width="80">Color</th>
					<th width="100">Construction</th>
					<th width="130">Copmposition</th>
					<th width="60">GSM</th>
					<th width="60">Dia/ Width</th>
					<th width="60">UOM</th>
					<th>Booking Type</th>
				</thead>
			 </table>
			 <div style="width:880px; max-height:250px; overflow-y:scroll">
				 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table" id="tbl_list_search">
				 <? 
				/*$con_select22 = connect();
				
				//echo "<pre>";print_r($prev_dtls_data_arr);die;
				$result = oci_parse($con_select22, $sql);
				oci_execute($result);
				//echo $result[csf("dtls_id")]->load();
				//print_r($result);
				while($summ=oci_fetch_array($result))
				{
					echo $summ[csf("dtls_id")]->load();
				}
				die;*/
				 $i=1;
				 $nameArray = sql_select($sql);
				 foreach ($nameArray as $row)
				 {
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$construction=''; $copmposition='';
					if($goods_rcv_status==2)
					{
						$construction=$row[csf('construction')]; 
						$copmposition=$row[csf('copmposition')];
					}
					else
					{
						$construction=$construction_arr[$row[csf('detarmination_id')]]; 
						$copmposition=$composition_arr[$row[csf('detarmination_id')]];
					}
					$prev_datas="";
					if($selected_based_on==1)
					{
						$bal_qtny=$row[csf('wo_qnty')]-$prev_pi_qnty_arr[$row[csf('mst_id')]];
					}
					else
					{
						$bal_qtny=$row[csf('wo_qnty')]-$prev_pi_qty_arr[$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]];
						$prev_datas=$prev_dtls_data_arr[$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]];
					}
					
					if($bal_qtny>0 && $prev_datas=="")
					{

						if($db_type==0)
						{
						  	$dtls_ids = $row[csf('dtls_id')];
						}else{
						  	$dtls_ids = $row[csf('dtls_id')]->load();
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$dtls_ids."*".$row[csf('type')];?>')"> 				
							<td width="40" align="center"><? echo "$i"; ?></td>	
							<td width="50" align="center"><p><? echo $row[csf('booking_no_prefix_num')];?></p></td>
							<td width="75" align="center"><p><? if($row[csf('booking_date')]!="" && $row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]);?></p></td>
							<td width="80"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td> 
							<td width="80"><p><? echo $color_library[$row[csf('fabric_color_id')]]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $construction; ?>&nbsp;</p></td>
							<td width="130"><p><? echo $copmposition; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
							<td width="60" align="center"><p><? echo $row[csf('dia_width')]; ?>&nbsp;</p></td>
							<td align="center" width="60"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?>&nbsp;</p></td>
							<td><p>
							<?
							$booking_type=""; 
							if($row[csf('type')]==1)
							{
								if($row[csf('booking_type')]==1 && $row[csf('is_short')]==1) $booking_type="Short Fab.";
								if($row[csf('booking_type')]==1 && $row[csf('is_short')]==2) $booking_type="Main Fab.";
								if($row[csf('booking_type')]==4 && $row[csf('is_short')]==2) $booking_type="Sample With Order";
							}
							else
							{
								$booking_type="Sample Without Order";
							}
							echo $booking_type; 
							?>&nbsp;</p></td>
						</tr>
						 <?
						 $i++;
					}
				 }
				 
				 ?>
				</table>
			</div>
			<?
		}
		
		?>
        <table width="880" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	}
	
	/*else if($item_category_id==3)
	{
		if ($data[0]!="") $wo_number=" and a.booking_no like '%".trim($data[0])."'"; else { $wo_number = ''; }
		if($goods_rcv_status==2)
		{
			$sql = "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, b.id, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width, b.uom from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id'  and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond order by a.id"; 
		}
		else
		{
			$sql = "select a.id as mst_id, a.booking_type, a.is_short, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, c.id as dtls_id, d.color as  fabric_color_id, d.detarmination_id, d.gsm as gsm_weight, d.dia_width, d.unit_of_measure as uom, 1 as type 
			from wo_booking_mst a, inv_receive_master b, inv_transaction c, product_details_master d
			where a.id=b.booking_id and b.id=c.mst_id and b.entry_form=17 and c.item_category=3 and b.pi_is_lock!=1 and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id=3 and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_co $prev_wo_ids_cond2";
			
			$composition_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
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
						$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
				}
			}
		}
		//echo $sql;die;
		//$result = sql_select($sql);
	
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		$arr=array (2=>$supplier_arr,3=>$color_library,8=>$unit_of_measurement);
		
		echo create_list_view("tbl_list_search", "WO Number, WO Date, Supplier, Color, Construction, Copmposition, Weight, Width, UOM", "105,80,100,80,120,120,60,70,60","880","250",0, $sql, "js_set_value", "id", "", 1, "0,0,supplier_id,fabric_color_id,0,0,0,0,uom", $arr , "booking_no_prefix_num,booking_date,supplier_id,fabric_color_id,construction,copmposition,gsm_weight,dia_width,uom", "",'','0,3,0,0,0,0,0,0,0','',1);
	}*/
	
	else if($item_category_id==4)
	{   

		
		$booking_id_cond=""; $result=explode(",",$wo_dtls_id);
		if($db_type==2 && count($result)>999)
		{
			$booking_id_chunk_arr=array_chunk($result,999) ;
			foreach($booking_id_chunk_arr as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$bokIds_cond.=" b.work_order_dtls_id in($chunk_arr_value) or ";	
			}
			
			$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
			//echo $booking_id_cond;die;
		}
		else
		{
			$booking_id_cond=" and b.work_order_dtls_id in($wo_dtls_id)";	 
		}
			
		
		
		if($db_type==0) $year_cond=" and year(a.insert_date)=".trim($data[6]); else $year_cond=" and to_char(a.insert_date,'YYYY')=".trim($data[6]);
		if($db_type==0) $year_cond_sample=" and year(s.insert_date)=".trim($data[6]); else $year_cond_sample=" and to_char(s.insert_date,'YYYY')=".trim($data[6]);
		
		if(trim($data[0])!="") 
		{
			$wo_number=" and a.booking_no like '%".trim($data[0])."'";
			$sample_wo_number=" and s.booking_no like '%".trim($data[0])."'";
		}
		else 
		{ 
			$wo_number = ''; 
			$sample_wo_number = ''; 
		}
		
		if($db_type==0) 
		{
			$year_field="YEAR(a.insert_date) as year,";
			$year_field_sample="YEAR(s.insert_date) as year,";  
		}
		else if($db_type==2) 
		{
			$year_field="to_char(a.insert_date,'YYYY') as year,";
			$year_field_sample="to_char(s.insert_date,'YYYY') as year,";
		}
		else 
		{
			$year_field="";//defined Later
			$year_field_sample="";//defined Later
		}
		
		/*if($goods_rcv_status==2)
		{
			$prev_pi_qty_arr=array();
			$sql_prev="select b.work_order_no, b.work_order_dtls_id, b.color_id, b.item_group, b.item_description, b.size_id, b.item_color, b.item_size, b.uom, b.rate, b.brand_supplier, sum(b.quantity) as qty 
			from com_pi_master_details a, com_pi_item_details b 
			where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 $booking_id_cond 
			group by b.work_order_no, b.work_order_dtls_id, b.color_id, b.item_group, b.item_description, b.size_id, b.item_color, b.item_size, b.uom, b.rate, b.brand_supplier";// and b.work_order_dtls_id in($wo_dtls_id)
			$prevData=sql_select($sql_prev);
			foreach($prevData as $rowP)
			{
				$prev_pi_qty_non_order_arr[$rowP[csf('work_order_no')]][$rowP[csf('work_order_dtls_id')]][$rowP[csf('color_id')]][$rowP[csf('item_group')]][$rowP[csf('item_description')]][$rowP[csf('size_id')]][$rowP[csf('item_color')]][$rowP[csf('item_size')]][$rowP[csf('brand_supplier')]][$rowP[csf('uom')]][$rowP[csf('rate')]]=$rowP[csf('qty')];
			}
		}
		else
		{
			$prev_pi_qty_order_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty 
			from com_pi_master_details a, com_pi_item_details b 
			where a.id=b.pi_id and a.importer_id='$importer_id' and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 $booking_id_cond group by b.work_order_dtls_id", "work_order_dtls_id", "qty");// and b.work_order_dtls_id in($wo_dtls_id)
		}*/
		
 		if($goods_rcv_status==2)
		{
			//echo $selected_based_on;die;
			if(trim($selected_based_on)==1)
			{ 
				if($db_type==0)
				{
					  $sql = "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short,group_concat(b.id) as id, 0 as trim_group,  max(b.description) as description, max(b.Sensitivity) as sensitivity, 0 as uom, 0 as type, $year_field b.booking_type, sum(b.amount) as amount, sum(b.wo_qnty) as quantity 
					from wo_booking_mst a, wo_booking_dtls b 
				where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $year_cond $prev_wo_ids_cond
				group by a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short, a.insert_date, b.booking_type  
				union all
					select s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, group_concat(d.id) as id, 0 as trim_group, max(d.fabric_description) as description, 0 as sensitivity, 0 as uom, 1 as type, $year_field_sample d.sample_type as booking_type, sum(d.amount) as amount, sum(d.trim_qty) as quantity   
					FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls d 
					WHERE s.booking_no=d.booking_no and s.company_id=$company_id and s.pay_mode=2 and s.status_active =1 and s.is_deleted=0 and d.status_active =1 and d.is_deleted=0 and s.item_category=4 $sample_wo_number $sample_wo_date_cond $year_cond_sample $prev_wo_ids_cond3
					group by s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, s.insert_date, d.sample_type 
					order by type";
				} 
 				else
				{
					
					/*$sql = "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as id, 0 as trim_group, null as description, max(b.Sensitivity) as sensitivity, 0 as uom, 0 as type, $year_field b.booking_type, sum(b.amount) as amount, sum(b.wo_qnty) as quantity 
					from wo_booking_mst a, wo_booking_dtls b 
					where 
					a.booking_no=b.booking_no 
					and a.company_id=$company_id
					and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $year_cond $prev_wo_ids_cond
					group by  a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short, a.insert_date, b.booking_type
					union all
					select s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, listagg(cast(d.id as varchar(4000)),',') within group(order by d.id) as id, 0 as trim_group, max(d.fabric_description) as description, 0 as sensitivity, 0 as uom, 1 as type, $year_field_sample d.sample_type as booking_type, sum(d.amount) as amount, sum(d.trim_qty) as quantity  
					FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls d WHERE s.booking_no=d.booking_no and s.company_id=$company_id and s.pay_mode=2 and s.status_active =1 and s.is_deleted=0 and d.status_active =1 and d.is_deleted=0 and s.item_category=4 $sample_wo_number $sample_wo_date_cond $year_cond_sample $prev_wo_ids_cond3
					group by  s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, s.insert_date, d.sample_type
					order by type";*/ 
					
					$sql = "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short, rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id, 0 as trim_group, null as description, max(b.Sensitivity) as sensitivity, 0 as uom, 0 as type, $year_field b.booking_type, sum(b.amount) as amount, sum(b.wo_qnty) as quantity 
					from wo_booking_mst a, wo_booking_dtls b 
					where 
					a.booking_no=b.booking_no 
					and a.company_id=$company_id
					and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $year_cond $prev_wo_ids_cond
					group by  a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short, a.insert_date, b.booking_type
					union all
					select s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, rtrim(xmlagg(xmlelement(e,d.id,',').extract('//text()') order by d.id).GetClobVal(),',') as id, 0 as trim_group, max(d.fabric_description) as description, 0 as sensitivity, 0 as uom, 1 as type, $year_field_sample d.sample_type as booking_type, sum(d.amount) as amount, sum(d.trim_qty) as quantity  
					FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls d WHERE s.booking_no=d.booking_no and s.company_id=$company_id and s.pay_mode=2 and s.status_active =1 and s.is_deleted=0 and d.status_active =1 and d.is_deleted=0 and s.item_category=4 $sample_wo_number $sample_wo_date_cond $year_cond_sample $prev_wo_ids_cond3
					group by  s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, s.insert_date, d.sample_type
					order by type";
					// listagg(cast(b.id as varchar(4000)),',') within group (order by b.id) as id,  listagg(cast(d.id as varchar(4000)),',') within group (order by d.id) as id,
				}
				
				//echo $sql;
				
				  $nameArray=sql_select( $sql );
				  $booking_dtls_idArr=array();
				  $booking_dtls_dataArr=array();
				  foreach ($nameArray as $row)
				  {
                                          if($db_type==0)
                                          {
                                              $wo_dtls_id[$row[csf('id')]]=$row[csf('id')];
                                              $booking_dtls_dataArr[$row[csf('booking_no')]]['id']=$row[csf('id')];
                                          }else{
                                              $wo_dtls_id[$row[csf('id')]]=$row[csf('id')]->load();
                                              $booking_dtls_dataArr[$row[csf('booking_no')]]['id']=$row[csf('id')]->load();
                                          }
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['type']=$row[csf('type')];
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['booking_type']=$row[csf('booking_type')];
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['is_short']=$row[csf('is_short')];
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['sensitivity']=$row[csf('sensitivity')];
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['amount']+=$row[csf('amount')];
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['booking_no_prefix_num']=$row[csf('booking_no_prefix_num')];
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['year']=$row[csf('year')];
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['booking_date']=$row[csf('booking_date')];
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['supplier_id']=$row[csf('supplier_id')];
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['trim_group']=$row[csf('trim_group')];
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['description']=$row[csf('description')];
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['uom']=$row[csf('uom')];
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['quantity']=$row[csf('quantity')];
					  $booking_dtls_dataArr[$row[csf('booking_no')]]['wo_id']=$row[csf('id')];
				  }
				 
				  $wo_dtls_id=array_chunk($wo_dtls_id,999);
				  $pi_qnty_cond="";
				  if(count($wo_dtls_id)>0)
				  {
				  		$pi_qnty_cond=" and";
				  }
				  foreach($wo_dtls_id as $dtls_id)
				  {
					  if($pi_qnty_cond==" and")  $pi_qnty_cond.=" (work_order_dtls_id in(".implode(',',$dtls_id).")"; else $pi_qnty_cond.=" or work_order_dtls_id in(".implode(',',$dtls_id).")";
				  }
				 if($pi_qnty_cond!=""){ $pi_qnty_cond.=")";}
				  //echo $pi_qnty_cond;die;
				 
				 $pi_qnty_sql="select work_order_no , sum(quantity) as quantity from com_pi_item_details where work_order_dtls_id>0 and status_active=1 and is_deleted=0 $pi_qnty_cond group by work_order_no";
				   //echo $pi_qnty_sql;
				  $pi_qnty_sql_result=sql_select($pi_qnty_sql);
				  $prev_qnty_arr=array();
				  foreach ($pi_qnty_sql_result as $row)
				  {
					   $prev_qnty_arr[$row[csf('work_order_no')]]['qnty']+=$row[csf('quantity')];
				  }
		  					//print_r($booking_dtls_idArr);
							//print_r( $prev_qnty_arr);
					?>
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
						<thead>
							<th width="40">SL No</th>
							<th width="60">WO No</th>
							<th width="50">Year</th>
							<th width="105">Type</th>
							<th width="80">WO Date</th>
							<th width="130">Supplier</th>
							<th width="100">Item Group</th>
							<th width="140">Item Description</th>
							<th width="105">sensitivity</th>
							<th>uom</th>
						</thead>
					 </table>
					 <div style="width:880px; max-height:250px; overflow-y:scroll">
						 <table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table" id="tbl_list_search">
						 <?
						 $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
						 $item_group_arr=return_library_array( "select id,item_name FROM lib_item_group",'id','item_name'); 
						 $i=1;
						 $conj_dtls_id_arr=array();
						 foreach ($booking_dtls_dataArr as $bookingNo=>$value)
						 {
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							if($value['type']==1) 
							{
								$booking_type_text="Sample Without Order";
							}
							else
							{
								if($value['booking_type']==5) 
								{
									$booking_type_text="Sample With Order";
								}
								else 
								{
									if($value['is_short']==1) $booking_type_text="Short"; else $booking_type_text="Main"; 
								}	
							}
							//id
							$booking_dtls_id='';
							$booking_dtls_id=implode(',',array_unique(explode(',',$value['id'])));
							//echo $value['quantity'].'>'.$prev_qnty_arr[$bookingNo]['qnty']."<br>"; 
							if( $value['quantity']> $prev_qnty_arr[$bookingNo]['qnty'])
							{
							
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$booking_dtls_id."_".$value['sensitivity']."_".$value['type']."_".$selected_based_on; ?>')"> 				
								<td width="40">
									<? echo "$i"; ?>
                                    <input type="hidden" id="woAmt_<? echo $i;?>" name="woAmt_[]" value="<? echo $value['amount']; ?>" style="width:50px;" />
								</td>	
								<td width="60" title="<? echo $booking_dtls_id;?>"><p><? echo $value['booking_no_prefix_num'];?></p></td>
								<td width="50"><p><? echo $value['year'];?></p></td>
								<td width="105"><p><? echo $booking_type_text; ?></p></td> 
								<td width="80" align="center"><p><? echo change_date_format($value['booking_date']); ?>&nbsp;</p></td>
								<td width="130"><p><? echo $supplier_arr[$value['supplier_id']]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $item_group_arr[$value['trim_group']]; ?></p></td>
								<td width="140"><p><? echo $value['description']; ?></p></td>
								<td width="105"><p><? echo $size_color_sensitive[$value['sensitivity']]; ?>&nbsp;</p></td>
								<td align="center"><p><? echo $unit_of_measurement[$value['uom']]; ?></p></td>
							</tr>
							<?
							$i++;
						 }
						
						 }
						 ?>
						</table>
					</div>  
					<table width="880" cellspacing="0" cellpadding="0" style="border:none" align="center">
						<tr>
							<td align="center" height="30" valign="bottom">
								<div style="width:100%"> 
									<div style="width:50%; float:left" align="left">
										<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
									</div>
									<div style="width:50%; float:left" align="left">
									<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
									&nbsp;Total Wo Amount : &nbsp;
                                    <input type="text" class="text_boxes_numeric" id="totWoAmt" name="totWoAmt" value="" readonly />
									</div>
								</div>
							</td>
						</tr>
					</table>
				<?
				exit();	
			}
			else
			{
				if($db_type==0)
				{
					$sql = "select b.id as id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short , b.trim_group,  b.description as description, b.Sensitivity as sensitivity, b.uom as uom, 0 as type, $year_field b.booking_type, b.amount as amount, b.wo_qnty as quantity 
					from wo_booking_mst a, wo_booking_dtls b 
				where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $year_cond $prev_wo_ids_cond
				union all
					select d.id as id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short , d.trim_group as trim_group, d.fabric_description as description, 0 as sensitivity, d.uom as uom, 1 as type, $year_field_sample d.sample_type as booking_type, d.amount as amount, d.trim_qty as quantity
					FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls d 
					WHERE s.booking_no=d.booking_no and s.company_id=$company_id and s.pay_mode=2 and s.status_active =1 and s.is_deleted=0 and d.status_active =1 and d.is_deleted=0 and s.item_category=4 $sample_wo_number $sample_wo_date_cond $year_cond_sample $prev_wo_ids_cond3
					order by type, id";
				}
				else
				{
					$sql = "select b.id as id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short , b.trim_group, null as description, b.Sensitivity as sensitivity, b.uom as uom, 0 as type, $year_field b.booking_type, b.amount as amount, b.wo_qnty as quantity 
					from wo_booking_mst a, wo_booking_dtls b 
					where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $year_cond $prev_wo_ids_cond
					union all
					select d.id as id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short , d.trim_group as trim_group, d.fabric_description as description, 0 as sensitivity, d.uom as uom, 1 as type, $year_field_sample d.sample_type as booking_type, d.amount as amount, d.trim_qty as quantity 
					FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls d WHERE s.booking_no=d.booking_no and s.company_id=$company_id and s.pay_mode=2 and s.status_active =1 and s.is_deleted=0 and d.status_active =1 and d.is_deleted=0 and s.item_category=4 $sample_wo_number $sample_wo_date_cond $year_cond_sample $prev_wo_ids_cond3";
				}
			}
			
			//echo $sql;
			
			/*$sql_prev="select b.work_order_no, b.work_order_dtls_id, b.color_id, b.item_group, b.item_description, b.size_id, b.item_color, b.item_size, b.uom, b.rate, b.brand_supplier, sum(b.quantity) as qty 
			from com_pi_master_details a, com_pi_item_details b 
			where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 
			group by b.work_order_no, b.work_order_dtls_id, b.color_id, b.item_group, b.item_description, b.size_id, b.item_color, b.item_size, b.uom, b.rate, b.brand_supplier";// and b.work_order_dtls_id in($wo_dtls_id)
			$prevData=sql_select($sql_prev);
			foreach($prevData as $rowP)
			{
				$prev_pi_qty_arr[$rowP[csf('work_order_no')]][$rowP[csf('work_order_dtls_id')]][$rowP[csf('color_id')]][$rowP[csf('item_group')]][$rowP[csf('item_description')]][$rowP[csf('size_id')]][$rowP[csf('item_color')]][$rowP[csf('item_size')]][$rowP[csf('brand_supplier')]][$rowP[csf('uom')]][$rowP[csf('rate')]]=$rowP[csf('qty')];
			}*/
			
			$pi_qnty_sql="select work_order_dtls_id , sum(quantity) as quantity from com_pi_item_details where work_order_dtls_id>0 and status_active=1 and is_deleted=0 group by work_order_dtls_id";
			//echo $pi_qnty_sql;
			$pi_qnty_sql_result=sql_select($pi_qnty_sql);
			$prev_qnty_arr=array();
			foreach ($pi_qnty_sql_result as $row)
			{
				$prev_pi_qty_arr[$row[csf('work_order_dtls_id')]]=$row[csf('quantity')];
			}
			
		}
		else
		{
			if(trim($selected_based_on)==1)
			{
				if($db_type==0)  
				{
				 	$sql = "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short, a.booking_type, group_concat(d.id) as id, 0 as trim_group, max(b.item_description) as description, max(b.sensitivity) as sensitivity, 0 as uom, 0 as type, $year_field sum(d.order_amount) as amount 
					from wo_booking_mst a, inv_trims_entry_dtls b, inv_receive_master c, inv_transaction d  
					where a.id=c.booking_id and c.id=b.mst_id and c.id=d.mst_id and d.id=b.trans_id and c.entry_form=24 and c.item_category=4 and d.pi_is_lock!=1 and c.receive_basis=2 and a.pay_mode=1 and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $year_cond $prev_wo_ids_cond3 
					group by a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short, a.insert_date, a.booking_type
					union all
					select booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, m.sample_type as booking_type, group_concat(e.id) as id, 0 as trim_group, max(d.item_description) as description, 0 as sensitivity, 0 as uom, 1 as type, $year_field_sample sum(e.order_amount) as amount 
					FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls m, inv_trims_entry_dtls d, inv_receive_master n, inv_transaction e
					WHERE s.booking_no=m.booking_no and n.id=d.mst_id  and e.id=d.trans_id and n.id=e.mst_id and e.pi_is_lock!=1 and s.id=n.booking_id and s.company_id=$company_id and s.pay_mode=1 and s.status_active =1 and s.is_deleted=0 and m.status_active =1 and m.is_deleted=0 and s.item_category=4 and n.entry_form=24 and n.item_category=4 and n.receive_basis=2 $sample_wo_number $sample_wo_date_cond $year_cond_sample $prev_wo_ids_cond4
					group by s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, s.insert_date, m.sample_type 
					order by type, id";
				}
				else
				{
					$sql = "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short, a.booking_type, listagg(cast(d.id as varchar(4000)),',') within group (order by d.id) as id, 0 as trim_group, max(b.item_description) as description, max(b.sensitivity) as sensitivity, 0 as uom, 0 as type, $year_field sum(d.order_amount) as amount 
					from wo_booking_mst a, inv_trims_entry_dtls b, inv_receive_master c, inv_transaction d 
					where a.id=c.booking_id and c.id=b.mst_id and c.id=d.mst_id and d.id=b.trans_id and c.entry_form=24 and d.pi_is_lock!=1 and c.item_category=4 and c.receive_basis=2 and a.pay_mode=1 and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $year_cond $prev_wo_ids_cond3 
					group by a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short, a.insert_date, a.booking_type
					union all
					select s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, m.sample_type as booking_type, listagg(cast(e.id as varchar(4000)),',') within group (order by e.id) as id, 0 as trim_group, max(d.item_description) as description, 0 as sensitivity, 0 as uom, 1 as type, $year_field_sample sum(e.order_amount) as amount 
					FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls m, inv_trims_entry_dtls d, inv_receive_master n, inv_transaction e
					WHERE s.booking_no=m.booking_no and n.id=d.mst_id and n.id=e.mst_id and s.id=n.booking_id and e.id=d.trans_id and s.company_id=$company_id and s.pay_mode=1 and e.pi_is_lock!=1 and s.status_active =1 and s.is_deleted=0 and m.status_active =1 and m.is_deleted=0 and s.item_category=4 and n.entry_form=24 and n.item_category=4 and n.receive_basis=2 $sample_wo_number $sample_wo_date_cond $year_cond_sample $prev_wo_ids_cond4
					group by  s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, s.insert_date, m.sample_type 
					order by type, id";
				}
				
				/*$prev_pi_qty_arr=return_library_array( "select b.work_order_id,sum(b.quantity) as qty 
				from com_pi_master_details a, com_pi_item_details b 
				where a.id=b.pi_id and a.importer_id='$importer_id' and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 $booking_id_cond 
				group by b.work_order_id", "work_order_id", "qty");
				
				$prev_retun_sql="select a.booking_id, a.prod_id, b.quantity as issue_qnty from inv_trims_issue_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.trans_id=b.trans_id and a.booking_id>0 and b.quantity>0 and b.entry_form=49";
				$prev_retun_sql_result=sql_select($prev_retun_sql);
				foreach($prev_retun_sql_result as $row)
				{
					$prev_retun_qnty[$row[csf("booking_id")]]+=$row[csf("issue_qnty")];
				}*/
			}
			else
			{
				if($db_type==0)
				{
					$sql = "select e.id as id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short, a.booking_type,  b.item_group_id as trim_group, max(b.item_description) as description, max(b.sensitivity) as sensitivity, max(e.order_uom) as uom, 0 as type, $year_field sum(e.order_amount) as amount, sum(e.order_qnty) as order_qnty, e.prod_id  
					from wo_booking_mst a, inv_trims_entry_dtls b, inv_receive_master c, inv_transaction e 
					where a.id=c.booking_id and c.id=b.mst_id and c.id=e.mst_id and e.id=b.trans_id and e.pi_is_lock!=1 and c.entry_form=24 and c.item_category=4 and c.receive_basis=2 and a.pay_mode=1 and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $year_cond $prev_wo_ids_cond4 
					group by e.id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short, a.insert_date, a.booking_type, b.item_group_id, e.prod_id 
					union all
					select e.id as id,s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, m.sample_type as booking_type, d.item_group_id as trim_group, max(d.item_description) as description, 0 as sensitivity, max(e.order_uom) as uom, 1 as type, $year_field_sample sum(e.order_amount) as amount, sum(e.order_qnty) as order_qnty , e.prod_id 
					FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls m, inv_trims_entry_dtls d, inv_receive_master n, inv_transaction e
					WHERE s.booking_no=m.booking_no and n.id=d.mst_id and n.id=e.mst_id and s.id=n.booking_id and e.id=d.trans_id and e.pi_is_lock!=1 and s.company_id=$company_id and s.pay_mode=1 and s.status_active =1 and s.is_deleted=0 and m.status_active =1 and m.is_deleted=0 and s.item_category=4 and n.entry_form=24 and n.item_category=4 and n.receive_basis=2 $sample_wo_number $sample_wo_date_cond $year_cond_sample $prev_wo_ids_cond4
					group by  e.id,s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, s.insert_date, m.sample_type, d.item_group_id, e.prod_id 
					order by type, id";
				}
				else
				{
					$sql = "select e.id as id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short, a.booking_type, b.item_group_id as trim_group, max(b.item_description) as description, max(b.sensitivity) as sensitivity, max(e.order_uom) as uom, 0 as type, $year_field sum(e.order_amount) as amount, sum(e.order_qnty) as order_qnty , e.prod_id 
					from wo_booking_mst a, inv_trims_entry_dtls b, inv_receive_master c, inv_transaction e 
					where a.id=c.booking_id and c.id=b.mst_id and c.id=e.mst_id and e.id=b.trans_id and e.pi_is_lock!=1 and c.entry_form=24 and c.item_category=4 and c.receive_basis=2 and a.pay_mode=1 and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $year_cond $prev_wo_ids_cond4 
					group by  e.id,a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.is_short, a.insert_date, a.booking_type, b.item_group_id, e.prod_id 
					union all
					select e.id as id,s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, m.sample_type as booking_type, d.item_group_id as trim_group, max(d.item_description) as description, 0 as sensitivity, max(e.order_uom) as uom, 1 as type, $year_field_sample sum(e.order_amount) as amount, sum(e.order_qnty) as order_qnty, e.prod_id 
					FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls m, inv_trims_entry_dtls d, inv_receive_master n, inv_transaction e
					WHERE s.booking_no=m.booking_no and n.id=d.mst_id and n.id=e.mst_id and s.id=n.booking_id and e.id=d.trans_id and e.pi_is_lock!=1 and s.company_id=$company_id and s.pay_mode=1 and s.status_active =1 and s.is_deleted=0 and m.status_active =1 and m.is_deleted=0 and s.item_category=4 and n.entry_form=24 and n.item_category=4 and n.receive_basis=2 $sample_wo_number $sample_wo_date_cond $year_cond_sample $prev_wo_ids_cond4
					group by  e.id,s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, s.is_short, s.insert_date, m.sample_type, d.item_group_id, e.prod_id  
					order by type, id";
				}
				
				/*$prev_pi_qty_arr=return_library_array( "select b.work_order_id,sum(b.quantity) as qty 
				from com_pi_master_details a, com_pi_item_details b 
				where a.id=b.pi_id and a.importer_id='$importer_id' and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 $booking_id_cond 
				group by b.work_order_id", "work_order_id", "qty");
				
				$prev_retun_sql="select a.booking_id, a.prod_id, b.quantity as issue_qnty from inv_trims_issue_dtls a, order_wise_pro_details b where a.id=b.dtls_id and a.trans_id=b.trans_id and a.booking_id>0 and b.quantity>0 and b.entry_form=49";
				$prev_retun_sql_result=sql_select($prev_retun_sql);
				foreach($prev_retun_sql_result as $row)
				{
					$prev_retun_qnty[$row[csf("booking_id")]][$row[csf("prod_id")]]+=$row[csf("issue_qnty")];
				}*/
			}
		}
		
		//echo $sql;
		
		
		/*$sql = "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, b.id as dtls_id, b.trim_group, c.description, b.Sensitivity as sensitivity, c.id, b.uom, 0 as type, $year_field a.booking_type, a.is_short, c.amount from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $year_cond 
			union all
				select s.booking_no_prefix_num, s.booking_no, s.booking_date, s.supplier_id, d.id as dtls_id, d.trim_group, d.fabric_description as description, 0 as sensitivity, d.id as id, d.uom, 1 as type, $year_field_sample s.booking_type, s.is_short, d.amount FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls d WHERE s.booking_no=d.booking_no and s.company_id=$company_id and s.pay_mode=2 and s.status_active =1 and s.is_deleted=0 and d.status_active =1 and d.is_deleted=0 and s.item_category=4 $sample_wo_number $sample_wo_date_cond $year_cond_sample order by type, id"; */
		
		//echo $sql;//die;
		//$result = sql_select($sql);
	
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		$item_group_arr=return_library_array( "select id,item_name FROM lib_item_group",'id','item_name');
		
		//$arr=array (2=>$supplier_arr,3=>$item_group_arr,5=>$size_color_sensitive,6=>$unit_of_measurement);
		//echo create_list_view("tbl_list_search", "WO Number, WO Date, Supplier, Item Group, Item Description, sensitivity, UOM", "80,80,160,100,170,140,60","880","250",0, $sql, "js_set_value", "id,sensitivity,type", "", 1, "0,0,supplier_id,trim_group,0,sensitivity,uom", $arr , "booking_no_prefix_num,booking_date,supplier_id,trim_group,description,sensitivity,uom", "",'','0,3,0,0,0,0,0','',1);
		
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
            <thead>
                <th width="40">SL No</th>
                <th width="60">WO No</th>
                <th width="50">Year</th>
                <th width="105">Type</th>
                <th width="80">WO Date</th>
                <th width="130">Supplier</th>
                <th width="100">Item Group</th>
                <th width="140">Item Description</th>
                <th width="105">sensitivity</th>
                <th>uom</th>
            </thead>
         </table>
         <div style="width:880px; max-height:250px; overflow-y:scroll">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table" id="tbl_list_search">
             <? 
             $i=1;
             $nameArray=sql_select( $sql );$conj_dtls_id_arr=array();
             foreach ($nameArray as $row)
             {
				
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				if($row[csf('type')]==1) 
				{
					$booking_type_text="Sample Without Order";
				}
				else
				{
					if($row[csf('booking_type')]==5) 
					{
						$booking_type_text="Sample With Order";
					}
					else 
					{
						if($row[csf('is_short')]==1) $booking_type_text="Short"; else $booking_type_text="Main"; 
					}	
				}
				
				if($goods_rcv_status==2)
				{
					/*if($row[csf('type')]==0)
					{
						$bl_qty=$row[csf('qnty')]-$prev_pi_qty_arr[$row[csf('booking_no')]][$row[csf('dtls_id')]][$row[csf('color_number_id')]][$row[csf('trim_group')]][$row[csf('description')]][$row[csf('size_id')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('brand_supplier')]][$row[csf('uom')]][$row[csf('rate')]];
					}
					else
					{
						$bl_qty=$row[csf('qnty')]-$prev_pi_qty_arr[$row[csf('dtls_id')]];
					}*/
					
					$bl_qty=$row[csf('quantity')]-$prev_pi_qty_arr[$row[csf('id')]];
					if($bl_qty>0)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$row[csf('id')]."_".$row[csf('sensitivity')]."_".$row[csf('type')]."_".$selected_based_on; ?>')"> 				
							<td width="40">
							<? echo "$i"; ?>
							<input type="hidden" id="woAmt_<? echo $i;?>" name="woAmt_[]" value="<? echo number_format($row[csf('amount')],4,'.',''); ?>" style="width:50px;" />
							</td>	
							<td width="60"><p><? echo $row[csf('booking_no_prefix_num')];?></p></td>
							<td width="50"><p><? echo $row[csf('year')];?></p></td>
							<td width="105"><p><? echo $booking_type_text; ?></p></td> 
							<td width="80" align="center"><p><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</p></td>
							<td width="130"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $item_group_arr[$row[csf('trim_group')]]; ?></p></td>
							<td width="140"><p><? echo $row[csf('description')]; ?></p></td>
							<td width="105"><p><? echo $size_color_sensitive[$row[csf('sensitivity')]]; ?>&nbsp;</p></td>
							<td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				else
				{
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$row[csf('id')]."_".$row[csf('sensitivity')]."_".$row[csf('type')]."_".$selected_based_on; ?>')"> 				
                        <td width="40">
						<? echo "$i"; ?>
                        <input type="hidden" id="woAmt_<? echo $i;?>" name="woAmt_[]" value="<? echo $row[csf('amount')]; ?>" style="width:50px;" />
                        </td>	
                        <td width="60"><p><? echo $row[csf('booking_no_prefix_num')];?></p></td>
                        <td width="50"><p><? echo $row[csf('year')];?></p></td>
                        <td width="105"><p><? echo $booking_type_text; ?></p></td> 
                        <td width="80" align="center"><p><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</p></td>
                        <td width="130"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                        <td width="100"><p><? echo $item_group_arr[$row[csf('trim_group')]]; ?></p></td>
                        <td width="140"><p><? echo $row[csf('description')]; ?></p></td>
                        <td width="105"><p><? echo $size_color_sensitive[$row[csf('sensitivity')]]; ?>&nbsp;</p></td>
                        <td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    </tr>
                    <?
             		$i++;
				}
				
				/*if($conj_dtls_id_arr[$row[csf('id')]]=="")
				{
					$conj_dtls_id_arr[$row[csf('id')]]=$row[csf('id')];
					
				}*/
               
             }
             ?>
            </table>
        </div>  
		<table width="880" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%"> 
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        &nbsp;Total Wo Amount : &nbsp; <input type="text" class="text_boxes_numeric" id="totWoAmt" name="totWoAmt" value="" readonly />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	<?
	}
	
	else if($item_category_id==12)
	{
		if ($data[0]!="") $wo_number=" and a.booking_no like '%".trim($data[0])."'"; else { $wo_number = ''; }
		
		if ($data[0]!="") $wo_sam_number=" and a.wo_no like '%".trim($data[0])."'"; else { $wo_sam_number = ''; }
		
		/*$sql = "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, b.id, b.process, b.uom, b.color_size_table_id, b.fabric_color_id, b.item_size, c.fabric_description from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fab_conv_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company_id and a.item_category=$item_category_id and a.pay_mode=2 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond order by a.id";*/
		if($selected_based_on==1)
		{
			if($db_type==0)
			{
				$sql = "select a.id as mst_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, group_concat(b.id) as id, sum(b.wo_qnty) as wo_qnty, 1 as type 
				from wo_booking_mst a, wo_booking_dtls b
				where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category=$item_category_id and a.pay_mode=2 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond
				group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id
				union all
				select a.id as mst_id, a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no, a.booking_date, a.supplier_id, group_concat(b.id) as id, sum(b.wo_qty) as wo_qnty, 2 as type  
				from wo_non_ord_aop_booking_mst a, wo_non_ord_aop_booking_dtls b 
				where a.id=b.wo_id and a.company_id=$company_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_sam_number $wo_date_cond $prev_wo_ids_cond 
				group by a.id, a.wo_no_prefix_num, a.wo_no, a.booking_date, a.supplier_id
				order by mst_id";
			}
			else
			{
				$sql = "select a.id as mst_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as id, sum(b.wo_qnty) as wo_qnty, 1 as type 
				from wo_booking_mst a, wo_booking_dtls b 
				where a.booking_no=b.booking_no and a.company_id=$company_id and a.item_category=$item_category_id and a.pay_mode=2 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond
				group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id  
				union all
				select a.id as mst_id, a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no, a.booking_date, a.supplier_id, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as id, sum(b.wo_qty) as wo_qnty, 2 as type  
				from wo_non_ord_aop_booking_mst a, wo_non_ord_aop_booking_dtls b 
				where a.id=b.wo_id and a.company_id=$company_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_sam_number $wo_date_cond $prev_wo_ids_cond 
				group by a.id, a.wo_no_prefix_num, a.wo_no, a.booking_date, a.supplier_id
				order by mst_id";
			}
			
			$prev_pi_qnty_arr=return_library_array("select b.work_order_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_id",'work_order_id','quantity');
			
		}
		else
		{
			$sql = "select a.id as mst_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, b.id, b.process, b.uom, b.fabric_color_id as fabric_color_id, b.item_size, c.fabric_description, b.gmts_size, b.gmts_color_id, b.wo_qnty, 1 as type 
			from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fab_conv_cost_dtls c 
			where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company_id and a.item_category=$item_category_id and a.pay_mode=2 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond  
			union all
			select a.id as mst_id, a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no, a.booking_date, a.supplier_id, b.id, 35 as process, b.uom, 0 as fabric_color_id, null as item_size, null as fabric_description, 0 as gmts_size, 0 as gmts_color_id, b.wo_qty as wo_qnty, 2 as type  
			from wo_non_ord_aop_booking_mst a, wo_non_ord_aop_booking_dtls b 
			where a.id=b.wo_id and a.company_id=$company_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_sam_number $wo_date_cond $prev_wo_ids_cond order by mst_id";
			
			$prev_pi_qnty_arr=return_library_array("select b.work_order_dtls_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_dtls_id",'work_order_dtls_id','quantity');
			
		}
		
		
		 
		//, b.color_size_table_id, 0 as color_size_table_id
		//echo $sql;die;
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		
		/*$sizeColor_arr=array();
		$col_size=sql_select( "select id, color_number_id, size_number_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0" );
		foreach($col_size as $csRow)
		{
			$sizeColor_arr[$csRow[csf('id')]]['color']=$csRow[csf('color_number_id')];
			$sizeColor_arr[$csRow[csf('id')]]['size']=$csRow[csf('size_number_id')];
		}*/
		
		$desc_arr=array();
		$descArrray=sql_select( "select id, body_part_id, color_type_id, construction, composition from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0");
		foreach($descArrray as $dRow)
		{
			$descArrray[$dRow[csf('id')]]['bp']=$dRow[csf('body_part_id')];
			$descArrray[$dRow[csf('id')]]['ct']=$dRow[csf('color_type_id')];
			$descArrray[$dRow[csf('id')]]['cons']=$dRow[csf('construction')];
			$descArrray[$dRow[csf('id')]]['comp']=$dRow[csf('composition')];
		}
		
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
            <thead>
                <th width="40">SL No</th>
                <th width="60">WO No</th>
                <th width="70">WO Date</th>
                <th width="120">Supplier</th>
                <th width="80">process</th>
                <th width="160">Description</th>
                <th width="80">Gmts Color</th>
                <th width="80">Item Color</th>
                <th width="60">Gmts Size</th>
                <th width="60">Item Size</th>
                <th>uom</th>
            </thead>
         </table>
         <div style="width:888px; max-height:250px; overflow-y:scroll">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" id="tbl_list_search">
             <? 
             $i=1;
             $nameArray=sql_select( $sql );
             foreach ($nameArray as $selectResult)
             {
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
                    
                //$col_size=sql_select( "select color_number_id, size_number_id from wo_po_color_size_breakdown where id='".$selectResult[csf('color_size_table_id')]."'" );
				
				if($selectResult[csf('fabric_description')]==0)
				{
					$desc="All Fabrics";
				}
				else
				{
					//$descArrray=sql_select( "select body_part_id, color_type_id, construction, composition from wo_pre_cost_fabric_cost_dtls where id='".$selectResult[csf('fabric_description')]."'" );
					//$desc=$body_part[$descArrray[0][csf('body_part_id')]].", ".$color_type[$descArrray[0][csf('color_type_id')]].", ".$descArrray[0][csf('construction')].", ".$descArrray[0][csf('composition')];
					
					$desc=$body_part[$descArrray[$selectResult[csf('fabric_description')]]['bp']].", ".$color_type[$descArrray[$selectResult[csf('fabric_description')]]['ct']].", ".$descArrray[$selectResult[csf('fabric_description')]]['cons'].", ".$descArrray[$selectResult[csf('fabric_description')]]['comp'];
				}
				
				if($selected_based_on==1)
				{
					$bal_qnty=$selectResult[csf('wo_qnty')]-$prev_pi_qnty_arr[$selectResult[csf('mst_id')]];
				}
				else
				{
					$bal_qnty=$selectResult[csf('wo_qnty')]-$prev_pi_qnty_arr[$selectResult[csf('id')]];
				}
				
				if($bal_qnty>0)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$selectResult[csf('id')]."*".$selectResult[csf('type')]; ?>')"> 				
						<td width="40"><? echo "$i"; ?></td>	
						<td width="60"><p><? echo $selectResult[csf('booking_no_prefix_num')];?></p></td>
						<td width="70"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?>&nbsp;</p></td> 
						<td width="120"><p><? echo $supplier_arr[$selectResult[csf('supplier_id')]]; ?>&nbsp;</p></td>
						<td width="80"><p><? echo $conversion_cost_head_array[$selectResult[csf('process')]]; ?>&nbsp;</p></td>
						<td width="160"><p><? echo $desc; ?>&nbsp;</p></td>
						<td width="80"><p><? echo $color_library[$selectResult[csf('gmts_color_id')]]; ?>&nbsp;</p></td>
						<td width="80"><p><? echo $color_library[$selectResult[csf('fabric_color_id')]]; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $size_library[$selectResult[csf('gmts_size')]]; ?>&nbsp;</p></td>
						<td width="60"><p><? echo $selectResult[csf('item_size')]; ?>&nbsp;</p></td>
						<td><p><? echo $unit_of_measurement[$selectResult[csf('uom')]]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
             }
             ?>
            </table>
        </div>  
		<table width="870" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	}
	
	else if($item_category_id==24)
	{
		if ($data[0]!="") $wo_number=" and a.ydw_no like '%".trim($data[0])."'"; else { $wo_number = ''; }
		
		
		if($selected_based_on==1)
		{
			if($goods_rcv_status==2)
			{
				if($db_type==0)
				{
					$sql = "select a.id as mst_id, a.yarn_dyeing_prefix_num, a.ydw_no, a.booking_date, a.supplier_id, group_concat(b.id) as id, sum(b.yarn_wo_qty) as yarn_wo_qty 
					from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b 
					where a.id=b.mst_id and a.company_id=$company_id and a.item_category_id=$item_category_id and a.pay_mode=2 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond 
					group by  a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.booking_date, a.supplier_id
					order by a.id";
				}
				else
				{
					$sql = "select a.id as mst_id, a.yarn_dyeing_prefix_num, a.ydw_no, a.booking_date, a.supplier_id, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as id, sum(b.yarn_wo_qty) as yarn_wo_qty  
					from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b 
					where a.id=b.mst_id and a.company_id=$company_id and a.item_category_id=$item_category_id and a.pay_mode=2 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond 
					group by  a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.booking_date, a.supplier_id
					order by a.id"; 
				}
				
				$prev_pi_qnty_arr=return_library_array("select b.work_order_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_id",'work_order_id','quantity');
				 
			} 
			else
			{
				if($db_type==0)
				{
					$sql = "select a.id as mst_id, a.yarn_dyeing_prefix_num, a.ydw_no, a.booking_date, a.supplier_id, group_concat(c.id) as id 
					from wo_yarn_dyeing_mst a, inv_receive_master b, inv_transaction c, product_details_master d
					where a.id=b.booking_id and b.id=c.mst_id and b.entry_form=1 and b.receive_purpose=2 and c.item_category=1 and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id=1 and a.company_id=$company_id and a.item_category_id=$item_category_id and a.pay_mode=2 and c.pi_is_lock<>1 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond2
					group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.booking_date, a.supplier_id
					order by a.id";
				}
				else
				{
					$sql = "select a.id as mst_id, a.yarn_dyeing_prefix_num, a.ydw_no, a.booking_date, a.supplier_id, listagg(cast(c.id as varchar(4000)),',') within group(order by c.id) as id 
					from wo_yarn_dyeing_mst a, inv_receive_master b, inv_transaction c, product_details_master d
					where a.id=b.booking_id and b.id=c.mst_id and b.entry_form=1 and b.receive_purpose=2 and c.item_category=1 and c.prod_id=d.id and b.receive_basis=2 and d.item_category_id=1 and a.company_id=$company_id and a.item_category_id=$item_category_id and a.pay_mode=2 and c.pi_is_lock<>1 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond2
					group by a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.booking_date, a.supplier_id 
					order by a.id";
				}
				 
			}
		}
		else
		{
			if($goods_rcv_status==2)
			{
				$sql = "select a.id as mst_id, a.yarn_dyeing_prefix_num, a.ydw_no, a.booking_date, a.supplier_id, b.id, b.product_id, b.job_no, b.uom, b.yarn_description, b.yarn_color, b.color_range 
				from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.item_category_id=$item_category_id and a.pay_mode=2 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond order by a.id";
				
				$prev_pi_qnty_arr=return_library_array("select b.work_order_dtls_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_dtls_id",'work_order_dtls_id','quantity');  
			} 
			else
			{
				$sql = "select a.id as mst_id, a.yarn_dyeing_prefix_num, a.ydw_no, a.booking_date, a.supplier_id, c.id, c.prod_id as product_id, c.job_no, c.order_uom as uom, d.product_name_details as yarn_description, d.color as yarn_color, 0 as color_range from wo_yarn_dyeing_mst a, inv_receive_master b, inv_transaction c, product_details_master d
				 where a.id=b.booking_id and b.id=c.mst_id and b.entry_form=1 and b.receive_purpose=2 and c.item_category=1 and c.prod_id=d.id and b.receive_basis=2 and c.pi_is_lock<>1 and d.item_category_id=1 and a.company_id=$company_id and a.item_category_id=$item_category_id and a.pay_mode=2 and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond2 order by a.id"; 
			}
		}
		
		
		
		//echo $sql;die;
		//$result = sql_select($sql);
	
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		$lot_arr=return_library_array( "select id, lot from product_details_master where item_category_id=1",'id','lot');
		$color_array=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
            <thead>
                <th width="40">SL No</th>
                <th width="70">WO No</th>
                <th width="80">WO Date</th>
                <th width="130">Supplier</th>
                <th width="100">Job No</th>
                <th width="80">Lot</th>
                <th width="150">Yarn Description</th>
                <th width="80">Yarn Color</th>
                <th width="80">Color_range</th>
                <th>UOM</th>
            </thead>
         </table>
         <div style="width:888px; max-height:250px; overflow-y:scroll">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" id="tbl_list_search">
             <? 
             $i=1;
             $nameArray=sql_select( $sql );
             foreach ($nameArray as $selectResult)
             {
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
             	
				if($goods_rcv_status==2)
				{
					if($selected_based_on==1)
					{
						$bal_quantity=$selectResult[csf("yarn_wo_qty")]-$prev_pi_qnty_arr[$selectResult[csf("mst_id")]];
					}
					else
					{
						$bal_quantity=$selectResult[csf("yarn_wo_qty")]-$prev_pi_qnty_arr[$selectResult[csf("id")]];
					}
				}
				else
				{
					$bal_quantity=1;
				}
				
				if($bal_quantity>0)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$selectResult[csf('id')]; ?>')"> 				
						<td width="40"><? echo "$i"; ?></td>	
						<td width="70"><p><? echo $selectResult[csf('yarn_dyeing_prefix_num')];?></p></td>
						<td width="80"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?>&nbsp;</p></td> 
						<td width="130"><p><? echo $supplier_arr[$selectResult[csf('supplier_id')]]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $selectResult[csf('job_no')]; ?>&nbsp;</p></td>
						<td width="80"><p><? echo $lot_arr[$selectResult[csf('product_id')]]; ?>&nbsp;</p></td>
						<td width="150"><p><? echo $selectResult[csf('yarn_description')]; ?>&nbsp;</p></td>
						<td width="80"><p><? echo $color_array[$selectResult[csf('yarn_color')]]; ?>&nbsp;</p></td>
						<td width="80"><p><? echo $color_range[$selectResult[csf('color_range')]]; ?>&nbsp;</p></td>
						<td><p><? echo $unit_of_measurement[$selectResult[csf('uom')]]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
             }
             ?>
            </table>
        </div>  
		<table width="870" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	}
	
	else if($item_category_id==25)
	{
		if ($data[0]!="") $wo_number=" and a.booking_no like '%".trim($data[0])."'"; else { $wo_number = ''; }
		
		if($selected_based_on==1)
		{
			if($db_type==0)
			{
				$sql = "select a.id as mst_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.buyer_id, group_concat(b.id) as id, sum(b.wo_qnty) as wo_qnty 
				from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c 
				where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond
				group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.buyer_id 
				order by a.id"; 
			}
			else
			{
				$sql = "select a.id as mst_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.buyer_id, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as id, sum(b.wo_qnty) as wo_qnty 
				from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c 
				where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond
				group by a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.buyer_id 
				order by a.id"; 
			}
			$prev_pi_qnty_arr=return_library_array("select b.work_order_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_id",'work_order_id','quantity');
		}
		else
		{
			$sql = "select a.id as mst_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.supplier_id, a.buyer_id, b.id, b.gmts_color_id, b.gmt_item, c.emb_name, c.emb_type, b.wo_qnty as wo_qnty 
			from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_embe_cost_dtls c 
			where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=$company_id and a.item_category=$item_category_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond 
			order by a.id";
			$prev_pi_qnty_arr=return_library_array("select b.work_order_dtls_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_dtls_id",'work_order_dtls_id','quantity'); 
		}
		
		//echo $sql;//die;
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
            <thead>
                <th width="40">SL No</th>
                <th width="70">WO No</th>
                <th width="80">WO Date</th>
                <th width="120">Supplier</th>
                <th width="70">Buyer</th>
                <th width="150">Gmts Item</th>
                <th width="90">Embell. Name</th>
                <th width="100">Embell. Type</th>
                <th>Gmts Color</th>
            </thead>
         </table>
         <div style="width:888px; max-height:250px; overflow-y:scroll">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" id="tbl_list_search">
             <? 
             $i=1;
             $nameArray=sql_select( $sql );
             foreach ($nameArray as $selectResult)
             {
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
             	$bal_qnty=0;
				if($selected_based_on==1)
				{
					$bal_qnty=$selectResult[csf('wo_qnty')]-$prev_pi_qnty_arr[$selectResult[csf('mst_id')]];
				}
				else
				{
					$bal_qnty=$selectResult[csf('wo_qnty')]-$prev_pi_qnty_arr[$selectResult[csf('id')]];
				}
				if($bal_qnty>0)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$selectResult[csf('id')]; ?>')"> 				
						<td width="40"><? echo "$i"; ?></td>	
						<td width="70"><p><? echo $selectResult[csf('booking_no_prefix_num')];?></p></td>
						<td width="80"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?>&nbsp;</p></td> 
						<td width="120"><p><? echo $supplier_arr[$selectResult[csf('supplier_id')]]; ?>&nbsp;</p></td>
						<td width="70"><p><? echo $buyer_arr[$selectResult[csf('buyer_id')]]; ?>&nbsp;</p></td>
						<td width="150"><p><? echo $garments_item[$selectResult[csf('gmt_item')]]; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $emblishment_name_array[$selectResult[csf('emb_name')]]; ?>&nbsp;</p></td>
						<td width="100">
							<p>
								<? 
									if($selectResult[csf('emb_name')]==1) echo $emblishment_print_type[$selectResult[csf('emb_type')]];
									else if($selectResult[csf('emb_name')]==2) echo $emblishment_embroy_type[$selectResult[csf('emb_type')]];
									else if($selectResult[csf('emb_name')]==3) echo $emblishment_wash_type[$selectResult[csf('emb_type')]];
									else if($selectResult[csf('emb_name')]==4) echo $emblishment_spwork_type[$selectResult[csf('emb_type')]]; 
								?>
								&nbsp;
							</p>
						</td>
						<td><p><? echo $color_library[$selectResult[csf('gmts_color_id')]]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
             }
             ?>
            </table>
        </div>  
		<table width="870" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	}
	
	else if($item_category_id==31)
	{
		if ($data[0]!="") $wo_number=" and a.labtest_no like '%".trim($data[0])."'"; else { $wo_number = ''; }
		
		if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		else $year_field="";//defined Later
		if($selected_based_on==1)
		{
			if($db_type==0)
			{
				$sql = "select a.id as mst_id, a.labtest_prefix_num, a.labtest_no, a.wo_date, a.supplier_id, $year_field, group_concat(b.id) as id, sum(b.wo_value) as wo_value 
				from wo_labtest_mst a, wo_labtest_dtls b 
				where a.id=b.mst_id and a.company_id=$company_id and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond
				group by a.id, a.labtest_prefix_num, a.labtest_no, a.wo_date, a.supplier_id,a.insert_date
				 order by a.id";
			}
			else
			{
				$sql = "select a.id as mst_id, a.labtest_prefix_num, a.labtest_no, a.wo_date, a.supplier_id, $year_field, listagg(cast(b.id as varchar(4000)),',') within group (order by b.id) as id, sum(b.wo_value) as wo_value
				from wo_labtest_mst a, wo_labtest_dtls b 
				where a.id=b.mst_id and a.company_id=$company_id and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond 
				group by a.id, a.labtest_prefix_num, a.labtest_no, a.wo_date, a.supplier_id, a.insert_date
				order by a.id";
			}
			
			$prev_pi_qnty_arr=return_library_array("select b.work_order_id, sum(b.amount) as amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_id",'work_order_id','amount');
			 
		}
		else
		{
			$sql = "select a.id as mst_id, a.labtest_prefix_num, a.labtest_no, a.wo_date, a.supplier_id, $year_field, b.id, b.test_for, b.test_item_id, b.color, b.wo_value 
			from wo_labtest_mst a, wo_labtest_dtls b 
			where a.id=b.mst_id and a.company_id=$company_id and a.supplier_id like '$supplier_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond order by a.id"; 
			
			$prev_pi_qnty_arr=return_library_array("select b.work_order_dtls_id, sum(b.amount) as amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_dtls_id",'work_order_dtls_id','amount');
		}
		
		
		//echo $sql; 
		
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		$test_item_arr=return_library_array( "SELECT id,test_item FROM lib_lab_test_rate_chart",'id','test_item');
		
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
            <thead>
                <th width="40">SL No</th>
                <th width="70">WO No</th>
                <th width="60">Year</th>
                <th width="80">WO Date</th>
                <th width="170">Supplier</th>
                <th width="120">Test For</th>
                <th width="200">Test Item</th>
                <th>Color</th>
            </thead>
         </table>
         <div style="width:890px; max-height:250px; overflow-y:scroll">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" id="tbl_list_search">
             <? 
             $i=1;
             $nameArray=sql_select( $sql );
             foreach ($nameArray as $selectResult)
             {
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";	
					
				$test_item='';
				$test_item_ids=array_unique(explode(",",$selectResult[csf('test_item_id')]));
				foreach($test_item_ids as $test_item_id)
				{
					$test_item.=$test_item_arr[$test_item_id].",";
				}
				$test_item=chop($test_item,',');
				$bl_amt=0;
				if($selected_based_on==1)
				{
					$bl_amt=$selectResult[csf("wo_value")]-$prev_pi_qnty_arr[$selectResult[csf("mst_id")]];
				}
				else
				{
					$bl_amt=$selectResult[csf("wo_value")]-$prev_pi_qnty_arr[$selectResult[csf("id")]];
				}
				if($bl_amt>0)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$selectResult[csf('id')]; ?>')"> 				
						<td width="40"><? echo "$i"; ?></td>	
						<td width="70"><p><? echo $selectResult[csf('labtest_prefix_num')];?></p></td>
						<td width="60" align="center"><p><? echo $selectResult[csf('year')];?></p></td>
						<td width="80" align="center"><p><? echo change_date_format($selectResult[csf('wo_date')]); ?>&nbsp;</p></td> 
						<td width="170"><p><? echo $supplier_arr[$selectResult[csf('supplier_id')]]; ?>&nbsp;</p></td>
						<td width="120"><p><? echo $test_for[$selectResult[csf('test_for')]]; ?>&nbsp;</p></td>
						<td width="200"><p><? echo $test_item; ?>&nbsp;</p></td>
						<td><p><? echo $color_library[$selectResult[csf('color')]]; ?>&nbsp;</p></td>
					</tr>
					 <?
					 $i++;
				}
             }
             ?>
            </table>
        </div>  
		<table width="870" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
	}
	
	else
	{
		/*$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id) group by b.work_order_dtls_id", "work_order_dtls_id", "qty");
		
		$rcv_booking_arr=return_library_array("select id, booking_id from  inv_receive_master where entry_form=20 and receive_basis=2","id","booking_id");
		
		$prev_retun_sql="select a.received_id, b.prod_id, b.cons_quantity from inv_issue_master a, inv_transaction b 
		where a.id=b.mst_id and a.entry_form=26 and b.transaction_type=3 and a.received_id>0";
		$prev_retun_sql_result=sql_select($prev_retun_sql);
		foreach($prev_retun_sql_result as $row)
		{
			$prev_retun_qnty[$rcv_booking_arr[$row[csf("received_id")]]][$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
		}*/
		
		if ($data[0]!="") $wo_number=" and a.wo_number like '%".trim($data[0])."'"; else { $wo_number = ''; }
		
		/*if($goods_rcv_status==2)
		{
			$sql_data="select a.id, a.wo_number, a.wo_date, a.supplier_id, b.id as dtls_id, b.item_id, b.uom, b.supplier_order_quantity, b.rate, b.amount, c.item_group_id, c.item_description, c.item_size, 0 as trans_id 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c 
			where a.id=b.mst_id and b.item_id=c.id and b.is_deleted=0 and b.id in($wo_dtls_id)";
		}
		else
		{
			$sql_data="select a.id, a.wo_number, a.wo_date, a.supplier_id, b.id as dtls_id, b.item_id, b.uom, d.cons_quantity as supplier_order_quantity, d.cons_rate as rate, d.cons_amount as amount, e.item_group_id, e.item_description, e.item_size, d.id as trans_id 
			from wo_non_order_info_mst a, wo_non_order_info_dtls b, inv_receive_master c, inv_transaction d, product_details_master e 
			where a.id=b.mst_id and b.mst_id=c.booking_id and c.id=d.mst_id and d.prod_id=e.id and d.prod_id=b.item_id and c.entry_form=20 and c.receive_basis=2 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.is_deleted=0 and b.id in($wo_dtls_id)";
		}*/
		if($item_category_id==11) $wo_item_category_id="4,11";
		
		if($selected_based_on==1)
		{
			if($goods_rcv_status==2)
			{
				
				if($db_type==0)
				{
					$sql="select a.id as mst_id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.item_category, a.supplier_id, group_concat(b.id) as dtls_id, sum(b.supplier_order_quantity) as wo_qnty 
					from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c 
					where a.id=b.mst_id and b.item_id=c.id and a.company_name=$company_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id in($wo_item_category_id) and c.item_category_id in($wo_item_category_id) $wo_number $wo_date_cond $prev_wo_ids_cond
					group by a.id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.item_category, a.supplier_id";
				}
				else
				{
					$sql="select a.id as mst_id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.item_category, a.supplier_id, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as dtls_id, sum(b.supplier_order_quantity) as wo_qnty 
					from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c 
					where a.id=b.mst_id and b.item_id=c.id and a.company_name=$company_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id in($wo_item_category_id) and c.item_category_id in($wo_item_category_id) $wo_number $wo_date_cond $prev_wo_ids_cond
					group by a.id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.item_category, a.supplier_id";
				}
				
				$prev_pi_qnty_arr=return_library_array("select b.work_order_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_id",'work_order_id','quantity');
				 
			}
			else
			{
				
				if($db_type==0)
				{
					$sql="select a.id as mst_id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.item_category, a.supplier_id, group_concat(d.id) as dtls_id, sum(d.cons_quantity) as supplier_order_quantity
					from wo_non_order_info_mst a, wo_non_order_info_dtls b, inv_receive_master c, inv_transaction d, product_details_master e 
					where a.id=b.mst_id and b.mst_id=c.booking_id and c.id=d.mst_id and d.prod_id=e.id and d.prod_id=b.item_id and c.entry_form=20 and e.entry_form=20 and a.company_name=$company_id and a.supplier_id like '$supplier_id' and a.pay_mode=1 and d.pi_is_lock!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.item_category_id in($wo_item_category_id) and e.item_category_id in($wo_item_category_id) $wo_number $wo_date_cond $prev_wo_ids_cond3
					group by a.id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.item_category, a.supplier_id";
				}
				else
				{
					$sql="select a.id as mst_id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.item_category, a.supplier_id, listagg(cast(d.id as varchar(4000)),',') within group(order by d.id) as dtls_id, sum(d.cons_quantity) as supplier_order_quantity
					from wo_non_order_info_mst a, wo_non_order_info_dtls b, inv_receive_master c, inv_transaction d, product_details_master e 
					where a.id=b.mst_id and b.mst_id=c.booking_id and c.id=d.mst_id and d.prod_id=e.id and d.prod_id=b.item_id and c.entry_form=20 and e.entry_form=20 and a.company_name=$company_id and a.supplier_id like '$supplier_id' and a.pay_mode=1 and d.pi_is_lock!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.item_category_id in($wo_item_category_id) and e.item_category_id in($wo_item_category_id) $wo_number $wo_date_cond $prev_wo_ids_cond3
					group by a.id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.item_category, a.supplier_id";
				}
				 
			}
		}
		else
		{
			if($goods_rcv_status==2)
			{
				
				$sql="select a.id as mst_id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.item_category, a.supplier_id, b.id as dtls_id, b.item_id, b.uom, b.supplier_order_quantity, c.item_group_id, c.item_description, c.item_size, b.supplier_order_quantity as wo_qnty  
				from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c 
				where a.id=b.mst_id and b.item_id=c.id and a.company_name=$company_id and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id= in($wo_item_category_id) and c.item_category_id in($wo_item_category_id) $wo_number $wo_date_cond $prev_wo_ids_cond"; 
				
				$prev_pi_qnty_arr=return_library_array("select b.work_order_dtls_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_dtls_id",'work_order_dtls_id','quantity');
				
			}
			else
			{
				$sql="select a.id as mst_id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.item_category, a.supplier_id, d.id as dtls_id, b.item_id, b.uom, d.cons_quantity as supplier_order_quantity, e.item_group_id, e.item_description, e.item_size
				from wo_non_order_info_mst a, wo_non_order_info_dtls b, inv_receive_master c, inv_transaction d, product_details_master e 
				where a.id=b.mst_id and b.mst_id=c.booking_id and c.id=d.mst_id and d.prod_id=e.id and d.prod_id=b.item_id and c.entry_form=20 and e.entry_form=20 and a.company_name=$company_id and a.supplier_id like '$supplier_id' and a.pay_mode=1 and d.pi_is_lock!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.item_category_id in($wo_item_category_id) and c.item_category_id in($wo_item_category_id) $wo_number $wo_date_cond $prev_wo_ids_cond3"; 
			}
		}
		
		
		
		//echo $sql;
		$data_array=sql_select($sql);
		
		/*$data_result=$transaction_check=array();
		foreach($data_array as $row)
		{
			//$i++;
			if($row[csf("trans_id")]==0)
			{
				$data_result[$row[csf("dtls_id")]]["id"]=$row[csf("dtls_id")];
				$data_result[$row[csf("dtls_id")]]["wo_number_prefix_num"]=$row[csf("wo_number_prefix_num")];
				$data_result[$row[csf("dtls_id")]]["wo_date"]=$row[csf("wo_date")];
				$data_result[$row[csf("dtls_id")]]["item_category"]=$row[csf("item_category")];
				$data_result[$row[csf("dtls_id")]]["supplier_id"]=$row[csf("supplier_id")];
				$data_result[$row[csf("dtls_id")]]["item_id"]=$row[csf("item_id")];
				$data_result[$row[csf("dtls_id")]]["uom"]=$row[csf("uom")];
				$data_result[$row[csf("dtls_id")]]["supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
				$data_result[$row[csf("dtls_id")]]["item_group_id"]=$row[csf("item_group_id")];
				$data_result[$row[csf("dtls_id")]]["item_description"]=$row[csf("item_description")];
				$data_result[$row[csf("dtls_id")]]["item_size"]=$row[csf("item_size")];
			}
			else
			{
				if($transaction_check[$row[csf("trans_id")]]=="")
				{
					$transaction_check[$row[csf("trans_id")]]=$row[csf("trans_id")];
					
					$data_result[$row[csf("dtls_id")]]["id"]=$row[csf("dtls_id")];
					$data_result[$row[csf("dtls_id")]]["wo_number_prefix_num"]=$row[csf("wo_number_prefix_num")];
					$data_result[$row[csf("dtls_id")]]["wo_date"]=$row[csf("wo_date")];
					$data_result[$row[csf("dtls_id")]]["item_category"]=$row[csf("item_category")];
					$data_result[$row[csf("dtls_id")]]["supplier_id"]=$row[csf("supplier_id")];
					$data_result[$row[csf("dtls_id")]]["item_id"]=$row[csf("item_id")];
					$data_result[$row[csf("dtls_id")]]["uom"]=$row[csf("uom")];
					$data_result[$row[csf("dtls_id")]]["supplier_order_quantity"]+=$row[csf("supplier_order_quantity")];
					$data_result[$row[csf("dtls_id")]]["item_group_id"]=$row[csf("item_group_id")];
					$data_result[$row[csf("dtls_id")]]["item_description"]=$row[csf("item_description")];
					$data_result[$row[csf("dtls_id")]]["item_size"]=$row[csf("item_size")];
				}
				//echo "$i=";
			}
		}*/
		
		//echo $sql;//die;
		
		/*$sql="select a.wo_number_prefix_num, a.wo_number, a.wo_date, a.item_category, a.supplier_id, b.id, b.item_id, b.uom, c.item_group_id, c.item_description, c.item_size from wo_non_order_info_mst a, wo_non_order_info_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and a.company_name=$company_id and a.item_category in(4,11) and a.supplier_id like '$supplier_id' and a.pay_mode=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $prev_wo_ids_cond order by a.id"; */ 
		//echo $sql;die;
		
		$result = sql_select($sql);
		$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
		$item_group_arr=return_library_array( "select id,item_name FROM lib_item_group",'id','item_name');
		
		
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
            <thead>
                <th width="40">SL No</th>
                <th width="70">WO Number</th>
                <th width="80">WO Date</th>
                <th width="140">Supplier</th>
                <th width="90">Item Category</th>
                <th width="120">Item Group</th>
                <th width="160">Item Description</th>
                <th width="80">Item Size</th>
                <th>uom</th>
            </thead>
         </table>
         <div style="width:880px; max-height:250px; overflow-y:scroll">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table" id="tbl_list_search">
             <? 
             $i=1;
			 
            /* foreach ($data_result as $row)
             {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$bl_qty=0;
				
				if($goods_rcv_status==2)
				{
					$bl_qty=$row[('supplier_order_quantity')]-$prev_pi_qty_arr[$row[('dtls_id')]];
				}
				else
				{
					$bl_qty=($row[('supplier_order_quantity')]-($prev_retun_qnty[$row[("dtls_id")]][$row[("item_id")]]+$prev_pi_qty_arr[$row[('dtls_id')]]));
				}
				
				if($bl_qty>0)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$row[('dtls_id')]; ?>')"> 				
                        <td width="40"><? echo "$i"; ?></td>	
                        <td width="70"><p><? echo $row[csf('wo_number_prefix_num')];?></p></td>
                        <td width="80"><p><? echo change_date_format($row[csf('wo_date')]);?></p></td>
                        <td width="140"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td> 
                        <td width="90" align="center"><p><? echo $item_category[$row[csf('item_category')]]; ?>&nbsp;</p></td>
                        <td width="120"><p><? echo $item_group_arr[$row[csf('item_group_id')]]; ?>&nbsp;</p></td>
                        <td width="160"><p><? echo $row[csf('item_description')]; ?></p></td>
                        <td width="80"><p><? echo $row[csf('item_size')]; ?></p></td>
                        <td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
                    </tr>
                    <?
             		$i++;
				}
               
             }*/
			 
			foreach ($data_array as $row)
			{
				if($goods_rcv_status==2)
				{
					if($selected_based_on==1)
					{
						$bal_qnty=$row[csf('wo_qnty')]-$prev_pi_qnty_arr[$row[csf('mst_id')]];
					}
					else
					{
						$bal_qnty=$row[csf('wo_qnty')]-$prev_pi_qnty_arr[$row[csf('dtls_id')]];
					}
				}
				else
				{
					$bal_qnty=1;
				}
				if($bal_qnty>0)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$row[csf('dtls_id')]; ?>')"> 				
                        <td width="40"><? echo "$i"; ?></td>	
                        <td width="70"><p><? echo $row[csf('wo_number_prefix_num')];?></p></td>
                        <td width="80"><p><? echo change_date_format($row[csf('wo_date')]);?></p></td>
                        <td width="140"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td> 
                        <td width="90" align="center"><p><? echo $item_category[$row[csf('item_category')]]; ?>&nbsp;</p></td>
                        <td width="120"><p><? echo $item_group_arr[$row[csf('item_group_id')]]; ?>&nbsp;</p></td>
                        <td width="160"><p><? echo $row[csf('item_description')]; ?></p></td>
                        <td width="80"><p><? echo $row[csf('item_size')]; ?></p></td>
                        <td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
					</tr>
					<?
					$i++;
				}
			}
			?>
		</table>
        </div>  
		<table width="880" cellspacing="0" cellpadding="0" style="border:none" align="center">
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
		
		
	}
	
	exit();	
} 

if( $action == 'populate_data_wo_form' ) 
{
	$data=explode('**',$data);
	$wo_dtls_id=$data[0];
	$item_category_id=$data[1];
	$tblRow=$data[2];
	$order_type=str_replace(",","",$data[3]);
	$goods_rcv_status=$data[4];
	$importer_id=$data[5];
	$cbo_pi_basis_id=$data[6];
	
	$prev_pi_amnt_arr=array(); $prev_pi_qty_arr=array();
	
	
	if($item_category_id==4)
	{
		$booking_id_cond=""; $result=explode(",",$wo_dtls_id);
		if($db_type==2 && count($result)>999)
		{
			$booking_id_chunk_arr=array_chunk($result,999) ;
			foreach($booking_id_chunk_arr as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$bokIds_cond.=" b.work_order_dtls_id in($chunk_arr_value) or ";	
			}
			
			$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
			//echo $booking_id_cond;die;
		}
		else
		{
			$booking_id_cond=" and b.work_order_dtls_id in($wo_dtls_id)";	 
		}
			
		if($goods_rcv_status==2 && $order_type==0)
		{
			$prev_pi_qty_arr=array();
			
			$sql_prev="select b.work_order_no, b.work_order_dtls_id, b.color_id, b.item_group, b.item_description, b.size_id, b.item_color, b.item_size, b.uom, b.rate, b.brand_supplier, sum(b.quantity) as qty 
			from com_pi_master_details a, com_pi_item_details b 
			where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 $booking_id_cond group by b.work_order_no, b.work_order_dtls_id, b.color_id, b.item_group, b.item_description, b.size_id, b.item_color, b.item_size, b.uom, b.rate, b.brand_supplier";// and b.work_order_dtls_id in($wo_dtls_id)
			$prevData=sql_select($sql_prev);
			foreach($prevData as $rowP)
			{
				//$prev_pi_qty_arr[$rowP[csf('work_order_no')]][$rowP[csf('work_order_dtls_id')]][$rowP[csf('color_id')]][$rowP[csf('item_group')]][$rowP[csf('item_description')]][$rowP[csf('size_id')]][$rowP[csf('item_color')]][$rowP[csf('item_size')]][$rowP[csf('brand_supplier')]][$rowP[csf('uom')]][$rowP[csf('rate')]]=$rowP[csf('qty')];
				$prev_pi_qty_arr[$rowP[csf('work_order_no')]][$rowP[csf('work_order_dtls_id')]][$rowP[csf('color_id')]][$rowP[csf('item_group')]][$rowP[csf('item_description')]][$rowP[csf('size_id')]][$rowP[csf('item_color')]][$rowP[csf('item_size')]][$rowP[csf('brand_supplier')]][$rowP[csf('uom')]]=$rowP[csf('qty')];
			}
			
			
			
		}
		else
		{
			$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty 
			from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.importer_id='$importer_id' and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 $booking_id_cond group by b.work_order_dtls_id", "work_order_dtls_id", "qty");// and b.work_order_dtls_id in($wo_dtls_id)
		}
	}
	
	if($cbo_pi_basis_id==1) $disable_rate="disabled='disabled'";  else $disable_rate="";
	
	
	if($item_category_id==4)
	{
		$item_group_arr=return_library_array("SELECT id,item_name FROM lib_item_group WHERE item_category=4 AND status_active=1 and is_deleted=0",'id','item_name');
		
		
		
		$booking_without_order=$order_type;
		
		if($goods_rcv_status==2)
		{
			$booking_id_cond=""; $result=explode(",",$wo_dtls_id);
			if($db_type==2 && count($result)>999)
			{
				$booking_id_chunk_arr=array_chunk($result,999) ;
				foreach($booking_id_chunk_arr as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);	
					$bokIds_cond.=" b.id in($chunk_arr_value) or ";	
				}
				
				$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
				//echo $booking_id_cond;die;
			}
			else
			{
				$booking_id_cond=" and b.id in($wo_dtls_id)";	 
			}
			
			if($order_type==1)
			{
				$sql_trims=("select a.id, a.booking_no, b.id as dtls_id, b.trim_group, b.fabric_description as description, b.uom, b.rate, b.trim_qty as qnty, b.gmts_color as color_number_id, b.fabric_color as item_color, b.gmts_size as size_id, b.item_size, b.barnd_sup_ref as brand_supplier from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no $booking_id_cond");// and b.id in($wo_dtls_id)
			}
			else
			{
				$sql_trims=("select a.id, a.booking_no, b.id as dtls_id, b.trim_group, b.uom, c.description, c.rate, c.color_number_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, sum(c.cons) as qnty from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id $booking_id_cond and c.cons>0 group by a.id, a.booking_no, b.id, b.trim_group, b.uom, c.description, c.rate, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier");// and b.id in($wo_dtls_id)
			}
			//echo $sql_trims;die;
			$data_array=sql_select($sql_trims);
			foreach($data_array as $row)
			{
				
				if($order_type==0)
				{
					//$bl_qty=$row[csf('qnty')]-$prev_pi_qty_arr[$row[csf('booking_no')]][$row[csf('dtls_id')]][$row[csf('color_number_id')]][$row[csf('trim_group')]][$row[csf('description')]][$row[csf('size_id')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('brand_supplier')]][$row[csf('uom')]][$row[csf('rate')]];
					$bl_qty=$row[csf('qnty')]-$prev_pi_qty_arr[$row[csf('booking_no')]][$row[csf('dtls_id')]][$row[csf('color_number_id')]][$row[csf('trim_group')]][$row[csf('description')]][$row[csf('size_id')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('brand_supplier')]][$row[csf('uom')]];
				}
				else
				{
					$bl_qty=$row[csf('qnty')]-$prev_pi_qty_arr[$row[csf('dtls_id')]];
				}
				
				//$bl_qty=$row[csf('qnty')]-$prev_pi_qty_arr[$row[csf('dtls_id')]];
				$amount=$row[csf('rate')]*$bl_qty;
				$bl_qty=number_format($bl_qty,2,'.','');
				if($bl_qty>0)
				{
					$tblRow++;
					?>
					<tr class="general" id="row_<? echo $tblRow; ?>">
						<td>
							<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
						</td>
						<td>
							<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('booking_no')]; ?>" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
							<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>" readonly />
							<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('dtls_id')]; ?>" readonly />
							<input type="hidden" name="hideTransData_<? echo $tblRow; ?>" id="hideTransData_<? echo $tblRow; ?>" value="" readonly />
                            <input type="hidden" name="itemProdId_<? echo $tblRow; ?>" id="itemProdId_<? echo $tblRow; ?>" value="" readonly />
						</td>
						<td> 
							<?
								//echo create_drop_down( "itemgroupid_".$tblRow, 110, $item_group_arr,'', 1, '-Select-',$row[csf('trim_group')],"get_php_form_data( this.value+'**'+'uom_$tblRow', 'get_uom', 'requires/pi_accessories_controller' );",1); 
							?>
                            <input type="text" name="itemgroupid_<? echo $tblRow; ?>" id="itemgroupid_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $item_group_arr[$row[csf('trim_group')]]; ?>" style="width:100px;" placeholder="<? echo $row[csf('trim_group')]; ?>" readonly /> 
						</td>
						<td>
							<input type="text" name="itemdescription_<? echo $tblRow; ?>" id="itemdescription_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('description')]; ?>" style="width:130px" maxlength="200" disabled="disabled"/>
						</td>
						 <td>
							 <input type="text" name="brandSupRef_<? echo $tblRow; ?>" id="brandSupRef_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('brand_supplier')]; ?>" style="width:80px" maxlength="150" disabled="disabled"/>
							</td>
						<td>
							<input type="text" name="colorName_<? echo $tblRow; ?>" id="colorName_<? echo $tblRow; ?>" class="text_boxes" style="width:70px" maxlength="50" value="<? echo $color_library[$row[csf('color_number_id')]]; ?>" disabled="disabled"/>                        
						</td>
						<td>
							<input type="text" name="sizeName_<? echo $tblRow; ?>" id="sizeName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('size_id')]]; ?>" style="width:60px;" maxlength="50" disabled="disabled"/>
						</td>
						<td>
							<input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('item_color')]]; ?>" style="width:70px" maxlength="50" disabled="disabled"/>
						</td>
						<td>
							<input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('item_size')]; ?>" style="width:60px;" maxlength="50" disabled="disabled"/>
						</td>
						 <td>
                         	<input type="text" name="uom_<? echo $tblRow; ?>" id="uom_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $unit_of_measurement[$row[csf('uom')]]; ?>" style="width:50px;" placeholder="<? echo $row[csf('uom')]; ?>" readonly/>
						</td>
						<td>
							<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo number_format($bl_qty,2,'.',''); ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" placeholder="<? echo number_format($bl_qty,2,'.',''); ?>" title="<? echo $bl_qty; ?>" />
						</td>
						<td>
							<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo number_format($row[csf('rate')],4,'.',''); ?>" style="width:45px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" <? echo $disable_rate; ?> />
						</td>
						<td>
							<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo number_format($amount,2,'.',''); ?>" style="width:75px;" readonly />
							<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
							<input type="hidden" name="bookingWithoutOrder_<? echo $tblRow; ?>" id="bookingWithoutOrder_<? echo $tblRow; ?>" value="<? echo $booking_without_order; ?>"/>
						</td>
					</tr>
				<?
				}
			}
		}
		else
		{
			$prev_retun_qnty=array();
			$booking_id_cond=""; $result=explode(",",$wo_dtls_id);
			if($db_type==2 && count($result)>999)
			{
				$booking_id_chunk_arr=array_chunk($result,999) ;
				foreach($booking_id_chunk_arr as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);	
					$bokIds_cond.=" d.id in($chunk_arr_value) or ";	
				}
				
				$booking_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
				//echo $booking_id_cond;die;
			}
			else
			{
				$booking_id_cond=" and d.id in($wo_dtls_id)";	 
			}
			
			if($order_type==1)
			{
				$sql_trims=("select a.id, a.booking_no, d.id as dtls_id, b.item_group_id as trim_group, b.item_description as description, b.order_uom as uom, b.gmts_color_id as color_number_id, b.item_color as item_color, b.gmts_size_id as size_id, b.item_size, b.brand_supplier, d.prod_id, d.order_rate as rate, d.order_qnty as qnty, d.order_amount 
				from wo_non_ord_samp_booking_mst a, inv_trims_entry_dtls b, inv_receive_master c, inv_transaction d 
				where c.id=b.mst_id and a.id=c.booking_id and c.id=d.mst_id and d.id=b.trans_id $booking_id_cond");// and d.id in($wo_dtls_id)
			}
			else
			{
				$conver_factor=return_library_array("select a.id, b.conversion_factor from product_details_master a,  lib_item_group b where a.item_group_id=b.id","id","conversion_factor");
				$prev_retun_sql="select booking_id, prod_id, issue_qnty from inv_trims_issue_dtls where booking_id>0";
				$prev_retun_sql_result=sql_select($prev_retun_sql);
				foreach($prev_retun_sql_result as $row)
				{
					$prev_retun_qnty[$row[csf("booking_id")]][$row[csf("prod_id")]]+=$row[csf("issue_qnty")]/$conver_factor[$row[csf("prod_id")]];
				}
				//$prev_retun_qnty
				
				$sql_trims=("select a.id, a.booking_no, d.id as dtls_id, b.item_group_id as trim_group, b.item_description as description, b.order_uom as uom, b.gmts_color_id as color_number_id, b.item_color as item_color, b.gmts_size_id as size_id, b.item_size, b.brand_supplier, d.prod_id, d.order_rate as rate, d.order_qnty as qnty, d.order_amount 
				from wo_booking_mst a, inv_trims_entry_dtls b, inv_receive_master c, inv_transaction d 
				where c.id=b.mst_id and a.id=c.booking_id and c.id=d.mst_id and d.id=b.trans_id $booking_id_cond");// and d.id in ($wo_dtls_id)
			}
			
			//echo($prev_retun_qnty[8278][9228]);die;
			
			$data_array=sql_select($sql_trims);
			$dtls_data=array();
			foreach($data_array as $row)
			{
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["id"]=$row[csf("id")];
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["booking_no"]=$row[csf("booking_no")];
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["trim_group"]=$row[csf("trim_group")];
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["description"]=$row[csf("description")];
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["uom"]=$row[csf("uom")];
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["color_number_id"]=$row[csf("color_number_id")];
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["item_color"]=$row[csf("item_color")];
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["size_id"]=$row[csf("size_id")];
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["item_size"]=$row[csf("item_size")];
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["brand_supplier"]=$row[csf("brand_supplier")];
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["qnty"]+=$row[csf("qnty")];
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["order_amount"]+=$row[csf("order_amount")];
				$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["dtls_id"].=$row[csf("dtls_id")]."_";
				
				$trans_qnty_data[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("dtls_id")]]["trans_qnty"]=$row[csf("qnty")];
				$trans_qnty_data[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("dtls_id")]]["order_amount"]=$row[csf("order_amount")];
			}
			
			
			foreach($dtls_data as $book_id=>$row_val)
			{
				foreach($row_val as $pord_id=>$row)
				{
				
					
					$rtn_qnty=$rate=$prev_pi_qnty=$book_item_qnty=$book_item_amt=$bl_trns_qnty=$item_rate=$item_amt=0;$trans_id_arr=$trans_data_arr=array();$trans_data=$all_dtls_id="";
					
					$trans_id_arr=explode("_",chop($row[("dtls_id")],'_'));
					foreach($trans_id_arr as $trans_id)
					{
						//$dtls_data[$book_id][$pord_id][$trans_id]["trans_qnty"]-$prev_pi_qty_arr[$trans_id];
						if($trans_qnty_data[$book_id][$pord_id][$trans_id]["trans_qnty"]>$prev_retun_qnty[$book_id][$pord_id])
						{
							//$trans_qnty_data[$book_id][$pord_id][$trans_id]["trans_qnty"]=
							$bl_trns_qnty=number_format(($trans_qnty_data[$book_id][$pord_id][$trans_id]["trans_qnty"]-($prev_retun_qnty[$book_id][$pord_id]+$prev_pi_qty_arr[$trans_id])),2,'.','');
							$item_rate=$trans_qnty_data[$book_id][$pord_id][$trans_id]["order_amount"]/$trans_qnty_data[$book_id][$pord_id][$trans_id]["trans_qnty"];
							$item_amt=number_format($bl_trns_qnty*$item_rate,2,'.','');
							
							$rtn_qnty+=$prev_retun_qnty[$book_id][$pord_id];
							$all_dtls_id.=$trans_id.",";
							$trans_data.=$trans_id."_".$bl_trns_qnty."_".$item_amt."_".$pord_id."__";
							$prev_retun_qnty[$book_id][$pord_id]=0;
							
						}
						else
						{
							//$trans_qnty_data[$book_id][$pord_id][$trans_id]["trans_qnty"]=
							$bl_trns_qnty=number_format(($trans_qnty_data[$book_id][$pord_id][$trans_id]["trans_qnty"]-($trans_qnty_data[$book_id][$pord_id][$trans_id]["trans_qnty"]+$prev_pi_qty_arr[$trans_id])),2,'.','');
							$item_rate=$trans_qnty_data[$book_id][$pord_id][$trans_id]["order_amount"]/$trans_qnty_data[$book_id][$pord_id][$trans_id]["trans_qnty"];
							$item_amt=number_format($bl_trns_qnty*$item_rate,2,'.','');							
							$trans_data.=$trans_id."_".$bl_trns_qnty."_".$item_amt."_".$pord_id."__";
							$rtn_qnty+=$trans_qnty_data[$book_id][$pord_id][$trans_id]["trans_qnty"];
							$prev_retun_qnty[$book_id][$pord_id]=$prev_retun_qnty[$book_id][$pord_id]-$trans_qnty_data[$book_id][$pord_id][$trans_id]["trans_qnty"];
							$all_dtls_id.=$trans_id.",";
							
						}
						$book_item_qnty+=$trans_qnty_data[$book_id][$pord_id][$trans_id]["trans_qnty"];
						$book_item_amt+=$trans_qnty_data[$book_id][$pord_id][$trans_id]["order_amount"];
						$prev_pi_qnty+=$prev_pi_qty_arr[$trans_id];
						
					}
					$all_dtls_id=chop($all_dtls_id,",");
					$bl_qty=($book_item_qnty-($rtn_qnty+$prev_pi_qnty));
					$rate=$book_item_amt/$book_item_qnty;
					$amount=$rate*$bl_qty;
					
					$bl_qty=number_format($bl_qty,2,'.','');
					//echo $bl_qty.'=';
					if($bl_qty>0)
					{
						$tblRow++;
						?>
						<tr class="general" id="row_<? echo $tblRow; ?>">
							<td>
								<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
							</td>
							<td>
								<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[('booking_no')]; ?>" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
								<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[('id')]; ?>" readonly />
								<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $all_dtls_id; ?>" readonly />
								<input type="hidden" name="hideTransData_<? echo $tblRow; ?>" id="hideTransData_<? echo $tblRow; ?>" value="<? echo chop($trans_data,'__'); ?>" readonly />
                                <input type="hidden" name="itemProdId_<? echo $tblRow; ?>" id="itemProdId_<? echo $tblRow; ?>" value="<? echo $pord_id; ?>" readonly />
							</td>
							<td> 
								<?
									echo create_drop_down( "itemgroupid_".$tblRow, 110, $item_group_arr,'', 1, '-Select-',$row[('trim_group')],"get_php_form_data( this.value+'**'+'uom_$tblRow', 'get_uom', 'requires/pi_accessories_controller' );",1); 
								?>  
							</td>
							<td>
								<input type="text" name="itemdescription_<? echo $tblRow; ?>" id="itemdescription_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[('description')]; ?>" style="width:130px" maxlength="200" disabled="disabled"/>
							</td>
							 <td>
								 <input type="text" name="brandSupRef_<? echo $tblRow; ?>" id="brandSupRef_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[('brand_supplier')]; ?>" style="width:80px" maxlength="150" disabled="disabled"/>
								</td>
							<td>
								<input type="text" name="colorName_<? echo $tblRow; ?>" id="colorName_<? echo $tblRow; ?>" class="text_boxes" style="width:70px" maxlength="50" value="<? echo $color_library[$row[('color_number_id')]]; ?>" disabled="disabled"/>                        
							</td>
							<td>
								<input type="text" name="sizeName_<? echo $tblRow; ?>" id="sizeName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $size_library[$row[('size_id')]]; ?>" style="width:60px;" maxlength="50" disabled="disabled"/>
							</td>
							<td>
								<input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[('item_color')]]; ?>" style="width:70px" maxlength="50" disabled="disabled"/>
							</td>
							<td>
								<input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[('item_size')]; ?>" style="width:60px;" maxlength="50" disabled="disabled"/>
							</td>
							 <td>
								<? 
									echo create_drop_down( "uom_".$tblRow, 60, $unit_of_measurement,'', 0, '',$row[('uom')],'',1);            
								?>						 
							</td>
							<td>
								<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo number_format($bl_qty,2,'.',''); ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" placeholder="<? echo number_format($bl_qty,2,'.',''); ?>" readonly />
							</td>
							<td>
								<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo number_format($rate,4,'.',''); ?>" style="width:45px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" readonly />
							</td>
							<td>
								<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo number_format($amount,2,'.',''); ?>" style="width:75px;" readonly />
								<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
								<input type="hidden" name="bookingWithoutOrder_<? echo $tblRow; ?>" id="bookingWithoutOrder_<? echo $tblRow; ?>" value="<? echo $booking_without_order; ?>"/>
							</td>
						</tr>
					<?
					}
				}
			}
		}
		
		//echo $sql_trims;//die;
		
	}
	
	
	exit();
}

if ($action=="itemDesc_popup")
{
	echo load_html_head_contents("WO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
	
	var item_category=<? echo $item_category; ?>;
	var selected_id = new Array, selected_name = new Array();
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
							
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#txt_selected_item_id').val( id );
		}
	
		function reset_hide_field()
		{
			$('#txt_selected_item_id').val( '' );
		}
	
    </script>

</head>

<body>
<div align="center" style="width:800px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:100%;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="700px" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Supplier</th>
                    <th>Item Description</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" onClick="reset_hide_field()" />
                        <input type="hidden" name="txt_item_category" id="txt_item_category" class="text_boxes" style="width:70px" value="<? echo $item_category; ?>">
                    	<input type="hidden" name="txt_selected_item_id" id="txt_selected_item_id" class="text_boxes" value="">   
                    </th> 
                </thead>
                <tr class="general">
                    <td>
						 <? echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'pi_accessories_controller',this.value+'_'+$item_category, 'load_supplier_dropdown', 'supplier_td' );",0); ?>       
                    </td>
                    <td id="supplier_td">	
                        <?
							echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
						?> 
                    </td>                 
                    <td> 
                        <input type="text" name="txt_item_desc" id="txt_item_desc" class="text_boxes" style="width:120px">
                    </td>						
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_item_desc').value+'_'+document.getElementById('txt_item_category').value+'_'+document.getElementById('cbo_importer_id').value+'_'+document.getElementById('cbo_supplier_id').value, 'create_item_search_list_view', 'search_div', 'pi_accessories_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
           </table>
           <table width="100%" style="margin-top:5px">
                <tr>
                    <td colspan="5">
                        <div style="width:100%; margin-top:10px; margin-left:10px" id="search_div" align="left"></div>
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

if($action=="create_item_search_list_view")

{
	$data = explode("_",$data);
	
	$item_category_id =$data[1];
	$company_id =$data[2];
	
	if($item_category_id==1) $field_name="product_name_details"; else $field_name="item_description";
	
	if($company_id==0) { echo "Please Select Company First."; die; }
	if($data[3]==0) $supplier_id="%%"; else $supplier_id =$data[3];
	
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	if($item_category_id==1)
	{
		$sql = "select id, item_category_id, supplier_id, lot, product_name_details, unit_of_measure from product_details_master where company_id=$company_id and item_category_id=$item_category_id and supplier_id like '$supplier_id' and product_name_details like '%".trim($data[0])."%' and status_active=1 and is_deleted=0";
		
		$arr=array (0=>$item_category,1=>$supplier_arr,4=>$unit_of_measurement);
		
		echo create_list_view("tbl_list_search", "Item Category, Supplier, Lot No, Yarn Description, UOM", "100,170,100,260","770","250",0, $sql, "js_set_value", "id", "", 1, "item_category_id,supplier_id,0,0,unit_of_measure", $arr , "item_category_id,supplier_id,lot,product_name_details,unit_of_measure", "",'','0,0,0,0,0','',1);
	}
	else
	{
		$sql = "select id, item_category_id, supplier_id, item_group_id, item_description, item_size, unit_of_measure from product_details_master where company_id=$company_id and item_category_id=$item_category_id and supplier_id like '$supplier_id' and item_description like '%".trim($data[0])."%' and status_active=1 and is_deleted=0";
		
		$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$arr=array (0=>$item_category,1=>$supplier_arr,2=>$item_group_arr,5=>$unit_of_measurement);
		
		echo create_list_view("tbl_list_search", "Item Category, Supplier, Item Group, Item Description, Item Size, UOM", "120,120,120,160,90","770","250",0, $sql, "js_set_value", "id", "", 1, "item_category_id,supplier_id,item_group_id,0,0,unit_of_measure", $arr , "item_category_id,supplier_id,item_group_id,item_description,item_size,unit_of_measure", "",'','0,0,0,0,0,0','',1);

	}
	//echo $sql;
	exit();	
} 

if( $action == 'populate_data_item_form' ) 
{
	$data=explode('**',$data);
	//print_r($data);
	//die;
	$item_id=$data[0];
	$item_category_id=$data[1];
	$tblRow=$data[2];

	if($item_category_id==1)
	{
		$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count",'id','yarn_count');
		$color_array=return_library_array( "select id, color_name from lib_color WHERE status_active = 1 AND is_deleted = 0 ORDER BY color_name",'id','color_name');

		$data_array=sql_select("select id, lot, product_name_details, yarn_count_id, unit_of_measure from product_details_master where id in($item_id)");
		foreach($data_array as $row)
		{
			$tblRow++;
			?>
			<tr class="general" id="row_<? echo $tblRow; ?>">
            	<td>
                    <input type="text" name="lot_<? echo $tblRow; ?>" id="lot_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('lot')];?>" style="width:80px" maxlength="200" onDblClick="openmypage_item_desc(<? echo $tblRow; ?>);" placeholder="Double Click" readonly/>
                    <input type="hidden" name="itemProdId_<? echo $tblRow; ?>" id="itemProdId_<? echo $tblRow; ?>" readonly value="<? echo $row[csf('id')];?>"/> 
                </td>
                <td>
                    <? echo create_drop_down( "countName_".$tblRow, 90, $count_arr,'', 1, '-Select-', $row[csf('yarn_count_id')],"",1); ?>
                </td>
                <td>
                    <input type="text" name="itemdescription_<? echo $tblRow; ?>" id="itemdescription_<? echo $tblRow; ?>" class="text_boxes" style="width:200px" value="<? echo $row[csf('product_name_details')];?>" disabled/>
                </td>
                <td>
                    <? echo create_drop_down( "yarnColor_".$tblRow, 110, $color_array,'', 1, '-Select-',0,"",0); ?>
                </td>
                <td>
                    <? echo create_drop_down( "colorRange_".$tblRow, 110, $color_range,'', 1, '-Select-',0,"",0); ?>
                </td>
                <td>
                    <?
                        echo create_drop_down( "uom_".$tblRow, 60, $unit_of_measurement,'', 0, '',12,'12',1); 
                    ?>
                </td>
				<td>
					<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
				</td>
				<td>
					<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="" style="width:45px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
				</td>
				<td>
					<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
					<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly/>
				</td>
				<td width="65">
					<input type="button" id="increase_<? echo $tblRow; ?>" name="increase_<? echo $tblRow; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $tblRow; ?> )" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease_<? echo $tblRow; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);" />
				</td>
			</tr>
		<?
		}
	}
	else
	{
		$data_array=sql_select("select id, item_group_id, item_description, item_size, unit_of_measure from product_details_master where id in($item_id)");
		foreach($data_array as $row)
		{
			$tblRow++;
			?>
			<tr class="general" id="row_<? echo $tblRow; ?>">
				<td> 
					<?
						echo create_drop_down( "itemgroupid_".$tblRow, 130, "SELECT id,item_name FROM lib_item_group WHERE item_category =$item_category_id AND status_active = 1 AND is_deleted = 0 ORDER BY item_name ASC",'id,item_name', 1, '-Select-',$row[csf('item_group_id')],"get_php_form_data( this.value+'**'+'uom_$tblRow', 'get_uom', 'requires/pi_accessories_controller' );",1); 
					?>  
				</td>
				<td>
					<input type="text" name="itemdescription_<? echo $tblRow; ?>" id="itemdescription_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('item_description')]; ?>" style="width:200px" maxlength="200" <? echo $disable; ?> onDblClick="openmypage_item_desc(<? echo $tblRow; ?>);" placeholder="Double Click" readonly/>
					<input type="hidden" name="itemProdId_<? echo $tblRow; ?>" id="itemProdId_<? echo $tblRow; ?>" readonly value="<? echo $row[csf('id')]; ?>"/> 
				</td>
				<td>
					<input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('item_size')]; ?>" style="width:90px;" maxlength="50" disabled="disabled"/>
				</td>
				<td>
					<? 
						echo create_drop_down( "uom_$tblRow", 80, $unit_of_measurement,'', 0, '',$row[csf('unit_of_measure')],'',1);            
					?>		
				</td>
				<td>
					<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="" style="width:90px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
				</td>
				<td>
					<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
				</td>
				<td>
					<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="" style="width:85px;" readonly/>
					<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly/>
				</td>
				<td width="65">
					<input type="button" id="increase_<? echo $tblRow; ?>" name="increase_<? echo $tblRow; ?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $tblRow; ?> )" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease_<? echo $tblRow; ?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);" />
				</td>
			</tr>
		<?
		}
	}
	exit();
}

if($action=="approvalUser_popup")
{
	echo load_html_head_contents("User Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $menu_id;
	?> 
	<script>
		
		function js_set_value( str )
		{
			//alert(str)
			var splitData = str.split("_");
			document.getElementById('hdn_user_id').value=splitData[0];
			document.getElementById('hdn_user_name').value=splitData[1];
			parent.emailwindow.hide();
		}
		
    </script>
	<input type="hidden" name="hdn_user_name" id="hdn_user_name" class="text_boxes" style="width:70px"> 
    <input type="hidden" name="hdn_user_id" id="hdn_user_id" class="text_boxes" style="width:70px">
<?
	$sql= "select a.id, a.user_name, a.user_full_name from user_passwd a, user_priv_mst b where a.id=b.user_id and a.valid=1 and b.main_menu_id=$menu_id and b.valid=1 and b.approve_priv=1 order by a.id ASC";
	
	echo create_list_view("list_view", "User ID, User Name, User Full Name","60,80,150","360","300",0, $sql , "js_set_value", "id,user_name", "", 1, "0,0,0", $arr, "id,user_name,user_full_name", "","setFilterGrid('list_view',-1)",'0,0,0') ;	
	exit();
}
?>


 