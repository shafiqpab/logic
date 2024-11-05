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
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/fabric_requisition_for_cutting_entry_controller.php?action=requisition_popup&company_id='+cbo_company_id,'Requisition Popup', 'width=780px,height=350px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var reqn_id=this.contentDoc.getElementById("hidden_reqn_id").value;	 //Requisition Id and Number
			
			if(reqn_id!="")
			{
				freeze_window(5);
				reset_form('requisitionEntry_1','','','','','');
				get_php_form_data(reqn_id, "populate_data_from_requisition", "requires/fabric_requisition_for_cutting_entry_controller" );
				var list_view = trim(return_global_ajax_value(reqn_id, 'populate_list_view', '', 'requires/fabric_requisition_for_cutting_entry_controller'));
				$("#scanning_tbl tbody").html(list_view);	
				set_all_onclick();
				set_button_status(1, permission, 'fnc_fabric_requisition_for_cutting',1);
				release_freezing();
			}
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
	
	function openmypage_po()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Fabric Selection Form';	
			var page_link ='requires/fabric_requisition_for_cutting_entry_controller.php?company_id='+cbo_company_id+'&action=po_popup';
			var popup_width="1000px";

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var hidden_data=this.contentDoc.getElementById("hidden_data").value;	
				var data=hidden_data.split("_");
				var html=''; var num_row=$('#scanning_tbl tbody tr').length+1;
				for(var k=0; k<data.length; k++)
				{
					if(num_row%2==0) var bgcolor="#E9F3FF"; else var bgcolor="#FFFFFF";

					var row_data=data[k].split("**");
					
					var html=html+'<tr bgcolor="'+bgcolor+'" id="tr_'+num_row+'"><td width="40">'+num_row+'</td><td width="60" style="word-break:break-all;">'+row_data[0]+'</td><td width="50">'+row_data[2]+'</td><td width="60">'+row_data[3]+'</td><td width="80" style="word-break:break-all;">'+row_data[5]+'</td><td width="100" style="word-break:break-all;">'+row_data[8]+'</td><td width="100" style="word-break:break-all;">'+row_data[10]+'</td><td width="150" style="word-break:break-all;">'+row_data[11]+'</td><td width="60" style="word-break:break-all;" id="gsm'+num_row+'">'+row_data[13]+'</td><td width="60" id="dia'+num_row+'" style="word-break:break-all;">'+row_data[14]+'</td><td width="80">'+row_data[15]+'</td><td width="60">'+row_data[17]+'</td><td width="80" align="right" style="word-break:break-all;">'+row_data[19]+'</td><td align="center"><input type="text" class="text_boxes_numeric" style="width:80px" id="reqsnQty'+num_row+'" name="reqsnQty[]" value="'+row_data[20]+'"/><input type="hidden" value="'+row_data[1]+'" id="buyerId'+num_row+'" name="buyerId[]"/><input type="hidden" value="'+row_data[6]+'" id="poId'+num_row+'" name="poId[]"/><input type="hidden" value="'+row_data[12]+'" id="deterId'+num_row+'" name="deterId[]"/><input type="hidden" value="'+row_data[16]+'" id="colorId'+num_row+'" name="colorId[]"/><input type="hidden" value="'+row_data[7]+'" id="itemId'+num_row+'" name="itemId[]"/><input type="hidden" value="'+row_data[9]+'" id="bodyPartId'+num_row+'" name="bodyPartId[]"/><input type="hidden" value="'+row_data[4]+'" id="jobNo'+num_row+'" name="jobNo[]"/><input type="hidden" value="'+row_data[18]+'" id="sizeId'+num_row+'" name="sizeId[]"/><input type="hidden" value="" id="dtlsId'+num_row+'" name="dtlsId[]"/></td></tr>';
					num_row++;
				}
				$("#scanning_tbl tbody:last").append(html);	
				set_all_onclick();
			}
		}
	}
	
	function fnc_fabric_requisition_for_cutting( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if(operation==4)
		{
			//alert('report');
			
			/*var report_title=$( "div.form_caption" ).html();
		  	generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 'emblishment_issue_print', 'requires/bundle_wise_iron_entry_controller'); 
		 	return;*/
			
			var report_title=$( "div.form_caption" ).html();
			//generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_challan_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_delivery_print','requires/fabric_requisition_for_cutting_entry_controller');
			return;
		}
		
	 	if(form_validation('cbo_company_id*txt_requisition_date','Company*Requisition Date')==false)
		{
			return; 
		}
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_requisition_date').val(), current_date)==false)
		{
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
			var sizeId=$('#sizeId'+i).val();
			var reqsnQty=$('#reqsnQty'+i).val()*1;
			var dtlsId=$('#dtlsId'+i).val();
			
			if(reqsnQty>0 || dtlsId!="")
			{
				j++;
				dataString+='&buyerId' + j + '=' + buyerId + '&jobNo' + j + '=' + jobNo + '&poId' + j + '=' + poId + '&itemId' + j + '=' + itemId + '&bodyPartId' + j + '=' + bodyPartId + '&deterId' + j + '=' + deterId + '&gsm' + j + '=' + gsm + '&dia' + j + '=' + dia + '&colorId' + j + '=' + colorId + '&sizeId' + j + '=' + sizeId + '&reqsnQty' + j + '=' + reqsnQty + '&dtlsId' + j + '=' + dtlsId;
			}
		}
		
		if(j<1)
		{
			alert('No data');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('cbo_company_id*layPlan_id*txt_requisition_date*txt_requisition_no*update_id',"../")+dataString;
		//alert(data);return;
		freeze_window(operation);
		
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
			show_msg(response[0]);
			
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_requisition_no').value = response[2];
				var list_view = trim(return_global_ajax_value(response[1], 'populate_list_view', '', 'requires/fabric_requisition_for_cutting_entry_controller'));
				$("#scanning_tbl tbody").html(list_view);
				set_button_status(1, permission, 'fnc_fabric_requisition_for_cutting',1);
			}
			release_freezing();
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
    <form name="requisitionEntry_1" id="requisitionEntry_1"> 
		<div align="center" style="width:100%;">
			<? echo load_freeze_divs ("../",$permission); ?>
            <fieldset style="width:810px;">
				<legend>Fabric Requisition</legend>
                <table cellpadding="0" cellspacing="2" width="800">
                    <tr>
                        <td align="right" width="100" colspan="3"><b>Requisition No</b></td>
                        <td colspan="3">
                        	<input type="text" name="txt_requisition_no" id="txt_requisition_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_requisition()" placeholder="Browse For Requisition No" readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption" align="right">Company</td>
                        <td>
                            <? 
                                echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", 0, "",0 );
                            ?>
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
                        <td align="center" colspan="6"><strong>Order Number</strong>&nbsp;&nbsp;
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:170px;" placeholder="Browse" onDblClick="openmypage_po()" readonly/>
                        </td>
                    </tr>
                </table>
			</fieldset>
			<br>
			<fieldset style="width:1133px;text-align:left">
				<table cellpadding="0" width="1115" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
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
                        <th width="60">Size</th>
                        <th width="80">Budget Qty</th>
                        <th>Reqn. Qty</th>
                    </thead>
                 </table>
                 <div style="width:1133px; max-height:250px; overflow-y:scroll" align="left">
                 	<table cellpadding="0" cellspacing="0" width="1115" border="1" id="scanning_tbl" rules="all" class="rpt_table">
                    	<tbody>
                        </tbody>
                	</table>
                </div>
                <br>
                <table width="1120" cellpadding="0" cellspacing="0" border="1" id="" rules="all">
                    <tr>
                        <td align="center" class="button_container">
                            <? 
                            	echo load_submit_buttons($permission,"fnc_fabric_requisition_for_cutting",0,1,"reset_form('requisitionEntry_1','','','','$(\'#scanning_tbl tbody tr\').remove();')",1);
                            ?>
                        </td>
                    </tr>  
                </table>
			</fieldset>
    	</div>
	</form>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
