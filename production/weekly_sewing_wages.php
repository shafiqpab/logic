<?
/*-------------------------------------------- Comments
Purpose			: 	This form created Weekly Sewing wages

Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza
Creation date 	: 	04.02.2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	: Samd, Rana, Sujon ,Nasir		
QC Date			:	
Comments		:
*/ 

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//-----------------------------------------------------------------------------------------------------
echo load_html_head_contents("Weekly Sewing Wages Entry Info","../", 1, 1, "",'1','');
?>

<script>

	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	
	function set_exchange_rate(currence)
	{	// 1 for TK.
		if(currence==1)
		{
			$('#txt_exchange_rate').val(1);
			$('#txt_exchange_rate').attr('readonly', 1);
		}
		else
		{
			$('#txt_exchange_rate').val('');
			$('#txt_exchange_rate').removeAttr("readonly");
		}
	}
	
	
	
	function popuppage_system_id()
	{ 
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var cbo_company_id = $('#cbo_company_id').val();
			var title = 'System ID Info';
			var page_link = 'requires/weekly_sewing_wages_controller.php?cbo_company_id='+cbo_company_id+'&action=systemId_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var mst_id=this.contentDoc.getElementById("hidden_mst_id").value;
				
				mst_id=mst_id.split("***");
				
				reset_form('','','txt_order_no*txt_order_id*hidden_order_break_down*txt_cutting_bill_qty*txt_wo_rate*txt_amount*tex_emp_deduct_amount*txt_net_amount*txt_previous_bill_qty*txt_yet_to_bill_qty*cbo_wages_rate_variables*txt_appv_wo_order_qty*txt_gmt_item_name*txt_gmt_item_id*cbo_buyer_name*txt_style_ref_no');
				
				
				get_php_form_data(mst_id[0], "populate_sewing_wages_mst_form_data", "requires/weekly_sewing_wages_controller" );
				$("#txt_system_id").val(mst_id[1]);
				show_list_view(mst_id[0],'show_weekly_wages_bill_listview','list_container','requires/weekly_sewing_wages_controller','');
			//set_button_status(1, permission, 'fnc_weekly_wages_bill',1);
				disable_enable_fields('cbo_company_id*cbo_final_bill*cbo_location*cbo_division*cbo_department*cbo_shift*cbo_floor_name*txt_week_date_from*txt_week_date_to',1);
			}
		}
	}
	
	
	
	
	function openpage_emp_id()
	{
		if (form_validation('cbo_company_id*cbo_bill_for','Company*Bill for')==false)
		{
			return;
		}
		else
		{
			var cbo_company_id = $('#cbo_company_id').val();
			var cbo_bill_for = $('#cbo_bill_for').val();
			var title = 'Employee Info';
			var page_link = 'requires/weekly_sewing_wages_controller.php?cbo_company_id='+cbo_company_id+'&cbo_bill_for='+cbo_bill_for+'&action=employee_info_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=580px,height=390px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var emp_id=this.contentDoc.getElementById("hidden_emp_id").value;
				get_php_form_data(emp_id, "populate_emp_info", "requires/weekly_sewing_wages_controller");
			}
		}
	}
	
	function openpage_search_order()
	{
		
		if (form_validation('cbo_company_id*cbo_bill_for*cbo_final_bill*cbo_location*cbo_line_num*txt_week_date_from*txt_emp_name','Company*Bill for*Finall bill*Location*Line*Week Date*Service Provider')==false)
		{
			return;
		}
		else
		{
			var cbo_company_id = $('#cbo_company_id').val();
			var cbo_location = $('#cbo_location').val();
			var cbo_floor_name = $('#cbo_floor_name').val();
			var cbo_line_num = $('#cbo_line_num').val();
			var cbo_bill_for = $('#cbo_bill_for').val();
			var cbo_final_bill = $('#cbo_final_bill').val();
			var txt_week_date_from = $('#txt_week_date_from').val();
			var txt_week_date_to = $('#txt_week_date_to').val();
			var cbo_location = $('#cbo_location').val();
			var cbo_shift = $('#cbo_shift').val();
			var txt_emp_code = $('#txt_emp_code').val();
			var txt_order_id = $('#txt_order_id').val();
			var txt_gmt_item_id = $('#txt_gmt_item_id').val();
			var dtls_update_id = $('#dtls_update_id').val();
			
			var title = 'Order Info';
			var page_link = 'requires/weekly_sewing_wages_controller.php?action=order_info_popup&mst_data='+cbo_company_id+'_'+cbo_bill_for+'_'+txt_week_date_from+'_'+txt_week_date_to+'_'+cbo_location+'_'+cbo_line_num+'_'+cbo_shift+'_'+txt_emp_code+'&txt_order_id='+txt_order_id+'&txt_gmt_item_name='+txt_gmt_item_id;

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=950px,height=390px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var hidden_order_id=this.contentDoc.getElementById("hidden_order_id").value;
				var hidden_job_id=this.contentDoc.getElementById("hidden_job_id").value;
				var hidden_rate=this.contentDoc.getElementById("hidden_rate").value;
				var hidden_rate_var_id=this.contentDoc.getElementById("hidden_rate_var_id").value;
				var hidden_style_ref=this.contentDoc.getElementById("hidden_style_ref").value;
				var hidden_item_id=this.contentDoc.getElementById("hidden_item_id").value;
				
				if(dtls_update_id!=''){
					var ic=confirm('This Bill Will Be Deleted?');
					}
				
				var data=hidden_order_id+'**'+hidden_job_id+'**'+hidden_rate+'**'+hidden_rate_var_id+'**'+hidden_style_ref+'**'+hidden_item_id+'**'+cbo_company_id+'**'+cbo_location+'**'+cbo_floor_name+'**'+cbo_line_num+'**'+cbo_bill_for+'**'+cbo_final_bill+'**'+txt_week_date_from+'**'+txt_week_date_to+'**'+txt_emp_code+'**'+dtls_update_id;
				get_php_form_data(data, "populate_order_data", "requires/weekly_sewing_wages_controller");
			}
		}
	}


	function generate_report_file(data,action)
	{
		window.open("requires/weekly_sewing_wages_controller.php?data=" + data+'&action='+action, true );
	}

	
	function fnc_weekly_sewing_wages_entry(operation)
	{ 

		if(operation==4)
		{
			// var report_title=$( "div.form_caption" ).html();
			 generate_report_file($('#update_id').val(),'price_rate_wo_print');
			 return;
		}
		if(operation==0 || operation==1 || operation==2)
		{
	

			if( form_validation('cbo_company_id*cbo_bill_for*cbo_final_bill*cbo_location*txt_week_date_from*txt_week_date_to*txt_emp_name*txt_order_no*cbo_line_num','Company*Bill For* Final Bill*Location*From Date*To Date*Emp Card No*Order No*Line')==false )
			{
				return;
			}	
	
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*update_id*dtls_update_id*cbo_company_id*cbo_bill_for*cbo_final_bill*cbo_location*cbo_floor_name*cbo_line_num*cbo_division*cbo_department*cbo_shift*txt_week_date_from*txt_week_date_to*txt_emp_code*txt_emp_name*txt_order_id*cbo_buyer_name*txt_style_ref_no*txt_gmt_item_name*txt_appv_wo_order_qty*cbo_wages_rate_variables*txt_cutting_bill_qty*txt_deducted_qty_hidden*hidden_order_break_down*txt_wo_rate*txt_amount*tex_emp_deduct_amount*txt_net_amount*txt_previous_bill_qty*txt_yet_to_bill_qty*cbo_cutting_bill_uom*cbo_wo_rate_uom*cbo_previous_bill_qty_uom*cbo_yet_to_bill_qty_uom',"../");
			
			freeze_window(operation);
			http.open("POST","requires/weekly_sewing_wages_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_weekly_sewing_wages_entry_reponse;
		}
	}
	
	
	function fnc_weekly_sewing_wages_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				show_list_view(reponse[1],'show_weekly_wages_bill_listview','list_container','requires/weekly_sewing_wages_controller','');
				set_button_status(0, permission, 'fnc_weekly_sewing_wages_entry',1);
				
				disable_enable_fields('cbo_company_id*cbo_final_bill*cbo_location*cbo_division*cbo_department*cbo_shift*cbo_floor_name*txt_week_date_from*txt_week_date_to',1);
				reset_form('','','txt_order_no*txt_order_id*hidden_order_break_down*txt_cutting_bill_qty*txt_wo_rate*txt_amount*tex_emp_deduct_amount*txt_net_amount*txt_previous_bill_qty*txt_yet_to_bill_qty*cbo_wages_rate_variables*txt_appv_wo_order_qty*txt_gmt_item_name*txt_gmt_item_id*cbo_buyer_name*txt_style_ref_no');

			$('#dtls_update_id').val('');
			}
			
			
			release_freezing();
		}
	}



	function calculate_date()
	{		
		var thisDate=($('#txt_week_date_from').val()).split('-');
		var in_date=thisDate[2]+'-'+thisDate[1]+'-'+thisDate[0];
		//var days=($('#days_required').val())-1;
		var days=5;
		var date = add_days(in_date,days);	
		var split_date=date.split('-');			
		var res_date=split_date[0]+'-'+split_date[1]+'-'+split_date[2];
		$('#txt_week_date_to').val(res_date);
	}
		
	function fnc_enable(){
					disable_enable_fields('cbo_company_id*cbo_final_bill*cbo_location*cbo_division*cbo_department*cbo_shift*cbo_floor_name*txt_week_date_from*txt_week_date_to',0);
	set_button_status(0, permission, 'fnc_weekly_wages_bill_entry',1);	
	}
	
	function fnc_net_amount(deducted){
		var amount=$('#txt_amount').val()*1;	
		$('#txt_net_amount').val(amount-deducted);	
	}
</script>


	<?
        $company_arr = return_library_array("select id, company_name from lib_company order by company_name","id","company_name");
        $location_details = return_library_array("select id,location_name from lib_location order by location_name","id","location_name");
        $division_details = return_library_array("select id,division_name from lib_division order by division_name","id","division_name");
        $department_details = return_library_array("select id,department_name from lib_department order by department_name","id","department_name");
    ?>	
    


</head>
<body onLoad="set_hotkey()">
<div style="width:850px; margin:0 auto;">

<? echo load_freeze_divs ("../",$permission); ?>
    <form name="weekly_wages_bill_1" id="weekly_wages_bill_1" autocomplete="off" >
        <fieldset>
        <legend>&nbsp; Weekly Sewing Wages</legend>
     <fieldset>
    <div style=" width:800px;">   
    <table cellpadding="0" cellspacing="2" width="100%">
           <tr>
                <td colspan="3" align="right">System ID</td>
                <td colspan="4" align="left">
                <input type="hidden" id="update_id" name="update_id" />
                <input type="hidden" id="dtls_update_id" name="dtls_update_id" />
                    <input name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px" placeholder="Double Click to Search" readonly value="" onDblClick="popuppage_system_id('search_cutting_wages_system_id.php','Cutting Wages Info'); return false" tabindex="12" />
                    <input type="hidden" id="cbo_bill_for" name="cbo_bill_for" value="30"/>
              </td>
           </tr>
           
           <tr>
                <td colspan="7">&nbsp;</td>
           </tr>
           
            <tr>
                <td width="130" class="must_entry_caption" align="right">Company Name</td>
                <td width="170">
					<?
                        echo create_drop_down( "cbo_company_id", 160, $company_arr,"", 1, "--Select Company--", 0, "load_drop_down('requires/weekly_sewing_wages_controller', this.value, 'load_drop_down_location', 'location_td' );","","" );
                    ?>
                </td>
               <td width="130" class="must_entry_caption" align="right">Final Bill</td>
                <td width="170">
					<?
                        echo create_drop_down( "cbo_final_bill", 160, $yes_no,"", 1, "--Select Final Bill--", 2, "");
                    ?>
              </td>	
              <td width="130" class="must_entry_caption" align="right">Location</td>
              <td width="170" id="location_td">
					<?
                        echo create_drop_down( "cbo_location", 160, $location_details,"", 1, "--Select Location--", 0, "",1 );
                    ?>
              </td>
           </tr>
           <tr>
                <td width="130" align="right">Division Name</td>
                <td width="170">
					<?
                        echo create_drop_down( "cbo_division", 160, $division_details,"", 1, "--Select Division--", 0, "" );
                    ?>
                </td>
                <td width="130" align="right">Department Name</td>
                <td width="170">
					<?
                        echo create_drop_down( "cbo_department", 160, $department_details,"", 1, "--Select Department--", 0, "" );
                    ?>
                </td>
                <td width="130" align="right">Shift</td>
                <td width="170">
					<?
                        echo create_drop_down( "cbo_shift", 160, $shift_name,"", 1, "--Select Shift--", 1, "" );
                    ?>
                </td>
            </tr>
            
            
            <tr>
                <td width="130" align="right">Floor</td>
                <td width="170" id="floor_td">
                    <? 
						echo create_drop_down( "cbo_floor_name", 160,"select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0","id,floor_name", 1, "-- Select Floor --", $selected, "",1 );
					
					
					
					?>
                </td>
                <td width="130" class="must_entry_caption" align="right">Week From</td>
                <td width="170">
                    <input type="text" name="txt_week_date_from" id="txt_week_date_from" class="datepicker" style="text-align:center;width:150px" onChange="calculate_date()" readonly />
               </td>
                <td width="130" class="must_entry_caption" align="right">Week To</td>
                <td width="170">
                    <input type="text" name="txt_week_date_to" id="txt_week_date_to" class="datepicker" style="text-align:center;width:150px" readonly />
                </td>

            </tr>
        </table>
        </div>
        </fieldset>
      
      
        <fieldset>
        <legend>&nbsp;  New Entry</legend>
        
      <table cellpadding="0" cellspacing="2" width="850" border="0">
						<tr>
							<td width="140" align="right">Line</td>
                            <td width="170" id="line_td">
                                <?
                                    echo create_drop_down( "cbo_line_num", 160, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "--- Select ---", $selected, "",0,0 );
                                ?>
                           </td>
                            <td width="130" align="right">Order No</td>
							<td width="470" colspan="4">
                            	<input name="txt_order_no" placeholder="Double Click to Search" id="txt_order_no" onDblClick="openpage_search_order(); return false"  class="text_boxes" style="width:473px;" readonly>
                                <input type="hidden" name="txt_order_id" id="txt_order_id">
                                <input type="hidden" name="hidden_order_break_down" id="hidden_order_break_down">
                            </td>
						</tr>
                        				
						<tr>
                        	<td width="140" align="right">Service Prov</td>
                            <td width="170">
                                <input type="hidden" name="txt_emp_card_no" id="txt_emp_card_no"/>
                                <input type="hidden" name="txt_emp_code" id="txt_emp_code"/>
                                <input type="text" name="txt_emp_name" id="txt_emp_name" class="text_boxes" style="width:150px"  tabindex="13" onDblClick="openpage_emp_id()" placeholder="Double Click to Search" readonly />
                               
                               
                            </td>
                            <td width="130" align="right">Buyer</td>
                            <td width="170">
								<?
                                    echo create_drop_down( "cbo_buyer_name", 160, "select buyer_name,id from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name","id,buyer_name", 1, "--Select Buyer--", 0, "",1 );
                                ?>
                            </td>
                            <td width="120" align="right">Sewing/Bill Qty</td>
							<td width="100">
                            	<input type="text" name="txt_cutting_bill_qty" id="txt_cutting_bill_qty" class="text_boxes" style="width:107px; text-align:right" tabindex="22"  disabled />
                                <input type="hidden" id="txt_deducted_qty_hidden" name="txt_deducted_qty_hidden"/>
                            </td>
                            <td width="60">
								<?
                                    echo create_drop_down( "cbo_cutting_bill_uom", 60, $unit_of_measurement,"",1, "--Select--", "","",1,"1,2" );
                                ?>
                            </td>                  
						</tr>
						<tr>
                        	<td rowspan="8" colspan="2" valign="top">
                                <fieldset style="margin-top:5px;">
                                    <div id="display_prv_bill_history" style="height:100px;">Note:</div>
                                </fieldset>
							</td>
                            <td width="130" align="right">Style Ref. No</td>
							<td width="170">
                            	<input type="text" name="txt_style_ref_no" id="txt_style_ref_no" class="text_boxes" style="width:150px" disabled tabindex="18"  />
                            </td>
                            <td width="120" align="right">WO Rate</td>
							<td width="100">
                                <input type="text" name="txt_wo_rate" id="txt_wo_rate" class="text_boxes_numeric" tabindex="23" style="width:107px;" disabled />
                            </td>
                            <td width="60">
								<?
                                    echo create_drop_down( "cbo_wo_rate_uom", 60,$unit_of_measurement,"",1, "--Select--", "","",1,"1,2" );
                                ?>
                            </td> 
                    	</tr>
                        <tr>
                            <td width="130" align="right">Gmt. Item Name</td>
							<td width="170">
                            	<input type="text" name="txt_gmt_item_name" id="txt_gmt_item_name" class="text_boxes" style="width:150px" disabled tabindex="18"  />
                                <input type="hidden" name="txt_gmt_item_id" id="txt_gmt_item_id"   class="text_boxes" style="width:140px ">
                            </td>
                            <td width="120" align="right">Amount</td>
                            <td width="110">
                            	<input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" tabindex="24" style="width:107px;" disabled />
								<input type="hidden" id="txt_deducted_amount_hidden" name="txt_deducted_amount_hidden"/>
                            </td>
                            <td>&nbsp;</td>
                    	</tr>
                        
                        <tr>
                           	<td width="130" align="right">Apv. Order Qty.</td>
							<td width="170">
                            	<input type="text" name="txt_appv_wo_order_qty" id="txt_appv_wo_order_qty" class="text_boxes" style="width:150px" disabled tabindex="18"  />
                                <input type="hidden" name="txt_appv_wo_order_qty_hidden" id="txt_appv_wo_order_qty_hidden" class="text_boxes" style="width:150px" disabled tabindex="18"  />
                            </td>
                            <td align="right">Deduct</td>
                            <td><input type="text" name="tex_emp_deduct_amount" id="tex_emp_deduct_amount" class="text_boxes_numeric" style="width:107px" onKeyUp="fnc_net_amount(this.value)"/></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="130" align="right">Rate Variables</td>
                            <td width="170" id="item_group_td_id">
								<?
                                    echo create_drop_down( "cbo_wages_rate_variables", 160,$color_type,"", 1, "--Rate Variable--", 0, "" ,1);
                                ?>
                            </td>
                            <td align="right">Net Amount</td>
                            <td><input type="text" name="txt_net_amount" id="txt_net_amount" class="text_boxes_numeric" style="width:107px;" disabled /></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                           	<td width="130" align="right"></td>
							<td width="170"></td>
                            <td width="120" align="right">Previous Bill Qty.</td>
                            <td width="100">
                            	<input type="text" name="txt_previous_bill_qty" id="txt_previous_bill_qty" class="text_boxes" tabindex="24" style="width:107px;text-align:right " disabled />
                                <input type="hidden" name="txt_previous_bill_qty_hidden" id="txt_previous_bill_qty_hidden" class="text_boxes" tabindex="24" style="width:107px;text-align:right " disabled />
                            </td>
                            <td width="60">
								<?
                                    echo create_drop_down( "cbo_previous_bill_qty_uom",60,$unit_of_measurement,"",1, "--Select--", "","",1,"1,2" );
                                ?>
                            </td>
                    	</tr>		
                        <tr>
                            <td width="130" align="right"></td>
                            <td width="170"></td>
                            <td width="120" align="right">Yet To Bill Qty.</td>
                            <td width="100">
                            	<input type="text" name="txt_yet_to_bill_qty" id="txt_yet_to_bill_qty" class="text_boxes" tabindex="24" style="width:107px;text-align:right " disabled />
                                <input type="hidden" name="txt_yet_to_bill_qty_hidden" id="txt_yet_to_bill_qty_hidden" class="text_boxes" tabindex="24" style="width:107px;text-align:right " disabled />
                            </td>
                            <td width="60">
								<?
                                    echo create_drop_down( "cbo_yet_to_bill_qty_uom", 60,$unit_of_measurement,"",1, "--Select--", "","",1,"1,2" );
                                ?>
                            </td>
                        </tr>
                        <tr><td colspan="7" height="8"></td></tr>
                		<tr>
                    		<td colspan="7" align="center" class="button_container">
								<?
                                    echo load_submit_buttons($permission, "fnc_weekly_sewing_wages_entry", 0,1,"reset_form('weekly_wages_bill_1','list_container','','','fnc_enable()','cbo_bill_for')",1);
                                ?>
                    		</td>
                 		</tr>
					</table>        
        </fieldset>      
        <div id="list_container"></div>
		</fieldset>
   
	</form>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>