<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Trims group entry for cotton club BD
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
echo load_html_head_contents("Trims Group Entry","../../", 1, 1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<?=$permission; ?>';
	function openmypage_job(page_link,title){
		hide_left_menu("Button1");
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		page_link=page_link+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1210px,height=430px,center=1,resize=0,scrolling=0','../')		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_id=this.contentDoc.getElementById("job_id").value;
			var job_no=this.contentDoc.getElementById("job_no").value;
				$('#hidden_job_id').val(job_id);
				$('#txt_job_no').val(job_no);
		}
	}
	function openmypage_style(page_link,title){
		hide_left_menu("Button1");
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var garments_nature=document.getElementById('garments_nature').value;
		page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1210px,height=430px,center=1,resize=0,scrolling=0','../')		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("job_no").value;
			var style_no=this.contentDoc.getElementById("style_no").value;
				$('#txt_style_no').val(style_no);
				$('#txt_job_no').val(job_no);
		}
	}
	function openmypage_internalref(page_link,title){
		hide_left_menu("Button1");
		var cbo_company_name=$('#cbo_company_name').val();
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var garments_nature=document.getElementById('garments_nature').value;
		page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1210px,height=430px,center=1,resize=0,scrolling=0','../')		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("job_no").value;
			var internal_no=this.contentDoc.getElementById("grouping_no").value;
			$('#txt_job_no').val(job_no);
			$('#txt_inter_ref').val(internal_no);
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
	function fnc_process_data()
	{
		if (form_validation('cbo_company_name','Company')==false){
			return;
		}
		else{

			//alert(str_data);cbo_company_name*cbo_buyer_name*txt_job_no*hidden_job_id*txt_style_no*txt_inter_ref*hidden_item_id*txt_item_no*cbo_level

			var cbo_company_name=document.getElementById('cbo_company_name').value;
			var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
			var txt_job_no=document.getElementById('txt_job_no').value;
			var hidden_job_id=document.getElementById('hidden_job_id').value;
			var txt_style_no=document.getElementById('txt_style_no').value;
			var txt_inter_ref=document.getElementById('txt_inter_ref').value;
			var hidden_item_id=document.getElementById('hidden_item_id').value;
			var txt_item_no=document.getElementById('txt_item_no').value;
			var cbo_level=document.getElementById('cbo_level').value;

			var page_link='requires/trims_group_entry_controller.php?action=process_data';
			var title='PO Search For Trim Booking';
			page_link=page_link+'&company_id='+cbo_company_name+'&cbo_level='+cbo_level+'&txt_inter_ref='+txt_inter_ref+'&txt_style_no='+txt_style_no+'&txt_job_no='+txt_job_no;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1240px,height=450px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function(){
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("txt_selected_id");
				var theemail2=this.contentDoc.getElementById("txt_job_id");
				var theemail3=this.contentDoc.getElementById("txt_selected_po");
				var theemail4=this.contentDoc.getElementById("itemGroup");
				if (theemail.value!=""){
					document.getElementById('hidden_item_id').value=theemail.value;
					document.getElementById('txt_item_no').value=theemail3.value;
					document.getElementById('txt_selected_trim_id').value=theemail4.value;
					document.getElementById('txt_job_no').value=theemail2.value;
					//fnc_generate_booking(theemail.value,theemail3.value,theemail4.value,cbo_company_name)
				}
			}
		}
	}
	function fn_report_generated(operation)
	{
		var cbo_company_name=$( "#cbo_company_name" ).val();
		var cbo_buyer_name=$( "#cbo_buyer_name" ).val();
		var job_no=$( "#txt_job_no" ).val();
		var txt_style_no=$( "#txt_style_no" ).val();
		var txt_inter_ref=$( "#txt_inter_ref" ).val();
		var hidden_item_id=$( "#hidden_item_id" ).val();
		var txt_item_no=$( "#txt_item_no" ).val();
		var cbo_level=$( "#cbo_level" ).val();
		if(cbo_company_name ==0 && txt_item_no ==0 || txt_item_no =="" ){

			if(form_validation('cbo_company_name*txt_item_no','Company Name*Item No')==false){
				return;
			}
			
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*hidden_job_id*txt_style_no*txt_inter_ref*txt_item_no*hidden_item_id*txt_item_no*cbo_level',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/trims_group_entry_controller.php",true);
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
		document.getElementById('chk_kimble').value=0;
		document.getElementById('chk_sku').value=0;
		document.getElementById('chk_barcode').value=0;
		document.getElementById('chk_fabrication').value=0;

		if(id==1){
			if(document.getElementById('chk_kimble').value==0){
				document.getElementById('chk_kimble').value=1;	
			}
			else document.getElementById('chk_kimble').value=0;
		}
		else if(id==2){
			if(document.getElementById('chk_sku').value==0){
				document.getElementById('chk_sku').value=1;
			}
			else document.getElementById('chk_sku').value=0;
		}
		else if(id==3){
			if(document.getElementById('chk_barcode').value==0){
				document.getElementById('chk_barcode').value=1;
			}
			else document.getElementById('chk_barcode').value=0;
		}
		else{
			if(document.getElementById('chk_fabrication').value==0){
				document.getElementById('chk_fabrication').value=1;
			}
			else document.getElementById('chk_fabrication').value=0;
		}		
	}
	
	function copy_value(value,field_id,i)
	{
		var reportlevel = $('#hiddreportlevel').val();
		 if(reportlevel==2)
		{
			var kimbleno=document.getElementById('txtkimbleno_'+i).value*1;
			var sku=document.getElementById('txtsku_'+i).value*1;
			var barcodeno=document.getElementById('txtbarcodeno_'+i).value*1;
			var fabrication=document.getElementById('txtfabrication_'+i).value*1;
			var rowCount = $('#color_size_data tr').length;

			
			var chk_kimble=document.getElementById('chk_kimble').value;
			var chk_sku=document.getElementById('chk_sku').value;
			var chk_barcode=document.getElementById('chk_barcode').value;
			var chk_fabrication=document.getElementById('chk_fabrication').value;

		 if(field_id=='txtkimbleno_'){
				for(var j=i; j<=rowCount; j++)
				{
					if(chk_kimble==1){
							document.getElementById(field_id+j).value=value;
					}
				}

			}
			else if(field_id=='txtsku_'){
				for(var j=i; j<=rowCount; j++)
				{
					if(chk_sku==1){
							document.getElementById(field_id+j).value=value;
					}
				}

			}
			else if(field_id=='txtbarcodeno_'){
				for(var j=i; j<=rowCount; j++)
				{
					if(chk_barcode==1){
							document.getElementById(field_id+j).value=value;
					}
				}

			}else if(field_id=='txtfabrication_'){
				for(var j=i; j<=rowCount; j++)
				{
					if(chk_fabrication==1){
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
	}
	function set_sum_value_set(des_fil_id,field_id)
	{
		var rowCount = $('#color_size_data tr').length;
		var ddd={ dec_type:1, comma:0, currency:1}
		if(des_fil_id=="total_amount") math_operation( des_fil_id, field_id, '+', rowCount );
	}
	
	function fnc_order_entry_details(operation){
		
		var rowCount = $('#color_size_data tr').length;
		var data_all="";
		var reportlevel = $('#hiddreportlevel').val();
		if(reportlevel==2)
		{
			for(var m=1; m<=rowCount; m++)
			{
					//jobid_*jobno_*styleref_*grouping_*gmstcolorid_*gmtssizeid_*itemsizeid_*itemnumberid_*txtitemsizeid_*image_button_front_*txtkimbleno_*txtsku_*txtbarcodeno_*txtfabrication_*colorsizeid_

					data_all+="&jobid_"+m+"='" + $('#jobid_'+m).val()+"&jobno_"+m+"='" + $('#jobno_'+m).val()+"&poid_"+m+"='" + $('#poid_'+m).val()+"'"+"&styleref_"+m+"='"+$('#styleref_'+m).val() +"&colorsizeid_"+m+"='" + $('#colorsizeid_'+m).val()+"'"+"&grouping_"+m+"='" + $('#grouping_'+m).val()+"'"+"&gmstcolorid_"+m+"='" + $('#gmstcolorid_'+m).val()+"'"+"&gmtssizeid_"+m+"='" + $('#gmtssizeid_'+m).val()+"'"+"&itemsizeid_"+m+"='" + $('#itemsizeid_'+m).val()+"'"+"&itemnumberid_"+m+"='" + $('#itemnumberid_'+m).val()+"&txtitemsizeid_"+m+"='" + $('#txtitemsizeid_'+m).val()+"'"+"&image_button_front_"+m+"='" + $('#image_button_front_'+m).val()+"'"+"&txtkimbleno_"+m+"='" + $('#txtkimbleno_'+m).val()+"'"+"&txtsku_"+m+"='" + $('#txtsku_'+m).val()+"'"+"&txtbarcodeno_"+m+"='" + $('#txtbarcodeno_'+m).val()+"'"+"&txtfabrication_"+m+"='" + $('#txtfabrication_'+m).val()+"'";
				
			}
		}
		
		var data="action=save_update_delete&operation="+operation+"&row_table="+rowCount+"&reporttype="+reportlevel+get_submitted_data_string('hidden_job_id',"../../")+data_all;			
		http.open("POST","requires/trims_group_entry_controller.php",true);
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
			var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*hidden_job_id*txt_style_no*txt_inter_ref*txt_item_no*hidden_item_id*txt_item_no*cbo_level',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/trims_group_entry_controller.php",true);
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
	 
	 function generate_report_file(data,action,page)
	{
		window.open("requires/trims_group_entry_controller.php?data=" + data+'&action='+action, true );
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
                    	<th width="100" class="must_entry_caption">Company</th>
                        <th width="100">Buyer</th>
                        <th width="100">Job No</th>
                        <th width="100">Style Ref.</th>
                        <th width="100">IR/IB No.</th>
                        <th width="120" class="must_entry_caption">Item Search Pop Up</th>
						<th width="100">Level</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_name", 120, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-Select Company-", $selected, " load_drop_down( 'requires/trims_group_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td');");  ?>
                        </td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-Select Buyer-", $selected, "" ); ?>
                        </td>
                        <td><input style="width:90px;" type="text" title="Double Click to Search" onDblClick="openmypage_job('requires/trims_group_entry_controller.php?action=job_popup','Job Selection Form');" class="text_boxes" placeholder="Browse Job No" name="txt_job_no" id="txt_job_no" readonly />
                        <input type="hidden" name="job_id" id="hidden_job_id">
                        </td>
                        <td><input style="width:90px;" type="text" title="Double Click to Search" onDblClick="openmypage_style('requires/trims_group_entry_controller.php?action=style_popup','Style Selection Form');" class="text_boxes" placeholder="Browse Style No" name="txt_style_no" id="txt_style_no" readonly />
                        </td>
                        <td><input style="width:90px;" type="text" title="Double Click to Search" onDblClick="openmypage_internalref('requires/trims_group_entry_controller.php?action=intrnal_popup','Job Selection Form');" class="text_boxes" placeholder="Browse IR/IB No" name="txt_inter_ref" id="txt_inter_ref" readonly />
                        </td>
						<td><input style="width:90px;" type="text" title="Double Click to Search" onDblClick="fnc_process_data();" class="text_boxes" placeholder="Browse Item No" name="txt_item_no" id="txt_item_no" readonly />
                        <input type="hidden" name="hidden_item_id" id="hidden_item_id">
                        </td>
                        <?$level_arr= array(0=>"Color & Size Level",1=>"Color Level") ?>
                        <td><?=create_drop_down( "cbo_level", 120, $level_arr,"", 0, "-Select Level-", $selected, "" ); ?>
                        </td>
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