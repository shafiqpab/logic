<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Linking Input
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	19-06-2019
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
echo load_html_head_contents("Linking Input","../../", 1, 1, $unicode,'','');
?>
	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function fnc_duplicate_bundle1(barcode_no)
	{
		var challan_duplicate=return_ajax_request_value( barcode_no,"challan_duplicate_check", "requires/linking_input_controller");
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
			create_row(barcode_no,'scan',"");
		}
		$('#txt_bundle_no').val('');
	}
	
	function fnc_duplicate_bundle(bundle_no)
    {
        var challan_duplicate = return_ajax_request_value(bundle_no, "challan_duplicate_check", "requires/linking_input_controller");
        //var ex_challan_duplicate = challan_duplicate.split("_");
       
		if ( trim( challan_duplicate)!= '')
        {
            alert(trim(challan_duplicate));
            $('#txt_bundle_no').val('');
            return;
        }
        else
        {
			var tot_row=$('#tbl_details tbody tr').length; 
            create_row(bundle_no,'scan','');
			var bundle_no=bundle_no.split(',');
				 
			if( (tot_row*1) <1)
			{
				var response_mstdata = return_global_ajax_value(bundle_no[0]+'_'+$('#cbo_company_name').val(), 'populate_mst_data', '', 'requires/linking_input_controller');
				if(response_mstdata!="")
				{
					var ex_data=response_mstdata.split('_');
					//$('#cbo_company_name').val(ex_data[0]);
					
					$('#cbo_source').val(ex_data[2]);
					fnc_load_party(1);
					load_drop_down( 'requires/linking_input_controller', ex_data[0]+'_'+1, 'load_drop_down_location', 'location_td' );
					$('#cbo_location').val(ex_data[1]);
					$('#cbo_working_company').val(ex_data[3]);
					fnc_load_party(2);
					$('#cbo_working_location').val(ex_data[4]);
					
					if(ex_data[2]==1) var location=ex_data[4]; else  var location=ex_data[1];
					load_drop_down('requires/linking_input_controller', location, 'load_drop_down_floor', 'floor_td' );
					$('#cbo_floor').val(ex_data[5]);
					
					$('#cbo_company_name').attr('disabled', 'disabled');
					$('#cbo_location').attr('disabled', 'disabled');
					//$('#cbo_source').attr('disabled', 'disabled');
					$('#cbo_working_company').attr('disabled', 'disabled');
					$('#cbo_working_location').attr('disabled', 'disabled');
					$('#cbo_floor').attr('disabled', 'disabled');
					
					load_drop_down( 'requires/linking_input_controller', ex_data[3]+'_'+ex_data[4]+'_'+ex_data[5]+'_'+$('#txt_input_date').val(), 'load_drop_down_line', 'line_td' );
				}
			}
        }
        $('#txt_bundle_no').val('');
    }
	
	 $('#txt_bundle_no').live('keydown', function(e) {
        if (e.keyCode === 13)
        {
			if ( form_validation('cbo_company_name','Company Name')==false )
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

	function create_row(bundle_nos, vscan, hidden_source_cond)
    {
        freeze_window(5);

        var row_num =  $('#tbl_details tbody tr').length; //$('#hidden_row_number').val();
   
        var response_data = return_global_ajax_value(bundle_nos + "**" + row_num + "****" + $('#cbo_company_name').val() + "**" + vscan + "**" + hidden_source_cond, 'populate_bundle_data', '', 'requires/linking_input_controller');
        if (trim(response_data) == '')
        {
            alert("No Data Found. Please Check Pre-Costing Or Order Entry For Bundle Previous Process.");
        }

        $('#tbl_details tbody').prepend(response_data);
        var tot_row = $('#tbl_details tbody tr').length;
        if ((tot_row * 1) > 0)
        {
            $('#cbo_company_name').attr('disabled', 'disabled');
        }
        $('#hidden_row_number').val(tot_row);
        release_freezing();
    }

	function openmypage_bundle(page_link,title)
	{
		var bundleNo='';
		$("#tbl_details").find('tbody tr').each(function()
		{
			bundleNo+=$(this).find("td:eq(1)").text()+',';
			
		});
		
		var title='Bundle Search';
		var page_link='	requires/linking_input_controller.php?action=bundle_popup&company_id='+document.getElementById('cbo_company_name').value+'&bundleNo='+bundleNo;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&bundleNo='+bundleNo, title,'width=1050px,height=370px,center=1,resize=0,scrolling=0','../')
									
		emailwindow.onclose=function()
		{
			var theform				=this.contentDoc.forms[0];
			var hidden_bundle_nos	=this.contentDoc.getElementById("hidden_bundle_nos").value;
			if (hidden_bundle_nos!="")
			{ 
				//create_row(hidden_bundle_nos,"Browse");
				
				fnc_duplicate_bundle(hidden_bundle_nos);
			}
		}
	}//end function

	function fn_deleteRow(id)
	{
		$("#txt_defect_qty_"+id).closest('tr').remove();
		calculate_defect_qty();
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

	function fnc_linking_input_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 'emblishment_issue_print', 'requires/print_embro_delivery_entry_controller');
			return;
		}
		
		if (form_validation('cbo_company_name*cbo_location*cbo_source*cbo_working_company*cbo_working_location*cbo_floor*cbo_line_no*txt_input_date','LC Company*LC Location*Source*W. Company*WC. Location*Floor*Line No*Input Date')==false )
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
				var qty 			= $(this).find('input[name="txtqty[]"]').val();	
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
			
			var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_system_no*txt_update_id*cbo_company_name*cbo_location*cbo_source*cbo_working_company*cbo_working_location*cbo_floor*cbo_line_no*txt_input_date*txt_challan_no*txt_job_no*txt_remarks*garments_nature',"../../")+dataString;
			
			freeze_window(operation);
			//alert(data); return;
			http.open("POST","requires/linking_input_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_linking_input_entry_reply_info;
		}
	}
  
	function fnc_linking_input_entry_reply_info()
	{
		if(http.readyState == 4) 
		{		
			var reponse=http.responseText.split('**');
			if(trim(reponse[0])=='linkOut'){
				alert("Output Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible.")
				release_freezing();
				return;
			}	 
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_linking_input_entry('+ reponse[1]+')',4000); 
			}
			else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				if(reponse[4]){ alert("Receive Found Bundle List : "+reponse[3]+" This Bundle Not Any Change.");}
				show_msg(trim(reponse[0]));
				
				document.getElementById('txt_update_id').value = reponse[1];
				document.getElementById('txt_system_no').value = reponse[2];
				document.getElementById('txt_challan_no').value = reponse[3];
				set_button_status(1, permission, 'fnc_linking_input_entry',1,1);	
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
		var page_link = 'requires/linking_input_controller.php?action=system_number_popup';
			
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_data=this.contentDoc.getElementById("hidd_str_data").value;//po id
			if(mst_data!="")
			{ 
				freeze_window(5);
				
				var ex_data=mst_data.split('_');
				
				$('#txt_update_id').val(ex_data[0]);
				$('#txt_system_no').val(ex_data[1]);
				$('#cbo_company_name').val(ex_data[2]);
				
				$('#cbo_source').val(ex_data[4]);
				fnc_load_party(1);
				load_drop_down( 'requires/linking_input_controller', ex_data[2]+'_'+1, 'load_drop_down_location', 'location_td' );
				$('#cbo_location').val(ex_data[3]);
				$('#cbo_working_company').val(ex_data[5]);
				fnc_load_party(2);
				$('#cbo_working_location').val(ex_data[6]);
				
				if(ex_data[4]==1) var location=ex_data[6]; else  var location=ex_data[3];
				load_drop_down('requires/linking_input_controller', location, 'load_drop_down_floor', 'floor_td' );
				$('#cbo_floor').val(ex_data[7]);
				$('#txt_input_date').val(ex_data[8]);
				
				load_drop_down( 'requires/linking_input_controller', ex_data[5]+'_'+ex_data[6]+'_'+ex_data[7]+'_'+ex_data[8], 'load_drop_down_line', 'line_td');
				$('#txt_challan_no').val(ex_data[9]);
				$('#txt_remarks').val(ex_data[10]);
				$('#cbo_line_no').val(ex_data[11]);
				
				$('#cbo_company_name').attr('disabled', 'disabled');
				$('#cbo_location').attr('disabled', 'disabled');
				//$('#cbo_source').attr('disabled', 'disabled');
				$('#cbo_working_company').attr('disabled', 'disabled');
				$('#cbo_working_location').attr('disabled', 'disabled');
				$('#cbo_floor').attr('disabled', 'disabled');
	
				var response_data = return_global_ajax_value(ex_data[0]+"**"+ ex_data[2], 'populate_bundle_data_update', '', 'requires/linking_input_controller');
				
				$('#tbl_details tbody tr').remove();
                $('#tbl_details tbody').prepend(response_data);
	
				var tot_row=$('#tbl_details tbody tr').length; 
				$('#hidden_row_number').val(tot_row);
				set_button_status(1, permission, 'fnc_linking_input_entry',1,0);
				release_freezing();
			}
		}
	}//end function
	
	function pageReset(){
	
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/linking_input_controller.php?data=" + data+'&action='+action, true );
	}
	
	function fnc_load_party(type)
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_source').val(1);
			return;
		}
		var source=$('#cbo_source').val();
		var company = $('#cbo_company_name').val();
		var working_company = $('#cbo_working_company').val();
		var location_name = $('#cbo_location').val();
		
		if(source==1 && type==1)
		{
			load_drop_down( 'requires/linking_input_controller', company+'_'+1, 'load_drop_down_working_com', 'working_com' );
		}
		else if(source==2 && type==1)
		{
			load_drop_down( 'requires/linking_input_controller', company+'_'+2, 'load_drop_down_working_com', 'working_com' );
		}
		else if(source==1 && type==2)
		{
			load_drop_down( 'requires/linking_input_controller', working_company+'_'+2, 'load_drop_down_location', 'working_location_td' ); 
		} 
	}

	function location_select()
	{
		if($('#cbo_location option').length==2)
		{
			if($('#cbo_location option:first').val()==0)
			{
				$('#cbo_location').val($('#cbo_location option:last').val());
			}
		}
		else if($('#cbo_location option').length==1)
		{
			$('#cbo_location').val($('#cbo_location option:last').val());
		}	
	}

	function working_location_select()
	{
		if($('#cbo_working_location option').length==2)
		{
			if($('#cbo_working_location option:first').val()==0)
			{
				$('#cbo_working_location').val($('#cbo_working_location option:last').val());
				if($('#cbo_source').val()==1){var location=$('#cbo_working_location').val();}else{var location=$('#cbo_location').val();}
				load_drop_down('requires/linking_input_controller',location, 'load_drop_down_floor', 'floor_td');
			}
		}
		else if($('#cbo_working_location option').length==1)
		{
			$('#cbo_working_location').val($('#cbo_working_location option:last').val());
		}	
	}
</script>
</head>
<body onLoad="set_hotkey()">
 <div style="width:100%;">
  	<? echo load_freeze_divs ("../",$permission);  ?>
    <div style=" float:left" align="left"> 
 		
        <form name="linkinginput_1" id="linkinginput_1" method="" autocomplete="off" >
			<fieldset style="width:1050px;">
        		<legend>Linking Input</legend>
                <table width="1050">
                    <tr>
                        <td align="right" colspan="4"><b>Input Challan No</b></td>
                        <td colspan="4"> 
                            <input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_sysNo();" placeholder="Browse" readonly/>
                            <input name="txt_update_id" id="txt_update_id" type="hidden" /> 
                            <input name="txt_job_no" id="txt_job_no" type="hidden" />
                        </td>
                    </tr>
                    <tr>
                    	<td width="100" class="must_entry_caption">LC Company</td>
                        <td width="140"><? echo create_drop_down( "cbo_company_name",130, "select id, company_name from lib_company comp  where status_active =1 and  is_deleted=0  $company_cond  order by company_name","id,company_name", 1, "-- Select --", $selected,"load_drop_down( 'requires/linking_input_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td' ); fnc_load_party(1);location_select()","" ); ?></td>
                        <td width="100" class="must_entry_caption">LC Location</td>
                        <td width="140" id="location_td"><? echo create_drop_down( "cbo_location",  130, $blank_array, "",  1, "-- Select Location --", $selected, "",1 ); ?></td>
                        <td width="100" class="must_entry_caption">Source</td>
                        <td width="140"><? echo create_drop_down( "cbo_source", 130, $knitting_source,"", 1, "-- Select Source --", $selected, "", 1, '1' ); ?></td>
                        <td width="100" class="must_entry_caption">W. Company</td>
                        <td id="working_com"><? echo create_drop_down( "cbo_working_company", 130, $blank_array,"", 1, "-- Select --", $selected, "",1 ); ?></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">WC. Location</td>
                        <td id="working_location_td"><? echo create_drop_down( "cbo_working_location", 130, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>
                        <td class="must_entry_caption">Floor</td>
                        <td id="floor_td"><? echo create_drop_down( "cbo_floor", 130, $blank_array,"", 1,"-- Select Floor --", $selected, "",1 ); ?></td>
                        <td class="must_entry_caption">Line No.</td>
                        <td id="line_td"><? echo create_drop_down( "cbo_line_no", 130, $blank_array,"", 1, "--Select Line--", $selected, "",1 ); ?></td>
                        <td class="must_entry_caption">Input Date</td>
                        <td><input type="text" name="txt_input_date" id="txt_input_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:120px;" /></td> 
                     </tr>
                     <tr>
                     	<td>Challan No</td>
                        <td><input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text" style="width:120px" placeholder="Write" /></td>
                        <td>Remarks</td>
                        <td colspan="5"><input name="txt_remarks" id="txt_remarks" class="text_boxes" type="text" style="width:610px" /></td>
                     </tr>
                </table>
			</fieldset><br />
               
            <fieldset style="width:1100px">
				<legend>Linking Input Bundle List</legend>
                <div id="bundle_list_view">
                    <table cellpadding="0" cellspacing="2" width="1100">
                        <tr>
                            <td width="110" class="must_entry_caption" id="td_caption">Barcode No</td>
                            <td align="left" width="240"> 
                                <input name="txt_bundle_no" id="txt_bundle_no" class="text_boxes" style="width:212px" placeholder="Browse / Write / Scan" onDblClick="openmypage_bundle('requires/linking_input_controller.php?action=bundle_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value, 'Bundle Search');" />
                            </td>
                            <td width="100" class="must_entry_caption" align="right">Re-Scan Barcode</td>
                            <td> 
                               <input name="txt_bundle_rescan" placeholder="Browse / Write / Scan" onDblClick="openmypage_bundle_rescan( 'requires/linking_input_controller.php?action=bundle_popup_rescan&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Search Bundle For Rescan')" id="txt_bundle_rescan" class="text_boxes" style="width:212px" />
                            </td>
                        </tr>
                    </table>
                    <table cellpadding="0" width="1050" cellspacing="0" border="1" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th width="30">SL</th>
                                <th width="80">Bundle No</th>
                                <th width="90">Barcode No</th>
                                <th width="50">Job Year</th>
                                <th width="60">Job No</th>
                                <th width="65">Buyer</th>
                                <th width="65">Style No</th>
                                <th width="90">Order No</th>
                                <th width="100">Gmts. Item</th>
                                <th width="90">Country</th>
                                <th width="100">Gmts. Color</th>
                                <th width="60">Size</th>
                                <th width="65">Bundle Qty. (Pcs)</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                    </table>
                    <div  style="width:1050px;max-height:250px;overflow-y:scroll"  align="left">    
                        <table cellpadding="0" width="1030" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">      
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <table cellpadding="0" cellspacing="1" width="1050">
                    <tr>
                        <td align="center" colspan="14" valign="middle" class="button_container">
                            <?
                                $date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_linking_input_entry", 0,1 , "reset_form('linkinginput_1','bundle_list_view','', 'txt_input_date,".$date."','pageReset();')",1); 
                            ?>
                            <input type="hidden"  name="hidden_row_number" id="hidden_row_number"> 
                        </td>
                        <td>&nbsp;</td>				
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
	<div id="list_view_country" style="width:370px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>