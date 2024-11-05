<?
/*--- ----------------------------------------- Comments
Purpose			: 						
Created by		:	Rakib
Creation date 	: 	07-11-2021
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
//----------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("C and F Bill slub Entry", "../../", 1, 1,$unicode,'','');
?>
<script type="text/javascript">

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function fnc_cnf_bill_slub_entry( operation )
	{
		if ( form_validation('cbo_company_name*cbo_type_name*txt_slub_name','Company*C&F Type*Slub Name')==false )
		{
			return;
		}

		var j=0; data_all=""; 
		var txt_deleted_id 		= $('#txt_deleted_id').val();
		var update_id 		    = $('#update_id').val();
		var cbo_company_name 	= $('#cbo_company_name').val();
		var cbo_type_name 		= $('#cbo_type_name').val();
		var txt_slub_name 		= $('#txt_slub_name').val();
			
		$("#tbl_dtls_emb tbody tr").each(function()
		{
			var txtFromUnit 	 = $(this).find('input[name="txtFromUnit[]"]').val();
			var txtToUnit 		 = $(this).find('input[name="txtToUnit[]"]').val();
			var txtCharge 		 = $(this).find('input[name="txtCharge[]"]').val();
			var hdnDtlsUpdateId  = $(this).find('input[name="hdnDtlsUpdateId[]"]').val();
			j++

			data_all += "&txtFromUnit_" + j + "='" + txtFromUnit + "'&txtToUnit_" + j + "='" + txtToUnit + "'&txtCharge_" + j + "='" + txtCharge + "'&hdnDtlsUpdateId_" + j + "='" + hdnDtlsUpdateId + "'";
		});		

		var data="action=save_update_delete&operation="+operation+'&total_row='+j+'&cbo_company_name='+cbo_company_name+'&cbo_type_name='+cbo_type_name+'&txt_slub_name='+"'"+txt_slub_name+"'"+'&txt_deleted_id='+txt_deleted_id+'&update_id='+update_id+data_all;
		//alert (data); return;
		freeze_window(operation);
		http.open("POST","requires/cnf_bill_slub_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_cnf_bill_slub_entry_reponse;
	}
	
	function fnc_cnf_bill_slub_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			if(response[0]==0 || response[0]==1|| response[0]==2)
			{
				show_msg(trim(response[0]));
				show_list_view(company,'dtls_list_view', 'list_view', 'requires/cnf_bill_slub_entry_controller', '');
				set_button_status(0, permission, 'fnc_cnf_bill_slub_entry',1);
			}
			else if(response[0]=="26")
			{
				alert ("Duplicate Data Found .Operation Not Complete.");
				release_freezing();
				return;
			}
		}
		release_freezing();
	}
	

	function fnc_addRow( i, table_id, tr_id )
	{ 
		//alert();
		var prefix=tr_id.substr(0, tr_id.length-1);
		var row_num = $('#tbl_dtls_emb tbody tr').length; 
		//alert(i+"**"+table_id+"**"+tr_id+"**"+row_num);
		row_num++;
		var clone= $("#"+tr_id+i).clone();
		clone.attr({
			id: tr_id + row_num,
		});
		
		clone.find("input,select").each(function(){
			$(this).attr({ 
				'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
				'name': function(_, name) { return name },
				'value': function(_, value) { return value }
			});
		}).end();
		$("#"+tr_id+i).after(clone);


		$('#hdnDtlsUpdateId_'+row_num).removeAttr("value").attr("value","");
		$('#txtFromUnit_'+row_num).removeAttr("value").attr("value","");
		$('#txtToUnit_'+row_num).removeAttr("value").attr("value","");
		$('#txtCharge_'+row_num).removeAttr("value").attr("value","");
		$('#increase_'+row_num).removeAttr("value").attr("value","+");
		$('#decrease_'+row_num).removeAttr("value").attr("value","-");
		$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fnc_addRow("+row_num+",'"+table_id+"','"+tr_id+"');");
		$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fnc_deleteRow("+row_num+",'"+table_id+"','"+tr_id+"');");
		set_all_onclick();
	}

	function fn_deletebreak_down_tr(rowNo,table_id) 
	{   
		var numRow = $('table#tbl_dtls_emb tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_dtls_emb tbody tr:last').remove();
		}
	}

	function show_detail_form(mst_id)
	{
		show_list_view(mst_id,'show_detail_form','form_div','requires/cnf_bill_slub_entry_controller','');
	}

	function fnc_deleteRow(rowNo,table_id,tr_id) 
	{ 
		var numRow = $('#'+table_id+' tbody tr').length; 
		var prefix=tr_id.substr(0, tr_id.length-1);
		var total_row=$('#'+prefix+'_tot_row').val();
		
		var numRow = $('table#tbl_dtls_emb tbody tr').length; 
		if(numRow!=1)
		{
			var updateIdDtls=$('#hdnDtlsUpdateId_'+rowNo).val();
			var txt_deleted_id=$('#txt_deleted_id').val();
			var selected_id='';
			
			if(updateIdDtls!='')
			{
				if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
				$('#txt_deleted_id').val( selected_id );
			}
			
			$("#"+tr_id+rowNo).remove();
			$('#'+prefix+'_tot_row').val(total_row-1);
			//calculate_total_amount(1);
		}
		else
		{
			return false;
		}
	}

	/*function fnc_onclick_from_data(ids)
	{	
		get_php_form_data(ids, "load_php_data_to_form", "requires/cnf_bill_slub_entry_controller");
		var list_view_orders = return_global_ajax_value( ids, 'load_php_dtlsdata_to_form', '', 'requires/cnf_bill_slub_entry_controller');
			$('#hardware_details_container').html(list_view_orders);
		show_list_view(company,'load_php_dtlsdata_to_form','landing_slub_tbody','requires/cnf_bill_slub_entry_controller', '');
	}*/

	function load_landing_slub_list()
	{
		var company=$('#cbo_company_name').val();
		show_list_view(company,'dtls_list_view', 'list_view', 'requires/cnf_bill_slub_entry_controller', '');
		set_button_status(0, permission, 'fnc_cnf_bill_slub_entry',1);
		//$("#landing_slub_tbody").remove().appendTo("#landing_slub_tbody");
		//if(company==0) set_button_status(0, permission, 'fnc_cnf_bill_slub_entry',1);
		//else set_button_status(0, permission, 'fnc_cnf_bill_slub_entry',1);
	}
</script>

</head>	
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%; position:relative; margin-bottom:5px; margin-top:5px">
		<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="cnfbillslubentry_1" id="cnfbillslubentry_1" autocomplete="off">
            <fieldset style="width:480px;">
                <legend>C and F Bill slub Entry</legend>
                <div id="form_div">
                	<table>
                		<tr>
	                    	<td width="120" class="must_entry_caption"><strong>Company Name</strong></td>
	                        <td width="200"><? echo create_drop_down( "cbo_company_name", 200, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_landing_slub_list();"); ?> 
	                        </td>
	                    </tr>
	                    <tr>
		                    <td class="must_entry_caption">C&F Type</td>
                            <td>
                            	<? echo create_drop_down( "cbo_type_name",200,array(1=>"Export",2=>"Import"),'',1,'--Select--',0,"fn_container(this.value)",0); ?>
                            </td>
                        </tr>
                        <tr>
		                    <td class="must_entry_caption">Slub Name</td>
                            <td>
                            	<input type="text" name="txt_slub_name" id="txt_slub_name" style="width:190px" class="text_boxes" />
                            </td>
                        </tr> 
                	</table>
                    <table width="100%" border="0" id="tbl_dtls_emb" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
                        <thead>
                        	<tr><th colspan="4" style="text-align: left;">Landing Charge Slub<th></tr>
                            <tr>
                            	<th width="120" class="must_entry_caption">From Unit</th>
                            	<th width="120" class="must_entry_caption">To Unit</th>
                            	<th width="120" class="must_entry_caption">Charge</th>
                            	<th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody id="landing_slub_tbody">
                        	<tr id="row_1" align="center">
					            <td><input type="text" name="txtFromUnit[]" id="txtFromUnit_1" style="width:120px" class="text_boxes_numeric" /></td>
					            <td><input type="text" name="txtToUnit[]" id="txtToUnit_1" style="width:120px" class="text_boxes_numeric" /></td>
					            <td><input type="text" name="txtCharge[]" id="txtCharge_1" style="width:120px" class="text_boxes_numeric" /></td>
					            <td> 
					               	<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_emb','row_')" />
									<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_emb','row_');" />
									<input id="hdnDtlsUpdateId_1" name="hdnDtlsUpdateId[]" type="hidden" class="text_boxes_numeric" style="width:40px" />					                
					            </td>  
					        </tr>
                        </tbody>
                    </table>
                </div>
                <table width="100%" border="" cellpadding="0" cellspacing="0"  rules="all">
                    <tr>
                        <td colspan="4" align="center" class="button_container"><? echo load_submit_buttons( $permission, "fnc_cnf_bill_slub_entry", 0,0 ,"reset_form('cnfbillslubentry_1','','')",1); ?> 
                        </td>
                        <input type="hidden" name="txt_deleted_id[]" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly />	
                        <input type="hidden" id="update_id" name="update_id"  class="text_boxes" style="width:20px" value=""  /> 	
                    </tr>
                </table>
            </fieldset>
            <div id="list_view"></div>
            <div id="xyz"></div>
        </form>	
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		<!-- <script type="text/javascript">load_landing_slub_list()</script> -->
    </div>
</body>

</html>
