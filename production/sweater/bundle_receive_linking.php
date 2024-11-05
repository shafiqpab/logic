<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Bundle Receive In Linking
				
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
echo load_html_head_contents("Bundle Receive In Linking","../../", 1, 1, $unicode,'','');
?>
	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function fnc_duplicate_bundle(barcode_no)
	{
		var challan_duplicate=return_ajax_request_value( barcode_no,"challan_duplicate_check", "requires/bundle_receive_linking_controller");
		var ex_challan_duplicate=challan_duplicate.split("_");
		if(ex_challan_duplicate[0]==2) 
		{
			var alt_str=ex_challan_duplicate[1].split("*");
			var al_msglc="Bundle No '"+trim(alt_str[0])+"' Found in Challan No '"+trim(alt_str[1])+"'";
			alert(al_msglc);
			//$('#txt_issue_no').val('');
			return;
		}
		else
		{
			create_row( $('#txt_issue_no').val() ,'scan',"");
		}
		//$('#txt_issue_no').val('');
	}
	
	 $('#txt_issue_no').live('keydown', function(e) {
        if (e.keyCode === 13)
        {
            e.preventDefault();
            var txt_issue_no = trim($('#txt_issue_no').val().toUpperCase());
		
            var flag = 1;
            $("#tbl_details").find('tbody tr').each(function()
            {
                var bundleNo = $(this).find("td:eq(1)").text();
				var barcodeNo=$(this).find("td:eq(1)").attr('title');
                if (txt_bundle_no == barcodeNo) {
                    alert("Issue No: " + bundleNo + " already scan, try another one.");
                    $('#txt_issue_no').val('');
                    flag = 0;
                    return false;
                }
            });

            if (flag == 1)
            {
                fnc_duplicate_bundle(txt_issue_no);
            }
        }
    });

	function create_row(issue_no, vscan, hidden_source_cond)
    {
        freeze_window(5);

   		var response_mstdata = return_global_ajax_value(issue_no, 'populate_mst_data', '', 'requires/bundle_receive_linking_controller');
		var ex_data=response_mstdata.split('_');
		$('#txt_issue_id').val(ex_data[0]);
		$('#txt_issue_no').val(ex_data[1]);
		$('#cbo_company_name').val(ex_data[2]);
		
		$('#cbo_source').val(ex_data[4]);
		fnc_load_party(1);
		load_drop_down( 'requires/bundle_receive_linking_controller', ex_data[2]+'_'+1, 'load_drop_down_location', 'location_td' );
		$('#cbo_location').val(ex_data[3]);
		$('#cbo_working_company').val(ex_data[5]);
		fnc_load_party(2);
		$('#cbo_working_location').val(ex_data[6]);
		
		if(ex_data[4]==1) var location=ex_data[3]; else  var location=ex_data[6];
		load_drop_down('requires/bundle_receive_linking_controller', location, 'load_drop_down_floor', 'floor_td' );
		$('#cbo_floor').val(ex_data[7]);
		//$('#txt_receive_date').val(ex_data[8]);
		$('#txt_challan_no').val(ex_data[9]);
		$('#txt_remarks').val(ex_data[10]);
		
		var row_num =  $('#tbl_details tbody tr').length; //$('#hidden_row_number').val();

		var response_data = return_global_ajax_value(ex_data[0]+"**"+ ex_data[2]+"**1", 'populate_bundle_data_update', '', 'requires/bundle_receive_linking_controller');
		
		$('#tbl_details tbody tr').remove();
		$('#tbl_details tbody').prepend(response_data);

		var tot_row=$('#tbl_details tbody tr').length; 
		$('#hidden_row_number').val(tot_row);
		if(tot_row>0){
                    fn_totalqty();
				}
        release_freezing();
    }

	function fn_deleteRow(id)
	{   
		// alert('Test');
		var counter =$('#tbl_details tbody tr').length;
		$("#barcode_"+id).closest('tbody tr').remove();
		//calculate_defect_qty();
		var numRow = $('table#tbl_details tbody tr').length;
		for(var i=1; i<=counter; i++)
		{
			var index=i-1;
			$("#tbl_details tbody tr:eq("+index+")").find("input").each(function() {
				$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				});
			});
			
			
		}
		
		rearrange_serial_no();
	    fn_totalqty();
	}

	function fn_totalqty()
	{
		var tblRow = $("#tbl_details tbody tr").length;
		// alert(tblRow);

	    math_operation( "total_qty", "txtqty_", "+", tblRow );
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

	function fnc_receive_linking_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#delivery_basis').val()+'*'+report_title, 'emblishment_issue_print', 'requires/print_embro_delivery_entry_controller');
			return;
		}
		
		if (form_validation('txt_issue_no*cbo_company_name*cbo_location*cbo_source*cbo_working_company*cbo_working_location*cbo_floor*txt_receive_date','Isuue No*LC Company*LC Location*Source*W. Company*WC. Location*Floor*Receive Date')==false )
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
				var qty 			=$(this).find("td:eq(5)").text();
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
			
			var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_system_no*txt_update_id*txt_issue_id*txt_issue_no*cbo_company_name*cbo_location*cbo_source*cbo_working_company*cbo_working_location*cbo_floor*txt_receive_date*txt_challan_no*txt_job_no*txt_remarks*garments_nature',"../../")+dataString;
			
			freeze_window(operation);
			//alert(data); return;
			http.open("POST","requires/bundle_receive_linking_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_receive_linking_entry_reply_info;
		}
	}
  
	function fnc_receive_linking_entry_reply_info()
	{
		if(http.readyState == 4) 
		{		
			var reponse=http.responseText.split('**');	
			// alert(http.responseText);	 
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_receive_linking_entry('+ reponse[1]+')',4000); 
			}
			else if(reponse[0]==0 || reponse[0]==1)
			{
				if(reponse[4]){ alert("Receive Found Bundle List : "+reponse[3]+" This Bundle Not Any Change.");}
				show_msg(trim(reponse[0]));
				
				document.getElementById('txt_update_id').value = reponse[1];
				document.getElementById('txt_system_no').value = reponse[2];
				document.getElementById('txt_challan_no').value = reponse[3];
				set_button_status(1, permission, 'fnc_receive_linking_entry',1,1);	
			}
			else if(reponse[0]==2)
			{
				pageReset();
			}
			if(reponse[0]!=15)
			{
			  release_freezing();
			}
		}
	} 

	function openmypage_sysNo()
	{
		var title = 'Receive Challan Selection Form';	
		var page_link = 'requires/bundle_receive_linking_controller.php?action=system_number_popup';
			
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
				load_drop_down( 'requires/bundle_receive_linking_controller', ex_data[2]+'_'+1, 'load_drop_down_location', 'location_td' );
				$('#cbo_location').val(ex_data[3]);
				$('#cbo_working_company').val(ex_data[5]);
				fnc_load_party(2);
				$('#cbo_working_location').val(ex_data[6]);
				
				if(ex_data[4]==1) var location=ex_data[3]; else  var location=ex_data[6];
				load_drop_down('requires/bundle_receive_linking_controller', location, 'load_drop_down_floor', 'floor_td' );
				$('#cbo_floor').val(ex_data[7]);
				$('#txt_receive_date').val(ex_data[8]);
				$('#txt_challan_no').val(ex_data[9]);
				$('#txt_remarks').val(ex_data[10]);
				
				$('#txt_issue_id').val(ex_data[11]);
				$('#txt_issue_no').val(ex_data[12]);
	
				var response_data = return_global_ajax_value(ex_data[0]+"**"+ ex_data[2]+"**2", 'populate_bundle_data_update', '', 'requires/bundle_receive_linking_controller');
				
				$('#tbl_details tbody tr').remove();
                $('#tbl_details tbody').prepend(response_data);
	
				var tot_row=$('#tbl_details tbody tr').length;
				$('#hidden_row_number').val(tot_row);
				set_button_status(1, permission, 'fnc_receive_linking_entry',1,0);
				release_freezing();
				if(tot_row>0){
                    fn_totalqty();
				}
			}
		}
	}//end function
	
	function openmypage_issue_no()
	{
		var title = 'Issue Challan Selection Form';	
		var page_link = 'requires/bundle_receive_linking_controller.php?action=issueNo_popup';
			
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_data=this.contentDoc.getElementById("hidd_str_data").value;//po id
			if(mst_data!="")
			{ 
				freeze_window(5);
				
				var ex_data=mst_data.split('_');
				
				$('#txt_issue_id').val(ex_data[0]);
				$('#txt_issue_no').val(ex_data[1]);
				$('#cbo_company_name').val(ex_data[2]);
				
				$('#cbo_source').val(ex_data[4]);
				fnc_load_party(1);
				load_drop_down( 'requires/bundle_receive_linking_controller', ex_data[2]+'_'+1, 'load_drop_down_location', 'location_td' );
				$('#cbo_location').val(ex_data[3]);
				$('#cbo_working_company').val(ex_data[5]);
				fnc_load_party(2);
				$('#cbo_working_location').val(ex_data[6]);
				
				if(ex_data[4]==1) var location=ex_data[3]; else  var location=ex_data[6];
				load_drop_down('requires/bundle_receive_linking_controller', location, 'load_drop_down_floor', 'floor_td' );
				$('#cbo_floor').val(ex_data[7]);
				//$('#txt_receive_date').val(ex_data[8]);
				$('#txt_challan_no').val(ex_data[9]);
				$('#txt_remarks').val(ex_data[10]);
	
				var response_data = return_global_ajax_value(ex_data[0]+"**"+ ex_data[2]+"**1", 'populate_bundle_data_update', '', 'requires/bundle_receive_linking_controller');
				
				$('#tbl_details tbody tr').remove();
                $('#tbl_details tbody').prepend(response_data);
	
				var tot_row=$('#tbl_details tbody tr').length; 
				$('#hidden_row_number').val(tot_row);
				//set_button_status(1, permission, 'fnc_receive_linking_entry',1,0);
				if(tot_row>0){
                    fn_totalqty();
				}
			   
				release_freezing();
			}
		}
	}//end function
	
	function pageReset(){
		location.reload();
	}
	
/*	function generate_report_file(data,action,page)
	{
		window.open("requires/print_embro_delivery_entry_controller.php?data=" + data+'&action='+action, true );
	}
	
*/	function fnc_load_party(type)
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
			load_drop_down( 'requires/bundle_receive_linking_controller', company+'_'+1, 'load_drop_down_working_com', 'working_com' );
		}
		else if(source==2 && type==1)
		{
			load_drop_down( 'requires/bundle_receive_linking_controller', company+'_'+2, 'load_drop_down_working_com', 'working_com' );
		}
		else if(source==1 && type==2)
		{
			load_drop_down( 'requires/bundle_receive_linking_controller', working_company+'_'+2, 'load_drop_down_location', 'working_location_td' ); 
		} 
	}
</script>
</head>
<body onLoad="set_hotkey()">
 <div style="width:100%;">
  	<? echo load_freeze_divs ("../",$permission);  ?>
    <div style=" float:left" align="left"> 
 		
        <form name="bundlereceivelinking_1" id="bundlereceivelinking_1" method="" autocomplete="off" >
			<fieldset style="width:1050px;">
        		<legend>Bundle Receive In Linking</legend>
                <table width="1050">
                    <tr>
                        <td align="right" colspan="4"><b>RECEIVE NO</b></td>
                        <td colspan="4"> 
                            <input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_sysNo();" placeholder="Browse" readonly />
                            <input name="txt_update_id" id="txt_update_id" type="hidden" /> 
                            <input name="txt_job_no" id="txt_job_no" type="hidden" />
                        </td>
                    </tr>
                    <tr>
                    	<td width="100" class="must_entry_caption">Issue NO</td>
                        <td width="140"><input name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:120px" placeholder="Browse / Write / Scan" onDblClick="openmypage_issue_no('requires/bundle_receive_linking_controller.php?action=bundle_popup&company='+document.getElementById('cbo_company_name').value+'&garments_nature='+document.getElementById('garments_nature').value, 'Issue Challan Search');" />
                        	<input name="txt_issue_id" id="txt_issue_id" type="hidden" />
                        </td>
                    	<td width="100" class="must_entry_caption">LC Company</td>
                        <td width="140"><? echo create_drop_down( "cbo_company_name",130, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0  $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected,"load_drop_down( 'requires/bundle_receive_linking_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td' ); fnc_load_party(1)",1 ); ?></td>
                        <td width="100" class="must_entry_caption">LC Location</td>
                        <td width="140" id="location_td"><? echo create_drop_down( "cbo_location",  130, $blank_array, "",  1, "-- Select Location --", $selected, "",1 ); ?></td>
                        <td width="100" class="must_entry_caption">Source</td>
                        <td><? echo create_drop_down( "cbo_source", 130, $knitting_source,"", 1, "-- Select Source --", $selected, "", 1, '1' ); ?></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">W. Company</td>
                        <td id="working_com"><? echo create_drop_down( "cbo_working_company", 130, $blank_array,"", 1, "-- Select --", $selected, "",1 ); ?></td>
                    	<td class="must_entry_caption">WC. Location</td>
                        <td id="working_location_td"><? echo create_drop_down( "cbo_working_location", 130, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>
                        <td class="must_entry_caption">Floor</td>
                        <td id="floor_td"><? echo create_drop_down( "cbo_floor", 130, $blank_array,"", 1,"-- Select Floor --", $selected, "",1 ); ?></td>
                        <td class="must_entry_caption">Receive Date</td>
                        <td><input type="text" name="txt_receive_date" id="txt_receive_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:120px;" /></td> 
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
				<legend>Bundle Receive In Linking Info</legend>
                <div id="bundle_list_view">
                    <table cellpadding="0" width="1100" cellspacing="0" border="1" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th width="30" rowspan="2">SL</th>
                                <th width="80" rowspan="2">Bundle No</th>
                                <th width="90" rowspan="2">Barcode No</th>
                                <th width="100" rowspan="2">Gmts. Color</th>
                                <th width="60" rowspan="2">Size</th>
                                <th width="65" rowspan="2">Bundle Qty. (Pcs)</th>
                                <th colspan="2">RMG No.</th>
                                <th width="50" rowspan="2">Job Year</th>
                                <th width="60" rowspan="2">Job No</th>
                                <th width="65" rowspan="2">Buyer</th>
                                <th width="65" rowspan="2">Style No</th>
                                <th width="90" rowspan="2">Order No</th>
                                <th width="100" rowspan="2">Gmts. Item</th>
                                <th width="90" rowspan="2">Country</th>
                                <th rowspan="2">&nbsp;</th>
                            </tr>
                            <tr>
                                <th width="40">From</th>
                                <th width="40">To</th>
                            </tr>
                        </thead>
                    </table>
                    <div  style="width:1100px;max-height:250px;overflow-y:scroll"  align="left">    
                        <table cellpadding="0" width="1080" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_details">      
                            <tbody>
                               
                            </tbody>
							<tfoot class="tbl_bottom">
								<tr>
									<td colspan="4"></td>
									<td align="left">Total: </td>
									<td align="right">
								      <input type="text" name="total_qty" id="total_qty" class="text_boxes_numeric" value="" style="width:60px;" readonly/></td> 
									<td colspan="9"></td>
								</tr>
							</tfoot>
                        </table>
                    </div>
                </div>
                
                <table cellpadding="0" cellspacing="1" width="1100">
                    <tr>
                        <td align="center" colspan="9" valign="middle" class="button_container">
                            <?
                                $date=date('d-m-Y');
                                echo load_submit_buttons( $permission, "fnc_receive_linking_entry", 0,1 , "reset_form('bundlereceivelinking_1','bundle_list_view','', 'txt_receive_date,".$date."','pageReset();')",1); 
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