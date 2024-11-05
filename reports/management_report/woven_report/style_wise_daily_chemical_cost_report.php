<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create  Style Wise Daily Chemical Cost Report.
Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	23-02-2021
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

echo load_html_head_contents("Style Wise  Daily Chemical Cost  Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

	function fn_report_generated(type)
	{
			
		var txt_style=document.getElementById('txt_style').value;
		var txt_styleId=document.getElementById('txt_style_id').value;
		var txt_job_id=document.getElementById('txt_job_id').value;
		var txt_job=document.getElementById('txt_job').value;
	
	 
		var company_name=document.getElementById('cbo_company_name').value;
		var cbo_party_name=document.getElementById('cbo_party_name').value;
		 var buyer_customer=document.getElementById('cbo_buyer_party_name').value+'_'+ $('#cbo_buyer_party_name option:selected' ).text();
		 //alert(buyer_customer);
//return;
		if (type==1)
		{
			if(txt_style!="" || txt_job!="")
			{
				if(form_validation('cbo_company_name','Company')==false)
				{
					return;
				}
			}
			else
			{
				if(form_validation('cbo_company_name*txt_style','Company*Style')==false)
					{
						return;
					}
			}
		}
		
		//var sign=1;
		var report_title=$("div.form_caption" ).html();
		var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_party_name*cbo_within_group*txt_style_id*txt_style*txt_job*txt_job_id*cbo_buyer_party_name*cbo_party_name*txt_job_id*txt_job',"../../../")+'&report_title='+report_title+'&buyer_customer='+buyer_customer;
		//alert(data);return;
		freeze_window(3);
		if(type==1)
		{
			http.open("POST","requires/style_wise_daily_chemical_cost_report_controller.php",true);
		}

		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("****");
			$('#report_container2').html(reponse[0]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1);" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

				var tableFilters = {
					col_operation: {
					id: ["value_total_fab_qnty","value_total_trims_amount"],
					col: [9,11],
					operation: ["sum","sum"],
					write_method: ["innerHTML","innerHTML"]
					}
				}
				setFilterGrid("table_body_accss",-1,tableFilters);
				/*
				setFilterGrid("table_body1",-1);
				setFilterGrid("table_body2",-1);
				setFilterGrid("table_body3",-1);
				setFilterGrid("table_body4",-1);
				setFilterGrid("table_body5",-1);
				setFilterGrid("table_body6",-1);
				setFilterGrid("table_body7",-1);
			*/

			//append_report_checkbox('table_header_1',1);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',580,700 );
			release_freezing();
			show_msg('3');
		}
	}

	function new_window(type)
	{

		$('.scroll_div_inner').css('overflow','auto');
		$('.scroll_div_inner').css('maxHeight','none');
		$("#table_body_accss tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	 '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$('.scroll_div_inner').css('overflow','scroll');
		$('.scroll_div_inner').css('maxHeight','480px');
		$("#table_body_accss tr:first").show();
	}



	function openmypage_quotation()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var buyer = $("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();
		var txt_season_id = $("#txt_season_id").val();
		var txt_season = $("#txt_season").val();

		var page_link='requires/style_wise_daily_chemical_cost_report_controller.php?action=quotation_popup&companyID='+company+'&buyer_name='+buyer+'&txt_season_id='+txt_season_id+'&txt_season='+txt_season+'&cbo_year='+cbo_year;
		var title="Search Quotation Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			$("#txt_quotation_id").val(order_no);
			$("#txt_hidden_quot_id").val(order_id);

		}
	}
	function openmypage_style()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var buyer = $("#cbo_buyer_name").val();
	//	var cbo_year = $("#cbo_year").val();
		var txt_style_ref_no = $("#txt_style_ref_no").val();
		var txt_style_ref_id = $("#txt_style_ref_id").val();
		var txt_style_ref = $("#txt_style_ref").val();
		var page_link='requires/style_wise_daily_chemical_cost_report_controller.php?action=style_refarence_search&companyID='+company+'&buyer_name='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_style_ref").val(style_des);
			$("#txt_style_ref_id").val(style_id);
			$("#txt_style_ref_no").val(style_no);
		}
	}

	 

	function search_populate(str)
	{
		if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Shipment Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==2)
		{
			document.getElementById('search_by_th_up').innerHTML="PO Received Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="PO Insert Date";
			$('#search_by_th_up').css('color','blue');
		}
	}

	 
	function generate_worder_report3(cbo_company_name,update_id,print_id,type,i)
	{
		var report_title='Lab Test Work Order';
		print_report(cbo_company_name+'*'+update_id+'*'+0+'*'+0+'*'+report_title, "show_trim_booking_report","../../../order/woven_order/requires/labtest_work_order_controller" ) ;
	}

	 
	function fnc_load_party(type,within_group)
	{
		
		// alert(within_group +'='+ type);
		$("#txt_style").val('');
		$("#txt_style_id").val('');
		$("#txt_job").val('');
		$("#txt_job_id").val('');
		if(type==1)
		{
		$("#cbo_party_name").val(0);
		$("#cbo_buyer_party_name").val(0);
		}
		
		
		if ( form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		
		// alert(within_group);
		//$('#txtOrderDeliveryDate_1').val($('#txt_delivery_date').val());
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		if(within_group==1 && type==1)
		{
			load_drop_down( 'requires/style_wise_daily_chemical_cost_report_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
			//var party_name = $('#cbo_party_name').val();
			//var length=$("#cbo_party_name option").length;
			//alert(length);
			
			//load_drop_down( 'requires/style_wise_daily_chemical_cost_report_controller', company+'_'+1, 'load_drop_down_customer_buyer', 'buyer_customer_td' );
			$("#cbo_buyer_party_name").attr("disabled",true);
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/style_wise_daily_chemical_cost_report_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
			//var party_name = $('#cbo_party_name').val();
			//var length=$("#cbo_party_name option").length;
			//alert(length+'='+3);
		//	load_drop_down( 'requires/style_wise_daily_chemical_cost_report_controller', company+'_'+2, 'load_drop_down_customer_buyer', 'buyer_customer_td' );
			//cbo_buyer_party_name;
			$("#cbo_buyer_party_name").val(0);
			$("#cbo_buyer_party_name").attr("disabled",true);
		}
		if(type==2)
		{
			load_drop_down( 'requires/style_wise_daily_chemical_cost_report_controller', company+'_'+2, 'load_drop_down_customer_buyer', 'buyer_customer_td' );
		}
	}
	function openmypage_job(type)
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var cbo_party_name = $("#cbo_party_name").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		var cbo_within_group = $("#cbo_within_group").val();
		var txt_job_id = $("#txt_job_id").val();
		var txt_job = $("#txt_job").val();
		
		var txt_style_id = $("#txt_style_id").val();
		var txt_style = $("#txt_style").val();
		 var buyer_customer=document.getElementById('cbo_buyer_party_name').value+'_'+ $('#cbo_buyer_party_name option:selected' ).text();
		 
		//txt_job_id txt_job_sl
		var page_link='requires/style_wise_daily_chemical_cost_report_controller.php?action=job_no_popup&companyID='+companyID+'&cbo_party_name='+cbo_party_name+'&cbo_year_id='+cbo_year_id+'&cbo_within_group='+cbo_within_group+'&txt_job_id='+txt_job_id+'&txt_job='+txt_job+'&type='+type+'&buyer_customer='+buyer_customer;
		 
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=370px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
		//	var job_sl=this.contentDoc.getElementById("hide_job_sl").value;
			//$('#txt_job').val(job_no);
			//$('#txt_job_id').val(job_id);
			//var style_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			//var style_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			//var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			if(type==1)
			{
				$("#txt_style").val(job_no);
				$("#txt_style_id").val(job_id); 
			}
			else
			{
			$("#txt_job").val(job_no);
			$("#txt_job_id").val(job_id); 
			}
			//$("#txt_job_sl").val(job_sl);	 
		}
	}




</script>
<style>
 /* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

 /* Modal Header */
.modal-header {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Body */
.modal-body {padding: 2px 16px;}

/* Modal Footer */
.modal-footer {
    padding: 2px 16px;
    background-color: #999;
    color: white;
}

/* Modal Content */
.modal-content {
    position: relative;
    background-color: #fefefe;
    margin: auto;
    padding: 0;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
    -webkit-animation-name: animatetop;
    -webkit-animation-duration: 0.4s;
    animation-name: animatetop;
    animation-duration: 0.4s
}

/* Add Animation */
@-webkit-keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

@keyframes animatetop {
    from {top: 300px; opacity: 0}
    to {top: 0; opacity: 1}
}

/* The Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
</style>
</head>
<body onLoad="set_hotkey();">
<form id="costSheetReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
          <h3 align="left" id="accordion_h1" style="width:900px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:900px;" id="content_search_panel">
            <table class="rpt_table" width="900" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>
                    <th class="must_entry_caption">Working Company</th>
                    <th>Within Group</th>
                    <th>Customer</th>
                     <th>Customer Buyer</th>
                     <th  class="must_entry_caption">Customer Style Ref</th>
                     <th  class="must_entry_caption">Wash Job</th>
                  
                    <th><input type="reset" id="reset_btn" class="formbutton" value="Reset" /></th>
                    </thead>
                <tbody>
                    <tr class="general">
                        <td width="150">
							<?

                           		echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(1,document.getElementById('cbo_within_group').value);" );
                            ?>
                        </td>
                         <td><?php 
						echo create_drop_down( "cbo_within_group", 110, $yes_no,"", 0, "-- Select  --", 1, "fnc_load_party(1,this.value); " ); ?>
                        </td>
                        
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?>
                        </td>
                          <td id="buyer_customer_td">
						  <?   
						  echo create_drop_down( "cbo_buyer_party_name", 150, $blank_array,"", 1, "-- Select --", "", "" );
						  ?>
                          
                        </td>
                      
                       <td align="center" >
                        <input type="text" name="txt_style" id="txt_style" class="text_boxes" style="width:100px" placeholder="Browse" onDblClick="openmypage_job(1);" readonly />
                         <input type="hidden" name="txt_style_id" id="txt_style_id" class="text_boxes" style="width:50px" />
                          
                      </td>
                         <td align="center" >
                        <input type="text" name="txt_job" id="txt_job" class="text_boxes" style="width:100px" placeholder="Browse" onDblClick="openmypage_job(2);" readonly/>
                         <input type="hidden" name="txt_job_id" id="txt_job_id" class="text_boxes" style="width:50px" />
                          
                      </td>
                        
                         <td>
                            <input type="button" id="show_button_1" class="formbutton" value="Show" onClick="fn_report_generated(1)" />

                        </td>

                    </tr>
                </table>
            </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>

 </form>
   <input type="button" id="myBtn" value="OPen" style="display:none"/>
    <div id="myModal" class="modal">
  <div class="modal-content">
  <div class="modal-header">
    <span class="close">×</span>
    <h2>Po Number</h2>
  </div>
  <div class="modal-body">
    <p id="ccc">Some text in the Modal Body</p>

  </div>
  <div class="modal-footer">
    <h3></h3>
  </div>
</div>

</div>
<script>
$("#cbo_buyer_party_name").attr("disabled",true);
</script>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>