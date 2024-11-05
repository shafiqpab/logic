<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Order Wise Finich Fabric Stock Report

Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	24-04-2014
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
echo load_html_head_contents("Style Wise Daily Finish Fabrci Follow UP Report","../../../", 1, 1, $unicode,1,1);

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters =
	{
		//col_15: "none",
		col_operation: {
		id: ["total_order_qnty","total_req_qty","total_rec_qty","total_rec_bal","total_issue_qty","total_stock","total_possible_cut_pcs","total_actual_cut_qty"],
		col: [16,18,19,20,21,22,24,25],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function generate_report(type)
	{
		/*var company = document.getElementById('cbo_company_id').value;
		var buyer = document.getElementById('cbo_buyer_id').value;*/
		if( form_validation('cbo_company_id*txt_date_from','Company Name*Date')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		if(type == 1){
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*txt_date_from*cbo_buyer_id*txt_book_no*txt_book_id*cbo_year*txt_job_no*txt_job_id*txt_order_no_show*txt_order_no*txt_style*cbo_location_id*cbo_store_id',"../../../")+'&report_title='+report_title+'&type='+type;
		}

		//alert (data);
		freeze_window(3);
		http.open("POST","requires/style_wise_daily_finish_fabric_follow_up_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			/*var reponse=trim(http.responseText).split("####");
			//alert(reponse[1]);
			$("#report_container2").html(reponse[0]);
			if(typeof(reponse[1]) != 'undefined') 
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				if(reponse[2]!=3)
				{
				setFilterGrid("table_body",-1);
				setFilterGrid("table_body2",-1);
				}
				else
				{
				 setFilterGrid("table_body",-1,tableFilters);
				}
				show_msg('3');
			}

			release_freezing();*/

			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			setFilterGrid("table_body",-1,tableFilters);
			//append_report_checkbox('table_header_1',1);

			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="450px";
		
		$("#table_body tr:first").show();
	}

	function new_window_______(type)
	{
		
		//alert(type);
		if(type!=3)
		{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		document.getElementById('scroll_body2').style.overflow="auto";
		document.getElementById('scroll_body2').style.maxHeight="none";
		$('#table_body2 tr:first').hide();
		
		}
		else
		{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		}
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		if(type!=3)
		{
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#table_body tr:first').show();
		document.getElementById('scroll_body2').style.overflowY="scroll";
		document.getElementById('scroll_body2').style.maxHeight="380px";
		$('#table_body2 tr:first').show();
		}
		else
		{
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$('#table_body tr:first').show();
		}
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		//alert(companyID);
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/style_wise_daily_finish_fabric_follow_up_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_job_no').val(job_no);
			$('#hide_job_id').val(job_id);
		}
	}

	function openPopupOrder()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		
		var page_link='requires/style_wise_daily_finish_fabric_follow_up_report_controller.php?action=order_no_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'Order No Search', 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			$('#txt_order_no_show').val(order_no);
			$('#txt_order_no').val(order_id);
		}
	}

	/*function openmypage(po_id,prod_id,color,gsm_weight,action)
	{
		alert(gsm_weight);
		var companyID = $("#cbo_company_id").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_daily_finish_fabric_follow_up_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&gsm_weight='+gsm_weight+'&prod_id='+prod_id+'&color='+color+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}*/
	function openmypage_booking()
	{
			//var job_no = $("#txt_job_no").val();
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('cbo_year').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/style_wise_daily_finish_fabric_follow_up_report_controller.php?action=booking_no_popup&data='+data,'Booking No Popup', 'width=1050px,height=420px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			//var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				//document.getElementById("txt_booking_id").value=theemail.value;
			    document.getElementById("txt_book_no").value=theemail.value;
				release_freezing();
			}
		}
	}

	function openmypage(po_id,body_part_id,color,gsm_weight,action)
	{
		var companyID = $("#cbo_company_id").val();
		var popup_width='600px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_daily_finish_fabric_follow_up_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&body_part_id='+body_part_id+'&color='+color+'&gsm_weight='+gsm_weight+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
	}

	function generate_req_qty_report(po_id,job_no,buyer_name,body_part_id,color_id,booking_no,booking_type,action)
	{
		//alert(action);
		var companyID = $("#cbo_company_id").val();
		var popup_width='700px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_daily_finish_fabric_follow_up_report_controller.php?companyID='+companyID+'&po_id='+po_id+'&job_no='+job_no+'&buyer_name='+buyer_name+'&body_part_id='+body_part_id+'&color_id='+color_id+'&booking_no='+booking_no+'&booking_type='+booking_type+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}
	function openmypage_issue_recv(recvIssue_id,rpt_type,data_string)
	{ 
		var companyID = $("#cbo_company_id").val();
		var action="open_recv_issue_popup";
		var popup_width='1340px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/style_wise_daily_finish_fabric_follow_up_report_controller.php?companyID='+companyID+'&recvIssue_id='+recvIssue_id+'&action='+action+'&rpt_type='+rpt_type+'&data_string='+data_string, 'Details Veiw', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../../');
	}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" >
    <h3 style="width:1100px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1100px;">
                <table class="rpt_table" width="1100" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="140" class="must_entry_caption">Company</th>
                            <th width="140">Location</th>
                            <th width="130">Buyer</th>
                            <th width="60">Year</th>
                            <th width="75">Job</th>
                            <th width="70">Style</th>
                            <th width="75">Order</th>
                            <th width="75">Booking No</th>
                            <th width="75" class="must_entry_caption">Date</th>
                            <th width="75">Store</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:50px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                    	  <td>
                            <?
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/style_wise_daily_finish_fabric_follow_up_report_controller',this.value, 'load_drop_down_location', 'location_td'); load_drop_down('requires/style_wise_daily_finish_fabric_follow_up_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td'); load_drop_down( 'requires/style_wise_daily_finish_fabric_follow_up_report_controller',this.value, 'load_drop_down_store', 'store_td' );" );
                            ?>
                        </td>
                        <td id="location_td">
							<?
								echo create_drop_down("cbo_location_id", 140, $blank_arra, "",1, "-Select Location-", "", "");
							?>
                        </td>
                        <td id="buyer_td">
							<?
								echo create_drop_down("cbo_buyer_id", 130, $blank_arra, "",1, "-Select Buyer-", "", "");
							?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down("cbo_year", 60, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, "");
                            ?>
                        </td>

                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:70px" onDblClick="openmypage_job();" placeholder="Write/Browse" />
                            <input type="hidden" id="txt_job_id" name="txt_job_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" id="txt_style" name="txt_style" class="text_boxes" style="width:70px"  placeholder="Write"  />

                        </td>
                       
                        <td>
                            <input type="text" id="txt_order_no_show" name="txt_order_no_show" class="text_boxes" style="width:100px" onDblClick="openPopupOrder()" placeholder="Browse/Write" />
                            <input type="hidden" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:100px" />
                        </td>
                        <td>
                            <input type="text" id="txt_book_no" name="txt_book_no" class="text_boxes" style="width:70px" onDblClick="openmypage_booking();" placeholder="Write/Browse" />
                            <input type="hidden" id="txt_book_id" name="txt_book_id" class="text_boxes" style="width:60px" />
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from"   class="datepicker" style="width:60px;" value=" <? echo date("d-m-Y");?> "/>
                        </td>
                        <td id="store_td">
                        	<?
                               echo create_drop_down( "cbo_store_id", 90, "select comp.id, comp.store_location from lib_store_location comp where comp.status_active=1 and comp.is_deleted=0 $sotre_cond order by comp.store_location","id,store_location", 1, "-Select Store-", $selected, "" );
                            ?>
                        </td>
                        <td>
                            <input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1)" style="display: block;width:50px" class="formbutton" />
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </form>
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</body>
<script>
	//set_multiselect('cbo_uom','0','0','','0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
