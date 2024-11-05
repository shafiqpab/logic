<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Woven Order color size possibe ship date update
Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	17-10-2022
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
	var permission = '<?=$permission; ?>';
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
			$('#hidden_job_id').val(job_id);
			$('#txt_job_no').val(job_no);
			/*freeze_window(3);
			if(job_id!=''){
				var report_title=$( "div.form_caption" ).html();
				var operation=1;
				var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_job_no*hidden_job_id',"../../")+'&report_title='+report_title;
				http.open("POST","requires/order_possible_shipdate_country_update_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse;
			}*/
		}
	}
	
	function openmypage_order(page_link,title){
		hide_left_menu("Button1");
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var hidden_job_id=$('#hidden_job_id').val();
		var garments_nature=document.getElementById('garments_nature').value;
		page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&job_id='+hidden_job_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1210px,height=430px,center=1,resize=0,scrolling=0','../')		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_id=this.contentDoc.getElementById("job_id").value;
			var job_no=this.contentDoc.getElementById("job_no").value;
			$('#hidden_order_id').val(job_id);
			$('#txt_order_no').val(job_no);
			
		}
	}
	
	function fn_report_generated(operation)
	{
		var job_no=$( "#txt_job_no" ).val();
		var date_from=$( "#txt_date_from" ).val();
		var date_to=$( "#txt_date_to" ).val();
		if(job_no =="" && (date_from =="" || date_to =="")){

			if(form_validation('txt_job_no','Job Number')==false){
				return;
			}
			
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_job_no*hidden_job_id*txt_order_no*hidden_order_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/order_possible_shipdate_country_update_controller.php",true);
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
		var reportlevel = $('#hiddreportlevel').val();
		document.getElementById('chk_job').value=0;
		document.getElementById('chk_po').value=0;
		document.getElementById('chk_country').value=0;
		if(reportlevel==1) {
			document.getElementById('chk_color').value=0;
			document.getElementById('chk_size').value=0;
		}

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
		var reportlevel = $('#hiddreportlevel').val();
		 if(reportlevel==2)
		{
			var jobid=document.getElementById('jobid_'+i).value*1;
			var ponoid=document.getElementById('poid_'+i).value*1;
			var countryid=document.getElementById('countryid_'+i).value*1;
			var rowCount = $('#color_size_data tr').length;
	
			var chk_job=document.getElementById('chk_job').value;
			var chk_po=document.getElementById('chk_po').value;
			var chk_country=document.getElementById('chk_country').value;
			var qty=0;
	
			if( field_id=='txtpossibleshipdate_'){
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
					else if(chk_country==1){
						if( countryid==document.getElementById('countryid_'+j).value){
							document.getElementById(field_id+j).value=value;
						}
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
		var reportlevel = $('#hiddreportlevel').val();
		if(reportlevel==2)
		{
			for(var m=1; m<=rowCount; m++)
			{
				if (form_validation('txtpubshipdate_'+m+'*txtpossibleshipdate_'+m+'*txtcountryshipdate_'+m+'*orderrate_'+m,'Publish Shipdate*PO Ship Date*Country Ship Date*Rate')==false)
				{
					release_freezing();
					return;
				}
				else{
					data_breakdown+="&txtpubshipdate_"+m+"='" + $('#txtpubshipdate_'+m).val()+"'"+"&txtpossibleshipdate_"+m+"='" + $('#txtpossibleshipdate_'+m).val()+"'"+"&txtcountryshipdate_"+m+"='" + $('#txtcountryshipdate_'+m).val()+"'"+"&poid_"+m+"='" + $('#poid_'+m).val()+"'"+"&colorsizeid_"+m+"='" + $('#colorsizeid_'+m).val()+"'"+"&approved_"+m+"='" + $('#approved_'+m).val()+"'";
				}
			}
		}
		
		var data="action=save_update_delete_dtls&operation="+operation+"&row_table="+rowCount+"&reporttype="+reportlevel+get_submitted_data_string('hidden_job_id',"../../")+data_breakdown;			
		http.open("POST","requires/order_possible_shipdate_country_update_controller.php",true);
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
			var operation = $('#hiddreportlevel').val();
			var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_location_name*cbo_buyer_name*txt_job_no*hidden_job_id',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/order_possible_shipdate_country_update_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;			
		}
	}
	
	function set_tna_task(i)
	{
		var txt_po_received_date=document.getElementById('txtporecdate_'+i).value;
		var txt_pub_shipment_date=document.getElementById('txtpubshipdate_'+i).value;
		//alert(txt_po_received_date+'=='+txt_pub_shipment_date)
		var datediff = date_compare(txt_po_received_date,txt_pub_shipment_date);
		//alert(datediff);
		if(datediff==false)
		{
			//alert("PO Received Date Is Greater Than Shipment Date.");
			//$('#txtpubshipdate_'+i).val("");
			//return;
		}
		
		$('#txtcountryshipdate_'+i).val( txt_pub_shipment_date );
		//alert(txt_factory_rec_date);
		//if($('#txtpossibleshipdate_'+i).val()=="") $('#txtpossibleshipdate_'+i).val(txt_pub_shipment_date);
		
		var shipment_date=$('#txtpossibleshipdate_'+i).val();
		
		if(txt_pub_shipment_date == '')
		{
			//alert("Publish Shipment Date Can Not Be Null");
			//$('#txtpossibleshipdate_'+i).val("");
			//return;
		}
		var datediff2 = date_compare(txt_pub_shipment_date,shipment_date);
		if(datediff2 == false){
			//alert("Shipment date can not be less then Publish shipment date");
			//$('#txtpossibleshipdate_'+i).val("");
		}
	}
	 function fn_report_print(type)
	 {
	var report_title=$( "div.form_caption" ).html();
	 generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_job_no').val()+'*'+report_title+'*'+$('#hidden_job_id').val()+'*'+$('#hidden_order_id').val()+'*'+$('#txt_date_from').val()+'*'+$('#txt_date_to').val(),'po_print_action','requires/order_possible_shipdate_country_update_controller');
	 }
	 
	 function generate_report_file(data,action,page)
	{
		window.open("requires/order_possible_shipdate_country_update_controller.php?data=" + data+'&action='+action, true );
	}

</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>    		 
    <form name="qcReport_1" id="qcReport_1" autocomplete="off" > 
    <h3 style="width:900px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:900px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <tr>
                    	<th width="150">Company</th>
                    	<th width="150">Location</th>
                        <th width="150">Buyer</th>
                        <th width="100" class="must_entry_caption">Job No</th>
						<th width="100" style="display:none">Order No</th>
                        <th>Country ShipDate</th>
                        <th colspan="2"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('qcReport_1', 'report_container3*report_container4', '','','');" /> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_name", 150, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'requires/order_possible_shipdate_country_update_controller', this.value, 'load_drop_down_location', 'location'); load_drop_down( 'requires/order_possible_shipdate_country_update_controller', this.value, 'load_drop_down_buyer', 'buyer_td');");  ?></td>
                        <td id="location"><?=create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-Select-", $selected, "" ); ?></td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-Select Buyer-", $selected, "" ); ?></td>
                        <td><input style="width:90px;" type="text" title="Double Click to Search" onDblClick="openmypage_job('requires/order_possible_shipdate_country_update_controller.php?action=job_popup','Job Selection Form');" class="text_boxes" placeholder="Browse Job No" name="txt_job_no" id="txt_job_no" readonly />
                        <input type="hidden" name="job_id" id="hidden_job_id">
                        </td>
						<td style="display:none"><input style="width:90px;" type="text" title="Double Click to Search" onDblClick="openmypage_order('requires/order_possible_shipdate_country_update_controller.php?action=order_popup','Order Selection Form');" class="text_boxes" placeholder="Browse Order No" name="txt_order_no" id="txt_order_no" readonly />
                        <input type="hidden" name="order_id" id="hidden_order_id">
                        </td>
                       <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" >&nbsp; To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date" ></td>

                        <td>
                        <td>
                        	
                        	<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(2);" />
                        </td>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        <div id="report_container3" align="center"></div>
        <div id="report_container4" align="center"></div>
    </form> 
</div>
   <div style="display:none" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>