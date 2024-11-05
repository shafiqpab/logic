<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Ready To Sewing
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	15-03-2015
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
	
	function openmypage_sewing()
	{ 
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/ready_to_sewing_entry_controller.php?action=sewing_popup&company_id='+cbo_company_id,'Sewing Popup', 'width=780px,height=420px,center=1,resize=1,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var reqn_id=this.contentDoc.getElementById("hidden_reqn_id").value.split("_");	 //Requisition Id and Number
			//alert(reqn_id[0]);
			if(reqn_id[0]>0)
			{
				freeze_window(5);
				get_php_form_data(reqn_id[0], "populate_data_from_requisition", "requires/ready_to_sewing_entry_controller" );
				show_list_view( reqn_id[1]+'**'+reqn_id[0], 'order_details_list', 'sewing_dtls_container', 'requires/ready_to_sewing_entry_controller', '' ) ;
				show_list_view( reqn_id[1], 'trims_status_list', 'trims_status_container', 'requires/ready_to_sewing_entry_controller', '' ) ;
				show_list_view( reqn_id[1], 'approval_status_list', 'approval_container', 'requires/ready_to_sewing_entry_controller', '' ) ;
				release_freezing();
			}
		}
	}
	
	function openmypage_po()
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var cbo_company_id = $('#cbo_company_id').val();
			var cbo_buyer_name = $('#cbo_buyer_name').val(); 	
			var cbo_year = $('#cbo_year').val(); 
			var sewing_production_variable = $('#sewing_production_variable').val(); 
			var title = 'Fabric Selection Form';	
			var page_link ='requires/ready_to_sewing_entry_controller.php?company_id='+cbo_company_id+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_year='+cbo_year+'&sewing_production_variable='+sewing_production_variable+'&action=po_popup';
			var popup_width="790px";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=420px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var order_id_ref=this.contentDoc.getElementById("hidden_data").value.split('**');	
				var order_id=order_id_ref[0];
				var order_num=order_id_ref[1];
				var buyer_id=order_id_ref[2];
				var job_no=order_id_ref[3];
				$("#txt_order_no").val(order_num);
				$("#txt_order_id").val(order_id);
				$("#cbo_buyer_name").val(buyer_id);
				//alert(order_id);return;
				if(order_id>0)
				{
					freeze_window(5);
					show_list_view( order_id+'**'+0+'**'+job_no+'**'+sewing_production_variable, 'order_details_list', 'sewing_dtls_container', 'requires/ready_to_sewing_entry_controller', '' ) ;
					show_list_view( order_id, 'trims_status_list', 'trims_status_container', 'requires/ready_to_sewing_entry_controller', '' ) ;
					show_list_view( order_id, 'approval_status_list', 'approval_container', 'requires/ready_to_sewing_entry_controller', '' ) ;
					release_freezing();
				}
			}
		}
	}
	
	
	function fnc_ready_to_sewing( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		if(operation==4)
		{
			//var report_title=$( "div.form_caption" ).html();
			//generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_challan_no').val()+'*'+$('#update_id').val()+'*'+report_title,'grey_delivery_print');
			return;
		}
		
	 	if(form_validation('cbo_company_id*txt_sewing_date','Company*Sewing Date')==false)
		{
			return; 
		}
		
		/*var current_date='<?// echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_requisition_date').val(), current_date)==false)
		{
			alert("Requisition Date Can not Be Greater Than Today");
			return;
		}*/	
		var sewing_production_variable = $('#sewing_production_variable').val();
		var row_num=$('#order_table tbody tr').length;
		var dataString=""; var j=0;
		if(sewing_production_variable==3)
		{
			for (var i=1; i<=row_num; i++)
			{
				var hiddcountryid=$('#hiddcountryid_'+i).val();
				var gramentitem=$('#gramentitem_'+i).val();
				var colorid=$('#color_'+i).val();
				var sizeid=$('#size_'+i).val();
				var sewingissu=$('#sewingissu_'+i).val();
				var dtlsid=$('#dtlsid_'+i).val();
				var colorpoqty=$('#tdcolorqty_'+i).text()*1;
				//var colorpoqty=$('#tdcolorqty_'+i).innerHTML()*1;
				
				if(sewingissu>0)
				{
					j++;
					dataString+='&hiddcountryid'+j+'='+hiddcountryid+'&gramentitem'+j+'='+gramentitem+'&colorid'+j+'='+ colorid+'&sizeid'+j+'='+ sizeid+'&colorpoqty'+j+'='+ colorpoqty+'&sewingissu'+j+'='+sewingissu+'&dtlsid'+j+'='+dtlsid;
				}
			}
		}
		else
		{
			for (var i=1; i<=row_num; i++)
			{
				var hiddcountryid=$('#hiddcountryid_'+i).val();
				var gramentitem=$('#gramentitem_'+i).val();
				var colorid=$('#color_'+i).val();
				var sewingissu=$('#sewingissu_'+i).val();
				var dtlsid=$('#dtlsid_'+i).val();
				var colorpoqty=$('#tdcolorqty_'+i).text()*1;
				//var colorpoqty=$('#tdcolorqty_'+i).innerHTML()*1;
				
				if(sewingissu>0)
				{
					j++;
					dataString+='&hiddcountryid'+j+'='+hiddcountryid+'&gramentitem'+j+'='+gramentitem+'&colorid'+j+'='+ colorid+'&colorpoqty'+j+'='+ colorpoqty+'&sewingissu'+j+'='+sewingissu+'&dtlsid'+j+'='+dtlsid;
				}
			}
		}
		//alert(dataString)
		if(j<1)
		{
			alert('No data');
			return;
		}
		
		var data="action=save_update_delete&operation="+operation+'&tot_row='+j+get_submitted_data_string('txt_sewing_no*update_id*cbo_company_id*cbo_buyer_name*txt_sewing_date*txt_order_no*txt_order_id*sewing_production_variable' ,"../")+dataString;
		//alert(data);return;
		freeze_window(operation);
		
		http.open("POST","requires/ready_to_sewing_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_ready_to_sewing_response;
	}

	function fnc_ready_to_sewing_response()
	{
		if(http.readyState == 4) 
		{
			// alert(http.responseText);release_freezing();return;
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			if((response[0]==0 || response[0]==1))
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_sewing_no').value = response[3];
				show_list_view( response[2]+'**'+response[1], 'order_details_list', 'sewing_dtls_container', 'requires/ready_to_sewing_entry_controller', '' ) ;
				$('#cbo_company_id').attr('disabled','disabled');
				$('#cbo_buyer_name').attr('disabled','disabled');
				$('#txt_order_no').attr('disabled','disabled');
				$('#cbo_year').attr('disabled','disabled');
				set_button_status(1, permission, 'fnc_ready_to_sewing',1);
			}
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
			var page_link ='requires/ready_to_sewing_entry_controller.php?update_id='+update_id+'&txt_order_id='+txt_order_id+'&tot_seq_qnty='+tot_seq_qnty+'&action=trims_popup'+'&permission='+permission;
			var title="Trims Info";
			var popup_width="980px";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=450px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				
			}
		}
	}
	
	function openmypage_image(page_link, title)
    {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=420px,center=1,resize=1,scrolling=0', '')
        emailwindow.onclose = function ()
        {
        }
    }
	
	function fnc_ready_to_sewing_print()
	{
		if ($("#txt_sewing_no").val() == "") 
		{
			alert("Please Save First.");
			return;
		}
		else
		{
			var report_title = $("div.form_caption").html();
            generate_report_file($('#cbo_company_id').val() + '*' + $('#txt_sewing_no').val() + '*' + report_title + '*' + $('#update_id').val(), 'ready_to_sewing_print', 'requires/ready_to_sewing_entry_controller');
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/ready_to_sewing_entry_controller.php?data=" + data+'&action='+action, true );
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
		<div align="left" style="width:100%;">
        	<div style="float:left; width:670px;">
    <form name="readytosewing_1" id="readytosewing_1"> 
            <? echo load_freeze_divs ("../",$permission); ?>
            <fieldset style="width:660px;">
				<legend>Fabric Requisition</legend>
                <table cellpadding="0" cellspacing="2" width="650" id="tbl_mst">
                    <tr>
                        <td align="right" colspan="3"><b>System No.</b></td>
                        <td colspan="3">
                        	<input type="text" name="txt_sewing_no" id="txt_sewing_no" class="text_boxes" style="width:120px;" onDblClick="openmypage_sewing()" placeholder="Browse Reqsn. No." readonly/>
                            <input type="hidden" name="update_id" id="update_id"/>
                        </td>
                    </tr>
                    <tr>
                    	<td height="5" colspan="6"></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption" width="100" align="right">Company</td>
                        <td width="150">
                            <? 
                                echo create_drop_down( "cbo_company_id", 132, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/ready_to_sewing_entry_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'load_variable_settings','requires/ready_to_sewing_entry_controller')",0 );
                            ?>
                            <input type="hidden" id="sewing_production_variable" />
            				<input type="hidden" id="styleOrOrderWisw" />
                        </td>
                        <td width="100"  align="right">Buyer</td>
                        <td id="buyer_td" width="150">
						<? 
							echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "",0,"" );
						?>
                        </td>
                        <td width="50" align="right">Year</td>
                        <td>
                           <?
								echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
                        </td>
                    </tr>
                    <tr>
                        <td  class="must_entry_caption"  align="right">Entry Date</td>
                        <td><input type="text" name="txt_sewing_date" id="txt_sewing_date" class="datepicker" style="width:120px;" readonly /></td>
                        <td align="right">Order No</td>
                        <td>
                            <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:120px;" placeholder="Browse" onDblClick="openmypage_po()" readonly/>
                            <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes"/>
                        </td>
                        <td width="50" align="right">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
			</fieldset>
			<br>
			<fieldset style="width:660px;text-align:left">
                 <div style="width:660px;" align="left" id="sewing_dtls_container"></div>
			</fieldset>
	</form>
            </div>
            <div style="float:left; width:650px; margin-left:5px; margin-top:25px;">
            	<div style="width:100%" id="trims_status_container"></div>
                <div style="width:100%" id="approval_container"></div>
            </div>
			
    	</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
