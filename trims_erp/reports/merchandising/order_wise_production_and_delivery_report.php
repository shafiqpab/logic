<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Monthly capacity vs booked report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam 
Creation date 	: 	02-05-2019
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Monthly capacity vs booked report","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(report_type)
	{
		if(document.getElementById('cbo_company_id').value==0 || document.getElementById('txt_order_no').value=='' && document.getElementById('txt_style_ref').value=='' && document.getElementById('txt_buyer').value=='' &&  document.getElementById('txt_internal_no').value==''&& document.getElementById('cbo_wo_type_id').value==''){
			if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From date*To date')==false )
			{
				return;
			}
		}
        var action='';
        if(report_type==1){
            action='generate_report';
        }
        if(report_type==2){
            action='generate_report_2';
        }
        if(report_type==3){
            action='generate_report_3';
        }
		
		var data="action="+action+"&report_type="+report_type+"&report_title="+$( "div.form_caption" ).html()+get_submitted_data_string('cbo_company_id*cbo_location_name*cbo_customer_source*cbo_customer_name*txt_order_no*cbo_section_id*cbo_sub_section_id*txt_item_description*cbo_date_category*txt_date_from*txt_date_to*cbo_delivery_status*txt_internal_no*cbo_customer_buyer*cbo_team_leader*cbo_team_member*cbo_wo_type_id*txt_style_ref*txt_buyer',"../../../");
		freeze_window(3);
		http.open("POST","requires/order_wise_production_and_delivery_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{	 
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);  
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body_id",-1,'');
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body_id tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$('#table_body_id tr:first').show();
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
	}
	
	function fnc_amount_details(ids,rate,action)
    {
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_wise_production_and_delivery_report_controller.php?ids='+ids+'&rate='+rate+'&action='+action,'Amount Details', 'width=500px,height=320px,center=1,resize=0','../../');
        emailwindow.onclose=function()
        {
            
        }
    }
    function fnc_delivery_amount_details(ids,rate,action)
    {
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/order_wise_production_and_delivery_report_controller.php?ids='+ids+'&rate='+rate+'&action='+action,'Delivery Quantity Details', 'width=950px,height=320px,center=1,resize=0','../../');
        emailwindow.onclose=function()
        {
            
        }
    }
	
	
	function load_internall(id)
	{
		if(id==1)
		{
			$('#txt_internal_no').attr('disabled',false);
            $('#cbo_customer_buyer').attr('disabled',false);
		}
		else if (id==2 || id==0)
		{
			$('#txt_internal_no').attr('disabled',true);
			$('#txt_internal_no').val("");
            $('#cbo_customer_buyer').attr('disabled',true);
            $('#cbo_customer_buyer').val("");
		}
		
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?>
    <h3 style="width:1900px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <div id="content_search_panel" style="width:1900px"> 
         <form name="monthly_capacity_booked_1" id="monthly_capacity_booked_1" autocomplete="off" >    
            <fieldset>  
                <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="120" class="must_entry_caption">Company</th>
                        <th width="120">Location</th>
                        <th width="100">Order Source</th>
                        <th width="100">Customer Name</th>
                        <th width="100">Cust. Buyer</th>
                        <th width="100">Team Leader</th>
                        <th width="100">Team Member</th>
                        <th width="100">WO Type</th>
                        <th width="100">Work Order No</th>
                        <th width="100">Style Ref</th>
                        <th width="100">Buyer</th>
                        <th width="100">Internal Ref</th>
                        <th width="100">Section</th>
                        <th width="100">Sub Section</th>
                        <th width="140">Item Description</th>
                        <th width="150">Delivery Status</th>
                        <th width="100">Date Category</th>
                        <th colspan="2" id="th_date_caption">Order Receive Date</th>
                        <th colspan="3"><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('monthly_capacity_booked_1','','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <? 
									echo create_drop_down( "cbo_company_id",120,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/order_wise_production_and_delivery_report_controller', this.value, 'load_drop_down_location', 'location_td');get_php_form_data( this.value, 'company_wise_report_button_setting','requires/order_wise_production_and_delivery_report_controller');","","","","","",2);
                                ?>                            
                            </td>
                            <td id="location_td">
                                <?php 
                                    echo create_drop_down( 'cbo_location_name', 100, $blank_array, '', 1, '-- Select Location --', $selected, '', '', '', '', '', '', 2);
                                ?>
                            </td>
                            <td>
                                <? 
                                    $customer_source = array(1 => "Internal", 2 => "External");
									echo create_drop_down( "cbo_customer_source",100,$customer_source,"", 1, "--All--",0, "load_drop_down( 'requires/order_wise_production_and_delivery_report_controller', (document.getElementById('cbo_company_id').value+'_'+this.value), 'load_drop_down_buyer', 'buyer_td' );load_internall(this.value)","","","","","",2);
                                ?>
                            </td>
                            <td id="buyer_td">
                                <? 
									echo create_drop_down( "cbo_customer_name",100,$blank_array,"", 1, "--All--", $selected, "","","","","","",2);
                                ?>
                            </td>
                            <td >
                                <? 
                                echo create_drop_down( "cbo_customer_buyer", 100, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- Select --","", "",1,'','','','','','',"txtbuyer[]");   
                                ?>                                        
                            </td>
                            <td><? echo create_drop_down( "cbo_team_leader", 100, "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0 and project_type=3","id,team_leader_name", 1, "-- Select Leader --", $selected, "load_drop_down( 'requires/order_wise_production_and_delivery_report_controller', this.value+'_'+1, 'load_drop_down_member', 'member_td');"); ?>
                            </td>
                            <td id="member_td"><? echo create_drop_down( "cbo_team_member", 100,  $blank_array,"", 1, "-- Select Member --", $selected, "load_drop_down( 'requires/order_wise_production_and_delivery_report_controller', this.value+'_'+1, 'load_drop_down_member', 'member_td');"); ?></td>

                            <td><?php echo create_drop_down("cbo_wo_type_id", 150, $wo_type_arr, '', 1, '-Select-', 0, "", 0, '');?></td>
                            <td >
                                <input type="text" name="txt_order_no" id="txt_order_no" value="" class="text_boxes" style="width:100px;"/>                    							
                            </td>
                            <td >
                                <input type="text" name="txt_style_ref" id="txt_style_ref" value="" class="text_boxes" style="width:100px;" />                                              
                            </td>
                            <td >
                                <input type="text" name="txt_buyer" id="txt_buyer" value="" class="text_boxes" style="width:100px;" />                                              
                            </td>
                            <td >
                                <input type="text" name="txt_internal_no" id="txt_internal_no" value="" class="text_boxes" style="width:100px;" disabled/>                    							
                            </td>
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_section_id", 100, $trims_section,"", 1, "--All--", "", "" );
                                ?>
                            </td>
                           <td>
                                <? 
                                    echo create_drop_down( "cbo_sub_section_id", 100, $trims_sub_section,"", 1, "--All--", "", "" );
                                ?>
                           </td>
                            <td>
                                <input type="text" name="txt_item_description" id="txt_item_description" value="" class="text_boxes" style="width:130px;"/>                    							
                            </td>
                            <td> 
							    <?   
									echo create_drop_down( "cbo_delivery_status", 150, $delivery_status, "", 1, "--All--","", " $('#th_date_caption').html($('#cbo_date_category option:selected').text());", "", "");
                                ?>
                        	</td>
                            <td> 
							    <?   
                                    $date_cat=array(1=>"Order Receive Date",2=>"Target Delv Date",3=>"Actual Delv Date",4=>"Production Date");
									echo create_drop_down( "cbo_date_category", 100, $date_cat, "", 0, "--All--","", " $('#th_date_caption').html($('#cbo_date_category option:selected').text());", "", "");
                                ?>
                        	</td>
                            <td width="90">
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:80px;"/>                    							
                            </td>
                            <td width="90">
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:80px;"/>                        
                        	</td>
                        <td>
                            <input type="button" name="search" id="show" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                           
                        </td>
                        <td>
                            <input type="button" name="search" id="print_without_rate" value="Without Rate" onClick="generate_report(2)" style="width:80px" class="formbutton" />
                        </td>
                        <td>
                            <input type="button" name="search" id="summary" value="Summary" onClick="generate_report(3)" style="width:80px" class="formbutton" />
                        </td>
                        </tr>
                        <tr>
                        	<td colspan="14" align="center"><? echo load_month_buttons(1);  ?></td>
                        </tr>
                    <tbody>
                </table> 
            </fieldset>
            </form> 
            </div>
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div> 
            
    </div>
</body>  
<script>
    set_multiselect('cbo_delivery_status','0','0','','0');
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
