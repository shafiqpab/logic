<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Send on Area

Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	24-09-2023
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
$u_id=$_SESSION['logic_erp']['user_id'];
$level=return_field_value("user_level","user_passwd","id='$u_id' and valid=1 ","user_level");

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Send on Area","../../", 1, 1, $unicode,'','');

if ($db_type == 0) {
    $sending_location="select concat(b.id,'*',a.id) id,concat(b.location_name,':',a.company_name) location_name from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
} else if ($db_type == 2 || $db_type == 1) {
    $sending_location="select b.id||'*'||a.id as id, b.location_name||' : '||a.company_name as location_name  from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
}
?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	function dynamic_must_entry_caption(data)
	{
		if(data==1)
		{
			$('#locations').css('color','blue');
			$('#floors').css('color','blue');
		}
		else
		{
			$('#locations').css('color','black');
			$('#floors').css('color','black');
		}
	}

	function openmypage_sysNo()
	{
		var title = 'Send on Area Info';	
		var page_link = 'requires/send_on_area_controller.php?action=system_number_popup';
			
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
				load_drop_down( 'requires/send_on_area_controller', ex_data[2]+'_'+1, 'load_drop_down_location', 'location_td' );
				$('#cbo_location').val(ex_data[3]);
				$('#cbo_source').val(ex_data[4]);
				fnc_load_party(1);
				
				$('#cbo_send_company').val(ex_data[5]);
				fnc_load_party(2);
				$('#cbo_send_location').val(ex_data[6]);
				
				if(ex_data[4]==1) var location=ex_data[6]; else  var location=ex_data[3];
				load_drop_down('requires/send_on_area_controller', location, 'load_drop_down_floor', 'floor_td' );
				$('#cbo_floor').val(ex_data[7]);
				$('#txt_job_id').val(ex_data[8]);
				$('#txt_job_no').val(ex_data[9]);
				$('#txt_send_date').val(ex_data[10]);
				load_drop_down( 'requires/send_on_area_controller', ex_data[7]+'_'+ex_data[3]+'_'+ex_data[10], 'load_drop_down_send_line_floor', 'send_line_td' );
				$('#cbo_send_line').val(ex_data[11]);
				$('#txt_challan').val(ex_data[12]);
				$('#txt_remark').val(ex_data[13]);
				$('#txt_style_ref').val(ex_data[14]);
				$('#cbo_buyer_name').val(ex_data[15]);
				$('#txt_reporting_hour').val(ex_data[16]);
	
				fnc_dtls_data_load(ex_data[9],ex_data[0]);
				dynamic_must_entry_caption(ex_data[4]);
				get_php_form_data(ex_data[2], "production_process_control", "requires/send_on_area_controller" );
				
				set_button_status(1, permission, 'fnc_sendonarea_entry',1,0);
				release_freezing();
			}
		}
	}//end function
	
	function fnc_sendonarea_entry(operation)
	{
		freeze_window(operation);
		if(operation==2)
		{
			alert("Delete Restricted.");
			release_freezing();
			return;
		}
		var source=$("#cbo_source").val();
		if(operation==4)
		{
			// var report_title=$("div.form_caption").html();
			 //print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "sendonarea_print", "requires/send_on_area_controller" );
			 //release_freezing();
			// return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if ( form_validation('cbo_company_name*cbo_location*cbo_source*cbo_send_company*txt_job_no*txt_send_date*cbo_send_line*txt_reporting_hour','Company Name*Location*Source*W. Company*Job No*Send Date*Send Line No*Reporting Hour')==false )
			{
				release_freezing();
				return;
			}
			else
			{
				if(source==1)
				{
					if ( form_validation('cbo_send_location*cbo_floor','W. Location*Floor')==false )
					{
						release_freezing();
						return;
					}
				}
				
				var current_date='<? echo date("d-m-Y"); ?>';
				if(date_compare($('#txt_send_date').val(), current_date)==false)
				{
					alert("Send on Area Date Can not Be Greater Than Current Date");
					release_freezing();
					return;
				}
				var tot_row=$('#tbl_details tr').length-1;
				//alert(tot_row);
				var k=0; var data_str="";
				//alert(data_str);
				for (var i=1; i<=tot_row; i++)
				{
					var qty=$('#txtQty_'+i).val();
					if(qty*1>0)
					{
						k++;
						data_str+="&txtAltQty_" + k + "='" + $('#txtAltQty_'+i).val()+"'"+"&txtSpot_" + k + "='" + $('#txtSpot_'+i).val()+"'"+"&txtRjtQty_" + k + "='" + $('#txtRjtQty_'+i).val()+"'"+"&txtReIrnQty_" + k + "='" + $('#txtReIrnQty_'+i).val()+"'"+"&txtQty_" + k + "='" + $('#txtQty_'+i).val()+"'"+"&txtpoid_" + k + "='" + $('#txtpoid_'+i).val()+"'"+"&txtDtlsData_" + k + "='" + $('#txtDtlsData_'+i).val()+"'"+"&txtColorSizeid_" + k + "='" + $('#txtColorSizeid_'+i).val()+"'"+"&txtDtlsUpId_" + k + "='" + $('#txtDtlsUpId_'+i).val()+"'";
					}
				}
				if(k==0)
				{
					alert("Please input QC Pass Qty. (PCS).");
					release_freezing();
					return;
				}
				//alert(data_str)
	
				var data="action=save_update_delete&operation="+operation+'&tot_row='+k+get_submitted_data_string('garments_nature*txt_system_no*cbo_company_name*cbo_location*cbo_source*cbo_send_company*cbo_send_location*cbo_floor*txt_job_no*txt_style_ref*cbo_buyer_name*txt_send_date*txt_reporting_hour*cbo_send_line*txt_challan*txt_remark*txt_job_id*txt_update_id',"../../")+data_str;
				//alert (data); return;
				
				http.open("POST","requires/send_on_area_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_sendonarea_entry_reply_info;
			}
		}
	}
	
	function fnc_sendonarea_entry_reply_info()
	{
		if(http.readyState == 4)
		{
			var reponse=http.responseText.split('**');
			/*if(trim(reponse[0])=='emblRec'){
				alert("Receive Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible.")
				release_freezing();
				return;
			}*/	 
			if(reponse[0]==15) 
			{ 
				 setTimeout('fnc_sendonarea_entry('+ reponse[1]+')',4000); 
			}
			else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				//if(reponse[4]){ alert("Receive Found Bundle List : "+reponse[3]+" This Bundle Not Any Change.");}
				show_msg(trim(reponse[0]));
				
				document.getElementById('txt_update_id').value = reponse[1];
				document.getElementById('txt_system_no').value = reponse[2];
				fnc_dtls_data_load( $('#txt_job_no').val(),reponse[1])
				set_button_status(1, permission, 'fnc_sendonarea_entry',1,0);	
				release_freezing();
			}
			if(reponse[0]!=15)
			{
			  release_freezing();
			}
		}
	}
	
	function childFormReset()
	{
		reset_form('','','txt_issue_qty*txt_challan*txt_iss_id*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*txt_mst_id','','');
		$('#txt_issue_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_cutting_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_cumul_issue_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_yet_to_issue').attr('placeholder','');//placeholder value initilize
		$('#printing_production_list_view').html('');//listview container
		$("#breakdown_td_id").html('');
	}
	
	function fnc_total_calculate(value,i)
	{
		var txt_user_lebel=$('#txt_user_lebel').val();
		var variable_is_controll=$('#hidden_variable_cntl').val();

		var placeholder_value = $("#txtQty_"+i).attr('placeholder')*1;
		var pre_iss_qty = $("#txtQty_"+i).attr('pre_issue_qty')*1;
		var planCut = $("#planCutQty_"+i).text()*1;
		var tot_row=$('#tbl_details tr').length-1;
		// alert(placeholder_value+'*'+variable_is_controll +'*'+txt_user_lebel);
		if(((value*1)+pre_iss_qty)>placeholder_value)
		{
			if(variable_is_controll==1 && txt_user_lebel!=2)
			{
				alert("Qnty Excceded");
				$("#txtQty_"+i).val('');
			}
			else
			{
				var confirm_value=confirm("Send on Area Excceded by Plan Qty. (PCS). Press cancel to proceed otherwise press ok.");
				if(confirm_value!=0)
				{
					$("#txtQty_"+i).val('');
				}	
			}		
		}
		math_operation( "txtTotAltQty", "txtAltQty_", "+", tot_row );
		math_operation( "txtTotSpotQty", "txtSpot_", "+", tot_row );
		math_operation( "txtTotRjtQty", "txtRjtQty_", "+", tot_row );
		math_operation( "txtTotReIrnQty", "txtReIrnQty_", "+", tot_row );
		math_operation( "txtTotQty", "txtQty_", "+", tot_row );
	}
	
	function openmypage_job(page_link,title)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;   
		}
		else
		{
			var cbo_company_name=$('#cbo_company_name').val();
			page_link=page_link+'&cbo_company_name='+cbo_company_name;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=430px,center=1,resize=0,scrolling=0','../')
			release_freezing();
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_job").value;
				if (theemail!="")
				{
					freeze_window(5);
					var ex_data=theemail.split('__');
					$('#txt_job_id').val(ex_data[0]);
					$('#txt_job_no').val(ex_data[1]);
					$('#cbo_buyer_name').val(ex_data[2]);
					$('#txt_style_ref').val(ex_data[3]);
					$('#cbo_company_name').attr('disabled','disabled');
					fnc_dtls_data_load(ex_data[1],0);
					get_php_form_data(cbo_company_name, "production_process_control", "requires/send_on_area_controller" );
					release_freezing();
				}
			}
		}
	}
	
	function fnc_dtls_data_load(job_no,uid)
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var list_view_orders = return_global_ajax_value( cbo_company_id+'***'+job_no+'***'+uid, 'order_details', '', 'requires/send_on_area_controller');
		if(list_view_orders!='')
		{
			//$("#tbl_details").html(list_view_orders);
			$("#tbl_details tr").remove();
			$("#tbl_details").prepend(list_view_orders);
		}
		
		setFilterGrid("tbl_details",-1);
		$('#txt_job_no').attr('disabled','disabled');
		$('#cbo_source').attr('disabled','disabled');
		fnc_total_calculate();
		
		var tot_row=$('#tbl_details tr').length-1;
		var orderQty=0; var planCutQty=0;
		for (var i=1; i<=tot_row; i++)
		{
			orderQty += $("#orderQty_"+i).text()*1;
			planCutQty += $("#planCutQty_"+i).text()*1;
		}
		
		$("#txtTotPoQty").val( orderQty );
		$("#txtTotPlanQty").val( planCutQty );
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
		var working_company = $('#cbo_send_company').val();
		var location_name = $('#cbo_location').val();
		
		if(source==1 && type==1)
		{
			load_drop_down( 'requires/send_on_area_controller', company+'_'+1, 'load_drop_down_working_com', 'send_company_td' );
		}
		else if(source==3 && type==1)
		{
			load_drop_down( 'requires/send_on_area_controller', company+'_'+3, 'load_drop_down_working_com', 'send_company_td' );
		}
		else if(source==1 && type==2)
		{
			load_drop_down( 'requires/send_on_area_controller', working_company+'_'+2, 'load_drop_down_location', 'working_location_td' ); 
		} 
	}
	
	function fnc_valid_time( val, field_id )
	{
		var val_length=val.length;
		if(val_length==2)
		{
			document.getElementById(field_id).value=val+":";
		}
		
		var colon_contains=val.includes(":");
		if(colon_contains==false)
		{
			if(val>23)
			{
				document.getElementById(field_id).value='23:';
			}
		}
		else
		{
			var data=val.split(":");
			var minutes=data[1];
			var str_length=minutes.length;
			var hour=data[0]*1;
			
			if(hour>23) hour=23;
			
			if(str_length>=2)
			{
				minutes= minutes.substr(0, 2);
				if(minutes*1>59) minutes=59;
			}
			var valid_time=hour+":"+minutes;
			document.getElementById(field_id).value=valid_time;
		}
	}

	function numOnly(myfield, e, field_id)
	{
		var key;
		var keychar;
		if (window.event)
			key = window.event.keyCode;
		else if (e)
			key = e.which;
		else
			return true;
		keychar = String.fromCharCode(key);
	
		// control keys
		if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;
		// numbers
		else if ((("0123456789:").indexOf(keychar) > -1))
		{
			var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
			if(keychar==":" && dotposl!=-1)
			{
				return false;
			}
			return true;
		}
		else
			return false;
	}
	
	function calculate_qcpasss(id)
	{ 
		//alert(23);
		var prodQty=$("#prodQty_"+id).text()*1;
		var rejectQty=$("#rejectQty_"+id).val()*1;
		var alterQty=$("#alterQty_"+id).val()*1;
		var spotQty=$("#spotQty_"+id).val()*1;
		var totReject=(rejectQty+alterQty+spotQty);
		var replaceQty=$("#replaceQty_"+id).val()*1;
		var qc_qty=(prodQty-totReject)+replaceQty;
		
		if(prodQty<qc_qty)
		{
			qc_qty=qc_qty=(prodQty-totReject);
			$("#replaceQty_"+id).val('');
		}
		
		if(totReject>=prodQty)
		{
			$("#rejectQty_"+id).val('');
			$("#alterQty_"+id).val('');
			$("#spotQty_"+id).val('');
			$("#replaceQty_"+id).val('');
			$("#qcQty_"+id).text(prodQty);
		}
		else
		{
			$("#txtqty_"+id).val(qc_qty);
			$("#qcQty_"+id).text(qc_qty);
		}
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
  	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div style="width:1050px; float:left" align="center">
 		<fieldset style="width:1050px;">
            <legend>Send on Area Info.</legend>
            <form name="sendonarea_1" id="sendonarea_1" method="" autocomplete="off" >
                <fieldset>
                    <table width="100%">
                        <tr>
                            <td colspan="4" align="right"><b>Send On Area NO : </b></td>
                            <td colspan="4"><input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_sysNo();" placeholder="Browse" readonly/>
                                <input name="txt_update_id" id="txt_update_id" type="hidden" />
                                <input name="txt_job_id" id="txt_job_id" type="hidden" />
                                <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl">
                                <input name="hidden_preceding_process" id="hidden_preceding_process" type="hidden" />
            					<input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td width="100" class="must_entry_caption">LC Company</td>
                            <td width="140"><? echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select--", $selected, "load_drop_down( 'requires/send_on_area_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); fnc_load_party(1,document.getElementById('cbo_source').value);","" ); ?></td>
                            <td width="100" class="must_entry_caption">Lc. Com. Location</td>
                            <td width="140" id="location_td"><? echo create_drop_down( "cbo_location", 130, $blank_array,"", 1, "--Select Location--", $selected, "" ); ?></td>
                            <td width="100" class="must_entry_caption">Source</td>
                            <td width="140"><? echo create_drop_down( "cbo_source", 130, $knitting_source,"", 1, "--Select Source--", $selected, "fnc_load_party(1,this.value); dynamic_must_entry_caption(this.value);", 0, '1,3' ); ?></td>
                            <td width="100" class="must_entry_caption">W. Company</td>
                            <td id="send_company_td"><? echo create_drop_down( "cbo_send_company", 130, $blank_array,"", 1, "-W. Company-", $selected, "" ); ?></td>
                        </tr>
                        <tr>
                            <td id="locations">W.C Location</td>
                            <td id="working_location_td"><? echo create_drop_down( "cbo_send_location", 130, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                            <td id="floors">Floor</td>
                            <td id="floor_td"><? echo create_drop_down( "cbo_floor", 130, $blank_array,"", 1, "--Select Floor--", $selected, "" ); ?></td>
                            <td class="must_entry_caption">Job No</td>
                            <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:120px" onDblClick="openmypage_job('requires/send_on_area_controller.php?action=job_popup', 'Job/Order Selection Form');" placeholder="Browse" readonly /></td>
                            <td>Style Ref.</td>
                            <td><input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:120px" disabled /></td>
                        </tr>
                        <tr>
                        	<td>Buyer Name</td>
                            <td><? echo create_drop_down( "cbo_buyer_name", 130, "select id, buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "",1,0 ); ?></td>
                            <td class="must_entry_caption">Send Date</td>
                            <td><input type="text" name="txt_send_date" id="txt_send_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:120px;" onChange="load_drop_down( 'requires/send_on_area_controller',document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+this.value, 'load_drop_down_send_line_floor', 'send_line_td' );"  /></td>
                            <td class="must_entry_caption">Send Line No</td> 
                            <td id="send_line_td"><? echo create_drop_down( "cbo_send_line", 130, $blank_array,"", 1, "Select Line", $selected, "",1,0 ); ?></td>
                            <td class="must_entry_caption">Reporting Hour</td>
                            <td><input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:120px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" maxlength="8" /></td>
                        </tr>
                        <tr>
                            <td>Challan No</td>
                            <td><input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:120px" /></td>
                            <td>Remarks</td>
                            <td colspan="5"><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:605px" title="450 Characters Only." /></td>
                        </tr>
                    </table>
                </fieldset><br />
                    
                <fieldset style="width:1200px">
                    <legend>Send on Area Details List</legend>
                    <div>
                        <table cellpadding="0" width="1200" cellspacing="0" border="1" class="rpt_table" rules="all">
                            <thead>
                                <tr>
                                    <th width="30">SL</th>
                                    <th width="110">Order No</th>
                                    <th width="60">Pub. Shipment Date</th>
                                    <th width="110">Gmts. Item</th>
                                    <th width="110">Country</th>
                                    <th width="60">C. Shipment Date</th>
                                    <th width="120">Gmts. Color</th>
                                    <th width="70">Size</th>
                                    <th width="70">Po Qty. [PCS]</th>
                                    <th width="70">Plan Qty. [PCS]</th>
                                    <th width="70">Alter Qty.</th>
                                    <th width="70">Spot Qty.</th>
                                    <th width="70">Reject Qty.</th>
                                    <th width="70">Re-Send Qty. [PCS]</th>
                                    <th>QC Pass Qty. [PCS]</th>
                                </tr>
                            </thead>
                        </table>
                        <div  style="width:1200px;max-height:250px;overflow-y:scroll"  align="left">    
                            <table cellpadding="0" width="1180" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_item_details">      
                                <tbody id="tbl_details">
                                	<tr bgcolor="#E9F3FF">
                                        <td width="30" align="center">1</td>
                                        <td width="110">&nbsp;</td>
                                        <td width="60">&nbsp;</td>
                                        <td width="110">&nbsp;</td>
                                        <td width="110">&nbsp;</td>
                                        <td width="60">&nbsp;</td>
                                        <td width="120">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="70" id="orderQty_1">&nbsp;</td>
                                        <td width="70" id="planCutQty_1">&nbsp;</td>
                                        
                                        <td width="70"><input type="text" name="txtAltQty_1" id="txtAltQty_1" class="text_boxes_numeric" style="width:53px;" value="" onBlur="fnc_total_calculate(this.value,1);" /></td>
                                        <td width="70"><input type="text" name="txtSpot_1" id="txtSpot_1" class="text_boxes_numeric" style="width:53px;" value="" onBlur="fnc_total_calculate(this.value,1);" /></td>
                                        <td width="70"><input type="text" name="txtRjtQty_1" id="txtRjtQty_1" class="text_boxes_numeric" style="width:53px;" value="" onBlur="fnc_total_calculate(this.value,1);" /></td>
                                        <td width="70"><input type="text" name="txtReIrnQty_1" id="txtReIrnQty_1" class="text_boxes_numeric" style="width:53px;" value="" onBlur="fnc_total_calculate(this.value,1);" /></td>
                                        <td>
                                        	<input type="text" name="txtQty_1" id="txtQty_1" class="text_boxes_numeric" style="width:53px;" value="" onBlur="fnc_total_calculate(this.value,1);" />
                                            <input type="hidden" name="txtpoid_1" id="txtpoid_1" style="width:30px" class="text_boxes" value="" />
                                            <input type="hidden" name="txtColorSizeid_1" id="txtColorSizeid_1" style="width:30px" class="text_boxes" value="" />
                                            <input type="hidden" name="txtDtlsData_1" id="txtDtlsData_1" style="width:30px" class="text_boxes" value="" />
                                            <input type="hidden" name="txtDtlsUpId_1" id="txtDtlsUpId_1" style="width:30px" class="text_boxes" value="" />
                						</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <table cellpadding="0" width="1200" cellspacing="0" border="1" class="rpt_table" rules="all">      
                            <tbody>
                                <tr class="tbl_bottom" name="tr_btm" id="tr_btm">
                                    <td width="30">&nbsp;</td>
                                    <td width="110">&nbsp;</td>
                                    <td width="60">&nbsp;</td>
                                    <td width="110">&nbsp;</td>
                                    <td width="110">&nbsp;</td>
                                    <td width="60">&nbsp;</td>
                                    <td width="120">&nbsp;</td>
                                    <td width="70">Total:</td>
                                    <td width="70"><input type="text" name="txtTotPoQty" id="txtTotPoQty" class="text_boxes_numeric" style="width:53px;" value="" readonly /></td>
                                    <td width="70"><input type="text" name="txtTotPlanQty" id="txtTotPlanQty" class="text_boxes_numeric" style="width:53px;" value="" readonly /></td>
                                    
                                    <td width="70"><input type="text" name="txtTotAltQty" id="txtTotAltQty" class="text_boxes_numeric" style="width:53px;" value="" readonly /></td>
                                    <td width="70"><input type="text" name="txtTotSpotQty" id="txtTotSpotQty" class="text_boxes_numeric" style="width:53px;" value="" readonly /></td>
                                    <td width="70"><input type="text" name="txtTotRjtQty" id="txtTotRjtQty" class="text_boxes_numeric" style="width:53px;" value="" readonly /></td>
                                    <td width="70"><input type="text" name="txtTotReIrnQty" id="txtTotReIrnQty" class="text_boxes_numeric" style="width:53px;" value="" readonly /></td>
                                    <td><input type="text" name="txtTotQty" id="txtTotQty" class="text_boxes_numeric" style="width:53px;" value="" readonly /></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <table cellpadding="0" cellspacing="1" width="1200">
                        <tr>
                            <td align="center" colspan="18" valign="middle" class="button_container">
                                <?
                                    $date=date('d-m-Y');
                                    echo load_submit_buttons( $permission, "fnc_sendonarea_entry", 0,0 , "reset_form('sendonarea_1','','', 'txt_send_date,".$date."','')",1); 
                                ?>
                                <input type="hidden"  name="hidden_row_number" id="hidden_row_number"> 
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </fieldset>
    </div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>