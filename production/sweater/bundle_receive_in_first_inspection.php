<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Bundle Receive In First Inspection
				
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	01-03-2020
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
echo load_html_head_contents("Bundle Receive In First Inspection","../../", 1, 1, $unicode,'','');
?>
	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function fnc_duplicate_bundle(txt_issue_no)
    {
        var challan_duplicate = '';//return_ajax_request_value(txt_issue_no, "challan_duplicate_check", "requires/bundle_receive_in_first_inspection_controller");
        //var ex_challan_duplicate = challan_duplicate.split("_");
       
		if ( trim( challan_duplicate)!= '')
        {
            alert(trim(challan_duplicate));
            $('#txt_issue_no').val('');
            return;
        }
        else
        {
            create_row(txt_issue_no,0,'');
			var tot_row=$('#tbl_details tbody tr').length; 
        }
       // $('#txt_issue_no').val('');
    }
	
	 $('#txt_issue_no').live('keydown', function(e) {
        if (e.keyCode === 13)
        {
			if ( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		
            e.preventDefault();
            var txt_issue_no = trim($('#txt_issue_no').val().toUpperCase());
		
            var flag = 1;
            $("#tbl_details").find('tbody tr').each(function()
            {
                var bundleNo = $(this).find("td:eq(1)").text();
				var barcodeNo=$(this).find("td:eq(2)").text();
               /* if (txt_issue_no == barcodeNo) {
                    alert("Bundle No: " + bundleNo + " already scan, try another one.");
                    $('#txt_issue_no').val('');
                    flag = 0;
                    return false;
                }*/
            });

            if (flag == 1)
            {
                fnc_duplicate_bundle(txt_issue_no);
            }
        }
    });

	function create_row(issue_no, vscan, issue_id)
    {
        freeze_window(5);
		
		if(vscan==1)
		{
			//show_list_view(issue_id, 'populate_bundle_data', 'bundle_list_view', 'requires/bundle_receive_in_first_inspection_controller', '');
			//show_list_view(issue_id, 'show_dtls_yarn_listview', 'yarn_list_view', 'requires/bundle_receive_in_first_inspection_controller', '');
		}
		else
		{
			//get_php_form_data(bundle_nos, "populate_data_from_yarn_lot_bundle", "requires/bundle_receive_in_first_inspection_controller");
			//show_list_view(bundle_nos, 'show_dtls_listview_bundle', 'bundle_list_view', 'requires/bundle_receive_in_first_inspection_controller', '');
			//show_list_view(bundle_nos, 'show_dtls_yarn_listview', 'yarn_list_view', 'requires/bundle_receive_in_first_inspection_controller', '');
		}		
		//calculate_yarn_qty();
	
		//release_freezing();

        var row_num =  $('#tbl_details tbody tr').length; //$('#hidden_row_number').val();
   
        var response_data = return_global_ajax_value(issue_no + "**" + row_num + "****" + $('#cbo_company_name').val() + "**" + vscan + "**" + issue_id, 'populate_bundle_data', '', 'requires/bundle_receive_in_first_inspection_controller');
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
		fnc_total_calculate();
        release_freezing();
    }
	
	function openmypage_issue_popup(page_link,title)
	{
		if ( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var title='Bundle Search';
		var page_link='	requires/bundle_receive_in_first_inspection_controller.php?action=issue_popup&company_id='+document.getElementById('cbo_company_name').value;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=890px,height=370px,center=1,resize=0,scrolling=0', '../')
									
		emailwindow.onclose=function()
		{
			var theform				=this.contentDoc.forms[0];
			var hidden_system_id	=this.contentDoc.getElementById("hidden_system_id").value;
			var ex_data=hidden_system_id.split('_');
			$("#txt_issue_no").val(ex_data[1]);
			
			if (ex_data[1]!="")
			{ 
				//get_php_form_data(hidden_system_id, "populate_data_from_yarn_issue", "requires/bundle_receive_in_first_inspection_controller");
				create_row('',1,ex_data[0]);
			}
		}
		
	}//end function

	function openmypage_bundle(page_link,title)
	{
		if ( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var bundleNo='';
		$("#tbl_details").find('tbody tr').each(function()
		{
			bundleNo+=$(this).find("td:eq(1)").text()+',';
			
		});
		
		var title='Bundle Search';
		var page_link='	requires/bundle_receive_in_first_inspection_controller.php?action=bundle_popup&company_id='+document.getElementById('cbo_company_name').value+'&bundleNo='+bundleNo;
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link+'&bundleNo='+bundleNo, title,'width=1050px,height=370px,center=1,resize=0,scrolling=0','../')
									
		emailwindow.onclose=function()
		{
			var theform				=this.contentDoc.forms[0];
			var hidd_str_data	=this.contentDoc.getElementById("hidd_str_data").value;
			var ex_data=hidd_str_data.split('_');
			
			
			if (ex_data[1]!="")
			{ 
				create_row(ex_data[1],"Browse");
			}
		}
	}//end function

	function fn_deleteRow(id)
	{
		$("#txtqty_"+id).closest('tr').remove();
		fnc_total_calculate();
		//calculate_defect_qty();
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

	function fnc_rec_first_inspection_entry(operation)
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted.");
			release_freezing();
			return;
		}
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_no').val()+'*'+$('#txt_update_id').val()+'*'+report_title, 'receive_in_1st_inspection_print', 'requires/bundle_receive_in_first_inspection_controller');
			release_freezing();
			return;
		}
		
		if (form_validation('cbo_company_name*cbo_source*txt_issue_date','LC Company*Source*Issue Date')==false )
		{
			release_freezing();
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
					release_freezing();
					return;
				}
			});
			
			if(j<1)
			{
				alert('No data Found.');
				release_freezing();
				return;
			}
			
			var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_system_no*txt_update_id*cbo_company_name*cbo_source*txt_issue_date*txt_challan_no*txt_job_no*txt_remarks*garments_nature*hidden_issue_id',"../../")+dataString;
			
			//alert(data); release_freezing(); return;
			http.open("POST","requires/bundle_receive_in_first_inspection_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_rec_first_inspection_entry_reply_info;
		}
	}
  
	function fnc_rec_first_inspection_entry_reply_info()
	{
		if(http.readyState == 4) 
		{		
			var reponse=http.responseText.split('**');
			if(trim(reponse[0])=='insRec'){
				alert("Inspection Receive Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible.")
				release_freezing();
				return;
			}	 
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_rec_first_inspection_entry('+ reponse[1]+')',4000); 
			}
			else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				if(reponse[4]){ alert("Receive Found Bundle List : "+reponse[3]+" This Bundle Not Any Change.");}
				show_msg(trim(reponse[0]));
				
				document.getElementById('txt_update_id').value = reponse[1];
				document.getElementById('txt_system_no').value = reponse[2];
				document.getElementById('txt_challan_no').value = reponse[3];
				fnc_total_calculate();
				set_button_status(1, permission, 'fnc_rec_first_inspection_entry',1,1);	
			}
			if(reponse[0]!=15)
			{
			  release_freezing();
			}
		}
	} 

	function openmypage_sysNo()
	{
		if ( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var title = 'Challan Selection Form';	
		var page_link = 'requires/bundle_receive_in_first_inspection_controller.php?action=system_number_popup&company_id='+document.getElementById('cbo_company_name').value;
			
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=890px,height=390px,center=1,resize=0,scrolling=0','../')
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
				
				$('#cbo_source').val(ex_data[3]);
				//fnc_load_party(1);
				//load_drop_down( 'requires/bundle_receive_in_first_inspection_controller', ex_data[2]+'_'+1, 'load_drop_down_location', 'location_td' );
				//$('#cbo_location').val(ex_data[3]);
				//$('#cbo_working_company').val(ex_data[5]);
				//fnc_load_party(2);
				//$('#cbo_working_location').val(ex_data[6]);
				
				//if(ex_data[4]==1) var location=ex_data[6]; else  var location=ex_data[3];
				//load_drop_down('requires/bundle_receive_in_first_inspection_controller', location, 'load_drop_down_floor', 'floor_td' );
				//$('#cbo_floor').val(ex_data[7]);
				$('#txt_issue_date').val(ex_data[4]);
				
				//load_drop_down( 'requires/bundle_receive_in_first_inspection_controller', ex_data[5]+'_'+ex_data[6]+'_'+ex_data[7]+'_'+ex_data[8], 'load_drop_down_line', 'line_td');
				$('#txt_challan_no').val(ex_data[5]);
				$('#txt_remarks').val(ex_data[6]);
				$('#hidden_issue_id').val(ex_data[7]);
				$('#txt_issue_no').val(ex_data[8]);
				//$('#cbo_line_no').val(ex_data[11]);
				
				$('#cbo_company_name').attr('disabled', 'disabled');
				//$('#cbo_location').attr('disabled', 'disabled');
				//$('#cbo_source').attr('disabled', 'disabled');
				//$('#cbo_working_company').attr('disabled', 'disabled');
				//$('#cbo_working_location').attr('disabled', 'disabled');
				//$('#cbo_floor').attr('disabled', 'disabled');
	
				var response_data = return_global_ajax_value(ex_data[0]+"**"+ ex_data[2], 'populate_bundle_data_update', '', 'requires/bundle_receive_in_first_inspection_controller');
				
				$('#tbl_details tbody tr').remove();
                $('#tbl_details tbody').prepend(response_data);
	
				var tot_row=$('#tbl_details tbody tr').length; 
				$('#hidden_row_number').val(tot_row);
				fnc_total_calculate();
				set_button_status(1, permission, 'fnc_rec_first_inspection_entry',1,1);
				release_freezing();
			}
		}
	}//end function
	
	function pageReset(){
	
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/bundle_receive_in_first_inspection_controller.php?data=" + data+'&action='+action, true );
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
			load_drop_down( 'requires/bundle_receive_in_first_inspection_controller', company+'_'+1, 'load_drop_down_working_com', 'working_com' );
		}
		else if(source==2 && type==1)
		{
			load_drop_down( 'requires/bundle_receive_in_first_inspection_controller', company+'_'+2, 'load_drop_down_working_com', 'working_com' );
		}
		else if(source==1 && type==2)
		{
			load_drop_down( 'requires/bundle_receive_in_first_inspection_controller', working_company+'_'+2, 'load_drop_down_location', 'working_location_td' ); 
		} 
	}
	
	function fnc_total_calculate()
	{
		var tot_row=$('#tbl_details tr').length;
		//alert(tot_row)
		var qty=0;
		for (var i=1; i<=tot_row; i++)
		{
			qty=qty+$('#prodQty_'+i).text()*1;
		}
		
		$('#totQty').text(qty);
	}
</script>
</head>
<body onLoad="set_hotkey()">
 <div style="width:100%;">
  	<? echo load_freeze_divs ("../",$permission);  ?>
    <div style=" float:left" align="left"> 
        <form name="recfirstinspection_1" id="recfirstinspection_1" method="" autocomplete="off" >
			<fieldset style="width:1050px;">
        		<legend>Bundle Receive In First Inspection</legend>
                <table width="1050">
                    <tr>
                    	<td width="80" class="must_entry_caption">LC Company</td>
                        <td width="120"><? echo create_drop_down( "cbo_company_name",110, "select id, company_name from lib_company comp  where status_active =1 and  is_deleted=0  $company_cond  order by company_name","id,company_name", 1, "-- Select --", $selected,"","" ); //load_drop_down( 'requires/bundle_receive_in_first_inspection_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td' ); fnc_load_party(1)?></td>
                        <td width="100">Sys. Receive No</td>
                        <td width="120"> 
                            <input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text" style="width:100px" onDblClick="openmypage_sysNo();" placeholder="Browse" readonly/>
                            <input name="txt_update_id" id="txt_update_id" type="hidden" /> 
                            <input name="txt_job_no" id="txt_job_no" type="hidden" />
                            <input name="hidden_issue_id" id="hidden_issue_id" type="hidden" />
                        </td>
                        <td width="80" class="must_entry_caption">Source</td>
                        <td width="120"><? echo create_drop_down( "cbo_source", 110, $knitting_source,"", 1, "-- Select Source --", $selected, "", 1, '1' ); ?></td>
                        <td width="80" class="must_entry_caption">Receive Date</td>
                        <td><input type="text" name="txt_issue_date" id="txt_issue_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:80px;" /></td> 
                        <td>Challan No</td>
                        <td><input name="txt_challan_no" id="txt_challan_no" class="text_boxes" type="text" style="width:100px" placeholder="Write" /></td>
                    </tr>
                    <tr>
                        <td>Remarks</td>
                        <td><input name="txt_remarks" id="txt_remarks" class="text_boxes" type="text" style="width:100px" /></td>
                        <td class="must_entry_caption" id="td_caption"><b>Issue No:</b></td>
                        <td colspan="2"> 
                                <input name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:180px" placeholder="Browse / Write / Scan" onDblClick="openmypage_issue_popup('requires/bundle_receive_in_first_inspection_controller.php?action=bundle_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value, 'Bundle Search');" />
                       </td>
                       <td>&nbsp;</td>
                       <td>&nbsp;</td>
                       <td>&nbsp;</td>
                     </tr>
                </table>
			</fieldset>
               
            <fieldset style="width:1205px">
				<legend>Bundle Receive In First Inspection Bundle List</legend>
                <div id="bundle_list_view">
                    <table cellpadding="0" cellspacing="2" width="1100" style="display:none">
                        <tr>
                            <td width="100" class="must_entry_caption" align="right" style="display:none">Re-Scan Barcode</td>
                            <td style="display:none"> 
                               <input name="txt_bundle_rescan" placeholder="Browse / Write / Scan" onDblClick="openmypage_bundle_rescan( 'requires/bundle_receive_in_first_inspection_controller.php?action=bundle_popup_rescan&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value,'Search Bundle For Rescan')" id="txt_bundle_rescan" class="text_boxes" style="width:212px" />
                            </td>
                        </tr>
                    </table>
                    <table cellpadding="0" width="1205" cellspacing="0" border="1" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th width="30">SL</th>
                                <th width="80">Bundle No</th>
                                <th width="90">QR Code</th>
                                <th width="100">Gmts. Color</th>
                                <th width="60">Size</th>
                                <th width="65">Knit Qty. (Pcs)</th>
                                <th width="70">Knit. Comp.</th>
                                <th width="90">Knit Floor</th>
                                
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
                    <div  style="width:1205px;max-height:250px;overflow-y:scroll"  align="left">    
                        <table cellpadding="0" width="1185" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">      
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <table cellpadding="0" width="1205" cellspacing="0" border="1" class="tbl_bottom" rules="all">
                        <thead>
                            <tr>
                                <td width="30">&nbsp;</td>
                                <td width="80">&nbsp;</td>
                                <td width="90">&nbsp;</td>
                                <td width="100">Total:</td>
                                <td width="60">&nbsp;</td>
                                <td width="65" id="totQty"></td>
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
                
                <table cellpadding="0" cellspacing="1" width="1205">
                    <tr>
                        <td align="center" valign="middle" class="button_container">
                            <?
                                $date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_rec_first_inspection_entry", 0,1 , "reset_form('recfirstinspection_1','bundle_list_view','', 'txt_issue_date,".$date."','pageReset();')",1); 
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