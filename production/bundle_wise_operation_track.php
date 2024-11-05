<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Bundle Wise Operation Track
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	25-03-2020
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
//echo integration_params(2);die;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Bundle Wise Operation Track","../", 1, 1, $unicode,'','');
?>
	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function openmypage_bulletin()
	{ 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/bundle_wise_operation_track_controller.php?action=bulletin_popup','Operation Bulletin Search', 'width=1000px,height=350px,center=1,resize=1,scrolling=0','')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("system_id").value;
			//alert(theemail.value); return;
			var response=theemail.split('_');
			if (theemail!="")
			{
				freeze_window(5);
				$('#txt_bulletin_id').val(theemail);
				get_php_form_data( theemail, "populate_data_from_ws_popup", "requires/bundle_wise_operation_track_controller" );
				load_drop_down( 'requires/bundle_wise_operation_track_controller',theemail, 'load_drop_down_operation', 'operation_td');
				release_freezing();
			}
		}
	}
	
	function fnc_ws_data(val)
	{
		var bulletin_id=$('#txt_bulletin_id').val();
		var wsdata = return_ajax_request_value(bulletin_id+'_'+val, 'load_wsdata', 'requires/bundle_wise_operation_track_controller');
		var dataws=wsdata.split("_");
		
		$('#txt_ws_smv').val(dataws[0]);
		$('#txt_resource').val(dataws[2]);
		$('#txt_resource_id').val(dataws[1]);
		$('#txt_wtrack').val(dataws[3]);
	}
	
	function openmypage_operator(inc)
	{
		var page_link='requires/bundle_wise_operation_track_controller.php?action=operator_popup';
		var title='Operator Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=390px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{				
			var employeeData=(this.contentDoc.getElementById("hidden_emp_number").value);//po id
			var employee_data=employeeData.split("_");
			//alert(employee_data[1]+'-'+employee_data[2]);
			$("#txtOperatorId_"+inc).val(employee_data[1]);
			$("#operatorName_"+inc).text(employee_data[2]);	
			fnc_copyval(1,employeeData,inc);
		}
	}//end function
	
	/*$('#txt_operator_id').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var txt_operator_id=trim($('#txt_operator_id').val().toUpperCase());
			var flag=1;
		
			if(flag==1)
			{
				get_php_form_data( txt_operator_id, "populate_operator_data", "requires/bundle_wise_operation_track_controller");
			}
		}
	});*/
	
	$('#txt_barcode_no').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			if(form_validation('txt_bulletin_no*cbo_operation_id*txt_production_date','Opr. Bulletin ID*Operation*Production Date')==false)
			{
				return;
			}
			e.preventDefault();
			var txt_barcode_no=trim($('#txt_barcode_no').val().toUpperCase());
			var flag=1;
			$("#tbl_details").find('tbody tr').each(function()
			{
				var barcodeNo=$(this).find("td:eq(1)").text();
				var bundleNo=$(this).find("td:eq(3)").text();
				if(txt_barcode_no==barcodeNo){
					
					alert("Barcode No: "+barcodeNo+" already scan, try another one.");
					$('#txt_barcode_no').val('');
					flag=0;
					return false;
				}
			});
		
			if(flag==1)
			{
				fnc_duplicate_bundle(txt_barcode_no);
			}
		}
	});

	function fnc_duplicate_bundle(barcodeNo)
    {
        var challan_duplicate ='';// return_ajax_request_value(barcodeNo, "challan_duplicate_check", "requires/bundle_wise_operation_track_controller");
        //var ex_challan_duplicate = challan_duplicate.split("_");
       
		if ( trim( challan_duplicate)!= '')
        {
            alert(trim(challan_duplicate));
            $('#txt_barcode_no').val('');
            return;
        }
        else
        {
            create_row(barcodeNo,'scan','');
			var tot_row=$('#tbl_details tbody tr').length; 
			if( (tot_row*1) <2)
			{
				get_php_form_data( barcodeNo+'_'+$('#cbo_working_company').val(), "populate_mst_data", "requires/bundle_wise_operation_track_controller");
				$('#txt_bulletin_no').attr('disabled','true');
				$('#cbo_operation_id').attr('disabled','true');
			}
        }
        $('#txt_barcode_no').val('');
    }

	/*function fnc_duplicate_bundle(barcode_no)
	{
		var challan_duplicate=return_ajax_request_value( barcode_no,"challan_duplicate_check", "requires/bundle_wise_operation_track_controller");
		var ex_challan_duplicate=challan_duplicate.split("_");
		if(ex_challan_duplicate[0]==2) 
		{
			var alt_str=ex_challan_duplicate[1].split("*");
			var al_msglc="Bundle No '"+trim(alt_str[0])+"' Found in Challan No '"+trim(alt_str[1])+"'";
			alert(al_msglc);
			$('#txt_barcode_no').val('');
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
			if(ex_challan_duplicate[0]==3)
			{
				//alert("Body Part "+ex_challan_duplicate[1]+" Not Already Receive");
				//return;
	
			} 
			create_row(barcode_no,'scan');
		}
		$('#txt_barcode_no').val('');
	}*/

	function create_row(bundle_nos, vscan, hidden_source_cond)
    {
		var mst_id="";
		if(trim(bundle_nos)=="") var mst_id=$('#txt_update_id').val();
        var row_num =  $('#tbl_details tbody tr').length; //$('#hidden_row_number').val();
   		//alert(row_num);
        var response_data = return_global_ajax_value(bundle_nos + "**" + row_num + "**"+mst_id+"**" + $('#cbo_working_company').val() + "**" + vscan + "**" + hidden_source_cond+ "**" + $('#txt_bulletin_id').val()+ "**" + $('#cbo_operation_id').val()+ "**" + $('#txt_job_no').val()+ "**" + $('#cbo_buyer_name').val()+ "**" + $('#txt_style_no').val(), 'populate_bundle_data', '', 'requires/bundle_wise_operation_track_controller');
		
        if (trim(response_data) == '')
        {
            alert("No Data Found. Please Check Pre-Costing Or Order Entry For Bundle Previous Process.");
        }
		
       	$('#tbl_details tbody').prepend(response_data);
    }

	function openmypage_bundle(page_link,title)
	{
		if(form_validation('txt_bulletin_no*cbo_operation_id*txt_production_date','Opr. Bulletin ID*Operation*Production Date')==false)
		{
			return;
		}
		var bundleNo='';
		$("#tbl_details").find('tbody tr').each(function()
		{
			bundleNo+=$(this).find("td:eq(1)").text()+',';
		});
		
		var bulletin_id=$('#txt_bulletin_id').val();
		var operation_id=$('#cbo_operation_id').val();
		var job_no=$('#txt_job_no').val();
		var buyer=$('#cbo_buyer_name').val();
		var style_ref=$('#txt_style_no').val();
		var production_date=$('#txt_production_date').val();
		
		var title='Bundle No Search';
		var page_link='	requires/bundle_wise_operation_track_controller.php?action=bundle_popup&wcompany_id='+document.getElementById('cbo_working_company').value+'&bundleNo='+bundleNo+'&bulletin_id='+bulletin_id+'&operation_id='+operation_id+'&job_no='+job_no+'&buyer='+buyer+'&style_ref='+style_ref+'&production_date='+production_date;
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,title,'width=1050px,height=370px,center=1,resize=0,scrolling=0','../')
									
		emailwindow.onclose=function()
		{
			var theform				=this.contentDoc.forms[0];
			var hidden_bundle_nos	=this.contentDoc.getElementById("hidden_bundle_nos").value;
			if (hidden_bundle_nos!="")
			{
				get_php_form_data( hidden_bundle_nos+'_'+$('#cbo_working_company').val(), "populate_mst_data", "requires/bundle_wise_operation_track_controller");
				create_row(hidden_bundle_nos,"Browse");
				$('#txt_bulletin_no').attr('disabled','true');
				$('#cbo_operation_id').attr('disabled','true');
				$('#txt_production_date').attr('disabled','true');
				release_freezing();
			}
		}
	}//end function

	function fnc_calculate_amount(inc)
	{
		var rowBunQty=0; var rowProdQty=0; var rowRate=0; var rowAmt=0;
		var totBunQty=0; var totProQty=0; var totAmt=0;
		
		$("#tbl_details").find('tbody tr').each(function()
		{
			var rowBunQty=$(this).find("td:eq(9)").text()*1;
			var rowProdQty=$(this).find("td:eq(10)").text()*1;
			var rowRate=$(this).find('input[name="txtRate[]"]').val()*1;
			
			var rowAmt=rowProdQty*rowRate;
			$(this).find('input[name="txtAmount[]"]').val(number_format(rowAmt,4,'.','' ));
		});
	}
	
	function fnc_operation_track_entry(operation)
	{
		if(operation==4)
		{
			// var report_title=$( "div.form_caption" ).html();
			//generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 'emblishment_issue_print', 'requires/bundle_wise_operation_track_controller');
			var title = 'Search Panel';	
			var page_link = 'requires/bundle_wise_operation_track_controller.php?action=print_search_popup&company_name='+document.getElementById('cbo_working_company').value+'&bulletin_id='+document.getElementById('txt_bulletin_no').value;
				
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=0,scrolling=0','')
			return;
		}
		
		if (form_validation('cbo_working_company*cbo_working_location*txt_operation_date*txt_bulletin_id*cbo_operation_id*txt_production_date','W. Company*WC. Location*Operation Date*Opr. Bulletin ID*Operation*Production Date')==false )
		{
			return;
		}
		if(operation==0 || operation==1 || operation==2)
		{
			var j 			=0; 
			var dataString 	='';

			$("#tbl_details").find('tbody tr').each(function()
			{
				var bundleNo	 	=$(this).find("td:eq(1)").text();
				var txtoperatorId 	=$(this).find('input[name="txtOperatorId[]"]').val();
				var txtcutNo 		=$(this).find('input[name="txtcutNo[]"]').val();
				var barcodeNo 		=$(this).find('input[name="txtbarcode[]"]').val();
				var txtcolorSizeId 	=$(this).find('input[name="txtcolorSizeId[]"]').val();
				var txtorderId 		=$(this).find('input[name="txtorderId[]"]').val();
				var txtgmtsitemId 	=$(this).find('input[name="txtgmtsitemId[]"]').val();
				var txtcountryId 	=$(this).find('input[name="txtcountryId[]"]').val();
				var txtcolorId 		=$(this).find('input[name="txtcolorId[]"]').val();			
				var txtsizeId 		=$(this).find('input[name="txtsizeId[]"]').val();
				var txtBundleQty 	=$(this).find("td:eq(9)").text();
				var txtqty 			=$(this).find('input[name="txtqty[]"]').val();
				var txtRate 		=$(this).find('input[name="txtRate[]"]').val();	
				var txtAmount 		=$(this).find('input[name="txtAmount[]"]').val();
				
				var dtlsId 			=$(this).find('input[name="dtlsId[]"]').val();	
				var isRescan 		=$(this).find('input[name="isRescan[]"]').val();
				
				try 
				{
					j++;
					dataString+='&bundleNo_' + j + '=' + bundleNo + 
								'&barcodeNo_' + j + '=' + barcodeNo + 
								'&txtoperatorId_' + j + '=' + txtoperatorId + 
								'&txtcutNo_' + j + '=' + txtcutNo + 
								'&txtcolorSizeId_' + j + '=' + txtcolorSizeId + 
								'&txtorderId_' + j + '=' + txtorderId + 
								'&txtgmtsitemId_' + j + '=' + txtgmtsitemId  + 
								'&txtcountryId_' + j + '=' + txtcountryId  + 
								'&txtcolorId_' + j + '=' + txtcolorId + 
								'&txtsizeId_' + j + '=' + txtsizeId + 
								'&txtBundleQty_' + j + '=' + txtBundleQty + 
								'&txtqty_' + j + '=' + txtqty + 
								'&txtRate_' + j + '=' + txtRate + 
								'&txtAmount_' + j + '=' + txtAmount + 
								
								'&dtlsId_' + j + '=' + dtlsId +  		
								'&isRescan_' + j + '=' + isRescan; 							
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
			
			var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_system_no*txt_update_id*cbo_working_company*cbo_working_location*txt_operation_date*txt_challan_no*cbo_floor*cbo_line_no*txt_job_no*txt_bulletin_id*cbo_operation_id*txt_remarks*hidd_prod_reso_allo*txt_production_date',"../")+dataString;
			//alert(data); return;
			freeze_window(operation);
			http.open("POST","requires/bundle_wise_operation_track_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_liniking_operation_info;
		}
	}
  
	function fnc_liniking_operation_info()
	{
		if(http.readyState == 4) 
		{		
			var reponse=http.responseText.split('**');		 
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_operation_track_entry('+ reponse[1]+')',4000); 
			}
			else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				if(reponse[3]){alert("Receive Found Bundle List : "+reponse[3]+" This Bundle Not Any Change.");}
				show_msg(trim(reponse[0]));
				
				document.getElementById('txt_update_id').value = reponse[1];
				document.getElementById('txt_system_no').value = reponse[2];
				if(reponse[0]!=2)
				{
					set_button_status(0, permission, 'fnc_operation_track_entry',1,0);
					show_list_view(reponse[1],'operation_list_view','operation_list_view','requires/bundle_wise_operation_track_controller','setFilterGrid("table_body",-1)');
					fnc_change_operation();
					
					//$('#tbl_details tbody tr').remove();
					//create_row('','browse','');
					release_freezing();
				}
			}
			if(reponse[0]!=15)
			{
				show_msg(trim(reponse[0]));
				release_freezing();
			}
			//release_freezing();
		}
	}
	
	function openmypage_sysNo()
	{
		var title = 'Challan Selection Form';	
		var page_link = 'requires/bundle_wise_operation_track_controller.php?action=system_number_popup';
			
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_id=this.contentDoc.getElementById("update_mst_id").value;//po id
			if(mst_id!="")
			{ 
				//freeze_window(5);
				get_php_form_data( mst_id, "populate_data_from_track", "requires/bundle_wise_operation_track_controller");
				$('#tbl_details tbody tr').remove();
				show_list_view(mst_id,'operation_list_view','operation_list_view','requires/bundle_wise_operation_track_controller','setFilterGrid("table_body",-1)');
				//create_row('','browse','');
				set_button_status(0, permission, 'fnc_operation_track_entry',1,0);
				//$('#txt_operator_id').attr('disabled','true');
				release_freezing();
				//release_freezing();
			}
		}
	}//end function
	
	
	function get_details_form_data(id,type,path)
	{
		
	}

	function pageReset(){
	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/bundle_wise_operation_track_controller.php?data=" + data+'&action='+action, true );
	}

	function fn_deleteRow(id)
	{
		$("#txtOperatorId_"+id).closest('tr').remove();
		//calculate_defect_qty();
		rearrange_serial_no();
	}
	
	function rearrange_serial_no()
	{
		var tot_row = $('#tbl_details tbody tr').length;
		var i=tot_row;
		$("#tbl_details").find('tbody tr').each(function()
		{
			$(this).find("td:eq(0)").text(i);
			i--;
		});
	}
	
	function fnc_change_operation()
	{
		$('#cbo_operation_id').removeAttr('disabled',true);
		$('#cbo_operation_id').val(0);
		
		$("#tbl_details").find('tbody tr').each(function()
		{
			$(this).find("td:eq(4)").text('');
			$(this).find('input[name="txtOperatorId[]"]').val('');
			$(this).find('input[name="txtRate[]"]').val('');
			$(this).find('input[name="txtAmount[]"]').val('');
		});
		
		//set_button_status(0, permission, 'fnc_operation_track_entry',0,0);
	}
	
	function copy_check(type)
	{
		if(type==1)
		{
			if(document.getElementById('copy_opt').checked==true)
			{
				document.getElementById('copy_opt').value=1;
			}
			else if(document.getElementById('copy_opt').checked==false)
			{
				document.getElementById('copy_opt').value=2;
			}
		}
		else if(type==2)
		{
			if(document.getElementById('copy_rate').checked==true)
			{
				document.getElementById('copy_rate').value=1;
			}
			else if(document.getElementById('copy_rate').checked==false)
			{
				document.getElementById('copy_rate').value=2;
			}
		}
	}
	
	function fnc_copyval(type,value,i)
	{
		var tot_row=$('#tbl_details tbody tr').length;
		var copy_operator=$('#copy_opt').val();
		var copy_rate=$('#copy_rate').val();
		
		if(copy_operator==1 && type==1)
		{
			var employee_data=value.split("_");
			//alert(i+'_'+k+'_'+tot_row)//employee_data[0]+'_'+employee_data[1]+'_'+employee_data[2]
			var q=1;
			for(var j=i; j>=q; j--)
			{
				$("#txtOperatorId_"+j).val(employee_data[1]);
				$("#operatorName_"+j).text(employee_data[2]);
			}
		}
		
		if(copy_rate==1  && type==2)
		{
			var q=1;
			for(var j=i; j>=q; j--)
			{
				$("#txtRate_"+j).val(value);
				fnc_calculate_amount(j);
			}
		}
	}

</script>
</head>
<body onLoad="set_hotkey()">
 <div style="width:100%;">
  	<? echo load_freeze_divs ("../",$permission);  ?>
    <div style=" float:left" align="left"> 
        <form name="operationtrack_1" id="operationtrack_1" method="" autocomplete="off" >
			<fieldset style="width:950px;">
        		<legend>Bundle Wise Operation Track</legend>
                <table width="950">
                    <tr>
                        <td align="right" colspan="4"><b>System ID</b></td>
                        <td colspan="2"> 
                            <input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_sysNo();" placeholder="Browse" readonly/>
                            <input name="txt_update_id" id="txt_update_id" type="hidden" /> 
                            <input name="hidd_prod_reso_allo" id="hidd_prod_reso_allo" type="hidden" />
                        </td>
                        <td colspan="2"><input type="button" name="btn_change" class="formbutton" value="Change Operation" id="btn_change" onClick="fnc_change_operation();" style="width:100px; display:none" /></td>
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption">W. Company</td>
                        <td width="130" id="working_com"><? echo create_drop_down( "cbo_working_company",130, "select id, company_name from lib_company comp  where status_active =1 and  is_deleted=0  $company_cond  order by company_name","id,company_name", 1, "-- Select --", $selected,"load_drop_down( 'requires/bundle_wise_operation_track_controller', this.value, 'load_drop_down_location', 'working_location_td' );",1 ); ?></td>
                        <td width="100" class="must_entry_caption">WC. Location</td>
                        <td width="130" id="working_location_td"><? echo create_drop_down( "cbo_working_location", 130, "select id, location_name from lib_location where status_active=1 and is_deleted=0","id,location_name", 1, "--Select Location--", $selected, "",1 ); ?></td>
                        <td width="100" class="must_entry_caption">Operation Date</td>
                        <td width="130"><input type="text" name="txt_operation_date" id="txt_operation_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:120px;" /></td>
                        <td width="100" class="must_entry_caption">Production Date</td>
                        <td><input type="text" name="txt_production_date" id="txt_production_date" class="datepicker" style="width:120px" placeholder="Write" /></td>
                    </tr>
                    <tr>
                     	<td class="must_entry_caption">Floor</td>
                        <td id="floor_td"><? echo create_drop_down( "cbo_floor", 130,"select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name","id,floor_name", 1, "--Select Floor--", $selected, "",1 ); ?></td>
                        <td class="must_entry_caption">Line No.</td>
                        <td id="line_td"><? echo create_drop_down( "cbo_line_no", 130, $blank_array,"", 1, "--Select Line--", $selected, "" ); ?></td>
                        <td>Buyer</td>
                        <td><? echo create_drop_down( "cbo_buyer_name", 130, "select id, buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "Dispaly", $selected, "",1,0 ); ?></td> 
                         <td>Style</td>
                         <td><input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:120px" disabled readonly /></td>
                     </tr>
                     <tr>
                     	<td>Job No</td>
                        <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:120px" readonly /></td>
                        <td class="must_entry_caption">Opr. Bulletin ID</td>
                        <td><input name="txt_bulletin_no" id="txt_bulletin_no" class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_bulletin();" placeholder="Browse" readonly /> <input name="txt_bulletin_id" id="txt_bulletin_id" type="hidden" /> </td>
                        <td class="must_entry_caption">Operation</td>
                        <td id="operation_td"><? echo create_drop_down( "cbo_operation_id", 130, $blank_array,"", 1, "-Select-", $selected, "","",0 ); ?></td> 
                        <td>Resource</td>
                        <td><input type="text" name="txt_resource" id="txt_resource" class="text_boxes" style="width:120px" readonly /><input name="txt_resource_id" id="txt_resource_id" type="hidden" /></td>
                     </tr>
                     <tr>
                     	<td>WS. SMV</td>
                        <td><input type="text" name="txt_ws_smv" id="txt_ws_smv" class="text_boxes_numeric" style="width:120px" readonly /></td>
                        <td>W. Track</td>
                        <td><input name="txt_wtrack" id="txt_wtrack" class="text_boxes" type="text" style="width:120px" readonly /></td>
                        <td>Challan No</td>
                        <td><input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text" style="width:120px" placeholder="Write" /></td>
                        <td>Remarks</td>
                        <td><input name="txt_remarks" id="txt_remarks" class="text_boxes" type="text" style="width:120px" /></td>
                     </tr>
                     
                     <tr>
                     	<td>&nbsp;</td>
                        <td>&nbsp;</td>
                     	<td class="must_entry_caption"><strong>Barcode No</strong></td>
                        <td><input name="txt_barcode_no" placeholder="Browse/Write/Scan" onDblClick="openmypage_bundle();" id="txt_barcode_no" class="text_boxes" style="width:120px" /></td>
                        <td class="must_entry_caption" style="display:none"><strong>Re Ticket No</strong></td>
                        <td style="display:none"><input name="txt_barcode_rescan" placeholder="Browse/Write/Scan" onDblClick="openmypage_bundle_rescan();" id="txt_barcode_rescan" class="text_boxes" style="width:120px" /></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                     </tr>
                </table>
			</fieldset><br />
            <fieldset>
				<legend>Operator Wise Bundle List</legend>
                    <div id="bundle_list_view">
                        <table cellpadding="0" width="950" cellspacing="0" border="1" class="rpt_table" rules="all">
                            <thead>
                                <th width="25">SL</th>
                                <th width="70">Bundle No</th>
                                <th width="90">Barcode No</th>
                                <th width="75">Operator ID<input type="checkbox" name="copy_opt" id="copy_opt" onClick="copy_check(1)" value="1" checked ></th>
                                <th width="90">Op. Name</th>
                                <th width="90">Order No</th>
                                <th width="100">Gmts. Item</th>
                                <th width="100">GMT Color</th>
                                <th width="50">Size</th>
                                <th width="50">Bundle Qty. (Pcs)</th>
                                <th width="50">QC Pass Qty. (Pcs)</th>
                                <th width="40">Rate<input type="checkbox" name="copy_rate" id="copy_rate" onClick="copy_check(2)" value="2" ></th>
                                <th width="50">Amount</th>
                                <th>&nbsp;</th>
                            </thead>
                        </table>
                        <div style="width:950px; max-height:250px; overflow-y:scroll" align="left">    
                            <table cellpadding="0" width="930" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">      
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <table cellpadding="0" cellspacing="1" width="100%">
                        <tr>
                            <td align="center" colspan="9" valign="middle" class="button_container">
                            <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, 'fnc_operation_track_entry', 0, 1, "reset_form('operationtrack_1','bundle_list_view','', 'txt_operation_date,".date('d-m-Y')."','pageReset();')",1); ?>
                            <input type="hidden"  name="hidden_row_number" id="hidden_row_number"> 
                            </td>
                            <td>&nbsp;</td>				
                        </tr>
                    </table>
            </fieldset>
        </form>
    </div>
	<div id="operation_list_view" style="width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>