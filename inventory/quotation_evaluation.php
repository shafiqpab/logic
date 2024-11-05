<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Quotation Evaluation
				
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	08-09-2013
Updated by 		: 		
Update date		: 		   
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
echo load_html_head_contents("Quotation Evaluation Info","../", 1, 1, $unicode,1,1); 
?>	
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

<!--Requisition No popup-->
function openmypage_requisition()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
    }
	  var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_item_category_id').value;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/quotation_evaluation_controller.php?action=quotation_evaluation_popup&data='+data, 'Quotation Evaluation Search', 'width=950px,height=450px,center=1,resize=0,scrolling=0','')
	emailwindow.onclose=function()
	{
 		var theemail=this.contentDoc.getElementById("selected_job");
		if (theemail.value!="")
		{
			freeze_window(5);
			var response=theemail.value.split('_');
			//alert(response);return;
			$('#req_id').val(response[0]);
			$('#txt_requisition_no').val(response[1]);
			show_list_view( response[0],'requisition_container_dtls','requisition_container_dtls','requires/quotation_evaluation_controller','');
			release_freezing();
		}
	}
}
<!--System ID-->
function open_qepopup()
{ 
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	} 
	// var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_item_category_id').value;	
	var page_link='requires/quotation_evaluation_controller.php?action=quot_popup&company='+$("#cbo_company_id").val(); 

	var title="Search QE Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0',' ')
	emailwindow.onclose=function()
	{
		var sysNumber = this.contentDoc.getElementById("hidden_sys_number"); 
		var sysNumber=sysNumber.value.split('_');
		get_php_form_data( sysNumber[0], "load_php_mst_form", "requires/quotation_evaluation_controller" );
		
		show_list_view(sysNumber[1],'requisition_container_dtls','requisition_container_dtls','requires/quotation_evaluation_controller','');
		show_list_view(sysNumber[0],'show_dtls_list_view','list_container','requires/quotation_evaluation_controller','');
		reset_form('','','cbo_supplier_id*txt_quotaion_ref*hidden_requsition*update_id_dtls_id','','','');
		set_button_status(0, permission, 'fnc_quotation_evaluation',1,1);
 	}
}

function js_set_value_des( id )
{
	var reponse=id.split('_');
	var req_item=reponse[1]+', '+reponse[2]+', '+reponse[3]+', '+reponse[4];
	document.getElementById("hidden_requsition").value=reponse[0];
	document.getElementById("txt_requisition_item").value=req_item;
	reset_form('','','cbo_1*txtevaluationfactor_1','','clear_table()');	
	validate_supplier();

}

function add_factor_row( i ) 
{	
	if( form_validation('cbo_company_id*txt_requisition_item','Company Name*Item')==false )
	{
		return;
    }
	var row_num=$('#evaluation_tbl tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
		i++;
	$("#evaluation_tbl tr:last").clone().find("input,select").each(function() {
		$(this).attr({
		  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
		  'name': function(_, name) { return name + i },
		  'value': function(_, value) { return value }              
		});
	}).end().appendTo("#evaluation_tbl");
		var k=i-1;
		$('#incrementfactor_'+k).hide();
	  	$('#decrementfactor_'+k).hide();
		$('#updateiddtls_'+i).val('');
	  
	  $('#incrementfactor_'+i).removeAttr("onClick").attr("onClick","add_factor_row("+i+");");
	  $('#decrementfactor_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+','+'"evaluation_tbl"'+");");
}

function fn_deletebreak_down_tr(rowNo,table_id ) 
{
	var numRow = $('#'+table_id+' tbody tr').length;
	if(numRow==rowNo && rowNo!=1)
	{
		var k=rowNo-1;
		$('#incrementfactor_'+k).show();
		$('#decrementfactor_'+k).show();
		
		$('#'+table_id+' tbody tr:last').remove();
	}
	else
		return false;
	
}
<!--for insert value-->
function fnc_quotation_evaluation(operation)
{ 
	if(operation==4)
	 {
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#cbo_item_category_id').val()+'*'+$('#cbo_location_name').val()+'*'+$('#txt_requisition_date').val()+'*'+$('#txt_requisition_no').val()+'*'+$('#hidden_quotation').val()+'*'+$('#update_id_dtls_id').val()+'*'+$('#hidden_requsition').val()+'*'+$('#req_id').val()+'*'+$('#txt_comment').val()+'*'+report_title, "quotation_evaluation_print", "requires/quotation_evaluation_controller" ) 
		 return;
	 }
		else if(operation==0 || operation==1 || operation==2)
	{
 
	if( form_validation('cbo_company_id*cbo_item_category_id*cbo_location_name*txt_requisition_no*txt_requisition_date*cbo_supplier_id*txt_quotaion_ref*txt_requisition_item*cbo_1*txtevaluationfactor_1','Company Name*Item Category*Location Name*Requisition No*Requisition Date*Supplier Name*Quotaion Ref*Requisition Item*Quot Evaluation Factor*Evaluation Factor')==false )
	{
		return;
    }
	else{
	var tot_row=$('#evaluation_tbl tbody tr').length;
	var data1="action=save_update_delete&operation="+operation+"&tot_row="+tot_row+get_submitted_data_string( 'txt_system_id*cbo_company_id*cbo_item_category_id*cbo_location_name*txt_requisition_date*txt_comment*req_id*hidden_quotation*cbo_supplier_id*txt_quotaion_ref*txt_requisition_item*hidden_requsition*update_id_dtls_id',"../");
	
	var data2='';
	for(var i=1; i<=tot_row; i++)
	{
		data2+=get_submitted_data_string('cbo_'+i+'*txtevaluationfactor_'+i+'*updateiddtls_'+i,"../",i);
	}
	var data=data1+data2;
	//alert(data);
	http.open("POST","requires/quotation_evaluation_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_quotation_evaluation_reponse;
	}
	}
}

function fnc_quotation_evaluation_reponse()
{	
	if(http.readyState == 4) 
	{			
		//alert(http.responseText);release_freezing();return;
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==11)
		{
			 show_msg(reponse[0]);return;
		}
		else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			show_msg(reponse[0]);
			document.getElementById('hidden_quotation').value = reponse[1];
			document.getElementById('txt_system_id').value = reponse[2];
			document.getElementById('update_id_dtls_id').value = reponse[3];
			show_list_view(reponse[1],'show_dtls_list_view','list_container','requires/quotation_evaluation_controller','');
			reset_form('','','cbo_supplier_id*txt_quotaion_ref*update_id_dtls_id','','','');
			var eval_fac = return_global_ajax_value( reponse[0], 'primary_eval_fac_body', '', 'requires/quotation_evaluation_controller');
			$('#eval_fac_body').html("");
			$('#eval_fac_body').html(eval_fac);
			set_button_status(0, permission, 'fnc_quotation_evaluation',1,1);
			release_freezing();
			
		}
		else
		{
			show_msg(reponse[0]);
			release_freezing();
		}
	    

	}
}

function validate_supplier()
{
	var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_item_category_id').value+"_"+document.getElementById('req_id').value+"_"+document.getElementById('cbo_supplier_id').value+"_"+document.getElementById('hidden_requsition').value;
	var list_view_orders = return_global_ajax_value( data, 'validate_supplier_load_php_dtls_form', '', 'requires/quotation_evaluation_controller');

	if(list_view_orders==1)
	{
		alert("This supplier is exist for same item of this requisition.");
		$("#cbo_supplier_id").focus();
	}
}

function update_factor_data( id )
{
	var text=return_global_ajax_value( id, "show_factor_list", '', 'requires/quotation_evaluation_controller');
	$("#evaluation_tbl tbody").html('');
	$("#evaluation_tbl tbody").html(text);
 
}
function clear_table()
{
 
	var numRow = $('#evaluation_tbl tbody tr').length;
	for(var i=numRow; i>1; i--)
	{
		fn_deletebreak_down_tr(i,"evaluation_tbl" );
	}
}
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center"> 
    <div style="width:850px;" align="center">
    <? echo load_freeze_divs ("../",$permission);  ?>
    </div>
        <fieldset style="width:980px;">
        <legend>Quotation Evaluation Entry</legend>
        <form name="quotationevaluation_1" id="quotationevaluation_1" autocomplete="off">
        <table width="950" cellspacing="2" cellpadding="0" border="1"  id="tbl_quotation_evalu" rules="all">
            <tr>
                <td align="center" colspan="2" width="520">
                    <fieldset style="width:520px;">
                        <table width="520" cellspacing="2" cellpadding="0" border="0" >
                            <tr>
                                <td align="right" colspan="2"><strong>System ID</strong></td>
                                <td align="left" colspan="2">
                                	
                                    <input type="hidden" id="hidden_quotation" />
                                    <input type="text" name="txt_system_id" id="txt_system_id" title="Double Click to Search" style="width:140px;"  class="text_boxes" placeholder="Double Click To Search" onDblClick="open_qepopup()" readonly />
                                </td>
                                <td></td> 
                            </tr>
                            <tr>
                                <td colspan="4">&nbsp; </td>
                            </tr>
                            <tr>
                                <td width="110" class="must_entry_caption">Company </td>
                                <td width="150">
									<? 
										echo create_drop_down( "cbo_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/quotation_evaluation_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/quotation_evaluation_controller', this.value, 'load_drop_down_supplier', 'supplier_td');","","","","","",2);
											
                                    ?> 
                                </td>
                                <td width="110" class="must_entry_caption">Item Category </td>
                                <td width="150">
									<? 
										echo create_drop_down( "cbo_item_category_id", 150, $item_category, 0, 1, "-- Select Category --", $selected, "", "", "", "", "", "1,2,3,4,12,13,14" );  
                                    ?>	 
                                </td>
                            </tr> 
                            <tr>
                                <td class="must_entry_caption">Location </td>
                                <td id="location_td">
									<? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                                </td>
                                <td class="must_entry_caption">Requisition No</td>
                                <td>
                                    <input name="txt_requisition_no"  id="txt_requisition_no" placeholder="Double Click to Search" onDblClick="openmypage_requisition(); return false" style="width:140px "  class="text_boxes" readonly/>
                                    <input type="hidden" id="req_id" />
                                   <!-- <input type="hidden" name="requisition_no_id" id="requisition_no_id"/>-->
                                </td>
                            </tr>
                            <tr>
                                <td class="must_entry_caption">Evaluation Date </td>
                                <td>
                                    <input type="text" name="txt_requisition_date" id="txt_requisition_date" value="<? echo date("d-m-Y")?>"  class="datepicker" style="width:140px" />  
                                </td>
                               
                            </tr>
                            <tr>
                                <td>Comments</td>
                                <td colspan="3">
                                    <input style="width:406px;" class="text_boxes" type="text" name="txt_comment" id="txt_comment" placeholder="Comments" />   
                                </td>
                            </tr>
                        </table>
					</fieldset>
				</td>
				<td  rowspan="6" align="center" valign="top">
					<div id="requisition_container_dtls" style="max-height:250px; width:450px; overflow:auto;" ></div>
				</td>
			</tr>
            <tr>
                <td align="center" colspan="2" width="520">
                <br>
                    <fieldset style="width:500px;" id="quotationevaluation_3">
                        <table width="520" cellspacing="2" cellpadding="0" border="0">
                            <tr>
                                <td width="110" class="must_entry_caption">Supplier </td>
                                <input type="hidden" id="update_id_dtls_id">
                                <td id="supplier_td" width="160">
                                
									<? 
										echo create_drop_down( "cbo_supplier_id", 150, $blank_array,"", 1, "-- Select supplier --", $selected, "",0,"","","","",10);
                                    ?>	 
                                </td>
                                <td width="110" class="must_entry_caption">Quotation Ref.</td>
                                <td>
                                    <input style="width:120px;" type="text" name="txt_quotaion_ref" id="txt_quotaion_ref"  class="text_boxes"  />
                                </td>
                            </tr>
                            <tr>
                                <td width="110" class="must_entry_caption">Requisition Item </td>
                                <td colspan="3">
                                    <input type="hidden" id="hidden_requsition" name="hidden_requsition" />
                                    <input style="width:395px;" class="text_boxes" type="text" name="txt_requisition_item" id="txt_requisition_item" readonly />                
                                </td>
                            </tr>
                           
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="2" width="520">
                <br>
                    <fieldset style="width:500px;">
                        <table width="520" cellspacing="2" cellpadding="0" border="1" class="rpt_table" rules="all">
                            <tr>
                                <td colspan="4" width="520">
                                    <table align="left" cellspacing="0" cellpadding="0" id="evaluation_tbl"  border="1" class="rpt_table" rules="all">
                                        <thead>
                                            <th width="220">Evaluation Factor</th>
                                            <th width="220">Value</th>
                                            <th width="70">&nbsp;</th>
                                        </thead>
                                        <tbody id="eval_fac_body">
                                            <tr id="evaluationfactor_1">
                                                <td width="220px">
													<? 
														
														echo create_drop_down( "cbo_1", 220, $quot_evaluation_factor, 1, 1, "-- Select Factor --", $selected, "", "", "", "", "", "" );
                                                    ?>	 
                                                </td>
                                                <td width="220">
                                                    <input style="width:210px;" type="text" name="txtevaluationfactor_1" id="txtevaluationfactor_1"  class="text_boxes" />
                                                    
                                                    <input type="hidden" id="updateiddtls_1" />
                                                </td>
                                                <td width="70" align="left">
                                                  &nbsp;
                                                    <input style="width:25px;" type="button" id="incrementfactor_1"  class="formbutton" value="+" onClick="add_factor_row(1)"/>
                                                    <input style="width:25px;" type="button" id="decrementfactor_1"  class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1,'evaluation_tbl')"/>
                                                    
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                            	</td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="5" class="button_container">
                    <?
					
						echo load_submit_buttons($permission,"fnc_quotation_evaluation",0,1,"reset_form('quotationevaluation_1','list_container*requisition_container_dtls','','','clear_table()')",1);
	
                    ?>
                    
                </td>
            </tr>
        </table>
        <div style="width:850px;" id="list_container">
        
			
        </div>
    </form>
    </fieldset>
    </div>   
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html> 