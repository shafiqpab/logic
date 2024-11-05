<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Report Will Create Knit Garments FSO Report.
Functionality	:
JS Functions	:
Created by		:	Md. Abu Sayed
Creation date 	: 	13-12-2022
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


$fabric_booking_type = array( 1 => "Main Fabric Booking",2=>'Partial Fabric Booking', 3 => "Short Fabric Booking", 4 => "Sample Fabric Booking - With Order", 5 => 'Sample Fabric Booking - Without Order');

echo load_html_head_contents("FSO Report", "../../", 1, 1,$unicode,1,1);
?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	function fn_report_generated(type)
	{
		//console.log(type);

		if( form_validation('cbo_company_id','Company Name')==false )
		{
			release_freezing();
			return;
		}

		var fso_no = $("#txt_fso_no").val();
		var fab_booking_type = $("#cbo_fab_booking_type").val();
		var search_type = $("#search_type").val();
		if (fab_booking_type != "")
		{
			if(search_type !=2)
			{
				alert('Please select Fabric Booking Date....');return;
			}
		}

		if (fso_no =="")
		{
			if( form_validation('txt_date_from*txt_date_to','To Date*From Date')==false )
			{
				release_freezing();
				return;
			}
		}

		//console.log("here");
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_fab_booking_type*search_type*txt_date_from*txt_date_to*txt_fso_no_id*txt_fso_no',"../../")+'&report_title='+report_title+'&type='+type;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/fso_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;

	}
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$("#table_body tr:first").hide();
		$("#table_body_non_ord tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflow="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";

		$("#table_body tr:first").show();
		$("#table_body_non_ord tr:first").show();
	}

	function openmypage_fso()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var title = 'Booking Selection Form';
			var page_link = 'requires/fso_report_controller.php?cbo_company_id='+cbo_company_id+'&action=fso_popup';
			var popup_width="1070px";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width='+popup_width+',height=400px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("hidden_fso_id").value;	 //Knit Id for Kintting Plan
				var theename=this.contentDoc.getElementById("hidden_fso_no").value; //all data for Kintting Plan
				if(theemail!="")
				{
					freeze_window(5);

					$('#txt_fso_no').val(theename);
					$('#txt_fso_no_id').val(theemail);
					release_freezing();
				}
			}
		}
	}

	function fnRemoveHidden(str){
		document.getElementById(str).value='';
	}

    function search_populate(str)
    {
        if (str == 1)
        {
            document.getElementById('based_on_th_up').innerHTML = "FSO Date";
        }
        else if (str == 2)
        {
            document.getElementById('based_on_th_up').innerHTML = "F. Booking Date";
        }

    }

</script>

</head>
<body onLoad="set_hotkey();">
<form id="bookingreport_1">
<div style="width:100%; margin:1px auto;" align="center">
    <? echo load_freeze_divs ("../../",$permission);  ?>
    <h3 style="width: 815px;margin-left: 115px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:710px" >
            <fieldset style="width:710px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th width="150" class="must_entry_caption">Company Name</th>
                    <th width="100">FSO No</th>
                    <th width="120">Booking Type</th>
                    <th width="150">Based On Date</th>
                    <th width="200" id="based_on_th_up" colspan="2">FSO Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" onClick="reset_form('bookingreport_1','report_container*report_container2','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" ); ?></td>

						<td><input type="text" name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:90px" placeholder="Browse/Write" onChange="fnRemoveHidden('txt_fso_no_id');" onDblClick="openmypage_fso();" > <input type="hidden" name="txt_fso_no_id" id="txt_fso_no_id" value=""></td>

                        <td><? echo create_drop_down( "cbo_fab_booking_type", 120, $fabric_booking_type, "--Select Type--", $selected, ""); ?></td>
                        <td >
                            <?
                               $search_type_arr = array(1 => "FSO Date", 2 => "Fabric Booking Date");
                               $fnc_name = "search_populate(this.value)";
                               echo create_drop_down("search_type", 150, $search_type_arr, "", 0, "-Select-", 0, $fnc_name, 0, "", "", "", "", "");
                            ?>
                        </td>

                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date"/><input type="hidden" name="cbo_approval_status" id="cbo_approval_status" value="0"></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px"  placeholder="To Date" /></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(0);" /></td>
                    </tr>
					<tr>
                        <td align="center" colspan="8"><?=load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table>
        	</fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </div>
</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	set_multiselect('cbo_fab_booking_type','0','0','','0');
</script>
</html>