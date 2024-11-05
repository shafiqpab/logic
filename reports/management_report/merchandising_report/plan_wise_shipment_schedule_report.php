<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Page Will Create Line Wise Planning Report
Functionality	:	
JS Functions	:
Created by		:	Al-Hasan
Creation date 	: 	11-NOV-2023
Updated by 		: 		
Update date		: 	27-06-2021	   
QC Performed BY	:	Version 2 is developed by REZA. If any query plz contact me. cell: 01511100004	
QC Date			:	
Comments		:
*/

session_start();

extract($_REQUEST);


if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Line Wise Planning Report","../../../", 1, 1, $unicode,1,1);
?>	
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
	
	var tableFilters = 
	 {
		col_33: "none",
		col_operation: {
		id: ["total_order_qnty","total_order_qnty_in_pcs","value_tot_cm_cost","value_tot_cost","value_order","value_margin","value_tot_trims_cost","value_tot_embell_cost"],
	    col: [9,11,25,26,29,30,31,32],
	    operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
	    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	 }
	 
	
	
	function fn_report_generate(type)
	{
       
		// if( $('#cbo_company_id').val()!="" && $('#txt_search_by').val()!=''){
		// 	var dataField="cbo_company_id*txt_search_by";
		// 	var messageField="Company Name*Search by";
		// }
		// else if( $('#cbo_company_id').val()!="" && $('#txt_style_no').val()!=''){
		// 	var dataField="cbo_company_id*txt_style_no";
		// 	var messageField="Company Name*Style No";
		// }
		// else{
		// 	var dataField="cbo_company_id*txt_date_from*txt_date_to";
		// 	var messageField="Company Name*From Date*To Date";
		// }
		
		// if(form_validation(dataField,messageField)==false)
		// {
		// 	return;
		// }

        if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		else
		{	 
			var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_brand_name*txt_job_no*txt_order_no*cbo_ship_status*cbo_date_category*txt_date_from*txt_date_to',"../../../");
			freeze_window(3);
			http.open("POST","requires/plan_wise_shipment_schedule_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generate_reponse;
		}
	}
		
	function fn_report_generate_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			release_freezing(); 
			if(response[1] == 1 || response[1] == 2 || response[1] == 3 || response[1] == 4 || response[1] == 5 || response[1] == 6 || response[1] == 7)
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[2]+'" style="text-decoration:none"><input type="button" value="Excel Preview-ui" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview-8" name="Print" class="formbutton" style="width:100px"/>';
				release_freezing();
			}
			else
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				release_freezing();
			}
			if(response[1]==1)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			if(response[1]==2 || response[1]==3)
			{ 
				setFilterGrid("table_body",-1);
			}
			if(response[1]==6)
			{
				setFilterGrid("table_body",-1,tableFilters_3);
			}
			
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$(".flt").css("display","none");

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="400px";
        $(".flt").css("display","block");
	}
    // working
	function getCompanyID() 
	{
	    var company_id = document.getElementById('cbo_company_id').value;
	    if(company_id !='') {
		    var data="action=load_drop_down_buyers&data="+company_id;
		    http.open("POST","requires/plan_wise_shipment_schedule_report_controller.php",true); 
		    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		    http.send(data); 
		    http.onreadystatechange = function(){
				if(http.readyState == 4) 
				{
					var response = trim(http.responseText);
					$('#buyer_id').html(response);
					set_multiselect('cbo_buyer_id','0','0','','0');
					setTimeout[($("#buyer_id a").attr("onclick","disappear_list(cbo_buyer_id,'0');getBuyerID();") ,3000)]; 
					get_php_form_data(company_id,'print_button_variable_setting','requires/plan_wise_shipment_schedule_report_controller');
				}			 
	        };
	    }         
	}
    // working
	function getBuyerID() 
	{
	    var company_id = document.getElementById('cbo_company_id').value;
	    var buyer_id = document.getElementById('cbo_buyer_id').value; 
	    if(company_id !='') {
			var data="action=load_drop_down_brands&data="+company_id+'_'+buyer_id;
			http.open("POST","requires/plan_wise_shipment_schedule_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data); 
			http.onreadystatechange = function(){
				if(http.readyState == 4) 
				{
					var response = trim(http.responseText);
					$('#brand_id').html(response);
					set_multiselect('cbo_brand_name','0','0','','0');
				}			 
			};
	    }         
	}
    // working job no.
    function openmypage_jobno()
	{ 
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		} 
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var page_link='requires/plan_wise_shipment_schedule_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=730px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_no=this.contentDoc.getElementById("hide_job_no").value;
			var response=trim(style_no).split("_");
			$('#txt_job_no').val(response[1]);
		}
	}
    // working order no.
    function openmypage_orderno()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
        }
		var companyID = $("#cbo_company_id").val();
		// alert(companyID);
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var page_link='requires/plan_wise_shipment_schedule_report_controller.php?action=orderno_popup&companyID='+companyID+'&buyer='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Order No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=580px,height=390px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			// var theform=this.contentDoc.forms[0];
			// var style_no=this.contentDoc.getElementById("hide_style_no").value;
			// var style_id=this.contentDoc.getElementById("hide_style_id").value;
			// $('#txt_style_no').val(style_no);
			// $('#txt_style_id').val(style_id);

			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			var prodDescription=this.contentDoc.getElementById("txt_selected").value;  
			$("#txt_order_no").val(prodDescription);
			$("#txt_order_id").val(prodID);

		}
	}
</script>
</head>
 
<body onLoad="set_hotkey();">
<form id="cost_breakdown_rpt">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1030px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel"> 
            <fieldset style="width:1030px;">
                <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                	<thead>
                    	<tr>                 
                            <th class="must_entry_caption">Company Name</th>
                            <th class="">Buyer</th>
                            <th class="">Brand</th>
                            <th id="td_search_by">Job No</th>
                            <th>Order</th>
                            <th>Shipment Status</th>
                            <th>Date Category</th>
                            <th class="must_entry_caption" id="td_date_caption" colspan="3">Plan Date</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                        </tr>
                     </thead>
                    <tbody>
                    <tr class="general" id="company_td">
                        <td> 
                            <?= create_drop_down("cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 0, "-- Select Company --", $selected, "load_drop_down( 'requires/plan_wise_shipment_schedule_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
                            ?>
                        </td>
                        <td id="buyer_id">
                            <?= create_drop_down("cbo_buyer_id", 120, "select id,buyer_name from lib_buyer where status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "");
                            ?>
                        </td>
                        <td id="brand_id">
                            <?= create_drop_down("cbo_brand_name", 120, "select id,brand_name from lib_buyer_brand where status_active=1 order by brand_name","id,brand_name", 1, "-- Select Buyer --", $selected, "");
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" onDblClick="openmypage_jobno();" placeholder="Browse" class="text_boxes" style="width:80px"/>
                            <input type="hidden" id="txt_job_id" name="txt_job_id"/>
                        </td>
                        <td>
                            <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px"  onDblClick="openmypage_orderno();" placeholder="Browse"/>
                            <input type="hidden" id="txt_order_id" name="txt_order_id"/>
                        </td>
                        <td>
                            <?
                            $shipStatus_arr=array(1=>'Full Pending',2=>'Partial Delivery',3=>'Full Delivery/Closed');
							echo create_drop_down( "cbo_ship_status", 100, $shipStatus_arr,"", 0, "-- All --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td>
                            <?
                            $dateCategory_arr=array(1=>'Pub Ship Date',2=>'Org. Ship Date',3=>'Country Ship Date',4=>'PHD/PCD Date',5=>'Plan Date');
							echo create_drop_down( "cbo_date_category", 100, $dateCategory_arr,"", 0, "-- All --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" ></td>
                        <td>To</td>
                        <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date" ></td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="fn_report_generate(1)" style="width:60px" class="formbutton"/> 
                        </td>
                        <!-- <td id="button_data_panel"></td> -->
                    </tr>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td>
                            <?= load_month_buttons(1);?>
                        </td>
                    </tr>
                </table> 
            </fieldset>
        </div>
    </div>
    <br>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>

<script>
	set_multiselect('cbo_company_id','0','0','0','0');	
	set_multiselect('cbo_buyer_id','0','0','','0');
	set_multiselect('cbo_brand_name','0','0','','0');
	set_multiselect('cbo_ship_status','0','0','','0');
	setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyID();") ,3000)]; 
	setTimeout[($("#buyer_id a").attr("onclick","disappear_list(cbo_buyer_id,'0');getBuyerID();") ,3000)]; 
	// $('#cbo_location').val(0);
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>