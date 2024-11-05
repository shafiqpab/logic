<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create CPA/Short Fabric Booking Analysis Report.
Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	01-09-2014
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
//echo load_html_head_contents("Cost Breakdown Report","../../", 1, 1, $unicode,1,1);
echo load_html_head_contents("CPA/Short Fabric Booking Analysis Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
	var tableFilters =
	{
		col_9: "none",
		col_operation: {
			id: ["value_order_qty_pcs","value_main_fin_book_qty","value_main_fin_book_qty_yds","value_main_fin_book_qty_mtr","value_main_grey_book_qty","value_short_fin_book_qty","value_short_fin_book_qty_yds","value_short_fin_book_qty_mtr","value_short_grey_book_qty","value_tot_grey_book_qty","value_tot_fin_book_qty","value_tot_fin_book_qty_yds","value_tot_fin_book_qty_mtr","value_short_fin_book_amount_usd","value_tot_bom_cons_qty","value_total_booking_cons_dzn","value_total_bom_variance"],
		    col: [13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29],
		    operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	var tableFilters2 =
	{
		col_9: "none",
		col_operation: {
			id: ["value_order_qty_pcs","value_main_fin_book_qty","value_main_fin_book_qty_yds","value_main_fin_book_qty_mtr","value_main_grey_book_qty","value_short_fin_book_qty","value_short_fin_book_qty_yds","value_short_fin_book_qty_mtr","value_short_grey_book_qty","value_tot_grey_book_qty","value_tot_fin_book_qty","value_tot_fin_book_qty_yds","value_tot_fin_book_qty_mtr","value_short_fin_book_amount_usd"],
		    col: [13,14,15,16,17,18,19,20,21,22,23,24,25,26],
		    operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	var tableFilters3 =
	{

		col_operation: {
			id: ["tot_booking_qnty","tot_yarn_qnty","tot_yarn_cost","tot_knitting_qnty","tot_knitting_cost","tot_dyeing_qnty","tot_dyeing_cost","total_cost"],
		    col: [18,19,20,21,22,23,24,25],
		    operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	function fn_cpa_report_generated(rpt_type)
	{
		var job_no=document.getElementById('txt_job_no').value;
		var booking_no=document.getElementById('txt_booking_no').value;
		var ref_no=document.getElementById('txt_ref_no').value;
		var file_no=document.getElementById('txt_file_no').value;
		var search_date=document.getElementById('cbo_search_date').value;

		if(job_no=="" && booking_no=="" && ref_no=="" && file_no=="")
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From date Fill*To date Fill')==false)
	  			{

	  				return;
	  			}
		}

		else
		{

			if(form_validation('cbo_company_name','Company')==false)
			{
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=cpa_report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_date_from*txt_date_to*txt_job_no*txt_job_id*txt_booking_no*txt_booking_no*cbo_search_date*cbo_year*txt_ref_no*txt_file_no*cbo_booking_type*chk_date_range*cbo_division_id*cbo_short_booking_type',"../../../")+"&rpt_type="+rpt_type+"&report_title="+report_title;

		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/cpa_short_fabric_booking_analysis_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_cpa_report_generated_reponse;
	}

	function fn_cpa_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("****");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			append_report_checkbox('table_header_1',1);
			if(reponse[2]==1 && (reponse[3] ==3 || reponse[3]==1) )
			{
				setFilterGrid("table_body",-1,tableFilters2 );
				setFilterGrid("table_body2",-1,tableFilters3 );
			}
			else
			{
				setFilterGrid("table_body",-1,tableFilters );
				setFilterGrid("table_body2",-1,tableFilters3 );
			}
			//alert(document.getElementById('graph_data').value);
			//show_graph( '', document.getElementById('graph_data').value, "pie", "chartdiv", "", "../../../", '',580,700 );
			release_freezing();
			show_msg('3');
		}
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_year_id = $("#cbo_year").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/cpa_short_fabric_booking_analysis_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../../');
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
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value;
		//alert (data);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/cpa_short_fabric_booking_analysis_report_controller.php?action=booking_no_popup&data='+data,'Booking No Popup', 'width=1050px,height=420px,center=1,resize=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_booking");
			//var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				//document.getElementById("txt_booking_id").value=theemail.value;
			    document.getElementById("txt_booking_no").value=theemail.value;
				release_freezing();
			}
		}
	}

	function ms_booking_no_popup(type,company,booking_no,po_id,job_no,fabric_source,fabric_nature,id_approved_id,action)
	{
		if(type==1)
		{
			var report_title="Short Fabric Booking";
		}
		else if(type==2)
		{
			var report_title="Main Fabric Booking";
		}
		else if(type==108)
		{
			var report_title="Partial Main Fabric Booking";
		}
		else
		{
			var report_title="Sample Fabric Booking With Order";
		}
		var path="../../../";
		if(type==108)
		{
		var data="action=show_fabric_booking_report_urmi_per_job"+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company+"'"+
					'&txt_order_no_id='+"'"+po_id+"'"+
					'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
					'&cbo_fabric_source='+"'"+fabric_source+"'"+
					'&id_approved_id='+"'"+id_approved_id+"'"+
					'&report_title='+"'"+report_title+"'"+
					'&txt_job_no='+"'"+job_no+"'";
					'&path='+"'"+path+"'";
		}
		else
		{
			var data="action="+action+
					'&txt_booking_no='+"'"+booking_no+"'"+
					'&cbo_company_name='+"'"+company+"'"+
					'&txt_order_no_id='+"'"+po_id+"'"+
					'&cbo_fabric_natu='+"'"+fabric_nature+"'"+
					'&cbo_fabric_source='+"'"+fabric_source+"'"+
					'&id_approved_id='+"'"+id_approved_id+"'"+
					'&report_title='+"'"+report_title+"'"+
					'&txt_job_no='+"'"+job_no+"'";
					'&path='+"'"+path+"'";
		}
				//	alert(data);//show_fabric_booking_report_urmi show_fabric_booking_report_urmi
		if(type==1)
		{
			http.open("POST","../../../order/woven_order/requires/short_fabric_booking_controller.php",true);
		}
		else if(type==2)
		{
			// http.open("POST","../../../order/woven_order/requires/fabric_booking_controller.php",true);
			http.open("POST","../../../order/woven_order/requires/fabric_booking_urmi_controller.php",true);
		}
		else if(type==108)
		{
			http.open("POST","../../../order/woven_order/requires/partial_fabric_booking_controller.php",true);
		}
		else
		{
			http.open("POST","../../../order/woven_order/requires/sample_booking_controller.php",true);
		}

		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4)
		    {
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
			d.close();
		   }
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
			document.getElementById('search_by_th_up').innerHTML="Short Booking Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==3)
		{
			document.getElementById('search_by_th_up').innerHTML="Country Ship Date";
			$('#search_by_th_up').css('color','blue');
		}
		else if(str==4)
		{
			document.getElementById('search_by_th_up').innerHTML="Insert Date";
			$('#search_by_th_up').css('color','blue');
		}
	}

	function fnc_fabric_sales_order_print(company_name, booking_id, booking_no, sales_job_no, action) {
			var data = company_name + '*' + booking_id + '*' + booking_no + '*' + sales_job_no + '*Fabric Sales Order Entry';
			window.open("../../../production/requires/fabric_sales_order_entry_controller.php?data=" + data + '&action=' + action, true);
		}

	function generate_color_popup(color_id,company_id,job_no,buyer_id,style,action,type_id)
	{
		var popup_width=1230;
		//alert(popup_width);
		//var popup_width='730px';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/cpa_short_fabric_booking_analysis_report_controller.php?company_id='+company_id+'&color_id='+color_id+'&job_no='+job_no+'&buyer_id='+buyer_id+'&style='+style+'&type_id='+type_id+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=350px,center=1,resize=0,scrolling=0','../../');
	}

	function fnc_date_range()
	{
		if(document.getElementById('chk_date_range').checked==true)
		{
			document.getElementById('chk_date_range').value=1;
		}
		else if(document.getElementById('chk_date_range').checked==false)
		{
			document.getElementById('chk_date_range').value=2;
		}
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../","");  ?>
    <form id="cost_breakdown_rpt">
    <h3 align="left" id="accordion_h1" style="width:1140px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
    <div id="content_search_panel">
        <fieldset style="width:1050px;">
        <table class="rpt_table" width="1050" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
            <thead>
                <th class="must_entry_caption">Company Name</th>
                <th>Buyer Name</th>
                <th>Year</th>
                <th>Job No.</th>
                <th>Ref No.</th>
                <th>File No.</th>
                <th title="Only Short Booking">Booking No</th>
                <th>Search By</th>
                 <th>Req Booking</th>
				 <th>Division</th>
				 <th>Short Booking Type</th>
                <th colspan="2" id="search_by_th_up"  >Short Booking Date</th>
                <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
						<?
                        	echo create_drop_down( "cbo_company_name", 140, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cpa_short_fabric_booking_analysis_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                    </td>
                    <td id="buyer_td">
						<?
                        	echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                        ?>
                    </td>
                    <td align="center">
						<?
							$year_current=date("Y");
							echo create_drop_down( "cbo_year", 80, $year,"", 1, "--All Year--", 0, "" );
                        ?>
                    </td>
                    <td>
                        <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes_numeric" style="width:75px" onDblClick="openmypage_job();" placeholder="Write/Browse Job" />
                        <input type="hidden" id="txt_job_id" name="txt_job_id" class="text_boxes"/>
                    </td>
                    <!-- new development -->
                     <td>
                        <input type="text" id="txt_ref_no" name="txt_ref_no" class="text_boxes" style="width:75px" onDblClick="openmypage_job();" placeholder="Write" />
                    </td>
                     <td>
                        <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes_numeric" style="width:75px" onDblClick="openmypage_job();" placeholder="Write" />
                    </td>
                    <!--  -->
                    <td>
                        <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes_numeric" style="width:75px" onDblClick="openmypage_booking();" placeholder="Write/Browse Booking"  />
                        <input type="hidden" id="txt_booking_id" name="txt_booking_id"/>
                    </td>
                    <td width="" align="center">
						<?
							$search_by = array(1=>'Shipment Date',2=>'Booking Date',3=>'Country Ship Date',4=>'Job Insert Date');

							$dd="search_populate(this.value)";
							echo create_drop_down( "cbo_search_date", 100, $search_by,"",0, "--Select--", 2,$dd,0 );
                    	?>
                    </td>
                     <td width="" align="center">
						<?
							$booking_by = array(0=>'All',1=>'Approved');
						  echo create_drop_down( "cbo_booking_type", 100, $booking_by,"", 1, "-- Select --", $selected, "",0,"" );

                    	?>
                    </td>
					 <td width="" align="center">
						<?

						  echo create_drop_down( "cbo_division_id", 100, $short_division_array,"", 1, "-- All --", $selected, "",0,"" );

                    	?>
                    </td>
					 <td width="" align="center">
						<?

						  echo create_drop_down( "cbo_short_booking_type", 100, $short_booking_type,"", 1, "-- All --", $selected, "",0,"" );

                    	?>
                    </td>
                    <td>
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" value="<? //echo date('d-m-Y');?>" >
                    </td>
                    <td>
                    	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" value="<? //echo date('d-m-Y');?>" >
                    </td>
                    <td>
                    	<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_cpa_report_generated(1)" />

                    </td>
                </tr>
                <tr>
                    <td colspan="10" align="center">
                    	<? echo load_month_buttons(1); ?>
                    </td>
                    <td colspan="2" align="center"><input type="checkbox" name="chk_date_range" id="chk_date_range" onClick="fnc_date_range();" value="2"><strong>Include Date Range</strong></td>
                     <td>
                    	<input type="button" id="show_button" class="formbutton" style="width:70px" value="FSO" onClick="fn_cpa_report_generated(2)" />

                    </td>
					<td>
                    <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show 2" onClick="fn_cpa_report_generated(3)" />

                    </td>
                </tr>
            </tbody>
        </table>
        </fieldset>
    </div>
    </form>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
