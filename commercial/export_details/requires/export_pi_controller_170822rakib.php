<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=5 and report_id=151 and is_deleted=0 and status_active=1");
    //print_r($print_report_format);
	$print_report_format_arr=explode(",",$print_report_format);
		
	foreach($print_report_format_arr as $id){
		if($id==110){echo '<input type="button" id="btn_print" value="Knit Fabric 2" class="formbutton" style="width:80px; " onClick="show_print_report();" >&nbsp;';}
		if($id==111){echo '<input type="button" id="btn_print_3" value="Grmnt Washing" class="formbutton" style="width:100px; " onClick="show_print_report_3();">&nbsp;';}
		if($id==89){echo '<input type="button" id="btn_print_4" value="Accessories 1" class="formbutton" style="width:80px;" onClick="show_print_report_4();" >&nbsp;';}
		if($id==129){echo '<input type="button" id="btn_print_5" value="Grmnt Embroidery" class="formbutton" style="width:100px;" onClick="show_print_report_5();">&nbsp;';}
		if($id==751){echo '<input type="button" id="btn_print_6" value="Accessories 2" class="formbutton" style="width:80px;" onClick="show_print_report_6();">&nbsp;';}
		if($id==191){echo '<input type="button" id="btn_print_7" value="Knit Fabric 3" class="formbutton" style="width:80px;" onClick="show_print_report_7();" >&nbsp;';}
		if($id==109){echo '<input type="button" id="Print1" value="Knit Fabric 1" class="formbutton" style="width:80px;" onClick="fnc_pi_mst(4);" >&nbsp;';}
		if($id==227){echo '<input type="button" id="Print8" value="Knit Fabric 4" class="formbutton" style="width:80px;" onClick="show_print_report_8();" >&nbsp;';}
		if($id==235){echo '<input type="button" id="btn_print_9" value="Knit Fabric 5" class="formbutton" style="width:80px;" onClick="show_print_report_9();">&nbsp;';}
		if($id==274){echo '<input type="button" id="btn_print_10" value="Knitting, Dyeing & Finishing" class="formbutton" style="width:120px;" onClick="show_print_report_10();">&nbsp;';}
		if($id==241){echo '<input type="button" id="btn_print_11" value="Accessories 3" class="formbutton" style="width:80px;" onClick="show_print_report_11();">&nbsp;';}
        if($id==427){echo '<input type="button" id="btn_print_12" value="AOP" class="formbutton" style="width:80px;" onClick="show_print_report_12();">&nbsp;';}
        if($id==269){echo '<input type="button" id="btn_print_13" value="Accessories 4" class="formbutton" style="width:80px;" onClick="show_print_report_13();">&nbsp;';}
        if($id==304){echo '<input type="button" id="btn_print_14" value="Accessories 5" class="formbutton" style="width:80px;" onClick="show_print_report_14();">&nbsp;';}
        //if($id==274){echo "$('#print_7').show();\n";}

	}
	exit();
}

if($action=="load_drop_down_buyer")
{
	$data = explode("_",$data);
	$company_id=$data[0];
	$selected_buyer=$data[2];
	if($selected_buyer!=0)
	{
		$disabled=1;
	}
	else
	{
		$disabled=0;
	}
	if($data[1]==0)
	{
		echo create_drop_down( "cbo_buyer_name", 151, $blank_array,"",1, "-- Select Buyer --", 0, "" );
	}
	else if($data[1]==1)
	{
		echo create_drop_down( "cbo_buyer_name", 151, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Buyer --", "$selected_buyer", "",$disabled,"","","" );
	}
	else if($data[1]==2)
	{
		echo create_drop_down( "cbo_buyer_name", 151, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected_buyer, "",$disabled,"","","" ); 
	}
	
	exit();
}

if($action=="load_drop_down_avising_bank")
{	
	if ($db_type==0)
	{
		$sql="select a.id, concat(a.bank_name,' ( ', a.branch_name,' )') as bank_name from lib_bank a , lib_bank_account b where a.advising_bank=1 and a.id=b.account_id and a.status_active=1 and b.status_active=1 and b.company_id=$data group by a.id ,a.bank_name, a.branch_name ";
	}
	else
	{
		$sql="select a.id, (a.bank_name||' ( '|| a.branch_name||' )') as bank_name from lib_bank a , lib_bank_account b where a.advising_bank=1 and a.id=b.account_id and a.status_active=1 and b.status_active=1 and b.company_id=$data group by a.id ,a.bank_name, a.branch_name ";
	}
	//echo $sql;
	$result=sql_select($sql);
	$selected=0;
	if (count($result)==1) {
		$selected=$result[0][csf('id')];
	}

	echo create_drop_down( "cbo_advising_bank", 151,$sql,"id,bank_name", 1, "-- Select Bank --", $selected, "" );
	// echo create_drop_down( "cbo_advising_bank", 151, "$sql","id,bank_name",1, "-- Select Bank --", "", "","","","","" );		
	exit();
}

if ($action==='company_variable_setting_check')
{
	$variable_setting = return_field_value('pi_source_btb_lc', 'variable_settings_commercial', "company_name=$data and variable_list=27", 'pi_source_btb_lc');
	echo $variable_setting;
	exit();
}	

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if ($operation==0)  // Insert Here
	{ 
		/*$pi_number=str_replace("'","",$pi_number);
		if($xyz && $pi_number!="")
		{
			echo "11**";die;
		}*/
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

			if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
			else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";

            if (str_replace("'", "", $hidden_variable_setting) == 1)
            {
            	$new_pi_number=explode("*",return_mrr_number( str_replace("'","",$cbo_exporter_id), '', 'EPI', date("Y",time()), 5, "select pi_no_prefix, pi_no_prefix_num from com_export_pi_mst where entry_form=152 and exporter_id=$cbo_exporter_id $insert_date_con and pi_no_prefix is not null and pi_no_prefix_num is not null order by id desc ", "pi_no_prefix", "pi_no_prefix_num" ));
            	$pi_number = "'".$new_pi_number[0]."'";
            } 
		
			$field_array="id,pi_no_prefix,pi_no_prefix_num,item_category_id,exporter_id,within_group,buyer_id,pi_number,pi_date,last_shipment_date,pi_validity_date,currency_id,hs_code,swift_code,internal_file_no,remarks,attention,advising_bank,entry_form,inserted_by,insert_date,pi_revised_date";
			
			$data_array="(".$id.",'".$new_pi_number[1]."','".$new_pi_number[2]."',".$cbo_item_category_id.",".$cbo_exporter_id.",".$cbo_within_group.",".$cbo_buyer_name.",".$pi_number.",".$pi_date.",".$last_shipment_date.",".$pi_validity_date.",".$cbo_currency_id.",".$hs_code.",".$txt_swift.",".$txt_internal_file_no.",".$txt_remarks.",".$txt_attention.",".$cbo_advising_bank.",152,".$user_id.",'".$pc_date_time."',".$pi_revised_date.")";			
			//echo "5**insert into com_export_pi_mst (".$field_array.") values ".$data_array;die;	
			$rID=sql_insert("com_export_pi_mst",$field_array,$data_array,1);
			//mysql_query("ROLLBACK"); 
			//echo "5**".$rID;die;
			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");  
					echo "0**".$id."**".str_replace("'","",$pi_number);
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
					echo "0**".$id."**".str_replace("'","",$pi_number);
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
			
			$sql_app=sql_select("select pi_number from com_pi_master_details where export_pi_id=$update_id and import_pi=1 and status_active =1 and is_deleted=0");
			if(count($sql_app)>0)
			{
				echo "7**".str_replace("'", '', $update_id);disconnect($con);
				die;	
			}

			$field_array="item_category_id*exporter_id*within_group*buyer_id*pi_number*pi_date*last_shipment_date*pi_validity_date*currency_id*hs_code*swift_code*internal_file_no*remarks*attention*advising_bank*pi_revised_date*updated_by*update_date";
			
			$data_array="".$cbo_item_category_id."*".$cbo_exporter_id."*".$cbo_within_group."*".$cbo_buyer_name."*".$pi_number."*".$pi_date."*".$last_shipment_date."*".$pi_validity_date."*".$cbo_currency_id."*".$hs_code."*".$txt_swift."*".$txt_internal_file_no."*".$txt_remarks."*".$txt_attention."*".$cbo_advising_bank."*".$pi_revised_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rID=sql_update("com_export_pi_mst",$field_array,$data_array,"id",$update_id,1);
			if($db_type==0)
			{
				if($rID)
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'", '', $update_id)."**".str_replace("'","",$pi_number);
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
					echo "1**".str_replace("'", '', $update_id)."**".str_replace("'","",$pi_number);
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
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$sql_app=sql_select("select approved from com_pi_master_details where id=$update_id and approved=1");
		$sql_app=sql_select("select pi_number from com_pi_master_details where export_pi_id=$update_id and import_pi=1 and status_active =1 and is_deleted=0");
		if(count($sql_app)>0)
		{
			echo "7**".str_replace("'", '', $update_id);disconnect($con); 
			die;	
		}
		
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("com_export_pi_mst",$field_array,$data_array,"id",$update_id,0);
		
		$field_array_dtls="status_active*is_deleted*updated_by*update_date";
		$data_array_dtls="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID2=sql_update("com_export_pi_dtls",$field_array_dtls,$data_array_dtls,"pi_id",$update_id,1);
		
		
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
				echo "2**".str_replace("'", '', $update_id);
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
	
		$field_array="id,pi_id,work_order_no,work_order_id,buyer_style_ref,work_order_dtls_id,determination_id,construction,composition,hs_code,booking,gmts_item_id,main_process_id,embl_type,body_part,item_desc,item_size,color_id,aop_color_id,gsm,dia_width,wash_type,uom,quantity,rate,amount,net_pi_rate,net_pi_amount,remarks,inserted_by,insert_date"; 
		
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
			$jobstyle="jobstyle_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$determinationId="hideDeterminationId_".$i; 
			$construction="construction_".$i; 
			$composition="composition_".$i;
			$hscode="hscode_".$i;
			$salesbooking="salesbooking_".$i;
			$gsmitem="hideGsmItem_".$i;
			$processembl="hideProcessEmbl_".$i;
			$embltype="hideEmblType_".$i;
			$bodypart="hideBodypart_".$i;
			$itemDesc="itemDesc_".$i;
			$itemSize="hideitemSize_".$i;
			$colorId="colorId_".$i;
			$aopcolorId="hideAopColor_".$i;
			$gsm="gsm_".$i;
			$diawidth="diawidth_".$i;
			$washType="hideWashType_".$i;
			$uom="uom_".$i;
			$quantity="quantity_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			$remarks="txtRemarks_".$i;
			
			$perc=(str_replace("'","",$$amount)/$txt_total_amount)*100;
			$net_pi_amount=($perc*$txt_total_amount_net)/100;
			$net_pi_rate=$net_pi_amount/str_replace("'","",$$quantity);
			
			if($cbo_currency_id==1)
				$net_pi_amount=number_format($net_pi_amount,$dec_place[4],'.','');
			else
				$net_pi_amount=number_format($net_pi_amount,$dec_place[5],'.','');
					
			$net_pi_rate=number_format($net_pi_rate,$dec_place[3],'.','');
			
			if($data_array!="") $data_array.=",";
			$data_array .="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$jobstyle)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$determinationId)."','".str_replace("'","",$$construction)."','".str_replace("'","",$$composition)."','".str_replace("'","",$$hscode)."','".str_replace("'","",$$salesbooking)."','".str_replace("'","",$$gsmitem)."','".str_replace("'","",$$processembl)."','".str_replace("'","",$$embltype)."','".str_replace("'","",$$bodypart)."','".str_replace("'","",$$itemDesc)."','".str_replace("'","",$$itemSize)."','".str_replace("'","",$$colorId)."','".str_replace("'","",$$aopcolorId)."','".str_replace("'","",$$gsm)."','".str_replace("'","",$$diawidth)."','".str_replace("'","",$$washType)."','".str_replace("'","",$$uom)."',".$$quantity.",".$$rate.",".$$amount.",".$net_pi_rate.",".$net_pi_amount.",'".str_replace("'","",$$remarks)."',".$user_id.",'".$pc_date_time."')"; 
			
			$id=$id+1;
			
		}
		//echo "5**insert into com_export_pi_dtls (".$field_array.") Values ".$data_array."";die;
		$rID=sql_insert("com_export_pi_dtls",$field_array,$data_array,0);
		$rID2=sql_update("com_export_pi_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		//echo "5**".$rID."**".$rID2;die;
	
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
		//die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$sql_app=sql_select("select pi_number from com_pi_master_details where export_pi_id=$update_id and import_pi=1 and status_active =1 and is_deleted=0");
		if(count($sql_app)>0)
		{
			echo "7**".str_replace("'", '', $update_id);disconnect($con); 
			die;	
		}
		
		$field_array_update="work_order_no*work_order_id*buyer_style_ref*work_order_dtls_id*determination_id*construction*composition*hs_code*booking*gmts_item_id*main_process_id*embl_type*body_part*item_desc*item_size*color_id*aop_color_id*gsm*dia_width*wash_type*uom*quantity*rate*amount*net_pi_rate*net_pi_amount*remarks*updated_by*update_date";
			
		$field_array="id, pi_id, work_order_no, work_order_id, buyer_style_ref, work_order_dtls_id, determination_id, construction, composition, hs_code, booking, gmts_item_id, main_process_id, embl_type, body_part, item_desc, item_size, color_id, aop_color_id, gsm, dia_width, wash_type, uom, quantity, rate, amount, net_pi_rate, net_pi_amount,remarks, inserted_by, insert_date"; 
		
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
			$jobstyle="jobstyle_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$determinationId="hideDeterminationId_".$i; 
			$construction="construction_".$i; 
			$composition="composition_".$i;
			$hscode="hscode_".$i;
			$salesbooking="salesbooking_".$i;
			$gsmitem="hideGsmItem_".$i;
			$processembl="hideProcessEmbl_".$i;
			$embltype="hideEmblType_".$i;
			$bodypart="hideBodypart_".$i;
			$itemDesc="itemDesc_".$i;
			$itemSize="hideitemSize_".$i;
			$colorId="colorId_".$i;
			$aopcolorId="hideAopColor_".$i;
			$gsm="gsm_".$i;
			$diawidth="diawidth_".$i;
			$washType="hideWashType_".$i;
			$uom="uom_".$i;
			$quantity="quantity_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			$remarks="txtRemarks_".$i;
						
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
				$data_array_update[str_replace("'",'',$$updateIdDtls)] = explode("*",("'".str_replace("'","",$$workOrderNo)."'*'".str_replace("'","",$$workOrderId)."'*'".str_replace("'","",$$jobstyle)."'*'".str_replace("'","",$$workOrderDtlsId)."'*'".str_replace("'","",$$determinationId)."'*'".str_replace("'","",$$construction)."'*'".str_replace("'","",$$composition)."'*'".str_replace("'","",$$hscode)."'*'".str_replace("'","",$$salesbooking)."'*'".str_replace("'","",$$gsmitem)."'*'".str_replace("'","",$$processembl)."'*'".str_replace("'","",$$embltype)."'*'".str_replace("'","",$$bodypart)."'*'".str_replace("'","",$$itemDesc)."'*'".str_replace("'","",$$itemSize)."'*'".str_replace("'","",$$colorId)."'*'".str_replace("'","",$$aopcolorId)."'*'".str_replace("'","",$$gsm)."'*'".str_replace("'","",$$diawidth)."'*'".str_replace("'","",$$washType)."'*'".str_replace("'","",$$uom)."'*".$$quantity."*".$$rate."*".$$amount."*".$net_pi_rate."*".$net_pi_amount."*'".str_replace("'","",$$remarks)."'*".$user_id."*'".$pc_date_time."'"));
			}
			else
			{
				if($data_array!="") $data_array.=",";
				$data_array .="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$jobstyle)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$determinationId)."','".str_replace("'","",$$construction)."','".str_replace("'","",$$composition)."','".str_replace("'","",$$hscode)."','".str_replace("'","",$$salesbooking)."','".str_replace("'","",$$gsmitem)."','".str_replace("'","",$$processembl)."','".str_replace("'","",$$embltype)."','".str_replace("'","",$$bodypart)."','".str_replace("'","",$$itemDesc)."','".str_replace("'","",$$itemSize)."','".str_replace("'","",$$colorId)."','".str_replace("'","",$$aopcolorId)."','".str_replace("'","",$$gsm)."','".str_replace("'","",$$diawidth)."','".str_replace("'","",$$washType)."','".str_replace("'","",$$uom)."',".$$quantity.",".$$rate.",".$$amount.",".$net_pi_rate.",".$net_pi_amount.",'".str_replace("'","",$$remarks)."',".$user_id.",'".$pc_date_time."')"; 
				$id=$id+1;
			}
		}

		$rID=true; $rID2=true;
		// echo "10**".bulk_update_sql_statement( "com_export_pi_dtls", "id", $field_array_update, $data_array_update, $id_arr ); die;
		if(count($data_array_update)>0)
		{
			$rID=execute_query(bulk_update_sql_statement( "com_export_pi_dtls", "id", $field_array_update, $data_array_update, $id_arr ));
		}
		
		if($data_array!="")
		{
			$rID2=sql_insert("com_export_pi_dtls",$field_array,$data_array,0);
		}

		$rID3=sql_update("com_export_pi_mst",$field_array_update2,$data_array_update2,"id",$update_id,1);
		// echo "5**".$rID."**".$rID2."**".$rID3;die;
		
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
		//die;
	}
	else if ($operation==2)// Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$sql_app=sql_select("select approved from com_pi_master_details where id=$update_id and approved=1");
		$sql_app=sql_select("select pi_number from com_pi_master_details where export_pi_id=$update_id and import_pi=1 and is_deleted = 0 and status_active = 1");
		if(count($sql_app)>0)
		{
			echo "7**".str_replace("'", '', $update_id);disconnect($con);
			die;	
		}
		
		
		for($i=1;$i<=$total_row;$i++)
		{
			$deleteIdDtls="deleteIdDtls_".$i;
			$workOrderDtlsId="hideWoDtlsId_".$i;
			$amount="amount_".$i;
		
			if(str_replace("'","",$$deleteIdDtls)!="")
			{
				$id_arr[]=str_replace("'",'',$$deleteIdDtls);
				$data_array_update[str_replace("'",'',$$deleteIdDtls)] = explode("*",("0*1*".$user_id."*'".$pc_date_time."'"));

				$detele_amount += str_replace("'",'',$$amount);
			}
		
		}
		$master_data = sql_select("select a.net_total_amount, a.total_amount from com_export_pi_mst a where a.id = $update_id");

		$total_amount = $master_data[0][csf("total_amount")] - $detele_amount;
		$net_total_amount = $master_data[0][csf("net_total_amount")] - $detele_amount;

		$field_array_mst_update = "total_amount*net_total_amount";
		$data_array_mst_update=$total_amount."*".$net_total_amount;
		//echo "10**".$data_array_mst_update."==".$detele_amount;die;
		$field_array_update="status_active*is_deleted*updated_by*update_date";
		if(count($data_array_update)>0)
		{
			$rID=execute_query(bulk_update_sql_statement( "com_export_pi_dtls", "id", $field_array_update, $data_array_update, $id_arr ));
		}		
		
		$rID2=sql_update("com_export_pi_mst",$field_array_mst_update,$data_array_mst_update,"id",$update_id,1);

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'", '', $update_id);;
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
				echo "2**".str_replace("'", '', $update_id);;
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

if ($action=="pi_popup")
{
	echo load_html_head_contents("PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value( val ){
			ids = val.split('__');
			document.getElementById('txt_selected_pi_id').value=ids[0];
			document.getElementById('item_category_id').value=ids[1];
			parent.emailwindow.hide();
		}
		
		function is_chk(value) {
			if(document.getElementById('checkalltr').checked==true){
				document.getElementById('hid_is_dtls').value=2;
			}else{
				document.getElementById('hid_is_dtls').value=1;
			}
		}
		
    </script>
	</head>

	<body>
	<div align="center" style="width:900px;">
		<form name="searchpifrm"  id="searchpifrm">
			<fieldset style="width:100%;">
			<legend>Enter search words</legend>           
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="950" class="rpt_table">
	                <thead>
	                    <th>Company</th>
	                    <th>PI Number</th>
                        <th><? if($item_category_id==37) echo "Wash "; ?>Job No</th>
	                    <th>Sales/Booking</th>
	                    <th>System ID</th>
	                    <th>Date Range</th>
	                    <th>Without Details</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                    	<input type="hidden" name="txt_selected_pi_id" id="txt_selected_pi_id" value="">   
	                    	<input type="hidden" name="item_category_id" id="item_category_id" value="<? echo $item_category_id; ?>">   
	                    	<input type="hidden" name="hid_is_dtls" id="hid_is_dtls" value="1">   
	                    </th> 
	                </thead>
	                <tr class="general">
	                    <td>
							 <? echo create_drop_down( "cbo_exporter_id", 100,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',$exporter_id,"",1); ?>
							 <!-- <input type="hidden" name="item_category_id" id="item_category_id" value="<? //echo $item_category; ?>">  -->     
	                    </td>
	                    <td> 
	                        <input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:100px">
	                    </td>
                        <td> 
	                        <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px">
	                    </td>						
                        <td> 
	                        <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px">
	                    </td>						
	                    <td> 
	                        <input type="text" name="txt_sys_id" id="txt_sys_id" class="text_boxes" style="width:100px">
	                    </td>						
	                    <td align="center">
	                      <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
						  <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						</td>
						<td align="center"><input type="checkbox" name="checkalltr" id="checkalltr" onClick="is_chk(this.value)" value="1"></td> 
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_exporter_id').value+'_'+document.getElementById('txt_sys_id').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('hid_is_dtls').value+'_'+document.getElementById('item_category_id').value+'_'+<?=$cbo_within_group;?>+'_'+<?=$cbo_buyer_name;?>+'_'+document.getElementById('txt_booking_no').value, 'create_pi_search_list_view', 'search_div', 'export_pi_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
	                	<td colspan="8" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_pi_search_list_view")
{
	$data=explode('_',$data);
	$pi_number   = trim($data[0]);
    $date_from   = $data[1];
    $date_to     = $data[2];
    $exporter_id = $data[3];
    $sys_id      = trim($data[4]);
    $job_no      = trim($data[5]);
    $is_dtls     = $data[6];
	$iten_cat_id     = $data[7];
	$within_group     = $data[8];
	$cbo_buyer_name     = $data[9];
	$txt_booking_no     = trim($data[10]);
	 
	$pi_number_cond=$sys_id_cond=$job_number_cond=$pi_date_cond=$within_group_cond=$buyer_name_cond=$booking_no_cond='';
	if ($pi_number != '') $pi_number_cond=" and a.pi_number like '%".$pi_number."%'";
	if ($sys_id != '') $sys_id_cond=" and a.id=$sys_id";	
	if ($job_no != '') $job_number_cond=" and b.work_order_no like '%".$job_no."'";
	if ($txt_booking_no != '') $booking_no_cond=" and b.booking like '%".$txt_booking_no."'";
	if ($within_group != '' && $within_group != 0) $within_group_cond=" and a.within_group =".$within_group;
	if ($cbo_buyer_name != '' && $cbo_buyer_name != 0) $buyer_name_cond=" and a.buyer_id =".$cbo_buyer_name;

	if ($date_from != '' &&  $date_to != '')
	{
		if($db_type==0)	{
			$pi_date_cond = "and a.pi_date between '".change_date_format($date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($date_to, "yyyy-mm-dd", "-")."'";
		} else {
			$pi_date_cond = "and a.pi_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$order_buyer_arr=return_library_array( "select id, party_id from subcon_ord_mst where entry_form=295 and status_active=1",'id','party_id');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$sub_order_sql=sql_select("select id, job_no_prefix_num, party_id from subcon_ord_mst where entry_form=295 and status_active=1");
	$sub_order_data=array();
	foreach($sub_order_sql as $row)
	{
		$sub_order_data[$row[csf("id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$sub_order_data[$row[csf("id")]]["party_id"]=$row[csf("party_id")];
	}
	
	if($is_dtls==1)
	{
		if($db_type==2) 
		{
			$job_con=" rtrim(xmlagg(xmlelement(e,b.work_order_no,',').extract('//text()') order by b.work_order_no).GetClobVal(),',') AS JOB_NO, rtrim(xmlagg(xmlelement(e,b.work_order_id,',').extract('//text()') order by b.work_order_id).GetClobVal(),',') AS JOB_ID, rtrim(xmlagg(xmlelement(e,b.booking,',').extract('//text()') order by b.booking).GetClobVal(),',') AS BOOKING_NO";
		} else {
			$job_con="group_concat(b.work_order_no) as JOB_NO , group_concat(b.work_order_id) as JOB_ID, group_concat(b.booking) as BOOKING_NO";
		}

		$sql= "SELECT a.ID, a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.EXPORTER_ID, a.WITHIN_GROUP, a.LAST_SHIPMENT_DATE, a.HS_CODE, a.BUYER_ID, $job_con
		from com_export_pi_mst a, com_export_pi_dtls b
		where a.id=b.pi_id and a.exporter_id=$exporter_id $pi_number_cond $sys_id_cond $job_number_cond $pi_date_cond $within_group_cond $buyer_name_cond $booking_no_cond and a.entry_form=152 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.pi_number, a.pi_date, a.item_category_id, a.exporter_id, a.within_group, a.last_shipment_date, a.hs_code, a.buyer_id
		order by a.id desc";
	} 
	else  // without details
	{
		$sql_dtls="SELECT a.id as PI_ID from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.exporter_id=$exporter_id $pi_number_cond $sys_id_cond $job_number_cond $pi_date_cond $booking_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=152 and b.pi_id is not null";
		$sql_dtls_res = sql_select($sql_dtls);

		$tot_rows=0;
		$pi_id_arr=array();
		foreach ($sql_dtls_res as $val) {
			$tot_rows++;
			$pi_id_arr[$val['PI_ID']] = $val['PI_ID'];
		}

		if (!empty($pi_id_arr))
	    {     
	        $pi_id_cond = '';
	        if($db_type==2 && $tot_rows>1000)
	        {
	            $piIds = array_keys($pi_id_arr);
	            $piIdArr = array_chunk($piIds,999);
	            foreach($piIdArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $pi_id_cond .= " and a.id not in($ids) ";
	            }
	        }
	        else
	        {
	            $piIds = implode(',',array_keys($pi_id_arr));
	            $pi_id_cond = " and a.id not in ($piIds) ";
	        }
	    }

		$sql= "SELECT a.ID, a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.EXPORTER_ID, a.WITHIN_GROUP, a.LAST_SHIPMENT_DATE, a.HS_CODE, a.BUYER_ID 
		from com_export_pi_mst a 
		where a.exporter_id=$exporter_id $pi_number_cond $sys_id_cond $job_number_cond $pi_date_cond $pi_id_cond $within_group_cond $buyer_name_cond and a.entry_form=152 and a.status_active=1 and a.is_deleted=0 order by a.id desc";
	}
	// echo $sql;//die;
	$sql_result=sql_select($sql);
	?>
    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="970" class="rpt_table">
        <thead>
			<tr>
				<th colspan="12"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
			</tr>
            <tr>
                <th width="30">SL</th>
                <th width="100">PI No</th>
                <th width="70">PI Date</th>
                <th width="80">Item Category</th>
                <th width="120">Exporter</th>
                <th width="120"><? if($iten_cat_id==10) {echo "Customer/";} ?>Buyer</th>
                <th width="100"><? if($iten_cat_id==37) echo "Wash "; ?>Job No</th> 
                <th width="100">Sales/Booking No</th> 
                <th width="50">Job Suffix</th>
                <th width="60">Within Group</th>
                <th width="70">Last Shipment Date</th>
                <th>HS Code</th>
            </tr>
        </thead>
    </table>
    <div style="width:970px; max-height:270; overflow-y:scroll;">
	    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="952" class="rpt_table" id="list_view">
	    	<tbody>
	        	<?
				$i=1;
				foreach($sql_result as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
	                <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" onClick="js_set_value('<? echo $row['ID'].'__'.$row['ITEM_CATEGORY_ID'];?>')" style="cursor:pointer" align="center">
	                    <td width="30"><? echo $i; ?></td>
	                    <td width="100" style="word-break:break-all"><? echo $row['PI_NUMBER']; ?></td>
	                    <td width="70" align="center"><? echo change_date_format($row['PI_DATE']); ?></td>
	                    <td width="80" style="word-break:break-all"><? echo $export_item_category[$row['ITEM_CATEGORY_ID']]; ?></td>
	                    <td width="120" style="word-break:break-all"><? echo $comp[$row['EXPORTER_ID']]; ?></td>
	                    <td width="120" title="<? echo $is_dtls; ?>" style="word-break:break-all">
						<?
						if ($is_dtls==1)
						{
							if($db_type==2 ) $row['JOB_ID'] = $row['JOB_ID']->load();	
							if($db_type==2 ) $row['JOB_NO'] = $row['JOB_NO']->load();	
							if($db_type==2 ) $row['BOOKING_NO'] = $row['BOOKING_NO']->load();	
							$job_buyer = '';$job_suffix = '';
							$job_id_arr=array_unique(explode(',',$row['JOB_ID']));
							//print_r($job_id_arr);die;
							foreach($job_id_arr as $job_id)
							{
								if($row['WITHIN_GROUP']==2)
								{
									if($row['ITEM_CATEGORY_ID']==10)
									{
										$job_buyer.=$buyer_arr[$row['BUYER_ID']].',';
									}else{
										$job_buyer.=$buyer_arr[$sub_order_data[$job_id]["party_id"]].',';
									}
								}
								else
								{
									$job_buyer.=$comp[$sub_order_data[$job_id]["party_id"]].',';
								}
								$job_suffix.=$sub_order_data[$job_id]["job_no_prefix_num"].",";
							}
							$job_buyer=chop($job_buyer, ',');
							$job_suffix=chop($job_suffix, ',');
							echo $job_buyer;
						} else echo '';
						?></td>
	                    <td width="100" style="word-break:break-all"><? if ($is_dtls==1) echo implode(',',array_unique(explode(',',$row['JOB_NO']))); else echo ''; ?></td>
	                    <td width="100" style="word-break:break-all"><? if ($is_dtls==1) echo implode(',',array_unique(explode(',',$row['BOOKING_NO']))); else echo '';?></td>
                        <td width="50" align="center" style="word-break:break-all"><? if ($is_dtls==1) echo $job_suffix; else echo '' ?></td>  
	                    <td width="60" style="word-break:break-all"><? echo $yes_no[$row['WITHIN_GROUP']]; ?></td>
	                    <td width="70"><? if($row['LAST_SHIPMENT_DATE'] != '' && $row['LAST_SHIPMENT_DATE'] != '0000-00-00') echo change_date_format($row['LAST_SHIPMENT_DATE']); ?></td>
	                    <td style="word-break:break-all"><? echo $row['HS_CODE']; ?></td>
	                </tr>
	                <?
	                $i++;
				}
				?>
	        </tbody>  
	    </table>
    </div>
    <? 
	exit();	
} 

if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("SELECT ID, ITEM_CATEGORY_ID, EXPORTER_ID, WITHIN_GROUP, BUYER_ID, PI_NUMBER, PI_DATE, LAST_SHIPMENT_DATE, PI_VALIDITY_DATE, CURRENCY_ID, HS_CODE, SWIFT_CODE, INTERNAL_FILE_NO, REMARKS,ATTENTION, ADVISING_BANK, TOTAL_AMOUNT, UPCHARGE, DISCOUNT, NET_TOTAL_AMOUNT from com_export_pi_mst where id='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_item_category_id').value = '".$row["ITEM_CATEGORY_ID"]."';\n";  
		echo "document.getElementById('cbo_exporter_id').value = '".$row["EXPORTER_ID"]."';\n";  
		echo "document.getElementById('cbo_within_group').value = '".$row["WITHIN_GROUP"]."';\n";  
		
		echo "load_drop_down('requires/export_pi_controller',".$row["EXPORTER_ID"]."+'_'+".$row["WITHIN_GROUP"].", 'load_drop_down_buyer', 'buyer_td' );\n";
		
		if($row["LAST_SHIPMENT_DATE"]=="0000-00-00" || $row["LAST_SHIPMENT_DATE"]=="") $last_shipment_date=""; else $last_shipment_date=change_date_format($row["LAST_SHIPMENT_DATE"]);
		if($row["PI_VALIDITY_DATE"]=="0000-00-00" || $row["PI_VALIDITY_DATE"]=="") $pi_validity_date=""; else $pi_validity_date=change_date_format($row["PI_VALIDITY_DATE"]);
		echo "document.getElementById('cbo_buyer_name').value = '".$row["BUYER_ID"]."';\n";  
		echo "document.getElementById('pi_number').value = '".$row["PI_NUMBER"]."';\n";  
		echo "document.getElementById('pi_date').value = '".change_date_format($row["PI_DATE"])."';\n";  
		echo "document.getElementById('last_shipment_date').value = '".$last_shipment_date."';\n";  
		echo "document.getElementById('pi_validity_date').value = '".$pi_validity_date."';\n";  
		echo "document.getElementById('cbo_currency_id').value = '".$row["CURRENCY_ID"]."';\n";  
		echo "document.getElementById('hs_code').value = '".$row["HS_CODE"]."';\n";  
		echo "document.getElementById('txt_swift').value = '".$row["SWIFT_CODE"]."';\n";  
		
		echo "document.getElementById('txt_total_amount').value = '".$row["TOTAL_AMOUNT"]."';\n";  
		echo "document.getElementById('txt_upcharge').value = '".($row["UPCHARGE"])."';\n";
		echo "document.getElementById('txt_discount').value = '".$row["DISCOUNT"]."';\n";
		echo "document.getElementById('txt_total_amount_net').value = '".$row["NET_TOTAL_AMOUNT"]."';\n";
		
		echo "document.getElementById('txt_internal_file_no').value = '".$row["INTERNAL_FILE_NO"]."';\n";  
		echo "document.getElementById('txt_remarks').value = '".($row["REMARKS"])."';\n";
		echo "document.getElementById('txt_attention').value = '".($row["ATTENTION"])."';\n";
		echo "document.getElementById('cbo_advising_bank').value = '".($row["ADVISING_BANK"])."';\n";
		echo "document.getElementById('update_id').value = '".$row["ID"]."';\n";
		echo "document.getElementById('txt_system_id').value = '".$row["ID"]."';\n";		

		echo "$('#cbo_item_category_id').attr('disabled','true')".";\n";
		echo "$('#cbo_exporter_id').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_pi_mst',1);\n";
	}
	exit();
}

if ($action=="populate_total_amount_data")
{
	$data_array=sql_select("SELECT ID, ITEM_CATEGORY_ID, EXPORTER_ID, WITHIN_GROUP, BUYER_ID, PI_NUMBER, PI_DATE, LAST_SHIPMENT_DATE, PI_VALIDITY_DATE, CURRENCY_ID, HS_CODE, INTERNAL_FILE_NO, REMARKS, ADVISING_BANK, TOTAL_AMOUNT, UPCHARGE, DISCOUNT, NET_TOTAL_AMOUNT from com_export_pi_mst where id='$data' and status_active=1");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_total_amount').value = '".$row["TOTAL_AMOUNT"]."';\n";  
		echo "document.getElementById('txt_upcharge').value = '".($row["UPCHARGE"])."';\n";
		echo "document.getElementById('txt_discount').value = '".$row["DISCOUNT"]."';\n";
		echo "document.getElementById('txt_total_amount_net').value = '".$row["NET_TOTAL_AMOUNT"]."';\n";
	}
	exit();
}
//---------------------------------------------- Start Pi Details -----------------------------------------------------------------------//

if( $action == 'pi_details' ) 
{
	$data = explode( '_', $data );
	$pi_id=$data[0];
	$item_category_id=$data[1];
	$importer_id=$data[2];
	//echo $pi_id.'='.$item_category_id.'='.$importer_id;die;

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4 and status_active=1",'id','item_name');

	if ($item_category_id==1 || $item_category_id==10) // FSO Knit Garments and Fabric
	{
		$tblRow=0;
		$sql = "SELECT id, work_order_no,booking, work_order_id, work_order_dtls_id, determination_id, color_id, construction, composition, gsm, dia_width, uom, quantity, rate, amount,remarks from com_export_pi_dtls where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0";
		/*$prev_pi_qnty_arr_dtls=return_library_array("SELECT a.id, a.grey_qnty_by_uom-sum(b.quantity) as balance_qty  from fabric_sales_order_dtls a ,com_export_pi_dtls b where  a.id=work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.finish_qty ",'id','balance_qty');*/

		$prev_pi_qnty_sql=sql_select("SELECT a.id as ID, a.grey_qnty_by_uom as GREY_QNTY_BY_UOM, a.pp_qnty as PP_QNTY, a.mtl_qnty as MTL_QNTY, a.fpt_qnty as FPT_QNTY, a.gpt_qnty as GPT_QNTY, sum(b.quantity) as BALANCE_QTY  from fabric_sales_order_dtls a ,com_export_pi_dtls b where  a.id=work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.grey_qnty_by_uom,a.pp_qnty, a.mtl_qnty, a.fpt_qnty, a.gpt_qnty");
		$prev_pi_qnty_arr_dtls=array();
		foreach($prev_pi_qnty_sql as $row)
		{
			$prev_pi_qnty_arr_dtls[$row['ID']]=$row['GREY_QNTY_BY_UOM']+$row['PP_QNTY']+$row['MTL_QNTY']+$row['FPT_QNTY']+$row['GPT_QNTY']-$row['BALANCE_QTY'];
		}
		//print_r($prev_pi_qnty_arr_dtls);
		$data_array=sql_select($sql);
		if(count($data_array) > 0)
		{
			foreach($data_array as $row)
			{ 
				$tblRow++;
				if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$bal_qtny=$prev_pi_qnty_arr_dtls[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
						<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $tblRow; ?>" readonly/>
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_id')]; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_dtls_id')]; ?>" readonly />
					</td>
					<td> 
                        <input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('booking')]; ?>" style="width:100px" disabled="disabled"/>
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
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny ?>" style="width:61px;" />
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
					</td>
					<?
						if($item_category_id==10){
							?>
								<td><input type="text" name="txtRemarks_<? echo $tblRow; ?>" id="txtRemarks_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('remarks')]; ?>" style="width:100px;" onKeyUp="copy_remarks(<? echo $tblRow; ?>)" /></td>
							<?
						}
					?>
				</tr>
				<?
			}
		}
		else
		{
			?>
				<tr bgcolor="#E9F3FF" id="row_1" align="center">
	            	<td>
	                    <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
						<input type="hidden" name="txtSerial[]" id="txtSerial_1" class="text_boxes" value="1" readonly/>
	                </td>
	                <td>
	                    <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />
	                    <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
	                    <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
	                </td>
					<td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:100px" disabled="disabled"/>
                    </td>
	                <td>
	                    <input type="text" name="construction_1" id="construction_1" class="text_boxes" style="width:110px" disabled="disabled"/>
	                    <input type="hidden" name="hideDeterminationId_1" id="hideDeterminationId_1" readonly />
	                </td>
	                <td>
	                    <input type="text" name="composition_1" id="composition_1" class="text_boxes" value="" style="width:160px" disabled="disabled"/> 
	                </td> 
	                <td>
	                    <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:80px" disabled="disabled"/>
	                    <input type="hidden" name="colorId_1" id="colorId_1"/>
	                </td>
	                <td>
	                    <input type="text" name="gsm_1" id="gsm_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
	                </td>
	                <td>
	                    <input type="text" name="diawidth_1" id="diawidth_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
	                </td>
	                 <td>
	                    <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
	                </td>
	                <td>
	                    <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
	                </td>
	                <td>
	                    <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
	                </td>
	                <td>
	                    <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
	                    <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
	                </td>	
					<?
						if($item_category_id==10){
							?>
								<td><input type="text" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" value="" style="width:100px;" onKeyUp="copy_remarks(1)" /></td>
							<?
						}
					?>
	            </tr>
			<?
		}
	}

	else if ($item_category_id==20 || $item_category_id==22) // Knitting,  Dyeing and Finishing
	{
		$tblRow=0;
		$sql = "SELECT id, work_order_no,booking, work_order_id, work_order_dtls_id, determination_id, color_id, construction, composition, gsm, dia_width, uom, quantity, rate, amount, remarks from com_export_pi_dtls where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0";
		/*$prev_pi_qnty_arr_dtls=return_library_array("SELECT a.id, a.grey_qnty_by_uom-sum(b.quantity) as balance_qty  from fabric_sales_order_dtls a ,com_export_pi_dtls b where  a.id=work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.finish_qty ",'id','balance_qty');*/

		$prev_pi_qnty_sql=sql_select("SELECT a.id as ID, sum(b.quantity) as BALANCE_QTY from wo_booking_dtls a, com_export_pi_dtls b where a.id=work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id");
		$prev_pi_qnty_arr_dtls=array();
		foreach($prev_pi_qnty_sql as $row)
		{
			$prev_pi_qnty_arr_dtls[$row[csf('id')]]=$row[csf('balance_qty')];
		}
		//print_r($prev_pi_qnty_arr_dtls);
		$data_array=sql_select($sql);
		if(count($data_array) > 0)
		{
			foreach($data_array as $row)
			{ 
				$tblRow++;
				if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$bal_qtny=$prev_pi_qnty_arr_dtls[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
						<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $tblRow; ?>" readonly/>
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('work_order_no')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_id')]; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_dtls_id')]; ?>" readonly />
					</td>
					<td> 
                        <input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('booking')]; ?>" style="width:100px" disabled="disabled"/>
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
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny ?>" style="width:61px;" />
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value="<? echo $row[csf('id')]; ?>"/>
					</td>
					<td><input type="text" name="txtRemarks_<? echo $tblRow; ?>" id="txtRemarks_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('remarks')]; ?>" style="width:100px;" onKeyUp="copy_remarks(<? echo $tblRow; ?>)" /></td>
				</tr>
				<?
			}
		}
		else
		{
			?>
				<tr bgcolor="#E9F3FF" id="row_1" align="center">
	            	<td>
	                    <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
						<input type="hidden" name="txtSerial[]" id="txtSerial_1" class="text_boxes" value="1" readonly/>
	                </td>
	                <td>
	                    <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />
	                    <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
	                    <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
	                </td>
					<td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:100px" disabled="disabled"/>
                    </td>
	                <td>
	                    <input type="text" name="construction_1" id="construction_1" class="text_boxes" style="width:110px" disabled="disabled"/>
	                    <input type="hidden" name="hideDeterminationId_1" id="hideDeterminationId_1" readonly />
	                </td>
	                <td>
	                    <input type="text" name="composition_1" id="composition_1" class="text_boxes" value="" style="width:160px" disabled="disabled"/> 
	                </td> 
	                <td>
	                    <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:80px" disabled="disabled"/>
	                    <input type="hidden" name="colorId_1" id="colorId_1"/>
	                </td>
	                <td>
	                    <input type="text" name="gsm_1" id="gsm_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
	                </td>
	                <td>
	                    <input type="text" name="diawidth_1" id="diawidth_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
	                </td>
	                 <td>
	                    <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
	                </td>
	                <td>
	                    <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
	                </td>
	                <td>
	                    <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
	                </td>
	                <td>
	                    <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
	                    <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
	                </td>	
					<td><input type="text" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" value="" style="width:100px;" onKeyUp="copy_remarks(1)" /></td>
	            </tr>
			<?
		}
	}

	else if ($item_category_id==35) // Gmts Printing
	{
		$tblRow=0;
		$sql = "SELECT ID, WORK_ORDER_NO, WORK_ORDER_ID, WORK_ORDER_DTLS_ID, BOOKING, GMTS_ITEM_ID, MAIN_PROCESS_ID, EMBL_TYPE, ITEM_DESC, COLOR_ID, UOM, QUANTITY, RATE, AMOUNT from com_export_pi_dtls where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0";
		//echo $sql;
		/*$prev_pi_qnty_arr_dtls=return_library_array("SELECT a.id, a.grey_qnty_by_uom-sum(b.quantity) as balance_qty  from subcon_ord_dtls a ,com_export_pi_dtls b where  a.id=work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.finish_qty ",'id','balance_qty');*/

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT a.ID, a.order_quantity-sum(b.quantity) as BALANCE_QTY from subcon_ord_dtls a, com_export_pi_dtls b where a.id=b.work_order_dtls_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by a.id, a.order_quantity ",'id','balance_qty');

		//print_r($prev_pi_qnty_arr_dtls);
		$data_array=sql_select($sql);
		if(count($data_array) > 0)
		{
			foreach($data_array as $row)
			{
				$tblRow++;
				$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);	
				if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$bal_qtny=$prev_pi_qnty_arr_dtls[$row['WORK_ORDER_DTLS_ID']]+$row['QUANTITY'];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row['WORK_ORDER_NO']; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row['WORK_ORDER_ID']; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row['WORK_ORDER_DTLS_ID']; ?>" readonly />
					</td>
		            <td> 
						<input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row['BOOKING']; ?>" style="width:110px" disabled="disabled"/>
					</td>
					<td>
						<input type="text" name="gsmItem_<? echo $tblRow; ?>" id="gsmItem_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $garments_item[$row['GMTS_ITEM_ID']]; ?>" style="width:110px;" disabled="disabled"/>
						<input type="hidden" name="hideGsmItem_<? echo $tblRow; ?>" id="hideGsmItem_<? echo $tblRow; ?>" value="<? echo $row['GMTS_ITEM_ID']; ?>"/>
					</td>
					<td>
		                <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row['COLOR_ID']]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row['COLOR_ID']; ?>"/>
		            </td>
		            <td style="display:none"> 
						<input type="text" name="itemDesc_<? echo $tblRow; ?>" id="itemDesc_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row['ITEM_DESC']; ?>" style="width:70px" disabled="disabled"/>
					</td>
		            <td style="display:none">
		                <input type="text" name="processEmbl_<? echo $tblRow; ?>" id="processEmbl_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $emblishment_name_array[$row['MAIN_PROCESS_ID']]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideProcessEmbl_<? echo $tblRow; ?>" id="hideProcessEmbl_<? echo $tblRow; ?>" value="<? echo $row['MAIN_PROCESS_ID']; ?>"/>
		            </td>
		            <td style="display:none">
		                <input type="text" name="emblType_<? echo $tblRow; ?>" id="emblType_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $type_array[$row['EMBL_TYPE']][$row['EMBL_TYPE']]; ?>" style="width:80px" disabled="disabled"/>
		                <input type="hidden" name="hideEmblType_<? echo $tblRow; ?>" id="hideEmblType_<? echo $tblRow; ?>" value="<? echo $row['EMBL_TYPE']; ?>"/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row['UOM'],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row['QUANTITY']; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny ?>" style="width:61px;" />
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row['RATE']; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row['AMOUNT']; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value="<? echo $row['ID']; ?>"/>
					</td>
				</tr>
				<?
			}
		}
		else
		{
			?>
				<tr bgcolor="#E9F3FF" id="row_1" align="center">
                	<td>
                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                        <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                        <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                    </td>
                    <td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="gsmItem_1" id="gsmItem_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                     <td>
                        <input type="text" name="colorId_1" id="colorId_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td style="display:none">
                        <input type="text" name="itemDesc_1" id="itemDesc_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td style="display:none">
                        <input type="text" name="processEmbl_1" id="processEmbl_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td style="display:none">
                        <input type="text" name="emblType_1" id="emblType_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                     <td>
                        <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                </tr>
			<?
		}
	}

	else if ($item_category_id==23) // All Over Printing (AOP)
	{
		$tblRow=0;
		$sql = "SELECT id, work_order_no, work_order_id, work_order_dtls_id, booking, color_id, aop_color_id,main_process_id, embl_type, body_part, gsm, uom, quantity, rate, amount from com_export_pi_dtls where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0";

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT a.id, a.order_quantity-sum(b.quantity) as balance_qty  from subcon_ord_dtls a ,com_export_pi_dtls b where  a.id=b.work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.order_quantity ",'id','balance_qty');
		//print_r($prev_pi_qnty_arr_dtls);
		$data_array=sql_select($sql);
		if(count($data_array) > 0)
		{
			foreach($data_array as $row)
			{
				$tblRow++;
				if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$bal_qtny=$prev_pi_qnty_arr_dtls[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
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
						<input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('booking')]; ?>" style="width:110px" disabled="disabled"/>
					</td>
		            <td>
		                <input type="text" name="gmtsColor_<? echo $tblRow; ?>" id="gmtsColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:80px" disabled="disabled"/>
		                <input type="hidden" name="gmtsColor_<? echo $tblRow; ?>" id="gmtsColor_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="aopColor_<? echo $tblRow; ?>" id="aopColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('aop_color_id')]]; ?>" style="width:80px" disabled="disabled"/>
		                <input type="hidden" name="aopColor_<? echo $tblRow; ?>" id="aopColor_<? echo $tblRow; ?>" value="<? echo $row[csf('aop_color_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="gsm_<? echo $tblRow; ?>" id="gsm_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('gsm')]; ?>" style="width:60px" disabled="disabled"/>
		            </td>
		            <td>
		                <input type="text" name="bodypart_<? echo $tblRow; ?>" id="bodypart_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $body_part[$row[csf('body_part')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideBodypart_<? echo $tblRow; ?>" id="hideBodypart_<? echo $tblRow; ?>" value="<? echo $row[csf('body_part')]; ?>"/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('uom')],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;"/>
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
			?>
				<tr bgcolor="#E9F3FF" id="row_1" align="center">
                	<td>
                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                        <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                        <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                    </td>
                    <td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                    </td> 
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:80px" disabled="disabled"/>
                        <input type="hidden" name="colorId_1" id="colorId_1"/>
                    </td>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:80px" disabled="disabled"/>
                        <input type="hidden" name="colorId_1" id="colorId_1"/>
                    </td>
                    <td>
                        <input type="text" name="gsm_1" id="gsm_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="bodypart_1" id="bodypart_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                     <td>
                        <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                </tr>
			<?
		}
	}

	else if ($item_category_id==37) // Gmts Wash
	{
		/*$sql_export_pi_dtls = "select a.id, a.item_category_id, a.currency_id, b.work_order_no as job_no, b.booking, b.color_id, b.uom, b.quantity, b.rate, b.amount, b.gmts_item_id, c.buyer_style_ref, c.buyer_po_no 
			from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_dtls c  
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.id= $data[1] and a.is_deleted=0 and b.is_deleted=0";*/
		$tblRow=0;
		$sql = "SELECT b.id, b.work_order_no, b.work_order_id, b.work_order_dtls_id, b.gmts_item_id, b.color_id, b.item_desc, b.main_process_id, b.wash_type, b.gsm, b.booking, b.uom, b.quantity, b.rate, b.amount, c.buyer_style_ref, c.buyer_po_no  
		from com_export_pi_dtls b, subcon_ord_dtls c   
		where b.work_order_dtls_id=c.id and b.pi_id='$pi_id' and b.quantity>0 and b.status_active=1 and b.is_deleted=0";

		/*$prev_pi_qnty_arr_dtls=return_library_array("SELECT a.id, a.grey_qnty_by_uom-sum(b.quantity) as balance_qty  from subcon_ord_dtls a ,com_export_pi_dtls b where  a.id=work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.finish_qty ",'id','balance_qty');*/

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT a.id, a.order_quantity-sum(b.quantity) as balance_qty  from subcon_ord_dtls a ,com_export_pi_dtls b where  a.id=b.work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.order_quantity ",'id','balance_qty');

		//print_r($prev_pi_qnty_arr_dtls);
		$data_array=sql_select($sql);
		if(count($data_array) > 0)
		{
			foreach($data_array as $row)
			{
				$tblRow++;
				if($row[csf('main_process_id')]==1) $process_type=$wash_wet_process;
				else if($row[csf('main_process_id')]==2) $process_type=$wash_dry_process;
				else if($row[csf('main_process_id')]==3) $process_type=$wash_laser_desing;	
				if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$bal_qtny=$prev_pi_qnty_arr_dtls[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
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
						<input type="text" name="jobstyle_<? echo $tblRow; ?>" id="jobstyle_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('buyer_style_ref')]; ?>" style="width:110px" disabled="disabled"/>
					</td>
		            <td> 
						<input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('booking')]; ?>" style="width:110px" disabled="disabled"/>
					</td>
					<td>
						<input type="text" name="gsmItem_<? echo $tblRow; ?>" id="gsmItem_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $garments_item[$row[csf('gmts_item_id')]]; ?>" style="width:110px;" disabled="disabled"/>
						<input type="hidden" name="hideGsmItem_<? echo $tblRow; ?>" id="hideGsmItem_<? echo $tblRow; ?>" value="<? echo $row[csf('gmts_item_id')]; ?>"/>
					</td>
					<td>
		                <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
		            </td>
		            <td style="display:none"> 
						<input type="text" name="itemDesc_<? echo $tblRow; ?>" id="itemDesc_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('item_desc')]; ?>" style="width:70px" disabled="disabled"/>
					</td>
		            <td style="display:none">
		                <input type="text" name="processEmbl_<? echo $tblRow; ?>" id="processEmbl_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $wash_type[$row[csf('main_process_id')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideProcessEmbl_<? echo $tblRow; ?>" id="hideProcessEmbl_<? echo $tblRow; ?>" value="<? echo $row[csf('main_process_id')]; ?>"/>
		            </td>
		            <td style="display:none">
		                <input type="text" name="washType_<? echo $tblRow; ?>" id="washType_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $process_type[$row[csf('wash_type')]]; ?>" style="width:80px" disabled="disabled"/>
		                <input type="hidden" name="hideWashType_<? echo $tblRow; ?>" id="hideWashType_<? echo $tblRow; ?>" value="<? echo $row[csf('wash_type')]; ?>"/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('uom')],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny ?>" style="width:61px;" />
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
			?>
				<tr bgcolor="#E9F3FF" id="row_1" align="center">
                	<td>
                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                        <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                        <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                    </td>
					<td> 
						<input type="text" name="jobstyle_1" id="jobstyle_1" class="text_boxes" style="width:110px" disabled="disabled"/>
					</td>
                    <td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="gsmItem_1" id="gsmItem_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                     <td>
                        <input type="text" name="colorId_1" id="colorId_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td style="display:none">
                        <input type="text" name="itemDesc_1" id="itemDesc_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td style="display:none">
                        <input type="text" name="processEmbl_1" id="processEmbl_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td style="display:none">
                        <input type="text" name="washType_1" id="washType_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                     <td>
                        <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                </tr>
			<?
		}
	}

	else if ($item_category_id==36) // Gmts EMB
	{
		$tblRow=0;
		$sql = "SELECT id, work_order_no, work_order_id, work_order_dtls_id, booking, gmts_item_id,body_part, main_process_id, embl_type, item_desc, color_id, item_size, uom, quantity, rate, amount from com_export_pi_dtls where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0";

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT a.id, a.order_quantity-sum(b.quantity) as balance_qty  from subcon_ord_dtls a ,com_export_pi_dtls b where  a.id=b.work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.order_quantity ",'id','balance_qty');
		//print_r($prev_pi_qnty_arr_dtls);
		$data_array=sql_select($sql);
		if(count($data_array) > 0)
		{
			foreach($data_array as $row)
			{
				$tblRow++;
				$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
				if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$bal_qtny=$prev_pi_qnty_arr_dtls[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
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
						<input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('booking')]; ?>" style="width:110px" disabled="disabled"/>
					</td>
		            <td>
		                <input type="text" name="gsmItem_<? echo $tblRow; ?>" id="gsmItem_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $garments_item[$row[csf('gmts_item_id')]]; ?>" style="width:60px" disabled="disabled"/>
		                <input type="hidden" name="hideGsmItem_<? echo $tblRow; ?>" id="hideGsmItem_<? echo $tblRow; ?>" value="<? echo $row[csf('gmts_item_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="bodypart_<? echo $tblRow; ?>" id="bodypart_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $body_part[$row[csf('body_part')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideBodypart_<? echo $tblRow; ?>" id="hideBodypart_<? echo $tblRow; ?>" value="<? echo $row[csf('body_part')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="processEmbl_<? echo $tblRow; ?>" id="processEmbl_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $emblishment_name_array[$row[csf('main_process_id')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideProcessEmbl_<? echo $tblRow; ?>" id="hideProcessEmbl_<? echo $tblRow; ?>" value="<? echo $row[csf('main_process_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="emblType_<? echo $tblRow; ?>" id="emblType_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $type_array[$row[csf('embl_type')]][$row[csf('embl_type')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideEmblType_<? echo $tblRow; ?>" id="hideEmblType_<? echo $tblRow; ?>" value="<? echo $row[csf('embl_type')]; ?>"/>
		            </td>
		            <td> 
						<input type="text" name="itemDesc_<? echo $tblRow; ?>" id="itemDesc_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('item_desc')]; ?>" style="width:70px" disabled="disabled"/>
					</td>
					<td>
		                <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('item_size')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideitemSize_<? echo $tblRow; ?>" id="hideitemSize_<? echo $tblRow; ?>" value="<? echo $row[csf('item_size')]; ?>"/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('uom')],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;"/>
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly  value="<? echo $row[csf('id')]; ?>"/>
					</td>
				</tr>
				<?
			}
		}
		else
		{
			?>
				<tr bgcolor="#E9F3FF" id="row_1" align="center">
                	<td>
                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                        <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                        <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                    </td>
                    <td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="gsmItem_1" id="gsmItem_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="bodypart_1" id="bodypart_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="processEmbl_1" id="processEmbl_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="emblType_1" id="emblType_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>

                    <td>
                        <input type="text" name="itemDesc_1" id="itemDesc_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemColor_1" id="itemColor_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemSize_1" id="itemSize_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                     <td>
                        <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                </tr>
			<?
		}
	}

	else if ($item_category_id==45) // Trims
	{
		$tblRow=0;
		$sql = "SELECT id, work_order_no, work_order_id, work_order_dtls_id, hs_code, booking, gmts_item_id, item_desc, color_id, item_size, uom, quantity, rate, amount from com_export_pi_dtls where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0";

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT a.id, a.order_quantity-sum(b.quantity) as balance_qty  from subcon_ord_dtls a, com_export_pi_dtls b where  a.id=b.work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.order_quantity ",'id','balance_qty');
		//print_r($prev_pi_qnty_arr_dtls);
		$data_array=sql_select($sql);
		if(count($data_array) > 0)
		{
			foreach($data_array as $row)
			{
				$tblRow++;
				if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$bal_qtny=$prev_pi_qnty_arr_dtls[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
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
						<input type="text" name="hscode_<? echo $tblRow; ?>" id="hscode_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('hs_code')]; ?>" style="width:50px"/>
					</td>
		            <td> 
						<input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('booking')]; ?>" style="width:110px" disabled="disabled"/>
					</td>
		            <td>
		                <input type="text" name="itemGroup_<? echo $tblRow; ?>" id="itemGroup_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $item_group_arr[$row[csf('gmts_item_id')]]; ?>" style="width:60px" disabled="disabled"/>
		                <input type="hidden" name="hideGsmItem_<? echo $tblRow; ?>" id="hideGsmItem_<? echo $tblRow; ?>" value="<? echo $row[csf('gmts_item_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="itemDesc_<? echo $tblRow; ?>" id="itemDesc_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('item_desc')]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideitemDesc_<? echo $tblRow; ?>" id="hideitemDesc_<? echo $tblRow; ?>" value="<? echo $row[csf('item_desc')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('item_size')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideitemSize_<? echo $tblRow; ?>" id="hideitemSize_<? echo $tblRow; ?>" value="<? echo $row[csf('item_size')]; ?>"/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('uom')],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;"/>
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly  value="<? echo $row[csf('id')]; ?>"/>
					</td>
				</tr>
				<?
			}
		}
		else
		{
			?>
				<tr bgcolor="#E9F3FF" id="row_1" align="center">
                	<td>
                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                        <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                        <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                    </td>
                    <td> 
                        <input type="text" name="hscode_1" id="hscode_1" class="text_boxes" style="width:50px"/>
                    </td>
                    <td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemGroup_1" id="itemGroup_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemDesc_1" id="itemDesc_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemColor_1" id="itemColor_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemSize_1" id="itemSize_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                     <td>
                        <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                </tr>
			<?
		}
	}
    else if($item_category_id==67) // Sub con
    {
        $tblRow=0;
        $sql = "SELECT id, work_order_no, work_order_id, work_order_dtls_id, hs_code, booking, gmts_item_id, item_desc, color_id, item_size, uom, quantity, rate, amount from com_export_pi_dtls where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0";

        $prev_pi_qnty_arr_dtls=return_library_array("SELECT a.id, a.order_quantity-sum(b.quantity) as balance_qty  from subcon_ord_dtls a, com_export_pi_dtls b where  a.id=b.work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.order_quantity ",'id','balance_qty');
        //print_r($prev_pi_qnty_arr_dtls);
        $data_array=sql_select($sql);
        if(count($data_array) > 0)
        {
            foreach($data_array as $row)
            {
                $tblRow++;
                if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $bal_qtny=$prev_pi_qnty_arr_dtls[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
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
                        <input type="text" name="itemGroup_<? echo $tblRow; ?>" id="itemGroup_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $item_group_arr[$row[csf('gmts_item_id')]]; ?>" style="width:60px" disabled="disabled"/>
                        <input type="hidden" name="hideGsmItem_<? echo $tblRow; ?>" id="hideGsmItem_<? echo $tblRow; ?>" value="<? echo $row[csf('gmts_item_id')]; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="itemDesc_<? echo $tblRow; ?>" id="itemDesc_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('item_desc')]; ?>" style="width:70px" disabled="disabled"/>
                        <input type="hidden" name="hideitemDesc_<? echo $tblRow; ?>" id="hideitemDesc_<? echo $tblRow; ?>" value="<? echo $row[csf('item_desc')]; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:70px" disabled="disabled"/>
                        <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('item_size')]]; ?>" style="width:70px" disabled="disabled"/>
                        <input type="hidden" name="hideitemSize_<? echo $tblRow; ?>" id="hideitemSize_<? echo $tblRow; ?>" value="<? echo $row[csf('item_size')]; ?>"/>
                    </td>
                    <td>
                        <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('uom')],'',1,''); ?>
                    </td>
                    <td>
                        <input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
                        <input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;"/>
                    </td>
                    <td>
                        <input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
                    </td>
                    <td>
                        <input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly  value="<? echo $row[csf('id')]; ?>"/>
                    </td>
                </tr>
                <?
            }
        }
        else
        {
            $tblRow = 1;
            ?>
            <tr bgcolor="#E9F3FF" id="row_1" align="center">
                <td>
                    <input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
                </td>
                <td>
                    <input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />
                    <input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="" readonly />
                    <input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="" readonly />
                </td>
                <td>
                    <input type="text" name="itemGroup_<? echo $tblRow; ?>" id="itemGroup_<? echo $tblRow; ?>" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                </td>
                <td>
                    <input type="text" name="itemDesc_<? echo $tblRow; ?>" id="itemDesc_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:70px" disabled="disabled"/>
                </td>
                <td>
                    <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:70px" disabled="disabled"/>
                    <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value=""/>
                </td>
                <td>
                    <input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:70px" disabled="disabled"/>
                    <input type="hidden" name="hideitemSize_<? echo $tblRow; ?>" id="hideitemSize_<? echo $tblRow; ?>" value=""/>
                </td>
                <td>
                    <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>
                </td>
                <td>
                    <input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
                    <input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="" style="width:61px;"/>
                </td>
                <td>
                    <input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
                </td>
                <td>
                    <input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    <input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
                </td>
            </tr>
            <?
        }
    }
	else
	{
		echo "Develop Later";
	}
	
	exit();
}

//---------------------------------End Pi Details---------------------------------------------//

if( $action == 'catagory_wise_pi_details' ) // onchange catagory
{
	$data = explode( '_', $data );
	$company_id=$data[0];
	$item_category_id=$data[1];
	$type=$data[2];

	if($item_category_id!=0)
	{
		?>
    	<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
		<?
		// $item_category_id==2 || $item_category_id==3 || $item_category_id==4 || $item_category_id==10 || $item_category_id==20 || $item_category_id==22 || $item_category_id==24 || $item_category_id==30 || $item_category_id==31

        if($item_category_id==1 || $item_category_id==10) // Knit Garments and Fabric
        {
        	?>
        	<thead>
            	<th width="40">&nbsp;</th>
				<th>Job No</th>
				<th>Sales/Booking</th>
                <th class="must_entry_caption">Construction</th>
                <th>Composition</th>
                <th class="must_entry_caption">Color</th>					
                <th>GSM</th>
                <th class="must_entry_caption">Dia/Width</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
				<?
					if($item_category_id==10){
						?>
							<th width="100" title="Remarks copy when construction, composition, GSM are same">Remarks <input type="checkbox" checked id="copy_remarks_all" name="copy_remarks_all" /></th>
						<?
					}
				?>
            </thead>
            <tbody id="pi_details_container">
            	<tr bgcolor="#E9F3FF" id="row_1" align="center">
                	<td>
                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
						<input type="hidden" name="txtSerial[]" id="txtSerial_1" class="text_boxes" value="1" readonly/>
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                        <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                        <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                    </td>
					<td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:100px" disabled="disabled"/>
                    </td>
                    <td> 
                        <input type="text" name="construction_1" id="construction_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                        <input type="hidden" name="hideDeterminationId_1" id="hideDeterminationId_1" readonly />
                    </td>
                    <td>
                        <input type="text" name="composition_1" id="composition_1" class="text_boxes" value="" style="width:160px" disabled="disabled"/> 
                    </td> 
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:80px" disabled="disabled"/>
                        <input type="hidden" name="colorId_1" id="colorId_1"/>
                    </td>
                    <td>
                        <input type="text" name="gsm_1" id="gsm_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="diawidth_1" id="diawidth_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                     <td>
                        <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
					<?
						if($item_category_id==10){
							?>
								<td><input type="text" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" value="" style="width:100px;" onKeyUp="copy_remarks(1)" /></td>
							<?
						}
					?>
                </tr>
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
                    <td>Total&nbsp;</td>
                    <td style="text-align:center"><input type="text" name="txt_total_qnty" id="txt_total_qnty" class="text_boxes_numeric" value="" style="width:60px;" readonly/></td>
                    <td >&nbsp;</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    </td>
					<?
						if($item_category_id==10)
						{
							?><td>&nbsp;</td><?
						}
					?>
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
                    <td>&nbsp;</td>
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
                    </td>
					<?
						if($item_category_id==10)
						{
							?><td>&nbsp;</td><?
						}
					?>
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
                    <td>&nbsp;</td>
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
                    </td>
					<?
						if($item_category_id==10)
						{
							?><td>&nbsp;</td><?
						}
					?>
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
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    </td>
					<?
						if($item_category_id==10)
						{
							?><td>&nbsp;</td><?
						}
					?>
                </tr>
            </tfoot>
        	<?
        }

        else if($item_category_id==20 || $item_category_id==22) // Knitting, Dyeing and Finishing 
        {
        	?>
        	<thead>
            	<th width="40">&nbsp;</th>
				<th>Job No</th>
				<th>Booking No</th>
                <th class="must_entry_caption">Construction</th>
                <th>Composition</th>
                <th class="must_entry_caption">Color</th>					
                <th>GSM</th>
                <th class="must_entry_caption">Dia/Width</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
				<th width="100" title="Remarks copy when construction, composition, GSM are same">Remarks<input type="checkbox" checked id="copy_remarks_all" name="copy_remarks_all" /></th>
            </thead>
            <tbody id="pi_details_container">
            	<tr bgcolor="#E9F3FF" id="row_1" align="center">
                	<td>
                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
						<input type="hidden" name="txtSerial[]" id="txtSerial_1" class="text_boxes" value="1" readonly/>
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                        <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                        <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                    </td>
					<td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:100px" disabled="disabled"/>
                    </td>
                    <td> 
                        <input type="text" name="construction_1" id="construction_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                        <input type="hidden" name="hideDeterminationId_1" id="hideDeterminationId_1" readonly />
                    </td>
                    <td>
                        <input type="text" name="composition_1" id="composition_1" class="text_boxes" value="" style="width:160px" disabled="disabled"/> 
                    </td> 
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:80px" disabled="disabled"/>
                        <input type="hidden" name="colorId_1" id="colorId_1"/>
                    </td>
                    <td>
                        <input type="text" name="gsm_1" id="gsm_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="diawidth_1" id="diawidth_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                     <td>
                        <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
					<td><input type="text" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" value="" style="width:100px;" onKeyUp="copy_remarks(1)" /></td>
                </tr>
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
                    <td>Total&nbsp;</td>
                    <td style="text-align:center"><input type="text" name="txt_total_qnty" id="txt_total_qnty" class="text_boxes_numeric" value="" style="width:60px;" readonly/></td>
                    <td >&nbsp;</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
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
                    <td>&nbsp;</td>
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <td>&nbsp;</td>
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    </td>
					<td>&nbsp;</td>
                </tr>
            </tfoot>
        	<?
        }

        else if($item_category_id==35) // Gmts Printing
		{
			?>
        	<thead>
            	<th width="40">&nbsp;</th>
				<th>Job No</th>
                <th class="must_entry_caption">Sales/Booking No</th>					
                <th>Gmts. Item</th>
                <th>Color</th>
                <th style="display:none">Embl Desc</th>
                <th style="display:none">Process</th>
                <th class="must_entry_caption" style="display:none">Embl Type</th>
                <th>Order UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
            </thead>
            <tbody id="pi_details_container">
            	<tr bgcolor="#E9F3FF" id="row_1" align="center">
                	<td>
                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                        <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                        <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                    </td>
                    <td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                    </td>
                    <td >
                        <input type="text" name="gsmItem_1" id="gsmItem_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="colorId_1" id="colorId_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td style="display:none">
                        <input type="text" name="itemDesc_1" id="itemDesc_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td style="display:none">
                        <input type="text" name="processEmbl_1" id="processEmbl_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td style="display:none">
                        <input type="text" name="emblType_1" id="emblType_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                     <td>
                        <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                </tr>
            </tbody>
            <tfoot class="tbl_bottom">
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td>Total&nbsp;</td>
                    <td style="text-align:center"><input type="text" name="txt_total_qnty" id="txt_total_qnty" class="text_boxes_numeric" value="" style="width:60px;" readonly/></td>
                    <td >&nbsp;</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    </td>
                </tr>
            </tfoot>
        	<?
        }

        else if($item_category_id==23) // All Over Printing (AOP)
		{
        	?>
        	<thead>
            	<th width="40">&nbsp;</th>
				<th>Job No</th>
				<th>Booking No</th>
                <th>Gmts Color</th>
                <th>AOP Color</th>
                <th>GSM</th>
                <th>Body part</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Amount</th>
            </thead>
            <tbody id="pi_details_container">
            	<tr bgcolor="#E9F3FF" id="row_1" align="center">
                	<td>
                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                        <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                        <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                    </td>
                    <td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:80px" disabled="disabled"/>
                        <input type="hidden" name="colorId_1" id="colorId_1"/>
                    </td>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:80px" disabled="disabled"/>
                        <input type="hidden" name="colorId_1" id="colorId_1"/>
                    </td>
                    <td>
                        <input type="text" name="gsm_1" id="gsm_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="bodypart_1" id="bodypart_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                     <td>
                        <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                </tr>
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
                    <td>Total&nbsp;</td>
                    <td style="text-align:center"><input type="text" name="txt_total_qnty" id="txt_total_qnty" class="text_boxes_numeric" value="" style="width:60px;" readonly/></td>
                    <td >&nbsp;</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
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
                    <td>&nbsp;</td>
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <td>&nbsp;</td>
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    </td>
                </tr>
            </tfoot>
        	<?
        }

        else if($item_category_id==37) // Gmts Washing
		{
			?>
        	<thead>
            	<th width="40">&nbsp;</th>
				<th>Wash Job No</th>
                <th>Style No</th>
                <th class="must_entry_caption">Sales/Booking No</th>					
                <th>Gmts. Item</th>
                <th>Color</th>
                <th style="display:none">Wash Desc</th>
                <th style="display:none">Process</th>
                <th class="must_entry_caption" style="display:none">Wash Type</th>
                <th>Order UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
            </thead>
            <tbody id="pi_details_container">
            	<tr bgcolor="#E9F3FF" id="row_1" align="center">
                	<td>
                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                        <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                        <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                    </td>
                    <td> 
                        <input type="text" name="jobstyle_1" id="jobstyle_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                    </td>
                    <td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                    </td>
                    <td >
                        <input type="text" name="gsmItem_1" id="gsmItem_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="colorId_1" id="colorId_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td style="display:none">
                        <input type="text" name="itemDesc_1" id="itemDesc_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td style="display:none">
                        <input type="text" name="processEmbl_1" id="processEmbl_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td style="display:none">
                        <input type="text" name="washType_1" id="washType_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                     <td>
                        <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                </tr>
            </tbody>
            <tfoot class="tbl_bottom">
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td>Total&nbsp;</td>
                    <td style="text-align:center"><input type="text" name="txt_total_qnty" id="txt_total_qnty" class="text_boxes_numeric" value="" style="width:60px;" readonly/></td>
                    <td >&nbsp;</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td style="display:none">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    </td>
                </tr>
            </tfoot>
        	<?
        }

        else if($item_category_id==36) // Gmts Emb view
		{
			?>
        	<thead>
            	<th width="40">&nbsp;</th>
				<th>Job No</th>
                <th class="must_entry_caption">Sales/Booking No</th>					
                <th>Gmts. Item</th>
                <th>Body Part</th>
                <th class="must_entry_caption">Process /Embl. Name</th>
                <th>Embl. Type</th>
                <th>Embl.Description</th>
                <th>Color</th>
                <th>GMTS Size</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
            </thead>
            <tbody id="pi_details_container">
            	<tr bgcolor="#E9F3FF" id="row_1" align="center">
                	<td>
                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                        <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                        <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                    </td>
                    <td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="gsmItem_1" id="gsmItem_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="bodypart_1" id="bodypart_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="processEmbl_1" id="processEmbl_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="emblType_1" id="emblType_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemDesc_1" id="itemDesc_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="colorId_1" id="colorId_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemSize_1" id="itemSize_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                     <td>
                        <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                </tr>
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
                    <td>&nbsp;</td>
                    <td>Total&nbsp;</td>
                    <td style="text-align:center"><input type="text" name="txt_total_qnty" id="txt_total_qnty" class="text_boxes_numeric" value="" style="width:60px;" readonly/></td>
                    <td >&nbsp;</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    </td>
                </tr>
            </tfoot>
        	<?
        }
        else if($item_category_id==67) // Inbound Sub Contract
        {
            ?>
            <thead>
            <th width="40">&nbsp;</th>
            <th>Job No</th>
            <th>Item Group</th>
            <th>Item Description</th>
            <th class="must_entry_caption">Item Color</th>
            <th>Item Size</th>
            <th>Order UOM</th>
            <th class="must_entry_caption">Order Qty</th>
            <th class="must_entry_caption">Rate</th>
            <th>Amount</th>
            </thead>
            <tbody id="pi_details_container">
            <tr bgcolor="#E9F3FF" id="row_1" align="center">
                <td>
                    <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                </td>
                <td>
                    <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />
                    <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                    <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                </td>
                <td>
                    <input type="text" name="itemGroup_1" id="itemGroup_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                </td>
                <td>
                    <input type="text" name="itemDesc_1" id="itemDesc_1" class="text_boxes" value="" style="width:110px"  disabled="disabled"/>
                </td>
                <td>
                    <input type="text" name="itemColor_1" id="itemColor_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                </td>
                <td>
                    <input type="text" name="itemSize_1" id="itemSize_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                </td>
                <td>
                    <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>
                </td>
                <td>
                    <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                </td>
                <td>
                    <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                </td>
                <td>
                    <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                </td>
            </tr>
            </tbody>
            <tfoot class="tbl_bottom">
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>

                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Total&nbsp;</td>
                <td style="text-align:center"><input type="text" name="txt_total_qnty" id="txt_total_qnty" class="text_boxes_numeric" value="" style="width:60px;" readonly/></td>
                <td >&nbsp;</td>
                <td style="text-align:center">
                    <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
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
                    <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                </td>
            </tr>
            </tfoot>
            <?
            }

        else if($item_category_id==45) // Gmts Trims
		{
			?>
        	<thead>
            	<th width="40">&nbsp;</th>
				<th>Job No</th>
				<th>HS Code</th>
                <th class="must_entry_caption">Wo No</th>
                <th>Item Group</th>
                <th>Item Description</th>
                <th class="must_entry_caption">Item Color</th>
                <th>Item Size</th>
                <th>Order UOM</th>
                <th class="must_entry_caption">Order Qty</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
            </thead>
            <tbody id="pi_details_container">
            	<tr bgcolor="#E9F3FF" id="row_1" align="center">
                	<td>
                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" value="" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
                        <input type="hidden" name="hideWoId_1" id="hideWoId_1" readonly />
                        <input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1" readonly />
                    </td>
                    <td> 
                        <input type="text" name="hscode_1" id="hscode_1" class="text_boxes" style="width:50px"/>
                    </td>
                    <td> 
                        <input type="text" name="salesbooking_1" id="salesbooking_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemGroup_1" id="itemGroup_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemDesc_1" id="itemDesc_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemColor_1" id="itemColor_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemSize_1" id="itemSize_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                     <td>
                        <? echo create_drop_down( "uom_1", 70, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>						 
                    </td>
                    <td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                    </td>
                    <td>
                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                    </td>
                    <td>
                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                    </td>	
                </tr>
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
                    <td>Total&nbsp;</td>
                    <td style="text-align:center"><input type="text" name="txt_total_qnty" id="txt_total_qnty" class="text_boxes_numeric" value="" style="width:60px;" readonly/></td>
                    <td >&nbsp;</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    </td>
                </tr>
            </tfoot>
        	<?
        }

        else
        {
        	echo "Develop Later";
        }

		?>
	    </table>
	    <table style="margin-top:5px" width="100%">
       		<tr>
            	<td valign="top" width="20%"><input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/> Check / Uncheck All</td>
            	<td valign="top" width="80%" colspan="5" align="center" class="button_container">
                	<? echo load_submit_buttons( $_SESSION['page_permission'], "fnc_pi_item_details", 0,0 ,"reset_form('pimasterform_2','','','','$(\'#tbl_pi_item tbody tr:not(:first)\').remove();')",2) ; ?>
                </td>
            </tr>
        </table>
	    <?
	}	
	exit();
}

if ($action=="wo_popup")
{
	echo load_html_head_contents("Sales/Booking No. Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $exporter_id."**".$within_group."**".$buyer_name; die;
	//$previous_wo_ids=$prev_wo_nums=$prev_deters=$prev_colors=$prev_constructs=$prev_compositions=$prev_gsms=$prev_widths=$prev_uoms="";
	
	?> 		
	<script>
	
		function load_buyer(exporter_id,within_group,buyer_name)
		{
			load_drop_down( 'export_pi_controller',exporter_id+'_'+within_group+'_'+buyer_name, 'load_drop_down_buyer', 'buyer_td' );
		}

		var selected_id = new Array;
		
	 	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				if($("search"+i).css('display') != 'none')
				{
					js_set_value( i );
				}
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
			//alert(str);//return;
			if($("search"+str).css('display') != 'none')
			{
				toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
				if( jQuery.inArray( $('#txt_wo_id_dtls' + str).val(), selected_id ) == -1 ) {
					selected_id.push( $('#txt_wo_id_dtls' + str).val() );
					selected_mst_id.push( $('#txt_wo_id' + str).val() );
					//based_on.push( $('#txt_wo_id' + str).val() );
				}
				else 
				{
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == $('#txt_wo_id_dtls' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
				}
				var id = ''; var mst_id = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					mst_id += selected_mst_id[i] + ',';
				}
				id = id.substr( 0, id.length - 1 );
				mst_id = mst_id.substr( 0, mst_id.length - 1 );
				
				$('#txt_selected_wo_id').val( id );
				$('#txt_selected_wo_mst_id').val( mst_id );
			}
		}
	
		function reset_hide_field()
		{
			$('#txt_selected_wo_id').val( '' );
			$('#txt_selected_wo_mst_id').val( '' );
			selected_id = new Array();
			selected_mst_id = new Array();
		}
    </script>

	</head>

	<body onLoad="load_buyer(<? echo $exporter_id;?>,<? echo $within_group;?>,<? echo $buyer_name;?>)">
	<div align="center" style="width:98%;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:98%; margin-left:2px">
			<legend>Enter search words</legend>           
	            <table cellpadding="0" cellspacing="0" width="980" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<th>Within Group</th>
                        <?
                        if(str_replace("'","", $item_category_id) != 67){
                        ?>
	                    <th><?if(str_replace("'","", $item_category_id)==10 || str_replace("'","", $item_category_id)==20 || str_replace("'","", $item_category_id)==22 || str_replace("'","", $item_category_id)==67){echo "Customer/Buyer";}else{echo "Buyer";}?></th>
                        <?
                        }
                        ?>
                        <th>Year</th>
                        <?
                        if(str_replace("'","", $item_category_id) != 67){
                        ?>
	                    <th><? if(str_replace("'","", $item_category_id)==1 || str_replace("'","", $item_category_id)==10){ echo "Sales/Booking No."; } elseif(str_replace("'","", $item_category_id)==37) { echo "Wash Job No."; } else { echo "WO No"; } ?></th>
                        <th>Buyer Style</th>
                        <?
                        }
                        ?>
	                    <th><? if(str_replace("'","", $item_category_id)==1 || str_replace("'","", $item_category_id)==10) {echo "Sales Order";} elseif(str_replace("'","", $item_category_id)==37) { echo "Booking No"; } else {echo "Job";}?></th>
	                    <th><? if(str_replace("'","", $item_category_id)==1 || str_replace("'","", $item_category_id)==10){ echo "Sales/Booking"; } elseif(str_replace("'","", $item_category_id)==37) { echo "Wash"; } else { echo "Booking"; } ?> Date Range</th>
                        <?
                        if(str_replace("'","", $item_category_id) != 67){
                        ?>
                        <th>Based on</th>
                        <?
                        }
                        ?>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" onClick="reset_hide_field();" />
	                    	<input type="hidden" name="txt_selected_wo_id" id="txt_selected_wo_id" class="text_boxes" value=""> 
	                    	<input type="hidden" name="txt_selected_wo_mst_id" id="txt_selected_wo_mst_id" class="text_boxes" value="">
	                    	<input type="hidden" name="txt_item_category" id="txt_item_category" class="text_boxes" style="width:70px" value="<? echo $item_category_id; ?>">
	                    </th> 
	                </thead>
	                <tr class="general">
	                	<td>
							<? echo create_drop_down( "cbo_within_group", 70, $yes_no,"", 0, "-- Select --",$within_group, "load_drop_down( 'export_pi_controller',$exporter_id+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );",1 ); ?>
	                    </td>
                        <?
                        if(str_replace("'","", $item_category_id) != 67){
                        ?>
	                    <td id="buyer_td"> 
							<? echo create_drop_down( "cbo_buyer_name", 151, $blank_array,"", 1, "-- Select Buyer --", 0, "",0 ); ?>
	                    </td>
                        <?
                        }else{
                        ?>
                            <input type="hidden" name="cbo_buyer_name" id="cbo_buyer_name" value="">
                        <?
                        }
                        ?>
                        <td> 
							<?
							$cu_year=date("Y");
							//echo $cu_year;die;
							echo create_drop_down( "cbo_year", 80, $year,"", 1, "-- All Year--", $cu_year, "",0 ); 
							?>
	                    </td>
	                    <?
                        if(str_replace("'","", $item_category_id) != 67){
                        ?>
	                    <td> 
	                        <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:100px">
	                    </td>
                        <td> 
	                        <input type="text" name="txt_buyer_style" id="txt_buyer_style" class="text_boxes" style="width:100px">
	                    </td>
                        <?
                        }else{
                            ?>
                            <input type="hidden" name="txt_wo_no" id="txt_wo_no" value="">
                            <input type="hidden" name="txt_buyer_style" id="txt_buyer_style" value="">
                            <?
                        }
                        ?>
	                    <td> 
	                        <input type="text" name="txt_sales_no" id="txt_sales_no" class="text_boxes" style="width:100px">
	                    </td>						
	                    <td align="center">
	                      <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px">To
						  <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px">
						</td>
                        <?
                        if (str_replace("'", "", $item_category_id) != 67) {
                        ?>
						<td> 
	                    <?


                            if (str_replace("'", "", $item_category_id) == 35 || str_replace("'", "", $item_category_id) == 37) {
                                $wo_based_on = array(1 => "Work Orde Wise");
                                echo create_drop_down("cbo_based_on", 80, $wo_based_on, "", 1, "Select", 1, "", 1);
                            } else {
                                $wo_based_on = array(1 => "Work Orde Wise", 2 => "Item Wise");
                                echo create_drop_down("cbo_based_on", 80, $wo_based_on, "", 1, "Select", 1, "");
                            }

						?>
	                    </td>
                            <?
                        }else{
                            ?>
                            <input type="hidden" name="cbo_based_on" id="cbo_based_on" value="">
                            <?
                        }
                        ?>
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_wo_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $within_group; ?>+'_'+<? echo $buyer_name; ?>+'_'+<? echo $exporter_id; ?>+'_'+document.getElementById('txt_sales_no').value+'_'+document.getElementById('cbo_based_on').value+'_'+'<? echo $prev_wo_ids; ?>'+'_'+'<? echo $previous_wo_ids; ?>'+'_'+'<? echo $item_category_id; ?>'+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_buyer_style').value+'_'+'<? echo $curr_wo_id; ?>'+'_'+'<? echo $curr_quantity; ?>'+'_'+'<? echo $curr_amount; ?>', 'create_wo_search_list_view', 'search_div', 'export_pi_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:80px;" />
	                     	
	                    </td>
	                </tr>
	                <tr>
	                	<td colspan="9" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px; margin-left:5px" id="search_div" align="center"></div> 
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_wo_search_list_view")
{
	$data = explode("_",$data);
//	print_r($data);
	$within_group =$data[3];
	$buyer_id =$data[4];
	$company_id =$data[5];

	$selected_based_on=$data[7];	
	$prev_wo_ids=$data[9];
	$item_category = $data[10];
	$year = $data[11];
	$buyer_style = trim($data[12]);
	$curr_wo_id = explode(',',$data[13]);
	$curr_quantity = explode(',',$data[14]);
	$curr_amount = explode(',',$data[15]);
	$prev_wo_all=implode(",",array_unique(explode(",",$data[8])));
	$curr_wo_id_all=implode(",",array_unique(explode(",",$data[13])));

	if($selected_based_on!=2) 
	{
		if ($curr_wo_id_all != '') {$wo_without= " and a.id not in(".$curr_wo_id_all.")";}else{$wo_without='';}
	}
	else
	{
		if ($prev_wo_all != '') 
		{
			if($item_category==36){$wo_without= " and d.id not in(".$prev_wo_all.")";}
			if($item_category==1 || $item_category==10 || $item_category==20 || $item_category==23){$wo_without= " and b.id not in(".$prev_wo_all.")";}
			if($item_category==45){$wo_without= " and c.id not in(".$prev_wo_all.")";}
		}else{$wo_without='';}
	}

	$curr_data=array();
	for($i=0;$i<count($curr_wo_id);$i++)
	{
		$curr_data[$curr_wo_id[$i]]['quantity']+=$curr_quantity[$i];
		$curr_data[$curr_wo_id[$i]]['amount']+=$curr_amount[$i];
	}
	// var_dump($curr_data);
	//echo $buyer_style.t4st;die;
	if($item_category_id==10)
	{
		$prev_wo_mst_ids=$data[10];
		$prev_dtls_data_arr=array();
		
	}
	//print_r($prev_dtls_data_arr);die;
	
	if($company_id==0) { echo "Please Select Company First."; die; }
	
	///////////////////////////////////////////
	$buyer_id_cond=$wo_number='';
	$sales_order=$wo_date_cond=$cbo_year='';
	
	if($item_category==1 || $item_category==10)
	{
		if($buyer_id != 0) $buyer_id_cond=" and a.buyer_id=$buyer_id";
		if (trim($data[0]) != '') $wo_number=" and a.sales_booking_no like '%".trim($data[0])."'";
		if (trim($data[6]) != '') $sales_order=" and a.job_no like '%".trim($data[6])."'";
		if ($data[1] != '' &&  $data[2] != '')
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
		
		if($year)
		{
			if($db_type==0)
			{
				$cbo_year=" and YEAR(a.booking_date) =$year";
			}
			else
			{	
				$cbo_year=" and to_char(a.booking_date,'YYYY') =$year";
			}
		}
	}
	else if($item_category==20 || $item_category==22) // Knitting, Dyeing and Finishing
	{
		if($buyer_id != 0) $buyer_id_cond=" and a.company_id=$buyer_id";	
		if (trim($data[0]) != '') $wo_number=" and b.booking_no like '%".trim($data[0])."'";
		if (trim($data[6]) != '') $sales_order=" and b.job_no like '%".trim($data[6])."'";
		if ($data[1] != '' &&  $data[2] != '')
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
		
		if($year)
		{
			if($db_type==0)
			{
				$cbo_year=" and YEAR(a.booking_date) =$year";
			}
			else
			{	
				$cbo_year=" and to_char(a.booking_date,'YYYY') =$year";
			}
		}
	}
	else if($item_category==23|| $item_category==45 || $item_category==36)
	{
		if($buyer_id != 0) $buyer_id_cond=" and a.party_id=$buyer_id";
		// if (trim($data[0]) != '') $wo_number=" and a.subcon_job like '%".trim($data[0])."'";
		if (trim($data[0]) != '') $wo_number=" and a.order_no like '%".trim($data[0])."'";
		if (trim($data[6]) != '') $sales_order=" and b.job_no_mst like '%".trim($data[6])."'";
		if ($buyer_style != '') $sales_order.=" and b.buyer_style_ref='".$buyer_style."'";

		if ($data[1] != '' &&  $data[2] != '')
		{
			if($db_type==0)
			{
				$wo_date_cond = "and a.receive_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; 
			}
			else
			{
				$wo_date_cond = "and a.receive_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; 
			}
		}
		
		if($year)
		{
			if($db_type==0)
			{
				$cbo_year=" and YEAR(a.receive_date) =$year";
			}
			else
			{	
				$cbo_year=" and to_char(a.receive_date,'YYYY') =$year";
			}
		}
	}
    else if($item_category==67) //Inbound Sub Con
    {

        if (trim($data[6]) != '') $sales_order=" and b.job_no_mst like '%".trim($data[6])."'";


        if ($data[1] != '' &&  $data[2] != '')
        {
            if($db_type==0)
            {
                $wo_date_cond = "and a.insert_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'";
            }
            else
            {
                $wo_date_cond = "and a.insert_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'";
            }
        }

        if($year)
        {
            if($db_type==0)
            {
                $cbo_year=" and YEAR(a.insert_date) = $year";
            }
            else
            {
                $cbo_year=" and to_char(a.insert_date,'YYYY') = $year";
            }
        }
    }
	else if($item_category==37)
	{
		if($buyer_id != 0) $buyer_id_cond=" and a.party_id=$buyer_id";
		if (trim($data[0]) != '') $wo_number=" and a.subcon_job like '%".trim($data[0])."'";
		if (trim($data[6]) != '') $sales_order=" and a.order_no like '%".trim($data[6])."'";
		if ($buyer_style != '') $sales_order.=" and b.buyer_style_ref='".$buyer_style."'";
		if ($data[1] != '' &&  $data[2] != '')
		{
			if($db_type==0)
			{
				$wo_date_cond = "and a.receive_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; 
			}
			else
			{
				$wo_date_cond = "and a.receive_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; 
			}
		}
		
		if($year)
		{
			if($db_type==0)
			{
				$cbo_year=" and YEAR(a.receive_date) =$year";
			}
			else
			{	
				$cbo_year=" and to_char(a.receive_date,'YYYY') =$year";
			}
		}
	}
	else if($item_category==35) // Gmts Printing condition
	{
		if($buyer_id != 0) $buyer_id_cond=" and a.party_id=$buyer_id";
		if (trim($data[0]) != '') $wo_number=" and a.embellishment_job like '%".trim($data[0])."'";
		if (trim($data[6]) != '') $sales_order=" and b.job_no_mst like '%".trim($data[6])."'";
		if ($buyer_style != '') $sales_order.=" and b.buyer_style_ref='".$buyer_style."'";
		if ($data[1] != '' &&  $data[2] != '')
		{
			if($db_type==0)
			{
				$wo_date_cond = "and a.receive_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; 
			}
			else
			{
				$wo_date_cond = "and a.receive_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; 
			}
		}
		
		if($year)
		{
			if($db_type==0)
			{
				$cbo_year=" and YEAR(a.receive_date) =$year";
			}
			else
			{	
				$cbo_year=" and to_char(a.receive_date,'YYYY') =$year";
			}
		}
	}

	$within_group_cond='';
	if($within_group != 0) $within_group_cond=" and a.within_group=$within_group";
	
	
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4 and status_active=1",'id','item_name');
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
	//echo $item_category;die;
	if($item_category==1 || $item_category==10)
	{
		$sql = "SELECT a.id as mst_id, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, b.id as dtls_id, b.fabric_desc, b.gsm_weight, b.dia, b.color_id, b.grey_qnty_by_uom, b.pp_qnty, b.mtl_qnty, b.fpt_qnty, b.gpt_qnty, b.cons_uom from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without order by a.id";
		// echo $sql;die;
		$prev_pi_qnty_arr=return_library_array("SELECT work_order_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_id",'work_order_id','quantity');

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table">
			<thead>
				<tr>
					<th colspan="11"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="120">Job No</th>
					<th width="105">Sales/Booking No</th>
					<th width="65">WO Date</th>
					<th width="50"><?if($item_category==10){echo "Customer/Buyer";}else{echo "Buyer";}?></th>
					<th width="80">Color</th>
					<th width="200">Fabric Description</th>
					<th width="40">GSM</th>
					<th width="40">Dia/ Width</th>
					<th width="70">Balance Qty</th>
					<th>UOM</th>
				</tr>
			</thead>
		</table>
		<div style="width:940px; max-height:250px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="920" class="rpt_table" id="tbl_list_search">
				<? 
				$i=1; $job_id_arr =array(); $without_order_arr =array();
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					if($row[csf('within_group')]==2) $buyer=$buyer_arr[$row[csf('buyer_id')]];
					else $buyer=$company_arr[$row[csf('buyer_id')]];
					$bal_qtny=0; $is_loop=1;
					if($selected_based_on==1)
					{
						if (in_array($row[csf('mst_id')], $job_id_arr))
						{
						  	$is_loop=2;
						}

						$bal_qtny=$row[csf('grey_qnty_by_uom')]+$row[csf('pp_qnty')]+$row[csf('mtl_qnty')]+$row[csf('fpt_qnty')]+$row[csf('gpt_qnty')]-$prev_pi_qnty_arr[$row[csf('mst_id')]];

					}
					else
					{
						$bal_qtny=$row[csf('grey_qnty_by_uom')]+$row[csf('pp_qnty')]+$row[csf('mtl_qnty')]+$row[csf('fpt_qnty')]+$row[csf('gpt_qnty')]-$prev_pi_qnty_arr_dtls[$row[csf('dtls_id')]];
					}
					//echo $bal_qtny."**";

					if($bal_qtny>0 && $is_loop==1)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
			                <td width="40" align="center"><? echo $i; ?>
			                    <input type="hidden" name="txt_wo_id_dtls" id="txt_wo_id_dtls<? echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
			                    <input type="hidden" name="txt_wo_id" id="txt_wo_id<? echo $i ?>" value="<? echo $row[csf('mst_id')]; ?>"/>	
			                </td>	
							<td width="120"><p><? echo $row[csf('job_no')];?></p></td>
			                <td width="105"><p><? echo $row[csf('sales_booking_no')];?></p></td>
							<td width="65" align="center"><p><? if($row[csf('booking_date')]!="" && $row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]);?></p></td>
							<td width="80"><p><? echo $buyer; ?>&nbsp;</p></td> 
							<td width="80"><p><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</p></td>
							<td width="200"><p><? echo $row[csf('fabric_desc')]; ?>&nbsp;</p></td>
							<td width="40"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
							<td width="40"><p><? echo $row[csf('dia')]; ?>&nbsp;</p></td>
							<td width="70" align="right"><p><? echo number_format($bal_qtny,2,".",""); ?>&nbsp;</p></td>
							<td align="center"><p><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</p></td>
						</tr>
				 		<?
				 		$i++;
					}
				 	$job_id_arr[]=$row[csf('mst_id')];		 
				}
				?>
			</table>
		</div>
		<?
	}

	else if($item_category==20 || $item_category==22 ) // knitting, Dyeing and Finishing
	{
		//$sql="SELECT a.id as mst_id, a.booking_no, a.booking_date, a.company_id, a.buyer_id, b.id as dtls_id, b.job_no, b.fabric_color_id as color_id, b.fin_gsm as gsm, b.fin_dia as dia, b.uom, b.wo_qnty, d.fabric_description as fabric_desc from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=d.id and a.booking_type=3 and a.item_category=12 and a.entry_form=228 and b.entry_form_id=228 and b.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $wo_number $wo_date_cond $cbo_year $buyer_id_cond $sales_order $wo_without order by a.id";
		if ($item_category==20)
		{
			$sql="SELECT a.id as mst_id, a.booking_no, a.booking_date, a.company_id, a.buyer_id, b.id as dtls_id, b.job_no, b.uom, b.wo_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=3 and a.item_category=12 and a.pay_mode=5 and a.entry_form=228 and b.entry_form_id=228 and b.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $cbo_year $buyer_id_cond $sales_order $wo_without order by a.id";
		}
		else
		{
			$sql="SELECT a.id as mst_id, a.booking_no, a.booking_date, a.company_id, a.buyer_id, b.id as dtls_id, b.job_no, b.uom, b.wo_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=3 and a.item_category=12 and a.pay_mode=5 and a.entry_form=229 and b.entry_form_id=229 and b.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $cbo_year $buyer_id_cond $sales_order $wo_without order by a.id";
		}	
		
		$sql_res=sql_select($sql);
		$nameArray=array();
		foreach ($sql_res as $row) 
		{
			$nameArray[$row[csf('mst_id')]]['mst_id']=$row[csf('mst_id')];
			$nameArray[$row[csf('mst_id')]]['booking_no']=$row[csf('booking_no')];
			$nameArray[$row[csf('mst_id')]]['booking_date']=$row[csf('booking_date')];
			$nameArray[$row[csf('mst_id')]]['company_id']=$row[csf('company_id')];
			$nameArray[$row[csf('mst_id')]]['buyer_id']=$row[csf('buyer_id')];
			$nameArray[$row[csf('mst_id')]]['dtls_id'].=$row[csf('dtls_id')].',';
			$nameArray[$row[csf('mst_id')]]['job_no'].=$row[csf('job_no')].',';
			$nameArray[$row[csf('mst_id')]]['uom'].=$row[csf('uom')].',';
			$nameArray[$row[csf('mst_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
		}
		//echo '<pre>';print_r($nameArray);
		//echo $sql;die;
		$prev_pi_qnty_arr=return_library_array("SELECT work_order_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_id",'work_order_id','quantity');

		//$prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');
		?>
		<div style="width:700px;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table">
			<thead>
				<tr>
					<th colspan="7"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="150">Job No</th>
					<th width="120">Booking No</th>
					<th width="80">WO Date</th>
					<th width="120">Customer/Buyer</th>
					<th width="80">UOM</th>
					<th>Balance Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:700px; max-height:250px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="680" class="rpt_table" id="tbl_list_search">
				<? 
				$i=1; $job_id_arr =array(); $without_order_arr =array();
				//$nameArray=sql_select( $sql );
				foreach ($nameArray as $mst_id=>$row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					if($row['within_group']==2) $buyer=$buyer_arr[$row['buyer_id']];
					else $buyer=$company_arr[$row['company_id']];
					$job_no = implode(',',array_unique(explode(',', rtrim($row['job_no'],','))));
					$uom = implode(',',array_unique(explode(',', rtrim($row['uom'],','))));
					$bal_qtny=0; $is_loop=1;
					$bal_qtny=$row['wo_qnty']-$prev_pi_qnty_arr[$row['mst_id']];
					//echo $bal_qtny."**";

					if($bal_qtny>0)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
			                <td width="40" align="center"><? echo $i; ?>
			                    <input type="hidden" name="txt_wo_id_dtls" id="txt_wo_id_dtls<? echo $i ?>" value="<? echo rtrim($row['dtls_id'],','); ?>"/>
			                    <input type="hidden" name="txt_wo_id" id="txt_wo_id<? echo $i ?>" value="<? echo $row['mst_id']; ?>"/>	
			                </td>	
							<td width="150"><p><? echo $job_no;?></p></td>
			                <td width="120"><p><? echo $row['booking_no'];?></p></td>
							<td width="80" align="center"><p><? if($row['booking_date']!="" && $row['booking_date']!="0000-00-00") echo change_date_format($row['booking_date']);?></p></td>
							<td width="120"><p><? echo $buyer; ?>&nbsp;</p></td>
							<td align="center" width="80"><p><? echo $unit_of_measurement[$uom]; ?>&nbsp;</p></td>
							<td align="right"><p><? echo number_format($bal_qtny,2,".",""); ?>&nbsp;</p></td>							
						</tr>
				 		<?
				 		$i++;
					}
				 	$job_id_arr[]=$row[csf('mst_id')];
				}
				?>
			</table>
		</div>
		</div>
		<?
	}

	else if($item_category==35) // Gmts Printing view
	{
		if($selected_based_on==1)
		{
			if ($db_type==0) 
			{
				$sql="SELECT a.id as MST_ID, b.JOB_NO_MST, a.WITHIN_GROUP, a.order_no as SALES_BOOKING_NO,
				b.id as DTLS_ID, sum(b.order_quantity) as QUANTITY, sum(b.amount) as AMOUNT, b.ORDER_UOM, b.GMTS_ITEM_ID, b.EMBL_TYPE
				FROM subcon_ord_mst a, subcon_ord_dtls b
				WHERE a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=204 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
				GROUP by a.id, b.job_no_mst, a.within_group, a.order_no, b.order_uom, b.gmts_item_id, b.id, b.embl_type
				ORDER by a.id ";
			} 
			else
			{
				$sql="SELECT a.id as MST_ID, b.JOB_NO_MST, a.WITHIN_GROUP, a.order_no as SALES_BOOKING_NO,
				b.id as DTLS_ID, sum(b.order_quantity) as QUANTITY, sum(b.amount) as AMOUNT, b.ORDER_UOM, b.GMTS_ITEM_ID, b.EMBL_TYPE
				FROM subcon_ord_mst a, subcon_ord_dtls b
				WHERE a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=204 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
				GROUP by a.id, b.job_no_mst, a.within_group, a.order_no, b.order_uom, b.gmts_item_id, b.id, b.embl_type
				ORDER by a.id";
			}
			

			$sql_prev_pi=sql_select("SELECT WORK_ORDER_DTLS_ID, sum(quantity) as QUANTITY, sum(amount) as AMOUNT from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id");
			$prev_data=array();
			foreach($sql_prev_pi as $row)
			{
				$prev_data[$row['WORK_ORDER_DTLS_ID']]['QUANTITY']+=$row['QUANTITY'];
				$prev_data[$row['WORK_ORDER_DTLS_ID']]['AMOUNT']+=$row['AMOUNT'];
			}
		}
		/*else
		{
			if ($db_type==0) 
			{
				$sql = "SELECT a.id AS mst_id, b.job_no_mst, a.within_group, a.order_no AS sales_booking_no, a.party_id AS buyer_id, group_concat(d.id) AS dtls_id, b.gmts_item_id, b.main_process_id, b.embl_type, b.body_part, sum(d.qnty) as qnty, sum(d.amount) as amount, b.order_uom, c.booking_date
				FROM subcon_ord_mst a, subcon_ord_dtls b, wo_booking_mst c, subcon_ord_breakdown d
				WHERE a.id=b.mst_id and c.id=b.order_id and b.id=d.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=204 $wo_number $wo_date_cond $within_group_cond $buyer_id_cond $sales_order
				GROUP by a.id, b.job_no_mst, a.within_group, a.order_no, a.party_id, b.gmts_item_id, b.main_process_id, b.embl_type, b.body_part, c.booking_date, b.order_uom
				ORDER by a.id";//die;
			}
			else
			{
				$sql = "SELECT a.id AS mst_id, b.job_no_mst, a.within_group, a.order_no AS sales_booking_no, a.party_id AS buyer_id, rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') AS dtls_id, b.gmts_item_id, b.main_process_id, b.embl_type, b.body_part, sum(d.qnty) as qnty, sum(d.amount) as amount, b.order_uom, c.booking_date
				FROM subcon_ord_mst a, subcon_ord_dtls b, wo_booking_mst c, subcon_ord_breakdown d
				WHERE a.id=b.mst_id and c.id=b.order_id and b.id=d.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=204 $wo_number $wo_date_cond $within_group_cond $buyer_id_cond $sales_order
				GROUP by a.id, b.job_no_mst, a.within_group, a.order_no, a.party_id, b.gmts_item_id, b.main_process_id, b.embl_type, b.body_part, c.booking_date, b.order_uom
				ORDER by a.id";//die;
			}

			$sql_prev_pi=sql_select("SELECT work_order_id, gmts_item_id, embl_type, quantity, amount from com_export_pi_dtls where status_active=1 and is_deleted=0");
			$prev_data=array();
			foreach($sql_prev_pi as $row)
			{
				$prev_data[$row[csf("work_order_id")]][$row[csf("gmts_item_id")]][$row[csf("embl_type")]]["quantity"]+=$row[csf("quantity")];
				$prev_data[$row[csf("work_order_id")]][$row[csf("gmts_item_id")]][$row[csf("embl_type")]]["amount"]+=$row[csf("amount")];
			}
		}*/
		//echo $sql;
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table">
			<thead>
				<tr>
					<th colspan="9"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="120">Job No</th>
					<th width="110">Sales/Booking No</th>
					<th width="120">Gmts. Item</th>
					<th width="100">Embl Type</th>
					<th width="70">UOM</th>
					<th width="90">Order Qty</th>
					<th width="80">Rate/Dzn</th>
					<th>Amount</th>
				</tr>

			</thead>
		</table>
		<div style="width:890px; max-height:250px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" id="tbl_list_search">
				<? 
				$i=1; $job_id_arr =array(); $without_order_arr =array();
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";				
					
					if($row['WITHIN_GROUP']==2) $buyer=$buyer_arr[$row['BUYER_ID']];
					else $buyer=$company_arr[$row['BUYER_ID']];
					$bal_qtny=0; $is_loop=1;
					if($selected_based_on==1)
					{
						$bal_qtny=$row['QUANTITY']-$prev_data[$row['DTLS_ID']]['QUANTITY'];
						$amount=$row['AMOUNT']-$prev_data[$row['DTLS_ID']]['AMOUNT'];
						if($amount>0 && $bal_qtny) $rate=$amount/$bal_qtny; else $rate=0;
						//$rate=$row[csf('amount')]/$row[csf('quantity')]; else $rate=0;
						//echo $amount.'**'.$bal_qtny.'**'.$rate;
					}
					/*else
					{
						$bal_qtny=$row['QNTY']-$prev_data[$row['MST_ID']][$row['GMTS_ITEM_ID']][$row['EMBELLISHMENT_TYPE']]['QUANTITY'];
						$amount=$row['AMOUNT']-$prev_data[$row['MST_ID']][$row['GMTS_ITEM_ID']][$row['EMBELLISHMENT_TYPE']]['AMOUNT'];
						if($amount>0 && $bal_qtny) $rate=$amount/$bal_qtny; else $rate=0;
					}*/

					if($bal_qtny>0)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
			                <td width="40" align="center"><? echo $i; ?>
			                    <input type="hidden" name="txt_wo_id_dtls" id="txt_wo_id_dtls<? echo $i ?>" value="<? echo $row['DTLS_ID']; ?>"/>
			                    <input type="hidden" name="txt_wo_id" id="txt_wo_id<? echo $i ?>" value="<? echo $row['MST_ID']; ?>"/>	
			                </td>	
							<td width="120"><p><? echo $row['JOB_NO_MST'];?></p></td>
			                <td width="110"><p><? echo $row['SALES_BOOKING_NO'];?></p></td>
							<td width="120"><p><? echo $garments_item[$row['GMTS_ITEM_ID']]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $emblishment_print_type[$row['EMBL_TYPE']]; ?>&nbsp;</p></td>
							<td width="70" align="center"><p><? echo $unit_of_measurement[$row['ORDER_UOM']]; ?>&nbsp;</p></td>
							<td width="90" align="right"><p><? //if($selected_based_on==1) { $qty=0; } else { $qty=$bal_qtny; } echo number_format($qty,4,".",""); ?><? echo number_format($bal_qtny,4,".",""); ?>&nbsp;</p></td>
							<td width="80" align="right"><p><? echo number_format($rate,4,".",""); ?>&nbsp;</p></td>
							<td align="right"><p><? echo number_format($amount,4,".",""); ?>&nbsp;</p></td>
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

	else if($item_category==23) // All Over Printing (AOP)
	{
        if($within_group == 1){
            $sql = "SELECT a.id as mst_id, b.job_no_mst, a.within_group, a.order_no as sales_booking_no, a.party_id as buyer_id, b.id as dtls_id, b.gmts_color_id, b.construction, b.composition, b.gsm, b.grey_dia, b.order_quantity, b.rate, b.amount, b.order_uom, c.booking_date
		from subcon_ord_mst a, subcon_ord_dtls b, wo_booking_mst c
		where a.id=b.mst_id and c.id=b.order_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
		order by a.id";
        }else{
            $sql = "SELECT a.id as mst_id, b.job_no_mst, a.within_group, a.order_no as sales_booking_no, a.party_id as buyer_id, b.id as dtls_id, b.gmts_color_id, b.construction, b.composition, b.gsm, b.grey_dia, b.order_quantity, b.rate, b.amount, b.order_uom, '' as booking_date
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
		order by a.id";
        }

        //echo $sql; //die;

		$prev_pi_qnty_arr=return_library_array("SELECT work_order_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_id",'work_order_id','quantity');

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');

		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table">
			<thead>
				<tr>
					<th colspan="13"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="120">Job No</th>
					<th width="105">Sales/Booking No</th>
					<th width="65">WO Date</th>
					<th width="50">Buyer</th>
					<th width="80">Gmts Color</th>
					<th width="100">Fabric Description</th>
					<th width="70">GSM</th>
					<th width="85">Dia/ Width</th>
					<th width="70">UOM</th>
					<th width="70">Order Qty</th>
					<th width="60">Rate/Dzn</th>
					<th>Amount</th>
				</tr>
			</thead>
		</table>
		<div style="width:1020px; max-height:250px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">
				<? 
				$i=1; $job_id_arr =array(); $without_order_arr =array();
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					if($row[csf('within_group')]==2) $buyer=$buyer_arr[$row[csf('buyer_id')]];
					else $buyer=$company_arr[$row[csf('buyer_id')]];
					$bal_qtny=0; $is_loop=1;
					if($selected_based_on==1)
					{
						if (in_array($row[csf('mst_id')], $job_id_arr))
						{
						  	$is_loop=2;
						}

						$bal_qtny=$row[csf('order_quantity')]-$prev_pi_qnty_arr[$row[csf('mst_id')]];

					}
					else
					{
						$bal_qtny=$row[csf('order_quantity')]-$prev_pi_qnty_arr_dtls[$row[csf('dtls_id')]];
					}

					if($bal_qtny>0 && $is_loop==1)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
			                <td width="40" align="center"><? echo $i; ?>
			                    <input type="hidden" name="txt_wo_id_dtls" id="txt_wo_id_dtls<? echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
			                    <input type="hidden" name="txt_wo_id" id="txt_wo_id<? echo $i ?>" value="<? echo $row[csf('mst_id')]; ?>"/>	
			                </td>	
							<td width="120"><p><? echo $row[csf('job_no_mst')];?></p></td>
			                <td width="105"><p><? echo $row[csf('sales_booking_no')];?></p></td>
							<td width="65" align="center"><p><? if($row[csf('booking_date')]!="" && $row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]);?></p></td>
							<td width="50"><p><? echo $buyer; ?>&nbsp;</p></td> 
							<td width="80"><p><? echo $color_library[$row[csf('gmts_color_id')]]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $row[csf('construction')].', '.$row[csf('composition')]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $row[csf('gsm')]; ?></p></td>
							<td width="85"><p><? echo $row[csf('grey_dia')]; ?>&nbsp;</p></td>
							<td width="70" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>&nbsp;</p></td>
							<td width="70" align="right"><p><? echo number_format($bal_qtny,2,".",""); ?>&nbsp;</p></td>
							<td width="60"><p><? echo $row[csf('rate')]; ?>&nbsp;</p></td>
							<td><p><? echo $row[csf('amount')]; ?>&nbsp;</p></td>
						</tr>
				 		<?
				 		$i++;
					}
				 	$job_id_arr[]=$row[csf('mst_id')];		 
				}
				?>
			</table>
		</div>
		<?
	}

	else if($item_category==37) // Gmts Washing
	{
		if($selected_based_on==1)
		{
			if($within_group==1)
			{
				if ($db_type==0) 
				{
					$sql = "SELECT a.id AS mst_id, a.job_no_prefix_num, c.booking_no_prefix_num, b.job_no_mst, a.within_group, a.order_no AS sales_booking_no, group_concat(b.id) AS dtls_id, sum(b.order_quantity) as quantity, sum(b.amount) as amount, b.buyer_style_ref, b.order_uom, a.gmts_type, max(b.gmts_item_id) as gmts_item_id
					FROM subcon_ord_mst a, subcon_ord_dtls b, wo_booking_mst c
					WHERE a.id=b.mst_id and a.order_id = c.id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=295 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
					GROUP by a.id, b.job_no_mst, a.job_no_prefix_num, c.booking_no_prefix_num, a.within_group, a.order_no,b.buyer_style_ref, b.order_uom, a.gmts_type
					ORDER by a.id desc";//die;
				}
				else
				{
					$sql = "SELECT a.id AS mst_id, a.job_no_prefix_num, c.booking_no_prefix_num, b.job_no_mst, a.within_group, a.order_no AS sales_booking_no, rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') AS dtls_id, sum(b.order_quantity) as quantity, sum(b.amount) as amount, b.buyer_style_ref, b.order_uom, a.gmts_type, max(b.gmts_item_id) as gmts_item_id
					FROM subcon_ord_mst a, subcon_ord_dtls b, wo_booking_mst c
					WHERE a.id=b.mst_id and a.order_id = c.id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=295 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
					GROUP by a.id, b.job_no_mst, a.job_no_prefix_num, c.booking_no_prefix_num, a.within_group, a.order_no,b.buyer_style_ref, b.order_uom, a.gmts_type
					ORDER by a.id desc";//die;
				}
			}
			else
			{
				if ($db_type==0) 
				{
					$sql = "SELECT a.id AS mst_id, a.job_no_prefix_num, a.order_no as booking_no_prefix_num, b.job_no_mst, a.within_group, a.order_no AS sales_booking_no, group_concat(b.id) AS dtls_id, sum(b.order_quantity) as quantity, sum(b.amount) as amount, b.buyer_style_ref, b.order_uom, a.gmts_type, max(b.gmts_item_id) as gmts_item_id
					FROM subcon_ord_mst a, subcon_ord_dtls b
					WHERE a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=295 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
					GROUP by a.id, b.job_no_mst, a.job_no_prefix_num, a.within_group, a.order_no, b.buyer_style_ref, b.order_uom, a.gmts_type
					ORDER by a.id desc";//die;
				}
				else
				{
					$sql = "SELECT a.id AS mst_id, a.job_no_prefix_num, a.order_no as booking_no_prefix_num, b.job_no_mst, a.within_group, a.order_no AS sales_booking_no, rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') AS dtls_id, sum(b.order_quantity) as quantity, sum(b.amount) as amount, b.buyer_style_ref, b.order_uom, a.gmts_type, max(b.gmts_item_id) as gmts_item_id
					FROM subcon_ord_mst a, subcon_ord_dtls b
					WHERE a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=295 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
					GROUP by a.id, b.job_no_mst, a.job_no_prefix_num, a.within_group, a.order_no,b.buyer_style_ref, b.order_uom, a.gmts_type
					ORDER by a.id desc";//die;
				}
			}
			

			//echo $sql;die;
			
			$sql_prev_pi=sql_select("SELECT work_order_id, quantity, amount from com_export_pi_dtls where status_active=1 and is_deleted=0");
			$prev_data=array();
			foreach($sql_prev_pi as $row)
			{
				$prev_data[$row[csf("work_order_id")]]["quantity"]+=$row[csf("quantity")];
				$prev_data[$row[csf("work_order_id")]]["amount"]+=$row[csf("amount")];
			}
		}
		/*else
		{
			if ($db_type==0) 
			{
				$sql = "SELECT a.id AS mst_id, b.job_no_mst, a.within_group, a.order_no AS sales_booking_no, group_concat(c.id) AS dtls_id, b.gmts_item_id, b.main_process_id, c.embellishment_type, b.body_part, b.order_uom, b.order_quantity as qnty, b.rate, b.amount
				FROM subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
				WHERE a.id=b.mst_id and b.id=c.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=295 $wo_number $wo_date_cond $within_group_cond $buyer_id_cond $sales_order
				GROUP by a.id, b.job_no_mst, a.within_group, a.order_no, b.gmts_item_id, b.main_process_id, c.embellishment_type, b.body_part, b.order_uom, b.order_quantity, b.rate, b.amount
				ORDER by a.id";//die;
			}
			else
			{
				$sql = "SELECT a.id AS mst_id, b.job_no_mst, a.within_group, a.order_no AS sales_booking_no, listagg (c.id, ',') WITHIN GROUP (ORDER BY c.id) AS dtls_id, b.gmts_item_id, b.main_process_id, c.embellishment_type, b.body_part, b.order_uom, b.order_quantity as qnty, b.rate, b.amount
				FROM subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
				WHERE a.id=b.mst_id and b.id=c.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=295 $wo_number $wo_date_cond $within_group_cond $buyer_id_cond $sales_order
				GROUP by a.id, b.job_no_mst, a.within_group, a.order_no, b.gmts_item_id, b.main_process_id, c.embellishment_type, b.body_part, b.order_uom, b.order_quantity, b.rate, b.amount
				ORDER by a.id";//die;
			}	
			

			$sql_prev_pi=sql_select("SELECT work_order_id, gmts_item_id, wash_type, quantity, amount from com_export_pi_dtls where status_active=1 and is_deleted=0");
			$prev_data=array();
			foreach($sql_prev_pi as $row)
			{
				$prev_data[$row[csf("work_order_id")]][$row[csf("gmts_item_id")]][$row[csf("wash_type")]]["quantity"]+=$row[csf("quantity")];
				$prev_data[$row[csf("work_order_id")]][$row[csf("gmts_item_id")]][$row[csf("wash_type")]]["amount"]+=$row[csf("amount")];
			}
		}*/
		// echo $sql;
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
			<thead>
				<tr>
					<th colspan="12"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="110">Wash Job No</th>
					<th width="50">Job Suffix</th>
					<th width="100">Sales/Booking No</th>
					<th width="50">Booking Suffix</th>
					<th width="100">Buyer Style</th>
					<th width="90">Gmts. Item</th>
					<th width="100">Washing Type</th>
					<th width="70">UOM</th>
					<th width="90">Order Qty</th>
					<th width="80">Rate</th>
					<th>Amount</th>
				</tr>
			</thead>
		</table>
		<div style="width:1000px; max-height:250px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table" id="tbl_list_search">
				<? 
				$i=1; $job_id_arr =array(); $without_order_arr =array();
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($db_type==2) $row[csf('dtls_id')] = $row[csf('dtls_id')]->load();
					
					if($row[csf('within_group')]==2) $buyer=$buyer_arr[$row[csf('buyer_id')]];
					else $buyer=$company_arr[$row[csf('buyer_id')]];
					$bal_qtny=0; $is_loop=1;
					if($selected_based_on==1)
					{
						if (in_array($row[csf('mst_id')], $job_id_arr))
						{
						  	$is_loop=2;
						}

						$bal_qtny=$row[csf('quantity')]-$prev_data[$row[csf('mst_id')]]["quantity"]-$curr_data[$row[csf('mst_id')]]['quantity'];
						// echo $row[csf('mst_id')]."</br>";
						$amount=$row[csf('amount')]-$prev_data[$row[csf('mst_id')]]["amount"]-$curr_data[$row[csf('mst_id')]]['amount'];
						if($amount>0 && $bal_qtny) $rate=$amount/$bal_qtny; else $rate=0;
						//$rate=$row[csf('amount')]/$row[csf('quantity')]; else $rate=0;
						//echo $amount.'**'.$bal_qtny.'**'.$rate;
					}
					else
					{
						$bal_qtny=$row[csf('qnty')]-$prev_data[$row[csf('mst_id')]][$row[csf('gmts_item_id')]][$row[csf('embellishment_type')]]["quantity"];
						$amount=$row[csf('amount')]-$prev_data[$row[csf('mst_id')]][$row[csf('gmts_item_id')]][$row[csf('embellishment_type')]]["amount"];
						if($amount>0 && $bal_qtny) $rate=$amount/$bal_qtny; else $rate=0;
					}

					if($bal_qtny>0 && $is_loop==1)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
			                <td width="30" align="center"><? echo $i; ?>
			                    <input type="hidden" name="txt_wo_id_dtls" id="txt_wo_id_dtls<? echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
			                    <input type="hidden" name="txt_wo_id" id="txt_wo_id<? echo $i ?>" value="<? echo $row[csf('mst_id')]; ?>"/>	
			                </td>	
							<td width="110"><p><? echo $row[csf('job_no_mst')];?></p></td>
							<td width="50" align="center"><p><? echo $row[csf('job_no_prefix_num')];?></p></td>
			                <td width="100"><p><? echo $row[csf('sales_booking_no')];?></p></td>
							<td width="50" align="center"><p><? echo $row[csf('booking_no_prefix_num')];?></p></td>
                            <td width="100"><p><? echo $row[csf('buyer_style_ref')];?></p></td>
							<td width="90"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $wash_gmts_type_array[$row[csf('gmts_type')]]; ?>&nbsp;</p></td>
							<td width="70" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>&nbsp;</p></td>
							<td width="90" align="right"><p><? //if($selected_based_on==1) { $qty=0; } else { $qty=$bal_qtny; } echo number_format($qty,4,".",""); ?><? echo number_format($bal_qtny,4,".",""); ?>&nbsp;</p></td>
							<td width="80" align="right"><? echo number_format($rate,4,".",""); ?></td>
							<td align="right"><? echo number_format($amount,4,".",""); ?></td>
						</tr>
				 		<?
				 		$i++;
					}
				 	$job_id_arr[]=$row[csf('mst_id')];		 
				}
				?>
			</table>
		</div>
		<?
	}

	else if($item_category==36) // Gmts Emb view
	{
		if ($selected_based_on==1) // Remove subcon_ord_breakdown table
		{
			if ($db_type==0) 
			{
				$sql = "SELECT a.id AS mst_id, b.job_no_mst, a.within_group, a.order_no AS sales_booking_no, a.party_id AS buyer_id, group_concat(b.id) AS dtls_id, sum(b.order_quantity) as quantity, sum(b.amount) as amount, c.booking_date
				FROM subcon_ord_mst a, subcon_ord_dtls b, wo_booking_mst c
				where a.id=b.mst_id and c.id=b.order_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=311 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
				GROUP by a.id, b.job_no_mst, a.within_group, a.order_no, a.party_id, c.booking_date
				ORDER by a.id";//die;
			}
			else
			{
				$sql = "SELECT a.id AS mst_id, b.job_no_mst, a.within_group, a.order_no AS sales_booking_no, a.party_id AS buyer_id, rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') AS dtls_id, sum(b.order_quantity) as quantity, sum(b.amount) as amount, c.booking_date
				FROM subcon_ord_mst a, subcon_ord_dtls b, wo_booking_mst c
				where a.id=b.mst_id and c.id=b.order_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=311 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
				GROUP by a.id, b.job_no_mst, a.within_group, a.order_no, a.party_id, c.booking_date
				ORDER by a.id";//die;
			}	
			

			$sql_prev_pi=sql_select("SELECT work_order_id, quantity, amount from com_export_pi_dtls where status_active=1 and is_deleted=0");
			$prev_data=array();
			foreach($sql_prev_pi as $row)
			{
				$prev_data[$row[csf("work_order_id")]]["quantity"]+=$row[csf("quantity")];
				$prev_data[$row[csf("work_order_id")]]["amount"]+=$row[csf("amount")];
			}
		}
		else
		{
			if ($db_type==0) 
			{
				$sql= "SELECT a.id AS mst_id, b.job_no_mst, a.within_group, a.order_no AS sales_booking_no, a.party_id AS buyer_id, group_concat(d.id) AS dtls_id, b.gmts_item_id, b.main_process_id, b.embl_type, b.body_part, sum(d.qnty) as qnty, sum(d.amount) as amount, b.order_uom, c.booking_date
				FROM subcon_ord_mst a, subcon_ord_dtls b, wo_booking_mst c, subcon_ord_breakdown d
				WHERE a.id=b.mst_id and c.id=b.order_id and b.id=d.mst_id and b.job_no_mst=d.job_no_mst and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.entry_form=311 $wo_number $wo_date_cond $within_group_cond $buyer_id_cond $sales_order $wo_without
				GROUP by a.id, b.job_no_mst, a.within_group, a.order_no, a.party_id, b.gmts_item_id, b.main_process_id, b.embl_type, b.body_part, c.booking_date, b.order_uom
				ORDER by a.id";//die;
			}
			else
			{
				$sql= "SELECT a.id AS mst_id, b.job_no_mst, a.within_group, a.order_no AS sales_booking_no, a.party_id AS buyer_id, rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') AS dtls_id, b.gmts_item_id, b.main_process_id, b.embl_type, b.body_part, sum(d.qnty) as qnty, sum(d.amount) as amount, b.order_uom, c.booking_date
				FROM subcon_ord_mst a, subcon_ord_dtls b, wo_booking_mst c, subcon_ord_breakdown d
				WHERE a.id=b.mst_id and c.id=b.order_id and b.id=d.mst_id and b.job_no_mst=d.job_no_mst and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.entry_form=311 $wo_number $wo_date_cond $within_group_cond $buyer_id_cond $sales_order $wo_without
				GROUP by a.id, b.job_no_mst, a.within_group, a.order_no, a.party_id, b.gmts_item_id, b.main_process_id, b.embl_type, b.body_part, c.booking_date, b.order_uom
				ORDER by a.id";//die;
			}	
			

			$sql_prev_pi=sql_select("SELECT work_order_id, gmts_item_id, embl_type, quantity, amount from com_export_pi_dtls where status_active=1 and is_deleted=0");
			$prev_data=array();
			foreach($sql_prev_pi as $row)
			{
				$prev_data[$row[csf("work_order_id")]][$row[csf("gmts_item_id")]][$row[csf("embl_type")]]["quantity"]+=$row[csf("quantity")];
				$prev_data[$row[csf("work_order_id")]][$row[csf("gmts_item_id")]][$row[csf("embl_type")]]["amount"]+=$row[csf("amount")];
			}
		}

		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" class="rpt_table">
			<thead>
				<tr>
					<th colspan="13"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="120">Job No</th>
					<th width="105">Sales/Booking No</th>
					<th width="65">WO Date</th>
					<th width="50">Buyer</th>
					<th width="80">Gmts. Item</th>
					<th width="100">Process /Embl. Name</th>
					<th width="70">Embl. Type</th>
					<th width="85">Body Part</th>
					<th width="70">UOM</th>
					<th width="70">Order Qty</th>
					<th width="60">Rate/Dzn</th>
					<th>Amount</th>
				</tr>
			</thead>
		</table>
		<div style="width:1020px; max-height:250px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="tbl_list_search">
				<? 
				$i=1; $job_id_arr =array(); $without_order_arr =array();
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($db_type==2) $row[csf('dtls_id')] = $row[csf('dtls_id')]->load();					
					if($row[csf('within_group')]==2) $buyer=$buyer_arr[$row[csf('buyer_id')]];
					else $buyer=$company_arr[$row[csf('buyer_id')]];
					$bal_qtny=0; $is_loop=1;
					if($selected_based_on==1)
					{
						if (in_array($row[csf('mst_id')], $job_id_arr))
						{
						  	$is_loop=2;
						}

						$bal_qtny=$row[csf('quantity')]-$prev_data[$row[csf('mst_id')]]["quantity"];
						$amount=$row[csf('amount')]-$prev_data[$row[csf('mst_id')]]["amount"];
					}
					else
					{
						$bal_qtny=$row[csf('qnty')]-$prev_data[$row[csf('mst_id')]][$row[csf('gmts_item_id')]][$row[csf('embl_type')]]["quantity"];
						$amount=$row[csf('amount')]-$prev_data[$row[csf('mst_id')]][$row[csf('gmts_item_id')]][$row[csf('embl_type')]]["amount"];
						if($amount>0 && $bal_qtny) $rate=$amount/$bal_qtny; else $rate=0;
					}
					if($bal_qtny>0 && $is_loop==1)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
			                <td width="40" align="center"><? echo $i; ?>
			                    <input type="hidden" name="txt_wo_id_dtls" id="txt_wo_id_dtls<? echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
			                    <input type="hidden" name="txt_wo_id" id="txt_wo_id<? echo $i ?>" value="<? echo $row[csf('mst_id')]; ?>"/>	
			                </td>	
							<td width="120"><p><? echo $row[csf('job_no_mst')];?></p></td>
			                <td width="105"><p><? echo $row[csf('sales_booking_no')];?></p></td>
							<td width="65" align="center"><p><? if($row[csf('booking_date')]!="" && $row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]);?></p></td>
							<td width="50"><p><? echo $buyer; ?>&nbsp;</p></td> 
							<td width="80"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $emblishment_name_array[$row[csf('main_process_id')]]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $emblishment_embroy_type[$row[csf('embl_type')]]; ?></p></td>
							<td width="85"><p><? echo $body_part[$row[csf('body_part')]]; ?>&nbsp;</p></td>
							<td width="70" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>&nbsp;</p></td>
							<td width="70" align="right"><p><? if($selected_based_on==1) { $qty=0; } else { $qty=$bal_qtny; } echo number_format($qty,4,".",""); ?>&nbsp;</p></td>
							<td width="60" align="right"><p><? echo number_format($rate,4,".",""); ?>&nbsp;</p></td>
							<td align="right"><p><? echo number_format($amount,4,".",""); ?>&nbsp;</p></td>
						</tr>
				 		<?
				 		$i++;
					}
				 	$job_id_arr[]=$row[csf('mst_id')];		 
				}
				?>
			</table>
		</div>
		<?
	}

	else if($item_category==45) // Gmts Trims (Accessories) view
	{
		if($selected_based_on==1) // Work Orde Wise
		{
			if ($db_type==0) 
			{
				$sql = "SELECT a.id AS mst_id, b.job_no_mst, a.party_id, a.within_group, a.order_no, sum(b.order_quantity) as quantity, sum(b.amount) as amount, group_concat(b.id) as dtls_id, max(b.buyer_buyer) as buyer_buyer
				from subcon_ord_mst a, subcon_ord_dtls b
				where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=255 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
				group by  a.id, b.job_no_mst, a.party_id, a.within_group, a.order_no
				order by a.id";//die;
			}
			else
			{
				$sql = "SELECT a.id AS mst_id, b.job_no_mst, a.party_id, a.within_group, a.order_no, sum(b.order_quantity) as quantity, sum(b.amount) as amount, rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') AS dtls_id, max(b.buyer_buyer) as buyer_buyer
				from subcon_ord_mst a, subcon_ord_dtls b
				where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=255 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
				group by  a.id, b.job_no_mst, a.party_id, a.within_group, a.order_no
				order by a.id";//die;
			}
			
			$sql_prev_pi=sql_select("SELECT work_order_id, quantity, amount from com_export_pi_dtls where status_active=1 and is_deleted=0");
			$prev_data=array();
			foreach($sql_prev_pi as $row)
			{
				$prev_data[$row[csf("work_order_id")]]["quantity"]+=$row[csf("quantity")];
				$prev_data[$row[csf("work_order_id")]]["amount"]+=$row[csf("amount")];
			}
		}
		else // Item Wise
		{
			if ($db_type==0) 
			{
				$sql = "SELECT a.id as mst_id, b.job_no_mst, a.party_id, a.within_group, a.order_no, b.item_group, sum(c.qnty) as qnty, sum(c.amount) as amount, b.order_uom, group_concat(c.id) as dtls_id, b.buyer_buyer
				from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
				where a.id=b.mst_id and b.id=c.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=255 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
				group by a.id, b.job_no_mst, a.party_id, a.within_group, a.order_no, b.item_group, b.rate, b.amount, b.order_uom, b.buyer_buyer
				order by a.id";//die;
			}
			else
			{
				$sql = "SELECT a.id as mst_id, b.job_no_mst, a.party_id, a.within_group, a.order_no, b.item_group, sum(c.qnty) as qnty, sum(c.amount) as amount, b.order_uom, rtrim(xmlagg(xmlelement(e,c.id,',').extract('//text()') order by c.id).GetClobVal(),',') AS dtls_id, b.buyer_buyer
				from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
				where a.id=b.mst_id and b.id=c.mst_id and b.job_no_mst=c.job_no_mst and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=255 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
				group by a.id, b.job_no_mst, a.party_id, a.within_group, a.order_no, b.item_group, b.rate, b.amount, b.order_uom, b.buyer_buyer
				order by a.id";//die;
				
			}
			
			$sql_prev_pi=sql_select("SELECT work_order_id, gmts_item_id, quantity, amount from com_export_pi_dtls where status_active=1 and is_deleted=0");
			$prev_data=array();
			foreach($sql_prev_pi as $row)
			{
				$prev_data[$row[csf("work_order_id")]][$row[csf("gmts_item_id")]]["quantity"]+=$row[csf("quantity")];
				$prev_data[$row[csf("work_order_id")]][$row[csf("gmts_item_id")]]["amount"]+=$row[csf("amount")];
			}
		}
		//echo $sql;

		?>
        <p style="color:#F00; font-size:14px; font-weight:bold">Order Qty, Rate, Amount Show Based On Item Wise</p>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table">
			<thead>
				<tr>
					<th colspan="9"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="120">Job No</th>
					<th width="110">Wo No</th>
					<th width="110">Buyer</th>
					<th width="150">Item Group</th>
					<th width="80">UOM</th>
					<th width="120">Order Qty</th>
					<th width="90">Rate</th>
					<th>Amount</th>
				</tr>
			</thead>
		</table>
		<div style="width:990px; max-height:250px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table" id="tbl_list_search">
				<? 
				$i=1; $job_id_arr =array(); $without_order_arr =array();
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($db_type==2) $row[csf('dtls_id')] = $row[csf('dtls_id')]->load();
					$bal_qtny=0; $is_loop=1;
					$rate=$row[csf('amount')]/$row[csf('quantity')];
					if($selected_based_on==1) // Work Orde Wise
					{
						if (in_array($row[csf('mst_id')], $job_id_arr))
						{
						  	$is_loop=2;
						}
						$bal_qtny=$row[csf('quantity')]-$prev_data[$row[csf('mst_id')]]["quantity"];
						$amount=$row[csf('amount')]-$prev_data[$row[csf('mst_id')]]["amount"];
						//if($amount>0 && $bal_qtny) $rate=$amount/$bal_qtny; else $rate=0;
					}
					else // Item Wise
					{						
						$item_group=$item_group_arr[$row[csf('item_group')]];
						$order_uom=$unit_of_measurement[$row[csf('order_uom')]];
						$bal_qtny=$row[csf('qnty')]-$prev_data[$row[csf('mst_id')]][$row[csf('item_group')]]["quantity"];
						$amount=$row[csf('amount')]-$prev_data[$row[csf('mst_id')]][$row[csf('item_group')]]["amount"];
						//if($amount>0 && $bal_qtny) $rate=$amount/$bal_qtny; else $rate=0;
					}
					$amount=$bal_qtny*$rate;
					//echo $bal_qtny."=".$row[csf('qnty')]."=".$prev_data[$row[csf('mst_id')]][$row[csf('item_group')]]["quantity"]."=".$row[csf('mst_id')]."=".$row[csf('item_group')]."<br>";
					if(number_format($bal_qtny,2,".","")>0 && $is_loop==1)
					//if($bal_qtny>0 && $is_loop==1)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
			                <td width="40" align="center"><? echo $i; ?>
			                    <input type="hidden" name="txt_wo_id_dtls" id="txt_wo_id_dtls<? echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
			                    <input type="hidden" name="txt_wo_id" id="txt_wo_id<? echo $i ?>" value="<? echo $row[csf('mst_id')]; ?>"/>
			                </td>
							<td width="120"><p><? echo $row[csf('job_no_mst')];?></p></td>
			                <td width="110"><p><? echo $row[csf('order_no')];?></p></td>
                            <td width="110"><p><? echo $buyer_arr[$row[csf('buyer_buyer')]]; //if($row[csf('within_group')]==1) echo $company_arr[$row[csf('party_id')]]; else echo $buyer_arr[$row[csf('party_id')]];?></p></td>
							<td width="150"><p><? echo $item_group; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $order_uom; ?>&nbsp;</p></td>
							<td width="120" align="right" title="<?= $bal_qtny;?>"><p><? echo number_format($bal_qtny,2,".",""); ?>&nbsp;</p></td>
							<td width="90" align="right"><p><? if(number_format($bal_qtny,2,".","")>0) echo number_format($rate,4); else echo "0.00"; ?>&nbsp;</p></td>
							<td align="right"><p><? echo number_format($amount,2); ?>&nbsp;</p></td>
						</tr>
				 		<?
				 		$i++;
					}
				 	$job_id_arr[]=$row[csf('mst_id')];		 
				}
				?>
			</table>
		</div>
		<?
	}
    else if($item_category==67) // Sub Con view
{
    if ($db_type==0)
    {
        $sql = "SELECT a.id AS mst_id, b.job_no_mst, a.party_id, a.within_group, b.order_uom, a.order_no, sum(b.order_quantity) as quantity, sum(b.amount) as amount, group_concat(b.id) as dtls_id, max(b.buyer_buyer) as buyer_buyer
            from subcon_ord_mst a, subcon_ord_dtls b
            where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=238 $wo_date_cond $cbo_year  $sales_order
            group by  a.id, b.job_no_mst, a.party_id, a.within_group, a.order_no, b.order_uom
            order by a.id";//die;
    }
    else
    {
        $sql = "SELECT a.id AS mst_id, b.job_no_mst, a.party_id, b.order_uom, a.within_group, a.order_no, sum(b.order_quantity) as quantity, sum(b.amount) as amount, rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') AS dtls_id, max(b.buyer_buyer) as buyer_buyer
            from subcon_ord_mst a, subcon_ord_dtls b
            where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=238  $wo_date_cond $cbo_year $sales_order
            group by  a.id, b.job_no_mst, a.party_id, a.within_group, a.order_no, b.order_uom
            order by a.id";//die;
    }

//    echo $sql;
    $party_arr = return_library_array("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name", "id", "buyer_name");
//print_r($party_arr);
    $sql_prev_pi=sql_select("SELECT work_order_id, quantity, amount from com_export_pi_dtls where status_active=1 and is_deleted=0");
    $prev_data=array();
    foreach($sql_prev_pi as $row)
    {
        $prev_data[$row[csf("work_order_id")]]["quantity"]+=$row[csf("quantity")];
        $prev_data[$row[csf("work_order_id")]]["amount"]+=$row[csf("amount")];
    }
//    print_r($prev_data);
    //echo $sql;

    ?>
    <p style="color:#F00; font-size:14px; font-weight:bold">Order Qty, Rate, Amount Show Based On Item Wise</p>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table">
        <thead>
        <tr>
            <th colspan="9"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
        </tr>
        <tr>
            <th width="40">SL</th>
            <th width="120">Job No</th>
            <th width="110">Party</th>
            <th width="150">Item Group</th>
            <th width="80">UOM</th>
            <th width="120">Order Qty</th>
            <th width="90">Rate</th>
            <th>Amount</th>
        </tr>
        </thead>
    </table>
    <div style="width:990px; max-height:250px; overflow-y:scroll">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table" id="tbl_list_search">
            <?
            $i=1; $job_id_arr =array(); $without_order_arr =array();
            $nameArray=sql_select( $sql );
            foreach ($nameArray as $row)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                if($db_type==2) $row[csf('dtls_id')] = $row[csf('dtls_id')]->load();
                $bal_qtny=0; $is_loop=1;
                $rate=$row[csf('amount')]/$row[csf('quantity')];
                    $item_group=$item_group_arr[$row[csf('item_group')]];
                    $order_uom=$unit_of_measurement[$row[csf('order_uom')]];
                    $bal_qtny=$row[csf('quantity')]-$prev_data[$row[csf('mst_id')]]["quantity"];
                    $amount=$row[csf('amount')]-$prev_data[$row[csf('mst_id')]]["amount"];
                    //if($amount>0 && $bal_qtny) $rate=$amount/$bal_qtny; else $rate=0;

                $amount=$bal_qtny*$rate;
                //echo $bal_qtny."=".$row[csf('qnty')]."=".$prev_data[$row[csf('mst_id')]][$row[csf('item_group')]]["quantity"]."=".$row[csf('mst_id')]."=".$row[csf('item_group')]."<br>";
                if($bal_qtny > 0 && $is_loop==1)
                    //if($bal_qtny>0 && $is_loop==1)
                {
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                        <td width="40" align="center"><? echo $i; ?>
                            <input type="hidden" name="txt_wo_id_dtls" id="txt_wo_id_dtls<? echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
                            <input type="hidden" name="txt_wo_id" id="txt_wo_id<? echo $i ?>" value="<? echo $row[csf('mst_id')]; ?>"/>
                        </td>
                        <td width="120"><p><? echo $row[csf('job_no_mst')];?></p></td>
                        <td width="110"><p><? echo $party_arr[$row[csf('party_id')]]; //if($row[csf('within_group')]==1) echo $company_arr[$row[csf('party_id')]]; else echo $buyer_arr[$row[csf('party_id')]];?></p></td>
                        <td width="150"><p><? echo $item_group; ?>&nbsp;</p></td>
                        <td width="80" align="center"><p><? echo $order_uom; ?>&nbsp;</p></td>
                        <td width="120" align="right" title="<?= $bal_qtny;?>"><p><? echo number_format($bal_qtny,2,".",""); ?>&nbsp;</p></td>
                        <td width="90" align="right"><p><? if(number_format($bal_qtny,2,".","")>0) echo number_format($rate,4); else echo "0.00"; ?>&nbsp;</p></td>
                        <td align="right"><p><? echo number_format($amount,2); ?>&nbsp;</p></td>
                    </tr>
                    <?
                    $i++;
                }
                $job_id_arr[]=$row[csf('mst_id')];
            }
            ?>
        </table>
    </div>
    <?
    }
	else
	{
		echo "Develop Later";
	}
	
	?>	
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
	$wo_dtls_id =$data[0];
	$tblRow 	=$data[1];
	$wo_mst_id 	=$data[2];
	$based_on 	=$data[3];
	$item_category_id 	=$data[4];
	$curr_wo_dtls_id = explode(',',$data[5]);
	$curr_quantity = explode(',',$data[6]);

	$curr_data=array();
	for($i=0;$i<count($curr_wo_dtls_id);$i++)
	{
		$curr_data[$curr_wo_dtls_id[$i]]['quantity']+=$curr_quantity[$i];
	}
	$company_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4 and status_active=1",'id','item_name');
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );

	
	if($item_category_id==1 || $item_category_id==10) // Knit Garments and Fabric
	{
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
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
		if($based_on==2) $cond_mst_dls="and b.id in($wo_dtls_id)"; else $cond_mst_dls=" and b.mst_id in ($wo_mst_id)";
		
		$sql = "SELECT a.id, a.job_no,a.sales_booking_no, b.cons_uom, b.id as dtls_id, b.determination_id, b.gsm_weight, b.dia, b.color_id, b.grey_qnty_by_uom  as qty, b.pp_qnty, b.mtl_qnty, b.fpt_qnty, b.gpt_qnty, b.avg_rate, b.amount from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id $cond_mst_dls and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');
		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{			
			
			if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$bal_qtny=$row[csf('qty')]+$row[csf('pp_qnty')]+$row[csf('mtl_qnty')]+$row[csf('fpt_qnty')]+$row[csf('gpt_qnty')]-$prev_pi_qnty_arr_dtls[$row[csf('dtls_id')]];			
			if($bal_qtny>0)
			{
				$tblRow++;
				$amount= $bal_qtny*$row[csf('avg_rate')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
						<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $tblRow; ?>" readonly/>
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('job_no')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('dtls_id')]; ?>" readonly />
					</td>
					<td> 
                        <input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('sales_booking_no')]; ?>" style="width:100px" disabled="disabled"/>
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
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('cons_uom')],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; //$row[csf('qty')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; //$row[csf('qty')]; ?>" style="width:61px;"/>
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('avg_rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $amount;//$row[csf('amount')]; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
					</td>
					<?
						if($item_category_id==10){
							?>
								<td><input type="text" name="txtRemarks_<? echo $tblRow; ?>" id="txtRemarks_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:100px;" onKeyUp="copy_remarks(<? echo $tblRow; ?>)" /></td>
							<?
						}
					?>
				</tr>
				<?
			}		
		}
	}

	if($item_category_id==20 || $item_category_id==22) // Knitting,Dyeing and Finishing
	{
		$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
		//if($based_on==2) $cond_mst_dls="and b.id in($wo_dtls_id)"; else $cond_mst_dls=" and a.id in ($wo_mst_id)";
		$cond_mst_dls="and b.id in($wo_dtls_id)";
		if ($item_category_id==20)
		{
			$sql="SELECT a.id as mst_id, a.booking_no, a.booking_date, a.company_id, a.buyer_id, b.id as dtls_id, b.job_no, b.fabric_color_id as color_id, b.fin_gsm as gsm, b.fin_dia as dia, b.uom, b.wo_qnty, b.rate, d.fabric_description as fabric_desc, d.lib_yarn_count_deter_id as determination_id, d.construction, d.composition from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=d.id and a.booking_type=3 and a.item_category=12 and a.pay_mode=5 and a.entry_form=228 and b.entry_form_id=228 and b.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $cond_mst_dls order by a.id";
		}
		else
		{
			$sql="SELECT a.id as mst_id, a.booking_no, a.booking_date, a.company_id, a.buyer_id, b.id as dtls_id, b.job_no, b.fabric_color_id as color_id, b.fin_gsm as gsm, b.dia_width as dia, b.uom, b.wo_qnty, b.rate, d.fabric_description as fabric_desc, d.lib_yarn_count_deter_id as determination_id, d.construction, d.composition from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fab_conv_cost_dtls c, wo_pre_cost_fabric_cost_dtls d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and c.fabric_description=d.id and a.booking_type=3 and a.item_category=12 and a.pay_mode=5 and a.entry_form=229 and b.entry_form_id=229 and b.booking_type=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $cond_mst_dls order by a.id";
		}	
		

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');
		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{		
			
			if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$bal_qtny=$row[csf('wo_qnty')]-$prev_pi_qnty_arr_dtls[$row[csf('dtls_id')]];			
			if($bal_qtny>0)
			{
				$tblRow++;
				$amount= $bal_qtny*$row[csf('rate')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
						<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $tblRow; ?>" readonly/>
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('job_no')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('mst_id')]; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('dtls_id')]; ?>" readonly />
					</td>
					<td> 
                        <input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('booking_no')]; ?>" style="width:100px" disabled="disabled"/>
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
		                <input type="text" name="diawidth_<? echo $tblRow; ?>" id="diawidth_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('dia')]; ?>" style="width:70px" disabled="disabled"/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('uom')],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; //$row[csf('qty')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; //$row[csf('qty')]; ?>" style="width:61px;"/>
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $amount;//$row[csf('amount')]; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
					</td>
					<td><input type="text" name="txtRemarks_<? echo $tblRow; ?>" id="txtRemarks_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:100px;" onKeyUp="copy_remarks(<? echo $tblRow; ?>)" /></td>
				</tr>
				<?
			}		
		}
	}

	else if($item_category_id==35) // Gmts Printing
	{
		if($based_on==2) $cond_mst_dls="and c.id in($wo_dtls_id)"; else $cond_mst_dls=" and b.mst_id in ($wo_mst_id)";
		
		$sql = "SELECT a.ID, b.JOB_NO_MST, a.WITHIN_GROUP, a.order_no as SALES_BOOKING_NO, b.id as DTLS_ID, b.GMTS_ITEM_ID, b.ORDER_UOM, c.RATE, c.COLOR_ID, sum(c.qnty) as ORDER_QUANTITY
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c 
		where a.id=b.mst_id and b.id=c.mst_id and b.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=204 $cond_mst_dls		 
		group by a.id, b.job_no_mst, a.within_group, a.order_no, b.id, b.gmts_item_id,  b.order_uom, c.rate, c.color_id 
		order by a.id ";//die;
		/*$sql = "SELECT a.ID, b.JOB_NO_MST, a.WITHIN_GROUP, a.order_no as SALES_BOOKING_NO, b.id as DTLS_ID, b.GMTS_ITEM_ID, b.ORDER_QUANTITY, b.RATE, b.AMOUNT, b.ORDER_UOM, b.GMTS_COLOR_ID
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=204 $cond_mst_dls
		order by a.id";*/

		/*$prev_pi_qnty_arr_dtls=return_library_array("SELECT WORK_ORDER_DTLS_ID, sum(quantity) as QUANTITY from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');*/
		$prev_pi_qnty_arr_dtls=array();
		$sql_pi_qty="SELECT WORK_ORDER_DTLS_ID, COLOR_ID, sum(quantity) as QUANTITY from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id, color_id";
		$sql_pi_qty_res=sql_select($sql_pi_qty);
		foreach ($sql_pi_qty_res as $row) {
			$prev_pi_qnty_arr_dtls[$row['WORK_ORDER_DTLS_ID']][$row['COLOR_ID']] += $row['QUANTITY'];
		}

		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{
			$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
			
			/*$amount = $row['ORDER_QUANTITY']*$row['rate'];*/
			$process_embl_type=$blank_array;

			if($row['WITHIN_GROUP']==2) $buyer=$buyer_arr[$row['BUYER_ID']];
			else $buyer=$company_arr[$row['BUYER_ID']];

			
			$bal_qtny=$row['ORDER_QUANTITY']-$prev_pi_qnty_arr_dtls[$row['DTLS_ID']][$row['COLOR_ID']];
			$amount=$row['RATE']*$bal_qtny;
			if($bal_qtny>0)
			{
				$tblRow++;
				if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row['JOB_NO_MST']; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row['ID']; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row['DTLS_ID']; ?>" readonly />
					</td>
		            <td> 
						<input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row['SALES_BOOKING_NO']; ?>" style="width:110px" disabled="disabled"/>
					</td>
		            <td>
		                <input type="text" name="gsmItem_<? echo $tblRow; ?>" id="gsmItem_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $garments_item[$row['GMTS_ITEM_ID']]; ?>" style="width:110px; text-align: left;" disabled="disabled"/>
		                <input type="hidden" name="hideGsmItem_<? echo $tblRow; ?>" id="hideGsmItem_<? echo $tblRow; ?>" value="<? echo $row['GMTS_ITEM_ID']; ?>"/>
		            </td>
					<td>
		                <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row['COLOR_ID']]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row['COLOR_ID']; ?>"/>
		            </td>
		            <td style="display:none"> 
						<input type="text" name="itemDesc_<? echo $tblRow; ?>" id="itemDesc_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:70px" disabled="disabled"/>
					</td>
		            <td style="display:none">
		                <input type="text" name="processEmbl_<? echo $tblRow; ?>" id="processEmbl_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideProcessEmbl_<? echo $tblRow; ?>" id="hideProcessEmbl_<? echo $tblRow; ?>" value=""/>
		            </td>
		            <td style="display:none">
		                <input type="text" name="emblType_<? echo $tblRow; ?>" id="emblType_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:80px" disabled="disabled"/>
		                <input type="hidden" name="hideEmblType_<? echo $tblRow; ?>" id="hideEmblType_<? echo $tblRow; ?>" value=""/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row['ORDER_UOM'],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;"/>
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row['RATE']; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $amount; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
					</td>
				</tr>
				<?
			}		
		}
	}

	else if($item_category_id==23) // All Over Printing (AOP)
	{
		if($based_on==2) $cond_mst_dls="and b.id in($wo_dtls_id)"; else $cond_mst_dls=" and b.mst_id in ($wo_mst_id)";
		
		$sql = "SELECT a.id, b.job_no_mst, a.within_group, a.order_no as sales_booking_no, a.party_id as buyer_id, b.id as dtls_id, b.gmts_color_id, b.aop_color_id, b.construction, b.composition, b.lib_yarn_deter, b.gsm, b.grey_dia, b.order_quantity, b.rate, b.amount, b.order_uom, b.body_part
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 $cond_mst_dls
		order by a.id";//die;

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');

		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{			
			
			if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$bal_qtny=$row[csf('order_quantity')]-$prev_pi_qnty_arr_dtls[$row[csf('dtls_id')]];		
			if($bal_qtny>0)
			{
				$tblRow++;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('job_no_mst')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('dtls_id')]; ?>" readonly />
					</td>					
		            <td> 
						<input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('sales_booking_no')]; ?>" style="width:110px" disabled="disabled"/>
					</td>
		            <td>
		                <input type="text" name="gmtsColor_<? echo $tblRow; ?>" id="gmtsColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('gmts_color_id')]]; ?>" style="width:80px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('gmts_color_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="aopColor_<? echo $tblRow; ?>" id="aopColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('aop_color_id')]]; ?>" style="width:80px" disabled="disabled"/>
		                <input type="hidden" name="hideAopColor_<? echo $tblRow; ?>" id="hideAopColor_<? echo $tblRow; ?>" value="<? echo $row[csf('aop_color_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="gsm_<? echo $tblRow; ?>" id="gsm_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('gsm')]; ?>" style="width:60px" disabled="disabled"/>
		            </td>
		            <td>
		                <input type="text" name="bodypart_<? echo $tblRow; ?>" id="bodypart_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $body_part[$row[csf('body_part')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideBodypart_<? echo $tblRow; ?>" id="hideBodypart_<? echo $tblRow; ?>" value="<? echo $row[csf('body_part')]; ?>"/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('order_uom')],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;"/>
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('amount')]; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
					</td>
				</tr>
				<?
			}		
		}
	}

	else if($item_category_id==37) // Gmts Wash
	{
		if($based_on==2) $cond_mst_dls="and c.id in($wo_dtls_id)"; else $cond_mst_dls=" and b.mst_id in ($wo_mst_id)";
		
		 /*$sql = "SELECT a.id, b.job_no_mst, a.within_group, a.order_no as sales_booking_no, c.id as dtls_id, b.gmts_item_id, b.order_quantity, b.rate, b.amount, b.order_uom, b.gmts_color_id, c.description, c.process, c.embellishment_type
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
		where a.id=b.mst_id and b.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=295 $cond_mst_dls
		order by a.id";*///die;
		$sql = "SELECT a.id, b.job_no_mst, a.within_group, a.order_no as sales_booking_no, b.id as dtls_id, b.gmts_item_id, b.order_quantity, b.rate, b.amount, b.order_uom, b.gmts_color_id, b.buyer_style_ref
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=295 $cond_mst_dls
		order by a.id";

		//$prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');

		$prev_pi_qnty_sql = "SELECT c.work_order_dtls_id, sum(c.quantity) as quantity from subcon_ord_mst a, subcon_ord_dtls b, com_export_pi_dtls c where a.id=b.mst_id and b.id=c.work_order_dtls_id and a.id=c.work_order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=295 $cond_mst_dls group by c.work_order_dtls_id ";

		$prev_qty_data_array=sql_select($prev_pi_qnty_sql);
		foreach($prev_qty_data_array as $row)
		{
			$prev_pi_qnty_arr_dtls[$row[csf('work_order_dtls_id')]]=$row[csf('quantity')];
		}


		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{
			//$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
			
			/*if($row[csf('process')]==1) $process_embl_type=$wash_wet_process;
			else if($row[csf('process')]==2) $process_embl_type=$wash_dry_process;
			else if($row[csf('process')]==3) $process_embl_type=$wash_laser_desing;
			else */
			
			$process_embl_type=$blank_array;

			if($row[csf('within_group')]==2) $buyer=$buyer_arr[$row[csf('buyer_id')]];
			else $buyer=$company_arr[$row[csf('buyer_id')]];

			
			$bal_qtny=$row[csf('order_quantity')]-$prev_pi_qnty_arr_dtls[$row[csf('dtls_id')]]-$curr_data[$row[csf('dtls_id')]]['quantity'];
			$amount=$row[csf('rate')]*$bal_qtny;
			if($bal_qtny>0)
			{
				$tblRow++;
				if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('job_no_mst')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('dtls_id')]; ?>" readonly />
					</td>
                    <td> 
						<input type="text" name="jobstyle_<? echo $tblRow; ?>" id="jobstyle_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('buyer_style_ref')]; ?>" style="width:110px" disabled="disabled"/>
					</td>
		            <td> 
						<input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('sales_booking_no')]; ?>" style="width:110px" disabled="disabled"/>
					</td>
		            <td>
		                <input type="text" name="gsmItem_<? echo $tblRow; ?>" id="gsmItem_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $garments_item[$row[csf('gmts_item_id')]]; ?>" style="width:110px; text-align: left;" disabled="disabled"/>
		                <input type="hidden" name="hideGsmItem_<? echo $tblRow; ?>" id="hideGsmItem_<? echo $tblRow; ?>" value="<? echo $row[csf('gmts_item_id')]; ?>"/>
		            </td>
					<td>
		                <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('gmts_color_id')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('gmts_color_id')]; ?>"/>
		            </td>
		            <td style="display:none"> 
						<input type="text" name="itemDesc_<? echo $tblRow; ?>" id="itemDesc_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:70px" disabled="disabled"/>
					</td>
		            <td style="display:none">
		                <input type="text" name="processEmbl_<? echo $tblRow; ?>" id="processEmbl_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideProcessEmbl_<? echo $tblRow; ?>" id="hideProcessEmbl_<? echo $tblRow; ?>" value=""/>
		            </td>
		            <td style="display:none">
		                <input type="text" name="washType_<? echo $tblRow; ?>" id="washType_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:80px" disabled="disabled"/>
		                <input type="hidden" name="hideWashType_<? echo $tblRow; ?>" id="hideWashType_<? echo $tblRow; ?>" value=""/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('order_uom')],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;"/>
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $amount; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
					</td>
				</tr>
				<?
			}		
		}
	}

	else if($item_category_id==36) // Gmts emb
	{
		if($based_on==2) $cond_mst_dls="and c.id in($wo_dtls_id)"; else $cond_mst_dls=" and b.mst_id in ($wo_mst_id)";
		
		$sql = "SELECT a.id, b.job_no_mst, a.within_group, a.order_no as sales_booking_no, c.id as dtls_id, b.gmts_item_id, b.main_process_id, b.embl_type, b.body_part, b.order_quantity, b.rate, b.amount, b.order_uom, c.description, c.color_id, c.size_id, c.qnty, c.amount, c.rate
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
		where a.id=b.mst_id and b.id=c.mst_id and b.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.entry_form=311 $cond_mst_dls
		order by a.id";//die;

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');

		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{
			$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);

			if($row[csf('within_group')]==2) $buyer=$buyer_arr[$row[csf('buyer_id')]];
			else $buyer=$company_arr[$row[csf('buyer_id')]];

			
			
			if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$bal_qtny=$row[csf('qnty')]-$prev_pi_qnty_arr_dtls[$row[csf('dtls_id')]];	
			$amount=$row[csf('rate')]*$bal_qtny;	
			if($bal_qtny>0)
			{
				$tblRow++;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('job_no_mst')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('dtls_id')]; ?>" readonly />
					</td>
		            <td> 
						<input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('sales_booking_no')]; ?>" style="width:110px" disabled="disabled"/>
					</td>
		            <td>
		                <input type="text" name="gsmItem_<? echo $tblRow; ?>" id="gsmItem_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $garments_item[$row[csf('gmts_item_id')]]; ?>" style="width:60px" disabled="disabled"/>
		                <input type="hidden" name="hideGsmItem_<? echo $tblRow; ?>" id="hideGsmItem_<? echo $tblRow; ?>" value="<? echo $row[csf('gmts_item_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="bodypart_<? echo $tblRow; ?>" id="bodypart_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $body_part[$row[csf('body_part')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideBodypart_<? echo $tblRow; ?>" id="hideBodypart_<? echo $tblRow; ?>" value="<? echo $row[csf('body_part')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="processEmbl_<? echo $tblRow; ?>" id="processEmbl_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $emblishment_name_array[$row[csf('main_process_id')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideProcessEmbl_<? echo $tblRow; ?>" id="hideProcessEmbl_<? echo $tblRow; ?>" value="<? echo $row[csf('main_process_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="emblType_<? echo $tblRow; ?>" id="emblType_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $type_array[$row[csf('embl_type')]][$row[csf('embl_type')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideEmblType_<? echo $tblRow; ?>" id="hideEmblType_<? echo $tblRow; ?>" value="<? echo $row[csf('embl_type')]; ?>"/>
		            </td>
		            <td> 
						<input type="text" name="itemDesc_<? echo $tblRow; ?>" id="itemDesc_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('description')]; ?>" style="width:70px" disabled="disabled"/>
					</td>
					<td>
		                <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('size_id')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideitemSize_<? echo $tblRow; ?>" id="hideitemSize_<? echo $tblRow; ?>" value="<? echo $row[csf('size_id')]; ?>"/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('order_uom')],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;"/>
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $amount; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
					</td>
				</tr>
				<?
			}		
		}
	}

	else if($item_category_id==45) // Gmts Trims
	{
		if($based_on==2) $cond_mst_dls="and c.id in($wo_dtls_id)"; else $cond_mst_dls=" and b.mst_id in ($wo_mst_id)";
		
		$sql = "SELECT a.id, b.job_no_mst, a.within_group, a.order_no, c.id as dtls_id, b.item_group, c.rate, b.order_uom, c.description, c.color_id, c.size_id, c.qnty, c.amount
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
		where a.id=b.mst_id and b.id=c.mst_id and b.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=255 $cond_mst_dls
		order by a.id";//die;

		
		$prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');

		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{
			if($row[csf('within_group')]==2) $buyer=$buyer_arr[$row[csf('buyer_id')]];
			else $buyer=$company_arr[$row[csf('buyer_id')]];
			
			
			if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
			$bal_qtny=$row[csf('qnty')]-$prev_pi_qnty_arr_dtls[$row[csf('dtls_id')]];	
			$amount=$row[csf('rate')]*$bal_qtny;	
			if($bal_qtny>0)
			{
				$tblRow++;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('job_no_mst')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('dtls_id')]; ?>" readonly />
					</td>
					<td> 
						<input type="text" name="hscode_<? echo $tblRow; ?>" id="hscode_<? echo $tblRow; ?>" class="text_boxes" value="<? //echo $row[csf('order_no')]; ?>" style="width:60px"/>
					</td>
		            <td> 
						<input type="text" name="salesbooking_<? echo $tblRow; ?>" id="salesbooking_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('order_no')]; ?>" style="width:110px" disabled="disabled"/>
					</td>
		            <td>
		                <input type="text" name="itemGroup_<? echo $tblRow; ?>" id="itemGroup_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $item_group_arr[$row[csf('item_group')]]; ?>" style="width:60px" disabled="disabled"/>
		                <input type="hidden" name="hideGsmItem_<? echo $tblRow; ?>" id="hideGsmItem_<? echo $tblRow; ?>" value="<? echo $row[csf('item_group')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="itemDesc_<? echo $tblRow; ?>" id="itemDesc_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('description')]; ?>" style="width:70px" disabled="disabled"/>
		            </td>
		            <td>
		                <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
		            </td>
		            <td>
		                <input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('size_id')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideitemSize_<? echo $tblRow; ?>" id="hideitemSize_<? echo $tblRow; ?>" value="<? echo $row[csf('size_id')]; ?>"/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('order_uom')],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;"/>
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo number_format($amount,4,".",""); ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
					</td>
				</tr>
				<?
			}		
		}
	}
    else if($item_category_id==67) // Sub con
    {
        $cond_mst_dls=" and b.mst_id in ($wo_mst_id)";

        $sql = "SELECT a.id, b.job_no_mst, a.within_group, b.main_process_id, a.order_no, c.id as dtls_id, b.item_group, c.item_id, c.rate, b.order_uom, c.description, c.color_id, c.size_id, c.qnty, c.amount
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
		where a.id=b.mst_id and b.id=c.order_id and b.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=238 $cond_mst_dls
		order by a.id";//die;

        $prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');

        $data_array=sql_select($sql);
        foreach($data_array as $row)
        {

            if($row[csf('main_process_id')]==2 || $row[csf('main_process_id')]==3 || $row[csf('main_process_id')]==4 || $row[csf('main_process_id')]==6) //|| $process_id==7
            {
                $garments_item=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
            }


            

            if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            $bal_qtny=$row[csf('qnty')]-$prev_pi_qnty_arr_dtls[$row[csf('dtls_id')]];
            $amount=$row[csf('rate')]*$bal_qtny;
            if($bal_qtny>0)
            {
				$tblRow++;
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
                    <td>
                        <input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
                    </td>
                    <td>
                        <input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('job_no_mst')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />
                        <input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('id')]; ?>" readonly />
                        <input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('dtls_id')]; ?>" readonly />
                    </td>
                    <td>
                        <input type="text" name="itemGroup_<? echo $tblRow; ?>" id="itemGroup_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $item_group_arr[$row[csf('item_group')]]; ?>" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemDesc_<? echo $tblRow; ?>" id="itemDesc_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $garments_item[$row[csf('item_id')]]; ?>" style="width:70px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:70px" disabled="disabled"/>
                        <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $size_library[$row[csf('size_id')]]; ?>" style="width:70px" disabled="disabled"/>
                        <input type="hidden" name="hideitemSize_<? echo $tblRow; ?>" id="hideitemSize_<? echo $tblRow; ?>" value="<? echo $row[csf('size_id')]; ?>"/>
                    </td>
                    <td>
                        <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('order_uom')],'',1,''); ?>
                    </td>
                    <td>
                        <input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
                        <input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;"/>
                    </td>
                    <td>
                        <input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
                    </td>
                    <td>
                        <input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo number_format($amount,4,".",""); ?>" style="width:75px;" readonly/>
                        <input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
                    </td>
                </tr>
                <?
            }
        }
    }

	else
	{
		echo "Develop Later";
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
	$bank_arr=return_library_array( "select id, (bank_name||' ( '|| branch_name||' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name');
    ?>
	<div style="width:1000px">
		<?
			$sql_mst = sql_select("select id,pi_number,pi_date,item_category_id,buyer_id,currency_id,pi_validity_date,exporter_id,within_group,last_shipment_date,hs_code,remarks,advising_bank from com_export_pi_mst where id= $data[1]"); 
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
                <td width="110">PI No:</td>
                <td width="200"><? echo $sql_mst[0][csf('pi_number')];?></td>
                <td width="150">Within Group:</td>
                <td><? echo $yes_no[$sql_mst[0][csf('within_group')]];?></td>
            </tr>
            <tr>
                <td></td>
                <td rowspan="4" valign="top"><? echo $company_address ;?></td>
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
            <tr>
            	<td></td>
                <td>Advising Bank:</td>
                <td colspan="3"><? echo $bank_arr[$sql_mst[0][csf('advising_bank')]];?></td>
            </tr> 
            <tr>
            	<td></td>
                <td>Remarks:</td>
                <td colspan="3"><? echo $sql_mst[0][csf('remarks')];?></td>
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
				//echo $sql = "select id, work_order_no, color_id, construction, composition, gsm, dia_width, uom, quantity, rate, amount from com_export_pi_dtls where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0";
				$sql ="select a.id, a.work_order_no, a.color_id, a.construction, a.composition, a.gsm, a.dia_width, a.uom, a.quantity, a.rate, a.amount,b.upcharge, b.discount,b.net_total_amount from com_export_pi_dtls a, com_export_pi_mst b where a.pi_id = b.id and pi_id='$data[1]' and a.quantity>0 and a.status_active=1 and a.is_deleted=0 ";
				$data_array=sql_select($sql);
				foreach($data_array as $row)
				{
				?>
                    <tr>
                        <td><? echo $row[csf('work_order_no')]; ?></td>
                        <td><? echo $row[csf('construction')]; ?></td>
                        <td><? echo $row[csf('composition')]; ?></td>
                        <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
                        <td><? echo $row[csf('gsm')]; ?></td>
                        <td><? echo $row[csf('dia_width')]; ?></td>
                        <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                        <td align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
                    </tr>
				<? 
				$upcharge = $row[csf('upcharge')];
				$discount = $row[csf('discount')];
				$net_total_amount = $row[csf('net_total_amount')];
                } 
                ?>
                <tr>
                    <td align="right" colspan="9">Total</td>
                    <td align="right"><? echo number_format($total_ammount,4); ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="9">Upcharge</td>
                    <td align="right"><? echo number_format($upcharge,4); ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="9">Discount</td>
                    <td align="right"><? echo number_format($discount,4); ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="9">Net Total</td>
                    <td align="right"><? echo number_format($net_total_amount,4); ?></td>
                </tr>
            </tbody> 
        </table>
        <table>
            <tr height="20"></tr>
            <tr>
                <td valign="top"><strong>In-Words: </strong></td>
                <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
            </tr>
            <tr> 
            <tr height="50"></tr>
        </table>
        <? 
        	echo get_spacial_instruction($data[1],"100%",152);
        ?>
        <table>
            <tr height="50"></tr>
            <tr>
            	<td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
            </tr>
            <tr height="50"></tr>
        </table>
	</div>
    <?
	exit();	 
}
 
 /*if($action=="print_new") 
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
		$bank_arr=return_library_array( "select id, (bank_name||' ( '|| branch_name||' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name');
	?>
	<div style="width:1000px">
		<?
			$sql_mst = sql_select("select id,pi_number,pi_date,item_category_id,buyer_id,currency_id,pi_validity_date,exporter_id,within_group,last_shipment_date,hs_code,remarks,advising_bank from com_export_pi_mst where id= $data[1]"); 
		?>
        <table width="100%">
            <tr>
            	<td style="font-size:20px;" align="center" colspan="6">
                	<strong>
						<? 
							echo "PROFORMA INVOICE";
                        ?>
                    </strong>
                </td>
            </tr>
        </table>
        <br>
		<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
            <thead>
            <tr>
                <td colspan="4" valign="top">
                        <b>BUYER:</b>	<br>	
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
                        ?>	<br> <br><br>	
                                
                        <b>Beneficiary</b> :<br>		
                       <? echo $company_arr[$sql_mst[0][csf('exporter_id')]]; ?>			
                
                </td>
                <td colspan="8">
                    Proforma Invoice No	: <? echo $sql_mst[0][csf('pi_number')];?>	<br>					
                    Place Of Loding	: <? echo change_date_format($sql_mst[0][csf('pi_date')]);?>	<br>					
                    Place Of Delivery	: <span style="color:#F00;">Suppliers Factory</span><br>				
                    Country Of Origin	: <span style="color:#F00;">Openers Factory</span><br>					
                    Terms Of Payment	: <span style="color:#F00;">By Confirmed Irrevocable Letter Of Credit.</span><br><br>					
                                            
                    Advising Bank:<br>						
                        <? echo $bank_arr[$sql_mst[0][csf('advising_bank')]];?>				
                </td>
            </tr>
            <tr>
            	<th>Style Ref. No</th>
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
            </tr>
            </thead>
            <tbody>
			<?
				$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" ); $total_ammount = 0; $total_quantity=0;
				$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
				//echo $sql = "select id, work_order_no, color_id, construction, composition, gsm, dia_width, uom, quantity, rate, amount from com_export_pi_dtls where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0";
				$sql ="select a.id, a.work_order_no, a.color_id, a.construction, a.composition, a.gsm, a.dia_width, a.uom, a.quantity, a.rate, a.amount,b.upcharge, b.discount,b.net_total_amount from com_export_pi_dtls a, com_export_pi_mst b where a.pi_id = b.id and pi_id='$data[1]' and a.quantity>0 and a.status_active=1 and a.is_deleted=0 ";
				$data_array=sql_select($sql);
				foreach($data_array as $row)
				{
				?>
                    <tr>
                    	<td><? echo $style_ref_no[$row[csf('work_order_no')]]; ?></td>
                        <td><? echo $row[csf('work_order_no')]; ?></td>
                        <td><? echo $row[csf('construction')]; ?></td>
                        <td><? echo $row[csf('composition')]; ?></td>
                        <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
                        <td><? echo $row[csf('gsm')]; ?></td>
                        <td><? echo $row[csf('dia_width')]; ?></td>
                        <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                        <td align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
                    </tr>
				<? 
				$upcharge = $row[csf('upcharge')];
				$discount = $row[csf('discount')];
				$net_total_amount = $row[csf('net_total_amount')];
                } 
                ?>
                <tr>
                    <td align="right" colspan="10">Total</td>
                    <td align="right"><? echo number_format($total_ammount,4); ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="10">Upcharge</td>
                    <td align="right"><? echo number_format($upcharge,4); ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="10">Discount</td>
                    <td align="right"><? echo number_format($discount,4); ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="10">Net Total</td>
                    <td align="right"><? echo number_format($net_total_amount,4); ?></td>
                </tr>
            </tbody> 
        </table>
        <table>
            <tr height="20"></tr>
            <tr>
                <td valign="top"><strong>In-Words: </strong></td>
                <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
            </tr>
            <tr> 
            <tr height="50"></tr>
        </table>
        <? 
        	echo get_spacial_instruction($data[1],"100%",152);
        ?>
        <table>
            <tr height="50"></tr>
            <tr>
            	<td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
            </tr>
            <tr height="50"></tr>
        </table>
	</div>
<?
	exit();	 
 }*/
 
if($action=="print_new") 
{
	$data = explode('*',$data);
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	$country_name_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $country_name_arr[$company_data[csf('country_id')]].','.' ';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
	if ($db_type==0)
	{
		$bank_arr=return_library_array( "select id, concat(bank_name, ' ( ',branch_name,' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name'); 
	}
	else
	{
		$bank_arr=return_library_array( "select id, (bank_name||' ( '|| branch_name||' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name');
	}
	
	$bank_address_arr=return_library_array( "select id, address as address from lib_bank where advising_bank=1",'id','address');					    				    $lib_buyer_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
    ?>
	<div style="width:1000px">
		<?
			$sql_mst = sql_select("select id,pi_number,pi_date,item_category_id,buyer_id,currency_id,pi_validity_date,exporter_id,within_group,last_shipment_date,hs_code,remarks,advising_bank from com_export_pi_mst where id= $data[1]"); 
		?>
        <table width="100%">
            <tr>
            	<td style="font-size:20px;" align="center" colspan="6">
                	<strong>
						<? 
							echo "PROFORMA INVOICE";
                        ?>
                    </strong>
                </td>
            </tr>
        </table>
        <br>
		<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
            <thead>
            <tr>
                <td colspan="4" valign="top">
                        <b>BUYER:<br></b>	
                       <? 
							if($sql_mst[0][csf('within_group')]==1)
							{
                            	$buyer=$company_arr[$sql_mst[0][csf('buyer_id')]].$lib_buyer_arr[$sql_mst[0][csf('buyer_id')]];
							}
							else
							{
								
								$buyer=return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0][csf('buyer_id')]."'");
								//$buyer=$lib_buyer_arr[$sql_mst[0][csf('buyer_id')]].$lib_buyer_arr[$sql_mst[0][csf('buyer_id')]];;///return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0][csf('buyer_id')]."'");
							}
							echo $buyer."<br>".$lib_buyer_arr[$sql_mst[0][csf('buyer_id')]];
                        ?>	<br> <br><br>	
                                
                        <b>Beneficiary:	</b><br>		
                       <? echo $company_arr[$sql_mst[0][csf('exporter_id')]]."<br>".$company_plot_no_arr[$sql_mst[0][csf('exporter_id')]]; ?>			
                
                </td>
                <td colspan="8">
                    Proforma Invoice No	: <? echo $sql_mst[0][csf('pi_number')];?>	<br>					
                    Place Of Loding	: <? echo change_date_format($sql_mst[0][csf('pi_date')]);?>	<br>					
                    Place Of Delivery	:Suppliers Factory<br>				
                    Country Of Origin	:Openers Factory<br>					
                    Terms Of Payment	:By Confirmed Irrevocable Letter Of Credit.<br><br>					
                                            
                    Advising Bank:<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;					
                        <? echo $bank_arr[$sql_mst[0][csf('advising_bank')]]."<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$bank_address_arr[$sql_mst[0][csf('advising_bank')]];?>				
                </td>
            </tr>
            <tr>
            	<th>Style Ref. No</th>
                <th>Job No</th>
                <th>Construction</th>
                <th width="180">Composition</th>
                <th>Color</th>					
                <th>GSM</th>
                <th>Dia/Width</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
			<?
				$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" ); $total_ammount = 0; $total_quantity=0;
				$style_ref_no=return_library_array( "select job_no,style_ref_no from fabric_sales_order_mst", "Job_no", "Style_ref_no" );
				//echo $sql = "select id, work_order_no, color_id, construction, composition, gsm, dia_width, uom, quantity, rate, amount from com_export_pi_dtls where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0";
				$sql ="select a.id, a.work_order_no, a.color_id, a.construction, a.composition, a.gsm, a.dia_width, a.uom, a.quantity, a.rate, a.amount,b.upcharge, b.discount,b.net_total_amount from com_export_pi_dtls a, com_export_pi_mst b where a.pi_id = b.id and pi_id='$data[1]' and a.quantity>0 and a.status_active=1 and a.is_deleted=0 ";
				$data_array=sql_select($sql);
				foreach($data_array as $row)
				{
				?>
                    <tr>
                    	<td><? echo $style_ref_no[$row[csf('work_order_no')]]; ?></td>
                        <td><? echo $row[csf('work_order_no')]; ?></td>
                        <td><? echo $row[csf('construction')]; ?></td>
                        <td><? echo $row[csf('composition')]; ?></td>
                        <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
                        <td align="center"><? echo $row[csf('gsm')]; ?></td>
                        <td align="center"><? echo $row[csf('dia_width')]; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                        <td align="right"><? echo number_format($row[csf('amount')],2); $total_ammount += $row[csf('amount')];  ?></td>
                    </tr>
				<? 
				$upcharge = $row[csf('upcharge')];
				$discount = $row[csf('discount')];
				$net_total_amount = $row[csf('net_total_amount')];
                } 
                ?>
                <tr>
                    <td align="right" colspan="10">Total</td>
                    <td align="right"><? echo number_format($total_ammount,2); ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="10">Upcharge</td>
                    <td align="right"><? echo number_format($upcharge,2); ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="10">Discount</td>
                    <td align="right"><? echo number_format($discount,2); ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="10">Net Total</td>
                    <td align="right"><? echo number_format($net_total_amount,2); ?></td>
                </tr>
            </tbody> 
        </table>
        <table>
            <tr height="20"></tr>
            <tr>
                <td valign="top"><strong>In-Words: </strong></td>
                <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>

            </tr>
            <tr> 
            <tr height="50"></tr>
        </table>
        <? 
        	echo get_spacial_instruction($data[1],"100%",152);
        ?>
        <table>
            <tr height="50"></tr>
            <tr>
            	<td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
            </tr>
            <tr height="50"></tr>
        </table>
	</div>
    <?
	exit();	 
}

if($action=="print_new_rpt_3") 
{
	$data = explode('*',$data);
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	$country_name_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $country_name_arr[$company_data[csf('country_id')]].','.' ';else $country='';
		if($company_data[csf('contact_no')]!=0)$contact_no = $company_data[csf('contact_no')];else $contact_no='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country.$contact_no;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
	if ($db_type==0)
	{
		$bank_arr=return_library_array( "select id, concat(bank_name, ' ( ',branch_name,' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name'); 
	}
	else
	{
		$bank_arr=return_library_array( "select id, (bank_name||' ( '|| branch_name||' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name');
	}
	
	$bank_address_arr=return_library_array( "select id, address as address from lib_bank where advising_bank=1",'id','address');	
	$lib_buyer_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');	
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	?>
	<div style="width:1000px">
		<?
			$sql_export_pi = "select id,pi_number,pi_date,item_category_id,buyer_id,currency_id,pi_validity_date,exporter_id,within_group,last_shipment_date,hs_code,swift_code,remarks,advising_bank from com_export_pi_mst where id= $data[1]";
			//echo $sql_export_pi;
			$sql_mst = sql_select($sql_export_pi); 
		?>
        <table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
            <tr>
            	<td align="center" colspan="11">
                	<h1 style="font-size: 20px;"><?= $company_arr[$data[0]]; ?> </h1>
					<?= $company_address; ?> 
                </td>
            </tr>
            <tr>
            	<td style="font-size:14px; padding: 10px 0px;" align="center" colspan="11">
                	<strong><u><?= "PROFORMA INVOICE";?></u></strong>
                </td>
            </tr>
			<tr>
				<td rowspan="2" style="border-right: 1px solid white; width: 50; vertical-align: top;"> TO MESSARS:</td>
				<td rowspan="2" colspan="4">
                       <? 
							if($sql_mst[0][csf('within_group')]==1)
							{
                            	$buyer=$company_arr[$sql_mst[0][csf('buyer_id')]].'<br>'.$lib_buyer_arr[$sql_mst[0][csf('buyer_id')]];
							}
							else
							{
								
								$buyer=return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0][csf('buyer_id')]."'");
								
							}
							echo $buyer."<br>".$lib_buyer_arr[$sql_mst[0][csf('buyer_id')]];
                        ?>	
				</td>
				<td colspan="6">
					<p>Proforma Invoice No: <strong><? echo $sql_mst[0][csf('pi_number')];?></strong></p>
					<p>Date: <strong><? echo change_date_format($sql_mst[0][csf('pi_date')]);?></strong></p>
				</td>
			</tr>
			<tr>
				<td colspan="6">
					<p>Advising bank: <br/><? echo $bank_arr[$sql_mst[0][csf('advising_bank')]]; ?> <br/>
					<? echo $bank_address_arr[$sql_mst[0][csf('advising_bank')]];?></p>
					<p><span>HS Code:&nbsp;<strong><? echo $sql_mst[0][csf('hs_code')]; ?></strong></span>&nbsp;&nbsp;&nbsp;&nbsp;SWIFT Code:<span>&nbsp;<strong><? echo $sql_mst[0][csf('swift_code')]; ?></strong></span></p>
				</td>

			</tr>
			<tr>
				<th style="width: 40px; text-align: center;">SL. No. </th>
				<th style="width: 70px; text-align: center;">JOB No.</th>
				<th style="width: 70px; text-align: center;">WO/BOOKING</th>
				<th style="width: 70px; text-align: center;">GMTS ITEM</th>
				<th style="width: 70px; text-align: center;">COLOR</th>
				<th style="width: 70px; text-align: center;">WASH TYPE</th>
				<th style="width: 70px; text-align: center;">UOM</th>
				<th style="width: 60px; text-align: center;">QUANTITY</th>
				<th style="width: 60px; text-align: center;">UNIT PRICE<br/>IN USD ($)</th>
				<th style="width: 70px; text-align: center;">UNIT PRICE<br/>PER DZN</th>
				<th style="width: 60px; text-align: center;">AMOUNT<br/>IN USD ($)</th>
			</tr>
			<?
			
			if($db_type==0) $process_type_cond="group_concat(c.process,'*',c.embellishment_type)";
			else if ($db_type==2) $process_type_cond="listagg(c.process||'*'||c.embellishment_type,',') within group (order by c.process||'*'||c.embellishment_type)";
			$sql_export_pi_dtls = "SELECT a.id,a.item_category_id,a.currency_id,b.work_order_no as job_no, b.booking, b.color_id, b.uom, sum(b.quantity) as quantity, b.rate, sum(b.amount) as amount, b.gmts_item_id,$process_type_cond as process_type from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c  where a.id=b.pi_id and  b.work_order_dtls_id=c.id and  a.id= $data[1] and a.is_deleted=0 and b.is_deleted=0 group by a.id,a.item_category_id,a.currency_id,b.work_order_no, b.booking, b.color_id, b.uom, b.rate, b.gmts_item_id";
			
			$exprt_pi_dtls_result = sql_select($sql_export_pi_dtls);
			$i=1;
			foreach ($exprt_pi_dtls_result as $key => $value)
			 {
				 
				$ex_process=array_unique(explode(",",$value[csf("process_type")]));
				$process_name=""; $sub_process_name="";
				foreach($ex_process as $process_data)
				{
					$ex_process_type=explode("*",$process_data);
					$process_id=$ex_process_type[0];
					$type_id=$ex_process_type[1];
					if($process_id==1) $process_type_arr=$wash_wet_process;
					else if($process_id==2) $process_type_arr=$wash_dry_process;
					else if($process_id==3) $process_type_arr=$wash_laser_desing;
					else $process_type_arr=$blank_array;
					
					if($process_name=="") $process_name=$wash_type[$process_id]; else $process_name.=','.$wash_type[$process_id];
					
					if($sub_process_name=="") $sub_process_name=$process_type_arr[$type_id]; else $sub_process_name.=','.$process_type_arr[$type_id];
				}
				
				
				$process_name=implode(",",array_unique(explode(",",$process_name)));
				$sub_process_name=implode(",",array_unique(explode(",",$sub_process_name)));
		
				# code...
			?>
			<tr>
				<td style="width: 40px; text-align: center;"><?= $i;?></td>
				<td style="width: 70px; text-align: center;"><p><?= $value[csf("job_no")]; ?></p></td>
				<td style="width: 70px; text-align: center;"><p><?= $value[csf("booking")]; ?></p></td>
				<td style="width: 70px; text-align: center;"><p><?= $garments_item[$value[csf("gmts_item_id")]]; ?></p></td>
				<td style="width: 70px; text-align: center;"><p><?= $color_arr[$value[csf("color_id")]]; ?></p></td>
				<td style="width: 70px; text-align: center;"><p><?= $sub_process_name; ?></p></td>
				<td style="width: 70px; text-align: center;"><p><?= $unit_of_measurement[$value[csf("uom")]]; ?></p></td>
				<td style="width: 60px; text-align: right;"><p><?= number_format($value[csf("quantity")],2,".",""); $total_qty+=$value[csf("quantity")];?></p></td>
				<td style="width: 60px; text-align: right;"><p><?= number_format($value[csf("rate")],2,".",""); ?></p></td>
				<td style="width: 70px; text-align: right;"><p><?= number_format($value[csf("rate")]*12,2,".",""); ?></p></td>
				<td style="width: 60px; text-align: right;"><p><?= number_format($value[csf("amount")],2,".",""); $total_amt+=$value[csf("amount")]; ?></p></td>
			</tr>
			<?
			$i++; 
			}
			?>
			<tr>
				<td colspan="7" align="right">Total</td>
				<td align="right"><p><?= number_format($total_qty,2,".","");?></p></td>
				<td></td>
				<td></td>
				<td align="right"><p><?= number_format($total_amt,2, '.', '');//$total_amt;?></p></td>
			</tr>
			<tr>
				<td colspan="11" align="center">(<?= number_to_words(number_format($total_amt,2, '.', ''), "USD", "Cent");?>)</td>
			</tr>
        </table>
        <br><style> .terms_cond_table tr td { border: 1px solid white;}</style>
		<table class="terms_cond_table" width="100%" cellspacing="0" rules="all" border="0">
			<tr>
				<td><strong>Remarks</strong></td>
				<td>:<strong>&nbsp;<? echo $sql_mst[0][csf('remarks')]; ?></strong></td>
			</tr>
			<tr>
				<td><em>PAYMENT</em></td>
				<td>:<em> Irrevocable L/C At 90 days Sight Incorporating Export L/C No & Date which shall not be concerned to the openers Export Realisation.</em></td>
			</tr>
			<tr>
				<td><em>SHIPMENT</em></td>
				<td>:<em> Within 30 days from the receiving date of L/C</em></td>
			</tr>
			<tr>
				<td><em>NEGOTIATION</em></td>
				<td>:<em> Within 20 days from the date of shipment.</em></td>
			</tr>
			<tr>
				<td><em>INSURANCE</em></td>
				<td>:<em> Covered by the buyer.</em></td>
			</tr>
			<tr>
				<td><em>INTEREST</em></td>
				<td>:<em> Interest will be paid by the opener for the usance period as per rate  prescribed by the Bangladesh Bank. On advance export affairs. </em></td>
			</tr>
			<tr>
				<td colspan="2"><em>Utilization Declaration : U.D Issue By the L/C opener.</em>
					<ul>
						<li><em><strong><small>U.D copy must be attached by BGMEA Or BKMEA</small></strong> </em></li>
						<li><em>No Claim will be accepted after taking delivery the goods.</em></li>
						<li><em>Over due interest will be paid @16% for delayed period only</em></li>
					</ul>
				</td>
			</tr>
			<tr>
				<td colspan="2">TIN No. 481300693049 &nbsp; BIN: 000288683-0103</td>
			</tr>
			<tr>
				<td colspan="2">IRC No. BA 150911 &nbsp; ERC No: RA 75028</td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
            <tr>
				<td colspan="2">FOR AND ON BEHALF OF</td>
			</tr>
            <tr>
				<td colspan="2"><strong>DHAKA GARMENTS AND WASHING LTD</strong></td>
			</tr>
		</table>
        <? 
        	//echo get_spacial_instruction($data[1],"100%",152);
        ?>
        <table width="100%">
            <tr height="50"></tr>
            <tr>
            	<td style="">Authorized Signature</td>
            	<td style="text-align:right;">
					CONFIRMED<BR/>
					BY THE BUYER<BR/><BR/><BR/>
					SEAL & SIGNATURE
				</td>
            </tr>
            <tr height="50"></tr>
        </table>
	</div>
	<?
	exit();	 
}

if($action=="print_new_rpt_7") 
{
	// $data = explode('*',$data);
	extract($_REQUEST);
	$sql_company = sql_select("SELECT * FROM lib_company WHERE is_deleted=0 and status_active=1");
	$country_name_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$company_info='';
	$company_arr_info='';
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = $company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = $company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = $company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = $company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $country_name_arr[$company_data[csf('country_id')]].','.' ';else $country='';
		if($company_data[csf('contact_no')]!=0)$contact_no = $company_data[csf('contact_no')];else $contact_no='';
		
		if($company_data[csf('bin_no')]!='')$bin_no = $company_data[csf('bin_no')];else $bin_no='';
				

		$company_address[$company_data[csf('ID')]]['ADDRESS'] = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country.$contact_no;
		$company_arr_info[$company_data[csf('ID')]]['COMPANY_NAME'] = $company_data[csf('COMPANY_NAME')];
		$company_bin_arr[$company_data[csf('ID')]] = $bin_no;
	}
	//echo "<pre>";print_r($company_address);die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
	if ($db_type==0)
	{
		$bank_arr=return_library_array( "select id, concat(bank_name, ' ( ',branch_name,' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name'); 
	}
	else
	{
		$bank_arr=return_library_array( "select id, (bank_name||' ( '|| branch_name||' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name');
	}
	
	$bank_address_arr=return_library_array( "select id, (a.bank_name||', '|| a.address||' ')  as address from lib_bank a where a.advising_bank=1",'id','address');	
	$lib_buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');	
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	//$bank_address_arr=return_library_array( "select id, a.bank_name, a.address(||' '|| ||' ')  as address from lib_bank a where a.advising_bank=1",'id','address');
	ob_start();
	?>
	<div style="width:900px">
		<?
			$sql_export_pi = "select id, pi_number, pi_date, item_category_id, buyer_id, currency_id, pi_validity_date, exporter_id, within_group, last_shipment_date, hs_code, swift_code, remarks, advising_bank, internal_file_no from com_export_pi_mst where id= $update_id";
			//echo $sql_export_pi;
			$sql_mst = sql_select($sql_export_pi);
			$advising_bank_id = $sql_mst[0][csf('advising_bank')];
			if ($advising_bank_id != '')
			{
				$sql_bank_ac = sql_select("SELECT b.ACCOUNT_NO, a.SWIFT_CODE, a.CONTACT_NO from lib_bank a, lib_bank_account b where a.id=$advising_bank_id and a.id=b.account_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				$bank_account_no_arr=array();
				foreach ($sql_bank_ac as $value) {
					$bank_account_no_arr[$value['ACCOUNT_NO']] = $value['ACCOUNT_NO'];
					$swift_code = $value['SWIFT_CODE'];
					$contact_no = $value['CONTACT_NO'];
				}
				$bank_account_no = implode(',',$bank_account_no_arr);
			} 
			if($sql_mst[0][csf('within_group')]==1)
			{
				$consignee_name=$company_arr_info[$sql_mst[0][csf('buyer_id')]]['COMPANY_NAME'];
				$consignee_address=$company_address[$sql_mst[0][csf('buyer_id')]]['ADDRESS'];
			}
			else
			{
				$consignee_name=return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0][csf('buyer_id')]."'");
				$consignee_address=return_field_value("address_1","lib_buyer","id='".$sql_mst[0][csf('buyer_id')]."'");	
			} 
			$sql_export_pi_dtls = "select a.id, a.item_category_id, a.currency_id,a.within_group, b.work_order_no as job_no, b.booking, b.color_id, b.uom, b.quantity, b.rate, b.amount, b.gmts_item_id,c.party_buyer_name, c.id as dtls_id, c.buyer_style_ref, c.buyer_po_no 
			from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_dtls c  
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.id= $update_id and a.is_deleted=0 and b.is_deleted=0";
			$mst_id = $update_id;

			$exprt_pi_dtls_result = sql_select($sql_export_pi_dtls);
			$buyer_name=$exprt_pi_dtls_result[0][csf('party_buyer_name')];
			$exporter_id=$sql_mst[0][csf('exporter_id')];
			$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$exporter_id'","image_location");


		?>
        <table width="890" cellspacing="1" border="0">
        	<tr>
            	<td width="90%" align="right" valign="bottom" colspan="9">
                	<strong style="font-size:30px;">Uniglory Washing Ltd</strong>
                </td>
            	<td align="center" rowspan="2">
				<img src="../../<? echo $image_location; ?>" height="50" width="75">
                </td>
            </tr>
        	<tr>
            	<td colspan="9">
                </td>
            </tr>
        	<tr>
            	<td style="font-size:14px; padding: 10px 0px;" align="center" colspan="10">
                	<strong><u><?= "PROFORMA INVOICE";?></u></strong>
                </td>
            </tr>
            <tr>
				<td colspan="10">&nbsp;</td>
			</tr>
			<tr>
				<td width="65%" valign="top" colspan="5">
                <b>Consignee:</b><br>
                <spam style="font-style:italic;">
				<? 
                // echo strtoupper($company_arr[$data[0]])."<br>";
                echo strtoupper($consignee_name)."<br>";
				echo chop($consignee_address,", ")."<br>";
				echo "<b>Buyer : ".strtoupper($buyer_name);
				// if($sql_mst[0][csf("within_group")]==1) echo strtoupper($company_arr[$sql_mst[0][csf("buyer_id")]])."<br>"; else echo strtoupper($lib_buyer_arr[$sql_mst[0][csf("buyer_id")]])."</b><br>";
                ?>
                </spam>	
				</td>
				<td valign="top" colspan="5">
					<spam style="font-style:italic;">PI  No. : <? echo $sql_mst[0][csf('pi_number')]."<br>";?>
                    PI Ref No. : <? echo $sql_mst[0][csf('internal_file_no')]."<br>";?>
					Date : <? echo change_date_format($sql_mst[0][csf('pi_date')]);?></spam>
				</td>
			</tr>
			<tr>
				<td colspan="10">&nbsp;</td>
			</tr>
        </table>
        <table class="rpt_table" width="890" cellspacing="1" rules="all" border="1">
        	<thead>
            	<tr>
                    <th width="30">SL. No. </th>
                    <th width="100">Style</th>
                    <th width="100">PO No</th>
                    <th width="90">Color</th>
                    <th width="120">Description of Goods</th>
                    <th width="150">Wash Type</th>
                    <th width="60">Order Qty. (Pcs)</th>
                    <th width="70">Order Qty. (Dzn)</th>
                    <th width="60">Unit Price USD/Dzn</th>
                    <th>Amount in USD ($)</th>
                </tr>
            </thead>
        	<tbody>
			<?
			
			$all_dtls_id=array();
			foreach($exprt_pi_dtls_result as $row)
			{
				$all_dtls_id[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			}
			if(count($all_dtls_id)>0)
			{
				$con_dtls_sql="select mst_id, process, listagg(cast(embellishment_type as varchar(4000)),',') within group (order by embellishment_type) as embellishment_type
				from subcon_ord_breakdown where status_active=1 and is_deleted=0 and mst_id in(".implode(",",$all_dtls_id).")  and embellishment_type>0
				group by mst_id, process";
				//echo $con_dtls_sql;
				$con_dtls_sql_result = sql_select($con_dtls_sql);
				foreach($con_dtls_sql_result as $row)
				{
					$embtype_data[$row[csf("mst_id")]][$row[csf("process")]]=$row[csf("embellishment_type")];
					$embprocess_data[$row[csf("mst_id")]][$row[csf("process")]]=$row[csf("process")];
					
				}
			}
			
			$i=1;
			foreach ($exprt_pi_dtls_result as $key => $value)
			 {
				 
				/*$ex_process=array_unique(explode(",",$value[csf("process_type")]));
				$process_name=""; $sub_process_name="";
				foreach($ex_process as $process_data)
				{
					$ex_process_type=explode("*",$process_data);
					$process_id=$ex_process_type[0];
					$type_id=$ex_process_type[1];
					if($process_id==1) $process_type_arr=$wash_wet_process;
					else if($process_id==2) $process_type_arr=$wash_dry_process;
					else if($process_id==3) $process_type_arr=$wash_laser_desing;
					else $process_type_arr=$blank_array;
					
					if($process_name=="") $process_name=$wash_type[$process_id]; else $process_name.=','.$wash_type[$process_id];
					
					if($sub_process_name=="") $sub_process_name=$process_type_arr[$type_id]; else $sub_process_name.=','.$process_type_arr[$type_id];
				}
				
				
				$process_name=implode(",",array_unique(explode(",",$process_name)));
				$sub_process_name=implode(",",array_unique(explode(",",$sub_process_name)));*/
		
				?>
                <tr>
                    <td align="center"><?= $i;?></td>
                    <td align="left"><p><?= $value[csf("buyer_style_ref")]; ?></p></td>
                    <td align="left"><p><?= $value[csf("buyer_po_no")]; ?></p></td>
                    <td align="left"><p><?= $color_arr[$value[csf("color_id")]]; ?></p></td>
                    <td align="left"><p><?= $garments_item[$value[csf("gmts_item_id")]]; ?></p></td>
                    <td align="left"><p><?
					$emb_name="";
					$emb_process_arr=$embprocess_data[$value[csf("dtls_id")]];
					foreach($emb_process_arr as $process_id)
					{
						if($embprocess_data[$value[csf("dtls_id")]][$process_id]==1) $process_type=$wash_wet_process;
						else if($embprocess_data[$value[csf("dtls_id")]][$process_id]==2) $process_type=$wash_dry_process;
						else if($embprocess_data[$value[csf("dtls_id")]][$process_id]==3) $process_type=$wash_laser_desing;
						$emb_id_arr=array_unique(explode(",",$embtype_data[$value[csf("dtls_id")]][$process_id]));
						foreach($emb_id_arr as $emb_id)
						{
							$emb_name.=$process_type[$emb_id].",";
						}
					}
					echo chop($emb_name,","); ?></p></td>
                    <td style="text-align: right;"><p><?= number_format($value[csf("quantity")]); $total_qty+=$value[csf("quantity")];?></p></td>
                    <td style="text-align: right;"><p><?= number_format($value[csf("quantity")]/12,2); $total_qty_dzn+=$value[csf("quantity")]/12; $dzn_qty=number_format($value[csf("quantity")]/12,2,".","");?></p></td>
                    <td style="text-align: right;"><p><?= number_format($value[csf("rate")]*12,2,".",""); $dzn_rate=number_format($value[csf("rate")]*12,2);?></p></td>
                    <td style="text-align: right;"><p><?= number_format((($value[csf("quantity")]/12)*($value[csf("rate")]*12)),2); 
					$total_amt+=(($value[csf("quantity")]/12)*($value[csf("rate")]*12)); 
					?></p></td>
                </tr>
                <?
                $i++; 
                }
                ?>
                <tr>
                    <td colspan="6" align="right" style="font-weight:bold;">Total</td>
                    <td align="right" style="font-weight:bold;"><p><?= number_format($total_qty);?></p></td>
                    <td align="right" style="font-weight:bold;"><p><?= number_format($total_qty_dzn,2);?></p></td>
                    <td></td>
                    <td align="right" style="font-weight:bold;"><p><?= number_format($total_amt,2);//$total_amt;?></p></td>
                </tr>
                <tr>
                    <td colspan="10"><strong><?= "Amount in Word : ".number_to_words(number_format($total_amt,2, '.', ''), "USD", "Cent");?></strong></td>
                </tr>
            </tbody>
        </table>
        
        <br>
		<table  width="100%" border="0" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<td colspan="10" height="30" valign="middle"><u><b>Terms & Conditions:</b></u></td>
					</tr>
				</thead>
			<tbody>
		<?php

			$data_array = sql_select("select id, terms,terms_prefix from  wo_booking_terms_condition where booking_no='" . str_replace("'", "", $mst_id) . "' and entry_form=152   order by id");
			if (count($data_array) > 0) 
			{
				$i=0;
				foreach ($data_array as $row) 
				{
					$i++;
					?>
						<tr id="settr_1">
							<td width="3%"><?=$i;?></td>
							<td><? echo $row[csf('terms_prefix')] ;?></td>
							<td colspan="8"><? echo  $row[csf('terms')] ;?></td>
						</tr>
					<?
				}
			}
		?>
			</tbody>
		</table>
        <!-- <?= getTermsConditions($mst_id,"100%",152);?> -->
		
        <br><style> .terms_cond_table tr td { border: 1px solid white;}</style>
		<table class="terms_cond_table" width="100%" cellspacing="0" rules="all" border="0">
			<!--<tr>
				<td colspan="2"><strong>Terms & Conditions:</strong></td>
			</tr>
            <tr>
				<td><strong>1) Payment </strong></td>
				<td><strong>
                : 100% irrecoverable L/C at sight.<br>
                : Payment to be made on maturity in USD.</strong>
                </td>
			</tr>
			<tr>
				<td><strong>2) Variance</strong></td>
				<td><strong> : Both Quantity & Price should be allowed 5% Plus / Minus based on actual delivered Qty.</strong></td>
			</tr>
			<tr>
				<td><em>3) PI Validity</em></td>
				<td> :<em> 20 working days from the PI issue date.</em></td>
			</tr>
			<tr>
				<td><em>4) Transport</em></td>
				<td> :<em> Covered  Van / Any suitable media.</em></td>
			</tr>
			<tr>
				<td><em>5) Delivery of Garments</em></td>
				<td> :<em> Delivery of Garments will be started after receiving of the L/C.</em></td>
			</tr>
			<tr>
				<td><em>6) Negotiation</em></td>
				<td> :<em> Within 15 days from the date of shipment.  </em></td>
			</tr>
			<tr>
				<td><strong>7) Rejection</strong></td>
				<td> :<strong> Denim 1.25% and Non-Denim .25% rejection is acceptable.</strong></td>
			</tr>
            <tr>
				<td><em>8) Partial Shipment</em></td>
				<td> :<em> Allowed for partial shipment with partial documents with maturity.</em></td>
			</tr>
			<tr>
				<td><em>9) Maturity date </em></td>
				<td> :<em> Maturity date is to be counted  from the date of last delivery of goods.</em></td>
			</tr>
			<tr>
				<td><em>10) Over Due Interest</em></td>
				<td> :<em> Over due interest will be paid by the customer at 18.5%.</em></td>
			</tr>
			<tr>
				<td><em>11) U.D.</em></td>
				<td> :<em> U.D. will be supplied to the beneficiary within 10 days of opening of L/C by the Customer.</em></td>
			</tr>
			<tr>
				<td><em>12) L/C Processing Charge</em></td>
				<td> :<em> Will be beared by the Customer.</em></td>
			</tr>
            <tr>
				<td><em>13) Reimbursement Charge</em></td>
				<td> :<em> All reimbursement charges will be beared by the Customer.</em></td>
			</tr>
			<tr>
				<td><em>14) Discrepancy Charge</em></td>
				<td> :<em> All discrepancy charges will be entertained by the Customer.</em></td>
			</tr>-->
			<!-- <tr>
				<td width="100"><strong>BIN Number</strong></td>
				<td><strong>: <? echo $company_bin_arr[$sql_mst[0][csf("exporter_id")]]; ?>.</strong></td>
			</tr> -->
            <tr>
				<td colspan="10">&nbsp;</td>
			</tr>
            <tr>
				<td colspan="10">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="10" style="font-weight:bold;">ADVISING BANK: <? echo strtoupper($bank_address_arr[$sql_mst[0][csf("advising_bank")]]); ?>, PHONE-<? echo $contact_no; ?></td>
			</tr>
			<tr>
				<td colspan="10" style="font-weight:bold;">BENEFICIARY -  <? echo strtoupper($company_arr[$cbo_exporter_id]);?> SWIFT CODE - <? echo $swift_code; ?>, ACCOUNT NO - <? echo $bank_account_no; ?></td>
			</tr>
		</table>
        
        
        
        <table width="100%">
            <tr height="40"></tr>
            <tr>
            	<td colspan="5" style="font-weight:bold;"><? echo strtoupper($company_arr[$cbo_exporter_id]);?></td>
            	<td colspan="5" style="text-align:right; font-weight:bold">Confirmed By the Buyer</td>
            </tr>
            <tr height="100"></tr>
            <tr>
            	<td colspan="5"><span style="border-top-style:dashed">Authorized Signature & Seal</span></td>
            	<td colspan="5" style="text-align:right;"><span style="border-top-style:dashed">Signature & Seal</span></td>
            </tr>
        </table>
	</div>
	<?
		$report_cat=100;
		$html = ob_get_contents();
		ob_clean();
		//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
		foreach (glob("tb*.xls") as $filename) {
		//if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename="tb".$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$filename****$html****$report_cat";
	exit();	 
}

//This button created by Rakib
if($action==='print_new_rpt_4') 
{	
	list($company_id, $mst_id, $system_id) = explode('*', $data);
	//echo $company_id.'**'.$mst_id.'**'.$system_id;
	$sql_company = sql_select("SELECT COMPANY_NAME, CONTRACT_PERSON, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, CITY, ZIP_CODE, COUNTRY_ID, CONTACT_NO, EMAIL FROM lib_company WHERE id=$company_id and status_active=1 and is_deleted=0");
	$country_name_arr=return_library_array("select id, country_name from lib_country where status_active=1",'id','country_name');
	$plot_no=$level_no=$road_no=$block_no='';
	$city=$zip_code=$country=$contact_no='';
  	foreach($sql_company as $company_data) 
  	{
		if ($company_data['PLOT_NO'] !='') $plot_no = 'Plot No.#'.$company_data['PLOT_NO'].','.' ';
		if ($company_data['LEVEL_NO'] !='') $level_no = 'Level No.#'.$company_data['LEVEL_NO'].','.' ';
		if ($company_data['ROAD_NO'] !='') $road_no = 'Road No.#'.$company_data['ROAD_NO'].','.' ';
		if ($company_data['BLOCK_NO'] !='') $block_no = 'Block No.#'.$company_data['BLOCK_NO'].','.' ';
		if ($company_data['CITY'] !='') $city = $company_data['CITY'].','.' ';
		if ($company_data['ZIP_CODE'] !='') $zip_code = '-'.$company_data['ZIP_CODE'].','.' ';
		if ($company_data['COUNTRY_ID'] !=0) $country = $country_name_arr[$company_data['COUNTRY_ID']].','.' ';
		if ($company_data['CONTACT_NO'] !=0) $contact_no = $company_data['CONTACT_NO'];
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.'<br>'.$city.$zip_code.$country.$contact_no;
	}

	if ($db_type==0)
	{
		$bank_arr=return_library_array( "select id, concat(bank_name, ' ( ',branch_name,' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name');
	}
	else
	{
		$bank_arr=return_library_array( "select id, (bank_name||' ( '|| branch_name||' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name');
	}
	
	$bank_address_arr=return_library_array( "select id, address as address from lib_bank where advising_bank=1",'id','address');
	$company_arr = return_library_array( "select id, company_name from lib_company where status_active=1",'id','company_name');
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer where status_active=1",'id','buyer_name');
	$color_arr = return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$item_group_arr = return_library_array( "select id, item_name from lib_item_group where status_active=1",'id','item_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_id'","image_location");

	$sql_export_pi = "SELECT ID, PI_NUMBER, PI_DATE, ITEM_CATEGORY_ID, BUYER_ID, CURRENCY_ID, PI_VALIDITY_DATE, EXPORTER_ID, WITHIN_GROUP, LAST_SHIPMENT_DATE, HS_CODE, SWIFT_CODE, REMARKS, ADVISING_BANK from com_export_pi_mst where id= $mst_id";
	//echo $sql_export_pi;
	$sql_mst = sql_select($sql_export_pi);
	$buyer_id = $sql_mst[0]['BUYER_ID'];

	$sql_buyer=sql_select("SELECT BUYER_NAME, ADDRESS_1, CONTACT_PERSON, EXPORTERS_REFERENCE, BUYER_EMAIL from lib_buyer where id=$buyer_id and status_active=1 and is_deleted=0");
	$advising_bank_id = $sql_mst[0]['ADVISING_BANK'];
	if ($advising_bank_id != '')
	{
		$sql_bank_ac = sql_select("SELECT b.ACCOUNT_NO, a.SWIFT_CODE from lib_bank a, lib_bank_account b where a.id=$advising_bank_id and a.id=b.account_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$bank_account_no_arr=array();
		foreach ($sql_bank_ac as $value) {
			$bank_account_no_arr[$value['ACCOUNT_NO']] = $value['ACCOUNT_NO'];
			$swift_code = $value['SWIFT_CODE'];
		}
		$bank_account_no = implode(',',$bank_account_no_arr);
	}

	/*$sql_export_pi_dtls = "SELECT a.ID, a.ITEM_CATEGORY_ID, a.CURRENCY_ID, b.work_order_no as JOB_NO, b.HS_CODE, b.BOOKING, b.ITEM_DESC, b.GMTS_ITEM_ID, b.COLOR_ID, b.UOM, b.QUANTITY, b.RATE, b.AMOUNT, d.BUYER_BUYER, d.BUYER_PO_NO, d.BUYER_STYLE_REF from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c, subcon_ord_dtls d where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and a.exporter_id=$company_id and a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.id, a.item_category_id, a.currency_id, b.work_order_no, b.hs_code, b.booking, b.item_desc, b.gmts_item_id, b.color_id, b.uom, b.quantity, b.rate, b.amount, d.buyer_buyer, d.buyer_po_no, d.buyer_style_ref";*/
	$sql_export_pi_dtls = "SELECT a.ID, a.ITEM_CATEGORY_ID, a.CURRENCY_ID, b.work_order_no as JOB_NO, b.HS_CODE, b.BOOKING, b.ITEM_DESC, b.GMTS_ITEM_ID, b.COLOR_ID, b.UOM, b.QUANTITY, b.RATE, b.AMOUNT, d.BUYER_BUYER, d.BUYER_PO_NO, d.BUYER_STYLE_REF from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c, subcon_ord_dtls d where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and a.exporter_id=$company_id and a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	
	$exprt_pi_dtls_result = sql_select($sql_export_pi_dtls);
	?>

	<div style="width:1100px">		
        <table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
            <tr>
            	<td colspan="3" style="border-right: 0px;">
            		<img src="../../<? echo $image_location; ?>" height="70" width="250" style="float:left;">
            	</td>
            	<td align="right" colspan="8" style="border-left: 0px;">
                	<strong style="font-size: xx-large; margin-bottom: 10px;"><? echo $company_arr[$company_id]; ?></strong><br>
					<? echo $company_address; ?> 
                </td>
            </tr>
            <tr>
            	<td style="font-size:18px; padding: 10px 0px;" align="center" colspan="11">
                	<strong><? echo 'PROFORMA INVOICE'; ?></strong>
                </td>
            </tr>
            <tr>
            	<td colspan="5" style="vertical-align: top;">
                	<p><strong><? echo $sql_mst[0]['PI_NUMBER']; ?></strong><br>Date:&nbsp;&nbsp;<? echo $sql_mst[0]['PI_DATE']; ?></p>
                </td>
                <td colspan="6" style="vertical-align: top;">
                	<p>Advising Bank:<br/><strong><? echo $bank_arr[$advising_bank_id]; ?></strong><br/><? echo $bank_address_arr[$sql_mst[0]['ADVISING_BANK']]; ?>,&nbsp;SWIFT: <? echo $swift_code; ?><br>A/C No:&nbsp;<? echo $bank_account_no; ?></p>
                </td>
            </tr>

			<tr>
				<?
        		if ($sql_mst[0]['WITHIN_GROUP'] == 1)
        		{
        			$sql_company2 = sql_select("SELECT COMPANY_NAME, CONTRACT_PERSON, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, CITY, ZIP_CODE, COUNTRY_ID, CONTACT_NO, EMAIL FROM lib_company WHERE id=$buyer_id and is_deleted=0 and status_active=1");
					$country_name_arr2=return_library_array( "select id, country_name from lib_country where status_active=1",'id','country_name');
					$plot_no2=$level_no2=$road_no2=$block_no2='';
					$zip_code2=$country2=$contact_no2='';
				  	foreach($sql_company2 as $company_data2)
				  	{
						if ($company_data2['PLOT_NO'] !='') $plot_no2 = 'Plot No.#'.$company_data2['PLOT_NO'].','.' ';
						if ($company_data2['LEVEL_NO'] !='') $level_no2 = 'Level No.#'.$company_data2['LEVEL_NO'].','.' ';
						if ($company_data2['ROAD_NO'] !='') $road_no2 = 'Road No.#'.$company_data2['ROAD_NO'].','.' ';
						if ($company_data2['BLOCK_NO'] !='') $block_no2 = 'Block No.#'.$company_data2['BLOCK_NO'].','.' ';
						if ($company_data2['CITY'] !='') $city2 = $company_data2['CITY'].','.' '; else $city2='';
						if ($company_data2['ZIP_CODE'] !='') $zip_code2 = '-'.$company_data2['ZIP_CODE'].','.' ';
						if ($company_data2['COUNTRY_ID'] !=0) $country2 = $country_name_arr2[$company_data2['COUNTRY_ID']].','.' ';
						if ($company_data2['CONTACT_NO'] !=0) $contact_no2 = $company_data2['CONTACT_NO'];
						
						$company_address2 = $plot_no2.$level_no2.$road_no2.$block_no2.'<br>'.$city2.$zip_code2.$country2.$contact_no2;
					}
        			?>
	            	<td colspan="5" style="vertical-align: top;">            			
	                	<p>Applicant<br><strong><? echo $company_arr[$buyer_id]; ?></strong><br><? echo $company_address2; ?><br>Contact Person:&nbsp;<? echo $sql_company2[0]['CONTRACT_PERSON']; ?><br>Cell:&nbsp;<? echo $sql_company2[0]['CONTACT_NO']; ?><br>E-mail:&nbsp;<? echo $sql_company2[0]['EMAIL']; ?></p>        		
	                </td>
                	<?
                }	
            	else
            	{
            		?>
					<td colspan="5" style="vertical-align: top;">            			
	                	<p>Applicant<br><strong><? echo $sql_buyer[0]['BUYER_NAME']; ?></strong><br><? echo $sql_buyer[0]['ADDRESS_1']; ?><br>Contact Person:&nbsp;<? echo $sql_buyer[0]['CONTACT_PERSON']; ?><br>Cell:&nbsp;<? echo $sql_buyer[0]['EXPORTERS_REFERENCE']; ?><br>E-mail:&nbsp;<? echo $sql_buyer[0]['BUYER_EMAIL']; ?></p>               		
	                </td>
            		<?
            	}
            	?>
                <td colspan="6" style="vertical-align: top;">
                	<p>Beneficiary<br><strong><? echo $sql_company[0]['COMPANY_NAME']; ?></strong><br><? echo $company_address; ?><br>Contact Person:&nbsp;<? echo $sql_company[0]['CONTRACT_PERSON']; ?><br>Cell:&nbsp;<? echo $sql_company[0]['CONTACT_NO']; ?><br>E-mail:&nbsp;<? echo $sql_company[0]['EMAIL']; ?></p>
                </td>
            </tr>

            <tr>
            	<td colspan="11">
            		<p>We are pleased to offer the under mentioned as per conditions and details as follows:</p>
                </td>
            </tr>            
			
			<tr style="background-color: #BCBCBC;">
				<th style="width: 30px; text-align: center;">SL No</th>
				<th style="width: 150px; text-align: center;">Job Order No</th>
				<th style="width: 100px; text-align: center;">Item Group</th>
				<th style="width: 100px; text-align: center;">Mesurement</th>
				<th style="width: 80px; text-align: center;">HS Code</th>
				<th style="width: 100px; text-align: center;">Buyer's Buyer</th>
				<th style="width: 150px; text-align: center;">Buyer's Order<br>Buyer's Style No</th>			
				<th style="width: 100px; text-align: center;">Quantity</th>
				<th style="width: 80px; text-align: center;">UOM</th>
				<th style="width: 80px; text-align: center;">Unit Price</th>				
				<th style="width: 100px; text-align: center;">Amount&nbsp;<? echo $currency[$sql_mst[0]['CURRENCY_ID']]; ?></th>
			</tr>
			<?			
			$i=1;
			foreach ($exprt_pi_dtls_result as $key => $value)
			{
				if (fmod($i,2) == 0) $bgcolor="#E9F3FF"; 
				else $bgcolor="#FFFFFF";				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td style="width: 30px; text-align: center;"><? echo $i;?></td>
					<td style="width: 150px; text-align: center;"><p><? echo $value['JOB_NO']; ?></p></td>
					<td style="width: 100px; text-align: center;"><p><? echo $item_group_arr[$value['GMTS_ITEM_ID']]; ?></p></td>
					<td style="width: 100px; text-align: center;"><p><? echo $value['ITEM_DESC']; ?></p></td>
					<td style="width: 80px; text-align: center;"><p><? echo $value['HS_CODE']; ?></p></td>
					<td style="width: 100px; text-align: center;"><p>
						<? if ($sql_mst[0]['WITHIN_GROUP'] == 1) echo $buyer_arr[$value['BUYER_BUYER']]; else echo $value['BUYER_BUYER']; ?></p></td>
					<td style="width: 150px; text-align: center;"><p><? echo $value['BUYER_PO_NO'].'<br>'.$value['BUYER_STYLE_REF']; ?></p></td>
					<td style="width: 100px; text-align: right;"><p><? echo number_format($value['QUANTITY'], 2, '.', ''); $total_qty+=$value['QUANTITY'];?></p></td>
					<td style="width: 80px; text-align: center;"><p><? //echo number_format($value["RATE")],2,".",""); ?><? echo $unit_of_measurement[$value['UOM']]; ?></p></td>
					<td style="width: 80px; text-align: right;"><p><? echo number_format($value['RATE'], 2, '.', ''); ?></p></td>
					<td style="width: 100px; text-align: right;"><p><? echo number_format($value['AMOUNT'], 2, '.', ''); $total_amt+=$value['AMOUNT']; ?></p></td>
				</tr>
				<?
				$i++; 
			}
			?>
			<tr style="background-color: #BCBCBC;">
				<td colspan="7" align="right"><strong>Total</strong></td>
				<td align="right"><p><strong><? echo number_format($total_qty, 2, '.', '');?></strong></p></td>
				<td></td>
				<td></td>
				<td align="right"><p><strong><? echo number_format($total_amt, 2, '.', ''); ?></strong></p></td>
			</tr>			
        </table>
        <br>
		<table>
			<tr style="background-color: #FABBAC">
				<td colspan="11" width="1100" align="left"><p><strong>In Word:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo number_to_words(number_format($total_amt,2, '.', ''), 'USD', 'Cent'); ?></strong></p></td>
			</tr>
		</table>
        <br><style> .terms_cond_table tr td { border: 1px solid white;}</style>

		<table class="terms_cond_table" width="100%" cellspacing="0" rules="all" border="0">
			<tr>
				<td><strong>Terms and Conditions</strong></td>							
			</tr>
			
		</table>
        <? 
        	echo get_spacial_instruction($mst_id,"50%",152);
        ?>
        <br>
        <table width="1100">
            <tr height="50">
            	<td width="100">&nbsp;</td>
            	<td width="230" align="center">CONFIRMED</td>
            	<td width=""></td>
            	<td width="200" style="font-size: 20px; font-style: italic" align="center"><strong><? echo $company_arr[$company_id]; ?></strong></td>
            	<td width="100"></td>
            </tr>
            <tr height="50"></tr>
            <tr>
            	<td width="100">&nbsp;</td>
            	<td width="230" align="center" style="border-top: 1px solid black;">Buyer's Signature & Company Seal</td>
            	<td width=""></td>
            	<td width="150" align="center" style="border-top: 1px solid black;">Authorized Signature</td>
            	<td width="100"></td>
            </tr>
            <tr height="50"></tr>
        </table>
	</div>
	<?
	exit();	 
}

//This button created by Rakib
if($action=="print_new_rpt_5") 
{
	list($company_id, $mst_id, $system_id) = explode('*', $data);
	$sql_company = sql_select("SELECT COMPANY_NAME, CONTRACT_PERSON, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, CITY, ZIP_CODE, COUNTRY_ID, CONTACT_NO, EMAIL FROM lib_company WHERE id=$company_id and is_deleted=0 and status_active=1");
	$country_name_arr=return_library_array( "select id, country_name from lib_country where status_active=1",'id','country_name');  	
	$plot_no=$level_no=$road_no=$block_no='';
	$city=$zip_code=$country=$contact_no='';
  	foreach($sql_company as $company_data) 
  	{
		if ($company_data['PLOT_NO'] !='') $plot_no = 'Plot No.#'.$company_data['PLOT_NO'].','.' ';
		if ($company_data['LEVEL_NO'] !='') $level_no = 'Level No.#'.$company_data['LEVEL_NO'].','.' ';
		if ($company_data['ROAD_NO'] !='') $road_no = 'Road No.#'.$company_data['ROAD_NO'].','.' ';
		if ($company_data['BLOCK_NO'] !='') $block_no = 'Block No.#'.$company_data['BLOCK_NO'].','.' ';
		if ($company_data['CITY'] !='') $city = $company_data['CITY'].','.' ';
		if ($company_data['ZIP_CODE'] !='') $zip_code = '-'.$company_data['ZIP_CODE'].','.' ';
		if ($company_data['COUNTRY_ID'] !=0) $country = $country_name_arr[$company_data['COUNTRY_ID']].','.' ';
		if ($company_data['CONTACT_NO'] !=0) $contact_no = $company_data['CONTACT_NO'];
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country.$contact_no;
	}
	
	if ($db_type==0)
	{
		$bank_arr=return_library_array( "select id, concat(bank_name, ' ( ',branch_name,' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name'); 
	}
	else
	{
		$bank_arr=return_library_array( "select id, (bank_name||' ( '|| branch_name||' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name');
	}
	
	$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company where status_active=1",'id','plot_no');
	$bank_address_arr=return_library_array( "select id, address as address from lib_bank where advising_bank=1",'id','address');	
	$lib_buyer_arr=return_library_array( "select id, address_1 from lib_buyer where status_active=1",'id','address_1');	
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	?>
	<div style="width:1080px">
		<?
			$sql_export_pi = "SELECT ID, PI_NUMBER, PI_DATE, ITEM_CATEGORY_ID, BUYER_ID, CURRENCY_ID, PI_VALIDITY_DATE, EXPORTER_ID, WITHIN_GROUP, LAST_SHIPMENT_DATE, HS_CODE, SWIFT_CODE, REMARKS, ADVISING_BANK from com_export_pi_mst where id=$mst_id";
			//echo $sql_export_pi;
			$sql_mst = sql_select($sql_export_pi); 
		?>
        <table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
            <tr>
            	<td align="center" colspan="12">
                	<h1 style="font-size: 20px;"><?= $company_arr[$company_id]; ?> </h1>
					<?= $company_address; ?> 
                </td>
            </tr>
            <tr>
            	<td style="font-size:14px; padding: 10px 0px;" align="center" colspan="12">
                	<strong><u><?= "PROFORMA INVOICE";?></u></strong>
                </td>
            </tr>
			<tr>
				<td rowspan="2" style="border-right: 1px solid white; width: 50; vertical-align: top;"> TO MESSARS:</td>
				<td rowspan="2" colspan="4">
                       <? 
							if($sql_mst[0]['WITHIN_GROUP']==1)
							{
                            	$buyer=$company_arr[$sql_mst[0]['BUYER_ID']].'<br>'.$lib_buyer_arr[$sql_mst[0]['BUYER_ID']];
							}
							else
							{
								
								$buyer=return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0]['BUYER_ID']."'");
								
							}
							echo $buyer."<br>".$lib_buyer_arr[$sql_mst[0]['BUYER_ID']];
                        ?>	
				</td>
				<td colspan="7">
					<p>Proforma Invoice No: <strong><? echo $sql_mst[0]['PI_NUMBER'];?></strong></p>
					<p>Date: <strong><? echo change_date_format($sql_mst[0]['PI_DATE']);?></strong></p>
				</td>
			</tr>
			<tr>
				<td colspan="7">
					<p>Advising bank: <br/><? echo $bank_arr[$sql_mst[0]['ADVISING_BANK']]; ?> <br/>
					<? echo $bank_address_arr[$sql_mst[0]['ADVISING_BANK']];?></p>
					<p><span>HS Code:&nbsp;<strong><? echo $sql_mst[0]['HS_CODE']; ?></strong></span>&nbsp;&nbsp;&nbsp;&nbsp;SWIFT Code:<span>&nbsp;<strong><? echo $sql_mst[0]['SWIFT_CODE']; ?></strong></span></p>
				</td>

			</tr>
			<tr>
				<th style="width: 40px; text-align: center;">SL/No</th>
				<th style="width: 70px; text-align: center;">JOB No</th>
				<th style="width: 80px; text-align: center;">Style No</th>
				<th style="width: 70px; text-align: center;">WO/BOOKING</th>
				<th style="width: 70px; text-align: center;">GMTS ITEM</th>
				<th style="width: 70px; text-align: center;">COLOR</th>
				<th style="width: 70px; text-align: center;">Embl TYPE</th>
				<th style="width: 70px; text-align: center;">UOM</th>
				<th style="width: 60px; text-align: center;">QUANTITY</th>
				<th style="width: 60px; text-align: center;">UNIT PRICE<br/>IN USD ($)</th>
				<th style="width: 70px; text-align: center;">UNIT PRICE<br/>PER DZN</th>
				<th style="width: 60px; text-align: center;">AMOUNT<br/>IN USD ($)</th>
			</tr>
			<?
			
			if ($sql_mst[0]['WITHIN_GROUP']==1)
			{
				/*$sql_export_pi_dtls = "SELECT a.ID, a.ITEM_CATEGORY_ID, a.CURRENCY_ID, b.work_order_no as JOB_NO, b.BOOKING, b.COLOR_ID, b.UOM, b.QUANTITY, b.RATE, b.AMOUNT, b.GMTS_ITEM_ID, d.MAIN_PROCESS_ID, d.EMBL_TYPE 
				from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_dtls d 
				where a.id=b.pi_id and b.work_order_dtls_id=d.id and a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0
				group by a.id, a.item_category_id, a.currency_id, b.work_order_no, b.booking, b.color_id, b.uom, b.quantity, b.rate, b.amount, b.gmts_item_id, d.main_process_id, d.embl_type";*/
				$sql_export_pi_dtls = "SELECT a.ID,b.id,d.id, a.ITEM_CATEGORY_ID, a.CURRENCY_ID, b.work_order_no as JOB_NO, b.BOOKING, b.COLOR_ID, b.UOM, b.QUANTITY, b.RATE, b.AMOUNT, b.GMTS_ITEM_ID, c.MAIN_PROCESS_ID, c.EMBL_TYPE,d.COLOR_ID
				from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_dtls c, subcon_ord_breakdown d 
				where a.id=b.pi_id and b.work_order_dtls_id=d.id and a.id=$mst_id and c.id=d.mst_id and  c.job_no_mst=d.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.is_deleted=0
				group by a.id,b.id,d.id, a.item_category_id, a.currency_id, b.work_order_no, b.booking, b.color_id, b.uom, b.quantity, b.rate, b.amount, b.gmts_item_id, c.main_process_id, c.embl_type,d.color_id";
				$exprt_pi_dtls_result = sql_select($sql_export_pi_dtls);
				foreach ($exprt_pi_dtls_result as $val) {
					$booking_no .= "'".$val['BOOKING']."'".',';
				}
				$booking_nos=implode(',',array_flip((array_flip(explode(',',rtrim($booking_no,','))))));

				$sql_order="SELECT a.BOOKING_NO, c.STYLE_REF_NO from wo_booking_dtls a, wo_po_break_down b, wo_po_details_master c where a.job_no=b.job_no_mst and a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.booking_type=6 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and a.booking_no in($booking_nos) group by a.booking_no, c.style_ref_no";
				$sql_order_res=sql_select($sql_order);
				$style_ref_arr=array();
				foreach ($sql_order_res as $val) {
					$style_ref_arr[$val['BOOKING_NO']]=$val['STYLE_REF_NO'];
				}
			
			}
			else
			{
				$sql_export_pi_dtls = "SELECT a.ID, a.ITEM_CATEGORY_ID, a.CURRENCY_ID, b.work_order_no as JOB_NO, b.BOOKING, b.COLOR_ID, b.UOM, b.QUANTITY, b.RATE, b.AMOUNT, b.GMTS_ITEM_ID, d.buyer_style_ref as STYLE_REF_NO, c.MAIN_PROCESS_ID, c.EMBL_TYPE 
				from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_dtls c, subcon_ord_breakdown d 
				where a.id=b.pi_id and b.work_order_dtls_id=d.id and a.id=$mst_id and c.job_no_mst=d.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0
				group by a.id, a.item_category_id, a.currency_id, b.work_order_no, b.booking, b.color_id, b.uom, b.quantity, b.rate, b.amount, b.gmts_item_id, d.buyer_style_ref, c.main_process_id, c.embl_type";
				$exprt_pi_dtls_result = sql_select($sql_export_pi_dtls);				
			}
			// echo $sql_export_pi_dtls;
			$i=1;
			foreach ($exprt_pi_dtls_result as $key => $value)
			{				

				$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
				if ($sql_mst[0]['WITHIN_GROUP']==1) $style = $style_ref_arr[$value['BOOKING']];
				else $style = $value['STYLE_REF_NO'];

				?>
				<tr>
					<td style="width: 40px; text-align: center;"><?= $i;?></td>
					<td style="width: 70px; text-align: center;"><p><?= $value['JOB_NO']; ?></p></td>
					<td style="width: 80px; text-align: center;"><p><?= $style; ?></p></td>
					<td style="width: 70px; text-align: center;"><p><?= $value['BOOKING']; ?></p></td>
					<td style="width: 70px; text-align: center;"><p><?= $garments_item[$value['GMTS_ITEM_ID']]; ?></p></td>
					<td style="width: 70px; text-align: center;"><p><?= $color_arr[$value['COLOR_ID']]; ?></p></td>
					<td style="width: 70px; text-align: center;"><p><?= $type_array[$value['MAIN_PROCESS_ID']][$value['EMBL_TYPE']]; ?></p></td>
					<td style="width: 70px; text-align: center;"><p><?= $unit_of_measurement[$value['UOM']]; ?></p></td>
					<td style="width: 60px; text-align: right;"><p><?= number_format($value['QUANTITY'], 2, '.', ''); $total_qty+=$value['QUANTITY']; ?></p></td>
					<td style="width: 60px; text-align: right;"><p><?= number_format($value['RATE'], 2, '.', ''); ?></p></td>
					<td style="width: 70px; text-align: right;"><p><?= number_format($value['RATE']*12, 2, '.', ''); ?></p></td>
					<td style="width: 60px; text-align: right;"><p><?= number_format($value['AMOUNT'], 2, '.', ''); $total_amt+=$value['AMOUNT']; ?></p></td>
				</tr>
				<?
				$i++; 
			}
			?>
			<tr>
				<td colspan="8" align="right">Total</td>
				<td align="right"><p><?= number_format($total_qty, 2, '.', '');?></p></td>
				<td></td>
				<td></td>
				<td align="right"><p><?= number_format($total_amt,2, '.', ''); ?></p></td>
			</tr>
			<tr>
				<td colspan="12" align="center">(<?= number_to_words(number_format($total_amt,2, '.', ''), 'USD', 'Cent');?>)</td>
			</tr>
        </table>
        <br><style> .terms_cond_table tr td { border: 1px solid white;}</style>
		<table class="terms_cond_table" width="100%" cellspacing="0" rules="all" border="0">
			<tr>
				<td><strong>Remarks</strong></td>
				<td>:<strong>&nbsp;<? echo $sql_mst[0]['REMARKS']; ?></strong></td>
			</tr>
			<tr>
				<td><em>PAYMENT</em></td>
				<td>:<em> Irrevocable L/C At 90 days Sight Incorporating Export L/C No & Date which shall not be concerned to the openers Export Realisation.</em></td>
			</tr>
			<tr>
				<td><em>SHIPMENT</em></td>
				<td>:<em> Within 30 days from the receiving date of L/C</em></td>
			</tr>
			<tr>
				<td><em>NEGOTIATION</em></td>
				<td>:<em> Within 20 days from the date of shipment.</em></td>
			</tr>
			<tr>
				<td><em>INSURANCE</em></td>
				<td>:<em> Covered by the buyer.</em></td>
			</tr>
			<tr>
				<td><em>INTEREST</em></td>
				<td>:<em> Interest will be paid by the opener for the usance period as per rate  prescribed by the Bangladesh Bank. On advance export affairs. </em></td>
			</tr>
			<tr>
				<td colspan="2"><em><strong>Utilization Declaration : U.D Issue By the L/C opener.</strong></em></td>
			</tr>
			<tr>
				<td colspan="2">TIN No. 675976812463 &nbsp; BIN: 000420765</td>
			</tr>
			<tr>
				<td colspan="2">IRC No. BA 145245 &nbsp; ERC No: RA 61952</td>
			</tr>
            <tr>
				<td colspan="2">&nbsp;</td>
			</tr>
            <tr>
				<td colspan="2">FOR AND ON BEHALF OF</td>
			</tr>
            <tr>
				<td colspan="2"><strong>HAMS FASHION LTD.</strong></td>
			</tr>
		</table>
        <? 
        	//echo get_spacial_instruction($$mst_id,"100%",152);
        ?>
        <table width="100%">
            <tr height="50"></tr>
            <tr>
            	<td style="">Authorized Signature</td>
            	<td style="text-align:right;">
					CONFIRMED<BR/>
					BY THE BUYER<BR/><BR/><BR/>
					SEAL & SIGNATURE
				</td>
            </tr>
            <tr height="50"></tr>
        </table>
	</div>
	<?
	exit();	 
}

if($action=="print_new_rpt_6") 
{
	list($company_id, $mst_id, $system_id) = explode('*', $data);
	$sql_company = sql_select("SELECT ID, COMPANY_NAME, CONTRACT_PERSON, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, CITY, ZIP_CODE, COUNTRY_ID, CONTACT_NO, EMAIL FROM lib_company WHERE is_deleted=0 and status_active=1");
	$country_name_arr=return_library_array( "select id, country_name from lib_country where status_active=1",'id','country_name');  	
	$plot_no=$level_no=$road_no=$block_no='';
	$city=$zip_code=$country=$contact_no='';
	$company_info='';
	$company_arr_info='';
  	foreach($sql_company as $company_data) 
  	{
		if ($company_data['PLOT_NO'] !='') $plot_no = 'Plot No.#'.$company_data['PLOT_NO'].','.' ';
		if ($company_data['LEVEL_NO'] !='') $level_no = 'Level No.#'.$company_data['LEVEL_NO'].','.' ';
		if ($company_data['ROAD_NO'] !='') $road_no = 'Road No.#'.$company_data['ROAD_NO'].','.' ';
		if ($company_data['BLOCK_NO'] !='') $block_no = 'Block No.#'.$company_data['BLOCK_NO'].','.' ';
		if ($company_data['CITY'] !='') $city = $company_data['CITY'].','.' ';
		if ($company_data['ZIP_CODE'] !='') $zip_code = '-'.$company_data['ZIP_CODE'].','.' ';
		if ($company_data['COUNTRY_ID'] !=0) $country = $country_name_arr[$company_data['COUNTRY_ID']].','.' ';
		if ($company_data['CONTACT_NO'] !=0) $contact_no = $company_data['CONTACT_NO'];
		$company_info[$company_data['ID']]['CONTRACT_PERSON'] = $company_data['CONTRACT_PERSON'];
		$company_info[$company_data['ID']]['EMAIL'] = $company_data['EMAIL'];
		$company_info[$company_data['ID']]['CONTACT_NO'] = $company_data['CONTACT_NO'];
		$company_address[$company_data['ID']]['ADDRESS'] = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country.$contact_no;
		$company_arr_info[$company_data['ID']]['COMPANY_NAME'] = $company_data['COMPANY_NAME'];
		
	}
	
	
	$bank_arr=return_library_array( "select id, (bank_name||' ( '|| branch_name||' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name');
	
	$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company where status_active=1",'id','plot_no');
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer where status_active=1",'id','buyer_name');
	$bank_address_arr=return_library_array( "select id, address as address from lib_bank where advising_bank=1",'id','address');	
	$lib_buyer_arr=return_library_array( "select id, address_1 from lib_buyer where status_active=1",'id','address_1');	
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$item_group_arr = return_library_array( "select id, item_name from lib_item_group where status_active=1",'id','item_name');
	$bank_ac_sql=sql_select("select a.ID, b.ACCOUNT_NO from LIB_BANK a, LIB_BANK_ACCOUNT b where a.id=b.account_id");
	$bank_ac_data=array();
	foreach($bank_ac_sql as $row)
	{
		$bank_ac_data[$row["ID"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}
	?>
	<div style="width:1080px">
		<?
			$sql_export_pi = "SELECT ID, PI_NUMBER, PI_DATE, ITEM_CATEGORY_ID, BUYER_ID, CURRENCY_ID, PI_VALIDITY_DATE, EXPORTER_ID, WITHIN_GROUP, LAST_SHIPMENT_DATE, HS_CODE, SWIFT_CODE, REMARKS, ADVISING_BANK from com_export_pi_mst where id=$mst_id";
			//echo $sql_export_pi;
			$sql_mst = sql_select($sql_export_pi); 
			$term_data_array = sql_select("select id, terms from  wo_booking_terms_condition where booking_no='".$sql_mst[0]['ID']."' and entry_form='152' order by id");
			// echo $term_data_array;die;

		?>
        <table class="rpt_table" width="100%" cellspacing="1" rules="all" >
            <tr>
            	<td align="center">
                	<h1 style="font-size: 20px;"><?= $company_arr_info[$sql_mst[0]['EXPORTER_ID']]['COMPANY_NAME'] ; ?> </h1>
					<?= $company_address[$sql_mst[0]['EXPORTER_ID']]['ADDRESS'] ; ?> 
                </td>
            </tr>
			</table>
		<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
            <tr>
            	<td style="font-size:14px; padding: 10px 0px;" align="center" colspan="11">
                	<strong><?= "PROFORMA INVOICE";?></strong>
                </td>
            </tr>
			<tr>
				<!-- <td rowspan="2" style="border-right: 1px solid white; width: 50; vertical-align: top;"> TO MESSARS:</td> -->
				<td colspan="6" valign="top">
				 <strong><? echo $sql_mst[0]['PI_NUMBER'];?></strong></br>
					Date: <? echo change_date_format($sql_mst[0]['PI_DATE']);?>
				</td>
				<td colspan="5">
					<p>Advising bank: <br/><strong><? echo $bank_arr[$sql_mst[0]['ADVISING_BANK']]; ?></strong> <br/>
					<? echo $bank_address_arr[$sql_mst[0]['ADVISING_BANK']];?>
					<!-- <span>HS Code:&nbsp;<? echo $sql_mst[0]['HS_CODE']; ?></span> -->
					&nbsp;&nbsp;SWIFT Code:<span>&nbsp;<strong><? echo $sql_mst[0]['SWIFT_CODE']; ?></strong></span></br>
					A/C No:<span>&nbsp;<? echo $bank_ac_data[$sql_mst[0]['ADVISING_BANK']]["ACCOUNT_NO"]; ?></span></p>
				</td>
			</tr>
			<tr>
			<td colspan="6" valign="top">
			Applicant</br>
			<?
					if($sql_mst[0]['WITHIN_GROUP']==1)
					{
						$buyer_name=$company_arr_info[$sql_mst[0]['BUYER_ID']]['COMPANY_NAME'];
						$buyer_address=$company_address[$sql_mst[0]['BUYER_ID']]['ADDRESS'];
						$buyer_person=$company_info[$sql_mst[0]['BUYER_ID']]['CONTRACT_PERSON'];
						$buyer_contact=$company_info[$sql_mst[0]['BUYER_ID']]['CONTACT_NO'];
						$buyer_mail=$company_info[$sql_mst[0]['BUYER_ID']]['EMAIL'];
					}
					else
					{
						
						$buyer_name=return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0]['BUYER_ID']."'");
						$buyer_address=return_field_value("address_1","lib_buyer","id='".$sql_mst[0]['BUYER_ID']."'");
						$buyer_person=return_field_value("contact_person","lib_buyer","id='".$sql_mst[0]['BUYER_ID']."'");
						// $buyer_contact=return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0]['BUYER_ID']."'");
						$buyer_mail=return_field_value("buyer_email","lib_buyer","id='".$sql_mst[0]['BUYER_ID']."'");
						
					}
			?>
			<strong><? echo $buyer_name; ?> </br></strong>
				<? echo  $buyer_address; ?> </br>
				Contact Person:&nbsp; <? echo $buyer_person; ?></br>
				Cell:&nbsp; <? echo $buyer_contact; ?></br>
				E-mail:&nbsp; <? echo $buyer_mail; ?></br>
				</td>
				<td colspan="5">
				Beneficiary </br>
				<strong><?= $company_arr[$company_id]; ?> </br></strong>
				<?= $company_address[$sql_mst[0]['EXPORTER_ID']]['ADDRESS']; ?> </br>
				Contact Person:&nbsp; <? echo $company_info[$sql_mst[0]['EXPORTER_ID']]['CONTRACT_PERSON']; ?></br>
				Cell:&nbsp; <? echo $company_info[$sql_mst[0]['EXPORTER_ID']]['CONTACT_NO']; ?></br>
				E-mail:&nbsp; <? echo $company_info[$sql_mst[0]['EXPORTER_ID']]['EMAIL']; ?></br>
				</td>
			</tr>
			<tr>
            	<td align="left" colspan="11">
                <?= "We are pleased to offer the under mentioned as per conditions and details as follows:";?>
                </td>
            </tr>
			<tr>
				<th style="width: 40px; text-align: center;">Sl No</th>
				<th style="width: 70px; text-align: center;">Job Order No</th>
				<th style="width: 80px; text-align: center;">Work Order No</th>
				<th style="width: 70px; text-align: center;">Buyers Name</th>
				<th style="width: 70px; text-align: center;">Item Group</th>
				<th style="width: 70px; text-align: center;">Item Description</th>
				<th style="width: 70px; text-align: center;">UOM</th>
				<th style="width: 70px; text-align: center;">Qty</th>
				<th style="width: 60px; text-align: center;">Rate</th>
				<th style="width: 60px; text-align: center;">Amount <br/>USD</th>
				<th style="width: 70px; text-align: center;">Remarks</th>
			</tr>
			<?
				$sql_export_pi_dtls = "SELECT a.ID, b.work_order_no as JOB_NO, b.BOOKING, b.ITEM_DESC, b.GMTS_ITEM_ID, b.UOM, b.RATE, SUM (b.QUANTITY) as QUANTITY, SUM (b.AMOUNT) as AMOUNT,d.BUYER_BUYER from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c, subcon_ord_dtls d where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and a.exporter_id=$company_id and a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.id, b.work_order_no, b.booking, b.item_desc, b.gmts_item_id, b.uom, b.rate, d.buyer_buyer";
				// echo $sql_export_pi_dtls;die;
				$exprt_pi_dtls_result = sql_select($sql_export_pi_dtls);

			$i=1;
			foreach ($exprt_pi_dtls_result as $key => $value)
			{				

				$type_array=array(0=>$blank_array,1=>$emblishment_print_type,2=>$emblishment_embroy_type,3=>$emblishment_wash_type,4=>$emblishment_spwork_type,5=>$emblishment_gmts_type);
				if ($sql_mst[0]['WITHIN_GROUP']==1) $style = $style_ref_arr[$value['BOOKING']];
				else $style = $value['STYLE_REF_NO'];

				?>
				<tr>
					<td style="width: 40px; text-align: center;"><?= $i;?></td>
					<td style="width: 70px; text-align: center;"><p><?= $value['JOB_NO']; ?></p></td>
					<td style="width: 70px; text-align: center;"><p><?= $value['BOOKING']; ?></p></td>
					<td style="width: 70px; text-align: center;"><p>
					<? if ($sql_mst[0]['WITHIN_GROUP'] == 1) {echo $buyer_arr[$value['BUYER_BUYER']];} else {echo $value['BUYER_BUYER'];} ?>
					</p></td>
					<td style="width: 70px; text-align: center;"><p><?
					echo $item_group_arr[$value['GMTS_ITEM_ID']]
					?></p></td>
					<td style="width: 70px; text-align: center;"><p><?= $value['ITEM_DESC']; ?></p></td>
					<td style="width: 70px; text-align: center;"><p><?= $unit_of_measurement[$value['UOM']]; ?></p></td>
					<td style="width: 60px; text-align: right;"><p><?= number_format($value['QUANTITY'], 2, '.', ''); $total_qty+=$value['QUANTITY']; ?></p></td>
					<td style="width: 60px; text-align: right;"><p><?= number_format($value['RATE'], 2, '.', ''); ?></p></td>
					<td style="width: 70px; text-align: right;"><p><?= number_format($value['AMOUNT'], 2, '.', '');$total_amt+=$value['AMOUNT']; ?></p></td>
					<td style="width: 60px; text-align: right;"><p><?= $sql_mst[0]['REMARKS'];;  ?></p></td>
				</tr>
				<?
				$i++; 
			}
			?>
			<tr>
				<td colspan="7" align="right">Grand Total</td>
				<td align="right"><p><?= number_format($total_qty, 2, '.', '');?></p></td>
				<td></td>
				<td align="right"><p><?= number_format($total_amt,2, '.', ''); ?></p></td>
			</tr>
        </table>
        <br><style> .terms_cond_table tr td { border: 1px solid white;}</style>
		<table class="terms_cond_table" width="100%" cellspacing="0" rules="all" border="0">
			<tr>
			<!-- <td width='25'></td> -->
				<td colspan='2'><strong>In Word:&nbsp;<?= number_to_words(number_format($total_amt,2, '.', ''), 'USD', 'Cent');?></strong></td>
			</tr>
			<tr>
				<!-- <td width='25'></td> -->
				<td width="160"><strong>Terms and Conditions:</strong></td>
				<td width="600"><strong>&nbsp;</strong></td>
			</tr>
			<!-- <?
			foreach ($term_data_array as $value){
				?>
			<tr>
				<td width='25'></td><td >
				<? //echo $value['TERMS'];
			}?>
			</td></tr> -->
		</table>
        <? 
        	echo get_spacial_instruction($mst_id,"70%",152);
        ?>
        <table width="100%">
            <tr height="50"></tr>
            <tr >
			<td width="25"></td>
			<td width="200" align="center"><strong><?= $company_arr_info[$sql_mst[0]['EXPORTER_ID']]['COMPANY_NAME'] ; ?> </strong></td>
			
			
			</tr>
			<tr >
			<td height="100"></td>
			</tr>
            <tr>
			<td ></td>
			<td style="border-top: 1px solid; " align="center">Authorized Signature</td>
			<td ></td>
			
			<td ></td>
            </tr>
            
        </table>
	</div>
	<?
	exit();	 
}

if($action=="print_new_rpt_3asasas") 
{
	$data = explode('*',$data);
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	$country_name_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $country_name_arr[$company_data[csf('country_id')]].','.' ';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_plot_no_arr=return_library_array( "select id, plot_no from lib_company",'id','plot_no');
	
	if ($db_type==0)
	{
		$bank_arr=return_library_array( "select id, concat(bank_name, ' ( ',branch_name,' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name'); 
	}
	else
	{
		$bank_arr=return_library_array( "select id, (bank_name||' ( '|| branch_name||' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name');
	}
	
	$bank_address_arr=return_library_array( "select id, address as address from lib_bank where advising_bank=1",'id','address');	
	$lib_buyer_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	?>
	<div style="width:1000px">
		<?
			$sql_export_pi = "select id,pi_number,pi_date,item_category_id,buyer_id,currency_id,pi_validity_date,exporter_id,within_group,last_shipment_date,hs_code,remarks,advising_bank from com_export_pi_mst where id= $data[1]";
			//echo $sql_export_pi;
			$sql_mst = sql_select($sql_export_pi); 
		?>
        <table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
            <tr>
            	<td align="center" colspan="11">
                	<h1 style="font-size: 20px;"><?= $company_arr[$data[0]]; ?> </h1>
					<?= $company_address; ?> 
                </td>
            </tr>
            <tr>
            	<td style="font-size:14px; padding: 10px 0px;" align="center" colspan="11">
                	<strong><u><?= "PROFORMA INVOICE";?></u></strong>
                </td>
            </tr>
			<tr>
				<td rowspan="2" style="border-right: 1px solid white; width: 40;"> TO MESSARS:</td>
				<td rowspan="2" colspan="4" >
                       <? 
							if($sql_mst[0][csf('within_group')]==1)
							{
                            	$buyer=$company_arr[$sql_mst[0][csf('buyer_id')]].$lib_buyer_arr[$sql_mst[0][csf('buyer_id')]];
							}
							else
							{
								
								$buyer=return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0][csf('buyer_id')]."'");
								
							}
							echo $buyer."<br>".$lib_buyer_arr[$sql_mst[0][csf('buyer_id')]];
                        ?>	
				</td>
				<td colspan="6">
					<p>Proform Invoice No: <strong><? echo $sql_mst[0][csf('pi_number')];?></strong></p>
					<p>Date: <strong><? echo $sql_mst[0][csf('pi_date')];?></strong></p>
				</td>
			</tr>
			<tr>
				<td colspan="6">
					Advising bank: <br/><? echo $bank_arr[$sql_mst[0][csf('advising_bank')]];?> <br/>
					<? echo $bank_address_arr[$sql_mst[0][csf('advising_bank')]];?>
				</td>
			</tr>
			<tr>
				<th style="width: 40px; text-align: center;">SL. No. </th>
				<th style="width: 70px; text-align: center;">JOB No.</th>
				<th style="width: 70px; text-align: center;">WO/BOOKING</th>
				<th style="width: 70px; text-align: center;">GMTS ITEM</th>
				<th style="width: 70px; text-align: center;">COLOR</th>
				<th style="width: 70px; text-align: center;">PROCESS</th>
				<th style="width: 70px; text-align: center;">WASH TYPE</th>
				<th style="width: 70px; text-align: center;">UOM</th>
				<th style="width: 60px; text-align: center;">QUANTITY</th>
				<th style="width: 60px; text-align: center;">UNIT PRICE<br/>IN USD ($)</th>
				<th style="width: 60px; text-align: center;">AMOUNT<br/>IN USD ($)</th>
			</tr>
			<?
			
			if($db_type==0) $process_type_cond="group_concat(c.process,'*',c.embellishment_type)";
			else if ($db_type==2) $process_type_cond="listagg(c.process||'*'||c.embellishment_type,',') within group (order by c.process||'*'||c.embellishment_type)";
			
			
			 $sql_export_pi_dtls = "select a.id,a.item_category_id,a.currency_id,b.work_order_no as job_no, b.booking, b.color_id, b.uom, b.quantity, b.rate, b.amount, b.gmts_item_id,$process_type_cond as process_type from com_export_pi_mst a, com_export_pi_dtls b,subcon_ord_breakdown c  where a.id=b.pi_id and  b.work_order_dtls_id=c.mst_id and  a.id= $data[1] and a.is_deleted=0 and b.is_deleted=0 group by a.id,a.item_category_id,a.currency_id,b.work_order_no, b.booking, b.color_id, b.uom, b.quantity, b.rate, b.amount, b.gmts_item_id";
			
			$exprt_pi_dtls_result = sql_select($sql_export_pi_dtls);
			$i=1;
			foreach ($exprt_pi_dtls_result as $key => $value)
			 {
				 
				$ex_process=array_unique(explode(",",$value[csf("process_type")]));
				$process_name=""; $sub_process_name="";
				foreach($ex_process as $process_data)
				{
					$ex_process_type=explode("*",$process_data);
					$process_id=$ex_process_type[0];
					$type_id=$ex_process_type[1];
					if($process_id==1) $process_type_arr=$wash_wet_process;
					else if($process_id==2) $process_type_arr=$wash_dry_process;
					else if($process_id==3) $process_type_arr=$wash_laser_desing;
					else $process_type_arr=$blank_array;
					
					if($process_name=="") $process_name=$wash_type[$process_id]; else $process_name.=','.$wash_type[$process_id];
					
					if($sub_process_name=="") $sub_process_name=$process_type_arr[$type_id]; else $sub_process_name.=','.$process_type_arr[$type_id];
				}
				
				
				$process_name=implode(",",array_unique(explode(",",$process_name)));
				$sub_process_name=implode(",",array_unique(explode(",",$sub_process_name)));
		
				# code...
			?>
			<tr>
				<td style="width: 40px; text-align: center;"><?= $i;?></td>
				<td style="width: 70px; text-align: center;"><?= $value[csf("job_no")]; ?></td>
				<td style="width: 70px; text-align: center;"><?= $value[csf("booking")]; ?></td>
				<td style="width: 70px; text-align: center;"><?= $garments_item[$value[csf("gmts_item_id")]]; ?></td>
				<td style="width: 70px; text-align: center;"><?= $color_arr[$value[csf("color_id")]]; ?></td>
				<td style="width: 70px; text-align: center;"><?= $process_name; ?></td>
				<td style="width: 70px; text-align: center;"><?= $sub_process_name; ?></td>
				<td style="width: 70px; text-align: center;"><?= $unit_of_measurement[$value[csf("uom")]]; ?></td>
				<td style="width: 60px; text-align: center;"><?= $value[csf("quantity")]; $total_qty+=$value[csf("quantity")];?></td>
				<td style="width: 60px; text-align: center;"><?= $value[csf("rate")]; ?></td>
				<td style="width: 60px; text-align: center;"><?= number_format($value[csf("amount")],4,".",""); $total_amt+=$value[csf("amount")]; ?></td>
			</tr>
			<?
			$i++; 
			}
			?>
			<tr>
				<td colspan="8" align="right">Total</td>
				<td align="right"><?= $total_qty;?></td>
				<td></td>
				<td align="right"><?= number_format($total_amt,4, '.', '');//$total_amt;?></td>
			</tr>
			<tr>
				<td colspan="11" align="center">(<?= number_to_words(number_format($total_amt,4, '.', ''), "USD", "Cent");?>)</td>
			</tr>
        </table>
        <br><style> .terms_cond_table tr td { border: 1px solid white;}</style>
		<table class="terms_cond_table" width="100%" cellspacing="0" rules="all" border="0">
			<tr>
				<td><em>PAYMENT</em></td>
				<td>:<em> Irrevocable L/C At 90 days Sight Incorporating Export L/C No & Date which shall not be concerned to the openers Export Realisation.</em></td>
			</tr>
			<tr>
				<td><em>SHIPMENT</em></td>
				<td>:<em> Within 30 days from the receiving date of L/C</em></td>
			</tr>
			<tr>
				<td><em>NEGOTIATION</em></td>
				<td>:<em> Within 20 days from the date of shipment.</em></td>
			</tr>
			<tr>
				<td><em>INSURANCE</em></td>
				<td>:<em> Covered by the buyer.</em></td>
			</tr>
			<tr>
				<td><em>INTEREST</em></td>
				<td>:<em> Interest will be paid by the opener for the usance period as per rate  prescribed by the Bangladesh Bank. On advance export affairs. </em></td>
			</tr>
			<tr>
				<td colspan="2"><em>Utilization Declaration : U.D Issue By the L/C opener.</em>
					<ul>
						<li><em><strong><small>U.D copy must be attached by BGMEA Or BKMEA</small></strong> </em></li>
						<li><em>No Claim will be accepted after taking delivery the goods.</em></li>
						<li><em>Over due interest will be paid @16% for delayed period only</em></li>
					</ul>
				</td>
			</tr>
			<tr>
				<td colspan="2">TIN No. 481300693049l &nbsp; BIN: 00028</td>
			</tr>
			<tr>
				<td colspan="2">IRC No. 481300693049l &nbsp; ERC No: 00028</td>
			</tr>
            <tr>
				<td colspan="2">FOR AND ON BEHALF OF</td>
			</tr>
            <tr>
				<td colspan="2"><strong>DHAKA GARMENTS AND WASHING LTD</strong></td>
			</tr>
		</table>
        <? 
        	//echo get_spacial_instruction($data[1],"100%",152);
        ?>
        <table width="100%">
            <tr height="50"></tr>
            <tr>
            	<td style="">Authorized Signature</td>
            	<td style="text-align:right;">
					CONFIRMED<BR/>
					BY THE BUYER<BR/><BR/><BR/>
					SEAL & SIGNATURE
				</td>
            </tr>
            <tr height="50"></tr>
        </table>
	</div>
	<?
	exit();	 
}

if($action=="print_new_rpt_8") 
{
	$data = explode('*',$data);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$fabric_composition_arr=return_library_array( "select id, fabric_composition_name from lib_fabric_composition",'id','fabric_composition_name');
	$sql_company_info = sql_select("SELECT contact_no as CONTACT_NO,email as EMAIL,website as WEBSITE FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	$sql_mst = sql_select("select id,pi_number,pi_date,item_category_id,buyer_id,currency_id,pi_validity_date,exporter_id,within_group,last_shipment_date,attention,pi_revised_date,remarks from com_export_pi_mst where id= $data[1]");

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" ); $total_ammount = 0; $total_quantity=0; $total_pcs_qty=0;  $total_kg_qty=0;
	$sql ="SELECT f.fabric_composition_id, a.id,a.booking, a.work_order_no, a.color_id, a.construction, a.composition, a.uom, a.quantity, a.rate, a.amount, a.work_order_dtls_id, a.determination_id, b.upcharge, b.discount, b.net_total_amount, c.Style_ref_no, c.customer_buyer, d.gsm_weight as before_wash_gsm, d.after_wash_gsm, d.dia as fabric_dia, d.cuttable_dia, d.color_range_id from lib_yarn_count_determina_mst f, com_export_pi_dtls a, com_export_pi_mst b, fabric_sales_order_mst c, fabric_sales_order_dtls d where f.id=a.determination_id and a.pi_id = b.id and a.pi_id='$data[1]' and a.quantity>0 and a.work_order_id=c.id and c.id=d.mst_id and a.work_order_dtls_id=d.id and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and f.status_active=1 and f.is_deleted=0";
	$sql_res=sql_select($sql);
	$data_array=array();
	foreach ($sql_res as $row) 
	{
		$booking.=$row[csf('booking')].',';
		$Style_ref_no.=$row[csf('Style_ref_no')].',';
		$customer_buyer.=$buyer_arr[$row[csf('customer_buyer')]].',';
		$description=$row[csf('construction')].'**'.$row[csf('fabric_composition_id')].'**'.$row[csf('before_wash_gsm')].'**'.$row[csf('after_wash_gsm')].'**'.$row[csf('fabric_dia')].'**'.$row[csf('cuttable_dia')];
		$data_array[$description][$row[csf('color_range_id')]][$row[csf('uom')]][$row[csf('rate')]]['quantity']+=$row[csf('quantity')];
		$data_array[$description][$row[csf('color_range_id')]][$row[csf('uom')]][$row[csf('rate')]]['amount']+=$row[csf('amount')];
		$data_array[$description][$row[csf('color_range_id')]][$row[csf('uom')]][$row[csf('rate')]]['upcharge']+=$row[csf('upcharge')];
		$data_array[$description][$row[csf('color_range_id')]][$row[csf('uom')]][$row[csf('rate')]]['discount']+=$row[csf('discount')];
		$data_array[$description][$row[csf('color_range_id')]][$row[csf('uom')]][$row[csf('rate')]]['net_total_amount']+=$row[csf('net_total_amount')];
	}
	$booking = implode(', ',array_flip(array_flip(explode(',', rtrim($booking,',')))));
	$Style_ref_no = implode(', ',array_flip(array_flip(explode(',', rtrim($Style_ref_no,',')))));
	$customer_buyer = implode(', ',array_flip(array_flip(explode(',', rtrim($customer_buyer,',')))));
    ?>
	<div style="width:1000px">

        <table width="100%">
            <tr>
            	<td style="font-size:24px;" align="center" colspan="6">
                	<strong><? echo $company_arr[$sql_mst[0][csf('exporter_id')]]; ?></strong>
                </td>
            </tr>
            <tr>
            	<td style="font-size:16px;" align="center" colspan="6">
                	<strong><? echo "FACTORY:  ZEERANI BAZAR, KASHIMPUR, JOYDEBPUR, GAZIPUR."; ?></strong>
                </td>
            </tr>
            <tr>
            	<td style="font-size:16px;" align="center" colspan="6">
                	<strong><? echo "Head Office: House # 9/KHA, CONFIDENCE CENTER, SHAHAZADPUR, GULSHAN, Dhaka-1212, Bangladesh."; ?></strong>
                </td>
            </tr>
            <tr>
            	<td style="font-size:16px;" align="center" colspan="6">
                	<strong><? echo 'Tel: '.$sql_company_info[0]['CONTACT_NO']; ?></strong>
                </td>
            </tr>

            <tr>
            	<td style="font-size:16px;" align="center" colspan="6">
                	<strong><? echo 'E-mail: '.$sql_company_info[0]['EMAIL'].'; Web: '.$sql_company_info[0]['WEBSITE'];; ?></strong>
                </td>
            </tr>
            <tr>
            	<td height="10" colspan="6"></td>
            </tr>
            <tr>
            	<td style="font-size:20px;" align="center" colspan="6">
                	<strong><? echo "PROFORMA INVOICE"; ?></strong>
                </td>
            </tr>
        </table>

        <br>
		<table width="1000" cellspacing="0" rowspacing="0">
            <tr>
				<td width="50">To:</td>
				<td width="350"><strong>
					<? 
						if($sql_mst[0][csf('within_group')]==1)
						{
							$buyer=$company_arr[$sql_mst[0][csf('buyer_id')]].$lib_buyer_arr[$sql_mst[0][csf('buyer_id')]];
						}
						else
						{
							$buyer=$buyer_arr[$sql_mst[0][csf('buyer_id')]];
						}
						echo $buyer;
					?></strong>
				</td>
				<td></td>
				<td width="80">PI No: </td>
				<td width="150" colspan="2" style="font-size:20px;"><? echo $sql_mst[0][csf('pi_number')];?> </td>
            </tr>
            <tr>
				<td></td>
				<td rowspan="2" valign="top"> <strong>
					<? 
						if($sql_mst[0][csf('within_group')]==2)
						{
							echo return_field_value("address_1","lib_buyer","id='".$sql_mst[0][csf('buyer_id')]."'");
						}						
					?></strong>
				</td>
				<td></td>
				<td>PI Date: </td>
				<td colspan="2"><? echo change_date_format($sql_mst[0][csf('pi_date')]);?> </td>
            </tr>
            <tr>
				<td></td>
				<td></td>
				<td  valign="top">Currency: </td>
				<td  colspan="2" valign="top"><? echo $currency[$sql_mst[0][csf('currency_id')]];?> </td>
				<td></td>
            </tr>
			<tr>
				<td></td>
				<td></td>
				<td colspan="2"valign="baseline">PI Revised Date: </td>
			    <td><? echo change_date_format($sql_mst[0][csf('pi_revised_date')]);?> </td>
				<td></td>

            </tr>
            <tr>
				<td colspan="6" height="20"></td>
            </tr>
            <tr>
				<td></td>
				<td><strong>Attn:
					<? 
						echo $sql_mst[0][csf('attention')];
					?></strong>
				</td>
				<td></td>
				<td> </td>
				<td> </td>
				<td> </td>
            </tr>
            <tr>
				<td width="100"><strong>Cust. Buyer:</strong></td>
				<td width="200"><strong><? echo $customer_buyer; ?></strong></td>
				<td width="40"><strong>Style:</strong></td>
				<td width="200"><? echo $Style_ref_no; ?></td>
				<td width="100"><strong>Sales Job/ Booking No:</strong></td>
				<td width="200"><? echo $booking; ?></td>
            </tr>
			<tr>
			    <td colspan="6" valign="top"> <strong> Remark: <? echo $sql_mst[0][csf('remarks')];?> </strong> </td>
				
            </tr>
		</table>
		<br>
		<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
			<thead>
				<tr>
					<th width="50">SL No.</th>
					<th width="380">Fabric  Description</th>
					<th width="150">Color Range</th>
					<th width="100">Quantity (Kg)</th>
					<th width="100">Quantity (Pcs)</th>
					<th width="80">Unit Price</th>
					<th>Amount</th>
				</tr>
            </thead>
            <tbody>
				<?				
				$i=1;
				foreach($data_array as $description => $color_range_val)
				{
					foreach($color_range_val as $color_range_id => $uom_val)
					{
						foreach($uom_val as $uom_id => $rate_val)
						{
							foreach($rate_val as $rate => $row)
							{
								$ex_desc=explode("**", $description);

								$pcs_qty=''; $kg_qty='';
								if ($uom_id==1) {
									$pcs_qty=$row['quantity'];
								}else{
									$pcs_qty='';
								}
								if ($uom_id==12) {
									$kg_qty=$row['quantity'];
								}else{
									$kg_qty='';
								}

								?>
			                    <tr>
									<td><? echo $i;?></td>
			                        <td><? echo $ex_desc[0].', '.$fabric_composition_arr[$ex_desc[1]].', GSM B-'.$ex_desc[2].'/A-'.$ex_desc[3].', Dia F-'.$ex_desc[4].'/C-'.$ex_desc[5]; ?></td>
			                        <td><? echo $color_range[$color_range_id]; ?></td>
			                        <td align="right" title="<? echo $uom_id;?>"><? echo number_format($kg_qty,2); $total_kg_qty += $kg_qty; ?></td>
			                        <td align="right" ><? echo number_format($pcs_qty,2); $total_pcs_qty += $pcs_qty; ?></td>
			                        <td align="right"><? echo number_format($rate,2); ?></td>
			                        <td align="right"><? echo number_format($row['amount'],2); $total_ammount += $row['amount'];  ?></td>
			                    </tr>
								<? 
								$i++;
								$upcharge = $row['upcharge'];
								$discount = $row['discount'];
								$net_total_amount = $row['net_total_amount'];
							}
						}
					}			
                }
				?>
				<tr>
					<td align="right" colspan="3"><strong>Total</strong></td>
					<td align="right"><strong><? echo number_format($total_kg_qty,2); ?></strong></td>
					<td align="right"><strong><? echo number_format($total_pcs_qty,2); ?></strong></td>
					<td align="right"></td>
					<td align="right"><strong><? echo number_format($total_ammount,2); ?></strong></td>
				</tr>
				<?
				if($upcharge!='' || $discount!='')
				{ 
					?>
					<tr>
						<td align="right" colspan="6"><strong>Upcharge</strong></td>
						<td align="right"><strong><? echo number_format($upcharge,2); ?></strong></td>
					</tr>
					<tr>
						<td align="right" colspan="6"><strong>Discount</strong></td>
						<td align="right"><strong><? echo number_format($discount,2); ?></strong></td>
					</tr>
					<tr>
						<td align="right" colspan="6"><strong>Net Total</strong></td>
						<td align="right"><strong><? echo number_format($net_total_amount,2); ?></strong></td>
					</tr>
					<?
				}
				?>
            </tbody> 
        </table>
        <table>
            <tr height="20"></tr>
            <tr>
                <td valign="top"><strong>Amount in Word: </strong></td>
                <td><strong><? echo number_to_words(number_format($total_ammount,2, '.', ''),'Dollar','Cents');?></strong></td>
            </tr>
            <tr> 
            <tr height="50"></tr>
        </table>
        <!-- <? 
        	echo get_spacial_instruction($data[1],"100%",152);
        ?> -->
		<table  width='1000' class="rpt_table" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
					<th width="97%" >Special Instruction</th>
				</tr>
			</thead>
		<tbody>
			<?
				$data_array = sql_select("select id, terms,terms_prefix from  wo_booking_terms_condition where booking_no='$data[1]' and entry_form=152 order by id");
				if (count($data_array) > 0) {
					$i = 0;
					foreach ($data_array as $row) {
						$i++;
						if($row[csf('terms_prefix')]!='')
						{
							?>
								<tr >
									<td ><?echo $row[csf('terms_prefix')];?></td>
								</tr>
							<?
						}
						?>
						<tr >
							<td ><?echo $row[csf('terms')];?></td>
						</tr>
						<?
					}
				}
			?>
		</tbody>
	</table>


        <table>
            <tr height="50"></tr>
            <tr>
            	<td >For On Behalf of</td>
            </tr>
            <tr>
            	<td ><? echo $company_arr[$sql_mst[0][csf('exporter_id')]]; ?></td>
            </tr>
			<tr height="50"></tr>
            <tr>
            	<td style="border-top-style:dotted; border-top-width:3px;">Authorized Signature</td>
            </tr>
            <tr height="50"></tr>
        </table>
	</div>
    <?
	exit();	 
}

//This button created by Rakib
if($action=="print_new_rpt_9") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_id=$data[0];
	$mst_id=$data[1];
	$system_id = $data[2];

	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_id' and is_deleted=0","image_location");
	
	$sql_company = sql_select("SELECT * FROM lib_company WHERE is_deleted=0 and status_active=1");
  	foreach($sql_company as $row) 
  	{
		if($row[csf('plot_no')] !='') $plot_no = $row[csf('plot_no')].', ';
		if($row[csf('level_no')] !='') $level_no = $row[csf('level_no')].', ';
		if($row[csf('road_no')] !='') $road_no = $row[csf('road_no')].', ';
		if($row[csf('block_no')] !='') $block_no = $row[csf('block_no')].', ';
		if($row[csf('city')] !='') $city = $row[csf('city')].', ';
		if($row[csf('zip_code')] !='') $zip_code = $row[csf('zip_code')].', ';
		if($row[csf('country_id')] !=0) $country = $country_arr[$row[csf('country_id')]];
		if($row[csf('email')] !='') $company_email = "Email:&nbsp;".$row[csf('email')];
		if($row[csf('contact_no')] !='') $contact_no = "TEL#&nbsp;".$row[csf('contact_no')];
		if($row[csf('bin_no')] !='') $bin_no = $row[csf('bin_no')];
		
		$company_address[$row[csf('id')]] = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
		$company_arr[$row[csf('id')]]=$row[csf('company_name')];
	}	

	$sql ="SELECT a.id, a.pi_date, a.pi_revised_date, a.last_shipment_date, a.pi_number, a.within_group, a.hs_code, a.advising_bank, a.item_category_id,a.upcharge,a.discount,a.net_total_amount, b.id as dtls_id, b.work_order_no, b.work_order_id, b.color_id, b.construction, b.composition, b.gsm, b.dia_width, b.uom, b.quantity, b.rate, b.amount, b.remarks from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.id=$mst_id and a.exporter_id=$company_id and a.item_category_id=10 and a.status_active=1 and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0";
	$sql_res=sql_select($sql);
	$check_master_arr=array();
	$all_data_arr=array();
	foreach ($sql_res as $val) 
	{
		$sales_order_id.=$val[csf('work_order_id')].',';
		if ($check_master_arr[$val[csf('id')]]==""){
			$check_master_id[$val[csf('id')]]=$val[csf('id')];
			$pi_date=$val[csf('pi_date')];	
			$pi_revised_date=$val[csf('pi_revised_date')];	
			$pi_number=$val[csf('pi_number')];
			$advising_bank=$val[csf('advising_bank')];
			$hs_code=$val[csf('hs_code')];
			$item_category_name=$export_item_category[$val[csf('item_category_id')]];
		}
		$key=$val[csf('work_order_no')].'**'.$val[csf('construction')].'**'.$val[csf('composition')].'**'.$val[csf('color_id')].'**'.$val[csf('gsm')].'**'.$val[csf('dia_width')].'**'.$val[csf('uom')].'**'.$val[csf('rate')];
		$all_data_arr[$key]['work_order_no']=$val[csf('work_order_no')];
		$all_data_arr[$key]['work_order_id']=$val[csf('work_order_id')];
		$all_data_arr[$key]['construction']=$val[csf('construction')];
		$all_data_arr[$key]['composition']=$val[csf('composition')];
		$all_data_arr[$key]['color_id']=$val[csf('color_id')];
		$all_data_arr[$key]['gsm']=$val[csf('gsm')];
		$all_data_arr[$key]['dia_width']=$val[csf('dia_width')];
		$all_data_arr[$key]['uom']=$val[csf('uom')];
		$all_data_arr[$key]['rate']=$val[csf('rate')];
		$all_data_arr[$key]['quantity']+=$val[csf('quantity')];
		$all_data_arr[$key]['amount']+=$val[csf('rate')]*$val[csf('quantity')];
		$all_data_arr[$key]['remarks']=$val[csf('remarks')];
		$upcharge=$val[csf('upcharge')];
		$discount=$val[csf('discount')];
		$net_total_amount=$val[csf('net_total_amount')];
	}

	$sales_order_ids=implode(',',array_unique(explode(',',rtrim($sales_order_id,','))));
	//echo $sales_order_ids.'system';
	

	$sql_sales_order=sql_select("select id, company_id, buyer_id, within_group, style_ref_no, ship_mode, booking_date, sales_booking_no, booking_id, delivery_date, booking_approval_date, po_job_no from fabric_sales_order_mst where id in($sales_order_ids) and entry_form=109 and status_active=1 and is_deleted=0");
	$sales_order_no='';
	$booking_id_arr=array();
	foreach ($sql_sales_order as $val) {
		$style_ref_no.=$val[csf('style_ref_no')].',';
		$ship_mode=$shipment_mode[$val[csf('ship_mode')]];
		if ($val[csf('buyer_id')] != "") $buyer_id=$val[csf('buyer_id')];
		if ($val[csf('booking_date')] != "") $receive_date=$val[csf('booking_date')];
		if ($val[csf('booking_id')] != "") {
			$booking_id.=$val[csf('booking_id')].',';
			$booking_id_arr[$val[csf('booking_id')]]=$val[csf('id')];
		}
		$company_ID=$val[csf('company_id')];
		$within_group=$val[csf('within_group')];
		$delivery_date=change_date_format($val[csf('delivery_date')]).',';
		$order_date.=change_date_format($val[csf('booking_approval_date')]).',';
		$order_no.=$val[csf('sales_booking_no')].',';
		if ($val[csf('po_job_no')]) {
			$po_job_no.=$val[csf('po_job_no')].',';
		}		
	}

	$booking_ids=implode(',',array_unique(explode(',',rtrim($booking_id,','))));
	$style_ref_nos=implode(', ',array_unique(explode(',',rtrim($style_ref_no,','))));
	

	if ($booking_ids != "")
	{
		$sql_order=sql_select("select a.id, a.buyer_id, b.booking_no, c.po_number from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id in($booking_ids)");
		$booking_no_arr=array();
		foreach ($sql_order as $val) 
		{
			$po_number.=$val[csf('po_number')].',';
			$buyer_names.=$buyer_arr[$val[csf('buyer_id')]].',';
		}
	}
	$po_numbers=implode(', ',array_unique(explode(',',rtrim($po_number,','))));
	//echo '<pre>';print_r($po_number_arr);

	$sql_buyer = sql_select("select id, buyer_name, country_id, buyer_email, address_1, address_2, address_3, address_4 from lib_buyer where status_active=1 and is_deleted=0");    
    foreach ($sql_buyer as $row) 
    {
    	$buyer_address=$buyer_email="";
        if ($row[csf('address_1')] !='') $buyer_address .= $row[csf('address_1')].', ';
        if ($row[csf('address_2')] !='') $buyer_address .= $row[csf('address_2')].', ';
        if ($row[csf('address_3')] !='') $buyer_address .= $row[csf('address_3')].', ';
        if ($row[csf('address_4')] !='') $buyer_address .= $row[csf('address_4')].', ';
        if ($row[csf('country_id')] !='') $buyer_address .= $country_arr[$row[csf('country_id')]];
        if ($row[csf('buyer_email')] !='') $buyer_email = "Email:&nbsp;".$row[csf('buyer_email')];

        $buyer_details_arr[$row[csf('id')]]['buyer_address']=$buyer_address;
        $buyer_details_arr[$row[csf('id')]]['buyer_email']=$buyer_email;
    }
	//echo '<pre>';print_r($sales_order_arr);

    if ($advising_bank != ""){
    	//echo "select a.id, a.bank_name, a.branch_name, a.address, a.swift_code, b.account_type, b.account_no from lib_bank a, lib_bank_account b where a.id=$advising_bank and a.advising_bank=1 and a.is_deleted=0 and a.status_active=1 and b.account_type=10 and b.status_active=1 and b.is_deleted=0 and company_id=$company_id";
    	$sql_bank = sql_select("select a.id, a.bank_name, a.branch_name, a.address, a.swift_code, b.account_type, b.account_no from lib_bank a, lib_bank_account b where a.id=$advising_bank and a.advising_bank=1 and a.is_deleted=0 and a.status_active=1 and b.account_type=10 and b.status_active=1 and b.is_deleted=0 and company_id=$company_id");
	  	foreach($sql_bank as $row)
	  	{
	  		$bank_name=$row[csf('bank_name')];
	  		$branch_name=$row[csf('branch_name')];
	  		$address=$row[csf('address')];
	  		$swift_code=$row[csf('swift_code')];
	  		$account_no=$row[csf('account_no')];
		}
    }

	/*if ($db_type==0)
	{
		$sql="select a.id, concat(a.bank_name,' ( ', a.branch_name,' )') as bank_name from lib_bank a , lib_bank_account b where a.advising_bank=1 and a.id=b.account_id and a.status_active=1 and b.status_active=1 and b.company_id=$data group by a.id ,a.bank_name, a.branch_name ";
	}
	else
	{
		$sql="select a.id, (a.bank_name||' ( '|| a.branch_name||' )') as bank_name from lib_bank a , lib_bank_account b where a.advising_bank=1 and a.id=b.account_id and a.status_active=1 and b.status_active=1 and b.company_id=$data group by a.id ,a.bank_name, a.branch_name ";
	}*/
    ?>
    <style type="text/css">
    	table>tr>th,td{word-break: break-all;}
    </style>
	<div style="width:1200px">
		<table width="1200" cellspacing="0" border="0">
	        <tr>
	            <td colspan="2" rowspan="5" width="150" style="vertical-align: top;">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
				</td>
				<td colspan="8" style="font-size:xx-large; justify-content: center;text-align: center;" >
	            	<strong style="justify-content: center; text-align: center;"><? echo $company_arr[$company_id]; ?></strong>
	            </td>
	            <td colspan="3" style="font-size:20px;"><strong>Proforma Invoice</strong></td>          
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $company_address[$company_id]; ?></strong></td>
	            <td colspan="3" style="font-size:16px;">PI Date:&nbsp;<? echo change_date_format($pi_date); ?></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $contact_no; ?></strong></td>
	            <td colspan="3" style="font-size:16px;">PI Revised Date:&nbsp;<? echo change_date_format($pi_revised_date); ?></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $company_email; ?></strong></td>
	            <td colspan="3" style="font-size:16px;"><strong>PI No:&nbsp;<? echo $pi_number; ?></strong></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; ">&nbsp;</strong></td>
	            <td colspan="3" style="font-size:16px;"><strong>BIN No:&nbsp;<? echo $bin_no; ?></strong></td>
	        </tr>	        
	    </table>
	    <br>
		<table class="rpt_table" width="1200" cellspacing="1" rules="all" border="1">
			<tr>
				<td colspan="6" width="520" style="font-size: 18px;"><strong>For Account & Risk Of:</strong></td>
				<td colspan="7" width="480" style="font-size: 18px;"><strong>Beneficiary:</strong></td>
			</tr>
			<tr>
				<td colspan="6"  width="520" style="font-size: 18px;"><strong><? if ($within_group==1) echo $company_arr[$buyer_id].'<br>'.$company_address[$buyer_id].'<br>'.$contact_no.'<br>'.$company_email; else if ($within_group==2) echo $buyer_arr[$buyer_id].'<br>'.$buyer_details_arr[$buyer_id]["buyer_address"].'<br>'.$buyer_details_arr[$buyer_id]["buyer_email"]; ?></strong></td>
				<td colspan="7"  width="480" style="font-size: 18px;"><strong><? echo $company_arr[$company_id].'<br>'.$company_address[$company_id].'<br>'.$contact_no.'<br>'.$company_email; ?></strong></td>
			</tr>
			<tr>
				<td colspan="6"  width="520" style="font-size: 18px;"><strong>Buyer:&nbsp;<? echo implode(',',array_unique(explode(',',rtrim($buyer_names,',')))); ?><br>Garments Style:&nbsp;<? if (strlen($style_ref_nos) > 45) echo substr($style_ref_nos, 0, 45).'...'; else echo $style_ref_nos; ?><br>Garmments PO:&nbsp;<? if (strlen($po_numbers) > 45) echo substr($po_numbers, 0, 45).'...'; else echo $po_numbers; ?></strong></td>
				<td colspan="7"  width="480" style="font-size: 18px;"><strong><? echo 'Advising Bank:<br>'.$bank_name.'<br>'.$branch_name.', '.$address.'<br>Account No:&nbsp;'.$account_no.'<br>Swift Code:&nbsp;'.$swift_code; ?></strong></td>
			</tr>

			<tr>
				<td colspan="3" width="250" style="font-size: 18px;"><strong>Order No:</strong></td>
				<td colspan="3" width="270" style="font-size: 18px;"><strong>Order Date:</strong></td>
				<td colspan="4" width="210" style="font-size: 18px;"><strong>Shipping Mode:</strong></td>
				<td colspan="3" width="270" style="font-size: 18px;"><strong>Delivery Date:</strong></td>
			</tr>
			<tr>
				<td colspan="3" width="250" style="font-size: 18px;"><strong><? echo implode(',',array_unique(explode(',',rtrim($order_no,',')))); ?></strong></td>
				<td colspan="3" width="270" style="font-size: 18px;"><strong><? echo implode(',',array_unique(explode(',',rtrim($order_date,',')))); ?></strong></td>
				<td colspan="4" width="210" style="font-size: 18px;"><strong><? echo $ship_mode; ?></strong></td>
				<td colspan="3" width="270" style="font-size: 18px;"><strong><? echo implode(',',array_unique(explode(',',rtrim($delivery_date,',')))); ?></strong></td>
			</tr>
			<tr>
				<td colspan="13" width="1000" style="font-size: 18px;"><strong><? echo $item_category_name; ?>&nbsp;(H.S. Code - <? echo $hs_code; ?>) charge for 100 % export oriented readymade garments industries  As Follows:</strong></td>
			</tr>
        </table>
        <br>
        <table width="1200" cellspacing="0" align="center" border="1" rules="all" class="rpt_table">
            <thead bgcolor="#dddddd" align="center">                
                <th width="50">SL</th>
                <th width="140">Sales Order No</th>
                <th width="120">Construction</th>
                <th width="180">Composition</th>
                <th width="100">Color</th>
                <th width="50">GSM</th>
                <th width="80">Dia/Width</th>
                <th width="50">UOM</th>
                <th width="100">Finish Qty</th>
                <th width="80">Rate USD</th>
                <th width="80">Amount in USD</th>
                <th width="80">Remarks</th>
            </thead>
            <tbody>
                <?
                $i=1;
                $tot_wo_qty=$tot_amount=0;
                foreach ($all_data_arr as $row) 
                {
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="50"><? echo $i; ?></td>
                        <td width="140"><? echo $row['work_order_no']; ?></td>
                        <td width="120"><? echo $row['construction']; ?></td>
                        <td width="180"><? echo $row['composition']; ?></td>
                        <td width="100"><? echo $color_arr[$row['color_id']]; ?></td>
                        <td width="50"><? echo $row['gsm']; ?></td>
                        <td width="80"><? echo $row['dia_width']; ?></td>
                        <td width="50"><? echo $unit_of_measurement[$row['uom']]; ?></td>
                        <td width="100" align="right"><? echo number_format($row['quantity'],2); ?></td>
                        <td width="80" align="right"><? echo number_format($row['rate'],4); ?></td>                        
                        <td width="80" align="right"><? echo number_format($row['amount'],2); ?></td>
                        <td ><? echo $row['remarks']; ?></td>
                    </tr>
                    <?
                    $tot_quantity+=$row['quantity'];
                    $tot_amount+=$row['amount'];
                    $i++;
                }
                ?>    
            </tbody>                
            <tfoot>
                <tr bgcolor="#dddddd">
                    <td colspan="8" align="right"><strong>Total</strong></td>
                    <td align="right"><strong><? echo number_format($tot_quantity,2); ?></strong></td>
                    <td>&nbsp;</td>
                    <td align="right"><strong><? echo number_format($tot_amount,2); ?></strong></td>
					<td>&nbsp;</td>
                </tr>
                <tr bgcolor="#dddddd">
                    <td colspan="10" align="right"><strong>Upcharge</strong></td>
                    <td align="right"><strong><? echo number_format($upcharge,2); ?></strong></td>
					<td>&nbsp;</td>
                </tr>
                <tr bgcolor="#dddddd">
                    <td colspan="10" align="right"><strong>Discount</strong></td>
                    <td align="right"><strong><? echo number_format($discount,2); ?></strong></td>
					<td>&nbsp;</td>
                </tr>
                <tr bgcolor="#dddddd">
                    <td colspan="10" align="right"><strong>Net Total</strong></td>
                    <td align="right"><strong><? echo number_format($net_total_amount,2); ?></strong></td>
					<td>&nbsp;</td>
                </tr>
            </tfoot> 
        </table>
        <table>
            <tr height="20"></tr>
            <tr>
                <td valign="top"><strong>In-Words: </strong></td>
                <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>

            </tr>
            <tr> 
            <tr height="50"></tr>
        </table>
        <? 
        	echo get_spacial_instruction($mst_id,"100%",152);
        	//echo signature_table(257, $company_id, "1000px");
        	echo signature_table(257, $company_id, "1000px", "", "70", $user_arr[$user_id]);
        ?>        
	</div>
    <?
	exit();	 
}

if($action=="print_new_rpt_10") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_id=$data[0];
	$mst_id=$data[1];
	$system_id = $data[2];
	$cbo_item_category_id = $data[3];

	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_id' and is_deleted=0","image_location");
	
	$sql_company = sql_select("SELECT * FROM lib_company WHERE is_deleted=0 and status_active=1");
  	foreach($sql_company as $row) 
  	{
		if($row[csf('plot_no')] !='') $plot_no = $row[csf('plot_no')].', ';
		if($row[csf('level_no')] !='') $level_no = $row[csf('level_no')].', ';
		if($row[csf('road_no')] !='') $road_no = $row[csf('road_no')].', ';
		if($row[csf('block_no')] !='') $block_no = $row[csf('block_no')].', ';
		if($row[csf('city')] !='') $city = $row[csf('city')].', ';
		if($row[csf('zip_code')] !='') $zip_code = $row[csf('zip_code')].', ';
		if($row[csf('country_id')] !=0) $country = $country_arr[$row[csf('country_id')]];
		if($row[csf('email')] !='') $company_email = "Email:&nbsp;".$row[csf('email')];
		if($row[csf('contact_no')] !='') $contact_no = "TEL#&nbsp;".$row[csf('contact_no')];
		if($row[csf('bin_no')] !='') $bin_no = $row[csf('bin_no')];
		
		$company_address[$row[csf('id')]] = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
		$company_arr[$row[csf('id')]]=$row[csf('company_name')];
	}	

	$sql ="SELECT a.id, a.pi_date, a.exporter_id, a.pi_revised_date, a.last_shipment_date, a.pi_number, a.within_group, a.buyer_id, a.hs_code, a.advising_bank, a.item_category_id,a.upcharge,a.discount,a.net_total_amount, b.id as dtls_id, b.work_order_no, b.work_order_id, b.color_id, b.construction, b.composition, b.gsm, b.dia_width, b.uom, b.quantity, b.rate, b.amount, b.remarks from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.id=$mst_id and a.exporter_id=$company_id and a.item_category_id=$cbo_item_category_id and a.status_active=1 and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0";
	$sql_res=sql_select($sql);
	$check_master_arr=array();
	$all_data_arr=array();
	foreach ($sql_res as $val) 
	{
		$work_order_id.=$val[csf('work_order_id')].',';
		if ($check_master_arr[$val[csf('id')]]==""){
			$check_master_id[$val[csf('id')]]=$val[csf('id')];
			$pi_date=$val[csf('pi_date')];
			$buyer_id=$val[csf('buyer_id')];
			$within_group=$val[csf('within_group')];
			$pi_revised_date=$val[csf('pi_revised_date')];	
			$pi_number=$val[csf('pi_number')];
			$advising_bank=$val[csf('advising_bank')];
			$hs_code=$val[csf('hs_code')];
			$item_category_name=$export_item_category[$val[csf('item_category_id')]];
		}
		$key=$val[csf('work_order_no')].'**'.$val[csf('construction')].'**'.$val[csf('composition')].'**'.$val[csf('color_id')].'**'.$val[csf('gsm')].'**'.$val[csf('dia_width')].'**'.$val[csf('uom')].'**'.$val[csf('rate')];
		$all_data_arr[$key]['work_order_no']=$val[csf('work_order_no')];
		$all_data_arr[$key]['work_order_id']=$val[csf('work_order_id')];
		$all_data_arr[$key]['construction']=$val[csf('construction')];
		$all_data_arr[$key]['composition']=$val[csf('composition')];
		$all_data_arr[$key]['color_id']=$val[csf('color_id')];
		$all_data_arr[$key]['gsm']=$val[csf('gsm')];
		$all_data_arr[$key]['dia_width']=$val[csf('dia_width')];
		$all_data_arr[$key]['uom']=$val[csf('uom')];
		$all_data_arr[$key]['rate']=$val[csf('rate')];
		$all_data_arr[$key]['quantity']+=$val[csf('quantity')];
		$all_data_arr[$key]['amount']+=$val[csf('rate')]*$val[csf('quantity')];
		$all_data_arr[$key]['remarks']=$val[csf('remarks')];
		$upcharge=$val[csf('upcharge')];
		$discount=$val[csf('discount')];
		$net_total_amount=$val[csf('net_total_amount')];
	}

	$booking_ids=implode(',',array_unique(explode(',',rtrim($work_order_id,','))));
	//echo $sales_order_ids.'system';
	

	if ($booking_ids != "")
	{
		$sql_order="select a.id, a.buyer_id, a.booking_date, a.delivery_date, b.booking_no, c.po_number, d.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.id in($booking_ids)";
		$sql_order_res=sql_select($sql_order);
		$booking_no_arr=array();
		foreach ($sql_order_res as $val)
		{
			$po_number.=$val[csf('po_number')].',';
			$buyer_names.=$buyer_arr[$val[csf('buyer_id')]].',';
			$booking_date.=change_date_format($val[csf('booking_date')]).',';
			$delivery_date.=change_date_format($val[csf('delivery_date')]).',';
			$booking_no.=$val[csf('booking_no')].',';
			$style_ref_no.=$val[csf('style_ref_no')].',';
		}
	}
	$po_numbers=implode(', ',array_unique(explode(',',rtrim($po_number,','))));
	$booking_dates=implode(', ',array_unique(explode(',',rtrim($booking_date,','))));
	$delivery_dates=implode(', ',array_unique(explode(',',rtrim($delivery_date,','))));
	$booking_nos=implode(', ',array_unique(explode(',',rtrim($booking_no,','))));
	$style_ref_nos=implode(', ',array_unique(explode(',',rtrim($style_ref_no,','))));
	//echo '<pre>';print_r($po_number_arr);

	$sql_buyer = sql_select("select id, buyer_name, country_id, buyer_email, address_1, address_2, address_3, address_4 from lib_buyer where status_active=1 and is_deleted=0");    
    foreach ($sql_buyer as $row) 
    {
    	$buyer_address=$buyer_email="";
        if ($row[csf('address_1')] !='') $buyer_address .= $row[csf('address_1')].', ';
        if ($row[csf('address_2')] !='') $buyer_address .= $row[csf('address_2')].', ';
        if ($row[csf('address_3')] !='') $buyer_address .= $row[csf('address_3')].', ';
        if ($row[csf('address_4')] !='') $buyer_address .= $row[csf('address_4')].', ';
        if ($row[csf('country_id')] !='') $buyer_address .= $country_arr[$row[csf('country_id')]];
        if ($row[csf('buyer_email')] !='') $buyer_email = "Email:&nbsp;".$row[csf('buyer_email')];

        $buyer_details_arr[$row[csf('id')]]['buyer_address']=$buyer_address;
        $buyer_details_arr[$row[csf('id')]]['buyer_email']=$buyer_email;
    }
	//echo '<pre>';print_r($sales_order_arr);

    if ($advising_bank != ""){
    	$sql_bank = sql_select("select a.id, a.bank_name, a.branch_name, a.address, a.swift_code, b.account_type, b.account_no from lib_bank a, lib_bank_account b where a.id=$advising_bank and a.advising_bank=1 and a.is_deleted=0 and a.status_active=1 and b.account_type=10 and b.status_active=1 and b.is_deleted=0 and company_id=$company_id");
	  	foreach($sql_bank as $row)
	  	{
	  		$bank_name=$row[csf('bank_name')];
	  		$branch_name=$row[csf('branch_name')];
	  		$address=$row[csf('address')];
	  		$swift_code=$row[csf('swift_code')];
	  		$account_no=$row[csf('account_no')];
		}
    }
    ?>
    <style type="text/css">
    	table>tr>th,td{word-break: break-all;}
    </style>
	<div style="width:1200px">
		<table width="1200" cellspacing="0" border="0">
	        <tr>
	            <td colspan="2" rowspan="5" width="150" style="vertical-align: top;">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
				</td>
				<td colspan="8" style="font-size:xx-large; justify-content: center;text-align: center;" >
	            	<strong style="justify-content: center; text-align: center;"><? echo $company_arr[$company_id]; ?></strong>
	            </td>
	            <td colspan="3" style="font-size:20px;"><strong>Proforma Invoice</strong></td>          
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $company_address[$company_id]; ?></strong></td>
	            <td colspan="3" style="font-size:16px;">PI Date:&nbsp;<? echo change_date_format($pi_date); ?></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $contact_no; ?></strong></td>
	            <td colspan="3" style="font-size:16px;">PI Revised Date:&nbsp;<? echo change_date_format($pi_revised_date); ?></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $company_email; ?></strong></td>
	            <td colspan="3" style="font-size:16px;"><strong>PI No:&nbsp;<? echo $pi_number; ?></strong></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; ">&nbsp;</strong></td>
	            <td colspan="3" style="font-size:16px;"><strong>BIN No:&nbsp;<? echo $bin_no; ?></strong></td>
	        </tr>	        
	    </table>
	    <br>
		<table class="rpt_table" width="1200" cellspacing="1" rules="all" border="1">
			<tr>
				<td colspan="6" width="520" style="font-size: 18px;"><strong>For Account & Risk Of:</strong></td>
				<td colspan="7" width="480" style="font-size: 18px;"><strong>Beneficiary:</strong></td>
			</tr>
			<tr>
				<td colspan="6"  width="520" style="font-size: 18px;"><strong><? if ($within_group==1) echo $company_arr[$buyer_id].'<br>'.$company_address[$buyer_id].'<br>'.$contact_no.'<br>'.$company_email; else if ($within_group==2) echo $buyer_arr[$buyer_id].'<br>'.$buyer_details_arr[$buyer_id]["buyer_address"].'<br>'.$buyer_details_arr[$buyer_id]["buyer_email"]; ?></strong></td>
				<td colspan="7"  width="480" style="font-size: 18px;"><strong><? echo $company_arr[$company_id].'<br>'.$company_address[$company_id].'<br>'.$contact_no.'<br>'.$company_email; ?></strong></td>
			</tr>
			<tr>
				<td colspan="6"  width="520" style="font-size: 18px;"><strong>Buyer:&nbsp;<? echo implode(',',array_unique(explode(',',rtrim($buyer_names,',')))); ?><br>Garments Style:&nbsp;<? if (strlen($style_ref_nos) > 45) echo substr($style_ref_nos, 0, 45).'...'; else echo $style_ref_nos; ?><br>Garmments PO:&nbsp;<? if (strlen($po_numbers) > 45) echo substr($po_numbers, 0, 45).'...'; else echo $po_numbers; ?></strong></td>
				<td colspan="7"  width="480" style="font-size: 18px;"><strong><? echo 'Advising Bank:<br>'.$bank_name.'<br>'.$branch_name.', '.$address.'<br>Account No:&nbsp;'.$account_no.'<br>Swift Code:&nbsp;'.$swift_code; ?></strong></td>
			</tr>

			<tr>
				<td colspan="3" width="250" style="font-size: 18px;"><strong>Order No:</strong></td>
				<td colspan="3" width="270" style="font-size: 18px;"><strong>Order Date:</strong></td>
				<td colspan="4" width="210" style="font-size: 18px;"><strong>Shipping Mode:</strong></td>
				<td colspan="3" width="270" style="font-size: 18px;"><strong>Delivery Date:</strong></td>
			</tr>
			<tr>
				<td colspan="3" width="250" style="font-size: 18px;"><strong><? echo $booking_nos; ?></strong></td>
				<td colspan="3" width="270" style="font-size: 18px;"><strong><? echo $booking_dates; ?></strong></td>
				<td colspan="4" width="210" style="font-size: 18px;"><strong><? echo 'Sea'; ?></strong></td>
				<td colspan="3" width="270" style="font-size: 18px;"><strong><? echo $delivery_dates; ?></strong></td>
			</tr>
			<tr>
				<td colspan="13" width="1000" style="font-size: 18px;"><strong><? echo $item_category_name; ?>&nbsp;(H.S. Code - <? echo $hs_code; ?>) charge for 100 % export oriented readymade garments industries  As Follows:</strong></td>
			</tr>
        </table>
        <br>
        <table width="1200" cellspacing="0" align="center" border="1" rules="all" class="rpt_table">
            <thead bgcolor="#dddddd" align="center">                
                <th width="50">SL</th>
                <th width="120">Construction</th>
                <th width="250">Composition</th>
                <th width="100">Color</th>
                <th width="50">GSM</th>
                <th width="80">Dia/Width</th>
                <th width="50">UOM</th>
                <th width="100">Finish Qty</th>
                <th width="80">Rate USD</th>
                <th width="80">Amount in USD</th>
                <th>Remarks</th>
            </thead>
            <tbody>
                <?
                $i=1;
                $tot_wo_qty=$tot_amount=0;
                foreach ($all_data_arr as $row) 
                {
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="50"><? echo $i; ?></td>
                        <td width="120"><? echo $row['construction']; ?></td>
                        <td width="250"><? echo $row['composition']; ?></td>
                        <td width="100"><? echo $color_arr[$row['color_id']]; ?></td>
                        <td width="50"><? echo $row['gsm']; ?></td>
                        <td width="80"><? echo $row['dia_width']; ?></td>
                        <td width="50"><? echo $unit_of_measurement[$row['uom']]; ?></td>
                        <td width="100" align="right"><? echo number_format($row['quantity'],2); ?></td>
                        <td width="80" align="right"><? echo number_format($row['rate'],4); ?></td>                        
                        <td width="80" align="right"><? echo number_format($row['amount'],2); ?></td>
                        <td ><? echo $row['remarks']; ?></td>
                    </tr>
                    <?
                    $tot_quantity+=$row['quantity'];
                    $tot_amount+=$row['amount'];
                    $i++;
                }
                ?>    
            </tbody>                
            <tfoot>
                <tr bgcolor="#dddddd">
                    <td colspan="7" align="right"><strong>Total</strong></td>
                    <td align="right"><strong><? echo number_format($tot_quantity,2); ?></strong></td>
                    <td>&nbsp;</td>
                    <td align="right"><strong><? echo number_format($tot_amount,2); ?></strong></td>
					<td>&nbsp;</td>
                </tr>
				<tr bgcolor="#dddddd">
                    <td colspan="9" align="right"><strong>Upcharge</strong></td>
                    <td align="right"><strong><? echo number_format($upcharge,2); ?></strong></td>
					<td>&nbsp;</td>
                </tr>
                <tr bgcolor="#dddddd">
                    <td colspan="9" align="right"><strong>Discount</strong></td>
                    <td align="right"><strong><? echo number_format($discount,2); ?></strong></td>
					<td>&nbsp;</td>
                </tr>
                <tr bgcolor="#dddddd">
                    <td colspan="9" align="right"><strong>Net Total</strong></td>
                    <td align="right"><strong><? echo number_format($net_total_amount,2); ?></strong></td>
					<td>&nbsp;</td>
                </tr>
            </tfoot> 
        </table>
        <table>
            <tr height="20"></tr>
            <tr>
                <td valign="top"><strong>In-Words: </strong></td>
                <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
            </tr>
            <tr> 
            <tr height="50"></tr>
        </table>
        <? 
        	echo get_spacial_instruction($mst_id,"100%",152);
        	//echo signature_table(257, $company_id, "1000px");
        	echo signature_table(257, $company_id, "1000px", "", "70", $user_arr[$user_id]);
        ?>        
	</div>
    <?
	exit();	 
}

if($action=="print_new_rpt_11") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_id=$data[0];
	$mst_id=$data[1];
	$system_id = $data[2];
	$cbo_item_category_id = $data[3];

	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4 and status_active=1",'id','item_name');
	$user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_id' and is_deleted=0","image_location");
	
	$sql_company = sql_select("SELECT * FROM lib_company WHERE is_deleted=0 and status_active=1");
  	foreach($sql_company as $row) 
  	{
		if($row[csf('plot_no')] !='') $plot_no = $row[csf('plot_no')].', ';
		if($row[csf('level_no')] !='') $level_no = $row[csf('level_no')].', ';
		if($row[csf('road_no')] !='') $road_no = $row[csf('road_no')].', ';
		if($row[csf('block_no')] !='') $block_no = $row[csf('block_no')].', ';
		if($row[csf('city')] !='') $city = $row[csf('city')].', ';
		if($row[csf('zip_code')] !='') $zip_code = $row[csf('zip_code')].', ';
		if($row[csf('country_id')] !=0) $country = $country_arr[$row[csf('country_id')]];
		if($row[csf('email')] !='') $company_email = "Email:&nbsp;".$row[csf('email')];
		if($row[csf('contact_no')] !='') $contact_no = "TEL#&nbsp;".$row[csf('contact_no')];
		if($row[csf('bin_no')] !='') $bin_no = $row[csf('bin_no')];
		
		$company_address[$row[csf('id')]] = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
		$company_arr[$row[csf('id')]]=$row[csf('company_name')];
	}	

	$sql ="SELECT a.id as ID, a.pi_date as PI_DATE, a.pi_revised_date as PI_REVISED_DATE, a.pi_number as PI_NUMBER, a.within_group as WITHIN_GROUP, a.buyer_id as BUYER_ID, a.hs_code as HS_CODE, a.advising_bank as ADVISING_BANK, a.item_category_id as ITEM_CATEGORY_ID,a.upcharge as UPCHARGE,a.discount as DISCOUNT,a.net_total_amount as NET_TOTAL_AMOUNT, b.id as dtls_id, b.work_order_no as WORK_ORDER_NO, b.work_order_id as WORK_ORDER_ID, b.booking as BOOKING, b.hs_code as HS_CODE_DTLS,b.gmts_item_id as GMTS_ITEM_ID, b.item_desc as ITEM_DESC, b.uom as UOM, b.quantity as QUANTITY, b.amount as AMOUNT, d.section as SECTION from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c,subcon_ord_dtls d where a.id=b.pi_id and a.id=$mst_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and a.exporter_id=$company_id and a.item_category_id=$cbo_item_category_id and a.status_active=1 and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0";
	// $sql = "SELECT id, work_order_no, work_order_id, work_order_dtls_id, hs_code, booking, gmts_item_id, item_desc, color_id, item_size, uom, quantity, rate, amount from com_export_pi_dtls where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0";
	// echo $sql;
	$sql_res=sql_select($sql);
	$check_master_arr=array();
	$all_data_arr=array();
	foreach ($sql_res as $val) 
	{
		$work_order_id.=$val['WORK_ORDER_ID'].',';
		if ($check_master_arr[$val['ID']]==""){
			$check_master_id[$val['ID']]=$val['ID'];
			$pi_date=$val['PI_DATE'];
			$buyer_id=$val['BUYER_ID'];
			$within_group=$val['WITHIN_GROUP'];
			$pi_revised_date=$val['PI_REVISED_DATE'];	
			$pi_number=$val['PI_NUMBER'];
			$advising_bank=$val['ADVISING_BANK'];
			$hs_code=$val['HS_CODE'];
			$item_category_name=$export_item_category[$val['ITEM_CATEGORY_ID']];
			$upcharge=$val['UPCHARGE'];
			$discount=$val['DISCOUNT'];
			$net_total_amount=$val['NET_TOTAL_AMOUNT'];
		}
		$key=$val['WORK_ORDER_NO'].'**'.$val['BOOKING'].'**'.$val['SECTION'].'**'.$val['GMTS_ITEM_ID'].'**'.$val['ITEM_DESC'].'**'.$val['UOM'];
		$all_data_arr[$key]['work_order_no']=$val['WORK_ORDER_NO'];
		$all_data_arr[$key]['booking']=$val['BOOKING'];
		$all_data_arr[$key]['hs_code_dtls'].=$val['HS_CODE_DTLS'].',';
		$all_data_arr[$key]['section']=$val['SECTION'];
		$all_data_arr[$key]['gmts_item_id']=$val['GMTS_ITEM_ID'];
		$all_data_arr[$key]['item_desc']=$val['ITEM_DESC'];
		$all_data_arr[$key]['uom']=$val['UOM'];
		$all_data_arr[$key]['quantity']+=$val['QUANTITY'];
		$all_data_arr[$key]['amount']+=$val['AMOUNT'];
	}
	$booking_ids=implode(',',array_unique(explode(',',rtrim($work_order_id,','))));
	
	if ($booking_ids != "")
	{
		$sql_order="SELECT b.buyer_po_no as BUYER_PO_NO, b.buyer_style_ref as BUYER_STYLE_REF, b.buyer_buyer as BUYER_BUYER  from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($booking_ids)";
		// echo $sql_order;
		$sql_order_res=sql_select($sql_order);
		$booking_no_arr=array();
		foreach ($sql_order_res as $val)
		{
			$po_number.=$val['BUYER_PO_NO'].',';
			$style_ref_no.=$val['BUYER_STYLE_REF'].',';
			if($within_group==1){ $buyer_names.=$buyer_arr[$val['BUYER_BUYER']].','; }
			else{ $buyer_names.=$val['BUYER_BUYER'].','; }
		}
	}
	$po_numbers=implode(', ',array_unique(explode(',',rtrim($po_number,','))));
	$style_ref_nos=implode(', ',array_unique(explode(',',rtrim($style_ref_no,','))));
	$buyer_names=implode(', ',array_unique(explode(',',rtrim($buyer_names,','))));

	$sql_buyer = sql_select("select id, buyer_name, country_id, buyer_email, address_1, address_2, address_3, address_4 from lib_buyer where status_active=1 and is_deleted=0");    
    foreach ($sql_buyer as $row) 
    {
    	$buyer_address=$buyer_email="";
        if ($row[csf('address_1')] !='') $buyer_address .= $row[csf('address_1')].', ';
        if ($row[csf('address_2')] !='') $buyer_address .= $row[csf('address_2')].', ';
        if ($row[csf('address_3')] !='') $buyer_address .= $row[csf('address_3')].', ';
        if ($row[csf('address_4')] !='') $buyer_address .= $row[csf('address_4')].', ';
        if ($row[csf('country_id')] !='') $buyer_address .= $country_arr[$row[csf('country_id')]];
        if ($row[csf('buyer_email')] !='') $buyer_email = "Email:&nbsp;".$row[csf('buyer_email')];

        $buyer_details_arr[$row[csf('id')]]['buyer_address']=$buyer_address;
        $buyer_details_arr[$row[csf('id')]]['buyer_email']=$buyer_email;
    }

    if ($advising_bank != ""){
    	$sql_bank = sql_select("select a.id, a.bank_name, a.branch_name, a.address, a.swift_code, b.account_type, b.account_no from lib_bank a, lib_bank_account b where a.id=$advising_bank and a.advising_bank=1 and a.is_deleted=0 and a.status_active=1 and b.account_type=10 and b.status_active=1 and b.is_deleted=0 and company_id=$company_id");
	  	foreach($sql_bank as $row)
	  	{
	  		$bank_name=$row[csf('bank_name')];
	  		$branch_name=$row[csf('branch_name')];
	  		$address=$row[csf('address')];
	  		$swift_code=$row[csf('swift_code')];
	  		$account_no=$row[csf('account_no')];
		}
    }
    ?>
    <style type="text/css">
    	table>tr>th,td{word-break: break-all;}
    </style>
	<div style="width:1200px">
		<table width="1200" cellspacing="0" border="0">
	        <tr>
	            <td colspan="2" rowspan="5" width="150" style="vertical-align: top;">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
				</td>
				<td colspan="8" style="font-size:xx-large; justify-content: center;text-align: center;" >
	            	<strong style="justify-content: center; text-align: center;"><? echo $company_arr[$company_id]; ?></strong>
	            </td>
	            <td colspan="3" style="font-size:20px;"><strong>Proforma Invoice</strong></td>          
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $company_address[$company_id]; ?></strong></td>
	            <td colspan="3" style="font-size:16px;">PI Date:&nbsp;<? echo change_date_format($pi_date); ?></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $contact_no; ?></strong></td>
	            <td colspan="3" style="font-size:16px;">PI Revised Date:&nbsp;<? echo change_date_format($pi_revised_date); ?></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $company_email; ?></strong></td>
	            <td colspan="3" style="font-size:16px;"><strong>PI No:&nbsp;<? echo $pi_number; ?></strong></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; ">&nbsp;</strong></td>
	            <td colspan="3" style="font-size:16px;"><strong>BIN No:&nbsp;<? echo $bin_no; ?></strong></td>
	        </tr>	        
	    </table>
	    <br>
		<table class="rpt_table" width="1200" cellspacing="1" rules="all" border="1">
			<tr>
				<td colspan="6" width="520" style="font-size: 18px;"><strong>For Account & Risk Of:</strong></td>
				<td colspan="7" width="480" style="font-size: 18px;"><strong>Beneficiary:</strong></td>
			</tr>
			<tr>
				<td colspan="6"  width="520" style="font-size: 18px;" valign="top"><strong><? if ($within_group==1) echo $company_arr[$buyer_id].'<br>'.$company_address[$buyer_id].'<br>'.$contact_no.'<br>'.$company_email; else if ($within_group==2) echo $buyer_arr[$buyer_id].'<br>'.$buyer_details_arr[$buyer_id]["buyer_address"].'<br>'.$buyer_details_arr[$buyer_id]["buyer_email"]; ?></strong></td>
				<td colspan="7"  width="480" style="font-size: 18px;"><strong><? echo $company_arr[$company_id].'<br>'.$company_address[$company_id].'<br>'.$contact_no.'<br>'.$company_email; ?></strong></td>
			</tr>
			<tr>
				<td colspan="6"  width="520" style="font-size: 18px;" valign="top"><strong>Buyer:&nbsp;<? echo $buyer_names; ?><br>Garments Style:&nbsp;<? if (strlen($style_ref_nos) > 45) echo substr($style_ref_nos, 0, 45).'...'; else echo $style_ref_nos; ?><br>Garmments PO:&nbsp;<? if (strlen($po_numbers) > 45) echo substr($po_numbers, 0, 45).'...'; else echo $po_numbers; ?></strong></td>
				<td colspan="7"  width="480" style="font-size: 18px;"><strong><? echo 'Advising Bank:<br>'.$bank_name.'<br>'.$branch_name.', '.$address.'<br>Account No:&nbsp;'.$account_no.'<br>Swift Code:&nbsp;'.$swift_code; ?></strong></td>
			</tr>
        </table>
		<br>
		<div width="1000" style="font-size: 18px;"><strong><? echo $item_category_name; ?>&nbsp;(H.S. Code - <? echo $hs_code; ?>) charge for 100 % export oriented readymade garments industries  As Follows:</strong></div>
        <table width="1200" cellspacing="0" align="center" border="1" rules="all" class="rpt_table">
            <thead bgcolor="#dddddd" align="center">                
                <th width="50">SL</th>
                <th width="150">Job Order No</th>
                <th width="150">Work Order No</th>
                <th width="100">HS Code</th>
                <th width="100">Section</th>
                <th width="100">Item Group</th>
                <th width="180">Item Description</th>
                <th width="50">UOM</th>
                <th width="80">Quantity</th>
                <th width="80">Rate USD</th>
                <th >Amount in USD</th>
            </thead>
            <tbody>
                <?
                $i=1;
                $tot_wo_qty=$tot_amount=0;
                foreach ($all_data_arr as $row) 
                {
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td ><? echo $i; ?></td>
                        <td ><? echo $row['work_order_no']; ?></td>
                        <td ><? echo $row['booking']; ?></td>
                        <td ><? echo implode(', ',array_unique(explode(',',rtrim($row['hs_code_dtls'],',')))) ; ?></td>
                        <td ><? echo $trims_section[$row['section']]; ?></td>
                        <td ><? echo $item_group_arr[$row['gmts_item_id']]; ?></td>
                        <td ><? echo $row['item_desc']; ?></td>
                        <td align="center"><? echo $unit_of_measurement[$row['uom']]; ?></td>
                        <td align="right"><? echo number_format($row['quantity'],2); ?></td>                        
                        <td align="right"><? echo number_format($row['amount']/$row['quantity'],4); ?></td>
                        <td align="right"><? echo number_format($row['amount'],2); ?></td>
                    </tr>
                    <?
                    $tot_quantity+=$row['quantity'];
                    $tot_amount+=$row['amount'];
                    $i++;
                }
                ?>    
            </tbody>                
            <tfoot>
                <tr bgcolor="#dddddd">
                    <td colspan="10" align="right"><strong>Total</strong></td>
                    <!-- <td align="right"><strong><? echo number_format($tot_quantity,2); ?></strong></td>
                    <td>&nbsp;</td> -->
                    <td align="right"><strong><? echo number_format($tot_amount,2); ?></strong></td>
                </tr>
				<tr bgcolor="#dddddd">
                    <td colspan="10" align="right"><strong>Upcharge</strong></td>
                    <td align="right"><strong><? echo number_format($upcharge,2); ?></strong></td>
                </tr>
                <tr bgcolor="#dddddd">
                    <td colspan="10" align="right"><strong>Discount</strong></td>
                    <td align="right"><strong><? echo number_format($discount,2); ?></strong></td>
                </tr>
                <tr bgcolor="#dddddd">
                    <td colspan="10" align="right"><strong>Net Total</strong></td>
                    <td align="right"><strong><? echo number_format($net_total_amount,2); ?></strong></td>
                </tr>
            </tfoot> 
        </table>
        <table>
            <tr height="20"></tr>
            <tr>
                <td valign="top"><strong>In-Words: </strong></td>
                <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
            </tr>
            <tr> 
            <tr height="50"></tr>
        </table>
        <? 
        	echo get_spacial_instruction($mst_id,"100%",152);
        	//echo signature_table(257, $company_id, "1000px");
        	echo signature_table(257, $company_id, "1000px", "", "70", $user_arr[$user_id]);
        ?>        
	</div>
    <?
	exit();	 
}


if($action=="print_new_rpt_12")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    $company_id=$data[0];
    $mst_id=$data[1];
    $system_id = $data[2];
    $cbo_item_category_id = $data[3];
    $pi_date= '';
    $buyer_id='';
    $within_group='';
    $pi_revised_date='';
    $pi_number='';
    $advising_bank='';
    $hs_code='';
    $item_category_name='';
    $upcharge=0;
    $discount=0;
    $net_total_amount=0;
    $insertUserId = 0;

    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $item_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4 and status_active=1",'id','item_name');
    $user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_id' and is_deleted=0","image_location");
    $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
    $sql_company = sql_select("SELECT * FROM lib_company WHERE is_deleted=0 and status_active=1");
    foreach($sql_company as $row)
    {
        if($row[csf('plot_no')] !='') $plot_no = $row[csf('plot_no')].', ';
        if($row[csf('level_no')] !='') $level_no = $row[csf('level_no')].', ';
        if($row[csf('road_no')] !='') $road_no = $row[csf('road_no')].', ';
        if($row[csf('block_no')] !='') $block_no = $row[csf('block_no')].', ';
        if($row[csf('city')] !='') $city = $row[csf('city')].', ';
        if($row[csf('zip_code')] !='') $zip_code = $row[csf('zip_code')].', ';
        if($row[csf('country_id')] !=0) $country = $country_arr[$row[csf('country_id')]];
        if($row[csf('email')] !='') $company_email = "Email:&nbsp;".$row[csf('email')];
        if($row[csf('contact_no')] !='') $contact_no = "TEL#&nbsp;".$row[csf('contact_no')];
        if($row[csf('bin_no')] !='') $bin_no = $row[csf('bin_no')];

        $company_address[$row[csf('id')]] = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
        $company_arr[$row[csf('id')]]=$row[csf('company_name')];
    }

    $sql ="SELECT a.id as ID, a.pi_date as PI_DATE, a.pi_revised_date as PI_REVISED_DATE, a.pi_number as PI_NUMBER, b.body_part as BODY_PART, b.gsm as GSM, d.grey_dia as DIA_WIDTH, d.print_type as PRINT_TYPE, b.aop_color_id as AOP_COLOR_ID, a.within_group as WITHIN_GROUP, a.buyer_id as BUYER_ID, a.hs_code as HS_CODE, a.advising_bank as ADVISING_BANK, a.item_category_id as ITEM_CATEGORY_ID,a.upcharge as UPCHARGE,a.discount as DISCOUNT,a.net_total_amount as NET_TOTAL_AMOUNT, b.id as dtls_id, b.work_order_no as WORK_ORDER_NO, b.work_order_id as WORK_ORDER_ID, b.booking as BOOKING, c.receive_date as ORDER_RCV_DATE,  c.delivery_date AS DELIVERY_DATE, b.hs_code as HS_CODE_DTLS,b.gmts_item_id as GMTS_ITEM_ID, b.item_desc as ITEM_DESC, b.uom as UOM, b.quantity as QUANTITY, b.amount as AMOUNT, d.section as SECTION, d.construction as CONSTRUCTION, d.composition as COMPOSITION, a.inserted_by as INSERTED_BY from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_dtls d, subcon_ord_mst c  where a.id=b.pi_id and a.id=$mst_id and b.work_order_dtls_id=d.id and d.mst_id = c.id and a.exporter_id=$company_id and a.item_category_id=$cbo_item_category_id and a.status_active=1 and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0";

    $sql_res=sql_select($sql);

    $check_master_arr=array();
    $all_data_arr=array();
    $bookingContainer = array();
    $rcvDate = array();
    $deliveryDate = array();
    foreach ($sql_res as  $val)
    {
        $work_order_id.=$val['WORK_ORDER_ID'].',';
        if ($check_master_arr[$val['ID']]==""){
            $check_master_id[$val['ID']]=$val['ID'];
            $pi_date=$val['PI_DATE'];
            $buyer_id=$val['BUYER_ID'];
            $within_group=$val['WITHIN_GROUP'];
            $pi_revised_date=$val['PI_REVISED_DATE'];
            $pi_number=$val['PI_NUMBER'];
            $advising_bank=$val['ADVISING_BANK'];
            $hs_code=$val['HS_CODE'];
            $item_category_name=$export_item_category[$val['ITEM_CATEGORY_ID']];
            $upcharge=$val['UPCHARGE'];
            $discount=$val['DISCOUNT'];
            $net_total_amount=$val['NET_TOTAL_AMOUNT'];
            $insertUserId = $val['INSERTED_BY'];
        }
        if($val['BOOKING'] != ''){
            array_push($bookingContainer, $val['BOOKING']);
        }
        if($val['ORDER_RCV_DATE'] != ''){
            array_push($rcvDate, change_date_format($val['ORDER_RCV_DATE']));
        }
        if($val['DELIVERY_DATE'] != ''){
            array_push($deliveryDate, change_date_format($val['DELIVERY_DATE']));
        }
        $key=$val['WORK_ORDER_NO'].'**'.$val['GSM'].'**'.$val['DIA_WIDTH'].'**'.$val['CONSTRUCTION'].'**'.$val['PRINT_TYPE'].'**'.$val['BODY_PART'].'**'.$val['AOP_COLOR_ID'].'**'.$val['COMPOSITION'].'**'.$val['UOM'];
        $all_data_arr[$key]['work_order_no']=  $val['WORK_ORDER_NO'];
        $all_data_arr[$key]['gsm'] =  $val['GSM'];
        $all_data_arr[$key]['dia_width']=  $val['DIA_WIDTH'];
        $all_data_arr[$key]['uom']=  $val['UOM'];
        $all_data_arr[$key]['body_part'] =  $val['BODY_PART'];
        $all_data_arr[$key]['print_type'] =  $val['PRINT_TYPE'];
        $all_data_arr[$key]['aopcolor'] =  $val['AOP_COLOR_ID'];
        $all_data_arr[$key]['construction'] =  $val['CONSTRUCTION'].($val['COMPOSITION'] != '' ? ', '.$val['COMPOSITION'] : '');
        $all_data_arr[$key]['quantity']= isset($all_data_arr[$key]['quantity']) ? $all_data_arr[$key]['quantity'] + $val['QUANTITY'] : $val['QUANTITY'];
        $all_data_arr[$key]['amount']= isset($all_data_arr[$key]['amount']) ? $all_data_arr[$key]['amount'] +  $val['AMOUNT'] : $val['AMOUNT'];
    }
    $booking_ids=implode(',',array_unique(explode(',',rtrim($work_order_id,','))));

    if ($booking_ids != "")
    {
        $sql_order="SELECT b.buyer_po_no as BUYER_PO_NO, b.buyer_style_ref as BUYER_STYLE_REF, b.buyer_buyer as BUYER_BUYER  from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($booking_ids)";
        // echo $sql_order;
        $sql_order_res=sql_select($sql_order);
        $booking_no_arr=array();
        foreach ($sql_order_res as $val)
        {
            $po_number.=$val['BUYER_PO_NO'].',';
            $style_ref_no.=$val['BUYER_STYLE_REF'].',';
            if($within_group==1){ $buyer_names.=$buyer_arr[$val['BUYER_BUYER']].','; }
            else{ $buyer_names.=$val['BUYER_BUYER'].','; }
        }
    }
    $po_numbers=implode(', ',array_unique(explode(',',rtrim($po_number,','))));
    $style_ref_nos=implode(', ',array_unique(explode(',',rtrim($style_ref_no,','))));
    $buyer_names=implode(', ',array_unique(explode(',',rtrim($buyer_names,','))));

    $sql_buyer = sql_select("select id, buyer_name, country_id, buyer_email, address_1, address_2, address_3, address_4 from lib_buyer where status_active=1 and is_deleted=0");
	//    $sql_party = sql_select("select buy.id, buy.buyer_name, buy.country_id, buy.buyer_email, buy.address_1, buy.address_2, buy.address_3, buy.address_4 from lib_buyer buy, lib_buyer_tag_company b  where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name");

    foreach ($sql_buyer as $row)
    {
        $buyer_address=$buyer_email="";
        if ($row[csf('address_1')] !='') $buyer_address .= $row[csf('address_1')].', ';
        if ($row[csf('address_2')] !='') $buyer_address .= $row[csf('address_2')].', ';
        if ($row[csf('address_3')] !='') $buyer_address .= $row[csf('address_3')].', ';
        if ($row[csf('address_4')] !='') $buyer_address .= $row[csf('address_4')].', ';
        if ($row[csf('country_id')] !='') $buyer_address .= $country_arr[$row[csf('country_id')]];
        if ($row[csf('buyer_email')] !='') $buyer_email = "Email:&nbsp;".$row[csf('buyer_email')];

        $buyer_details_arr[$row[csf('id')]]['buyer_address']=$buyer_address;
        $buyer_details_arr[$row[csf('id')]]['buyer_email']=$buyer_email;
    }


    if ($advising_bank != ""){
        $sql_bank = sql_select("select a.id, a.bank_name, a.branch_name, a.address, a.swift_code, b.account_type, b.account_no from lib_bank a, lib_bank_account b where a.id=$advising_bank and a.advising_bank=1 and a.is_deleted=0 and a.status_active=1 and b.account_type=10 and b.status_active=1 and b.is_deleted=0 and company_id=$company_id");
        foreach($sql_bank as $row)
        {
            $bank_name=$row[csf('bank_name')];
            $branch_name=$row[csf('branch_name')];
            $address=$row[csf('address')];
            $swift_code=$row[csf('swift_code')];
            $account_no=$row[csf('account_no')];
        }
    }
    ?>
    <style type="text/css">
        table>tr>th,td{word-break: break-all;}
    </style>
    <div style="width:1200px">
        <table width="1200" cellspacing="0" border="0">
            <tr>
                <td colspan="2" rowspan="5" width="150" style="vertical-align: top;">
                    <img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
                </td>
                <td colspan="8" style="font-size:xx-large; justify-content: center;text-align: center;" >
                    <strong style="justify-content: center; text-align: center;"><? echo $company_arr[$company_id]; ?></strong>
                </td>
                <td colspan="3" style="font-size:20px;"><strong>Proforma Invoice</strong></td>
            </tr>
            <tr>
                <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $company_address[$company_id]; ?></strong></td>
                <td colspan="3" style="font-size:16px;">PI Date:&nbsp;<? echo change_date_format($pi_date); ?></td>
            </tr>
            <tr>
                <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $contact_no; ?></strong></td>
                <td colspan="3" style="font-size:16px;">PI Revised Date:&nbsp;<? echo change_date_format($pi_revised_date); ?></td>
            </tr>
            <tr>
                <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $company_email; ?></strong></td>
                <td colspan="3" style="font-size:16px;"><strong>PI No:&nbsp;<? echo $pi_number; ?></strong></td>
            </tr>
            <tr>
                <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; ">&nbsp;</strong></td>
                <td colspan="3" style="font-size:16px;"><strong>BIN No:&nbsp;<? echo $bin_no; ?></strong></td>
            </tr>
        </table>
        <br>
        <table class="rpt_table" width="1200" cellspacing="1" rules="all" border="1">
            <tr>
                <td colspan="6" width="520" style="font-size: 18px;"><strong>For Account & Risk Of:</strong></td>
                <td colspan="7" width="480" style="font-size: 18px;"><strong>Beneficiary:</strong></td>
            </tr>
            <tr>
                <td colspan="6"  width="520" style="font-size: 18px;" valign="top"><strong><? if ($within_group==1) echo $company_arr[$buyer_id].'<br>'.$company_address[$buyer_id].'<br>'.$contact_no.'<br>'.$company_email; else if ($within_group==2) echo $buyer_arr[$buyer_id].'<br>'.$buyer_details_arr[$buyer_id]["buyer_address"].'<br>'.$buyer_details_arr[$buyer_id]["buyer_email"]; ?></strong></td>
                <td colspan="7"  width="480" style="font-size: 18px;"><strong><? echo $company_arr[$company_id].'<br>'.$company_address[$company_id].'<br>'.$contact_no.'<br>'.$company_email; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6"  width="520" style="font-size: 18px;" valign="top"><strong>Buyer:&nbsp;<? echo $buyer_names; ?><br>Garments Style:&nbsp;<? if (strlen($style_ref_nos) > 45) echo substr($style_ref_nos, 0, 45).'...'; else echo $style_ref_nos; ?><br>Garmments PO:&nbsp;<? if (strlen($po_numbers) > 45) echo substr($po_numbers, 0, 45).'...'; else echo $po_numbers; ?></strong></td>
                <td colspan="7"  width="480" style="font-size: 18px;"><strong><? echo 'Advising Bank:<br>'.$bank_name.'<br>'.$branch_name.', '.$address.'<br>Account No:&nbsp;'.$account_no.'<br>Swift Code:&nbsp;'.$swift_code; ?></strong></td>
            </tr>
            <tr>
                <td colspan="3" width="260" style="font-size: 18px;"><strong>Order Number</strong></td>
                <td colspan="3" width="260" style="font-size: 18px;"><strong>Order Date</strong></td>
                <td colspan="7"  width="480" style="font-size: 18px;"><strong>Delivery Date</strong></td>
            </tr>
            <tr>
                <td colspan="3" width="260" style="font-size: 18px;"><strong><?
                        $booking = implode(', ', array_unique($bookingContainer));
                        $receiveDate = implode(', ', array_unique($rcvDate));
                        $deliveryDateStr = implode(', ', array_unique($deliveryDate));
                        echo  $booking;
                        ?></strong></td>
                <td colspan="3" width="260" style="font-size: 18px;"><strong><?=$receiveDate;?></strong></td>
                <td colspan="7"  width="480" style="font-size: 18px;"><strong><?=$deliveryDateStr;?></strong></td>
            </tr>
        </table>
        <br>
        <div width="1000" style="font-size: 18px;"><strong><? echo $item_category_name; ?>&nbsp;(H.S. Code - <? echo $hs_code; ?>) charge for 100 % export oriented readymade garments industries  As Follows:</strong></div>
        <table width="1200" cellspacing="0" align="center" border="1" rules="all" class="rpt_table">
            <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="150">AOP Job No</th>
            <th width="100">Body Part</th>
            <th width="170">Fabric Description</th>
            <th width="70">GSM</th>
            <th width="70">DIA</th>
            <th width="130">AOP Color</th>
            <th width="110">Print Type</th>
            <th width="50">UOM</th>
            <th width="80">Quantity</th>
            <th width="80">Rate USD</th>
            <th >Amount in USD</th>
            </thead>
            <tbody>
            <?
            $i=1;
            $tot_wo_qty=$tot_amount=0;

            foreach ($all_data_arr as $row)
            {
                ?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <td ><? echo $row['work_order_no']; ?></td>
                    <td ><? echo $body_part[$row['body_part']]; ?></td>
                    <td ><? echo $row['construction'] ?></td>
                    <td ><? echo $row['gsm']; ?></td>
                    <td ><? echo $row['dia_width']; ?></td>
                    <td ><? echo $color_library[$row['aopcolor']]; ?></td>
                    <td><? echo $print_type[$row['print_type']]?></td>
                    <td align="center"><? echo $unit_of_measurement[$row['uom']]; ?></td>
                    <td align="right"><? echo number_format($row['quantity'],2); ?></td>
                    <td align="right"><? echo number_format($row['amount']/$row['quantity'],4); ?></td>
                    <td align="right"><? echo number_format($row['amount'],2); ?></td>
                </tr>
                <?
                $tot_quantity+=$row['quantity'];
                $tot_amount+=$row['amount'];
                $i++;
            }
            ?>
            </tbody>
            <tfoot>
            <tr bgcolor="#dddddd">
                <td colspan="11" align="right"><strong>Total</strong></td>
                <!-- <td align="right"><strong><? echo number_format($tot_quantity,2); ?></strong></td>
                    <td>&nbsp;</td> -->
                <td align="right"><strong><? echo number_format($tot_amount,2); ?></strong></td>

            </tr>
            <tr bgcolor="#dddddd">
                <td colspan="11" align="right"><strong>Upcharge</strong></td>
                <td align="right"><strong><? echo number_format($upcharge,2); ?></strong></td>

            </tr>
            <tr bgcolor="#dddddd">
                <td colspan="11" align="right"><strong>Discount</strong></td>
                <td align="right"><strong><? echo number_format($discount,2); ?></strong></td>

            </tr>
            <tr bgcolor="#dddddd">
                <td colspan="11" align="right"><strong>Net Total</strong></td>
                <td align="right"><strong><? echo number_format($net_total_amount,2); ?></strong></td>

            </tr>
            </tfoot>
        </table>
        <table>
            <tr height="20"></tr>
            <tr>
                <td valign="top"><strong>In-Words: </strong></td>
                <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
            </tr>
            <tr>
            <tr height="50"></tr>
        </table>
        <?
        echo get_spacial_instruction($mst_id,"100%",152);
        //echo signature_table(257, $company_id, "1000px");
        echo signature_table(257, $company_id, "1197px", "", 70, $insertUserId);
        ?>
    </div>
    <?
    exit();
}


if($action=="print_new_rpt_13") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_id=$data[0];
	$mst_id=$data[1];
	$system_id = $data[2];
	$cbo_item_category_id = $data[3];

	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$lib_size=return_library_array( "select id, size_name from lib_size where status_active = 1 and is_deleted = 0",'id','size_name');
	$lib_color=return_library_array( "select id, color_name from lib_color where status_active = 1 and is_deleted = 0",'id','color_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4 and status_active=1",'id','item_name');
	$sql_company = sql_select("SELECT * FROM lib_company WHERE is_deleted=0 and status_active=1 and id = $company_id");
    $company_address = [];
    foreach($sql_company as $row)
  	{
		if($row[csf('plot_no')] !=''){
            $plot_no = "Plot No.-".$row[csf('plot_no')].'<br>';
            $plot_no1 = "Plot No.-".$row[csf('plot_no')];
        }
		if($row[csf('level_no')] !='') $level_no = $row[csf('level_no')];
		if($row[csf('road_no')] !='') $road_no = ', '.$row[csf('road_no')];
		if($row[csf('block_no')] !='') $block_no = ', '.$row[csf('block_no')];
		if($row[csf('city')] !='') $city = ', '.$row[csf('city')];
		if($row[csf('zip_code')] !='') $zip_code = ', '.$row[csf('zip_code')];
		if($row[csf('country_id')] !=0) $country = $country_arr[$row[csf('country_id')]];
        if($row[csf('bin_no')] !='') $bin_no = $row[csf('bin_no')];
        if($row[csf('vat_number')] !='') $vat_number = $row[csf('vat_number')];
		if($row[csf('contact_no')] !='') $contact = "<br> Tel: ".$row[csf('contact_no')];
		if($row[csf('trade_license_no')] !='') $trade_license = "<br> Registered in Bangladesh No- ".$row[csf('trade_license_no')];

        $company_address[$row[csf('id')]] = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code;
		$company_address_footer[$row[csf('id')]] = $plot_no1.$city.$contact.$trade_license;
		$company_arr[$row[csf('id')]]=$row[csf('company_name')];
	}
    $group_address = [];
    $sql_group = sql_select("SELECT a.contact_no, a.website, a.address, a.email, a.country_id, b.id FROM lib_group a, lib_company b WHERE b.group_id = a.id and a.is_deleted=0 and a.status_active=1 and b.id = $company_id");
    foreach($sql_group as $row)
    {
        if($row[csf('address')] !='') $address = $row[csf('address')].'<br>';
//        if($row[csf('country_id')] !=0) $country = $country_arr[$row[csf('country_id')]];
        if($row[csf('contact_no')] !='') $contact = "Tel: ".$row[csf('contact_no')].', ';
        if($row[csf('email')] !='') $email = "Email: ".$row[csf('email')];
        if($row[csf('website')] !='') $website = "<br>Web: ".$row[csf('website')];
        $group_address[$row[csf('id')]] = $address.$contact.$email.$website;
    }


	$sql ="SELECT a.id as ID, a.pi_date as PI_DATE, a.pi_validity_date as PI_VALIDITY_DATE, a.pi_revised_date as PI_REVISED_DATE, a.pi_number as PI_NUMBER, a.within_group as WITHIN_GROUP, a.buyer_id as BUYER_ID, a.hs_code as HS_CODE, a.advising_bank as ADVISING_BANK, a.item_category_id as ITEM_CATEGORY_ID,a.upcharge as UPCHARGE,a.discount as DISCOUNT,a.net_total_amount as NET_TOTAL_AMOUNT, b.id as dtls_id, b.work_order_no as WORK_ORDER_NO, b.work_order_id as WORK_ORDER_ID, b.booking as BOOKING, b.hs_code as HS_CODE_DTLS,b.gmts_item_id as GMTS_ITEM_ID, b.item_desc as ITEM_DESC, b.uom as UOM, b.quantity as QUANTITY, b.amount as AMOUNT, c.size_id as SIZE_ID, c.color_id as COLOR_ID, c.size_name as SIZE_NAME, d.section as SECTION, d.buyer_style_ref as BUYER_STYLE_REF, d.buyer_buyer as BUYER_BUYER from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c,subcon_ord_dtls d where a.id=b.pi_id and a.id=$mst_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and a.exporter_id=$company_id and a.item_category_id=$cbo_item_category_id and a.status_active=1 and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0";
	// echo $sql;

	$sql_res=sql_select($sql);
	$check_master_arr=array();
	$all_data_arr=array(); $temp_group = array(); $temp_buyer = array(); $temp_desc = array(); $temp_booking = array(); $temp_workorder = array(); $temp_style = array(); $temp_hscode = array();
	foreach ($sql_res as $sql_key => $val)
	{
		$work_order_id.=$val['WORK_ORDER_ID'].',';
		if ($check_master_arr[$val['ID']]==""){
			$check_master_id[$val['ID']]=$val['ID'];
			$pi_date=$val['PI_DATE'];
			$pi_validity_date=$val['PI_VALIDITY_DATE'];
			$buyer_id=$val['BUYER_ID'];
			$within_group=$val['WITHIN_GROUP'];
			$pi_revised_date=$val['PI_REVISED_DATE'];	
			$pi_number=$val['PI_NUMBER'];
			$advising_bank=$val['ADVISING_BANK'];
			$hs_code=$val['HS_CODE'];
			$item_category_name=$export_item_category[$val['ITEM_CATEGORY_ID']];
			$upcharge=$val['UPCHARGE'];
			$discount=$val['DISCOUNT'];
			$net_total_amount=$val['NET_TOTAL_AMOUNT'];
		}
		$key=$val['WORK_ORDER_NO'].'**'.$val['BOOKING'].'**'.$val['BUYER_BUYER'].'**'.$val['BUYER_STYLE_REF'].'**'.$val['HS_CODE_DTLS'].'**'.$val['GMTS_ITEM_ID'].'**'.$val['COLOR_ID'].'**'.$val['SIZE_ID'].'**'.$val['SIZE_NAME'].'**'.$val['ITEM_DESC'].'**'.$val['UOM'];
		$color_size_key = $val['COLOR_ID'].'**'.$val['SIZE_ID'].'**'.$val['SIZE_NAME'];
        $all_data_arr[$key]['work_order_no']=$val['WORK_ORDER_NO'];
		$all_data_arr[$key]['booking']=$val['BOOKING'];
		$all_data_arr[$key]['buyer_buyer']=$val['BUYER_BUYER'];
		$all_data_arr[$key]['buyer_style_ref']=$val['BUYER_STYLE_REF'];
		$all_data_arr[$key]['hs_code_dtls']=$val['HS_CODE_DTLS'];
		$all_data_arr[$key]['section']=$val['SECTION'];
		$all_data_arr[$key]['gmts_item_id']=$val['GMTS_ITEM_ID'];
		$all_data_arr[$key]['item_desc']=$val['ITEM_DESC'];
		$all_data_arr[$key]['uom']=$val['UOM'];
		$all_data_arr[$key]['quantity']+=$val['QUANTITY'];
		$all_data_arr[$key]['amount']+=$val['AMOUNT'];
		$all_data_arr[$key]['color_size_key']=$color_size_key;
        if($val['SIZE_ID'] > 0) {
            $all_data_arr[$key]['SIZE_NAME'] = $lib_size[$val['SIZE_ID']];
        }else {
            $all_data_arr[$key]['SIZE_NAME'] = $val['SIZE_NAME'];
        }
		$all_data_arr[$key]['COLOR']=$lib_color[$val['COLOR_ID']];
        $temp_group[$key] =  $val['GMTS_ITEM_ID'];
        $temp_buyer[$key] =  $val['BUYER_BUYER'];
        $temp_workorder[$key] =  $val['WORK_ORDER_NO'];
        $temp_booking[$key] =  $val['BOOKING'];
        $temp_style[$key] =  $val['BUYER_STYLE_REF'];
        $temp_hscode[$key] =  $val['HS_CODE_DTLS'];
        $temp_desc[$key] =  $val['ITEM_DESC'];
	}
    function arraySeqCount($arr)
    {
        $result = [];
        $carry = [array_shift($arr) => 1];
        $c = 0;
        foreach ($arr as $key => $value) {
            if (isset($carry[$value])) {
                ++$carry[$value];
            } else {
                $result[] = $carry;
                $carry = [$value => 1];
            }
        }
            $result[] = $carry;

        return $result;
    }
    $buyerRowspan = arraySeqCount($temp_buyer);
    $groupRowspan = arraySeqCount($temp_group);
    $descRowspan = arraySeqCount($temp_desc);
    $styleRowspan = arraySeqCount($temp_style);
    $hscodeRowspan = arraySeqCount($temp_hscode);
    $bookingRowspan = arraySeqCount($temp_booking);
    $workorderRowspan = arraySeqCount($temp_workorder);
    $tempArrBuyer = array();
    $flag = true;
    $counter = 0;
    foreach($temp_buyer as $key => $value){
        if($flag){
            $tempArrBuyer[$key] = $buyerRowspan[$counter][$value];
            unset($buyerRowspan[$counter]);
            $counter++;
        }else{
            $tempArrBuyer[$key] = 0;
        }
        if(current($temp_buyer) != next($temp_buyer))
            $flag = true;
        else
            $flag = false;
    }
    $tempArrGroup = array();
    $flag = true;
    $counter = 0;
    foreach($temp_group as $key => $value){
        if($flag){
            $tempArrGroup[$key] = $groupRowspan[$counter][$value];
            unset($groupRowspan[$counter]);
            $counter++;
        }else{
            $tempArrGroup[$key] = 0;
        }
        if(current($temp_group) != next($temp_group))
            $flag = true;
        else
            $flag = false;
    }
    $tempArrDesc = array();
    $flag = true;
    $counter = 0;
    foreach($temp_desc as $key => $value){
        if($flag){
            $tempArrDesc[$key] = $descRowspan[$counter][$value];
            unset($descRowspan[$counter]);
            $counter++;
        }else{
            $tempArrDesc[$key] = 0;
        }
        if(current($temp_desc) != next($temp_desc))
            $flag = true;
        else
            $flag = false;
    }
    $tempArrStyle = array();
    $flag = true;
    $counter = 0;
    foreach($temp_style as $key => $value){
        if($flag){
            $tempArrStyle[$key] = $styleRowspan[$counter][$value];
            unset($styleRowspan[$counter]);
            $counter++;
        }else{
            $tempArrStyle[$key] = 0;
        }
        if(current($temp_style) != next($temp_style))
            $flag = true;
        else
            $flag = false;
    }
    $tempArrHscode = array();
    $flag = true;
    $counter = 0;
    foreach($temp_hscode as $key => $value){
        if($flag){
            $tempArrHscode[$key] = $hscodeRowspan[$counter][$value];
            unset($hscodeRowspan[$counter]);
            $counter++;
        }else{
            $tempArrHscode[$key] = 0;
        }
        if(current($temp_hscode) != next($temp_hscode))
            $flag = true;
        else
            $flag = false;
    }
    $tempArrBooking = array();
    $flag = true;
    $counter = 0;
    foreach($temp_booking as $key => $value){
        if($flag){
            $tempArrBooking[$key] = $bookingRowspan[$counter][$value];
            unset($bookingRowspan[$counter]);
            $counter++;
        }else{
            $tempArrBooking[$key] = 0;
        }
        if(current($temp_booking) != next($temp_booking))
            $flag = true;
        else
            $flag = false;
    }
    $tempArrWorkorder = array();
    $flag = true;
    $counter = 0;
    foreach($temp_workorder as $key => $value){
        if($flag){
            $tempArrWorkorder[$key] = $workorderRowspan[$counter][$value];
            unset($workorderRowspan[$counter]);
            $counter++;
        }else{
            $tempArrWorkorder[$key] = 0;
        }
        if(current($temp_workorder) != next($temp_workorder))
            $flag = true;
        else
            $flag = false;
    }

    $booking_ids=implode(',',array_unique(explode(',',rtrim($work_order_id,','))));
	
	if ($booking_ids != "")
	{
		$sql_order="SELECT b.buyer_po_no as BUYER_PO_NO, b.buyer_style_ref as BUYER_STYLE_REF, b.buyer_buyer as BUYER_BUYER  from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($booking_ids)";
		// echo $sql_order;
		$sql_order_res=sql_select($sql_order);
		$booking_no_arr=array();
		foreach ($sql_order_res as $val)
		{
			$po_number.=$val['BUYER_PO_NO'].',';
			$style_ref_no.=$val['BUYER_STYLE_REF'].',';
			if($within_group==1){ $buyer_names.=$buyer_arr[$val['BUYER_BUYER']].','; }
			else{ $buyer_names.=$val['BUYER_BUYER'].','; }
		}
	}
	$po_numbers=implode(', ',array_unique(explode(',',rtrim($po_number,','))));
	$style_ref_nos=implode(', ',array_unique(explode(',',rtrim($style_ref_no,','))));
	$buyer_names=implode(', ',array_unique(explode(',',rtrim($buyer_names,','))));

	$sql_buyer = sql_select("select id, buyer_name, country_id, buyer_email, address_1, address_2, address_3, address_4 from lib_buyer where status_active=1 and is_deleted=0");    
    foreach ($sql_buyer as $row) 
    {
    	$buyer_address=$buyer_email="";
        if ($row[csf('address_1')] !='') $buyer_address .= $row[csf('address_1')].', ';
        if ($row[csf('address_2')] !='') $buyer_address .= $row[csf('address_2')].', ';
        if ($row[csf('address_3')] !='') $buyer_address .= $row[csf('address_3')].', ';
        if ($row[csf('address_4')] !='') $buyer_address .= $row[csf('address_4')].', ';
        if ($row[csf('country_id')] !='') $buyer_address .= $country_arr[$row[csf('country_id')]];
        if ($row[csf('buyer_email')] !='') $buyer_email = "Email:&nbsp;".$row[csf('buyer_email')];

        $buyer_details_arr[$row[csf('id')]]['buyer_address']=$buyer_address;
        $buyer_details_arr[$row[csf('id')]]['buyer_email']=$buyer_email;
    }

    if ($advising_bank != ""){
    	$sql_bank = sql_select("select a.id, a.bank_name, a.branch_name, a.address, a.swift_code, b.account_type, b.account_no from lib_bank a, lib_bank_account b where a.id=$advising_bank and a.advising_bank=1 and a.is_deleted=0 and a.status_active=1 and b.account_type=10 and b.status_active=1 and b.is_deleted=0 and company_id=$company_id");
	  	foreach($sql_bank as $row)
	  	{
	  		$bank_name=$row[csf('bank_name')];
	  		$branch_name=$row[csf('branch_name')];
	  		$address=$row[csf('address')];
	  		$swift_code=$row[csf('swift_code')];
	  		$account_no=$row[csf('account_no')];
		}
    }

    $tbl_width=1400;
    ?>
    <style type="text/css">
    	table>tr>th,td{word-break: break-all;}
    </style>
	<div style="width:<?=$tbl_width;?>px">
		<table width="<?=$tbl_width;?>" cellspacing="0" border="0">
	        <tr>
				<td colspan="14" style="font-size:38px" >
	            	<strong><? echo $company_arr[$company_id]; ?></strong>
	            </td>         
	        </tr>	        
	    </table>
	    <br>
		<table class="rpt_table" width="<?=$tbl_width;?>" cellspacing="1" rules="all" border="1">
			<tr>
				<td colspan="14" style="text-align: center;font-size:24px;"><strong>PROFORMA INVOICE</strong></td>
			</tr>
			<tr>
				<td colspan="7" width="50%" style="font-size: 18px;"><strong>PROFORMA INVOICE:</strong>&nbsp;<? echo $pi_number; ?></td>
				<td colspan="7" style="font-size: 18px;"><strong>DATE:</strong>&nbsp;<? echo change_date_format($pi_date); ?></td>
			</tr>
			<tr>
				<td colspan="7" style="font-size: 18px;" valign="top">
					<strong>CONSIGNOR/BENEFICARY:</strong><br>
					<strong><? echo $company_arr[$company_id]; ?></strong><br>
					<? echo $company_address[$company_id]; ?><br>
					<strong>Vat NO:</strong>&nbsp;<? echo $vat_number; ?>&nbsp;<strong>BIN NO:</strong>&nbsp;<? echo $bin_no; ?>
				</td>
				<td colspan="7" style="font-size: 18px;" valign="top">
					<strong>Advising Bank:</strong>&nbsp;<br>
					<? echo $bank_name; ?><br>
					<? echo $branch_name; ?><br>
					<? echo $address; ?><br>
					<strong>SWIFT: </strong>&nbsp;<? echo $swift_code; ?>
				</td>
			</tr>
			<tr>
				<td colspan="7" style="font-size: 18px;" valign="top">
					<strong>BUYER/IMPORTER/BILL TO: </strong><br>
					<? 
						if ($within_group==1) 
						{echo $company_arr[$buyer_id].'<br>'.$company_address[$buyer_id]; }
						else if ($within_group==2)
						{ echo $buyer_arr[$buyer_id].'<br>'.$buyer_details_arr[$buyer_id]["buyer_address"]; }
					?>
				</td>
				<td colspan="7" style="font-size: 18px;">
					<strong>DELIVERY TO/CONSIGNEE: </strong><br>
					<? 
						if ($within_group==1) 
						{echo $company_arr[$buyer_id].'<br>'.$company_address[$buyer_id]; }
						else if ($within_group==2)
						{ echo $buyer_arr[$buyer_id].'<br>'.$buyer_details_arr[$buyer_id]["buyer_address"]; }
					?>
				</td>
			</tr>
            <tr>
                <td colspan="7" width="50%" style="font-size: 18px;"><strong>POST OF LOADING: </strong>BENEFICARY FACTORY, DEPZ.</td>
                <td colspan="7" width="50%" style="font-size: 18px;"><strong>VALIDITY: </strong>
                    <?
                    $str_pi_date = strtotime($pi_date);
                    $str_pi_validity_date = strtotime($pi_validity_date);
                    if($str_pi_date > $str_pi_validity_date) {
                        echo 0;
                    }else{
                        $date1=date_create(date('Y-m-d', $str_pi_date));
                        $date2=date_create(date('Y-m-d', $str_pi_validity_date));
                        $diff=date_diff($date1,$date2);
                        echo $diff->format("%a");
                    }
                    ?> DAYS FROM THE DATE OF PI.</td>
            </tr>
            <tr>
                <td colspan="7" width="50%" style="font-size: 18px;"><strong>POST OF LOADING: </strong>APPLICANT WAREHOUSE.</td>
                <td colspan="7" width="50%" style="font-size: 18px;"><strong>MODE OF SHIPMENT: </strong>BY ROAD.</td>
            </tr>
        </table>
		<br>
        <table width="<?=$tbl_width;?>" cellspacing="0" align="center" border="1" rules="all" class="rpt_table">
            <thead bgcolor="#dddddd" align="center">                
                <th width="30">SL No</th>
                <th width="80">Item Group</th>
                <th width="180">Item Description</th>
                <th width="80">Buyer's Buyer</th>
                <th width="100">Buyer's Style</th>
                <th width="80">HS Code</th>
                <th width="135">Job Order No</th>
                <th width="135">Work Order No</th>
                <th width="85">Item Color</th>
                <th width="170">Size</th>
                <th width="75">Qty</th>
				<th width="45">UOM</th>
                <th width="75">Price/USD</th>
                <th >Amount USD</th>
            </thead>
            <tbody>
                <?
                $i=1;
                $tot_wo_qty=$tot_amount=0;
                foreach ($all_data_arr as $key=>$row) 
                {
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <?
                        if($tempArrGroup[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrGroup[$key]?>" style="word-break: break-word;"><? echo $item_group_arr[$row['gmts_item_id']]; ?></td>
                        <?
                        }
                        if($tempArrDesc[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrDesc[$key]?>" style="word-break: break-word;"><? echo $row['item_desc']; ?></td>
                        <?
                        }
                        if($tempArrBuyer[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrBuyer[$key]?>" align="center" style="word-break: break-word;">
                            <?
                            if($within_group==1)
                                $buyer_names= $buyer_arr[$val['BUYER_BUYER']];
                            else
                                $buyer_names = $val['BUYER_BUYER'];
                            echo $buyer_names;
                            ?>
                        </td>
                        <?
                        }
                        if($tempArrStyle[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrStyle[$key]?>" style="word-break: break-word;"><? echo $row['buyer_style_ref']; ?></td>
                        <?
                        }
                        if($tempArrHscode[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrHscode[$key]?>" align="center" style="word-break: break-word;"><? echo $row['hs_code_dtls']; ?></td>
                        <?
                        }
                        if($tempArrWorkorder[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrWorkorder[$key]?>" align="center"><? echo $row['work_order_no']; ?></td>
                        <?
                        }
                        if($tempArrBooking[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrBooking[$key]?>" align="center"><? echo $row['booking']; ?></td>
                        <?
                        }
                        ?>
                        <td style="word-break: break-word;"><? echo $row['COLOR']; ?></td>
                        <td align="center" style="font-size: 15px; word-break: break-word;"><? echo $row['SIZE_NAME']; ?></td>
						<td align="right"><? echo number_format($row['quantity'],2); ?></td>
                        <td align="center"><? echo $unit_of_measurement[$row['uom']]; ?></td>
                        <td align="right"><? echo number_format($row['amount']/$row['quantity'],4); ?></td>
                        <td align="right"><? echo number_format($row['amount'],2); ?></td>
                    </tr>
                    <?
                    $tot_quantity+=$row['quantity'];
                    $tot_amount+=$row['amount'];
                    $i++;
                }
                ?>    
            </tbody>                
            <tfoot>
                <tr bgcolor="#dddddd">
                    <td colspan="9" align="right"></td>
                    <td align="right"><strong>Total</strong></td>
                    <td align="right"><strong><? echo number_format($tot_quantity,2); ?></strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><strong><? echo number_format($tot_amount,2); ?></strong></td>
                </tr>
            </tfoot> 
        </table>
        <table>
            <tr height="20"></tr>
            <tr>
                <td valign="top"><strong>In-Words: </strong></td>
                <td><? echo number_to_words(number_format($tot_amount,2),'USD','Cent');?></td>
            </tr>
            <tr> 
            <tr height="20"></tr>
        </table>
        <? 
        	echo get_spacial_instruction($mst_id,"100%",152);
        ?>
        <div style="margin-top: 50px;margin-left: 100px;">
            <table>
                <tr>
                    <td><strong>BUYER'S ACCEPTANCE</strong></td>
                </tr>
            </table>
        </div>
        <?
        	echo signature_table(257, $company_id, $tbl_width, "", "90",$user_id);
        ?>
	</div>

    <div style="margin-top: 10px; border-top: 2px solid black; width: <?=$tbl_width;?>">
        <table width="<?=$tbl_width;?>" border="0">
            <tr>
                <td width="30"></td>
                <td width="80"></td>
                <td width="180"></td>
                <td width="80"></td>
                <td width="100"></td>
                <td width="80"></td>
                <td width="135"></td>
                <td width="135"></td>
                <td width="85"></td>
                <td width="170"></td>
                <td width="75"></td>
                <td width="45"></td>
                <td width="75"></td>
                <td ></td>
            </tr>
            <tr>
                <td colspan="7" valign="top" style="padding-left: 10px; word-break: break-word; font-size: 16px;">
                    <div style="float: left; width: 430px;">
                        <strong>Head Office Address:</strong><br>
                        <?=$group_address[$company_id]?>
                    </div>
                </td>
                <td colspan="7" valign="top">
                    <div style="float: right; width: 430px; padding-right: 10px; word-break: break-word;font-size: 16px;">
                        <strong>Factory Address:</strong><br>
                        <?=$company_address_footer[$company_id]?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <?
	exit();	 
}

if($action=="print_new_rpt_14")
{
    extract($_REQUEST);
    $data=explode('*',$data);
    $company_id=$data[0];
    $mst_id=$data[1];
    $system_id = $data[2];
    $cbo_item_category_id = $data[3];

    $country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $lib_size=return_library_array( "select id, size_name from lib_size where status_active = 1 and is_deleted = 0",'id','size_name');
    $lib_color=return_library_array( "select id, color_name from lib_color where status_active = 1 and is_deleted = 0",'id','color_name');
    $item_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4 and status_active=1",'id','item_name');
    $sql_company = sql_select("SELECT * FROM lib_company WHERE is_deleted=0 and status_active=1 and id = $company_id");
    $company_address = [];
    foreach($sql_company as $row)
    {
        if($row[csf('plot_no')] !=''){
            $plot_no = "Plot No.-".$row[csf('plot_no')].'<br>';
            $plot_no1 = "Plot No.-".$row[csf('plot_no')];
        }
        if($row[csf('level_no')] !='') $level_no = $row[csf('level_no')];
        if($row[csf('road_no')] !='') $road_no = ', '.$row[csf('road_no')];
        if($row[csf('block_no')] !='') $block_no = ', '.$row[csf('block_no')];
        if($row[csf('city')] !='') $city = ', '.$row[csf('city')];
        if($row[csf('zip_code')] !='') $zip_code = ', '.$row[csf('zip_code')];
        if($row[csf('country_id')] !=0) $country = $country_arr[$row[csf('country_id')]];
        if($row[csf('bin_no')] !='') $bin_no = $row[csf('bin_no')];
        if($row[csf('vat_number')] !='') $vat_number = $row[csf('vat_number')];
        if($row[csf('contact_no')] !='') $contact = "<br> Tel: ".$row[csf('contact_no')];
        if($row[csf('trade_license_no')] !='') $trade_license = "<br> Registered in Bangladesh No- ".$row[csf('trade_license_no')];

        $company_address[$row[csf('id')]] = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code;
        $company_address_footer[$row[csf('id')]] = $plot_no1.$city.$contact.$trade_license;
        $company_arr[$row[csf('id')]]=$row[csf('company_name')];
    }
    $group_address = [];
    $sql_group = sql_select("SELECT a.contact_no, a.website, a.address, a.email, a.country_id, b.id FROM lib_group a, lib_company b WHERE b.group_id = a.id and a.is_deleted=0 and a.status_active=1 and b.id = $company_id");
    foreach($sql_group as $row)
    {
        if($row[csf('address')] !='') $address = $row[csf('address')].'<br>';
//        if($row[csf('country_id')] !=0) $country = $country_arr[$row[csf('country_id')]];
        if($row[csf('contact_no')] !='') $contact = "Tel: ".$row[csf('contact_no')].', ';
        if($row[csf('email')] !='') $email = "Email: ".$row[csf('email')];
        if($row[csf('website')] !='') $website = "<br>Web: ".$row[csf('website')];
        $group_address[$row[csf('id')]] = $address.$contact.$email.$website;
    }

    $sql ="SELECT a.id as ID, a.pi_date as PI_DATE, a.pi_validity_date as PI_VALIDITY_DATE, a.pi_revised_date as PI_REVISED_DATE, a.pi_number as PI_NUMBER, a.within_group as WITHIN_GROUP, a.buyer_id as BUYER_ID, a.hs_code as HS_CODE, a.advising_bank as ADVISING_BANK, a.item_category_id as ITEM_CATEGORY_ID,a.upcharge as UPCHARGE,a.discount as DISCOUNT,a.net_total_amount as NET_TOTAL_AMOUNT, b.id as dtls_id, b.work_order_no as WORK_ORDER_NO, b.work_order_id as WORK_ORDER_ID, b.booking as BOOKING, b.hs_code as HS_CODE_DTLS,b.gmts_item_id as GMTS_ITEM_ID, b.item_desc as ITEM_DESC, b.uom as UOM, b.quantity as QUANTITY, b.amount as AMOUNT, c.size_id as SIZE_ID, c.color_id as COLOR_ID, c.size_name as SIZE_NAME, c.ply as PLY, e.length as LENGTH, e.width as WIDTH, e.height as HEIGHT, e.flap as FLAP, e.gusset as GUSSET, e.thickness as TICKNESS, e.measurementid as MEASUREMENTID, d.section as SECTION, d.buyer_style_ref as BUYER_STYLE_REF, d.buyer_buyer as BUYER_BUYER from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c left join subcon_ord_breakdown_size_info e on e.subconordbreakdownid  = c.id,subcon_ord_dtls d where a.id=b.pi_id and a.id=$mst_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and a.exporter_id=$company_id and a.item_category_id=$cbo_item_category_id and a.status_active=1 and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0 order by c.id asc ";
    // echo $sql;

    $sql_res=sql_select($sql);
    $check_master_arr=array();
    $all_data_arr=array(); $temp_group = array(); $temp_buyer = array(); $temp_desc = array(); $temp_booking = array(); $temp_workorder = array(); $temp_style = array(); $temp_hscode = array();
    foreach ($sql_res as $sql_key => $val)
    {
        $work_order_id.=$val['WORK_ORDER_ID'].',';
        if ($check_master_arr[$val['ID']]==""){
            $check_master_id[$val['ID']]=$val['ID'];
            $pi_date=$val['PI_DATE'];
            $pi_validity_date=$val['PI_VALIDITY_DATE'];
            $buyer_id=$val['BUYER_ID'];
            $within_group=$val['WITHIN_GROUP'];
            $pi_revised_date=$val['PI_REVISED_DATE'];
            $pi_number=$val['PI_NUMBER'];
            $advising_bank=$val['ADVISING_BANK'];
            $hs_code=$val['HS_CODE'];
            $item_category_name=$export_item_category[$val['ITEM_CATEGORY_ID']];
            $upcharge=$val['UPCHARGE'];
            $discount=$val['DISCOUNT'];
            $net_total_amount=$val['NET_TOTAL_AMOUNT'];
        }
        $key=$val['WORK_ORDER_NO'].'**'.$val['BOOKING'].'**'.$val['BUYER_BUYER'].'**'.$val['BUYER_STYLE_REF'].'**'.$val['HS_CODE_DTLS'].'**'.$val['GMTS_ITEM_ID'].'**'.$val['PLY'].'**'.$val['SIZE_ID'].'**'.$val['SIZE_NAME'].'**'.$val['ITEM_DESC'].'**'.$val['UOM'];
        $color_size_key = $val['COLOR_ID'].'**'.$val['SIZE_ID'].'**'.$val['SIZE_NAME'];
        $all_data_arr[$key]['work_order_no']=$val['WORK_ORDER_NO'];
        $all_data_arr[$key]['booking']=$val['BOOKING'];
        $all_data_arr[$key]['buyer_buyer']=$val['BUYER_BUYER'];
        $all_data_arr[$key]['buyer_style_ref']=$val['BUYER_STYLE_REF'];
        $all_data_arr[$key]['hs_code_dtls']=$val['HS_CODE_DTLS'];
        $all_data_arr[$key]['section']=$val['SECTION'];
        $all_data_arr[$key]['gmts_item_id']=$val['GMTS_ITEM_ID'];
        $all_data_arr[$key]['item_desc']=$val['ITEM_DESC'];
        $all_data_arr[$key]['uom']=$val['UOM'];
        $all_data_arr[$key]['quantity']+=$val['QUANTITY'];
        $all_data_arr[$key]['amount']+=$val['AMOUNT'];
        $all_data_arr[$key]['color_size_key']=$color_size_key;
        if($val['SIZE_ID'] > 0) {
            $all_data_arr[$key]['SIZE_NAME'] = $lib_size[$val['SIZE_ID']];
        }else {
            $all_data_arr[$key]['SIZE_NAME'] = $val['SIZE_NAME'];
        }
        $all_data_arr[$key]['PLY']=$val['PLY'];
        $all_data_arr[$key]['LENGTH']=$val['LENGTH'];
        $all_data_arr[$key]['WIDTH']=$val['WIDTH'];
        $all_data_arr[$key]['HEIGHT']=$val['HEIGHT'];
        $all_data_arr[$key]['FLAP']=$val['FLAP'];
        $all_data_arr[$key]['GUSSET']=$val['GUSSET'];
        $all_data_arr[$key]['THICKNESS']=$val['THICKNESS'];
        $all_data_arr[$key]['MEASUREMENTID']=$val['MEASUREMENTID'];
        $temp_group[$key] =  $val['GMTS_ITEM_ID'];
        $temp_buyer[$key] =  $val['BUYER_BUYER'];
        $temp_workorder[$key] =  $val['WORK_ORDER_NO'];
        $temp_booking[$key] =  $val['BOOKING'];
        $temp_style[$key] =  $val['BUYER_STYLE_REF'];
        $temp_hscode[$key] =  $val['HS_CODE_DTLS'];
        $temp_desc[$key] =  $val['ITEM_DESC'];
    }
    function arraySeqCount($arr)
    {
        $result = [];
        $carry = [array_shift($arr) => 1];
        $c = 0;
        foreach ($arr as $key => $value) {
            if (isset($carry[$value])) {
                ++$carry[$value];
            } else {
                $result[] = $carry;
                $carry = [$value => 1];
            }
        }
        $result[] = $carry;

        return $result;
    }
    $buyerRowspan = arraySeqCount($temp_buyer);
    $groupRowspan = arraySeqCount($temp_group);
    $descRowspan = arraySeqCount($temp_desc);
    $styleRowspan = arraySeqCount($temp_style);
    $hscodeRowspan = arraySeqCount($temp_hscode);
    $bookingRowspan = arraySeqCount($temp_booking);
    $workorderRowspan = arraySeqCount($temp_workorder);
    $tempArrBuyer = array();
    $flag = true;
    $counter = 0;
    foreach($temp_buyer as $key => $value){
        if($flag){
            $tempArrBuyer[$key] = $buyerRowspan[$counter][$value];
            unset($buyerRowspan[$counter]);
            $counter++;
        }else{
            $tempArrBuyer[$key] = 0;
        }
        if(current($temp_buyer) != next($temp_buyer))
            $flag = true;
        else
            $flag = false;
    }
    $tempArrGroup = array();
    $flag = true;
    $counter = 0;
    foreach($temp_group as $key => $value){
        if($flag){
            $tempArrGroup[$key] = $groupRowspan[$counter][$value];
            unset($groupRowspan[$counter]);
            $counter++;
        }else{
            $tempArrGroup[$key] = 0;
        }
        if(current($temp_group) != next($temp_group))
            $flag = true;
        else
            $flag = false;
    }
    $tempArrDesc = array();
    $flag = true;
    $counter = 0;
    foreach($temp_desc as $key => $value){
        if($flag){
            $tempArrDesc[$key] = $descRowspan[$counter][$value];
            unset($descRowspan[$counter]);
            $counter++;
        }else{
            $tempArrDesc[$key] = 0;
        }
        if(current($temp_desc) != next($temp_desc))
            $flag = true;
        else
            $flag = false;
    }
    $tempArrStyle = array();
    $flag = true;
    $counter = 0;
    foreach($temp_style as $key => $value){
        if($flag){
            $tempArrStyle[$key] = $styleRowspan[$counter][$value];
            unset($styleRowspan[$counter]);
            $counter++;
        }else{
            $tempArrStyle[$key] = 0;
        }
        if(current($temp_style) != next($temp_style))
            $flag = true;
        else
            $flag = false;
    }
    $tempArrHscode = array();
    $flag = true;
    $counter = 0;
    foreach($temp_hscode as $key => $value){
        if($flag){
            $tempArrHscode[$key] = $hscodeRowspan[$counter][$value];
            unset($hscodeRowspan[$counter]);
            $counter++;
        }else{
            $tempArrHscode[$key] = 0;
        }
        if(current($temp_hscode) != next($temp_hscode))
            $flag = true;
        else
            $flag = false;
    }
    $tempArrBooking = array();
    $flag = true;
    $counter = 0;
    foreach($temp_booking as $key => $value){
        if($flag){
            $tempArrBooking[$key] = $bookingRowspan[$counter][$value];
            unset($bookingRowspan[$counter]);
            $counter++;
        }else{
            $tempArrBooking[$key] = 0;
        }
        if(current($temp_booking) != next($temp_booking))
            $flag = true;
        else
            $flag = false;
    }
    $tempArrWorkorder = array();
    $flag = true;
    $counter = 0;
    foreach($temp_workorder as $key => $value){
        if($flag){
            $tempArrWorkorder[$key] = $workorderRowspan[$counter][$value];
            unset($workorderRowspan[$counter]);
            $counter++;
        }else{
            $tempArrWorkorder[$key] = 0;
        }
        if(current($temp_workorder) != next($temp_workorder))
            $flag = true;
        else
            $flag = false;
    }

    $booking_ids=implode(',',array_unique(explode(',',rtrim($work_order_id,','))));

    if ($booking_ids != "")
    {
        $sql_order="SELECT b.buyer_po_no as BUYER_PO_NO, b.buyer_style_ref as BUYER_STYLE_REF, b.buyer_buyer as BUYER_BUYER  from subcon_ord_mst a, subcon_ord_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($booking_ids)";
        // echo $sql_order;
        $sql_order_res=sql_select($sql_order);
        $booking_no_arr=array();
        foreach ($sql_order_res as $val)
        {
            $po_number.=$val['BUYER_PO_NO'].',';
            $style_ref_no.=$val['BUYER_STYLE_REF'].',';
            if($within_group==1){ $buyer_names.=$buyer_arr[$val['BUYER_BUYER']].','; }
            else{ $buyer_names.=$val['BUYER_BUYER'].','; }
        }
    }
    $po_numbers=implode(', ',array_unique(explode(',',rtrim($po_number,','))));
    $style_ref_nos=implode(', ',array_unique(explode(',',rtrim($style_ref_no,','))));
    $buyer_names=implode(', ',array_unique(explode(',',rtrim($buyer_names,','))));

    $sql_buyer = sql_select("select id, buyer_name, country_id, buyer_email, address_1, address_2, address_3, address_4 from lib_buyer where status_active=1 and is_deleted=0");
    foreach ($sql_buyer as $row)
    {
        $buyer_address=$buyer_email="";
        if ($row[csf('address_1')] !='') $buyer_address .= $row[csf('address_1')].', ';
        if ($row[csf('address_2')] !='') $buyer_address .= $row[csf('address_2')].', ';
        if ($row[csf('address_3')] !='') $buyer_address .= $row[csf('address_3')].', ';
        if ($row[csf('address_4')] !='') $buyer_address .= $row[csf('address_4')].', ';
        if ($row[csf('country_id')] !='') $buyer_address .= $country_arr[$row[csf('country_id')]];
        if ($row[csf('buyer_email')] !='') $buyer_email = "Email:&nbsp;".$row[csf('buyer_email')];

        $buyer_details_arr[$row[csf('id')]]['buyer_address']=$buyer_address;
        $buyer_details_arr[$row[csf('id')]]['buyer_email']=$buyer_email;
    }

    if ($advising_bank != ""){
        $sql_bank = sql_select("select a.id, a.bank_name, a.branch_name, a.address, a.swift_code, b.account_type, b.account_no from lib_bank a, lib_bank_account b where a.id=$advising_bank and a.advising_bank=1 and a.is_deleted=0 and a.status_active=1 and b.account_type=10 and b.status_active=1 and b.is_deleted=0 and company_id=$company_id");
        foreach($sql_bank as $row)
        {
            $bank_name=$row[csf('bank_name')];
            $branch_name=$row[csf('branch_name')];
            $address=$row[csf('address')];
            $swift_code=$row[csf('swift_code')];
            $account_no=$row[csf('account_no')];
        }
    }

    $tbl_width=1555;
    ?>
    <style type="text/css">
        table>tr>th,td{word-break: break-all;}
    </style>
    <div style="width:<?=$tbl_width;?>px">
        <table width="<?=$tbl_width;?>" cellspacing="0" border="0">
            <tr>
                <td colspan="19" style="font-size:38px" >
                    <strong><? echo $company_arr[$company_id]; ?></strong>
                </td>
            </tr>
        </table>
        <br>
        <table class="rpt_table" width="<?=$tbl_width;?>" cellspacing="1" rules="all" border="1">
            <tr>
                <td colspan="19" style="text-align: center;font-size:24px;"><strong>PROFORMA INVOICE</strong></td>
            </tr>
            <tr>
                <td colspan="9" width="50%" style="font-size: 18px;"><strong>PROFORMA INVOICE:</strong>&nbsp;<? echo $pi_number; ?></td>
                <td colspan="10" style="font-size: 18px;"><strong>DATE:</strong>&nbsp;<? echo change_date_format($pi_date); ?></td>
            </tr>
            <tr>
                <td colspan="9" style="font-size: 18px;" valign="top">
                    <strong>CONSIGNOR/BENEFICARY:</strong><br>
                    <strong><? echo $company_arr[$company_id]; ?></strong><br>
                    <? echo $company_address[$company_id]; ?><br>
                    <strong>Vat NO:</strong>&nbsp;<? echo $vat_number; ?>&nbsp;<strong>BIN NO:</strong>&nbsp;<? echo $bin_no; ?>
                </td>
                <td colspan="10" style="font-size: 18px;" valign="top">
                    <strong>Advising Bank:</strong>&nbsp;<br>
                    <? echo $bank_name; ?><br>
                    <? echo $branch_name; ?><br>
                    <? echo $address; ?><br>
                    <strong>SWIFT: </strong>&nbsp;<? echo $swift_code; ?>
                </td>
            </tr>
            <tr>
                <td colspan="9" style="font-size: 18px;" valign="top">
                    <strong>BUYER/IMPORTER/BILL TO: </strong><br>
                    <?
                    if ($within_group==1)
                    {echo $company_arr[$buyer_id].'<br>'.$company_address[$buyer_id]; }
                    else if ($within_group==2)
                    { echo $buyer_arr[$buyer_id].'<br>'.$buyer_details_arr[$buyer_id]["buyer_address"]; }
                    ?>
                </td>
                <td colspan="10" style="font-size: 18px;">
                    <strong>DELIVERY TO/CONSIGNEE: </strong><br>
                    <?
                    if ($within_group==1)
                    {echo $company_arr[$buyer_id].'<br>'.$company_address[$buyer_id]; }
                    else if ($within_group==2)
                    { echo $buyer_arr[$buyer_id].'<br>'.$buyer_details_arr[$buyer_id]["buyer_address"]; }
                    ?>
                </td>
            </tr>
            <tr>
                <td colspan="9" width="50%" style="font-size: 18px;"><strong>POST OF LOADING: </strong>BENEFICARY FACTORY, DEPZ.</td>
                <td colspan="10" width="50%" style="font-size: 18px;"><strong>VALIDITY: </strong>
                    <?
                    $str_pi_date = strtotime($pi_date);
                    $str_pi_validity_date = strtotime($pi_validity_date);
                    if($str_pi_date > $str_pi_validity_date) {
                        echo 0;
                    }else{
                        $date1=date_create(date('Y-m-d', $str_pi_date));
                        $date2=date_create(date('Y-m-d', $str_pi_validity_date));
                        $diff=date_diff($date1,$date2);
                        echo $diff->format("%a");
                    }
                    ?> DAYS FROM THE DATE OF PI.</td>
            </tr>
            <tr>
                <td colspan="9" width="50%" style="font-size: 18px;"><strong>POST OF LOADING: </strong>APPLICANT WAREHOUSE.</td>
                <td colspan="10" width="50%" style="font-size: 18px;"><strong>MODE OF SHIPMENT: </strong>BY ROAD.</td>
            </tr>
        </table>
        <br>
        <table width="<?=$tbl_width;?>" cellspacing="0" align="center" border="1" rules="all" class="rpt_table">
            <thead bgcolor="#dddddd" align="center">
            <th width="30">SL No</th>
            <th width="95">Item Group</th>
            <th width="180">Item Description</th>
            <th width="80">Buyer's Buyer</th>
            <th width="115">Buyer's Style</th>
            <th width="80">HS Code</th>
            <th width="135">Job Order No</th>
            <th width="135">Work Order No</th>
            <th width="40">Ply</th>
            <th width="55">L</th>
            <th width="55">W</th>
            <th width="55">H</th>
            <th width="55">F</th>
            <th width="55">G</th>
            <th width="55">Thick ness</th>
            <th width="75">Qty</th>
            <th width="45">UOM</th>
            <th width="75">Price/USD</th>
            <th >Amount USD</th>
            </thead>
            <tbody>
            <?
            $i=1;
            $tot_wo_qty=$tot_amount=0;
            foreach ($all_data_arr as $key=>$row)
            {
                ?>
                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td align="center"><? echo $i; ?></td>
                    <?
                    if($tempArrGroup[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrGroup[$key]?>" style="word-break: break-word;"><? echo $item_group_arr[$row['gmts_item_id']]; ?></td>
                        <?
                    }
                    if($tempArrDesc[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrDesc[$key]?>" style="word-break: break-word;"><? echo $row['item_desc']; ?></td>
                        <?
                    }
                    if($tempArrBuyer[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrBuyer[$key]?>" align="center" style="word-break: break-word;">
                            <?
                            if($within_group==1)
                                $buyer_names= $buyer_arr[$val['BUYER_BUYER']];
                            else
                                $buyer_names = $val['BUYER_BUYER'];
                            echo $buyer_names;
                            ?>
                        </td>
                        <?
                    }
                    if($tempArrStyle[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrStyle[$key]?>" style="word-break: break-word;"><? echo $row['buyer_style_ref']; ?></td>
                        <?
                    }
                    if($tempArrHscode[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrHscode[$key]?>" align="center" style="word-break: break-word;"><? echo $row['hs_code_dtls']; ?></td>
                        <?
                    }
                    if($tempArrWorkorder[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrWorkorder[$key]?>" align="center"><? echo $row['work_order_no']; ?></td>
                        <?
                    }
                    if($tempArrBooking[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrBooking[$key]?>" align="center"><? echo $row['booking']; ?></td>
                        <?
                    }
                    ?>
                    <td align="center"><? echo $row['PLY']; ?></td>
                    <td align="center"><?=(int) $row['LENGTH'] != 0 ? $row['LENGTH']." ".strtolower($unit_of_measurement[$row['MEASUREMENTID']]) : ""?></td>
                    <td align="center"><?=(int) $row['WIDTH'] !=  0 ? $row['WIDTH']." ".strtolower($unit_of_measurement[$row['MEASUREMENTID']]) : ""?></td>
                    <td align="center"><?= (int) $row['HEIGHT'] !=  0 ? $row['HEIGHT']." ".strtolower($unit_of_measurement[$row['MEASUREMENTID']]) : ""?></td>
                    <td align="center"><?= (int) $row['FLAP'] !=  0 ? $row['FLAP']." ".strtolower($unit_of_measurement[$row['MEASUREMENTID']]) : ""?></td>
                    <td align="center"><?= (int) $row['GUSSET'] !=  0 ? $row['GUSSET']." ".strtolower($unit_of_measurement[$row['MEASUREMENTID']]) : ""?></td>
                    <td align="center"><? echo $row['THICKNESS']; ?></td>
                    <td align="right"><? echo number_format($row['quantity'],2); ?></td>
                    <td align="center"><? echo $unit_of_measurement[$row['uom']]; ?></td>
                    <td align="right"><? echo number_format($row['amount']/$row['quantity'],4); ?></td>
                    <td align="right"><? echo number_format($row['amount'],2); ?></td>
                </tr>
                <?
                $tot_quantity+=$row['quantity'];
                $tot_amount+=$row['amount'];
                $i++;
            }
            ?>
            </tbody>
            <tfoot>
            <tr bgcolor="#dddddd">
                <td colspan="14" align="right"></td>
                <td align="right"><strong>Total</strong></td>
                <td align="right"><strong><? echo number_format($tot_quantity,2); ?></strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><? echo number_format($tot_amount,2); ?></strong></td>
            </tr>
            </tfoot>
        </table>
        <table>
            <tr height="20"></tr>
            <tr>
                <td valign="top"><strong>In-Words: </strong></td>
                <td><? echo number_to_words(number_format($tot_amount,2),'USD','Cent');?></td>
            </tr>
            <tr>
            <tr height="20"></tr>
        </table>
        <?
        echo get_spacial_instruction($mst_id,"100%",152);
        ?>
        <div style="margin-top: 50px;margin-left: 100px;">
            <table>
                <tr>
                    <td><strong>BUYER'S ACCEPTANCE</strong></td>
                </tr>
            </table>
        </div>
        <?
        echo signature_table(257, $company_id, $tbl_width, "", "90",$user_id);
        ?>
    </div>
    <div style="margin-top: 10px; border-top:2px solid black; width: <?=$tbl_width;?>">
        <table width="<?=$tbl_width;?>" border="0">
            <tr>
                <td width="30"></td>
                <td width="80"></td>
                <td width="180"></td>
                <td width="80"></td>
                <td width="100"></td>
                <td width="80"></td>
                <td width="135"></td>
                <td width="135"></td>
                <td width="40"></td>
                <td width="60"></td>
                <td width="60"></td>
                <td width="60"></td>
                <td width="60"></td>
                <td width="60"></td>
                <td width="60"></td>
                <td width="75"></td>
                <td width="45"></td>
                <td width="75"></td>
                <td ></td>
            </tr>
            <tr>
                <td colspan="10" valign="top" style="padding-left: 10px; word-break: break-word; font-size: 16px;">
                    <div style="float: left; width: 430px;">
                        <strong>Head Office Address:</strong><br>
                        <?=$group_address[$company_id]?>
                    </div>
                </td>
                <td colspan="9" valign="top">
                    <div style="float: right; width: 430px; padding-right: 10px; word-break: break-word; font-size: 16px;">
                        <strong>Factory Address:</strong><br>
                        <?=$company_address_footer[$company_id]?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <?
    exit();
}

?>


 