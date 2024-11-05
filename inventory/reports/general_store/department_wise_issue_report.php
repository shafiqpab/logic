<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Item Wise Purchase Report

Functionality	:
JS Functions	:
Created by		:	Monir Hossain
Creation date 	: 	30-01-2017
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
echo load_html_head_contents("Department Wise Issue Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function openmypage_item_account()
	{
		if( form_validation('cbo_company_name*cbo_item_category_id*txt_item_group','Company Name*Item Category*Item Group')==false )
		{
			return;
		}

		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_item_category_id').value+"_"+document.getElementById('txt_item_group_id').value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/department_wise_issue_report_controller.php?action=item_account_popup&data='+data,'Item Account Popup', 'width=800px,height=480px,center=1,resize=0','../../')

		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("item_account_id");
			var theemailv=this.contentDoc.getElementById("item_account_val");
			var response=theemail.value.split('_');
			if (theemail.value!="")
			{
				freeze_window(5);
				document.getElementById("txt_item_account_id").value=response[0];
			    document.getElementById("txt_item_acc").value=theemailv.value;
				//reset_form();
				get_php_form_data( response[0], "item_account_dtls_popup", "requires/department_wise_issue_report_controller" );
				release_freezing();
			}
		}
	}

	function openmypage_item_group()
	{

		if( form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_item_group = $("#txt_item_group").val();
		var txt_item_group_id = $("#txt_item_group_id").val();
		var txt_item_group_no = $("#txt_item_group_no").val();
		var page_link='requires/department_wise_issue_report_controller.php?action=item_group_such_popup&company='+company+'&cbo_item_category_id='+cbo_item_category_id+'&txt_item_group='+txt_item_group+'&txt_item_group_id='+txt_item_group_id+'&txt_item_group_no='+txt_item_group_no;
		var title="Search Item Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=320px,height=370px,center=1,resize=0,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var item_group_id=this.contentDoc.getElementById("txt_selected_id").value; // product ID
			var item_group_des=this.contentDoc.getElementById("txt_selected").value; // product Description
			var item_group_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			//alert(style_no);
			$("#txt_item_group").val(item_group_des);
			$("#txt_item_group_id").val(item_group_id);
			$("#txt_item_group_no").val(item_group_no);
		}

	}

	function generate_report(operation)
	{

		var txt_reference_id = $("#txt_reference_id").val();
		txt_reference_id = txt_reference_id.trim();

		if(txt_reference_id != "")
		{
			if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
		}
		else{
			if( form_validation('cbo_company_name*cbo_item_category_id*txt_date_from*txt_date_to','Company Name*Item Category*From Date*To Date')==false )
			{
				return;
			}
		}

		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var txt_product_id = $("#txt_product_id").val();
		var item_group_id = $("#txt_item_group_id").val();
		var item_account_id = $("#txt_item_account_id").val();
		var txt_item_code = $("#txt_item_code").val();
		var cbo_department = $("#cbo_department").val();
		var cbo_section = $("#cbo_section").val();
		var cbo_store_name = $("#cbo_store_name").val();
		var cbo_location = $("#cbo_location").val();
		var from_date = $("#txt_date_from").val();
		var to_date = $("#txt_date_to").val();
		var cbo_search_by = $("#cbo_search_by").val();
		var txt_reference_id = $("#txt_reference_id").val();
		var cbo_sewing_floor_name = $("#cbo_sewing_floor_name").val();
		var cbo_sewing_floor_line = $("#cbo_sewing_floor_line").val();

		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_store_name="+cbo_store_name+"&cbo_item_category_id="+cbo_item_category_id+"&txt_item_code="+txt_item_code+"&txt_product_id="+txt_product_id+"&from_date="+from_date+"&to_date="+to_date+"&item_account_id="+item_account_id+"&item_group_id="+item_group_id+"&cbo_department="+cbo_department+"&cbo_section="+cbo_section+"&cbo_location="+cbo_location+"&cbo_search_by="+cbo_search_by+"&txt_reference_id="+txt_reference_id+"&cbo_sewing_floor_name="+cbo_sewing_floor_name+"&cbo_sewing_floor_line="+cbo_sewing_floor_line;
		if(operation==1){action="generate_summary_report";}
		else if(operation==3){ action="generate_report";}
		else if(operation==4){ action="generate_summary_report2";}
		else if(operation==5){ action="generate_report2";}
		var data="action="+action+dataString;
		//alert (data);
		freeze_window(operation);
		http.open("POST","requires/department_wise_issue_report_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="##" onclick="fnExportToExcel()" target=_blank; style="text-decoration:none" id="dlink"><input type="button" class="formbutton" value="Excel Preview" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}
function fnExportToExcel()
{
    // $(".fltrow").hide();
    let tableData = document.getElementById("report_container2").innerHTML;
    // alert(tableData);
    let data_type = 'data:application/vnd.ms-excel;base64,',
    template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="2px">{table}</table></body></html>',
    base64 = function (s) {
        return window.btoa(unescape(encodeURIComponent(s)))
    },
    format = function (s, c) {
        return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; })
    }

    let ctx = {
        worksheet: 'Worksheet',
        table: tableData
    }

    let dt = new Date();
    document.getElementById("dlink").href = data_type + base64(format(template, ctx));
    document.getElementById("dlink").traget = "_blank";
    document.getElementById("dlink").download = dt.getTime()+'_display_board.xls';
    document.getElementById("dlink").click();
    // $(".fltrow").show();
    // alert('ok');
}
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="auto";
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
	function loadnewstore( value )
	{
		load_drop_down( 'requires/department_wise_issue_report_controller', value, 'load_drop_down_store', 'store_td' );

	}
	function changeTitle(ref)
	{
		var fld = document.getElementById('cbo_search_by');
		var fld_data  =fld.options[fld.selectedIndex].text;
		$("#search_by_td_up").html("Please Enter "+fld_data);
	}

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />
        <form name="departmentwiseissue_1" id="departmentwiseissue_1" autocomplete="off" >
         <h3 style="width:1710px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <div id="content_search_panel" style="width:1710px" >
            <fieldset>
                <table class="rpt_table" width="1710" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="120">Location</th>
                        <th width="120" class="must_entry_caption">Item Category</th>
                        <th width="90" class="must_entry_caption">Item Group</th>
                        <th width="90">Item Account</th>
                        <th width="120">Department</th>
						<th width="90">Section</th>
						<th width="90">Sewing Floor</th>
						<th width="90">Sewing Line</th>
                        <th width="100">Store</th>
                        <th class="must_entry_caption" width="160">Date</th>
                        <th width="100">Req/Issue No</th>
                        <th id="search_by_td_up" width="100">Please Enter Req no</th>
                        <th width="150" align="center"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('departmentwiseissue_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?
                                    echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/department_wise_issue_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/department_wise_issue_report_controller', this.value, 'load_drop_down_department', 'department_td' );load_drop_down( 'requires/department_wise_issue_report_controller', this.value , 'load_drop_down_store', 'store_td' );" );
                                ?>
                            </td>
                            <td id="location_td">
                           <?
                             echo create_drop_down( "cbo_location", 120, $blank_array,"", 1, "-- Select --", "", "" );
                           ?>
                            </td>
                           <td>
							<?php
							//create_drop_down($field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name, $additionalClass, $additionalAttributes)
							//8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,68,67
                            echo create_drop_down( "cbo_item_category_id", 180, $general_item_category,"", 0, "", 0, "", 0,"","","","4");
                            ?>
                            <input type="hidden" name="txt_product_id" id="txt_product_id" style="width:90px;"/>
                          </td>

                            <td>
                            	<input style="width:90px;" name="txt_item_group" id="txt_item_group" class="text_boxes" onDblClick="openmypage_item_group()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_item_group_id" id="txt_item_group_id" style="width:90px;"/>
                            </td>
                            <td>
                            	<input style="width:90px;" name="txt_item_acc" id="txt_item_acc" class="text_boxes" onDblClick="openmypage_item_account()" placeholder="Browse" readonly />
                                <input type="hidden" name="txt_item_account_id" id="txt_item_account_id" style="width:90px;"/>
                            </td>
                           <td id="department_td"><?
                                        echo create_drop_down( "cbo_department", 120, $blank_array,"", 1, "-- Select --", "", "" );
                                        ?></td>
							<td id="section_td"><?
                                        echo create_drop_down( "cbo_section", 90, $blank_array,"", 1, "-- Select --", "", "" );
                            		?>
							</td>
							<td id="sewing_td" >
								<?php echo create_drop_down( "cbo_sewing_floor_name", 90,$blank_array,"", 1, "-- Select Sewing Floor --",'' ); ?>
							</td>
							<td id="line_td" >
								<?php echo create_drop_down( "cbo_sewing_floor_line", 90,$blank_array,"", 1, "-- Select --",'' ); ?>
							</td>
                          <td width="100" id="store_td">
                                <?
                                    echo create_drop_down( "cbo_store_name", 100, $blank_array,"", 1, "-- Select --", "", "");
                                ?>
                           </td>
                            <td>
                                <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px;"/>
                                To
                                <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px;"/>
                            </td>

							<td align="center">
	                    	<?
	                       		$search_by_arr=array(1=>"Req No",2=>"Issue No");
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "", 1,"changeTitle(this.value)",0 );
							?>
		                    </td>
		                    <td align="center">
		                    	<input type="text" style="width:100px" class="text_boxes"  name="txt_reference_id" id="txt_reference_id" />
		                    </td>

                            <td align="center">
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(3)" style="width:60px" class="formbutton" />
                                <input type="button" name="search" id="search" value="Summary" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="13" align="center"><? echo load_month_buttons(1);  ?></td>
                            <td align="center">
                                <input type="button" name="search" id="search" value="Summary 2" onClick="generate_report(4)" style="width:80px" class="formbutton" />
								<input type="button" name="search" id="search" value="Show 2" onClick="generate_report(5)" style="width:60px" class="formbutton" />
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
            </div>
            <br />
                <div id="report_container" align="center" style="padding-bottom: 10px;"></div>
                <div id="report_container2"></div>
        </form>
    </div>
</body>
<script>
	set_multiselect('cbo_item_category_id','0','0','0','0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
