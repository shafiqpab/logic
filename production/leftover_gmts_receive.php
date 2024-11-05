<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create leftover_gmts_receive Entry

Functionality	:
JS Functions	:
Created by		:	Shafiq
Creation date 	: 	24-12-2023
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
$u_id=$_SESSION['logic_erp']['user_id'];
$level=return_field_value("user_level","user_passwd","id='$u_id' and valid=1 ","user_level");

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Leftover Receive Entry","../", 1, 1, $unicode,'','');

if ($db_type == 0) {
    $sending_location="select concat(b.id,'*',a.id) id,concat(b.location_name,':',a.company_name) location_name from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
} else if ($db_type == 2 || $db_type == 1) {
    $sending_location="select b.id||'*'||a.id as id, b.location_name||' : '||a.company_name as location_name  from lib_company a, lib_location b where a.id=b.company_id and b.status_active =1 and b.is_deleted=0 and a.status_active =1 and a.is_deleted=0 order by a.company_name";
}
?>

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function dynamic_must_entry_caption(data)
	{
		if(data==1)
		{
			$('#locations').css('color','blue');
			$('#floors').css('color','blue');
			
			$('#servicewo_td').css('color','black');
			$("#txt_table_no").val('');
			$("#txt_wo_id").val('');
			// $("#txt_wo_no").attr("disabled",true);
		}
		else if(data==3)
		{
			$("#txt_table_no").val('');
			$("#txt_wo_id").val('');
			$('#locations').css('color','black');
			$('#floors').css('color','black');
			$('#servicewo_td').css('color','blue');
			// $("#txt_wo_no").attr("disabled",false);
		}
		else
		{
			$("#txt_table_no").val('');
			$("#txt_wo_id").val('');
			$('#locations').css('color','black');
			$('#floors').css('color','black');
			$('#servicewo_td').css('color','black');
			// $("#txt_wo_no").attr("disabled",true);
		}
	}

	function openmypage_sysNo()
	{
		var title = 'Leftover Info';	
		var page_link = 'requires/leftover_gmts_receive_controller.php?action=system_number_popup';
			
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=970px,height=390px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_data=this.contentDoc.getElementById("hidden_search_data").value;//po id
			if(mst_data!="")
			{ 
				freeze_window(5);
				
				var ex_data=mst_data.split('_');
				
				$('#txt_update_id').val(ex_data[0]);
				$('#txt_system_no').val(ex_data[1]);
				$('#cbo_company_name').val(ex_data[2]);
				load_drop_down( 'requires/leftover_gmts_receive_controller', ex_data[2]+'_'+1, 'load_drop_down_location', 'location_td' );
				$('#cbo_location').val(ex_data[3]);
				$('#cbo_source').val(ex_data[4]);
				fnc_load_party(1);
				
				$('#cbo_iron_company').val(ex_data[5]);
				fnc_load_party(2);
				$('#cbo_iron_location').val(ex_data[6]);
				
				if(ex_data[4]==1) var location=ex_data[6]; else  var location=ex_data[3];
				load_drop_down('requires/leftover_gmts_receive_controller', location, 'load_drop_down_floor', 'floor_td' );
				$('#cbo_floor').val(ex_data[7]);
				$('#txt_job_no').val(ex_data[8]);
				$('#txt_iron_date').val(ex_data[9]);
				$('#cbo_order_type').val(ex_data[10]);
				$('#cbo_goods_type').val(ex_data[11]);
				$('#txt_style_ref').val(ex_data[12]);
				$('#cbo_buyer_name').val(ex_data[13]);
				$('#exchange_rate').val(ex_data[14]);
				$('#cbo_category_id').val(ex_data[15]);
				$('#txt_remark').val(ex_data[16]);
	
				fnc_dtls_data_load_update(ex_data[8],ex_data[0]);
				
				get_php_form_data(ex_data[2], "production_process_control", "requires/leftover_gmts_receive_controller" );
				
				set_button_status(1, permission, 'fnc_iron_entry',1,0);
				release_freezing();
			}
		}
	}//end function
	
	function fnc_iron_entry(operation)
	{
		if(operation==2)
		{
			alert("Delete Restricted.")
			return;
		}
		var source=$("#cbo_source").val();
		if(operation==4)
		{
			// var report_title=$("div.form_caption").html();
			 //print_report( $('#cbo_company_name').val()+'*'+master_ids+'*'+report_title, "emblishment_issue_print", "requires/leftover_gmts_receive_controller" )
			// return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if ( form_validation('cbo_company_name*cbo_location*cbo_source*cbo_iron_company*txt_job_no*txt_iron_date*cbo_category_id','Company Name*Location*Source*Embel.Company*Job No*Issue Date*Category')==false )
			{
				return;
			}
			else
			{
				if(source==1)
				{
					if ( form_validation('cbo_iron_location*cbo_floor','Embel. Location*Floor')==false )
					{
						return;
					}
				}
				
				var current_date='<? echo date("d-m-Y"); ?>';
				if(date_compare($('#txt_iron_date').val(), current_date)==false)
				{
					alert("Receive Date Can not Be Greater Than Current Date");
					return;
				}
				var tot_row=$('#tbl_details tr').length;
				// alert(tot_row);
				var k=0; var data_str="";
				//alert(data_str);
				for (var i=1; i<=tot_row; i++)
				{
					var qty=$('#txtQty_'+i).val();
					if(qty*1>0)
					{
						k++;
						data_str+="&txtQty_" + k + "='" + $('#txtQty_'+i).val()+"'"+"&txtpoid_" + k + "='" + $('#txtpoid_'+i).val()+"'"+"&txtDtlsData_" + k + "='" + $('#txtDtlsData_'+i).val()+"'"+"&txtColorSizeid_" + k + "='" + $('#txtColorSizeid_'+i).val()+"'"+"&txtDtlsUpId_" + k + "='" + $('#txtDtlsUpId_'+i).val()+"'"+"&txtRate_" + k + "='" + $('#txtRate_'+i).val()+"'"+"&txtItemID_" + k + "='" + $('#txtItemID_'+i).val()+"'"+"&txtCountryID_" + k + "='" + $('#txtCountryID_'+i).val()+"'";
					}
				}
				if(k==0)
				{
					alert("Please Input Receive Qty.");
					return;
				}
				//alert(data_str)
	
				var data="action=save_update_delete&operation="+operation+'&tot_row='+k+get_submitted_data_string('garments_nature*txt_system_no*cbo_company_name*cbo_location*cbo_source*cbo_iron_company*cbo_iron_location*cbo_floor*txt_job_no*txt_style_ref*cbo_buyer_name*txt_iron_date*txt_remark*txt_job_id*txt_update_id*cbo_order_type*cbo_goods_type*exchange_rate*cbo_category_id',"../")+data_str;
				//alert (data); return;
				freeze_window(operation);
				http.open("POST","requires/leftover_gmts_receive_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_iron_entry_reply_info;
			}
		}
	}
	
	function fnc_iron_entry_reply_info()
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
				 setTimeout('fnc_iron_entry('+ reponse[1]+')',4000); 
			}
			else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				//if(reponse[4]){ alert("Receive Found Bundle List : "+reponse[3]+" This Bundle Not Any Change.");}
				show_msg(trim(reponse[0]));
				
				document.getElementById('txt_update_id').value = reponse[1];
				document.getElementById('txt_system_no').value = reponse[2];
				fnc_dtls_data_load_update( $('#txt_job_no').val(),reponse[1])
				set_button_status(1, permission, 'fnc_iron_entry',1,0);	
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
		reset_form('','','txt_issue_qty*txt_iss_id*hidden_break_down_html*hidden_colorSizeID*txt_remark*txt_cutting_qty*txt_cumul_issue_qty*txt_yet_to_issue*txt_mst_id','','');
		$('#txt_issue_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_cutting_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_cumul_issue_qty').attr('placeholder','');//placeholder value initilize
		$('#txt_yet_to_issue').attr('placeholder','');//placeholder value initilize
		$('#printing_production_list_view').html('');//listview container
		$("#breakdown_td_id").html('');
	
	}
	
	function fnc_total_calculate(qty,i)
	{

		var txt_user_lebel=$('#txt_user_lebel').val();
		var variable_is_controll=$('#hidden_variable_cntl').val();
		var exchange_rate=$('#exchange_rate').val();

		var placeholder_value = $("#txtQty_"+i).attr('placeholder')*1;
		var pre_iss_qty = $("#txtQty_"+i).attr('pre_issue_qty')*1;
		var orderRate = $("#orderRate_"+i).val()*1;
		var tot_row=$('#tbl_details tr').length;
		// alert(value+'='+placeholder_value);
		if((qty*1)>placeholder_value)
		{
			if(variable_is_controll==1 && txt_user_lebel!=2)
			{
				alert("Qnty Excceded");
				$("#txtQty_"+i).val('');
				$("#amtUSD_"+i).val('');
				$("#amtBDT_"+i).val('');
			}
			else
			{
				var confirm_value=confirm("Qnty Excceded. Press cancel to proceed otherwise press ok.");
				if(confirm_value!=0)
				{
					$("#txtQty_"+i).val('');
					$("#amtUSD_"+i).val('');
					$("#amtBDT_"+i).val('');
				}	
			}		
		}
		math_operation( "txtTotQty", "txtQty_", "+", (tot_row-1) );		
		$("#amtUSD_"+i).val(qty*orderRate);
		$("#amtBDT_"+i).val(exchange_rate*orderRate*qty);
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
			var cbo_order_type=$('#cbo_order_type').val();
			var cbo_goods_type=$('#cbo_goods_type').val();
			var cbo_source=$('#cbo_source').val();
			page_link=page_link+'&cbo_company_name='+cbo_company_name+'&cbo_source='+cbo_source+'&cbo_order_type='+cbo_order_type+'&cbo_goods_type='+cbo_goods_type;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=430px,center=1,resize=0,scrolling=0','')
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
					let currency_id = 2;
					check_exchange_rate(currency_id);
					get_php_form_data(cbo_company_name, "production_process_control", "requires/leftover_gmts_receive_controller" );
					release_freezing();
				}
			}
		}
	}	 
	 
	function check_exchange_rate(curr_id)
	{
		var cbo_currercy=curr_id;
		var booking_date = $('#txt_iron_date').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/leftover_gmts_receive_controller');
		var response=response.split("_");
		$('#exchange_rate').val(response[1]);
	}
	
	function fnc_dtls_data_load(job_no,uid)
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_order_type=$('#cbo_order_type').val();
		var cbo_goods_type=$('#cbo_goods_type').val();
		var cbo_source=$('#cbo_source').val();
		var list_view_orders = return_global_ajax_value( cbo_company_id+'***'+job_no+'***'+uid+'***'+cbo_order_type+'***'+cbo_goods_type+'***'+cbo_source, 'order_details', '', 'requires/leftover_gmts_receive_controller');
		if(list_view_orders!='')
		{
			//$("#tbl_details").html(list_view_orders);
			$("#tbl_details tr").remove();
			$("#tbl_details").prepend(list_view_orders);
		}
		
		let tableFilters1 =
		{ 
			col_operation: {
				id: ["th_total_size_qty","th_total_sewing_qty","th_total_exfact_qty","th_total_blnce","txtTotQty"],
				col: [8,9,10,11,12],
				operation: ["sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		} 
		setFilterGrid("tbl_item_details",-1,tableFilters1);
		$('#txt_job_no').attr('disabled','disabled');
		$('#cbo_source').attr('disabled','disabled');
		// fnc_total_calculate();
		
		/* var tot_row=$('#tbl_details tr').length-1;
		var orderQty=0; var planCutQty=0;
		for (var i=1; i<=tot_row; i++)
		{
			orderQty += $("#orderQty_"+i).text()*1;
			planCutQty += $("#planCutQty_"+i).text()*1;
		}
		
		$("#txtTotPoQty").val( orderQty );
		$("#txtTotPlanQty").val( planCutQty ); */
	}
	
	function fnc_dtls_data_load_update(job_no,uid)
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_order_type=$('#cbo_order_type').val();
		var cbo_goods_type=$('#cbo_goods_type').val();
		var cbo_source=$('#cbo_source').val();
		var list_view_orders = return_global_ajax_value( cbo_company_id+'***'+job_no+'***'+uid+'***'+cbo_order_type+'***'+cbo_goods_type+'***'+cbo_source, 'order_details_update', '', 'requires/leftover_gmts_receive_controller');
		if(list_view_orders!='')
		{
			//$("#tbl_details").html(list_view_orders);
			$("#tbl_details tr").remove();
			$("#tbl_details").prepend(list_view_orders);
		}
		let tableFilters1 =
		{ 
			col_operation: {
				id: ["th_total_size_qty","th_total_sewing_qty","th_total_exfact_qty","th_total_blnce","txtTotQty"],
				col: [8,9,10,11,12],
				operation: ["sum","sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
			}
		} 
		setFilterGrid("tbl_item_details",-1,tableFilters1);
		$('#txt_job_no').attr('disabled','disabled');
		$('#cbo_source').attr('disabled','disabled');
		// fnc_total_calculate();
		
		/* var tot_row=$('#tbl_details tr').length-1;
		var orderQty=0; var planCutQty=0;
		for (var i=1; i<=tot_row; i++)
		{
			orderQty += $("#orderQty_"+i).text()*1;
			planCutQty += $("#planCutQty_"+i).text()*1;
		}
		
		$("#txtTotPoQty").val( orderQty );
		$("#txtTotPlanQty").val( planCutQty ); */
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
		var working_company = $('#cbo_iron_company').val();
		var location_name = $('#cbo_location').val();
		
		if(source==1 && type==1)
		{
			load_drop_down( 'requires/leftover_gmts_receive_controller', company+'_'+1, 'load_drop_down_working_com', 'iron_company_td' );
		}
		else if(source==3 && type==1)
		{
			load_drop_down( 'requires/leftover_gmts_receive_controller', company+'_'+3, 'load_drop_down_working_com', 'iron_company_td' );
		}
		else if(source==1 && type==2)
		{
			load_drop_down( 'requires/leftover_gmts_receive_controller', working_company+'_'+2, 'load_drop_down_location', 'working_location_td' ); 
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
  	<? echo load_freeze_divs ("../",$permission);  ?>
    <div style="width:1050px; float:left" align="center">
 		<fieldset style="width:1050px;">
            <legend>Iron Info.</legend>
            <form name="ironentry_1" id="ironentry_1" method="" autocomplete="off" >
                <fieldset>
                    <table width="100%">
                        <tr>
                            <td colspan="4" align="right"><b>System NO : </b></td>
                            <td colspan="4"><input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_sysNo();" placeholder="Browse" readonly/>
                                <input name="txt_update_id" id="txt_update_id" type="hidden" />
                                <input name="txt_job_id" id="txt_job_id" type="hidden" />
                                <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl">
                                <input name="hidden_preceding_process" id="hidden_preceding_process" type="hidden" />
            					<input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td width="100" class="must_entry_caption">Lc. Company</td>
                            <td width="140"><? echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select--", $selected, "load_drop_down( 'requires/leftover_gmts_receive_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); fnc_load_party(1,document.getElementById('cbo_source').value);","" ); ?></td>
                            <td width="100" class="must_entry_caption">Lc. Com. Location</td>
                            <td width="140" id="location_td"><? echo create_drop_down( "cbo_location", 130, $blank_array,"", 1, "--Select Location--", $selected, "" ); ?></td>
                            <td width="100" class="must_entry_caption">Prod. Source</td>
                            <td width="140"><? echo create_drop_down( "cbo_source", 130, $knitting_source,"", 1, "--Select Source--", $selected, "fnc_load_party(1,this.value); dynamic_must_entry_caption(this.value);", 0, '1,3' ); ?></td>
                            <td width="100" class="must_entry_caption">WO. Company</td>
                            <td id="iron_company_td"><? echo create_drop_down( "cbo_iron_company", 130, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                        </tr>
                        <tr>
                            <td id="locations">Location</td>
                            <td id="working_location_td"><? echo create_drop_down( "cbo_iron_location", 130, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                            <td id="floors">Floor</td>
                            <td id="floor_td"><? echo create_drop_down( "cbo_floor", 130, $blank_array,"", 1, "--Select Floor--", $selected, "" ); ?></td>
							<td class="must_entry_caption">Order Type</td>
                            <td>
                            <?
                            
                            echo create_drop_down( "cbo_order_type", 130, $order_source, "", 1, "-- Select --", $selected, "", "", "1,2", "", "");
                            ?>
                            </td>
							
                        	<td width="120" class="must_entry_caption">Goods Type</td>
	                        <td> 
	                        <?
	                        $goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
	                        echo create_drop_down( "cbo_goods_type", 130, $goods_type_arr, "", 1, "-- Select Goods Type --", $selected, "", "", "", '', '');
	                        ?>
	                        </td> 
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Job/Style NO</td>
                            <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:120px" onDblClick="openmypage_job('requires/leftover_gmts_receive_controller.php?action=job_popup', 'Job/Order Selection Form');" placeholder="Browse" readonly /></td>
                            <td>Style Ref.</td>
                            <td><input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:120px" disabled /></td>
                        	<td>Buyer Name</td>
                            <td><? echo create_drop_down( "cbo_buyer_name", 130, "select id, buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "",1,0 ); ?></td>
							
                              
							
	                        <td width="100">Exchange Rate(USD to BDT)</td>
	                        <td width="130">
	                        <input name="exchange_rate" id="exchange_rate" class="text_boxes" style="width:120px " disabled>	
	                        </td>                  
						</tr>
                        <tr>
                            <td class="must_entry_caption">Receive Date</td>
                            <td><input type="text" name="txt_iron_date" id="txt_iron_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:120px;"  /></td>  
                            <td>Remarks</td>
                            <td colspan="3"><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:440px" title="Max 450 Characters Only." /> <input type="hidden" name="txt_wo_id" id="txt_wo_id" class="text_boxes" style="width:50px" /></td>
							
							
                        	<td width="120" class="must_entry_caption">Category</td>
	                        <td> 
	                        <?
	                        $categories = array(1 => 'A', 2 => 'B');
							echo create_drop_down( 'cbo_category_id', 132, $categories, '', 1, '-- Select Category --', $selected, '', 0);
	                        ?>
	                        </td> 
                        </tr>
                    </table>
                </fieldset><br />
                    
                <fieldset style="width:1270px">
                    <legend>Details List</legend>
                    <div>
                        <table cellpadding="0" width="1252" cellspacing="0" border="1" class="rpt_table" rules="all"  align="left">
                            <thead>
                                <tr>
                                    <th width="30">SL</th>
                                    <th width="110">Order No</th>
                                    <th width="70">Pub. Shipment Date</th>
                                    <th width="110">Gmts. Item</th>
                                    <th width="110">Country</th>
                                    <th width="70">C. Shipment Date</th>
                                    <th width="120">Gmts. Color</th>
                                    <th width="70">Size</th>
                                    <th width="70">Size Qty</th>
                                    <th width="70">Sewing Qty</th>
                                    <th width="70">Garments Delivery Qty</th>
                                    <th width="70">Leftover Balance</th>
                                    <th width="70">Leftover Qty</th>
                                    <th width="70">FOB Rate</th>
                                    <th width="70">Amount USD</th>
                                    <th>Amount BDT</th>
                                </tr>
                            </thead>
                        </table>
                        <div  style="width:1270px;max-height:250px;overflow-y:scroll"  align="left">    
                            <table cellpadding="0" width="1252" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_item_details"  align="left">      
                                <tbody id="tbl_details">
                                	<tr bgcolor="#E9F3FF">
                                        <td width="30" align="center">1</td>
                                        <td width="110">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="110">&nbsp;</td>
                                        <td width="110">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="120">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        
                                        <td width="70"></td>
                                        <td width="70"></td>
                                        <td width="70">
											<input type="text" name="txtRecvQty_1" id="txtRecvQty_1" class="text_boxes_numeric" style="width:53px;" value="" onBlur="fnc_total_calculate(this.value,1);" />
										</td>
                                        <td width="70"></td>
                                        <td width="70"></td>
                                        <td>
                                            <input type="hidden" name="txtpoid_1" id="txtpoid_1" style="width:30px" class="text_boxes" value="" />
                                            <input type="hidden" name="txtColorSizeid_1" id="txtColorSizeid_1" style="width:30px" class="text_boxes" value="" />
                						</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <table cellpadding="0" width="1252" cellspacing="0" border="1" class="rpt_table" rules="all"  align="left">      
                            <tfoot>
                                <tr class="tbl_bottom" name="tr_btm" id="tr_btm">
                                    <th width="30">&nbsp;</th>
                                    <th width="110">&nbsp;</th>
                                    <th width="70">&nbsp;</th>
                                    <th width="110">&nbsp;</th>
                                    <th width="110">&nbsp;</th>
                                    <th width="70">&nbsp;</th>
                                    <th width="120">&nbsp;</th>
                                    <th width="70">Total:</th>
                                    <th width="70" id="th_total_size_qty"></th>
                                    <th width="70" id="th_total_sewing_qty"></th>
                                    
                                    <th width="70" id="th_total_exfact_qty"></th>
                                    <th width="70" id="th_total_blnce"></th>
                                    <th width="70">
										<input type="text" name="txtTotQty" id="txtTotQty" class="text_boxes_numeric" style="width:55px;" value="" readonly />
									</th>
                                    <th width="70"></th>
                                    <th width="70"></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <table cellpadding="0" cellspacing="1" width="1270">
                        <tr>
                            <td align="center" colspan="19" valign="middle" class="button_container">
                                <?
                                    $date=date('d-m-Y');
                                    echo load_submit_buttons( $permission, "fnc_iron_entry", 0,0 , "reset_form('ironentry_1','','', 'txt_iron_date,".$date."','')",1); 
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
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
