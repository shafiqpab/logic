<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Woven Order color size update
Functionality	:
JS Functions	:
Created by		:	Zakaria joy
Creation date 	: 	09-12-2021
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
echo load_html_head_contents("Order Color Size Update","../../", 1, 1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	function openmypage_job(page_link,title){
		hide_left_menu("Button1");
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var garments_nature=document.getElementById('garments_nature').value;
		page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1210px,height=430px,center=1,resize=0,scrolling=0','../')		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_id=this.contentDoc.getElementById("job_id").value;
			var job_no=this.contentDoc.getElementById("job_no").value;
			$('#hidden_po_id').val(job_id);
			$('#txt_job_no').val(job_no);
			/*freeze_window(3);
			if(job_id!=''){
				var report_title=$( "div.form_caption" ).html();
				var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_job_no*hidden_po_id',"../../")+'&report_title='+report_title;
				http.open("POST","requires/order_color_size_update_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse;
			}*/

		}
	}
	function fn_report_generated(operation)
	{
		if(form_validation('txt_job_no','Job Number')==false){
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			if(operation==1)
			{
			var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_job_no*hidden_po_id',"../../")+'&report_title='+report_title;
			}
			if(operation==2)
			{
			var data="action=report_generate2&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_job_no*hidden_po_id',"../../")+'&report_title='+report_title;
			}
			freeze_window(3);
			http.open("POST","requires/order_color_size_update_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			$('#report_container4').html(reponse[0]);			
	 		show_msg('3');
			release_freezing();
		}
	}
	function set_checkvalue(id)
	{
		document.getElementById('chk_job').value=0;
		document.getElementById('chk_po').value=0;
		document.getElementById('chk_country').value=0;
		document.getElementById('chk_color').value=0;
		document.getElementById('chk_size').value=0;

		if(id==1){
			if(document.getElementById('chk_job').value==0){
				document.getElementById('chk_job').value=1;
			}
			else document.getElementById('chk_job').value=0;
		}
		else if(id==2){
			if(document.getElementById('chk_po').value==0){
				document.getElementById('chk_po').value=1;	
			}
			else document.getElementById('chk_po').value=0;
		}
		else if(id==3){
			if(document.getElementById('chk_country').value==0){
				document.getElementById('chk_country').value=1;
			}
			else document.getElementById('chk_country').value=0;
		}
		else if(id==4){
			if(document.getElementById('chk_color').value==0){
				document.getElementById('chk_color').value=1;
			}
			else document.getElementById('chk_color').value=0;
		}
		else{
			if(document.getElementById('chk_size').value==0){
				document.getElementById('chk_size').value=1;
			}
			else document.getElementById('chk_size').value=0;
		}		
	}
	function copy_value(value,field_id,i)
	{
		var jobid=document.getElementById('jobid_'+i).value*1;
		var gmtssizesid=document.getElementById('gmtssizesid_'+i).value*1;
		var ponoid=document.getElementById('poid_'+i).value*1;
		var countryid=document.getElementById('countryid_'+i).value*1;
		var gmtscolorid=document.getElementById('gmtscolorid_'+i).value*1;
		var rowCount = $('#color_size_data tr').length;

		var chk_job=document.getElementById('chk_job').value;
		var chk_po=document.getElementById('chk_po').value;
		var chk_country=document.getElementById('chk_country').value;
		var chk_color=document.getElementById('chk_color').value;
		var chk_size=document.getElementById('chk_size').value;
		var qty=0;

		if(field_id=='orderrate_'){
			for(var j=i; j<=rowCount; j++)
			{
				if(chk_job==1){
					if( jobid==document.getElementById('jobid_'+j).value*1)
					{
						document.getElementById(field_id+j).value=value;
						qty=document.getElementById('gmtsqty_'+j).value;
						document.getElementById('ordeamount_'+j).value=number_format_common(value*qty,2);
					} 
				}
				else if(chk_po==1){
					if( ponoid==document.getElementById('poid_'+j).value*1){
						document.getElementById(field_id+j).value=value;
						qty=document.getElementById('gmtsqty_'+j).value;
						document.getElementById('ordeamount_'+j).value=number_format_common(value*qty,2);
					} 
				}
				else if(chk_country==1){
					if( countryid==document.getElementById('countryid_'+j).value){
						document.getElementById(field_id+j).value=value;
						qty=document.getElementById('gmtsqty_'+j).value;
						document.getElementById('ordeamount_'+j).value=number_format_common(value*qty,2);
					}
				}
				else if(chk_color==1){
					if( gmtscolorid==document.getElementById('gmtscolorid_'+j).value){
						document.getElementById(field_id+j).value=value;
						qty=document.getElementById('gmtsqty_'+j).value;
						document.getElementById('ordeamount_'+j).value=number_format_common(value*qty,2);
					}
				}
				else if(chk_size==1){
					if( gmtssizesid==document.getElementById('gmtssizesid_'+j).value){
						document.getElementById(field_id+j).value=value;
						qty=document.getElementById('gmtsqty_'+j).value;
						document.getElementById('ordeamount_'+j).value=number_format_common(value*qty,2);
					}
				}
				else{
					qty=document.getElementById('gmtsqty_'+i).value;
					document.getElementById('ordeamount_'+i).value=number_format_common(value*qty,2);
				}
				set_sum_value_set( 'total_amount', 'ordeamount_' );
			}
		}
		else if(field_id=='txt_rfi_date_'){
			for(var j=i; j<=rowCount; j++)
			{
				if(chk_job==1){
					if( jobid==document.getElementById('jobid_'+j).value*1)
					{
						document.getElementById(field_id+j).value=value;
					} 
				}
				else if(chk_po==1){
					if( ponoid==document.getElementById('poid_'+j).value*1){
						document.getElementById(field_id+j).value=value;
					} 
				}				
				else{
					document.getElementById(field_id+j).value=value;
				}
			}
		}
		else{
			for(var j=i; j<=rowCount; j++)
			{
				console.log(value);
				document.getElementById(field_id+j).value=value;
			}
		}
	}
	function set_sum_value_set(des_fil_id,field_id)
	{
		var rowCount = $('#color_size_data tr').length;
		var ddd={ dec_type:1, comma:0, currency:1}
		if(des_fil_id=="total_amount") math_operation( des_fil_id, field_id, '+', rowCount );
	}
	function fnc_order_entry_details(operation){
		if(operation==2){
			alert("Delete Restricted");
			release_freezing();
			return;
		}
		var rowCount = $('#color_size_data tr').length;
		var data_breakdown="";
		for(var m=1; m<=rowCount; m++)
		{
			if (form_validation('orderrate_'+m,'Rate')==false)
			{
				release_freezing();
				return;
			}
			else{
				data_breakdown+="&orderrate_"+m+"='" + $('#orderrate_'+m).val()+"'"+"&ordeamount_"+m+"='" + $('#ordeamount_'+m).val()+"'"+"&fileyear_"+m+"='" + $('#fileyear_'+m).val()+"'"+"&fileno_"+m+"='" + $('#fileno_'+m).val()+"'"+"&sclcno_"+m+"='" + $('#sclcno_'+m).val()+"'"+"&poid_"+m+"='" + $('#poid_'+m).val()+"'"+"&colorsizeid_"+m+"='" + $('#colorsizeid_'+m).val()+"'"+"&gmtsqty_"+m+"='" + $('#gmtsqty_'+m).val()+"'"+"&ratioid_"+m+"='" + $('#ratioid_'+m).val()+"'"+"&approved_"+m+"='" + $('#approved_'+m).val()+"'"+"&txt_rfi_date_"+m+"='" + change_date_format($('#txt_rfi_date_'+m).val())+"'"+"&jobid_"+m+"='" + $('#jobid_'+m).val()+"'"+"&cartoon_qty_"+m+"='" + $('#cartoon_qty_'+m).val()+"'"+"&approx_cbm_"+m+"='" + $('#approx_cbm_'+m).val()+"'"+"&gross_weight_"+m+"='" + $('#gross_weight_'+m).val()+"'"+"&net_weight_"+m+"='" + $('#net_weight_'+m).val()+"'"+"&approx_ship_mode_"+m+"='" + $('#approx_ship_mode_'+m).val()+"'";
			}
		}
		var data="action=save_update_delete_dtls&operation="+operation+"&row_table="+rowCount+get_submitted_data_string('hidden_po_id',"../../")+data_breakdown;	
		
		http.open("POST","requires/order_color_size_update_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_on_submit_reponsedtls;
	}
	function fnc_on_submit_reponsedtls()
	{
		if(http.readyState == 4)
		{
			var reponse=http.responseText.split('**');
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_job_no*hidden_po_id',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/order_color_size_update_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;			
		}
	}

	function fnc_order_entry_details2(operation){
		if(operation==2){
			alert("Delete Restricted");
			release_freezing();
			return;
		}
		var rowCount = $('#color_size_data tr').length;
		var data_breakdown2="";
		for(var m=1; m<=rowCount; m++)
		{
			if (form_validation('orderrate_'+m,'Rate')==false)
			{
				release_freezing();
				return;
			}
			else{
				data_breakdown2+="&orderrate_"+m+"='" + $('#orderrate_'+m).val()+"'"+"&ordeamount_"+m+"='" + $('#ordeamount_'+m).val()+"'"+"&fileyear_"+m+"='" + $('#fileyear_'+m).val()+"'"+"&fileno_"+m+"='" + $('#fileno_'+m).val()+"'"+"&poid_"+m+"='" + $('#poid_'+m).val()+"'"+"&colorsizeid_"+m+"='" + $('#colorsizeid_'+m).val()+"'"+"&gmtsqty_"+m+"='" + $('#gmtsqty_'+m).val()+"'"+"&ratioid_"+m+"='" + $('#ratioid_'+m).val()+"'"+"&approved_"+m+"='" + $('#approved_'+m).val()+"'"+"&txt_pub_shipment_date_"+m+"='" + change_date_format($('#txt_pub_shipment_date_'+m).val())+"'"+"&txt_org_shipment_date_"+m+"='" + change_date_format($('#txt_org_shipment_date_'+m).val())+"'"+"&txt_country_ship_date_"+m+"='" + change_date_format($('#txt_country_ship_date_'+m).val())+"'"+"&jobid_"+m+"='" + $('#jobid_'+m).val()+"'";
			}
		}
		var data="action=save_update_delete_dtls2&operation="+operation+"&row_table="+rowCount+get_submitted_data_string('hidden_po_id',"../../")+data_breakdown2;	
		http.open("POST","requires/order_color_size_update_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_on_submit_reponsedtls2;
	}
	function fnc_on_submit_reponsedtls2()
	{
		if(http.readyState == 4)
		{
			var reponse=http.responseText.split('**');
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate2&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_job_no*hidden_po_id',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/order_color_size_update_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;			
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>    		 
    <form name="qcReport_1" id="qcReport_1" autocomplete="off" > 
    <h3 style="width:860px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:860px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <tr>
                    	<th width="150">Company</th>
                    	<th width="150">Location</th>
                        <th width="150">Buyer</th>
                        <th width="150" class="must_entry_caption">Job No</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:200px" value="Reset" onClick="reset_form('qcReport_1', 'report_container4', '','','');" /> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_name", 180, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "Select Company", $selected, "load_drop_down( 'requires/order_color_size_update_controller', this.value, 'load_drop_down_location', 'location'); load_drop_down( 'requires/order_color_size_update_controller', this.value, 'load_drop_down_buyer', 'buyer_td');");  ?></td>
                        <td id="location"><?=create_drop_down( "cbo_location_name", 180, $blank_array,"", 1, "Select", $selected, "" ); ?></td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 180, $blank_array,"", 1, "Select Buyer", $selected, "" ); ?></td>
                        <td><input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_job('requires/order_color_size_update_controller.php?action=job_popup','Job/Order Selection Form');" class="text_boxes" placeholder="Browse Job No" name="txt_job_no" id="txt_job_no" readonly />
                        <input type="hidden" name="po_id" id="hidden_po_id">
                        </td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(1)" />
						<input type="button" id="show_button2" class="formbutton" style="width:90px" value="Ship Date" onClick="fn_report_generated(2)" />
						</td>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        <div id="report_container4" align="center"></div>
    </form> 
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>