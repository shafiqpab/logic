<?
/*-------------------------------------------- Comments
Purpose			: 	This Report is about Yarn top price list

Functionality	:
JS Functions	:
Created by		:	Safa
Creation date 	: 	21-06-2023
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
echo load_html_head_contents("Yarn Top Price List","../../", 1, 1, $unicode,1,1);

?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

function generate_report(type)
{

	if( form_validation('cbo_company_id','Comapny Name')==false)
	{
		return;
	}

	
	var cbo_company 	= $("#cbo_company_id").val();
	var cbo_count 	= $("#cbo_count_name").val();
	//var txt_count 	= $("#txt_yarn_count").val();
	//var txt_count_id 	= $("#txt_yarn_count_id").val();

	var txt_composition = $("#txt_composition").val();
	var txt_composition_id = $("#txt_composition_id").val();

	var yarn_type = $("#txt_yarn_type").val();
	var yarn_type_id = $("#txt_yarn_type_id").val();

	//var cbo_price 	= $("#cbo_price").val();

	var from_date 	= $("#txt_date_from").val();
	var to_date 	= $("#txt_date_to").val();



	var txt_composition1 = document.getElementById('txt_composition').value;
	var cbo_count_name1 = document.getElementById('cbo_count_name').value;
	var txt_yarn_type1 = document.getElementById('txt_yarn_type').value;
	if(from_date == ""){

	if (txt_composition1 <=0 && cbo_count_name1 <=0 && txt_yarn_type1 <=0) {
		alert('Please, Give Count/composiotion/Yarn Type or PI Date Range.');
		return;
	}

	}
	var dataString = "&cbo_company="+cbo_company+"&cbo_count="+cbo_count+"&txt_composition="+txt_composition+"&txt_composition_id="+txt_composition_id+"&yarn_type="+yarn_type+"&yarn_type_id="+yarn_type_id+"&from_date="+from_date+"&to_date="+to_date;
 	var data="action=generate_report"+dataString;
	freeze_window(3);
	http.open("POST","requires/yarn_top_price_list_report_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;
}

function generate_report_reponse()
{
	if(http.readyState == 4)
	{
 		var reponse=trim(http.responseText).split("****");
		$("#report_container2").html(reponse[0]);
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		setFilterGrid('table_body',-1);
		show_msg('3');
		release_freezing();
	}
}


function new_window()
	{
		document.getElementById('scroll_body').style.overflow='auto';
		document.getElementById('scroll_body').style.maxHeight='none';
		$('#table_body tbody').find('tr:first').hide();
		var w = window.open('Surprise', '#');
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$('#table_body tbody').find('tr:first').show();
		document.getElementById('scroll_body').style.overflowY='scroll';
		document.getElementById('scroll_body').style.maxHeight='300px';
	}


function openmypage(prod_id,action)
{
	var companyID = $("#cbo_company_name").val();
	var popup_width='1200px';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_top_price_list_report_controller.php?companyID='+companyID+'&prod_id='+prod_id+'&action='+action, 'Yarn top price list report', 'width='+popup_width+', height=450px,center=1,resize=0,scrolling=0','../../');
}



function openmypage_composition()
{
	var pre_composition_id = $("#txt_composition_id").val();
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_top_price_list_report_controller.php?action=composition_popup&pre_composition_id='+pre_composition_id, 'Composition Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var composition_des=this.contentDoc.getElementById("hidden_composition").value; //Access form field with id="emailfield"
		var composition_id=this.contentDoc.getElementById("hidden_composition_id").value;
		$("#txt_composition").val(composition_des);
		$("#txt_composition_id").val(composition_id);

	}
}



function openmypage_yarn_type()
{
	var companyID = $("#cbo_company_name").val();

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_top_price_list_report_controller.php?action=yarn_type_popup&companyID='+companyID, 'Yarn Type Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var yarn_type_des=this.contentDoc.getElementById("hidden_yarn_type").value; //Access form field with id="emailfield"
		var yarn_type_id=this.contentDoc.getElementById("hidden_yarn_type_id").value;
		$("#txt_yarn_type").val(yarn_type_des);
		$("#txt_yarn_type_id").val(yarn_type_id);

	}
}

function openmypage_yarn_count()
{
	var companyID = $("#cbo_company_name").val();

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/yarn_top_price_list_report_controller.php?action=yarn_count_popup&companyID='+companyID, 'Yarn Count Details', 'width=410px,height=420px,center=1,resize=0,scrolling=0','../../');

	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var yarn_count_des=this.contentDoc.getElementById("hidden_yarn_count").value; //Access form field with id="emailfield"
		var yarn_count_id=this.contentDoc.getElementById("hidden_yarn_count_id").value;
		$("#txt_yarn_count").val(yarn_count_des);
		$("#txt_yarn_count_id").val(yarn_count_id);

	}
}


function generate_report2(company_id, mst_id) {
    //alert(mst_id);
		var page = 'Yarn Top Price List';
		var report_title ='Yarn Purchase Order [Sweater]';
        var type = 1;
        var ref_cls = 0;
    
        var path = '../';
		print_report(company_id + '*' + mst_id + '*' + report_title+ '*' + type + '*' + ref_cls+ '*' + path, "print_to_html_report", "requires/yarn_top_price_list_report_controller");

		//window.open("requires/yarn_top_price_list_report_controller.php?data=" + company_id +'*'+$('#update_id').val()+'*'+report_title+'*'+type+'*'+$('#ref_closed_sts').val()+'&action='+"print_to_html_report", true );
    }


</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="stock_ledger_1" id="stock_ledger_1" autocomplete="off" >
    <div style="width:100%;" align="center">
        <h3 style="width:850px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div style="width:100%;" id="content_search_panel">
            <fieldset style="width:850px;">
                <table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
							<th class="must_entry_caption">Company</th>
                            <th>Count</th>
                            <th>Composition</th>
                            <th>Yarn Type</th>
                            <th class="must_entry_caption" colspan="2">PI Date Range</th>
                            <th colspan="2"><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" /></th>
                        </tr>
                    </thead>
                    <tr>
						<td align="center">
							<? 
								echo create_drop_down( "cbo_company_id", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and comp.core_business in(1,3) order by company_name","id,company_name", 1, "--Select Company--", $selected, "" );
                            ?>       
						</td>

                        <td align="center">
							<? 
								$count_query = "SELECT ID,YARN_COUNT FROM LIB_YARN_COUNT WHERE IS_DELETED=0 AND STATUS_ACTIVE=1 ORDER BY YARN_COUNT";
                                echo create_drop_down( "cbo_count_name", 120, $count_query,"id,yarn_count", 1, "-- Select One --", $selected, "",0,"" );
                            ?>

							<!-- <input type="text" id="txt_yarn_count" name="txt_yarn_count" class="text_boxes" style="width:120px" value="" onDblClick="openmypage_yarn_count();" placeholder="Browse" readonly /> -->

							<input type="hidden" id="txt_yarn_count_id" name="txt_yarn_count_id" class="text_boxes" style="width:120px" value=""  />
                          
                        </td>
                        <td align="center">
                            <input type="text" id="txt_composition" name="txt_composition" class="text_boxes" style="width:120px" value="" onDblClick="openmypage_composition();" placeholder="Browse" readonly />

                            <input type="hidden" id="txt_composition_id" name="txt_composition_id" class="text_boxes" style="width:120px" value=""  />
                        </td>

                        <td align="center">
							<input type="text" id="txt_yarn_type" name="txt_yarn_type" class="text_boxes" style="width:120px" value="" onDblClick="openmypage_yarn_type();" placeholder="Browse" readonly />

							<input type="hidden" id="txt_yarn_type_id" name="txt_yarn_type_id" class="text_boxes" style="width:120px" value=""  />
                        </td>

                        <!-- <td>
							<?
                            // $price_arr = array(0 => "Top 1", 1 => "Top 2", 2 => "Top 3", 3 => "Top 4", 4 => "Top 5", 5 => "Top 6", 6 => "Top 7", 7 => "Top 8", 8 => "Top 9", 9 => "Top 10");
                            // echo create_drop_down("cbo_price", 120, $price_arr, "", 0, "", 0, "", "");
                            ?>
                        </td>
						 -->
                        <td  align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:60px">TO
                    	    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td>
                         <td>

                        <td align="center">
                    <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:60px" class="formbutton" />
                    </td>
                       
                    </tr>
                    <tr>
                        <td colspan="7" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                        </td>
                    </tr>
                </table>
            </fieldset>
		</div>
    </div>
    <br />

    <!-- Result Contain Start-->

        <div id="report_container" align="center"></div>
        <div id="report_container2" style="margin-left:5px"></div>

    <!-- Result Contain END-->


    </form>
</div>
</body>
<script>
	//set_multiselect('cbo_yarn_count*cbo_supplier*cbo_yarn_type','0*0*0','0*0*0','','0*0*0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>set_multiselect('cbo_company_id','0','0','','0',"load_drop_down( 'requires/sc_wise_order_summary_controller',$('#cbo_company_id').val(), 'load_drop_down_buyer', 'buyer_td' )");</script>
<script>
	$("#cbo_value_with").val(1);
</script>
</html>
