<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create AOP Batch Creation
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	12.03.2019
Updated by 		: 		
Update date		: 
Oracle Convert 	:	
Convert date	: 	
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
echo load_html_head_contents("AOP Batch Creation Info", "../../", 1, 1,$unicode,1,1); 
//echo load_html_head_contents("Line Wise Productivity Analysis","../../", 1, 1, $unicode,1,1); 

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	//var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];

	/*$(document).ready(function(e)
	 {
            $("#txt_batch_color").autocomplete({
			 source: str_color
		  });
        });*/

	function add_break_down_tr( i )
	{ 
		if (form_validation('cbo_company_id','Company Name')==false)
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
                        var k= i-1;
	
			$("#tbl_item_details tbody tr:last").clone().find("input,select").each(function(){
				  
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			//  'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return value }              
			});
			 
			}).end().appendTo("#tbl_item_details");
                        
				$('#cbofabricfrom_'+i).val($('#cbofabricfrom_'+k).val());
                                
			$("#tbl_item_details tbody tr:last").removeAttr('id').attr('id','tr_'+i);
			//$('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id','slTd_'+i);
			//$('#tr_' + i).find("td:eq(1)").removeAttr('id').attr('id','poNoTd_'+i);
			$('#tr_' + i).find("td:eq(2)").removeAttr('id').attr('id','itemDescTd_'+i);
			$('#tr_' + i).find("td:eq(3)").removeAttr('id').attr('id','gsmTd_'+i);
			$('#tr_' + i).find("td:eq(4)").removeAttr('id').attr('id','diaTd_'+i);
			$('#tr_' + i).find("td:eq(5)").removeAttr('id').attr('id','finDiaTd_'+i);
			$('#tr_' + i).find("td:eq(6)").removeAttr('id').attr('id','DiaWidthType_'+i);
			
		
			
			$('#updateIdDtls_'+i).removeAttr("value").attr("value","");
			$('#processId_'+i).removeAttr("value").attr("value","");
			$('#txtRollNo_'+i).removeAttr("value").attr("value","");
			$('#txtItemDesc_'+i).removeAttr("value").attr("value","");
          $('#txtItemDescid_'+i).removeAttr("value").attr("value","");
			//$('#txtGsm_'+i).removeAttr("value").attr("value","");
			//$('#txtDia_'+i).removeAttr("value").attr("value","");
			$('#txtBatchQnty_'+i).removeAttr("value").attr("value","");
			//$('#txtrecChallan_'+i).removeAttr("value").attr("value","");
			$('#processId_'+i).removeAttr("value").attr("value","");

			$('#poId_'+i).removeAttr("onChange").attr("onChange","hidden_data_load("+i+")");
			$('#txtPoNo_'+i).removeAttr("value").attr("value","");
			$('#txtJobParty_'+i).removeAttr("value").attr("value","");

			$('#txtItemDesc_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_itemdes("+i+");");
			//$('#cboDiaWidthType_'+i).val(0); document.getElementById('cboItemDesc_"+i"').value+'_'+"+i")
			//$('#cboDiaWidthType_'+i).val(0);
			
/*					$('#txtRollNo_'+i).removeAttr('placeholder','placeholder');
				$('#txtRollNo_'+i).removeAttr('onDblClick','onDblClick');
				$('#txtRollNo_'+i).removeAttr('readonly','readonly');
*/			$('#txtBalance_'+i).removeAttr("value").attr("value","");
			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
		}
		calculate_batch_qnty();
	}
	
	function fn_deleteRow(rowNo) 
	{ 
		var numRow = $('#tbl_item_details tbody tr').length; 
		if( numRow==1)
			{
				return false;
			}
			
		//if(numRow==rowNo && rowNo!=1)
		if(rowNo!=0)
		{
			var updateIdDtls=$('#updateIdDtls_'+rowNo).val();
			var txt_deleted_id=$('#txt_deleted_id').val();
			var selected_id='';
		
			if(updateIdDtls!='')
			{
				if(txt_deleted_id=='') selected_id=updateIdDtls; else selected_id=txt_deleted_id+','+updateIdDtls;
				$('#txt_deleted_id').val( selected_id );
			}
			
			//$('#tbl_item_details tbody tr:last').remove();
			$('#tr_'+rowNo).remove();
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
		var ddd={ dec_type:2, comma:0}
		math_operation( "txt_total_batch_qnty", "txtBatchQnty_", "+",numRow,ddd );
		math_operation( "txt_batch_weight", "txtBatchQnty_", "+",numRow,ddd );
	}
	
	function hidden_data_load(row_no)
	{
		var cbo_company_id=$('#cbo_company_id').val();
		var job_no=$('#hidden_job_number_for_po_popup').val();
		var po_id=$('#poId_'+row_no).val();
		var data=row_no+"_"+cbo_company_id+"_"+job_no+"_"+po_id;
		/*alert(data);
		return;*/
		get_php_form_data( data, 'po_wise_data_load','requires/aop_batch_creation_controller' );
	}


	function openmypage_itemdes(row_no)
	{
		var cbo_company_id=$('#cbo_company_id').val();
		//var fabricfrom=$('#cbofabricfrom_'+row_no).val();
		var po_id=$('#poId_'+row_no).val();
		//var no_of_row=$('#tbl_item_details tbody tr').length;
		//alert (row_no);
		if(form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		var title = 'Item Selection Form';	
		var page_link = 'requires/aop_batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&po_id='+po_id+'&action=itemdes_popup';
 		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=1,scrolling=0','');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var prod_id=this.contentDoc.getElementById("prod_id").value;
			var challan=this.contentDoc.getElementById("challan").value; 
			var description=this.contentDoc.getElementById("description").value;
			var gsm=this.contentDoc.getElementById("gsm").value;
			var grey_dia=this.contentDoc.getElementById("grey_dia").value;
			var fin_dia=this.contentDoc.getElementById("fin_dia").value;
            var balance=this.contentDoc.getElementById("balance").value;
			//alert (item_id); 
			$('#txtItemDescid_'+row_no).val(prod_id);
			$('#txtrecChallan_'+row_no).val(challan);
			$('#txtItemDesc_'+row_no).val(description);
			$('#txtGsm_'+row_no).val(gsm);
			$('#txtDia_'+row_no).val(grey_dia);
			$('#txtFinDia_'+row_no).val(fin_dia);
            $('#txtBalance_'+row_no).val(balance);
		}
	}	
	
	function fnc_batch_creation(operation)
	{
		if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+report_title+'*'+$('#hide_within_group').val(), "batch_card_print", "requires/aop_batch_creation_controller" ) ;
			 return;
		}
		if(operation==5)
		{
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+report_title+'*'+$('#hide_within_group').val(), "batch_card_print2", "requires/aop_batch_creation_controller" ) ;
			 return;
		}
		if(operation==6)
		{
			var show_item='';
			var r=confirm("Press  \"Ok\" To Show Organic Cotton \nPress  \"Cancel\"  to Not Show Organic Cotton");
			if (r==true)
			{
				show_item="1";
			}
			else
			{
				show_item="0";
			}
			 var report_title=$( "div.form_caption" ).html();
			 print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+report_title+'*'+$('#hide_within_group').val()+'*'+show_item, "batch_card_print3", "requires/aop_batch_creation_controller" ) ;
			 return;
		}
		
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		/*if($('#txt_batch_weight').val()*1 < 0.1)
		{
			alert('Please Insert Batch Weight.');
			$('#txt_batch_weight').focus();
			return;
		}
		if( $("#ext_from").val() != 0)
		{
			alert("This Batch No is already extended. Update is not allowed.");
			return;
		}
		
		if($('#cbo_batch_against').val()==2 && $('#txt_ext_no').val()=="")
		{
			alert("Please Insert Extention No.");
			$('#txt_ext_no').focus();
			return;
		}

		if($('#txt_batch_weight').val()*1!=($('#txt_total_batch_qnty').val()*1+$('#txt_tot_trims_weight').val()*1))
		{
			alert('Batch Weight and Total Batch Qnty+Trims Weight should be same.');
			return;
		}*/
		
		if($('#batch_no_creation').val()!=1)
		{
			if( form_validation('txt_batch_number','Batch Number')==false )
			{
				alert("Plesae Insert Batch No.");
				$('#txt_batch_number').focus();
				return;
			}
		}

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][281]);?>')
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][281]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][281]);?>')==false)
			{
				return;
			}
		}
		
		if( form_validation('cbo_company_id*txt_batch_date*txt_batch_weight*txt_batch_color','Company*Batch Date*Batch Weight*Batch Color')==false )
		{
			return;
		}
		
		var txt_deleted_id=$('#txt_deleted_id').val();
		var row_num=$('#tbl_item_details tbody tr').length;
		var data_all="";
		//alert (row_num); 

		/*for (var i=1; i<=row_num; i++)
		{
			alert(i);
			if (form_validation('txtPoNo_'+i+'*txtItemDesc_'+i+'*cboDiaWidthType_'+i+'*txtBatchQnty_'+i,'Po Number*Item Description*Dia/ W. Type*Batch Qty')==false)
			{
				return;
			}
			data_all+=get_submitted_data_string('cbofabricfrom_'+i+'*txtPoNo_'+i+'*poId_'+i+'*txtItemDesc_'+i+'*txtItemDescid_'+i+'*txtFinDia_'+i+'*txtGsm_'+i+'*txtDia_'+i+'*txtRollNo_'+i+'*txtBatchQnty_'+i+'*updateIdDtls_'+i+'*cboDiaWidthType_'+i+'*txtrecChallan_'+i,"../../",i);
		}*/
		var j=0; var breakOut = true; var error=0; error_barcode='';
		//$("#tbl_item_details tbody").find('tr').each(function()
		$("#tbl_item_details").find('tbody tr').each(function()
		{
			if(breakOut==false || error==1)
			{
				return;
			}
			var trId = $(this).attr('id').split('_');
			//alert(trId[1]);
			var i=trId[1];
			j++;
			
			var txtPoNo=$(this).find('input[name="txtPoNo[]"]').val();
			//var cbofabricfrom=$(this).find($('select[name="cbofabricfrom[]"] option:selected')).val();
			var poId=$(this).find($('select[name="poId[]"] option:selected')).val();
			var cboDiaWidthType=$(this).find($('select[name="cboDiaWidthType[]"] option:selected')).val();
		
			var txtBuyerPoNo=$(this).find('input[name="txtBuyerPoNo[]"]').val();
			var txtBuyerPoId=$(this).find('input[name="txtBuyerPoId[]"]').val();

			var txtItemDesc=$(this).find('input[name="txtItemDesc[]"]').val();
			var txtItemDescid=$(this).find('input[name="txtItemDescid[]"]').val();
			var cboBodyPart=$(this).find('select[name="cboBodyPart[]"]').val();
			var txtRollNo=$(this).find('input[name="txtRollNo[]"]').val();
			var txtBatchQnty=$(this).find('input[name="txtBatchQnty[]"]').val();
			var updateIdDtls=$(this).find('input[name="updateIdDtls[]"]').val();
			var txtrecChallan=$(this).find('input[name="txtrecChallan[]"]').val();
	
		if (form_validation('poId_'+i+'*txtBuyerPoId_'+i+'*txtItemDesc_'+i+'*cboDiaWidthType_'+i+'*txtBatchQnty_'+i,'WO number*Buyer PO*Item Description*Dia/ W. Type*Batch Qnty')==false)
			{
				breakOut = false;
				return false;
			}
			data_all += "&txtPoNo_" + j + "='" + txtPoNo + "'&poId_" + j + "='" + poId + "'&txtBuyerPoId_" + j + "='" + txtBuyerPoId + "'&txtItemDesc_" + j + "='" + txtItemDesc + "'&txtItemDescid_" + j + "='" + txtItemDescid + "'&cboBodyPart_" + j + "='" + cboBodyPart + "'&txtRollNo_" + j + "='" + txtRollNo + "'&txtBatchQnty_" + j + "='" + txtBatchQnty + "'&txtrecChallan_" + j + "='" + txtrecChallan + "'&cboDiaWidthType_" + j + "='" + cboDiaWidthType + "'&updateIdDtls_" + j + "='" + updateIdDtls + "'";
			
			//data_all+='&txtPoNo_' + j + '=' + txtPoNo + '&poId_' + j + '=' + poId + '&txtItemDesc_' + j + '=' + txtItemDesc + '&cboBodyPart_' + j + '=' + cboBodyPart + '&txtItemDescid_' + j + '=' + txtItemDescid + '&txtRollNo_' + j + '=' + txtRollNo + '&txtBatchQnty_' + j + '=' + txtBatchQnty+ '&updateIdDtls_' + j + '=' + updateIdDtls+ '&txtrecChallan_' + j + '=' + txtrecChallan+ '&cboDiaWidthType_' + j + '=' + cboDiaWidthType;
		
		
		});
		if(breakOut==false)
		{
			return;
		}
		
		//alert(data_all);
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_batch_sl_no*cbo_company_id*cbo_batch_against*batch_no_creation*txt_batch_number*txt_machine_no*txt_batch_date*txt_batch_weight*txt_tot_trims_weight*txt_ext_no*txt_batch_color*cbo_color_range*txt_process_id*txt_du_req_hr*txt_du_req_min*update_id*hide_update_id*txt_remarks*cbo_location_name*unloaded_batch*ext_from*txt_aop_type*cbo_floor_name*txt_po_id*txt_style_ref*hide_party_id*hide_within_group*cbo_batch_for*txt_print_type*txt_design_number*txt_coverage',"../../")+data_all+'&total_row='+row_num+'&txt_deleted_id='+txt_deleted_id;
		//alert (data);return;
		freeze_window(operation);
		
		http.open("POST","requires/aop_batch_creation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_batch_creation_Reply_info;
	}
	
	function fnc_batch_creation_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing(); 
			//alert(http.responseText);//return;
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
				document.getElementById('txt_batch_number').value = reponse[3];
				
				var batch_against=$('#cbo_batch_against').val();
				
				if(batch_against==2)
				{
					document.getElementById('hide_update_id').value = reponse[1];
				}
				else
				{
					document.getElementById('hide_update_id').value = '';
				}
				//alert (reponse[1])
				var dyeing_batch_id=$('#dyeing_batch_id').val();
				show_list_view(batch_against+'**'+reponse[1]+'**'+dyeing_batch_id,'batch_details','batch_details_container','requires/aop_batch_creation_controller','');
				set_button_status(1, permission, 'fnc_batch_creation',1,1);
				const el = document.querySelector('#batch_card_print_btn');
				if (el.classList.contains("formbutton_disabled")) {
				    el.classList.remove("formbutton_disabled");

				}
			}
			release_freezing();	
		}
	}
	
	function openmypage_batchNo()
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 
			$('#dyeing_batch_id').val('');
			var cbo_company_id = $('#cbo_company_id').val();
			var batch_against = $('#cbo_batch_against').val();
			//var batch_against = $('#cbo_batch_against').val();
			
			var title = 'Batch No Selection Form';	
			var page_link = 'requires/aop_batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=480px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				//alert();
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;
				var aop_ref=this.contentDoc.getElementById("hidden_aop_ref").value;
				var ext_from=this.contentDoc.getElementById("hidden_aop_ref").value;
				var hidden_batch_no=this.contentDoc.getElementById("hidden_batch_no").value;
				var unloaded_batch=this.contentDoc.getElementById("hidden_unloaded_batch").value;
				//alert(hidden_batch_no);
				if(batch_id!="")
				{
					freeze_window(5);
					get_php_form_data(batch_against+'**'+batch_id+'**'+hidden_batch_no+'**'+ext_from+'**'+cbo_company_id+'**'+unloaded_batch, "populate_data_from_search_popup", "requires/aop_batch_creation_controller" );
					var dyeing_batch_id=$('#dyeing_batch_id').val();
				    show_list_view(batch_against+'**'+batch_id+'**'+dyeing_batch_id,'batch_details','batch_details_container','requires/aop_batch_creation_controller','');
					release_freezing();
					$('#txt_deleted_id').val('');
					$('#cbo_company_id').attr('disabled','disabled');
					const el = document.querySelector('#batch_card_print_btn');
					if (el.classList.contains("formbutton_disabled")) {
					    el.classList.remove("formbutton_disabled");

					}
					const elb = document.querySelector('#batch_card_print_btn_2');
					if (elb.classList.contains("formbutton_disabled")) {
					    elb.classList.remove("formbutton_disabled");
					}
					$('#txt_aop_ref').val(aop_ref);
					calculate_batch_qnty();
				} 
			}
		}
	}
	
	function openmypage_process()
	{
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';	
		var page_link = 'requires/aop_batch_creation_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup';
		  
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
	
	function active_inactive()
	{
		reset_form('','','txt_ext_no*cbo_color_range*txt_process_name*txt_process_id*txt_du_req_hr*txt_du_req_min*txt_batch_color*txtPoNo_1*poId_1*txtItemDesc_1*txtItemDescid_1*txtRollNo_1*txtBatchQnty_1*txtrecChallan_1*txt_total_batch_qnty*hide_job_no','','$(\'#tbl_item_details tbody tr:not(:first)\').remove();','');//
		//var batch_against= $('#cbo_batch_against').val();
		
		$('#txtRollNo_1').val('');
		var batch_against=$('#cbo_batch_against').val();
		var dyeing_batch_id=$('#dyeing_batch_id').val();
		
		if(batch_against==1 )
		{
			$('#txt_ext_no').attr('disabled','disabled');
			$('#txt_batch_color').removeAttr('disabled','disabled');
			$('#cbo_color_range').removeAttr('disabled','disabled');
			$('#txt_process_name').removeAttr('disabled','disabled');
			$('#txt_batch_number').removeAttr('readOnly','readOnly');
			$('#txtBatchQnty_1').removeAttr('disabled','disabled');
			$('#txtrecChallan_1').removeAttr('disabled','disabled');
			
		 	$('#txtRollNo_1').removeAttr('disabled','disabled');
			if(dyeing_batch_id=='')
			{
				$('#cbo_company_id').removeAttr('disabled','disabled');
				$('#txt_batch_number').removeAttr('readOnly','readOnly');
				$('#txt_batch_color').removeAttr('disabled','disabled');
			}
			else
			{
				$('#cbo_company_id').attr('disabled','disabled');
				$('#txt_ext_no').attr('disabled','disabled');
				$('#txt_batch_number').attr('readOnly','readOnly');
				$('#txt_batch_color').attr('disabled','disabled');
			}
		}
		else if(batch_against==2 )
		{
			//$('#txt_ext_no').removeAttr('disabled','disabled');
			$('#txt_batch_number').val('');
			$('#txt_batch_number').attr('readOnly','readOnly');
			//$('#txtBatchQnty_1').attr('disabled','disabled');
			$('#txtBatchQnty_1').removeAttr('disabled','disabled');
			$('#txt_batch_color').attr('disabled','disabled');
			$('#update_id').val('');
			$('#hide_update_id').val('');
			$('#hide_batch_against').val('');
			$('#cbo_color_range').attr('disabled','disabled');
			$('#txt_process_name').attr('disabled','disabled');
			$('#txtPoNo_1').attr('disabled','disabled');
			$('#txtItemDesc_1').attr('disabled','disabled');
			$('#txtGsm_1').attr('disabled','disabled');
			$('#txtDia_1').attr('disabled','disabled');
			$('#txtRollNo_1').attr('disabled','disabled');
			$('#cboDiaWidthType_1').attr('disabled','disabled');
			$('#txtrecChallan_1').attr('disabled','disabled');
		}
	}
	
	function gsm_dia_load(id)
	{
		//alert(val)
		var item_des_full=get_dropdown_text('txtItemDesc_'+id);
		var item_des_part=item_des_full.split(',');
		$('#txtGsm_'+id).val(item_des_part[1]);
		$('#txtDia_'+id).val(item_des_part[2]);
		$('#txtFinDia_'+id).val(item_des_part[3]);
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
	
	/*function check_po_no(val)
	{ 
		var batch_no=$('#txt_batch_no').val();
		var cbo_company_id = $('#cbo_company_id').val();
		if(batch_no!="")
		{
			if (form_validation('cbo_company_id','Company')==false)
			{
				return;
			}
			
			var response=return_global_ajax_value( cbo_company_id+"**"+batch_no, 'check_batch_no', '', 'requires/aop_batch_creation_controller');
		
			var response=response.split("_");
			//alert(response[0]);return;
			if(response[0]==0)
			{
				alert('Po No not found.');
				
				$('#txt_batch_no').val('');
				$('#hidden_batch_id').val(''); 
				//$('#cbo_company_id').val(''); 
				$('#txt_update_id').val(''); 
				$('#cbo_sub_process').val('');
				$('#txt_process_end_date').val('');
				$('#txt_end_hours').val('');
				$('#txt_end_minutes').val('');
				$('#cbo_machine_name').val('');
				$('#txt_remarks').val('');
				reset_form('dyeingproduction_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
			}
			else
			{
				$('#hidden_batch_id').val(response[1]);
				get_php_form_data(document.getElementById('cbo_load_unload').value+'_'+response[1]+'_'+batch_no, "populate_data_from_batch", "requires/aop_batch_creation_controller" );
				show_list_view(response[1],'show_fabric_desc_listview','list_fabric_desc_container','requires/aop_batch_creation_controller','');
				get_php_form_data(document.getElementById('cbo_company_id').value+'**'+document.getElementById('cbo_floor').value+'**'+document.getElementById('cbo_machine_name').value, 'populate_data_from_machine', 'requires/aop_batch_creation_controller' );
			}
		}
	}*/
		
         function openmypage_batch_color()
         {
             if (form_validation('cbo_company_id','Company')==false)
            {
                    return;
            }
            var company_id = $('#cbo_company_id').val();
            var hidden_po_dtls_id = $('#hidden_po_dtls_id').val();
            var hidden_buyer_po_id = $('#hidden_buyer_po_id').val();
            var hidden_color_id = $('#hidden_color_id').val();
            var title = 'Batch Color Popup';	
            var page_link = 'requires/aop_batch_creation_controller.php?company_id='+company_id+'&hidden_po_dtls_id='+hidden_po_dtls_id+'&hidden_buyer_po_id='+hidden_buyer_po_id+'&hidden_color_id='+hidden_color_id+'&action=batch_color_popup';
		  
            //emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=760px,height=370px,center=1,resize=1,scrolling=0','');
            emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=370px, center=1, resize=0, scrolling=0','../')
            emailwindow.onclose=function()
			{
				//alert();
				var theform=this.contentDoc.forms[0]
				var buyer_po_id=this.contentDoc.getElementById("txt_selected_buyer_po_id").value;
				var po_dtls_id=this.contentDoc.getElementById("txt_selected_po_dtls_id").value;
				var color_id=this.contentDoc.getElementById("txt_selected_color_id").value;
				//var theemailqty=this.contentDoc.getElementById("total_order_qty").value;
				var print_type_names=this.contentDoc.getElementById("txt_print_type_names").value;
				var aop_type_names=this.contentDoc.getElementById("txt_aop_type_names").value;
				//$('#txt_order_qty').val(theemailqty);
				//alert(color_id+"=="+po_dtls_id+"=="+buyer_po_id);
				if (color_id!="" && po_dtls_id!="" && buyer_po_id!="")
				{
					
					//freeze_window(5);
					get_php_form_data( 1+'**'+buyer_po_id+'**'+po_dtls_id+'**'+color_id, "load_php_data_to_form", "requires/aop_batch_creation_controller" );
					//var within_group = $('#cbo_within_group').val();
					show_list_view(1+'**'+buyer_po_id+'**'+po_dtls_id+'**'+color_id,'order_dtls_list_view','batch_details_container','requires/aop_batch_creation_controller','setFilterGrid(\'list_view\',-1)');
					calculate_batch_qnty();	
					$('#hidden_po_dtls_id').val(po_dtls_id);
					$('#hidden_buyer_po_id').val(buyer_po_id);
					$('#hidden_color_id').val(color_id);
					$('#txt_print_type').val(print_type_names);
					$('#txt_aop_type').val(aop_type_names);
					set_button_status(0, permission, 'fnc_batch_creation',1);
					//release_freezing();
				}
			}
         }

         function color_set_value(color,job,process_id,process_name){
             $('#txt_batch_color').val(color);
			 $('#txt_process_id').val(process_id);
			 $('#txt_process_name').val(process_name);
			 
             $('#hidden_job_number_for_po_popup').val(job);
             var source = "";
             $("#batch_details_container tr").each(function(){
                 //alert($(this).find("input").attr("id"));
                source += $(this).find("select").attr("id"); 
                source += "*";
            
             });
                if (source.charAt(source.length - 1) == '*') {
                    source = source.substr(0, source.length - 1);
                }
              //alert(source);  
              source = source.trim();
             
             //reset_form('batchcreation_1','','','cbo_batch_against,1*txt_batch_date,".$date."','disable_enable_fields(\'cbo_color_range*txt_batch_color*txt_process_name*txtPoNo_1*poId_1*txtItemDesc_1*txtItemDescid_1*txtGsm_1*txtDia_1*cboDiaWidthType_1*txtRollNo_1*txtBatchQnty_1*txtrecChallan_1*hide_party_id*hide_job_no\',0)');$('#txt_ext_no').val('');$('#txt_ext_no').attr('disabled','disabled');$('#txt_batch_number').removeAttr('readOnly','readOnly');$('#tbl_item_details tbody tr:not(:first)').remove();
       		reset_form('batchcreation_1','','','','','txt_batch_color*cbo_batch_against*txt_batch_date*txt_process_id*txt_process_name*cbo_company_id*hidden_job_number_for_po_popup*'+source); 
        }
        function fnc_reset(){
            reset_form('batchcreation_1','list_color','','cbo_batch_against,1*txt_batch_date,<?echo date('d-m-Y') ?>','disable_enable_fields(\'cbo_color_range*txt_batch_color*txt_process_name*txtPoNo_1*poId_1*txtItemDesc_1*txtItemDescid_1*txtGsm_1*txtDia_1*cboDiaWidthType_1*txtRollNo_1*txtBatchQnty_1*txtrecChallan_1*hide_party_id*hide_within_group*hide_job_no\',0)',"");
            $('#txt_ext_no').val('');
            $('#txt_ext_no').attr('disabled','disabled');
            $('#txt_batch_number').removeAttr('readOnly','readOnly');
            $('#tbl_item_details tbody tr:not(:first)').remove();
        }
        function check_balance_qnty(id){
            id= id.trim()
            var strid = id.split("_");
            var row_num = strid[1];
            if($("#cbofabricfrom_"+row_num).val() == 3){
                if($("#txtBalance_"+row_num).val() != ""){
                    var balance = $("#txtBalance_"+row_num).val() - $("#txtBatchQnty_"+row_num).val();
                    if(balance < 0){
                        $("#txtBatchQnty_"+row_num).val("");
                        alert("Batch Qnty cannot exceeds Balance");
                    }
                }
            }
            calculate_batch_qnty();
        }
 </script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?>
    <div style="width:765px; float:left" align="center">
    <fieldset style="width:970px;">
    <legend>Batch Creation</legend> 
        <form name="batchcreation_1" id="batchcreation_1"> 
            <fieldset style="width:940px;">
                <table width="930" align="center" border="0">
                    <tr>
                        <td width="110" colspan="2" align="right"><b>Batch Serial No</b></td>
                        <td colspan="2"><input type="hidden" name="dyeing_batch_id" id="dyeing_batch_id" />
                            <input type="text" name="txt_batch_sl_no" id="txt_batch_sl_no" class="text_boxes" style="width:160px;" placeholder="Display" disabled />
                        </td>
                    </tr>
                    <tr>
                    	<td></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Company</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_id", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--Select Company--', 0,"get_php_form_data(this.value,'load_variable_settings','requires/aop_batch_creation_controller');load_drop_down( 'requires/aop_batch_creation_controller', this.value, 'load_drop_down_location', 'location_td' );",'','','','','',3);
                            ?>                              
                        </td>
                        <td class="must_entry_caption">Batch Color</td>
                        <td>
                            <input type="text" name="txt_batch_color" id="txt_batch_color" class="text_boxes" value="" style="width:160px;" tabindex="10" onDblClick="openmypage_batch_color()" placeholder="Double click" readonly/>
                            <input type="hidden" value="" id="hidden_batch_color_id">
                            <input type="hidden" value="" id="hidden_job_number_for_po_popup">
                            <input type="hidden" value="" id="hidden_main_process_id">
                            <input type="hidden" value="" id="hidden_po_dtls_id">
                            <input type="hidden" value="" id="hidden_buyer_po_id">
                            <input type="hidden" value="" id="hidden_color_id">
                        </td>
                        <td width="110" class="must_entry_caption">Batch Against</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_batch_against", 172, $batch_against,"", 1, '--- Select ---', 1, "active_inactive();",'','1,2','','','',1 );
                            ?>                              
                        </td>
                    </tr>
                    <tr>                        
                        <td class="must_entry_caption">Batch Date</td>
                        <td style="display: none;">
                            <?
                                echo create_drop_down( "cbo_color_range", 172, $color_range,"",1, "-- Select --", 0, "" );
                            ?>
                        </td>
                        <td>
                        	<input type="text" name="txt_batch_date" id="txt_batch_date" class="datepicker" style="width:160px;" tabindex="6" value="<? echo date("d-m-Y"); ?>" readonly />
                        </td>
                        <td class="must_entry_caption">Batch Number</td>
                        <td>
                            <input type="text" name="txt_batch_number" id="txt_batch_number" class="text_boxes" style="width:160px;" placeholder="Browse or Edit" onDblClick="openmypage_batchNo()" tabindex="4" />
                            <input type="hidden" name="variable_check" id="variable_check" style="width:60px"/>

                        </td>
                        <td width="110" class="must_entry_caption">Batch Weight </td>
                        <td>
                            <input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes" readonly style="width:160px;" tabindex="7" />
                        </td>
                    </tr>
                    <tr>
                        <td width="130" >AOP Type</td>
                        <td>
                            <input type="text" name="txt_aop_type" id="txt_aop_type" class="text_boxes" style="width:160px;" />
                        </td>
                        <td  class="must_entry_caption">Process Name</td>
                        <td>
                        	<? echo create_drop_down( "txt_process_id", 172, $conversion_cost_head_array,"", 0, " Select Process", $selected, "" ,"",""); //35,133,148,150,171,207,84,156,209,93,220,221,230,231,232,233,234,235,236,237 ?>
                            <!-- <input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:160px;" placeholder="Double Click To Search" onDblClick="openmypage_process();" tabindex="12" readonly />
                            <input type="hidden" name="txt_process_id" id="txt_process_id" /> -->
                        </td>
                        <td>Extention No.</td>
                        <td>
                            <input type="text" name="txt_ext_no" id="txt_ext_no" class="text_boxes_numeric" style="width:160px;" disabled="disabled" tabindex="5" />
                        </td>
                    </tr>
                    <tr style="display: none;">                        
                        <td>Total Trims Weight</td>
                        <td>
                            <input type="text" name="txt_tot_trims_weight" id="txt_tot_trims_weight" class="text_boxes_numeric" style="width:160px;" tabindex="8"/>
                        </td>
                        <td>Location</td>
                        <td id="location_td">
                        	<? echo create_drop_down( "cbo_location_name", 172, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?>
                        </td>
                        <td>Floor</td>
                        <td  id="floor_td">
                        	<? echo create_drop_down( "cbo_floor_name", 172, $blank_array,"", 1, "-- Select Floor --", $selected, "" ); ?>
                        </td>
                    </tr>
                    <tr>
                    	<td>Machine No.</td>
                        <td id="machine_td">
                        	<? echo create_drop_down( "txt_machine_no", 172, $blank_array,"", 1, "-- Select Machine --", $selected, "" ); ?>
                        </td>
                        <td>Buyer Style Ref.</td>
                        <td><input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:160px;"  tabindex="14" readonly /></td>
                        <td width="110">Batch For</td>
                        <td>
                            <?
                            $aop_batch_for = array(1 =>"Bulk",2 =>"Sample");
                                echo create_drop_down( "cbo_batch_for", 172, $aop_batch_for,"",'','',1, "",'','1,2','','','',1 );
                            ?>                              
                        </td>
                    </tr>
                    <tr>                    	
                    	<td>AOP Ref.</td>
                    	<td ><input name="txt_aop_ref" id="txt_aop_ref" type="text" class="text_boxes" readonly style="width:160px"/></td>
                     	<td>Remarks</td>
                        <td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:160px;"  tabindex="15" /></td>
                        <td width="130" >Print Type</td>
                        <td>
                            <input type="text" name="txt_print_type" id="txt_print_type" class="text_boxes" style="width:160px;" />
                        </td>
					</tr>
					<tr>
						<td width="130" >Design Number</td>
                        <td>
                            <input type="text" name="txt_design_number" id="txt_design_number" class="text_boxes" style="width:160px;" />
                        </td>
                        <td width="130" >Coverage%</td>
                        <td>
                            <input type="text" name="txt_coverage" id="txt_coverage" class="text_boxes" style="width:160px;" />
                        </td>
						<td>&nbsp;</td>
                        <td>
                            <?
                            include("../../terms_condition/terms_condition.php");
                            terms_condition(281,'txt_batch_sl_no','../../','update_id');
                            ?>
                        </td>
					</tr>
					<tr>
                        <td style="display: none;">
                            <input type="text" name="txt_du_req_hr" id="txt_du_req_hr" class="text_boxes_numeric" placeholder="Hour" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_hr','txt_end_date',2,23)" style="width:70px;" />&nbsp;
                            <input type="text" name="txt_du_req_min" id="txt_du_req_min" class="text_boxes_numeric" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_min','txt_end_date',2,59)" placeholder="Minute" style="width:70px;" />
                        </td>
                        <td style="display: none">
                        	<input type="text" name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:160px;" readonly />
                        	<input type="hidden" name="txt_po_id" id="txt_po_id" />
                    	</td> 
                    </tr>
                 </table>
            </fieldset>                 
            <fieldset style="width:750px; margin-top:10px">
            <legend>Item Details</legend>
                <table cellpadding="0" cellspacing="0" width="735" class="rpt_table" border="1" rules="all" id="tbl_item_details">
                    <thead>
                        <th class="must_entry_caption">Work Order</th>
                        <th class="must_entry_caption">Buyer PO</th>
                        <th>Body Part</th>
                        <th class="must_entry_caption">Item Description</th>
                        <th class="must_entry_caption">Dia/ W. Type</th>
                        <th>Roll No.</th>
                        <th class="must_entry_caption">Batch Qty</th>
                    </thead>
                    <tbody id="batch_details_container">
                        <tr class="general" id="tr_1">
                            <td id="field_po_id">	
                            	<?
                            		echo create_drop_down( "poId_1", 150, $blank_array,"", 1, "-- Select WO --", $selected, "","","","", "", "", "", "","poId[]" );
                            	?>
                            </td>
                            <td>
	                            <input type="text" name="txtBuyerPoNo[]" id="txtBuyerPoNo_1" class="text_boxes" style="width:100px;" readonly />
	                            <input type="hidden" name="txtBuyerPoId" id="txtBuyerPoId_1" />
                        	</td> 
                            <td>
                            	<? 
                            		echo create_drop_down( "cboBodyPart_1", 130, $body_part,"", 1, "--Select--",0, "","","","", "", "", "", "","cboBodyPart[]" );
                            	?>
                            </td>                            
                            <td id="itemDescTd_1">
								<!-- <input type="text" name="txtItemDesc[]" id="txtItemDesc_1" class="text_boxes" style="width:150px" placeholder="Browse" onDblClick="openmypage_itemdes(1)" readonly /> -->
								<input type="text" name="txtItemDesc[]" id="txtItemDesc_1" class="text_boxes" style="width:150px" placeholder="Display" readonly />
                            	<input type="hidden" name="txtPoNo[]"  id="txtPoNo_1" class="text_boxes" style="width:70px" readonly/>
                                <input type="hidden" name="processId[]" id="processId_1" style="width:50px" class="text_boxes" readonly />
                                <input type="hidden" name="txtItemDescid[]" id="txtItemDescid_1" class="text_boxes" style="width:60px" />
                            </td>
                            <td id="DiaWidthType_1">
                                <?
									echo create_drop_down( "cboDiaWidthType_1",80, $fabric_typee,"", 1, "-- Select  --", 0, "","","","", "", "", "", "","cboDiaWidthType[]" );
                                ?>
                            </td>                              
                            <td>
                                <input type="text" name="txtRollNo[]" id="txtRollNo_1" class="text_boxes_numeric" style="width:50px" />
                                <input type="hidden" name="hideRollNo[]" id="hideRollNo_1" class="text_boxes" readonly />
                                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" class="text_boxes" readonly />
                            </td>
                            <td>
                                <input type="text" name="txtBatchQnty[]"  id="txtBatchQnty_1" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" onChange="check_balance_qnty(this.id)" style="width:60px" />
                                <input type="hidden" name="txtBalance[]" id="txtBalance_1" class="text_boxes">
                            </td>
                            <td style="display: none;">
                                <input type="text" name="txtrecChallan[]"  id="txtrecChallan_1" class="text_boxes" style="width:60px" readonly />
                           
                                <input type="text" name="txtJobParty[]"  id="txtJobParty_1" class="text_boxes" style="width:50px" readonly />
                                <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(1)" />
                                <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                            </td>
                            <td id="gsmTd_1" style="display: none;">
                                <input type="text" name="txtGsm[]" id="txtGsm_1" class="text_boxes_numeric" style="width:50px" readonly />
                            </td>
                            <td id="diaTd_1" style="display: none;">
                                <input type="text" name="txtDia[]" id="txtDia_1" class="text_boxes_numeric" style="width:50px" readonly />
                            </td>
                            <td id="finDiaTd_1" style="display: none;">
                                <input type="text" name="txtFinDia[]" id="txtFinDia_1" class="text_boxes_numeric" style="width:50px" readonly />
                            </td>
                            <td id="dyenamic_fabricfrom" style="display: none;">
                                <?  
								echo create_drop_down("cbofabricfrom_1", 70, $blank_array, "", 1, "--Select --", 0, "", 1, "", "", "", "", "", "", "cbofabricfrom[]");                            
                                ?>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="tbl_bottom">
                    	<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>Sum</td>
                        <td><input type="text" name="txt_total_batch_qnty" id="txt_total_batch_qnty" class="text_boxes_numeric" style="width:60px" readonly />
                        	<input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" style="width:60px" readonly /></td>
                    </tfoot>
                </table>
            </fieldset> 
            <table width="730">
                <tr>
                    <td colspan="4" align="center" class="button_container">
                        <? 
                            $date=date('d-m-Y');
                            echo load_submit_buttons($permission, "fnc_batch_creation",0,1,"fnc_reset()",1);
                        ?> 
                        <input type="button" value="Batch Card" name="batch_card_print_btn" onClick="fnc_batch_creation(5)" style="width:80px" id="batch_card_print_btn" class="formbutton formbutton_disabled">
                        <input type="button" value="Batch Card 2" name="batch_card_print_btn_2" onClick="fnc_batch_creation(6)" style="width:80px" id="batch_card_print_btn_2" class="formbutton formbutton_disabled">
                        <input type="hidden" name="update_id" id="update_id"/>
                        <input type="hidden" name="hide_update_id" id="hide_update_id"/>
                        <input type="hidden" name="hide_batch_against" id="hide_batch_against"/>
                        <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                        <input type="hidden" name="batch_no_creation" id="batch_no_creation" readonly>
                        <input type="hidden" name="hide_job_no" id="hide_job_no" readonly><!--For Duplication Check-->
                        <input type="hidden" name="hide_party_id" id="hide_party_id" readonly>
                        <input type="hidden" name="hide_within_group" id="hide_within_group" readonly>
						<input type="hidden" name="ext_from" id="ext_from" readonly>
						<input type="hidden" name="unloaded_batch" id="unloaded_batch" readonly>
                    </td>	  
                </tr>
            </table>
        </form>
    </fieldset>
    </div>
    <div id="list_color" style="width:350px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</body>
<script>
	set_multiselect('txt_process_id','0','0','','0');
</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>