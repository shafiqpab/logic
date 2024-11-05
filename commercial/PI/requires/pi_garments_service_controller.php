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
	echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type in (22) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
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
		if (is_duplicate_field( "pi_number", "com_pi_master_details", "pi_number=$pi_number and importer_id=$cbo_importer_id and supplier_id=$cbo_supplier_id  $year_cond and status_active=1 and is_deleted=0" ) == 1)
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

			
			$field_array="id,item_category_id,importer_id,supplier_id,pi_number,pi_date,last_shipment_date,pi_validity_date,currency_id,source,hs_code,internal_file_no,intendor_name,pi_basis_id,remarks,goods_rcv_status,export_pi_id,within_group,total_amount,upcharge,discount,net_total_amount,import_pi,ready_to_approved,approval_user,inserted_by,insert_date,lc_group_no,entry_form,version";
			
			$data_array="(".$id.",30,".$cbo_importer_id.",".$cbo_supplier_id.",".$pi_number.",".$pi_date.",".$last_shipment_date.",".$pi_validity_date.",".$cbo_currency_id.",".$cbo_source_id.",".$hs_code.",".$txt_internal_file_no.",".$intendor_name.",".$cbo_pi_basis_id.",".$txt_remarks.",".$cbo_goods_rcv_status.",".$export_pi_id.",".$within_group.",'".str_replace("'", '', $txt_total_amount)."','".str_replace("'", '', $txt_upcharge)."','".str_replace("'", '', $txt_discount)."','".str_replace("'", '', $txt_total_amount_net)."',".$is_import_pi.",".$cbo_ready_to_approved.",".$hiddn_user_id.",".$user_id.",'".$pc_date_time."',".$txt_lc_group_no.",197,2)";
			
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
			
			$data_array="30*".$cbo_importer_id."*".$cbo_supplier_id."*".$pi_number."*".$pi_date."*".$last_shipment_date."*".$pi_validity_date."*".$cbo_currency_id."*".$cbo_source_id."*".$hs_code."*".$txt_internal_file_no."*".$intendor_name."*".$cbo_pi_basis_id."*".$txt_remarks."*".$cbo_goods_rcv_status."*".$txt_total_amount."*".$txt_upcharge."*".$txt_discount."*".$txt_total_amount_net."*".$cbo_ready_to_approved."*".$hiddn_user_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_lc_group_no."";
			
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
						<?php echo create_drop_down( "cbo_within_group", 151, $yes_no,"", 1, "-- Select --", 0, "load_drop_down( 'pi_garments_service_controller',this.value, 'load_drop_down_importer', 'importer_td' );" ); ?>
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_importer').value, 'create_export_pi_search_list_view', 'search_div', 'pi_garments_service_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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

		$field_array="id,pi_id,work_order_no,work_order_id,work_order_dtls_id,wo_qty_dtls_id,order_id,order_source,booking_without_order,color_id,item_size,uom,quantity,rate,amount,net_pi_rate,net_pi_amount,gmts_item_id,color_range,remarks,item_category_id,entry_form,inserted_by,insert_date";


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
		
		
		$all_dtls_id="";$all_itemCategoryId = "";
		for($i=1;$i<=$total_row;$i++)
		{
			$workOrderId="hideWoId_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$hideTransData="hideTransData_".$i;
			$itemCategoryId = "itemCategoryId_".$i;
			$all_WoId.=str_replace("'","",$$workOrderId).",";
			if(str_replace("'", '',$cbo_pi_basis_id)==1 && str_replace("'", '',$cbo_goods_rcv_status)==1)
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
			$all_itemCategoryId.=str_replace("'","",$$itemCategoryId).",";
		}
		$all_dtls_id=chop($all_dtls_id,",");
		$all_WoId=implode(",",array_unique(explode(",",chop($all_WoId,","))));
		$all_itemCategoryId=implode(",",array_unique(explode(",",chop($all_itemCategoryId,","))));
		
		
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
		

		$previous_pi_qty=return_library_array( "select b.wo_qty_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.item_category_id =30 and a.pi_basis_id=1 and a.version=2 and a.goods_rcv_status=2 and b.status_active=1 and b.is_deleted=0 $booking_id_cond group by b.wo_qty_dtls_id", "wo_qty_dtls_id", "qty");
		
		$wo_qnty_res=sql_select("select c.id, c.final_wo_qty as qnty,c.rate from  piece_rate_wo_qty_dtls c where  c.status_active = 1 and  c.is_deleted = 0 and c.dtls_id in ($all_dtls_id)");
		foreach ($wo_qnty_res as $val) 
		{
			$wo_qnty_arr[$val[csf("id")]]["qnty"] = $val[csf("qnty")];
			$wo_qnty_arr[$val[csf("id")]]["rate"] = $val[csf("rate")];
		}
		
		
		
		//echo "10**".var_dump($prev_pi_qty_arr);die;

		//echo "10**$total_row jahid";die; txt_order_type
		$woDtlsTrsansId='';$trans_qty_check_arr=array();


		for($i=1;$i<=$total_row;$i++)
		{
			$workOrderNo="workOrderNo_".$i;
			$workOrderId="hideWoId_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$workOrderQtyDtlsId="hideWoQtyDtlsId_".$i;
			$txtOrderNo="txtOrderNo_".$i;
			$txtOrderSource="txtOrderSource_".$i;
			$gmtsitem="gmtsitem_".$i;
			$colorName="colorName_".$i;  
			$itemSize="itemSize_".$i;
			$rateVariable="rateVariable_".$i;
			$uom="uom_".$i;
			$quantity="quantity_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			$remarks="remarks_".$i;
			
			$hideTransData="hideTransData_".$i;
			
			$allWoId.=str_replace("'","",$$workOrderId).",";

			$perc=(str_replace("'","",$$amount)/$txt_total_amount)*100;
			$net_pi_amount=($perc*$txt_total_amount_net)/100;
			
			$net_pi_rate=$net_pi_amount/str_replace("'","",$$quantity);
			$net_pi_rate=number_format($net_pi_rate,$dec_place[3],'.','');
			
			if($cbo_currency_id==1)
				$net_pi_amount=number_format($net_pi_amount,$dec_place[4],'.','');
			else
				$net_pi_amount=number_format($net_pi_amount,$dec_place[5],'.','');
					
			
			
			
			if(str_replace("'", '',$cbo_pi_basis_id)==1)
			{
				
				$bal_wo_qnty=($wo_qnty_arr[str_replace("'","",$$workOrderQtyDtlsId)]["qnty"]-$previous_pi_qty[str_replace("'","",$$workOrderQtyDtlsId)]);
				//$wo_prev_qnty=$wo_qnty_arr[str_replace("'","",$$workOrderDtlsId)];
				//$pi_prev_qnty=$previous_pi_qty[str_replace("'","",$$workOrderDtlsId)];
			
				$trans_qnty=str_replace("'","",$$quantity);
				$trans_qnty=number_format($trans_qnty,2,'.','');

				$bal_wo_qnty=number_format($bal_wo_qnty,2,'.','');
				
				
				if($trans_qnty>$bal_wo_qnty)
				{
					echo "11** PI Quantity Not Allow Over Balance Quantity";die;
				}
				
				$wo_qnty_rate = str_replace("'","",$$rate);
				$wo_qnty_rate=number_format($wo_qnty_rate,2,'.','');
				   
				if($wo_qnty_rate > $wo_qnty_arr[str_replace("'","",$$workOrderQtyDtlsId)]["rate"])
				{
					echo "11** PI Rate Not Allow Over Work Order Rate";die;
				}
				
			}
			
			
			
			$data_array[$id]="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$workOrderQtyDtlsId)."','".str_replace("'","",$$txtOrderNo)."','".str_replace("'","",$$txtOrderSource)."',0,'".str_replace("'","",$$colorName)."','".str_replace("'","",$$itemSize)."','".str_replace("'","",$$uom)."','".str_replace("'","",$$quantity)."','".str_replace("'","",$$rate)."','".str_replace("'","",$$amount)."','".$net_pi_rate."','".$net_pi_amount."','".str_replace("'","",$$gmtsitem)."','".str_replace("'","",$$rateVariable)."','".str_replace("'","",$$remarks)."',30,197,".$user_id.",'".$pc_date_time."')"; 
			$id=$id+1;
			
			
		}
		

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
		
		$cbo_item_category_id="30";//str_replace("'", '',$cbo_item_category_id);
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
		
		$field_array_update="pi_id*work_order_no*work_order_id*work_order_dtls_id*wo_qty_dtls_id*color_id*item_size*uom*quantity*rate*amount*net_pi_rate*net_pi_amount*gmts_item_id*color_range*remarks*updated_by*update_date";

		$field_array="id,pi_id,work_order_no,work_order_id,work_order_dtls_id,wo_qty_dtls_id,order_id,order_source,booking_without_order,color_id,item_size,uom,quantity,rate,amount,net_pi_rate,net_pi_amount,gmts_item_id,color_range,remarks,item_category_id,entry_form,inserted_by,insert_date";
		
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
		
		$all_dtls_id="";$all_itemCategoryId ="";
		for($i=1;$i<=$total_row;$i++)
		{
			$workOrderId="hideWoId_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$hideTransData="hideTransData_".$i;
			$itemCategoryId="itemCategoryId_".$i;
			$all_WoId.=str_replace("'","",$$workOrderId).",";
			if(str_replace("'", '',$cbo_pi_basis_id)==1 && str_replace("'", '',$cbo_goods_rcv_status)==1)
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
			$all_itemCategoryId.=str_replace("'","",$$itemCategoryId).",";
		}
		$all_dtls_id=chop($all_dtls_id,",");
		$all_WoId=implode(",",array_unique(explode(",",chop($all_WoId,","))));

		$all_itemCategoryId=implode(",",array_unique(explode(",",chop($all_itemCategoryId,","))));
		
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
			$previous_pi_qty=return_library_array("select b.wo_qty_dtls_id as id, sum(b.quantity) as qnty 
			from com_pi_master_details a, com_pi_item_details b 
			where a.id=b.pi_id and b.item_category_id =30 and a.goods_rcv_status = 2 and b.status_active=1 $booking_id_cond and a.id not in($update_id) group by b.wo_qty_dtls_id","id","qnty");
			

			$wo_qnty_res=sql_select("select c.id, c.final_wo_qty as qnty,c.rate from  piece_rate_wo_qty_dtls c where  c.status_active = 1 and  c.is_deleted = 0 and c.dtls_id in ($all_dtls_id)");


			foreach ($wo_qnty_res as $val) 
			{
				$wo_qnty_arr[$val[csf("id")]]["qnty"] = $val[csf("qnty")];
				$wo_qnty_arr[$val[csf("id")]]["rate"] = $val[csf("rate")];
			}
			
		}
		
		$id = return_next_id( "id","com_pi_item_details", 1 );
		$data_array==""; $data_array_update=array();
		$woDtlsTrsansId='';$trans_qty_check_arr=array();
		for($i=1;$i<=$total_row;$i++)
		{
			$updateIdDtls="updateIdDtls_".$i;
			
			$amount="amount_".$i;
			$workOrderNo="workOrderNo_".$i;
			$workOrderId="hideWoId_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$workOrderQtyDtlsId="hideWoQtyDtlsId_".$i;
			
			$txtOrderNo="txtOrderNo_".$i;
			$txtOrderSource="txtOrderSource_".$i;
			$gmtsitem="gmtsitem_".$i;
			$colorName="colorName_".$i; 
			$itemSize="itemSize_".$i;
			$rateVariable="rateVariable_".$i;
			$uom="uom_".$i;
			$quantity="quantity_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			$remarks="remarks_".$i;


			
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
					
			
			
			
			if(str_replace("'", '',$cbo_goods_rcv_status)==2 && str_replace("'", '',$cbo_pi_basis_id)==1)
			{
				
				$bal_wo_qnty=($wo_qnty_arr[str_replace("'","",$$workOrderQtyDtlsId)]["qnty"]-$previous_pi_qty[str_replace("'","",$$workOrderQtyDtlsId)]);
					
				$trans_qnty=str_replace("'","",$$quantity);
				$bal_wo_qnty=number_format($bal_wo_qnty,2,'.','');
				$trans_qnty=number_format($trans_qnty,2,'.','');
				//echo "11** $trans_qnty=$bal_wo_qnty=$wo_prev_qnty=$pi_prev_qnty";die;

				if($trans_qnty>$bal_wo_qnty)
				{
					echo "11**PI Quantity Not Allow Over Balance Quantity";die;
				}	


				$wo_qnty_rate = str_replace("'","",$$rate);
				$wo_qnty_rate=number_format($wo_qnty_rate,2,'.','');
				   
				if($wo_qnty_rate > $wo_qnty_arr[str_replace("'","",$$workOrderQtyDtlsId)]["rate"])
				{
					echo "11** PI Rate Not Allow Over Work Order Rate";die;
				}

			}
			
			
			
			if(str_replace("'","",$$updateIdDtls)!="")
			{
				$all_updateDtls_id.=str_replace("'",'',$$updateIdDtls).",";
				$id_arr[]=str_replace("'",'',$$updateIdDtls);
				$data_array_update[str_replace("'",'',$$updateIdDtls)] = explode("*",($update_id."*'".str_replace("'","",$$workOrderNo)."'*'".str_replace("'","",$$workOrderId)."'*'".str_replace("'","",$$workOrderDtlsId)."'*'".str_replace("'","",$$workOrderQtyDtlsId)."'*'".str_replace("'","",$$colorName)."'*'".str_replace("'","",$$itemSize)."'*'".str_replace("'","",$$uom)."'*'".str_replace("'","",$$quantity)."'*'".str_replace("'","",$$rate)."'*'".str_replace("'","",$$amount)."'*'".$net_pi_rate."'*'".$net_pi_amount."'*'".str_replace("'","",$$gmtsitem)."'*'".str_replace("'","",$$rateVariable)."'*'".str_replace("'","",$$remarks)."'*".$user_id."*'".$pc_date_time."'"));

			}
			else
			{
				if($data_array!="") $data_array.=","; 		
						

				$data_array.="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$workOrderQtyDtlsId)."','".str_replace("'","",$$txtOrderNo)."','".str_replace("'","",$$txtOrderSource)."',0,'".str_replace("'","",$$colorName)."','".str_replace("'","",$$itemSize)."','".str_replace("'","",$$uom)."','".str_replace("'","",$$quantity)."','".str_replace("'","",$$rate)."','".str_replace("'","",$$amount)."','".$net_pi_rate."','".$net_pi_amount."','".str_replace("'","",$$gmtsitem)."','".str_replace("'","",$$rateVariable)."','".str_replace("'","",$$remarks)."',30,197,".$user_id.",'".$pc_date_time."')"; 



				$id=$id+1;
			}
		}
		
		//echo "10**".print_r($data_array_update)."**1";die;
		
		$allWoId=chop($allWoId,",");
		$all_updateDtls_id=chop($all_updateDtls_id,",");
		
		//echo "6**=".$rID."=".$rID2.'='.$rID3."=".$rID5."=".$rID4."=".$flag."**1";die;
	 	$flag=1;
		$rID=$rID2=$rID3=$rID4=true;
		if(str_replace("'", '',$cbo_goods_rcv_status)==2)
		{
			if(count($data_array_update)>0)
			{
				$rID=execute_query(bulk_update_sql_statement( "com_pi_item_details", "id", $field_array_update, $data_array_update, $id_arr ));
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
		}

		$rID4=sql_update("com_pi_master_details",$field_array_update2,$data_array_update2,"id",$update_id,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
		//echo "10**==$operation==jahid**1";die;
		//echo "6**=$rID=$rID2=$rID3=$rID5=$rID4=$flag**1";die;
		//echo "6**=".$rID."=".$rID2.'='.$rID3."=".$rID5."=".$rID4."=".$flag."**1";die;
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
	
	?>
    <table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
		<?
       
			$item_group_arr=return_library_array( "SELECT id,item_name FROM lib_item_group",'id','item_name');
			$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");
			$po_number_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
			$subcon_po_number_arr = return_library_array("select id, order_no from subcon_ord_dtls  where status_active=1 and is_deleted=0","id","order_no");
		 ?>
        	<thead>
                <th>&nbsp;</th>
                <th class="must_entry_caption">WO No</th>
                <th>Order No</th>
                <th class="must_entry_caption">Garments Item</th>
                <th class="must_entry_caption">Gmts Color</th>
                <th>Gmts Size</th>
                <th>Rate Variable</th>
                <th>UOM</th>
                <th class="must_entry_caption">Gmts Qty</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
                <th>Remarks</th>
            </thead>
            <tbody>
       	<?

			$disable="disabled='disabled'";
			$placeholder="";
			if($goods_rcv_status==1) $disable_field="disabled='disabled'"; else $disable_field="";
            
			
			if($goods_rcv_status==2)
            {
				$nameArray=sql_select( "select id, work_order_no, work_order_id, work_order_dtls_id,wo_qty_dtls_id,color_range,color_id,gmts_item_id, item_size, uom, quantity, rate, amount,remarks,order_id,order_source from com_pi_item_details where pi_id='$pi_id' and status_active=1 and is_deleted=0" );
            }
          /*  else
            {
				
				if($db_type==0)
				{
					
					$nameArray=sql_select( "select group_concat(id) as id, work_order_no, work_order_id, group_concat(work_order_dtls_id) as work_order_dtls_id, item_prod_id, item_group,item_category_id, item_description, item_size, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount 
					from com_pi_item_details 
					where pi_id='$pi_id' and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, item_prod_id, item_group,item_category_id, item_description, item_size, uom" );
					
				
				}
				else
				{
					
					$nameArray=sql_select( "select listagg(cast(id as varchar(4000)), ',') within group(order by id) as id, work_order_no, work_order_id, listagg(cast(work_order_dtls_id as varchar(4000)), ',') within group(order by work_order_dtls_id) as work_order_dtls_id, item_prod_id, item_group,item_category_id, item_description, item_size, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount  
					from com_pi_item_details 
					where pi_id='$pi_id' and status_active=1 and is_deleted=0
					group by work_order_no, work_order_id, item_prod_id, item_group,item_category_id, item_description, item_size, uom" );
					
				}
            }*/
			

			if($type==1 || count($nameArray)<1)
            {
				$tot_amnt=''; $upcharge=''; $discount=''; $tot_amnt_net=''; $txt_tot_row=0;
            	?>
                <tr class="general" id="row_1">
                    <td>
                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                        <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                        <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                        <input type="hidden" name="hideWoQtyDtlsId_" id="hideWoQtyDtlsId_" readonly />
                    </td>
                    <td>
                        <input type="text" name="txtOrderNoShow_1" id="txtOrderNoShow_1" class="text_boxes" value="" style="width:90px;"  disabled="disabled"/>

                        <input type="hidden" name="txtOrderNo_1" id="txtOrderNo_1" class="text_boxes" value="" style="width:90px;"  disabled="disabled"/>
                        <input type="hidden" name="txtOrderSource_1" id="txtOrderSource_1" class="text_boxes" value="" style="width:90px;"  disabled="disabled"/>
                    </td>
                    
                    <td> 
                    	<? echo create_drop_down( "gmtsitem_1", 150, $garments_item,'', 1, '-Select-', 0,"",$disable_status); ?> 
                    </td>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" onFocus="add_auto_complete( 1 )" style="width:90px;"  <? echo $disable; ?>/>
                    </td>
                    <td>
                        <input type="text" name="itemSize_1" id="itemSize_1" class="text_boxes" value="" style="width:90px;" maxlength="50" disabled="disabled"/>
                    </td>

                    <td>
						<? 
                        echo create_drop_down( "rateVariable_1", 90, $color_type,"",1, "--Select--", "","",1,"" ); 
						?>                                    
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
                    <td>
						<input type="text" name="remarks_1" id="remarks_1" class="text_boxes" value="" style="width:130px;" maxlength="250" />
                    </td>
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
					$prev_pi_qty_arr=return_library_array( "select b.wo_qty_dtls_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=$item_category_id and a.pi_basis_id=1 and b.status_active=1 and b.is_deleted=0 and b.work_order_dtls_id in($wo_dtls_id) group by b.wo_qty_dtls_id", "wo_qty_dtls_id", "qty");
					
					//$wo_qty_arr=return_library_array("select id, supplier_order_quantity as qty from wo_non_order_info_dtls where id in($wo_dtls_id)", "id", "qty");

					$wo_qty_arr=return_library_array("select c.id, c.final_wo_qty,c.rate from  piece_rate_wo_qty_dtls c where  c.status_active = 1 and  c.is_deleted = 0 and c.dtls_id in ($wo_dtls_id) order by c.id", "id", "final_wo_qty");
				
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
                            <td title="<? echo $wo_qty_arr[$row[csf('wo_qty_dtls_id')]]."==".$prev_pi_qty_arr[$row[csf('wo_qty_dtls_id')]]."==".$row[csf('quantity')]?>">
                                <input type="text" name="workOrderNo_<? echo $i; ?>" id="workOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $i; ?>);" <? echo $disable_field; ?> readonly />			
                                <input type="hidden" name="hideWoId_<? echo $i; ?>" id="hideWoId_<? echo $i; ?>" readonly value="<? echo $row[csf('work_order_id')]; ?>" />
                                <input type="hidden" name="hideWoDtlsId_<? echo $i;?>" id="hideWoDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('work_order_dtls_id')];?>"/>
                                <input type="hidden" name="hideWoQtyDtlsId_<? echo $i;?>" id="hideWoQtyDtlsId_<? echo $i;?>" readonly value="<? echo $row[csf('wo_qty_dtls_id')];?>"/>
                                <input type="hidden" name="hideTransData_<? echo $i; ?>" id="hideTransData_<? echo $i; ?>" value="" readonly />
                            </td>
                        <? 
							$bl_qty=$wo_qty_arr[$row[csf('wo_qty_dtls_id')]]-$prev_pi_qty_arr[$row[csf('wo_qty_dtls_id')]]+$row[csf('quantity')];
                        } 
                        ?>
                        <td>
                        	<? if($row[csf('order_source')] == 1) $po_number_arr=$po_number_arr; else $po_number_arr=$subcon_po_number_arr; ?>
                        	<input type="text" name="txtOrderNoShow_<? echo $i; ?>" id="txtOrderNoShow_<? echo $i; ?>" class="text_boxes" value="<? echo $po_number_arr[$row[csf('order_id')]]?>" style="width:90px;"  disabled="disabled"/>
                        	<input type="hidden" name="txtOrderNo_<? echo $i; ?>" id="txtOrderNo_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('order_id')];?>" style="width:90px;" disabled="disabled"/>
                        	<input type="hidden" name="txtOrderSource_<? echo $i; ?>" id="txtOrderSource_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('order_source')];?>" style="width:90px;"  disabled="disabled"/>
                    	</td>
                    	
                        <td> 
                    		<? echo create_drop_down( "gmtsitem_$i", 150, $garments_item,'', 1, '-Select-', $row[csf("gmts_item_id")],"","1"); ?> 
                    	</td>
                    	<td>
                        	<input type="text" name="colorNameShow_<? echo $i; ?>" id="colorNameShow_<? echo $i; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]];?>" onFocus="add_auto_complete( 1 )" style="width:90px;"  disabled />
                        	<input type="hidden" name="colorName_<? echo $i; ?>" id="colorName_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('color_id')];?>" style="width:90px;"  />
                    	</td>
                    	<td>
                        	<input type="text" name="itemSizeShow_<? echo $i; ?>" id="itemSizeShow_<? echo $i; ?>" class="text_boxes" value="<? echo $size_arr[$row[csf('item_size')]]; ?>" style="width:90px;" maxlength="50"  disabled="disabled"/>
                        	<input type="hidden" name="itemSize_<? echo $i; ?>" id="itemSize_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('item_size')]; ?>" style="width:90px;" maxlength="50"  disabled="disabled"/>
                    	</td>

                        <td>
							<? 
	                        	echo create_drop_down( "rateVariable_".$i, 90, $color_type,"",1, "--Select--", $row[csf('color_range')],"",1,"" ); 
							?>                                    
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
                            <input type="text" name="rate_<? echo $i; ?>" id="rate_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:75px;" onKeyUp="calculate_amount(<? echo $i; ?>)" />
                        </td>
                        <td>
                            <input type="text" name="amount_<? echo $i; ?>" id="amount_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:85px;" <? echo $disable; ?> readonly/>
                            <input type="hidden" name="updateIdDtls_<? echo $i; ?>" id="updateIdDtls_<? echo $i; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
                        </td>
                        <td>
						<input type="text" name="remarks_<? echo $i; ?>" id="remarks_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('remarks')]?>" style="width:130px;" maxlength="250" />
                    </td>
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
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<? echo $tot_amnt; ?>" style="width:85px;" readonly/>
                    </td>
                   <td>&nbsp;</td>
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
                    <td>&nbsp;</td>
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<? echo $upcharge; ?>" style="width:85px;" onKeyUp="calculate_total_amount(2)"/>
                    </td>
                    <td>&nbsp;</td>
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
                    <td>&nbsp;</td>
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<? echo $discount; ?>" style="width:85px;" onKeyUp="calculate_total_amount(2)"/>
                    </td>
                    <td>&nbsp;</td>
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
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<? echo $tot_amnt_net; ?>" style="width:85px;" readonly/>
                    </td>
                    <td>&nbsp;</td>
                </tr>
       	 	</tfoot>
        <?     
		
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
<div align="center" style="width:1000px;">
	<form name="searchpifrm"  id="searchpifrm">
		<fieldset style="width:100%;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="970" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Supplier</th>
                    <th>Item Category</th>
                    <th>PI Number</th>
                    <th>System ID</th>
                    <th>Date Range</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        
                    	<input type="hidden" name="txt_selected_pi_id" id="txt_selected_pi_id" class="text_boxes" style="width:70px" value="">   
                    </th> 
                </thead>
                <tr class="general">
                    <td class="must_entry_caption">
						 <? echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--Select--',$importer_id,"load_drop_down( 'pi_garments_service_controller',this.value, 'load_supplier_dropdown', 'supplier_td' );",0); ?>       
                    </td>
                    <td id="supplier_td">	
                        <?
							//echo create_drop_down( "cbo_supplier_id", 151, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
							echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$importer_id' and b.party_type in (3,6,7,8,92) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
						?> 
                    </td>
                    <td>
                    	<? echo create_drop_down( "cbo_item_category_id", 151, $item_category,'', 1, '--Select--',0,"",0,'','','','74,72,79,73,71,77,78,75,76,1,2,3,4,12,13,14,24,25,31'); ?>
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_item_category_id').value+'_'+document.getElementById('cbo_importer_id').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_system_id').value, 'create_pi_search_list_view', 'search_div', 'pi_garments_service_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
	$item_cond = ($data[3] == 0) ? "" : " and b.item_category_id = ".$data[3];
	$importer_id =$data[4];
	if($data[5]==0) $supplier_id="%%"; else $supplier_id=$data[5];
	
	if (trim($data[6])=="") $system_id_cond=''; else $system_id_cond=" and a.id=".trim($data[6]);
	  
	if($importer_id==0) { echo "Please Select Company First."; die; }
	
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$arr=array(2=>$comp,3=>$supplier,6=>$pi_basis);
	 
	//echo $sql= "select a.id,a.pi_number,a.pi_date,a.importer_id,a.supplier_id,a.last_shipment_date,a.hs_code,a.pi_basis_id from com_pi_master_details a, com_pi_item_details b where a.id = b.pi_id and  a.supplier_id like '$supplier_id' and importer_id=$importer_id $item_cond and b.item_category_id not in (74,72,79,73,71,77,78,75,76,1,2,3,4,12,13,14,24,25,31) and a.entry_form=172 and b.status_active=1 and b.is_deleted=0 and a.status_active = 1 and a.is_deleted = 0 $pi_number $pi_date $system_id_cond order by a.pi_number";

	 $sql= "select distinct(a.id),a.pi_number,a.pi_date,a.importer_id,a.supplier_id,a.last_shipment_date,a.hs_code,a.pi_basis_id 
		from com_pi_master_details a left join  com_pi_item_details b on a.id = b.pi_id $item_cond and b.item_category_id not in (74,72,79,73,71,77,78,75,76,1,2,3,4,12,13,14,24,25,31) and b.status_active=1 and b.is_deleted=0
		where a.supplier_id like '$supplier_id' and a.importer_id=$importer_id $pi_number $pi_date $system_id_cond and a.entry_form=197 and a.version = 2 and a.status_active = 1 and a.is_deleted = 0
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
		
		echo "load_drop_down('requires/pi_garments_service_controller',".$row[csf("importer_id")].", 'load_supplier_dropdown', 'supplier_td' );\n";
		
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
	$previous_wo_ids=$prev_wo_nums=$prev_deters=$prev_colors=$prev_constructs=$prev_compositions=$prev_gsms=$prev_widths=$prev_uoms="";

?> 
	<script>
	
		var item_category_id='';
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
			$('#txt_selected_wo_id').val( id );
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
<div align="center" style="width:1000px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:100%;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="980" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Company</th>
                    <th>Supplier</th>
                    <th>WO Number</th>
                    <th>WO Date Range</th>
                    <th>Based on</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" onClick="reset_hide_field()" />
                        <input type="hidden" name="txt_goods_rcv_status" id="txt_goods_rcv_status" class="text_boxes" style="width:70px" value="<? echo $goods_rcv_status; ?>">
                    	<input type="hidden" name="txt_selected_wo_id" id="txt_selected_wo_id" class="text_boxes" value=""> 
                        <input type="hidden" name="order_non_order_type" id="order_non_order_type" class="text_boxes" value="">  
                    </th> 
                </thead>
                <tr class="general">
                    <td>
					<?
						if($importer_id>0) $dis_ana=1; else $dis_ana=0;
                    	echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',$importer_id,"load_drop_down( 'pi_garments_service_controller',this.value+'_'+$item_category_id, 'load_supplier_dropdown', 'supplier_td' );",$dis_ana); 
                    ?>       
                    </td>
                    <td id="supplier_td">	
					<?
					if($importer_id>0 && $supplier_id>0)
					{
						echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$importer_id and c.id=$supplier_id and b.party_type in (22) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier_id,'',1);
					}
					else if($importer_id>0 && $supplier_id<1)
					{

						echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$importer_id and b.party_type in (22) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_wo_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'30'+'_'+document.getElementById('cbo_importer_id').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_based_on').value+'_'+document.getElementById('txt_goods_rcv_status').value+'_'+'<? echo $prev_wo_ids; ?>'+'_'+'<? echo $previous_wo_ids; ?>', 'create_wo_search_list_view', 'search_div', 'pi_garments_service_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
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

	$prev_wo_ids_cond=""; $prev_wo_ids_cond2=""; $prev_wo_ids_cond3=""; $prev_wo_ids_cond4="";
	if($prev_wo_ids!="")
	{
		$prev_wo_ids_cond=" and b.id not in($prev_wo_ids)";
		/*$prev_wo_ids_cond2=" and c.id not in($prev_wo_ids)";
		$prev_wo_ids_cond3=" and d.id not in($prev_wo_ids)";
		$prev_wo_ids_cond4=" and e.id not in($prev_wo_ids)";*/
	}
	
	if($company_id==0) { echo "Please Select Company First."; die; }
	if($data[5]==0) $supplier_id="%%"; else $supplier_id =$data[5];
	

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
	

	if ($data[0]!="") $wo_number=" and a.sys_number like '%".trim($data[0])."'"; else { $wo_number = ''; }
	
	$item_cond = ($item_category_id == 0)? "":" and b.item_category_id = $item_category_id" ;

	if($selected_based_on==1)
	{

		
		$sql="select a.sys_number, a.id,a.wo_date,a.company_id, a.service_provider_id
				from piece_rate_wo_mst a, piece_rate_wo_dtls b
				where a.id = b.mst_id and b.order_source in (1,2) 
				and a.status_active = 1 and b.status_active = 1 and a.company_id=$company_id and a.service_provider_id like '$supplier_id' $wo_number $wo_date_cond $prev_wo_ids_cond
				group by a.id, a.sys_number, a.wo_date,a.company_id, a.service_provider_id";
		
		
		$prev_pi_qnty_arr=return_library_array("select b.work_order_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.item_category_id not in (74,72,79,73,71,77,78,75,76,1,2,3,12,13,14,24,25,31) and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_id",'work_order_id','quantity');
	}
	else
	{
		
			
			$sql="select a.sys_number, a.id as mst_id,a.wo_date,a.company_id, a.service_provider_id, b.order_source,b.item_id,b.style_ref, b.uom, b.id
				from piece_rate_wo_mst a, piece_rate_wo_dtls b
				where a.id = b.mst_id and b.order_source in (1,2) 
				and a.status_active = 1 and b.status_active = 1 and a.company_id=$company_id and a.service_provider_id = '$supplier_id' $wo_number $wo_date_cond $prev_wo_ids_cond "; 
			
			$prev_pi_qnty_arr=return_library_array("select b.work_order_dtls_id, sum(b.quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id $item_cond and b.item_category_id not in (74,72,79,73,71,77,78,75,76,1,2,3,12,13,14,24,25,31) and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_dtls_id",'work_order_dtls_id','quantity');
	}
	
	
	//echo $sql;
	$data_array=sql_select($sql);
	
	
	$result = sql_select($sql);
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$item_group_arr=return_library_array( "select id,item_name FROM lib_item_group",'id','item_name');
	//$supplier_arr=sql_select("SELECT a.id,a.supplier_name,a.party_type FROM lib_supplier a,lib_supplier_party_type b,lib_supplier_tag_company c WHERE a.id=b.supplier_id and a.id=c.supplier_id and b.party_type  in(22,36) and c.tag_company =$cbo_company_id");
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table">
        <thead>
            <th width="40">SL No</th>
            <th width="110">WO Number</th>
            <th width="80">WO Date</th>
            <th width="140">Supplier</th>
            <th width="140">Order Source</th>
            <th width="100">Item Name</th>
            <th width="120">Style</th>
            <th>uom</th>
        </thead>
     </table>
     <div style="width:880px; max-height:250px; overflow-y:scroll">
         <table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table" id="tbl_list_search">
         <? 
         $i=1;
		 
     
		 
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
			/*if($bal_qnty>0)
			{*/
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$row[csf('id')]; ?>')"> 				
                    <td width="40" align="center"><? echo "$i"; ?></td>	
                    <td width="110"><p><? echo $row[csf('sys_number')];?></p></td>
                    <td width="80"><p><? echo change_date_format($row[csf('wo_date')]);?></p></td>
                    <td width="140"><p><? echo $supplier_arr[$row[csf('service_provider_id')]]; ?></p></td> 
                    <td width="140" align="center"><p><? echo $order_source[$row[csf('order_source')]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $garments_item[$row[csf('item_id')]]; ?>&nbsp;</p></td>
                    <td width="120" align="center"><p><? echo $row[csf('style_ref')]; ?></p></td>
                    <td align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
				</tr>
				<?
				$i++;
			/*}*/
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
	$cbo_based_on=$data[7];
	
	$prev_pi_amnt_arr=array(); $prev_pi_qty_arr=array();
	
	if($cbo_based_on == 1) $wo_id_cond = " and a.id in ($wo_dtls_id)"; else $wo_id_cond = " and b.id in ($wo_dtls_id)";
	if($cbo_based_on == 1) $wo_id_cond_2 = " and b.work_order_id in ($wo_dtls_id)"; else $wo_id_cond_2 = " and b.work_order_dtls_id in ($wo_dtls_id)";

	$prev_pi_qty_arr=return_library_array( "select b.wo_qty_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.item_category_id =30 and a.pi_basis_id=1 and a.version=2 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 $wo_id_cond_2 group by b.wo_qty_dtls_id", "wo_qty_dtls_id", "qty");
	//echo "select b.wo_qty_dtls_id,sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.item_category_id =30 and a.pi_basis_id=1 and a.version=2 and a.goods_rcv_status=$goods_rcv_status and b.status_active=1 and b.is_deleted=0 $wo_id_cond_2 group by b.wo_qty_dtls_id";die;
	if($cbo_pi_basis_id==1) $disable_rate="disabled='disabled'";  else $disable_rate="";

	
	$item_group_arr=return_library_array( "SELECT id,item_name FROM lib_item_group",'id','item_name');
	$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");
	$po_number_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$subcon_po_number_arr = return_library_array("select id, order_no from subcon_ord_dtls  where status_active=1 and is_deleted=0","id","order_no");
	
	if($goods_rcv_status==2)
	{
		$sql_data= "select a.sys_number, a.id as mst_id,a.wo_date,a.company_id, a.service_provider_id, b.order_source,b.order_id,b.item_id,b.style_ref, b.uom,b.color_type as rate_variable, b.id as dtls_id, c.id as qty_dtls_id, c.color_id, c.size_id, c.final_wo_qty,c.rate
		from piece_rate_wo_mst a, piece_rate_wo_dtls b, piece_rate_wo_qty_dtls c
		where a.id = b.mst_id and b.id = c.dtls_id and b.order_source in (1,2) $wo_id_cond and a.status_active = 1 and b.status_active = 1 and c.status_active = 1
		order by c.id ";
		
		$data_array=sql_select($sql_data);


		foreach($data_array as $row)
		{
			$bl_qty=$row[csf('final_wo_qty')]-$prev_pi_qty_arr[$row[csf('qty_dtls_id')]];
			
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
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('sys_number')]; ?>" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('mst_id')]; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('dtls_id')]; ?>" readonly />
						<input type="hidden" name="hideWoQtyDtlsId_<? echo $tblRow; ?>" id="hideWoQtyDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('qty_dtls_id')]; ?>" readonly />
                        <input type="hidden" name="hideTransData_<? echo $tblRow; ?>" id="hideTransData_<? echo $tblRow; ?>" value="" readonly />
					</td>
					<td>
						<? if($row[csf('order_source')] == 1) $po_number_arr=$po_number_arr; else $po_number_arr=$subcon_po_number_arr; ?>
                        <input type="text" name="txtOrderNoShow_<? echo $tblRow; ?>" id="txtOrderNoShow_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $po_number_arr[$row[csf('order_id')]]?>" style="width:90px;" disabled="disabled"/>
                        <input type="hidden" name="txtOrderNo_<? echo $tblRow; ?>" id="txtOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('order_id')];?>" style="width:90px;"  disabled="disabled"/>
                        <input type="hidden" name="txtOrderSource_<? echo $tblRow; ?>" id="txtOrderSource_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('order_source')];?>" style="width:90px;"  disabled="disabled"/>
                    </td>
					<td> 
                    	<? echo create_drop_down( "gmtsitem_$tblRow", 150, $garments_item,'', 1, '-Select-', $row[csf('item_id')],"","1"); ?> 
                    </td>
                    <td>
                        <input type="text" name="colorNameShow_<? echo $tblRow; ?>" id="colorNameShow_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]?>" style="width:90px;" maxlength="50" disabled/>

                        <input type="hidden" name="colorName_<? echo $tblRow; ?>" id="colorName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('color_id')]?>" onFocus="add_auto_complete( 1 )" style="width:90px;" maxlength="50" disabled/>
                    </td>
                    <td>
                        <input type="text" name="itemSizeShow_<? echo $tblRow; ?>" id="itemSizeShow_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $size_arr[$row[csf('size_id')]]?>" style="width:90px;" maxlength="50" disabled="disabled"/>
                        <input type="hidden" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('size_id')]?>" style="width:90px;" maxlength="50" disabled="disabled"/>
                    </td>
                    <td>
						<? 
                        echo create_drop_down( "rateVariable_$tblRow", 90, $color_type,"",1, "--Select--", $row[csf('rate_variable')],"",1,"" ); 
						?>                                    
                    </td>
                    <td>
                        <? 
                            echo create_drop_down( "uom_$tblRow", 80, $unit_of_measurement,'', 0, '',$row[csf('uom')],'',1);            
                        ?>		
                    </td>
                    <td>
                        <input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bl_qty; ?>" style="width:90px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" placeholder="<? echo $bl_qty;?>"/>
                    </td>
                    <td>
                        <input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:75px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" <? echo $disable; ?> />
                    </td>
                    <td>
                        <input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bl_qty*$row[csf('rate')];?>" style="width:85px;" <? echo $disable; ?> readonly/>
                        <input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly/>
                    </td>
                    <td>
						<input type="text" name="remarks_<? echo $tblRow; ?>" id="remarks_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:130px;" maxlength="250" />
                    </td>
				</tr>

				<?
			}
		}
		
	}
	
/*	else
	{
		$conver_factor=return_library_array("select a.id, b.conversion_factor from product_details_master a,  lib_item_group b where a.item_group_id=b.id","id","conversion_factor");
		
		$rcv_rtn_qnty=return_library_array("select recv_trans_id, sum(issue_qnty) as qnty from  inv_mrr_wise_issue_details where entry_form=26 and status_active=1 group by recv_trans_id","recv_trans_id","qnty");
		
		$sql_data="select a.id, a.wo_number, a.wo_date, a.supplier_id, c.id as dtls_id, c.order_uom as uom, c.order_qnty as qnty, c.order_amount as amount, d.id as prod_id, d.item_category_id, d.item_group_id, d.item_description, d.item_size
		from wo_non_order_info_mst a, inv_receive_master b, inv_transaction c, product_details_master d 
		where a.id=b.booking_id and b.id=c.mst_id and c.prod_id=d.id and b.entry_form=20 and b.receive_basis=2 and a.is_deleted=0 and b.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.id in($wo_dtls_id)";
		$data_array=sql_select($sql_data);
		
		$dtls_data=array();
		foreach($data_array as $row)
		{
			$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["id"]=$row[csf("id")];
			$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["wo_number"]=$row[csf("wo_number")];
			$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["supplier_id"]=$row[csf("supplier_id")];
			$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["item_group_id"]=$row[csf("item_group_id")];
			$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["item_category_id"]=$row[csf("item_category_id")];
			$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["item_description"]=$row[csf("item_description")];
			$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["item_size"]=$row[csf("item_size")];
			$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["uom"]=$row[csf("uom")];
			$dtls_data[$row[csf("id")]][$row[csf("prod_id")]]["dtls_id"].=$row[csf("dtls_id")]."_";
			
			$trans_qnty_data[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("dtls_id")]]["trans_qnty"]=$row[csf("qnty")];
			$trans_qnty_data[$row[csf("id")]][$row[csf("prod_id")]][$row[csf("dtls_id")]]["order_amount"]=$row[csf("amount")];
		}
	
		foreach($dtls_data as $book_id=>$book_data)
		{
			foreach($book_data as $prod_id=>$row)
			{
				
				$rtn_qnty=$rate=$prev_pi_qnty=$book_item_qnty=$book_item_amt=$bl_trans_qnty=$item_amt=$item_rate=0;$trans_data="";
				$trans_id_arr=explode("_",chop($row[("dtls_id")],'_'));
				$all_dtls_id="";
				foreach($trans_id_arr as $trans_id)
				{
					$bl_trans_qnty=number_format(($trans_qnty_data[$book_id][$prod_id][$trans_id]["trans_qnty"]-(($rcv_rtn_qnty[$trans_id]/$conver_factor[$prod_id])+$prev_pi_qty_arr[$trans_id])),2,'.','');
					$item_rate=$trans_qnty_data[$book_id][$prod_id][$trans_id]["order_amount"]/$trans_qnty_data[$book_id][$prod_id][$trans_id]["trans_qnty"];
					$item_amt=number_format($bl_trans_qnty*$item_rate,2,'.','');
					
					$trans_data.=$trans_id."_".$bl_trans_qnty."_".$item_amt."_".$prod_id."__";
					$rtn_qnty+=$rcv_rtn_qnty[$trans_id]/$conver_factor[$prod_id];
					$prev_pi_qnty+=$prev_pi_qty_arr[$trans_id];
					$book_item_qnty+=$trans_qnty_data[$book_id][$prod_id][$trans_id]["trans_qnty"];
					$book_item_amt+=$trans_qnty_data[$book_id][$prod_id][$trans_id]["order_amount"];
					$all_dtls_id.=$trans_id.",";
					
				}
				$all_dtls_id=chop($all_dtls_id,",");
				$bl_qty=($book_item_qnty-($rtn_qnty+$prev_pi_qnty));
				$rate=$book_item_amt/$book_item_qnty;
				$amount=$rate*$bl_qty;
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
							<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[('wo_number')]; ?>" style="width:100px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" title="<? echo $rtn_qnty; ?>" readonly />			
							<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[('id')]; ?>" readonly />
							<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $all_dtls_id; ?>" readonly />
                            <input type="hidden" name="hideTransData_<? echo $tblRow; ?>" id="hideTransData_<? echo $tblRow; ?>" value="<? echo chop($trans_data,'__'); ?>" readonly />
						</td>
						<td> 
						<?
							echo create_drop_down( "itemgroupid_".$tblRow, 130, $item_group_arr,'', 1, '- Select -',$row[('item_group_id')],"get_php_form_data( this.value+'**'+'uom_$tblRow', 'get_uom', 'requires/pi_garments_service_controller' );",1); 
						?>  
						</td>
						<td>
						<? echo create_drop_down( "itemCategoryId_".$tblRow, 151, $item_category,'', 1, '--Select--',$row[csf('item_category_id')],"",1,'','','','74,72,79,73,71,77,78,75,76,1,2,3,4,12,13,14,24,25,31'); ?>
						</td>
						<td>
							<input type="text" name="itemdescription_<? echo $tblRow; ?>" id="itemdescription_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[('item_description')]; ?>" style="width:200px" maxlength="200" <? echo $disable; ?> onDblClick="openmypage_item_desc(<? echo $tblRow; ?>);" placeholder="Double Click" readonly/>
							<input type="hidden" name="itemProdId_<? echo $tblRow; ?>" id="itemProdId_<? echo $tblRow; ?>" readonly value="<? echo $prod_id; ?>"/> 
						</td>
						<td>
							<input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[('item_size')]; ?>" style="width:90px;" maxlength="50" disabled="disabled"/>
						</td>
						<td>
							<? 
								echo create_drop_down( "uom_$tblRow", 80, $unit_of_measurement,'', 0, '',$row[('uom')],'',1); 
							?>		
						</td>
						<td>
							<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo number_format($bl_qty,2,'.',''); ?>" style="width:90px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" placeholder="<? echo $bl_qty; ?>" readonly />
						</td>
						<td>
							<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo number_format($rate,2,'.',''); ?>" style="width:75px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" readonly />
						</td>
						<td>
							<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo number_format($amount,2,'.',''); ?>" style="width:85px;" readonly/>
							<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly/>
						</td>
					</tr>
					<?
				}
				
			}
			
		}
		
	}*/
	

	exit();
}

if ($action=="itemDesc_popup")
{
	echo load_html_head_contents("WO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
	
	var item_category= "";
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
                         
                    	<input type="hidden" name="txt_selected_item_id" id="txt_selected_item_id" class="text_boxes" value="">   
                    </th> 
                </thead>
                <tr class="general">
                    <td>
						 <? echo create_drop_down( "cbo_importer_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'pi_garments_service_controller',this.value, 'load_supplier_dropdown', 'supplier_td' );",0); ?>       
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_item_desc').value+'_'+''+'_'+document.getElementById('cbo_importer_id').value+'_'+document.getElementById('cbo_supplier_id').value, 'create_item_search_list_view', 'search_div', 'pi_garments_service_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
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
	
	
	$company_id =$data[2];
	
	$field_name="item_description";
	
	if($company_id==0) { echo "Please Select Company First."; die; }
	if($data[3]==0) $supplier_id="%%"; else $supplier_id =$data[3];
	
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$sql = "select id, item_category_id, supplier_id, item_group_id, item_description, item_size, unit_of_measure from product_details_master where company_id=$company_id and item_category_id not in (74,72,79,73,71,77,78,75,76,1,2,3,4,12,13,14,24,25,31) and supplier_id like '$supplier_id' and item_description like '%".trim($data[0])."%' and status_active=1 and is_deleted=0";
		
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$arr=array (0=>$item_category,1=>$supplier_arr,2=>$item_group_arr,5=>$unit_of_measurement);
		
	echo create_list_view("tbl_list_search", "Item Category, Supplier, Item Group, Item Description, Item Size, UOM", "120,120,120,160,90","770","250",0, $sql, "js_set_value", "id", "", 1, "item_category_id,supplier_id,item_group_id,0,0,unit_of_measure", $arr , "item_category_id,supplier_id,item_group_id,item_description,item_size,unit_of_measure", "",'','0,0,0,0,0,0','',1);


	//echo $sql;
	exit();	
} 

if( $action == 'populate_data_item_form' ) 
{
	$data=explode('**',$data);
	//print_r($data);
	//die;
	$item_id=$data[0];
	$item_category_id='';
	$tblRow=$data[2];
	//echo "select id, item_group_id, item_description,item_category_id, item_size, unit_of_measure from product_details_master where id in($item_id)";
	$data_array=sql_select("select id, item_group_id, item_description,item_category_id, item_size, unit_of_measure from product_details_master where id in($item_id)");
	foreach($data_array as $row)
	{
		$tblRow++;
		?>
		<tr class="general" id="row_<? echo $tblRow; ?>">
			<td> 
				<?
					echo create_drop_down( "itemgroupid_".$tblRow, 130, "SELECT id,item_name FROM lib_item_group WHERE  status_active = 1 AND is_deleted = 0 ORDER BY item_name ASC",'id,item_name', 1, '-Select-',$row[csf('item_group_id')],"get_php_form_data( this.value+'**'+'uom_$tblRow', 'get_uom', 'requires/pi_garments_service_controller' );",1); 
					//item_category =".$row[csf('item_category_id')]." AND
				?>  
			</td>
			<td>
				<? 
					echo create_drop_down( "itemCategoryId_".$tblRow, 151, $item_category,'', 1, '--Select--',$row[csf('item_category_id')],"",1,'','','','74,72,79,73,71,77,78,75,76,1,2,3,4,12,13,14,24,25,31');
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


 