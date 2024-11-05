<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create batch creation For Gmts. Wash 
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahman 
Creation date 	: 	02.06.2022
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
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Batch Creation For Gmts. Wash", "../../", 1, 1,'','','');
echo load_html_head_contents("Batch Creation For Gmts. Wash","../../", 1, 1, $unicode,1,'');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	var garments_item_array=[];
	<?
		$jsgarments_item= json_encode($garments_item);
		echo "garments_item_array = ". $jsgarments_item . ";\n";
	?>


	var str_supervisor = [<? echo substr(return_library_autocomplete( "select supervisor_name from pro_batch_create_mst group by supervisor_name", "supervisor_name" ), 0, -1); ?>];
	$(document).ready(function(e)
	{
		$("#txt_supervisor").autocomplete({
			source: str_supervisor
		});
	});

	var str_operator = [<? echo substr(return_library_autocomplete( "select operator_name from pro_batch_create_mst group by operator_name", "operator_name" ), 0, -1); ?>];
	$(document).ready(function(e)
	{
		$("#txt_operator").autocomplete({
			source: str_operator
		});
	});
	
	function add_break_down_tr( i )
	{ 
		
		//return; // business wise not solutions this reason temporary return  use 
		if (form_validation('cbo_company_id*txt_batch_color*txtPoNo_'+i,'Company Name*Batch Color*PO No')==false)
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
			$('#txtbuyerPo_'+i).removeAttr("value").attr("value","");
			$('#txtbuyerstyle_'+i).removeAttr("value").attr("value","");
			$('#txtGmtsQty_'+i).removeAttr("value").attr("value","");
			$('#txtBatchQnty_'+i).removeAttr("value").attr("value","");
			$('#chkgmts_'+i).removeAttr("value").attr("value","");
			$('#txtPoNo_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_po("+i+");");
			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");

			$('#cboItem_'+i).removeAttr("onchange").attr("onchange","load_color_list("+i+");"); 
		}
		set_all_onclick();
		calculate_batch_qnty();
	}
	
	function fn_deleteRow(rowNo) 
	{ 
	
		//return; // business wise not solutions this reason temporary return  use 
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

	function load_color_list(rowNo)
	{
		var poId=$('#poId_'+rowNo).val();
		var PoNo=$('#txtPoNo_'+rowNo).val();
		var cboItem=$('#cboItem_'+rowNo).val();
		var cbo_operation_type=$('#cbo_operation').val();
		var hiddenOperationType=$('#hidden_operation_id').val();
		var hiddenColorId=$('#hidden_color_id').val();
		var hiddenbatchno=$('#hidden_batch_no').val();
		var hiddenbatchid=$('#hidden_batch_id').val();
		var hiddenbatchdtlsid=$('#hidden_batch_dtls_id').val();
		var update_id=$('#update_id').val();
		var batch_against=$('#cbo_batch_against').val();
		//alert(poId+"**"+cboItem);
		
		if(batch_against!=11)
		{
			show_list_view(poId+"*"+cboItem+"*"+rowNo+"*"+hiddenOperationType+"*"+hiddenColorId+"*"+PoNo+"*"+hiddenbatchno+"*"+hiddenbatchid+"*"+hiddenbatchdtlsid+"*"+cbo_operation_type+"*"+update_id+"*"+batch_against,'show_color_listview','list_color','requires/wash_batch_creation_v2_controller','');
			change_delivery_issue_captions();
		}
	}
 
 	function list_wash_operation(rowNo)
	{
		 //alert(x);
      //x.querySelector(".example").innerHTML = "Hello World!";
	 // $("#regTitle").html('Hello World');
	    var txtPoNo=$('#txtPoNo_1').val();
		if(txtPoNo!="")
		{
 			//reset_form('batchcreation_1','','','','$(\'#tbl_item_details tbody tr:not(:first)\').remove();','');
 			reset_form('batchcreation_1','','','','$(\'#tbl_item_details tbody tr:not(:first)\').remove();','txt_batch_sl_no*cbo_batch_against*cbo_gmts_type*cbo_sub_operation*cbo_company_id*cbo_location_name*cbo_floor_name*batch_no_creation*txt_batch_number*txt_batch_date*txt_batch_weight*txt_ext_no*txt_batch_color*cbo_color_range*txt_organic*txt_process_id*txt_du_req_hr*txt_du_req_min*update_id*machine_id*txt_remarks*txt_dryer_no*txt_dryer_perator*txt_dryer_emp*txt_hydro_rpm*txt_mc_loading_time*txt_mc_un_loading_time*txt_mc_rpm*cbo_shift*txt_operator*txt_supervisor*buyer_id*cbo_within_group*cbo_operation*hidden_operation_id*hidden_color_id*hidden_batch_no*hidden_batch_id*hidden_batch_dtls_id*unloaded_batch*ext_from*hide_batch_against*hide_update_id');
 			}
		
		load_color_list(rowNo);
		var operation_type=$('#cbo_operation').val()*1;
		var hidden_operation_id=$('#hidden_operation_id').val()*1;
		var batch_against=$('#cbo_batch_against').val();
 		change_delivery_issue_captions();
		
 	}
	
	function change_delivery_issue_captions()
	{
	 
 		var batch_against=$('#cbo_batch_against').val();
		
		if(batch_against==12)
		{
		  document.getElementById("change_delivery_issue").innerHTML = "Delivery Return Qty";
		}
		else
		{
			document.getElementById("change_delivery_issue").innerHTML = "Meterial Issue Qty (Pcs)";;
			
		}
  	}
 
 
 
 
	function color_set_value(color,batch_blnc,rowNo,within_group,party_id,buyer_po_id,gmts_type,buyer_po_no)
	{
		if(($('#txt_batch_color').val() == "") || ($('#txt_batch_color').val() == color) || (rowNo==1))
		{
	        $('#cbo_gmts_type').val(gmts_type);
			$('#txt_batch_color').val(color);
	        $('#txtGmtsQty_'+rowNo).val(batch_blnc);
	        calculate_batch_qnty();
	    }
	    else
	    {
    		alert("Batch Color doesn't match");
    		return;
	    }

	    if($('#updateIdDtls_'+rowNo).val()=="")
	    {
	    	$('#chkgmts_'+rowNo).val("");
	    	$('#chkgmts_'+rowNo).val(batch_blnc);
	    } 
		//alert(within_group);
	    $('#cbo_within_group').val(within_group);   
	    $('#buyer_id').val(party_id);   
	    $('#buyerPoId_'+rowNo).val(buyer_po_id);   
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
		var update_id=$('#update_id').val();
		var tot_row=$('#tbl_item_details tbody tr').length;
		var color_name=$('#txt_batch_color').val();
	    var batch_against=$('#cbo_batch_against').val(); 
		var operation=$('#cbo_operation').val();
		
		if(form_validation('cbo_company_id*cbo_operation','Company*operation')==false)
		{
			return;
		}
		
		var title = 'PO Selection Form';	
		var page_link = 'requires/wash_batch_creation_v2_controller.php?cbo_company_id='+cbo_company_id+'&buyer_id='+buyer_id+'&color_name='+color_name+'&operation='+operation+'&tot_row='+tot_row+'&batch_against='+batch_against+'&update_id='+update_id+'&action=po_popup';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var po_id=this.contentDoc.getElementById("po_id").value; //Access form field with id="emailfield"
			var po_no=this.contentDoc.getElementById("po_no").value;
			var buyer_po_no=this.contentDoc.getElementById("buyer_po_no").value; //Access form field with id="emailfield"
			var gmts_item_id=this.contentDoc.getElementById("gmts_item_id").value.split(","); //Access form field with id="emailfield"
			var buyer_id=this.contentDoc.getElementById("buyer_id").value; //Access form field with id="emailfield"
			
			var operation_type_id=this.contentDoc.getElementById("operation_type_id").value; //Access form field with id="emailfield"
			var batch_color_id=this.contentDoc.getElementById("batch_color_id").value; //Access form field with id="emailfield"
			var batch_no=this.contentDoc.getElementById("batch_no").value;
			var batch_id=this.contentDoc.getElementById("batch_id").value;
			var batch_dtls_id=this.contentDoc.getElementById("batch_dtls_id").value;
			var buyer_style_ref=this.contentDoc.getElementById("buyer_style_ref").value;
			var rewash_qty=this.contentDoc.getElementById("rewash_qty").value;
			var batch_color=this.contentDoc.getElementById("batch_color").value;
			var gmts_type=this.contentDoc.getElementById("gmts_type").value;
			
			//alert(gmts_type);
			
			//alert(operation_type_id);
			
			if(operation_type_id==1)
			{
				//$('#cbo_operation').val(operation_type_id);
				$('#hidden_operation_id').val(operation_type_id);
				$('#hidden_color_id').val(batch_color_id);
				$('#hidden_batch_no').val(batch_no);
				$('#hidden_batch_id').val(batch_id);
				$('#hidden_batch_dtls_id').val(batch_dtls_id);
				//$("#cbo_operation").attr("disabled",false);
				//document.getElementById("cbo_operation").disabled = true; 
			}
			else if (operation_type_id==2)
			{
				//$('#cbo_operation').val(operation_type_id);
				$('#hidden_operation_id').val(operation_type_id);
				$('#hidden_color_id').val(batch_color_id);
				$('#hidden_batch_no').val(batch_no);
				$('#hidden_batch_id').val(batch_id);
				$('#hidden_batch_dtls_id').val(batch_dtls_id);
				//$("#cbo_operation").attr("disabled",true);
			}
			else if (operation_type_id==3)
			{
				//$('#cbo_operation').val(operation_type_id);
				$('#hidden_operation_id').val(operation_type_id);
				$('#hidden_color_id').val(batch_color_id);
				$('#hidden_batch_no').val(batch_no);
				$('#hidden_batch_id').val(batch_id);
				$('#hidden_batch_dtls_id').val(batch_dtls_id);
			}
			else if (operation_type_id==4)
			{
				//$('#cbo_operation').val(operation_type_id);
				$('#hidden_operation_id').val(operation_type_id);
				$('#hidden_color_id').val(batch_color_id);
				$('#hidden_batch_no').val(batch_no);
				$('#hidden_batch_id').val(batch_id);
				$('#hidden_batch_dtls_id').val(batch_dtls_id);
				//$("#cbo_operation").attr("disabled",true);
			}
			else
			{
				$('#hidden_operation_id').val(operation_type_id);
				$('#hidden_color_id').val(batch_color_id);
				$('#hidden_batch_no').val(batch_no);
				$('#hidden_batch_id').val(batch_id);
				$('#hidden_batch_dtls_id').val(batch_dtls_id);
			}
			
			
			$("#cbo_operation").attr("disabled",false);
			
			//alert(operation_type_id+'=='+batch_color_id);
			$('#cbo_gmts_type').val(gmts_type);
			$('#txt_batch_color').val(batch_color);
			$('#poId_'+row_no).val(po_id);
			$('#txtPoNo_'+row_no).val(po_no);
			$('#txtbuyerPo_'+row_no).val(buyer_po_no);
			$('#txtbuyerstyle_'+row_no).val(buyer_style_ref);
			$('#buyer_id').val(buyer_id);
			$('#txtGmtsQty_'+row_no).val(rewash_qty);
			$('#chkgmts_'+row_no).val(rewash_qty);
			//$("#cbo_operation").attr("disabled",true);
			$('#txtBatchQnty_'+row_no).val("");
			calculate_batch_qnty();
			
			$("#cboItem_"+ row_no +" option[value!='0']").remove();
			for(var i=0; i<gmts_item_id.length; i++)
			{	
				$('#cboItem_'+row_no).append("<option value='"+gmts_item_id[i]+"'>"+garments_item_array[gmts_item_id[i]]+"</option>");
			}
			
			var item_length=$("#cboItem_"+ row_no +" option").length;
			if(item_length==2)
			{
				$('#cboItem_'+ row_no).val($("#cboItem_"+ row_no +" option:last").val());
				load_color_list(row_no);
			}
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/wash_batch_creation_v2_controller.php?data=" + data+'&action='+action, true );
	}
	
	function fnc_batch_creation(operation)
	{
		if(operation==4)
		{
			
			// generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#cbo_within_group').val(),'batch_card_print','requires/wash_batch_creation_v2_controller');

			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+$('#cbo_within_group').val(), "batch_card_print", "requires/wash_batch_creation_v2_controller") 
			return;
		}
			
		/*if(operation==2)
		{
			show_msg('13');
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
		
		if($('#txt_batch_weight').val()*1 < 0.1)
		{
			alert('Please Insert Batch Weight.');
			$('#txt_batch_weight').focus();
			return;
		}
		var cbo_batch_against = $('#cbo_batch_against').val();

		/*if(cbo_batch_against==7)
		{*/
			if(form_validation('txt_batch_color','Batch Color')==false )
			{
				return;
			}
		//}
		
		if( form_validation('cbo_batch_against*cbo_company_id*cbo_location_name*txt_batch_date*txt_batch_weight*cbo_operation','Batch Against*Company*Location*Batch Date*Batch Weight*operation')==false )
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
			if(($('#txtGmtsQty_'+i).val()*1) > ($('#chkgmts_'+i).val()*1))
			{
				var amnt=$('#chkgmts_'+i).val();
				alert("Gmts Qty can be maximum "+amnt);
				$('#txtGmtsQty_'+i).val("");
				$('#txtGmtsQty_'+i).focus();
				return;
			}
		}
		

		/*for (var i=1; i<=row_num; i++)
		{
			if(($('#txtGmtsQty_'+i).val()*1) > ($('#chkgmts_'+i).val()*1))
			{
				var amnt=$('#chkgmts_'+i).val();
				alert("Gmts Qty can be maximum "+amnt);
				$('#txtGmtsQty_'+i).val("");
				$('#txtGmtsQty_'+i).focus();
				return;
			}
		}*/

		for (var i=1; i<=row_num; i++)
		{
			if (form_validation('txtPoNo_'+i+'*cboItem_'+i+'*txtGmtsQty_'+i+'*txtBatchQnty_'+i,'PO No.*Gmts. Item*Gmts Qnty*Batch Qnty')==false)
			{
				return;
			}
			data_all+=get_submitted_data_string('poId_'+i+'*cboItem_'+i+'*txtGmtsQty_'+i+'*txtBatchQnty_'+i+'*buyerPoId_'+i+'*updateIdDtls_'+i+'*txtbuyerPo_'+i+'*txtbuyerstyle_'+i,"../../",2);
		}
		
		//alert(data_all);return;
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_batch_sl_no*cbo_batch_against*cbo_gmts_type*cbo_sub_operation*cbo_company_id*cbo_location_name*cbo_floor_name*batch_no_creation*txt_batch_number*txt_batch_date*txt_batch_weight*txt_ext_no*txt_batch_color*cbo_color_range*txt_organic*txt_process_id*txt_du_req_hr*txt_du_req_min*update_id*machine_id*txt_remarks*txt_dryer_no*txt_dryer_perator*txt_dryer_emp*txt_hydro_rpm*txt_mc_loading_time*txt_mc_un_loading_time*txt_mc_rpm*cbo_shift*txt_operator*txt_supervisor*buyer_id*cbo_within_group*cbo_operation*hidden_operation_id*hidden_color_id*hidden_batch_no*hidden_batch_id*hidden_batch_dtls_id*unloaded_batch*ext_from*hide_batch_against*hide_update_id',"../../")+data_all+'&total_row='+row_num+'&txt_deleted_id='+txt_deleted_id;
		freeze_window(operation);
		//alert(data); return;
		http.open("POST","requires/wash_batch_creation_v2_controller.php",true);
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
			if(reponse[0]==trim('balExe')) 
			{ 
				 alert(reponse[1])
				 return;
			}
			else if(reponse[0]==50)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			else if(reponse[0]==16) 
			{ 
				 setTimeout('fnc_batch_creation('+ reponse[1] +')',8000); 
				 return;
			}
			else if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_batch_sl_no').value = reponse[2];
				var batch_against=$('#cbo_batch_against').val();
				var batch_for=0;//$('#cbo_batch_for').val();
				
				show_list_view(batch_against+'**'+batch_for+'**'+reponse[1],'batch_details','batch_details_container','requires/wash_batch_creation_v2_controller','');
				set_button_status(1, permission, 'fnc_batch_creation',1);
			}
			$("#cbo_operation").attr("disabled",true);
			load_color_list(1);
			release_freezing();	
		}
	}
	
	function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_against = $('#cbo_batch_against').val();
		var operation_type = $('#cbo_operation').val();
		var batch_for = 0;//$('#cbo_batch_for').val();
		
		if (form_validation('cbo_batch_against*cbo_company_id','Batch Against*Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Batch No Selection Form';	
			var page_link = 'requires/wash_batch_creation_v2_controller.php?cbo_company_id='+cbo_company_id+'&batch_against='+batch_against+'&action=batch_popup';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1065px,height=370px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;	 //Access form field with id="emailfield"
				//var operation_id=this.contentDoc.getElementById("hidden_operation_id").value;
				//var sub_operation_id=this.contentDoc.getElementById("hidden_sub_operation_id").value;
				//alert(sub_operation_id);
				//load_drop_down( 'requires/general_item_issue_controller',operation_id, 'load_drop_down_sub_operation', 'sub_operation' );
				//load_drop_down( 'requires/wash_batch_creation_v2_controller', this.value, 'load_drop_down_sub_operation', 'sub_operation');
				//set_multiselect('cbo_sub_operation','0','0','','0'); 
				//set_multiselect('cbo_sub_operation','0','1',sub_operation_id,'0');
				
				var po_id=this.contentDoc.getElementById("po_id").value;	
				var operation_type_id=this.contentDoc.getElementById("operation_type_id").value;	
				var batch_color_id=this.contentDoc.getElementById("batch_color_id").value;	
				var unloaded_batch=this.contentDoc.getElementById("hidden_unloaded_batch").value;
				var ext_from=this.contentDoc.getElementById("hidden_ext_from").value;
				var batch_no=this.contentDoc.getElementById("hidden_batch_no").value;
				
			//alert(po_id);return;
				if(batch_id!="")
				{
					freeze_window(5);
					get_php_form_data(batch_against+'**'+batch_for+'**'+batch_id+'**'+po_id+'**'+operation_type_id+'**'+batch_color_id+'**'+operation_type+'**'+unloaded_batch+'**'+ext_from+'**'+cbo_company_id+'**'+batch_no, "populate_data_from_search_popup", "requires/wash_batch_creation_v2_controller" );
				    show_list_view(batch_against+'**'+batch_for+'**'+batch_id+'**'+po_id+'**'+operation_type_id+'**'+batch_color_id+'**'+operation_type+'**'+cbo_company_id,'batch_details','batch_details_container','requires/wash_batch_creation_v2_controller','');
					release_freezing();
					$('#txt_deleted_id').val('');
					calculate_batch_qnty();
					load_color_list(1);
					$("#cbo_operation").attr("disabled",true);
				} 
			}
			
		}
	}

	function load_color_list_update(data)
	{
		//
		var batch_against=$('#cbo_batch_against').val();
		//alert(batch_against); return;
		var data=data.split("*");
		
		if(batch_against!=11)
		{
			show_list_view(data[0]+"*"+data[1]+"*"+"0*"+data[2]+"*"+batch_against,'show_color_listview','list_color','requires/wash_batch_creation_v2_controller','');
			change_delivery_issue_captions();
		}
	}
	
	function openmypage_process()
	{
		if(form_validation('cbo_batch_against','Batch Against')==false ){ return; }

		var cbo_batch_against = $('#cbo_batch_against').val();
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';	

		var page_link = 'requires/wash_batch_creation_v2_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup&cbo_batch_against='+cbo_batch_against;
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;
			$('#txt_process_id').val(process_id);
			//$('#txt_process_name').val(process_name);
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
		var cbo_batch_against = $('#cbo_batch_against').val();
		
		if (form_validation('cbo_company_id*cbo_batch_against','Company* Batch Against')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Machine No Selection Form';	
			var page_link = 'requires/wash_batch_creation_v2_controller.php?cbo_company_id='+cbo_company_id+'&action=machineNo_popup&cbo_batch_against='+cbo_batch_against;
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=755px,height=350px,center=1,resize=1,scrolling=0','../');
			
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
		if(str==7) $('#batch_color_td').css('color','blue'); else $('#batch_color_td').css('color','black');
	}
	
	
	
	
	
</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:70%; float:left;" align="center">
	<? echo load_freeze_divs ("../../",$permission); ?>
    <fieldset style="width:770px;">
    <legend>Wash Batch Creation</legend> 
        <form name="batchcreation_1" id="batchcreation_1"> 
            <fieldset style="width:740px;">
                <table width="730" align="center" border="0">
                    <tr>
                   		<td width="110" class="must_entry_caption">Batch Against</td>
								<td>
									<?
									
									//echo create_drop_down( "cbo_batch_against", 172, $batch_against,"",6, '--- Select ---',1, "active_inactive()",'','6,11','','','',1 );
									echo create_drop_down( "cbo_batch_against", 172, $batch_against,"",6, '--- Select ---',1, "",'','11','','','',1 );
									//echo create_drop_down( "cbo_batch_against", 172, $batch_against,"",0, '--- Select ---', 1, "validate_check(this.value)",'','6','','','',1 );
									?>
								</td>
                        <td width="110" colspan="1" align="right"><b>Batch Serial No</b></td>
                        <td colspan="1">
                            <input type="text" name="txt_batch_sl_no" id="txt_batch_sl_no" class="text_boxes" style="width:160px;" placeholder="Display" disabled />
                        </td>
                    </tr>
                    <tr>
                    <td width="110" class="must_entry_caption">Gmts Type</td>
                        <td><? 
							echo create_drop_down( "cbo_gmts_type", 170, $wash_gmts_type_array,"", 1, "-- Select Type --", $selected, "",1 ); ?></td>
                        <td width="130" class="must_entry_caption">Batch Date</td>
                        <td>
                            <input type="text" name="txt_batch_date" id="txt_batch_date" class="datepicker" style="width:52px;" tabindex="4" value="<? echo date("d-m-Y"); ?>" readonly />
                            &nbsp;Shift&nbsp;
                            <? echo create_drop_down( "cbo_shift", 73, $shift_name,"", 1, '- Select -', 0, "",'','','','','' ); ?>
                        </td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Company</td>
                        <td><? echo create_drop_down( "cbo_company_id", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--Select Company--', 0," load_drop_down( 'requires/wash_batch_creation_v2_controller', this.value, 'load_drop_down_location', 'location_td' ); get_php_form_data(this.value,'batch_no_creation','requires/wash_batch_creation_v2_controller');get_php_form_data( this.value, 'company_wise_report_button_setting','requires/wash_batch_creation_v2_controller');",'','','','','',3); ?></td>
                        <td>Supervisor</td>
                        <td><input type="text" name="txt_supervisor" id="txt_supervisor" class="text_boxes" style="width:160px;" /></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Location Name</td>
                        <td width="170" id="location_td"><? echo create_drop_down( "cbo_location_name", 170, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>

                        <td >Floor/Unit</td>
                        <td width="170" id="floor_td"><? echo create_drop_down( "cbo_floor_name", 170, $blank_array,"", 1, "-- Select Floor --", $selected, "" ); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Batch Number</td>
                        <td><input type="text" name="txt_batch_number" id="txt_batch_number" class="text_boxes" style="width:160px;" placeholder="Double Click or Write" onDblClick="openmypage_batchNo()" tabindex="5" /></td>
                        <td>Operator</td>
                        <td><input type="text" name="txt_operator" id="txt_operator" class="text_boxes" style="width:160px;" /></td>
                    </tr>
                    <tr>
                        <td id="batch_color_td" class="must_entry_caption">Batch Color</td>
                        <td><input type="text" name="txt_batch_color" id="txt_batch_color" class="text_boxes" value="" style="width:160px;" disabled="disabled" tabindex="7" /></td>
                        <td width="110" class="must_entry_caption">Batch Weight </td>
                        <td>
                            <input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:160px;" readonly tabindex="8" />
                            
                            <input type="hidden" name="txt_ext_no" id="txt_ext_no" class="text_boxes_numeric" style="width:50px;" disabled="disabled" tabindex="6" />
                        </td>
                    </tr>
                    <tr style="display:none"> 
                        <td>Color Range</td>
                        <td><? echo create_drop_down( "cbo_color_range", 172, $color_range,"",1, "-- Select --", 0, "",'','','','','',9); ?>
                        </td>
                        <td>Organic</td>
                        <td><input type="text" name="txt_organic" id="txt_organic" class="text_boxes" style="width:160px;" tabindex="10" /></td>
                    </tr>
                    <tr>
                    	<td>Process Name</td>
                    	<td><? echo create_drop_down( "txt_process_id",172, $wash_type,"",1, "-- Select --",1,"",1); ?></td>
                        <td style="display:none;">
                            <input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:160px;" placeholder="Double Click To Search" onDblClick="openmypage_process();" tabindex="11" readonly />
                            <input type="hidden" name="txt_process_idddd" id="txt_process_idddd" value="" />
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
						<td class="must_entry_caption" width="130">Operation</td>
                        <td><? echo create_drop_down( "cbo_operation",172, $wash_operation_arr,"",1, "-- Select --",0,"load_drop_down( 'requires/wash_batch_creation_v2_controller', this.value, 'load_drop_down_sub_operation', 'sub_operation'),set_multiselect('cbo_sub_operation','0','0','','0'),list_wash_operation(1)",""); ?>
                    <input type="hidden" name="hidden_operation_id" id="hidden_operation_id" class="text_boxes_numeric" readonly />  
                    <input type="hidden" name="hidden_color_id" id="hidden_color_id" class="text_boxes_numeric" readonly />
                    <input type="hidden" name="hidden_batch_no" id="hidden_batch_no" readonly />
                    <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" readonly />
                    <input type="hidden" name="hidden_batch_dtls_id" id="hidden_batch_dtls_id" readonly />  
                        
                        </td>
                    </tr>
                    <tr>
                    	<td>Sub Operation</td>
                        <td id="sub_operation"><? echo create_drop_down( "cbo_sub_operation",172, $wash_sub_operation_arr,"","", "", 0, "",'','','','','',9); ?></td>
                    	<td>.Dryer no</td>
                        <td ><input type="text" name="txt_dryer_no" id="txt_dryer_no" class="text_boxes"  style="width:160px;" /></td>
                    </tr>
					<tr>
                    	<td>Dryer Operator Name</td>
                        <td ><input type="text" name="txt_dryer_perator" id="txt_dryer_perator" class="text_boxes"  style="width:160px;" /></td>
                    	<td>Dryer Temp</td>
                        <td ><input type="text" name="txt_dryer_emp" id="txt_dryer_emp" class="text_boxes"  style="width:160px;" /></td>
                    </tr> <tr>
                    	<td>Hydro Rpm</td>
                        <td ><input type="text" name="txt_hydro_rpm" id="txt_hydro_rpm" class="text_boxes"  style="width:160px;" /></td>
                        <td>M/C Loading Time</td>
                        <td ><input type="text" name="txt_mc_loading_time" id="txt_mc_loading_time" class="text_boxes"  style="width:160px;" /></td>
                    </tr>
                    <tr>
                    	<td>M/C Un-Loading Time</td>
                        <td ><input type="text" name="txt_mc_un_loading_time" id="txt_mc_un_loading_time" class="text_boxes"  style="width:160px;" /></td>
                    	<td>M/C RPM</td>
                        <td ><input type="text" name="txt_mc_rpm" id="txt_mc_rpm" class="text_boxes"  style="width:160px;" /></td>
                    </tr>
                    <tr>
                    	<td>Remarks</td>
                        <td colspan="5"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" tabindex="15" style="width:360px;" /></td>
                    </tr>
                 </table>
            </fieldset>                 
            <fieldset style="width:750px; margin-top:10px">
            <legend>Item Details</legend>
                <table cellpadding="0" cellspacing="0" width="740" class="rpt_table" border="1" rules="all" id="tbl_item_details">
                    <thead>
                        <th class="must_entry_caption">PO No.</th>
                        <th>Buyer Style</th>
                        <th class="must_entry_caption">Gmts. Item</th>
                        <th >Buyer PO</th>
                        <th class="must_entry_caption">Gmts Qty. (Pcs) </th>
                        <th class="must_entry_caption">Batch Wgt</th>
                        <th></th>
                    </thead>
                    <tbody id="batch_details_container">
                        <tr class="general" id="tr_1">
                            <td>						 
                                <input type="text" name="txtPoNo_1" id="txtPoNo_1" class="text_boxes" style="width:100px;" placeholder="Double Click to Search" onDblClick="openmypage_po(1)" readonly />
                                <input type="hidden" name="poId_1" id="poId_1" class="text_boxes" readonly />
                                <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" class="text_boxes" readonly />
                                <input type="hidden" name="buyerPoId_1" id="buyerPoId_1" class="text_boxes" readonly />
                            </td> 
                            <td>						 
                                <input type="text" name="txtbuyerstyle_1" id="txtbuyerstyle_1" class="text_boxes" style="width:130px;" placeholder="Display" readonly />
                            </td>                             
                            <td><? echo create_drop_down( "cboItem_1", 100, $garments_item,"", 1, "-- Select Item --", 0, "load_color_list(1)" ); ?></td>
                            <td><input name="txtbuyerPo_1" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:80px" placeholder="Display" readonly />
                            <td>
                                <input type="text" name="txtGmtsQty_1" id="txtGmtsQty_1" class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:75px"/>

                                <input type="hidden" name="chkgmts_1" id="chkgmts_1" class="text_boxes" readonly />
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
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>Sum</td>
                        <td style="text-align:center"><input type="text" name="txt_total_gmts_qnty" id="txt_total_gmts_qnty" class="text_boxes_numeric" style="width:75px" readonly /></td>
                        <td style="text-align:center"><input type="text" name="txt_total_batch_qnty" id="txt_total_batch_qnty" class="text_boxes_numeric" style="width:75px" readonly /></td>
                        <td>&nbsp;</td>
                    </tfoot>
                </table>
            </fieldset> 
            <table width="740">
                <tr>
                    <td colspan="6" align="center" class="button_container"> 
                        <? 
                            $date=date('d-m-Y');
						///function reset_form( forms, divs, fields, default_val, extra_func, non_refresh_ids )
                            echo load_submit_buttons($permission, "fnc_batch_creation",0,0,"reset_form('batchcreation_1','','','txt_batch_date,".$date."','','txt_process_id'); $('#tbl_item_details tbody tr:not(:first)').remove(); $('.color_tble').remove(); ",1);
                        ?> 
                        <input type="button" name="print" id="print" value="Print" onClick="fnc_batch_creation(4)" style="width:100px;display:none;" class="formbuttonplasminus" />
                        <input type="hidden" name="update_id" id="update_id"/>
                        <input type="hidden" name="batch_no_creation" id="batch_no_creation" readonly>
                        <input type="hidden" name="cbo_within_group" id="cbo_within_group" readonly>
                        <input type="hidden" name="buyer_id" id="buyer_id" readonly>
                         <input type="hidden" name="unloaded_batch" id="unloaded_batch" readonly>
                          <input type="hidden" name="ext_from" id="ext_from" readonly>
                          <input type="hidden" name="hide_batch_against" id="hide_batch_against"/>
                           <input type="hidden" name="hide_update_id" id="hide_update_id"/>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>
<br>
<div id="list_color" style="width:30%; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;" align="center"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>set_multiselect('cbo_sub_operation','0','0','','0');</script>
</html>