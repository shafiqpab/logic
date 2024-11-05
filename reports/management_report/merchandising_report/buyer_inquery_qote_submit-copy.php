<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Buyer Inquery VS Quatation Report

Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	01/09/2014
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
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Info","../../../", 1, 1, $unicode);
?>

<script>
	var permission='<? echo $permission; ?>';

	function fn_report_generated(report_type)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		else
		{
			$("#report_type_id").val(report_type);
			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_season_id*cbo_search_by*txt_inqu_no*txt_department*txt_date_from*txt_date_to*txt_inq_no*txt_style_ref*price_stage',"../../../")+'&report_type='+report_type;
			freeze_window(3);
			http.open("POST","requires/buyer_inquery_qote_submit_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			//alert(http.responseText);
			var reponse=trim(http.responseText).split("****");
			$('#report_container2').html("");
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid('tbl_ship_pending',-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		var report_type= $('#report_type_id').val();
		if(report_type !=4 && report_type!=5)
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#tbl_ship_pending tbody').find('tr:first').hide();
		}
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><style>.verticalText2{writing-mode: tb-rl; filter: flipv fliph; -webkit-transform: rotate(270deg);-moz-transform: rotate(270deg);-o-transform: rotate(270deg);-ms-transform: rotate(270deg);transform: rotate(270deg);width: 1em;line-height: 1em;};@media print {thead {display: table-header-group;}}</style></head><body>'+document.getElementById('report_container2').innerHTML+'</body></html>');
		d.close();
		if(report_type !=4 && report_type!=5)
		{
			$('#tbl_ship_pending tbody').find('tr:first').show();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="200px";
		}
	}


	
	function openmypage_image(page_link,title)
	{

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{

		}

		/*var page_link='requires/buyer_inquery_qote_submit_controller.php?action='+action+'&sys_id='+sys_id+'&type='+type
		//var title="Image View";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=300px,center=1,resize=0,scrolling=0','../../')*/
	}
	function openmypage_file(page_link,title)
	{
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=280px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{

		}
	}
	function print_report_button_setting(report_ids)
{
	var report_id=report_ids.split(",");
	$("#report1").hide();
	$("#report2").hide();
	$("#report3").hide();
	$("#report4").hide();
	for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==266)
			{
				$("#report1").show();
			}
			if(report_id[k]==256)
			{
				$("#report2").show();
			}
			if(report_id[k]==267)
			{
				$("#report3").show();
			}
			if(report_id[k]==264)
			{
				$("#report4").show();
			}
		}
}
</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
    <h3 align="left" id="accordion_h1" style="width:1190px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel">
            <form id="frm_inquery_vs_quatation" name="frm_inquery_vs_quatation" action="" autocomplete="off" method="post">
                <fieldset style="width:1190px;">
                    <table width="1190" cellpadding="0" cellspacing="2" border="1" rules="all" class="rpt_table" >
                        <thead>
                            <th width="100" class="must_entry_caption">Company Name</th>
                            <th width="100">Buyer Name</th>
                            <th width="100">Inq.Id</th>
                            <th width="70">Buyer Inqu. No</th>
                            <th width="70">Prod. Department</th>
                            <th width="70">Style Name</th>
                            <th width="60">Season</th>
                            <th width="90">Price Stage</th>
                            <th width="90">Search By</th>
                            <th width="170" colspan="2">Date Range</th>
                            <th ><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('frm_inquery_vs_quatation','report_container*report_container2','','','')" /></th>
                        </thead>
                        <tr class="general">
                            <td><? echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/buyer_inquery_qote_submit_controller', this.value, 'load_drop_down_buyer', 'buyer_td'); get_php_form_data( this.value, 'get_company_config', 'requires/buyer_inquery_qote_submit_controller');" ); ?></td>
                            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 100, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" ); ?></td>
                            <td><input type="text" id="txt_inq_no" name="txt_inq_no" class="text_boxes" style="width:70px;" ></td>
                            <td><input type="text" id="txt_inqu_no" name="txt_inqu_no" class="text_boxes" style="width:70px;" ></td>
                            <td><input type="text" id="txt_department" name="txt_department" class="text_boxes" style="width:70px;"></td>
                            <td><input type="text" id="txt_style_ref" name="txt_style_ref" class="text_boxes" style="width:70px;"></td>
                            <td id="season_td"><? echo create_drop_down( "cbo_season_id", 60, $blank_array,'', 1, "-Season-",$selected, "" ); ?></td>
                           
                            <td ><? echo create_drop_down( "price_stage", 90,  $inquery_stage_arr,'', 1, "-Price Stage-",$selected, "" ); ?></td>
                            <td>
                            <?
                             $search_by_arr=array(1=>"Inquiry Receive Date",2=>"Shipment Date");
                                echo create_drop_down( "cbo_search_by", 90, $search_by_arr, 1, "-- Select  --", $selected, "" );
                            ?>
                            </td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" ></td>
                        	<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date" ></td>
                            <td>
                                <input type="button" name="show" id="report1" onClick="fn_report_generated(1);" class="formbutton" style="width:60px; display: none;" value="Report1" />
                                <input type="button" name="show" id="report2" onClick="fn_report_generated(2);" class="formbutton" style="width:60px; display: none;" value="Report2" />
                               
                                
                            </td>
                        </tr>
                         <tr>
		                    <td colspan="10" align="center">
								<? echo load_month_buttons(1); ?>

		                    </td>
                            <td>
                            	 <input type="button" name="show" id="report3" onClick="fn_report_generated(3);" class="formbutton" style="width:60px; display: none;" value="Report3" />
                                <input type="button" name="show" id="report4" onClick="fn_report_generated(4);" class="formbutton" style="width:60px; display: none;" value="Report4" />
                                 
                                <input type="hidden" id="report_type_id" value="">
                           </td>
                           <td>
                           		<input type="button" name="show" id="report5" onClick="fn_report_generated(5);" class="formbutton" style="width:60px;" value="Report5" />
                           </td>
		                </tr>
                    </table>
                </fieldset>
            </form>
        </div>
       <div id="report_container" align="center"></div>
       <div id="report_container2"></div>
 </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>