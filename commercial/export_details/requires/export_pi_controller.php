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
		if($id==477){echo '<input type="button" id="btn_print" value="Knit Fabric 2" class="formbutton" style="width:80px; " onClick="show_print_report();" >&nbsp;';}
		if($id==478){echo '<input type="button" id="btn_print_3" value="Grmnt Washing" class="formbutton" style="width:100px; " onClick="show_print_report_3();">&nbsp;';}
		if($id==835){echo '<input type="button" id="btn_print_3" value="Grmnt Washing 2" class="formbutton" style="width:100px; " onClick="btn_print_Grmnt_Washing_2();">&nbsp;';}
		if($id==479){echo '<input type="button" id="btn_print_4" value="Accessories 1" class="formbutton" style="width:80px;" onClick="show_print_report_4();" >&nbsp;';}
		if($id==480){echo '<input type="button" id="btn_print_5" value="Grmnt Embroidery" class="formbutton" style="width:100px;" onClick="show_print_report_5();">&nbsp;';}
		if($id==481){echo '<input type="button" id="btn_print_6" value="Accessories 2" class="formbutton" style="width:80px;" onClick="show_print_report_6();">&nbsp;';}
		if($id==482){echo '<input type="button" id="Print1" value="Knit Fabric 1" class="formbutton" style="width:80px;" onClick="fnc_pi_mst(4);" >&nbsp;';}
		if($id==483){echo '<input type="button" id="Print8" value="Knit Fabric 4" class="formbutton" style="width:80px;" onClick="show_print_report_8();" >&nbsp;';}
		if($id==484){echo '<input type="button" id="btn_print_9" value="Knit Fabric 5" class="formbutton" style="width:80px;" onClick="show_print_report_9();">&nbsp;';}
		if($id==485){echo '<input type="button" id="btn_print_10" value="Knitting, Dyeing & Finishing" class="formbutton" style="width:120px;" onClick="show_print_report_10();">&nbsp;';}
		if($id==486){echo '<input type="button" id="btn_print_11" value="Accessories 3" class="formbutton" style="width:80px;" onClick="show_print_report_11();">&nbsp;';}
        if($id==487){echo '<input type="button" id="btn_print_12" value="AOP" class="formbutton" style="width:80px;" onClick="show_print_report_12();">&nbsp;';}
        if($id==488){echo '<input type="button" id="btn_print_13" value="Accessories 4" class="formbutton" style="width:80px;" onClick="show_print_report_13();">&nbsp;';}
		if($id==489){echo '<input type="button" id="btn_print_7" value="Knit Fabric 3" class="formbutton" style="width:80px;" onClick="show_print_report_7();" >&nbsp;';}
        if($id==490){echo '<input type="button" id="btn_print_14" value="Accessories 5" class="formbutton" style="width:80px;" onClick="show_print_report_14();">&nbsp;';}
		if($id==491){echo '<input type="button" id="btn_print_15" value="YD" class="formbutton" style="width:80px;" onClick="show_print_report_15();">&nbsp;';}
		if($id==159){echo '<input type="button" id="btn_print_16" value="Woven Garments" class="formbutton" style="width:80px;" onClick="show_print_report_16();">&nbsp;';}
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
		echo create_drop_down( "cbo_buyer_name", 151, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select Buyer --", "$selected_buyer", "load_drop_down( 'requires/export_pi_controller', this.value, 'load_drop_down_issue_bank', 'issue_bank_td' );",$disabled,"","","" );
	}
	else if($data[1]==2)
	{
		echo create_drop_down( "cbo_buyer_name", 151, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,2,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected_buyer, "load_drop_down( 'requires/export_pi_controller', this.value, 'load_drop_down_issue_bank', 'issue_bank_td' );",$disabled,"","","" ); 
	}
	exit();
}

if ($action=="load_drop_down_issue_bank")
{
	$sql = "select a.bank_name as bank_name, a.id from lib_bank a, LIB_BUYER_TAG_BANK b where a.id=b.TAG_BANK and b.BUYER_ID='$data' and a.is_deleted=0 and a.status_active=1 and a.ISSUSING_BANK=1 order by bank_name";
	//echo $sql;
 	echo create_drop_down( "txt_issuing_bank", 151, $sql,"id,bank_name", 1, "Select", '', '' );
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

	echo create_drop_down( "cbo_advising_bank", 151,$sql,"id,bank_name", 1, "-- Select Bank --", $selected, "check_swift_code_setting(this.value)" );
	// echo create_drop_down( "cbo_advising_bank", 151, "$sql","id,bank_name",1, "-- Select Bank --", "", "","","","","" );		
	exit();
}

if ($action=="job_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
    ?>	
    <script>
        function js_set_value(str)
        {
            $("#hdn_job_info").val(str); 
            parent.emailwindow.hide();
        }  
	</script>
    <input type="hidden" id="hdn_job_info" />
    <?
	//if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name=$data[1]";
	// if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
	//$job_no=str_replace("'","",$txt_job_id);
	//if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and job_no_prefix_num in('$data[2]')";
	
	$sql="select id, job_no_prefix_num, yd_job, order_no, pro_type, order_type, yd_type, yd_process from yd_ord_mst where company_id=$data[0] and status_active=1 and is_deleted=0 and entry_form=374 and order_type=2 and check_box_advance=1 and yd_job not in(select YD_JOB from com_export_pi_mst where item_category_id=69 and YD_JOB is not null and status_active in(1,2,3) and is_deleted=0) order by id desc";//$job_no_cond
	$sql_res=sql_select($sql);
	// echo $sql;

	//$arr=array(2=>$w_pro_type_arr,3=>$w_order_type_arr,4=>$yd_type_arr,5=>$yd_process_arr);
	
	//echo  create_list_view("list_view", "YD Job No,YD Worder No,Prod. Type,Order Type,Y/D Type,Y/D Process", "100,100,100,100,100,100","660","350",0, $sql, "js_set_value", "id,yd_job", "", 1, "0,0,pro_type,order_type,yd_type,yd_process", $arr , "yd_job,order_no,pro_type, order_type, yd_type, yd_process", "export_pi_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','') ;
	?>
	<table cellpadding="0" cellspacing="0" border="1" rules="all" width="700" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">YD Job No</th>
                <th width="100">YD Worder No</th>
                <th width="100">Prod. Type</th>
                <th width="100">Order Type</th>
                <th width="100">Y/D Type</th>
                <th >Y/D Process</th>               
            </tr>
        </thead>
    </table>
	<div style="width:720px; max-height:350; overflow-y:scroll;">
	    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="700" class="rpt_table" id="list_view">	    	
			<?
			$i=1;
			foreach($sql_res as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('yd_job')];?>')" style="cursor:pointer" align="center">
					<td width="30"><? echo $i; ?></td>
					<td width="100" align="left"><? echo $row[csf('yd_job')]; ?></td>
					<td width="100" align="left"><? echo $row[csf('order_no')]; ?></td>
					<td width="100" align="left"><? echo $w_pro_type_arr[$row[csf('pro_type')]]; ?></td>
					<td width="100" align="left"><? echo $w_order_type_arr[$row[csf('order_type')]]; ?></td>
					<td width="100" align="left"><? echo $yd_type_arr[$row[csf('yd_type')]]; ?></td>
					<td align="left"><? echo $yd_process_arr[$row[csf('yd_process')]]; ?></td>
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

if ($action==='company_variable_setting_check')
{
	$variable_setting = return_field_value('pi_source_btb_lc', 'variable_settings_commercial', "company_name=$data and variable_list=27", 'pi_source_btb_lc');
	echo $variable_setting;
	exit();
}

if ($action==='swift_code_setting')
{
	$data=explode('**',$data); 
	 $sql="select a.id,a.swift_code from lib_bank a , lib_bank_account b where a.advising_bank=1 and a.id=b.account_id and a.status_active=1 and b.status_active=1 and b.company_id=$data[1]  and a.id=$data[0] group by a.id ,a.swift_code ";
	$swift_code_setting=sql_select($sql);
	echo $swift_code_setting[0][csf('swift_code')];
	exit();
}		

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 	
	
	if ($operation==0)  // Insert Here
	{ 
		if (is_duplicate_field( "pi_number", "com_export_pi_mst", "pi_number=$pi_number and exporter_id=$cbo_exporter_id and within_group=$cbo_within_group and status_active in(1,2,3) and is_deleted=0" ) == 1)
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
		
			$field_array="id,pi_no_prefix,pi_no_prefix_num,item_category_id,exporter_id,within_group,ready_to_approved,buyer_id,pi_number,pi_date,last_shipment_date,pi_validity_date,currency_id,hs_code,swift_code,internal_file_no,remarks,weight_approx,attention,advising_bank,beneficiary_bank,entry_form,inserted_by,insert_date,pi_revised_date,pi_revise,pay_term,tenor,yd_job,yd_job_id,status_active";
			
			$data_array="(".$id.",'".$new_pi_number[1]."','".$new_pi_number[2]."',".$cbo_item_category_id.",".$cbo_exporter_id.",".$cbo_within_group.",".$cbo_approved.",".$cbo_buyer_name.",".$pi_number.",".$pi_date.",".$last_shipment_date.",".$pi_validity_date.",".$cbo_currency_id.",".$hs_code.",".$txt_swift.",".$txt_internal_file_no.",".$txt_remarks.",".$txt_weight_approx.",".$txt_attention.",".$cbo_advising_bank.",".$txt_issuing_bank.",152,".$user_id.",'".$pc_date_time."',".$pi_revised_date.",".$cbo_pi_revise.",".$cbo_pay_term.",".$txt_tenor.",".$txt_advance_job.",".$txt_yd_job_id.",".$cbo_status.")";	
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
		if (is_duplicate_field( "pi_number", "com_export_pi_mst", "pi_number=$pi_number and exporter_id=$cbo_exporter_id and within_group=$cbo_within_group and is_deleted=0 and id!=$update_id" ) == 1)
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

			$field_array="item_category_id*exporter_id*within_group*ready_to_approved*buyer_id*pi_number*pi_date*last_shipment_date*pi_validity_date*currency_id*hs_code*swift_code*internal_file_no*remarks*weight_approx*attention*advising_bank*beneficiary_bank*pi_revised_date*pi_revise*pay_term*tenor*status_active*yd_job*yd_job_id*updated_by*update_date";
			
			$data_array="".$cbo_item_category_id."*".$cbo_exporter_id."*".$cbo_within_group."*".$cbo_approved."*".$cbo_buyer_name."*".$pi_number."*".$pi_date."*".$last_shipment_date."*".$pi_validity_date."*".$cbo_currency_id."*".$hs_code."*".$txt_swift."*".$txt_internal_file_no."*".$txt_remarks."*".$txt_weight_approx."*".$txt_attention."*".$cbo_advising_bank."*".$txt_issuing_bank."*".$pi_revised_date."*".$cbo_pi_revise."*".$cbo_pay_term."*".$txt_tenor."*".$cbo_status."*".$txt_advance_job."*".$txt_yd_job_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			if(str_replace("'","",$cbo_item_category_id)==69)
			{
				$sql_yd_order=sql_select("select yd_job from yd_ord_mst where tag_pi_no=$pi_number and advance_job=$txt_advance_job and order_type=2 and check_box_confirm=1 and status_active =1 and is_deleted=0");
				if(count($sql_yd_order)>0)
				{
					$field_array="pi_revise*updated_by*update_date";			
					$data_array="".$cbo_pi_revise."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
			}

			//echo "10**$data_array";die;
			
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

		if(str_replace("'","",$cbo_item_category_id)==69)
		{
			$sql_yd_order=sql_select("select yd_job from yd_ord_mst where tag_pi_no=$pi_number and advance_job=$txt_advance_job and order_type=2 and check_box_confirm=1 and status_active =1 and is_deleted=0");
			if(count($sql_yd_order)>0)
			{
				echo "8**".str_replace("'", '', $update_id)."**Delete Not Allow, Order Entry Found. Order No: ".$sql_yd_order[0][csf('yd_job')];
				disconnect($con);
				die;
			}
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
	
		$field_array="id,pi_id,work_order_no,work_order_id,buyer_style_ref,work_order_dtls_id,determination_id,construction,composition,hs_code,booking,gmts_item_id,main_process_id,embl_type,body_part,item_desc,item_size,color_id,aop_color_id,gsm,dia_width,wash_type,buyer_job,cust_buyer,count_id,count_type_id,yarn_type_id,yarn_composition_id,color_range_id,app_ref,adj_type_id,order_no,acc_po_no,brand_id,att_deta, total_order_qty,uom,quantity,rate,amount,net_pi_rate,net_pi_amount,remarks,inserted_by,insert_date"; 
		
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
			$buyerJob="buyerJob_".$i;
			$custBuyer="custBuyer_".$i;
			$hidecountId="hidecountId_".$i;
			$countTypeId="countTypeId_".$i;		
			$yarnTypeId="yarnTypeId_".$i;
			$yarnCompositionId="yarnCompositionId_".$i;
			$colorRangeId="colorRangeId_".$i;
			$appRef="appRef_".$i;
			$adjTypeId="adjTypeId_".$i;
			$totalQty="totalQty_".$i;
			$uom="uom_".$i;
			$quantity="quantity_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			$remarks="txtRemarks_".$i;
			$Order_no="Order_no_".$i;
			$accpo_no="AccPo_NO_".$i;
			$brandid="BrandId_".$i;
			$Status_ids="Status_".$i;
			
			$cal_amt=str_replace("'","",$$quantity)*str_replace("'","",$$rate);
			
			$perc=($cal_amt/$txt_total_amount)*100;
			$net_pi_amount=($perc*$txt_total_amount_net)/100;
			$net_pi_rate=$net_pi_amount/str_replace("'","",$$quantity);
			
			if($cbo_currency_id==1)
				$net_pi_amount=number_format($net_pi_amount,$dec_place[4],'.','');
			else
				$net_pi_amount=number_format($net_pi_amount,$dec_place[5],'.','');
					
			$net_pi_rate=number_format($net_pi_rate,$dec_place[3],'.','');
			
			if($data_array!="") $data_array.=",";
			$data_array .="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$jobstyle)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$determinationId)."','".str_replace("'","",$$construction)."','".str_replace("'","",$$composition)."','".str_replace("'","",$$hscode)."','".str_replace("'","",$$salesbooking)."','".str_replace("'","",$$gsmitem)."','".str_replace("'","",$$processembl)."','".str_replace("'","",$$embltype)."','".str_replace("'","",$$bodypart)."','".str_replace("'","",$$itemDesc)."','".str_replace("'","",$$itemSize)."','".str_replace("'","",$$colorId)."','".str_replace("'","",$$aopcolorId)."','".str_replace("'","",$$gsm)."','".str_replace("'","",$$diawidth)."','".str_replace("'","",$$washType)."','".str_replace("'","",$$buyerJob)."','".str_replace("'","",$$custBuyer)."','".str_replace("'","",$$hidecountId)."','".str_replace("'","",$$countTypeId)."','".str_replace("'","",$$yarnTypeId)."','".str_replace("'","",$$yarnCompositionId)."','".str_replace("'","",$$colorRangeId)."','".str_replace("'","",$$appRef)."','".str_replace("'","",$$adjTypeId)."','".str_replace("'","",$$Order_no)."','".str_replace("'","",$$accpo_no)."','".str_replace("'","",$$brandid)."','".str_replace("'","",$$Status_ids)."','".str_replace("'","",$$totalQty)."','".str_replace("'","",$$uom)."',".$$quantity.",".$$rate.",'".$cal_amt."',".$net_pi_rate.",".$net_pi_amount.",'".str_replace("'","",$$remarks)."',".$user_id.",'".$pc_date_time."')";
			
			$id=$id+1;
			
		}
		// echo "5**insert into com_export_pi_dtls (".$field_array.") Values ".$data_array."";disconnect($con);die;
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
		
		if($update_id!='')
		{
			if(str_replace("'","",$cbo_item_category)==69)
			{
				 
 			 //$yd_job_id=str_replace("'","",$txt_yd_job_id);  
  		     $pi_Yd_qnty_sql=sql_select("SELECT d.yd_job_id as job_id,sum(a.order_quantity) as yd_quantity,sum(a.amount) as yd_amount, sum(c.quantity) as pi_qty,sum(c.amount) as pi_amount from yd_ord_dtls a,yd_ord_mst b, com_export_pi_dtls c,com_export_pi_mst d where a.pi_dtls_id=c.id  and a.mst_id=b.id and c.pi_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and b.tag_pi_no='$pi_number' and b.advance_job='$txt_advance_job' and b.order_type=2 and b.check_box_confirm=1 group by d.yd_job_id");
  			 $yd_quantity=number_format($pi_Yd_qnty_sql[0][csf('yd_quantity')],$dec_place[5],'.',''); 
 			 // $pi_qty=number_format($pi_Yd_qnty_sql[0][csf('pi_qty')],$dec_place[5],'.','');;
			  $yd_amount=number_format($pi_Yd_qnty_sql[0][csf('yd_amount')],$dec_place[5],'.',''); 
			  //$pi_amount=number_format($pi_Yd_qnty_sql[0][csf('pi_amount')],$dec_place[5],'.','');
 			  $pi_total_amount=number_format($txt_total_amount,$dec_place[5],'.',''); 
			  $pi_total_qnty=number_format($txt_total_qnty,$dec_place[5],'.',''); 
			  
			 
  	
			if($pi_total_qnty<$yd_quantity)
			{ 
				 
				echo "8**".str_replace("'", '', $update_id)."**Update Not Allow, PI Order Qty. can not be less than Order Qty.!!! Balance Qty.=".$yd_quantity; 
				disconnect($con);
				die;	
			}	
			
			if($pi_total_amount<$yd_amount)
			{ 
				echo "8**".str_replace("'", '', $update_id)."**Update Not Allow, PI Amount can not be less than Order Amount.!!! Balance Amount. =".$yd_amount;
				
				disconnect($con);
				die;	
			}	
				
				
				/*$sql_yd_order=sql_select("select yd_job from yd_ord_mst where tag_pi_no='$pi_number' and advance_job='$txt_advance_job' and order_type=2 and check_box_confirm=1 and status_active =1 and is_deleted=0");
	
				if(count($sql_yd_order)>0)
				{
					echo "8**".str_replace("'", '', $update_id)."**Update And Delete Not Allow, Order Entry Found. Order No: ".$sql_yd_order[0][csf('yd_job')];
					
					disconnect($con);
					die;	
				}*/
			}
		 }
		
		$sql_app=sql_select("select pi_number from com_pi_master_details where export_pi_id=$update_id and import_pi=1 and status_active =1 and is_deleted=0");
		if(count($sql_app)>0)
		{
			echo "7**".str_replace("'", '', $update_id);disconnect($con); 
			die;	
		}
		
		$field_array_update="work_order_no*work_order_id*buyer_style_ref*work_order_dtls_id*determination_id*construction*composition*hs_code*booking*gmts_item_id*main_process_id*embl_type*body_part*item_desc*item_size*color_id*aop_color_id*gsm*dia_width*wash_type*buyer_job*cust_buyer*count_id*count_type_id*yarn_type_id*yarn_composition_id*color_range_id*app_ref*adj_type_id*order_no*acc_po_no*brand_id*att_deta* total_order_qty*uom*quantity*rate*amount*net_pi_rate*net_pi_amount*remarks*updated_by*update_date";
			
		$field_array="id, pi_id, work_order_no, work_order_id, buyer_style_ref, work_order_dtls_id, determination_id, construction, composition, hs_code, booking, gmts_item_id, main_process_id, embl_type, body_part, item_desc, item_size, color_id, aop_color_id,gsm,dia_width,wash_type,buyer_job,cust_buyer,count_id,count_type_id,yarn_type_id,yarn_composition_id,color_range_id,app_ref,adj_type_id,order_no,acc_po_no,brand_id,att_deta,total_order_qty,uom,quantity,rate,amount,net_pi_rate,net_pi_amount,remarks,inserted_by, insert_date"; 

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
			$buyerJob="buyerJob_".$i;
			$custBuyer="custBuyer_".$i;
			$hidecountId="hidecountId_".$i;
			$countTypeId="countTypeId_".$i;	
			$yarnTypeId="yarnTypeId_".$i;
			$yarnCompositionId="yarnCompositionId_".$i;
			$colorRangeId="colorRangeId_".$i;
			$appRef="appRef_".$i;
			$adjTypeId="adjTypeId_".$i;
			$totalQty="totalQty_".$i;
			$uom="uom_".$i;
			$quantity="quantity_".$i;
			$rate="rate_".$i;
			$amount="amount_".$i;
			$remarks="txtRemarks_".$i;
			$Order_no="Order_no_".$i;
			$accpo_no="AccPo_NO_".$i;
			$brandid="BrandId_".$i;
			$Status_ids="Status_".$i;
			$cal_amt=str_replace("'","",$$quantity)*str_replace("'","",$$rate);
						
			$perc=($cal_amt/$txt_total_amount)*100;
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
				$data_array_update[str_replace("'",'',$$updateIdDtls)] = explode("*",("'".str_replace("'","",$$workOrderNo)."'*'".str_replace("'","",$$workOrderId)."'*'".str_replace("'","",$$jobstyle)."'*'".str_replace("'","",$$workOrderDtlsId)."'*'".str_replace("'","",$$determinationId)."'*'".str_replace("'","",$$construction)."'*'".str_replace("'","",$$composition)."'*'".str_replace("'","",$$hscode)."'*'".str_replace("'","",$$salesbooking)."'*'".str_replace("'","",$$gsmitem)."'*'".str_replace("'","",$$processembl)."'*'".str_replace("'","",$$embltype)."'*'".str_replace("'","",$$bodypart)."'*'".str_replace("'","",$$itemDesc)."'*'".str_replace("'","",$$itemSize)."'*'".str_replace("'","",$$colorId)."'*'".str_replace("'","",$$aopcolorId)."'*'".str_replace("'","",$$gsm)."'*'".str_replace("'","",$$diawidth)."'*'".str_replace("'","",$$washType)."'*'".str_replace("'","",$$buyerJob)."'*'".str_replace("'","",$$custBuyer)."'*'".str_replace("'","",$$hidecountId)."'*'".str_replace("'","",$$countTypeId)."'*'".str_replace("'","",$$yarnTypeId)."'*'".str_replace("'","",$$yarnCompositionId)."'*'".str_replace("'","",$$colorRangeId)."'*'".str_replace("'","",$$appRef)."'*'".str_replace("'","",$$adjTypeId)."'*'".str_replace("'","",$$Order_no)."'*'".str_replace("'","",$$accpo_no)."'*'".str_replace("'","",$$brandid)."'*'".str_replace("'","",$$Status_ids)."'*'".str_replace("'","",$$totalQty)."'*'".str_replace("'","",$$uom)."'*".$$quantity."*".$$rate."*'".$cal_amt."'*".$net_pi_rate."*".$net_pi_amount."*'".str_replace("'","",$$remarks)."'*".$user_id."*'".$pc_date_time."'"));
			}
			else
			{
				if($data_array!="") $data_array.=",";
				$data_array .="(".$id.",".$update_id.",'".str_replace("'","",$$workOrderNo)."','".str_replace("'","",$$workOrderId)."','".str_replace("'","",$$jobstyle)."','".str_replace("'","",$$workOrderDtlsId)."','".str_replace("'","",$$determinationId)."','".str_replace("'","",$$construction)."','".str_replace("'","",$$composition)."','".str_replace("'","",$$hscode)."','".str_replace("'","",$$salesbooking)."','".str_replace("'","",$$gsmitem)."','".str_replace("'","",$$processembl)."','".str_replace("'","",$$embltype)."','".str_replace("'","",$$bodypart)."','".str_replace("'","",$$itemDesc)."','".str_replace("'","",$$itemSize)."','".str_replace("'","",$$colorId)."','".str_replace("'","",$$aopcolorId)."','".str_replace("'","",$$gsm)."','".str_replace("'","",$$diawidth)."','".str_replace("'","",$$washType)."','".str_replace("'","",$$buyerJob)."','".str_replace("'","",$$custBuyer)."','".str_replace("'","",$$hidecountId)."','".str_replace("'","",$$countTypeId)."','".str_replace("'","",$$yarnTypeId)."','".str_replace("'","",$$yarnCompositionId)."','".str_replace("'","",$$colorRangeId)."','".str_replace("'","",$$appRef)."','".str_replace("'","",$$adjTypeId)."','".str_replace("'","",$$Order_no)."','".str_replace("'","",$$accpo_no)."','".str_replace("'","",$$brandid)."','".str_replace("'","",$$Status_ids)."','".str_replace("'","",$$totalQty)."','".str_replace("'","",$$uom)."',".$$quantity.",".$$rate.",'".$cal_amt."',".$net_pi_rate.",".$net_pi_amount.",'".str_replace("'","",$$remarks)."',".$user_id.",'".$pc_date_time."')"; 
				$id=$id+1;
			}
		}

		$rID=true; $rID2=true;
		// echo "10**".bulk_update_sql_statement( "com_export_pi_dtls", "id", $field_array_update, $data_array_update, $id_arr ); die;
		if(count($data_array_update)>0)
		{
			//echo "5**".bulk_update_sql_statement( "com_export_pi_dtls", "id", $field_array_update, $data_array_update, $id_arr );oci_rollback($con);disconnect($con);die;
			$rID=execute_query(bulk_update_sql_statement( "com_export_pi_dtls", "id", $field_array_update, $data_array_update, $id_arr ));
		}
		
		if($data_array!="")
		{
			$rID2=sql_insert("com_export_pi_dtls",$field_array,$data_array,0);
		}

		$rID3=sql_update("com_export_pi_mst",$field_array_update2,$data_array_update2,"id",$update_id,1);
		//echo "5**".$rID."**".$rID2."**".$rID3;oci_rollback($con);disconnect($con);die;
		
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
		disconnect($con);die;
	}
	else if ($operation==2)// Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($update_id!='')
		{
			if(str_replace("'","",$cbo_item_category)==69)
			{
				$sql_yd_order=sql_select("select yd_job from yd_ord_mst where tag_pi_no='$pi_number' and advance_job='$txt_advance_job' and order_type=2 and check_box_confirm=1 and status_active =1 and is_deleted=0");
	
				if(count($sql_yd_order)>0)
				{
					echo "8**".str_replace("'", '', $update_id)."**Delete Not Allow, Order Entry Found. Order No: ".$sql_yd_order[0][csf('yd_job')];
					
					disconnect($con);
					die;	
				}
			}
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
	<div align="center" style="width:1070px;">
		<form name="searchpifrm"  id="searchpifrm">
			<fieldset style="width:100%;">
			<legend>Enter search words</legend>           
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="950" class="rpt_table">
	                <thead>
	                    <th>Company</th>
	                    <th>PI Number</th>
                        <th><? if($item_category_id==37) echo "Wash "; else if ($item_category_id==68 || $item_category_id==69) echo "YD "; ?>Job No</th>
	                    <th><? if ($item_category_id==68 || $item_category_id==69) echo "YD WO No"; else echo "Sales/Booking"; ?></th>
	                    <th>System ID</th>
	                    <th>PI Date Range</th>
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
							 <input type="hidden" name="item_category_id" id="item_category_id" value="<? echo $item_category; ?>">
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
	 
	$pi_number_cond=$sys_id_cond=$job_number_cond=$pi_date_cond=$within_group_cond=$buyer_name_cond=$booking_no_cond=$item_category_cond='';
	if ($pi_number != '') $pi_number_cond=" and a.pi_number like '%".$pi_number."%'";
	if ($sys_id != '') $sys_id_cond=" and a.id=$sys_id";	
	if ($job_no != '') $job_number_cond=" and b.work_order_no like '%".$job_no."'";
	if ($txt_booking_no != '') $booking_no_cond=" and b.booking like '%".$txt_booking_no."'";
	if ($within_group != '' && $within_group != 0) $within_group_cond=" and a.within_group =".$within_group;
	if ($cbo_buyer_name != '' && $cbo_buyer_name != 0) $buyer_name_cond=" and a.buyer_id =".$cbo_buyer_name;
	if ($iten_cat_id > 0) $item_category_cond=" and a.item_category_id =".$iten_cat_id;

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
	$sub_order_data=array();
	if ($iten_cat_id==68 || $iten_cat_id==69)
	{
		$sub_order_sql=sql_select("select id, job_no_prefix_num, party_id, order_no from yd_ord_mst where entry_form=374 and status_active=1");
		$sub_order_data=array();
		foreach($sub_order_sql as $row)
		{
			$sub_order_data[$row[csf("id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$sub_order_data[$row[csf("id")]]["order_no"]=$row[csf("order_no")];
			$sub_order_data[$row[csf("id")]]["party_id"]=$row[csf("party_id")];
		}
	}
	else
	{
		$sub_order_sql=sql_select("select id, job_no_prefix_num, party_id from subcon_ord_mst where entry_form=295 and status_active=1");
		$sub_order_data=array();
		foreach($sub_order_sql as $row)
		{
			$sub_order_data[$row[csf("id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$sub_order_data[$row[csf("id")]]["party_id"]=$row[csf("party_id")];
		}
	}

	// echo $is_dtls;die;
	
	
	if($is_dtls==1)
	{
		if($db_type==2) 
		{
			$job_con=" rtrim(xmlagg(xmlelement(e,b.work_order_no,',').extract('//text()') order by b.work_order_no).GetClobVal(),',') AS JOB_NO, rtrim(xmlagg(xmlelement(e,b.work_order_id,',').extract('//text()') order by b.work_order_id).GetClobVal(),',') AS JOB_ID, rtrim(xmlagg(xmlelement(e,b.booking,',').extract('//text()') order by b.booking).GetClobVal(),',') AS BOOKING_NO";
		} else {
			$job_con="group_concat(b.work_order_no) as JOB_NO , group_concat(b.work_order_id) as JOB_ID, group_concat(b.booking) as BOOKING_NO";
		}

		$sql= "SELECT a.ID, a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.EXPORTER_ID, a.WITHIN_GROUP, a.LAST_SHIPMENT_DATE, a.HS_CODE, a.BUYER_ID, a.PI_REVISE, a.PI_REVISED_DATE, a.PAY_TERM, $job_con
		from com_export_pi_mst a, com_export_pi_dtls b
		where a.id=b.pi_id and a.exporter_id=$exporter_id $pi_number_cond $sys_id_cond $job_number_cond $pi_date_cond $within_group_cond $buyer_name_cond $booking_no_cond $item_category_cond and a.entry_form=152 and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id, a.pi_number, a.pi_date, a.item_category_id, a.exporter_id, a.within_group, a.last_shipment_date, a.hs_code, a.buyer_id, a.PI_REVISE, a.PI_REVISED_DATE, a.PAY_TERM
		order by a.id desc";
	} 
	else  // without details
	{
		$sql_dtls="SELECT a.id as PI_ID from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.exporter_id=$exporter_id $pi_number_cond $sys_id_cond $job_number_cond $pi_date_cond $booking_no_cond and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=152 and b.pi_id is not null";
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

		$sql= "SELECT a.ID, a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.EXPORTER_ID, a.WITHIN_GROUP, a.LAST_SHIPMENT_DATE, a.HS_CODE, a.BUYER_ID, a.PI_REVISE, a.PI_REVISED_DATE, a.PAY_TERM 
		from com_export_pi_mst a 
		where a.exporter_id=$exporter_id $pi_number_cond $sys_id_cond $job_number_cond $pi_date_cond $pi_id_cond $within_group_cond $buyer_name_cond $item_category_cond and a.entry_form=152 and a.status_active=1 and a.is_deleted=0 order by a.id desc";
	}
	//echo $sql;//die;
	$sql_result=sql_select($sql);
	?>
    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="1050" class="rpt_table">
        <thead>
			<tr>
				<th colspan="15"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
			</tr>
            <tr>
                <th width="30">SL</th>
                <th width="100">PI No</th>
                <th width="70">PI Date</th>
                <th width="80">Item Category</th>
                <th width="100">Exporter</th>
                <th width="80"><? if($iten_cat_id==10) {echo "Customer/";} else if ($iten_cat_id==68 || $iten_cat_id==69) {echo "Party/";} ?>Buyer</th>
                <th width="80"><? if($iten_cat_id==37) echo "Wash "; else if ($iten_cat_id==68 || $iten_cat_id==69) {echo "YD ";} ?>Job No</th> 
                <th width="80"><? if ($iten_cat_id==68 || $iten_cat_id==69) {echo "YD WO No";} else {echo "Sales/Book/WO No";} ?></th> 
                <th width="50">Job Suffix</th>
                <th width="50">Within Group</th>
                <th width="60">Last Shipment Date</th>
				<th width="60">PI Revise</th>
				<th width="60">PI Revised Date</th>
				<th width="60">Pay Term</th>
                <th>HS Code</th>
            </tr>
        </thead>
    </table>
    <div style="width:1070px; max-height:270; overflow-y:scroll;">
	    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="1050" class="rpt_table" id="list_view">
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
	                    <td width="100" style="word-break:break-all"><? echo $comp[$row['EXPORTER_ID']]; ?></td>
	                    <td width="80" title="<? echo $is_dtls; ?>" style="word-break:break-all">
						<?
						if ($is_dtls==1)
						{
							if($db_type==2 ) $row['JOB_ID'] = $row['JOB_ID']->load();	
							if($db_type==2 ) $row['JOB_NO'] = $row['JOB_NO']->load();	
							if($db_type==2 ) $booking_no = implode(', ',array_unique(explode(',',$row['BOOKING_NO']->load())));	
							$job_buyer = '';$job_suffix = '';
							$job_id_arr=array_unique(explode(',',$row['JOB_ID']));
							//print_r($job_id_arr);die;
							foreach($job_id_arr as $job_id)
							{
								if($row['WITHIN_GROUP']==2)
								{
									if($row['ITEM_CATEGORY_ID']==10 || $row['ITEM_CATEGORY_ID']==68 || $row['ITEM_CATEGORY_ID']==68)
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
								$order_no.=$sub_order_data[$job_id]["order_no"].",";
							}
							$job_buyer=chop($job_buyer, ',');
							$order_no=chop($order_no, ',');
							$job_suffix=chop($job_suffix, ',');
							echo implode(',',array_unique(explode(',',$job_buyer)));
						} else echo '';
						?></td>
	                    <td width="80" style="word-break:break-all"><? if ($is_dtls==1) echo implode(',',array_unique(explode(',',$row['JOB_NO']))); else echo ""; ?></td>
	                    <td width="80" style="word-break:break-all"><? if ($iten_cat_id==68 || $iten_cat_id==69) echo $order_no; else echo $booking_no;?></td>
                        <td width="50" align="center" style="word-break:break-all"><? if ($is_dtls==1) echo $job_suffix; else echo '' ?></td>  
	                    <td width="50" style="word-break:break-all"><? echo $yes_no[$row['WITHIN_GROUP']]; ?></td>
	                    <td width="60"><? if($row['LAST_SHIPMENT_DATE'] != '' && $row['LAST_SHIPMENT_DATE'] != '0000-00-00') echo change_date_format($row['LAST_SHIPMENT_DATE']); ?></td>
						<td width="60" style="word-break:break-all"><? echo $pi_revise_array[$row['PI_REVISE']]; ?></td>
						<td width="60" style="word-break:break-all"><? echo change_date_format($row['PI_REVISED_DATE']); ?></td>
						<td width="60" style="word-break:break-all"><? echo $pay_term[$row['PAY_TERM']]; ?></td>
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
	$data_array=sql_select("SELECT ID, ITEM_CATEGORY_ID, EXPORTER_ID, WITHIN_GROUP, BUYER_ID, PI_NUMBER, PI_DATE, LAST_SHIPMENT_DATE, PI_VALIDITY_DATE, CURRENCY_ID, HS_CODE, SWIFT_CODE, INTERNAL_FILE_NO, REMARKS,ATTENTION, ADVISING_BANK, TOTAL_AMOUNT, UPCHARGE, DISCOUNT, NET_TOTAL_AMOUNT, PI_REVISED_DATE, PI_REVISE, PAY_TERM, TENOR, YD_JOB,READY_TO_APPROVED, YD_JOB_ID, STATUS_ACTIVE,WEIGHT_APPROX,BENEFICIARY_BANK from com_export_pi_mst where id='$data' and status_active in(1,2,3) and is_deleted=0");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_item_category_id').value = '".$row["ITEM_CATEGORY_ID"]."';\n";  
		echo "document.getElementById('cbo_exporter_id').value = '".$row["EXPORTER_ID"]."';\n";  
		echo "document.getElementById('cbo_within_group').value = '".$row["WITHIN_GROUP"]."';\n";  
		echo "document.getElementById('cbo_approved').value = '".$row["READY_TO_APPROVED"]."';\n";  
		
		echo "load_drop_down('requires/export_pi_controller',".$row["EXPORTER_ID"]."+'_'+".$row["WITHIN_GROUP"].", 'load_drop_down_buyer', 'buyer_td' );\n";
		
		
		if($row["LAST_SHIPMENT_DATE"]=="0000-00-00" || $row["LAST_SHIPMENT_DATE"]=="") $last_shipment_date=""; else $last_shipment_date=change_date_format($row["LAST_SHIPMENT_DATE"]);
		if($row["PI_VALIDITY_DATE"]=="0000-00-00" || $row["PI_VALIDITY_DATE"]=="") $pi_validity_date=""; else $pi_validity_date=change_date_format($row["PI_VALIDITY_DATE"]);
		if($row["PI_REVISED_DATE"]=="0000-00-00" || $row["PI_REVISED_DATE"]=="") $pi_revised_date=""; else $pi_revised_date=change_date_format($row["PI_REVISED_DATE"]);
		echo "document.getElementById('cbo_buyer_name').value = '".$row["BUYER_ID"]."';\n";  
		echo "load_drop_down( 'requires/export_pi_controller', document.getElementById('cbo_buyer_name').value, 'load_drop_down_issue_bank', 'issue_bank_td' );\n";
		echo "document.getElementById('pi_number').value = '".$row["PI_NUMBER"]."';\n";  
		echo "document.getElementById('pi_date').value = '".change_date_format($row["PI_DATE"])."';\n";  
		echo "document.getElementById('last_shipment_date').value = '".$last_shipment_date."';\n";  
		echo "document.getElementById('pi_validity_date').value = '".$pi_validity_date."';\n";  
		echo "document.getElementById('cbo_currency_id').value = '".$row["CURRENCY_ID"]."';\n";  
		echo "document.getElementById('txt_issuing_bank').value = '" . $row[csf("beneficiary_bank")] . "';\n";
		echo "document.getElementById('hs_code').value = '".$row["HS_CODE"]."';\n";  
		echo "document.getElementById('txt_swift').value = '".$row["SWIFT_CODE"]."';\n";  
		echo "document.getElementById('txt_weight_approx').value = '".$row["WEIGHT_APPROX"]."';\n";  
		
		echo "document.getElementById('txt_total_amount').value = '".$row["TOTAL_AMOUNT"]."';\n";  
		echo "document.getElementById('txt_upcharge').value = '".($row["UPCHARGE"])."';\n";
		echo "document.getElementById('txt_discount').value = '".$row["DISCOUNT"]."';\n";
		echo "document.getElementById('txt_total_amount_net').value = '".$row["NET_TOTAL_AMOUNT"]."';\n";
		
		echo "document.getElementById('txt_internal_file_no').value = '".$row["INTERNAL_FILE_NO"]."';\n";  
		echo "document.getElementById('txt_remarks').value = '".($row["REMARKS"])."';\n";
		echo "document.getElementById('txt_attention').value = '".($row["ATTENTION"])."';\n";
		echo "document.getElementById('cbo_advising_bank').value = '".($row["ADVISING_BANK"])."';\n";
		echo "document.getElementById('cbo_pi_revise').value = '".($row["PI_REVISE"])."';\n";
		echo "document.getElementById('pi_revised_date').value = '".$pi_revised_date."';\n";
		echo "document.getElementById('cbo_pay_term').value = '".($row["PAY_TERM"])."';\n";
		echo "document.getElementById('txt_tenor').value = '".($row["TENOR"])."';\n";
		echo "document.getElementById('cbo_status').value = '".($row["STATUS_ACTIVE"])."';\n";
		echo "document.getElementById('txt_advance_job').value = '".($row["YD_JOB"])."';\n";
		echo "document.getElementById('txt_yd_job_id').value = '".($row["YD_JOB_ID"])."';\n";


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
	else if ($item_category_id==11) // Woven Fabric
	{
		$tblRow=0;
		$sql = "SELECT id, work_order_no,booking, work_order_id, work_order_dtls_id, determination_id, color_id, construction, composition, gsm, dia_width, uom, quantity, rate, amount,remarks from com_export_pi_dtls where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0";
		/*$prev_pi_qnty_arr_dtls=return_library_array("SELECT a.id, a.grey_qnty_by_uom-sum(b.quantity) as balance_qty  from fabric_sales_order_dtls a ,com_export_pi_dtls b where  a.id=work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.finish_qty ",'id','balance_qty');*/

		$prev_pi_qnty_sql=sql_select("SELECT a.id as ID, a.grey_qnty_by_uom as GREY_QNTY_BY_UOM, a.pp_qnty as PP_QNTY, a.mtl_qnty as MTL_QNTY, a.fpt_qnty as FPT_QNTY, a.gpt_qnty as GPT_QNTY, sum(b.quantity) as BALANCE_QTY  from fabric_sales_order_dtls a ,com_export_pi_dtls b,fabric_sales_order_mst c where  a.id=work_order_dtls_id and c.id=a.mst_id and c.entry_form = 547 and c.is_deleted = 0  and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.grey_qnty_by_uom,a.pp_qnty, a.mtl_qnty, a.fpt_qnty, a.gpt_qnty");
		$prev_pi_qnty_arr_dtls=array();
		
		foreach($prev_pi_qnty_sql as $row)
		{
			$prev_pi_qnty_arr_dtls[$row['ID']]=$row['GREY_QNTY_BY_UOM']+$row['PP_QNTY']+$row['MTL_QNTY']+$row['FPT_QNTY']+$row['GPT_QNTY']-$row['BALANCE_QTY'];
			
		}
		

		//print_r($prev_pi_qnty_arr_dtls);
		$data_array=sql_select($sql);
		$dtls_id_arr=array();
		foreach($data_array as $row)
		{
			$dtls_id_arr[$row[csf('work_order_dtls_id')]]=$row[csf('work_order_dtls_id')];
		}

		$wo_dtls_cond = where_con_using_array($dtls_id_arr,0,"id");
		$sql_wo = sql_select("select id,fabric_desc,cutable_width from fabric_sales_order_dtls where is_deleted = 0 $wo_dtls_cond");
		$wo_data = array();

		foreach($sql_wo as $row)
		{
			$wo_data[$row[csf('id')]]['des'] = $row[csf('fabric_desc')];
			$wo_data[$row[csf('id')]]['cutable_width'] = $row[csf('cutable_width')];
		}
		if(count($data_array) > 0)
		{
			foreach($data_array as $row)
			{ 
				$tblRow++;
				if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$bal_qtny=$prev_pi_qnty_arr_dtls[$row[csf('work_order_dtls_id')]]+$row[csf('quantity')];
				$cutable_width=$wo_data[$row[csf('work_order_dtls_id')]]['cutable_width'];
				$des=$wo_data[$row[csf('work_order_dtls_id')]]['des'];
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
						<input type="text" name="fabricdes_<? echo $tblRow; ?>" id="fabricdes_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $des; ?>" style="width:110px" disabled="disabled"/>
						<input type="hidden" name="hideDeterminationId_<? echo $tblRow; ?>" id="hideDeterminationId_<? echo $tblRow; ?>" value="<? echo $row[csf('determination_id')]; ?>" readonly />

						<input type="hidden" name="construction_<? echo $tblRow; ?>" id="construction_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('construction')]; ?>" style="width:110px" disabled="disabled"/>
						<input type="hidden" name="composition_<? echo $tblRow; ?>" id="composition_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('composition')]; ?>" style="width:110px" disabled="disabled"/>
					</td>
					 
		            
		            <td>
		                <input type="text" name="gsm_<? echo $tblRow; ?>" id="gsm_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('gsm')]; ?>" style="width:60px" disabled="disabled"/>
		            </td>
		            <td>
		                <input type="text" name="diawidth_<? echo $tblRow; ?>" id="diawidth_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('dia_width')]; ?>" style="width:70px" disabled="disabled"/>
		            </td>
		            <td>
		                <input type="text" name="cuttablewidth_<? echo $tblRow; ?>" id="cuttablewidth_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $cutable_width; ?>" style="width:70px" disabled="disabled"/>
		            </td>
		            <td>
		                <input type="text" name="colorName_<? echo $tblRow; ?>" id="colorName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:80px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
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
	                    <input type="text" name="fabricdes_1" id="fabricdes_1" class="text_boxes" style="width:110px" disabled="disabled"/>
	                    <input type="hidden" name="hideDeterminationId_1" id="hideDeterminationId_1" readonly />

	                    <input type="hidden" name="construction_1" id="construction_1" class="text_boxes"  style="width:110px" disabled="disabled"/>
						<input type="hidden" name="composition_1" id="composition_1" class="text_boxes" style="width:110px" disabled="disabled"/>
	                </td>
	               
	                <td>
	                    <input type="text" name="gsm_1" id="gsm_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
	                </td>
	                <td>
	                    <input type="text" name="diawidth_1" id="diawidth_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
	                </td>
	                 <td>
	                    <input type="text" name="cuttablewidth_1" id="cuttablewidth_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
	                </td>
	                <td>
	                    <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:80px" disabled="disabled"/>
	                    <input type="hidden" name="colorId_1" id="colorId_1"/>
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
		$sql = "SELECT id, work_order_no, work_order_id, work_order_dtls_id, booking, color_id, aop_color_id,main_process_id, embl_type, body_part, gsm, uom, quantity, rate, amount, remarks from com_export_pi_dtls where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0";

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
						<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $tblRow; ?>" readonly/>
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
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
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
					<td><input type="text" name="txtRemarks_<? echo $tblRow; ?>" id="txtRemarks_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('remarks')]; ?>" style="width:100px;" onKeyUp="copy_remarks_v2(<? echo $tblRow; ?>)" /></td>
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
	else if ($item_category_id==116) // service Garments
	{
		$tblRow=0;
		$sql = "SELECT b.id, b.work_order_no, b.work_order_id, b.work_order_dtls_id, b.gmts_item_id, b.color_id, b.item_desc, b.main_process_id, b.wash_type, b.gsm, b.booking, b.uom, b.quantity, b.rate, b.amount, c.buyer_style_ref, c.buyer_po_no  
		from com_export_pi_dtls b, subcon_ord_dtls c   
		where b.work_order_dtls_id=c.id and b.pi_id='$pi_id' and b.quantity>0 and b.status_active=1 and b.is_deleted=0";

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
					    <input type="text" name="gsmItem_<? echo $tblRow; ?>" id="gsmItem_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $garments_item[$row[csf('gmts_item_id')]]; ?>" style="width:110px; text-align: left;" disabled="disabled"/>
						<input type="hidden" name="hideGsmItem_<? echo $tblRow; ?>" id="hideGsmItem_<? echo $tblRow; ?>" value="<? echo $row[csf('gmts_item_id')]; ?>"/>
						<input type="hidden" name="hideProcessEmbl_<? echo $tblRow; ?>" id="hideProcessEmbl_<? echo $tblRow; ?>" value="<? echo $row['main_process_id']; ?>"/>
					</td>
					<td>
					   <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
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
						<input type="hidden" name="deleteIdDtls_<? echo $tblRow; ?>" id="deleteIdDtls_<? echo $tblRow; ?>" readonly value=""/>
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
						<input type="text" name="gsmItem_1" id="gsmItem_1" class="text_boxes" style="width:110px" disabled="disabled"/>
					</td>
                     <td>
                        <input type="text" name="colorId_1" id="colorId_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
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
		$sql = "SELECT a.id, a.work_order_no, a.work_order_id, a.work_order_dtls_id, a.hs_code, a.booking, a.gmts_item_id, a.item_desc, a.color_id, a.item_size, a.uom, a.quantity, a.rate, a.amount, b.size_name from com_export_pi_dtls a, subcon_ord_breakdown b where a.pi_id='$pi_id' and a.work_order_dtls_id=b.id and a.quantity>0 and a.status_active=1 and a.is_deleted=0";

		// echo "SELECT a.id, a.work_order_no, a.work_order_id, a.work_order_dtls_id, a.hs_code, a.booking, a.gmts_item_id, a.item_desc, a.color_id, a.item_size, a.uom, a.quantity, a.rate, a.amount from com_export_pi_dtls a, subcon_ord_breakdown b where a.pi_id='$pi_id' and a.work_order_dtls_id=b.id and a.quantity>0 and a.status_active=1 and a.is_deleted=0";

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
		            <!-- <td>
		                <input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? //echo $size_library[$row[csf('item_size')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideitemSize_<? echo $tblRow; ?>" id="hideitemSize_<? echo $tblRow; ?>" value="<? //echo $row[csf('item_size')]; ?>"/>
		            </td> -->
					<td>
		                <input type="text" name="itemSize_<? echo $tblRow; ?>" id="itemSize_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('size_name')]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="hideitemSize_<? echo $tblRow; ?>" id="hideitemSize_<? echo $tblRow; ?>" value="<? echo $row[csf('size_name')]; ?>"/>
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
	else if ($item_category_id==68 || $item_category_id==69) // Yarn Dyeing[Service], Yarn Dyeing[Sales]
	{
		$tblRow=0;

		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1", "id", "color_name" );
		$count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
		
		$sql="SELECT b.id, b.work_order_no, b.work_order_id, b.work_order_dtls_id, b.hs_code, b.buyer_job, b.cust_buyer, b.count_type_id, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.color_range_id, b.color_id, b.app_ref, b.uom, b.adj_type_id, b.total_order_qty, b.quantity, b.rate, b.amount from com_export_pi_dtls b where pi_id='$pi_id' and quantity>0 and status_active=1 and is_deleted=0";

		/*$prev_pi_qnty_arr_dtls=return_library_array("SELECT a.id, a.grey_qnty_by_uom-sum(b.quantity) as balance_qty  from fabric_sales_order_dtls a ,com_export_pi_dtls b where  a.id=work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id , a.finish_qty ",'id','balance_qty');*/

		$prev_pi_qnty_sql=sql_select("SELECT a.id as ID, sum(b.quantity) as BALANCE_QTY from yd_ord_dtls a, com_export_pi_dtls b where a.id=b.work_order_dtls_id and b.status_active=1 and a.status_active=1 and a.is_deleted=0 group by a.id");
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
						<input type="text" name="hscode_<? echo $tblRow; ?>" id="hscode_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? echo $row[csf('hs_code')]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="buyerJob_<? echo $tblRow; ?>" id="buyerJob_<? echo $tblRow; ?>" class="text_boxes" style="width:70px" disabled="disabled" value="<? echo $row[csf('buyer_job')]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="custBuyer_<? echo $tblRow; ?>" id="custBuyer_<? echo $tblRow; ?>" class="text_boxes" style="width:70px" disabled="disabled" value="<? echo $row[csf('cust_buyer')]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="countType_<? echo $tblRow; ?>" id="countType_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" disabled="disabled" value="<? echo $count_type_arr[$row[csf('count_type_id')]]; ?>"/>
						<input type="hidden" name="countTypeId_<? echo $tblRow; ?>" id="countTypeId_<? echo $tblRow; ?>" value="<? echo $row[csf('count_type_id')]; ?>" readonly />
                    </td>
					<td> 
						<input type="text" name="count_<? echo $tblRow; ?>" id="count_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? echo $count_arr[$row[csf('count_id')]]; ?>" disabled="disabled"/>
						<input type="hidden" name="hidecountId_<? echo $tblRow; ?>" id="hidecountId_<? echo $tblRow; ?>" value="<? echo $row[csf('count_id')]; ?>" readonly />
                    </td>
					<td> 
						<input type="text" name="yarnType_<? echo $tblRow; ?>" id="yarnType_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? echo $yarn_type[$row[csf('yarn_type_id')]]; ?>" disabled="disabled"/>
						<input type="hidden" name="yarnTypeId_<? echo $tblRow; ?>" id="yarnTypeId_<? echo $tblRow; ?>" value="<? echo $row[csf('yarn_type_id')]; ?>" readonly />
                    </td>
                    <td>
                        <input type="text" name="yarnComposition_<? echo $tblRow; ?>" id="yarnComposition_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $composition[$row[csf('yarn_composition_id')]]; ?>" style="width:100px" disabled="disabled"/>
						<input type="hidden" name="yarnCompositionId_<? echo $tblRow; ?>" id="yarnCompositionId_<? echo $tblRow; ?>" value="<? echo $row[csf('yarn_composition_id')]; ?>" readonly /> 
                    </td>
					<td>
                        <input type="text" name="colorRange_<? echo $tblRow; ?>" id="colorRange_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_range[$row[csf('color_range_id')]]; ?>" style="width:60px" disabled="disabled"/>
                        <input type="hidden" name="colorRangeId_<? echo $tblRow; ?>" id="colorRangeId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_range_id')]; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="colorName_<? echo $tblRow; ?>" id="colorName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:50px" disabled="disabled"/>
                        <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
                    </td>
					<td>
                        <input type="text" name="appRef_<? echo $tblRow; ?>" id="appRef_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('app_ref')]; ?>" style="width:50px" disabled="disabled"/>
                    </td>
					<td>
                        <? echo create_drop_down( "uom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$row[csf('uom')],"", 1,'','','','','','',"cboUom[]"); ?>
                    </td>

					<td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny ?>" style="width:61px;" />
					</td>

					<td>
                        <input type="text" name="adjType_<? echo $tblRow; ?>" id="adjType_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $adj_type_arr[$row[csf('adj_type_id')]]; ?>" style="width:50px" disabled="disabled"/>
                        <input type="hidden" name="adjTypeId_<? echo $tblRow; ?>" id="adjTypeId_<? echo $tblRow; ?>" value="<? echo $row[csf('adj_type_id')]; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="totalQty_<? echo $tblRow; ?>" id="totalQty_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('total_order_qty')]; ?>" style="width:60px" disabled="disabled"/>
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
					<input type="hidden" name="txtSerial[]" id="txtSerial_1" class="text_boxes" value="1" readonly/>
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
					<input type="text" name="buyerJob_1" id="buyerJob_1" class="text_boxes" style="width:70px" disabled="disabled"/>
				</td>
				<td> 
					<input type="text" name="custBuyer_1" id="custBuyer_1" class="text_boxes" style="width:70px" disabled="disabled"/>
				</td>
				<td> 
					<input type="text" name="countType_1" id="countType_1" class="text_boxes" style="width:50px" disabled="disabled"/>
					<input type="hidden" name="countTypeId_1" id="countTypeId_1" readonly />
				</td>
				<td> 
					<input type="text" name="count_1" id="count_1" class="text_boxes" style="width:50px" disabled="disabled"/>
					<input type="hidden" name="hidecountId_1" id="hidecountId_1" readonly />
				</td>
				<td> 
					<input type="text" name="yarnType_1" id="yarnType_1" class="text_boxes" style="width:50px" disabled="disabled"/>
					<input type="hidden" name="yarnTypeId_1" id="yarnTypeId_1" readonly />
				</td>
				<td>
					<input type="text" name="yarnComposition_1" id="yarnComposition_1" class="text_boxes" value="" style="width:100px" disabled="disabled"/>
					<input type="hidden" name="yarnCompositionId_1" id="yarnCompositionId_1" readonly /> 
				</td>
				<td>
					<input type="text" name="colorRange_1" id="colorRange_1" class="text_boxes" value="" style="width:60px" disabled="disabled"/>
					<input type="hidden" name="colorRangeId_1" id="colorRangeId_1"/>
				</td>
				<td>
					<input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:50px" disabled="disabled"/>
					<input type="hidden" name="colorId_1" id="colorId_1"/>
				</td>
				<td>
					<input type="text" name="appRef_1" id="appRef_1" class="text_boxes" value="" style="width:50px" disabled="disabled"/>
				</td>
				<td>
					<? echo create_drop_down( "uom_1", 60, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>
				</td>
				<td>
					<input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
				</td>
				<td>
					<input type="text" name="adjType_1" id="adjType_1" class="text_boxes" value="" style="width:50px" disabled="disabled"/>
					<input type="hidden" name="adjTypeId_1" id="adjTypeId_1"/>
				</td>
				<td>
					<input type="text" name="totalQty_1" id="totalQty_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
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
	else if ($item_category_id==2) // Woven Garments
	{
		$tblRow=0;

		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1", "id", "color_name" );
		$brand_name_arr = return_library_array("Select id, brand_name from  lib_buyer_brand where  status_active=1", 'id', 'brand_name');
		
		 $sql="SELECT b.id, b.work_order_no, b.work_order_id, b.work_order_dtls_id, b.hs_code, b.order_no,b.COMPOSITION, b.color_range_id, b.color_id, b.app_ref, b.uom, b.quantity, b.rate, b.amount, b.att_deta, b.att_deta, b.order_no, b.acc_po_no, b.buyer_style_ref, b.item_desc, b.gmts_item_id, b.brand_id from com_export_pi_dtls b where pi_id='$pi_id' and b.ATT_DETA=1 and quantity>0 and status_active=1 and is_deleted=0";
		
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
						<input type="text" name="Order_no_<? echo $tblRow; ?>" id="Order_no_<? echo $tblRow; ?>" class="text_boxes" style="width:70px" disabled="disabled" value="<? echo $row[csf('order_no')]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="AccPo_NO_<? echo $tblRow; ?>" id="AccPo_NO_<? echo $tblRow; ?>" class="text_boxes" style="width:70px" disabled="disabled" value="<? echo $row[csf('acc_po_no')]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="jobstyle_<? echo $tblRow; ?>" id="jobstyle_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" disabled="disabled" value="<? echo $row[csf('buyer_style_ref')]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="Style_Desc_<? echo $tblRow; ?>" id="Style_Desc_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? echo $row[csf('item_desc')]; ?>" disabled="disabled"/>
                    </td>
					<td> 
						<input type="text" name="GsmItem_<? echo $tblRow; ?>" id="GsmItem_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? echo $garments_item[$row[csf('gmts_item_id')]]; ?>" disabled="disabled"/>
						<input type="hidden" name="hideGsmItem_<? echo $tblRow; ?>" id="hideGsmItem_<? echo $tblRow; ?>" value="<? echo $row[csf('gmts_item_id')]; ?>" readonly />
                    </td>
                    <td>
                        <input type="text" name="composition_<? echo $tblRow; ?>" id="composition_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('composition')]; ?>" style="width:100px" />
                    </td>
					<td>
                        <input type="text" name="Brand_<? echo $tblRow; ?>" id="Brand_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $brand_name_arr[$row[csf('brand_id')]]; ?>" style="width:60px" disabled="disabled"/>
                        <input type="hidden" name="BrandId_<? echo $tblRow; ?>" id="BrandId_<? echo $tblRow; ?>" value="<? echo $row[csf('brand_id')]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="hscode_<? echo $tblRow; ?>" id="hscode_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? echo $row[csf('hs_code')]; ?>"/>
                    </td>
                    <td>
					   <? $status=array(1=>"Attach",2=>"Detach");
					   echo create_drop_down( "Status_".$tblRow, 60, $status,"", 1, "-- Select --",$row[csf('att_deta')],"", "",'','','','','','',"Status[]"); 
					   ?>
                    </td>
					<td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />

						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')];  ?>" style="width:61px;" />
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
						<input type="hidden" name="txtSerial[]" id="txtSerial_1" class="text_boxes" value="1" readonly/>
					</td>
					<td>
						<input type="text" name="workOrderNo_1" id="workOrderNo_1" class="text_boxes" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(1);" readonly />			
						<input type="hidden" name="hideWoId_1" id="hideWoId_1"  readonly />
						<input type="hidden" name="hideWoDtlsId_1" id="hideWoDtlsId_1"  readonly />
					</td>
					
					<td> 
						<input type="text" name="Order_no_1" id="Order_no_1" class="text_boxes" style="width:70px" disabled="disabled"/>
                    </td>
					<td> 
						<input type="text" name="AccPo_NO_1" id="AccPo_NO_1" class="text_boxes" style="width:70px" disabled="disabled"/>
                    </td>
					<td> 
						<input type="text" name="jobstyle_1" id="jobstyle_1" class="text_boxes" style="width:50px" disabled="disabled"/>
                    </td>
					<td> 
						<input type="text" name="Style_Desc_1" id="Style_Desc_1" class="text_boxes" style="width:50px" disabled="disabled"/>
                    </td>
					<td> 
						<input type="text" name="GsmItem_1" id="GsmItem_1" class="text_boxes" style="width:50px" disabled="disabled"/>
						<input type="hidden" name="hideGsmItem_1" id="hideGsmItem_1"  readonly />
                    </td>
                    <td>
                        <input type="text" name="composition_1" id="composition_1" class="text_boxes" style="width:100px" />
                    </td>
					<td>
                        <input type="text" name="Brand_1" id="Brand_1" class="text_boxes"  style="width:60px" disabled="disabled"/>
                        <input type="hidden" name="BrandId_1" id="BrandId_1"/>
                    </td>
					<td> 
						<input type="text" name="hscode_1" id="hscode_1" class="text_boxes" style="width:50px" />
                    </td>
                    <td>
					   <? $status=array(1=>"Attach",2=>"Detach");
					   echo create_drop_down( "Status_".$tblRow, 60, $status,"", 1, "-- Select --","","", "",'','','','','','',"Status[]"); 
					   ?>
                    </td>
					<td>
						<input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric"  style="width:61px;" onKeyUp="calculate_amount(1)" />
						<input type="hidden" name="hdnQuantity_1" id="hdnQuantity_1" class="text_boxes_numeric"  style="width:61px;" />
					</td>       
					<td>
						<input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric"  style="width:60px;" onKeyUp="calculate_amount(1)" />
					</td>
					<td>
						<input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric"  style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly />
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

	// echo $item_category_id;

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
        else if($item_category_id==11) // Woven Fabric
        {
        	?>
        	<thead>
            	<th width="40">&nbsp;</th>
				<th>Job No</th>
				<th>Sales/Booking</th>
                <th class="must_entry_caption">Fabric Description</th>
                <th>Weight</th>
                <th class="must_entry_caption">Full Width</th>
                <th class="must_entry_caption">Cutable Width</th>
                <th class="must_entry_caption">Color</th>
                <th>UOM</th>
                <th class="must_entry_caption">Quantity</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
				
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
                        <input type="text" name="fabricdes_1" id="fabricdes_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                        <input type="hidden" name="hideDeterminationId_1" id="hideDeterminationId_1" readonly />

                        <input type="hidden" name="construction_1" id="construction_1" class="text_boxes" style="width:110px" disabled="disabled"/>
						<input type="hidden" name="composition_1" id="composition_1" class="text_boxes" value="" style="width:160px" disabled="disabled"/>
                    </td>
                    
                    
                    <td>
                        <input type="text" name="gsm_1" id="gsm_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="diawidth_1" id="diawidth_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="cuttablewidth_1" id="cuttablewidth_1" class="text_boxes" value="" style="width:70px"  disabled="disabled"/>
                    </td>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:80px" disabled="disabled"/>
                        <input type="hidden" name="colorId_1" id="colorId_1"/>
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

		else if($item_category_id==116) // service Garments
        {
        	?>
        	<thead>
            	<th width="40">&nbsp;</th>
				<th>Job No</th>
                <th class="must_entry_caption">Description</th>
                <th class="must_entry_caption">Color</th>
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
                        <input type="text" name="gsmItem_1" id="gsmItem_1" class="text_boxes" style="width:110px" disabled="disabled"/>
                        <input type="hidden" name="hideGsmItem_1" id="hideGsmItem_1" readonly />
                        <input type="hidden" name="hideProcessEmbl_1" id="hideProcessEmbl_1" readonly />
                    </td>
                    <td>
                        <input type="text" name="itemColor_1" id="itemColor_1" class="text_boxes" value="" style="width:80px" disabled="disabled"/>
                        <input type="hidden" name="colorId_1" id="colorId_1"/>
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
                        <input type="hidden" name="deleteIdDtls_1" id="deleteIdDtls_1" readonly/>
                    </td>		
                </tr>	
            </tbody>
            <tfoot class="tbl_bottom">
                <tr>
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
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    </td>	
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
				<th width="100" title="Remarks copy when construction, composition, GSM are same">Remarks <input type="checkbox" checked id="copy_remarks_all" name="copy_remarks_all" /></th>
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
					<td><input type="text" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" value="" style="width:100px;" onKeyUp="copy_remarks_v2(1)" /></td>
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
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                    </td>
					<td>&nbsp;</td>
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

		else if($item_category_id==68 || $item_category_id==69) // Yarn Dyeing[Service],Yarn Dyeing[Sales]
        {
        	?>
        	<thead>
            	<th width="40">&nbsp;</th>
				<th>Job No</th>
				<th>H.S Code</th>
                <th>Buyer Job</th>
                <th>Cust. Buyer</th>
                <th>Count Type</th>
                <th>Count</th>
                <th>Yarn Type</th>
                <th>Yarn Composition</th>
				<th>Color Range</th>
				<th>Y/D Color</th>
				<th>App. Ref.</th>
				<th>UOM</th>
				<th class="must_entry_caption">Order Qty.</th>
				<th>Adj. Type</th>
				<th>Total Qty.</th>
                <th class="must_entry_caption">Rate</th>
                <th>Amount</th>
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
						<input type="text" name="hscode_1" id="hscode_1" class="text_boxes" style="width:50px"/>
                    </td>
					<td> 
						<input type="text" name="buyerJob_1" id="buyerJob_1" class="text_boxes" style="width:70px" disabled="disabled"/>
                    </td>
					<td> 
						<input type="text" name="custBuyer_1" id="custBuyer_1" class="text_boxes" style="width:70px" disabled="disabled"/>
                    </td>
					<td> 
						<input type="text" name="countType_1" id="countType_1" class="text_boxes" style="width:50px" disabled="disabled"/>
						<input type="hidden" name="countTypeId_1" id="countTypeId_1" readonly />
                    </td>
					<td> 
						<input type="text" name="count_1" id="count_1" class="text_boxes" style="width:50px" disabled="disabled"/>
						<input type="hidden" name="hidecountId_1" id="hidecountId_1" readonly />
                    </td>
					<td> 
						<input type="text" name="yarnType_1" id="yarnType_1" class="text_boxes" style="width:50px" disabled="disabled"/>
						<input type="hidden" name="yarnTypeId_1" id="yarnTypeId_1" readonly />
                    </td>
                    <td>
                        <input type="text" name="yarnComposition_1" id="yarnComposition_1" class="text_boxes" value="" style="width:100px" disabled="disabled"/>
						<input type="hidden" name="yarnCompositionId_1" id="yarnCompositionId_1" readonly /> 
                    </td>
					<td>
                        <input type="text" name="colorRange_1" id="colorRange_1" class="text_boxes" value="" style="width:60px" disabled="disabled"/>
                        <input type="hidden" name="colorRangeId_1" id="colorRangeId_1"/>
                    </td>
                    <td>
                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:50px" disabled="disabled"/>
                        <input type="hidden" name="colorId_1" id="colorId_1"/>
                    </td>
					<td>
                        <input type="text" name="appRef_1" id="appRef_1" class="text_boxes" value="" style="width:50px" disabled="disabled"/>
                    </td>
					<td>
                        <? echo create_drop_down( "uom_1", 60, $unit_of_measurement,'', 1, ' Display ','','',1,''); ?>
                    </td>
					<td>
                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                    </td>
					<td>
                        <input type="text" name="adjType_1" id="adjType_1" class="text_boxes" value="" style="width:50px" disabled="disabled"/>
                        <input type="hidden" name="adjTypeId_1" id="adjTypeId_1"/>
                    </td>
                    <td>
                        <input type="text" name="totalQty_1" id="totalQty_1" class="text_boxes_numeric" value="" style="width:60px" disabled="disabled"/>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total&nbsp;</td>
					<td style="text-align:center"><input type="text" name="txt_total_qnty" id="txt_total_qnty" class="text_boxes_numeric" value="" style="width:60px;" readonly/></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>                  
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
		else if($item_category_id==2) // Woven Garments
        {
        	?>
        	<thead>
            	<th width="40">&nbsp;</th>
				<th>Job No</th>			
                <th>Order Number</th>
                <th>Acc.PO No.</th>
                <th>Style Ref</th>
                <th>Style Desc.</th>
                <th>Item</th>
                <th>Composition</th>
				<th>Brand</th>
				<th>H.S Code</th>
				<th>Status</th>
				<th class="must_entry_caption">Order Qty.</th>
                <th class="must_entry_caption">Rate</th>
                <th>Order Value</th>
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
						<input type="text" name="Order_no_1" id="Order_no_1" class="text_boxes" style="width:70px" disabled="disabled"/>
                    </td>
					<td> 
						<input type="text" name="AccPo_NO_1" id="AccPo_NO_1" class="text_boxes" style="width:70px" disabled="disabled"/>
                    </td>
					<td> 
						<input type="text" name="jobstyle_1" id="jobstyle_1" class="text_boxes" style="width:50px" disabled="disabled"/> 
                    </td>
					<td> 
						<input type="text" name="Style_Desc_1" id="Style_Desc_1" class="text_boxes" style="width:50px" disabled="disabled"/>
                    </td>
					<td> 
						<input type="text" name="GsmItem_1" id="GsmItem_1" class="text_boxes" style="width:50px" disabled="disabled"/>
						<input type="hidden" name="hideGsmItem_1" id="hideGsmItem_1" readonly />
                    </td>
                    <td>
                        <input type="text" name="composition_1" id="composition_1" class="text_boxes" value="" style="width:100px" disabled="disabled"/>
                    </td>
					<td>
                        <input type="text" name="Brand_1" id="Brand_1" class="text_boxes" value="" style="width:60px" disabled="disabled"/>
                        <input type="hidden" name="BrandId_1" id="BrandId_1"/>
                    </td>
					<td> 
						<input type="text" name="hscode_1" id="hscode_1" class="text_boxes" style="width:50px"/>
                    </td>
                    <td>
						<? $status=array(1=>"Attach",2=>"Detach");
						echo create_drop_down( "Status_1", 60, $status,'', 1, ' Display ','','',1,''); 
						?>
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
                    <td>&nbsp;</td>             
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
		var currency_arr = new Array;
		var job_arr = new Array;
		var currency_check_arr = new Array;
		var item_category_id=<? echo str_replace("'","", $item_category_id); ?>
		
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
				if (item_category_id==68 || item_category_id==69)
				{ 
					var currencyId = $('#currencyId_' + str).val();
					var job_no = $('#jobNo_' + str).val();
					//alert(currency_arr+"="+currencyId);
					if(currency_arr.length==0)
					{
						currency_arr.push( currencyId );
					}
					else if( jQuery.inArray( currencyId, currency_arr )==-1 &&  currency_arr.length>0)
					{
						alert("Currency Mixed is Not Allowed");
						return;
					}

					/*if(job_arr.length==0)
					{
						job_arr.push( job_no );
					}
					else if( jQuery.inArray( job_no, job_arr )==-1 &&  job_arr.length>0)
					{
						alert("Job Mixed is Not Allowed");
						return;
					}*/
				}
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
					if (item_category_id==68 || item_category_id==69){ 
						job_arr.splice( i, 1 );
						currency_arr.splice( i, 1 );
					}
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
                        if(str_replace("'","", $item_category_id) != 67)
						{
                        	?>
	                    	<th>
								<?
								if (str_replace("'","", $item_category_id)==10 || str_replace("'","", $item_category_id)==20 || str_replace("'","", $item_category_id)==22) {echo "Customer/Buyer";}
								else if (str_replace("'","", $item_category_id)==68 || str_replace("'","", $item_category_id)==69) {echo "Party/Buyer";}
								else {echo "Buyer";}
								?>
							</th>
                        	<?
                        }
						if (str_replace("'","", $item_category_id) != 68 && str_replace("'","", $item_category_id) != 69)
						{
                        	?>
                        	<th>Year</th>
                        	<?
						}	
                        if(str_replace("'","", $item_category_id) != 67)
						{
							?>
							<th>
								<? 
								if (str_replace("'","", $item_category_id)==1 || str_replace("'","", $item_category_id)==10) { echo "Sales/Booking No."; }
								else if(str_replace("'","", $item_category_id)==37) { echo "Wash Job No."; }
								else if(str_replace("'","", $item_category_id)==68 || str_replace("'","", $item_category_id)==69) { echo "YD Job No."; }
								else { echo "WO No"; }
								?>
							</th>
							<?
							if (str_replace("'","", $item_category_id) != 68 && str_replace("'","", $item_category_id) != 69)
							{
                        		?>
                        		<th>Buyer Style</th>
                        		<?
							}
                        }
                        ?>
	                    <th>
							<? 
							if(str_replace("'","", $item_category_id)==1 || str_replace("'","", $item_category_id)==10) {echo "Sales Order";} 
							else if(str_replace("'","", $item_category_id)==37) { echo "Booking No"; }
							else if(str_replace("'","", $item_category_id)==68 || str_replace("'","", $item_category_id)==69) { echo "YD WO No"; }
							else {echo "Job";} ?>
						</th>
	                    <th>
							<? 
							if (str_replace("'","", $item_category_id)==1 || str_replace("'","", $item_category_id)==10){ echo "Sales/Booking Date Range"; } 
							else if(str_replace("'","", $item_category_id)==37) { echo "Wash Date Range"; }
							else if(str_replace("'","", $item_category_id)==68 || str_replace("'","", $item_category_id)==69) { echo "Ord. Rcvd. Date"; }
							else { echo "Booking Date Range"; } 
							?></th>
                        <?
                        if(str_replace("'","", $item_category_id) != 67)
						{
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
                        if(str_replace("'","", $item_category_id) != 67)
						{
                        	?>
							<td id="buyer_td"> 
								<? echo create_drop_down( "cbo_buyer_name", 151, $blank_array,"", 1, "-- Select Buyer --", 0, "",0 ); ?>
							</td>
                        	<?
                        }
						else
						{
                        	?>
                            <input type="hidden" name="cbo_buyer_name" id="cbo_buyer_name" value="">
                        	<?
                        }

						if (str_replace("'","", $item_category_id) != 68 && str_replace("'","", $item_category_id) != 69)
						{
                        	?>
							<td> 
								<?
								$cu_year=date("Y");
								echo create_drop_down( "cbo_year", 80, $year,"", 1, "-- All Year--", $cu_year, "",0 ); 
								?>
							</td>
	                    	<?
						}
						else
						{
							?>
							<input type="hidden" name="cbo_year" id="cbo_year" value="">
							<?
						}
                        if(str_replace("'","", $item_category_id) != 67)
						{
                        	?>
							<td> 
								<input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:100px">
							</td>
							<?
							if (str_replace("'","", $item_category_id) != 68 && str_replace("'","", $item_category_id) != 69)
							{
								?>
								<td> 
									<input type="text" name="txt_buyer_style" id="txt_buyer_style" class="text_boxes" style="width:100px">
								</td>
                        		<?
							}
							else
							{
								?>
								<input type="hidden" name="txt_buyer_style" id="txt_buyer_style" value="">
								<?
							}
                        }
						else
						{
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
                        if (str_replace("'", "", $item_category_id) != 67) 
						{
                        	?>
							<td> 
	                    	<?
                            if (str_replace("'", "", $item_category_id) == 35 || str_replace("'", "", $item_category_id) == 37 || str_replace("'", "", $item_category_id) == 68 || str_replace("'", "", $item_category_id) == 69) 
							{
                                $wo_based_on = array(1 => "Work Orde Wise");
                                echo create_drop_down("cbo_based_on", 80, $wo_based_on, "", 1, "Select", 1, "", 1);
                            } 
							else 
							{
                                $wo_based_on = array(1 => "Work Orde Wise", 2 => "Item Wise");
                                echo create_drop_down("cbo_based_on", 80, $wo_based_on, "", 1, "Select", 1, "");
                            }
							?>
	                    	</td>
                            <?
                        }
						else
						{
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
	// print_r($data);
	$within_group =$data[3];
	$buyer_id =$data[4];
	$company_id =$data[5];

	// echo $buyer_id."__";

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
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
	$location_arr = return_library_array("select id,location_name from lib_location", 'id', 'location_name');

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
	else if($item_category==116)
	{
		if($buyer_id != 0) $buyer_id_cond=" and a.party_id=$buyer_id";
		if (trim($data[6]) != '') $job_no=" and a.job_no_prefix_num like '%".trim($data[6])."'";
		// if (trim($data[6]) != '') $sales_order=" and a.order_no like '%".trim($data[6])."'";
		if ($buyer_style != '') $sales_order.=" and b.buyer_style_ref='".$buyer_style."'";
		if ($data[1] != '' &&  $data[2] != '')
		{
			if($db_type==0)
			{
				$wo_date_cond = "and b.order_rcv_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; 
			}
			else
			{
				$wo_date_cond = "and b.order_rcv_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; 
			}
		}

		if($db_type==0) $year_field="YEAR(a.insert_date) as year";
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		else $year_field="";//defined Later
		
		if($year)
		{
			if($db_type==0)
			{
				$cbo_year=" and YEAR(a.insert_date) =$year";
			}
			else
			{	
				$cbo_year=" and to_char(a.insert_date,'YYYY') =$year";
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
	else if($item_category==68 || $item_category==69) //Yarn Dyeing[Service], Yarn Dyeing[Salse]
	{
		$order_receive_date_cond=$yd_job_number_cond=$wo_order_cond="";
		if($buyer_id != 0) $buyer_id_cond=" and a.party_id=$buyer_id";	
		if (trim($data[0]) != '') $yd_job_number_cond=" and a.yd_job like '%".trim($data[0])."'";
		if (trim($data[6]) != '') $wo_order_cond=" and a.order_no like '%".trim($data[6])."'";
		if ($data[1] != '' &&  $data[2] != '')
		{
			if($db_type==0)
			{
				$order_receive_date_cond = "and a.receive_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; 
			}
			else
			{
				$order_receive_date_cond = "and a.receive_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; 
			}
		}
	}
	else if($item_category==2) //Woven Garments
	{
		$cond="";
		if($company_id != 0) $company_id=" and a.COMPANY_NAME=$company_id";	
		if($buyer_id != 0) $buyer_ids=" and a.BUYER_NAME=$buyer_id";	
		if (trim($data[6]) != '') $cond=" and a.job_no like '%".trim($data[6])."'";
		if (trim($data[12]) != '') $cond.=" and a.job_no like '%".trim($data[12])."'";
		if ($data[1] != '' &&  $data[2] != '')
		{
			$cond.= "and b.SHIPMENT_DATE between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'"; 		
		}
		
		if($year)
		{
			if($db_type==0)
			{
				$cond.=" and YEAR(a.INSERT_DATE) =$year";
			}
			else
			{	
				$cond.=" and to_char(a.INSERT_DATE,'YYYY') =$year";
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
		$sql = "SELECT a.id as mst_id, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, b.id as dtls_id, b.fabric_desc, b.gsm_weight, b.dia, b.color_id, b.grey_qnty_by_uom, b.pp_qnty, b.mtl_qnty, b.fpt_qnty, b.gpt_qnty, b.cons_uom from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without and a.fso_status=1 order by a.id";
		// echo $sql;die;
		$prev_pi_qnty_arr=return_library_array("SELECT b.work_order_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.item_category_id in (1,10) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 group by b.work_order_id",'work_order_id','quantity');

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT b.work_order_dtls_id, sum(b.quantity) as quantity from com_export_pi_mst a,com_export_pi_dtls b where a.id=b.pi_id and a.item_category_id in (1,10) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_dtls_id",'work_order_dtls_id','quantity');
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
	else if($item_category==11)
	{
		$sql = "SELECT a.id as mst_id, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, b.id as dtls_id, b.fabric_desc, b.gsm_weight, b.dia, b.color_id, b.grey_qnty_by_uom, b.pp_qnty, b.mtl_qnty, b.fpt_qnty, b.gpt_qnty, b.cons_uom from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form = 547 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without and a.fso_status=1 order by a.id";
		// echo $sql;die;
		$prev_pi_qnty_arr=return_library_array("SELECT b.work_order_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.item_category_id in (11) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 group by b.work_order_id",'work_order_id','quantity');

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT b.work_order_dtls_id, sum(b.quantity) as quantity from com_export_pi_mst a,com_export_pi_dtls b where a.id=b.pi_id and a.item_category_id in (11) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_dtls_id",'work_order_dtls_id','quantity');
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
		$prev_pi_qnty_arr=return_library_array("SELECT b.work_order_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category group by b.work_order_id",'work_order_id','quantity');

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
			

			$sql_prev_pi=sql_select("SELECT b.WORK_ORDER_ID, b.QUANTITY, b.AMOUNT from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category");
			$prev_data=array();
			foreach($sql_prev_pi as $row)
			{
				$prev_data[$row['WORK_ORDER_ID']]['QUANTITY']+=$row['QUANTITY'];
				$prev_data[$row['WORK_ORDER_ID']]['AMOUNT']+=$row['AMOUNT'];
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
		if($selected_based_on==1)
		{
			if($within_group == 1){
				$sql = "SELECT a.id as mst_id, b.job_no_mst, a.within_group, a.order_no as sales_booking_no, a.party_id as buyer_id, b.order_uom, c.booking_date, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as dtls_id, 0 as gmts_color_id, null as construction, null as composition, 0 as gsm, null as grey_dia, sum(b.order_quantity) as order_quantity, sum(b.amount)/sum(b.order_quantity) as rate, sum(b.amount) as amount
				from subcon_ord_mst a, subcon_ord_dtls b, wo_booking_mst c
				where a.id=b.mst_id and c.id=b.order_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
				group by a.id, b.job_no_mst, a.within_group, a.order_no, a.party_id, b.order_uom, c.booking_date
				order by a.id";
			}else{
				$sql = "SELECT a.id as mst_id, b.job_no_mst, a.within_group, a.order_no as sales_booking_no, a.party_id as buyer_id, b.order_uom, '' as booking_date, listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as dtls_id, 0 as gmts_color_id, null as construction, null as composition, 0 as gsm, null as grey_dia, sum(b.order_quantity) as order_quantity, sum(b.amount)/sum(b.order_quantity) as rate, sum(b.amount) as amount
				from subcon_ord_mst a, subcon_ord_dtls b
				where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 $wo_number $wo_date_cond $cbo_year $within_group_cond $buyer_id_cond $sales_order $wo_without
				group by a.id, b.job_no_mst, a.within_group, a.order_no, a.party_id, b.order_uom
				order by a.id";
			}
			
			$prev_pi_qnty_arr=return_library_array("SELECT b.work_order_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category group by b.work_order_id",'work_order_id','quantity');
		}
		else
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
			$prev_pi_qnty_arr=return_library_array("SELECT b.work_order_dtls_id, b.quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category",'work_order_dtls_id','quantity');
		}
        //echo $sql; die;

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
					$bal_qtny=0; 
					if($selected_based_on==1)
					{
						$bal_qtny=$row[csf('order_quantity')]-$prev_pi_qnty_arr[$row[csf('mst_id')]];
					}
					else
					{
						$bal_qtny=$row[csf('order_quantity')]-$prev_pi_qnty_arr[$row[csf('dtls_id')]];
					}

					if($bal_qtny>0)
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
                            
                            <td width="70" align="right"><? echo number_format($bal_qtny,2,".",""); ?>&nbsp;</td>
							<td width="60" align="right"><? echo number_format($row[csf('rate')],4,".",""); ?>&nbsp;</td>
							<td align="right"><? echo number_format($row[csf('amount')],4,".",""); ?>&nbsp;</td>
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
				// $prev_data=return_library_array("SELECT b.work_order_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category group by b.work_order_id",'work_order_id','quantity');
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
				$sql_prev_pi=sql_select("SELECT b.work_order_id, b.quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category");
			}
			//echo $sql;die;

			$prev_data_sql="SELECT b.work_order_id, sum(b.quantity) as quantity,b.gmts_item_id from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category group by b.work_order_id,b.gmts_item_id";
			$prv_result_sql = sql_select($prev_data_sql);
			foreach($prv_result_sql as $row)
			{
				$prev_data[$row[csf("work_order_id")]][$row[csf("gmts_item_id")]]["quantity"]+=$row[csf("quantity")];
				$prev_data[$row[csf("work_order_id")]][$row[csf("gmts_item_id")]]["amount"]+=$row[csf("amount")];
			}
		}
		
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

						$bal_qtny=$row[csf('quantity')]-$prev_data[$row[csf('mst_id')]][$row[csf('gmts_item_id')]]["quantity"]-$curr_data[$row[csf('mst_id')]]['quantity'];
						// echo $row[csf('mst_id')]."</br>";
						$amount=$row[csf('amount')]-$prev_data[$row[csf('mst_id')]][$row[csf('gmts_item_id')]]["amount"]-$curr_data[$row[csf('mst_id')]]['amount'];
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

	else if($item_category==116) // Service Garments
	{
		if($selected_based_on==1)
		{
			if($within_group==2)
			{		
				$sql = "SELECT a.id AS mst_id, a.job_no_prefix_num, a.order_no as booking_no_prefix_num, b.job_no_mst, a.within_group, a.order_no AS sales_booking_no , rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') AS dtls_id, sum(b.order_quantity) as quantity, sum(b.amount) as amount, b.buyer_style_ref, b.order_uom, a.gmts_type, max(b.gmts_item_id) as gmts_item_id,a.location_id,b.order_rcv_date,a.party_id
				FROM subcon_ord_mst a, subcon_ord_dtls b
				WHERE a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=238 $job_no $wo_date_cond $cbo_year  $buyer_id_cond  $wo_without
				GROUP by a.id, b.job_no_mst, a.job_no_prefix_num, a.within_group, a.order_no,b.buyer_style_ref, b.order_uom, a.gmts_type,a.location_id,b.order_rcv_date,a.party_id
				ORDER by a.id desc";//die;
			}
			// echo $sql;die;
			$sql_prev_pi=sql_select("SELECT b.work_order_id, b.quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category");
			$prev_data=array();
			foreach($sql_prev_pi as $row)
			{
				$prev_data[$row[csf("work_order_id")]]["quantity"]+=$row[csf("quantity")];
				$prev_data[$row[csf("work_order_id")]]["amount"]+=$row[csf("amount")];
			}
		}

		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table">
			<thead>
				<tr>
					<th colspan="7"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="110">Job No</th>
					<th width="50">Location</th>
					<th width="100">Order Receive Date</th>
					<th width="50">Buyer</th>
					<th width="100">Uom</th>
					<th width="90">Buyer Style</th>
				</tr>
			</thead>
		</table>
		<div style="width:780px; max-height:250px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">
				<? 
				$i=1; $job_id_arr =array(); $without_order_arr =array();
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($db_type==2) $row[csf('dtls_id')] = $row[csf('dtls_id')]->load();
			
						$bal_qtny=$row[csf('quantity')]-$prev_data[$row[csf('mst_id')]]["quantity"];
						// $amount=$row[csf('amount')]-$prev_data[$row[csf('mst_id')]]["amount"];
						// if($amount>0 && $bal_qtny) $rate=$amount/$bal_qtny; else $rate=0;

					if($bal_qtny>0){
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
								<td width="30" align="center"><? echo $i; ?>
									<input type="hidden" name="txt_wo_id_dtls" id="txt_wo_id_dtls<? echo $i ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
									<input type="hidden" name="txt_wo_id" id="txt_wo_id<? echo $i ?>" value="<? echo $row[csf('mst_id')]; ?>"/>	
								</td>	
								<td width="110"><p><? echo $row[csf('job_no_mst')];?></p></td>
								<td width="50" align="right"><p><? echo $location_arr[$row[csf('location_id')]]; ?>&nbsp;</p></td>
								<td width="100"><p><? echo $row[csf('order_rcv_date')];?></p></td>
								<td width="50" align="center"><p><? echo $buyer_arr[$row[csf('party_id')]]; ?>&nbsp;</p></td>						
								<td width="100" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>&nbsp;</p></td>						
								<td width="90" align="center"><p><? echo $row[csf('buyer_style_ref')]; ?>&nbsp;</p></td>						
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
			
			$sql_prev_pi=sql_select("SELECT b.work_order_id, b.quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category");
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
			
			$sql_prev_pi=sql_select("SELECT b.work_order_id, b.quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category");
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
			
			$sql_prev_pi=sql_select("SELECT b.work_order_id, b.gmts_item_id, b.quantity, b.amount from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category");
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
		$sql_prev_pi=sql_select("SELECT b.work_order_id, b.quantity, b.amount from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category");
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

	else if($item_category==68 || $item_category==69 ) //Yarn Dyeing[Service], Yarn Dyeing[Salse]
	{
		if ($item_category==68)
		{
			$sql="SELECT a.id as mst_id, a.job_no_prefix_num, a.party_id, a.yd_job, a.order_no as wo_no, a.order_type, a.receive_date as order_receive_date, a.check_box_confirm, a.company_id,  a.currency_id, a.within_group, b.id as dtls_id, b.order_quantity as order_qty from yd_ord_mst a, yd_ord_dtls b where a.id=b.mst_id and a.order_type=1 and a.check_box_confirm=1 and a.entry_form=374 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $within_group_cond $order_receive_date_cond $buyer_id_cond $yd_job_number_cond $wo_order_cond order by a.id desc";
		}
		else
		{
			$sql="SELECT a.id as mst_id, a.job_no_prefix_num, a.party_id, a.yd_job, a.order_no as wo_no, a.order_type, a.check_box_confirm, a.receive_date as order_receive_date, a.company_id, a.currency_id, a.within_group, b.id as dtls_id, b.order_quantity as order_qty from yd_ord_mst a, yd_ord_dtls b where a.id=b.mst_id and a.order_type=2 and a.entry_form=374 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $within_group_cond $order_receive_date_cond $buyer_id_cond $yd_job_number_cond $wo_order_cond order by a.id desc";
		}
		//echo $sql;
		$sql_res=sql_select($sql);
		$nameArray=array();
		foreach ($sql_res as $row)
		{
			$nameArray[$row[csf('mst_id')]]['mst_id']=$row[csf('mst_id')];
			$nameArray[$row[csf('mst_id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
			$nameArray[$row[csf('mst_id')]]['party_id']=$row[csf('party_id')];
			$nameArray[$row[csf('mst_id')]]['yd_job']=$row[csf('yd_job')];
			$nameArray[$row[csf('mst_id')]]['wo_no']=$row[csf('wo_no')];
			$nameArray[$row[csf('mst_id')]]['within_group']=$row[csf('within_group')];
			$nameArray[$row[csf('mst_id')]]['currency_id']=$row[csf('currency_id')];
			$nameArray[$row[csf('mst_id')]]['order_type']=$row[csf('order_type')];
			$nameArray[$row[csf('mst_id')]]['check_box_confirm']=$row[csf('check_box_confirm')];
			$nameArray[$row[csf('mst_id')]]['order_receive_date']=$row[csf('order_receive_date')];
			$nameArray[$row[csf('mst_id')]]['dtls_id'].=$row[csf('dtls_id')].',';
			$nameArray[$row[csf('mst_id')]]['order_qty']+=$row[csf('order_qty')];
		}
		//echo '<pre>';print_r($nameArray);
		//echo $sql;die;
		$prev_pi_qnty_arr=return_library_array("SELECT b.work_order_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category group by b.work_order_id",'work_order_id','quantity');

		//$prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');
		?>
		<div style="width:900px;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
			<thead>
				<tr>
					<th colspan="9"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="120">Party/Buyer</th>
					<th width="120">YD Job No</th>
					<th width="70">Job Suffix</th>
					<th width="120">YD WO No</th>
					<th width="100">Order Status</th>
					<th width="100">Order Type</th>					
					<th width="100">Ord. Rcvd. Date</th>
					<th>Currency</th>
				</tr>
			</thead>
		</table>
		<div style="width:900px; max-height:250px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="tbl_list_search">
				<? 
				$i=1; $job_id_arr =array(); $without_order_arr =array();
				//$nameArray=sql_select( $sql );
				foreach ($nameArray as $mst_id=>$row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					if($row['within_group']==2) $buyer=$buyer_arr[$row['party_id']];
					else $buyer=$company_arr[$row['party_id']];

					if ($row['check_box_confirm'] == 1) $order_status="Confirm";
					else $order_status="Advance";
					
					$bal_qtny=0; $is_loop=1;
					$bal_qtny=$row['order_qty']-$prev_pi_qnty_arr[$row['mst_id']];
					//echo $bal_qtny."**";

					if($bal_qtny>0)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
			                <td width="40" align="center"><? echo $i; ?>
			                    <input type="hidden" name="txt_wo_id_dtls" id="txt_wo_id_dtls<? echo $i ?>" value="<? echo rtrim($row['dtls_id'],','); ?>"/>
			                    <input type="hidden" name="txt_wo_id" id="txt_wo_id<? echo $i ?>" value="<? echo $row['mst_id']; ?>"/>
								<input type="hidden" name="currencyId[]" id="currencyId_<? echo $i; ?>" value="<? echo $row['currency_id']; ?>"/>
								<input type="hidden" name="jobNo[]" id="jobNo_<? echo $i; ?>" value="<? echo $row['yd_job']; ?>"/>
			                </td>	
							<td width="120"><p><? echo $buyer; ?></p></td>
			                <td width="120"><p><? echo $row['yd_job']; ?></p></td>
							<td width="70" align="center"><p><? echo $row['job_no_prefix_num']; ?></p></td>
							<td width="120"><p><? echo $row['wo_no']; ?></p></td>
							<td align="center" width="100"><p><? echo $order_status; ?></p></td>
							<td align="center" width="100"><p><? echo $w_order_type_arr[$row['order_type']]; ?></p></td>							
							<td align="right"><p><? echo change_date_format($row['order_receive_date']); ?></p></td>							
							<td align="right"><p><? echo $currency[$row['currency_id']]; ?></p></td>
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
	else if($item_category==2) // Woven Garments
	{
	
		// $sql="SELECT a.id as mst_id, a.job_no_prefix_num, a.party_id, a.yd_job, a.order_no as wo_no, a.order_type, a.check_box_confirm, a.receive_date as order_receive_date, a.company_id, a.currency_id, a.within_group, b.id as dtls_id, b.order_quantity as order_qty from yd_ord_mst a, yd_ord_dtls b where a.id=b.mst_id and a.order_type=2 and a.entry_form=374 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $within_group_cond $order_receive_date_cond $buyer_id_cond $yd_job_number_cond $wo_order_cond order by a.id desc";
		$lib_buyer_arr_data=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');


		 $sql="SELECT a.id as job_id, a.BUYER_NAME, a.JOB_NO, a.JOB_NO_PREFIX_NUM, a.STYLE_REF_NO, b.PO_NUMBER, b.SHIPMENT_DATE, b.id as PO_ID, sum(b.po_quantity) as PO_QUANTITY  from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and a.garments_nature=3 and b.status_active=1 and b.is_deleted=0 $company_id $buyer_ids $cond group by a.id, a.BUYER_NAME, a.JOB_NO, a.JOB_NO_PREFIX_NUM, a.STYLE_REF_NO, b.PO_NUMBER, b.SHIPMENT_DATE, b.id order by a.id desc";
		
		// echo $sql;
		$sql_res=sql_select($sql);
		$prev_pi_qnty_arr=return_library_array("SELECT b.work_order_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category group by b.work_order_id",'work_order_id','quantity');
		
		?>
		<div style="width:900px;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table">
			<thead>
				<tr>
					<th colspan="9"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="40">SL</th>
					<th width="120">Buyer</th>
					<th width="120">Job No</th>
					<th width="70">Job Suffix</th>
					<th width="120">Po No</th>
					<th width="100">Style Ref</th>
					<th width="100">Shipment Date</th>					
				</tr>
			</thead>
		</table>
		<div style="width:900px; max-height:250px; overflow-y:scroll">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="880" class="rpt_table" id="tbl_list_search">
				<? 
				$i=1; $job_id_arr =array(); $without_order_arr =array();
				//$nameArray=sql_select( $sql );
				foreach ($sql_res as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$bal_qtny=$row['PO_QUANTITY']-$prev_pi_qnty_arr[$row['JOB_ID']];

					if($bal_qtny>0){
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
							<td width="40" align="center"><? echo $i; ?>
								<input type="hidden" name="txt_wo_id_dtls" id="txt_wo_id_dtls<? echo $i ?>" value="<? echo $row['PO_ID']; ?>"/>
								<input type="hidden" name="jobNo[]" id="jobNo_<? echo $i; ?>" value="<? echo $row['JOB_NO']; ?>"/>
							</td>	
							<td width="120"><p><? echo $lib_buyer_arr_data[$row['BUYER_NAME']]; ?></p></td>
							<td width="120"><p><? echo $row['JOB_NO']; ?></p></td>
							<td width="70" align="center"><p><? echo $row['JOB_NO_PREFIX_NUM']; ?></p></td>
							<td width="120"><p><? echo $row['PO_NUMBER']; ?></p></td>
							<td align="center" width="100"><p><? echo $row["STYLE_REF_NO"]; ?></p></td>
							<td align="center" width="100"><p><? echo $row["SHIPMENT_DATE"]; ?></p></td>							
						</tr>
						<?
						$i++;
					}
					
				}
				?>
			</table>
		</div>
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
		
		$sql = "SELECT a.id, a.job_no,a.sales_booking_no, b.cons_uom, b.id as dtls_id, b.determination_id, b.gsm_weight, b.dia, b.color_id, b.grey_qnty_by_uom  as qty, b.pp_qnty, b.mtl_qnty, b.fpt_qnty, b.gpt_qnty, b.avg_rate, b.amount,b.pre_cost_remarks from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id $cond_mst_dls and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";

		// $prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');
		$prev_pi_qnty_arr_dtls=return_library_array("SELECT b.work_order_dtls_id, sum(b.quantity) as quantity from com_export_pi_mst a,com_export_pi_dtls b where a.id=b.pi_id and a.item_category_id in (1,10) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_dtls_id",'work_order_dtls_id','quantity');
		
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
								<!-- <td><input type="text" name="txtRemarks_<? echo $tblRow; ?>" id="txtRemarks_<?// echo $tblRow; ?>" class="text_boxes" value="" style="width:100px;" onKeyUp="copy_remarks(<?// echo $tblRow; ?>)" /></td> -->
								<td><input type="text" name="txtRemarks_<? echo $tblRow; ?>" id="txtRemarks_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('pre_cost_remarks')]; ?>" style="width:100px;" /></td>
							<?
						}
					?>
				</tr>
				<?
			}		
		}
	}
	else if($item_category_id==11) // Knit Garments and Fabric
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
		
		$sql = "SELECT a.id, a.job_no,a.sales_booking_no, b.cons_uom, b.id as dtls_id, b.determination_id, b.gsm_weight, b.dia, b.color_id, b.grey_qnty_by_uom  as qty, b.pp_qnty, b.mtl_qnty, b.fpt_qnty, b.gpt_qnty, b.avg_rate, b.amount,b.fabric_desc,b.cutable_width from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id and a.entry_form = 547 $cond_mst_dls and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";

		// $prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');
		$prev_pi_qnty_arr_dtls=return_library_array("SELECT b.work_order_dtls_id, sum(b.quantity) as quantity from com_export_pi_mst a,com_export_pi_dtls b where a.id=b.pi_id and a.item_category_id in (11) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.work_order_dtls_id",'work_order_dtls_id','quantity');
		
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
						<input type="text" name="fabricdes_<? echo $tblRow; ?>" id="fabricdes_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('fabric_desc')]; ?>" style="width:110px" disabled="disabled"/>
						<input type="hidden" name="hideDeterminationId_<? echo $tblRow; ?>" id="hideDeterminationId_<? echo $tblRow; ?>" value="<? echo $row[csf('determination_id')]; ?>" readonly />

						<input type="hidden" name="construction_<? echo $tblRow; ?>" id="construction_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $construction_arr[$row[csf('determination_id')]]; ?>" style="width:110px" disabled="disabled"/>
						<input type="hidden" name="composition_<? echo $tblRow; ?>" id="composition_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $composition_arr[$row[csf('determination_id')]]; ?>" style="width:110px" disabled="disabled"/>
					</td>
					
		            
		            <td>
		                <input type="text" name="gsm_<? echo $tblRow; ?>" id="gsm_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('gsm_weight')]; ?>" style="width:60px" disabled="disabled"/>
		            </td>
		            <td>
		                <input type="text" name="diawidth_<? echo $tblRow; ?>" id="diawidth_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('dia')]; ?>" style="width:70px" disabled="disabled"/>
		            </td>
		            <td>
		                <input type="text" name="cuttablewidth_<? echo $tblRow; ?>" id="cuttablewidth_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('cutable_width')]; ?>" style="width:70px" disabled="disabled"/>
		            </td>
		            <td>
		                <input type="text" name="colorName_<? echo $tblRow; ?>" id="colorName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:80px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
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
		

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT b.work_order_dtls_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category_id group by work_order_dtls_id",'work_order_dtls_id','quantity');
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
		$sql_pi_qty="SELECT b.WORK_ORDER_DTLS_ID, b.COLOR_ID, b.QUANTITY from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category_id";
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
		if($based_on==2) $cond_mst_dls=" and b.id in($wo_dtls_id)"; else $cond_mst_dls=" and b.mst_id in ($wo_mst_id)";
		if($based_on==2) $cond_mst_dls2=" and b.WORK_ORDER_DTLS_ID in($wo_dtls_id)"; else $cond_mst_dls2=" and b.WORK_ORDER_ID in ($wo_mst_id)";
		
		$sql = "SELECT a.id, b.job_no_mst, a.within_group, a.order_no as sales_booking_no, a.party_id as buyer_id, b.id as dtls_id, b.gmts_color_id, b.aop_color_id, b.construction, b.composition, b.lib_yarn_deter, b.gsm, b.grey_dia, b.order_quantity, b.rate, b.amount, b.order_uom, b.body_part
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 $cond_mst_dls
		order by a.id";//die;
		//echo $sql;

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT b.work_order_dtls_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category_id $cond_mst_dls2 group by work_order_dtls_id",'work_order_dtls_id','quantity');

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
						<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $tblRow; ?>" readonly/>
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
					<td><input type="text" name="txtRemarks_<? echo $tblRow; ?>" id="txtRemarks_<? echo $tblRow; ?>" class="text_boxes" value="" style="width:100px;" onKeyUp="copy_remarks_v2(<? echo $tblRow; ?>)" /></td>
				</tr>
				<?
			}		
		}
	}

	else if($item_category_id==37) // Gmts Wash
	{
		if($based_on==2) $cond_mst_dls="and c.id in($wo_dtls_id)"; else $cond_mst_dls=" and b.mst_id in ($wo_mst_id)";
		 
		$sql = "SELECT a.id, b.job_no_mst, a.within_group, a.order_no as sales_booking_no, b.id as dtls_id, b.gmts_item_id, b.order_quantity, b.rate, b.amount, b.order_uom, b.gmts_color_id, b.buyer_style_ref
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=295 $cond_mst_dls
		order by a.id";

		//$prev_pi_qnty_arr_dtls=return_library_array("SELECT work_order_dtls_id, sum(quantity) as quantity from com_export_pi_dtls where status_active=1 and is_deleted=0 group by work_order_dtls_id",'work_order_dtls_id','quantity');

		$prev_pi_qnty_sql = "SELECT c.work_order_dtls_id, sum(c.quantity) as quantity 
		from subcon_ord_mst a, subcon_ord_dtls b, com_export_pi_dtls c, com_export_pi_mst d 
		where a.id=b.mst_id and b.id=c.work_order_dtls_id and a.id=c.work_order_id and c.pi_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=295 and d.item_category_id=$item_category_id $cond_mst_dls 
		group by c.work_order_dtls_id ";

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
	else if($item_category_id==116) // Service Garments
	{
		if($based_on==2) $cond_mst_dls="and c.id in($wo_dtls_id)"; else $cond_mst_dls=" and b.mst_id in ($wo_mst_id)";

		$sql = "SELECT a.id, b.job_no_mst, a.within_group, a.order_no as sales_booking_no, b.id as dtls_id, b.gmts_item_id, b.order_quantity, b.rate, b.amount, b.order_uom, b.gmts_color_id, b.buyer_style_ref
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=238 $cond_mst_dls
		order by a.id";

       //  echo $sql;die;


		$prev_pi_qnty_sql = "SELECT a.id as mst_id ,b.id as work_order_dtls_id, sum(c.qnty) as quantity,b.order_uom,c.color_id,a.subcon_job,c.rate, b.main_process_id ,c.item_id 
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c 
		where a.id=b.mst_id and b.id=c.order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=238 $cond_mst_dls group by a.id, b.id, b.order_uom, c.color_id, a.subcon_job, c.rate, b.main_process_id,c.item_id";

		$data_array=sql_select($prev_pi_qnty_sql);
		foreach($data_array as $row)
		{

			if($row[csf('main_process_id')]==2 || $row[csf('main_process_id')]==3 || $row[csf('main_process_id')]==4 || $row[csf('main_process_id')]==6)
			{
				$garments_item=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
			}
			else
			{
				$garments_item;
			}

			if($row[csf('within_group')]==2) $buyer=$buyer_arr[$row[csf('buyer_id')]];
			else $buyer=$company_arr[$row[csf('buyer_id')]];

				$tblRow++;
				if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('subcon_job')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('mst_id')]; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('work_order_dtls_id')]; ?>" readonly />
					</td>
		            <td> 
						<input type="text" name="gsmItem_<? echo $tblRow; ?>" id="gsmItem_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $garments_item[$row[csf('item_id')]]; ?>" style="width:110px; text-align: left;" disabled="disabled"/>
						<input type="hidden" name="hideGsmItem_<? echo $tblRow; ?>" id="hideGsmItem_<? echo $tblRow; ?>" value="<? echo $row[csf('item_id')]; ?>"/>
						<input type="hidden" name="hideProcessEmbl_<? echo $tblRow; ?>" id="hideProcessEmbl_<? echo $tblRow; ?>" value="<? echo $row['main_process_id']; ?>"/>
					</td>
					<td>
		                <input type="text" name="itemColor_<? echo $tblRow; ?>" id="itemColor_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:70px" disabled="disabled"/>
		                <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
		            </td>
		             <td>
		                <? echo create_drop_down("uom_".$tblRow, 70, $unit_of_measurement,'', 1, ' Display ',$row[csf('order_uom')],'',1,''); ?>						 
		            </td>
		            <td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]; ?>" style="width:61px;"/>
					</td>
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('rate')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('quantity')]*$row[csf('rate')]; ?>" style="width:75px;" readonly/>
						<input type="hidden" name="updateIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
						<input type="hidden" name="deleteIdDtls_<? echo $tblRow; ?>" id="updateIdDtls_<? echo $tblRow; ?>" readonly value=""/>
					</td>
				</tr>
				<?
				
		}
	}

	else if($item_category_id==36) // Gmts emb
	{
		if($based_on==2) $cond_mst_dls="and c.id in($wo_dtls_id)"; else $cond_mst_dls=" and b.mst_id in ($wo_mst_id)";
		
		$sql = "SELECT a.id, b.job_no_mst, a.within_group, a.order_no as sales_booking_no, c.id as dtls_id, b.gmts_item_id, b.main_process_id, b.embl_type, b.body_part, b.order_quantity, b.rate, b.amount, b.order_uom, c.description, c.color_id, c.size_id, c.qnty, c.amount, c.rate
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
		where a.id=b.mst_id and b.id=c.mst_id and b.job_no_mst=c.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.entry_form=311 $cond_mst_dls
		order by a.id";//die;

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT b.work_order_dtls_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category_id group by work_order_dtls_id",'work_order_dtls_id','quantity');

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

		
		$prev_pi_qnty_arr_dtls=return_library_array("SELECT b.work_order_dtls_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category_id group by work_order_dtls_id",'work_order_dtls_id','quantity');

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

        $prev_pi_qnty_arr_dtls=return_library_array("SELECT b.work_order_dtls_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category_id group by work_order_dtls_id",'work_order_dtls_id','quantity');

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

	if($item_category_id==68 || $item_category_id==69) // Yarn Dyeing[Service],Yarn Dyeing[Sales]
	{
		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1", "id", "color_name" );
		$count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
		//if($based_on==2) $cond_mst_dls="and b.id in($wo_dtls_id)"; else $cond_mst_dls=" and a.id in ($wo_mst_id)";
		$cond_mst_dls="and b.id in($wo_dtls_id)";
		if ($item_category_id==68)
		{
			$sql="SELECT a.id as mst_id, a.company_id, a.yd_job as job_no, b.id as dtls_id, b.sales_order_no as buyer_job, b.buyer_buyer as cust_buyer, b.count_type as count_type_id, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.item_color_id as color_range_id, b.yd_color_id as color_id, b.app_ref, b.uom, b.order_quantity as order_qty, b.adj_type as adj_type_id, b.total_order_quantity as total_order_qty, b.rate, b.amount from yd_ord_mst a, yd_ord_dtls b where a.id=b.mst_id and a.order_type=1 and a.check_box_confirm=1 and a.entry_form=374 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cond_mst_dls order by b.id";
		}
		else
		{
			$sql="SELECT a.id as mst_id, a.company_id, a.yd_job as job_no, b.id as dtls_id, b.sales_order_no as buyer_job, b.buyer_buyer as cust_buyer, b.count_type as count_type_id, b.count_id, b.yarn_type_id, b.yarn_composition_id, b.item_color_id as color_range_id, b.yd_color_id as color_id, b.app_ref, b.uom, b.order_quantity as order_qty, b.adj_type as adj_type_id, b.total_order_quantity as total_order_qty, b.rate, b.amount from yd_ord_mst a, yd_ord_dtls b where a.id=b.mst_id and a.order_type=2 and a.entry_form=374 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $cond_mst_dls order by b.id";
		}	
		
		$prev_pi_qnty_arr_dtls=return_library_array("SELECT b.work_order_dtls_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category_id group by work_order_dtls_id",'work_order_dtls_id','quantity');
		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{		
			
			if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$bal_qtny=$row[csf('order_qty')]-$prev_pi_qnty_arr_dtls[$row[csf('dtls_id')]];			
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
						<input type="text" name="hscode_<? echo $tblRow; ?>" id="hscode_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? //echo $row[csf('work_order_no')]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="buyerJob_<? echo $tblRow; ?>" id="buyerJob_<? echo $tblRow; ?>" class="text_boxes" style="width:70px" disabled="disabled" value="<? echo $row[csf('buyer_job')]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="custBuyer_<? echo $tblRow; ?>" id="custBuyer_<? echo $tblRow; ?>" class="text_boxes" style="width:70px" disabled="disabled" value="<? echo $row[csf('cust_buyer')]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="countType_<? echo $tblRow; ?>" id="countType_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" disabled="disabled" value="<? echo $count_type_arr[$row[csf('count_type_id')]]; ?>"/>
						<input type="hidden" name="countTypeId_<? echo $tblRow; ?>" id="countTypeId_<? echo $tblRow; ?>" value="<? echo $row[csf('count_type_id')]; ?>" readonly />
                    </td>
					<td> 
						<input type="text" name="count_<? echo $tblRow; ?>" id="count_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? echo $count_arr[$row[csf('count_id')]]; ?>" disabled="disabled"/>
						<input type="hidden" name="hidecountId_<? echo $tblRow; ?>" id="hidecountId_<? echo $tblRow; ?>" value="<? echo $row[csf('count_id')]; ?>" readonly />
                    </td>
					<td> 
						<input type="text" name="yarnType_<? echo $tblRow; ?>" id="yarnType_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? echo $yarn_type[$row[csf('yarn_type_id')]]; ?>" disabled="disabled"/>
						<input type="hidden" name="yarnTypeId_<? echo $tblRow; ?>" id="yarnTypeId_<? echo $tblRow; ?>" value="<? echo $row[csf('yarn_type_id')]; ?>" readonly />
                    </td>
                    <td>
                        <input type="text" name="yarnComposition_<? echo $tblRow; ?>" id="yarnComposition_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $composition[$row[csf('yarn_composition_id')]]; ?>" style="width:100px" disabled="disabled"/>
						<input type="hidden" name="yarnCompositionId_<? echo $tblRow; ?>" id="yarnCompositionId_<? echo $tblRow; ?>" value="<? echo $row[csf('yarn_composition_id')]; ?>" readonly /> 
                    </td>
					<td>
                        <input type="text" name="colorRange_<? echo $tblRow; ?>" id="colorRange_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_range[$row[csf('color_range_id')]]; ?>" style="width:60px" disabled="disabled"/>
                        <input type="hidden" name="colorRangeId_<? echo $tblRow; ?>" id="colorRangeId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_range_id')]; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="colorName_<? echo $tblRow; ?>" id="colorName_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $color_library[$row[csf('color_id')]]; ?>" style="width:50px" disabled="disabled"/>
                        <input type="hidden" name="colorId_<? echo $tblRow; ?>" id="colorId_<? echo $tblRow; ?>" value="<? echo $row[csf('color_id')]; ?>"/>
                    </td>
					<td>
                        <input type="text" name="appRef_<? echo $tblRow; ?>" id="appRef_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('app_ref')]; ?>" style="width:50px" disabled="disabled"/>
                    </td>
					<td>
                        <? echo create_drop_down( "uom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --",$row[csf('uom')],"", 1,'','','','','','',"cboUom[]"); ?>
                    </td>

					<td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; //echo $row[csf('quantity')]; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny ?>" style="width:61px;" />
					</td>

					<td>
                        <input type="text" name="adjType_<? echo $tblRow; ?>" id="adjType_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $adj_type_arr[$row[csf('adj_type_id')]]; ?>" style="width:50px" disabled="disabled"/>
                        <input type="hidden" name="adjTypeId_<? echo $tblRow; ?>" id="adjTypeId_<? echo $tblRow; ?>" value="<? echo $row[csf('adj_type_id')]; ?>"/>
                    </td>
                    <td>
                        <input type="text" name="totalQty_<? echo $tblRow; ?>" id="totalQty_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('total_order_qty')]; ?>" style="width:60px" disabled="disabled"/>
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
	if($item_category_id==2) // Woven Garments
	{
		$color_library=return_library_array( "select id,color_name from lib_color where status_active=1", "id", "color_name" );
		$count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
		$brand_name_arr = return_library_array("Select id, brand_name from  lib_buyer_brand where  status_active=1", 'id', 'brand_name');

		//if($based_on==2) $cond_mst_dls="and b.id in($wo_dtls_id)"; else $cond_mst_dls=" and a.id in ($wo_mst_id)";
		//$cond_mst_dls="and a.id in($wo_dtls_id)";
		$cond_dls_id="and b.id in($wo_dtls_id)";

		$sql="SELECT a.id as job_id, a.BUYER_NAME, a.JOB_NO, a.JOB_NO_PREFIX_NUM, a.STYLE_REF_NO, a.STYLE_DESCRIPTION, b.PO_NUMBER, b.SHIPMENT_DATE, b.id as PO_ID, b.po_quantity, a.BRAND_ID, b.unit_price  from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and a.garments_nature=3 and b.status_active=1 and b.is_deleted=0 $cond_dls_id order by a.id desc";

		$PrevData=sql_select("select PO_BREAK_DOWN_ID,ITEM_NUMBER_ID from wo_po_color_size_breakdown where JOB_ID in($wo_dtls_id) and STATUS_ACTIVE=1");
		$item_id_arr=array();
		foreach($PrevData as $row){
			$item_id_arr[$row["PO_BREAK_DOWN_ID"]]["ITEM_NUMBER_ID"]=$row["ITEM_NUMBER_ID"];
		}
		$acc_po=sql_select("select PO_BREAK_DOWN_ID,ACC_PO_NO from wo_po_acc_po_info where JOB_ID in($wo_dtls_id) and STATUS_ACTIVE=1");
		$acc_po_arr=array();
		foreach($acc_po as $row){
			$acc_po_arr[$row["PO_BREAK_DOWN_ID"]]["ACC_PO_NO"]=$row["ACC_PO_NO"];
		}

		$prev_pi_qnty_arr_dtls=return_library_array("SELECT b.work_order_dtls_id, sum(b.quantity) as quantity from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category_id=$item_category_id group by work_order_dtls_id",'work_order_dtls_id','quantity');

		$data_array=sql_select($sql);
		foreach($data_array as $row)
		{	$tblRow++;			
			if($tblRow%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$bal_qtny=$row[csf('po_quantity')]-$prev_pi_qnty_arr_dtls[$row[csf('po_id')]];		
			if($bal_qtny>0){
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $tblRow; ?>" align="center">
		            <td>
						<input type="checkbox" name="workOrderChkbox_<? echo $tblRow; ?>" id="workOrderChkbox_<? echo $tblRow; ?>" value="" />
						<input type="hidden" name="txtSerial[]" id="txtSerial_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $tblRow; ?>" readonly/>
					</td>
					<td>
						<input type="text" name="workOrderNo_<? echo $tblRow; ?>" id="workOrderNo_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $row[csf('job_no')]; ?>" style="width:110px;" placeholder="Dbl click" onDblClick="openmypage_wo(<? echo $tblRow; ?>);" readonly />			
						<input type="hidden" name="hideWoId_<? echo $tblRow; ?>" id="hideWoId_<? echo $tblRow; ?>" value="<? echo $row[csf('job_id')]; ?>" readonly />
						<input type="hidden" name="hideWoDtlsId_<? echo $tblRow; ?>" id="hideWoDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf('po_id')]; ?>" readonly />
					</td>
					<td> 
						<input type="text" name="Order_no_<? echo $tblRow; ?>" id="Order_no_<? echo $tblRow; ?>" class="text_boxes" style="width:70px" disabled="disabled" value="<? echo $row[csf('po_number')]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="AccPo_NO_<? echo $tblRow; ?>" id="AccPo_NO_<? echo $tblRow; ?>" class="text_boxes" style="width:70px" disabled="disabled" value="<? echo $acc_po_arr[$row["PO_ID"]]["ACC_PO_NO"]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="jobstyle_<? echo $tblRow; ?>" id="jobstyle_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" disabled="disabled" value="<? echo $row[csf('style_ref_no')]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="Style_Desc_<? echo $tblRow; ?>" id="Style_Desc_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? echo $row[csf('style_description')]; ?>" disabled="disabled"/>
                    </td>
					<td> 
						<input type="text" name="GsmItem_<? echo $tblRow; ?>" id="GsmItem_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? echo $garments_item[$item_id_arr[$row["PO_ID"]]["ITEM_NUMBER_ID"]]; ?>" disabled="disabled"/>
						<input type="hidden" name="hideGsmItem_<? echo $tblRow; ?>" id="hideGsmItem_<? echo $tblRow; ?>" value="<? echo $item_id_arr[$row["PO_ID"]]["ITEM_NUMBER_ID"]; ?>" readonly />
                    </td>
                    <td>
                        <input type="text" name="composition_<? echo $tblRow; ?>" id="composition_<? echo $tblRow; ?>" class="text_boxes" value="<? echo ""; ?>" style="width:100px"/>
                    </td>
					<td>
                        <input type="text" name="Brand_<? echo $tblRow; ?>" id="Brand_<? echo $tblRow; ?>" class="text_boxes" value="<? echo $brand_name_arr[$row[csf('brand_id')]]; ?>" style="width:60px" disabled="disabled"/>
                        <input type="hidden" name="BrandId_<? echo $tblRow; ?>" id="BrandId_<? echo $tblRow; ?>" value="<? echo $row[csf('brand_id')]; ?>"/>
                    </td>
					<td> 
						<input type="text" name="hscode_<? echo $tblRow; ?>" id="hscode_<? echo $tblRow; ?>" class="text_boxes" style="width:50px" value="<? //echo $row[csf('work_order_no')]; ?>"/>
                    </td>
                    <td>
					   <? 
					    $status=array(1=>"Attach",2=>"Detach");
						 echo create_drop_down( "Status_".$tblRow, 60, $status,"", 1, "-- Select --","","", 0,'','','','','','',"cboUom[]");
					    ?>
                    </td>
					<td>
						<input type="text" name="quantity_<? echo $tblRow; ?>" id="quantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny; ?>" style="width:61px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
						<input type="hidden" name="hdnQuantity_<? echo $tblRow; ?>" id="hdnQuantity_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $bal_qtny ?>" style="width:61px;" />
					</td>		            
					<td>
						<input type="text" name="rate_<? echo $tblRow; ?>" id="rate_<? echo $tblRow; ?>" class="text_boxes_numeric" value="<? echo $row[csf('unit_price')]; ?>" style="width:60px;" onKeyUp="calculate_amount(<? echo $tblRow; ?>)" />
					</td>
					<td>
						<input type="text" name="amount_<? echo $tblRow; ?>" id="amount_<? echo $tblRow; ?>" class="text_boxes_numeric" style="width:75px;" value="<?= $bal_qtny*$row[csf('unit_price')]?>" readonly/>
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
				$sql ="select a.id, a.work_order_no, a.color_id, a.construction, a.composition, a.gsm, a.dia_width, a.uom, a.quantity, a.rate, a.amount,b.upcharge, b.discount,b.net_total_amount from com_export_pi_dtls a, com_export_pi_mst b where a.pi_id = b.id and pi_id='$data[1]' and a.quantity>0 and a.status_active in(1,2,3) and a.is_deleted=0 ";
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

if($action=="print_new_rpt_Grmnt_Washing_2") 
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
		if($company_data[csf('vat_number')]!=0)$vat_number = $company_data[csf('vat_number')];else $vat_number='';
		$bin_no = $company_data[csf('bin_no')];
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country.$contact_no;
	}
	if($bin_no!="" && $vat_number==""){ $bin_vat_data=$bin_no;}
	if($bin_no=="" && $vat_number!=""){ $bin_vat_data=$vat_number;}
	if($bin_no!="" && $vat_number!=""){ $bin_vat_data=$vat_number."/".$bin_no;}

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$bank_address_arr=return_library_array( "select id, address as address from lib_bank where ",'id','address');	
	$lib_buyer_arr=return_library_array( "select id, address_1 from lib_buyer",'id','address_1');	
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$lib_buyer_arr_data=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$locataionArrays = sql_select("select address as address_1 from lib_location where company_id=$data[0]");

	$bank_accountArray=sql_select( "select id, account_id, account_type, account_no, currency, loan_limit, loan_type, company_id ,status_active from lib_bank_account where account_id=$data[4]" );

	$nameBankArray=sql_select( "select id,bank_name,bank_short_name,branch_name,bank_code,contact_person,contact_no,swift_code,web_site,email,address,country_id,lien_bank,issusing_bank,advising_bank,salary_bank,remark, status_active,designation,ac_type_id,cheque_template from lib_bank  where id=$data[4]" );

	if ($db_type==0)
	{
		$bank_arr=return_library_array( "select id, concat(bank_name, ' ( ',branch_name,' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name'); 
	}
	else
	{
		$bank_arr=return_library_array( "select id, (bank_name||' ( '|| branch_name||' )') as bank_name from lib_bank where advising_bank=1",'id','bank_name');
	}
	
	$sql ="SELECT a.id as ID, a.pi_date as PI_DATE, a.pi_revised_date as PI_REVISED_DATE, a.pi_number as PI_NUMBER, a.within_group as WITHIN_GROUP, a.buyer_id as BUYER_ID, a.hs_code as HS_CODE, a.advising_bank as ADVISING_BANK, a.item_category_id as ITEM_CATEGORY_ID,a.upcharge as UPCHARGE,a.discount as DISCOUNT,a.net_total_amount as NET_TOTAL_AMOUNT, b.id as dtls_id, b.work_order_no as WORK_ORDER_NO, b.work_order_id as WORK_ORDER_ID, b.booking as BOOKING, b.hs_code as HS_CODE_DTLS,b.gmts_item_id as GMTS_ITEM_ID, b.item_desc as ITEM_DESC, b.uom as UOM, b.quantity as QUANTITY, b.amount as AMOUNT, d.section as SECTION,b.BUYER_STYLE_REF,a.WEIGHT_APPROX,a.CURRENCY_ID from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c,subcon_ord_dtls d where a.id=b.pi_id and a.id=$data[1] and b.work_order_dtls_id=c.id and c.mst_id=d.id and a.exporter_id=$data[0] and a.status_active in(1) and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0";
    $sql_mst = sql_select($sql); 

		if($sql_mst[0][csf('within_group')]==1)
		{
			$buyer=$company_arr[$sql_mst[0][csf('buyer_id')]].'<br>'.$lib_buyer_arr[$sql_mst[0][csf('buyer_id')]];
		}
		else
		{						
			$buyer=return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0][csf('buyer_id')]."'");						
		}
     ?>
		<style>
			.hight td{
				height: 151.18px;
				border: none;
			
			}
			.hight_footer{
				height: 98.227px;
				border: none;
			}
			.bolt{
					font-size:18px;
					border: none;
				}

			.border_size th {
				border: 1px solid black !important;
				border-collapse: collapse;
			}
			.border_data td {
				border: 1px solid black!important;
				border-collapse: collapse;
			}
			table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
			border: none;
			}			
		</style>
	<div style="width:1000px;padding-left: 20px;" >
	
        <table  width="100%" >
			<thead>
				<tr  class="hight">
					<td class="bolt"  colspan="11"></td>
				</tr>
			</thead>
           <tbody>
				<tr>
					<td style="font-size:30px; padding: 15px 0px;color: rgb(30,129,176);" align="center" colspan="11">PROFORMA INVOICE
					</td>
				</tr>
				<tr>
					<td colspan="4"  class="bolt" ><b>PI NO: <?=$sql_mst[0]['PI_NUMBER']?></b></td>
					<td class="bolt" width="100px"></td>
					<td colspan="6" class="bolt" ><b>DATE: <?=$sql_mst[0]['PI_DATE']?></b></td>
				</tr>
				<tr>
					<td colspan="4"  class="bolt" style="vertical-align: top;"> <u><b>Applicant:</b></u><br> <? echo "<b>".$buyer."</b>"."<br>".$company_address;?></b></td>
					<td class="bolt" width="100px"></td>
					<td colspan="6"  class="bolt" style="vertical-align: top;"><u><b>Beneficiary</b></u> <br> <b><?= $company_arr[$data[0]];?></b> <br><?=$locataionArrays[0]['ADDRESS_1']."<br>"."BIN/VAT No:"."  ".$bin_vat_data?> </td>
				</tr>
				<tr style="height: 20px;">
				</tr>
				<tr class="border_size">
					<th style="width: 40px; text-align: center;">SL </th>
					<th style="width: 70px; text-align: center;">Work Description </th>
					<th style="width: 70px; text-align: center;">Buyer</th>
					<th style="width: 100px; text-align: center;">PO .NO</th>
					<th style="width: 70px; text-align: center;"> Item name</th>
					<th style="width: 70px; text-align: center;">Style .No</th>
					<th style="width: 70px; text-align: center;">Color</th>
					<th style="width: 70px; text-align: center;">QTY /PCS</th>
					<th style="width: 60px; text-align: center;">QTY/<?=$unit_of_measurement[$sql_mst[0]['UOM']]?></th>
					<th style="width: 70px; text-align: center;">Unit Price/<?=$unit_of_measurement[$sql_mst[0]['UOM']]?></th>
					<th style="width: 80px; text-align: center;">Amount <?=$currency[$sql_mst[0]['CURRENCY_ID']]?>($)</th>
				</tr>
				<?
				
				if($db_type==0) $process_type_cond="group_concat(c.process,'*',c.embellishment_type)";
				else if ($db_type==2) $process_type_cond="listagg(c.process||'*'||c.embellishment_type,',') within group (order by c.process||'*'||c.embellishment_type)";
				$sql_export_pi_dtls = "SELECT a.id,a.item_category_id,a.currency_id,b.work_order_no as job_no, b.booking, b.color_id, b.uom, sum(b.quantity) as quantity, b.rate, sum(b.amount) as amount ,$process_type_cond as process_type, b.gmts_item_id,b.buyer_style_ref,d.buyer_po_no,a.buyer_id,a.within_group,d.party_buyer_name,c.description from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c, subcon_ord_dtls d  where a.id=b.pi_id and  b.work_order_dtls_id=c.id and c.mst_id=d.id and  a.id= $data[1] and a.is_deleted=0 and b.is_deleted=0 group by a.id,a.item_category_id,a.currency_id,b.work_order_no, b.booking, b.color_id, b.uom, b.rate, b.gmts_item_id,b.buyer_style_ref,d.buyer_po_no,a.buyer_id,a.within_group,d.party_buyer_name,c.description";
				
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
					//$process_name=implode(",",array_unique(explode(",",$process_name)));
					$sub_process_name=implode(",",array_unique(explode(",",$sub_process_name)));
				?>
				<tr class="border_data">
					<td style="width: 40px; text-align: center;"><?= $i;?></td>
					<td style="width: 70px; text-align: center;"><p><?= $sub_process_name; ?></p></td>
					<td style="width: 70px; text-align: center;"><p><?= $value[csf("party_buyer_name")]; ?></p></td>
					<td style="width: 100px; text-align: center;"><p><?= $value[csf("buyer_po_no")]; ?></p></td>
					<td style="width: 70px; text-align: center;"><p><?= $garments_item[$value[csf("gmts_item_id")]]; ?></p></td>
					<td style="width: 70px; text-align: center;"><p><?= $value[csf("buyer_style_ref")]; ?></p></td>
					<td style="width: 70px; text-align: center;"><p><?= $color_arr[$value[csf("color_id")]]; ?></p></td>
					<td style="width: 70px; text-align: right;"><p><?= number_format($value[csf("quantity")]*12,2,".",""); $total_qty+=$value[csf("quantity")]*12;?></p></td>
					<td style="width: 60px; text-align: right;"><p><?= number_format($value[csf("quantity")],2,".",""); $total_qty_dzn+=$value[csf("quantity")];?></p></td>
					<td style="width: 70px; text-align: right;"><p><?= "$ ".number_format($value[csf("rate")],2,".",""); ?></p></td>
					<td style="width: 80px; text-align: right;"><p><?="$ ".number_format($value[csf("quantity")]*$value[csf("rate")],2,".",""); $total_amt+=$value[csf("quantity")]*$value[csf("rate")]; ?></p></td>
				</tr>
				<?
				$i++; 
				}
				?>
				<tr class="border_data">
					<td colspan="7" align="right"><b>Total</b></td>
					<td align="right"><p><b><?= number_format($total_qty,2,".","");?></b></p></td>
					<td align="right"><p><b><?= number_format($total_qty_dzn,2,".","");?></b></p></td>
					<td></td>
					<td align="right"><p><b><?= "$ ".number_format($total_amt,2, '.', '');//$total_amt;?></b></p></td>
				</tr>
				<tr>
					<td colspan="12" align="left"> <b><?= number_to_words(number_format($total_amt,2, '.', ''), "USD", "Cent");?></b> </td>
				</tr>
				<tr style="height: 20px;">
				</tr>
				<br><style> .terms_cond_table tr td { border: 1px solid white;}</style>
				<? $data_array=sql_select("select id, terms,terms_prefix from  wo_booking_terms_condition where booking_no=$data[1] and entry_form =152 order by id");
				?>
				<tr>
					<td colspan="10"><u><strong> TERMS & CONDITIONS :</strong></u></td>
				</tr>
				<tr> 
					<td style="vertical-align: top;">1.</td>
					<td style="vertical-align: top;width:130px" >Payment Terms</td>		
					<td style="vertical-align: top;" colspan="9">A. PI value less then  $1,500.00 for RTGS,  $ 1,500.00-5,000.00 for At Sight & more $ 5,000.00 then 90 days from the date of delivery.<br>B. Payment will be made in US Dollar Drawn on Bangladesh Bank or EQV. BD take. <br> C. Bank attested P/I must be attached with Back to Back LC.</td>
				</tr>
				<tr>
					<td style="vertical-align: top;">2.</td>
					<td  style="vertical-align: top;">Delivery Lead Time</td>		
					<td colspan="9" style="vertical-align: top;">A. Delivery will be made within  15 days from the date of workable LC receipt <br>B. Partial Shipment are allowed.</td>
				</tr>
				<tr>
					<td style="vertical-align: top;">3.</td>
					<td  style="vertical-align: top;">U/D</td>		
					<td colspan="9" style="vertical-align: top;"><b>: LC & a copy of UD provide us with mentioning Beneficiary's name & address, Product dtails, qty & value.</b></td>
				</tr>
				<tr>
					<td style="vertical-align: top;">4.</td>
					<td  style="vertical-align: top;">Time of Negotiation </td>		
					<td colspan="9" style="vertical-align: top;">: 15 Days from the date of shipment/delivery challan.</td>
				</tr>
				<tr> 
					<? if($bank_accountArray[0]['ACCOUNT_TYPE']==10){
						$bank_acc=$bank_accountArray[0]['ACCOUNT_NO'];
					}else{
						$bank_acc="";
					}?>
					<td style="vertical-align: top;">5.</td>
					<td  style="vertical-align: top;">Advising Bank</td>		
					<td colspan="9" style="vertical-align: top;"><b>: <?="Bank Name:".$bank_arr[$sql_mst[0]['ADVISING_BANK']]." Brance Name:".$nameBankArray[0]['BRANCH_NAME']." Address:".$nameBankArray[0]['ADDRESS']." Bank Account No:".$bank_acc." Swift Code:".$nameBankArray[0]['SWIFT_CODE']?></b></td>
				</tr>
				<tr>
					<td style="vertical-align: top;">6.</td>
					<td  style="vertical-align: top;">Acceptance</td>		
					<td colspan="9" style="vertical-align: top;">: Should be provide within 7 days after Transport Documents Submission.</td>
				</tr>
				<tr>
					<td style="vertical-align: top;">7.</td>
					<td  >Bank Charge</td>		
					<td colspan="9">: All Bank Charges except beneficiary's Bank are on Opener's Account.</td>
				</tr>
				<tr>
					<td style="vertical-align: top;">8.</td>
					<td  style="vertical-align: top;">Overdue Interest</td>		
					<td colspan="9" style="vertical-align: top;"><b>:Payment will be made within the date of Maturity. If failled, Overdue Interest to be paid by opener at 16%.</b></td>
				</tr>
				<tr>
					<td style="vertical-align: top;">9.</td>
					<td  style="vertical-align: top;">Claim/ Compain</td>		
					<td colspan="9" style="vertical-align: top;">: Claim / Complain regarding quantity /qyality of the merchandise must be inform to beneficiary within 3 days, after Receive of Goods.</td>
				</tr>
				<tr>
					<td style="vertical-align: top;">10.</td>
					<td  style="vertical-align: top;">Expiry date of LC</td>		
					<td colspan="9" style="vertical-align: top;">: 15 Days from the date of last shipment date.</td>
				</tr>
				<tr>
					<td>11.</td>
					<td  style="vertical-align: top;">HS code </td>		
					<td colspan="9" style="vertical-align: top;">: <?=$sql_mst[0]['HS_CODE']?></td>
				</tr>
				<tr>
					<td>12.</td>
					<td  style="vertical-align: top;">Weight approx</td>		
					<td colspan="9" style="vertical-align: top;">:<?=$sql_mst[0]['WEIGHT_APPROX']?></td>
				</tr>
				<tr style="height: 30px;">
				
				</tr>
				<tr>

					<td align="left" colspan="5"><b></td>

					<td colspan="6" align="right"> <b>Accepted By </b></td>
				</tr>
				<tr>

					<td align="left" colspan="5"><b><?= $company_arr[$data[0]];?></b></td>

					<td colspan="6" align="right"> <b><?=$buyer?></b></td>
				</tr>
			</tbody>
			<tfoot>
				<tr  class="hight_footer">
					<td class="bolt"  colspan="11"></td>
				</tr>
			</tfoot>
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
	$sql_export_pi_dtls = "SELECT a.ID, a.ITEM_CATEGORY_ID, a.CURRENCY_ID, b.work_order_no as JOB_NO, b.HS_CODE, b.BOOKING, b.ITEM_DESC, b.GMTS_ITEM_ID, b.COLOR_ID, b.UOM, b.QUANTITY, b.RATE, b.AMOUNT, d.BUYER_BUYER, d.BUYER_PO_NO, d.BUYER_STYLE_REF from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c, subcon_ord_dtls d where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and a.exporter_id=$company_id and a.id=$mst_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	
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
				where a.id=b.pi_id and b.work_order_dtls_id=d.id and a.id=$mst_id and c.id=d.mst_id and  c.job_no_mst=d.job_no_mst and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.is_deleted=0
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
				where a.id=b.pi_id and b.work_order_dtls_id=d.id and a.id=$mst_id and c.job_no_mst=d.job_no_mst and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0
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
				$sql_export_pi_dtls = "SELECT a.ID, b.work_order_no as JOB_NO, b.BOOKING, b.ITEM_DESC, b.GMTS_ITEM_ID, b.UOM, b.RATE, SUM (b.QUANTITY) as QUANTITY, SUM (b.AMOUNT) as AMOUNT,d.BUYER_BUYER from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c, subcon_ord_dtls d where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and a.exporter_id=$company_id and a.id=$mst_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.id, b.work_order_no, b.booking, b.item_desc, b.gmts_item_id, b.uom, b.rate, d.buyer_buyer";
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
	$sql_mst = sql_select("SELECT id,pi_number,pi_date,item_category_id,buyer_id,currency_id,pi_validity_date,exporter_id,within_group,last_shipment_date,attention,pi_revised_date,remarks, hs_code from com_export_pi_mst where id= $data[1]");

	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" ); $total_ammount = 0; $total_quantity=0; $total_pcs_qty=0;  $total_kg_qty=0;
	$sql ="SELECT f.fabric_composition_id, a.id,a.booking, a.work_order_no, a.color_id, a.construction, a.composition, a.uom, a.quantity, a.rate, a.amount, a.work_order_dtls_id, a.determination_id, b.upcharge, b.discount, b.net_total_amount, c.Style_ref_no, c.customer_buyer, d.gsm_weight as before_wash_gsm, d.after_wash_gsm, d.dia as fabric_dia, d.cuttable_dia, d.color_range_id, a. remarks from lib_yarn_count_determina_mst f, com_export_pi_dtls a, com_export_pi_mst b, fabric_sales_order_mst c, fabric_sales_order_dtls d where f.id=a.determination_id and a.pi_id = b.id and a.pi_id='$data[1]' and a.quantity>0 and a.work_order_id=c.id and c.id=d.mst_id and a.work_order_dtls_id=d.id and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and f.status_active=1 and f.is_deleted=0";
	// echo $sql;
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
		$data_array[$description][$row[csf('color_range_id')]][$row[csf('uom')]][$row[csf('rate')]]['remarks']=$row[csf('remarks')];
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
					<th width="300">Fabric  Description</th>
					<th width="150">Color Range</th>
					<th width="100">Quantity (Kg)</th>
					<th width="100">Quantity (Pcs)</th>
					<th width="80">Unit Price</th>
					<th width="60">Amount</th>
					<th>Remark</th>
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
			                        <td align="left"><? echo $row['remarks'];?></td>
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
					<td align="right"></td>
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
            <tr height="20"></tr>
        </table>
        <table width="100%" cellspacing="1" rules="all" border="1">
			<tr>
				<td><strong> HS CODE: <? echo $sql_mst[0][csf('hs_code')];?></strong></td>
			</tr>
        </table>

        <!-- <? 
        	echo get_spacial_instruction($data[1],"100%",152);
        ?> -->
		<table  width='1000' class="rpt_table" cellpadding="0" cellspacing="0">
			<thead>
				<tr height="20"></tr>
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
	//$user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
	$user_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_id' and is_deleted=0","image_location");
	// $lib_fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where status_active=1", "id", "fabric_composition_name");
	$lib_compositionArr=return_library_array( "select id,composition_name from lib_composition_array where status_active=1", "id", "composition_name");
	$fab_com_arr = return_library_array("select id, fabric_composition_id from lib_yarn_count_determina_mst", 'id', 'fabric_composition_id');

	// $sql_company = sql_select("SELECT * FROM lib_company WHERE is_deleted=0 and status_active=1 and id='$company_id'");
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
		$contact_no_arr[$row[csf('id')]]=$contact_no;
		$company_email_arr[$row[csf('id')]]=$company_email;
		$bin_no_arr[$row[csf('id')]]=$bin_no;
	}	
	// echo $company_address[$company_id]; die;
	$sql ="SELECT a.id, a.pi_date, a.pi_revised_date, a.last_shipment_date, a.pi_number, a.within_group, a.hs_code, a.advising_bank, a.item_category_id,a.upcharge,a.discount,a.net_total_amount, b.id as dtls_id, b.work_order_no, b.work_order_id, b.color_id, b.construction, b.composition, b.gsm, b.dia_width, b.uom, b.quantity, b.rate, b.amount, b.remarks, a.attention,b.determination_id from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.id=$mst_id and a.exporter_id=$company_id and a.item_category_id=10 and a.status_active in(1,2,3) and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0";
	$sql_res=sql_select($sql);
	$check_master_arr=array();
	$all_data_arr=array();
	foreach ($sql_res as $val) 
	{
		$sales_order_id.=$val[csf('work_order_id')].',';
		if ($check_master_arr[$val[csf('id')]]==""){
			$check_master_id[$val[csf('id')]]=$val[csf('id')];
			$pi_date=$val[csf('pi_date')];	
			$attention=$val[csf('attention')];	
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
		$all_data_arr[$key]['determination_id']=$val[csf('determination_id')];
		$all_data_arr[$key]['color_id']=$val[csf('color_id')];
		$all_data_arr[$key]['gsm']=$val[csf('gsm')];
		$all_data_arr[$key]['dia_width']=$val[csf('dia_width')];
		$all_data_arr[$key]['uom']=$val[csf('uom')];
		$all_data_arr[$key]['rate']=$val[csf('rate')];
		$all_data_arr[$key]['quantity']+=$val[csf('quantity')];
		$all_data_arr[$key]['amount']+=$val[csf('rate')]*$val[csf('quantity')];
		$all_data_arr[$key]['remarks'].=$val[csf('remarks')].",";
		$upcharge=$val[csf('upcharge')];
		$discount=$val[csf('discount')];
		$net_total_amount=$val[csf('net_total_amount')];
	}

	$sales_order_ids=implode(',',array_unique(explode(',',rtrim($sales_order_id,','))));
	//echo $sales_order_ids.'system';
	

	$sql_sales_order=sql_select("SELECT id, company_id, buyer_id, within_group, customer_buyer, style_ref_no, ship_mode, booking_date, sales_booking_no, booking_id, delivery_date, booking_approval_date, po_job_no from fabric_sales_order_mst where id in($sales_order_ids) and entry_form=109 and status_active=1 and is_deleted=0");
	$sales_order_no='';
	$booking_id_arr=array();
	foreach ($sql_sales_order as $val) {
		$style_ref_no.=$val[csf('style_ref_no')].',';
		$ship_mode=$shipment_mode[$val[csf('ship_mode')]];
		if ($val[csf('within_group')] == 2)
		{
			if ($val[csf('customer_buyer')] != "") $customer_buyer.=$buyer_arr[$val[csf('customer_buyer')]].',';
		}
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
	// echo $buyer_id; die;
	$booking_ids=implode(',',array_unique(explode(',',rtrim($booking_id,','))));
	$style_ref_nos=implode(', ',array_unique(explode(',',rtrim($style_ref_no,','))));
	$customer_buyers=implode(', ',array_unique(explode(',',rtrim($customer_buyer,','))));
	

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
	$buyer_names=implode(', ',array_unique(explode(',',rtrim($buyer_names,','))));
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
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $contact_no_arr[$company_id]; ?></strong></td>
	            <td colspan="3" style="font-size:16px;">PI Revised Date:&nbsp;<? echo change_date_format($pi_revised_date); ?></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $company_email_arr[$company_id]; ?></strong></td>
	            <td colspan="3" style="font-size:16px;"><strong>PI No:&nbsp;<? echo $pi_number; ?></strong></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; ">&nbsp;</strong></td>
	            <td colspan="3" style="font-size:16px;"><strong>BIN No:&nbsp;<? echo $bin_no_arr[$company_id]; ?></strong></td>
	        </tr>	        
	    </table>
	    <br>
		<table class="rpt_table" width="1200" cellspacing="1" rules="all" border="1">
			<tr>
				<td colspan="6" width="520" style="font-size: 18px;"><strong>For Account & Risk Of:</strong></td>
				<td colspan="7" width="480" style="font-size: 18px;"><strong>Beneficiary:</strong></td>
			</tr>
			<tr>
				<td colspan="6"  width="520" style="font-size: 18px;"><strong><? if ($within_group==1) echo $company_arr[$buyer_id].'<br>'.$company_address[$buyer_id].'<br>'.$contact_no_arr[$buyer_id].'<br>'.$company_email_arr[$buyer_id]; else if ($within_group==2) echo $buyer_arr[$buyer_id].'<br>'.$buyer_details_arr[$buyer_id]["buyer_address"].'<br>'.$buyer_details_arr[$buyer_id]["buyer_email"]; ?></strong></td>
				<td colspan="7"  width="480" style="font-size: 18px;"><strong><? echo $company_arr[$company_id].'<br>'.$company_address[$company_id].'<br>'.$contact_no_arr[$company_id].'<br>'.$company_email_arr[$company_id]; ?></strong></td>
			</tr>
			<tr>
				<td colspan="6"  width="520" style="font-size: 18px;"><strong>Buyer:&nbsp;<? if ($within_group==1) echo $buyer_names; else echo $customer_buyers; ?><br>Garments Style:&nbsp;<? if (strlen($style_ref_nos) > 45) echo substr($style_ref_nos, 0, 45).'...'; else echo $style_ref_nos; ?><br>Garmments PO:&nbsp;<? if (strlen($po_numbers) > 45) echo substr($po_numbers, 0, 45).'...'; else echo $po_numbers; ?></strong></td>
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
				<td colspan="13" width="1000" style="font-size: 18px;"><strong><?// echo $item_category_name; ?>&nbsp;(H.S. Code - <? echo $hs_code; ?>) charge for 100 % export oriented readymade garments industries  As Follows:</strong></td>
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
                        <td width="180"><? echo $lib_compositionArr[$fab_com_arr[$row['determination_id']]]//$row['composition']; ?></td>
                        <td width="100"><? echo $color_arr[$row['color_id']]; ?></td>
                        <td width="50"><? echo $row['gsm']; ?></td>
                        <td width="80"><? echo $row['dia_width']; ?></td>
                        <td width="50"><? echo $unit_of_measurement[$row['uom']]; ?></td>
                        <td width="100" align="right"><? echo number_format($row['quantity'],2); ?></td>
                        <td width="80" align="right"><? echo number_format($row['rate'],4); ?></td>                        
                        <td width="80" align="right"><? echo number_format($row['amount'],2); ?></td>
                        <td ><? echo ltrim(implode(",", array_unique(explode(",", chop($row['remarks'], ",")))), ',');
						?></td>
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
                <td valign="top"><strong>NOTE: </strong></td>
                <td><? echo $attention;?></td>

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
		$contact_no_arr[$row[csf('id')]]=$contact_no;
		$company_email_arr[$row[csf('id')]]=$company_email;
		$bin_no_arr[$row[csf('id')]]=$bin_no;
	}	

	$sql ="SELECT a.id, a.pi_date, a.exporter_id, a.pi_revised_date, a.last_shipment_date, a.pi_number, a.within_group, a.buyer_id, a.hs_code, a.advising_bank, a.item_category_id,a.upcharge,a.discount,a.net_total_amount, b.id as dtls_id, b.work_order_no, b.work_order_id, b.color_id, b.construction, b.composition, b.gsm, b.dia_width, b.uom, b.quantity, b.rate, b.amount, b.remarks from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id and a.id=$mst_id and a.exporter_id=$company_id and a.item_category_id=$cbo_item_category_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0";
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
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $contact_no_arr[$company_id]; ?></strong></td>
	            <td colspan="3" style="font-size:16px;">PI Revised Date:&nbsp;<? echo change_date_format($pi_revised_date); ?></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $company_email_arr[$company_id]; ?></strong></td>
	            <td colspan="3" style="font-size:16px;"><strong>PI No:&nbsp;<? echo $pi_number; ?></strong></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; ">&nbsp;</strong></td>
	            <td colspan="3" style="font-size:16px;"><strong>BIN No:&nbsp;<? echo $bin_no_arr[$company_id]; ?></strong></td>
	        </tr>	        
	    </table>
	    <br>
		<table class="rpt_table" width="1200" cellspacing="1" rules="all" border="1">
			<tr>
				<td colspan="6" width="520" style="font-size: 18px;"><strong>For Account & Risk Of:</strong></td>
				<td colspan="7" width="480" style="font-size: 18px;"><strong>Beneficiary:</strong></td>
			</tr>
			<tr>
				<td colspan="6"  width="520" style="font-size: 18px;"><strong><? if ($within_group==1) echo $company_arr[$buyer_id].'<br>'.$company_address[$buyer_id].'<br>'.$contact_no_arr[$buyer_id].'<br>'.$company_email_arr[$buyer_id]; else if ($within_group==2) echo $buyer_arr[$buyer_id].'<br>'.$buyer_details_arr[$buyer_id]["buyer_address"].'<br>'.$buyer_details_arr[$buyer_id]["buyer_email"]; ?></strong></td>

				<td colspan="7"  width="480" style="font-size: 18px;"><strong>
					<? 
					if($within_group==1)
					echo $company_arr[$company_id].'<br>'.$company_address[$company_id].'<br>'.$contact_no_arr[$company_id].'<br>'.$company_email_arr[$company_id]; 
					else if($within_group==2)
					echo $buyer_arr[$buyer_id].'<br>'.$buyer_details_arr[$buyer_id]["buyer_address"].'<br>'.$buyer_details_arr[$buyer_id]["buyer_email"];
					?>
				</strong></td>
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
		$contact_no_arr[$row[csf('id')]]=$contact_no;
		$company_email_arr[$row[csf('id')]]=$company_email;
		$bin_no_arr[$row[csf('id')]]=$bin_no;
	}	

	$sql ="SELECT a.id as ID, a.pi_date as PI_DATE, a.pi_revised_date as PI_REVISED_DATE, a.pi_number as PI_NUMBER, a.within_group as WITHIN_GROUP, a.buyer_id as BUYER_ID, a.hs_code as HS_CODE, a.advising_bank as ADVISING_BANK, a.item_category_id as ITEM_CATEGORY_ID,a.upcharge as UPCHARGE,a.discount as DISCOUNT,a.net_total_amount as NET_TOTAL_AMOUNT, b.id as dtls_id, b.work_order_no as WORK_ORDER_NO, b.work_order_id as WORK_ORDER_ID, b.booking as BOOKING, b.hs_code as HS_CODE_DTLS,b.gmts_item_id as GMTS_ITEM_ID, b.item_desc as ITEM_DESC, b.uom as UOM, b.quantity as QUANTITY, b.amount as AMOUNT, d.section as SECTION from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c,subcon_ord_dtls d where a.id=b.pi_id and a.id=$mst_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and a.exporter_id=$company_id and a.item_category_id=$cbo_item_category_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0";
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
	// echo "<pre>"; print_r($buyer_arr); echo "bid- ". $buyer_id; echo "buyer- ". $buyer_arr[$buyer_id]; die;
	// echo "<pre>"; print_r($company_email_arr); echo "bid- ". $buyer_id; echo "email- ". $company_email_arr[$buyer_id]; die;
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
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $contact_no_arr[$company_id]; ?></strong></td>
	            <td colspan="3" style="font-size:16px;">PI Revised Date:&nbsp;<? echo change_date_format($pi_revised_date); ?></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $company_email_arr[$company_id]; ?></strong></td>
	            <td colspan="3" style="font-size:16px;"><strong>PI No:&nbsp;<? echo $pi_number; ?></strong></td>
	        </tr>
	        <tr>
	            <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; ">&nbsp;</strong></td>
	            <td colspan="3" style="font-size:16px;"><strong>BIN No:&nbsp;<? echo $bin_no_arr[$company_id]; ?></strong></td>
	        </tr>	        
	    </table>
	    <br>
		<table class="rpt_table" width="1200" cellspacing="1" rules="all" border="1">
			<tr>
				<td colspan="6" width="520" style="font-size: 18px;"><strong>For Account & Risk Of:</strong></td>
				<td colspan="7" width="480" style="font-size: 18px;"><strong>Beneficiary:</strong></td>
			</tr>
			<tr>
				<td colspan="6"  width="520" style="font-size: 18px;" valign="top"><strong><? if ($within_group==1) echo $company_arr[$buyer_id].'<br>'.$company_address[$buyer_id].'<br>'.$contact_no_arr[$buyer_id].'<br>'.$company_email_arr[$buyer_id]; else if ($within_group==2) echo $buyer_arr[$buyer_id].'<br>'.$buyer_details_arr[$buyer_id]["buyer_address"].'<br>'.$buyer_details_arr[$buyer_id]["buyer_email"]; ?></strong></td>
				<td colspan="7"  width="480" style="font-size: 18px;"><strong><? echo $company_arr[$company_id].'<br>'.$company_address[$company_id].'<br>'.$contact_no_arr[$company_id].'<br>'.$company_email_arr[$company_id]; ?></strong></td>
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

//This button created by Wayasel
if($action==='print_new_rpt_woven') 
{	
	list($company_id, $mst_id, $system_id) = explode('*', $data);
	//echo $company_id.'**'.$mst_id.'**'.$system_id;
	$sql_company = sql_select("SELECT COMPANY_NAME, CONTRACT_PERSON, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, CITY, ZIP_CODE, COUNTRY_ID, CONTACT_NO, EMAIL FROM lib_company WHERE id=$company_id and status_active=1 and is_deleted=0");
	$country_name_arr=return_library_array("select id, country_name from lib_country where status_active=1",'id','country_name');

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

	$sql_export_pi = "SELECT ID, PI_NUMBER, PI_DATE, ITEM_CATEGORY_ID, BUYER_ID, CURRENCY_ID, PI_VALIDITY_DATE, EXPORTER_ID, WITHIN_GROUP, LAST_SHIPMENT_DATE, HS_CODE, SWIFT_CODE, REMARKS, ADVISING_BANK,BENEFICIARY_BANK from com_export_pi_mst where id= $mst_id";
	//echo $sql_export_pi;
	$sql_mst = sql_select($sql_export_pi);
	$buyer_id = $sql_mst[0]['BUYER_ID'];

	$sql_buyer=sql_select("SELECT BUYER_NAME, ADDRESS_1, CONTACT_PERSON, EXPORTERS_REFERENCE, BUYER_EMAIL from lib_buyer where id=$buyer_id and status_active=1 and is_deleted=0");
	$advising_bank_id = $sql_mst[0]['ADVISING_BANK'];
	$beneficiary_bank = $sql_mst[0]['BENEFICIARY_BANK'];

	$sql_export_pi_dtls = "SELECT a.ID, a.ITEM_CATEGORY_ID, a.CURRENCY_ID, b.work_order_no as JOB_NO, b.HS_CODE, b.ITEM_DESC, b.GMTS_ITEM_ID, b.QUANTITY, b.RATE, b.AMOUNT, b.COMPOSITION, b.BUYER_STYLE_REF, b.ORDER_NO, b.WORK_ORDER_DTLS_ID from com_export_pi_mst a, com_export_pi_dtls b where a.id=b.pi_id  and a.exporter_id=$company_id and a.id=$mst_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.ATT_DETA=1";
	$exprt_pi_dtls_result = sql_select($sql_export_pi_dtls);

	$buyer_name=return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0][csf('buyer_id')]."'");
	$buyer_address=return_field_value("address_1","lib_buyer","id='".$sql_mst[0][csf('buyer_id')]."'");	
	$company_addres=return_field_value("city","lib_company","id='".$company_id."'");	

	$shipment_date=sql_select("SELECT SHIPMENT_DATE, ID FROM wo_po_break_down WHERE is_deleted=0 and status_active=1");
	$shiment_arr=array();
	foreach($shipment_date as $row){
		$shiment_arr[$row["ID"]]["SHIPMENT_DATE"]=$row["SHIPMENT_DATE"];
	}
	?>

	<div style="width:1100px">		
        <table class="rpt_table" width="100%" cellspacing="1"  border="0">
            <tr>
            	<td style="font-size:18px; padding: 10px 0px;" align="center" colspan="11">
                	<strong><? echo 'PROFORMA INVOICE'; ?></strong>
                </td>
            </tr>
            <tr>
            	<td colspan="7" width="600" align="left" style="vertical-align: top;">
                	<p>PI Number:</p>
                </td>
                <td colspan="4" align="left" style="vertical-align: top;">
                	<p>: <?echo $sql_mst[0]['PI_NUMBER']?></p>
                </td>
            </tr>
			<tr>
            	<td colspan="7" align="left" style="vertical-align: top;">
                	<p>PI Date:</p>
                </td>
                <td colspan="5" align="left" style="vertical-align: top;">
                	<p>: <?echo change_date_format($sql_mst[0]['PI_DATE'])?></p>
                </td>
            </tr>
			<tr>
            	<td colspan="7" align="left" style="vertical-align: top;">
                	<p>Name Of Buyer/ Impoter:</p>
                </td>
                <td colspan="5" align="left" style="vertical-align: top;">
                	<p>: <?echo $buyer_name."<br>".$buyer_address;?></p>
                </td>
            </tr>
			<tr>
            	<td colspan="7" align="left" style="vertical-align: top;">
                	<p>Applicant Bank Name And Address:</p>
                </td>
                <td colspan="5" align="left" style="vertical-align: top;">
                	<p>: <?echo $bank_arr[$beneficiary_bank]."<br>".$bank_address_arr[$beneficiary_bank];?></p>
                </td>
            </tr>
			<tr>
            	<td colspan="7" align="left" style="vertical-align: top;">
                	<p>Name Of Shipper / Exporter:</p>
                </td>
                <td colspan="5" align="left" style="vertical-align: top;">
                	<p>: <?echo $company_arr[$company_id]."<br>".$company_addres;?></p>
                </td>
            </tr>
			<tr>
            	<td colspan="7" align="left" style="vertical-align: top;">
                	<p>Shipper / Exporter Bank Detail:</p>
                </td>
                <td colspan="5" align="left" style="vertical-align: top;">
                	<p>: <?echo $bank_arr[$advising_bank_id]."<br>".$bank_address_arr[$sql_mst[0]['ADVISING_BANK']];?></p>
                </td>
            </tr>
		</table>
		<table  width="100%" cellspacing="1"  border="1">
			<tr style="background-color: #BCBCBC;">
				<th style="width: 30px; text-align: center;">SL No</th>
				<th style="width: 150px; text-align: center;">Style No</th>
				<th style="width: 100px; text-align: center;">PO No.</th>
				<th style="width: 100px; text-align: center;">Description</th>
				<th style="width: 80px; text-align: center;">Fabrication</th>
				<th style="width: 100px; text-align: center;">Quantity</th>
				<th style="width: 150px; text-align: center;">Price In US $</th>			
				<th style="width: 100px; text-align: center;">Total Amount US $</th>
				<th style="width: 80px; text-align: center;">Shipment Date</th>
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
					<td style="width: 150px; text-align: center;"><p><? echo $value['BUYER_STYLE_REF']; ?></p></td>
					<td style="width: 100px; text-align: center;"><p><? echo $value['ORDER_NO']; ?></p></td>
					<td style="width: 100px; text-align: center;"><p><? echo $value['ITEM_DESC']; ?></p></td>
					<td style="width: 80px; text-align: center;"><p><? echo $value['COMPOSITION'] ; ?></p></td>
					<td style="width: 100px; text-align: right;"><p><?= number_format($value['QUANTITY'], 2, '.', '');?></p></td>
					<td style="width: 150px; text-align: center;"><p><? echo $value["RATE"];  ?></p></td>
					<td style="width: 100px; text-align: right;"><p><? echo  $value["AMOUNT"];?></p></td>
					<td style="width: 80px; text-align: center;"><p><? echo change_date_format($shiment_arr[$value["WORK_ORDER_DTLS_ID"]]["SHIPMENT_DATE"]); ?></p></td>
				</tr>
				<?
				$total_qty+=$value['QUANTITY'];
				$total_amount+=$value['AMOUNT'];
				$i++; 
			}
			?>
			<tr style="background-color: #BCBCBC;">
				<td colspan="5" align="right"><strong>Total</strong></td>
				<td align="right"><p><strong><? echo number_format($total_qty, 2, '.', '');?></strong></p></td>
				<td></td>
				<td align="right"><p><strong><? echo number_format($total_amount, 2, '.', '');?></strong></p></td>
				<td></td>
			</tr>			
        </table>
        <br>
		<table>
			<tr style="background-color: #FABBAC">
				<td colspan="9" width="1100" align="left"><p><strong>In Word:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? echo number_to_words(number_format($total_amount,2, '.', ''), 'USD', 'Cent'); ?></strong></p></td>
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
            	<td width="230" align="center"><u>FOR AND ON BEHALF OF</u></td>
            	<td width=""></td>
            	<td width="200" align="center"><u>FOR AND ON BEHALF OF</u></td>
            	<td width="100"></td>
            </tr>
            <tr>
            	<td width="100">&nbsp;</td>
            	<td width="230" align="center"><strong><? echo $company_arr[$company_id]; ?></strong></td>
            	<td width=""></td>
            	<td width="150" align="center"><strong><?=$buyer_arr[$buyer_id]?></strong></td>
            	<td width="100"></td>
            </tr>
            <tr height="50"></tr>
        </table>
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
    //$user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
    $user_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
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
		$contact_no_arr[$row[csf('id')]]=$contact_no;
		$company_email_arr[$row[csf('id')]]=$company_email;
		$bin_no_arr[$row[csf('id')]]=$bin_no;
    }

    $sql ="SELECT a.id as ID, a.pi_date as PI_DATE, a.pi_revised_date as PI_REVISED_DATE, a.pi_number as PI_NUMBER, b.body_part as BODY_PART, b.gsm as GSM, d.grey_dia as DIA_WIDTH, d.print_type as PRINT_TYPE, b.aop_color_id as AOP_COLOR_ID, a.within_group as WITHIN_GROUP, a.buyer_id as BUYER_ID, a.hs_code as HS_CODE, a.advising_bank as ADVISING_BANK, a.item_category_id as ITEM_CATEGORY_ID,a.upcharge as UPCHARGE,a.discount as DISCOUNT,a.net_total_amount as NET_TOTAL_AMOUNT, b.id as dtls_id, b.work_order_no as WORK_ORDER_NO, b.work_order_id as WORK_ORDER_ID, b.booking as BOOKING, c.receive_date as ORDER_RCV_DATE,  c.delivery_date AS DELIVERY_DATE, b.hs_code as HS_CODE_DTLS,b.gmts_item_id as GMTS_ITEM_ID, b.item_desc as ITEM_DESC, b.uom as UOM, b.quantity as QUANTITY, b.amount as AMOUNT, d.section as SECTION, d.construction as CONSTRUCTION, d.composition as COMPOSITION, a.inserted_by as INSERTED_BY, b.REMARKS, a.ATTENTION from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_dtls d, subcon_ord_mst c  where a.id=b.pi_id and a.id=$mst_id and b.work_order_dtls_id=d.id and d.mst_id = c.id and a.exporter_id=$company_id and a.item_category_id=$cbo_item_category_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0";

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
            $attention=$val['ATTENTION'];
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
        $all_data_arr[$key]['remarks'] =  $val['REMARKS'];
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
                <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $contact_no_arr[$company_id]; ?></strong></td>
                <td colspan="3" style="font-size:16px;">PI Revised Date:&nbsp;<? echo change_date_format($pi_revised_date); ?></td>
            </tr>
            <tr>
                <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><? echo $company_email_arr[$company_id]; ?></strong></td>
                <td colspan="3" style="font-size:16px;"><strong>PI No:&nbsp;<? echo $pi_number; ?></strong></td>
            </tr>
            <tr>
                <td colspan="8" style="font-size:20px; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; ">&nbsp;</strong></td>
                <td colspan="3" style="font-size:16px;"><strong>BIN No:&nbsp;<? echo $bin_no_arr[$company_id]; ?></strong></td>
            </tr>
        </table>
        <br>
        <table class="rpt_table" width="1200" cellspacing="1" rules="all" border="1">
            <tr>
                <td colspan="6" width="520" style="font-size: 18px;"><strong>For Account & Risk Of:</strong></td>
                <td colspan="7" width="480" style="font-size: 18px;"><strong>Beneficiary:</strong></td>
            </tr>
            <tr>
                <td colspan="6"  width="520" style="font-size: 18px;" valign="top"><strong><? if ($within_group==1) echo $company_arr[$buyer_id].'<br>'.$company_address[$buyer_id].'<br>'.$contact_no_arr[$buyer_id].'<br>'.$company_email_arr[$buyer_id]; else if ($within_group==2) echo $buyer_arr[$buyer_id].'<br>'.$buyer_details_arr[$buyer_id]["buyer_address"].'<br>'.$buyer_details_arr[$buyer_id]["buyer_email"]; ?></strong></td>
                <td colspan="7"  width="480" style="font-size: 18px;"><strong><? echo $company_arr[$company_id].'<br>'.$company_address[$company_id].'<br>'.$contact_no_arr[$company_id].'<br>'.$company_email_arr[$company_id]; ?></strong></td>
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
        <div width="1100" style="font-size: 18px;"><strong><? echo $item_category_name; ?>&nbsp;(H.S. Code - <? echo $hs_code; ?>) charge for 100 % export oriented readymade garments industries  As Follows:</strong></div>
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
				<th width="80">Amount in USD</th>
				<th >Remarks</th>
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
                    <td align="right"><? echo $row['remarks']; ?></td>
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
                <td colspan="9" align="right"><strong>Total</strong></td>
                <td align="right"><strong><? echo number_format($tot_quantity,2); ?></strong></td>
                    <td>&nbsp;</td> 
                <td align="right"><strong><? echo number_format($tot_amount,2); ?></strong></td>
				<td>&nbsp;</td> 
            </tr>
            <tr bgcolor="#dddddd">
                <td colspan="11" align="right"><strong>Upcharge</strong></td>
                <td align="right"><strong><? echo number_format($upcharge,2); ?></strong></td>
				<td>&nbsp;</td> 
            </tr>
            <tr bgcolor="#dddddd">
                <td colspan="11" align="right"><strong>Discount</strong></td>
                <td align="right"><strong><? echo number_format($discount,2); ?></strong></td>
				<td>&nbsp;</td> 
            </tr>
            <tr bgcolor="#dddddd">
                <td colspan="11" align="right"><strong>Net Total</strong></td>
                <td align="right"><strong><? echo number_format($net_total_amount,2); ?></strong></td>
				<td>&nbsp;</td> 
            </tr>
            </tfoot>
        </table>
        <table>
            <tr height="20"></tr>
            <tr>
                <td valign="top"><strong>In-Words </strong></td>
                <td>: <? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
            </tr>
			<tr>
                <td valign="top"><strong>NOTE </strong></td>
                <td>: <? echo $attention;?></td>
            </tr>
            <tr>
            <tr height="50"></tr>
        </table>
        <?
        echo get_spacial_instruction($mst_id,"100%",152);
        //echo signature_table(257, $company_id, "1000px");
        //echo signature_table(257, $company_id, "1197px", "", 70, $insertUserId);
        echo signature_table(257, $company_id, "1197px", "", 70, $user_arr[$user_id]);
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
    $company_address = array();
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


	$sql ="SELECT a.id as ID, a.pi_date as PI_DATE, a.pi_validity_date as PI_VALIDITY_DATE, a.pi_revised_date as PI_REVISED_DATE, a.pi_number as PI_NUMBER, a.within_group as WITHIN_GROUP, a.buyer_id as BUYER_ID, a.hs_code as HS_CODE, a.advising_bank as ADVISING_BANK, a.item_category_id as ITEM_CATEGORY_ID,a.upcharge as UPCHARGE,a.discount as DISCOUNT,a.net_total_amount as NET_TOTAL_AMOUNT, b.id as dtls_id, b.work_order_no as WORK_ORDER, b.work_order_id as WORK_ORDER_ID, b.booking as BOOKING, b.hs_code as HS_CODE_DTLS,b.gmts_item_id as GMTS_ITEM_ID, b.item_desc as ITEM_DESC, b.uom as UOM, b.quantity as QUANTITY, b.amount as AMOUNT, c.size_id as SIZE_ID, c.color_id as COLOR_ID, c.size_name as SIZE_NAME, d.section as SECTION, d.buyer_style_ref as BUYER_STYLE_REF, d.buyer_buyer as BUYER_BUYER, e.job_no_prefix_num as WORK_ORDER_NO  from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c,subcon_ord_dtls d,subcon_ord_mst e where a.id=b.pi_id and a.id=$mst_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and e.id=d.mst_id and a.exporter_id=$company_id and a.item_category_id=$cbo_item_category_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0";
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
						{?><strong><? echo $company_arr[$buyer_id] ?></strong> <br><? echo $company_address[$buyer_id]; }
						else if ($within_group==2)
						{?><strong><? echo $buyer_arr[$buyer_id] ?></strong> <br><? echo $buyer_details_arr[$buyer_id]["buyer_address"]; }
					?>
				</td>
				<td colspan="7" style="font-size: 18px;">
					<strong>DELIVERY TO/CONSIGNEE: </strong><br>
					<? 
						if ($within_group==1) 
						{ ?><strong><?echo $company_arr[$buyer_id];  ?></strong> <br> <?echo $company_address[$buyer_id]; }
						else if ($within_group==2)
						{ ?><strong><?echo $buyer_arr[$buyer_id]; ?></strong> <br><?echo $buyer_details_arr[$buyer_id]["buyer_address"]; }
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
                <!-- <th width="80">Item Group</th> -->
                <th width="180">Item Description</th>
                <th width="120">Buyer's Buyer</th>
                <th width="120">Buyer's Style</th>
                <th width="80">HS Code</th>
                <th width="145">Job No</th>
                <th width="145">Work Order No</th>
                <th width="85">Item Color</th>
                <th width="180">Size</th>
                <th width="75">Qty</th>
				<th width="45">UOM</th>
                <th width="75">Price<br>/USD</th>
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
                       /* if($tempArrGroup[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrGroup[$key]?>" style="word-break: break-word;"><? echo $item_group_arr[$row['gmts_item_id']]; ?></td>
                        <?
                        }*/
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
                    <td colspan="8" align="right"></td>
                    <td align="right"><strong>Total</strong></td>
                    <td align="right"><strong><? echo number_format($tot_quantity,2); ?></strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right"><strong><? echo number_format($tot_amount,2); ?></strong></td>
                </tr>
				<tr>
                <td colspan="13" valign="top"><strong>In-Words: <? echo number_to_words(number_format($tot_amount,2),'USD','Cent');?></strong></td>
               
            </tr>
            </tfoot> 
        </table>
		<br>
       
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

    $sql ="SELECT a.id as ID, a.pi_date as PI_DATE, a.pi_validity_date as PI_VALIDITY_DATE, a.pi_revised_date as PI_REVISED_DATE, a.pi_number as PI_NUMBER, a.within_group as WITHIN_GROUP, a.buyer_id as BUYER_ID, a.hs_code as HS_CODE, a.advising_bank as ADVISING_BANK, a.item_category_id as ITEM_CATEGORY_ID,a.upcharge as UPCHARGE,a.discount as DISCOUNT,a.net_total_amount as NET_TOTAL_AMOUNT, b.id as dtls_id, b.work_order_no as WORK_ORDER, b.work_order_id as WORK_ORDER_ID, b.booking as BOOKING, b.hs_code as HS_CODE_DTLS,b.gmts_item_id as GMTS_ITEM_ID, b.item_desc as ITEM_DESC, b.uom as UOM, b.quantity as QUANTITY, b.amount as AMOUNT, c.size_id as SIZE_ID, c.color_id as COLOR_ID, c.size_name as SIZE_NAME, c.ply as PLY, e.length as LENGTH, e.width as WIDTH, e.height as HEIGHT, e.flap as FLAP, e.gusset as GUSSET, e.thickness as TICKNESS, e.measurementid as MEASUREMENTID, d.section as SECTION, d.buyer_style_ref as BUYER_STYLE_REF, d.buyer_buyer as BUYER_BUYER , f.job_no_prefix_num as WORK_ORDER_NO from com_export_pi_mst a, com_export_pi_dtls b, subcon_ord_breakdown c left join subcon_ord_breakdown_size_info e on e.subconordbreakdownid  = c.id,subcon_ord_dtls d, subcon_ord_mst f
    where a.id=b.pi_id and a.id=$mst_id and b.work_order_dtls_id=c.id and c.mst_id=d.id and a.exporter_id=$company_id and a.item_category_id=$cbo_item_category_id and f.id=d.mst_id and  a.status_active in(1,2,3) and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0 order by c.id asc ";
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
        $all_data_arr[$key]['THICKNESS']=$val['TICKNESS'];
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

    $tbl_width=1655;
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
                <td colspan="9" width="50%" style="font-size: 20px;"><strong>PROFORMA INVOICE:</strong>&nbsp;<? echo $pi_number; ?></td>
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
                    {?> <strong> <? echo $buyer_arr[$buyer_id] ?> </strong> <br> <?echo $buyer_details_arr[$buyer_id]["buyer_address"]; }
                    ?>
                </td>
                <td colspan="10" style="font-size: 18px;">
                    <strong>DELIVERY TO/CONSIGNEE: </strong><br>
                    <?
                    if ($within_group==1)
                    {echo $company_arr[$buyer_id].'<br>'.$company_address[$buyer_id]; }
                    else if ($within_group==2)
                    { ?> <strong> <? echo $buyer_arr[$buyer_id] ?> <strong> <br> <? echo $buyer_details_arr[$buyer_id]["buyer_address"]; }
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
            <th width="185">Item Description</th>
            <th width="85">Buyer's Buyer</th>
            <th width="130">Buyer's Style</th>
            <th width="85">HS Code</th>
            <th width="145">Job No</th>
            <th width="145">Work Order No</th>
            <th width="50">Ply</th>
            <th width="60">L<br>(CM)</th>
            <th width="60">W<br>(CM)</th>
            <th width="60">H<br>(CM)</th>
            <th width="60">F<br>(CM)</th>
            <th width="60">G<br>(CM)</th>
            <th width="65">Thick ness</th>
            <th width="75">Qty</th>
            <th width="55">UOM</th>
            <th width="85">Price<br>/USD</th>
            <th width="75">Amount<br>USD</th>
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
                   /* if($tempArrGroup[$key] > 0){
                        ?>
                        <td rowspan="<?=$tempArrGroup[$key]?>" style="word-break: break-word;"><? echo $item_group_arr[$row['gmts_item_id']]; ?></td>
                        <?
                    }*/
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
                        <td style="word-break: break-word;" rowspan="<?=$tempArrWorkorder[$key]?>" align="center"><? echo $row['work_order_no']; ?></td>
                        <?
                    }
                    if($tempArrBooking[$key] > 0){
                        ?>
                        <td style="word-break: break-word;" rowspan="<?=$tempArrBooking[$key]?>" align="center"><? echo $row['booking']; ?></td>
                        <?
                    }
                    ?>  
                    <td align="center"><? echo $row['PLY']; ?></td>
                    <td align="center"><?=(int) $row['LENGTH'] != 0 ? $row['LENGTH'] : ""?></td>
                    <td align="center"><?=(int) $row['WIDTH'] !=  0 ? $row['WIDTH'] : ""?></td>
                    <td align="center"><?= (int) $row['HEIGHT'] !=  0 ? $row['HEIGHT'] : ""?></td>
                    <td align="center"><?= (int) $row['FLAP'] !=  0 ? $row['FLAP'] : ""?></td>
                    <td align="center"><?= (int) $row['GUSSET'] !=  0 ? $row['GUSSET'] : ""?></td>
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
                <td colspan="13" align="right"></td>
                <td align="right"><strong>Total</strong></td>
                <td align="right"><strong><? echo number_format($tot_quantity,2); ?></strong></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><strong><? echo number_format($tot_amount,2); ?></strong></td>
            </tr>
			<tr>
                <td colspan="18" valign="top"><strong>In-Words: <? echo number_to_words(number_format($tot_amount,2),'USD','Cent');?></strong></td>
               
            </tr>
            </tfoot>
        </table>
		<br>
        
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

if($action=="print_new_rpt_15") 
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company_id=$data[0];
	$mst_id=$data[1];
	$system_id = $data[2];

	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$user_arr = return_library_array("select id, user_full_name from user_passwd", 'id', 'user_full_name');
	$count_arr = return_library_array("Select id, yarn_count from  lib_yarn_count where  status_active=1", 'id', 'yarn_count');
	//$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_id' and is_deleted=0","image_location");
	
	// Company info
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$company_id and is_deleted=0 and status_active=1");
  	foreach($sql_company as $row) 
  	{
		if($row[csf('plot_no')] !='') $plot_no = $row[csf('plot_no')].', ';
		if($row[csf('level_no')] !='') $level_no = $row[csf('level_no')].', ';
		if($row[csf('road_no')] !='') $road_no = $row[csf('road_no')].', ';
		if($row[csf('block_no')] !='') $block_no = $row[csf('block_no')].', ';
		if($row[csf('city')] !='') $city = $row[csf('city')];
		if($row[csf('zip_code')] !='') $zip_code = $row[csf('zip_code')].', ';
		if($row[csf('country_id')] !=0) $country = $country_arr[$row[csf('country_id')]];
		if($row[csf('email')] !='') $company_email = "Email:&nbsp;".$row[csf('email')];
		if($row[csf('contact_no')] !='') $contact_no = "Phone:&nbsp;".$row[csf('contact_no')];
		if($row[csf('bin_no')] !='') $bin_no = $row[csf('bin_no')];
		if($row[csf('vat_number')] !='') $vat_number = $row[csf('vat_number')];
		if($row[csf('website')] !='') $website_no = "Website:&nbsp;".$row[csf('website')];		
		$plot_city=$plot_no.$city;
		$group_id=$row[csf('group_id')];
	}

	// Group info
	$sql_group = sql_select("SELECT * FROM lib_group WHERE id=$group_id and is_deleted=0 and status_active=1");
  	foreach($sql_group as $row)
  	{
		if($row[csf('address')] !='') $group_address = $row[csf('address')];
		if($row[csf('contact_no')] !='') $group_contact_no = "Phone:&nbsp;".$row[csf('contact_no')];
		if($row[csf('website')] !='') $group_website_no = "Website:&nbsp;".$row[csf('website')];
	}

	$sql ="SELECT a.id, a.pi_date, a.buyer_id, a.pi_revised_date, a.pi_validity_date, a.last_shipment_date, a.pi_number, a.within_group, a.currency_id, a.hs_code, a.advising_bank, a.item_category_id, a.pi_revise, a.net_total_amount, a.remarks as mst_remarks, a.pay_term, a.tenor, b.id as dtls_id, b.work_order_no, b.work_order_id, b.work_order_dtls_id, b.color_id, b.yarn_composition_id, b.yarn_type_id, b.count_id as count_type_id, b.buyer_job, b.cust_buyer, b.color_range_id, b.hs_code as dtls_hs_code, b.uom, b.quantity, b.rate, b.amount, b.remarks, c.style_ref,a.swift_code from com_export_pi_mst a, com_export_pi_dtls b, yd_ord_dtls c where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.id=$mst_id and a.exporter_id=$company_id and a.item_category_id in(68,69) and a.status_active in(1,2,3) and a.is_deleted=0 and b.quantity>0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	$sql_res=sql_select($sql);
	$check_master_arr=array();
	$all_data_arr=array();
	foreach ($sql_res as $val) 
	{		
		if ($check_master_arr[$val[csf('id')]]==""){
			$check_master_id[$val[csf('id')]]=$val[csf('id')];
			$pi_date=$val[csf('pi_date')];
			$buyer_id=$val[csf('buyer_id')];
			$pi_revised_date=$val[csf('pi_revised_date')];
			$last_shipment_date=$val[csf('last_shipment_date')];
			$pi_validity_date=$val[csf('pi_validity_date')];	
			$pi_number=$val[csf('pi_number')];
			$mst_remarks=$val[csf('mst_remarks')];
			$pay_term=$val[csf('pay_term')];
			$tenor=$val[csf('tenor')];
			$currency_id=$val[csf('currency_id')];
			$pi_revise=$val[csf('pi_revise')];
			$item_category_id=$val[csf('item_category_id')];
			$advising_bank=$val[csf('advising_bank')];
			$hs_code=$val[csf('hs_code')];
			$swift_code_page=$val[csf('swift_code')];
			$item_category_name=$export_item_category[$val[csf('item_category_id')]];
			$uom_first_row=$unit_of_measurement[$val[csf('uom')]];	
		}

		if ($val[csf('item_category_id')]==69)  // sales
		{
			$keys=$val[csf('yarn_composition_id')].'##'.$val[csf('yarn_type_id')].'##'.$val[csf('count_type_id')].'##'.$val[csf('color_range_id')];
			//echo $keys.'system';
			$all_data_arr[$keys]['work_order_no']=$val[csf('work_order_no')];
			$all_data_arr[$keys]['work_order_id']=$val[csf('work_order_id')];
			$all_data_arr[$keys]['yarn_composition_id']=$val[csf('yarn_composition_id')];
			$all_data_arr[$keys]['yarn_type_id']=$val[csf('yarn_type_id')];
			$all_data_arr[$keys]['count_type_id']=$val[csf('count_type_id')];
			$all_data_arr[$keys]['color_range_id']=$val[csf('color_range_id')];
			$all_data_arr[$keys]['cust_buyer'].=$val[csf('cust_buyer')].',';
			$all_data_arr[$keys]['buyer_job'].=$val[csf('buyer_job')].',';
			$all_data_arr[$keys]['style_ref'].=$val[csf('style_ref')].',';
			$all_data_arr[$keys]['dtls_hs_code'].=$val[csf('dtls_hs_code')].',';
			$all_data_arr[$keys]['uom']=$val[csf('uom')];
			$all_data_arr[$keys]['quantity']+=$val[csf('quantity')];
			$all_data_arr[$keys]['amount']+=$val[csf('rate')]*$val[csf('quantity')];
			$all_data_arr[$keys]['remarks'].=$val[csf('remarks')].',';
		}
		else  //68 service
		{

			if ($check_all_data_arr[$val[csf('dtls_id')]]=="")
			{
				$all_data_arr[$val[csf('dtls_id')]]['work_order_no']=$val[csf('work_order_no')];
				$all_data_arr[$val[csf('dtls_id')]]['work_order_id']=$val[csf('work_order_id')];
				$all_data_arr[$val[csf('dtls_id')]]['yarn_composition_id']=$val[csf('yarn_composition_id')];
				$all_data_arr[$val[csf('dtls_id')]]['yarn_type_id']=$val[csf('yarn_type_id')];
				$all_data_arr[$val[csf('dtls_id')]]['count_type_id']=$val[csf('count_type_id')];
				$all_data_arr[$val[csf('dtls_id')]]['color_range_id']=$val[csf('color_range_id')];
				$all_data_arr[$val[csf('dtls_id')]]['cust_buyer']=$val[csf('cust_buyer')];
				$all_data_arr[$val[csf('dtls_id')]]['buyer_job']=$val[csf('buyer_job')];
				$all_data_arr[$val[csf('dtls_id')]]['style_ref']=$val[csf('style_ref')];
				$all_data_arr[$val[csf('dtls_id')]]['dtls_hs_code']=$val[csf('dtls_hs_code')];
				$all_data_arr[$val[csf('dtls_id')]]['uom']=$val[csf('uom')];
				$all_data_arr[$val[csf('dtls_id')]]['rate']=$val[csf('rate')];
				$all_data_arr[$val[csf('dtls_id')]]['quantity']+=$val[csf('quantity')];
				$all_data_arr[$val[csf('dtls_id')]]['amount']+=$val[csf('rate')]*$val[csf('quantity')];
				$all_data_arr[$val[csf('dtls_id')]]['remarks']=$val[csf('remarks')];
			}
		}				
	}
	//echo '<pre>';print_r($all_data_arr);

	$sql_buyer = sql_select("select id, buyer_name, country_id, buyer_email, address_1, address_2, address_3, address_4 from lib_buyer where id=$buyer_id and status_active=1 and is_deleted=0");    
    foreach ($sql_buyer as $row) 
    {
    	$buyer_address="";
        if ($row[csf('address_1')] !='') $buyer_address = $row[csf('address_1')];
    }	

    if ($advising_bank != ""){
    	$sql_bank = sql_select("select a.id, a.bank_name, a.branch_name, a.address, a.swift_code, b.account_type, b.account_no from lib_bank a, lib_bank_account b where a.id=$advising_bank and a.id=b.account_id and a.advising_bank=1 and a.is_deleted=0 and a.status_active=1 and b.account_type=10 and b.status_active=1 and b.is_deleted=0 and company_id=$company_id");
	  	foreach($sql_bank as $row)
	  	{
	  		$bank_name=$row[csf('bank_name')];
	  		$branch_name=$row[csf('branch_name')];
	  		$address=$row[csf('address')];
	  		$swift_code=$row[csf('swift_code')];
	  		$account_no=$row[csf('account_no')];
		}
    }

	if ($pay_term==2) $colspan=2;
	else $colspan="";
	
	$diff = strtotime($pi_validity_date) - strtotime($pi_date);
	$pi_validity_days= round($diff / 86400);
	$com_dtls = fnc_company_location_address($company, $location, 2);

	$currency_sign_arr = array(1 => "৳", 2 => "$", 3 => "€", 4 => "CHF", 5 => "S$", 6 => "£", 7 => "¥");
	$data_array = sql_select("select id, terms, terms_prefix from wo_booking_terms_condition where booking_no='$mst_id' and entry_form=152 order by id");

	$data_img_array=sql_select("select image_location from common_photo_library where master_tble_id='$company_id' and form_name='company_details' and is_deleted=0 and file_type=1");
    ?>
    <style type="text/css">
    	table>tr>th,td{word-break: break-all;}
		.float-container {
   			padding: 10px;
		}
    </style>
	<div style="width:1200px">
		<div class="float-container">
			<table cellspacing="0" border="0" style="width: 20%; float: left; ">
				<div class="green">
					<tr>
						<td align="left">
						<?
						if ($data[8]!="") $path=$data[8];
						else $path="../../";
			
						foreach($data_img_array as $img_row)
						{
							?>
							<img src='<? echo $path.$img_row[csf('image_location')]; ?>' height='150'  align="middle" />
							<?
						}
						?>
						</td>
					</tr>
				</div>
			</table>
			<table cellspacing="0" border="0" style="float: left;  width: 80%;">
				<div class="blue">
					<tr>      
					<td colspan="3" align="center"  style="font-size: 65px;"><strong> <? echo $company_arr[$company_id]; ?></strong></td>
					</tr>
					<tr>
						<td colspan="3" align="center" style="width: 1100px;font-size: 19px;"><strong >Head Office:</strong><? echo $group_address; ?></td>
					</tr>
					<tr>
						<td colspan="3" align="center" style="font-size: 19px; font-size: 19px;" style="width: 1100px;"><? echo $group_contact_no; ?>,&nbsp;<? echo $group_website_no; ?></td>
					</tr>
					<tr>
						<td colspan="3" align="center" style="width: 1100px; font-size: 19px;"><strong>Factory:</strong><? echo $plot_city; ?></td>
					</tr>
					<tr>
						<td colspan="3" align="center" style="width: 1100px;"><? echo $contact_no; ?>,&nbsp;<? echo $website_no; ?></td>
					</tr>
				</div>
			</table>
				<br>
				<br>			
				<hr>
		</div>
		<table width="1200" cellspacing="0" border="0">
	        <tr>      
				<td colspan="3" style="font-size: 16px; text-align: center; text-decoration:underline;"><strong>PROFORMA INVOICE</strong></td>
	        </tr>
		</table>
		<br>

		<table class="rpt_table" width="1200" cellspacing="1" rules="all" border="1">
			<tr>
				<td width="200"><strong>Importer</strong></td>
				<td width="300"><? echo $buyer_arr[$buyer_id]; ?></td>
				<td width="150"><strong>PI No</strong></td>
				<td width="200"><? echo $pi_number; if ($pi_revise > 0) echo "&nbsp;($pi_revise_array[$pi_revise])";?></td>
				<td width="150"><strong>Yarn Cal.on</strong></td>
				<td width="200" colspan="<? echo $colspan; ?>"><? if ($item_category_id==69) echo 'Finish Weight'; else echo 'Grey Weight'; ?></td>
			</tr>
			<tr>
				<td rowspan="2"><strong>Address</strong></td>
				<td rowspan="2"><? echo $buyer_address; ?></td>
				<td rowspan="2"><strong>PI Date</strong></td>
				<td rowspan="2"><? echo change_date_format($pi_date); ?></td>
				<td><strong>Pay Method</strong></td>
				<td colspan="<? echo $colspan; ?>">Credit Doc.</td>
			</tr>
			<tr>
				<td><strong>LC Mode</strong></td>
				<? if ($pay_term==2) { ?>
					<td><? echo 'Deferred'; ?></td>
					<td><? echo $tenor.'&nbsp;Days'; ?></td>
				<? } else { ?>
					<td colspan="2">At Sight</td>
				<? } ?>
			</tr>
			<tr>
				<td ><strong>Delivery Last Date</strong></td>
				<td><? echo change_date_format($last_shipment_date); ?></td>
				<td><strong>Company Bank</strong></td>
				<td></td>
				<td><strong>Terms. Dlv.</strong></td>
				<td colspan="<? echo $colspan; ?>">N/A</td>
			</tr>
			<tr>
				<td><strong>Valid Upto:</strong></td>
				<td><? echo change_date_format($pi_validity_date); ?></td>
				<td><strong>H.S. Code</strong></td>
				<td><? echo $hs_code; ?></td>
				<td><strong>Nom. Place</strong></td>
				<td colspan="<? echo $colspan; ?>">Factory</td>
			</tr>
			<tr>
				<td><strong>Advising Bank</strong></td>
				<td colspan="6"><? echo $bank_name.'<br>'.$branch_name.',&nbsp;'.$address.'<br>Bank Account Number:&nbsp;'.$account_no.'<br><b>Swift Code:&nbsp;'.$swift_code_page;'</b>'?></td> 
			</tr>
			<tr>
				<td><strong>Beneficiary Address</strong></td>
				<td colspan="6"><? echo $plot_city; ?></td>
			</tr>
			<tr>
				<td><strong>Remarks</strong></td>
				<td colspan="6"><? echo $mst_remarks; ?></td>
			</tr>
		</table>		
		<br>
        <table width="1200" cellspacing="0" align="center" border="1" rules="all" class="rpt_table">
            <thead bgcolor="#dddddd" align="center">                
				<th width="30">SL</th>
				<? if ($item_category_id==69) { ?>
					<th width="100">Style</th>
					<th width="100">Buyer Job</th>
				<? } else { ?>
					<th width="150">Style</th>
					<th width="150">Buyer Job</th>
				<? } ?>	
				<th width="100">End Customer</th>
				<th width="350">Description of Goods</th>
				<th width="100">Quantity</th>
				<th width="70">Unit</th>
				<th width="80">Price/<? echo $uom_first_row; ?>.-<? echo $currency_sign_arr[$currency_id]; ?></th>
				<th>Total Price-<? echo $currency_sign_arr[$currency_id]; ?></th>		
				<? if ($item_category_id==69) { ?>
					<th width="100">H.S Code</th>
				<? } ?>
            </thead>
            <tbody>
                <?
                $i=1;
                $tot_wo_qty=$tot_amount=0;
				$style_ref=$buyer_job=$cust_buyer=$descriptionofgoods=$uom=$dtls_hs_code="";
				
				if ($item_category_id==68) //Service
				{				
					foreach ($all_data_arr as $row) 
					{
						$color_range_value = '';
						if($item_category_id==68 || $item_category_id==69)
						{	

							$color_range_value = str_replace("Color", "shade",ucwords(strtolower($color_range[$row['color_range_id']])));
						}
						else
						{
							$color_range_value = $color_range[$row['color_range_id']];
						}
												
						$style_ref.=$row['style_ref'].'<br>';
						$buyer_job.=$row['buyer_job'].'<br>';
						$dtls_hs_code.=$row['dtls_hs_code'].'<br>';
						if ($check_cust_buyer_arr[$row['cust_buyer']]==""){
							$cust_buyer.=$row['cust_buyer'].'<br>';
							$check_cust_buyer_arr[$row['cust_buyer']]=$row['cust_buyer'];
						}

						if ($i==1){
							$descriptionofgoods='Yarn Dyeing Charge'.'<br>'.$composition[$row['yarn_composition_id']].'&nbsp'.$yarn_type[$row['yarn_type_id']].', '.$count_arr[$row['count_type_id']].'<br>'.$color_range_value.'<br>'.'(Pre-Treatment, Dyeing, Washing and Finishing)';
							$uom=$unit_of_measurement[$row['uom']];						
						}
						
						$tot_quantity+=$row['quantity'];
						$tot_amount+=$row['amount'];
						$i++;					
					}
					?>	
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30">1</td>
						<td width="150"><? echo rtrim($style_ref,'<br>'); ?></td>
						<td width="150"><? echo rtrim($buyer_job,'<br>'); ?></td>
						<td width="100"><? echo rtrim($cust_buyer,'<br>'); ?></td>
						<td width="350" align="center"><? echo $descriptionofgoods; ?></td>
						<td width="100" align="right"><? echo number_format($tot_quantity,2); ?></td>
						<td width="70" align="center"><? echo $uom; ?></td>
						<td width="80" align="right"><? echo $currency_sign_arr[$currency_id].'&nbsp;'.number_format($tot_amount/$tot_quantity,2); ?></td>
						<td align="right"><? echo $currency_sign_arr[$currency_id].'&nbsp;'.number_format($tot_amount,2); ?></td>						              
					</tr>
					<?
				}
				else  // Sales
				{
					foreach ($all_data_arr as $row) 
					{

						$color_range_value = '';
						if($item_category_id==68 || $item_category_id==69)
						{	

							$color_range_value = str_replace("Color", "shade",ucwords(strtolower($color_range[$row['color_range_id']])));
						}
						else
						{
							$color_range_value = $color_range[$row['color_range_id']];
						}
						
						$descriptionofgoods='Dyed Yarn'.'<br>'.$composition[$row['yarn_composition_id']].'&nbsp'.$yarn_type[$row['yarn_type_id']].', '.$count_arr[$row['count_type_id']].'<br>'.$color_range_value.'<br>'.'(Pre-Treatment, Dyeing, Washing and Finishing)';
						$style_ref=implode(",", array_unique(explode(",", rtrim($row['style_ref'],','))));
						$buyer_job=implode(",", array_unique(explode(",", rtrim($row['buyer_job'],','))));
						$cust_buyer=implode(",", array_unique(explode(",", rtrim($row['cust_buyer'],','))));
						$dtls_hs_code=implode(",", array_unique(explode(",", rtrim($row['dtls_hs_code'],','))));
						?>	
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="100"><? echo $style_ref; ?></td>
							<td width="100"><? echo $buyer_job; ?></td>
							<td width="100"><? echo $cust_buyer; ?></td>
							<td width="350" align="center"><? echo $descriptionofgoods; ?></td>
							<td width="100" align="right"><? echo number_format($row['quantity'],2); ?></td>
							<td width="70" align="center"><? echo $unit_of_measurement[$row['uom']]; ?></td>
							<td width="80" align="right"><? echo $currency_sign_arr[$currency_id].'&nbsp;'.number_format($row['amount']/$row['quantity'],2); ?></td>
							<td align="right"><? echo $currency_sign_arr[$currency_id].'&nbsp;'.number_format($row['amount'],2); ?></td>
							<? if ($item_category_id==69) { ?>
								<td width="100" align="center"><? echo $dtls_hs_code; ?></td>
							<? } ?>							                
						</tr>
						<?
						$i++;
						$tot_quantity+=$row['quantity'];
						$tot_amount+=$row['amount'];				
					}
				}
				?>
				 <tr bgcolor="#dddddd">
                    <td colspan="5" align="right"><strong>Total</strong></td>
                    <td align="right"><strong><? echo number_format($tot_quantity,2); ?></strong></td>
                    <td>&nbsp;</td>
					<td>&nbsp;</td>
                    <td align="right"><strong><? echo $currency_sign_arr[$currency_id].'&nbsp;'.number_format($tot_amount,2); ?></strong></td>
					<? if ($item_category_id==69) { ?>
						<td>&nbsp;</td>
					<? } ?>						
                </tr>                   
            </tbody>
        </table>
        <table style="padding-left: 20px;">
            <tr height="20"></tr>
            <tr>
                <td valign="top"><strong>In-Words: </strong></td>
                <td><? echo number_to_words(number_format($tot_amount,2, '.', ''),'USD','CENT');?> Only</td>

            </tr>
            <tr> 
            <tr height="20"></tr>
        </table>

		<table style="padding-left: 20px;" width="1200" class="rpt_table" cellpadding="0" cellspacing="0">
		    <tr >
				<td width="150" ><b>Tenor :</b></td>
				<td ><b>By Irrevocable L/C at : <? if ($pay_term==2) echo $tenor.'&nbsp;Days'; else echo 'At Sight'; ?><b></td>
			</tr>

			<tr >
				<td >Vat Reg :</td>
				<td ><? echo $vat_number; ?></td>
			</tr>
			<tr >
				<td >PI Validity :</td>
				<td ><? echo $pi_validity_days.'&nbsp;days from the date of PI number.'; ?></td>
			</tr>
			<?
			$data_array = sql_select("select id, terms, terms_prefix from wo_booking_terms_condition where booking_no='$mst_id' and entry_form=152 order by id");

			if (count($data_array)>0)
			{			
				foreach ($data_array as $row)
				{				
					?>
					<tr >
						<td ><? echo $row[csf('terms_prefix')]; ?></td>
						<td ><? echo $row[csf('terms')]; ?></td>
					</tr>
					<?
				}
			}	
			?>		
		</table>

		<table width="1200" cellpadding="0" cellspacing="0" style="padding-top: 100px;">
            <tr>&nbsp;</tr>     
        </table>
		
		<table width="1200" cellpadding="0" cellspacing="0" >
			<tr>
				<td style=" width:600px; text-align: center; border-right: 1px solid black; border-top: 1px solid black; border-left: 1px solid black;"><strong><? echo 'For&nbsp;'.$company_arr[$company_id]; ?></strong></td>
				<td style="width:600px; text-align: center;border-top: 1px solid black; border-right: 1px solid black;"></td>
			</tr>
			<tr height="100">
				<td style="width:600px; text-align: center; border-left: 1px solid black;border-right: 1px solid black;"></td>
				<td style="width:600px; text-align: center; border-right: 1px solid black;"></td>
			</tr>
			<tr >
				<td style="width:600px; text-align: center;  border: 1px solid black;" ><strong>Authorized Signature</strong></td>
				<td style="width:600px; text-align: center; border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;" ><strong>Buyer's Authorized Signature</strong></td>
			</tr>
		</table>
	</div>
    <?
	exit();	 
}
?>


 