<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Trims Purchase Requisition
Functionality	:	
JS Functions	:
Created by		:	Sapayth
Creation date 	: 	17-01-2021
Updated by 		:	
Update date		: 	
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = '';

if ($company_id >0) {
    $company_credential_cond = "and comp.id in($company_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;  
}
//========== user credential end ==========

//--------------------------------------------------------------------------------------------------------------------
// finish_trims_purchase_requisition_controller.php
echo load_html_head_contents("Finish Trims Purchase Requisition","../../", 1, 1, $unicode);
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../../logout.php";
	var permission='<? echo $permission; ?>';
	<?
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][69] );
	echo "var field_level_data= ". $data_arr . ";\n";
	?>
	var str_size = [<? echo substr(return_library_autocomplete("select size_name from lib_size where status_active=1 and is_deleted=0", "size_name"), 0, -1); ?>];
	var str_color = [<? echo substr(return_library_autocomplete("select color_name from lib_color where status_active=1 and is_deleted=0", "color_name"), 0, -1); ?>];
	var str_trimdescription = [ <? echo substr(return_library_autocomplete("select description from  wo_pre_cost_trim_cost_dtls group by description", "description" ), 0, -1); ?> ];

	$("#itemColor_1").autocomplete({
		source: str_color
	});
	$("#itemsize_1").autocomplete({
		source:  str_size
	});
	$("#itemdescription_1").autocomplete({
		source: str_trimdescription
	});

	function openmypage_requisition()
	{
		if (form_validation('cbo_company_name','Company Name')==false) { return; }
		
  		var cbo_company_name = $("#cbo_company_name").val();
  		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/finish_trims_purchase_requisition_controller.php?cbo_company_name='+cbo_company_name+'&action=purchase_requisition_popup', 'Purchase Requisition Search', 'width=920px,height=400px,center=1,resize=0,scrolling=0','')
  		emailwindow.onclose=function()
  		{
  			var theemail=this.contentDoc.getElementById("selected_job");
  			if (theemail.value!="")
  			{
  				freeze_window(5);
  				get_php_form_data(theemail.value, "load_php_requ_popup_to_form","requires/finish_trims_purchase_requisition_controller" );
  				show_list_view(theemail.value,'purchase_requisition_list_view_dtls','purchase_requisition_list_view_dtls','requires/finish_trims_purchase_requisition_controller','setFilterGrid("list_view",-1)');
  				disable_enable_fields('cbo_company_name*cbo_location_name*cbo_pay_mode',1);
  				set_button_status(1, permission, 'fnc_purchase_requisition',1,0);
  				release_freezing();
  			}
  		}
	}
	
	/*function openmypage_manual_requisition()
	{
		var cbo_company_name = $("#cbo_company_name").val();
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/finish_trims_purchase_requisition_controller.php?cbo_company_name='+cbo_company_name+'&action=purchase_manual_requisition_popup', 'Purchase Manual Requisition Search', 'width=900px,height=400px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("txt_manual_req");
			if (theemail.value!="")
			{
				//freeze_window(5);
				document.getElementById('txt_manual_req').value = theemail.value;
				//release_freezing();
			}
		}
	}*/
			
	function fnc_purchase_requisition( operation )
	{
		var is_approved=$('#is_approved').val();
		
		if(is_approved==1 || is_approved==3)
		{
			alert("Requisition is Approved. So Change Not Allowed");
			return;	
		}
		
		if (form_validation('cbo_company_name*cbo_location_name*txt_req_date*txt_date_delivery','Company Name*Location*Requisation Date*Delivery Date')==false)
		{
			return;
		}
			
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_requisition_no*cbo_company_name*cbo_location_name*txt_req_date*cbo_pay_mode*cbo_source*txt_manual_req*cbo_currency_name*txt_date_delivery*txt_req_by*txt_remarks*cbo_template_id*update_id', '../../');
		/*-------------additional code-----------*/
		/*var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_requisition_no*cbo_company_name*cbo_item_category_id*cbo_location_name*cbo_division_name*cbo_department_name*cbo_section_name*txt_req_date*cbo_store_name*cbo_pay_mode*cbo_source*cbo_currency_name*txt_date_delivery*txt_remarks*txt_manual_req*update_id*txt_brand*txt_model_name*cbo_origin*txt_req_by',"../../");*/
		
		freeze_window(operation);
		http.open("POST","requires/finish_trims_purchase_requisition_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_purchase_requisition_response;
	}

	function fnc_purchase_requisition_response()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var response=trim(http.responseText).split('**');
			// if (response[0].length>2) response[0]=10;
			
			show_msg(response[0]);
			if( response[0]==0 ||  response[0]==1)
			{
				document.getElementById('txt_requisition_no').value = response[1];
				document.getElementById('update_id').value = response[2];
				disable_enable_fields('cbo_company_name*cbo_store_name*cbo_location_name',1);
				set_button_status(1, permission, 'fnc_purchase_requisition',1,0);
			}
			else if( response[0]==2)
			{
				reset_form('purchaserequisition_1*purchaserequisition_2','item_category_div*purchase_requisition_list_view_dtls','','','disable_enable_fields(\'cbo_company_name\');$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();', 'cboItemCategory_1')
				/*------write new code below if necerssary-------*/
			}
			else if(response[0]==15) 
			{ 
				 setTimeout('fnc_purchase_requisition( 0 )',8000); 
			}
			else if(response[0]==11) 
			{ 
				 alert(response[1]);
			}
			
			/*if(document.getElementById('cbo_ready_to_approved').value==1){
				var returnValue=return_global_ajax_value(response[2], 'pending_purchase_requisition_for_approval', '', '../../auto_mail/pending_purchase_requisition_for_approval_mail_notification');
			}*/
			
			release_freezing();
		}
	}


	function openmypage()
	{
		var txt_requisition_no=$('#txt_requisition_no').val();
		var cbo_store_name=$('#cbo_store_name').val();
		if(txt_requisition_no=="")
		{
			alert("Save Data First");return;
		}
		if (form_validation('cbo_company_name*cbo_store_name','Company Name*Item Catagory*Store Name')==false)
		 {
			 return;
		 }
		 else
		 {
			 var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_store_name').value;
			 var page_link='requires/finish_trims_purchase_requisition_controller.php?action=account_order_popup&data='+data
			 var title='Search Item Account';
			 emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1280px,height=400px,center=1,resize=0,scrolling=0','')
				
			 emailwindow.onclose=function()
			 {
			 	var theemail=this.contentDoc.getElementById("item_1").value;
				var re_order_lebel=this.contentDoc.getElementById("re_order_lebel").value;
				//alert(theemail); 
				if(theemail!="")
				{
					var tot_row = $('#tbl_purchase_item tbody tr').length;
					var array = JSON.parse("[" + theemail + "]");
					var row_num=tot_row;
					var order_no=$('#itemaccount_'+row_num).val();
					//alert(order_no);
					if(order_no=="")
					{
						//alert(order_no);
						$("#tbl_purchase_item tbody tr:last").remove();
					}
					tot_row = $('#tbl_purchase_item tbody tr').length;
					for(var cnt=0;cnt<array.length;cnt++)
					{
						var row=Number(Number(tot_row)+Number(cnt));
						var data=array[cnt]+"**"+row+"**"+cbo_store_name+"**"+re_order_lebel;	
						var list_view_orders = return_global_ajax_value( data, 'load_php_popup_to_form', '', 'requires/finish_trims_purchase_requisition_controller');
						$("#tbl_purchase_item tbody:last").append(list_view_orders);
					}
					set_all_onclick();
					release_freezing();
				}
			}
		}
	}

	function calculate_val()
	{
		var tot_row=$('#tbl_purchase_item'+' tbody tr').length;  
		for(var i=1; i<=tot_row; i++)
		{
			var quantity_val=parseFloat(Number($('#quantity_'+i).val()));
			var rate_val=parseFloat(Number($('#rate_'+i).val()));
			var attached_val=quantity_val*rate_val;
			document.getElementById('amount_'+i).value = number_format (attached_val, 2,'.',"");
		}
	}

	function fnc_purchase_requisition_dtls( operation )
	{
		if (form_validation('update_id*txtitemgroup_1*quantity_1','Master Table*Item Group*Quantity')==false) { return; }
		
		var tot_row=$('#tbl_purchase_item'+' tbody tr').length;
		var update_id=document.getElementById('update_id').value;
		var cbo_pay_mode=document.getElementById('cbo_pay_mode').value;
		var dtls_id=document.getElementById('hdnDtlsId_1').value;
		var data="action=save_update_delete_trim_cost_dtls&operation="+operation+"&tot_row="+tot_row+"&update_id="+update_id+"&cbo_pay_mode="+cbo_pay_mode+"&dtls_id="+dtls_id;
		//var update_id=document.getElementById('update_id').value;
		var data1='';
		
		for(var i=1; i<=tot_row; i++)
		{
			if(trim($("#quantity_"+i).val())!="")
			{
				data1+=get_submitted_data_string('txtitemgroup_'+i+'*itemdescription_'+i+'*itemColor_'+i+'*itemsize_'+i+'*cboUom_'+i+'*quantity_'+i+'*rate_'+i+'*amount_'+i+'*txtRemarks_'+i+'*cbostatus_'+i+'*hdnItemGroupId_'+i+'*hdnItemColorId_'+i+'*hdnItemSizeId_'+i,"../../",i);
			}
		}
	    data=data+data1;
	    //alert (data1);//return;

	    freeze_window(operation);
	    http.open("POST","requires/finish_trims_purchase_requisition_controller.php",true);
	    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    http.send(data);
	    http.onreadystatechange = fnc_purchase_requisition_dtls_response;
	}

	function fnc_purchase_requisition_dtls_response()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			if( response[0]==11)
			{
				alert(response[1]);release_freezing();return;
			}
			if( response[0]==0 ||  response[0]==1 ||  response[0]==2)
			{
				show_list_view(response[1],'purchase_requisition_list_view_dtls','purchase_requisition_list_view_dtls','requires/finish_trims_purchase_requisition_controller','setFilterGrid("list_view",-1)');
				reset_form('purchaserequisition_2','','','','$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();', 'cboItemCategory_1');
				set_button_status(0, permission, 'fnc_purchase_requisition_dtls', 2);
			}
			release_freezing();
		}
	}

	function openmypage_unapprove_request()
	{
		if (form_validation('txt_requisition_no','Req. Number')==false)
		{
			return;
		}
		
		var txt_requisition_no=document.getElementById('txt_requisition_no').value;
		var txt_un_appv_request=document.getElementById('txt_un_appv_request').value;
		
		var data=txt_requisition_no+"_"+txt_un_appv_request;
		
		var title = 'Un Approval Request';	
		var page_link = 'requires/finish_trims_purchase_requisition_controller.php?data='+data+'&action=unapp_request_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=500px,height=250px,center=1,resize=1,scrolling=0','../../');
		
		emailwindow.onclose=function()
		{
			var unappv_request=this.contentDoc.getElementById("hidden_appv_cause");
			
			$('#txt_un_appv_request').val(unappv_request.value);
		}
	}

	function openmypage_not_approve_cause()
	{
		if (form_validation('txt_requisition_no','Req. Number')==false)
		{
			return;
		}
		
		var txt_not_approve_cause=document.getElementById('txt_not_approve_cause').value;
		
		
		var data=txt_not_approve_cause;
		
		var title = 'Not Appv. Cause';	
		var page_link = 'requires/finish_trims_purchase_requisition_controller.php?data='+data+'&action=not_approve_cause_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=250px,center=1,resize=1,scrolling=0','../../');
		
		emailwindow.onclose=function()
		{
			
		}
	}

	function set_trim_cons_uom(trim_group_id,i) {
		var trim_rate_variable=document.getElementById('trim_rate_variable').value;
	}

	function add_break_down_tr_trim_cost( i )
	{
		var row_num=$('#tbl_purchase_item tr').length-1;
		if (i==0)
		{
			i=1;
			$("#itemColor_"+i).autocomplete({
				source: str_color
			});
			$("#itemsize_"+i).autocomplete({
				source:  str_size
			});
		   $("#itemdescription_"+i).autocomplete({
				source: str_trimdescription
			});
		  	return;
		}
		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;
			$("#tbl_purchase_item tr:last").clone().find("input,select").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }
				});

			}).end().appendTo("#tbl_purchase_item");

			var uom_id=$('#cboUom_'+Number(i-1)).val();
			$('#cboUom_'+i).val(uom_id);

			$('#txtitemgroup_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_itemgroup("+i+")");
			$('#increasetrim_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr_trim_cost("+i+");");
			$('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+",'tbl_purchase_item');");

			/*$('#txtitemgroup_'+i).val("");
			$('#itemdescription_'+i).val("");
			$('#itemColor_'+i).val("");
			$('#itemsize_'+i).val("");
			$('#quantity_'+i).val("");
			$('#rate_'+i).val("");
			$('#amount_'+i).val("");
			$('#txtRemarks_'+i).val("");
			$('#hdnItemGroupId_'+i).val("");
			$('#hdnItemColorId_'+i).val("");
			$('#hdnItemSizeId_'+i).val("");
			$('#hdnDtlsId_'+i).val("");
			$('#cboUom_'+i).val(0);*/

			$("#itemdescription_"+i).autocomplete({
				source: str_trimdescription
			});
		}
	}

	function fn_deletebreak_down_tr(rowNo,table_id)
	{
		var r=confirm("Do you want to delete this row?.\n If yes press OK \n or press Cancel." );
		if(r==false) { return; }

		if(table_id =='tbl_purchase_item')
		{
			var numRow = $('table#tbl_purchase_item tbody tr').length-1;

			if(numRow!=1)
			{
				var permission_array=permission.split("_");
				
				var index=rowNo-1;
				$("table#tbl_purchase_item tbody tr:eq("+index+")").remove();
				var numRow = $('table#tbl_purchase_item tbody tr').length;
				for(i = rowNo;i <= numRow;i++)
				{
					$("#tbl_purchase_item tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							'value': function(_, value) { return value }
						});
						$('#txtitemgroup_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_itemgroup("+i+")");
					})
				}
			}
		}
	}

	function generate_report(type) {
		if (type==1) {
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_template_id').val()+'*'+$('#cbo_location_name').val(), "generate_report_1", "requires/finish_trims_purchase_requisition_controller" ) ;
			//return;
			show_msg("3");
		}
	}

	function openpopup_itemgroup(i)
	{

		var page_link="requires/finish_trims_purchase_requisition_controller.php?action=openpopup_itemgroup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Item Group Select', 'width=450px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			//var id=this.contentDoc.getElementById("gid");
			var itemdata=this.contentDoc.getElementById("itemdata").value;
			var row_count=$('#tbl_purchase_item tr').length-1;
			var itemdata=itemdata.split(",");
			var a=0; var n=0;
			for(var b=1; b<=itemdata.length; b++)
			{
				// console.log(itemdata[a]);
				var exdata="";
				var exdata=itemdata[a].split("***");
				if(a==0)
				{
					document.getElementById('hdnItemGroupId_'+i).value=exdata[0];
					document.getElementById('txtitemgroup_'+i).value=exdata[1];
					document.getElementById('cboUom_'+i).value=exdata[2];
				}
				else
				{
					add_break_down_tr_trim_cost(row_count);
					n++;
					row_count++;
					document.getElementById('hdnItemGroupId_'+row_count).value=exdata[0];
					document.getElementById('txtitemgroup_'+row_count).value=exdata[1];
					document.getElementById('cboUom_'+row_count).value=exdata[2];
				}
				a++;
			}
		}
	}

	function fn_reset_form(type) {
		if(type == 1) {
			reset_form('purchaserequisition_1','','','','', 'cboItemCategory_1');			
			document.getElementById('txt_req_date').value = "<?php echo date("d-m-Y");?>";
			document.getElementById('cbo_source').value = 3;
			document.getElementById('cbo_currency_name').value = 1;
			document.getElementById('cbo_company_name').removeAttribute('disabled');
			document.getElementById('cbo_location_name').removeAttribute('disabled');
			document.getElementById('cbo_pay_mode').removeAttribute('disabled');
			return;
		}
		if(type == 2) {
			reset_form('purchaserequisition_2','purchase_requisition_list_view_dtls*approved', '', '', 'disable_enable_fields(\'cbo_company_name\');$(\'#tbl_purchase_item tbody tr:not(:first)\').remove();', 'cboItemCategory_1')
		}
	}

</script>
</head>
<body>
<div style="width:100%;" align="center">
 	<?php echo load_freeze_divs ("../../", $permission); ?>
    <fieldset style="width:900px;height:auto;">
    <legend>Purchase Requisition</legend> 
    <form name="purchaserequisition_1" id="purchaserequisition_1" autocomplete="off"> 
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td colspan="6" align="center" ><b>Requisition No</b>
                	<input type="hidden" name="update_id" id="update_id" value="">
                	<input type="hidden" name="is_approved" id="is_approved" value="">
                	<input name="txt_requisition_no"  id="txt_requisition_no" placeholder="Double Click to Search" onDblClick="openmypage_requisition();" readonly  style="width:158px "  class="text_boxes"/>
                </td>
            </tr>
	        <tr><td height="15"></td></tr>
	        <tr>
	            <td width="110" class="must_entry_caption">Company</td>
	            <td width="160">
	                
					<? 
						echo create_drop_down( "cbo_company_name", 160,"select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/finish_trims_purchase_requisition_controller', this.value, 'load_drop_down_location','location_td');" );
	                ?>
	            </td>
	            <td width="110" class="must_entry_caption">Location</td>
	            <td id="location_td" width="160">
					<? 
						 echo create_drop_down( "cbo_location_name", 160,$blank_array,"", 1, "-- Select --", $selected, "" );
	                 ?> 	
	            </td>
	            <td class="must_entry_caption">Req. Date</td>
	            <td>
	                <input type="text" name="txt_req_date"  style="width:150px" id="txt_req_date" class="datepicker" value="<? echo date("d-m-Y");?>" readonly />
	            </td>
	        </tr>
	        <tr>
	            
	        </tr>
	        <tr>
	            <td>Pay Mode</td>
	            <td>
					<? 
						echo create_drop_down( "cbo_pay_mode", 160,$pay_mode,"", 0, "", $selected, "","" );
	                ?> 
	            </td>
	            <td>Source</td>
	            <td id="mode_td">
					 <? 
						 echo create_drop_down( "cbo_source", 160, $source,"", 0, "", 3, "","" );
	                 ?> 
	            </td>
	        	<td>Manual Req. No.</td>
	            <td>
	                <input type="text" name="txt_manual_req" id="txt_manual_req" style="width:150px" class="text_boxes" placeholder="Write" />
	            </td>
	        </tr>
	        <tr>
	            <td>Currency</td>
	            <td> 
					<?
						echo create_drop_down( "cbo_currency_name", 160,$currency,"", 1, "-- Select --", 1, "" );
	                ?> 
	            </td>
	            <td class="must_entry_caption">Delivery Date</td>
	            <td>
	                <input type="text" name="txt_date_delivery"  style="width:150px"  id="txt_date_delivery" class="datepicker" readonly />
	            </td>
	            <td>Req.By</td>
	            <td>
	                <input type="text" name="txt_req_by"  id="txt_req_by" style="width:150px " value="" class="text_boxes" maxlength="50" title="Maximum 50 Character"/>
	            </td>
	        </tr>
	        <tr>
	            <td>Remarks</td>
	            <td colspan="3">
	                <input type="text" name="txt_remarks"  id="txt_remarks" style="width:460px " value="" class="text_boxes" maxlength="400" title="Maximum 400 Character"/>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" class="button_container">
	            	<div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
					<?php echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>
					
					<?php 
						echo load_submit_buttons($permission, "fnc_purchase_requisition", 0,0,"fn_reset_form(1);",1);//item_category_div*purchase_requisition_list_view_dtls*approved
	                ?>
	                <input type="button" name="btnPrint" id="btnPrint" value="Print Report" onClick="generate_report(1)" style="width:100px;" class="formbuttonplasminus" />
	            </td>
	        </tr>
    	</table>
  	</form>
  	</fieldset>
	<fieldset style="width:915px; margin-top:10px;">
	    <legend>Purchase Requisition Details</legend>
	    <form name="purchaserequisition_2" id="purchaserequisition_2" autocomplete="off">
	    	<table class="rpt_table" width="100%" cellspacing="1" id="tbl_purchase_item">
	            <thead>
	            	<tr>
	                    <th width="90">Item Category</th>
	                    <th width="85" class="must_entry_caption">Item Group</th>
	                    <th width="130">Item Description</th>
	                    <th width="70">Item Color</th>
	                    <th width="70">Item Size</th>
	                    <th width="60">Order UOM</th>
	                    <th width="60" class="must_entry_caption" title="Must Entry Field.">Quantity</th>
	                    <th width="60">Rate</th>
	                    <th width="60">Amount</th>
	                    <th width="100">Remarks</th>
	                    <th width="60">Status</th>
	                    <th width="70">Action</th>
	                </tr>
	        	</thead>
	            <tbody>
	                <tr class="general" >
	                	<input type="hidden" id="updateidtrim_1" name="updateidtrim_1" />
	                    <td>
	                    	<input type="hidden" id="hdnItemGroupId_1" name="hdnItemGroupId_1">
	                    	<input type="hidden" id="hdnItemColorId_1" name="hdnItemColorId_1">
	                    	<input type="hidden" id="hdnItemSizeId_1" name="hdnItemSizeId_1">
	                    	<input type="hidden" id="hdnDtlsId_1" name="hdnDtlsId_1">
							<?php
								$item_cat = array(4=>'Accessories');
	                            echo create_drop_down( "cboItemCategory_1", 90, $item_cat, 1, 0, '', 4, '', 1, '', '', '', '');
	                        ?>
	                    </td>
	                    <td>
	                    	<input type="text" name="txtitemgroup_1" id="txtitemgroup_1" class="text_boxes" value="" style="width:85px;" ondblclick="openpopup_itemgroup(1)" maxlength="200" placeholder="Browse" readonly/>
	                    </td>
	                    <td>
	                        <input type="text" name="itemdescription_1" id="itemdescription_1" class="text_boxes" style="width:130px;" maxlength="200" />
	                    </td>
	                    <td id="color_td">
	                        <input type="text" name="itemColor_1" id="itemColor_1" class="text_boxes" style="width: 60px;" maxlength="200" />
	                    </td>
	                    <td id="group_td">
	                        <input type="text" name="itemsize_1" id="itemsize_1" class="text_boxes" style="width: 60px;" maxlength="200" />
	                        
	                    </td>
	                    <td id="tduom_1">
	                        <?php
	                        	echo create_drop_down( 'cboUom_1', 60, $unit_of_measurement, '', 0, '', '', '', 1, '' );
	                        ?>
	                    </td> 
	                    <td>
	                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" autocomplete="off" style="width:60px;" onKeyUp="calculate_val()"/>
	                    </td>
	                    <td>
	                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" autocomplete="off" style="width:60px;" onKeyUp="calculate_val()" />
	                    </td>
	                    <td>
	                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" autocomplete="off" style="width:60px; text-align:right;" readonly />
	                    </td>
	                    <td>
	                        <input type="text" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" style="width:100px;" />
	                    </td>
	                    <td> 
	                        <? echo create_drop_down( "cbostatus_1", 60, $row_status,'', 0, '',1,0); ?> 
	                    </td>
	                    <td style="display: inline-flex;">
	                    	<input type="button" id="increasetrim_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr_trim_cost(1)" <? if($approved==1 || $component_approved==1) {echo "disabled";} else {echo "";}?> <?// echo $txt_disabled; echo $txt_dis; ?> />
	                        <input type="button" id="decreasetrim_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(1 ,'tbl_purchase_item' );" <? echo $txt_disabled; echo $txt_dis; ?> />
	                    </td>
	                </tr>
	            </tbody>
	        </table>
            <table width="100%">
            	<tr>
                    <td colspan="19" height="20" valign="middle" align="center" class="button_container"> 
                        <?
                            echo load_submit_buttons( $permission, "fnc_purchase_requisition_dtls", 0,0 ,"fn_reset_form(2)",2) ;
                        ?>
                    </td>
                </tr>
            </table>
	    </form>
	</fieldset>
    
    <fieldset style="width:1100px; margin: 10px auto;">
        <legend>Purchase Requisition List</legend>
        <div id="purchase_requisition_list_view_dtls" overflow:auto;> </div>
   	</fieldset>
</div>
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>  
</html>
