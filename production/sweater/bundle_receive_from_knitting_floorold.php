<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create print imbro issue
				
Functionality	:	
JS Functions	:
Created by		:	Ashraful 
Creation date 	: 	24-02-2013
Updated by 		: 	Kausar (Creating Print Report )	
Update date		: 	08-01-2014	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//echo integration_params(2);die;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Bundle Issue to Knitting Floor","../../", 1, 1, $unicode,'','');
?>
	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


var str_machineline='';
function create_row(bundle_nos,vscan,hidden_cutting_no,bodypart_type)
{
	//freeze_window(5);
	var tot_row=$('#tbl_details tbody tr').length; 
	
	if(tot_row==0)
	{
		if(vscan=="Browse")
		{
			get_php_form_data(
								hidden_cutting_no,
								"populate_data_from_yarn_lot", 
								"requires/bundle_receive_from_knitting_floor_controller"
							 );
			show_list_view(
							hidden_cutting_no+'_'+bundle_nos+'_'+bodypart_type,
							'show_dtls_listview',
							'bundle_list_view',
							'requires/bundle_receive_from_knitting_floor_controller',
							''
						  );
						  
			show_list_view(
							hidden_cutting_no+'_'+bundle_nos,
							'show_dtls_yarn_listview',
							'yarn_list_view',
							'requires/bundle_receive_from_knitting_floor_controller',
							''
						  );

			
		
		}
		else
		{
			get_php_form_data(
								bundle_nos,
								"populate_data_from_yarn_lot_bundle", 
								"requires/bundle_receive_from_knitting_floor_controller"
							 );
			show_list_view(
							bundle_nos+'_'+bodypart_type,
							'show_dtls_listview_bundle',
							'bundle_list_view',
							'requires/bundle_receive_from_knitting_floor_controller',
							''
						  );

			show_list_view(
							$("#txt_lot_ratio").val()+'_'+bundle_nos,
							'show_dtls_yarn_listview',
							'yarn_list_view',
							'requires/bundle_receive_from_knitting_floor_controller',
							''
						  );
			
		}

		var all_barcode_no=bundle_nos.split(",");
		$("#hidden_row_number").val(all_barcode_no.length);
		var all_machine=trim(return_global_ajax_value( $("#cbo_floor").val(), 'load_machine', '', 'requires/bundle_receive_from_knitting_floor_controller'));
			str_machineline =eval(all_machine);
			
			
			
		
	}
	else
	{
		var row_num=$("#hidden_row_number").val();
		if(vscan=='scan') hidden_cutting_no=$("#txt_lot_ratio").val();
		
		var response_data=return_global_ajax_value(bundle_nos+"**"+row_num+"**"+bodypart_type+"**"+vscan+"**"+hidden_cutting_no, 'populate_bundle_data', '', 'requires/bundle_receive_from_knitting_floor_controller');
	
		$('#tbl_details tbody').append(response_data);
		calculate_bundle_qty();
		var all_barcode_no=bundle_nos.split(",");
		$("#hidden_row_number").val((all_barcode_no.length*1+row_num*1));

		fnc_load_machine();
		
		
	}

	

	calculate_yarn_qty();

	release_freezing();
}

function fnc_load_machine()
{

	$("#tbl_details").find('tbody tr').each(function()
	{
		$(this).find('input[name="txt_machine_no[]"]').autocomplete({
			source: str_machineline
		});
		
	});
}


function openmypage_bundle(page_link,title)
{
	
	if ( form_validation('cbo_bodypart_type*txt_operator_id','Bodypart Type*Operator ID')==false )
	{
		return;
	}
	
	var bundleNo='';
	$("#tbl_details").find('tbody tr').each(function()
	{
		bundleNo+=$(this).find("td:eq(1)").text()+',';
		
	});
	
	var title='Bundle Search';
	var page_link='	requires/bundle_receive_from_knitting_floor_controller.php?action=bundle_popup&lot_ratio='+
					document.getElementById('txt_lot_ratio').value+
					'&garments_nature='+document.getElementById('garments_nature').value+
					'&company_id='+document.getElementById('cbo_company_name').value+
					'&bodypart_type='+document.getElementById('cbo_bodypart_type').value+
					'&bundleNo='+bundleNo;
					
	
	emailwindow=dhtmlmodal.open('EmailBox', 
								'iframe', 
								page_link+
								'&bundleNo='+bundleNo, 
								title, 
								'width=890px,height=370px,center=1,resize=0,scrolling=0',
								'../')
								
	emailwindow.onclose=function()
	{
		var theform				=this.contentDoc.forms[0];
		var hidden_bundle_nos	=this.contentDoc.getElementById("hidden_bundle_nos").value;
		var hidden_cutting_no	=this.contentDoc.getElementById("hidden_cutting_no").value;
		$("#txt_lot_ratio").val(hidden_cutting_no);
		
		if (hidden_bundle_nos!="")
		{ 
			create_row(hidden_bundle_nos,"Browse",hidden_cutting_no,$("#cbo_bodypart_type").val());
			$("#cbo_bodypart_type").attr('disabled',true);
		}
	}
	
}//end function



function load_location()
{
	var cbo_company_id = $('#cbo_company_name').val();
	var cbo_source = $('#cbo_source').val();
	var cbo_working_company = $('#cbo_working_company').val();
	if(cbo_source==1)
	{
		load_drop_down( 'requires/bundle_receive_from_knitting_floor_controller',cbo_working_company, 'load_drop_down_location', 'working_location_td' );
	}
	else
	{
		$("#cbo_location_name").val(0).attr("disabled",true);
		$("#cbo_floor").val(0).attr("disabled",true);
	}
}

$('#txt_bundle_no').live('keydown', function(e) {

	if ( form_validation('cbo_bodypart_type*txt_operator_id','Bodypart Type*Operator ID')==false )
	{
		$('#txt_bundle_no').val('');
		return;
	}


	if (e.keyCode === 13) 
	{
		e.preventDefault();
		var txt_bundle_no=trim($('#txt_bundle_no').val().toUpperCase());
		var flag=1;
		$("#tbl_details").find('tbody tr').each(function()
		{
			var bundleNo=$(this).find("td:eq(1)").text();
			var barcodeNo=$(this).find("td:eq(1)").attr('title');
			if(txt_bundle_no==barcodeNo){
				
				alert("Bundle No: "+bundleNo+" already scan, try another one.");
				$('#txt_bundle_no').val('');
				flag=0;
				return false;
			}
		});
	
		if(flag==1)
		{
			fnc_duplicate_bundle(txt_bundle_no);
			$("#cbo_bodypart_type").attr('disabled',true);
		}
	}
});



function fnc_duplicate_bundle(barcode_no)
{
	var challan_duplicate=return_ajax_request_value( barcode_no,"challan_duplicate_check", "requires/bundle_receive_from_knitting_floor_controller");
	var ex_challan_duplicate=challan_duplicate.split("_");
	if(ex_challan_duplicate[0]==2) 
	{
		var alt_str=ex_challan_duplicate[1].split("*");
		var al_msglc="Bundle No '"+trim(alt_str[0])+"' Found in Challan No '"+trim(alt_str[1])+"'";
		alert(al_msglc);
		$('#txt_bundle_no').val('');
		return;
	}
	else
	{
		if($('#txt_lot_ratio').val())
		{
			if($('#txt_lot_ratio').val()!=ex_challan_duplicate[2]) {
				alert("Lot Retio Mixed Not Allow.This Barcode not Belong to "+$('#txt_lot_ratio').val());
				$('#txt_bundle_no').val('');
				return;
			}
		}
		create_row(barcode_no,'scan','',$("#cbo_bodypart_type").val());
	}
	$('#txt_bundle_no').val('');
}



function calculate_bundle_qty()
{
	var total_bundle_qty	=0;
	var color_id_arr		=$("#color_id_string").val().split(",");
	var total_color_qty_arr	=Array();

	for(var i=0;i<color_id_arr.length;i++)
	{	
		total_color_qty_arr[color_id_arr[i]]=0;
	}

	$("#tbl_details").find('tbody tr').each(function()
	{
		total_bundle_qty+=$(this).find("td:eq(5)").text()*1;
		var id=$(this).find('input[name="trId[]"]').val()*1;
		
		for(var i=0;i<color_id_arr.length;i++)
		{	
			total_color_qty_arr[color_id_arr[i]]+=$("#bdl_"+id+"_"+color_id_arr[i]).text()*1;
		}
		
	});

	$("#total_bundle_qty").text(total_bundle_qty);
	for(var j=0;j<color_id_arr.length;j++)
	{	
		$("#ttl_"+color_id_arr[j]).text(total_color_qty_arr[color_id_arr[j]]);
	}	
}


function calculate_yarn_qty()
{
	var total_color_cons=0;
	$("#tbl_yarn_details").find('tbody tr').each(function()
	{
		var color_id 	=$(this).find('input[name="hidden_yarn_color[]"]').val();
		var color_qty 	=$("#ttl_"+color_id).text()*1;
		total_color_cons+=color_qty;
		$(this).find("td:eq(3)").text(color_qty);

	});
	
	$("#total_required_qty").text(total_color_cons.toFixed(4));
	$("#total_color_cons").text(total_color_cons.toFixed(4));
}


function fnc_total_issue_balance(id)
{
	
	var total_issue_qty		=0;
	var total_short_qty		=0;
	var total_issue_balance =0;

	$("#tbl_yarn_details").find('tbody tr').each(function()
	{

		var required_qty  	=$(this).find("td:eq(3)").text()*1;
		var returnable_qty	=$(this).find("td:eq(4)").text()*1;
		var issue_qty 		=$(this).find('input[name="txt_issue_qty[]"]').val()*1;
		var current_short	=required_qty-issue_qty;
		var issue_balance  	=returnable_qty+current_short;
		
		total_issue_qty		+=issue_qty;		
		total_short_qty 	+=current_short;
		total_issue_balance +=issue_balance;

		$(this).find("td:eq(6)").text(current_short.toFixed(4));
		$(this).find("td:eq(7)").text(issue_balance.toFixed(4))
	});
	
	$("#total_issue_qty").text(total_issue_qty.toFixed(4));
	$("#total_short_excess_qty").text(total_short_qty.toFixed(4));
	$("#total_balance_qty").text(total_issue_balance.toFixed(4));
	
	
}

function openmypage_operator()
{
	var page_link='requires/bundle_receive_from_knitting_floor_controller.php?action=operator_popup';
	var title='Bundle Search';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=390px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{				
		var employee_data=(this.contentDoc.getElementById("hidden_emp_number").value).split("_");//po id
		$("#txt_operator_id").val(employee_data[1]);
		$("#txt_operation_name").val(employee_data[2]);	
	}
	
}//end function


function fnc_minusRow(id)
{
	$("#txt_machine_no_"+id).closest('tr').remove();
	var i=1;
	$("#tbl_details").find('tbody tr').each(function()
	{
		$(this).find("td:eq(0)").text(i);
		i++;
	});

	calculate_bundle_qty();
	calculate_yarn_qty();
}





function fnc_issue_print_embroidery_entry(operation)
{
		
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 'emblishment_issue_print', 'requires/print_embro_delivery_entry_controller');
		return;
	}
	
	if ( form_validation('cbo_floor*txt_issue_date*cbo_source*txt_operator_id*cbo_working_company*cbo_working_location*cbo_company_name*cbo_location*cbo_bodypart_type',
		'Floor*Issue Date*Source*Operator ID*W. Company*WC. Location*LC Company*Location*Bodypart Type')==false )
	{
		return;
	}
	
	
	if(operation==0 || operation==1 || operation==2)
	{
				
			var color_id_arr=$("#color_id_string").val().split(",");
			var j 			=0; 
			var k 			=0; 
			var dataString 	='';
			var dataString1	='';
			var bdl_cons_td	=8+(color_id_arr.length)*1;

			$("#tbl_details").find('tbody tr').each(function()
			{
				//var machine_no 		=$(this).find('input[name="txt_machine_no[]"]').val();
				var bundleNo 		=$(this).find("td:eq(1)").text();
				var barcodeNo 		=$(this).find("td:eq(1)").attr('title');
				var colorId 		=$(this).find('input[name="txt_color_id[]"]').val();
				var sizeId 			=$(this).find('input[name="txt_size_id[]"]').val();
				var orderId 		=$(this).find('input[name="txt_order_id[]"]').val();
				var countryId 		=$(this).find('input[name="txt_country_id[]"]').val();
				var gmtsitemId 		=$(this).find('input[name="txt_gmt_item_id[]"]').val();
				var machine_id 		=$(this).find('input[name="txt_machine_id[]"]').val();
				var bundle_cons 	=$(this).find("td:eq("+bdl_cons_td+")").text()*1;				
				var qty 			=$(this).find("td:eq(5)").text();		
				
				try 
				{
					j++;
					dataString+='&bundleNo_' + j + '=' + bundleNo + 
								'&orderId_' + j + '=' + orderId + 
								'&gmtsitemId_' + j + '=' + gmtsitemId + 
								'&countryId_' + j + '=' + countryId + 
								'&colorId_' + j + '=' + colorId + 
								'&sizeId_' + j + '=' + sizeId  + 
								'&qty_' + j + '=' + qty + 
								'&bundle_cons_' + j + '=' + bundle_cons + 
								'&machine_id_' + j + '=' + machine_id + 		
								'&barcodeNo_' + j + '=' + barcodeNo; 							
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
			
			$("#tbl_yarn_details").find('tbody tr').each(function()
			{
				var yarn_color 		=$(this).find('input[name="hidden_yarn_color[]"]').val();
				var sample_color 	=$(this).find('input[name="hidden_sample_color[]"]').val();
				var required_qty	=$(this).find("td:eq(3)").text()*1;
				var returanable_qty	=$(this).find("td:eq(4)").text()*1;
				var short_excess	=$(this).find("td:eq(6)").text()*1;
				var issue_balance	=$(this).find("td:eq(7)").text()*1;
				var isssue_qty 		=$(this).find('input[name="txt_issue_qty[]"]').val();	
				var yarn_dtls_id 	=$(this).find('input[name="hidden_yarn_dtls_id[]"]').val();				
				
				try 
				{
					k++;
					
					dataString1+='&yarnColor_' + k + '=' + yarn_color + 
								'&sampleColor_' + k+ '=' + sample_color + 
								'&requiredQty_' + k + '=' + required_qty + 
								'&returnableQty_' + k + '=' + returanable_qty + 
								'&shortExcess_' + k + '=' + short_excess + 
								'&issueBalance_' + k + '=' + issue_balance  + 
								'&issueQty_' + k + '=' + isssue_qty +
								'&yarnDtlsId_' + k + '=' + yarn_dtls_id ; 
				}
				catch(e) 
				{
					//got error no operation
				}
			});


			
			var data="action=save_update_delete&operation="+operation+
							'&tot_row='+j+
							'&yarn_color_row='+k+
							get_submitted_data_string('cbo_floor*txt_issue_date*cbo_source*txt_operator_id*cbo_working_company*cbo_working_location*cbo_company_name*cbo_location*cbo_bodypart_type*txt_challan_no*txt_system_id*txt_lot_ratio*txt_job_no*txt_remarks*garments_nature',"../")+dataString+dataString1;
		
		freeze_window(operation);
		http.open("POST","requires/bundle_receive_from_knitting_floor_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_issue_to_knitting_Reply_info;
	}
}
  
function fnc_issue_to_knitting_Reply_info()
{
 	if(http.readyState == 4) 
	{
		release_freezing();return;
		// alert(http.responseText);
		//var variableSettings=$('#sewing_production_variable').val();
		//var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	//	var item_id=$('#cbo_item_name').val();
		//var country_id = $("#cbo_country_name").val();
		
		var reponse=http.responseText.split('**');		 
		if(reponse[0]==15) 
		{ 
			 setTimeout('fnc_issue_print_embroidery_entry('+ reponse[1]+')',4000); 
		}
		else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			if(reponse[3]){alert("Receive Found Bundle List : "+reponse[3]+" This Bundle Not Any Change.");}
			show_msg(trim(reponse[0]));
			
			document.getElementById('txt_system_id').value = reponse[1];
			document.getElementById('txt_challan_no').value = reponse[2];
			set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,1);	
			$('#txt_operator_id').attr('disabled','true');
			$('#cbo_location').attr('disabled','true');

		}
		if(reponse[0]!=15)
		{
		  release_freezing();
		}
 	}
} 


function openmypage_sysNo()
{
	
	var title = 'Challan Selection Form';	
	var page_link = 'requires/bundle_receive_from_knitting_floor_controller.php?action=challan_no_popup';
		
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=390px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var mst_id=this.contentDoc.getElementById("hidden_mst_id").value;//po id
		if(mst_id!="")
		{ 
			freeze_window(5);
			//reset_form('printembro_1','list_view_country*breakdown_td_id','','','txt_issue_date,<? echo date("d-m-Y"); ?>','');

			get_php_form_data(
								mst_id,
								"populate_data_from_issue", 
								"requires/bundle_receive_from_knitting_floor_controller"
							 );

			$('#txt_operator_id').attr('disabled','true');
			$('#cbo_location').attr('disabled','true');
			show_list_view(
							$("#txt_lot_ratio").val()+'_'+$("#txt_system_id").val()+'_'+$("#cbo_bodypart_type").val(),
							'show_dtls_listview_update',
							'bundle_list_view',
							'requires/bundle_receive_from_knitting_floor_controller',
							''
						  );
			
						  
			show_list_view(
							$("#txt_lot_ratio").val()+'_'+$("#txt_system_id").val(),
							'show_dtls_yarn_listview_update',
							'yarn_list_view',
							'requires/bundle_receive_from_knitting_floor_controller',
							''
						  );

			var tot_row=$('#tbl_details tbody tr').length; 
			$('#hidden_row_number').val(tot_row);
			var all_machine=trim(return_global_ajax_value( $("#cbo_floor").val(), 'load_machine', '', 'requires/bundle_receive_from_knitting_floor_controller'));
			str_machineline =eval(all_machine);
			fnc_load_machine();
			set_button_status(1, permission, 'fnc_issue_print_embroidery_entry',1,0);
	
			release_freezing();
		}
	}
	
}//end function


function checkMachineId(id)
{
	var data=$("#txt_machine_no_"+id).val().split(":");
	$("#txt_machine_no_"+id).val(data[1]);
	$("#txt_machine_id_"+id).val(data[0]);
}



function generate_report_file(data,action,page)
{
	window.open("requires/print_embro_delivery_entry_controller.php?data=" + data+'&action='+action, true );
}





</script>
</head>
<body onLoad="set_hotkey()">
 <div style="width:100%;">
  	<? echo load_freeze_divs ("../",$permission);  ?>
    <div style=" float:left" align="left"> 
 		
        <form name="printembro_1" id="printembro_1" method="" autocomplete="off" >
			<fieldset style="width:950px;">
        		<legend>Master Part</legend>
                	<table width="920">
                        <tr>
                            <td align="right" colspan="4">Receive Id</td>
                            <td colspan="4"> 
                            	<input 
                                	name="txt_challan_no" 
                                    id="txt_challan_no" 
                                    class="text_boxes" 
                                    type="text"
                                 	style="width:127px" 
                                    onDblClick="openmypage_sysNo()" 
                                 	placeholder="Double click to search" />
                                 
                            	<input 
                                	name="txt_system_id" 
                                    id="txt_system_id" 
                                    type="hidden" />
                                    
                                <input 
                                	name="txt_lot_ratio" 
                                    id="txt_lot_ratio" 
                                    type="hidden" />
                                <input 
                                	name="txt_lot_ratio_id" 
                                    id="txt_lot_ratio_id" 
                                    type="hidden" />
                                <input 
                                	name="txt_job_no" 
                                    id="txt_job_no" 
                                    type="hidden" />
                            </td>
                        </tr>
                    	
                    	<tr>
							<td width="120" class="must_entry_caption">Bodypart Type</td>
							<td >
							<? 
							$bodypat_type_arr=array(1=>"Main Body",
													2=>"Accessories");
											
							echo create_drop_down( "cbo_bodypart_type", 
											140, 
											$bodypat_type_arr,
											"", 
											0,
											"-- Select Body_art Type --",
											 1,
											 "",0 );
							?>
							</td>
							<td width="100" class="must_entry_caption" id="td_caption">Operator ID</td>
	                        <td  width=""> 
	                        	<input 
	                            	name="txt_operator_id" 
	                                placeholder="Browse/Scan" 
	                                onDblClick="openmypage_operator()"  
	                                id="txt_operator_id" 
	                                class="text_boxes" 
	                                style="width:127px" />
	                        </td>
	                        <td>Operator Name</td>
	                        <td>
	                        	<input 
	                            	name="txt_operation_name" 
	                                id="txt_operation_name" 
	                                class="text_boxes" 
	                                type="text" 
	                                style="width:130px"
	                                disabled />
	                        </td>
	                        <td width="80" class="must_entry_caption">Issue Date</td>
	                        <td width="110"> 
	                        	<input 
	                            	type="text" 
	                                name="txt_issue_date" 
	                                id="txt_issue_date" 
	                                value="<? echo date("d-m-Y")?>" 
	                                class="datepicker" 
	                                style="width:127px;"  />
	                        </td>                        
                    	</tr>
                        <tr>
                            <td width="80" class="must_entry_caption" id="td_caption">Barcode No</td>
                            <td  width="110"> 
                            	<input 
                                	name="txt_bundle_no" 
                                    placeholder="Browse/Write/Scan" 
                                	onDblClick="openmypage_bundle()" 
                                    id="txt_bundle_no" 
                                    class="text_boxes"
                                	style="width:127px" />
                            </td>
                        	<td width="80" class="must_entry_caption" id="td_caption">Re-Scan Barcode</td>
                        	<td  width="110"> 
                        		<input 
                                	name="txt_bundle_rescan" 
                                    placeholder="Browse/Write/Scan" 
                                    onDblClick="openmypage_bundle_rescan()"  
                                    id="txt_bundle_rescan" 
                                    class="text_boxes" 
                                    style="width:127px" />
                        </td>
                        <td  width="80" class="must_entry_caption">Floor</td>
                        <td width="110" id="floor_td">
							<? 
                            	echo create_drop_down( "cbo_floor", 140, $blank_array,"", 1,
								 "-- Select Floor --", $selected, "",1 );
                            ?>
                        </td>
                        <td class="must_entry_caption">Source</td>
                        <td>
						<? 
                        	echo create_drop_down( "cbo_source", 140, $knitting_source,"", 1, "-- Select Source --",
							 	$selected, "", 1, '1,3' );
                        ?>
                        </td>
					</tr>
                    <tr>                       
                        <td class="must_entry_caption">W. Company</td>
                        <td id="knitting_com">
                        <? 
                        	echo create_drop_down( "cbo_working_company", 140, $blank_array,"", 1,
							"-- Select --", $selected, "",1 );
                        ?>
                        </td>
                        <td class="must_entry_caption">WC. Location</td>
                        <td id="working_location_td">
                        <? 
                        	echo create_drop_down( "cbo_working_location", 140, $blank_array,"", 1,
								"-- Select Location --", $selected, "",1 );
                        ?>
                        </td>
                        <td width="" class="must_entry_caption">LC Company</td>
                        <td>
                        <? 
							$sql_com="select 
											id,
											company_name
										from 
											lib_company comp
						 				where 
											status_active =1 and 
											is_deleted=0 
											$company_cond 
										order by company_name";
										
                        	echo create_drop_down( "cbo_company_name",
													140, 
													$sql_com,
													"id,company_name", 
													1,
													"-- Select --", 
													$selected,
													 "",1 );
                        ?>
                        </td>
                        <td class="must_entry_caption">Location</td>
                        <td id="location_td">
                        <? 
                        	echo create_drop_down( "cbo_location", 
													140, 
													$blank_array,
													"", 
													1,
													"-- Select Location --",
													 $selected,
													 "",1 );
                        ?>
                        </td>
                    </tr>
                  
                    <tr>
                        <td class="must_entry_caption">Remarks</td>
                        <td colspan="7">
                        	<input 
                            	name="txt_remarks" 
                                id="txt_remarks" 
                                class="text_boxes" 
                                type="text" 
                                style="width:820px" />
                        </td>
                    </tr>
                   
                </table>
			</fieldset> <br />
               
            <fieldset style="">
				<legend>Bundle List</legend>
                	<fieldset style="">
                    	<div id="bundle_list_view">
                        <table 
                            cellpadding="0" 
                            width="" 
                            cellspacing="0" 
                            border="1" 
                            class="rpt_table" 
                            rules="all">
                            
                            <thead>
                                <th width="30">SL</th>
                                <th width="100">Bundle No</th>
                                <th width="100">MC No</th>
                                <th width="60">G. Color</th>
                                <th width="70">Size</th>
                                <th width="65">Bundle Qty. (Pcs)</th>
                                <th width="90">From</th>
                                <th width="120">To</th>
                                <th width="100">Yarn Color</th>
                                <th width="80">Bndl.  Cons. Qty. (Lbs)</th>
                                <th width="50">Year</th>
                                <th width="60">Job No</th>
                                <th width="65">Buyer</th>
                                <th width="90">Order No</th>
                                <th width="120">Gmts. Item</th>
                                <th width="100">Country</th>
                                <th></th>
                            </thead>
                        </table>
                        <div 
                            style="width:920px;max-height:250px;overflow-y:scroll" 
                            align="left">    
                            <table 
                                cellpadding="0" 
                                width="900" 
                                cellspacing="0" 
                                border="1" 
                                class="rpt_table" 
                                rules="all" 
                                id="tbl_details">      
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                	</fieldset>
                    <br />
               		<fieldset style="">
                    	<div id="yarn_list_view">
                            <table 
                                cellpadding="0" 
                                width="830" 
                                cellspacing="0" 
                                border="1" 
                                class="rpt_table" 
                                rules="all">
                                
                                <thead>
                                    <th width="30">SL</th>
                                    <th width="100">Sample Color</th>
                                    <th width="200">Yarn Color</th>
                                    <th width="100">Required Qty(Lbs)</th>
                                    <th width="100">Returnable Qty (Lbs)</th>
                                    <th width="100">Issue Qty. (Lbs)</th>
                                    <th width="100">Current Short/Excess</th>
                                    <th width="">Issue Balance</th>
                                   
                                </thead>
                            </table>
                            <div 
                                style="width:850px;max-height:250px;overflow-y:scroll" 
                                align="left">    
                                <table 
                                    cellpadding="0" 
                                    width="820" 
                                    cellspacing="0" 
                                    border="1" 
                                    class="rpt_table" 
                                    rules="all" 
                                    id="tbl_yarn_details">      
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                	</fieldset>
               		<table cellpadding="0" cellspacing="1" width="100%">
               		<tr>
                        <td align="center" colspan="9" valign="middle" class="button_container">
                            <?
								$date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_issue_print_embroidery_entry", 0,1 ,
									"reset_form('printembro_1','list_view_country','',
									'txt_issue_date,".$date."','pageReset();')",1); 
                            ?>
                          	<input 
                            	type="hidden"
                                name="hidden_row_number" 
                                id="hidden_row_number"> 
                                
                            
                            	
                        </td>
                        <td>&nbsp;</td>				
                    </tr>
               	</table>
            </fieldset>
        </form>
    </div>
	<div 
    	id="list_view_country" 
        style="	width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px;
        		position:relative; margin-left:10px">
 	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>