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

function openmypage_issue_popup(page_link,title)
{
	var title='Bundle Search';
	var page_link='	requires/bundle_receive_from_knitting_floor_controller.php?action=issue_popup';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=890px,height=370px,center=1,resize=0,scrolling=0', '../')
								
	emailwindow.onclose=function()
	{
		var theform				=this.contentDoc.forms[0];
		var hidden_system_id	=this.contentDoc.getElementById("hidden_system_id").value;

		$("#txt_issue_id").val(hidden_system_id);
		
		if (hidden_system_id!="")
		{ 
			get_php_form_data(hidden_system_id, "populate_data_from_yarn_issue", "requires/bundle_receive_from_knitting_floor_controller");
			create_row('',1,$("#txt_lot_ratio").val(),$("#txt_bodyPart_id").val(),$("#txt_issue_id").val());
		}
	}
	
}//end function

$('#txt_issue_no').live('keydown', function(e) {

	if (e.keyCode === 13) 
	{
		e.preventDefault();
		var txt_issue_no=trim($('#txt_issue_no').val());
		
		var challan_duplicate=return_ajax_request_value( txt_issue_no,"challan_duplicate_check", "requires/bundle_receive_from_knitting_floor_controller");
		var ex_challan_duplicate=challan_duplicate.split("_");
		if(ex_challan_duplicate[0]==2) 
		{
			var al_msglc="Issue ID '"+txt_issue_no+"' Found in Receive  Challan '"+trim(ex_challan_duplicate[1])+"'";
			alert(al_msglc);
			$('#txt_issue_no').val('');
			return;
		}
		else
		{
			get_php_form_data(ex_challan_duplicate[1], "populate_data_from_yarn_issue", "requires/bundle_receive_from_knitting_floor_controller");
			create_row('',1,$("#txt_lot_ratio").val(),$("#txt_bodyPart_id").val(),$("#txt_issue_id").val());
		}			
	}
});


function create_row(bundle_nos,vscan,hidden_cutting_no,bodypart_ids,issue_id)
{
	var bodypart_type = $("#cbo_bodypart_type").val();
	if(vscan==1)
	{
	
		show_list_view(hidden_cutting_no+'_'+bundle_nos+'_'+bodypart_ids+'_'+issue_id+'_'+bodypart_type, 'show_dtls_listview_from_issue', 'bundle_list_view', 'requires/bundle_receive_from_knitting_floor_controller', '');
		show_list_view(hidden_cutting_no+'_'+bundle_nos+'_'+issue_id, 'show_dtls_yarn_listview', 'yarn_list_view', 'requires/bundle_receive_from_knitting_floor_controller', '');
	}
	else
	{
		get_php_form_data(bundle_nos, "populate_data_from_yarn_lot_bundle", "requires/bundle_receive_from_knitting_floor_controller");
		show_list_view(bundle_nos+'_'+bodypart_type, 'show_dtls_listview_bundle', 'bundle_list_view', 'requires/bundle_receive_from_knitting_floor_controller', '');
		show_list_view($("#txt_lot_ratio").val()+'_'+bundle_nos, 'show_dtls_yarn_listview', 'yarn_list_view', 'requires/bundle_receive_from_knitting_floor_controller', '');
	}		
	//calculate_yarn_qty();

	release_freezing();
}

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

function fnc_total_receive_qty()
{
	var total_bundle_qty=0; var total_wastage_qty=0; var total_bundle_qtygm=0; var total_wastageQty=0;
	$("#tbl_details").find('tbody tr').each(function()
	{
		var receive_qtygm=$(this).find('input[name="txt_receive_qtygm[]"]').val()*1;
		var receive_qty=receive_qtygm*0.00220462;
		
		$(this).find('input[name="txt_receive_qty[]"]').val(receive_qty.toFixed(4))*1;
		var issue_qty=$(this).find("td:eq(8)").text()*1;
		//alert(issue_qty)
		var wastageQty=issue_qty-receive_qty;
		$(this).find("td:eq(12)").text(wastageQty.toFixed(4));
		total_bundle_qty+=receive_qty;
		total_bundle_qtygm+=receive_qtygm;
		total_wastageQty+=wastageQty;
	});
	$("#total_wst_consmg").text(total_bundle_qtygm.toFixed(4));
	$("#total_wst_cons").text(total_bundle_qty.toFixed(4));
	$("#total_wastageQty").text(total_wastageQty.toFixed(4));

	var total_percentage=0;
	$("#tbl_yarn_details").find('tbody tr').each(function()
	{
		total_percentage =total_percentage+$("#percentage_"+$(this).find('input[name="hidden_yarn_color[]"]').val()).val()*1;
	});
	var sl=1;
	var avg_cos = 0;
	var prev_rcv_qty = 0;
	$("#tbl_yarn_details").find('tbody tr').each(function()
	{
		/*var color_percentage=$("#percentage_"+$(this).find('input[name="hidden_yarn_color[]"]').val()).val()*1;
		var actual_percentage=(total_percentage) ? color_percentage/total_percentage : 0;
		var color_qty		=(total_bundle_qty*actual_percentage);*/

		var prev_rcv_qty=$("#prev_rcv_qty_"+sl).val()*1;
		var avg_cos=$("#hidden_size_set_cons_"+sl).val()*1;
		var color_qty		=(total_bundle_qty*avg_cos);
		var tot_color_qty = color_qty+prev_rcv_qty
		// alert(avg_cos+'*'+total_bundle_qty+'='+color_qty);
		$(this).find("td:eq(5)").text(tot_color_qty.toFixed(4)); // cuml. qty
		$(this).find("td:eq(6)").text(color_qty.toFixed(4)); // cur rcv qty
		sl++;
	});

	fnc_total_issue_balance();	
}

function fnc_total_issue_balance()
{
	var total_receive=0; var total_return=0; var total_wastage=0; var total_cuml_rcv_qty=0;

	$("#tbl_yarn_details").find('tbody tr').each(function()
	{
		var cuml_rcv_qty  	=$(this).find("td:eq(5)").text()*1;// cuml. qty
		var receive_qty  	=$(this).find("td:eq(6)").text()*1;// cur rcv qty
		var issue_qty		=$(this).find("td:eq(4)").text()*1;
		var returnable_qty 	=$(this).find('input[name="txt_returnable_qty[]"]').val()*1;	
		// var issue_balance  	=receive_qty-(issue_qty+returnable_qty);
		var issue_balance  	=issue_qty-(receive_qty+returnable_qty);
			total_cuml_rcv_qty  	+=cuml_rcv_qty;
			total_receive  	+=receive_qty;
			total_return  	+=returnable_qty;
			total_wastage  	+=issue_balance;

		$(this).find("td:eq(8)").text(issue_balance.toFixed(4))

	});

	$("#total_cuml_rcv_qty").text(total_cuml_rcv_qty.toFixed(4));
	$("#total_receive_qty").text(total_receive.toFixed(4));
	$("#total_returnable_qty").text(total_return.toFixed(4));	
	$("#total_wastage_qty").text(total_wastage.toFixed(4));
}

function fnc_check_knit_qty(id){
	$("#tbl_details").find('tbody tr').each(function()
	{
		var bundle_qty  	=$(this).find("td:eq(6)").text()*1;
		var receive_qty		=$(this).find('input[name="txt_knit_qty[]"]').val()*1;
		if(bundle_qty<receive_qty)  $(this).find('input[name="txt_knit_qty[]"]').val('');
 
	});	
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

function fnc_receive_from_knitting_entry(operation)
{
	freeze_window(operation);
	if(operation==2)
	{
		alert("Delete Restricted.")
		release_freezing();
		return;
	}
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_lot_ratio').val()+'*'+$('#txt_job_no').val()+'*'+report_title, 'emblishment_issue_print', 'requires/bundle_receive_from_knitting_floor_controller');
		release_freezing();
		return;
	}
	
	if ( form_validation('cbo_floor*txt_receive_date*cbo_source*txt_operator_id*cbo_working_company*cbo_working_location*cbo_company_name*cbo_location*txt_bodypart_name',
		'Floor*Issue Date*Source*Operator ID*W. Company*WC. Location*LC Company*Location*Body Part')==false )
	{
		release_freezing();
		return;
	}
	
	if(operation==0 || operation==1 || operation==2)
	{
		var j 			=0; 
		var k 			=0; 
		var dataString 	='';
		var dataString1	='';	
		var errorCheck 	= 0;

		$("#tbl_details").find('tbody tr').each(function()
		{
			var tr_id 			=$(this).find('input[name="trId[]"]').val();

			if(errorCheck==0)
			{

				if (form_validation('txt_knit_qty_'+tr_id+'*txt_receive_qty_'+tr_id,'knit Qty.(Pcs)*Bundle Weight Rec. (Lbs)')==false) 
				{
					errorCheck = 1;
					release_freezing();
					return;
				}

				var bundleNo 		=$(this).find("td:eq(1)").text();
				var barcodeNo 		=$(this).find("td:eq(2)").text();
				var colorId 		=$(this).find('input[name="txt_color_id[]"]').val();
				var sizeId 			=$(this).find('input[name="txt_size_id[]"]').val();
				var orderId 		=$(this).find('input[name="txt_order_id[]"]').val();
				var countryId 		=$(this).find('input[name="txt_country_id[]"]').val();
				var gmtsitemId 		=$(this).find('input[name="txt_gmt_item_id[]"]').val();
				var machine_id 		=$(this).find('input[name="txt_machine_id[]"]').val();
				var bundle_consgm 	=$(this).find('input[name="txt_receive_qtygm[]"]').val();	
				var bundle_cons 	=$(this).find('input[name="txt_receive_qty[]"]').val();				
				var qty 			=$(this).find('input[name="txt_knit_qty[]"]').val();
				var color_size_id	=$(this).find('input[name="txt_colorsize_id[]"]').val();
				
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
								'&bundle_consgm_' + j + '=' + bundle_consgm + 
								'&bundle_cons_' + j + '=' + bundle_cons + 
								'&machine_id_' + j + '=' + machine_id + 		
								'&barcodeNo_' + j + '=' + barcodeNo+
								'&colorSizeId_' + j + '=' + color_size_id;  							
				}
				catch(e) 
				{
					alert("There is some problem.");
					release_freezing();
					return;
				}
			}
		});
			

		if(errorCheck == 1){
			release_freezing();
			return;	
		}

		if(j<1)
		{
			alert('No data Found.');
			release_freezing();
			return;
		}
		
		$("#tbl_yarn_details").find('tbody tr').each(function()
		{
			var yarn_color 		=$(this).find('input[name="hidden_yarn_color[]"]').val();
			var sample_color 	=$(this).find('input[name="hidden_sample_color[]"]').val();
			var required_qty	=$(this).find("td:eq(3)").text()*1;
			var isssue_qty 		=$(this).find("td:eq(4)").text()*1;					
			var receive_qty 	=$(this).find("td:eq(6)").text()*1;
			var returanable_qty	=$(this).find('input[name="txt_returnable_qty[]"]').val();
			var wastage	 		=$(this).find("td:eq(8)").text()*1;				
			var yarn_dtls_id 	=$(this).find('input[name="hidden_yarn_dtls_id[]"]').val();				
			
			try 
			{
				k++;
				
				dataString1+='&yarnColor_' + k + '=' + yarn_color + 
							'&sampleColor_' + k+ '=' + sample_color + 
							'&requiredQty_' + k + '=' + required_qty + 
							'&returnableQty_' + k + '=' + returanable_qty + 
							'&receiveQty_' + k + '=' + receive_qty + 
							'&wastage_' + k + '=' + wastage  + 
							'&issueQty_' + k + '=' + isssue_qty +
							'&yarnDtlsId_' + k + '=' + yarn_dtls_id ; 
			}
			catch(e) 
			{
				//got error no operation
				release_freezing();
			}
		});
			
		var data="action=save_update_delete&operation="+operation+ '&tot_row='+j+ '&yarn_color_row='+k+get_submitted_data_string('cbo_floor*txt_receive_date*cbo_source*txt_operator_id*hidden_sup_id*cbo_working_company*cbo_working_location*cbo_company_name*cbo_location*cbo_bodypart_type*txt_challan_no*txt_system_id*txt_lot_ratio*txt_job_no*txt_remarks*garments_nature*txt_issue_id*txt_bodyPart_id*txt_size_set_no',"../")+dataString+dataString1;
		
		
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
		var reponse=http.responseText.split('**');		 
		if(reponse[0]==15) 
		{ 
			 setTimeout('fnc_receive_from_knitting_entry('+ reponse[1]+')',4000); 
		}
		else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			if(reponse[4]){alert("Receive Found Bundle List : "+reponse[4]+" This Bundle Not Any Change.");}
			show_msg(trim(reponse[0]));
			
			document.getElementById('txt_system_id').value = reponse[1];
			document.getElementById('txt_challan_no').value = reponse[2];
			set_button_status(1, permission, 'fnc_receive_from_knitting_entry',1,1);	
			$('#txt_operator_id').attr('disabled','true');
			$('#cbo_location').attr('disabled','true');
			$('#txt_bodypart_name').attr('disabled','true');
			$('#txt_issue_no').attr('disabled','true');

			if(reponse[0]==0)
			{
				var details_id=reponse[3].split('#');
				for(var i=1;i<details_id.length;i++)
				{
					var data=details_id[i-1].split("_");
					document.getElementById('hidden_yarn_dtls_id_'+i).value=data[1];	
				}				
			}
			release_freezing();
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
			//reset_form('printembro_1','list_view_country*breakdown_td_id','','','txt_receive_date,<? echo date("d-m-Y"); ?>','');

			get_php_form_data(mst_id, "populate_data_from_receive", "requires/bundle_receive_from_knitting_floor_controller");

			$('#txt_operator_id').attr('disabled','true');
			$('#cbo_location').attr('disabled','true');
			show_list_view($("#txt_lot_ratio").val()+'_'+$("#txt_system_id").val()+'_'+$("#cbo_bodypart_type").val()+'_'+$("#txt_bodyPart_id").val(), 'show_dtls_listview_update', 'bundle_list_view', 'requires/bundle_receive_from_knitting_floor_controller', '');
			show_list_view($("#txt_lot_ratio").val()+'_'+$("#txt_system_id").val()+'_'+$("#txt_issue_id").val(), 'show_dtls_yarn_listview_update', 'yarn_list_view', 'requires/bundle_receive_from_knitting_floor_controller', '');
			set_button_status(1, permission, 'fnc_receive_from_knitting_entry',1,0);
			release_freezing();
		}
	}
	
}//end function

function pageReset()
{
	location.reload();
}

function generate_report_file(data,action,page)
{
	window.open("requires/bundle_receive_from_knitting_floor_controller.php?data=" + data+'&action='+action, true );
}

function fn_deleteRow(id)
{
	// alert(id);
	$("#minusButton_"+id).closest('tr').remove();
	// calculate_defect_qty();
	rearrange_serial_no();
}

function rearrange_serial_no()
{
	var i=1;
	$("#tbl_details").find('tbody tr').each(function()
	{
		$(this).find("td:eq(0)").text(i);
		i++;
	});
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
                            <td align="right" colspan="4">Receive ID</td>
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
                                	name="txt_issue_id" 
                                    id="txt_issue_id" 
                                    type="hidden" />
                                <input 
                                	name="txt_job_no" 
                                    id="txt_job_no" 
                                    type="hidden" />
                            </td>
                        </tr>
                    	
                        <tr>
                        	<td width="120" class="must_entry_caption">Body Part</td>
							<td>
								<input 
									type="text" 
									name="txt_bodypart_name" 
									id="txt_bodypart_name" 
									class="text_boxes" 
									style="width:127px;"	
									readonly />
									<!--placeholder="Double Click To Search" 
									onDblClick="openmypage_party();" -->
                            	<input 
                            		type="hidden" 
                            		name="txt_bodyPart_id" 
                            		id="txt_bodyPart_id" />

                            	<input 
                            		type="hidden" 
                            		name="cbo_bodypart_type" 
                            		id="cbo_bodypart_type" />
							</td>
							<td width="120" class="must_entry_caption">Issue ID</td>
	                        <td>
	                        	<input 
	                            	name="txt_issue_no" 
	                                id="txt_issue_no" 
	                                class="text_boxes" 
	                                type="text"
	                                placeholder="Browse or Scan"
	                                onDblClick="openmypage_issue_popup();" 
	                                style="width:127px" />
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
	                        <td >Operator Name</td>
	                        <td width="110">
	                        	<input 
	                            	name="txt_operation_name" 
	                                id="txt_operation_name" 
	                                class="text_boxes" 
	                                type="text" 
	                                style="width:130px"
	                                disabled />
	                        </td>
	                                                
                    	</tr>


                    	<tr>
                    		<td class="must_entry_caption" id="td_caption">Barcode No</td>
                            <td  width="110"> 
                            	<input 
                                	name="txt_bundle_no" 
                                    placeholder="Browse/Write/Scan" 
                                	onDblClick="openmypage_bundle()" 
                                    id="txt_bundle_no" 
                                    class="text_boxes"
                                	style="width:127px"
                                	disabled="" />
                            </td>
                        	<td width="120" class="must_entry_caption" id="td_caption">Re-Scan Bar.</td>
                        	<td  width="110"> 
                        		<input 
                                	name="txt_bundle_rescan" 
                                    placeholder="Browse/Write/Scan" 
                                    onDblClick="openmypage_bundle_rescan()"  
                                    id="txt_bundle_rescan" 
                                    class="text_boxes" 
                                    style="width:127px" 
                                    disabled=""/>
                        	</td>
                    		<td class="must_entry_caption">Receive Date</td>
	                        <td width="110"> 
	                        	<input 
	                            	type="text" 
	                                name="txt_receive_date" 
	                                id="txt_receive_date" 
	                                value="<? echo date("d-m-Y")?>" 
	                                class="datepicker" 
	                                style="width:127px;"  />
	                        </td>

	                        <td width="" class="must_entry_caption">LC Company</td>
	                        <td>
	                        <? 
								$sql_com="select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name";
											
	                        	echo create_drop_down( "cbo_company_name", 140, $sql_com, "id,company_name", 1, "-- Select --", $selected, "",1 );
	                        ?>
	                        </td>  						
						                       
	                	</tr>
	                    <tr>
	                    	<td class="must_entry_caption">Location</td>
	                        <td id="location_td">
	                        <? 
	                        	echo create_drop_down( "cbo_location", 140, $blank_array, "", 1, "-- Select Location --", $selected, "",1 );
	                        ?>
	                        </td>
	                    	<td class="must_entry_caption">Source</td>
	                        <td>
							<? 
	                        	echo create_drop_down( "cbo_source", 140, $knitting_source,"", 1, "-- Select Source --",
								 	$selected, "", 1, '1,3' );
	                        ?>
	                        </td>
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
	                        
						</tr>
	                    <tr>                       
	                    
	                    <tr>

	                        <td  class="must_entry_caption">Floor</td>
	                        <td width="110" id="floor_td">
								<? 
	                            	echo create_drop_down( "cbo_floor", 140, $blank_array,"", 1,
									 "-- Select Floor --", $selected, "",1 );
	                            ?>
	                        </td>                   		
                    		<td class="must_entry_caption">Issue ID Qty.</td>
	                        <td width="110"> 
	                        	<input 
	                            	name="txt_issue_qty" 
	                                id="txt_issue_qty" 
	                                class="text_boxes" 
	                                type="text" 
	                                style="width:130px"
	                                disabled />
	                        </td>

	                        <td width="" class="must_entry_caption">Cumu. Bal. Qty.</td>
	                        <td>
	                        	<input 
	                            	name="txt_issue_balance" 
	                                id="txt_issue_balance" 
	                                class="text_boxes" 
	                                type="text" 
	                                style="width:130px"
	                                disabled />
	                        </td>
	                        <td class="">Size Set No</td>
	                        <td >
	                        	<input 
	                            	name="txt_size_set_no" 
	                                id="txt_size_set_no" 
	                                class="text_boxes" 
	                                type="text" 
	                                style="width:127px"
	                                disabled />
	                        </td>              
	                	</tr>   
	                       
	                    </tr>
	                  
	                    <tr>
	                    	<td class="">Supervisor Name</td>
	                        <td>
	                        	<input 
	                            	name="txt_sup_name" 
	                                id="txt_sup_name" 
	                                class="text_boxes" 
	                                type="text" 
	                                style="width:127px" readonly disabled="true" />
	                            <input type="hidden" name="hidden_sup_id" id="hidden_sup_id">    
	                        </td>
	                        <td class="must_entry_caption">Remarks</td>
	                        <td colspan="5">
	                        	<input 
	                            	name="txt_remarks" 
	                                id="txt_remarks" 
	                                class="text_boxes" 
	                                type="text" 
	                                style="width:600px" />
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
                            width="1400" 
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
                                <th width="100">Knit Qty. (Pcs)</th>
                                <th width="100">Incl. Process Loss (Lbs)</th>
                                <th width="100">Bundle Weight Rec. (Lbs)</th>
                                <th width="100">Wastage Qty (Lbs)</th>
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
                            style="width:1400px;max-height:250px;overflow-y:scroll" 
                            align="left">    
                            <table 
                                cellpadding="0" 
                                width="1380" 
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
                                width="930" 
                                cellspacing="0" 
                                border="1" 
                                class="rpt_table" 
                                rules="all">
                                
                                <thead>
                                    <th width="30">SL</th>
                                    <th width="100">Sample Color</th>
                                    <th width="200">Yarn Color</th>
                                    <th width="100">Required Qty(Lbs)</th>
                                    <th width="100">Issue Qty. (Lbs)</th>
                                    <th width="100">Cuml. Rcv Qty (Lbs)</th>
                                    <th width="100">Receive Qty (Lbs)</th>
                                    <th width="100">Returnable Qty (Lbs)</th>
                                    <th width="">Wastage (Lbs)</th>
                                   
                                </thead>
                            </table>
                            <div 
                                style="width:950px;max-height:250px;overflow-y:scroll" 
                                align="left">    
                                <table 
                                    cellpadding="0" 
                                    width="920" 
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
                                echo load_submit_buttons( $permission, "fnc_receive_from_knitting_entry", 0,1 , "reset_form('printembro_1','list_view_country','', 'txt_receive_date,".$date."','pageReset();')",1);
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