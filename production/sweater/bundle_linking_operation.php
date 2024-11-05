<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create  Bundle Wise Linking Operation Track
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	23-10-2019
Updated by 		: 	
Update date		: 	   
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
echo load_html_head_contents("Bundle Wise Linking Operation Track","../../", 1, 1, $unicode,'','');
?>
	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function openmypage_operator()
	{
		var page_link='requires/bundle_linking_operation_controller.php?action=operator_popup';
		var title='Bundle Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{				
			var employee_data=(this.contentDoc.getElementById("hidden_emp_number").value).split("_");//po id
			//alert(employee_data[1]+'-'+employee_data[2]);
			$("#txt_operator_id").val(employee_data[1]);
			$("#txt_operator_name").val(employee_data[2]);	
		}
	}//end function
	
	$('#txt_operator_id').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var txt_operator_id=trim($('#txt_operator_id').val().toUpperCase());
			var flag=1;
		
			if(flag==1)
			{
				get_php_form_data( txt_operator_id, "populate_operator_data", "requires/bundle_linking_operation_controller");
			}
		}
	});
	
	$('#txt_barcode_no').live('keydown', function(e) {
		if (e.keyCode === 13) 
		{
			e.preventDefault();
			var txt_barcode_no=trim($('#txt_barcode_no').val().toUpperCase());
			var flag=1;
			$("#tbl_details").find('tbody tr').each(function()
			{
				var barcodeNo=$(this).find("td:eq(1)").text();
				var bundleNo=$(this).find("td:eq(3)").text();
				if(txt_barcode_no==barcodeNo){
					
					alert("Ticket No: "+barcodeNo+" already scan, try another one.");
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
        var challan_duplicate ='';// return_ajax_request_value(barcodeNo, "challan_duplicate_check", "requires/bundle_linking_operation_controller");
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
				get_php_form_data( barcodeNo+'_'+$('#cbo_working_company').val(), "populate_mst_data", "requires/bundle_linking_operation_controller");
			}
        }
        $('#txt_barcode_no').val('');
    }

	/*function fnc_duplicate_bundle(barcode_no)
	{
		var challan_duplicate=return_ajax_request_value( barcode_no,"challan_duplicate_check", "requires/bundle_linking_operation_controller");
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
		/*var ticketNo='';
		$("#tbl_details").find('tbody tr').each(function()
		{
			ticketNo+=$(this).find("td:eq(1)").text()+',';
		});
		if(ticketNo!="") bundle_nos=bundle_nos+','+ticketNo;*/
		//alert(bundle_nos)
       // freeze_window(5);
		var mst_id="";
		if(trim(bundle_nos)=="") var mst_id=$('#txt_update_id').val(); else 

        var row_num =  $('#tbl_details tbody tr').length; //$('#hidden_row_number').val();
   		//alert(row_num);
        var response_data = return_global_ajax_value(bundle_nos + "**" + row_num + "**"+mst_id+"**" + $('#cbo_working_company').val() + "**" + vscan + "**" + hidden_source_cond, 'populate_bundle_data', '', 'requires/bundle_linking_operation_controller');
        /* if (trim(response_data) == '')
        {
			alert("No Data Found. Please Check Pre-Costing Or Order Entry For Bundle Previous Process.");
        } */
		var response_data_arr=response_data.split('####');
		if(response_data_arr[0]==10)
		{
			alert(response_data_arr[1]);
		}
		else
		{
			$('#tbl_details tbody').prepend(response_data_arr[1]);
		}

        /*var tot_row = $('#tbl_details tbody tr').length;
		//alert(tot_row);
        if ((tot_row*1)>0 && mst_id=="")
        {
			$('#txt_operator_id').attr('disabled','true');
        }*/
        //$('#hidden_row_number').val(tot_row);
        
    }

	function openmypage_bundle(page_link,title)
	{
		var ticketNo='';
		$("#tbl_details").find('tbody tr').each(function()
		{
			ticketNo+=$(this).find("td:eq(1)").text()+',';
		});
		//alert(ticketNo)
		var title='Ticket No Search';
		var page_link='	requires/bundle_linking_operation_controller.php?action=bundle_popup&wcompany_id='+document.getElementById('cbo_working_company').value+'&ticketNo='+ticketNo;
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link+'&ticketNo='+ticketNo,title,'width=1150px,height=370px,center=1,resize=0,scrolling=0','../')
									
		emailwindow.onclose=function()
		{
			var theform				=this.contentDoc.forms[0];
			var hidden_bundle_nos	=this.contentDoc.getElementById("hidden_bundle_nos").value;
			if (hidden_bundle_nos!="")
			{
				get_php_form_data( hidden_bundle_nos+'_'+$('#cbo_working_company').val(), "populate_mst_data", "requires/bundle_linking_operation_controller");
				create_row(hidden_bundle_nos,"Browse");
				$('#txt_operator_id').attr('disabled','true');
				release_freezing();
			}
		}
	}//end function

	function fnc_calculate_amount(inc)
	{
		//var row_num =  $('#tbl_details tbody tr').length;
		var rowBunQty=0; var rowProdQty=0; var rowRate=0; var rowAmt=0;
		var totBunQty=0; var totProQty=0; var totAmt=0;
		
		$("#tbl_details").find('tbody tr').each(function()
		{
			var rowBunQty=$(this).find("td:eq(14)").text()*1;
			var rowProdQty=$(this).find("td:eq(15)").text()*1;
			var rowRate=$(this).find('input[name="txtRate[]"]').val()*1;
			
			var rowAmt=rowProdQty*rowRate;
			//$("#txtAmount_"+inc).val(rowAmt);
			$(this).find('input[name="txtAmount[]"]').val(number_format(rowAmt,4,'.','' ));
			
			totBunQty+=rowBunQty;
			totProQty+=rowProdQty;
			totAmt+=rowAmt;		
		});
	}
	
	function fnc_liniking_operation_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			//generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 'emblishment_issue_print', 'requires/bundle_linking_operation_controller');
			return;
		}
		
		if (form_validation('cbo_working_company*cbo_working_location*txt_operation_date*txt_operator_id','W. Company*WC. Location*Operation Date*OP ID')==false )
		{
			return;
		}
		if(operation==0 || operation==1 || operation==2)
		{
				var j 			=0; 
				var dataString 	='';
	
				$("#tbl_details").find('tbody tr').each(function()
				{
					var ticketNo 		=$(this).find("td:eq(1)").text();
					var bundleNo 		=$(this).find("td:eq(3)").text();
					var txtOperationId 	=$(this).find('input[name="txtOperationId[]"]').val();
					var txtcutNo 		=$(this).find('input[name="txtcutNo[]"]').val();
					var txtbarcode 		=$(this).find('input[name="txtbarcode[]"]').val();
					var txtcolorSizeId 	=$(this).find('input[name="txtcolorSizeId[]"]').val();
					var txtorderId 		=$(this).find('input[name="txtorderId[]"]').val();
					var txtgmtsitemId 	=$(this).find('input[name="txtgmtsitemId[]"]').val();
					//var txtcountryId 	=$(this).find('input[name="txtcountryId[]"]').val();
					var txtcolorId 		=$(this).find('input[name="txtcolorId[]"]').val();			
					var txtsizeId 		=$(this).find('input[name="txtsizeId[]"]').val();
					var txtBundleQty 	=$(this).find("td:eq(14)").text();
					var txtqty 			=$(this).find('input[name="txtqty[]"]').val();
					var txtRate 		=$(this).find('input[name="txtRate[]"]').val();	
					var txtAmount 		=$(this).find('input[name="txtAmount[]"]').val();
					
					var dtlsId 			=$(this).find('input[name="dtlsId[]"]').val();	
					var isRescan 		=$(this).find('input[name="isRescan[]"]').val();
					var txtstyle 		=$(this).find('input[name="txtstyle[]"]').val();
					
					try 
					{
						j++;
						dataString+='&txtOperationId_' + j + '=' + txtOperationId + 
									'&ticketNo_' + j + '=' + ticketNo + 
									'&bundleNo_' + j + '=' + bundleNo + 
									'&txtcutNo_' + j + '=' + txtcutNo + 
									'&txtbarcode_' + j + '=' + txtbarcode + 
									'&txtcolorSizeId_' + j + '=' + txtcolorSizeId + 
									'&txtorderId_' + j + '=' + txtorderId + 
									'&txtgmtsitemId_' + j + '=' + txtgmtsitemId  + 
									'&txtcolorId_' + j + '=' + txtcolorId + 
									'&txtsizeId_' + j + '=' + txtsizeId + 
									'&txtBundleQty_' + j + '=' + txtBundleQty + 
									'&txtqty_' + j + '=' + txtqty + 
									'&txtRate_' + j + '=' + txtRate + 
									'&txtAmount_' + j + '=' + txtAmount + 
									
									'&dtlsId_' + j + '=' + dtlsId +  		
									'&isRescan_' + j + '=' + isRescan+
									'&txtstyle_' + j + '=' + txtstyle; 							
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
				
				var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_system_no*txt_update_id*cbo_working_company*cbo_working_location*txt_operation_date*txt_operator_id*txt_reporting_hour*txt_challan_no*txt_remarks*garments_nature*txt_loss_min',"../../")+dataString;
			//alert(data); return;
			freeze_window(operation);
			http.open("POST","requires/bundle_linking_operation_controller.php",true);
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
				 setTimeout('fnc_liniking_operation_entry('+ reponse[1]+')',4000); 
			}
			else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				if(reponse[3]){alert("Receive Found Bundle List : "+reponse[3]+" This Bundle Not Any Change.");}
				show_msg(trim(reponse[0]));
				
				document.getElementById('txt_update_id').value = reponse[1];
				document.getElementById('txt_system_no').value = reponse[2];
				if(reponse[0]!=2)
				{
					set_button_status(1, permission, 'fnc_liniking_operation_entry',1,0);
					$('#tbl_details tbody tr').remove();
					create_row('','browse','');
					release_freezing();
				}
			}
			if(reponse[0]!=15)
			{
				release_freezing();
			}
			//release_freezing();
		}
	}
	
	function openmypage_sysNo()
	{
		var title = 'Challan Selection Form';	
		var page_link = 'requires/bundle_linking_operation_controller.php?action=system_number_popup';
			
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_id=this.contentDoc.getElementById("update_mst_id").value;//po id
			if(mst_id!="")
			{ 
				//freeze_window(5);
				get_php_form_data( mst_id, "populate_data_from_track", "requires/bundle_linking_operation_controller");
				$('#tbl_details tbody tr').remove();
				create_row('','browse','');
				set_button_status(1, permission, 'fnc_liniking_operation_entry',1,0);
				$('#txt_operator_id').attr('disabled','true');
				release_freezing();
				//release_freezing();
			}
		}
	}//end function
	

	function pageReset(){
		
		var tot_row=$('#tbl_details tbody tr').length;
		var req_id_str='';
		for(var i=1;i<=tot_row;i++)
		{
			$("#txtOperationId_"+i).closest('tr').remove();
		}

	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/bundle_linking_operation_controller.php?data=" + data+'&action='+action, true );
	}

	function fnc_valid_time( val, field_id )
	{
		var val_length=val.length;
		if(val_length==2)
		{
			document.getElementById(field_id).value=val+":";
		}
		
		var colon_contains=val.includes(":");
		if(colon_contains==false)
		{
			if(val>23)
			{
				document.getElementById(field_id).value='23:';
			}
		}
		else
		{
			var data=val.split(":");
			var minutes=data[1];
			var str_length=minutes.length;
			var hour=data[0]*1;
			
			if(hour>23) hour=23;
			
			if(str_length>=2)
			{
				minutes= minutes.substr(0, 2);
				if(minutes*1>59) minutes=59;
			}
			var valid_time=hour+":"+minutes;
			document.getElementById(field_id).value=valid_time;
		}
	}

	function numOnly(myfield, e, field_id)
	{
		var key;
		var keychar;
		if (window.event)
			key = window.event.keyCode;
		else if (e)
			key = e.which;
		else
			return true;
		keychar = String.fromCharCode(key);
	
		// control keys
		if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;
		// numbers
		else if ((("0123456789:").indexOf(keychar) > -1))
		{
			var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
			if(keychar==":" && dotposl!=-1)
			{
				return false;
			}
			return true;
		}
		else
			return false;
	}
	
	function fn_deleteRow(id)
	{
		$("#txtOperationId_"+id).closest('tr').remove();
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

</script>
</head>
<body onLoad="set_hotkey()">
 <div style="width:100%;">
  	<? echo load_freeze_divs ("../",$permission);  ?>
    <div style=" float:left" align="left"> 
        <form name="linkingoperation_1" id="linkingoperation_1" method="" autocomplete="off" >
			<fieldset style="width:1050px;">
        		<legend>OP & Company Info.</legend>
                <table width="1050">
                    <tr>
                        <td align="right" colspan="4"><b>System ID</b></td>
                        <td colspan="4"> 
                            <input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_sysNo();" placeholder="Browse" readonly/>
                            <input name="txt_update_id" id="txt_update_id" type="hidden" /> 
							
                        </td>
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption">W. Company</td>
                        <td width="130" id="working_com"><? echo create_drop_down( "cbo_working_company",130, "select id, company_name from lib_company comp  where status_active =1 and  is_deleted=0  $company_cond  order by company_name","id,company_name", 1, "-- Select --", $selected,"load_drop_down( 'requires/bundle_linking_operation_controller', this.value, 'load_drop_down_location', 'working_location_td' );" ); ?></td>
                        <td width="100" class="must_entry_caption">WC. Location</td>
                        <td width="130" id="working_location_td"><? echo create_drop_down( "cbo_working_location", 130, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td width="100" class="must_entry_caption">Operation Date</td>
                        <td width="130"><input type="text" name="txt_operation_date" id="txt_operation_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:120px;" /></td>
                        <td width="100">Challan No</td>
                        <td><input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text" style="width:120px" placeholder="Write" /></td>
                    </tr>
                     <tr>
                     	<td class="must_entry_caption">OP ID</td>
                        <td><input name="txt_operator_id" id="txt_operator_id" class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_operator();" placeholder="Browse/Write/Scan" /></td>
                        <td>OP Name</td>
                        <td><input name="txt_operator_name" id="txt_operator_name" class="text_boxes" type="text" style="width:120px"  /></td>
                        <td>Hour</td>
                        <td> 
                            <input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:120px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" />
                        </td>
                        <td>Loss Min</td>
                        <td>
                        	<input name="txt_loss_min" id="txt_loss_min" class="text_boxes" style="width:120px" placeholder="Write"/>
                        </td>
                     </tr>
                     <tr>
                        <td>Remarks</td>
                        <td colspan="5"><input name="txt_remarks" id="txt_remarks" class="text_boxes" type="text" style="width:608px" /></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                     </tr>
                     <tr>
                     	<td colspan="8">&nbsp;</td>
                     </tr>
                     <tr>
                     	<td class="must_entry_caption">Ticket No</td>
                        <td><input name="txt_barcode_no" placeholder="Browse/Write/Scan" onDblClick="openmypage_bundle();" id="txt_barcode_no" class="text_boxes" style="width:120px" /></td>
                        <td class="must_entry_caption">Re Ticket No</td>
                        <td><input name="txt_barcode_rescan" placeholder="Browse/Write/Scan" onDblClick="openmypage_bundle_rescan();" id="txt_barcode_rescan" class="text_boxes" style="width:120px" /></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                     </tr>
                </table>
			</fieldset><br />
               
            <fieldset>
				<legend>Operation Name Wise Ticket List</legend>
                    <div id="bundle_list_view">
                        <table cellpadding="0" width="1240" cellspacing="0" border="1" class="rpt_table" rules="all">
                            <thead>
                                <th width="30">SL</th>
                                <th width="90">Ticket No</th>
                                <th width="120">Operation</th>
                                <th width="70">Bundle No</th>
                                <th width="60">Floor</th>
                                <th width="60">Line</th>
                                <th width="40">J. Year</th>
                                <th width="40">Job No</th>
                                <th width="65">Buyer</th>
                                <th width="100">Style No</th>
                                <th width="70">Order No</th>
                                <th width="100">Gmts. Item</th>
                                <th width="100">GMT Color</th>
                                <th width="50">Size</th>
                                <th width="50">Bundle Qty. (Pcs)</th>
                                <th width="50">Prod. Qty (Pcs)</th>
                                <!-- <th width="40">Rate</th>
                                <th width="50">Amount</th> -->
                                <th>&nbsp;</th>
                            </thead>
                        </table>
                        <div style="width:1260px; max-height:250px; overflow-y:scroll" align="left">    
                            <table cellpadding="0" width="1240" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">      
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <table cellpadding="0" cellspacing="1" width="100%">
                        <tr>
                            <td align="center" colspan="9" valign="middle" class="button_container">
                            <? $date=date('d-m-Y'); echo load_submit_buttons( $permission, "fnc_liniking_operation_entry", 0,0 , "reset_form('linkingoperation_1','','', 'txt_operation_date,".date('d-m-Y')."','pageReset();')",1); ?>
                            <input type="hidden"  name="hidden_row_number" id="hidden_row_number"> 
                            </td>
                            <td>&nbsp;</td>				
                        </tr>
                    </table>
            </fieldset>
        </form>
    </div>
	<div  id="list_view_country"   style="	width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>