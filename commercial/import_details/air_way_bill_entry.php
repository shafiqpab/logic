<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for Air Way Bill Entry
Functionality	:	
JS Functions	:
Created by		:	MD. SAIDUL ISLAM REZA 
Creation date 	: 	20-9-2020 
Updated by 		: 	Nahin
Update date		: 	  	   
QC Performed BY	:		
QC Date			:	
Comments		: 
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Air Way Bill Entry", "../../", 1, 1,'','1','');
 // $conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)","",$con);


?>	
 
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission='<? echo $permission; ?>';


	function fn_get_style_name()
	{
		var company_id = $("#cbo_company_id").val();	  
		var style_status = $("#cbo_style_status").val();	  
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var page_link = 'requires/air_way_bill_entry_controller.php?style_status='+style_status+'&company_id='+company_id+'&action=style_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Style List View', 'width=915px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		
		{ 
			
			var data=this.contentDoc.getElementById("cbo_po_id").value.split("_");
			// alert(data)
			 $("#txt_style_name").val(data[2]);
			//$("#txt_style_name").val(data[2]);
			show_list_view(company_id+"**"+data[0]+"**"+data[1]+"**"+data[2],'show_po_listview','list_view','requires/air_way_bill_entry_controller','setFilterGrid("table_body",-1);');
			calculate_balance(1);
		}
		
	}

    function calculate_balance(i)
   {
	var numRow = $('table#table_body tbody tr').length; 
	var tot_amount=0;
		for(var i=1; i<numRow; i++)
		{
			var amnt=$('#txt_total_charge_usd_'+i).val();
			tot_amount=tot_amount*1+amnt*1;
		}

		$('#tot_amount').val(tot_amount);
		var tot_remain=$('#txt_total_charge_usd').val()*1-tot_amount;
		$('#tot_remain').text(tot_remain);
   }

   function fn_amount_propotionate()
   {	 
       var txt_amount=$('#txt_total_charge_usd').val()*1;
		var tblRow = $("#table_body tbody tr").length-1;
		var tot_po_qnty=$('#td_tot_po_qnty').text()*1;
		//alert(txt_amount+"="+tblRow+"="+tot_po_qnty);return;
		var tot_distribute_amt=0; var rest_amt=0;
		for(var i=1; i<=tblRow; i++)
		{
			var po_qnty=$('#txt_total_charge_usd_'+i).attr('title')*1;
			//var issue_qnty=(txt_amount/tot_po_qnty)*po_qnty;
			var issue_qnty=(po_qnty/tot_po_qnty)*txt_amount;
			if(i==tblRow)
			{				
				rest_amt=txt_amount-tot_distribute_amt;
				//alert(rest_amt+"="+txt_amount+"="+tot_distribute_amt);//return;
				$('#txt_total_charge_usd_'+i).val(rest_amt);
			}
			else
			{
				tot_distribute_amt += number_format (issue_qnty, 4, '.', "")*1 ;
				//$('#txt_amount_'+i).val(issue_qnty.toFixed(4));
				$('#txt_total_charge_usd_'+i).val(number_format (issue_qnty, 4, '.', ""));
			}
			
		}
		calculate_balance(tblRow);
	}
   
	
	function fn_get_sys_id()
	{
		var company_id = $("#cbo_company_id").val();	  
		var style_status = $("#cbo_style_status").val();	  
		if (form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		var page_link = 'requires/air_way_bill_entry_controller.php?style_status='+style_status+'&company_id='+company_id+'&action=sys_id_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Sys Id', 'width=1015px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{ 
			// var sys_id=this.contentDoc.getElementById("txt_selected_name").value;
			var data=this.contentDoc.getElementById("txt_selected_name").value.split("_");

			$("#update_id").val(data[0]); 
			//$("#txt_system_id").val(dataArr[1]); 
			get_php_form_data(data[0], "get_form_data", "requires/air_way_bill_entry_controller" );
			set_button_status(1, permission, 'fnc_air_way_bill_entry',1); 
			// alert(sys_id)
			show_list_view(data[1]+"*"+company_id,'show_po_listview_master','list_view','requires/air_way_bill_entry_controller','setFilterGrid("table_body",-1);');
		}
	}
	
	

	function fnc_air_way_bill_entry(operation)
	{

		if(operation==4)
		{
			if (form_validation('update_id','Save Data First')==false)
			{
				alert("Save Data First");
				return;
			}
			else
			{
				print_report( $('#update_id').val(), "print_report", "requires/air_way_bill_entry_controller" ) ;
			 	return;
			}
		}
		
		if($("#hidden_posted_in_account").val()*1==1)
		{
			alert("Already Posted In Accounts.Save,Update & Delete Not Allowed.");
            return;
		}

		if (form_validation('cbo_company_id*cbo_buyer_id*cbo_currier_name*txt_air_way_bill*cbo_country_id','Company id*Buyer id*Currier Name*Air way bill*Country id')==false )
		{
			return;
		}

		var dataString=''; var j=0; 
		var txt_amount=$('#txt_total_charge_usd').val()*1;
		var tot_amount=$('#tot_amount').val()*1;
		var tot_row=$('table#table_body tbody tr').length-1;
		for(i=1; i<=tot_row; i++)
		{

			   
			var txt_amount=$('#txt_total_charge_usd_'+i).val()*1;
			// var txt_amount=$('#txt_amount_'+i).val()*1;
			var po_id=$('#po_id_'+i).val();
			var jobNo=$('#job_no_'+i).text();
			var txt_dtls_id=$('#txt_dtls_id_'+i).val()*1;
			var updateid=$('#updateid_'+i).val()*1;
			
			if(txt_amount>0)
			{
				j++;
				dataString+='&txt_amount' + j + '=' + txt_amount + '&po_id' + j + '=' + po_id + '&jobNo' + j + '=' + jobNo+ '&txt_dtls_id' + j + '=' + txt_dtls_id+'&updateid_' + j + '=' + updateid;
			}

			// alert(dataString); return
		}

 		var data="action=save_update_delete&operation="+operation+get_submitted_data_string("cbo_company_id*cbo_buyer_id*cbo_currier_name*txt_air_way_bill*cbo_country_id*cbo_team_leader*cbo_dealing_merchant*cbo_style_status*txt_style_name*txt_style_qty*txt_bill_date*txt_weight*txt_charge_usd*txt_dfs_charge_usd*txt_total_charge_usd*txt_ex_rate*txt_charge_bdt*cbo_approve_status*update_id*txt_system_id","../../")+dataString+'&tot_row='+j;;

		freeze_window(operation);		
		http.open("POST","requires/air_way_bill_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_air_way_bill_entry_response;
	}


	function fnc_air_way_bill_entry_response()
	{
		
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');	
			if( reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('update_id').value = reponse[2];
				document.getElementById('txt_system_id').value = reponse[1];
				document.getElementById("cbo_company_id").disabled = true;
				document.getElementById("cbo_buyer_id").disabled = true;
				document.getElementById("cbo_currier_name").disabled = true;
				document.getElementById("cbo_team_leader").disabled = true;
				document.getElementById("cbo_dealing_merchant").disabled = true;
				set_button_status(1, permission, 'fnc_air_way_bill_entry',1); 
				//reset_form('docsubmFrm_1','','','','','');
				show_msg(reponse[0]);
				
			}


			//
			release_freezing();
		}
	}


	function fnResetForm()
	{
		reset_form('docsubmFrm_1','','','','','');
		$('#cbo_importer_id').attr('disabled',false);
		$('#pi_details_list').find('tr:gt(0)').remove();
 		set_button_status(0, permission, 'fnc_com_office_note_entry',1);	
	}

	function getTotalCharge(){
		var total_charge_usd=(document.getElementById("txt_charge_usd").value*1)+(document.getElementById("txt_dfs_charge_usd").value*1);
		document.getElementById("txt_total_charge_usd").value=(total_charge_usd).toFixed(2);
		
		var ex_rate=(document.getElementById("txt_ex_rate").value*1);
		
		document.getElementById("txt_charge_bdt").value=(total_charge_usd*ex_rate).toFixed(2);

		
		
		
	}



</script>
 
</head> 
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?><br/>
		<fieldset style="width:930px; margin-bottom:10px;">
			<form name="docsubmFrm_1" id="docsubmFrm_1" autocomplete="off" method="POST"  >

				<fieldset style="width:950px;">
					<legend>Air Way Bill Entry</legend>
					<table width="100%" cellspacing="5" cellpadding="5">
						<tr>
							<td colspan="6" align="center">System ID 
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" placeholder="Borwse" onDblClick="fn_get_sys_id();" readonly style="width:200px;" />
                            <input type="hidden" id="hidden_posted_in_account" name="hidden_posted_in_account" value="" />
                            </td>
							
						</tr>
						<tr>
							<td colspan="6" align="center">
                            	<input type="hidden" name="update_id" id="update_id" value=""/>
                            </td>
						</tr>
						<tr>
							<td align="right" class="must_entry_caption">Company Name</td>
							<td>
								<?
								echo create_drop_down( "cbo_company_id", 212, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", 0, "load_drop_down( 'requires/air_way_bill_entry_controller',this.value, 'load_buyer_dropdown', 'buyer_td' );load_drop_down( 'requires/air_way_bill_entry_controller',this.value, 'load_currier_dropdown', 'currier_td' );" );
								?>
							</td>
							<td align="right" class="must_entry_caption">Buyer</td>
							<td id="buyer_td">
								<?
									echo create_drop_down( "cbo_buyer_id",212,array(),'',1,'-Select',1,"",0);
								?>
							</td>
							<td align="right" class="must_entry_caption">Currier Name</td>
							<td id="currier_td">
								<? //Royale International //DPS International Courier //DEX Dreamco Express
								// $air_bill_courierArr
								echo create_drop_down( "cbo_currier_name",212,"",'',1,'-Select',0,"",0);
								?>
							</td>
						</tr>
                        
						<tr>
							<td align="right" class="must_entry_caption">Air Way Bill</td>
							<td>
                                <input style="width:200px " name="txt_air_way_bill" id="txt_air_way_bill" class="text_boxes"  />
							</td>
							<td align="right" class="must_entry_caption">Destination</td>
							<td>
								<?
								echo create_drop_down( "cbo_country_id",212,"select ID,COUNTRY_NAME FROM LIB_COUNTRY WHERE STATUS_ACTIVE=1 AND IS_DELETED=0","ID,COUNTRY_NAME",1,'-Select',0,"",0);
								?>
							</td>
							<td align="right">Team Leader</td>
							<td>
								<? echo create_drop_down( "cbo_team_leader", 212, "select id,team_leader_name from lib_marketing_team where project_type in (1,2,6) and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/air_way_bill_entry_controller', this.value, 'load_dealing_merchant_dropdown', 'marchant_td' );" ); ?> 
							</td>
						</tr>
                        
						<tr>
							<td align="right">Deling Mar.</td>
							<td id="marchant_td">
								<?
								echo create_drop_down( "cbo_dealing_merchant", 212, array(),"", 1, "-- Select --", 0, "" );
								?>
							</td>
							<td align="right">Style Status</td>
							<td>
								<?
								echo create_drop_down( "cbo_style_status", 212, array(1=>"Before Order",2=>"After Order"),"", 1, "-- Select --", 0, "" );
								?>
							</td>
							<td align="right">Style Name</td>
							<td>
								<input style="width:200px " name="txt_style_name" id="txt_style_name" class="text_boxes"  placeholder="Write/Browse" onDblClick="fn_get_style_name()" />
							</td>
						</tr>
                        
						<tr>
							<td align="right" class="must_entry_caption">Style Qty.</td>
							<td>
								<input style="width:200px " name="txt_style_qty" id="txt_style_qty" class="text_boxes_numeric"  />
							</td>
							<td align="right">Bill Date</td>
							<td>
								<input style="width:200px " name="txt_bill_date" id="txt_bill_date" class="datepicker"  placeholder="Select Date" />
							</td>
							<td align="right" class="must_entry_caption">WT (Kg)</td>
							<td>
								<input style="width:200px " name="txt_weight" id="txt_weight" class="text_boxes_numeric"   />
							</td>
						</tr>
                        
                        
						<tr>
							<td align="right" class="must_entry_caption">Charge ($)</td>
							<td>
								<input style="width:200px " name="txt_charge_usd" id="txt_charge_usd" class="text_boxes_numeric" onKeyUp="getTotalCharge()"   />

							</td>
							<td align="right" class="must_entry_caption">DFS Charge ($)</td>
							<td>
								<input style="width:200px " name="txt_dfs_charge_usd" id="txt_dfs_charge_usd" class="text_boxes_numeric" onKeyUp="getTotalCharge()"   />
							</td>
							<td align="right">Total Charge ($)</td>
							<td>
								<input style="width:100px " name="txt_total_charge_usd" id="txt_total_charge_usd" class="text_boxes_numeric" readonly   />
								<input  style="width:60px "type="button" name="btn_propotion" id="btn_propotion" value="Proportionate" onClick="fn_amount_propotionate()"  class="formbuttonplasminus"/>
							</td>
						
						
						</tr>
						
                        
						<tr>
							<td align="right">Ex Rate</td>
							<td>
								<input style="width:200px " name="txt_ex_rate" id="txt_ex_rate" class="text_boxes_numeric" value="0"  onKeyUp="getTotalCharge()"   />
							</td>
							<td align="right">Charge BDT</td>
							<td>
								<input style="width:200px " name="txt_charge_bdt" id="txt_charge_bdt" class="text_boxes_numeric" readonly  />
								
							</td>

							<td>Ready To Approve</td>
                            <td><? echo create_drop_down( "cbo_approve_status", 200, $yes_no,"", 1, "-- Select --", "", "","" ); ?> </td>
							<td align="right" class="must_entry_caption"></td>
							<td>
							</td>
						</tr>
                        <tr>
                            <td align="center" colspan="6" valign="middle" class="button_container">
                            <div id="is_posted_accounts" style="float:left; font-size:24px; color:#FF0000;"></div>
                                <? echo load_submit_buttons( $permission, "fnc_air_way_bill_entry", 0,1 ,"fnResetForm();",1); ?>
                            </td>
                        </tr>
						
					</table>
				</fieldset>
				
			</form>
		</fieldset>
		<div style="margin-top:5px;" align="center" id="list_view"></div>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript">  </script> 
</html>