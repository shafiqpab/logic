<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Woven MKT Costing
Functionality	:	
JS Functions	:
Created by		:	Md. Helal Uddin 
Creation date 	: 	05-11-2022
Updated by 		: 		
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
echo load_html_head_contents("Woven MKT Costing","../../", 1, 1, $unicode,1,'');

$bodyPartReverse=array();
foreach($body_part as $key=>$val)
{
	$bodyPartReverse[strtoupper($val)]=$key;
}

$washTypeReverse=array();
foreach($emblishment_wash_type as $key=>$val)
{
	$washTypeReverse[strtoupper($val)]=$key;
}

$itemGroupReverse=array(); $itemGroupArr=array(); $itemConsUomArr=array();
$itemGroupSql=sql_select("select id, item_name, trim_uom, cal_parameter from lib_item_group where status_active=1 and is_deleted=0 and item_category=4");
foreach($itemGroupSql as $row)
{
	$itemGroupReverse[strtoupper($row[csf('item_name')])]=$row[csf('id')].'_'.$row[csf('trim_uom')].'_'.$row[csf('cal_parameter')];
	$itemGroupArr[$row[csf('id')]]=strtoupper($row[csf('item_name')]);
}

?>	
<script>

	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	// Master Form-----------------------------------------------------------------------------
	var mandatory_field = '';
	var mandatory_message = '';
	<?
	
	echo " mandatory_field = '". implode('*',$_SESSION['logic_erp']['mandatory_field'][430]) . "';\n";
	echo " mandatory_message = '". implode('*',$_SESSION['logic_erp']['mandatory_message'][430]) . "';\n";

	?>
	var prev_item_id='';
	function fnc_select()
	{
		$(document).ready(function() {
			$("input:text").focus(function() { $(this).select(); } );
		});
	}
	
	var str_bodyPart_head = [<?=substr(return_library_autocomplete_fromArr( $body_part ), 0, -1); ?>];
	var str_washType = [<?=substr(return_library_autocomplete_fromArr( $emblishment_wash_type ), 0, -1); ?>];
	var str_acc= [<?=substr(return_library_autocomplete( "select item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category=4","item_name"),0,-1); ?>];
	function add_auto_complete(inc)
	{
		var incVal=inc.split("_");
		
		var i=incVal[0];
		var type=incVal[1];
		if(type==1)
		{
			$("#txtbodyparttext_"+i).autocomplete({
				source: str_bodyPart_head
			});
		}
		else if(type==2)
		{
			$("#txtspbodyparttext_"+i).autocomplete({
				source: str_bodyPart_head
			});
		}
		else if(type==3)
		{
			$("#txtWashTypetext_"+i).autocomplete({
				source: str_washType
			});
		}
		else if(type==4)
		{
			$("#txtWbodyparttext_"+i).autocomplete({
				source: str_bodyPart_head
			});
		}
		else if(type==5)
		{
			$("#txtAcctext_"+i).autocomplete({
				source: str_acc
			});
		}
	}
	
	function fnc_meeting_remarks_pop_up(costSheetId,styleRef)
	{
		var title = 'Meeting Remarks Form';	
		var page_link = 'requires/woven_mkt_costing_controller.php?action=meeting_remarks_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&costSheetId='+costSheetId+'&styleRef='+styleRef, title, 'width=1050px,height=420px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var meeting_data=this.contentDoc.getElementById("hide_meeting_data").value;	 //Access form field with id="emailfield"
			//alert(meeting_data);  
			
			var ex_data=meeting_data.split("__");
			$('#txt_meeting_no').val(ex_data[0]);
			$('#cbo_buyer_agent').val(ex_data[1]);
			$('#cbo_agent_location').val(ex_data[2]);
			$('#txt_meeting_date').val(ex_data[3]);
			$('#txt_meeting_time').val(ex_data[4]);
			$('#txt_meeting_remarks').val(ex_data[5]);
		}
	}
	
	function fnc_rate_write_popup(inc_id)
	{
		if(inc_id=='meeting')
		{
			if(document.getElementById('chk_is_new_meeting').checked==true)
			{
				document.getElementById('chk_is_new_meeting').value=1;
				//$('#txt_meeting_date').removeAttr('disabled','disabled');
				//$('#txt_meeting_time').removeAttr('disabled','disabled');
				$('#txt_meeting_remarks').val('1. ');
			}
			else if(document.getElementById('chk_is_new_meeting').checked==false)
			{
				document.getElementById('chk_is_new_meeting').value=2;
				//$('#txt_meeting_date').attr('disabled','disabled');
				//$('#txt_meeting_time').attr('disabled','disabled');
				//$('#txt_meeting_remarks').attr('disabled','disabled');
			}
			var chk_meeting_val=$('#chk_is_new_meeting').val();
			
			fnc_meeting_no(chk_meeting_val);
		}
		else if(inc_id=='cm')
		{
			var gmtsItem_id=$('#txt_temp_id').val();
			//alert(gmtsItem_id);
			var gmtId=gmtsItem_id.split(",");
			if(document.getElementById('cmPop').checked==true)
			{
				document.getElementById('cmPop').value=1;
				var k=0;
				for(var y=1; y<=gmtId.length; y++)
				{
					$('#txt_cpm_'+trim(gmtId[k])).removeAttr('disabled','disabled');
					$('#txt_smv_'+trim(gmtId[k])).removeAttr('disabled','disabled');
					$('#txt_eff_'+trim(gmtId[k])).removeAttr('disabled','disabled');
					
					$('#txtCmCost_'+trim(gmtId[k])).val('');
					$('#txtCmCost_'+trim(gmtId[k])).attr("placeholder", "Cal.");
					$('#txtCmCost_'+trim(gmtId[k])).attr("readonly", "readonly");
					k++;
				}
				
				//=((SMV*CPM)*Costing per + (SMV*CPM*Costing per)* Efficiency Wastage%)/Exchange Rate
			}
			else if(document.getElementById('cmPop').checked==false)
			{
				document.getElementById('cmPop').value=2;
				var j=0;
				for(var u=1; u<=gmtId.length; u++)
				{
					$('#txt_cpm_'+trim(gmtId[j])).attr('disabled','disabled');
					$('#txt_smv_'+trim(gmtId[j])).attr('disabled','disabled');
					$('#txt_eff_'+trim(gmtId[j])).attr('disabled','disabled');
					/*$('#txt_cpm_'+trim(gmtId[j])).val('');
					$('#txt_smv_'+trim(gmtId[j])).val('');
					$('#txt_eff_'+trim(gmtId[j])).val('');*/
					
					$('#txtCmCost_'+trim(gmtId[j])).val('');
					$('#txtCmCost_'+trim(gmtId[j])).attr("placeholder", "Write");
					$('#txtCmCost_'+trim(gmtId[j])).removeAttr("readonly", "readonly");
					j++;
				}
			}
		}
		else
		{
			if(document.getElementById('ratePop_'+inc_id).checked==true)
			{
				var consumtion=$('#txt_consumtion'+inc_id).val();
				document.getElementById('ratePop_'+inc_id).value=1;
				$('#txt_rate'+inc_id).val('');
				$('#txtRateData_'+inc_id).val('');
				$('#txt_rate'+inc_id).attr('readonly','readonly');
				$('#txt_rate'+inc_id).attr('readonly','readonly');
				$('#txt_rate'+inc_id).attr("placeholder", "Browse");
				$('#txt_rate'+inc_id).removeAttr("onDblClick").attr("onDblClick","fnc_openmypage_rate("+inc_id+",'"+consumtion+"');");
			}
			else if(document.getElementById('ratePop_'+inc_id).checked==false)
			{
				document.getElementById('ratePop_'+inc_id).value=2;
				$('#txt_rate'+inc_id).val('');
				$('#txtRateData_'+inc_id).val('');
				$('#txt_rate'+inc_id).removeAttr('readonly','readonly');
				$('#txt_rate'+inc_id).attr("placeholder", "Write");
				$('#txt_rate'+inc_id).removeAttr("onDblClick");
			}
		}
	}
	

	function fnc_openmypage_rate()
	{
		var qc_no=$('#hid_qc_no').val();
		var mst_id=$('#mst_id').val();
		var yarn_cont_id=$('#txt_fabid').val();
		var cboSpandex=$('#cboSpandex').val();
		var cboColorType=$('#cboColorType').val();
		if (qc_no=="")
		{
			alert("Please Save First.");
			return;
		}
		
		if (yarn_cont_id=="" || yarn_cont_id==0)
		{
			alert("Please Select Fabric First.");
			return;
		}

		if (cboSpandex=="" || cboSpandex==0)
		{
			alert("Please Select Spandex First.");
			return;
		}
		if (cboColorType=="" || cboColorType==0)
		{
			alert("Please Select Color Type First.");
			return;
		}
		
		var totCons=$('#txt_totConsumtion').val();
		//
		var title = 'Rate Details PopUp';	
		var page_link = 'requires/woven_mkt_costing_controller.php?action=rate_details_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&qc_no='+qc_no+'&totCons='+totCons+'&yarn_cont_id='+yarn_cont_id+'&cboSpandex='+cboSpandex+'&cboColorType='+cboColorType+'&mst_id='+mst_id, title, 'width=1250px,height=460px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			var cons=this.contentDoc.getElementById("txt_total_yarn_consumption").value;
			var totamount=this.contentDoc.getElementById("txt_total_yarn_cost").value;
			//console.log(this.contentDoc);
			console.log(cons);
			console.log(totamount);
			var avgRate=(totamount*1)/(cons*1);
			$('#txt_consumtion').val( number_format(cons,4,'.','') );
			$('#txt_totConsumtion').val( number_format(cons,4,'.','') );
			$('#txt_rate').val( number_format(avgRate,4,'.','') );
			$('#txt_value').val( number_format(totamount,4,'.','') );
			calculate_total();
		}
	}
	function fnc_openmypage_conversion()
	{
		var qc_no=$('#hid_qc_no').val();
		var mst_id=$('#mst_id').val();
		var yarn_cont_id=$('#txt_fabid').val();
		var cboSpandex=$('#cboConvSpandex').val();
		var cboColorType=$('#cboConvColorType').val();
		if (qc_no=="")
		{
			alert("Please Save First.");
			return;
		}
		
		if (yarn_cont_id=="" || yarn_cont_id==0)
		{
			alert("Please Select Fabric First.");
			return;
		}

		
		
		var totCons=$('#txtTotConvConsumtion').val();
		//
		var title = 'Conversion Details PopUp';	
		var page_link = 'requires/woven_mkt_costing_controller.php?action=conversion_details_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&qc_no='+qc_no+'&totCons='+totCons+'&yarn_cont_id='+yarn_cont_id+'&mst_id='+mst_id, title, 'width=1250px,height=460px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			var cons=this.contentDoc.getElementById("total_cost_pop").value;
			var totamount=this.contentDoc.getElementById("txttotamount").value;
			//console.log(this.contentDoc);
			console.log(cons);
			console.log(totamount);
			var avgRate=(totamount*1)/(cons*1);
			$('#txtConvConsumtion').val( number_format(cons,4,'.','') );
			$('#txtTotConvConsumtion').val( number_format(cons,4,'.','') );
			$('#txtConvRate').val( number_format(avgRate,4,'.','') );
			$('#txtConvValue').val( number_format(totamount,4,'.','') );
			calculate_total();
		}
	}
	
	function fnc_new_stage_popup()
	{
		var title = 'Stage Entry/Update PopUp';	
		var page_link = 'requires/woven_mkt_costing_controller.php?action=stage_saveUpdate_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=380px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			load_drop_down( 'requires/woven_mkt_costing_controller', '', 'load_drop_stage_name', 'stage_td');
		}
	}
	
	function fnc_consumption_write_disable(val)
	{
		//alert(val)
		var ex_id=val.split("__");
		if(ex_id[0]==1 || ex_id[0]==3)//manual
		{
			for(var i=1; i<=5; i++)
			{
				var rate=0;
				rate=$('#txt_rate'+i).val()*1;
				if(ex_id[1]==1)
					$('#txt_consumtion'+i).val('');
					
				$('#txt_consumtion'+i).removeAttr('disabled','disabled');
				$('#txt_consumtion'+i).attr("placeholder", "Write");
				$('#txt_consumtion'+i).removeAttr("onChange").attr("onChange","fnc_amount_calculation('fabric','"+i+"','"+rate+"');");
			}
		}
		else
		{
			for(var i=1; i<=5; i++)
			{
				if(ex_id[1]==1)
					$('#txt_consumtion'+i).val('');
				$('#txt_consumtion'+i).attr('disabled','disabled');
				$('#txt_consumtion'+i).attr("placeholder", "Display");
				$('#txt_consumtion'+i).removeAttr("onChange");
			}
		}
	}
	
	function reset_fnc()
	{
		location.reload(); 
	}
	
	
	
	function fnc_specialAcc_reset()
	{
		var sp_row=5;
		for(var n=1; n<=sp_row; n++)
		{
			$('#txt_spConsumtion'+n).val('');
			$('#txt_spexper'+n).val('');
			$('#txtSpRate_'+n).val('');
			$('#txt_spValue'+n).val('');
		}
		
		var ac_row=$('#particulars_acc tbody tr').length;
		for(var q=1; q<=ac_row; q++)
		{
			$('#txtaccConsumtion_'+q).val('');
			$('#txtacexper_'+q).val('');
			$('#txtacRate_'+q).val('');
		}
	}
	
	function fnc_select()
	{
		$(document).ready(function() {
			$("input:text").focus(function() { $(this).select(); } );
		});
	}
	
	function fnc_qcosting_entry( operation )
	{
		freeze_window(operation);
		var type=0;
		if( operation==6)
		{
			type=6; operation=0;
		}
		else if( operation==7)
		{
			type=7; operation=0;
		}
		else
		{
			type=1; operation=operation;
		}
		
		if( ($('#totalFob_td').text()*1)==0 )
		{
			//alert("Please fill up F.O.B $.");
			//release_freezing();	
			//return;
		}
		
		if(operation==2)
		{
			var rr=confirm("You are going to delete Cost Sheet No.\n Are you sure?");
			if(rr==true)
			{
				 //delete_country=1;
			}
			else
			{
				//delete_country=0;
				release_freezing();	
				return;
			}
		}
		//temp_id
		if (form_validation('txt_inquiry_no*txt_exchange_rate*cbo_buyer_id*cbo_season_id*cbo_season_year*txt_styleRef*txt_costingDate','Inquiry ID*Exchange Rate*Buyer Name*Season*Season Year*Style Ref.*Costing Date')==false)
		{
			release_freezing();
			return;
		}	
		else
		{
			if(operation==4)
			{
				var report_title=$( "div.form_caption" ).html();
				generate_report_file( $('#hid_qc_no').val()+'*'+$('#txt_costSheetNo').val()+'*'+report_title,'quick_costing_print','requires/woven_mkt_costing_controller');
				release_freezing();
				return;
			}
			else
			{
				var meeting_txt=$('#txt_meeting_remarks').val();
				if(trim(meeting_txt)!="" && trim(meeting_txt)!="1.")
				{
					if($('#txt_meeting_date').val()=="" && $('#txt_meeting_time').val()=="")
					{
						alert("Please fill up meeting date and time.");
						release_freezing();
						return;
					}
					
					if( $('#cbo_buyer_agent').val()==0 && $('#cbo_agent_location').val()==0)
					{
						alert("Please fill up meeting Buyer agent and location.");
						release_freezing();
						return;
					}
				}
				
				
				
				var txt_consumtion_chexbox = 0;
				if(document.getElementById("txt_consumtion_chexbox").checked)
				{
					txt_consumtion_chexbox = 1;
				}

				console.log(txt_consumtion_chexbox);
				
				var data="action=save_update_delete&operation="+operation+"&type="+type+"&txt_consumtion_chexbox="+txt_consumtion_chexbox+get_submitted_data_string('txt_inquiry_no*cbouom*txt_exchange_rate*txt_costSheetNo*cbo_buyer_id*cbo_season_year*cbo_season_id*cbo_brand*txt_styleRef*txt_style_id*cbo_subDept_id*txt_delivery_date*txt_offerQty*txt_quotedPrice*txt_tgtPrice*txt_costingDate*txt_styleDesc*txtfabDesc*txt_inquery_id*mst_id*hid_qc_no*cbo_revise_no*cbo_option_id*txt_option_remarks*txt_meeting_date*txt_meeting_time*chk_is_new_meeting*txt_meeting_remarks*txt_meeting_no*cbo_buyer_agent*cbo_agent_location*cbo_uom*txtfabDesc*txt_fabid*cboSpandex*cboColorType*txt_consumtion*txt_totConsumtion*txt_rate*txt_value*txt_yarn_cost*txt_lab_test*txt_sizing_chemicals*txt_other_cost*txt_dyes_chemicals*txt_comercial_cost*txt_gas_utility*txt_other_factory_expenses*txt_peach_finish_one_side*txt_wages_salary*txt_peach_finish_both_side*txt_admin_selling*txt_eti_finish*txt_financial_exp*txt_brush_one_side*txt_brush_one_side*txt_brush_both_side*txt_seersucker*txt_instalment_of_term_loan*txt_mechanical_stretch*txt_mechanical_stretch*txt_income_tax*txt_only_rotary_print_cost*txt_fob_p_yrd*txt_fob_p_mtr*txt_upcharge_p_yrd*txt_upcharge_p_mtr*txt_discount_p_yrd*txt_discount_p_mtr*txt_t_cost_p_yrd*txt_t_cost_p_mtr*txt_s_p_p_yrd*txt_s_p_p_mtr*txt_profit_loss_p_yrd*txt_profit_loss_p_mtr*cboConvSpandex*cboConvColorType*txtConvConsumtion*txtTotConvConsumtion*txtConvRate*txtConvValue',"../../");
				
				
				console.log(data);
				http.open("POST","requires/woven_mkt_costing_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_qcosting_entry_response;
			}
		}
	}
	
	function fnc_qcosting_entry_response()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			
			if (reponse[0]=="approvedQc")
			{
				alert("This Option (QC) is Confirm.");
				release_freezing();
				return;
			}
			if (reponse[0]==13)
			{
				var altbom_msg="Delete Restricted, BOM Found, Job No: "+trim(reponse[1]);
				alert(altbom_msg);
				release_freezing();
				return;
			}
			
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_qcosting_entry('+ reponse[1]+')',8000); 
			}
			else if (reponse[0]==1 || reponse[0]==0)
			{
				$('#txt_update_id').val(reponse[1]);
				$('#hid_qc_no').val(reponse[7]);
				$('#txt_costSheetNo').val(reponse[2]);
				//if(reponse[6]!='') $('#txt_meeting_no').val(reponse[6]);
				//alert(reponse[1])
				set_button_status(1, permission, 'fnc_qcosting_entry',1);
				if (reponse[0]==0) alert("Data is Save Successfully");
				else if (reponse[0]==1) alert("Data is Update Successfully");
			//}
			//release_freezing(); return;
			//if (reponse[0]==0 || reponse[0]==1)
			//{
				var user_id='<? echo $_SESSION['logic_erp']['user_id']; ?>';
				if(reponse[5]==1)//Insert
				{
					var temp_style_list=return_ajax_request_value(reponse[2]+'__'+1, 'temp_style_list_view', 'requires/woven_mkt_costing_controller');
					$('#style_td').html( temp_style_list );
					var user_id='<? echo $_SESSION['logic_erp']['user_id']; ?>';
					//get_php_form_data(reponse[1]+"__"+user_id+"__"+reponse[2]+"__"+0+"***"+0, 'populate_style_details_data', 'requires/woven_mkt_costing_controller');
					load_drop_down('requires/woven_mkt_costing_controller', reponse[2]+'__'+reponse[4]+'__'+reponse[3], 'load_drop_down_revise_no', 'revise_td');
					load_drop_down('requires/woven_mkt_costing_controller', reponse[2]+'__'+reponse[4]+'__'+reponse[4], 'load_drop_down_option_id', 'option_td');
					set_onclick_style_list(reponse[2]+'__'+user_id+'__'+reponse[2]);
				}
				if(reponse[5]==6) //Revise
				{
					load_drop_down('requires/woven_mkt_costing_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_revise_no', 'revise_td');
					load_drop_down('requires/woven_mkt_costing_controller', reponse[2]+'__'+reponse[4]+'__'+reponse[4], 'load_drop_down_option_id', 'option_td');
					set_onclick_style_list(reponse[2]+'__'+user_id+'__'+reponse[2]);
				}
				else if(  reponse[5]==7) //Option
				{	
					load_drop_down('requires/woven_mkt_costing_controller', reponse[2]+'__'+reponse[4]+'__'+$('#cbo_revise_no').val(), 'load_drop_down_revise_no', 'revise_td');
					load_drop_down('requires/woven_mkt_costing_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_option_id', 'option_td');
					
					//load_drop_down('requires/woven_mkt_costing_controller', reponse[2]+'__'+reponse[4]+'__'+$('#cbo_revise_no').val(), 'load_drop_down_revise_no', 'revise_td');
					//load_drop_down('requires/woven_mkt_costing_controller', reponse[2]+'__'+reponse[4]+'__0', 'load_drop_down_option_id', 'option_td');
					set_onclick_style_list(reponse[2]+'__'+user_id+'__'+reponse[2]);
				}
			}
			else if (reponse[0]==2)
			{
				reset_fnc();
				release_freezing();
				return;
			}
			
			var temp_style_list=return_ajax_request_value(reponse[2]+'__'+0, 'temp_style_list_view', 'requires/woven_mkt_costing_controller');
			$('#style_td').html( temp_style_list );
			//alert(reponse[7]);
			//change_color_tr( reponse[7], $('#tr_'+trim(reponse[7])).attr('bgcolor') );
			
			if (reponse[0]==6) alert(reponse[1]);
			if (reponse[0]==11) alert(reponse[1]);
			//alert(show_msg(trim(reponse[0])));
			release_freezing();
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/woven_mkt_costing_controller.php?data=" + data+'&action='+action, true );
	}
	
	function openmypage_style(type)
	{
		var page_link='requires/woven_mkt_costing_controller.php?action=style_popup';
		var title="Style Search Popup";
		var data=$('#cbo_company_id').val()+'__'+$('#cbo_buyer_id').val()+'__'+type;
		var user_id='<? echo $_SESSION['logic_erp']['user_id']; ?>';
		var k=1;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=1400px,height=450px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("hide_style_id").value; // product ID
			var cost_sheet_no=this.contentDoc.getElementById("hide_cost_no").value;
			//alert(style_id);
			if(style_id!="")
			{
				var temp_style_list=return_ajax_request_value(cost_sheet_no+'__'+1, 'temp_style_list_view', 'requires/woven_mkt_costing_controller');
				$('#style_td').html( temp_style_list );
				
				var str=style_id.split(",");
				var strcst=cost_sheet_no.split(",");
				for( var i=0; i< str.length; i++ ) {
					if( k==1)
					{
						set_onclick_style_list(str[0]+'__'+user_id+'__'+strcst[0]+'__'+0);
						var qc_no=$('#hid_qc_no').val()*1;
						change_color_tr( qc_no , $('#tr_'+qc_no).attr('bgcolor') );
						load_drop_down('requires/woven_mkt_costing_controller', cost_sheet_no+'__0__0', 'load_drop_down_option_id', 'option_td');
						load_drop_down('requires/woven_mkt_costing_controller', cost_sheet_no+'__'+$('#cbo_option_id').val()+'__0', 'load_drop_down_revise_no', 'revise_td');
						k++;
					}
				}
			}
			
		}
	}
	
	function set_onclick_style_list( data )
	{
		var datas=data.split("__");
		
		if(datas[3]=="25")
		{
			var cost_sheet_no=$('#txt_costSheetNo').val()*1;
			var styleRef=$('#txt_styleRef').val();
			
			var recent_fob=$('#totalFob_td').text()*1;
			var recent_costing_remarks=$('#txt_costing_remarks').val();
			var recent_opt_remarks=$('#txt_option_remarks').val();
			var recent_meeting_remark=$('#txt_meeting_remarks').val();
			
			var pre_fob=$('#totalFob_td').attr('prev_fob')*1;
			var pre_costing_remarks=$('#txt_costing_remarks').attr('pre_costing_remarks');
			var pre_opt_remarks=$('#txt_option_remarks').attr('pre_opt_remarks');
			var pre_meeting_remark=$('#txt_meeting_remarks').attr('pre_meeting_remark');
			
			if(styleRef!="")
			{
				if(cost_sheet_no!=0)
				{
					if( pre_fob!=recent_fob || pre_costing_remarks!=recent_costing_remarks || pre_opt_remarks!=recent_opt_remarks || pre_meeting_remark!=recent_meeting_remark )
					{
						//var r=confirm("You are Going to Generate Style Data Without Update.\n Please, Press OK to Generate.\n Otherwise Press Cencel.");
						
						//var r=confirm("Switch to another costing need update, Press OK. \n Otherwise Press Cencel then switch. \n Do You Want To Update?");
						var r=confirm("Do You Want To Update?");
						//alert(r); return;
						if(r==true)
						{
							fnc_qcosting_entry(1);
						}
						else
						{
							//release_freezing();	
							//return;
						}
					}
				}
				else if(cost_sheet_no==0)
				{
					//var r=confirm("You are Going to Generate Style Data Without Save.\n Please, Press OK to Generate.\n Otherwise Press Cencel.");
					//var r=confirm("Switch to another costing need Save, Press OK. \n Otherwise Press Cencel then switch. \n Do You Want To Update?");
					var r=confirm("Do You Want To Update?");
					//alert(r); return;
					if(r==true)
					{
						fnc_qcosting_entry(1);
					}
					else
					{
						//release_freezing();	
						//return;
					}
				}
			}
			load_drop_down('requires/woven_mkt_costing_controller', datas[2]+'__0__0', 'load_drop_down_option_id', 'option_td');
			load_drop_down('requires/woven_mkt_costing_controller', datas[2]+'__'+$('#cbo_option_id').val()+'__0', 'load_drop_down_revise_no', 'revise_td');
			
			var val=document.getElementById('cbo_revise_no').value+'***'+document.getElementById('cbo_option_id').value+'***'+document.getElementById('chk_is_new_meeting').value;
			get_php_form_data(datas[0]+"__"+datas[1]+"__"+datas[2]+"__"+val, 'populate_style_details_data', 'requires/woven_mkt_costing_controller');
			$('#txt_seleted_row_id').val(datas[0]);
			$('#hid_selected_cost_no').val(datas[2]);
			
		}
		else
		{
			var val=document.getElementById('cbo_revise_no').value+'***'+document.getElementById('cbo_option_id').value+'***'+document.getElementById('chk_is_new_meeting').value;
			get_php_form_data(datas[0]+"__"+datas[1]+"__"+datas[2]+"__"+val, 'populate_style_details_data', 'requires/woven_mkt_costing_controller');
			$('#txt_seleted_row_id').val(datas[0]);
			$('#hid_selected_cost_no').val(datas[2]);
			//$('#chk_is_new_meeting').val(2);
			//document.getElementById('chk_is_new_meeting').checked=false;
			//fnc_meeting_no(2);
		}
	}
	
	function fnc_delete_style_row()
	{
		var style_id=$('#txt_seleted_row_id').val();
		var hid_qc_no=$('#hid_qc_no').val();
		var temp_style_list=return_ajax_request_value(hid_qc_no+'__'+3, 'temp_style_list_view', 'requires/woven_mkt_costing_controller');
		$('#style_td').html( temp_style_list );
		reset_fnc();
		/*if( (td*1)>1 )
		{
			$('#tr_'+td).remove();
			//localStorage.setItem( "temp_style_list_view", $('#style_td').html() );
			
		}*/
	}
	
	function fnc_cost_id_write()
	{
		var user_id='<? echo $_SESSION['logic_erp']['user_id']; ?>';
		get_php_form_data(($("#hid_qc_no").val()*1)+'__'+user_id+'__'+0, 'populate_style_details_data', 'requires/woven_mkt_costing_controller');
	}
	
	function fnc_copy_cost_sheet( operation )
	{
		$('#txt_inquiry_no').val('');
		$('#txt_inquery_id').val('');
		//alert( $('#txt_update_id').val() );
		var data_copy="action=copy_cost_sheet&operation="+operation+get_submitted_data_string('txt_costSheetNo*txt_update_id*txt_styleRef*cbo_season_id*cbo_buyer_id*hid_qc_no',"../../");
		var data=data_copy;
		//alert(data);
		//return;
		freeze_window(operation);
		http.open("POST","requires/woven_mkt_costing_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_qcosting_entry_response;
	}
	
	function fnc_check_zero_val(val)
	{
		var new_val=val*1;
		if(new_val<1)
		{
			alert("Less Then 1 (one) value not Allowed");
			$("#txt_noOfPack").val(1);
			return;
		}
	}
	
	function fnc_clear_all()
	{
		//localStorage.removeItem("temp_style_list_view", $('#style_td').html() ); 
		var r=confirm("Do you want clear style list?\n If ok List will clear.");
		if(r==false)
		{
			return;
		}
		else
		{
			var style_id='';
			var temp_style_list=return_ajax_request_value(style_id+'__'+2, 'temp_style_list_view', 'requires/woven_mkt_costing_controller');
			$('#style_td').html('');
			reset_fnc();
		}
	}
	
	function fnc_confirm_style()
	{
		if($('#txt_update_id').val()!="")
		{
			var data=$('#hid_qc_no').val()+'__'+$('#txt_update_id').val()+'__'+$('#txt_costSheetNo').val();
			var page_link='requires/woven_mkt_costing_controller.php?action=confirmStyle_popup';
			var title="Confirm Style Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=1000px,height=450px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				/*var theform=this.contentDoc.forms[0];
				var style_id=this.contentDoc.getElementById("hide_style_id").value; // product ID
				var temp_style_list=return_ajax_request_value(style_id+'__'+1, 'temp_style_list_view', 'requires/woven_mkt_costing_controller');
				$('#style_td').html( temp_style_list );*/
				//alert(temp_style_list)
				//$('#style_td').html( $('#style_td').html() +""+ return_ajax_request_value(style_id, 'temp_style_list_view', 'requires/woven_mkt_costing_controller') );
				//show_list_view( style_id,'temp_style_list_view','style_td','requires/woven_mkt_costing_controller','',1);//setFilterGrid(\'tbl_po_list\',-1)
				//localStorage.setItem( "temp_style_list_view", $('#style_td').html() );
			}
		}
		else
		{
			alert("Please Cost Sheet Save First.");
			return;
		}
	}
	
	function fnc_option_rev( val )
	{
		var user_id='<? echo $_SESSION['logic_erp']['user_id']; ?>';
		var selected_id=$('#txt_seleted_row_id').val();
		var cost_no=$('#hid_selected_cost_no').val();
		var hid_qc_no=$('#hid_qc_no').val();
		//alert(user_id)
		
		get_php_form_data(hid_qc_no+"__"+user_id+"__"+cost_no+"__"+val+"__from_option", 'populate_style_details_data', 'requires/woven_mkt_costing_controller');
	}
	
	var row_color=new Array();
	var lastid='';
	function change_color_tr( v_id, e_color )
	{
		//alert(v_id+'='+e_color)
		if(lastid!='') $('#tr_'+lastid).attr('bgcolor',row_color[lastid])
		
		if( row_color[v_id]==undefined ) row_color[v_id]=$('#tr_'+v_id).attr('bgcolor');
		
		if( $('#tr_'+v_id).attr('bgcolor')=='#FF9900')
			$('#tr_'+v_id).attr('bgcolor',row_color[v_id])
		else
			$('#tr_'+v_id).attr('bgcolor','#FF9900')
		
		lastid=v_id;
	}
	
	$(document).ready(function(){
	  navigate_arrow_key()
	});
	
	 new function ($) {
        $.fn.getCursorPosition = function () {
            var pos = 0;
            var el = $(this).get(0);
            // IE Support
            if (document.selection) {
                el.focus();
                var Sel = document.selection.createRange();
                var SelLength = document.selection.createRange().text.length;
                Sel.moveStart('character', -el.value.length);
                pos = Sel.text.length - SelLength;
            }
            // Firefox support
            else if (el.selectionStart || el.selectionStart == '0')
                pos = el.selectionStart;
            return pos;
        }
    } (jQuery);
   
	function navigate_arrow_key()
	{
		$('#item_tbl input').keyup(function(e){
			
			if( e.which==39 )
			{
				 if( $(this).getCursorPosition() == $(this).val().length ) 
				 	$(this).closest('td').next().find('.text_boxes,.text_boxes_numeric').focus();
			}
			else if( e.which==37 )
			{
				if( $(this).getCursorPosition() == 0 ) 
					$(this).closest('td').prev().find('.text_boxes,.text_boxes_numeric').focus();
			}
			else if( e.which==40 )
			{
				// alert( (($(this).closest('tr').index()*1)+1)%5 )
				 
				 /*if( $(this).closest('tr').index()!=0 && (($(this).closest('tr').index()*1)+1)%5==0)
				 
				 	$(this).closest('tr').next().find('td:eq('+ tind +')').find('.text_boxes,.text_boxes_numeric').focus();
				 return;
				  alert( $(this).closest('td').index() )
				  */
				//  && (($(this).closest('tr').next().index()*1)+1)%5!=0  alert( $(this).closest('tr').next().index() );
				var i=1;
				
				if( $(this).closest('tr').index()==0 ){
					var ind= $(this).closest('td').index()-1;
					//alert('k');
				}
				else if((($(this).closest('tr').index()*1)+1)%5==0  )
				{
					var ind= ($(this).closest('td').index()*1)+1;
					//alert('f');
					//alert( $(this).closest('tr').index())
					//i=1;
				}
				else{
					if((($(this).closest('tr').prev().index()*1)+1)%5==0  )
					var ind= ($(this).closest('td').index()*1)-1;
					else
					var ind= ($(this).closest('td').index()*1);
					//ind-i
					//i=0
				}
				
				 $(this).closest('tr').next().find('td:eq('+ind +')').find('.text_boxes,.text_boxes_numeric').focus();
				 return;
				/*if( (($(this).closest('tr').index()*1)+1)%5==0 ) 
					var tind= ($(this).closest('td').index()*1)+1; 
				else  
					var tind= ($(this).closest('td').index()*1);
				*/
				if( $(this).closest('tr').index()!=0 )
					$(this).closest('tr').next().find('td:eq('+ $(this).closest('td').index() +')').find('.text_boxes,.text_boxes_numeric').focus();
				else
					$(this).closest('tr').next().find('td:eq('+ind +')').find('.text_boxes,.text_boxes_numeric').focus();
			}
			else if( e.which==38 )
			{
				var ind= ($(this).closest('td').index()*1)+1;
				if($(this).closest('tr').index()!=1)
					$(this).closest('tr').prev().find('td:eq('+$(this).closest('td').index()+')').find('.text_boxes,.text_boxes_numeric').focus();
				else
					$(this).closest('tr').prev().find('td:eq('+ind+')').find('.text_boxes,.text_boxes_numeric').focus();
			}
		});
	}
	
	function fnc_valid_time(val,field_id)
	{
		var val_length=val.length;
		if(val_length==2)
		{
			document.getElementById(field_id).value=val+":";
		}
		
		var colon_contains=val.includes(":");
		if(colon_contains==false)
		{
			if(val>23)
			{
				document.getElementById(field_id).value='23:';
			}
		}
		else
		{
			var data=val.split(":");
			var minutes=data[1];
			var str_length=minutes.length;
			var hour=data[0]*1;
			
			if(hour>23)
			{
				hour=23;
			}
			
			if(str_length>=2)
			{
				minutes= minutes.substr(0, 2);
				if(minutes*1>59)
				{
					minutes=59;
				}
			}
			
			var valid_time=hour+":"+minutes;
			document.getElementById(field_id).value=valid_time;
		}
	}
	
	function numOnly(myfield, e, field_id)
	{
		var key;
		var keychar;
		if (window.event) key = window.event.keyCode;
		else if (e) key = e.which;
		else return true;
		keychar = String.fromCharCode(key);
	
		// control keys
		if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;
		// numbers
		else if ((("0123456789:").indexOf(keychar) > -1))
		{
			var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
			if(keychar==":" && dotposl!=-1)
			{
				return false;
			}
			return true;
		}
		else
			return false;
	}
	
	function openmypage_agent_location(page_link,title,type)
	{
		var temp_id=document.getElementById('cbo_temp_id').value;
		page_link=page_link+'&temp_id='+temp_id+'&type='+type;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=350px,center=1,resize=0,scrolling=0','../')
		release_freezing();
		emailwindow.onclose=function()
		{
			if(type==1) load_drop_down( 'requires/woven_mkt_costing_controller', type, 'load_drop_agent_location_name', 'agent_td');
			else if (type==2) load_drop_down( 'requires/woven_mkt_costing_controller', type, 'load_drop_agent_location_name', 'location_td');
		}
	}
	
	function fnc_meeting_no(chk_meeting_val)
	{
		var meeting_val=1;
		var max_meeting_no=return_ajax_request_value('', 'max_meeting_no', 'requires/woven_mkt_costing_controller');
		if(chk_meeting_val==1)
		{
			meeting_val=(max_meeting_no*1)+1;
		}
		else
		{
			meeting_val=(max_meeting_no*1);
		}
		$('#txt_meeting_no').val(meeting_val);
	}
	
	function fnc_fobavg_option()
	{
		if( form_validation('txt_costSheetNo','Please Save First.')==false)
		{
			return;
		}
		else
		{
			var data=$('#txt_costSheetNo').val();
			var page_link='requires/woven_mkt_costing_controller.php?action=fobavg_option_popup';
			var title="FOB Average Option PopUp";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=650px,height=450px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				
			}
		}
	}
	
	function print_report_button_setting()
	{
		var report_ids=return_ajax_request_value('', 'print_btn_id', 'requires/woven_mkt_costing_controller');
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==84) $("#report_btn_2").show();
			if(report_id[k]==86) $("#report_btn_1").show();
		}
	}
	
	function fnc_print_report2(action)
	{
		if( form_validation('txt_costSheetNo','Please Save First.')==false) 
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#hid_qc_no').val()+'*'+$('#txt_costSheetNo').val()+'*'+report_title, action,'requires/woven_mkt_costing_controller');
		return;
	}
	function fnc_print_report(action)
	{
		if( form_validation('txt_costSheetNo','Please Save First.')==false) 
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var is_excel=1;
		var data=$('#hid_qc_no').val()+'*'+$('#txt_costSheetNo').val()+'*'+$('#cbo_company_id').val()+'*'+$('#cbo_revise_no').val()+'*'+report_title+'*'+is_excel;

         freeze_window();
		var data="action="+action+'&data='+data;
		http.open("POST","requires/woven_mkt_costing_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_print_report_reponse;
		
	}
	function fnc_print_report_reponse()
	{
		if(http.readyState == 4)
		{
	        release_freezing();
			var file_data=http.responseText.split("####");
	        //  alert(file_data[2]);
			if(file_data[2]==100)
	        {
	        $('#data_panel').html(file_data[0]);
	        $('#qc_report_btn_1').removeAttr('href').attr('href','requires/'+trim(file_data[1]));
	        document.getElementById('qc_report_btn_1').click();
	        }
			 
	        
	        var report_title=$( "div.form_caption" ).html();
	        var w = window.open("Surprise", "_blank");
	        var d = w.document.open();
	        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title>'+report_title+'</title></head><body>'+document.getElementById('data_panel').innerHTML+'</body></html>');
	        d.close();
		}
	}

	
	function fnc_openmypage_inquery()
	{
		
		
		var data=$('#cbo_buyer_id').val();
		
		var page_link='requires/woven_mkt_costing_controller.php?action=inquery_id_popup';
		var title='Inquiry ID Selection Form' ;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&data='+data, title, 'width=1020px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theval=this.contentDoc.getElementById("selected_id").value;
			//alert(theval);
			if(theval!="")
			{
				var theemail=theval.split("_");
				console.log(theemail);
				//alert(theemail[1]);
				document.getElementById('txt_inquery_id').value=theemail[0];
				document.getElementById('cbo_buyer_id').value=theemail[1];
				
				load_drop_down( 'requires/woven_mkt_costing_controller', theemail[1], 'load_drop_down_season', 'season_td');
				load_drop_down( 'requires/woven_mkt_costing_controller', theemail[1], 'load_drop_down_brand', 'brand_td');
				
				document.getElementById('txt_styleRef').value=theemail[2];
				document.getElementById('txt_inquiry_no').value=theemail[3];
				document.getElementById('cbo_season_id').value=theemail[4];
				document.getElementById('cbo_season_year').value=theemail[5];
				document.getElementById('cbo_brand').value=theemail[6];
				document.getElementById('txt_styleDesc').value=theemail[7];
				document.getElementById('txt_styleDesc').title=theemail[7];
				document.getElementById('txt_inqueryfab_id').value=theemail[8];
				//document.getElementById('txt_bodywashcolor').value=theemail[9];
				//document.getElementById('txt_bodywashcolor').title=theemail[9];
				document.getElementById('txt_offerQty').value=theemail[10];
				document.getElementById('txt_tgtPrice').value=theemail[11];
				document.getElementById('txt_quotedPrice').value=theemail[12];
				
				$('#cbo_buyer_id').attr('disabled',true);
				$('#cbo_brand').attr('disabled',true);
				$('#cbo_season_year').attr('disabled',true);
				$('#cbo_season_id').attr('disabled',true);
				$('#txt_bodywashcolor').attr('disabled',true);
				$('#txt_styleDesc').attr('disabled',true);
				
				get_php_form_data(theemail[8], "populate_data_from_rdnolib", "requires/woven_mkt_costing_controller" );
			}
		}
	}
	
	
	function fnc_type_loder( i )
	{
		var cboembname=document.getElementById('cboSpeciaOperationId_'+i).value
		load_drop_down( 'requires/woven_mkt_costing_controller', cboembname+'_'+i, 'load_drop_down_embtype', 'embtypetd_'+i );
	}
	
	function fnc_fabric_popup()
	{
		var txt_fabid =document.getElementById('txt_fabid').value;
		var inqueryfab_id =document.getElementById('txt_inqueryfab_id').value;
		var page_link='requires/woven_mkt_costing_controller.php?action=fabric_description_popup&txt_fabid='+txt_fabid+'&inqueryfab_id='+inqueryfab_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Fabric Description', 'width=1160px,height=450px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var fab_des_id=this.contentDoc.getElementById("hiddfabid");
			var fab_desctiption=this.contentDoc.getElementById("hiddFabricDescription");
			var fabric_description_title=this.contentDoc.getElementById("hiddFabricDescriptionTitle");
			
			document.getElementById('txt_fabid').value=fab_des_id.value;
			document.getElementById('txtfabDesc').value=fab_desctiption.value;
			document.getElementById('txtConFab').value=fab_desctiption.value;
			document.getElementById('txtfabDesc').title=fabric_description_title.value;
		}
	}
	
	function fnc_conscopy(type)
	{
		if(type==1)
		{
			if(document.getElementById('chk_washconscopy').checked==true)
			{
				document.getElementById('chk_washconscopy').value=1;
			}
			else
			{
				document.getElementById('chk_washconscopy').value=2;
			}
		}
		else if (type==2)
		{
			if(document.getElementById('chk_accconscopy').checked==true)
			{
				document.getElementById('chk_accconscopy').value=1;
			}
			else
			{
				document.getElementById('chk_accconscopy').value=2;
			}
		}
	}
	
	
	
	
	

	function check_exchange_rate()
	{
		var cbo_currercy = 2;
		var txt_costingDate = $('#txt_costingDate').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+txt_costingDate, 'check_exchange_rate', '', 'requires/woven_mkt_costing_controller');
		var response=response.split("_");
		$('#txt_exchange_rate').val(response[1]);
	}

	function write_or_browse()
	{
		if(document.getElementById('txt_consumtion_chexbox').checked)
		{
			var consumtion=$('#txt_consumtion').val();
			$('#txt_consumtion').removeAttr("onDblClick").attr("onDblClick","fnc_openmypage_rate();");
			$('#txt_consumtion').attr("placeholder", "Browse");
		}
		else
		{
			$('#txt_consumtion').removeAttr("onDblClick");
			$('#txt_consumtion').attr("placeholder", "Write");
		}
	}

	function calculate_total()
	{
		var txt_yarn_cost 				=$('#txt_yarn_cost').val() * 1 ;
		var txt_lab_test 				=$('#txt_lab_test').val() * 1 ;
		var txt_sizing_chemicals 		=$('#txt_sizing_chemicals').val() * 1 ;
		var txt_other_cost 				=$('#txt_other_cost').val() * 1 ;
		var txt_dyes_chemicals 			=$('#txt_dyes_chemicals').val() * 1 ;
		var txt_comercial_cost 			=$('#txt_comercial_cost').val() * 1 ;
		var txt_gas_utility 			=$('#txt_gas_utility').val() * 1 ;
		var txt_other_factory_expenses 	=$('#txt_other_factory_expenses').val() * 1 ;
		var txt_peach_finish_one_side 	=$('#txt_peach_finish_one_side').val() * 1 ;
		var txt_wages_salary 			=$('#txt_wages_salary').val() * 1 ;
		var txt_peach_finish_both_side 	=$('#txt_peach_finish_both_side').val() * 1 ;
		var txt_admin_selling 			=$('#txt_admin_selling').val() * 1 ;
		var txt_eti_finish 				=$('#txt_eti_finish').val() * 1 ;
		var txt_financial_exp 			=$('#txt_financial_exp').val() * 1 ;
		var txt_brush_one_side 			=$('#txt_brush_one_side').val() * 1 ;
		var txt_brush_both_side 		=$('#txt_brush_both_side').val() * 1 ;
		var txt_seersucker 				=$('#txt_seersucker').val() * 1 ;
		var txt_instalment_of_term_loan =$('#txt_instalment_of_term_loan').val() * 1 ;
		var txt_mechanical_stretch 		=$('#txt_mechanical_stretch').val() * 1 ;
		var txt_income_tax 				=$('#txt_income_tax').val() * 1 ;
		var txt_only_rotary_print_cost 	=$('#txt_only_rotary_print_cost').val() * 1 ;
		var txt_value 				 	=$('#txt_value').val() * 1 ;
		var txtConvValue 				=$('#txtConvValue').val() * 1 ;
		var txt_upcharge_p_yrd 			=$('#txt_upcharge_p_yrd').val() * 1 ;
		var txt_discount_p_yrd 			=$('#txt_discount_p_yrd').val() * 1 ;
		var txt_s_p_p_yrd 				=$('#txt_s_p_p_yrd').val() * 1 ;
		var txt_profit_loss_p_yrd 		=$('#txt_profit_loss_p_yrd').val() * 1 ;
		var total_amount = txt_yarn_cost + txt_lab_test + txt_sizing_chemicals + txt_other_cost + txt_dyes_chemicals + txt_gas_utility + txt_other_factory_expenses + txt_peach_finish_one_side + txt_wages_salary + txt_peach_finish_both_side + txt_admin_selling + txt_eti_finish + txt_financial_exp + txt_brush_one_side + txt_seersucker + txt_instalment_of_term_loan + txt_mechanical_stretch + txt_income_tax  + txt_only_rotary_print_cost + txt_comercial_cost + txt_brush_both_side + txt_value + txtConvValue;
		$('#txt_fob_p_yrd').val(number_format_common(total_amount, 1, 0, 2));
		$('#txt_fob_p_mtr').val(number_format_common(total_amount*1.09361), 1, 0, 2);

		$('#txt_upcharge_p_mtr').val(number_format_common(txt_discount_p_yrd*1.09361, 1, 0, 2));
		$('#txt_fob_p_mtr').val(number_format_common(total_amount*1.09361, 1, 0, 2));
		$('#txt_discount_p_mtr').val(number_format_common(txt_discount_p_yrd*1.09361, 1, 0, 2));
		var txt_t_cost_p_yrd = total_amount+txt_upcharge_p_yrd-txt_discount_p_yrd;
		$('#txt_t_cost_p_yrd').val(number_format_common(txt_t_cost_p_yrd, 1, 0, 2));
		var txt_fob_p_mtr = $('#txt_fob_p_mtr').val() * 1 ;
		var txt_upcharge_p_mtr = $('#txt_upcharge_p_mtr').val() * 1 ;
		var txt_discount_p_mtr = $('#txt_discount_p_mtr').val() * 1 ;
		var txt_t_cost_p_mtr = txt_fob_p_mtr+txt_upcharge_p_mtr-txt_discount_p_mtr;
		$('#txt_t_cost_p_mtr').val(number_format_common(txt_discount_p_mtr, 1, 0, 2));
		var txt_profit_loss_p_yrd =txt_t_cost_p_yrd - txt_s_p_p_yrd ;

		$('#txt_s_p_p_mtr').val(number_format_common(txt_s_p_p_yrd * 1.09361, 1, 0, 2) );

		$('#txt_profit_loss_p_yrd').val(number_format_common(txt_profit_loss_p_yrd, 1, 0, 2));
		
		var txt_profit_loss_p_mtr = txt_t_cost_p_mtr - txt_s_p_p_yrd * 1.09361;
		$('#txt_profit_loss_p_mtr').val(number_format_common(txt_profit_loss_p_mtr, 1, 0, 2));
		$('#totalFob_td').text(number_format_common(total_amount, 1, 0, 2));
	}

	function fnc_amount_calculation()
	{
		var txt_consumtion = $("#txt_consumtion").val() * 1;
		var txt_rate = $("#txt_rate").val() * 1;
		var txt_value = txt_consumtion * txt_rate;
		$("#txt_value").val(txt_value);
	}
	
</script>
<style>
	.textbox1
	{
		background-color : #FFC0CB;
	}
	.textbox2
	{
		background-color : #DDA0DD;
	}
	
	.tr1
	{
		background:#FFC0CB;
	}
	
	.tr2
	{
		background:#DDA0DD;
	}
</style> 	
    
</head>
<body onLoad="check_exchange_rate();">
    <div style="width:100%;">
        <div style="display:none"><? echo load_freeze_divs ("../../",$permission); ?></div>
        <form name="quickCosting_1" id="quickCosting_1" autocomplete="off">
            <fieldset style="width:1200px;">
                <table width="1200px" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        
                        <td class="must_entry_caption">
                        	<strong>Inquiry ID</strong>
                        </td>
                        <td>
                        	<input style="width:120px;" type="text" class="text_boxes" name="txt_inquiry_no" id="txt_inquiry_no" readonly placeholder="Browse" onDblClick="fnc_openmypage_inquery();"/>
                        </td>
                        <td width="110" class="must_entry_caption">
                        	Cons. Basis <? echo create_drop_down( "cbouom", 45, $unit_of_measurement,'', 0, '-Uom-', 27, "",$disabled,"12,23,27" ); ?>	
                        </td>
                        <td>Exc. Rate <input class="text_boxes_numeric" type="text" style="width:74px;"   name="txt_exchange_rate" id="txt_exchange_rate" disabled="" readonly/></td>
                       
                        <td width="110">
                        	<strong>Cost Sheet No</strong>
                        </td>
                        <td>
                        	<input style="width:120px;" type="text" class="text_boxes_numeric textbox2" name="txt_costSheetNo" id="txt_costSheetNo" placeholder="Display"  readonly/>
                        	<input style="width:40px;" type="hidden" name="txt_update_id" id="txt_update_id"/>

                        	<input type="hidden" id="mst_id">
	                        <input type="hidden" id="hid_selected_cost_no">
	                        <input type="hidden" id="hid_qc_no">
	                        <input type="hidden" id="txt_inquery_id">
	                        <input type="hidden" id="txt_inqueryfab_id">
	                        <input type="hidden" id="txt_commercial_cost_method"/>
	                        <input type="hidden" id="txt_commercial_per"/>
                        </td>
                        <td class="must_entry_caption"><strong>Buyer Name</strong></td>
                        <td><? echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down('requires/woven_mkt_costing_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/woven_mkt_costing_controller',this.value, 'load_drop_down_sub_dep', 'sub_td' ); load_drop_down( 'requires/woven_mkt_costing_controller', this.value, 'load_drop_down_brand', 'brand_td');" ); ?></td>
                        <td class="must_entry_caption"><strong>Season</strong>&nbsp;&nbsp;&nbsp;<? echo create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                        <td id="season_td">
                        	<? echo create_drop_down("cbo_season_id", 130, $blank_array,'', 1, "-- Select Season--",$selected, "" ); ?>
                        </td>
                        
                    </tr>
                    <tr>
                    	<td class="must_entry_caption"><strong>Brand</strong></td>
                        <td id="brand_td">
                        	<? echo create_drop_down("cbo_brand", 130, $blank_array,"",1, "-Brand-", $selected,""); ?>
                        		
                        </td>
                        <td class="must_entry_caption"><strong>Master Style</strong></td>
                        <td>
                        	<input style="width:120px;" type="text" onDblClick="openmypage_style(0);" class="text_boxes textbox1" name="txt_styleRef" id="txt_styleRef" placeholder="Write/Browse" />
                        	<input type="hidden" name="txt_style_id" id="txt_style_id">
                        </td>
                        <td><strong>Department</strong></td>
                        <td id="sub_td">
                        	<? echo create_drop_down( "cbo_subDept_id", 130, $blank_array,'', 1, "-- Select Dept--",$selected, "" ); ?>
                        		
                        </td>
                        <td><strong>Delivery Date</strong></td>
                        <td>
                        	<input name="txt_delivery_date" id="txt_delivery_date" class="datepicker" type="text" style="width:117px;" value="" />
                        </td>
                        <td><strong>Offer Qty</strong></td>
                        <td>
                        	<input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_offerQty" id="txt_offerQty" onChange="fnc_amount_calculation();" />
                        </td>
                    </tr>
                    <tr>
                    	<td><strong>Quoted Price ($)</strong></td>
                        <td>
                        	<input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_quotedPrice" id="txt_quotedPrice" />
                        </td>
                    	<td><strong>TGT Price</strong></td>
                        <td>
                        	<input style="width:120px;" type="text" class="text_boxes_numeric" name="txt_tgtPrice" id="txt_tgtPrice" />
                        </td>
                    	<td class="must_entry_caption"><strong>Costing Date</strong></td>
                        <td>
                        	<input style="width:120px;" type="text" class="datepicker" name="txt_costingDate" id="txt_costingDate" value="<?=date('d-m-Y');?>" />
                        </td>
                        <td><strong>Style Description</strong></td>
                        <td>
                        	<input style="width:120px;" type="text" class="text_boxes" name="txt_styleDesc" id="txt_styleDesc" />
                        </td>
                        
                    </tr>
                    
                    <tr>
                    	<td><strong>&nbsp;</strong></td>
                        <td id="td_hiddData">
                        	<input style="width:120px;" type="hidden" class="text_boxes" name="txtfabricData_0" id="txtfabricData_0" />
                        	<input style="width:120px;" type="hidden" class="text_boxes" name="txtspData_0" id="txtspData_0" />.
                            <input style="width:120px;" type="hidden" class="text_boxes" name="txtwashData_0" id="txtwashData_0" />
                            <input style="width:120px;" type="hidden" class="text_boxes" name="txtaccData_0" id="txtaccData_0" />
                        </td>
                        <td><strong>&nbsp;</strong></td>
                        <td><strong>&nbsp;</strong></td>
                        <td><strong>&nbsp;</strong></td>
                        <td><strong>&nbsp;</strong></td>
                        <td><strong>&nbsp;</strong></td>
                        <td><strong>&nbsp;</strong></td>
                        <td><strong>&nbsp;</strong></td>
                        <td><strong>&nbsp;</strong></td>
                    </tr>
                </table>
            </fieldset>
            
            <fieldset style="width:1200px;">
            <table width="1200" cellspacing="2" cellpadding="0" border="0" class="rpt_table" rules="all">
                <tr>
                	<td width="600" valign="top">
                		<table width="580" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                			<thead>
                                <tr>
                                    <th width="90">Cost Head</th>
                                    <th width="120">Fab. Des.</th>
                                    <th width="80">Spandex</th>
                                    <th width="50">Color Type</th>
                                    <th width="50">Cal</th>
                                    <th width="40">Cons</th>
                                    <th width="50">Total Cons</th>
                                    <th width="40">Rate</th>
                                    <th>Value ($)</th>
                                </tr>
                            </thead>
                        	<tbody>
                                <tr>
                                	
                                    <td width="90">
                                    	Yarn Cost
                                    </td>
                                    <td width="120">
                                    	<input style="width:100px;" type="text" class="text_boxes" name="txtfabDesc" id="txtfabDesc" readonly placeholder="Browse/Display" onDblClick="fnc_fabric_popup();" />
                                    	<input style="width:50px;" type="hidden" name="txt_fabid" id="txt_fabid" readonly />
                                    </td>
                                    <td width="80">
                                    	<? echo create_drop_down( "cboSpandex", 60, $yes_no,'', 1, "-- Select Spandex--",$selected, "" ); ?>
                                    </td>
                                    <td width="50">
                                    	<? echo create_drop_down( "cboColorType", 35, $color_type,'', 1, "-- Select Color Type--",$selected, "" ); ?>
                                    </td>
                                    <td width="50">
                                    	<input style="width:38px;" type="checkbox" class="text_boxes_numeric" name="txt_consumtion_chexbox" id="txt_consumtion_chexbox" placeholder="Write" onclick ="write_or_browse()"  />
                                    </td>
                                    <td width="50">
                                    	<input style="width:38px;" type="text" class="text_boxes_numeric" name="txt_consumtion" id="txt_consumtion" placeholder="Write" onChange="fnc_amount_calculation();" onBlur="" />
                                    	<input type="hidden" name="txtConsData" id="txtConsData">
                                    </td>
                               
                                    <td width="40">
                                    	<input style="width:38px;" type="text" class="text_boxes_numeric" name="txt_totConsumtion" id="txt_totConsumtion" disabled />
                                    </td>
                                    <td width="40">
                                    	<input style="width:27px;" type="text" class="text_boxes_numeric" name="txt_rate1" id="txt_rate" placeholder="Write" onChange="fnc_amount_calculation('fabric',1,document.getElementById('txt_rate').value);" />
                                    </td>
                                   
                                    <td>
                                    	<input style="width:37px;" type="text" class="text_boxes_numeric" name="txt_value" id="txt_value" placeholder="Display" readonly />
                                    </td>
                                </tr>
                                <tr>
                                	
                                    <td width="90">
                                    	Conversion Charge Details
                                    </td>
                                    <td width="120">
                                    	<input style="width:100px;" type="text" class="text_boxes" name="txtConFab" id="txtConFab" readonly />
                                
                                    </td>
                                    <td width="80">
                                    	<? echo create_drop_down( "cboConvSpandex", 60, $yes_no,'', 1, "-- Select Spandex--",$selected, "" ); ?>
                                    </td>
                                    <td width="50">
                                    	<? echo create_drop_down( "cboConvColorType", 35, $color_type,'', 1, "-- Select Color Type--",$selected, "" ); ?>
                                    </td>
                                    <td width="50">
                                    	
                                    </td>
                                    <td width="50">
                                    	<input style="width:38px;" type="text" class="text_boxes_numeric" name="txtConvConsumtion" id="txtConvConsumtion" placeholder="Write" onclick="fnc_openmypage_conversion();" onChange="fnc_amount_calculation();" onBlur="" />
                                    	
                                    </td>
                               
                                    <td width="40">
                                    	<input style="width:38px;" type="text" class="text_boxes_numeric" name="txtTotConvConsumtion" id="txtTotConvConsumtion" disabled />
                                    </td>
                                    <td width="40">
                                    	<input style="width:27px;" type="text" class="text_boxes_numeric" name="txtConvRate" id="txtConvRate" placeholder="Write" onChange="fnc_amount_calculation();" />
                                    </td>
                                   
                                    <td>
                                    	<input style="width:37px;" type="text" class="text_boxes_numeric" name="txtConvValue" id="txtConvValue" placeholder="Display" readonly />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                       			
                            <table width="580" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all"> 
                              	<thead>
	                                <tr>
	                                	<th colspan="2" >Variable Cost</th>
	                                	<th colspan="2" >Fixed Cost</th>
	                                </tr>
	                                
	                            </thead>
	                            <tbody>
	                            	<tr bgcolor="#E9F3FF">
	                            	    <td width="120">
	                            	    	Yarn
	                            	    </td>
	                            	    <td width="100" >
	                            	    	<input type="text" name="txt_yarn_cost" id="txt_yarn_cost" style="width:70px;" class="text_boxes" onkeyup="calculate_total();" placeholder="Write">
	                            	    </td>
	                            	    <td width="90">
	                            	    	Lab - Test($)
	                            	    </td>
	                            	    <td width="80">
	                            	    	<input style="width:68px;" type="text" class="text_boxes" name="txt_lab_test" id="txt_lab_test"  placeholder="Write" onkeyup="calculate_total();" title=""/>
	                            	    </td>
	                            	</tr>
	                            	<tr bgcolor="#FFFFFF">
	                            	    <td width="120">
	                            	    	Sizing Chemicals
	                            	    </td>
	                            	    <td width="100" >
	                            	    	<input type="text" name="txt_sizing_chemicals" id="txt_sizing_chemicals" style="width:70px;" class="text_boxes" onkeyup="calculate_total();" placeholder="Write">
	                            	    </td>
	                            	    <td width="90">
	                            	    	Other Cost($)
	                            	    </td>
	                            	    <td width="80">
	                            	    	<input style="width:68px;" type="text" class="text_boxes" name="txt_other_cost" id="txt_other_cost"  placeholder="Write" onkeyup="calculate_total();" title=""/>
	                            	    </td>
	                            	</tr>
	                            	<tr bgcolor="#E9F3FF">
	                            	    <td width="120">
	                            	    	Dyes & Chemicals(Except Sizing)
	                            	    </td>
	                            	    <td width="100" >
	                            	    	<input type="text" name="txt_dyes_chemicals" id="txt_dyes_chemicals" style="width:70px;" class="text_boxes" onkeyup="calculate_total();" placeholder="Write">
	                            	    </td>
	                            	    <td width="90">
	                            	    	Comercial Cost ($)
	                            	    </td>
	                            	    <td width="80">
	                            	    	<input style="width:68px;" type="text" class="text_boxes" name="txt_comercial_cost" id="txt_comercial_cost"  placeholder="Write" onkeyup="calculate_total();" title=""/>
	                            	    </td>
	                            	</tr>
	                            	<tr bgcolor="#FFFFFF">
	                            	    <td width="120">
	                            	    	Gas - Utility
	                            	    </td>
	                            	    <td width="100" >
	                            	    	<input type="text" name="txt_gas_utility" id="txt_gas_utility" style="width:70px;" class="text_boxes" onkeyup="calculate_total();" placeholder="Write">
	                            	    </td>
	                            	    <td width="90">
	                            	    	Other Factory Expenses
	                            	    </td>
	                            	    <td width="80">
	                            	    	<input style="width:68px;" type="text" class="text_boxes" name="txt_other_factory_expenses" id="txt_other_factory_expenses"  placeholder="Write" onkeyup="calculate_total();" title=""/>
	                            	    </td>
	                            	</tr>

	                            	<tr bgcolor="#E9F3FF">
	                            	    <td width="120">
	                            	    	Peach Finish (One side)
	                            	    </td>
	                            	    <td width="100" >
	                            	    	<input type="text" name="txt_peach_finish_one_side" id="txt_peach_finish_one_side" style="width:70px;" class="text_boxes" onkeyup="calculate_total();" placeholder="Write">
	                            	    </td>
	                            	    <td width="90">
	                            	    	Wages & Salary 
	                            	    </td>
	                            	    <td width="80">
	                            	    	<input style="width:68px;" type="text" class="text_boxes" name="txt_wages_salary" id="txt_wages_salary"  placeholder="Write" onkeyup="calculate_total();" title=""/>
	                            	    </td>
	                            	</tr>
	                            	<tr bgcolor="#FFFFFF">
	                            	    <td width="120">
	                            	    	Peach Finish (Both side)
	                            	    </td>
	                            	    <td width="100" >
	                            	    	<input type="text" name="txt_peach_finish_both_side" id="txt_peach_finish_both_side" style="width:70px;" class="text_boxes" onkeyup="calculate_total();" placeholder="Write">
	                            	    </td>
	                            	    <td width="90">
	                            	    	Admin. & selling exp.
	                            	    </td>
	                            	    <td width="80">
	                            	    	<input style="width:68px;" type="text" class="text_boxes" name="txt_admin_selling" id="txt_admin_selling"  placeholder="Write" onkeyup="calculate_total();" title=""/>
	                            	    </td>
	                            	</tr>
	                            	<tr bgcolor="#E9F3FF">
	                            	    <td width="120">
	                            	    	ETI Finish
	                            	    </td>
	                            	    <td width="100" >
	                            	    	<input type="text" name="txt_eti_finish" id="txt_eti_finish" style="width:70px;" class="text_boxes" onkeyup="calculate_total();" placeholder="Write">
	                            	    </td>
	                            	    <td width="90">
	                            	    	Financial Exp. 
	                            	    </td>
	                            	    <td width="80">
	                            	    	<input style="width:68px;" type="text" class="text_boxes" name="txt_financial_exp" id="txt_financial_exp"  placeholder="Write" onkeyup="calculate_total();" title=""/>
	                            	    </td>
	                            	</tr>
	                            	<tr bgcolor="#FFFFFF">
	                            	    <td width="120">
	                            	    	Brush (One side)
	                            	    </td>
	                            	    <td width="100" >
	                            	    	<input type="text" name="txt_brush_one_side" id="txt_brush_one_side" style="width:70px;" class="text_boxes" onkeyup="calculate_total();" placeholder="Write">
	                            	    </td>
	                            	    <td width="90">
	                            	    	Brush (Both side)
	                            	    </td>
	                            	    <td width="80">
	                            	    	<input style="width:68px;" type="text" class="text_boxes" name="txt_brush_both_side" id="txt_brush_both_side"  placeholder="Write" onkeyup="calculate_total();" title=""/>
	                            	    </td>
	                            	</tr>
	                            	<tr bgcolor="#E9F3FF">
	                            	    <td width="120">
	                            	    	Seersucker
	                            	    </td>
	                            	    <td width="100" >
	                            	    	<input type="text" name="txt_seersucker" id="txt_seersucker" style="width:70px;" class="text_boxes" onkeyup="calculate_total();" placeholder="Write">
	                            	    </td>
	                            	    <td width="90">
	                            	    	Instalment of Term Loan  
	                            	    </td>
	                            	    <td width="80">
	                            	    	<input style="width:68px;" type="text" class="text_boxes" name="txt_instalment_of_term_loan" id="txt_instalment_of_term_loan"  placeholder="Write" onkeyup="calculate_total();" title=""/>
	                            	    </td>
	                            	</tr>
	                            	<tr bgcolor="#FFFFFF">
	                            	    <td width="120">
	                            	    	Mechanical Stretch
	                            	    </td>
	                            	    <td width="100" >
	                            	    	<input type="text" name="txt_mechanical_stretch" id="txt_mechanical_stretch" style="width:70px;" class="text_boxes" onkeyup="calculate_total();" placeholder="Write">
	                            	    </td>
	                            	    <td width="90">
	                            	    	Income Tax
	                            	    </td>
	                            	    <td width="80">
	                            	    	<input style="width:68px;" type="text" class="text_boxes" name="txt_income_tax" id="txt_income_tax"  placeholder="Write" onkeyup="calculate_total();" title=""/>
	                            	    </td>
	                            	</tr>
	                            	<tr bgcolor="#E9F3FF">
	                            	    <td width="120">
	                            	    	Only Rotary Print Cost
	                            	    </td>
	                            	    <td width="100" >
	                            	    	<input type="text" name="txt_only_rotary_print_cost" id="txt_only_rotary_print_cost" style="width:70px;" class="text_boxes" onkeyup="calculate_total();" placeholder="Write">
	                            	    </td>
	                            	    <td width="90" colspan="2">
	                            	    	
	                            	    </td>
	                            	    
	                            	</tr>
	                            </tbody>
                            </table>
                   
                    </td>
                    <td valign="top" width="250">
                    	<table width="240" cellspacing="0" cellpadding="0" border="0" class="rpt_table" rules="all">
                        	<tr valign="top">
                            	<td width="110">
                            		<strong>Buyer Agent</strong>
                            		<input type="button" class="formbutton" style="width:15px; font-style:italic" value="N" onClick="openmypage_agent_location('requires/woven_mkt_costing_controller.php?action=agent_location_popup','Create Buyer Agent',1)"/>
                            	</td>
                                <td width="80" id="agent_td">
                                	<? echo create_drop_down( "cbo_buyer_agent", 80,"select tuid, agent_location from lib_agent_location where type=1 and status_active=1 and is_deleted=0","tuid,agent_location", 1, "-Agent-", $selected, "" ); ?>
                                </td>
                                <td>
                                	<input type="button" class="formbutton" value="Approval" onClick="openmypage_style(1); "/>
                                </td>
                            </tr>
                            <tr>
                            	<td>
                            		<strong>B. Location
	                            	    <input type="button" class="formbutton" style="width:15px; font-style:italic" value="N" onClick="openmypage_agent_location('requires/woven_mkt_costing_controller.php?action=agent_location_popup','Create Location',2)"/>
	                            	</strong>
	                            </td>
                                <td id="location_td">
                                	<? echo create_drop_down( "cbo_agent_location", 80,"select tuid, agent_location from lib_agent_location where type=2 and status_active=1 and is_deleted=0","tuid,agent_location", 1, "-Location-", $selected, "" ); ?>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                            	<td style="height:28px; font-size:25px; background:#00CED1;" onClick="fnc_fobavg_option();">F.O.B $
                            	</td>
                                <td style="height:28px; font-size:25px; color:#FFFFFF; background:#FE00FF;" id="totalFob_td" prev_fob="" align="right" title="(Rmg Ratio*Fob (Pcs))*No Of. Pack's">&nbsp;
                                </td>
                                <td style="height:28px; font-size:25px; background:#00FA9A;" id="uom_td">&nbsp;</td>
                            </tr>
                            <tr></tr>
                            <tr>
                            	<td><strong>No Of. Pack's</strong></td>
                                <td>
                                	
                                	<? echo create_drop_down( "cbo_uom", 60, $unit_of_measurement,'', 1, "-- Select UOM--",$selected, "" ); ?>
                                </td>
                                <td>
                                	<input type="button" name="confirm_style" id="confirm_style" value="Confirm" onClick="fnc_confirm_style();" style="width:70px" class="formbuttonplasminus" />
                                </td>
                            </tr>
                            <tr>
                            	<td colspan="3" align="center" class="button_container">
									<? echo load_submit_buttons($permission,"fnc_qcosting_entry",0,0,"reset_fnc();",1); ?><br>
									<input type="button" id="report_btn_1" class="formbutton" style="width:30px;" value="PNT" onClick="fnc_print_report('quick_costing_print')" />&nbsp;
									<input type="button" id="report_btn_3" class="formbutton" style="width:40px;" value="PNT 3" onClick="fnc_print_report('quick_costing_print3')" />&nbsp;
									<a id="qc_report_btn_1" href="" style="text-decoration:none" download hidden>BB</a>
									<input type="button" id="report_btn_2" class="formbutton" style="width:35px;display:none;" value="PNT2" onClick="fnc_print_report('quick_costing_print2')" />&nbsp;
									<input type="button" id="set_button" class="formbutton" style="width:60px;" value="Copy" onClick="fnc_copy_cost_sheet(0);" />&nbsp;
									<input type="button" id="set_button" class="formbutton" style="width:45px;" value="Revise" onClick="fnc_qcosting_entry(6)" />&nbsp;
									<input type="button" id="set_button" class="formbutton" style="width:45px;" value="Option" onClick="fnc_qcosting_entry(7)" />
                                </td>
                            </tr>
                            <tr>
                            	<td colspan="3" align="center">
                            		<strong>Merchandiser Remarks : </strong>
                            	</td>
                            </tr>
                            <tr>
                            	<td colspan="3">
                            		<textarea id="txt_costing_remarks" pre_costing_remarks="" class="text_area" style="width:220px; height:40px;" placeholder="Merchandiser Remarks">1. </textarea>
                            	</td>
                            </tr>
                            <tr>
                            	<td colspan="3">
                            		<strong>Date & Time:</strong>
                            		<input style="width:45px;" type="text" class="datepicker" name="txt_meeting_date" id="txt_meeting_date" value="<? echo date('d-m-Y');?>" /><input name="txt_meeting_time" id="txt_meeting_time" class="text_boxes" type="text" style="width:30px;" placeholder="24 H. Format" onChange="fnc_valid_time(this.value,'txt_meeting_time');" onKeyUp="fnc_valid_time(this.value,'txt_meeting_time');" onKeyPress="return numOnly(this,event,this.id);" value="<? echo date('H:i', time()); ?>" />
                            		<strong>New Meeting No.</strong>
                            		<input type="checkbox" name="chk_is_new_meeting" id="chk_is_new_meeting" onClick="fnc_rate_write_popup('meeting');" value="2" style="width:12px;" >
                            	</td>
                            </tr>
                            <tr>
                            	<td>
                            		<strong>Meeting No:</strong>
                            	</td>
                            	<td align="center">
                            		<input style="width:55px;" type="text" class="text_boxes" name="txt_meeting_no" id="txt_meeting_no" disabled readonly />
                            	</td>
                            	<td>
                            		<input type="button" name="meeting_remarks" id="meeting_remarks" value="M.Minutes" onClick="fnc_meeting_remarks_pop_up(document.getElementById('txt_update_id').value, document.getElementById('txt_styleRef').value);" style="width:70px" class="formbuttonplasminus" />
                            	</td>
                            </tr>
                            <tr>
                            	<td colspan="3" align="center">
                            		<strong>Meeting Remarks:</strong>
                            	</td>
                            </tr>
                            <tr>
                            	<td colspan="3">
                            		<textarea id="txt_meeting_remarks" pre_meeting_remark="" class="text_area" style="width:220px; height:40px;" placeholder="Meeting Remarks">1. 
                            		</textarea>
                            	</td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellspacing="2" cellpadding="0" border="0" class="rpt_table" rules="all">
                            <tr>
                                <td valign="top">
                                    <table width="330" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                                        <thead>
                                            <tr>
                                                <th width="150">Style Ref.</th>
                                                <th width="90">Season</th>
                                                
                                                <th>User</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <div style="width:330px; max-height:100px; overflow-y:scroll" id="scroll_body" > 
                                        <table width="310" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                                            <tbody id="style_td">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="test">
                                    	<input type="button" name="clear_style" id="clear_style" value="Clear" onClick="fnc_clear_all();" style="width:50px" class="formbuttonplasminus" />&nbsp;
                                    	<input type="button" name="delete_style" id="delete_style" value="Clear ST" onClick="fnc_delete_style_row();" style="width:60px" class="formbuttonplasminus" />
                                    	<strong> Rv: </strong>
                                    	<span id="revise_td"><? echo create_drop_down( "cbo_revise_no", 45, $blank_array,'', 1, "-0-",$selected, "","","","","","" ); ?>
                                    	</span>
                                    	<strong> Op: </strong>
                                    	<span id="option_td"><? echo create_drop_down( "cbo_option_id", 45, $blank_array,'', 1, "-0-",1, "","","","","","" ); ?>
                                    	</span>
                                    </div>
                                   <div title="Option Reason/Remarks">
	                                   	<textarea id="txt_option_remarks" pre_opt_remarks="" class="text_area" style="width:250px; height:40px;" placeholder="Option Reason/Remarks">
	                                   	</textarea>
	                                </div> 
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" id="summary_td">
                                    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
                                        <thead>
                                            
                                            <tr>
                                                <th width="120">Particulars</th>
                                                <th width="70">P/YRD(USD)</th>
                                                <th>P/MTR(USD)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>FOB</td>
                                                <td  align="right">
                                                	<input type="text" name="txt_fob_p_yrd" id="txt_fob_p_yrd" class="text_boxes_numeric" style="width: 50px;">
                                                </td>
                                                <td>
                                                	<input type="text" name="txt_fob_p_mtr" id="txt_fob_p_mtr" class="text_boxes_numeric" style="width: 50px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Upcharge</td>
                                                <td  align="right">
                                                	<input type="text" name="txt_upcharge_p_yrd" id="txt_upcharge_p_yrd" class="text_boxes_numeric" style="width: 50px;" onkeyup="calculate_total();">
                                                </td>
                                                <td>
                                                	<input type="text" name="txt_upcharge_p_mtr" id="txt_upcharge_p_mtr" class="text_boxes_numeric" style="width: 50px;">
                                                </td>
                                            <tr>
                                                <td>Discount</td>
                                                <td  align="right">
                                                	<input type="text" name="txt_discount_p_yrd" id="txt_discount_p_yrd" class="text_boxes_numeric" style="width: 50px;" onkeyup="calculate_total();">
                                                </td>
                                                <td>
                                                	<input type="text" name="txt_discount_p_mtr" id="txt_discount_p_mtr" class="text_boxes_numeric" style="width: 50px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>T.COST</td>
                                                <td  align="right">
                                                	<input type="text" name="txt_t_cost_p_yrd" id="txt_t_cost_p_yrd" class="text_boxes_numeric" style="width: 50px;">
                                                </td>
                                                <td>
                                                	<input type="text" name="txt_t_cost_p_mtr" id="txt_t_cost_p_mtr" class="text_boxes_numeric" style="width: 50px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>S.P</td>
                                                <td  align="right">
                                                	<input type="text" name="txt_s_p_p_yrd" id="txt_s_p_p_yrd" class="text_boxes_numeric" style="width: 50px;" onkeyup="calculate_total();">
                                                </td>
                                                <td>
                                                	<input type="text" name="txt_s_p_p_mtr" id="txt_s_p_p_mtr" class="text_boxes_numeric" style="width: 50px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>PROFIT/(LOSS)</td>
                                                <td  align="right">
                                                	<input type="text" name="txt_profit_loss_p_yrd" id="txt_profit_loss_p_yrd" class="text_boxes_numeric" style="width: 50px;">
                                                </td>
                                                <td>
                                                	<input type="text" name="txt_profit_loss_p_mtr" id="txt_profit_loss_p_mtr" class="text_boxes_numeric" style="width: 50px;">
                                                </td>
                                            </tr>
                                        </tbody>
                                   </table>
                                </td>
                            </tr>
                        </table>
                    </td>
            	</tr>
            </table>
            </fieldset>
        </form>
        </div>
        <div style="display:none" id="data_panel"></div>
    </body>
    <script>
		jQuery("#txt_costing_remarks").keyup(function(e) 
		{
			var c = String.fromCharCode(e.which);
			var evt = (e) ? e : window.event;
			var key = (evt.keyCode) ? evt.keyCode : evt.which;
			// var key = e.keyCode;
			 //alert (key )
			if (key == 13) 
			{
				var text = $("#txt_costing_remarks").val();   
				var lines = text.split(/\r|\r\n|\n/);
				var count = (lines.length*1)+1;
				document.getElementById("txt_costing_remarks").value =document.getElementById("txt_costing_remarks").value + "\n"+count+". ";
				return false;
			}
			else {
				return true;
			}
		});
		
		jQuery("#txt_meeting_remarks").keyup(function(e) 
		{
			var c = String.fromCharCode(e.which);
			var evt = (e) ? e : window.event;
			var key = (evt.keyCode) ? evt.keyCode : evt.which;
			// var key = e.keyCode;
			 //alert (key )
			if (key == 13) 
			{
				var text = $("#txt_meeting_remarks").val();   
				var lines = text.split(/\r|\r\n|\n/);
				var count = (lines.length*1)+1;
				document.getElementById("txt_meeting_remarks").value =document.getElementById("txt_meeting_remarks").value + "\n"+count+". ";
				return false;
			}
			else {
				return true;
			}
		});
	</script>
    <script> 
		var style_id='';
		var temp_style_list=return_ajax_request_value(style_id+'__'+0, 'temp_style_list_view', 'requires/woven_mkt_costing_controller');
		$('#style_td').html( temp_style_list );
		//$('#style_td').html( localStorage.getItem("temp_style_list_view") ); 
		//fnc_select(); fnc_meeting_no(0); print_report_button_setting();
    </script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>