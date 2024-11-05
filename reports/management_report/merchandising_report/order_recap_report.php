<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This file for Order recap report
Functionality	:
JS Functions	:
Created by		:	Md. Sakibul Islam 
Creation date 	: 	13-12-2023
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
//echo load_html_head_contents("Order Recap Report","../../../", 1, 1, $unicode);
echo load_html_head_contents("Order Recap Report", "../../../", 1, 1,$unicode,1,1);
$date_category_arr = array(1 => "Country Ship Date",2 => "Pub Shipment Date",3 => "Original Shipment Date",4 => "Po Insert Date");
?>
<script>
	var permission = '<? echo $permission; ?>';

	function fn_report_generated(type)
	{
		
			if (form_validation('cbo_company_name','Please Select Comapny')==false)
			{
				return;
			}
			else
			{
				if(type==1)
				{
					var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_working_company*cbo_buyer_name*txt_style_ref*txt_job_no*txt_job_no_id*cbo_date_category*txt_date_from*txt_date_to*cbo_year_selection',"../../../");
					//alert(data);
					freeze_window();
					http.open("POST","requires/order_recap_report_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = fn_report_generated_reponse;
				}else{
					var data="action=report_generate2"+get_submitted_data_string('cbo_company_name*cbo_working_company*cbo_buyer_name*txt_style_ref*txt_job_no*txt_job_no_id*cbo_date_category*txt_date_from*txt_date_to*cbo_year_selection',"../../../");
					//alert(data);
					freeze_window();
					http.open("POST","requires/order_recap_report_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = fn_report_generated_reponse;
				}
			}
		
	}
	

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("****");
			var totRow=reponse[2];
			$('#data_panel').html( '<br><b>Convert To </b><a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>' );
			$('#data_panel').append( '&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window();" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>' );
			$('#report_container').html(reponse[0]);
			var tableFilters = {
				col_operation: {
				id: ["tot_order_qty","tot_order_qty_pcs","tot_minute","tot_order_val"],
				col: [30,33,37,38],
				operation: ["sum","sum","sum","sum"],
				write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
				}
			}
			setFilterGrid("table_body",-1,tableFilters);
			//setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}
	function openmypage_file(action,job_no,type)
	{
		var page_link='requires/order_recap_report_controller.php?action='+action+'&job_no='+job_no+'&type='+type
		var title="File View";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=300px,center=1,resize=0,scrolling=0','../../')
	}
	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
		}
	}
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";

		$("#table_body tr:first").show();
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}

	function date_fill_change(str)
	{
		if (str==1)
		{
			document.getElementById('search_date_td').innerHTML='OPD Date';
		}
		else
		{
			document.getElementById('search_date_td').innerHTML='Insert Date';
		}
	}

	// function generate_worder_report(type,booking_no,company_id,order_id,fabric_nature,fabric_source,job_no,approved)
	// {
	// 	var data="action=show_fabric_booking_report"+
	// 				'&txt_booking_no='+"'"+booking_no+"'"+
	// 				'&cbo_company_name='+"'"+company_id+"'"+
	// 				'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
	// 				'&cbo_fabric_source='+"'"+fabric_source+"'"+
	// 				'&id_approved_id='+"'"+approved+"'"+
	// 				'&txt_job_no='+"'"+job_no+"'"+
	// 				'&txt_order_no_id='+"'"+order_id+"'";
	// 				//javascript:generate_worder_report('2','FFL-Fb-16-01064','1','6614','2','1','FFL-16-00719','1');
	// 	if(type==1)
	// 	{
	// 		http.open("POST","../../../order/woven_order/requires/short_fabric_booking_controller.php",true);
	// 	}
	// 	else if(type==2)
	// 	{
	// 		http.open("POST","../../../order/woven_order/requires/fabric_booking_controller.php",true);
	// 	}
	// 	else
	// 	{
	// 		http.open("POST","../../../order/woven_order/requires/sample_booking_controller.php",true);
	// 	}

	// 	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	// 	http.send(data);
	// 	http.onreadystatechange = generate_fabric_report_reponse;
	// }

	// function generate_fabric_report_reponse()
	// {
	// 	if(http.readyState == 4)
	// 	{
	// 		var w = window.open("Surprise", "#");
	// 		var d = w.document.open();
	// 		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	// '<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
	// 		d.close();
	// 	}
	// }
    function openmypage_job()
    {
        // alert('ok');
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }

        var companyID = $("#cbo_company_name").val();

        var page_link='requires/order_recap_report_controller.php?action=job_no_popup&companyID='+companyID;

        var title='Job No Search';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=1,scrolling=0','../../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var data_arr=this.contentDoc.getElementById("hide_job_no").value.split("_");
            $('#txt_job_no').val(data_arr[1]);
            $('#txt_job_no_id').val(data_arr[0]);
        }
    }
	function change_date_caption(id){
		if(id==1){
			$('#date_caption').html("<span style='color:blue'>Country Ship Date</span>");
		}
		else if(id==2){
			$('#date_caption').html("<span style='color:blue'>Pub Ship Date</span>");
		}
		else if(id==3){
			$('#date_caption').html("<span style='color:blue'>Original Ship Date</span>");
		}
		else{
			$('#date_caption').html("<span style='color:blue'>Po Insert Date</span>");
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
<form id="dailyorderentry_1">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <h3 style="width:1050px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1050px;">
                <table class="rpt_table" width="1050px" cellpadding="1" cellspacing="0" rules="all" border="1">
                    <thead>
                        <th width="150" class="must_entry_caption">Company</th>
                        <th width="150" class="must_entry_caption">W.Working</th>
						<th width="150">Buyer</th>
                        <th width="100" id="search_text_td">Style</th>
                        <th width="100">Job No</th>
						<th width="100">Date Range</th>
                    	<th width="140" colspan="2" id="date_caption">Date Category</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                    </thead>
                    <tr class="general">
                        <td>
							<?
                           	 echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/order_recap_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );set_multiselect('cbo_buyer_name','0','0','','0');" );
                            ?>
                        </td>
                        <td>
							<?
                            	echo create_drop_down( "cbo_working_company", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                            ?>
                        </td>
						<td id="buyer_td">
							<? echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
							<td>

                        	<input type="text"  id="txt_style_ref" class="text_boxes" style="width:100px">
                        </td>
                        <td>
                        	<input type="text"  id="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write/Browse"  onDblClick="openmypage_job();">
                            <input type="hidden" name="txt_job_no_id" id="txt_job_no_id"/>
                        </td>
						<td><? echo create_drop_down( "cbo_date_category", 100, $date_category_arr, "", 0,"All Type", $selected, "change_date_caption(this.value)" ); ?></td>
						<td>
							<input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:60px" placeholder="From Date">
						</td>
                        <td>
							<input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:60px" placeholder="To Date"></td>
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center"><? echo load_month_buttons(1); ?></td>
                        <td align="center">
                        	<input type="button"  id="show_button2" class="formbutton" style="width:100px" value="Show 2" onClick="fn_report_generated(2)" />
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="data_panel" align="center"></div>
    <div id="report_container" align="center"></div>
</form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	set_multiselect('cbo_buyer_name','0','0','','0');	
</script>


</html>