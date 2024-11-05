<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Daily Line wise Production Report

Functionality	:
JS Functions	:
Created by		:	Ashraful
Creation date 	: 	05-04-2018
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Daily Line wise Production Report","../../", 1, 1, $unicode,1);
?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report()
	{
		if( form_validation('cbo_company_id*txt_date','Working Company*Production Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();

		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_floor_id*txt_search_val*cbo_search_type*cbo_buyer_id*cbo_line*hidden_line_id*txt_date*txt_ir_no',"../../")+'&report_title='+report_title;

		//alert(data); return;

		freeze_window(3);
		http.open("POST","requires/daily_line_wise_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4)
			{
				//alert (http.responseText);
				var reponse=trim(http.responseText).split("####");
				$("#report_container2").html(reponse[0]);

				release_freezing();
				//document.getElementById('factory_efficiency').innerHTML=document.getElementById('total_factory_effi').innerHTML;
				//document.getElementById('factory_parfomance').innerHTML=document.getElementById('total_factory_per').innerHTML;
				//alert(reponse[1]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

				show_msg('3');
				release_freezing();
			}
		}
	}
	function type_wise_fnc(data)
	{
		if(data==1)
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("Job No");
			$("#txt_search_val").attr("placeholder","          Job No");

		}
		else if(data==2)
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("Style");
			$("#txt_search_val").attr("placeholder","            Style");

		}
		else if(data==3)
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("PO");
			$("#txt_search_val").attr("placeholder","               PO");
		}
		else
		{
			$("#txt_search_val").val('');
			$("#type_wise_name").text("");
			$("#txt_search_val").attr("placeholder","");
		}
	}

	function openmypage_line() // For Line number
	{
		if( form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		var company = $("#cbo_company_id").val();
		var location=$("#cbo_location_id").val();
		var floor_id=$("#cbo_floor_id").val();
		var txt_date=$("#txt_date").val();

		var page_link='requires/daily_line_wise_production_report_controller.php?action=line_search_popup&company='+company+'&location='+location+'&floor_id='+floor_id+'&txt_date='+txt_date;

		var title="Search line Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=260px,height=360px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;

			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#cbo_line").val(prodDescription);
			$("#hidden_line_id").val(prodID);
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="400px";
	}

</script>

</head>
<body onLoad="set_hotkey();">

<form id="DailyLineWiseProductionReport_1">
    <div style="width:100%;" align="center">

        <? echo load_freeze_divs ("../../",'');  ?>

         <h3 style="width:1174px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel" >
         <fieldset style="width:1100px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="160" class="must_entry_caption">Working Company</th>
                    <th width="120" class="">Buyer</th>
                    <th class="must_entry_caption">Production Date</th>
                    <th width="150">Location</th>
                    <th width="120">Floor</th>
                    <th width="100">Search Type</th>
                    <th width="100" id="type_wise_name"></th>
                    <th width="120">Line No</th>
                    <th width="120">IR/IB</th>
                    <th width="180"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('DailyLineWiseProductionReport_1','report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                       <td>
							<?
                                echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/daily_line_wise_production_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>
                        </td>
                        <td>
							<?
                                echo create_drop_down( "cbo_buyer_id", 150, "SELECT id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 group by id,buyer_name order by buyer_name ","id,buyer_name", 1, "-- Select Buyer --", 0, "" );
                            ?>
                        </td>

                         <td>
                            <input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:70px;" readonly/>
                        </td>
                        <td id="location_td">
                            <?
                                echo create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-- Select Location --", "", "" );
                            ?>
                        </td>

                        <td id="floor_td">
                            <?
                                echo create_drop_down( "cbo_floor_id", 120, $blank_array,"", 1, "-- Select Floor --", "", "" );
                            ?>
                        </td>
                        <td>
                          <?
                        	  $type_array=array(0=>"Select",1=>"Job",2=>"Style",3=>"Po");
                              echo create_drop_down( "cbo_search_type", 100, $type_array,"", 1, "-- Select Type --", "", "type_wise_fnc(this.value);" );
                            ?>

                        </td>
                        <td>
                         <input type="text" id="txt_search_val"  name="txt_search_val"  style="width:100px" class="text_boxes"  placeholder=""  />

                        </td>

                        <td id="line_td">
                               <input type="text" id="cbo_line"  name="cbo_line"  style="width:120px" class="text_boxes" onDblClick="openmypage_line()" placeholder="Browse"  readonly/>
                               <input type="hidden" id="hidden_line_id" name="hidden_line_id" />
                        </td>
						<td>
							<input type="text" name="txt_ir_no" id="txt_ir_no" class="text_boxes" style="width:100px" />
						</td>

                        <td>
                            <input type="button" name="search2" id="search2" value="Show" onClick="generate_report()" style="width:60px" class="formbutton" />
                        </td>

                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>

    <div id="report_container" align="center"></div>

    <div id="report_container2" align="left">
    	<div style="float:left; " id="report_container3"></div>
    </div>
 </form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
