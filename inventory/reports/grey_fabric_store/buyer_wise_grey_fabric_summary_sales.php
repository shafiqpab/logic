<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Buyer and sales order Wise Grey Fabrics Stock Report 

Functionality	:
JS Functions	:
Created by		:	Md. Tofael Hossain
Creation date 	: 	12-Jun-2023
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
echo load_html_head_contents("Order Wise Grey Fabrics Stock Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters_2 =
	{
		col_operation: {
			id: ["value_tot_booking_qty","value_tot_recv_qty","value_tot_iss_ret_qty","value_tot_trans_in_qty","value_grand_tot_recv_qty","value_tot_iss_qty","value_tot_rec_ret_qty","value_tot_trans_out_qty","value_grand_tot_iss_qty","value_grand_stock_qty"],
			col: [8,14,15,16,17,18,19,20,21,22],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| openmypage_job
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_job()
	{
		if( form_validation('cbo_company_id*cbo_buyer_id','Company Name*Buyer Name')==false )
		{
			return;
		}

		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var cbo_month_id = $("#cbo_month").val();
		
		var title='Job No Search';
		var page_link='requires/buyer_wise_grey_fabric_summary_sales_controller.php?action=fso_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| openmypage_booking
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_booking()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var cbo_month_id = $("#cbo_month").val();
		var txt_job_no = $("#txt_job_no").val();
		var page_link='requires/buyer_wise_grey_fabric_summary_sales_controller.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&txt_job_no='+txt_job_no;
		var title='Booking No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=430px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var booking_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_booking_no').val(booking_no);
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| openmypage_item
	|--------------------------------------------------------------------------
	|
	*/
	function openmypage_item()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var page_link='requires/buyer_wise_grey_fabric_summary_sales_controller.php?action=item_description_search&company='+company; 
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			var prodNo=this.contentDoc.getElementById("txt_selected_no").value; // product Serial No
			$("#txt_product").val(prodDescription);
			$("#txt_product_id").val(prodID);
			$("#txt_product_no").val(prodNo); 
		}
	}
	
	
	/*
	|--------------------------------------------------------------------------
	| generate_report
	|--------------------------------------------------------------------------
	| rpt_type = 1 = Summary
	|
	*/
	function generate_report(rpt_type)
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}

		var action="report_generate";
				
		var report_title=$( "div.form_caption" ).html();
		var data='action='+action+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*txt_booking_no*txt_booking_id*txt_interal_ref*cbo_value_type*txt_date_to',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;

		freeze_window(3);
		http.open("POST","requires/buyer_wise_grey_fabric_summary_sales_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}
	
	/*
	|--------------------------------------------------------------------------
	| generate_report_reponse
	|--------------------------------------------------------------------------
	|
	*/
	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}

	function new_window(type)
	{
		if( type==1)
		{
			$(".flt").css("display","none");
			$(".search_type").css("display","none");
		}
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		if(type==1)
		{
			$(".flt").css("display","block");
			$(".search_type").css("display","block");
		}
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="280px";
		
	}
	
	function change_caption(type)
	{
		$('#txt_search_comm').val('');
		if(type==1)
		{
			$('#td_search').html('Enter Style');
		}
		else if(type==2)
		{
			$('#td_search').html('Enter Order');
		}
	}

	function getCompanyId() 
	{	 
	    var company_id = document.getElementById('cbo_company_id').value;
	    if(company_id !='') {
			var data="action=load_drop_down_buyer&choosenCompany="+company_id+'&type='+1+'&type2='+4;
			http.open("POST","requires/buyer_wise_grey_fabric_summary_sales_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data); 
			http.onreadystatechange = function(){
			if(http.readyState == 4) 
			{
			  var response = trim(http.responseText);//.split("**");
			  $('#buyer_td').html(response);
			  set_multiselect('cbo_buyer_id','0','0','','0');
			}			 
	      };
	    }         
	}

	function openmypage_rollbal(fso_id, product_ids, color_id)
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+fso_id+"_"+product_ids+"_"+color_id;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/buyer_wise_grey_fabric_summary_sales_controller.php?action=stock_roll_balance_popup&data='+data,'Roll Stock Popup', 'width=850px,height=420px,center=1,resize=0','../../');
	}

	function openmypage(fso_id, product_ids , action, type)
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var popup_width='1050px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/buyer_wise_grey_fabric_summary_sales_controller.php?companyID='+companyID+'&fso_id='+fso_id+'&product_ids='+product_ids+'&action='+action+'&type='+type, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function fabric_sales_order_print6(company_id,booking_no_id,booking_no,job_no, within_group) {
        var data = company_id + '*' + booking_no_id + '*' + booking_no + '*' +job_no + '*' + 'Fabric Sales Order Entry v2';

		if (within_group == 2) {
			window.open("../../../production/requires/fabric_sales_order_entry_v2_controller.php?data=" + data + '&action=fabric_sales_order_print6', true);
		} else {
			alert("This report available for Within Group No");
		}

		return;
    }

	function print_button_setting(company)
	{
		$('#button_data_panel').html('');		get_php_form_data($('#cbo_company_id').val(),'print_button_variable_setting','requires/buyer_wise_grey_fabric_summary_sales_controller' );
	}

	function print_report_button_setting(report_ids)
	{
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==150)
			{
				$('#button_data_panel')
					.append( '<td align="right"><input type="button" name="search" id="search" value="Summary 2" onClick="generate_report(5)" style="width:80px" class="formbutton" /></td>&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==777)
			{
				$('#button_data_panel').append( '<td align="right"><input type="button" name="search" id="search" value="FSO Wise" onClick="generate_report(2)" style="width:80px" class="formbutton" /></td>&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==778)
			{
				$('#button_data_panel').append( '<td align="right"><input type="button" name="search" id="search" value="Rack Wise" onClick="generate_report(3)" style="width:80px" class="formbutton" /></td>&nbsp;&nbsp;&nbsp;' );
			}
			if(report_id[k]==149)
			{
				$('#button_data_panel').append( '<td align="right"><input type="button" name="search" id="search" value="Summary" onClick="generate_report(1)" style="width:80px" class="formbutton" /></td>&nbsp;&nbsp;&nbsp;' );
			}
			
		
		}
	}
</script>
<script src="../../../ext_resource/hschart/hschart.js"></script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<?php echo load_freeze_divs ("../../../",$permission); ?>
		<form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" >
			<h3 style="width:1020px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel" style="width:100%;" align="center">
				<fieldset style="width:1000px;">
					<table class="rpt_table" width="1000" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
								<th class="must_entry_caption">Company</th>
								<th class="must_entry_caption">Cust. Buyer</th>
								<th>Year</th>
								<th>Sales Order No</th>
								<th>Sales/Booking</th>
								<th>IR/IB</th>
                                <th>Value</th>
								<th class="must_entry_caption">Date</th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td id="td_company">
								<?
								echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", "1", "-- Select Company --", $selected, "getCompanyId();" );
								?>
							</td>
							<td id="buyer_td">
								<?
								echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
								?>
							</td>
							<td>
								<?
								echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
								?>
							</td>
							<td>
								<input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:110px" onDblClick="openmypage_job();" placeholder="Browse/Write" />
								<input type="hidden" id="txt_job_id" name="txt_job_id"/>
							</td>
							<td>
								<input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:110px" onDblClick="openmypage_booking();" placeholder="Browse/Write" />
								<input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
							</td>

	                        <td align="center">
		                        <input style="width:140px;" name="txt_interal_ref" id="txt_interal_ref" class="text_boxes" placeholder="write"  />           
		                    </td>
                            <td>
								<?   
                                $valueTypeArr=array(1=>'Value With 0',2=>'Value Without 0');
                                echo create_drop_down( "cbo_value_type", 100, $valueTypeArr,"",0,"",2,"","","");
                                ?>
                            </td>                            

							<td>
								<input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:70px;"/>
							</td>
							<td align="center">
								<input type="button" name="search" id="search" value="Summary" onClick="generate_report(1)" style="width:80px" class="formbutton" />
							</td>		
						</tr>
					</table>
				</fieldset>
			</div>
			<div id="report_container" align="center"></div>
			<div id="report_container2"></div>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_buyer_id','0','0','0');
	//setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_company_id,'0');getCompanyId();") ,3000)];	
</script>
</html>
