<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Garments Receive In Store

Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	03-10-2023
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

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Garments Receive In Store","../../", 1, 1, $unicode,'','');

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
		}
		else
		{
			$('#locations').css('color','black');
			$('#floors').css('color','black');
		}
	}

	function openmypage_sysNo()
	{ 
		var title = 'Garments Receive In Store Info';	
		var page_link = 'requires/garments_receive_in_store_controller.php?action=system_number_popup';
			
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var mst_data=this.contentDoc.getElementById("hidd_str_data").value;//po id
			if(mst_data!="")
			{ 
				freeze_window(5);

				var ex_data=mst_data.split('_');
			
				$('#txt_wo_no').val(ex_data[18]);
				$('#txt_update_id').val(ex_data[0]);
				$('#txt_system_no').val(ex_data[1]);
				$('#cbo_company_name').val(ex_data[2]);
				load_drop_down( 'requires/garments_receive_in_store_controller', ex_data[2]+'_'+1, 'load_drop_down_location', 'location_td' );
				$('#cbo_location').val(ex_data[3]);
				$('#cbo_source').val(ex_data[4]);
				fnc_load_party(1);
				
				$('#cbo_working_company').val(ex_data[5]);
				fnc_load_party(2);
				$('#cbo_working_location').val(ex_data[6]);
				
				if(ex_data[4]==1) var location=ex_data[6]; else  var location=ex_data[3];
				load_drop_down('requires/garments_receive_in_store_controller', location, 'load_drop_down_floor', 'floor_td' );
				$('#cbo_floor').val(ex_data[7]);
				$('#txt_job_id').val(ex_data[8]);
				$('#txt_job_no').val(ex_data[9]);
				$('#txt_receive_date').val(ex_data[10]);
				$('#cbo_sending_location').val(ex_data[13]);
				$('#txt_challan').val(ex_data[14]);
				$('#txt_remark').val(ex_data[15]);
				$('#txt_style_ref').val(ex_data[16]);
				$('#cbo_buyer_name').val(ex_data[17]);
   
				fnc_dtls_data_load(ex_data[9],ex_data[0]);
				dynamic_must_entry_caption(ex_data[4]);
				
				set_button_status(1, permission, 'fnc_receive_in_store_entry',1,0);
				release_freezing();
			}
		}
	}//end function
	
	function fnc_receive_in_store_entry(operation)
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
			var report_title=$("div.form_caption").html();
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_update_id').val()+'*'+report_title, "garments_receive_store_print", "requires/garments_receive_in_store_controller");
			release_freezing();
			return;
		}
		
		else if(operation==0 || operation==1 || operation==2)
		{  
			if ( form_validation('cbo_company_name*cbo_location*cbo_source*cbo_working_company*txt_job_no*txt_receive_date','Company Name*Location*Source*W. Company*Job No*Receive Date')==false )
			{
				release_freezing();
				return;
			}
			else
			{
				if(source==1)
				{
					if ( form_validation('cbo_working_location*cbo_floor','WC. Location*Floor')==false )
					{
						release_freezing();
						return;
					}
				}
				
				var current_date='<? echo date("d-m-Y"); ?>';
				if(date_compare($('#txt_receive_date').val(), current_date)==false)
				{
					alert("Receive Date Can not Be Greater Than Current Date");
					release_freezing();
					return;
				}
				var tot_row=$('#tbl_details tr').length-1;
				//alert(tot_row);
				var k=0; var data_str="";
				//alert(data_str);
				for (var i=1; i<=tot_row; i++)
				{
					var qty=$('#txtReceiveQty_'+i).val();
					if(qty*1>0)
					{
						k++;
						data_str+="&txtReceiveQty_" + k + "='" + $('#txtReceiveQty_'+i).val()+"'"+"&txtpoid_" + k + "='" + $('#txtpoid_'+i).val()+"'"+"&txtDtlsData_" + k + "='" + $('#txtDtlsData_'+i).val()+"'"+"&txtColorSizeid_" + k + "='" + $('#txtColorSizeid_'+i).val()+"'"+"&txtDtlsUpId_" + k + "='" + $('#txtDtlsUpId_'+i).val()+"'";
					}
				}
				if(k==0)
				{
					alert("Please input Receive Qty. (PCS).");
					release_freezing();
					return;
				}
				//alert(data_str)
	
				var data="action=save_update_delete&operation="+operation+'&tot_row='+k+get_submitted_data_string('garments_nature*txt_system_no*cbo_company_name*cbo_location*cbo_source*cbo_working_company*cbo_working_location*cbo_floor*txt_job_no*txt_style_ref*cbo_buyer_name*txt_receive_date*cbo_sending_location*txt_challan*txt_remark*txt_job_id*txt_update_id*txt_wo_id',"../../")+data_str;
				// alert (data); return;
				
				http.open("POST","requires/garments_receive_in_store_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_receive_in_store_entry_reply_info;
			}
		}
	}
	
	function fnc_receive_in_store_entry_reply_info()
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
				 setTimeout('fnc_receive_in_store_entry('+ reponse[1]+')',4000); 
			}
			else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				//if(reponse[4]){ alert("Receive Found Bundle List : "+reponse[3]+" This Bundle Not Any Change.");}
				show_msg(trim(reponse[0]));
				
				document.getElementById('txt_update_id').value = reponse[1];
				document.getElementById('txt_system_no').value = reponse[2];
				fnc_dtls_data_load( $('#txt_job_no').val(),reponse[1])
				set_button_status(1, permission, 'fnc_receive_in_store_entry',1,0);	
				release_freezing();
			}
			if(reponse[0]!=15)
			{
			  release_freezing();
			}
		}
	}
	
	function fnc_total_calculate(value,i)
	{
		var placeholder_value = $("#txtReceiveQty_"+i).attr('placeholder')*1;
		var pre_iss_qty = $("#txtReceiveQty_"+i).attr('pre_issue_qty')*1;
		var planCut = $("#planCutQty_"+i).text()*1;
		//alert(placeholder_value);
		if(((value*1)+pre_iss_qty)>planCut)
		{
			//alert("Qnty Excceded");
			var confirm_value=confirm("Receive qty Excceded by Plan Qty. (PCS). Press cancel to proceed otherwise press ok.");
			if(confirm_value!=0)
			{
				$("#txtReceiveQty_"+i).val('');
			}
						
			return;
		}
		fnc_total_calculate();
	}
	
	function fnc_total_calculate()
	{
		var tot_row=$('#tbl_details tr').length-1;
		//alert(rowCount)
		math_operation( "txtTotReceiveQty", "txtReceiveQty_", "+", tot_row );
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
					release_freezing();
				}
			}
		}
	}
	
	function fnc_dtls_data_load(job_no,uid)
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var list_view_orders = return_global_ajax_value( cbo_company_id+'***'+job_no+'***'+uid, 'order_details', '', 'requires/garments_receive_in_store_controller');
		if(list_view_orders!='')
		{
			//$("#tbl_details").html(list_view_orders);
			$("#tbl_details tr").remove();
			$("#tbl_details").prepend(list_view_orders);
		}
		
		setFilterGrid("tbl_details",-1);
		$('#txt_job_no').attr('disabled','disabled');
		$('#cbo_source').attr('disabled','disabled');
		//$('#cbo_company_name').attr('disabled','disabled');
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
		var working_company = $('#cbo_working_company').val();
		var location_name = $('#cbo_location').val();
		
		if(source==1 && type==1)
		{
			load_drop_down( 'requires/garments_receive_in_store_controller', company+'_'+1, 'load_drop_down_working_com', 'working_company_td' );
		}
		else if(source==3 && type==1)
		{
			load_drop_down( 'requires/garments_receive_in_store_controller', company+'_'+3, 'load_drop_down_working_com', 'working_company_td' );
		}
		else if(source==1 && type==2)
		{
			load_drop_down( 'requires/garments_receive_in_store_controller', working_company+'_'+2, 'load_drop_down_location', 'working_location_td' ); 
		} 
	}
	
	function openmypage_woNo()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var cbo_service_source = $('#cbo_source').val(); 
		var txt_job_no = $('#txt_job_no').val();

		if (form_validation('cbo_company_name*cbo_source*txt_job_no','LC. Company*Source*Job No')==false)
		{
			return;
		}
		else
		{			
			if (form_validation('cbo_company_name','cbo_source','txt_job_no','LC Company','Source','Job No')==false)
			{
				return;
			}	
			var page_link='requires/garments_receive_in_store_controller.php?company_id='+cbo_company_id+'&cbo_service_source='+cbo_service_source+'&txt_job_no='+txt_job_no+'&action=work_order_popup';
			var title='WO Number Popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320,height=370px,center=1,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];				
				var theemail=this.contentDoc.getElementById("txt_selected");
				if (theemail.value!="")
				{	  				
					var wo_data=(theemail.value).split("_");
					var wo_no=wo_data[1];
					var wo_id=wo_data[0];
					$('#txt_wo_id').val(wo_id);
					$('#txt_wo_no').val(wo_no);
					$('#txt_wo_no').attr('disabled',true);
				}
			}
		}
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;">
  	<? echo load_freeze_divs ("../../",$permission);  ?>
    <div style="width:1050px; float:left" align="center">
 		<fieldset style="width:1050px;">
            <legend>Garments Receive In Store Info.</legend>
            <form name="garmentsreceiveinstore_1" id="garmentsreceiveinstore_1" method="" autocomplete="off" >
                <fieldset>
                    <table width="100%">
                        <tr>
                            <td colspan="4" align="right"><b>RECEIVE NO : </b></td>
                            <td colspan="4"><input name="txt_system_no" id="txt_system_no" class="text_boxes" type="text" style="width:120px" onDblClick="openmypage_sysNo();" placeholder="Browse" readonly/>
                                <input name="txt_update_id" id="txt_update_id" type="hidden" />
                                <input name="txt_job_id" id="txt_job_id" type="hidden" />
                            </td>
                        </tr>
                        <tr>
                            <td width="100" class="must_entry_caption">Lc. Company</td>
                            <td width="140"><? echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select--", $selected, "load_drop_down( 'requires/garments_receive_in_store_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); fnc_load_party(1,document.getElementById('cbo_source').value);","" ); ?></td>
                            <td width="100" class="must_entry_caption">Lc. Com. Location</td>
                            <td width="140" id="location_td"><? echo create_drop_down( "cbo_location", 130, $blank_array,"", 1, "--Select Location--", $selected, "" ); ?></td>
                            <td width="100" class="must_entry_caption">Source</td>
                            <td width="140"><? echo create_drop_down( "cbo_source", 130, $knitting_source,"", 1, "--Select Source--", $selected, "fnc_load_party(1,this.value); dynamic_must_entry_caption(this.value);", 0, '1,3' ); ?></td>
                            <td width="100" class="must_entry_caption">W. Company</td>
                            <td id="working_company_td"><? echo create_drop_down( "cbo_working_company", 130, $blank_array,"", 1, "-W. Company-", $selected, "" ); ?></td>
                        </tr>
                        <tr>
                            <td id="locations">WC. Location</td>
                            <td id="working_location_td"><? echo create_drop_down( "cbo_working_location", 130, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                            <td id="floors">Floor</td>
                            <td id="floor_td"><? echo create_drop_down( "cbo_floor", 130, $blank_array,"", 1, "--Select Floor--", $selected, "" ); ?></td>
                            <td class="must_entry_caption">Job No</td>
                            <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:120px" onDblClick="openmypage_job('requires/garments_receive_in_store_controller.php?action=job_popup', 'Job/Order Selection Form');" placeholder="Browse" readonly /></td>
                            <td>Style Ref.</td>
                            <td><input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:120px" disabled /></td>
                        </tr>
                        <tr>
                        	<td>Buyer Name</td>
                            <td><? echo create_drop_down( "cbo_buyer_name", 130, "select id, buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "",1,0 ); ?></td>
                            <td class="must_entry_caption">Rec. Date</td>
                            <td><input type="text" name="txt_receive_date" id="txt_receive_date" value="<? echo date("d-m-Y")?>" class="datepicker" style="width:120px;"  /></td>
                            <td>Sending Location</td>
                            <td><? echo create_drop_down( "cbo_sending_location", 130, $sending_location,"id,location_name", 1, "-Select Sending Location-", $selected, "" ); ?></td>
                            <td>Challan No</td>
                            <td><input type="text" name="txt_challan" id="txt_challan" class="text_boxes" style="width:120px" /></td>
                        </tr>
                        <tr>
                        	<td>WO No</td> 
							<td><input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes"  style="width:120px;" placeholder="Browse/Write/scan" onDblClick="openmypage_woNo();" /><input type="hidden" id="txt_wo_id" value="0" /></td>
                            <td>Remarks</td>
                            <td><input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:120px" title="450 Characters Only." /></td>
							<td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </fieldset><br />
                    
                <fieldset style="width:1050px">
                    <legend>Garments Receive In Store Details List</legend>
                    <div>
                        <table cellpadding="0" width="1050" cellspacing="0" border="1" class="rpt_table" rules="all">
                            <thead>
                                <tr>
                                    <th width="30">SL</th>
                                    <th width="130">Order No</th>
                                    <th width="70">Pub. Shipment Date</th>
                                    <th width="120">Gmts. Item</th>
                                    <th width="120">Country</th>
                                    <th width="70">C. Shipment Date</th>
                                    <th width="140">Gmts. Color</th>
                                    <th width="80">Size</th>
                                    <th width="80">Po Qty. [PCS]</th>
                                    <th width="80">Plan Qty. [PCS]</th>
                                    <th>Receive Qty. [PCS]</th>
                                </tr>
                            </thead>
                        </table>
                        <div  style="width:1050px;max-height:250px;overflow-y:scroll"  align="left">    
                            <table cellpadding="0" width="1030" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_item_details">      
                                <tbody id="tbl_details">
                                	<tr bgcolor="#E9F3FF">
                                        <td width="30" align="center">1</td>
                                        <td width="130">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="120">&nbsp;</td>
                                        <td width="120">&nbsp;</td>
                                        <td width="70">&nbsp;</td>
                                        <td width="140">&nbsp;</td>
                                        <td width="80">&nbsp;</td>
                                        <td width="80">&nbsp;</td>
                                        <td width="80">&nbsp;</td>
                                        <td align="right">
                                        	<input type="text" name="txtReceiveQty_1" id="txtReceiveQty_1" class="text_boxes_numeric" style="width:70px;" value="" onBlur="fnc_total_calculate(this.value,1);" />
                                            <input type="hidden" name="txtpoid_1" id="txtpoid_1" style="width:30px" class="text_boxes" value="" />
                                            <input type="hidden" name="txtColorSizeid_1" id="txtColorSizeid_1" style="width:30px" class="text_boxes" value="" />
                						</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <table cellpadding="0" width="1050" cellspacing="0" border="1" class="rpt_table" rules="all">      
                            <tbody>
                                <tr class="tbl_bottom" name="tr_btm" id="tr_btm">
                                    <td width="30">&nbsp;</td>
                                    <td width="130">&nbsp;</td>
                                    <td width="70">&nbsp;</td>
                                    <td width="120">&nbsp;</td>
                                    <td width="120">&nbsp;</td>
                                    <td width="70">&nbsp;</td>
                                    <td width="140">&nbsp;</td>
                                    <td width="80">Total:</td>
                                    <td width="80"><input type="text" name="txtTotPoQty" id="txtTotPoQty" class="text_boxes_numeric" style="width:60px;" value="" readonly /></td>
                                    <td width="80"><input type="text" name="txtTotPlanQty" id="txtTotPlanQty" class="text_boxes_numeric" style="width:60px;" value=""readonly /></td>
                                    <td><input type="text" name="txtTotReceiveQty" id="txtTotReceiveQty" class="text_boxes_numeric" style="width:80px;" value="" readonly /></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <table cellpadding="0" cellspacing="1" width="1050">
                        <tr>
                            <td align="center" colspan="14" valign="middle" class="button_container">
                                <?
                                    $date=date('d-m-Y');
                                    echo load_submit_buttons( $permission, "fnc_receive_in_store_entry", 0,1 , "reset_form('garmentsreceiveinstore_1','','', 'txt_receive_date,".$date."','')",1); 
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
