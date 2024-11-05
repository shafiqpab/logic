<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims batch creation
Functionality	:	
JS Functions	:
Created by		:	Aziz 
Creation date 	: 	5.05.2017
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
echo load_html_head_contents("Trims Batch Creation Info", "../", 1, 1,'','','');
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';	
	

	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];
	$(document).ready(function(e)
	{
		$("#txt_batch_color").autocomplete({
			source: str_color
		});
	});

	function openmypage_job()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_batch_against = $('#cbo_batch_against').val();
		var batch_for = $('#cbo_batch_for').val();
		
		if (form_validation('cbo_batch_against*cbo_batch_for*cbo_company_id','Batch Against*Batch For*Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Job Selection Form';	
			var page_link = 'requires/trims_batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&batch_against='+cbo_batch_against+'&action=job_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=400px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var batch_against = $("#cbo_batch_against"). val();
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			
				var hidden_po_id=this.contentDoc.getElementById("hidden_po_id").value; //Access form field with id="emailfield"
				var job_no=this.contentDoc.getElementById("hidden_job_no").value; //Access form field with id="emailfield"
				

				//$('#txt_booking_no_id').val(theemail);
				$('#txt_job_no').val(job_no);
				//$('#txt_batch_color').val(theecolor);
				//$('#booking_without_order').val(booking_without_order);
				//alert(job_no);
				reset_form('','','','',"$('#tbl_item_details tbody tr:not(:first)').remove();",'');
				
				var roll_maintained=$('#roll_maintained').val();
				var batch_maintained=$('#batch_maintained').val();
				$('#cbo_company_id').attr('disabled',true);
				
				//var booking =this.contentDoc.getElementById("hidden_booking_no").value; //Access form field with id="emailfield"
				show_list_view(job_no+'**'+hidden_po_id,'show_color_listview','list_color','requires/trims_batch_creation_controller','');	
				load_drop_down( 'requires/trims_batch_creation_controller', job_no, 'load_drop_down_trims_item_desc', 'desc_td_id' );

			}
		}
	}
	
	function active_inactive()
	{
		reset_form('','list_color','txt_job_no*txt_ext_no*cbo_color_range*txt_organic*txt_process_name*txt_process_id*txt_du_req_hr*txt_du_req_min*txt_batch_color','','$(\'#tbl_item_details tbody tr:not(:first)\').remove();','');
		var batch_against= $('#cbo_batch_against').val();
		var batch_for= $('#cbo_batch_for').val();
		$('#hidden_booking_without_order').val(0);
		
		if(batch_against==1 || batch_against==3)
		{
			$('#txt_ext_no').attr('disabled','disabled');
			//$('#txt_batch_color').attr('disabled','disabled');
			$('#txt_job_no').removeAttr('disabled','disabled');
			$('#cbo_color_range').removeAttr('disabled','disabled');
			$('#txt_process_name').removeAttr('disabled','disabled');
			$('#txt_batch_number').removeAttr('readOnly','readOnly');
			//$('#txtBatchQnty_1').removeAttr('disabled','disabled');
			
			
		}
		else if(batch_against==2)
		{
			$('#txt_ext_no').removeAttr('disabled','disabled');
			$('#txt_batch_number').val('');
			$('#txt_batch_number').attr('readOnly','readOnly');
			//$('#txt_booking_no_id').val('');
			$('#txt_job_no').attr('disabled','disabled');
			//$('#txtBatchQnty_1').attr('disabled','disabled');
			//$('#txt_batch_color').attr('disabled','disabled');
			$('#update_id').val('');
			$('#hide_update_id').val('');
			$('#hide_batch_against').val('');
			$('#cbo_color_range').attr('disabled','disabled');
			$('#txt_process_name').attr('disabled','disabled');
			
		}
		else if(batch_against==9)
		{
			$('#txt_ext_no').attr('disabled','disabled');
			//$('#txt_batch_color').removeAttr('disabled','disabled');
			$('#txt_job_no').attr('disabled','disabled');
			$('#txt_batch_number').removeAttr('readOnly','readOnly');
			//$('#txtBatchQnty_1').removeAttr('disabled','disabled');
			$('#cbo_color_range').removeAttr('disabled','disabled');
			$('#txt_process_name').removeAttr('disabled','disabled');
			$('#hidden_booking_without_order').val(1);
			
		}
		
		var roll_maintained=$('#roll_maintained').val();
		
	}
	
	function active_inactive_delete()
	{
		reset_form('','','txt_ext_no*txt_batch_weight*update_id*txt_batch_sl_no*hide_update_id','','$(\'#tbl_item_details tbody tr:not(:first)\').remove();','');
		var batch_against= $('#cbo_batch_against').val();
		var batch_for= $('#cbo_batch_for').val();
		$('#hidden_booking_without_order').val(0);
		
		
		if(batch_against==1 || batch_against==3)
		{
			$('#txt_ext_no').attr('disabled','disabled');
			//$('#txt_batch_color').attr('disabled','disabled');
			$('#txt_job_no').removeAttr('disabled','disabled');
			$('#cbo_color_range').removeAttr('disabled','disabled');
			$('#txt_process_name').removeAttr('disabled','disabled');
			$('#txt_batch_number').removeAttr('readOnly','readOnly');
			$('#txtBatchQnty_1').removeAttr('disabled','disabled');
			
			//$('#poNoTd_1').html('<select name="cboPoNo[]" id="cboPoNo_1" class="combo_boxes" style="width:130px"><option value="0">-- Select Po Number --</option></select>');
			
		}
		else if(batch_against==2)
		{
			$('#txt_ext_no').removeAttr('disabled','disabled');
			$('#txt_batch_number').val('');
			$('#txt_batch_number').attr('readOnly','readOnly');
		//	$('#txt_booking_no_id').val('');
			$('#txt_job_no').attr('disabled','disabled');
			$('#txtBatchQnty_1').attr('disabled','disabled');
			//$('#txt_batch_color').attr('disabled','disabled');
			$('#update_id').val('');
			$('#hide_update_id').val('');
			$('#hide_batch_against').val('');
			$('#cbo_color_range').attr('disabled','disabled');
			$('#txt_process_name').attr('disabled','disabled');
		
			
		}
		else if(batch_against==9)
		{
			$('#txt_ext_no').attr('disabled','disabled');
			//$('#txt_batch_color').removeAttr('disabled','disabled');
			$('#txt_job_no').attr('disabled','disabled');
			$('#txt_batch_number').removeAttr('readOnly','readOnly');
			$('#txtBatchQnty_1').removeAttr('disabled','disabled');
			$('#cbo_color_range').removeAttr('disabled','disabled');
			$('#txt_process_name').removeAttr('disabled','disabled');
			$('#hidden_booking_without_order').val(1);
			
		}
		
		var roll_maintained=$('#roll_maintained').val();
	
	}
	
	function calculate_batch_qnty()
	{
		var total_batch_qnty='';
		$("#tbl_item_details tbody").find('tr').each(function()
		{
			var batchQnty=$(this).find('input[name="txtBatchQnty[]"]').val();
			total_batch_qnty=total_batch_qnty*1+batchQnty*1;
		});
		
		$('#txt_total_batch_qnty').val(total_batch_qnty.toFixed(2));
		
	}
	
	
	
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/trims_batch_creation_controller.php?data=" + data+'&action='+action, true );
	}
	
	function fnc_batch_creation(operation)
	{
		if(operation==4)
		{
			alert('Not Allow');return;
			 var report_title=$( "div.form_caption" ).html();
			 generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title,'batch_card_print','requires/trims_batch_creation_controller');
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
		
		if( form_validation('cbo_batch_against*cbo_batch_for*cbo_company_id*txt_batch_date*txt_batch_weight*txt_batch_color','Batch Against*Batch For*Company*Batch Date*Batch Weight*Batch Color')==false )
		{
			return;
		}
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][136]);?>')
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][136]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][136]);?>')==false)
			{
				return;
			}
		}	
					
		
		if(($('#cbo_batch_against').val()==1 || $('#cbo_batch_against').val()==3) && $('#txt_job_no').val()=="")
		{
			alert("Please Select Job No");
			$('#txt_job_no').focus();
			return;
		}
		
		if($('#cbo_batch_against').val()==2 && $('#txt_ext_no').val()=="")
		{
			alert("Please Insert Extention No.");
			$('#txt_ext_no').focus();
			return;
		}
		//var save_data=$('#save_data').val();
		//alert(save_data);return;
		var txt_batch_weight=$('#txt_batch_weight').val()*1;
		var total_trims_qnty=$('#txt_total_trims_qnty').val()*1;
		//var batch_qty=$('#txt_total_batch_qnty').val()*1+$('#txt_tot_trims_weight').val()*1;
	
		if(txt_batch_weight!=total_trims_qnty)
		{
			alert('Batch Weight and Total Trim weight should be same.');
			return;
		}
		
		//var txt_deleted_id=$('#txt_deleted_id').val();
		var row_num=$('#tbl_item_details tbody tr').length;
		var data_all="";
		
		for(var i=1; i<=row_num; i++)
			{
				//alert(row_num);
				
				if (form_validation('txtitemDesc_'+i+'*trimsWeight_'+i,'Item Description*Trims Weight')==false)
				{
					return;
				}
				//alert(2);
				data_all+=get_submitted_data_string('updateIdDtls_'+i+'*txtitemDesc_'+i+'*trimsWeight_'+i+'*remarks_'+i,"../",i);
				//alert(data_all);
			}
	
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_batch_sl_no*cbo_batch_against*cbo_batch_for*cbo_company_id*batch_no_creation*batch_maintained*txt_batch_number*txt_batch_date*txt_batch_weight*txt_job_no*txt_ext_no*txt_batch_color*cbo_color_range*txt_organic*txt_process_id*txt_du_req_hr*txt_du_req_min*update_id*hide_update_id*hide_batch_against*roll_maintained*txt_remarks*txt_cuff_qty*txt_color_qty*cbo_machine_name*hidden_booking_without_order*cbo_working_company_id*cbo_floor',"../")+data_all+'&total_row='+row_num;
//alert(data);
		freeze_window(operation);
		
		http.open("POST","requires/trims_batch_creation_controller.php",true);
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
			else if(reponse[0]==13) 
			{ 
				 alert("Batch is used");
				 release_freezing();	
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
				
				var batch_for=$('#cbo_batch_for').val();
				var txt_job_no=$('#txt_job_no').val();
				var roll_maintained=$('#roll_maintained').val();
				var batch_maintained=$('#batch_maintained').val();
				
				show_list_view(batch_against+'**'+batch_for+'**'+reponse[1]+'**'+roll_maintained+'**'+batch_maintained+'**'+txt_job_no,'batch_details','batch_details_container','requires/trims_batch_creation_controller','');
				$('#cbo_company_id').attr('disabled',true);
				
				//$('#txt_deleted_id').val('');
				set_button_status(1, permission, 'fnc_batch_creation',1);
			}
			else if(reponse[0]==2) 
			{
				var batch_for=$('#cbo_batch_for').val();
				var roll_maintained=$('#roll_maintained').val();
				var batch_maintained=$('#batch_maintained').val();
				active_inactive_delete();
				set_button_status(0, permission, 'fnc_batch_creation',1);
			}
			release_freezing();	
		}
	}
	
	function fnc_trim_batch_card()
	{
		var txt_job_no=$('#txt_job_no').val();
		var report_title=$( "div.form_caption" ).html();
		generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title+'*'+txt_job_no,'trim_batch_card_print','requires/trims_batch_creation_controller');
		return;		
	}
	
	function openmypage_batchNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var batch_against = $('#cbo_batch_against').val();
		var batch_for = $('#cbo_batch_for').val();
		var roll_maintained = $('#roll_maintained').val();
		var batch_maintained=$('#batch_maintained').val();
	
		
		if (form_validation('cbo_batch_against*cbo_batch_for*cbo_company_id','Batch Against*Batch For*Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Batch No Selection Form';	
			var page_link = 'requires/trims_batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&batch_against='+batch_against+'&action=batch_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;	 //Access form field with id="emailfield"
				//alert(batch_id);
				if(batch_id!="")
				{
					freeze_window(5);
					get_php_form_data(batch_against+'**'+batch_for+'**'+batch_id, "populate_data_from_search_popup", "requires/trims_batch_creation_controller" );
					var txt_job_no=$('#txt_job_no').val();
				    show_list_view(batch_against+'**'+batch_for+'**'+batch_id+'**'+roll_maintained+'**'+batch_maintained+'**'+txt_job_no,'batch_details','batch_details_container','requires/trims_batch_creation_controller','');
					$('#cbo_company_id').attr('disabled',true);
					release_freezing();
					if(roll_maintained==1)
					{
						<?
							/*$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=64 and status_active=1 and is_deleted=0");
							foreach($scanned_barcode_data as $row)
							{
								$scanned_barcode_array[]=$row[csf('barcode_no')];
							}
							$jsscanned_barcode_array= json_encode($scanned_barcode_array);
							echo "scanned_barcode = ". $jsscanned_barcode_array . ";\n";*/
						?>
					}
					//$('#txt_deleted_id').val('');
					calculate_trims_qnty();
				} 
			}
		}
	}
	
	function openmypage_process()
	{
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';	
		var page_link = 'requires/trims_batch_creation_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup';
		  
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
	
	
	
	function put_country_data(color_id,color)
	{
		var batch_against = $('#cbo_batch_against').val();
		//var booking_without_order=$('#booking_without_order').val();
		var job_no=$('#txt_job_no').val();
		//var prev_color=$('#txt_batch_color').val();
		//var roll_maintained=$('#roll_maintained').val();
		//var batch_maintained=$('#batch_maintained').val();
		//alert(prev_color+"##"+color);
		
		$('#txt_batch_color').val(color);
		$('#txt_color_id').val(color_id);
			if(batch_against!=2)
			{
				//reset_form('','','txt_batch_sl_no*txt_batch_weight*txt_batch_number*txt_ext_no*cbo_color_range*txt_organic*txt_process_name*txt_process_id*txt_du_req_hr*txt_du_req_min*update_id*hide_update_id*hide_batch_against*txt_deleted_id','',"$('#tbl_item_details tbody tr:not(:first)').remove();",'');
				
				
				//$("#batch_details_container").find('select,input:not([type=button])').val('');
				
				
				//set_button_status(0, permission, 'fnc_batch_creation',1);
			}
			else
			{
				alert("Not For Re-Dyeing");
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
	
	
	
	
function fn_addRow_trims( i )
	{ 
			var row_num=$('#tbl_item_details tbody tr').length;
			//alert(lastTrId[1]);
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
				  'name': function(_, name) { return name },
				  'value': function(_, value) { return value }              
				});
				 
				}).end().appendTo("#tbl_item_details");
					
				$('#slTd_'+i).val('');
				$('#txtitemDesc_'+i).val('');
				$('#updateIdDtls_'+i).val('');
				$('#trimsWeight_'+i).val('');
				$('#remarks_'+i).val('');
				$("#tbl_item_details tbody tr:last").removeAttr('id').attr('id','tr_'+i);
				$('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id','slTd_'+i);
				$('#tr_' + i).find("td:eq(0)").text(i);
				
				$('#increase_'+i).removeAttr("value").attr("value","+");
				$('#decrease_'+i).removeAttr("value").attr("value","-");
				$('#increase_'+i).removeAttr("onclick").attr("onclick","fn_addRow_trims("+i+");");
				$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");
			}
			set_all_onclick();
	}
	
	function fn_deleteRow(rowNo) 
		{ 		
			
			var row_num=$('#tbl_item_details tbody tr').length;
			
			if(row_num!=1)
			{
				//alert(row_num);
				$("#tr_"+rowNo).remove();
			}
			 calculate_trims_qnty();
		}
		
		function calculate_trims_qnty()
			{
			var total_trims_qnty='';
			$("#tbl_item_details tbody").find('tr').each(function()
			{
				var trimsQnty=$(this).find('input[name="trimsWeight[]"]').val();
				total_trims_qnty=total_trims_qnty*1+trimsQnty*1;
			});
			
			$('#txt_total_trims_qnty').val(total_trims_qnty.toFixed(2));
			$('#txt_batch_weight').val(total_trims_qnty.toFixed(2));
			
			}
</script>
</head>

<body onLoad="set_hotkey();active_inactive();">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission); ?>
    <div style="width:995px; float:left" align="center">
    <fieldset style="width:995px;">
    <legend>Batch Creation</legend> 
        <form name="batchcreation_1" id="batchcreation_1"> 
            <fieldset style="width:970px;">
                <table width="830" align="center" border="0">
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
                                echo create_drop_down( "cbo_batch_against", 172, $batch_against,"", 1, '--- Select ---', 1, "active_inactive();",'','1,2,3,9','','','',1 );
                            ?>                              
                        </td>
                        <td width="130" class="must_entry_caption">Batch Date</td>
                        <td>
                            <input type="text" name="txt_batch_date" id="txt_batch_date" class="datepicker" style="width:160px;" tabindex="6" value="<? echo date("d-m-Y"); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Batch For</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_batch_for", 172, $batch_for,"", 0, '--- Select ---', 1, "",'1','3','','','',2 );
                            ?>                              
                        </td>
                        <td width="110" class="must_entry_caption">Batch Weight </td>
                        <td>
                            <input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:160px;" tabindex="7" />
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Company</td>
                        <td>
                            <?
							echo create_drop_down( "cbo_company_id", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name",'id,company_name', 1, '--Select Company--', 0,"get_php_form_data(this.value,'batch_no_creation','requires/trims_batch_creation_controller' );",'','','','','',3);
                            ?>                              
                        </td>
                        <td>Job No</td>
                        <td>
                            <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:160px;" placeholder="Double Click To Search" onDblClick="openmypage_job();" readonly tabindex="9"/>
                           <input type="hidden" name="hidden_booking_without_order" id="hidden_booking_without_order" />
                        </td>
                       
                    </tr>
					<tr>
                    	
                        <td class="">Working Company</td>
                        <td>
                            <?
							echo create_drop_down( "cbo_working_company_id", 172, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--Select Working--', 0,"load_drop_down('requires/trims_batch_creation_controller',this.value, 'load_drop_down_floor', 'td_floor' );fnc_load_report_format(this.value);",'','','','','',3);
                            ?>                              
                        </td>
                        <td>Floor</td>
                        <td id="td_floor">
						<? 
						echo create_drop_down("cbo_floor", 172, $blank_array,"", 1, "-- Select Floor--", 0, "",0,"","","","");
						?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Batch Number</td>
                        <td>
                            <input type="text" name="txt_batch_number" id="txt_batch_number" class="text_boxes" style="width:160px;" placeholder="Double Click To Edit" onDblClick="openmypage_batchNo()" tabindex="4" />
                        </td>
                        <td class="must_entry_caption">Batch Color</td>
                        <td>
                            <input type="text" name="txt_batch_color" id="txt_batch_color" class="text_boxes" value="" style="width:160px;" tabindex="10"  />
                             <input type="hidden" name="txt_color_id" id="txt_color_id" value="" />
                             
                        </td>
                    </tr>
                    <tr>
                        <td>Extention No.</td>
                        <td>
                            <input type="text" name="txt_ext_no" id="txt_ext_no" class="text_boxes_numeric" style="width:160px;" disabled="disabled" tabindex="5" />
                        </td>
                        <td>Color Range</td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_color_range", 172, $color_range,"",1, "-- Select --", 0, "" );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        
                        <td>Organic</td>
                        <td>
                            <input type="text" name="txt_organic" id="txt_organic" class="text_boxes" style="width:160px;" tabindex="12" />
                        </td>
                        <td>Process Name</td>
                        <td>
                            <input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:160px;" placeholder="Double Click To Search" onDblClick="openmypage_process();" tabindex="13" readonly />
                            <input type="hidden" name="txt_process_id" id="txt_process_id" value="" />
                        </td>
                    </tr>
                    <tr>
                    	
                        <td>Duration Req.</td>
                        <td>
                            <input type="text" name="txt_du_req_hr" id="txt_du_req_hr" class="text_boxes_numeric" placeholder="Hour" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_hr','txt_end_date',2,23)" style="width:70px;" />&nbsp;
                            <input type="text" name="txt_du_req_min" id="txt_du_req_min" class="text_boxes_numeric" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_min','txt_end_date',2,59)" placeholder="Minute" style="width:70px;" />
                        </td>
                        <td>Collar Qty (Pcs)</td>
                        <td>
                            <input type="text" name="txt_color_qty" id="txt_color_qty" class="text_boxes_numeric" style="width:160px;"/>
                        </td>
                    </tr>
                    <tr>
                    	
                        <td>Cuff Qty (Pcs)</td>
                      	<td>
                            <input type="text" name="txt_cuff_qty" id="txt_cuff_qty" class="text_boxes_numeric" style="width:160px;"/>
                        </td>
                        <td>Dyeing Machine</td>
                        <td>
						<? 
						//echo create_drop_down("cbo_machine_name", 172, $blank_array,"", 1, "-- Select Machine --", 0, "",0,"","","","");
						if($db_type==2)
						{
							echo create_drop_down( "cbo_machine_name", 172, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id=2 and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id,machine_name", 1,"-- Select Machine --", $selected, "","" );
						}
						else if($db_type==0)
						{
							echo create_drop_down( "cbo_machine_name", 172, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=2 and status_active=1 and is_deleted=0 and is_locked=0 order by  seq_no","id,machine_name", 1, "-- Select Machine --", $selected, "","" );
						}
						?></td>
                    </tr>
					
                    <tr>
                    	
                        <td>Remarks</td>
                        <td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:580px;" /></td>
                    </tr>
					 
                 </table>
            </fieldset>                 
            <fieldset style="width:990px; margin-top:10px">
            <legend>Trims Weight Details</legend>
            	
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600" id="tbl_item_details">
            	<thead>
                    <th width="40">SL</th>
                    <th width="200" class="must_entry_caption">Item Description</th>
                    <th width="80">Weight In Kg</th>
                    <th width="150">Remarks</th>
                    <th></th>
                </thead>
                <tbody id="batch_details_container">
                    <tr id="tr_1">
                    	<td id="slTd_1" width="30">1</td>
                        <td id="desc_td_id">
                         <?
							  echo create_drop_down( "txtitemDesc_1", 200, $blank_array,"", 1, "-- Select Item  --", 0, "", "", "", "", "", "","","","txtitemDesc[]");
                         ?>
                         
                        </td>
                        <td>
                        <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" class="text_boxes"  />
                        <input type="text" name="trimsWeight[]" id="trimsWeight_1" class="text_boxes_numeric" style="width:80px;" onKeyUp="calculate_trims_qnty();"/>
                        </td>
                        <td>
                        <input type="text" name="remarks[]" id="remarks_1" class="text_boxes" style="width:150px;"/>
                        </td>
                        
                        <td>
                        <input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fn_addRow_trims(1)" />
                        <input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
                        </td>
        			</tr>
                   
                </tbody> 
                <tfoot class="tbl_bottom">
                        <td>&nbsp;</td>
                        
                        <td>Sum</td>
                        <td><input type="text" name="txt_total_trims_qnty" id="txt_total_trims_qnty" class="text_boxes_numeric" style="width:80px" readonly /></td>
                        
                        <td colspan="2">&nbsp;</td>
                    </tfoot>   
            </table>
                
            </fieldset> 
            <table width="985">
                <tr>
                    <td align="center" class="button_container">
                        <? 
                            $date=date('d-m-Y');
                            echo load_submit_buttons($permission, "fnc_batch_creation",0,1,"reset_form('batchcreation_1','list_color','','cbo_batch_against,1*txt_batch_date,".$date."','disable_enable_fields(\'txt_job_no*\',0)'); $('#txt_ext_no').val(''); $('#txt_ext_no').attr('disabled','disabled');$('#txt_batch_number').removeAttr('readOnly','readOnly');$('#tbl_item_details tbody tr:not(:first)').remove();",1);
                        ?> 
                       
                        <input type="hidden" name="update_id" id="update_id"/>
                        <input type="hidden" name="hide_update_id" id="hide_update_id"/>
                        <input type="hidden" name="hide_batch_against" id="hide_batch_against"/>
                        <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                        <input type="hidden" name="batch_no_creation" id="batch_no_creation" readonly>
                        <input type="hidden" name="batch_maintained" id="batch_maintained" readonly>
                        <input type="hidden" name="hide_job_no" id="hide_job_no" readonly><!--For Duplication Check-->
	                    <input id="Print2" class="formbutton" type="button" style="width:80px;" onClick="fnc_trim_batch_card()" name="print2" value="Print2">
                    </td>	  
                </tr>
            </table>
        </form>
    </fieldset>
    </div>
    <div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
    <div id="list_color" style="width:330px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	//$('#txt_process_id').val(mandatory_subprocess);
</script>
</html>