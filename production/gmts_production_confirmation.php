<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Garments Production Confirmation
				
Functionality	:	
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	07-09-2022
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Ready To Sewing","../", 1, 1, $unicode,0,0); 

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $_SESSION['page_permission']; ?>';
	
	function openmypage_sys_no()
	{
		var title = 'Job Search';	
		var page_link ='requires/gmts_production_confirmation_controller.php?action=sys_no_popup';
		var popup_width="790px";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=420px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var job_data=this.contentDoc.getElementById("hidden_data").value.split('**');	
			var job_id=job_data[0];
			var job_no=job_data[1];
			var buyer_name=job_data[2];
			var style=job_data[3];
			var season=job_data[4];
			var marchant=job_data[5];
			var team_leader=job_data[6];
			var job_qty=job_data[7];
			var company_id=job_data[8];
			var company_name=job_data[9];
			var sys_no=job_data[10];
			var sys_id=job_data[11];

			$("#txt_job_no").val(job_no);
			$("#txt_job_id").val(job_id);
			$("#buyer").text(buyer_name);
			$("#season").text(season);
			$("#style").text(style);
			$("#merchant").text(marchant);
			$("#team_leader").text(team_leader);
			$("#job_qty").text(job_qty);
			$("#company_id").val(company_id);
			$("#company").val(company_name);
			$("#txt_sys_no").val(sys_no);
			$("#update_id").val(sys_id);

			if(job_id!="")
			{
				freeze_window(5);
				show_list_view( sys_id, 'order_details_list_update', 'po_dtls_container', 'requires/gmts_production_confirmation_controller', '' );
				release_freezing();
			}
		}
		
	}
	function openmypage(po_id,item_name,job_no,book_num,trim_dtla_id,action)
	{ //alert(book_num);
		var cbo_company_name=$("#company_id").val();
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/gmts_production_confirmation_controller.php?po_id='+po_id+'&item_name='+item_name+'&job_no='+job_no+'&book_num='+book_num+'&trim_dtla_id='+trim_dtla_id+'&action='+action+'&cbo_company_name='+cbo_company_name, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	function openmypage_balance(po_id,item_name,action)
	{
		var popup_width='900px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/gmts_production_confirmation_controller.php?po_id='+po_id+'&item_name='+item_name+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	function openmypage_job()
	{
		var title = 'Job Search';	
		var page_link ='requires/gmts_production_confirmation_controller.php?action=job_popup';
		var popup_width="790px";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=420px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var job_data=this.contentDoc.getElementById("hidden_data").value.split('**');	
			var job_id=job_data[0];
			var job_no=job_data[1];
			var buyer_name=job_data[2];
			var style=job_data[3];
			var season=job_data[4];
			var marchant=job_data[5];
			var team_leader=job_data[6];
			var job_qty=job_data[7];
			var company_id=job_data[8];
			var company_name=job_data[9];
			var confirm_status=job_data[10];

			$("#txt_job_no").val(job_no);
			$("#txt_job_id").val(job_id);
			$("#buyer").text(buyer_name);
			$("#season").text(season);
			$("#style").text(style);
			$("#merchant").text(marchant);
			$("#team_leader").text(team_leader);
			$("#job_qty").text(job_qty);
			$("#company_id").val(company_id);
			$("#company").text(company_name);
			$("#confirm_status").val(confirm_status);

			if(job_id!="")
			{
				freeze_window(5);
				show_list_view( job_id+'__'+confirm_status, 'order_details_list', 'po_dtls_container', 'requires/gmts_production_confirmation_controller', '' );
				release_freezing();
			}

			if(confirm_status==1)// when confirm, can not hold
			{
				document.getElementById('confirm').disabled=true; 
				document.getElementById('confirm').classList.add("formbutton_disabled");
			}
			if(confirm_status==2)
			{
				document.getElementById('hold').disabled=true; 
				document.getElementById('hold').classList.add("formbutton_disabled");
			}
		}
		
	}
	
	
	function fnc_ready_to_confirm( operation, type )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
	 	if(form_validation('txt_job_no','Job No')==false)
		{
			return; 
		}

		var dataString=""; 
		var j=0;
		$("input[name=chk_po]").each(function(index, element) {
			if( $(this).prop('checked')==true)
			{			
				j++;
				var idd=$(this).attr('id').split("_");
				var po_id = $('#po_id_'+idd[2] ).val();
				var remarks = $('#remarks_'+idd[2] ).val();
				dataString+='&po_id'+j+'='+po_id+'&remarks'+j+'='+remarks;
			}
		});
			
		// alert(dataString); return;
		if(j<1)
		{
			alert('Please select PO Number');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_job_no*txt_job_id*company_id*confirm_status' ,"../")+dataString+'&type='+type;
		//alert(data);return;
		freeze_window(operation);
		
		http.open("POST","requires/gmts_production_confirmation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_ready_to_confirm_response;
	}

	function fnc_ready_to_confirm_response()
	{
		if(http.readyState == 4) 
		{
			// alert(http.responseText);release_freezing();return;
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			if((response[0]==0 || response[0]==1))
			{
				if(response[1]!="")
				{
					show_list_view( response[1]+'__'+response[2], 'order_details_list_update', 'po_dtls_container', 'requires/gmts_production_confirmation_controller', '' );
				}

				// set_button_status(1, permission, 'fnc_ready_to_confirm',1);
			}			

			if(response[2]==1)// when confirm, can not hold
			{
				document.getElementById('confirm').disabled=true; 
				document.getElementById('confirm').classList.add("formbutton_disabled");
			}
			if(response[2]==2)
			{
				document.getElementById('hold').disabled=true; 
				document.getElementById('hold').classList.add("formbutton_disabled");
			}
			release_freezing();
		}
	}

	function fnc_material_status_report()
	{
				
	 	if(form_validation('txt_job_no','Job No')==false)
		{
			return; 
		}
		
		var data="action=material_status_report&operation="+get_submitted_data_string('txt_job_no*txt_job_id*company_id' ,"../");
		//alert(data);return;
		freeze_window(operation);
		
		http.open("POST","requires/gmts_production_confirmation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_material_status_report_response;
	}

	function fnc_material_status_report_response()
	{
		if(http.readyState == 4) 
		{
			// alert(http.responseText);release_freezing();return;
			
			// $('#po_dtls_container').html('');
			var response=trim(http.responseText);	
			$('#report_container').html(response);
			release_freezing();
		}
	}

 	function resetReport()
	{
		$('#report_container').html('');
	}

function fnc_approval_status_report()
{
			
	 if(form_validation('txt_job_no','Job No')==false)
	{
		return; 
	}
	
	var data="action=approval_status_report&operation="+get_submitted_data_string('txt_job_no*txt_job_id*company_id' ,"../");
	//alert(data);return;
	freeze_window(operation);
	
	http.open("POST","requires/gmts_production_confirmation_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange =fnc_approval_status_report_response;
}

function fnc_approval_status_report_response()
{
	if(http.readyState == 4) 
	{
		// alert(http.responseText);release_freezing();return;
			
		// $('#po_dtls_container').html('');
		var response=trim(http.responseText);	
		$('#report_container').html(response);
		release_freezing();
	}
}
	
	function fn_trims_issue_req()
	{
		if (form_validation('update_id','Save Data First')==false)
		{
			alert("save data first");return;
		}
		else
		{
			var update_id = $('#update_id').val();
			var txt_order_id = $('#txt_order_id').val(); 	
			var tot_seq_qnty = $('#tot_seq_qnty').text();
			//alert(tot_seq_qnty);
			var page_link ='requires/gmts_production_confirmation_controller.php?update_id='+update_id+'&txt_order_id='+txt_order_id+'&tot_seq_qnty='+tot_seq_qnty+'&action=trims_popup'+'&permission='+permission;
			var title="Trims Info";
			var popup_width="980px";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=450px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				
			}
		}
	}
	
	function fnc_ready_to_confirm_print()
	{
		if ($("#txt_sewing_no").val() == "") 
		{
			alert("Please Save First.");
			return;
		}
		else
		{
			var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_sewing_no').val() + '*' + report_title + '*' + $('#update_id').val(), 'ready_to_sewing_print', 'requires/gmts_production_confirmation_controller');
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/gmts_production_confirmation_controller.php?data=" + data+'&action='+action, true );
	}

	function check_all_report()
	{
		$("input[name=chk_po]").each(function(index, element) { 
				
			if( $('#check_all').prop('checked')==true) 
			{
				$(this).attr('checked','true');
			}
			else
			{
				$(this).removeAttr('checked');
			}
		});
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
	<div align="center" style="width:100%;">
        <div width:970px;">
    		<form name="readytosewing_1" id="readytosewing_1"> 
            <? echo load_freeze_divs ("../",$permission); ?>
            <fieldset style="width:960px;">
				<legend>Master Part</legend>
                <table cellpadding="0" cellspacing="2" width="950" id="tbl_mst">
                    <tr>
                        <td align="right" colspan="4" width="50%"><b>Job No:</b></td>
                        <td colspan="4" align="left" width="50%">                        	
							<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:120px;" placeholder="Browse" onDblClick="openmypage_job()" readonly/>
                            <input type="hidden" name="txt_job_id" id="txt_job_id"/>
                            <input type="hidden" name="company_id" id="company_id"/>
                            <input type="hidden" name="confirm_status" id="confirm_status"/>
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="10"><hr class="style-two"></td>
                    </tr>
                    <tr>
                       
						<td width="10%"><b>Company Name:</b></td>
						<td width="15%"><div id="company"></div></td>
						<td width="10%"><b>Buyer Name:</b></td>
						<td width="15%"><div id="buyer"></div></td>
						<td width="10%"><b>Season:</b></td>
						<td width="15%"><div id="season"></div></td>
						<td width="10%"><b>Style:</b></td>
						<td width="15%"><div id="style"></div></td>
                    </tr>
                    <tr>
                        <td width="10%" align="right"><b>Job Qty:</b></td>
                        <td width="15%"><div id="job_qty"></div></td>
						<td width="10%"><b>Team Leader:</b></td>
						<td width="15%"><div id="team_leader"></div></td>
						<td width="10%"><b>Dealing Merchant:</b></td>
						<td width="15%"><div id="merchant"></div></td>
						<td colspan="2" width="25%">
							<input type="button" class="formbutton" value="Material Status" onclick="fnc_material_status_report();">
							<input type="button" class="formbutton" value="Approval Status" onclick="fnc_approval_status_report()">
							<input type="button" class="formbutton" value="Close Report" onclick="resetReport()">
						</td>
                    </tr>
                </table>
			</fieldset>
		</form>
    </div>
        <div style="width:850px; margin-left:5px; margin-top:25px;" align="center">
            <div style="width:100%" id="po_dtls_container"></div>
        </div>
        <div id="report_container"></div>
			
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
