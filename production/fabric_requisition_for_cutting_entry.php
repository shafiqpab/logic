<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Fabric Requisition For Cutting
				
Functionality	:	
JS Functions	:
Created by		:	Fuad
Creation date 	: 	15-03-2015
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
echo load_html_head_contents("Fabric Requisition For Cutting","../", 1, 1, $unicode,0,0); 

?>	
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function openmypage_requisition()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var buyerId = $("#hidden_buyer_id").val();
		var entry_break_down_type = $("#entry_break_down_type").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/fabric_requisition_for_cutting_entry_controller.php?action=requisition_popup&company_id='+cbo_company_id+'&buyerId='+buyerId,'Requisition Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var reqn_id=this.contentDoc.getElementById("hidden_reqn_id").value;	 //Requisition Id and Number
			
			if(reqn_id!="")
			{
				freeze_window(5);
				reset_form('requisitionEntry_1','','','','','entry_break_down_type');
				get_php_form_data(reqn_id, "populate_data_from_requisition", "requires/fabric_requisition_for_cutting_entry_controller" );
				var list_view = trim(return_global_ajax_value(reqn_id+'**'+entry_break_down_type, 'populate_list_view', '', 'requires/fabric_requisition_for_cutting_entry_controller'));
				$("#scanning_tbl tbody").html(list_view);	
				set_all_onclick();
				set_button_status(1, permission, 'fnc_fabric_requisition_for_cutting',1);
				release_freezing();
			}
		}
	}
	/*function wvn_finish_fabric_po_to_style_wise_fnc(data)
	{
		var varible_data=return_global_ajax_value( data, 'varible_setting_wvn_style_wise', '', 'requires/fabric_requisition_for_cutting_entry_controller');
		if(varible_data==1)
		{
			$("#hdn_variable_setting_status").val(varible_data);
			$("#captionName").html("Style Number");


		}
		else
		{
			varible_data=0;
			$("#hdn_variable_setting_status").val(varible_data);
			$("#captionName").html("Order Number");
		}
	}*/
	function company_wise_finish_fabric_requisition_cutting_variable(data)
	{
		var varible_data=return_global_ajax_value( data, 'varible_setting_fabric_requisition_cutting_variable', '', 'requires/fabric_requisition_for_cutting_entry_controller');
		// alert(varible_data);
		if(varible_data=="")
		{
			varible_data=0;
			$("#entry_break_down_type").val(varible_data);
		}
		else
		{
			$("#entry_break_down_type").val(varible_data);
		}
	}
	function openmypage_layPlan()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/fabric_requisition_for_cutting_entry_controller.php?action=layPlan_popup&company_id='+cbo_company_id,'Lay Plan Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var lay_plan_id=this.contentDoc.getElementById("hidden_lay_plan_id").value;	 //Id and Number
			var layPlan_no=this.contentDoc.getElementById("hidden_layPlan_no").value;	 //Id and Number
			
			$('#layPlan_id').val(lay_plan_id);
			$('#txt_layPlan_No').val(layPlan_no);
		}
	}

	function com_variable_data(data){

		//var varible_data=return_global_ajax_value( data, 'varible_setting_wvn_style_wise', '', 'requires/fabric_requisition_for_cutting_entry_controller');
		var varible_data_colorSize=return_global_ajax_value( data, 'varible_setting_fabric_requisition_cutting_variable', '', 'requires/fabric_requisition_for_cutting_entry_controller');


		if(varible_data_colorSize==2)
		{
			$("#entry_break_down_type").val(varible_data_colorSize);
			var table_heading='<thead><th style="word-break:break-all;" width="40"><p>SL</p></th><th style="word-break:break-all;" width="60"><p>Buyer</p></th><th style="word-break:break-all;" width="50"><p>Job Year</p></th><th style="word-break:break-all;" width="60"><p>Job No</p></th><th style="word-break:break-all;" width="80"><p>Order No</p></th><th style="word-break:break-all;" width="100"><p>Gmts. Item</p></th><th style="word-break:break-all;" width="100"><p>Body part</p></th><th style="word-break:break-all;" width="150"><p>Const/Composition</p></th><th style="word-break:break-all;" width="60"><p>GSM</p></th><th style="word-break:break-all;" width="60"><p>Dia</P></th><th style="word-break:break-all;" width="80"><p>Gmts. Color</p></th><th style="word-break:break-all;" width="80"><p>Fab. Color</p></th><th style="word-break:break-all;" width="80"><p>Uom</p></th><th style="word-break:break-all;" width="80"><p>Consumption</p></th><th style="word-break:break-all;" width="80"><p>Fin. Cons/Dzn.[Pack]</p></th><th style="word-break:break-all;" width="80"><p>Budget Qty</p></th><th style="word-break:break-all;" width="80"><p>Cumulative Qty</p></th><th style="word-break:break-all;"><p>Reqn. Qty</p></th></thead>';
			$("#scanning_tbl_top").html(table_heading);
		}
		else
		{
			$("#entry_break_down_type").val(varible_data_colorSize);
			var table_heading='<thead><th style="word-break:break-all;" width="40"><p>SL</p></th><th style="word-break:break-all;" width="60"><p>Buyer</p></th><th style="word-break:break-all;" width="50"><p>Job Year</p></th><th style="word-break:break-all;" width="60"><p>Job No</p></th><th style="word-break:break-all;" width="80"><p>Order No</p></th><th style="word-break:break-all;" width="100"><p>Gmts. Item</p></th><th style="word-break:break-all;" width="100"><p>Body part</p></th><th style="word-break:break-all;" width="150"><p>Const/Composition</p></th><th style="word-break:break-all;" width="60"><p>GSM</p></th><th style="word-break:break-all;" width="60"><p>Dia</P></th><th style="word-break:break-all;" width="80"><p>Gmts. Color</p></th><th style="word-break:break-all;" width="80"><p>Fab. Color</p></th><th style="word-break:break-all;" width="60"><p>Size</p></th><th style="word-break:break-all;" width="80"><p>Uom</p></th><th style="word-break:break-all;" width="80"><p>Consumption</p></th><th style="word-break:break-all;" width="80"><p>Fin. Cons/Dzn.[Pack]</p></th><th style="word-break:break-all;" width="80"><p>Budget Qty</p></th><th style="word-break:break-all;" width="80"><p>Cumulative Qty</p></th><th style="word-break:break-all;"><p>Reqn. Qty</p></th></thead>';
			$("#scanning_tbl_top").html(table_heading);
		}

		/*if(varible_data==1)
		{
            
			$("#hdn_variable_setting_status").val(varible_data);
			var table_heading='<thead><th style="word-break:break-all;" width="40"><p>SL</p></th><th style="word-break:break-all;" width="60"><p>Buyer</p></th><th style="word-break:break-all;" width="50"><p>Job Year</p></th><th style="word-break:break-all;" width="60"><p>Job No</p></th><th style="word-break:break-all;" width="100"><p>Style No</p></th><th style="word-break:break-all;" width="80"><p>Order No</p></th><th style="word-break:break-all;" width="100"><p>Gmts. Item</p></th><th style="word-break:break-all;" width="100"><p>Body part</p></th><th style="word-break:break-all;" width="150"><p>Const/Composition</p></th><th style="word-break:break-all;" width="60"><p>Fabric Ref</p></th><th style="word-break:break-all;" width="60"><p>RD No</p></th><th style="word-break:break-all;" width="60"><p>Weight</p></th><th style="word-break:break-all;" width="60"><p>Weight Type</p>	</th><th style="word-break:break-all;" width="60"><p>Width</P></th><th style="word-break:break-all;" width="60"><p>Cutable Width</p></th><th style="word-break:break-all;" width="80"><p>Gmts. Color</p></th><th style="word-break:break-all;" width="60"><p>Size</p></th><th style="word-break:break-all;" width="80"><p>Uom</p></th><th style="word-break:break-all;" width="80"><p>Consumption</p></th><th style="word-break:break-all;" width="80"><p>Budget Qty</p></th><th style="word-break:break-all;" width="80"><p>Cumulative Qty</p></th><th style="word-break:break-all;" width="116"><p>Reqn. Qty</p></th></thead>';
			$("#scanning_tbl_top").html(table_heading);
			
		}
		else
		{
			$("#hdn_variable_setting_status").val(varible_data);
			var table_heading='<thead><th style="word-break:break-all;" width="40">SL</th><th style="word-break:break-all;" width="60">Buyer</th><th style="word-break:break-all;" width="50">Job Year</th><th style="word-break:break-all;" width="60">Job No</th><th style="word-break:break-all;" width="80">Order No</th><th style="word-break:break-all;" width="100">Gmts. Item</th><th style="word-break:break-all;" width="100">Body part</th><th style="word-break:break-all;" width="150">Const/Composition</th><th style="word-break:break-all;" width="60">GSM</th><th style="word-break:break-all;" width="60">Dia</th><th style="word-break:break-all;" width="80">Gmts. Color</th><th style="word-break:break-all;" width="60">Size</th><th style="word-break:break-all;" width="80">Uom</th><th style="word-break:break-all;" width="80">Consumption</th><th style="word-break:break-all;" width="80">Budget Qty</th><th style="word-break:break-all;" width="80">Cumulative Qty</th><th style="word-break:break-all;">Reqn. Qty</th></thead>';
			$("#scanning_tbl_top").html(table_heading);

		}*/

	}
	
	function openmypage_po()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var buyerId = $("#hidden_buyer_id").val();
			var entry_break_down_type = $("#entry_break_down_type").val();
			var title = 'Fabric Selection Form';	
			var page_link ='requires/fabric_requisition_for_cutting_entry_controller.php?company_id='+cbo_company_id+'&buyerId='+buyerId+'&entry_break_down_type='+entry_break_down_type+'&action=po_popup';
			var popup_width="1140px";

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var hidden_data=this.contentDoc.getElementById("hidden_data").value;
				// alert(hidden_data);	
				var data=hidden_data.split("_");
				var html=''; var num_row=$('#scanning_tbl tbody tr').length+1;
				for(var k=0; k<data.length; k++)
				{
					if(num_row%2==0) var bgcolor="#E9F3FF"; else var bgcolor="#FFFFFF";

					var row_data=data[k].split("**");
					//alert(row_data[24]);

					if(entry_break_down_type==2)
					{
						var html=html+'<tr bgcolor="'+bgcolor+'" id="tr_'+num_row+'"><td width="40">'+num_row+'</td><td width="60" style="word-break:break-all;">'+row_data[0]+'</td><td width="50">'+row_data[2]+'</td><td width="60">'+row_data[3]+'</td><td width="80" style="word-break:break-all;">'+row_data[5]+'</td><td width="100" style="word-break:break-all;">'+row_data[8]+'</td><td width="100" style="word-break:break-all;">'+row_data[10]+'</td><td width="150" style="word-break:break-all;"><a href="#report_details" onClick="openmypage_stock_qnty('+row_data[6]+','+row_data[12]+','+row_data[25]+','+"'stock_qnty_popup'"+');">'+row_data[11]+'</a></td><td width="60" style="word-break:break-all;" id="gsm'+num_row+'">'+row_data[13]+'</td><td width="60" id="dia'+num_row+'" style="word-break:break-all;">'+row_data[14]+'</td><td width="80">'+row_data[15]+'</td><td width="80">'+row_data[26]+'</td><td width="80" align="right" style="word-break:break-all;">'+row_data[19]+'</td><td width="80" align="right" style="word-break:break-all;">'+row_data[20]+'</td><td width="80" align="right" style="word-break:break-all;">'+row_data[27]+'</td><td width="80" align="right" style="word-break:break-all;">'+row_data[21]+'</td><td width="80" align="right" style="word-break:break-all;">'+row_data[23]+'</td><td align="center"><input type="text" class="text_boxes_numeric" style="width:80px" id="reqsnQty'+num_row+'" name="reqsnQty[]" value="" onblur="fn_check_qnty(this.id,this.value,'+row_data[21]+','+num_row+')" ondblclick="open_requisition_batch('+num_row+')" placeholder="double click" readonly/><input type="hidden" value="'+row_data[1]+'" id="buyerId'+num_row+'" name="buyerId[]"/><input type="hidden" value="'+row_data[6]+'" id="poId'+num_row+'" name="poId[]"/><input type="hidden" value="'+row_data[12]+'" id="deterId'+num_row+'" name="deterId[]"/><input type="hidden" value="'+row_data[16]+'" id="colorId'+num_row+'" name="colorId[]"/><input type="hidden" value="'+row_data[25]+'" id="fabColorId'+num_row+'" name="fabColorId[]"/><input type="hidden" value="'+row_data[7]+'" id="itemId'+num_row+'" name="itemId[]"/><input type="hidden" value="'+row_data[9]+'" id="bodyPartId'+num_row+'" name="bodyPartId[]"/><input type="hidden" value="'+row_data[4]+'" id="jobNo'+num_row+'" name="jobNo[]"/><input type="hidden" value="'+row_data[18]+'" id="sizeId'+num_row+'" name="sizeId[]"/><input type="hidden" value="" id="dtlsId'+num_row+'" name="dtlsId[]"/><input type="hidden" value="'+row_data[23]+'" id="prevReqQty'+num_row+'" name="prevReqQty[]"/><input type="hidden" value="'+row_data[21]+'" id="budgetQty'+num_row+'" name="budgetQty[]"/><input type="hidden" value="'+row_data[24]+'" id="uom'+num_row+'" name="uom[]"/><input type="hidden" value="'+row_data[20]+'" id="consumption'+num_row+'" name="consumption[]"/><input type="hidden" value="'+row_data[27]+'" id="FinConsDzn'+num_row+'" name="FinConsDzn[]"/><input type="hidden" value="" id="saveString'+num_row+'" name="saveString[]"/></td></tr>';
					}
					else
					{
						var html=html+'<tr bgcolor="'+bgcolor+'" id="tr_'+num_row+'"><td width="40">'+num_row+'</td><td width="60" style="word-break:break-all;">'+row_data[0]+'</td><td width="50">'+row_data[2]+'</td><td width="60">'+row_data[3]+'</td><td width="80" style="word-break:break-all;">'+row_data[5]+'</td><td width="100" style="word-break:break-all;">'+row_data[8]+'</td><td width="100" style="word-break:break-all;">'+row_data[10]+'</td><td width="150" style="word-break:break-all;"><a href="#report_details" onClick="openmypage_stock_qnty('+row_data[6]+','+row_data[12]+','+row_data[25]+','+"'stock_qnty_popup'"+');">'+row_data[11]+'</a></td><td width="60" style="word-break:break-all;" id="gsm'+num_row+'">'+row_data[13]+'</td><td width="60" id="dia'+num_row+'" style="word-break:break-all;">'+row_data[14]+'</td><td width="80">'+row_data[15]+'</td><td width="80">'+row_data[26]+'</td><td width="60">'+row_data[17]+'</td><td width="80" align="right" style="word-break:break-all;">'+row_data[19]+'</td><td width="80" align="right" style="word-break:break-all;">'+row_data[20]+'</td><td width="80" align="right" style="word-break:break-all;">'+row_data[27]+'</td><td width="80" align="right" style="word-break:break-all;">'+row_data[21]+'</td><td width="80" align="right" style="word-break:break-all;">'+row_data[23]+'</td><td align="center"><input type="text" class="text_boxes_numeric" style="width:80px" id="reqsnQty'+num_row+'" name="reqsnQty[]" value="" onblur="fn_check_qnty(this.id,this.value,'+row_data[21]+','+num_row+')" ondblclick="open_requisition_batch('+num_row+')" placeholder="double click" readonly/><input type="hidden" value="'+row_data[1]+'" id="buyerId'+num_row+'" name="buyerId[]"/><input type="hidden" value="'+row_data[6]+'" id="poId'+num_row+'" name="poId[]"/><input type="hidden" value="'+row_data[12]+'" id="deterId'+num_row+'" name="deterId[]"/><input type="hidden" value="'+row_data[16]+'" id="colorId'+num_row+'" name="colorId[]"/><input type="hidden" value="'+row_data[25]+'" id="fabColorId'+num_row+'" name="fabColorId[]"/><input type="hidden" value="'+row_data[7]+'" id="itemId'+num_row+'" name="itemId[]"/><input type="hidden" value="'+row_data[9]+'" id="bodyPartId'+num_row+'" name="bodyPartId[]"/><input type="hidden" value="'+row_data[4]+'" id="jobNo'+num_row+'" name="jobNo[]"/><input type="hidden" value="'+row_data[18]+'" id="sizeId'+num_row+'" name="sizeId[]"/><input type="hidden" value="" id="dtlsId'+num_row+'" name="dtlsId[]"/><input type="hidden" value="'+row_data[23]+'" id="prevReqQty'+num_row+'" name="prevReqQty[]"/><input type="hidden" value="'+row_data[21]+'" id="budgetQty'+num_row+'" name="budgetQty[]"/><input type="hidden" value="'+row_data[24]+'" id="uom'+num_row+'" name="uom[]"/><input type="hidden" value="'+row_data[20]+'" id="consumption'+num_row+'" name="consumption[]"/><input type="hidden" value="'+row_data[27]+'" id="FinConsDzn'+num_row+'" name="FinConsDzn[]"/><input type="hidden" value="" id="saveString'+num_row+'" name="saveString[]"/></td></tr>';

						//<input type="text" class="text_boxes_numeric" style="width:80px" id="reqsnQty'+num_row+'" name="reqsnQty[]" value="'+row_data[22]+'" onblur="fn_check_qnty(this.id,this.value,'+row_data[21]+','+num_row+')" ondblclick="open_requisition_batch('+num_row+')" />

					}

					
					
					num_row++;
				}
				$("#scanning_tbl tbody:last").append(html);	
				set_all_onclick();
				var rowData=data[0].split("**");
				$("#hidden_buyer_id").val(rowData[1]);	
				document.getElementById('cbo_company_id').disabled=true;
			}
		}
	}

	function open_requisition_batch(row_num)
	{
		var cbo_company_id = $("#cbo_company_id").val();
		var buyerId = $("#buyerId"+row_num).val()*1;
		var poId = $("#poId"+row_num).val()*1;
		var deterId = $("#deterId"+row_num).val()*1;
		var bodyPartId = $("#bodyPartId"+row_num).val()*1;
		var uom = $("#uom"+row_num).val()*1;
		var gsm = $("#gsm"+row_num).text()*1;
		var dia = $("#dia"+row_num).text()*1;
		var garmentColor = $("#colorId"+row_num).val()*1;
		var fabColorId = $("#fabColorId"+row_num).val()*1;
		var budgetQty = $("#budgetQty"+row_num).val()*1;
		var dtlsId = $("#dtlsId"+row_num).val()*1;
		var itemId = $("#itemId"+row_num).val()*1;
		var saveString = $('#saveString'+row_num).val();

		var title = 'Batch Selection Form';	
		var page_link ='requires/fabric_requisition_for_cutting_entry_controller.php?company_id='+cbo_company_id+'&buyerId='+buyerId+'&poId='+poId+'&deterId='+deterId+'&bodyPartId='+bodyPartId+'&uom='+uom+'&gsm='+gsm+'&dia='+dia+'&dtlsId='+dtlsId+'&itemId='+itemId+'&budgetQty='+budgetQty+'&garmentColor='+garmentColor+'&fabColorId='+fabColorId+'&saveString='+saveString+'&action=requisition_qnty_popup';
		var popup_width="850px";

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm");
			var save_string=this.contentDoc.getElementById("save_string").value;
			var tot_requ_qnty=this.contentDoc.getElementById("tot_requ_qnty").value;
			$('#saveString'+row_num).val(save_string);
			$('#reqsnQty'+row_num).val(tot_requ_qnty);
		}
	}

	function fn_check_qnty(id,reqQty,budgetQty,row_num)
	{
		var prevRcvQty = $("#prevReqQty"+row_num).val()*1;
		var curBudgetQty = budgetQty*1 - prevRcvQty;
		if(reqQty*1 > curBudgetQty*1)
		{
			alert('Reqn. qty can not over than budget qty. Previous receive qnty = '+prevRcvQty);
			document.getElementById(id).value='';
			return;
		}
	}
	
	function fnc_fabric_requisition_for_cutting( operation )
	{
		freeze_window(operation);
		/*if(operation==2)
		{
			show_msg('13');
			release_freezing();
			return;
		}*/
		
		if(operation==4)
		{cbo_template_id
			
			 print_report( $('#cbo_company_id').val()+'*'+$('#txt_requisition_no').val()+'*'+$('#update_id').val()+'*'+$('#txt_layPlan_No').val()+'*'+$('#cbo_template_id').val(), "grey_delivery_print", "requires/fabric_requisition_for_cutting_entry_controller" );
			 release_freezing();
			return;
		}
		
	 	if(form_validation('cbo_company_id*txt_requisition_date','Company*Requisition Date')==false)
		{
			release_freezing();
			return; 
		}
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_requisition_date').val(), current_date)==false)
		{
			release_freezing();
			alert("Requisition Date Can not Be Greater Than Today");
			return;
		}	
		
		var row_num=$('#scanning_tbl tbody tr').length;
		var dataString=""; var j=0;
		for (var i=1; i<=row_num; i++)
		{
			var buyerId=$('#buyerId'+i).val();
			var jobNo=$('#jobNo'+i).val();
			var poId=$('#poId'+i).val();
			var itemId=$('#itemId'+i).val();
			var bodyPartId=$('#bodyPartId'+i).val();
			var deterId=$('#deterId'+i).val();
			var gsm=$('#gsm'+i).text();
			var dia=$('#dia'+i).text();
			var colorId=$('#colorId'+i).val();
			var fabColorId=$('#fabColorId'+i).val();
			var sizeId=$('#sizeId'+i).val();
			var reqsnQty=$('#reqsnQty'+i).val()*1;
			var budgetQty=$('#budgetQty'+i).val()*1;
			var dtlsId=$('#dtlsId'+i).val();
			var uom=$('#uom'+i).val();
			var consumption=$('#consumption'+i).val();
			var saveString=$('#saveString'+i).val();

			if(operation==1 && reqsnQty <1)
			{
				release_freezing();
				alert('Can not update with 0 qnty.');
				$('#reqsnQty'+i).focus();
				return;
			}
			
			if(reqsnQty>0 || dtlsId!="")
			{
				j++;
				dataString+='&buyerId' + j + '=' + buyerId + '&jobNo' + j + '=' + jobNo + '&poId' + j + '=' + poId + '&itemId' + j + '=' + itemId + '&bodyPartId' + j + '=' + bodyPartId + '&deterId' + j + '=' + deterId + '&gsm' + j + '=' + gsm + '&dia' + j + '=' + dia + '&colorId' + j + '=' + colorId + '&fabColorId' + j + '=' + fabColorId + '&sizeId' + j + '=' + sizeId + '&reqsnQty' + j + '=' + reqsnQty + '&budgetQty' + j + '=' + budgetQty + '&uom' + j + '=' + uom + '&consumption' + j + '=' + consumption + '&saveString' + j + '=' + saveString + '&dtlsId' + j + '=' + dtlsId;
			}
		}
		
		if(j<1)
		{
			release_freezing();
			alert('No data');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('cbo_company_id*layPlan_id*txt_requisition_date*txt_requisition_no*entry_break_down_type*update_id',"../")+dataString;
		//alert(data);//return;
		
		
		http.open("POST","requires/fabric_requisition_for_cutting_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_fabric_requisition_for_cutting_Reply_info;
	}

	function fnc_fabric_requisition_for_cutting_Reply_info()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();return;
			var response=trim(http.responseText).split('**');
			
			
			if((response[0]==0 || response[0]==1))
			{
				show_msg(response[0]);
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_requisition_no').value = response[2];
				var entry_break_down_type = $("#entry_break_down_type").val();
				var list_view = trim(return_global_ajax_value(response[1]+'**'+entry_break_down_type, 'populate_list_view', '', 'requires/fabric_requisition_for_cutting_entry_controller'));
				$("#scanning_tbl tbody").html(list_view);
				$('#cbo_company_id').attr('disabled','disabled');
				set_button_status(1, permission, 'fnc_fabric_requisition_for_cutting',1);
			}
			if(response[0]==2)
			{
				if(response[1]==14)
				{
					alert(response[2]);
				}
				else
				{
					reset_form('requisitionEntry_1','','','','','');
					$("#scanning_tbl tbody").html('');
					$('#cbo_company_id').removeAttr('disabled','disabled');
					show_msg(response[0]);
				}
			}
			release_freezing();
		}
	}

	function fnDisableEnableFields()
	{
		document.getElementById('cbo_company_id').disabled=false;
	}

	function generate_print_report(i)
	{
		
		if(form_validation('txt_requisition_no','Requisition No')==false)
		{
			swal({
			  title: "OOPS!",
			  text: "Requisition Number Required!",
			  icon: "error",
			});
			return; 
		}
		print_report( $('#cbo_company_id').val()+'*'+$('#txt_requisition_no').val()+'*'+$('#update_id').val()+'*'+$('#txt_layPlan_No').val()+'*'+$('#cbo_template_id').val(), "grey_delivery_print"+i, "requires/fabric_requisition_for_cutting_entry_controller" );
			return;
	}
	function openmypage_stock_qnty(po_id,determinationId,fabcolor,action)
	{
		var companyID = $("#cbo_company_id").val();
			//alert(desc_key);
		var popup_width='850px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/fabric_requisition_for_cutting_entry_controller.php?companyID='+companyID+'&po_id='+po_id+'&determinationId='+determinationId+'&fabcolor='+fabcolor+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=300px,center=1,resize=0,scrolling=0','../');
	}
</script>
</head>
<body onLoad="set_hotkey();">
	<? echo load_freeze_divs ("../",$permission); ?>
    <form name="requisitionEntry_1" id="requisitionEntry_1"> 
		<div align="center" style="width:100%;">
			
            <fieldset style="width:810px;">
				<legend>Fabric Requisition</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td align="right" width="100" colspan="3"><b>Requisition No</b></td>
                        <td colspan="3">
                        	<input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_requisition();" placeholder="Browse For Requisition No" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                            <input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id"/>
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption" align="right">Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", 0, "company_wise_finish_fabric_requisition_cutting_variable(this.value);com_variable_data(this.value);",0 );
                                //wvn_finish_fabric_po_to_style_wise_fnc(this.value);
                            ?>
                            <input type="hidden" name="hdn_variable_setting_status" id="hdn_variable_setting_status" class="text_boxes"/>
							<input type="hidden" name="entry_break_down_type" id="entry_break_down_type" class="text_boxes"/>
                        </td>
                        <td align="right" class="must_entry_caption">Requisition Date</td>
                        <td><input type="text" name="txt_requisition_date" id="txt_requisition_date" class="datepicker" style="width:140px;" readonly /></td>
                        <td>Lay Plan Cutting No</td>                                              
                        <td>
                            <input type="text" name="txt_layPlan_No" id="txt_layPlan_No" class="text_boxes" style="width:170px;" placeholder="Browse" onDblClick="openmypage_layPlan()" readonly/>
                            <input type="hidden" name="layPlan_id" id="layPlan_id" class="text_boxes"/>
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                        <td align="center" colspan="6"><strong><span id="captionName">Order Number</span></strong>&nbsp;&nbsp;
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:170px;" placeholder="Browse" onDblClick="openmypage_po()" readonly/>
                        </td>
                    </tr>
                </table>
			</fieldset>
			<br>

			<? 
			 	// $hdn_variable_setting_status=0;
				/*if ($hdn_variable_setting_status==1) {
					$width1="1713px";
					$width2="1695";
				}
				else
				{*/
					$width1="1533px";
					$width2="1515";
				//}
			?>
			<fieldset style="width:<? echo $width1; ?>;text-align:left">
				<table cellpadding="0" width="<? echo $width2; ?>" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
                    <thead>
                    	<th width="40">SL</th>
                        <th width="60">Buyer</th>
                        <th width="50">Job Year</th>
                        <th width="60">Job No</th>
                        
                        <th width="80">Order No</th>
                        <th width="100">Gmts. Item</th>
                        <th width="100">Body part</th>
                        <th width="150">Const/Composition</th>				
                        
	                        <th width="60">GSM</th>
	                        <th width="60">Dia</th>
	                       
                        <th width="80">Gmts. Color</th>
                        <th width="80">Fab. Color</th>
                        <th width="60">Size</th>
                        <th width="80">Uom</th>
                        <th width="80">Consumption</th>                        
                        <th width="80">Fin. Cons/Dzn.[Pack]</th>                        
                        <th width="80">Budget Qty</th>
                        <th width="80">Cumulative Qty</th>
                        <th>Reqn. Qty</th>
                    </thead>
                 </table>
                 <div style="width:<? echo $width1; ?>; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="<? echo $width2; ?>" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        </tbody>
                	</table>
                </div>
                <br>
                <table width="<? echo $width1; ?>" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                            <? 
                            	echo create_drop_down( "cbo_template_id", 85, $report_template_list,'', 0, '', 0, "");
								echo load_submit_buttons($permission,"fnc_fabric_requisition_for_cutting",0,1,"reset_form('requisitionEntry_1','','','','$(\'#scanning_tbl tbody tr\').remove();');fnDisableEnableFields();",1);
                            ?>
                            <input type="button" value="Print2" name="print2" onClick="generate_print_report(2)" style="width:80px" id="Print2" class="formbutton">
                            <input type="button" value="Print3" name="print3" onClick="generate_print_report(3)" style="width:80px" id="Print2" class="formbutton">
                        </td>
                    </tr>  
                </table>
			</fieldset>
    	</div>
	</form>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
