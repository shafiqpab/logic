<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create App. BOM Sync After New Data Add in PO
Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	06-02-2023
Updated by 		:	
Update date		:	
QC Performed BY	:
QC Date			:
Comments		:
*/
session_start();

if( $_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../../includes/common.php');
if($approve_bom_sync!=1) { unset($_SESSION['logic_erp']); unset($_SESSION['page_permission']); }
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

echo load_html_head_contents("App. BOM Sync After New Data Add in PO","../../", 1, 1, $unicode,1,'');

$bom_cost_head_arr = array(1 => "Fabric Cost", 2 => "Trim Cost", 3 => "Embellishment Cost", 4 => "Wash Cost");
$newadd_type_arr = array(1 => "PO", 2 => "Color", 3 => "Size");

?>
<script>
	var approve_bom_sync = '<? echo $approve_bom_sync; ?>';
	if( $('#index_page', window.parent.document).val()!=1 || approve_bom_sync!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	function openmypage_job(page_link,title){
		if(form_validation('cbo_company_name*cbo_cost_head*cbo_newadd_type','Company*Cost Head Type*New Added Type')==false){
			return;
		}
		else
		{
			hide_left_menu("Button1");
			var cbo_company_name=$('#cbo_company_name').val();
			var cbo_buyer_name=$('#cbo_buyer_name').val();
			var cbo_cost_head=$('#cbo_cost_head').val();
			var cbo_newadd_type=$('#cbo_newadd_type').val();
			var garments_nature=document.getElementById('garments_nature').value;
			page_link=page_link+'&garments_nature='+garments_nature+'&cbo_company_name='+cbo_company_name+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_cost_head='+cbo_cost_head+'&cbo_newadd_type='+cbo_newadd_type;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=430px,center=1,resize=0,scrolling=0','../')		
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var job_id=this.contentDoc.getElementById("job_id").value;
				var job_no=this.contentDoc.getElementById("job_no").value;
				$('#hidden_job_id').val(job_id);
				$('#txt_job_no').val(job_no);
			}
		}
	}
	
	function fn_report_generated(operation)
	{
		if(form_validation('cbo_company_name*txt_job_no*cbo_cost_head*cbo_newadd_type','Company*Job No*Cost Head Type*New Added Type')==false){
			return;
		}
		else
		{	
			$("#hidden_costhead_id").val( $("#cbo_cost_head").val() );
			$("#hidden_newadd_type_id").val( $("#cbo_newadd_type").val() );
			
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_cost_head*cbo_newadd_type*hidden_costhead_id*hidden_newadd_type_id*txt_job_no*hidden_job_id',"../../")+'&report_title='+report_title;
			freeze_window(3);
			http.open("POST","requires/approve_bom_sync_controller.php",true);
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
				data_breakdown+="&orderrate_"+m+"='" + $('#orderrate_'+m).val()+"'"+"&ordeamount_"+m+"='" + $('#ordeamount_'+m).val()+"'"+"&fileyear_"+m+"='" + $('#fileyear_'+m).val()+"'"+"&fileno_"+m+"='" + $('#fileno_'+m).val()+"'"+"&sclcno_"+m+"='" + $('#sclcno_'+m).val()+"'"+"&poid_"+m+"='" + $('#poid_'+m).val()+"'"+"&colorsizeid_"+m+"='" + $('#colorsizeid_'+m).val()+"'"+"&gmtsqty_"+m+"='" + $('#gmtsqty_'+m).val()+"'"+"&ratioid_"+m+"='" + $('#ratioid_'+m).val()+"'"+"&approved_"+m+"='" + $('#approved_'+m).val()+"'"+"&txt_rfi_date_"+m+"='" + change_date_format($('#txt_rfi_date_'+m).val())+"'"+"&jobid_"+m+"='" + $('#jobid_'+m).val()+"'";
			}
		}
		var data="action=save_update_delete_dtls&operation="+operation+"&row_table="+rowCount+get_submitted_data_string('hidden_po_id',"../../")+data_breakdown;			
		http.open("POST","requires/approve_bom_sync_controller.php",true);
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
			http.open("POST","requires/approve_bom_sync_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;			
		}
	}
	
	function fn_bom_sysnc(str)
	{
		var operation=1;
		freeze_window(operation);
		
		var costhead_id=$("#hidden_costhead_id").val();
		var newadd_type_id=$("#hidden_newadd_type_id").val();
		
		var exval=str.split("|-|");
		var inc=exval[0];
		var fabricid=exval[1];
		var poid=exval[2];
		if(costhead_id==1)//fabric
		{
			if(newadd_type_id==1)//PO
			{
				var bodypartid=exval[3];
				var consdtlsdata=exval[4];
				
				var data="action=save_update_delete_cons_sync&operation="+operation+get_submitted_data_string('hidden_job_id*hidden_costhead_id*hidden_newadd_type_id',"../../")+'&inc='+inc+'&fabricid='+fabricid+'&poid='+poid+'&bodypartid='+bodypartid+'&consdtlsdata='+consdtlsdata;
			}
			else if(newadd_type_id==2)//Color
			{
				var colorid=exval[3];
				var consdtlsdata=exval[4];
				
				var data="action=save_update_delete_cons_sync&operation="+operation+get_submitted_data_string('hidden_job_id*hidden_costhead_id*hidden_newadd_type_id',"../../")+'&inc='+inc+'&fabricid='+fabricid+'&poid='+poid+'&colorid='+colorid+'&consdtlsdata='+consdtlsdata;
			}
			else if(newadd_type_id==3)//Size
			{
				var colorid=exval[3];
				var sizeid=exval[4];
				var consdtlsdata=exval[5];
				
				var data="action=save_update_delete_cons_sync&operation="+operation+get_submitted_data_string('hidden_job_id*hidden_costhead_id*hidden_newadd_type_id',"../../")+'&inc='+inc+'&fabricid='+fabricid+'&poid='+poid+'&colorid='+colorid+'&sizeid='+sizeid+'&consdtlsdata='+consdtlsdata;
			}
		}
		else if(costhead_id==2)//Trims
		{
			var bodypartid=exval[3];
			var consdtlsdata=exval[4];
			
			var data="action=save_update_delete_cons_sync&operation="+operation+get_submitted_data_string('hidden_job_id*hidden_costhead_id*hidden_newadd_type_id',"../../")+'&inc='+inc+'&trimid='+fabricid+'&poid='+poid+'&bodypartid='+bodypartid+'&consdtlsdata='+consdtlsdata;
		}
		else if(costhead_id==3)//Emb
		{
			if(newadd_type_id==1)//PO
			{
				var bodypartid=exval[3];
				var consdtlsdata=exval[4];
				
				var data="action=save_update_delete_cons_sync&operation="+operation+get_submitted_data_string('hidden_job_id*hidden_costhead_id*hidden_newadd_type_id',"../../")+'&inc='+inc+'&embid='+fabricid+'&poid='+poid+'&bodypartid='+bodypartid+'&consdtlsdata='+consdtlsdata;
			}
			else if(newadd_type_id==2)//Color
			{
				var colorid=exval[3];
				var consdtlsdata=exval[4];
				
				var data="action=save_update_delete_cons_sync&operation="+operation+get_submitted_data_string('hidden_job_id*hidden_costhead_id*hidden_newadd_type_id',"../../")+'&inc='+inc+'&embid='+fabricid+'&poid='+poid+'&colorid='+colorid+'&consdtlsdata='+consdtlsdata;
			}
			else if(newadd_type_id==3)//Size
			{
				var colorid=exval[3];
				var sizeid=exval[4];
				var consdtlsdata=exval[5];
				
				var data="action=save_update_delete_cons_sync&operation="+operation+get_submitted_data_string('hidden_job_id*hidden_costhead_id*hidden_newadd_type_id',"../../")+'&inc='+inc+'&embid='+fabricid+'&poid='+poid+'&colorid='+colorid+'&sizeid='+sizeid+'&consdtlsdata='+consdtlsdata;
			}
		}
		else if(costhead_id==4)//Wash
		{
			if(newadd_type_id==1)//PO
			{
				var bodypartid=exval[3];
				var consdtlsdata=exval[4];
				
				var data="action=save_update_delete_cons_sync&operation="+operation+get_submitted_data_string('hidden_job_id*hidden_costhead_id*hidden_newadd_type_id',"../../")+'&inc='+inc+'&washid='+fabricid+'&poid='+poid+'&bodypartid='+bodypartid+'&consdtlsdata='+consdtlsdata;
			}
			else if(newadd_type_id==2)//Color
			{
				var colorid=exval[3];
				var consdtlsdata=exval[4];
				
				var data="action=save_update_delete_cons_sync&operation="+operation+get_submitted_data_string('hidden_job_id*hidden_costhead_id*hidden_newadd_type_id',"../../")+'&inc='+inc+'&washid='+fabricid+'&poid='+poid+'&colorid='+colorid+'&consdtlsdata='+consdtlsdata;
			}
			else if(newadd_type_id==3)//Size
			{
				var colorid=exval[3];
				var sizeid=exval[4];
				var consdtlsdata=exval[5];
				
				var data="action=save_update_delete_cons_sync&operation="+operation+get_submitted_data_string('hidden_job_id*hidden_costhead_id*hidden_newadd_type_id',"../../")+'&inc='+inc+'&washid='+fabricid+'&poid='+poid+'&colorid='+colorid+'&sizeid='+sizeid+'&consdtlsdata='+consdtlsdata;
			}
		}
	    
		http.open("POST","requires/approve_bom_sync_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_bom_sysnc_reponse;
	}
	
	function fn_bom_sysnc_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			
			if(reponse[0]==1) alert("Data is Synchronize Successfully");
			else alert("Data is Not Synchronize Successfully");
			fn_report_generated(1);
			release_freezing();
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>    		 
    <form name="approvalbomsync_1" id="approvalbomsync_1" autocomplete="off" > 
    <h3 style="width:670px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:660px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <tr>
                    	<th width="130" class="must_entry_caption">Company</th>
                        <th width="130">Buyer</th>
                        <th width="100" class="must_entry_caption">Cost Head Type</th>
                        <th width="100" class="must_entry_caption">New Added Type</th>
                        <th width="100" class="must_entry_caption">Job No</th>
                        
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:90px" value="Reset" onClick="reset_form('approvalbomsync_1', 'report_container4', '','','');" /> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_name", 130, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-Company-", $selected, "load_drop_down( 'requires/approve_bom_sync_controller', this.value, 'load_drop_down_buyer', 'buyer_td');");  ?></td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-Buyer-", $selected, "" ); ?></td>
                        <td><?=create_drop_down( "cbo_cost_head", 100, $bom_cost_head_arr,"", 1, "-Cost Head-", $selected, "" ); ?></td>
                        <td><?=create_drop_down( "cbo_newadd_type", 100, $newadd_type_arr,"", 1, "-New Added-", $selected, "" ); ?></td>
                        <td><input style="width:90px;" type="text" title="Browse" onDblClick="openmypage_job('requires/approve_bom_sync_controller.php?action=job_popup','Job Selection Form');" class="text_boxes" placeholder="Browse Job No" name="txt_job_no" id="txt_job_no" readonly />
                        <input type="hidden" name="hidden_job_id" id="hidden_job_id">
                        <input type="hidden" name="hidden_costhead_id" id="hidden_costhead_id">
                        <input type="hidden" name="hidden_newadd_type_id" id="hidden_newadd_type_id">
                        </td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(1);" /></td>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        &nbsp;
        <div id="report_container4" align="center"></div>
    </form> 
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>