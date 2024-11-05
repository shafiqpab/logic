
<?
/****************************************************************
|	Purpose			:	This form is Reference Closing
|	Functionality	:
|	JS Functions	:
|	Created by		:	MA.Kaiyum
|	Creation date 	:	11-08-2016
|	Updated by 		:	Rehan Uddin
|	Update date		:   04-23-2017
|	QC Performed BY	:
|	QC Date			:
|	Comments		:
******************************************************************/

	session_start();
	if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
	require_once('../../includes/common.php');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Reference Closing", "../../", 1, 1,'','','');
?>
<script>

<? $data_arr = json_encode($_SESSION['logic_erp']['data_arr'][735]);
if ($data_arr){
	echo "var field_level_data= " . $data_arr . ";\n";
}
?>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
		var permission='<? echo $permission; ?>';

	function fnc_ref_closing(operation)
	{
		var only_full=$('#with_full_shipment').is(':checked');
		var fso_wise_chk_active=$('#fso_wise_chk').is(':checked');
		var ref_type=$('#cbo_ref_type').val();
		
		var unclose_id=$('#unclose_id').val()*1;

		if(fso_wise_chk_active==true){var fso_wise_chk_status=1;}else{var fso_wise_chk_status=0;}
		//alert(only_full+'='+unclose_id);
		if (form_validation('cbo_company_name*txt_ref_cls_date*cbo_ref_type','Company*Closing Date*Reference Type')== false)
		{
			return;
		}
		var total_id=$('#total_id').val();
		//alert(total_id);
		if(total_id=="")
		{
			alert("Please Select Reference");return;
		}
		if(!(ref_type==163 || ref_type==370 || ref_type==104 || ref_type==105 || ref_type==106 || ref_type==2 || ref_type==108 || ref_type==94 || ref_type==144 || ref_type==140))
		{
			if(only_full==false)
			{
				var data="action=save_update_delete&operation="+operation+"&only_full="+only_full+get_submitted_data_string('cbo_company_name*txt_ref_cls_date*cbo_ref_type*total_id*update_id',"../../")+'&unclose_id='+unclose_id+'&fso_wise_chk_active='+fso_wise_chk_active; 
				//alert(data); return;
				//alert(); return;
				freeze_window(operation);
				http.open("POST","requires/reference_closing_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_reference_response;
			}
			else
			{
				alert("This Reference Already Closed");return;
			}
		}
		else
		{
			if(only_full==false || only_full==true)
			{
				var data="action=save_update_delete&operation="+operation+"&only_full="+only_full+get_submitted_data_string('cbo_company_name*txt_ref_cls_date*cbo_ref_type*total_id*update_id',"../../")+'&unclose_id='+unclose_id+'&fso_wise_chk_active='+fso_wise_chk_active;
				//alert(data); return;
				//alert(); return;
				freeze_window(operation);
				http.open("POST","requires/reference_closing_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_reference_response;
			}
			
		}
	}

	function fnc_reference_response()
	{
		if(http.readyState == 4)
		{
			//release_freezing();return;
			//alert (http.responseText); return ;
			var reponse=http.responseText.split('**');
			show_msg(trim(reponse[0]));
			//reset_form('refclosingform_1','','');
			resultofdetails();
			//set_button_status(0, permission, 'fnc_ref_closing',1);
			release_freezing();
		}
	}

	function resultofdetails()
	{
		var type=$("#cbo_ref_type").val();
		var only_full=$('#with_full_shipment').is(':checked');
		var check_only_full=$('#with_full_shipment').is(':checked');
		var fso_wise_chk_var=$('#fso_wise_chk').is(':checked');
		//alert(only_full);
		if(form_validation('cbo_company_name*cbo_ref_type','Company Name*Reference Type')==false)
		{
			return;
		}
		else
		{
			if(type==104)
			{
				var data="action=show_details_pi&type="+type+"&only_full="+Number(only_full)+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to',"../../")+'&check_only_full='+check_only_full;
			}
			else if(type==105)
			{
				var data="action=show_details_btb&type="+type+"&only_full="+Number(only_full)+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to',"../../")+'&check_only_full='+check_only_full;
			}
			else if(type==370)
			{
				var data="action=show_details_knit_qc_sweater&type="+type+"&only_full="+Number(only_full)+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to',"../../")+'&check_only_full='+check_only_full;
			}
			else if(type==2)
			{
				//alert('Not allowed.');
				//return;
				if(fso_wise_chk_var==true){
					var data="action=show_details_knit_closing_fso&type="+type+"&only_full="+Number(only_full)+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to*txt_job_no',"../../")+'&check_only_full='+check_only_full;
				}
				else
				{
					var data="action=show_details_knit_closing&type="+type+"&only_full="+Number(only_full)+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to*txt_job_no',"../../")+'&check_only_full='+check_only_full;
				}
			}
			else if(type==108)
			{
				//alert('Not allowed.');
				//return;
				var data="action=show_details_fabric_booking_closing&type="+type+"&only_full="+Number(only_full)+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to',"../../")+'&check_only_full='+check_only_full;
			}
			/*else if(type==163)
			{
				//alert('Not allowed.');
				//return;
				var data="action=show_details_order_closing2&type="+type+"&only_full="+Number(only_full)+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to',"../../")+'&check_only_full='+check_only_full;
			}*/
			else if(type==106)
			{
				//alert('Not allowed.');
				//return;
				var data="action=show_details_export_lc_closing&type="+type+"&only_full="+Number(only_full)+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to',"../../")+'&check_only_full='+check_only_full;
			}
			else if(type==94)
			{
				//alert('Not allowed.');
				//return;
				var data="action=show_details_yarn_service_closing&type="+type+"&only_full="+Number(only_full)+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to',"../../")+'&check_only_full='+check_only_full;
			}
			else if(type==144)
			{
				//alert('Not allowed.');
				//return;
				var data="action=show_details_yarn_po_closing&type="+type+"&only_full="+Number(only_full)+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to',"../../")+'&check_only_full='+check_only_full;
			}
			else if(type==140)
			{
				//alert('Not allowed.');
				//return;
				var data="action=show_details_smn_booking_closing&type="+type+"&only_full="+Number(only_full)+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to',"../../")+'&check_only_full='+check_only_full;
			}
			else
			{
				var data="action=show_details&type="+type+"&only_full="+only_full+get_submitted_data_string('cbo_company_name*txt_date_from*txt_date_to*cbo_buyer*txt_job_no',"../../");
			}
			
			// alert(data);return
			freeze_window(3);
			http.open("POST","requires/reference_closing_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_result_response;
		}
	}

	function fnc_result_response()
	{
		if(http.readyState == 4)
		{
			//alert (http.responseText); return ;
			//var reponse=http.responseText.split('**');
			//show_msg(trim(reponse[0]));
			$("#responsecontainer").html(http.responseText);
			release_freezing();
            if($('#with_full_shipment').is(':checked')){
                $('#save1').val('Reference Open');
            }else{
                $('#save1').val('Reference Close');
            }
			$('#exl_rpt_link').attr('href',document.getElementById('txt_excl_link').value);
		}
	}
	function disabled_fn()
	{
		
		$("#with_full_shipment").attr("disabled",true);
	}
	function fnc_type_fso_chk(type)
	{
		if(type==2)
		{
			$("#fso_wise_chk").attr("disabled", false);
		}
		else
		{
			$("#fso_wise_chk").attr("disabled", true);
		}
	}
	function fnc_type(type)
	{
		//alert(type);
		if(type==105)
		{
		document.getElementById('th_dynamic').innerHTML='L/C Date';
		}
		else if(type==4)
		{
		document.getElementById('th_dynamic').innerHTML='Receive Date';
		}
		else if(type==106)
		{
		document.getElementById('th_dynamic').innerHTML='LC Date';
		}
		else if(type==2)
		{
		document.getElementById('th_dynamic').innerHTML='Production Date';
		}
		else if(type==370)
		{
		document.getElementById('th_dynamic').innerHTML='Pub ShipDate';
		}
		else if(type==104)
		{
		document.getElementById('th_dynamic').innerHTML='PI Date';
		}
		else if(type==163)
		{
		document.getElementById('th_dynamic').innerHTML='Orgi. Shipdate Date';
		}
		else if(type==107)
		{
		document.getElementById('th_dynamic').innerHTML='Contract Date Date';
		}
		else if(type==69)
		{
		document.getElementById('th_dynamic').innerHTML='Requisition Date';
		}
		else if(type==117)
		{
		document.getElementById('th_dynamic').innerHTML='Requisition Date';
		}
		else if(type==70)
		{
		document.getElementById('th_dynamic').innerHTML='Requisition Date';
		}
		else if(type==108)
		{
		document.getElementById('th_dynamic').innerHTML='Booking Date';
		}
		else if(type==94)
		{
		document.getElementById('th_dynamic').innerHTML='WO Date';
		}
		else {
			document.getElementById('th_dynamic').innerHTML='Date Range';
		}
	}
	
	function check_all_data444() 
	{
		var tbl_row_count = document.getElementById( 'tbl_list' ).rows.length;
		tbl_row_count = tbl_row_count-1;
		//alert(tbl_row_count)
		if(document.getElementById('all_chk').checked==true)
		{
			//po_job_level=1;
		}
		else if(document.getElementById('all_chk').checked==false)
		{
			//po_job_level=cbo_level;
		}
		for( var i = 1; i <= tbl_row_count; i++ ) {
			js_set_value( i );
		}
	}
	function fnc_type_job_chk(type)
	{
		if(type==163 || type==2)
		{
			$("#txt_job_no").attr("disabled", false);
		}
		else
		{
			$("#txt_job_no").attr("disabled", true);
		}
	}
	function openJobPopup() 
	{
		var ref_type_chk = document.getElementById('cbo_ref_type').value;
		if( form_validation('cbo_company_name', 'Company Name')==false ) { return; }
			if(ref_type_chk==163 || ref_type_chk==2){
				var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_ref_type').value;
				emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/reference_closing_controller.php?data='+data+'&action=job_search_popup','Job Reference', 'width=1050px,height=400px,center=1,resize=1,scrolling=0','')

				emailwindow.onclose=function() {
				var theform=this.contentDoc.forms[0];
				var style_no=this.contentDoc.getElementById("selected_job").value; 
				var data=style_no.split("_");
				//alert(data);
				$('#txt_job_no').val(data);
			}
	    }
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div align="center">
<? echo load_freeze_divs ("../../", $permission);  ?>
<form  name="refclosingform_1" id="refclosingform_1" autocomplete="off">
<fieldset style="width:980px;">
    <legend>Reference Closing</legend>
    <table class="rpt_table" cellspacing="0" cellpadding="0" width="980" border="1" rules="all">
         <thead>
            <tr>
                 <th class="must_entry_caption">Company</th>
                 <th class="must_entry_caption">Closing Date</th>
                 <th class="must_entry_caption">Reference Type</th>
                 <th>Job No</th>
                 <th>Buyer</th>
                 <th id="th_dynamic">Date Range</th>
                 <th>
                 	<input type="checkbox" name="fso_wise_chk" id="fso_wise_chk" disabled="disabled" >&nbsp; FSO Wise
                 </br>
                 	<input type="checkbox" name="with_full_shipment" id="with_full_shipment" >&nbsp; Reference Closed
                 </th>
            </tr>
         </thead>
         <tbody>
            <tr>
                <td align="center" width="100">
                    <?
                    echo create_drop_down("cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "set_field_level_access(this.value):");
                    ?>
                </td>
                 <td align="center" width="80">
                    <input type="text" name="txt_ref_cls_date" id="txt_ref_cls_date" class="datepicker"  maxlength="50" title="Maximum 50 Character" style="width:70px;" value="<? echo date("d-m-Y"); ?>"/>
                </td>
                <td align="center" width="100">
                    <?
                        echo create_drop_down( "cbo_ref_type", 160, $entry_form,"", 1, "-- Select Ref.Type --", $selected,"fnc_type(this.value);fnc_type_fso_chk(this.value);fnc_type_job_chk(this.value);load_drop_down( 'requires/reference_closing_controller', this.value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );","","2,4,69,70,94,100,101,102,103,104,105,106,107,117,163,370,108,144,140" );
                    ?>
                </td>
                <td align="center" width="120" id="job_td">
				
                   	<input type="text" name="txt_job_no" id="txt_job_no" title="Double Click to Search" class="text_boxes" style="width:150px" placeholder="Browse"  onDblClick="openJobPopup();" readonly  />
                </td>
                <td align="center" width="130" id="buyer_td">
                    <?
                    echo create_drop_down( "cbo_buyer", 130, $blank_array,"", 1, "-- Select Buyer --", "","", 1);
                    ?>
                </td>
                <td align="center" width="180">
                     <input type="text" name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:65px;" placeholder="From Date" readonly /> To
                     <input type="text" name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:65px;" placeholder="To Date" readonly />
                </td>
                <td align="center">
                     <input type="button" name="search" id="search" value="Show" onClick="resultofdetails();" style="width:70px" class="formbutton" />
                </td>
            </tr>
             <tr>
                <td colspan="13" align="center">
                    <? echo load_month_buttons(1); ?>
                </td>
            </tr>
            <tr>
                <input type="hidden" id="update_id" name="update_id"/>
            </tr>
            <tr>
                <td colspan="8" align="center" height="30" valign="bottom">
                   <div id="report_container" align="center" style="margin-top:10px; margin-bottom:10px">
                        <input style="display:none;" type="button" id="reprt_html" onClick="view_html_report_lp()" class="formbutton" value="HTML Preview">&nbsp;&nbsp;
                            <a id="exl_rpt_link"><input type="button" id="reprt_excl" class="formbutton" value="Download Excel"></a>
                        </a>
                    </div>
                </td>
            </tr>
            <tr>
                <td  align="center" colspan="10"  class="button_container">
                <input id="save1" class="formbutton" type="button" style="width:110px" onClick="fnc_ref_closing(0)" name="save" value="Reference Close">
                <?
                //echo load_submit_buttons( $permission, "fnc_ref_closing", 0,0 ,"reset_form('refclosingform_1','','')",1);
                ?>
                </td>
            </tr>
         </tbody>
    </table>
    <div id="report_container2"> </div>
</fieldset>
<fieldset style="width:90%;" id="responsecontainer"></fieldset>
</form>
</div>
</body>
<script>
function view_html_report_lp()
{
    $('#table_body tbody tr:first').hide();
    //return;
    var response = document.getElementById('report_container2').innerHTML;
    var w = window.open("Surprise", "#");
    var d = w.document.open();
    $('#table_body tbody tr:first').show();
    d.write(response);
    d.close();
}
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
