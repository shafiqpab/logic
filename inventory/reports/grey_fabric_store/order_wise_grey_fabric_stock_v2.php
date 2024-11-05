<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Order Wise Grey Fabrics Stock Report

Functionality	:
JS Functions	:
Created by		:	JAHID HASAN
Creation date 	: 	31-07-2019
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
			id: ["value_total_opening","value_total_receive","value_total_issue","value_total_tot_receive","value_total_tot_issue","value_total_closing_stock"],
			col: [2,3,4,5,6,7],
			operation: ["sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	var tableFilters_3 =
	{
		col_operation: {
			id: ["value_total_req_qnty","value_total_opening","value_total_receive","value_total_issue","value_total_tot_receive","value_total_tot_issue","value_total_closing_stock"],
			col: [18,19,20,21,22,23,24],
			operation: ["sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function openmypage_color()
	{

		if (form_validation('cbo_company_id', 'Company') == false)
		{
			return;
		}

		var page_link = "requires/order_wise_grey_fabric_stock_controller_v2.php?action=check_color_id";
        var title = "Color Name";
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=350px,center=1,resize=0,scrolling=0', '../')
        emailwindow.onclose = function ()
        {
        	var theemail = this.contentDoc.getElementById("selected_id").value;
        	var split_value = theemail.split('_');

            document.getElementById('hdn_color').value = split_value[0];
            document.getElementById('txt_color').value = split_value[1];
            release_freezing();
        }
    }
	
    function openpage(action,data,width)
	{
		var dataStr="";
		data = encodeURIComponent(data);
		/*if(action=='stock_popup'){
			var dataArr=data.split('_');
			var dataStr='&data0='+dataArr[0]+'&data1='+dataArr[1]+'&data2='+dataArr[2]+'&data3='+dataArr[3]+'&data4='+dataArr[4]+'&data5='+dataArr[5]+'&data6='+dataArr[6]+'&data7='+dataArr[7]+'&data8='+dataArr[8]+'&data9='+dataArr[9]+'&data10='+dataArr[10]+'&data11='+dataArr[11]+'&data12='+dataArr[12]+'&data13='+dataArr[13]+'&data14='+dataArr[14]+'&data15='+dataArr[15]+'&data16='+dataArr[16]+'&data17='+dataArr[17];
			var data='';
		}*/
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_grey_fabric_stock_controller_v2.php?action='+action+'&data='+data+dataStr, 'Details Info', 'width='+width+'px,height=390px,center=1,resize=0,scrolling=0','../../');
	}
	
	function openpage_details(action,barcode,po_id,store_id,popup_type,width)
	{
		//var dataStr="";
		//data = encodeURIComponent(data);
		/*if(action=='stock_popup'){
			var dataArr=data.split('_');
			var dataStr='&data0='+dataArr[0]+'&data1='+dataArr[1]+'&data2='+dataArr[2]+'&data3='+dataArr[3]+'&data4='+dataArr[4]+'&data5='+dataArr[5]+'&data6='+dataArr[6]+'&data7='+dataArr[7]+'&data8='+dataArr[8]+'&data9='+dataArr[9]+'&data10='+dataArr[10]+'&data11='+dataArr[11]+'&data12='+dataArr[12]+'&data13='+dataArr[13]+'&data14='+dataArr[14]+'&data15='+dataArr[15]+'&data16='+dataArr[16]+'&data17='+dataArr[17];
			var data='';
		}*/
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_grey_fabric_stock_controller_v2.php?action='+action+'&barcode='+barcode+'&po_id='+po_id+'&store_id='+store_id+'&popup_type='+popup_type, 'Details Info', 'width='+width+'px,height=390px,center=1,resize=0,scrolling=0','../../');
	}
	


	function generate_report(rpt_type)
	{
		if( form_validation('cbo_company_id','Company Name*Date')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		if(rpt_type==3) var action="report_generate_summery"; 
		else if(rpt_type==2) var action="report_generate_buyer"; 
		else if(rpt_type==4) var action="report_generate3"; 
		else var action="report_generate";
		var data="action="+action+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*txt_booking_no*txt_order_no*txt_date_from*txt_date_to*cbo_store_name*txt_color*hdn_color*cbo_trans_year*txt_ir_no*txt_file_no',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;

		freeze_window(3);
		http.open("POST","requires/order_wise_grey_fabric_stock_controller_v2.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
			//alert(reponse[2]);
			if(reponse[2]==3)
			{
				setFilterGrid("table_body",-1,tableFilters_3);
			}
			else if(reponse[2]==2)
			{
				setFilterGrid("table_body",-1,tableFilters_2);
			}
			else
			{
				var tableFilters_4 =
				{
					col_operation: {
						id : ["value_td_total_req_qnty","value_total_opening_td"],
						col : [20,21],
						operation : ["sum","sum"],
						write_method : ["innerHTML","innerHTML"],
						skip_sum : ['skip_td','none']
					}
				}
				setFilterGrid("table_body",-1,tableFilters_4);
			}
		}
	}

	function new_window(type)
	{
		/*if(type==2||type==3){
			$(".flt").css("display","none");
		}*/	
		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		if(type==3)
		{
			$('#scroll_body tbody tr:first').hide();
		}

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		if(type==2||type==3){
			$(".flt").css("display","block");
		}
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="280px";
		if(type==3)
		{
			$('#scroll_body tbody tr:first').show();
		}
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/order_wise_grey_fabric_stock_controller_v2.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);
		}
	}
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
		var page_link='requires/order_wise_grey_fabric_stock_controller_v2.php?action=booking_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id+'&txt_job_no='+txt_job_no;
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

	function openpage_fabric_booking(action,po_id)
	{
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_grey_fabric_stock_controller_v2.php?action='+action+'&po_id='+po_id, 'Booking Details Info', 'width=900px,height=370px,center=1,resize=0,scrolling=0','../../');
	}

	function openmypage_delivery(orderID,programNo,prodID,from_date,to_date,popup_width,action,type)
	{
		var companyID = $("#cbo_company_id").val();
		var popup_width=popup_width;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_grey_fabric_stock_controller_v2.php?companyID='+companyID+'&orderID='+orderID+'&programNo='+programNo+'&prodID='+prodID+'&from_date='+from_date+'&to_date='+to_date+'&action='+action+'&popup_width='+popup_width+'&type='+type, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../",$permission); ?>
		<form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" >
			<h3 style="width:1380px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel" style="width:100%;" align="center">
				<fieldset style="width:1360px;">
					<table class="rpt_table" width="1360" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<tr>
								<th class="must_entry_caption">Company</th>
								<th>Buyer</th>
								<th>Job Year</th>
								<th>Job</th>
								<th>Booking</th>
								<th>Order No</th>
								<th>File No</th>
								<th>Internal Ref.</th>
                                <th>Transaction Year</th>
								<th>Store</th>
								<th>Fabric Color</th>
								<th class="must_entry_caption">Date From</th>
								<th class="must_entry_caption">Date To</th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
							</tr>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_wise_grey_fabric_stock_controller_v2',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/order_wise_grey_fabric_stock_controller_v2', this.value, 'load_drop_down_store', 'store_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/order_wise_grey_fabric_stock_controller_v2' );get_php_form_data(this.value, 'company_wise_report_button_setting','requires/order_wise_grey_fabric_stock_controller_v2');" );
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
								<input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:70px" onDblClick="openmypage_job();" placeholder="Browse/Write" />
								<input type="hidden" id="txt_job_id" name="txt_job_id"/>
							</td>
							<td>
								<input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes" style="width:80px" onDblClick="openmypage_booking();" placeholder="Browse/Write" />
								<input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
							</td>
							<td>
								<input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:80px" placeholder="Write"/>
							</td>
							<td>
								<input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:80px" placeholder="Write"/>
							</td>
							<td>
								<input type="text" id="txt_ir_no" name="txt_ir_no" class="text_boxes" style="width:100px" placeholder="Write"/>
							</td>
                            <td>
								<?
								//date("Y",time())
								echo create_drop_down( "cbo_trans_year", 70, create_year_array(),"", 1,"-- All --", '', "",0,"" );
								?>
							</td>
							<td id="store_td">
								<? echo create_drop_down( "cbo_store_name", 150, $blank_array,"", 1, "-- All Store --", $storeName, "",0 ); ?>
							</td>
							<td>
								<input type="text" id="txt_color" name="txt_color" class="text_boxes" style="width:80px" onDblClick="openmypage_color();" placeholder="Browse" />
								<input type="hidden" id="hdn_color" name="hdn_color"/>
							</td>
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:70px;"/>
							</td>
							<td>
								<input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:70px;"/>
							</td>
							<td>
								<input type="button" name="show" id="show" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
							</td>
						</tr>
						<tr>
							<td colspan="14" align="center"><? echo load_month_buttons(1); ?> &nbsp;&nbsp;<input type="button" name="buyer_wise" id="buyer_wise" value="Buyer Wise" onClick="generate_report(2)" style="width:80px" class="formbutton" />
                            <input type="button" name="report2" id="report2" value="Report2" onClick="generate_report(3)" style="width:80px" class="formbutton" />
							<input type="button" name="report3" id="report3" value="Report3" onClick="generate_report(4)" style="width:80px" class="formbutton" />
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
	set_multiselect('cbo_year*cbo_store_name','0*0','0*0','0*0');
	set_multiselect('cbo_year','0','1','<?= date("Y",time());?>','0');
</script>

</html>
