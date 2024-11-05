<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Batch Creation For Sweater 
Functionality	:	
JS Functions	:
Created by		:	Kausar 
Creation date 	: 	18.03.2020
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
echo load_html_head_contents("Batch Creation For Sweater","../../", 1, 1, $unicode,1,'');

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
	
	function openmypage_bundle(page_link,title)
	{
		if ( form_validation('cbo_company_id','Lc Company Name')==false )
		{
			return;
		}
		
		var bundleNo='';
		$("#tbl_details").find('tbody tr').each(function()
		{
			bundleNo+=$(this).find("td:eq(1)").text()+',';
			
		});
		
		var title='Bundle Search';
		var colorId=$('#hidden_color_id').val();
		var page_link='	requires/sweater_batch_creation_controller.php?action=bundle_popup&company_id='+document.getElementById('cbo_company_id').value+'&bundleNo='+bundleNo+'&colorId='+colorId;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&bundleNo='+bundleNo, title,'width=1050px,height=370px,center=1,resize=0,scrolling=0','../')
									
		emailwindow.onclose=function()
		{
			var theform				=this.contentDoc.forms[0];
			var hidden_bundle_nos	=this.contentDoc.getElementById("hidden_bundle_nos").value;
			var hidden_color		=this.contentDoc.getElementById("hidden_color").value;
			var hidden_color_id		=this.contentDoc.getElementById("hidden_color_id").value;
			
			if (hidden_bundle_nos!="") 
			{
				if($('#txt_batch_color').val()=="")
				{
					$('#txt_batch_color').val( hidden_color ); 
					$('#hidden_color_id').val( hidden_color_id );
				}
				//create_row(hidden_bundle_nos,"Browse");
				var flag = 1;
				$("#tbl_details").find('tbody tr').each(function()
				{
					var bundleNo = $(this).find("td:eq(1)").text();
					var barcodeNo=$(this).find("td:eq(2)").text();
					if (hidden_bundle_nos == barcodeNo) {
						alert("Bundle No: " + bundleNo + " already scan, try another one.");
						$('#txt_bundle_no').val('');
						flag = 0;
						return;
					}
				});
				if(flag!=0)
					fnc_duplicate_bundle(hidden_bundle_nos,'Browse','');
			}
		}
	}//end function
	
	function create_row(bundle_nos, vscan, hidden_source_cond)
    {
        freeze_window(5);

        var row_num =  $('#tbl_details tbody tr').length; //$('#hidden_row_number').val();
   		var colorId=$('#hidden_color_id').val();
        var response_data = return_global_ajax_value(bundle_nos + "**" + row_num + "****" + $('#cbo_company_id').val() + "**" + vscan + "**" + hidden_source_cond+ "**" + colorId, 'populate_bundle_data', '', 'requires/sweater_batch_creation_controller');
        if (trim(response_data) == '')
        {
            alert("No Data Found. Please Check Pre-Costing Or Order Entry For Bundle Previous Process.");
        }

        $('#tbl_details tbody').prepend(response_data);
        var tot_row = $('#tbl_details tbody tr').length;
        if ((tot_row * 1) > 0)
        {
            $('#cbo_company_id').attr('disabled', 'disabled');
        }
        $('#hidden_row_number').val(tot_row);
		fnc_total_calculate();
        release_freezing();
    }
	
	function fnc_duplicate_bundle(bundle_no)
    {
        var challan_duplicate = return_ajax_request_value(bundle_no, "challan_duplicate_check", "requires/sweater_batch_creation_controller");
        //var ex_challan_duplicate = challan_duplicate.split("_");
       
		if ( trim( challan_duplicate)!= '')
        {
            alert(trim(challan_duplicate));
            $('#txt_bundle_no').val('');
            return;
        }
        else
        {
            create_row(bundle_no,'scan','');
			var tot_row=$('#tbl_details tbody tr').length; 
				 
			if( (tot_row*1) <2)
			{
				var response_mstdata = return_global_ajax_value(bundle_no+'_'+$('#cbo_company_id').val(), 'populate_mst_data', '', 'requires/sweater_batch_creation_controller');
				if(response_mstdata!="")
				{
					var ex_data=response_mstdata.split('_');
					//$('#cbo_company_id').val(ex_data[0]);
					
					$('#cbo_working_company').val(ex_data[3]);
					load_drop_down( 'requires/sweater_batch_creation_controller', ex_data[3], 'load_drop_down_location', 'working_location_td');
					$('#cbo_working_location').val(ex_data[4]);
					$('#txt_batch_color').val(ex_data[5]);
					$('#hidden_color_id').val(ex_data[6]);
				}
			}
        }
        $('#txt_bundle_no').val('');
    }
	
	 $('#txt_bundle_no').live('keydown', function(e) {
        if (e.keyCode === 13)
        {
			if ( form_validation('cbo_company_id','Lc Company Name')==false )
			{
				return;
			}
		
            e.preventDefault();
            var txt_bundle_no = trim($('#txt_bundle_no').val().toUpperCase());
		
            var flag = 1;
            $("#tbl_details").find('tbody tr').each(function()
            {
                var bundleNo = $(this).find("td:eq(1)").text();
				var barcodeNo=$(this).find("td:eq(2)").text();
                if (txt_bundle_no == barcodeNo) {
                    alert("Bundle No: " + bundleNo + " already scan, try another one.");
                    $('#txt_bundle_no').val('');
                    flag = 0;
                    return false;
                }
            });

            if (flag == 1)
            {
                fnc_duplicate_bundle(txt_bundle_no);
            }
        }
    });
	
	function fn_deleteRow(id)
	{
		$("#txtqty_"+id).closest('tr').remove();
		//calculate_defect_qty();
		fnc_total_calculate();
		rearrange_serial_no();
	}
	
	function rearrange_serial_no()
	{
		//$('#tbl_details tbody tr').length;
		var i=$('#tbl_details tbody tr').length;
		$("#tbl_details").find('tbody tr').each(function()
		{
			$(this).find("td:eq(0)").text(i);
			i--;
		});
	}
	
	function fnc_total_calculate()
	{
		var tot_row=$('#tbl_details tr').length;
		//alert(tot_row)
		var qty=0; var qtygm=0; var qtylbs=0;
		for (var i=1; i<=tot_row; i++)
		{
			qty=qty+$('#prodQty_'+i).text()*1;
			qtygm=qtygm+$('#prodQtyGm_'+i).text()*1;
			qtylbs=qtylbs+$('#prodQtyLbs_'+i).text()*1;
		}
		
		$('#totQty').text(qty);
		$('#totQtyGm').text(qtygm);
		$('#totQtyLbs').text(qtylbs);
		$('#txt_batch_weight').val(qtygm);
	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/sweater_batch_creation_controller.php?data=" + data+'&action='+action, true );
	}
	
	function fnc_batch_creation(operation)
	{
		if(operation==2)
		{
			alert ('Delete Restricted.');
			return;
		}
		if(operation==4)
		{
			if ( $('#txt_batch_sl_no').val()=='')
			{
				alert ('Batch Serial Not Save.');
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_batch_sl_no').val()+'*'+$('#txt_batch_number').val()+'*'+$('#txt_ext_no').val()+'*'+report_title,'batch_card_print','requires/sweater_batch_creation_controller');
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

		if( form_validation('cbo_batch_against*cbo_company_id*cbo_working_company*cbo_working_location*txt_batch_color*txt_batch_date*txt_batch_weight*cbo_operation','Batch Against*Lc Company*Working Company*Working Location*Batch Color*Batch Date*Batch Weight*operation')==false )
		{
			return;
		}
		
		if(operation==0 || operation==1 || operation==2)
		{
			var j 			=0; 
			var dataString 	='';
	
			$("#tbl_details").find('tbody tr').each(function()
			{
				//var machine_no 		=$(this).find('input[name="txt_machine_no[]"]').val();
				var bundleNo 		=$(this).find("td:eq(1)").text();
				var barcodeNo 		=$(this).find("td:eq(2)").text();
				var colorId 		=$(this).find('input[name="txtcolorId[]"]').val();
				var sizeId 			=$(this).find('input[name="txtsizeId[]"]').val();
				//var qty 			=$(this).find("td:eq(11)").text();
				var qty 			=$(this).find('input[name="txtqty[]"]').val();
				var qtygm			=$(this).find('input[name="txtqtygm[]"]').val();
				var qtylbs			=$(this).find('input[name="txtqtylbs[]"]').val();
					
				var orderId 		=$(this).find('input[name="txtorderId[]"]').val();
				var countryId 		=$(this).find('input[name="txtcountryId[]"]').val();
				
				var gmtsitemId 		=$(this).find('input[name="txtgmtsitemId[]"]').val();
				var color_size_id 	=$(this).find('input[name="txtcolorSizeId[]"]').val();
				var txtcutNo 		=$(this).find('input[name="txtcutNo[]"]').val();
				var dtlsId 			= $(this).find('input[name="dtlsId[]"]').val();			
				
				try 
				{
					j++;
					dataString+='&bundleNo_' + j + '=' + bundleNo + 
								'&barcodeNo_' + j + '=' + barcodeNo + 
								'&colorId_' + j + '=' + colorId + 
								'&sizeId_' + j + '=' + sizeId + 
								'&colorSizeId_' + j + '=' + color_size_id + 
								'&qty_' + j + '=' + qty + 
								'&qtygm_' + j + '=' + qtygm + 
								'&qtylbs_' + j + '=' + qtylbs + 
								'&orderId_' + j + '=' + orderId  + 
								'&countryId_' + j + '=' + countryId + 
								'&gmtsitemId_' + j + '=' + gmtsitemId + 
								'&txtcutNo_' + j + '=' + txtcutNo+
								'&dtlsId_' + j + '=' + dtlsId; 							
				}
				catch(e) 
				{
					alert("There is some problem.");
					return;
				}
			});
			
			if(j<1)
			{
				alert('No data Found.');
				return;
			}
			
			//alert(dataString);return;
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_batch_sl_no*cbo_batch_against*cbo_gmts_type*txt_batch_date*cbo_shift*cbo_company_id*cbo_working_company*cbo_working_location*txt_supervisor*txt_operator*txt_batch_number*hidden_batch_no*hidden_batch_id*txt_ext_no*batch_no_creation*txt_batch_weight*txt_batch_color*hidden_color_id*txt_process_id*txt_du_req_hr*txt_du_req_min*machine_id*cbo_operation*hidden_operation_id*cbo_sub_operation*txt_remarks*update_id',"../../")+dataString+'&tot_row='+j;
			freeze_window(operation);
			//alert(data); release_freezing();return;
			http.open("POST","requires/sweater_batch_creation_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_batch_creation_Reply_info;
		}
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
				var batch_no_creation=$('#batch_no_creation').val()*1;
				if(batch_no_creation==1)
				{
					$('#txt_batch_number').val(reponse[1]);
				}
				var batch_against=$('#cbo_batch_against').val();
				var batch_for=0;//$('#cbo_batch_for').val();
				
				var response_data = return_global_ajax_value($('#cbo_company_id').val()+'**'+reponse[1], 'populate_bundle_data_update', '', 'requires/sweater_batch_creation_controller');
				
				$('#tbl_details tbody tr').remove();
                $('#tbl_details tbody').prepend(response_data);
				fnc_total_calculate();
				//show_list_view($('#cbo_company_id').val()+'**'+reponse[1],'populate_bundle_data_update','batch_details_container','requires/sweater_batch_creation_controller','');
				set_button_status(1, permission, 'fnc_batch_creation',1,1);
			}
			$("#cbo_operation").attr("disabled",true);
			//load_color_list(1);
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
			var page_link = 'requires/sweater_batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&batch_against='+batch_against+'&action=batch_popup';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=965px,height=370px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var batch_id=this.contentDoc.getElementById("hidden_batch_id").value;	 //Access form field with id="emailfield"
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
					get_php_form_data(batch_id+'**'+batch_for+'**'+batch_id+'**'+po_id+'**'+operation_type_id+'**'+batch_color_id+'**'+operation_type+'**'+unloaded_batch+'**'+ext_from+'**'+cbo_company_id+'**'+batch_no, "populate_data_from_search_popup", "requires/sweater_batch_creation_controller" );
				   var response_data = return_global_ajax_value(cbo_company_id+'**'+batch_id, 'populate_bundle_data_update', '', 'requires/sweater_batch_creation_controller');
				
					$('#tbl_details tbody tr').remove();
					$('#tbl_details tbody').prepend(response_data);
					//set_multiselect('cbo_sub_operation','0','1',sub_operation_id,'0');
					fnc_total_calculate();
					release_freezing();
				} 
			}
			$("#cbo_operation").attr("disabled",true);
		}
	}

	function openmypage_process()
	{
		if(form_validation('cbo_batch_against','Batch Against')==false ){ return; }

		var cbo_batch_against = $('#cbo_batch_against').val();
		var txt_process_id = $('#txt_process_id').val();
		var title = 'Process Name Selection Form';	

		var page_link = 'requires/sweater_batch_creation_controller.php?txt_process_id='+txt_process_id+'&action=process_name_popup&cbo_batch_against='+cbo_batch_against;
		  
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
		
		if (form_validation('cbo_company_id*cbo_batch_against','Lc Company* Batch Against')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Machine No Selection Form';	
			var page_link = 'requires/sweater_batch_creation_controller.php?cbo_company_id='+cbo_company_id+'&action=machineNo_popup&cbo_batch_against='+cbo_batch_against;
			  
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
<div style="width:100%; float:left;">
	<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="batchcreation_1" id="batchcreation_1"> 
            <fieldset style="width:800px;">
            	<legend>Batch Creation For Sweater</legend> 
                <table width="800" align="center" border="0">
                	<tr>
                    	<td align="right" colspan="3"><b>Batch Serial No</b></td>
                        <td colspan="3"><input type="text" name="txt_batch_sl_no" id="txt_batch_sl_no" class="text_boxes" style="width:130px;" placeholder="Browse" onDblClick="openmypage_batchNo();" readonly /></td>
                    </tr>
                    <tr>
                   		<td width="110" class="must_entry_caption">Batch Against</td>
                        <td width="150"><? echo create_drop_down( "cbo_batch_against", 140, $batch_against,"",6, '--- Select ---',1, "",'','6,11','','','',1 ); ?></td>
                        <td width="110" class="must_entry_caption">Gmts Type</td>
                        <td width="150"><? echo create_drop_down( "cbo_gmts_type", 140, $wash_gmts_type_array,"", 1, "-Select Type--", 7, "","" ); ?></td>
                        <td width="110" class="must_entry_caption">Batch Date</td>
                        <td>
                            <input type="text" name="txt_batch_date" id="txt_batch_date" class="datepicker" style="width:50px;" tabindex="2" value="<? echo date("d-m-Y"); ?>" />
                            &nbsp;Shift&nbsp;
                            <? echo create_drop_down( "cbo_shift", 50, $shift_name,"", 1, '---', 0, "",'','','','','',3 ); ?>
                        </td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">LC Company</td>
                        <td><?=create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '--Select Company--', 0,"get_php_form_data(this.value,'batch_no_creation','requires/sweater_batch_creation_controller'); get_php_form_data( this.value, 'company_wise_report_button_setting','requires/sweater_batch_creation_controller'); load_drop_down( 'requires/sweater_batch_creation_controller', this.value, 'load_drop_down_working_com', 'working_com' ); ",'','','','','',4); ?></td>
                        <td class="must_entry_caption">W. Company</td>
                        <td id="working_com"><?=create_drop_down( "cbo_working_company", 140, $blank_array,"", 1, "--Select--", $selected, "",1 ); ?></td>
                    	<td class="must_entry_caption">WC. Location</td>
                        <td id="working_location_td"><?=create_drop_down( "cbo_working_location", 140, $blank_array,"", 1, "--Select Location--", $selected, "",0 ); ?></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Batch Number</td>
                        <td><input type="text" name="txt_batch_number" id="txt_batch_number" class="text_boxes" style="width:130px;" placeholder="Double Click To Edit" onDblClick="openmypage_batchNo();" tabindex="7" /></td>
                        <td class="must_entry_caption">Batch Weight[GM] </td>
                        <td>
                            <input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:50px;" readonly tabindex="9" />
                            &nbsp;Ext No.&nbsp;
                            <input type="text" name="txt_ext_no" id="txt_ext_no" class="text_boxes_numeric" style="width:25px;" disabled="disabled" tabindex="10" />
                        </td>
                        <td id="batch_color_td" class="must_entry_caption">Batch Color</td>
                        <td><input type="text" name="txt_batch_color" id="txt_batch_color" class="text_boxes" value="" style="width:130px;" disabled="disabled" tabindex="8" /></td>
                    </tr>
                    <tr>
                    	<td>Process Name</td>
                    	<td><? echo create_drop_down( "txt_process_id",140, $wash_type,"",1, "-- Select --",1,"",1,'','','','',11); ?></td>
                        <td>Duration Req.</td>
                        <td>
                            <input type="text" name="txt_du_req_hr" id="txt_du_req_hr" class="text_boxes_numeric" placeholder="Hour" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_hr','txt_end_date',2,23)" style="width:55px;" tabindex="12"/>&nbsp;
                            <input type="text" name="txt_du_req_min" id="txt_du_req_min" class="text_boxes_numeric" onKeyUp="fnc_move_cursor(this.value,'txt_du_req_min','txt_end_date',2,59)" placeholder="Minute" style="width:55px;" tabindex="13" />
                        </td>
                        <td>Machine No</td>
                       <td>
                            <input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes" tabindex="14" style="width:130px;" onDblClick="fn_machine_seach();" placeholder="Browse" readonly/>
                            <input type="hidden" name="machine_id" id="machine_id" class="text_boxes"/>
                      	</td>
                    </tr>
                    <tr>
						<td class="must_entry_caption">Operation</td>
                        <td><? echo create_drop_down( "cbo_operation",140, $wash_operation_arr,"",1, "-- Select --",0,"load_drop_down( 'requires/sweater_batch_creation_controller', this.value, 'load_drop_down_sub_operation', 'sub_operation'), set_multiselect('cbo_sub_operation','0','0','','0'), list_wash_operation(1)",0,'','','','','',15); ?>
                            <input type="hidden" name="hidden_operation_id" id="hidden_operation_id" class="text_boxes_numeric"  />  
                            <input type="hidden" name="hidden_color_id" id="hidden_color_id" class="text_boxes_numeric"  />
                            <input type="hidden" name="hidden_batch_no" id="hidden_batch_no" readonly />
                            <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" readonly />
                            <input type="hidden" name="hidden_batch_dtls_id" id="hidden_batch_dtls_id" readonly />  
                        </td>
                        <td>Supervisor</td>
                        <td><input type="text" name="txt_supervisor" id="txt_supervisor" class="text_boxes" style="width:130px;" tabindex="5" /></td>
                        <td>Operator</td>
                        <td><input type="text" name="txt_operator" id="txt_operator" class="text_boxes" style="width:130px;" tabindex="6" /></td>
                    </tr>
                    <tr>
                        <td>Sub Operation</td>
                        <td id="sub_operation"><? echo create_drop_down( "cbo_sub_operation",140, $wash_sub_operation_arr,"","", "", 0, "",'','','','','',16); ?></td>
                        <td>Remarks</td>
                        <td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" tabindex="17" style="width:410px;" /></td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                        <td>&nbsp;</td>
                    	<td class="must_entry_caption" id="td_caption"><b>Barcode No:</b></td>
                    	<td colspan="2"> 
                                <input name="txt_bundle_no" id="txt_bundle_no" class="text_boxes" style="width:180px" placeholder="Browse / Write / Scan" onDblClick="openmypage_bundle('requires/sweater_batch_creation_controller.php?action=bundle_popup&company='+document.getElementById('cbo_company_id').value+'&garments_nature='+document.getElementById('garments_nature').value, 'Bundle Search');" tabindex="18" />
                       </td>
                       <td>&nbsp;</td>
                    </tr>
                 </table>
            </fieldset>                 
            <fieldset style="width:1330px">
            <legend>Bundle Details</legend>
            	<div id="bundle_list_view">
                	<table cellpadding="0" width="1330" cellspacing="0" border="1" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th width="30">SL</th>
                                <th width="80">Bundle No</th>
                                <th width="90">QR Code</th>
                                <th width="100">Gmts. Color</th>
                                <th width="60">Size</th>
                                <th width="65">Knit Qty.(Pcs)</th>
                                <th width="65">Weight Rec. (GM)</th>
                                <th width="65">Weight Rec. (LBS)</th>
                                <th width="70">Working Comp.</th>
                                <th width="90">Linking Floor</th>
                                
                                <th width="50">Job Year</th>
                                <th width="60">Job No</th>
                                <th width="65">Buyer</th>
                                <th width="90">Style No</th>
                                <th width="90">Order No</th>
                                <th width="100">Gmts. Item</th>
                                <th width="80">Country</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                    </table>
                    <div  style="width:1330px;max-height:250px;overflow-y:scroll"  align="left">    
                        <table cellpadding="0" width="1310" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">      
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <table cellpadding="0" width="1330" cellspacing="0" border="1" class="tbl_bottom" rules="all">
                        <thead>
                            <tr>
                                <td width="30">&nbsp;</td>
                                <td width="80">&nbsp;</td>
                                <td width="90">&nbsp;</td>
                                <td width="100">Total:</td>
                                <td width="60">&nbsp;</td>
                                <td width="65" id="totQty"></td>
                                <td width="65" id="totQtyGm"></td>
                                <td width="65" id="totQtyLbs"></td>
                                <td width="70">&nbsp;</td>
                                <td width="90">&nbsp;</td>
                                
                                <td width="50">&nbsp;</td>
                                <td width="60">&nbsp;</td>
                                <td width="65">&nbsp;</td>
                                <td width="90">&nbsp;</td>
                                <td width="90">&nbsp;</td>
                                <td width="100">&nbsp;</td>
                                <td width="80">&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </fieldset> 
            <table width="1330">
                <tr>
                    <td align="center" valign="middle" class="button_container"> 
                        <? 
                            $date=date('d-m-Y');
                            echo load_submit_buttons($permission, "fnc_batch_creation",0,1,"reset_form('batchcreation_1','','','txt_batch_date,".$date."','','txt_process_id'); $('#tbl_item_details tbody tr:not(:first)').remove(); $('.color_tble').remove(); ",1);
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
                        <input type="hidden" name="hidden_row_number" id="hidden_row_number"> 
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <br>
    <!--<div id="list_color" style="width:30%; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;" align="center"></div>-->
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>set_multiselect('cbo_sub_operation','0','0','','0');</script>
</html>