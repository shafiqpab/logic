<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Buyer Inspection V2 Entry Page
				
Functionality	:	
JS Functions	:
Created by		:	Shafiq 
Creation date 	: 	14-09-2023
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
//$inpLevelArray = [1=>'In-line Inspection',2=>'Mid-line Inspection',3=>'Final Inspection'];
$inpLevelArray = array(1=>'In-line Inspection',2=>'Mid-line Inspection',3=>'Final Inspection',4=>'Pre-Final Inspection');
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Cutting Info","../", 1, 1, $unicode,'','');

?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	 
	function openmypage_order(page_link,title)
	{
			page_link=page_link+get_submitted_data_string('cbo_company_name*garments_nature','../');
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1153px,height=470px,center=1,resize=1,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];;
				var job=this.contentDoc.getElementById("selected_job");
				if (job.value!="")
				{
					freeze_window(5);
					document.getElementById('txt_job_no').value=job.value;
					get_php_form_data( job.value, "populate_order_data_from_search_popup", "requires/buyer_inspection_controller_v2" );
					//load_drop_down( 'requires/buyer_inspection_controller_v2', job.value, 'load_drop_down_po_number', 'order_drop_down_td' );
					// load_drop_down( 'requires/buyer_inspection_controller_v2', job.value, 'load_drop_down_week_no', 'week_drop_down_td' );
					// load_drop_down( 'requires/buyer_inspection_controller_v2', job.value, 'load_drop_down_country_id', 'country_drop_down_td' );
					
			        show_list_view(job.value,'show_active_listview','inspection_production_list_view','requires/buyer_inspection_controller_v2','setFilterGrid(\'tbl_list_search\',-1)');
					
					
				reset_form('','','cboorderid_1*cboorderval_1*actualorderid_1*txtpoquantity_1*txtpubshipmentdate_1*txtinspectionqnty_1*txtcuminspectionqnty_1*cboinspectionstatus_1*cbocause_1*txtcomments_1*txt_mst_id*cboweekno_1*cbocountryid_1*txtfinishingqnty_1*txtinsreason_1','','');
					
					release_freezing();
				}
			}
		
	}

	function open_set_popup(unit_id)
	{
		var txt_quotation_id=document.getElementById('txt_job_no').value;
		var set_breck_down=document.getElementById('set_breck_down').value;
		var tot_set_qnty=document.getElementById('tot_set_qnty').value;
		var page_link="requires/buyer_inspection_controller_v2.php?txt_quotation_id="+trim(txt_quotation_id)+"&action=open_set_list_view&set_breck_down="+set_breck_down+"&tot_set_qnty="+tot_set_qnty;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Set Details", 'width=400px,height=250px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var set_breck_down=this.contentDoc.getElementById("set_breck_down") //Access form field with id="emailfield"
			var item_id=this.contentDoc.getElementById("item_id") //Access form field with id="emailfield"
			var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty") //Access form field with id="emailfield"
			document.getElementById('set_breck_down').value=set_breck_down.value;
			document.getElementById('item_id').value=item_id.value;
			document.getElementById('tot_set_qnty').value=tot_set_qnty.value;

		}		
	}
	function openOrderPoup(row)
	{
		if (form_validation('txt_job_no','Job No')==false)
		{
			return;   
		}
		else
		{	
			var txt_job_no=document.getElementById('txt_job_no').value;
			 
			var page_link="requires/buyer_inspection_controller_v2.php?action=open_order_popup&txt_job_no="+txt_job_no+'&row='+row;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Order Popup", 'width=600px,height=350px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				 var hidden_order_val=this.contentDoc.getElementById("hidden_order_val").value ;
				 var hidden_order_id=this.contentDoc.getElementById("hidden_order_id").value ;
				 var hidden_actual_order_id=this.contentDoc.getElementById("hidden_actual_order_id").value ;
				//  alert(hidden_order_id+'='+hidden_order_val);
				 if(hidden_order_id)
				 {	

				 	$("#cboorderval_"+row).val(hidden_order_val);
				 	$("#cboorderid_"+row).val(hidden_order_id);
				 	// $("#cboactualorderid_1").val(hidden_actual_order_id);
				 	load_drop_down( 'requires/buyer_inspection_controller_v2', '__'+hidden_order_id+'__'+row, 'load_drop_down_week_no', 'week_drop_down_td_'+row );
				 	load_drop_down( 'requires/buyer_inspection_controller_v2', '__'+hidden_order_id+'____'+row, 'load_drop_down_country_id', 'country_drop_down_td_'+row );
				 	get_php_form_data( hidden_order_id+','+document.getElementById('cboweekno_'+row).value+','+document.getElementById('cbocountryid_'+row).value+','+document.getElementById('cbo_inspection_level').value+','+document.getElementById('cbo_company_name').value+','+row, 'set_po_qnty_ship_date', 'requires/buyer_inspection_controller_v2');
				 }			 

			}		
		}

	}


	function openActualPoPoup()
	{
		if (form_validation('cboorderval_1','PO No')==false)
		{
			return;   
		}
		else
		{	
			var txt_po_id=document.getElementById('cboorderid_1').value;
			 
			var page_link="requires/buyer_inspection_controller_v2.php?action=open_actual_order_popup&txt_po_no="+txt_po_id;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Order Popup", 'width=600px,height=350px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				 var hidden_actual_order_no=this.contentDoc.getElementById("hidden_actual_order_no").value ;
				 var hidden_actualorderid_1=this.contentDoc.getElementById("hidden_actualorderid_1").value ;
				 
				 if(hidden_actualorderid_1)
				 {	
				 	$("#actualorderid_1").val(hidden_actual_order_no);
				 	$("#cboactualorderid_1").val(hidden_actualorderid_1);				 	 
				 }
				 

			}		
		}

	}

	function openInspPoup(rID)
	{	
		// alert('ok');return;		
		if (form_validation('txt_job_no*cboorderid_1*txtfinishingqnty_1','Job No*PO number*Qty')==false)
		{
			return;   
		}
		else
		{	
			var txt_job_no=document.getElementById('txt_job_no').value;
			var hiddeninsdata_1=$("#hiddeninsdata_"+rID).val();
			var cboorderid_1=document.getElementById('cboorderid_'+rID).value;
			var cbocountryid_1=document.getElementById('cbocountryid_'+rID).value;
			var garments_nature=document.getElementById('garments_nature').value;
			var company_name=document.getElementById('cbo_company_name').value;
			var inspection_level=document.getElementById('cbo_inspection_level').value;
			var inspection_status=document.getElementById('cboinspectionstatus_'+rID).value;

			var data = txt_job_no+"_____"+cboorderid_1+"_____"+cbocountryid_1+"_____"+hiddeninsdata_1+"_____"+garments_nature+"_____"+company_name+"_____"+inspection_level+"_____"+inspection_status+"_____"+rID;
			// alert(data);
			var page_link="requires/buyer_inspection_controller_v2.php?action=open_insp_qty_popup&data="+data;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, "Inspection Quantity Popup", 'width=600px,height=350px,center=1,resize=1,scrolling=0','../')
			emailwindow.onclose=function()
			{
				 var hidden_all_data=this.contentDoc.getElementById("hidden_all_data").value ;
				 var hidden_qnty_data=this.contentDoc.getElementById("hidden_qnty_data").value ;
				 $("#cbocountryid_"+rID).attr("disabled",false);
				 if(hidden_qnty_data)
				 {
				 	$("#cbocountryid_"+rID).attr("disabled",true);
				 	$("#txtinspectionqnty_"+rID).val(hidden_qnty_data);
				 	$("#hiddeninsdata_"+rID).val(hidden_all_data);
				 }
				// var item_id=this.contentDoc.getElementById("item_id") //Access form field with id="emailfield"
				// var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty") //Access form field with id="emailfield"
				// document.getElementById('set_breck_down').value=set_breck_down.value;
				// document.getElementById('item_id').value=item_id.value;
				// document.getElementById('tot_set_qnty').value=tot_set_qnty.value;

			}		
		}
	}

	 
	function fnc_buyer_inspection_entry( operation )
	{
		/* if('<?php //echo implode('*',$_SESSION['logic_erp']['mandatory_field'][470]);?>'){
			if (form_validation('<?php //echo implode('*',$_SESSION['logic_erp']['mandatory_field'][470]);?>','<?php //echo implode('*',$_SESSION['logic_erp']['field_message'][470]);?>')==false)
			{
				
				return;
			}
		} */

		if (form_validation('txt_job_no*cbo_source*cbo_inspection_company*txt_inp_date*cbo_inspection_by','Job No*Source*Insp. Company*Inspection Date*Inspection By')==false)
		{
			return;   
		}	
		else
		{
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_inp_date').val(), current_date)==false)
			{
				alert("Inspection Date Can not Be Greater Than Current Date");
				return;
			}	
			
			var row_num=$('#tbl_list tbody tr').length;
			var data1="action=save_update_delete&operation="+operation+"&row_num="+row_num+get_submitted_data_string('txt_job_no*cbo_inspection_company*txt_inp_date*cbo_source*cbo_working_company*cbo_working_location*cbo_working_floor*cbo_inspection_level*txt_mst_id*cbo_inspection_by*cbo_company_name',"../");

			
			var data2='';
			for(var k=1; k<=row_num; k++)
			{				
				if(form_validation('cboorderval_'+k+'*cbocountryid_'+k+'*txtfinishingqnty_'+k+'*txtinspectionqnty_'+k+'*cboinspectionstatus_'+k,'Order No*Country*Finishing Qty*Inspec. Qty*Inspec. Status')==false)
				{
					return;
				}	

				var insfaction=document.getElementById('cboinspectionstatus_'+k).value;
				if(insfaction!=1)
				{
					if (form_validation('cbocause_'+k,'Inspection Cause')==false)
					{
						return;
					}
				}			
				
				data2 += '&cboorderval_' + k + '=' + $('#cboorderval_' + k).val() + '&actualorderid_' + k + '=' + $('#actualorderid_' + k).val() + '&cboweekno_' + k + '=' + $('#cboweekno_' + k).val() + '&cbocountryid_' + k + '=' + $('#cbocountryid_' + k).val() + '&txtpubshipmentdate_' + k + '=' + $('#txtpubshipmentdate_' + k).val() + '&txtfinishingqnty_' + k + '=' + $('#txtfinishingqnty_' + k).val() + '&txtinspectionqnty_' + k + '=' + $('#txtinspectionqnty_' + k).val() + '&cboinspectionstatus_' + k + '=' + $('#cboinspectionstatus_' + k).val() + '&cbocause_' + k + '=' + $('#cbocause_' + k).val() + '&txtinsreason_' + k + '=' + $('#txtinsreason_' + k).val() + '&txtcomments_' + k + '=' + $('#txtcomments_' + k).val() + '&cboorderid_' + k + '=' + $('#cboorderid_' + k).val() + '&cboactualorderid_' + k + '=' + $('#cboactualorderid_' + k).val() + '&hiddeninsdata_' + k + '=' + $('#hiddeninsdata_' + k).val();
			}
			var data=data1+data2;
			// alert(data);return;

			freeze_window(operation);
			http.open("POST","requires/buyer_inspection_controller_v2.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_buyer_inspection_entry_reponse;
		}
	}
		 
	function fnc_buyer_inspection_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			
			if(reponse[0]==25)
			{
				$("#txtinspectionqnty_1").val("");
				show_msg('29');
			}
			else if(reponse[0]==35)
			{
				alert(reponse[1]);
				$("#txtinspectionqnty_1").val("");
				$("#txtinspectionqnty_1").focus();
				show_msg('29');
				release_freezing();return;
			}
			else if(reponse[0]==10)
			{
				release_freezing();return;
			}
			else
			{
				show_msg(trim(reponse[0]));
				set_button_status(0, permission, 'fnc_buyer_inspection_entry',1); 
				show_list_view(document.getElementById('txt_job_no').value,'show_active_listview','inspection_production_list_view','requires/buyer_inspection_controller_v2','setFilterGrid(\'tbl_list_search\',-1)');
				reset_form('','','cboorderid_1*cboorderval_1*cboactualorderid_1*actualorderid_1*txtpoquantity_1*hiddeninsdata_1*txtpubshipmentdate_1*txtfinishingqnty_1*txtinspectionqnty_1*txtcuminspectionqnty_1*txtinsreason_1*cboinspectionstatus_1*cbocause_1*txtcomments_1*txt_mst_id*cboweekno_1*cbocountryid_1','','');
			}
			release_freezing();
		}
	}
	function openmypage_image(job)
	{
		//alert(job);//var issue_num=$('#sysid_'+i).val();
		//var dev_id=$('#updateid_'+i).val();
		//alert(issue_num);
		var page_link='requires/buyer_inspection_controller_v2.php?action=show_image&job='+job; 
		var title="Image View";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=0,scrolling=0','../')
	}

	function fn_qnty_check()
	{
		var txt_user_lebel=$('#txt_user_lebel').val();
		var variable_is_controll=$('#variable_is_controll').val();
		
		if(variable_is_controll==1 && txt_user_lebel!=2)
		{
			if(($('#txtinspectionqnty_1').val()*1)>($('#txtfinishingqnty_1').val()*1))
			{
				alert("Inspection Not Over Finishing Quantity");
				$('#txtinspectionqnty_1').val("");
				$('#txtinspectionqnty_1').focus();
				return;
			}
		}
		
	}
	function changeInspactionLevel(insLevel)
	{ 
		let fieldName = (insLevel==1) ?'Sewing Qty': 'Finishing Qty'; 
		$('#qtyField').html(fieldName);
		
		let hidden_order_id = $('#cboorderid_1').val();
		if (hidden_order_id) 
		{
			get_php_form_data( hidden_order_id+','+document.getElementById('cboweekno_1').value+','+document.getElementById('cbocountryid_1').value+','+insLevel+','+document.getElementById('cbo_company_name').value+','+1, 'set_po_qnty_ship_date', 'requires/buyer_inspection_controller_v2');
		}
	}

	function add_break_down_tr( i,tr )
	{
		var row_num=$('#tbl_list tbody tr').length;
		
		if (form_validation('cboorderval_'+row_num+'*txtpoquantity_'+row_num+'*txtfinishingqnty_'+row_num+'*cboinspectionstatus_'+row_num,'Order NO*Order Qty*Finishing Qty*Inspection Status')==false)
		{
			return;
		}
		var j=i;
		var index = $(tr).closest("tr").index();
		// alert(index);return;
		var i=row_num;
		i++;
		var tr=$("#tbl_list tbody tr:eq("+index+")");
		//alert(tr)
		var cl=$("#tbl_list tbody tr:eq("+index+")").clone().find("input,select").each(function() 
		{
			$(this).attr({
			'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },
			'value': function(_, value) { return value }              
			});
		}).end();
		tr.after(cl);
		
		
		$("#tbl_list tbody tr:last").removeAttr('id').attr('id','tr_'+i);

		$("#tbl_list tbody tr:last td:nth-child(1)").removeAttr('id').attr('id','order_drop_down_td_'+i);
		$("#tbl_list tbody tr:last td:nth-child(2)").removeAttr('id').attr('id','acc_order_drop_down_td_'+i);
		$("#tbl_list tbody tr:last td:nth-child(3)").removeAttr('id').attr('id','week_drop_down_td_'+i);
		$("#tbl_list tbody tr:last td:nth-child(4)").removeAttr('id').attr('id','country_drop_down_td_'+i);

		$('#cboorderval_'+i).removeAttr("ondblclick").attr("ondblclick","openOrderPoup("+i+");");
		$('#txtinspectionqnty_'+i).removeAttr("ondblclick").attr("ondblclick","openInspPoup("+i+");");
		
		$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+",this);");
		$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_break_down_tr("+i+",this);");
		
		$('#cboorderval_'+i).val('');
		$('#cboorderid_'+i).val('');
		$('#cboactualorderid_'+i).val('');
		$('#actualorderid_'+i).val('');
		$('#cboweekno_'+i).val('');
		$('#cbocountryid_'+i).val('');
		$('#txtpoquantity_'+i).val('');
		$('#txtpubshipmentdate_'+i).val('');
		$('#txtfinishingqnty_'+i).val('');
		$('#hiddeninsdata_'+i).val('');
		$('#txtinspectionqnty_'+i).val('');
		$('#txtcuminspectionqnty_'+i).val('');
		$('#cboinspectionstatus_'+i).val('');
		$('#cbocause_'+i).val('');
		$('#txtinsreason_'+i).val('');
		$('#txtcomments_'+i).val('');

		set_all_onclick();
	}

	function fn_delete_break_down_tr(rowNo,tr) 
	{
		var numRow = $('table#tbl_list tbody tr').length; 
		// $("#dist_qty").val('');
		// alert(`${rowNo} and ${tr}`);
		if(rowNo==1 && numRow >1)
		{
			var index = $(tr).closest("tr").index();
			$("table#tbl_list tbody tr:eq("+index+")").remove()
			var numRow = $('table#tbl_list tbody tr').length; 
			for(i = rowNo;i <= numRow;i++)
			{
				$("#tbl_list tr:eq("+i+")").find("input,select").each(function() 
				{
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
						'value': function(_, value) { return value }             
					}); 
					$('#cboorderval_'+i).removeAttr("ondblclick").attr("ondblclick","openOrderPoup("+i+");");
		
					$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+",this);");
					$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_break_down_tr("+i+",this);");

					$("#tbl_list tbody tr:last").removeAttr('id').attr('id','tr_'+i);
					$("#tbl_list tbody tr:last td:nth-child(1)").removeAttr('id').attr('id','order_drop_down_td_'+i);
					$("#tbl_list tbody tr:last td:nth-child(2)").removeAttr('id').attr('id','acc_order_drop_down_td_'+i);
					$("#tbl_list tbody tr:last td:nth-child(3)").removeAttr('id').attr('id','week_drop_down_td_'+i);
					$("#tbl_list tbody tr:last td:nth-child(4)").removeAttr('id').attr('id','country_drop_down_td_'+i);
				})
			}
			// fn_sum_qty();
		}
		if(rowNo!=1)
		{				
			var index = $(tr).closest("tr").index();
			$("table#tbl_list tbody tr:eq("+index+")").remove()
			var numRow = $('table#tbl_list tbody tr').length; 
			for(i = rowNo;i <= numRow;i++)
			{
				$("#tbl_list tr:eq("+i+")").find("input,select").each(function() 
				{
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
						'value': function(_, value) { return value }             
					}); 
					$('#cboorderval_'+i).removeAttr("ondblclick").attr("ondblclick","openOrderPoup("+i+");");
		
					$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+",this);");
					$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_break_down_tr("+i+",this);");

					$("#tbl_list tbody tr:last").removeAttr('id').attr('id','tr_'+i);
					$("#tbl_list tbody tr:last td:nth-child(1)").removeAttr('id').attr('id','order_drop_down_td_'+i);
					$("#tbl_list tbody tr:last td:nth-child(2)").removeAttr('id').attr('id','acc_order_drop_down_td_'+i);
					$("#tbl_list tbody tr:last td:nth-child(3)").removeAttr('id').attr('id','week_drop_down_td_'+i);
					$("#tbl_list tbody tr:last td:nth-child(4)").removeAttr('id').attr('id','country_drop_down_td_'+i);
				})
			}
			// fn_sum_qty()
		}
		/* let dist_type = $("#cbo_distribution").val();
		if(dist_type==1)
		{
			$("#tbl_list").find('tbody tr').each(function()
			{
				$(this).find('input[name="qty[]"]').val('');
			});
		} */
		let dis_qty = $("#dist_qty").val();
		fn_distribute_qty(dis_qty);
	}

</script>
</head>
<body onLoad="set_hotkey()">

    <div style="width:100%;" align="center">
        <div style="width:850px;" align="center">
             <? echo load_freeze_divs ("../",$permission);  ?>
        </div>
         
        <fieldset style="width:1120px"> 
        <legend>Production Module</legend>
        <form name="inspectionentry_1" id="inspectionentry_1" action=""  autocomplete="off">
                <fieldset>
                    <table width="100%">
                         <tr>
                         	<td width="130" class="must_entry_caption" align="right">Job No</td>
                                <td width="170" >
									<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:200px" placeholder="Double Click to Search" onDblClick="openmypage_order('requires/buyer_inspection_controller_v2.php?action=order_search_popup','Order Search')"/>
                                </td>
                                <td width="130" class="must_entry_caption" align="right">Company </td>
                                <td width="">
                                	<?
										echo create_drop_down( "cbo_company_name", 210, "select id,company_name from lib_company where is_deleted=0 and status_active=1 order by company_name","id,company_name", 1, "-- Select Company --", $selected,"", 1,0 );	
									?> 
										<input type="hidden" id="sewing_production_variable" />
										<input type="hidden" id="styleOrOrderWisw" />
                                    	<input type="hidden" id="variable_is_controll" />
                                		<input type="hidden" id="txt_user_lebel" value="<? echo $_SESSION['logic_erp']['user_level']; ?>" />                                  		
				                        <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl" value="0">
				                        <input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process" value="0">
                                	</td>

                                <td width="130" class="must_entry_caption" align="right">Source </td>
                                <td width="">
                                	<?
                    					echo create_drop_down( "cbo_source", 210, $knitting_source,"", 0, "-- Select Source --", $selected, "load_drop_down( 'requires/buyer_inspection_controller_v2', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_working_company', 'working_company_td' );", 0, '1,3' );
                    				?> 
                                </td>
                          </tr>
                          <tr>  
                                <td width="130" align="right">Working Company</td>
                                <td width="170" id="working_company_td">
									<?
			                       		echo create_drop_down( "cbo_working_company", 210, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Working Company --", '', "load_drop_down( 'requires/buyer_inspection_controller_v2', this.value, 'load_drop_down_working_location', 'working_location_td' );",0 ); 
								   ?> 
                                 </td>
                                <td width="130" align="right">Location</td>
                                <td width="170" id="working_location_td">
                                    <? echo create_drop_down( "cbo_working_location", 210, $blank_array,"", 1, "-- Select Working Location --", $selected, "",1,0 );?>
                                </td>  
                                 <td width="130" align="right">Floor/Unit</td>
                                 <td width="170" id="working_floor_td">
                                 	<? echo create_drop_down( "cbo_working_floor", 210, $blank_array,"", 1, "-- Select Working Floor --", $selected, "",1,0 );?>
                                 </td>
                          </tr>
                          <tr>  
                                <td width="130" align="right">Buyer Name</td>
                                <td width="170" id="buyer_td">
									<?
                                        echo create_drop_down( "cbo_buyer_name", 210, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1","id,buyer_name", 1, "-- Select Buyer --", $selected, "",1,0 );	
                                    ?> 
                                 </td>
                                <td width="130" align="right">Style No</td>
                                <td width="170">
                                    <input name="txt_style_no"   id="txt_style_no" class="text_boxes" style="width:200px " disabled readonly />
                                </td>  
                                 <td width="130" align="right">Style Des</td>
                                 <td width="170">
                                 	<input type="text" name="txt_style_des" id="txt_style_des" class="text_boxes" style="width:200px" disabled readonly />
                                 </td>
                          </tr>
                          <tr>  
                                 <td width="130" align="right">Job Qnty</td>
                                 <td width="170">
                                 	<input name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric"  style="width:200px" disabled readonly  />
                                 </td>
                                 
                                 <td width="130" align="right">Order UOM </td> 
                                 <td width="170">
                                 <? 
                                 echo create_drop_down( "cbo_order_uom",60, $unit_of_measurement, "",0, "", 1, "change_caption_cost_dtls( this.value, 'change_caption_pcs' )",1,"1,58" );
                                 ?>
                                 <input type="button" id="set_button" class="image_uploader"  value="Item Details" onClick="open_set_popup(document.getElementById('cbo_order_uom').value)" />
                                 <input type="button" class="image_uploader"  value="Image" onClick="openmypage_image(document.getElementById('txt_job_no').value)" />
                                 <input type="hidden" id="set_breck_down" />     
                                 <input type="hidden" id="item_id" /> 
                                 <input type="hidden" id="tot_set_qnty" />    
                                 </td>
                                 <td width="130" class="must_entry_caption" align="right">Inspected By</td>
                                 <td width="170">
									 <? 
										echo create_drop_down( "cbo_inspection_by", 210, $inspected_by_arr,"", 1, "--- Select ---", $selected, "load_drop_down( 'requires/buyer_inspection_controller_v2', this.value+','+document.getElementById('cbo_buyer_name').value+','+document.getElementById('cbo_company_name').value, 'load_drop_down_buyer_party_company', 'cutt_company_td' );" );     	 
                                     ?> 
                                 </td>
                          </tr>
                          <tr> 
                             
                                 <td width="130" class="must_entry_caption" align="right">Inp. Company</td>
                                 <td width="170" id="cutt_company_td">
									 <? //echo "select distinct a.id, a.supplier_name from lib_supplier a,lib_supplier_party_type b  where a.id=b.supplier_id and  b.party_type=41 and a.status_active=1 and a.is_deleted=0 order by a.supplier_name";
										echo create_drop_down( "cbo_inspection_company", 210, $blank_array,"", 1, "--- Select ---", $selected, "",1,0 );     	 
                                     ?> 
                                 </td>
                             
                             <!--<td width="130" class="must_entry_caption" align="right">Week No</td>
                             <td width="170">
                             	<?
									//echo create_drop_down( "cboweekno_1", 210, $yes_no,"", 1, "--- Select ---", $selected, "" );     	 
								?>
                             </td>
                             <td width="130" class="must_entry_caption" align="right">Country</td>
                             <td width="170">
                             	<?
									//echo create_drop_down( "cbo_country", 210, $yes_no,"", 1, "--- Select ---", $selected, "" );     	 
								?>
                             </td>-->
                             <td width="130" class="must_entry_caption" align="right">Inp. Date</td>
                             <td width="170">
                                <input type="text" value="<? echo date('d-m-Y');?>" name="txt_inp_date" id="txt_inp_date" class="datepicker" style="width:195px"   />
                             </td>

                          	<td width="130" class="must_entry_caption" align="right">Inspection Level</td>
                             <td width="170">
                                <? 
									echo create_drop_down( "cbo_inspection_level", 210, $inpLevelArray,"", 0, "--- Select ---", 3, "changeInspactionLevel(this.value)" );     	 
                                     ?> 
                             </td>
                          </tr>
						<tr>
							<td width="130" align="right">Add File</td>
							<td >
								<input type="button" class="image_uploader" style="width:150px" value="ADD/VIEW FILE" onClick="file_uploader ( '../', document.getElementById('txt_job_no').value,'', 'buyer_inspection', 2 ,1)">
							</td>
						</tr>
                    </table>
                    </fieldset>
                    <table><tr><td colspan="6" height="5"></td></tr></table>
                    <!-- ============================this is blank===================== -->
                    <table cellpadding="0" cellspacing="1" width="100%" class="rpt_table" rules="all" id="tbl_list">
                        <thead>
                            <th width="100" class="must_entry_caption">Order No</th>
                            <th width="100">Actual PO</th>
                            <th width="40">Week No</th>
                            <th width="100" >Country</th>
                            <th width="40" class="must_entry_caption">PO Qty</th>
                            <th width="40">Ship. Date </th>
                            <th width="40" id="qtyField">Finishing Qty</th>
                            <th width="40">Inspec. Qty</th>
                            <th width="40">Cuml. Insp. Qty</th>
                            <th width="80" class="must_entry_caption">Inspec. Status</th>
                            <th width="100">Cause </th>
                            <th width="100">Inspec.Reason </th>
							<th  width="180"><b> Comments</b></th>
							<th  width="70"><b> Action</b></th>
                        </thead>
                        <tr id="tr_1">
                            <td id="order_drop_down_td_1">
                               <input name="cboorderval_1" placeholder="Browse" id="cboorderval_1"  class="text_boxes" type="text" readonly  style="width:80px"   ondblclick="openOrderPoup(1)" />
                               <input type="hidden" name="cboorderid_1" id="cboorderid_1" value="">

                            	
                            </td>
                            <td id="acc_order_drop_down_td_1"><?  //echo create_drop_down( "cboorderid_1",100, $blank_array, 0, "", $selected, "" ); ?>
                               <input name="actualorderid_1" placeholder="Browse" id="actualorderid_1"  class="text_boxes" type="text" readonly  style="width:80px"   ondblclick="openActualPoPoup()" />
                               <input type="hidden" name="cboactualorderid_1" id="cboactualorderid_1" value="">

                            	
                            </td>
                            <td id="week_drop_down_td_1">
								<?  echo create_drop_down( "cboweekno_1",40, $blank_array,"", 1, "-- Select --", $selected, "" ); ?>
							</td>
                            <td id="country_drop_down_td_1">
								<?  echo create_drop_down( "cbocountryid_1",100, $blank_array, "",1, "-- Select --", $selected, "" ); ?>
							</td>
                            <td>
								<input name="txtpoquantity_1" id="txtpoquantity_1"  class="text_boxes_numeric" type="text"  style="width:40px"  disabled />
							</td>
                            <td>
								<input name="txtpubshipmentdate_1" id="txtpubshipmentdate_1" class="datepicker" type="text" value="" style="width:40px;" disabled  />
							</td>
                            <td>
								<input name="txtfinishingqnty_1" id="txtfinishingqnty_1"  class="text_boxes_numeric" type="text"  style="width:40px" readonly />
							</td>
                            <td>
								<input type="hidden" name="hiddeninsdata_1" id="hiddeninsdata_1" value="">
								<input name="txtinspectionqnty_1" placeholder="Write or Browse" id="txtinspectionqnty_1"  class="text_boxes_numeric" type="text" readonly  style="width:40px" onKeyUp="fn_qnty_check()" onDblClick="openInspPoup(1)" />
							</td>
                            <td >
								<input name="txtcuminspectionqnty_1" id="txtcuminspectionqnty_1"  class="text_boxes_numeric" type="text"  style="width:40px" readonly />
							</td>
                            <td>
								<?  echo create_drop_down( "cboinspectionstatus_1",80, $inspection_status,"", 1, "-- Select --", $selected, "" ); //change_cause_validation( this.value ) ?>
							</td>
                            <td>
								<?  echo create_drop_down( "cbocause_1",100, $inspection_cause,"", 1, "-- Select --", $selected, "" ); ?>
							</td>
                            <td >
								<input name="txtinsreason_1" id="txtinsreason_1"  class="text_boxes" type="text"  style="width:80px"  />
							</td>
							<td>
								<input name="txtcomments_1" id="txtcomments_1"  class="text_boxes" type="text"  style="width:180px"/>
							</td>
							<td>
                                <input type="button" id="increase_1" name="increase_1" style="width:20px" class="formbuttonplasminus" value="+" onclick="add_break_down_tr(1,this)" onkeydown="if (event.keyCode == 13) document.getElementById(this.id).onclick()">
                                
								<input type="button" id="decrease_1" name="decrease_1" style="width:20px" class="formbuttonplasminus" value="-" onclick="fn_delete_break_down_tr(1,this);">
                            </td>
                        </tr>
                </table>
				<table>					
					<tr>
						<td align="center" colspan="12" valign="middle" class="button_container">
							<?
							echo load_submit_buttons( $permission, "fnc_buyer_inspection_entry", 0,0 ,"reset_form('inspectionentry_1','inspection_production_list_view','','','')",1); 
							?>
							<input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly >	
						</td>
                    </tr>
				</table>
                <div style="width:1120px; margin-top:5px;"  id="inspection_production_list_view" align="center"></div>
          </form>
        </fieldset>
    </div>
</body> 
<script src="../includes/functions_bottom.js" type="text/javascript"></script>  
</html>