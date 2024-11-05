<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create batch creation For Gmts. Wash
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	23.04.2015
Updated by 		: 	
Update date		: 	
Report by		:	
Creation date 	: 	
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Batch Creation For Gmts. Wash Info", "../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	var garments_item_array=[];
	<?
		$jsgarments_item= json_encode($garments_item);
		echo "garments_item_array = ". $jsgarments_item . ";\n";
	?>
	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];
	$(document).ready(function(e)
	{
		$("#txt_batch_color").autocomplete({
			source: str_color
		});
	});
	
	function add_break_down_tr( i )
	{ 
		if (form_validation('cbo_company_id*txtPoNo_'+i,'Company Name*PO No')==false)
		{
			return;
		}
		
		var row_num=$('#tbl_item_details tbody tr').length;
		if (row_num!=i)
		{
			return false;
		}
		else
		{ 
			i++;
	
			$("#tbl_item_details tbody tr:last").clone().find("input,select").each(function(){
				  
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
			  'value': function(_, value) { return value }              
			});
			 
			}).end().appendTo("#tbl_item_details");
				
			$("#tbl_item_details tbody tr:last").removeAttr('id').attr('id','tr_'+i);

			$('#updateIdDtls_'+i).removeAttr("value").attr("value","");
			$('#poId_'+i).removeAttr("value").attr("value","");
			$('#txtPoNo_'+i).removeAttr("value").attr("value","");
			$('#txtGmtsQty_'+i).removeAttr("value").attr("value","");
			$('#txtBatchQnty_'+i).removeAttr("value").attr("value","");
			$('#txtPoNo_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_po("+i+");");
			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
		}
		set_all_onclick();
		calculate_batch_qnty();
	}
	
	function fn_deleteRow(rowNo) 
	{ 
		var numRow = $('#tbl_item_details tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			var updateIdDtls=$('#updateIdDtls_'+rowNo).val();
			var txt_deleted_id=$('#txt_deleted_id').val();
			var selected_id='';
		
			if(updateIdDtls!='')
			{
				if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
				$('#txt_deleted_id').val( selected_id );
			}
			$('#tbl_item_details tbody tr:last').remove();
		}
		else
		{
			return false;
		}
		
		calculate_batch_qnty();
	}
	
	function calculate_batch_qnty()
	{
		var numRow = $('#tbl_item_details tbody tr').length;
		var ddd={ dec_type:2, comma:0};
		var dd={ dec_type:6, comma:0};
		math_operation( "txt_total_gmts_qnty", "txtGmtsQty_", "+",numRow,dd );
		math_operation( "txt_total_batch_qnty", "txtBatchQnty_", "+",numRow,ddd );
		
		var txt_total_batch_qnty=$('#txt_total_batch_qnty').val();
		$('#txt_batch_weight').val(txt_total_batch_qnty);
	}
	
	function openmypage_po(row_no)
	{
		var cbo_company_id=$('#cbo_company_id').val();
		var buyer_id=$('#buyer_id').val();
		var tot_row=$('#tbl_item_details tbody tr').length;
		
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		var title = 'PO Selection Form';	
		var page_link = 'requires/batch_creation_for_gmts_wash_controller.php?cbo_company_id='+cbo_company_id+'&buyer_id='+buyer_id+'&tot_row='+tot_row+'&action=po_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var po_id=this.contentDoc.getElementById("po_id").value; //Access form field with id="emailfield"
			var po_no=this.contentDoc.getElementById("po_no").value; //Access form field with id="emailfield"
			var gmts_item_id=this.contentDoc.getElementById("gmts_item_id").value.split(","); //Access form field with id="emailfield"
			var buyer_id=this.contentDoc.getElementById("buyer_id").value; //Access form field with id="emailfield"
			
			$('#poId_'+row_no).val(po_id);
			$('#txtPoNo_'+row_no).val(po_no);
			$('#buyer_id').val(buyer_id);
			
			$("#cboItem_"+ row_no +" option[value!='0']").remove();
			for(var i=0; i<gmts_item_id.length; i++)
			{	
				$('#cboItem_'+row_no).append("<option value='"+gmts_item_id[i]+"'>"+garments_item_array[gmts_item_id[i]]+"</option>");
			}
			
			var item_length=$("#cboItem_"+ row_no +" option").length;
			if(item_length==2)
			{
				$('#cboItem_'+ row_no).val($("#cboItem_"+ row_no +" option:last").val());
			}
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/batch_creation_for_gmts_wash_controller.php?data=" + data+'&action='+action, true );
	}
	
	function fnc_batch_creation(operation)
	{
		if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title,'batch_card_print','requires/batch_creation_for_gmts_wash_controller');
			 return;
		}
			
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if($('#batch_no_creation').val()!=1)
		{
			if( form_validation('txt_batch_number','Batch Number')==false )
			{
				alert("Plesae Insert Batch No.");
				$('#txt_batch_number').focus();
				return;
			}
		}
		
		if($('#txt_batch_weight').val()*1 < 0.1)
		{
			alert('Please Insert Batch Weight.');
			$('#txt_batch_weight').focus();
			return;
		}
		var cbo_batch_against = $('#cbo_batch_against').val();
		if(cbo_batch_against==7)
		{
			if(form_validation('txt_batch_color','Batch Color')==false )
			{
				return;
			}
		}
		
		if( form_validation('cbo_batch_against*cbo_company_id*cbo_working_company_id*txt_batch_date*txt_batch_weight','Batch Against*Company*Batch Date*Batch Weight')==false )
		{
			return;
		}
		
		if($('#txt_batch_weight').val()*1!=$('#txt_total_batch_qnty').val()*1)
		{
			alert('Batch Weight and Total Batch Qnty should be same.');
			return;
		}
		
		var txt_deleted_id=$('#txt_deleted_id').val();
		var row_num=$('#tbl_item_details tbody tr').length;
		var data_all="";

		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('txtPoNo_'+i+'*cboItem_'+i+'*txtBatchQnty_'+i,'PO No.*Gmts. Item*Batch Qnty')==false)
			{
				return;
			}
			
			data_all+=get_submitted_data_string('poId_'+i+'*cboItem_'+i+'*txtGmtsQty_'+i+'*txtBatchQnty_'+i+'*updateIdDtls_'+i,"../",2);
		}
		
		//alert(data_all);return;
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_batch_sl_no*cbo_batch_against*cbo_batch_for*cbo_company_id*cbo_working_company_id*batch_no_creation*txt_batch_number*txt_batch_date*txt_batch_weight*txt_ext_no*txt_batch_color*cbo_color_range*txt_organic*txt_process_id*txt_du_req_hr*txt_du_req_min*update_id*machine_id*txt_remarks',"../")+data_all+'&total_row='+row_num+'&txt_deleted_id='+txt_deleted_id;

		freeze_window(operation);
		
		http.open("POST","requires/batch_creation_for_gmts_wash_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_batch_creation_Reply_info;
	}
	
	function fnc_batch_creation_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing(); alert(http.responseText);return;
			var reponse=trim(http.responseText).split('**');	
				
			show_msg(reponse[0]);
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_batch_creation('+ reponse[1] +')',8000); 
				 return;
			}
			else if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_batch_sl_no').value = reponse[2];
				var batch_against=$('#cbo_batch_against').val();
				var batch_for=$('#cbo_batch_for').val();
				
				show_list_view(batch_against+'**'+batch_for+'**'+reponse[1],'batch_details','batch_details_container','requires/batch_creation_for_gmts_wash_controller','');
				set_button_status(1, permission, 'fnc_batch_creation',1);
			}
			release_freezing();	
		}
	}
	
	function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_against = $('#cbo_batch_against').val();
		var batch_for = $('#cbo_batch_for').val();
		
		if (form_validation('cbo_batch_against*cbo_company_id','Batch Against*Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Batch No Selection Form';	
			var page_link = 'requires/batch_creation_for_gmts_wash_controller.php?cbo_company_id='+cbo_company_id+'&batch_against='+batch_against+'&action=batch_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;	 //Access form field with id="emailfield"
				//alert(theemail);return;
				if(batch_id!="")
				{
					freeze_window(5);
					get_php_form_data(batch_against+'**'+batch_for+'**'+batch_id, "populate_data_from_search_popup", "requires/batch_creation_for_gmts_wash_controller" );
				    show_list_view(batch_against+'**'+batch_for+'**'+batch_id,'batch_details','batch_details_container','requires/batch_creation_for_gmts_wash_controller','');
					release_freezing();
					$('#txt_deleted_id').val('');
					calculate_batch_qnty();
				} 
			}
		}
	}
	
	function openmypage_process()
	{
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';	
		var page_link = 'requires/batch_creation_for_gmts_wash_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;
			$('#txt_process_id').val(process_id);
			$('#txt_process_name').val(process_name);
		}
	}
	
	function fnc_move_cursor(val,id, field_id,lnth,max_val)
	{
		var str_length=val.length;
		if(str_length==lnth)
		{
			$('#'+field_id).select();
			$('#'+field_id).focus();
		}
		
		if(val>max_val)
		{
			document.getElementById(id).value=max_val;
		}
	}
	
	function fn_machine_seach()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Machine No Selection Form';	
			var page_link = 'requires/batch_creation_for_gmts_wash_controller.php?cbo_company_id='+cbo_company_id+'&action=machineNo_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=755px,height=350px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var machine_id=this.contentDoc.getElementById("hidden_machine_id").value;	
				var machine_name=this.contentDoc.getElementById("hidden_machine_name").value;	
				
				$('#machine_id').val(machine_id);
				$('#txt_machine_no').val(machine_name);
			}
		}
	}
	function validate_check(str)
	{
	
		if(str==7)
		{
			$('#batch_color_td').css('color','blue');
		}
		else
		{
			$('#batch_color_td').css('color','black');
		}
	}
	
</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../",$permission); ?>
    <fieldset style="width:670px;">
    <legend>Batch Creation</legend> 
        <form name="batchcreation_1" id="batchcreation_1"> 
            <fieldset style="width:640px;">
                <table width="630" align="center" border="0">
                    <tr>
                        <td width="110" colspan="2" align="right"><b>Batch Serial No</b></td>
                        <td colspan="2">
                            <input type="text" name="txt_batch_sl_no" id="txt_batch_sl_no" class="text_boxes" style="width:160px;" placeholder="Display" disabled />
                        </td>
                    </tr>
                    <tr><td></td></tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Batch Against</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_batch_against", 172, $batch_against,"",1, '--- Select ---', 1, "validate_check(this.value)",'','6,7,8','','','',1 );
                            ?>                              
                        </td>
                        <td>Batch For</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_batch_for", 172, $batch_for,"", 1, '', 0, "",'1','0','','','' );
							  
                            ?>                              
                        </td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Company</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_id", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name",'id,company_name', 1, '--Select Company--', 0,"get_php_form_data(this.value,'batch_no_creation','requires/batch_creation_for_gmts_wash_controller');",'','','','','',3);
                            ?>                              
                        </td>
                        <td width="130" class="must_entry_caption">Batch Date</td>
                        <td>
                            <input type="text" name="txt_batch_date" id="txt_batch_date" class="datepicker" style="width:160px;" tabindex="4" value="<? echo date("d-m-Y"); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Batch Number</td>
                        <td>
                            <input type="text" name="txt_batch_number" id="txt_batch_number" class="text_boxes" style="width:160px;" placeholder="Double Click To Edit" onDblClick="openmypage_batchNo()" tabindex="5" />
                        </td>
                        <td>Extention No.</td>
                        <td>
                            <input type="text" name="txt_ext_no" id="txt_ext_no" class="text_boxes_numeric" style="width:160px;" disabled="disabled" tabindex="6" />
                        </td>
                    </tr>
                    <tr>
                        <td  id="batch_color_td">Batch Color</td>
                        <td>
                            <input type="text" name="txt_batch_color" id="txt_batch_color" class="text_boxes" value="" style="width:160px;" tabindex="7" />
                        </td>
                        <td width="110" class="must_entry_caption">Batch Weight </td>
                        <td>
                            <input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:160px;" readonly tabindex="8" />
                        </td>
                    </tr>
                    <tr>
                        <td>Color Range</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_color_range", 172, $color_range,"",1, "-- Select --", 0, "",'','','','','',9);
                            ?>
                        </td>
                        <td>Organic</td>
                        <td>
                            <input type="text" name="txt_organic" id="txt_organic" class="text_boxes" style="width:160px;" tabindex="10" />
                        </td>
                    </tr>
                    <tr>
                    	<td>Process Name</td>
                        <td>
                            <input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:160px;" placeholder="Double Click To Search" onDblClick="openmypage_process();" tabindex="11" readonly />
                            <input type="hidden" name="txt_process_id" id="txt_process_id" value="" />
                        </td>
                        <td>Duration Req.</td>
                        <td>
                            <input type="text" name="txt_du_req_hr" id="txt_du_req_hr" class="text_boxes_numeric" placeholder="Hour" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_hr','txt_end_date',2,23)" style="width:70px;" tabindex="12"/>&nbsp;
                            <input type="text" name="txt_du_req_min" id="txt_du_req_min" class="text_boxes_numeric" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_min','txt_end_date',2,59)" placeholder="Minute" style="width:70px;" tabindex="13" />
                        </td>
                    </tr>
                    <tr>
                    	<td>Machine No</td>
                           <td>
                                <input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes" tabindex="14" style="width:160px;" onDblClick="fn_machine_seach();" placeholder="Browse" readonly/>
                                <input type="hidden" name="machine_id" id="machine_id" class="text_boxes"/>
                          </td>
                        
                        <td>Remarks</td>
                        <td colspan="2"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" tabindex="15" style="width:84%;" /></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Working Company</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_working_company_id", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--Select Company--', 0,"get_php_form_data(this.value,'batch_no_creation','requires/batch_creation_for_gmts_wash_controller');",'','','','','',3);
                            ?>                              
                        </td>
                    </tr>
                 </table>
            </fieldset>                 
            <fieldset style="width:650px; margin-top:10px">
            <legend>Item Details</legend>
                <table cellpadding="0" cellspacing="0" width="640" class="rpt_table" border="1" rules="all" id="tbl_item_details">
                    <thead>
                        <th class="must_entry_caption">PO No.</th>
                        <th class="must_entry_caption">Gmts. Item</th>
                        <th>Gmts Qty.</th>
                        <th class="must_entry_caption">Batch Qty.</th>
                        <th></th>
                    </thead>
                    <tbody id="batch_details_container">
                        <tr class="general" id="tr_1">
                            <td>						 
                                <input type="text" name="txtPoNo_1" id="txtPoNo_1" class="text_boxes" style="width:130px;" placeholder="Double Click to Search" onDblClick="openmypage_po(1)" readonly />
                                <input type="hidden" name="poId_1" id="poId_1" class="text_boxes" readonly />
                                <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" class="text_boxes" readonly />
                            </td>                             
                            <td>
                                <?
                                    echo create_drop_down( "cboItem_1", 180, $garments_item,"", 1, "-- Select Item --", 0, "" );
                                ?>
                            </td>
                            <td>
                                <input type="text" name="txtGmtsQty_1" id="txtGmtsQty_1" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:75px" />
                            </td>
                            <td>
                                <input type="text" name="txtBatchQnty_1" id="txtBatchQnty_1" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:75px" />
                            </td>
                            <td width="65">
                                <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" />
                                <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="tbl_bottom">
                        <td><input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" readonly /></td>
                        <td>Sum</td>
                        <td style="text-align:center"><input type="text" name="txt_total_gmts_qnty" id="txt_total_gmts_qnty" class="text_boxes_numeric" style="width:75px" readonly /></td>
                        <td style="text-align:center"><input type="text" name="txt_total_batch_qnty" id="txt_total_batch_qnty" class="text_boxes_numeric" style="width:75px" readonly /></td>
                        <td>&nbsp;</td>
                    </tfoot>
                </table>
            </fieldset> 
            <table width="640">
                <tr>
                    <td colspan="4" align="center" class="button_container">
                        <? 
                            $date=date('d-m-Y');
                            echo load_submit_buttons($permission, "fnc_batch_creation",0,1,"reset_form('batchcreation_1','','','txt_batch_date,".$date."',''); $('#tbl_item_details tbody tr:not(:first)').remove();",1);
                        ?> 
                        <input type="hidden" name="update_id" id="update_id"/>
                        <input type="hidden" name="batch_no_creation" id="batch_no_creation" readonly>
                        <input type="hidden" name="buyer_id" id="buyer_id" readonly>
                    </td>	  
                </tr>
            </table>
        </form>
    </fieldset>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>