﻿<? 
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
		exit();
	}
	echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(20,21,22,23,24,30,31,32,35,36,37,38,39) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	
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

			
			$field_array="id,importer_id,supplier_id,pi_number,pi_date,last_shipment_date,pi_validity_date,currency_id,source,hs_code,internal_file_no,intendor_name,pi_basis_id,remarks,goods_rcv_status,export_pi_id,within_group,total_amount,upcharge,discount,net_total_amount,import_pi,ready_to_approved,approval_user,inserted_by,insert_date,lc_group_no,entry_form,version";
			
			$data_array="(".$id.",".$cbo_importer_id.",".$cbo_supplier_id.",".$pi_number.",".$pi_date.",".$last_shipment_date.",".$pi_validity_date.",".$cbo_currency_id.",".$cbo_source_id.",".$hs_code.",".$txt_internal_file_no.",".$intendor_name.",".$cbo_pi_basis_id.",".$txt_remarks.",".$cbo_goods_rcv_status.",".$export_pi_id.",".$within_group.",'".str_replace("'", '', $txt_total_amount)."','".str_replace("'", '', $txt_upcharge)."','".str_replace("'", '', $txt_discount)."','".str_replace("'", '', $txt_total_amount_net)."',".$is_import_pi.",".$cbo_ready_to_approved.",".$hiddn_user_id.",".$user_id.",'".$pc_date_time."',".$txt_lc_group_no.",168,2)";
			
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
			
			$field_array="importer_id*supplier_id*pi_number*pi_date*last_shipment_date*pi_validity_date*currency_id*source*hs_code*internal_file_no*intendor_name*pi_basis_id*remarks*goods_rcv_status*total_amount*upcharge*discount*net_total_amount*ready_to_approved*approval_user*updated_by*update_date*lc_group_no";
			
			$data_array= $cbo_importer_id."*".$cbo_supplier_id."*".$pi_number."*".$pi_date."*".$last_shipment_date."*".$pi_validity_date."*".$cbo_currency_id."*".$cbo_source_id."*".$hs_code."*".$txt_internal_file_no."*".$intendor_name."*".$cbo_pi_basis_id."*".$txt_remarks."*".$cbo_goods_rcv_status."*".$txt_total_amount."*".$txt_upcharge."*".$txt_discount."*".$txt_total_amount_net."*".$cbo_ready_to_approved."*".$hiddn_user_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_lc_group_no."";
			
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
						<?php echo create_drop_down( "cbo_within_group", 151, $yes_no,"", 1, "-- Select --", 0, "load_drop_down( 'pi_service_fabric_controller',this.value, 'load_drop_down_importer', 'importer_td' );" ); ?>
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_importer').value, 'create_export_pi_search_list_view', 'search_div', 'pi_service_fabric_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
			$all_dtls_id.=str_replace("'","",$$workOrderDtlsId).",";
		}
		$all_dtls_id=chop($all_dtls_id,",");
		$all_WoId=implode(",",array_unique(explode(",",chop($all_WoId,","))));
		
		if(str_replace("'", '',$cbo_goods_rcv_status)==1)
		{
			if($all_dtls_id=="") $all_dtls_id=0;

			if(str_replace("'", '',$txt_order_type)==1)
			{
				$wo_qnty_arr=return_library_array("select id, wo_qnty as qnty from wo_booking_dtls where status_active=1 and id in($all_dtls_id)","id","qnty");
			}
			else
			{
				$wo_qnty_arr=return_library_array("select id, wo_qty as qnty from wo_non_ord_aop_booking_dtls where status_active=1 and id in($all_dtls_id)","id","qnty");
			}
			
			$previous_pi_qty=return_library_array("select b.work_order_dtls_id as id, sum(b.quantity) as qnty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=".str_replace("'", '',$cbo_item_category_id)." and a.goods_rcv_status=1 and b.status_active=1 and b.work_order_dtls_id in($all_dtls_id) group by b.work_order_dtls_id","id","qnty");
			
			$prev_retun_qnty=return_library_array("select b.recv_trans_id, sum(b.issue_qnty) as qnty 
				from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.issue_trans_id and a.transaction_type=3 and b.recv_trans_id in($all_dtls_id) and b.status_active=1 group by recv_trans_id","recv_trans_id","qnty");
			//echo "11**select booking_id, prod_id, issue_qnty from inv_trims_issue_dtls where booking_id>0";die;
			
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
				$wo_qnty_arr=return_library_array("select id, wo_qnty as qnty from wo_booking_dtls where status_active=1 and id in($all_dtls_id)","id","qnty");
			}
			else
			{
				$wo_qnty_arr=return_library_array("select id, wo_qty as qnty from wo_non_ord_aop_booking_dtls where status_active=1 and id in($all_dtls_id)","id","qnty");
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
			
		
			$allWoId.=str_replace("'","",$$workOrderId).",";
			$wotransId=str_replace("'","",$$workOrderDtlsId);
			$trans_qty_check_arr[$wotransId]=str_replace("'","",$$quantity);
			if($woDtlsTrsansId=='') $woDtlsTrsansId=$wotransId; else $woDtlsTrsansId.=','.$wotransId;
			
			$perc=(str_replace("'","",$$amount)/$txt_total_amount)*100;
			$net_pi_amount=($perc*$txt_total_amount_net)/100;
			
			if(str_replace("'", '',$cbo_item_category_id)==31)
			{
				$net_pi_rate=0;
			}
			else
			{
				$net_pi_rate=$net_pi_amount/str_replace("'","",$$quantity);
				$net_pi_rate=number_format($net_pi_rate,$dec_place[3],'.','');
			}
			
			if($cbo_currency_id==1)
				$net_pi_amount=number_format($net_pi_amount,$dec_place[4],'.','');
			else
				$net_pi_amount=number_format($net_pi_amount,$dec_place[5],'.','');
					
			if(str_replace("'","",$$colorName)!="")
			{ 
				if (!in_array(str_replace("'","",$$colorName),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$colorName), $color_library, "lib_color", "id,color_name","168");  
					$new_array_color[$color_id]=str_replace("'","",$$colorName);
				}
				else $color_id =  array_search(str_replace("'","",$$colorName), $new_array_color); 
			}
			else
			{
				$color_id=0;
			}
			
			
			if(str_replace("'","",$$sizeName)!="")
			{
				if (!in_array(str_replace("'","",$$sizeName),$new_array_size))
				{
				  $size_id = return_id( str_replace("'","",$$sizeName), $size_library, "lib_size", "id,size_name","168");  
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
					$item_color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","168");  
					$new_array_color[$item_color_id]=str_replace("'","",$$item_color);
				}
				else $item_color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
			}
			else
			{
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
			
			$data_array[$id]="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$bookingWithoutOrder)."','".str_replace("'","",$$determinationId)."','".str_replace("'","",$$itemProdId)."','".str_replace("'","",$$itemgroupid)."','".str_replace("'","",$$itemdescription)."','".str_replace("'","",$color_id)."','".str_replace("'","",$size_id)."','".str_replace("'","",$$itemSize)."','".str_replace("'","",$$countName)."','".str_replace("'","",$$yarnCompositionItem1)."','".str_replace("'","",$$yarnCompositionPercentage1)."','".str_replace("'","",$$yarnCompositionItem2)."','".str_replace("'","",$$yarnCompositionPercentage2)."','".str_replace("'","",$$composition)."','".str_replace("'","",$$construction)."','".str_replace("'","",$$yarnType)."','".str_replace("'","",$$gsm)."','".str_replace("'","",$$diawidth)."','".str_replace("'","",$$weight)."','".str_replace("'","",$item_color_id)."','".str_replace("'","",$$uom)."','".str_replace("'","",$$quantity)."','".str_replace("'","",$$rate)."',".$$amount.",'".$net_pi_rate."','".$net_pi_amount."','".str_replace("'","",$$servicetype)."','".str_replace("'","",$$brandSupRef)."','".str_replace("'","",$$gmtsitem)."','".str_replace("'","",$$embellname)."','".str_replace("'","",$$embelltype)."','".str_replace("'","",$$lot)."','".str_replace("'","",$$yarnColor)."','".str_replace("'","",$$colorRange)."','".str_replace("'","",$$cboTestFor)."','".str_replace("'","",$$testItemId)."','".str_replace("'","",$$remarks)."',12,168,".$user_id.",'".$pc_date_time."')"; 
			$id=$id+1;
			
		}
		
		//echo "10**$woDtlsTrsansId";die;
		
		
		//echo "10**".$test_lock3;die;
		//echo "5**insert into com_pi_item_details (".$field_array.") Values ".$data_array."";die;
		//echo "10**".print_r($data_array);die;
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
		
		$field_array_update="pi_id*work_order_no*work_order_id*work_order_dtls_id*booking_without_order*determination_id*item_prod_id*item_group*item_description*color_id*size_id*item_size*count_name*yarn_composition_item1*yarn_composition_percentage1*yarn_composition_item2*yarn_composition_percentage2*fabric_composition*fabric_construction*yarn_type*gsm*dia_width*weight*item_color*uom*quantity*rate*amount*net_pi_rate*net_pi_amount*service_type*brand_supplier*gmts_item_id*embell_name*embell_type*lot_no*yarn_color*color_range*test_for*test_item_id*remarks*updated_by*update_date";
			
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
			$bookingWithoutOrder="bookingWithoutOrder_".$i;
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
			$all_bookingWithoutOrder.=str_replace("'","",$$bookingWithoutOrder).",";
		}
		$all_dtls_id=chop($all_dtls_id,",");
		$all_WoId=implode(",",array_unique(explode(",",chop($all_WoId,","))));
		$all_bookingWithoutOrder=implode(",",array_unique(explode(",",chop($all_bookingWithoutOrder,","))));
		if($all_bookingWithoutOrder == 0){
			$txt_order_type = 1;
		}else{
			$txt_order_type = 2;
		}
		
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
			where a.id=b.pi_id and b.item_category_id=".str_replace("'", '',$cbo_item_category_id)." and a.goods_rcv_status<>1 and b.status_active=1 $booking_id_cond and a.id not in($update_id) group by b.work_order_dtls_id","id","qnty");
			

			if(str_replace("'", '',$txt_order_type)==1)
			{
				$wo_qnty_arr=return_library_array("select id, wo_qnty as qnty from wo_booking_dtls where status_active=1 and id in($all_dtls_id)","id","qnty");
			}
			else
			{
				$wo_qnty_arr=return_library_array("select id, wo_qty as qnty from wo_non_ord_aop_booking_dtls where status_active=1 and id in($all_dtls_id)","id","qnty");
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
					$color_id = return_id( str_replace("'","",$$colorName), $color_library, "lib_color", "id,color_name","168");  
					$new_array_color[$color_id]=str_replace("'","",$$colorName);
				}
				else $color_id =  array_search(str_replace("'","",$$colorName), $new_array_color); 
			}
			else
			{
				$color_id=0;
			}
			

			if(str_replace("'","",$$sizeName)!="")
			{
				if (!in_array(str_replace("'","",$$sizeName),$new_array_size))
				{
				  $size_id = return_id( str_replace("'","",$$sizeName), $size_library, "lib_size", "id,size_name","168");  
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
					$item_color_id = return_id( str_replace("'","",$$item_color), $color_library, "lib_color", "id,color_name","168");  
					$new_array_color[$item_color_id]=str_replace("'","",$$item_color);
				}
				else $item_color_id =  array_search(str_replace("'","",$$item_color), $new_array_color); 
			}
			else
			{
				$item_color_id=0;
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
				$data_array_update[str_replace("'",'',$$updateIdDtls)] = explode("*",($update_id."*'".str_replace("'","",$$workOrderNo)."'*'".str_replace("'","",$$workOrderId)."'*'".str_replace("'","",$$workOrderDtlsId)."'*'".str_replace("'","",$$bookingWithoutOrder)."'*'".str_replace("'","",$$determinationId)."'*'".str_replace("'","",$$itemProdId)."'*'".str_replace("'","",$$itemgroupid)."'*'".str_replace("'","",$$itemdescription)."'*'".str_replace("'","",$color_id)."'*'".str_replace("'","",$size_id)."'*'".str_replace("'","",$$itemSize)."'*'".str_replace("'","",$$countName)."'*'".str_replace("'","",$$yarnCompositionItem1)."'*'".str_replace("'","",$$yarnCompositionPercentage1)."'*'".str_replace("'","",$$yarnCompositionItem2)."'*'".str_replace("'","",$$yarnCompositionPercentage2)."'*'".str_replace("'","",$$composition)."'*'".str_replace("'","",$$construction)."'*'".str_replace("'","",$$yarnType)."'*'".str_replace("'","",$$gsm)."'*'".str_replace("'","",$$diawidth)."'*'".str_replace("'","",$$weight)."'*'".str_replace("'","",$item_color_id)."'*'".str_replace("'","",$$uom)."'*'".str_replace("'","",$$quantity)."'*'".str_replace("'","",$$rate)."'*".$$amount."*'".$net_pi_rate."'*'".$net_pi_amount."'*'".str_replace("'","",$$servicetype)."'*'".str_replace("'","",$$brandSupRef)."'*'".str_replace("'","",$$gmtsitem)."'*'".str_replace("'","",$$embellname)."'*'".str_replace("'","",$$embelltype)."'*'".str_replace("'","",$$lot)."'*'".str_replace("'","",$$yarnColor)."'*'".str_replace("'","",$$colorRange)."'*'".str_replace("'","",$$cboTestFor)."'*'".str_replace("'","",$$testItemId)."'*'".str_replace("'","",$$remarks)."'*".$user_id."*'".$pc_date_time."'"));
			}
			else
			{
				if($data_array!="") $data_array.=","; 		
						
				$data_array .="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$bookingWithoutOrder)."','".str_replace("'","",$$determinationId)."','".str_replace("'","",$$itemProdId)."','".str_replace("'","",$$itemgroupid)."','".str_replace("'","",$$itemdescription)."','".str_replace("'","",$color_id)."','".str_replace("'","",$size_id)."','".str_replace("'","",$$itemSize)."','".str_replace("'","",$$countName)."','".str_replace("'","",$$yarnCompositionItem1)."','".str_replace("'","",$$yarnCompositionPercentage1)."','".str_replace("'","",$$yarnCompositionItem2)."','".str_replace("'","",$$yarnCompositionPercentage2)."','".str_replace("'","",$$composition)."','".str_replace("'","",$$construction)."','".str_replace("'","",$$yarnType)."','".str_replace("'","",$$gsm)."','".str_replace("'","",$$diawidth)."','".str_replace("'","",$$weight)."','".str_replace("'","",$item_color_id)."','".str_replace("'","",$$uom)."','".str_replace("'","",$$quantity)."','".str_replace("'","",$$rate)."',".$$amount.",'".$net_pi_rate."','".$net_pi_amount."','".str_replace("'","",$$servicetype)."','".str_replace("'","",$$brandSupRef)."','".str_replace("'","",$$gmtsitem)."','".str_replace("'","",$$embellname)."','".str_replace("'","",$$embelltype)."','".str_replace("'","",$$lot)."','".str_replace("'","",$$yarnColor)."','".str_replace("'","",$$colorRange)."','".str_replace("'","",$$cboTestFor)."','".str_replace("'","",$$testItemId)."','".str_replace("'","",$$remarks)."',12,168,". $user_id.",'".$pc_date_time."')"; 
				$id=$id+1;
			}
		}
		
		//echo "10**".print_r($data_array_update)."**1";die;
		
		$allWoId=chop($allWoId,",");
		$all_updateDtls_id=chop($all_updateDtls_id,",");
		
		if(str_replace("'", '',$cbo_pi_basis_id)==1 && str_replace("'", '',$cbo_goods_rcv_status)==1 && str_replace("'", '',$cbo_item_category_id)!=31)
		{
			$prev_pi_qnty=return_library_array("Select b.work_order_dtls_id, sum(b.quantity) as pi_qnty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.goods_rcv_status=1 and b.item_category_id=$cbo_item_category_id and b.work_order_dtls_id in ($woDtlsTrsansId) and id not in($all_updateDtls_id) and b.status_active=1 group by b.work_order_dtls_id","work_order_dtls_id","pi_qnty");
			
			$prev_retun_qnty=return_library_array("select b.recv_trans_id, sum(b.issue_qnty) as qnty 
				from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.issue_trans_id and a.transaction_type=3 and b.recv_trans_id in($woDtlsTrsansId) and b.status_active=1 group by recv_trans_id","recv_trans_id","qnty");
			
			
			$field_array_trans_update="pi_is_lock*updated_by*update_date";
			$sql_trns="Select id, pi_wo_batch_no, order_qnty as order_qnty from inv_transaction where id in ($woDtlsTrsansId) and item_category=$cbo_item_category_id order by id";
						
			$sql_trns_res = sql_select($sql_trns);
			foreach($sql_trns_res as $row)
			{
				
				$pi_trans_qty=number_format(($trans_qty_check_arr[$row[csf('id')]]),2,'.','');
				$order_trans_qty=number_format(($row[csf('order_qnty')]-($prev_retun_qnty[$row[csf('id')]]+$prev_pi_qnty[$row[csf('id')]])),2,'.','');
				
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
        if($item_category_id==1)
        {
			?>
        	<thead>
				<? 
                if($pi_basis_id == 1) 
                { 
                ?>
                    <th>&nbsp;</th>
                    <th class="must_entry_caption">WO</th>
                <? 
                } 
                ?>
                <th class="must_entry_caption">Color</th>
                <th class="must_entry_caption">Count</th>
                <th class="must_entry_caption">Composition 1st</th>
                <th class="must_entry_caption">Composition 2nd</th>
                <th class="must_entry_caption">Yarn Type</th>
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
				if($goods_rcv_status==1) $disable_qty_field="disabled='disabled'"; else $disable_qty_field="";
            }
			
			
			if($goods_rcv_status==2)
            {
                
				$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, color_id, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, yarn_composition_percentage2, yarn_type, uom, quantity, rate, amount from com_pi_item_details where pi_id='$pi_id' and status_active=1 and is_deleted=0" );
            }
            else
            {
				
				if($db_type==0)
				{
					$nameArray=sql_select( "select group_concat(id) as id, work_order_no, work_order_id, group_concat(work_order_dtls_id) as work_order_dtls_id, color_id, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, yarn_composition_percentage2, yarn_type, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount 
					from com_pi_item_details 
					where pi_id='$pi_id' and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, color_id, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, yarn_composition_percentage2, yarn_type, uom" );
				
				}
				else
				{
					
					$nameArray=sql_select( "select listagg(cast(id as varchar(4000)), ',') within group(order by id) as id, work_order_no, work_order_id, listagg(cast(work_order_dtls_id as varchar(4000)), ',') within group(order by work_order_dtls_id) as work_order_dtls_id, color_id, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, yarn_composition_percentage2, yarn_type, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount 
					from com_pi_item_details 
					where pi_id='$pi_id' and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, color_id, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, yarn_composition_percentage2, yarn_type, uom" );
					
				}
            }

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
                        </td>
                    <? 
                    } 
                    ?>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" onFocus="add_auto_complete( 1 )" style="width:<? if( $pi_basis_id== 1 ) echo "80px;"; else echo "100px;"; ?>"  maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                    <?
                        echo create_drop_down( "countName_1", 85, "SELECT id,yarn_count FROM lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count ASC",'id,yarn_count', 1, '-Select-',0,"",$disable_status); 
                    ?>                         
                    </td>
                    <td>
                        <?
                            if( $pi_basis_id == 1 ) $composition_item1_width = 75; else $composition_item1_width = 85;
                            echo create_drop_down( "yarnCompositionItem1_1",$composition_item1_width, $composition,'', 1, '-Select-',0,"control_composition(1,'comp_one')",$disable_status); 
                        ?>    
                        
                        <input type="text" name="yarnCompositionPercentage1_1" id="yarnCompositionPercentage1_1" class="text_boxes_numeric" value="100" onChange="control_composition(1,'percent_one')" style="width:25px;" disabled/>%
                    </td>
                    <td>
                        <?
							//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
                            if( $pi_basis_id == 1 ) $composition_item2_width = 75; else $composition_item2_width = 85;
                          	echo create_drop_down( "yarnCompositionItem2_1",$composition_item2_width, $composition,'', 1, '-Select-',0,"control_composition(1,'comp_two');",1); 
                        ?>   
                        <input type="text" name="yarnCompositionPercentage2_1" id="yarnCompositionPercentage2_1" class="text_boxes_numeric" value="" style="width:25px;" disabled/>
                    </td>
                    <td>
                        <?
                            if( $pi_basis_id == 1 ) $yarn_type_width = 70; else $yarn_type_width = 80;
                            echo create_drop_down( "yarnType_1",$yarn_type_width,$yarn_type,'', 1,'-Select-',0,"",$disable_status); 
                        ?>    
                    </td>
                    <td>
                        <?
                            if( $pi_basis_id == 1 ) $yarn_uom = 60; else $yarn_uom = 85;
                            echo create_drop_down( "uom_1", $yarn_uom, $unit_of_measurement,'', 0, '',12,'',1,12); 
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
				$data_array=sql_select("select item_category_id,goods_rcv_status,total_amount,upcharge,discount,net_total_amount from com_pi_master_details where id='$pi_id'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')]; 
				$item_category_id=$data_array[0][csf('item_category_id')];  
				$goods_rcv_status=$data_array[0][csf('goods_rcv_status')];  
				
				if( $pi_basis_id == 1 )
				{
					$wo_dtls_id='';
					foreach ($nameArray as $row)
					{
						$wo_dtls_id.=$row[csf('work_order_dtls_id')].',';
					}
					
					$wo_dtls_id=chop($wo_dtls_id,',');
					$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id) group by b.work_order_dtls_id", "work_order_dtls_id", "qty");
					
					if($goods_rcv_status==2)
					{
						$wo_qty_arr=return_library_array("select id, supplier_order_quantity as qty from wo_non_order_info_dtls where id in($wo_dtls_id)", "id", "qty");
					}
					else
					{
						$wo_qty_arr=return_library_array("select id, order_qnty as qty from inv_transaction where id in($wo_dtls_id)", "id", "qty");
					}
				}
				else
				{
					$mrr_check=return_field_value("id", "inv_receive_master", "booking_id=$pi_id and entry_form=1 and receive_basis=1 and status_active=1","id");
					if($mrr_check!=""){ $disable_mrr_check="disabled='disabled'"; $disable_status=1;} else $disable_mrr_check="";
				}
				
				$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count",'id','yarn_count');
				
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
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $i; ?>);" readonly <? echo $disable_qty_field; ?> />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                                <input type="hidden" name="hideTransData_<? echo $i; ?>" id="hideTransData_<? echo $i; ?>" value="" readonly />
                            </td>
                        <? 
							$bl_qty=$wo_qty_arr[$row[csf('work_order_dtls_id')]]-$prev_pi_qty_arr[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
                        } 
                        ?>
                        <td>
                            <input type="text" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:<? if( $pi_basis_id == 1 ) echo "80px;"; else echo "100px;"; ?>"  maxlength="50" <? echo $disable; ?> value="<? echo $color_library[$row[csf('color_id')]]; ?>" <? echo $disable_mrr_check; ?> />
                        </td>
                        <td>
							<?
                                echo create_drop_down( "countName_".$i, 85, $count_arr,'', 1, '-Select-',$row[csf('count_name')],"",$disable_status); 
                            ?>                         
                        </td>
                        <td>
                            <?
                                if( $pi_basis_id == 1 ) $composition_item1_width = 75; else $composition_item1_width = 85;
                                echo create_drop_down( "yarnCompositionItem1_".$i,$composition_item1_width, $composition,'', 1, '-Select-',$row[csf('yarn_composition_item1')],"control_composition($i,'comp_one')",$disable_status); 
                            ?>    
                            <input type="text" name="yarnCompositionPercentage1_<? echo $i; ?>" id="yarnCompositionPercentage1_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('yarn_composition_percentage1')]; ?>" onChange="control_composition(<? echo $i; ?>,'percent_one')" style="width:25px;" disabled/>%
                        </td>
                        <td>
                            <?
                                if( $pi_basis_id == 1 ) $composition_item2_width = 75; else $composition_item2_width = 85;
                                echo create_drop_down( "yarnCompositionItem2_".$i,$composition_item2_width, $composition,'', 1, '-Select-',$row[csf('yarn_composition_item2')],"control_composition($i,'comp_two')",1); 
                            ?>  
                            <input type="text" name="yarnCompositionPercentage2_<? echo $i; ?>" id="yarnCompositionPercentage2_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('yarn_composition_percentage2')]; ?>" onChange="control_composition(<? echo $i; ?>,'percent_two')" style="width:25px;" disabled/>
                        </td>
                        <td>
                            <?
                                if( $pi_basis_id == 1 ) $yarn_type_width = 70; else $yarn_type_width = 80;
                                echo create_drop_down( "yarnType_".$i,$yarn_type_width,$yarn_type,'', 1,'-Select-',$row[csf('yarn_type')],"",$disable_status); 
                            ?>    
                        </td>
                        <td>
                            <?
                                if( $pi_basis_id == 1 ) $yarn_uom = 60; else $yarn_uom = 85;
                                echo create_drop_down( "uom_".$i, $yarn_uom, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1,12); 
                            ?>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $i; ?>)" placeholder="<? echo $bl_qty; ?>" <? echo $disable_qty_field; ?> />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:45px;" onKeyUp="calculate_amount(<? echo $i; ?>)" <? echo $disable;?> />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" <? echo $disable;?> readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
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
                    <td>Upcharge</td>
                    <td align="center">
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
                    <td>Discount</td>
                    <td align="center">
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
                    <td>Net Total</td>
                    <td align="center">
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
		
		else if($item_category_id==2 || $item_category_id==3 || $item_category_id==13 || $item_category_id==14)
		{
			if($pi_basis_id==2)
            {
               $disable="";
                $disable_status=0; 
            }
            else
            {
				$disable="disabled='disabled'";
                $disable_status=1;
				if($goods_rcv_status==1) $disable_qty_field="disabled='disabled'"; else $disable_qty_field="";
            }
			
			if($item_category_id==13)
            {
                $disable_status_uom=1;
				$selected=12;
            }
            else
            {
				if($pi_basis_id==1)
				{
					 $disable_status_uom=1;
				}
				else
				{
					 $disable_status_uom=0;
				}
				$selected=0;
            }
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
                <th class="must_entry_caption">Construction</th>
                <th>Composition</th>
                <th class="must_entry_caption">Color</th>					
                <th><? if($item_category_id==2 || $item_category_id==13) echo "GSM"; else echo "Weight"; ?></th>
                <th class="must_entry_caption">Dia/Width</th>
                <th style="color:blue">UOM</th>
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
			
			if($goods_rcv_status==2)
            {
				$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, determination_id, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, booking_without_order, quantity, rate, amount from com_pi_item_details where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0" );
            }
            else
            {
				
				if($db_type==0)
				{
					
					$nameArray=sql_select( "select group_concat(id) as id, work_order_no, work_order_id, group_concat(work_order_dtls_id) as work_order_dtls_id, determination_id, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, booking_without_order, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount
					from com_pi_item_details 
					where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, determination_id, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, booking_without_order" );
					
				
				}
				else
				{
					
					$nameArray=sql_select( "select listagg(cast(id as varchar(4000)), ',') within group(order by id) as id, work_order_no, work_order_id, listagg(cast(work_order_dtls_id as varchar(4000)), ',') within group(order by work_order_dtls_id) as work_order_dtls_id, determination_id, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, booking_without_order, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount 
					from com_pi_item_details 
					where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, determination_id, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, booking_without_order" );
					
				}
            }
			

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
                        </td>
                    <? 
                    } 
                    ?>
                    <td> 
                        <input type="text" name="construction_1" id="construction_1" class="text_boxes" style="width:110px" onDblClick="openmypage_fabricDescription(1)" placeholder="Double Click To Search" readonly <? echo $disable; ?>/> <!--onFocus="add_auto_complete( 1 );"-->
                        <input type="hidden" name="hideDeterminationId_1" id="hideDeterminationId_1" readonly />
                    </td>
                    <td>
                        <input type="text" name="composition_1" id="composition_1" class="text_boxes" value="" style="width:120px" disabled="disabled"/> <!--onFocus="add_auto_complete(1);"-->
                    </td> 
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" onFocus="add_auto_complete( 1 )" value="" style="width:80px" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="gsm_1" id="gsm_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="diawidth_1" id="diawidth_1" class="text_boxes" onFocus="add_auto_complete( 1 )" value="" style="width:70px" <? echo $disable; ?>/>
                    </td>
                    <?
                    if( $pi_basis_id == 1 ) 
                    {
						?>
                        <td>
                        <input type="text" name="uom_1" id="uom_1" class="text_boxes" value="" style="width:50px;" placeholder="" readonly <? echo $disable; ?>/>
                        </td>
                        <?
					}
					else
					{
						if( $search[0] == 1 ) $yarn_uom = 60; else $yarn_uom = 85; 
						?>
                        <td>
							<? 
                                echo create_drop_down( "uom_1", $yarn_uom, $unit_of_measurement,'', 1, 'Select',$selected,'',$disable_status_uom);            
                            ?>						 
                        </td>
                        <?
					}
                    ?>
                    	
                     
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)" <? echo $disable_qty_field;?> />
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:45px;" onKeyUp="calculate_amount(1)" <? echo $disable; ?> />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" <? echo $disable; ?> readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
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
				$data_array=sql_select("select item_category_id,goods_rcv_status,total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_id'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')]; 
				$item_category_id=$data_array[0][csf('item_category_id')];  
				$goods_rcv_status=$data_array[0][csf('goods_rcv_status')]; 
				
				if( $pi_basis_id == 1 )
				{
					if($goods_rcv_status==2)
					{
						$wo_dtls_id=''; $wo_ids=''; 
						foreach ($nameArray as $row)
						{
							if($row[csf('booking_without_order')]==1)
							{
								$wo_dtls_id.=$row[csf('work_order_dtls_id')].',';
							}
							else
							{
								$wo_ids.=$row[csf('work_order_id')].',';
							}
						}
						$wo_dtls_id=chop($wo_dtls_id,',');
						$wo_ids=chop($wo_ids,',');
						
						if($wo_ids!="")
						{
							//$wo_qty_arr=return_library_array("select id, fin_fab_qnty as qty from wo_booking_dtls where id in($wo_ids)", "id", "qty");
							$wo_qty_arr=array();
							$sql_prev="select a.booking_no, c.lib_yarn_count_deter_id, b.fabric_color_id, c.construction, c.composition, b.gsm_weight, b.dia_width, c.uom, sum(b.fin_fab_qnty) as fin_fab_qnty from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.id in($wo_ids) group by a.booking_no, b.fabric_color_id, c.construction, c.composition, b.gsm_weight, b.dia_width, c.lib_yarn_count_deter_id, c.uom";
							$prevData=sql_select($sql_prev);
							foreach($prevData as $rowP)
							{
								$wo_qty_arr[$rowP[csf('booking_no')]][$rowP[csf('lib_yarn_count_deter_id')]][$rowP[csf('fabric_color_id')]][$rowP[csf('construction')]][$rowP[csf('composition')]][$rowP[csf('gsm_weight')]][$rowP[csf('dia_width')]][$rowP[csf('uom')]]=$rowP[csf('fin_fab_qnty')];
							}
							
							$prev_pi_qty_arr=array();
							$sql_prev="select b.work_order_no, b.determination_id, b.color_id, b.fabric_construction, b.fabric_composition, b.gsm, b.dia_width, b.uom, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 and b.work_order_id in($wo_ids) group by b.work_order_no, b.determination_id, b.color_id, b.fabric_construction, b.fabric_composition, b.gsm, b.dia_width, b.uom";
							$prevData=sql_select($sql_prev);
							foreach($prevData as $rowP)
							{
								$prev_pi_qty_arr[$rowP[csf('work_order_no')]][$rowP[csf('determination_id')]][$rowP[csf('color_id')]][$rowP[csf('fabric_construction')]][$rowP[csf('fabric_composition')]][$rowP[csf('gsm')]][$rowP[csf('dia_width')]][$rowP[csf('uom')]]=$rowP[csf('qty')];
							}
						}
						
						if($wo_dtls_id!="")
						{
							$wo_qty_arr2=return_library_array("select id, finish_fabric as qty from wo_non_ord_samp_booking_dtls where id in($wo_dtls_id2)", "id", "qty");
							
							$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id2) group by b.work_order_dtls_id", "work_order_dtls_id", "qty");
						}
					}
					else
					{
						$wo_dtls_id='';
						foreach ($nameArray as $row)
						{
							$wo_dtls_id.=$row[csf('work_order_dtls_id')].',';
						}
						
						$wo_dtls_id=chop($wo_dtls_id,',');
						$wo_qty_arr=return_library_array("select id, order_qnty as qty from inv_transaction where id in($wo_dtls_id)", "id", "qty");
						
						$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id) group by b.work_order_dtls_id", "work_order_dtls_id", "qty");
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
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $i; ?>);" readonly <? echo $disable_qty_field;?> />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                                <input type="hidden" name="hideTransData_<? echo $i; ?>" id="hideTransData_<? echo $i; ?>" value="" readonly />
                            </td>
                        <?
							if($goods_rcv_status==2 && $row[csf('booking_without_order')]!=1)
							{ 
								$bl_qty=$wo_qty_arr[$row[csf('work_order_no')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]]-$prev_pi_qty_arr[$row[csf('work_order_no')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]]+$row[csf('quantity')];
							}
							else
							{
								$bl_qty=$wo_qty_arr[$row[csf('work_order_dtls_id')]]-$prev_pi_qty_arr[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
							}
                        } 
                        ?>
                        <td> 
                            <input type="text" name="construction_<? echo $i; ?>" id="construction_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('fabric_construction')]; ?>" style="width:110px" onDblClick="openmypage_fabricDescription(<? echo $i; ?>)" placeholder="Double Click To Search" readonly <? echo $disable; ?>/>
                            <input type="hidden" name="hideDeterminationId_<? echo $i; ?>" id="hideDeterminationId_<? echo $i; ?>" value="<? echo $row[csf('determination_id')]; ?>" readonly />
                        </td>
                        <td>
                            <input type="text" name="composition_<? echo $i; ?>" id="composition_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('fabric_composition')]; ?>" style="width:120px" disabled="disabled"/>
                        </td> 
                        <td>
 							<input type="text" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:80px" maxlength="50" <? echo $disable; ?> value="<? echo $color_library[$row[csf('color_id')]]; ?>"/>                        
                        </td>
                        <td>
                            <input type="text" name="gsm_<? echo $i; ?>" id="gsm_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('gsm')]; ?>" style="width:60px"  disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="diawidth_<? echo $i; ?>" id="diawidth_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="<? echo $row[csf('dia_width')]; ?>" style="width:70px" <? echo $disable; ?>/>
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
							if( $pi_basis_id == 1 ) $yarn_uom = 60; else $yarn_uom = 85;
                            ?>
                            <td>
                                <?
									echo create_drop_down( "uom_".$i, $yarn_uom, $unit_of_measurement,'', 1, 'Select',$row[csf('uom')],'',$disable_status_uom); 
								?>						 
                            </td>
                            <?
                        }
						?>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $i; ?>)" placeholder="<? echo $bl_qty; ?>" <? echo $disable_qty_field; ?> />
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
		
		
		//else if($item_category_id==3 || $item_category_id==14)
		//{
			/*
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
                <th>Construction</th>
                <th>Composition</th>
                <th>Color</th>
                <th>Weight</th>
                <th>Width</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>Rate</th>
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
				if($goods_rcv_status==1) $disable_qty_field="disabled='disabled'"; else $disable_qty_field="";
            }
			
			
			if($goods_rcv_status==2)
            {
				$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, determination_id, color_id, fabric_construction, fabric_composition, weight, dia_width, uom, quantity, rate, amount from com_pi_item_details where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0" );
            }
            else
            {
				
				if($db_type==0)
				{
					$nameArray=sql_select( "select group_concat(id) as id, work_order_no, work_order_id, group_concat(work_order_dtls_id) as work_order_dtls_id, determination_id, color_id, fabric_construction, fabric_composition, weight, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount
					from com_pi_item_details 
					where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, determination_id, color_id, fabric_construction, fabric_composition, weight, dia_width, uom" );
					
				
				}
				else
				{
					
					$nameArray=sql_select( "select listagg(cast(id as varchar(4000)), ',') within group(order by id) as id, work_order_no, work_order_id, listagg(cast(work_order_dtls_id as varchar(4000)), ',') within group(order by work_order_dtls_id) as work_order_dtls_id, determination_id, color_id, fabric_construction, fabric_composition, weight, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount 
					from com_pi_item_details 
					where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, determination_id, color_id, fabric_construction, fabric_composition, weight, dia_width, uom" );
					
				}
            }*/

			/*if($type==1 || count($nameArray)<1)
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
                        </td>
                    <? 
                    } 
                    ?>
                    <td> 
                        <input type="text" name="construction_1" id="construction_1" class="text_boxes" style="width:110px" onDblClick="openmypage_fabricDescription(1)" placeholder="Double Click To Search" readonly <? echo $disable; ?>/>
                        <input type="hidden" name="hideDeterminationId_1" id="hideDeterminationId_1" readonly />
                    </td>
                    <td>
                        <input type="text" name="composition_1" id="composition_1" class="text_boxes" value="" style="width:120px" disabled="disabled"/>
                    </td> 
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" onFocus="add_auto_complete( 1 )" value="" style="width:80px" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="weight_1" id="weight_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="diawidth_1" id="diawidth_1" class="text_boxes" onFocus="add_auto_complete( 1 )" value="" style="width:70px" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <? 
                            if( $search[0] == 1 ) $yarn_uom = 60; else $yarn_uom = 85;
                            echo create_drop_down( "uom_1", $yarn_uom, $unit_of_measurement,'', 0, '',27,'',1,27);            
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
				$data_array=sql_select("select item_category_id,goods_rcv_status,total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_id'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')];
				$item_category_id=$data_array[0][csf('item_category_id')];  
				$goods_rcv_status=$data_array[0][csf('goods_rcv_status')]; 
				
				if( $pi_basis_id == 1 )
				{
					$wo_dtls_id='';
					foreach ($nameArray as $row)
					{
						$wo_dtls_id.=$row[csf('work_order_dtls_id')].',';
					}
					$wo_dtls_id=chop($wo_dtls_id,',');
					
					$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id) group by b.work_order_dtls_id", "work_order_dtls_id", "qty");
					
					if($goods_rcv_status==2)
					{
						$wo_qty_arr=return_library_array("select id, fin_fab_qnty as qty from wo_booking_dtls where id in($wo_dtls_id)", "id", "qty");
					}
					else
					{
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
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $i; ?>);" readonly <? echo $disable_qty_field;?> />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                                <input type="hidden" name="hideTransData_<? echo $i; ?>" id="hideTransData_<? echo $i; ?>" value="" readonly />
                            </td>
                        <? 
							$bl_qty=$wo_qty_arr[$row[csf('work_order_dtls_id')]]-$prev_pi_qty_arr[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
                        } 
                        ?>
                        <td> 
                            <input type="text" name="construction_<? echo $i; ?>" id="construction_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('fabric_construction')]; ?>" style="width:110px" onDblClick="openmypage_fabricDescription(<? echo $i; ?>)" placeholder="Double Click To Search" readonly <? echo $disable; ?>/>
                            <input type="hidden" name="hideDeterminationId_<? echo $i; ?>" id="hideDeterminationId_<? echo $i; ?>" value="<? echo $row[csf('determination_id')]; ?>" readonly />
                        </td>
                        <td>
                            <input type="text" name="composition_<? echo $i; ?>" id="composition_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('fabric_composition')]; ?>" style="width:120px" disabled="disabled"/>
                        </td> 
                        <td>
 							<input type="text" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:80px" maxlength="50" <? echo $disable; ?> value="<? echo $color_library[$row[csf('color_id')]]; ?>"/>                        
                        </td>
                        <td>
                            <input type="text" name="weight_<? echo $i; ?>" id="weight_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('weight')]; ?>" style="width:60px" disabled="disabled"/>
                        </td>
                        <td>
                            <input type="text" name="diawidth_<? echo $i; ?>" id="diawidth_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="<? echo $row[csf('dia_width')]; ?>" style="width:70px" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <?
                                if( $pi_basis_id == 1 ) $yarn_uom = 60; else $yarn_uom = 85;
                                echo create_drop_down( "uom_".$i, $yarn_uom, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1,27); 
                            ?>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $i; ?>)" placeholder="<? echo $bl_qty; ?>" <? echo $disable_qty_field;?> />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:45px;" onKeyUp="calculate_amount(<? echo $i; ?>)" <? echo $disable; ?> />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" <? echo $disable; ?> readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
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
            <? */
		    
		//}
		
		
		else if($item_category_id==4)
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
;
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
                                //echo create_drop_down( "itemgroupid_1", 110, "SELECT id,item_name FROM lib_item_group WHERE item_category =$item_category_id AND status_active = 1 AND is_deleted = 0 ORDER BY item_name ASC",'id,item_name', 1, '-Select-',0,"get_php_form_data( this.value+'**'+'uom_1', 'get_uom', 'requires/pi_service_fabric_controller' );",$disable_status); 
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
                                echo create_drop_down( "itemgroupid_1", 110, "SELECT id,item_name FROM lib_item_group WHERE item_category =$item_category_id AND status_active = 1 AND is_deleted = 0 ORDER BY item_name ASC",'id,item_name', 1, '-Select-',0,"get_php_form_data( this.value+'**'+'uom_1', 'get_uom', 'requires/pi_service_fabric_controller' );",$disable_status); 
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
							$sql_prev="select a.booking_no, b.id, b.trim_group, b.uom, c.description, c.rate, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier, sum(c.cons) as qnty from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and b.id in($wo_dtls_id) group by a.booking_no, b.id, b.trim_group, b.uom, c.description, c.rate, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier";
							$prevData=sql_select($sql_prev);
							foreach($prevData as $rowP)
							{
								$wo_qty_arr[$rowP[csf('booking_no')]][$rowP[csf('id')]][$rowP[csf('color_number_id')]][$rowP[csf('trim_group')]][$rowP[csf('description')]][$rowP[csf('gmts_sizes')]][$rowP[csf('item_color')]][$rowP[csf('item_size')]][$rowP[csf('brand_supplier')]][$rowP[csf('uom')]][$rowP[csf('rate')]]=$rowP[csf('qnty')];
							}
							
							$prev_pi_qty_arr=array();
							$sql_prev="select b.work_order_no, b.work_order_dtls_id, b.color_id, b.item_group, b.item_description, b.size_id, b.item_color, b.item_size, b.uom, b.rate, b.brand_supplier, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id_all) group by b.work_order_no, b.work_order_dtls_id, b.color_id, b.item_group, b.item_description, b.size_id, b.item_color, b.item_size, b.uom, b.rate, b.brand_supplier";
							$prevData=sql_select($sql_prev);
							foreach($prevData as $rowP)
							{
								$prev_pi_qty_arr[$rowP[csf('work_order_no')]][$rowP[csf('work_order_dtls_id')]][$rowP[csf('color_id')]][$rowP[csf('item_group')]][$rowP[csf('item_description')]][$rowP[csf('size_id')]][$rowP[csf('item_color')]][$rowP[csf('item_size')]][$rowP[csf('brand_supplier')]][$rowP[csf('uom')]][$rowP[csf('rate')]]=$rowP[csf('qty')];
							}
							
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
								$bl_qty=$wo_qty_arr[$row[csf('work_order_no')]][$row[csf('work_order_dtls_id')]][$row[csf('color_id')]][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('size_id')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('brand_supplier')]][$row[csf('uom')]][$row[csf('rate')]]-$prev_pi_qty_arr[$row[csf('work_order_no')]][$row[csf('work_order_dtls_id')]][$row[csf('color_id')]][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('size_id')]][$row[csf('item_color')]][$row[csf('item_size')]][$row[csf('brand_supplier')]][$row[csf('uom')]][$row[csf('rate')]]+$row[csf('quantity')];
							}
							else
							{
								$bl_qty=$wo_qty_arr[$row[csf('work_order_dtls_id')]]-$prev_pi_qty_arr[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
							}
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
								echo create_drop_down( "itemgroupid_".$i, 110, $item_group_arr,'', 1, '-Select-',$row[csf('item_group')],"get_php_form_data( this.value+'**'+'uom_$i', 'get_uom', 'requires/pi_service_fabric_controller' );",$disable_status); 
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
		
		else if($item_category_id==12)
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
                <th class="must_entry_caption">Service Type</th>
                <th class="must_entry_caption">Description</th>
                <th>Gmts Color</th>
                <th>Gmts Size</th>
                <th>Item Color</th>
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
            }
			
			$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, color_id, service_type, item_description, size_id, item_color, item_size, uom, quantity, rate, amount, booking_without_order from com_pi_item_details where pi_id='$pi_id' and status_active=1 and is_deleted=0" );

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
                        </td>
                    <? 
                    } 
                    ?>
                    <td> 
                    	<? echo create_drop_down( "servicetype_1", 110, $conversion_cost_head_array,'', 1,'-Select-',0,"",$disable_status); ?> 
                    </td>
                    <td>
                        <input type="text" name="itemdescription_1" id="itemdescription_1" class="text_boxes" value="" style="width:150px" maxlength="200" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" onFocus="add_auto_complete( 1 )" value="" style="width:80px" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="sizeName_1" id="sizeName_1" class="text_boxes" value="" onFocus="add_auto_complete( 1 )" style="width:70px;" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="itemColor_1" id="itemColor_1" class="text_boxes" value="" onFocus="add_auto_complete( 1 )" style="width:80px" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="itemSize_1" id="itemSize_1" class="text_boxes" value="" style="width:70px;" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <? 
                            echo create_drop_down( "uom_1", 60, $unit_of_measurement,'', 0, '',0,'',0);            
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
				$data_array=sql_select("select total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_id'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')];
				
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
					
					$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=12 and a.pi_basis_id=1 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id_all) group by b.work_order_dtls_id", "work_order_dtls_id", "qty");
					
					if($wo_dtls_id!="")
					{
						$wo_qty_arr=return_library_array("select id, wo_qnty as qty from wo_booking_dtls where id in($wo_dtls_id)", "id", "qty");
					}
					
					if($wo_dtls_id2!="")
					{
						$wo_qty_arr2=return_library_array("select id, wo_qty as qty from wo_non_ord_aop_booking_dtls where id in($wo_dtls_id2)", "id", "qty");
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
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $i; ?>);" readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                            </td>
                        <? 
							if($row[csf('booking_without_order')]==1)
							{ 
								$bl_qty=$wo_qty_arr2[$row[csf('work_order_dtls_id')]]-$prev_pi_qty_arr[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
							}
							else
							{
								$bl_qty=$wo_qty_arr[$row[csf('work_order_dtls_id')]]-$prev_pi_qty_arr[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
							}
                        } 
                        ?>
                        <td>
							<? echo create_drop_down( "servicetype_".$i, 110, $conversion_cost_head_array,'', 1,'-Select-',$row[csf('service_type')],"",$disable_status); ?>
                        </td>
                        <td>
                            <input type="text" name="itemdescription_<? echo $i; ?>" id="itemdescription_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('item_description')]; ?>" style="width:150px" maxlength="200" <? echo $disable; ?>/>
                        </td>
                        <td>
 							<input type="text" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:80px" maxlength="50" <? echo $disable; ?> value="<? echo $color_library[$row[csf('color_id')]]; ?>"/>                        
                        </td>
                        <td>
                            <input type="text" name="sizeName_<? echo $i; ?>" id="sizeName_<? echo $i; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('size_id')]]; ?>" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:70px;" maxlength="50" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <input type="text" name="itemColor_<? echo $i; ?>" id="itemColor_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" value="<? echo $color_library[$row[csf('item_color')]]; ?>" style="width:80px" maxlength="50" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <input type="text" name="itemSize_<? echo $i; ?>" id="itemSize_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('item_size')]; ?>" style="width:70px;" maxlength="50" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "uom_".$i, 60, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',$disable_status); 
                            ?>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $i; ?>)" placeholder="<? echo $bl_qty; ?>" />
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
		
		else if($item_category_id==24)
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
                <th class="must_entry_caption">Lot</th>
                <th>Count</th>
                <th>Yarn Description</th>
                <th>Yarn Color</th>
                <th>Color Range</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
                <? 
                if($pi_basis_id == 2 ) 
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
				$placeholder="Doublic Click";
            }
            else
            {
				$disable="disabled='disabled'";
                $disable_status=1;
				$placeholder="";
				if($goods_rcv_status==1) $disable_field="disabled='disabled'"; else $disable_field="";
            }
			
			$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count WHERE status_active = 1 AND is_deleted = 0 ORDER BY yarn_count",'id','yarn_count');
			
			$colorIds=array();
			
			
			
			if($goods_rcv_status==2)
            {
				$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, lot_no, yarn_color, count_name, color_range, item_description, item_prod_id, uom, quantity, rate, amount from com_pi_item_details where pi_id='$pi_id' and status_active=1 and is_deleted=0" );
            }
            else
            {
				
				if($db_type==0)
				{
					
					$nameArray=sql_select( "select group_concat(id) as id, work_order_no, work_order_id, group_concat(work_order_dtls_id) as work_order_dtls_id, lot_no, yarn_color, count_name, color_range, item_description, item_prod_id, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount
					from com_pi_item_details 
					where pi_id='$pi_id' and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, lot_no, yarn_color, count_name, color_range, item_description, item_prod_id, uom" );
					
				
				}
				else
				{
					
					$nameArray=sql_select( "select listagg(cast(id as varchar(4000)), ',') within group(order by id) as id, work_order_no, work_order_id, listagg(cast(work_order_dtls_id as varchar(4000)), ',') within group(order by work_order_dtls_id) as work_order_dtls_id, lot_no, yarn_color, count_name, color_range, item_description, item_prod_id, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount  
					from com_pi_item_details 
					where pi_id='$pi_id' and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, lot_no, yarn_color, count_name, color_range, item_description, item_prod_id, uom" );
					
				}
            }
			
			if($type==1 || count($nameArray)<1)
            {
				$color_array=return_library_array("select id,color_name from lib_color WHERE status_active=1 AND is_deleted=0",'id','color_name');
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
                            <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:115px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                            <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                            <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                        </td>
                    <? 
                    } 
                    ?>
                    <td>
                        <input type="text" name="lot_1" id="lot_1" class="text_boxes" value="" style="width:80px" maxlength="200" <? echo $disable; ?> onDblClick="openmypage_item_desc(1);" placeholder="<? echo $placeholder; ?>" readonly/>
                        <input type="hidden" name="itemProdId_1" id="itemProdId_1" readonly value=""/> 
                    </td>
                    <td>
                    	<? echo create_drop_down( "countName_1", 90, $count_arr,'', 1, '-Select-', 0,"",1); ?>
                    </td>
                    <td>
                        <input type="text" name="itemdescription_1" id="itemdescription_1" class="text_boxes" value="" style="width:200px" maxlength="200" disabled/>
                    </td>
                    <td>
                    	<? echo create_drop_down( "yarnColor_1", 110, $color_array,'', 1, '-Select-', 0,"",$disable_status); ?>
                    </td>
                    <td>
                        <? echo create_drop_down( "colorRange_1", 110, $color_range,'', 1, '-Select-', 0,"",$disable_status); ?>
                    </td>
                    <td>
                        <? 
							echo create_drop_down( "uom_1", 60, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1,12); 
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
				foreach ($nameArray as $row)
				{
					$colorIds[$row[csf('yarn_color')]]=$row[csf('yarn_color')];
				}
				
				if(count($colorIds)>0)
				{
					$colorIds=implode(",",$colorIds);
				}
				else $colorIds=0;
				
				$color_array=return_library_array("select id,color_name from lib_color WHERE status_active=1 AND is_deleted=0 and id in($colorIds)",'id','color_name');
				
				$data_array=sql_select("select goods_rcv_status, total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_id'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')]; 
				$goods_rcv_status=$data_array[0][csf('goods_rcv_status')];
				
				if( $pi_basis_id == 1 )
				{
					$wo_dtls_id='';
					foreach ($nameArray as $row)
					{
						$wo_dtls_id.=$row[csf('work_order_dtls_id')].',';
					}
					$wo_dtls_id=chop($wo_dtls_id,',');
					
					$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=24 and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id) group by b.work_order_dtls_id", "work_order_dtls_id", "qty");
					
					if($goods_rcv_status==2)
					{
						$wo_qty_arr=return_library_array("select id, yarn_wo_qty as qty from wo_yarn_dyeing_dtls where id in($wo_dtls_id)", "id", "qty");
					}
					else
					{
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
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:115px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $i; ?>);" <? echo $disable_field;?> readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                                <input type="hidden" name="hideTransData_<? echo $i; ?>" id="hideTransData_<? echo $i; ?>" value="" readonly />
                            </td>
                        <? 
							$bl_qty=$wo_qty_arr[$row[csf('work_order_dtls_id')]]-$prev_pi_qty_arr[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
                        } 
                        ?>
                        <td>
                            <input type="text" name="lot_<? echo $i; ?>" id="lot_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('lot_no')];?>" style="width:80px" maxlength="200" <? echo $disable; ?> onDblClick="openmypage_item_desc(<? echo $i; ?>);" placeholder="<? echo $placeholder; ?>" readonly/>
                            <input type="hidden" name="itemProdId_<? echo $i; ?>" id="itemProdId_<? echo $i; ?>" readonly value="<? echo $row[csf('item_prod_id')];?>"/> 
                        </td>
                        <td>
                            <? echo create_drop_down( "countName_".$i, 90, $count_arr,'', 1, '-Select-', $row[csf('count_name')],"",1); ?>
                        </td>
                        <td>
                            <input type="text" name="itemdescription_<? echo $i; ?>" id="itemdescription_<? echo $i; ?>" class="text_boxes" style="width:200px" value="<? echo $row[csf('item_description')];?>" disabled/>
                        </td>
                        <td>
                            <? echo create_drop_down( "yarnColor_".$i, 110, $color_array,'', 1, '-Select-', $row[csf('yarn_color')],"",$disable_status); ?>
                        </td>
                        <td>
                            <? echo create_drop_down( "colorRange_".$i, 110, $color_range,'', 1, '-Select-', $row[csf('color_range')],"",$disable_status); ?>
                        </td>
                        <td>
                            <?
								echo create_drop_down( "uom_".$i, 60, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1,12);
                            ?>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $i; ?>)" placeholder="<? echo $bl_qty; ?>" <? echo $disable_field;?> />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:45px;" onKeyUp="calculate_amount(<? echo $i; ?>)" <? echo $disable; ?> />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" <? echo $disable; ?> readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
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
		
		else if($item_category_id==25)
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
                <th class="must_entry_caption">Gmts Item</th>
                <th class="must_entry_caption">Embellishment Name</th>
                <th class="must_entry_caption">Embellishment Type</th>
                <th>Gmts Color</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
                <? 
                if($pi_basis_id == 2 ) 
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
            }
			
			$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, color_id, gmts_item_id, embell_name, embell_type, uom, quantity, rate, amount from com_pi_item_details where pi_id='$pi_id' and status_active=1 and is_deleted=0" );

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
                        </td>
                    <? 
                    } 
                    ?>
                    <td> 
                    	<? echo create_drop_down( "gmtsitem_1", 150, $garments_item,'', 1, '-Select-', 0,"",$disable_status); ?> 
                    </td>
                    <td>
                    	<? echo create_drop_down( "embellname_1", 130, $emblishment_name_array,'', 1, '-Select-', 0,"load_drop_down( 'requires/pi_service_fabric_controller', this.value+'**'+".$disable_status."+'**'+'embelltype_1', 'load_drop_down_embelltype', 'embelltypeTd_1');",$disable_status); ?>
                    </td>
                    <td id="embelltypeTd_1">
                    	<? echo create_drop_down( "embelltype_1", 130, $blank_array,'', 1, '-Select-', 0,"",$disable_status); ?>
                    </td>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" onFocus="add_auto_complete( 1 )" style="width:90px;" maxlength="50" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <? 
							echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 0, '',0,'',1,2);           
                        ?>		
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:81px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:50px;" onKeyUp="calculate_amount(1)" <? echo $disable; ?> />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:85px;" <? echo $disable; ?> readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
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
				$data_array=sql_select("select total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_id'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')]; 
				
				if( $pi_basis_id == 1 )
				{
					$wo_dtls_id='';
					foreach ($nameArray as $row)
					{
						$wo_dtls_id.=$row[csf('work_order_dtls_id')].',';
					}
					$wo_dtls_id=chop($wo_dtls_id,',');
					
					$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=25 and a.pi_basis_id=1 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id) group by b.work_order_dtls_id", "work_order_dtls_id", "qty");
					
					$wo_qty_arr=return_library_array("select id, wo_qnty as qty from wo_booking_dtls where id in($wo_dtls_id)", "id", "qty");
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
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $i; ?>);" readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                            </td>
                        <? 
							$bl_qty=$wo_qty_arr[$row[csf('work_order_dtls_id')]]-$prev_pi_qty_arr[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
                        } 
                        ?>
                        <td>
                        	<? echo create_drop_down( "gmtsitem_".$i, 150, $garments_item,'', 1, '-Select-', $row[csf('gmts_item_id')],"",$disable_status); ?>
                        </td>
                        <td>
                            <? echo create_drop_down( "embellname_".$i, 130, $emblishment_name_array,'', 1, '-Select-', $row[csf('embell_name')],"load_drop_down( 'requires/pi_service_fabric_controller', this.value+'**'+".$disable_status."+'**'+'embelltype_$i', 'load_drop_down_embelltype', 'embelltypeTd_$i');",$disable_status); ?>
                        </td>
                        <td id="embelltypeTd_<? echo $i; ?>">
                        	<?
								$emb_arr=array();
								if($row[csf('embell_name')]==1) $emb_arr=$emblishment_print_type;
								else if($row[csf('embell_name')]==2) $emb_arr=$emblishment_embroy_type;
								else if($row[csf('embell_name')]==3) $emb_arr=$emblishment_wash_type;
								else if($row[csf('embell_name')]==4) $emb_arr=$emblishment_spwork_type;
								else $emb_arr=$blank_array;
								 
								echo create_drop_down( "embelltype_".$i, 130, $emb_arr,'', 1, '-Select-', $row[csf('embell_type')],"",$disable_status); 
							?>
                        </td>
                        <td>
                            <input type="text" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" onFocus="add_auto_complete( <? echo $i; ?> )" style="width:90px" maxlength="50" <? echo $disable; ?> value="<? echo $color_library[$row[csf('color_id')]]; ?>"/>
                        </td>
                        <td>
                            <?
								echo create_drop_down( "uom_".$i, 70, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1,2); 
                            ?>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:81px;" onKeyUp="calculate_amount(<? echo $i; ?>)" placeholder="<? echo $bl_qty; ?>" />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:50px;" onKeyUp="calculate_amount(<? echo $i; ?>)" <? echo $disable; ?> />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:85px;" <? echo $disable; ?> readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
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
                    <td>Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $tot_amnt; ?>" style="width:85px;" readonly/>
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
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:85px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:85px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $tot_amnt_net; ?>" style="width:85px;" readonly/>
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
		
		else if($item_category_id==31)
		{
			$test_item_arr=return_library_array( "SELECT id,test_item FROM lib_lab_test_rate_chart",'id','test_item');
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
                <th class="must_entry_caption" style="color:blue">Test For</th>
                <th>Remarks</th>
                <th>Color</th>
                <th>Test Item</th>
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
				$disableCombo=0;
				$placeholder="Doublic Click";
            }
            else
            {
				$disable="disabled='disabled'";
				$disableCombo=1;
				$placeholder="Display";
            }

			$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, test_for, test_item_id, remarks, color_id, amount from com_pi_item_details where pi_id='$pi_id' and status_active=1 and is_deleted=0" );

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
                            <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                            <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                            <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                        </td>
                    <? 
                    } 
                    ?>
                    <td> 
						 <?
                            echo create_drop_down( "cboTestFor_1", 140, $test_for,'', 1, '-Select-',0,"",$disableCombo); 
                         ?>  
                    </td>
                    <td>

                        <input type="text" name="remarks_1" id="remarks_1" class="text_boxes" value="" style="width:170px;" maxlength="250" <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:100px;" maxlength="100" <? echo $disable; ?> />
                    </td>
                    <td>
                        <input type="text" name="txtTestItem_1" id="txtTestItem_1" class="text_boxes" value="" style="width:200px" maxlength="200" <? echo $disable; ?> onDblClick="openmypage_test_item(1);" placeholder="<? echo $placeholder; ?>" readonly/>
                        <input type="hidden" name="testItemId_1" id="testItemId_1" readonly value=""/> 
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:85px;" onKeyUp="calculate_total_amount(1);" <? echo $disable; ?> />
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
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
				$data_array=sql_select("select total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_id'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')];
				
				if( $pi_basis_id == 1 )
				{
					$wo_dtls_id='';
					foreach ($nameArray as $row)
					{
						$wo_dtls_id.=$row[csf('work_order_dtls_id')].',';
					}
					$wo_dtls_id=chop($wo_dtls_id,',');
					
					$prev_pi_amnt_arr=return_library_array( "select b.work_order_dtls_id,sum(b.amount) as amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=31 and a.pi_basis_id=1 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id) group by b.work_order_dtls_id", "work_order_dtls_id", "amount");
					
					$wo_amnt_arr=return_library_array("select id, wo_value as amnt from wo_labtest_dtls where id in($wo_dtls_id)", "id", "amnt"); 
				}
				
				$i=1;
				foreach ($nameArray as $row)
				{
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
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $i; ?>);" readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                            </td>
                        <? 
							$bl_amnt=$wo_amnt_arr[$row[csf('work_order_dtls_id')]]-$prev_pi_amnt_arr[$row[csf('work_order_dtls_id')]]+$row[csf('amount')];
                        } 
                        ?>
                        <td> 
							 <?
                                echo create_drop_down( "cboTestFor_".$i, 140, $test_for,'', 1, '-Select-',$row[csf('test_for')],"",$disableCombo); 
                             ?>  
                        </td>
                        <td>
                            <input type="text" name="remarks_<? echo $i; ?>" id="remarks_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('remarks')]; ?>" style="width:170px;" maxlength="250" <? echo $disable; ?>/>
                        </td>
                        <td>
                            <input type="text" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:100px;" maxlength="100" <? echo $disable; ?> />
                        </td>
                        <td>
                        	<?
								$test_item='';
								$test_item_ids=array_unique(explode(",",$row[csf('test_item_id')]));
								foreach($test_item_ids as $test_item_id)
								{
									$test_item.=$test_item_arr[$test_item_id].",";
								}
								$test_item=chop($test_item,',');
							?>
                            <input type="text" name="txtTestItem_<? echo $i; ?>" id="txtTestItem_<? echo $i; ?>" class="text_boxes" value="<? echo $test_item; ?>" style="width:200px" maxlength="200" <? echo $disable; ?> onDblClick="openmypage_test_item(<? echo $i; ?>);" placeholder="<? echo $placeholder; ?>" readonly/>
                            <input type="hidden" name="testItemId_<? echo $i; ?>" id="testItemId_<? echo $i; ?>" readonly value="<? echo $row[csf('test_item_id')]; ?>"/> 
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:85px;" onKeyUp="check_amount(<? echo $i; ?>);" placeholder="<? echo $bl_amnt; ?>" <? echo $disable; ?> />
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
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
                <td>Total</td>
                <td style="text-align:center">
                    <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $tot_amnt; ?>" style="width:85px;" readonly/>
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
                <td>Upcharge</td>
                <td style="text-align:center">
                    <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:85px;" onKeyUp="calculate_total_amount(2)"/>
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
                <td>Discount</td>
                <td style="text-align:center">
                    <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:85px;" onKeyUp="calculate_total_amount(2)"/>
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
                <td>Net Total</td>
                <td style="text-align:center">
                    <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $tot_amnt_net; ?>" style="width:85px;" readonly/>
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
		
		else
		{
			$item_group_arr=return_library_array( "SELECT id,item_name FROM lib_item_group",'id','item_name');
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
				$placeholder="Doublic Click";
            }
            else
            {
				$disable="disabled='disabled'";
				$placeholder="";
				if($goods_rcv_status==1) $disable_field="disabled='disabled'"; else $disable_field="";
            }
			
			if($goods_rcv_status==2)
            {
				$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id, item_prod_id, item_group, item_description, item_size, uom, quantity, rate, amount from com_pi_item_details where pi_id='$pi_id' and status_active=1 and is_deleted=0" );
            }
            else
            {
				
				if($db_type==0)
				{
					
					$nameArray=sql_select( "select group_concat(id) as id, work_order_no, work_order_id, group_concat(work_order_dtls_id) as work_order_dtls_id, item_prod_id, item_group, item_description, item_size, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount 
					from com_pi_item_details 
					where pi_id='$pi_id' and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, item_prod_id, item_group, item_description, item_size, uom" );
					
				
				}
				else
				{
					
					$nameArray=sql_select( "select listagg(cast(id as varchar(4000)), ',') within group(order by id) as id, work_order_no, work_order_id, listagg(cast(work_order_dtls_id as varchar(4000)), ',') within group(order by work_order_dtls_id) as work_order_dtls_id, item_prod_id, item_group, item_description, item_size, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount  
					from com_pi_item_details 
					where pi_id='$pi_id' and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, item_prod_id, item_group, item_description, item_size, uom" );
					
				}
            }
			

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
                        </td>
                    <? 
                    } 
                    ?>
                    <td> 
						 <?
                            echo create_drop_down( "itemgroupid_1", 130, $item_group_arr,'', 1, '-Select-',0,"get_php_form_data( this.value+'**'+'uom_1', 'get_uom', 'requires/pi_service_fabric_controller' );",1); 
                         ?>  
                    </td>
                    <td>
                        <input type="text" name="itemdescription_1" id="itemdescription_1" class="text_boxes" value="" style="width:200px" maxlength="200" <? echo $disable; ?> onDblClick="openmypage_item_desc(1);" placeholder="<? echo $placeholder; ?>" readonly/>
                        <input type="hidden" name="itemProdId_1" id="itemProdId_1" readonly value=""/> 
                    </td>
                    <td>
                        <input type="text" name="itemSize_1" id="itemSize_1" class="text_boxes" value="" style="width:90px;" maxlength="50" disabled="disabled"/>
                    </td>
                    <td>
                        <? 
                            echo create_drop_down( "uom_1", 80, $unit_of_measurement,'', 0, '',0,'',1);            
                        ?>		
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:90px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_amount(1)" <? echo $disable; ?> />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:85px;" <? echo $disable; ?> readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
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
				$data_array=sql_select("select item_category_id,goods_rcv_status,total_amount, upcharge, discount, net_total_amount from com_pi_master_details where id='$pi_id'");
				$tot_amnt=$data_array[0][csf('total_amount')]; 
				$upcharge=$data_array[0][csf('upcharge')];  
				$discount=$data_array[0][csf('discount')];  
				$tot_amnt_net=$data_array[0][csf('net_total_amount')];
				$item_category_id=$data_array[0][csf('item_category_id')];  
				$goods_rcv_status=$data_array[0][csf('goods_rcv_status')];  
				
				if( $pi_basis_id == 1 )
				{
					$wo_dtls_id='';
					foreach ($nameArray as $row)
					{
						$wo_dtls_id.=$row[csf('work_order_dtls_id')].',';
					}
					
					$wo_dtls_id=chop($wo_dtls_id,',');
					$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id) group by b.work_order_dtls_id", "work_order_dtls_id", "qty");
					
					$wo_qty_arr=return_library_array("select id, supplier_order_quantity as qty from wo_non_order_info_dtls where id in($wo_dtls_id)", "id", "qty");
					/*if($goods_rcv_status==2)
					{
						$wo_qty_arr=return_library_array("select id, supplier_order_quantity as qty from wo_non_order_info_dtls where id in($wo_dtls_id)", "id", "qty");
					}
					else
					{
						$wo_qty_arr=return_library_array("select id, order_qnty as qty from inv_transaction where id in($wo_dtls_id)", "id", "qty");
					} */
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
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $i; ?>);" <? echo $disable_field; ?> readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                                <input type="hidden" name="hideTransData_<? echo $i; ?>" id="hideTransData_<? echo $i; ?>" value="" readonly />
                            </td>
                        <? 
							$bl_qty=$wo_qty_arr[$row[csf('work_order_dtls_id')]]-$prev_pi_qty_arr[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
                        } 
                        ?>
                        <td> 
						 <?
                            echo create_drop_down( "itemgroupid_".$i, 130, $item_group_arr,'', 1, '-Select-',$row[csf('item_group')],"get_php_form_data( this.value+'**'+'uom_$i', 'get_uom', 'requires/pi_service_fabric_controller' );",1); 
                         ?>  
                    	</td>
                        <td>
                            <input type="text" name="itemdescription_<? echo $i; ?>" id="itemdescription_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('item_description')]; ?>" style="width:200px" maxlength="200" <? echo $disable; ?> onDblClick="openmypage_item_desc(<? echo $i; ?>);" readonly />
                            <input type="hidden" name="itemProdId_<? echo $i; ?>" id="itemProdId_<? echo $i; ?>" readonly value="<? echo $row[csf('item_prod_id')]; ?>"/> 
                        </td>
                        <td>
                            <input type="text" name="itemSize_<? echo $i; ?>" id="itemSize_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('item_size')]; ?>" style="width:90px;" maxlength="50"  disabled="disabled"/>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "uom_".$i, 80, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1); 
                            ?>
                        </td>
                        <td>
                            <input type="text" name="quantity_<? echo $i; ?>" id="quantity_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:90px;" onKeyUp="calculate_amount(<? echo $i; ?>)" placeholder="<? echo $bl_qty; ?>" <? echo $disable_field; ?> />
                        </td>
                        <td>
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:75px;" onKeyUp="calculate_amount(<? echo $i; ?>)" <? echo $disable; ?> />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:85px;" <? echo $disable; ?> readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
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
                    <td>Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $tot_amnt; ?>" style="width:85px;" readonly/>
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
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:85px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:85px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $tot_amnt_net; ?>" style="width:85px;" readonly/>
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
						 <? echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--Select--',$importer_id,"load_drop_down( 'pi_service_fabric_controller',this.value+'_'+$item_category, 'load_supplier_dropdown', 'supplier_td' );",0); ?>       
                    </td>
                    <td id="supplier_td">	
                        <?
							echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_item_category').value+'_'+document.getElementById('cbo_importer_id').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_system_id').value, 'create_pi_search_list_view', 'search_div', 'pi_service_fabric_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
	
	$arr=array(2=>$comp,3=>$supplier,6=>$pi_basis);
	 
	//$sql= "select id,pi_number,pi_date,item_category_id,importer_id,supplier_id,last_shipment_date,hs_code,pi_basis_id from com_pi_master_details where supplier_id like '$supplier_id' and importer_id=$importer_id and item_category_id=$item_category_id and entry_form=168 and status_active=1 and is_deleted=0 $pi_number $pi_date $system_id_cond order by pi_number";

	$sql = "select distinct(a.id),a.pi_number,a.pi_date,a.importer_id,a.supplier_id,a.last_shipment_date,a.hs_code,a.pi_basis_id 
        from com_pi_master_details a left join com_pi_item_details b on a.id = b.pi_id  and b.item_category_id = $item_category_id and b.status_active=1 and b.is_deleted=0
        where a.supplier_id like '$supplier_id' and importer_id=$importer_id
        and a.entry_form=168 and a.version = 2 and a.status_active = 1 and a.is_deleted = 0
        order by a.pi_number";
	
	echo create_list_view("list_view", "PI No,PI Date,Importer,Supplier,Last Shipment Date,HS Code,PI Basis, System ID", "90,75,120,100,90,80,100","890","270",0, $sql , "js_set_value", "id", "", 1, "0,0,importer_id,supplier_id,0,0,pi_basis_id,0", $arr , "pi_number,pi_date,importer_id,supplier_id,last_shipment_date,hs_code,pi_basis_id,id", "",'','0,3,0,0,3,0,0,0');
	 
	exit();	
} 

if ($action=="populate_data_from_search_popup")
{
	$userName_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$data_array=sql_select("select id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, approved, ready_to_approved, lc_group_no, approval_user, goods_rcv_status from com_pi_master_details where id='$data'");
	foreach ($data_array as $row)
	{
		//echo "document.getElementById('cbo_item_category_id').value = '".$row[csf("item_category_id")]."';\n";  
		echo "document.getElementById('cbo_importer_id').value = '".$row[csf("importer_id")]."';\n";  
		
		echo "load_drop_down('requires/pi_service_fabric_controller',".$row[csf("importer_id")]."+'_'+'12', 'load_supplier_dropdown', 'supplier_td' );\n";
		
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

	//echo $previous_wo_ids;die;
	
?> 

	<script>
	
		var item_category_id=<? echo $item_category_id; ?>;
		
		/*$(document).ready(function(e) {
            load_drop_down( 'pi_service_fabric_controller',<?echo $importer_id; ?>+'_'+item_category, 'load_supplier_dropdown', 'supplier_td' );
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

			var id_sensitivity_ref=str[1].split("*");
			var id_sensitivity=id_sensitivity_ref[0];
			var id_sensitivity_type=id_sensitivity_ref[1];
			
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
                    	echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',$importer_id,"load_drop_down( 'pi_service_fabric_controller',this.value+'_'+$item_category_id, 'load_supplier_dropdown', 'supplier_td' );",$dis_ana); 
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

						echo create_drop_down( "cbo_based_on", 100, $wo_based_on,"", 0, "", 1, "",0 );
					?>
                    </td> 
                    
            		 <td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_wo_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_item_category').value+'_'+document.getElementById('cbo_importer_id').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_based_on').value+'_'+document.getElementById('txt_goods_rcv_status').value+'_'+'<? echo $prev_wo_ids; ?>'+'_'+'<? echo $previous_wo_ids; ?>'+'_'+'<? echo $prev_deters; ?>'+'_'+'<? echo $prev_colors; ?>'+'_'+'<? echo $prev_constructs; ?>'+'_'+'<? echo $prev_compositions; ?>'+'_'+'<? echo $prev_gsms; ?>'+'_'+'<? echo $prev_widths; ?>'+'_'+'<? echo $prev_uoms; ?>', 'create_wo_search_list_view', 'search_div', 'pi_service_fabric_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
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
	
	if ($data[0]!="") $wo_number=" and a.booking_no like '%".trim($data[0])."'"; else { $wo_number = ''; }
	
	if ($data[0]!="") $wo_sam_number=" and a.wo_no like '%".trim($data[0])."'"; else { $wo_sam_number = ''; }
	
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
		
		$prev_pi_qnty_arr=return_library_array("select b.work_order_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.item_category_id=$item_category_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_id",'work_order_id','quantity');	
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
		
		$prev_pi_qnty_arr=return_library_array("select b.work_order_dtls_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.item_category_id=$item_category_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_dtls_id",'work_order_dtls_id','quantity');
	}
	
	//echo $sql;die;
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
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
	        	
		if($selectResult[csf('fabric_description')]==0)
		{
			$desc="All Fabrics";
		}
		else
		{	
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
	

	$prev_pi_qty_arr=return_library_array( "select b.work_order_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.item_category_id=$item_category_id and a.pi_basis_id=1 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id) group by b.work_order_dtls_id", "work_order_dtls_id", "qty");
	
	
	if($cbo_pi_basis_id==1) $disable_rate="disabled='disabled'";  else $disable_rate="";
	

	$descArray=array();
	$descData=sql_select( "select id, body_part_id, color_type_id, construction, composition from wo_pre_cost_fabric_cost_dtls" );
	foreach($descData as $descRow)
	{
		$descArray[$descRow[csf('id')]]['bp']=$descRow[csf('body_part_id')];
		$descArray[$descRow[csf('id')]]['ct']=$descRow[csf('color_type_id')];
		$descArray[$descRow[csf('id')]]['con']=$descRow[csf('construction')];
		$descArray[$descRow[csf('id')]]['com']=$descRow[csf('composition')];
	}
	
	
	if($order_type==1)
	{
		$data_array=sql_select("select a.id, a.booking_no, b.id as dtls_id, b.process, b.uom, b.gmts_size, b.gmts_color_id, b.fabric_color_id, b.item_size, b.wo_qnty as qnty, b.rate, b.amount, c.fabric_description from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fab_conv_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.id in($wo_dtls_id)");
		$booking_without_order=0;
	}
	else
	{
		$data_array=sql_select("select a.id, a.wo_no as booking_no, b.id as dtls_id, 35 as process, b.uom, 0 as gmts_size, 0 as gmts_color_id, 0 as fabric_color_id, null as item_size, b.wo_qty as qnty, b.rate, b.amount, b.fabric_description from wo_non_ord_aop_booking_mst a, wo_non_ord_aop_booking_dtls b where a.id=b.wo_id and b.id in($wo_dtls_id)");
		$booking_without_order=1;
	}
	
	foreach($data_array as $row)
	{
		if($row[csf('fabric_description')]==0)
		{
			$desc="All Fabrics";
		}
		else
		{
			//$descArrray=sql_select( "select body_part_id, color_type_id, construction, composition from wo_pre_cost_fabric_cost_dtls where id='$row[fabric_description]'" );
			$desc=$body_part[$descArray[$row[csf('fabric_description')]]['bp']].", ".$color_type[$descArray[$row[csf('fabric_description')]]['ct']].", ".$descArray[$row[csf('fabric_description')]]['con'].", ".$descArray[$row[csf('fabric_description')]]['com'];
		}
		
		$bl_qty=$row[csf('qnty')]-$prev_pi_qty_arr[$row[csf('dtls_id')]];
		$amount=number_format($bl_qty*$row[csf('rate')],4,'.','');
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
                </td>
                <td>
                    <? echo create_drop_down( "servicetype_".$tblRow, 110, $conversion_cost_head_array,'', 1,'-Select-',$row[csf('process')],"",1); ?> 
                </td>
                <td>
                    <input type="text" name="itemdescription_<? echo $tblRow; ?>" id="itemdescription_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $desc; ?>" style="width:150px" maxlength="200" disabled="disabled"/>
                </td>
                <td>
                    <input type="text" name="colorName_<? echo $tblRow; ?>" id="colorName_<? echo $tblRow; ?>" class="text_boxes" style="width:80px" maxlength="50" value="<? echo $color_library[$row[csf('gmts_color_id')]]; ?>" disabled="disabled"/>                        
                </td>
                <td>
                    <input type="text" name="sizeName_<? echo $tblRow; ?>" id="sizeName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('gmts_size')]]; ?>" style="width:70px;" maxlength="50" disabled="disabled"/>
                </td>
                <td>
                    <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('fabric_color_id')]]; ?>" style="width:80px" maxlength="50" disabled="disabled"/>
                </td>
                <td>

                    <input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('item_size')]; ?>" style="width:70px;" maxlength="50" disabled="disabled"/>
                </td>
                 <td>
                    <? 
                        echo create_drop_down( "uom_".$tblRow, 60, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1);            
                    ?>						 
                </td>
                <td>
                    <input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bl_qty; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" placeholder="<? echo $bl_qty; ?>" />
                </td>
                <td>
                    <input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:45px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" <? echo $disable_rate; ?> />
                </td>
                <td>
                    <input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $amount; ?>" style="width:75px;" readonly/>
                    <input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
                    <input type="hidden" name="bookingWithoutOrder_<? echo $tblRow; ?>" id="bookingWithoutOrder_<? echo $tblRow; ?>" value="<? echo $booking_without_order; ?>"/>
                </td>
            </tr>
        <?
		}
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
						 <? echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'pi_service_fabric_controller',this.value+'_'+$item_category, 'load_supplier_dropdown', 'supplier_td' );",0); ?>       
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_item_desc').value+'_'+document.getElementById('txt_item_category').value+'_'+document.getElementById('cbo_importer_id').value+'_'+document.getElementById('cbo_supplier_id').value, 'create_item_search_list_view', 'search_div', 'pi_service_fabric_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
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
						echo create_drop_down( "itemgroupid_".$tblRow, 130, "SELECT id,item_name FROM lib_item_group WHERE item_category =$item_category_id AND status_active = 1 AND is_deleted = 0 ORDER BY item_name ASC",'id,item_name', 1, '-Select-',$row[csf('item_group_id')],"get_php_form_data( this.value+'**'+'uom_$tblRow', 'get_uom', 'requires/pi_service_fabric_controller' );",1); 
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


 